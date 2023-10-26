var url;
var table_sistema;

$(function () {
  $('.select2').select2();
  $('[data-mask]').inputmask();
  
	$(".toggle-password-sistema").click(function() {
		$(this).toggleClass("fa-eye fa-eye-slash");
    var $pwd = $(".pwd_sistema");
    if ($pwd.attr('type') == 'password') {
      $pwd.attr('type', 'text');
      $('#Txt_Token').css("-webkit-text-security", "initial");
    } else {
      $pwd.attr('type', 'password');
      $('#Txt_Token').css("-webkit-text-security", "disc");
    }
  });

  url = base_url + 'Configuracion/SistemaController/ajax_list';
  table_sistema = $( '#table-Sistema' ).DataTable({
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
        data.Filtro_Tipo_Sistema = $('#cbo-filtro-tipo_sistema').val(),
        data.Filtro_Estado = $('#cbo-filtro-estado').val(),
        data.filtro_empresa = $( '#cbo-filtro_empresa' ).val(),
        data.filtro_organizacion = $( '#cbo-filtro_organizacion' ).val(),
        data.Filtros_Sistemas = $( '#cbo-Filtros_Sistemas' ).val(),
        data.Global_Filter = $( '#txt-Global_Filter' ).val();
      },
    },
    'columnDefs': [{
      'className' : 'text-center',
      'targets'   : 'no-sort',
      'orderable' : false,
    },],
    'lengthMenu': [[10, 100, 1000, -1], [10, 100, 1000, "Todos"]],
  });
  
  $( '.dataTables_length' ).addClass('col-md-3');
  $( '.dataTables_paginate' ).addClass('col-md-9');
  
  $( '#txt-Global_Filter' ).keyup(function() {
    table_sistema.search($(this).val()).draw();
  });
  
  $( '#form-Sistema' ).validate({
		rules:{
      ID_Empresa: {
        required: true,
      },
			Fe_Inicio_Sistema: {
				required: true,
      },
      Nu_Height_Logo_Ticket: {
        required: true,
      },
      Nu_Width_Logo_Ticket: {
        required: true,
      },
			Nu_Imprimir_Liquidacion_Caja: {
				required: true,
      },
      Txt_Token: {
        required: true,
      },
			Txt_Email_Empresa:{
				validemail: true,
			},
		},
    messages: {
      ID_Empresa: {
        required: "Seleccionar empresa"
      },
			Fe_Inicio_Sistema:{
				required: "Ingresar fecha"
      },
      Nu_Height_Logo_Ticket: {
        required: "Alto"
      },
      Nu_Width_Logo_Ticket: {
        required: "Ancho"
      },
			Nu_Imprimir_Liquidacion_Caja:{
				required: "Ingresar valor"
      },
      Txt_Token: {
        required: "Ingresar llave key de token",
      },
			Txt_Email_Empresa:{
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
		submitHandler: form_Sistema
  });  

  $('#cbo-filtro-tipo_sistema').change(function () {
    table_sistema.search($(this).val()).draw();
  });

  $('#cbo-filtro-estado').change(function () {
    table_sistema.search($(this).val()).draw();
  });
  
  $( '#cbo-filtro_empresa' ).html('<option value="0" selected="selected">- Todas -</option>');
  $( '#cbo-filtro_organizacion' ).html('<option value="0" selected="selected">- Todas -</option>');

  url = base_url + 'HelperController/getEmpresas';
  $.post( url , function( response ){
    $( '#cbo-filtro_empresa' ).html('<option value="0" selected="selected">- Todas -</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-filtro_empresa' ).append( '<option value="' + response[i].ID_Empresa + '">' + response[i].No_Empresa + '</option>' );
  }, 'JSON');
  
	$( '#cbo-filtro_empresa' ).change(function(){
    if ( $(this).val() > 0 ) {
      $( '#modal-loader' ).modal('show');
      url = base_url + 'HelperController/getOrganizaciones';
      var arrParams = {
        iIdEmpresa : $( this ).val(),
      };
      $.post( url, arrParams, function( response ){
        $( '#cbo-filtro_organizacion' ).html('<option value="0" selected="selected">- Todas -</option>');
        for (var i = 0; i < response.length; i++)
          $( '#cbo-filtro_organizacion' ).append( '<option value="' + response[i].ID_Organizacion + '">' + response[i].No_Organizacion + '</option>' );    
        $( '#modal-loader' ).modal('hide');
      }, 'JSON');
    }
    table_sistema.search($(this).val()).draw();
  });

	$( '#cbo-filtro_organizacion' ).change(function(){
    table_sistema.search($(this).val()).draw();
  });
})

