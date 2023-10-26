var url;
var table_blog_post;
var accion_blog_post = '';

$(function () {
  url = base_url + 'Blog/BlogHistorialUsuarioController/ajax_list';
  table_blog_post = $('#table-Producto').DataTable({
    'dom': 'B<"top">frt<"bottom"lip><"clear">',
    buttons: [{
      extend: 'excel',
      text: '<i class="fa fa-file-excel-o color_icon_excel"></i> Excel',
      titleAttr: 'Excel',
      exportOptions: {
        columns: ':visible'
      }
    },
    {
      extend: 'pdf',
      text: '<i class="fa fa-file-pdf-o color_icon_pdf"></i> PDF',
      titleAttr: 'PDF',
      exportOptions: {
        columns: ':visible'
      }
    },
    {
      extend: 'colvis',
      text: '<i class="fa fa-ellipsis-v"></i> Columnas',
      titleAttr: 'Columnas',
      exportOptions: {
        columns: ':visible'
      }
    }],
    'searching': false,
    'bStateSave': true,
    'processing': true,
    'serverSide': true,
    'info': true,
    'autoWidth': false,
    'pagingType': 'full_numbers',
    'oLanguage': {
      'sInfo': 'Mostrando (_START_ - _END_) total de registros _TOTAL_',
      'sLengthMenu': '_MENU_',
      'sSearch': 'Buscar por: ',
      'sSearchPlaceholder': 'UPC / Nombre',
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
      'dataType': 'json',
      'data': function (data) {
        data.Filtro_Fe_Inicio = ParseDateString($('#txt-Filtro_Fe_Inicio').val(), 1, '/'),
        data.Filtro_Fe_Fin = ParseDateString($('#txt-Filtro_Fe_Fin').val(), 1, '/'),
        data.Global_Filter = $('#txt-Global_Filter').val();
      },
    },
    'columnDefs': [{
      'className': 'text-center',
      'targets': 'no-sort',
      'orderable': false,
    }, {
      'className': 'text-right',
      'targets': 'sort_right',
      'orderable': true,
    },],
    'lengthMenu': [[10, 100, 1000, -1], [10, 100, 1000, "Todos"]],
  });

  $('.dataTables_length').addClass('col-sm-3 col-md-1');
  $('.dataTables_info').addClass('col-sm-3 col-md-2');
  $('.dataTables_paginate').addClass('col-sm-6 col-md-9');

  $('#btn-filter').click(function () {
    table_blog_post.ajax.reload();
  });
})

function reload_table_blog_post() {
  table_blog_post.ajax.reload(null, false);
}