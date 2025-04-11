var spinner = null;
var table_Entidad = null;
var idContenedor = 0;
var btnCrear = null;
var btnCrearCotizacion = null;
var paises = [];
var fToday = new Date();
var fYear = fToday.getFullYear();
var fDay = fToday.getDate();
var currentCarga = 0;
var currentProveedor = 0;
var currentCotizacion;
var currentTableCotizacion = "prospectos";
var currentPrivilege = localStorage.getItem("currentPrivilege") == null ? "" : localStorage.getItem("currentPrivilege");
var fileManager = null;
var fileManagerInspection = null;
var fileManagerInspectionCoordinacion = null;
var currentCargaNumber = 0;
var selectedTabDocumentacionId = 0;
var shouldSaveInspection = false;
var originalNoteInpectionText = "";
var shouldSaveDocumentacion = false;
var originalVolumenDocumento = "";
var originalValorDocumento = "";
var sectionsDisabled = false;
var meses = [
  {
    id: "ENERO",
    value: "ENERO",
  },
  {
    id: "FEBRERO",
    value: "FEBRERO",
  },
  {
    id: "MARZO",
    value: "MARZO",
  },
  {
    id: "ABRIL",
    value: "ABRIL",
  },
  {
    id: "MAYO",
    value: "MAYO",
  },
  {
    id: "JUNIO",
    value: "JUNIO",
  },
  {
    id: "JULIO",
    value: "JULIO",
  },
  {
    id: "AGOSTO",
    value: "AGOSTO",
  },
  {
    id: "SETIEMBRE",
    value: "SETIEMBRE",
  },
  {
    id: "OCTUBRE",
    value: "OCTUBRE",
  },
  {
    id: "NOVIEMBRE",
    value: "NOVIEMBRE",
  },
  {
    id: "DICIEMBRE",
    value: "DICIEMBRE",
  },
];
var contentHeader = null;
var stepsContainer = null;
var mainContainer = null;
var stepIndex = 0;
var stepId = 0;
var cotizacionContainer = null;
var clientesContainer = null;
var tableCotizacion = null;
var tableCotizacionEmbarque = null;
var tableClientesGeneral = null;
var tableClientesVariacion = null;
var clientesDocumentacionContainer = null;
var documentationContainer = null;
var idCotizacion = 0;
var cotizacionInspectionContainer = null;
var dropZone;
var fileInput;
var fileList;
var cotizacionAlmacenContainer = null;
var cotizacionFinalContainer = null;
var tableCotizacionFinal = null;
var facturaGuiaContainer = null;
var tableFacturaGuia = null;
var documentationContainerProfile = null;
var documentacionSelectedProvider = 0;
var documentacionDocumentacionContainer = null;
var documentacionAduanaContainer = null;

function getSwalConfig(type, privilege) {
  const isEnglish = privilege === "ContenedorAlmacen";

  const messages = {
    confirmDelete: {
      title: isEnglish ? "Are you sure?" : "¿Estás seguro?",
      text: isEnglish ? "You won't be able to revert this!" : "¡No podrás revertir esto!",
      icon: "warning",
      confirmButtonText: isEnglish ? "Yes, delete it" : "Sí, eliminarlo",
      cancelButtonText: isEnglish ? "No, cancel" : "No, cancelar",
    },
    confirmSave: {
      title: isEnglish ? "Are you sure you want to save?" : "¿Estás seguro de que deseas guardar?",
      text: isEnglish ? "This action will save the changes." : "Esta acción guardará los cambios.",
      icon: "warning",
      confirmButtonText: isEnglish ? "Yes, save it" : "Sí, guardarlo",
      cancelButtonText: isEnglish ? "No, cancel" : "No, cancelar",
    },
    successDelete: {
      title: isEnglish ? "Deleted!" : "Eliminado!",
      text: isEnglish ? "The file has been deleted." : "El archivo ha sido eliminado.",
    },
    successSave: {
      title: isEnglish ? "Saved!" : "¡Guardado!",
      text: isEnglish ? "The changes have been saved successfully." : "Los cambios se han guardado correctamente.",
    },
    error: {
      title: isEnglish ? "Error!" : "¡Error!",
      text: isEnglish ? "An error occurred." : "Ocurrió un error.",
    },
  };

  return messages[type];
}

// Obtener configuración de Swal para guardar
const swalConfig = getSwalConfig("confirmSave", currentPrivilege);
const successConfig = getSwalConfig("successSave", currentPrivilege);
const errorConfig = getSwalConfig("error", currentPrivilege);


async function saveDocumentation() {
  event.preventDefault();

  //show confirm swall


  // upload add idCotizacion and get files from #file-inpute
  const formData = new FormData();
  formData.append("idCotizacion", currentCotizacion);
  formData.append("idProveedor", currentProveedor);
  const fileInput = $("#file-input-documentacion")[0];
  const files = fileInput.files;
  for (let i = 0; i < files.length; i++) {
    formData.append("files[]", files[i]);
  }
  spinner.show();
  url =
    base_url + "CargaConsolidada/ContenedorConsolidado/saveDocumentation";
  $.ajax({
    url: url,
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    success: function (response) {
      const result = JSON.parse(response);
      if (result.status == "success") {
        Swal.fire(successConfig.title, result.message, "success");
      } else {
        Swal.fire(errorConfig.title, result.message, "error");
      }
      spinner.hide();
      //clear input and file-lista
      fileInput.value = "";
      $("#file-lista-documentacion").html("");
      getFilesAlmacenDocument(currentProveedor, currentCotizacion).then(
        (files) => {
          files.forEach((file) =>
            addFileToList(file, null, "file-lista-documentacion")
          );
        }
      );
    },
    error: function () {
      spinner.hide();
    },
  });


}
async function saveInspection() {
  event.preventDefault();
  //show confirm swall

  // upload add idCotizacion and get files from #file-inpute
  const formData = new FormData();
  formData.append("idCotizacion", currentCotizacion);
  formData.append("idProveedor", currentProveedor);
  const fileInput = $("#file-input-inspeccion")[0];
  const files = fileInput.files;
  for (let i = 0; i < files.length; i++) {
    formData.append("files[]", files[i]);
  }

  spinner.show();
  url = base_url + "CargaConsolidada/ContenedorConsolidado/saveInspection";
  $.ajax({
    url: url,
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    success: function (response) {
      const result = JSON.parse(response);
      if (result.status == "success") {
        Swal.fire(successConfig.title, result.message, "success");
        reloadTableCotizacionEmbarque();
      } else {
        Swal.fire(errorConfig.title, result.message, "error");
      }
      spinner.hide();
      //clear input and file-lista
      fileInput.value = "";
      $("#file-lista-inspection").html("");
      getFilesAlmacenInspection(currentProveedor, currentCotizacion).then(
        (files) => {
          files.forEach((file) =>
            addFileToList(file, null, "file-lista-inspection")
          );
        }
      );
    },
    error: function () {
      spinner.hide();
    },
  });


}
async function descargarBoletaPDF(idCotizacionFinal) {
  spinner.show();
  $.ajax({
    url:
      base_url +
      "CargaConsolidada/ContenedorConsolidado/downloadBoleta/" +
      idCotizacionFinal,
    type: "GET",
    xhrFields: {
      responseType: "blob",
    },
    //   data: JSON.stringify({ idCotizacionFinal: idCotizacionFinal }),
    success: function (response) {
      var blob = new Blob([response], {
        type: "application/pdf",
      });
      var link = document.createElement("a");
      link.href = window.URL.createObjectURL(blob);
      const currentDate = new Date();
      //format date to dd_mm_yyyy
      const formattedDate = `${currentDate.getDate()}_${currentDate.getMonth() + 1
        }_${currentDate.getFullYear()}`;
      link.download = `Cotizacion.pdf`;
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);

      spinner.hide();
    },
    error: function (errorThrown) {
      Swal.fire(errorConfig.title, "Hubo un error", "error");
      console.error("Error al descargar el archivo Excel: " + errorThrown);
      spinner.hide();
    },
  });
}
async function updateEstadoDocumentacion(id) {
  const estado = $(`#estado-documentacion-${id}`).val();
  url =
    base_url +
    "CargaConsolidada/ContenedorConsolidado/updateEstadoDocumentacion";
  $.ajax({
    url: url,
    type: "POST",
    data: {
      id: id,
      estado: estado,
    },
    success: function (response) {
      const result = JSON.parse(response);
      if (result.status == "success") {
        Swal.fire(successConfig.title, result.message, "success");
      } else {
        Swal.fire(errorConfig.title, result.message, "error");
      }
      table_Entidad.ajax.reload();
    },
  });
}
async function updateEstado(id) {
  const estado = $(`#estado-${id}`).val();
  url = base_url + "CargaConsolidada/ContenedorConsolidado/updateEstado";
  $.ajax({
    url: url,
    type: "POST",
    data: {
      id: id,
      estado: estado,
    },
    success: function (response) {
      const result = JSON.parse(response);
      if (result.status == "success") {
        Swal.fire(successConfig.title, result.message, "success");
      } else {
        Swal.fire(errorConfig.title, result.message, "error");
      }
      table_Entidad.ajax.reload();
    },
  });
}
async function uploadCotizacionFinal(id) {
  //swall with file input
  const { value: file } = await Swal.fire({
    title: "Subir Cotización Final",
    input: "file",
    inputAttributes: {
      //excel
      accept: ".xlsx, .xls,.xlsm, .xlsb, .xltx, .xltm, .xlam, .xla, .xlw",
      "aria-label": "Sube tu archivo",
    },
    showCancelButton: true,
    confirmButtonText: "Subir",
    cancelButtonText: "Cancelar",
    inputValidator: (value) => {
      if (!value) {
        return "Debes elegir un archivo!";
      }
    },
  });
  if (file) {
    const formData = new FormData();
    formData.append("file", file);
    formData.append("idCotizacionFinal", id);
    url =
      base_url + "CargaConsolidada/ContenedorConsolidado/uploadCotizacionFinal";
    $.ajax({
      url: url,
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        const result = JSON.parse(response);
        if (result.status == "success") {
          Swal.fire("Correcto!", result.message, "success");
          tableCotizacionFinal.ajax.reload();
        } else {
          Swal.fire("Error!", result.message, "error");
        }
      },
    });
  }
}
async function deleteCotizacionFinalFile(id) {
  Swal.fire(
    {
      title: "¿Estás seguro?",
      text: "¡No podrás revertir esto!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Sí, eliminarlo",
      cancelButtonText: "No, cancelar",
      iconColor: "#FF0000",
      color: "#FF0000",
    }).then((result) => {
      if (result.isConfirmed) {
        url =
          base_url +
          "CargaConsolidada/ContenedorConsolidado/deleteCotizacionFinalFile/" +
          id;
        $.ajax({
          url: url,
          type: "GET",

          success: function (response) {
            const result = JSON.parse(response);
            if (result.status == "success") {
              tableCotizacionFinal.ajax.reload();
              Swal.fire("Eliminado!", result.message, "success");
            } else {
              Swal.fire("Error!", result.message, "error");
            }
            reloadTableCotizacionFinal();
          },
        });
      }
    });
}
async function updateEstadoCotizacionFinal(idCotizacionFinal) {
  const estado = $(`#estado-cotizacion-final${idCotizacionFinal}`).val();

  url =
    base_url +
    "CargaConsolidada/ContenedorConsolidado/updateEstadoCotizacionFinal";
  $.ajax({
    url: url,
    type: "POST",
    data: {
      idCotizacionFinal: idCotizacionFinal,
      estado: estado,
    },
    success: function (response) {
      const result = JSON.parse(response);
      if (result.status == "success") {
        Swal.fire("Correcto!", result.message, "success");
        reloadTableCotizacionFinal();
      } else {
        Swal.fire("Error!", result.message, "error");
      }
      table_Entidad.ajax.reload();
    },
  });
}
async function saveBoth() {

  try {
    //promise all
    Promise.all([saveInspection(), addNote()]);

  } catch (e) {
    console.error(e);
  }
}


function deleteFile(fileId, cardElement, deleteFileList) {
  console.log(deleteFileList, "deleteFileList");
  if (deleteFileList == "file-lista-documentacion") {
    url = base_url + "CargaConsolidada/ContenedorConsolidado/deleteFileDocumentation/" + fileId;
  } else if (deleteFileList == "file-lista-aduana") {
    url = base_url + "CargaConsolidada/ContenedorConsolidado/deleteFileAduana/" + fileId;
  }

  else {
    url = base_url + "CargaConsolidada/ContenedorConsolidado/deleteFileInspection/" + fileId;
  }
  $.ajax({
    url,
    type: "GET",
    success: function (response) {
      const data = JSON.parse(response);
      if (data.status === "success") {
        cardElement.remove();
      } else {
        alert("Error deleting file: " + data.message);
      }
    },
    error: function () {
      alert("An error occurred while deleting the file.");
    },
  });
}
function addFileToList(file, fileList = null, id = null, deleteHidden = false) {
  if (fileList == null) {
    fileList = $(".file-lista");
  }
  if (id != null) {
    fileList = $(`#${id}`);
  }
  let listId = id;

  // Usar la función getIconByType para obtener el ícono correspondiente
  const fileIcon = getIconByType(file.file_ext);

  // Verificar si el usuario pertenece al grupo "Cotizador"
  const isCotizador = currentPrivilege === "Cotizador";


  // Crear el elemento HTML para el archivo
  const fileItem = $(`
        <div class="file-item">
            <div class="file-preview d-flex align-items-center">
                ${fileIcon}&nbsp;
                <p>${file.file_name}</p>
            </div>
            <div class="file-actions d-flex flex-row">
                <button class="btn-sm download-btn" data-url="${file.file_url}">
                    <i class="fas fa-download"></i> 
                </button>
                ${!isCotizador && !deleteHidden
      ? `<button class="btn-sm delete-btn" data-id="${file.id}">
                        <i class="far fa-trash-alt"></i>
                      </button>`
      : ""
    }
            </div>
        </div>
    `);

  // Botón de descarga
  fileItem.find(".download-btn").on("click", function (event) {
    event.preventDefault();
    const url = $(this).data("url");
    window.open(url, "_blank");
  });

  // Botón de eliminar
  fileItem.find(".delete-btn").on("click", function (event) {
    event.preventDefault();
    const id = $(this).data("id");
    deleteFile(id, fileItem, listId);
  });

  // Agregar funcionalidad de vista previa para imágenes y videos
  if (file.file_ext.startsWith("image/") || file.file_ext.startsWith("video/")) {
    fileItem.find(".file-preview").css("cursor", "pointer");
    fileItem.find(".file-preview").on("click", function () {
      if (file.file_ext.startsWith("image/")) {
        // Mostrar la imagen en el modal
        const modal = document.getElementById("image-modal");
        const modalImage = modal.querySelector("#image-preview");
        modalImage.src = file.file_url;
        const bootstrapModal = new bootstrap.Modal(modal);
        bootstrapModal.show();
      } else if (file.file_ext.startsWith("video/")) {
        // Mostrar el video en el modal
        const modal = document.getElementById("video-modal");
        const modalVideo = modal.querySelector("#video-preview");
        modalVideo.src = file.file_url;
        modalVideo.load(); // Cargar el video
        const bootstrapModal = new bootstrap.Modal(modal);
        bootstrapModal.show();

        // Detener el video cuando se cierre el modal
        modal.addEventListener('click', (e) => {
          if (e.target === modal) { // Verifica si el clic fue en el fondo del modal
            modalVideo.pause(); // Pausar el video
            modalVideo.currentTime = 0; // Reiniciar el video al inicio
            bootstrapModal.hide(); // Cerrar el modal
          }
        });
      }
    });
  }

  fileList.append(fileItem);
  // Remover clase "hidden" si está presente
  fileList.removeClass("hidden");
}

function viewFile(url, fileExt) {
  spinner.show(); // Mostrar el spinner antes de cargar el archivo

  // Mapeo de tipos de archivo a selectores y eventos
  const fileHandlers = {
    image: {
      selector: "#image-preview",
      modal: "#image-modal",
      event: "load",
    },
    video: {
      selector: "#video-preview",
      modal: "#video-modal",
      event: "canplay",
    },
    pdf: {
      selector: "#file-preview",
      modal: "#file-modal",
      event: null, // No hay evento confiable para iframes
    },
    word: {
      selector: "#file-preview",
      modal: "#file-modal",
      event: null,
    },
    excel: {
      selector: "#file-preview",
      modal: "#file-modal",
      event: null,
    },
  };

  // Determinar el tipo de archivo
  let fileType = null;
  if (fileExt.startsWith("image")) fileType = "image";
  else if (fileExt.startsWith("video")) fileType = "video";
  else if (fileExt.startsWith("application/pdf")) fileType = "pdf";
  else if (
    fileExt.startsWith("application/msword") ||
    fileExt.startsWith(
      "application/vnd.openxmlformats-officedocument.wordprocessingml.document"
    )
  )
    fileType = "word";
  else if (
    fileExt.startsWith("application/vnd.ms-excel") ||
    fileExt.startsWith(
      "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
    )
  )
    fileType = "excel";

  // Manejo del archivo según su tipo
  if (fileType && fileHandlers[fileType]) {
    const handler = fileHandlers[fileType];
    const element = $(handler.selector);

    // Configurar la URL del archivo
    element.attr("src", url);

    // Si hay un evento asociado, manejarlo
    if (handler.event) {
      element.off(handler.event); // Eliminar eventos previos
      element.on(handler.event, function () {
        spinner.hide(); // Ocultar el spinner cuando el archivo esté listo
        $(handler.modal).modal("show"); // Mostrar el modal
      });

      // Manejar errores de carga
      element.on("error", function () {
        spinner.hide();
        Swal.fire("Error!", result.message, "error");
      });
    } else {
      // Para iframes (PDF, Word, Excel), usar un retraso para ocultar el spinner
      setTimeout(() => {
        spinner.hide();
        $(handler.modal).modal("show");
      }, 1000);
    }
  } else {
    spinner.hide();
    Swal.fire("Error!", result.message, "error");
  }
}