function agregarSistema(){
  $( '#txt-EID_Empresa' ).focus();
  
  $( '#modal-loader' ).modal('show');
  
  $( '#form-Sistema' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '.div-Listar' ).hide();
  $( '.div-AgregarEditar' ).show();

  $( '[name="EID_Empresa"]' ).val('');
  $( '[name="EID_Configuracion"]' ).val('');
  $( '[name="ENo_Dominio_Empresa"]' ).val('');
  
  $( '[name="ENo_Foto_Boleta"]' ).val('');
  $( '[name="ENo_Foto_Factura"]' ).val('');
  $( '[name="ENo_Foto_NCredito"]' ).val('');
  $( '[name="ENo_Foto_Guia"]' ).val('');
      
  $( '.date-picker-invoice' ).val(fDay + '/' + fMonth + '/' + fYear);

  url = base_url + 'HelperController/getEmpresas';
  $.post( url , function( response ){
    $( '#modal-loader' ).modal('hide');
    $( '#cbo-Empresas' ).html('<option value="" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-Empresas' ).append( '<option value="' + response[i].ID_Empresa + '">' + response[i].No_Empresa + '</option>' );
  }, 'JSON');
  
  $( '#cbo-enviar_sunat_automatic' ).html( '<option value="1">Si</option>' );
  $( '#cbo-enviar_sunat_automatic' ).append( '<option value="0">No</option>' );
  
  $('#cbo-activar_stock').html( '<option value="1">Activar</option>' );
  $('#cbo-activar_stock').append( '<option value="0">Desactivar</option>' );

  $('#cbo-activar_redondeo').html( '<option value="1">Activar</option>' );
  $('#cbo-activar_redondeo').append( '<option value="0">Desactivar</option>' );

  $( '#cbo-logo_ticket' ).html( '<option value="1">Si</option>' );
  $( '#cbo-logo_ticket' ).append( '<option value="0">No</option>' );
  
  $( '#cbo-arqueo_punto_venta' ).html( '<option value="1">Categoría</option>' );
  $('#cbo-arqueo_punto_venta').append('<option value="2">Productos</option>');

  $('#cbo-precio_punto_venta').html('<option value="1">Activar</option>');
  $('#cbo-precio_punto_venta').append('<option value="0">Desactivar</option>');

  $('#cbo-activar_descuento_punto_venta').html( '<option value="1">Activar</option>' );
  $('#cbo-activar_descuento_punto_venta').append('<option value="0">Desactivar</option>');

  $('#cbo-activar_ticket_linea_detalle').html('<option value="0">Desactivar</option>');
  $('#cbo-activar_ticket_linea_detalle').append('<option value="1">Activar</option>');

  $('#cbo-predeterminar_tipo_documento_venta').html('<option value="4">Boleta</option>');
  $('#cbo-predeterminar_tipo_documento_venta').append('<option value="2">Nota de Venta</option>');
  $('#cbo-predeterminar_tipo_documento_venta').append('<option value="3">Factura</option>');
  
  $('#div-predeterminar_cliente_varios_venta').hide();
  $('#cbo-predeterminar_cliente_varios_venta').html('<option value="0">Desactivar</option>');
  $('#cbo-predeterminar_cliente_varios_venta').append('<option value="1">Activar</option>');

  $('#cbo-autorizacion_punto_venta').html( '<option value="1">Activar</option>' );
  $('#cbo-autorizacion_punto_venta').append('<option value="0">Desactivar</option>');
  
  $('#cbo-tipo_lenguaje_impresion_pos').html('<option value="1">HTML</option>');
  $('#cbo-tipo_lenguaje_impresion_pos').append('<option value="2">PDF</option>');

  $('#cbo-formato_impresion_pdf').html('<option value="TICKET">TICKET</option>');
  $('#cbo-formato_impresion_pdf').append('<option value="A4">A4</option>');

  $('#cbo-imprimir_columna_ticket_detalle').html('<option value="0">Nombre</option>');
  $('#cbo-imprimir_columna_ticket_detalle').append('<option value="1">Codigo + Nombre</option>');
  $('#cbo-imprimir_columna_ticket_detalle').append('<option value="2">Codigo + Nombre + Marca</option>');

  $('#cbo-Nu_Tipo_Vender_Usuario_POS').html('<option value="1">Cajero</option>');
  $('#cbo-Nu_Tipo_Vender_Usuario_POS').append('<option value="2">Cajero + Vendedor</option>');

	url = base_url + 'HelperController/getValoresTablaDato';
	$.post( url, {sTipoData : 'Tipo_Rubro_Empresa'}, function( response ){
    if ( response.sStatus == 'success' ) {
      var iTotalRegistros = response.arrData.length, response=response.arrData;
      $( '#cbo-tipo_rubro_empresa' ).html( '<option value="0" selected="selected">- Todos -</option>');
      for (var i = 0; i < iTotalRegistros; i++)
        $( '#cbo-tipo_rubro_empresa' ).append( '<option value="' + response[i].Nu_Valor + '">' + response[i].No_Descripcion + '</option>' );
    } else {
      $( '#cbo-tipo_rubro_empresa' ).html( '<option value="0" selected="selected">- Vacío -</option>');
      console.log( response );
    }
		$( '#modal-loader' ).modal('hide');
  }, 'JSON');

  $( '.div-Estado' ).hide();
  $( '#cbo-Estado' ).html( '<option value="1">Activo</option>' );
  
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
  
  url = base_url + 'Configuracion/SistemaController/uploadOnly';
  var myDropzone = new Dropzone("#id-divDropzone", {
    url: url,
    params: {
      iVersionImage: 1,
      iIdConfiguracion: 1,
    },
    acceptedFiles: ".jpeg,.jpg,.png",
    addRemoveLinks: true,
    uploadMultiple: false,
    maxFilesize: 1,//Peso en MB
    maxFiles: 1,
    thumbnailHeight: 200,
    thumbnailWidth: 200,
    parallelUploads: 1,
    thumbnail: function(file, dataUrl) {
      if (file.previewElement) {
        file.previewElement.classList.remove("dz-file-preview");
        var images = file.previewElement.querySelectorAll("[data-dz-thumbnail]");
        for (var i = 0; i < images.length; i++) {
          var thumbnailElement = images[i];
          thumbnailElement.alt = file.name;
          thumbnailElement.src = dataUrl;
        }
        setTimeout(function() { file.previewElement.classList.add("dz-image-preview"); }, 1);
      }
    },
    removedfile: function(file){
      var nameFileImage = file.name;
      url = base_url + 'Configuracion/SistemaController/removeFileImage';
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
      //Verificar respuesta del servidor al subir archivo
      this.on("success", function(file, response) {
        var response = jQuery.parseJSON(response);

        $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
        $( '#modal-message' ).modal('show');
        
        if (response.sStatus != 'error'){
          $( '.modal-message' ).addClass(response.sClassModal);
          $( '.modal-title-message' ).text(response.sMessage);

          $( '[name="hidden-nombre_logo"]' ).val( response.sNombreImagenLogoEmpresa );
          $( '#hidden-nombre_imagen_logo_empresa' ).val( response.sNombreImagenLogoEmpresaUrl );

          setTimeout(function() {$('#modal-message').modal('hide'); }, 1100);
        } else {
          $( '.modal-message' ).addClass(response.sClassModal);
          $( '.modal-title-message' ).text(response.sMessage);
          setTimeout(function() {$('#modal-message').modal('hide');}, 1200);
        }
      })
    },
  });
  
  $( '#upload-file-info_boleta' ).text('');
  $( '#upload-file-info_factura' ).text('');
  $( '#upload-file-info_ncredito' ).text('');
  $( '#upload-file-info_guia' ).text('');
  
  $( '#modal-loader' ).modal('hide');
}

