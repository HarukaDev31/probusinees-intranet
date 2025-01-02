
let spinner = null;
var table_Entidad = null;
var idContenedor = 0;
var btnCrear = null;
var btnCrearCotizacion = null;
var paises = [];
var fToday = new Date();
var fYear = fToday.getFullYear();
//parse date to yyyy-mm-dd

var fDay = fToday.getDate();
var currentCarga = 0;
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
var tableCotizacion = null;
var idCotizacion = 0;

async function updateEstado(id) {
    //get select value
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
async function updateEstadoCotizacion(id) {
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
const reloadTableCotizacion = () => {
    tableCotizacion.ajax.reload();
};
const openStepFunction = async (step, id) => {
    stepIndex = step;
    stepId = id;
    spinner.show();
    stepsContainer.hide();
    url = base_url + "CargaConsolidada/ContenedorConsolidado/step";
    if (stepIndex == 1) {
        cotizacionContainer.show();
        if ($.fn.DataTable.isDataTable("#table-cotizacion")) {
            reloadTableCotizacion();
        } else {
            tableCotizacion = $('#table-cotizacion').DataTable({
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
                order: [[0, "desc"]],
                ajax: {
                    url: url,
                    type: "POST",
                    dataType: "JSON",
                    data: function (data) {
                        data.stepIndex = stepIndex;
                        data.idContenedor = idContenedor;

                    },
                    complete: function () {
                        $(".width_full").val($("#hidden-sCorrelativoCotizacion").val());
                        spinner.hide();
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
                ],
                lengthMenu: [
                    [10, 100, 1000, -1],
                    [10, 100, 1000, "Todos"],
                ],
            });
        }
        $("#btn-back-cotizacion").off("click");
        $("#btn-back-cotizacion").on("click", function () {
            returnToSteps();
        });
        await getTipoCliente();

    }
    spinner.hide();
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
const returnToSteps = () => {
    cotizacionContainer.hide();
    stepsContainer.show();
}
$(document).ready(async function () {
    spinner = $(".backdrop");
    stepsContainer = $("#steps");
    mainContainer = $("#main-container");
    stepsContainer.hide();
    cotizacionContainer = $("#cotizacion-container");
    cotizacionContainer.hide();
    tableCotizacion = $("#table-cotizacion");
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
        order: [[0, "desc"]],
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
        ],
        lengthMenu: [
            [10, 100, 1000, -1],
            [10, 100, 1000, "Todos"],
        ],
    });



    const fillSelects = async () => {
        $("#txt-ID_Pais").empty();
        $("#txt-Mes").empty();
        await getPaises();
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
        paises.forEach((pais) => {
            $("#txt-ID_Pais").append(
                `<option value="${pais.ID_Pais}">${pais.No_Pais}</option>`
            );
        });
    }
    await fillSelects();
    btnCrear = $("#btn-crear");
    btnCrear.on("click", function () {
        $("#modal-crear").modal("show");
        $("#btn-guardar").show();
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
        let form= $("#form-crear")[0];
        console.log(form.checkValidity());
        if(!form.checkValidity()){
            form.classList.add('was-validated');
            return;
        }

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
            },
        });
    });
    $("#btn-guardar-cotizacion").click(function (e) {
        e.preventDefault();
        const formData = new FormData($("#form-crear-cotizacion")[0]);
        formData.append("id_contenedor", idContenedor);
        let form= $("#form-crear-cotizacion")[0];
        if(!form.checkValidity()){
            form.classList.add('was-validated');
            return;
        }
        $.ajax({
            url: base_url + "CargaConsolidada/ContenedorConsolidado/storeCotizacion",
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
    $("#btn-actualizar-cotizacion").click(function (e) {
        e.preventDefault();
        const formData = new FormData($("#form-crear-cotizacion")[0]);
        formData.append("id", idCotizacion);
        let form= $("#form-crear-cotizacion")[0];
        if(!form.checkValidity()){
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

        let form= $("#form-crear")[0];
        if(!form.checkValidity()){
            form.classList.add('was-validated');
            return;
        }
        $.ajax({
            url: base_url + "CargaConsolidada/ContenedorConsolidado/update",
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
});