async function getClientesHeader() {
  spinner.show();
  url =
    base_url +
    "CargaConsolidada/ContenedorConsolidado/getClientesHeader/" +
    idContenedor;
  const response = await fetch(url);
  const result = await response.json();
  $("#txt-Monto_Total").val(result.monto);
  $("#cotizacion_name").val('#' + result.carga);
  $("#txt-CBM_Total_China_Clientes").val(result.cbm_total_china);
  console.log(result);
  spinner.hide();
}
async function verCotizacionEmbarque(
  idProveedor,
  idCotizacion,
  supplierCode,
  clientName
) {
  //hide table cotizacion embarque
  cotizacionContainer.hide();
  contentHeader.hide();
  cotizacionAlmacenContainer.show();
  currentProveedor = idProveedor;
  currentCotizacion = idCotizacion;
  // Función para inicializar la lista al cargar la página
  spinner.show();
  $("#client-title").text(clientName);
  $("#client-supplier-code").text(supplierCode);
  $("#cotizacion_name").text('#' + currentCargaNumber);
  $("#file-lista-documentacion").empty();
  getFilesAlmacenDocument(idProveedor, idCotizacion).then((files) => {
    files.forEach((file) =>
      addFileToList(file, null, "file-lista-documentacion")
    );
  });
  $("#file-lista-inspection").empty();
  getFilesAlmacenInspection(idProveedor, idCotizacion).then((files) => {
    files.forEach((file) => addFileToList(file, null, "file-lista-inspection"));
  });

  // fileManager = new FileManager({
  //     fileGrid: "#file-grid",
  //     fileInput: "#file-input-modal",
  //     uploadBtn: "#btn-upload",
  //     dragDropContainer: "#drag-drop-container",
  //     searchInput: "#search-input",
  //     pendingFileList: "#pending-file-list",
  //     onFileUpload: (file) => uploadFileDocument(file, fileManager),
  //     onLoadFiles: () => getFilesAlmacenDocument(idProveedor, idCotizacion),
  // });
  // fileManagerInspection = new FileManager({
  //     fileGrid: "#file-grid-inspection",
  //     fileInput: "#file-input-modal-inspection",
  //     uploadBtn: "#btn-upload-inspection",
  //     dragDropContainer: "#drag-drop-container-inspection",
  //     searchInput: "#search-input-inspection",
  //     pendingFileList: "#pending-file-list-inspection",
  //     onFileUpload: (file) => uploadFileAlmacenInspection(file, fileManagerInspection),
  //     onLoadFiles: () => getFilesAlmacenInspection(idProveedor, idCotizacion),
  // });
  //fectch getNotes
  url =
    base_url + "CargaConsolidada/ContenedorConsolidado/getNotes/" + idProveedor;
  const response = await fetch(url);
  const result = await response.json();
  $("#txt-Id_Carga_Consolidada").val(result.nota);
  originalNoteInpectionText = result.nota;
  if (currentPrivilege != "ContenedorAlmacen") {
    $("#btn-upload-document-cotizacion").css("pointer-events", "none");
    $("#btn-upload-inspection-cotizacion").css("pointer-events", "none");
  }

  spinner.hide();
  // Función para agregar un archivo a la lista con vista previa y botones

}
async function deleteFileInspection(id, cardElement) {
  event.preventDefault();
  Swal.fire({
    title: swalConfig.title,
    text: swalConfig.text,
    icon: swalConfig.icon,
    showCancelButton: true,
    confirmButtonText: swalConfig.confirmButtonText,
    cancelButtonText: swalConfig.cancelButtonText,
    iconColor: "#FF0000",
    color: "#FF0000",
  }).then((result) => {
    if (result.isConfirmed) {
      spinner.show();
      url =
        base_url +
        "CargaConsolidada/ContenedorConsolidado/deleteFileInspection/" +
        id;
      $.ajax({
        url: url,
        type: "GET",

        success: function (response) {
          const result = JSON.parse(response);
          if (result.status == "success") {
            cardElement.remove();
            Swal.fire(successConfig.title, result.message, "success");
          } else {
            Swal.fire(errorConfig.title, result.message, "error");
          }
          spinner.hide();
        },
      });
    }
  });
}
async function getFilesAlmacenDocument(idProveedor, idCotizacion) {
  spinner.show();

  const url =
    base_url +
    "CargaConsolidada/ContenedorConsolidado/getFilesAlmacenDocument/" +
    idProveedor;
  return new Promise((resolve, reject) => {
    $.ajax({
      url: url,
      method: "GET",
      processData: false,
      contentType: false,
      success: function (data) {
        const result = JSON.parse(data);
        if (result.status === "success") {
          resolve(result.data);
        } else {
          reject(result.message);
        }
      },
      error: function () {
        reject("Error al subir el archivo");
      },
      complete: function () {
        spinner.hide();
      },
    });
  });
}
async function getFilesAlmacenInspection(idProveedor, idCotizacion) {
  spinner.show();

  const url =
    base_url +
    "CargaConsolidada/ContenedorConsolidado/getFilesAlmacenInspection/" +
    idProveedor;
  return new Promise((resolve, reject) => {
    $.ajax({
      url: url,
      method: "GET",
      processData: false,
      contentType: false,
      success: function (data) {
        const result = JSON.parse(data);
        if (result.status === "success") {
          resolve(result.data);
        } else {
          reject(result.message);
        }
      },
      error: function () {
        reject("Error al subir el archivo");
      },
      complete: function () {
        spinner.hide();
      },
    });
  });
}
async function uploadFileDocument(file, fileManager) {
  spinner.show();
  const formData = new FormData();
  formData.append("file", file);
  formData.append("idProveedor", currentProveedor);
  formData.append("idCotizacion", currentCotizacion);
  const url =
    base_url + "CargaConsolidada/ContenedorConsolidado/uploadFileDocument";
  return new Promise((resolve, reject) => {
    $.ajax({
      url: url,
      method: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (data) {
        const result = JSON.parse(data);
        if (result.status === "success") {
          resolve(result.data);
        } else {
          reject(result.message);
        }
      },
      error: function () {
        reject("Error al subir el archivo");
      },
      complete: function () {
        spinner.hide();
      },
    });
  });
}
async function uploadFileAlmacenInspection(file, fileManager) {
  console.log("upload file inspection");
  spinner.show();
  const formData = new FormData();
  formData.append("file", file);
  formData.append("idProveedor", currentProveedor);
  formData.append("idCotizacion", currentCotizacion);
  const url =
    base_url +
    "CargaConsolidada/ContenedorConsolidado/uploadFileAlmacenInspection";
  return new Promise((resolve, reject) => {
    $.ajax({
      url: url,
      method: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (data) {
        const result = JSON.parse(data);
        if (result.status === "success") {
          resolve(result.data);
        } else {
          reject(result.message);
        }
      },
      error: function () {
        reject("Error al subir el archivo");
      },
      complete: function () {
        spinner.hide();
      },
    });
  });
  spinner.hide();
}
async function updateEstadoCotizacion(id, idCotizacion) {
  //get select value
  const estado = $(`#estado-cotizacion-${id}`).val();

  url =
    base_url + "CargaConsolidada/ContenedorConsolidado/updateEstadoCotizacion";
  $.ajax({
    url: url,
    type: "POST",
    data: {
      id: id,
      estado: estado,
    },
    success: function (response) {
      const result = JSON.parse(response);
      if (result.status == "success") {
        Swal.fire("Correcto!", result.message, "success");
      } else {
        Swal.fire("Error!", result.message, "error");
      }
      reloadTableCotizacion();
    },
  });
}
async function addNote() {
  event.preventDefault();
  const note = $("#txt-Id_Carga_Consolidada").val();
  spinner.show();
  url = base_url + "CargaConsolidada/ContenedorConsolidado/addNote";
  $.ajax({
    url: url,
    type: "POST",
    data: {
      id: currentCarga,
      idProveedor: currentProveedor,
      note: note,
    },
    success: function (response) {
      spinner.hide();
      const result = JSON.parse(response);
      if (result.status == "success") {
        Swal.fire("Correcto!", result.message, "success");
        $("#txt-Nota").val("");
        table_Entidad.ajax.reload();
      } else {
        Swal.fire("Error!", result.message, "error");
      }
    },
  });
}
async function updateTelefonoProveedor(idProveedor) {
  $telefono = $("#telefono-" + idProveedor).val();
  url =
    base_url + "CargaConsolidada/ContenedorConsolidado/updateTelefonoProveedor";
  spinner.show();
  $.ajax({
    url: url,
    type: "POST",
    data: {
      idProveedor: idProveedor,
      telefono: $telefono,
    },
    success: function (response) {
      const result = JSON.parse(response);
      if (result.status == "success") {
        Swal.fire("Correcto!", result.message, "success");
        reloadTableCotizacionEmbarque();
      } else {
        Swal.fire("Error!", result.message, "error");
      }
    },
  });
  spinner.hide();
}
async function updateProveedor($idProveedor) {
  $supplier = $("#proveedor-" + $idProveedor).val();
  url = base_url + "CargaConsolidada/ContenedorConsolidado/updateProveedor";
  spinner.show();
  $.ajax({
    url: url,
    type: "POST",
    data: {
      idProveedor: $idProveedor,
      supplier: $supplier,
    },
    success: function (response) {
      const result = JSON.parse(response);
      if (result.status == "success") {
        Swal.fire(successConfig.title, result.message, "success");
        reloadTableCotizacionEmbarque();
      } else {
        Swal.fire(errorConfig.title, result.message, "error");
      }
    },
  });
}
async function updateQtyChina($idProveedor) {
  $qtyChina = $(`#qty-china-${$idProveedor}`).val();
  url = base_url + "CargaConsolidada/ContenedorConsolidado/updateQtyChina";
  spinner.show();
  $.ajax({
    url: url,
    type: "POST",
    data: {
      idProveedor: $idProveedor,
      qtyChina: $qtyChina,
    },
    success: function (response) {
      const result = JSON.parse(response);
      if (result.status == "success") {
        Swal.fire(successConfig.title, result.message, "success");
        reloadTableCotizacionEmbarque();
      } else {
        Swal.fire(errorConfig.title, result.message, "error");
      }
    },
  });
  spinner.hide();
}
async function updateCBMChina($idProveedor) {
  $cbmChina = $(`#cbm-china-${$idProveedor}`).val();
  url = base_url + "CargaConsolidada/ContenedorConsolidado/updateCBMChina";
  spinner.show();
  $.ajax({
    url: url,
    type: "POST",
    data: {
      idProveedor: $idProveedor,
      cbmChina: $cbmChina,
    },
    success: function (response) {
      const result = JSON.parse(response);
      if (result.status == "success") {
        Swal.fire(successConfig.title, result.message, "success");
        reloadTableCotizacionEmbarque();
      } else {
        Swal.fire("Error!", result.message, "error");
      }
    },
  });
  spinner.hide();
}
async function updateProveedorData(idCotizacion, idProveedor) {
  let telefono = $("#telefono-" + idProveedor).val();
  let supplier = $("#proveedor-" + idProveedor).val();
  let qtyChina = $(`#qty-china-${idProveedor}`).val();
  let cbmChina = $(`#cbm-china-${idProveedor}`).val();
  let arriveDateChina = $(`#arrive-date-china-${idProveedor}`).val();
  let codigoSupplier = $(`#codigo-${idProveedor}`).val();
  let productos = $(`#productos-${idProveedor}`).val();
  //return fields not empty and not nul
  let data = {};
  if (telefono != "") {
    data.supplier_phone = telefono;
  }
  if (supplier != "") {
    data.supplier = supplier;
  }
  if (qtyChina != "") {
    data.qty_box_china = qtyChina;
  }
  if (cbmChina != "") {
    data.cbm_total_china = cbmChina;
  }
  if (arriveDateChina != "") {
    data.arrive_date_china = arriveDateChina;
  }
  if (codigoSupplier != "") {
    data.code_supplier = codigoSupplier;
  }
  if (productos != "") {
    data.products = productos;
  }

  url = base_url + "CargaConsolidada/ContenedorConsolidado/updateProveedorData";
  spinner.show();
  $.ajax({
    url: url,
    type: "POST",
    data: {
      idProveedor: idProveedor,
      idCotizacion: idCotizacion,
      data: data,
    },
    success: function (response) {
      const result = JSON.parse(response);
      if (result.status == "success") {
        Swal.fire(successConfig.title, result.message, "success");
        reloadTableCotizacionEmbarque();
      } else {
        Swal.fire("Error!", result.message, "error");
      }
      spinner.hide();
    },
    error: function () {
      spinner.hide();
    },
  });
}
async function updateArriveDateChina($idProveedor) {
  $arriveDateChina = $(`#arrive-date-china-${$idProveedor}`).val();
  url =
    base_url + "CargaConsolidada/ContenedorConsolidado/updateArriveDateChina";
  spinner.show();
  $.ajax({
    url: url,
    type: "POST",
    data: {
      idProveedor: $idProveedor,
      arriveDateChina: $arriveDateChina,
    },
    success: function (response) {
      const result = JSON.parse(response);
      if (result.status == "success") {
        Swal.fire(successConfig.title, result.message, "success");
        reloadTableCotizacionEmbarque();
      } else {
        Swal.fire("Error!", result.message, "error");
      }
    },
  });
  spinner.hide();
}
async function uploadFacturaGeneral(idCotizacion) {
  //swall with file input
  const { value: file } = await Swal.fire({
    title: "Subir Factura",
    input: "file",
    inputAttributes: {
      //excel
      accept: "*",
      "aria-label": "Sube tu archivo",
    },
    showCancelButton: true,
    confirmButtonText: "Subir",
    cancelButtonText: "Cancelar",
    inputValidator: (value) => {
      if (!value) {
        return "Debes elegir un archivo!";
      }
    },
  });
  if (file) {
    const formData = new FormData();
    formData.append("file", file);
    formData.append("idCotizacion", idCotizacion);
    url =
      base_url + "CargaConsolidada/ContenedorConsolidado/uploadFacturaGeneral";
    $.ajax({
      url: url,
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        const result = JSON.parse(response);
        if (result.status == "success") {
          Swal.fire("Correcto!", result.message, "success");
          tableFacturaGuia.ajax.reload();
        } else {
          Swal.fire("Error!", result.message, "error");
        }
      },
    });
  }
}
async function uploadGuiaRemision(idCotizacion) {
  //swall with file input
  const { value: file } = await Swal.fire({
    title: "Subir Guia de Remisión",
    input: "file",
    inputAttributes: {
      //all files
      accept: "*",
      "aria-label": "Sube tu archivo",
    },
    showCancelButton: true,
    confirmButtonText: "Subir",
    cancelButtonText: "Cancelar",
    inputValidator: (value) => {
      if (!value) {
        return "Debes elegir un archivo!";
      }
    },
  });
  if (file) {
    const formData = new FormData();
    formData.append("file", file);
    formData.append("idCotizacion", idCotizacion);
    url =
      base_url + "CargaConsolidada/ContenedorConsolidado/uploadGuiaRemision";
    $.ajax({
      url: url,
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        const result = JSON.parse(response);
        if (result.status == "success") {
          Swal.fire("Correcto!", result.message, "success");
          tableFacturaGuia.ajax.reload();
        } else {
          Swal.fire("Error!", result.message, "error");
        }
      },
    });
  }
}
async function deleteFacturaGeneralFile(id) {
  Swal.fire({
    title: "¿Estás seguro?",
    text: "¡No podrás revertir esto!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, eliminarlo",
    cancelButtonText: "No, cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      url =
        base_url +
        "CargaConsolidada/ContenedorConsolidado/deleteFacturaGeneralFile/" +
        id;
      $.ajax({
        url: url,
        type: "GET",

        success: function (response) {
          const result = JSON.parse(response);
          if (result.status == "success") {
            tableFacturaGuia.ajax.reload();
            Swal.fire("Eliminado!", result.message, "success");
          } else {
            Swal.fire("Error!", result.message, "error");
          }
          reloadTableFacturaGuia();
        },
      });
    }
  });
}
async function deleteGuiaRemisionFile(id) {
  Swal.fire({
    title: "¿Estás seguro?",
    text: "¡No podrás revertir esto!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, eliminarlo",
    cancelButtonText: "No, cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      url =
        base_url +
        "CargaConsolidada/ContenedorConsolidado/deleteGuiaRemisionFile/" +
        id;
      $.ajax({
        url: url,
        type: "GET",

        success: function (response) {
          const result = JSON.parse(response);
          if (result.status == "success") {
            tableFacturaGuia.ajax.reload();
            Swal.fire("Eliminado!", result.message, "success");
          } else {
            Swal.fire("Error!", result.message, "error");
          }
          reloadTableFacturaGuia();
        },
      });
    }
  });
}
async function updateProductos($idProveedor, idCotizacion) {
  $productos = $(`#productos-${$idProveedor}`).val();

  url = base_url + "CargaConsolidada/ContenedorConsolidado/updateProductos";
  spinner.show();
  $.ajax({
    url: url,
    type: "POST",
    data: {
      idProveedor: $idProveedor,
      productos: $productos,
    },
    success: function (response) {
      const result = JSON.parse(response);
      if (result.status == "success") {
        Swal.fire(successConfig.title, result.message, "success");
        reloadTableCotizacionEmbarque();
      } else {
        Swal.fire("Error!", result.message, "error");
      }
    },
  });
  spinner.hide();
}
async function updateEstadoProveedor($idCotizacion, $idProveedor) {
  $estado = $(`#estado-${$idProveedor}`).val();
  url =
    base_url + "CargaConsolidada/ContenedorConsolidado/updateEstadoProveedor";
  spinner.show();
  $.ajax({
    url: url,
    type: "POST",
    data: {
      idCotizacion: $idCotizacion,
      idProveedor: $idProveedor,
      estado: $estado,
    },
    success: function (response) {
      const result = JSON.parse(response);
      if (result.status == "success") {
        Swal.fire(successConfig.title, result.message, "success");
        reloadTableCotizacionEmbarque();
      } else {
        Swal.fire("Error!", result.message, "error");
      }
    },
  });
  spinner.hide();
}
async function updateEstadoCotizacionProveedor(
  idCotizacion,
  idProveedor,
  previousStatus
) {
  console.log(spinner)
  const estado = $(`#estado-${idCotizacion}-${idProveedor}`).val();
  //set this previous status
  previousStatus = $(`#estado-${idCotizacion}-${idProveedor}`).data("previous");
  //validate if all products are filled
  allProducts = $(`.cotizacion-products-${idCotizacion}`);
  isValid = true;
  allProducts.each(function () {
    if ($(this).val() == "") {
      isValid = false;
    }
  });
  spinner.show()
  url =
    base_url +
    "CargaConsolidada/ContenedorConsolidado/updateEstadoCotizacionProveedor";
  if (estado == "ROTULADO") {
    if (!isValid) {
      Swal.fire("Error!", "Debe ingresar todos los productos", "error");
      //set current select previous status
      $(`#estado-${idCotizacion}-${idProveedor}`).val(previousStatus);
      console.log(previousStatus);
      spinner.hide();
      return;
    }


    url =
      base_url +
      "CargaConsolidada/ContenedorConsolidado/updateEstadoCotizacionProveedor";
    await $.ajax({
      url: url,
      type: "POST",
      data: {
        idCotizacion: idCotizacion,
        idProveedor: idProveedor,
        estado: estado,
      },
      //blob
      xhrFields: {
        responseType: "blob",
      },
      success: function (response) {
        var blob = new Blob([response], {
          //zip
          type: "application/zip",
        });
        var link = document.createElement("a");
        link.href = window.URL.createObjectURL(blob);
        link.download = `Cotizacion-${idCotizacion}.zip`;
        link.click();
        //reload table 
        reloadTableCotizacionEmbarque();
      },
      error: function () {
        //set current select previous status
        $(`#estado-${idCotizacion}-${idProveedor}`).val(previousStatus);
        Swal.fire("Error!", "Algo ha fallado", "error");
        spinner.hide();
      },
    });
  } else {
    url =
      base_url +
      "CargaConsolidada/ContenedorConsolidado/updateEstadoCotizacionProveedor";
    await $.ajax({
      url: url,
      type: "POST",
      data: {
        idCotizacion: idCotizacion,
        idProveedor: idProveedor,
        estado: estado,
      },
      //blob

      success: function (response) {
        //manage blob
        spinner.hide();
        const result = JSON.parse(response);
        if (result.status == "success") {
          Swal.fire("Correcto!", result.message, "success");
          reloadTableCotizacionEmbarque();
        } else {
          Swal.fire("Error!", result.message, "error");
        }
      },
    });
  }
  spinner.hide();
}
async function view(id) {
  url = base_url + "CargaConsolidada/ContenedorConsolidado/show/" + id;
  const response = await fetch(url);
  const result = await response.json();
  $("#txt-Mes").val(result.mes);
  $("#txt-ID_Pais").val(result.ID_Pais);
  $("#txt-No_Carga").val(result.carga);
  $("#txt-Fe_Puerto").val(result.f_puerto);
  $("#txt-Fe_Entrega").val(result.f_entrega);
  $("#txt-Fe_Cierre").val(result.f_cierre);

  $("#txt-Empresa").val(result.empresa);
  $("#modal-crear").modal("show");
  $("#btn-guardar").hide();
  $("#btn-actualizar").show();
  currentCarga = result.id;
}
async function viewSteps(id, carga, disabled = false) {
  sectionsDisabled = disabled;
  currentCargaNumber = carga;
  url = base_url + "CargaConsolidada/ContenedorConsolidado/steps/" + id;
  idContenedor = id;
  spinner.show();
  $(".step-column").remove();
  idPedido = id;
  url = base_url + "CargaConsolidada/ContenedorConsolidado/steps/" + id;
  const steps = $("#steps-container");
  const loading = $("#loading-steps");
  const response = await fetch(url);
  const result = await response.json();
  try {
    if (result.status == "success") {
      spinner.hide();
      const data = result.data;
      currentPrivilege = result.currentPrivilege;
      //here
      if (currentPrivilege == "ContenedorAlmacen") {
        openStepFunction(1, id);
        mainContainer.hide();
        contentHeader.hide();
        return;
      }
      mainContainer.hide();
      contentHeader.hide();
      stepsContainer.show();
      loading.hide();
      steps.empty();
      data.forEach((step, i) => {
        steps.append(stepTemplate(step, i));
        console.log(step);
        if (step.status == "COMPLETED") {
          $(`#step-${i}`)
            .removeClass("step-container")
            .addClass("step-container-completed");
        }
        if (step.status == "PROGRESS") {
          $(`#step-${i}`)
            .removeClass("step-container")
            .addClass("step-container-progress");
        }
      });
      $(".steps-buttons").empty();
      const configButtons = {
        btnCancel: {
          text: "Regresar",
          action: "hideSteps()",
        },
      };
      $(".steps-buttons").append(getActionButtons(configButtons));

      //set data to modal
    } else {
      loading.hide();
      steps.append(`<div class="alert alert-danger">Hubo un error</div>`);
      //show error message
    }
  } catch (e) {
    loading.hide();
    steps.append(`<div class="alert alert-danger">Hubo un error</div>`);

    //show error message
  }
}
async function hideSteps() {
  stepsContainer.hide();
  contentHeader.show();
  mainContainer.show();
  table_Entidad.ajax.reload();
}
async function deleteCarga(id) {
  Swal.fire({
    title: "¿Estás seguro que deseas eliminar esta carga?",
    // text: "¿Estás seguro que deseas eliminar esta carga?",
    icon: "warning",
    iconColor: "#FF0000",
    showCancelButton: true,

    confirmButtonText: "Sí, eliminar",
    confirmButtonColor: "#FF0000",
    cancelButtonColor: "#000000",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    console.log(result)
    if (result.isConfirmed) {
      url = base_url + "CargaConsolidada/ContenedorConsolidado/delete";
      $.ajax({
        url: url,
        type: "POST",
        data: {
          id: id,
        },
        success: function (response) {
          const result = JSON.parse(response);
          if (result.status == "success") {
            table_Entidad.ajax.reload();
            Swal.fire("Eliminado!", result.message, "success");
          } else {
            Swal.fire("Error!", result.message, "error");
          }
        },
      });
    }
  });
}
async function getTipoCliente() {
  url = base_url + "CargaConsolidada/ContenedorConsolidado/getTipoCliente";
  const response = await fetch(url);
  const result = await response.json();
  $("txt-ID_TipoCliente").empty();
  result.forEach((tipo) => {
    $("#txt-ID_Tipo_Cliente").append(
      `<option value="${tipo.id}">${tipo.name}</option>`
    );
  });
}
async function showDocumentacionDocumentacionContainer(id) {
  documentacionDocumentacionContainer.show();
  url =
    base_url +
    "CargaConsolidada/ContenedorConsolidado/getDocumentationFolderFiles/" +
    idContenedor;
  spinner.show();
  const response = await fetch(url);
  const result = await response.json();
  spinner.hide();
  $("#documentacion-documentacion").empty();

  result.forEach((file) => {
    let color = "bg-blue-50";
    if (file.categoria == "DEFAULT") {
      color = "bg-blue-50";
    } else if (file.categoria == "ENVIO") {
      color = "bg-green-50";
    } else if (file.categoria == "COMERCIAL") {
      color = "bg-yellow-50";
    } else if (file.categoria == "LEGAL") {
      color = "bg-red-50";
    } else if (file.categoria == "OTROS") {
      color = "bg-purple-50";
    }

    if (file.file_url) {
      // $("#documentacion-documentacion").append(`
      //           <div class="doc-card opacity-0 ${color} p-4 rounded-lg transition-all duration-300" data-type="${file.categoria
      //   }">
      //   <div class="flex items-center justify-between">
      //     <div class="flex items-center space-x-3">
      //       <i class="bi bi-${file.b_icon} text-blue-500 text-xl"></i>
      //       <div>
      //         <h3 class="font-medium text-gray-800">${file.folder_name}</h3>
      //         <p class="text-sm text-gray-500">Ver documento</p>
      //       </div>
      //     </div>
      //     <a class="download-btn text-blue-500 hover:text-blue-700"
      //     href="${file.file_url}" target="_blank" download>

      //       <i class="${`bi bi-download`}"></i>
      //     </a>
      //   </div>
      // </div>`);
      $("#documentacion-documentacion").append(`
        <div class="col-12 col-sm-12" id="single-${file.id}">
            <div class="form-group">
                <label>${file.folder_name}
                  ${file.id_contenedor ? `<div class="badge badge-danger text-white delete-folder-button" onclick="deleteDocumentacionFolder(${file.id})">X</div>` : ""}
                </label>
                <div class="file-upload-box">
                    ${file.file_url ? `
                            <div class="file-info">
                              <div class="file-iconic">
                                ${getIconByType(file.type)}
                              </div>
                                <span class="file-name">${file.folder_name}</span>
                                
                                <div 
                                class="d-flex flex-row gap-5"
                                >
                                <button class="download-file-button" onclick=window.location.href='${file.file_url}'>
                                <svg width="14" height="13" viewBox="0 0 14 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12.7436 8.61328H1.25641C0.56718 8.61328 0 9.18046 0 9.86969V11.3056C0 11.9948 0.56718 12.562 1.25641 12.562H12.7436C13.4328 12.562 14 11.9948 14 11.3056V9.86969C14 9.18046 13.4328 8.61328 12.7436 8.61328ZM12.9231 11.3056C12.9231 11.4061 12.8441 11.4851 12.7436 11.4851H1.25641C1.1559 11.4851 1.07692 11.4061 1.07692 11.3056V9.86969C1.07692 9.76918 1.1559 9.6902 1.25641 9.6902H12.7436C12.8441 9.6902 12.9231 9.76918 12.9231 9.86969V11.3056Z" fill="#585858"/>
                                <path d="M8.4638 4.46608L7.22893 5.70096L7.22893 0.538904C7.22893 0.244545 6.98483 0.000441819 6.69047 0.000441793C6.39611 0.000441767 6.15201 0.244545 6.15201 0.538903L6.15201 5.70096L4.91714 4.46608C4.80944 4.35839 4.67303 4.30813 4.53662 4.30813C4.40021 4.30813 4.2638 4.35839 4.15611 4.46608C3.9479 4.67429 3.9479 5.0189 4.15611 5.22711L6.30996 7.38096C6.51816 7.58916 6.86278 7.58916 7.07098 7.38096L9.22483 5.22711C9.43303 5.0189 9.43303 4.67429 9.22483 4.46608C9.01662 4.25788 8.67201 4.25788 8.4638 4.46608Z" fill="#585858"/>
                                </svg>

                                </button>
                                <div  
                                style="cursor: pointer;${sectionsDisabled ? "display: none;" : ""}"
                                onclick="deleteDocumentacionFileDocumentacion(${file.id_file})">
                                  <svg width="12" height="14" viewBox="0 0 12 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                  <path d="M1 3.32031H2.16H11.44" stroke="#585858" stroke-width="1.09" stroke-linecap="round" stroke-linejoin="round"/>
                                  <path d="M10.2799 3.32V11.44C10.2799 11.7477 10.1577 12.0427 9.94016 12.2602C9.72261 12.4778 9.42756 12.6 9.11991 12.6H3.31991C3.01226 12.6 2.71721 12.4778 2.49967 12.2602C2.28213 12.0427 2.15991 11.7477 2.15991 11.44V3.32M3.89991 3.32V2.16C3.89991 1.85235 4.02213 1.5573 4.23967 1.33976C4.45721 1.12221 4.75226 1 5.05991 1H7.37991C7.68756 1 7.98261 1.12221 8.20016 1.33976C8.4177 1.5573 8.53991 1.85235 8.53991 2.16V3.32" stroke="#585858" stroke-width="1.09" stroke-linecap="round" stroke-linejoin="round"/>
                                  <path d="M5.06006 6.2207V9.7007" stroke="#585858" stroke-width="1.09" stroke-linecap="round" stroke-linejoin="round"/>
                                  <path d="M7.37988 6.2207V9.7007" stroke="#585858" stroke-width="1.09" stroke-linecap="round" stroke-linejoin="round"/>
                                  </svg>

                                </div>
                                </div>
                            </div>
                    `
          : `
                        <input type="file" id="file-input-${file.id}" class="file-input" accept=".pdf, .docx, .xlsx, .xls, .doc, .xlsm, .csv, .xlsb, .xltx, .xlt"/>
                        <label for="file-inputo" class="file-label d-flex">
                            <i class="fas fa-upload"></i>
                            <div class="file-group-text">
                                <span class="file-text">Selecciona o arrastra tu archivo aquí</span>
                                <span class="file-format">Formatos: .pdf, .docx, .xlsx, .xls, .doc, .xlsm, .csv, .xlsb, .xltx, .xlt</span>
                            </div>
                            <button class="upload-button upload-button-documentacion-documentacion-${file.id}" type="button">Subir archivo</button>
                        </label>
                     
                    `
        }
            </div>    
        </div>
    `);
    } else {
      //   $("#documentacion-documentacion").append(`
      //             <div class="doc-card opacity-0 ${color} p-4 rounded-lg transition-all duration-300" data-type="${file.categoria
      //     }">
      //     <div class="flex items-center justify-between">
      //         <div class="flex items-center space-x-3">
      //             <i class="bi bi-file
      //             text-blue-500 text-xl"></i>
      //             <div>
      //                 <h3 class="font-medium text-gray-800">${file.folder_name
      //     }</h3>
      //                 <p class="text-sm text-red-500">Documento no subido</p>
      //             </div>
      //         </div>
      //           <span class="download-btn text-blue-500 hover:text-blue-700"
      //             id="file-input-doc-${file.id}-label">
      //             <i class="${file.b_icon ?? `bi bi-upload`}"></i>
      //              </span>
      //                       <input type="file" id="file-input-doc-${file.id
      //     }" class="hidden" />
      //     </div>
      // </div>`);
      $("#documentacion-documentacion").append(`
      <div class="col-12 col-sm-12" id="single-${file.id}">
          <div class="form-group">
              <label>${file.folder_name}
                ${file.id_contenedor ? `<div class="badge badge-danger text-white delete-folder-button" onclick="deleteDocumentacionFolder(${file.id})">X</div>` : ""}
              </label>
              <div class="file-upload-box">
                  ${file.file_url ? `
                          <div class="file-info">
                            <div class="file-iconic">
                              ${getIconByType(file.type)}
                            </div>
                              <span class="file-name">${file.folder_name}</span>
                              
                              <button class="download-file-button" onclick=window.location.href='${file.file_url}'>
                              <i class="fas fa-download"></i>
                              </button>
                              <div  onclick="deleteDocumentacionFile(${file.id_file})"
                              style="${sectionsDisabled ? "display:none" : ""}">
                              <i class="fas fa-trash"></i>
                              </div>
                          </div>
                  `
          : `
                      <input type="file" id="file-input-${file.id}" class="file-input" accept=".pdf, .docx, .xlsx, .xls, .doc, .xlsm, .csv, .xlsb, .xltx, .xlt"/>
                      <labelf for="file-inputo" class="file-label d-flex">
                          <i class="fas fa-upload"></i>
                          <div class="file-group-text">
                              <span class="file-text">Selecciona o arrastra tu archivo aquí</span>
                          </div>
                          <button class="upload-button upload-button-documentacion-documentacion-${file.id}" type="button">Subir archivo</button>
                      </label>
                   
                  `
        }
          </div>    
      </div>
  `);
      // Add event listener to file input
      $(`.upload-button-documentacion-documentacion-${file.id}`).off("click");
      $(`.upload-button-documentacion-documentacion-${file.id}`).on("click", function () {
        //show swall for upload
        Swal.fire({
          title: "Subir archivo",
          //set modal width to 50%
          width: "500px",
          //html body modal
          html: `<div class="bg-gray-100 flex items-center justify-center">
      <div class=" w-100  bg-white py-8 px-4 rounded-lg shadow-md text-center">
        
        <div class="mb-4">
          <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 cursor-pointer hover:border-blue-500 transition-colors" id="dropZone">
            <div class="text-gray-500">
              <svg class="w-10 h-10 mx-auto mb-4" viewBox="0 0 25 23" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M22.7564 15.6641H2.24359C1.01282 15.6641 0 16.6769 0 17.9077V20.4718C0 21.7025 1.01282 22.7153 2.24359 22.7153H22.7564C23.9872 22.7153 25 21.7025 25 20.4718V17.9077C25 16.6769 23.9872 15.6641 22.7564 15.6641ZM23.0769 20.4718C23.0769 20.6512 22.9359 20.7923 22.7564 20.7923H2.24359C2.0641 20.7923 1.92308 20.6512 1.92308 20.4718V17.9077C1.92308 17.7282 2.0641 17.5871 2.24359 17.5871H22.7564C22.9359 17.5871 23.0769 17.7282 23.0769 17.9077V20.4718Z" fill="#585858"/>
              <path d="M8.78064 5.76718L10.9858 3.56205V12.78C10.9858 13.3056 11.4217 13.7415 11.9473 13.7415C12.473 13.7415 12.9088 13.3056 12.9088 12.78V3.56205L15.114 5.76718C15.3063 5.95949 15.5499 6.04923 15.7935 6.04923C16.0371 6.04923 16.2806 5.95949 16.4729 5.76718C16.8447 5.39538 16.8447 4.78 16.4729 4.4082L12.6268 0.562049C12.255 0.190254 11.6396 0.190254 11.2678 0.562049L7.42167 4.4082C7.04987 4.78 7.04987 5.39538 7.42167 5.76718C7.79346 6.13897 8.40885 6.13897 8.78064 5.76718Z" fill="#585858"/>
              </svg>

              <p class="text-md">Selecciona tu archivo aquí</p>
              <p class="text-sm text-gray-400">Formatos: xlsx</p>
            </div>
          </div>
          <input type="file" id="fileInput" class="hidden" accept=".xlsx">
        </div>
        <div id="fileList" class="space-y-2"></div>
        <div class="flex justify-between mt-6">
          <button id="cancelBtn" class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
            Cancelar
          </button>
          <button id="saveBtn" class="px-4 py-2 bg-orange-500 text-white rounded hover:bg-orange-600 transition-colors">
            Guardar
          </button>
        </div>
      </div>
    </div>`,
          showCancelButton: false,
          showConfirmButton: false,
          //on open modal add function to file input
          didOpen: () => {
            const $dropZone = $('#dropZone');
            const $fileInput = $('#fileInput');
            const $fileList = $('#fileList');
            const $cancelBtn = $('#cancelBtn');
            const $saveBtn = $('#saveBtn');

            // Handle drag and drop events
            $dropZone.on('dragover', function (e) {
              e.preventDefault();
              $(this).addClass('border-blue-500');
            });

            $dropZone.on('dragleave', function (e) {
              e.preventDefault();
              $(this).removeClass('border-blue-500');
            });

            $dropZone.on('drop', function (e) {
              e.preventDefault();
              $(this).removeClass('border-blue-500');
              const files = e.originalEvent.dataTransfer.files;
              handleFiles(files);
            });

            // Handle click to upload
            $dropZone.on('click', function () {
              $fileInput.click();
            });

            $fileInput.on('change', function (e) {
              handleFiles(this.files);
            });

            function handleFiles(files) {
              $fileList.empty();
              Array.from(files).forEach(file => {
                if (file.name.endsWith('.xlsx')) {
                  const fileSize = (file.size / 1024).toFixed(0) + ' KB';
                  const $fileItem = $(`
                    <div class="flex items-center justify-between bg-gray-50 p-3 rounded">
                      <div class="flex items-center">
                        <svg class="w-6 h-6 text-green-600 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <div>
                          <p class="text-sm font-medium">${file.name}</p>
                          <p class="text-xs text-gray-500">${fileSize}</p>
                        </div>
                      </div>
                      <button class="delete-file text-gray-400 hover:text-red-500">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                      </button>
                    </div>
                  `);
                  $fileList.append($fileItem);
                }
              });
            }

            // Handle delete file
            $(document).on('click', '.delete-file', function () {
              $(this).closest('div').remove();
              $fileInput.val('');
            });

            // Handle cancel button
            $cancelBtn.on('click', function () {
              $fileList.empty();
              $fileInput.val('');
              // Close the modal
              Swal.close();
            });

            // Handle save button
            $saveBtn.on('click', function () {
              //validate if file is selected
              const files = $fileInput[0].files;
              if (files.length === 0) {
                Swal.fire({
                  icon: "error",
                  title: "Error",
                  text: "Debes seleccionar un archivo",
                });
                return;
              }
              const fileI = files[0];
              const formData = new FormData();
              formData.append("file", fileI);
              formData.append("idFolder", file.id);
              formData.append("idContenedor", idContenedor);
              url =
                base_url +
                "CargaConsolidada/ContenedorConsolidado/uploadFileDocumentation";
              $.ajax({
                url: url,
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                  const result = JSON.parse(response);
                  if (result.status == "success") {
                    Swal.fire("Correcto!", result.message, "success");
                    showDocumentacionDocumentacionContainer(idContenedor);
                  } else {
                    Swal.fire("Error!", result.message, "error");
                  }
                },
              });
            });
          }
        }).then((result) => {
          if (result.isConfirmed) {

          }
        });
      });
    }
    if (sectionsDisabled) {
      $(".upload-button").prop("disabled", true);
      $("#btn-crear-documentacion-documentacion").hide();
    }
  });
  $(".input-group").on("focusin", "input", function () {
    $(this).parent().addClass("ring-2 ring-blue-500");
    $(this).prev("label").addClass("text-blue-600");
  });

  $(".input-group").on("focusout", "input", function () {
    $(this).parent().removeClass("ring-2 ring-blue-500");
    if (!$(this).val()) {
      $(this).prev("label").removeClass("text-blue-600");
    }
  });

  // Document card hover effects
  $(".doc-card").hover(
    function () {
      $(this).addClass("transform -translate-y-1");
    },
    function () {
      $(this).removeClass("transform -translate-y-1");
    }
  );

  // Document filter functionality
  $(".doc-filter").click(function () {
    const filter = $(this).data("filter");

    // Update active filter button
    $(".doc-filter").removeClass("active bg-white shadow-md");
    $(this).addClass("active bg-white shadow-md");

    if (filter === "todos") {
      $(".doc-card").fadeIn();
    } else {
      $(".doc-card").hide();
      $(`.doc-card[data-type="${filter}"]`).fadeIn();
    }
  });

  // Download button hover effect
  $(".download-btn").hover(
    function () {
      $(this).find(".bi-download").addClass("transform -translate-y-1");
    },
    function () {
      $(this).find(".bi-download").removeClass("transform -translate-y-1");
    }
  );

  // Initial animations
  $(".input-group, .doc-card").each(function (index) {
    $(this)
      .delay(index * 100)
      .animate({ opacity: 1 }, 500);
  });
}
$("#file-input-inspeccion").on("change", function (e) {
  shouldSaveInspection = true;
});
$("#txt-Id_Carga_Consolidada").on("change", function (e) {
  if ($(this).val() != originalNoteInpectionText) {
    shouldSaveInspection = true;
  }
});
async function deleteCotizacionFile(id) {
  Swal.fire({
    title: "¿Estás seguro?",
    text: "¡No podrás revertir esto!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, eliminarlo",
    cancelButtonText: "No, cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      url =
        base_url +
        "CargaConsolidada/ContenedorConsolidado/deleteCotizacionFile/" +
        id;
      $.ajax({
        url: url,
        type: "GET",

        success: function (response) {
          const result = JSON.parse(response);
          if (result.status == "success") {
            tableCotizacion.ajax.reload();
            Swal.fire("Eliminado!", result.message, "success");
          } else {
            Swal.fire("Error!", result.message, "error");
          }
          reloadTableCotizacion();
        },
      });
    }
  });
}
async function deleteCotizacion(id) {
  Swal.fire({
    title: "¿Estás seguro?",
    text: "¡No podrás revertir esto!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, eliminarlo",
    cancelButtonText: "No, cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      url =
        base_url +
        "CargaConsolidada/ContenedorConsolidado/deleteCotizacion/" +
        id;
      $.ajax({
        url: url,
        type: "GET",
        success: function (response) {
          const result = JSON.parse(response);
          if (result.status == "success") {
            Swal.fire("Eliminado!", result.message, "success");
          } else {
            Swal.fire("Error!", result.message, "error");
          }
          reloadTableCotizacion();
        },
      });
    }
  });
}

