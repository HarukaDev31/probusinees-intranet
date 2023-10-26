var url;
var table_serie;
var accion_serie;

$(function () {
	$(document).keyup(function(event){
    if(event.which == 27){//ESC
      $( "#modal-Pos" ).modal('hide');
    }
	});
  
	$(".toggle-password").click(function() {
		$(this).toggleClass("fa-eye fa-eye-slash");
    var $pwd = $(".pwd");
    if ($pwd.attr('type') == 'password') {
      $pwd.attr('type', 'text');
    } else {
      $pwd.attr('type', 'password');
    }
  });
  
	$(".toggle-password_key_hdd").click(function() {
		$(this).toggleClass("fa-eye fa-eye-slash");
    var $pwd = $(".pwd_key_hdd");
    if ($pwd.attr('type') == 'password') {
      $pwd.attr('type', 'text');
    } else {
      $pwd.attr('type', 'password');
    }
  });

  $('#cbo-filtro_almacen').html('<option value="0" selected="selected">- Todos -</option>');
  url = base_url + 'HelperController/getAlmacenes';
  var arrParams = {
    iIdOrganizacion: $('#header-a-id_organizacion').val(),
  };
  $.post(url, arrParams, function (response) {
    $('#cbo-filtro_almacen').html('<option value="0" selected="selected">- Todos -</option>');
    for (var i = 0; i < response.length; i++)
      $('#cbo-filtro_almacen').append('<option value="' + response[i].ID_Almacen + '">' + response[i].No_Almacen + '</option>');
  }, 'JSON');
		
  url = base_url + 'Ventas/PosController/ajax_list';
  table_serie = $('#table-Pos').DataTable({
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
      'url'       : url,
      'type'      : 'POST',
      'dataType'  : 'JSON',
      'data'      : function ( data ) {
        data.filtro_empresa = $( '#cbo-filtro_empresa' ).val(),
        data.filtro_organizacion = $('#cbo-filtro_organizacion').val(),
        data.filtro_almacen = $('#cbo-filtro_almacen').val();
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

  $( '#btn-filter' ).click(function(){
    table_serie.ajax.reload();
  });
  
  $( "#form-Pos" ).validate({
		rules:{
			Nu_Pos: {
				required: true
			},
		},
		messages:{
			Nu_Pos:{
				required: "Ingresar nro. caja",
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
		submitHandler: form_Pos
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
    table_serie.search($(this).val()).draw();
  });

	$( '#cbo-filtro_organizacion' ).change(function(){
    table_serie.search($(this).val()).draw();
  });

	$( '#cbo-Empresas' ).change(function(){
    url = base_url + 'HelperController/getOrganizaciones';
    var arrParams = {
      iIdEmpresa: $(this).val(),
    }
    $.post(url, arrParams, function (response) {
      if (response.length == 1) {
        $('#cbo-Organizaciones').html('<option value="' + response[0].ID_Organizacion + '">' + response[0].No_Organizacion + '</option>');
      } else {
        $('#cbo-Organizaciones').html('<option value="" selected="selected">- Seleccionar -</option>');
        for (var i = 0; i < response.length; i++)
          $('#cbo-Organizaciones').append('<option value="' + response[i].ID_Organizacion + '">' + response[i].No_Organizacion + '</option>');
      }
    }, 'JSON');
  });

  $(document).bind('keydown', 'f2', function(){
    agregarPos();
  });
})

function agregarPos(){
  accion_serie='add_serie';
  
  $( '#form-Pos' )[0].reset();
  
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  
  $( '.help-block' ).empty();
  
  $( '#modal-Pos' ).modal('show');
  
  $( '.modal-title' ).text('Nueva Pos');
  
  $('.div-add_serie').show();

  $( '[name="EID_Empresa"]' ).val('');
  $( '[name="EID_Organizacion"]' ).val('');
  $( '[name="EID_Pos"]' ).val('');
  $( '[name="ENu_Pos"]' ).val('');
  $( '[name="ENo_Pos"]' ).val('');
  
	$( '#modal-Pos' ).on('shown.bs.modal', function() {
		$( '#txt-ID_Pos_Documento' ).focus();
	})
  
  url = base_url + 'HelperController/getEmpresas';
  $.post( url , function( response ){
    $( '#cbo-Empresas' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-Empresas' ).append( '<option value="' + response[i].ID_Empresa + '">' + response[i].No_Empresa + '</option>' );
    $( '#modal-loader' ).modal('hide');
  }, 'JSON');

  $('#cbo-Organizaciones').html('<option value="" selected="selected">- Vacio -</option>');
  
  $( '#cbo-Estado' ).html( '<option value="1">Activo</option>' );
  $( '#cbo-Estado' ).append( '<option value="0">Inactivo</option>' );
}

function verPos(ID_POS){
  accion_serie='upd_serie';
  
  $( '#form-Pos' )[0].reset();
  

  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  
  $( '.help-block' ).empty();
  
  $( '#modal-loader' ).modal('show');
 
  url = base_url + 'Ventas/PosController/ajax_edit/' + ID_POS;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $( '#modal-loader' ).modal('hide');
      
      $( '#modal-Pos' ).modal('show');
      
      $('.div-add_serie').hide();

      $( '.modal-title' ).text('Modifcar Pos');
      
      $( '[name="EID_Empresa"]' ).val( response.ID_Empresa );
      $( '[name="EID_Organizacion"]' ).val( response.ID_Organizacion );
      $( '[name="EID_Pos"]' ).val( response.ID_POS );
      $( '[name="ENu_Pos"]' ).val( response.Nu_Pos );
      $( '[name="ENo_Pos"]' ).val( response.No_Pos );
      
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
      
      url = base_url + 'HelperController/getOrganizaciones';
      var arrParams = {
        iIdEmpresa : response.ID_Empresa,
      }
      $.post( url, arrParams, function( responseOrganizacion ){
        if (response.length == 1) {
          $( '#cbo-Organizaciones' ).html( '<option value="' + responseOrganizacion[0].ID_Organizacion + '">' + responseOrganizacion[0].No_Organizacion + '</option>' );
        } else {
          for (var i = 0; i < responseOrganizacion.length; i++){
            selected = '';
            if(response.ID_Organizacion == responseOrganizacion[i].ID_Organizacion)
              selected = 'selected="selected"';
            $( '#cbo-Organizaciones' ).append( '<option value="' + responseOrganizacion[i].ID_Organizacion + '" ' + selected + '>' + responseOrganizacion[i].No_Organizacion + '</option>' );
          }
        }
      }, 'JSON');
      
      $( '[name="Nu_Pos"]' ).val( response.Nu_Pos );
      $( '[name="No_Pos"]' ).val( response.No_Pos );
      
      $( '#cbo-Estado' ).html( '' );
      for (var i = 0; i < 2; i++){
        selected = '';
        if(response.Nu_Estado == i)
          selected = 'selected="selected"';
        $( '#cbo-Estado' ).append( '<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Inactivo' : 'Activo') + '</option>' );
      }

      $( '[name="Txt_Autorizacion_Venta_Serie_Disco_Duro"]' ).val( response.Txt_Autorizacion_Venta_Serie_Disco_Duro );
      $( '[name="Key_Serie_Disco_Duro"]' ).val( response.Key_Serie_Disco_Duro );
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

function form_Pos(){
  if ( accion_serie=='add_serie' || accion_serie=='upd_serie' ) {
    if ( $( '#cbo-Empresas' ).val() == 0){
      $( '#cbo-Empresas' ).closest('.form-group').find('.help-block').html('Seleccionar empresa');
  	  $( '#cbo-Empresas' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $( '#cbo-Organizaciones' ).val() == 0){
      $( '#cbo-Organizaciones' ).closest('.form-group').find('.help-block').html('Seleccionar organización');
  	  $( '#cbo-Organizaciones' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $( '[name="Nu_Pos"]' ).val().length == 0 ){
  		$( '[name="Nu_Pos"]' ).closest('.form-group').find('.help-block').html('Ingresar nro. caja');
  		$( '[name="Nu_Pos"]' ).closest('.form-group').removeClass('has-success').addClass('has-error');
  	} else {
      $( '#btn-save' ).text('');
      $( '#btn-save' ).attr('disabled', true);
      $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
    
      $( '#modal-loader' ).modal('show');
      
      url = base_url + 'Ventas/PosController/crudPos';
    	$.ajax({
        type		  : 'POST',
        dataType	: 'JSON',
    		url		    : url,
    		data		  : $('#form-Pos').serialize(),
    		success : function( response ){
    		  $( '#modal-loader' ).modal('hide');
  
    	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
      	  $( '#modal-message' ).modal('show');
    		  
    		  if (response.status == 'success'){
    		    accion_serie='';
    		    
    		    $('#modal-Pos').modal('hide');
      	    $( '.modal-message' ).addClass(response.style_modal);
      	    $( '.modal-title-message' ).text(response.message);
      	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
      	    reload_table_serie();
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
}

function eliminarPos(ID_Pos, accion_serie){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });
  
  $(document).bind('keydown', 'return', function(){
    if ( accion_serie=='delete' ) {
      _eliminarPos($modal_delete, ID_Pos);
      accion_serie='';
    }
  });

  $( '#btn-save-delete' ).off('click').click(function () {
    _eliminarPos($modal_delete, ID_Pos);
  });
}

function _eliminarPos($modal_delete, ID_Pos){
  $( '#modal-loader' ).modal('show');
  
  url = base_url + 'Ventas/PosController/eliminarPos/' + ID_Pos;
  $.ajax({
    url       : url,
    type      : "GET",
    dataType  : "JSON",
    success: function( response ){
      $( '#modal-loader' ).modal('hide');//modal del cargador
      $modal_delete.modal('hide');//modal del mensaje de eliminación
      
	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
  	  $( '#modal-message' ).modal('show');
		  
		  if (response.status == 'success'){
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
  	    reload_table_serie();
		  } else {
		    $( '#txt-ID_Pos_Documento' ).val('');
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
		  }
		  accion_serie='';
    },
    error: function (jqXHR, textStatus, errorThrown) {
		  accion_serie='';
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

function reload_table_serie(){
  table_serie.ajax.reload(null,false);
}
