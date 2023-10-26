var url;
var table_orden_compra;
var considerar_igv;
var value_importes_cero = 0.00;
var texto_importes_cero = '0.00';
var arrImpuestosProducto = '{ "arrImpuesto" : [';
var arrImpuestosProductoDetalle;
var accion_orden_compra = '';
var bEstadoValidacion, timesClicked = 0;

$('.date-picker-invoice').val(fDay + '/' + fMonth + '/' + fYear);
$('#txt-Filtro_Fe_Inicio').val(fDay + '/' + fMonth + '/' + fYear);

function agregarOrdenCompra(){
  accion_orden_compra = 'add_orden_compra';
  
  $('#modal-loader').modal('show');

  $(".clearable__clear").toggle(false);
  
  $( '.div-Listar' ).hide();
  $( '.div-AgregarEditar' ).show();
  
  $( '#txt-EID_Empresa' ).focus();
  
  $( '#form-OrdenCompra' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '.title_OrdenCompra' ).text('Nuevo OrdenCompra');
  
  $('[name="EID_Empresa"]').val('');
  $('[name="EID_Documento_Cabecera"]').val('');
  $('[name="ENu_Estado"]').val('');

  $( '.date-picker-invoice' ).val(fDay + '/' + fMonth + '/' + fYear);
  
  $( '#radio-cliente_existente' ).prop('checked', true).iCheck('update');
  $( '#radio-cliente_nuevo' ).prop('checked', false).iCheck('update');
  $( '.div-cliente_existente' ).show();
  $( '.div-cliente_nuevo' ).hide();
  
  $( '#radio-contacto_existente' ).prop('checked', true).iCheck('update');
  $( '#radio-contacto_nuevo' ).prop('checked', false).iCheck('update');
  $( '.div-contacto_existente' ).show();
  $( '.div-contacto_nuevo' ).hide();
  
	$( '#table-DetalleProductosOrdenCompra tbody' ).empty();
  $( '#table-DetalleProductosOrdenCompraModal thead' ).empty();
  $( '#table-DetalleProductosOrdenCompraModal tbody' ).empty();
	  
	$( '#panel-DetalleProductosOrdenCompra' ).removeClass('panel-danger');
	$( '#panel-DetalleProductosOrdenCompra' ).addClass('panel-default');
  
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
  
  considerar_igv=1;
    
  url = base_url + 'HelperController/getMonedas';
  $.post( url , function( response ){
    $( '#cbo-Monedas' ).html('');
    $( '.span-signo' ).text(response[0]['No_Signo']);
    for (var i = 0; i < response.length; i++)
      $( '#cbo-Monedas' ).append( '<option value="' + response[i]['ID_Moneda'] + '" data-no_signo="' + response[i]['No_Signo'] + '">' + response[i]['No_Moneda'] + '</option>' );
  }, 'JSON');
	
  url = base_url + 'HelperController/getMediosPago';
  $.post( url , function( response ){
    $( '#cbo-MediosPago' ).html('');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-MediosPago' ).append( '<option value="' + response[i]['ID_Medio_Pago'] + '" data-nu_tipo="' + response[i]['Nu_Tipo'] + '">' + response[i]['No_Medio_Pago'] + '</option>' );
  }, 'JSON');
  
  $( '#txt-Fe_Emision' ).datepicker({}).on('changeDate', function (selected) {
    var minDate = new Date(selected.date.valueOf());
    $( '#txt-Fe_Vencimiento' ).datepicker('setStartDate', minDate);
    $( '#txt-Fe_Entrega' ).datepicker('setStartDate', minDate);
  });

  var Fe_Emision = $( '#txt-Fe_Emision' ).val().split('/');
  $( '#txt-Fe_Vencimiento' ).datepicker({
    autoclose : true,
    startDate : new Date(Fe_Emision[2], Fe_Emision[1] - 1, Fe_Emision[0]),
    todayHighlight: true
  })
  
  $( '#txt-Fe_Entrega' ).datepicker({
    autoclose : true,
    startDate : new Date(Fe_Emision[2], Fe_Emision[1] - 1, Fe_Emision[0]),
    todayHighlight: true
  })
  
  $( '#txt-Fe_Vencimiento' ).val($( '#txt-Fe_Emision' ).val());
  $( '#txt-Fe_Entrega' ).val($( '#txt-Fe_Emision' ).val());
  
  url = base_url + 'HelperController/getTiposDocumentoIdentidad';
  $.post( url , function( response ){
    $( '#cbo-TiposDocumentoIdentidadProveedor' ).html('');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-TiposDocumentoIdentidadProveedor' ).append( '<option value="' + response[i]['ID_Tipo_Documento_Identidad'] + '" data-nu_cantidad_caracteres="' + response[i]['Nu_Cantidad_Caracteres'] + '">' + response[i]['No_Tipo_Documento_Identidad_Breve'] + '</option>' );
  }, 'JSON');
  
  url = base_url + 'HelperController/getTiposDocumentoIdentidad';
  $.post( url , function( response ){
    $( '#cbo-TiposDocumentoIdentidadContacto_existe' ).html('');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-TiposDocumentoIdentidadContacto_existe' ).append( '<option value="' + response[i]['ID_Tipo_Documento_Identidad'] + '" data-nu_cantidad_caracteres="' + response[i]['Nu_Cantidad_Caracteres'] + '">' + response[i]['No_Tipo_Documento_Identidad_Breve'] + '</option>' );
  }, 'JSON');
  
  url = base_url + 'HelperController/getTiposDocumentoIdentidad';
  $.post( url , function( response ){
    $( '#cbo-TiposDocumentoIdentidadContacto' ).html('');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-TiposDocumentoIdentidadContacto' ).append( '<option value="' + response[i]['ID_Tipo_Documento_Identidad'] + '" data-nu_cantidad_caracteres="' + response[i]['Nu_Cantidad_Caracteres'] + '">' + response[i]['No_Tipo_Documento_Identidad_Breve'] + '</option>' );
  }, 'JSON');

  $( '#cbo-descargar_stock' ).html( '<option value="1">Si</option>' );
  $( '#cbo-descargar_stock' ).append( '<option value="0">No</option>' );

  var arrParams = {
    ID_Almacen: $('#cbo-almacen').val(),
  };
  getListaPrecios(arrParams);

  $( '#table-DetalleProductosOrdenCompra' ).hide();
  
  url = base_url + 'HelperController/getImpuestos';
  $.post( url , function( response ){
    arrImpuestosProducto = '';
    arrImpuestosProductoDetalle = '';
    for (var i = 0; i < response.length; i++)
      arrImpuestosProductoDetalle += '{"ID_Impuesto_Cruce_Documento" : "' + response[i].ID_Impuesto_Cruce_Documento + '", "Ss_Impuesto":"' + response[i].Ss_Impuesto + '", "Nu_Tipo_Impuesto":"' + response[i].Nu_Tipo_Impuesto + '", "No_Impuesto":"' + response[i].No_Impuesto + '"},';
    arrImpuestosProducto = '{ "arrImpuesto" : [' + arrImpuestosProductoDetalle.slice(0, -1) + ']}';
    
    $( '#modal-loader' ).modal('hide');
  }, 'JSON');
  
  var _ID_Producto = '';
  var option_impuesto_producto = '';
}

function verOrdenCompra(ID){
  accion_orden_compra = 'upd_orden_compra';
  
  $( '#modal-loader' ).modal('show');
  
  $( '.div-Listar' ).hide();
  
  $( '#txt-EID_Empresa' ).focus();
  
  $( '#form-OrdenCompra' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
	$( '#panel-DetalleProductosOrdenCompra' ).removeClass('panel-danger');
	$( '#panel-DetalleProductosOrdenCompra' ).addClass('panel-default');
  
  $( '#radio-cliente_existente' ).prop('checked', true).iCheck('update');
  $( '#radio-cliente_nuevo' ).prop('checked', false).iCheck('update');
  $( '.div-cliente_existente' ).show();
  $( '.div-cliente_nuevo' ).hide();
  
  $( '#radio-contacto_existente' ).prop('checked', true).iCheck('update');
  $( '#radio-contacto_nuevo' ).prop('checked', false).iCheck('update');
  $( '.div-contacto_existente' ).show();
  $( '.div-contacto_nuevo' ).hide();
  
  $( '#table-DetalleProductosOrdenCompra tbody' ).empty();
  $( '#table-DetalleProductosOrdenCompraModal thead' ).empty();
  $( '#table-DetalleProductosOrdenCompraModal tbody' ).empty();
      
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
	
  $('[name="ENu_Estado"]').val('');

  $( '#btn-save' ).attr('disabled', false);

  considerar_igv=1;
	
  url = base_url + 'HelperController/getTiposDocumentoIdentidad';
  $.post( url , function( response ){
    $( '#cbo-TiposDocumentoIdentidadProveedor' ).html('');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-TiposDocumentoIdentidadProveedor' ).append( '<option value="' + response[i]['ID_Tipo_Documento_Identidad'] + '" data-nu_cantidad_caracteres="' + response[i]['Nu_Cantidad_Caracteres'] + '">' + response[i]['No_Tipo_Documento_Identidad_Breve'] + '</option>' );
  }, 'JSON');
  
  url = base_url + 'HelperController/getTiposDocumentoIdentidad';
  $.post( url , function( response ){
    $( '#cbo-TiposDocumentoIdentidadContacto' ).html('');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-TiposDocumentoIdentidadContacto' ).append( '<option value="' + response[i]['ID_Tipo_Documento_Identidad'] + '" data-nu_cantidad_caracteres="' + response[i]['Nu_Cantidad_Caracteres'] + '">' + response[i]['No_Tipo_Documento_Identidad_Breve'] + '</option>' );
  }, 'JSON');
  
  url = base_url + 'Logistica/OrdenCompraController/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $( '.div-AgregarEditar' ).show();
      
      $( '.title_OrdenCompra' ).text('Modifcar OrdenCompra');
      
      $('[name="EID_Empresa"]').val(response.arrEdit[0].ID_Empresa);
      $('[name="EID_Documento_Cabecera"]').val(response.arrEdit[0].ID_Documento_Cabecera);
      $('[name="ENu_Estado"]').val(response.arrEdit[0].Nu_Estado);
      
      //Datos Documento
      $( '#txt-ID_Serie_Documento' ).val(response.arrEdit[0].ID_Serie_Documento);
      $( '#txt-ID_Numero_Documento' ).val(response.arrEdit[0].ID_Numero_Documento);
      
      $( '[name="Fe_Emision"]' ).val(ParseDateString(response.arrEdit[0].Fe_Emision, 6, '-'));
  	  
      $( '#txt-Fe_Emision' ).datepicker({}).on('changeDate', function (selected) {
        var minDate = new Date(selected.date.valueOf());
        $( '#txt-Fe_Vencimiento' ).datepicker('setStartDate', minDate);
        $( '#txt-Fe_Entrega' ).datepicker('setStartDate', minDate);
      });
      
      var Fe_Emision = response.arrEdit[0].Fe_Emision.split('-');
      $( '#txt-Fe_Vencimiento' ).datepicker({
        autoclose : true,
        startDate : new Date(parseInt(Fe_Emision[0]), parseInt(Fe_Emision[1]) - 1, parseInt(Fe_Emision[2])),
        todayHighlight: true
      })
      
      $( '#txt-Fe_Entrega' ).datepicker({
        autoclose : true,
        startDate : new Date(parseInt(Fe_Emision[0]), parseInt(Fe_Emision[1]) - 1, parseInt(Fe_Emision[2])),
        todayHighlight: true
      })
      
      $( '#txt-Fe_Vencimiento' ).datepicker('setStartDate', new Date(Fe_Emision[0] + "/" + Fe_Emision[1] + "/" + Fe_Emision[2]) );
      $( '#txt-Fe_Entrega' ).datepicker('setStartDate', new Date(Fe_Emision[0] + "/" + Fe_Emision[1] + "/" + Fe_Emision[2]) );
      
      $( '#txt-Fe_Vencimiento' ).val(ParseDateString(response.arrEdit[0].Fe_Vencimiento, 6, '-'));
      $( '#txt-Fe_Entrega' ).val(ParseDateString(response.arrEdit[0].Fe_Periodo, 6, '-'));
      
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
      
      url = base_url + 'HelperController/getMediosPago';
      $.post( url , function( responseMediosPago ){
        $( '#cbo-MediosPago' ).html('');
        for (var i = 0; i < responseMediosPago.length; i++){
          selected = '';
          if(response.arrEdit[0].ID_Medio_Pago == responseMediosPago[i]['ID_Medio_Pago'])
            selected = 'selected="selected"';
          $( '#cbo-MediosPago' ).append( '<option value="' + responseMediosPago[i]['ID_Medio_Pago'] + '" data-nu_tipo="' + responseMediosPago[i]['Nu_Tipo'] + '" ' + selected + '>' + responseMediosPago[i]['No_Medio_Pago'] + '</option>' );
        }
      }, 'JSON');
  	  
      if ( response.arrEdit[0].Nu_Descargar_Inventario == 1 ) {        
        $( '#cbo-descargar_stock' ).html( '<option value="1" selected>Si</option>' );
        $( '#cbo-descargar_stock' ).append( '<option value="0">No</option>' );
      } else {      
        $( '#cbo-descargar_stock' ).html( '<option value="1">Si</option>' );
        $( '#cbo-descargar_stock' ).append( '<option value="0" selected>No</option>' );
      }
      
	    //PROVEEDOR
      $( '#txt-AID' ).val(response.arrEdit[0].ID_Entidad);
      $( '#txt-ANombre' ).val(response.arrEdit[0].No_Entidad);
      $( '#txt-ACodigo' ).val(response.arrEdit[0].Nu_Documento_Identidad);
      $( '#txt-Txt_Direccion_Entidad' ).val(response.arrEdit[0].Txt_Direccion_Entidad);
	    
	    //CONTACTO
      $( '#txt-AID_Contacto' ).val(response.arrEdit[0].ID_Contacto);
      
      url = base_url + 'HelperController/getTiposDocumentoIdentidad';
      $.post( url , function( responseTiposDocumentoIdentidad ){
        $( '#cbo-TiposDocumentoIdentidadContacto_existe' ).html( '' );
        for (var i = 0; i < responseTiposDocumentoIdentidad.length; i++){
          selected = '';
          if(response.arrEdit[0].ID_Tipo_Documento_Identidad_Contacto == responseTiposDocumentoIdentidad[i]['ID_Tipo_Documento_Identidad'])
            selected = 'selected="selected"';
          $( '#cbo-TiposDocumentoIdentidadContacto_existe' ).append( '<option value="' + responseTiposDocumentoIdentidad[i]['ID_Tipo_Documento_Identidad'] + '" data-nu_cantidad_caracteres="' + responseTiposDocumentoIdentidad[i]['Nu_Cantidad_Caracteres'] + '" ' + selected + '>' + responseTiposDocumentoIdentidad[i]['No_Tipo_Documento_Identidad_Breve'] + '</option>' );
        }
      }, 'JSON');
      
      $( '#txt-Nu_Documento_Identidad_existe' ).val(response.arrEdit[0].Nu_Documento_Identidad_Contacto);
      $( '#txt-No_Contacto_existe' ).val(response.arrEdit[0].No_Contacto);
      $( '#txt-Txt_Email_Contacto_existe' ).val(response.arrEdit[0].Txt_Email_Contacto);
      $( '#txt-Nu_Telefono_Contacto_existe' ).val(response.arrEdit[0].Nu_Telefono_Contacto);
      $( '#txt-Nu_Celular_Contacto_existe' ).val(response.arrEdit[0].Nu_Celular_Contacto);
	    
      url = base_url + 'HelperController/getListaPrecio';
      $.post( url, {Nu_Tipo_Lista_Precio : $( '[name="Nu_Tipo_Lista_Precio"]' ).val(), ID_Organizacion: response.arrEdit[0].ID_Organizacion, ID_Almacen : response.arrEdit[0].ID_Almacen}, function( responseLista ){
        var iCantidadRegistrosListaPrecios = responseLista.length;
        if (iCantidadRegistrosListaPrecios == 1) {
          $('#cbo-lista_precios').html('<option value="0">- Seleccionar -</option>');
          selected = '';
          if (response.arrEdit[0].ID_Lista_Precio_Cabecera == responseLista[0].ID_Lista_Precio_Cabecera)
            selected = 'selected="selected"';
          $('#cbo-lista_precios').append('<option value="' + responseLista[0].ID_Lista_Precio_Cabecera + '" ' + selected + '>' + responseLista[0].No_Lista_Precio + '</option>' );
        } else if ( iCantidadRegistrosListaPrecios > 1 ) {
          $( '#cbo-lista_precios' ).html( '<option value="0">- Seleccionar -</option>');
          for (var i = 0; i < iCantidadRegistrosListaPrecios; i++) {
            selected = '';
            if(response.arrEdit[0].ID_Lista_Precio_Cabecera == responseLista[i].ID_Lista_Precio_Cabecera)
              selected = 'selected="selected"';
            $( '#cbo-lista_precios' ).append( '<option value="' + responseLista[i].ID_Lista_Precio_Cabecera + '" ' + selected + '>' + responseLista[i].No_Lista_Precio + '</option>' );
          }
        } else {
          $( '#cbo-lista_precios' ).html( '<option value="0">- Sin lista precio -</option>');
        }
      }, 'JSON');
      
      if ( response.arrEdit[0].Txt_Garantia != '' && response.arrEdit[0].Txt_Garantia != null ) {
	      $('[name="Txt_Garantia"]').val( response.arrEdit[0].Txt_Garantia );
      }
      
      if ( response.arrEdit[0].Txt_Glosa != '' && response.arrEdit[0].Txt_Glosa != null ) {
        $('[name="Txt_Glosa"]').val( response.arrEdit[0].Txt_Glosa );
      }
	    
      //Detalle
      $( '#table-DetalleProductosOrdenCompra' ).show();
      $( '#table-DetalleProductosOrdenCompra tbody' ).empty();
      
      var table_detalle_producto = '';
      var _ID_Producto = '';
      var $Ss_SubTotal_Producto = 0.00;
      var $Ss_IGV_Producto = 0.00;
      var $Ss_Descuento_Producto = 0.00;
      var $Ss_Total_Producto = 0.00;
      var $Ss_Gravada = 0.00;
      var $Ss_Exonerada = 0.00;
      var $Ss_Inafecto = 0.00;
      var $Ss_Gratuita = 0.00;
      var $Ss_IGV = 0.00;
      var $Ss_Total = 0.00;
      var option_impuesto_producto = '';
      
      var $fDescuento_Producto = 0;
      var fDescuento_Total_Producto = 0;
      var globalImpuesto = 0;
      var $iDescuentoGravada = 0;
      var $iDescuentoExonerada = 0;
      var $iDescuentoInafecto = 0;
      var $iDescuentoGratuita = 0;
      var $iDescuentoGlobalImpuesto = 0;
      var selected;
      
      var iTotalRegistros = response.arrEdit.length;
      var iTotalRegistrosImpuestos = response.arrImpuesto.length;
      for (var i = 0; i < iTotalRegistros; i++) {
        if (_ID_Producto != response.arrEdit[i].ID_Producto) {
          _ID_Producto = response.arrEdit[i].ID_Producto;
          option_impuesto_producto = '';
        }
        
        $Ss_SubTotal_Producto = parseFloat(response.arrEdit[i].Ss_SubTotal_Producto)
        if (response.arrEdit[i].Nu_Tipo_Impuesto == 1){
					$Ss_IGV += parseFloat(response.arrEdit[i].Ss_Impuesto_Producto);
					$Ss_Gravada += $Ss_SubTotal_Producto;
        } else if (response.arrEdit[i].Nu_Tipo_Impuesto == 2){
          $Ss_Inafecto += $Ss_SubTotal_Producto;
        } else if (response.arrEdit[i].Nu_Tipo_Impuesto == 3){
          $Ss_Exonerada += $Ss_SubTotal_Producto;
        } else if (response.arrEdit[i].Nu_Tipo_Impuesto == 4){
          $Ss_Gratuita += $Ss_SubTotal_Producto;
        }
        
        $Ss_Descuento_Producto += parseFloat(response.arrEdit[i].Ss_Descuento_Producto);
        $Ss_Total += parseFloat(response.arrEdit[i].Ss_Total_Producto);
        
	      for (var x = 0; x < iTotalRegistrosImpuestos; x++){
	        selected = '';
	        if (response.arrImpuesto[x].ID_Impuesto_Cruce_Documento == response.arrEdit[i].ID_Impuesto_Cruce_Documento)
	          selected = 'selected="selected"';
          option_impuesto_producto += "<option value='" + response.arrImpuesto[x].ID_Impuesto_Cruce_Documento + "' data-nu_tipo_impuesto='" + response.arrImpuesto[x].Nu_Tipo_Impuesto + "' data-impuesto_producto='" + response.arrImpuesto[x].Ss_Impuesto + "' " + selected + ">" + response.arrImpuesto[x].No_Impuesto + "</option>";
	      }
	      
        table_detalle_producto += 
        "<tr id='tr_detalle_producto" + response.arrEdit[i].ID_Producto + "'>"
          +"<td style='display:none;' class='text-left'>" + response.arrEdit[i].ID_Producto + "</td>"
          +"<td class='text-right'><input type='text' inputmode='decimal' class='pos-input txt-Qt_Producto form-control input-size_cantidad input-decimal' value='" + Math.round10(response.arrEdit[i].Qt_Producto, -3) + "' autocomplete='off'></td>"
          +"<td class='text-left'>" + response.arrEdit[i].Nu_Codigo_Barra + " " + response.arrEdit[i].No_Producto + ' [' + response.arrEdit[i].No_Unidad_Medida + ']' + "</td>"
          +"<td class='text-right'><input type='text' inputmode='decimal' class='pos-input txt-Ss_Precio form-control input-size_importe input-decimal' value='" + Math.round10(response.arrEdit[i].Ss_Precio, -3) + "' autocomplete='off'></td>"
          +"<td class='text-right'>"
            +"<select class='cbo-ImpuestosProducto form-control required' style='width: 100%;'>"
              +option_impuesto_producto
            +"</select>"
          +"</td>"
          +"<td style='display:none;' class='text-right'><input type='text' class='pos-input txt-Ss_SubTotal_Producto form-control' value='" + response.arrEdit[i].Ss_SubTotal_Producto + "' autocomplete='off' disabled></td>"
          +"<td class='text-right'><input type='text' inputmode='decimal' class='pos-input txt-Ss_Descuento form-control input-decimal input-size_otros' value='" + (response.arrEdit[i].Po_Descuento_Impuesto_Producto == 0.00 ? '' : response.arrEdit[i].Po_Descuento_Impuesto_Producto) + "' autocomplete='off'></td>"
          +"<td class='text-right'><input type='text' inputmode='decimal' class='pos-input txt-Ss_Total_Producto form-control input-decimal input-size_importe' value='" + response.arrEdit[i].Ss_Total_Producto + "' autocomplete='off'></td>"
          +"<td style='display:none;' class='text-right td-fDescuentoSinImpuestosItem'>" + (response.arrEdit[i].Ss_Descuento_Producto == 0.00 ? '' : response.arrEdit[i].Ss_Descuento_Producto) + "</td>"
          +"<td style='display:none;' class='text-right td-fDescuentoImpuestosItem'>" + (response.arrEdit[i].Ss_Descuento_Impuesto_Producto == 0.00 ? '' : response.arrEdit[i].Ss_Descuento_Impuesto_Producto) + "</td>"
          +"<td class='text-center'><button type='button' id='btn-deleteProducto' class='btn btn-sm btn-link' alt='Eliminar' title='Eliminar'><i class='fa fa-trash-o fa-2x' aria-hidden='true'> </i></button></td>"
        +"</tr>";
      }
      
		  $( '#table-DetalleProductosOrdenCompra >tbody' ).append(table_detalle_producto);
            
			$( '#txt-subTotal' ).val( $Ss_Gravada.toFixed(2) );
			$( '#span-subTotal' ).text( $Ss_Gravada.toFixed(2) );
      
      $( '#txt-exonerada' ).val( $Ss_Exonerada.toFixed(2) );
      $( '#span-exonerada' ).text( $Ss_Exonerada.toFixed(2) );
			
			$( '#txt-inafecto' ).val( $Ss_Inafecto.toFixed(2) );
			$( '#span-inafecto' ).text( $Ss_Inafecto.toFixed(2) );
			
      if (parseFloat(response.arrEdit[0].Ss_Descuento) > 0 && $Ss_Descuento_Producto == 0)
        $( '#txt-Ss_Descuento' ).val( response.arrEdit[0].Po_Descuento );
      else
        $( '#txt-Ss_Descuento' ).val( '' );
      
      $( '#txt-descuento' ).val( response.arrEdit[0].Ss_Descuento );
      $( '#span-descuento' ).text( response.arrEdit[0].Ss_Descuento );
			
			$( '#txt-impuesto' ).val( $Ss_IGV.toFixed(2) );
			$( '#span-impuesto' ).text( $Ss_IGV.toFixed(2) );
    
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
        
        $( '#modal-loader' ).modal('hide');
      }, 'JSON');
      
      var _ID_Producto = '';
      var option_impuesto_producto = '';
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
  })
}

