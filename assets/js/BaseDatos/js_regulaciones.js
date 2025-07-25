
// Product data structure to store information for each product
const productData = {
  calzados: {
    name: "Calzados",
    description: "CALZADO CON SUELA Y PARTE SUPERIOR DE CAUCHO O PLÁSTICO",
    antidumping: { completed: 85, hasData: true },
    permiso: { completed: 60, hasData: true },
    etiquetado: { completed: 25, hasData: true },
    documentos: { completed: 0, hasData: false },
  },
  "motos-electricas": {
    name: "Motos Eléctricas",
    description: "MOTOCICLETA ELÉCTRICA CON BATERÍA DE LITIO",
    antidumping: { completed: 0, hasData: false },
    permiso: { completed: 40, hasData: true },
    etiquetado: { completed: 0, hasData: false },
    documentos: { completed: 75, hasData: true },
  },
  textiles: {
    name: "Textiles",
    description: "PRODUCTOS TEXTILES DE ALGODÓN Y FIBRAS SINTÉTICAS",
    antidumping: { completed: 90, hasData: true },
    permiso: { completed: 0, hasData: false },
    etiquetado: { completed: 80, hasData: true },
    documentos: { completed: 30, hasData: true },
  },
  electronicos: {
    name: "Electrónicos",
    description: "DISPOSITIVOS ELECTRÓNICOS Y COMPONENTES",
    antidumping: { completed: 70, hasData: true },
    permiso: { completed: 85, hasData: true },
    etiquetado: { completed: 60, hasData: true },
    documentos: { completed: 90, hasData: true },
  },
  juguetes: {
    name: "Juguetes",
    description: "JUGUETES INFANTILES DE PLÁSTICO Y MATERIALES SEGUROS",
    antidumping: { completed: 0, hasData: false },
    permiso: { completed: 20, hasData: true },
    etiquetado: { completed: 95, hasData: true },
    documentos: { completed: 50, hasData: true },
  },
}

// Estado de guardado de cada tab
const tabStates = {
  antidumping: false,
  permiso: false,
  etiquetado: false,
  documentos: false
};
let currentTab = 'antidumping';
let tabDirty = false; // Si hay cambios sin guardar en el tab actual
let tabData = {
  antidumping: {},
  permiso: {},
  etiquetado: {},
  documentos: {}
};
const MAX_IMAGES_ANTIDUMPING = 5;
const MAX_DOCS_PERMISO = 5;
const MAX_IMAGES_ETIQUETADO = 5;
const MAX_DOCS_ESPECIALES = 5;

var antidumpingUploaders = [];
var permisoUploaders = [];
var etiquetadoUploaders = [];
var documentosUploaders = [];
let antidumpingTable, permisoTable, etiquetadoTable, documentosTable;

async function saveRegulaciones(formData) {
  try {
    // Obtener el producto seleccionado para determinar el id_rubro
    const selectedProduct = $("#productSelector").val();
    
    // Mapeo de productos a IDs de rubro (ajusta según tus datos)
    const productToRubroMap = {
      'calzados': 1,
      'motos-electricas': 2, 
      'textiles': 3,
      'electronicos': 4,
      'juguetes': 5
    };
    
    const idRubro = productToRubroMap[selectedProduct] || 1;
    
    // Agregar el id_rubro al FormData
    formData.append('id_rubro', idRubro);
    
    // Agregar información del producto seleccionado
    formData.append('producto', selectedProduct);
    
    // Agregar timestamp de creación
    formData.append('created_at', new Date().toISOString());
    
    const response = await fetch(base_url + 'BaseDatos/RegulacionesController/saveRegulacion', {
      method: 'POST',
      body: formData
    });
    
    const result = await response.json();
    
    if (result.success) {
      showToast("Regulaciones guardadas correctamente", "success");
      
      // Recargar las datatables correspondientes
      if (antidumpingTable) antidumpingTable.ajax.reload();
      if (permisoTable) permisoTable.ajax.reload();
      if (etiquetadoTable) etiquetadoTable.ajax.reload();
      if (documentosTable) documentosTable.ajax.reload();
      
      // Limpiar el estado de los tabs
      Object.keys(tabStates).forEach(tab => {
        tabStates[tab] = false;
        tabData[tab] = {};
      });
      updateStepper();
      
      // Volver a las tablas
      $('#formulario-regulacion').addClass('hidden');
      $('#datatables-container').removeClass('hidden');
      
    } else {
      showToast(result.message || "Error al guardar regulaciones", "error");
    }
  } catch (error) {
    console.error('Error al guardar regulaciones:', error);
    showToast("Error de conexión al guardar regulaciones", "error");
  }
}
function updateStepper() {
  ["antidumping", "permiso", "etiquetado", "documentos"].forEach(tab => {
    const circle = document.querySelector(`.stepper-circle[data-step="${tab}"]`);
    if (tabStates[tab]) {
      circle.classList.remove("bg-gray-300");
      circle.classList.add("bg-green-500");
    } else {
      circle.classList.remove("bg-green-500");
      circle.classList.add("bg-gray-300");
    }
  });
  // Botón global habilitado si AL MENOS UN tab está completo
  const atLeastOneComplete = Object.values(tabStates).some(Boolean);
  document.getElementById("guardar-global-btn").disabled = !atLeastOneComplete;
}

