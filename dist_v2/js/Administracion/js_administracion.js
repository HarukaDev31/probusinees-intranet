async function configurarBuscador(tableId, searchInputClass, infoContainerId) {
    // Obtener la instancia de DataTable
    var table = $("#" + tableId).DataTable();
    console.log(table);

    // Escuchar el evento "input" en el buscador
    $("." + searchInputClass).on("input", function () {
        var searchTerm = $(this).val(); // Obtener el valor del buscador
        table.search(searchTerm).draw(); // Aplicar la búsqueda y redibujar la tabla
    });

    // Función para limpiar el buscador y el filtro
    window["resetBuscador_" + tableId] = function () {
        $("." + searchInputClass).val("");
        table.search("").draw();
    };

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
async function viewClientePagosCoordination(idCotizacion, nombreCliente) {
  //show modal with table of pagos coordination
  $("#modalClientePagosCoordination").modal("show");
  $("#modalClientePagosCoordination .modal-title").text(`Pagos de Coordinación - ${nombreCliente}`);
  url =
    base_url + "Administracion/Administracion/getPagosCoordination/" + idCotizacion;
  if (!$.fn.DataTable.isDataTable("#table-pagos-tracking-coordinacion")) {
    tableCotizacionTrackingPagos = $("#table-pagos-tracking-coordinacion").DataTable({
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

        },
      },
      initComplete: function () {

      },
      complete: function () {


      },
      drawCallback: function (settings) {

      },
    });
  } else {
    tableCotizacionTrackingPagos.ajax.url(url).load();
  }

}
async function viewClientePagosCurso(idPedidoCurso, nombreCliente) {
  //show modal with table of pagos coordination
  $("#modalClientePagosCoordination").modal("show");
  $("#modalClientePagosCoordination .modal-title").text(`Pagos de Coordinación - ${nombreCliente}`);
  url =
    base_url + "Administracion/Administracion/getPagosCurso/" + idPedidoCurso;
  if (!$.fn.DataTable.isDataTable("#table-pagos-tracking-coordinacion")) {
    tableCotizacionTrackingPagos = $("#table-pagos-tracking-coordinacion").DataTable({
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

        },
      },
      initComplete: function () {

      },
      complete: function () {


      },
      drawCallback: function (settings) {

      },
    });
  } else {
    tableCotizacionTrackingPagos.ajax.url(url).load();
  }

}
$(".tab-administracion").removeClass("active");
$(".tab-administracion").off("click").click(function () {
    $(".tab-administracion").removeClass("active");
    // $("#table-administracion-pagos_wrapper").hide();
    // $("#table-administracion-variacion_wrapper").hide();
    // $("#table-administracion-pagos_wrapper").hide();

    let table = this.getAttribute("data-table");
    this.classList.add("active");

    if (table == "consolidado") {
        $("#table-pagos-consolidado").attr("style", "");
        $("#table-pagos-curso").hide();
        $("#table-pagos-curso_wrapper").hide();
        url = base_url + 'Administracion/Administracion/getConsolidadoPagos';

        if ($.fn.DataTable.isDataTable("#table-pagos-consolidado")) {

            $("#table-pagos-consolidado").show();
            $("#table-pagos-consolidado_wrapper").show();
            tableCursoPedidos.ajax.reload(null, false);
        } else {
            tableCursoPedidos = $("#table-pagos-consolidado").DataTable({
                dom: "<'row'<'col-sm-12 col-md-4'B><'col-sm-12 col-md-7'f><'col-sm-12 col-md-1'>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-2'l><'col-sm-12 col-md-5'i><'col-sm-12 col-md-5'p>>",
                buttons: [{
                    extend: 'excel',
                    text: '<i class="fa fa-file-excel color_icon_excel"></i> Excel',
                    titleAttr: 'Excel',
                    exportOptions: {
                        columns: ':visible'
                    },
                    attr: {
                        class: "hidden"
                    }
                },
                {
                    extend: 'pdf',
                    text: '<i class="fa fa-file-pdf color_icon_pdf"></i> PDF',
                    titleAttr: 'PDF',
                    exportOptions: {
                        columns: ':visible'
                    },
                    attr: {
                        class: "hidden"
                    }
                },
                {
                    extend: 'colvis',
                    text: '<i class="fa fa-ellipsis-v"></i> Columnas',
                    titleAttr: 'Columnas',
                    exportOptions: {
                        columns: ':visible'
                    },
                    attr: {
                        class: "hidden"
                    }
                },

                ],
                'searching': true,
                'bStateSave': true,
                "lengthChange": true,
                'processing': true,
                'serverSide': false,
                'info': true,
                'autoWidth': false,
                'pagingType': 'full_numbers',
                'oLanguage': {
                    'sInfo': 'Mostrando (_START_ - _END_) total de registros _TOTAL_',
                    'sLengthMenu': '_MENU_',
                    'sSearch': 'Buscar por: ',
                    'sSearchPlaceholder': '',
                    'sZeroRecords': 'No se encontraron registros',
                    'sInfoEmpty': 'No hay registros',
                    'sLoadingRecords': 'Cargando...',
                    'sProcessing': 'Procesando...',
                    'oPaginate': {
                        'sFirst': '<<',
                        'sLast': '>>',
                        'sPrevious': '<',
                        'sNext': '>',
                    },
                },
                'order': [],
                'ajax': {
                    'url': url,
                    'type': 'POST',
                    'dataType': 'JSON',
                    'data': function (data) {
                        data.sMethod = $('#hidden-sMethod').val(),
                            data.estado_pago = $('#cbo-filtro-estado_pago').val(),
                            data.Filtro_Fe_Inicio = ParseDateString($('#txt-Fe_Inicio').val(), 'fecha', '/'),
                            data.Filtro_Fe_Fin = ParseDateString($('#txt-Fe_Fin').val(), 'fecha', '/');
                        data.tipoTabla = "alumnos";
                    },
                },
                'columnDefs': [
                    {
                        targets: 'no-hidden',
                        visible: false,
                    }, {
                        className: 'text-center',
                        targets: 'no-sort',
                        orderable: false,
                    }, {
                        targets: "",
                        orderable: false,
                    },],
                'lengthMenu': [[10, 100, 1000, -1], [10, 100, 1000, "Todos"]],
            });

            // FUNCION PARA CONFIGURAR EL BUSCADOR
            configurarBuscador(
                "table-pagos-consolidado",
                "search-table",
                "table-pagos-consolidado_info"
            );
            //Funcion para exportar a excel

            $("#table-pagos-consolidado").show();
        }
        currentTableCurso = "consolidado";
    }

    else if (table == "cursos") {
        url = base_url + 'Administracion/Administracion/getCursosPagos';
        $("#table-pagos-consolidado").hide();
        $("#table-pagos-consolidado_wrapper").hide();
        $("#table-pagos-curso").attr("style", "");
        $("#table-pagos-curso_wrapper").show();

        if ($.fn.DataTable.isDataTable("#table-pagos-curso")) {

            $("#table-pagos-curso").show();
            $("#table-pagos-curso_wrapper").show();
            tableCursoPagos.ajax.reload(null, false);
        } else {

            tableCursoPagos = $("#table-pagos-curso").DataTable({
                dom: "<'row'<'col-sm-12 col-md-4'B><'col-sm-12 col-md-7'f><'col-sm-12 col-md-1'>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-2'l><'col-sm-12 col-md-5'i><'col-sm-12 col-md-5'p>>",
                buttons: [],
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
                        data.sMethod = $('#hidden-sMethod').val();
                        data.estado_pago = $('#cbo-filtro-estado_pago').val();
                        data.Filtro_Fe_Inicio = ParseDateString($('#txt-Fe_Inicio').val(), 'fecha', '/');
                        data.Filtro_Fe_Fin = ParseDateString($('#txt-Fe_Fin').val(), 'fecha', '/');
                        data.tipoTabla = "pagos";

                    },
                },
            });
            // FUNCION PARA CONFIGURAR EL BUSCADOR
            configurarBuscador(
                "table-pagos-curso",
                "search-table",
                "table-pagos-curso_info"
            );
            //Funcion para exportar a excel

            $("#table-pagos-curso").show();
            currentTableCurso = "curso";

        }
    }
});
$(".tab-administracion").first().click();