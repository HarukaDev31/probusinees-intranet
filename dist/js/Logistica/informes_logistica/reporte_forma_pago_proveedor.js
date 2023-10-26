var url;

$(function () {
  $( '#modal-loader' ).modal('show');
  $( '#div-ventas_detalladas_generales' ).hide();

  var arrParams = {};
  getAlmacenes(arrParams);

  //Global Autocomplete
  $('.autocompletar_personal').autoComplete({
    minChars: 0,
    source: function (term, response) {
      var term = term.toLowerCase();
      var global_class_method = $('.autocompletar_personal').data('global-class_method');
      var global_table = $('.autocompletar_personal').data('global-table');
      var filter_id_codigo = '';
      $.post(base_url + global_class_method, { global_table: global_table, global_search: term, filter_id_codigo: filter_id_codigo }, function (arrData) {
        response(arrData);
      }, 'JSON');
    },
    renderItem: function (item, search) {
      search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
      var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");
      return '<div class="autocomplete-suggestion" data-id="' + item.ID + '" data-nombre="' + item.Nombre + '" data-val="' + search + '">' + item.Nombre.replace(re, "<b>$1</b>") + '</div>';
    },
    onSelect: function (e, term, item) {
      $('#txt-AID_Personal').val(item.data('id'));
      $('#txt-Filtro_Personal').val(item.data('nombre'));
      $('#txt-Filtro_Personal').closest('.form-group').find('.help-block').html('');
      $('#txt-Filtro_Personal').closest('.form-group').removeClass('has-error');
    }
  });
  
  url = base_url + 'HelperController/getTiposDocumentos';
  $.post( url, {Nu_Tipo_Filtro : 3}, function( response ){
    $( '#cbo-filtros_tipos_documento' ).html('<option value="0" selected="selected">Todos</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-filtros_tipos_documento' ).append( '<option value="' + response[i].ID_Tipo_Documento + '">' + response[i].No_Tipo_Documento_Breve + '</option>' );
	  $( '#modal-loader' ).modal('hide');
  }, 'JSON');
  	
	url = base_url + 'HelperController/getMediosPago';
	var arrPost = {
	  iIdEmpresa : $( '#cbo-Empresas' ).val(),
	};
	$.post( url, arrPost, function( response ){
	  $( '#cbo-forma_pago' ).html('<option value="0">Todos</option>');
	  for (var i = 0; i < response.length; i++)
		$( '#cbo-forma_pago' ).append( '<option value="' + response[i].ID_Medio_Pago + '" data-nu_tipo_medio_pago="' + response[i].Nu_Tipo + '">' + response[i].No_Medio_Pago + '</option>' );
  }, 'JSON');

  $('#cbo-tipo_tarjeta').html('<option value="0">Todos</option>');
  $( '.div-tipos_tarjetas').hide();
	$( '#cbo-forma_pago' ).change(function(){
		ID_Medio_Pago = $(this).val();
		Nu_Tipo_Medio_Pago = $(this).find(':selected').data('nu_tipo_medio_pago');
		$( '.div-tipos_tarjetas').hide();
		$( '#cbo-tipo_tarjeta' ).html('<option value="0">Todos</option>');
		if (Nu_Tipo_Medio_Pago==2){
			$( '.div-tipos_tarjetas').show();

			url = base_url + 'HelperController/getTiposTarjetaCredito';
			$.post( url, {ID_Medio_Pago : ID_Medio_Pago} , function( response ){
				$( '#cbo-tipo_tarjeta' ).html('<option value="0">Todos</option>');
				for (var i = 0; i < response.length; i++)
					$( '#cbo-tipo_tarjeta' ).append( '<option value="' + response[i].ID_Tipo_Medio_Pago + '">' + response[i].No_Tipo_Medio_Pago + '</option>' );
			}, 'JSON');
		}
  })
  
  $( '.btn-generar_ventas_detalladas_generales' ).click(function(){    
    var sMethod, ID_Almacen, Fe_Inicio, Fe_Fin, ID_Tipo_Documento, ID_Serie_Documento, ID_Numero_Documento, Nu_Estado_Documento, iIdProveedor, sNombreProveedor, iTipoVenta, iMedioPago, iIdPersonal, sNombrePersonal, iTipoTarjeta;
    
    sMethod = $( '#hidden-sMethod' ).val();
    Fe_Inicio = ParseDateString($( '#txt-Filtro_Fe_Inicio' ).val(), 1, '/');
    Fe_Fin = ParseDateString($( '#txt-Filtro_Fe_Fin' ).val(), 1, '/');
    ID_Tipo_Documento = $( '#cbo-filtros_tipos_documento' ).val();
    ID_Serie_Documento = ($( '#cbo-filtros_series_documento' ).val().length == 0 ? '-' : $( '#cbo-filtros_series_documento' ).val());
    ID_Numero_Documento = ($( '#txt-Filtro_NumeroDocumento' ).val().length == 0 ? '-' : $( '#txt-Filtro_NumeroDocumento' ).val());
    Nu_Estado_Documento = $( '#cbo-estado_documento' ).val();
    iIdProveedor = ($( '#txt-AID' ).val().length === 0 ? '-' : $( '#txt-AID' ).val());
    sNombreProveedor = ($('#txt-Filtro_Entidad').val().length === 0 ? '-' : $('#txt-Filtro_Entidad').val());
    iIdPersonal = ($('#txt-AID_Personal').val().length === 0 ? '-' : $('#txt-AID_Personal').val());
    sNombrePersonal = ($('#txt-Filtro_Personal').val().length === 0 ? '-' : $('#txt-Filtro_Personal').val());
    iTipoVenta = $( '#cbo-tipo_venta' ).val();
    iMedioPago = $( '#cbo-forma_pago' ).val();
    iTipoTarjeta = $('#cbo-tipo_tarjeta').val();
    ID_Almacen = $('#cbo-Almacenes_ReporteFormaPagoProveedor').val();

    if ($(this).data('type') == 'html') {
      getReporteHTML();
    } else if ($(this).data('type') == 'pdf') {
      $( '#btn-pdf_ventas_detalladas_generales' ).text('');
      $( '#btn-pdf_ventas_detalladas_generales' ).attr('disabled', true);
      $( '#btn-pdf_ventas_detalladas_generales' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
          
      url = base_url + 'Logistica/informes_logistica/ReporteFormaPagoProveedorController/sendReportePDF/' + Fe_Inicio + '/' + Fe_Fin + '/' + ID_Tipo_Documento + '/' + ID_Serie_Documento + '/' + ID_Numero_Documento + '/' + Nu_Estado_Documento + '/' + iIdProveedor + '/' + sNombreProveedor + '/' + iTipoVenta + '/' + iMedioPago + '/' + iIdPersonal + '/' + sNombrePersonal + '/' + iTipoTarjeta + '/' + ID_Almacen;
      window.open(url,'_blank');
      
      $( '#btn-pdf_ventas_detalladas_generales' ).text('');
      $( '#btn-pdf_ventas_detalladas_generales' ).append( '<i class="fa fa-file-pdf-o color_white"></i> PDF' );
      $( '#btn-pdf_ventas_detalladas_generales' ).attr('disabled', false);
    } else if ($(this).data('type') == 'excel') {
      $( '#btn-excel_ventas_detalladas_generales' ).text('');
      $( '#btn-excel_ventas_detalladas_generales' ).attr('disabled', true);
      $( '#btn-excel_ventas_detalladas_generales' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
      
      url = base_url + 'Logistica/informes_logistica/ReporteFormaPagoProveedorController/sendReporteEXCEL/' + Fe_Inicio + '/' + Fe_Fin + '/' + ID_Tipo_Documento + '/' + ID_Serie_Documento + '/' + ID_Numero_Documento + '/' + Nu_Estado_Documento + '/' + iIdProveedor + '/' + sNombreProveedor + '/' + iTipoVenta + '/' + iMedioPago + '/' + iIdPersonal + '/' + sNombrePersonal + '/' + iTipoTarjeta + '/' + ID_Almacen;
      window.open(url,'_blank');
      
      $( '#btn-excel_ventas_detalladas_generales' ).text('');
      $( '#btn-excel_ventas_detalladas_generales' ).append( '<i class="fa fa-file-excel-o color_icon_excel"></i> Excel' );
      $( '#btn-excel_ventas_detalladas_generales' ).attr('disabled', false);
    }// /. if
  })// /. btn
})

// Ayudas - combobox
function getAlmacenes(arrParams) {
  $('#cbo-Almacenes_ReporteFormaPagoProveedor').html('<option value="0">- Todos -</option>');
  url = base_url + 'HelperController/getAlmacenes';
  $.post(url, {}, function (responseAlmacen) {
    var iCantidadRegistros = responseAlmacen.length;
    var selected = '';
    var iIdAlmacen = 0;
    $('#cbo-Almacenes_ReporteFormaPagoProveedor').html('<option value="0">- Todos -</option>');
    if (iCantidadRegistros == 1) {
      if (arrParams !== undefined) {
        iIdAlmacen = arrParams.ID_Almacen;
      }
      if (iIdAlmacen == responseAlmacen[0]['ID_Almacen']) {
        selected = 'selected="selected"';
      }
      $('#cbo-Almacenes_ReporteFormaPagoProveedor').append('<option value="' + responseAlmacen[0]['ID_Almacen'] + '" ' + selected + ' data-direccion_almacen="' + responseAlmacen[0]['Txt_Direccion_Almacen'] + '">' + responseAlmacen[0]['No_Almacen'] + '</option>');
    } else {
      for (var i = 0; i < iCantidadRegistros; i++) {
        if (arrParams !== undefined) {
          iIdAlmacen = arrParams.ID_Almacen;
        }
        if (iIdAlmacen == responseAlmacen[0]['ID_Almacen']) {
          selected = 'selected="selected"';
        }
        $('#cbo-Almacenes_ReporteFormaPagoProveedor').append('<option value="' + responseAlmacen[i]['ID_Almacen'] + '" ' + selected + ' data-direccion_almacen="' + responseAlmacen[0]['Txt_Direccion_Almacen'] + '">' + responseAlmacen[i]['No_Almacen'] + '</option>');
      }
    }
    $('#modal-loader').modal('hide');
  }, 'JSON');
}

function getReporteHTML(){
  var sMethod, ID_Almacen, Fe_Inicio, Fe_Fin, ID_Tipo_Documento, ID_Serie_Documento, ID_Numero_Documento, Nu_Estado_Documento, iIdProveedor, sNombreProveedor, iTipoVenta, iMedioPago, iIdPersonal, sNombrePersonal, iTipoTarjeta;
    
  sMethod = $( '#hidden-sMethod' ).val();
  Fe_Inicio = ParseDateString($( '#txt-Filtro_Fe_Inicio' ).val(), 1, '/');
  Fe_Fin = ParseDateString($( '#txt-Filtro_Fe_Fin' ).val(), 1, '/');
  ID_Tipo_Documento = $( '#cbo-filtros_tipos_documento' ).val();
  ID_Serie_Documento = ($( '#cbo-filtros_series_documento' ).val().length == 0 ? '-' : $( '#cbo-filtros_series_documento' ).val());
  ID_Numero_Documento = ($( '#txt-Filtro_NumeroDocumento' ).val().length == 0 ? '-' : $( '#txt-Filtro_NumeroDocumento' ).val());
  Nu_Estado_Documento = $( '#cbo-estado_documento' ).val();
  iIdProveedor = ($( '#txt-AID' ).val().length === 0 ? '-' : $( '#txt-AID' ).val());
  sNombreProveedor = ($('#txt-Filtro_Entidad').val().length === 0 ? '-' : $('#txt-Filtro_Entidad').val());
  iIdPersonal = ($('#txt-AID_Personal').val().length === 0 ? '-' : $('#txt-AID_Personal').val());
  sNombrePersonal = ($('#txt-Filtro_Personal').val().length === 0 ? '-' : $('#txt-Filtro_Personal').val());
  iTipoVenta = $( '#cbo-tipo_venta' ).val();
  iMedioPago = $( '#cbo-forma_pago' ).val();
  iTipoTarjeta = $('#cbo-tipo_tarjeta').val();
  ID_Almacen = $('#cbo-Almacenes_ReporteFormaPagoProveedor').val();

  $( '#btn-html_ventas_detalladas_generales' ).text('');
  $( '#btn-html_ventas_detalladas_generales' ).attr('disabled', true);
  $( '#btn-html_ventas_detalladas_generales' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

  $( '#table-ventas_detalladas_generales > tbody' ).empty();
  $( '#table-ventas_detalladas_generales > tfoot' ).empty();

  var arrPost = {
    sMethod : sMethod,
    Fe_Inicio : Fe_Inicio,
    Fe_Fin : Fe_Fin,
    ID_Tipo_Documento : ID_Tipo_Documento,
    ID_Serie_Documento : ID_Serie_Documento,
    ID_Numero_Documento : ID_Numero_Documento,
    Nu_Estado_Documento : Nu_Estado_Documento,
    iIdProveedor : iIdProveedor,
    sNombreProveedor: sNombreProveedor,
    iIdPersonal: iIdPersonal,
    sNombrePersonal: sNombrePersonal,
    iTipoVenta : iTipoVenta,
    iMedioPago: iMedioPago,
    iTipoTarjeta: iTipoTarjeta,
    ID_Almacen: ID_Almacen,
  };      
  url = base_url + 'Logistica/informes_logistica/ReporteFormaPagoProveedorController/sendReporte';
  $.post( url, arrPost, function( response ){
    if ( response.sStatus == 'success' ) {
      var iTotalRegistros = response.arrData.length, response=response.arrData, tr_body = '', tr_foot = '';
      var fTotalItem = 0.00, fTotalGeneral = 0.00;
      var $ID_Almacen = 0, $fTotalGeneralAlmacen = 0.00, $counter_almacen = 0;
      for (var i = 0; i < iTotalRegistros; i++) {
        fTotalItem = (!isNaN(parseFloat(response[i].Ss_Total)) ? parseFloat(response[i].Ss_Total) : 0);

        if ($ID_Almacen != response[i].ID_Almacen) {
          if ($counter_almacen != 0) {
            tr_body +=
            +"<tr>"
              + "<th class='text-right' colspan='14'>Total Almacén</th>"
              + "<th class='text-right'>" + number_format($fTotalGeneralAlmacen, 2) + "</th>"
            + "</tr>";
          }

          $fTotalGeneralAlmacen = 0.00;

          tr_body +=
            "<tr>"
            + "<th class='text-right'><span style='font-size: 15px;'>Almacén</span></th>"
            + "<th class='text-left' colspan='15'><span style='font-size: 15px;'>" + response[i].No_Almacen + "</span></th>"
          + "</tr>";

          $ID_Almacen = response[i].ID_Almacen;
        }// if almacen

        tr_body +=
        "<tr>"
          +"<td class='text-center'>" + response[i].Fe_Emision_Hora + "</td>"
          +"<td class='text-center'>" + response[i].Fe_Emision_Hora_Pago + "</td>"
          +"<td class='text-center'>" + response[i].No_Tipo_Documento_Breve + "</td>"
          +"<td class='text-center'>" + response[i].ID_Serie_Documento + "</td>"
          +"<td class='text-center'>" + response[i].ID_Numero_Documento + "</td>"
          +"<td class='text-center'>" + response[i].No_Tipo_Documento_Identidad_Breve + "</td>"
          +"<td class='text-center'>" + response[i].Nu_Documento_Identidad + "</td>"
          +"<td class='text-left'>" + response[i].No_Entidad + "</td>"
          +"<td class='text-center'>" + response[i].No_Signo + "</td>"
          +"<td class='text-right'>" + number_format(response[i].Ss_Tipo_Cambio, 3) + "</td>"
          +"<td class='text-center'>" + response[i].No_Medio_Pago + "</td>"
          +"<td class='text-center'>" + response[i].No_Tipo_Medio_Pago + "</td>"
          +"<td class='text-center'>" + response[i].Nu_Tarjeta + "</td>"
          +"<td class='text-center'>" + response[i].Nu_Transaccion + "</td>"
          +"<td class='text-right'>" + (response[i].ID_Tipo_Documento != 5 ? '' : '-') + number_format(fTotalItem, 2) + "</td>"
          +"<td class='text-center'><span class='label label-" + response[i].No_Class_Estado + "'>" + response[i].No_Estado + "</span></td>"
          +"<td class='text-center'>" + ((response[i].Nu_Estado == 6) ? response[i].btn_eliminar : '') + "</td>"
          //+"<td class='text-center'>" + (response[i].Nu_Estado == 6 || response[i].Nu_Estado == 8 ? response[i].sAccionVer : '') + "</td>"
          //+"<td class='text-center'>" + (response[i].Nu_Estado == 6 || response[i].Nu_Estado == 8 ? response[i].sAccionImprimir : '') + "</td>"
        +"</tr>";
        
        fTotalGeneral += fTotalItem;
        $fTotalGeneralAlmacen += fTotalItem;
        $counter_almacen++;
      }
      
      tr_foot =
      "<tfoot>"
        +"<tr>"
          +"<th class='text-right' colspan='14'>Total Almacén</th>"
          +"<th class='text-right'>" + number_format($fTotalGeneralAlmacen, 2) + "</th>"
        + "</tr>"
        + "<tr>"
          + "<th class='text-right' colspan='14'>Total</th>"
          + "<th class='text-right'>" + number_format(fTotalGeneral, 2) + "</th>"
        + "</tr>"
      +"</tfoot>";
    } else {
      if( response.sMessageSQL !== undefined ) {
        console.log(response.sMessageSQL);
      }
      tr_body +=
      "<tr>"
        +"<td colspan='17' class='text-center'>" + response.sMessage + "</td>"
      + "</tr>";
    } // ./ if arrData
    
    $( '#div-ventas_detalladas_generales' ).show();
    $( '#table-ventas_detalladas_generales > tbody' ).append(tr_body);
    $( '#table-ventas_detalladas_generales > tbody' ).after(tr_foot);
    
    $( '#btn-html_ventas_detalladas_generales' ).text('');
    $( '#btn-html_ventas_detalladas_generales' ).append( '<i class="fa fa-search"></i> Buscar' );
    $( '#btn-html_ventas_detalladas_generales' ).attr('disabled', false);
  }, 'JSON')
  .fail(function(jqXHR, textStatus, errorThrown) {
    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
    
    $( '#modal-message' ).modal('show');
    $( '.modal-message' ).addClass( 'modal-danger' );
    $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
    setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
    
    //Message for developer
    console.log(jqXHR.responseText);
    
    $( '#btn-html_ventas_detalladas_generales' ).text('');
    $( '#btn-html_ventas_detalladas_generales' ).append( '<i class="fa fa-search color_white"></i> Buscar' );
    $( '#btn-html_ventas_detalladas_generales' ).attr('disabled', false);
  });
}

function eliminarFormaPago(ID){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $( '#btn-save-delete' ).off('click').click(function () {
    _eliminarFormaPago($modal_delete, ID);
  });
}
function _eliminarFormaPago($modal_delete, ID){
  $( '#modal-loader' ).modal('show');
    
  url = base_url + 'Logistica/informes_logistica/ReporteFormaPagoProveedorController/eliminarFormaPago/' + ID;
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
        getReporteHTML();
		  } else {
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
		  }
		  accion_cliente = '';
    },
    error: function (jqXHR, textStatus, errorThrown) {
		  accion_cliente = '';
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