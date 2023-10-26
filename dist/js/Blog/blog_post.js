var url;
var table_blog_post;
var accion_blog_post = '';

$(function () {
  $( '.div-AgregarEditar' ).hide();

  $('.select2').select2();
  CKEDITOR.replace('Txt_Contenido_Blog');
  
  url = base_url + 'Blog/BlogPostController/ajax_list';
  table_blog_post = $( '#table-Producto' ).DataTable({
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
        data.Filtros_BlogPost = $( '#cbo-Filtros_BlogPost' ).val(),
        data.Global_Filter = $( '#txt-Global_Filter' ).val();
      },
    },
    'columnDefs': [{
      'className' : 'text-center',
      'targets'   : 'no-sort',
      'orderable' : false,
    },{
      'className' : 'text-right',
      'targets'   : 'sort_right',
      'orderable' : true,
    },],
    'lengthMenu': [[10, 100, 1000, -1], [10, 100, 1000, "Todos"]],
  });
  
  $( '.dataTables_length' ).addClass('col-md-3');
  $( '.dataTables_paginate' ).addClass('col-md-9');

  $( '#txt-Global_Filter' ).keyup(function() {
    table_blog_post.search($(this).val()).draw();
  });

  $( '#form-BlogPost' ).validate({
		rules:{
      No_Titulo_Blog: {
				required: true,
			},
      Txt_Contenido_Blog: {
				required: true,
      },
		},
		messages:{
      No_Titulo_Blog:{
				required: "Ingresar título",
      },
      Txt_Contenido_Blog: {
        required: "Ingresar contenido",
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
		submitHandler: form_BlogPost
	});

  $( '.div-video' ).hide();
	$( '.div-imagen' ).hide();
  $('#cbo-tipo_media').change(function () {
    $('.div-video').hide();
    $('.div-imagen').hide();
    if ($(this).val() == 1) {//Imagen
      $('.div-imagen').show();
      $('.div-video').hide();
    }
    if ( $(this).val() == 2 ){//Video
      $('.div-imagen').show();
      $('.div-video').show();
	  }
	})
	
  $(document).bind('keydown', 'f2', function(){
    agregarBlogPost();
  });
})

