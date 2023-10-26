var url;
var table_empleado;
var accion_empleado;

$(function () {
  $('.select2').select2();
  $('[data-mask]').inputmask();
  
  //LAE API SUNAT / RENIEC
  $( '#btn-cloud-api_empleado' ).click(function(){
    $( '#btn-cloud-api_empleado' ).text('');
    $( '#btn-cloud-api_empleado' ).attr('disabled', true);
    $( '#btn-cloud-api_empleado' ).append( '<i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

    // Obtener datos de SUNAT y RENIEC
    var url_api = 'https://www.laesystems.com/librerias/sunat/partner/format/json/x-api-key/';
    if ($('#cbo-TiposDocumentoIdentidad').val() == 2)//2=RENIEC, API SUNAT
      url_api = 'https://www.laesystems.com/librerias/reniec/partner/format/json/x-api-key/';
    url_api = url_api + sTokenGlobal;

    var data = {
      ID_Tipo_Documento_Identidad: $('#cbo-TiposDocumentoIdentidad').val(),
      Nu_Documento_Identidad : $( '#txt-Nu_Documento_Identidad' ).val(),
    };
    
    $.ajax({
      url   : url_api,
      type  :'POST',
      data  : data,
      success: function(response){
        if (response.success==true){
          $('[name="No_Entidad"]').val( response.data.No_Names );
        } else {
          $('[name="No_Entidad"]').val( '' );
          $( '#txt-Nu_Documento_Identidad' ).closest('.form-group').find('.help-block').html(response.msg);
          $( '#txt-Nu_Documento_Identidad' ).closest('.form-group').removeClass('has-success').addClass('has-error');
        }
        
        $( '#txt-Nu_Documento_Identidad' ).focus();
        
        $( '#btn-cloud-api_empleado' ).text('');
        $( '#btn-cloud-api_empleado' ).attr('disabled', false);
        $( '#btn-cloud-api_empleado' ).append( '<i class="fa fa-cloud-download fa-lg"></i>' );
      },
      error: function(response){
        $( '#btn-cloud-api_empleado' ).closest('.form-group').find('.help-block').html('Sin acceso');
        $( '#btn-cloud-api_empleado' ).closest('.form-group').removeClass('has-success').addClass('has-error');
        
        $( '[name="No_Entidad"]' ).val( '' );
        
        $( '#btn-cloud-api_empleado' ).text('');
        $( '#btn-cloud-api_empleado' ).attr('disabled', false);
        $( '#btn-cloud-api_empleado' ).append( '<i class="fa fa-cloud-download fa-lg"></i>' );
      }
    });
  })

  /* Tipo Documento Identidad */
  $('#cbo-TiposDocumentoIdentidad').change(function () {
    $('#div-api-dni_ruc').hide();
    if ($(this).val() == 2) {//DNI
      $('#div-api-dni_ruc').show();
      $('#label-Nombre_Documento_Identidad').text('DNI (*)');
      $('#label-No_Entidad').text('Nombre(s) y Apellidos (*)');
      $('#txt-Nu_Documento_Identidad').attr('maxlength', $(this).find(':selected').data('nu_cantidad_caracteres'));
    } else if ($(this).val() == 4) {//RUC
      $('#div-api-dni_ruc').show();
      $('#label-Nombre_Documento_Identidad').text('RUC');
      $('#label-No_Entidad').text('Razón Social');
      $('#txt-Nu_Documento_Identidad').attr('maxlength', $(this).find(':selected').data('nu_cantidad_caracteres'));
    } else {
      $('#label-Nombre_Documento_Identidad').text('OTROS (*)');
      $('#label-No_Entidad').text('Nombre(s) y Apellidos (*)');
      $('#txt-Nu_Documento_Identidad').attr('maxlength', $(this).find(':selected').data('nu_cantidad_caracteres'));
    }
  })

  url = base_url + 'Personal/DeliveryController/ajax_list';
  table_empleado = $( '#table-Delivery' ).DataTable({
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
        data.Filtros_Deliverys = $( '#cbo-Filtros_Deliverys' ).val(),
        data.Global_Filter = $( '#txt-Global_Filter' ).val();
      },
    },
    'columnDefs': [{
      'className' : 'text-center',
      'targets'   : 'no-sort',
      'orderable' : false,
    },],
  });
  
  $( '.dataTables_length' ).addClass('col-md-3');
  $( '.dataTables_paginate' ).addClass('col-md-9');
  
  $( '#txt-Global_Filter' ).keyup(function() {
    table_empleado.search($(this).val()).draw();
  });
  
  $( '#form-Delivery' ).validate({
		rules:{
			Nu_Documento_Identidad: {
				required: true,
				maxlength: 16
			},
			No_Entidad: {
				required: true
			},
		},
		messages:{
			Nu_Documento_Identidad:{
				required: "Ingresar número",
				maxlength: "Máximo 16 dígitos"
			},
			No_Entidad:{
				required: "Ingresar nombre",
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
		submitHandler: form_Delivery
	});
	
  $(document).bind('keydown', 'f2', function(){
    agregarDelivery();
  });
  
  $(document).bind('keydown', 'esc', function(){
    $( "#modal-Delivery" ).modal('hide');
	});
})

function agregarDelivery(){
  accion_empleado='add_empleado';
  $( '#form-Delivery' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  $( '#modal-Delivery' ).modal('show');
  $('.modal-title').text('Nuevo Delivery');
  
  $( '[name="EID_Empresa"]' ).val('');
  $( '[name="EID_Entidad"]' ).val('');
  $( '[name="ENu_Documento_Identidad"]' ).val('');
  
	$( '#modal-Delivery' ).on('shown.bs.modal', function() {
		$( '#txt-Nu_Documento_Identidad' ).focus();
	});

  $('#txt-Nu_Documento_Identidad').attr('maxlength', 8);

  $('#div-api-dni_ruc').show();
  url = base_url + 'HelperController/getTiposDocumentoIdentidad';
  $.post(url, function (response) {
    $('#cbo-TiposDocumentoIdentidad').html('');
    for (var i = 0; i < response.length; i++) {
      if (response[i]['ID_Tipo_Documento_Identidad'] == 1 || response[i]['ID_Tipo_Documento_Identidad'] == 2 || response[i]['ID_Tipo_Documento_Identidad'] == 4)
        $('#cbo-TiposDocumentoIdentidad').append('<option value="' + response[i]['ID_Tipo_Documento_Identidad'] + '" data-nu_cantidad_caracteres="' + response[i]['Nu_Cantidad_Caracteres'] + '">' + response[i]['No_Tipo_Documento_Identidad_Breve'] + '</option>');
    }
  }, 'JSON');

  $( '.div-Estado' ).hide();
  $( '#cbo-Estado' ).html( '<option value="1">Activo</option>' );
}

function verDelivery(ID){
  accion_empleado='upd_empleado';
  $( '#form-Delivery' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '#modal-loader' ).modal('show');
 
  url = base_url + 'Personal/DeliveryController/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $( '#modal-Delivery' ).modal('show');
      $( '.modal-title' ).text('Modifcar Delivery');
      
      $( '[name="EID_Empresa"]' ).val(response.ID_Empresa);
      $( '[name="EID_Entidad"]' ).val(response.ID_Entidad);
      $( '[name="ENu_Documento_Identidad"]' ).val(response.Nu_Documento_Identidad);
      
    	$( '#modal-Delivery' ).on('shown.bs.modal', function() {
    		$( '#txt-Nu_Documento_Identidad' ).focus();
    	})

      $('#div-api-dni_ruc').hide();
      $('#cbo-TiposDocumentoIdentidad').html('');
      url = base_url + 'HelperController/getTiposDocumentoIdentidad';
      $.post(url, function (responseTDI) {
        $('#cbo-TiposDocumentoIdentidad').html('');
        for (var i = 0; i < responseTDI.length; i++) {
          if (responseTDI[i]['ID_Tipo_Documento_Identidad'] == 1 || responseTDI[i]['ID_Tipo_Documento_Identidad'] == 2 || responseTDI[i]['ID_Tipo_Documento_Identidad'] == 4) {
            selected = '';
            if (response.ID_Tipo_Documento_Identidad == responseTDI[i]['ID_Tipo_Documento_Identidad'])
              selected = 'selected="selected"';
            if (response.ID_Tipo_Documento_Identidad == 2) {
              $('#div-api-dni_ruc').show();
              $('#txt-Nu_Documento_Identidad').attr('maxlength', 8);
            } else if (response.ID_Tipo_Documento_Identidad == 4) {
              $('#div-api-dni_ruc').show();
              $('#txt-Nu_Documento_Identidad').attr('maxlength', 11);
            } else {
              $('#txt-Nu_Documento_Identidad').attr('maxlength', 15);
            }
            $('#cbo-TiposDocumentoIdentidad').append('<option value="' + responseTDI[i]['ID_Tipo_Documento_Identidad'] + '" data-nu_cantidad_caracteres="' + responseTDI[i]['Nu_Cantidad_Caracteres'] + '" ' + selected + '>' + responseTDI[i]['No_Tipo_Documento_Identidad_Breve'] + '</option>');
          }
        }
      }, 'JSON');

      $( '[name="Nu_Documento_Identidad"]' ).val(response.Nu_Documento_Identidad);
      $( '[name="No_Entidad"]' ).val(response.No_Entidad);
      $('[name="Nu_Celular_Entidad"]').val(response.Nu_Celular_Entidad);  
      $('[name="Txt_Direccion_Entidad"]').val(response.Txt_Direccion_Entidad);
      
      $( '.div-Estado' ).show();
      $( '#cbo-Estado' ).html( '' );
      var selected;
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

function form_Delivery(){
  if ( accion_empleado=='add_empleado' || accion_empleado=='upd_empleado' ) {
    $( '#btn-save' ).text('');
    $( '#btn-save' ).attr('disabled', true);
    $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
  
    $( '#modal-loader' ).modal('show');
    
    url = base_url + 'Personal/DeliveryController/crudDelivery';
  	$.ajax({
      type		  : 'POST',
      dataType	: 'JSON',
  		url		    : url,
  		data		  : $('#form-Delivery').serialize(),
  		success : function( response ){
  		  $( '#modal-loader' ).modal('hide');
  		  
  	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
    	  $( '#modal-message' ).modal('show');
  		  
  		  if (response.status == 'success'){
  		    accion_empleado='';
  		    $('#modal-Delivery').modal('hide');
    	    $( '.modal-message' ).addClass(response.style_modal);
    	    $( '.modal-title-message' ).text(response.message);
    	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
    	    reload_table_empleado();
  		  } else {
    	    $( '.modal-message' ).addClass(response.style_modal);
    	    $( '.modal-title-message' ).text(response.message);
    	    setTimeout(function() {$('#modal-message').modal('hide');}, 1200);
  		  }
  	  
        $( '#btn-save' ).text('');
        $( '#btn-save' ).append( '<span class="fa fa-save"></span> Guardar (ENTER)' );
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
}

function eliminarDelivery(ID, accion_empleado){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $(document).bind('keydown', 'return', function(){
    if ( accion_empleado=='delete' ) {
      _eliminarDelivery($modal_delete, ID);
      accion_empleado='';
    }
  });

  $( '#btn-save-delete' ).off('click').click(function () {
    _eliminarDelivery($modal_delete, ID);
  });
}

function reload_table_empleado(){
  table_empleado.ajax.reload(null,false);
}

function _eliminarDelivery($modal_delete, ID){
  $( '#modal-loader' ).modal('show');
  
  url = base_url + 'Personal/DeliveryController/eliminarDelivery/' + ID;
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
		    accion_empleado='';
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
  	    reload_table_empleado();
		  } else {
		    accion_empleado='';
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
		  }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      accion_empleado='';
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