async function viewFormularioAduana() {
  url =
    base_url +
    "CargaConsolidada/ContenedorConsolidado/viewFormularioAduana/" +
    idContenedor;
  spinner.show();
  let response = await fetch(url);
  const result = await response.json();
  $('#file-lista-aduana').empty();
  $('#file-input-aduana').val('');
  files = JSON.parse(result[0].files);
  files.forEach((file) => {
    addFileToList(file, null, 'file-lista-aduana', true);
  });
  spinner.hide();
  documentacionAduanaContainer.show();
  $(".tab-btn").click(function () {
    const tab = $(this).data("tab");
    $(".tab-btn").removeClass("active bg-gray-100");
    $(this).addClass("active bg-gray-100");
    $(".tab-content").removeClass("active").addClass("hidden");
    $(`#${tab}`).removeClass("hidden").addClass("active");
  });
  //fetch to getNavieras
  url = base_url + "AgenteCompra/PedidosPagados/getNavieras";
  response = await fetch(url);
  const data = await response.json();
  let navieras = data.data;

  //populate navieras select
  const navieraSelect = $("#select-naviera");
  navieraSelect.empty();
  navieraSelect.append(
    `<option value="" selected disabled>Selecciona una naviera</option>`
  );
  for (const naviera of navieras) {
    navieraSelect.append(
      `<option value="${naviera.name}">${naviera.name}</option>`
    );
  }
  //disavle .input-aduana}
  if (sectionsDisabled) {
    $(".input-aduana").prop("disabled", true);
    $(".btn-guardar-aduana").hide();
    $(".upload-button-aduana").hide();
  }
  // Control channel color indicator
  function updateChannelIndicator() {
    const channel = $("#controlChannel").val();
    const indicator = $("#channelIndicator");

    switch (channel) {
      case "Verde":
        $("#controlChannel").css("background-color", "#22c55e");
        break;
      case "Naranja":
        $("#controlChannel").css("background-color", "#f97316");
        break;
      case "Rojo":
        $("#controlChannel").css("background-color", "#ef4444");
        break;
      default:

        break;
    }
  }

  $("#controlChannel").change(updateChannelIndicator);

  // Form field focus effects
  $(".form-group input, .form-group select, .form-group textarea")
    .on("focus", function () {
      $(this).parent().find("label").addClass("text-blue-600");
    })
    .on("blur", function () {
      if (!$(this).val()) {
        $(this).parent().find("label").removeClass("text-blue-600");
      }
    });

  // Form validation function
  function validateForm($form) {
    const errors = [];

    // Validar campos del tab General
    if (!$form.find('[name="naviera"]').val()) {
      errors.push({ field: "naviera", tab: "general" });
    }
    if (!$form.find('[name="tipo_contenedor"]').val()) {
      errors.push({ field: "tipo_contenedor", tab: "general" });
    }
    if (!$form.find('[name="canal_control"]').val()) {
      errors.push({ field: "canal_control", tab: "general" });
    }
    if (!$form.find('[name="numero_dua"]').val()) {
      errors.push({ field: "numero_dua", tab: "general" });
    }

    // Validar campos del tab Dates
    if (!$form.find('[name="fecha_zarpe"]').val()) {
      errors.push({ field: "fecha_zarpe", tab: "dates" });
    }
    if (!$form.find('[name="fecha_arribo"]').val()) {
      errors.push({ field: "fecha_arribo", tab: "dates" });
    }
    if (!$form.find('[name="fecha_declaracion"]').val()) {
      errors.push({ field: "fecha_declaracion", tab: "dates" });
    }
    if (!$form.find('[name="fecha_levante"]').val()) {
      errors.push({ field: "fecha_levante", tab: "dates" });
    }

    // Validar campos del tab Values
    if (!$form.find('[name="valor_fob"]').val()) {
      errors.push({ field: "valor_fob", tab: "values" });
    }
    if (!$form.find('[name="valor_flete"]').val()) {
      errors.push({ field: "valor_flete", tab: "values" });
    }
    if (!$form.find('[name="costo_destino"]').val()) {
      errors.push({ field: "costo_destino", tab: "values" });
    }
    if (!$form.find('[name="ajuste_valor"]').val()) {
      errors.push({ field: "ajuste_valor", tab: "values" });
    }
    if (!$form.find('[name="multa"]').val()) {
      errors.push({ field: "multa", tab: "values" });
    }

    return errors;
  }

  // Get tab name for error messages
  function getTabName(tabId) {
    const tabNames = {
      general: "Información General",
      dates: "Fechas y Plazos",
      values: "Valores y Costos",
    };

    return tabNames[tabId] || tabId;
  }
  const form = $("#customsForm")[0];
  // Iterar sobre las claves del objeto
  Object.keys(result[0]).forEach((key) => {
    // Buscar el elemento del formulario que coincida con la clave
    const input = form.elements[key];
    console.log(key, input);

    // Si el elemento existe, asignar el valor correspondiente
    if (input) {
      // Si es un select, buscar la opción que coincida con el valor
      if (input.tagName === "SELECT") {
        const option = Array.from(input.options).find(
          (opt) => opt.value === result[0][key]
        );
        if (option) {
          option.selected = true;
        }
      } else {
        // Para inputs y textareas, asignar el valor directamente
        input.value = result[0][key];
      }
    }
  });
  // Form submission with validation
  $("#customsForm").on("submit", function (e) {
    e.preventDefault();

    // Validar el formulario
    const $form = $(this);
    const errors = validateForm($form);

    if (errors.length > 0) {
      // Ir al tab con el primer error
      const firstErrorTab = errors[0].tab;

      // Cambiar al tab con el error
      $(".tab-btn").removeClass("active bg-gray-100");
      $(`.tab-btn[data-tab="${firstErrorTab}"]`).addClass("active bg-gray-100");
      $(".tab-content").removeClass("active").addClass("hidden");
      $(`#${firstErrorTab}`).removeClass("hidden").addClass("active");

      // Mostrar mensaje de error
      Swal.fire(
        "Error!",
        `Por favor complete todos los campos obligatorios en la sección ${getTabName(
          firstErrorTab
        )}`,
        "error"
      );

      // Enfocar el primer campo con error
      $form.find(`[name="${errors[0].field}"]`).focus();

      return false;
    }

    // Si no hay errores, continuar con el envío del formulario
    const formData = new FormData(this);
    //add files in file-input-aduana input
    const fileInput = $("#file-input-aduana")[0];
    const files = fileInput.files;
    for (let i = 0; i < files.length; i++) {
      formData.append("files[]", files[i]);
    }
    formData.append("idContainer", idContenedor);
    url =
      base_url +
      "CargaConsolidada/ContenedorConsolidado/updateFormularioAduana";
    spinner.show();

    $.ajax({
      url: url,
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        const result = JSON.parse(response);
        if (result.status == "success") {
          Swal.fire("Correcto!", result.message, "success");
          viewFormularioAduana();
        } else {
          Swal.fire("Error!", result.message, "error");
        }
        spinner.hide();
      },
      error: function () {
        Swal.fire(
          "Error!",
          "Ha ocurrido un error en la comunicación con el servidor",
          "error"
        );
        spinner.hide();
      },
    });
  });
  updateChannelIndicator(); // Initial state
}
async function deleteCliente(idCotizacion) {
  Swal.fire({
    title: "¿Estás seguro?",
    text: "¡No podrás revertir esto!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, eliminarlo",
    cancelButtonText: "No, cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      url =
        base_url +
        "CargaConsolidada/ContenedorConsolidado/deleteCliente/" +
        idCotizacion;
      $.ajax({
        url: url,
        type: "GET",
        success: function (response) {
          const result = JSON.parse(response);
          if (result.status == "success") {
            Swal.fire("Eliminado!", result.message, "success");
          } else {
            Swal.fire("Error!", result.message, "error");
          }
          reloadTableClientesGeneral();
        },
      });
    }
  });
}

function FormatText(string) {
  if (!string) return string;
  return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
}

const stepTemplate = (step, i) => {
  let stepname = FormatText(step.name);
  const stepHTML = `
        <div class="step-container" onclick="openStepFunction(${i + 1},${step.id
    })"  id="step-${i}">
        <div class="step-icon">
        <img src="${step.iconURL}" style="widht:100%;height:100%" /></div><br>
        <span class="step">${stepname}</span>
        
        </div>
    `;
  return stepHTML;
};
const getActionButtons = (data) => {
  try {
    let buttons = "";

    if (data.hasOwnProperty("btnSave") && data.hasOwnProperty("btnCancel")) {
      buttons = `
        <div class="row buttons mt-2" style="row-gap:1em">
          <div class="col-12 col-md-6 d-flex">
            <div class="btn mx-auto btn-primary button-save" onclick='${data.btnSave.action}'>${data.btnSave.text}</div>
          </div>
          <div class="col-12 col-md-6 d-flex">
            <div class="btn mx-auto btn-outline-secondary button-cancel" onclick='${data.btnCancel.action}'>${data.btnCancel.text}</div>
          </div>
        </div>`;
    } else if (data.hasOwnProperty("btnSave")) {
      buttons = `
        <div class="row buttons w-100 mt-2">
          <div class="col-12 col-md-12 d-flex align-items-center justiy-content-center">
            <div class="btn btn-primary mx-auto button-save" onclick='${data.btnSave.action}'>${data.btnSave.text}</div>
          </div>
        </div>`;
    } else if (data.hasOwnProperty("btnCancel")) {
      buttons = `
        <div class="row buttons w-100 mt-2">
          <div class="col-12 col-md-12 d-flex align-items-center justiy-content-center">
            <div class="btn btn-outline-secondary mx-auto w-50 button-cancel" onclick='${data.btnCancel.action}'>${data.btnCancel.text}</div>
          </div>
        </div>`;
    }
    return buttons;
  } catch (e) {
    console.error(e);
    return "";
  }
};
const reloadTableCotizacion = async () => {
  tableCotizacion.ajax.reload();
  await getTableCotizacionEmbarqueHeaders();
};
const updateEstadoCliente = (id) => {
  const estado = $(`#estado-cliente-${id}`).val();
  url = base_url + "CargaConsolidada/ContenedorConsolidado/updateEstadoCliente";
  $.ajax({
    url: url,
    type: "POST",
    data: {
      id: id,
      estado: estado,
    },
    success: function (response) {
      const result = JSON.parse(response);
      if (result.status == "success") {
        Swal.fire("Correcto!", result.message, "success");
      } else {
        Swal.fire("Error!", result.message, "error");
      }
      reloadTableClientesGeneral();
    },
  });
};
const openStepFunction = async (step, id) => {
  stepIndex = step;
  stepId = id;
  spinner.show();
  stepsContainer.hide();
  url = base_url + "CargaConsolidada/ContenedorConsolidado/step";
  if (stepIndex == 1 && currentPrivilege != "Documentacion") {
    cotizacionContainer.show();
    if (
      currentPrivilege == "ContenedorAlmacen" ||
      currentPrivilege == "Documentacion"
    ) {
      $("#table-cotizacion-prospectos").attr("style", "display:none");
      if ($.fn.DataTable.isDataTable("#table-cotizacion-embarque")) {
        reloadTableCotizacionEmbarque();
      } else {
        url = base_url + "CargaConsolidada/ContenedorConsolidado/step";
        tableCotizacionEmbarque.show();
        tableCotizacionEmbarque = $("#table-cotizacion-embarque").DataTable({
          dom:
            "<'row'<'col-sm-12 col-md-7'B><'col-sm-12 col-md-4'f><'col-sm-12 col-md-1'>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-5'i><'col-sm-12 col-md-5'p>>",
          buttons: [],
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
          },
          columnDefs: [
            {
              targets: "no-hidden",
              visible: false,
            },
            {
              className: "text-center",
              targets: "no-sort",
              orderable: false,
            },
            {
              targets: "",
              orderable: false,
            },
            {
              targets: "sorting_asc",
              orderable: false,
            },
          ],
          pageLength: 100, // Mostrar 100 elementos por página
          lengthMenu: [
            [100, 1000, -1],
            [100, 1000, "Todos"],
          ],
          order: [[1, "asc"]],
          ajax: {
            url: url,
            type: "POST",
            dataType: "JSON",
            data: function (data) {
              data.stepIndex = stepIndex;
              data.idContenedor = idContenedor;
              data.tipoTabla = "embarque";
              data.estado = $("#txt-ID_Estado").val();
              validateListEmbarque(idContenedor);
              $(".input-date").datepicker({
                autoclose: true,
                startDate: new Date(fYear, fToday.getMonth(), fDay),
                todayHighlight: true,
                format: "dd/mm/yyyy",
                dateFormat: "dd/mm/yyyy",
              });
              getTableCotizacionEmbarqueHeaders();
            },
          },
          initComplete: function (settings, json) {
            $(".input-date").datepicker({
              autoclose: true,
              startDate: new Date(fYear, fToday.getMonth(), fDay),
              todayHighlight: true,
              format: "dd/mm/yyyy",
              dateFormat: "dd/mm/yyyy",
            });
          },
          complete: function () {
            $(".input-date").datepicker({
              autoclose: true,
              startDate: new Date(fYear, fToday.getMonth(), fDay),
              todayHighlight: true,
              format: "dd/mm/yyyy",
              dateFormat: "dd/mm/yyyy",
            });
          },
          drawCallback: function (settings) {
            $(".input-date").datepicker({
              autoclose: true,
              startDate: new Date(fYear, fToday.getMonth(), fDay),
              todayHighlight: true,
              format: "dd/mm/yyyy",
              dateFormat: "dd/mm/yyyy",
            });
          },
        });

        // FUNCION PARA CONFIGURAR EL BUSCADOR
        configurarBuscador(
          "table-cotizacion-embarque",
          "search-table",
          "table-cotizacion-embarque_info"
        );
        //Funcion para exportar a excel
        configurarExportarExcel(
          "export-excel-main",
          url,
          "Cotizacion-" + idContenedor
        );
      }
      spinner.hide();
    } else {
      if (currentTableCotizacion != "prospectos") {
        reloadTableCotizacionEmbarque();
      }
      if (
        $.fn.DataTable.isDataTable("#table-cotizacion-prospectos") &&
        currentTableCotizacion != "embarque"
      ) {
        reloadTableCotizacion();
      } else if (currentTableCotizacion == "prospectos") {
        url = base_url + "CargaConsolidada/ContenedorConsolidado/step";
        tableCotizacion = $("#table-cotizacion-prospectos").DataTable({
          dom:
            "<'row'<'col-sm-12 col-md-4'B><'col-sm-12 col-md-7'f><'col-sm-12 col-md-1'>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-12 col-md-2'l><'col-sm-12 col-md-5'i><'col-sm-12 col-md-5'p>>",
          buttons: [
            {
              extend: "excel",
              text: '<i class="fa fa-file-excel color_icon_excel"></i> Excel',
              titleAttr: "Excel",
              exportOptions: {
                columns: ":visible",
              },
              attr: {
                id: "export-excel-main",
                class: "hidden",
              },
            },

            {
              extend: "pdf",
              text: '<i class="fa fa-file-pdf color_icon_pdf"></i> PDF',
              titleAttr: "PDF",
              exportOptions: {
                columns: ":visible",
              },
              attr: {
                id: "export-pdf-main",
                class: "hidden",
              },
            },
            {
              text: "Prospectos",
              action: function () {
                if ($.fn.DataTable.isDataTable("#table-cotizacion-embarque")) {
                  $("#table-cotizacion-embarque").attr("style", "display:none");
                  $("#table-cotizacion-embarque_wrapper").hide();
                }
                if (
                  $.fn.DataTable.isDataTable("#table-cotizacion-prospectos")
                ) {
                  //display block
                  $("#table-cotizacion-prospectos").attr("style", "");
                  $("#table-cotizacion-prospectos_wrapper").show();
                  reloadTableCotizacion();
                } else {
                  $("#table-cotizacion-prospectos").attr("style", "");
                  $("#table-cotizacion-prospectos_wrapper").show();
                  reloadTableCotizacion();
                }

                currentTableCotizacion = "prospectos";
              },
              className: "btn btn-light",
            },
            {
              text: "Por Embarcar",
              action: async function () {
                if (
                  $.fn.DataTable.isDataTable("#table-cotizacion-prospectos")
                ) {
                  $("#table-cotizacion-prospectos").attr(
                    "style",
                    "display:none"
                  );
                  $("#table-cotizacion-prospectos_wrapper").hide();
                }
                if ($.fn.DataTable.isDataTable("#table-cotizacion-embarque")) {
                  $("#table-cotizacion-embarque").attr("style", "");
                  $("#table-cotizacion-embarque_wrapper").show();
                  reloadTableCotizacionEmbarque();
                } else {
                  url =
                    base_url + "CargaConsolidada/ContenedorConsolidado/step";
                  tableCotizacionEmbarque.show();
                  tableCotizacionEmbarque = $(
                    "#table-cotizacion-embarque"
                  ).DataTable({
                    dom:
                      "<'row'<'col-sm-12 col-md-7'B><'col-sm-12 col-md-4'f><'col-sm-12 col-md-1'>>" +
                      "<'row'<'col-sm-12'tr>>" +
                      "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-5'i><'col-sm-12 col-md-5'p>>",
                    buttons: [
                      {
                        text: "Prospectos",
                        action: function () {
                          if (
                            $.fn.DataTable.isDataTable(
                              "#table-cotizacion-embarque"
                            )
                          ) {
                            $("#table-cotizacion-embarque").attr(
                              "style",
                              "display:none"
                            );
                            $("#table-cotizacion-embarque_wrapper").hide();
                          }
                          if (
                            $.fn.DataTable.isDataTable(
                              "#table-cotizacion-prospectos"
                            )
                          ) {
                            $("#table-cotizacion-prospectos_wrapper").show();

                            $("#table-cotizacion-prospectos").attr("style", "");
                            reloadTableCotizacion();
                          } else {
                            $("#table-cotizacion-prospectos").attr("style", "");
                            reloadTableCotizacion();
                          }
                          currentTableCotizacion = "prospectos";
                        },
                      },
                      {
                        text: "Por Embarcar",
                        className: "btn btn-light",
                        action: function () {
                          if (
                            $.fn.DataTable.isDataTable(
                              "#table-cotizacion-prospectos"
                            )
                          ) {
                            $("#table-cotizacion-prospectos").attr(
                              "style",
                              "display:none"
                            );
                            $("#table-cotizacion-prospectos_wrapper").hide();
                          }
                          if (
                            $.fn.DataTable.isDataTable(
                              "#table-cotizacion-embarque"
                            )
                          ) {
                            $("#table-cotizacion-embarque").attr("style", "");
                          } else {
                            $("#table-cotizacion-embarque").attr("style", "");
                          }
                          $(".input-date").datepicker({
                            autoclose: true,
                            startDate: new Date(fYear, fToday.getMonth(), fDay),
                            todayHighlight: true,
                            format: "dd/mm/yyyy",
                            dateFormat: "dd/mm/yyyy",
                          });
                          currentTableCotizacion = "embarque";
                        },
                      },
                    ],
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
                      sInfo:
                        "Mostrando (_START_ - _END_) total de registros _TOTAL_",
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
                    },
                    //hide last two columns
                    columnDefs: [
                      {
                        targets: "no-hidden",
                        visible: false,
                      },
                      {
                        className: "text-center",
                        targets: "no-sort",
                        orderable: false,
                      },
                      {
                        targets: "",
                        orderable: false,
                      },
                    ],
                    pageLength: 100, // Mostrar 100 elementos por página
                    lengthMenu: [
                      [100, 1000, -1],
                      [100, 1000, "Todos"],
                    ],
                    ajax: {
                      url: url,
                      type: "POST",
                      dataType: "JSON",
                      data: function (data) {
                        data.stepIndex = stepIndex;
                        data.idContenedor = idContenedor;
                        data.tipoTabla = "embarque";
                        data.estado = $("#txt-ID_Estado").val();
                        validateListEmbarque(idContenedor);
                        $(".input-date").datepicker({
                          autoclose: true,
                          startDate: new Date(fYear, fToday.getMonth(), fDay),
                          todayHighlight: true,
                          format: "dd/mm/yyyy",
                          dateFormat: "dd/mm/yyyy",
                        });
                      },
                    },
                    initComplete: function (settings, json) {
                      $(".input-date").datepicker({
                        autoclose: true,
                        startDate: new Date(fYear, fToday.getMonth(), fDay),
                        todayHighlight: true,
                        format: "dd/mm/yyyy",
                        dateFormat: "dd/mm/yyyy",
                      });
                    },
                    complete: function () {
                      $("#aplicar-btn").off("click");
                      $("#aplicar-btn").click(function () {
                        table_Entidad.ajax.reload(); // Recargar la tabla sin reiniciar la paginación
                      });
                    },
                  });
                  configurarBuscador(
                    "table-cotizacion-embarque",
                    "search-table",
                    "table-cotizacion-embarque_info"
                  );
                  await getTableCotizacionEmbarqueHeaders();

                }
                currentTableCotizacion = "embarque";
                $(".input-date").datepicker({
                  autoclose: true,
                  startDate: new Date(fYear, fToday.getMonth(), fDay),
                  todayHighlight: true,
                  format: "dd/mm/yyyy",
                  dateFormat: "dd/mm/yyyy",
                });
              },
            },
          ],
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
          },
          columnDefs: [
            {
              targets: "no-hidden",
              visible: false,
            },
          ],
          pageLength: 100, // Mostrar 100 elementos por página
          lengthMenu: [
            [100, 1000, -1],
            [100, 1000, "Todos"],
          ],
          ajax: {
            url: url,
            type: "POST",
            dataType: "JSON",
            data: function (data) {
              data.stepIndex = stepIndex;
              data.idContenedor = idContenedor;
              data.tipoTabla = "prospectos";
            },
            complete: async function () {
              $(".width_full").val($("#hidden-sCorrelativoCotizacion").val());
              $("#aplicar-btn").off("click");
              $("#aplicar-btn").click(function () {
                table_Entidad.ajax.reload(); // Recargar la tabla sin reiniciar la paginación
              });
              spinner.hide();
              // clean options in select txt-ID_Estado and add option todos value 0 , PENDIENTE VALUE PENDIENTE AND CONFIRMADO VALUE CONFIRMADO IF currentPrivilege =="Cotizador
              if (
                currentPrivilege == "Cotizador" 
              ) {
                $("#txt-ID_Estado").empty();
                $("#txt-ID_Estado").append(
                  '<option value="0">Todos</option>' +
                  '<option value="PENDIENTE">Pendiente</option>' +
                  '<option value="CONFIRMADO">Confirmado</option>'
                );
              }
              await getTableCotizacionEmbarqueHeaders();
              
            },
          },
          columnDefs: [
            {
              targets: "no-hidden",
              visible: false,
            },
            {
              className: "text-center",
              targets: "no-sort",
              orderable: false,
            },
            {
              targets: "",
              orderable: false,
            },
          ],
          pageLength: 100, // Mostrar 100 elementos por página
          lengthMenu: [
            [100, 1000, -1],
            [100, 1000, "Todos"],
          ],
        });
      }
      configurarBuscador(
        "table-cotizacion-prospectos",
        "search-table",
        "table-cotizacion-prospectos_info"
      );

      await getTipoCliente();
    }
  } else if (stepIndex == 2 && currentPrivilege == "Documentacion") {
    showDocumentacionDocumentacionContainer(id);
  } else if (
    stepIndex == 2 ||
    (currentPrivilege == "Documentacion" && stepIndex == 1)
  ) {
    await getClientesHeader();
    url = base_url + "CargaConsolidada/ContenedorConsolidado/step";
    clientesContainer.show();
    if ($.fn.DataTable.isDataTable("#table-clientes-general")) {
      reloadTableClientesGeneral();
    } else {
      tableClientesGeneral.show();

      tableClientesGeneral = $("#table-clientes-general").DataTable({
        dom:
          "<'row'<'col-sm-12 col-md-4'B><'col-sm-12 col-md-7'f><'col-sm-12 col-md-1'>>" +
          "<'row'<'col-sm-12'tr>>" +
          "<'row'<'col-sm-12 col-md-2'l><'col-sm-12 col-md-5'i><'col-sm-12 col-md-5'p>>",
        buttons: [
          {
            extend: "excel",
            text: '<i class="fa fa-file-excel color_icon_excel"></i> Excel',
            titleAttr: "Excel",
            exportOptions: {
              columns: ":visible",
            },
            attr: {
              id: "export-excel-main",
              class: "hidden",
            },
          },
          {
            text: "General",
            action: function () {
              if ($.fn.DataTable.isDataTable("#table-clientes-variacion")) {
                $("#table-clientes-variacion").attr("style", "display:none");
                $("#table-clientes-variacion_wrapper").hide();
              }
              if ($.fn.DataTable.isDataTable("#table-clientes-general")) {
                //display block
                $("#table-clientes-general").attr("style", "");
                $("#table-clientes-general_wrapper").show();
                reloadTableClientesGeneral();
              } else {
                $("#table-clientes-general").attr("style", "");
                $("#table-clientes-general_wrapper").show();
                reloadTableClientesGeneral();
              }
            },
            className: "btn btn-light",
          },
          currentPrivilege != "Documentacion"
            ? {
              text: "Variación",
              action: function () {
                if ($.fn.DataTable.isDataTable("#table-clientes-general")) {
                  $("#table-clientes-general").attr("style", "display:none");
                  $("#table-clientes-general_wrapper").hide();
                }
                if ($.fn.DataTable.isDataTable("#table-clientes-variacion")) {
                  $("#table-clientes-variacion").attr("style", "");
                  $("#table-clientes-variacion_wrapper").show();
                  reloadTableClientesVariacion();
                } else {
                  url =
                    base_url + "CargaConsolidada/ContenedorConsolidado/step";
                  tableClientesVariacion.show();
                  tableClientesVariacion = $(
                    "#table-clientes-variacion"
                  ).DataTable({
                    dom:
                      "<'row'<'col-sm-12 col-md-7'B><'col-sm-12 col-md-4'f><'col-sm-12 col-md-1'>>" +
                      "<'row'<'col-sm-12'tr>>" +
                      "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-5'i><'col-sm-12 col-md-5'p>>",
                    buttons: [
                      {
                        extend: "excel",
                        text: '<i class="fa fa-file-excel color_icon_excel"></i> Excel',
                        titleAttr: "Excel",
                        exportOptions: {
                          columns: ":visible",
                        },
                        attr: {
                          id: "export-excel-main",
                          class: "hidden",
                        },
                      },
                      {
                        extend: "pdf",
                        text: '<i class="fa fa-file-pdf color_icon_pdf"></i> PDF',
                        titleAttr: "PDF",
                        exportOptions: {
                          columns: ":visible",
                        },
                        attr: {
                          id: "export-pdf-main",
                          class: "hidden",
                        },
                      },
                      {
                        text: "General",
                        action: function () {
                          if (
                            $.fn.DataTable.isDataTable(
                              "#table-clientes-variacion"
                            )
                          ) {
                            $("#table-clientes-variacion").attr(
                              "style",
                              "display:none"
                            );
                            $("#table-clientes-variacion_wrapper").hide();
                          }
                          if (
                            $.fn.DataTable.isDataTable(
                              "#table-clientes-general"
                            )
                          ) {
                            $("#table-clientes-general_wrapper").show();

                            $("#table-clientes-general").attr("style", "");
                            reloadTableClientesGeneral();
                          } else {
                            $("#table-clientes-general").attr("style", "");
                            reloadTableClientesGeneral();
                          }
                        },
                      },
                      {
                        text: "Variación",
                        className: "btn btn-light",
                        action: function () {
                          if (
                            $.fn.DataTable.isDataTable(
                              "#table-clientes-general"
                            )
                          ) {
                            $("#table-clientes-general").attr(
                              "style",
                              "display:none"
                            );
                            $("#table-clientes-general_wrapper").hide();
                          }
                          if (
                            $.fn.DataTable.isDataTable(
                              "#table-clientes-variacion"
                            )
                          ) {
                            $("#table-clientes-variacion").attr("style", "");
                          } else {
                            $("#table-clientes-variacion").attr("style", "");
                          }
                        },
                      },
                      // {
                      //     text: "<i class='fa fa-upload'></i> Lista de embarque",
                      //     action: function () {
                      //         Swal.fire({
                      //             title: "Subir lista de embarque",
                      //             input: "file",
                      //             inputAttributes: {
                      //                 accept: "*",
                      //                 "aria-label": "Sube tu archivo",
                      //             },
                      //             showCancelButton: true,
                      //             confirmButtonText: "Subir",
                      //             showLoaderOnConfirm: true,
                      //             preConfirm: (file) => {
                      //                 const formData = new FormData();
                      //                 formData.append("file", file);
                      //                 formData.append("idContenedor", idContenedor);
                      //                 formData.append("idCotizacion", idCotizacion);
                      //                 return fetch(base_url + "CargaConsolidada/ContenedorConsolidado/uploadListaEmbarque", {
                      //                     method: "POST",
                      //                     body: formData,
                      //                 })
                      //                     .then((response) => {
                      //                         return response.json();
                      //                     })
                      //                     .catch((error) => {
                      //                         Swal.showValidationMessage(
                      //                             `Request failed: ${error}`
                      //                         );
                      //                     });
                      //             },
                      //             allowOutsideClick: () => !Swal.isLoading(),
                      //         }).then((result) => {
                      //             if (result.value) {
                      //                 if (result.value.status == "success") {
                      //                     Swal.fire("Correcto", result.value.message, "success");
                      //                     reloadTableClientesVariacion();
                      //                 } else {
                      //                     Swal.fire("Error", result.value.message, "error");
                      //                 }
                      //             }
                      //         });
                      //     },
                      //     className: "btn btn-secondary btn-lista-embarque"
                      // }
                    ],
                    columnDefs: [
                      {
                        targets: "no-hidden",
                        visible: false,
                      },
                      {
                        className: "text-center",
                        targets: "no-sort",
                        orderable: false,
                      },
                      {
                        targets: "",
                        orderable: false,
                      },
                      {
                        targets: "sorting_asc",
                        orderable: false,
                      },
                    ],
                    pageLength: 100, // Mostrar 100 elementos por página
                    lengthMenu: [
                      [100, 1000, -1],
                      [100, 1000, "Todos"],
                    ],
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
                      sInfo:
                        "Mostrando (_START_ - _END_) total de registros _TOTAL_",
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
                    },
                    ajax: {
                      url: url,
                      type: "POST",
                      dataType: "JSON",
                      data: function (data) {
                        data.stepIndex = stepIndex;
                        data.idContenedor = idContenedor;
                        data.tipoTabla = "variacion";
                        data.estado = $("#txt-ID_Estado").val();
                        validateListEmbarque(idContenedor);
                      },
                    },
                  });
                  configurarBuscador(
                    "table-clientes-variacion",
                    "search-table",
                    "table-clientes-variacion_info"
                  );
                }
              },
            }
            : null,
        ]
          //filter null
          .filter(Boolean),
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: true,
        info: true,
        autoWidth: false,
        responsive: false,
        serverSide: false,
        pagingType: "full_numbers",
        columnDefs: [
          {
            targets: "no-hidden",
            visible: false,
          },
          {
            targets: "no-sort",
            orderable: false,
          },
          {
            targets: "",
            orderable: false,
          },
          {
            targets: "sorting_asc",
            orderable: false,
          },
        ],
        pageLength: 100, // Mostrar 100 elementos por página
        lengthMenu: [
          [100, 1000, -1],
          [100, 1000, "Todos"],
        ],
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
        },
        ajax: {
          url: url,
          type: "POST",
          dataType: "JSON",
          data: function (data) {
            data.stepIndex = stepIndex;
            data.idContenedor = idContenedor;
            data.tipoTabla = "general";
            data.estado = "0";
          },
        },

      });
      configurarBuscador(
        "table-clientes-general",
        "search-table",
        "table-clientes-general_info"
      );

    }
  } else if (stepIndex == 3 && currentPrivilege == "Documentacion") {
    viewFormularioAduana();
  } else if (stepIndex == 3) {
    viewDocumentacion();
  } else if (stepIndex == 4) {
    viewCotizacionFinal();
  } else if (stepIndex == 5) {
    viewFacturaGuia();
  }
  $(".btn-back-cotizacion").off("click");
  $(".btn-back-cotizacion").on("click", function () {
    if (currentPrivilege == "ContenedorAlmacen") {
      mainContainer.show();
      contentHeader.show();
      cotizacionContainer.hide();
      clientesDocumentacionContainer.hide();
      table_Entidad.ajax.reload()
      stepsContainer.hide();
    } else {
      returnToSteps();
    }
  });
  $(".btn-back-cotizacion-documentacion").off("click");
  $(".btn-back-cotizacion-documentacion").on("click", function () {
    if (shouldSaveDocumentacion) {
      //ajax confirm to back without save
      Swal.fire({
        title: "Are you sure?",
        text: "You have unsaved changes!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, go back",
        cancelButtonText: "No, stay here",
      }).then((result) => {
        if (result.isConfirmed) {
          clientesContainer.show();
          cotizacionContainer.hide();
          clientesDocumentacionContainer.hide();
          stepsContainer.hide();
          selectedTabDocumentacionId = null;
          shouldSaveDocumentacion = false;
        }
      });
    } else {
      clientesContainer.show();
      cotizacionContainer.hide();
      clientesDocumentacionContainer.hide();
      stepsContainer.hide();
      selectedTabDocumentacionId = null;
      shouldSaveDocumentacion = false;

    }

  });

  spinner.hide();
};