function eliminarOrdenCompra(ID, Nu_Descargar_Inventario){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '.modal-message-delete' ).removeClass('modal-danger modal-warning modal-success');
  $( '.modal-message-delete' ).addClass('modal-danger');
  
  $( '.modal-title-message-delete' ).text('¿Deseas eliminar la orden de compra?');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });

  accion_orden_compra = 'eliminar_orden_compra';

  $(document).bind('keydown', 'return', function(){
    if(accion_orden_compra == 'eliminar_orden_compra') {
      eliminarData_OrdenCompra($modal_delete, ID, Nu_Descargar_Inventario);
      accion_orden_compra = '';
    }
  });

  $( '#btn-save-delete' ).off('click').click(function () {
    eliminarData_OrdenCompra($modal_delete, ID, Nu_Descargar_Inventario);
    accion_orden_compra = '';
  });
}

$(function () {
  $('[data-mask]').inputmask();
  
  //Flat red color scheme for iCheck
  $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
    checkboxClass: 'icheckbox_flat-green',
    radioClass   : 'iradio_flat-green'
  });
  
  $( '#radio-cliente_existente' ).on('ifChecked', function () {
    $( '.div-cliente_existente' ).show();
    $( '.div-cliente_nuevo' ).hide();
  })
  
  $( '#radio-cliente_nuevo' ).on('ifChecked', function () {
    $( '.div-cliente_existente' ).hide();
    $( '.div-cliente_nuevo' ).show();
  })
  
  $( '#radio-contacto_existente' ).on('ifChecked', function () {
    $( '.div-contacto_existente' ).show();
    $( '.div-contacto_nuevo' ).hide();
  })
  
  $( '#radio-contacto_nuevo' ).on('ifChecked', function () {
    $( '.div-contacto_existente' ).hide();
    $( '.div-contacto_nuevo' ).show();
  })
  
  //LAE API SUNAT / RENIEC - CLIENTE
  $( '#btn-cloud-api_orden_compra_cliente' ).click(function(){
    if ( $( '#cbo-TiposDocumentoIdentidadProveedor' ).val().length === 0){
      $( '#cbo-TiposDocumentoIdentidadProveedor' ).closest('.form-group').find('.help-block').html('Seleccionar tipo doc. identidad');
  	  $( '#cbo-TiposDocumentoIdentidadProveedor' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ($( '#cbo-TiposDocumentoIdentidadProveedor' ).find(':selected').data('nu_cantidad_caracteres') != $( '#txt-Nu_Documento_Identidad_Proveedor').val().length ) {
      $( '#txt-Nu_Documento_Identidad_Proveedor' ).closest('.form-group').find('.help-block').html('Debe ingresar ' + $( '#cbo-TiposDocumentoIdentidadProveedor' ).find(':selected').data('nu_cantidad_caracteres') + ' dígitos' );
  	  $( '#txt-Nu_Documento_Identidad_Proveedor' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if (
      (
        $( '#cbo-TiposDocumentoIdentidadProveedor' ).val() == 1 ||
        $( '#cbo-TiposDocumentoIdentidadProveedor' ).val() == 3 ||
        $( '#cbo-TiposDocumentoIdentidadProveedor' ).val() == 5 ||
        $( '#cbo-TiposDocumentoIdentidadProveedor' ).val() == 6
      )
      ) {
      $( '#cbo-TiposDocumentoIdentidadProveedor' ).closest('.form-group').find('.help-block').html('Disponible DNI / RUC');
  	  $( '#cbo-TiposDocumentoIdentidadProveedor' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      $( '#btn-cloud-api_orden_compra_cliente' ).text('');
      $( '#btn-cloud-api_orden_compra_cliente' ).attr('disabled', true);
      $( '#btn-cloud-api_orden_compra_cliente' ).append( '<i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
      
      // Obtener datos de SUNAT y RENIEC
      var url_api = 'https://www.laesystems.com/librerias/sunat/partner/format/json/x-api-key/';
			if ( $( '#cbo-TiposDocumentoIdentidadProveedor' ).val() == 2 )//2=RENIEC
				url_api = 'https://www.laesystems.com/librerias/reniec/partner/format/json/x-api-key/';
			url_api = url_api + sTokenGlobal;
			
      var data = {
        ID_Tipo_Documento_Identidad : $( '#cbo-TiposDocumentoIdentidadProveedor' ).val(),
        Nu_Documento_Identidad : $( '#txt-Nu_Documento_Identidad_Proveedor' ).val(),
      };
      
      $.ajax({
        url   : url_api,
        type  :'POST',
        data  : data,
        success: function(response){
          $( '#btn-cloud-api_orden_compra_cliente' ).closest('.form-group').find('.help-block').html('');
      	  $( '#btn-cloud-api_orden_compra_cliente' ).closest('.form-group').removeClass('has-success').addClass('has-error');
      	  
          if (response.success == true){
            $( '#txt-No_Entidad_Proveedor' ).val( response.data.No_Names );
            if ( $( '#cbo-TiposDocumentoIdentidadProveedor' ).val() == 4) {//RUC
              $( '#txt-Txt_Direccion_Entidad_Proveedor' ).val( response.data.Txt_Address );
              $( '#txt-Nu_Telefono_Entidad_Proveedor' ).val( response.data.Nu_Phone );
              $( '#txt-Nu_Celular_Entidad_Proveedor' ).val( response.data.Nu_Cellphone );
            }
          } else {
            $( '#txt-No_Entidad_Proveedor' ).val( '' );
            if ( $( '#cbo-TiposDocumentoIdentidadProveedor' ).val() == 4) {//RUC
              $( '#txt-Txt_Direccion_Entidad_Proveedor' ).val( '' );
              $( '#txt-Nu_Telefono_Entidad_Proveedor' ).val( '' );
              $( '#txt-Nu_Celular_Entidad_Proveedor' ).val( '' );
            }
            $( '#txt-Nu_Documento_Identidad_Proveedor' ).closest('.form-group').find('.help-block').html(response.msg);
        	  $( '#txt-Nu_Documento_Identidad_Proveedor' ).closest('.form-group').removeClass('has-success').addClass('has-error');
        	  
  		  	  $( '#txt-Nu_Documento_Identidad_Proveedor' ).focus();
  		  	  $( '#txt-Nu_Documento_Identidad_Proveedor' ).select();
          }
  		  	
          $( '#btn-cloud-api_orden_compra_cliente' ).text('');
          $( '#btn-cloud-api_orden_compra_cliente' ).attr('disabled', false);
          $( '#btn-cloud-api_orden_compra_cliente' ).append( '<i class="fa fa-cloud-download fa-lg"></i>' );
        },
        error: function(response){
          $( '#btn-cloud-api_orden_compra_cliente' ).closest('.form-group').find('.help-block').html('Sin acceso');
      	  $( '#btn-cloud-api_orden_compra_cliente' ).closest('.form-group').removeClass('has-success').addClass('has-error');
      	  
          $( '#txt-No_Entidad_Proveedor' ).val( '' );
          $( '#txt-Txt_Direccion_Entidad_Proveedor' ).val( '' );
          $( '#txt-Nu_Telefono_Entidad_Proveedor' ).val( '' );
          $( '#txt-Nu_Celular_Entidad_Proveedor' ).val( '' );
              
          $( '#btn-cloud-api_orden_compra_cliente' ).text('');
          $( '#btn-cloud-api_orden_compra_cliente' ).attr('disabled', false);
          $( '#btn-cloud-api_orden_compra_cliente' ).append( '<i class="fa fa-cloud-download fa-lg"></i>' );
        }
      });// /. Get data SUNAT / RENIEC
    }
  })
  
  //LAE API SUNAT / RENIEC - CONTACTO
  $( '#btn-cloud-api_orden_compra_contacto' ).click(function(){
    if ( $( '#cbo-TiposDocumentoIdentidadContacto' ).val().length === 0){
      $( '#cbo-TiposDocumentoIdentidadContacto' ).closest('.form-group').find('.help-block').html('Seleccionar tipo doc. identidad');
  	  $( '#cbo-TiposDocumentoIdentidadContacto' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ($( '#cbo-TiposDocumentoIdentidadContacto' ).find(':selected').data('nu_cantidad_caracteres') != $( '#txt-Nu_Documento_Identidad').val().length ) {
      $( '#txt-Nu_Documento_Identidad' ).closest('.form-group').find('.help-block').html('Debe ingresar ' + $( '#cbo-TiposDocumentoIdentidadContacto' ).find(':selected').data('nu_cantidad_caracteres') + ' dígitos' );
  	  $( '#txt-Nu_Documento_Identidad' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( 
        (
          $( '#cbo-TiposDocumentoIdentidadContacto' ).val() == 1 ||
          $( '#cbo-TiposDocumentoIdentidadContacto' ).val() == 3 ||
          $( '#cbo-TiposDocumentoIdentidadContacto' ).val() == 5 ||
          $( '#cbo-TiposDocumentoIdentidadContacto' ).val() == 6
        )
        ) {
      $( '#cbo-TiposDocumentoIdentidadContacto' ).closest('.form-group').find('.help-block').html('Disponible DNI / RUC');
  	  $( '#cbo-TiposDocumentoIdentidadContacto' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      $( '#btn-cloud-api_orden_compra_contacto' ).text('');
      $( '#btn-cloud-api_orden_compra_contacto' ).attr('disabled', true);
      $( '#btn-cloud-api_orden_compra_contacto' ).append( '<i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
      
      // Obtener datos de SUNAT y RENIEC
      var url_api = 'https://www.laesystems.com/librerias/sunat/partner/format/json/x-api-key/';
			if ( $( '#cbo-TiposDocumentoIdentidadContacto' ).val() == 2 )//2=RENIEC
				url_api = 'https://www.laesystems.com/librerias/reniec/partner/format/json/x-api-key/';
			url_api = url_api + sTokenGlobal;
			
      var data = {
        ID_Tipo_Documento_Identidad : $( '#cbo-TiposDocumentoIdentidadContacto' ).val(),
        Nu_Documento_Identidad : $( '#txt-Nu_Documento_Identidad' ).val(),
      };
      
      $.ajax({
        url   : url_api,
        type  :'POST',
        data  : data,
        success: function(response){
          $( '#btn-cloud-api_orden_compra_contacto' ).closest('.form-group').find('.help-block').html('');
      	  $( '#btn-cloud-api_orden_compra_contacto' ).closest('.form-group').removeClass('has-success').addClass('has-error');
      	  
          if (response.success == true){
            $( '#txt-No_Contacto' ).val( response.data.No_Names );
            if ( $( '#cbo-TiposDocumentoIdentidadContacto' ).val() == 4) {//RUC
              $( '#txt-Nu_Telefono_Contacto' ).val( response.data.Nu_Phone );
              $( '#txt-Nu_Celular_Contacto' ).val( response.data.Nu_Cellphone );
            }
          } else {
            $( '#txt-No_Contacto' ).val( '' );
            if ( $( '#cbo-TiposDocumentoIdentidadContacto' ).val() == 4) {//RUC
              $( '#txt-Nu_Telefono_Contacto' ).val( '' );
              $( '#txt-Nu_Celular_Contacto' ).val( '' );
            }
            $( '#txt-Nu_Documento_Identidad' ).closest('.form-group').find('.help-block').html(response.msg);
        	  $( '#txt-Nu_Documento_Identidad' ).closest('.form-group').removeClass('has-success').addClass('has-error');
          
    		  	$( '#txt-Nu_Documento_Identidad' ).focus();
    		  	$( '#txt-Nu_Documento_Identidad' ).select();
          }
  		  	
          $( '#btn-cloud-api_orden_compra_contacto' ).text('');
          $( '#btn-cloud-api_orden_compra_contacto' ).attr('disabled', false);
          $( '#btn-cloud-api_orden_compra_contacto' ).append( '<i class="fa fa-cloud-download fa-lg"></i>' );
        },
        error: function(response){
          $( '#btn-cloud-api_orden_compra_contacto' ).closest('.form-group').find('.help-block').html('Sin acceso');
      	  $( '#btn-cloud-api_orden_compra_contacto' ).closest('.form-group').removeClass('has-success').addClass('has-error');
      	  
          $( '#txt-No_Contacto' ).val( '' );
          $( '#txt-Nu_Telefono_Contacto' ).val( '' );
          $( '#txt-Nu_Celular_Contacto' ).val( '' );
              
          $( '#btn-cloud-api_orden_compra_contacto' ).text('');
          $( '#btn-cloud-api_orden_compra_contacto' ).attr('disabled', false);
          $( '#btn-cloud-api_orden_compra_contacto' ).append( '<i class="fa fa-cloud-download fa-lg"></i>' );
        }
      });// /. Get data SUNAT / RENIEC
    }
  })
  
	/* Tipo Documento Identidad Proveedor */
	$( '#cbo-TiposDocumentoIdentidadProveedor' ).change(function(){
	  if ( $(this).val() == 2 ) {//DNI
		  $( '#label-Nombre_Documento_Identidad_Proveedor' ).text('DNI');
		  $( '#label-No_Entidad_Proveedor' ).text('Nombre(s) y Apellidos');
			$( '#txt-Nu_Documento_Identidad_Proveedor' ).attr('maxlength', $(this).find(':selected').data('nu_cantidad_caracteres'));
	  } else if ( $(this).val() == 4 ) {//RUC
		  $( '#label-Nombre_Documento_Identidad_Proveedor' ).text('RUC');
		  $( '#label-No_Entidad_Proveedor' ).text('Razón Social');
			$( '#txt-Nu_Documento_Identidad_Proveedor' ).attr('maxlength', $(this).find(':selected').data('nu_cantidad_caracteres'));
	  } else {
	    $( '#label-Nombre_Documento_Identidad_Proveedor' ).text('# Documento Identidad');
		  $( '#label-No_Entidad_Proveedor' ).text('Nombre(s) y Apellidos');
			$( '#txt-Nu_Documento_Identidad_Proveedor' ).attr('maxlength', $(this).find(':selected').data('nu_cantidad_caracteres'));
	  }
	})
	
	/* Tipo Documento Identidad Contacto */
	$( '#cbo-TiposDocumentoIdentidadContacto' ).change(function(){
	  if ( $(this).val() == 2 ) {//DNI
		  $( '#label-Nombre_Documento_Identidad' ).text('DNI');
		  $( '#label-No_Contacto' ).text('Nombre(s) y Apellidos');
			$( '#txt-Nu_Documento_Identidad' ).attr('maxlength', $(this).find(':selected').data('nu_cantidad_caracteres'));
	  } else if ( $(this).val() == 4 ) {//RUC
		  $( '#label-Nombre_Documento_Identidad' ).text('RUC');
		  $( '#label-No_Contacto' ).text('Razón Social');
			$( '#txt-Nu_Documento_Identidad' ).attr('maxlength', $(this).find(':selected').data('nu_cantidad_caracteres'));
	  } else {
	    $( '#label-Nombre_Documento_Identidad' ).text('# Documento Identidad');
		  $( '#label-No_Contacto' ).text('Nombre(s) y Apellidos');
			$( '#txt-Nu_Documento_Identidad' ).attr('maxlength', $(this).find(':selected').data('nu_cantidad_caracteres'));
	  }
	})

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

  url = base_url + 'Logistica/OrdenCompraController/ajax_list';
  table_orden_compra = $('#table-OrdenCompra').DataTable({
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
    'order':[],
    'ajax': {
      'url'       : url,
      'type'      : 'POST',
      'dataType'  : 'json',
      'data'      : function ( data ) {
        data.sMethod = $('#hidden-sMethod').val(),
          data.filtro_almacen = $('#cbo-filtro_almacen').val(),
        data.Filtro_Fe_Inicio       = ParseDateString($( '#txt-Filtro_Fe_Inicio' ).val(), 1, '/'),
        data.Filtro_Fe_Fin          = ParseDateString($( '#txt-Filtro_Fe_Fin' ).val(), 1, '/'),
        data.Filtro_SerieDocumento  = $( '#txt-Filtro_SerieDocumento' ).val(),
        data.Filtro_NumeroDocumento = $( '#txt-Filtro_NumeroDocumento' ).val(),
        data.Filtro_Contacto        = $( '#txt-Filtro_Contacto' ).val(),
        data.Filtro_Estado          = $( '#cbo-Filtro_Estado option:selected' ).val(),
        data.Filtro_Entidad         = $( '#txt-Filtro_Entidad' ).val();
      },
    },
    'columnDefs': [{
      'className' : 'text-center',
      'targets'   : 'no-sort',
      'orderable' : false,
    },
    {
      'className' : 'text-right',
      'targets'   : 'no-sort_right',
      'orderable' : false,
    },
    {
      'className' : 'text-left',
      'targets'   : 'no-sort_left',
      'orderable' : false,
    },
    {
      'className' : 'text-center',
      'targets'   : 'sort_center',
      'orderable' : true,
    },
    {
      'className' : 'text-right',
      'targets'   : 'sort_right',
      'orderable' : true,
    },],
    'lengthMenu': [[10, 100, 1000, -1], [10, 100, 1000, "Todos"]],
  });

  $('.dataTables_length').addClass('col-xs-4 col-sm-5 col-md-1');
  $('.dataTables_info').addClass('col-xs-8 col-sm-7 col-md-4');
  $('.dataTables_paginate').addClass('col-xs-12 col-sm-12 col-md-7');

  $( '#btn-filter' ).click(function(){
    table_orden_compra.ajax.reload();
  });
  
  $( '#form-OrdenCompra' ).validate({
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
			Fe_Vencimiento: {
				required: true,
			},
			Fe_Entrega: {
				required: true,
			},
		},
		messages:{
			ID_Serie_Documento:{
				required: "Ingresar serie",
			},
			ID_Numero_Documento:{
				required: "Ingresar número",
			},
			Fe_Emision:{
				required: "Ingresar F. Emisión",
			},
			Fe_Vencimiento:{
				required: "Ingresar F. Vencimiento",
			},
			Fe_Entrega:{
				required: "Ingresar F. Entrega",
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
		submitHandler: form_OrdenCompra
	});
	
	$( '#cbo-Monedas' ).change(function(){
	  if ( $(this).val() > 0 )
	    $( '.span-signo' ).text( $(this).find(':selected').data('no_signo') );
	})

  $('#cbo-almacen').change(function () {
    if ($(this).val() > 0) {
      var arrParams = {
        ID_Almacen: $('#cbo-almacen').val(),
      };
      getListaPrecios(arrParams);
    }
  })

  var _ID_Producto = '';
  var option_impuesto_producto = '';
	$( '#btn-addProductoOrden' ).click(function(){
	  var $ID_Producto                  = $( '#txt-ID_Producto' ).val();
    var $Nu_Codigo_Barra              = $( '#txt-Nu_Codigo_Barra' ).val();
    var $No_Producto                  = $( '#txt-No_Producto' ).val();
    var $Ss_Precio                    = parseFloat($( '#txt-Ss_Precio' ).val());
    var $ID_Impuesto_Cruce_Documento  = $( '#txt-ID_Impuesto_Cruce_Documento' ).val();
    var $Nu_Tipo_Impuesto             = $( '#txt-Nu_Tipo_Impuesto' ).val();
    var $Ss_Impuesto                  = $( '#txt-Ss_Impuesto' ).val();
    
    bEstadoValidacion = validatePreviousDocumentToSaveOrderPurchase();

    if ( $ID_Producto.length === 0 || $No_Producto.length === 0) {
	    $( '#txt-No_Producto' ).closest('.form-group').find('.help-block').html('Ingresar producto');
			$( '#txt-No_Producto' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if (bEstadoValidacion) {
      _ID_Producto = '';
      option_impuesto_producto = '';
      
      var obj = JSON.parse(arrImpuestosProducto);
      for (var x = 0; x < obj.arrImpuesto.length; x++){
        var selected = '';
        if ($ID_Impuesto_Cruce_Documento == obj.arrImpuesto[x].ID_Impuesto_Cruce_Documento)
          selected = 'selected="selected"';
        option_impuesto_producto += "<option value='" + obj.arrImpuesto[x].ID_Impuesto_Cruce_Documento + "' data-nu_tipo_impuesto='" + obj.arrImpuesto[x].Nu_Tipo_Impuesto + "' data-impuesto_producto='" + obj.arrImpuesto[x].Ss_Impuesto + "' " + selected + ">" + obj.arrImpuesto[x].No_Impuesto + "</option>";
      }
      
      $Ss_Precio = isNaN($Ss_Precio) ? 0 : $Ss_Precio;

      $Ss_Total_Producto = $Ss_Precio;
      $Ss_SubTotal_Producto = parseFloat($Ss_Precio / $Ss_Impuesto);
			
      var table_detalle_producto =
      "<tr id='tr_detalle_producto" + $ID_Producto + "'>"
        +"<td style='display:none;' class='text-left'>" + $ID_Producto + "</td>"
        +"<td class='text-right'><input type='text' inputmode='decimal' id=" + $ID_Producto + " class='pos-input txt-Qt_Producto form-control input-size_cantidad input-decimal' data-id_producto='" + $ID_Producto + "' value='1' autocomplete='off'></td>"
        +"<td class='text-left'>" + $Nu_Codigo_Barra + " " + $No_Producto + "</td>"
        +"<td class='text-right'><input type='text' inputmode='decimal' class='pos-input txt-Ss_Precio form-control input-decimal input-size_importe' data-id_producto='" + $ID_Producto + "' value='" + $Ss_Precio + "' autocomplete='off'></td>"
        +"<td class='text-right'>"
          +"<select class='cbo-ImpuestosProducto form-control required' style='width: 100%;'>"
            +option_impuesto_producto
          +"</select>"
        +"</td>"
        +"<td style='display:none;' class='text-right'><input type='text' inputmode='decimal' class='pos-input txt-Ss_SubTotal_Producto form-control' value='" + $Ss_SubTotal_Producto.toFixed(2) + "' autocomplete='off' disabled></td>"
        +"<td class='text-right'><input type='text' inputmode='decimal' class='pos-input txt-Ss_Descuento form-control input-decimal input-size_otros' data-id_producto='" + $ID_Producto + "' value='' autocomplete='off'></td>"
        +"<td class='text-right'><input type='text' inputmode='decimal' class='pos-input txt-Ss_Total_Producto form-control input-decimal input-size_importe' data-id_producto='" + $ID_Producto + "' value='" + $Ss_Total_Producto.toFixed(2) + "' autocomplete='off'></td>"
        +"<td style='display:none;' class='text-right td-fDescuentoSinImpuestosItem'>0.00</td>"
        +"<td style='display:none;' class='text-right td-fDescuentoImpuestosItem'>0.00</td>"
        +"<td class='text-center'><button type='button' id='btn-deleteProducto' class='btn btn-sm btn-link' alt='Eliminar' title='Eliminar'><i class='fa fa-trash-o fa-2x' aria-hidden='true'> </i></button></td>"
      + "</tr>";
	    
	    if( isExistTableTemporalProductoOrden($ID_Producto) ){
  	    $( '#txt-No_Producto' ).closest('.form-group').find('.help-block').html('Ya existe producto <b>' + $No_Producto + '</b>');
  			$( '#txt-No_Producto' ).closest('.form-group').removeClass('has-success').addClass('has-error');
  			$( '#txt-No_Producto' ).focus();
  			
  			$( '#txt-ID_Producto' ).val('');
  			$( '#txt-No_Producto' ).val('');
  			$( '#txt-Ss_Precio' ).val('');
	    } else {
			  $( '#txt-ID_Producto' ).val('');
  			$( '#txt-No_Producto' ).val('');
  			$( '#txt-Ss_Precio' ).val('');
  			
	      $( '#table-DetalleProductosOrdenCompra' ).show();
			  $( '#table-DetalleProductosOrdenCompra >tbody' ).append(table_detalle_producto);
			  
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
        $("#table-DetalleProductosOrdenCompra > tbody > tr").each(function(){
          var rows = $(this);
          var fImpuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
          var iGrupoImpuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat(rows.find('td:eq(5) input', this).val());
          var $Ss_Descuento_Producto = parseFloat(rows.find('td:eq(6) input', this).val());
          var $Ss_Total_Producto = parseFloat(rows.find('td:eq(7) input', this).val());
    
          $Ss_Total += $Ss_Total_Producto;
    
          if (iGrupoImpuesto == 1) {
            $Ss_SubTotal += $Ss_SubTotal_Producto;
            $Ss_IGV += $Ss_Total_Producto - $Ss_SubTotal_Producto;
            globalImpuesto = fImpuesto;
          } else if (iGrupoImpuesto == 2) {
            $Ss_Inafecto += $Ss_SubTotal_Producto;
            globalImpuesto += 0;
          } else {
            $Ss_Exonerada += $Ss_SubTotal_Producto;
            globalImpuesto += 0;
          }
            
          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
            
          $Ss_Descuento_p += (($Ss_Descuento_Producto * (parseFloat(rows.find('td:eq(1) input', this).val()) * parseFloat(rows.find('td:eq(3) input', this).val()))) / 100);
        });
        
        if ($Ss_SubTotal > 0.00 || $Ss_Inafecto > 0.00 || $Ss_Exonerada > 0.00) {
          if ($Ss_Descuento > 0.00) {
            var $Ss_Descuento_Gravadas = 0, $Ss_Descuento_Inafecto = 0, $Ss_Descuento_Exonerada = 0;
            if ($Ss_SubTotal > 0.00) {
              $Ss_Descuento_Gravadas = (($Ss_Descuento * $Ss_SubTotal) / 100);
              $Ss_SubTotal = ($Ss_SubTotal - $Ss_Descuento_Gravadas);
              $Ss_IGV = ($Ss_SubTotal * globalImpuesto) - $Ss_SubTotal;
            }
      
            if ($Ss_Inafecto > 0.00) {
              $Ss_Descuento_Inafecto = (($Ss_Descuento * $Ss_Inafecto) / 100);
              $Ss_Inafecto = ($Ss_Inafecto - $Ss_Descuento_Inafecto);
            }
            
            if ($Ss_Exonerada > 0.00) {
              $Ss_Descuento_Exonerada = (($Ss_Descuento * $Ss_Exonerada) / 100);
              $Ss_Exonerada = ($Ss_Exonerada - $Ss_Descuento_Exonerada);
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
	})

  $('#table-DetalleProductosOrdenCompra tbody' ).on('input', '.txt-Ss_Precio', function(){
    var fila = $(this).parents("tr");
    var $ID_Producto = fila.find(".txt-Ss_Precio").data('id_producto');
    var precio = fila.find(".txt-Ss_Precio").val();
    var cantidad = fila.find(".txt-Qt_Producto").val();
    var subtotal_producto = fila.find(".txt-Ss_SubTotal_Producto").val();
    var impuesto_producto = fila.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto');
    var nu_tipo_impuesto = fila.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
    var descuento = fila.find(".txt-Ss_Descuento").val();
    var total_producto = fila.find(".txt-Ss_Total_Producto").val();
    var fDescuento_SubTotal_Producto = 0, fDescuento_Total_Producto = 0;
    
    if ( parseFloat(precio) > 0.00 && parseFloat(cantidad) > 0){
      $('#tr_detalle_producto' + $ID_Producto).removeClass('danger');
	    $( '#table-DetalleProductosOrdenCompra tfoot' ).empty();
      if (nu_tipo_impuesto == 1) {//CON IGV
        fDescuento_SubTotal_Producto = parseFloat(((precio * cantidad) - (((descuento * (precio * cantidad)) / 100))) / impuesto_producto);
        fDescuento_Total_Producto = parseFloat(((precio * cantidad) - (((descuento * (precio * cantidad)) / 100))));
        fila.find(".td-fDescuentoSinImpuestosItem").text((parseFloat((((descuento * (precio * cantidad)) / 100) / impuesto_producto)).toFixed(2)).toString().split(". "));
        fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100) - (((descuento * (precio * cantidad)) / 100) / impuesto_producto)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat(fDescuento_SubTotal_Producto).toFixed(6)).toString().split(". "));
        fila.find(".txt-Ss_Total_Producto").val((parseFloat(fDescuento_Total_Producto).toFixed(2)).toString().split(". "));
        
        var $Ss_SubTotal = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_IGV = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleProductosOrdenCompra > tbody > tr").each(function(){
          var rows = $(this);
          var Ss_Impuesto           = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
          var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat(rows.find('td:eq(5) input', this).val());
          var $Ss_Descuento_Producto = parseFloat(rows.find('td:eq(6) input', this).val());
          var $Ss_Total_Producto = parseFloat(rows.find('td:eq(7) input', this).val());
    
          $Ss_Total += $Ss_Total_Producto;

          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
            
          if (Nu_Tipo_Impuesto == 1){
            $Ss_SubTotal += $Ss_SubTotal_Producto;
            $Ss_IGV += $Ss_Total_Producto - $Ss_SubTotal_Producto;
          }
          
          $Ss_Descuento += (($Ss_Descuento_Producto * (parseFloat(rows.find('td:eq(1) input', this).val()) * parseFloat(rows.find('td:eq(3) input', this).val()))) / 100);
        });
        $( '#txt-subTotal' ).val( $Ss_SubTotal.toFixed(2) );
    		$( '#span-subTotal' ).text( $Ss_SubTotal.toFixed(2) );
    		
    		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
    		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
    		
    		$( '#txt-impuesto' ).val( $Ss_IGV.toFixed(2) );
    		$( '#span-impuesto' ).text( $Ss_IGV.toFixed(2) );
    		
    		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
    		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
      } else if (nu_tipo_impuesto == 2) {//Inafecto
        fila.find(".td-fDescuentoSinImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_Total_Producto").val((parseFloat(((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)) * impuesto_producto).toFixed(2)).toString().split(". "));
        
        var $Ss_Inafecto = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_IGV = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleProductosOrdenCompra > tbody > tr").each(function(){
          var rows = $(this);
          var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat(rows.find('td:eq(5) input', this).val());
          var $Ss_Descuento_Producto = parseFloat(rows.find('td:eq(6) input', this).val());

          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
            
          if (Nu_Tipo_Impuesto == 2)
            $Ss_Inafecto += $Ss_SubTotal_Producto;
          
          $Ss_Descuento += (($Ss_Descuento_Producto * (parseFloat(rows.find('td:eq(1) input', this).val()) * parseFloat(rows.find('td:eq(3) input', this).val()))) / 100);
          $Ss_Total += parseFloat(rows.find('td:eq(7) input', this).val());
        });
        
        $( '#txt-inafecto' ).val( $Ss_Inafecto.toFixed(2) );
    		$( '#span-inafecto' ).text( $Ss_Inafecto.toFixed(2) );
    		
    		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
    		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
    		
    		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
    		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
      } else if (nu_tipo_impuesto == 3) {//Exonerada
        fila.find(".td-fDescuentoSinImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_Total_Producto").val((parseFloat(((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)) * impuesto_producto).toFixed(2)).toString().split(". "));
        
        var $Ss_Exonerada = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleProductosOrdenCompra > tbody > tr").each(function(){
          var rows = $(this);
          var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat(rows.find('td:eq(5) input', this).val());
          var $Ss_Descuento_Producto = parseFloat(rows.find('td:eq(6) input', this).val());

          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
            
          if (Nu_Tipo_Impuesto == 3)
            $Ss_Exonerada += $Ss_SubTotal_Producto;
          
          $Ss_Descuento += (($Ss_Descuento_Producto * (parseFloat(rows.find('td:eq(1) input', this).val()) * parseFloat(rows.find('td:eq(3) input', this).val()))) / 100);
          $Ss_Total += parseFloat(rows.find('td:eq(7) input', this).val());
        });
        
        $( '#txt-exonerada' ).val( $Ss_Exonerada.toFixed(2) );
    		$( '#span-exonerada' ).text( $Ss_Exonerada.toFixed(2) );
    		
    		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
    		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
    		
    		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
    		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
      } else if (nu_tipo_impuesto == 4) {//Gratuita
        fila.find(".td-fDescuentoSinImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_Total_Producto").val((parseFloat(((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)) * impuesto_producto).toFixed(2)).toString().split(". "));
        
        var $Ss_Gratuita = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleProductosOrdenCompra > tbody > tr").each(function(){
          var rows = $(this);
          var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat(rows.find('td:eq(5) input', this).val());
          var $Ss_Descuento_Producto = parseFloat(rows.find('td:eq(6) input', this).val());

          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
            
          if (Nu_Tipo_Impuesto == 4)
            $Ss_Gratuita += $Ss_SubTotal_Producto;
          
          $Ss_Descuento += (($Ss_Descuento_Producto * (parseFloat(rows.find('td:eq(1) input', this).val()) * parseFloat(rows.find('td:eq(3) input', this).val()))) / 100);
          $Ss_Total += parseFloat(rows.find('td:eq(7) input', this).val());
        });
        
        $( '#txt-gratuita' ).val( $Ss_Gratuita.toFixed(2) );
    		$( '#span-gratuita' ).text( $Ss_Gratuita.toFixed(2) );
    		
    		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
    		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
    		
    		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
    		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
  		}
    }
  })
	
  $('#table-DetalleProductosOrdenCompra tbody' ).on('input', '.txt-Qt_Producto', function(){
    var fila = $(this).parents("tr");
    var $ID_Producto = fila.find(".txt-Ss_Precio").data('id_producto');
    var precio = fila.find(".txt-Ss_Precio").val();
    var cantidad = fila.find(".txt-Qt_Producto").val();
    var subtotal_producto = fila.find(".txt-Ss_SubTotal_Producto").val();
    var impuesto_producto = fila.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto');
    var nu_tipo_impuesto = fila.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
    var descuento = fila.find(".txt-Ss_Descuento").val();
    var total_producto = fila.find(".txt-Ss_Total_Producto").val();
    var fDescuento_SubTotal_Producto = 0, fDescuento_Total_Producto = 0;
    
    if ( parseFloat(precio) > 0.00 && parseFloat(cantidad) > 0){
      $('#tr_detalle_producto' + $ID_Producto).removeClass('danger');
      $( '#table-DetalleProductosOrdenCompra tfoot' ).empty();
      if (nu_tipo_impuesto == 1) {//CON IGV
        fDescuento_SubTotal_Producto = parseFloat(((precio * cantidad) - (((descuento * (precio * cantidad)) / 100))) / impuesto_producto);
        fDescuento_Total_Producto = parseFloat(((precio * cantidad) - (((descuento * (precio * cantidad)) / 100))));
        fila.find(".td-fDescuentoSinImpuestosItem").text((parseFloat((((descuento * (precio * cantidad)) / 100) / impuesto_producto)).toFixed(2)).toString().split(". "));
        fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100) - (((descuento * (precio * cantidad)) / 100) / impuesto_producto)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat(fDescuento_SubTotal_Producto).toFixed(6)).toString().split(". "));
        fila.find(".txt-Ss_Total_Producto").val((parseFloat(fDescuento_Total_Producto).toFixed(2)).toString().split(". "));
        
        var $Ss_SubTotal = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_IGV = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleProductosOrdenCompra > tbody > tr").each(function(){
          var rows = $(this);
          var Ss_Impuesto           = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
          var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat(rows.find('td:eq(5) input', this).val());
          var $Ss_Descuento_Producto = parseFloat(rows.find('td:eq(6) input', this).val());
          var $Ss_Total_Producto = parseFloat(rows.find('td:eq(7) input', this).val());
    
          $Ss_Total += $Ss_Total_Producto;

          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
            
          if (Nu_Tipo_Impuesto == 1){
            $Ss_SubTotal += $Ss_SubTotal_Producto;
            $Ss_IGV += $Ss_Total_Producto - $Ss_SubTotal_Producto;
          }
          
          $Ss_Descuento += (($Ss_Descuento_Producto * (parseFloat(rows.find('td:eq(1) input', this).val()) * parseFloat(rows.find('td:eq(3) input', this).val()))) / 100);
        });
        $( '#txt-subTotal' ).val( $Ss_SubTotal.toFixed(2) );
    		$( '#span-subTotal' ).text( $Ss_SubTotal.toFixed(2) );
    		
    		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
    		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
    		
    		$( '#txt-impuesto' ).val( $Ss_IGV.toFixed(2) );
    		$( '#span-impuesto' ).text( $Ss_IGV.toFixed(2) );
    		
    		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
    		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
      } else if (nu_tipo_impuesto == 2) {//Inafecto
        fila.find(".td-fDescuentoSinImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_Total_Producto").val((parseFloat(((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)) * impuesto_producto).toFixed(2)).toString().split(". "));

        var $Ss_Inafecto = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_Total = 0.00;
        
        $("#table-DetalleProductosOrdenCompra > tbody > tr").each(function(){
          var rows = $(this);
          var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat(rows.find('td:eq(5) input', this).val());
          var $Ss_Descuento_Producto = parseFloat(rows.find('td:eq(6) input', this).val());

          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
            
          if (Nu_Tipo_Impuesto == 2)
            $Ss_Inafecto += $Ss_SubTotal_Producto;
          
          $Ss_Descuento += (($Ss_Descuento_Producto * (parseFloat(rows.find('td:eq(1) input', this).val()) * parseFloat(rows.find('td:eq(3) input', this).val()))) / 100);
          $Ss_Total += parseFloat(rows.find('td:eq(7) input', this).val());
        });
        
        $( '#txt-inafecto' ).val( $Ss_Inafecto.toFixed(2) );
    		$( '#span-inafecto' ).text( $Ss_Inafecto.toFixed(2) );
    		
    		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
    		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
    		
    		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
    		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
      } else if (nu_tipo_impuesto == 3) {//Exonerada
        fila.find(".td-fDescuentoSinImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_Total_Producto").val((parseFloat(((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)) * impuesto_producto).toFixed(2)).toString().split(". "));
        
        var $Ss_Exonerada = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleProductosOrdenCompra > tbody > tr").each(function(){
          var rows = $(this);
          var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat(rows.find('td:eq(5) input', this).val());
          var $Ss_Descuento_Producto = parseFloat(rows.find('td:eq(6) input', this).val());

          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
            
          if (Nu_Tipo_Impuesto == 3)
            $Ss_Exonerada += $Ss_SubTotal_Producto;
          
          $Ss_Descuento += (($Ss_Descuento_Producto * (parseFloat(rows.find('td:eq(1) input', this).val()) * parseFloat(rows.find('td:eq(3) input', this).val()))) / 100);
          $Ss_Total += parseFloat(rows.find('td:eq(7) input', this).val());
        });
        
        $( '#txt-exonerada' ).val( $Ss_Exonerada.toFixed(2) );
    		$( '#span-exonerada' ).text( $Ss_Exonerada.toFixed(2) );
    		
    		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
    		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
    		
    		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
    		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
      } else if (nu_tipo_impuesto == 4) {//Gratuita
        fila.find(".td-fDescuentoSinImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_Total_Producto").val((parseFloat(((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)) * impuesto_producto).toFixed(2)).toString().split(". "));
        
        var $Ss_Gratuita = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleProductosOrdenCompra > tbody > tr").each(function(){
          var rows = $(this);
          var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat(rows.find('td:eq(5) input', this).val());
          var $Ss_Descuento_Producto = parseFloat(rows.find('td:eq(6) input', this).val());

          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
            
          if (Nu_Tipo_Impuesto == 4)
            $Ss_Gratuita += $Ss_SubTotal_Producto;
          
          $Ss_Descuento += (($Ss_Descuento_Producto * (parseFloat(rows.find('td:eq(1) input', this).val()) * parseFloat(rows.find('td:eq(3) input', this).val()))) / 100);
          $Ss_Total += parseFloat(rows.find('td:eq(7) input', this).val());
        });
        
        $( '#txt-gratuita' ).val( $Ss_Gratuita.toFixed(2) );
    		$( '#span-gratuita' ).text( $Ss_Gratuita.toFixed(2) );
    		
    		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
    		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
    		
    		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
    		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
  		}
    }
  })

  $('#table-DetalleProductosOrdenCompra tbody' ).on('change', '.cbo-ImpuestosProducto', function(){
    var fila = $(this).parents("tr");
    var precio = fila.find(".txt-Ss_Precio").val();
    var cantidad = fila.find(".txt-Qt_Producto").val();
    var subtotal_producto = fila.find(".txt-Ss_SubTotal_Producto").val();
    var impuesto_producto = fila.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto');
    var nu_tipo_impuesto = fila.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
    var total_producto = fila.find(".txt-Ss_Total_Producto").val();
    var descuento = fila.find(".txt-Ss_Descuento").val();
    var fDescuento_SubTotal_Producto = 0, fDescuento_Total_Producto = 0;

    if (considerar_igv == 1) {//SI IGV
      if ( parseFloat(precio) > 0.00 && parseFloat(cantidad) > 0 && parseFloat(total_producto) > 0){
    		if (nu_tipo_impuesto == 1){//CON IGV
          fila.find(".td-fDescuentoSinImpuestosItem").text( (parseFloat(((descuento * (subtotal_producto)) / 100)).toFixed(2)).toString().split(". ") );
          fila.find(".td-fDescuentoImpuestosItem").text( (parseFloat(((descuento * (subtotal_producto * impuesto_producto)) / 100) - ((descuento * (subtotal_producto)) / 100)).toFixed(2)).toString().split(". ") );
    		  fila.find(".txt-Ss_Precio").val( (parseFloat(subtotal_producto * impuesto_producto).toFixed(6)).toString().split(". ") );
    		  fila.find(".txt-Ss_Total_Producto").val( (parseFloat(subtotal_producto * impuesto_producto).toFixed(2)).toString().split(". ") );
    		  
          var $Ss_SubTotal = 0.00;
          var $Ss_Exonerada = 0.00;
          var $Ss_Inafecto = 0.00;
          var $Ss_Gratuita = 0.00;
          var $Ss_Descuento = 0.00;
          var $Ss_IGV = 0.00;
          var $Ss_Total = 0.00;
          $("#table-DetalleProductosOrdenCompra > tbody > tr").each(function(){
            var rows = $(this);
            var Ss_Impuesto           = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
            var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
            var $Ss_SubTotal_Producto = parseFloat(rows.find('td:eq(5) input', this).val());
            var $Ss_Descuento_Producto = parseFloat(rows.find('td:eq(6) input', this).val());
            var $Ss_Total_Producto = parseFloat(rows.find('td:eq(7) input', this).val());
      
            $Ss_Total += $Ss_Total_Producto;
  
            if(isNaN($Ss_Descuento_Producto))
              $Ss_Descuento_Producto = 0;
            
            if (Nu_Tipo_Impuesto == 1){
              $Ss_SubTotal += $Ss_SubTotal_Producto;
              $Ss_IGV += $Ss_Total_Producto - $Ss_SubTotal_Producto;
            } else if (Nu_Tipo_Impuesto == 2) {
              $Ss_Inafecto += $Ss_SubTotal_Producto;
            } else if (Nu_Tipo_Impuesto == 3) {
              $Ss_Exonerada += $Ss_SubTotal_Producto;
            } else if (Nu_Tipo_Impuesto == 4) {
              $Ss_Gratuita += $Ss_SubTotal_Producto;
            }
            
            $Ss_Descuento += (($Ss_Descuento_Producto * (parseFloat(rows.find('td:eq(1) input', this).val()) * parseFloat(rows.find('td:eq(3) input', this).val()))) / 100);
          });
          
          $( '#txt-subTotal' ).val( $Ss_SubTotal.toFixed(2) );
      		$( '#span-subTotal' ).text( $Ss_SubTotal.toFixed(2) );
      		
          $( '#txt-exonerada' ).val( $Ss_Exonerada.toFixed(2) );
      		$( '#span-exonerada' ).text( $Ss_Exonerada.toFixed(2) );
      		
          $( '#txt-inafecto' ).val( $Ss_Inafecto.toFixed(2) );
      		$( '#span-inafecto' ).text( $Ss_Inafecto.toFixed(2) );
      		
          $( '#txt-gratuita' ).val( $Ss_Gratuita.toFixed(2) );
      		$( '#span-gratuita' ).text( $Ss_Gratuita.toFixed(2) );
      		
      		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
      		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
      		
      		$( '#txt-impuesto' ).val( $Ss_IGV.toFixed(2) );
      		$( '#span-impuesto' ).text( $Ss_IGV.toFixed(2) );
      		
      		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
      		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
    		} else if (nu_tipo_impuesto == 2){//Inafecto
          fila.find(".td-fDescuentoSinImpuestosItem").text( (parseFloat(((descuento * (subtotal_producto)) / 100)).toFixed(2)).toString().split(". ") );
          fila.find(".td-fDescuentoImpuestosItem").text( (parseFloat(((descuento * (subtotal_producto * impuesto_producto)) / 100) - ((descuento * (subtotal_producto)) / 100)).toFixed(2)).toString().split(". ") );
    		  fila.find(".txt-Ss_Precio").val( (parseFloat(subtotal_producto * impuesto_producto).toFixed(6)).toString().split(". ") );
          fila.find(".txt-Ss_Total_Producto").val((parseFloat(subtotal_producto * impuesto_producto).toFixed(2)).toString().split(". "));

          fila.find(".txt-Ss_SubTotal_Producto").val(parseFloat(subtotal_producto).toFixed(2));
    		  
          var $Ss_SubTotal = 0.00;
          var $Ss_Exonerada = 0.00;
          var $Ss_Inafecto = 0.00;
          var $Ss_Gratuita = 0.00;
          var $Ss_IGV = 0.00;
          var $Ss_Descuento = 0.00;
          var $Ss_Total = 0.00;
          $("#table-DetalleProductosOrdenCompra > tbody > tr").each(function(){
            var rows = $(this);
            var Ss_Impuesto           = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
            var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
            var $Ss_SubTotal_Producto = parseFloat(rows.find('td:eq(5) input', this).val());
            var $Ss_Descuento_Producto = parseFloat(rows.find('td:eq(6) input', this).val());
            var $Ss_Total_Producto = parseFloat(rows.find('td:eq(7) input', this).val());
      
            $Ss_Total += $Ss_Total_Producto;
  
            if(isNaN($Ss_Descuento_Producto))
              $Ss_Descuento_Producto = 0;
            
            if (Nu_Tipo_Impuesto == 1){
              $Ss_SubTotal += $Ss_SubTotal_Producto;
              $Ss_IGV += $Ss_Total_Producto - $Ss_SubTotal_Producto;
            } else if (Nu_Tipo_Impuesto == 2) {
              $Ss_Inafecto += $Ss_SubTotal_Producto;
            } else if (Nu_Tipo_Impuesto == 3) {
              $Ss_Exonerada += $Ss_SubTotal_Producto;
            } else if (Nu_Tipo_Impuesto == 4) {
              $Ss_Gratuita += $Ss_SubTotal_Producto;
            }
            
            $Ss_Descuento += (($Ss_Descuento_Producto * (parseFloat(rows.find('td:eq(1) input', this).val()) * parseFloat(rows.find('td:eq(3) input', this).val()))) / 100);
          });
          
      		$( '#txt-subTotal' ).val( $Ss_SubTotal.toFixed(2) );
      		$( '#span-subTotal' ).text( $Ss_SubTotal.toFixed(2) );
      		
          $( '#txt-exonerada' ).val( $Ss_Exonerada.toFixed(2) );
      		$( '#span-exonerada' ).text( $Ss_Exonerada.toFixed(2) );
      		
          $( '#txt-inafecto' ).val( $Ss_Inafecto.toFixed(2) );
      		$( '#span-inafecto' ).text( $Ss_Inafecto.toFixed(2) );
      		
          $( '#txt-gratuita' ).val( $Ss_Gratuita.toFixed(2) );
      		$( '#span-gratuita' ).text( $Ss_Gratuita.toFixed(2) );
      		
      		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
      		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
      		
      		$( '#txt-impuesto' ).val( $Ss_IGV.toFixed(2) );
      		$( '#span-impuesto' ).text( $Ss_IGV.toFixed(2) );
      		
      		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
      		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
    		} else if (nu_tipo_impuesto == 3){//Exonerada
          fila.find(".td-fDescuentoSinImpuestosItem").text( (parseFloat(((descuento * (subtotal_producto)) / 100)).toFixed(2)).toString().split(". ") );
          fila.find(".td-fDescuentoImpuestosItem").text( (parseFloat(((descuento * (subtotal_producto * impuesto_producto)) / 100) - ((descuento * (subtotal_producto)) / 100)).toFixed(2)).toString().split(". ") );
    		  fila.find(".txt-Ss_Precio").val( (parseFloat(subtotal_producto * impuesto_producto).toFixed(6)).toString().split(". ") );
          fila.find(".txt-Ss_Total_Producto").val((parseFloat(subtotal_producto * impuesto_producto).toFixed(2)).toString().split(". "));

          fila.find(".txt-Ss_SubTotal_Producto").val(parseFloat(subtotal_producto).toFixed(2));
    		  
          var $Ss_SubTotal = 0.00;
          var $Ss_Exonerada = 0.00;
          var $Ss_Inafecto = 0.00;
          var $Ss_Gratuita = 0.00;
          var $Ss_IGV = 0.00;
          var $Ss_Descuento = 0.00;
          var $Ss_Total = 0.00;
          $("#table-DetalleProductosOrdenCompra > tbody > tr").each(function(){
            var rows = $(this);
            var Ss_Impuesto           = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
            var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
            var $Ss_SubTotal_Producto = parseFloat(rows.find('td:eq(5) input', this).val());
            var $Ss_Descuento_Producto = parseFloat(rows.find('td:eq(6) input', this).val());
            var $Ss_Total_Producto = parseFloat(rows.find('td:eq(7) input', this).val());
      
            $Ss_Total += $Ss_Total_Producto;
    
            if(isNaN($Ss_Descuento_Producto))
              $Ss_Descuento_Producto = 0;
            
            if (Nu_Tipo_Impuesto == 1){
              $Ss_SubTotal += $Ss_SubTotal_Producto;
              $Ss_IGV += $Ss_Total_Producto - $Ss_SubTotal_Producto;
            } else if (Nu_Tipo_Impuesto == 2) {
              $Ss_Inafecto += $Ss_SubTotal_Producto;
            } else if (Nu_Tipo_Impuesto == 3) {
              $Ss_Exonerada += $Ss_SubTotal_Producto;
            } else if (Nu_Tipo_Impuesto == 4) {
              $Ss_Gratuita += $Ss_SubTotal_Producto;
            }
            
            $Ss_Descuento += (($Ss_Descuento_Producto * (parseFloat(rows.find('td:eq(1) input', this).val()) * parseFloat(rows.find('td:eq(3) input', this).val()))) / 100);
          });
          
      		$( '#txt-subTotal' ).val( $Ss_SubTotal.toFixed(2) );
      		$( '#span-subTotal' ).text( $Ss_SubTotal.toFixed(2) );
      		
          $( '#txt-exonerada' ).val( $Ss_Exonerada.toFixed(2) );
      		$( '#span-exonerada' ).text( $Ss_Exonerada.toFixed(2) );
      		
          $( '#txt-inafecto' ).val( $Ss_Inafecto.toFixed(2) );
      		$( '#span-inafecto' ).text( $Ss_Inafecto.toFixed(2) );
      		
          $( '#txt-gratuita' ).val( $Ss_Gratuita.toFixed(2) );
      		$( '#span-gratuita' ).text( $Ss_Gratuita.toFixed(2) );
      		
      		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
      		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
      		
      		$( '#txt-impuesto' ).val( $Ss_IGV.toFixed(2) );
      		$( '#span-impuesto' ).text( $Ss_IGV.toFixed(2) );
      		
      		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
      		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
    		} else if (nu_tipo_impuesto == 4){//Gratuita
          fila.find(".td-fDescuentoSinImpuestosItem").text( (parseFloat(((descuento * (subtotal_producto)) / 100)).toFixed(2)).toString().split(". ") );
          fila.find(".td-fDescuentoImpuestosItem").text( (parseFloat(((descuento * (subtotal_producto * impuesto_producto)) / 100) - ((descuento * (subtotal_producto)) / 100)).toFixed(2)).toString().split(". ") );
    		  fila.find(".txt-Ss_Precio").val( (parseFloat(subtotal_producto * impuesto_producto).toFixed(6)).toString().split(". ") );
          fila.find(".txt-Ss_Total_Producto").val((parseFloat(subtotal_producto * impuesto_producto).toFixed(2)).toString().split(". "));

          fila.find(".txt-Ss_SubTotal_Producto").val(parseFloat(subtotal_producto).toFixed(2));
    		  
          var $Ss_SubTotal = 0.00;
          var $Ss_Exonerada = 0.00;
          var $Ss_Inafecto = 0.00;
          var $Ss_Gratuita = 0.00;
          var $Ss_IGV = 0.00;
          var $Ss_Descuento = 0.00;
          var $Ss_Total = 0.00;
          $("#table-DetalleProductosOrdenCompra > tbody > tr").each(function(){
            var rows = $(this);
            var Ss_Impuesto           = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
            var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
            var $Ss_SubTotal_Producto = parseFloat(rows.find('td:eq(5) input', this).val());
            var $Ss_Descuento_Producto = parseFloat(rows.find('td:eq(6) input', this).val());
            var $Ss_Total_Producto = parseFloat(rows.find('td:eq(7) input', this).val());
      
            $Ss_Total += $Ss_Total_Producto;
    
            if(isNaN($Ss_Descuento_Producto))
              $Ss_Descuento_Producto = 0;
            
            if (Nu_Tipo_Impuesto == 1){
              $Ss_SubTotal += $Ss_SubTotal_Producto;
              $Ss_IGV += $Ss_Total_Producto - $Ss_SubTotal_Producto;
            } else if (Nu_Tipo_Impuesto == 2) {
              $Ss_Inafecto += $Ss_SubTotal_Producto;
            } else if (Nu_Tipo_Impuesto == 3) {
              $Ss_Exonerada += $Ss_SubTotal_Producto;
            } else if (Nu_Tipo_Impuesto == 4) {
              $Ss_Gratuita += $Ss_SubTotal_Producto;
            }
            
            $Ss_Descuento += (($Ss_Descuento_Producto * (parseFloat(rows.find('td:eq(1) input', this).val()) * parseFloat(rows.find('td:eq(3) input', this).val()))) / 100);
          });
          
      		$( '#txt-subTotal' ).val( $Ss_SubTotal.toFixed(2) );
      		$( '#span-subTotal' ).text( $Ss_SubTotal.toFixed(2) );
      		
          $( '#txt-exonerada' ).val( $Ss_Exonerada.toFixed(2) );
      		$( '#span-exonerada' ).text( $Ss_Exonerada.toFixed(2) );
      		
          $( '#txt-inafecto' ).val( $Ss_Inafecto.toFixed(2) );
      		$( '#span-inafecto' ).text( $Ss_Inafecto.toFixed(2) );
      		
          $( '#txt-gratuita' ).val( $Ss_Gratuita.toFixed(2) );
      		$( '#span-gratuita' ).text( $Ss_Gratuita.toFixed(2) );
      		
      		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
      		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
      		
      		$( '#txt-impuesto' ).val( $Ss_IGV.toFixed(2) );
      		$( '#span-impuesto' ).text( $Ss_IGV.toFixed(2) );
      		
      		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
      		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
    		}
      }
    }
  })
  
  $('#table-DetalleProductosOrdenCompra tbody' ).on('input', '.txt-Ss_Descuento', function(){
    var fila = $(this).parents("tr");
    var $ID_Producto = fila.find(".txt-Ss_Precio").data('id_producto');
    var precio = fila.find(".txt-Ss_Precio").val();
    var cantidad = fila.find(".txt-Qt_Producto").val();
    var subtotal_producto = fila.find(".txt-Ss_SubTotal_Producto").val();
    var impuesto_producto = fila.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto');
    var nu_tipo_impuesto = fila.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
    var descuento = fila.find(".txt-Ss_Descuento").val();
    var total_producto = fila.find(".txt-Ss_Total_Producto").val();
    var fDescuento_SubTotal_Producto = 0, fDescuento_Total_Producto = 0;
          
    if ( parseFloat(precio) > 0.00 && parseFloat(cantidad) > 0 && parseFloat(descuento) >= 0 && parseFloat(total_producto) > 0 && (parseFloat($( '#txt-Ss_Descuento' ).val()) == 0 || $( '#txt-Ss_Descuento' ).val() == '')){
      if ( parseFloat(subtotal_producto) >= parseFloat(((subtotal_producto * descuento) / 100)) ){
        if (nu_tipo_impuesto == 1) {//CON IGV
          fDescuento_SubTotal_Producto = parseFloat(((precio * cantidad) - (((descuento * (precio * cantidad)) / 100))) / impuesto_producto);
          fDescuento_Total_Producto = parseFloat(((precio * cantidad) - (((descuento * (precio * cantidad)) / 100))));
          fila.find(".td-fDescuentoSinImpuestosItem").text((parseFloat((((descuento * (precio * cantidad)) / 100) / impuesto_producto)).toFixed(2)).toString().split(". "));
          fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100) - (((descuento * (precio * cantidad)) / 100) / impuesto_producto)).toFixed(2)).toString().split(". "));
          fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat(fDescuento_SubTotal_Producto).toFixed(6)).toString().split(". "));
          fila.find(".txt-Ss_Total_Producto").val((parseFloat(fDescuento_Total_Producto).toFixed(2)).toString().split(". "));
          
          var $Ss_SubTotal = 0.00;
          var $Ss_Descuento = 0.00;
          var $Ss_IGV = 0.00;
          var $Ss_Total = 0.00;
          $("#table-DetalleProductosOrdenCompra > tbody > tr").each(function(){
            var rows = $(this);
            var Ss_Impuesto           = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
            var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
            var $Ss_SubTotal_Producto = parseFloat(rows.find('td:eq(5) input', this).val());
            var $Ss_Descuento_Producto = parseFloat(rows.find('td:eq(6) input', this).val());
            var $Ss_Total_Producto = parseFloat(rows.find('td:eq(7) input', this).val());
      
            $Ss_Total += $Ss_Total_Producto;
            
            if(isNaN($Ss_Descuento_Producto))
              $Ss_Descuento_Producto = 0;
              
            if (Nu_Tipo_Impuesto == 1){
              $Ss_SubTotal += $Ss_SubTotal_Producto;
              $Ss_IGV += $Ss_Total_Producto - $Ss_SubTotal_Producto;
            }
            
            $Ss_Descuento += (($Ss_Descuento_Producto * (parseFloat(rows.find('td:eq(1) input', this).val()) * parseFloat(rows.find('td:eq(3) input', this).val()))) / 100);
          });
          
          $( '#txt-subTotal' ).val( $Ss_SubTotal.toFixed(2) );
      		$( '#span-subTotal' ).text( $Ss_SubTotal.toFixed(2) );
      		
      		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
      		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
      		
      		$( '#txt-impuesto' ).val( $Ss_IGV.toFixed(2) );
      		$( '#span-impuesto' ).text( $Ss_IGV.toFixed(2) );
      		
      		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
      		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
        } else if (nu_tipo_impuesto == 2) {//Inafecto
          fila.find(".td-fDescuentoSinImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
          fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
          fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
          fila.find(".txt-Ss_Total_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));

          var $Ss_Inafecto = 0.00;
          var $Ss_Descuento = 0.00;
          var $Ss_Total = 0.00;
          $("#table-DetalleProductosOrdenCompra > tbody > tr").each(function(){
            var rows = $(this);
            var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
            var $Ss_SubTotal_Producto = parseFloat(rows.find('td:eq(5) input', this).val());
            var $Ss_Descuento_Producto = parseFloat(rows.find('td:eq(6) input', this).val());
  
            if(isNaN($Ss_Descuento_Producto))
              $Ss_Descuento_Producto = 0;
              
            if (Nu_Tipo_Impuesto == 2)
              $Ss_Inafecto += $Ss_SubTotal_Producto;
            
            $Ss_Descuento += (($Ss_Descuento_Producto * (parseFloat(rows.find('td:eq(1) input', this).val()) * parseFloat(rows.find('td:eq(3) input', this).val()))) / 100);
            $Ss_Total += parseFloat(rows.find('td:eq(7) input', this).val());
          });
          
          $( '#txt-inafecto' ).val( $Ss_Inafecto.toFixed(2) );
      		$( '#span-inafecto' ).text( $Ss_Inafecto.toFixed(2) );
      		
      		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
      		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
      		
      		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
      		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
        } else if (nu_tipo_impuesto == 3) {//Exonerada
          fila.find(".td-fDescuentoSinImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
          fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
          fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
          fila.find(".txt-Ss_Total_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));

          var $Ss_Exonerada = 0.00;
          var $Ss_Descuento = 0.00;
          var $Ss_Total = 0.00;
          $("#table-DetalleProductosOrdenCompra > tbody > tr").each(function(){
            var rows = $(this);
            var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
            var $Ss_SubTotal_Producto = parseFloat(rows.find('td:eq(5) input', this).val());
            var $Ss_Descuento_Producto = parseFloat(rows.find('td:eq(6) input', this).val());
  
            if(isNaN($Ss_Descuento_Producto))
              $Ss_Descuento_Producto = 0;
              
            if (Nu_Tipo_Impuesto == 3)
              $Ss_Exonerada += $Ss_SubTotal_Producto;
            
            $Ss_Descuento += (($Ss_Descuento_Producto * (parseFloat(rows.find('td:eq(1) input', this).val()) * parseFloat(rows.find('td:eq(3) input', this).val()))) / 100);
            $Ss_Total += parseFloat(rows.find('td:eq(7) input', this).val());
          });
          
          $( '#txt-exonerada' ).val( $Ss_Exonerada.toFixed(2) );
      		$( '#span-exonerada' ).text( $Ss_Exonerada.toFixed(2) );
      		
      		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
      		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
      		
      		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
      		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
        } else if (nu_tipo_impuesto == 4) {//Gratuita
          fila.find(".td-fDescuentoSinImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
          fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
          fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
          fila.find(".txt-Ss_Total_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));

          var $Ss_Gratuita = 0.00;
          var $Ss_Descuento = 0.00;
          var $Ss_Total = 0.00;
          $("#table-DetalleProductosOrdenCompra > tbody > tr").each(function(){
            var rows = $(this);
            var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
            var $Ss_SubTotal_Producto = parseFloat(rows.find('td:eq(5) input', this).val());
            var $Ss_Descuento_Producto = parseFloat(rows.find('td:eq(6) input', this).val());
  
            if(isNaN($Ss_Descuento_Producto))
              $Ss_Descuento_Producto = 0;
              
            if (Nu_Tipo_Impuesto == 4)
              $Ss_Gratuita += $Ss_SubTotal_Producto;
            
            $Ss_Descuento += (($Ss_Descuento_Producto * (parseFloat(rows.find('td:eq(1) input', this).val()) * parseFloat(rows.find('td:eq(3) input', this).val()))) / 100);
            $Ss_Total += parseFloat(rows.find('td:eq(7) input', this).val());
          });
          
          $( '#txt-gratuita' ).val( $Ss_Gratuita.toFixed(2) );
      		$( '#span-gratuita' ).text( $Ss_Gratuita.toFixed(2) );
      		
      		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
      		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
      		
      		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
      		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
    		}
      }
    }
  })

  $('#table-DetalleProductosOrdenCompra tbody' ).on('input', '.txt-Ss_Total_Producto', function(){
    var fila = $(this).parents("tr");
    var $ID_Producto = fila.find(".txt-Ss_Precio").data('id_producto');
    var precio = fila.find(".txt-Ss_Precio").val();
    var cantidad = fila.find(".txt-Qt_Producto").val();
    var subtotal_producto = fila.find(".txt-Ss_SubTotal_Producto").val();
    var impuesto_producto = fila.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto');
    var nu_tipo_impuesto = fila.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
    var descuento = fila.find(".txt-Ss_Descuento").val();
    var total_producto = fila.find(".txt-Ss_Total_Producto").val();
    
    if ( parseFloat(precio) > 0.00 && parseFloat(cantidad) > 0 && parseFloat(total_producto) > 0){
      $('#tr_detalle_producto' + $ID_Producto).removeClass('danger');
      $( '#table-DetalleProductosOrdenCompra tfoot' ).empty();
      if (nu_tipo_impuesto == 1) {//CON IGV
        fila.find(".txt-Ss_Precio").val((parseFloat(total_producto / cantidad).toFixed(6)).toString().split(". "));
        fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat(total_producto / impuesto_producto).toFixed(6)).toString().split(". "));
        
        var $Ss_SubTotal = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_IGV = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleProductosOrdenCompra > tbody > tr").each(function(){
          var rows = $(this);
          var Ss_Impuesto           = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
          var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat(rows.find('td:eq(5) input', this).val());
          var $Ss_Descuento_Producto = parseFloat(rows.find('td:eq(6) input', this).val());
          var $Ss_Total_Producto = parseFloat(rows.find('td:eq(7) input', this).val());
    
          $Ss_Total += $Ss_Total_Producto;

          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
          
          if (Nu_Tipo_Impuesto == 1){
            $Ss_SubTotal += $Ss_SubTotal_Producto;
            $Ss_IGV += $Ss_Total_Producto - $Ss_SubTotal_Producto;
          }
          
          $Ss_Descuento += (($Ss_Descuento_Producto * (parseFloat(rows.find('td:eq(1) input', this).val()) * parseFloat(rows.find('td:eq(3) input', this).val()))) / 100);
        });
        
        $( '#txt-subTotal' ).val( $Ss_SubTotal.toFixed(2) );
    		$( '#span-subTotal' ).text( $Ss_SubTotal.toFixed(2) );
    		
    		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
    		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
    		
    		$( '#txt-impuesto' ).val( $Ss_IGV.toFixed(2) );
    		$( '#span-impuesto' ).text( $Ss_IGV.toFixed(2) );
    		
    		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
    		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
  		} else if (nu_tipo_impuesto == 2){//Inafecto
        fila.find(".txt-Ss_Precio").val( (parseFloat((total_producto / cantidad) / impuesto_producto).toFixed(6)).toString().split(". ") );
        fila.find(".txt-Ss_SubTotal_Producto").val( (parseFloat(total_producto / impuesto_producto).toFixed(6)).toString().split(". ") );
        
        var $Ss_Inafecto = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_IGV = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleProductosOrdenCompra > tbody > tr").each(function(){
          var rows = $(this);
          var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat(rows.find('td:eq(5) input', this).val());
          var $Ss_Descuento_Producto = parseFloat(rows.find('td:eq(6) input', this).val());

          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
          
          if (Nu_Tipo_Impuesto == 2)
            $Ss_Inafecto += $Ss_SubTotal_Producto;
          
          $Ss_Descuento += (($Ss_Descuento_Producto * (parseFloat(rows.find('td:eq(1) input', this).val()) * parseFloat(rows.find('td:eq(3) input', this).val()))) / 100);
          $Ss_Total += parseFloat(rows.find('td:eq(7) input', this).val());
        });
        
        $( '#txt-inafecto' ).val( $Ss_Inafecto.toFixed(2) );
    		$( '#span-inafecto' ).text( $Ss_Inafecto.toFixed(2) );
    		
    		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
    		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
    		
    		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
    		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
  		} else if (nu_tipo_impuesto == 3){//Exonerada
        fila.find(".txt-Ss_Precio").val( (parseFloat((total_producto / cantidad) / impuesto_producto).toFixed(6)).toString().split(". ") );
        fila.find(".txt-Ss_SubTotal_Producto").val( (parseFloat(total_producto / impuesto_producto).toFixed(6)).toString().split(". ") );
        
        var $Ss_Exonerada = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_IGV = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleProductosOrdenCompra > tbody > tr").each(function(){
          var rows = $(this);
          var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat(rows.find('td:eq(5) input', this).val());
          var $Ss_Descuento_Producto = parseFloat(rows.find('td:eq(6) input', this).val());

          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
          
          if (Nu_Tipo_Impuesto == 3)
            $Ss_Exonerada += $Ss_SubTotal_Producto;
          
          $Ss_Descuento += (($Ss_Descuento_Producto * (parseFloat(rows.find('td:eq(1) input', this).val()) * parseFloat(rows.find('td:eq(3) input', this).val()))) / 100);
          $Ss_Total += parseFloat(rows.find('td:eq(7) input', this).val());
        });
        
        $( '#txt-exonerada' ).val( $Ss_Exonerada.toFixed(2) );
    		$( '#span-exonerada' ).text( $Ss_Exonerada.toFixed(2) );
    		
    		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
    		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
    		
    		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
    		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
  		} else if (nu_tipo_impuesto == 4){//Gratuita
        fila.find(".txt-Ss_Precio").val( (parseFloat((total_producto / cantidad) / impuesto_producto).toFixed(6)).toString().split(". ") );
        fila.find(".txt-Ss_SubTotal_Producto").val( (parseFloat(total_producto / impuesto_producto).toFixed(6)).toString().split(". ") );
        
        var $Ss_Gratuita = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_IGV = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleProductosOrdenCompra > tbody > tr").each(function(){
          var rows = $(this);
          var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat(rows.find('td:eq(5) input', this).val());
          var $Ss_Descuento_Producto = parseFloat(rows.find('td:eq(6) input', this).val());

          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
          
          if (Nu_Tipo_Impuesto == 4)
            $Ss_Gratuita += $Ss_SubTotal_Producto;
          
          $Ss_Descuento += (($Ss_Descuento_Producto * (parseFloat(rows.find('td:eq(1) input', this).val()) * parseFloat(rows.find('td:eq(3) input', this).val()))) / 100);
          $Ss_Total += parseFloat(rows.find('td:eq(7) input', this).val());
        });
        
        $( '#txt-gratuita' ).val( $Ss_Gratuita.toFixed(2) );
    		$( '#span-gratuita' ).text( $Ss_Gratuita.toFixed(2) );
    		
    		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
    		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
    		
    		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
    		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
  		}
    }
  })
  
	$( '#table-DetalleProductosOrdenCompra tbody' ).on('click', '#btn-deleteProducto', function(){
    $(this).closest('tr').remove ();
    
    var $Ss_Descuento = parseFloat($('#txt-Ss_Descuento').val());
    var $Ss_SubTotal = 0.00;
    var $Ss_Exonerada = 0.00;
    var $Ss_Inafecto = 0.00;
    var $Ss_Gratuita = 0.00;
    var $Ss_IGV = 0.00;
    var $Ss_Total = 0.00;
    var iCantDescuento = 0;
    var globalImpuesto = 0;
    var $Ss_Descuento_p = 0;
    $("#table-DetalleProductosOrdenCompra > tbody > tr").each(function(){
      var rows = $(this);
      var fImpuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
      var iGrupoImpuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
      var $Ss_SubTotal_Producto = parseFloat(rows.find('td:eq(5) input', this).val());
      var $Ss_Descuento_Producto = parseFloat(rows.find('td:eq(6) input', this).val());
      var $Ss_Total_Producto = parseFloat(rows.find('td:eq(7) input', this).val());

      $Ss_Total += $Ss_Total_Producto;

      if (iGrupoImpuesto == 1) {
        $Ss_SubTotal += $Ss_SubTotal_Producto;
        $Ss_IGV += $Ss_Total_Producto - $Ss_SubTotal_Producto;
        globalImpuesto = fImpuesto;
      } else if (iGrupoImpuesto == 2) {
        $Ss_Inafecto += $Ss_SubTotal_Producto;
        globalImpuesto += 0;
      } else if (iGrupoImpuesto == 3) {
        $Ss_Exonerada += $Ss_SubTotal_Producto;
        globalImpuesto += 0;
      } else {
        $Ss_Gratuita += $Ss_SubTotal_Producto;
        globalImpuesto += 0;
      }
        
      if(isNaN($Ss_Descuento_Producto))
        $Ss_Descuento_Producto = 0;
        
      $Ss_Descuento_p += (($Ss_Descuento_Producto * (parseFloat(rows.find('td:eq(1) input', this).val()) * parseFloat(rows.find('td:eq(3) input', this).val()))) / 100);
    });
    
    if ($Ss_Descuento > 0.00) {
      var $Ss_Descuento_Gravadas = 0, $Ss_Descuento_Inafecto = 0, $Ss_Descuento_Exonerada = 0, $Ss_Descuento_Gratuita = 0;
      if ($Ss_SubTotal > 0.00) {
        $Ss_Descuento_Gravadas = (($Ss_Descuento * $Ss_SubTotal) / 100);
        $Ss_SubTotal = ($Ss_SubTotal - $Ss_Descuento_Gravadas);
        $Ss_IGV = ($Ss_SubTotal * globalImpuesto) - $Ss_SubTotal;
      }

      if ($Ss_Inafecto > 0.00) {
        $Ss_Descuento_Inafecto = (($Ss_Descuento * $Ss_Inafecto) / 100);
        $Ss_Inafecto = ($Ss_Inafecto - $Ss_Descuento_Inafecto);
      }
      
      if ($Ss_Exonerada > 0.00) {
        $Ss_Descuento_Exonerada = (($Ss_Descuento * $Ss_Exonerada) / 100);
        $Ss_Exonerada = ($Ss_Exonerada - $Ss_Descuento_Exonerada);
      }
      
      if ($Ss_Gratuita > 0.00) {
        $Ss_Descuento_Gratuita = (($Ss_Descuento * $Ss_Gratuita) / 100);
        $Ss_Gratuita = ($Ss_Gratuita - $Ss_Descuento_Gratuita);
      }
      
      $Ss_Total = ($Ss_SubTotal * globalImpuesto) + $Ss_Inafecto + $Ss_Exonerada + $Ss_Gratuita;
      $Ss_Descuento = $Ss_Descuento_Gravadas + $Ss_Descuento_Inafecto + $Ss_Descuento_Exonerada + $Ss_Descuento_Gratuita;
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
    
    $( '#txt-gratuita' ).val( $Ss_Gratuita.toFixed(2) );
    $( '#span-gratuita' ).text( $Ss_Gratuita.toFixed(2) );

    $( '#txt-impuesto' ).val( $Ss_IGV.toFixed(2) );
    $( '#span-impuesto' ).text( $Ss_IGV.toFixed(2) );
  	
  	$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
  	$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );

		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
		
    if ($( '#table-DetalleProductosOrdenCompra >tbody >tr' ).length == 0)
	      $( '#table-DetalleProductosOrdenCompra' ).hide();
	})
	
  $('#table-OrdenCompraTotal' ).on('input', '#txt-Ss_Descuento', function(){
    var $Ss_Descuento_Producto = 0.00;
    $("#table-DetalleProductosOrdenCompra > tbody > tr").each(function(){
      var rows = $(this);
      $Ss_Descuento_Producto = parseFloat(rows.find('td:eq(6) input', this).val());
      
      if(isNaN($Ss_Descuento_Producto))
        $Ss_Descuento_Producto = 0;
      
      $Ss_Descuento_Producto += $Ss_Descuento_Producto;
    })

    if ($Ss_Descuento_Producto == 0) {
  		var $Ss_Descuento = parseFloat($(this).val());
      var $Ss_SubTotal = 0.00;
      var $Ss_Exonerada = 0.00;
      var $Ss_Inafecto = 0.00;
      var $Ss_Gratuita = 0.00;
      var $Ss_IGV = 0.00;
      var $Ss_Total = 0.00;
      var globalImpuesto = 0;
      $("#table-DetalleProductosOrdenCompra > tbody > tr").each(function(){
        var rows = $(this);
        var fImpuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
        var iGrupoImpuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
        var $Ss_SubTotal_Producto = parseFloat(rows.find('td:eq(5) input', this).val());
        var $Ss_Total_Producto = parseFloat(rows.find('td:eq(7) input', this).val());
  
        $Ss_Total += $Ss_Total_Producto;
  
        if (iGrupoImpuesto == 1) {
          $Ss_SubTotal += $Ss_SubTotal_Producto;
          $Ss_IGV += $Ss_Total_Producto - $Ss_SubTotal_Producto;
          globalImpuesto = fImpuesto;
        } else if (iGrupoImpuesto == 2) {
          $Ss_Inafecto += $Ss_SubTotal_Producto;
          globalImpuesto += 0;
        } else if (iGrupoImpuesto == 3) {
          $Ss_Exonerada += $Ss_SubTotal_Producto;
          globalImpuesto += 0;
        } else {
          $Ss_Gratuita += $Ss_SubTotal_Producto;
          globalImpuesto += 0;
        }
      });
      
      if ($Ss_Descuento > 0.00) {
        var $Ss_Descuento_Gravadas = 0, $Ss_Descuento_Inafecto = 0, $Ss_Descuento_Exonerada = 0, $Ss_Descuento_Gratuita = 0;
        if ($Ss_SubTotal > 0.00) {
          $Ss_Descuento_Gravadas = (($Ss_Descuento * $Ss_SubTotal) / 100);
          $Ss_SubTotal = ($Ss_SubTotal - $Ss_Descuento_Gravadas);
          $Ss_IGV = ($Ss_SubTotal * globalImpuesto) - $Ss_SubTotal;
        }

        if ($Ss_Inafecto > 0.00) {
          $Ss_Descuento_Inafecto = (($Ss_Descuento * $Ss_Inafecto) / 100);
          $Ss_Inafecto = ($Ss_Inafecto - $Ss_Descuento_Inafecto);
        }
        
        if ($Ss_Exonerada > 0.00) {
          $Ss_Descuento_Exonerada = (($Ss_Descuento * $Ss_Exonerada) / 100);
          $Ss_Exonerada = ($Ss_Exonerada - $Ss_Descuento_Exonerada);
        }
      
        if ($Ss_Gratuita > 0.00) {
          $Ss_Descuento_Gratuita = (($Ss_Descuento * $Ss_Gratuita) / 100);
          $Ss_Gratuita = ($Ss_Gratuita - $Ss_Descuento_Gratuita);
        }
        
        $Ss_Total = ($Ss_SubTotal * globalImpuesto) + $Ss_Inafecto + $Ss_Exonerada + $Ss_Gratuita;
        $Ss_Descuento = $Ss_Descuento_Gravadas + $Ss_Descuento_Inafecto + $Ss_Descuento_Exonerada + $Ss_Descuento_Gratuita;
      }
      
      $( '#txt-subTotal' ).val( $Ss_SubTotal.toFixed(2) );
      $( '#span-subTotal' ).text( $Ss_SubTotal.toFixed(2) );
      
      $( '#txt-exonerada' ).val( $Ss_Exonerada.toFixed(2) );
      $( '#span-exonerada' ).text( $Ss_Exonerada.toFixed(2) );
      
      $( '#txt-inafecto' ).val( $Ss_Inafecto.toFixed(2) );
      $( '#span-inafecto' ).text( $Ss_Inafecto.toFixed(2) );
      
      $( '#txt-gratuita' ).val( $Ss_Gratuita.toFixed(2) );
      $( '#span-gratuita' ).text( $Ss_Gratuita.toFixed(2) );

      $( '#txt-impuesto' ).val( $Ss_IGV.toFixed(2) );
      $( '#span-impuesto' ).text( $Ss_IGV.toFixed(2) );
    	
    	$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
    	$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
  
  		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
  		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
    }
  })
})

