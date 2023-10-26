var url;
var table_almacen;

$(function () {
  $('.select2').select2();

  $( '.input-datepicker_todo' ).inputmask('dd/mm/yyyy', { 'placeholder': 'dd/mm/yyyy' });
  $( '.input-datepicker_todo' ).val(fDay + '/' + fMonth + '/' + fYear);
  $( '.input-datepicker_todo' ).datepicker({
    autoclose : true,
    todayHighlight : true
  });

  $(".toggle-password").click(function () {
    $(this).toggleClass("fa-eye fa-eye-slash");
    var $pwd = $(".pwd");
    if ($pwd.attr('type') == 'password') {
      $pwd.attr('type', 'text');
    } else {
      $pwd.attr('type', 'password');
    }
  });

  $(".toggle-password-laeshop").click(function () {
    $(this).toggleClass("fa-eye fa-eye-slash");
    var $pwd = $(".pwd-laeshop");
    if ($pwd.attr('type') == 'password') {
      $pwd.attr('type', 'text');
    } else {
      $pwd.attr('type', 'password');
    }
  });
  
  url = base_url + 'Logistica/ReglasLogistica/AlmacenController/ajax_list';
  table_almacen = $('#table-Almacen').DataTable({
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
      'url'     : url,
      'type'      : 'POST',
      'dataType'  : 'JSON',
      'data'      : function ( data ) {
        data.filtro_empresa = $( '#cbo-filtro_empresa' ).val(),
        data.filtro_organizacion = $('#cbo-filtro_organizacion').val(),
        data.filtro_estado_laegestion = $('#cbo-filtro-estado_laegestion').val(),
        data.filtro_estado_laeshop = $('#cbo-filtro-estado_laeshop').val(),
        data.filtro_tipo_sistema = $('#cbo-filtro-tipo_sistema').val(),
        data.filtro_estado_sistema = $('#cbo-filtro-estado_sistema').val(),
        data.filtro_estado_pago = $('#cbo-filtro-estado_pago').val(),
        data.filtro_estado_pago_laeshop = $('#cbo-filtro-estado_pago_laeshop').val(),
        data.Filtros_Almacenes = $( '#cbo-Filtros_Almacenes' ).val(),
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

  $('.dataTables_length').addClass('col-xs-4 col-sm-5 col-md-1');
  $('.dataTables_info').addClass('col-xs-8 col-sm-7 col-md-4');
  $('.dataTables_paginate').addClass('col-xs-12 col-sm-12 col-md-7');

  $( "#txt-Global_Filter" ).keyup(function() {
    table_almacen.search($(this).val()).draw();
  });
  
  $("#form-Almacen").validate({
		rules:{
			ID_Organizacion: {
				required: true
			},
			ID_Pais: {
				required: true
			},
			ID_Departamento: {
				required: true
			},
			ID_Provincia: {
				required: true
			},
			ID_Distrito: {
				required: true
			},
			No_Almacen: {
				required: true
			},
			Txt_Direccion_Almacen: {
				required: true
      },
      Txt_FE_Ruta: {
				required: true
      },
      Txt_FE_Token: {
				required: true
      },
		},
		messages:{
			ID_Oganizacion:{
				required: "Seleccionar organización",
			},
			ID_Pais:{
				required: "Seleccionar país",
			},
			ID_Departamento:{
				required: "Seleccionar departamento",
			},
			ID_Provincia:{
				required: "Seleccionar provincia",
			},
			ID_Distrito:{
				required: "Seleccionar distrito",
			},
			No_Almacen:{
				required: "Ingresar nombre",
			},
			Txt_Direccion_Almacen:{
				required: "Ingresar dirección",
			},
			Txt_FE_Ruta:{
				required: "Ingresar ruta",
			},
			Txt_FE_Token:{
				required: "Ingresar token",
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
		submitHandler: form_Almacen
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
    table_almacen.search($(this).val()).draw();
  });

	$( '#cbo-filtro_organizacion' ).change(function(){
    table_almacen.search($(this).val()).draw();
  });

  $( '#cbo-filtro-estado_laegestion' ).change(function(){
    table_almacen.search($(this).val()).draw();
  });
  
  $( '#cbo-filtro-estado_laeshop' ).change(function(){
    table_almacen.search($(this).val()).draw();
  });

  $('#cbo-filtro-tipo_sistema').change(function () {
    table_almacen.search($(this).val()).draw();
  });

  $('#cbo-filtro-estado_sistema').change(function () {
    table_almacen.search($(this).val()).draw();
  });

  $('#cbo-filtro-estado_pago').change(function () {
    table_almacen.search($(this).val()).draw();
  });

  $('#cbo-filtro-estado_pago_laeshop').change(function () {
    table_almacen.search($(this).val()).draw();
  });

  $('.div-row-nubefact').hide();
  $('.div-generar-token_lae_fe').hide();
	$( '#cbo-Empresas' ).change(function(){
    $('.div-row-nubefact').hide();
    $('.div-generar-token_lae_fe').hide();

    if ($('#cbo-Empresas').find(':selected').data('nu_tipo_proveedor_fe') == 2)
      $('.div-generar-token_lae_fe').show();

    if($( '#cbo-Empresas' ).find(':selected').data('nu_tipo_proveedor_fe') != 3 )
      $( '.div-row-nubefact' ).show();
    if($( '#cbo-Empresas' ).find(':selected').data('nu_tipo_ecommerce') == 1 )
      $( '.div-row-nubefact' ).show();
    url = base_url + 'HelperController/getOrganizaciones';
    var arrParams = {
      iIdEmpresa : $( this ).val(),
    };
    $.post( url, arrParams, function( response ){
      $( '#cbo-Organizaciones' ).html('<option value="" selected="selected">- Seleccionar -</option>');
      for (var i = 0; i < response.length; i++)
        $( '#cbo-Organizaciones' ).append( '<option value="' + response[i].ID_Organizacion + '">' + response[i].No_Organizacion + '</option>' );    
    }, 'JSON');
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

  $(document).bind('keydown', 'f2', function () {
    agregarAlmacen();
  });
})// ./ function

function agregarAlmacen(){
  $( '.div-row-nubefact' ).hide();

  $( '#form-Almacen' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  
  $( '.help-block' ).empty();
  
  $( '#modal-loader' ).modal('show');
  
  $( '#modal-Almacen' ).modal('show');
  $( '.modal-title' ).text('Nuevo Almacén');
  
  $( '[name="EID_Organizacion"]' ).val('');
  $( '[name="EID_Almacen"]' ).val('');
  $( '[name="ENo_Almacen"]' ).val('');
  
  $( '#cbo-Departamentos' ).html('');
  $( '#cbo-Provincias' ).html('');
  $( '#cbo-Distritos' ).html('');
  
  url = base_url + 'HelperController/getEmpresas';
  $.post( url , function( response ){
    $( '#cbo-Empresas' ).html('<option value="" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++) {
      $( '#cbo-Empresas' ).append( '<option value="' + response[i].ID_Empresa + '" data-nu_tipo_proveedor_fe="' + response[i].Nu_Tipo_Proveedor_FE + '" data-nu_tipo_ecommerce="' + response[i].Nu_Tipo_Ecommerce + '">' + response[i].No_Empresa + ' (' + response[i].No_Descripcion_Proveedor_FE + ')</option>' );
    }
    $( '#modal-loader' ).modal('hide');
  }, 'JSON');

  $( '#cbo-Organizaciones' ).html('<option value="" selected="selected">- Seleccionar -</option>');

  $('.div-row-nubefact').hide();
  $('.div-generar-token_lae_fe').hide();

  if ($('#hidden-Nu_Agregar_Almacen_Virtual').val()==0 && $('#cbo-Empresas').find(':selected').data('nu_tipo_proveedor_fe') == 2)
    $('.div-generar-token_lae_fe').show();

  if ($('#hidden-Nu_Agregar_Almacen_Virtual').val() == 0 && $('#cbo-Empresas').find(':selected').data('nu_tipo_proveedor_fe') != 3)
    $('.div-row-nubefact').show();

  if ($('#hidden-Nu_Agregar_Almacen_Virtual').val() == 0 && $('#cbo-Empresas').find(':selected').data('nu_tipo_ecommerce') == 1)
    $('.div-row-nubefact').show();

  url = base_url + 'HelperController/getOrganizaciones';
  var arrParams = {
    iIdEmpresa: $('#cbo-Empresas').val(),
  };
  $.post(url, arrParams, function (response) {
    $('#cbo-Organizaciones').html('<option value="" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++)
      $('#cbo-Organizaciones').append('<option value="' + response[i].ID_Organizacion + '">' + response[i].No_Organizacion + '</option>');
  }, 'JSON');

  url = base_url + 'HelperController/getPaises';
  $.post(url, function (response) {
    if (response.length == 1) {
      $('#cbo-Paises').html('<option value="' + response[0].ID_Pais + '">' + response[0].No_Pais + '</option>');

      url = base_url + 'HelperController/getDepartamentos';
      $.post(url, { ID_Pais: response[0].ID_Pais }, function (response) {
        $('#cbo-Departamentos').html('<option value="" selected="selected">- Seleccionar -</option>');
        for (var i = 0; i < response.length; i++)
          $('#cbo-Departamentos').append('<option value="' + response[i].ID_Departamento + '">' + response[i].No_Departamento + '</option>');
      }, 'JSON');
    } else {
      $('#cbo-Paises').html('<option value="" selected="selected">- Seleccionar -</option>');
      for (var i = 0; i < response.length; i++)
        $('#cbo-Paises').append('<option value="' + response[i].ID_Pais + '">' + response[i].No_Pais + '</option>');
    }
    $('#modal-loader').modal('hide');
  }, 'JSON');
  
	$( '#modal-Almacen' ).on('shown.bs.modal', function() {
		$( '#txt-No_Almacen' ).focus();
	})
  
  $( '#cbo-Estado' ).html( '<option value="1">Activo</option>' );
  $( '#cbo-Estado' ).append( '<option value="0">Inactivo</option>' );
  
  $( '#cbo-Estado_Pago_Sistema' ).html( '<option value="1">Cancelado</option>' );
  $( '#cbo-Estado_Pago_Sistema' ).append( '<option value="0">Pendiente</option>' );
  
  $( '#cbo-Estado_Pago_Sistema_Laeshop' ).html( '<option value="1">Cancelado</option>' );
  $( '#cbo-Estado_Pago_Sistema_Laeshop' ).append( '<option value="0">Pendiente</option>' );

  /* obtener imagen guardada(s) */
  $('.divDropzone').html(
    '<div id="id-divDropzone" class="dropzone div-dropzone">'
    + '<div class="dz-message">'
    + 'Arrastrar o presionar click para subir imágen'
    + '</div>'
    + '</div>'
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

  url = base_url + 'Logistica/ReglasLogistica/AlmacenController/uploadOnly';
  var myDropzone = new Dropzone("#id-divDropzone", {
    url: url,
    params: {
      iVersionImage: 1,
      iIdFamilia: 1,
    },
    acceptedFiles: ".jpeg,.jpg,.png",
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
      url = base_url + 'Logistica/ReglasLogistica/AlmacenController/removeFileImage';
      $.ajax({
        url: url,
        type: "POST",
        dataType: "JSON",
        data: { nameFileImage: nameFileImage },
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

          $('[name="No_Logo_Almacen"]').val(response.sNombreLogoAlmacen);
          $('[name="No_Logo_Url_Almacen"]').val(response.sNombreImagenLogoAlmacenUrl);

          setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
        } else {
          $('.modal-message').addClass(response.sClassModal);
          $('.modal-title-message').text(response.sMessage);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1200);
        }
      })
    },
  });
  
  url = base_url + 'HelperController/getValoresTablaDato';
  var arrParams = {
    sTipoData : 'Ubigeo_INEI',
  }
  $.post( url, arrParams, function( response ){
    if ( response.sStatus == 'success' ) {
      var iTotalRegistros = response.arrData.length, response=response.arrData;
      $( '#cbo-ubigeo_inei' ).html( '<option value="" selected="selected">- Seleccionar -</option>');
      for (var i = 0; i < iTotalRegistros; i++)
        $( '#cbo-ubigeo_inei' ).append( '<option value="' + response[i].ID_Tabla_Dato + '">' + response[i].Nu_Valor + ': ' + response[i].No_Descripcion + '</option>' );
    } else {
      $( '#cbo-ubigeo_inei' ).html( '<option value="" selected="selected">- Vacío -</option>');
      console.log( response );
    }
  }, 'JSON');
}

function verAlmacen(ID, No_Logo_Almacen, No_Logo_Url_Almacen){
  $( '#form-Almacen' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '#modal-loader' ).modal('show');
 
  url = base_url + 'Logistica/ReglasLogistica/AlmacenController/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $( '#modal-Almacen' ).modal('show');
      $( '.modal-title' ).text('Modificar Almacén');
  
    	$( '#modal-Almacen' ).on('shown.bs.modal', function() {
    		$( '#txt-No_Almacen' ).focus();
    	})
      
      $('[name="EID_Organizacion"]').val(response.ID_Organizacion);
      $('[name="EID_Almacen"]').val(response.ID_Almacen);
      $('[name="ENo_Almacen"]').val(response.No_Almacen);
      $('[name="No_Logo_Almacen"]').val(response.No_Logo_Almacen);
      $('[name="No_Logo_Url_Almacen"]').val(response.No_Logo_Url_Almacen);
      
      $('[name="No_Almacen"]').val(response.No_Almacen);
      $('[name="Nu_Codigo_Establecimiento_Sunat"]').val(response.Nu_Codigo_Establecimiento_Sunat);
      $('[name="Txt_Direccion_Almacen"]').val(response.Txt_Direccion_Almacen);
      
      var selected;
      url = base_url + 'HelperController/getEmpresas';
      $.post( url , function( responseEmpresa ){
        $( '#cbo-Empresas' ).html('<option value="" selected="selected">- Seleccionar -</option>');
        for (var i = 0; i < responseEmpresa.length; i++) {
          selected = '';
          if(response.ID_Empresa == responseEmpresa[i].ID_Empresa)
            selected = 'selected="selected"';
          $( '#cbo-Empresas' ).append( '<option value="' + responseEmpresa[i].ID_Empresa + '" data-nu_tipo_proveedor_fe="' + responseEmpresa[i].Nu_Tipo_Proveedor_FE + '" data-nu_tipo_ecommerce="' + responseEmpresa[i].Nu_Tipo_Ecommerce + '"  ' + selected + '>' + responseEmpresa[i].No_Empresa + ' (' + responseEmpresa[i].No_Descripcion_Proveedor_FE + ')</option>' );
        }
      }, 'JSON');

      $( '.div-row-nubefact' ).hide();
      if( response.Nu_Tipo_Proveedor_FE != 3)
        $( '.div-row-nubefact' ).show();

      url = base_url + 'HelperController/getOrganizaciones';
      var arrParams = {
        iIdEmpresa: response.ID_Empresa,
      };
      $.post( url, arrParams, function( responseOrganizaciones ){
        $( '#cbo-Organizaciones' ).html( '' );
        for (var i = 0; i < responseOrganizaciones.length; i++){
          selected = '';
          if(response.ID_Organizacion == responseOrganizaciones[i].ID_Organizacion)
            selected = 'selected="selected"';
          $( '#cbo-Organizaciones' ).append( '<option value="' + responseOrganizaciones[i].ID_Organizacion + '" ' + selected + '>' + responseOrganizaciones[i].No_Organizacion + '</option>' );
        }
        $( '#modal-loader' ).modal('hide');
      }, 'JSON');
      
      url = base_url + 'HelperController/getPaises';
      $.post( url , function( responsePais ){
        $( '#cbo-Paises' ).html('');
        for (var i = 0; i < responsePais.length; i++){
          selected = '';
          if(response.ID_Pais == responsePais[i]['ID_Pais'])
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
        $( '#modal-loader' ).modal('hide');
        $( '#cbo-Distritos' ).html('');
        for (var i = 0; i < responseDistrito.length; i++){
          selected = '';
          if(response.ID_Distrito == responseDistrito[i].ID_Distrito)
            selected = 'selected="selected"';
          $( '#cbo-Distritos' ).append( '<option value="' + responseDistrito[i].ID_Distrito + '" ' + selected + '>' + responseDistrito[i].No_Distrito + '</option>' );
        }
      }, 'JSON');
      
      url = base_url + 'HelperController/getValoresTablaDato';
      var arrParams = {
        sTipoData : 'Ubigeo_INEI',
      }
      $.post( url, arrParams, function( responseUbigeo ){
        if ( responseUbigeo.sStatus == 'success' ) {
          var iTotalRegistros = responseUbigeo.arrData.length, responseUbigeo=responseUbigeo.arrData;
          $( '#cbo-ubigeo_inei' ).html( '<option value="" selected="selected">- Seleccionar -</option>');
          for (var i = 0; i < iTotalRegistros; i++){
            selected = '';
            if(response.ID_Ubigeo_Inei_Partida == responseUbigeo[i].ID_Tabla_Dato)
              selected = 'selected="selected"';
            $( '#cbo-ubigeo_inei' ).append( '<option value="' + responseUbigeo[i].ID_Tabla_Dato + '" ' + selected + '>' + responseUbigeo[i].Nu_Valor + ': ' + responseUbigeo[i].No_Descripcion + '</option>' );
          }

          //1444 = LIMA LIMA LIMA UBIGEO
          if(response.ID_Ubigeo_Inei_Partida==0) {
            $('#cbo-ubigeo_inei').val('1444');
            $('#cbo-ubigeo_inei').select().trigger('change');
          }
        } else {
          $( '#cbo-ubigeo_inei' ).html( '<option value="" selected="selected">- Vacío -</option>');
          console.log( responseUbigeo );
        }
      }, 'JSON');
      
      $( '#cbo-Estado_Pago_Sistema' ).html( '' );
      for (var i = 0; i < 2; i++){
        selected = '';
        if(response.Nu_Estado_Pago_Sistema == i)
          selected = 'selected="selected"';
        $( '#cbo-Estado_Pago_Sistema' ).append( '<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Pendiente' : 'Cancelado') + '</option>' );
      }
      
      if(response.Fe_Vencimiento_LaeGestion != null && response.Fe_Vencimiento_LaeGestion != '')
        $('[name="Fe_Vencimiento_LaeGestion"]').val(ParseDateString(response.Fe_Vencimiento_LaeGestion, 6, '-'));

      $( '#cbo-Estado_Pago_Sistema_Laeshop' ).html( '' );
      for (var i = 0; i < 2; i++){
        selected = '';
        if(response.Nu_Estado_Pago_Sistema_Laeshop == i)
          selected = 'selected="selected"';
        $( '#cbo-Estado_Pago_Sistema_Laeshop' ).append( '<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Pendiente' : 'Cancelado') + '</option>' );
      }

      if(response.Fe_Vencimiento_Laeshop != null && response.Fe_Vencimiento_Laeshop != '')
        $('[name="Fe_Vencimiento_Laeshop"]').val(ParseDateString(response.Fe_Vencimiento_Laeshop, 6, '-'));

      $( '#cbo-Estado' ).html( '' );
      for (var i = 0; i < 2; i++){
        selected = '';
        if(response.Nu_Estado == i)
          selected = 'selected="selected"';
        $( '#cbo-Estado' ).append( '<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Inactivo' : 'Activo') + '</option>' );
      }
      
      $( '[name="Txt_FE_Ruta"]' ).val(response.Txt_FE_Ruta);
      $( '[name="Txt_FE_Token"]' ).val(response.Txt_FE_Token);
      
      $( '[name="Nu_Latitud_Maps"]' ).val(response.Nu_Latitud_Maps);
      $( '[name="Nu_Longitud_Maps"]' ).val(response.Nu_Longitud_Maps);
      
      $( '[name="Txt_Ruta_Lae_Shop"]' ).val(response.Txt_Ruta_Lae_Shop);
      $( '[name="Txt_Token_Lae_Shop"]' ).val(response.Txt_Token_Lae_Shop);
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
  $('.divDropzone').html(
    '<div id="id-divDropzone" class="dropzone div-dropzone">'
    + '<div class="dz-message">'
    + 'Arrastrar o presionar click para subir imágen'
    + '</div>'
    + '</div>'
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

  url = base_url + 'Logistica/ReglasLogistica/AlmacenController/uploadOnly/' + ID;
  var myDropzone = new Dropzone("#id-divDropzone", {
    url: url,
    params: {
      iVersionImage: 0,//(parseInt(Nu_Version_Imagen) + 1),
      iIdFamilia: ID,
    },
    acceptedFiles: ".jpeg,.jpg,.png",
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
      var arrName = file.name.split('/');
      var nameFileImage;
      if (arrName.length === 4)//Si la imagen ya está en el server
        nameFileImage = arrName[3];
      else//Si la imagén recién la vamos a subir y no existe en el server
        nameFileImage = arrName[0];
      url = base_url + 'Logistica/ReglasLogistica/AlmacenController/removeFileImage';
      $.ajax({
        url: url,
        type: "POST",
        dataType: "JSON",
        data: { nameFileImage: nameFileImage },
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

          $('[name="No_Logo_Almacen"]').val(response.sNombreImagenAlmacen);
          $('[name="No_Logo_Url_Almacen"]').val(response.sNombreImagenAlmacenUrl);

          setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
        } else {
          $('.modal-message').addClass(response.sClassModal);
          $('.modal-title-message').text(response.sMessage);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1200);
        }
      })

      var me = this;
      url = base_url + 'Logistica/ReglasLogistica/AlmacenController/get_image';
      var arrPost = {
        'sNombreImage': No_Logo_Almacen,
        'sUrlImage': No_Logo_Url_Almacen,
      }
      $.post(url, arrPost, function (response) {
        $.each(response, function (key, value) {
          var mockfile = value;
          me.emit("addedfile", mockfile);
          me.emit("thumbnail", mockfile, No_Logo_Url_Almacen);
          me.emit("complete", mockfile);
        })
      }, 'json');
    }
  })
}

function form_Almacen(){
  $( '#btn-save' ).text('');
  $( '#btn-save' ).attr('disabled', true);
  $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

  $( '#modal-loader' ).modal('show');
  
  url = base_url + 'Logistica/ReglasLogistica/AlmacenController/crudAlmacen';
  $.ajax({
    type		  : 'POST',
    dataType	: 'JSON',
    url		    : url,
    data		  : $('#form-Almacen').serialize(),
    success : function( response ){
      $( '#modal-loader' ).modal('hide');
      
      $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
      $( '#modal-message' ).modal('show');
      
      if (response.status == 'success'){
        $('#modal-Almacen').modal('hide');
        $( '.modal-message' ).addClass(response.style_modal);
        $( '.modal-title-message' ).text(response.message);
        setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
        reload_table_almacen();
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

function eliminarAlmacen(ID){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });
  
  $( '#btn-save-delete' ).off('click').click(function () {
    $( '#modal-loader' ).modal('show');
    
    url = base_url + 'Logistica/ReglasLogistica/AlmacenController/eliminarAlmacen/' + ID;
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
    	    reload_table_almacen();
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

function reload_table_almacen(){
  table_almacen.ajax.reload(null,false);
}

function cambiarEstadoPago(ID, Nu_Estado) {
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

    url = base_url + 'Logistica/ReglasLogistica/AlmacenController/cambiarEstadoPago/' + ID + '/' + Nu_Estado;
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
          reload_table_almacen();
        } else {
          $('.modal-message').addClass(response.style_modal);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1500);
        }
      }
    });
  });
}

function cambiarEstadoPagoLaeshop(ID, Nu_Estado) {
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

    url = base_url + 'Logistica/ReglasLogistica/AlmacenController/cambiarEstadoPagoLaeshop/' + ID + '/' + Nu_Estado;
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
          reload_table_almacen();
        } else {
          $('.modal-message').addClass(response.style_modal);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1500);
        }
      }
    });
  });
}