function agregarBlogPost(){
  accion_blog_post = 'add_blog_post';

  $( '#modal-loader' ).modal('show');

  //Se usa porque si entro a modificar y luego salgo, sin realizar alguna modificación y quiero agregar se quedan los datos de modificar porque todo esta en la misma pantalla
  $('#form-BlogPost')[0].reset();
  $('.form-group').removeClass('has-error');
  $('.form-group').removeClass('has-success');
  $('.help-block').empty();

  CKEDITOR.instances.Txt_Contenido_Blog.setData("");

  $( '.div-Listar' ).hide();
  $( '.div-AgregarEditar' ).show();

  $('.div-video').hide();
  $('.div-imagen').hide();

  $('[name="EID_Post_Blog"]').val('');
  $('[name="No_Imagen_Item"]').val('');

  $('#cbo-tag').html('<option value="0" selected="selected">- Sin datos -</option>');
  url = base_url + 'HelperController/getValoresTablaDato';
  $.post(url, { sTipoData: 'Tag_Blog' }, function (response) {
    $('#cbo-tag').html('<option value="0" selected="selected">- Sin datos -</option>');
    if (response.sStatus == 'success') {
      $('#cbo-tag').html('<option value="" selected="selected">- Seleccionar -</option>');
      var response = response.arrData;
      for (var i = 0; i < response.length; i++)
        $('#cbo-tag').append('<option value="' + response[i].ID_Tabla_Dato + '">' + response[i].No_Descripcion + '</option>');
    }
  }, 'JSON');

  $('[name="No_Titulo_Blog"]').focus();

  $('#cbo-tipo_media').html( '<option value="0">- Seleccionar -</option>' );
  $('#cbo-tipo_media').append('<option value="1">Imagen</option>');
  $('#cbo-tipo_media').append('<option value="2">Video</option>');

  $('#cbo-estado').html('<option value="1">Activo</option>');
  $('#cbo-estado').append('<option value="0">Inactivo</option>');

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
  Dropzone.prototype.defaultOptions.dictInvalidFileType = "Solo se permite imágenes PNG / JPG";
  Dropzone.prototype.defaultOptions.dictCancelUpload = "Cancelar";
  Dropzone.prototype.defaultOptions.dictCancelUploadConfirmation = "¿Estás seguro de cancelar la subida?";
  Dropzone.prototype.defaultOptions.dictRemoveFile = "Eliminar";
  Dropzone.prototype.defaultOptions.dictMaxFilesExceeded = "Solo se puede subir 6 imágenes";
  
  url = base_url + 'Blog/BlogPostController/uploadMultiple';
  var myDropzone = new Dropzone("#id-divDropzone", {
    url: url,
    acceptedFiles: ".jpeg,.jpg,.png,.webp",
    addRemoveLinks: true,
    uploadMultiple: false,
    maxFilesize: 1,//Peso en MB
    maxFiles: 6,
    thumbnailHeight: 200,
    thumbnailWidth: 200,
    parallelUploads: 6,
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
      url = base_url + 'Blog/BlogPostController/removeAddFileImage';
      $.ajax({
        url : url,
        type: "POST",
        dataType: "JSON",
        data: { file_name: file.name},
      })
      var previewElement;
      return (previewElement = file.previewElement) != null ? (previewElement.parentNode.removeChild(file.previewElement)) : (void 0);
    },
    init : function() {
      //Verificar respuesta del servidor al subir archivo
      this.on("success", function(file, response, formData) {
        var response = jQuery.parseJSON(response);
        
        $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
        $( '#modal-message' ).modal('show');

        if (response.sStatus == 'success'){
          $( '.modal-message' ).addClass(response.sClassModal);
          $( '.modal-title-message' ).text(response.sMessage);


          var EID_Gallery = $('[name="EID_Gallery"]').val();
          
          $('[name="ENo_Imagen_Gallery"]').val(response.sNombreImagenGallery);
          $('[name="ENo_Url_Imagen_Gallery"]').val(response.sNombreImagenGalleryUrl);

          if ( EID_Gallery.length > 0 )
            $('[name="EID_Gallery"]').val(EID_Gallery + "','" + response.iLastIdGallery);
          else if (EID_Gallery.length == 0)
            $('[name="EID_Gallery"]').val(response.iLastIdGallery);
        
          setTimeout(function() {$('#modal-message').modal('hide'); }, 1100);
        } else {
          $( '.modal-message' ).addClass(response.sClassModal);
          $( '.modal-title-message' ).text(response.sMessage);
          setTimeout(function() {$('#modal-message').modal('hide');}, 1200);
        }
      })
    },
  })

  $('#modal-loader').modal('hide');
}