function isExistTableTemporalProductoOrden($ID_Producto){
  return Array.from($('tr[id*=tr_detalle_producto]'))
    .some(element => ($('td:nth(0)',$(element)).html()===$ID_Producto));
}

function form_OrdenCompra(){
  if (accion_orden_compra == 'add_orden_compra' || accion_orden_compra == 'upd_orden_compra'){
    var arrDetalleOrdenCompra = [];
    var arrValidarNumerosEnCero = [];
    var $counterNumerosEnCero = 0;
    var tr_foot = '';
    
    $("#table-DetalleProductosOrdenCompra > tbody > tr").each(function(){
      var rows = $(this);
      
      var $ID_Producto                  = rows.find("td:eq(0)").text();
      var $Qt_Producto                  = $('td:eq(1) input', this).val();
      var $Ss_Precio                    = $('td:eq(3) input', this).val();
      var $ID_Impuesto_Cruce_Documento  = $('td:eq(4) select', this).val();
      var $Ss_SubTotal                  = $('td:eq(5) input', this).val();
      var $Ss_Descuento                 = $('td:eq(6) input', this).val();
      var $Ss_Total                     = $('td:eq(7) input', this).val();
      var $fDescuentoSinImpuestosItem = rows.find(".td-fDescuentoSinImpuestosItem").text();
      var $fDescuentoImpuestosItem = rows.find(".td-fDescuentoImpuestosItem").text();
      
      if (parseFloat($Ss_Precio) == 0 || parseFloat($Qt_Producto) == 0 || parseFloat($Ss_Total) == 0){
        arrValidarNumerosEnCero[$counterNumerosEnCero] = $ID_Producto;
        $('#tr_detalle_producto' + $ID_Producto).addClass('danger');
      }
      
      var obj = {};
      
      obj.ID_Producto	                = $ID_Producto;
      obj.Ss_Precio	                  = $Ss_Precio;
      obj.Qt_Producto	                = $Qt_Producto;
      obj.ID_Impuesto_Cruce_Documento	= $ID_Impuesto_Cruce_Documento;
      obj.Ss_SubTotal	                = $Ss_SubTotal;
      obj.Ss_Descuento	              = $Ss_Descuento;
      obj.Ss_Impuesto	                = $Ss_Total - $Ss_SubTotal;
      obj.Ss_Total	                  = $Ss_Total;
      obj.fDescuentoSinImpuestosItem = $fDescuentoSinImpuestosItem;
      obj.fDescuentoImpuestosItem	= $fDescuentoImpuestosItem;
      arrDetalleOrdenCompra.push(obj);
      $counterNumerosEnCero++;
    });
    
    bEstadoValidacion = validatePreviousDocumentToSaveOrderPurchase();
    
    if ( arrDetalleOrdenCompra.length == 0){
  		$( '#panel-DetalleProductosOrdenCompra' ).removeClass('panel-default');
  		$( '#panel-DetalleProductosOrdenCompra' ).addClass('panel-danger');
  		
      $( '#txt-No_Producto' ).closest('.form-group').find('.help-block').html('Documento <b>sin detalle</b>');
  	  $( '#txt-No_Producto' ).closest('.form-group').removeClass('has-success').addClass('has-error');
  	  
		  scrollToError( $("html, body"), $( '#txt-No_Producto' ) );
    } else if (arrValidarNumerosEnCero.length > 0) {
      tr_foot +=
      "<tfoot>"
        +"<tr class='danger'>"
          +"<td colspan='9' class='text-center'>Item(s) con <b>precio / cantidad / total en cero</b></td>"
        +"</tr>"
      +"<tfoot>";
      $( '#table-DetalleProductosOrdenCompra >tbody' ).after(tr_foot);
    } else if (bEstadoValidacion) {
  		$( '#panel-DetalleProductosOrdenCompra' ).removeClass('panel-danger');
  		$( '#panel-DetalleProductosOrdenCompra' ).addClass('panel-default');
  		
  		var arrOrdenCompraCabecera = Array();
  		arrOrdenCompraCabecera = {
  		  'EID_Empresa'                 : $( '[name="EID_Empresa"]' ).val(),
  		  'EID_Documento_Cabecera'      : $( '[name="EID_Documento_Cabecera"]' ).val(),
  		  'ENu_Estado'                  : $( '[name="ENu_Estado"]' ).val(),
  		  'ID_Serie_Documento'          : $( '#txt-ID_Serie_Documento' ).val(),
  		  'ID_Numero_Documento'         : $( '#txt-ID_Numero_Documento' ).val(),
  		  'Fe_Emision'                  : $( '#txt-Fe_Emision' ).val(),
  		  'Fe_Vencimiento'              : $( '#txt-Fe_Vencimiento' ).val(),
  		  'Fe_Entrega'                  : $( '#txt-Fe_Entrega' ).val(),
  		  'ID_Moneda'                   : $( '#cbo-Monedas' ).val(),
  		  'ID_Medio_Pago'               : $( '#cbo-MediosPago' ).val(),
  		  'Nu_Descargar_Inventario'     : 0,
  		  'ID_Almacen'                  : 0,
  		  'ID_Entidad'                  : $( '#txt-AID' ).val(),
  		  'ID_Contacto'                 : $( '#txt-AID_Contacto' ).val(),
  		  'Txt_Garantia'                : $( '[name="Txt_Garantia"]' ).val(),
  		  'Txt_Glosa'                   : $( '[name="Txt_Glosa"]' ).val(),
  		  'Po_Descuento'                : $( '#txt-Ss_Descuento' ).val(),
  		  'Ss_Descuento'                : $( '#txt-descuento' ).val(),
  		  'Ss_Total'                    : $( '#txt-total' ).val(),
  		  'ID_Lista_Precio_Cabecera'    : $( '#cbo-lista_precios' ).val(),
        'Nu_Descargar_Inventario' : $( '#cbo-descargar_stock' ).val(),
        'ID_Almacen' : $( '#cbo-almacen' ).val(),
  		};
  		
  		var No_Proveedor_Filter=$( '#txt-ANombre' ).val(), arrProveedorNuevo = {};
  		if ($('[name="addProveedor"]:checked').attr('value') == 1){//Agregar proveedor
    		arrProveedorNuevo = {
    		  'ID_Tipo_Documento_Identidad' : $( '#cbo-TiposDocumentoIdentidadProveedor' ).val(),
    		  'Nu_Documento_Identidad'      : $( '#txt-Nu_Documento_Identidad_Proveedor' ).val(),
    		  'No_Entidad'                  : $( '#txt-No_Entidad_Proveedor' ).val(),
    		  'Txt_Direccion_Entidad'       : $( '#txt-Txt_Direccion_Entidad_Proveedor' ).val(),
    		  'Nu_Telefono_Entidad'         : $( '#txt-Nu_Telefono_Entidad_Proveedor' ).val(),
    		  'Nu_Celular_Entidad'          : $( '#txt-Nu_Celular_Entidad_Proveedor' ).val(),
        };
        No_Proveedor_Filter=$( '#txt-No_Entidad_Proveedor' ).val();
  		}
  		
      var No_Contacto_Filter=$( '#txt-No_Contacto_existe' ).val(), arrContactoNuevo = {};
  		if ($('[name="addContacto"]:checked').attr('value') == 1){//Agregar contacto
    		arrContactoNuevo = {
    		  'ID_Tipo_Documento_Identidad' : $( '#cbo-TiposDocumentoIdentidadContacto' ).val(),
    		  'Nu_Documento_Identidad'      : $( '#txt-Nu_Documento_Identidad' ).val(),
    		  'No_Entidad'                  : $( '#txt-No_Contacto' ).val(),
    		  'Nu_Telefono_Entidad'         : $( '#txt-Nu_Telefono_Contacto' ).val(),
    		  'Nu_Celular_Entidad'          : $( '#txt-Nu_Celular_Contacto' ).val(),
    		  'Txt_Email_Entidad'           : $( '#txt-Txt_Email_Contacto' ).val(),
    		};
  		  No_Contacto_Filter = $( '#txt-No_Contacto' ).val();
  		}

      $( '#btn-save' ).text('');
      $( '#btn-save' ).attr('disabled', true);
      $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
      
      $( '#modal-loader' ).modal('show');
  
      url = base_url + 'Logistica/OrdenCompraController/crudOrdenCompra';
    	$.ajax({
        type		  : 'POST',
        dataType	: 'JSON',
    		url		    : url,
    		data		  : {
    		  arrOrdenCompraCabecera : arrOrdenCompraCabecera,
    		  arrDetalleOrdenCompra : arrDetalleOrdenCompra,
    		  arrProveedorNuevo : arrProveedorNuevo,
    		  arrContactoNuevo : arrContactoNuevo,
    		},
    		success : function( response ){
    		  $( '#modal-loader' ).modal('hide');
    		  
    	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
      	  $( '#modal-message' ).modal('show');
      	  
    		  if (response.status == 'success'){
    		    accion_orden_compra='';
    		    
            $( '#txt-Filtro_Entidad' ).val( No_Proveedor_Filter );
            $( '#txt-Filtro_Contacto' ).val( No_Contacto_Filter );
      	    
    		    $( '#form-OrdenCompra' )[0].reset();
            $( '.div-AgregarEditar' ).hide();
            $( '.div-Listar' ).show();
      	    $( '.modal-message' ).addClass(response.style_modal);
      	    $( '.modal-title-message' ).text(response.message);
      	    setTimeout(function() {$('#modal-message').modal('hide'); }, 1100);
      	    reload_table_orden_compra();
    		  } else {
      	    $( '.modal-message' ).addClass(response.style_modal);
      	    $( '.modal-title-message' ).text(response.message);
      	    setTimeout(function() {$('#modal-message').modal('hide');}, 1200);
    		  }
    		  
          $( '#btn-save' ).text('');
          $( '#btn-save' ).append( 'Guardar' );
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
          $( '#btn-save' ).append( 'Guardar' );
          $( '#btn-save' ).attr('disabled', false);
        }
    	});
    }
  }
}