function initAutocomplete(lat = 48, lng = 4) {
  /*
  var map = new google.maps.Map(document.getElementById('map'), {
    center: {
      lat: lat,
      lng: lng
    },
    zoom: 13,
    disableDefaultUI: true
  });

  var geocoder = new google.maps.Geocoder();

  var input = document.getElementById('txt-direccion');
  var autocomplete = new google.maps.places.Autocomplete(input);
  map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);
  var marker = new google.maps.Marker({
    map: map
  });

  autocomplete.bindTo('bounds', map);
  autocomplete.setFields(['address_components', 'geometry', 'name']);

  autocomplete.addListener('place_changed', function() {
    var place = autocomplete.getPlace();
    if (!place.geometry) {
      alert("Si no encuentras tu dirección, prueba buscando primero tu provincia");
      return;
    }
    var bounds = new google.maps.LatLngBounds();
    marker.setPosition(place.geometry.location);
	
    if (place.geometry.viewport) {
      bounds.union(place.geometry.viewport);
      $('#map').css("height", '160px');

      document.getElementById('txt-direccion-lat').value = place.geometry.location.lat();
      document.getElementById('txt-direccion-lng').value = place.geometry.location.lng();
    } else {
      bounds.extend(place.geometry.location);
    }
    map.fitBounds(bounds);
  });
  */
}

/*
document.addEventListener("DOMContentLoaded", function(event) {
  initAutocomplete();
});
*/