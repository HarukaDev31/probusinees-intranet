// ========================================
// CONFIGURACIÓN Y CONSTANTES
// ========================================

// Estructura de datos de productos
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

// Constantes para límites de archivos
const MAX_IMAGES_ANTIDUMPING = 5;
const MAX_DOCS_PERMISO = 5;
const MAX_IMAGES_ETIQUETADO = 5;
const MAX_DOCS_ESPECIALES = 5;

// ========================================
// VARIABLES GLOBALES
// ========================================

// Estado de guardado de cada tab
let tabStates = {
  antidumping: false,
  permiso: false,
  etiquetado: false,
  documentos: false
};

let currentTab = 'antidumping';
let tabDirty = false;
let tabData = {
  antidumping: {},
  permiso: {},
  etiquetado: {},
  documentos: {}
};

// Arrays de uploaders para cada sección
var antidumpingUploaders = [];
var permisoUploaders = [];
var etiquetadoUploaders = [];
var documentosUploaders = [];

// Variables de DataTables
let antidumpingTable, permisoTable, etiquetadoTable, documentosTable;
let antidumpingDetailTable;
let permisoDetailTable;
let currentRubroId = null;
let currentEntidadId = null;

// ========================================
// FUNCIONES DE UTILIDAD
// ========================================

function capitalize(str) {
  return str.charAt(0).toUpperCase() + str.slice(1);
}

function showToast(msg, type = "info") {
  if (typeof showNotification === "function") {
    showNotification(msg, type);
  } else {
    const toast = $(`<div class='fixed top-6 right-6 z-50 px-6 py-3 rounded-lg shadow-lg text-white ${type === "success" ? "bg-green-600" : type === "error" ? "bg-red-600" : "bg-blue-600"}'>${msg}</div>`);
    $("body").append(toast);
    setTimeout(() => toast.fadeOut(400, () => toast.remove()), 2500);
  }
}

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

// ========================================
// GESTIÓN DE TABS
// ========================================

function markTabDirty() {
  tabDirty = true;
}

function markTabClean() {
  tabDirty = false;
}

function switchTab(tab) {
  $(".tab-btn").removeClass("active bg-blue-600 text-white").addClass("bg-gray-100 text-gray-700");
  $(`.tab-btn[data-tab='${tab}']`).removeClass("bg-gray-100 text-gray-700").addClass("active bg-blue-600 text-white");
  $(".tab-content").addClass("hidden").removeClass("active");
  $(`#${tab}`).removeClass("hidden").addClass("active");
  currentTab = tab;
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
  
  const atLeastOneComplete = Object.values(tabStates).some(Boolean);
  document.getElementById("guardar-global-btn").disabled = !atLeastOneComplete;
}

function isTabComplete(tab) {
  const selectedRubro = $("#rubroSelector").val();
  if (!selectedRubro) {
    showToast("Debes seleccionar un rubro antes de guardar", "error");
    return false;
  }
  
  let valid = true;
  $(`#${tab} :input[required]:visible`).each(function () {
    if (!$(this).val()) valid = false;
  });
  return valid;
}

function getTabData(tab) {
  const formData = new FormData()
  
  const selectedRubro = $("#rubroSelector").val()
  if (selectedRubro) {
    formData.append('id_rubro', selectedRubro)
  }
  
  $(`#${tab} :input[name*="${tab}-"]`).each(function () {
    const value = $(this).val()
    if (value) {
      formData.append(`${tab}[${$(this).attr('name')}]`, value)
    }
  })
  
  const uploaders = window[`${tab}Uploaders`] || []
  uploaders.forEach((uploaderObj, index) => {
    const file = uploaderObj.uploader.getFile()
    if (file) {
      formData.append(`${tab}[archivos][${index}]`, file)
    }
  })
  
  return formData
}

// ========================================
// GESTIÓN DE SUBTABLAS
// ========================================

