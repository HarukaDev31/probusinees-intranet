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
    } else {
      $('#label-Nombre_Documento_Identidad').text('OTROS (*)');
      $('#label-No_Entidad').text('Nombre(s) y Apellidos (*)');
      $('#txt-Nu_Documento_Identidad').attr('maxlength', $(this).find(':selected').data('nu_cantidad_caracteres'));
    }
  })

  url = base_url + 'Personal/EmpleadoController/ajax_list';
  table_empleado = $( '#table-Empleado' ).DataTable({
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
        data.Filtros_Empleados = $( '#cbo-Filtros_Empleados' ).val(),
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
  
  $( '#form-Empleado' ).validate({
		rules:{
			Nu_Documento_Identidad: {
				required: true,
				maxlength: 16
			},
			No_Entidad: {
				required: true
			},
			Nu_Pin_Caja: {
				minlength: 4,
				maxlength: 4
      },
      Nu_Celular_Entidad: {
        minlength: 11,
        maxlength: 11
      },
      Txt_Email_Entidad: {
        validemail: true,
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
			Nu_Pin_Caja:{
				minlength: "Ingresar 4 dígitos",
				maxlength: "Ingresar 4 dígitos",
      },
      Nu_Celular_Entidad: {
        minlength: "Debe ingresar 9 dígitos",
        maxlength: "Debe ingresar 9 dígitos"
      },
      Txt_Email_Entidad: {
        validemail: "Ingresar correo válido",
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
		submitHandler: form_Empleado
	});
	
  $(document).bind('keydown', 'f2', function(){
    agregarEmpleado();
  });
  
  $(document).bind('keydown', 'esc', function(){
    $( "#modal-Empleado" ).modal('hide');
	});
})

function agregarEmpleado(){
  accion_empleado='add_empleado';
  $( '#form-Empleado' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  $( '#modal-Empleado' ).modal('show');
  $( '.modal-title' ).text('Nuevo Personal');
  
  $( '[name="EID_Empresa"]' ).val('');
  $( '[name="EID_Entidad"]' ).val('');
  $( '[name="ENu_Documento_Identidad"]' ).val('');
  $( '[name="ENu_Pin_Caja"]' ).val('');
  
	$( '#modal-Empleado' ).on('shown.bs.modal', function() {
		$( '#txt-Nu_Documento_Identidad' ).focus();
	});

  $('#txt-Nu_Documento_Identidad').attr('maxlength', 8);

  $('#div-api-dni_ruc').show();
  url = base_url + 'HelperController/getTiposDocumentoIdentidad';
  $.post(url, function (response) {
    $('#cbo-TiposDocumentoIdentidad').html('');
    for (var i = 0; i < response.length; i++) {
      if (response[i]['ID_Tipo_Documento_Identidad'] == 1 || response[i]['ID_Tipo_Documento_Identidad'] == 2) 
        $('#cbo-TiposDocumentoIdentidad').append('<option value="' + response[i]['ID_Tipo_Documento_Identidad'] + '" data-nu_cantidad_caracteres="' + response[i]['Nu_Cantidad_Caracteres'] + '">' + response[i]['No_Tipo_Documento_Identidad_Breve'] + '</option>');
    }
  }, 'JSON');

  url = base_url + 'HelperController/getTiposSexo';
  $.post( url, function( response ){
    $( '#cbo-Sexos' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-Sexos' ).append( '<option value="' + response[i].Nu_Valor + '">' + response[i].No_Descripcion + '</option>' );
  }, 'JSON');
	
  url = base_url + 'HelperController/getDistritos';
  $.post( url, {ID_Provincia : 1}, function( response ){
    $( '#cbo-Distritos' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-Distritos' ).append( '<option value="' + response[i].ID_Distrito + '">' + response[i].No_Distrito + '</option>' );
  }, 'JSON');

  $( '.div-Estado' ).hide();
  $( '#cbo-Estado' ).html( '<option value="1">Activo</option>' );
}

function verEmpleado(ID){
  accion_empleado='upd_empleado';
  $( '#form-Empleado' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '#modal-loader' ).modal('show');
 
  url = base_url + 'Personal/EmpleadoController/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $( '#modal-Empleado' ).modal('show');
      $( '.modal-title' ).text('Modifcar Personal');
      
      $( '[name="EID_Empresa"]' ).val(response.ID_Empresa);
      $( '[name="EID_Entidad"]' ).val(response.ID_Entidad);
      $( '[name="ENu_Documento_Identidad"]' ).val(response.Nu_Documento_Identidad);
      $( '[name="ENu_Pin_Caja"]' ).val(response.Nu_Pin_Caja);
      
    	$( '#modal-Empleado' ).on('shown.bs.modal', function() {
    		$( '#txt-Nu_Documento_Identidad' ).focus();
    	})

      $('#div-api-dni_ruc').hide();
      $('#cbo-TiposDocumentoIdentidad').html('');
      url = base_url + 'HelperController/getTiposDocumentoIdentidad';
      $.post(url, function (responseTDI) {
        $('#cbo-TiposDocumentoIdentidad').html('');
        for (var i = 0; i < responseTDI.length; i++) {
          if (responseTDI[i]['ID_Tipo_Documento_Identidad'] == 1 || responseTDI[i]['ID_Tipo_Documento_Identidad'] == 2) {
            selected = '';
            if (response.ID_Tipo_Documento_Identidad == responseTDI[i]['ID_Tipo_Documento_Identidad'])
              selected = 'selected="selected"';
            if (response.ID_Tipo_Documento_Identidad==2) {
              $('#div-api-dni_ruc').show();
              $('#txt-Nu_Documento_Identidad').attr('maxlength', 8);
            } else {
              $('#txt-Nu_Documento_Identidad').attr('maxlength', 15);              
            }
            $('#cbo-TiposDocumentoIdentidad').append('<option value="' + responseTDI[i]['ID_Tipo_Documento_Identidad'] + '" data-nu_cantidad_caracteres="' + responseTDI[i]['Nu_Cantidad_Caracteres'] + '" ' + selected + '>' + responseTDI[i]['No_Tipo_Documento_Identidad_Breve'] + '</option>');
          }
        }
      }, 'JSON');

      $( '[name="Nu_Documento_Identidad"]' ).val(response.Nu_Documento_Identidad);
      $( '[name="No_Entidad"]' ).val(response.No_Entidad);
      
      $( '[name="Fe_Nacimiento"]' ).val(ParseDateString(response.Fe_Nacimiento, 6, '-'));
      
      var selected;
      url = base_url + 'HelperController/getTiposSexo';
      $.post( url, function( responseSexo ){
        $( '#cbo-Sexos' ).html('');
        for (var i = 0; i < responseSexo.length; i++){
          selected = '';
          if(response.Nu_Tipo_Sexo == responseSexo[i].Nu_Valor)
            selected = 'selected="selected"';
          $( '#cbo-Sexos' ).append( '<option value="' + responseSexo[i].Nu_Valor + '" ' + selected + '>' + responseSexo[i].No_Descripcion + '</option>' );
        }
      }, 'JSON');
  
      $('[name="Nu_Celular_Entidad"]').val(response.Nu_Celular_Entidad);
      $('[name="Nu_Pin_Caja"]').val(response.Nu_Pin_Caja);
      $('[name="Txt_Email_Entidad"]').val(response.Txt_Email_Entidad);
      
      url = base_url + 'HelperController/getDistritos';
      $.post( url, {ID_Provincia : 1}, function( responseDistrito ){
        $( '#modal-loader' ).modal('hide');
        $( '#cbo-Distritos' ).html('');
        for (var i = 0; i < responseDistrito.length; i++){
          selected = '';
          if(response.ID_Distrito == responseDistrito[i].ID_Distrito)
            selected = 'selected="selected"';
          $( '#cbo-Distritos' ).append( '<option value="' + responseDistrito[i].ID_Distrito + '" ' + selected + '>' + responseDistrito[i].No_Distrito + '</option>' );
        }
      }, 'JSON');
  
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

function form_Empleado(){
  if ( accion_empleado=='add_empleado' || accion_empleado=='upd_empleado' ) {
    crudEmpleado();
  }
}

function crudEmpleado(){
  $( '#btn-save' ).text('');
  $( '#btn-save' ).attr('disabled', true);
  $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

  $( '#modal-loader' ).modal('show');
  
  url = base_url + 'Personal/EmpleadoController/crudEmpleado';
  $.ajax({
    type		  : 'POST',
    dataType	: 'JSON',
    url		    : url,
    data		  : $('#form-Empleado').serialize(),
    success : function( response ){
      $( '#modal-loader' ).modal('hide');
      
      $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
      $( '#modal-message' ).modal('show');
      
      if (response.status == 'success'){
        accion_empleado='';
        $('#modal-Empleado').modal('hide');
        $( '.modal-message' ).addClass(response.style_modal);
        $( '.modal-title-message' ).text(response.message);
        setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
        reload_table_empleado();
      } else {
        $( '.modal-message' ).addClass(response.style_modal);
        $( '.modal-title-message' ).text(response.message);
        setTimeout(function() {$('#modal-message').modal('hide');}, 4100);
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

function eliminarEmpleado(ID, accion_empleado){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $(document).bind('keydown', 'return', function(){
    if ( accion_empleado=='delete' ) {
      _eliminarEmpleado($modal_delete, ID);
      accion_empleado='';
    }
  });

  $( '#btn-save-delete' ).off('click').click(function () {
    _eliminarEmpleado($modal_delete, ID);
  });
}

function reload_table_empleado(){
  table_empleado.ajax.reload(null,false);
}

function _eliminarEmpleado($modal_delete, ID){
  $( '#modal-loader' ).modal('show');
  
  url = base_url + 'Personal/EmpleadoController/eliminarEmpleado/' + ID;
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