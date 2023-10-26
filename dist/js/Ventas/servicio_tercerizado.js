var url;

$(function () {
  $( '#modal-loader' ).modal('show');
  $( '#div-proceso_planta_lavanderia' ).hide();
  $('.select2').select2();
  
  url = base_url + 'AutocompleteController/sendData';
  $.post( url, {sTabla : 'entidad', iTipoSocio : 2}, function( response ){
    $( '#cbo-filtro_proveedor' ).html( '<option value="0" selected="selected">- Todos -</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-filtro_proveedor' ).append( '<option value="' + response[i].ID + '">' + response[i].Nombre + '</option>' );
  }, 'JSON');

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
    
      var Fe_Inicio, Fe_Fin, iIdTipoDocumento, iIdSerieDocumento, iNumeroDocumento, iEstado, iIdCliente, sNombreCliente, iEstadoLavado, iIdProveedor;
      Fe_Inicio = ParseDateString($( '#txt-Filtro_Fe_Inicio' ).val(), 1, '/');
      Fe_Fin = ParseDateString($( '#txt-Filtro_Fe_Fin' ).val(), 1, '/');
      iIdTipoDocumento = $( '#cbo-filtros_tipos_documento' ).val();
      iIdSerieDocumento = $( '#cbo-filtros_series_documento' ).val();
      iNumeroDocumento = ($( '#txt-Filtro_NumeroDocumento' ).val().length === 0 ? '-' : $( '#txt-Filtro_NumeroDocumento' ).val());
      iEstado = $( '#cbo-estado_documento' ).val();
      iIdCliente = ($( '#txt-AID' ).val().length === 0 ? '-' : $( '#txt-AID' ).val());
      sNombreCliente = ($( '#txt-Filtro_Entidad' ).val().length === 0 ? '-' : $( '#txt-Filtro_Entidad' ).val());
      iEstadoLavado = $( '#cbo-estado_lavado' ).val();
      iIdProveedor = $( '#cbo-filtro_proveedor' ).val();

      if ($(this).data('type') == 'html') {
        getReporteHTML();
      } else if ($(this).data('type') == 'pdf') {
        $( '#btn-pdf_proceso_planta_lavanderia' ).text('');
        $( '#btn-pdf_proceso_planta_lavanderia' ).attr('disabled', true);
        $( '#btn-pdf_proceso_planta_lavanderia' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

        url = base_url + 'Ventas/ServicioTercerizadoController/sendReportePDF/' + Fe_Inicio + '/' + Fe_Fin + '/' + iIdTipoDocumento + '/' + iIdSerieDocumento + '/' + iNumeroDocumento + '/' + iEstado + '/' + iIdCliente + '/' + sNombreCliente + '/' + iEstadoLavado + '/' + iIdProveedor;
        window.open(url,'_blank');
        
        $( '#btn-pdf_proceso_planta_lavanderia' ).text('');
        $( '#btn-pdf_proceso_planta_lavanderia' ).append( '<i class="fa fa-file-pdf-o color_icon_pdf"></i> PDF' );
        $( '#btn-pdf_proceso_planta_lavanderia' ).attr('disabled', false);
      } else if ($(this).data('type') == 'excel') {
        $( '#btn-excel_proceso_planta_lavanderia' ).text('');
        $( '#btn-excel_proceso_planta_lavanderia' ).attr('disabled', true);
        $( '#btn-excel_proceso_planta_lavanderia' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
        
        url = base_url + 'Ventas/ServicioTercerizadoController/sendReporteEXCEL/' + Fe_Inicio + '/' + Fe_Fin + '/' + iIdTipoDocumento + '/' + iIdSerieDocumento + '/' + iNumeroDocumento + '/' + iEstado + '/' + iIdCliente + '/' + sNombreCliente + '/' + iEstadoLavado + '/' + iIdProveedor;
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
  
  $( '#table-detalle_item_modificar_pedido > tbody' ).on('click', '#btn-add_nota_producto_pos', function(){
    var fila = $(this).parents("tr");
    var id_item = fila.find( ".td-sNotaItem" ).data( "id_item" );
    var estado = fila.find( ".td-sNotaItem" ).data( "estado" );
    
    if ( $(this).data('id_item') == id_item && estado == 'mostrar') {
      fila.find( "#td-sNotaItem" + id_item ).show();
      fila.find( ".td-sNotaItem" ).data( "estado", "ocultar" );
      fila.find( ".input-sNotaItem" ).focus();
    } else {
      fila.find( "#td-sNotaItem" + id_item ).hide();
      fila.find( ".td-sNotaItem" ).data( "estado", "mostrar" );
    }
  });

  $( '#btn-modificar_pedido' ).click(function(){
    if ( $( '#cbo-proveedor' ).val()==0 ) {
      $( '#cbo-proveedor' ).closest('.form-group').find('.help-block').html('Seleccionar proveedor');
      $( '#cbo-proveedor' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    
      scrollToError($('.modal-modificar_pedido .modal-body'), $( '#cbo-proveedor' ));
    } else if ( $( '#modal-cbo-estado_lavado' ).val()==0 ) {
      $( '#modal-cbo-estado_lavado' ).closest('.form-group').find('.help-block').html('Seleccionar estado');
      $( '#modal-cbo-estado_lavado' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    
      scrollToError($('.modal-modificar_pedido .modal-body'), $( '#modal-cbo-estado_lavado' ));
    } else {
      $( '.help-block' ).empty();
      $( '#cbo-proveedor' ).closest('.form-group').removeClass('has-error');
      $( '#txt-fe_entrega' ).closest('.form-group').removeClass('has-error');
      $( '#modal-cbo-estado_lavado' ).closest('.form-group').removeClass('has-error');

      $( '#btn-modificar_pedido' ).text('');
      $( '#btn-modificar_pedido' ).attr('disabled', true);
      $( '#btn-modificar_pedido' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

      var arrCabecera=Array(), arrDetalle=Array(), $iIdItem=0, $fCantidadItem, arrDetalleNuevosItems=Array(), $fCantidadItemNuevo = 0, $sNombreItemNuevo='';
      $("#table-detalle_item_modificar_pedido > tbody > tr").each(function(){
        fila = $(this);
        
        $iIdItem = fila.find(".td-iIdItem").text();
        $fCantidadItem = fila.find(".td-fCantidadItem").text();

        obj = {};
        
        obj.iIdItem = $iIdItem;
        obj.fCantidadItem = $fCantidadItem;
        
        arrDetalle.push(obj);
      });

      $("#table-detalle_item_pedido > tbody > tr").each(function(){
        fila = $(this);
        
        $fCantidadItemNuevo = fila.find(".td-fCantidadItemNuevo").text();
        $sNombreItemNuevo = fila.find(".td-sNombreItemNuevo").text();

        obj = {};
        
        obj.fCantidadItemNuevo = $fCantidadItemNuevo;
        obj.sNombreItemNuevo = $sNombreItemNuevo;
        
        arrDetalleNuevosItems.push(obj);
      });

      arrCabecera = {
        'iIdDocumentoCabecera' : $( '#hidden-iIdDocumentoCabeceraModificar' ).val(),
        'iIdProveedor' : $( '#cbo-proveedor' ).val(),
        'dEntrega' : $( '#txt-fe_entrega' ).val(),
        'iEstadoLavado' : $( '#modal-cbo-estado_lavado' ).val(),
      }

      url = base_url + 'Ventas/ServicioTercerizadoController/modificarPedido';
      var $arrParamsPost = {
        arrCabecera : arrCabecera,
        arrDetalle : arrDetalle,
        arrDetalleNuevosItems : arrDetalleNuevosItems,
      };

      $.post( url, $arrParamsPost, function( response ){
        $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
        $( '.modal-message' ).css("z-index", "2000");

        if ( response.sStatus=='success' ) {
          $( '.modal-modificar_pedido' ).modal('hide');

          $( '.modal-message' ).addClass( 'modal-' + response.sStatus);
          $( '.modal-title-message' ).text( response.sMessage );
          setTimeout(function() {$('#modal-message').modal('hide');}, 1100);

          getReporteHTML();
        } else {
          $( '#modal-message' ).modal('show');
          $( '.modal-message' ).addClass( 'modal-' + response.sStatus );
          $( '.modal-title-message' ).text( response.sMessage );
          setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
        } // /. if - else ajax post personal
        
        $( '#btn-modificar_pedido' ).text('');
        $( '#btn-modificar_pedido' ).append( 'Guardar' );
        $( '#btn-modificar_pedido' ).attr('disabled', false);
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
        
        $( '#btn-modificar_pedido' ).text('');
        $( '#btn-modificar_pedido' ).append( 'Guardar' );
        $( '#btn-modificar_pedido' ).attr('disabled', false);
      });
    }// /. if - else validacion guardar modificacion pedido
  });
  
  $( '#btn-verificar_pedido' ).click(function(){
    if ( $('#form-verificar_pedido').serialize() == '' ) {
      $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
      $( '#modal-message' ).modal('show');
      $( '.modal-message' ).addClass( 'modal-danger' );
      $( '.modal-title-message' ).text( 'Debe seleccionar al menos 1 fila' );
      setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
    } else {
      $( '#modal-loader' ).modal('show');
      url = base_url + 'Ventas/ServicioTercerizadoController/verificarPedido';
      $.ajax({
        type : 'POST',
        dataType : 'JSON',
        url : url,
        data : $('#form-verificar_pedido').serialize(),
        success : function( response ){
          $( '#modal-loader' ).modal('hide');
          $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
          $( '#modal-message' ).modal('show');
          
          if (response.sStatus == 'success'){
            $( '.modal-verificar_pedido' ).modal('hide');

            $( '.modal-message' ).addClass( 'modal-' + response.sStatus);
            $( '.modal-title-message' ).text( response.sMessage );
            setTimeout(function() {$('#modal-message').modal('hide');}, 1100);

            getReporteHTML();
          } else {
            $( '.modal-message' ).addClass( 'modal-' + response.sStatus);
            $( '.modal-title-message' ).text(response.sMessage);
            setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
          }

          $( '.btn-save' ).text('');
          $( '.btn-save' ).append( '<span class="fa fa-save"></span> Enviar' );
          $( '.btn-save' ).attr('disabled', false);
        }
      })
      .fail(function(jqXHR, textStatus, errorThrown) {
        $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
        
        $( '#modal-message' ).modal('show');
        $( '.modal-message' ).addClass( 'modal-danger' );
        $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
        setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
        
        $( '#modal-loader' ).modal('hide');
      
        //Message for developer
        console.log(jqXHR.responseText);

        $( '.btn-save' ).text('');
        $( '.btn-save' ).append( '<span class="fa fa-save"></span> Enviar' );
        $( '.btn-save' ).attr('disabled', false);
      });
    }
  });
})// /. document ready

function checkAllMenuHeader(){
	if ( $( '#check-AllMenuHeader' ).prop('checked') ){
		$( '.check-iIdDocumentoCabecera' ).prop('checked', true);
		$( '#check-AllMenuFooter' ).prop('checked', true);
	}else{
		if( false == $( '#check-AllMenuHeader' ).prop('checked') ){
			$( '.check-iIdDocumentoCabecera' ).prop('checked', false);
			$( '#check-AllMenuFooter' ).prop('checked', false);
		}
	}
}

function checkAllMenuFooter(){
	if ( $( '#check-AllMenuFooter' ).prop('checked') ){
		$( '.check-iIdDocumentoCabecera' ).prop('checked', true);
		$( '#check-AllMenuHeader' ).prop('checked', true);
	}else{
		if( false == $( '#check-AllMenuFooter' ).prop('checked') ){
			$( '.check-iIdDocumentoCabecera' ).prop('checked', false);
			$( '#check-AllMenuHeader' ).prop('checked', false);
		}
	}
}

function checkAllMenuHeaderVerificarPedido(){
	if ( $( '#check-AllMenuHeaderVerificarPedido' ).prop('checked') ){
		$( '.check-iIdItem' ).prop('checked', true);
	}else{
		if( false == $( '#check-AllMenuHeaderVerificarPedido' ).prop('checked') ){
			$( '.check-iIdItem' ).prop('checked', false);
		}
	}
}

function cambiarEstadoLavado(){
  if ( $('#form-estado_lavado').serialize() == '' ) {
    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
    $( '#modal-message' ).modal('show');
    $( '.modal-message' ).addClass( 'modal-danger' );
    $( '.modal-title-message' ).text( 'Debe seleccionar al menos 1 fila' );
    setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
  } else {
    var $modal_delete = $( '#modal-message-delete' );
    $modal_delete.modal('show');
    
    $modal_delete.removeClass('modal-danger modal-warning modal-success');
    $modal_delete.addClass('modal-success');
    
    $( '.modal-title-message-delete' ).text('¿Deseas enviar?');

    $( '#btn-cancel-delete' ).off('click').click(function () {
      $modal_delete.modal('hide');
    });

    $( '#btn-save-delete' ).off('click').click(function () {
      $modal_delete.modal('hide');

      $( '.help-block' ).empty();
      $( '#cbo-transporte' ).closest('.form-group').removeClass('has-error');
      $( '.modal-delivery' ).modal('hide');

      $( '.btn-save' ).text('');
      $( '.btn-save' ).attr('disabled', true);
      $( '.btn-save' ).append( 'Enviando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

      $( '#modal-loader' ).modal('show');
      url = base_url + 'Ventas/ServicioTercerizadoController/cambiarEstadoLavado';
      $.ajax({
        type : 'POST',
        dataType : 'JSON',
        url : url,
        data : $('#form-estado_lavado').serialize(),
        success : function( response ){
          $( '#modal-loader' ).modal('hide');
          $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
          $( '#modal-message' ).modal('show');
          
          if (response.sStatus == 'success'){
            $( '.modal-message' ).addClass( 'modal-' + response.sStatus);
            $( '.modal-title-message' ).text( response.sMessage );
            setTimeout(function() {$('#modal-message').modal('hide');}, 1100);

            getReporteHTML();
          } else {
            $( '.modal-message' ).addClass( 'modal-' + response.sStatus);
            $( '.modal-title-message' ).text(response.sMessage);
            setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
          }

          $( '.btn-save' ).text('');
          $( '.btn-save' ).append( '<span class="fa fa-save"></span> Enviar' );
          $( '.btn-save' ).attr('disabled', false);
        }
      })
      .fail(function(jqXHR, textStatus, errorThrown) {
        $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
        
        $( '#modal-message' ).modal('show');
        $( '.modal-message' ).addClass( 'modal-danger' );
        $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
        setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
        
        $( '#modal-loader' ).modal('hide');
      
        //Message for developer
        console.log(jqXHR.responseText);

        $( '.btn-save' ).text('');
        $( '.btn-save' ).append( '<span class="fa fa-save"></span> Enviar' );
        $( '.btn-save' ).attr('disabled', false);
      });
    });
  } // if - else validacion de checkbox
}

function addItemPedido(){
  var $cantidad = $( '#txt-cantidad' ).val();
  var $item = $( '#txt-item' ).val();

  if ( $cantidad.length === 0) {
    $( '#txt-cantidad' ).closest('.form-group').find('.help-block').html('Ingresar cantidad');
    $( '#txt-cantidad' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    
    scrollToError($('.modal-tomar_pedido_prelavado .modal-body'), $( '#txt-cantidad' ));
  } else if ( $item.length === 0 ) {
    $( '#txt-item' ).closest('.form-group').find('.help-block').html('Ingresar producto');
    $( '#txt-item' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    
    scrollToError($('.modal-tomar_pedido_prelavado .modal-body'), $( '#txt-item' ));
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
        + "<td class='text-center td-fCantidadItemNuevo'>" + $cantidad + "</td>"
        + "<td class='text-left td-sNombreItemNuevo'>" + $item + "</td>"
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

function getReporteHTML(){
  var Fe_Inicio, Fe_Fin, iIdTipoDocumento, iIdSerieDocumento, iNumeroDocumento, iEstado, iIdCliente, sNombreCliente, iTipoRecepcionCliente, iEstadoLavado, iIdProveedor;
  
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
  iIdProveedor = $( '#cbo-filtro_proveedor' ).val();

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
    iIdProveedor : iIdProveedor,
  };
  $( '#btn-html_proceso_planta_lavanderia' ).text('');
  $( '#btn-html_proceso_planta_lavanderia' ).attr('disabled', true);
  $( '#btn-html_proceso_planta_lavanderia' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

  $( '#table-proceso_planta_lavanderia > tbody' ).empty();
  $( '#table-proceso_planta_lavanderia > tfoot' ).empty();
  
  url = base_url + 'Ventas/ServicioTercerizadoController/sendReporte';
  $.post( url, arrPost, function( response ){
    if ( response.sStatus == 'success' ) {
      var iTotalRegistros = response.arrData.length, tr_body = '', tr_foot = '', total_s = 0.00, total_s_saldo = 0.00, sum_total_s = 0.00, sum_total_s_saldo = 0.00;
      var response=response.arrData, arrParams = '', sAccionButton = '', sIconoCargando='';
      for (var i = 0; i < iTotalRegistros; i++) {
        total_s = (!isNaN(parseFloat(response[i].Ss_Total)) ? parseFloat(response[i].Ss_Total) : 0);
        total_s_saldo = (!isNaN(parseFloat(response[i].Ss_Total_Saldo)) ? parseFloat(response[i].Ss_Total_Saldo) : 0);

        arrParams = {
          'iIdDocumentoCabecera' : response[i].ID_Documento_Cabecera,
          'iIdDocumentoDetalle' : response[i].ID_Documento_Detalle,
          'sTipoDocumento' : response[i].No_Tipo_Documento_Breve,
          'sSerieDocumento' : response[i].ID_Serie_Documento,
          'sNumeroDocumento' : response[i].ID_Numero_Documento,
          'fCantidadItem' : response[i].Qt_Producto,
          'sNombreItem' : response[i].No_Producto,
          'iEstadoLavado' : response[i].Nu_Estado_Lavado,
          'sNota' : response[i].Txt_Glosa,
        }
        arrParams = JSON.stringify(arrParams);
        
        sAccionButton = '';
        sIconoCargando = "<i class='fa fa-refresh fa-spin fa-lg fa-fw'></i>";
        if ( response[i].Nu_Estado_Lavado == 13 || response[i].Nu_Estado_Lavado == 14 ) {
          sAccionButton = "<button type='button' class='btn btn-xs btn-link' alt='Verificar pedido' title='Verificar pedido' href='javascript:void(0)' onclick='verificarPedido(" + arrParams + ")'>Verificar</button>";
        } else if ( response[i].Nu_Estado_Lavado == 11 ) {
          sAccionButton = "<button type='button' class='btn btn-xs btn-link' alt='Modificar pedido' title='Modificar pedido' href='javascript:void(0)' onclick='modificarPedido(" + arrParams + ")'>Modificar</button>";
        } else if ( response[i].Nu_Estado_Lavado == 15 ) {
          sIconoCargando = '';
        }

        tr_body +=
        "<tr>"
          +"<td class='text-center'>" + (response[i].Nu_Estado_Lavado == 12 ? "<input type='checkbox' id='" + response[i].ID_Documento_Cabecera + "' class='check-iIdDocumentoCabecera' name='arrIdDocumentoCabecera[" + response[i].ID_Documento_Cabecera + "]'>" : '') + "</td>"
          +"<td class='text-center'>" + response[i].Fe_Emision_Hora + "</td>"
          +"<td class='text-center'>" + response[i].No_Tipo_Documento_Breve + "</td>"
          +"<td class='text-center'>" + response[i].ID_Serie_Documento + "</td>"
          +"<td class='text-center'>" + response[i].ID_Numero_Documento + "</td>"
          +"<td class='text-left'>" + response[i].No_Entidad + "</td>"
          +"<td class='text-center'>" + response[i].No_Signo + "</td>"
          +"<td class='text-right'>" + (response[i].ID_Tipo_Documento != 5 ? '' : '-') + number_format(total_s, 2) + "</td>"
          +"<td class='text-right'>" + (response[i].ID_Tipo_Documento != 5 ? '' : '-') + number_format(total_s_saldo, 2) + "</td>"
          +"<td class='text-center'>" + sAccionButton + "</td>"
          +"<td class='text-center'><span class='label label-" + response[i].No_Class_Estado_Lavado + "'>" + response[i].No_Estado_Lavado + "</span>" + sIconoCargando + "</td>"
          +"<td class='text-left'>" + response[i].No_Entidad_Proveedor + "</td>"
        +"</tr>";
        
        sum_total_s += total_s;
        sum_total_s_saldo += total_s_saldo;
      }
      
      tr_foot =
      "<tfoot>"
        +"<tr>"
          +"<th class='text-center'><input type='checkbox' onclick='checkAllMenuFooter();' id='check-AllMenuFooter'></th>"
          +"<th class='text-right' colspan='11'></th>"
          +"<th class='text-center' colspan='2'><button type='button' class='btn btn-success btn-block btn-save' onclick='cambiarEstadoLavado();'><span class='fa fa-truck'></span> Enviar</button></th>"
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

function modificarPedido(arrParams){
  limpiarValores();
  
  $( '#btn-salir' ).off('click').click(function () {
    limpiarValores();
  });

  $( '#modal-header-label-title_modificado' ).text( arrParams.sTipoDocumento + ' - ' + arrParams.sSerieDocumento + ' - ' + arrParams.sNumeroDocumento);

  $( '.modal-header-label-subtitle_nota' ).hide();
  if ( arrParams.sNota != null && arrParams.sNota.length>0 ) {
    $( '.modal-header-label-subtitle_nota' ).show();
    $( '#modal-header-label-subtitle_nota' ).text( 'Nota de caja: ' + clearHTMLTextArea(arrParams.sNota) );
  }

  url = base_url + 'Ventas/ServicioTercerizadoController/getDocumento';
  var arrParams = {
    iIdDocumentoCabecera : arrParams.iIdDocumentoCabecera,
  }
  $.post( url, arrParams, function( response ){
    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
    $( '.modal-message' ).css("z-index", "2000");

    if ( response.sStatus=='success' ) {
      $( '#hidden-iIdDocumentoCabeceraModificar' ).val( arrParams.iIdDocumentoCabecera );

      $( '.modal-modificar_pedido' ).modal('show');

      url = base_url + 'AutocompleteController/sendData';
      $.post( url, {sTabla : 'entidad', iTipoSocio : 2}, function( response ){
        $( '#cbo-proveedor' ).html( '<option value="0" selected="selected">- Seleccionar -</option>');
        for (var i = 0; i < response.length; i++)
          $( '#cbo-proveedor' ).append( '<option value="' + response[i].ID + '">' + response[i].Nombre + '</option>' );
      }, 'JSON');

      var iTotalRegistros = response.arrData.length, response=response.arrData, tr_body='';
      for (var i = 0; i < iTotalRegistros; i++) {
        tr_body +=
        "<tr>"
          +"<td class='text-center td-iIdItem' style='display:none;'>" + response[i].ID_Producto + "</td>"
          +"<td class='text-right td-fCantidadItem'>" + number_format(response[i].Qt_Producto, 2) + "</td>"
          +"<td class='text-left'>" + response[i].No_Producto + ((response[i].Txt_Nota_Detalle!=null && response[i].Txt_Nota_Detalle!='') ? ' ' + response[i].Txt_Nota_Detalle : '') + "</td>"
          //+"<td class='text-right td-sNotaItem' style='display:none;' data-estado='mostrar' data-id_item=" + response[i].ID_Producto + " id='td-sNotaItem" + response[i].ID_Producto + "'>"
          //  +"<input type='text' class='form-control input-sNotaItem' maxlength='250' autocomplete='off'></td>"
          //+"</td>"
          //+"<td class='text-center'>"
          //  +"<button type='button' id='btn-add_nota_producto_pos' data-id_item=" + response[i].ID_Producto + " class='btn btn-sm btn-link' alt='Nota' title='Nota'><i class='fa fa-edit fa-2x' aria-hidden='true'></i></button>"
          //+"</td>"
        +"</tr>";
      }
      $( '#table-detalle_item_modificar_pedido > tbody' ).html(tr_body);

    } else {
      $( '#modal-message' ).modal('show');
      $( '.modal-message' ).addClass( 'modal-' + response.sStatus );
      $( '.modal-title-message' ).text( response.sMessage );
      setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
    } // /. if - else $post ajax response status
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
  });
}

function verificarPedido(arrParams){
  limpiarValores();
  
  $( '#btn-salir' ).off('click').click(function () {
    limpiarValores();
  });

  url = base_url + 'Ventas/ServicioTercerizadoController/getDocumentoProcesadoLavado';
  var arrParams = {
    iIdDocumentoCabecera : arrParams.iIdDocumentoCabecera,
  }
  $.post( url, arrParams, function( response ){
    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
    $( '.modal-message' ).css("z-index", "2000");

    if ( response.sStatus=='success' ) {
      $( '#hidden-iIdDocumentoCabeceraVerificar' ).val( arrParams.iIdDocumentoCabecera );

      $( '.modal-verificar_pedido' ).modal('show');

      $('.select2').select2();
      url = base_url + 'AutocompleteController/sendData';
      $.post( url, {sTabla : 'entidad', iTipoSocio : 2}, function( response ){
        $( '#cbo-proveedor' ).html( '<option value="0" selected="selected">- Seleccionar -</option>');
        for (var i = 0; i < response.length; i++)
          $( '#cbo-proveedor' ).append( '<option value="' + response[i].ID + '">' + response[i].Nombre + '</option>' );
      }, 'JSON');

      var iTotalRegistros = response.arrData.length, response=response.arrData, tr_body='', sCheck = '';
      $( '#hidden-iTotalItemDetalle' ).val( iTotalRegistros );
      for (var i = 0; i < iTotalRegistros; i++) {
        sCheck = (response[i].Nu_Estado_Verificacion == 1 ? "Verficado" : "<input type='checkbox' id='" + response[i].ID_Documento_Estado_Lavado + "' class='check-iIdItem' name='arrIdItem[" + response[i].ID_Documento_Estado_Lavado + "]'>");
        tr_body +=
        "<tr>"
          +"<td class='text-center td-iIdItem' style='display:none;'>" + response[i].ID_Documento_Estado_Lavado + "</td>"
          +"<td class='text-center td-fCantidadItem'>" + response[i].Qt_Producto + "</td>"
          +"<td class='text-center'>" + response[i].No_Producto + "</td>"
          +"<td class='text-center'>" + sCheck +"</td>"
        +"</tr>";
      }
      $( '#table-detalle_item_verificar_pedido > tbody' ).html(tr_body);

    } else {
      $( '#modal-message' ).modal('show');
      $( '.modal-message' ).addClass( 'modal-' + response.sStatus );
      $( '.modal-title-message' ).text( response.sMessage );
      setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
    } // /. if - else $post ajax response status
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
  });
}

function limpiarValores(){
  $( '.help-block' ).empty();
  $( '#txt-item' ).val( '' );
  $( '#txt-item' ).closest('.form-group').removeClass('has-error');
  $( '#table-detalle_item_pedido' ).hide();
  $( '#table-detalle_item_pedido tbody' ).empty();

  $( '.modal-tomar_pedido_ServicioTercerizado' ).modal('hide');
  $( '.modal-finalizar_pedido_ServicioTercerizado' ).modal('hide');

  $( '#hidden-iIdDocumentoCabeceraModificar' ).val( '' );
  
	$('#cbo-proveedor').val('0');
  $('#cbo-proveedor').select2().trigger('change');
	$( '.input-datepicker' ).inputmask('dd/mm/yyyy', { 'placeholder': 'dd/mm/yyyy' });
	$( '.input-datepicker' ).val(fDay + '/' + fMonth + '/' + fYear);
	$( '.input-datepicker' ).datepicker({
		autoclose : true,
		startDate : new Date(fYear + '-' + fMonth + '-' + (parseInt(fDay) + 1)),
		todayHighlight : true
	});
  $('#modal-cbo-estado_lavado').val('0');
}