function verBlogPost(ID){
  accion_blog_post = 'upd_blog_post';

  $('#modal-loader').modal('show');

  $('.div-Listar').hide();
  $('.div-AgregarEditar').show();

  $('.div-video').hide();
  $('.div-imagen').hide();

  //Se usa porque si entro a modificar y luego salgo, sin realizar alguna modificación y quiero agregar se quedan los datos de modificar porque todo esta en la misma pantalla
  $('#form-BlogPost')[0].reset();
  $('.form-group').removeClass('has-error');
  $('.form-group').removeClass('has-success');
  $('.help-block').empty();

  CKEDITOR.instances.Txt_Contenido_Blog.setData("");

  $('[name="EID_Post_Blog"]').val('');
  $('[name="No_Imagen_Item"]').val('');
    
  url = base_url + 'Blog/BlogPostController/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $( '.div-AgregarEditar' ).show();
            
      $('[name="EID_Post_Blog"]').val(response.ID_Post_Blog);
      $('[name="ENo_Titulo_Blog"]').val(response.No_Titulo_Blog);
      $('[name="EID_Gallery"]').val(response.ID_Gallery);//ID_Gallery se usuará para indicar que ya se agrego imágenes
      
      var selected = '';
      $('#cbo-tag').html('<option value="0" selected="selected">- Sin datos -</option>');
      url = base_url + 'HelperController/getValoresTablaDato';
      $.post(url, { sTipoData: 'Tag_Blog' }, function (responseTag) {
        $('#cbo-tag').html('<option value="0" selected="selected">- Sin datos -</option>');
        if (responseTag.sStatus == 'success') {
          $('#cbo-tag').html('<option value="" selected="selected">- Seleccionar -</option>');
          var responseTag = responseTag.arrData;
          for (var i = 0; i < responseTag.length; i++) {
            selected = '';
            if (response.ID_Tag_Blog == responseTag[i].ID_Tabla_Dato)
              selected = 'selected="selected"';
            $('#cbo-tag').append('<option value="' + responseTag[i].ID_Tabla_Dato + '" ' + selected + '>' + responseTag[i].No_Descripcion + '</option>');
          }
        }
      }, 'JSON');

      $('[name="No_Titulo_Blog"]').val(clearHTMLTextArea(response.No_Titulo_Blog));

      CKEDITOR.instances.Txt_Contenido_Blog.setData(response.Txt_Contenido_Blog);
      
      if (response.ID_Tipo_Media == 1) {
        $('.div-imagen').show();
        $('.div-video').hide();
      } else if (response.ID_Tipo_Media == 2) {
        $('.div-imagen').show();
        $('.div-video').show();
      }

      $('#cbo-tipo_media').html('');
      var sNombreMedia = '';
      for (var i = 0; i < 3; i++) {
        selected = '';
        if (response.ID_Tipo_Media == i)
          selected = 'selected="selected"';
        if ( i == 0 )
          sNombreMedia = '- Seleccionar -';
        else if (i == 1)
          sNombreMedia = 'Imagen';
        else if (i == 2)
          sNombreMedia = 'Video';
        $('#cbo-tipo_media').append('<option value="' + i + '" ' + selected + '>' + sNombreMedia + '</option>');
      }

      $('[name="No_Url_Video_Blog"]').val(response.No_Url_Video_Blog);

      $( '#cbo-estado' ).html('');
      for (var i = 0; i < 2; i++){
        selected = '';
        if(response.Nu_Estado == i)
          selected = 'selected="selected"';
        $( '#cbo-estado' ).append( '<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Inactivo' : 'Activo') + '</option>' );
      }

      $('[name="No_Titulo_Blog"]').focus();

      $('#modal-loader').modal('hide');
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
      +'Arrastrar o presionar click para subir imágen(es)'
    +'</div>'
  +'</div>'
  );
  
  Dropzone.autoDiscover = false;
  Dropzone.prototype.defaultOptions.dictDefaultMessage = "Arrastrar o presionar click para subir imágen";
  Dropzone.prototype.defaultOptions.dictFallbackMessage = "Tu navegador no soporta la función arrastrar la imágen";
  Dropzone.prototype.defaultOptions.dictFileTooBig = "La imágen pesa ({{filesize}}MiB). El tamaño máximo es: {{maxFilesize}}MiB.";
  Dropzone.prototype.defaultOptions.dictInvalidFileType = "Solo se permite imágenes PNG / JPG";
  Dropzone.prototype.defaultOptions.dictCancelUpload = "Cancelar";
  Dropzone.prototype.defaultOptions.dictCancelUploadConfirmation = "¿Estás seguro de cancelar la subida?";
  Dropzone.prototype.defaultOptions.dictRemoveFile = "Eliminar";
  Dropzone.prototype.defaultOptions.dictMaxFilesExceeded = "Solo se puede subir 6 imágenes";
  
  url = base_url + 'Blog/BlogPostController/uploadMultiple';
  var myDropzone = new Dropzone("#id-divDropzone", {
    url: url,
    acceptedFiles: ".jpeg,.jpg,.png,.webp",
    addRemoveLinks: true,
    uploadMultiple: false,
    maxFilesize: 1,//Peso en MB
    maxFiles: 6,
    thumbnailHeight: 200,
    thumbnailWidth: 200,
    parallelUploads: 6,
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
      url = base_url + 'Blog/BlogPostController/removeFileImage';
      $.ajax({
        url : url,
        type: "POST",
        dataType: "JSON",
        data: { file_name: file.file_name, id_image : file.id_image },
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

          var EID_Gallery = $('[name="EID_Gallery"]').val();

          $('[name="ENo_Imagen_Gallery"]').val(response.sNombreImagenGallery);
          $('[name="ENo_Url_Imagen_Gallery"]').val(response.sNombreImagenGalleryUrl);

          if (EID_Gallery.length > 0)
            $('[name="EID_Gallery"]').val(EID_Gallery + "','" + response.iLastIdGallery);
          else if (EID_Gallery.length == 0)
            $('[name="EID_Gallery"]').val(response.iLastIdGallery);
          
          setTimeout(function() {$('#modal-message').modal('hide'); }, 1100);
        } else {
          $( '.modal-message' ).addClass(response.sClassModal);
          $( '.modal-title-message' ).text(response.sMessage);
          setTimeout(function() {$('#modal-message').modal('hide');}, 1200);
        }
      })
      var me = this;
      url = base_url + 'Blog/BlogPostController/get_image';
      var arrPost={'iIdRelacionGallery': ID};
      $.post(url, arrPost, function(response){
        if ( response.sStatus == 'success' ) {
          $.each(response.arrfilesImages, function(key, value){
            var mockfile = value;
            me.emit("addedfile", mockfile);
            me.emit("thumbnail", mockfile, value.name);
            me.emit("complete", mockfile);
          })
        } else {
          $('.modal-message').addClass(response.sClassModal);
          $('.modal-title-message').text(response.sMessage);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 3200);
        }
      }, 'json');
    }
  })
}

