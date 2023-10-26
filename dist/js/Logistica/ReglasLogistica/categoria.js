var url;
var table_familia;
var accion_familia;

$(function () {
	$(document).keyup(function(event){
    if(event.which == 27){//ESC
      $( "#modal-Categoria" ).modal('hide');
    }
	});

  $('.select2').select2();
  $('[data-mask]').inputmask();
  
  url = base_url + 'Logistica/ReglasLogistica/CategoriaController/ajax_list';
  table_familia = $( '#table-Categoria' ).DataTable({
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
  
  $( '.dataTables_length' ).addClass('col-md-3');
  $( '.dataTables_paginate' ).addClass('col-md-9');
  
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

  //CSS
  $('#cbo-color').change(function () {
    $(".background").css("background-color", "#" + $(this).val());
  })
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

  $('#cbo-Estado').html('<option value="1">Activo</option>');
  
  $('#cbo-imprimir_comanda').html('<option value="1">Si</option>');
  $('#cbo-imprimir_comanda').append('<option value="0">No</option>');

  $('#cbo-color').html('<option value="766df4">Púrpura</option>');
  $('#cbo-color').append('<option value="7B39FF">Morado</option>');
  $('#cbo-color').append('<option value="1A7FDC">Celeste</option>');
  $('#cbo-color').append('<option value="1B61A1">Azul</option>');
  $('#cbo-color').append('<option value="227E52">Verde</option>');
  $('#cbo-color').append('<option value="6D7A6A">Verde grisáceo</option>');
  $('#cbo-color').append('<option value="ED5702">Naranja</option>');
  $('#cbo-color').append('<option value="DE063A">Rojo</option>');
  $('#cbo-color').append('<option value="950919">Guinda</option>');
  $('#cbo-color').append('<option value="DE287F">Fucsia</option>');
  $('#cbo-color').append('<option value="FF3048">Rosado</option>');
  $('#cbo-color').append('<option value="ffe930">Amarillo</option>');
  $('#cbo-color').append('<option value="2D2C2C">Negro</option>');
  $('#cbo-color').append('<option value="6C6B6C">Gris</option>');
  $('#cbo-color').append('<option value="4e342e">Marron</option>');
  
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
  
  url = base_url + 'Logistica/ReglasLogistica/CategoriaController/uploadOnly';
  var myDropzone = new Dropzone("#id-divDropzone", {
    url: url,
    params: {
      iVersionImage: 1,
      iIdFamilia: 1,
    },
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
      url = base_url + 'Logistica/ReglasLogistica/CategoriaController/removeFileImage';
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

          $( '[name="No_Imagen_Categoria"]' ).val( response.sNombreImagenCategoria );
          $( '[name="No_Imagen_Url_Categoria"]' ).val( response.sNombreImagenCategoriaUrl );

          setTimeout(function() {$('#modal-message').modal('hide'); }, 1100);
        } else {
          $( '.modal-message' ).addClass(response.sClassModal);
          $( '.modal-title-message' ).text(response.sMessage);
          setTimeout(function() {$('#modal-message').modal('hide');}, 1200);
        }
      })
    },
  });
  
  $( '#modal-loader' ).modal('hide');
}