function pdfOrdenCompra(ID){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '.modal-message-delete' ).removeClass('modal-danger modal-warning modal-success');
  $( '.modal-message-delete' ).addClass('modal-success');
  
  $( '.modal-title-message-delete' ).text('¿Deseas generar PDF?');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });
  
  $( '#btn-save-delete' ).off('click').click(function () {
    $( '#modal-loader' ).modal('show');
    url = base_url + 'Logistica/OrdenCompraController/getOrdenCompraPDF/' + ID;
    window.open(url,'_blank');
    $( '#modal-loader' ).modal('hide');
    $modal_delete.modal('hide');
  });
}

function estadoOrdenCompra(ID, Nu_Descargar_Inventario, Nu_Estado){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '.modal-message-delete' ).removeClass('modal-danger modal-warning modal-success');
  $( '.modal-message-delete' ).addClass('modal-success');
  
  $( '.modal-title-message-delete' ).text('¿Deseas cambiar el estado?');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });
  
  $( '#btn-save-delete' ).off('click').click(function () {
    $( '#modal-loader' ).modal('show');
    
    url = base_url + 'Logistica/OrdenCompraController/estadoOrdenCompra/' + ID + '/' + Nu_Descargar_Inventario + '/' + Nu_Estado;
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
    	    reload_table_orden_compra();
  		  } else {
    	    $( '.modal-message' ).addClass(response.style_modal);
    	    $( '.modal-title-message' ).text(response.message);
    	    setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
  		  }
      }
    });
  });
}