function showSubtable(rubroId) {
  currentRubroId = rubroId;
  $('#antidumping-table .table-responsive').addClass('hidden');
  $('#antidumping-detail-table .table-responsive ').removeClass('hidden'); 
  
  let detailTableContainer = $('#antidumping-detail-table');
  if (detailTableContainer.length === 0) {
    const detailHtml = `
      <div id="antidumping-detail-table" class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex justify-between items-center mb-6">
          <div class="flex items-center gap-3">
            <button id="btn-back-to-rubros" class="flex items-center text-gray-600 hover:text-gray-800 transition-all duration-300 hover:scale-105 group">
              <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i>
              <span class="font-medium">Regresar</span>
            </button>
          </div>
          <div class="flex items-center space-x-2">
            <button class="btn-refresh-detail-table bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg transition-all">
              <i class="fas fa-sync-alt"></i>
            </button>
          </div>
        </div>
        <div class="table-responsive">
          <table id="antidumpingDetailTable" class="stripe hover w-full text-sm">
            <thead>
              <tr>
                <th>ID</th>
                <th>Descripción del Producto</th>
                <th>Partida</th>
                <th>P. Declarado</th>
                <th>Antidumping</th>
                <th>Rubro</th>
                <th>Observaciones</th>
                <th>Fecha</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    `;
    $('#antidumping-table').append(detailHtml);
    detailTableContainer = $('#antidumping-detail-table');
  }
  
  detailTableContainer.removeClass('hidden');
  
  if (!$.fn.DataTable.isDataTable('#antidumpingDetailTable')) {
    antidumpingDetailTable = $('#antidumpingDetailTable').DataTable({
      ajax: {
        url: base_url + 'BaseDatos/RegulacionesController/getAllAntidumpingData',
        type: 'POST',
        data: function (d) {
          d.id_rubro = currentRubroId;
        }
      },
      order: [[0, 'desc']],
      paging: true,
      lengthChange: true,
      searching: true,
      ordering: true,
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
  } else {
    antidumpingDetailTable.ajax.reload();
  }
  
  $('#btn-back-to-rubros').off('click').on('click', function() {
    hideSubtable();
  });
  
  $('.btn-refresh-detail-table').off('click').on('click', function() {
    antidumpingDetailTable.ajax.reload();
  });
  
  $(document).off('click', '#antidumpingDetailTable .btn-edit').on('click', '#antidumpingDetailTable .btn-edit', function() {
    const id = $(this).data('id');
    console.log('Editar regulación antidumping ID:', id);
  });
  
  $(document).off('click', '#antidumpingDetailTable .btn-delete').on('click', '#antidumpingDetailTable .btn-delete', function() {
    const id = $(this).data('id');
    if (confirm('¿Está seguro de que desea eliminar esta regulación antidumping?')) {
      console.log('Eliminar regulación antidumping ID:', id);
    }
  });

  // Event listener para botón de ver detalles
  $(document).off('click', '#antidumpingDetailTable .btn-view').on('click', '#antidumpingDetailTable .btn-view', function() {
    const id = $(this).data('id');
    const observaciones = $(this).data('observaciones');
    const imagenes = $(this).data('imagenes');
    showAntidumpingDetails(id, observaciones, imagenes);
  });

  // Event listener para botón de ver imagen en modal
  $(document).off('click', '.view-image-btn').on('click', '.view-image-btn', function() {
    const imageUrl = $(this).data('image-url');
    const imageName = $(this).data('image-name');
    showImageModal(imageUrl, imageName);
  });
}

function hideSubtable() {
  $('#antidumping-detail-table').addClass('hidden');
  $('#antidumping-table .table-responsive').removeClass('hidden');
}

function showPermisoSubtable(entidadId) {
  currentEntidadId = entidadId;
  $('#permiso-table .table-responsive').addClass('hidden');
  $('#permiso-detail-table .table-responsive ').removeClass('hidden'); 
  
  let detailTableContainer = $('#permiso-detail-table');
  if (detailTableContainer.length === 0) {
    const detailHtml = `
      <div id="permiso-detail-table" class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex justify-between items-center mb-6">
          <div class="flex items-center gap-3">
            <button id="btn-back-to-entidades" class="flex items-center text-gray-600 hover:text-gray-800 transition-all duration-300 hover:scale-105 group">
              <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i>
              <span class="font-medium">Regresar</span>
            </button>
          </div>
          <div class="flex items-center space-x-2">
            <button class="btn-refresh-permiso-detail-table bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg transition-all">
              <i class="fas fa-sync-alt"></i>
            </button>
          </div>
        </div>
        <div class="table-responsive">
          <table id="permisoDetailTable" class="stripe hover w-full text-sm">
            <thead>
              <tr>
                <th>ID</th>
                <th>Nombre del Permiso</th>
                <th>C. Permiso</th>
                <th>C. Tramitador</th>
                <th>Rubro</th>
                <th>Observaciones</th>
                <th>Fecha</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    `;
    $('#permiso-table').append(detailHtml);
    detailTableContainer = $('#permiso-detail-table');
  }
  
  detailTableContainer.removeClass('hidden');
  
  if (!$.fn.DataTable.isDataTable('#permisoDetailTable')) {
    permisoDetailTable = $('#permisoDetailTable').DataTable({
      ajax: {
        url: base_url + 'BaseDatos/RegulacionesController/getAllPermisoData',
        type: 'POST',
        data: function (d) {
          d.entidad_id = currentEntidadId;
        }
      },
      order: [[0, 'desc']],
      paging: true,
      lengthChange: true,
      searching: true,
      ordering: true,
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
  } else {
    permisoDetailTable.ajax.reload();
  }
  
  $('#btn-back-to-entidades').off('click').on('click', function() {
    hidePermisoSubtable();
  });
  
  $('.btn-refresh-permiso-detail-table').off('click').on('click', function() {
    permisoDetailTable.ajax.reload();
  });
  
  $(document).off('click', '#permisoDetailTable .btn-edit').on('click', '#permisoDetailTable .btn-edit', function() {
    const id = $(this).data('id');
    console.log('Editar regulación permiso ID:', id);
  });
  
  $(document).off('click', '#permisoDetailTable .btn-delete').on('click', '#permisoDetailTable .btn-delete', function() {
    const id = $(this).data('id');
    if (confirm('¿Está seguro de que desea eliminar esta regulación de permiso?')) {
      console.log('Eliminar regulación permiso ID:', id);
    }
  });

  // Event listener para botón de ver detalles
  $(document).off('click', '#permisoDetailTable .btn-view').on('click', '#permisoDetailTable .btn-view', function() {
    const id = $(this).data('id');
    const observaciones = $(this).data('observaciones');
    const documentos = $(this).data('documentos');
    showPermisoDetails(id, observaciones, documentos);
  });
}

function hidePermisoSubtable() {
  $('#permiso-detail-table').addClass('hidden');
  $('#permiso-table .table-responsive').removeClass('hidden');
}

// ========================================
// FUNCIÓN PARA MOSTRAR DETALLES DE ANTIDUMPING
// ========================================

function showAntidumpingDetails(id, observaciones, imagenes) {
  // Crear el modal de detalles
  const modalHtml = `
    <div id="antidumping-details-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center p-6 border-b">
          <h3 class="text-xl font-bold text-gray-800">Detalles de Regulación Antidumping</h3>
          <button id="close-details-modal" class="text-gray-500 hover:text-gray-700 text-2xl">
            <i class="fas fa-times"></i>
          </button>
        </div>
        
        <div class="p-6">
          <!-- Sección de Observaciones -->
          <div class="mb-8">
            <h4 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
              <i class="fas fa-comment-alt mr-2 text-blue-600"></i>
              Observaciones
            </h4>
            <div class="bg-gray-50 rounded-lg p-4 border">
              <p class="text-gray-800 whitespace-pre-wrap">${observaciones || 'No hay observaciones registradas'}</p>
            </div>
          </div>
          
          <!-- Sección de Imágenes -->
          <div>
            <h4 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
              <i class="fas fa-images mr-2 text-green-600"></i>
              Imágenes Adjuntas
            </h4>
            <div id="images-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              ${renderImages(imagenes)}
            </div>
          </div>
        </div>
        
        <div class="flex justify-end p-6 border-t">
          <button id="close-details-modal-btn" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
            Cerrar
          </button>
        </div>
      </div>
    </div>
  `;
  
  // Remover modal anterior si existe
  $('#antidumping-details-modal').remove();
  
  // Agregar el modal al body
  $('body').append(modalHtml);
  
  // Event listeners para cerrar el modal
  $('#close-details-modal, #close-details-modal-btn').on('click', function() {
    $('#antidumping-details-modal').remove();
  });
  
  // Cerrar modal al hacer clic fuera de él
  $('#antidumping-details-modal').on('click', function(e) {
    if (e.target === this) {
      $(this).remove();
    }
  });
}

// ========================================
// FUNCIÓN PARA MOSTRAR DETALLES DE PERMISO
// ========================================

function showPermisoDetails(id, observaciones, documentos) {
  // Crear el modal de detalles
  const modalHtml = `
    <div id="permiso-details-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center p-6 border-b">
          <h3 class="text-xl font-bold text-gray-800">Detalles de Regulación de Permiso</h3>
          <button id="close-permiso-details-modal" class="text-gray-500 hover:text-gray-700 text-2xl">
            <i class="fas fa-times"></i>
          </button>
        </div>
        
        <div class="p-6">
          <!-- Sección de Observaciones -->
          <div class="mb-8">
            <h4 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
              <i class="fas fa-comment-alt mr-2 text-blue-600"></i>
              Observaciones
            </h4>
            <div class="bg-gray-50 rounded-lg p-4 border">
              <p class="text-gray-800 whitespace-pre-wrap">${observaciones || 'No hay observaciones registradas'}</p>
            </div>
          </div>
          
          <!-- Sección de Documentos -->
          <div>
            <h4 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
              <i class="fas fa-file-alt mr-2 text-green-600"></i>
              Documentos Adjuntos
            </h4>
            <div id="documentos-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              ${renderDocuments(documentos)}
            </div>
          </div>
        </div>
        
        <div class="flex justify-end p-6 border-t">
          <button id="close-permiso-details-modal-btn" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
            Cerrar
          </button>
        </div>
      </div>
    </div>
  `;
  
  // Remover modal anterior si existe
  $('#permiso-details-modal').remove();
  
  // Agregar el modal al body
  $('body').append(modalHtml);
  
  // Event listeners para cerrar el modal
  $('#close-permiso-details-modal, #close-permiso-details-modal-btn').on('click', function() {
    $('#permiso-details-modal').remove();
  });
  
  // Cerrar modal al hacer clic fuera de él
  $('#permiso-details-modal').on('click', function(e) {
    if (e.target === this) {
      $(this).remove();
    }
  });
}

function renderImages(imagenes) {
  if (!imagenes || imagenes.length === 0) {
    return `
      <div class="col-span-full text-center py-8">
        <i class="fas fa-image text-4xl text-gray-300 mb-2"></i>
        <p class="text-gray-500">No hay imágenes adjuntas</p>
      </div>
    `;
  }
  
  let imagesHtml = '';
  imagenes.forEach((imagen, index) => {
    const imageUrl = imagen.ruta;
    const fileName = imagen.nombre_original || 'Imagen ' + (index + 1);
    const fileSize = (imagen.peso / 1024 / 1024).toFixed(2) + ' MB';
    
    imagesHtml += `
      <div class="bg-white rounded-lg border shadow-sm overflow-hidden group">
        <div class="aspect-square relative">
          <img src="${imageUrl}" alt="${fileName}" class="w-full h-full object-cover">
          <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-300 flex items-center justify-center">
            <button class="view-image-btn opacity-0 group-hover:opacity-100 bg-white text-gray-800 px-3 py-1 rounded-lg shadow-lg transition-all transform scale-90 group-hover:scale-100" data-image-url="${imageUrl}" data-image-name="${fileName}">
              <i class="fas fa-expand-alt mr-1"></i>Ver
            </button>
          </div>
        </div>
        <div class="p-3">
          <p class="text-sm font-medium text-gray-800 truncate" title="${fileName}">${fileName}</p>
          <p class="text-xs text-gray-500">${fileSize}</p>
        </div>
      </div>
    `;
  });
  
  return imagesHtml;
}

function renderDocuments(documentos) {
  if (!documentos || documentos.length === 0) {
    return `
      <div class="col-span-full text-center py-8">
        <i class="fas fa-file-alt text-4xl text-gray-300 mb-2"></i>
        <p class="text-gray-500">No hay documentos adjuntos</p>
      </div>
    `;
  }
  
  let documentsHtml = '';
  documentos.forEach((documento, index) => {
    const documentUrl = documento.ruta;
    const fileName = documento.nombre_original || 'Documento ' + (index + 1);
    const fileSize = (documento.peso / 1024 / 1024).toFixed(2) + ' MB';
    const fileExtension = fileName.split('.').pop().toLowerCase();
    
    // Icono según el tipo de archivo
    let fileIcon = 'fas fa-file';
    if (fileExtension === 'pdf') fileIcon = 'fas fa-file-pdf';
    else if (['doc', 'docx'].includes(fileExtension)) fileIcon = 'fas fa-file-word';
    else if (['xls', 'xlsx'].includes(fileExtension)) fileIcon = 'fas fa-file-excel';
    else if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) fileIcon = 'fas fa-file-image';
    
    documentsHtml += `
      <div class="bg-white rounded-lg border shadow-sm overflow-hidden group">
        <div class="aspect-square relative bg-gray-50 flex items-center justify-center">
          <div class="text-center">
            <i class="${fileIcon} text-4xl text-gray-400 mb-2"></i>
            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-300 flex items-center justify-center">
              <a href="${documentUrl}" download="${fileName}" class="view-document-btn opacity-0 group-hover:opacity-100 bg-white text-gray-800 px-3 py-1 rounded-lg shadow-lg transition-all transform scale-90 group-hover:scale-100">
                <i class="fas fa-download mr-1"></i>Descargar
              </a>
            </div>
          </div>
        </div>
        <div class="p-3">
          <p class="text-sm font-medium text-gray-800 truncate" title="${fileName}">${fileName}</p>
          <p class="text-xs text-gray-500">${fileSize}</p>
        </div>
      </div>
    `;
  });
  
  return documentsHtml;
}

// ========================================
// FUNCIÓN PARA MOSTRAR MODAL DE IMAGEN
// ========================================

function showImageModal(imageUrl, imageName) {
  const modalHtml = `
    <div id="image-modal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50">
      <div class="relative max-w-4xl max-h-[90vh] mx-4">
        <div class="bg-white rounded-lg shadow-xl overflow-hidden">
          <div class="flex justify-between items-center p-4 border-b">
            <h4 class="text-lg font-semibold text-gray-800 truncate">${imageName}</h4>
            <button id="close-image-modal" class="text-gray-500 hover:text-gray-700 text-2xl">
              <i class="fas fa-times"></i>
            </button>
          </div>
          <div class="p-4">
            <img src="${imageUrl}" alt="${imageName}" class="max-w-full max-h-[70vh] object-contain mx-auto">
          </div>
          <div class="flex justify-between items-center p-4 border-t">
            <a href="${imageUrl}" download="${imageName}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
              <i class="fas fa-download mr-2"></i>Descargar
            </a>
            <button id="close-image-modal-btn" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
              Cerrar
            </button>
          </div>
        </div>
      </div>
    </div>
  `;
  
  // Remover modal anterior si existe
  $('#image-modal').remove();
  
  // Agregar el modal al body
  $('body').append(modalHtml);
  
  // Event listeners para cerrar el modal
  $('#close-image-modal, #close-image-modal-btn').on('click', function() {
    $('#image-modal').remove();
  });
  
  // Cerrar modal al hacer clic fuera de él
  $('#image-modal').on('click', function(e) {
    if (e.target === this) {
      $(this).remove();
    }
  });
  
  // Cerrar modal con tecla ESC
  $(document).on('keydown.imageModal', function(e) {
    if (e.key === 'Escape') {
      $('#image-modal').remove();
      $(document).off('keydown.imageModal');
    }
  });
}

// ========================================
// FUNCIÓN PARA CREAR ENTIDAD REGULADORA
// ========================================

function showCreateEntidadModal() {
  const modalHtml = `
    <div id="create-entidad-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="flex justify-between items-center p-6 border-b">
          <h3 class="text-xl font-bold text-gray-800">Crear Nueva Entidad Reguladora</h3>
          <button id="close-create-entidad-modal" class="text-gray-500 hover:text-gray-700 text-2xl">
            <i class="fas fa-times"></i>
          </button>
        </div>
        
        <form id="create-entidad-form" class="p-6">
          <div class="mb-4">
            <label for="entidad-nombre" class="block text-sm font-medium text-gray-700 mb-2">
              Nombre de la Entidad *
            </label>
            <input 
              type="text" 
              id="entidad-nombre" 
              name="nombre" 
              required 
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Ej: SUNAT, DIGESA, etc."
            >
          </div>
          
          <div class="mb-6">
            <label for="entidad-descripcion" class="block text-sm font-medium text-gray-700 mb-2">
              Descripción (opcional)
            </label>
            <textarea 
              id="entidad-descripcion" 
              name="descripcion" 
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Descripción de la entidad reguladora..."
            ></textarea>
          </div>
          
          <div class="flex justify-end space-x-3">
            <button 
              type="button" 
              id="cancel-create-entidad" 
              class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors"
            >
              Cancelar
            </button>
            <button 
              type="submit" 
              class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center"
            >
              <i class="fas fa-save mr-2"></i>
              Crear Entidad
            </button>
          </div>
        </form>
      </div>
    </div>
  `;
  
  // Remover modal anterior si existe
  $('#create-entidad-modal').remove();
  
  // Agregar el modal al body
  $('body').append(modalHtml);
  
  // Event listeners para cerrar el modal
  $('#close-create-entidad-modal, #cancel-create-entidad').on('click', function() {
    $('#create-entidad-modal').remove();
  });
  
  // Cerrar modal al hacer clic fuera de él
  $('#create-entidad-modal').on('click', function(e) {
    if (e.target === this) {
      $(this).remove();
    }
  });
  
  // Manejar el envío del formulario
  $('#create-entidad-form').on('submit', async function(e) {
    e.preventDefault();
    
    const nombre = $('#entidad-nombre').val().trim();
    const descripcion = $('#entidad-descripcion').val().trim();
    
    if (!nombre) {
      showToast('El nombre de la entidad es obligatorio', 'error');
      return;
    }
    
    try {
      const formData = new FormData();
      formData.append('nombre', nombre);
      formData.append('descripcion', descripcion);
      
      const response = await fetch(base_url + 'BaseDatos/RegulacionesController/createEntidadReguladora', {
        method: 'POST',
        body: formData
      });
      
      const result = await response.json();
      
      if (result.success) {
        showToast('Entidad creada exitosamente', 'success');
        
        // Cerrar el modal
        $('#create-entidad-modal').remove();
        
        // Recargar las entidades en el select
        await loadEntidadesReguladoras();
        
        // Seleccionar la nueva entidad creada
        const entidadSelect = $('#entidadSelector');
        entidadSelect.val(result.data.id);
        
      } else {
        showToast('Error al crear entidad: ' + result.message, 'error');
      }
    } catch (error) {
      console.error('Error al crear entidad:', error);
      showToast('Error de conexión al crear entidad', 'error');
    }
  });
}

// ========================================
// GESTIÓN DE ARCHIVOS
// ========================================

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

// ========================================
// GESTIÓN DE DATOS
// ========================================

async function loadEntidadesReguladoras() {
  try {
    // Limpiar y deshabilitar el select mientras carga
    const entidadSelect = $('#entidadSelector');
    if (entidadSelect.length === 0) return; // Si no existe el select, salir
    
    entidadSelect.empty();
    entidadSelect.append('<option value="">Cargando entidades...</option>');
    entidadSelect.prop('disabled', true);
    
    const response = await fetch(base_url + 'BaseDatos/RegulacionesController/getEntidadesReguladoras', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      }
    });
    
    const result = await response.json();
    
    if (result.success) {
      // Llenar el select de entidades
      entidadSelect.empty();
      entidadSelect.append('<option value="">Selecciona una entidad</option>');
      
      result.data.forEach(entidad => {
        entidadSelect.append(`<option value="${entidad.id}">${entidad.nombre}</option>`);
      });
      
      // Habilitar el select de entidades
      entidadSelect.prop('disabled', false);
      
      showToast(`Entidades cargadas correctamente`, "success");
    } else {
      entidadSelect.empty();
      entidadSelect.append('<option value="">Selecciona una entidad para continuar</option>');
      entidadSelect.prop('disabled', true);
    }
  } catch (error) {
    console.error('Error al cargar entidades:', error);
    const entidadSelect = $('#entidadSelector');
    if (entidadSelect.length > 0) {
      entidadSelect.empty();
      entidadSelect.append('<option value="">Error de conexión</option>');
      entidadSelect.prop('disabled', true);
    }
    showToast("Error de conexión al cargar entidades", "error");
  }
}

async function loadRubrosForProduct() {
  try {
    const rubroSelect = $('#rubroSelector');
    rubroSelect.empty();
    rubroSelect.append('<option value="">Cargando rubros...</option>');
    rubroSelect.prop('disabled', true);
    
    const response = await fetch(base_url + 'BaseDatos/RegulacionesController/getRubrosByProduct', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      }
    });
    
    const result = await response.json();
    
    if (result.success) {
      rubroSelect.empty();
      rubroSelect.append('<option value="">Selecciona un rubro</option>');
      
      result.data.forEach(rubro => {
        rubroSelect.append(`<option value="${rubro.id}">${rubro.nombre}</option>`);
      });
      
      rubroSelect.prop('disabled', false);
      showToast(`Rubros cargados correctamente`, "success");
    } else {
      rubroSelect.empty();
      rubroSelect.append('<option value="">Error al cargar rubros</option>');
      rubroSelect.prop('disabled', true);
      showToast("Error al cargar rubros: " + result.message, "error");
    }
  } catch (error) {
    console.error('Error al cargar rubros:', error);
    const rubroSelect = $('#rubroSelector');
    rubroSelect.empty();
    rubroSelect.append('<option value="">Error de conexión</option>');
    rubroSelect.prop('disabled', true);
    showToast("Error de conexión al cargar rubros", "error");
  }
}