function configurarExportarExcel(buttonId, url, fileName) {
  $("#" + buttonId).on("click", function () {
    $.ajax({
      url: url,
      type: "GET",
      xhrFields: {
        responseType: "blob",
      },
      success: function (response) {
        var blob = new Blob([response], {
          type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        });
        var link = document.createElement("a");
        link.href = window.URL.createObjectURL(blob);
        link.download = fileName + ".xlsx";
        link.click();
      },
      error: function () {
        Swal.fire(
          "Error!",
          "Hubo un error al descargar el archivo Excel",
          "error"
        );
      },
    });
  });
}

async function configurarBuscador(tableId, searchInputClass, infoContainerId) {
  // Obtener la instancia de DataTable
  var table = $("#" + tableId).DataTable();
  console.log(table);

  // Escuchar el evento "input" en el buscador
  $("." + searchInputClass).on("input", function () {
    var searchTerm = $(this).val(); // Obtener el valor del buscador
    table.search(searchTerm).draw(); // Aplicar la búsqueda y redibujar la tabla
  });

  // Actualizar el mensaje de información después de cada búsqueda
  table.on("draw", function () {
    var info = table.page.info();
    if (infoContainerId) {
      $("#" + infoContainerId).html(
        `Mostrando ${info.start + 1} a ${info.end} de ${info.recordsTotal
        } registros`
      );
    }
  });
}

async function viewFacturaGuia() {
  $("#factura-guia-title").html(`
    Cotizacion #${currentCargaNumber}
    Factura Guia`);
  facturaGuiaContainer.show();
  spinner.show();
  url = base_url + "CargaConsolidada/ContenedorConsolidado/step";
  if ($.fn.DataTable.isDataTable("#table-factura-guia")) {
    tableFacturaGuia.ajax.reload();
  } else {
    tableFacturaGuia.show();

    tableFacturaGuia = $("#table-factura-guia").DataTable({
      dom:
        "<'row'<'col-sm-12 col-md-4'B><'col-sm-12 col-md-7'f><'col-sm-12 col-md-1'>>" +
        "<'row'<'col-sm-12'tr>>" +
        "<'row'<'col-sm-12 col-md-2'l><'col-sm-12 col-md-5'i><'col-sm-12 col-md-5'p>>",
      buttons: [
        {
          extend: "excel",
          text: '<i class="fa fa-file-excel color_icon_excel"></i> Excel',
          titleAttr: "Excel",
          exportOptions: {
            columns: ":visible",
          },
        },
        {
          extend: "pdf",
          text: '<i class="fa fa-file-pdf color_icon_pdf"></i> PDF',
          titleAttr: "PDF",
          exportOptions: {
            columns: ":visible",
          },
        },
        {
          extend: "colvis",
          text: '<i class="fa fa-ellipsis-v"></i> Columnas',
          titleAttr: "Columnas",
          exportOptions: {
            columns: ":visible",
          },
        },
      ],
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
      },
      columnDefs: [
        {
          targets: "no-hidden",
          visible: false,
        },
        {
          className: "text-center",
          targets: "no-sort",
          orderable: false,
        },
        {
          targets: "",
          orderable: false,
        },
      ],
      pageLength: 100, // Mostrar 100 elementos por página
      lengthMenu: [
        [100, 1000, -1],
        [100, 1000, "Todos"],
      ],
      ajax: {
        url: url,
        type: "POST",
        dataType: "JSON",
        data: function (data) {
          data.stepIndex = stepIndex;
          data.idContenedor = idContenedor;
        },
      },
      initComplete: function (settings, json) {
        spinner.hide();
      },
    });
  }
}
async function viewCotizacionFinal() {
  cotizacionFinalContainer.show();
  $("#cotizacion-final-title").html(`
    Cotizacion #${currentCargaNumber}
    Cotización Final`);
  spinner.show();
  url = base_url + "CargaConsolidada/ContenedorConsolidado/step";
  if ($.fn.DataTable.isDataTable("#table-cotizacion-final")) {
    tableCotizacionFinal.ajax.reload();
  } else {
    tableCotizacionFinal.show();

    tableCotizacionFinal = $("#table-cotizacion-final").DataTable({
      dom:
        "<'row'<'col-sm-12 col-md-4'B><'col-sm-12 col-md-7'f><'col-sm-12 col-md-1'>>" +
        "<'row'<'col-sm-12'tr>>" +
        "<'row'<'col-sm-12 col-md-2'l><'col-sm-12 col-md-5'i><'col-sm-12 col-md-5'p>>",
      buttons: [
        {
          extend: "excel",
          text: '<i class="fa fa-file-excel color_icon_excel"></i> Excel',
          titleAttr: "Excel",
          exportOptions: {
            columns: ":visible",
          },
        },
        {
          extend: "pdf",
          text: '<i class="fa fa-file-pdf color_icon_pdf"></i> PDF',
          titleAttr: "PDF",
          exportOptions: {
            columns: ":visible",
          },
        },
        {
          extend: "colvis",
          text: '<i class="fa fa-ellipsis-v"></i> Columnas',
          titleAttr: "Columnas",
          exportOptions: {
            columns: ":visible",
          },
        },
      ],
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
      },
      columnDefs: [
        {
          targets: "no-hidden",
          visible: false,
        },
        {
          className: "text-center",
          targets: "no-sort",
          orderable: false,
        },
        {
          targets: "",
          orderable: false,
        },
      ],
      pageLength: 100, // Mostrar 100 elementos por página
      lengthMenu: [
        [100, 1000, -1],
        [100, 1000, "Todos"],
      ],
      ajax: {
        url: url,
        type: "POST",
        dataType: "JSON",
        data: function (data) {
          data.stepIndex = stepIndex;
          data.idContenedor = idContenedor;
        },
      },
      initComplete: function (settings, json) {
        spinner.hide();
      },
    });
  }
}
async function deleteDocumentacionFolder(id) {
  Swal.fire({
    title: "¿Estás seguro?",
    text: "¡No podrás revertir esto!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, eliminarlo",
    cancelButtonText: "No, cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      url =
        base_url +
        "CargaConsolidada/ContenedorConsolidado/deleteDocumentacionFolder/" +
        id;
      $.ajax({
        url: url,
        type: "GET",
        success: function (response) {
          const result = JSON.parse(response);
          if (result.status == "success") {
            Swal.fire("Eliminado!", result.message, "success");
          } else {
            Swal.fire("Error!", result.message, "error");
          }
          viewDocumentacion();
        },
      });
    }
  });
}
async function updateEstadoCotizador(id) {
  const estado = $(`#estado-cotizador-${id}`).val();
  url =
    base_url + "CargaConsolidada/ContenedorConsolidado/updateEstadoCotizador";
  $.ajax({
    url: url,
    type: "POST",
    data: {
      id: id,
      estado: estado,
    },
    success: function (response) {
      const result = JSON.parse(response);
      if (result.status == "success") {
        Swal.fire("Correcto!", "Se cambio el estado con wéxito", "success");
      } else {
        Swal.fire("Error!", result.message, "error");
      }
      reloadTableCotizacion();
    },
  });
}
async function viewDocumentacion() {
  spinner.show();
  $.ajax({
    url: base_url + "CargaConsolidada/ContenedorConsolidado/step",
    type: "POST",
    data: {
      stepIndex: stepIndex,
      idContenedor: idContenedor,
    },
    success: function (response) {
      documentationContainer.show();
      spinner.hide();
      let dataParsed = JSON.parse(response);
      $(".documentation-files-container").empty();
      dataParsed.forEach((file) => {
        $(".documentation-files-container").append(`
                    <div class="col-12 col-sm-12" id="single-${file.id}">
                        <div class="form-group">
                            <label>${file.folder_name}
                              ${file.id_contenedor ? `<div class="badge badge-danger text-white delete-folder-button" onclick="deleteDocumentacionFolder(${file.id})">X</div>` : ""}
                            </label>
                            <div class="file-upload-box">
                                ${file.file_url ? `
                                        <div class="file-info">
                                          <div class="file-iconic">
                                            ${getIconByType(file.type)}
                                          </div>
                                            <span class="file-name">${file.folder_name}</span>
                                            
                                            <button class="download-file-button" onclick=window.location.href='${file.file_url}'>
                                            <i class="fas fa-download"></i>
                                            </button>
                                            <div  onclick="deleteDocumentacionFile(${file.id_file})">
                                            <i class="fas fa-trash"></i>
                                            </div>
                                        </div>
                                `
            : `
                                    <input type="file" id="file-input-${file.id}" class="file-input" accept=".pdf, .docx, .xlsx, .xls, .doc, .xlsm, .csv, .xlsb, .xltx, .xlt"/>
                                    <label for="file-inputo" class="file-label d-flex">
                                        <i class="fas fa-upload"></i>
                                        <div class="file-group-text">
                                            <span class="file-text">Selecciona o arrastra tu archivo aquí</span>
                                            <span class="file-format">Formatos: .pdf, .docx, .xlsx, .xls, .doc, .xlsm, .csv, .xlsb, .xltx, .xlt</span>
                                        </div>
                                        <button class="upload-button upload-button-documentacion-peru-${file.id}" type="button">Subir archivo</button>
                                    </label>
                                    <!-- Cuadro de información del archivo subido (oculto inicialmente) -->
                                    <div class="file-info-box hidden">
                                        <div class="file-info">
                                          <div class="file-iconic"></div>
                                            <span class="file-name"></span>
                                            <span class="file-size"></span>
                                            <button class="upload-file-button" type="button" onclick="openUploadFileDocumentation(${file.id})">
                                            <i class="fas fa-save"></i>
                                            </button>
                                            <button class="remove-file-button">
                                            <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <script>   setupSingleFileUpload('single-${file.id}', 'file-input-${file.id}', ['xlsx', 'xls', 'csv', 'xlsm','pdf','docx','doc'], '.upload-button-documentacion-peru-${file.id}')</script>
                                `
          }
                        </div>    
                    </div>
                `);
      });
    },
  });
}

async function reloadTableClientesGeneral() {
  tableClientesGeneral.ajax.reload();
  await getClientesHeader();
}
async function reloadTableClientesVariacion() {
  tableClientesVariacion.ajax.reload();
  await getClientesHeader();
}

async function reloadTableCotizacionEmbarque() {
  tableCotizacionEmbarque.ajax.reload();
  await getTableCotizacionEmbarqueHeaders();
}
async function reloadTableCotizacionFinal() {
  tableCotizacionFinal.ajax.reload();
}
async function getTableCotizacionEmbarqueHeaders() {
  url =
    base_url +
    "CargaConsolidada/ContenedorConsolidado/getCotizacionEmbarqueHeaders/" +
    idContenedor;

  const response = await fetch(url);
  const result = await response.json();
  $("#cotizacion_name").val('#' + currentCargaNumber);
  $("#txt-CBM_Total_Peru").html(result.cbm_total);
  $("#txt-CBM_Total_China").html(result.cbm_total_china);
  $("#txt-CBM_Total_Pendiente").html(result.cbm_total_pendiente);
  //if result.lista_embarque_url is not null add button to download else file input with button to upload remember remove and add event listener
  if (result.lista_embarque_url) {
    $("#packing-list-container").empty();
    $("#packing-list-container").append(`
        <a href="${result.lista_embarque_url}" target="_blank" class="bg-white hover:bg-white-200 text-black-200 py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block">
        <i class="fas fa-file-alt"></i>Packing List
        </a>
        <button class="btn text-danger" onclick="deleteListaEmbarque()">
        <i class="fa fa-trash"></i>
        </button>
        `);
    //add event for delete
  } else {
    $("#packing-list-container").empty();
    $("#packing-list-container").append(`
        <button class="bg-white hover:bg-white-200 text-black-200 py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block"
        id="btn-upload-lista-embarque"
        >
        <i class="fa fa-upload"></i>Packing List
        </button>
   
        `);

    $("#btn-upload-lista-embarque").off("click");
    $("#btn-upload-lista-embarque").on("click", async function () {
      //open swall with input file
      const { value: file } = await Swal.fire({
        title: "Subir lista de embarque",
        input: "file",
        inputAttributes: {
          accept: "*",
          "aria-label": "Sube tu archivo",
        },
        showCancelButton: true,
        confirmButtonText: "Subir",
        showLoaderOnConfirm: true,
        preConfirm: (file) => {
          const formData = new FormData();
          formData.append("file", file);
          formData.append("idContenedor", idContenedor);
          formData.append("idCotizacion", idCotizacion);
          return fetch(
            base_url +
            "CargaConsolidada/ContenedorConsolidado/uploadListaEmbarque",
            {
              method: "POST",
              body: formData,
            }
          )
            .then((response) => {
              getTableCotizacionEmbarqueHeaders();
              //show success message
              if (response.status == 200) {
                Swal.fire("Correcto", "Se subió la lista de embarque", "success");
              } else {
                Swal.fire("Error", "No se pudo subir la lista de embarque", "error");
              }
              return response.json();
            })
            .catch((error) => {
              Swal.showValidationMessage(`Request failed: ${error}`);
            });
        },
        allowOutsideClick: () => !Swal.isLoading(),
      });
    });
  }
  if (result.bl_file_url) {
    $("#bl-file-container").empty();
    $("#bl-file-container").append(`
        <a href="${result.bl_file_url}" target="_blank" class="bg-white hover:bg-white-200 text-black-200 py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block">
        <i class="fas fa-file-alt"></i>BL File
        </a>
        <button class="btn text-danger" onclick="deleteBL()">
        <i class="fa fa-trash"></i>
        </button>
        `);
  } else {
    $("#bl-file-container").empty();
    $("#bl-file-container").append(`
            <button class="bg-white hover:bg-white-200 text-black-200 py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block"
            id="btn-upload-bl" >
            <i class="fa fa-upload"></i>BL File
        </button>
       
        `);

    $("#btn-upload-bl").off("click");
    $("#btn-upload-bl").on("click", async function () {
      //open swall with input file
      const { value: file } = await Swal.fire({
        title: "Subir BL",
        input: "file",
        inputAttributes: {
          accept: "*",
          "aria-label": "Sube tu archivo",
        },
        showCancelButton: true,
        confirmButtonText: "Subir",
        showLoaderOnConfirm: true,
        preConfirm: (file) => {
          if (!file) {
            Swal.showValidationMessage("Debes seleccionar un archivo");
            return;
          }
          const formData = new FormData();
          formData.append("file", file);
          formData.append("idContenedor", idContenedor);
          return fetch(
            base_url + "CargaConsolidada/ContenedorConsolidado/uploadBL",
            {
              method: "POST",
              body: formData,
            }
          )
            .then((response) => {
              console.log("Respuesta del servidor:", response); // Depuración
              if (!response.ok) {
                throw new Error("Error al subir el archivo");
              }
              return response.json();
            })
            .then((result) => {
              console.log("Resultado del servidor:", result); // Depuración
              return result;
            })
            .catch((error) => {
              Swal.showValidationMessage(`Request failed: ${error}`);
            });
        },
        allowOutsideClick: () => !Swal.isLoading(),
      });
      if (file) {
        if (file.status === "success") {
          Swal.fire({
            icon: "success",
            title: "¡Archivo subido!",
            text: "El archivo BL se ha subido correctamente.",
            timer: 3000,
            showConfirmButton: false,
          });
          getTableCotizacionEmbarqueHeaders(); // Actualizar la lista de archivos
        } else {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: file.message || "Hubo un problema al subir el archivo.",
          });
        }
      }
    });
  }
}

async function deleteBL() {
  Swal.fire({
    title: "¿Estás seguro?",
    text: "¡No podrás revertir esto!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, eliminarlo",
    cancelButtonText: "No, cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      spinner.show(); // Mostrar spinner
      url =
        base_url +
        "CargaConsolidada/ContenedorConsolidado/deleteBL/" +
        idContenedor;
      $.ajax({
        url: url,
        type: "GET",
        success: function (response) {
          const result = JSON.parse(response);
          console.log(response);
          if (result.status == "success") {
            Swal.fire("Eliminado!", result.message, "success");
            getTableCotizacionEmbarqueHeaders();
          } else {
            Swal.fire("Error!", result.message, "error");
          }
        },
        error: function () {
          Swal.fire("Error!", "Hubo un error en la solicitud", "error");
        },
        complete: function () {
          spinner.hide(); // Ocultar spinner siempre
        },
      });
    }
  });
}
async function deleteListaEmbarque() {
  Swal.fire({
    title: "¿Estás seguro?",
    text: "¡No podrás revertir esto!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, eliminarlo",
    cancelButtonText: "No, cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      url =
        base_url +
        "CargaConsolidada/ContenedorConsolidado/deleteListaEmbarque/" +
        idContenedor;
      $.ajax({
        url: url,
        type: "GET",
        success: function (response) {
          const result = JSON.parse(response);
          if (result.status == "success") {
            Swal.fire("Eliminado!", result.message, "success");
          } else {
            Swal.fire("Error!", result.message, "error");
          }
          getTableCotizacionEmbarqueHeaders();
        },
      });
    }
  });
}

