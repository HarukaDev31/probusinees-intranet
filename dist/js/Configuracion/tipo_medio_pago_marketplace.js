var url;
var table_medio_pago;

$(function () {
  $('.select2').select2();
  
	$(document).keyup(function(event){
    if(event.which == 27){//ESC
      $( "#modal-TipoMedioPagoMarketplace" ).modal('hide');
    }
  });

  url = base_url + 'Configuracion/TipoMedioPagoMarketplaceController/ajax_list';
  table_medio_pago = $( '#table-TipoMedioPagoMarketplace' ).DataTable({
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
      'dataType'  : 'JSON',
      'data'      : function ( data ) {
        data.filtro_empresa = $( '#cbo-filtro_empresa' ).val(),
        data.Filtros_TipoMedioPagoMarketplace = $( '#cbo-Filtros_TipoMedioPagoMarketplace' ).val(),
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
    table_medio_pago.search($(this).val()).draw();
  });
  
  $( '#form-TipoMedioPagoMarketplace' ).validate({
		rules:{
			ID_Medio_Pago_Marketplace: {
				required: true
			},
			No_Tipo_Medio_Pago_Marketplace: {
				required: true
			},
		},
		messages:{
			ID_Medio_Pago_Marketplace:{
				required: "Seleccionar medio pago",
			},
			No_Tipo_Medio_Pago_Marketplace:{
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
		submitHandler: form_TipoMedioPagoMarketplace
  });
  
  $( '#cbo-filtro_empresa' ).html('<option value="0" selected="selected">- Todas -</option>');

  url = base_url + 'HelperController/getEmpresas';
  $.post( url , function( response ){
    $( '#cbo-filtro_empresa' ).html('<option value="0" selected="selected">- Todas -</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-filtro_empresa' ).append( '<option value="' + response[i].ID_Empresa + '">' + response[i].No_Empresa + '</option>' );
  }, 'JSON');
  
	$( '#cbo-filtro_empresa' ).change(function(){
    table_medio_pago.search($(this).val()).draw();
  });

	$( '#cbo-Empresas' ).change(function(){
	  $( '#cbo-medio_pago' ).html('');
	  if ( $(this).val() > 0 ) {
      url = base_url + 'HelperController/getMediosPagoMarketplace';
      var arrPost = {
        iIdEmpresa : $( this ).val(),
      };
      $.post( url, arrPost, function( response ){
        $( '#cbo-medio_pago' ).html('<option value="" selected="selected">- Seleccionar -</option>');
        for (var i = 0; i < response.length; i++)
          $( '#cbo-medio_pago' ).append( '<option value="' + response[i].ID_Medio_Pago_Marketplace + '">' + response[i].No_Medio_Pago_Marketplace + '</option>' );
      }, 'JSON');
	  }
	})
})

function agregarTipoMedioPagoMarketplace(){
  $( '#form-TipoMedioPagoMarketplace' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  
  $( '.help-block' ).empty();
  
  $( '#modal-TipoMedioPagoMarketplace' ).modal('show');
  
  $( '.modal-title' ).text('Nuevo Tipo Medio Pago');
  
  $( '[name="EID_Medio_Pago_Marketplace"]' ).val('');
  $( '[name="EID_Tipo_Medio_Pago_Marketplace"]' ).val('');
  $( '[name="ENo_Tipo_Medio_Pago_Marketplace"]' ).val('');

  $( '#modal-loader' ).modal('show');
  url = base_url + 'HelperController/getEmpresas';
  $.post( url , function( response ){
    $( '#cbo-Empresas' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-Empresas' ).append( '<option value="' + response[i].ID_Empresa + '">' + response[i].No_Empresa + '</option>' );
    $( '#modal-loader' ).modal('hide');
  }, 'JSON');

  $( '#modal-loader' ).modal('show');
  url = base_url + 'HelperController/getMediosPagoMarketplace';
  var arrPost = {
    iIdEmpresa : $( '#cbo-Empresas' ).val(),
  };
  $.post( url, arrPost, function( response ){
    $( '#cbo-medio_pago' ).html('<option value="" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-medio_pago' ).append( '<option value="' + response[i].ID_Medio_Pago_Marketplace + '">' + response[i].No_Medio_Pago_Marketplace + '</option>' );
    $( '#modal-loader' ).modal('hide');
  }, 'JSON');
  
  $( '.div-Estado' ).hide();
  $( '#cbo-Estado' ).html( '<option value="1">Activo</option>' );
  
  /* obtener imagen guardada(s) */
  $( '.divDropzone' ).html(
    '<div id="id-divDropzone" class="dropzone div-dropzone">'
      +'<div class="dz-message">'
        +'Arrastrar o presionar click para subir imágen(es)'
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
  
  url = base_url + 'Configuracion/TipoMedioPagoMarketplaceController/uploadOnly';
  var myDropzone = new Dropzone("#id-divDropzone", {
    url : url,
    acceptedFiles: ".png",
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
      url = base_url + 'Configuracion/TipoMedioPagoMarketplaceController/removeFileImage';
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

          $( '[name="No_Imagen_Tipo_Medio_Pago_Marketplace"]' ).val( response.sNombreImagen );
          $( '[name="No_Imagen_Url_Tipo_Medio_Pago_Marketplace"]' ).val( response.sNombreImagenUrl );

          setTimeout(function() {$('#modal-message').modal('hide'); }, 1100);
        } else {
          $( '.modal-message' ).addClass(response.sClassModal);
          $( '.modal-title-message' ).text(response.sMessage);
          setTimeout(function() {$('#modal-message').modal('hide');}, 1200);
        }
      })
    },
  })
}

function verTipoMedioPagoMarketplace(ID, No_Imagen_Tipo_Medio_Pago_Marketplace, No_Imagen_Url_Tipo_Medio_Pago_Marketplace){
  $( '#form-TipoMedioPagoMarketplace' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '#modal-loader' ).modal('show');
 
  url = base_url + 'Configuracion/TipoMedioPagoMarketplaceController/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $( '#modal-loader' ).modal('hide');
      
      $( '#modal-TipoMedioPagoMarketplace' ).modal('show');
      $( '.modal-title' ).text('Modificar Tipo Medio Pago');
      
      $( '[name="EID_Medio_Pago_Marketplace"]' ).val(response.ID_Medio_Pago_Marketplace);
      $( '[name="EID_Tipo_Medio_Pago_Marketplace"]' ).val(response.ID_Tipo_Medio_Pago_Marketplace);
      $( '[name="ENo_Tipo_Medio_Pago_Marketplace"]' ).val(response.No_Tipo_Medio_Pago_Marketplace);
      $( '[name="No_Imagen_Tipo_Medio_Pago_Marketplace"]' ).val(response.No_Imagen_Tipo_Medio_Pago_Marketplace);
      $( '[name="No_Imagen_Url_Tipo_Medio_Pago_Marketplace"]' ).val(response.No_Imagen_Url_Tipo_Medio_Pago_Marketplace);
      
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

      url = base_url + 'HelperController/getMediosPagoMarketplace';
      var arrPost = {
        iIdEmpresa : response.ID_Empresa,
      };
      $.post( url, arrPost, function( responseMedioPago ){
        $( '#cbo-medio_pago' ).html('');
        for (var i = 0; i < responseMedioPago.length; i++){
          selected = '';
          if(response.ID_Medio_Pago_Marketplace == responseMedioPago[i].ID_Medio_Pago_Marketplace)
            selected = 'selected="selected"';
          $( '#cbo-medio_pago' ).append( '<option value="' + responseMedioPago[i].ID_Medio_Pago_Marketplace + '" ' + selected + '>' + responseMedioPago[i].No_Medio_Pago_Marketplace + '</option>' );
        }
      }, 'JSON');

      $( '[name="No_Tipo_Medio_Pago_Marketplace"]' ).val(response.No_Tipo_Medio_Pago_Marketplace);
      
      $( '.div-Estado' ).show();
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
  
  /* obtener imagen guardada(s) */
  $( '.divDropzone' ).html(
    '<div id="id-divDropzone" class="dropzone div-dropzone">'
      +'<div class="dz-message">'
        +'Arrastrar o presionar click para subir imágen(es)'
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
  
  url = base_url + 'Configuracion/TipoMedioPagoMarketplaceController/uploadOnly';
  var myDropzone = new Dropzone("#id-divDropzone", {
    url : url,
    acceptedFiles: ".png",
    addRemoveLinks: true,
    uploadMultiple: false,
    maxFilesize: 1,//Peso en MB
    maxFiles: 2,
    thumbnailHeight: 200,
    thumbnailWidth: 200,
    parallelUploads: 2,
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
      var arrName = file.name.split('/');
      var nameFileImage;
      if (arrName.length === 6)//Si la imagen ya está en el server
        nameFileImage = arrName[5];
      else//Si la imagén recién la vamos a subir y no existe en el server
        nameFileImage = arrName[0];
      url = base_url + 'Configuracion/TipoMedioPagoMarketplaceController/removeFileImage';
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
          
          $( '[name="No_Imagen_Tipo_Medio_Pago_Marketplace"]' ).val( response.sNombreImagen );
          $( '[name="No_Imagen_Url_Tipo_Medio_Pago_Marketplace"]' ).val( response.sNombreImagenUrl );
          
          setTimeout(function() {$('#modal-message').modal('hide'); }, 1100);
        } else {
          $( '.modal-message' ).addClass(response.sClassModal);
          $( '.modal-title-message' ).text(response.sMessage);
          setTimeout(function() {$('#modal-message').modal('hide');}, 1200);
        }
      })
      
      var me = this;
      url = base_url + 'Configuracion/TipoMedioPagoMarketplaceController/get_image';
      var arrPost={
        'sNombreImage': No_Imagen_Tipo_Medio_Pago_Marketplace,
        'sUrlImage': No_Imagen_Url_Tipo_Medio_Pago_Marketplace,
      }
      $.post(url, arrPost, function(response){
        $.each(response, function(key, value){
          var mockfile = value;
          me.emit("addedfile", mockfile);
          me.emit("thumbnail", mockfile, No_Imagen_Url_Tipo_Medio_Pago_Marketplace);
          me.emit("complete", mockfile);
        })
      }, 'json');
    }
  })
}

