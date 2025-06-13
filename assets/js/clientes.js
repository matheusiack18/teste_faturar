$(document).ready(function () {
  loadClientes();

  $("#clienteForm").on("submit", function (e) {
    e.preventDefault();

    $(".form-control").removeClass("is-invalid");
    $("#modalAlerts").empty();

    const formData = new FormData(this);

    const submitBtn = $("#submitBtn");
    const originalText = submitBtn.html();
    submitBtn
      .html(
        '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Processando...'
      )
      .prop("disabled", true);

    $.ajax({
      url: "../controllers/ClienteController.php",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        try {
          const result = JSON.parse(response);
          if (result.success) {
            $("#clienteModal").modal("hide");

            showPageAlert("success", result.message);

            loadClientes();
          } else {
            showModalAlert("danger", result.message);
            highlightErrorField(result.message);
          }
        } catch (e) {
          showModalAlert("danger", "Erro ao processar resposta do servidor");
        }
      },
      error: function (xhr, status, error) {
        showModalAlert(
          "danger",
          "Erro na comunicação com o servidor: " + error
        );
      },
      complete: function () {
        submitBtn.html(originalText).prop("disabled", false);
      },
    });
  });

  $("#nome").on("input", function (e) {
    let value = e.target.value;

    value = value.replace(/[^a-zA-ZÀ-ÿ\s]/g, "");

    value = value.replace(/\s+/g, " ");

    e.target.value = value;
  });

  $("#cpf_cnpj").on("input", function (e) {
    let value = e.target.value.replace(/\D/g, "");

    if (value.length > 14) {
      value = value.substring(0, 14);
    }

    if (value.length <= 11) {
      value = value.replace(/(\d{3})(\d)/, "$1.$2");
      value = value.replace(/(\d{3})(\d)/, "$1.$2");
      value = value.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
    } else {
      value = value.replace(/(\d{2})(\d)/, "$1.$2");
      value = value.replace(/(\d{3})(\d)/, "$1.$2");
      value = value.replace(/(\d{3})(\d)/, "$1/$2");
      value = value.replace(/(\d{4})(\d{1,2})$/, "$1-$2");
    }

    e.target.value = value;

    if (value.length > 0) {
      const numbersOnly = value.replace(/\D/g, "");
      if (numbersOnly.length === 11 || numbersOnly.length === 14) {
        $(this).removeClass("is-invalid").addClass("is-valid");
      } else {
        $(this).removeClass("is-valid").addClass("is-invalid");
      }
    }
  });

  $("#telefone").on("input", function (e) {
    let value = e.target.value.replace(/\D/g, "");

    if (value.length > 11) {
      value = value.substring(0, 11);
    }

    if (value.length <= 10) {
      value = value.replace(/(\d{2})(\d)/, "($1) $2");
      value = value.replace(/(\d{4})(\d)/, "$1-$2");
    } else {
      value = value.replace(/(\d{2})(\d)/, "($1) $2");
      value = value.replace(/(\d{5})(\d)/, "$1-$2");
    }

    e.target.value = value;

    if (value.length > 0) {
      const numbersOnly = value.replace(/\D/g, "");
      if (numbersOnly.length >= 10 && numbersOnly.length <= 11) {
        const ddd = numbersOnly.substring(0, 2);
        if (parseInt(ddd) >= 11 && parseInt(ddd) <= 99) {
          $(this).removeClass("is-invalid").addClass("is-valid");
        } else {
          $(this).removeClass("is-valid").addClass("is-invalid");
        }
      } else {
        $(this).removeClass("is-valid").addClass("is-invalid");
      }
    }
  });

  $("#email").on("input", function (e) {
    const email = e.target.value;
    if (email.length > 0) {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (emailRegex.test(email)) {
        $(this).removeClass("is-invalid").addClass("is-valid");
      } else {
        $(this).removeClass("is-valid").addClass("is-invalid");
      }
    }
  });
});

function loadClientes() {
  $("#loading").show();

  $.ajax({
    url: "../controllers/ClienteController.php",
    type: "GET",
    data: { action: "list" },
    success: function (response) {
      try {
        const result = JSON.parse(response);
        if (result.success) {
          renderClientesTable(result.data);
        } else {
          console.error("Erro ao carregar clientes:", result.message);
        }
      } catch (e) {
        console.error("Erro ao processar resposta:", e);
      }
      $("#loading").hide();
    },
    error: function (xhr, status, error) {
      console.error("Erro na comunicação:", error);
      $("#loading").hide();
    },
  });
}