async function deleteDocumentacionFile(id) {
  event.preventDefault();
  Swal.fire({
    title: "¿Estás seguro?",
    text: "¡No podrás revertir esto!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí  , eliminarlo",
    cancelButtonText: "No, cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      url = base_url + "CargaConsolidada/ContenedorConsolidado/deleteDocumentacionFile/" + id;
      $.ajax({
        url: url,
        type: "GET",
        success: function (response) {
          const result = JSON.parse(response);
          if (result.status == "success") {
            Swal.fire("Eliminado!", result.message, "success");
          } else {
            Swal.fire("Error!", result.message, "error");
          }
          viewDocumentacion();
        },
      });
    }
  });
}
async function deleteDocumentacionFileDocumentacion(id) {
  event.preventDefault();
  Swal.fire({
    title: "¿Estás seguro?",
    text: "¡No podrás revertir esto!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí  , eliminarlo",
    cancelButtonText: "No, cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      url = base_url + "CargaConsolidada/ContenedorConsolidado/deleteDocumentacionFile/" + id;
      $.ajax({
        url: url,
        type: "GET",
        success: function (response) {
          const result = JSON.parse(response);
          if (result.status == "success") {
            Swal.fire("Eliminado!", result.message, "success");
          } else {
            Swal.fire("Error!", result.message, "error");
          }
          showDocumentacionDocumentacionContainer();
        },
      });
    }
  });
}
async function openUploadFileDocumentation(idFolder) {
  const file = $(`#file-input-${idFolder}`)[0].files[0];
  console.log(file);
  if (!file) {
    Swal.fire({
      icon: "error",
      title: "Error",
      text: "No se ha seleccionado ningún archivo.",
    });
    return;
  }
  const fileExtension = file.name.split('.').pop().toLowerCase(); // Obtener la extensión del archivo
  const fileType = file.type; // Tipo MIME completo

  const formData = new FormData();
  formData.append("file", file);
  formData.append("idFolder", idFolder);
  formData.append("idContenedor", idContenedor);

  try {
    const response = await fetch(
      base_url +
      "CargaConsolidada/ContenedorConsolidado/uploadFileDocumentation",
      {
        method: "POST",
        body: formData,
      }
    );

    const result = await response.json();
    console.log("Respuesta del servidor:", result); // Depuración

    if (result.status === "success" || result.file_url) {
      const fileName = result.file_name || file.name;
      const fileSize = result.file_size || file.size;
      const fileIcon = getIconByType(fileType || fileExtension);
      console.log("Icono del archivo:", fileIcon); // Depuración

      const fileContainer = document.getElementById(`single-${idFolder}`);
      console.log(fileContainer, "fileContainer"); // Depuración
      if (fileContainer) {
        const fileIconElement = fileContainer.querySelector(".file-iconic");
        const fileNameElement = fileContainer.querySelector(".file-name");
        const fileSizeElement = fileContainer.querySelector(".file-size");
        console.log("Elementos encontrados:", fileIconElement, fileNameElement, fileSizeElement); // Depuración

        if (fileIconElement && fileNameElement && fileSizeElement) {
          console.log("Elementos encontrados:", fileIconElement, fileNameElement, fileSizeElement); // Depuración
          fileIconElement.innerHTML = fileIcon;
          fileNameElement.textContent = fileName;
          fileSizeElement.textContent = `${(fileSize / 1024).toFixed(2)} KB`;
        }
      }

      Swal.fire({
        icon: "success",
        title: "¡Archivo subido!",
        html: `
                    <div class="text-left">
                        <p><strong>Nombre:</strong> ${fileName}</p>
                        <p><strong>Tamaño:</strong> ${(fileSize / 1024).toFixed(
          2
        )} KB</p>
                    </div>
                `,
        showConfirmButton: false,
        timer: 3000,
      });

      viewDocumentacion(); // Actualiza la lista de archivos
    } else {
      Swal.fire({
        icon: "error",
        title: "Error al subir",
        text: result.message || "Error desconocido",
      });
    }
  } catch (error) {
    Swal.fire({
      icon: "error",
      title: "Error de conexión",
      html: `
                No se pudo contactar al servidor. <br>
                <small>${error.message}</small>
            `,
    });
  }
}
async function viewCotizacion(id) {
  url =
    base_url + "CargaConsolidada/ContenedorConsolidado/showCotizacion/" + id;
  const response = await fetch(url);
  const result = await response.json();
  $("#txt-ID_Tipo_Cliente").val(result.id_tipo_cliente);
  $("#txt-Fe_Cotizacion").val(result.fecha);
  $("#txt-Nombre").val(result.nombre);
  $("#txt-Dni").val(result.documento);
  $("#txt-Correo").val(result.correo);
  $("#txt-Whatsapp").val(result.telefono);
  $("#txt-Volumen").val(result.volumen);
  if (result.cotizacion_file_url) {
    //set value of form to show file
    $("#txt-CotizacionFile").val(result.cotizacion_file);
  }
  $("#modal-crear-cotizacion").modal("show");
  $("#btn-guardar-cotizacion").hide();
  $("#btn-actualizar-cotizacion").show();
  idCotizacion = result.id;
}
async function uploadCotizacionFile(id) {
  //swall with input file
  const { value: file } = await Swal.fire({
    title: "Subir cotización",
    input: "file",
    inputAttributes: {
      accept: "*",
      "aria-label": "Sube tu archivo",
    },
    showCancelButton: true,
    confirmButtonText: "Subir",
    showLoaderOnConfirm: true,
    preConfirm: (file) => {
      const formData = new FormData();
      formData.append("file", file);
      formData.append("id", id);
      return fetch(
        base_url +
        "CargaConsolidada/ContenedorConsolidado/uploadCotizacionFile",
        {
          method: "POST",
          body: formData,
        }
      )
        .then((response) => {
          return response.json();
        })
        .catch((error) => {
          Swal.showValidationMessage(`Request failed: ${error}`);
        });
    },
    allowOutsideClick: () => !Swal.isLoading(),
  });
  if (file) {
    if (file.status == "success") {
      Swal.fire({
        icon: "success",
        title: "Correcto",
        text: file.message,
      });
      reloadTableCotizacion();
    } else {
      Swal.fire({
        icon: "error",
        title: "Error",
        text: file.message,
      });
    }
  }
}
const validateVolumenDocumentacion = (value) => {
  if (value !== originalVolumenDocumento) {
    shouldSaveDocumentacion = true;
  }
}
const validateValorDocumentacion = (value) => {
  if (value !== originalValorDocumento) {
    shouldSaveDocumentacion = true;
  }
}
async function viewDocumentacionByDocumentacionProfile(idCotizacion) {
  clientesContainer.hide();
  documentationContainerProfile.show();
  url =
    base_url +
    "CargaConsolidada/ContenedorConsolidado/showClientesDocumentacionByDoc/" +
    idCotizacion;
  spinner.show();
  const response = await fetch(url);
  const result = await response.json();
  //foreach row in result add  button <button class="provider-btn px-6 py-3 rounded-lg font-medium transition-all duration-300 shadow-sm hover:shadow-md bg-orange-500 text-white">PROVEEDOR 1</button> to .providers container
  const proveedores_documentacion = result.proveedores_documentacion;
  $(".providers").empty();
  const parsed_proveedores_documentacion = JSON.parse(
    proveedores_documentacion
  );
  parsed_proveedores_documentacion.forEach((row, index) => {
    $(".providers").append(`
        <button class="provider-btn px-6 py-3 
        rounded-lg font-medium transition-all duration-300 shadow-sm hover:shadow-md bg-orange-500 text-white"
         id="tab-${index}"
        >${row.code_supplier} </button>
       
        `);
    $("#tab-" + index).off("click");
    $("#tab-" + index).on("click", function () {
      loadProviderData(index, parsed_proveedores_documentacion);
    });
  });
  loadProviderData(0, parsed_proveedores_documentacion);
  spinner.hide();
}
async function loadProviderData(index, parsed_proveedores_documentacion) {
  console.log(index, parsed_proveedores_documentacion);
  firstProvider = parsed_proveedores_documentacion[index];
  documentacionPeru = firstProvider.documentacion_peru;
  documentacionChina = firstProvider.documentacion_china;
  documentosAdicionales = firstProvider.documentos_adicionales;
  inspeccion = firstProvider.inspeccion;
  $("#txt-Vol_Doc").val(documentacionPeru.volumen_doc);
  $("#txt-Valor_Doc").val(documentacionPeru.valor_doc);

  const facturaComercial = documentacionPeru.factura_comercial;
  const excelConfirmacion = documentacionPeru.excel_confirmacion;
  $("#documentacion-peru-documents").empty();
  if (facturaComercial) {
    $("#documentacion-peru-documents").append(`
            <div class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
            <div class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
            <div class="p-3 bg-green-100 rounded-full">
                <i class="fa fa-file-text text-green-600" style="font-size: 1.5rem;"></i>
            </div>
            <div class="ml-4 flex-grow">
                <div class="text-title font-medium text-gray-700">F. Comercial</div>
                <div class="text-sm text-gray-500">Descargar documento</div>
            </div>
           
            </div>
             <div class="flex gap-2">
                <a href="${facturaComercial}" target="_blank" class="download-btn px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                Descargar
                </a>
            </div>
    </div>
        `);
  } else {
    $("#documentacion-peru-documents")
      .append(` <div class="flex items-center p-4 bg-gray-50 rounded-lg">
      <div class="p-3 bg-red-100 rounded-full">
        <i class="fa fa-file text-red-600" style="font-size: 1.5rem;"></i>
      </div>
      <div class="ml-4 flex-grow">
        <div class="text-title font-medium text-gray-700">F. Comercial</div>
        <div class="text-sm text-red-500">Documento no disponible</div>
      </div>
   
    </div>`);
  }
  if (excelConfirmacion) {
    $("#documentacion-peru-documents").append(`
                        <div class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">

        <div class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
        <div class="p-3 bg-green-100 rounded-full">
            <i class="fa fa-file-excel text-green-600" style="font-size: 1.5rem;"></i>
        </div>
        <div class="ml-4 flex-grow">
            <div class="text-title font-medium text-gray-700">Excel Confirmación</div>
            <div class="text-sm text-gray-500">Descargar documento</div>
        </div>
            </div>
               <div class="flex gap-2">
            <a href="${excelConfirmacion}" target="_blank" class="download-btn px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
            Descargar
            </a>
        </div>
        </div>
        `);
  } else {
    $("#documentacion-peru-documents")
      .append(`<div class="flex items-center p-4 bg-gray-50 rounded-lg">
      <div class="p-3 bg-red-100 rounded-full">
        <i class="fa fa-file-excel text-red-600" style="font-size: 1.5rem;"></i>
      </div>
      <div class="ml-4 flex-grow">
        <div class="text-title font-medium text-gray-700">Excel Confirmación</div>
        <div class="text-sm text-red-500">Documento no disponible</div>
      </div>
     
    </div>`);
  }
  if (documentosAdicionales) {
    documentosAdicionales.forEach((data) => {
      $("#documentacion-peru-documents").append(`
            <div class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <div class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fa fa-file-text text-green-600" style="font-size: 1.5rem;"></i>
                </div>
            <div class="ml-4 flex-grow">
                <div class="text-title font-medium text-gray-700">${data.name}</div>
                <div class="text-sm text-gray-500">Descargar documento</div>
            </div>
           
            </div>
             <div class="flex gap-2">
                <a href="${data.file_url}" target="_blank" class="download-btn px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                Descargar
                </a>
            </div>
        </div>`);
    });
  }
  $("#documentacion-china").empty();
  if (documentacionChina) {
    documentacionChina.forEach((data) => {
      $("#documentacion-china").append(`
                            <div class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">

                <div class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
        <div class="p-3 bg-green-100 rounded-full">
            <i class="fa fa-file-text text-green-600" style="font-size: 1.5rem;"></i>
        </div>
        <div class="ml-4 flex-grow">
            <div class="text-title font-medium text-gray-700">${data.name}</div>
            <div class="text-sm text-gray-500">Descargar documento</div>
        </div>
       </div>
        <div class="flex gap-2">
            <a href="${data.file_url}" target="_blank" class="download-btn px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
            Descargar
            </a>
        </div>
        </div>`);
    });
  }
  $("#documentacion-inspeccion").empty();
  if (inspeccion) {
    inspeccion.forEach((data) => {
      $("#documentacion-inspeccion").append(`
          <div class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">

                <div class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
        <div class="p-3 bg-green-100 rounded-full">
            <i class="fa fa-file-text text-green-600" style="font-size: 1.5rem;"></i>
        </div>
        <div class="ml-4 flex-grow">
            <div class="text-title font-medium text-gray-700">${data.name}</div>
            <div class="text-sm text-gray-500">Descargar documento</div>
        </div>
       </div>
        <div class="flex gap-2">
            <a href="${data.file_url}" target="_blank" class="download-btn px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
            Descargar
            </a>
        </div>
        </div>`);
    });
  }
}
async function viewClientesDocumentacion(id, nombrecliente = null) {
  if (nombrecliente) {
    $(".name_cliente").text(nombrecliente);
  }
  $(".aditional-file").remove();
  idCotizacion = id;
  clientesContainer.hide();
  url =
    base_url +
    "CargaConsolidada/ContenedorConsolidado/showClientesDocumentacion/" +
    id;
  spinner.show();
  const response = await fetch(url);
  const result = await response.json();
  spinner.hide();
  $("#clientes-documentation-container").show();
  const providers = JSON.parse(result.providers);
  $(".documentos-clientes-tabs").empty();
  $(".documentos-clientes-content").empty();

  providers.forEach((provider) => {
    $(".documentos-clientes-tabs").append(`
        <div class="tab-cliente-documentacion" data-id="${provider.id}">
          ${provider.code_supplier}
        </div>`);
  });

  selectedTabDocumentacionId = !selectedTabDocumentacionId ? providers[0].id : selectedTabDocumentacionId;

  const filesAlmacenDocumentacion = JSON.parse(result.files_almacen_documentacion || "[]");
  const filesAlmacenInspection = JSON.parse(result.files_almacen_inspection || "[]");
  const filesF = JSON.parse(result.files || "[]");
  const filteredFiles = filesAlmacenDocumentacion.filter(
    (file) => file.id_proveedor === selectedTabDocumentacionId
  );
  const filteredFilesInspection = filesAlmacenInspection.filter(
    (file) => file.id_proveedor === selectedTabDocumentacionId
  );
  const filtFiles = filesF.filter(
    (file) => file.id_proveedor === selectedTabDocumentacionId
  );
  // Mostrar los archivos en la interfaz
  const documentosContainer = $("#documentos-china");
  documentosContainer.empty();

  filteredFiles.forEach((file) => {
    if (file.file_url == null) {
      return;
    }
    documentosContainer.append(`
      <div class="file-item">
        <div class="file-info">
          <div class="file-iconic">
            ${getIconByType(file.file_url.split('.').pop().toLowerCase())}
          </div>
          <span class="file-name">${file.folder_name}</span>
          <div class="file-actions">
            <a href="${file.file_url}" target="_blank" class="btn btn-primary">Ver</a>
            <button class="btn btn-danger" onclick="deleteClienteDocumentacionFile(${file.id})">
              <i class="fas fa-trash"></i> Eliminar
            </button>
          </div>
        </div>
      </div>
    `);
  });


  $(".tab-cliente-documentacion").on("click", function () {
    const id = $(this).data("id");
    const provider = providers.find((p) => p.id == id);
    selectedTabDocumentacionId = id;
    $(".tab-cliente-documentacion").removeClass("active");
    $(this).addClass("active");
    originalValorDocumento = provider.valor_doc;
    originalVolumenDocumento = provider.volumen_doc;

    $(".documentos-clientes-content").empty();

    // Filtrar y mostrar archivos para el proveedor seleccionado
    const filteredFiles = filesAlmacenDocumentacion.filter(
      (file) => file.id_proveedor === selectedTabDocumentacionId
    );
    const filteredFilesInspection = filesAlmacenInspection.filter(
      (file) => file.id_proveedor === selectedTabDocumentacionId
    );
    const filteredFilesF = filesF.filter(
      (file) => file.id_proveedor === selectedTabDocumentacionId
    );

    let facturaDiv = "";
    let excelDiv = "";
    let facturaComercial = provider.factura_comercial;
    let excelConfirmacion = provider.excel_confirmacion;
    let packingList = provider.packing_list;
    if (currentPrivilege != "Documentacion") {
      $(".documentos-clientes-content").append(`<div class="flex gap-8">
        <div class="bg-white p-6 rounded-lg shadow-md" style="width:60%">
          <div class="flex items-center gap-2 mb-6
          justify-between">
          <div>
            <h2 class="text-lg">Documentación</h2>
            <i class="far fa-folder-open"></i>
          </div>
              <div id="btn-crear-documentacion-cliente" 
              data-id="${provider.id}"
              class="bg-orange py-2 px-5 border border-transparent rounded text-sm btn-crear-documentacion-cliente" data-type="html">
              Nuevo Documento
              </div>
          </div>

          <form id="form-documentacion" class="space-y-4">
            <div class="flex justify-between align-items-center gap-4">
              <div class="flex align-items-center justify-flex-start gap-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Volumen documento</label>
                <input type="number"
                value="${provider.volumen_doc}"
                onchange="validateVolumenDocumentacion(this.value)"
                id="txt-Vol_Doc" class="w-25 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" name="volumen_doc">
              </div>
              <div class="flex align-items-center justify-flex-start gap-2">
                <label class="block text-sm font-medium text-gray-700 mb-1 w-full">Valor documento</label>
                <div class="relative">
                  <span class="absolute left-3 top-2">$</span>
                  <input type="number"
                  value="${provider.valor_doc}"
                  onchange="validateValorDocumentacion(this.value)"
                  id="txt-Valor_Doc" class="w-75 pl-7 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" name="valor_doc">
                </div>
              </div>
            </div>

            <div class="space-y-4" id="documentos-clientes-documentacion">
           
           
            </div>
          </form>
        </div>

        <!-- Sección de Cotizaciones -->
        <div class="bg-white p-6 rounded-lg shadow-md"
          style="height: 40%;min-height: 300px;">
          <div class="flex items-center gap-2 mb-6">
            <h2 class="text-lg">Cotizaciones</h2>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
              <polyline points="14 2 14 8 20 8" />
              <line x1="16" y1="13" x2="8" y2="13" />
              <line x1="16" y1="17" x2="8" y2="17" />
              <line x1="10" y1="9" x2="8" y2="9" />
            </svg>
          </div>

          <div class="space-y-4">
            <button class="w-full flex items-center justify-between px-4 py-3 bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors" id="cotizacion_file_url">
              <span class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                  <polyline points="7 10 12 15 17 10" />
                  <line x1="12" y1="15" x2="12" y2="3" />
                </svg>
                Descargar cotización inicial
              </span>
            </button>

            <button class="w-full flex items-center justify-between px-4 py-3 bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors" id="cotizacion_final_url">
              <span class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                  <polyline points="7 10 12 15 17 10" />
                  <line x1="12" y1="15" x2="12" y2="3" />
                </svg>
                Descargar cotización final
              </span>
            </button>
          </div>
        </div></div>`);

      $("#cotizacion_file_url").off("click").on("click", function () {
        if (result.cotizacion_file_url) {
          window.open(result.cotizacion_file_url);
        } else {
          Swal.fire("Error", "No hay un enlace disponible para la cotización.", "error");
        }
      });
      $("#cotizacion_final_url").off("click").on("click", function () {
        if (result.cotizacion_final_url) {
          window.open(result.cotizacion_final_url);
        } else {
          Swal.fire("Error", "No hay un enlace disponible para la cotización final.", "error");
        }
      });
      $(".btn-crear-documentacion-cliente").off("click");
      $(".btn-crear-documentacion-cliente").on("click", function () {
        const providerId = $(this).data("id");
        Swal.fire({
          title: "Crear nuevo documento",
          html: `
                <input type="text" id="swal-input1" class="swal2-input" placeholder="Nombre del documento">
                <input type="file" id="swal-input2" class="swal2-file" accept=".pdf, .doc, .docx, .xls, .xlsx, .png, .jpg, .jpeg" placeholder="Selecciona un archivo">
            `,
          showCancelButton: true,
          confirmButtonText: "Subir",
          preConfirm: async () => {
            const name = document.getElementById("swal-input1").value;
            const file = document.getElementById("swal-input2").files[0];
            if (!name || !file) {
              Swal.showValidationMessage("Por favor, completa todos los campos");
              return;
            }
            const formData = new FormData();
            formData.append("name", name);
            formData.append("file", file);
            formData.append("id_cotizacion", idCotizacion);
            formData.append("id_proveedor", providerId);
            const response = await fetch(
              base_url +
              "CargaConsolidada/ContenedorConsolidado/createClienteDocumentacion",
              {
                method: "POST",
                body: formData,
              }
            );

            const result = await response.json();
            console.log(result, "result");
            if (result.status === "success") {
              Swal.fire("¡Documento subido!", result.message, "success");
              console.log(name, "name");
              // Agregar dinámicamente el nuevo documento al DOM
              const newDocument = `
            ${name}
            <div class="col-12 col-sm-12 file-info-container">
              <div class="form-group">
                <div class="file-upload-box">
                  <div class="file-info-box">
                    <div class="file-info">
                      <div class="file-iconic">
                        ${getIconByType(file.name.split('.').pop().toLowerCase())}
                      </div>
                      <span class="file-name">${file.name}</span>
                      <span class="file-size">${(file.size / 1024).toFixed(2)} KB</span>
                   
                    </div>
                  </div>
                </div>
              </div>
            </div>
          `;

              // Validar que el HTML sea válido antes de agregarlo al DOM
              try {
                $("#documentos-clientes-documentacion").append(newDocument);

                // Agregar funcionalidad al botón de borrar

              } catch (error) {
                console.error("Error al procesar el HTML del nuevo documento:", error);
                Swal.fire("Error", "Hubo un problema al agregar el documento al DOM.", "error");
              }
            } else {
              Swal.fire("Error", result.message, "error");
              // Si ocurre un error en el servidor, eliminar el archivo subido
              if (fileInput.dataset.fileId) {
                deleteClienteDocumentacionFile(fileInput.dataset.fileId);
              }

            }
          },
        });
      });
      $(".aditional-file").remove();
      filteredFilesF.forEach((file) => {
        if (!file.file_url) {
          return; // Exit the current function instead of using 'continue'
        }
        $("#form-documentacion").append(`<div class="aditional-file">
           
            ${file.folder_name}
            <div class="col-12 col-sm-12" id="single-file-upload-confirmacion">
                <div class="form-group">
                    <div class="file-upload-box">
                        <div class="file-info-box">
                            <div class="file-info">
                                <div class="file-iconic">
                                    <a href="javascript:void(0)" class="file-icon-link" id="file-icon-link-${file.id}">${getIconByType((file.file_url).split('.').pop().toLowerCase())}</a>
                                </div>
                                <span class="file-name">${file.folder_name}</span>
                                <button class="remove-file-button" onclick="deleteClienteDocumentacionFile(${file.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>`);
        const fileIconLink = $(`#file-icon-link-${file.id}`)[0];
        if (fileIconLink) {
          const fileExtension = (file.file_url).split('.').pop().toLowerCase();
          if (['png', 'jpg', 'jpeg', 'gif', 'bmp', 'webp'].includes(fileExtension)) {
            // Agregar estilo de cursor: pointer
            fileIconLink.style.cursor = 'pointer';

            // Agregar evento de clic para mostrar la vista previa
            fileIconLink.addEventListener('click', (event) => {
              event.preventDefault(); // Evitar comportamiento predeterminado del enlace

              // Mostrar la imagen en el modal
              const modal = document.getElementById('image-modal');
              const modalImage = modal.querySelector('#image-preview');
              modalImage.src = (file.file_url);
              const bootstrapModal = new bootstrap.Modal(modal);
              bootstrapModal.show();
            });
          } else {
            // Si no es una imagen, redirigir al archivo
            fileIconLink.href = (file.file_url);
            fileIconLink.target = '_blank';
          }
        }

      });
    } else {
      // Vista para otros usuarios (como en la imagen)
      $(".documentos-clientes-content").append(`
      <div class="flex col-4 h-25">
        <div class="bg-white rounded-lg shadow-md w-100">
          <div class="p-6" style="border-bottom:solid 2px #DFDFDF;">
            <h2 class="text-md d-flex gap-4">Documentación Perú 
              <img src="https://upload.wikimedia.org/wikipedia/commons/c/cf/Flag_of_Peru.svg" alt="Peru" style="height:20px; margin-top:-5px; border-radius:5px">
            </h2>
          </div>
          <div class="p-6">
            <div class="flex flex-column gap-4 pb-4">
              <p>Volumen Documento: &nbsp ${provider.volumen_doc}</p>
              <p>Valor Documento: &nbsp  $${provider.valor_doc}</p>
            </div>
            <div>
              <p>Factura Comercial</p>
              <div class="flex align-items-center py-2 gap-5">
                ${facturaComercial
          ? `
                    <div class="file-iconic">${getIconByType(facturaComercial.split('.').pop().toLowerCase())}</div>
                    <div>
                      <span class="file-name">${decodeURIComponent(facturaComercial.split('_').pop())}</span>
                    </div>
                  `
          : `
                    <div class="py-2">No hay archivo disponible</div>
                  `}
              </div>

            </div>
            <div>
              <p>Excel Confirmacion</p>
              <div class="flex align-items-center py-2 gap-5">
                ${excelConfirmacion
          ? `
                    <div class="file-iconic">${getIconByType(excelConfirmacion.split('.').pop().toLowerCase())}</div>
                    <div>
                      <span class="file-name">${decodeURIComponent(excelConfirmacion.split('_').pop())}</span>
                    </div>
                  `
          : `
                    <div class="py-2">No hay archivo disponible</div>
                  `}
              </div>
            </div>
            
            <div>

            </div>
          </div>
        </div>
      </div>
      <div class="flex col-4 h-25">
        <div class="bg-white rounded-lg shadow-md w-100">
          <div class="p-6" style="border-bottom:solid 2px #DFDFDF;">
            <h2 class="text-md d-flex gap-4">Documentación China 
              <img src="https://upload.wikimedia.org/wikipedia/commons/f/fa/Flag_of_the_People%27s_Republic_of_China.svg" alt="China" style="height:20px; margin-top:-5px; border-radius:5px">
            </h2>
          </div>
          <div class="p-6">
            <div class="col-12 col-sm-12" id="multiple-file-upload">
                <div class="form-group">
                  
                  <div class="file-lista hidden" id="file-lista-documentacion-documentacion">
                  </div>
                  <span class="invalid-feedback" id="error-volumen">La cotización es requerida</span>
                </div>
            </div
          </div>
        </div>
      </div>
      </div>
      <div class="flex col-4 h-25">
        <div class="bg-white rounded-lg shadow-md w-100">
          <div class="p-6" style="border-bottom:solid 2px #DFDFDF;">
            <h2 class="text-md d-flex gap-4">Inspección
            </h2>
          </div>
          <div class="p-6">
             <div class="col-12 col-sm-12" id="multiple-file-upload">
                <div class="form-group">
                  
                  <div class="file-lista hidden" id="file-lista-documentacion-inspection">
                  </div>
                  <span class="invalid-feedback" id="error-volumen">La cotización es requerida</span>
                </div>
            </div
          </div>
        </div>
      </div>
    `);
      filteredFiles.forEach((file) => {
        addFileToList(file, null, 'file-lista-documentacion-documentacion', true)
      })
      filteredFilesInspection.forEach((file) => {
        addFileToList(file, null, 'file-lista-documentacion-inspection', true)
      })
    }


    if (!facturaComercial) {
      facturaDiv = `
        Factura Comercial
        <div class="col-12 col-sm-12" id="single-file-upload-factura">
            <div class="form-group">
                <div class="file-upload-box">
                    <input type="file" id="file-input-factura" class="file-input" name="file_comercial"
                        accept=".xlsx, .xls, .xlsm, .csv, .xlsb, .xltx, .xlt, .png, .jpg, .jpeg" />
                    <label for="file-input-factura" class="file-label d-flex">
                        <i class="fas fa-upload"></i>
                        <div class="file-group-text">
                            <span class="file-text">Selecciona o arrastra tu archivo aquí</span>
                            <span class="file-format">Formatos: .xlsx, .png, .jpg, .jpeg</span>
                        </div>
                        <button class="upload-button" type="button">Subir archivo</button>
                    </label>
    
                    <!-- Cuadro de información del archivo subido (oculto inicialmente) -->
                    <div class="file-info-box hidden">
                        <div class="file-info">
                            <div class="file-iconic"></div>
                            <span class="file-name"></span>
                            <span class="file-size"></span>
                            <button class="remove-file-button
                            
                            ">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
    } else {
      // Si existe el enlace de la factura, mostrar el contenedor con la información del archivo
      facturaDiv = `
        Factura Comercial
        <div class="col-12 col-sm-12" id="single-file-upload-factura">
            <div class="form-group">
                <div class="file-upload-box">
                    <div class="file-info-box">
                        <div class="file-info">
                            <div class="file-iconic"><a href="javascript:void(0)" class="file-icon-link">${getIconByType(facturaComercial.split('.').pop().toLowerCase())}</a></div>
                            <span class="file-name">Factura Comercial</span>
                            <button class="remove-file-button" onclick="deleteFacturaComercial(${provider.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
    }
    $("#documentos-clientes-documentacion").append(facturaDiv);

    // Agregar funcionalidad de vista previa si es una imagen
    const facturaContainer = document.getElementById('single-file-upload-factura');
    if (facturaContainer) {
      const fileIconLink = facturaContainer.querySelector('.file-icon-link');
      if (fileIconLink) {
        const fileExtension = facturaComercial.split('.').pop().toLowerCase();
        if (['png', 'jpg', 'jpeg', 'gif', 'bmp', 'webp'].includes(fileExtension)) {
          // Agregar estilo de cursor: pointer
          fileIconLink.style.cursor = 'pointer';

          // Agregar evento de clic para mostrar la vista previa
          fileIconLink.addEventListener('click', (event) => {
            event.preventDefault(); // Evitar comportamiento predeterminado del enlace

            // Mostrar la imagen en el modal
            const modal = document.getElementById('image-modal');
            const modalImage = modal.querySelector('#image-preview');
            modalImage.src = facturaComercial;
            const bootstrapModal = new bootstrap.Modal(modal);
            bootstrapModal.show();
          });
        } else {
          // Si no es una imagen, redirigir al archivo
          fileIconLink.href = excelConfirmacion;
          fileIconLink.target = '_blank';
        }
      }
    }
    if (!packingList) {
      packingDiv = `
        Packing List
        <div class="col-12 col-sm-12" id="single-file-upload-packing">
            <div class="form-group">
                <div class="file-upload-box">
                    <input type="file" id="file-input-packing" class="file-input" name="packing_list"
                        accept=".xlsx, .xls, .xlsm, .csv, .xlsb, .xltx, .xlt, .png, .jpg, .jpeg" />
                    <label for="file-input-packing" class="file-label d-flex">
                        <i class="fas fa-upload"></i>
                        <div class="file-group-text">
                            <span class="file-text">Selecciona o arrastra tu archivo aquí</span>
                            <span class="file-format">Formatos: .xlsx, .png, .jpg, .jpeg</span>
                        </div>
                        <button class="upload-button" type="button">Subir archivo</button>
                    </label>
    
                    <!-- Cuadro de información del archivo subido (oculto inicialmente) -->
                    <div class="file-info-box hidden">
                        <div class="file-info">
                            <div class="file-iconic"></div>
                            <span class="file-name"></span>
                            <span class="file-size"></span>
                            <button class="remove-file-button
                            
                            ">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
    } else {
      // Si existe el enlace de la packing, mostrar el contenedor con la información del archivo
      packingDiv = `
        Packing List
        <div class="col-12 col-sm-12" id="single-file-upload-packing">
            <div class="form-group">
                <div class="file-upload-box">
                    <div class="file-info-box">
                        <div class="file-info">
                            <div class="file-iconic"><a href="javascript:void(0)" class="file-icon-link">${getIconByType(packingList.split('.').pop().toLowerCase())}</a></div>
                            <span class="file-name">Packing List</span>
                            <button class="remove-file-button" onclick="deleteFacturaComercial(${provider.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
    }
    $("#documentos-clientes-documentacion").append(packingDiv);

    // Agregar funcionalidad de vista previa si es una imagen
    const packingContainer = document.getElementById('single-file-upload-packing');
    if (packingContainer) {
      const fileIconLink = packingContainer.querySelector('.file-icon-link');
      if (fileIconLink) {
        const fileExtension = packingList.split('.').pop().toLowerCase();
        if (['png', 'jpg', 'jpeg', 'gif', 'bmp', 'webp'].includes(fileExtension)) {
          // Agregar estilo de cursor: pointer
          fileIconLink.style.cursor = 'pointer';

          // Agregar evento de clic para mostrar la vista previa
          fileIconLink.addEventListener('click', (event) => {
            event.preventDefault(); // Evitar comportamiento predeterminado del enlace

            // Mostrar la imagen en el modal
            const modal = document.getElementById('image-modal');
            const modalImage = modal.querySelector('#image-preview');
            modalImage.src = packingList;
            const bootstrapModal = new bootstrap.Modal(modal);
            bootstrapModal.show();
          });
        } else {
          // Si no es una imagen, redirigir al archivo
          fileIconLink.href = packingList;
          fileIconLink.target = '_blank';
        }
      }
    }


    // Repetir el mismo proceso para el Excel de confirmación
    if (!excelConfirmacion) {
      excelDiv = `
        Excel Confirmación
        <div class="col-12 col-sm-12" id="single-file-upload-confirmacion">
            <div class="form-group">
                <div class="file-upload-box">
                    <input type="file" id="file-input-confirmacion" class="file-input" name="excel_confirmacion"
                        accept=".xlsx, .xls, .xlsm, .csv, .xlsb, .xltx, .xlt, .png, .jpg, .jpeg" />
                    <label for="file-input-confirmacion" class="file-label d-flex">
                        <i class="fas fa-upload"></i>
                        <div class="file-group-text">
                            <span class="file-text">Selecciona o arrastra tu archivo aquí</span>
                            <span class="file-format">Formatos: .xlsx, .png, .jpg, .jpeg</span>
                        </div>
                        <button class="upload-button" type="button">Subir archivo</button>
                    </label>
    
                    <!-- Cuadro de información del archivo subido (oculto inicialmente) -->
                    <div class="file-info-box hidden">
                        <div class="file-info">
                            <div class="file-iconic"></div>
                            <span class="file-name"></span>
                            <span class="file-size"></span>
                            <button class="remove-file-button"
                                                    onclick="deleteExcelConfirmacion(${provider.id})"
    
                            >
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
    } else {
      // Si existe el enlace del Excel de confirmación, mostrar el contenedor con la información del archivo
      excelDiv = `
        Excel Confirmación
        <div class="col-12 col-sm-12" id="single-file-upload-confirmacion">
            <div class="form-group">
                <div class="file-upload-box">
                    <div class="file-info-box">
                        <div class="file-info">
                            <div class="file-iconic">
                                <a href="javascript:void(0)" class="file-icon-link">${getIconByType(excelConfirmacion.split('.').pop().toLowerCase())}</a>
                            </div>
                            <span class="file-name">Excel Confirmación</span>
                            <button class="remove-file-button" onclick="deleteExcelConfirmacion(${provider.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
    }

    $("#documentos-clientes-documentacion").append(excelDiv);

    // Agregar funcionalidad de vista previa si es una imagen
    const excelContainer = document.getElementById('single-file-upload-confirmacion');
    console.log(excelContainer, "excelContainer");
    if (excelContainer) {
      const fileIconLink = excelContainer.querySelector('.file-icon-link');
      if (fileIconLink) {
        const fileExtension = excelConfirmacion.split('.').pop().toLowerCase();
        if (['png', 'jpg', 'jpeg', 'gif', 'bmp', 'webp'].includes(fileExtension)) {
          // Agregar estilo de cursor: pointer
          fileIconLink.style.cursor = 'pointer';

          // Agregar evento de clic para mostrar la vista previa
          fileIconLink.addEventListener('click', (event) => {
            event.preventDefault(); // Evitar comportamiento predeterminado del enlace

            // Mostrar la imagen en el modal
            const modal = document.getElementById('image-modal');
            const modalImage = modal.querySelector('#image-preview');
            modalImage.src = excelConfirmacion;
            const bootstrapModal = new bootstrap.Modal(modal);
            bootstrapModal.show();
          });
        } else {
          // Si no es una imagen, redirigir al archivo
          fileIconLink.href = excelConfirmacion;
          fileIconLink.target = '_blank';
        }
      }
    }

    if (!facturaComercial) {
      setupSingleFileUpload(
        "single-file-upload-factura",
        "file-input-factura",
        ["xlsx", "xls", "csv", "xlsb", "xlsm", "jpg", "png", "jpeg"]
      );
    }
    if (!excelConfirmacion) {
      setupSingleFileUpload(
        "single-file-upload-confirmacion",
        "file-input-confirmacion",
        ["xlsx", "xls", "csv", "xlsb", "xlsm", "jpg", "png", "jpeg"]
      );
    }
    if (!packingList) {
      setupSingleFileUpload('single-file-upload-packing', 'file-input-packing', ['xlsx', 'xls', 'csv', 'xlsb', 'xlsm', 'jpg', 'png', 'jpeg']);
    }
    $("#file-input-factura").on("change", function () {
      shouldSaveDocumentacion = true;
    });
    $("#file-input-confirmacion").on("change", function () {
      shouldSaveDocumentacion = true;
    });

    //clean .aditional-file

    // const filesDoc = JSON.parse(result[0].files_almacen_documentacion ?? "[]");
    // const files = JSON.parse(result.files_almacen_documentacion ?? "[]");
    // const filesFilter = files.filter((file) => file.id_proveedor == selectedTabDocumentacionId);

    // //for each file add a col with a link to download and delete icon  in collapse-documentacion
    // filesFilter.forEach((file) => {
    //   if(!file.file_url){
    //     return; // Exit the current function instead of using 'continue'
    //   }
    //   $("#form-documentacion").append(`

    //         ${file.folder_name}
    //         <div class="col-12 col-sm-12" id="single-file-upload-confirmacion">
    //             <div class="form-group">
    //                 <div class="file-upload-box">
    //                     <div class="file-info-box">
    //                         <div class="file-info">
    //                             <div class="file-iconic">
    //                                 <a href="javascript:void(0)" class="file-icon-link" id="file-icon-link-${file.id}">${getIconByType((file.file_url).split('.').pop().toLowerCase())}</a>
    //                             </div>
    //                             <span class="file-name">Excel Confirmación</span>
    //                             <button class="remove-file-button" onclick="deleteClienteDocumentacionFile(${file.id})">
    //                                 <i class="fas fa-trash"></i>
    //                             </button>
    //                         </div>
    //                     </div>
    //                 </div>
    //             </div>
    //         </div>`
    //   );
    //   const fileIconLink = $(`#file-icon-link-${file.id}`)[0];
    //   if (fileIconLink) {
    //     const fileExtension = (file.file_url).split('.').pop().toLowerCase();
    //     if (['png', 'jpg', 'jpeg', 'gif', 'bmp', 'webp'].includes(fileExtension)) {
    //       // Agregar estilo de cursor: pointer
    //       fileIconLink.style.cursor = 'pointer';

    //       // Agregar evento de clic para mostrar la vista previa
    //       fileIconLink.addEventListener('click', (event) => {
    //         event.preventDefault(); // Evitar comportamiento predeterminado del enlace

    //         // Mostrar la imagen en el modal
    //         const modal = document.getElementById('image-modal');
    //         const modalImage = modal.querySelector('#image-preview');
    //         modalImage.src = (file.file_url);
    //         const bootstrapModal = new bootstrap.Modal(modal);
    //         bootstrapModal.show();
    //       });
    //     } else {
    //       // Si no es una imagen, redirigir al archivo
    //       fileIconLink.href = (file.file_url);
    //       fileIconLink.target = '_blank';
    //     }
    //   }

    // });
  });

  $(`.tab-cliente-documentacion[data-id="${selectedTabDocumentacionId}"]`).click();  // $("#txt-Vol_Doc").val(parseFloat(result[0].volumen_doc));
  // $("#txt-Valor_Doc").val(parseFloat(result[0].valor_doc));
  // //set txt-F_Comercial href
  // const facturaComercial = result[0].factura_comercial;
  // const excelConfirmacion = result[0].excel_confirmacion;
  $("#btn-descargar-cotizacion-inicial").attr(
    "href",
    result.cotizacion_file_url
  );


  spinner.hide();
  return;
  filesDoc.forEach((file) => {
    $(".documentos-clientes-documentacion").append(`
            <div class="col-3 aditional-file d-flex flex-column mb-1">
            <label>Documentacion Almacen:${file.folder_name}</label>
            <div class="d-flex flex-row gap-1">
            <div>
                <a href="${file.file_url}" target="_blank" class="btn btn-outline-primary">
                <i class="fa fa-download"></i>
                Descargar
                </a>
               </div>
               
            </div>  
            </div>
            `)
  });


  clientesDocumentacionContainer.show();
  //   fileManagerInspectionCoordinacion = new FileManager({
  //     fileGrid: "#file-grid-inspection-coordinacion",
  //     fileInput: "#file-input-modal-inspection-coordinacion",
  //     uploadBtn: "#btn-upload-inspection-coordinacion",
  //     dragDropContainer: "#drag-drop-container-inspection-coordinacion",
  //     searchInput: "#search-input-inspection-coordinacion",
  //     pendingFileList: "#pending-file-list-inspection-coordinacion",
  //     onFileUpload: (file) =>
  //       uploadFileAlmacenInspection(file, fileManagerInspectionCoordinacion),
  //     // onLoadFiles:()=>getFilesAlmacenInspection(idProveedor,idCotizacion),
  //   });
  spinner.hide();
}
async function validateListEmbarque(id) {
  url =
    base_url +
    "CargaConsolidada/ContenedorConsolidado/validateListEmbarque/" +
    id;
  const response = await fetch(url);
  const result = await response.json();
  if (result.status) {
    //from $(".btn-lista-embarque") remove btn-secondary and add btn-success
    $(".btn-lista-embarque")
      .removeClass("btn-secondary")
      .addClass("btn-success");
  } else {
  }
  // reloadTableClientesVariacion();
}
async function updateVolSelected(idCotizacion, type) {
  //show confirm swal to confirm asign this tarifa for factura general
  Swal.fire({
    title: "¿Estás seguro?",
    text: "Estás a punto de asignar esta tarifa a la factura general",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, asignar",
    cancelButtonText: "No, cancelar",
    reverseButtons: true,
  }).then((result) => {
    if (result.isConfirmed) {
      url =
        base_url + "CargaConsolidada/ContenedorConsolidado/updateVolSelected";
      $.ajax({
        url: url,
        type: "POST",
        data: {
          idCotizacion: idCotizacion,
          type: type,
        },
        success: function (response) {
          const result = JSON.parse(response);
          if (result.status == "success") {
            Swal.fire("Correcto", result.message, "success");
            reloadTableClientesVariacion();
          } else {
            Swal.fire("Error", result.message, "error");
          }
        },
      });
    }
  });
}
async function deleteFacturaComercial(id) {
  event.preventDefault();
  Swal.fire({
    title: "¿Estás seguro?",
    text: "¡No podrás revertir esto!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, eliminarlo",
    cancelButtonText: "No, cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      url =
        base_url +
        "CargaConsolidada/ContenedorConsolidado/deleteFacturaComercial/" +
        id;
      $.ajax({
        url: url,
        type: "GET",
        success: function (response) {
          const result = JSON.parse(response);
          if (result.status == "success") {
            Swal.fire("Eliminado!", result.message, "success");
          } else {
            Swal.fire("Error!", result.message, "error");
          }
          viewClientesDocumentacion(idCotizacion);
        },
      });
    }
  });
}
async function deleteExcelConfirmacion(id) {
  event.preventDefault();
  Swal.fire({
    title: "¿Estás seguro?",
    text: "¡No podrás revertir esto!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, eliminarlo",
    cancelButtonText: "No, cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      url =
        base_url +
        "CargaConsolidada/ContenedorConsolidado/deleteExcelConfirmacion/" +
        id;
      $.ajax({
        url: url,
        type: "GET",
        success: function (response) {
          const result = JSON.parse(response);
          if (result.status == "success") {
            Swal.fire("Eliminado!", result.message, "success");
          } else {
            Swal.fire("Error!", result.message, "error");
          }
          viewClientesDocumentacion(idCotizacion);
        },
      });
    }
  });
}
const deleteClienteDocumentacionFile = (id) => {
  event.preventDefault();
  Swal.fire({
    title: "¿Estás seguro?",
    text: "¡No podrás revertir esto!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, eliminarlo",
    cancelButtonText: "No, cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      url =
        base_url +
        "CargaConsolidada/ContenedorConsolidado/deleteClienteDocumentacionFile/" +
        id;
      $.ajax({
        url: url,
        type: "GET",
        success: function (response) {
          const result = JSON.parse(response);
          if (result.status == "success") {
            Swal.fire("Eliminado!", result.message, "success");
          } else {
            Swal.fire("Error!", result.message, "error");
          }
          viewClientesDocumentacion(idCotizacion);
        },
      });
    }
  });
};
const returnToSteps = () => {
  cotizacionContainer.hide();
  clientesContainer.hide();
  documentationContainer.hide();
  facturaGuiaContainer.hide();
  stepsContainer.show();
};
$(document).ready(async function () {
  $('.dropdown-menu').on('click', function (event) {
    event.stopPropagation(); // Evita que el evento se propague
  });

  // Cierra el menú al hacer clic en "Cancelar" o "Aplicar"
  $('#cancelar-btn, #aplicar-btn').on('click', function () {
    $('#filtros-btn').dropdown('hide'); // Cierra el menú
  });

  // Cierra el menú al hacer clic en el botón "Filtros" si ya está abierto
  $('#filtros-btn').on('click', function (event) {
    if ($(this).attr('aria-expanded') === 'true') {
      $(this).dropdown('hide'); // Cierra el menú si ya está abierto
    }
  });
  console.log("waos", currentPrivilege);

  spinner = $(".backdrop");
  stepsContainer = $("#steps");
  contentHeader = $("#content-header");
  mainContainer = $("#main-container");
  stepsContainer.hide();
  cotizacionContainer = $("#cotizacion-container");
  cotizacionContainer.hide();
  tableCotizacion = $("#table-cotizacion-prospectos");
  tableCotizacionEmbarque = $("#table-cotizacion-embarque");
  tableCotizacionEmbarque.hide();
  clientesContainer = $("#clientes-container");
  clientesContainer.hide();
  tableClientesGeneral = $("#table-clientes-general");
  tableClientesVariacion = $("#table-clientes-variacion");
  tableClientesGeneral.hide();
  tableClientesVariacion.hide();
  clientesDocumentacionContainer = $("#clientes-documentation-container");
  clientesDocumentacionContainer.hide();
  documentationContainer = $("#documentation-container");
  documentationContainer.hide();
  cotizacionInspectionContainer = $("#cotizacion-almacen-inspeccion");
  cotizacionInspectionContainer.hide();
  cotizacionAlmacenContainer = $("#cotizacion-almacen");
  cotizacionAlmacenContainer.hide();
  dropZone = $("#drop-zone");
  fileInput = $("#file-input");
  fileList = $("#file-list");
  cotizacionFinalContainer = $("#cotizacion-final-container");
  cotizacionFinalContainer.hide();
  tableCotizacionFinal = $("#table-cotizacion-final");
  tableCotizacionFinal.hide();
  facturaGuiaContainer = $("#factura-guia-container");
  facturaGuiaContainer.hide();
  tableFacturaGuia = $("#table-factura-guia");
  tableFacturaGuia.hide();
  documentationContainerProfile = $("#documentacion-container");
  documentationContainerProfile.hide();
  documentacionDocumentacionContainer = $(
    "#documentacion-documentacion-container"
  );
  documentacionDocumentacionContainer.hide();
  documentacionAduanaContainer = $("#documentacion-aduana-container");
  documentacionAduanaContainer.hide();

  try {
    $(".export-pdf-main-content").off("click");
    $(".export-pdf-main-content").on("click", function () {
      // Simular clic en el botón de PDF
      console.log("click");
      $("#export-pdf-main").click();
    });
  } catch (error) {
    console.log(error);
  }
  try {
    $(".export-excel-main-content").off("click");
    $(".export-excel-main-content").on("click", function () {
      // Simular clic en el botón de Excel
      console.log("click");
      $("#export-excel-main").click();
    });
  } catch (error) {
    console.log(error);
  }

  //if current windows route includes listarCompletados hide .filter-contenedor 


  if (window.location.href.includes("listarCompletados")) {
    $(".filter-contenedor").hide();

    $("#table-contenedor-completados").show();

    url = base_url + "CargaConsolidada/ContenedorConsolidado/indexCompletados";

    if (currentPrivilege == "Documentacion") {

      $("#table-contenedor").html("");
      table_Entidad = $("#table-contenedor-completados").DataTable({
        dom:
          "<'row'<'col-sm-12 col-md-4'B><'col-sm-12 col-md-7'f><'col-sm-12 col-md-1'>>" +
          "<'row'<'col-sm-12'tr>>" +
          "<'row'<'col-sm-12 col-md-2'l><'col-sm-12 col-md-5'i><'col-sm-12 col-md-5'p>>",
        buttons: [
          {
            extend: "excel",
            text: '<i class="fa fa-file-excel color_icon_excel"></i> Excel',
            titleAttr: "Excel",
            exportOptions: {
              columns: ":visible",
            },
            attr: {
              id: "export-excel-main",
              class: "hidden",
            },
          },
          {
            extend: "pdf",
            text: '<i class="fa fa-file-pdf color_icon_pdf"></i> PDF',
            titleAttr: "PDF",
            exportOptions: {
              columns: ":visible",
            },
            attr: {
              id: "export-pdf-main",
              class: "hidden",
            },
          },
          {
            extend: "colvis",
            text: '<i class="fa fa-ellipsis-v"></i> Columnas',
            titleAttr: "Columnas",
            exportOptions: {
              columns: ":visible",
            },
            attr: {
              class: "hidden",
            },
          },
        ],
        paging: true,
        lengthChange: true,
        searching: false,
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
        },
        ajax: {
          url: url,
          type: "POST",
          dataType: "JSON",
          data: function (data) {
            data.Filtro_Estado = $("#txt-ID_Estado").val();
            data.Fe_Inicio_Carga = $("#txt-Fe_Inicio_Carga").val();
            data.Fe_Fin_Carga = $("#txt-Fe_Fin_Carga").val();
            //if url contains listarCompletados 

          },
          complete: function () {
            $(".width_full").val($("#hidden-sCorrelativoCotizacion").val());
          },
        },
        columnDefs: [
          {
            targets: "no-hidden",
            visible: false,
          },
          {
            className: "text-center",
            targets: "no-sort",
            orderable: false,
          },
          {
            targets: "",
            orderable: false,
          },
        ],
        pageLength: 100, // Mostrar 100 elementos por página
        lengthMenu: [
          [100, 1000, -1],
          [100, 1000, "Todos"],
        ],
      });
    } else {
      $("#table-contenedor-completados").html("");
      table_Entidad = $("#table-contenedor").DataTable({
        dom:
          "<'row'<'col-sm-12 col-md-4'B><'col-sm-12 col-md-7'f><'col-sm-12 col-md-1'>>" +
          "<'row'<'col-sm-12'tr>>" +
          "<'row'<'col-sm-12 col-md-2'l><'col-sm-12 col-md-5'i><'col-sm-12 col-md-5'p>>",
        buttons: [
          {
            extend: "excel",
            text: '<i class="fa fa-file-excel color_icon_excel"></i> Excel',
            titleAttr: "Excel",
            exportOptions: {
              columns: ":visible",
            },
            attr: {
              id: "export-excel-main",
              class: "hidden",
            },
          },
          {
            extend: "pdf",
            text: '<i class="fa fa-file-pdf color_icon_pdf"></i> PDF',
            titleAttr: "PDF",
            exportOptions: {
              columns: ":visible",
            },
            attr: {
              id: "export-pdf-main",
              class: "hidden",
            },
          },
          {
            extend: "colvis",
            text: '<i class="fa fa-ellipsis-v"></i> Columnas',
            titleAttr: "Columnas",
            exportOptions: {
              columns: ":visible",
            },
            attr: {
              class: "hidden",
            },
          },
        ],
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: false,
        info: false,
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
        },
        ajax: {
          url: url,
          type: "POST",
          dataType: "JSON",
          data: function (data) {
            data.Filtro_Estado = $("#txt-ID_Estado").val();
            data.Fe_Inicio_Carga = $("#txt-Fe_Inicio_Carga").val();
            data.Fe_Fin_Carga = $("#txt-Fe_Fin_Carga").val();
            //if url contains listarCompletados 

          },
          complete: function () {
            $(".width_full").val($("#hidden-sCorrelativoCotizacion").val());
            $("#aplicar-btn").off("click");
            $("#aplicar-btn").click(function () {
              table_Entidad.ajax.reload(); // Recargar la tabla sin reiniciar la paginación
            });
          },
        },
        columnDefs: [
          {
            targets: "no-hidden",
            visible: false,
          },
          {
            className: "text-center",
            targets: "no-sort",
            orderable: false,
          },
          {
            targets: "",
            orderable: false,
          },
        ],
        pageLength: 100, // Mostrar 100 elementos por página
        lengthMenu: [
          [100, 1000, -1],
          [100, 1000, "Todos"],
        ],
      });
    }
  } else {
    url = base_url + "CargaConsolidada/ContenedorConsolidado/index";
    table_Entidad = $("#table-contenedor").DataTable({
      dom:
        "<'row'<'col-sm-12 col-md-4'B><'col-sm-12 col-md-7'f><'col-sm-12 col-md-1'>>" +
        "<'row'<'col-sm-12'tr>>" +
        "<'row'<'col-sm-12 col-md-2'l><'col-sm-12 col-md-5'i><'col-sm-12 col-md-5'p>>",
      buttons: [
        {
          extend: "excel",
          text: '<i class="fa fa-file-excel color_icon_excel"></i> Excel',
          titleAttr: "Excel",
          exportOptions: {
            columns: ":visible",
          },
          attr: {
            id: "export-excel-main",
            class: "hidden",
          },
        },
        {
          extend: "pdf",
          text: '<i class="fa fa-file-pdf color_icon_pdf"></i> PDF',
          titleAttr: "PDF",
          exportOptions: {
            columns: ":visible",
          },
          attr: {
            id: "export-pdf-main",
            class: "hidden",
          },
        },
        {
          extend: "colvis",
          text: '<i class="fa fa-ellipsis-v"></i> Columnas',
          titleAttr: "Columnas",
          exportOptions: {
            columns: ":visible",
          },
          attr: {
            class: "hidden",
          },
        },
      ],
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
      },
      ajax: {
        url: url,
        type: "POST",
        dataType: "JSON",
        data: function (data) {
          data.Filtro_Estado = $("#txt-ID_Estado").val();
          data.Fe_Inicio_Carga = $("#txt-Fe_Inicio_Carga").val();
          data.Fe_Fin_Carga = $("#txt-Fe_Fin_Carga").val();
          //if url contains listarCompletados 

        },
        complete: function () {
          $(".width_full").val($("#hidden-sCorrelativoCotizacion").val());
          $("#aplicar-btn").off("click");
          $("#aplicar-btn").click(function () {
            table_Entidad.ajax.reload(); // Recargar la tabla sin reiniciar la paginación
          });
        },
      },
      columnDefs: [
        {
          targets: "no-hidden",
          visible: false,
        },
        {
          className: "text-center",
          targets: "no-sort",
          orderable: false,
        },
        {
          targets: "",
          orderable: false,
        },
      ],
      pageLength: 100, // Mostrar 100 elementos por página
      lengthMenu: [
        [100, 1000, -1],
        [100, 1000, "Todos"],
      ],
    });
    configurarBuscador('table-contenedor', 'search-table', 'table-contenedor_filter');

  }
  //if current windows route includes listarCompletados hide .filter-contenedor




  $("#upload-documents").click(() => $("#upload-input-documents").click());
  $("#upload-inspection").click(() => $("#upload-input-inspection").click());

  // Listeners para subir archivos
  $('#btn-guardar-doc-not').click(async () => {
    try {
      await saveBoth();
      shouldSaveInspection = false;
    }
    catch (error) {
      console.log(error);
    }
  });

  $("#upload-input-documents").change(function () {
    handleFileUpload(this.files, "documents");
  });

  $("#upload-input-inspection").change(function () {
    handleFileUpload(this.files, "inspection");
  });
  $("#btn-back-documentacion-profile").click(() => {
    documentationContainerProfile.hide();
    clientesContainer.show();
  });
  $("#btn-crear-documentacion-documentacion").click(() => {
    //show swall with file name file input category select envio comercial legal and list with all boostrap icons to select
    Swal.fire({
      title: "Crear nueva documentación",
      html: `
              <div class="mb-3">
                <input type="text" id="document-name" class="form-control" placeholder="Nombre del documento" required>
                <div id="document-name-error" class="invalid-feedback">Este campo es obligatorio</div>
              </div>
              <div class="mb-3">
                <input type="file" id="file-input-documentacion" class="form-control" required>
                <div id="file-input-error" class="invalid-feedback">Debe seleccionar un archivo</div>
              </div>
              <div class="mb-3">
                <select id="category" class="form-control" required>
                  <option value="">Seleccione una categoría</option>
                  <option value="ENVIO">Envio</option>
                  <option value="COMERCIAL">Comercial</option>
                  <option value="LEGAL">Legal</option>
                </select>
                <div id="category-error" class="invalid-feedback">Debe seleccionar una categoría</div>
              </div>
              <div class="mb-3">
                <label for="icon-selection">Icono seleccionado: </label>
                <span id="selected-icon"><i class="bi bi-file-earmark-text"></i></span>
                <input type="hidden" id="icon-value" value="file-earmark-text">
              </div>
              <div class="mb-3">
                <button id="show-icons" class="btn btn-outline-secondary btn-sm">Mostrar iconos</button>
                <div id="icons-table-container" style="display: none;">
                  ${generateIconsTable()}
                </div>
              </div>
            `,
      showCancelButton: true,
      confirmButtonText: "Guardar",
      cancelButtonText: "Cancelar",
      preConfirm: () => {
        // Validar todos los campos antes de confirmar
        let isValid = true;

        // Validar nombre del documento
        const documentName = document.getElementById("document-name");
        if (!documentName.value.trim()) {
          documentName.classList.add("is-invalid");
          isValid = false;
        } else {
          documentName.classList.remove("is-invalid");
        }

        // Validar archivo
        const fileInput = document.getElementById("file-input-documentacion");
        if (!fileInput.files || fileInput.files.length === 0) {
          fileInput.classList.add("is-invalid");
          isValid = false;
        } else {
          fileInput.classList.remove("is-invalid");
        }

        // Validar categoría
        const category = document.getElementById("category");
        if (!category.value) {
          category.classList.add("is-invalid");
          isValid = false;
        } else {
          category.classList.remove("is-invalid");
        }

        // Si hay errores, cancelar la confirmación
        if (!isValid) {
          Swal.showValidationMessage(
            "Por favor complete todos los campos obligatorios"
          );
          return false;
        }

        // Devolver los valores para usarlos en el then()
        return {
          documentName: documentName.value,
          file: fileInput.files[0],
          category: category.value,
          icon: document.getElementById("icon-value").value,
        };
      },
      didOpen: () => {
        // Mostrar/ocultar la tabla de iconos
        const showIconsBtn = document.getElementById("show-icons");
        const iconsContainer = document.getElementById("icons-table-container");

        showIconsBtn.addEventListener("click", () => {
          iconsContainer.style.display =
            iconsContainer.style.display === "none" ? "block" : "none";
          showIconsBtn.textContent =
            iconsContainer.style.display === "none"
              ? "Mostrar iconos"
              : "Ocultar iconos";
        });

        // Manejar la selección de iconos
        const iconElements = document.querySelectorAll(".icon-select");
        const selectedIcon = document.getElementById("selected-icon");
        const iconValue = document.getElementById("icon-value");

        iconElements.forEach((icon) => {
          icon.addEventListener("click", () => {
            const iconName = icon.getAttribute("data-icon");
            selectedIcon.innerHTML = `<i class="bi bi-${iconName}"></i>`;
            iconValue.value = iconName;
            iconsContainer.style.display = "none";
            showIconsBtn.textContent = "Mostrar iconos";
          });
        });

        // Limpiar errores al escribir o cambiar valores
        document
          .getElementById("document-name")
          .addEventListener("input", function () {
            this.classList.remove("is-invalid");
          });

        document
          .getElementById("file-input-documentacion")
          .addEventListener("change", function () {
            this.classList.remove("is-invalid");
          });

        document
          .getElementById("category")
          .addEventListener("change", function () {
            this.classList.remove("is-invalid");
          });
      },
    }).then((result) => {
      if (result.isConfirmed) {
        // Usar los valores validados devueltos por preConfirm
        console.log("Nombre del documento:", result.value.documentName);
        console.log("Archivo:", result.value.file);
        console.log("Categoría:", result.value.category);
        console.log("Icono seleccionado:", `bi bi-${result.value.icon}`);
        spinner.show();
        const formData = new FormData();
        formData.append("idContenedor", idContenedor);
        formData.append("name", result.value.documentName);
        formData.append("categoria", result.value.category);
        formData.append("icon", result.value.icon);
        formData.append("file", result.value.file);
        url =
          base_url +
          "CargaConsolidada/ContenedorConsolidado/createDocumentacionFolder";
        $.ajax({
          url,
          type: "POST",
          data: formData,
          contentType: false,
          processData: false,
          success: function (response) {
            const data = JSON.parse(response);
            if (data.status === "success") {
              Swal.fire("Correcto", data.message, "success");
              showDocumentacionDocumentacionContainer(idContenedor);
            } else {
              Swal.fire("Error", data.message, "error");
            }
            spinner.hide();
          },
          error: function () {
            Swal.fire("Error", "Ocurrió un error al subir el archivo", "error");
            spinner.hide();
          },
        });
      }
    });
  });

  function generateIconsTable() {
    // Lista de iconos populares de Bootstrap (puedes agregar más según necesites)
    const bootstrapIcons = [
      "alarm",
      "archive",
      "arrow-left",
      "arrow-right",
      "bag",
      "bell",
      "bookmark",
      "calendar",
      "camera",
      "chat",
      "check",
      "clipboard",
      "cloud",
      "code",
      "cog",
      "collection",
      "credit-card",
      "dash",
      "database",
      "download",
      "envelope",
      "exclamation-circle",
      "eye",
      "file",
      "folder",
      "gear",
      "graph-up",
      "heart",
      "house",
      "image",
      "info-circle",
      "key",
      "laptop",
      "lock",
      "map",
      "mic",
      "music-note",
      "paperclip",
      "pencil",
      "people",
      "person",
      "phone",
      "pie-chart",
      "pin",
      "plus",
      "printer",
      "question-circle",
      "search",
      "shield",
      "shop",
      "star",
      "tags",
      "trash",
      "trophy",
      "upload",
      "wallet",
      "x",
      "zoom-in",
    ];

    // Crear la estructura de la tabla
    let tableHTML =
      '<div style="max-height: 200px; overflow-y: auto; margin-top: 15px;"><table class="table table-bordered">';
    tableHTML += "<tbody>";

    // Número de columnas en la tabla
    const columns = 8;

    // Crear filas y celdas
    for (let i = 0; i < bootstrapIcons.length; i += columns) {
      tableHTML += "<tr>";

      for (let j = 0; j < columns; j++) {
        if (i + j < bootstrapIcons.length) {
          const icon = bootstrapIcons[i + j];
          // Cada celda contiene un icono clickeable
          tableHTML += `<td><i class="bi bi-${icon} icon-select" data-icon="${icon}" style="font-size: 1.2rem; cursor: pointer;"></i></td>`;
        } else {
          tableHTML += "<td></td>";
        }
      }

      tableHTML += "</tr>";
    }

    tableHTML += "</tbody></table></div>";

    return tableHTML;
  }
  // Función para manejar la subida de archivos
  function handleFileUpload(files, section) {
    const formData = new FormData();
    formData.append("idProveedor", currentProveedor);
    url =
      base_url +
      "CargaConsolidada/ContenedorConsolidado/uploadFileAlmacenInspection";

    for (let i = 0; i < files.length; i++) {
      formData.append("files[]", files[i]);
    }

    $.ajax({
      url, // Ruta del backend
      type: "POST",
      data: formData,
      contentType: false,
      processData: false,
      success: function (response) {
        const data = JSON.parse(response);
        if (data.status === "success") {
          addFilesToView(data.data, section);
        } else {
          alert("Error uploading files: " + data.message);
        }
      },
      error: function () {
        alert("An error occurred while uploading files.");
      },
    });
  }

  // Función para agregar archivos a la vista
  function addFilesToView(files, section) {
    const container =
      section === "documents" ? $("#documents") : $("#inspection");

    files.forEach((file) => {
      // const isImage = file.file_ext.match(/image\//); // Verificar si es una imagen
      // Verificar si es una imagen basándonos en la extensión
      const isImage = /\.(png|jpe?g|gif|bmp|webp)$/i.test(file.file_ext);

      const card = $(`
                <div class="file-card">
                    ${isImage
          ? `<img src="${file.file_url}" alt="${file.file_name}">`
          : `<div class="file-placeholder"><i class="bi bi-file-earmark"></i></div>`
        }
                    <div class="file-name">${file.file_name}</div>
                    <div class="actions">
                        <button class="btn btn-sm btn-primary download-btn" data-url="${file.file_url
        }">Download</button>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="${file.id
        }">Delete</button>
                    </div>
                </div>
            `);

      container.append(card);

      // Descargar archivo
      card.find(".download-btn").click(function () {
        const fileUrl = $(this).data("url");
        window.open(fileUrl, "_blank");
      });

      // Eliminar archivo
      // card.find(".delete-btn").click(function () {
      //   const fileId = $(this).data("id");
      //   deleteFile(fileId, card);
      // });
    });
  }
  const fillSelects = async () => {
    $("#txt-ID_Pais").empty();
    $("#txt-Mes").empty();
    await getPaises();
    //APPEND DEFAULT OPTION DISABLED SELECT
    $("#txt-Mes").append(
      '<option value="" disabled selected>Seleccione un mes</option>'
    );
    meses.forEach((mes) => {
      $("#txt-Mes").append(`<option value="${mes.id}">${mes.value}</option>`);
    });
  };
  const getPaises = async () => {
    url = base_url + "CargaConsolidada/ContenedorConsolidado/getPaises";
    const response = await fetch(url);
    const result = await response.json();
    paises = result;

    $("#txt-ID_Pais").append(
      '<option value="" disabled selected>Seleccione un país</option>'
    );
    paises.forEach((pais) => {
      $("#txt-ID_Pais").append(
        `<option value="${pais.ID_Pais}">${pais.No_Pais}</option>`
      );
    });
  };
  await fillSelects();
  /**Start of Listeners */
  //on change file-input-documentacion save documentation and clean file input
  $("#file-input-documentacion").change(async function () {
    await saveDocumentation();
    $(this).val("");
  });
  btnCrear = $("#btn-crear");
  btnCrear.on("click", async function () {
    $("#modal-crear").modal("show");
    $("#btn-guardar").show();
    //do fetch to get data from id
    const validContainers = await fetch(
      base_url + "CargaConsolidada/ContenedorConsolidado/getValidContainers"
    );
    const parsedContainer = await validContainers.json();
    console.log(parsedContainer);
    $("#txt-No_Carga").empty();

    $("#txt-No_Carga").append(
      '<option value="" disabled selected>Seleccione un consolidado</option>'
    );
    // $("#txt-No_Carga").empty();
    parsedContainer.forEach((container) => {
      $("#txt-No_Carga").append(
        `<option value="${container}">Consolidado #${container}</option>`
      );
    });
    //off event listener
    $("#txt-ID_Pais").off("change");
    $("#txt-ID_Pais").on("change", function () {
      if ($(this).val() == 1) {
        $("#txt-Empresa").val("PRO MUNDO COMEX SAC.");
      } else {
        $("#txt-Empresa").val("");
      }
    });
    $("#btn-actualizar").hide();
  });
  btnCrearCotizacion = $("#btn-crear-cotizacion");
  btnCrearCotizacion.on("click", function () {
    $("#modal-crear-cotizacion").modal("show");
    $("#btn-guardar-cotizacion").show();
    $("#btn-actualizar-cotizacion").hide();
  });

  $("#btn-guardar").click(function (e) {
    e.preventDefault();
    const formData = new FormData($("#form-crear")[0]);
    let form = $("#form-crear")[0];
    console.log(form.checkValidity());
    if (!form.checkValidity()) {
      form.classList.add("was-validated");
      return;
    }
    spinner.show();
    $.ajax({
      url: base_url + "CargaConsolidada/ContenedorConsolidado/store",
      type: "POST",
      data: formData,
      contentType: false,
      processData: false,
      success: function (response) {
        const result = JSON.parse(response);
        if (result.status == 1) {
          $("#modal-crear").modal("hide");
          table_Entidad.ajax.reload();
          Swal.fire({
            icon: "success",
            title: "Correcto",
            text: result.message,
          });
        } else {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: result.message,
          });
        }
        spinner.hide();
      },
    });
  });
  $("#btn-back-cliente-documentacion").click(function () {
    clientesDocumentacionContainer.hide();
    clientesContainer.show();
  });
  $(".btn-back-documentacion").click(function () {
    returnToSteps();
  });
  $("#btn-back-cotizacion-almacen").click(async function () {
    if (shouldSaveInspection) {
      //show confirmation swall to exit changes not saved
      const result = await Swal.fire({
        title: "¿Are you sure?",
        text: "You have unsaved changes. Do you want to exit?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, exit",
        cancelButtonText: "No, stay",
      });
      if (result.isConfirmed) {
        cotizacionAlmacenContainer.hide();
        contentHeader.hide();
        cotizacionContainer.show();

      }

    } else {
      cotizacionAlmacenContainer.hide();
      contentHeader.hide();
      cotizacionContainer.show();
    }



  });
  $("#btn-back-factura-guia").click(function () {
    returnToSteps();
  });
  $("#btn-documentacion-zip").click(function () {
    spinner.show();
    url =
      base_url +
      "CargaConsolidada/ContenedorConsolidado/downloadDocumentacionZip/" +
      idContenedor;
    $.ajax({
      url: url,
      type: "GET",
      //data return multipart/form-data
      xhrFields: {
        responseType: "blob",
      },
      success: function (response) {
        spinner.hide();
        const url = window.URL.createObjectURL(new Blob([response]));
        const link = document.createElement("a");
        link.href = url;
        link.setAttribute("download", "documentacion.zip");
        document.body.appendChild(link);
        link.click();
      },
    });
  });
  $("#btn-guardar-cotizacion").off("click");
  $("#btn-guardar-cotizacion").click(function (e) {
    e.preventDefault();
    const formData = new FormData($("#form-crear-cotizacion")[0]);
    formData.append("id_contenedor", idContenedor);
    let form = $("#form-crear-cotizacion")[0];
    if (!form.checkValidity()) {
      form.classList.add("was-validated");
      return;
    }
    spinner.show();
    $.ajax({
      url: base_url + "CargaConsolidada/ContenedorConsolidado/storeCotizacion",
      type: "POST",
      data: formData,
      contentType: false,
      processData: false,
      success: function (response) {
        spinner.hide();

        const result = JSON.parse(response);
        if (result.status == "success") {
          $("#modal-crear-cotizacion").modal("hide");
          if (currentTableCotizacion == "prospectos") {
            tableCotizacion.ajax.reload();
          } else if (currentTableCotizacion == "embarque") {
            tableCotizacionEmbarque.ajax.reload();
          }
          Swal.fire({
            icon: "success",
            title: "Correcto",
            text: result.message,
          });
        } else {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: result.message,
          });
        }
      },
      error: function (error) {
        spinner.hide();
        console.log(error);
      },
    });
  });
  $("#btn-actualizar-cotizacion").click(function (e) {
    e.preventDefault();
    const formData = new FormData($("#form-crear-cotizacion")[0]);
    formData.append("id", idCotizacion);
    let form = $("#form-crear-cotizacion")[0];
    if (!form.checkValidity()) {
      form.classList.add("was-validated");
      return;
    }
    $.ajax({
      url: base_url + "CargaConsolidada/ContenedorConsolidado/updateCotizacion",
      type: "POST",
      data: formData,
      contentType: false,
      processData: false,
      success: function (response) {
        const result = JSON.parse(response);
        if (result.status == "success") {
          $("#modal-crear-cotizacion").modal("hide");
          tableCotizacion.ajax.reload();
          Swal.fire({
            icon: "success",
            title: "Correcto",
            text: result.message,
          });
        } else {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: result.message,
          });
        }
      },
    });
  });
  $("#btn-actualizar").click(function (e) {
    e.preventDefault();
    const formData = new FormData($("#form-crear")[0]);
    formData.append("id", currentCarga);
    //FORMAT DATES TO YYYY-MM-DD

    let form = $("#form-crear")[0];
    if (!form.checkValidity()) {
      form.classList.add("was-validated");
      return;
    }
    spinner.show();
    $.ajax({
      url: base_url + "CargaConsolidada/ContenedorConsolidado/update",
      type: "POST",
      data: formData,
      contentType: false,
      processData: false,
      success: function (response) {
        spinner.hide();
        const result = JSON.parse(response);
        if (result.status == 1) {
          $("#modal-crear").modal("hide");
          table_Entidad.ajax.reload();
          Swal.fire({
            icon: "success",
            title: "Correcto",
            text: result.message,
          });
        } else {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: result.message,
          });
        }
      },
    });
  });
  $("#btn-buscar-carga").click(function (e) {
    e.preventDefault();
    table_Entidad.ajax.reload();
  });

  $(".input-date").datepicker({
    autoclose: true,
    startDate: new Date(fYear, fToday.getMonth(), fDay),
    todayHighlight: true,
    format: "dd/mm/yyyy",
    dateFormat: "dd/mm/yyyy",
  });
  $("#modal-crear-cotizacion").on("hidden.bs.modal", function () {
    $("#form-crear-cotizacion")[0].reset();
  });
  $("#modal-crear").on("hidden.bs.modal", function () {
    $("#form-crear")[0].reset();
  });
  $("#btn-crear-documentacion").click(function (e) {
    e.preventDefault();
    Swal.fire({
      title: "Crear documento",
      html:
        '<input id="swal-input1" class="swal2-input" placeholder="Nombre del documento">' +
        '<input type="file" id="swal-input2" class="swal2-file">',
      focusConfirm: false,
      buttonConfirmText: "Crear",
      //COLOR BUTTONS
      confirmButtonColor: "#e67e22",
      showCancelButton: true,
      preConfirm: () => {
        const name = Swal.getPopup().querySelector("#swal-input1").value;
        const file = Swal.getPopup().querySelector("#swal-input2").files[0];
        if (!name || !file) {
          Swal.showValidationMessage(`Por favor, complete todos los campos`);
        }
        return { name: name, file: file };
      },
    }).then((result) => {
      if (result.isConfirmed) {
        spinner.show();

        const formData = new FormData();
        formData.append("name", result.value.name);
        formData.append("file", result.value.file);
        formData.append("id", idCotizacion);
        formData.append("idProveedor", currentProveedor);
        $.ajax({
          url:
            base_url +
            "CargaConsolidada/ContenedorConsolidado/createClienteDocumentacion",
          type: "POST",
          data: formData,
          contentType: false,
          processData: false,
          success: function (response) {
            spinner.hide();
            const result = JSON.parse(response);
            if (result.status == "success") {
              Swal.fire({
                icon: "success",
                title: "Correcto",
                text: result.message,
              });
              viewClientesDocumentacion(idCotizacion);
            } else {
              Swal.fire({
                icon: "error",
                title: "Error",
                text: result.message,
              });
            }
          },
        });
      }
    });
  });
  $("#btn-guardar-documentacion").click(function (e) {
    e.preventDefault();
    const formData = new FormData($("#form-documentacion")[0]);
    const check = $("#form-documentacion")[0].checkValidity();
    if (!check) {
      $("#form-documentacion")[0].classList.add("was-validated");
      return;
    }
    formData.append("id", idCotizacion);
    formData.append("idProveedor", selectedTabDocumentacionId);
    //f
    $.ajax({
      url:
        base_url +
        "CargaConsolidada/ContenedorConsolidado/updateClienteDocumentacion",
      type: "POST",
      data: formData,
      contentType: false,
      processData: false,
      success: function (response) {
        const result = JSON.parse(response);
        if (result.status == "success") {
          Swal.fire({
            icon: "success",
            title: "Correcto",
            text: result.message,
          });
          viewClientesDocumentacion(idCotizacion);
        } else {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: result.message,
          });
        }
      },
    });
    shouldSaveDocumentacion = false;
  });
  $(".upload-btn").click(() => {
    const file = $("#file-input-modal").prop("files")[0];

    uploadFileDocument(file, fileManager).then((response) => {
      fileManager.data.driveFiles.push(response);
      fileManager.renderFileGrid(fileManager.data.driveFiles);
    });
    //hide modal
    $("#uploadModal").modal("hide");
  });
  $(".upload-btn-inspection").click(function () {
    const file = $("#file-input-modal-inspection").prop("files")[0];
    uploadFileAlmacenInspection(file, fileManagerInspection).then(
      (response) => {
        fileManagerInspection.data.driveFiles.push(response);
        fileManagerInspection.renderFileGrid(
          fileManagerInspection.data.driveFiles
        );
      }
    );
    $("#uploadModalInspection").modal("hide");
  });
  $("#btn-documentacion-factura").click(async function (e) {
    e.preventDefault();
    spinner.show();
    url =
      base_url +
      "CargaConsolidada/ContenedorConsolidado/downloadFacturaComercial/" +
      idContenedor;
    let isError = true;
    await $.ajax({
      url: url,
      type: "GET",
      //data return multipart/form-data
      xhrFields: {
        responseType: "blob",
      },
      success: function (response) {
        isError = false;

        spinner.hide();
        //check if response is a file
        const url = window.URL.createObjectURL(new Blob([response]));
        const link = document.createElement("a");
        link.href = url;
        link.setAttribute("download", "FACTURA_GENERAL.xlsx");
        document.body.appendChild(link);
        link.click();
      },
      error: function (response) {
        isError = true;
        spinner.hide();
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "No se pudo descargar la factura comercial",
        });
      },
    });
    spinner.hide();
    if (isError) {
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "No se pudo descargar la factura General",
      });
    }
  });
  $("#btn-documentacion-new").click(function (e) {
    e.preventDefault();
    Swal.fire({
      title: "Crear documento",
      html:
        '<input id="swal-input1" class="swal2-input" placeholder="Nombre del documento">' +
        '<input type="file" id="swal-input2" class="swal2-file">',
      focusConfirm: false,
      buttonConfirmText: "Crear",
      //COLOR BUTTONS
      confirmButtonColor: "#e67e22",
      showCancelButton: true,
      preConfirm: () => {
        const name = Swal.getPopup().querySelector("#swal-input1").value;
        const file = Swal.getPopup().querySelector("#swal-input2").files[0];
        if (!name || !file) {
          Swal.showValidationMessage(`Por favor, complete todos los campos`);
        }
        return { name: name, file: file };
      },
    }).then((result) => {
      if (result.isConfirmed) {
        spinner.show();

        const formData = new FormData();
        formData.append("name", result.value.name);
        formData.append("file", result.value.file);
        formData.append("idContenedor", idContenedor);
        $.ajax({
          url:
            base_url +
            "CargaConsolidada/ContenedorConsolidado/createDocumentacionFolder",
          type: "POST",
          data: formData,
          contentType: false,
          processData: false,
          success: function (response) {
            spinner.hide();
            const result = JSON.parse(response);
            if (result.status == "success") {
              Swal.fire({
                icon: "success",
                title: "Correcto",
                text: result.message,
              });
              viewDocumentacion();
            } else {
              Swal.fire({
                icon: "error",
                title: "Error",
                text: result.message,
              });
            }
          },
        });
      }
    });
  });
  $("#btn-buscar-clientes-general").click(function (e) {
    e.preventDefault();
    console.log("click");
    tableClientesGeneral.ajax.reload();
  });
  $("#uploadGeneral").click(() => {
    url = base_url + "CargaConsolidada/ContenedorConsolidado/uploadGeneral";
    const formData = new FormData();
    formData.append("idContenedor", idContenedor);
    //swall input file
    Swal.fire({
      title: "Subir Factura General",
      input: "file",
      inputAttributes: {
        accept:
          "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
      },
      showCancelButton: true,
      confirmButtonText: "Subir",
      showLoaderOnConfirm: true,
      preConfirm: async (file) => {
        formData.append("file", file);
        try {
          const response = await fetch(url, {
            method: "POST",
            body: formData,
          });
          if (!response.ok) {
            throw new Error(response.statusText);
          }
          return await response.json();
        } catch (error) {
          Swal.showValidationMessage(`Request failed: ${error}`);
        }
      },
      allowOutsideClick: () => !Swal.isLoading(),
    }).then((result) => {
      if (result.isConfirmed) {
        if (result.value.status == "success") {
          Swal.fire("Correcto", result.value.message, "success");
          tableCotizacionFinal.ajax.reload();
        } else {
          Swal.fire("Error", result.value.message, "error");
        }
      }
    });
  });
  $("#downloadTemplate").click(function (e) {
    e.preventDefault();
    //ajax request to download template blob excel
    url =
      base_url +
      "CargaConsolidada/ContenedorConsolidado/downloadPlantillaGeneral/" +
      idContenedor;
    $.ajax({
      url: url,
      type: "GET",
      //data return multipart/form-data
      xhrFields: {
        responseType: "blob",
      },
      success: function (response) {
        const url = window.URL.createObjectURL(new Blob([response]));
        const link = document.createElement("a");
        link.href = url;
        link.setAttribute("download", "PLANTILLA GENERAL.xlsx");
        document.body.appendChild(link);
        link.click();
      },
    });
  });
  $("#uploadFinal").click(() => {
    url =
      base_url +
      "CargaConsolidada/ContenedorConsolidado/generateMassiveExcelPayrolls";
    const formData = new FormData();
    formData.append("idContenedor", idContenedor);
    //swall input file
    Swal.fire({
      title: "Subir Factura General",
      input: "file",
      inputAttributes: {
        //excel file
        accept:
          "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
      },
      showCancelButton: true,
      confirmButtonText: "Subir",
      showLoaderOnConfirm: true,
      preConfirm: async (file) => {
        formData.append("file", file);
        try {
          const response = await fetch(url, {
            method: "POST",
            body: formData, // Asegúrate de que formData contiene los datos correctos
          });

          const blob = await response.blob();
          const blobUrl = window.URL.createObjectURL(blob);

          // Crear un enlace <a> invisible y simular un clic para descargar
          const a = document.createElement("a");
          a.href = blobUrl;
          a.download = "Cotizaciones Finales.zip"; // Nombre del archivo
          document.body.appendChild(a);
          a.click();

          // Limpiar recursos
          window.URL.revokeObjectURL(blobUrl);
          document.body.removeChild(a);
        } catch (error) {
          console.error("Error al descargar el archivo:", error);
        }
      },
      allowOutsideClick: () => !Swal.isLoading(),
    }).then((result) => {
      Swal.fire("Correcto", result.value.message, "success");
      tableCotizacionFinal.ajax.reload();
    });
  });
  $("#btn-back-cotizacion-final").click(function () {
    cotizacionFinalContainer.hide();
    returnToSteps();
  });
  $("#btn-back-documentacion-documentacion").click(function () {
    documentacionDocumentacionContainer.hide();
    returnToSteps();
  });
  $("#btn-back-documentacion-aduana").click(function () {
    documentacionAduanaContainer.hide();
    returnToSteps();
  });
  /*End of Listeners */
});
/**Sockets Config */
window.addEventListener("load", () => {
  socket.onmessage = function (event) {
    console.log(event);
    const { message, role, action } = JSON.parse(event.data);
    // Aquí puedes manejar los mensajes recibidos del servidor
    try {
      let text = "";
      if (action == "new-container") {
        //confirm swall
        text = "El coordinador registro un nuevo contenedo";
        //check if mainContainer is visible
        if (mainContainer.is(":visible")) {
          text += "¿Desea actualizar?";
          Swal.fire({
            title: "Nuevo contenedor",
            text: text,
            icon: "info",
            showCancelButton: true,
            confirmButtonText: "Si",
            cancelButtonText: "Cerrar",
          }).then((result) => {
            if (result.isConfirmed) {
              table_Entidad.ajax.reload();
            }
          });
        } else {
          //show alert swall
          Swal.fire({
            title: "Nuevo contenedor",
            text: text,
            icon: "info",
            confirmButtonText: "Cerrar",
          });
        }
      }
      if (action == "new-cotizacion") {
        //confirm swall
        text = "El coordinador registro un nuevo prospecto";
        //check if mainContainer is visible
        if (cotizacionContainer.is(":visible")) {
          text += "¿Desea actualizar?";
          Swal.fire({
            title: "Nueva cotización",
            text: text,
            icon: "info",
            showCancelButton: true,
            confirmButtonText: "Si",
            cancelButtonText: "Cerrar",
          }).then((result) => {
            if (result.isConfirmed) {
              tableCotizacion.ajax.reload();
            }
          });
        } else {
          //show alert swall
          Swal.fire({
            title: "Nueva cotización",
            text: text,
            icon: "info",
            confirmButtonText: "Cerrar",
          });
        }
      }
      if (action == "cambio-estado-proveedor") {
        if ($("#table-cotizacion-embarque").is(":visible")) {
          text = "Actualización de estado de proveedor";
          text += "¿Desea actualizar?";
          Swal.fire({
            title: "Cambio de estado",
            text: message,
            icon: "info",
            showCancelButton: true,
            confirmButtonText: "Si",
            cancelButtonText: "Cerrar",
          }).then((result) => {
            if (result.isConfirmed) {
              tableCotizacionEmbarque.ajax.reload();
            }
          });
        } else {
          //show alert swall
          Swal.fire({
            title: "Cambio de estado",
            text: message,
            icon: "info",
            confirmButtonText: "Cerrar",
          });
        }
      }
      //show message swall
    } catch (e) {
      console.log(e);
    }
  };
});

