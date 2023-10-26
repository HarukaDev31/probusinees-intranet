var url;
var table_Entidad;
var accion_cliente = '';

function importarExcelCliente(){
  $( ".modal_importar_cliente" ).modal( "show" );
}

$(function () {
  $('.div-mas_opciones').hide();
  $('#checkbox-mas_filtros').on('ifChanged', function(){
    $('.div-mas_opciones').hide();
    var _this = jQuery(this);
    if(_this.is(':checked')){
      $('.div-mas_opciones').show();
    }
  });

  // Validate exist file excel product
	$( document ).on('click', '#btn-excel-importar_cliente', function(event) {
	  if ( $( "#my-file-selector_cliente" ).val().length === 0 ) {
      $( '#my-file-selector_cliente' ).closest('.form-group').find('.help-block').html('Seleccionar archivo');
		  $( '#my-file-selector_cliente' ).closest('.form-group').removeClass('has-success').addClass('has-error');
	  } else {
      $( '#btn-cancel-client' ).attr('disabled', true);
      $( '#a-download-client' ).attr('disabled', true);
	    
      $( '#btn-excel-importar_cliente' ).text('');
      $( '#btn-excel-importar_cliente' ).attr('disabled', true);
      $( '#btn-excel-importar_cliente' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
  
      $( '#modal-loader' ).modal('show');
	  }
  })
  
  $('.select2').select2();
  $('[data-mask]').inputmask();
  
  //LAE API SUNAT / RENIEC
  $( '#btn-cloud-api' ).click(function(){
    if ( $( '#cbo-TiposDocumentoIdentidad' ).val().length === 0){
      $( '#cbo-TiposDocumentoIdentidad' ).closest('.form-group').find('.help-block').html('Seleccionar tipo doc. identidad');
  	  $( '#cbo-TiposDocumentoIdentidad' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ($( '#cbo-TiposDocumentoIdentidad' ).find(':selected').data('nu_cantidad_caracteres') != $( '#txt-Nu_Documento_Identidad').val().length ) {
      $( '#txt-Nu_Documento_Identidad' ).closest('.form-group').find('.help-block').html('Debe ingresar ' + $( '#cbo-TiposDocumentoIdentidad' ).find(':selected').data('nu_cantidad_caracteres') + ' dígitos' );
  	  $( '#txt-Nu_Documento_Identidad' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( 
        (
          $( '#cbo-TiposDocumentoIdentidad' ).val() == 1 ||
          $( '#cbo-TiposDocumentoIdentidad' ).val() == 3 ||
          $( '#cbo-TiposDocumentoIdentidad' ).val() == 5 ||
          $( '#cbo-TiposDocumentoIdentidad' ).val() == 6
        )
        ) {
      $( '#cbo-TiposDocumentoIdentidad' ).closest('.form-group').find('.help-block').html('Disponible DNI / RUC');
  	  $( '#cbo-TiposDocumentoIdentidad' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      $( '#btn-cloud-api' ).text('');
      $( '#btn-cloud-api' ).attr('disabled', true);
      $( '#btn-cloud-api' ).append( '<i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
      
      // Obtener datos de SUNAT y RENIEC
      var url_api = 'https://www.laesystems.com/librerias/sunat/partner/format/json/x-api-key/';
			if ( $( '#cbo-TiposDocumentoIdentidad' ).val() == 2 )//2=RENIEC, API SUNAT
				url_api = 'https://www.laesystems.com/librerias/reniec/partner/format/json/x-api-key/';
			url_api = url_api + sTokenGlobal;
			
      var data = {
        ID_Tipo_Documento_Identidad : $( '#cbo-TiposDocumentoIdentidad' ).val(),
        Nu_Documento_Identidad : $( '#txt-Nu_Documento_Identidad' ).val(),
      };
      
      $.ajax({
        url   : url_api,
        type  :'POST',
        data  : data,
        success: function(response){
          $( '#btn-cloud-api' ).closest('.form-group').find('.help-block').html('');
      	  $( '#btn-cloud-api' ).closest('.form-group').removeClass('has-success').addClass('has-error');
      	  
          if (response.success == true){
            $('[name="No_Entidad"]').val( response.data.No_Names );
            if ( $( '#cbo-TiposDocumentoIdentidad' ).val() == 4) {//RUC
              if (response.data.Txt_Address != null)
                $('[name="Txt_Direccion_Entidad"]').val(response.data.Txt_Address);
              if (response.data.Nu_Phone != null )
                $('[name="Nu_Telefono_Entidad"]').val(response.data.Nu_Phone);
              if (response.data.Nu_Cellphone != null)
                $('[name="Nu_Telefono_Entidad"]').val(response.data.Nu_Cellphone);
              
              if ( response.data.Nu_Status == 1)
                $("div.estado select").val("1");
              else {
                $("div.estado select").val("0");

                $( '#modal-message' ).modal('show');
                $( '.modal-message' ).addClass('modal-danger');
                $( '.modal-title-message' ).text( 'El cliente se encuentra con BAJA DE OFICIO / NO HABIDO' );
                setTimeout(function() {$('#modal-message').modal('hide');}, 2500);
              }
            }
          } else {
            $('[name="No_Entidad"]').val( '' );
            if ( $( '#cbo-TiposDocumentoIdentidad' ).val() == 4) {//RUC
              $('[name="Txt_Direccion_Entidad"]').val( '' );
              $('[name="Nu_Telefono_Entidad"]').val( '' );
              $('[name="Nu_Celular_Entidad"]').val( '' );
            }
            
            $( '#txt-Nu_Documento_Identidad' ).closest('.form-group').find('.help-block').html( response.msg );
  	        $( '#txt-Nu_Documento_Identidad' ).closest('.form-group').removeClass('has-success').addClass('has-error');
          
  		  	  $( '#txt-Nu_Documento_Identidad' ).focus();
  		  	  $( '#txt-Nu_Documento_Identidad' ).select();
          }
  		  	
          $( '#btn-cloud-api' ).text('');
          $( '#btn-cloud-api' ).attr('disabled', false);
          $( '#btn-cloud-api' ).append( '<i class="fa fa-cloud-download fa-lg"></i>' );
        },
        error: function(response){
          $( '#btn-cloud-api' ).closest('.form-group').find('.help-block').html('Sin acceso');
      	  $( '#btn-cloud-api' ).closest('.form-group').removeClass('has-success').addClass('has-error');
      	  
          $( '[name="No_Entidad"]' ).val( '' );
          $( '[name="Txt_Direccion_Entidad"]' ).val( '' );
          $( '[name="Nu_Telefono_Entidad"]' ).val( '' );
          $( '[name="Nu_Celular_Entidad"]' ).val( '' );
              
          $( '#btn-cloud-api' ).text('');
          $( '#btn-cloud-api' ).attr('disabled', false);
          $( '#btn-cloud-api' ).append( '<i class="fa fa-cloud-download fa-lg"></i>' );
        }
      });// /. SUNAT y RENIEC
    }
  })
  
  url = base_url + 'Ventas/ReglasVenta/ClienteController/ajax_list';
  table_Entidad = $( '#table-Cliente' ).DataTable({
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
    //'bStateSave'  : true,
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
      'dataType'  : 'JSON',
      'data'      : function ( data ) {
        data.Filtros_Entidades = $( '#cbo-Filtros_Entidades' ).val(),
        data.Global_Filter = $( '#txt-Global_Filter' ).val();
      },
    },
    'columnDefs': [
      {
        'targets': 'no-hidden',
        "visible": false, 
      },{
      'className' : 'text-center',
      'targets'   : 'no-sort',
      'orderable' : false,
    },],
    'lengthMenu': [[10, 100, 1000, -1], [10, 100, 1000, "Todos"]],
  });
  
  $( '.dataTables_length' ).addClass('col-md-3');
  $( '.dataTables_paginate' ).addClass('col-md-9');
  
  $( '#txt-Global_Filter' ).keyup(function() {
    table_Entidad.search($(this).val()).draw();
  });
  
  $( "#form-Cliente" ).validate({
		rules:{
			ID_Tipo_Documento_Identidad: {
				required: true,
			},
			No_Entidad: {
				required: true,
				maxlength: 100
			},
			Nu_Telefono_Entidad: {
				minlength: 8,
				maxlength: 8
			},
			Nu_Celular_Entidad: {
				minlength: 11,
				maxlength: 11
			},
			Txt_Email_Entidad:{
				validemail: true,
			},
			Nu_Celular_Contacto: {
				minlength: 11,
				maxlength: 11
			},
			Txt_Email_Contacto:{
				validemail: true,
			},
		},
		messages:{
			ID_Tipo_Documento_Identidad:{
				required: "Seleccionar tipo doc.",
			},
			No_Entidad:{
				required: "Ingresar nombre",
				maxlength: "Máximo 100 dígitos"
			},
			Nu_Telefono_Entidad:{
				minlength: "Debe ingresar 7 dígitos",
				maxlength: "Debe ingresar 7 dígitos"
			},
			Nu_Celular_Entidad:{
				minlength: "Debe ingresar 9 dígitos",
				maxlength: "Debe ingresar 9 dígitos"
			},
			Txt_Email_Entidad:{
				validemail: "Ingresar correo válido",
			},
			Nu_Celular_Contacto:{
				minlength: "Debe ingresar 9 dígitos",
				maxlength: "Debe ingresar 9 dígitos"
			},
			Txt_Email_Contacto:{
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
		submitHandler: form_Entidad
	});
  
	/* Tipo Documento Identidad */
	$( '#cbo-TiposDocumentoIdentidad' ).change(function(){
    $('.div-api').show();
	  if ( $(this).val() == 2 ) {//DNI
		  $( '#label-Nombre_Documento_Identidad' ).text('DNI');
		  $( '#label-No_Entidad' ).text('Nombre(s) y Apellidos');
			$( '#txt-Nu_Documento_Identidad' ).attr('maxlength', $(this).find(':selected').data('nu_cantidad_caracteres'));
	  } else if ( $(this).val() == 4 ) {//RUC
		  $( '#label-Nombre_Documento_Identidad' ).text('RUC');
		  $( '#label-No_Entidad' ).text('Razón Social');
			$( '#txt-Nu_Documento_Identidad' ).attr('maxlength', $(this).find(':selected').data('nu_cantidad_caracteres'));
	  } else {
      $('.div-api').hide();
	    $( '#label-Nombre_Documento_Identidad' ).text('DOCUMENTO');
		  $( '#label-No_Entidad' ).text('Nombre(s) y Apellidos');
			$( '#txt-Nu_Documento_Identidad' ).attr('maxlength', $(this).find(':selected').data('nu_cantidad_caracteres'));
	  }
	})
		
	$( '#cbo-Departamentos' ).change(function(){
    $('#cbo-Provincias').html('');
    $('#cbo-Distritos').html('');
	  if ( $(this).val() > 0 ) {
      url = base_url + 'HelperController/getProvincias';
      $.post( url, {ID_Departamento : $(this).val()}, function( response ){
        $( '#cbo-Provincias' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
        for (var i = 0; i < response.length; i++)
          $( '#cbo-Provincias' ).append( '<option value="' + response[i].ID_Provincia + '">' + response[i].No_Provincia + '</option>' );
      }, 'JSON');
	  }
	})
	
	$( '#cbo-Provincias' ).change(function(){
	  $( '#cbo-Distritos' ).html('');
	  if ( $(this).val() > 0 ) {
      url = base_url + 'HelperController/getDistritos';
      $.post( url, {ID_Provincia : $(this).val()}, function( response ){
        $( '#cbo-Distritos' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
        for (var i = 0; i < response.length; i++)
          $( '#cbo-Distritos' ).append( '<option value="' + response[i].ID_Distrito + '">' + response[i].No_Distrito + '</option>' );
      }, 'JSON');
	  }
	})
	
	$( '.div-TiposCliente' ).hide();
	$( '#cbo-TiposCliente' ).change(function(){
	  $( '.div-TiposCliente' ).hide();
	  if ( $(this).val() == 1) //Si es Crédito
	    $( '.div-TiposCliente' ).show();
	})
	
  $(document).bind('keydown', 'f2', function(){
    agregarCliente();
  });
})

function agregarCliente(){
  accion_cliente = 'add_cliente';
  $( '#form-Cliente' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '.div-Listar' ).hide();
  $( '.div-AgregarEditar' ).show();
		
  $( '.title_Entidad' ).text('Nuevo Cliente');

  $( '[name="EID_Empresa"]' ).val('');
  $( '[name="EID_Entidad"]' ).val('');
  $( '[name="ENu_Documento_Identidad"]' ).val('');

  $('.div-mas_opciones').hide();
  $('#checkbox-mas_filtros').prop('checked', false).iCheck('update');
  $('.div-api').show();

  $('#cbo-tipo_cliente_1').html('<option value="0" selected="selected">- Sin registros -</option>');
  url = base_url + 'HelperController/getValoresTablaDato';
  $.post(url, { 'sTipoData': 'ID_Tipo_Cliente_1' }, function (response) {
    $('#cbo-tipo_cliente_1').html('<option value="0" selected="selected">- Seleccionar -</option>');
    if (response.sStatus == 'success') {
      var l = response.arrData.length;
      for (var x = 0; x < l; x++) {
        $('#cbo-tipo_cliente_1').append('<option value="' + response.arrData[x].ID_Tabla_Dato + '">' + response.arrData[x].No_Descripcion + '</option>');
      }
    } else {
      if (response.sMessageSQL !== undefined) {
        console.log(response.sMessageSQL);
      }
      console.log(response.sMessage);
    }
  }, 'JSON');

  url = base_url + 'HelperController/getTiposDocumentoIdentidad';
  $.post( url , function( response ){
    $( '#cbo-TiposDocumentoIdentidad' ).html('');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-TiposDocumentoIdentidad' ).append( '<option value="' + response[i]['ID_Tipo_Documento_Identidad'] + '" data-nu_cantidad_caracteres="' + response[i]['Nu_Cantidad_Caracteres'] + '">' + response[i]['No_Tipo_Documento_Identidad_Breve'] + '</option>' );
  }, 'JSON');
  
  $( '#cbo-Paises' ).html('<option value="1" selected="selected">- Peru -</option>');

  // Departamento - Provincia - Distrito
  $('#cbo-Departamentos').html('<option value="0" selected="selected">- Seleccionar -</option>');
  $('#cbo-Provincias').html('<option value="0" selected="selected"></option>');
  $('#cbo-Distritos').html('<option value="0" selected="selected"></option>');

  url = base_url + 'HelperController/getDepartamentos';
  $.post(url, { ID_Pais: 1 }, function (response) {
    $('#cbo-Departamentos').html('<option value="0" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++)
      $('#cbo-Departamentos').append('<option value="' + response[i].ID_Departamento + '">' + response[i].No_Departamento + '</option>');
  }, 'JSON');


  $( '#cbo-TiposCliente' ).html( '<option value="0">Efectivo</option>' );
  $( '#cbo-TiposCliente' ).append( '<option value="1">Crédito</option>' );
  $( '#cbo-TiposCliente' ).append( '<option value="2">Anticipo</option>' );
  
  $( '#cbo-Estado' ).html( '<option value="1">Activo</option>' );
  $( '#cbo-Estado' ).append( '<option value="0">Inactivo</option>' );
  
  /* obtener imagen guardada(s) */
  $( '.divDropzone' ).html(
  '<div id="id-divDropzone" class="dropzone div-dropzone">'
    +'<div class="dz-message">'
      +'Arrastrar o presionar click para subir imágen'
    +'</div>'
  +'</div>'
  );

  Dropzone.autoDiscover = false;
  Dropzone.prototype.defaultOptions.dictDefaultMessage = "Arrastrar o presionar click para subir imágen";
  Dropzone.prototype.defaultOptions.dictFallbackMessage = "Tu navegador no soporta la función arrastrar la imágen";
  Dropzone.prototype.defaultOptions.dictFileTooBig = "La imágen pesa ({{filesize}}MiB). El tamaño máximo es: {{maxFilesize}}MiB.";
  Dropzone.prototype.defaultOptions.dictInvalidFileType = "Solo se permite imágenes PNG";
  Dropzone.prototype.defaultOptions.dictCancelUpload = "Cancelar";
  Dropzone.prototype.defaultOptions.dictCancelUploadConfirmation = "¿Estás seguro de cancelar la subida?";
  Dropzone.prototype.defaultOptions.dictRemoveFile = "Eliminar";
  Dropzone.prototype.defaultOptions.dictMaxFilesExceeded = "Solo se puede subir 1 imágen";
  
  url = base_url + 'Ventas/ReglasVenta/ClienteController/uploadOnly';
  var myDropzone = new Dropzone("#id-divDropzone", {
    url : url,
    acceptedFiles: "image/png",
    addRemoveLinks: true,
    uploadMultiple: false,
    maxFilesize: 1,//Peso en MB
    maxFiles: 1,
    removedfile: function(file){
      var nameFileImage = file.name;
      url = base_url + 'Ventas/ReglasVenta/ClienteController/removeFileImage';
      $.ajax({
        url : url,
        type: "POST",
        dataType: "JSON",
        data: {nameFileImage : nameFileImage},
      })
      var previewElement;
      return (previewElement = file.previewElement) != null ? (previewElement.parentNode.removeChild(file.previewElement)) : (void 0);
    },
  })
}

function verCliente(ID, Nu_Documento_Identidad){
  accion_cliente = 'upd_cliente';
  $( '#modal-loader' ).modal('show');
  
  $( '.div-Listar' ).hide();
  
  $( '.div-TiposCliente' ).hide();

  $( '#form-Cliente' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();

  $('#cbo-Paises').html('<option value="1" selected="selected">- Peru -</option>');

  $('.div-mas_opciones').hide();
  $('#checkbox-mas_filtros').prop('checked', false).iCheck('update');

  url = base_url + 'Ventas/ReglasVenta/ClienteController/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $( '.div-AgregarEditar' ).show();
      
      $( '.title_Entidad' ).text('Modifcar Cliente');
      
      $( '[name="EID_Empresa"]' ).val(response.ID_Empresa);
      $( '[name="EID_Entidad"]' ).val(response.ID_Entidad);
      $( '[name="ENu_Documento_Identidad"]' ).val(response.Nu_Documento_Identidad);
      $( '[name="ENo_Entidad"]' ).val(response.No_Entidad);
      
      var selected;
      url = base_url + 'HelperController/getTiposDocumentoIdentidad';
      $.post( url , function( responseTiposDocumentoIdentidad ){
        $( '#cbo-TiposDocumentoIdentidad' ).html( '' );
        for (var i = 0; i < responseTiposDocumentoIdentidad.length; i++){
          selected = '';
          if(response.ID_Tipo_Documento_Identidad == responseTiposDocumentoIdentidad[i]['ID_Tipo_Documento_Identidad'])
            selected = 'selected="selected"';
          $( '#cbo-TiposDocumentoIdentidad' ).append( '<option value="' + responseTiposDocumentoIdentidad[i]['ID_Tipo_Documento_Identidad'] + '" data-nu_cantidad_caracteres="' + responseTiposDocumentoIdentidad[i]['Nu_Cantidad_Caracteres'] + '" ' + selected + '>' + responseTiposDocumentoIdentidad[i]['No_Tipo_Documento_Identidad_Breve'] + '</option>' );
        }
        
        if ( response.ID_Tipo_Documento_Identidad == 2 ) {//DNI
          $( '#label-Nombre_Documento_Identidad' ).text('DNI');
          $( '#label-No_Entidad' ).text('Nombre(s) y Apellidos');
          $( '#txt-Nu_Documento_Identidad' ).attr('maxlength', $( '#cbo-TiposDocumentoIdentidad' ).find(':selected').data('nu_cantidad_caracteres'));
        } else if ( response.ID_Tipo_Documento_Identidad == 4 ) {//RUC
          $( '#label-Nombre_Documento_Identidad' ).text('RUC');
          $( '#label-No_Entidad' ).text('Razón Social');
          $( '#txt-Nu_Documento_Identidad' ).attr('maxlength', $( '#cbo-TiposDocumentoIdentidad' ).find(':selected').data('nu_cantidad_caracteres'));
        } else {
          $('.div-api').hide();
          $( '#label-Nombre_Documento_Identidad' ).text('# Documento Identidad');
          $( '#label-No_Entidad' ).text('Nombre(s) y Apellidos');
          $( '#txt-Nu_Documento_Identidad' ).attr('maxlength', $( '#cbo-TiposDocumentoIdentidad' ).find(':selected').data('nu_cantidad_caracteres'));
        }
      }, 'JSON');

      $('.div-api').show();

      $( '[name="Nu_Documento_Identidad"]' ).val(response.Nu_Documento_Identidad);
      $( '[name="No_Entidad"]' ).val(response.No_Entidad);
      
      $( '[name="Nu_Dias_Credito"]' ).val(response.Nu_Dias_Credito);
      $( '[name="Nu_Telefono_Entidad"]' ).val(response.Nu_Telefono_Entidad);
      $( '[name="Nu_Celular_Entidad"]' ).val(response.Nu_Celular_Entidad);
      $( '[name="Txt_Email_Entidad"]' ).val(response.Txt_Email_Entidad);
            
      url = base_url + 'HelperController/getDepartamentos';
      $.post( url, {ID_Pais : 1}, function( responseDepartamentos ){
        $( '#cbo-Departamentos' ).html('');
        for (var i = 0; i < responseDepartamentos.length; i++){
          selected = '';
          if(response.ID_Departamento == responseDepartamentos[i].ID_Departamento)
            selected = 'selected="selected"';
          $( '#cbo-Departamentos' ).append( '<option value="' + responseDepartamentos[i].ID_Departamento + '" ' + selected + '>' + responseDepartamentos[i].No_Departamento + '</option>' );
        }
      }, 'JSON');
      
      url = base_url + 'HelperController/getProvincias';
      $.post( url, {ID_Departamento : response.ID_Departamento}, function( responseProvincia ){
        $( '#cbo-Provincias' ).html('');
        for (var i = 0; i < responseProvincia.length; i++){
          selected = '';
          if(response.ID_Provincia == responseProvincia[i].ID_Provincia)
            selected = 'selected="selected"';
          $( '#cbo-Provincias' ).append( '<option value="' + responseProvincia[i].ID_Provincia + '" ' + selected + '>' + responseProvincia[i].No_Provincia + '</option>' );
        }
      }, 'JSON');
      
      url = base_url + 'HelperController/getDistritos';
      $.post( url, {ID_Provincia : response.ID_Provincia}, function( responseDistrito ){
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

      $('#cbo-tipo_cliente_1').html('<option value="0" selected="selected">- Sin registros -</option>');
      url = base_url + 'HelperController/getValoresTablaDato';
      $.post(url, { 'sTipoData': 'ID_Tipo_Cliente_1'}, function (responseTipoCliente1) {
        $('#cbo-tipo_cliente_1').html('<option value="0" selected="selected">- Seleccionar -</option>');
        if (responseTipoCliente1.sStatus == 'success') {
          var l = responseTipoCliente1.arrData.length;
          for (var x = 0; x < l; x++) {
            selected = '';
            if (response.ID_Tipo_Cliente_1 == responseTipoCliente1.arrData[x].ID_Tabla_Dato)
              selected = 'selected="selected"';
            $('#cbo-tipo_cliente_1').append('<option value="' + responseTipoCliente1.arrData[x].ID_Tabla_Dato + '" ' + selected + '>' + responseTipoCliente1.arrData[x].No_Descripcion + '</option>');
          }
        } else {
          if (responseTipoCliente1.sMessageSQL !== undefined) {
            console.log(responseTipoCliente1.sMessageSQL);
          }
          console.log(responseTipoCliente1.sMessage);
        }
      }, 'JSON');

      $( '#cbo-Estado' ).html( '' );
      for (var i = 0; i < 2; i++){
        selected = '';
        if(response.Nu_Estado == i)
          selected = 'selected="selected"';
        $( '#cbo-Estado' ).append( '<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Inactivo' : 'Activo') + '</option>' );
      }
      
      $('[name="No_Contacto"]').val(response.No_Contacto); 
      
      if (response.Fe_Nacimiento!==null)
        $('[name="Fe_Nacimiento"]').val(ParseDateString(response.Fe_Nacimiento, 6, '-'));

      $( '[name="Nu_Celular_Contacto"]' ).val(response.Nu_Celular_Contacto);
      $( '[name="Txt_Email_Contacto"]' ).val(response.Txt_Email_Contacto);
      
      $( '[name="Txt_Descripcion"]' ).val(response.Txt_Descripcion);
      
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
  })
  
  /* obtener imagen guardada(s) */
  $( '.divDropzone' ).html(
  '<div id="id-divDropzone" class="dropzone div-dropzone">'
    +'<div class="dz-message">'
      +'Arrastrar o presionar click para subir imágen'
    +'</div>'
  +'</div>'
  );

  Dropzone.autoDiscover = false;
  Dropzone.prototype.defaultOptions.dictDefaultMessage = "Arrastrar o presionar click para subir imágen";
  Dropzone.prototype.defaultOptions.dictFallbackMessage = "Tu navegador no soporta la función arrastrar la imágen";
  Dropzone.prototype.defaultOptions.dictFileTooBig = "La imágen pesa ({{filesize}}MiB). El tamaño máximo es: {{maxFilesize}}MiB.";
  Dropzone.prototype.defaultOptions.dictInvalidFileType = "Solo se permite imágenes PNG";
  Dropzone.prototype.defaultOptions.dictCancelUpload = "Cancelar";
  Dropzone.prototype.defaultOptions.dictCancelUploadConfirmation = "¿Estás seguro de cancelar la subida?";
  Dropzone.prototype.defaultOptions.dictRemoveFile = "Eliminar";
  Dropzone.prototype.defaultOptions.dictMaxFilesExceeded = "Solo se puede subir 1 imágen";
  
  url = base_url + 'Ventas/ReglasVenta/ClienteController/uploadOnly';
  var myDropzone = new Dropzone("#id-divDropzone", {
    url : url,
    acceptedFiles: "image/png",
    addRemoveLinks: true,
    uploadMultiple: false,
    maxFilesize: 1,//Peso en MB
    maxFiles: 1,
    removedfile: function(file){
      var arrName = file.name.split('/');
      var nameFileImage;
      if (arrName.length === 5)//Si la imagen ya está en el server
        nameFileImage = arrName[4];
      else//Si la imagén recién la vamos a subir y no existe en el server
        nameFileImage = arrName[0];
      url = base_url + 'Ventas/ReglasVenta/ClienteController/removeFileImage';
      $.ajax({
        url : url,
        type: "POST",
        dataType: "JSON",
        data: {nameFileImage : nameFileImage},
      })
      var previewElement;
      return (previewElement = file.previewElement) != null ? (previewElement.parentNode.removeChild(file.previewElement)) : (void 0);
    },
    init : function() {
      var me = this;
      url = base_url + 'Ventas/ReglasVenta/ClienteController/get_image/' + Nu_Documento_Identidad;
      $.getJSON(url,
      function(response){
        var mockfile = response;
        me.emit("addedfile", mockfile);
        me.emit("thumbnail", mockfile, base_url + response.name);
        me.emit("complete", mockfile);
      }, 'JSON');
    }
  })
}

function form_Entidad(){
  if (accion_cliente == 'add_cliente' || accion_cliente == 'upd_cliente') {
    if ( ($( '#cbo-TiposDocumentoIdentidad' ).val() != 2 && $('#cbo-TiposDocumentoIdentidad' ).val() != 1) && $( '#txt-Nu_Documento_Identidad' ).val().length == 0){
  		$( '#txt-Nu_Documento_Identidad' ).closest('.form-group').find('.help-block').html('Ingresar datos');
  		$( '#txt-Nu_Documento_Identidad' ).closest('.form-group').removeClass('has-success').addClass('has-error');
  	} else if ( $( '#cbo-TiposDocumentoIdentidad' ).val() != 2 && $('[name="No_Entidad"]').val().length == 0 ){
  		$( '#txt-Nu_Documento_Identidad' ).closest('.form-group').find('.help-block').html('Ingresar nombre(s)');
  		$( '#txt-Nu_Documento_Identidad' ).closest('.form-group').removeClass('has-success').addClass('has-error');
  	} else if ( $( '#cbo-TiposDocumentoIdentidad' ).val() != 1 && $( '#cbo-TiposDocumentoIdentidad' ).val() != 2 && ($( '#cbo-TiposDocumentoIdentidad' ).find(':selected').data('nu_cantidad_caracteres') != $( '#txt-Nu_Documento_Identidad').val().length) ) {
      $( '#txt-Nu_Documento_Identidad' ).closest('.form-group').find('.help-block').html('Debe ingresar ' + $( '#cbo-TiposDocumentoIdentidad' ).find(':selected').data('nu_cantidad_caracteres') + ' dígitos' );
  	  $( '#txt-Nu_Documento_Identidad' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      $( '#btn-save' ).text('');
      $( '#btn-save' ).attr('disabled', true);
      $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
    
      $( '#modal-loader' ).modal('show');
      
      url = base_url + 'Ventas/ReglasVenta/ClienteController/crudCliente';
    	$.ajax({
        type		  : 'POST',
        dataType	: 'JSON',
    		url		    : url,
    		data		  : $('#form-Cliente').serialize(),
    		success : function( response ){
    		  $( '#modal-loader' ).modal('hide');
    		  
    	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
      	  $( '#modal-message' ).modal('show');
    		  
    		  if (response.status == 'success'){
            accion_cliente = '';
            
            $( '#form-Cliente' )[0].reset();
            
            $( '.div-AgregarEditar' ).hide();
            $( '.div-Listar' ).show();
      	    $( '.modal-message' ).addClass(response.style_modal);
      	    $( '.modal-title-message' ).text(response.message);
      	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
      	    reload_table_Entidad();
    		  } else {
      	    $( '.modal-message' ).addClass(response.style_modal);
      	    $( '.modal-title-message' ).text(response.message);
      	    setTimeout(function() {$('#modal-message').modal('hide');}, 1200);
    		  }
    		  
          $( '#btn-save' ).text('');
          $( '#btn-save' ).append( 'Guardar' );
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
          $( '#btn-save' ).append( 'Guardar' );
          $( '#btn-save' ).attr('disabled', false);
        }
    	});
    }
  }
}

function eliminarCliente(ID_Empresa, ID, Nu_Documento_Identidad, accion_cliente){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $(document).bind('keydown', 'esc', function(){
    if ( accion_cliente=='delete' ) {
      _eliminarCliente($modal_delete, ID_Empresa, ID, Nu_Documento_Identidad);
      accion_cliente='';
    }
  });

  $( '#btn-save-delete' ).off('click').click(function () {
    _eliminarCliente($modal_delete, ID_Empresa, ID, Nu_Documento_Identidad);
  });
}

function reload_table_Entidad(){
  table_Entidad.ajax.reload(null,false);
}

function _eliminarCliente($modal_delete, ID_Empresa, ID, Nu_Documento_Identidad){
  $( '#modal-loader' ).modal('show');
    
  url = base_url + 'Ventas/ReglasVenta/ClienteController/eliminarCliente/' + ID_Empresa + '/' + ID + '/' + Nu_Documento_Identidad;
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
  	    reload_table_Entidad();
		  } else {
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
		  }
		  accion_cliente = '';
    },
    error: function (jqXHR, textStatus, errorThrown) {
		  accion_cliente = '';
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