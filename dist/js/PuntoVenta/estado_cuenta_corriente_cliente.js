var url;

$(function () {
  $( '.div-fecha_historica' ).hide();
  $( '#div-estado_cuenta_corriente_cliente' ).hide();
  
  $( '#modal-loader' ).modal('show');

  $( '#cbo-tipo_consulta_fecha' ).change(function(){
    $( '.div-fecha_historica' ).hide();
    if (  $(this).val() > 0 )
      $( '.div-fecha_historica' ).show();
  })

  url = base_url + 'HelperController/getTiposDocumentos';
  $.post( url, {Nu_Tipo_Filtro : 1}, function( response ){
    $( '#cbo-filtros_tipos_documento' ).html('<option value="0" selected="selected">Todos</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-filtros_tipos_documento' ).append( '<option value="' + response[i].ID_Tipo_Documento + '">' + response[i].No_Tipo_Documento_Breve + '</option>' );
	  $( '#modal-loader' ).modal('hide');
  }, 'JSON');
  
	$( '#cbo-filtros_series_documento' ).html('<option value="0" selected="selected">Todos</option>');
	$( '#cbo-filtros_tipos_documento' ).change(function(){
	  $( '#cbo-filtros_series_documento' ).html('<option value="0" selected="selected">Todos</option>');
	  if ( $(this).val() > 0) {
		  url = base_url + 'HelperController/getSeriesDocumentoPuntoVenta';
      $.post( url, { ID_Tipo_Documento: $(this).val() }, function( response ){
        for (var i = 0; i < response.length; i++)
          $( '#cbo-filtros_series_documento' ).append( '<option value="' + response[i].ID_Serie_Documento + '">' + response[i].ID_Serie_Documento + '</option>' );
      }, 'JSON');
	  }
  })
  
	url = base_url + 'HelperController/getMediosPago';
	var arrPost = {
	  iIdEmpresa : $( '#cbo-Empresas' ).val(),
	};
	$.post( url, arrPost, function( response ){
	  $( '#cbo-forma_pago' ).html('<option value="0">Todos</option>');
	  for (var i = 0; i < response.length; i++)
		$( '#cbo-forma_pago' ).append( '<option value="' + response[i].ID_Medio_Pago + '" data-nu_tipo_medio_pago="' + response[i].Nu_Tipo + '">' + response[i].No_Medio_Pago + '</option>' );
  }, 'JSON');
  
  $( '.div-tipos_tarjetas').hide();
  $( '#cbo-tipo_tarjeta' ).html('<option value="0">Todos</option>');
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

  $( '.btn-generar_estado_cuenta_corriente_cliente' ).click(function(){
    if ( $( '#cbo-tipo_consulta_fecha' ).val() == '1' || ($( '#cbo-tipo_consulta_fecha' ).val() == '0' && $('#header-a-id_matricula_empleado').length) ) {
      $( '.help-block' ).empty();
    
      var Fe_Inicio, Fe_Fin, ID_Tipo_Documento, ID_Serie_Documento, ID_Numero_Documento, Nu_Estado_Documento, iIdCliente, sNombreCliente, iMedioPago, iTipoTarjeta;
      
      iTipoConsultaFecha  = $( '#cbo-tipo_consulta_fecha' ).val();
      Fe_Inicio           = ParseDateString($( '#txt-Filtro_Fe_Inicio' ).val(), 1, '/');
      Fe_Fin              = ParseDateString($( '#txt-Filtro_Fe_Fin' ).val(), 1, '/');
      ID_Tipo_Documento   = $( '#cbo-filtros_tipos_documento' ).val();
      ID_Serie_Documento  = $( '#cbo-filtros_series_documento' ).val();
      ID_Numero_Documento = ($( '#txt-Filtro_NumeroDocumento' ).val().length == 0 ? '-' : $( '#txt-Filtro_NumeroDocumento' ).val());
      Nu_Estado_Documento = $( '#cbo-estado_documento' ).val();
      iIdCliente = ($( '#txt-AID' ).val().length === 0 ? '-' : $( '#txt-AID' ).val());
      sNombreCliente = ($( '#txt-Filtro_Entidad' ).val().length === 0 ? '-' : $( '#txt-Filtro_Entidad' ).val());
      iMedioPago = $( '#cbo-forma_pago' ).val();
      iTipoTarjeta = $( '#cbo-tipo_tarjeta' ).val();
  
      var arrPost = {
        iTipoConsultaFecha  : iTipoConsultaFecha,
        Fe_Inicio           : Fe_Inicio,
        Fe_Fin              : Fe_Fin,
        ID_Tipo_Documento   : ID_Tipo_Documento,
        ID_Serie_Documento  : ID_Serie_Documento,
        ID_Numero_Documento : ID_Numero_Documento,
        Nu_Estado_Documento : Nu_Estado_Documento,
        iIdCliente : iIdCliente,
        sNombreCliente : sNombreCliente,
        iMedioPago : iMedioPago,
        iTipoTarjeta : iTipoTarjeta,
      };

      if ($(this).data('type') == 'html') {
        getReporteHTML(arrPost);
      } else if ($(this).data('type') == 'pdf') {
        $( '#btn-pdf_estado_cuenta_corriente_cliente' ).text('');
        $( '#btn-pdf_estado_cuenta_corriente_cliente' ).attr('disabled', true);
        $( '#btn-pdf_estado_cuenta_corriente_cliente' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
        
        url = base_url + 'PuntoVenta/EstadoCuentaCorrienteClienteController/sendReportePDF/' + iTipoConsultaFecha + '/' + Fe_Inicio + '/' + Fe_Fin + '/' + ID_Tipo_Documento + '/' + ID_Serie_Documento + '/' + ID_Numero_Documento + '/' + Nu_Estado_Documento + '/' + iIdCliente + '/' + sNombreCliente + '/' + iMedioPago + '/' + iTipoTarjeta;
        window.open(url,'_blank');
        
        $( '#btn-pdf_estado_cuenta_corriente_cliente' ).text('');
        $( '#btn-pdf_estado_cuenta_corriente_cliente' ).append( '<i class="fa fa-file-pdf-o color_icon_pdf"></i> PDF' );
        $( '#btn-pdf_estado_cuenta_corriente_cliente' ).attr('disabled', false);
      } else if ($(this).data('type') == 'excel') {
        $( '#btn-excel_estado_cuenta_corriente_cliente' ).text('');
        $( '#btn-excel_estado_cuenta_corriente_cliente' ).attr('disabled', true);
        $( '#btn-excel_estado_cuenta_corriente_cliente' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
        
        url = base_url + 'PuntoVenta/EstadoCuentaCorrienteClienteController/sendReporteEXCEL/'  + iTipoConsultaFecha + '/' + Fe_Inicio + '/' + Fe_Fin + '/' + ID_Tipo_Documento + '/' + ID_Serie_Documento + '/' + ID_Numero_Documento + '/' + Nu_Estado_Documento + '/' + iIdCliente + '/' + sNombreCliente + '/' + iMedioPago + '/' + iTipoTarjeta;
        window.open(url,'_blank');
        
        $( '#btn-excel_estado_cuenta_corriente_cliente' ).text('');
        $( '#btn-excel_estado_cuenta_corriente_cliente' ).append( '<i class="fa fa-file-excel-o color_icon_excel"></i> Excel' );
        $( '#btn-excel_estado_cuenta_corriente_cliente' ).attr('disabled', false);
      }
    } else {
      alert('Primero se debe de aperturar caja');
    }// ./ if - else
  })//./ btn
})

function getReporteHTML(arrPost){  
  $( '#btn-html_estado_cuenta_corriente_cliente' ).text('');
  $( '#btn-html_estado_cuenta_corriente_cliente' ).attr('disabled', true);
  $( '#btn-html_estado_cuenta_corriente_cliente' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

  $( '#table-estado_cuenta_corriente_cliente > tbody' ).empty();
  $( '#table-estado_cuenta_corriente_cliente > tfoot' ).empty();
  
  url = base_url + 'PuntoVenta/EstadoCuentaCorrienteClienteController/sendReporte';
  $.post( url, arrPost, function( response ){
    if ( response.sStatus == 'success' ) {
      var iTotalRegistros = response.arrData.length, response=response.arrData, tr_body = '', tr_foot = '', arrParams = '', sButtonCobrarPedido = '', sButtonEntregarPedido='', sButtonFacturarLavanderia='';
      var subtotal_s = 0.00, descuento_s = 0.00, igv_s = 0.00, total_s = 0.00, total_d = 0.00;
      var sum_general_subtotal_s=0.00, sum_general_igv_s=0.00, sum_general_descuento_s=0.00, sum_general_total_s=0.00, sum_general_total_d=0.00;
      for (var i = 0; i < iTotalRegistros; i++) {
        total_s = (!isNaN(parseFloat(response[i].Ss_Total)) ? parseFloat(response[i].Ss_Total) : 0);
        total_d = (!isNaN(parseFloat(response[i].Ss_Total_Extranjero)) ? parseFloat(response[i].Ss_Total_Extranjero) : 0);
        
        tr_body +=
        "<tr>"
          +"<td class='text-center'>" + response[i].Fe_Emision_Hora + "</td>"
          +"<td class='text-center'>" + response[i].No_Tipo_Documento_Breve + "</td>"
          +"<td class='text-center'>" + response[i].ID_Serie_Documento + "</td>"
          +"<td class='text-center'>" + response[i].ID_Numero_Documento + "</td>"
          +"<td class='text-left'>" + response[i].No_Entidad + "</td>"
          +"<td class='text-right'>" + number_format(response[i].Ss_Tipo_Cambio, 3) + "</td>"
          +"<td class='text-left'>" + response[i].No_Medio_Pago + "</td>"
          +"<td class='text-left'>" + response[i].No_Tipo_Medio_Pago + "</td>"
          +"<td class='text-left'>" + response[i].Nu_Tarjeta + "</td>"
          +"<td class='text-left'>" + response[i].Nu_Transaccion + "</td>"
          +"<td class='text-right'>" + (response[i].ID_Tipo_Documento != 5 ? '' : '-') + number_format(total_s, 2) + "</td>"
          +"<td class='text-right'>" + (response[i].ID_Tipo_Documento != 5 ? '' : '-') + number_format(total_d, 2) + "</td>"
          +"<td class='text-center'><span class='label label-" + response[i].No_Class_Estado + "'>" + response[i].No_Estado + "</span></td>"
        +"</tr>";
        
        sum_general_total_s += total_s;
        sum_general_total_d += total_d;
      }
      
      tr_foot =
      "<tfoot>"
        +"<tr>"
          +"<th class='text-right' colspan='10'>Total</th>"
          +"<th class='text-right'>" + number_format(sum_general_total_s, 2) + "</th>"
          +"<th class='text-right'>" + number_format(sum_general_total_d, 2) + "</th>"
          +"<th class='text-right'></th>"
        +"</tr>"
      +"</tfoot>";
    } else {
      if( response.sMessageSQL !== undefined ) {
        console.log(response.sMessageSQL);
      }
      tr_body +=
      "<tr>"
        +"<td colspan='13' class='text-center'>" + response.sMessage + "</td>"
      + "</tr>";
    } // ./ if arrData
    
    $( '#div-estado_cuenta_corriente_cliente' ).show();
    $( '#table-estado_cuenta_corriente_cliente > tbody' ).append(tr_body);
    $( '#table-estado_cuenta_corriente_cliente > tbody' ).after(tr_foot);
    
    $( '#btn-html_estado_cuenta_corriente_cliente' ).text('');
    $( '#btn-html_estado_cuenta_corriente_cliente' ).append( '<i class="fa fa-search"></i> Buscar' );
    $( '#btn-html_estado_cuenta_corriente_cliente' ).attr('disabled', false);
  }, 'JSON')
  .fail(function(jqXHR, textStatus, errorThrown) {
    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
    
    $( '#modal-message' ).modal('show');
    $( '.modal-message' ).addClass( 'modal-danger' );
    $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
    setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
    
    //Message for developer
    console.log(jqXHR.responseText);
    
    $( '#btn-html_estado_cuenta_corriente_cliente' ).text('');
    $( '#btn-html_estado_cuenta_corriente_cliente' ).append( '<i class="fa fa-search"></i> Buscar' );
    $( '#btn-html_estado_cuenta_corriente_cliente' ).attr('disabled', false);
  });
}