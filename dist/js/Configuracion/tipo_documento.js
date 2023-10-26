var url;
var table_tipo_documento;

$(function () {
  url = base_url + 'Configuracion/TipoDocumentoController/ajax_list';
  table_tipo_documento = $( '#table-Tipo_Documento' ).DataTable({
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
      'url'     : url,
      'type'      : 'POST',
      'dataType'  : 'JSON',
      'data'      : function ( data ) {
        data.Filtros_TiposDocumento = $( '#cbo-Filtros_TiposDocumento' ).val(),
        data.Global_Filter          = $( '#txt-Global_Filter' ).val();
      },
    },
    'columnDefs': [{
      'className'  : 'text-center',
      'targets'    : 'no-sort',
      'orderable'  : false,
    },],
  });
    
  $( '.dataTables_length' ).addClass('col-md-3');
  $( '.dataTables_paginate' ).addClass('col-md-9');

  $( '#txt-Global_Filter' ).keyup(function() {
    table_tipo_documento.search($(this).val()).draw();
  });
  
  $( '#form-Tipo_Documento' ).validate({
		rules:{
			No_Tipo_Documento: {
				required: true
			},
			No_Tipo_Documento_Breve: {
				required: true
			},
			Nu_Sunat_Codigo:{
				required: true,
			},
		},
		messages:{
			No_Tipo_Documento:{
				required: "Ingresar nombre",
			},
			No_Tipo_Documento_Breve:{
				required: "Ingresar nombre breve",
			},
			Nu_Sunat_Codigo:{
				required: "Ingresar código",
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
		submitHandler: form_Tipo_Documento
	});
})

function agregarTipo_Documento(){
  $( '#form-Tipo_Documento' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  
  $( '.help-block' ).empty();
  
  $( '#modal-Tipo_Documento' ).modal('show');
  
  $( '.modal-title' ).text('Nuevo Tipo Documento');
  
  $( '[name="EID_Tipo_Documento"]' ).val('');
  $( '[name="ENo_Tipo_Documento"]' ).val('');
  
  $( '#cbo-EsSunat' ).html( '<option value="0">No</option>' );
  $( '#cbo-EsSunat' ).append( '<option value="1">Si</option>' );
  
  $( '#cbo-Impuesto' ).html( '<option value="0">No</option>' );
  $( '#cbo-Impuesto' ).append( '<option value="1">Si</option>' );
  
  $( '#cbo-Enlazar' ).html( '<option value="0">No</option>' );
  $( '#cbo-Enlazar' ).append( '<option value="1">Si</option>' );
  
  $( '#cbo-estado_cotizacion' ).html( '<option value="0">No</option>' );
  $('#cbo-estado_cotizacion').append('<option value="1">Si</option>');

  $('#cbo-estado_venta').html('<option value="0">No</option>');
  $('#cbo-estado_venta').append('<option value="1">Si</option>');
  
  $( '#cbo-estado_orden_compra' ).html( '<option value="0">No</option>' );
  $('#cbo-estado_orden_compra').append('<option value="1">Si</option>');

  $('#cbo-estado_compra').html('<option value="0">No</option>');
  $('#cbo-estado_compra').append('<option value="1">Si</option>');
  
  $( '#cbo-Estado' ).html( '<option value="1">Activo</option>' );
  $( '#cbo-Estado' ).append( '<option value="0">Inactivo</option>' );
}

function verTipo_Documento(ID){
  $( '#form-Tipo_Documento' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '#modal-loader' ).modal('show');
 
  url = base_url + 'Configuracion/TipoDocumentoController/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $( '#modal-loader' ).modal('hide');
      
      $( '#modal-Tipo_Documento' ).modal('show');
      $( '.modal-title' ).text('Modifcar Tipo Documento');
      
      $( '[name="EID_Tipo_Documento"]' ).val(response.ID_Tipo_Documento);
      $( '[name="ENo_Tipo_Documento"]' ).val(response.No_Tipo_Documento);
      
      $( '[name="No_Tipo_Documento"]' ).val(response.No_Tipo_Documento);
      $( '[name="No_Tipo_Documento_Breve"]' ).val(response.No_Tipo_Documento_Breve);
      
      var selected;

      $( '#cbo-EsSunat' ).html( '' );
      for (var i = 0; i < 2; i++){
        selected = '';
        if(response.Nu_Es_Sunat == i)
          selected = 'selected="selected"';
        $( '#cbo-EsSunat' ).append( '<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'No' : 'Si') + '</option>' );
      }
      $('[name="Nu_Sunat_Codigo"]').val(response.Nu_Sunat_Codigo);
      
      $( '#cbo-Impuesto' ).html( '' );
      for (var i = 0; i < 2; i++){
        selected = '';
        if(response.Nu_Impuesto == i)
          selected = 'selected="selected"';
        $( '#cbo-Impuesto' ).append( '<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'No' : 'Si') + '</option>' );
      }
      
      $( '#cbo-estado_cotizacion' ).html( '' );
      for (var i = 0; i < 2; i++){
        selected = '';
        if (response.Nu_Cotizacion_Venta == i)
          selected = 'selected="selected"';
        $( '#cbo-estado_cotizacion' ).append( '<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'No' : 'Si') + '</option>' );
      }

      $('#cbo-estado_venta').html('');
      for (var i = 0; i < 2; i++) {
        selected = '';
        if (response.Nu_Venta == i)
          selected = 'selected="selected"';
        $('#cbo-estado_venta').append('<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'No' : 'Si') + '</option>');
      }

      $('#cbo-estado_orden_compra').html('');
      for (var i = 0; i < 2; i++) {
        selected = '';
        if (response.Nu_Orden_Compra == i)
          selected = 'selected="selected"';
        $('#cbo-estado_orden_compra').append('<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'No' : 'Si') + '</option>');
      }
      
      $( '#cbo-estado_compra' ).html( '' );
      for (var i = 0; i < 2; i++){
        selected = '';
        if (response.Nu_Compra == i)
          selected = 'selected="selected"';
        $( '#cbo-estado_compra' ).append( '<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'No' : 'Si') + '</option>' );
      }
      
      $( '#cbo-Estado' ).html( '' );
      for (var i = 0; i < 2; i++){
        selected = '';
        if(response.Nu_Estado == i)
          selected = 'selected="selected"';
        $( '#cbo-Estado' ).append( '<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Inactivo' : 'Activo') + '</option>' );
      }
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

function form_Tipo_Documento(){
  $( '#btn-save' ).text('');
  $( '#btn-save' ).attr('disabled', true);
  $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

  $( '#modal-loader' ).modal('show');
  
  url = base_url + 'Configuracion/TipoDocumentoController/crudTipo_Documento';
	$.ajax({
    type		  : 'POST',
    dataType	: 'JSON',
		url		    : url,
		data		  : $('#form-Tipo_Documento').serialize(),
		success : function( response ){
		  $( '#modal-loader' ).modal('hide');
		  
	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
  	  $( '#modal-message' ).modal('show');
		  
		  if (response.status == 'success'){
		    $('#modal-Tipo_Documento').modal('hide');
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
  	    reload_table_tipo_documento();
		  } else {
		    $( '#txt-No_Tipo_Documento' ).val('');
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1200);
		  }
		  
      $( '#btn-save' ).text('');
      $( '#btn-save' ).append( '<span class="fa fa-save"></span> Guardar' );
      $( '#btn-save' ).attr('disabled', false);
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
	    
      $( '#btn-save' ).text('');
      $( '#btn-save' ).append( '<span class="fa fa-save"></span> Guardar' );
      $( '#btn-save' ).attr('disabled', false);
    }
	});
}

function eliminarTipo_Documento(ID){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });
  
  $( '#btn-save-delete' ).off('click').click(function () {
    $( '#modal-loader' ).modal('show');
    
    url = base_url + 'Configuracion/TipoDocumentoController/eliminarTipo_Documento/' + ID;
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
    	    reload_table_tipo_documento();
  		  } else {
    	    $( '.modal-message' ).addClass(response.style_modal);
    	    $( '.modal-title-message' ).text(response.message);
    	    setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
  		  }
      },
      error: function (jqXHR, textStatus, errorThrown) {
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
  });
}

function reload_table_tipo_documento(){
  table_tipo_documento.ajax.reload(null,false);
}