function form_BlogPost(){
  if (accion_blog_post == 'add_blog_post' || accion_blog_post == 'upd_blog_post') {
    $('.help-block').empty();
    if ($('#cbo-tipo_media').val() == '1' && $('#txt-EID_Gallery').val().length === 0) {
      $('#cbo-tipo_media').closest('.form-group').find('.help-block').html('Subir al menos 1 imagen');
      $('#cbo-tipo_media').closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ($('#cbo-tipo_media').val() == '2' && $('[name="No_Url_Video_Blog"]').val().length === 0) {
      $('#txt-No_Url_Video_Blog').closest('.form-group').find('.help-block').html('Ingresar link del video');
      $('#txt-No_Url_Video_Blog').closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      $( '#btn-save' ).text('');
      $( '#btn-save' ).attr('disabled', true);
      $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
    
      $( '#modal-loader' ).modal('show');

      var arrBlogPost = {
        'EID_Post_Blog': $('#txt-EID_Post_Blog').val(),
        'ENo_Titulo_Blog': $('#txt-ENo_Titulo_Blog').val(),
        'EID_Gallery': $('#txt-EID_Gallery').val(),
        'ID_Tag_Blog': $('#cbo-tag').val(),
        'No_Titulo_Blog': $('[name="No_Titulo_Blog"]').val(),
        'ID_Tipo_Media': $('#cbo-tipo_media').val(),
        'No_Url_Video_Blog': $('[name="No_Url_Video_Blog"]').val(),
        'Nu_Estado': $('#cbo-estado').val(),
        'Txt_Contenido_Blog' : CKEDITOR.instances.Txt_Contenido_Blog.getData(),
      };

      url = base_url + 'Blog/BlogPostController/crudPostBlog';
      $.ajax({
        type		  : 'POST',
        dataType	: 'JSON',
        url: url,
        data: arrBlogPost,
        success : function( response ){
          $( '#modal-loader' ).modal('hide');
          
          $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
          $( '#modal-message' ).modal('show');
          
          if (response.status == 'success'){
            accion_blog_post = '';
            
            $('#form-BlogPost')[0].reset();
            $('.form-group').removeClass('has-error');
            $('.form-group').removeClass('has-success');
            $('.help-block').empty();

            CKEDITOR.instances.Txt_Contenido_Blog.setData("");

            $( '.div-AgregarEditar' ).hide();
            $( '.div-Listar' ).show();
            $( '.modal-message' ).addClass(response.style_modal);
            $( '.modal-title-message' ).text(response.message);
            setTimeout(function() {$('#modal-message').modal('hide'); }, 1100);
            reload_table_blog_post();
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
    }// /. if de validaciones
  }// /. if de accion_blog_post
}

function eliminarPostBlog(ID){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });
  
  $(document).bind('keydown', 'alt+l', function(){
    if ( accion_blog_post=='delete' ) {
      _eliminarPostBlog($modal_delete, ID);
      accion_blog_post='';
    }
  });

  $( '#btn-save-delete' ).off('click').click(function () {
    _eliminarPostBlog($modal_delete, ID);
  });
}

function reload_table_blog_post(){
  table_blog_post.ajax.reload(null,false);
}

function _eliminarPostBlog($modal_delete, ID){
  $( '#modal-loader' ).modal('show');
    
  url = base_url + 'Blog/BlogPostController/eliminarPostBlog/' + ID;
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
    	  accion_blog_post = '';
    		    
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
  	    reload_table_blog_post();
		  } else {
    	  accion_blog_post = '';
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
		  }
    },
    error: function (jqXHR, textStatus, errorThrown) {
    	accion_blog_post = '';
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