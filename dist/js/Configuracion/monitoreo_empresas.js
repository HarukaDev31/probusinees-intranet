var url;
var table_empresa;

$(function () {
  $('.select2').select2();
  $('[data-mask]').inputmask();

  $(".toggle-password").click(function () {
    $(this).toggleClass("fa-eye fa-eye-slash");
    var $pwd = $(".pwd");
    if ($pwd.attr('type') == 'password') {
      $pwd.attr('type', 'text');
    } else {
      $pwd.attr('type', 'password');
    }
  });
  
  //LAE API SUNAT
  $('#btn-cloud-api_empresa').click(function () {
    if ($('#cbo-TiposDocumentoIdentidad').val().length === 0) {
      $('#cbo-TiposDocumentoIdentidad').closest('.form-group').find('.help-block').html('Seleccionar tipo doc. identidad');
      $('#cbo-TiposDocumentoIdentidad').closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ($('#cbo-TiposDocumentoIdentidad').find(':selected').data('nu_cantidad_caracteres') != $('#txt-Nu_Documento_Identidad').val().length) {
      $('#txt-Nu_Documento_Identidad').closest('.form-group').find('.help-block').html('Debe ingresar ' + $('#cbo-TiposDocumentoIdentidad').find(':selected').data('nu_cantidad_caracteres') + ' dígitos');
      $('#txt-Nu_Documento_Identidad').closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if (
      (
        $('#cbo-TiposDocumentoIdentidad').val() == 1 ||
        $('#cbo-TiposDocumentoIdentidad').val() == 3 ||
        $('#cbo-TiposDocumentoIdentidad').val() == 5 ||
        $('#cbo-TiposDocumentoIdentidad').val() == 6
      )
    ) {
      $('#cbo-TiposDocumentoIdentidad').closest('.form-group').find('.help-block').html('Disponible DNI / RUC');
      $('#cbo-TiposDocumentoIdentidad').closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      $( '#btn-cloud-api_empresa' ).text('');
      $( '#btn-cloud-api_empresa' ).attr('disabled', true);
      $( '#btn-cloud-api_empresa' ).append( '<i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
      
      //PRUEBAS LOCAL
      var url_api = 'https://www.ecxpresslae.com/librerias/sunat/partner/format/json/x-api-key/' + sTokenGlobal;
      
      var data = {
        ID_Tipo_Documento_Identidad: $('#cbo-TiposDocumentoIdentidad').val(),
        Nu_Documento_Identidad : $( '#txt-Nu_Documento_Identidad' ).val(),
      };
      
      $.ajax({
        url   : url_api,
        type  :'POST',
        data  : data,
        success: function(response){
          if (response.success === true){
            $('[name="No_Empresa"]').val( response.data.No_Names );
            $('[name="Txt_Direccion_Empresa"]').val( response.data.Txt_Address );
            if ( response.data.Nu_Status == '1')
              $("div.estado select").val("1");
            else if ( $('#cbo-tipo_proveedor_fe').val() != 3 )
              $("div.estado select").val("0");
          } else {
            if ($('#cbo-tipo_proveedor_fe').val() != 3) {
              $('[name="No_Empresa"]').val( '' );
              $('[name="Txt_Direccion_Empresa"]').val( '' );
              
              $( '#txt-Nu_Documento_Identidad' ).closest('.form-group').find('.help-block').html(response.msg);
              $( '#txt-Nu_Documento_Identidad' ).closest('.form-group').removeClass('has-success').addClass('has-error');
            }
          }
          
  		    $( '#txt-Nu_Documento_Identidad' ).focus();
  		    
          $( '#btn-cloud-api_empresa' ).text('');
          $( '#btn-cloud-api_empresa' ).attr('disabled', false);
          $( '#btn-cloud-api_empresa' ).append( '<i class="fa fa-cloud-download fa-lg"></i>' );
        },
        error: function(response){
          console.log(response);
        }
      });
    }
  })
  
  $( '#cbo-filtro-pais' ).html('<option value="0" selected="selected">- Todas -</option>');

  url = base_url + 'Configuracion/MonitoreoEmpresasController/ajax_list';
  table_empresa = $('#table-Empresa').DataTable({
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
    'order'       : [],
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
    'ajax': {
      'url'       : url,
      'type'      : 'POST',
      'dataType'  : 'JSON',
      'data': function (data) {
        data.Filtro_Pais = $('#cbo-filtro-pais').val(),
        data.Filtro_Tipo_Sistema = $('#cbo-filtro-tipo_sistema').val(),
        data.Filtro_Estado = $('#cbo-filtro-estado').val(),
        data.estado_proveedor = $('#cbo-filtro-estado_proveedor').val(),
        data.filtro_estado_laegestion = $('#cbo-filtro-estado_laegestion').val(),
        data.filtro_estado_laeshop = $('#cbo-filtro-estado_laeshop').val(),
        data.filtro_guia = $('#cbo-filtro-guia').val(),
        data.Filtros_Empresas = $( '#cbo-Filtros_Empresas' ).val(),
        data.Global_Filter = $( '#txt-Global_Filter' ).val();
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
  
  $( '#txt-Global_Filter' ).keyup(function() {
    table_empresa.search($(this).val()).draw();
  });

  $('#cbo-filtro-tipo_sistema').change(function () {
    table_empresa.search($(this).val()).draw();
  });

  $('#cbo-filtro-estado').change(function () {
    table_empresa.search($(this).val()).draw();
  });

  $('#cbo-filtro-estado_proveedor').change(function () {
    table_empresa.search($(this).val()).draw();
  });

  $( '#cbo-filtro-estado_laegestion' ).change(function(){
    table_empresa.search($(this).val()).draw();
  });
  
  $( '#cbo-filtro-estado_laeshop' ).change(function(){
    table_empresa.search($(this).val()).draw();
  });
  
  $( '#cbo-filtro-guia' ).change(function(){
    table_empresa.search($(this).val()).draw();
  });
  
  $( '#cbo-filtro-pais' ).change(function(){
    table_empresa.search($(this).val()).draw();
  });

  $( '#form-Empresa' ).validate({
		rules:{
			No_Empresa: {
				required: true,
			},
			Txt_Direccion_Empresa: {
				required: true,
			},
			Txt_Usuario_Sunat_Sol: {
				required: true,
			},
			Txt_Password_Sunat_Sol: {
				required: true,
			},
			Txt_Password_Firma_Digital: {
				required: true,
			},
		},
		messages:{
			No_Empresa:{
				required: "Ingresar razón social"
			},
			Txt_Direccion_Empresa:{
				required: "Ingresar dirección"
			},
			Txt_Usuario_Sunat_Sol:{
				required: "Ingresar usuario SOL"
			},
			Txt_Password_Sunat_Sol:{
				required: "Ingresar contraseña SOL"
			},
			Txt_Password_Firma_Digital:{
				required: "Ingresar contraseña firma"
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
		submitHandler: form_Empresa
  });
  
  url = base_url + 'HelperDropshippingController/listarTodosPaises';
  var selected = '';
  $.post(url, function (response) {
    $('#cbo-filtro-pais').html('<option value="0" selected="selected">- TODOS -</option>');
    for (var i = 0; i < response.length; i++) {
      $('#cbo-filtro-pais').append('<option value="' + response[i].ID_Pais + '">' + response[i].No_Pais + '</option>');
    }
  }, 'JSON');

  $('#cbo-tipo_proveedor_fe').change(function () {
    $('#cbo-tipo_ecommerce_empresa').val('2043');

    $( '.div-row-nubefact' ).show();
    if ( $(this).val() == 1 || $(this).val() == 3 ) {
      $( '.div-row-nubefact' ).hide();
    }

    if ($(this).val() == 1 || $(this).val() == 2) {
      $('#cbo-TiposDocumentoIdentidad').val('4');
    }
  });

  $('#cbo-Activar_Guia_Electronica').change(function () {
    $( '.div-guia_sunat' ).hide();
    if ( $(this).val() == 1 ) {
      $( '.div-guia_sunat' ).show();
    }
  });

  /* Tipo Documento Identidad */
  $('#cbo-TiposDocumentoIdentidad').change(function () {
    $('[name="Nu_Documento_Identidad"]').val('');
    $('[name="No_Empresa"]').val('');
    $('[name="Txt_Direccion_Empresa"]').val('');
    
    if ($(this).val() == 2) {//DNI
      $('#label-Nombre_Documento_Identidad').text('DNI');
      $('#label-No_Entidad').text('Nombre(s) y Apellidos');
      $('#txt-Nu_Documento_Identidad').attr('maxlength', $(this).find(':selected').data('nu_cantidad_caracteres'));
    } else if ($(this).val() == 4) {//RUC
      $('#label-Nombre_Documento_Identidad').text('RUC');
      $('#label-No_Entidad').text('Razón Social');
      $('#txt-Nu_Documento_Identidad').attr('maxlength', $(this).find(':selected').data('nu_cantidad_caracteres'));
    } else {
      $('#label-Nombre_Documento_Identidad').text('OTROS');
      $('#label-No_Entidad').text('Nombre(s) y Apellidos');
      $('#txt-Nu_Documento_Identidad').attr('maxlength', $(this).find(':selected').data('nu_cantidad_caracteres'));
    }
  })

  $('#form-Empresa').submit(function (e) {
    $('.help-block').empty();
    $('.form-group').removeClass('has-error');
    if ($('#cbo-TiposDocumentoIdentidad').val().length === 0) {
      $('#cbo-TiposDocumentoIdentidad').closest('.form-group').find('.help-block').html('Seleccionar T.D.I.');
      $('#cbo-TiposDocumentoIdentidad').closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ($('#cbo-TiposDocumentoIdentidad').val() != 1 && ($('#cbo-TiposDocumentoIdentidad').find(':selected').data('nu_cantidad_caracteres') != $('#txt-Nu_Documento_Identidad').val().length)) {
      $('#txt-Nu_Documento_Identidad').closest('.form-group').find('.help-block').html('Debe ingresar ' + $('#cbo-TiposDocumentoIdentidad').find(':selected').data('nu_cantidad_caracteres') + ' dígitos');
      $('#txt-Nu_Documento_Identidad').closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ($('#cbo-tipo_proveedor_fe').val() != 3 && $('#cbo-TiposDocumentoIdentidad').val() != 4) {
      $('#cbo-TiposDocumentoIdentidad').closest('.form-group').find('.help-block').html('Deben elegir RUC, cuando es SUNAT O PSE N');
      $('#cbo-TiposDocumentoIdentidad').closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $( '[name="No_Empresa"]' ).val().length === 0){
      $( '[name="No_Empresa"]' ).closest('.form-group').find('.help-block').html('Ingresar Nombres o Razón Social');
      $( '[name="No_Empresa"]' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $('#cbo-tipo_proveedor_fe').val() != 3 && $( '[name="Txt_Direccion_Empresa"]' ).val().length === 0){
      $( '[name="Txt_Direccion_Empresa"]' ).closest('.form-group').find('.help-block').html('Ingresar dirección');
      $( '[name="Txt_Direccion_Empresa"]' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $( '#cbo-tipo_proveedor_fe' ).val() == 2 && $( '#cbo-ubigeo_inei' ).val() == ''){
      $( '#cbo-ubigeo_inei' ).closest('.form-group').find('.help-block').html('Seleccionar ubigeo inei');
      $( '#cbo-ubigeo_inei' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $( '#cbo-tipo_proveedor_fe' ).val() == 2 && $( '#cbo-Paises' ).val() == ''){
      $( '#cbo-Paises' ).closest('.form-group').find('.help-block').html('Seleccionar país');
      $( '#cbo-Paises' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $( '#cbo-tipo_proveedor_fe' ).val() == 2 && $( '#cbo-Departamentos' ).val() == ''){
      $( '#cbo-Departamentos' ).closest('.form-group').find('.help-block').html('Seleccionar departamento');
      $( '#cbo-Departamentos' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $( '#cbo-tipo_proveedor_fe' ).val() == 2 && $( '#cbo-Provincias' ).val() == ''){
      $( '#cbo-Provincias' ).closest('.form-group').find('.help-block').html('Seleccionar provincia');
      $( '#cbo-Provincias' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $( '#cbo-tipo_proveedor_fe' ).val() == 2 && $( '#cbo-Distritos' ).val() == ''){
      $( '#cbo-Distritos' ).closest('.form-group').find('.help-block').html('Seleccionar distrito');
      $( '#cbo-Distritos' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $( '#cbo-tipo_proveedor_fe' ).val() == 2 && $( '[name="Txt_Usuario_Sunat_Sol"]' ).val().length === 0){
      $( '[name="Txt_Usuario_Sunat_Sol"]' ).closest('.form-group').find('.help-block').html('Ingresar usuario SOL');
      $( '[name="Txt_Usuario_Sunat_Sol"]' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $( '#cbo-tipo_proveedor_fe' ).val() == 2 && $( '[name="Txt_Password_Sunat_Sol"]' ).val().length === 0){
      $( '[name="Txt_Password_Sunat_Sol"]' ).closest('.form-group').find('.help-block').html('Ingresar contraseña SOL');
      $( '[name="Txt_Password_Sunat_Sol"]' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $( '#cbo-tipo_proveedor_fe' ).val() == 2 && $( '[name="Txt_Password_Firma_Digital"]' ).val().length === 0){
      $( '[name="Txt_Password_Firma_Digital"]' ).closest('.form-group').find('.help-block').html('Ingresar contraseña certificado');
      $( '[name="Txt_Password_Firma_Digital"]' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $( '#cbo-tipo_proveedor_fe' ).val() == 2 && $( '#cbo-Activar_Guia_Electronica' ).val() == 1 && $( '[name="Txt_Sunat_Token_Guia_Client_ID"]' ).val().length === 0){
      $( '[name="Txt_Sunat_Token_Guia_Client_ID"]' ).closest('.form-group').find('.help-block').html('Ingresar token');
      $( '[name="Txt_Sunat_Token_Guia_Client_ID"]' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $( '#cbo-tipo_proveedor_fe' ).val() == 2 && $( '#cbo-Activar_Guia_Electronica' ).val() == 1 && $( '[name="Txt_Sunat_Token_Guia_Client_Secret"]' ).val().length === 0){
      $( '[name="Txt_Sunat_Token_Guia_Client_Secret"]' ).closest('.form-group').find('.help-block').html('Ingresar token');
      $( '[name="Txt_Sunat_Token_Guia_Client_Secret"]' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      e.preventDefault();

      $( '#btn-save' ).text('');
      $( '#btn-save' ).attr('disabled', true);
      $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

      $( '#modal-loader' ).modal('show');

      url = base_url + 'Configuracion/MonitoreoEmpresasController/crudEmpresa';
      $.ajax({
        type: 'POST',
        dataType: 'JSON',
        url: url,
        data: new FormData(this),
        processData:false,
        contentType:false,
        cache:false,
        async:false,
        success : function( response ){
          $( '#modal-loader' ).modal('hide');
          
          $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
          $( '.modal-title-message' ).text( '' );
          $( '#modal-message' ).modal('show');
          
          if (response.status == 'success'){
            $( '#form-Empresa' )[0].reset();
            $( '#upload-file-certificado_digital' ).text( '' );

            $('#modal-Empresa').modal('hide');
            $( '.modal-message' ).addClass(response.style_modal);
            $( '.modal-title-message' ).text(response.message);
            setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
            reload_table_empresa();
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
    } // if y else - validaciones
  });
  
	$( '#cbo-Paises' ).change(function(){
	  $( '#cbo-Departamentos' ).html('');
	  if ( $(this).val() > 0 ) {
      url = base_url + 'HelperController/getDepartamentos';
      $.post( url, {ID_Pais : $(this).val()}, function( response ){
        $( '#cbo-Departamentos' ).html('<option value="" selected="selected">- Seleccionar -</option>');
        for (var i = 0; i < response.length; i++)
          $( '#cbo-Departamentos' ).append( '<option value="' + response[i].ID_Departamento + '">' + response[i].No_Departamento + '</option>' );
      }, 'JSON');
	  }
	})
	
	$( '#cbo-Departamentos' ).change(function(){
	  $( '#cbo-Provincias' ).html('');
	  if ( $(this).val() > 0 ) {
      url = base_url + 'HelperController/getProvincias';
      $.post( url, {ID_Departamento : $(this).val()}, function( response ){
        $( '#cbo-Provincias' ).html('<option value="" selected="selected">- Seleccionar -</option>');
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
        $( '#cbo-Distritos' ).html('<option value="" selected="selected">- Seleccionar -</option>');
        for (var i = 0; i < response.length; i++)
          $( '#cbo-Distritos' ).append( '<option value="' + response[i].ID_Distrito + '">' + response[i].No_Distrito + '</option>' );
      }, 'JSON');
	  }
  })
	
	$( '#cbo-tipo_ecommerce_empresa' ).change(function(){
	  $( '.div-row-empresas-marketplace' ).hide();
    $( '#cbo-empresa-marketplace' ).html('<option value="0" selected="selected">- Vacío -</option>');
	  if ( $( '#cbo-tipo_ecommerce_empresa' ).find(':selected').data('tipo') == 2 ) {
      $( '.div-row-empresas-marketplace' ).show();
      $( '#cbo-empresa-marketplace' ).html('<option value="0" selected="selected">- Vacío -</option>');
      url = base_url + 'HelperController/getEmpresasMarketplace';
      $.post( url, {}, function( response ){
        if ( response.sStatus == 'success' ) {
          var l = response.arrData.length;
          $( '#cbo-empresa-marketplace' ).html('<option value="" selected="selected">- Seleccionar -</option>');
          for (var x = 0; x < l; x++) {
            $( '#cbo-empresa-marketplace' ).append( '<option value="' + response.arrData[x].ID_Empresa + '">' + response.arrData[x].No_Empresa + '</option>' );
          }
        } else {
          if( response.sMessageSQL !== undefined ) {
            console.log(response.sMessageSQL);
          }
          if ( response.sStatus == 'warning')
            $( '#cbo-empresa-marketplace' ).html('<option value="0" selected="selected">- Vacío -</option>');
        }
      }, 'JSON');
	  }
  })
  
	$( '#btn-whatsapp-laegestion_usuario' ).click(function(){
    $( '#btn-whatsapp-laegestion_usuario' ).text('');
    $( '#btn-whatsapp-laegestion_usuario' ).attr('disabled', true);
    $( '#btn-whatsapp-laegestion_usuario' ).append( 'Enviando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

    url = 'https://api.whatsapp.com/send?phone=' + $('#txt-Nu_Celular_Usuario').val() + '&text=';
    url += $('[name="Mensaje_WhatsApp_Usuario"]').val();
    url = encodeURI(url);
    var win = window.open(url, '_blank');

    setTimeout(function () { win.close(); }, 1500);

    $('#modal-laegestion_credenciales_usuario').modal('hide');

    $( '#btn-whatsapp-laegestion_usuario' ).text('');
    $( '#btn-whatsapp-laegestion_usuario' ).append( 'Enviar WhatsApp' );
    $( '#btn-whatsapp-laegestion_usuario' ).attr('disabled', false);
  })
  
  $('#btn-save_deposito_billetera').off('click').click(function () {
    if($( '#txt-modal_deposito-importe_deposito' ).val().length==0){
      $( '#txt-modal_deposito-importe_deposito' ).closest('.form-group').find('.help-block').html('Ingresar precio');
      $( '#txt-modal_deposito-importe_deposito' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if(parseFloat($( '#txt-modal_deposito-importe_deposito' ).val())<=0.00 || isNaN(parseFloat($( '#txt-modal_deposito-importe_deposito' ).val()))){
      $( '#txt-modal_deposito-importe_deposito' ).closest('.form-group').find('.help-block').html('Ingresar precio');
      $( '#txt-modal_deposito-importe_deposito' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      $( '#btn-save_deposito_billetera' ).text('');
      $( '#btn-save_deposito_billetera' ).attr('disabled', true);
      $( '#btn-save_deposito_billetera' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

      var arrDataDepositoBilletera = Array();
      arrDataDepositoBilletera = {
        'id_empresa' : $( '#hidden-modal_deposito-id_empresa' ).val(),
        'importe_deposito' : $( '#txt-modal_deposito-importe_deposito' ).val()
      }

      url = base_url + 'Configuracion/MonitoreoEmpresasController/actualizarDepositoBilletera';
      $.ajax({
        type		  : 'POST',
        dataType	: 'JSON',
        url		    : url,
        data		  : {
          arrDataDepositoBilletera : arrDataDepositoBilletera
        },
        success: function (response) {
          $('.modal-deposito_billetera').modal('hide');

          $('.modal-message').removeClass('modal-danger modal-warning modal-success');
          $('#modal-message').modal('show');

          if (response.status == 'success') {
            $('.modal-message').addClass(response.style_modal);
            $('.modal-title-message').text(response.message);
            setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
            
            reload_table_empresa();
          } else {
            $('.modal-message').addClass(response.style_modal);
            $('.modal-title-message').text(response.message);
            setTimeout(function () { $('#modal-message').modal('hide'); }, 1500);
          }
        
          $( '#btn-save_deposito_billetera' ).text('');
          $( '#btn-save_deposito_billetera' ).append( 'Guardar' );
          $( '#btn-save_deposito_billetera' ).attr('disabled', false);
        },
        error: function (jqXHR, textStatus, errorThrown) {
          $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
          
          $( '#modal-message' ).modal('show');
          $( '.modal-message' ).addClass( 'modal-danger' );
          $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
          setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
          
          //Message for developer
          console.log(jqXHR.responseText);
          
          $( '#btn-save_deposito_billetera' ).text('');
          $( '#btn-save_deposito_billetera' ).append( 'Guardar' );
          $( '#btn-save_deposito_billetera' ).attr('disabled', false);
        }
      });
    }
  });
  
  $('#btn-save_actualizar_saldo_billetera').off('click').click(function () {
    if($( '#txt-modal_saldo-importe_deposito' ).val().length==0){
      $( '#txt-modal_saldo-importe_deposito' ).closest('.form-group').find('.help-block').html('Ingresar precio');
      $( '#txt-modal_saldo-importe_deposito' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if(parseFloat($( '#txt-modal_saldo-importe_deposito' ).val())<=0.00 || isNaN(parseFloat($( '#txt-modal_saldo-importe_deposito' ).val()))){
      $( '#txt-modal_saldo-importe_deposito' ).closest('.form-group').find('.help-block').html('Ingresar precio');
      $( '#txt-modal_saldo-importe_deposito' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      $( '#btn-save_actualizar_saldo_billetera' ).text('');
      $( '#btn-save_actualizar_saldo_billetera' ).attr('disabled', true);
      $( '#btn-save_actualizar_saldo_billetera' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

      var arrDataDepositoBilletera = Array();
      arrDataDepositoBilletera = {
        'id_empresa' : $( '#hidden-modal_saldo-id_empresa' ).val(),
        'importe_deposito' : $( '#txt-modal_saldo-importe_deposito' ).val()
      }

      url = base_url + 'Configuracion/MonitoreoEmpresasController/actualizarSaldoAcumuladoBilletera';
      $.ajax({
        type		  : 'POST',
        dataType	: 'JSON',
        url		    : url,
        data		  : {
          arrDataDepositoBilletera : arrDataDepositoBilletera
        },
        success: function (response) {
          $('.modal-actualizar_saldo_billetera').modal('hide');

          $('.modal-message').removeClass('modal-danger modal-warning modal-success');
          $('#modal-message').modal('show');

          if (response.status == 'success') {
            $('.modal-message').addClass(response.style_modal);
            $('.modal-title-message').text(response.message);
            setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
            
            reload_table_empresa();
          } else {
            $('.modal-message').addClass(response.style_modal);
            $('.modal-title-message').text(response.message);
            setTimeout(function () { $('#modal-message').modal('hide'); }, 1500);
          }
        
          $( '#btn-save_actualizar_saldo_billetera' ).text('');
          $( '#btn-save_actualizar_saldo_billetera' ).append( 'Guardar' );
          $( '#btn-save_actualizar_saldo_billetera' ).attr('disabled', false);
        },
        error: function (jqXHR, textStatus, errorThrown) {
          $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
          
          $( '#modal-message' ).modal('show');
          $( '.modal-message' ).addClass( 'modal-danger' );
          $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
          setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
          
          //Message for developer
          console.log(jqXHR.responseText);
          
          $( '#btn-save_actualizar_saldo_billetera' ).text('');
          $( '#btn-save_actualizar_saldo_billetera' ).append( 'Guardar' );
          $( '#btn-save_actualizar_saldo_billetera' ).attr('disabled', false);
        }
      });
    }
  });
})

function agregarEmpresa(){
  $( '#modal-loader' ).modal('show');
  
  $( '#form-Empresa' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '#modal-Empresa' ).modal('show');
  $( '.modal-title' ).text('Nueva Empresa');

  $('[name="EID_Empresa"]').val('');
  $('[name="ENu_Documento_Identidad"]').val('');
  
  $( '.div-row-empresas-marketplace' ).hide();

  $( '.div-Estado' ).hide();
  $( '#cbo-Estado' ).html( '<option value="1">Activo</option>' );

  $('#cbo-Activar_Guia_Electronica').html('<option value="0">No</option>');
  $('#cbo-Activar_Guia_Electronica').append('<option value="1">Si</option>');

  $('#cbo-Agregar_Almacen_Virtual').html('<option value="0">No</option>');
  $('#cbo-Agregar_Almacen_Virtual').append('<option value="1">Si</option>');

  $('#cbo-multi_almacen').html('<option value="0">No</option>');
  $('#cbo-multi_almacen').append('<option value="1">Si</option>');

  url = base_url + 'HelperController/getTiposDocumentoIdentidad';
  $.post(url, function (response) {
    $('#cbo-TiposDocumentoIdentidad').html('<option value="">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++) {
      if (response[i]['ID_Tipo_Documento_Identidad'] == 2 || response[i]['ID_Tipo_Documento_Identidad'] == 4 || response[i]['ID_Tipo_Documento_Identidad'] == 1 )
        $('#cbo-TiposDocumentoIdentidad').append('<option value="' + response[i]['ID_Tipo_Documento_Identidad'] + '" data-nu_cantidad_caracteres="' + response[i]['Nu_Cantidad_Caracteres'] + '">' + response[i]['No_Tipo_Documento_Identidad_Breve'] + '</option>');
    }
  }, 'JSON');

  url = base_url + 'HelperController/getValoresTablaDato';
  $.post( url, {sTipoData : 'Tipos_Proveedor_FE'}, function( response ){
    if ( response.sStatus == 'success' ) {
      var l = response.arrData.length;
      $( '#cbo-tipo_proveedor_fe' ).html('<option value="" selected="selected">- Seleccionar -</option>');
      for (var x = 0; x < l; x++) {
        $( '#cbo-tipo_proveedor_fe' ).append( '<option value="' + response.arrData[x].Nu_Valor + '">' + response.arrData[x].No_Descripcion + '</option>' );
      }
    } else {
      if( response.sMessageSQL !== undefined ) {
        console.log(response.sMessageSQL);
      }
      if ( response.sStatus == 'warning')
        $( '#cbo-tipo_proveedor_fe' ).html('<option value="0" selected="selected">- Vacío -</option>');
    }
  }, 'JSON');

  url = base_url + 'HelperController/getValoresTablaDato';
  $.post( url, {sTipoData : 'Tipos_Ecommerce_Empresa'}, function( response ){
    if ( response.sStatus == 'success' ) {
      var l = response.arrData.length;
      $( '#cbo-tipo_ecommerce_empresa' ).html('<option value="" selected="selected">- Seleccionar -</option>');
      for (var x = 0; x < l; x++) {
        selected = '';
        if (response.arrData[x].ID_Tabla_Dato == 2043)
          selected = 'selected="selected"';
        $('#cbo-tipo_ecommerce_empresa').append('<option value="' + response.arrData[x].ID_Tabla_Dato + '"  ' + selected + ' data-tipo="' + response.arrData[x].Nu_Valor + '">' + response.arrData[x].No_Descripcion + '</option>' );
      }
    } else {
      if( response.sMessageSQL !== undefined ) {
        console.log(response.sMessageSQL);
      }
      if ( response.sStatus == 'warning')
        $( '#cbo-tipo_ecommerce_empresa' ).html('<option value="0" selected="selected">- Vacío -</option>');
    }
  }, 'JSON');

  $( '#cbo-empresa-marketplace' ).html('<option value="0" selected="selected">- Vacío -</option>');

  url = base_url + 'HelperController/getPaises';
  $.post( url , function( response ){
    if ( response.length == 1 ) {
      $('#cbo-Paises').html('<option value="' + response[0].ID_Pais + '">' + response[0].No_Pais + '</option>');

      url = base_url + 'HelperController/getDepartamentos';
      $.post(url, { ID_Pais: response[0].ID_Pais }, function (response) {
        $('#cbo-Departamentos').html('<option value="" selected="selected">- Seleccionar -</option>');
        for (var i = 0; i < response.length; i++) {
          selected = '';
          if (response[i].ID_Departamento == 1)
            selected = 'selected="selected"';
          $('#cbo-Departamentos').append('<option value="' + response[i].ID_Departamento + '" ' + selected + '>' + response[i].No_Departamento + '</option>');
        }
      }, 'JSON');
    } else {
      $( '#cbo-Paises' ).html('<option value="" selected="selected">- Seleccionar -</option>');
      for (var i = 0; i < response.length; i++)
        $( '#cbo-Paises' ).append( '<option value="' + response[i].ID_Pais + '">' + response[i].No_Pais + '</option>' );
    }
    $('#modal-loader').modal('hide');
  }, 'JSON');

  url = base_url + 'HelperController/getProvincias';
  $.post(url, { ID_Departamento: 1 }, function (response) {
    $('#cbo-Provincias').html('<option value="" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++) {
      selected = '';
      if (response[i].ID_Provincia == 1)
        selected = 'selected="selected"';
      $('#cbo-Provincias').append('<option value="' + response[i].ID_Provincia + '" ' + selected + '>' + response[i].No_Provincia + '</option>');
    }
  }, 'JSON');

  url = base_url + 'HelperController/getDistritos';
  $.post(url, { ID_Provincia: 1 }, function (response) {
    $('#cbo-Distritos').html('<option value="" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++) {
      selected = '';
      if (response[i].ID_Distrito == 1)
        selected = 'selected="selected"';
      $('#cbo-Distritos').append('<option value="' + response[i].ID_Distrito + '" ' + selected + '>' + response[i].No_Distrito + '</option>');
    }
  }, 'JSON');

  url = base_url + 'HelperController/getValoresTablaDato';
  var arrParams = {
    sTipoData : 'Ubigeo_INEI',
  }
  $.post( url, arrParams, function( response ){
    if ( response.sStatus == 'success' ) {
      var iTotalRegistros = response.arrData.length, response=response.arrData;
      $( '#cbo-ubigeo_inei' ).html( '<option value="" selected="selected">- Seleccionar -</option>');
      for (var i = 0; i < iTotalRegistros; i++) {
        selected = '';
        if (response[i].ID_Tabla_Dato == 1444)
          selected = 'selected="selected"';        
        $('#cbo-ubigeo_inei').append('<option value="' + response[i].ID_Tabla_Dato + '" ' + selected + '>' + response[i].Nu_Valor + ': ' + response[i].No_Descripcion + '</option>' );
      }
    } else {
      $( '#cbo-ubigeo_inei' ).html( '<option value="" selected="selected">- Vacío -</option>');
      console.log( response );
    }
  }, 'JSON');
}

function verEmpresa(ID){
  $( '#form-Empresa' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '#modal-loader' ).modal('show');
 
  url = base_url + 'Configuracion/MonitoreoEmpresasController/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $( '#modal-Empresa' ).modal('show');
      $( '.modal-title' ).text('Modifcar Empresa');
      
      $( '[name="EID_Empresa"]' ).val( response.ID_Empresa );
      $( '[name="ENu_Documento_Identidad"]' ).val( response.Nu_Documento_Identidad );
      
      $( '.div-row-nubefact' ).show();
      if ( response.Nu_Tipo_Proveedor_FE == 1 || response.Nu_Tipo_Proveedor_FE == 3 ) {
        $( '.div-row-nubefact' ).hide();
      }
      
      $( '.div-guia_sunat' ).hide();
      if ( response.Nu_Activar_Guia_Electronica == 1 ) {
        $( '.div-guia_sunat' ).show();
      }

      url = base_url + 'HelperController/getTiposDocumentoIdentidad';
      $.post(url, function (responseTiposDocumentoIdentidad) {
        $('#cbo-TiposDocumentoIdentidad').html('');
        for (var i = 0; i < responseTiposDocumentoIdentidad.length; i++) {
          if (responseTiposDocumentoIdentidad[i]['ID_Tipo_Documento_Identidad'] == 2 || responseTiposDocumentoIdentidad[i]['ID_Tipo_Documento_Identidad'] == 4 || responseTiposDocumentoIdentidad[i]['ID_Tipo_Documento_Identidad'] == 1) {
            selected = '';
            if (response.ID_Tipo_Documento_Identidad == responseTiposDocumentoIdentidad[i]['ID_Tipo_Documento_Identidad'])
              selected = 'selected="selected"';
            $('#cbo-TiposDocumentoIdentidad').append('<option value="' + responseTiposDocumentoIdentidad[i]['ID_Tipo_Documento_Identidad'] + '" data-nu_cantidad_caracteres="' + responseTiposDocumentoIdentidad[i]['Nu_Cantidad_Caracteres'] + '" ' + selected + '>' + responseTiposDocumentoIdentidad[i]['No_Tipo_Documento_Identidad_Breve'] + '</option>');

            if (response.ID_Tipo_Documento_Identidad == 2) {//DNI
              $('#label-Nombre_Documento_Identidad').text('DNI');
              $('#label-No_Entidad').text('Nombre(s) y Apellidos');
              $('#txt-Nu_Documento_Identidad').attr('maxlength', $('#cbo-TiposDocumentoIdentidad').find(':selected').data('nu_cantidad_caracteres'));
            } else if (response.ID_Tipo_Documento_Identidad == 4) {//RUC
              $('#label-Nombre_Documento_Identidad').text('RUC');
              $('#label-No_Entidad').text('Razón Social');
              $('#txt-Nu_Documento_Identidad').attr('maxlength', $('#cbo-TiposDocumentoIdentidad').find(':selected').data('nu_cantidad_caracteres'));
            } else {
              $('#label-Nombre_Documento_Identidad').text('OTROS');
              $('#label-No_Entidad').text('Nombre(s) y Apellidos');
              $('#txt-Nu_Documento_Identidad').attr('maxlength', $('#cbo-TiposDocumentoIdentidad').find(':selected').data('nu_cantidad_caracteres'));
            }
          }
        }
      }, 'JSON');

      url = base_url + 'HelperController/getValoresTablaDato';
      $.post( url, {sTipoData : 'Tipos_Proveedor_FE'}, function( responseProveedorFE ){
        $( '#cbo-tipo_proveedor_fe' ).html('<option value="" selected="selected">- Seleccionar -</option>');
        if ( responseProveedorFE.sStatus == 'success' ) {
          var l = responseProveedorFE.arrData.length;
          $( '#cbo-tipo_proveedor_fe' ).html('<option value="" selected="selected">- Seleccionar -</option>');
          for (var x = 0; x < l; x++) {
            selected = '';
            if(response.Nu_Tipo_Proveedor_FE == responseProveedorFE.arrData[x].Nu_Valor)
              selected = 'selected="selected"';
            $( '#cbo-tipo_proveedor_fe' ).append( '<option value="' + responseProveedorFE.arrData[x].Nu_Valor + '" ' + selected + '>' + responseProveedorFE.arrData[x].No_Descripcion + '</option>' );
          }
        } else {
          if( responseProveedorFE.sMessageSQL !== undefined ) {
            console.log(responseProveedorFE.sMessageSQL);
          }
          if ( responseProveedorFE.sStatus == 'warning')
            $( '#cbo-tipo_proveedor_fe' ).html('<option value="0" selected="selected">- Vacío -</option>');
        }
      }, 'JSON');

      url = base_url + 'HelperController/getValoresTablaDato';
      $.post( url, {sTipoData : 'Tipos_Ecommerce_Empresa'}, function( responseEcommerce ){
        $( '#cbo-tipo_ecommerce_empresa' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
        if ( responseEcommerce.sStatus == 'success' ) {
          var l = responseEcommerce.arrData.length;
          $( '#cbo-tipo_ecommerce_empresa' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
          for (var x = 0; x < l; x++) {
            selected = '';
            if(response.Nu_Tipo_Ecommerce_Empresa == responseEcommerce.arrData[x].ID_Tabla_Dato)
              selected = 'selected="selected"';
            $( '#cbo-tipo_ecommerce_empresa' ).append( '<option value="' + responseEcommerce.arrData[x].ID_Tabla_Dato + '" data-tipo="' + responseEcommerce.arrData[x].Nu_Valor + '" ' + selected + '>' + responseEcommerce.arrData[x].No_Descripcion + '</option>' );
          }
        } else {
          if( responseEcommerce.sMessageSQL !== undefined ) {
            console.log(responseEcommerce.sMessageSQL);
          }
          if ( responseEcommerce.sStatus == 'warning')
            $( '#cbo-tipo_ecommerce_empresa' ).html('<option value="0" selected="selected">- Vacío -</option>');
        }
      }, 'JSON');
      
      $( '.div-row-empresas-marketplace' ).hide();
      if ( response.ID_Empresa_Marketplace > 0 )
        $( '.div-row-empresas-marketplace' ).show();

      url = base_url + 'HelperController/getEmpresasMarketplace';
      $.post( url, {}, function( responseEmpresasMarketplace ){
        if ( responseEmpresasMarketplace.sStatus == 'success' ) {
          var l = responseEmpresasMarketplace.arrData.length;
          $( '#cbo-empresa-marketplace' ).html('<option value="" selected="selected">- Seleccionar -</option>');
          for (var x = 0; x < l; x++) {
            selected = '';
            if(response.ID_Empresa_Marketplace == responseEmpresasMarketplace.arrData[x].ID_Empresa)
              selected = 'selected="selected"';
            $( '#cbo-empresa-marketplace' ).append( '<option value="' + responseEmpresasMarketplace.arrData[x].ID_Empresa + '" ' + selected + '>' + responseEmpresasMarketplace.arrData[x].No_Empresa + '</option>' );
          }
        } else {
          if( responseEmpresasMarketplace.sMessageSQL !== undefined ) {
            console.log(responseEmpresasMarketplace.sMessageSQL);
          }
          if ( responseEmpresasMarketplace.sStatus == 'warning')
            $( '#cbo-empresa-marketplace' ).html('<option value="0" selected="selected">- Vacío -</option>');
        }
      }, 'JSON');

      $( '[name="Nu_Documento_Identidad"]' ).val( response.Nu_Documento_Identidad );
      $( '[name="No_Empresa"]' ).val( response.No_Empresa );
      $( '[name="No_Empresa_Comercial"]' ).val( response.No_Empresa_Comercial );
      $( '[name="Txt_Direccion_Empresa"]' ).val( response.Txt_Direccion_Empresa );

      $( '[name="Txt_Usuario_Sunat_Sol"]' ).val( response.Txt_Usuario_Sunat_Sol );
      $( '[name="Txt_Password_Sunat_Sol"]' ).val( response.Txt_Password_Sunat_Sol );
      $( '[name="Txt_Password_Firma_Digital"]' ).val( response.Txt_Password_Firma_Digital );
      $( '#upload-file-certificado_digital' ).text( response.sNombreArchivoCertificadoDigital );

      //GUIA ELECTRONICA TOKEN V2.0
      $( '[name="Txt_Sunat_Token_Guia_Client_ID"]' ).val( response.Txt_Sunat_Token_Guia_Client_ID );
      $( '[name="Txt_Sunat_Token_Guia_Client_Secret"]' ).val( response.Txt_Sunat_Token_Guia_Client_Secret );

      $( '.div-Estado' ).show();
      $( '#cbo-Estado' ).html( '' );
      var selected;
      for (var i = 0; i < 2; i++){
        selected = '';
        if(response.Nu_Estado == i)
          selected = 'selected="selected"';
        $( '#cbo-Estado' ).append( '<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Inactivo' : 'Activo') + '</option>' );
      }

      $('#cbo-Activar_Guia_Electronica').html('');
      var selected;
      for (var i = 0; i < 2; i++) {
        selected = '';
        if (response.Nu_Activar_Guia_Electronica == i)
          selected = 'selected="selected"';
        $('#cbo-Activar_Guia_Electronica').append('<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'No' : 'Si') + '</option>');
      }

      $('#cbo-Agregar_Almacen_Virtual').html('');
      var selected;
      for (var i = 0; i < 2; i++) {
        selected = '';
        if (response.Nu_Agregar_Almacen_Virtual == i)
          selected = 'selected="selected"';
        $('#cbo-Agregar_Almacen_Virtual').append('<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'No' : 'Si') + '</option>');
      }

      $('#cbo-multi_almacen').html('');
      var selected;
      for (var i = 0; i < 2; i++) {
        selected = '';
        if (response.Nu_MultiAlmacen == i)
          selected = 'selected="selected"';
        $('#cbo-multi_almacen').append('<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'No' : 'Si') + '</option>');
      }
      
      url = base_url + 'HelperController/getValoresTablaDato';
      var arrParams = {
        sTipoData : 'Ubigeo_INEI',
      }
      $.post( url, arrParams, function( responseUbigeoInei ){
        if ( responseUbigeoInei.sStatus == 'success' ) {
          var iTotalRegistros = responseUbigeoInei.arrData.length, responseUbigeoInei=responseUbigeoInei.arrData;
          $( '#cbo-ubigeo_inei' ).html( '<option value="" selected="selected">- Seleccionar -</option>');
          for (var i = 0; i < iTotalRegistros; i++) {
            selected = '';
            if(response.ID_Ubigeo_Inei == responseUbigeoInei[i].ID_Tabla_Dato)
              selected = 'selected="selected"';
            $( '#cbo-ubigeo_inei' ).append( '<option value="' + responseUbigeoInei[i].ID_Tabla_Dato + '" ' + selected + '>' + responseUbigeoInei[i].Nu_Valor + ': ' + responseUbigeoInei[i].No_Descripcion + '</option>' );
          }
        } else {
          $( '#cbo-ubigeo_inei' ).html( '<option value="" selected="selected">- Vacío -</option>');
          console.log( response );
        }
      }, 'JSON');

      url = base_url + 'HelperController/getPaises';
      $.post( url , function( responsePais ){
        $( '#cbo-Paises' ).html('');
        for (var i = 0; i < responsePais.length; i++){
          selected = '';
          if(response.ID_Pais == responsePais[i].ID_Pais)
            selected = 'selected="selected"';
          $( '#cbo-Paises' ).append( '<option value="' + responsePais[i].ID_Pais + '" ' + selected + '>' + responsePais[i].No_Pais + '</option>' );
        }
      }, 'JSON');
      
      url = base_url + 'HelperController/getDepartamentos';
      $.post( url, {ID_Pais : response.ID_Pais}, function( responseDepartamentos ){
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
        $( '#cbo-Distritos' ).html('');
        for (var i = 0; i < responseDistrito.length; i++){
          selected = '';
          if(response.ID_Distrito == responseDistrito[i].ID_Distrito)
            selected = 'selected="selected"';
          $( '#cbo-Distritos' ).append( '<option value="' + responseDistrito[i].ID_Distrito + '" ' + selected + '>' + responseDistrito[i].No_Distrito + '</option>' );
        }
      }, 'JSON');

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

function form_Empresa(){  
}

function eliminarEmpresa(ID){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });
  
  $( '#btn-save-delete' ).off('click').click(function () {
    $( '#modal-loader' ).modal('show');
    
    url = base_url + 'Configuracion/MonitoreoEmpresasController/eliminarEmpresa/' + ID;
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
    	    reload_table_empresa();
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
      }
    });
  });
}

function configuracionAutomaticaOpciones(ID, Nu_Estado_Empresa, Nu_Tipo_Proveedor_FE, Nu_Activar_Guia_Electronica, Txt_Direccion_Empresa) {
  var $modal_id = $('#modal-configuracion_automatica');
  $modal_id.modal('show');

  $('.modal-header-title-configuracion_automatica').text('¿Deseas configurar la empresa automáticamente?');

  url = base_url + 'HelperController/getValoresTablaDato';
  $.post(url, { sTipoData: 'Tipo_Rubro_Empresa' }, function (response) {
    if (response.sStatus == 'success') {
      var iTotalRegistros = response.arrData.length, response = response.arrData;
      $('#cbo-tipo_rubro_empresa_automatico').html('<option value="0" selected="selected">- Seleccionar -</option>');
      for (var i = 0; i < iTotalRegistros; i++)
        $('#cbo-tipo_rubro_empresa_automatico').append('<option value="' + response[i].Nu_Valor + '">' + response[i].No_Descripcion + '</option>');
    } else {
      $('#cbo-tipo_rubro_empresa_automatico').html('<option value="0" selected="selected">- Vacío -</option>');
      console.log(response);
    }
  }, 'JSON');

  $('#modal-configuracion_automatica').on('shown.bs.modal', function () {
    $('#txt-nombres_apellidos_automatico').focus();
  })

  $('#btn-modal-configuracion_automatica-cancel').off('click').click(function () {
    $modal_id.modal('hide');
  });

  $("#txt-email_automatico").blur(function () {
    caracteresCorreoValido($(this).val(), '#span-email');
  })
/*
1 = Nubefact
2 = Sunat
3 = Sin facturacion electronica*/

  $('.div-fe').show();
  $('.token_fe_automatico').hide();
  $('.div-generar-token_lae_fe').hide();
  $("#txt-url_fe_automatico").val('');
  $("#txt-token_fe_automatico").val('');
  if (Nu_Tipo_Proveedor_FE == 2 ) {
    $("#txt-url_fe_automatico").val('https://ecxpresslae.com/librerias/SunatFacturador/cpe');
    $("#txt-token_fe_automatico").val('.');
    $('.div-generar-token_lae_fe').show();
  }
  if (Nu_Tipo_Proveedor_FE == 1) {
    $('.token_fe_automatico').show();
  }
  if (Nu_Tipo_Proveedor_FE == 3) {
    $('.div-fe').hide();
  }

  $("#txt-email_automatico").blur(function () {
    caracteresCorreoValido($(this).val(), '#div-email');
  })

  $('#btn-modal-configuracion_automatica-send').off('click').click(function () {
    if ($("#txt-nombres_apellidos_automatico").val().length === 0) {
      $('#txt-nombres_apellidos_automatico').closest('.form-group').find('.help-block').html('Ingresar nombres y apellidos');
      $('#txt-nombres_apellidos_automatico').closest('.form-group').removeClass('has-success').addClass('has-error');

      scrollToError($('#modal-configuracion_automatica .modal-body'), $('#txt-nombres_apellidos_automatico'));
    } else if ($('#cbo-tipo_rubro_empresa_automatico').val() == 0) {
      $('#cbo-tipo_rubro_empresa_automatico').closest('.form-group').find('.help-block').html('Seleccionar tipo impuesto');
      $('#cbo-tipo_rubro_empresa_automatico').closest('.form-group').removeClass('has-success').addClass('has-error');

      scrollToError($('#modal-configuracion_automatica .modal-body'), $('#cbo-tipo_rubro_empresa_automatico'));
    } else if (!caracteresCorreoValido($('#txt-email_automatico').val(), '#div-email')) {
      $('#txt-email_automatico').closest('.form-group').find('.help-block').html('Ingresar email válido');
      $('#txt-email_automatico').closest('.form-group').removeClass('has-success').addClass('has-error');
      scrollToError($('#modal-configuracion_automatica .modal-body'), $('#txt-email_automatico'));
    } else if ($("#txt-password_automatico").val().length === 0 ) {
      $('#txt-password_automatico').closest('.form-group').find('.help-block').html('Ingresar contraseña');
      $('#txt-password_automatico').closest('.form-group').removeClass('has-success').addClass('has-error');

      scrollToError($('#modal-configuracion_automatica .modal-body'), $('#txt-password_automatico'));
    } else if ($("#txt-pago_cliente").val().length === 0) {
      $('#txt-pago_cliente').closest('.form-group').find('.help-block').html('Ingresar importe');
      $('#txt-pago_cliente').closest('.form-group').removeClass('has-success').addClass('has-error');

      scrollToError($('#modal-configuracion_automatica .modal-body'), $('#txt-pago_cliente'));
    } else if ($("#txt-url_fe_automatico").val().length === 0 && (Nu_Tipo_Proveedor_FE == 1 || Nu_Tipo_Proveedor_FE == 2) ) {
      $('#txt-url_fe_automatico').closest('.form-group').find('.help-block').html('Ingresar url');
      $('#txt-url_fe_automatico').closest('.form-group').removeClass('has-success').addClass('has-error');

      scrollToError($('#modal-configuracion_automatica .modal-body'), $('#txt-url_fe_automatico'));
    } else if ($("#txt-token_fe_automatico").val().length === 0 && (Nu_Tipo_Proveedor_FE == 1 || Nu_Tipo_Proveedor_FE == 2)) {
      $('#txt-token_fe_automatico').closest('.form-group').find('.help-block').html('Ingresar token');
      $('#txt-token_fe_automatico').closest('.form-group').removeClass('has-success').addClass('has-error');

      scrollToError($('#modal-configuracion_automatica .modal-body'), $('#txt-token_fe_automatico'));
    } else {
      $( '#modal-loader' ).modal('show');
      
      var arrParams = {
        'iIdEmpresa': ID,
        'iEstadoEmpresa': Nu_Estado_Empresa,
        'iTipoProveedorFE': Nu_Tipo_Proveedor_FE,
        'Nu_Activar_Guia_Electronica': Nu_Activar_Guia_Electronica,
        'sDireccionEmpresa': Txt_Direccion_Empresa,
        'iTipoRubroEmpresa': $('#cbo-tipo_rubro_empresa_automatico').val(),
        'sNombresApellidos': $('#txt-nombres_apellidos_automatico').val(),
        'sNumeroCelular': $('#txt-celular_automatico').val(),
        'sEmailUsuario': $('#txt-email_automatico').val(),
        'sPasswordUsuario': $('#txt-password_automatico').val(),
        'fPagoClienteServicio': $('#txt-pago_cliente').val(),
        'sUrlFE': $('#txt-url_fe_automatico').val(),
        'sTokenFE': $('#txt-token_fe_automatico').val(),
      };

      url = base_url + 'Configuracion/MonitoreoEmpresasController/configuracionAutomaticaOpciones';
      $.ajax({
        url       : url,
        type      : "POST",
        dataType  : "JSON",
        data : arrParams,
        success: function( response ){
          $( '#modal-loader' ).modal('hide');
          
          $modal_id.modal('hide');
          $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
          $( '#modal-message' ).modal('show');
          
          if (response.sStatus == 'success'){
            $( '.modal-message' ).addClass( 'modal-' + response.sStatus);
            $( '.modal-title-message' ).text(response.sMessage);
            setTimeout(function() {$('#modal-message').modal('hide');}, 1100);

            $('#cbo-tipo_rubro_empresa_automatico').val('0');
            $('#txt-nombres_apellidos_automatico').val('');
            $('#txt-celular_automatico').val('');
            $('#txt-email_automatico').val('');
            $('#txt-password_automatico').val('');
            $('#txt-pago_cliente').val('');
            $('#txt-url_fe_automatico').val('');
            $('#txt-token_fe_automatico').val( '' );

            reload_table_empresa();
          } else {
            $('.modal-message').addClass('modal-' + response.sStatus);
            $('.modal-title-message').text(response.sMessage);
            setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
          }
        },
        error: function (jqXHR, textStatus, errorThrown) {
          $( '#modal-loader' ).modal('hide');
          $modal_id.modal('hide');
          $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
          
          $( '#modal-message' ).modal('show');
          $( '.modal-message' ).addClass( 'modal-danger' );
          $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
          setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
          
          //Message for developer
          console.log(jqXHR.responseText);
        }
      });
    } // if - else validacion
  });
}

function reload_table_empresa(){
  table_empresa.ajax.reload(null,false);
}

function getPrimerUsuarioLaeGestionxEmpresa(ID_Empresa) {
  url = base_url + 'Configuracion/MonitoreoEmpresasController/getPrimerUsuarioLaeGestionxEmpresa';
  $.post( url, {ID_Empresa : ID_Empresa}, function( response ){
    console.log(response);
    if (response.sStatus=='success') {
      $('#modal-laegestion_credenciales_usuario').modal('show');

      $('.modal-header-title_empresa').html(response.arrData.Nu_Documento_Identidad + ' - ' + response.arrData.No_Empresa);

      $('#txt-Nu_Celular_Usuario').val(response.arrData.Nu_Codigo_Pais + response.arrData.Nu_Celular);

      var laegestion_credenciales_usuario='';
      laegestion_credenciales_usuario += '¡Bienvenido a *EcxLae*!\n\n';
      laegestion_credenciales_usuario += '*🌐 Web:* https://ecxpresslae.com/principal\n';
      laegestion_credenciales_usuario += '*👤 Usuario:* ' + response.arrData.No_Usuario + '\n';
      laegestion_credenciales_usuario += '*🔒 Contraseña:* ' + response.arrData.No_Password;
      
      $('[name="Mensaje_WhatsApp_Usuario"]').val(laegestion_credenciales_usuario);

      $('.modal-p-body-laegestion_credenciales_usuario').html('');
    } else {
      alert(response.sMessage);
    }
  }, 'JSON');
}

function cambiarEstadoLaeShop(ID, Nu_Estado) {
  var $modal_delete = $('#modal-message-delete');
  $modal_delete.modal('show');

  $('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
  $('.modal-message-delete').addClass('modal-success');

  $('.modal-title-message-delete').text('¿Deseas cambiar estado?');

  $('#btn-cancel-delete').off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $('#btn-save-delete').off('click').click(function () {
    $('#modal-loader').modal('show');

    url = base_url + 'Configuracion/MonitoreoEmpresasController/cambiarEstadoLaeShop/' + ID + '/' + Nu_Estado;
    $.ajax({
      url: url,
      type: "GET",
      dataType: "JSON",
      success: function (response) {
        $('#modal-loader').modal('hide');

        $modal_delete.modal('hide');
        $('.modal-message').removeClass('modal-danger modal-warning modal-success');
        $('#modal-message').modal('show');

        if (response.status == 'success') {
          $('.modal-message').addClass(response.style_modal);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
          reload_table_empresa();
        } else {
          $('.modal-message').addClass(response.style_modal);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1500);
        }
      }
    });
  });
}

function cambiarEstadoLaeGestion(ID, Nu_Estado) {
  var $modal_delete = $('#modal-message-delete');
  $modal_delete.modal('show');

  $('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
  $('.modal-message-delete').addClass('modal-success');

  $('.modal-title-message-delete').text('¿Deseas cambiar estado?');

  $('#btn-cancel-delete').off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $('#btn-save-delete').off('click').click(function () {
    $('#modal-loader').modal('show');

    url = base_url + 'Configuracion/MonitoreoEmpresasController/cambiarEstadoLaeGestion/' + ID + '/' + Nu_Estado;
    $.ajax({
      url: url,
      type: "GET",
      dataType: "JSON",
      success: function (response) {
        $('#modal-loader').modal('hide');

        $modal_delete.modal('hide');
        $('.modal-message').removeClass('modal-danger modal-warning modal-success');
        $('#modal-message').modal('show');

        if (response.status == 'success') {
          $('.modal-message').addClass(response.style_modal);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
          reload_table_empresa();
        } else {
          $('.modal-message').addClass(response.style_modal);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1500);
        }
      }
    });
  });
}

function cambiarPlanLaeGestion(ID, Nu_Estado) {
  var $modal_delete = $('#modal-message-delete');
  $modal_delete.modal('show');

  $('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
  $('.modal-message-delete').addClass('modal-success');

  $('.modal-title-message-delete').text('¿Deseas cambiar estado?');

  $('#btn-cancel-delete').off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $('#btn-save-delete').off('click').click(function () {
    $('#modal-loader').modal('show');

    url = base_url + 'Configuracion/MonitoreoEmpresasController/cambiarPlanLaeGestion/' + ID + '/' + Nu_Estado;
    $.ajax({
      url: url,
      type: "GET",
      dataType: "JSON",
      success: function (response) {
        $('#modal-loader').modal('hide');

        $modal_delete.modal('hide');
        $('.modal-message').removeClass('modal-danger modal-warning modal-success');
        $('#modal-message').modal('show');

        if (response.status == 'success') {
          $('.modal-message').addClass(response.style_modal);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
          reload_table_empresa();
        } else {
          $('.modal-message').addClass(response.style_modal);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1500);
        }
      }
    });
  });
}

function cambiarPlanLaeShop(ID, Nu_Estado) {
  var $modal_delete = $('#modal-message-delete');
  $modal_delete.modal('show');

  $('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
  $('.modal-message-delete').addClass('modal-success');

  $('.modal-title-message-delete').text('¿Deseas cambiar estado?');

  $('#btn-cancel-delete').off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $('#btn-save-delete').off('click').click(function () {
    $('#modal-loader').modal('show');

    url = base_url + 'Configuracion/MonitoreoEmpresasController/cambiarPlanLaeShop/' + ID + '/' + Nu_Estado;
    $.ajax({
      url: url,
      type: "GET",
      dataType: "JSON",
      success: function (response) {
        $('#modal-loader').modal('hide');

        $modal_delete.modal('hide');
        $('.modal-message').removeClass('modal-danger modal-warning modal-success');
        $('#modal-message').modal('show');

        if (response.status == 'success') {
          $('.modal-message').addClass(response.style_modal);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
          reload_table_empresa();
        } else {
          $('.modal-message').addClass(response.style_modal);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1500);
        }
      }
    });
  });
}

function configurarTiendaVirtual(ID, Nu_Estado_Empresa, Nu_Tipo_Proveedor_FE,) {
  var $modal_delete = $('#modal-message-delete');
  $modal_delete.modal('show');

  $('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
  $('.modal-message-delete').addClass('modal-success');

  $('.modal-title-message-delete').text('¿Deseas configurar tienda virtual automaticamente?');

  $('#btn-cancel-delete').off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $('#btn-save-delete').off('click').click(function () {
    $('#modal-loader').modal('show');
    var arrParams = {
      'iIdEmpresa': ID,
      'iEstadoEmpresa': Nu_Estado_Empresa,
      'iTipoProveedorFE': Nu_Tipo_Proveedor_FE,
    };

    url = base_url + 'Configuracion/MonitoreoEmpresasController/configurarTiendaVirtual';
    $.ajax({
      url: url,
      type: "POST",
      dataType: "JSON",
      data: arrParams,
      success: function (response) {
        $('#modal-loader').modal('hide');

        $modal_delete.modal('hide');
        $('.modal-message').removeClass('modal-danger modal-warning modal-success');
        $('#modal-message').modal('show');

        if (response.sStatus == 'success') {
          $('.modal-message').addClass('modal-' + response.sStatus);
          $('.modal-title-message').text(response.sMessage);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);

          reload_table_empresa();
        } else {
          $('.modal-message').addClass('modal-' + response.sStatus);
          $('.modal-title-message').text(response.sMessage);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1500);
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        $('#modal-loader').modal('hide');
        $modal_delete.modal('hide');
        $('.modal-message').removeClass('modal-danger modal-warning modal-success');

        $('#modal-message').modal('show');
        $('.modal-message').addClass('modal-danger');
        $('.modal-title-message').text(textStatus + ' [' + jqXHR.status + ']: ' + errorThrown);
        setTimeout(function () { $('#modal-message').modal('hide'); }, 1700);

        //Message for developer
        console.log(jqXHR.responseText);
      }
    });
  });
}

function cambiarEstadoEmpresa(ID, Nu_Estado) {
  var $modal_delete = $('#modal-message-delete');
  $modal_delete.modal('show');

  $('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
  $('.modal-message-delete').addClass('modal-success');

  $('.modal-title-message-delete').text('¿Deseas cambiar estado?');

  $('#btn-cancel-delete').off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $('#btn-save-delete').off('click').click(function () {
    $('#modal-loader').modal('show');

    url = base_url + 'Configuracion/MonitoreoEmpresasController/cambiarEstadoEmpresa/' + ID + '/' + Nu_Estado;
    $.ajax({
      url: url,
      type: "GET",
      dataType: "JSON",
      success: function (response) {
        $('#modal-loader').modal('hide');

        $modal_delete.modal('hide');
        $('.modal-message').removeClass('modal-danger modal-warning modal-success');
        $('#modal-message').modal('show');

        if (response.status == 'success') {
          $('.modal-message').addClass(response.style_modal);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
          reload_table_empresa();
        } else {
          $('.modal-message').addClass(response.style_modal);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1500);
        }
      }
    });
  });
}

function cambiarEstadoVendedorDrop(ID, Nu_Estado) {
  var $modal_delete = $('#modal-message-delete');
  $modal_delete.modal('show');

  $('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
  $('.modal-message-delete').addClass('modal-success');

  $('.modal-title-message-delete').text('¿Deseas cambiar estado?');

  $('#btn-cancel-delete').off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $('#btn-save-delete').off('click').click(function () {
    $('#modal-loader').modal('show');

    url = base_url + 'Configuracion/MonitoreoEmpresasController/cambiarEstadoVendedorDrop/' + ID + '/' + Nu_Estado;
    $.ajax({
      url: url,
      type: "GET",
      dataType: "JSON",
      success: function (response) {
        $('#modal-loader').modal('hide');

        $modal_delete.modal('hide');
        $('.modal-message').removeClass('modal-danger modal-warning modal-success');
        $('#modal-message').modal('show');

        if (response.status == 'success') {
          $('.modal-message').addClass(response.style_modal);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
          reload_table_empresa();
        } else {
          $('.modal-message').addClass(response.style_modal);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1500);
        }
      }
    });
  });
}

function cambiarEstadoProveedorDrop(ID, Nu_Estado) {
  var $modal_delete = $('#modal-message-delete');
  $modal_delete.modal('show');

  $('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
  $('.modal-message-delete').addClass('modal-success');

  $('.modal-title-message-delete').text('¿Deseas cambiar estado?');

  $('#btn-cancel-delete').off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $('#btn-save-delete').off('click').click(function () {
    $('#modal-loader').modal('show');

    url = base_url + 'Configuracion/MonitoreoEmpresasController/cambiarEstadoProveedorDrop/' + ID + '/' + Nu_Estado;
    $.ajax({
      url: url,
      type: "GET",
      dataType: "JSON",
      success: function (response) {
        $('#modal-loader').modal('hide');

        $modal_delete.modal('hide');
        $('.modal-message').removeClass('modal-danger modal-warning modal-success');
        $('#modal-message').modal('show');

        if (response.status == 'success') {
          $('.modal-message').addClass(response.style_modal);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
          reload_table_empresa();
        } else {
          $('.modal-message').addClass(response.style_modal);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1500);
        }
      }
    });
  });
}

function cambiarEstadoTiendaPropia(ID, Nu_Estado) {
  var $modal_delete = $('#modal-message-delete');
  $modal_delete.modal('show');

  $('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
  $('.modal-message-delete').addClass('modal-success');

  $('.modal-title-message-delete').text('¿Deseas cambiar estado?');

  $('#btn-cancel-delete').off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $('#btn-save-delete').off('click').click(function () {
    $('#modal-loader').modal('show');

    url = base_url + 'Configuracion/MonitoreoEmpresasController/cambiarEstadoTiendaPropia/' + ID + '/' + Nu_Estado;
    $.ajax({
      url: url,
      type: "GET",
      dataType: "JSON",
      success: function (response) {
        $('#modal-loader').modal('hide');

        $modal_delete.modal('hide');
        $('.modal-message').removeClass('modal-danger modal-warning modal-success');
        $('#modal-message').modal('show');

        if (response.status == 'success') {
          $('.modal-message').addClass(response.style_modal);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
          reload_table_empresa();
        } else {
          $('.modal-message').addClass(response.style_modal);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1500);
        }
      }
    });
  });
}

function verProgresoTienda(ID_Empresa) {
  url = base_url + 'Configuracion/MonitoreoEmpresasController/verProgresoTienda';

  $('.modal-header-ver_progreso_global_cliente').html('<strong>Progreso de Tienda</strong>');
  $('.modal-p-body-ver_progreso_global_cliente').html('Cargando...');

  $.post( url, {ID_Empresa : ID_Empresa}, function( response ){
    $('.modal-p-body-ver_progreso_global_cliente').html('');
    if(response.status=='success'){
      $('#modal-ver_progreso_global_cliente').modal('show');

      var iTotalRegistros = response.result.length, response=response.result, sMensajeHtml = '';
      for (var i = 0; i < iTotalRegistros; i++) {
        sMensajeHtml += '<b>' + response[i].No_Subtitulo + ':</b> ' + response[i].No_Titulo + ' - <b>Estado:</b> ' + (response[i].Nu_Estado_Proceso == 1 ? '<span class="label label-success">Completado</span>' : '<span class="label label-default">Pendiente</span>') + '<br><br>';
      }
      $('.modal-p-body-ver_progreso_global_cliente').html(sMensajeHtml);
    } else {
      alert(response.message);
    }
  }, 'JSON');
}

function agregarDeposito(ID_Empresa){
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();

  $( '#hidden-modal_deposito-id_empresa' ).val(ID_Empresa);
	$( '.modal-deposito_billetera' ).modal('show');
  $( '#txt-modal_deposito-importe_deposito' ).val('');
  $( '.modal-deposito_billetera' ).on('shown.bs.modal', function() {
    $( '#txt-modal_deposito-importe_deposito' ).focus();
  })
}

function actualizarSaldoAcumulado(ID_Empresa){
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();

  $( '#hidden-modal_saldo-id_empresa' ).val(ID_Empresa);
	$( '.modal-actualizar_saldo_billetera' ).modal('show');
  $( '#txt-modal_saldo-importe_deposito' ).val('');
  $( '.modal-actualizar_saldo_billetera' ).on('shown.bs.modal', function() {
    $( '#txt-modal_saldo-importe_deposito' ).focus();
  })
}