function verSistema(ID, No_Logo_Empresa, No_Imagen_Logo_Empresa, Nu_Version_Imagen){
  $( '#txt-EID_Empresa' ).focus();
  
  $( '#form-Sistema' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '.div-Listar' ).hide();
  $( '.div-AgregarEditar' ).show();
  
  $( '#modal-loader' ).modal('show');
 
  $( '[name="ENo_Foto_Boleta"]' ).val('');
  $( '[name="ENo_Foto_Factura"]' ).val('');
  $( '[name="ENo_Foto_NCredito"]' ).val('');
  $( '[name="ENo_Foto_Guia"]' ).val('');
  
  url = base_url + 'Configuracion/SistemaController/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $('[name="EID_Empresa"]').val(response.ID_Empresa);
      $('[name="EID_Configuracion"]').val(response.ID_Configuracion);
      $('[name="ENo_Dominio_Empresa"]').val(response.No_Dominio_Empresa);
      $('[name="hidden-nombre_logo"]').val(response.No_Logo_Empresa);
      $('[name="No_Imagen_Logo_Empresa"]').val(response.No_Imagen_Logo_Empresa);
      
      var selected;
      url = base_url + 'HelperController/getEmpresas';
      $.post( url , function( responseEmpresa ){
        $( '#cbo-Empresas' ).html('');
        for (var i = 0; i < responseEmpresa.length; i++){
          selected = '';
          if(response.ID_Empresa == responseEmpresa[i].ID_Empresa)
            selected = 'selected="selected"';
          $( '#cbo-Empresas' ).append( '<option value="' + responseEmpresa[i].ID_Empresa + '" ' + selected + '>' + responseEmpresa[i].No_Empresa + '</option>' );
        }
      }, 'JSON');
      
      $( '[name="Fe_Inicio_Sistema"]' ).val(ParseDateString(response.Fe_Inicio_Sistema, 6, '-'));
      
      $( '#cbo-enviar_sunat_automatic' ).html('');
      for (var i = 0; i < 2; i++){
        selected = '';
        if(response.Nu_Enviar_Sunat_Automatic == i)
          selected = 'selected="selected"';
        $( '#cbo-enviar_sunat_automatic' ).append( '<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'No' : 'Si') + '</option>' );
      }
      
      $( '#cbo-activar_stock' ).html('');
      for (var i = 0; i < 2; i++){
        selected = '';
        if(response.Nu_Validar_Stock == i)
          selected = 'selected="selected"';
        $( '#cbo-activar_stock' ).append( '<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Desactivar' : 'Activar') + '</option>' );
      }
      
      $( '#cbo-activar_redondeo' ).html('');
      for (var i = 0; i < 2; i++){
        selected = '';
        if(response.Nu_Activar_Redondeo == i)
          selected = 'selected="selected"';
        $( '#cbo-activar_redondeo' ).append( '<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Desactivar' : 'Activar') + '</option>' );
      }
      
      $( '[name="Nu_Dia_Limite_Fecha_Vencimiento"]' ).val(response.Nu_Dia_Limite_Fecha_Vencimiento);

      $( '#cbo-logo_ticket' ).html( '' );
      for (var i = 0; i < 2; i++){
        selected = '';
        if(response.Nu_Logo_Empresa_Ticket == i)
          selected = 'selected="selected"';
        $( '#cbo-logo_ticket' ).append( '<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'No' : 'Si') + '</option>' );
      }

      $( '[name="Nu_Height_Logo_Ticket"]' ).val(response.Nu_Height_Logo_Ticket);
      $( '[name="Nu_Width_Logo_Ticket"]' ).val(response.Nu_Width_Logo_Ticket);
      $( '[name="Nu_Imprimir_Liquidacion_Caja"]' ).val(response.Nu_Imprimir_Liquidacion_Caja);

      $('#cbo-precio_punto_venta').html('');
      for (var i = 0; i < 2; i++) {
        selected = '';
        if (response.Nu_Precio_Punto_Venta == i)
          selected = 'selected="selected"';
        $('#cbo-precio_punto_venta').append('<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Desactivar' : 'Activar') + '</option>');
      }

      url = base_url + 'HelperController/getValoresTablaDato';
      $.post( url, {sTipoData : 'Tipo_Rubro_Empresa'}, function( responseRubrosEmpresa ){
        if ( responseRubrosEmpresa.sStatus == 'success' ) {
          var iTotalRegistros = responseRubrosEmpresa.arrData.length, responseRubrosEmpresa=responseRubrosEmpresa.arrData;
          $( '#cbo-tipo_rubro_empresa' ).html( '<option value="0" selected="selected">- Todos -</option>');
          for (var i = 0; i < iTotalRegistros; i++) {
            selected = '';
            if(response.Nu_Tipo_Rubro_Empresa == responseRubrosEmpresa[i].Nu_Valor)
              selected = 'selected="selected"';
            $( '#cbo-tipo_rubro_empresa' ).append( '<option value="' + responseRubrosEmpresa[i].Nu_Valor + '" ' + selected + '>' + responseRubrosEmpresa[i].No_Descripcion + '</option>' );
          }
        } else {
          $( '#cbo-tipo_rubro_empresa' ).html( '<option value="0" selected="selected">- Vacío -</option>');
          console.log( response );
        }
        $( '#modal-loader' ).modal('hide');
      }, 'JSON');

      $( '[name="Nu_Verificar_Autorizacion_Venta"]' ).val(response.Nu_Verificar_Autorizacion_Venta);

      $( '#cbo-arqueo_punto_venta' ).html( '' );
      for (var i = 1; i < 3; i++){
        selected = '';
        if(response.Nu_Imprimir_Liquidacion_Caja == i)
          selected = 'selected="selected"';
        $( '#cbo-arqueo_punto_venta' ).append( '<option value="' + i + '" ' + selected + '>' + (i == 1 ? 'Categoría' : 'Producto') + '</option>' );
      }

      $( '#cbo-activar_ticket_linea_detalle' ).html( '' );
      for (var i = 0; i < 2; i++){
        selected = '';
        if (response.Nu_Activar_Detalle_Una_Linea_Ticket == i)
          selected = 'selected="selected"';
        $('#cbo-activar_ticket_linea_detalle').append('<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Desactivar' : 'Activar') + '</option>' );
      }
      
      $('#div-predeterminar_cliente_varios_venta').hide();

      $('#cbo-predeterminar_tipo_documento_venta').html('');
      var arrTipoDocumento = [
        ['4', 'Boleta'],
        ['3', 'Factura'],
        ['2', 'Nota de Venta'],
      ];
      for (var i = 0; i < arrTipoDocumento.length; i++) {
        selected = '';
        if (arrTipoDocumento[i][0] == response.Nu_ID_Tipo_Documento_Venta_Predeterminado)
          selected = 'selected="selected"';
        $('#cbo-predeterminar_tipo_documento_venta').append('<option value="' + arrTipoDocumento[i][0] + '" ' + selected + '>' + arrTipoDocumento[i][1] + '</option>');
      }
      
      $('#cbo-predeterminar_cliente_varios_venta').html('');
      for (var i = 0; i < 2; i++) {
        selected = '';
        if (response.Nu_Cliente_Varios_Venta_Predeterminado == i)
          selected = 'selected="selected"';

        $('#cbo-predeterminar_cliente_varios_venta').append('<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Desactivar' : 'Activar') + '</option>');
      }

      $('#cbo-tipo_lenguaje_impresion_pos').html('');
      for (var i = 1; i < 3; i++) {
        selected = '';
        if (response.Nu_Tipo_Lenguaje_Impresion_Pos == i)
          selected = 'selected="selected"';
        $('#cbo-tipo_lenguaje_impresion_pos').append('<option value="' + i + '" ' + selected + '>' + (i == 1 ? 'HTML' : 'PDF') + '</option>');
      }

      $('#cbo-activar_descuento_punto_venta').html('');
      for (var i = 0; i < 2; i++) {
        selected = '';
        if (response.Nu_Activar_Descuento_Punto_Venta == i)
          selected = 'selected="selected"';
        $('#cbo-activar_descuento_punto_venta').append('<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Desactivar' : 'Activar') + '</option>');
      }

      $( '#cbo-autorizacion_punto_venta' ).html( '' );
      for (var i = 0; i < 2; i++){
        selected = '';
        if(response.Nu_Verificar_Autorizacion_Venta == i)
          selected = 'selected="selected"';
        $( '#cbo-autorizacion_punto_venta' ).append( '<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'No' : 'Si') + '</option>' );
      }

      $( '.div-Estado' ).show();
      $( '#cbo-Estado' ).html( '' );
      for (var i = 0; i < 2; i++){
        selected = '';
        if(response.Nu_Estado == i)
          selected = 'selected="selected"';
        $( '#cbo-Estado' ).append( '<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Inactivo' : 'Activo') + '</option>' );
      }

      $('[name="Ss_Total_Pago_Cliente_Servicio"]').val(response.Ss_Total_Pago_Cliente_Servicio);

      $( '[name="Txt_Token"]' ).val(response.Txt_Token);
      
      $( '[name="ENo_Foto_Boleta"]' ).val(response.No_Foto_Boleta);
      $( '[name="ENo_Foto_Factura"]' ).val(response.No_Foto_Factura);
      $( '[name="ENo_Foto_NCredito"]' ).val(response.No_Foto_NCredito);
      $( '[name="ENo_Foto_Guia"]' ).val(response.No_Foto_Guia);
      
      $( '#upload-file-info_boleta' ).text(response.No_Foto_Boleta);
      $( '#upload-file-info_factura' ).text(response.No_Foto_Factura);
      $( '#upload-file-info_ncredito' ).text(response.No_Foto_NCredito);
      $( '#upload-file-info_guia' ).text(response.No_Foto_Guia);
      
      $( '[name="No_Dominio_Empresa"]' ).val(response.No_Dominio_Empresa);
      $( '[name="Txt_Email_Empresa"]' ).val(response.Txt_Email_Empresa);
      $( '[name="Nu_Celular_Empresa"]' ).val(response.Nu_Celular_Empresa);
      $( '[name="Nu_Telefono_Empresa"]' ).val(response.Nu_Telefono_Empresa);
      $( '[name="Txt_Slogan_Empresa"]' ).val(response.Txt_Slogan_Empresa);

      if (response.Txt_Terminos_Condiciones_Ticket != null)
        $( '[name="Txt_Terminos_Condiciones_Ticket"]' ).val( clearHTMLTextArea(response.Txt_Terminos_Condiciones_Ticket) );
      if (response.Txt_Terminos_Condiciones != null)
        $( '[name="Txt_Terminos_Condiciones"]' ).val( clearHTMLTextArea(response.Txt_Terminos_Condiciones) );
      if (response.Txt_Cuentas_Bancarias != null)
        $( '[name="Txt_Cuentas_Bancarias"]' ).val( clearHTMLTextArea(response.Txt_Cuentas_Bancarias) );
      if (response.Txt_Nota != null)
        $( '[name="Txt_Nota"]' ).val( clearHTMLTextArea(response.Txt_Nota) );
      if (response.Txt_Cuenta_Banco_Detraccion != null)
        $( '[name="Txt_Cuenta_Banco_Detraccion"]' ).val( clearHTMLTextArea(response.Txt_Cuenta_Banco_Detraccion) );

      //Formato PDF
      var arrFormatoPDF = [
        { "No_Formato_PDF": "TICKET" },
        { "No_Formato_PDF": "A4" },
      ];
      $('#cbo-formato_impresion_pdf').html('');
      for (var i = 0; i < arrFormatoPDF.length; i++) {
        selected = '';
        if (response.No_Predeterminado_Formato_PDF_POS == arrFormatoPDF[i]['No_Formato_PDF'])
          selected = 'selected="selected"';
        $('#cbo-formato_impresion_pdf').append('<option value="' + arrFormatoPDF[i]['No_Formato_PDF'] + '" ' + selected + '>' + arrFormatoPDF[i]['No_Formato_PDF'] + '</option>');
      }

      //Imprimir columna ticket detalle disenio
      var arrImprimirTicketDetalleVista = [
        { "id": "0", "valor" : "Nombre" },
        { "id": "1", "valor" : "Codigo + Nombre" },
        { "id": "2", "valor" : "Codigo + Nombre + Marca" },
      ];
      $('#cbo-imprimir_columna_ticket_detalle').html('');
      for (var i = 0; i < arrImprimirTicketDetalleVista.length; i++) {
        selected = '';
        if (response.Nu_Imprimir_Columna_Ticket_Detalle  == arrImprimirTicketDetalleVista[i]['id'])
          selected = 'selected="selected"';
        $('#cbo-imprimir_columna_ticket_detalle').append('<option value="' + arrImprimirTicketDetalleVista[i]['id'] + '" ' + selected + '>' + arrImprimirTicketDetalleVista[i]['valor'] + '</option>');
      }

      //Imprimir columna ticket detalle disenio
      var arrTipoVenderUsuarioPos = [
        { "id": "1", "valor" : "Cajero" },
        { "id": "2", "valor" : "Cajero + Vendedor" }
      ];
      $('#cbo-Nu_Tipo_Vender_Usuario_POS').html('');
      for (var i = 0; i < arrTipoVenderUsuarioPos.length; i++) {
        selected = '';
        if (response.Nu_Tipo_Vender_Usuario_POS  == arrTipoVenderUsuarioPos[i]['id'])
          selected = 'selected="selected"';
        $('#cbo-Nu_Tipo_Vender_Usuario_POS').append('<option value="' + arrTipoVenderUsuarioPos[i]['id'] + '" ' + selected + '>' + arrTipoVenderUsuarioPos[i]['valor'] + '</option>');
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
    
  url = base_url + 'Configuracion/SistemaController/uploadOnly/' + ID;
  var myDropzone = new Dropzone("#id-divDropzone", {
    url: url,
    params: {
      iVersionImage: (parseInt(Nu_Version_Imagen) + 1),
      iIdConfiguracion: ID,
    },
    acceptedFiles: ".jpeg,.jpg,.png",
    addRemoveLinks: true,
    uploadMultiple: false,
    maxFilesize: 1,//Peso en MB
    maxFiles: 1,
    thumbnailHeight: 200,
    thumbnailWidth: 200,
    parallelUploads: 1,
    thumbnail: function(file, dataUrl) {
      if (file.previewElement) {
        file.previewElement.classList.remove("dz-file-preview");
        var images = file.previewElement.querySelectorAll("[data-dz-thumbnail]");
        for (var i = 0; i < images.length; i++) {
          var thumbnailElement = images[i];
          thumbnailElement.alt = file.name;
          thumbnailElement.src = dataUrl;
        }
        setTimeout(function() { file.previewElement.classList.add("dz-image-preview"); }, 1);
      }
    },
    removedfile: function(file){
      url = base_url + 'Configuracion/SistemaController/removeFileImage';
      $.ajax({
        url : url,
        type: "POST",
        dataType: "JSON",
        data: { nameFileImage: file.name},
      })
      var previewElement;
      return (previewElement = file.previewElement) != null ? (previewElement.parentNode.removeChild(file.previewElement)) : (void 0);
    },
    init : function() {
      //Verificar respuesta del servidor al subir archivo
      this.on("success", function(file, response) {
        var response = jQuery.parseJSON(response);
        
        $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
        $( '#modal-message' ).modal('show');
        
        if (response.sStatus != 'error'){
          $( '.modal-message' ).addClass(response.sClassModal);
          $( '.modal-title-message' ).text(response.sMessage);
          
          $( '[name="hidden-nombre_logo"]' ).val( response.sNombreImagenLogoEmpresa );
          $( '#hidden-nombre_imagen_logo_empresa' ).val( response.sNombreImagenLogoEmpresaUrl );
          
          setTimeout(function() {$('#modal-message').modal('hide'); }, 1100);
        } else {
          $( '.modal-message' ).addClass(response.sClassModal);
          $( '.modal-title-message' ).text(response.sMessage);
          setTimeout(function() {$('#modal-message').modal('hide');}, 1200);
        }
      })

      if (No_Imagen_Logo_Empresa.length > 0 && No_Imagen_Logo_Empresa != '' && No_Imagen_Logo_Empresa !== undefined) {
        var me = this;
        url = base_url + 'Configuracion/SistemaController/get_image';
        var arrPost={
          'sNombreImage': No_Logo_Empresa,
          'sUrlImage': No_Imagen_Logo_Empresa,
        }
        $.post(url, arrPost, function(response){
          $.each(response, function(key, value){
            var mockfile = value;
            me.emit("addedfile", mockfile);
            me.emit("thumbnail", mockfile, No_Imagen_Logo_Empresa);
            me.emit("complete", mockfile);
          })
        }, 'json');
      }
    }
  })
}

