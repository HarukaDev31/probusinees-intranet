var url;
var table_ubicacion_inventario;

$(function () {
  $('.select2').select2();
  $('[data-mask]').inputmask()
  
  url = base_url + 'Logistica/ReglasLogistica/UbicacionInventarioController/ajax_list';
  table_ubicacion_inventario = $( '#table-UbicacionInventario' ).DataTable({
    'dom'       : 'B<"top">frt<"bottom"lp><"clear">',
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
      'sInfo'                 : 'Mostrando (_START_ - _END_) total de registros _TOTAL_',
      'sLengthMenu'           : '_MENU_',
      'sSearch'               : 'Buscar por: ',
      'sSearchPlaceholder'    : 'UPC / Nombre',
      'sZeroRecords'          : 'No se encontraron registros',
      'sInfoEmpty'            : 'No hay registros',
      'sLoadingRecords'       : 'Cargando...',
      'sProcessing'           : 'Procesando...',
      'oPaginate'             : {
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
          data.Filtros_UnidadesMedida = $( '#cbo-Filtros_UnidadesMedida' ).val(),
          data.Global_Filter = $( '#txt-Global_Filter' ).val();
        },
    },
    'columnDefs': [{
        'className'     : 'text-center',
        'targets'       : 'no-sort',
        'orderable'     : false,
    },],
  });
  
  $( '#txt-Global_Filter' ).keyup(function() {
    table_ubicacion_inventario.search($(this).val()).draw();
  });
  
  $( '#form-UbicacionInventario' ).validate({
		rules:{
			ID_Almacen: {
				required: true,
			},
			No_Ubicacion_Inventario: {
				required: true,
			},
		},
		messages:{
			ID_Almacen:{
				required: "Seleccionar almacén"
			},
			No_Ubicacion_Inventario:{
				required: "Ingresar nombre"
			},
		},
		errorPlacement : function(error, element) {
		  $(element).closest('.form-group').find('.help-block').html(error.html());
	  },
		highlight : function(element) {
	    $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
    },
    unhighlight: function(element, errorClass, validClass) {
			$(element).closest('.form-group').removeClass('has-error').addClass('has-success');
			$(element).closest('.form-group').find('.help-block').html('');
    },
		submitHandler: form_UbicacionInventario
	});
})

function agregarUbicacionInventario(){
  $( '#modal-loader' ).modal('show');
  
  $( '#form-UbicacionInventario' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '#modal-UbicacionInventario' ).modal('show');
  $( '.modal-title' ).text('Nueva Ubicación Inventario');
  
	$( '#modal-UbicacionInventario' ).on('shown.bs.modal', function() {
		$( '#txt-No_Ubicacion_Inventario' ).focus();
	})
	
  $('[name="EID_Empresa"]').val('');
  $('[name="EID_Ubicacion_Inventario"]').val('');
  $('[name="ENo_Ubicacion_Inventario"]').val('');
  
  url = base_url + 'HelperController/getAlmacenesEmpresa';
  $.post( url, function( response ){
    $( '#cbo-Almacenes' ).html( '<option value="">- Seleccionar -</option>' );
    for (var i = 0; i < response.length; i++)
      $( '#cbo-Almacenes' ).append( '<option value="' + response[i].ID_Almacen + '">' + response[i].No_Almacen + '</option>' );
  }, 'JSON');
  
  $( '.div-Estado' ).hide();
  $( '#cbo-Estado' ).html( '<option value="1">Activo</option>' );
  
  $( '#modal-loader' ).modal('hide');
}

