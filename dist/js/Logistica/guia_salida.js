var url;
var table_guia_salida;
var considerar_igv;
var nu_enlace;
var value_importes_cero = 0.00;
var texto_importes_cero = '0.00';
var arrImpuestosProducto = '{ "arrImpuesto" : [';
var arrImpuestosProductoDetalle;
var accion_guia_salida = '';

function verDocumento(tipo_documento){
  if (tipo_documento == 7) {//Guia de Remisión
    $( "#radio-guia" ).prop("checked", true);
    $( "#radio-ambos" ).prop("checked", false);
    
    $( ".div-Factura" ).hide();
    $( ".div-Guia" ).show();
    
    $( '#cbo-DescargarInventario' ).html( '<option value="1">Si</option>' );
    $( '#cbo-DescargarInventario' ).append( '<option value="0">No</option>' );
    $( '#cbo-DescargarInventario' ).attr('disabled', false);
    $( '#cbo-Almacenes' ).attr('disabled', false);
    
    $( '#error-msgTipoDocumento' ).html('');
    
    $( '#cbo-SeriesDocumento' ).val('');
    $( '#txt-ID_Numero_Documento_Factura' ).val('');
    
    considerar_igv = 0;
    
    var $Ss_SubTotal_Producto = 0.00;
    var $Ss_SubTotal = 0.00;
    var $Ss_Total = 0.00;

  	$( '.txt-Ss_Descuento' ).prop('disabled', true);
    $( '#txt-Ss_Descuento' ).prop('disabled', true);
  	$( '#txt-Ss_Descuento' ).val( '' );
  	
  	$( '#txt-subTotal' ).val( value_importes_cero );
  	$( '#span-subTotal' ).text( texto_importes_cero );
  	
  	$( '#txt-exonerada' ).val( value_importes_cero );
  	$( '#span-exonerada' ).text( texto_importes_cero );
  	
  	$( '#txt-inafecto' ).val( value_importes_cero );
  	$( '#span-inafecto' ).text( texto_importes_cero );
  	
  	$( '#txt-impuesto' ).val( value_importes_cero );
  	$( '#span-impuesto' ).text( texto_importes_cero );
  	
  	$( '#txt-descuento' ).val( value_importes_cero );
  	$( '#span-descuento' ).text( texto_importes_cero );

		$( '#txt-total' ).val( value_importes_cero );
		$( '#span-total' ).text( texto_importes_cero );
		
    $("#table-DetalleGuiasSalidaProductos > tbody > tr").each(function(){
      var rows = $(this);
      var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
      rows.find('txt-Ss_Total_Producto', this).val($Ss_SubTotal_Producto);
      $Ss_SubTotal += $Ss_SubTotal_Producto;
      $Ss_Total += $Ss_SubTotal_Producto;
    })
    
    $( '#txt-subTotal' ).val( $Ss_SubTotal.toFixed(2) );
    $( '#span-subTotal' ).text( $Ss_SubTotal.toFixed(2) );
    
		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
  } else {//Guia y Factura
    $( "#radio-guia" ).prop("checked", false);
    $( "#radio-ambos" ).prop("checked", true);
    
    $( ".div-Factura" ).show();
    $( ".div-Guia" ).show();
    
    url = base_url + 'HelperController/getSeriesDocumento';
    $.post( url, {ID_Tipo_Documento: 3}, function( response ){
      if (response.length == 0)
        $( '#cbo-SeriesDocumento' ).html('<option value="0" selected="selected">Sin serie</option>');
      else if (response.length === 1){//única serie de factura
        $( '#cbo-SeriesDocumento' ).html( '<option value="' + response[0].ID_Serie_Documento + '">' + response[0].ID_Serie_Documento + '</option>' );
        //Get número de la serie de factura
    	  $( '#txt-ID_Numero_Documento_Factura' ).val('');
  		  url = base_url + 'HelperController/getNumeroDocumento';
        $.post( url, { ID_Tipo_Documento: 3, ID_Serie_Documento: response[0].ID_Serie_Documento }, function( response ){
          if (response.length === 0)
            $( '#txt-ID_Numero_Documento_Factura' ).val('');
          else
            $( '#txt-ID_Numero_Documento_Factura' ).val(response.ID_Numero_Documento);
        }, 'JSON');
      } else {
        $( '#cbo-SeriesDocumento' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
        for (var i = 0; i < response.length; i++)
          $( '#cbo-SeriesDocumento' ).append( '<option value="' + response[i].ID_Serie_Documento + '">' + response[i].ID_Serie_Documento + '</option>' );
      }
    }, 'JSON');
  
    $( '#error-msgTipoDocumento' ).html('');
    
    $( '#cbo-DescargarInventario' ).show();
	  if ( $('#txt-EID_Empresa').val() != '' && ($( '#txt-ENu_Descargar_Inventario' ).val() === '0' && $( '#txt-ENu_Descargar_Inventario_Guia' ).val() === '0') ){//Si ya descargo
      $( '#cbo-DescargarInventario' ).html('');
      $( '#cbo-DescargarInventario' ).append( '<option value="1">Si</option>' );
      $( '#cbo-DescargarInventario' ).attr('disabled', true);
      $( '.div-Almacen' ).show();
      $( '#cbo-Almacenes' ).attr('disabled', false);
      
      url = base_url + 'HelperController/getAlmacenes';
      $.post( url, function( response ){
        $( '#cbo-Almacenes' ).html('');
        for (var i = 0; i < response.length; i++)
          $( '#cbo-Almacenes' ).append( '<option value="' + response[i].ID_Almacen + '">' + response[i].No_Almacen + '</option>' );
      }, 'JSON');
	  } else {
      $( '#cbo-DescargarInventario' ).html('');
      $( '#cbo-DescargarInventario' ).append( '<option value="1">Si</option>' );
      $( '#cbo-DescargarInventario' ).attr('disabled', true);
      $( '.div-Almacen' ).show();
      $( '#cbo-Almacenes' ).attr('disabled', true);
      
      url = base_url + 'HelperController/getAlmacenes';
      $.post( url, function( response ){
        $( '#cbo-Almacenes' ).html('');
        for (var i = 0; i < response.length; i++)
          $( '#cbo-Almacenes' ).append( '<option value="' + response[i].ID_Almacen + '">' + response[i].No_Almacen + '</option>' );
      }, 'JSON');
	  }
  
    considerar_igv = 1;
  
    var $Ss_SubTotal = 0.00;
    var $Ss_Inafecto = 0.00;
    var $Ss_Exonerada = 0.00;
    var $Ss_IGV = 0.00;
    var $Ss_Descuento = 0.00;
    var $Ss_Total = 0.00;
    
  	$( '.txt-Ss_Descuento' ).prop('disabled', false);
    $( '#txt-Ss_Descuento' ).prop('disabled', false);
    
    Ss_Descuento = parseFloat($( '#txt-Ss_Descuento' ).val());
    if ( isNaN(Ss_Descuento) )
  	  $( '#txt-Ss_Descuento' ).val( '' );

  	$( '#txt-subTotal' ).val( value_importes_cero );
  	$( '#span-subTotal' ).text( texto_importes_cero );
  	
  	$( '#txt-exonerada' ).val( value_importes_cero );
  	$( '#span-exonerada' ).text( texto_importes_cero );
  	
  	$( '#txt-inafecto' ).val( value_importes_cero );
  	$( '#span-inafecto' ).text( texto_importes_cero );
  	
  	$( '#txt-impuesto' ).val( value_importes_cero );
  	$( '#span-impuesto' ).text( texto_importes_cero );
  	
		$( '#txt-total' ).val( value_importes_cero );
		$( '#span-total' ).text( texto_importes_cero );
		
    $("#table-DetalleGuiasSalidaProductos > tbody > tr").each(function(){
      var rows = $(this);
      var Ss_Impuesto           = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
      var Nu_Tipo_Impuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
      var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
      var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());
      
      if(isNaN($Ss_Descuento_Producto))
        $Ss_Descuento_Producto = 0;
            
      if (Nu_Tipo_Impuesto === 1) {
        $Ss_SubTotal += $Ss_SubTotal_Producto;
        $Ss_IGV += (($Ss_SubTotal_Producto * Ss_Impuesto) - $Ss_SubTotal_Producto);
        rows.find('.txt-Ss_Total_Producto', this).val(($Ss_SubTotal + $Ss_IGV).toFixed(2));
      } else if (Nu_Tipo_Impuesto === 2){
        $Ss_Inafecto += $Ss_SubTotal_Producto;
        rows.find('.txt-Ss_Total_Producto', this).val($Ss_Inafecto);
      } else if (Nu_Tipo_Impuesto === 3){
        $Ss_Exonerada += $Ss_SubTotal_Producto;
        rows.find('.txt-Ss_Total_Producto', this).val($Ss_Exonerada);
      }

      $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto))) / 100);
      $Ss_Total += parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
    });
    
    $( '#txt-subTotal' ).val( $Ss_SubTotal.toFixed(2) );
    $( '#span-subTotal' ).text( $Ss_SubTotal.toFixed(2) );
    
    $( '#txt-exonerada' ).val( $Ss_Exonerada.toFixed(2) );
    $( '#span-exonerada' ).text( $Ss_Exonerada.toFixed(2) );
    
    $( '#txt-inafecto' ).val( $Ss_Inafecto.toFixed(2) );
    $( '#span-inafecto' ).text( $Ss_Inafecto.toFixed(2) );
      
    $( '#txt-impuesto' ).val( $Ss_IGV.toFixed(2) );
    $( '#span-impuesto' ).text( $Ss_IGV.toFixed(2) );
  	
  	$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
  	$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );

		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
  }
}