function getIconByType(typeOrExtension) {
  const icons = {
    pdf: `
      <svg width="40px" height="40px" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><defs><linearGradient id="a" x1="625.787" y1="825.641" x2="632.847" y2="812.848" gradientTransform="translate(-610.232 -803.285) rotate(0.063)" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#ffffff"/><stop offset="1" stop-color="#e1e1e1"/></linearGradient><linearGradient id="b" x1="634.081" y1="810.251" x2="635.169" y2="809.248" gradientTransform="translate(-610.524 -802.52)" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#ffffff"/><stop offset="1" stop-color="#c8c8c8"/></linearGradient><linearGradient id="c" x1="14.019" y1="-116.816" x2="10.665" y2="-106.493" gradientTransform="matrix(1, 0, 0, -1, 0.04, -103.785)" gradientUnits="userSpaceOnUse"><stop offset="0.127" stop-color="#8a0000"/><stop offset="0.244" stop-color="#900000" stop-opacity="0.999"/><stop offset="0.398" stop-color="#a00000" stop-opacity="0.999"/><stop offset="0.573" stop-color="#bc0000" stop-opacity="0.998"/><stop offset="0.761" stop-color="#e20000" stop-opacity="0.997"/><stop offset="0.867" stop-color="#fa0000" stop-opacity="0.996"/></linearGradient><linearGradient id="d" x1="14.16" y1="-117.225" x2="10.541" y2="-106.084" gradientTransform="matrix(1, 0, 0, -1, 0.04, -103.785)" gradientUnits="userSpaceOnUse"><stop offset="0.315" stop-color="#5e0000"/><stop offset="0.444" stop-color="#830000" stop-opacity="0.999"/><stop offset="0.618" stop-color="#ae0000" stop-opacity="0.998"/><stop offset="0.775" stop-color="#cd0000" stop-opacity="0.997"/><stop offset="0.908" stop-color="#e00000" stop-opacity="0.996"/><stop offset="1" stop-color="#e70000" stop-opacity="0.996"/></linearGradient></defs><title>file_type_pdf</title><image width="490" height="641" transform="translate(8.426 2.792) scale(0.042)" xlink:href="data:image/png;base64,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gA4INgB0QLABoAOCDQAdEGwA6IBgA0AHBBsAOiDYANABwQaADgg2AHRAsAGgA4INAB0QbADogGADQAcEGwA6INgA0AHBBoAOCDYAdECwAaADgg0AHRBsAOiAYANABwQbADog2ADQAcEGgA4INgB0QLABoAOCDQAdEGwA6IBgA0AHBBsAOiDYANABwQaADgg2AHRAsAGgA4INAB0QbADogGADQAcEGwA6INgA0AHBBoAOCDYAdECwAaADgg0AHRBsAOiAYANABwQbADog2ADQAcEGgA4INgB0QLABoAOCDQAdEGwA6IBgA0AHBBsAOiDYANABwQaADgg2AHRAsAGgA4INAB0QbADogGADQAcEGwA6INgA0AHBBoAOCDYAdECwAaADgg0AHRBsAOiAYANABwQbADog2ADQAcEGgA4INgB0QLABoAOCDQAdEGwA6IBgA0AHBBsAOiDYANABwQaADgg2AHRAsAGgA4INAB0QbADogGADQAcEGwA6INgA0AHBBoAOCDYAdECwAaADgg0AHRBsAOiAYANABwQbADog2ADQAcEGgA4INgB0QLABoAOCDQAdEGwA6IBgA0AHBBsAOiDYANABwQaADgg2AHRAsAGgA4INAB0QbADogGADQAcEGwA6INgA0AHBBoAOCDYAdECwAaADgg0AHRBsAOiAYANABwQbADog2ADQAcEGgA4INgB0QLABoAOCDQAdEGwA6IBgA0AHBBsAOiDYANABwQaADgg2AHRAsAGgA4INAB0QbADogGADQAcEGwA6INgA0AHBBoAOCDYAdECwAaADgg0AHRBsAOiAYANABwQbADog2ADQAcEGgA4INgB0QLABoAOCDQAdEGwA6IBgA0AHBBsAOiDYANABwQaADgg2AHRAsAGgA4INAB0QbADogGADQAcEGwA6INgA0AHBBoAOCDYAdECwAaADgg0AHRBsAOiAYANABwQbADog2ADQAcEGgA4INgB0QLABoAOCDQAdEGwA6IBgA0AHBBsAOiDYANABwQaADgg2AHRAsAGgA4INAB0QbADogGADQAcEGwA6INgA0AHBBoAOCDYAdECwAaADgg0AHRBsAOiAYANABwQbADog2ADQAcEGgA4INgB0QLABoAOCDQAdEGwA6IBgA0AHBBsAOiDYANABwQaADgg2AHRAsAGgA4INAB0QbADogGADQAcE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" style="opacity:0.75;isolation:isolate"/><path d="M9.064,3.162h11.6A31.459,31.459,0,0,1,28.188,10.7V28.542H9.064Z" style="fill:url(#a)"/><path d="M9.064,3.162h11.6A31.459,31.459,0,0,1,28.188,10.7V28.542H9.064Z" style="fill:none;stroke:#c8c8c8;stroke-width:0.5px"/><image width="213" height="212" transform="translate(20.01 2.5) scale(0.041 0.042)" xlink:href="data:image/png;base64,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" style="opacity:0.75;isolation:isolate"/><path d="M20.662,3.162A31.807,31.807,0,0,1,28.188,10.7a6.765,6.765,0,0,0-5.332-2.03A6.025,6.025,0,0,0,20.662,3.162Z" style="fill:url(#b)"/><path d="M20.662,3.162A31.807,31.807,0,0,1,28.188,10.7a6.765,6.765,0,0,0-5.332-2.03A6.025,6.025,0,0,0,20.662,3.162Z" style="fill:none;stroke:#c8c8c8;stroke-width:0.5px"/><rect x="5.339" y="6.496" width="14.1" height="2.7" style="fill:none;stroke:#c8c8c8;stroke-width:4px"/><path d="M15.819,19.855c.466-.914,1-1.943,1.42-2.977h0l.168-.408c-.554-2.108-.886-3.8-.589-4.894h0a.755.755,0,0,1,.763-.458h0l.215,0h.039c.484-.007.711.608.737.847h0a3.847,3.847,0,0,1-.141,1.072h0a2.639,2.639,0,0,0-.161-1.091h0c-.2-.439-.391-.7-.562-.743h0a.54.54,0,0,0-.2.407h0a5.874,5.874,0,0,0-.077.939h0a10.511,10.511,0,0,0,.433,2.729h0c.054-.156.1-.306.14-.447h0c.059-.222.433-1.691.433-1.691h0s-.094,1.956-.226,2.547h0c-.028.125-.059.249-.092.375h0a8.586,8.586,0,0,0,2.145,3.351h0a6.7,6.7,0,0,0,1.24.852h0a16.9,16.9,0,0,1,2.517-.189h0a3.153,3.153,0,0,1,1.938.433h0a.738.738,0,0,1,.213.484h0a1.446,1.446,0,0,1-.041.282h0c.01-.051.01-.3-.755-.546h0a8.91,8.91,0,0,0-3.086-.043h0c1.566.766,3.093,1.147,3.576.919h0a1.015,1.015,0,0,0,.262-.254h0a2.727,2.727,0,0,1-.146.484h0a.764.764,0,0,1-.377.258h0c-.764.2-2.752-.268-4.485-1.258h0a36.619,36.619,0,0,0-5.768,1.371h0c-1.675,2.936-2.935,4.284-3.959,3.771h0l-.377-.189a.436.436,0,0,1-.141-.474h0c.119-.584.852-1.465,2.324-2.344h0c.158-.1.864-.469.864-.469h0s-.523.506-.645.605h0c-1.175.963-2.042,2.174-2.021,2.644h0l0,.041c1-.142,2.495-2.174,4.419-5.939m.61.312c-.321.605-.636,1.166-.926,1.682h0a24.582,24.582,0,0,1,4.975-1.408h0c-.221-.153-.435-.314-.637-.485h0a8.531,8.531,0,0,1-2.1-2.729h0a23.388,23.388,0,0,1-1.317,2.94" style="fill:#f91d0a"/><image width="445" height="171" transform="translate(3.157 4.439) scale(0.042 0.041)" xlink:href="data:image/png;base64,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" style="opacity:0.30000000000000004;isolation:isolate"/><rect x="3.75" y="4.968" width="17.264" height="5.803" style="fill:url(#c)"/><path d="M21.343,11.119H3.437V4.62H21.343ZM20.7,5.264H4.081v5.209H20.7Z" style="fill:url(#d)"/><path d="M8.262,5.819H9.518a1.1,1.1,0,0,1,.859.331,1.338,1.338,0,0,1,.3.937,1.351,1.351,0,0,1-.3.942,1.1,1.1,0,0,1-.859.328h-.5V9.706H8.262V5.819m.757.726V7.631h.419a.423.423,0,0,0,.34-.141.611.611,0,0,0,.12-.4.6.6,0,0,0-.12-.4.422.422,0,0,0-.34-.141H9.019m2.949.031V8.949h.271a.853.853,0,0,0,.708-.3,1.382,1.382,0,0,0,.246-.885,1.375,1.375,0,0,0-.244-.88.858.858,0,0,0-.71-.3h-.271m-.757-.758h.8A2.9,2.9,0,0,1,13,5.947a1.283,1.283,0,0,1,.562.427,1.779,1.779,0,0,1,.307.607,2.783,2.783,0,0,1,.1.779,2.831,2.831,0,0,1-.1.786,1.779,1.779,0,0,1-.307.607,1.313,1.313,0,0,1-.566.43,2.965,2.965,0,0,1-.991.125h-.8V5.819m3.342,0H16.6v.758H15.31V7.3h1.209v.758H15.31V9.706h-.757V5.819" style="fill:#fff9f9"/></svg>
    `,
    word: `
      <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="40" height="40" viewBox="0 0 48 48">
        <linearGradient id="Q7XamDf1hnh~bz~vAO7C6a_pGHcje298xSl_gr1" x1="28" x2="28" y1="14.966" y2="6.45" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#42a3f2"></stop><stop offset="1" stop-color="#42a4eb"></stop></linearGradient><path fill="url(#Q7XamDf1hnh~bz~vAO7C6a_pGHcje298xSl_gr1)" d="M42,6H14c-1.105,0-2,0.895-2,2v7.003h32V8C44,6.895,43.105,6,42,6z"></path><linearGradient id="Q7XamDf1hnh~bz~vAO7C6b_pGHcje298xSl_gr2" x1="28" x2="28" y1="42" y2="33.054" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#11408a"></stop><stop offset="1" stop-color="#103f8f"></stop></linearGradient><path fill="url(#Q7XamDf1hnh~bz~vAO7C6b_pGHcje298xSl_gr2)" d="M12,33.054V40c0,1.105,0.895,2,2,2h28c1.105,0,2-0.895,2-2v-6.946H12z"></path><linearGradient id="Q7XamDf1hnh~bz~vAO7C6c_pGHcje298xSl_gr3" x1="28" x2="28" y1="-15.46" y2="-15.521" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#3079d6"></stop><stop offset="1" stop-color="#297cd2"></stop></linearGradient><path fill="url(#Q7XamDf1hnh~bz~vAO7C6c_pGHcje298xSl_gr3)" d="M12,15.003h32v9.002H12V15.003z"></path><linearGradient id="Q7XamDf1hnh~bz~vAO7C6d_pGHcje298xSl_gr4" x1="12" x2="44" y1="28.53" y2="28.53" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#1d59b3"></stop><stop offset="1" stop-color="#195bbc"></stop></linearGradient><path fill="url(#Q7XamDf1hnh~bz~vAO7C6d_pGHcje298xSl_gr4)" d="M12,24.005h32v9.05H12V24.005z"></path><path d="M22.319,13H12v24h10.319C24.352,37,26,35.352,26,33.319V16.681C26,14.648,24.352,13,22.319,13z" opacity=".05"></path><path d="M22.213,36H12V13.333h10.213c1.724,0,3.121,1.397,3.121,3.121v16.425	C25.333,34.603,23.936,36,22.213,36z" opacity=".07"></path><path d="M22.106,35H12V13.667h10.106c1.414,0,2.56,1.146,2.56,2.56V32.44C24.667,33.854,23.52,35,22.106,35z" opacity=".09"></path><linearGradient id="Q7XamDf1hnh~bz~vAO7C6e_pGHcje298xSl_gr5" x1="4.744" x2="23.494" y1="14.744" y2="33.493" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#256ac2"></stop><stop offset="1" stop-color="#1247ad"></stop></linearGradient><path fill="url(#Q7XamDf1hnh~bz~vAO7C6e_pGHcje298xSl_gr5)" d="M22,34H6c-1.105,0-2-0.895-2-2V16c0-1.105,0.895-2,2-2h16c1.105,0,2,0.895,2,2v16	C24,33.105,23.105,34,22,34z"></path><path fill="#fff" d="M18.403,19l-1.546,7.264L15.144,19h-2.187l-1.767,7.489L9.597,19H7.641l2.344,10h2.352l1.713-7.689	L15.764,29h2.251l2.344-10H18.403z"></path>
      </svg>
    `,
    excel: `
      <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="40" height="40" viewBox="0 0 48 48">
        <path fill="#169154" d="M29,6H15.744C14.781,6,14,6.781,14,7.744v7.259h15V6z"></path><path fill="#18482a" d="M14,33.054v7.202C14,41.219,14.781,42,15.743,42H29v-8.946H14z"></path><path fill="#0c8045" d="M14 15.003H29V24.005000000000003H14z"></path><path fill="#17472a" d="M14 24.005H29V33.055H14z"></path><g><path fill="#29c27f" d="M42.256,6H29v9.003h15V7.744C44,6.781,43.219,6,42.256,6z"></path><path fill="#27663f" d="M29,33.054V42h13.257C43.219,42,44,41.219,44,40.257v-7.202H29z"></path><path fill="#19ac65" d="M29 15.003H44V24.005000000000003H29z"></path><path fill="#129652" d="M29 24.005H44V33.055H29z"></path></g><path fill="#0c7238" d="M22.319,34H5.681C4.753,34,4,33.247,4,32.319V15.681C4,14.753,4.753,14,5.681,14h16.638 C23.247,14,24,14.753,24,15.681v16.638C24,33.247,23.247,34,22.319,34z"></path><path fill="#fff" d="M9.807 19L12.193 19 14.129 22.754 16.175 19 18.404 19 15.333 24 18.474 29 16.123 29 14.013 25.07 11.912 29 9.526 29 12.719 23.982z"></path>
      </svg>
    `,
    image: `
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-image">
        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
        <circle cx="8.5" cy="8.5" r="1.5"></circle>
        <polyline points="21 15 16 10 5 21"></polyline>
      </svg>
    `,
    video: `
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-video">
        <polygon points="23 7 16 12 23 17 23 7"></polygon>
        <rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect>
      </svg>
    `,
    default: `
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file">
        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
        <polyline points="14 2 14 8 20 8"></polyline>
      </svg>
    `,

  };

  const mimeToIcon = {
    'application/pdf': 'pdf',
    'application/msword': 'word',
    'doc': 'word',
    'docx': 'word',
    'pdf': 'pdf',
    'xls': 'excel',
    'xlsx': 'excel',
    'xlsb': 'excel',
    'xlsm': 'excel',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document': 'word',
    'application/vnd.ms-excel': 'excel',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': 'excel',
    'application/vnd.ms-excel.sheet.binary.macroenabled.12': 'excel', // Tipo MIME para .xlsb
    'application/vnd.ms-excel.sheet.macroenabled.12': 'excel', // Tipo MIME para .xlsm
    'image/png': 'image',
    'image/jpeg': 'image',
    'jpg': 'image',
    'jpeg': 'image',
    'png': 'image',
    'image/gif': 'image',
    'video/mp4': 'video',
  };
  // Normalizar el tipo o extensión
  const normalizedType = typeOrExtension.toLowerCase();

  // Si el tipo es un MIME, buscar en mimeToIcon
  if (mimeToIcon[normalizedType]) {
    return icons[mimeToIcon[normalizedType]];
  }

  // Si el tipo es una extensión, buscar directamente en icons
  if (icons[normalizedType]) {
    return icons[normalizedType];
  }

  // Si no se encuentra, devolver el ícono por defecto
  return icons.default;
}
async function showObservaciones(id) {
  spinner.show();
  url = base_url + "CargaConsolidada/ContenedorConsolidado/getObservaciones/" + id;
  const response = await fetch(url, {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json'
    }
  });
  const data = await response.json();
  const files = JSON.parse(data.data.files);
  const observaciones = data.data.observaciones;
  spinner.hide();
  //SHOW SWALL WITH DATA
  if (data.status) {
    const html = `<div class="col-12 col-sm-12" id="multiple-file-upload-aduana">
                  <div class="form-group">
                    <!--TEXT AREA-->
                    <textarea class="form-control" id="observaciones" rows="3" readonly>${observaciones}</textarea>
                    
                    <div class="file-lista" id="file-lista-observaciones">
                    </div>
                    <span class="invalid-feedback" id="error-volumen">La cotización es requerida</span>
                  </div>
                </div>`
    Swal.fire({
      title: 'Observaciones',
      html: html,

      showCloseButton: false,
      showCancelButton: true,
      showConfirmButton: false,

      focusConfirm: false,
      confirmButtonText: 'Cerrar',
      confirmButtonAriaLabel: 'Cerrar'
    });
  }
  files.forEach((file) => {
    addFileToList(file, null, 'file-lista-observaciones', true);
  });

}
function setupSingleFileUpload(containerId, inputId, allowedFileTypes = [], selectInputId = '.upload-button', automaticUpload = false) {
  try {
    const container = document.getElementById(containerId);
    const fileInput = $(`#${inputId}`)[0];
    const fileLabel = container.querySelector('.file-label');
    const fileInfoBox = container.querySelector('.file-info-box');
    const fileNameElement = container.querySelector('.file-name');
    const fileSizeElement = container.querySelector('.file-size');
    const fileIconElement = container.querySelector('.file-iconic');
    const removeFileButton = container.querySelector('.remove-file-button');
    const selectFileButton = container.querySelector(selectInputId);

    if (selectFileButton) {
      // Abrir el diálogo de selección de archivos al hacer clic en el botón
      selectFileButton.addEventListener('click', (e) => {
        e.preventDefault();
        fileInput.click();
      });
    }

    // Mostrar la información del archivo seleccionado
    fileInput.addEventListener('change', (e) => {
      if (fileInput.files.length > 0) {
        const file = fileInput.files[0];
        const fileExtension = file.name.split('.').pop().toLowerCase(); // Obtener la extensión del archivo
        const fileType = file.type; // Tipo MIME completo

        // Verificar si el archivo está en la lista de tipos permitidos
        if (!allowedFileTypes.includes(fileType) && !allowedFileTypes.includes(fileExtension)) {
          alert(`El archivo "${file.name}" no está permitido. Solo se permiten: ${allowedFileTypes.join(', ')}`);
          fileInput.value = ""; // Limpia el input
          return;
        }

        // Obtener el ícono correspondiente
        const fileIcon = getIconByType(fileType || fileExtension);
        console.log('Ícono obtenido:', fileIcon); // Depuración

        // Mostrar el cuadro de información del archivo
        fileInfoBox.classList.remove('hidden');
        // Mostrar el ícono, nombre y tamaño del archivo
        if (fileIconElement) {
          fileIconElement.innerHTML = fileIcon; // Asegúrate de que el elemento exista
        } else {
          console.warn('El elemento .file-iconic no se encontró en el contenedor.');
        }
        fileNameElement.textContent = file.name;
        fileSizeElement.textContent = `${(file.size / 1024).toFixed(2)} KB`;

        // Agregar funcionalidad de vista previa para imágenes y videos
        if (fileType.startsWith('image/') || fileType.startsWith('video/')) {
          fileIconElement.style.cursor = 'pointer';
          fileIconElement.addEventListener('click', () => {
            if (fileType.startsWith('image/')) {
              // Mostrar la imagen en el modal
              const modal = document.getElementById('image-modal');
              const modalImage = modal.querySelector('#image-preview');
              modalImage.src = URL.createObjectURL(file);
              const bootstrapModal = new bootstrap.Modal(modal);
              bootstrapModal.show();
            } else if (fileType.startsWith('video/')) {
              // Mostrar el video en el modal
              const modal = document.getElementById('video-modal');
              const modalVideo = modal.querySelector('#video-preview');
              modalVideo.src = URL.createObjectURL(file);
              modalVideo.load(); // Cargar el video
              const bootstrapModal = new bootstrap.Modal(modal);
              bootstrapModal.show();

              // Detener el video cuando se haga clic fuera del modal
              modal.addEventListener('click', (e) => {
                if (e.target === modal) { // Verifica si el clic fue en el fondo del modal
                  modalVideo.pause(); // Pausar el video
                  modalVideo.currentTime = 0; // Reiniciar el video al inicio
                  bootstrapModal.hide(); // Cerrar el modal
                }
              });
            }
          });
        }
      } else {
        fileInfoBox.classList.add('hidden'); // Ocultar el cuadro de información
      }
    });

    // Manejar el botón de tacho de basura para quitar el archivo
    if (removeFileButton) {
      removeFileButton.addEventListener('click', (e) => {
        e.preventDefault();
        fileInput.value = ""; // Limpia el input
        fileInfoBox.classList.add('hidden'); // Oculta el cuadro de información
      });
    }


    // Manejar el arrastre de archivos
    fileLabel.addEventListener('dragover', (e) => {
      e.preventDefault();
      fileLabel.style.borderColor = '#007bff';
    });

    fileLabel.addEventListener('dragleave', (e) => {
      e.preventDefault();
      fileLabel.style.borderColor = '#cccccc';
    });

    fileLabel.addEventListener('drop', (e) => {
      e.preventDefault();
      fileLabel.style.borderColor = '#cccccc';
      if (e.dataTransfer.files.length > 0) {
        const file = e.dataTransfer.files[0];
        const fileExtension = file.name.split('.').pop().toLowerCase();

        // Verificar si el archivo está en la lista de tipos permitidos
        if (!allowedFileTypes.includes(fileExtension)) {
          alert(`El archivo "${file.name}" no está permitido. Solo se permiten: ${allowedFileTypes.join(', ')}`);
          return;
        }

        fileInput.files = e.dataTransfer.files; // Asigna el archivo arrastrado al input

        // Mostrar la información del archivo
        fileInput.dispatchEvent(new Event('change'));
      }
    });

    // Restablecer el estado del cuadro de información cuando el modal se oculta
    $('#modal-crear-cotizacion').on('hidden.bs.modal', function () {
      fileInput.value = ""; // Limpia el input
      fileInfoBox.classList.add('hidden'); // Oculta el cuadro de información
    });

  } catch (e) {
    console.log(e);
  }
}

