var url;
var table;
var method;
var accion_usuario;

$(function () {
  $('[data-mask]').inputmask();
  
	$(document).keyup(function(event){
    if(event.which == 27){//ESC
      $( "#modal-Usuario" ).modal('hide');
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
  
  $('.select2').select2();
  url = base_url + 'PanelAcceso/UsuarioController/ajax_list';
  table = $( '#table-Usuario' ).DataTable({
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
      'dataType'  : 'json',
      'data'      : function ( data ) {
        data.filtro_empresa = $( '#cbo-filtro_empresa' ).val(),
        data.filtro_organizacion = $( '#cbo-filtro_organizacion' ).val(),
        data.Usuario = $( '#cbo-Filtros_Usuario' ).val(),
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

  $( '#txt-Global_Filter' ).keyup(function() {
    table.search($(this).val()).draw();
  });
  
  $("#form-Usuario").validate({
		rules:{
			ID_Grupo: {
				required: true
			},
			No_Usuario: {
				required: true,
				//validemail: true
			},
			No_Password: {
				required: true,
			},
			RNo_Password: {
				required: true,
				equalTo: '#No_Password'
			},
			Nu_Celular: {
				minlength: 11,
				maxlength: 11
			},
			Txt_Email: {
				required: true,
			  //validemail: true
			},
		},
		messages:{
			ID_Grupo:{
				required: "Seleccionar grupo",
			},
			No_Usuario:{
				required: "Ingresar usuario",
				//validemail: "Ingresar correo válido",
			},
			No_Password:{
				required: "Ingresar contraseña",
			},
			RNo_Password:{
				required: "Repetir contraseña",
				equalTo: "Las contraseñas no coinciden"
			},
			Nu_Celular:{
				minlength: "Debe ingresar 9 dígitos",
				maxlength: "Debe ingresar 9 dígitos"
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
		submitHandler: form_Usuario
	});
	
  $( '#cbo-filtro_empresa' ).html('<option value="0" selected="selected">- Todas -</option>');
  $( '#cbo-filtro_organizacion' ).html('<option value="0" selected="selected">- Todas -</option>');

  url = base_url + 'HelperController/getEmpresasTodo';
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
    table.search($(this).val()).draw();
  });

	$( '#cbo-filtro_organizacion' ).change(function(){
    table.search($(this).val()).draw();
  });

  $( '#cbo-Grupos' ).html('<option value="" selected="selected">- Sin valores -</option>');

	$( '#cbo-organizacion' ).change(function(){
    $( '#modal-loader' ).modal('show');
    url = base_url + 'HelperController/getGrupos';
    var arrParams = {
      iIdEmpresa : $( '#cbo-Empresas' ).val(),
      iIdOrganizacion : $(this).val(),
    };
    $.post( url, arrParams, function( response ){
      $( '#cbo-Grupos' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
      for (var i = 0; i < response.length; i++)
        $( '#cbo-Grupos' ).append( '<option value="' + response[i].ID_Grupo + '">' + response[i].No_Grupo + '</option>' );    
      $( '#modal-loader' ).modal('hide');
    }, 'JSON');
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
    agregarUsuario();
  });
})

function reload_table(){
    table.ajax.reload(null,false);
}
 
function agregarUsuario(){
  accion_usuario='add_usuario';
  
  $( '#form-Usuario' )[0].reset();
  
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  
  $( '#modal-Usuario' ).modal('show');
  $( '.help-block' ).empty();
  $( '.modal-title' ).text('Nuevo Usuario');
  
  $( '[name="EID_Empresa"]' ).val( '' );
  $( '[name="EID_Grupo"]' ).val( '' );
  $( '[name="EID_Usuario"]' ).val( '' );
  $( '[name="ENo_Usuario"]' ).val( '' );
  $( '[name="ENu_Celular"]' ).val( '' );
  $( '[name="ETxt_Email"]' ).val( '' );
  
  $( '#modal-loader' ).modal('show');
  
  url = base_url + 'HelperController/getEmpresasTodo';
  $.post( url , function( response ){
    $( '#cbo-Empresas' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-Empresas' ).append( '<option value="' + response[i].ID_Empresa + '">' + response[i].No_Empresa + '</option>' );
    $( '#modal-loader' ).modal('hide');
  }, 'JSON');

  url = base_url + 'HelperController/getOrganizaciones';
  var arrParams = {
    iIdEmpresa : $( '#cbo-Empresas' ).val(),
  }
  $.post( url, arrParams, function( response ){
    $( '#cbo-organizacion' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-organizacion' ).append( '<option value="' + response[i].ID_Organizacion + '">' + response[i].No_Organizacion + '</option>' );    
    $( '#modal-loader' ).modal('hide');  
  }, 'JSON');

  url = base_url + 'HelperController/getGrupos';
  var arrParams = {
    iIdEmpresa: $('#header-a-id_empresa').val(),
    iIdOrganizacion: $('#header-a-id_organizacion').val(),
  };
  $.post(url, arrParams, function (response) {
    $('#cbo-Grupos').html('<option value="0" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++)
      $('#cbo-Grupos').append('<option value="' + response[i].ID_Grupo + '">' + response[i].No_Grupo + '</option>');
  }, 'JSON');

  $( '#cbo-Estado' ).html( '<option value="1">Activo</option>' );
  $( '#cbo-Estado' ).append( '<option value="0">Inactivo</option>' );
}

function verUsuario(ID_Usuario){
  accion_usuario='upd_usuario';
  
  $( '#form-Usuario' )[0].reset();
  
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  
  $( '.help-block' ).empty();
 
  $( '#modal-loader' ).modal('show');
  
  url = base_url + 'PanelAcceso/UsuarioController/ajax_edit/' + ID_Usuario;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function( response ){
      $( '#modal-Usuario' ).modal('show');
      $( '.modal-title' ).text('Modifcar Usuario');
      
      $( '[name="EID_Empresa"]' ).val( response.ID_Empresa );
      $( '[name="EID_Organizacion"]' ).val( response.ID_Organizacion );
      $( '[name="EID_Grupo"]' ).val( response.ID_Grupo );
      $( '[name="EID_Usuario"]' ).val( response.ID_Usuario );
      $( '[name="ENo_Usuario"]' ).val( response.No_Usuario );
      $( '[name="ENu_Celular"]' ).val( response.Nu_Celular );
      $( '[name="ETxt_Email"]' ).val( response.Txt_Email );
      $( '[name="ENu_Estado"]' ).val( response.Nu_Estado );
      
      var selected;
      url = base_url + 'HelperController/getEmpresasTodo';
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

      url = base_url + 'HelperController/getGrupos';
      var arrParams = {
        iIdEmpresa : response.ID_Empresa,
        iIdOrganizacion : response.ID_Organizacion,
      };
      $.post( url, arrParams, function( responseGrupo ){
        $( '#cbo-Grupos' ).html('');
        for (var i = 0; i < responseGrupo.length; i++){
          selected = '';
          if(response.ID_Grupo == responseGrupo[i].ID_Grupo)
            selected = 'selected="selected"';
          $( '#cbo-Grupos' ).append( '<option value="' + responseGrupo[i].ID_Grupo + '" ' + selected + '>' + responseGrupo[i].No_Grupo + '</option>' );
        }
        $( '#modal-loader' ).modal('hide');
      }, 'JSON');
      
      $( '[name="No_Usuario"]' ).val( response.No_Usuario );
      $( '[name="No_Nombres_Apellidos"]' ).val( response.No_Nombres_Apellidos );
      $( '[name="No_Password"]' ).val( response.No_Password );
      $( '[name="RNo_Password"]' ).val( response.No_Password );
      $( '[name="Nu_Celular"]' ).val( response.Nu_Celular );
      $( '[name="Txt_Email"]' ).val( response.Txt_Email );
      
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
}

function form_Usuario(){
  if ( accion_usuario=='add_usuario' || accion_usuario=='upd_usuario' ) {
    if ( $( '#cbo-organizacion' ).val() == 0 ){
  		$( '#cbo-organizacion' ).closest('.form-group').find('.help-block').html('Seleccionar organizacion');
  		$( '#cbo-organizacion' ).closest('.form-group').removeClass('has-success').addClass('has-error');
  	} else if ( $( '#cbo-Grupos' ).val() == 0){
      $( '#cbo-Grupos' ).closest('.form-group').find('.help-block').html('Seleccionar grupo');
  	  $( '#cbo-Grupos' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $( '#txt-No_Usuario' ).val().length == 0 ){
  		$( '#txt-No_Usuario' ).closest('.form-group').find('.help-block').html('Ingresar perfil');
  		$( '#txt-No_Usuario' ).closest('.form-group').removeClass('has-success').addClass('has-error');
  	} else if ( $( '[name="No_Password"]' ).val().length == 0 ){
  		$( '[name="No_Password"]' ).closest('.form-group').find('.help-block').html('Ingresar contraseña');
  		$( '[name="No_Password"]' ).closest('.form-group').removeClass('has-success').addClass('has-error');
  	} else if ( $( '[name="RNo_Password"]' ).val().length == 0 ){
  		$( '[name="RNo_Password"]' ).closest('.form-group').find('.help-block').html('Repetir contraseña');
  		$( '[name="RNo_Password"]' ).closest('.form-group').removeClass('has-success').addClass('has-error');
  	} else {
      $( '#btn-save' ).text('');
      $( '#btn-save' ).attr('disabled', true);
      $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
      
      $( '#modal-loader' ).modal('show');
      
      url = base_url + 'PanelAcceso/UsuarioController/crudUsuario';
    	$.ajax({
        type		  : 'POST',
        dataType	: 'JSON',
    		url		    : url,
    		data		  : $('#form-Usuario').serialize(),
    		success : function( response ){
          $( '#modal-loader' ).modal('hide');
      
    	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
      	  $( '#modal-message' ).modal('show');
    		  
    		  if (response.status == 'success'){
    		    accion_usuario='';
    		    
    		    $('#modal-Usuario').modal('hide');
      	    $( '.modal-message' ).addClass(response.style_modal);
      	    $( '.modal-title-message' ).text(response.message);
      	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);

            if( $( '[name="hidden-sCorreUsuarioLink"]' ).val() != '' && $( '[name="hidden-sCorreUsuarioLink"]' ).val().length > 4 ){
              window.location = base_url + 'PanelAcceso/UsuarioController/listarUsuarios';
            } else {
      	      reload_table();
            }
    		  } else {
      	    $( '.modal-message' ).addClass(response.style_modal);
      	    $( '.modal-title-message' ).text(response.message);
      	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
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

function eliminarUsuario(ID, accion_usuario){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $(document).bind('keydown', 'alt+l', function(){
    if ( accion_usuario=='delete' ) {
      _eliminarUsuario($modal_delete, ID);
      accion_usuario='';
    }
  });

  $( '#btn-save-delete' ).off('click').click(function () {
    _eliminarUsuario($modal_delete, ID);
  });
}

function _eliminarUsuario($modal_delete, ID){
  $( '#modal-loader' ).modal('show');
  
  url = base_url + 'PanelAcceso/UsuarioController/eliminarUsuario/' + ID;
  $.ajax({
    url       : url,
    type      : "GET",
    dataType  : "JSON",
    success: function(response){
      $( '#modal-loader' ).modal('hide');
      
      $modal_delete.modal('hide');
      
	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
  	  $( '#modal-message' ).modal('show');
		  
		  if (response.status == 'success'){
		    accion_usuario='';
		    
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
  	    reload_table();
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
}