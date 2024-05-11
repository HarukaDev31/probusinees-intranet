var url;
$(function(){
    url=base_url+'CargaConsolidada/CCotizaciones/ajax_list';
    table_Entidad=$('#table-CCotizaciones').DataTable({
    dom: "<'row'<'col-sm-12 col-md-4'B><'col-sm-12 col-md-7'f><'col-sm-12 col-md-1'>>" +
    "<'row'<'col-sm-12'tr>>" +
    "<'row'<'col-sm-12 col-md-2'l><'col-sm-12 col-md-5'i><'col-sm-12 col-md-5'p>>",
    buttons     : [{
        extend    : 'excel',
        text      : '<i class="fa fa-file-excel color_icon_excel"></i> Excel',
        titleAttr : 'Excel',
        exportOptions: {
            columns: ':visible'
        }
    },
    {
        extend    : 'pdf',
        text      : '<i class="fa fa-file-pdf color_icon_pdf"></i> PDF',
        titleAttr : 'PDF',
        exportOptions: {
            columns: ':visible'
        }
    },
    {
        extend    : 'colvis',
        text      : '<i class="fa fa-ellipsis-v"></i> Columnas',
        titleAttr : 'Columnas',
        exportOptions: {
            columns: ':visible'
        }
    }],
    "paging": true,
    "lengthChange": true,
    "searching": true,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "responsive": false,
    'pagingType'  : 'full_numbers',
    'oLanguage' : {
        'sInfo'              : 'Mostrando (_START_ - _END_) total de registros _TOTAL_',
        'sLengthMenu'        : '_MENU_',
        'sSearch'            : 'Buscar por: ',
        'sSearchPlaceholder' : '',
        'sZeroRecords'       : 'No se encontraron registros',
        'sInfoEmpty'         : 'No hay registros',
        'sLoadingRecords'    : 'Cargando...',
        'sProcessing'        : 'Procesando...',
        'oPaginate'          : {
            'sFirst'    : '<<',
            'sLast'     : '>>',
            'sPrevious' : '<',
            'sNext'     : '>',
        },
    },
    'order': [],
    'ajax': {
        'url'       : url,
        'type'      : 'POST',
        'dataType'  : 'json',
        'data'      : function ( data ) {}

    },
    'columnDefs': [
        {
            'targets'   : 'no-hidden',
            'orderable' : false,
        },
        {
            'className' : 'text-center',
            'targets'   : 'no-sort',
            'orderable' : false,
        }
    ],
    'lengthMenu': [[10,100,1000,-1],[10,100,1000,"Todos"]],
});
})
function verCotizacion(ID){
    $( '.div-Listar' ).hide();
    $( '.div-AgregarEditar' ).show();

    url = base_url + 'CargaConsolidada/CCotizaciones/ajax_edit_body/' + ID;
    $.ajax({
      url : url,
      type: "GET",
      dataType: "JSON",
      success: function(response){
        console.log(response);
      },
      error: function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
      }
    })
  }

