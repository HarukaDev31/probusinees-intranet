var url;
var table_unidad_medida;
var accion_unidad_medida;

$(function () {
	$(document).keyup(function(event){
    if(event.which == 27){//ESC
      $( "#modal-UnidadMedida" ).modal('hide');
    }
	});
	
  $('.select2').select2();
  $('[data-mask]').inputmask()
  
  url = base_url + 'Logistica/ReglasLogistica/UnidadMedidaController/ajax_list';
  table_unidad_medida = $( '#table-UnidadMedida' ).DataTable({
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
      'sInfo'              : 'Mostrando (_START_ - _END_) total de registros _TOTAL_',
      'sLengthMenu'        : '_MENU_',
      'sSearch'            : 'Buscar por: ',
      'sSearchPlaceholder' : 'UPC / Nombre',
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
      'data'      : function ( data ) {
        data.Filtros_UnidadesMedida = $( '#cbo-Filtros_UnidadesMedida' ).val(),
        data.Global_Filter = $( '#txt-Global_Filter' ).val();
      },
    },
    'columnDefs': [{
      'className' : 'text-center',
      'targets'   : 'no-sort',
      'orderable' : false,
    },],
  });

  $('.dataTables_length').addClass('col-xs-4 col-sm-5 col-md-1');
  $('.dataTables_info').addClass('col-xs-8 col-sm-7 col-md-4');
  $('.dataTables_paginate').addClass('col-xs-12 col-sm-12 col-md-7');
  
  $( '#txt-Global_Filter' ).keyup(function() {
    table_unidad_medida.search($(this).val()).draw();
  });
  
  $( '#form-UnidadMedida' ).validate({
		rules:{
			Nu_Sunat_Codigo: {
				required: true,
			},
			No_Unidad_Medida: {
				required: true,
			},
		},
		messages:{
			Nu_Sunat_Codigo:{
				required: "Seleccionar código"
			},
			No_Unidad_Medida:{
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
		submitHandler: form_UnidadMedida
	});
	
  $(document).bind('keydown', 'alt+a', function(){
    agregarUnidadMedida();
  });
})

function agregarUnidadMedida(){
  accion_unidad_medida='add_unidad_medida';
  
  $( '#modal-loader' ).modal('show');
  
  $( '#form-UnidadMedida' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '#modal-UnidadMedida' ).modal('show');
  $( '.modal-title' ).text('Nueva Unidad Medida');
  
	$( '#modal-UnidadMedida' ).on('shown.bs.modal', function() {
		$( '#txt-No_Unidad_Medida' ).focus();
	})
	
  $( '[name="EID_Empresa"]' ).val('');
  $( '[name="EID_Unidad_Medida"]' ).val('');
  $( '[name="ENo_Unidad_Medida"]' ).val('');
  $( '[name="ENu_Sunat_Codigo"]' ).val('');
  
  url = base_url + 'HelperController/getCodigoUnidadMedida';
  $.post( url , function( response ){
    $( '#cbo-codigo_unidad_medida' ).html('<option value="" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-codigo_unidad_medida' ).append( '<option value="' + response[i].Nu_Valor + '">' + response[i].Nu_Valor + ' - ' + response[i].No_Descripcion + '</option>' );
  }, 'JSON');
  
  $( '.div-Estado' ).hide();
  $( '#cbo-Estado' ).html( '<option value="1">Activo</option>' );
  
  $( '#modal-loader' ).modal('hide');
}

function verUnidadMedida(ID){
  accion_unidad_medida='upd_unidad_medida';
  
  $( '#form-UnidadMedida' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '#modal-loader' ).modal('show');
 
  url = base_url + 'Logistica/ReglasLogistica/UnidadMedidaController/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $( '#modal-UnidadMedida' ).modal('show');
      $( '.modal-title' ).text('Modificar Unidad Medida');
      
    	$( '#modal-UnidadMedida' ).on('shown.bs.modal', function() {
    		$( '#txt-No_Unidad_Medida' ).focus();
    	})
	
      $( '[name="EID_Empresa"]' ).val(response.ID_Empresa);
      $( '[name="EID_Unidad_Medida"]' ).val(response.ID_Unidad_Medida);
      $( '[name="ENo_Unidad_Medida"]' ).val(response.No_Unidad_Medida);
      $( '[name="ENu_Sunat_Codigo"]' ).val(response.Nu_Sunat_Codigo);
      
      $( '[name="No_Unidad_Medida"]' ).val(response.No_Unidad_Medida);
      
      var selected;
      url = base_url + 'HelperController/getCodigoUnidadMedida';
      $.post( url , function( responseUnidadMedida ){
        $( '#cbo-codigo_unidad_medida' ).html('');
        for (var i = 0; i < responseUnidadMedida.length; i++) {
          selected = '';
          if(response.Nu_Sunat_Codigo == responseUnidadMedida[i].Nu_Valor)
            selected = 'selected="selected"';
          $( '#cbo-codigo_unidad_medida' ).append( '<option value="' + responseUnidadMedida[i].Nu_Valor + '" ' + selected + '>' + responseUnidadMedida[i].Nu_Valor + ' - ' + responseUnidadMedida[i].No_Descripcion + '</option>' );
        }
      }, 'JSON');
      
      $( '.div-Estado' ).show();
      $( '#cbo-Estado' ).html( '' );
      for (var i = 0; i < 2; i++){
        selected = '';
        if(response.Nu_Estado == i)
          selected = 'selected="selected"';
        $( '#cbo-Estado' ).append( '<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Inactivo' : 'Activo') + '</option>' );
      }
      $( '#modal-loader' ).modal('hide');
    },
    error: function (jqXHR, textStatus, errorThrown) {
      $( '#modal-loader' ).modal('hide');
	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
	    
  	  $( '#modal-message' ).modal('show');
	    $( '.modal-message' ).addClass( 'modal-danger' );
	    $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
	    setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
	    
	    //Message for developer
      console.log(jqXHR.responseText);
    }
  });
}

