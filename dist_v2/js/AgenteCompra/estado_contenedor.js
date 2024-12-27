let spinner = null;
var tc = 0;
var table_Entidad = null;
var idBooking = 0;
$(document).ready(function () {
    spinner = $(".backdrop");
    url = base_url + "AgenteCompra/EstadoContenedor/index";


    table_Entidad = $("#table-estadoContenedor").DataTable({
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

                // (data.sCorrelativoCotizacion = $(
                //     "#hidden-sCorrelativoCotizacion"
                // ).val()),
                //     (data.ID_Pedido_Cabecera = $("#hidden-ID_Pedido_Cabecera").val()),
                //     (data.Filtros_Entidades = $("#cbo-Filtros_Entidades").val()),
                //     (data.Global_Filter = $("#txt-Global_Filter").val()),
                //     (data.Filtro_Fe_Inicio = ParseDateString(
                //         $("#txt-Fe_Inicio").val(),
                //         "fecha",
                //         "/"
                //     )),
                //     (data.Filtro_Fe_Fin = ParseDateString(
                //         $("#txt-Fe_Fin").val(),
                //         "fecha",
                //         "/"
                //     ));
                // data.Filtro_Estado = $("#txt-ID_Estado").val();
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


});
function reloadTable() {
    table_Entidad.ajax.reload();
}
function guardarTC(id) {
    const tcValue = $(`#txtTC-${id}`).val();
    if (tcValue == "") {

        return;
    }
    tc = tcValue;
    idBooking = id;
    $("#modalConfirmar").modal("show");

}
$("#btn-html_reporte").click(function () {
    reloadTable();
  });
$("#btnGuardarTC").click(function () {
    event.preventDefault();
    spinner.show();
    $.ajax({
        url: base_url + "AgenteCompra/EstadoContenedor/guardarTC",
        type: "POST",
        data: {
            idBooking: idBooking,
            tc: tc
        },
        success: function (data) {
            spinner.hide();
            $("#modalConfirmar").modal("hide");
            reloadTable();
        },
        error: function () {
            spinner.hide();
        },
    });
});
function guardarBlTelex(id) {
    const blTelex = $(`#blTelex-${id}`).val();
    spinner.show();
    url = base_url + "AgenteCompra/EstadoContenedor/guardarBlTelex";
    $.ajax({
        url: url,
        type: "POST",
        data: {
            idBooking: id,
            blTelex: blTelex
        },
        success: function (data) {
            spinner.hide();
            reloadTable();
        },
        error: function () {
            spinner.hide();
        },
    });

}
function guardarPagado(id) {
    const pagado = $(`#pagado-${id}`).val();
    spinner.show();
    url = base_url + "AgenteCompra/EstadoContenedor/guardarPagado";
    $.ajax({
        url: url,
        type: "POST",
        data: {
            idBooking: id,
            pagado: pagado
        },
        success: function (data) {
            spinner.hide();
            reloadTable();
        },
        error: function () {
            spinner.hide();
        },
    });

}
const fillNavieraSelect = async () => {
    await $.ajax({
        url: base_url + "AgenteCompra/PedidosPagados/getNavieras",
        type: 'POST',
        success: function (response) {
            const { status, data } = JSON.parse(response);
            $('#naviera').empty();
            $('#naviera').append(`<option value="">Seleccionar naviera</option>`);
            data.forEach(item => {
                $('#naviera').append(`<option value="${item.id}">${item.name}</option>`);
            });
        }
    });
}
const fillContenedorSelect = async () => {
    await $.ajax({
        url: base_url + "AgenteCompra/PedidosPagados/getContainer",
        type: 'POST',
        success: function (response) {
            const { status, data } = JSON.parse(response);
            $('#contenedor').empty();
            $('#contenedor').append(`<option value="">Seleccionar contenedor</option>`);
            data.forEach(item => {
                $('#contenedor').append(`<option value="${item.id}">${item.name}</option>`);
            });
        }
    });
}
const fillCodShipperSelect = async () => {
    await $.ajax({
        url: base_url + "AgenteCompra/PedidosPagados/getShipper",
        type: 'POST',
        success: function (response) {
            const { status, data } = JSON.parse(response);
            $('#codShipper').empty();
            $('#codShipper').append(`<option value="">Seleccionar código</option>`);
            data.forEach(item => {
                $('#codShipper').append(`<option value="${item.id}">${item.name}</option>`);
            });
        }
    });
}
const fillFlcFormSelects = async () => {

    await Promise.all([
        fillNavieraSelect(),
        fillContenedorSelect(),
        fillCodShipperSelect()
    ]);
}

const openModalNuevo = async () => {
    $('#modalNuevo').modal('show');
    await fillFlcFormSelects()
}
$("#fclDetails").submit(function (event) {
    event.preventDefault();
    url = base_url + "AgenteCompra/PedidosPagados/saveFCLBooking";
    $('span.error').text('');
    $('input').removeClass('error');
    $('select').removeClass('error');
    const client = $('#client').val();
    const tc = $('#tc').val();
    const rmb = $('#rmb').val();
    const usd = $('#usd').val();
    const inland = $('#inland').val();
    const flete = $('#flete').val();
    const naviera = $('#naviera').val();
    const contenedor = $('#contenedor').val();
    const diasTransito = $('#diasTransito').val();

    const boxFree = $('#boxFree').val();
    const codShipper = $('#codShipper').val();
    const servicio = $('#servicio').val();
    const norden = $('#norden').val();
    const codbl = $('#codbl').val();
    if (!client) {
      $('#error-client').text('El campo cliente es obligatorio');
      $("#client").addClass("error");
    }
    if (!inland) {
      $('#error-inland').text('El campo inland es obligatorio');
      $("#inland").addClass("error");
  
    }
    if (!flete) {
      $('#error-flete').text('El campo flete es obligatorio');
      $("#flete").addClass("error");
    }
    if (!naviera) {
      $('#error-naviera').text('El campo naviera es obligatorio');
      $("#naviera").addClass("error");
    }
    if (!contenedor) {
      $('#error-contenedor').text('El campo contenedor es obligatorio');
      $("#contenedor").addClass("error");
    }
    if (!diasTransito) {
      $('#error-diasTransito').text('El campo días tránsito es obligatorio');
      $("#diasTransito").addClass("error");
    }
   
    if (!boxFree) {
      $('#error-boxFree').text('El campo box free es obligatorio');
      $("#boxFree").addClass("error");
    }
    if (!codShipper) {
      $('#error-codShipper').text('El campo cod shipper es obligatorio');
      $("#codShipper").addClass("error");
    }
    if (!norden) {
      $('#error-norden').text('El campo norden es obligatorio');
      $("#norden").addClass("error");
    }
    if (!codbl) {
      $('#error-codbl').text('El campo cod bl es obligatorio');
      $("#codbl").addClass("error");
    }
    if (!servicio) {
      $('#error-servicio').text('El campo servicio es obligatorio');
      $("#servicio").addClass("error");
    }
    if (!client || !inland || !flete || !naviera || !contenedor || !diasTransito || !boxFree || !codShipper || !norden || !codbl || !servicio) {
        console.log("waos")
        return;
    }
    spinner.show();

    const data = $(this).serialize();
    $.ajax({
        url: url,
        type: "POST",
        data: data,
        success: function (data) {
            spinner.hide();
            $('#modalNuevo').modal('hide');
            reloadTable();
        },
        error: function () {
            spinner.hide();
        },
    });
});
const deleteShipper = () => {
    const shipper = $('#codShipper').val();
    if (shipper != "") {
      //show confirmation dialog
      const r = confirm("¿Está seguro de eliminar el shipper seleccionado?");
      if (r == true) {
        $.ajax({
          url: base_url + "AgenteCompra/PedidosPagados/deleteShipper",
          type: 'POST',
          data: {
            shipper,
          },
          success: function (data) {
            fillCodShipperSelect();
          }
        });
      }
    }
  }
const eliminar=(id)=>{
    const r = confirm("¿Está seguro de eliminar el registro seleccionado?");
    if (r == true) {
        $.ajax({
          url: base_url + "AgenteCompra/EstadoContenedor/eliminar",
          type: 'POST',
          data: {
            id,
          },
          success: function (data) {
            reloadTable();
          }
        });
      }
}