function duplicarOrdenCompra(ID){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '.modal-message-delete' ).removeClass('modal-danger modal-warning modal-success');
  $( '.modal-message-delete' ).addClass('modal-success');
  
  $( '.modal-title-message-delete' ).text('¿Deseas duplicar?');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });
  
  $( '#btn-save-delete' ).off('click').click(function () {
    $( '#modal-loader' ).modal('show');
    
    url = base_url + 'Logistica/OrdenCompraController/duplicarOrdenCompra/' + ID
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
    	    reload_table_orden_compra();
  		  } else {
    	    $( '.modal-message' ).addClass(response.style_modal);
    	    $( '.modal-title-message' ).text(response.message);
    	    setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
  		  }
  		  
  		  accion_orden_compra = '';
      },
      error: function (jqXHR, textStatus, errorThrown) {
        accion_orden_compra = '';
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

function facturarOrdenCompra(ID){
  timesClicked = 0;

  var $modal = $( '.modal-orden' );
  $modal.modal('show');
  
	$modal.on('shown.bs.modal', function() {
		$( '.hidden-modal_orden' ).focus();
	})

  //Salir modal
  $( '#btn-modal-salir-orden' ).off('click').click(function () {
    $modal.modal('hide');
  });

  //Limpiar modal
  $( '#div-modal-body-orden' ).empty();
  
	$(document).ready(function(){
    $( '.input-codigo_barra' ).on('input', function () {
      this.value = this.value.replace(/[^a-zA-Z0-9]/g,'');
    });
    
    $( '.input-number' ).on('input', function () {
      this.value = this.value.replace(/[^0-9]/g,'');
    });

    $( '#cbo-modal-descargar_stock' ).html( '<option value="1">Si</option>' );
    $( '#cbo-modal-descargar_stock' ).append( '<option value="0">No</option>' );
  
    var arrParams = {
      'iIdComboxAlmacen' : 'cbo-modal-almacen'
    };
    getAlmacenes(arrParams);
  
    url = base_url + 'HelperController/getTiposDocumentos';
    $.post( url, {Nu_Tipo_Filtro : 4}, function( response ){//2 = Compra
      $( '#cbo-tipo_documento_modal' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
      for (var i = 0; i < response.length; i++) {
        if(response[i]['No_Tipo_Documento_Breve']=='Nota de Venta')
          response[i]['No_Tipo_Documento_Breve'] = 'Nota de Compra';
        $( '#cbo-tipo_documento_modal' ).append( '<option value="' + response[i]['ID_Tipo_Documento'] + '" data-nu_impuesto="' + response[i]['Nu_Impuesto'] + '" data-nu_enlace="' + response[i]['Nu_Enlace'] + '">' + response[i]['No_Tipo_Documento_Breve'] + '</option>' );
      }
    }, 'JSON');
  });
  
  var html_orden_cabecera='';
  html_orden_cabecera += 
  '<div class="row">'
    +'<div class="col-sm-12 col-md-12">'
	    +'<div class="panel panel-default">'
        +'<div class="panel-heading"><i class="fa fa-book"></i> <b>Datos del Documento</b></div>'
        +'<div class="panel-body">'
          +'<div class="row">'
            +'<div class="col-xs-6 col-sm-3 col-md-2">'
              +'<div class="form-group">'
                +'<label>Tipo <span class="label-advertencia">*</span></label>'
          			+'<select id="cbo-tipo_documento_modal" class="form-control required" style="width: 100%;"></select>'
                +'<span class="help-block" id="error"></span>'
              +'</div>'
            +'</div>'
            +'<div class="col-xs-6 col-sm-3 col-md-2">'
              +'<div class="form-group">'
                +'<label>Series <span class="label-advertencia">*</span></label>'
          			+'<input type="text" id="txt-ID_Serie_Documento_Modal" class="form-control required input-Mayuscula input-codigo_barra" maxlength="20" autocomplete="off" placeholder="Ingresar serie">'
                +'<span class="help-block" id="error"></span>'
              +'</div>'
            +'</div>'
            +'<div class="col-xs-6 col-sm-3 col-md-2">'
              +'<div class="form-group">'
                +'<label>Número <span class="label-advertencia">*</span></label>'
                +'<input type="tel" id="txt-ID_Numero_Documento_Modal" class="form-control required input-number" maxlength="20" autocomplete="off" placeholder="Ingresar número">'
                +'<span class="help-block" id="error"></span>'
              +'</div>'
            +'</div>'
            +'<div class="col-xs-6 col-sm-3 col-md-2">'
              +'<div class="form-group">'
                +'<label>F. Emisión <span class="label-advertencia">*</span></label>'
                +'<div class="input-group date">'
                  +'<input type="text" id="txt-Fe_Emision_modal" name="Fe_Emision" class="form-control date-picker-invoice required">'
                +'</div>'
                +'<span class="help-block" id="error"></span>'
              +'</div>'
            + '</div>'
            + '<div class="col-xs-12 col-sm-8 col-md-4">'
              + '<div class="form-group">'
                + '<label>Almacen</label>'
                + '<select id="cbo-modal-almacen" class="form-control required" style="width: 100%;"></select>'
                + '<span class="help-block" id="error"></span>'
              + '</div>'
            + '</div>'
            +'<div class="col-xs-12 col-sm-4 col-md-12">'
              +'<div class="form-group">'
                +'<label>¿Descargar Stock?</label>'
                +'<select id="cbo-modal-descargar_stock" class="form-control required" style="width: 100%;"></select>'
                +'<span class="help-block" id="error"></span>'
              +'</div>'
            +'</div>'
          +'</div>'
        +'</div>'
      +'</div>'
    +'</div>'
  +'</div>'
  ;
  
  $( '#div-modal-body-orden' ).append(html_orden_cabecera);
  $( '.date-picker-invoice' ).datepicker({
    autoclose : true,
    endDate   : new Date(fYear, fToday.getMonth(), fDay),
    todayHighlight: true
  })
  $( '.date-picker-invoice' ).val( fDay + '/' + fMonth + '/' + fYear);
  
  //Detalle de la Orden Compra
  $( '#modal-loader' ).modal('show');
  var html_table_orden_detalle='';
  url = base_url + 'Logistica/OrdenCompraController/ajax_edit/' + ID;
  $.getJSON( url, function( response ) {    
    url = base_url + 'HelperController/getMonedas';
    $.post( url , function( responseMonedas ){
      $( '#cbo-Monedas' ).html('');
      for (var i = 0; i < responseMonedas.length; i++){
        if(response.arrEdit[0].ID_Moneda == responseMonedas[i].ID_Moneda)
          $( '.span-signo' ).text( responseMonedas[i].No_Signo );
      }
    }, 'JSON');
      
	  $( '#table-DetalleProductosOrdenCompraModal thead' ).empty();
	  $( '#table-DetalleProductosOrdenCompraModal tbody' ).empty();
	  
    html_table_orden_detalle +=
    '<input type="hidden" id="txt-ID_Empresa" value="' + response.arrEdit[0].ID_Empresa + '">'
    +'<input type="hidden" id="txt-ID_Documento_Cabecera" value="' + response.arrEdit[0].ID_Documento_Cabecera + '">'
    +'<input type="hidden" id="modal-txt-TiposDocumentoModificar" value="' + response.arrEdit[0].ID_Tipo_Documento + '">'
    +'<input type="hidden" id="modal-txt-ID_Entidad" value="' + response.arrEdit[0].ID_Entidad + '">'
    +'<input type="hidden" id="txt-ID_Medio_Pago" value="' + response.arrEdit[0].ID_Medio_Pago + '">'
    +'<input type="hidden" id="txt-iTipoFormaPago" value="' + response.arrEdit[0].Nu_Tipo + '">'
    +'<input type="hidden" id="txt-ID_Moneda" value="' + response.arrEdit[0].ID_Moneda + '">'
    +'<input type="hidden" id="txt-ID_Entidad" value="' + response.arrEdit[0].ID_Entidad + '">'
    +'<input type="hidden" id="txt-ID_Contacto" value="' + response.arrEdit[0].ID_Contacto + '">'
    +'<input type="hidden" id="txt-Txt_Glosa" value="' + response.arrEdit[0].Txt_Glosa + '">'
    +'<input type="hidden" id="txt-Fe_Vencimiento_Modal" value="' + ParseDateString(response.arrEdit[0].Fe_Vencimiento, 6, '-') + '">'
    +'<input type="hidden" id="txt-ID_Lista_Precio_Cabecera" value="' + response.arrEdit[0].ID_Lista_Precio_Cabecera + '">'
	  +'<div class="row">'
      +'<div class="col-md-12">'
		    +'<div id="panel-DetalleProductosOrdenCompra_modal" class="panel panel-default">'
          +'<div class="panel-heading panel-heading_table"><i class="fa fa-shopping-cart"></i> <b>Detalle de items</b></div>'
          +'<div class="panel-body">'
            +'<div class="tab-panel_default_modal_table row">'
              +'<div class="col-md-12">'
                +'<div class="table-responsive">'
                  +'<table id="table-DetalleProductosOrdenCompraModal" class="table table-striped table-bordered">'
                    +'<thead>'
                      +'<tr>'
                        +'<th class="text-center"><input type="checkbox" class="flat-red" onclick="checkAllOrdenCompra();" id="check-AllOrden" checked></th>'
                        +'<th style="display:none;" class="text-left"></th>'
                        +'<th class="text-center">Cantidad</th>'
                        +'<th class="text-center">Item</th>'
                        +'<th class="text-center">Precio</th>'
                        +'<th class="text-left" style="width: 17%;">Impuesto Tributario</th>'
                        +'<th style="display:none;" class="text-center"></th>'
                        +'<th class="text-center">Sub Total</th>'
                        +'<th class="text-center">% Dscto</th>'
                        +'<th class="text-center">Total</th>'
                        +'<th style="display:none;" class="text-left"></th>'
                      +'</tr>'
                    +'</thead>'
                    +'<tbody>'
                    +'</tbody>'
                  +'</table>'
                +'</div>'
              +'</div>'
            +'</div>'
          +'</div>'//Fin panel body
        +'</div>'//Fin panel padre
      +'</div>'
    +'</div>'
    ;
    
    $( '#div-modal-body-orden' ).append(html_table_orden_detalle);
	  
    //Detallevar
    var table_detalle_producto = '';
    var _ID_Producto = '';
    var $Ss_SubTotal_Producto = 0.00;
    var $Ss_IGV_Producto = 0.00;
    var $Ss_Descuento_Producto = 0.00;
    var $Ss_Total_Producto = 0.00;
    var $Ss_Gravada = 0.00;
    var $Ss_Exonerada = 0.00;
    var $Ss_Inafecto = 0.00;
    var $Ss_Gratuita = 0.00;
    var $Ss_IGV = 0.00;
    var $Ss_Total = 0.00;
    var option_impuesto_producto = '';
    
    var $fDescuento_Producto = 0;
    var fDescuento_Total_Producto = 0;
    var globalImpuesto = 0;
    var $iDescuentoGravada = 0;
    var $iDescuentoExonerada = 0;
    var $iDescuentoInafecto = 0;
    var $iDescuentoGratuita = 0;
    var $iDescuentoGlobalImpuesto = 0;
    var selected;

    var iTotalRegistros = response.arrEdit.length;
    var iTotalRegistrosImpuestos = response.arrImpuesto.length;
    for (var i = 0; i < iTotalRegistros; i++) {
      if (_ID_Producto != response.arrEdit[i].ID_Producto) {
        _ID_Producto = response.arrEdit[i].ID_Producto;
        option_impuesto_producto = '';
      }
      
      $Ss_SubTotal_Producto = parseFloat(response.arrEdit[i].Ss_SubTotal_Producto)
      if (response.arrEdit[i].Nu_Tipo_Impuesto == 1){
        $Ss_IGV += parseFloat(response.arrEdit[i].Ss_Impuesto_Producto);
        $Ss_Gravada += $Ss_SubTotal_Producto;
      } else if (response.arrEdit[i].Nu_Tipo_Impuesto == 2){
        $Ss_Inafecto += $Ss_SubTotal_Producto;
      } else if (response.arrEdit[i].Nu_Tipo_Impuesto == 3){
        $Ss_Exonerada += $Ss_SubTotal_Producto;
      } else if (response.arrEdit[i].Nu_Tipo_Impuesto == 4){
        $Ss_Gratuita += $Ss_SubTotal_Producto;
      }
      
      $Ss_Descuento_Producto += parseFloat(response.arrEdit[i].Ss_Descuento_Producto);
      $Ss_Total += parseFloat(response.arrEdit[i].Ss_Total_Producto);
      
      for (var x = 0; x < iTotalRegistrosImpuestos; x++){
        selected = '';
        if (response.arrImpuesto[x].ID_Impuesto_Cruce_Documento == response.arrEdit[i].ID_Impuesto_Cruce_Documento)
          selected = 'selected="selected"';
        option_impuesto_producto += "<option value='" + response.arrImpuesto[x].ID_Impuesto_Cruce_Documento + "' data-nu_tipo_impuesto='" + response.arrImpuesto[x].Nu_Tipo_Impuesto + "' data-impuesto_producto='" + response.arrImpuesto[x].Ss_Impuesto + "' " + selected + ">" + response.arrImpuesto[x].No_Impuesto + "</option>";
      }
      
      table_detalle_producto += 
      "<tr id='tr_detalle_producto_modal" + response.arrEdit[i].ID_Producto + "'>"
        +"<td style='display:none;' class='text-left'>" + response.arrEdit[i].ID_Producto + "</td>"
        +"<td class='text-center'><input type='checkbox' class='flat-red check-orden' onclick='calcularTotalOrdenCompraChecked();' checked></td>"
        +"<td class='text-right'>" + response.arrEdit[i].Qt_Producto + "</td>"
        +"<td class='text-left'>" + response.arrEdit[i].Nu_Codigo_Barra + " " + response.arrEdit[i].No_Producto + "</td>"
        +"<td class='text-right'>" + response.arrEdit[i].Ss_Precio + "</td>"
        +"<td class='text-center'>" + response.arrEdit[i].No_Impuesto_Breve + " " + response.arrEdit[i].Po_Impuesto + " %</td>"
        +"<td style='display:none;' class='text-left'>" + response.arrEdit[i].ID_Impuesto_Cruce_Documento +"</td>"
        +"<td class='text-right'>" + response.arrEdit[i].Ss_SubTotal_Producto + "</td>"
        +"<td class='text-right'>" + (response.arrEdit[i].Po_Descuento_Impuesto_Producto == 0.00 ? '' : response.arrEdit[i].Po_Descuento_Impuesto_Producto) + "</td>"
        +"<td class='text-right'>" + response.arrEdit[i].Ss_Total_Producto + "</td>"
        +"<td style='display:none;' class='text-left'>" + response.arrEdit[i].Ss_Impuesto + "</td>"
        +"<td style='display:none;' class='text-right td-fDescuentoSinImpuestosItem'>" + (response.arrEdit[i].Ss_Descuento_Producto == 0.00 ? '' : response.arrEdit[i].Ss_Descuento_Producto) + "</td>"
        +"<td style='display:none;' class='text-right td-fDescuentoImpuestosItem'>" + (response.arrEdit[i].Ss_Descuento_Impuesto_Producto == 0.00 ? '' : response.arrEdit[i].Ss_Descuento_Impuesto_Producto) + "</td>"
        +"<td style='display:none;' class='text-right td-fValorUnitario'>" + parseFloat(response.arrEdit[i].Ss_Precio / response.arrEdit[i].Ss_Impuesto).toFixed(2) + "</td>"
      +"</tr>";
    }
    
	  $( '#table-DetalleProductosOrdenCompraModal >tbody' ).append(table_detalle_producto);
        
    //Orden totales
    var html_orden_totales='';
    html_orden_totales += 
    '<div class="row">'
      +'<div class="col-md-8"></div>'
      +'<div class="col-md-4">'
      +'<div class="panel panel-default">'
        +'<div class="panel-heading"><i class="fa fa-money"></i> <b>Totales</b></div>'
        +'<div class="panel-body">'
          +'<table class="table" id="table-OrdenCompraTotal">'
            +'<tr style="display:none;">'
              +'<td style="display:none;"><label>% Descuento</label></td>'
              +'<td style="display:none;" class="text-right">'
				        +'<input type="tel" class="form-control input-decimal" id="txt-Ss_Descuento" name="Ss_Descuento" size="3" value="' + response.arrEdit[0].Po_Descuento + '" autocomplete="off" />'
              +'</td>'
            +'</tr>'
            +'<tr>'
              +'<td><label>OP. Gravadas</label></td>'
              +'<td class="text-right">'
  			        +'<input type="hidden" class="form-control" id="txt-subTotal_modal" value="' + $Ss_Gravada.toFixed(2) + '"/>'
                +'<span class="span-signo"></span> <span id="span-subTotal_modal">' + $Ss_Gravada.toFixed(2) + '</span>'
              +'</td>'
            +'</tr>'
            +'<tr>'
              +'<td><label>OP. Inafectas</label></td>'
              +'<td class="text-right">'
                +'<input type="hidden" class="form-control" id="txt-inafecto_modal" value="' + $Ss_Inafecto.toFixed(2) + '"/>'
                +'<span class="span-signo"></span> <span id="span-inafecto_modal">' + $Ss_Inafecto.toFixed(2) + '</span>'
              +'</td>'
            +'</tr>'
            +'<tr>'
              +'<td><label>OP. Exoneradas</label></td>'
              +'<td class="text-right">'
                +'<input type="hidden" class="form-control" id="txt-exonerada_modal" value="' + $Ss_Exonerada.toFixed(2) + '"/>'
                +'<span class="span-signo"></span> <span id="span-exonerada_modal">' + $Ss_Exonerada.toFixed(2) + '</span>'
              +'</td>'
            +'</tr>'
            +'<tr>'
              +'<td><label>Gratuitas</label></td>'
              +'<td class="text-right">'
                +'<input type="hidden" class="form-control" id="txt-gratuita_modal" value="' + $Ss_Gratuita.toFixed(2) + '"/>'
                +'<span class="span-signo"></span> <span id="span-gratuita_modal">' + $Ss_Gratuita.toFixed(2) + '</span>'
              +'</td>'
            +'</tr>'
            +'<tr>'
              +'<td><label>Descuento Total (-)</label></td>'
              +'<td class="text-right">'
                +'<input type="hidden" class="form-control" id="txt-descuento_modal" value="' + response.arrEdit[0].Ss_Descuento + '"/>'
                +'<span class="span-signo"></span> <span id="span-descuento_modal">' + response.arrEdit[0].Ss_Descuento + '</span>'
              +'</td>'
            +'</tr>'
            +'<tr>'
              +'<td><label>I.G.V. %</label></td>'
              +'<td class="text-right">'
                +'<input type="hidden" class="form-control" id="txt-impuesto_modal" value="' + $Ss_IGV.toFixed(2) + '"/>'
                +'<span class="span-signo"></span> <span id="span-impuesto_modal">' + $Ss_IGV.toFixed(2) + '</span>'
              +'</td>'
            +'</tr>'
            +'<tr>'
              +'<td><label>Total</label></td>'
              +'<td class="text-right">'
                +'<input type="hidden" class="form-control" id="txt-total_modal" value="' + $Ss_Total.toFixed(2) + '"/>'
                +'<span class="span-signo"></span> <span id="span-total_modal">' + $Ss_Total.toFixed(2) + '</span>'
              +'</td>'
            +'</tr>'
          +'</table>'
        +'</div>'
      +'</div>'
    +'</div>'
    ;
    
    $( '#div-modal-body-orden' ).append(html_orden_totales);
    $( '#modal-loader' ).modal('hide');
  })//Fin Get JSON
  //Fin orden detalle

  $( '#btn-modal-facturar-orden' ).off('click').click(function () {
    accion_orden_compra='add_orden_compra_modal';
    addCompra();
  });
}// /. Modal de orden de compra a boleta / factura

function checkAllOrdenCompra(){
	if ( $( '#check-AllOrden' ).prop('checked') ) {
	  $( '.check-orden' ).prop('checked', true);
	  $( '#check-AllOrden' ).prop('checked', true);
	  calcularTotalOrdenCompraChecked();
	} else {
		if( false == $( '#check-AllOrden' ).prop('checked') ){
	    $( '.check-orden' ).prop('checked', false);
	    $( '#check-AllOrden' ).prop('checked', false);
	    
      $( '#txt-subTotal_modal' ).val( 0.00 );
      $( '#span-subTotal_modal' ).text( '0.00' );
      
      $( '#txt-inafecto_modal' ).val( 0.00 );
      $( '#span-inafecto_modal' ).text( '0.00' );
      
      $( '#txt-exonerada_modal' ).val( 0.00 );
      $( '#span-exonerada_modal' ).text( '0.00' );
      
      $( '#txt-gratuita_modal' ).val( 0.00 );
      $( '#span-gratuita_modal' ).text( '0.00' );
        
      $( '#txt-impuesto_modal' ).val( 0.00 );
      $( '#span-impuesto_modal' ).text( '0.00' );
    	
    	$( '#txt-descuento_modal' ).val( 0.00 );
    	$( '#span-descuento_modal' ).text( '0.00' );
    
    	$( '#txt-total_modal' ).val( 0.00 );
    	$( '#span-total_modal' ).text( '0.00' );
	  }
	}
}

function calcularTotalOrdenCompraChecked(){
  var $Ss_SubTotal = 0.00;
  var $Ss_Exonerada = 0.00;
  var $Ss_Inafecto = 0.00;
  var $Ss_Gratuita = 0.00;
  var $Ss_IGV = 0.00;
  var $Ss_Total = 0.00;
  var iCantDescuento = 0;
  var globalImpuesto = 0;

  $('#table-DetalleProductosOrdenCompraModal > tbody > tr').each(function () {
    var rows = $(this);

    if (rows.find('input[type="checkbox"]').is(':checked')) {
      var rows = $(this);
      
      var $ID_Impuesto_Cruce_Documento  = rows.find("td:eq(6)").text();
      var $Ss_SubTotal_Producto         = parseFloat(rows.find("td:eq(7)").text());
      var $Ss_Descuento_Producto        = parseFloat(rows.find("td:eq(8)").text());
      var $Ss_Impuesto                  = parseFloat(rows.find("td:eq(10)").text());
      var $Ss_Total_Producto = parseFloat(rows.find("td:eq(9)").text());

      $Ss_Total += $Ss_Total_Producto;
      
      if ($ID_Impuesto_Cruce_Documento == 1) {
        $Ss_SubTotal += $Ss_SubTotal_Producto;
        $Ss_IGV += $Ss_Total_Producto - $Ss_SubTotal_Producto;
        globalImpuesto = $Ss_Impuesto;
      } else if ($ID_Impuesto_Cruce_Documento == 2) {
        $Ss_Inafecto += $Ss_SubTotal_Producto;
      } else if ($ID_Impuesto_Cruce_Documento == 3) {
        $Ss_Exonerada += $Ss_SubTotal_Producto;
      } else {
        $Ss_Gratuita += $Ss_SubTotal_Producto;
      }
      
      if(isNaN($Ss_Descuento_Producto))
        $Ss_Descuento_Producto = 0;
    }
  });
  
  $( '#txt-subTotal_modal' ).val( $Ss_SubTotal.toFixed(2) );
  $( '#span-subTotal_modal' ).text( $Ss_SubTotal.toFixed(2) );
  
  $( '#txt-inafecto_modal' ).val( $Ss_Inafecto.toFixed(2) );
  $( '#span-inafecto_modal' ).text( $Ss_Inafecto.toFixed(2) );
  
  $( '#txt-exonerada_modal' ).val( $Ss_Exonerada.toFixed(2) );
  $( '#span-exonerada_modal' ).text( $Ss_Exonerada.toFixed(2) );
  
  $( '#txt-gratuita_modal' ).val( $Ss_Gratuita.toFixed(2) );
  $( '#span-gratuita_modal' ).text( $Ss_Gratuita.toFixed(2) );
    
  $( '#txt-impuesto_modal' ).val( $Ss_IGV.toFixed(2) );
  $( '#span-impuesto_modal' ).text( $Ss_IGV.toFixed(2) );
	
	$( '#txt-descuento_modal' ).val( 0 );
	$( '#span-descuento_modal' ).text( 0 );

	$( '#txt-total_modal' ).val( $Ss_Total.toFixed(2) );
	$( '#span-total_modal' ).text( $Ss_Total.toFixed(2) );
}

function addCompra(){
  if (accion_orden_compra=='add_orden_compra_modal') {
    var arrDetalleCompra = [];
    
    $('#table-DetalleProductosOrdenCompraModal > tbody > tr').each(function () {
      var rows = $(this);
      
      if (rows.find('input[type="checkbox"]').is(':checked')) {
        var $ID_Producto                  = rows.find("td:eq(0)").text();
        var $Qt_Producto                  = rows.find("td:eq(2)").text();
        var $Ss_Precio                    = rows.find("td:eq(4)").text();
        var $ID_Impuesto_Cruce_Documento  = rows.find("td:eq(6)").text();
        var $Ss_SubTotal                  = rows.find("td:eq(7)").text();
        var $Ss_Descuento                 = rows.find("td:eq(8)").text();
        var $Ss_Total                     = rows.find("td:eq(9)").text();
        var $fDescuentoSinImpuestosItem = rows.find(".td-fDescuentoSinImpuestosItem").text();
        var $fDescuentoImpuestosItem = rows.find(".td-fDescuentoImpuestosItem").text();
        var $fValorUnitario = rows.find(".td-fValorUnitario").text();
        
        var obj = {};
        
        obj.ID_Producto	                = $ID_Producto;
        obj.fValorUnitario = $fValorUnitario;
        obj.Ss_Precio	                  = $Ss_Precio;
        obj.Qt_Producto	                = $Qt_Producto;
        obj.ID_Impuesto_Cruce_Documento	= $ID_Impuesto_Cruce_Documento;
        obj.Ss_SubTotal	                = $Ss_SubTotal;
        obj.Ss_Descuento	              = $Ss_Descuento;
        obj.Ss_Impuesto	                = $Ss_Total - $Ss_SubTotal;
        obj.Ss_Total	                  = $Ss_Total;
        obj.fDescuentoSinImpuestosItem = $fDescuentoSinImpuestosItem;
        obj.fDescuentoImpuestosItem	= $fDescuentoImpuestosItem;
        
        arrDetalleCompra.push(obj);
      }
    });
      
    if ( $( '#cbo-tipo_documento_modal' ).val() == 0 ){
      $( '#cbo-tipo_documento_modal' ).closest('.form-group').find('.help-block').html('Seleccionar tipo');
      $( '#cbo-tipo_documento_modal' ).closest('.form-group').removeClass('has-success').addClass('has-error');

      scrollToError($('.modal-orden .modal-body'), $( '#cbo-tipo_documento_modal' ));
    } else if ( $( '#txt-ID_Serie_Documento_Modal' ).val().length === 0 ){
      $( '#txt-ID_Serie_Documento_Modal' ).closest('.form-group').find('.help-block').html('Ingresar serie');
      $( '#txt-ID_Serie_Documento_Modal' ).closest('.form-group').removeClass('has-success').addClass('has-error');

      scrollToError($('.modal-orden .modal-body'), $( '#txt-ID_Serie_Documento_Modal' ));
    } else if ( $( '#txt-ID_Numero_Documento_Modal' ).val().length === 0 ){
      $( '#txt-ID_Numero_Documento_Modal' ).closest('.form-group').find('.help-block').html('Ingresar número');
      $( '#txt-ID_Numero_Documento_Modal' ).closest('.form-group').removeClass('has-success').addClass('has-error');

      scrollToError($('.modal-orden .modal-body'), $( '#txt-ID_Numero_Documento_Modal' ));
    } else if ( $( '#cbo-modal-almacen' ).val() == 0 ){
      $( '#cbo-modal-almacen' ).closest('.form-group').find('.help-block').html('Seleccionar almacén');
      $( '#cbo-modal-almacen' ).closest('.form-group').removeClass('has-success').addClass('has-error');

      scrollToError($('.modal-orden .modal-body'), $( '#cbo-modal-almacen' ));
    } else if ( arrDetalleCompra.length == 0 ){
  		$( '#panel-DetalleProductosOrdenCompra_modal' ).removeClass('panel-default');
  		$( '#panel-DetalleProductosOrdenCompra_modal' ).addClass('panel-danger');
  		
    	$( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
    	
      $( '#modal-message' ).modal('show');
      
      $( '.modal-message' ).addClass( 'modal-danger' );
      $( '.modal-title-message' ).text( 'No ha seleccionado ningún producto' );
      
      $( '.modal-message' ).css("z-index", "2000");
      
      setTimeout(function() {$('#modal-message').modal('hide'); }, 1100);
    } else {
  		$( '#panel-DetalleProductosOrdenCompra_modal' ).removeClass('panel-danger');
  		$( '#panel-DetalleProductosOrdenCompra_modal' ).addClass('panel-default');
      
      var arrCompraCabecera = Array();
  		arrCompraCabecera = {
  		  'esEnlace'                  : 1,
  		  'EID_Empresa'               : '',
  		  'EID_Documento_Cabecera'    : '',
  		  'ID_Entidad'                : $( '#txt-ID_Entidad' ).val(),
  		  'ID_Contacto'               : $( '#txt-ID_Contacto' ).val(),
  		  'ID_Tipo_Documento'         : $( '#cbo-tipo_documento_modal' ).val(),
  		  'ID_Serie_Documento'        : $( '#txt-ID_Serie_Documento_Modal' ).val(),
  		  'ID_Numero_Documento'       : $( '#txt-ID_Numero_Documento_Modal' ).val(),
  		  'Fe_Emision'                : $( '#txt-Fe_Emision_modal' ).val(),
  		  'ID_Moneda'                 : $( '#txt-ID_Moneda' ).val(),
  		  'ID_Medio_Pago'             : $( '#txt-ID_Medio_Pago' ).val(),
        'Fe_Vencimiento': $( '#txt-Fe_Vencimiento_Modal' ).val(),
  		  'Fe_Periodo'                : $( '#txt-Fe_Emision_modal' ).val(),
        'Nu_Descargar_Inventario': $('#cbo-modal-descargar_stock').val(),
  		  'Txt_Glosa'                 : $( '#txt-Txt_Glosa' ).val(),
  		  'Po_Descuento'              : $( '#txt-Ss_Descuento' ).val(),
  		  'Ss_Descuento'              : $( '#txt-descuento_modal' ).val(),
  		  'Ss_Total'                  : $( '#txt-total_modal' ).val(),
  		  'ID_Lista_Precio_Cabecera'  : $( '#txt-ID_Lista_Precio_Cabecera' ).val(),
  		  'ID_Documento_Cabecera_Orden' : $( '#txt-ID_Documento_Cabecera' ).val(),
  		  'Ss_Percepcion'             : 0,
  		  'Fe_Detraccion'             : '00/00/0000',
  		  'Nu_Detraccion'             : '',
        'Nu_Descargar_Inventario' : $( '#cbo-modal-descargar_stock' ).val(),
        'ID_Almacen': $('#cbo-modal-almacen').val(),
        'iTipoFormaPago': $('#txt-iTipoFormaPago').val(),
        iTipoCliente: 0,
        'ID_Tipo_Medio_Pago': 0,
        'Nu_Transaccion': 0,
        'Nu_Tarjeta': 0,
  		};
  		
      var arrCompraModificar = Array();
      arrCompraModificar = {
        ID_Documento_Guardado: $('#txt-ID_Documento_Cabecera').val(),
        ID_Tipo_Documento_Modificar: $('#modal-txt-TiposDocumentoModificar').val(),
        ID_Serie_Documento_Modificar: null,
        ID_Numero_Documento_Modificar: null,
        iIdEntidad: $('#modal-txt-ID_Entidad').val(),
        iTipoCliente: 0,
      };
  		var arrProveedorNuevo = Array();
  		
      $( '#btn-modal-facturar-orden' ).text('');
      $( '#btn-modal-facturar-orden' ).attr('disabled', true);
      $( '#btn-modal-facturar-orden' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
      
      $( '#modal-loader' ).modal('show');
      $( '#modal-loader' ).css("z-index", "2000");
  		
      url = base_url + 'Logistica/CompraController/crudCompra';
    	$.ajax({
        type		  : 'POST',
        dataType	: 'JSON',
    		url		    : url,
    		data		  : {
    		  arrCompraCabecera : arrCompraCabecera,
    		  arrDetalleCompra : arrDetalleCompra,
    		  arrCompraModificar : arrCompraModificar,
    		  arrProveedorNuevo : arrProveedorNuevo,
    		},
    		success : function( response ){
          console.log(response);
    		  $( '#modal-loader' ).modal('hide');
    		  
    	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
      	  $( '#modal-message' ).modal('show');
          $( '.modal-message' ).css("z-index", "2000");
      	  
    		  if ( response.status=='success' ){
    		    accion_orden_compra='';
    		    
    		    $( '.modal-orden' ).modal('hide');
      	    $( '.modal-message' ).addClass(response.style_modal);
      	    $( '.modal-title-message' ).text(response.message);
      	    setTimeout(function() {$('#modal-message').modal('hide'); }, 1100);
      	    reload_table_orden_compra();
    		  } else {
      	    $( '.modal-message' ).addClass(response.style_modal);
      	    $( '.modal-title-message' ).text(response.message);
      	    setTimeout(function() {$('#modal-message').modal('hide');}, 1200);
    		  }
    		  
          $( '#btn-modal-facturar-orden' ).text('');
          $( '#btn-modal-facturar-orden' ).append( 'Facturar (ENTER)' );
          $( '#btn-modal-facturar-orden' ).attr('disabled', false);
    		}
    	});
    }
  }
}

function eliminarData_OrdenCompra($modal_delete, ID, Nu_Descargar_Inventario){
  $( '#modal-loader' ).modal('show');
    
  url = base_url + 'Logistica/OrdenCompraController/eliminarOrdenCompra/' + ID + '/' + Nu_Descargar_Inventario;
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
		    accion_orden_compra='';
		    
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
  	    reload_table_orden_compra();
		  } else {
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
		  }
    }
  });
}