function form_UnidadMedida(){
  if ( accion_unidad_medida=='add_unidad_medida' || accion_unidad_medida=='upd_unidad_medida' ) {
    $( '#btn-save' ).text('');
    $( '#btn-save' ).attr('disabled', true);
    $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
  
    $( '#modal-loader' ).modal('show');
    
    url = base_url + 'Logistica/ReglasLogistica/UnidadMedidaController/crudUnidadMedida';
  	$.ajax({
      type		  : 'POST',
      dataType	: 'JSON',
  		url		    : url,
  		data		  : $('#form-UnidadMedida').serialize(),
  		success : function( response ){
  		  $( '#modal-loader' ).modal('hide');
  		  
  	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
    	  $( '#modal-message' ).modal('show');
  		  
  		  if (response.status == 'success'){
  		    accion_unidad_medida='';
  		    $('#modal-UnidadMedida').modal('hide');
    	    $( '.modal-message' ).addClass(response.style_modal);
    	    $( '.modal-title-message' ).text(response.message);
    	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
    	    reload_table_unidad_medida();
  		  } else {
    	    $( '.modal-message' ).addClass(response.style_modal);
    	    $( '.modal-title-message' ).text(response.message);
    	    setTimeout(function() {$('#modal-message').modal('hide');}, 1200);
  	    }
  
        $( '#btn-save' ).text('');
        $( '#btn-save' ).append( '<span class="fa fa-save"></span> Guardar' );
        $( '#btn-save' ).attr('disabled', false);
  		},
      error: function (jqXHR, textStatus, errorThrown) {
  		  accion_unidad_medida='';
        $( '#modal-loader' ).modal('hide');
  	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
  	    
    	  $( '#modal-message' ).modal('show');
  	    $( '.modal-message' ).addClass( 'modal-danger' );
  	    $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
  	    
  	    //Message for developer
        console.log(jqXHR.responseText);
  	    
        $( '#btn-save' ).text('');
        $( '#btn-save' ).append( '<span class="fa fa-save"></span> Guardar' );
        $( '#btn-save' ).attr('disabled', false);
      }
  	});
  }
}

function eliminarUnidadMedida(ID, accion_unidad_medida){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });
  
  $(document).bind('keydown', 'alt+l', function(){
    if ( accion_unidad_medida=='delete' ) {
      _eliminarUnidadMedida($modal_delete, ID);
      accion_unidad_medida='';
    }
  });
  
  $( '#btn-save-delete' ).off('click').click(function () {
    _eliminarUnidadMedida($modal_delete, ID);
  });
}

function _eliminarUnidadMedida($modal_delete, ID){
  $( '#modal-loader' ).modal('show');
  
  url = base_url + 'Logistica/ReglasLogistica/UnidadMedidaController/eliminarUnidadMedida/' + ID;
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
		    accion_unidad_medida='';
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
  	    reload_table_unidad_medida();
		  } else {
		    accion_unidad_medida='';
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
		  }
    },
    error: function (jqXHR, textStatus, errorThrown) {
		  accion_unidad_medida='';
		    
      $( '#modal-loader' ).modal('hide');
      $modal_delete.modal('hide');
	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
	    
  	  $( '#modal-message' ).modal('show');
	    $( '.modal-message' ).addClass( 'modal-danger' );
	    $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
	    setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
	    
	    //Message for developer
      console.log(jqXHR.responseText);
    },
  });
}

function reload_table_unidad_medida(){
  table_unidad_medida.ajax.reload(null,false);
}