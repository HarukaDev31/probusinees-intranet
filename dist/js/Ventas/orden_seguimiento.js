var url;
var table_orden_seguimiento;
var accion_orden_seguimiento;
var arrHoraMinuto =
'{' +
  '"hora":[' +
    '{"value":"00"},' +
    '{"value":"01"},' +
    '{"value":"02"},' +
    '{"value":"03"},' +
    '{"value":"04"},' +
    '{"value":"05"},' +
    '{"value":"06"},' +
    '{"value":"07"},' +
    '{"value":"08"},' +
    '{"value":"09"},' +
    '{"value":"10"},' +
    '{"value":"11"},' +
    '{"value":"12"},' +
    '{"value":"13"},' +
    '{"value":"14"},' +
    '{"value":"15"},' +
    '{"value":"16"},' +
    '{"value":"17"},' +
    '{"value":"18"},' +
    '{"value":"19"},' +
    '{"value":"20"},' +
    '{"value":"21"},' +
    '{"value":"22"},' +
    '{"value":"23"}' +
  '],' +
  '"minuto":[' +
    '{"value":"00"},' +
    '{"value":"15"},' +
    '{"value":"30"},' +
    '{"value":"45"}' +
  ']' +
'}';
arrHoraMinuto = JSON.parse(arrHoraMinuto);