function verCategoria(ID, No_Imagen_Categoria, No_Imagen_Url_Categoria, Nu_Version_Imagen){
  accion_familia='upd_familia';
  
  $( '#form-Categoria' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '#modal-loader' ).modal('show');
 
  url = base_url + 'Logistica/ReglasLogistica/CategoriaController/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $( '#modal-Categoria' ).modal('show');
      $( '.modal-title' ).text('Modificar Categoría');
  
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

      $(".background").css("background-color", "#" + response.No_Html_Color);

      selected = '';
      if (response.No_Html_Color == '766df4')
        selected = 'selected="selected"';
      $('#cbo-color').html('<option value="766df4" ' + selected + '>Púrpura</option>');

      selected = '';
      if (response.No_Html_Color == '7B39FF')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="7B39FF" ' + selected + '>Morado</option>');

      selected = '';
      if (response.No_Html_Color == '1A7FDC')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="1A7FDC" ' + selected + '>Celeste</option>');

      selected = '';
      if (response.No_Html_Color == '1B61A1')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="1B61A1" ' + selected + '>Azul</option>');

      selected = '';
      if (response.No_Html_Color == '227E52')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="227E52" ' + selected + '>Verde</option>');

      selected = '';
      if (response.No_Html_Color == '6D7A6A')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="6D7A6A" ' + selected + '>Verde grisáceo</option>');

      selected = '';
      if (response.No_Html_Color == 'ED5702')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="ED5702" ' + selected + '>Naranja</option>');

      selected = '';
      if (response.No_Html_Color == 'DE063A')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="DE063A" ' + selected + '>Rojo</option>');

      selected = '';
      if (response.No_Html_Color == '950919')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="950919" ' + selected + '>Guinda</option>');

      selected = '';
      if (response.No_Html_Color == 'DE287F')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="DE287F" ' + selected + '>Fucsia</option>');

      selected = '';
      if (response.No_Html_Color == 'FF3048')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="FF3048" ' + selected + '>Rosado</option>');

      selected = '';
      if (response.No_Html_Color == 'ffe930')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="ffe930" ' + selected + '>Amarillo</option>');

      selected = '';
      if (response.No_Html_Color == '2D2C2C')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="2D2C2C" ' + selected + '>Negro</option>');

      selected = '';
      if (response.No_Html_Color == '6C6B6C')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="6C6B6C" ' + selected + '>Gris</option>');

      selected = '';
      if (response.No_Html_Color == '4e342e')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="4e342e" ' + selected + '>Marron</option>');

      for (var i = 0; i < 2; i++){
        selected = '';
        if(response.Nu_Estado == i)
          selected = 'selected="selected"';
        $( '#cbo-Estado' ).append( '<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Inactivo' : 'Activo') + '</option>' );
      }

      $( '#cbo-imprimir_comanda' ).html('');
      for (var i = 0; i < 2; i++){
        selected = '';
        if(response.Nu_Imprimir_Comanda_Restaurante == i)
          selected = 'selected="selected"';
        $( '#cbo-imprimir_comanda' ).append( '<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'No' : 'Si') + '</option>' );
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
    
  url = base_url + 'Logistica/ReglasLogistica/CategoriaController/uploadOnly/' + ID;
  var myDropzone = new Dropzone("#id-divDropzone", {
    url: url,
    params: {
      iVersionImage: (parseInt(Nu_Version_Imagen) + 1),
      iIdFamilia: ID,
    },
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
      var arrName = file.name.split('/');
      var nameFileImage;
      if (arrName.length === 4)//Si la imagen ya está en el server
        nameFileImage = arrName[3];
      else//Si la imagén recién la vamos a subir y no existe en el server
        nameFileImage = arrName[0];
      url = base_url + 'Logistica/ReglasLogistica/CategoriaController/removeFileImage';
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
          
          $( '[name="No_Imagen_Categoria"]' ).val( response.sNombreImagenCategoria );
          $( '[name="No_Imagen_Url_Categoria"]' ).val( response.sNombreImagenCategoriaUrl );
          
          setTimeout(function() {$('#modal-message').modal('hide'); }, 1100);
        } else {
          $( '.modal-message' ).addClass(response.sClassModal);
          $( '.modal-title-message' ).text(response.sMessage);
          setTimeout(function() {$('#modal-message').modal('hide');}, 1200);
        }
      })

      if (No_Imagen_Url_Categoria.length > 0 && No_Imagen_Url_Categoria != '' && No_Imagen_Url_Categoria !== undefined) {
        var me = this;
        url = base_url + 'Logistica/ReglasLogistica/CategoriaController/get_image';
        var arrPost={
          'sNombreImage': No_Imagen_Categoria,
          'sUrlImage': No_Imagen_Url_Categoria,
        }
        $.post(url, arrPost, function(response){
          $.each(response, function(key, value){
            var mockfile = value;
            me.emit("addedfile", mockfile);
            me.emit("thumbnail", mockfile, No_Imagen_Url_Categoria);
            me.emit("complete", mockfile);
          })
        }, 'json');
      }
    }
  })
}

function form_Categoria(){
  if ( accion_familia=='add_familia' || accion_familia=='upd_familia' ) {
    $( '#btn-save' ).text('');
    $( '#btn-save' ).attr('disabled', true);
    $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
  
    $( '#modal-loader' ).modal('show');
    
    url = base_url + 'Logistica/ReglasLogistica/CategoriaController/crudCategoria';
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
  
  url = base_url + 'Logistica/ReglasLogistica/CategoriaController/eliminarCategoria/' + ID;
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