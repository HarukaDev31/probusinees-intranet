var url;
var table_serie;
var accion_serie;

$(function () {
  $('.select2').select2();

	$(document).keyup(function(event){
    if(event.which == 27){//ESC
      $( "#modal-Serie" ).modal('hide');
    }
	});


  $('#cbo-filtro_empresa').html('<option value="0" selected="selected">- Todas -</option>');
  $('#cbo-filtro_organizacion').html('<option value="0" selected="selected">- Todas -</option>');
  $('#cbo-filtro_almacen').html('<option value="0" selected="selected">- Todas -</option>');

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

  url = base_url + 'Ventas/SerieController/ajax_list';
  table_serie = $('#table-Serie').DataTable({
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
        data.filtro_organizacion = $( '#cbo-filtro_organizacion' ).val(),
        data.filtro_almacen = $( '#cbo-filtro_almacen' ).val(),
        data.Filtro_TiposDocumento = $( '#cbo-Filtro_TiposDocumento' ).val(),
        data.Filtro_SeriesDocumento = $( '#cbo-Filtro_SeriesDocumento' ).val()
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
  
  $( "#form-Serie" ).validate({
		rules:{
			ID_Organizacion: {
				required: true
			},
			ID_Almacen: {
				required: true
			},
			ID_Serie_Documento: {
        required: true,
        minlength: 4,
        maxlength: 4,
			},
			Nu_Numero_Documento: {
        required: true,
        minlength: 1,
        maxlength: 8,
			},
			Nu_Cantidad_Caracteres: {
        required: true,
        minlength: 1,
        maxlength: 8,
			},
		},
		messages:{
			ID_Organizacion:{
				required: "Seleccionar organización",
			},
			ID_Almacen:{
				required: "Seleccionar almacén",
			},
			ID_Serie_Documento:{
				required: "Ingresar serie",
        minlength: "Ingresar 4 dígitos",
        maxlength: "Ingresar 4 dígitos",
			},
			Nu_Numero_Documento:{
        required: "Ingresar 1 al 8",
        minlength: "Ingresar 1 mínimo",
        minlength: "Ingresar 8 máximo",
			},
			Nu_Cantidad_Caracteres:{
        required: "Ingresar 1 al 8",
        minlength: "Ingresar 1 mínimo",
        minlength: "Ingresar 8 máximo",
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
		submitHandler: form_Serie
	});

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
  });
  
	$( '#cbo-filtro_organizacion' ).change(function(){
    if ( $(this).val() > 0 ) {
      url = base_url + 'HelperController/getAlmacenes';
      var arrParams = {
        iIdOrganizacion : $( this ).val(),
      };
      $.post( url, arrParams, function( response ){
        $( '#cbo-filtro_almacen' ).html('<option value="0" selected="selected">- Todos -</option>');
        for (var i = 0; i < response.length; i++)
          $( '#cbo-filtro_almacen' ).append( '<option value="' + response[i].ID_Almacen + '">' + response[i].No_Almacen + '</option>' );
      }, 'JSON');
    }
  });

  url = base_url + 'HelperController/getTiposDocumentos';
  $.post( url, {Nu_Tipo_Filtro : 8}, function( response ){
    $( '#cbo-Filtro_TiposDocumento' ).html('<option value="0" selected="selected">Todos</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-Filtro_TiposDocumento' ).append( '<option value="' + response[i].ID_Tipo_Documento + '">' + response[i].No_Tipo_Documento_Breve + '</option>' );
  }, 'JSON');
  
	$( '#cbo-Filtro_SeriesDocumento' ).html('<option value="0" selected="selected">Todos</option>');
	$( '#cbo-Filtro_TiposDocumento' ).change(function(){
	  $( '#cbo-Filtro_SeriesDocumento' ).html('<option value="0" selected="selected">Todos</option>');
	  if ( $(this).val() > 0) {
      url = base_url + 'HelperController/getSeriesEmpresaOrgAlmacenDocumentoOficinaPuntoVenta';
      var arrPost = {
        iIdEmpresa : $( '#cbo-filtro_empresa' ).val(),
        iIdOrganizacion : $( '#cbo-filtro_organizacion' ).val(),
        iIdAlmacen : ($('#cbo-filtro_almacen').val() != 0 ? $( '#cbo-filtro_almacen' ).val() : ''),
        ID_Tipo_Documento: $(this).val()
      };
      $.post( url, arrPost, function( response ){
        var l = response.length;
        var sTipoSerie = 'oficina';
        for (var i = 0; i < l; i++) {
          sTipoSerie = '(' + ( response[i].ID_POS > 0 ? 'pv' : 'oficina' ) + ')';
          $( '#cbo-Filtro_SeriesDocumento' ).append( '<option value="' + response[i].ID_Serie_Documento + '">' + response[i].ID_Serie_Documento + ' ' + sTipoSerie + '</option>' );
        }
      }, 'JSON');
	  }
  })
  
	$( '#cbo-Empresas' ).change(function(){
    url = base_url + 'HelperController/getOrganizaciones';
    var arrParams = {
      iIdEmpresa : $( this ).val(),
    };
    $.post( url, arrParams, function( response ){
      $( '#cbo-Organizaciones' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
      for (var i = 0; i < response.length; i++)
        $( '#cbo-Organizaciones' ).append( '<option value="' + response[i].ID_Organizacion + '">' + response[i].No_Organizacion + '</option>' );    
    }, 'JSON');
  });

	$( '#cbo-Organizaciones' ).change(function(){
    if ( $(this).val() > 0 ) {
      url = base_url + 'HelperController/getAlmacenes';
      var arrParams = {
        iIdOrganizacion : $( this ).val(),
      };
      $.post( url, arrParams, function( response ){
        var iTotalRegistros = response.length;
        if ( iTotalRegistros == 1 ) {
          $( '#cbo-almacen_serie' ).html( '<option value="' + response[0].ID_Almacen + '">' + response[0].No_Almacen + '</option>' );
        } else {
          $( '#cbo-almacen_serie' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
          for (var i = 0; i < iTotalRegistros; i++) {
            $( '#cbo-almacen_serie' ).append( '<option value="' + response[i].ID_Almacen + '">' + response[i].No_Almacen + '</option>' );
          }
        }
      }, 'JSON');
      
      url = base_url + 'HelperController/getPosConfiguracionxSerie';
      var arrParams = {
        iIdEmpresa : $( '#cbo-Empresas' ).val(),
        iIdOrganizacion : $( this ).val(),
      };
      $.post( url, arrParams, function( response ){
        if ( response.sStatus == 'success' ) {
          var l = response.arrData.length;
          $( '#cbo-pos' ).html('<option value="" selected="selected">- Seleccionar -</option>');
          for (var x = 0; x < l; x++) {
            $( '#cbo-pos' ).append( '<option value="' + response.arrData[x].ID_POS + '">' + response.arrData[x].Nu_Pos + '</option>' );
          }
        } else {
          if( response.sMessageSQL !== undefined ) {
            console.log(response.sMessageSQL);
          }
          if ( response.sStatus == 'warning')
            $( '#cbo-pos' ).html('<option value="" selected="selected">- No hay pos -</option>');
        }
      }, 'JSON');
    }
  });

  $(document).bind('keydown', 'f2', function(){
    agregarSerie();
  });
})

function agregarSerie(){
  accion_serie='add_serie';
  
  $( '#form-Serie' )[0].reset();
  
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  
  $( '.help-block' ).empty();
  
  $( '#modal-Serie' ).modal('show');
  
  $( '.modal-title' ).text('Nueva Serie');
  
  $( '[name="EID_Empresa"]' ).val('');
  $( '[name="EID_Organizacion"]' ).val('');
  $( '[name="EID_Almacen"]' ).val('');
  $( '[name="EID_Tipo_Documento"]' ).val('');
  $('[name="EID_Serie_Documento"]').val('');
  $('[name="EID_Serie_Documento_PK"]').val('');
  
  $( '.div-Estado' ).hide();

	$( '#modal-Serie' ).on('shown.bs.modal', function() {
		$( '#txt-ID_Serie_Documento' ).focus();
	})
  
	$('#radio-InactiveSerieIgual').prop('checked', true).iCheck('update');
	$('#radio-ActiveSerieIgual').prop('checked', false).iCheck('update');

  $( '#cbo-organizacion' ).html('<option value="0" selected="selected">- Vacio -</option>');
  $( '#cbo-almacen_serie' ).html('<option value="0" selected="selected">- Vacio -</option>');
  $( '#cbo-pos' ).html('<option value="" selected="selected">- Vacio -</option>');

  url = base_url + 'HelperController/getEmpresas';
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
    if (response.length == 1) {
      $( '#cbo-Organizaciones' ).html( '<option value="' + response[0].ID_Organizacion + '">' + response[0].No_Organizacion + '</option>' );
    } else {
      $( '#cbo-Organizaciones' ).html('<option value="" selected="selected">- Seleccionar -</option>');
      for (var i = 0; i < response.length; i++)
        $( '#cbo-Organizaciones' ).append( '<option value="' + response[i].ID_Organizacion + '">' + response[i].No_Organizacion + '</option>' );
    }
  }, 'JSON');

  url = base_url + 'HelperController/getAlmacenes';
  var arrParams = {
    iIdOrganizacion: $('#header-a-id_organizacion').val(),
  };
  $.post(url, arrParams, function (response) {
    var iTotalRegistros = response.length;
    if (iTotalRegistros == 1) {
      $('#cbo-almacen_serie').html('<option value="' + response[0].ID_Almacen + '">' + response[0].No_Almacen + '</option>');
    } else {
      $('#cbo-almacen_serie').html('<option value="0" selected="selected">- Seleccionar -</option>');
      for (var i = 0; i < iTotalRegistros; i++) {
        $('#cbo-almacen_serie').append('<option value="' + response[i].ID_Almacen + '">' + response[i].No_Almacen + '</option>');
      }
    }
  }, 'JSON');

  url = base_url + 'HelperController/getPosConfiguracionxSerie';
  var arrParams = {
    iIdEmpresa: $('#header-a-id_empresa').val(),
    iIdOrganizacion: $('#header-a-id_organizacion').val(),
  };
  $.post(url, arrParams, function (response) {
    if (response.sStatus == 'success') {
      var l = response.arrData.length;
      $('#cbo-pos').html('<option value="" selected="selected">- Seleccionar -</option>');
      for (var x = 0; x < l; x++) {
        $('#cbo-pos').append('<option value="' + response.arrData[x].ID_POS + '">' + response.arrData[x].Nu_Pos + '</option>');
      }
    } else {
      if (response.sMessageSQL !== undefined) {
        console.log(response.sMessageSQL);
      }
      if (response.sStatus == 'warning')
        $('#cbo-pos').html('<option value="" selected="selected">- No hay pos -</option>');
    }
  }, 'JSON');

  url = base_url + 'HelperController/getTiposDocumentos';
  $.post( url, {Nu_Tipo_Filtro : 8}, function( response ){
    $( '#cbo-TiposDocumento' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-TiposDocumento' ).append( '<option value="' + response[i]['ID_Tipo_Documento'] + '" data-nu_impuesto="' + response[i]['Nu_Impuesto'] + '">' + response[i]['No_Tipo_Documento_Breve'] + '</option>' );
  }, 'JSON');
  
  $( '#cbo-Estado' ).html( '<option value="1">Activo</option>' );
  $( '#cbo-Estado' ).append( '<option value="0">Inactivo</option>' );
}

function verSerie(ID_Serie_Documento_PK){
  accion_serie='upd_serie';
  
  $( '#form-Serie' )[0].reset();
  
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  
  $( '.help-block' ).empty();
  
  $( '#modal-loader' ).modal('show');
 
	$('#radio-InactiveSerieIgual').prop('checked', true).iCheck('update');
	$('#radio-ActiveSerieIgual').prop('checked', false).iCheck('update');

  url = base_url + 'Ventas/SerieController/ajax_edit/' + ID_Serie_Documento_PK;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $( '#modal-loader' ).modal('hide');
      
      $( '#modal-Serie' ).modal('show');
      $( '.modal-title' ).text('Modifcar Serie');

      $( '[name="EID_Empresa"]' ).val( response.ID_Empresa );
      $( '[name="EID_Organizacion"]' ).val( response.ID_Organizacion );
      $( '[name="EID_Almacen"]' ).val( response.ID_Almacen );
      $( '[name="EID_Tipo_Documento"]' ).val( response.ID_Tipo_Documento );
      $( '[name="EID_Serie_Documento"]' ).val( response.ID_Serie_Documento );
      $( '[name="EID_Serie_Documento_PK"]' ).val( response.ID_Serie_Documento_PK );
      
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
  
      url = base_url + 'HelperController/getAlmacenes';
      var arrParams = {
        iIdOrganizacion : response.ID_Organizacion,
      }
      $.post( url, arrParams , function( responseAlmacen ){
        $( '#cbo-almacen_serie' ).html('');
        for (var i = 0; i < responseAlmacen.length; i++){
          selected = '';
          if(response.ID_Almacen == responseAlmacen[i].ID_Almacen)
            selected = 'selected="selected"';
          $( '#cbo-almacen_serie' ).append( '<option value="' + responseAlmacen[i].ID_Almacen + '" ' + selected + '>' + responseAlmacen[i].No_Almacen + '</option>' );
        }
      }, 'JSON');

      url = base_url + 'HelperController/getPosConfiguracionxSerie';
      var arrParams = {
        iIdEmpresa : response.ID_Empresa,
        iIdOrganizacion : response.ID_Organizacion,
      };
      $.post( url, arrParams, function( responsePos ){
        if ( responsePos.sStatus == 'success' ) {
          var l = responsePos.arrData.length;
          $( '#cbo-pos' ).html('<option value="" selected="selected">- Seleccionar -</option>');
          for (var x = 0; x < l; x++) {
            selected = '';
            if(response.ID_POS == responsePos.arrData[x].ID_POS)
              selected = 'selected="selected"';
            $( '#cbo-pos' ).append( '<option value="' + responsePos.arrData[x].ID_POS + '" ' + selected + '>' + responsePos.arrData[x].Nu_Pos + '</option>' );
          }
        } else {
          if( responsePos.sMessageSQL !== undefined ) {
            console.log(responsePos.sMessageSQL);
          }
          if ( responsePos.sStatus == 'warning')
            $( '#cbo-pos' ).html('<option value="" selected="selected">- No hay pos -</option>');
        }
      }, 'JSON');

      url = base_url + 'HelperController/getTiposDocumentos';
      $.post( url, {Nu_Tipo_Filtro : 8}, function( responseTiposDocumento ){
        $( '#cbo-TiposDocumento' ).html('');
        for (var i = 0; i < responseTiposDocumento.length; i++){
          selected = '';
          if(response.ID_Tipo_Documento == responseTiposDocumento[i]['ID_Tipo_Documento'])
            selected = 'selected="selected"';
          $( '#cbo-TiposDocumento' ).append( '<option value="' + responseTiposDocumento[i]['ID_Tipo_Documento'] + '" data-nu_impuesto="' + responseTiposDocumento[i]['Nu_Impuesto'] + '" ' + selected + '>' + responseTiposDocumento[i]['No_Tipo_Documento_Breve'] + '</option>' );
        }
      }, 'JSON');
      
      $( '[name="ID_Serie_Documento"]' ).val( response.ID_Serie_Documento );
      $( '[name="Nu_Numero_Documento"]' ).val( response.Nu_Numero_Documento );
      $( '[name="Nu_Cantidad_Caracteres"]' ).val( response.Nu_Cantidad_Caracteres );
      $( '[name="ID_POS"]' ).val( response.ID_POS );
      
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

function form_Serie(){
  if ( accion_serie=='add_serie' || accion_serie=='upd_serie' ) {
    if ( $( '#cbo-Organizaciones' ).val() == 0){
      $( '#cbo-Organizaciones' ).closest('.form-group').find('.help-block').html('Seleccionar organización');
  	  $( '#cbo-Organizaciones' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ($( '#cbo-almacen_serie' ).val() == 0){
      $( '#cbo-almacen_serie' ).closest('.form-group').find('.help-block').html('Seleccionar almacén');
      $( '#cbo-almacen_serie' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $( '#cbo-TiposDocumento' ).val() == 0){
      $( '#cbo-TiposDocumento' ).closest('.form-group').find('.help-block').html('Seleccionar tipo');
  	  $( '#cbo-TiposDocumento' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $( '#txt-ID_Serie_Documento' ).val().length == 0 ){
  		$( '#txt-ID_Serie_Documento' ).closest('.form-group').find('.help-block').html('Ingresar serie');
  		$( '#txt-ID_Serie_Documento' ).closest('.form-group').removeClass('has-success').addClass('has-error');
  	} else if ( $( '[name="Nu_Numero_Documento"]' ).val().length == 0 ){
  		$( '[name="Nu_Numero_Documento"]' ).closest('.form-group').find('.help-block').html('Ingresar correlativo');
  		$( '[name="Nu_Numero_Documento"]' ).closest('.form-group').removeClass('has-success').addClass('has-error');
  	} else if ( $( '[name="Nu_Cantidad_Caracteres"]' ).val().length == 0 ){
  		$( '[name="Nu_Cantidad_Caracteres"]' ).closest('.form-group').find('.help-block').html('Ingresar cantidad caracteres');
  		$( '[name="Nu_Cantidad_Caracteres"]' ).closest('.form-group').removeClass('has-success').addClass('has-error');
  	} else {
      $( '#btn-save' ).text('');
      $( '#btn-save' ).attr('disabled', true);
      $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
    
      $( '#modal-loader' ).modal('show');
      
      url = base_url + 'Ventas/SerieController/crudSerie';
    	$.ajax({
        type		  : 'POST',
        dataType	: 'JSON',
    		url		    : url,
    		data		  : $('#form-Serie').serialize(),
    		success : function( response ){
    		  $( '#modal-loader' ).modal('hide');
  
    	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
      	  $( '#modal-message' ).modal('show');
    		  
          if (accion_serie=='upd_serie'){
    		    $('#modal-Serie').modal('hide');
          }

    		  if (response.status == 'success'){
    		    //accion_serie='';
    		    
      	    $( '.modal-message' ).addClass(response.style_modal);
      	    $( '.modal-title-message' ).text(response.message);
      	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
      	    reload_table_serie();
    		  } else {
      	    $( '.modal-message' ).addClass(response.style_modal);
      	    $( '.modal-title-message' ).text(response.message);
      	    setTimeout(function() {$('#modal-message').modal('hide');}, 7200);
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
    	    setTimeout(function() {$('#modal-message').modal('hide');}, 7700);
    	    
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

function eliminarSerie(ID_Serie_Documento_PK, accion_serie){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });
  
  $(document).bind('keydown', 'alt+l', function(){
    if ( accion_serie=='delete' ) {
      _eliminarSerie($modal_delete, ID_Serie_Documento_PK);
      accion_serie='';
    }
  });

  $( '#btn-save-delete' ).off('click').click(function () {
    _eliminarSerie($modal_delete, ID_Serie_Documento_PK);
  });
}

function _eliminarSerie($modal_delete, ID_Serie_Documento_PK){
  $( '#modal-loader' ).modal('show');
  
  url = base_url + 'Ventas/SerieController/eliminarSerie/' + ID_Serie_Documento_PK;
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
		    $( '#txt-ID_Serie_Documento' ).val('');
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
