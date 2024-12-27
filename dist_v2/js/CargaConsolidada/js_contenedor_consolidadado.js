let spinner = null;
var table_Entidad = null;
var idContenedor=0;
$(document).ready(function () {
    spinner = $(".backdrop");
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