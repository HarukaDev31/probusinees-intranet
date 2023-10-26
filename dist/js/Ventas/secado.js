var url;

$(function () {
  $( '#modal-loader' ).modal('show');
  $( '#div-proceso_planta_lavanderia' ).hide();
  $( '#div-pin' ).show();
  $( '#div-pin_finalizado' ).show();
  $( '#div-detalle_item_pedido' ).hide();
  $( '.div-detalle_item_pedido_finalizado' ).hide();
  $( '.div-estado_lavado' ).hide();
  $( '#table-detalle_item_pedido' ).hide();
  $( '#table-detalle_item_pedido_finalizado' ).hide();
  $( '#btn-procesar_pedido' ).hide();
  
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

        url = base_url + 'Ventas/SecadoController/sendReportePDF/' + Fe_Inicio + '/' + Fe_Fin + '/' + iIdTipoDocumento + '/' + iIdSerieDocumento + '/' + iNumeroDocumento + '/' + iEstado + '/' + iIdCliente + '/' + sNombreCliente;
        window.open(url,'_blank');
        
        $( '#btn-pdf_proceso_planta_lavanderia' ).text('');
        $( '#btn-pdf_proceso_planta_lavanderia' ).append( '<i class="fa fa-file-pdf-o color_icon_pdf"></i> PDF' );
        $( '#btn-pdf_proceso_planta_lavanderia' ).attr('disabled', false);
      } else if ($(this).data('type') == 'excel') {
        $( '#btn-excel_proceso_planta_lavanderia' ).text('');
        $( '#btn-excel_proceso_planta_lavanderia' ).attr('disabled', true);
        $( '#btn-excel_proceso_planta_lavanderia' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
        
        url = base_url + 'Ventas/SecadoController/sendReporteEXCEL/' + Fe_Inicio + '/' + Fe_Fin + '/' + iIdTipoDocumento + '/' + iIdSerieDocumento + '/' + iNumeroDocumento + '/' + iEstado + '/' + iIdCliente + '/' + sNombreCliente;
        window.open(url,'_blank');
        
        $( '#btn-excel_proceso_planta_lavanderia' ).text('');
        $( '#btn-excel_proceso_planta_lavanderia' ).append( '<i class="fa fa-file-excel-o color_icon_excel"></i> Excel' );
        $( '#btn-excel_proceso_planta_lavanderia' ).attr('disabled', false);
      }// /. if all button 
    }// /. if - else validacion
  })// /. btn
  
	$( '#btn-add_item_pedido' ).click(function(){
    addItemPedido();
  });
  
	$( '#table-detalle_item_pedido tbody' ).on('click', '#btn-detele_item_pedido', function(){
    $(this).closest ('tr').remove();
    if ($( '#table-detalle_item_pedido >tbody >tr' ).length == 0) {
      $( '#table-detalle_item_pedido' ).hide();
      $( '#btn-procesar_pedido' ).hide();
    }
  })
  
	$( '#btn-procesar_pedido' ).click(function(){
    generarPedido();
  });
})// /. document ready

