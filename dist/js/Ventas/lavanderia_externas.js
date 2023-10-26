var url;

$(function () {
  $( '#modal-loader' ).modal('show');
  $( '#div-proceso_planta_lavanderia' ).hide();
  
  url = base_url + 'HelperController/getTiposDocumentos';
  $.post( url, {Nu_Tipo_Filtro : 3}, function( response ){
    $( '#cbo-filtros_tipos_documento' ).html('<option value="0" selected="selected">Todos</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-filtros_tipos_documento' ).append( '<option value="' + response[i].ID_Tipo_Documento + '">' + response[i].No_Tipo_Documento_Breve + '</option>' );
	  $( '#modal-loader' ).modal('hide');
  }, 'JSON');
  
	$( '#cbo-filtros_series_documento' ).html('<option value="0" selected="selected">Todos</option>');
	$( '#cbo-filtros_tipos_documento' ).change(function(){
	  $( '#cbo-filtros_series_documento' ).html('<option value="0" selected="selected">Todos</option>');
	  if ( $(this).val() > 0) {
		  url = base_url + 'HelperController/getSeriesDocumentoOficinaPuntoVenta';
      $.post( url, { ID_Tipo_Documento: $(this).val() }, function( response ){
        var l = response.length;
        var sTipoSerie = 'oficina';
        for (var i = 0; i < l; i++) {
          sTipoSerie = '(' + ( response[i].ID_POS > 0 ? 'pv' : 'oficina' ) + ')';
          $( '#cbo-filtros_series_documento' ).append( '<option value="' + response[i].ID_Serie_Documento + '">' + response[i].ID_Serie_Documento + ' ' + sTipoSerie + '</option>' );
        }
      }, 'JSON');
	  }
  })
  
  $( '.btn-proceso_planta_lavanderia' ).click(function(){
    if ( $( '#txt-Filtro_Entidad' ).val().length > 0 && $( '#txt-AID' ).val().length === 0 ) {
      $( '#txt-Filtro_Entidad' ).closest('.form-group').find('.help-block').html('Seleccionar cliente');
		  $( '#txt-Filtro_Entidad' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      $( '.help-block' ).empty();
    
      var Fe_Inicio, Fe_Fin, iIdTipoDocumento, iIdSerieDocumento, iNumeroDocumento, iEstado, iIdCliente, sNombreCliente;
      
      Fe_Inicio = ParseDateString($( '#txt-Filtro_Fe_Inicio' ).val(), 1, '/');
      Fe_Fin = ParseDateString($( '#txt-Filtro_Fe_Fin' ).val(), 1, '/');
      iIdTipoDocumento = $( '#cbo-filtros_tipos_documento' ).val();
      iIdSerieDocumento = $( '#cbo-filtros_series_documento' ).val();
      iNumeroDocumento = ($( '#txt-Filtro_NumeroDocumento' ).val().length === 0 ? '-' : $( '#txt-Filtro_NumeroDocumento' ).val());
      iEstado = $( '#cbo-estado_documento' ).val();
      iIdCliente = ($( '#txt-AID' ).val().length === 0 ? '-' : $( '#txt-AID' ).val());
      sNombreCliente = ($( '#txt-Filtro_Entidad' ).val().length === 0 ? '-' : $( '#txt-Filtro_Entidad' ).val());

      if ($(this).data('type') == 'html') {
        getReporteHTML();
      } else if ($(this).data('type') == 'pdf') {
        $( '#btn-pdf_proceso_planta_lavanderia' ).text('');
        $( '#btn-pdf_proceso_planta_lavanderia' ).attr('disabled', true);
        $( '#btn-pdf_proceso_planta_lavanderia' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

        url = base_url + 'Ventas/LavanderiaExternasController/sendReportePDF/' + Fe_Inicio + '/' + Fe_Fin + '/' + iIdTipoDocumento + '/' + iIdSerieDocumento + '/' + iNumeroDocumento + '/' + iEstado + '/' + iIdCliente + '/' + sNombreCliente;
        window.open(url,'_blank');
        
        $( '#btn-pdf_proceso_planta_lavanderia' ).text('');
        $( '#btn-pdf_proceso_planta_lavanderia' ).append( '<i class="fa fa-file-pdf-o color_icon_pdf"></i> PDF' );
        $( '#btn-pdf_proceso_planta_lavanderia' ).attr('disabled', false);
      } else if ($(this).data('type') == 'excel') {
        $( '#btn-excel_proceso_planta_lavanderia' ).text('');
        $( '#btn-excel_proceso_planta_lavanderia' ).attr('disabled', true);
        $( '#btn-excel_proceso_planta_lavanderia' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
        
        url = base_url + 'Ventas/LavanderiaExternasController/sendReporteEXCEL/' + Fe_Inicio + '/' + Fe_Fin + '/' + iIdTipoDocumento + '/' + iIdSerieDocumento + '/' + iNumeroDocumento + '/' + iEstado + '/' + iIdCliente + '/' + sNombreCliente;
        window.open(url,'_blank');
        
        $( '#btn-excel_proceso_planta_lavanderia' ).text('');
        $( '#btn-excel_proceso_planta_lavanderia' ).append( '<i class="fa fa-file-excel-o color_icon_excel"></i> Excel' );
        $( '#btn-excel_proceso_planta_lavanderia' ).attr('disabled', false);
      }// /. if all button 
    }// /. if - else validacion
  })// /. btn buscar

  $( '#btn-cobrar_cliente' ).click(function(){
    if ( $( '#cbo-modal_quien_recibe' ).val() == 0 && $( '[name="sNombreRecepcion"]' ).val().length === 0 ) {
      $( '[name="sNombreRecepcion"]' ).closest('.form-group').find('.help-block').html('Ingresar datos');
      $( '[name="sNombreRecepcion"]' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    
      scrollToError($('.modal-cobrar_cliente .modal-body'), $( '[name="sNombreRecepcion"]' ));
    } else {
      $( '.help-block' ).empty();
      $( '[name="sNombreRecepcion"]' ).closest('.form-group').removeClass('has-error');
      
      $( '#btn-cobrar_cliente' ).text('');
      $( '#btn-cobrar_cliente' ).attr('disabled', true);
      $( '#btn-cobrar_cliente' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
      $( '#btn-salir' ).attr('disabled', true);

      url = base_url + 'Ventas/EstadoLavadoController/entregarPedidoLavado';
      $.ajax({
        type : 'POST',
        dataType : 'JSON',
        url : url,
        data : $('#form-cobrar_cliente').serialize(),
        success : function( response ){
          $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
          $( '#modal-message' ).modal('show');

          if ( response.sStatus=='success' ) {
            $( '.modal-cobrar_cliente' ).modal('hide');

            $( '.modal-message' ).addClass( 'modal-' + response.sStatus);
            $( '.modal-title-message' ).text( response.sMessage );
            setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
            
            getReporteHTML();
          } else {
            $( '.modal-message' ).addClass( 'modal-' + response.sStatus );
            $( '.modal-title-message' ).text( response.sMessage );
            setTimeout(function() {$('#modal-message').modal('hide');}, 3100);
          }
          
          $( '#btn-cobrar_cliente' ).text('');
          $( '#btn-cobrar_cliente' ).append( 'Generar comprobante' );
          $( '#btn-cobrar_cliente' ).attr('disabled', false);
          $( '#btn-salir' ).attr('disabled', false);
        }
      })
      .fail(function(jqXHR, textStatus, errorThrown) {
        $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
        
        $( '#modal-message' ).modal('show');
        $( '.modal-message' ).addClass( 'modal-danger' );
        $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
        setTimeout(function() {$('#modal-message').modal('hide');}, 3100);
        
        //Message for developer
        console.log(jqXHR.responseText);

        $( '#btn-cobrar_cliente' ).text('');
        $( '#btn-cobrar_cliente' ).attr('disabled', false);
        $( '#btn-cobrar_cliente' ).append( 'Generar comprobante' );
        $( '#btn-salir' ).attr('disabled', false);
      })
    }
  })
})// /. document ready

function getReporteHTML(){
  var Fe_Inicio, Fe_Fin, iIdTipoDocumento, iIdSerieDocumento, iNumeroDocumento, iEstado, iIdCliente, sNombreCliente, iTipoRecepcionCliente, iEstadoLavado;
  
  Fe_Inicio = ParseDateString($( '#txt-Filtro_Fe_Inicio' ).val(), 1, '/');
  Fe_Fin = ParseDateString($( '#txt-Filtro_Fe_Fin' ).val(), 1, '/');
  iIdTipoDocumento = $( '#cbo-filtros_tipos_documento' ).val();
  iIdSerieDocumento = $( '#cbo-filtros_series_documento' ).val();
  iNumeroDocumento = ($( '#txt-Filtro_NumeroDocumento' ).val().length === 0 ? '-' : $( '#txt-Filtro_NumeroDocumento' ).val());
  iEstado = $( '#cbo-estado_documento' ).val();
  iIdCliente = ($( '#txt-AID' ).val().length === 0 ? '-' : $( '#txt-AID' ).val());
  sNombreCliente = ($( '#txt-Filtro_Entidad' ).val().length === 0 ? '-' : $( '#txt-Filtro_Entidad' ).val());
  iTipoRecepcionCliente = $( '#cbo-tipo_recepcion_cliente' ).val();
  iEstadoLavado = $( '#cbo-estado_lavado' ).val();

  var arrPost = {
    Fe_Inicio : Fe_Inicio,
    Fe_Fin : Fe_Fin,
    iIdTipoDocumento : iIdTipoDocumento,
    iIdSerieDocumento : iIdSerieDocumento,
    iNumeroDocumento : iNumeroDocumento,
    iEstado : iEstado,
    iIdCliente : iIdCliente,
    sNombreCliente : sNombreCliente,
    iTipoRecepcionCliente : iTipoRecepcionCliente,
    iEstadoLavado : iEstadoLavado,
  };

  $( '#btn-html_proceso_planta_lavanderia' ).text('');
  $( '#btn-html_proceso_planta_lavanderia' ).attr('disabled', true);
  $( '#btn-html_proceso_planta_lavanderia' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

  $( '#table-proceso_planta_lavanderia > tbody' ).empty();
  $( '#table-proceso_planta_lavanderia > tfoot' ).empty();
  
  url = base_url + 'Ventas/LavanderiaExternasController/sendReporte';
  $.post( url, arrPost, function( response ){
    if ( response.sStatus == 'success' ) {
      var iTotalRegistros = response.arrData.length, response=response.arrData, arrParams='', sAccionButton = '', tr_body = '', tr_foot = '', total_s = 0.00, total_s_saldo = 0.00, sum_total_s = 0.00, sum_total_s_saldo = 0.00;
      for (var i = 0; i < iTotalRegistros; i++) {
        total_s = (!isNaN(parseFloat(response[i].Ss_Total)) ? parseFloat(response[i].Ss_Total) : 0);
        total_s_saldo = (!isNaN(parseFloat(response[i].Ss_Total_Saldo)) ? parseFloat(response[i].Ss_Total_Saldo) : 0);

        var arrParams = {
          'iIdEmpresa' : response[i].ID_Empresa,
          'fTotalSaldo' : total_s_saldo,
          'sCliente' : response[i].No_Entidad,
          'sSignoMoneda' : response[i].No_Signo,
          'iIdDocumentoCabecera' : response[i].ID_Documento_Cabecera,
          'iIdDocumentoDetalle' : response[i].ID_Documento_Detalle,
          'sTipoDocumento' : response[i].No_Tipo_Documento_Breve,
          'sSerieDocumento' : response[i].ID_Serie_Documento,
          'sNumeroDocumento' : response[i].ID_Numero_Documento,
          'fCantidadItem' : response[i].Qt_Producto,
          'sNombreItem' : response[i].No_Producto,
          'iEstadoLavado' : response[i].Nu_Estado_Lavado,
          'sNota' : response[i].Txt_Glosa,
          'iIdDocumentoMedioPago' : response[i].ID_Documento_Medio_Pago,
        }
        arrParams = JSON.stringify(arrParams);

        sAccionButton = '';
        if ( total_s_saldo > 0 ) {
          sAccionButton = "<button type='button' class='btn btn-xs btn-link' alt='Cobrar pedido' title='Cobrar pedido' href='javascript:void(0)' onclick='cobrarPedido(" + arrParams + ")'>Cobrar pedido</button>";
        }

        tr_body +=
        "<tr>"
          +"<td class='text-center'>" + response[i].Fe_Emision_Hora + "</td>"
          +"<td class='text-center'>" + response[i].No_Tipo_Documento_Breve + "</td>"
          +"<td class='text-center'>" + response[i].ID_Serie_Documento + "</td>"
          +"<td class='text-center'>" + response[i].ID_Numero_Documento + "</td>"
          +"<td class='text-left'>" + response[i].No_Entidad + "</td>"
          +"<td class='text-center'>" + response[i].No_Signo + "</td>"
          +"<td class='text-right'>" + (response[i].ID_Tipo_Documento != 5 ? '' : '-') + number_format(total_s, 2) + "</td>"
          +"<td class='text-right'>" + (response[i].ID_Tipo_Documento != 5 ? '' : '-') + number_format(total_s_saldo, 2) + "</td>"
          +"<td class='text-center'>" + response[i].Txt_Glosa + "</td>"
          +"<td class='text-center'>" + sAccionButton + "</td>"
          //+"<td class='text-center'>" + (response[i].Nu_Estado_Lavado_Recepcion_Cliente != 1 ? '' : "<button type='button' class='btn btn-xs btn-link' alt='Cobrar pedido' title='Cobrar pedido' href='javascript:void(0)' onclick='cobrarPedido(" + arrParams + ")'>Cobrar pedido</button>") + "</td>"
          +"<td class='text-center'><span class='label label-" + response[i].No_Class_Estado_Lavado + "'>" + response[i].No_Estado_Lavado + "</span><i class='fa fa-refresh fa-spin fa-lg fa-fw'></i></td>"
        +"</tr>";
        
        sum_total_s += total_s;
        sum_total_s_saldo += total_s_saldo;
      }
    } else {
      if( response.sMessageSQL !== undefined ) {
        console.log(response.sMessageSQL);
      }
      tr_body +=
      "<tr>"
        +"<td colspan='9' class='text-center'>" + response.sMessage + "</td>"
      + "</tr>";
    } // ./ if arrData
    
    $( '#div-proceso_planta_lavanderia' ).show();
    $( '#table-proceso_planta_lavanderia > tbody' ).append(tr_body);
    $( '#table-proceso_planta_lavanderia > tbody' ).after(tr_foot);
    
    $( '#btn-html_proceso_planta_lavanderia' ).text('');
    $( '#btn-html_proceso_planta_lavanderia' ).append( '<i class="fa fa-search"></i> Buscar' );
    $( '#btn-html_proceso_planta_lavanderia' ).attr('disabled', false);
  }, 'JSON')
  .fail(function(jqXHR, textStatus, errorThrown) {
    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
    
    $( '#modal-message' ).modal('show');
    $( '.modal-message' ).addClass( 'modal-danger' );
    $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
    setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
    
    //Message for developer
    console.log(jqXHR.responseText);
    
    $( '#btn-html_proceso_planta_lavanderia' ).text('');
    $( '#btn-html_proceso_planta_lavanderia' ).append( '<i class="fa fa-search"></i> Buscar' );
    $( '#btn-html_proceso_planta_lavanderia' ).attr('disabled', false);
  });
} // /. getReporteHTML


function cobrarPedido(arrParams){
  $( '#form-cobrar_cliente' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();

  $( '.div-forma_pago').hide();
  $( '.div-modal_datos_tarjeta_credito').hide();
  $( '.div-estado_lavado_recepcion_cliente' ).hide();
  $( '.div-recibe_otra_persona' ).hide();

  $( '.modal-cobrar_cliente' ).modal('show');

  $( '[name="iIdDocumentoCabecera"]' ).val( arrParams.iIdDocumentoCabecera );
  $( '[name="iIdDocumentoMedioPago"]' ).val( arrParams.iIdDocumentoMedioPago );

  $( '#modal-header-label-title_modificado' ).text( arrParams.sTipoDocumento + ' - ' + arrParams.sSerieDocumento + ' - ' + arrParams.sNumeroDocumento);
  $( '#cobrar_cliente-modal-body-cliente' ).text('Cliente: ' + arrParams.sCliente);
  $( '#cobrar_cliente-modal-body-saldo_cliente' ).text('Saldo: ' + arrParams.sSignoMoneda + ' ' + arrParams.fTotalSaldo);

  $( '#hidden-entregar_pedido-fsaldo' ).val( arrParams.fTotalSaldo );

  $( '.modal-header-label-subtitle_nota' ).hide();
  if ( arrParams.sNota != null && arrParams.sNota.length>0 ) {
    $( '.modal-header-label-subtitle_nota' ).show();
    $( '#modal-header-label-subtitle_nota' ).text( 'Nota de caja: ' + clearHTMLTextArea(arrParams.sNota) );
  }

  $( '.div-forma_pago').show();
  $( '.modal-cobrar_cliente' ).on('shown.bs.modal', function() {
    $( '[name="fPagoCliente"]' ).focus();
    $( '[name="fPagoCliente"]' ).val( arrParams.fTotalSaldo );
  });

  url = base_url + 'HelperController/getMediosPago';
  var arrPost = {
    iIdEmpresa : arrParams.iIdEmpresa,
  };
  $.post( url, arrPost, function( response ){
    $( '#cbo-modal_forma_pago' ).html('');
    for (var i = 0; i < response.length; i++) {
      console.log(response[i].Nu_Tipo);
      if ( response[i].Nu_Tipo != 1 )
        $( '#cbo-modal_forma_pago' ).append( '<option value="' + response[i].ID_Medio_Pago + '" data-nu_tipo_medio_pago="' + response[i].Nu_Tipo + '">' + response[i].No_Medio_Pago + '</option>' );
    }
  }, 'JSON');
  
  // Modal de cobranza al cliente
  $( '#cbo-modal_forma_pago' ).change(function(){
    ID_Medio_Pago = $(this).val();
    Nu_Tipo_Medio_Pago = $(this).find(':selected').data('nu_tipo_medio_pago');
    $( '.div-modal_datos_tarjeta_credito').hide();
    $( '#cbo-modal_tarjeta_credito' ).html('');
    $( '#tel-nu_referencia' ).val('');
    $( '#tel-nu_ultimo_4_digitos_tarjeta' ).val('');
    if (Nu_Tipo_Medio_Pago==2){
      $( '.div-modal_datos_tarjeta_credito').show();

      url = base_url + 'HelperController/getTiposTarjetaCredito';
      $.post( url, {ID_Medio_Pago : ID_Medio_Pago} , function( response ){
        $( '#cbo-modal_tarjeta_credito' ).html('');
        for (var i = 0; i < response.length; i++)
          $( '#cbo-modal_tarjeta_credito' ).append( '<option value="' + response[i].ID_Tipo_Medio_Pago + '">' + response[i].No_Tipo_Medio_Pago + '</option>' );
      }, 'JSON');
    }

    setTimeout(function(){ $( '[name="fPagoCliente"]' ).focus(); $( '[name="fPagoCliente"]' ).select(); }, 20);		
  })

  $( '#cbo-estado_lavado_recepcion_cliente' ).change(function(){
    $( '.div-estado_lavado_recepcion_cliente' ).hide();
    if($(this).val() == 3) {
      $( '.div-estado_lavado_recepcion_cliente' ).show();
    }
  })

  $( '#cbo-modal_quien_recibe' ).change(function(){
    $( '.div-recibe_otra_persona' ).hide();
    if($(this).val() == 0) {
      $( '.div-recibe_otra_persona' ).show();
      $( '[name="sNombreRecepcion"]' ).focus();
    }
  })
}