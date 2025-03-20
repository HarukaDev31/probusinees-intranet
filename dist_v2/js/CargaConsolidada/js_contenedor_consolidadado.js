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
var currentPrivilege = localStorage.getItem("currentPrivilege") == null? "": localStorage.getItem("currentPrivilege");
var fileManager = null;
var fileManagerInspection = null;
var fileManagerInspectionCoordinacion = null;
var currentCargaNumber = 0;
var selectedTabDocumentacionId=0;
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
  Swal.fire({
    title: swalConfig.title,
    text: swalConfig.text,
    icon: swalConfig.icon,
    showCancelButton: true,
    confirmButtonText: swalConfig.confirmButtonText,
    cancelButtonText: swalConfig.cancelButtonText,
  }).then((result) => {
    console.log(result);
    if (result.isConfirmed) {
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
      const formattedDate = `${currentDate.getDate()}_${
        currentDate.getMonth() + 1
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
  Swal.fire({
    title: swalConfig.title,
    text: swalConfig.text,
    icon: swalConfig.icon,
    showCancelButton: true,
    confirmButtonText: swalConfig.confirmButtonText,
    cancelButtonText: swalConfig.cancelButtonText,
  }).then((result) => {
    if (result.isConfirmed) {
      try{
         saveDocumentation();
         addNote();
      }catch(e){
        console.error(e);
      }
    }
  });
}
function deleteFile(fileId, cardElement) {
  $.ajax({
    url:
      base_url + "CargaConsolidada/ContenedorConsolidado/deleteFileInspection/" + fileId,
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
function addFileToList(file, fileList = null, id = null) {
  if (fileList == null) {
    fileList = $(".file-lista");
  }
  if (id != null) {
    fileList = $(`#${id}`);
  }
  const isImage = file?.file_ext.startsWith("image");
  const fileItem = $(`
        <div class="file-item">
            <div class="file-preview d-flex"><i class="fas fa-video pr-2"></i>&nbsp;
                <p>${file.file_name}</p>
            </div>
            <div class="file-actions d-flex flex-row">
                <button class="btn-sm download-btn" data-url="${file.file_url}">
                <i class="fas fa-download"></i> 
                </button>
                <button class="btn-sm delete-btn" data-id="${file.id}">
                <i class="far fa-trash-alt"></i>
                </button>
            </div>
        </div>
    `);

  // Botón de descarga
  fileItem.find(".download-btn").on("click", function () {
    event.preventDefault();
    const url = $(this).data("url");
    window.open(url, "_blank");
  });

  // Botón de eliminar
  fileItem.find(".delete-btn").on("click", function () {
    event.preventDefault();
    const id = $(this).data("id");
    deleteFile(id, fileItem);
  });
  //add icon eye button and add event to view image or video preview in other modal,only show icon if video or image
  if (isImage) {
    const imageicon = $(`<i class="far fa-file-image pr-2"></i>`);
    const viewBtn = $(`
            <button class="btn-sm view-btn"><i class="far fa-eye"></i></button>
        `);
    viewBtn.on("click", function () {
      event.preventDefault();
      const url = file.file_url;
      const fileExt = file.file_ext;
      viewFile(url, fileExt);
    });
    fileItem.find(".file-preview i").replaceWith(imageicon);
    fileItem.find(".file-actions").append(viewBtn);
  }
  fileList.append(fileItem);
  //remove class hidden
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
  $("#cotizacion_name").val('#'+result.carga);
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
  $("#cotizacion_name").text('#'+currentCargaNumber);
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
   await  $.ajax({
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
      },
      error: function () {
        //set current select previous status
        $(`#estado-${idCotizacion}-${idProveedor}`).val(previousStatus);
        Swal.fire("Error!", result.message, "error");
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
async function viewSteps(id, carga) {
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
    confirmButtonText: "Cancelar",
    confirmButtonColor: "#00000000",
    cancelButtonText: "Sí, eliminar",
    cancelButtonColor: "#FF0000",
    heihgtAuto: "false",
  }).then((result) => {
    if (result.isCancel) {
      url = base_url + "CargaConsolidada/ContenedorConsolidado/delete";
      $.ajax({
        url: url,
        type: "POST",
        data: {
          id: id,
        },
        success: function (response) {
          const result = JSON.parse(response);
          if (result.status == 1) {
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
      $("#documentacion-documentacion").append(`
                <div class="doc-card opacity-0 ${color} p-4 rounded-lg transition-all duration-300" data-type="${
        file.categoria
      }">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <i class="bi bi-${file.b_icon} text-blue-500 text-xl"></i>
            <div>
              <h3 class="font-medium text-gray-800">${file.folder_name}</h3>
              <p class="text-sm text-gray-500">Ver documento</p>
            </div>
          </div>
          <a class="download-btn text-blue-500 hover:text-blue-700"
          href="${file.file_url}" target="_blank" download>
                
            <i class="${`bi bi-download`}"></i>
          </a>
        </div>
      </div>`);
    } else {
      $("#documentacion-documentacion").append(`
                <div class="doc-card opacity-0 ${color} p-4 rounded-lg transition-all duration-300" data-type="${
        file.categoria
      }">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <i class="bi bi-file
                text-blue-500 text-xl"></i>
                <div>
                    <h3 class="font-medium text-gray-800">${
                      file.folder_name
                    }</h3>
                    <p class="text-sm text-red-500">Documento no subido</p>
                </div>
            </div>
              <span class="download-btn text-blue-500 hover:text-blue-700"
                id="file-input-doc-${file.id}-label">
                <i class="${file.b_icon ?? `bi bi-upload`}"></i>
                 </span>
                          <input type="file" id="file-input-doc-${
                            file.id
                          }" class="hidden" />
        </div>
    </div>`);
      // Add event listener to file input
      $(`#file-input-doc-${file.id}-label`).off("click");
      $(`#file-input-doc-${file.id}-label`).on("click", function () {
        //show swall for upload
        Swal.fire({
          title: "Subir documento",
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
        }).then((result) => {
          if (result.isConfirmed) {
            const fileI = result.value;
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
          }
        });
      });
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
  const response = await fetch(url);
  const result = await response.json();
  spinner.hide();
  documentacionAduanaContainer.show();
  $(".tab-btn").click(function () {
    const tab = $(this).data("tab");
    $(".tab-btn").removeClass("active bg-gray-100");
    $(this).addClass("active bg-gray-100");
    $(".tab-content").removeClass("active").addClass("hidden");
    $(`#${tab}`).removeClass("hidden").addClass("active");
  });

  // Control channel color indicator
  function updateChannelIndicator() {
    const channel = $("#controlChannel").val();
    const indicator = $("#channelIndicator");

    switch (channel) {
      case "Verde":
        indicator.css("background-color", "#22c55e");
        break;
      case "Naranja":
        indicator.css("background-color", "#f97316");
        break;
      case "Rojo":
        indicator.css("background-color", "#ef4444");
        break;
      default:
        indicator.css("background-color", "#d1d5db");
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
        <div class="step-container" onclick="openStepFunction(${i + 1},${
    step.id
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
              spinner.hide();
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
          lengthMenu: [
            [10, 100, 1000, -1],
            [10, 100, 1000, "Todos"],
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

      stepsContainer.hide();
    } else {
      returnToSteps();
    }
  });
  $(".btn-back-cotizacion-documentacion").off("click");
  $(".btn-back-cotizacion-documentacion").on("click", function () {
    clientesContainer.show();
    cotizacionContainer.hide();
    clientesDocumentacionContainer.hide();
    stepsContainer.hide();
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
    console.log(searchTerm);
  });

  // Actualizar el mensaje de información después de cada búsqueda
  table.on("draw", function () {
    var info = table.page.info();
    if (infoContainerId) {
      $("#" + infoContainerId).html(
        `Mostrando ${info.start + 1} a ${info.end} de ${
          info.recordsTotal
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
                    <div class="col-12 col-sm-12" id="single-${
                      file.folder_name
                    }">
                        <div class="form-group">
                            <label>${file.folder_name}
                            ${
                              file.id_contenedor
                                ? `<div class="badge badge-danger text-white delete-folder-button
                                
                                " onclick="deleteDocumentacionFolder(${file.id})">
                                X
                                </div>`
                                : ""
                            }
                            </label>
                            <div class="file-upload-box">
                                ${
                                  file.file_url
                                    ? `
                                        <div class="file-info">
                                            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="50" height="50" viewBox="0 0 48 48">
                                                <rect width="16" height="9" x="28" y="15" fill="#21a366"></rect>
                                                <path fill="#185c37" d="M44,24H12v16c0,1.105,0.895,2,2,2h28c1.105,0,2-0.895,2-2V24z"></path>
                                                <rect width="16" height="9" x="28" y="24" fill="#107c42"></rect>
                                                <rect width="16" height="9" x="12" y="15" fill="#3fa071"></rect>
                                                <path fill="#33c481" d="M42,6H28v9h16V8C44,6.895,43.105,6,42,6z"></path>
                                                <path fill="#21a366" d="M14,6h14v9H12V8C12,6.895,12.895,6,14,6z"></path>
                                                <path d="M22.319,13H12v24h10.319C24.352,37,26,35.352,26,33.319V16.681C26,14.648,24.352,13,22.319,13z" opacity=".05"></path>
                                                <path d="M22.213,36H12V13.333h10.213c1.724,0,3.121,1.397,3.121,3.121v16.425	C25.333,34.603,23.936,36,22.213,36z" opacity=".07"></path>
                                                <path d="M22.106,35H12V13.667h10.106c1.414,0,2.56,1.146,2.56,2.56V32.44C24.667,33.854,23.52,35,22.106,35z" opacity=".09"></path>
                                                <linearGradient id="flEJnwg7q~uKUdkX0KCyBa_UECmBSgBOvPT_gr1" x1="4.725" x2="23.055" y1="14.725" y2="33.055" gradientUnits="userSpaceOnUse">
                                                    <stop offset="0" stop-color="#18884f"></stop>
                                                    <stop offset="1" stop-color="#0b6731"></stop>
                                                </linearGradient>
                                                <path fill="url(#flEJnwg7q~uKUdkX0KCyBa_UECmBSgBOvPT_gr1)" d="M22,34H6c-1.105,0-2-0.895-2-2V16c0-1.105,0.895-2,2-2h16c1.105,0,2,0.895,2,2v16	C24,33.105,23.105,34,22,34z"></path>
                                                <path fill="#fff" d="M9.807,19h2.386l1.936,3.754L16.175,19h2.229l-3.071,5l3.141,5h-2.351l-2.11-3.93L11.912,29H9.526	l3.193-5.018L9.807,19z"></path>
                                            </svg>
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
                                    <input type="file" id="file-input-${file.id}" class="file-input" accept=".xlsx"/>
                                    <label for="file-inputo" class="file-label d-flex">
                                        <i class="fas fa-upload"></i>
                                        <div class="file-group-text">
                                            <span class="file-text">Selecciona o arrastra tu archivo aquí</span>
                                            <span class="file-format">Formatos: .xlsx</span>
                                        </div>
                                        <button class="upload-button-documentacion-peru-${file.id}" type="button">Subir archivo</button>
                                    </label>
                                    <!-- Cuadro de información del archivo subido (oculto inicialmente) -->
                                    <div class="file-info-box hidden">
                                        <div class="file-info">
                                            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="50" height="50" viewBox="0 0 48 48">
                                                <rect width="16" height="9" x="28" y="15" fill="#21a366"></rect>
                                                <path fill="#185c37" d="M44,24H12v16c0,1.105,0.895,2,2,2h28c1.105,0,2-0.895,2-2V24z"></path>
                                                <rect width="16" height="9" x="28" y="24" fill="#107c42"></rect>
                                                <rect width="16" height="9" x="12" y="15" fill="#3fa071"></rect>
                                                <path fill="#33c481" d="M42,6H28v9h16V8C44,6.895,43.105,6,42,6z"></path>
                                                <path fill="#21a366" d="M14,6h14v9H12V8C12,6.895,12.895,6,14,6z"></path>
                                                <path d="M22.319,13H12v24h10.319C24.352,37,26,35.352,26,33.319V16.681C26,14.648,24.352,13,22.319,13z" opacity=".05"></path>
                                                <path d="M22.213,36H12V13.333h10.213c1.724,0,3.121,1.397,3.121,3.121v16.425	C25.333,34.603,23.936,36,22.213,36z" opacity=".07"></path>
                                                <path d="M22.106,35H12V13.667h10.106c1.414,0,2.56,1.146,2.56,2.56V32.44C24.667,33.854,23.52,35,22.106,35z" opacity=".09"></path>
                                                <linearGradient id="flEJnwg7q~uKUdkX0KCyBa_UECmBSgBOvPT_gr1" x1="4.725" x2="23.055" y1="14.725" y2="33.055" gradientUnits="userSpaceOnUse">
                                                    <stop offset="0" stop-color="#18884f"></stop>
                                                    <stop offset="1" stop-color="#0b6731"></stop>
                                                </linearGradient>
                                                <path fill="url(#flEJnwg7q~uKUdkX0KCyBa_UECmBSgBOvPT_gr1)" d="M22,34H6c-1.105,0-2-0.895-2-2V16c0-1.105,0.895-2,2-2h16c1.105,0,2,0.895,2,2v16	C24,33.105,23.105,34,22,34z"></path>
                                                <path fill="#fff" d="M9.807,19h2.386l1.936,3.754L16.175,19h2.229l-3.071,5l3.141,5h-2.351l-2.11-3.93L11.912,29H9.526	l3.193-5.018L9.807,19z"></path>
                                            </svg>
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
                                    <script>   setupSingleFileUpload('single-${file.folder_name}', 'file-input-${file.id}', '.upload-button-documentacion-peru-${file.id}')</script>
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
  $("#cotizacion_name").val('#'+currentCargaNumber);
  $("#txt-CBM_Total_Peru").html(result.cbm_total);
  $("#txt-CBM_Total_China").html(result.cbm_total_china);
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
          const formData = new FormData();
          formData.append("file", file);
          formData.append("id", idContenedor);
          return fetch(
            base_url + "CargaConsolidada/ContenedorConsolidado/uploadBL",
            {
              method: "POST",
              body: formData,
            }
          )
            .then((response) => {
              getTableCotizacionEmbarqueHeaders();

              return response.json();
              //call header again
            })
            .catch((error) => {
              Swal.showValidationMessage(`Request failed: ${error}`);
            });
        },
        allowOutsideClick: () => !Swal.isLoading(),
      });
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
          if (result.status == "success") {
            Swal.fire("Eliminado!", result.message, "success");
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
      url =base_url +"CargaConsolidada/ContenedorConsolidado/deleteDocumentacionFile/" +id;
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

      const fileContainer = document.getElementById(`single-${idFolder}`);
      if (fileContainer) {
        const fileNameElement = fileContainer.querySelector(".file-name");
        const fileSizeElement = fileContainer.querySelector(".file-size");

        if (fileNameElement && fileSizeElement) {
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

  // Swal.fire({
  //     title: "Subir archivo",
  //     input: "file",
  //     inputAttributes: {
  //         accept: "*",
  //         "aria-label": "Sube tu archivo",
  //     },
  //     showCancelButton: true,
  //     confirmButtonText: "Subir",
  //     showLoaderOnConfirm: true,
  //     preConfirm: (file) => {
  //         const formData = new FormData();
  //         formData.append("file", file);
  //         formData.append("idFolder", idFolder);
  //         formData.append("idContenedor", idContenedor);
  //         return fetch(base_url + "CargaConsolidada/ContenedorConsolidado/uploadFileDocumentation", {
  //             method: "POST",
  //             body: formData,
  //         })
  //             .then((response) => {
  //                 return response.json();
  //             })
  //             .catch((error) => {
  //                 Swal.showValidationMessage(
  //                     `Request failed: ${error}`
  //                 );
  //             });
  //     },
  //     allowOutsideClick: () => !Swal.isLoading(),
  // }).then((result) => {
  //     if (result.value) {
  //         if (result.value.status == "success") {
  //             Swal.fire("Correcto", result.value.message, "success");
  //             viewDocumentacion();
  //         } else {
  //             Swal.fire("Error", result.value.message, "error");
  //         }
  //     }
  // });
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
async function viewClientesDocumentacion(id) {
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
  const providers=JSON.parse(result.providers);
  $(".documentos-clientes-tabs").empty();
  $(".documentos-clientes-content").empty();

  //FOR EACH PROVIDER ADD TAB WITH DATA-ID=provider.id
  providers.forEach((provider) => {
    $(".documentos-clientes-tabs").append(`
        <div class="tab-cliente-documentacion" data-id="${provider.id}">
          ${provider.code_supplier}
        </div>`);
  }); 
  //ADD EVENT LISTENER TO EACH TAB
  //default select first
  selectedTabDocumentacionId = providers[0].id;
  $(".tab-cliente-documentacion").on("click", function () {
    const id = $(this).data("id");
    const provider = providers.find((p) => p.id == id);
    selectedTabDocumentacionId = id;
    $(".tab-cliente-documentacion").removeClass("active");
    $(this).addClass("active");
    $(".documentos-clientes-content").empty();
    $(".documentos-clientes-content").append(`<div class="grid grid-cols-3 md:grid-cols-3 gap-8">
        <!-- Sección de Documentación -->
        <div class="bg-white p-6 rounded-lg shadow-md
        col-span-2
        ">
          <div class="flex items-center gap-2 mb-6">
            <h2 class="text-xl font-semibold">Documentación</h2>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
              <polyline points="14 2 14 8 20 8" />
              <line x1="16" y1="13" x2="8" y2="13" />
              <line x1="16" y1="17" x2="8" y2="17" />
              <line x1="10" y1="9" x2="8" y2="9" />
            </svg>
          </div>

          <form id="form-documentacion" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Volumen documento</label>
                <input type="number"
                value="${provider.volumen_doc}"
                id="txt-Vol_Doc" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" name="volumen_doc">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Valor documento</label>
                <div class="relative">
                  <span class="absolute left-3 top-2">$</span>
                  <input type="number"
                  value="${provider.valor_doc}"
                  id="txt-Valor_Doc" class="w-full pl-7 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" name="valor_doc">
                </div>
              </div>
            </div>

            <div class="space-y-4" id="documentos-clientes-documentacion">
           
           
            </div>
          </form>
        </div>

        <!-- Sección de Cotizaciones -->
        <div class="bg-white p-6 rounded-lg shadow-md
        col-span-1
        "
          style="height: 40%;min-height: 300px;">
          <div class="flex items-center gap-2 mb-6">
            <h2 class="text-xl font-semibold">Cotizaciones</h2>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
              <polyline points="7 10 12 15 17 10" />
              <line x1="12" y1="15" x2="12" y2="3" />
            </svg>
          </div>

          <div class="space-y-4">
            <button class="w-full flex items-center justify-between px-4 py-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors">
              <span class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                  <polyline points="7 10 12 15 17 10" />
                  <line x1="12" y1="15" x2="12" y2="3" />
                </svg>
                Descargar cotización inicial
              </span>
            </button>

            <button class="w-full flex items-center justify-between px-4 py-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors">
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
        </div>
      </div>`);
      let facturaDiv = "";
      let excelDiv = "";
      let facturaComercial = provider.factura_comercial;
      let excelConfirmacion = provider.excel_confirmacion;
      if (!facturaComercial) {
        facturaDiv = `
        Factura Comercial
        <div class="col-12 col-sm-12" id="single-file-upload-factura">
            <div class="form-group">
                <div class="file-upload-box">
                    <input type="file" id="file-input-factura" class="file-input" name="file_comercial"
                        accept=".xlsx,.xls,.csv,.xlsb,.xlsm,.xltx,.xltm,.xls,.xlt" />
                    <label for="file-input-factura" class="file-label d-flex">
                        <i class="fas fa-upload"></i>
                        <div class="file-group-text">
                            <span class="file-text">Selecciona o arrastra tu archivo aquí</span>
                            <span class="file-format">Formatos: .xlsx</span>
                        </div>
                        <button class="upload-button" type="button">Subir archivo</button>
                    </label>
    
                    <!-- Cuadro de información del archivo subido (oculto inicialmente) -->
                    <div class="file-info-box hidden">
                        <div class="file-info">
                            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="50" height="50" viewBox="0 0 48 48">
                                <!-- SVG content -->
                            </svg>
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
                            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="50" height="50" viewBox="0 0 48 48">
                                <!-- SVG content -->
                            </svg>
                            <span class="file-name">Factura Comercial</span>
                            <a href="${facturaComercial}" target="_blank" class="file-link">Ver archivo</a>
                            <button class="remove-file-button"
                            onclick="deleteFacturaComercial(${provider.id})"
                            >
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
      }
    
      $("#documentos-clientes-documentacion").append(facturaDiv);
      //   setupSingleFileUpload("single-file-upload-factura", "file-input-factura");
    
      // Repetir el mismo proceso para el Excel de confirmación
      if (!excelConfirmacion) {
        excelDiv = `
        Excel Confirmación
        <div class="col-12 col-sm-12" id="single-file-upload-confirmacion">
            <div class="form-group">
                <div class="file-upload-box">
                    <input type="file" id="file-input-confirmacion" class="file-input" name="excel_confirmacion"
                        accept=".xlsx,.xls,.csv,.xlsb,.xlsm,.xltx,.xltm,.xls,.xlt" />
                    <label for="file-input-confirmacion" class="file-label d-flex">
                        <i class="fas fa-upload"></i>
                        <div class="file-group-text">
                            <span class="file-text">Selecciona o arrastra tu archivo aquí</span>
                            <span class="file-format">Formatos: .xlsx</span>
                        </div>
                        <button class="upload-button" type="button">Subir archivo</button>
                    </label>
    
                    <!-- Cuadro de información del archivo subido (oculto inicialmente) -->
                    <div class="file-info-box hidden">
                        <div class="file-info">
                            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="50" height="50" viewBox="0 0 48 48">
                                <!-- SVG content -->
                            </svg>
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
                            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="50" height="50" viewBox="0 0 48 48">
                                <!-- SVG content -->
                            </svg>
                            <span class="file-name">Excel Confirmación</span>
                            <a href="${excelConfirmacion}" target="_blank" class="file-link">Ver archivo</a>
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
      }
    
      $("#documentos-clientes-documentacion").append(excelDiv);
      if (!facturaComercial) {
        setupSingleFileUpload(
          "single-file-upload-factura",
          "file-input-factura"
        );
      }
      if (!excelConfirmacion) {
        setupSingleFileUpload(
          "single-file-upload-confirmacion",
          "file-input-confirmacion"
        );
      }
  });
  $(".tab-cliente-documentacion").first().click();
  // $("#txt-Vol_Doc").val(parseFloat(result[0].volumen_doc));
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
  //clean .aditional-file
  $(".aditional-file").remove();
  const filesDoc = JSON.parse(result[0].files_almacen_documentacion ?? "[]");
  const files = JSON.parse(result[0].files ?? "[]");
  //for each file add a col with a link to download and delete icon  in collapse-documentacion
  files.forEach((file) => {
    $(`
        <div class="col-3 aditional-file d-flex flex-column mb-1">
        <label>${file.folder_name}</label>
        <div class="d-flex flex-row gap-1">
        <div>
            <a href="${file.file_url}" target="_blank" class="btn btn-outline-primary">
            <i class="fa fa-download"></i>
            Descargar
            </a>
           </div>
            <div class="btn btn-outline-danger" onclick="deleteClienteDocumentacionFile(${file.id})">
            <i class="fa fa-trash " ></i>
            </div>
        </div>  
        </div>
        `).insertBefore(".col-guardar-documentacion");
  });
  filesDoc.forEach((file) => {
    $(`
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
            `).insertBefore(".col-guardar-documentacion");
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
  $('.dropdown-menu').on('click', function(event) {
    event.stopPropagation(); // Evita que el evento se propague
  });

  // Cierra el menú al hacer clic en "Cancelar" o "Aplicar"
  $('#cancelar-btn, #aplicar-btn').on('click', function() {
    $('#filtros-btn').dropdown('hide'); // Cierra el menú
  });

  // Cierra el menú al hacer clic en el botón "Filtros" si ya está abierto
  $('#filtros-btn').on('click', function(event) {
    if ($(this).attr('aria-expanded') === 'true') {
      $(this).dropdown('hide'); // Cierra el menú si ya está abierto
    }
  });
  console.log("waos",currentPrivilege);

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
  url = base_url + "CargaConsolidada/ContenedorConsolidado/index";
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
    lengthMenu: [
      [10, 100, 1000, -1],
      [10, 100, 1000, "Todos"],
    ],
  });

  configurarBuscador('table-contenedor','search-table','table-contenedor_filter');
  

  $("#upload-documents").click(() => $("#upload-input-documents").click());
  $("#upload-inspection").click(() => $("#upload-input-inspection").click());

  // Listeners para subir archivos
  $('#btn-guardar-doc-not').click(async() => {
    await saveBoth();
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
      const isImage = file.file_ext.match(/image\//); // Verificar si es una imagen

      const card = $(`
                <div class="file-card">
                    ${
                      isImage
                        ? `<img src="${file.file_url}" alt="${file.file_name}">`
                        : `<div class="file-placeholder"><i class="bi bi-file-earmark"></i></div>`
                    }
                    <div class="file-name">${file.file_name}</div>
                    <div class="actions">
                        <button class="btn btn-sm btn-primary download-btn" data-url="${
                          file.file_url
                        }">Download</button>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="${
                          file.id
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
  $("#btn-back-cotizacion-almacen").click(function () {
    cotizacionAlmacenContainer.hide();
    contentHeader.hide();
    cotizacionContainer.show();
    
    
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
      title: "Subir Factura Final",
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
 

function setupSingleFileUpload(containerId, inputId,selectInputId='.upload-button') {
  try {
    const container = document.getElementById(containerId);
    const fileInput = $(`#${inputId}`)[0];
    const fileLabel = container.querySelector('.file-label');
    const fileInfoBox = container.querySelector('.file-info-box');
    const fileNameElement = container.querySelector('.file-name');
    const fileSizeElement = container.querySelector('.file-size');
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
        if (file.name.endsWith('.xlsx') || file.name.endsWith('.xls') || file.name.endsWith('.csv') || file.name.endsWith('.xlsb') || file.name.endsWith('.xlsm') || file.name.endsWith('.xltx') || file.name.endsWith('.xlt')) {
          // Mostrar el cuadro de información del archivo
          fileInfoBox.classList.remove('hidden');

          // Mostrar el nombre y el tamaño del archivo
          fileNameElement.textContent = file.name;
          fileSizeElement.textContent = `${(file.size / 1024).toFixed(2)} KB`;
        } else {
          alert("Solo se permiten archivos .xlsx");
          fileInput.value = ""; // Limpia el input
        }
      } else {
        fileInfoBox.classList.add('hidden'); // Ocultar el cuadro de información
      }
    });
    if(removeFileButton){
      removeFileButton.addEventListener('click', (e) => {
      e.preventDefault();
      fileInput.value = ""; // Limpia el input
      fileInfoBox.classList.add('hidden'); // Oculta el cuadro de información
    });
    }
    // Manejar el botón de tacho de basura para quitar el archivo
    

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
        if (file.name.endsWith('.xlsx')) {
          fileInput.files = e.dataTransfer.files; // Asigna el archivo arrastrado al input

          // Mostrar la información del archivo
          fileInput.dispatchEvent(new Event('change'));
        } else {
          alert("Solo se permiten archivos .xlsx");
        }
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

function setupMultiFileUpload(containerId, inputId) {
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
        // Crear un elemento de lista para cada archivo
        const fileItem = document.createElement('div');
        fileItem.classList.add('file-list-item');

        // Definir el ícono según el tipo de archivo
        let icon = '';
        if (file.name.endsWith('.jpeg') || file.name.endsWith('.jpg')) {
          icon = `
                      <svg style="width:20%" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="50" height="50" viewBox="0 0 48 48">
                        <path fill="#90caf9" d="M40,42H8c-1.105,0-2-0.895-2-2V8c0-1.105,0.895-2,2-2h32c1.105,0,2,0.895,2,2v32C42,41.105,41.105,42,40,42z"></path>
                        <path fill="#1565c0" d="M40,42H8c-1.105,0-2-0.895-2-2V8c0-1.105,0.895-2,2-2h32c1.105,0,2,0.895,2,2v32C42,41.105,41.105,42,40,42z"></path>
                        <path fill="#fff" d="M24,14c-5.523,0-10,4.477-10,10s4.477,10,10,10s10-4.477,10-10S29.523,14,24,14z M24,30c-3.314,0-6-2.686-6-6	s2.686-6,6-6s6,2.686,6,6S27.314,30,24,30z"></path>
                      </svg>
                  `;
        } else if (file.name.endsWith('.png')) {
          icon = `
                      <svg style="width:20%" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="50" height="50" viewBox="0 0 48 48">
                        <path fill="#90caf9" d="M40,42H8c-1.105,0-2-0.895-2-2V8c0-1.105,0.895-2,2-2h32c1.105,0,2,0.895,2,2v32C42,41.105,41.105,42,40,42z"></path>
                        <path fill="#1565c0" d="M40,42H8c-1.105,0-2-0.895-2-2V8c0-1.105,0.895-2,2-2h32c1.105,0,2,0.895,2,2v32C42,41.105,41.105,42,40,42z"></path>
                        <path fill="#fff" d="M24,14c-5.523,0-10,4.477-10,10s4.477,10,10,10s10-4.477,10-10S29.523,14,24,14z M24,30c-3.314,0-6-2.686-6-6	s2.686-6,6-6s6,2.686,6,6S27.314,30,24,30z"></path>
                      </svg>
                  `;
        } else if (file.name.endsWith('.xlsx')) {
          icon = `
                          <svg style="width:20%" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="50" height="50" viewBox="0 0 48 48">
                            <rect width="16" height="9" x="28" y="15" fill="#21a366"></rect>
                            <path fill="#185c37" d="M44,24H12v16c0,1.105,0.895,2,2,2h28c1.105,0,2-0.895,2-2V24z"></path>
                            <rect width="16" height="9" x="28" y="24" fill="#107c42"></rect>
                            <rect width="16" height="9" x="12" y="15" fill="#3fa071"></rect>
                            <path fill="#33c481" d="M42,6H28v9h16V8C44,6.895,43.105,6,42,6z"></path>
                            <path fill="#21a366" d="M14,6h14v9H12V8C12,6.895,12.895,6,14,6z"></path>
                            <path d="M22.319,13H12v24h10.319C24.352,37,26,35.352,26,33.319V16.681C26,14.648,24.352,13,22.319,13z" opacity=".05"></path>
                            <path d="M22.213,36H12V13.333h10.213c1.724,0,3.121,1.397,3.121,3.121v16.425	C25.333,34.603,23.936,36,22.213,36z" opacity=".07"></path>
                            <path d="M22.106,35H12V13.667h10.106c1.414,0,2.56,1.146,2.56,2.56V32.44C24.667,33.854,23.52,35,22.106,35z" opacity=".09"></path>
                            <linearGradient id="flEJnwg7q~uKUdkX0KCyBa_UECmBSgBOvPT_gr1" x1="4.725" x2="23.055" y1="14.725" y2="33.055" gradientUnits="userSpaceOnUse">
                              <stop offset="0" stop-color="#18884f"></stop>
                              <stop offset="1" stop-color="#0b6731"></stop>
                            </linearGradient>
                            <path fill="url(#flEJnwg7q~uKUdkX0KCyBa_UECmBSgBOvPT_gr1)" d="M22,34H6c-1.105,0-2-0.895-2-2V16c0-1.105,0.895-2,2-2h16c1.105,0,2,0.895,2,2v16	C24,33.105,23.105,34,22,34z"></path>
                            <path fill="#fff" d="M9.807,19h2.386l1.936,3.754L16.175,19h2.229l-3.071,5l3.141,5h-2.351l-2.11-3.93L11.912,29H9.526	l3.193-5.018L9.807,19z"></path>
                          </svg>
                      `;
        } else if (file.name.endsWith('.mp4')) {
          icon = `
                      <svg style="width:20%" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="50" height="50" viewBox="0 0 48 48">
                        <path fill="#ff7043" d="M40,42H8c-1.105,0-2-0.895-2-2V8c0-1.105,0.895-2,2-2h32c1.105,0,2,0.895,2,2v32C42,41.105,41.105,42,40,42z"></path>
                        <path fill="#bf360c" d="M40,42H8c-1.105,0-2-0.895-2-2V8c0-1.105,0.895-2,2-2h32c1.105,0,2,0.895,2,2v32C42,41.105,41.105,42,40,42z"></path>
                        <path fill="#fff" d="M19,32V16l12,8L19,32z"></path>
                      </svg>
                  `;
        } else {
          alert(`El archivo "${file.name}" no es un archivo válido`);
          return; // Salir si el archivo no es válido
        }

        // Mostrar el nombre y el tamaño del archivo
        fileItem.innerHTML = `
                  ${icon}
                  <span
                  style="width:70%"
                  >${file.name} (${(file.size / 1024).toFixed(2)} KB)</span>
                  <div class="remove-file-button" data-index="${index}">
                      <i class="fas fa-trash"></i>
                  </div>
              `;

        // Agregar el elemento a la lista
        fileList.appendChild(fileItem);
      });
    } else {
      // fileList.classList.add('hidden'); // Ocultar la lista si no hay archivos seleccionados
    }
  });

  // Manejar la eliminación de archivos individuales
  fileList.addEventListener('click', (e) => {
    if (e.target.classList.contains('remove-file-button') || e.target.closest('.remove-file-button')) {
      const index = e.target.dataset.index || e.target.closest('.remove-file-button').dataset.index;

      // Convertir FileList a un array para poder eliminar el archivo
      const files = Array.from(fileInput.files);
      files.splice(index, 1); // Eliminar el archivo del array

      // Crear un nuevo FileList (no es mutable, así que usamos DataTransfer)
      const dataTransfer = new DataTransfer();
      files.forEach(file => dataTransfer.items.add(file));
      fileInput.files = dataTransfer.files;

      // Volver a mostrar la lista de archivos actualizada
      fileInput.dispatchEvent(new Event('change'));
      //remove from file list
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
  if(removeFileButton){
  removeFileButton.addEventListener('click', (e) => {
    e.preventDefault();
    console.log(e.target,"removeFileButton");
    //remove most close file input and remove this from input file
    $(e.target).closest('.file-item').remove();

  });
}
}

setupSingleFileUpload("single-file-upload", "file-input-prospecto");


setupMultiFileUpload("multiple-file-upload-image", "file-input-inspeccion");
setupMultiFileUpload("multiple-file-upload", "file-input-documentacion");