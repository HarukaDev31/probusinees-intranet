var url;
var table_serie;
var accion_serie;

$(function () {
  $('.select2').select2();
  
  $('.date-picker-invoice').val(fDay + '/' + fMonth + '/' + fYear);

  $('#cbo-filtro_empresa').html('<option value="0" selected="selected">- Todas -</option>');
  $('#cbo-filtro_organizacion').html('<option value="0" selected="selected">- Todas -</option>');

  url = base_url + 'Ventas/MonitoreoDocumentosElectronicosController/ajax_list';
  table_serie = $('#table-MonitoreoDocumentosElectronicos').DataTable({
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
      'data': function (data) {
        data.sMethod = $('#hidden-sMethod').val(),
        data.filtro_tipo_documento = $('#cbo-filtro-tipo_documento').val(),
        data.filtro_tipo_sistema = $('#cbo-filtro-tipo_sistema').val(),
        data.filtro_estado_sistema = $('#cbo-filtro-estado_sistema').val(),
        data.filtro_empresa = $( '#cbo-filtro_empresa' ).val(),
        data.filtro_organizacion = $( '#cbo-filtro_organizacion' ).val(),
        data.filtro_almacen = $('#cbo-filtro_almacen').val(),
        data.Filtro_Fe_Inicio = ParseDateString($('#txt-Filtro_Fe_Inicio').val(), 1, '/'),
        data.Filtro_Fe_Fin = ParseDateString($('#txt-Filtro_Fe_Fin').val(), 1, '/')
      },
    },
    'columnDefs': [{
      'className' : 'text-center',
      'targets'   : 'no-sort',
      'orderable' : false,
    },
    {
      'className': 'text-left',
      'targets': 'no-sort_left',
      'orderable': false,
    },],
    'lengthMenu': [[10, 100, 1000, -1], [10, 100, 1000, "Todos"]],
  });

  $('.dataTables_length').addClass('col-xs-4 col-sm-5 col-md-1');
  $('.dataTables_info').addClass('col-xs-8 col-sm-7 col-md-4');
  $('.dataTables_paginate').addClass('col-xs-12 col-sm-12 col-md-7');

  $( '#btn-filter' ).click(function(){
    table_serie.ajax.reload();
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

  $( '#btn-modificar_venta' ).click(function(){
    if( $( '#txt-modificar-Fe_Emision' ).val().length < 10 ){
      alert('Formato de fecha inválida');
    } else {
      $( '#btn-modificar_venta' ).text('');
      $( '#btn-modificar_venta' ).attr('disabled', true);
      $( '#btn-modificar_venta' ).append( 'Modificando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

      $( '#modal-loader' ).modal('show');

      url = base_url + 'Ventas/MonitoreoDocumentosElectronicosController/actualizarVenta';
    	$.ajax({
        type		  : 'POST',
        dataType	: 'JSON',
    		url		    : url,
    		data		  : $('#form-modificar_venta').serialize(),
    		success : function( response ){
    		  $( '#modal-loader' ).modal('hide');
  
    	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
      	  $( '#modal-message' ).modal('show');
    		  
    		  if (response.status == 'success'){
            $('.modal-modificar_venta').modal('hide');

      	    $( '.modal-message' ).addClass('modal-' + response.status);
      	    $( '.modal-title-message' ).text(response.message);
      	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
      	    reload_table_serie();
    		  } else {
      	    $( '.modal-message' ).addClass(response.style_modal);
      	    $( '.modal-title-message' ).text(response.message);
      	    setTimeout(function() {$('#modal-message').modal('hide');}, 7200);
    		  }
  	  
          $( '#btn-modificar_venta' ).text('');
          $( '#btn-modificar_venta' ).append( 'Modificar' );
          $( '#btn-modificar_venta' ).attr('disabled', false);
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
  	  
          $( '#btn-modificar_venta' ).text('');
          $( '#btn-modificar_venta' ).append( 'Modificar' );
          $( '#btn-modificar_venta' ).attr('disabled', false);
        }
    	});
    }
  });

  
	$( '#cbo-modificar-organizacion' ).change(function(){
    if ( $(this).val() > 0 ) {
      url = base_url + 'HelperController/getAlmacenes';
      var arrParams = {
        iIdOrganizacion : $( this ).val(),
      };
      $.post( url, arrParams, function( response ){
        var iTotalRegistros = response.length;
        if ( iTotalRegistros == 1 ) {
          $( '#cbo-modificar-almacen' ).html( '<option value="' + response[0].ID_Almacen + '">' + response[0].No_Almacen + '</option>' );
        } else {
          $( '#cbo-modificar-almacen' ).html('');
          for (var i = 0; i < iTotalRegistros; i++) {
            $( '#cbo-modificar-almacen' ).append( '<option value="' + response[i].ID_Almacen + '">' + response[i].No_Almacen + '</option>' );
          }
        }
      }, 'JSON');
    }
  });
})

function reload_table_serie(){
  table_serie.ajax.reload(null,false);
}

function modificarVenta(id){
  $( '#title-venta' ).text('');

  $( '#txt-modificar-Fe_Emision' ).datepicker({}).on('changeDate', function (selected) {
    var minDate = new Date(selected.date.valueOf());
    $( '#txt-modificar-Fe_Vencimiento' ).datepicker('setStartDate', minDate);
  });

  var Fe_Emision = $( '#txt-modificar-Fe_Emision' ).val().split('/');
  Fe_Emision[0] = parseInt(Fe_Emision[0]) + 1;
  $( '#txt-modificar-Fe_Vencimiento' ).datepicker({
    autoclose : true,
    startDate : new Date(Fe_Emision[2], Fe_Emision[1] - 1, Fe_Emision[0]),
    todayHighlight: false
  })

  url = base_url + 'Ventas/MonitoreoDocumentosElectronicosController/obtenerVenta';
  var arrParams = {
    id : id,
  };
  $.post( url, arrParams, function( response ){
    if(response.status=='success'){
      $('.modal-modificar_venta').modal('show');
      
      var selected = '';
      url = base_url + 'Ventas/MonitoreoDocumentosElectronicosController/getOrganizacionEmpresa';
      $.post( url, {ID_Empresa : response.result.ID_Empresa}, function( responseOrganizacion ){
        $( '#cbo-modificar-organizacion' ).html('');
        if ( responseOrganizacion.sStatus == 'success' ) {
          var l = responseOrganizacion.arrData.length;
          for (var x = 0; x < l; x++) {
            selected = '';
            if(response.result.ID_Organizacion == responseOrganizacion.arrData[x].ID_Organizacion)
              selected = 'selected="selected"';
            $( '#cbo-modificar-organizacion' ).append( '<option value="' + responseOrganizacion.arrData[x].ID_Organizacion + '" ' + selected + '>' + responseOrganizacion.arrData[x].No_Organizacion + '</option>' );
          }
        } else {
          if( responseOrganizacion.sMessageSQL !== undefined ) {
            console.log(responseOrganizacion.sMessageSQL);
          }
          if ( responseOrganizacion.sStatus == 'warning')
            $( '#cbo-modificar-organizacion' ).html('<option value="0" selected="selected">- Vacío -</option>');          
        }
      }, 'JSON');

      url = base_url + 'Ventas/MonitoreoDocumentosElectronicosController/getAlmacenesEmpresa';
      $.post( url, {ID_Organizacion : response.result.ID_Organizacion}, function( responseAlmacen ){
        $( '#cbo-modificar-almacen' ).html('');
        if ( responseAlmacen.sStatus == 'success' ) {
          var l = responseAlmacen.arrData.length;
          for (var x = 0; x < l; x++) {
            selected = '';
            if(response.result.ID_Almacen == responseAlmacen.arrData[x].ID_Almacen)
              selected = 'selected="selected"';
            $( '#cbo-modificar-almacen' ).append( '<option value="' + responseAlmacen.arrData[x].ID_Almacen + '" ' + selected + '>' + responseAlmacen.arrData[x].No_Almacen + '</option>' );
          }
        } else {
          if( responseAlmacen.sMessageSQL !== undefined ) {
            console.log(responseAlmacen.sMessageSQL);
          }
          if ( responseAlmacen.sStatus == 'warning')
            $( '#cbo-modificar-almacen' ).html('<option value="0" selected="selected">- Vacío -</option>');          
        }
      }, 'JSON');

      $( '#hidden-ID_Venta_Modificar' ).val(id);
      $( '#hidden-Fe_Hora_Modificar' ).val(response.result.Fe_Emision_Hora);
      $( '#txt-modificar-Fe_Emision' ).val(ParseDateString(response.result.Fe_Emision, 6, '-'));
      $( '#txt-modificar-Fe_Vencimiento' ).val(ParseDateString(response.result.Fe_Vencimiento, 6, '-'));
      
      $( '#title-venta' ).html('<b>Comprobante: </b>' + response.result.ID_Tipo_Documento + ' - ' + response.result.ID_Serie_Documento + '-' + response.result.ID_Numero_Documento);

    } else {
      $('.modal-modificar_venta').modal('hide');
      alert(response.message);
    }
  }, 'JSON');
}

function anularVenta(id) {
  var $modal_delete = $('.modal-message-delete');
  $modal_delete.modal('show');

  $('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
  $('.modal-message-delete').addClass('modal-warning');

  $('.modal-title-message-delete').text('¿Deseas anular el documento?');

  $('#btn-cancel-delete').off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $(document).bind('keydown', 'alt+k', function () {
    if (accion == 'anular') {
      _anularVenta($modal_delete, id);
      accion = '';
    }
  });

  $('#btn-save-delete').off('click').click(function () {
    _anularVenta($modal_delete, id);
  });
}

function _anularVenta($modal_delete, id){
  $( '#modal-loader' ).modal('show');
    
  url = base_url + 'Ventas/MonitoreoDocumentosElectronicosController/anularVenta/' + id;
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
        $( '.modal-message' ).addClass('modal-' + response.status);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
		  } else {
        $( '.modal-message' ).addClass('modal-' + response.status);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 3100);
      }
      reload_table_serie();
		  accion = '';
    },
    error: function (jqXHR, textStatus, errorThrown) {
      accion = '';
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