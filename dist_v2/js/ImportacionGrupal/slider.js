var url;
var table_familia, table_slider_mobile;
var accion_familia;

$(function () {
	$(document).keyup(function(event){
    if(event.which == 27){//ESC
      $( "#modal-Inicio" ).modal('hide');
    }
	});

  //$('.select2').select2();
  //$('[data-mask]').inputmask()
  
  /*
  url = base_url + 'ImportacionGrupal/BannersGrupal/ajax_list';
  table_familia = $( '#table-Inicio' ).DataTable({
    'dom'       : '<"top">frt<"bottom"><"clear">',
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
    'info'        : false,
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
        data.Filtros_Inicios = $( '#cbo-Filtros_Inicios' ).val(),
        data.Global_Filter = $( '#txt-Global_Filter' ).val();
      },
    },
    'columnDefs': [{
      'className' : 'text-center',
      'targets'   : 'no-sort',
      'orderable' : false,
    },],
  });
  */
 
  url = base_url + 'ImportacionGrupal/BannersGrupal/ajax_list';
  table_familia = $( '#table-Inicio' ).DataTable({
    //'dom'       : 'B<"top">frt<"bottom"lp><"clear">',
    //dom: "<'row'<'col-sm-12 col-md-3'Q><'col-sm-12 col-md-5'l><'col-sm-12 col-md-4'f>>" +
    //"<'row'<'col-sm-12'tr>>" +
    //"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    dom: "<'row'<'col-sm-12 col-md-5'><'col-sm-12 col-md-2'><'col-sm-12 col-md-5'f>>" +
    "<'row'<'col-sm-12'tr>>" +
    "<'row'<'col-sm-12 col-md-2'l><'col-sm-12 col-md-5'i><'col-sm-12 col-md-5'p>>",
    buttons     : [{
      extend    : 'excel',
      text      : '<i class="fa fa-file-excel color_icon_excel"></i> Excel',
      titleAttr : 'Excel',
      exportOptions: {
        columns: ':visible'
      }
    },
    {
      extend    : 'pdf',
      text      : '<i class="fa fa-file-pdf color_icon_pdf"></i> PDF',
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
    "paging": true,
    "lengthChange": true,
    "searching": false,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "responsive": false,
    'pagingType'  : 'full_numbers',
    'oLanguage' : {
      'sInfo'              : 'Mostrando (_START_ - _END_) total de registros _TOTAL_',
      'sLengthMenu'        : '_MENU_',
      'sSearch'            : 'Buscar por: ',
      'sSearchPlaceholder' : '',
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
    'lengthMenu': [[10, 100, 500, 1000], [10, 100, 500, 1000]],
  });
  
  $('#table-Inicio_filter input').removeClass('form-control-sm');
  $('#table-Inicio_filter input').addClass('form-control-md');
  $('#table-Inicio_filter input').addClass("width_full");

  /* slider mobile */
  url = base_url + 'ImportacionGrupal/BannersGrupal/ajax_list_slider_mobile';
  table_slider_mobile = $('#table-Inicio-mobile').DataTable({
    'dom': '<"top">frt<"bottom"><"clear">',
    buttons: [{
      extend: 'excel',
      text: '<i class="fa fa-file-excel-o color_icon_excel"></i> Excel',
      titleAttr: 'Excel',
      exportOptions: {
        columns: ':visible'
      }
    },
    {
      extend: 'pdf',
      text: '<i class="fa fa-file-pdf-o color_icon_pdf"></i> PDF',
      titleAttr: 'PDF',
      exportOptions: {
        columns: ':visible'
      }
    },
    {
      extend: 'colvis',
      text: '<i class="fa fa-ellipsis-v"></i> Columnas',
      titleAttr: 'Columnas',
      exportOptions: {
        columns: ':visible'
      }
    }],
    'searching': false,
    'bStateSave': true,
    'processing': true,
    'serverSide': true,
    'info': false,
    'autoWidth': false,
    'pagingType': 'full_numbers',
    'oLanguage': {
      'sInfo': 'Mostrando (_START_ - _END_) total de registros _TOTAL_',
      'sLengthMenu': '_MENU_',
      'sSearch': 'Buscar por: ',
      'sSearchPlaceholder': 'UPC / Nombre',
      'sZeroRecords': 'No se encontraron registros',
      'sInfoEmpty': 'No hay registros',
      'sLoadingRecords': 'Cargando...',
      'sProcessing': 'Procesando...',
      'oPaginate': {
        'sFirst': '<<',
        'sLast': '>>',
        'sPrevious': '<',
        'sNext': '>',
      },
    },
    'order': [],
    'ajax': {
      'url': url,
      'type': 'POST',
      'dataType': 'json',
      'data': function (data) {
        data.Filtros_Inicios = $('#cbo-Filtros_Inicios').val(),
          data.Global_Filter = $('#txt-Global_Filter').val();
      },
    },
    'columnDefs': [{
      'className': 'text-center',
      'targets': 'no-sort',
      'orderable': false,
    },],
  });

  /* ofertas y promociones */
  url = base_url + 'ImportacionGrupal/BannersGrupal/ajax_list_ofertas';
  table_oferta = $('#table-oferta').DataTable({
    'dom': '<"top">frt<"bottom"><"clear">',
    buttons: [{
      extend: 'excel',
      text: '<i class="fa fa-file-excel-o color_icon_excel"></i> Excel',
      titleAttr: 'Excel',
      exportOptions: {
        columns: ':visible'
      }
    },
    {
      extend: 'pdf',
      text: '<i class="fa fa-file-pdf-o color_icon_pdf"></i> PDF',
      titleAttr: 'PDF',
      exportOptions: {
        columns: ':visible'
      }
    },
    {
      extend: 'colvis',
      text: '<i class="fa fa-ellipsis-v"></i> Columnas',
      titleAttr: 'Columnas',
      exportOptions: {
        columns: ':visible'
      }
    }],
    'searching': false,
    'bStateSave': true,
    'processing': true,
    'serverSide': true,
    'info': false,
    'autoWidth': false,
    'pagingType': 'full_numbers',
    'oLanguage': {
      'sInfo': 'Mostrando (_START_ - _END_) total de registros _TOTAL_',
      'sLengthMenu': '_MENU_',
      'sSearch': 'Buscar por: ',
      'sSearchPlaceholder': 'UPC / Nombre',
      'sZeroRecords': 'No se encontraron registros',
      'sInfoEmpty': 'No hay registros',
      'sLoadingRecords': 'Cargando...',
      'sProcessing': 'Procesando...',
      'oPaginate': {
        'sFirst': '<<',
        'sLast': '>>',
        'sPrevious': '<',
        'sNext': '>',
      },
    },
    'order': [],
    'ajax': {
      'url': url,
      'type': 'POST',
      'dataType': 'json',
      'data': function (data) {
        data.Filtros_Inicios = $('#cbo-Filtros_Inicios').val(),
          data.Global_Filter = $('#txt-Global_Filter').val();
      },
    },
    'columnDefs': [{
      'className': 'text-center',
      'targets': 'no-sort',
      'orderable': false,
    },],
  });

  $( '.dataTables_length' ).addClass('col-md-3');
  $( '.dataTables_paginate' ).addClass('col-md-9');
  
  $( '#txt-Global_Filter' ).keyup(function() {
    table_familia.search($(this).val()).draw();
  });
  
  $( '#form-Inicio' ).validate({
		rules:{
      /*
			No_Slider: {
				required: true,
			},
      */
		},
		messages:{
      /*
			No_Slider:{
				required: "Ingresar nombre"
			},
      */
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
		submitHandler: form_Inicio
	});
	
  $(document).bind('keydown', 'f2', function(){
    agregarInicio();
  });
})

function agregarInicio(iTipoInicio){
  accion_familia='add_familia';
  
  $( '#modal-loader' ).modal('show');
  
  $( '#form-Inicio' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '#modal-Inicio' ).modal('show');
  $( '.modal-title' ).text('Nuevo Slider');
  
  $( '[name="EID_Empresa"]' ).val('');
  $( '[name="EID_Ecommerce_Inicio"]' ).val('');
  $( '[name="ENo_Slider"]' ).val('');

  $('[name="Nu_Tipo_Inicio"]').val(iTipoInicio);
  
	$( '#modal-Inicio' ).on('shown.bs.modal', function() {
		$( '#txt-No_Slider' ).focus();
	})
  
  $( '#modal-loader' ).modal('show');
  url = base_url + 'HelperController/getEmpresas';
  $.post( url , function( response ){
    $( '#cbo-Empresas' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-Empresas' ).append( '<option value="' + response[i].ID_Empresa + '">' + response[i].No_Empresa + '</option>' );
    $( '#modal-loader' ).modal('hide');
  }, 'JSON');

  $( '.div-Estado' ).hide();
  $( '#cbo-Estado' ).html( '<option value="1">Activo</option>' );
  
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
  Dropzone.prototype.defaultOptions.dictInvalidFileType = "Solo se permite imágenes PNG";
  Dropzone.prototype.defaultOptions.dictCancelUpload = "Cancelar";
  Dropzone.prototype.defaultOptions.dictCancelUploadConfirmation = "¿Estás seguro de cancelar la subida?";
  Dropzone.prototype.defaultOptions.dictRemoveFile = "Eliminar";
  Dropzone.prototype.defaultOptions.dictMaxFilesExceeded = "Solo se puede subir 1 imágen";
  
  url = base_url + 'ImportacionGrupal/BannersGrupal/uploadOnly';
  var myDropzone = new Dropzone("#id-divDropzone", {
    url : url,
    params: {
      iVersionImage: 1,
      iIdEcommerceInicio: 1,
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
    removedfile: function(file){
      var nameFileImage = file.name;
      url = base_url + 'ImportacionGrupal/BannersGrupal/removeFileImage';
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

            $('[name="No_Imagen_Inicio_Slider"]').val('');
            $('[name="No_Imagen_Url_Inicio_Slider"]').val('');
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
    init : function() {
      //Verificar respuesta del servidor al subir archivo
      this.on("success", function(file, response) {
        var response = jQuery.parseJSON(response);

        $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
        $( '#modal-message' ).modal('show');
        
        if (response.sStatus != 'error'){
          $( '.modal-message' ).addClass(response.sClassModal);
          $( '.modal-title-message' ).text(response.sMessage);

          $( '[name="No_Imagen_Inicio_Slider"]' ).val( response.sNombreImagenInicio );
          $( '[name="No_Imagen_Url_Inicio_Slider"]' ).val( response.sNombreImagenInicioUrl );

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

function verInicio(ID, No_Imagen_Inicio_Slider, No_Imagen_Url_Inicio_Slider, Nu_Version_Imagen){
  accion_familia='upd_familia';
  
  $( '#form-Inicio' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '#modal-loader' ).modal('show');
 
  url = base_url + 'ImportacionGrupal/BannersGrupal/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $( '#modal-Inicio' ).modal('show');
      $( '.modal-title' ).text('Modifcar Slider');
  
    	$( '#modal-Inicio' ).on('shown.bs.modal', function() {
    		$( '#txt-No_Slider' ).focus();
    	})
      
      $( '[name="EID_Empresa"]' ).val(response.ID_Empresa);
      $( '[name="EID_Ecommerce_Inicio"]' ).val(response.ID_Ecommerce_Inicio);
      $( '[name="ENo_Slider"]' ).val(response.No_Slider);

      $('[name="Nu_Tipo_Inicio"]').val(response.Nu_Tipo_Inicio);

      $( '[name="No_Imagen_Inicio_Slider"]' ).val(response.No_Imagen_Inicio_Slider);
      $( '[name="No_Imagen_Url_Inicio_Slider"]' ).val(response.No_Imagen_Url_Inicio_Slider);
      
      $('[name="No_Slider"]').val(response.No_Slider);
      $('[name="Nu_Orden_Slider"]').val(response.Nu_Orden_Slider);
      $('[name="No_Url_Accion"]').val(response.No_Url_Accion);
      
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
      
      for (var i = 0; i < 2; i++){
        selected = '';
        if(response.Nu_Estado_Slider == i)
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
  Dropzone.prototype.defaultOptions.dictInvalidFileType = "Solo se permite imágenes PNG";
  Dropzone.prototype.defaultOptions.dictCancelUpload = "Cancelar";
  Dropzone.prototype.defaultOptions.dictCancelUploadConfirmation = "¿Estás seguro de cancelar la subida?";
  Dropzone.prototype.defaultOptions.dictRemoveFile = "Eliminar";
  Dropzone.prototype.defaultOptions.dictMaxFilesExceeded = "Solo se puede subir 1 imágen";
    
  url = base_url + 'ImportacionGrupal/BannersGrupal/uploadOnly/' + ID;
  var myDropzone = new Dropzone("#id-divDropzone", {
    url: url,
    params: {
      iVersionImage: (parseInt(Nu_Version_Imagen) + 1),
      iIdEcommerceInicio: ID,
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

            $('[name="No_Imagen_Inicio_Slider"]').val('');
            $('[name="No_Imagen_Url_Inicio_Slider"]').val('');

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

        $('.modal-message').removeClass('modal-danger modal-warning modal-success');
        $('#modal-message').modal('show');

        if (response.sStatus != 'error') {
          $('.modal-message').addClass(response.sClassModal);
          $('.modal-title-message').text(response.sMessage);

          $('[name="No_Imagen_Inicio_Slider"]').val(response.sNombreImagenInicio);
          $('[name="No_Imagen_Url_Inicio_Slider"]').val(response.sNombreImagenInicioUrl);

          setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
        } else {
          $('.modal-message').addClass(response.sClassModal);
          $('.modal-title-message').text(response.sMessage);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 2100);
        }
      })

      if (No_Imagen_Url_Inicio_Slider.length > 0 && No_Imagen_Url_Inicio_Slider != '' && No_Imagen_Url_Inicio_Slider !== undefined) {
        var me = this;
        url = base_url + 'ImportacionGrupal/BannersGrupal/get_image';
        var arrPost = {
          'sUrlImage': No_Imagen_Url_Inicio_Slider,
        }
        $.post(url, arrPost, function (response) {
          $.each(response, function (key, value) {
            var mockfile = value;
            me.emit("addedfile", mockfile);
            me.emit("thumbnail", mockfile, No_Imagen_Url_Inicio_Slider);
            me.emit("complete", mockfile);
          })
        }, 'json');
      }
    }
  })
}

function form_Inicio(){
  if ( accion_familia=='add_familia' || accion_familia=='upd_familia' ) {
    $( '#btn-save' ).text('');
    $( '#btn-save' ).attr('disabled', true);
    $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
  
    $( '#modal-loader' ).modal('show');
    
    url = base_url + 'ImportacionGrupal/BannersGrupal/crudInicio';
  	$.ajax({
      type		  : 'POST',
      dataType	: 'JSON',
  		url		    : url,
  		data		  : $('#form-Inicio').serialize(),
  		success : function( response ){
  		  $( '#modal-loader' ).modal('hide');
  		  
  	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
    	  $( '#modal-message' ).modal('show');
  		  
  		  if (response.status == 'success'){
  		    accion_familia='';
  		    $('#modal-Inicio').modal('hide');
    	    $( '.modal-message' ).addClass(response.style_modal);
    	    $( '.modal-title-message' ).text(response.message);
    	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
          reload_table_inicio_slider();
          reload_table_inicio_slider_mobile();
          reload_table_inicio_ofertas();
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

function eliminarInicio(ID, accion_familia){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });
  
  $(document).bind('keydown', 'esc', function(){
    if ( accion_familia=='delete' ) {
      _eliminarInicio($modal_delete, ID);
      accion_familia='';
    }
  });

  $( '#btn-save-delete' ).off('click').click(function () {
    _eliminarInicio($modal_delete, ID);
  });
}

function _eliminarInicio($modal_delete, ID){
  $( '#modal-loader' ).modal('show');
  
  url = base_url + 'ImportacionGrupal/BannersGrupal/eliminarInicio/' + ID;
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
        reload_table_inicio_slider();
        reload_table_inicio_slider_mobile();
        reload_table_inicio_ofertas();
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

function reload_table_inicio_slider(){
  table_familia.ajax.reload(null,false);
}

function reload_table_inicio_slider_mobile() {
  table_slider_mobile.ajax.reload(null, false);
}

function reload_table_inicio_ofertas() {
  table_oferta.ajax.reload(null, false);
}