function reload_table_orden_compra(){
  table_orden_compra.ajax.reload(null,false);
}

function validatePreviousDocumentToSaveOrderPurchase(){
  bEstadoValidacion = true;

  if ( $( '#txt-ID_Serie_Documento' ).val().length === 0){
    $( '#txt-ID_Serie_Documento' ).closest('.form-group').find('.help-block').html('Ingresar serie');
    $( '#txt-ID_Serie_Documento' ).closest('.form-group').removeClass('has-success').addClass('has-error');
	  
	  bEstadoValidacion = false;
		scrollToError( $("html, body"), $( '#txt-ID_Serie_Documento' ) );
  } else if ( $( '#txt-ID_Numero_Documento' ).val().length === 0){
    $( '#txt-ID_Numero_Documento' ).closest('.form-group').find('.help-block').html('Ingresar número');
	  $( '#txt-ID_Numero_Documento' ).closest('.form-group').removeClass('has-success').addClass('has-error');
	  
	  bEstadoValidacion = false;
		scrollToError( $("html, body"), $( '#txt-ID_Numero_Documento' ) );
  } else if ( $('[name="addProveedor"]:checked').attr('value') == 0 && ($( '#txt-AID' ).val().length === 0 || $( '#txt-ANombre' ).val().length === 0 || $( '#txt-ACodigo' ).val().length === 0)) {
    $( '#txt-ANombre' ).closest('.form-group').find('.help-block').html('Seleccionar proveedor');
		$( '#txt-ANombre' ).closest('.form-group').removeClass('has-success').addClass('has-error');
		
	  bEstadoValidacion = false;
		scrollToError( $("html, body"), $( '#txt-ANombre' ) );
  } else if ( $( '#cbo-TiposDocumentoIdentidadProveedor' ).val() == 4 && ($('[name="addProveedor"]:checked').attr('value') == 1 && $( '#cbo-TiposDocumentoIdentidadProveedor' ).find(':selected').data('nu_cantidad_caracteres') != $( '#txt-Nu_Documento_Identidad_Proveedor').val().length) ) {
    $( '#txt-Nu_Documento_Identidad_Proveedor' ).closest('.form-group').find('.help-block').html('Debe ingresar ' + $( '#cbo-TiposDocumentoIdentidadProveedor' ).find(':selected').data('nu_cantidad_caracteres') + ' dígitos' );
	  $( '#txt-Nu_Documento_Identidad_Proveedor' ).closest('.form-group').removeClass('has-success').addClass('has-error');
	  
	  bEstadoValidacion = false;
		scrollToError( $("html, body"), $( '#txt-Nu_Documento_Identidad_Proveedor' ) );
  } /*else if ( $('[name="addContacto"]:checked').attr('value') == 1 && $( '#txt-No_Contacto' ).val().length === 0){
    $( '#txt-No_Contacto' ).closest('.form-group').find('.help-block').html('Ingresar nombre' );
	  $( '#txt-No_Contacto' ).closest('.form-group').removeClass('has-success').addClass('has-error');
	  
	  bEstadoValidacion = false;
		scrollToError( $("html, body"), $( '#txt-No_Contacto' ) );
  }
  */
  return bEstadoValidacion;
}