// Funcion para subir archivos multiples

function setupMultiFileUpload(containerId, inputId, allowedFileTypes = [], automaticUpload = false) {
  const container = document.getElementById(containerId);
  const fileInput = $(`#${inputId}`)[0];
  const fileLabel = container.querySelector('.file-label');
  const fileList = container.querySelector('.file-lista');
  const uploadButton = container.querySelector('.upload-button');
  const removeFileButton = container.querySelector('.remove-file-button');

  // Abrir el diálogo de selección de archivos al hacer clic en el botón
  uploadButton.addEventListener('click', (e) => {
    e.preventDefault();
    fileInput.click();
  });

  // Mostrar la lista de archivos seleccionados
  fileInput.addEventListener('change', (e) => {
    if (fileInput.files.length > 0) {
      fileList.classList.remove('hidden');

      // Recorrer los archivos seleccionados
      Array.from(fileInput.files).forEach((file, index) => {
        const fileType = file.type; // Tipo MIME completo
        const fileExtension = file.name.split('.').pop().toLowerCase(); // Obtener la extensión del archivo desde el nombre

        // Verificar si el archivo está en la lista de tipos permitidos
        if (!allowedFileTypes.includes(fileType) && !allowedFileTypes.includes(fileExtension)) {
          alert(`El archivo "${file.name}" no está permitido.`);
          return;
        }

        // Determinar el ícono correspondiente usando getIconByType
        const fileIcon = getIconByType(fileType || fileExtension);

        // Crear un elemento de lista para cada archivo
        const fileItem = document.createElement('div');
        fileItem.classList.add('file-list-item');

        // Mostrar el nombre y el tamaño del archivo
        fileItem.innerHTML = `
          <div class="file-icon-container"> 
            <div class="file-icon" style="cursor: pointer;">${fileIcon}</div>
            <p>${file.name} (${(file.size / 1024).toFixed(2)} KB)</p>
            <div class="remove-file-button" data-index="${index}">
              <i class="fas fa-trash"></i>
            </div>
          </div>
        `;

        // Agregar el elemento a la lista
        fileList.appendChild(fileItem);

        // Agregar funcionalidad de vista previa para imágenes y videos
        if (fileType.startsWith('image/') || fileType.startsWith('video/')) {
          const fileIconElement = fileItem.querySelector('.file-icon');
          fileIconElement.style.cursor = 'pointer';
          fileIconElement.addEventListener('click', () => {
            if (fileType.startsWith('image/')) {
              // Mostrar la imagen en el modal
              const modal = document.getElementById('image-modal');
              const modalImage = modal.querySelector('#image-preview');
              modalImage.src = URL.createObjectURL(file);
              const bootstrapModal = new bootstrap.Modal(modal);
              bootstrapModal.show();
            } else if (fileType.startsWith('video/')) {
              // Mostrar el video en el modal
              const modal = document.getElementById('video-modal');
              const modalVideo = modal.querySelector('#video-preview');
              modalVideo.src = URL.createObjectURL(file);
              modalVideo.load(); // Cargar el video
              const bootstrapModal = new bootstrap.Modal(modal);
              bootstrapModal.show();

              // Detener el video cuando se haga clic fuera del modal
              modal.addEventListener('click', (e) => {
                if (e.target === modal) { // Verifica si el clic fue en el fondo del modal
                  modalVideo.pause(); // Pausar el video
                  modalVideo.currentTime = 0; // Reiniciar el video al inicio
                  bootstrapModal.hide(); // Cerrar el modal
                }
              });
            }
          });
        }
      });
    }
  });

  // Manejar la eliminación de archivos individuales
  fileList.addEventListener('click', (e) => {
    if (
      e.target.classList.contains('remove-file-button') ||
      e.target.closest('.remove-file-button')
    ) {
      const index =
        e.target.dataset.index ||
        e.target.closest('.remove-file-button').dataset.index;

      // Convertir FileList a un array para poder eliminar el archivo
      const files = Array.from(fileInput.files);
      files.splice(index, 1); // Eliminar el archivo del array

      // Crear un nuevo FileList (no es mutable, así que usamos DataTransfer)
      const dataTransfer = new DataTransfer();
      files.forEach((file) => dataTransfer.items.add(file));
      fileInput.files = dataTransfer.files;

      // Volver a mostrar la lista de archivos actualizada
      fileInput.dispatchEvent(new Event('change'));
      // Eliminar el elemento de la lista
      $(e.target).closest('.file-list-item').remove();
    }
  });

  // Manejar el arrastre de archivos
  fileLabel.addEventListener('dragover', (e) => {
    e.preventDefault();
    fileLabel.style.borderColor = '#007bff';
  });

  fileLabel.addEventListener('dragleave', (e) => {
    e.preventDefault();
    fileLabel.style.borderColor = '#cccccc';
  });

  fileLabel.addEventListener('drop', (e) => {
    e.preventDefault();
    fileLabel.style.borderColor = '#cccccc';
    if (e.dataTransfer.files.length > 0) {
      // Asignar los archivos arrastrados al input
      fileInput.files = e.dataTransfer.files;

      // Mostrar la lista de archivos
      fileInput.dispatchEvent(new Event('change'));
    }
  });
  if (removeFileButton) {
    removeFileButton.addEventListener('click', (e) => {
      e.preventDefault();
      console.log(e.target, "removeFileButton");
      //remove most close file input and remove this from input file
      $(e.target).closest('.file-item').remove();

    });
  }
}