function form_Sistema(){
  if ( $( '#cbo-Empresas' ).val() == 0){
    $( '#cbo-Empresas' ).closest('.form-group').find('.help-block').html('Seleccionar empresa');
    $( '#cbo-Empresas' ).closest('.form-group').removeClass('has-success').addClass('has-error');
  } else {
    $( '#btn-save' ).text('');
    $( '#btn-save' ).attr('disabled', true);
    $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
  
    $( '#modal-loader' ).modal('show');
    
    var formData = new FormData($('#form-Sistema')[0]);
    
    url = base_url + 'Configuracion/SistemaController/crudSistema';
  	$.ajax({
      type		    : 'POST',
      dataType	  : 'JSON',
  		url		      : url,
  		data		    : formData,
      mimeType    : "multipart/form-data",
      contentType : false,
      cache       : false,
      processData : false,
  		success : function( response ){
  		  $( '#modal-loader' ).modal('hide');        
  	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
    	  $( '#modal-message' ).modal('show');
  		  
  		  if (response.status == 'success'){
          $( '#form-Sistema' )[0].reset();
          $( '.div-AgregarEditar' ).hide();
          $( '.div-Listar' ).show();
    	    $( '.modal-message' ).addClass(response.style_modal);
    	    $( '.modal-title-message' ).text(response.message);
    	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
    	    reload_table_sistema();
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
  }
}

function eliminarSistema(ID_Empresa, ID){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });
  
  $( '#btn-save-delete' ).off('click').click(function () {
    $( '#modal-loader' ).modal('show');
    
    url = base_url + 'Configuracion/SistemaController/eliminarSistema/' + ID_Empresa + '/' + ID;
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
    	    reload_table_sistema();
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

function reload_table_sistema(){
  table_sistema.ajax.reload(null,false);
}