function agregarGuiaSalida(){
  accion_guia_salida = 'add_guia_salida';
  $( '#modal-loader' ).modal('show');
  
  $( '.div-Listar' ).hide();
  $( '.div-AgregarEditar' ).show();
  
	$( '#txt-EID_Empresa' ).focus();
	
  $( '#form-GuiaSalida' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $('[name="EID_Empresa"]').val('');
  $('[name="EID_Guia_Cabecera"]').val('');
  $('[name="EID_Tipo_Documento_Guia"]').val('');
  $('[name="EID_Serie_Documento_Guia"]').val('');
  $('[name="EID_Numero_Documento_Guia"]').val('');
  $('[name="EID_Documento_Cabecera"]').val('');
  $('[name="EID_Tipo_Documento_Factura"]').val('');
  $('[name="EID_Serie_Documento_Factura"]').val('');
  $('[name="EID_Numero_Documento_Factura"]').val('');
  
  $('[name="ENu_Descargar_Inventario"]').val('');
  $('[name="ENu_Descargar_Inventario_Guia"]').val('');
  
  considerar_igv = 0;
  
  $( ".div-TipoDocumento" ).show();
  
  $( '#cbo-SeriesDocumentoGuia' ).attr('disabled', false);
  
  $( ".div-Factura" ).hide();
  $( ".div-Guia" ).show();

  $( '.date-picker-invoice' ).val(fDay + '/' + fMonth + '/' + fYear);
  
  $( '#cbo-DescargarInventario' ).attr('disabled', false);
  $( '.div-DescargarInventario' ).show();
	$( '.div-Almacen' ).show();
  $( '#cbo-Almacenes' ).attr('disabled', false);
  
	$( '#table-DetalleGuiasSalidaProductos tbody' ).empty();
	
	$( '#panel-DetalleGuiasSalidaProductos' ).removeClass('panel-danger');
	$( '#panel-DetalleGuiasSalidaProductos' ).addClass('panel-default');

	$( '#txt-subTotal' ).val( value_importes_cero );
	$( '#span-subTotal' ).text( texto_importes_cero );
  	
	$( '#txt-exonerada' ).val( value_importes_cero );
	$( '#span-exonerada' ).text( texto_importes_cero );
	
	$( '#txt-inafecto' ).val( value_importes_cero );
	$( '#span-inafecto' ).text( texto_importes_cero );
	
	$( '#txt-impuesto' ).val( value_importes_cero );
	$( '#span-impuesto' ).text( texto_importes_cero );
	
	$( '#txt-descuento' ).val( value_importes_cero );
	$( '#span-descuento' ).text( texto_importes_cero );
	
	$( '#txt-total' ).val( value_importes_cero );
	$( '#span-total' ).text( texto_importes_cero );

  $( '.span-signo' ).text( 'S/' );

  $( '#btn-save' ).attr('disabled', false);
  
  if ($('input[name=radioTipoDocumento]:checked', '#form-GuiaSalida').val() == 7) {//Guia
    $( "#radio-guia" ).prop("checked", true);
    $( "#radio-ambos" ).prop("checked", false);
    
    $( ".div-Factura" ).hide();
    $( ".div-Guia" ).show();
    
    $( '#error-msgTipoDocumento' ).html('');
    
    $( '#cbo-SeriesDocumento' ).html('');
    $( '#txt-ID_Numero_Documento_Factura' ).val('');
  }
  
  url = base_url + 'HelperController/getSeriesDocumento';
  $.post( url, {ID_Tipo_Documento: 7}, function( response ){
    if (response.length === 0)
      $( '#cbo-SeriesDocumentoGuia' ).html('<option value="0" selected="selected">Sin serie</option>');
    else if (response.length === 1) {//única serie de guía de remisión de salida
      $( '#cbo-SeriesDocumentoGuia' ).html( '<option value="' + response[0].ID_Serie_Documento + '">' + response[0].ID_Serie_Documento + '</option>' );
      //Get número de la serie de guía de remisión de salida
  	  $( '#txt-ID_Numero_Documento_Guia' ).val('');
		  url = base_url + 'HelperController/getNumeroDocumento';
      $.post( url, { ID_Tipo_Documento: 7, ID_Serie_Documento: response[0].ID_Serie_Documento }, function( response ){
        if (response.length === 0)
          $( '#txt-ID_Numero_Documento_Guia' ).val('');
        else
          $( '#txt-ID_Numero_Documento_Guia' ).val(response.ID_Numero_Documento);
      }, 'JSON');
    } else {
      $( '#cbo-SeriesDocumentoGuia' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
      for (var i = 0; i < response.length; i++)
        $( '#cbo-SeriesDocumentoGuia' ).append( '<option value="' + response[i].ID_Serie_Documento + '">' + response[i].ID_Serie_Documento + '</option>' );
    }
  }, 'JSON');
  
  url = base_url + 'HelperController/getSeriesDocumento';
  $.post( url, {ID_Tipo_Documento: 3}, function( response ){
    if (response.length == 0)
      $( '#cbo-SeriesDocumento' ).html('<option value="0" selected="selected">Sin serie</option>');
    else if (response.length === 1){//única serie de factura
      $( '#cbo-SeriesDocumento' ).html( '<option value="' + response[0].ID_Serie_Documento + '">' + response[0].ID_Serie_Documento + '</option>' );
      //Get número de la serie de factura
  	  $( '#txt-ID_Numero_Documento_Factura' ).val('');
		  url = base_url + 'HelperController/getNumeroDocumento';
      $.post( url, { ID_Tipo_Documento: 3, ID_Serie_Documento: response[0].ID_Serie_Documento }, function( response ){
        if (response.length === 0)
          $( '#txt-ID_Numero_Documento_Factura' ).val('');
        else
          $( '#txt-ID_Numero_Documento_Factura' ).val(response.ID_Numero_Documento);
      }, 'JSON');
    } else {
      $( '#cbo-SeriesDocumento' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
      for (var i = 0; i < response.length; i++)
        $( '#cbo-SeriesDocumento' ).append( '<option value="' + response[i].ID_Serie_Documento + '">' + response[i].ID_Serie_Documento + '</option>' );
    }
  }, 'JSON');
  
  url = base_url + 'HelperController/getTipoMovimiento';
  $.post( url, {Nu_Tipo_Movimiento : 1}, function( response ){
    $( '#modal-loader' ).modal('hide');
    $( '#cbo-TiposMovimientoSalida' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-TiposMovimientoSalida' ).append( '<option value="' + response[i]['ID_Tipo_Movimiento'] + '">' + response[i]['No_Tipo_Movimiento'] + '</option>' );
  }, 'JSON');
	
  url = base_url + 'HelperController/getMonedas';
  $.post( url , function( response ){
    $( '#cbo-Monedas' ).html('');
    $( '.span-signo' ).text(response[0]['No_Signo']);
    for (var i = 0; i < response.length; i++)
      $( '#cbo-Monedas' ).append( '<option value="' + response[i]['ID_Moneda'] + '" data-no_signo="' + response[i]['No_Signo'] + '">' + response[i]['No_Moneda'] + '</option>' );
  }, 'JSON');
  
  $( '#cbo-DescargarInventario' ).html( '<option value="1">Si</option>' );
  $( '#cbo-DescargarInventario' ).append( '<option value="0">No</option>' );
  
  $( '#cbo-Almacenes' ).html('<option value="0" selected="selected">- Sin almacén -</option>');
  url = base_url + 'HelperController/getAlmacenes';
  $.post( url, function( response ){
    if ( response.length === 1) {//único almacén
      $( '#cbo-Almacenes' ).html( '<option value="' + response[0].ID_Almacen + '">' + response[0].No_Almacen + '</option>' );      
      
      url = base_url + 'HelperController/getListaPrecio';
      $.post( url, {Nu_Tipo_Lista_Precio : $( '[name="Nu_Tipo_Lista_Precio"]' ).val(), ID_Organizacion: 0, ID_Almacen : response[0].ID_Almacen}, function( responseLista ){
        if (responseLista.length === 1)//lista de precio por único almacén
          $( '#cbo-lista_precios' ).html( '<option value="' + responseLista[0].ID_Lista_Precio_Cabecera + '">' + responseLista[0].No_Lista_Precio + '</option>' );
      }, 'JSON');
    } else if (response.length > 1) {//multiple almacén
      $( '#cbo-Almacenes' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
      for (var i = 0; i < response.length; i++)
        $( '#cbo-Almacenes' ).append( '<option value="' + response[i].ID_Almacen + '">' + response[i].No_Almacen + '</option>' );
    }
  }, 'JSON');
  
  //Flete
  url = base_url + 'AutocompleteController/sendData';
  $.post( url, {sTabla : 'entidad', iTipoSocio : 2}, function( response ){
    $( '#cbo-transportista' ).html( '<option value="0" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-transportista' ).append( '<option value="' + response[i].ID + '">' + response[i].Nombre + '</option>' );
  }, 'JSON');
  
  url = base_url + 'HelperController/getMotivosTraslado';
  $.post( url, function( response ){
    $( '#cbo-motivo_traslado' ).html( '<option value="0" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-motivo_traslado' ).append( '<option value="' + response[i].Nu_Valor + '">' + response[i].No_Descripcion + '</option>' );
  }, 'JSON');
  
  $( '#table-DetalleGuiasSalidaProductos' ).hide();
  
  url = base_url + 'HelperController/getImpuestos';
  $.post( url , function( response ){
    arrImpuestosProducto = '';
    arrImpuestosProductoDetalle = '';
    for (var i = 0; i < response.length; i++)
      arrImpuestosProductoDetalle += '{"ID_Impuesto_Cruce_Documento" : "' + response[i].ID_Impuesto_Cruce_Documento + '", "Ss_Impuesto":"' + response[i].Ss_Impuesto + '", "Nu_Tipo_Impuesto":"' + response[i].Nu_Tipo_Impuesto + '", "No_Impuesto":"' + response[i].No_Impuesto + '"},';
    arrImpuestosProducto = '{ "arrImpuesto" : [' + arrImpuestosProductoDetalle.slice(0, -1) + ']}';
  }, 'JSON');
  
  var _ID_Producto = '';
  var option_impuesto_producto = '';
}

function verGuiaSalida(ID, Nu_Tipo_Operacion){
  accion_guia_salida = 'upd_guia_salida';
  $( '#modal-loader' ).modal('show');
  
  $( '.div-Listar' ).hide();
  
	$( '#txt-EID_Empresa' ).focus();
	
  $( '#form-GuiaSalida' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '#panel-DetalleGuiasSalidaProductos' ).removeClass('panel-danger');
  $( '#panel-DetalleGuiasSalidaProductos' ).addClass('panel-default');

  $( '#cbo-TiposMovimientoSalida' ).html('');
  
  $( '#error-msgTipoDocumento' ).html('');
  
	$( '#txt-subTotal' ).val( value_importes_cero );
	$( '#span-subTotal' ).text( texto_importes_cero );
	
	$( '#txt-exonerada' ).val( value_importes_cero );
	$( '#span-exonerada' ).text( texto_importes_cero );
	
	$( '#txt-inafecto' ).val( value_importes_cero );
	$( '#span-inafecto' ).text( texto_importes_cero );
	
	$( '#txt-impuesto' ).val( value_importes_cero );
	$( '#span-impuesto' ).text( texto_importes_cero );
	
	$( '#txt-descuento' ).val( value_importes_cero );
	$( '#span-descuento' ).text( texto_importes_cero );
  
  $( '#txt-total' ).val( value_importes_cero );
  $( '#span-total' ).text( texto_importes_cero );

  $( '#btn-save' ).attr('disabled', false);

  url = base_url + 'Logistica/GuiaSalidaController/ajax_edit/' + ID + '/' + Nu_Tipo_Operacion;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $( '.div-AgregarEditar' ).show();
      
      $('[name="EID_Empresa"]').val(response.arrEdit[0].ID_Empresa);
      $('[name="EID_Guia_Cabecera"]').val(response.arrEdit[0].ID_Guia_Cabecera);
      $('[name="EID_Documento_Cabecera"]').val(response.arrEdit[0].ID_Documento_Cabecera);
      
      $('[name="EID_Tipo_Documento_Guia"]').val(7);
      $('[name="EID_Serie_Documento_Guia"]').val(response.arrEdit[0].ID_Serie_Documento_Guia);
      $('[name="EID_Numero_Documento_Guia"]').val(response.arrEdit[0].ID_Numero_Documento_Guia);
      
      $('[name="ENu_Descargar_Inventario"]').val(response.arrEdit[0].Nu_Descargar_Inventario);
      $('[name="ENu_Descargar_Inventario_Guia"]').val(response.arrEdit[0].Nu_Descargar_Inventario_Guia);
      
      if (Nu_Tipo_Operacion == 7){//Guia
        $( '#txt-Ss_Descuento' ).prop('disabled', true);
        
        $('[name="EID_Serie_Documento_Factura"]').val('');
        $('[name="EID_Serie_Documento_Factura"]').val('');
        $('[name="EID_Numero_Documento_Factura"]').val('');
      } else {
        $( '#txt-Ss_Descuento' ).prop('disabled', true);
        
        $('[name="EID_Tipo_Documento_Factura"]').val(3);
        $('[name="EID_Serie_Documento_Factura"]').val(response.arrEdit[0].ID_Serie_Documento);
        $('[name="EID_Numero_Documento_Factura"]').val(response.arrEdit[0].ID_Numero_Documento);
      }
      //Datos Proveedor
      $('[name="AID"]').val(response.arrEdit[0].ID_Entidad);
      $('[name="ANombre"]').val(response.arrEdit[0].No_Entidad);
      $('[name="ACodigo"]').val(response.arrEdit[0].Nu_Documento_Identidad);
      $('[name="Txt_Direccion_Entidad"]').val(response.arrEdit[0].Txt_Direccion_Entidad);

      $('[name="Txt_Direccion_Llegada"]').val(response.arrEdit[0].Txt_Direccion_Llegada);
      $('[name="Txt_Referencia_Direccion_Llegada"]').val(response.arrEdit[0].Txt_Referencia_Direccion_Llegada);
      
      if (Nu_Tipo_Operacion == 7) {//Guia
        $( ".div-TipoDocumento" ).show();
        $( '#cbo-SeriesDocumentoGuia' ).prop('disabled', true);
        
  		  url = base_url + 'HelperController/getSeriesDocumento';
        $.post( url, { ID_Tipo_Documento: 7 }, function( responseSeriesDocumento ){
          for (var i = 0; i < responseSeriesDocumento.length; i++){
            selected = '';
            if(response.arrEdit[0].ID_Serie_Documento_Guia == responseSeriesDocumento[i].ID_Serie_Documento)
              selected = 'selected="selected"';
            $( '#cbo-SeriesDocumentoGuia' ).append( '<option value="' + responseSeriesDocumento[i].ID_Serie_Documento + '" ' + selected + '>' + responseSeriesDocumento[i].ID_Serie_Documento + '</option>' );
          }
        }, 'JSON');
      
        $('[name="ID_Numero_Documento_Guia"]').val(response.arrEdit[0].ID_Numero_Documento_Guia);
        $( '#cbo-SeriesDocumento' ).html('');
        $( '#txt-ID_Numero_Documento_Factura' ).val('');
        $( "#radio-guia" ).prop("checked", true);
        $( "#radio-ambos" ).prop("checked", false);
        $( ".div-Factura" ).hide();
        $( ".div-Guia" ).show();
        considerar_igv = 0;
      } else {
        $( ".div-TipoDocumento" ).hide();
        $( '#cbo-SeriesDocumentoGuia' ).prop('disabled', true);
        
  		  url = base_url + 'HelperController/getSeriesDocumento';
        $.post( url, { ID_Tipo_Documento: 3 }, function( responseSeriesDocumento ){
          $( '#cbo-SeriesDocumento' ).html('');
          for (var i = 0; i < responseSeriesDocumento.length; i++){
            selected = '';
            if(response.arrEdit[0].ID_Serie_Documento == responseSeriesDocumento[i].ID_Serie_Documento)
              selected = 'selected="selected"';
            $( '#cbo-SeriesDocumento' ).append( '<option value="' + responseSeriesDocumento[i].ID_Serie_Documento + '" ' + selected + '>' + responseSeriesDocumento[i].ID_Serie_Documento + '</option>' );
          }
        }, 'JSON');
        
        $('[name="ID_Numero_Documento_Factura"]').val(response.arrEdit[0].ID_Numero_Documento);
        
  		  url = base_url + 'HelperController/getSeriesDocumento';
        $.post( url, { ID_Tipo_Documento: 7 }, function( responseSeriesDocumento ){
          for (var i = 0; i < responseSeriesDocumento.length; i++){
            selected = '';
            if(response.arrEdit[0].ID_Serie_Documento_Guia == responseSeriesDocumento[i].ID_Serie_Documento)
              selected = 'selected="selected"';
            $( '#cbo-SeriesDocumentoGuia' ).append( '<option value="' + responseSeriesDocumento[i].ID_Serie_Documento + '" ' + selected + '>' + responseSeriesDocumento[i].ID_Serie_Documento + '</option>' );
          }
        }, 'JSON');
      
        $('[name="ID_Numero_Documento_Guia"]').val(response.arrEdit[0].ID_Numero_Documento_Guia);
        $( "#radio-guia" ).prop("checked", false);
        $( "#radio-ambos" ).prop("checked", true);
        $( ".div-Factura" ).show();
        $( ".div-Guia" ).show();
        considerar_igv = 1;
      }
      
      url = base_url + 'HelperController/getTipoMovimiento';
      $.post( url, {Nu_Tipo_Movimiento : 1}, function( responseTiposMovimiento ){
        for (var i = 0; i < responseTiposMovimiento.length; i++){
          selected = '';
          if(response.arrEdit[0].ID_Tipo_Movimiento == responseTiposMovimiento[i]['ID_Tipo_Movimiento'])
            selected = 'selected="selected"';
          $( '#cbo-TiposMovimientoSalida' ).append( '<option value="' + responseTiposMovimiento[i]['ID_Tipo_Movimiento'] + '" ' + selected + '>' + responseTiposMovimiento[i]['No_Tipo_Movimiento'] + '</option>' );
        }
      }, 'JSON');
      
      $( '[name="Fe_Emision"]' ).val(ParseDateString(response.arrEdit[0].Fe_Emision, 6, '-'));
      
      url = base_url + 'HelperController/getMonedas';
      $.post( url , function( responseMonedas ){
        $( '#cbo-Monedas' ).html('');
        for (var i = 0; i < responseMonedas.length; i++){
          selected = '';
          if(response.arrEdit[0].ID_Moneda == responseMonedas[i]['ID_Moneda']){
            selected = 'selected="selected"';
	          $( '.span-signo' ).text( responseMonedas[i]['No_Signo'] );
          }
          $( '#cbo-Monedas' ).append( '<option value="' + responseMonedas[i]['ID_Moneda'] + '" data-no_signo="' + responseMonedas[i]['No_Signo'] + '" ' + selected + '>' + responseMonedas[i]['No_Moneda'] + '</option>' );
        }
      }, 'JSON');
    	
    	if (response.arrEdit[0].Nu_Descargar_Inventario === '0' || response.arrEdit[0].Nu_Descargar_Inventario_Guia === '0') {
    	  $( '.div-DescargarInventario' ).show();
        $( '#cbo-DescargarInventario' ).attr('disabled', false);
        $( '#cbo-Almacenes' ).attr('disabled', false);
      	$( '.div-Almacen' ).hide();
    	  
        url = base_url + 'HelperController/getDescargarInventario';
        $.post( url , function( responseDescargarInventario ){
          $( '#cbo-DescargarInventario' ).html('');
          for (var i = 0; i < responseDescargarInventario.length; i++){
            selected = '';
            if(response.arrEdit[0].Nu_Descargar_Inventario == responseDescargarInventario[i]['Nu_Valor'])
              selected = 'selected="selected"';
            $( '#cbo-DescargarInventario' ).append( '<option value="' + responseDescargarInventario[i]['Nu_Valor'] + '" ' + selected + '>' + responseDescargarInventario[i]['No_Descripcion'] + '</option>' );
          }
        }, 'JSON');
        
      	$( '#cbo-DescargarInventario' ).change(function(){
      	  $( '.div-Almacen' ).hide();
      	  $( '#cbo-Almacenes' ).html( '<option value="" selected="selected">- Seleccionar -</option>');
      	  if ($(this).val() > 0 ){
            $( '.div-Almacen' ).show();
            url = base_url + 'HelperController/getAlmacenes';
            $.post( url, {ID_Organizacion : response.arrEdit[0].ID_Organizacion} , function( response ){
              $( '#cbo-Almacenes' ).html( '<option value="0" selected="selected">- Seleccionar -</option>');
              for (var i = 0; i < response.length; i++)
                $( '#cbo-Almacenes' ).append( '<option value="' + response[i].ID_Almacen + '">' + response[i].No_Almacen + '</option>' );
            }, 'JSON');
          }
      	})
    	} else {//Ya no descarga stock
    	  $( '.div-DescargarInventario' ).hide();
        $( '#cbo-DescargarInventario' ).html('');
        $( '#cbo-DescargarInventario' ).append( '<option value="1">Si</option>' );
        $( '.div-Almacen' ).show();
        $( '#cbo-Almacenes' ).attr('disabled', true);
        
        url = base_url + 'HelperController/getAlmacenes';
        $.post( url, function( responseAlmacenes ){
          $( '#cbo-Almacenes' ).html('');
          for (var i = 0; i < responseAlmacenes.length; i++){
            selected = '';
            if(response.arrEdit[0].ID_Almacen == responseAlmacenes[i].ID_Almacen)
              selected = 'selected="selected"';
            $( '#cbo-Almacenes' ).append( '<option value="' + responseAlmacenes[i].ID_Almacen + '" ' + selected + '>' + responseAlmacenes[i].No_Almacen + '</option>' );
          }
        }, 'JSON');
    	}
    	
	    $( '#cbo-lista_precios' ).html('');
      url = base_url + 'HelperController/getListaPrecio';
      $.post( url, {Nu_Tipo_Lista_Precio : $( '[name="Nu_Tipo_Lista_Precio"]' ).val(), ID_Organizacion: 0, ID_Almacen : response.arrEdit[0].ID_Almacen}, function( responseLista ){
        for (var i = 0; i < responseLista.length; i++) {
          selected = '';
          if(response.arrEdit[0].ID_Lista_Precio_Cabecera == responseLista[i].ID_Lista_Precio_Cabecera)
            selected = 'selected="selected"';
          $( '#cbo-lista_precios' ).append( '<option value="' + responseLista[i].ID_Lista_Precio_Cabecera + '" ' + selected + '>' + responseLista[i].No_Lista_Precio + '</option>' );
        }
      }, 'JSON');
      
      //Flete
      url = base_url + 'AutocompleteController/sendData';
      $.post( url, {sTabla : 'entidad', iTipoSocio : 2}, function( responseTransportista ){
        $( '#cbo-transportista' ).html('');
        for (var i = 0; i < responseTransportista.length; i++) {
          selected = '';
          if(response.arrEdit[0].ID_Entidad_Transportista == responseTransportista[i].ID)
            selected = 'selected="selected"';
          $( '#cbo-transportista' ).append( '<option value="' + responseTransportista[i].ID + '" ' + selected + '>' + responseTransportista[i].Nombre + '</option>' );
        }
      }, 'JSON');
      
      url = base_url + 'HelperController/getMotivosTraslado';
      $.post( url, function( responseMT ){
        $( '#cbo-motivo_traslado' ).html('');
        for (var i = 0; i < responseMT.length; i++) {
          selected = '';
          if(response.arrEdit[0].Nu_Tipo_Motivo_Traslado == responseMT[i].Nu_Valor)
            selected = 'selected="selected"';
          $( '#cbo-motivo_traslado' ).append( '<option value="' + responseMT[i].Nu_Valor + '" ' + selected + '>' + responseMT[i].No_Descripcion + '</option>' );
        }
      }, 'JSON');
      
      $('[name="Fe_Traslado"]').val(ParseDateString(response.arrEdit[0].Fe_Traslado, 6, '-'));
      $('[name="No_Chofer"]').val(response.arrEdit[0].No_Chofer);
      $('[name="Nu_Licencia"]').val(response.arrEdit[0].Nu_Licencia);
      $('[name="No_Placa"]').val(response.arrEdit[0].No_Placa);
      $('[name="Txt_Certificado_Inscripcion"]').val(response.arrEdit[0].Txt_Certificado_Inscripcion);
  
      $('[name="Txt_Glosa"]').val( clearHTMLTextArea(response.arrEdit[0].Txt_Glosa) );

      //Detalle
      $( '#table-DetalleGuiasSalidaProductos' ).show();
      $( '#table-DetalleGuiasSalidaProductos tbody' ).empty();
      
      var table_detalle_producto = '';
      var _ID_Producto = '';
      var $Ss_SubTotal_Producto = 0.00;
      var $Ss_IGV_Producto = 0.00;
      var $Ss_Descuento_Producto = 0.00;
      var $Ss_Total_Producto = 0.00;
      var $Ss_SubTotal = 0.00;
      var $Ss_Exonerada = 0.00;
      var $Ss_Inafecto = 0.00;
      var $Ss_IGV = 0.00;
      var $Ss_Total = 0.00;
      var option_impuesto_producto = '';
      
      var $fDescuento_Producto = 0;
      var fDescuento_Total_Producto = 0;
      var globalImpuesto = 0;
      var $iDescuentoGravada = 0;
      var $iDescuentoInafecto = 0;
      var $iDescuentoExonerada = 0;
      var $iDescuentoGlobalImpuesto = 0;
      var selected;
      
      for (var i = 0; i < response.arrEdit.length; i++) {
        if (_ID_Producto != response.arrEdit[i].ID_Producto) {
          _ID_Producto = response.arrEdit[i].ID_Producto;
          option_impuesto_producto = '';
        }
        
        $Ss_SubTotal_Producto = parseFloat(response.arrEdit[i].Qt_Producto * response.arrEdit[i].Ss_Precio);
        if (response.arrEdit[i].Nu_Tipo_Impuesto === '1'){
          fDescuento_Total_Producto = Math.round10(fDescuento_Total_Producto, -3);
          $Ss_SubTotal_Producto = Math.round10($Ss_SubTotal_Producto, -2);
          $fDescuento_Producto = ((response.arrEdit[i].Ss_Descuento_Producto * $Ss_SubTotal_Producto) / 100);
          fDescuento_Total_Producto = Math.round10(fDescuento_Total_Producto, -2);
          $Ss_IGV_Producto = parseFloat(response.arrEdit[i].Ss_Impuesto);
          $Ss_SubTotal += $Ss_SubTotal_Producto - $fDescuento_Producto;
          fDescuento_Total_Producto = parseFloat(($Ss_SubTotal_Producto - $fDescuento_Producto) * $Ss_IGV_Producto);
          $Ss_IGV += Math.round10(fDescuento_Total_Producto, -2) - ($Ss_SubTotal_Producto - $fDescuento_Producto);
          globalImpuesto = $Ss_IGV_Producto;
          $Ss_Total_Producto = (($Ss_SubTotal_Producto - $fDescuento_Producto) * $Ss_IGV_Producto);
          $iDescuentoGravada = 1;
          $Ss_SubTotal_Producto = $Ss_SubTotal_Producto - $fDescuento_Producto;
          $Ss_SubTotal_Producto = Math.round10($Ss_SubTotal_Producto, -2);
        } else if (response.arrEdit[i].Nu_Tipo_Impuesto === '2'){
          $fDescuento_Producto = ((response.arrEdit[i].Ss_Descuento_Producto * $Ss_SubTotal_Producto) / 100);
          $Ss_Inafecto += $Ss_SubTotal_Producto - $fDescuento_Producto;
          $Ss_Total_Producto = $Ss_SubTotal_Producto - $fDescuento_Producto;
          $Ss_Total_Producto = Math.round10($Ss_Total_Producto, -2);
          globalImpuesto += 0;
          $iDescuentoInafecto = 1;
          $Ss_SubTotal_Producto = $Ss_SubTotal_Producto - $fDescuento_Producto;
          $Ss_SubTotal_Producto = Math.round10($Ss_SubTotal_Producto, -2);
        } else if (response.arrEdit[i].Nu_Tipo_Impuesto === '3'){
          $fDescuento_Producto = ((response.arrEdit[i].Ss_Descuento_Producto * $Ss_SubTotal_Producto) / 100);
          $Ss_Exonerada += $Ss_SubTotal_Producto - $fDescuento_Producto;
          $Ss_Total_Producto = $Ss_SubTotal_Producto - $fDescuento_Producto;
          $Ss_Total_Producto = Math.round10($Ss_Total_Producto, -2);
          globalImpuesto += 0;
          $iDescuentoExonerada = 1;
          $Ss_SubTotal_Producto = $Ss_SubTotal_Producto - $fDescuento_Producto;
          $Ss_SubTotal_Producto = Math.round10($Ss_SubTotal_Producto, -2);
        } else {
          $fDescuento_Producto = ((response.arrEdit[i].Ss_Descuento_Producto * $Ss_SubTotal_Producto) / 100);
          $Ss_SubTotal += $Ss_SubTotal_Producto - $fDescuento_Producto;
          $Ss_Total_Producto = $Ss_SubTotal_Producto - $fDescuento_Producto;
          $Ss_Total_Producto = Math.round10($Ss_Total_Producto, -2);
          globalImpuesto += 0;
          $iDescuentoExonerada = 1;
          $Ss_SubTotal_Producto = $Ss_SubTotal_Producto - $fDescuento_Producto;
          $Ss_SubTotal_Producto = Math.round10($Ss_SubTotal_Producto, -2);
        }
        
        $Ss_Descuento_Producto += parseFloat(response.arrEdit[i].Ss_Descuento_Producto);
        $Ss_Total += $Ss_Total_Producto;
        
	      for (var x = 0; x < response.arrImpuesto.length; x++){
	        selected = '';
	        if (response.arrImpuesto[x].ID_Impuesto_Cruce_Documento == response.arrEdit[i].ID_Impuesto_Cruce_Documento)
	          selected = 'selected="selected"';
          option_impuesto_producto += "<option value='" + response.arrImpuesto[x].ID_Impuesto_Cruce_Documento + "' data-nu_tipo_impuesto='" + response.arrImpuesto[x].Nu_Tipo_Impuesto + "' data-impuesto_producto='" + response.arrImpuesto[x].Ss_Impuesto + "' " + selected + ">" + response.arrImpuesto[x].No_Impuesto + "</option>";
	      }
	      
        table_detalle_producto += 
        "<tr id='tr_detalle_producto" + response.arrEdit[i].ID_Producto + "'>"
          +"<td style='display:none;' class='text-left td-iIdItem'>" + response.arrEdit[i].ID_Producto + "</td>"
          +"<td class='text-right'><input type='tel' class='txt-Qt_Producto form-control input-decimal' value='" + response.arrEdit[i].Qt_Producto + "' autocomplete='off'></td>"
          +"<td class='text-left'>" + response.arrEdit[i].No_Producto + "</td>"
          +"<td class='text-right'><input type='text' class='txt-Ss_Precio form-control input-decimal' value='" + response.arrEdit[i].Ss_Precio + "' autocomplete='off'></td>"
          +"<td class='text-right'>"
            +"<select class='cbo-ImpuestosProducto form-control required' style='width: 100%;'>"
              +option_impuesto_producto
            +"</select>"
          +"</td>"
          +"<td class='text-right'><input type='tel' class='txt-Ss_SubTotal_Producto form-control' value='" + $Ss_SubTotal_Producto + "' autocomplete='off' disabled></td>"
          +"<td class='text-right'><input type='tel' class='txt-Ss_Descuento form-control input-decimal' value='" + (response.arrEdit[i].Ss_Descuento_Producto == 0.00 ? '' : response.arrEdit[i].Ss_Descuento_Producto) + "' autocomplete='off'></td>"
          +"<td class='text-right'><input type='text' class='txt-Ss_Total_Producto form-control input-decimal' value='" + $Ss_Total_Producto.toFixed(2) + "' autocomplete='off'></td>"
          +"<td class='text-center'><button type='button' id='btn-deleteProducto' class='btn btn-sm btn-link' alt='Eliminar' title='Eliminar'><i class='fa fa-trash-o fa-2x' aria-hidden='true'> </i></button></td>"
        +"</tr>";
      }
      
		  $( '#table-DetalleGuiasSalidaProductos >tbody' ).append(table_detalle_producto);
    
      if ($Ss_Descuento_Producto == 0){
        if ($Ss_SubTotal > 0.00) {
          $Ss_SubTotal = $Ss_SubTotal - ((response.arrEdit[0].Po_Descuento * $Ss_SubTotal) / 100);
          $Ss_SubTotal = Math.round10($Ss_SubTotal, -3);
          $Ss_SubTotal = Math.round10($Ss_SubTotal, -2);
          $Ss_IGV = ($Ss_SubTotal * globalImpuesto) - $Ss_SubTotal;
        }
        
        if ($Ss_Inafecto > 0.00) {
          $Ss_Inafecto = $Ss_Inafecto - ((response.arrEdit[0].Po_Descuento * $Ss_Inafecto) / 100);
          $Ss_Inafecto = Math.round10($Ss_Inafecto, -3);
          $Ss_Inafecto = Math.round10($Ss_Inafecto, -2);
        }
        
        if ($Ss_Exonerada > 0.00) {
          $Ss_Exonerada = $Ss_Exonerada - ((response.arrEdit[0].Po_Descuento * $Ss_Exonerada) / 100);
          $Ss_Exonerada = Math.round10($Ss_Exonerada, -3);
          $Ss_Exonerada = Math.round10($Ss_Exonerada, -2);
        }
        
        $Ss_Total = ($Ss_SubTotal * globalImpuesto) + $Ss_Inafecto + $Ss_Exonerada;
      }
      
      $( '#txt-subTotal' ).val( $Ss_SubTotal.toFixed(2) );
      $( '#span-subTotal' ).text( $Ss_SubTotal.toFixed(2) );
      
      $( '#txt-exonerada' ).val( $Ss_Exonerada.toFixed(2) );
      $( '#span-exonerada' ).text( $Ss_Exonerada.toFixed(2) );
      
      $( '#txt-inafecto' ).val( $Ss_Inafecto.toFixed(2) );
      $( '#span-inafecto' ).text( $Ss_Inafecto.toFixed(2) );
      
      if (parseFloat(response.arrEdit[0].Ss_Descuento) > 0 && $Ss_Descuento_Producto == 0)
        $( '#txt-Ss_Descuento' ).val( response.arrEdit[0].Po_Descuento );
      else
        $( '#txt-Ss_Descuento' ).val( '' );

      $( '#txt-impuesto' ).val( $Ss_IGV.toFixed(2) );
      $( '#span-impuesto' ).text( $Ss_IGV.toFixed(2) );

      $( '#txt-descuento' ).val( response.arrEdit[0].Ss_Descuento );
      $( '#span-descuento' ).text( response.arrEdit[0].Ss_Descuento );
    
			$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
			$( '#span-total' ).text( $Ss_Total.toFixed(2) );
  		
  		validateDecimal();
  		validateNumber();
  		validateNumberOperation();
			
      url = base_url + 'HelperController/getImpuestos';
      $.post( url , function( response ){
        arrImpuestosProducto = '';
        arrImpuestosProductoDetalle = '';
        for (var i = 0; i < response.length; i++)
          arrImpuestosProductoDetalle += '{"ID_Impuesto_Cruce_Documento" : "' + response[i].ID_Impuesto_Cruce_Documento + '", "Ss_Impuesto":"' + response[i].Ss_Impuesto + '", "Nu_Tipo_Impuesto":"' + response[i].Nu_Tipo_Impuesto + '", "No_Impuesto":"' + response[i].No_Impuesto + '"},';
        arrImpuestosProducto = '{ "arrImpuesto" : [' + arrImpuestosProductoDetalle.slice(0, -1) + ']}';
      }, 'JSON');
      
      var _ID_Producto = '';
      var option_impuesto_producto = '';
      
      if (Nu_Tipo_Operacion == 7) {//Guia
        $( '.txt-Ss_Descuento' ).val( '' );
      	$( '.txt-Ss_Descuento' ).prop('disabled', true);
      } else
      	$( '.txt-Ss_Descuento' ).prop('disabled', false);
  
      $( '#modal-loader' ).modal('hide');
    }
  })
}

function anularGuiaSalida(ID, Nu_Tipo_Operacion, Nu_Descargar_Inventario){
  var $modal_delete = $( '.modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '.modal-message-delete' ).removeClass('modal-danger modal-warning modal-success');
  $( '.modal-message-delete' ).addClass('modal-warning');
  
  $( '.modal-title-message-delete' ).text('¿Deseas anular la Guía Remisión?');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });
  accion_guia_salida = 'anular_guia_salida';
  $(document).keyup(function(event){
    if(event.which === 13 && accion_guia_salida === 'anular_guia_salida')//Tecla ENTER
      anularData_GuiaSalida($modal_delete, ID, Nu_Tipo_Operacion, Nu_Descargar_Inventario);
  });

  $( '#btn-save-delete' ).off('click').click(function () {
    anularData_GuiaSalida($modal_delete, ID, Nu_Tipo_Operacion, Nu_Descargar_Inventario);
  });
}

function eliminarGuiaSalida(ID, Nu_Tipo_Operacion, Nu_Descargar_Inventario){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '.modal-message-delete' ).removeClass('modal-danger modal-warning modal-success');
  $( '.modal-message-delete' ).addClass('modal-danger');
  
  $( '.modal-title-message-delete' ).text('¿Deseas eliminar la Guía Remisión?');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });
  accion_guia_salida = 'eliminar_guia_salida';
  $(document).keyup(function(event){
    if(event.which === 13 && accion_guia_salida === 'eliminar_guia_salida')//Tecla ENTER
      eliminarData_GuiaSalida($modal_delete, ID, Nu_Tipo_Operacion, Nu_Descargar_Inventario);
  });

  $( '#btn-save-delete' ).off('click').click(function () {
    eliminarData_GuiaSalida($modal_delete, ID, Nu_Tipo_Operacion, Nu_Descargar_Inventario);
  });
}

