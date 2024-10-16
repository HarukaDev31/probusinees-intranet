var url;
var table_perfil_usuario;
var accion_perfil_usuario;

$(function () {
  $('.select2').select2();

	$(document).keyup(function(event){
    if(event.which == 27){//ESC
      $( "#modal-Perfil_Usuario" ).modal('hide');
    }
	});
  
  url = base_url + 'PanelAcceso/PerfilUsuarioController/ajax_list';
  table_perfil_usuario = $( '#table-Perfil_Usuario' ).DataTable({
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
        data.filtro_empresa = $( '#cbo-filtro_empresa' ).val(),
        data.filtro_organizacion = $( '#cbo-filtro_organizacion' ).val(),
        data.Perfil_Usuario = $( '#cbo-Filtros_Perfil_Usuario' ).val(),
        data.Global_Filter  = $( '#txt-Global_Filter' ).val();
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
    table_perfil_usuario.search($(this).val()).draw();
  });
  
  $("#form-Perfil_Usuario").validate({
		rules:{
			No_Grupo: {
				required: true
			},
		},
		messages:{
			No_Grupo:{
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
		submitHandler: form_perfil_usuario
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
    table_perfil_usuario.search($(this).val()).draw();
  });

	$( '#cbo-filtro_organizacion' ).change(function(){
    table_perfil_usuario.search($(this).val()).draw();
  });
	
	$( '#cbo-Empresas' ).change(function(){
    $( '#modal-loader' ).modal('show');
    url = base_url + 'HelperController/getOrganizaciones';
    var arrParams = {
      iIdEmpresa : $( this ).val(),
    };
    $.post( url, arrParams, function( response ){
      $( '#cbo-organizacion' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
      for (var i = 0; i < response.length; i++)
        $( '#cbo-organizacion' ).append( '<option value="' + response[i].ID_Organizacion + '">' + response[i].No_Organizacion + '</option>' );    
      $( '#modal-loader' ).modal('hide');
    }, 'JSON');
  });

  $(document).bind('keydown', 'f2', function(){
    agregarPerfilUsuario();
  });
})

function agregarPerfilUsuario(){
  accion_perfil_usuario='add_perfil_usuario';
  
  $( '#modal-loader' ).modal('show');
  
  $( '#form-Perfil_Usuario' )[0].reset();
  
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '#modal-Perfil_Usuario' ).modal('show');
  
  $( '.modal-title' ).text('Nuevo Perfil Usuario');
  
  $( '[name="EID_Empresa"]' ).val('');
  $( '[name="EID_Organizacion"]' ).val('');

  url = base_url + 'HelperController/getEmpresas';
  $.post( url , function( response ){
    $( '#cbo-Empresas' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-Empresas' ).append( '<option value="' + response[i].ID_Empresa + '">' + response[i].No_Empresa + '</option>' );
    $( '#modal-loader' ).modal('hide');
  }, 'JSON');
  
  $( '#cbo-organizacion' ).html('<option value="0" selected="selected">- Seleccionar -</option>');

	$( '#modal-Perfil_Usuario' ).on('shown.bs.modal', function() {
		$( '[name="No_Grupo"]' ).focus();
	})

  $( '[name="EID_Grupo"]' ).val( '' );
  $( '[name="ENo_Grupo"]' ).val( '' );

  $( '#cbo-Estado' ).html( '<option value="1">Activo</option>' );
  $( '#cbo-Estado' ).append( '<option value="0">Inactivo</option>' );
}

function verPerfilUsuario(ID_Grupo){
  accion_perfil_usuario='upd_perfil_usuario';
  
  $( '#form-Perfil_Usuario' )[0].reset();
  
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '#modal-loader' ).modal('show');
 
  url = base_url + 'PanelAcceso/PerfilUsuarioController/ajax_edit/' + ID_Grupo;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $( '#modal-loader' ).modal('hide');
          
      $( '[name="EID_Empresa"]' ).val( response.ID_Empresa );
      $( '[name="EID_Organizacion"]' ).val( response.ID_Organizacion );
      $( '[name="EID_Grupo"]' ).val( response.ID_Grupo );
      $( '[name="ENo_Grupo"]' ).val( response.No_Grupo );
      
    	$( '#modal-Perfil_Usuario' ).on('shown.bs.modal', function() {
    		$( '[name="No_Grupo"]' ).focus();
    	})
	
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
      $.post( url, arrParams, function( responseGrupo ){
        $( '#cbo-organizacion' ).html('');
        for (var i = 0; i < responseGrupo.length; i++){
          selected = '';
          if(response.ID_Organizacion == responseGrupo[i].ID_Organizacion)
            selected = 'selected="selected"';
          $( '#cbo-organizacion' ).append( '<option value="' + responseGrupo[i].ID_Organizacion + '" ' + selected + '>' + responseGrupo[i].No_Organizacion + '</option>' );
        }
        $( '#modal-loader' ).modal('hide');
      }, 'JSON');

      $( '[name="No_Grupo"]' ).val( response.No_Grupo );
      $( '[name="No_Grupo_Descripcion"]' ).val( response.No_Grupo_Descripcion );
      
      $( '#cbo-Estado' ).html( '' );
      for (var i = 0; i < 2; i++){
        selected = '';
        if(response.Nu_Estado == i)
          selected = 'selected="selected"';
        $( '#cbo-Estado' ).append( '<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Inactivo' : 'Activo') + '</option>' );
      }
      
      $( '#modal-Perfil_Usuario' ).modal('show');
      $( '.modal-title' ).text('Modifcar Perfil Usuario');
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

function form_perfil_usuario(){
  if ( accion_perfil_usuario=='add_perfil_usuario' || accion_perfil_usuario=='upd_perfil_usuario' ) {
    if ( $( '#cbo-organizacion' ).val() == 0 ){
  		$( '#cbo-organizacion' ).closest('.form-group').find('.help-block').html('Seleccionar organizacion');
  		$( '#cbo-organizacion' ).closest('.form-group').removeClass('has-success').addClass('has-error');
  	} else if ( $( '#txt-No_Grupo' ).val().length == 0 ){
  		$( '#txt-No_Grupo' ).closest('.form-group').find('.help-block').html('Ingresar perfil');
  		$( '#txt-No_Grupo' ).closest('.form-group').removeClass('has-success').addClass('has-error');
  	} else {
      $( '#btn-save' ).text('');
      $( '#btn-save' ).attr('disabled', true);
      $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
    
      $( '#modal-loader' ).modal('show');
      
      url = base_url + 'PanelAcceso/PerfilUsuarioController/crudPerfilUsuario';
    	$.ajax({
        type		  : 'POST',
        dataType	: 'JSON',
    		url		    : url,
    		data		  : $('#form-Perfil_Usuario').serialize(),
    		success : function( response ){
    		  $( '#modal-loader' ).modal('hide');
    		  
    	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
      	  $( '#modal-message' ).modal('show');
    		  
    		  if (response.status == 'success'){
    		    accion_perfil_usuario='';
    		    
    		    $('#modal-Perfil_Usuario').modal('hide');
      	    $( '.modal-message' ).addClass(response.style_modal);
      	    $( '.modal-title-message' ).text(response.message);
      	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
      	    reload_table_peril_usuario();
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

function eliminarPerfilUsuario(ID, accion_perfil_usuario){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });
  
  $(document).bind('keydown', 'alt+l', function(){
    if ( accion_perfil_usuario=='delete' ) {
      _eliminarPerfilUsuario($modal_delete, ID);
      accion_perfil_usuario='';
    }
  });

  $( '#btn-save-delete' ).off('click').click(function () {
    _eliminarPerfilUsuario($modal_delete, ID);
  });
}

function _eliminarPerfilUsuario($modal_delete, ID){
  $( '#modal-loader' ).modal('show');
  
  url = base_url + 'PanelAcceso/PerfilUsuarioController/eliminarPerfilUsuario/' + ID;
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
  	    reload_table_peril_usuario();
		  } else {
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
		  }
		  accion_perfil_usuario='';
    },
    error: function (jqXHR, textStatus, errorThrown) {
      accion_perfil_usuario='';
      
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

function reload_table_peril_usuario(){
  table_perfil_usuario.ajax.reload(null,false);
}