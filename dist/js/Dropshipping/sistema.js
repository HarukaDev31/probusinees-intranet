var url;
var table_sistema;

$(function () {
  $('.select2').select2();
  $('[data-mask]').inputmask();
  
	$(".toggle-password").click(function() {
		$(this).toggleClass("fa-eye fa-eye-slash");
    var $pwd = $(".pwd");
    if ($pwd.attr('type') == 'password') {
      $pwd.attr('type', 'text');
    } else {
      $pwd.attr('type', 'password');
    }
  });

  url = base_url + 'Dropshipping/TiendaDropshippingController/ajax_list';
  table_sistema = $('#table-Sistema').DataTable({
    'dom': '<"top">frt<"bottom"l><"clear">',
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
  
//   $.validator.addMethod("RemoteTienda",   function(value, element) {
//     var formData        = new FormData();
//     // let DominioNuevo    = value.toLowerCase()+".compramaz.com";
//     // let DominioActual   = $(element).data("DominioActual");

//     // if(DominioNuevo==DominioActual)
//     //   return this.optional(element) || false;
    
//     //  formData.append("No_Subdominio_Tienda_Virtual", value);

//     // const response = await fetch( base_url + 'Dropshipping/TiendaDropshippingController/ValidarDominioTienda', {
//     //   method: 'POST', 
//     //   body: formData 
//     // });
//     // console.log(response);
//     return "pending";
// }, "Subominio ya esta en uso");


  $( '#form-Sistema' ).validate({
    rules: {
      No_Tienda_Lae_Shop: {
        required: true,
      },
      Nu_Celular_Whatsapp_Lae_Shop: {
        required: true,
      },
			Txt_Email_Lae_Shop:{
        validemail: true,
        required: true,
			},
      No_Subdominio_Tienda_Virtual: {
       
        required: true,
        remote: {
            url: base_url + 'Dropshipping/TiendaDropshippingController/ValidarDominioTienda',
            type: "post",
            beforeSend:function(){
              // $('[name="No_Subdominio_Tienda_Virtual"]').parent().addClass("has-jony");
              // $("#error").html("enviando");
            },
            data: {
              t: function() {
                return  $('[name="No_Subdominio_Tienda_Virtual"]').data( "ID_Subdominio_Tienda_Virtual");
              }
            },
        }

      },
		},
    messages: {
      No_Tienda_Lae_Shop:{
        required: "Ingresar Nombre",
      },
      Nu_Celular_Whatsapp_Lae_Shop: {
        required: "Ingresar WhatsApp",
      },
      Txt_Email_Lae_Shop: {
        validemail: "Ingresar correo válido",
        required: "Ingresar correo",
      },
      No_Subdominio_Tienda_Virtual: {
        required: "Ingresar subdominio",
        remote: "Subominio ya esta en uso"
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
    // submitHandler: function(){
    //   alert("ok");
    // }
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

  //CSS
  $('#cbo-color').change(function () {
    $(".background").css("background-color", "#" + $(this).val());
  })
})

function verSistema(ID, No_Imagen_Logo_Empresa, Nu_Version_Imagen){
  $( '#form-Sistema' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '.div-Listar' ).hide();
  $( '.div-AgregarEditar' ).show();
  
  $( '#modal-loader' ).modal('show');
   
  url = base_url + 'Dropshipping/TiendaDropshippingController/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $('[name="EID_Configuracion"]').val(response.ID_Configuracion);
      
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
      
      $('[name="Txt_Url_Logo_Lae_Shop"]').val(response.Txt_Url_Logo_Lae_Shop);

      $('[name="No_Tienda_Lae_Shop"]').val(response.No_Tienda_Lae_Shop);
      $('[name="Nu_Celular_Lae_Shop"]').val(response.Nu_Celular_Lae_Shop);
      $('[name="Nu_Celular_Whatsapp_Lae_Shop"]').val(response.Nu_Celular_Whatsapp_Lae_Shop);
      $('[name="Txt_Email_Lae_Shop"]').val(response.Txt_Email_Lae_Shop);
      $('[name="Txt_Descripcion_Lae_Shop"]').val(response.Txt_Descripcion_Lae_Shop);

      $(".background").css("background-color", "#" + response.No_Html_Color_Lae_Shop);
      
      selected = '';
      if (response.No_Html_Color_Lae_Shop == '766df4')
        selected = 'selected="selected"';
      $('#cbo-color').html('<option value="766df4" ' + selected + '>Púrpura</option>');

      selected = '';
      if (response.No_Html_Color_Lae_Shop == '7B39FF')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="7B39FF" ' + selected + '>Morado</option>');

      selected = '';
      if (response.No_Html_Color_Lae_Shop == '1A7FDC')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="1A7FDC" ' + selected + '>Celeste</option>');

      selected = '';
      if (response.No_Html_Color_Lae_Shop == '1B61A1')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="1B61A1" ' + selected + '>Azul</option>');

      selected = '';
      if (response.No_Html_Color_Lae_Shop == '227E52')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="227E52" ' + selected + '>Verde</option>');

      selected = '';
      if (response.No_Html_Color_Lae_Shop == '6D7A6A')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="6D7A6A" ' + selected + '>Verde grisáceo</option>');

      selected = '';
      if (response.No_Html_Color_Lae_Shop == 'ED5702')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="ED5702" ' + selected + '>Naranja</option>');

      selected = '';
      if (response.No_Html_Color_Lae_Shop == 'DE063A')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="DE063A" ' + selected + '>Rojo</option>');

      selected = '';
      if (response.No_Html_Color_Lae_Shop == '950919')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="950919" ' + selected + '>Guinda</option>');

      selected = '';
      if (response.No_Html_Color_Lae_Shop == 'DE287F')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="DE287F" ' + selected + '>Fucsia</option>');

      selected = '';
      if (response.No_Html_Color_Lae_Shop == 'FF3048')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="FF3048" ' + selected + '>Rosado</option>');

      selected = '';
      if (response.No_Html_Color_Lae_Shop == 'ffe930')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="ffe930" ' + selected + '>Amarillo</option>');

      selected = '';
      if (response.No_Html_Color_Lae_Shop == '2D2C2C')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="2D2C2C" ' + selected + '>Negro</option>');

      selected = '';
      if (response.No_Html_Color_Lae_Shop == '6C6B6C')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="6C6B6C" ' + selected + '>Gris</option>');

      selected = '';
      if (response.No_Html_Color_Lae_Shop == '4e342e')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="4e342e" ' + selected + '>Marron</option>');

      // DATOS ADICIONALES
      $('[name="No_Subdominio_Tienda_Virtual"]').val(response.No_Subdominio_Tienda_Virtual)
      .data( "ID_Subdominio_Tienda_Virtual", response.ID_Subdominio_Tienda_Virtual )
      .data( "DominioActual", response.DominioActual );
    
      $('#cbo-activar_stock').html('');
      for (var i = 0; i < 2; i++) {
        selected = '';
        if (response.Nu_Validar_Stock_Laeshop == i)
          selected = 'selected="selected"';
        $('#cbo-activar_stock').append('<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Desactivar' : 'Activar') + '</option>');
      }

      $('#cbo-precio_centralizado_laeshop').html('');
      for (var i = 0; i < 2; i++) {
        selected = '';
        if (response.Nu_Activar_Precio_Centralizado_Laeshop == i)
          selected = 'selected="selected"';
        $('#cbo-precio_centralizado_laeshop').append('<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Desactivar' : 'Activar') + '</option>');
      }

      $('#cbo-emitir_factura').html('');
      for (var i = 0; i < 2; i++) {
        selected = '';
        if (response.Nu_Activar_Emitir_Factura_Laeshop == i)
          selected = 'selected="selected"';
        $('#cbo-emitir_factura').append('<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Desactivar' : 'Activar') + '</option>');
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
    
  url = base_url + 'Dropshipping/TiendaDropshippingController/uploadOnly/' + ID;
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
      url = base_url + 'Dropshipping/TiendaDropshippingController/removeFileImage';
      $.ajax({
        url: url,
        type: "POST",
        dataType: "JSON",
        data: { iIdProducto: ID, nameFileImage: file.name },
        success: function (response) {
          $('.modal-message').removeClass('modal-danger modal-warning modal-success');
          $('#modal-message').modal('show');

          if (response.status == 'success') {
            $('.modal-message').addClass(response.style_modal);
            $('.modal-title-message').text(response.message);
            setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
            $('#hidden-nombre_imagen_logo_empresa').val('');
          } else {
            $('.modal-message').addClass(response.style_modal);
            $('.modal-title-message').text(response.message);
            setTimeout(function () { $('#modal-message').modal('hide'); }, 1200);
          }
        },
        error: function (jqXHR, textStatus, errorThrown) {
          $('.modal-message').removeClass('modal-danger modal-warning modal-success');
          $('#modal-message').modal('show');
          $('.modal-message').addClass('modal-danger');
          $('.modal-title-message').text(textStatus + ' [' + jqXHR.status + ']: ' + errorThrown);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1700);

          //Message for developer
          console.log(jqXHR.responseText);
        }
      })
      var previewElement;
      return (previewElement = file.previewElement) != null ? (previewElement.parentNode.removeChild(file.previewElement)) : (void 0);
    },
    init: function () {
      console.log('entro');
      //Verificar respuesta del servidor al subir archivo
      this.on("success", function (file, response) {
        var response = jQuery.parseJSON(response);

        $('.modal-message').removeClass('modal-danger modal-warning modal-success');
        $('#modal-message').modal('show');

        if (response.sStatus != 'error') {
          $('.modal-message').addClass(response.sClassModal);
          $('.modal-title-message').text(response.sMessage);

          $('#hidden-nombre_imagen_logo_empresa').val(response.sNombreImagenCategoriaUrl);

          setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
        } else {
          $('.modal-message').addClass(response.sClassModal);
          $('.modal-title-message').text(response.sMessage);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 2100);
        }
      })

      if (No_Imagen_Logo_Empresa.length > 0 && No_Imagen_Logo_Empresa != '' && No_Imagen_Logo_Empresa !== undefined) {
        var me = this;
        url = base_url + 'Dropshipping/TiendaDropshippingController/get_image';
        var arrPost = {
          'sUrlImage': No_Imagen_Logo_Empresa,
        }
        $.post(url, arrPost, function (response) {
          $.each(response, function (key, value) {
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
    
    url = base_url + 'Dropshipping/TiendaDropshippingController/crudSistema';
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
        $( '#btn-save' ).append( '<span class="fa fa-save"></span> Guardar (ENTER)' );
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
    
    url = base_url + 'Dropshipping/TiendaDropshippingController/eliminarSistema/' + ID_Empresa + '/' + ID;
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