$(function () {
  $('[data-mask]').inputmask();
  $( '.date-picker-invoice' ).val(fDay + '/' + fMonth + '/' + fYear);
      
  url = base_url + 'Logistica/GuiaSalidaController/ajax_list';
  table_guia_salida = $( '#table-GuiaSalida' ).DataTable({
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
    'order':[],
    'ajax': {
      'url'       : url,
      'type'      : 'POST',
      'dataType'  : 'json',
      'data'      : function ( data ) {
        data.Filtro_Fe_Inicio       = ParseDateString($( '#txt-Filtro_Fe_Inicio' ).val(), 1, '/'),
        data.Filtro_Fe_Fin          = ParseDateString($( '#txt-Filtro_Fe_Fin' ).val(), 1, '/'),
        data.Filtro_SerieDocumento  = $( '#txt-Filtro_SerieDocumento' ).val(),
        data.Filtro_NumeroDocumento = $( '#txt-Filtro_NumeroDocumento' ).val(),
        data.Filtro_Estado          = $( '#cbo-Filtro_Estado' ).val(),
        data.Filtro_Entidad         = $( '#txt-Filtro_Entidad' ).val();
      },
    },
    "aoColumns": [
    { "sWidth": "5%" },
    { "sWidth": "3%" },//tipo
    { "sWidth": "2%" },//serie
    { "sWidth": "3%" },//numero
    { "sWidth": "3%" },//t.d.i
    { "sWidth": "10%" },//cliente
    { "sWidth": "1%" },//moneda
    { "sWidth": "2%" },//total
    { "sWidth": "3%" },//estado
    { "sWidth": "2%" },
    { "sWidth": "2%" },
    { "sWidth": "15%" },
    ],
    'columnDefs': [{
      'className'     : 'text-center',
      'targets'       : 'no-sort',
      'orderable'     : false,
    },
    {
      'className'     : 'text-right',
      'targets'       : 'no-sort_right',
      'orderable'     : false,
    },
    {
      'className'     : 'text-center',
      'targets'       : 'sort_center',
      'orderable'     : true,
    },],
  });
  
  $( '#btn-filter' ).click(function(){
    table_guia_salida.ajax.reload();
  });
  
  $( '#form-GuiaSalida' ).validate({
		rules:{
			ID_Serie_Documento: {
				required: true,
			},
			ID_Numero_Documento: {
				required: true,
			},
			Fe_Emision: {
				required: true,
			},
			ID_Almacen: {
				required: true,
			},
		},
		messages:{
			ID_Serie_Documento:{
				required: "Ingresar serie",
				minlength: "Debe ingresar 3 dígitos",
				maxlength: "Debe ingresar 3 dígitos",
			},
			ID_Numero_Documento:{
				required: "Ingresar número",
				minlength: "Debe ingresar 4 dígitos",
				maxlength: "Debe ingresar 4 dígitos",
			},
			Fe_Emision:{
				required: "Ingresar F. Emisión",
			},
			ID_Almacen:{
				required: "Seleccionar almacén",
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
		submitHandler: form_GuiaSalida
	});
	
	$( '#cbo-SeriesDocumentoGuia' ).change(function(){
	  $( '#txt-ID_Numero_Documento_Guia' ).val('');
	  if ( $(this).val() != '') {
		  url = base_url + 'HelperController/getNumeroDocumento';
      $.post( url, { ID_Tipo_Documento: 7, ID_Serie_Documento: $(this).val() }, function( response ){
        if (response.length == 0)
          $( '#txt-ID_Numero_Documento_Guia' ).val('');
        else
          $( '#txt-ID_Numero_Documento_Guia' ).val(response.ID_Numero_Documento);
      }, 'JSON');
    }
	})
	
	$( '#cbo-SeriesDocumento' ).change(function(){
	  $( '#txt-ID_Numero_Documento_Factura' ).val('');
	  if ( $(this).val() != '') {
		  url = base_url + 'HelperController/getNumeroDocumento';
      $.post( url, { ID_Tipo_Documento: 3, ID_Serie_Documento: $(this).val() }, function( response ){
        if (response.length == 0)
          $( '#txt-ID_Numero_Documento_Factura' ).val('');
        else
          $( '#txt-ID_Numero_Documento_Factura' ).val(response.ID_Numero_Documento);
      }, 'JSON');
    }
	})
	
	$( '#cbo-Monedas' ).change(function(){
	  if ( $(this).val() > 0 )
      $( '.span-signo' ).text( $(this).find(':selected').data('no_signo') );
	})
  
	$( '#cbo-Almacenes' ).change(function(){
	  if ( $(this).val() > 0 ) {
      url = base_url + 'HelperController/getListaPrecio';
      $.post( url, {Nu_Tipo_Lista_Precio : $( '[name="Nu_Tipo_Lista_Precio"]' ).val(), ID_Organizacion: 0, ID_Almacen : $( '#cbo-Almacenes' ).val()}, function( response ){
        if (response.length === 1)//única lista de precio por almacén
          $( '#cbo-lista_precios' ).html( '<option value="' + response[0].ID_Lista_Precio_Cabecera + '">' + response[0].No_Lista_Precio + '</option>' );
        else {//multiple lista de precio por almacén
          $( '#cbo-lista_precios' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
          for (var i = 0; i < response.length; i++)
            $( '#cbo-lista_precios' ).append( '<option value="' + response[i].ID_Lista_Precio_Cabecera + '">' + response[i].No_Lista_Precio + '</option>' );
        }
      }, 'JSON');
	  }
	})
	
	$( '.div-Almacen' ).show();
	$( '#cbo-DescargarInventario' ).change(function(){
	  $( '.div-Almacen' ).show();
	  if ( $(this).val() == 0 )//Descarga NO
	    $( '.div-Almacen' ).hide();
	})
	
  $( '#btn-addProductoGuiaSalida' ).click(function(){
    var $ID_Producto                  = $( '#txt-ID_Producto' ).val();
    var $Ss_Precio                    = parseFloat($( '#txt-Ss_Precio' ).val());
    var $No_Producto                  = $( '#txt-No_Producto' ).val();
    var $ID_Impuesto_Cruce_Documento  = $( '#txt-ID_Impuesto_Cruce_Documento' ).val();
    var $Nu_Tipo_Impuesto             = $( '#txt-Nu_Tipo_Impuesto' ).val();
    var $Ss_Impuesto                  = $( '#txt-Ss_Impuesto' ).val();
    var $Qt_Producto                  = parseFloat($( '#txt-Qt_Producto' ).val());
    var $Ss_SubTotal_Producto         = 0.00;
    var $Ss_Total_Producto            = 0.00;
    
    if ( $('input[name=radioTipoDocumento]:checked', '#form-GuiaSalida').val() === undefined){
      $( '#error-msgTipoDocumento' ).html('Seleccionar <strong>Tipo de Documento</strong>');
    } else if ( $('input[name=radioTipoDocumento]:checked', '#form-GuiaSalida').val() == 3 && $( '#cbo-SeriesDocumento' ).val() == 0){
      $( '#cbo-SeriesDocumento' ).closest('.form-group').find('.help-block').html('seleccionar serie');
      $( '#cbo-SeriesDocumento' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $('input[name=radioTipoDocumento]:checked', '#form-GuiaSalida').val() == 3 && $( '#txt-ID_Numero_Documento_Factura' ).val().length === 0){
      $( '#txt-ID_Numero_Documento_Factura' ).closest('.form-group').find('.help-block').html('Ingresar número');
      $( '#txt-ID_Numero_Documento_Factura' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $('input[name=radioTipoDocumento]:checked', '#form-GuiaSalida').val() == 7 && $( '#cbo-SeriesDocumentoGuia' ).val() == 0){
      $( '#cbo-SeriesDocumentoGuia' ).closest('.form-group').find('.help-block').html('Seleccionar serie');
      $( '#cbo-SeriesDocumentoGuia' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $('input[name=radioTipoDocumento]:checked', '#form-GuiaSalida').val() == 7 && $( '#txt-ID_Numero_Documento_Guia' ).val().length === 0){
      $( '#txt-ID_Numero_Documento_Guia' ).closest('.form-group').find('.help-block').html('Ingresar número');
      $( '#txt-ID_Numero_Documento_Guia' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $('input[name=radioTipoDocumento]:checked', '#form-GuiaSalida').val() == 0 && $( '#cbo-SeriesDocumento' ).val() == 0){
      $( '#cbo-SeriesDocumento' ).closest('.form-group').find('.help-block').html('seleccionar serie');
      $( '#cbo-SeriesDocumento' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $('input[name=radioTipoDocumento]:checked', '#form-GuiaSalida').val() == 0 && $( '#txt-ID_Numero_Documento_Factura' ).val().length === 0){
      $( '#txt-ID_Numero_Documento_Factura' ).closest('.form-group').find('.help-block').html('Ingresar número');
      $( '#txt-ID_Numero_Documento_Factura' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $('input[name=radioTipoDocumento]:checked', '#form-GuiaSalida').val() == 0 && $( '#cbo-SeriesDocumentoGuia' ).val() == 0){
      $( '#cbo-SeriesDocumentoGuia' ).closest('.form-group').find('.help-block').html('Seleccionar serie');
      $( '#cbo-SeriesDocumentoGuia' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $('input[name=radioTipoDocumento]:checked', '#form-GuiaSalida').val() == 0 && $( '#txt-ID_Numero_Documento_Guia' ).val().length === 0){
      $( '#txt-ID_Numero_Documento_Guia' ).closest('.form-group').find('.help-block').html('Ingresar número');
      $( '#txt-ID_Numero_Documento_Guia' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $( '#cbo-TiposMovimientoSalida' ).val() == 0){
      $( '#cbo-TiposMovimientoSalida' ).closest('.form-group').find('.help-block').html('Seleccionar movimiento');
      $( '#cbo-TiposMovimientoSalida' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $( '#cbo-DescargarInventario' ).val() == 1 && $( '#cbo-Almacenes' ).val() == 0){
      $( '#cbo-Almacenes' ).closest('.form-group').find('.help-block').html('Seleccionar almacén');
      $( '#cbo-Almacenes' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $( '#txt-AID' ).val().length === 0 || $( '#txt-ANombre' ).val().length === 0 || $( '#txt-ACodigo' ).val().length === 0) {
      $( '#txt-ANombre' ).closest('.form-group').find('.help-block').html('Seleccionar proveedor');
  		$( '#txt-ANombre' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $ID_Producto.length === 0 || $No_Producto.length === 0) {
	    $( '#txt-No_Producto' ).closest('.form-group').find('.help-block').html('Ingresar producto');
			$( '#txt-No_Producto' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      //Validar stock
      $.post( base_url + 'HelperController/getValidarStock', function( arrData ){
        if (arrData.Nu_Validar_Stock === '1' && $Qt_Producto <= 0.000000) {
          var $modal_msg_stock = $( '.modal-message' );
          $modal_msg_stock.modal('show');
          
          $modal_msg_stock.removeClass('modal-danger modal-warning modal-success');
          $modal_msg_stock.addClass('modal-warning');
          
          $( '.modal-title-message' ).text('Sin stock disponible');
  
    	    setTimeout(function() {$modal_msg_stock.modal('hide');}, 1300);
        } else {
          var _ID_Producto = '';
          var option_impuesto_producto = '';
          
          var obj = JSON.parse(arrImpuestosProducto);
          for (var x = 0; x < obj.arrImpuesto.length; x++){
            var selected = '';
            if ($ID_Impuesto_Cruce_Documento == obj.arrImpuesto[x].ID_Impuesto_Cruce_Documento)
              selected = 'selected="selected"';
            option_impuesto_producto += "<option value='" + obj.arrImpuesto[x].ID_Impuesto_Cruce_Documento + "' data-nu_tipo_impuesto='" + obj.arrImpuesto[x].Nu_Tipo_Impuesto + "' data-impuesto_producto='" + obj.arrImpuesto[x].Ss_Impuesto + "' " + selected + ">" + obj.arrImpuesto[x].No_Impuesto + "</option>";
          }
          
          $Ss_Precio = isNaN($Ss_Precio) ? 0 : $Ss_Precio;
    			if ($Nu_Tipo_Impuesto === '1'){//CON IGV
    			  var $Ss_SubTotal_Producto = $Ss_Precio;
    			  var $Ss_Total_Producto = parseFloat($Ss_SubTotal_Producto * $Ss_Impuesto);
    			}else{
            $Ss_SubTotal_Producto = $Ss_Precio;
    			  $Ss_Total_Producto = $Ss_SubTotal_Producto;
    			}
    			
          var table_detalle_producto =
          "<tr id='tr_detalle_producto" + $ID_Producto + "'>"
            + "<td style='display:none;' class='text-left td-iIdItem'>" + $ID_Producto + "</td>"
            + "<td class='text-right'><input type='tel' id=" + $ID_Producto + " class='txt-Qt_Producto form-control input-decimal' data-id_producto='" + $ID_Producto + "' value='1' autocomplete='off'></td>"
            + "<td class='text-left'>" + $No_Producto + "</td>"
            + "<td class='text-right'><input type='text' class='txt-Ss_Precio form-control input-decimal' data-id_producto='" + $ID_Producto + "' value='" + $Ss_Precio + "' autocomplete='off'></td>"
            +"<td class='text-right'>"
              +"<select class='cbo-ImpuestosProducto form-control required' style='width: 100%;'>"
                +option_impuesto_producto
              +"</select>"
            +"</td>"
            + "<td class='text-right'><input type='tel' class='txt-Ss_SubTotal_Producto form-control' value='" + $Ss_SubTotal_Producto + "' autocomplete='off' disabled></td>"
            + "<td class='text-right'><input type='tel' class='txt-Ss_Descuento form-control input-decimal' data-id_producto='" + $ID_Producto + "' value='' autocomplete='off'></td>"
            + "<td class='text-right'><input type='text' class='txt-Ss_Total_Producto form-control input-decimal' data-id_producto='" + $ID_Producto + "' value='" + $Ss_Total_Producto + "' autocomplete='off'></td>"
            + "<td class='text-center'><button type='button' id='btn-deleteProducto' class='btn btn-sm btn-link' alt='Eliminar' title='Eliminar'><i class='fa fa-trash-o fa-2x' aria-hidden='true'> </i></button></td>"
          + "</tr>";
    	    
    	    if( isExistTableTemporalProducto($ID_Producto) ){
      	    $( '#txt-No_Producto' ).closest('.form-group').find('.help-block').html('Ya existe producto <b>' + $No_Producto + '</b>');
      			$( '#txt-No_Producto' ).closest('.form-group').removeClass('has-success').addClass('has-error');
      			$( '#txt-No_Producto' ).focus();
      			
      			$( '#txt-ID_Producto' ).val('');
      			$( '#txt-Nu_Codigo_Barra' ).val('');
      			$( '#txt-No_Producto' ).val('');
    	    } else {
    			  $( '#txt-ID_Producto' ).val('');
    			  $( '#txt-Nu_Codigo_Barra' ).val('');
      			$( '#txt-No_Producto' ).val('');
      			
    	      $( '#table-DetalleGuiasSalidaProductos' ).show();
    			  $( '#table-DetalleGuiasSalidaProductos >tbody' ).append(table_detalle_producto);
    			  
      			$( '#' + $ID_Producto ).focus();
    			  $( '#' + $ID_Producto ).select();
      			
      			var $Ss_Descuento = parseFloat($('#txt-Ss_Descuento').val());
            var $Ss_SubTotal = 0.00;
            var $Ss_Exonerada = 0.00;
            var $Ss_Inafecto = 0.00;
            var $Ss_IGV = 0.00;
            var $Ss_Total = 0.00;
            var iCantDescuento = 0;
            var globalImpuesto = 0;
            var $Ss_Descuento_p = 0;
            $("#table-DetalleGuiasSalidaProductos > tbody > tr").each(function(){
              var rows = $(this);
              var fImpuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
              var iGrupoImpuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
              var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
              var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());
        
              if (iGrupoImpuesto === 1) {
                $Ss_SubTotal += $Ss_SubTotal_Producto;
                $Ss_IGV += ($Ss_SubTotal_Producto * fImpuesto) - $Ss_SubTotal_Producto;
                globalImpuesto = fImpuesto;
              } else if (iGrupoImpuesto === 2) {
                $Ss_Inafecto += $Ss_SubTotal_Producto;
                globalImpuesto += 0;
              } else {
                $Ss_Exonerada += $Ss_SubTotal_Producto;
                globalImpuesto += 0;
              }
                
              if(isNaN($Ss_Descuento_Producto))
                $Ss_Descuento_Producto = 0;
                
              $Ss_Descuento_p += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto))) / 100);
              $Ss_Total += parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
            });
            
            if ($Ss_SubTotal > 0.00 || $Ss_Inafecto > 0.00 || $Ss_Exonerada > 0.00) {
              if ($Ss_Descuento > 0.00) {
                var $Ss_Descuento_Gravadas = 0, $Ss_Descuento_Inafecto = 0, $Ss_Descuento_Exonerada = 0;
                if ($Ss_SubTotal > 0.00) {
                  $Ss_Descuento_Gravadas = (($Ss_Descuento * $Ss_SubTotal) / 100);
                  $Ss_SubTotal = $Ss_SubTotal - $Ss_Descuento_Gravadas;
                  $Ss_SubTotal = Math.round10($Ss_SubTotal, -2);
                  $Ss_IGV = ($Ss_SubTotal * globalImpuesto) - $Ss_SubTotal;
                }
          
                if ($Ss_Inafecto > 0.00) {
                  $Ss_Descuento_Inafecto = (($Ss_Descuento * $Ss_Inafecto) / 100);
                  $Ss_Inafecto = $Ss_Inafecto - $Ss_Descuento_Inafecto;
                  $Ss_Inafecto = Math.round10($Ss_Inafecto, -2);
                }
                
                if ($Ss_Exonerada > 0.00) {
                  $Ss_Descuento_Exonerada = (($Ss_Descuento * $Ss_Exonerada) / 100);
                  $Ss_Exonerada = $Ss_Exonerada - $Ss_Descuento_Exonerada;
                  $Ss_Exonerada = Math.round10($Ss_Exonerada, -2);
                }
                
                $Ss_Total = ($Ss_SubTotal * globalImpuesto) + $Ss_Inafecto + $Ss_Exonerada;
                $Ss_Descuento = $Ss_Descuento_Gravadas + $Ss_Descuento_Inafecto + $Ss_Descuento_Exonerada;
              } else
                $Ss_Descuento = $Ss_Descuento_p;
          
              if(isNaN($Ss_Descuento))
                $Ss_Descuento = 0.00;
              
              $( '#txt-subTotal' ).val( $Ss_SubTotal.toFixed(2) );
              $( '#span-subTotal' ).text( $Ss_SubTotal.toFixed(2) );
              
              $( '#txt-exonerada' ).val( $Ss_Exonerada.toFixed(2) );
              $( '#span-exonerada' ).text( $Ss_Exonerada.toFixed(2) );
              
              $( '#txt-inafecto' ).val( $Ss_Inafecto.toFixed(2) );
              $( '#span-inafecto' ).text( $Ss_Inafecto.toFixed(2) );
                
              $( '#txt-impuesto' ).val( $Ss_IGV.toFixed(2) );
              $( '#span-impuesto' ).text( $Ss_IGV.toFixed(2) );
            	
            	$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
            	$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
          
          		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
          		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
            }
      			
      		  validateDecimal();
      		  validateNumber();
      		  validateNumberOperation();
    	    }
        }
      }, 'JSON')
    }
	})

  $('#table-DetalleGuiasSalidaProductos tbody' ).on('input', '.txt-Ss_Precio', function(){
    var fila = $(this).parents("tr");
    var $ID_Producto = fila.find(".txt-Ss_Precio").data('id_producto');
    var precio = fila.find(".txt-Ss_Precio").val();
    var cantidad = fila.find(".txt-Qt_Producto").val();
    var subtotal_producto = fila.find(".txt-Ss_SubTotal_Producto").val();
    var impuesto_producto = fila.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto');
    var nu_tipo_impuesto = fila.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
    var descuento = fila.find(".txt-Ss_Descuento").val();
    var total_producto = fila.find(".txt-Ss_Total_Producto").val();
    var fDescuento_SubTotal_Producto = 0;
    var fDescuento_Total_Producto = 0;

    if ( parseFloat(precio) > 0.00 && parseFloat(cantidad) > 0){
      $('#tr_detalle_producto' + $ID_Producto).removeClass('danger');
	    $( '#table-DetalleGuiasSalidaProductos tfoot' ).empty();
      if (nu_tipo_impuesto === 1){//CON IGV
        fDescuento_SubTotal_Producto = parseFloat(((precio * cantidad) - (((descuento * (precio * cantidad)) / 100))));
        fDescuento_Total_Producto = parseFloat(((precio * cantidad) - (((descuento * (precio * cantidad)) / 100))) * impuesto_producto);
        fila.find(".txt-Ss_SubTotal_Producto").val( (parseFloat(Math.round10(fDescuento_SubTotal_Producto, -2)).toFixed(2)).toString().split(". ") );
  		  fila.find(".txt-Ss_Total_Producto").val( (parseFloat(Math.round10(fDescuento_Total_Producto, -2)).toFixed(2)).toString().split(". ") );
        
        var $Ss_SubTotal = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_IGV = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleGuiasSalidaProductos > tbody > tr").each(function(){
          var rows = $(this);
          var Ss_Impuesto           = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
          var Nu_Tipo_Impuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
          var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());
          
          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
            
          if (Nu_Tipo_Impuesto === 1){
            $Ss_SubTotal += $Ss_SubTotal_Producto;
            $Ss_IGV += ($Ss_SubTotal_Producto * Ss_Impuesto) - $Ss_SubTotal_Producto;
          }
          
          $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto) )) / 100);
          $Ss_Total += parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
        });
        $( '#txt-subTotal' ).val( $Ss_SubTotal.toFixed(2) );
    		$( '#span-subTotal' ).text( $Ss_SubTotal.toFixed(2) );
    		
    		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
    		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
    		
    		$( '#txt-impuesto' ).val( $Ss_IGV.toFixed(2) );
    		$( '#span-impuesto' ).text( $Ss_IGV.toFixed(2) );
    		
    		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
    		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
  		} else if (nu_tipo_impuesto === 2){//Inafecto
        fila.find(".txt-Ss_SubTotal_Producto").val( (parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". ") );
        fila.find(".txt-Ss_Total_Producto").val( (parseFloat(((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)) * impuesto_producto).toFixed(2)).toString().split(". ") );
        
        var $Ss_Inafecto = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleGuiasSalidaProductos > tbody > tr").each(function(){
          var rows = $(this);
          var Nu_Tipo_Impuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
          var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());

          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
            
          if (Nu_Tipo_Impuesto === 2)
            $Ss_Inafecto += $Ss_SubTotal_Producto;

          $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto))) / 100);
          $Ss_Total += parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
        });
        
        $( '#txt-inafecto' ).val( $Ss_Inafecto.toFixed(2) );
    		$( '#span-inafecto' ).text( $Ss_Inafecto.toFixed(2) );
    		
    		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
    		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
    		
    		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
    		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
  		} else if (nu_tipo_impuesto === 3){//Exonerada
        fila.find(".txt-Ss_SubTotal_Producto").val( (parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". ") );
        fila.find(".txt-Ss_Total_Producto").val( (parseFloat(((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)) * impuesto_producto).toFixed(2)).toString().split(". ") );
        
        var $Ss_Exonerada = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleGuiasSalidaProductos > tbody > tr").each(function(){
          var rows = $(this);
          var Nu_Tipo_Impuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
          var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());

          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
            
          if (Nu_Tipo_Impuesto === 3)
            $Ss_Exonerada += $Ss_SubTotal_Producto;

          $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto))) / 100);
          $Ss_Total += parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
        });
        
        $( '#txt-exonerada' ).val( $Ss_Exonerada.toFixed(2) );
    		$( '#span-exonerada' ).text( $Ss_Exonerada.toFixed(2) );
    		
    		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
    		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
    		
    		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
    		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
  		}
    }
  })
	
  $('#table-DetalleGuiasSalidaProductos tbody' ).on('input', '.txt-Qt_Producto', function(){
    var fila = $(this).parents("tr");
    var $ID_Producto = fila.find(".txt-Ss_Precio").data('id_producto');
    var precio = fila.find(".txt-Ss_Precio").val();
    var cantidad = fila.find(".txt-Qt_Producto").val();
    var subtotal_producto = fila.find(".txt-Ss_SubTotal_Producto").val();
    var impuesto_producto = fila.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto');
    var nu_tipo_impuesto = fila.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
    var descuento = fila.find(".txt-Ss_Descuento").val();
    var total_producto = fila.find(".txt-Ss_Total_Producto").val();
    var fDescuento_SubTotal_Producto = 0;
    var fDescuento_Total_Producto = 0;

    if ( parseFloat(precio) > 0.00 && parseFloat(cantidad) > 0){
      $('#tr_detalle_producto' + $ID_Producto).removeClass('danger');
      $( '#table-DetalleGuiasSalidaProductos tfoot' ).empty();
  		if (nu_tipo_impuesto === 1){//CON IGV
    		fDescuento_SubTotal_Producto = parseFloat(((precio * cantidad) - (((descuento * (precio * cantidad)) / 100))));
        fDescuento_Total_Producto = parseFloat(((precio * cantidad) - (((descuento * (precio * cantidad)) / 100))) * impuesto_producto);
        fila.find(".txt-Ss_SubTotal_Producto").val( (parseFloat(Math.round10(fDescuento_SubTotal_Producto, -2)).toFixed(2)).toString().split(". ") );
  		  fila.find(".txt-Ss_Total_Producto").val( (parseFloat(Math.round10(fDescuento_Total_Producto, -2)).toFixed(2)).toString().split(". ") );
        
        var $Ss_SubTotal = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_IGV = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleGuiasSalidaProductos > tbody > tr").each(function(){
          var rows = $(this);
          var Ss_Impuesto           = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
          var Nu_Tipo_Impuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
          var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());

          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
            
          if (Nu_Tipo_Impuesto === 1){
            $Ss_SubTotal += $Ss_SubTotal_Producto;
            $Ss_IGV += ($Ss_SubTotal_Producto * Ss_Impuesto) - $Ss_SubTotal_Producto;
          }

          $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto))) / 100);
          $Ss_Total += parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
        });
        $( '#txt-subTotal' ).val( $Ss_SubTotal.toFixed(2) );
    		$( '#span-subTotal' ).text( $Ss_SubTotal.toFixed(2) );
    		
    		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
    		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
    		
    		$( '#txt-impuesto' ).val( $Ss_IGV.toFixed(2) );
    		$( '#span-impuesto' ).text( $Ss_IGV.toFixed(2) );
    		
    		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
    		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
  		} else if (nu_tipo_impuesto === 2){//Inafecto
    		fila.find(".txt-Ss_SubTotal_Producto").val( (parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". ") );
        fila.find(".txt-Ss_Total_Producto").val( (parseFloat(((precio * cantidad)  - ((descuento * (precio * cantidad)) / 100)) * impuesto_producto).toFixed(2)).toString().split(". ") );

        var $Ss_Inafecto = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleGuiasSalidaProductos > tbody > tr").each(function(){
          var rows = $(this);
          var Nu_Tipo_Impuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
          var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());

          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
            
          if (Nu_Tipo_Impuesto === 2)
            $Ss_Inafecto += $Ss_SubTotal_Producto;

          $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto))) / 100);
          $Ss_Total += parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
        });
        
        $( '#txt-inafecto' ).val( $Ss_Inafecto.toFixed(2) );
    		$( '#span-inafecto' ).text( $Ss_Inafecto.toFixed(2) );
    		
    		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
    		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
    		
    		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
    		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
  		} else if (nu_tipo_impuesto === 3){//Exonerada
    		fila.find(".txt-Ss_SubTotal_Producto").val( (parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". ") );
        fila.find(".txt-Ss_Total_Producto").val( (parseFloat(((precio * cantidad)  - ((descuento * (precio * cantidad)) / 100)) * impuesto_producto).toFixed(2)).toString().split(". ") );

        var $Ss_Exonerada = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_Total = 0.00;
        
        $("#table-DetalleGuiasSalidaProductos > tbody > tr").each(function(){
          var rows = $(this);
          var Nu_Tipo_Impuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
          var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());

          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
            
          if (Nu_Tipo_Impuesto === 3)
            $Ss_Exonerada += $Ss_SubTotal_Producto;

          $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto))) / 100);
          $Ss_Total += parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
        });
        
        $( '#txt-exonerada' ).val( $Ss_Exonerada.toFixed(2) );
    		$( '#span-exonerada' ).text( $Ss_Exonerada.toFixed(2) );
    		
    		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
    		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
    		
    		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
    		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
  		}
    }
  })
  
  $('#table-DetalleGuiasSalidaProductos tbody' ).on('change', '.cbo-ImpuestosProducto', function(){
    var fila = $(this).parents("tr");
    var precio = fila.find(".txt-Ss_Precio").val();
    var cantidad = fila.find(".txt-Qt_Producto").val();
    var subtotal_producto = fila.find(".txt-Ss_SubTotal_Producto").val();
    var impuesto_producto = fila.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto');
    var nu_tipo_impuesto = fila.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
    var total_producto = fila.find(".txt-Ss_Total_Producto").val();
    
    if ( parseFloat(precio) > 0.00 && parseFloat(cantidad) > 0 && parseFloat(total_producto) > 0){
      if (nu_tipo_impuesto === 1) {
  		  fila.find(".txt-Ss_Total_Producto").val( (parseFloat(subtotal_producto * impuesto_producto).toFixed(2)).toString().split(". ") );

        var $Ss_SubTotal = 0.00;
        var $Ss_Exonerada = 0.00;
        var $Ss_Inafecto = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_IGV = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleGuiasSalidaProductos > tbody > tr").each(function(){
          var rows = $(this);
          var Ss_Impuesto           = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
          var Nu_Tipo_Impuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
          var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());

          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
          
          if (Nu_Tipo_Impuesto === 1){
            $Ss_SubTotal += $Ss_SubTotal_Producto;
            $Ss_IGV += (($Ss_SubTotal_Producto * Ss_Impuesto) - $Ss_SubTotal_Producto);
          } else if (Nu_Tipo_Impuesto === 2) {
            $Ss_Inafecto += $Ss_SubTotal_Producto;
          } else if (Nu_Tipo_Impuesto === 3) {
            $Ss_Exonerada += $Ss_SubTotal_Producto;
          }

          $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto))) / 100);
          $Ss_Total += parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
        });
        
        $( '#txt-subTotal' ).val( $Ss_SubTotal.toFixed(2) );
    		$( '#span-subTotal' ).text( $Ss_SubTotal.toFixed(2) );
    		
        $( '#txt-exonerada' ).val( $Ss_Exonerada.toFixed(2) );
    		$( '#span-exonerada' ).text( $Ss_Exonerada.toFixed(2) );
    		
        $( '#txt-inafecto' ).val( $Ss_Inafecto.toFixed(2) );
    		$( '#span-inafecto' ).text( $Ss_Inafecto.toFixed(2) );
    		
    		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
    		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
    		
    		$( '#txt-impuesto' ).val( $Ss_IGV.toFixed(2) );
    		$( '#span-impuesto' ).text( $Ss_IGV.toFixed(2) );
    		
    		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
    		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
  		} else if (nu_tipo_impuesto === 2){//Inafecto
  		  fila.find(".txt-Ss_Total_Producto").val( (parseFloat(subtotal_producto * impuesto_producto).toFixed(2)).toString().split(". ") );
  		  
        var $Ss_SubTotal = 0.00;
        var $Ss_Exonerada = 0.00;
        var $Ss_Inafecto = 0.00;
        var $Ss_IGV = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleGuiasSalidaProductos > tbody > tr").each(function(){
          var rows = $(this);
          var Ss_Impuesto           = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
          var Nu_Tipo_Impuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
          var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());

          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
          
          if (Nu_Tipo_Impuesto === 1){
            $Ss_SubTotal += $Ss_SubTotal_Producto;
            $Ss_IGV += (($Ss_SubTotal_Producto * Ss_Impuesto) - $Ss_SubTotal_Producto);
          } else if (Nu_Tipo_Impuesto === 2) {
            $Ss_Inafecto += $Ss_SubTotal_Producto;
          } else if (Nu_Tipo_Impuesto === 3) {
            $Ss_Exonerada += $Ss_SubTotal_Producto;
          }

          $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto))) / 100);
          $Ss_Total += parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
        });
        
    		$( '#txt-subTotal' ).val( $Ss_SubTotal.toFixed(2) );
    		$( '#span-subTotal' ).text( $Ss_SubTotal.toFixed(2) );
    		
        $( '#txt-exonerada' ).val( $Ss_Exonerada.toFixed(2) );
    		$( '#span-exonerada' ).text( $Ss_Exonerada.toFixed(2) );
    		
        $( '#txt-inafecto' ).val( $Ss_Inafecto.toFixed(2) );
    		$( '#span-inafecto' ).text( $Ss_Inafecto.toFixed(2) );
    		
    		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
    		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
    		
    		$( '#txt-impuesto' ).val( $Ss_IGV.toFixed(2) );
    		$( '#span-impuesto' ).text( $Ss_IGV.toFixed(2) );
    		
    		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
    		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
  		} else if (nu_tipo_impuesto === 3){//Exonerada
  		  fila.find(".txt-Ss_Total_Producto").val( (parseFloat(subtotal_producto * impuesto_producto).toFixed(2)).toString().split(". ") );
  		  
        var $Ss_SubTotal = 0.00;
        var $Ss_Exonerada = 0.00;
        var $Ss_Inafecto = 0.00;
        var $Ss_IGV = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleGuiasSalidaProductos > tbody > tr").each(function(){
          var rows = $(this);
          var Ss_Impuesto           = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
          var Nu_Tipo_Impuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
          var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());
  
          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
          
          if (Nu_Tipo_Impuesto === 1){
            $Ss_SubTotal += $Ss_SubTotal_Producto;
            $Ss_IGV += (($Ss_SubTotal_Producto * Ss_Impuesto) - $Ss_SubTotal_Producto);
          } else if (Nu_Tipo_Impuesto === 2) {
            $Ss_Inafecto += $Ss_SubTotal_Producto;
          } else if (Nu_Tipo_Impuesto === 3) {
            $Ss_Exonerada += $Ss_SubTotal_Producto;
          }

          $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto))) / 100);
          $Ss_Total += parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
        });
        
    		$( '#txt-subTotal' ).val( $Ss_SubTotal.toFixed(2) );
    		$( '#span-subTotal' ).text( $Ss_SubTotal.toFixed(2) );
    		
        $( '#txt-exonerada' ).val( $Ss_Exonerada.toFixed(2) );
    		$( '#span-exonerada' ).text( $Ss_Exonerada.toFixed(2) );
    		
        $( '#txt-inafecto' ).val( $Ss_Inafecto.toFixed(2) );
    		$( '#span-inafecto' ).text( $Ss_Inafecto.toFixed(2) );
    		
    		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
    		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
    		
    		$( '#txt-impuesto' ).val( $Ss_IGV.toFixed(2) );
    		$( '#span-impuesto' ).text( $Ss_IGV.toFixed(2) );
    		
    		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
    		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
  		}
    }
  })
  
  $('#table-DetalleGuiasSalidaProductos tbody' ).on('input', '.txt-Ss_Descuento', function(){
    var fila = $(this).parents("tr");
    var $ID_Producto = fila.find(".txt-Ss_Precio").data('id_producto');
    var precio = fila.find(".txt-Ss_Precio").val();
    var cantidad = fila.find(".txt-Qt_Producto").val();
    var subtotal_producto = fila.find(".txt-Ss_SubTotal_Producto").val();
    var impuesto_producto = fila.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto');
    var nu_tipo_impuesto = fila.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
    var descuento = fila.find(".txt-Ss_Descuento").val();
    var total_producto = fila.find(".txt-Ss_Total_Producto").val();
    var fDescuento_SubTotal_Producto = 0;
    var fDescuento_Total_Producto = 0;

    //Solo si es Guia y Factura
    if ( parseFloat(precio) > 0.00 && parseFloat(cantidad) > 0 && parseFloat(descuento) >= 0 && parseFloat(total_producto) > 0 && (parseFloat($( '#txt-Ss_Descuento' ).val()) == 0 || $( '#txt-Ss_Descuento' ).val() == '')){
      if ( parseFloat(subtotal_producto) >= parseFloat(descuento) ){
        if (nu_tipo_impuesto === 1){//CON IGV
          fDescuento_SubTotal_Producto = parseFloat(((precio * cantidad) - (((descuento * (precio * cantidad)) / 100))));
          fDescuento_Total_Producto = parseFloat(((precio * cantidad) - (((descuento * (precio * cantidad)) / 100))) * impuesto_producto);
          fila.find(".txt-Ss_SubTotal_Producto").val( (parseFloat(Math.round10(fDescuento_SubTotal_Producto, -2)).toFixed(2)).toString().split(". ") );
    		  fila.find(".txt-Ss_Total_Producto").val( (parseFloat(Math.round10(fDescuento_Total_Producto, -2)).toFixed(2)).toString().split(". ") );
        
          var $Ss_SubTotal = 0.00;
          var $Ss_Descuento = 0.00;
          var $Ss_IGV = 0.00;
          var $Ss_Total = 0.00;
          var $fDescuento_Producto = 0;
          $("#table-DetalleGuiasSalidaProductos > tbody > tr").each(function(){
            var rows = $(this);
            var Ss_Impuesto           = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
            var Nu_Tipo_Impuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
            var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
            var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());
  
            if(isNaN($Ss_Descuento_Producto))
              $Ss_Descuento_Producto = 0;
          
            if (Nu_Tipo_Impuesto === 1){
              $Ss_SubTotal += $Ss_SubTotal_Producto;
              $Ss_IGV += ($Ss_SubTotal_Producto * Ss_Impuesto) - $Ss_SubTotal_Producto;
            }

            $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto))) / 100);
            $Ss_Total += parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
          });
          
          $( '#txt-subTotal' ).val( $Ss_SubTotal.toFixed(2) );
      		$( '#span-subTotal' ).text( $Ss_SubTotal.toFixed(2) );
      		
      		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
      		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
      		
      		$( '#txt-impuesto' ).val( $Ss_IGV.toFixed(2) );
      		$( '#span-impuesto' ).text( $Ss_IGV.toFixed(2) );
      		
      		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
      		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
  		  } else if (nu_tipo_impuesto === 2){//Inafecto
          fila.find(".txt-Ss_SubTotal_Producto").val( (parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". ") );
          fila.find(".txt-Ss_Total_Producto").val( (parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". ") );

          var $Ss_Inafecto = 0.00;
          var $Ss_Descuento = 0.00;
          var $Ss_Total = 0.00;
          
          $("#table-DetalleGuiasSalidaProductos > tbody > tr").each(function(){
            var rows = $(this);
            var Nu_Tipo_Impuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
            var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
            var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());
  
            if(isNaN($Ss_Descuento_Producto))
              $Ss_Descuento_Producto = 0;
              
            if (Nu_Tipo_Impuesto === 2)
              $Ss_Inafecto += $Ss_SubTotal_Producto;

            $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto))) / 100);
            $Ss_Total += parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
          });
          
          $( '#txt-inafecto' ).val( $Ss_Inafecto.toFixed(2) );
      		$( '#span-inafecto' ).text( $Ss_Inafecto.toFixed(2) );
      		
      		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
      		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
      		
      		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
      		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
    		} else if (nu_tipo_impuesto === 3){//Exonerada
          fila.find(".txt-Ss_SubTotal_Producto").val( (parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". ") );
          fila.find(".txt-Ss_Total_Producto").val( (parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". ") );

          var $Ss_Exonerada = 0.00;
          var $Ss_Descuento = 0.00;
          var $Ss_Total = 0.00;
          
          $("#table-DetalleGuiasSalidaProductos > tbody > tr").each(function(){
            var rows = $(this);
            var Nu_Tipo_Impuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
            var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
            var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());
  
            if(isNaN($Ss_Descuento_Producto))
              $Ss_Descuento_Producto = 0;
              
            if (Nu_Tipo_Impuesto === 3)
              $Ss_Exonerada += $Ss_SubTotal_Producto;

            $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto))) / 100);
            $Ss_Total += parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
          });
          
          $( '#txt-exonerada' ).val( $Ss_Exonerada.toFixed(2) );
      		$( '#span-exonerada' ).text( $Ss_Exonerada.toFixed(2) );
      		
      		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
      		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
      		
      		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
      		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
    		}
      }
    }
  })

  $('#table-DetalleGuiasSalidaProductos tbody' ).on('input', '.txt-Ss_Total_Producto', function(){
    var fila = $(this).parents("tr");
    var $ID_Producto = fila.find(".txt-Ss_Precio").data('id_producto');
    var precio = fila.find(".txt-Ss_Precio").val();
    var cantidad = fila.find(".txt-Qt_Producto").val();
    var subtotal_producto = fila.find(".txt-Ss_SubTotal_Producto").val();
    var impuesto_producto = fila.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto');
    var nu_tipo_impuesto = fila.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
    var descuento = fila.find(".txt-Ss_Descuento").val();
    var total_producto = fila.find(".txt-Ss_Total_Producto").val();

    //Solo si es Guia y Factura
    if ( parseFloat(precio) > 0.00 && parseFloat(cantidad) > 0 && parseFloat(total_producto) > 0){
      $('#tr_detalle_producto' + $ID_Producto).removeClass('danger');
      $( '#table-DetalleGuiasSalidaProductos tfoot' ).empty();
      if (nu_tipo_impuesto === 1){//CON IGV
        fila.find(".txt-Ss_Precio").val( (parseFloat((total_producto / cantidad) / impuesto_producto).toFixed(3)).toString().split(". ") );
        fila.find(".txt-Ss_SubTotal_Producto").val( (parseFloat(total_producto / impuesto_producto).toFixed(3)).toString().split(". ") );
      
        var $Ss_SubTotal = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_IGV = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleGuiasSalidaProductos > tbody > tr").each(function(){
          var rows = $(this);
          var Ss_Impuesto           = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
          var Nu_Tipo_Impuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
          var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());

          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
          
          if (Nu_Tipo_Impuesto === 1){
            $Ss_SubTotal += $Ss_SubTotal_Producto;
            $Ss_IGV += (($Ss_SubTotal_Producto * Ss_Impuesto) - $Ss_SubTotal_Producto);
          }

          $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto))) / 100);
          $Ss_Total += parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
        });
        
        $( '#txt-subTotal' ).val( $Ss_SubTotal.toFixed(2) );
    		$( '#span-subTotal' ).text( $Ss_SubTotal.toFixed(2) );
    		
    		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
    		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
    		
    		$( '#txt-impuesto' ).val( $Ss_IGV.toFixed(2) );
    		$( '#span-impuesto' ).text( $Ss_IGV.toFixed(2) );
    		
    		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
    		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
  		} else if (nu_tipo_impuesto === 2){//Inafecto
        fila.find(".txt-Ss_Precio").val( (parseFloat((total_producto / cantidad) / impuesto_producto).toFixed(3)).toString().split(". ") );
        fila.find(".txt-Ss_SubTotal_Producto").val( (parseFloat(total_producto / impuesto_producto).toFixed(3)).toString().split(". ") );
      
        var $Ss_Inafecto = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleGuiasSalidaProductos > tbody > tr").each(function(){
          var rows = $(this);
          var Nu_Tipo_Impuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
          var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());

          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
          
          if (Nu_Tipo_Impuesto === 2)
            $Ss_Inafecto += $Ss_SubTotal_Producto;

          $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto))) / 100);
          $Ss_Total += parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
        });
        
        $( '#txt-inafecto' ).val( $Ss_Inafecto.toFixed(2) );
    		$( '#span-inafecto' ).text( $Ss_Inafecto.toFixed(2) );
    		
    		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
    		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
    		
    		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
    		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
  		} else if (nu_tipo_impuesto === 3){//Exonerada
        fila.find(".txt-Ss_Precio").val( (parseFloat((total_producto / cantidad) / impuesto_producto).toFixed(3)).toString().split(". ") );
        fila.find(".txt-Ss_SubTotal_Producto").val( (parseFloat(total_producto / impuesto_producto).toFixed(3)).toString().split(". ") );
      
        var $Ss_Exonerada = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleGuiasSalidaProductos > tbody > tr").each(function(){
          var rows = $(this);
          var Nu_Tipo_Impuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
          var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());

          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
          
          if (Nu_Tipo_Impuesto === 3)
            $Ss_Exonerada += $Ss_SubTotal_Producto;

          $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto))) / 100);
          $Ss_Total += parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
        });
        
        $( '#txt-exonerada' ).val( $Ss_Exonerada.toFixed(2) );
    		$( '#span-exonerada' ).text( $Ss_Exonerada.toFixed(2) );
    		
    		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
    		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
    		
    		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
    		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
  		}
    }
  })
  
	$( '#table-DetalleGuiasSalidaProductos tbody' ).on('click', '#btn-deleteProducto', function(){
    $(this).closest('tr').remove ();
    
    var $Ss_Descuento = parseFloat($('#txt-Ss_Descuento').val());
    var $Ss_SubTotal = 0.00;
    var $Ss_Exonerada = 0.00;
    var $Ss_Inafecto = 0.00;
    var $Ss_IGV = 0.00;
    var $Ss_Total = 0.00;
    var iCantDescuento = 0;
    var globalImpuesto = 0;
    var $Ss_Descuento_p = 0;
    $("#table-DetalleGuiasSalidaProductos > tbody > tr").each(function(){
      var rows = $(this);
      var fImpuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
      var iGrupoImpuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
      var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
      var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());

      if (iGrupoImpuesto === 1) {
        $Ss_SubTotal += $Ss_SubTotal_Producto;
        $Ss_IGV += ($Ss_SubTotal_Producto * fImpuesto) - $Ss_SubTotal_Producto;
        globalImpuesto = fImpuesto;
      } else if (iGrupoImpuesto === 2) {
        $Ss_Inafecto += $Ss_SubTotal_Producto;
        globalImpuesto += 0;
      } else {
        $Ss_Exonerada += $Ss_SubTotal_Producto;
        globalImpuesto += 0;
      }
        
      if(isNaN($Ss_Descuento_Producto))
        $Ss_Descuento_Producto = 0;

      $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto))) / 100);
      $Ss_Total += parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
    });
    
    if ($Ss_Descuento > 0.00) {
      var $Ss_Descuento_Gravadas = 0, $Ss_Descuento_Inafecto = 0, $Ss_Descuento_Exonerada = 0;
      if ($Ss_SubTotal > 0.00) {
        $Ss_Descuento_Gravadas = (($Ss_Descuento * $Ss_SubTotal) / 100);
        $Ss_SubTotal = $Ss_SubTotal - $Ss_Descuento_Gravadas;
        $Ss_SubTotal = Math.round10($Ss_SubTotal, -2);
        $Ss_IGV = ($Ss_SubTotal * globalImpuesto) - $Ss_SubTotal;
      }

      if ($Ss_Inafecto > 0.00) {
        $Ss_Descuento_Inafecto = (($Ss_Descuento * $Ss_Inafecto) / 100);
        $Ss_Inafecto = $Ss_Inafecto - $Ss_Descuento_Inafecto;
        $Ss_Inafecto = Math.round10($Ss_Inafecto, -2);
      }
      
      if ($Ss_Exonerada > 0.00) {
        $Ss_Descuento_Exonerada = (($Ss_Descuento * $Ss_Exonerada) / 100);
        $Ss_Exonerada = $Ss_Exonerada - $Ss_Descuento_Exonerada;
        $Ss_Exonerada = Math.round10($Ss_Exonerada, -2);
      }
      
      $Ss_Total = ($Ss_SubTotal * globalImpuesto) + $Ss_Inafecto + $Ss_Exonerada;
      $Ss_Descuento = $Ss_Descuento_Gravadas + $Ss_Descuento_Inafecto + $Ss_Descuento_Exonerada;
    } else
      $Ss_Descuento = $Ss_Descuento_p;

    if(isNaN($Ss_Descuento))
      $Ss_Descuento = 0.00;
    
    $( '#txt-subTotal' ).val( $Ss_SubTotal.toFixed(2) );
    $( '#span-subTotal' ).text( $Ss_SubTotal.toFixed(2) );
    
    $( '#txt-exonerada' ).val( $Ss_Exonerada.toFixed(2) );
    $( '#span-exonerada' ).text( $Ss_Exonerada.toFixed(2) );
    
    $( '#txt-inafecto' ).val( $Ss_Inafecto.toFixed(2) );
    $( '#span-inafecto' ).text( $Ss_Inafecto.toFixed(2) );
      
    $( '#txt-impuesto' ).val( $Ss_IGV.toFixed(2) );
    $( '#span-impuesto' ).text( $Ss_IGV.toFixed(2) );
  	
  	$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
  	$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );

		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
		
    if ($( '#table-DetalleGuiasSalidaProductos >tbody >tr' ).length == 0)
      $( '#table-DetalleGuiasSalidaProductos' ).hide();
	})
	
	//Calcular porcentaje - Pendiente con fio, cuando tengo varios items y algunos son inafectos ó igv.
  $('#table-GuiaSalidaTotal' ).on('input', '#txt-Ss_Descuento', function(){
    if ( $('input[name=radioTipoDocumento]:checked', '#form-GuiaSalida').val() == 0 ) {
      var $Ss_Descuento_Producto = 0.00;
      $("#table-DetalleGuiasSalidaProductos > tbody > tr").each(function(){
        var rows = $(this);
        var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());
        
        if(isNaN($Ss_Descuento_Producto))
          $Ss_Descuento_Producto = 0;
        
        $Ss_Descuento_Producto += $Ss_Descuento_Producto;
      })
      
      if ($Ss_Descuento_Producto == 0) {
    		var $Ss_Descuento = parseFloat($(this).val());
        var $Ss_SubTotal = 0.00;
        var $Ss_Exonerada = 0.00;
        var $Ss_Inafecto = 0.00;
        var $Ss_IGV = 0.00;
        var $Ss_Total = 0.00;
        var globalImpuesto = 0;
        $("#table-DetalleGuiasSalidaProductos > tbody > tr").each(function(){
          var rows = $(this);
          var fImpuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
          var iGrupoImpuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
    
          if (iGrupoImpuesto === 1) {
            $Ss_SubTotal += $Ss_SubTotal_Producto;
            $Ss_IGV += ($Ss_SubTotal_Producto * fImpuesto) - $Ss_SubTotal_Producto;
            globalImpuesto = fImpuesto;
          } else if (iGrupoImpuesto === 2) {
            $Ss_Inafecto += $Ss_SubTotal_Producto;
            globalImpuesto += 0;
          } else {
            $Ss_Exonerada += $Ss_SubTotal_Producto;
            globalImpuesto += 0;
          }

          $Ss_Total += parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
        });
        
        if ($Ss_Descuento > 0.00) {
          var $Ss_Descuento_Gravadas = 0, $Ss_Descuento_Inafecto = 0, $Ss_Descuento_Exonerada = 0;
          if ($Ss_SubTotal > 0.00) {
            $Ss_Descuento_Gravadas = (($Ss_Descuento * $Ss_SubTotal) / 100);
            $Ss_SubTotal = $Ss_SubTotal - $Ss_Descuento_Gravadas;
            $Ss_SubTotal = Math.round10($Ss_SubTotal, -2);
            $Ss_IGV = ($Ss_SubTotal * globalImpuesto) - $Ss_SubTotal;
          }
  
          if ($Ss_Inafecto > 0.00) {
            $Ss_Descuento_Inafecto = (($Ss_Descuento * $Ss_Inafecto) / 100);
            $Ss_Inafecto = $Ss_Inafecto - $Ss_Descuento_Inafecto;
            $Ss_Inafecto = Math.round10($Ss_Inafecto, -2);
          }
          
          if ($Ss_Exonerada > 0.00) {
            $Ss_Descuento_Exonerada = (($Ss_Descuento * $Ss_Exonerada) / 100);
            $Ss_Exonerada = $Ss_Exonerada - $Ss_Descuento_Exonerada;
            $Ss_Exonerada = Math.round10($Ss_Exonerada, -2);
          }
          
          $Ss_Total = ($Ss_SubTotal * globalImpuesto) + $Ss_Inafecto + $Ss_Exonerada;
          $Ss_Descuento = $Ss_Descuento_Gravadas + $Ss_Descuento_Inafecto + $Ss_Descuento_Exonerada;
        }
        
        $( '#txt-subTotal' ).val( $Ss_SubTotal.toFixed(2) );
        $( '#span-subTotal' ).text( $Ss_SubTotal.toFixed(2) );
        
        $( '#txt-exonerada' ).val( $Ss_Exonerada.toFixed(2) );
        $( '#span-exonerada' ).text( $Ss_Exonerada.toFixed(2) );
        
        $( '#txt-inafecto' ).val( $Ss_Inafecto.toFixed(2) );
        $( '#span-inafecto' ).text( $Ss_Inafecto.toFixed(2) );
          
        $( '#txt-impuesto' ).val( $Ss_IGV.toFixed(2) );
        $( '#span-impuesto' ).text( $Ss_IGV.toFixed(2) );
      	
      	$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
      	$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
    
    		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
    		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
      }
    }
  })
})

