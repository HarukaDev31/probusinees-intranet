
let spinner = null;
var table_Entidad = null;
var idContenedor = 0;
var btnCrear = null;
var btnCrearCotizacion = null;
var paises = [];
var fToday = new Date();
var fYear = fToday.getFullYear();
var fDay = fToday.getDate();
var currentCarga = 0;
var currentProveedor=0;
var currentCotizacion
var currentTableCotizacion="prospectos";
var currentPrivilege="";
var fileManager = null;
var fileManagerInspection = null;
var fileManagerInspectionCoordinacion = null;
var meses = [
    {
        "id": "ENERO",
        "value": "ENERO"
    },
    {
        "id": "FEBRERO",
        "value": "FEBRERO"
    },
    {
        "id": "MARZO",
        "value": "MARZO"
    },
    {
        "id": "ABRIL",
        "value": "ABRIL"
    },
    {
        "id": "MAYO",
        "value": "MAYO"
    },
    {
        "id": "JUNIO",
        "value": "JUNIO"
    },
    {
        "id": "JULIO",
        "value": "JULIO"
    },
    {
        "id": "AGOSTO",
        "value": "AGOSTO"
    },
    {
        "id": "SETIEMBRE",
        "value": "SETIEMBRE"
    },
    {
        "id": "OCTUBRE",
        "value": "OCTUBRE"
    },
    {
        "id": "NOVIEMBRE",
        "value": "NOVIEMBRE"
    },
    {
        "id": "DICIEMBRE",
        "value": "DICIEMBRE"
    }
]
var stepsContainer = null;
var mainContainer = null;
var stepIndex = 0;
var stepId = 0;
var cotizacionContainer = null;
var clientesContainer = null;
var tableCotizacion = null;
var tableCotizacionEmbarque=null;
var tableClientesGeneral = null;
var tableClientesVariacion = null;
var clientesDocumentacionContainer = null;
var documentationContainer = null;
var idCotizacion = 0;
var cotizacionInspectionContainer = null;
var dropZone ;
var fileInput ; 
var fileList ;
var cotizacionAlmacenContainer = null;
var cotizacionFinalContainer = null;
var tableCotizacionFinal = null;
async function descargarBoletaPDF (idCotizacionFinal)  {
    spinner.show();
    $.ajax({
      url: base_url + "CargaConsolidada/ContenedorConsolidado/downloadBoleta/"+idCotizacionFinal,
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
        link.download = `Cotizacion.pdf`
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
  
        spinner.hide();
      },
      error: function (errorThrown) {
        Swal.fire("Error!", "Hubo un error", "error");
        console.error("Error al descargar el archivo Excel: " + errorThrown);
        spinner.hide();
      },
    });
  };