function getReporteHTML(){
  var Fe_Inicio, Fe_Fin, iIdTipoDocumento, iIdSerieDocumento, iNumeroDocumento, iEstado, iIdCliente, sNombreCliente;
  
  Fe_Inicio = ParseDateString($( '#txt-Filtro_Fe_Inicio' ).val(), 1, '/');
  Fe_Fin = ParseDateString($( '#txt-Filtro_Fe_Fin' ).val(), 1, '/');
  iIdTipoDocumento = $( '#cbo-filtros_tipos_documento' ).val();
  iIdSerieDocumento = $( '#cbo-filtros_series_documento' ).val();
  iNumeroDocumento = ($( '#txt-Filtro_NumeroDocumento' ).val().length === 0 ? '-' : $( '#txt-Filtro_NumeroDocumento' ).val());
  iEstado = $( '#cbo-estado_documento' ).val();
  iIdCliente = ($( '#txt-AID' ).val().length === 0 ? '-' : $( '#txt-AID' ).val());
  sNombreCliente = ($( '#txt-Filtro_Entidad' ).val().length === 0 ? '-' : $( '#txt-Filtro_Entidad' ).val());

  var arrPost = {
    Fe_Inicio : Fe_Inicio,
    Fe_Fin : Fe_Fin,
    iIdTipoDocumento : iIdTipoDocumento,
    iIdSerieDocumento : iIdSerieDocumento,
    iNumeroDocumento : iNumeroDocumento,
    iEstado : iEstado,
    iIdCliente : iIdCliente,
    sNombreCliente : sNombreCliente,
  };

  $( '#btn-html_proceso_planta_lavanderia' ).text('');
  $( '#btn-html_proceso_planta_lavanderia' ).attr('disabled', true);
  $( '#btn-html_proceso_planta_lavanderia' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

  $( '#table-proceso_planta_lavanderia > tbody' ).empty();
  $( '#table-proceso_planta_lavanderia > tfoot' ).empty();
  
  url = base_url + 'Ventas/SecadoController/sendReporte';
  $.post( url, arrPost, function( response ){
    if ( response.sStatus == 'success' ) {
      var iTotalRegistros = response.arrData.length, tr_body = '', tr_foot = '', total_s = 0.00, total_s_saldo = 0.00, sum_total_s = 0.00, sum_total_s_saldo = 0.00;
      var response=response.arrData, sAccionButton = '', arrParams;
      for (var i = 0; i < iTotalRegistros; i++) {
        total_s = (!isNaN(parseFloat(response[i].Ss_Total)) ? parseFloat(response[i].Ss_Total) : 0);
        total_s_saldo = (!isNaN(parseFloat(response[i].Ss_Total_Saldo)) ? parseFloat(response[i].Ss_Total_Saldo) : 0);

        arrParams = {
          'iIdDocumentoCabecera' : response[i].ID_Documento_Cabecera,
          'iIdEstadoLavado' : response[i].Nu_Estado_Lavado,
          'sNota' : response[i].Txt_Glosa,
        };
        arrParams = JSON.stringify(arrParams);
        sAccionButton = ( response[i].Nu_Estado_Lavado == 16 ? "<button type='button' class='btn btn-xs btn-link' alt='Tomar pedido' title='Tomar pedido' href='javascript:void(0)' onclick='tomarPedido(" + arrParams + ")'>Tomar pedido</button>" : "<button type='button' class='btn btn-xs btn-link' alt='Finalizar pedido' title='Finalizar pedido' href='javascript:void(0)' onclick='finalizarPedido(" + arrParams + ")'>Finalizar pedido</button>" );

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
          +"<td class='text-center'>" + sAccionButton + "</td>"
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
        +"<td colspan='10' class='text-center'>" + response.sMessage + "</td>"
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
}

function tomarPedido(arrParams){
  iIdDocumentoCabecera = arrParams.iIdDocumentoCabecera;
  sNota = arrParams.sNota;

  limpiarValores();

  $( '#hidden-iIdDocumentoCabecera' ).val( iIdDocumentoCabecera );
  $( '#modal-header-label-title_tomar' ).text( iIdDocumentoCabecera );

  $( '.modal-header-label-subtitle_nota' ).hide();
  if ( sNota != null && sNota.length>0 ) {
    $( '.modal-header-label-subtitle_nota' ).show();
    $( '#modal-header-label-subtitle_nota' ).text( 'Nota: ' + sNota );
  }

  $( '.modal-tomar_pedido_Secado' ).modal('show');
  $( '.modal-tomar_pedido_Secado' ).on('shown.bs.modal', function() {
    $( '#tel-pin' ).focus();
  });

  $( '#btn-salir' ).off('click').click(function () {
    limpiarValores();
  });

  $( '#btn-validar_pin_personal' ).off('click').click(function () {
    if ( $( '#tel-pin' ).val().length===0 ) {
      $( '#tel-pin' ).closest('.form-group').find('.help-block').html('Ingresar PIN');
      $( '#tel-pin' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    
      scrollToError($('.modal-tomar_pedido_Secado .modal-body'), $( '#tel-pin' ));
    } else {
      $( '.help-block' ).empty();
      $( '#tel-pin' ).closest('.form-group').removeClass('has-error');
  
      $( '#btn-validar_pin_personal' ).text('');
      $( '#btn-validar_pin_personal' ).attr('disabled', true);
      $( '#btn-validar_pin_personal' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
  
      url = base_url + 'Ventas/SecadoController/verificarPersonalxPIN';
      var arrParams = {
        iPin : $( '#tel-pin' ).val(),
      }
      $.post( url, arrParams, function( response ){
        $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
        $( '.modal-message' ).css("z-index", "2000");
  
        if ( response.sStatus=='success' ) {
          $( '#div-pin' ).hide();
          $( '#h4-datos_personal' ).text( '' + response.arrData[0].No_Entidad );
          $( '#hidden-iIdEntidad' ).val( response.arrData[0].ID_Entidad );
          $( '#div-detalle_item_pedido' ).show();
          $( '#txt-item' ).focus();
        } else {
          $( '#modal-message' ).modal('show');
          $( '.modal-message' ).addClass( 'modal-' + response.sStatus );
          $( '.modal-title-message' ).text( response.sMessage );
          setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
        }
        
        $( '#btn-validar_pin_personal' ).text('');
        $( '#btn-validar_pin_personal' ).append( 'Iniciar' );
        $( '#btn-validar_pin_personal' ).attr('disabled', false);
      }, 'JSON')
      .fail(function(jqXHR, textStatus, errorThrown) {
        $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
        
        $( '#modal-message' ).modal('show');
        $( '.modal-message' ).css("z-index", "2000");
        $( '.modal-message' ).addClass( 'modal-danger' );
        $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
        setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
        
        //Message for developer
        console.log(jqXHR.responseText);
        
        $( '#btn-validar_pin_personal' ).text('');
        $( '#btn-validar_pin_personal' ).append( 'Iniciar' );
        $( '#btn-validar_pin_personal' ).attr('disabled', false);
      });
    }// /. if - else validacion de PIN
  });// /. btn-pin acceder
}

function addItemPedido(){
  var $cantidad = $( '#txt-cantidad' ).val();
  var $item = $( '#txt-item' ).val();

  if ( $cantidad.length === 0) {
    $( '#txt-cantidad' ).closest('.form-group').find('.help-block').html('Ingresar cantidad');
    $( '#txt-cantidad' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    
    scrollToError($('.modal-tomar_pedido_Secado .modal-body'), $( '#txt-cantidad' ));
  } else if ( $item.length === 0 ) {
    $( '#txt-item' ).closest('.form-group').find('.help-block').html('Ingresar producto');
    $( '#txt-item' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    
    scrollToError($('.modal-tomar_pedido_Secado .modal-body'), $( '#txt-item' ));
  } else {
    $( '.help-block' ).empty();
    $( '#txt-cantidad' ).closest('.form-group').removeClass('has-error');
    $( '#txt-item' ).closest('.form-group').removeClass('has-error');
        
    if( isExistTableTemporalEnlacesProducto($item) ){
      $( '#txt-item' ).closest('.form-group').find('.help-block').html('Ya existe item <b>' + $item + '</b>');
      $( '#txt-item' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      var table_item_detalle_pedido =
      "<tr id='tr-item_detalle_pedido" + $item + "'>"
        + "<td class='text-left' style='display:none;'>" + $item + "</td>"
        + "<td class='text-center td-fCantidad'>" + $cantidad + "</td>"
        + "<td class='text-left td-sNombreItem'>" + $item + "</td>"
        + "<td class='text-center'><button type='button' id='btn-detele_item_pedido' class='btn btn-xs btn-link' alt='Eliminar' title='Eliminar'><i class='fa fa-trash-o' aria-hidden='true'> Eliminar</i></button></td>"
      + "</tr>";

      $( '#table-detalle_item_pedido' ).show();
      $( '#table-detalle_item_pedido' ).append(table_item_detalle_pedido);
      $( '#txt-item' ).val('');
      $( '#txt-item' ).focus();

      $( '#btn-procesar_pedido' ).show();
    }// /. if - else table por nombre de item
  }// /. if - else validacion
}

function isExistTableTemporalEnlacesProducto($item){
  return Array.from($('tr[id*=tr-item_detalle_pedido]'))
    .some(element => ($('td:nth(0)',$(element)).html()===$item));
}

function generarPedido(){
  var arrCabecera=Array(), arrDetalle=Array(), $fCantidad = 0.00, $sNombreItem = '';
  $("#table-detalle_item_pedido > tbody > tr").each(function(){
    fila = $(this);
    
    $fCantidad = fila.find(".td-fCantidad").text();
    $sNombreItem = fila.find(".td-sNombreItem").text();

    obj = {};
    
    obj.fCantidad = $fCantidad;
    obj.sNombreItem = $sNombreItem;
    
    arrDetalle.push(obj);
  });

  arrCabecera = {
    'iIdDocumentoCabecera' : $( '#hidden-iIdDocumentoCabecera' ).val(),
    'iIdEntidad' : $( '#hidden-iIdEntidad' ).val(),
  };

  var $url = base_url + 'Ventas/SecadoController/agregarPedido';
  var $arrParamsPost = {
    arrCabecera : arrCabecera,
    arrDetalle : arrDetalle,
  };

  $( '#btn-procesar_pedido' ).text('');
  $( '#btn-salir' ).attr('disabled', true);
  $( '#btn-procesar_pedido' ).attr('disabled', true);
  $( '#btn-procesar_pedido' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

  $.post( $url, $arrParamsPost, function( response ) {
    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
    $( '#modal-message' ).modal('show');

    if ( response.sStatus=='success' ) {
      getReporteHTML();

      $( '.modal-tomar_pedido_Secado' ).modal('hide');

      $( '.modal-message' ).addClass( 'modal-' + response.sStatus );
      $( '.modal-title-message' ).text( response.sMessage );
      setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
      
      limpiarValores();
    } else {
      $( '.modal-message' ).addClass( 'modal-' + response.sStatus );
      $( '.modal-title-message' ).text( response.sMessage );
      setTimeout(function() {$('#modal-message').modal('hide');}, 3100);
    }
    
    $( '#btn-procesar_pedido' ).text('');
    $( '#btn-procesar_pedido' ).append( 'Iniciar Pedido' );
    $( '#btn-procesar_pedido' ).attr('disabled', false);
    $( '#btn-salir' ).attr('disabled', false);
  }, 'json')
  .fail(function(jqXHR, textStatus, errorThrown) {
    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
    
    $( '#modal-message' ).modal('show');
    $( '.modal-message' ).addClass( 'modal-danger' );
    $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
    setTimeout(function() {$('#modal-message').modal('hide');}, 3100);
    
    //Message for developer
    console.log(jqXHR.responseText);

    $( '#btn-procesar_pedido' ).text('');
    $( '#btn-procesar_pedido' ).attr('disabled', false);
    $( '#btn-procesar_pedido' ).append( 'Iniciar Pedido' );
    $( '#btn-salir' ).attr('disabled', false);
  })
}

function limpiarValores(){
  $( '.modal-tomar_pedido_Secado' ).modal('hide');

  $( '#div-pin' ).show();
  $( '#hidden-iIdDocumentoCabecera' ).val( '' );
  $( '#hidden-iIdEntidad' ).val( '' );
  $( '#tel-pin' ).val( '' );

  $( '#h4-datos_personal' ).text( '' );
  $( '#div-detalle_item_pedido' ).hide();
  $( '#txt-cantidad' ).val( '1' );
  $( '#txt-item' ).val( '' );
  $( '#table-detalle_item_pedido' ).hide();
  $( '#table-detalle_item_pedido tbody' ).empty();
  
  $( '#div-pin_finalizado' ).show();
  $( '#tel-pin_finalizado' ).val( '' );
  $( '#hidden-iIdDocumentoCabeceraFinalizado' ).val( '' );
  $( '#hidden-iEstadoLavado' ).val( '' );
  $( '#hidden-iIdEntidadFinalizado' ).val( '' );
  $( '.div-detalle_item_pedido_finalizado' ).hide();
  $( '.div-estado_lavado' ).hide();
  $('[name="Txt_Final_Secado"]').val( '' );
  $( '#table-detalle_item_pedido_finalizado' ).hide();
  $( '#table-detalle_item_pedido_finalizado tbody' ).empty();

  $( '#btn-procesar_pedido' ).hide();
}