function renderClientesTable(clientes) {
  const tbody = $("#clientesTableBody");
  tbody.empty();

  if (clientes.length === 0) {
    tbody.append(
      '<tr><td colspan="6" class="text-center py-4"><i class="bi bi-inbox me-2"></i>Nenhum cliente encontrado</td></tr>'
    );
    return;
  }

  clientes.forEach(function (cliente) {
    const row = `
            <tr>
                <td class="text-center">${cliente.id}</td>
                <td><strong>${cliente.nome}</strong></td>
                <td>${cliente.cpf_cnpj}</td>
                <td>${cliente.email}</td>
                <td>${cliente.telefone}</td>
                <td class="text-center">
                    <button onclick="openEditModal(${cliente.id})" class="btn btn-sm btn-outline-primary me-1" title="Editar Cliente">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <a href="excluir.php?id=${cliente.id}" class="btn btn-sm btn-outline-danger" title="Excluir Cliente">
                        <i class="bi bi-trash"></i>
                    </a>
                </td>
            </tr>
        `;
    tbody.append(row);
  });
}

function openCreateModal() {
  resetForm();
  $("#clienteModalLabel").html(
    '<i class="bi bi-person-plus me-2"></i>Novo Cliente'
  );
  $("#submitBtn").html('<i class="bi bi-check-lg me-1"></i>Salvar Cliente');
  $("#formAction").val("create");
  $("#clienteId").val("");
}

function openEditModal(clienteId) {
  resetForm();
  $("#clienteModalLabel").html(
    '<i class="bi bi-pencil-square me-2"></i>Editar Cliente'
  );
  $("#submitBtn").html('<i class="bi bi-check-lg me-1"></i>Atualizar Cliente');
  $("#formAction").val("update");
  $("#clienteId").val(clienteId);

  showModalAlert(
    "info",
    '<i class="bi bi-hourglass-split me-2"></i>Carregando dados do cliente...'
  );

  $.ajax({
    url: "../controllers/ClienteController.php",
    type: "GET",
    data: {
      action: "get",
      id: clienteId,
    },
    success: function (response) {
      try {
        const result = JSON.parse(response);
        if (result.success) {
          const cliente = result.data;
          $("#nome").val(cliente.nome);
          $("#cpf_cnpj").val(cliente.cpf_cnpj);
          $("#email").val(cliente.email);
          $("#telefone").val(cliente.telefone);

          $("#modalAlerts").empty();

          $("#clienteModal").modal("show");
        } else {
          showModalAlert("danger", "Erro ao carregar dados: " + result.message);
        }
      } catch (e) {
        showModalAlert("danger", "Erro ao processar dados do cliente");
      }
    },
    error: function (xhr, status, error) {
      showModalAlert("danger", "Erro na comunicação com o servidor: " + error);
    },
  });
}

function resetForm() {
  $("#clienteForm")[0].reset();
  $(".form-control").removeClass("is-invalid");
  $("#modalAlerts").empty();
}

function showModalAlert(type, message) {
  const iconMap = {
    success: "bi-check-circle",
    danger: "bi-exclamation-circle",
    warning: "bi-exclamation-triangle",
    info: "bi-info-circle",
  };

  const icon = iconMap[type] || "bi-info-circle";

  const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            <i class="bi ${icon} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;

  $("#modalAlerts").html(alertHtml);
}

function showPageAlert(type, message) {
  const iconMap = {
    success: "bi-check-circle",
    danger: "bi-exclamation-circle",
    warning: "bi-exclamation-triangle",
    info: "bi-info-circle",
  };

  const icon = iconMap[type] || "bi-info-circle";

  const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show m-3" role="alert">
            <i class="bi ${icon} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;

  $(".card-body .alert").remove();

  $(".card-body").prepend(alertHtml);

  setTimeout(function () {
    $(".alert").fadeOut();
  }, 5000);
}

function highlightErrorField(errorMessage) {
  if (errorMessage.toLowerCase().includes("nome")) {
    $("#nome").addClass("is-invalid");
  }
  if (
    errorMessage.toLowerCase().includes("cpf") ||
    errorMessage.toLowerCase().includes("cnpj")
  ) {
    $("#cpf_cnpj").addClass("is-invalid");
  }
  if (errorMessage.toLowerCase().includes("mail")) {
    $("#email").addClass("is-invalid");
  }
  if (errorMessage.toLowerCase().includes("telefone")) {
    $("#telefone").addClass("is-invalid");
  }
}