function enableHorizontalAutoScrollForAllTables() {
  // Seleccionar todos los contenedores con la clase .table-responsive
  const tableWrappers = document.querySelectorAll('.table-responsive');

  tableWrappers.forEach((tableWrapper) => {
    let scrollInterval;

    // Detectar la posición del mouse y desplazar automáticamente
    tableWrapper.addEventListener("mousemove", (e) => {
      const rect = tableWrapper.getBoundingClientRect();
      const mouseX = e.clientX - rect.left; // Posición del mouse relativa al contenedor
      const scrollSpeed = 100; // Velocidad de desplazamiento

      // Si el mouse está cerca del borde izquierdo
      if (mouseX < 250) {
        clearInterval(scrollInterval);
        scrollInterval = setInterval(() => {
          tableWrapper.scrollLeft -= scrollSpeed;
        }, 20);
      }
      // Si el mouse está cerca del borde derecho
      else if (mouseX > rect.width - 250) {
        clearInterval(scrollInterval);
        scrollInterval = setInterval(() => {
          tableWrapper.scrollLeft += scrollSpeed;
        }, 20);
      } else {
        clearInterval(scrollInterval); // Detener el desplazamiento si el mouse no está cerca de los bordes
      }
    });

    // Detener el desplazamiento cuando el mouse salga del contenedor
    tableWrapper.addEventListener("mouseleave", () => {
      clearInterval(scrollInterval);
    });
  });
}
$("#navieraAdd").click(function () {
  //show swall with text input and save button
  Swal.fire({
    title: "Agregar Naviera",
    input: "text",
    inputLabel: "Nombre de la naviera",
    inputPlaceholder: "Ingrese el nombre de la naviera",
    showCancelButton: true,
    confirmButtonText: "Guardar",
    cancelButtonText: "Cancelar",
    preConfirm: (value) => {
      if (!value) {
        Swal.showValidationMessage("Por favor ingrese un nombre");
      } else {
        // Guardar la naviera en la base de datos
        $.ajax({
          url: base_url + "AgenteCompra/PedidosPagados/addNaviera",
          type: "POST",
          data: {
            name: value,
          },
          success: async function (response) {
            const result = JSON.parse(response);
            if (result.status == "success") {
              Swal.fire("¡Guardado!", result.message, "success");
              // Recargar la lista de navieras
              url = base_url + "AgenteCompra/PedidosPagados/getNavieras";
              response = await fetch(url);
              let navieras = await response.json();
              navieras = navieras.data;
              //populate navieras select
              const navieraSelect = $("#select-naviera");
              navieraSelect.empty();
              navieraSelect.append(
                `<option value="" selected disabled>Selecciona una naviera</option>`
              );
              for (const naviera of navieras) {
                navieraSelect.append(
                  `<option value="${naviera.name}">${naviera.name}</option>`
                );
              }
            } else {
              Swal.fire("Error", result.message, "error");
            }
          },
        });
      }
    },
  });
});
// Llamar a la función para aplicar el desplazamiento horizontal a todas las tablas
enableHorizontalAutoScrollForAllTables();

setupSingleFileUpload("single-file-upload", "file-input-prospecto", ['pdf', 'docx', 'xlsx', 'xls', 'doc', 'xlsm', 'csv', 'xlsb', 'xltx', 'xlt']);


setupMultiFileUpload("multiple-file-upload-image", "file-input-inspeccion", ['png', 'jpg', 'jpeg', 'mp4']);
setupMultiFileUpload("multiple-file-upload-aduana", "file-input-aduana", ['pdf', 'docx', 'xlsx', 'xls', 'doc', 'xlsm', 'csv', 'xlsb', 'xltx', 'xlt']);
setupMultiFileUpload("multiple-file-upload", "file-input-documentacion", ['pdf', 'docx', 'xlsx', 'xls', 'doc', 'xlsm', 'csv', 'xlsb', 'xltx', 'xlt'], true);