async function saveRegulaciones() {
  try {
    const formData = new FormData()
    formData.append('created_at', new Date().toISOString())
    
    Object.keys(tabData).forEach(tab => {
      const tabFormData = tabData[tab]
      if (tabFormData instanceof FormData) {
        for (let [key, value] of tabFormData.entries()) {
          formData.append(key, value)
        }
      }
    })
    
    const response = await fetch(base_url + 'BaseDatos/RegulacionesController/saveRegulacion', {
      method: 'POST',
      body: formData
    })
    
    const result = await response.json()
    
    if (result.success) {
      showToast('Regulaciones guardadas correctamente', 'success')
      
      if (antidumpingTable) antidumpingTable.ajax.reload()
      if (permisoTable) permisoTable.ajax.reload()
      if (etiquetadoTable) etiquetadoTable.ajax.reload()
      if (documentosTable) documentosTable.ajax.reload()
      
      tabStates = { antidumping: false, permiso: false, etiquetado: false, documentos: false }
      tabData = {}
      updateStepper()
      
      $('.formulario-container').addClass('hidden')
      $('.container').removeClass('hidden')
    } else {
      showToast('Error al guardar: ' + result.message, 'error')
    }
  } catch (error) {
    console.error('Error al guardar:', error)
    showToast('Error de conexión al guardar', 'error')
  }
}