async function updateEstado(id) {
    const estado = $(`#estado-${id}`).val();
    url = base_url + "CargaConsolidada/ContenedorConsolidado/updateEstado";
    $.ajax({
        url: url,
        type: "POST",
        data: {
            id: id,
            estado: estado
        },
        success: function (response) {
            const result = JSON.parse(response);
            if (result.status == "success") {
                Swal.fire("Correcto!", result.message, "success");
            } else {
                Swal.fire("Error!", result.message, "error");
            }
            table_Entidad.ajax.reload();
        },
    });
}
async function uploadCotizacionFinal(id){
    //swall with file input 
    const { value: file } = await Swal.fire({
        title: 'Subir Cotización Final',
        input: 'file',
        inputAttributes: {
            'accept': //excel
                '.xlsx, .xls,.xlsm, .xlsb, .xltx, .xltm, .xlam, .xla, .xlw',
            'aria-label': 'Sube tu archivo'

        },
        showCancelButton: true,
        confirmButtonText: 'Subir',
        cancelButtonText: 'Cancelar',
        inputValidator: (value) => {
            if (!value) {
                return 'Debes elegir un archivo!'
            }
        }
    })
    if (file) {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('idCotizacionFinal', id);
        url = base_url + "CargaConsolidada/ContenedorConsolidado/uploadCotizacionFinal";
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
async function deleteCotizacionFinalFile(id){
    Swal.fire({
        title: "¿Estás seguro?",
        text: "¡No podrás revertir esto!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminarlo",
        cancelButtonText: "No, cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            url = base_url + "CargaConsolidada/ContenedorConsolidado/deleteCotizacionFinalFile/" + id;
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
async function updateEstadoCotizacionFinal(idCotizacionFinal){
    const estado = $(`#estado-cotizacion-final${idCotizacionFinal}`).val();
    
    url = base_url + "CargaConsolidada/ContenedorConsolidado/updateEstadoCotizacionFinal";
    $.ajax({
        url: url,
        type: "POST",
        data: {
            idCotizacionFinal: idCotizacionFinal,
            estado: estado
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
function addFileToList(file) {
    const isImage = file.file_ext.startsWith('image');
    const fileItem = $(`
        <div class="file-item">
            <div class="file-preview">
                ${isImage ? `<img src="${file.file_url}" alt="${file.file_name}">` : `<span>📄</span>`}
                <span>${file.file_name}</span>
            </div>
            <div class="file-actions">
                <button class="btn btn-primary btn-sm download-btn" data-url="${file.file_url}">Descargar</button>
                <button class="btn btn-danger btn-sm delete-btn" data-id="${file.id}">Eliminar</button>
            </div>
        </div>
    `);

    // Botón de descarga
    fileItem.find('.download-btn').on('click', function () {
        const url = $(this).data('url');
        window.open(url, '_blank');
    });

    // Botón de eliminar
    fileItem.find('.delete-btn').on('click', function () {
        const id = $(this).data('id');
        deleteFile(id, fileItem);
    });

    fileList.append(fileItem);
}
async function getClientesHeader(){
    spinner.show();
    url = base_url + "CargaConsolidada/ContenedorConsolidado/getClientesHeader/"+idContenedor;
    const response = await fetch(url);
    const result = await response.json();
    $("#txt-Monto_Total").val(result.monto);
    $("#txt-CBM_Total_China_Clientes").val(result.cbm_total_china);
    console.log(result);
    spinner.hide();
    
}
async function verCotizacionEmbarque(idProveedor,idCotizacion,supplierCode,clientName){
    //hide table cotizacion embarque
    cotizacionContainer.hide();
    cotizacionAlmacenContainer.show();
    currentProveedor=idProveedor;
    currentCotizacion=idCotizacion;
     // Función para inicializar la lista al cargar la página
     spinner.show();
     $("#client-title").text(clientName+":");
     $("#client-supplier-code").text(supplierCode);
      fileManager = new FileManager({
        fileGrid: "#file-grid",
        fileInput: "#file-input-modal",
        uploadBtn: "#btn-upload",
        dragDropContainer: "#drag-drop-container",
        searchInput: "#search-input",
        pendingFileList: "#pending-file-list",
        onFileUpload:(file)=> uploadFileDocument(file,fileManager),
        onLoadFiles:()=>getFilesAlmacenDocument(idProveedor,idCotizacion),
      });
       fileManagerInspection = new FileManager({
        fileGrid: "#file-grid-inspection",
        fileInput: "#file-input-modal-inspection",
        uploadBtn: "#btn-upload-inspection",
        dragDropContainer: "#drag-drop-container-inspection",
        searchInput: "#search-input-inspection",
        pendingFileList: "#pending-file-list-inspection",
        onFileUpload:(file)=> uploadFileAlmacenInspection(file,fileManagerInspection),
        onLoadFiles:()=>getFilesAlmacenInspection(idProveedor,idCotizacion),
      });
        //fectch getNotes
        url = base_url + "CargaConsolidada/ContenedorConsolidado/getNotes/"+idProveedor;
        const response = await fetch(url);
        const result = await response.json();
        $("#txt-Id_Carga_Consolidada").val(result.nota);   
        if(currentPrivilege!="ContenedorAlmacen"){
            $(".file-section-container").css("pointer-events","none");
            $(".note-container-container").css("pointer-events","none");
        }

        spinner.hide();
    // Función para agregar un archivo a la lista con vista previa y botones
  

}
async function getFilesAlmacenDocument(idProveedor,idCotizacion){
    spinner.show();

    const url = base_url + 'CargaConsolidada/ContenedorConsolidado/getFilesAlmacenDocument/'+idProveedor;
    return new Promise((resolve, reject) => {
        $.ajax({
            url: url,
            method: 'GET',
            processData: false,
            contentType: false,
            success: function (data) {
                const result = JSON.parse(data);
                if (result.status === 'success') {
                    resolve(result.data);
                } else {
                    reject(result.message);
                }
            },
            error: function () {
                reject('Error al subir el archivo');
            },
            complete: function () {
                spinner.hide();
            }
        });
    });
}
async function getFilesAlmacenInspection(idProveedor,idCotizacion){
    spinner.show();

    const url = base_url + 'CargaConsolidada/ContenedorConsolidado/getFilesAlmacenInspection/'+idProveedor;
    return new Promise((resolve, reject) => {
        $.ajax({
            url: url,
            method: 'GET',
            processData: false,
            contentType: false,
            success: function (data) {
                const result = JSON.parse(data);
                if (result.status === 'success') {
                    resolve(result.data);
                } else {
                    reject(result.message);
                }
            },
            error: function () {
                reject('Error al subir el archivo');
            },
            complete: function () {
                spinner.hide();
            }
        });
    });
}
async function uploadFileDocument(file,fileManager){
    spinner.show();
    const formData = new FormData();
    formData.append('file', file);
    formData.append('idProveedor',currentProveedor);
    formData.append('idCotizacion',currentCotizacion);
    const url = base_url + 'CargaConsolidada/ContenedorConsolidado/uploadFileDocument';
    return new Promise((resolve, reject) => {
        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (data) {
                const result = JSON.parse(data);
                if (result.status === 'success') {
                    resolve(result.data);
                } else {
                    reject(result.message);
                }
            },
            error: function () {
                reject('Error al subir el archivo');
            },
            complete: function () {
                spinner.hide();
            }
        });
    });
}
async function uploadFileAlmacenInspection(file,fileManager){
    console.log("upload file inspection");
    spinner.show();
    const formData = new FormData();
    formData.append('file', file);
    formData.append('idProveedor',currentProveedor);
    formData.append('idCotizacion',currentCotizacion);
    const url = base_url + 'CargaConsolidada/ContenedorConsolidado/uploadFileAlmacenInspection';
    return new Promise((resolve, reject) => {
        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (data) {
                const result = JSON.parse(data);
                if (result.status === 'success') {
                    resolve(result.data);
                } else {
                    reject(result.message);
                }
            },
            error: function () {
                reject('Error al subir el archivo');
            },
            complete: function () {
                spinner.hide();
            }
        });
    });
    spinner.hide();
}
async function updateEstadoCotizacion(id,idCotizacion) {
    //get select value
    const estado = $(`#estado-cotizacion-${id}`).val();
   
    
    url = base_url + "CargaConsolidada/ContenedorConsolidado/updateEstadoCotizacion";
    $.ajax({
        url: url,
        type: "POST",
        data: {
            id: id,
            estado: estado
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
async function updateTelefonoProveedor(idProveedor){
    $telefono=$("#telefono-"+idProveedor).val();    
    url = base_url + "CargaConsolidada/ContenedorConsolidado/updateTelefonoProveedor";
    spinner.show();
    $.ajax({
        url: url,
        type: "POST",
        data: {
            idProveedor: idProveedor,
            telefono: $telefono
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
async function updateProveedor($idProveedor){
        $supplier=$("#proveedor-"+$idProveedor).val();
    url = base_url + "CargaConsolidada/ContenedorConsolidado/updateProveedor";
    spinner.show();
    $.ajax({
        url: url,
        type: "POST",
        data: {
            idProveedor: $idProveedor,
            supplier: $supplier
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
}
async function updateQtyChina($idProveedor){
    $qtyChina=$(`#qty-china-${$idProveedor}`).val();
    url = base_url + "CargaConsolidada/ContenedorConsolidado/updateQtyChina";
    spinner.show();
    $.ajax({
        url: url,
        type: "POST",
        data: {
            idProveedor: $idProveedor,
            qtyChina: $qtyChina
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
async function updateCBMChina($idProveedor){
    $cbmChina=$(`#cbm-china-${$idProveedor}`).val();
    url = base_url + "CargaConsolidada/ContenedorConsolidado/updateCBMChina";
    spinner.show();
    $.ajax({
        url: url,
        type: "POST",
        data: {
            idProveedor: $idProveedor,
            cbmChina: $cbmChina
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
async function updateProveedorData(idCotizacion,idProveedor){
    let telefono=$("#telefono-"+idProveedor).val();    
    let supplier=$("#proveedor-"+idProveedor).val();
    let qtyChina=$(`#qty-china-${idProveedor}`).val();
    let cbmChina=$(`#cbm-china-${idProveedor}`).val();
    let arriveDateChina=$(`#arrive-date-china-${idProveedor}`).val();
    let codigoSupplier=$(`#codigo-${idProveedor}`).val();
    let productos=$(`#productos-${idProveedor}`).val();
    //return fields not empty and not nul
    let data={};
    if(telefono!=""){
        data.supplier_phone=telefono;
    }
    if(supplier!=""){
        data.supplier=supplier;
    }
    if(qtyChina!=""){
        data.qty_box_china=qtyChina;
    }
    if(cbmChina!=""){
        data.cbm_total_china=cbmChina;
    }
    if(arriveDateChina!=""){
        data.arrive_date_china=arriveDateChina;
    }
    if(codigoSupplier!=""){
        data.code_supplier=codigoSupplier;
    }
    if(productos!=""){
        data.products=productos;
    }   

    url = base_url + "CargaConsolidada/ContenedorConsolidado/updateProveedorData";
    spinner.show();
    $.ajax({
        url: url,
        type: "POST",
        data: {
            idProveedor: idProveedor,
            idCotizacion:idCotizacion,
            data:data
        },
        success: function (response) {
            const result = JSON.parse(response);
            if (result.status == "success") {
                Swal.fire("Correcto!", result.message, "success");
                reloadTableCotizacionEmbarque();
            } else {
                Swal.fire("Error!", result.message, "error");
            }
            spinner.hide();
        },
        error:function(){
            spinner.hide();
        }
    });
    

}
async function updateArriveDateChina($idProveedor){
    $arriveDateChina=$(`#arrive-date-china-${$idProveedor}`).val();
    url = base_url + "CargaConsolidada/ContenedorConsolidado/updateArriveDateChina";
    spinner.show();
    $.ajax({
        url: url,
        type: "POST",
        data: {
            idProveedor: $idProveedor,
            arriveDateChina: $arriveDateChina
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
async function updateProductos($idProveedor,idCotizacion){
    $productos=$(`#productos-${$idProveedor}`).val();
    
    url = base_url + "CargaConsolidada/ContenedorConsolidado/updateProductos";
    spinner.show();
    $.ajax({
        url: url,
        type: "POST",
        data: {
            idProveedor: $idProveedor,
            productos: $productos
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
async function updateEstadoProveedor($idCotizacion,$idProveedor){
    $estado=$(`#estado-${$idProveedor}`).val();
    url = base_url + "CargaConsolidada/ContenedorConsolidado/updateEstadoProveedor";
    spinner.show();
    $.ajax({
        url: url,
        type: "POST",
        data: {
            idCotizacion: $idCotizacion,
            idProveedor: $idProveedor,
            estado: $estado
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
async function updateEstadoCotizacionProveedor(idCotizacion,idProveedor,previousStatus){
    const estado = $(`#estado-${idCotizacion}-${idProveedor}`).val();
    //set this previous status
    previousStatus=$(`#estado-${idCotizacion}-${idProveedor}`).data("previous");
    //validate if all products are filled
    allProducts=$(`.cotizacion-products-${idCotizacion}`);
    isValid=true;
    allProducts.each(function(){
        if($(this).val()==""){
            isValid=false;

        }
    });
    url = base_url + "CargaConsolidada/ContenedorConsolidado/updateEstadoCotizacionProveedor";
    spinner.show();
    if(estado=="ROTULADO"){
        if(!isValid){
            Swal.fire("Error!", "Debe ingresar todos los productos", "error");
            //set current select previous status
            $(`#estado-${idCotizacion}-${idProveedor}`).val(previousStatus);
            console.log(previousStatus);
            spinner.hide();
            return;
        }
        url = base_url + "CargaConsolidada/ContenedorConsolidado/updateEstadoCotizacionProveedor";
        $.ajax({
            url: url,
            type: "POST",
            data: {
                idCotizacion: idCotizacion,
                idProveedor:idProveedor,
                estado: estado
            },
            //blob
            xhrFields: {
                responseType: 'blob'
            },
            success: function (response) {
                var blob = new Blob([response], { type: //zip
                    'application/zip'
                });
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = `Cotizacion-${idCotizacion}.zip`;
                link.click();
            },
            error:function(){
                //set current select previous status
                $(`#estado-${idCotizacion}-${idProveedor}`).val(previousStatus);
                Swal.fire("Error!", "Hubo un error", "error"); 
                spinner.hide();
            }
        });
    }else{
        url = base_url + "CargaConsolidada/ContenedorConsolidado/updateEstadoCotizacionProveedor";
        $.ajax({
            url: url,
            type: "POST",
            data: {
                idCotizacion: idCotizacion,
                idProveedor:idProveedor,
                estado: estado
            },
            //blob
           
            success: function (response) {
                //manage blob
                const result = JSON.parse(response);
                if (result.status == "success") {
                    Swal.fire("Correcto!", result.message, "success");
                    reloadTableCotizacionEmbarque();
                }
                else {
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
async function viewSteps(id) {
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
            spinner.hide()
            const data = result.data;
            currentPrivilege=result.currentPrivilege;
            //here
            if(currentPrivilege=="ContenedorAlmacen"){
                openStepFunction(1,id);
                mainContainer.hide();
                return;
            }
            mainContainer.hide();
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
    mainContainer.show();
    table_Entidad.ajax.reload();
}
async function deleteCarga(id) {
    Swal.fire({
        title: "¿Estás seguro?",
        text: "¡No podrás revertir esto!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminarlo",
        cancelButtonText: "No, cancelar",
    }).then((result) => {
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
            url = base_url + "CargaConsolidada/ContenedorConsolidado/deleteCotizacionFile/" + id;
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
            url = base_url + "CargaConsolidada/ContenedorConsolidado/deleteCotizacion/" + id;
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
            url = base_url + "CargaConsolidada/ContenedorConsolidado/deleteCliente/" + idCotizacion;
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
const stepTemplate = (step, i) => {
    const stepHTML = `
        <div class="step-container" onclick="openStepFunction(${i + 1},${step.id
        })"  id="step-${i}">
        <span class="step">${step.name}</span>
        <img src="${step.iconURL} " class="step-icon w-100" />
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
const reloadTableCotizacion = async() => {
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
            estado: estado
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
}
const openStepFunction = async (step, id) => {
    stepIndex = step;
    stepId = id;
    spinner.show();
    stepsContainer.hide();
    url = base_url + "CargaConsolidada/ContenedorConsolidado/step";
    if (stepIndex == 1) {
        cotizacionContainer.show();        
        if(currentPrivilege=="ContenedorAlmacen"){
            $("#table-cotizacion-prospectos").attr("style", "display:none");
            if ($.fn.DataTable.isDataTable("#table-cotizacion-embarque")) {
                reloadTableCotizacionEmbarque();
            }else{
                url = base_url + "CargaConsolidada/ContenedorConsolidado/step";
                                tableCotizacionEmbarque.show();
                                tableCotizacionEmbarque = $('#table-cotizacion-embarque').DataTable({
                                    dom:
                                        "<'row'<'col-sm-12 col-md-7'B><'col-sm-12 col-md-4'f><'col-sm-12 col-md-1'>>" +
                                        "<'row'<'col-sm-12'tr>>" +
                                        "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-5'i><'col-sm-12 col-md-5'p>>",
                                    buttons: [
                                        {
                                            extend: "excel",
                                            text: '<i class="fa fa-file-excel color_icon_excel"></i> Excel',
                                            titleAttr: "Excel",
                                            action: function () {
                                                url=base_url+"CargaConsolidada/ContenedorConsolidado/downloadContenedorCotizacionProveedoresExcel/"+idContenedor;
                                                //AJAX MULTIPART FOR EXCEL
                                                $.ajax({
                                                    url: url,
                                                    type: "GET",
                                                    xhrFields: {
                                                        responseType: 'blob'
                                                    },
                                                    success: function (response) {
                                                        //excel
                                                        var blob = new Blob([response], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                                                        var link = document.createElement('a');
                                                        link.href = window.URL.createObjectURL(blob);
                                                        link.download = `Cotizacion-${idContenedor}.xlsx`;
                                                        link.click();
                                                        
                                                    },
                                                    error:function(){
                                                        Swal.fire("Error!", "Hubo un error", "error");
                                                    }
                                                });
                                                
                                            }
                                        },
                                        {
                                            extend: "colvis",
                                            text: '<i class="fa fa-ellipsis-v"></i> Columnas',
                                            titleAttr: "Columnas",
                                            exportOptions: {
                                                columns: ":visible",
                                            },
                                        },                              
                                        {
                                            text: "Por Embarcar",
                                            className: "btn btn-light",
                                            action: function () {
                                                if ($.fn.DataTable.isDataTable("#table-cotizacion-prospectos")) {
                                                    $("#table-cotizacion-prospectos").attr("style", "display:none");
                                                    $("#table-cotizacion-prospectos_wrapper").hide();
                                                }
                                                if ($.fn.DataTable.isDataTable("#table-cotizacion-embarque")) {

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
                                                currentTableCotizacion="embarque";
                                            }
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
                                        {
                                            targets: "sorting_asc",
                                            orderable: false,
                                        }
                                    ],
                                    order: [[1, "asc"]],
                                    ajax: {
                                        url: url,
                                        type: "POST",
                                        dataType: "JSON",
                                        data:   function (data) {
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
                                        }
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
                                        $('.input-date').datepicker({
                                            autoclose: true,
                                            startDate: new Date(fYear, fToday.getMonth(), fDay),
                                            todayHighlight: true,
                                            format: "dd/mm/yyyy",
                                            dateFormat: "dd/mm/yyyy",
                                        });
                                    },
                                    drawCallback: function (settings) {
                                        $('.input-date').datepicker({
                                            autoclose: true,
                                            startDate: new Date(fYear, fToday.getMonth(), fDay),
                                            todayHighlight: true,
                                            format: "dd/mm/yyyy",
                                            dateFormat: "dd/mm/yyyy",
                                        });
                                    }
                                });
            }
            spinner.hide();
        }else{
        if(currentTableCotizacion!="prospectos"){
            reloadTableCotizacionEmbarque();
        }
        if ($.fn.DataTable.isDataTable("#table-cotizacion-prospectos") && currentTableCotizacion!="embarque") {
            reloadTableCotizacion();
            
        } else if(currentTableCotizacion=="prospectos") {
            tableCotizacion = $('#table-cotizacion-prospectos').DataTable({
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
                    {
                        text: "Prospectos",
                        action: function () {
                            if ($.fn.DataTable.isDataTable("#table-cotizacion-embarque")) {
                                $("#table-cotizacion-embarque").attr("style", "display:none");
                                $("#table-cotizacion-embarque_wrapper").hide();
                            }
                            if ($.fn.DataTable.isDataTable("#table-cotizacion-prospectos")) {
                                //display block
                                $("#table-cotizacion-prospectos").attr("style", "");
                                $("#table-cotizacion-prospectos_wrapper").show();
                                reloadTableCotizacion();
                            } else {
                                $("#table-cotizacion-prospectos").attr("style", "");
                                $("#table-cotizacion-prospectos_wrapper").show();
                                reloadTableCotizacion();

                            }
                            currentTableCotizacion="prospectos";
                            
                        },
                        className: "btn btn-light"
                    },
                    {
                        text: "Por Embarcar",
                        action:async function () {
                            if ($.fn.DataTable.isDataTable("#table-cotizacion-prospectos")) {
                                $("#table-cotizacion-prospectos").attr("style", "display:none");
                                $("#table-cotizacion-prospectos_wrapper").hide();
                            }
                            if ($.fn.DataTable.isDataTable("#table-cotizacion-embarque")) {

                                $("#table-cotizacion-embarque").attr("style", "");
                                $("#table-cotizacion-embarque_wrapper").show();
                                reloadTableCotizacionEmbarque();
                            } else {
                                url = base_url + "CargaConsolidada/ContenedorConsolidado/step";
                                tableCotizacionEmbarque.show();
                                tableCotizacionEmbarque = $('#table-cotizacion-embarque').DataTable({
                                    dom:
                                        "<'row'<'col-sm-12 col-md-7'B><'col-sm-12 col-md-4'f><'col-sm-12 col-md-1'>>" +
                                        "<'row'<'col-sm-12'tr>>" +
                                        "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-5'i><'col-sm-12 col-md-5'p>>",
                                    buttons: [
                                        {
                                             extend: "excel",
                        text: '<i class="fa fa-file-excel color_icon_excel"></i> Excel',
                        titleAttr: "Excel",
                        action: function () {
                            url=base_url+"CargaConsolidada/ContenedorConsolidado/downloadContenedorCotizacionProveedoresExcel/"+idContenedor;
                            //AJAX MULTIPART FOR EXCEL
                            $.ajax({
                                url: url,
                                type: "GET",
                                xhrFields: {
                                    responseType: 'blob'
                                },
                                success: function (response) {
                                    //excel
                                    var blob = new Blob([response], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                                    var link = document.createElement('a');
                                    link.href = window.URL.createObjectURL(blob);
                                    link.download = `Cotizacion-${idContenedor}.xlsx`;
                                    link.click();
                                    
                                },
                                error:function(){
                                    Swal.fire("Error!", "Hubo un error", "error");
                                }
                            });
                            
                        }
                                        },

                                        {
                                            extend: "colvis",
                                            text: '<i class="fa fa-ellipsis-v"></i> Columnas',
                                            titleAttr: "Columnas",
                                            exportOptions: {
                                                columns: ":visible",
                                            },
                                        },
                                        {
                                            text: "Prospectos",
                                            action: function () {
                                                if ($.fn.DataTable.isDataTable("#table-cotizacion-embarque")) {
                                                    $("#table-cotizacion-embarque").attr("style", "display:none");
                                                    $("#table-cotizacion-embarque_wrapper").hide();
                                                }
                                                if ($.fn.DataTable.isDataTable("#table-cotizacion-prospectos")) {
                                                    $("#table-cotizacion-prospectos_wrapper").show();

                                                    $("#table-cotizacion-prospectos").attr("style", "");
                                                    reloadTableCotizacion();


                                                } else {
                                                    $("#table-cotizacion-prospectos").attr("style", "");
                                                    reloadTableCotizacion();

                                                }
                                                currentTableCotizacion="prospectos";
                                            },
                                        },
                                        {
                                            text: "Por Embarcar",
                                            className: "btn btn-light",
                                            action: function () {
                                                if ($.fn.DataTable.isDataTable("#table-cotizacion-prospectos")) {
                                                    $("#table-cotizacion-prospectos").attr("style", "display:none");
                                                    $("#table-cotizacion-prospectos_wrapper").hide();
                                                }
                                                if ($.fn.DataTable.isDataTable("#table-cotizacion-embarque")) {

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
                                                currentTableCotizacion="embarque";
                                            }
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
                                            targets:"",
                                            orderable:false
                                        }

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
                                        }
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
                               await  getTableCotizacionEmbarqueHeaders();
                            }
                            currentTableCotizacion="embarque";
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
                        targets:"",
                        orderable:false
                    }

                ],
                lengthMenu: [
                    [10, 100, 1000, -1],
                    [10, 100, 1000, "Todos"],
                ],
            });
          
        }
        await getTipoCliente();

    }
        

    } else if (stepIndex == 2) {
        await getClientesHeader();
        url = base_url + "CargaConsolidada/ContenedorConsolidado/step";
        clientesContainer.show();
        if ($.fn.DataTable.isDataTable("#table-clientes-general")) {
            reloadTableClientesGeneral();
            
        } else {
            tableClientesGeneral.show();

            tableClientesGeneral = $('#table-clientes-general').DataTable({
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
                        extend: "colvis",
                        text: '<i class="fa fa-ellipsis-v"></i> Columnas',
                        titleAttr: "Columnas",
                        exportOptions: {
                            columns: ":visible",
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
                        className: "btn btn-light"
                    },
                    {
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
                                url = base_url + "CargaConsolidada/ContenedorConsolidado/step";
                                tableClientesVariacion.show();
                                tableClientesVariacion = $('#table-clientes-variacion').DataTable({
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
                                        },

                                        {
                                            extend: "colvis",
                                            text: '<i class="fa fa-ellipsis-v"></i> Columnas',
                                            titleAttr: "Columnas",
                                            exportOptions: {
                                                columns: ":visible",
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
                                                if ($.fn.DataTable.isDataTable("#table-clientes-general")) {
                                                    $("#table-clientes-general").attr("style", "display:none");
                                                    $("#table-clientes-general_wrapper").hide();
                                                }
                                                if ($.fn.DataTable.isDataTable("#table-clientes-variacion")) {

                                                    $("#table-clientes-variacion").attr("style", "");

                                                } else {
                                                    $("#table-clientes-variacion").attr("style", "");
                                                }
                                            }
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
                                        }
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
                                        }
                                    }
                                });
                            }

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
                    }
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
                        data.estado = $("#txt-ID_Estado_Cliente").val();
                    }
                }
            });
        }
    } else if (stepIndex == 3) {
        viewDocumentacion();
    } else if (stepIndex==4){
        viewCotizacionFinal();
    }
    $(".btn-back-cotizacion").off("click");
    $(".btn-back-cotizacion").on("click", function () {
        if (currentPrivilege == "ContenedorAlmacen") {
            mainContainer.show();
            cotizacionContainer.hide();
            stepsContainer.hide();
        } else {
        returnToSteps();
        }
    });

    spinner.hide();
}
async function viewCotizacionFinal(){
    cotizacionFinalContainer.show();
    spinner.show();
    url=base_url+"CargaConsolidada/ContenedorConsolidado/step";
    if($.fn.DataTable.isDataTable("#table-cotizacion-final")){
        tableCotizacionFinal.ajax.reload();
    }else{
        tableCotizacionFinal.show();

        tableCotizacionFinal=$('#table-cotizacion-final').DataTable({
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
                }
            ],
            ajax: {
                url: url,
                type: "POST",
                dataType: "JSON",
                data: function (data) {
                    data.stepIndex = stepIndex;
                    data.idContenedor = idContenedor;
                }
            },
            initComplete: function (settings, json) {
                spinner.hide();
            },
        });
    }
}
async function  deleteDocumentacionFolder(id) {
    Swal.fire({
        title: "¿Estás seguro?",
        text: "¡No podrás revertir esto!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminarlo",
        cancelButtonText: "No, cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            url = base_url + "CargaConsolidada/ContenedorConsolidado/deleteDocumentacionFolder/" + id;
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
    url = base_url + "CargaConsolidada/ContenedorConsolidado/updateEstadoCotizador";
    $.ajax({
        url: url,
        type: "POST",
        data: {
            id: id,
            estado: estado
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
                    <div >
                        <label>${file.folder_name}
                        ${file.id_contenedor?`<div class="badge badge-danger text-white delete-folder-button
                            
                            " onclick="deleteDocumentacionFolder(${file.id})">
                            X
                            </div>`
                            :""}
                        </label>
                        ${file.file_url ? `<div>
                            <a href="${file.file_url}" target="_blank" class="btn btn-outline-primary">
                            <i class="fa fa-download"></i>
                            Descargar
                            </a>
                            <button class="btn btn-outline-danger" onclick="deleteDocumentacionFile(${file.id_file})">
                            <i class="fa fa-trash " ></i>
                            </button>
                        </div>` :
                        `<div>
                            <button class="btn btn-outline-primary" onclick="openUploadFileDocumentation(${file.id})">
                            <i class="fa fa-upload"></i>
                            Subir
                            </button>
                        </div>`}
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

async function reloadTableCotizacionEmbarque(){
    tableCotizacionEmbarque.ajax.reload()
    await getTableCotizacionEmbarqueHeaders(); 
}
async function reloadTableCotizacionFinal(){
    tableCotizacionFinal.ajax.reload()
}
async function getTableCotizacionEmbarqueHeaders(){
    url=base_url+"CargaConsolidada/ContenedorConsolidado/getCotizacionEmbarqueHeaders/"+idContenedor;

    const response=await fetch(url);
    const result=await response.json();
    $("#txt-CBM_Total_Peru").val(result.cbm_total);
    $("#txt-CBM_Total_China").val(result.cbm_total_china);
    //if result.lista_embarque_url is not null add button to download else file input with button to upload remember remove and add event listener
    if(result.lista_embarque_url){
        $("#packing-list-container").empty();
        $("#packing-list-container").append(`
        <a href="${result.lista_embarque_url}" target="_blank" class="btn btn-outline-primary">
        <i class="fa fa-download"></i>
        Descargar
        </a>
        <button class="btn btn-outline-danger" onclick="deleteListaEmbarque()">
        <i class="fa fa-trash " ></i>
        </button>
        `);
        //add event for delete 
        
    }else{
        $("#packing-list-container").empty();
        $("#packing-list-container").append(`
        <button class="btn btn-outline-primary"
        id="btn-upload-lista-embarque"
        >
        <i class="fa fa-upload"></i>
        Subir
        </button>
   
        `);

        $("#btn-upload-lista-embarque").off("click");
        $("#btn-upload-lista-embarque").on("click",async function(){
            //open swall with input file
            const { value: file } = await Swal.fire({
                title: 'Subir lista de embarque',
                input: 'file',
                inputAttributes: {
                    'accept': '*',
                    'aria-label': 'Sube tu archivo',
                },
                showCancelButton: true,
                confirmButtonText: 'Subir',
                showLoaderOnConfirm: true,
                preConfirm: (file) => {
                    const formData = new FormData();
                    formData.append('file', file);
                    formData.append("idContenedor", idContenedor);
                    formData.append("idCotizacion", idCotizacion);
                    return fetch(base_url + 'CargaConsolidada/ContenedorConsolidado/uploadListaEmbarque', {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => {
                            getTableCotizacionEmbarqueHeaders();

                            return response.json()
                        })
                        .catch(error => {
                            Swal.showValidationMessage(
                                `Request failed: ${error}`
                            )
                        })
                },
                allowOutsideClick: () => !Swal.isLoading()
            })

        });


    }
    if(result.bl_file_url){
        $("#bl-file-container").empty();
        $("#bl-file-container").append(`
        <a href="${result.bl_file_url}" target="_blank" class="btn btn-outline-primary">
        <i class="fa fa-download"></i>
        Descargar
        </a>
        <button class="btn btn-outline-danger" onclick="deleteBL()">
        <i class="fa fa-trash " ></i>
        </button>
        `);
        
    }else{
        $("#bl-file-container").empty();
        $("#bl-file-container").append(`
            <button class="btn btn-outline-primary"
            id="btn-upload-bl" >
            <i class="fa fa-upload"></i>
            Subir
        </button>
       
        `);

        $("#btn-upload-bl").off("click");
        $("#btn-upload-bl").on("click",async function(){
            //open swall with input file
            const { value: file } = await Swal.fire({
                title: 'Subir BL',
                input: 'file',
                inputAttributes: {
                    'accept': '*',
                    'aria-label': 'Sube tu archivo',
                },
                showCancelButton: true,
                confirmButtonText: 'Subir',
                showLoaderOnConfirm: true,
                preConfirm: (file) => {
                    const formData = new FormData();
                    formData.append('file', file);
                    formData.append('id', idContenedor);
                    return fetch(base_url + 'CargaConsolidada/ContenedorConsolidado/uploadBL', {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => {
                            getTableCotizacionEmbarqueHeaders();

                            return response.json()
                            //call header again
                        })
                        .catch(error => {
                            Swal.showValidationMessage(
                                `Request failed: ${error}`
                            )
                        })
                },
                allowOutsideClick: () => !Swal.isLoading()
            })
        });
        
    }
}

async function deleteBL(){
    Swal.fire({
        title: "¿Estás seguro?",
        text: "¡No podrás revertir esto!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminarlo",
        cancelButtonText: "No, cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            url = base_url + "CargaConsolidada/ContenedorConsolidado/deleteBL/" + idContenedor;
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
async function deleteListaEmbarque(){
    Swal.fire({
        title: "¿Estás seguro?",
        text: "¡No podrás revertir esto!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminarlo",
        cancelButtonText: "No, cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            url = base_url + "CargaConsolidada/ContenedorConsolidado/deleteListaEmbarque/" + idContenedor;
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
    Swal.fire({
        title: "¿Estás seguro?",
        text: "¡No podrás revertir esto!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminarlo",
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
async function openUploadFileDocumentation(idFolder) {
    Swal.fire({
        title: "Subir archivo",
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
            formData.append("idFolder", idFolder);
            formData.append("idContenedor", idContenedor);
            return fetch(base_url + "CargaConsolidada/ContenedorConsolidado/uploadFileDocumentation", {
                method: "POST",
                body: formData,
            })
                .then((response) => {
                    return response.json();
                })
                .catch((error) => {
                    Swal.showValidationMessage(
                        `Request failed: ${error}`
                    );
                });
        },
        allowOutsideClick: () => !Swal.isLoading(),
    }).then((result) => {
        if (result.value) {
            if (result.value.status == "success") {
                Swal.fire("Correcto", result.value.message, "success");
                viewDocumentacion();
            } else {
                Swal.fire("Error", result.value.message, "error");
            }
        }
    });
}
async function viewCotizacion(id) {
    url = base_url + "CargaConsolidada/ContenedorConsolidado/showCotizacion/" + id;
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
        $("#txt-CotizacionFile").val(result.cotizacion_file)
    }
    $("#modal-crear-cotizacion").modal("show");
    $("#btn-guardar-cotizacion").hide();
    $("#btn-actualizar-cotizacion").show();
    idCotizacion = result.id;
}
async function uploadCotizacionFile(id) {
    //swall with input file
    const { value: file } = await Swal.fire({
        title: 'Subir cotización',
        input: 'file',
        inputAttributes: {
            'accept': '*',
            'aria-label': 'Sube tu archivo',
        },
        showCancelButton: true,
        confirmButtonText: 'Subir',
        showLoaderOnConfirm: true,
        preConfirm: (file) => {
            const formData = new FormData();
            formData.append('file', file);
            formData.append('id', id);
            return fetch(base_url + 'CargaConsolidada/ContenedorConsolidado/uploadCotizacionFile', {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    return response.json()
                })
                .catch(error => {
                    Swal.showValidationMessage(
                        `Request failed: ${error}`
                    )
                })
        },
        allowOutsideClick: () => !Swal.isLoading()
    })
    if (file) {
        if (file.status == "success") {
            Swal.fire({
                icon: 'success',
                title: 'Correcto',
                text: file.message,
            })
            reloadTableCotizacion();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: file.message,
            })
        }
    }
}
async function viewClientesDocumentacion(id) {
    idCotizacion = id;
    clientesContainer.hide();
    url = base_url + "CargaConsolidada/ContenedorConsolidado/showClientesDocumentacion/" + id;
    spinner.show();
    const response = await fetch(url);
    const result = await response.json();
    $("#txt-Vol_Doc").val(result[0].volumen_doc);
    $("#txt-Valor_Doc").val(result[0].valor_doc);
    //set txt-F_Comercial href
    const facturaComercial = result[0].factura_comercial;
    const excelConfirmacion= result[0].excel_confirmacion;
    if (facturaComercial) {
        $("#factura-comercial").empty();
        const facturaDiv = `
        <div class="d-flex flex-row gap-1">
            <div>
                <a href="${facturaComercial}" target="_blank" class="btn btn-outline-primary">
                <i class="fa fa-download"></i>
                Descargar
                </a>
           </div>
            <div class="btn btn-outline-danger" onclick="deleteFacturaComercial(${idCotizacion})">
            <i class="fa fa-trash " ></i>
            </div>
        </div>  
        `
        $("#factura-comercial").append(facturaDiv);
    }
    else {
        $("#factura-comercial").empty();
        $("#factura-comercial").append(`<input type="file" id="txt-F_Comercial" name="file_comercial" class="form-control" required>`);
    }
    if (excelConfirmacion) {
        $("#excel-confirmacion").empty();
        const facturaDiv = `
        <div class="d-flex flex-row gap-1">
            <div>
                <a href="${excelConfirmacion}" target="_blank" class="btn btn-outline-primary">
                <i class="fa fa-download"></i>
                Descargar
                </a>
           </div>
            <div class="btn btn-outline-danger" onclick="deleteExcelConfirmacion(${idCotizacion})">
            <i class="fa fa-trash " ></i>
            </div>
        </div>  
        `
        $("#excel-confirmacion").append(facturaDiv);
    }
    else {
        $("#excel-confirmacion").empty();
        $("#excel-confirmacion").append(`<input type="file" id="txt-F_Comercial" name="excel_confirmacion" class="form-control" required>`);
    }
    //clean .aditional-file
    $(".aditional-file").remove();
    const filesDoc=JSON.parse(result[0].files_almacen_documentacion ?? '[]');
    const files = JSON.parse(result[0].files ?? '[]');
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

    $("#btn-descargar-cotizacion-inicial").attr("href", result[0].cotizacion_file_url);
    clientesDocumentacionContainer.show();
    fileManagerInspectionCoordinacion = new FileManager({
        fileGrid: "#file-grid-inspection-coordinacion",
        fileInput: "#file-input-modal-inspection-coordinacion",
        uploadBtn: "#btn-upload-inspection-coordinacion",
        dragDropContainer: "#drag-drop-container-inspection-coordinacion",
        searchInput: "#search-input-inspection-coordinacion",
        pendingFileList: "#pending-file-list-inspection-coordinacion",
        onFileUpload:(file)=> uploadFileAlmacenInspection(file,fileManagerInspectionCoordinacion),
        // onLoadFiles:()=>getFilesAlmacenInspection(idProveedor,idCotizacion),
      });
    spinner.hide();

}
async function validateListEmbarque(id) {
    url = base_url + "CargaConsolidada/ContenedorConsolidado/validateListEmbarque/" + id;
    const response = await fetch(url);
    const result = await response.json();
    if (result.status) {
        //from $(".btn-lista-embarque") remove btn-secondary and add btn-success
        $(".btn-lista-embarque").removeClass("btn-secondary").addClass("btn-success");
    } else {
    }
    // reloadTableClientesVariacion();
}
async function updateVolSelected(idCotizacion,type){
    //show confirm swal to confirm asign this tarifa for factura general
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Estás a punto de asignar esta tarifa a la factura general",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, asignar',
        cancelButtonText: 'No, cancelar',
        reverseButtons: true
      }).then((result) => {
        if (result.isConfirmed) {
            url = base_url + "CargaConsolidada/ContenedorConsolidado/updateVolSelected";
            $.ajax({
                url: url,
                type: "POST",
                data: {
                    idCotizacion: idCotizacion,
                    type:type
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
      })
}
async function deleteFacturaComercial(id) {
    Swal.fire({
        title: "¿Estás seguro?",
        text: "¡No podrás revertir esto!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminarlo",
        cancelButtonText: "No, cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            url = base_url + "CargaConsolidada/ContenedorConsolidado/deleteFacturaComercial/" + id;
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
    Swal.fire({
        title: "¿Estás seguro?",
        text: "¡No podrás revertir esto!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminarlo",
        cancelButtonText: "No, cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            url = base_url + "CargaConsolidada/ContenedorConsolidado/deleteExcelConfirmacion/" + id;
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
    Swal.fire({
        title: "¿Estás seguro?",
        text: "¡No podrás revertir esto!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminarlo",
        cancelButtonText: "No, cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            url = base_url + "CargaConsolidada/ContenedorConsolidado/deleteClienteDocumentacionFile/" + id;
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
const returnToSteps = () => {
    cotizacionContainer.hide();
    clientesContainer.hide();
    documentationContainer.hide();

    stepsContainer.show();

}
$(document).ready(async function () {
    spinner = $(".backdrop");
    stepsContainer = $("#steps");
    mainContainer = $("#main-container");
    stepsContainer.hide();
    cotizacionContainer = $("#cotizacion-container");
    cotizacionContainer.hide();
    tableCotizacion = $("#table-cotizacion-prospectos");
    tableCotizacionEmbarque=$("#table-cotizacion-embarque");
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
    cotizacionInspectionContainer=$("#cotizacion-almacen-inspeccion");
    cotizacionInspectionContainer.hide();
    cotizacionAlmacenContainer=$("#cotizacion-almacen");
    cotizacionAlmacenContainer.hide();
    dropZone = $('#drop-zone');
    fileInput = $('#file-input');
    fileList = $('#file-list');
    cotizacionFinalContainer=$("#cotizacion-final-container");
    cotizacionFinalContainer.hide();
    tableCotizacionFinal=$("#table-cotizacion-final");
    tableCotizacionFinal.hide();
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
                targets:"",
                orderable:false
            }
        ],
        lengthMenu: [
            [10, 100, 1000, -1],
            [10, 100, 1000, "Todos"],
        ],
    });
    $("#upload-documents").click(() => $("#upload-input-documents").click());
    $("#upload-inspection").click(() => $("#upload-input-inspection").click());

    // Listeners para subir archivos
    $("#upload-input-documents").change(function () {
        handleFileUpload(this.files, "documents");
    });

    $("#upload-input-inspection").change(function () {
        handleFileUpload(this.files, "inspection");
    });

    // Función para manejar la subida de archivos
    function handleFileUpload(files, section) {
        const formData = new FormData();
        formData.append('idProveedor', currentProveedor);
        url=base_url + "CargaConsolidada/ContenedorConsolidado/uploadFileAlmacenInspection";

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
        const container = section === "documents" ? $("#documents") : $("#inspection");

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
                        <button class="btn btn-sm btn-primary download-btn" data-url="${file.file_url}">Download</button>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="${file.id}">Delete</button>
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
            card.find(".delete-btn").click(function () {
                const fileId = $(this).data("id");
                deleteFile(fileId, card);
            });
        });
    }

    // Función para eliminar archivos
    function deleteFile(fileId, cardElement) {
        $.ajax({
            url: base_url + "CargaConsolidada/ContenedorConsolidado/deleteFile/"+fileId,
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


    // function initDropZone(dropZoneId, inputId, fileListId) {
    //     const dropZone = $(`#${dropZoneId}`);
    //     const fileInput = $(`#${inputId}`);
    //     

    //     // Maneja el evento de clic
    //     dropZone.on('click', function () {
    //         fileInput.click();
    //     });

    //     // Maneja el cambio del input de archivos
    //     fileInput.on('change', function (event) {
    //         handleFiles(event.target.files, fileList);
    //     });

    //     // Maneja arrastrar y soltar
    //     dropZone.on('dragover', function (event) {
    //         event.preventDefault();
    //         dropZone.addClass('dragover');
    //     });

    //     dropZone.on('dragleave', function () {
    //         dropZone.removeClass('dragover');
    //     });

    //     dropZone.on('drop', function (event) {
    //         event.preventDefault();
    //         dropZone.removeClass('dragover');
    //         const files = event.originalEvent.dataTransfer.files;
    //         handleFiles(files, fileList);
    //     });

    //     // Maneja los archivos subidos
    //     function handleFiles(files, fileList) {
    //         const uploadedFiles = Array.from(files);
    //         uploadedFiles.forEach(file => {
    //             const listItem = $('<div>')
    //                 .addClass('file-list-item text-secondary')
    //                 .text(file.name);
    //             fileList.append(listItem);
    //         });

    //         // Realiza la subida con AJAX
    //         uploadFiles(uploadedFiles);
    //     }

    //     // Función para subir archivos con AJAX
    //     function uploadFiles(files) {
    //         const formData = new FormData();
    //         formData.append('idProveedor', idProveedor);
    //         files.forEach(file => {
    //             formData.append('files[]', file); // Asegúrate de usar el nombre esperado por tu backend
    //         });
    //         url=base_url + "CargaConsolidada/ContenedorConsolidado/uploadFileInspection";
    //         $.ajax({
    //             url,
    //             type: 'POST',
    //             data: formData,
    //             processData: false,
    //             contentType: false,
    //             success: function (response) {
    //                 console.log('Archivos subidos con éxito:', response);
    //             },
    //             error: function (error) {
    //                 console.error('Error al subir archivos:', error);
    //             }
    //         });
    //     }
    // }

    // // Inicializa las zonas para Documents y Inspection
    // initDropZone('documents-dropzone', 'documents-input', 'documents-list');
    // initDropZone('inspection-dropzone', 'inspection-input', 'inspection-list');
    


    const fillSelects = async () => {
        $("#txt-ID_Pais").empty();
        $("#txt-Mes").empty();
        await getPaises();
        //APPEND DEFAULT OPTION DISABLED SELECT
        $("#txt-Mes").append('<option value="" disabled selected>Seleccione un mes</option>');
        meses.forEach((mes) => {
            $("#txt-Mes").append(
                `<option value="${mes.id}">${mes.value}</option>`
            );
        });
    }
    const getPaises = async () => {
        url = base_url + "CargaConsolidada/ContenedorConsolidado/getPaises";
        const response = await fetch(url);
        const result = await response.json();
        paises = result;
    
        $("#txt-ID_Pais").append('<option value="" disabled selected>Seleccione un país</option>');
        paises.forEach((pais) => {
            $("#txt-ID_Pais").append(
                `<option value="${pais.ID_Pais}">${pais.No_Pais}</option>`
            );
        });
    }
    await fillSelects();
    /**Start of Listeners */
    btnCrear = $("#btn-crear");
    btnCrear.on("click", async function () {
        $("#modal-crear").modal("show");
        $("#btn-guardar").show();
        //do fetch to get data from id
        const validContainers=await fetch(base_url + "CargaConsolidada/ContenedorConsolidado/getValidContainers");
        const parsedContainer=await validContainers.json();
        console.log(parsedContainer);
        $("#txt-No_Carga").empty();
        parsedContainer.forEach((container)=>{
            $("#txt-No_Carga").append(
                `<option value="${container}">Consolidado #${container}</option>`
            );
        });
        //off event listener
        $("#txt-ID_Pais").off("change");
        $("#txt-ID_Pais").on("change",function(){
            if($(this).val()==1){
                $("#txt-Empresa").val("PRO MUNDO COMEX SAC.");
            }else{
                $("#txt-Empresa").val("");
            }
        })
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
            form.classList.add('was-validated');
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
    })
    $(".btn-back-documentacion").click(function () {
        returnToSteps();
    })
    $("#btn-back-cotizacion-almacen").click(function () {
        cotizacionAlmacenContainer.hide();
        cotizacionContainer.show();
    })
    $("#btn-documentacion-zip").click(function () {
        spinner.show();
        url = base_url + "CargaConsolidada/ContenedorConsolidado/downloadDocumentacionZip/" + idContenedor;
        $.ajax({
            url: url,
            type: "GET",
            //data return multipart/form-data
            xhrFields: {
                responseType: 'blob'
            },
            success: function (response) {
                spinner.hide();
                const url = window.URL.createObjectURL(new Blob([response]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'documentacion.zip');
                document.body.appendChild(link);
                link.click();

            },

        });
    });
        $("#btn-guardar-cotizacion").click(function (e) {
            e.preventDefault();
            const formData = new FormData($("#form-crear-cotizacion")[0]);
            formData.append("id_contenedor", idContenedor);
            let form = $("#form-crear-cotizacion")[0];
            if (!form.checkValidity()) {
                form.classList.add('was-validated');
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
                        if(currentTableCotizacion=="prospectos"){
                            tableCotizacion.ajax.reload();
                        }else if(currentTableCotizacion=="embarque"){
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
                }
            });
        });
        $("#btn-actualizar-cotizacion").click(function (e) {
            e.preventDefault();
            const formData = new FormData($("#form-crear-cotizacion")[0]);
            formData.append("id", idCotizacion);
            let form = $("#form-crear-cotizacion")[0];
            if (!form.checkValidity()) {
                form.classList.add('was-validated');
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
                form.classList.add('was-validated');
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
        }
        );
        $("#modal-crear").on("hidden.bs.modal", function () {
            $("#form-crear")[0].reset();
        }
        );
        $("#btn-crear-documentacion").click(function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Crear documento',
                html:
                    '<input id="swal-input1" class="swal2-input" placeholder="Nombre del documento">' +
                    '<input type="file" id="swal-input2" class="swal2-file">',
                focusConfirm: false,
                buttonConfirmText: 'Crear',
                //COLOR BUTTONS
                confirmButtonColor: '#e67e22',
                showCancelButton: true,
                preConfirm: () => {
                    const name = Swal.getPopup().querySelector('#swal-input1').value
                    const file = Swal.getPopup().querySelector('#swal-input2').files[0]
                    if (!name || !file) {
                        Swal.showValidationMessage(`Por favor, complete todos los campos`)
                    }
                    return { name: name, file: file }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    spinner.show(); 

                    const formData = new FormData();
                    formData.append("name", result.value.name);
                    formData.append("file", result.value.file);
                    formData.append("id", idCotizacion);
                    $.ajax({
                        url: base_url + "CargaConsolidada/ContenedorConsolidado/createClienteDocumentacion",
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

            })


        });
        $("#btn-guardar-documentacion").click(function (e) {
            e.preventDefault();
            const formData = new FormData($("#form-documentacion")[0]);
            const check = $("#form-documentacion")[0].checkValidity();
            if (!check) {
                $("#form-documentacion")[0].classList.add('was-validated');
                return;
            }
            formData.append("id", idCotizacion);
            //f
            $.ajax({
                url: base_url + "CargaConsolidada/ContenedorConsolidado/updateClienteDocumentacion",
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
        $(".upload-btn").click(() =>{
            const file= $("#file-input-modal").prop('files')[0];

            uploadFileDocument(file,fileManager).then((response)=>{
                fileManager.data.driveFiles.push(response);
                fileManager.renderFileGrid(fileManager.data.driveFiles);
            });
            //hide modal
            $("#uploadModal").modal("hide");
        })
        $(".upload-btn-inspection").click(function () {
            const file=$("#file-input-modal-inspection").prop('files')[0];
            uploadFileAlmacenInspection(file,fileManagerInspection).then((response)=>{
                fileManagerInspection.data.driveFiles.push(response);
                fileManagerInspection.renderFileGrid(fileManagerInspection.data.driveFiles);
            }
            );
            $("#uploadModalInspection").modal("hide");
        })
        $("#btn-documentacion-factura").click(async function (e) {
            e.preventDefault();
            spinner.show();
            url=base_url + "CargaConsolidada/ContenedorConsolidado/downloadFacturaComercial/" + idContenedor;
            let isError=true;
            await $.ajax({
                url: url,
                type: "GET",
                //data return multipart/form-data
                xhrFields: {
                    responseType: 'blob'
                },
                success: function (response) {
                    isError=false;

                    spinner.hide();
                    //check if response is a file
                    const url = window.URL.createObjectURL(new Blob([response]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', 'FACTURA_GENERAL.xlsx');
                    document.body.appendChild(link);
                    link.click();
                    
                },
                error: function (response) {
                    isError=true;
                    spinner.hide();
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "No se pudo descargar la factura comercial",
                    });
                }
            

            });
            spinner.hide();
            if(isError){
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
                title: 'Crear documento',
                html:
                    '<input id="swal-input1" class="swal2-input" placeholder="Nombre del documento">' +
                    '<input type="file" id="swal-input2" class="swal2-file">',
                focusConfirm: false,
                buttonConfirmText: 'Crear',
                //COLOR BUTTONS
                confirmButtonColor: '#e67e22',
                showCancelButton: true,
                preConfirm: () => {
                    const name = Swal.getPopup().querySelector('#swal-input1').value
                    const file = Swal.getPopup().querySelector('#swal-input2').files[0]
                    if (!name || !file) {
                        Swal.showValidationMessage(`Por favor, complete todos los campos`)
                    }
                    return { name: name, file: file }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    spinner.show();

                    const formData = new FormData();
                    formData.append("name", result.value.name);
                    formData.append("file", result.value.file);
                    formData.append("idContenedor", idContenedor);
                    $.ajax({
                        url: base_url + "CargaConsolidada/ContenedorConsolidado/createDocumentacionFolder",
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
            })
        });
        $("#btn-buscar-clientes-general").click(function (e) {
            e.preventDefault();
            console.log("click");
            tableClientesGeneral.ajax.reload();
        });
        $("#uploadGeneral").click(() => {
            url=base_url + "CargaConsolidada/ContenedorConsolidado/uploadGeneral";
            const formData = new FormData();
            formData.append("idContenedor", idContenedor);
            //swall input file
            Swal.fire({
                title: 'Subir Factura General',
                input: 'file',
                inputAttributes: {
                    accept: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    
                },
                showCancelButton: true,
                confirmButtonText: 'Subir',
                showLoaderOnConfirm: true,
                preConfirm: async (file) => {
                    formData.append("file", file);
                    try {
                        const response = await fetch(url, {
                            method: 'POST',
                            body: formData,
                        });
                        if (!response.ok) {
                            throw new Error(response.statusText);
                        }
                        return await response.json();
                    } catch (error) {
                        Swal.showValidationMessage(
                            `Request failed: ${error}`
                        );
                    }
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    if (result.value.status == "success") {
                        Swal.fire("Correcto", result.value.message, "success");
                        tableClientesGeneral.ajax.reload();
                    } else {
                        Swal.fire("Error", result.value.message, "error");
                    }
                }
            })

        });
        $("#downloadTemplate").click(function (e) {
            e.preventDefault();
            //ajax request to download template blob excel
            url=base_url + "CargaConsolidada/ContenedorConsolidado/downloadPlantillaGeneral/"+idContenedor;
            $.ajax({
                url: url,
                type: "GET",
                //data return multipart/form-data
                xhrFields: {
                    responseType: 'blob'
                },
                success: function (response) {
                    const url = window.URL.createObjectURL(new Blob([response]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', 'PLANTILLA GENERAL.xlsx');
                    document.body.appendChild(link);
                    link.click();
                },
            });
        });
        $("#uploadFinal").click(() => {

            url=base_url + "CargaConsolidada/ContenedorConsolidado/generateMassiveExcelPayrolls";
            const formData = new FormData();
            formData.append("idContenedor", idContenedor);
            //swall input file
            Swal.fire({
                title: 'Subir Factura Final',
                input: 'file',
                inputAttributes: {
                    accept: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    
                },
                showCancelButton: true,
                confirmButtonText: 'Subir',
                showLoaderOnConfirm: true,
                preConfirm: async (file) => {
                    formData.append("file", file);
                    try {
                        const response = await fetch(url, {
                            method: 'POST',
                            body: formData, // Asegúrate de que formData contiene los datos correctos
                        });
                
                      
                        const blob = await response.blob();
                        const blobUrl = window.URL.createObjectURL(blob);
                
                        // Crear un enlace <a> invisible y simular un clic para descargar
                        const a = document.createElement('a');
                        a.href = blobUrl;
                        a.download = 'Cotizaciones Finales.zip'; // Nombre del archivo
                        document.body.appendChild(a);
                        a.click();
                
                        // Limpiar recursos
                        window.URL.revokeObjectURL(blobUrl);
                        document.body.removeChild(a);
                    } catch (error) {
                        console.error('Error al descargar el archivo:', error);
                    }
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                
                  
                        Swal.fire("Correcto", result.value.message, "success");
                        tableCotizacionFinal.ajax.reload();

                    
                
            })
        })
        $("#btn-back-cotizacion-final").click(function () {
            cotizacionFinalContainer.hide();
            returnToSteps();

        })
        /*End of Listeners */
    });
    /**Sockets Config */
    window.addEventListener('load', () => {
        socket.onmessage = function (event) {
            console.log(event);
            const { message, role, action } = JSON.parse(event.data)
            // Aquí puedes manejar los mensajes recibidos del servidor
            try {
                let text = "";
                if (action == "new-container") {
                    //confirm swall
                    text = "El coordinador registro un nuevo contenedo";
                    //check if mainContainer is visible
                    if (mainContainer.is(":visible")) {
                        text += "¿Desea actualizar?"
                        Swal.fire({
                            title: 'Nuevo contenedor',
                            text: text,
                            icon: 'info',
                            showCancelButton: true,
                            confirmButtonText: 'Si',
                            cancelButtonText: 'Cerrar',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                table_Entidad.ajax.reload();
                            }
                        })
                    } else {
                        //show alert swall
                        Swal.fire({
                            title: 'Nuevo contenedor',
                            text: text,
                            icon: 'info',
                            confirmButtonText: 'Cerrar',
                        })
                    }
                }
                if (action == "new-cotizacion") {
                    //confirm swall
                    text = "El coordinador registro un nuevo prospecto";
                    //check if mainContainer is visible
                    if (cotizacionContainer.is(":visible")) {
                        text += "¿Desea actualizar?"
                        Swal.fire({
                            title: 'Nueva cotización',
                            text: text,
                            icon: 'info',
                            showCancelButton: true,
                            confirmButtonText: 'Si',
                            cancelButtonText: 'Cerrar',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                tableCotizacion.ajax.reload();
                            }
                        })
                    } else {
                        //show alert swall
                        Swal.fire({
                            title: 'Nueva cotización',
                            text: text,
                            icon: 'info',
                            confirmButtonText: 'Cerrar',
                        })
                    }
                }
                if( action =="cambio-estado-proveedor"){
                    
                    if (
                        $("#table-cotizacion-embarque")
                        .is(":visible")) {
                        text="Actualización de estado de proveedor";
                        text += "¿Desea actualizar?"
                        Swal.fire({
                            title: 'Cambio de estado',
                            text: message,
                            icon: 'info',
                            showCancelButton: true,
                            confirmButtonText: 'Si',
                            cancelButtonText: 'Cerrar',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                tableCotizacionEmbarque.ajax.reload();
                            }
                        })
                    } else {
                        //show alert swall
                        Swal.fire({
                            title: 'Cambio de estado',
                            text: message,
                            icon: 'info',
                            confirmButtonText: 'Cerrar',
                        })
                    }
                    }
                    //show message swall
                   
            }
            catch (e) {
                console.log(e);
            }

        };
    });
