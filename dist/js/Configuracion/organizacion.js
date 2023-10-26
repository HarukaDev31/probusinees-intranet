var url;
var table_organizacion;

$(function () {
  $('.select2').select2();
  
  url = base_url + 'Configuracion/OrganizacionController/ajax_list';
  table_organizacion = $( '#table-Organizacion' ).DataTable({
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
      'url'     : url,
      'type'      : 'POST',
      'dataType'  : 'JSON',
      'data'      : function ( data ) {
        data.Filtros_Organizaciones = $( '#cbo-Filtros_Organizaciones' ).val(),
        data.Global_Filter = $( '#txt-Global_Filter' ).val();
      },
    },
    'columnDefs': [{
      'className'  : 'text-center',
      'targets'    : 'no-sort',
      'orderable'  : false,
    },],
    'lengthMenu': [[10, 100, 1000, -1], [10, 100, 1000, "Todos"]],
  });
  
  $( '.dataTables_length' ).addClass('col-md-3');
  $( '.dataTables_paginate' ).addClass('col-md-9');
  
  $( '#txt-Global_Filter' ).keyup(function() {
    table_organizacion.search($(this).val()).draw();
  });
  
  $( '#form-Organizacion' ).validate({
		rules:{
			No_Organizacion: {
				required: true
			},
			Nu_Tipo_Proveedor_FE: {
				required: true
			},
		},
		messages:{
			No_Organizacion:{
				required: "Ingresar descripción",
			},
			Nu_Tipo_Proveedor_FE:{
				required: "Seleccionar proveedor",
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
		submitHandler: form_Organizacion
  });
  
  
	$( '#cbo-tipo_proveedor_fe' ).change(function(){
    $( '.div-row-nubefact' ).hide();
    if ( $(this).val() == 1 ) {
      $( '.div-row-nubefact' ).show();
    }
  });
})

function agregarOrganizacion(){
  $( '#form-Organizacion' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  
  $( '.help-block' ).empty();
  
  $( '#modal-Organizacion' ).modal('show');
  
  $( '.modal-title' ).text('Nueva Organización');
  
  $( '[name="EID_Organizacion"]' ).val('');
  $( '[name="ENo_Organizacion"]' ).val('');
  
  $( '#modal-loader' ).modal('show');
  url = base_url + 'HelperController/getEmpresas';
  $.post( url , function( response ){
    $( '#modal-loader' ).modal('hide');
    $( '#cbo-Empresas' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-Empresas' ).append( '<option value="' + response[i].ID_Empresa + '">' + response[i].No_Empresa + '</option>' );
  }, 'JSON');
  
  $( '#cbo-Estado' ).html( '<option value="1">Activo</option>' );
  $( '#cbo-Estado' ).append( '<option value="0">Inactivo</option>' );

  $( '#cbo-tipo_proveedor_fe' ).html( '<option value="">- Seleccionar -</option>' );
  $( '#cbo-tipo_proveedor_fe' ).append( '<option value="1">Nubefact</option>' );
  $( '#cbo-tipo_proveedor_fe' ).append( '<option value="2">SUNAT</option>' );
}

function verOrganizacion(ID){
  $( '#form-Organizacion' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '#modal-loader' ).modal('show');
 
  url = base_url + 'Configuracion/OrganizacionController/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $( '#modal-loader' ).modal('hide');
      
      $( '#modal-Organizacion' ).modal('show');
      $( '.modal-title' ).text('Modificar Organización');
      
      $( '[name="EID_Organizacion"]' ).val(response.ID_Organizacion);
      $( '[name="ENo_Organizacion"]' ).val(response.No_Organizacion);
      
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
      
      $( '[name="No_Organizacion"]' ).val(response.No_Organizacion);
      $( '[name="Txt_Organizacion"]' ).val(response.Txt_Organizacion);
      $( '[name="Txt_FE_Ruta"]' ).val(response.Txt_FE_Ruta);
      $( '[name="Txt_FE_Token"]' ).val(response.Txt_FE_Token);
      $( '[name="Txt_Autorizacion_Venta_Localhost_Hostname"]' ).val(response.Txt_Autorizacion_Venta_Localhost_Hostname);
      $( '[name="Txt_Autorizacion_Venta_Localhost_User"]' ).val(response.Txt_Autorizacion_Venta_Localhost_User);
      $( '[name="Txt_Autorizacion_Venta_Localhost_Password"]' ).val(response.Txt_Autorizacion_Venta_Localhost_Password);
      $( '[name="Txt_Autorizacion_Venta_Localhost_Database"]' ).val(response.Txt_Autorizacion_Venta_Localhost_Database);
      
      $( '#cbo-Estado' ).html( '' );
      for (var i = 0; i < 2; i++){
        selected = '';
        if(response.Nu_Estado == i)
          selected = 'selected="selected"';
        $( '#cbo-Estado' ).append( '<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Inactivo' : 'Activo') + '</option>' );
      }

      $( '#cbo-tipo_proveedor_fe' ).html( '' );
      var sNombreProveedorFE = '';
      for (var i = 1; i < 3; i++){
        sNombreProveedorFE = '- Seleccionar -';
        if ( i == 1 ) {
          sNombreProveedorFE = 'Nubefact';
        } else if ( i == 2 ) {
          sNombreProveedorFE = 'SUNAT';
        }
        selected = '';
        if(response.Nu_Tipo_Proveedor_FE  == i)
          selected = 'selected="selected"';
        $( '#cbo-tipo_proveedor_fe' ).append( '<option value="' + i + '" ' + selected + '>' + sNombreProveedorFE + '</option>' );
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

function form_Organizacion(){
  $( '#btn-save' ).text('');
  $( '#btn-save' ).attr('disabled', true);
  $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

  $( '#modal-loader' ).modal('show');
  
  url = base_url + 'Configuracion/OrganizacionController/crudOrganizacion';
  $.ajax({
    type		  : 'POST',
    dataType	: 'JSON',
    url		    : url,
    data		  : $('#form-Organizacion').serialize(),
    success : function( response ){
      $( '#modal-loader' ).modal('hide');
      
      $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
      $( '#modal-message' ).modal('show');
      
      if (response.status == 'success'){
        $( '#modal-Organizacion' ).modal('hide');
        $( '.modal-message' ).addClass(response.style_modal);
        $( '.modal-title-message' ).text(response.message);
        setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
        reload_table_organizacion();
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

function eliminarOrganizacion(iIdEmpresa, ID, iEstadoSistema){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });
  
  $( '#btn-save-delete' ).off('click').click(function () {
    $( '#modal-loader' ).modal('show');
    
    url = base_url + 'Configuracion/OrganizacionController/eliminarOrganizacion/' + iIdEmpresa + '/' + ID;
    $.ajax({
      url       : url,
      type      : "GET",
      dataType  : "JSON",
      success: function( response ){
        $( '#modal-loader' ).modal('hide');
        
        $modal_delete.modal('hide');
        $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
        $( '#modal-message' ).modal('show');
        
        if (response.sStatus == 'success'){
          $( '.modal-message' ).addClass( 'modal-' + response.sStatus );
          $( '.modal-title-message' ).text(response.sMessage);
          setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
          reload_table_organizacion();
        } else {
          $( '.modal-message' ).addClass( 'modal-' + response.sStatus );
          $( '.modal-title-message' ).text(response.sMessage);
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

function limpiarData(iIdEmpresa, iIdOrganizacion, iEstadoSistema){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $modal_delete.removeClass('modal-danger modal-warning modal-success');
  $modal_delete.addClass('modal-danger');
  $( '.modal-title-message-delete' ).text('¿Al limpiar tu información, solo se eliminarán los documentos de ventas y compras, para las series se reiniciará el correlativo a 1?');

  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });
  
  $( '#btn-save-delete' ).off('click').click(function () {
    $( '#modal-loader' ).modal('show');
    
    arrParams = {
      'iIdEmpresa' : iIdEmpresa,
      'iIdOrganizacion' : iIdOrganizacion,
      'iEstadoSistema' : iEstadoSistema,
    };
    url = base_url + 'Configuracion/OrganizacionController/limpiarData';
    $.ajax({
      url : url,
      type : "POST",
      data : arrParams,
      dataType : "JSON",
      success: function( response ){
        $( '#modal-loader' ).modal('hide');
        
        $modal_delete.modal('hide');
  	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
    	  $( '#modal-message' ).modal('show');
  		  
  		  if (response.sStatus == 'success'){
    	    $( '.modal-message' ).addClass( 'modal-' + response.sStatus );
    	    $( '.modal-title-message' ).text( response.sMessage );
    	    setTimeout(function() {$('#modal-message').modal('hide');}, 1600);
    	    reload_table_organizacion();
  		  } else {
    	    $( '.modal-message' ).addClass( 'modal-' + response.sStatus );
    	    $( '.modal-title-message' ).text( response.sMessage );
    	    setTimeout(function() {$('#modal-message').modal('hide');}, 2100);
  		  }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        $( '#modal-loader' ).modal('hide');
        $modal_delete.modal('hide');
  	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
  	    
    	  $( '#modal-message' ).modal('show');
  	    $( '.modal-message' ).addClass( 'modal-danger' );
  	    $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 2100);
  	    
  	    //Message for developer
        console.log(jqXHR.responseText);
      },
    });
  });
}

function activarSistema(iIdEmpresa, iIdOrganizacion, iEstadoSistema){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $modal_delete.removeClass('modal-danger modal-warning modal-success');
  $modal_delete.addClass('modal-success');
  $( '.modal-title-message-delete' ).text('¿Deseas pasar a modo producción SE BORRARÁ solo VENTAS y COMPRAS?');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });
  
  $( '#btn-save-delete' ).off('click').click(function () {
    $( '#modal-loader' ).modal('show');
    
    arrParams = {
      'iIdEmpresa' : iIdEmpresa,
      'iIdOrganizacion' : iIdOrganizacion,
      'iEstadoSistema' : iEstadoSistema,
    };
    url = base_url + 'Configuracion/OrganizacionController/activarSistema';
    $.ajax({
      url : url,
      type : "POST",
      data : arrParams,
      dataType : "JSON",
      success: function( response ){
        $( '#modal-loader' ).modal('hide');
        
        $modal_delete.modal('hide');
  	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
    	  $( '#modal-message' ).modal('show');
  		  
  		  if (response.sStatus == 'success'){
    	    $( '.modal-message' ).addClass( 'modal-' + response.sStatus );
    	    $( '.modal-title-message' ).text( response.sMessage );
    	    setTimeout(function() {$('#modal-message').modal('hide');}, 1600);
    	    reload_table_organizacion();
  		  } else {
    	    $( '.modal-message' ).addClass( 'modal-' + response.sStatus );
    	    $( '.modal-title-message' ).text( response.sMessage );
    	    setTimeout(function() {$('#modal-message').modal('hide');}, 4100);
  		  }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        $( '#modal-loader' ).modal('hide');
        $modal_delete.modal('hide');
  	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
  	    
    	  $( '#modal-message' ).modal('show');
  	    $( '.modal-message' ).addClass( 'modal-danger' );
  	    $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 4100);
  	    
  	    //Message for developer
        console.log(jqXHR.responseText);
      },
    });
  });
}

function activarSistemaSinBorrar(iIdEmpresa, iIdOrganizacion, iEstadoSistema){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $modal_delete.removeClass('modal-danger modal-warning modal-success');
  $modal_delete.addClass('modal-success');
  $( '.modal-title-message-delete' ).text('¿Deseas pasar a modo producción sin BORRAR VENTAS y COMPRAS?');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });
  
  $( '#btn-save-delete' ).off('click').click(function () {
    $( '#modal-loader' ).modal('show');
    
    arrParams = {
      'iIdEmpresa' : iIdEmpresa,
      'iIdOrganizacion' : iIdOrganizacion,
      'iEstadoSistema' : iEstadoSistema,
    };
    url = base_url + 'Configuracion/OrganizacionController/activarSistemaSinBorrar';
    $.ajax({
      url : url,
      type : "POST",
      data : arrParams,
      dataType : "JSON",
      success: function( response ){
        $( '#modal-loader' ).modal('hide');
        
        $modal_delete.modal('hide');
        $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
        $( '#modal-message' ).modal('show');
        
        if (response.sStatus == 'success'){
          $( '.modal-message' ).addClass( 'modal-' + response.sStatus );
          $( '.modal-title-message' ).text( response.sMessage );
          setTimeout(function() {$('#modal-message').modal('hide');}, 1600);
          reload_table_organizacion();
        } else {
          $( '.modal-message' ).addClass( 'modal-' + response.sStatus );
          $( '.modal-title-message' ).text( response.sMessage );
          setTimeout(function() {$('#modal-message').modal('hide');}, 4100);
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        $( '#modal-loader' ).modal('hide');
        $modal_delete.modal('hide');
        $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
        
        $( '#modal-message' ).modal('show');
        $( '.modal-message' ).addClass( 'modal-danger' );
        $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
        setTimeout(function() {$('#modal-message').modal('hide');}, 4100);
        
        //Message for developer
        console.log(jqXHR.responseText);
      },
    });
  });
}
function reload_table_organizacion(){
  table_organizacion.ajax.reload(null,false);
}