function showToast(msg, type = "info") {
  // Usa la función showNotification si existe, si no, crea un toast simple
  if (typeof showNotification === "function") {
    showNotification(msg, type);
  } else {
    const toast = $(`<div class='fixed top-6 right-6 z-50 px-6 py-3 rounded-lg shadow-lg text-white ${type === "success" ? "bg-green-600" : type === "error" ? "bg-red-600" : "bg-blue-600"}'>${msg}</div>`);
    $("body").append(toast);
    setTimeout(() => toast.fadeOut(400, () => toast.remove()), 2500);
  }
}

function markTabDirty() {
  tabDirty = true;
}
function markTabClean() {
  tabDirty = false;
}

// Detectar cambios en inputs para marcar el tab como "dirty"
$(document).on("input change", ".tab-content.active :input", markTabDirty);

// Guardar por tab
$(document).on("click", ".guardar-tab-btn", function () {
  const tab = $(this).data("tab");
  // Validar que el tab esté completo (puedes personalizar la validación por tab)
  if (!isTabComplete(tab)) {
    showToast("Completa todos los campos obligatorios antes de guardar.", "error");
    return;
  }
  // Guardar los datos del tab (puedes personalizar qué datos guardar)
  tabData[tab] = getTabData(tab);
  tabStates[tab] = true;
  markTabClean();
  updateStepper();
});

// Cambio de tab con alerta si hay cambios sin guardar
$(document).on("click", ".tab-btn", function (e) {
  const nextTab = $(this).data("tab");
  if (nextTab === currentTab) return;
  if (tabDirty) {
    e.preventDefault();
    showConfirmModal(
      "Tienes cambios sin guardar",
      "Si cambias de sección perderás el avance no guardado. ¿Deseas continuar?",
      () => {
        // Sí, cambiar de tab
        switchTab(nextTab);
        markTabClean();
      }
    );
    return;
  }
  switchTab(nextTab);
});

function switchTab(tab) {
  $(".tab-btn").removeClass("active bg-blue-600 text-white").addClass("bg-gray-100 text-gray-700");
  $(`.tab-btn[data-tab='${tab}']`).removeClass("bg-gray-100 text-gray-700").addClass("active bg-blue-600 text-white");
  $(".tab-content").addClass("hidden").removeClass("active");
  $(`#${tab}`).removeClass("hidden").addClass("active");
  currentTab = tab;
}

// Modal de confirmación elegante
function showConfirmModal(title, message, onConfirm) {
  const modal = $(`
    <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-lg p-8 max-w-md w-full relative">
        <h3 class="text-lg font-bold mb-2 text-gray-800">${title}</h3>
        <p class="mb-6 text-gray-600">${message}</p>
        <div class="flex justify-end gap-3">
          <button class="cancel-modal-btn px-4 py-2 rounded bg-gray-200 text-gray-700 font-semibold">Cancelar</button>
          <button class="confirm-modal-btn px-4 py-2 rounded bg-blue-600 text-white font-semibold">Continuar</button>
        </div>
      </div>
    </div>
  `);
  $("body").append(modal);
  modal.find(".cancel-modal-btn").click(() => modal.remove());
  modal.find(".confirm-modal-btn").click(() => { modal.remove(); onConfirm(); });
}

// Validación por tab (personaliza según tus campos obligatorios)
function isTabComplete(tab) {
  // Ejemplo: todos los inputs visibles y requeridos deben tener valor
  let valid = true;
  $(`#${tab} :input[required]:visible`).each(function () {
    if (!$(this).val()) valid = false;
  });
  // Puedes agregar validaciones adicionales por tab aquí
  return valid;
}

// Obtener datos del tab (personaliza según tus campos)
function getTabData(tab) {
  const data = {};
  
  // Obtener el producto seleccionado para determinar el id_rubro
  const selectedProduct = $("#productSelector").val();
  const productToRubroMap = {
    'calzados': 1,
    'motos-electricas': 2, 
    'textiles': 3,
    'electronicos': 4,
    'juguetes': 5
  };
  
  // Agregar id_rubro a todos los tabs
  data.id_rubro = productToRubroMap[selectedProduct] || 1;
  
  // Obtener datos de los campos del formulario
  $(`#${tab} :input, #${tab} textarea, #${tab} select`).each(function () {
    const name = $(this).attr("name");
    if (name) {
      data[name] = $(this).val();
    }
  });

  // Agregar archivos de FileUploader según el tab
  if (tab === 'antidumping' && antidumpingUploaders.length > 0) {
    data.archivos = antidumpingUploaders.map(uploader => uploader.uploader.getFile()).filter(file => file);
  } else if (tab === 'permiso' && permisoUploaders.length > 0) {
    data.archivos = permisoUploaders.map(uploader => uploader.uploader.getFile()).filter(file => file);
  } else if (tab === 'etiquetado' && etiquetadoUploaders.length > 0) {
    data.archivos = etiquetadoUploaders.map(uploader => uploader.uploader.getFile()).filter(file => file);
  } else if (tab === 'documentos' && documentosUploaders.length > 0) {
    data.archivos = documentosUploaders.map(uploader => uploader.uploader.getFile()).filter(file => file);
  }

  return data;
}

function capitalize(str) {
  return str.charAt(0).toUpperCase() + str.slice(1);
}