function verUbicacionInventario(ID){
  $( '#form-UbicacionInventario' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '#modal-loader' ).modal('show');
 
  url = base_url + 'Logistica/ReglasLogistica/UbicacionInventarioController/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $( '#modal-UbicacionInventario' ).modal('show');
      $( '.modal-title' ).text('Modifcar Ubicación Inventario');
      
    	$( '#modal-UbicacionInventario' ).on('shown.bs.modal', function() {
    		$( '#txt-No_Ubicacion_Inventario' ).focus();
    	})
	
      $('[name="EID_Almacen"]').val(response.ID_Almacen);
      $('[name="EID_Ubicacion_Inventario"]').val(response.ID_Ubicacion_Inventario);
      $('[name="ENo_Ubicacion_Inventario"]').val(response.No_Ubicacion_Inventario);
      
      url = base_url + 'HelperController/getAlmacenesEmpresa';
      $.post( url , function( responseAlmacen ){
        $( '#cbo-Almacenes' ).html('');
        for (var i = 0; i < responseAlmacen.length; i++){
          selected = '';
          if(response.ID_Almacen == responseAlmacen[i].ID_Almacen)
            selected = 'selected="selected"';
          $( '#cbo-Almacenes' ).append( '<option value="' + responseAlmacen[i].ID_Almacen + '" ' + selected + '>' + responseAlmacen[i].No_Almacen + '</option>' );
        }
      }, 'JSON');
      
      $('[name="No_Ubicacion_Inventario"]').val(response.No_Ubicacion_Inventario);
      
      $( '.div-Estado' ).show();
      $( '#cbo-Estado' ).html( '' );
      for (var i = 0; i < 2; i++){
        selected = '';
        if(response.Nu_Estado == i)
          selected = 'selected="selected"';
        $( '#cbo-Estado' ).append( '<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Inactivo' : 'Activo') + '</option>' );
      }
      $( '#modal-loader' ).modal('hide');
    }
  });
}

function form_UbicacionInventario(){
  if ( $( '#cbo-Almacenes' ).val() == 0){
    $( '#cbo-Almacenes' ).closest('.form-group').find('.help-block').html('Seleccionar almacén');
    $( '#cbo-Almacenes' ).closest('.form-group').removeClass('has-success').addClass('has-error');
  } else {
    $( '#btn-save' ).text('');
    $( '#btn-save' ).attr('disabled', true);
    $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
  
    $( '#modal-loader' ).modal('show');
    
    url = base_url + 'Logistica/ReglasLogistica/UbicacionInventarioController/crudUbicacionInventario';
  	$.ajax({
      type		  : 'POST',
      dataType	: 'JSON',
  		url		    : url,
  		data		  : $('#form-UbicacionInventario').serialize(),
  		success : function( response ){
  		  $( '#modal-loader' ).modal('hide');
  		  
  	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
    	  $( '#modal-message' ).modal('show');
  		  
  		  if (response.status == 'success'){
  		    $('#modal-UbicacionInventario').modal('hide');
    	    $( '.modal-message' ).addClass(response.style_modal);
    	    $( '.modal-title-message' ).text(response.message);
    	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
    	    reload_table_ubicacion_inventario();
  		  } else {
  		    $( '#txt-No_Ubicacion_Inventario' ).val('');
    	    $( '.modal-message' ).addClass(response.style_modal);
    	    $( '.modal-title-message' ).text(response.message);
    	    setTimeout(function() {$('#modal-message').modal('hide');}, 1200);
  	    }
  	    
        $( '#btn-save' ).text('');
        $( '#btn-save' ).append( '<span class="fa fa-save"></span> Guardar' );
        $( '#btn-save' ).attr('disabled', false);
  		}
  	});
  }
}

function eliminarUbicacionInventario(ID, ID_Almacen){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });
  
  $( '#btn-save-delete' ).off('click').click(function () {
    $( '#modal-loader' ).modal('show');
    
    url = base_url + 'Logistica/ReglasLogistica/UbicacionInventarioController/eliminarUbicacionInventario/' + ID + '/' + ID_Almacen;
    $.ajax({
      url       : url,
      type      : "GET",
      dataType  : "JSON",
      success: function( response ){
        $( '#modal-loader' ).modal('hide');
        
        $modal_delete.modal('hide');
  	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
    	  $( '#modal-message' ).modal('show');
  		  
  		  if (response.status == 'success'){
    	    $( '.modal-message' ).addClass(response.style_modal);
    	    $( '.modal-title-message' ).text(response.message);
    	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
    	    reload_table_ubicacion_inventario();
  		  } else {
    	    $( '.modal-message' ).addClass(response.style_modal);
    	    $( '.modal-title-message' ).text(response.message);
    	    setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
  		  }
      }
    });
  });
}

function reload_table_ubicacion_inventario(){
  table_ubicacion_inventario.ajax.reload(null,false);
}