// Ayudas - combobox
function getAlmacenes(arrParams){
  url = base_url + 'HelperController/getAlmacenes';
  $.post( url, {}, function( responseAlmacen ){
    var iCantidadRegistros = responseAlmacen.length;
    var selected = '';
    var iIdAlmacen = 0;
    if (iCantidadRegistros == 1) {
      if (arrParams !== undefined) {
        iIdAlmacen = arrParams.ID_Almacen;
      }
      if (iIdAlmacen == responseAlmacen[0]['ID_Almacen']){
        selected = 'selected="selected"';
      }
      $( '#' + arrParams.iIdComboxAlmacen ).html( '<option value="' + responseAlmacen[0]['ID_Almacen'] + '" ' + selected + '>' + responseAlmacen[0]['No_Almacen'] + '</option>' );
      var arrParamsListaPrecio = {
        ID_Almacen : responseAlmacen[0]['ID_Almacen'],
      }
      getListaPrecios(arrParamsListaPrecio);
    } else {
      $( '#' + arrParams.iIdComboxAlmacen ).html( '<option value="0">- Seleccionar -</option>');
      for (var i = 0; i < iCantidadRegistros; i++) {
        selected='';
        if (arrParams !== undefined) {
          iIdAlmacen = arrParams.ID_Almacen;
        }
        if (iIdAlmacen == responseAlmacen[i]['ID_Almacen']){
          selected = 'selected="selected"';
        }
        $( '#' + arrParams.iIdComboxAlmacen ).append( '<option value="' + responseAlmacen[i]['ID_Almacen'] + '" ' + selected + '>' + responseAlmacen[i]['No_Almacen'] + '</option>' );
      }
    }
  }, 'JSON');
}

