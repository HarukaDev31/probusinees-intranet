var url;
var table_tipo_operacion_caja;

$(function () {
  $('.select2').select2();

	$(document).keyup(function(event){
    if(event.which == 27){//ESC
      $( "#modal-TipoOperacionCaja" ).modal('hide');
    }
  });

  url = base_url + 'Configuracion/TipoOperacionCajaController/ajax_list';
  table_tipo_operacion_caja = $( '#table-TipoOperacionCaja' ).DataTable({
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
        data.filtro_organizacion = $( '#cbo-filtro_organizacion' ).val(),
        data.filtro_almacen = $( '#cbo-filtro_almacen' ).val(),
        data.Filtros_TipoOperacionCaja = $( '#cbo-Filtros_TipoOperacionCaja' ).val(),
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
    table_tipo_operacion_caja.search($(this).val()).draw();
  });
  
  $( '#form-TipoOperacionCaja' ).validate({
		rules:{
			Nu_Tipo: {
				required: true
			},
			No_Tipo_Operacion_Caja: {
				required: true
			},
		},
		messages:{
			Nu_Tipo:{
				required: "Seleccionar grupo",
			},
			No_Tipo_Operacion_Caja:{
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
		submitHandler: form_TipoOperacionCaja
  });
  
  $( '#cbo-filtro_empresa' ).html('<option value="0" selected="selected">- Todas -</option>');
  $( '#cbo-filtro_organizacion' ).html('<option value="0" selected="selected">- Todas -</option>');
  $( '#cbo-filtro_almacen' ).html('<option value="0" selected="selected">- Todos -</option>');

  url = base_url + 'HelperController/getEmpresas';
  $.post( url , function( response ){
    $( '#cbo-filtro_empresa' ).html('<option value="0" selected="selected">- Todas -</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-filtro_empresa' ).append( '<option value="' + response[i].ID_Empresa + '">' + response[i].No_Empresa + '</option>' );
  }, 'JSON');
  
	$( '#cbo-filtro_empresa' ).change(function(){
    if ( $(this).val() > 0 ) {
      url = base_url + 'HelperController/getOrganizaciones';
      var arrParams = {
        iIdEmpresa : $( this ).val(),
      };
      $.post( url, arrParams, function( response ){
        $( '#cbo-filtro_organizacion' ).html('<option value="0" selected="selected">- Todas -</option>');
        for (var i = 0; i < response.length; i++)
          $( '#cbo-filtro_organizacion' ).append( '<option value="' + response[i].ID_Organizacion + '">' + response[i].No_Organizacion + '</option>' );    
      }, 'JSON');
    }
    table_tipo_operacion_caja.search($(this).val()).draw();
  });

	$( '#cbo-filtro_organizacion' ).change(function(){
    if ( $(this).val() > 0 ) {
      url = base_url + 'HelperController/getAlmacenes';
      var arrParams = {
        iIdOrganizacion : $( this ).val(),
      };
      $.post( url, arrParams, function( response ){
        var iTotalRegistros = response.length;
        $( '#cbo-filtro_almacen' ).html('<option value="0" selected="selected">- Todos -</option>');
        for (var i = 0; i < iTotalRegistros; i++)
          $( '#cbo-filtro_almacen' ).append( '<option value="' + response[i].ID_Almacen + '">' + response[i].No_Almacen + '</option>' );
      }, 'JSON');
    }
    table_tipo_operacion_caja.search($(this).val()).draw();
  });

	$( '#cbo-filtro_almacen' ).change(function(){
    table_tipo_operacion_caja.search($(this).val()).draw();
  });
  
	$( '#cbo-Empresas' ).change(function(){
    url = base_url + 'HelperController/getOrganizaciones';
    var arrParams = {
      iIdEmpresa : $( this ).val(),
    };
    $.post( url, arrParams, function( response ){
      $( '#cbo-organizacion' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
      for (var i = 0; i < response.length; i++)
        $( '#cbo-organizacion' ).append( '<option value="' + response[i].ID_Organizacion + '">' + response[i].No_Organizacion + '</option>' );    
    }, 'JSON');

    $( '#cbo-almacen-add' ).html('<option value="0" selected="selected">- Ninguno -</option>');
  });
  
	$( '#cbo-organizacion' ).change(function(){
    url = base_url + 'HelperController/getAlmacenes';
    var arrParams = {
      iIdOrganizacion : $( this ).val(),
    };
    $.post( url, arrParams, function( response ){
      var iTotalRegistros = response.length;
      if ( iTotalRegistros == 1 ) {
        $( '#cbo-almacen-add' ).html( '<option value="' + response[0].ID_Almacen + '">' + response[0].No_Almacen + '</option>' );
      } else {
        $( '#cbo-almacen-add' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
        for (var i = 0; i < iTotalRegistros; i++)
          $( '#cbo-almacen-add' ).append( '<option value="' + response[i].ID_Almacen + '">' + response[i].No_Almacen + '</option>' );
      }
    }, 'JSON');
  });
})

function agregarTipoOperacionCaja(){
  $( '#form-TipoOperacionCaja' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  
  $( '.help-block' ).empty();
  
  $( '#modal-TipoOperacionCaja' ).modal('show');
  
  $( '.modal-title' ).text('Nuevo Tipo Operacion Caja');
  
  $( '[name="EID_Empresa"]' ).val('');
  $( '[name="EID_Organizacion"]' ).val('');
  $( '[name="EID_Almacen"]' ).val('');
  $( '[name="EID_Tipo_Operacion_Caja"]' ).val('');
  $( '[name="ENo_Tipo_Operacion_Caja"]' ).val('');

  $( '#modal-loader' ).modal('show');
  url = base_url + 'HelperController/getEmpresas';
  $.post( url , function( response ){
    $( '#cbo-Empresas' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-Empresas' ).append( '<option value="' + response[i].ID_Empresa + '">' + response[i].No_Empresa + '</option>' );
    $( '#modal-loader' ).modal('hide');
  }, 'JSON');
  
  $( '#cbo-organizacion' ).html('<option value="0" selected="selected">- Ninguno -</option>');
  $( '#cbo-almacen-add' ).html('<option value="0" selected="selected">- Ninguno -</option>');

  url = base_url + 'HelperController/getValoresTablaDato';
  var arrParams = {
    sTipoData : 'Tipos_Operaciones_Caja_PV',
  }
  $.post( url, arrParams, function( response ){
    if ( response.sStatus == 'success' ) {
      var iTotalRegistros = response.arrData.length, response=response.arrData;
      $( '#cbo-grupo_tipo_operacion_caja' ).html( '<option value="" selected="selected">- Seleccionar -</option>');
      for (var i = 0; i < iTotalRegistros; i++)
        $( '#cbo-grupo_tipo_operacion_caja' ).append( '<option value="' + response[i].Nu_Valor + '">' + response[i].No_Descripcion + '</option>' );
    } else {
      $( '#cbo-grupo_tipo_operacion_caja' ).html( '<option value="" selected="selected">- Vacío -</option>');
      console.log( response );
    }
  }, 'JSON');

  $( '.div-Estado' ).hide();
  $( '#cbo-Estado' ).html( '<option value="1">Activo</option>' );
}

function verTipoOperacionCaja(ID){
  $( '#form-TipoOperacionCaja' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '#modal-loader' ).modal('show');
 
  url = base_url + 'Configuracion/TipoOperacionCajaController/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $( '#modal-loader' ).modal('hide');
      
      $( '#modal-TipoOperacionCaja' ).modal('show');
      $( '.modal-title' ).text('Modifcar Tipo Operacion Caja');
      
      $( '[name="EID_Empresa"]' ).val(response.ID_Empresa);
      $( '[name="EID_Organizacion"]' ).val(response.ID_Organizacion);
      $( '[name="EID_Almacen"]' ).val(response.ID_Almacen);
      $( '[name="EID_Tipo_Operacion_Caja"]' ).val(response.ID_Tipo_Operacion_Caja);
      $( '[name="ENo_Tipo_Operacion_Caja"]' ).val(response.No_Tipo_Operacion_Caja);
      
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
        if (responseOrganizacion.length == 1) {
          $( '#cbo-organizacion' ).html( '<option value="' + responseOrganizacion[0].ID_Organizacion + '">' + responseOrganizacion[0].No_Organizacion + '</option>' );
        } else {
          for (var i = 0; i < responseOrganizacion.length; i++){
            selected = '';
            if(response.ID_Organizacion == responseOrganizacion[i].ID_Organizacion)
              selected = 'selected="selected"';
            $( '#cbo-organizacion' ).append( '<option value="' + responseOrganizacion[i].ID_Organizacion + '" ' + selected + '>' + responseOrganizacion[i].No_Organizacion + '</option>' );
          }
        }
      }, 'JSON');
      
      url = base_url + 'HelperController/getAlmacenes';
      var arrParams = {
        iIdOrganizacion : response.ID_Organizacion,
      };
      $.post( url, arrParams, function( responseAlmacen ){
        $( '#cbo-almacen-add' ).html('');
        for (var i = 0; i < responseAlmacen.length; i++){
          selected = '';
          if(response.ID_Almacen == responseAlmacen[i].ID_Almacen)
            selected = 'selected="selected"';
          $( '#cbo-almacen-add' ).append( '<option value="' + responseAlmacen[i].ID_Almacen + '" ' + selected + '>' + responseAlmacen[i].No_Almacen + '</option>' );
        }
      }, 'JSON');
            
      $( '[name="No_Tipo_Operacion_Caja"]' ).val(response.No_Tipo_Operacion_Caja);

      url = base_url + 'HelperController/getValoresTablaDato';
      var arrParams = {
        sTipoData : 'Tipos_Operaciones_Caja_PV',
      }
      $.post( url, arrParams, function( responseGrupoOperacionCaja ){
        if ( responseGrupoOperacionCaja.sStatus == 'success' ) {
          var iTotalRegistros = responseGrupoOperacionCaja.arrData.length, responseGrupoOperacionCaja=responseGrupoOperacionCaja.arrData;
          $( '#cbo-grupo_tipo_operacion_caja' ).html( '<option value="" selected="selected">- Seleccionar -</option>');
          for (var i = 0; i < iTotalRegistros; i++) {
            selected = '';
            if(response.Nu_Tipo == responseGrupoOperacionCaja[i].Nu_Valor)
              selected = 'selected="selected"';
            $( '#cbo-grupo_tipo_operacion_caja' ).append( '<option value="' + responseGrupoOperacionCaja[i].Nu_Valor + '" ' + selected + '>' + responseGrupoOperacionCaja[i].No_Descripcion + '</option>' );
          }
        } else {
          $( '#cbo-grupo_tipo_operacion_caja' ).html( '<option value="" selected="selected">- Vacío -</option>');
          console.log( response );
        }
      }, 'JSON');

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
}

function form_TipoOperacionCaja(){
  $( '#btn-save' ).text('');
  $( '#btn-save' ).attr('disabled', true);
  $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

  $( '#modal-loader' ).modal('show');
  
  url = base_url + 'Configuracion/TipoOperacionCajaController/crudTipoOperacionCaja';
	$.ajax({
    type		  : 'POST',
    dataType	: 'JSON',
		url		    : url,
		data		  : $('#form-TipoOperacionCaja').serialize(),
		success : function( response ){
		  $( '#modal-loader' ).modal('hide');
		  
	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
  	  $( '#modal-message' ).modal('show');
		  
		  if (response.status == 'success'){
		    $('#modal-TipoOperacionCaja').modal('hide');
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
  	    reload_table_tipo_operacion_caja();
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

function eliminarTipoOperacionCaja(ID){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });
  
  $( '#btn-save-delete' ).off('click').click(function () {
    $( '#modal-loader' ).modal('show');
    
    url = base_url + 'Configuracion/TipoOperacionCajaController/eliminarTipoOperacionCaja/' + ID;
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
    	    reload_table_tipo_operacion_caja();
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

function reload_table_tipo_operacion_caja(){
  table_tipo_operacion_caja.ajax.reload(null,false);
}