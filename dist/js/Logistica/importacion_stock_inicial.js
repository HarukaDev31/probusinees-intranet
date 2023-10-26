var url;
var table_ImportacionStockInicial;
var accion_ImportacionStockInicial = '';

$(function () {
  // Validate exist file excel product
	$( document ).on('click', '#btn-excel-importar_stock_inicial_productos', function(event) {
	  if ( $( "#my-file-selector" ).val().length === 0 ) {
      $( '#my-file-selector' ).closest('.form-group').find('.help-block').html('Seleccionar archivo');
		  $( '#my-file-selector' ).closest('.form-group').removeClass('has-success').addClass('has-error');
	  } else {
      $( '#btn-cancel-product' ).attr('disabled', true);
      $( '#a-download-product' ).attr('disabled', true);
	    
      $( '#btn-excel-importar_stock_inicial_productos' ).text('');
      $( '#btn-excel-importar_stock_inicial_productos' ).attr('disabled', true);
      $( '#btn-excel-importar_stock_inicial_productos' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
  
      $( '#modal-loader' ).modal('show');
	  }
  })

  url = base_url + 'Logistica/ImportacionStockInicialController/ajax_list';
  table_ImportacionStockInicial = $('#table-ImportacionStockInicial').DataTable({
    'dom': 'B<"top">frt<"bottom"lip><"clear">',
    buttons     : [{
      extend    : 'excel',
      text      : '<i class="fa fa-file-excel-o color_icon_excel"></i> Excel',
      titleAttr : 'Excel',
      exportOptions: {
        columns: ':visible'
      }
    },
    {
      extend    : 'pdf',
      text      : '<i class="fa fa-file-pdf-o color_icon_pdf"></i> PDF',
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
    'searching'   : false,
    'bStateSave'  : true,
    'processing'  : true,
    'serverSide'  : true,
    'info'        : true,
    'autoWidth'   : false,
    'pagingType'  : 'full_numbers',
    'oLanguage' : {
      'sInfo'               : 'Mostrando (_START_ - _END_) total de registros _TOTAL_',
      'sLengthMenu'         : '_MENU_',
      'sSearch'             : 'Buscar por: ',
      'sSearchPlaceholder'  : 'UPC / Nombre',
      'sZeroRecords'        : 'No se encontraron registros',
      'sInfoEmpty'          : 'No hay registros',
      'sLoadingRecords'     : 'Cargando...',
      'sProcessing'         : 'Procesando...',
      'oPaginate'           : {
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
        'data'      : function ( data ) {
          data.Filtros_Productos = $( '#cbo-Filtros_Productos' ).val(),
          data.Global_Filter = $( '#txt-Global_Filter' ).val();
        },
    },
    'columnDefs': [{
      'className' : 'text-center',
      'targets'   : 'no-sort',
      'orderable' : false,
    },{
      'className' : 'text-left',
      'targets'   : 'no-sort_left',
      'orderable' : false,
    },{
      'className' : 'text-right',
      'targets'   : 'sort_right',
      'orderable' : true,
    },{
      'className' : 'text-center',
      'targets'   : 'sort_center',
      'orderable' : true,
    },],
    'lengthMenu': [[10, 100, 1000, -1], [10, 100, 500, 1000]],
  });

  $('.dataTables_length').addClass('col-xs-4 col-sm-5 col-md-1');
  $('.dataTables_info').addClass('col-xs-8 col-sm-7 col-md-4');
  $('.dataTables_paginate').addClass('col-xs-12 col-sm-12 col-md-7');
    
  $( '#txt-Global_Filter' ).keyup(function() {
    table_ImportacionStockInicial.search($(this).val()).draw();
  });

	$(document).bind('keydown', 'f2', function(){
    importarExcelStockInicialProductos();
  });
})

function importarExcelStockInicialProductos(){
  /*
  url = base_url + 'Logistica/ImportacionStockInicialController/verificarImportacionStockInicial';
  $.post( url, {}, function( response ){
    if ( response.sStatus == 'success' ) {
      */
      $( ".modal_importar_stock_inicial_productos" ).modal( "show" );
      /*
    } else {
  	  $( '#modal-message' ).modal('show');
      $( '.modal-message' ).addClass( 'modal-' + response.sStatus);
      $( '.modal-title-message' ).text(response.sMessage);
      setTimeout(function() {$('#modal-message').modal('hide');}, 4100);
    }
  }, 'JSON');
  */
}

function reload_table_ImportacionStockInicial(){
  table_ImportacionStockInicial.ajax.reload(null,false);
}