function getListaPrecios(arrParams){
  url = base_url + 'HelperController/getListaPrecio';
  var arrPost = {
    Nu_Tipo_Lista_Precio : $( '[name="Nu_Tipo_Lista_Precio"]' ).val(),
    ID_Organizacion: $( '#header-a-id_organizacion' ).val(),
    ID_Almacen : arrParams.ID_Almacen,
  }
  $.post( url, arrPost, function( responseListaPrecio ){
    var iCantidadRegistrosListaPrecios = responseListaPrecio.length;
    if (iCantidadRegistrosListaPrecios == 1) {
      $('#cbo-lista_precios').html('<option value="0">- Seleccionar -</option>');
      $( '#cbo-lista_precios' ).append( '<option value="' + responseListaPrecio[0].ID_Lista_Precio_Cabecera + '">' + responseListaPrecio[0].No_Lista_Precio + '</option>' );
    } else if ( iCantidadRegistrosListaPrecios > 1 ) {
      $( '#cbo-lista_precios' ).html( '<option value="0">- Seleccionar -</option>');
      for (var i = 0; i < iCantidadRegistrosListaPrecios; i++)
        $( '#cbo-lista_precios' ).append( '<option value="' + responseListaPrecio[i].ID_Lista_Precio_Cabecera + '">' + responseListaPrecio[i].No_Lista_Precio + '</option>' );
    } else {
      $( '#cbo-lista_precios' ).html( '<option value="0">- Sin lista precio -</option>');
    }
  }, 'JSON');
}