function form_TipoMedioPagoMarketplace(){
  $( '#btn-save' ).text('');
  $( '#btn-save' ).attr('disabled', true);
  $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

  $( '#modal-loader' ).modal('show');
  
  url = base_url + 'Configuracion/TipoMedioPagoMarketplaceController/crudTipoMedioPagoMarketplace';
	$.ajax({
    type		  : 'POST',
    dataType	: 'JSON',
		url		    : url,
		data		  : $('#form-TipoMedioPagoMarketplace').serialize(),
		success : function( response ){
		  $( '#modal-loader' ).modal('hide');
		  
	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
  	  $( '#modal-message' ).modal('show');
		  
		  if (response.status == 'success'){
		    $('#modal-TipoMedioPagoMarketplace').modal('hide');
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
  	    reload_table_medio_pago();
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

function eliminarTipoMedioPagoMarketplace(ID){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });
  
  $( '#btn-save-delete' ).off('click').click(function () {
    $( '#modal-loader' ).modal('show');
    
    url = base_url + 'Configuracion/TipoMedioPagoMarketplaceController/eliminarTipoMedioPagoMarketplace/' + ID;
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
    	    reload_table_medio_pago();
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

function reload_table_medio_pago(){
  table_medio_pago.ajax.reload(null,false);
}