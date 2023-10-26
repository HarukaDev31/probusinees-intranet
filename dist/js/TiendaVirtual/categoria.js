var url;
var table_familia;
var accion_familia;

$(function () {
	$(document).keyup(function(event){
    if(event.which == 27){//ESC
      $( "#modal-Categoria" ).modal('hide');
    }
	});


  $("#table-Categoria").on('click', '.img-fluid', function () {
    //$('.img-fluid').data('url_img');
    $('.img-responsive').attr('src', '');
    $('.modal-info_item').modal('show');
    $('.img-responsive').attr('src', $(this).data('url_img'));
  })

  $('.select2').select2();
  $('[data-mask]').inputmask();
  
  url = base_url + 'TiendaVirtual/CategoriasTiendaVirtualController/ajax_list';
  table_familia = $( '#table-Categoria' ).DataTable({
    'dom': '<"top">frt<"bottom"lip><"clear">',
    'searching'   : false,
    'bStateSave'  : true,
    'processing'  : true,
    'serverSide'  : true,
    'info'        : true,
    'autoWidth'   : false,
    'pagingType'  : 'full_numbers',
    'oLanguage' : {
      'sInfo'               : 'Mostrando (_START_ - _END_) total de registros _TOTAL_',
      'sLengthMenu'         : '_MENU_',
      'sSearch'             : 'Buscar por: ',
      'sSearchPlaceholder'  : 'UPC / Nombre',
      'sZeroRecords'        : 'No se encontraron registros',
      'sInfoEmpty'          : 'No hay registros',
      'sLoadingRecords'     : 'Cargando...',
      'sProcessing'         : 'Procesando...',
      'oPaginate'           : {
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
        data.Filtros_Categorias = $( '#cbo-Filtros_Categorias' ).val(),
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
    table_familia.search($(this).val()).draw();
  });
  
  $( '#form-Categoria' ).validate({
		rules:{
			No_Familia: {
				required: true,
			},
		},
		messages:{
			No_Familia:{
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
		submitHandler: form_Categoria
	});
	
  $(document).bind('keydown', 'f2', function(){
    agregarCategoria();
  });
})

function agregarCategoria(){
  accion_familia='add_familia';
  
  $( '#modal-loader' ).modal('show');
  
  $( '#form-Categoria' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '#modal-Categoria' ).modal('show');
  $( '.modal-title' ).text('Nueva Categoría');
  
  $('[name="EID_Empresa"]').val('');
  $('[name="EID_Familia"]').val('');
  $('[name="ENo_Familia"]').val('');
  $('[name="ENu_Orden"]').val('');
  $('[name="No_Imagen_Categoria"]').val('');
  $('[name="No_Imagen_Url_Categoria"]').val('');
  
	$( '#modal-Categoria' ).on('shown.bs.modal', function() {
		$( '#txt-No_Familia' ).focus();
	})
  
  $( '#modal-loader' ).modal('show');
  url = base_url + 'HelperController/getEmpresas';
  $.post( url , function( response ){
    $( '#cbo-Empresas' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-Empresas' ).append( '<option value="' + response[i].ID_Empresa + '">' + response[i].No_Empresa + '</option>' );
    $( '#modal-loader' ).modal('hide');
  }, 'JSON');

  $('.div-Estado').hide();
  $('#cbo-Estado').html('<option value="1">Activo</option>');

  $('#cbo-color').html('<option value="141619">Negro</option>' );
  $('#cbo-color').append('<option value="0d6efd">Azul</option>');
  $('#cbo-color').append('<option value="198754">Verde</option>');
  $('#cbo-color').append('<option value="dc3545">Rojo</option>');
  $('#cbo-color').append('<option value="ffff00">Amarillo</option>');
  $('#cbo-color').append('<option value="0dcaf0">Celeste</option>');
  $('#cbo-color').append('<option value="ff9100">Naranja</option>');
  $('#cbo-color').append('<option value="7c4dff">Morado</option>');
  $('#cbo-color').append('<option value="ff4081">Rosado</option>');
  $('#cbo-color').append('<option value="4e342e">Marron</option>');
  
  /* obtener imagen guardada(s) */
  $( '.divDropzone' ).html(
    '<div id="id-divDropzone" class="dropzone div-dropzone">'
      +'<div class="dz-message">'
        +'Presionar para subir imágen'
      +'</div>'
    +'</div>'
  );


  Dropzone.autoDiscover = false;
  Dropzone.prototype.defaultOptions.dictDefaultMessage = "Presionar para subir imágen";
  Dropzone.prototype.defaultOptions.dictFallbackMessage = "Tu navegador no soporta la función arrastrar la imágen";
  Dropzone.prototype.defaultOptions.dictFileTooBig = "La imágen pesa ({{filesize}}MiB). El tamaño máximo es: {{maxFilesize}}MiB.";
  Dropzone.prototype.defaultOptions.dictInvalidFileType = "Solo se permite imágenes PNG / JPG / JPEG";
  Dropzone.prototype.defaultOptions.dictCancelUpload = "Cancelar";
  Dropzone.prototype.defaultOptions.dictCancelUploadConfirmation = "¿Estás seguro de cancelar la subida?";
  Dropzone.prototype.defaultOptions.dictRemoveFile = "Eliminar";
  Dropzone.prototype.defaultOptions.dictMaxFilesExceeded = "Solo se puede subir 1 imágen";

  url = base_url + 'TiendaVirtual/CategoriasTiendaVirtualController/uploadOnly';
  var myDropzone = new Dropzone("#id-divDropzone", {
    url: url,
    params: {
      iVersionImage: 1,
      iIdProducto: 1,
    },
    acceptedFiles: ".jpeg,.jpg,.png,.webp",
    addRemoveLinks: true,
    uploadMultiple: false,
    maxFilesize: 1,//Peso en MB
    maxFiles: 1,
    thumbnailHeight: 200,
    thumbnailWidth: 200,
    parallelUploads: 1,
    thumbnail: function (file, dataUrl) {
      if (file.previewElement) {
        file.previewElement.classList.remove("dz-file-preview");
        var images = file.previewElement.querySelectorAll("[data-dz-thumbnail]");
        for (var i = 0; i < images.length; i++) {
          var thumbnailElement = images[i];
          thumbnailElement.alt = file.name;
          thumbnailElement.src = dataUrl;
        }
        setTimeout(function () { file.previewElement.classList.add("dz-image-preview"); }, 1);
      }
    },
    removedfile: function (file) {
      var nameFileImage = file.name;
      url = base_url + 'TiendaVirtual/CategoriasTiendaVirtualController/removeFileImage';
      $.ajax({
        url: url,
        type: "POST",
        dataType: "JSON",
        data: { iIdProducto: 1, nameFileImage: nameFileImage },
        success: function (response) {
          $('.modal-message').removeClass('modal-danger modal-warning modal-success');
          $('#modal-message').modal('show');

          if (response.status == 'success') {
            $('.modal-message').addClass(response.style_modal);
            $('.modal-title-message').text(response.message);
            setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
            $('#hidden-nombre_imagen_categoria').val('');
            $('#hidden-nombre_imagen_url_categoria').val('');
            //reload_table_producto();
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
      //Verificar respuesta del servidor al subir archivo
      this.on("success", function (file, response) {
        var response = jQuery.parseJSON(response);

        console.log('2');
        console.log(response);
        $('.modal-message').removeClass('modal-danger modal-warning modal-success');
        $('#modal-message').modal('show');

        if (response.sStatus != 'error') {
          $('.modal-message').addClass(response.sClassModal);
          $('.modal-title-message').text(response.sMessage);

          $('#hidden-nombre_imagen_categoria').val(response.sNombreImagenCategoria);
          $('#hidden-nombre_imagen_url_categoria').val(response.sNombreImagenCategoriaUrl);

          setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
        } else {
          $('.modal-message').addClass(response.sClassModal);
          $('.modal-title-message').text(response.sMessage);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 2100);
        }
      })
    },
  })
  //FIN IMAGEN

  $( '#modal-loader' ).modal('hide');
}

function verCategoria(ID, No_Imagen_Categoria, No_Imagen_Url_Categoria, Nu_Version_Imagen){
  accion_familia='upd_familia';
  
  $( '#form-Categoria' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '#modal-loader' ).modal('show');
 
  url = base_url + 'TiendaVirtual/CategoriasTiendaVirtualController/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $( '#modal-Categoria' ).modal('show');
      $( '.modal-title' ).text('Modifcar Categoría');
  
    	$( '#modal-Categoria' ).on('shown.bs.modal', function() {
    		$( '#txt-No_Familia' ).focus();
    	})
      
      $( '[name="EID_Empresa"]' ).val(response.ID_Empresa);
      $( '[name="EID_Familia"]' ).val(response.ID_Familia);
      $( '[name="ENo_Familia"]').val(response.No_Familia);
      $( '[name="ENu_Orden"]' ).val(response.Nu_Orden);
      $( '[name="No_Imagen_Categoria"]' ).val(response.No_Imagen_Categoria);
      $( '[name="No_Imagen_Url_Categoria"]' ).val(response.No_Imagen_Url_Categoria);

      $('[name="Nu_Orden"]').val(response.Nu_Orden);
      $('[name="No_Familia"]').val(response.No_Familia);
      $('[name="No_Familia_Breve"]').val(response.No_Familia_Breve);
      
      $( '.div-Estado' ).show();
      $( '#cbo-Estado' ).html( '' );
      
      var selected='';
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

      selected = '';
      if (response.No_Html_Color == '141619')
        selected = 'selected="selected"';
      $('#cbo-color').html('<option value="141619" ' + selected + '>Negro</option>');

      selected = '';
      if (response.No_Html_Color == '0d6efd')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="0d6efd" ' + selected + '>Azul</option>');

      selected = '';
      if (response.No_Html_Color == '198754')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="198754" ' + selected + '>Verde</option>');

      selected = '';
      if (response.No_Html_Color == 'dc3545')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="dc3545" ' + selected + '>Rojo</option>');

      selected = '';
      if (response.No_Html_Color == 'ffff00')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="ffff00" ' + selected + '>Amarillo</option>');

      selected = '';
      if (response.No_Html_Color == '0dcaf0')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="0dcaf0" ' + selected + '>Celeste</option>');

      selected = '';
      if (response.No_Html_Color == 'ff9100')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="ff9100" ' + selected + '>Naranja</option>');
      
      selected = '';
      if (response.No_Html_Color == '7c4dff')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="7c4dff" ' + selected + '>Morado</option>');

      selected = '';
      if (response.No_Html_Color == 'ff4081')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="ff4081" ' + selected + '>Rosado</option>');

      selected = '';
      if (response.No_Html_Color == '4e342e')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="4e342e" ' + selected + '>Marron</option>');

      for (var i = 0; i < 2; i++){
        selected = '';
        if (response.Nu_Activar_Familia_Lae_Shop == i)
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

  /* obtener imagen guardada(s) */
  $( '.divDropzone' ).html(
  '<div id="id-divDropzone" class="dropzone div-dropzone">'
    +'<div class="dz-message">'
      +'Presionar para subir imágen'
    +'</div>'
  +'</div>'
  );

  Dropzone.autoDiscover = false;
  Dropzone.prototype.defaultOptions.dictDefaultMessage = "Presionar para subir imágen";
  Dropzone.prototype.defaultOptions.dictFallbackMessage = "Tu navegador no soporta la función arrastrar la imágen";
  Dropzone.prototype.defaultOptions.dictFileTooBig = "La imágen pesa ({{filesize}}MiB). El tamaño máximo es: {{maxFilesize}}MiB.";
  Dropzone.prototype.defaultOptions.dictInvalidFileType = "Solo se permite imágenes PNG / JPG / JPEG";
  Dropzone.prototype.defaultOptions.dictCancelUpload = "Cancelar";
  Dropzone.prototype.defaultOptions.dictCancelUploadConfirmation = "¿Estás seguro de cancelar la subida?";
  Dropzone.prototype.defaultOptions.dictRemoveFile = "Eliminar";
  Dropzone.prototype.defaultOptions.dictMaxFilesExceeded = "Solo se puede subir 1 imágen";

  url = base_url + 'TiendaVirtual/CategoriasTiendaVirtualController/uploadOnly';
  var myDropzone = new Dropzone("#id-divDropzone", {
    url: url,
    params: {
      iVersionImage: (parseInt(Nu_Version_Imagen) + 1),
      iIdProducto: ID,
    },
    acceptedFiles: ".jpeg,.jpg,.png,.webp",
    addRemoveLinks: true,
    uploadMultiple: false,
    maxFilesize: 1,//Peso en MB
    maxFiles: 1,
    thumbnailHeight: 300,
    thumbnailWidth: 300,
    parallelUploads: 1,
    thumbnail: function (file, dataUrl) {
      if (file.previewElement) {
        file.previewElement.classList.remove("dz-file-preview");
        var images = file.previewElement.querySelectorAll("[data-dz-thumbnail]");
        for (var i = 0; i < images.length; i++) {
          var thumbnailElement = images[i];
          thumbnailElement.alt = file.name;
          thumbnailElement.src = dataUrl;
        }
        setTimeout(function () { file.previewElement.classList.add("dz-image-preview"); }, 1);
      }
    },
    removedfile: function (file) {
      url = base_url + 'TiendaVirtual/CategoriasTiendaVirtualController/removeFileImage';
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
            $('#hidden-nombre_imagen_categoria').val('');
            $('#hidden-nombre_imagen_url_categoria').val('');
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

          $('#hidden-nombre_imagen_categoria').val(response.sNombreImagenCategoria);
          $('#hidden-nombre_imagen_url_categoria').val(response.sNombreImagenCategoriaUrl);

          setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
        } else {
          $('.modal-message').addClass(response.sClassModal);
          $('.modal-title-message').text(response.sMessage);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 2100);
        }
      })

      if (No_Imagen_Url_Categoria.length > 0 && No_Imagen_Url_Categoria != '' && No_Imagen_Url_Categoria !== undefined) {
        var me = this;
        url = base_url + 'TiendaVirtual/CategoriasTiendaVirtualController/get_image';
        var arrPost = {
          'sUrlImage': No_Imagen_Url_Categoria,
        }
        $.post(url, arrPost, function (response) {
          $.each(response, function (key, value) {
            var mockfile = value;
            me.emit("addedfile", mockfile);
            me.emit("thumbnail", mockfile, No_Imagen_Url_Categoria);
            me.emit("complete", mockfile);
          })
        }, 'json');
      }
    }
  })
  //FIN IMAGEN
}

function form_Categoria(){
  if ( accion_familia=='add_familia' || accion_familia=='upd_familia' ) {
    $( '#btn-save' ).text('');
    $( '#btn-save' ).attr('disabled', true);
    $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
  
    $( '#modal-loader' ).modal('show');
    
    url = base_url + 'TiendaVirtual/CategoriasTiendaVirtualController/crudCategoria';
  	$.ajax({
      type		  : 'POST',
      dataType	: 'JSON',
  		url		    : url,
  		data		  : $('#form-Categoria').serialize(),
  		success : function( response ){
  		  $( '#modal-loader' ).modal('hide');
  		  
  	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
    	  $( '#modal-message' ).modal('show');
  		  
  		  if (response.status == 'success'){
  		    accion_familia='';
  		    $('#modal-Categoria').modal('hide');
    	    $( '.modal-message' ).addClass(response.style_modal);
    	    $( '.modal-title-message' ).text(response.message);
    	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
    	    reload_table_familia();
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

function eliminarCategoria(ID, accion_familia){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });
  
  $(document).bind('keydown', 'esc', function(){
    if ( accion_familia=='delete' ) {
      _eliminarCategoria($modal_delete, ID);
      accion_familia='';
    }
  });

  $( '#btn-save-delete' ).off('click').click(function () {
    _eliminarCategoria($modal_delete, ID);
  });
}

function _eliminarCategoria($modal_delete, ID){
  $( '#modal-loader' ).modal('show');
  
  url = base_url + 'TiendaVirtual/CategoriasTiendaVirtualController/eliminarCategoria/' + ID;
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
		    accion_familia='';
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
  	    reload_table_familia();
		  } else {
		    accion_familia='';
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
		  }
    },
    error: function (jqXHR, textStatus, errorThrown) {
		  accion_familia='';
		  
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

function reload_table_familia(){
  table_familia.ajax.reload(null,false);
}