// ========================================
// INICIALIZACIÓN DE DATATABLES
// ========================================

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
          // Filtros adicionales si los necesitas
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
          // Filtros adicionales si los necesitas
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
          // Filtros adicionales si los necesitas
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
          // Filtros adicionales si los necesitas
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
    $('.container').addClass('hidden');
    $('.formulario-container').removeClass('hidden');
  });

  // Botón Volver a Tablas
  $('#btn-volver-tablas').click(function () {
    $('.formulario-container').addClass('hidden');
    $('.container').removeClass('hidden');
  });

  // Tabs de regulaciones
  $('.regulacion-tab-btn').click(function () {
    const tab = $(this).data('tab');

    $('.regulacion-tab-btn').removeClass('active bg-blue-600 text-white').addClass('bg-gray-100 text-gray-700');
    $(this).removeClass('bg-gray-100 text-gray-700').addClass('active bg-blue-600 text-white');

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
    console.log('Editar regulación ID:', id);
  });

  // Botones de eliminar
  $(document).on('click', '.btn-delete', function () {
    const id = $(this).data('id');
    if (confirm('¿Está seguro de que desea eliminar esta regulación?')) {
      console.log('Eliminar regulación ID:', id);
    }
  });

  // Botón para mostrar subtabla
  $('#antidumpingTable').on('click', '.btn-show-subtable', function() {
    const rubroId = $(this).data('rubro-id');
    showSubtable(rubroId);
  });

  // Botón para mostrar subtabla de permisos
  $('#permisoTable').on('click', '.btn-show-subtable', function() {
    const entidadId = $(this).data('entidad-id');
    showPermisoSubtable(entidadId);
  });
}