// Guardar global
$("#guardar-global-btn").click(async function () {
  //si al menos una guardada, guardar global
  if (!Object.values(tabStates).some(Boolean)) {
    showToast("No hay datos para guardar", "error");
    return;
  }

  const formData = new FormData();

  // Agregar datos de cada tab guardado
  Object.entries(tabData).forEach(([tab, data]) => {
    if (tabStates[tab]) { // Solo agregar tabs que estén guardados
      Object.entries(data).forEach(([key, value]) => {
        if (key === 'archivos' && Array.isArray(value)) {
          // Agregar archivos múltiples
          value.forEach((file, index) => {
            if (file) {
              formData.append(`${tab}[archivos][${index}]`, file);
            }
          });
        } else {
          formData.append(`${tab}[${key}]`, value);
        }
      });
    }
  });

  // Agregar información del producto seleccionado
  const selectedProduct = $("#productSelector").val();
  formData.append('producto', selectedProduct);
  
  // Agregar timestamp de creación
  formData.append('created_at', new Date().toISOString());

  await saveRegulaciones(formData);
});

$(document).ready(() => {
  initializeDataTables();

  // Event listeners para datatables
  initializeDataTableEventListeners();

  function initializeDataTables() {
    console.log('initializeDataTables');
    
    // DataTable para Antidumping
    if ($.fn.DataTable.isDataTable('#antidumpingTable')) {
      antidumpingTable.ajax.reload();
    } else {
      antidumpingTable = $('#antidumpingTable').DataTable({
        ajax: {
          url: base_url + 'BaseDatos/RegulacionesController/getAntidumpingData',
          type: 'POST',
          data: function (d) {
            // Aquí puedes agregar filtros adicionales si los necesitas
          }
        },
        order: [[0, 'desc']],
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: false,
        info: true,
        autoWidth: false,
        responsive: false,
        serverSide: false,
        pagingType: "full_numbers",
        oLanguage: {
          sInfo: "Mostrando (_START_ - _END_) total de registros _TOTAL_",
          sLengthMenu: "_MENU_",
          sSearch: "Buscar por: ",
          sSearchPlaceholder: "",
          sZeroRecords: "No se encontraron registros",
          sInfoEmpty: "No hay registros",
          sLoadingRecords: "Cargando...",
          sProcessing: "Procesando...",
          oPaginate: {
            sFirst: "<<",
            sLast: ">>",
            sPrevious: "<",
            sNext: ">",
          },
        }
      });
    }

    // DataTable para Permisos
    if ($.fn.DataTable.isDataTable('#permisoTable')) {
      permisoTable.ajax.reload();
    } else {
      permisoTable = $('#permisoTable').DataTable({
        ajax: {
          url: base_url + 'BaseDatos/RegulacionesController/getPermisoData',
          type: 'POST',
          data: function (d) {
            // Aquí puedes agregar filtros adicionales si los necesitas
          }
        },
        order: [[0, 'desc']],
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: false,
        info: true,
        autoWidth: false,
        responsive: false,
        serverSide: false,
        pagingType: "full_numbers",
        oLanguage: {
          sInfo: "Mostrando (_START_ - _END_) total de registros _TOTAL_",
          sLengthMenu: "_MENU_",
          sSearch: "Buscar por: ",
          sSearchPlaceholder: "",
          sZeroRecords: "No se encontraron registros",
          sInfoEmpty: "No hay registros",
          sLoadingRecords: "Cargando...",
          sProcessing: "Procesando...",
          oPaginate: {
            sFirst: "<<",
            sLast: ">>",
            sPrevious: "<",
            sNext: ">",
          },
        }
      });
    }

    // DataTable para Etiquetado
    if ($.fn.DataTable.isDataTable('#etiquetadoTable')) {
      etiquetadoTable.ajax.reload();
    } else {
      etiquetadoTable = $('#etiquetadoTable').DataTable({
        ajax: {
          url: base_url + 'BaseDatos/RegulacionesController/getEtiquetadoData',
          type: 'POST',
          data: function (d) {
            // Aquí puedes agregar filtros adicionales si los necesitas
          }
        },
        order: [[0, 'desc']],
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: false,
        info: true,
        autoWidth: false,
        responsive: false,
        serverSide: false,
        pagingType: "full_numbers",
        oLanguage: {
          sInfo: "Mostrando (_START_ - _END_) total de registros _TOTAL_",
          sLengthMenu: "_MENU_",
          sSearch: "Buscar por: ",
          sSearchPlaceholder: "",
          sZeroRecords: "No se encontraron registros",
          sInfoEmpty: "No hay registros",
          sLoadingRecords: "Cargando...",
          sProcessing: "Procesando...",
          oPaginate: {
            sFirst: "<<",
            sLast: ">>",
            sPrevious: "<",
            sNext: ">",
          },
        }
      });
    }

    // DataTable para Documentos Especiales
    if ($.fn.DataTable.isDataTable('#documentosTable')) {
      documentosTable.ajax.reload();
    } else {
      documentosTable = $('#documentosTable').DataTable({
        ajax: {
          url: base_url + 'BaseDatos/RegulacionesController/getDocumentosData',
          type: 'POST',
          data: function (d) {
            // Aquí puedes agregar filtros adicionales si los necesitas
          }
        },
        order: [[0, 'desc']],
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: false,
        info: true,
        autoWidth: false,
        responsive: false,
        serverSide: false,
        pagingType: "full_numbers",
        oLanguage: {
          sInfo: "Mostrando (_START_ - _END_) total de registros _TOTAL_",
          sLengthMenu: "_MENU_",
          sSearch: "Buscar por: ",
          sSearchPlaceholder: "",
          sZeroRecords: "No se encontraron registros",
          sInfoEmpty: "No hay registros",
          sLoadingRecords: "Cargando...",
          sProcessing: "Procesando...",
          oPaginate: {
            sFirst: "<<",
            sLast: ">>",
            sPrevious: "<",
            sNext: ">",
          },
        }
      });
    }
  }

  function initializeDataTableEventListeners() {
    // Botón Nueva Regulación
    $('#btn-nueva-regulacion').click(function () {
      $('#datatables-container').addClass('hidden');
      $('#formulario-regulacion').removeClass('hidden');
    });

    // Botón Volver a Tablas
    $('#btn-volver-tablas').click(function () {
      $('#formulario-regulacion').addClass('hidden');
      $('#datatables-container').removeClass('hidden');
    });

    // Tabs de regulaciones
    $('.regulacion-tab-btn').click(function () {
      const tab = $(this).data('tab');

      // Actualizar botones
      $('.regulacion-tab-btn').removeClass('active bg-blue-600 text-white').addClass('bg-gray-100 text-gray-700');
      $(this).removeClass('bg-gray-100 text-gray-700').addClass('active bg-blue-600 text-white');

      // Mostrar contenido correspondiente
      $('.regulacion-content').addClass('hidden');
      $(`#${tab}-table`).removeClass('hidden');
    });

    // Botones de refresh
    $('.btn-refresh-table').click(function () {
      const table = $(this).data('table');
      switch (table) {
        case 'antidumping':
          antidumpingTable.ajax.reload();
          break;
        case 'permiso':
          permisoTable.ajax.reload();
          break;
        case 'etiquetado':
          etiquetadoTable.ajax.reload();
          break;
        case 'documentos':
          documentosTable.ajax.reload();
          break;
      }
    });

    // Botones de editar
    $(document).on('click', '.btn-edit', function () {
      const id = $(this).data('id');
      // Aquí puedes implementar la lógica para editar
      console.log('Editar regulación ID:', id);
    });

    // Botones de eliminar
    $(document).on('click', '.btn-delete', function () {
      const id = $(this).data('id');
      if (confirm('¿Está seguro de que desea eliminar esta regulación?')) {
        // Aquí puedes implementar la lógica para eliminar
        console.log('Eliminar regulación ID:', id);
      }
    });
  }
  // Initialize with default product
  updateProductData("calzados")

  // Product selector change
  $("#productSelector").change(function () {
    const selectedProduct = $(this).val()
    updateProductData(selectedProduct)
    showNotification(`Producto cambiado a: ${productData[selectedProduct].name}`, "info")
  })

  // Tab switching functionality
  $(".tab-btn").click(function () {
    const tabId = $(this).data("tab")

    // Update button states
    $(".tab-btn").removeClass("active bg-blue-600 text-white").addClass("bg-gray-100 text-gray-700")
    $(this).removeClass("bg-gray-100 text-gray-700").addClass("active bg-blue-600 text-white")

    // Show/hide content
    $(".tab-content").addClass("hidden")
    $(`#${tabId}`).removeClass("hidden")
  })

  // Function to update product data and status indicators
  function updateProductData(productKey) {
    const product = productData[productKey]

    // Update product description in forms
    $(".product-description").val(product.description)
    $(".permit-name").attr("placeholder", `Permiso para ${product.name}`)

    // Update status indicators
    updateStatusIndicators(product)
  }

  function updateStatusIndicators(product) {
    const tabs = ["antidumping", "permiso", "etiquetado", "documentos"]

    tabs.forEach((tab) => {
      const indicator = $(`.status-indicator[data-tab="${tab}"]`)
      const data = product[tab]
      const percentage = data.completed
      const hasData = data.hasData

      // Update percentage text
      indicator.find(".text-xs.text-gray-600").text(`${percentage}%`)

      // Update status dot color based on completion
      const statusDot = indicator.find(".w-2.h-2.rounded-full")
      statusDot.removeClass("bg-green-500 bg-yellow-500 bg-red-500 bg-gray-400")

      if (percentage >= 80) {
        statusDot.addClass("bg-green-500")
      } else if (percentage >= 40) {
        statusDot.addClass("bg-yellow-500")
      } else if (percentage > 0) {
        statusDot.addClass("bg-red-500")
      } else {
        statusDot.addClass("bg-gray-400")
      }

      // Add click functionality to status indicators
      indicator.css("cursor", "pointer").click(() => {
        // Switch to the corresponding tab
        $(`.tab-btn[data-tab="${tab}"]`).click()
      })
    })
  }

  // Image upload functionality
  $(".image-upload-container").click(function () {
    if (!$(this).find("img").length) {
      $("#imageUpload").click()
    }
  })

  $("#imageUpload").change((e) => {
    const files = e.target.files
    const emptyContainers = $(".image-upload-container").filter(function () {
      return !$(this).find("img").length
    })

    for (let i = 0; i < Math.min(files.length, emptyContainers.length); i++) {
      const file = files[i]
      const reader = new FileReader()

      reader.onload = (e) => {
        const container = $(emptyContainers[i])
        const uploadDiv = container.find("div").first()

        uploadDiv.html(`
                    <img src="${e.target.result}" alt="Producto" class="max-h-full max-w-full object-contain">
                `)

        // Add remove button
        if (!container.find(".group").length) {
          container.addClass("group")
          container.append(`
                        <button class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity remove-image">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    `)
        }
      }

      reader.readAsDataURL(file)
    }
  })

  // Remove image functionality
  $(document).on("click", ".remove-image", function (e) {
    e.stopPropagation()
    const container = $(this).parent()
    const uploadDiv = container.find("div").first()

    uploadDiv.html(`
            <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 mb-2"></i>
            <span class="text-sm text-gray-500">Subir imagen</span>
        `)

    container.removeClass("group")
    $(this).remove()
  })

  // Form validation and interactions
  $("input, select, textarea")
    .on("focus", function () {
      $(this).parent().addClass("focused")
    })
    .on("blur", function () {
      $(this).parent().removeClass("focused")
    })

  // Auto-calculate total when prices change
  function updateTotal() {
    const declaredPrice = Number.parseFloat($('input[value="7.5"]').val()) || 0
    const antidumping = Number.parseFloat($('input[value="0.63"]').val()) || 0
    const total = declaredPrice + antidumping

    $('.text-green-600:contains("$")')
      .last()
      .text(`$${total.toFixed(2)}`)
  }

  $('input[type="number"]').on("input", updateTotal)

  // Smooth animations for cards
  $(".bg-white").hover(
    function () {
      $(this).addClass("shadow-md").removeClass("shadow-sm")
    },
    function () {
      $(this).addClass("shadow-sm").removeClass("shadow-md")
    },
  )

  // Success notifications
  $(".bg-green-600").click(() => {
    showNotification("Información guardada exitosamente", "success")
  })

  $(".bg-red-600").click(() => {
    if (confirm("¿Está seguro de que desea limpiar esta sección?")) {
      showNotification("Sección limpiada", "error")
    }
  })

  // Enhanced notification system
  function showNotification(message, type) {
    let bgColor, icon

    switch (type) {
      case "success":
        bgColor = "bg-green-500"
        icon = "fa-check-circle"
        break
      case "error":
        bgColor = "bg-red-500"
        icon = "fa-exclamation-circle"
        break
      case "info":
        bgColor = "bg-blue-500"
        icon = "fa-info-circle"
        break
      default:
        bgColor = "bg-gray-500"
        icon = "fa-bell"
    }

    const notification = $(`
            <div class="fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform">
                <div class="flex items-center">
                    <i class="fas ${icon} mr-2"></i>
                    <span>${message}</span>
                </div>
            </div>
        `)

    $("body").append(notification)

    setTimeout(() => {
      notification.removeClass("translate-x-full")
    }, 100)

    setTimeout(() => {
      notification.addClass("translate-x-full")
      setTimeout(() => {
        notification.remove()
      }, 300)
    }, 3000)
  }

  // Document upload functionality for permits
  $(".document-upload-container, .add-document-btn").click(() => {
    $("#documentUpload").click()
  })

  $("#documentUpload").change((e) => {
    const files = e.target.files

    for (let i = 0; i < files.length; i++) {
      const file = files[i]
      addDocumentToList(file)
    }
  })

  function addDocumentToList(file) {
    const fileSize = (file.size / 1024 / 1024).toFixed(2) // Convert to MB
    const fileIcon = getFileIcon(file.type)

    const documentItem = $(`
        <div class="document-item flex items-center justify-between p-3 bg-gray-50 rounded-lg border">
            <div class="flex items-center">
                <i class="${fileIcon} text-2xl text-blue-600 mr-3"></i>
                <div>
                    <p class="font-medium text-gray-800">${file.name}</p>
                    <p class="text-sm text-gray-500">${fileSize} MB</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <button class="text-blue-600 hover:text-blue-800 transition-colors">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="text-red-600 hover:text-red-800 transition-colors remove-document">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `)

    $("#documentList").append(documentItem)
  }

  function getFileIcon(fileType) {
    if (fileType.includes("pdf")) return "fas fa-file-pdf"
    if (fileType.includes("word") || fileType.includes("document")) return "fas fa-file-word"
    if (fileType.includes("image")) return "fas fa-file-image"
    return "fas fa-file"
  }

  // Remove document functionality
  $(document).on("click", ".remove-document", function () {
    $(this)
      .closest(".document-item")
      .fadeOut(300, function () {
        $(this).remove()
      })
  })

  // Auto-calculate permit costs
  function updatePermitTotal() {
    const baseCost = Number.parseFloat($('#permiso input[value="90"]').val()) || 0
    const processorCost = Number.parseFloat($('#permiso input[value="50"]').val()) || 0
    const subtotal = baseCost + processorCost
    const igv = subtotal * 0.18
    const total = subtotal + igv

    // Update the summary card
    $('.text-green-600:contains("S/.")').text(`S/. ${total.toFixed(2)}`)
  }

  // Update costs when permit values change
  $('#permiso input[type="number"]').on("input", updatePermitTotal)

  // Label image upload functionality
  $(".label-image-upload-container, .add-label-image-btn").click(() => {
    $("#labelImageUpload").click()
  })

  $("#labelImageUpload").change((e) => {
    const files = e.target.files

    for (let i = 0; i < files.length; i++) {
      const file = files[i]
      addLabelImageToList(file)
    }
  })

  function addLabelImageToList(file) {
    const reader = new FileReader()

    reader.onload = (e) => {
      const imageItem = $(`
        <div class="label-image-item relative group">
          <div class="aspect-square rounded-lg overflow-hidden border-2 border-gray-200">
            <img src="${e.target.result}" alt="Etiqueta" class="w-full h-full object-cover">
          </div>
          <button class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity remove-label-image">
            <i class="fas fa-times text-xs"></i>
          </button>
          <div class="mt-2 text-xs text-gray-600 text-center truncate">${file.name}</div>
        </div>
      `)

      $("#labelImageList").append(imageItem)
    }

    reader.readAsDataURL(file)
  }

  // Remove label image functionality
  $(document).on("click", ".remove-label-image", function (e) {
    e.stopPropagation()
    $(this)
      .closest(".label-image-item")
      .fadeOut(300, function () {
        $(this).remove()
      })
  })

  // Special document upload functionality
  $(".special-document-upload-container, .add-special-document-btn").click(() => {
    $("#specialDocumentUpload").click()
  })

  $("#specialDocumentUpload").change((e) => {
    const files = e.target.files

    for (let i = 0; i < files.length; i++) {
      const file = files[i]
      addSpecialDocumentToList(file)
    }
  })

  function addSpecialDocumentToList(file) {
    const fileSize = (file.size / 1024 / 1024).toFixed(2) // Convert to MB
    const fileIcon = getSpecialFileIcon(file.type, file.name)

    const documentItem = $(`
      <div class="special-document-item flex items-center justify-between p-3 bg-gray-50 rounded-lg border">
        <div class="flex items-center">
          <i class="${fileIcon} text-2xl text-orange-600 mr-3"></i>
          <div>
            <p class="font-medium text-gray-800">${file.name}</p>
            <p class="text-sm text-gray-500">${fileSize} MB</p>
          </div>
        </div>
        <div class="flex items-center space-x-2">
          <button class="text-blue-600 hover:text-blue-800 transition-colors">
            <i class="fas fa-eye"></i>
          </button>
          <button class="text-green-600 hover:text-green-800 transition-colors">
            <i class="fas fa-check-circle"></i>
          </button>
          <button class="text-red-600 hover:text-red-800 transition-colors remove-special-document">
            <i class="fas fa-trash"></i>
          </button>
        </div>
      </div>
    `)

    $("#specialDocumentList").append(documentItem)
    updateDocumentProgress()
  }

  function getSpecialFileIcon(fileType, fileName) {
    const extension = fileName.split(".").pop().toLowerCase()

    if (fileType.includes("pdf") || extension === "pdf") return "fas fa-file-pdf"
    if (fileType.includes("word") || extension === "doc" || extension === "docx") return "fas fa-file-word"
    if (fileType.includes("excel") || extension === "xls" || extension === "xlsx") return "fas fa-file-excel"
    if (fileType.includes("image") || ["jpg", "jpeg", "png", "gif"].includes(extension)) return "fas fa-file-image"
    return "fas fa-file"
  }

  // Remove special document functionality
  $(document).on("click", ".remove-special-document", function () {
    $(this)
      .closest(".special-document-item")
      .fadeOut(300, function () {
        $(this).remove()
        updateDocumentProgress()
      })
  })

  // Update document progress
  function updateDocumentProgress() {
    const totalDocuments = $("#specialDocumentList .special-document-item").length
    const progress = Math.min((totalDocuments / 5) * 100, 100) // Assuming 5 documents needed

    $(".bg-orange-600.h-2.rounded-full").css("width", `${progress}%`)
    $(".text-center.text-sm.text-gray-600").text(`${Math.round(progress)}% Completado`)
  }

  // Checklist functionality
  $('input[type="checkbox"]').change(() => {
    const totalCheckboxes = $('input[type="checkbox"]').length
    const checkedBoxes = $('input[type="checkbox"]:checked').length
    const progress = (checkedBoxes / totalCheckboxes) * 100

    // Update any progress indicators if needed
    console.log(`Checklist progress: ${progress}%`)
  })

  // Initialize tooltips and help text
  $("[title]").each(function () {
    $(this).hover(
      function () {
        const tooltip = $(
          `<div class="absolute bg-gray-800 text-white text-xs rounded py-1 px-2 z-10">${$(this).attr("title")}</div>`,
        )
        $(this).append(tooltip)
        $(this).removeAttr("title")
      },
      function () {
        $(this).find(".absolute").remove()
      },
    )
  })

  // IMPORTANTE: Asegúrate de que este archivo se cargue después de dist_v2/js/utils/file_uploader.js en tu HTML

  // Inicialización de FileUploader para cada sección de subida de archivos
  if (document.getElementById('imageUploadContainer')) {
    const imageUploader = new FileUploader({
      containerId: 'imageUploadContainer',
      acceptedTypes: 'image/*',
      maxSize: 5 * 1024 * 1024,
      placeholderText: 'Arrastra o sube la imagen del producto',
      showPreview: true
    });
    document.getElementById('imageUploadContainer').addEventListener('imageUploadContainer-change', (e) => {
      // Aquí puedes acceder al archivo con e.detail.file
      // Por ejemplo, actualizar una variable global o hacer una vista previa adicional
    });
  }

  // --- SUBIDA DE DOCUMENTOS GENERALES ---
  if (document.getElementById('documentUploadContainer')) {
    const docUploader = new FileUploader({
      containerId: 'documentUploadContainer',
      acceptedTypes: 'application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
      maxSize: 10 * 1024 * 1024,
      placeholderText: 'Arrastra o sube un documento',
      showPreview: true
    });
    document.getElementById('documentUploadContainer').addEventListener('documentUploadContainer-change', (e) => {
      // Manejar el archivo subido: e.detail.file
    });
  }

  // --- SUBIDA DE IMÁGENES DE ETIQUETAS ---
  if (document.getElementById('labelImageUploadContainer')) {
    const labelUploader = new FileUploader({
      containerId: 'labelImageUploadContainer',
      acceptedTypes: 'image/*',
      maxSize: 2 * 1024 * 1024,
      placeholderText: 'Arrastra o sube la imagen de la etiqueta',
      showPreview: true
    });
    document.getElementById('labelImageUploadContainer').addEventListener('labelImageUploadContainer-change', (e) => {
      // Manejar el archivo subido: e.detail.file
    });
  }

  // --- SUBIDA DE DOCUMENTOS ESPECIALES ---
  if (document.getElementById('specialDocumentUploadContainer')) {
    const specialDocUploader = new FileUploader({
      containerId: 'specialDocumentUploadContainer',
      acceptedTypes: 'application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,image/*',
      maxSize: 15 * 1024 * 1024,
      placeholderText: 'Arrastra o sube un documento especial',
      showPreview: true
    });
    document.getElementById('specialDocumentUploadContainer').addEventListener('specialDocumentUploadContainer-change', (e) => {
      // Manejar el archivo subido: e.detail.file
    });
  }

  // --- SUBIDA MÚLTIPLE PARA TODOS LOS TABS (UNIFICADO) ---


  // Funciones para Antidumping
  function resetAntidumpingImageSlots() {
    const slotsContainer = document.getElementById('imageUploadSlots');
    if (slotsContainer) {
      slotsContainer.innerHTML = '';
      antidumpingUploaders = [];
      createImageSlotAntidumping();
      updateAddImageBtnAntidumping();
    }
  }

  function createImageSlotAntidumping() {
    if (antidumpingUploaders.length >= MAX_IMAGES_ANTIDUMPING) return;
    const slotId = `antidumping-image-slot-${Date.now()}-${Math.floor(Math.random() * 10000)}`;
    const slotDiv = document.createElement('div');
    slotDiv.className = 'image-upload-slot mb-2';
    slotDiv.id = slotId;
    document.getElementById('imageUploadSlots').appendChild(slotDiv);
    const uploader = new FileUploader({
      containerId: slotId,
      acceptedTypes: 'image/*',
      maxSize: 5 * 1024 * 1024,
      placeholderText: 'Arrastra o sube una imagen',
      showPreview: true
    });
    slotDiv.addEventListener(`${slotId}-change`, (e) => {
      if (!e.detail.file) {
        slotDiv.remove();
        antidumpingUploaders = antidumpingUploaders.filter(u => u.slotId !== slotId);
        if (antidumpingUploaders.length === 0) {
          createImageSlotAntidumping();
        }
        updateAddImageBtnAntidumping();
      }
    });
    antidumpingUploaders.push({ uploader, slotId });
    updateAddImageBtnAntidumping();
  }

  function updateAddImageBtnAntidumping() {
    const btn = document.getElementById('addImageSlotBtn');
    if (!btn) return;
    if (antidumpingUploaders.length >= MAX_IMAGES_ANTIDUMPING) {
      btn.disabled = true;
      btn.classList.add('opacity-50', 'cursor-not-allowed');
    } else {
      btn.disabled = false;
      btn.classList.remove('opacity-50', 'cursor-not-allowed');
    }
  }

  // Funciones para Permiso
  function resetPermisoDocSlots() {
    const slotsContainer = document.getElementById('documentUploadSlots');
    if (slotsContainer) {
      slotsContainer.innerHTML = '';
      permisoUploaders = [];
      createDocSlotPermiso();
      updateAddDocBtnPermiso();
    }
  }

  function createDocSlotPermiso() {
    if (permisoUploaders.length >= MAX_DOCS_PERMISO) return;
    const slotId = `permiso-doc-slot-${Date.now()}-${Math.floor(Math.random() * 10000)}`;
    const slotDiv = document.createElement('div');
    slotDiv.className = 'doc-upload-slot mb-2';
    slotDiv.id = slotId;
    document.getElementById('documentUploadSlots').appendChild(slotDiv);
    const uploader = new FileUploader({
      containerId: slotId,
      acceptedTypes: 'application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
      maxSize: 10 * 1024 * 1024,
      placeholderText: 'Arrastra o sube un documento',
      showPreview: true
    });
    slotDiv.addEventListener(`${slotId}-change`, (e) => {
      if (!e.detail.file) {
        slotDiv.remove();
        permisoUploaders = permisoUploaders.filter(u => u.slotId !== slotId);
        if (permisoUploaders.length === 0) {
          createDocSlotPermiso();
        }
        updateAddDocBtnPermiso();
      }
    });
    permisoUploaders.push({ uploader, slotId });
    updateAddDocBtnPermiso();
  }

  function updateAddDocBtnPermiso() {
    const btn = document.querySelector('.add-document-btn');
    if (!btn) return;
    if (permisoUploaders.length >= MAX_DOCS_PERMISO) {
      btn.disabled = true;
      btn.classList.add('opacity-50', 'cursor-not-allowed');
    } else {
      btn.disabled = false;
      btn.classList.remove('opacity-50', 'cursor-not-allowed');
    }
  }

  // Funciones para Etiquetado
  function resetEtiquetadoImageSlots() {
    const slotsContainer = document.getElementById('labelImageUploadSlots');
    if (slotsContainer) {
      slotsContainer.innerHTML = '';
      etiquetadoUploaders = [];
      createImageSlotEtiquetado();
      updateAddImageBtnEtiquetado();
    }
  }

  function createImageSlotEtiquetado() {
    if (etiquetadoUploaders.length >= MAX_IMAGES_ETIQUETADO) return;
    const slotId = `etiquetado-image-slot-${Date.now()}-${Math.floor(Math.random() * 10000)}`;
    const slotDiv = document.createElement('div');
    slotDiv.className = 'image-upload-slot mb-2';
    slotDiv.id = slotId;
    document.getElementById('labelImageUploadSlots').appendChild(slotDiv);
    const uploader = new FileUploader({
      containerId: slotId,
      acceptedTypes: 'image/*',
      maxSize: 2 * 1024 * 1024,
      placeholderText: 'Arrastra o sube una imagen',
      showPreview: true
    });
    slotDiv.addEventListener(`${slotId}-change`, (e) => {
      if (!e.detail.file) {
        slotDiv.remove();
        etiquetadoUploaders = etiquetadoUploaders.filter(u => u.slotId !== slotId);
        if (etiquetadoUploaders.length === 0) {
          createImageSlotEtiquetado();
        }
        updateAddImageBtnEtiquetado();
      }
    });
    etiquetadoUploaders.push({ uploader, slotId });
    updateAddImageBtnEtiquetado();
  }

  function updateAddImageBtnEtiquetado() {
    const btn = document.querySelector('.add-label-image-btn');
    if (!btn) return;
    if (etiquetadoUploaders.length >= MAX_IMAGES_ETIQUETADO) {
      btn.disabled = true;
      btn.classList.add('opacity-50', 'cursor-not-allowed');
    } else {
      btn.disabled = false;
      btn.classList.remove('opacity-50', 'cursor-not-allowed');
    }
  }

  // Funciones para Documentos Especiales
  function resetDocumentosEspecialesSlots() {
    const slotsContainer = document.getElementById('specialDocumentUploadSlots');
    if (slotsContainer) {
      slotsContainer.innerHTML = '';
      documentosUploaders = [];
      createDocSlotEspeciales();
      updateAddDocBtnEspeciales();
    }
  }

  function createDocSlotEspeciales() {
    if (documentosUploaders.length >= MAX_DOCS_ESPECIALES) return;
    const slotId = `documentos-special-slot-${Date.now()}-${Math.floor(Math.random() * 10000)}`;
    const slotDiv = document.createElement('div');
    slotDiv.className = 'doc-upload-slot mb-2';
    slotDiv.id = slotId;
    document.getElementById('specialDocumentUploadSlots').appendChild(slotDiv);
    const uploader = new FileUploader({
      containerId: slotId,
      acceptedTypes: 'application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,image/*',
      maxSize: 15 * 1024 * 1024,
      placeholderText: 'Arrastra o sube un documento especial',
      showPreview: true
    });
    slotDiv.addEventListener(`${slotId}-change`, (e) => {
      if (!e.detail.file) {
        slotDiv.remove();
        documentosUploaders = documentosUploaders.filter(u => u.slotId !== slotId);
        if (documentosUploaders.length === 0) {
          createDocSlotEspeciales();
        }
        updateAddDocBtnEspeciales();
      }
    });
    documentosUploaders.push({ uploader, slotId });
    updateAddDocBtnEspeciales();
  }

  function updateAddDocBtnEspeciales() {
    const btn = document.querySelector('.add-special-document-btn');
    if (!btn) return;
    if (documentosUploaders.length >= MAX_DOCS_ESPECIALES) {
      btn.disabled = true;
      btn.classList.add('opacity-50', 'cursor-not-allowed');
    } else {
      btn.disabled = false;
      btn.classList.remove('opacity-50', 'cursor-not-allowed');
    }
  }

  // Inicializar slots para todos los tabs
  if (document.getElementById('imageUploadSlots')) {
    resetAntidumpingImageSlots();
    $('#addImageSlotBtn').off('click').on('click', function () {
      createImageSlotAntidumping();
    });
  }

  if (document.getElementById('documentUploadSlots')) {
    resetPermisoDocSlots();
    $('.add-document-btn').off('click').on('click', function () {
      createDocSlotPermiso();
    });
  }

  if (document.getElementById('labelImageUploadSlots')) {
    resetEtiquetadoImageSlots();
    $('.add-label-image-btn').off('click').on('click', function () {
      createImageSlotEtiquetado();
    });
  }

  if (document.getElementById('specialDocumentUploadSlots')) {
    resetDocumentosEspecialesSlots();
    $('.add-special-document-btn').off('click').on('click', function () {
      createDocSlotEspeciales();
    });
  }

  // Eliminar la lógica manual de drag and drop y FileReader para subida de archivos
  // (El resto de la lógica de la app permanece igual, pero ahora la subida de archivos es gestionada por FileUploader)
  // Unificar colores de los slots y botones en todos los tabs
  $(document).on('mouseenter', '.image-upload-slot, .doc-upload-slot', function () {
    $(this).addClass('ring-2 ring-blue-300');
  });
  $(document).on('mouseleave', '.image-upload-slot, .doc-upload-slot', function () {
    $(this).removeClass('ring-2 ring-blue-300');
  });



})