$(function () {
	$(document).keyup(function(event){
    if(event.which == 27){//ESC
      $( "#modal-OrdenSeguimiento" ).modal('hide');
    }
    
    if(event.which == 13 && $( "#modal-OrdenSeguimiento" ).is(":visible") == true){//ENTER
      accion_orden_seguimiento='add_orden_seguimiento';
      form_OrdenSeguimiento();
    }
	});
  
  $('.select2').select2();
  $('[data-mask]').inputmask();

  $('#cbo-filtro_almacen').html('<option value="0" selected="selected">- Todos -</option>');
  url = base_url + 'HelperController/getAlmacenes';
  var arrParams = {
    iIdOrganizacion: $('#header-a-id_organizacion').val(),
  };
  $.post(url, arrParams, function (response) {
    $('#cbo-filtro_almacen').html('<option value="0" selected="selected">- Todos -</option>');
    for (var i = 0; i < response.length; i++)
      $('#cbo-filtro_almacen').append('<option value="' + response[i].ID_Almacen + '">' + response[i].No_Almacen + '</option>');
  }, 'JSON');
  
  url = base_url + 'Ventas/OrdenSeguimientoController/ajax_list';
  table_orden_seguimiento = $('#table-OrdenSeguimiento').DataTable({
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
      'data': function (data) {
        data.filtro_almacen = $('#cbo-filtro_almacen').val(),
        data.Filtro_Fe_Inicio       = ParseDateString($( '#txt-Filtro_Fe_Inicio' ).val(), 1, '/'),
        data.Filtro_Fe_Fin          = ParseDateString($( '#txt-Filtro_Fe_Fin' ).val(), 1, '/'),
        data.Filtro_NumeroDocumento = $( '#txt-Filtro_NumeroDocumento' ).val(),
        data.Filtro_Contacto        = $( '#txt-Filtro_Contacto' ).val(),
        data.Filtro_Entidad         = $( '#txt-Filtro_Entidad' ).val();
      },
    },
    'columnDefs': [{
        'className'     : 'text-center',
        'targets'       : 'no-sort',
        'orderable'     : false,
    },{
        'className'     : 'text-left',
        'targets'       : 'no-sort_left',
        'orderable'     : false,
    },],
    'lengthMenu': [[10, 100, 1000, -1], [10, 100, 1000, "Todos"]],
  });

  $('.dataTables_length').addClass('col-xs-4 col-sm-5 col-md-1');
  $('.dataTables_info').addClass('col-xs-8 col-sm-7 col-md-4');
  $('.dataTables_paginate').addClass('col-xs-12 col-sm-12 col-md-7');
  
  $( '#btn-filter' ).click(function(){
    table_orden_seguimiento.ajax.reload();
  });
  
  $( "#form-OrdenSeguimiento" ).validate({
		rules:{
			ID_Tipo_Orden_Seguimiento: {
				required: true
			},
			Fe_Registro: {
				required: true
			},
			ID_Documento_Cabecera: {
				required: true
			},
			ID_Numero_Documento: {
				required: true
			},
			No_Contacto: {
				required: true
			},
			Txt_Observacion: {
				required: true
			},
			Txt_Email_Contacto: {
				validemail: true
			},
		},
		messages:{
			ID_Tipo_Orden_Seguimiento:{
				required: "Seleccionar tipo",
			},
			Fe_Registro:{
				required: "Ingresar F. Registro",
			},
			ID_Documento_Cabecera:{
				required: "Ingresar orden",
			},
			ID_Numero_Documento:{
				required: "Ingresar orden",
			},
			No_Contacto:{
				required: "Ingresar contacto",
			},
			Txt_Observacion:{
				required: "Ingresar observación",
			},
			Txt_Email_Contacto:{
				validemail : "Ingresar un correo válido"
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
		submitHandler: form_OrdenSeguimiento
	});
	
  //LAE API SUNAT / RENIEC - CONTACTO
  $( '#btn-cloud-api_orden_seguimiento_contacto' ).click(function(){
    if ( $( '#cbo-tipos_documento_identidad_contacto' ).val().length === 0){
      $( '#cbo-tipos_documento_identidad_contacto' ).closest('.form-group').find('.help-block').html('Seleccionar tipo doc. identidad');
  	  $( '#cbo-tipos_documento_identidad_contacto' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ($( '#cbo-tipos_documento_identidad_contacto' ).find(':selected').data('nu_cantidad_caracteres') != $( '#txt-Nu_Documento_Identidad').val().length ) {
      $( '#txt-Nu_Documento_Identidad' ).closest('.form-group').find('.help-block').html('Debe ingresar ' + $( '#cbo-tipos_documento_identidad_contacto' ).find(':selected').data('nu_cantidad_caracteres') + ' dígitos' );
  	  $( '#txt-Nu_Documento_Identidad' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( 
        (
          $( '#cbo-tipos_documento_identidad_contacto' ).val() == 1 ||
          $( '#cbo-tipos_documento_identidad_contacto' ).val() == 3 ||
          $( '#cbo-tipos_documento_identidad_contacto' ).val() == 5 ||
          $( '#cbo-tipos_documento_identidad_contacto' ).val() == 6
        )
        ) {
      $( '#cbo-tipos_documento_identidad_contacto' ).closest('.form-group').find('.help-block').html('Disponible DNI / RUC');
  	  $( '#cbo-tipos_documento_identidad_contacto' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      $( '#btn-cloud-api_orden_seguimiento_contacto' ).text('');
      $( '#btn-cloud-api_orden_seguimiento_contacto' ).attr('disabled', true);
      $( '#btn-cloud-api_orden_seguimiento_contacto' ).append( '<i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
      
      // Obtener datos de SUNAT y RENIEC
      var url_api = 'https://www.laesystems.com/librerias/sunat/partner/format/json/x-api-key/';
			if ( $( '#cbo-api_orden_seguimiento_contacto' ).val() == 2 )//2=RENIEC
				url_api = 'https://www.laesystems.com/librerias/reniec/partner/format/json/x-api-key/';
			url_api = url_api + sTokenGlobal;
			
      var data = {
        ID_Tipo_Documento_Identidad : $( '#cbo-tipos_documento_identidad_contacto' ).val(),
        Nu_Documento_Identidad : $( '#txt-Nu_Documento_Identidad' ).val(),
      };
      
      $.ajax({
        url   : url_api,
        type  :'POST',
        data  : data,
        success: function(response){
          $( '#btn-cloud-api_orden_seguimiento_contacto' ).closest('.form-group').find('.help-block').html('');
      	  $( '#btn-cloud-api_orden_seguimiento_contacto' ).closest('.form-group').removeClass('has-success').addClass('has-error');
      	  
          if (response.success == true){
            $( '#txt-No_Contacto' ).val( response.data.No_Names );
            if ( $( '#cbo-tipos_documento_identidad_contacto option:selected' ).val() == 4) {//RUC
              $( '#txt-Nu_Telefono_Contacto' ).val( response.data.Nu_Phone );
              $( '#txt-Nu_Celular_Contacto' ).val( response.data.Nu_Cellphone );
            }
          } else {
            $( '#txt-No_Contacto' ).val( '' );
            if ( $( '#cbo-tipos_documento_identidad_contacto option:selected' ).val() == 4) {//RUC
              $( '#txt-Nu_Telefono_Contacto' ).val( '' );
              $( '#txt-Nu_Celular_Contacto' ).val( '' );
            }
            $( '#txt-Nu_Documento_Identidad' ).closest('.form-group').find('.help-block').html(response.msg);
        	  $( '#txt-Nu_Documento_Identidad' ).closest('.form-group').removeClass('has-success').addClass('has-error');
        	  
  		  	  $( '#txt-Nu_Documento_Identidad' ).focus();
  		  	  $( '#txt-Nu_Documento_Identidad' ).select();
          }
  		  	
          $( '#btn-cloud-api_orden_seguimiento_contacto' ).text('');
          $( '#btn-cloud-api_orden_seguimiento_contacto' ).attr('disabled', false);
          $( '#btn-cloud-api_orden_seguimiento_contacto' ).append( '<i class="fa fa-cloud-download fa-lg"></i>' );
        },
        error: function(response){
          $( '#btn-cloud-api_orden_seguimiento_contacto' ).closest('.form-group').find('.help-block').html('Sin acceso');
      	  $( '#btn-cloud-api_orden_seguimiento_contacto' ).closest('.form-group').removeClass('has-success').addClass('has-error');
      	  
          $( '#txt-No_Contacto' ).val( '' );
          $( '#txt-Nu_Telefono_Contacto' ).val( '' );
          $( '#txt-Nu_Celular_Contacto' ).val( '' );
              
          $( '#btn-cloud-api_orden_seguimiento_contacto' ).text('');
          $( '#btn-cloud-api_orden_seguimiento_contacto' ).attr('disabled', false);
          $( '#btn-cloud-api_orden_seguimiento_contacto' ).append( '<i class="fa fa-cloud-download fa-lg"></i>' );
        }
      });
    }
  })
})

function agregarOrdenSeguimiento(){
  accion_orden_seguimiento='add_orden_seguimiento';
  
  $( '#form-OrdenSeguimiento' )[0].reset();
  
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  
  $( '.help-block' ).empty();
  
  $( '#modal-OrdenSeguimiento' ).modal('show');
  
  $( '.modal-title' ).text('Nueva orden seguimiento');
  
  $('[name="EID_Tipo_Documento"]').val('');
  $('[name="EID_Orden_Seguimiento"]').val('');
  
	$( '#modal-OrdenSeguimiento' ).on('shown.bs.modal', function() {
		$( '#txt-ID_Documento_Cabecera' ).focus();
	})
  
  url = base_url + 'HelperController/getTiposOrdenSeguimiento';
  $.post( url , function( response ){
    $( '#cbo-tipos_orden_seguimiento' ).html('<option value="" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-tipos_orden_seguimiento' ).append( '<option value="' + response[i].Nu_Valor + '">' + response[i].No_Descripcion + '</option>' );
  }, 'JSON');
  
  $( '.date-picker-invoice' ).val(fDay + '/' + fMonth + '/' + fYear);
  
  $( '#cbo-hora' ).html( '' );
  for (var i = 0; i < arrHoraMinuto.hora.length; i++)
    $( '#cbo-hora' ).append( '<option value="' + arrHoraMinuto.hora[i].value + '">' + arrHoraMinuto.hora[i].value + '</option>' );

  $( '#cbo-minuto' ).html( '' );
  for (var i = 0; i < arrHoraMinuto.minuto.length; i++)
    $( '#cbo-minuto' ).append( '<option value="' + arrHoraMinuto.minuto[i].value + '">' + arrHoraMinuto.minuto[i].value + '</option>' );

  $( '.div-contacto_existente' ).show();
  $( '.div-contacto_nuevo' ).hide();

  $( "#radio-Nu_Tipo_Contacto_Existente" ).prop("checked", true);
  $( "#radio-Nu_Tipo_Contacto_Nuevo" ).prop("checked", false);
  
  url = base_url + 'HelperController/getTiposDocumentoIdentidad';
  $.post( url , function( response ){
    $( '#cbo-tipos_documento_identidad_contacto' ).html('<option value="" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-tipos_documento_identidad_contacto' ).append( '<option value="' + response[i].ID_Tipo_Documento_Identidad + '" data-nu_cantidad_caracteres="' + response[i].Nu_Cantidad_Caracteres + '">' + response[i].No_Tipo_Documento_Identidad_Breve + '</option>' );
  }, 'JSON');
}

function verOrdenSeguimiento(ID_Orden_Seguimiento){
  accion_orden_seguimiento='upd_orden_seguimiento';
  
  $( '#form-OrdenSeguimiento' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '#modal-loader' ).modal('show');
 
	/* Tipo Documento Identidad */
	$( '#cbo-tipos_documento_identidad_contacto' ).change(function(){
	  if ( $(this).val() == 2 ) {//DNI
		  $( '#label-Nombre_Documento_Identidad' ).text('DNI');
		  $( '#label-No_Contacto' ).text('Nombre(s) y Apellidos');
			$( '#txt-Nu_Documento_Identidad' ).attr('maxlength', $(this).find(':selected').data('nu_cantidad_caracteres'));
	  } else if ( $(this).val() == 4 ) {//RUC
		  $( '#label-Nombre_Documento_Identidad' ).text('RUC');
		  $( '#label-No_Contacto' ).text('Razón Social');
			$( '#txt-Nu_Documento_Identidad' ).attr('maxlength', $(this).find(':selected').data('nu_cantidad_caracteres'));
	  } else {
	    $( '#label-Nombre_Documento_Identidad' ).text('# Documento Identidad');
		  $( '#label-No_Contacto' ).text('Nombre(s) y Apellidos');
			$( '#txt-Nu_Documento_Identidad' ).attr('maxlength', $(this).find(':selected').data('nu_cantidad_caracteres'));
	  }
	})
	
  url = base_url + 'Ventas/OrdenSeguimientoController/ajax_edit/' + ID_Orden_Seguimiento;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $( '#modal-loader' ).modal('hide');
      
      $( '#modal-OrdenSeguimiento' ).modal('show');
      $( '.modal-title' ).text('Modifcar orden seguimiento');
      
      $('[name="EID_Orden_Seguimiento"]').val(response.ID_Orden_Seguimiento);
      
      var selected = '';
      
      url = base_url + 'HelperController/getTiposOrdenSeguimiento';
      $.post( url , function( responseTiposOrden ){
        $( '#cbo-tipos_orden_seguimiento' ).html('');
        for (var i = 0; i < responseTiposOrden.length; i++){
          selected = '';
          if(response.ID_Tipo_Orden_Seguimiento == responseTiposOrden[i].Nu_Valor)
            selected = 'selected="selected"';
          $( '#cbo-tipos_orden_seguimiento' ).append( '<option value="' + responseTiposOrden[i].Nu_Valor + '" ' + selected + '>' + responseTiposOrden[i].No_Descripcion + '</option>' );
        }
      }, 'JSON');
      
      $('[name="Fe_Registro"]').val( ParseDateString(response.Fe_Registro, 3, '-') );
      
      $( '#cbo-hora' ).html('');
      for (var i = 0; i < arrHoraMinuto.hora.length; i++) {
        selected = '';
        if(ParseDateString(response.Fe_Registro, 4, '-') == arrHoraMinuto.hora[i].value)
            selected = 'selected="selected"';
        $( '#cbo-hora' ).append( '<option value="' + arrHoraMinuto.hora[i].value + '" ' + selected + '>' + arrHoraMinuto.hora[i].value + '</option>' );
      }
      
      $( '#cbo-minuto' ).html('');
      for (var i = 0; i < arrHoraMinuto.minuto.length; i++) {
        selected = '';
        if(ParseDateString(response.Fe_Registro, 5, '-') == arrHoraMinuto.minuto[i].value)
            selected = 'selected="selected"';
        $( '#cbo-minuto' ).append( '<option value="' + arrHoraMinuto.minuto[i].value + '" ' + selected + '>' + arrHoraMinuto.minuto[i].value + '</option>' );
      }
      
      $('[name="ID_Documento_Cabecera"]').val(response.ID_Documento_Cabecera);
      $('[name="ID_Numero_Documento"]').val(response.ID_Numero_Documento);
      
      $( "#radio-Nu_Tipo_Contacto_Existente" ).prop("checked", true);
      $( "#radio-Nu_Tipo_Contacto_Nuevo" ).prop("checked", false);
      
      $( '.div-contacto_existente' ).show();
      $( '.div-contacto_nuevo' ).hide();
  
      $( '#txt-No_Contacto_existe' ).val(response.No_Contacto);
      if (response.Nu_Tipo_Contacto == 1){//Nuevo contacto
        $( "#radio-Nu_Tipo_Contacto_Existente" ).prop("checked", false);
        $( "#radio-Nu_Tipo_Contacto_Nuevo" ).prop("checked", true);
        
        $( '.div-contacto_nuevo' ).show();
        $( '.div-contacto_existente' ).hide();
      
        url = base_url + 'HelperController/getTiposDocumentoIdentidad';
        $.post( url , function( responseTipoDocumentoIdentidad ){
          $( '#cbo-tipos_documento_identidad_contacto' ).html('');
          for (var i = 0; i < responseTipoDocumentoIdentidad.length; i++) {
            selected = '';
            if(response.ID_Tipo_Documento_Identidad == responseTipoDocumentoIdentidad[i].ID_Tipo_Documento_Identidad)
              selected = 'selected="selected"';
            $( '#cbo-tipos_documento_identidad_contacto' ).append( '<option value="' + responseTipoDocumentoIdentidad[i].ID_Tipo_Documento_Identidad + '" data-nu_cantidad_caracteres="' + responseTipoDocumentoIdentidad[i].Nu_Cantidad_Caracteres + '" ' + selected + '>' + responseTipoDocumentoIdentidad[i].No_Tipo_Documento_Identidad_Breve + '</option>' );
          }
        }, 'JSON');
  
        $('[name="Nu_Documento_Identidad"]').val(response.Nu_Documento_Identidad);
        $('[name="No_Contacto"]').val(response.No_Contacto_Seguimiento);
        $('[name="Txt_Email_Contacto"]').val(response.Txt_Email_Contacto);
        $('[name="Nu_Celular_Contacto"]').val(response.Nu_Celular_Contacto);
        $('[name="Nu_Telefono_Contacto"]').val(response.Nu_Telefono_Contacto);
      }
      
      $( '[name="Txt_Observacion"]' ).val( clearHTMLTextArea(response.Txt_Observacion) );
    }
  });
}

function form_OrdenSeguimiento(){
  if (accion_orden_seguimiento=='add_orden_seguimiento' || accion_orden_seguimiento=='upd_orden_seguimiento') {
    
    if ( $( '#cbo-tipos_orden_seguimiento' ).val() == '' ) {
      $( '#cbo-tipos_orden_seguimiento' ).closest('.form-group').find('.help-block').html('Seleccionar tipo');
  	  $( '#cbo-tipos_orden_seguimiento' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $( '#txt-ID_Documento_Cabecera' ).val() == '' && $( '#txt-ID_Numero_Documento').val() == '' ) {
      $( '#txt-ID_Numero_Documento' ).closest('.form-group').find('.help-block').html('Seleccionar orden');
  	  $( '#txt-ID_Numero_Documento' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $( '[name="Txt_Observacion"]' ).val().length <= 10) {
      $( '[name="Txt_Observacion"]' ).closest('.form-group').find('.help-block').html('Ingresar observación');
  	  $( '[name="Txt_Observacion"]' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $( '[name="Nu_Tipo_Contacto"]:checked' ).attr('value') == 1 && $( '#txt-No_Contacto' ).val().length === 0) {
      $( '#txt-No_Contacto' ).closest('.form-group').find('.help-block').html('Ingresar contacto');
  	  $( '#txt-No_Contacto' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $( '[name="Nu_Tipo_Contacto"]:checked' ).attr('value') == 1 && $( '#txt-Txt_Email_Contacto' ).val().length === 0) {
      $( '#txt-Txt_Email_Contacto' ).closest('.form-group').find('.help-block').html('Ingresar correo');
  	  $( '#txt-Txt_Email_Contacto' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      $( '#btn-save' ).text('');
      $( '#btn-save' ).attr('disabled', true);
      $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
    
      $( '#modal-loader' ).modal('show');
      
      url = base_url + 'Ventas/OrdenSeguimientoController/crudOrdenSeguimiento';
    	$.ajax({
        type		  : 'POST',
        dataType	: 'JSON',
    		url		    : url,
    		data		  : $('#form-OrdenSeguimiento').serialize(),
    		success : function( response ){
    		  $( '#modal-loader' ).modal('hide');
    		  
    	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
      	  $( '#modal-message' ).modal('show');
    		  
    		  if (response.status == 'success'){
    		    accion_orden_seguimiento='';
    		    
    		    $('#modal-OrdenSeguimiento').modal('hide');
    		    
      	    $( '.modal-message' ).addClass(response.style_modal);
      	    $( '.modal-title-message' ).text(response.message);
      	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
      	    reload_table_orden_seguimiento();
    		  } else {
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
}

function eliminarOrdenSeguimiento(ID_Orden_Seguimiento){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });

  accion_orden_seguimiento='delete_orden_seguimiento';
  $(document).keyup(function(event){
    if(event.which == 13 && accion_orden_seguimiento=='delete_orden_seguimiento'){
      eliminarData_OrdenSeguimiento($modal_delete, ID_Orden_Seguimiento);
    }
  });

  $( '#btn-save-delete' ).off('click').click(function () {
    eliminarData_OrdenSeguimiento($modal_delete, ID_Orden_Seguimiento);
  });
}

function reload_table_orden_seguimiento(){
  table_orden_seguimiento.ajax.reload(null,false);
}

function addContacto(tipo){
  $( '.div-contacto_existente' ).show();
  $( '.div-contacto_nuevo' ).hide();
  if (tipo == 1) {
    $( '.div-contacto_nuevo' ).show();
    $( '.div-contacto_existente' ).hide();
  }
}

function eliminarData_OrdenSeguimiento($modal_delete, ID_Orden_Seguimiento){
  $( '#modal-loader' ).modal('show');
    
  url = base_url + 'Ventas/OrdenSeguimientoController/eliminarOrdenSeguimiento/' + ID_Orden_Seguimiento;
  $.ajax({
    url       : url,
    type      : "GET",
    dataType  : "JSON",
    success: function( response ){
      $( '#modal-loader' ).modal('hide');
      
	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
  	  $( '#modal-message' ).modal('show');
		  
		  if (response.status == 'success'){
    		accion_orden_seguimiento='';
    		
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
  	    reload_table_orden_seguimiento();
  	    $modal_delete.modal('hide');
		  } else {
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
		  }
    }
  });
}