// ========================================
// EVENT LISTENERS
// ========================================

$(document).ready(() => {
  initializeDataTables();
  initializeDataTableEventListeners();
  loadRubrosForProduct();
  loadEntidadesReguladoras();

  // Detectar cambios en inputs para marcar el tab como "dirty"
  $(document).on("input change", ".tab-content.active :input", markTabDirty);

  // Guardar por tab
  $(document).on("click", ".guardar-tab-btn", function () {
    const tab = $(this).data("tab");
    if (!isTabComplete(tab)) {
      showToast("Completa todos los campos obligatorios antes de guardar.", "error");
      return;
    }
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
          switchTab(nextTab);
          markTabClean();
        }
      );
      return;
    }
    switchTab(nextTab);
  });

  // Guardar global
  $("#guardar-global-btn").click(async function () {
    if (!Object.values(tabStates).some(Boolean)) {
      showToast("No hay datos para guardar", "error");
      return;
    }

    const formData = new FormData();

    Object.entries(tabData).forEach(([tab, data]) => {
      if (tabStates[tab]) {
        Object.entries(data).forEach(([key, value]) => {
          if (key === 'archivos' && Array.isArray(value)) {
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

    const selectedProduct = $("#productSelector").val();
    formData.append('producto', selectedProduct);
    formData.append('created_at', new Date().toISOString());

    await saveRegulaciones();
  });

  // Rubro selector change
  $("#rubroSelector").change(function () {
    const selectedRubro = $(this).val()
    if (selectedRubro) {
      showToast(`Rubro seleccionado`, "info")
    }
  });

  // Entidad selector change
  $("#entidadSelector").change(function () {
    const selectedEntidad = $(this).val()
    if (selectedEntidad) {
      showToast(`Entidad seleccionada`, "info")
    }
  });

  // Botón para crear nueva entidad
  $(document).on('click', '#btn-create-entidad', function() {
    showCreateEntidadModal();
  });

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

  // Efectos visuales para slots
  $(document).on('mouseenter', '.image-upload-slot, .doc-upload-slot', function () {
    $(this).addClass('ring-2 ring-blue-300');
  });
  $(document).on('mouseleave', '.image-upload-slot, .doc-upload-slot', function () {
    $(this).removeClass('ring-2 ring-blue-300');
  });
});