function isExistTableTemporalProducto($ID_Producto){
  return Array.from($('tr[id*=tr_detalle_producto]'))
    .some(element => ($('td:nth(0)',$(element)).html()===$ID_Producto));
}

function form_GuiaSalida(){
  if (accion_guia_salida === 'add_guia_salida' || accion_guia_salida === 'upd_guia_salida') {//Accion para validar tecla ENTER
    var arrDetalleGuiaSalida = [];
    var arrValidarNumerosEnCero = [];
    var $counterNumerosEnCero = 0;
    var tr_foot = '';
    
    $("#table-DetalleGuiasSalidaProductos > tbody > tr").each(function(){
      var rows = $(this);

      var $ID_Producto = rows.find(".td-iIdItem").text();
      var $Qt_Producto = $('.txt-Qt_Producto', this).val();
      var $fValorUnitario = $('.txt-fValorUnitario', this).val();
      var $Ss_Precio = $('.txt-Ss_Precio', this).val();
      var $ID_Impuesto_Cruce_Documento = $('.cbo-ImpuestosProducto option:selected', this).val();
      var $Ss_SubTotal = $('.txt-Ss_SubTotal_Producto', this).val();
      var $Ss_Descuento = $('.txt-Ss_Descuento', this).val();
      var $Ss_Total = $('.txt-Ss_Total_Producto', this).val();
      
      if (parseFloat($Ss_Precio) == 0 || parseFloat($Qt_Producto) == 0 || parseFloat($Ss_Total) == 0){
        arrValidarNumerosEnCero[$counterNumerosEnCero] = $ID_Producto;
        $('#tr_detalle_producto' + $ID_Producto).addClass('danger');
      }
      
      var obj = {};
      
      obj.ID_Producto = $ID_Producto;
      obj.fValorUnitario = $fValorUnitario;
      obj.Ss_Precio = $Ss_Precio;
      obj.Qt_Producto = $Qt_Producto;
      obj.ID_Impuesto_Cruce_Documento	= $ID_Impuesto_Cruce_Documento;
      obj.Ss_SubTotal = $Ss_SubTotal;
      obj.Ss_Descuento = $Ss_Descuento;
      arrDetalleGuiaSalida.push(obj);
      $counterNumerosEnCero++;
    });
   
    if ( $('input[name=radioTipoDocumento]:checked', '#form-GuiaSalida').val() === undefined){
      $( '#error-msgTipoDocumento' ).html('Seleccionar <strong>Tipo de Documento</strong>');
    } else if ( $('input[name=radioTipoDocumento]:checked', '#form-GuiaSalida').val() == 7 && $( '#cbo-SeriesDocumentoGuia' ).val() == 0){
      $( '#cbo-SeriesDocumentoGuia' ).closest('.form-group').find('.help-block').html('Seleccionar serie');
      $( '#cbo-SeriesDocumentoGuia' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $('input[name=radioTipoDocumento]:checked', '#form-GuiaSalida').val() == 7 && $( '#txt-ID_Numero_Documento_Guia' ).val().length === 0){
      $( '#txt-ID_Numero_Documento_Guia' ).closest('.form-group').find('.help-block').html('Ingresar número');
      $( '#txt-ID_Numero_Documento_Guia' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $('input[name=radioTipoDocumento]:checked', '#form-GuiaSalida').val() == 0 && $( '#cbo-SeriesDocumento' ).val() == 0){
      $( '#cbo-SeriesDocumento' ).closest('.form-group').find('.help-block').html('Seleccionar serie');
      $( '#cbo-SeriesDocumento' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $('input[name=radioTipoDocumento]:checked', '#form-GuiaSalida').val() == 0 && $( '#txt-ID_Numero_Documento_Factura' ).val().length === 0){
      $( '#txt-ID_Numero_Documento_Factura' ).closest('.form-group').find('.help-block').html('Ingresar número');
      $( '#txt-ID_Numero_Documento_Factura' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $('input[name=radioTipoDocumento]:checked', '#form-GuiaSalida').val() == 0 && $( '#cbo-SeriesDocumentoGuia' ).val() == 0){
      $( '#cbo-SeriesDocumentoGuia' ).closest('.form-group').find('.help-block').html('Seleccionar serie');
      $( '#cbo-SeriesDocumentoGuia' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $('input[name=radioTipoDocumento]:checked', '#form-GuiaSalida').val() == 0 && $( '#txt-ID_Numero_Documento_Guia' ).val().length === 0){
      $( '#txt-ID_Numero_Documento_Guia' ).closest('.form-group').find('.help-block').html('Ingresar número');
      $( '#txt-ID_Numero_Documento_Guia' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $( '#cbo-TiposMovimientoSalida' ).val() == 0){
      $( '#cbo-TiposMovimientoSalida' ).closest('.form-group').find('.help-block').html('Seleccionar movimiento');
  	  $( '#cbo-TiposMovimientoSalida' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $( '#cbo-DescargarInventario' ).val() == 1 && $( '#cbo-Almacenes' ).val() == 0){
      $( '#cbo-Almacenes' ).closest('.form-group').find('.help-block').html('Seleccionar almacén');
      $( '#cbo-Almacenes' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $( '#txt-AID' ).val().length === 0 || $( '#txt-ANombre' ).val().length === 0 || $( '#txt-ACodigo' ).val().length === 0) {
      $( '#txt-ANombre' ).closest('.form-group').find('.help-block').html('Seleccionar proveedor');
  		$( '#txt-ANombre' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ($('[name="addCliente"]:checked').attr('value') == 1 && $('#cbo-TiposDocumento').val() == 7 && $('#cbo-SeriesDocumento').val().substring(0, 1) == 'T' && ($('#txt-No_Entidad_Cliente').val().length === 0 || $('#txt-Nu_Documento_Identidad_Cliente').val().length === 0)) {//1 = Nuevo
      $('#modal-message').modal('show');
      $('.modal-message').addClass('modal-danger');
      $('.modal-title-message').text('Cliente nuevo, falta registrar Nro. Documento identidad / Nombre');
      setTimeout(function () { $('#modal-message').modal('hide'); }, 2500);
    } else if ($('[name="addCliente"]:checked').attr('value') == 1 && ($('#cbo-TiposDocumento').val() == 4 || $('#cbo-TiposDocumento').val() == 2) && ($('#txt-No_Entidad_Cliente').val().length == 1 || $('#txt-No_Entidad_Cliente').val().length == 2)) {//1 = Nuevo
      $('#txt-No_Entidad_Cliente').closest('.form-group').find('.help-block').html('Debes tener mínimo 3 carácteres');
      $('#txt-No_Entidad_Cliente').closest('.form-group').removeClass('has-success').addClass('has-error');

      bEstadoValidacion = false;
      scrollToError($("html, body"), $('#txt-No_Entidad_Cliente'));
    } else if ( arrDetalleGuiaSalida.length == 0){
  		$( '#panel-DetalleGuiasSalidaProductos' ).removeClass('panel-default');
  		$( '#panel-DetalleGuiasSalidaProductos' ).addClass('panel-danger');
      $( '#txt-No_Producto' ).closest('.form-group').find('.help-block').html('Documento <b>sin detalle</b>');
  	  $( '#txt-No_Producto' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      $( '#error-msgTipoDocumento' ).html('');
  		$( '#panel-DetalleGuiasSalidaProductos' ).removeClass('panel-danger');
  		$( '#panel-DetalleGuiasSalidaProductos' ).addClass('panel-default');
  		
  		var ID_Almacen = $( '#cbo-Almacenes' ).val();
  		if ($( '#cbo-DescargarInventario' ).val() == 0)
  		  ID_Almacen = 0;//Ninguno
  
      if ($('input[name=radioTipoDocumento]:checked', '#form-GuiaSalida').val() == 7)//Guia
        $( '#txt-ID_Numero_Documento_Factura' ).val('0');
  
  		var arrGuiaSalidaCabecera = Array();
  		arrGuiaSalidaCabecera = {
  		  'EID_Empresa'                 : $( '#txt-EID_Empresa' ).val(),
  		  'EID_Guia_Cabecera'           : $( '#txt-EID_Guia_Cabecera' ).val(),
  		  'EID_Documento_Cabecera'      : $( '#txt-EID_Documento_Cabecera' ).val(),
  		  'EID_Tipo_Documento_Guia'     : $( '#txt-EID_Tipo_Documento_Guia' ).val(),
  		  'EID_Serie_Documento_Guia'    : $( '#txt-EID_Serie_Documento_Guia' ).val(),
  		  'EID_Numero_Documento_Guia'   : $( '#txt-EID_Numero_Documento_Guia' ).val(),
  		  'EID_Tipo_Documento_Factura'  : $( '#txt-EID_Tipo_Documento_Factura' ).val(),
  		  'EID_Serie_Documento_Factura' : $( '#txt-EID_Serie_Documento_Factura' ).val(),
  		  'EID_Numero_Documento_Factura': $( '#txt-EID_Numero_Documento_Factura' ).val(),
  		  'ID_Entidad'                  : $( '#txt-AID' ).val(),
  		  'Txt_Direccion_Llegada'             : $( '[name="Txt_Direccion_Llegada"]' ).val(),
  		  'Txt_Referencia_Direccion_Llegada'  : $( '[name="Txt_Referencia_Direccion_Llegada"]' ).val(),
  		  'ID_Tipo_Operacion'           : $('input[name=radioTipoDocumento]:checked', '#form-GuiaSalida').val(),
  		  'ID_Tipo_Asiento_Factura'     : $( '#txt-ID_Tipo_Asiento_Factura' ).val(),
  		  'ID_Tipo_Documento_Factura'   : $( '#txt-ID_Tipo_Documento_Factura' ).val(),
  		  'ID_Serie_Documento_Factura'  : $( '#cbo-SeriesDocumento' ).val(),
  		  'ID_Numero_Documento_Factura' : $( '#txt-ID_Numero_Documento_Factura' ).val(),
  		  'ID_Tipo_Asiento_Guia'        : $( '#txt-ID_Tipo_Asiento_Guia' ).val(),
  		  'ID_Tipo_Documento_Guia'      : $( '#txt-ID_Tipo_Documento_Guia' ).val(),
  		  'ID_Serie_Documento_Guia'     : $( '#cbo-SeriesDocumentoGuia' ).val(),
  		  'ID_Numero_Documento_Guia'    : $( '#txt-ID_Numero_Documento_Guia' ).val(),
  		  'ID_Tipo_Movimiento'          : $( '#cbo-TiposMovimientoSalida' ).val(),
  		  'Fe_Emision'                  : $( '#txt-Fe_Emision' ).val(),
  		  'ID_Moneda'                   : $( '#cbo-Monedas' ).val(),
  		  'Nu_Descargar_Inventario'     : $( '#cbo-DescargarInventario' ).val(),
  		  'ID_Almacen'                  : ID_Almacen,
  		  'Txt_Glosa'                   : $( '[name="Txt_Glosa"]' ).val(),
  		  'Po_Descuento'                : $( '#txt-Ss_Descuento' ).val(),
  		  'Ss_Descuento'                : $( '#txt-descuento' ).val(),
  		  'Ss_Total'                    : $( '#txt-total' ).val(),
  		  'ID_Lista_Precio_Cabecera'    : $( '#cbo-lista_precios' ).val(),
  		};
  		
  		var arrFlete = Array();
  		arrFlete = {
  		  'ID_Entidad'                  : $( '#cbo-transportista' ).val(),
  		  'Fe_Traslado'                 : $( '[name="Fe_Traslado"]' ).val(),
  		  'Nu_Tipo_Motivo_Traslado'     : $( '#cbo-motivo_traslado' ).val(),
  		  'No_Chofer'                   : $( '[name="No_Chofer"]' ).val(),
  		  'No_Placa'                    : $( '[name="No_Placa"]' ).val(),
  		  'Nu_Licencia'                 : $( '[name="Nu_Licencia"]' ).val(),
  		  'Txt_Certificado_Inscripcion' : $( '[name="Txt_Certificado_Inscripcion"]' ).val(),
  		};
  		  
      $( '#btn-save' ).text('');
      $( '#btn-save' ).attr('disabled', true);
      $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
    
      $( '#modal-loader' ).modal('show');
      
      url = base_url + 'Logistica/GuiaSalidaController/crudGuiaSalida';
    	$.ajax({
        type		  : 'POST',
        dataType	: 'JSON',
    		url		    : url,
    		data		  : {
    		  arrGuiaSalidaCabecera : arrGuiaSalidaCabecera,
    		  arrDetalleGuiaSalida : arrDetalleGuiaSalida,
    		  arrFlete : arrFlete,
    		},
    		success : function( response ){
    		  $( '#modal-loader' ).modal('hide');
    		  
    	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
      	  $( '#modal-message' ).modal('show');
      	  
    	    $( '.div-mensaje_verificarExisteDocumento' ).removeClass('text-danger text-success');
    		  $( '.div-mensaje_verificarExisteDocumento' ).text('');
    		  
    		  if (response.status == 'success'){
            $( '.div-AgregarEditar' ).hide();
            $( '.div-Listar' ).show();
      	    $( '.modal-message' ).addClass(response.style_modal);
      	    $( '.modal-title-message' ).text(response.message);
      	    setTimeout(function() {$('#modal-message').modal('hide'); }, 1100);
    		  
            $( '#form-GuiaSalida' )[0].reset();
      	    reload_table_guia_salida();
    		  } else if (response.status == 'success2'){
            $( '.div-AgregarEditar' ).hide();
            $( '.div-Listar' ).show();
      	    $( '.modal-message' ).addClass(response.style_modal);
      	    $( '.modal-title-message' ).text(response.message);
      	    setTimeout(function() {$('#modal-message').modal('hide'); }, 5000);
    		  
      	    reload_table_guia_salida();
    		  } else {
      	    $( '.modal-message' ).addClass(response.style_modal);
      	    $( '.modal-title-message' ).text(response.message);
      	    setTimeout(function() {$('#modal-message').modal('hide');}, 1200);
      	    if ($('input[name=radioTipoDocumento]:checked', '#form-GuiaSalida').val() == 7){
      	      $( '#cbo-SeriesDocumentoGuia' ).closest('.form-group').find('.help-block').html('Cambiar serie');
  		        $( '#cbo-SeriesDocumentoGuia' ).closest('.form-group').removeClass('has-success').addClass('has-error');
  		        
      	      $( '#txt-ID_Numero_Documento_Guia' ).closest('.form-group').find('.help-block').html('Cambiar número');
  		        $( '#txt-ID_Numero_Documento_Guia' ).closest('.form-group').removeClass('has-success').addClass('has-error');
      	    } else {
      	      $( '#cbo-SeriesDocumento' ).closest('.form-group').find('.help-block').html('Cambiar serie');
  		        $( '#cbo-SeriesDocumento' ).closest('.form-group').removeClass('has-success').addClass('has-error');
  		        
      	      $( '#txt-ID_Numero_Documento_Guia' ).closest('.form-group').find('.help-block').html('Cambiar número');
  		        $( '#txt-ID_Numero_Documento_Guia' ).closest('.form-group').removeClass('has-success').addClass('has-error');
      	    }
    		  }
    		  
          $( '#btn-save' ).text('');
          $( '#btn-save' ).append( '<span class="fa fa-save"></span> Guardar (ENTER)' );
          $( '#btn-save' ).attr('disabled', false);
    		}
    	});
    }
  }
}

function reload_table_guia_salida(){
  table_guia_salida.ajax.reload(null,false);
}

function anularData_GuiaSalida($modal_delete, ID, Nu_Tipo_Operacion, Nu_Descargar_Inventario){
  $( '#modal-loader' ).modal('show');
    
  url = base_url + 'Logistica/GuiaSalidaController/anularGuiaSalida/' + ID + '/' + Nu_Tipo_Operacion + '/' + Nu_Descargar_Inventario;
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
  	    reload_table_guia_salida();
		  } else {
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
		  }
    }
  });
}

function eliminarData_GuiaSalida($modal_delete, ID, Nu_Tipo_Operacion, Nu_Descargar_Inventario){
  $( '#modal-loader' ).modal('show');
  
  url = base_url + 'Logistica/GuiaSalidaController/eliminarGuiaSalida/' + ID + '/' + Nu_Tipo_Operacion + '/' + Nu_Descargar_Inventario;
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
  	    reload_table_guia_salida();
		  } else {
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
		  }
    }
  });
}