var url;
var table_orden_venta;
var considerar_igv;
var value_importes_cero = 0.00;
var texto_importes_cero = '0.00';
var arrImpuestosProducto = '{ "arrImpuesto" : [';
var arrImpuestosProductoDetalle;
var accion_orden_venta = '';
var bEstadoValidacion;

//$('.date-picker-invoice').val(fDay + '/' + fMonth + '/' + fYear);
//$('#txt-Filtro_Fe_Inicio').val(fDay + '/' + fMonth + '/' + fYear);

function agregarOrdenVenta(){
  accion_orden_venta = 'add_orden_venta';
  
  $( '#modal-loader' ).modal('show');
  
  $('.div-adicionales_ov').css("display", "none");
  $('.div-adicionales_ov_garantia_glosa').css("display", "none");

  $('#btn-adicionales_ov').data('mostrar_campos_adicionales', 0);
  $('#btn-adicionales_ov_garantia_glosa').data('ver_adicionales_ov_garantia_glosa', 0);

  $('.panel_body_total_todo').css("display", "none");
  $('#btn-ver_total_todo').data('ver_total_todo', 0);
  $('#btn-ver_total_todo').text('VER / DESCUENTO');

  $( '.div-Listar' ).hide();
  $( '.div-AgregarEditar' ).show();
  
  $( '#txt-EID_Empresa' ).focus();
  
  $( '#form-OrdenVenta' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '.title_OrdenVenta' ).text('Nuevo OrdenVenta');
  
  CKEDITOR.instances.Txt_Glosa.setData("");

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
  
	$( '#table-DetalleProductosOrdenVenta tbody' ).empty();
	$( '#table-DetalleProductosOrdenVenta tfoot' ).empty();
  $( '#table-DetalleProductosOrdenVentaModal thead' ).empty();
  $( '#table-DetalleProductosOrdenVentaModal tbody' ).empty();
	  
	$( '#panel-DetalleProductosOrdenVenta' ).removeClass('panel-danger');
	$( '#panel-DetalleProductosOrdenVenta' ).addClass('panel-default');
  
	$( '#txt-subTotal' ).val( value_importes_cero );
	$( '#span-subTotal' ).text( texto_importes_cero );
	
	$( '#txt-inafecto' ).val( value_importes_cero );
	$( '#span-inafecto' ).text( texto_importes_cero );
	
	$( '#txt-exonerada' ).val( value_importes_cero );
	$( '#span-exonerada' ).text( texto_importes_cero );
	
	$( '#txt-gratuita' ).val( value_importes_cero );
	$( '#span-gratuita' ).text( texto_importes_cero );
	
	$( '#txt-impuesto' ).val( value_importes_cero );
	$( '#span-impuesto' ).text( texto_importes_cero );

	$( '#txt-descuento' ).val( value_importes_cero );
	$( '#span-descuento' ).text( texto_importes_cero );
	
	$( '#txt-total' ).val( value_importes_cero );
	$( '#span-total' ).text( texto_importes_cero );

	$( '#span-total_importe' ).text( texto_importes_cero );
  
  $( '.span-signo' ).text( 'S/' );

  $( '#btn-save' ).attr('disabled', false);
  
  considerar_igv=1;

  $( '.div-MediosPago' ).hide();

  $('#cbo-TiposDocumento').html('');
  url = base_url + 'HelperController/getTiposDocumentos';
  $.post(url, { Nu_Tipo_Filtro: 5}, function (response) {//5 = Cotizacion Venta
    //$('#cbo-TiposDocumento').html('<option value="0" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++)
      $('#cbo-TiposDocumento').append('<option value="' + response[i].ID_Tipo_Documento + '" data-nu_impuesto="' + response[i].Nu_Impuesto + '" data-nu_enlace="' + response[i].Nu_Enlace + '">' + response[i].No_Tipo_Documento_Breve + '</option>');
  }, 'JSON');

  /*
  $('#cbo-lista_precios').html('<option value="0">- Sin lista de precio -</option>');
  url = base_url + 'HelperController/getListaPrecio';
  $.post( url, {Nu_Tipo_Lista_Precio : $( '[name="Nu_Tipo_Lista_Precio"]' ).val(), ID_Organizacion: $( '#header-a-id_organizacion' ).val(), ID_Almacen : 0}, function( responseLista ){
    $('#cbo-lista_precios').html('<option value="0">- Seleccionar -</option>');
    $( '#cbo-lista_precios' ).append( '<option value="' + responseLista[0].ID_Lista_Precio_Cabecera + '">' + responseLista[0].No_Lista_Precio + '</option>' );
  }, 'JSON');
  */

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
  
	/* Personal de ventas */
  $( '#cbo-vendedor' ).html('<option value="" selected="selected">- No hay personal -</option>');
  url = base_url + 'HelperController/getPersonalVentas';
  $.post( url, {}, function( response ){
    if ( response.sStatus == 'success' ) {
      var l = response.arrData.length;
      if (l==1) {
        $( '#cbo-vendedor' ).html('<option value="" selected="selected">- Seleccionar -</option>');
        $( '#cbo-vendedor' ).append( '<option value="' + response.arrData[0].ID + '">' + response.arrData[0].Nombre + '</option>' );
      } else {
        $( '#cbo-vendedor' ).html('<option value="" selected="selected">- Seleccionar -</option>');
        for (var x = 0; x < l; x++) {
          $( '#cbo-vendedor' ).append( '<option value="' + response.arrData[x].ID + '">' + response.arrData[x].Nombre + '</option>' );
        }
      }
    } else {
      if( response.sMessageSQL !== undefined ) {
        console.log(response.sMessageSQL);
      }
      console.log(response.sMessage);
    }
  }, 'JSON');
  /* /. Personal de ventas */

	/* Porcentaje para ventas */
  $( '#cbo-porcentaje' ).html('<option value="" selected="selected">- No hay porcentaje -</option>');
  url = base_url + 'HelperController/getDataGeneral';
  $.post( url, {sTipoData: 'Porcentaje_Comision_Vendedores'}, function( response ){
    if ( response.sStatus == 'success' ) {
      var l = response.arrData.length;
      if (l==1) {
        $( '#cbo-porcentaje' ).html('<option value="" selected="selected">- Seleccionar -</option>');
        $( '#cbo-porcentaje' ).append( '<option value="' + response.arrData[0].ID_Tabla_Dato + '">' + response.arrData[0].No_Descripcion + '</option>' );
      } else {
        $( '#cbo-porcentaje' ).html('<option value="" selected="selected">- Seleccionar -</option>');
        for (var x = 0; x < l; x++) {
          $( '#cbo-porcentaje' ).append( '<option value="' + response.arrData[x].ID_Tabla_Dato + '">' + response.arrData[x].No_Descripcion + '</option>' );
        }
      }
    } else {
      if( response.sMessageSQL !== undefined ) {
        console.log(response.sMessageSQL);
      }
      console.log(response.sMessage);
    }
  }, 'JSON');
  /* /. Porcentaje para ventas */
  
  url = base_url + 'HelperController/getTiposDocumentoIdentidad';
  $.post( url , function( response ){
    $( '#cbo-TiposDocumentoIdentidadCliente' ).html('');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-TiposDocumentoIdentidadCliente' ).append( '<option value="' + response[i]['ID_Tipo_Documento_Identidad'] + '" data-nu_cantidad_caracteres="' + response[i]['Nu_Cantidad_Caracteres'] + '">' + response[i]['No_Tipo_Documento_Identidad_Breve'] + '</option>' );
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

  //Formato PDF
  var arrFormatoPDF = [
    {"No_Formato_PDF": "A4"},
    {"No_Formato_PDF": "A5"},
    {"No_Formato_PDF": "TICKET"},
  ];
  $( '#cbo-formato_pdf' ).html('');
  for (var i = 0; i < arrFormatoPDF.length; i++) {
    $( '#cbo-formato_pdf' ).append( '<option value="' + arrFormatoPDF[i]['No_Formato_PDF'] + '">' + arrFormatoPDF[i]['No_Formato_PDF'] + '</option>' );
  }

  var arrParams = {
    ID_Almacen: $('#cbo-almacen').val(),
  };
  getListaPrecios(arrParams);

  $( '#table-DetalleProductosOrdenVenta' ).hide();
  
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

function verOrdenVenta(ID){
  accion_orden_venta = 'upd_orden_venta';
  $( '#modal-loader' ).modal('show');
  
  $( '.div-Listar' ).hide();
  
  $('.div-adicionales_ov').css("display", "none");
  $('.div-adicionales_ov_garantia_glosa').css("display", "none");

  $('#btn-adicionales_ov').data('mostrar_campos_adicionales', 0);
  $('#btn-adicionales_ov_garantia_glosa').data('ver_adicionales_ov_garantia_glosa', 0);

  $('.panel_body_total_todo').css("display", "none");
  $('#btn-ver_total_todo').data('ver_total_todo', 0);
  $('#btn-ver_total_todo').text('VER / DESCUENTO');

  $( '#txt-EID_Empresa' ).focus();
  
  $( '#form-OrdenVenta' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  CKEDITOR.instances.Txt_Glosa.setData("");

	$( '#panel-DetalleProductosOrdenVenta' ).removeClass('panel-danger');
	$( '#panel-DetalleProductosOrdenVenta' ).addClass('panel-default');
  
  $( '#radio-cliente_existente' ).prop('checked', true).iCheck('update');
  $( '#radio-cliente_nuevo' ).prop('checked', false).iCheck('update');
  $( '.div-cliente_existente' ).show();
  $( '.div-cliente_nuevo' ).hide();
  
  $( '#radio-contacto_existente' ).prop('checked', true).iCheck('update');
  $( '#radio-contacto_nuevo' ).prop('checked', false).iCheck('update');
  $( '.div-contacto_existente' ).show();
  $( '.div-contacto_nuevo' ).hide();
  
  $( '#table-DetalleProductosOrdenVenta tbody' ).empty();
  $( '#table-DetalleProductosOrdenVentaModal thead' ).empty();
  $( '#table-DetalleProductosOrdenVentaModal tbody' ).empty();
      
	$( '#txt-subTotal' ).val( value_importes_cero );
	$( '#span-subTotal' ).text( texto_importes_cero );
	
	$( '#txt-inafecto' ).val( value_importes_cero );
	$( '#span-inafecto' ).text( texto_importes_cero );
	
	$( '#txt-exonerada' ).val( value_importes_cero );
	$( '#span-exonerada' ).text( texto_importes_cero );
	
	$( '#txt-gratuita' ).val( value_importes_cero );
	$( '#span-gratuita' ).text( texto_importes_cero );
	
	$( '#txt-impuesto' ).val( value_importes_cero );
	$( '#span-impuesto' ).text( texto_importes_cero );
	
	$( '#txt-descuento' ).val( value_importes_cero );
	$( '#span-descuento' ).text( texto_importes_cero );
	
	$( '#txt-total' ).val( value_importes_cero );
	$( '#span-total' ).text( texto_importes_cero );
  
	$( '#span-total_importe' ).text( texto_importes_cero );
	
  $('[name="ENu_Estado"]').val('');

  $( '#btn-save' ).attr('disabled', false);

  considerar_igv=1;
	
  url = base_url + 'HelperController/getTiposDocumentoIdentidad';
  $.post( url , function( response ){
    $( '#cbo-TiposDocumentoIdentidadCliente' ).html('');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-TiposDocumentoIdentidadCliente' ).append( '<option value="' + response[i]['ID_Tipo_Documento_Identidad'] + '" data-nu_cantidad_caracteres="' + response[i]['Nu_Cantidad_Caracteres'] + '">' + response[i]['No_Tipo_Documento_Identidad_Breve'] + '</option>' );
  }, 'JSON');
  
  url = base_url + 'HelperController/getTiposDocumentoIdentidad';
  $.post( url , function( response ){
    $( '#cbo-TiposDocumentoIdentidadContacto' ).html('');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-TiposDocumentoIdentidadContacto' ).append( '<option value="' + response[i]['ID_Tipo_Documento_Identidad'] + '" data-nu_cantidad_caracteres="' + response[i]['Nu_Cantidad_Caracteres'] + '">' + response[i]['No_Tipo_Documento_Identidad_Breve'] + '</option>' );
  }, 'JSON');
	
  url = base_url + 'Ventas/OrdenVentaController/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $( '.div-AgregarEditar' ).show();
      
      $( '.title_OrdenVenta' ).text('Modifcar OrdenVenta');
      
      $('[name="EID_Empresa"]').val(response.arrEdit[0].ID_Empresa);
      $('[name="EID_Documento_Cabecera"]').val(response.arrEdit[0].ID_Documento_Cabecera);
      $('[name="ENu_Estado"]').val(response.arrEdit[0].Nu_Estado);
      
      $( '.div-MediosPago' ).hide();
  	  if ( response.arrEdit[0].Nu_Tipo_Forma_Pago == 1)// Si es Crédito
  	    $( '.div-MediosPago' ).show();

      //Datos Documento
      var selected = '';
      url = base_url + 'HelperController/getTiposDocumentos';
      $.post(url, { Nu_Tipo_Filtro: 5 }, function (responseTiposDocumento) {//5 = Cotizacion Venta
        //$('#cbo-TiposDocumento').html('<option value="0" selected="selected">- Seleccionar -</option>');
        $('#cbo-TiposDocumento').html('');
        for (var i = 0; i < responseTiposDocumento.length; i++) {
          selected = '';
          if (response.arrEdit[0].ID_Tipo_Documento == responseTiposDocumento[i].ID_Tipo_Documento)
            selected = 'selected="selected"';
          $('#cbo-TiposDocumento').append('<option value="' + responseTiposDocumento[i].ID_Tipo_Documento + '" data-nu_impuesto="' + responseTiposDocumento[i].Nu_Impuesto + '" data-nu_enlace="' + responseTiposDocumento[i].Nu_Enlace + '" ' + selected + '>' + responseTiposDocumento[i].No_Tipo_Documento_Breve + '</option>');
        }
      }, 'JSON');

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
      
    	/* Personal de ventas */
      $( '#cbo-vendedor' ).html('<option value="">- No hay personal -</option>');
      url = base_url + 'HelperController/getPersonalVentas';
      $.post( url, {}, function( responsePersonal ){
        if ( responsePersonal.sStatus == 'success' ) {
          var l = responsePersonal.arrData.length;
          if (l==1) {
            $( '#cbo-vendedor' ).html('<option value="">- Seleccionar -</option>');
            selected = '';
            if(response.arrEdit[0].ID_Mesero == responsePersonal.arrData[0].ID)
              selected = 'selected="selected"';
            $( '#cbo-vendedor' ).append( '<option value="' + responsePersonal.arrData[0].ID + '" ' + selected + '>' + responsePersonal.arrData[0].Nombre + '</option>' );
          } else {
            $( '#cbo-vendedor' ).html('<option value="" selected="selected">- Seleccionar -</option>');
            for (var x = 0; x < l; x++) {
              selected = '';
              if(response.arrEdit[0].ID_Mesero == responsePersonal.arrData[x].ID)
                selected = 'selected="selected"';
              $( '#cbo-vendedor' ).append( '<option value="' + responsePersonal.arrData[x].ID + '" ' + selected + '>' + responsePersonal.arrData[x].Nombre + '</option>' );
            }
          }
        } else {
          if( responsePersonal.sMessageSQL !== undefined ) {
            console.log(responsePersonal.sMessageSQL);
          }
          console.log(responsePersonal.sMessage);
        }
      }, 'JSON');
      /* /. Personal de ventas */

    	/* Porcentaje para ventas */
      $( '#cbo-porcentaje' ).html('<option value="" selected="selected">- No hay porcentaje -</option>');
      url = base_url + 'HelperController/getDataGeneral';
      $.post( url, {sTipoData: 'Porcentaje_Comision_Vendedores'}, function( responsePorcentaje ){
        if ( responsePorcentaje.sStatus == 'success' ) {
          var l = responsePorcentaje.arrData.length;
          if (l==1) {
            $( '#cbo-porcentaje' ).html('<option value="">- Seleccionar -</option>');
            selected = '';
            if(response.arrEdit[0].ID_Comision == responsePorcentaje.arrData[0].ID_Tabla_Dato)
              selected = 'selected="selected"';
            $( '#cbo-porcentaje' ).append( '<option value="' + responsePorcentaje.arrData[0].ID_Tabla_Dato + '" ' + selected + '>' + responsePorcentaje.arrData[0].No_Descripcion + '</option>' );
          } else {
            $( '#cbo-porcentaje' ).html('<option value="" selected="selected">- Seleccionar -</option>');
            for (var x = 0; x < l; x++) {
              selected = '';
              if(response.arrEdit[0].ID_Comision == responsePorcentaje.arrData[x].ID_Tabla_Dato)
                selected = 'selected="selected"';
              $( '#cbo-porcentaje' ).append( '<option value="' + responsePorcentaje.arrData[x].ID_Tabla_Dato + '" ' + selected + '>' + responsePorcentaje.arrData[x].No_Descripcion + '</option>' );
            }
          }
        } else {
          if( responsePorcentaje.sMessageSQL !== undefined ) {
            console.log(responsePorcentaje.sMessageSQL);
          }
          console.log(responsePorcentaje.sMessage);
        }
      }, 'JSON');
      /* /. Porcentaje para ventas */
      
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

      //Formato PDF
      $( '#cbo-formato_pdf' ).html('');
      var arrFormatoPDF = [
        {"No_Formato_PDF": "A4"},
        {"No_Formato_PDF": "A5"},
        {"No_Formato_PDF": "TICKET"},
      ];
      for (var i = 0; i < arrFormatoPDF.length; i++) {
        selected = '';
        if(response.arrEdit[0].No_Formato_PDF == arrFormatoPDF[i]['No_Formato_PDF'])
          selected = 'selected="selected"';
        $( '#cbo-formato_pdf' ).append( '<option value="' + arrFormatoPDF[i]['No_Formato_PDF'] + '" ' + selected + '>' + arrFormatoPDF[i]['No_Formato_PDF'] + '</option>' );
      }

  	  //CLIENTE
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

      var arrParams = {
        ID_Almacen: $('#cbo-almacen').val(),
      };
      getListaPrecios(arrParams);
      
	    $( '#cbo-lista_precios' ).html('');
      url = base_url + 'HelperController/getListaPrecio';
      $.post( url, {Nu_Tipo_Lista_Precio : $( '[name="Nu_Tipo_Lista_Precio"]' ).val(), ID_Organizacion: response.arrEdit[0].ID_Organizacion, ID_Almacen : response.arrEdit[0].ID_Almacen}, function( responseLista ){
        $('#cbo-lista_precios').html('<option value="0">- Seleccionar -</option>');
        for (var i = 0; i < responseLista.length; i++) {
          selected = '';
          if(response.arrEdit[0].ID_Lista_Precio_Cabecera == responseLista[i].ID_Lista_Precio_Cabecera)
            selected = 'selected="selected"';
          $( '#cbo-lista_precios' ).append( '<option value="' + responseLista[i].ID_Lista_Precio_Cabecera + '" ' + selected + '>' + responseLista[i].No_Lista_Precio + '</option>' );
        }
      }, 'JSON');
      
	    $('[name="Txt_Garantia"]').val( response.arrEdit[0].Txt_Garantia );
      $('[name="Txt_Glosa"]').val( response.arrEdit[0].Txt_Glosa );
      CKEDITOR.instances.Txt_Glosa.setData(response.arrEdit[0].Txt_Glosa);
	    
      //Detalle
      $( '#table-DetalleProductosOrdenVenta' ).show();
      $( '#table-DetalleProductosOrdenVenta tbody' ).empty();
      
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
      var $Ss_Impuesto = 0.00;
      
      var $fDescuento_Producto = 0;
      var fDescuento_Total_Producto = 0;
      var globalImpuesto = 0;
      var $iDescuentoGravada = 0;
      var $iDescuentoExonerada = 0;
      var $iDescuentoInafecto = 0;
      var $iDescuentoGratuita = 0;
      var $iDescuentoGlobalImpuesto = 0, $fTotalIcbper = 0.00;
      var selected;
      
      var iTotalRegistros = response.arrEdit.length;
      var iTotalRegistrosImpuestos = response.arrImpuesto.length;
      for (var i = 0; i < iTotalRegistros; i++) {
        if (_ID_Producto != response.arrEdit[i].ID_Producto) {
          _ID_Producto = response.arrEdit[i].ID_Producto;
          option_impuesto_producto = '';
        }
        
        $Ss_SubTotal_Producto = parseFloat(response.arrEdit[i].Ss_SubTotal_Producto)
        if (response.arrEdit[i].Nu_Tipo_Impuesto == 1) {
          $Ss_Impuesto = parseFloat(response.arrEdit[i].Ss_Impuesto);
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
        $fTotalIcbper += parseFloat(response.arrEdit[i].Ss_Icbper);
        
	      for (var x = 0; x < iTotalRegistrosImpuestos; x++){
	        selected = '';
	        if (response.arrImpuesto[x].ID_Impuesto_Cruce_Documento == response.arrEdit[i].ID_Impuesto_Cruce_Documento)
	          selected = 'selected="selected"';
          option_impuesto_producto += "<option value='" + response.arrImpuesto[x].ID_Impuesto_Cruce_Documento + "' data-nu_tipo_impuesto='" + response.arrImpuesto[x].Nu_Tipo_Impuesto + "' data-impuesto_producto='" + response.arrImpuesto[x].Ss_Impuesto + "' " + selected + ">" + response.arrImpuesto[x].No_Impuesto + "</option>";
	      }
	      
        table_detalle_producto += 
        "<tr id='tr_detalle_producto" + response.arrEdit[i].ID_Producto + "'>"
          +"<td style='display:none;' class='text-left td-iIdItem'>" + response.arrEdit[i].ID_Producto + "</td>"
          +"<td class='text-right'><input type='text' inputmode='decimal' class='pos-input txt-Qt_Producto form-control input-decimal input-size_cantidad' value='" + Math.round10(response.arrEdit[i].Qt_Producto, -3) + "' data-nu_activar_precio_x_mayor=" + response.arrEdit[i].Nu_Activar_Precio_x_Mayor + " data-id_item='" + response.arrEdit[i].ID_Producto + "' data-ss_icbper_item='" + response.arrEdit[i].Ss_Icbper + "' data-ss_icbper='" + response.arrEdit[i].Ss_Icbper_Item + "' data-id_impuesto_icbper='" + response.arrEdit[i].ID_Impuesto_Icbper + "' autocomplete='off'></td>"
          +"<td class='text-left'>" + response.arrEdit[i].Nu_Codigo_Barra + " " + response.arrEdit[i].No_Producto + "</td>"
          +"<td style='display:none; width: 39%' class='text-right td-sNotaItem' data-estado='mostrar' data-id_item=" + response.arrEdit[i].ID_Producto + " id='td-sNotaItem" + response.arrEdit[i].ID_Producto + "'>"
            +"<textarea class='pos-input form-control input-sNotaItem hotkey-cobrar_cliente hotkey-cancelar_venta hotkey-limpiar_item hotkey-focus_item' placeholder='' maxlength='250' autocomplete='off'>" + (response.arrEdit[i].Txt_Nota_Item != null ? response.arrEdit[i].Txt_Nota_Item : '') + "</textarea></td>"
          +"</td>"
          +"<td class='text-center'>"
            +"<button type='button' id='btn-add_nota_producto_pos' class='btn btn-sm btn-link' alt='Nota' title='Nota'><i class='fa fa-edit fa-2x' aria-hidden='true'></i></button>"
          +"</td>"
          +"<td class='text-right'><input type='text' inputmode='decimal' class='pos-input txt-Ss_Precio form-control input-decimal input-size_importe' data-precio_actual='" + Math.round10(response.arrEdit[i].Ss_Precio, -3) + "' value='" + Math.round10(response.arrEdit[i].Ss_Precio, -3) + "' autocomplete='off'></td>"
          +"<td class='text-right'>"
            +"<select class='cbo-ImpuestosProducto form-control required' style='width: 100%;'>"
              +option_impuesto_producto
            +"</select>"
          +"</td>"
          +"<td style='display:none;' class='text-right'><input type='text' inputmode='decimal' class='pos-input txt-Ss_SubTotal_Producto form-control' value='" + response.arrEdit[i].Ss_SubTotal_Producto + "' autocomplete='off' disabled></td>"
          +"<td class='text-right'><input type='text' inputmode='decimal' class='pos-input txt-Ss_Descuento form-control input-decimal input-size_otros' value='" + (response.arrEdit[i].Po_Descuento_Impuesto_Producto == 0.00 ? '' : response.arrEdit[i].Po_Descuento_Impuesto_Producto) + "' autocomplete='off'></td>"
          +"<td class='text-right'><input type='text' inputmode='decimal' class='pos-input txt-Ss_Total_Producto form-control input-decimal input-size_importe' value='" + response.arrEdit[i].Ss_Total_Producto + "' autocomplete='off'></td>"
          +"<td style='display:none;' class='text-right td-fDescuentoSinImpuestosItem'>" + (response.arrEdit[i].Ss_Descuento_Producto == 0.00 ? '' : response.arrEdit[i].Ss_Descuento_Producto) + "</td>"
          +"<td style='display:none;' class='text-right td-fDescuentoImpuestosItem'>" + (response.arrEdit[i].Ss_Descuento_Impuesto_Producto == 0.00 ? '' : response.arrEdit[i].Ss_Descuento_Impuesto_Producto) + "</td>"
          +"<td class='text-center'><button type='button' id='btn-deleteProducto' class='btn btn-sm btn-link' alt='Eliminar' title='Eliminar'><i class='fa fa-trash-o fa-2x' aria-hidden='true'> </i></button></td>"
        +"</tr>";
      }
      
		  $( '#table-DetalleProductosOrdenVenta >tbody' ).append(table_detalle_producto);

      if (parseFloat(response.arrEdit[0].Ss_Descuento) > 0.00 && $Ss_Impuesto > 0) {
        $Ss_Gravada = parseFloat(response.arrEdit[0].Ss_Total) / $Ss_Impuesto;
        $Ss_IGV = parseFloat(response.arrEdit[0].Ss_Total) - $Ss_Gravada;
      }

			$( '#txt-subTotal' ).val( $Ss_Gravada.toFixed(2) );
			$( '#span-subTotal' ).text( $Ss_Gravada.toFixed(2) );
      
      $( '#txt-exonerada' ).val( $Ss_Exonerada.toFixed(2) );
      $( '#span-exonerada' ).text( $Ss_Exonerada.toFixed(2) );
			
			$( '#txt-inafecto' ).val( $Ss_Inafecto.toFixed(2) );
			$( '#span-inafecto' ).text( $Ss_Inafecto.toFixed(2) );
			
			$( '#txt-gratuita' ).val( $Ss_Gratuita.toFixed(2) );
			$( '#span-gratuita' ).text( $Ss_Gratuita.toFixed(2) );
			
      if (parseFloat(response.arrEdit[0].Ss_Descuento) > 0 && $Ss_Descuento_Producto == 0)
        $( '#txt-Ss_Descuento' ).val( response.arrEdit[0].Po_Descuento );
      else
        $( '#txt-Ss_Descuento' ).val( '' );
      
      $( '#txt-descuento' ).val( response.arrEdit[0].Ss_Descuento );
      $( '#span-descuento' ).text( response.arrEdit[0].Ss_Descuento );
			
			$( '#txt-impuesto' ).val( $Ss_IGV.toFixed(2) );
      $('#span-impuesto').text($Ss_IGV.toFixed(2));

      $('#txt-total_icbper').val($fTotalIcbper.toFixed(2));
      $('#span-total_icbper').text($fTotalIcbper.toFixed(2));

      $('#txt-total').val(parseFloat(response.arrEdit[0].Ss_Total).toFixed(2));
      $('#span-total').text(parseFloat(response.arrEdit[0].Ss_Total).toFixed(2));
      $('#span-total_importe').text(parseFloat(response.arrEdit[0].Ss_Total).toFixed(2));
  			
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
    }
  })
}

function eliminarOrdenVenta(ID, Nu_Descargar_Inventario, accion_orden_venta){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '.modal-message-delete' ).removeClass('modal-danger modal-warning modal-success');
  $( '.modal-message-delete' ).addClass('modal-danger');
  
  $( '.modal-title-message-delete' ).text('¿Deseas eliminar la orden de venta?');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });

  accion_orden_venta='delete';

  $(document).bind('keydown', 'return', function(){
    if ( accion_orden_venta=='delete' ) {
      _eliminarOrdenVenta($modal_delete, ID);
      accion_orden_venta='';
    }
  });

  $( '#btn-save-delete' ).off('click').click(function () {
    _eliminarOrdenVenta($modal_delete, ID, Nu_Descargar_Inventario);
    accion_orden_venta='';
  });
}

$(function () {
  $('[data-mask]').inputmask();

  // Generar Guía
  $('#btn-generar_guia').click(function () {
    //peso bruto debe de ser igual o mayor a 1
    //validacion de 01 transporte publico
    //si es guía electrónica, y es transporte publico, el transportista debe de ser tipo RUC y no puede ser la misma empresa, placa y licencia opcional
    //validacion de 02 transporte privado
    //si es guía electrónica, y es transporte publico, el transportista debe ingresar nombre, placa y licencia

    $('.help-block').empty();
    $('.form-group').removeClass('has-error');
    if ($('#cbo-transporte').val() == 0) {
      $('#cbo-transporte').closest('.form-group').find('.help-block').html('Seleccionar transporte');
      $('#cbo-transporte').closest('.form-group').removeClass('has-success').addClass('has-error');

      scrollToError($('.modal-generar_guia .modal-body'), $('#cbo-transporte'));
    } else if ($('[name="radio-TipoDocumento"]:checked').attr('value') == 8 && $('#txt-Txt_Direccion_Entidad-modal').val().length == 0) {
      $('#txt-Txt_Direccion_Entidad-modal').closest('.form-group').find('.help-block').html('Ingresar dirección');
      $('#txt-Txt_Direccion_Entidad-modal').closest('.form-group').removeClass('has-success').addClass('has-error');

      scrollToError($('.modal-generar_guia .modal-body'), $('#txt-No_Placa-modal'));
    } else if ($('[name="radio-TipoDocumento"]:checked').attr('value') == 8 && $('#cbo-ubigeo_inei-modal').val() == 0) {
      $('#cbo-ubigeo_inei-modal').closest('.form-group').find('.help-block').html('Seleccionar ubigeo');
      $('#cbo-ubigeo_inei-modal').closest('.form-group').removeClass('has-success').addClass('has-error');

      scrollToError($('.modal-generar_guia .modal-body'), $('#cbo-ubigeo_inei-modal'));
    } else if ($('[name="radio-TipoDocumento"]:checked').attr('value') == 8 && (parseFloat($('#txt-Ss_Peso_Bruto').val()) < 1 || isNaN(parseFloat($('#txt-Ss_Peso_Bruto').val())))) {
      $('#txt-Ss_Peso_Bruto').closest('.form-group').find('.help-block').html('Peso bruto debe ser 1 o mayor');
      $('#txt-Ss_Peso_Bruto').closest('.form-group').removeClass('has-success').addClass('has-error');

      scrollToError($('.modal-generar_guia .modal-body'), $('#txt-Ss_Peso_Bruto'));
    } else if ($('[name="radio-TipoDocumento"]:checked').attr('value') == 8 && $('[name="radio-TipoTransporte"]:checked').attr('value') == '01' && $( '#cbo-transporte' ).find(':selected').data('id_tipo_documento_identidad') != '4') {//01=publico y id tipo de documento = 4
      alert('TRANSPORTE PUBLICO: Transportista debe ser tipo RUC');
    } else if ($('[name="radio-TipoDocumento"]:checked').attr('value') == 8 && $('[name="radio-TipoTransporte"]:checked').attr('value') == '01' && $('#hidden-Nu_Documento_Identidad-empresa').val() == $( '#cbo-transporte' ).find(':selected').data('numero_documento_identidad')) {//01=publico
      alert('TRANSPORTE PUBLICO: El RUC de Transportista no puede ser igual al RUC de la Empresa');
    } else if ($('[name="radio-TipoDocumento"]:checked').attr('value') == 8 && $('[name="radio-TipoTransporte"]:checked').attr('value') == '02' && $('#txt-No_Placa').val().length < 6) {
      $('#txt-No_Placa').closest('.form-group').find('.help-block').html('Ingresar Placa mínimo 6 dígitos');
      $('#txt-No_Placa').closest('.form-group').removeClass('has-success').addClass('has-error');

      scrollToError($('.modal-generar_guia .modal-body'), $('#txt-No_Placa'));
    } else if ($('[name="radio-TipoDocumento"]:checked').attr('value') == 8 && $('[name="radio-TipoTransporte"]:checked').attr('value') == '02' && $('#txt-No_Licencia').val().length < 9) {
      $('#txt-No_Licencia').closest('.form-group').find('.help-block').html('Ingresar Licencia mínimo 9 dígitos');
      $('#txt-No_Licencia').closest('.form-group').removeClass('has-success').addClass('has-error');

      scrollToError($('.modal-generar_guia .modal-body'), $('#txt-No_Licencia'));
    } else {
      $('#btn-generar_guia').text('');
      $('#btn-generar_guia').attr('disabled', true);
      $('#btn-generar_guia').append('Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>');
      $('#btn-salir').attr('disabled', true);

      url = base_url + 'Ventas/VentaController/generarGuia';
      $.ajax({
        type: 'POST',
        dataType: 'JSON',
        url: url,
        data: $('#form-generar_guia').serialize(),
        success: function (response) {
          $('.modal-message').removeClass('modal-danger modal-warning modal-success');
          $('#modal-message').modal('show');

          if (response.sStatus == 'success') {
            $('.modal-generar_guia').modal('hide');

            $('.modal-message').addClass('modal-' + response.sStatus);
            $('.modal-title-message').text(response.sMessage);
            setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);

            reload_table_orden_venta();

            // Mandar a imprimir impresora
            var Accion = 'imprimir', iIdDocumentoCabecera = response.iIdDocumentoCabecera, url_print = 'ocultar-img-logo_punto_venta_click';
            formatoImpresionTicketGuia(Accion, iIdDocumentoCabecera, url_print);
          } else {
            $('.modal-message').addClass('modal-' + response.sStatus);
            $('.modal-title-message').text(response.sMessage);
            if (response.sStatus == 'danger') {
              $('.modal-generar_guia').modal('hide');
              setTimeout(function () { $('#modal-message').modal('hide'); }, 3100);
            }
          }

          $('#btn-generar_guia').text('');
          $('#btn-generar_guia').append('Generar Guia');
          $('#btn-generar_guia').attr('disabled', false);
          $('#btn-salir').attr('disabled', false);
        }

      })
      .fail(function (jqXHR, textStatus, errorThrown) {
        $('.modal-message').removeClass('modal-danger modal-warning modal-success');

        $('#modal-message').modal('show');
        $('.modal-message').addClass('modal-danger');
        $('.modal-title-message').text(textStatus + ' [' + jqXHR.status + ']: ' + errorThrown);
        setTimeout(function () { $('#modal-message').modal('hide'); }, 3100);

        //Message for developer
        console.log(jqXHR.responseText);

        $('#btn-generar_guia').text('');
        $('#btn-generar_guia').attr('disabled', false);
        $('#btn-generar_guia').append('Generar Guia');
        $('#btn-salir').attr('disabled', false);
      })
    }// ./ if - else validacion
  })// ./ Generar Guía

  CKEDITOR.replace( 'Txt_Glosa', {
    height: 100, 
    extraPlugins: 'wordcount', 
    wordcount: {
      showParagraphs: false,
      showWordCount: false,
      showCharCount: true,
      countSpacesAsChars:true,
      countHTML:true,
      maxWordCount: -1,
      maxCharCount: 10000,//word limit
      charLimit: 10000//word limit
    },
  });

  //adicionles de orden de venta
  $('#btn-adicionales_ov').click(function () {
    if ($(this).data('mostrar_campos_adicionales') == 1) {
      //setter
      $('#btn-adicionales_ov').data('mostrar_campos_adicionales', 0);
    } else {
      $('#btn-adicionales_ov').data('mostrar_campos_adicionales', 1);
    }

    if ($(this).data('mostrar_campos_adicionales') == 1) {
      $('.div-adicionales_ov').css("display", "");
    } else {
      $('.div-adicionales_ov').css("display", "none");
    }
  })
  
  $('#btn-adicionales_ov_garantia_glosa').click(function () {
    if ($(this).data('ver_adicionales_ov_garantia_glosa') == 1) {
      //setter
      $('#btn-adicionales_ov_garantia_glosa').data('ver_adicionales_ov_garantia_glosa', 0);
    } else {
      $('#btn-adicionales_ov_garantia_glosa').data('ver_adicionales_ov_garantia_glosa', 1);
    }

    if ($(this).data('ver_adicionales_ov_garantia_glosa') == 1) {
      $('.div-adicionales_ov_garantia_glosa').css("display", "");
    } else {
      $('.div-adicionales_ov_garantia_glosa').css("display", "none");
    }
  })

  $('#btn-ver_total_todo').click(function () {
    if ($(this).data('ver_total_todo') == 1) {
      //setter
      $('#btn-ver_total_todo').data('ver_total_todo', 0);
      $('#btn-ver_total_todo').text('VER / DESCUENTO');
    } else {
      $('#btn-ver_total_todo').data('ver_total_todo', 1);
      $('#btn-ver_total_todo').text('Ocultar');
    }

    if ($(this).data('ver_total_todo') == 1) {
      $('.panel_body_total_todo').css("display", "");
    } else {
      $('.panel_body_total_todo').css("display", "none");
    }
  })

  $('#txt-Filtro_Fe_Inicio').val(fDay + '/' + fMonth + '/' + fYear);
  $('#txt-Filtro_Fe_Fin').val(fDay + '/' + fMonth + '/' + fYear);

  //Nota en detalle de cada ítem
  $('#table-DetalleProductosOrdenVenta > tbody').on('click', '#btn-add_nota_producto_pos', function () {
    var fila = $(this).parents("tr");
    var id_item = fila.find(".td-sNotaItem").data("id_item");
    var estado = fila.find(".td-sNotaItem").data("estado");

    if (estado == 'mostrar') {
      fila.find("#td-sNotaItem" + id_item).show();
      fila.find(".td-sNotaItem").data("estado", "ocultar");
      fila.find(".td-sNombreItem").css("width", "5%");
      fila.find(".input-sNotaItem").focus();
    } else {
      fila.find("#td-sNotaItem" + id_item).hide();
      fila.find(".td-sNotaItem").data("estado", "mostrar");
      fila.find(".td-sNombreItem").css("width", "44%");
    }
  })

  /*
  url = base_url + 'HelperController/getTiposDocumentos';
  $.post(url, { Nu_Tipo_Filtro: 5 }, function (response) {//5 = Cotizacion Venta
    $('#cbo-Filtro_TiposDocumento').html('<option value="0" selected="selected">- Todos -</option>');
    //$('#cbo-Filtro_TiposDocumento').html('');
    for (var i = 0; i < response.length; i++)
      $('#cbo-Filtro_TiposDocumento').append('<option value="' + response[i].ID_Tipo_Documento + '" data-nu_impuesto="' + response[i].Nu_Impuesto + '" data-nu_enlace="' + response[i].Nu_Enlace + '">' + response[i].No_Tipo_Documento_Breve + '</option>');
  }, 'JSON');
  */

  //Flat red color scheme for iCheck
  $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
    checkboxClass: 'icheckbox_flat-green',
    radioClass: 'iradio_flat-green'
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
  $( '#btn-cloud-api_orden_venta_cliente' ).click(function(){
    if ( $( '#cbo-TiposDocumentoIdentidadCliente' ).val().length === 0){
      $( '#cbo-TiposDocumentoIdentidadCliente' ).closest('.form-group').find('.help-block').html('Seleccionar tipo doc. identidad');
  	  $( '#cbo-TiposDocumentoIdentidadCliente' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ($( '#cbo-TiposDocumentoIdentidadCliente' ).find(':selected').data('nu_cantidad_caracteres') != $( '#txt-Nu_Documento_Identidad_Cliente').val().length ) {
      $( '#txt-Nu_Documento_Identidad_Cliente' ).closest('.form-group').find('.help-block').html('Debe ingresar ' + $( '#cbo-TiposDocumentoIdentidadCliente' ).find(':selected').data('nu_cantidad_caracteres') + ' dígitos' );
  	  $( '#txt-Nu_Documento_Identidad_Cliente' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if (
      (
        $( '#cbo-TiposDocumentoIdentidadCliente' ).val() == 1 ||
        $( '#cbo-TiposDocumentoIdentidadCliente' ).val() == 3 ||
        $( '#cbo-TiposDocumentoIdentidadCliente' ).val() == 5 ||
        $( '#cbo-TiposDocumentoIdentidadCliente' ).val() == 6
      )
      ) {
      $( '#cbo-TiposDocumentoIdentidadCliente' ).closest('.form-group').find('.help-block').html('Disponible DNI / RUC');
  	  $( '#cbo-TiposDocumentoIdentidadCliente' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      $( '#btn-cloud-api_orden_venta_cliente' ).text('');
      $( '#btn-cloud-api_orden_venta_cliente' ).attr('disabled', true);
      $( '#btn-cloud-api_orden_venta_cliente' ).append( '<i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
      
      // Obtener datos de SUNAT y RENIEC
      var url_api = 'https://www.laesystems.com/librerias/sunat/partner/format/json/x-api-key/';
			if ( $( '#cbo-TiposDocumentoIdentidadCliente' ).val() == 2 )//2=RENIEC
				url_api = 'https://www.laesystems.com/librerias/reniec/partner/format/json/x-api-key/';
			url_api = url_api + sTokenGlobal;
			
		  var data = {
        ID_Tipo_Documento_Identidad : $( '#cbo-TiposDocumentoIdentidadCliente' ).val(),
        Nu_Documento_Identidad : $( '#txt-Nu_Documento_Identidad_Cliente' ).val(),
      };
      
      $.ajax({
        url   : url_api,
        type  :'POST',
        data  : data,
        success: function(response){
          $( '#btn-cloud-api_orden_venta_cliente' ).closest('.form-group').find('.help-block').html('');
      	  $( '#btn-cloud-api_orden_venta_cliente' ).closest('.form-group').removeClass('has-success').addClass('has-error');
      	  
          if (response.success == true){
            $( '#txt-No_Entidad_Cliente' ).val( response.data.No_Names );
            if ( $( '#cbo-TiposDocumentoIdentidadCliente' ).val() == 4) {//RUC
              if (response.data.Txt_Address != null)
                $( '#txt-Txt_Direccion_Entidad_Cliente' ).val( response.data.Txt_Address );
              if (response.data.Nu_Phone != null )
                $( '#txt-Nu_Telefono_Entidad_Cliente' ).val( response.data.Nu_Phone );
              if (response.data.Nu_Cellphone != null )
                $( '#txt-Nu_Celular_Entidad_Cliente' ).val( response.data.Nu_Cellphone );
            }
          } else {
            $( '#txt-No_Entidad_Cliente' ).val( '' );
            if ( $( '#cbo-TiposDocumentoIdentidadCliente' ).val() == 4) {//RUC
              $( '#txt-Txt_Direccion_Entidad_Cliente' ).val( '' );
              $( '#txt-Nu_Telefono_Entidad_Cliente' ).val( '' );
              $( '#txt-Nu_Celular_Entidad_Cliente' ).val( '' );
              $( '#txt-Txt_Email_Entidad' ).val( '' );
            }
            $( '#txt-Nu_Documento_Identidad_Cliente' ).closest('.form-group').find('.help-block').html(response.msg);
        	  $( '#txt-Nu_Documento_Identidad_Cliente' ).closest('.form-group').removeClass('has-success').addClass('has-error');
        	  
  		  	  $( '#txt-Nu_Documento_Identidad_Cliente' ).focus();
  		  	  $( '#txt-Nu_Documento_Identidad_Cliente' ).select();
          }
  		  	
          $( '#btn-cloud-api_orden_venta_cliente' ).text('');
          $( '#btn-cloud-api_orden_venta_cliente' ).attr('disabled', false);
          $( '#btn-cloud-api_orden_venta_cliente' ).append( '<i class="fa fa-cloud-download fa-lg"></i>' );
        },
        error: function(response){
          $( '#btn-cloud-api_orden_venta_cliente' ).closest('.form-group').find('.help-block').html('Sin acceso');
      	  $( '#btn-cloud-api_orden_venta_cliente' ).closest('.form-group').removeClass('has-success').addClass('has-error');
      	  
          $( '#txt-No_Entidad_Cliente' ).val( '' );
          $( '#txt-Txt_Direccion_Entidad_Cliente' ).val( '' );
          $( '#txt-Nu_Telefono_Entidad_Cliente' ).val( '' );
          $( '#txt-Nu_Celular_Entidad_Cliente' ).val( '' );
          $( '#txt-Txt_Email_Entidad' ).val( '' );
              
          $( '#btn-cloud-api_orden_venta_cliente' ).text('');
          $( '#btn-cloud-api_orden_venta_cliente' ).attr('disabled', false);
          $( '#btn-cloud-api_orden_venta_cliente' ).append( '<i class="fa fa-cloud-download fa-lg"></i>' );
        }
      });
    }
  })
  
  //LAE API SUNAT / RENIEC - CONTACTO
  $( '#btn-cloud-api_orden_venta_contacto' ).click(function(){
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
      $( '#btn-cloud-api_orden_venta_contacto' ).text('');
      $( '#btn-cloud-api_orden_venta_contacto' ).attr('disabled', true);
      $( '#btn-cloud-api_orden_venta_contacto' ).append( '<i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
      
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
          $( '#btn-cloud-api_orden_venta_contacto' ).closest('.form-group').find('.help-block').html('');
      	  $( '#btn-cloud-api_orden_venta_contacto' ).closest('.form-group').removeClass('has-success').addClass('has-error');
      	  
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
  		  	
          $( '#btn-cloud-api_orden_venta_contacto' ).text('');
          $( '#btn-cloud-api_orden_venta_contacto' ).attr('disabled', false);
          $( '#btn-cloud-api_orden_venta_contacto' ).append( '<i class="fa fa-cloud-download fa-lg"></i>' );
        },
        error: function(response){
          $( '#btn-cloud-api_orden_venta_contacto' ).closest('.form-group').find('.help-block').html('Sin acceso');
      	  $( '#btn-cloud-api_orden_venta_contacto' ).closest('.form-group').removeClass('has-success').addClass('has-error');
      	  
          $( '#txt-No_Contacto' ).val( '' );
          $( '#txt-Nu_Telefono_Contacto' ).val( '' );
          $( '#txt-Nu_Celular_Contacto' ).val( '' );
              
          $( '#btn-cloud-api_orden_venta_contacto' ).text('');
          $( '#btn-cloud-api_orden_venta_contacto' ).attr('disabled', false);
          $( '#btn-cloud-api_orden_venta_contacto' ).append( '<i class="fa fa-cloud-download fa-lg"></i>' );
        }
      });
    }
  })
  
	/* Tipo Documento Identidad Cliente */
	$( '#cbo-TiposDocumentoIdentidadCliente' ).change(function(){
	  if ( $(this).val() == 2 ) {//DNI
		  $( '#label-Nombre_Documento_Identidad_Cliente' ).html('DNI');
		  $( '#label-No_Entidad_Cliente' ).text('Nombre y Apellidos');
			$( '#txt-Nu_Documento_Identidad_Cliente' ).attr('maxlength', $(this).find(':selected').data('nu_cantidad_caracteres'));
      $( '#txt-Nu_Documento_Identidad_Cliente' ).attr("placeholder", "Opcional");
	  } else if ( $(this).val() == 4 ) {//RUC
		  $( '#label-Nombre_Documento_Identidad_Cliente' ).text('RUC');
		  $( '#label-No_Entidad_Cliente' ).text('Razón Social');
			$( '#txt-Nu_Documento_Identidad_Cliente' ).attr('maxlength', $(this).find(':selected').data('nu_cantidad_caracteres'));
      $( '#txt-Nu_Documento_Identidad_Cliente' ).attr("placeholder", "Ingresar RUC");
	  } else {
	    $( '#label-Nombre_Documento_Identidad_Cliente' ).html('OTROS');
		  $( '#label-No_Entidad_Cliente' ).text('Nombre y Apellidos');
			$( '#txt-Nu_Documento_Identidad_Cliente' ).attr('maxlength', $(this).find(':selected').data('nu_cantidad_caracteres'));
      $( '#txt-Nu_Documento_Identidad_Cliente' ).attr("placeholder", "Opcional");
	  }
	})
	
	/* Tipo Documento Identidad Contacto */
	$( '#cbo-TiposDocumentoIdentidadContacto' ).change(function(){
	  if ( $(this).val() == 2 ) {//DNI
		  $( '#label-Nombre_Documento_Identidad' ).text('DNI');
		  $( '#label-No_Contacto' ).text('Nombre y Apellidos');
			$( '#txt-Nu_Documento_Identidad' ).attr('maxlength', $(this).find(':selected').data('nu_cantidad_caracteres'));
	  } else if ( $(this).val() == 4 ) {//RUC
		  $( '#label-Nombre_Documento_Identidad' ).text('RUC');
		  $( '#label-No_Contacto' ).text('Razón Social');
			$( '#txt-Nu_Documento_Identidad' ).attr('maxlength', $(this).find(':selected').data('nu_cantidad_caracteres'));
	  } else {
	    $( '#label-Nombre_Documento_Identidad' ).text('# Documento Identidad');
		  $( '#label-No_Contacto' ).text('Nombre y Apellidos');
			$( '#txt-Nu_Documento_Identidad' ).attr('maxlength', $(this).find(':selected').data('nu_cantidad_caracteres'));
	  }
	})
	
  url = base_url + 'HelperController/getOrganizaciones';
  $.post( url , function( response ){
    if (response.length == 1) {
      $( '#cbo-filtro-organizacion' ).html( '<option value="' + response[0].ID_Organizacion + '">' + response[0].No_Organizacion + '</option>' );
    } else {
      $( '#cbo-filtro-organizacion' ).html('<option value="" selected="selected">- Todos -</option>');
      for (var i = 0; i < response.length; i++)
        $( '#cbo-filtro-organizacion' ).append( '<option value="' + response[i].ID_Organizacion + '">' + response[i].No_Organizacion + '</option>' );
    }
  }, 'JSON');

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


  /* Personal de ventas */
  $('#cbo-filtro-vendedor').html('<option value="0" selected="selected">- Todos -</option>');
  url = base_url + 'HelperController/getPersonalVentas';
  $.post(url, {}, function (response) {
    if (response.sStatus == 'success') {
      var l = response.arrData.length;
      if (l == 1) {
        $('#cbo-filtro-vendedor').html('<option value="0" selected="selected">- Todos -</option>');
        $('#cbo-filtro-vendedor').append('<option value="' + response.arrData[0].ID + '">' + response.arrData[0].Nombre + '</option>');
      } else {
        $('#cbo-filtro-vendedor').html('<option value="0" selected="selected">- Todos -</option>');
        for (var x = 0; x < l; x++) {
          $('#cbo-filtro-vendedor').append('<option value="' + response.arrData[x].ID + '">' + response.arrData[x].Nombre + '</option>');
        }
      }
    } else {
      if (response.sMessageSQL !== undefined) {
        console.log(response.sMessageSQL);
      }
      console.log(response.sMessage);
    }
  }, 'JSON');
  /* /. Personal de ventas */

  url = base_url + 'Ventas/OrdenVentaController/ajax_list';
  table_orden_venta = $('#table-OrdenVenta').DataTable({
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
      'data': function (data) {
        data.sMethod = $('#hidden-sMethod').val(),
        data.filtro_almacen = $('#cbo-filtro_almacen').val(),
        data.filtro_vendedor = $('#cbo-filtro-vendedor').val(),
        data.Filtro_TiposDocumento = $('#cbo-Filtro_TiposDocumento option:selected').val(),
        data.Filtro_Fe_Inicio       = ParseDateString($( '#txt-Filtro_Fe_Inicio' ).val(), 1, '/'),
        data.Filtro_Fe_Fin          = ParseDateString($( '#txt-Filtro_Fe_Fin' ).val(), 1, '/'),
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
    table_orden_venta.ajax.reload();
  });
  
  $( '#form-OrdenVenta' ).validate({
		rules:{
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
		submitHandler: form_OrdenVenta
	});
	
	$( '#cbo-Monedas' ).change(function(){
	  if ( $(this).val() > 0 )
	    $( '.span-signo' ).text( $(this).find(':selected').data('no_signo') );
	})

	$( '.div-MediosPago' ).hide();
	$( '#cbo-MediosPago' ).change(function(){
	  $( '.div-MediosPago' ).hide();
	  if ( $(this).find(':selected').data('nu_tipo') == 1 )// Si es Crédito
	    $( '.div-MediosPago' ).show();
	})

  $('#cbo-almacen').change(function () {
    var arrParams = {
      ID_Almacen: $('#cbo-almacen').val(),
    };
    getListaPrecios(arrParams);
  })

  var _ID_Producto = '';
  var option_impuesto_producto = '';
	$( '#btn-addProductoOrdenVenta' ).click(function(){
	  var $ID_Producto                  = $( '#txt-ID_Producto' ).val();
    var $Nu_Codigo_Barra              = $( '#txt-Nu_Codigo_Barra' ).val();
    var $No_Producto                  = $( '#txt-No_Producto' ).val();
    var $Ss_Precio                    = parseFloat($( '#txt-Ss_Precio' ).val());
    var $ID_Impuesto_Cruce_Documento  = $( '#txt-ID_Impuesto_Cruce_Documento' ).val();
    var $Nu_Tipo_Impuesto             = $( '#txt-Nu_Tipo_Impuesto' ).val();
    var $Ss_Impuesto                  = $( '#txt-Ss_Impuesto' ).val();
    var $ID_Impuesto_Icbper = $('#txt-ID_Impuesto_Icbper').val();
    var $Ss_Icbper = $('#txt-Ss_Icbper').val();
    var $Nu_Activar_Precio_x_Mayor = $('#txt-nu_activar_precio_x_mayor').val()
    
    bEstadoValidacion = validatePreviousDocumentToSaveOrderSale();
    
    if ( $ID_Producto.length === 0 || $No_Producto.length === 0) {
	    $( '#txt-No_Producto' ).closest('.form-group').find('.help-block').html('Ingresar producto');
			$( '#txt-No_Producto' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if (bEstadoValidacion){
      /*
      if (iValidarStockGlobal == 1 && $( '#txt-nu_tipo_item' ).val() == 1 && (parseFloat($( '#txt-Qt_Producto' ).val()) <= 0.000000 || $( '#txt-Qt_Producto' ).val() == 0) ) {
        $modal_msg_stock = $( '.modal-message' );
        $modal_msg_stock.modal('show');
    
        $modal_msg_stock.removeClass('modal-danger modal-warning modal-success');
        $modal_msg_stock.addClass('modal-warning');
    
        $( '.modal-title-message' ).text('Sin stock disponible');
    
        setTimeout(function() {$modal_msg_stock.modal('hide');}, 1300);
      } else {
        */
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
          +"<td style='display:none;' class='text-left td-iIdItem'>" + $ID_Producto + "</td>"
          + "<td class='text-right'><input type='text' inputmode='decimal' id=" + $ID_Producto + " class='pos-input txt-Qt_Producto form-control input-decimal input-size_cantidad' data-id_item='" + $ID_Producto + "' data-nu_activar_precio_x_mayor=" + $Nu_Activar_Precio_x_Mayor + " data-ss_icbper_item='0.00' data-ss_icbper='" + $Ss_Icbper + "' data-id_impuesto_icbper='" + $ID_Impuesto_Icbper + "' data-id_producto='" + $ID_Producto + "' value='1' autocomplete='off'></td>"
          +"<td class='text-left'>" + $Nu_Codigo_Barra + " " + $No_Producto + "</td>"
          +"<td style='display:none; width: 39%' class='text-right td-sNotaItem' data-estado='mostrar' data-id_item=" + $ID_Producto + " id='td-sNotaItem" + $ID_Producto + "'>"
            +"<textarea class='pos-input form-control input-sNotaItem hotkey-cobrar_cliente hotkey-cancelar_venta hotkey-limpiar_item hotkey-focus_item' placeholder='' maxlength='250' autocomplete='off'></textarea></td>"
          +"</td>"
          +"<td class='text-center'>"
            +"<button type='button' id='btn-add_nota_producto_pos' class='btn btn-sm btn-link' alt='Nota' title='Nota'><i class='fa fa-edit fa-2x' aria-hidden='true'></i></button>"
          +"</td>"
          +"<td class='text-right'><input type='text' inputmode='decimal' class='pos-input txt-Ss_Precio form-control input-decimal input-size_importe' data-id_producto='" + $ID_Producto + "' data-precio_actual='" + $Ss_Precio + "' value='" + $Ss_Precio + "' autocomplete='off'></td>"
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
          
          $( '#table-DetalleProductosOrdenVenta' ).show();
          $( '#table-DetalleProductosOrdenVenta >tbody' ).append(table_detalle_producto);
          
          $( '#' + $ID_Producto ).focus();
          $( '#' + $ID_Producto ).select();
          
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
          $("#table-DetalleProductosOrdenVenta > tbody > tr").each(function(){
            var rows = $(this);
            var fImpuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
            var iGrupoImpuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
            var $Ss_SubTotal_Producto = parseFloat($('.txt-Ss_SubTotal_Producto', this).val());
            var $Ss_Descuento_Producto = parseFloat($('.txt-Ss_Descuento', this).val());
            var $Ss_Total_Producto = parseFloat($('.txt-Ss_Total_Producto', this).val());
      
            $Ss_Total += $Ss_Total_Producto;

            if (iGrupoImpuesto == 1) {
              $Ss_SubTotal += $Ss_SubTotal_Producto;
              $Ss_IGV += $Ss_Total_Producto - $Ss_SubTotal_Producto;
              globalImpuesto = fImpuesto;
            } else if (iGrupoImpuesto == 2) {
              $Ss_Inafecto += $Ss_SubTotal_Producto;
              globalImpuesto += 0;
            } else if (iGrupoImpuesto == 3) {
              $Ss_Gratuita += $Ss_SubTotal_Producto;
              globalImpuesto += 0;
            } else {
              $Ss_Exonerada += $Ss_SubTotal_Producto;
              globalImpuesto += 0;
            }
              
            if(isNaN($Ss_Descuento_Producto))
              $Ss_Descuento_Producto = 0;
              
            $Ss_Descuento_p += (($Ss_Descuento_Producto * (parseFloat($('.txt-Qt_Producto', this).val()) * parseFloat($('.txt-Ss_Precio', this).val()))) / 100);
          });
          
          if ($Ss_SubTotal > 0.00 || $Ss_Inafecto > 0.00 || $Ss_Exonerada > 0.00 || $Ss_Gratuita > 0.00) {
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
            
            $( '#txt-inafecto' ).val( $Ss_Inafecto.toFixed(2) );
            $( '#span-inafecto' ).text( $Ss_Inafecto.toFixed(2) );
            
            $( '#txt-exonerada' ).val( $Ss_Exonerada.toFixed(2) );
            $( '#span-exonerada' ).text( $Ss_Exonerada.toFixed(2) );
            
            $( '#txt-gratuita' ).val( $Ss_Gratuita.toFixed(2) );
            $( '#span-gratuita' ).text( $Ss_Gratuita.toFixed(2) );
              
            $( '#txt-impuesto' ).val( $Ss_IGV.toFixed(2) );
            $( '#span-impuesto' ).text( $Ss_IGV.toFixed(2) );
            
            $( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
            $( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
        
            $( '#txt-total' ).val( $Ss_Total.toFixed(2) );
            $( '#span-total' ).text( $Ss_Total.toFixed(2) );
            $( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
          }
          
          validateDecimal();
          validateNumber();
          validateNumberOperation();

          calcularIcbper();
        }
      //} // if - else validacion de stock
    }
	})

  $('#table-DetalleProductosOrdenVenta tbody' ).on('input', '.txt-Ss_Precio', function(){
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
	    $( '#table-DetalleProductosOrdenVenta tfoot' ).empty();
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
        $("#table-DetalleProductosOrdenVenta > tbody > tr").each(function(){
          var rows = $(this);
          var Ss_Impuesto           = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
          var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat($('.txt-Ss_SubTotal_Producto', this).val());
          var $Ss_Descuento_Producto = parseFloat($('.txt-Ss_Descuento', this).val());
          var $Ss_Total_Producto = parseFloat($('.txt-Ss_Total_Producto', this).val());
    
          $Ss_Total += $Ss_Total_Producto;

          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
            
          if (Nu_Tipo_Impuesto == 1){
            $Ss_SubTotal += $Ss_SubTotal_Producto;
            $Ss_IGV += $Ss_Total_Producto - $Ss_SubTotal_Producto;
          }
          
          $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat($('.txt-Qt_Producto', this).val()) * parseFloat($('.txt-Ss_Precio', this).val()))) / 100) / impuesto_producto);
        });
        $( '#txt-subTotal' ).val( $Ss_SubTotal.toFixed(2) );
    		$( '#span-subTotal' ).text( $Ss_SubTotal.toFixed(2) );
    		
    		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
    		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
    		
    		$( '#txt-impuesto' ).val( $Ss_IGV.toFixed(2) );
    		$( '#span-impuesto' ).text( $Ss_IGV.toFixed(2) );
    		
    		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
    		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
    		$( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
      } else if (nu_tipo_impuesto == 2) {//Inafecto
        fila.find(".td-fDescuentoSinImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_Total_Producto").val((parseFloat(((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)) * impuesto_producto).toFixed(2)).toString().split(". "));
        
        var $Ss_Inafecto = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_IGV = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleProductosOrdenVenta > tbody > tr").each(function(){
          var rows = $(this);
          var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat($('.txt-Ss_SubTotal_Producto', this).val());
          var $Ss_Descuento_Producto = parseFloat($('.txt-Ss_Descuento', this).val());

          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
            
          if (Nu_Tipo_Impuesto == 2)
            $Ss_Inafecto += $Ss_SubTotal_Producto;
          
          $Ss_Descuento += (($Ss_Descuento_Producto * (parseFloat($('.txt-Qt_Producto', this).val()) * parseFloat($('.txt-Ss_Precio', this).val()))) / 100);
          $Ss_Total += parseFloat($('.txt-Ss_Total_Producto', this).val());
        });
        
        $( '#txt-inafecto' ).val( $Ss_Inafecto.toFixed(2) );
    		$( '#span-inafecto' ).text( $Ss_Inafecto.toFixed(2) );
    		
    		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
    		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
    		
    		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
    		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
    		$( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
      } else if (nu_tipo_impuesto == 3) {//Exonerada
        fila.find(".td-fDescuentoSinImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_Total_Producto").val((parseFloat(((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)) * impuesto_producto).toFixed(2)).toString().split(". "));
        
        var $Ss_Exonerada = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleProductosOrdenVenta > tbody > tr").each(function(){
          var rows = $(this);
          var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat($('.txt-Ss_SubTotal_Producto', this).val());
          var $Ss_Descuento_Producto = parseFloat($('.txt-Ss_Descuento', this).val());

          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
            
          if (Nu_Tipo_Impuesto == 3)
            $Ss_Exonerada += $Ss_SubTotal_Producto;
          
          $Ss_Descuento += (($Ss_Descuento_Producto * (parseFloat($('.txt-Qt_Producto', this).val()) * parseFloat($('.txt-Ss_Precio', this).val()))) / 100);
          $Ss_Total += parseFloat($('.txt-Ss_Total_Producto', this).val());
        });
        
        $( '#txt-exonerada' ).val( $Ss_Exonerada.toFixed(2) );
    		$( '#span-exonerada' ).text( $Ss_Exonerada.toFixed(2) );
    		
    		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
    		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
    		
    		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
    		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
    		$( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
      } else if (nu_tipo_impuesto == 4) {//Gratuita
        fila.find(".td-fDescuentoSinImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_Total_Producto").val((parseFloat(((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)) * impuesto_producto).toFixed(2)).toString().split(". "));
        
        var $Ss_Gratuita = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleProductosOrdenVenta > tbody > tr").each(function(){
          var rows = $(this);
          var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat($('.txt-Ss_SubTotal_Producto', this).val());
          var $Ss_Descuento_Producto = parseFloat($('.txt-Ss_Descuento', this).val());

          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
            
          if (Nu_Tipo_Impuesto == 4)
            $Ss_Gratuita += $Ss_SubTotal_Producto;
          
          $Ss_Descuento += (($Ss_Descuento_Producto * (parseFloat($('.txt-Qt_Producto', this).val()) * parseFloat($('.txt-Ss_Precio', this).val()))) / 100);
          $Ss_Total += parseFloat($('.txt-Ss_Total_Producto', this).val());
        });
        
        $( '#txt-gratuita' ).val( $Ss_Gratuita.toFixed(2) );
    		$( '#span-gratuita' ).text( $Ss_Gratuita.toFixed(2) );
    		
    		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
    		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
    		
    		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
    		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
    		$( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
  		}
      calcularIcbper();
      calcularDescuentoTotal(0);
    }
  })
	
  $('#table-DetalleProductosOrdenVenta tbody' ).on('input', '.txt-Qt_Producto', function(){
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
    
    //PRECIOS X MAYOR
    nu_activar_precio_x_mayor = fila.find(".txt-Qt_Producto").data('nu_activar_precio_x_mayor');
    precio_actual = parseFloat(fila.find(".txt-Ss_Precio").data('precio_actual'));

    //buscar precios por mayor solo si tiene activado el campo lista de precio x mayor
    var iAplicoPrecioxMayor = 0;
    if(nu_activar_precio_x_mayor==1){
      if(fila.find(".txt-Qt_Producto").val().substr(0, 1) == '0') {
        //buscamos nuevos precios configurados
        url = base_url + 'HelperController/obtenerPreciosxMayor';
        $.post(url, { ID_Empresa:$( '#header-a-id_empresa' ).val(), ID_Producto: $ID_Producto, ordenar : 'ASC' }, function (response) {
          for (var x = 0; x < response.length; x++) {
            if(response[x].Qt_Producto_x_Mayor.substr(0, 1) == '0' && cantidad <= parseFloat(response[x].Qt_Producto_x_Mayor) ){
              fila.find(".txt-Ss_Precio").val(response[x].Ss_Precio_x_Mayor);
              precio = response[x].Ss_Precio_x_Mayor;
              
              calcularImportexItemTemporal(fila);
              iAplicoPrecioxMayor = 1;
              return 1;
            }
          }
        }, 'JSON');
      } else {
        //buscamos nuevos precios configurados
        url = base_url + 'HelperController/obtenerPreciosxMayor';
        $.post(url, { ID_Empresa:$( '#header-a-id_empresa' ).val(), ID_Producto: $ID_Producto, ordenar : 'DESC' }, function (response) {
          for (var x = 0; x < response.length; x++) {
            if(response[x].Qt_Producto_x_Mayor.substr(0, 1) != '0' && cantidad >= parseFloat(response[x].Qt_Producto_x_Mayor)){
              fila.find(".txt-Ss_Precio").val(response[x].Ss_Precio_x_Mayor);
              precio = response[x].Ss_Precio_x_Mayor;

              calcularImportexItemTemporal(fila);
              iAplicoPrecioxMayor = 1;
              return 1;
            }
          }
        }, 'JSON');
      }
    }

    if (iAplicoPrecioxMayor==0) {
      fila.find(".txt-Ss_Precio").val(precio_actual);
      precio = precio_actual;

      if ( parseFloat(precio) > 0.00 && parseFloat(cantidad) > 0){
        $('#tr_detalle_producto' + $ID_Producto).removeClass('danger');
        $( '#table-DetalleProductosOrdenVenta tfoot' ).empty();
        if (nu_tipo_impuesto == 1 && considerar_igv == 1) {//CON IGV
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
          $("#table-DetalleProductosOrdenVenta > tbody > tr").each(function(){
            var rows = $(this);
            var Ss_Impuesto           = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
            var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
            var $Ss_SubTotal_Producto = parseFloat($('.txt-Ss_SubTotal_Producto', this).val());
            var $Ss_Descuento_Producto = parseFloat($('.txt-Ss_Descuento', this).val());
            var $Ss_Total_Producto = parseFloat($('.txt-Ss_Total_Producto', this).val());
      
            $Ss_Total += $Ss_Total_Producto;

            if(isNaN($Ss_Descuento_Producto))
              $Ss_Descuento_Producto = 0;
              
            if (Nu_Tipo_Impuesto == 1){
              $Ss_SubTotal += $Ss_SubTotal_Producto;
              $Ss_IGV += $Ss_Total_Producto - $Ss_SubTotal_Producto;
            }

            $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat($('.txt-Qt_Producto', this).val()) * parseFloat($('.txt-Ss_Precio', this).val()))) / 100) / impuesto_producto);
          });
          $( '#txt-subTotal' ).val( $Ss_SubTotal.toFixed(2) );
          $( '#span-subTotal' ).text( $Ss_SubTotal.toFixed(2) );
          
          $( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
          $( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
          
          $( '#txt-impuesto' ).val( $Ss_IGV.toFixed(2) );
          $( '#span-impuesto' ).text( $Ss_IGV.toFixed(2) );
          
          $( '#txt-total' ).val( $Ss_Total.toFixed(2) );
          $( '#span-total' ).text( $Ss_Total.toFixed(2) );
          $( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
        } else if (nu_tipo_impuesto == 2) {//Inafecto
          fila.find(".td-fDescuentoSinImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
          fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
          fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
          fila.find(".txt-Ss_Total_Producto").val((parseFloat(((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)) * impuesto_producto).toFixed(2)).toString().split(". "));

          var $Ss_Inafecto = 0.00;
          var $Ss_Descuento = 0.00;
          var $Ss_Total = 0.00;
          
          $("#table-DetalleProductosOrdenVenta > tbody > tr").each(function(){
            var rows = $(this);
            var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
            var $Ss_SubTotal_Producto = parseFloat($('.txt-Ss_SubTotal_Producto', this).val());
            var $Ss_Descuento_Producto = parseFloat($('.txt-Ss_Descuento', this).val());

            if(isNaN($Ss_Descuento_Producto))
              $Ss_Descuento_Producto = 0;
              
            if (Nu_Tipo_Impuesto == 2)
              $Ss_Inafecto += $Ss_SubTotal_Producto;
            
            $Ss_Descuento += (($Ss_Descuento_Producto * (parseFloat($('.txt-Qt_Producto', this).val()) * parseFloat($('.txt-Ss_Precio', this).val()))) / 100);
            $Ss_Total += parseFloat($('.txt-Ss_Total_Producto', this).val());
          });
          
          $( '#txt-inafecto' ).val( $Ss_Inafecto.toFixed(2) );
          $( '#span-inafecto' ).text( $Ss_Inafecto.toFixed(2) );
          
          $( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
          $( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
          
          $( '#txt-total' ).val( $Ss_Total.toFixed(2) );
          $( '#span-total' ).text( $Ss_Total.toFixed(2) );
          $( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
        } else if (nu_tipo_impuesto == 3) {//Exonerada
          fila.find(".td-fDescuentoSinImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
          fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
          fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
          fila.find(".txt-Ss_Total_Producto").val((parseFloat(((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)) * impuesto_producto).toFixed(2)).toString().split(". "));
          
          var $Ss_Exonerada = 0.00;
          var $Ss_Descuento = 0.00;
          var $Ss_Total = 0.00;
          $("#table-DetalleProductosOrdenVenta > tbody > tr").each(function(){
            var rows = $(this);
            var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
            var $Ss_SubTotal_Producto = parseFloat($('.txt-Ss_SubTotal_Producto', this).val());
            var $Ss_Descuento_Producto = parseFloat($('.txt-Ss_Descuento', this).val());

            if(isNaN($Ss_Descuento_Producto))
              $Ss_Descuento_Producto = 0;
              
            if (Nu_Tipo_Impuesto == 3)
              $Ss_Exonerada += $Ss_SubTotal_Producto;
            
            $Ss_Descuento += (($Ss_Descuento_Producto * (parseFloat($('.txt-Qt_Producto', this).val()) * parseFloat($('.txt-Ss_Precio', this).val()))) / 100);
            $Ss_Total += parseFloat($('.txt-Ss_Total_Producto', this).val());
          });
          
          $( '#txt-exonerada' ).val( $Ss_Exonerada.toFixed(2) );
          $( '#span-exonerada' ).text( $Ss_Exonerada.toFixed(2) );
          
          $( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
          $( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
          
          $( '#txt-total' ).val( $Ss_Total.toFixed(2) );
          $( '#span-total' ).text( $Ss_Total.toFixed(2) );
          $( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
        } else if (nu_tipo_impuesto == 4) {//Gratuita
          fila.find(".td-fDescuentoSinImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
          fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
          fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
          fila.find(".txt-Ss_Total_Producto").val((parseFloat(((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)) * impuesto_producto).toFixed(2)).toString().split(". "));
          
          var $Ss_Gratuita = 0.00;
          var $Ss_Descuento = 0.00;
          var $Ss_Total = 0.00;
          $("#table-DetalleProductosOrdenVenta > tbody > tr").each(function(){
            var rows = $(this);
            var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
            var $Ss_SubTotal_Producto = parseFloat($('.txt-Ss_SubTotal_Producto', this).val());
            var $Ss_Descuento_Producto = parseFloat($('.txt-Ss_Descuento', this).val());

            if(isNaN($Ss_Descuento_Producto))
              $Ss_Descuento_Producto = 0;
              
            if (Nu_Tipo_Impuesto == 4)
              $Ss_Gratuita += $Ss_SubTotal_Producto;
            
            $Ss_Descuento += (($Ss_Descuento_Producto * (parseFloat($('.txt-Qt_Producto', this).val()) * parseFloat($('.txt-Ss_Precio', this).val()))) / 100);
            $Ss_Total += parseFloat($('.txt-Ss_Total_Producto', this).val());
          });
          
          $( '#txt-gratuita' ).val( $Ss_Gratuita.toFixed(2) );
          $( '#span-gratuita' ).text( $Ss_Gratuita.toFixed(2) );
          
          $( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
          $( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
          
          $( '#txt-total' ).val( $Ss_Total.toFixed(2) );
          $( '#span-total' ).text( $Ss_Total.toFixed(2) );
          $( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
        }
        calcularIcbper();
        calcularDescuentoTotal(0);
      } //PRECIO Y CANTIDAD > 0
    }//PRECIOS POR MAYOR
  })

  $('#table-DetalleProductosOrdenVenta tbody').on('change', '.cbo-ImpuestosProducto', function () {
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

      if ( parseFloat(precio) > 0.00 && parseFloat(cantidad) > 0 && parseFloat(total_producto) > 0){
    		if (nu_tipo_impuesto == 1){//CON IGV
          fila.find(".td-fDescuentoSinImpuestosItem").text( (parseFloat(((descuento * (subtotal_producto)) / 100)).toFixed(2)).toString().split(". ") );
          fila.find(".td-fDescuentoImpuestosItem").text( (parseFloat(((descuento * (subtotal_producto * impuesto_producto)) / 100) - ((descuento * (subtotal_producto)) / 100)).toFixed(2)).toString().split(". ") );
    		  fila.find(".txt-Ss_Precio").val( (parseFloat(subtotal_producto * impuesto_producto).toFixed(6)).toString().split(". ") );
    		  fila.find(".txt-Ss_Total_Producto").val( (parseFloat(subtotal_producto * impuesto_producto).toFixed(2)).toString().split(". ") );
    		  
          var $Ss_SubTotal = 0.00;
          var $Ss_Exonerada = 0.00;
          var $Ss_Inafecto = 0.00;
          var $Ss_Descuento = 0.00;
          var $Ss_IGV = 0.00;
          var $Ss_Total = 0.00;
          $("#table-DetalleProductosOrdenVenta > tbody > tr").each(function(){
            var rows = $(this);
            var Ss_Impuesto           = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
            var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
            var $Ss_SubTotal_Producto = parseFloat($('.txt-Ss_SubTotal_Producto', this).val());
            var $Ss_Descuento_Producto = parseFloat($('.txt-Ss_Descuento', this).val());
            var $Ss_Total_Producto = parseFloat($('.txt-Ss_Total_Producto', this).val());
      
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
            }

            $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat($('.txt-Qt_Producto', this).val()) * parseFloat($('.txt-Ss_Precio', this).val()))) / 100) / impuesto_producto);
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
      		$( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
    		} else if (nu_tipo_impuesto == 2){//Inafecto
          fila.find(".td-fDescuentoSinImpuestosItem").text( (parseFloat(((descuento * (subtotal_producto)) / 100)).toFixed(2)).toString().split(". ") );
          fila.find(".td-fDescuentoImpuestosItem").text( (parseFloat(((descuento * (subtotal_producto * impuesto_producto)) / 100) - ((descuento * (subtotal_producto)) / 100)).toFixed(2)).toString().split(". ") );
    		  fila.find(".txt-Ss_Precio").val( (parseFloat(subtotal_producto * impuesto_producto).toFixed(6)).toString().split(". ") );
    		  fila.find(".txt-Ss_Total_Producto").val( (parseFloat(subtotal_producto * impuesto_producto).toFixed(2)).toString().split(". ") );
          
          fila.find(".txt-Ss_SubTotal_Producto").val(parseFloat(subtotal_producto).toFixed(2));

          var $Ss_SubTotal = 0.00;
          var $Ss_Exonerada = 0.00;
          var $Ss_Inafecto = 0.00;
          var $Ss_IGV = 0.00;
          var $Ss_Descuento = 0.00;
          var $Ss_Total = 0.00;
          $("#table-DetalleProductosOrdenVenta > tbody > tr").each(function(){
            var rows = $(this);
            var Ss_Impuesto           = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
            var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
            var $Ss_SubTotal_Producto = parseFloat($('.txt-Ss_SubTotal_Producto', this).val());
            var $Ss_Descuento_Producto = parseFloat($('.txt-Ss_Descuento', this).val());
            var $Ss_Total_Producto = parseFloat($('.txt-Ss_Total_Producto', this).val());
  
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
            }
            
            $Ss_Descuento += (($Ss_Descuento_Producto * (parseFloat($('.txt-Qt_Producto', this).val()) * parseFloat($('.txt-Ss_Precio', this).val()))) / 100);
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
      		$( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
        } else if (nu_tipo_impuesto == 3){//Exonerada
          fila.find(".td-fDescuentoSinImpuestosItem").text( (parseFloat(((descuento * (subtotal_producto)) / 100)).toFixed(2)).toString().split(". ") );
          fila.find(".td-fDescuentoImpuestosItem").text( (parseFloat(((descuento * (subtotal_producto * impuesto_producto)) / 100) - ((descuento * (subtotal_producto)) / 100)).toFixed(2)).toString().split(". ") );
    		  fila.find(".txt-Ss_Precio").val( (parseFloat(subtotal_producto * impuesto_producto).toFixed(6)).toString().split(". ") );
    		  fila.find(".txt-Ss_Total_Producto").val( (parseFloat(subtotal_producto * impuesto_producto).toFixed(2)).toString().split(". ") );
    		  
          fila.find(".txt-Ss_SubTotal_Producto").val(parseFloat(subtotal_producto).toFixed(2));

          var $Ss_SubTotal = 0.00;
          var $Ss_Exonerada = 0.00;
          var $Ss_Inafecto = 0.00;
          var $Ss_IGV = 0.00;
          var $Ss_Descuento = 0.00;
          var $Ss_Total = 0.00;
          $("#table-DetalleProductosOrdenVenta > tbody > tr").each(function(){
            var rows = $(this);
            var Ss_Impuesto           = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
            var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
            var $Ss_SubTotal_Producto = parseFloat($('.txt-Ss_SubTotal_Producto', this).val());
            var $Ss_Descuento_Producto = parseFloat($('.txt-Ss_Descuento', this).val());
            var $Ss_Total_Producto = parseFloat($('.txt-Ss_Total_Producto', this).val());
  
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
            }
            
            $Ss_Descuento += (($Ss_Descuento_Producto * (parseFloat($('.txt-Qt_Producto', this).val()) * parseFloat($('.txt-Ss_Precio', this).val()))) / 100);
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
      		$( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
    		} else if (nu_tipo_impuesto == 4){//Gratuita
          fila.find(".td-fDescuentoSinImpuestosItem").text( (parseFloat(((descuento * (subtotal_producto)) / 100)).toFixed(2)).toString().split(". ") );
          fila.find(".td-fDescuentoImpuestosItem").text( (parseFloat(((descuento * (subtotal_producto * impuesto_producto)) / 100) - ((descuento * (subtotal_producto)) / 100)).toFixed(2)).toString().split(". ") );
    		  fila.find(".txt-Ss_Precio").val( (parseFloat(subtotal_producto * impuesto_producto).toFixed(6)).toString().split(". ") );
    		  fila.find(".txt-Ss_Total_Producto").val( (parseFloat(subtotal_producto * impuesto_producto).toFixed(2)).toString().split(". ") );
    		  
          fila.find(".txt-Ss_SubTotal_Producto").val(parseFloat(subtotal_producto).toFixed(2));

          var $Ss_SubTotal = 0.00;
          var $Ss_Exonerada = 0.00;
          var $Ss_Inafecto = 0.00;
          var $Ss_Gratuita = 0.00;
          var $Ss_IGV = 0.00;
          var $Ss_Descuento = 0.00;
          var $Ss_Total = 0.00;
          $("#table-DetalleProductosOrdenVenta > tbody > tr").each(function(){
            var rows = $(this);
            var Ss_Impuesto           = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
            var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
            var $Ss_SubTotal_Producto = parseFloat($('.txt-Ss_SubTotal_Producto', this).val());
            var $Ss_Descuento_Producto = parseFloat($('.txt-Ss_Descuento', this).val());
            var $Ss_Total_Producto = parseFloat($('.txt-Ss_Total_Producto', this).val());
  
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
            
            $Ss_Descuento += (($Ss_Descuento_Producto * (parseFloat($('.txt-Qt_Producto', this).val()) * parseFloat($('.txt-Ss_Precio', this).val()))) / 100);
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
      		$( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
        }
        calcularIcbper();
        calcularDescuentoTotal(0);
      }
  })
  
  $('#table-DetalleProductosOrdenVenta tbody' ).on('input', '.txt-Ss_Descuento', function(){
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
          $("#table-DetalleProductosOrdenVenta > tbody > tr").each(function(){
            var rows = $(this);
            var Ss_Impuesto           = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
            var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
            var $Ss_SubTotal_Producto = parseFloat($('.txt-Ss_SubTotal_Producto', this).val());
            var $Ss_Descuento_Producto = parseFloat($('.txt-Ss_Descuento', this).val());
            var $Ss_Total_Producto = parseFloat($('.txt-Ss_Total_Producto', this).val());
  
            $Ss_Total += $Ss_Total_Producto;  
            
            if(isNaN($Ss_Descuento_Producto))
              $Ss_Descuento_Producto = 0;
              
            if (Nu_Tipo_Impuesto == 1){
              $Ss_SubTotal += $Ss_SubTotal_Producto;
              $Ss_IGV += $Ss_Total_Producto - $Ss_SubTotal_Producto;
            }
            
            $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat($('.txt-Qt_Producto', this).val()) * parseFloat($('.txt-Ss_Precio', this).val()))) / 100) / impuesto_producto);
          });
          
          $( '#txt-subTotal' ).val( $Ss_SubTotal.toFixed(2) );
      		$( '#span-subTotal' ).text( $Ss_SubTotal.toFixed(2) );
      		
      		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
      		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
      		
      		$( '#txt-impuesto' ).val( $Ss_IGV.toFixed(2) );
      		$( '#span-impuesto' ).text( $Ss_IGV.toFixed(2) );
      		
      		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
      		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
      		$( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
        } else if (nu_tipo_impuesto == 2) {//Inafecto
          fila.find(".td-fDescuentoSinImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
          fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
          fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
          fila.find(".txt-Ss_Total_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));

          var $Ss_Inafecto = 0.00;
          var $Ss_Descuento = 0.00;
          var $Ss_Total = 0.00;
          $("#table-DetalleProductosOrdenVenta > tbody > tr").each(function(){
            var rows = $(this);
            var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
            var $Ss_SubTotal_Producto = parseFloat($('.txt-Ss_SubTotal_Producto', this).val());
            var $Ss_Descuento_Producto = parseFloat($('.txt-Ss_Descuento', this).val());
  
            if(isNaN($Ss_Descuento_Producto))
              $Ss_Descuento_Producto = 0;
              
            if (Nu_Tipo_Impuesto == 2)
              $Ss_Inafecto += $Ss_SubTotal_Producto;
            
            $Ss_Descuento += (($Ss_Descuento_Producto * (parseFloat($('.txt-Qt_Producto', this).val()) * parseFloat($('.txt-Ss_Precio', this).val()))) / 100);
            $Ss_Total += parseFloat($('.txt-Ss_Total_Producto', this).val());
          });
          
          $( '#txt-inafecto' ).val( $Ss_Inafecto.toFixed(2) );
      		$( '#span-inafecto' ).text( $Ss_Inafecto.toFixed(2) );
      		
      		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
      		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
      		
      		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
      		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
      		$( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
        } else if (nu_tipo_impuesto == 3) {//Exonerada
          fila.find(".td-fDescuentoSinImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
          fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
          fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
          fila.find(".txt-Ss_Total_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));

          var $Ss_Exonerada = 0.00;
          var $Ss_Descuento = 0.00;
          var $Ss_Total = 0.00;
          $("#table-DetalleProductosOrdenVenta > tbody > tr").each(function(){
            var rows = $(this);
            var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
            var $Ss_SubTotal_Producto = parseFloat($('.txt-Ss_SubTotal_Producto', this).val());
            var $Ss_Descuento_Producto = parseFloat($('.txt-Ss_Descuento', this).val());
  
            if(isNaN($Ss_Descuento_Producto))
              $Ss_Descuento_Producto = 0;
              
            if (Nu_Tipo_Impuesto == 3)
              $Ss_Exonerada += $Ss_SubTotal_Producto;
            
            $Ss_Descuento += (($Ss_Descuento_Producto * (parseFloat($('.txt-Qt_Producto', this).val()) * parseFloat($('.txt-Ss_Precio', this).val()))) / 100);
            $Ss_Total += parseFloat($('.txt-Ss_Total_Producto', this).val());
          });
          
          $( '#txt-exonerada' ).val( $Ss_Exonerada.toFixed(2) );
      		$( '#span-exonerada' ).text( $Ss_Exonerada.toFixed(2) );
      		
      		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
      		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
      		
      		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
      		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
      		$( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
        } else if (nu_tipo_impuesto == 4) {//Gratuita
          fila.find(".td-fDescuentoSinImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
          fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
          fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
          fila.find(".txt-Ss_Total_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));

          var $Ss_Gratuita = 0.00;
          var $Ss_Descuento = 0.00;
          var $Ss_Total = 0.00;
          $("#table-DetalleProductosOrdenVenta > tbody > tr").each(function(){
            var rows = $(this);
            var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
            var $Ss_SubTotal_Producto = parseFloat($('.txt-Ss_SubTotal_Producto', this).val());
            var $Ss_Descuento_Producto = parseFloat($('.txt-Ss_Descuento', this).val());
  
            if(isNaN($Ss_Descuento_Producto))
              $Ss_Descuento_Producto = 0;
              
            if (Nu_Tipo_Impuesto == 4)
              $Ss_Gratuita += $Ss_SubTotal_Producto;
            
            $Ss_Descuento += (($Ss_Descuento_Producto * (parseFloat($('.txt-Qt_Producto', this).val()) * parseFloat($('.txt-Ss_Precio', this).val()))) / 100);
            $Ss_Total += parseFloat($('.txt-Ss_Total_Producto', this).val());
          });
          
          $( '#txt-gratuita' ).val( $Ss_Gratuita.toFixed(2) );
      		$( '#span-gratuita' ).text( $Ss_Gratuita.toFixed(2) );
      		
      		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
      		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
      		
      		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
      		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
      		$( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
    		}
        calcularIcbper();
        calcularDescuentoTotal(0);
      }
    }
  })

  $('#table-DetalleProductosOrdenVenta tbody' ).on('input', '.txt-Ss_Total_Producto', function(){
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
      $( '#table-DetalleProductosOrdenVenta tfoot' ).empty();
      if (nu_tipo_impuesto == 1){//CON IGV
        fila.find(".txt-Ss_Precio").val( (parseFloat(total_producto / cantidad).toFixed(6)).toString().split(". ") );
        fila.find(".txt-Ss_SubTotal_Producto").val( (parseFloat(total_producto / impuesto_producto).toFixed(6)).toString().split(". ") );
        
        var $Ss_SubTotal = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_IGV = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleProductosOrdenVenta > tbody > tr").each(function(){
          var rows = $(this);
          var Ss_Impuesto           = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
          var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat($('.txt-Ss_SubTotal_Producto', this).val());
          var $Ss_Descuento_Producto = parseFloat($('.txt-Ss_Descuento', this).val());
          var $Ss_Total_Producto = parseFloat($('.txt-Ss_Total_Producto', this).val());

          $Ss_Total += $Ss_Total_Producto;

          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
          
          if (Nu_Tipo_Impuesto == 1){
            $Ss_SubTotal += $Ss_SubTotal_Producto;
            $Ss_IGV += $Ss_Total_Producto - $Ss_SubTotal_Producto;
          }

          $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat($('.txt-Qt_Producto', this).val()) * parseFloat($('.txt-Ss_Precio', this).val()))) / 100) / impuesto_producto);
        });
        
        $( '#txt-subTotal' ).val( $Ss_SubTotal.toFixed(2) );
    		$( '#span-subTotal' ).text( $Ss_SubTotal.toFixed(2) );
    		
    		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
    		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
    		
    		$( '#txt-impuesto' ).val( $Ss_IGV.toFixed(2) );
    		$( '#span-impuesto' ).text( $Ss_IGV.toFixed(2) );
    		
    		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
    		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
    		$( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
  		} else if (nu_tipo_impuesto == 2){//Inafecto
        fila.find(".txt-Ss_Precio").val( (parseFloat((total_producto / cantidad) / impuesto_producto).toFixed(6)).toString().split(". ") );
        fila.find(".txt-Ss_SubTotal_Producto").val( (parseFloat(total_producto / impuesto_producto).toFixed(2)).toString().split(". ") );
        
        var $Ss_Inafecto = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_IGV = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleProductosOrdenVenta > tbody > tr").each(function(){
          var rows = $(this);
          var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat($('.txt-Ss_SubTotal_Producto', this).val());
          var $Ss_Descuento_Producto = parseFloat($('.txt-Ss_Descuento', this).val());

          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
          
          if (Nu_Tipo_Impuesto == 2)
            $Ss_Inafecto += $Ss_SubTotal_Producto;
          
          $Ss_Descuento += (($Ss_Descuento_Producto * (parseFloat($('.txt-Qt_Producto', this).val()) * parseFloat($('.txt-Ss_Precio', this).val()))) / 100);
          $Ss_Total += parseFloat($('.txt-Ss_Total_Producto', this).val());
        });
        
        $( '#txt-inafecto' ).val( $Ss_Inafecto.toFixed(2) );
    		$( '#span-inafecto' ).text( $Ss_Inafecto.toFixed(2) );
    		
    		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
    		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
    		
    		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
    		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
    		$( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
  		} else if (nu_tipo_impuesto == 3){//Exonerada
        fila.find(".txt-Ss_Precio").val( (parseFloat((total_producto / cantidad) / impuesto_producto).toFixed(6)).toString().split(". ") );
        fila.find(".txt-Ss_SubTotal_Producto").val( (parseFloat(total_producto / impuesto_producto).toFixed(2)).toString().split(". ") );
        
        var $Ss_Exonerada = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_IGV = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleProductosOrdenVenta > tbody > tr").each(function(){
          var rows = $(this);
          var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat($('.txt-Ss_SubTotal_Producto', this).val());
          var $Ss_Descuento_Producto = parseFloat($('.txt-Ss_Descuento', this).val());

          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
          
          if (Nu_Tipo_Impuesto == 3)
            $Ss_Exonerada += $Ss_SubTotal_Producto;
          
          $Ss_Descuento += (($Ss_Descuento_Producto * (parseFloat($('.txt-Qt_Producto', this).val()) * parseFloat($('.txt-Ss_Precio', this).val()))) / 100);
          $Ss_Total += parseFloat($('.txt-Ss_Total_Producto', this).val());
        });
        
        $( '#txt-exonerada' ).val( $Ss_Exonerada.toFixed(2) );
    		$( '#span-exonerada' ).text( $Ss_Exonerada.toFixed(2) );
    		
    		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
    		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
    		
    		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
    		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
    		$( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
  		} else if (nu_tipo_impuesto == 4){//Gratuita
        fila.find(".txt-Ss_Precio").val( (parseFloat((total_producto / cantidad) / impuesto_producto).toFixed(6)).toString().split(". ") );
        fila.find(".txt-Ss_SubTotal_Producto").val( (parseFloat(total_producto / impuesto_producto).toFixed(2)).toString().split(". ") );
        
        var $Ss_Gratuita = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_IGV = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleProductosOrdenVenta > tbody > tr").each(function(){
          var rows = $(this);
          var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat($('.txt-Ss_SubTotal_Producto', this).val());
          var $Ss_Descuento_Producto = parseFloat($('.txt-Ss_Descuento', this).val());

          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
          
          if (Nu_Tipo_Impuesto == 4)
            $Ss_Gratuita += $Ss_SubTotal_Producto;
          
          $Ss_Descuento += (($Ss_Descuento_Producto * (parseFloat($('.txt-Qt_Producto', this).val()) * parseFloat($('.txt-Ss_Precio', this).val()))) / 100);
          $Ss_Total += parseFloat($('.txt-Ss_Total_Producto', this).val());
        });
        
        $( '#txt-gratuita' ).val( $Ss_Gratuita.toFixed(2) );
    		$( '#span-gratuita' ).text( $Ss_Gratuita.toFixed(2) );
    		
    		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
    		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
    		
    		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
    		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
    		$( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
  		}
      calcularIcbper();
      calcularDescuentoTotal(0);
    }
  })
  
	$( '#table-DetalleProductosOrdenVenta tbody' ).on('click', '#btn-deleteProducto', function(){
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
    $("#table-DetalleProductosOrdenVenta > tbody > tr").each(function(){
      var rows = $(this);
      var fImpuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
      var iGrupoImpuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
      var $Ss_SubTotal_Producto = parseFloat($('.txt-Ss_SubTotal_Producto', this).val());
      var $Ss_Descuento_Producto = parseFloat($('.txt-Ss_Descuento', this).val());
      var $Ss_Total_Producto = parseFloat($('.txt-Ss_Total_Producto', this).val());

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
        
      $Ss_Descuento_p += (($Ss_Descuento_Producto * (parseFloat($('.txt-Qt_Producto', this).val()) * parseFloat($('.txt-Ss_Precio', this).val()))) / 100);
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
    
    $( '#txt-inafecto' ).val( $Ss_Inafecto.toFixed(2) );
    $( '#span-inafecto' ).text( $Ss_Inafecto.toFixed(2) );
    
    $( '#txt-exonerada' ).val( $Ss_Exonerada.toFixed(2) );
    $( '#span-exonerada' ).text( $Ss_Exonerada.toFixed(2) );
    
    $( '#txt-gratuita' ).val( $Ss_Gratuita.toFixed(2) );
    $( '#span-gratuita' ).text( $Ss_Gratuita.toFixed(2) );
      
    $( '#txt-impuesto' ).val( $Ss_IGV.toFixed(2) );
    $( '#span-impuesto' ).text( $Ss_IGV.toFixed(2) );
  	
  	$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
  	$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );

		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
    $( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
		
    if ($( '#table-DetalleProductosOrdenVenta >tbody >tr' ).length == 0)
      $('#table-DetalleProductosOrdenVenta').hide();

    calcularIcbper();
    calcularDescuentoTotal(0);
	})
	
  $('#table-OrdenVentaTotal' ).on('input', '#txt-Ss_Descuento', function(){
    var $Ss_Descuento_Producto = 0.00;
    $("#table-DetalleProductosOrdenVenta > tbody > tr").each(function(){
      var rows = $(this);
      $Ss_Descuento_Producto = parseFloat($('.txt-Ss_Descuento', this).val());
      
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
      $("#table-DetalleProductosOrdenVenta > tbody > tr").each(function(){
        var rows = $(this);
        var fImpuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
        var iGrupoImpuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
        var $Ss_SubTotal_Producto = parseFloat($('.txt-Ss_SubTotal_Producto', this).val());
        var $Ss_Total_Producto = parseFloat($('.txt-Ss_Total_Producto', this).val());

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
      
      $( '#txt-inafecto' ).val( $Ss_Inafecto.toFixed(2) );
      $( '#span-inafecto' ).text( $Ss_Inafecto.toFixed(2) );
      
      $( '#txt-exonerada' ).val( $Ss_Exonerada.toFixed(2) );
      $( '#span-exonerada' ).text( $Ss_Exonerada.toFixed(2) );
      
      $( '#txt-gratuita' ).val( $Ss_Gratuita.toFixed(2) );
      $( '#span-gratuita' ).text( $Ss_Gratuita.toFixed(2) );
        
      $( '#txt-impuesto' ).val( $Ss_IGV.toFixed(2) );
      $( '#span-impuesto' ).text( $Ss_IGV.toFixed(2) );
    	
    	$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
    	$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
  
  		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
      $('#span-total').text($Ss_Total.toFixed(2));
      $( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );

      calcularIcbper();
      calcularDescuentoTotal(0);
    }
  })
})

function isExistTableTemporalProductoOrden($ID_Producto){
  return Array.from($('tr[id*=tr_detalle_producto]'))
    .some(element => ($('td:nth(0)',$(element)).html()===$ID_Producto));
}

function form_OrdenVenta(){
  if (accion_orden_venta == 'add_orden_venta' || accion_orden_venta == 'upd_orden_venta'){
    var arrDetalleOrdenVenta = [];
    var arrValidarNumerosEnCero = [];
    var $counterNumerosEnCero = 0;
    var tr_foot = '';
    
    $("#table-DetalleProductosOrdenVenta > tbody > tr").each(function(){
      var rows = $(this);
      
      var $ID_Producto = rows.find(".td-iIdItem").text();
      var $Qt_Producto = $('.txt-Qt_Producto', this).val();
      var $Ss_Precio = $('.txt-Ss_Precio', this).val();
      var $ID_Impuesto_Cruce_Documento = $('.cbo-ImpuestosProducto option:selected', this).val();
      var $Ss_SubTotal = $('.txt-Ss_SubTotal_Producto', this).val();
      var $Ss_Descuento = $('.txt-Ss_Descuento', this).val();
      var $Ss_Total = $('.txt-Ss_Total_Producto', this).val();
      var $fDescuentoSinImpuestosItem = rows.find(".td-fDescuentoSinImpuestosItem").text();
      var $fDescuentoImpuestosItem = rows.find(".td-fDescuentoImpuestosItem").text();
      var $fIcbperItem = rows.find(".txt-Qt_Producto").data('ss_icbper_item');
      var $sNotaItem = rows.find(".input-sNotaItem").val();
      
      if (
        parseFloat($Ss_Precio) == 0
        || isNaN(parseFloat($Ss_Precio))
        || isNaN(parseFloat($Qt_Producto))
        || isNaN(parseFloat($Ss_Total))
        || parseFloat($Qt_Producto) == 0
        || parseFloat($Ss_Total) == 0
      ){
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
      obj.fDescuentoImpuestosItem = $fDescuentoImpuestosItem;
      obj.fIcbperItem = $fIcbperItem;
      obj.Txt_Nota = $sNotaItem;
      arrDetalleOrdenVenta.push(obj);
      $counterNumerosEnCero++;
    });
    
    bEstadoValidacion = validatePreviousDocumentToSaveOrderSale();
    
    if ( arrDetalleOrdenVenta.length == 0){
  		$( '#panel-DetalleProductosOrdenVenta' ).removeClass('panel-default');
  		$( '#panel-DetalleProductosOrdenVenta' ).addClass('panel-danger');
  		
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
      $( '#table-DetalleProductosOrdenVenta >tbody' ).after(tr_foot);
    } else if (bEstadoValidacion){
  		$( '#panel-DetalleProductosOrdenVenta' ).removeClass('panel-danger');
  		$( '#panel-DetalleProductosOrdenVenta' ).addClass('panel-default');
  		
  		
  		var arrOrdenVentaCabecera = Array();
  		arrOrdenVentaCabecera = {
  		  'EID_Empresa'                 : $( '[name="EID_Empresa"]' ).val(),
  		  'EID_Documento_Cabecera'      : $( '[name="EID_Documento_Cabecera"]' ).val(),
        'ENu_Estado': $('[name="ENu_Estado"]').val(),
        'ID_Tipo_Documento': $('#cbo-TiposDocumento').val(),
  		  'Fe_Emision'                  : $( '#txt-Fe_Emision' ).val(),
  		  'Fe_Vencimiento'              : $( '#txt-Fe_Vencimiento' ).val(),
  		  'Fe_Entrega'                  : $( '#txt-Fe_Entrega' ).val(),
  		  'ID_Moneda'                   : $( '#cbo-Monedas' ).val(),
  		  'ID_Medio_Pago'               : $( '#cbo-MediosPago' ).val(),
  		  'Nu_Descargar_Inventario'     : 0,
  		  'ID_Entidad'                  : $( '#txt-AID' ).val(),
  		  'ID_Contacto'                 : $( '#txt-AID_Contacto' ).val(),
  		  'Txt_Garantia'                : $( '[name="Txt_Garantia"]' ).val(),
        //'Txt_Garantia'                : CKEDITOR.instances.Txt_Garantia.getData(),
  		  //'Txt_Glosa'                   : $( '[name="Txt_Glosa"]' ).val(),
        'Txt_Glosa'                   : CKEDITOR.instances.Txt_Glosa.getData(),
  		  'Po_Descuento'                : $( '#txt-Ss_Descuento' ).val(),
  		  'Ss_Descuento'                : $( '#txt-descuento' ).val(),
  		  'Ss_Total'                    : $( '#txt-total' ).val(),
  		  'ID_Lista_Precio_Cabecera'    : $( '#cbo-lista_precios' ).val(),
  		  'ID_Mesero' : $( '#cbo-vendedor' ).val(),
  		  'ID_Comision' : $( '#cbo-porcentaje' ).val(),
        'No_Formato_PDF' : $( '#cbo-formato_pdf' ).val(),
        'Nu_Descargar_Inventario' : $( '#cbo-descargar_stock' ).val(),
        'ID_Almacen' : $( '#cbo-almacen' ).val(),
        'Ss_Descuento_Impuesto': $('#txt-descuento_igv').val(),
        'addCliente' : $('[name="addCliente"]:checked').attr('value'),
        'addContacto' : $('[name="addContacto"]:checked').attr('value'),
  		};
  		
  		var No_Cliente_Filter = $( '#txt-ANombre' ).val(), arrClienteNuevo = {};
  		if ($('[name="addCliente"]:checked').attr('value') == 1){//Agregar cliente
    		arrClienteNuevo = {
    		  'ID_Tipo_Documento_Identidad' : $( '#cbo-TiposDocumentoIdentidadCliente' ).val(),
    		  'Nu_Documento_Identidad'      : $( '#txt-Nu_Documento_Identidad_Cliente' ).val(),
    		  'No_Entidad'                  : $( '#txt-No_Entidad_Cliente' ).val(),
    		  'Txt_Direccion_Entidad'       : $( '#txt-Txt_Direccion_Entidad_Cliente' ).val(),
    		  'Nu_Telefono_Entidad'         : $( '#txt-Nu_Telefono_Entidad_Cliente' ).val(),
    		  'Nu_Celular_Entidad'          : $( '#txt-Nu_Celular_Entidad_Cliente' ).val(),
    		  'Txt_Email_Entidad'          : $( '#txt-Txt_Email_Entidad' ).val(),          
        };
        No_Cliente_Filter=$('#txt-No_Entidad_Cliente').val();
  		}

  		var No_Contacto_Filter = $( '#txt-No_Contacto_existe' ).val(), arrContactoNuevo = {};
  		if ($('[name="addContacto"]:checked').attr('value') == 1 ){//Agregar contacto
    		arrContactoNuevo = {
    		  'ID_Tipo_Documento_Identidad' : $( '#cbo-TiposDocumentoIdentidadContacto' ).val(),
    		  'Nu_Documento_Identidad'      : $( '#txt-Nu_Documento_Identidad' ).val(),
    		  'No_Entidad'                  : $( '#txt-No_Contacto' ).val(),
    		  'Txt_Email_Entidad'           : $( '#txt-Txt_Email_Contacto' ).val(),
    		  'Nu_Telefono_Entidad'         : $( '#txt-Nu_Telefono_Contacto' ).val(),
    		  'Nu_Celular_Entidad'          : $( '#txt-Nu_Celular_Contacto' ).val(),
    		};
  		  No_Contacto_Filter = $( '#txt-No_Contacto' ).val();
  		}
      
      if ($( '#txt-No_Contacto_existe' ).val().trim().length == 0){
  		  No_Contacto_Filter = '';
        arrOrdenVentaCabecera.ID_Contacto='';
      }
        		
      $( '#btn-save' ).text('');
      $( '#btn-save' ).attr('disabled', true);
      $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
      
      $( '#modal-loader' ).modal('show');
  
      url = base_url + 'Ventas/OrdenVentaController/crudOrdenVenta';
    	$.ajax({
        type		  : 'POST',
        dataType	: 'JSON',
    		url		    : url,
    		data		  : {
    		  arrOrdenVentaCabecera : arrOrdenVentaCabecera,
    		  arrDetalleOrdenVenta : arrDetalleOrdenVenta,
    		  arrClienteNuevo : arrClienteNuevo,
    		  arrContactoNuevo : arrContactoNuevo,
    		},
    		success : function( response ){
    		  $( '#modal-loader' ).modal('hide');
    		  
    	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
      	  $( '#modal-message' ).modal('show');
      	  
    		  if (response.status == 'success'){
    		    accion_orden_venta='';
    		    
            $( '#txt-Filtro_Contacto' ).val( No_Contacto_Filter );
            $( '#txt-Filtro_Entidad' ).val( No_Cliente_Filter );

    		    $( '#form-OrdenVenta' )[0].reset();
            $( '.div-AgregarEditar' ).hide();
            $( '.div-Listar' ).show();
      	    $( '.modal-message' ).addClass(response.style_modal);
      	    $( '.modal-title-message' ).text(response.message);
      	    setTimeout(function() {$('#modal-message').modal('hide'); }, 1100);
      	    reload_table_orden_venta();
    		  } else {
      	    $( '.modal-message' ).addClass(response.style_modal);
      	    $( '.modal-title-message' ).text(response.message);
      	    setTimeout(function() {$('#modal-message').modal('hide');}, 5200);
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

function reload_table_orden_venta(){
  table_orden_venta.ajax.reload(null,false);
}

function pdfOrdenVenta(ID){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '.modal-message-delete' ).removeClass('modal-danger modal-warning modal-success');
  $( '.modal-message-delete' ).addClass('modal-success');
  
  $( '.modal-title-message-delete' ).text('¿Deseas generar PDF?');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });
  
  $( '#btn-save-delete' ).off('click').click(function () {
    sendPDF($modal_delete, ID);
  });
}

function estadoOrdenVenta(ID, Nu_Descargar_Inventario, Nu_Estado){
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
    
    url = base_url + 'Ventas/OrdenVentaController/estadoOrdenVenta/' + ID + '/' + Nu_Descargar_Inventario + '/' + Nu_Estado;
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
    	    reload_table_orden_venta();
  		  } else {
    	    $( '.modal-message' ).addClass(response.style_modal);
    	    $( '.modal-title-message' ).text(response.message);
    	    setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
  		  }
      }
    });
  });
}

function duplicarOrdenVenta(ID){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '.modal-message-delete' ).removeClass('modal-danger modal-warning modal-success');
  $( '.modal-message-delete' ).addClass('modal-success');
  
  $( '.modal-title-message-delete' ).text('¿Deseas duplicar?');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $( '#btn-save-delete' ).off('click').click(function () {
    duplicarData($modal_delete, ID);
  });
}

function facturarOrdenVenta(ID){
  var $modal = $( '.modal-orden' );
  $modal.modal('show');
  
	$modal.on('shown.bs.modal', function() {
		$( '.hidden-modal_orden' ).focus();
	})
	
  //Salir modal
  $( '#btn-modal-salir-orden' ).off('click').click(function () {
    $modal.modal('hide');
  });
  //Fin Salir modal
  
  //Limpiar modal
  $( '#div-modal-body-orden' ).empty();
  
	$(document).ready(function(){
    $('[name="Txt_Direccion_Delivery"]').val('');
    
    $('.div-fecha_entrega').hide();
    $('#cbo-recepcion-modal').change(function () {
      $('.div-fecha_entrega').hide();
      if ($(this).val() != 5)
        $('.div-fecha_entrega').show();

      $('.modal-delivery').modal('hide');
      if ($(this).val() == 6) {
        $('.modal-delivery').modal('show');

        $('.modal-delivery').css("z-index", "6000");
      }
    })

    url = base_url + 'HelperController/getDeliveryVentas';
    var arrPost = {};
    $.post(url, arrPost, function (response) {
      if (response.sStatus == 'success') {
        var l = response.arrData.length;
        if (l == 1) {
          $('#modal-cbo-transporte').html('<option value="' + response.arrData[0].ID + '">' + response.arrData[0].Nombre + '</option>');
        } else {
          $('#modal-cbo-transporte').html('<option value="0" selected="selected">- Seleccionar -</option>');
          for (var x = 0; x < l; x++) {
            $('#modal-cbo-transporte').append('<option value="' + response.arrData[x].ID + '">' + response.arrData[x].Nombre + '</option>');
          }
        }
      } else {
        if (response.sMessageSQL !== undefined) {
          console.log(response.sMessageSQL);
        }
      }
    }, 'JSON');

    $( '#cbo-modal-descargar_stock' ).html( '<option value="1">Si</option>' );
    $( '#cbo-modal-descargar_stock' ).append( '<option value="0">No</option>' );
  
    $('#cbo-tipo_documento_modal').html('<option value="0" selected="selected">- Seleccionar -</option>');
    if( $('#hidden-Nu_Tipo_Proveedor_FE').val()!=3 ) {
      $('#cbo-tipo_documento_modal').append('<option value="3" data-nu_impuesto="1">Factura</option>');
      $('#cbo-tipo_documento_modal').append('<option value="4" data-nu_impuesto="1">Boleta</option>');
    }
    $('#cbo-tipo_documento_modal').append('<option value="2" data-nu_impuesto="0">Nota Venta</option>');

    /*
    url = base_url + 'HelperController/getTiposDocumentosOrden';
    $.post( url, function( response ){
      $( '#cbo-tipo_documento_modal' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
      for (var i = 0; i < response.length; i++) {
        if (response[i].ID_Tipo_Documento != 7 && response[i].ID_Tipo_Documento!=14){
          if( $('#hidden-Nu_Tipo_Proveedor_FE').val()!=3 ) {
            $( '#cbo-tipo_documento_modal' ).append( '<option value="' + response[i].ID_Tipo_Documento + '" data-nu_impuesto="' + response[i].Nu_Impuesto + '">' + response[i].No_Tipo_Documento_Breve + '</option>' );
          }
        }
      }
    }, 'JSON');
    */

    /* Sunat tipo de operacion */
    $('#cbo-sunat_tipo_transaction-modal').html('<option value="1" selected="selected">VENTA INTERNA</option>');
    url = base_url + 'HelperController/getSunatTipoOperacion';
    $.post(url, {}, function (response) {
      $('#cbo-sunat_tipo_transaction-modal').html('');
      if (response.sStatus == 'success') {
        var l = response.arrData.length;
        for (var x = 0; x < l; x++) {
          $('#cbo-sunat_tipo_transaction-modal').append('<option value="' + response.arrData[x].ID + '">' + response.arrData[x].Nombre + '</option>');
        }
      } else {
        if (response.sMessageSQL !== undefined) {
          console.log(response.sMessageSQL);
        }
        console.log(response.sMessage);
      }
    }, 'JSON');
    /* /. Sunat tipo de operacion */

  	$( '#cbo-tipo_documento_modal' ).change(function(){
      $( '#cbo-serie_documento_modal' ).html('');
      $( '#txt-ID_Numero_Documento' ).val('');
  	  if ( $(this).val() > 0 ) {
  		  url = base_url + 'HelperController/getSeriesDocumento';
        $.post( url, {ID_Organizacion : $( '#header-a-id_organizacion' ).val(), ID_Tipo_Documento: $(this).val()}, function( response ){
          if (response.length == 0)
            $( '#cbo-serie_documento_modal' ).html('<option value="0" selected="selected">Sin serie</option>');
          else {
            $( '#cbo-serie_documento_modal' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
            for (var i = 0; i < response.length; i++)
              $( '#cbo-serie_documento_modal' ).append( '<option value="' + response[i].ID_Serie_Documento + '" data-id_serie_documento_pk=' + response[i].ID_Serie_Documento_PK + '>' + response[i].ID_Serie_Documento + '</option>' );
          }
        }, 'JSON');
  	  }
  	})
  	
  	$( '#cbo-serie_documento_modal' ).change(function(){
  	  $( '#txt-ID_Numero_Documento' ).val('');
  	  if ( $(this).val() != '') {
  		  url = base_url + 'HelperController/getNumeroDocumento';
        $.post( url, { ID_Organizacion : $( '#header-a-id_organizacion' ).val(), ID_Tipo_Documento: $( '#cbo-tipo_documento_modal' ).val(), ID_Serie_Documento: $(this).val() }, function( response ){
          if (response.length == 0)
            $( '#txt-ID_Numero_Documento' ).val('');
          else
            $( '#txt-ID_Numero_Documento' ).val(response.ID_Numero_Documento);
        }, 'JSON');
      }
    })

    $('.div-modal_credito').hide();
    // Modal de cobranza al cliente
    $('#cbo-MediosPago-modal').change(function () {
      $('.div-modal_credito').hide();

      ID_Medio_Pago = $('#cbo-MediosPago-modal').val();
      Nu_Tipo_Medio_Pago = $('#cbo-MediosPago-modal').find(':selected').data('nu_tipo');

      $('.div-modal_datos_tarjeta_credito').hide();
      $('#cbo-modal_tarjeta_credito').html('');
      $('#tel-nu_referencia').val('');
      $('#tel-nu_ultimo_4_digitos_tarjeta').val('');
      if (Nu_Tipo_Medio_Pago == 2) {
        $('.div-modal_credito').hide();
        $('.div-modal_datos_tarjeta_credito').show();

        url = base_url + 'HelperController/getTiposTarjetaCredito';
        $.post(url, { ID_Medio_Pago: ID_Medio_Pago }, function (response) {
          $('#cbo-modal_tarjeta_credito').html('');
          for (var i = 0; i < response.length; i++)
            $('#cbo-modal_tarjeta_credito').append('<option value="' + response[i].ID_Tipo_Medio_Pago + '">' + response[i].No_Tipo_Medio_Pago + '</option>');
        }, 'JSON');
      } else if (Nu_Tipo_Medio_Pago == 1) {
        $('.div-modal_credito').show();
  
        var dNuevaFechaVencimiento = sumaFecha(1, $( '#txt-Fe_Emision' ).val());
        $( '#txt-Fe_Vencimiento-modal' ).val( dNuevaFechaVencimiento );
      }
    })
    
  });
  
  var html_orden_cabecera='';
  html_orden_cabecera += 
  '<div class="row">'
    +'<div class="col-md-12">'
      +'<h3 id="h3-modal_header-cliente" class="text-center"></h3>'
    +'</div>'
    +'<div class="col-sm-12 col-md-12">'
	    +'<div class="panel panel-default">'
        +'<div class="panel-heading"><i class="fa fa-book"></i> <b>Documento</b></div>'
        +'<div class="panel-body">'
            + '<div class="row">'
            + '<div class="col-xs-12 col-sm-3 col-md-3">'
              + '<div class="form-group">'
                  +'<label>Operación</label>'
                  +'<select id="cbo-sunat_tipo_transaction-modal" class="form-control required" style="width: 100%;"></select>'
                + '<span class="help-block" id="error"></span>'
              + '</div>'
            + '</div>'
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
          			+'<select id="cbo-serie_documento_modal" class="form-control required" style="width: 100%;"></select>'
                +'<span class="help-block" id="error"></span>'
              +'</div>'
            +'</div>'
            +'<div class="col-xs-12 col-sm-3 col-md-2 hidden">'
              +'<div class="form-group">'
                +'<label>Número <span class="label-advertencia">*</span></label>'
                +'<input type="tel" id="txt-ID_Numero_Documento" name="ID_Numero_Documento" class="form-control required input-number" disabled>'
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
            +'</div>'
            + '<div class="col-xs-6 col-sm-3 col-md-3">'
              + '<div class="form-group">'
                + '<label>Forma Pago</label>'
                + '<select id="cbo-MediosPago-modal" class="form-control required" style="width: 100%;"></select>'
                + '<span class="help-block" id="error"></span>'
              + '</div>'
            + '</div>'

            + '<div class="col-xs-6 col-sm-3 div-modal_datos_tarjeta_credito">'
              + '<label>Tarjeta Crédito</label>'
              + '<div class="form-group">'
                + '<select id="cbo-modal_tarjeta_credito" class="form-control" style="width: 100%;"></select>'
                + '<span class="help-block" id="error"></span>'
              + '</div>'
            + '</div>'

            + '<div class="col-xs-6 col-sm-3 col-md-2 col-lg-2 div-modal_credito">'
              + '<div class="form-group">'
                + '<label>F. Vencimiento</label>'
                + '<div class="input-group date" style="width: 100%;">'
                  + '<input type="text" id="txt-Fe_Vencimiento-modal" class="form-control required" data-inputmask="alias: dd/mm/yyyy" data-mask>'
                + '</div>'
                + '<span class="help-block" id="error"></span>'
              + '</div>'
            + '</div>'

            + '<div class="col-xs-6 col-sm-3 col-md-2 col-lg-2 div-modal_datos_tarjeta_credito">'
              + '<label>Opcional</label>'
              + '<div class="form-group">'
                + '<input type="text" inputmode="numeric" id="tel-nu_referencia" class="form-control input-number" value="" maxlength="10" placeholder="No. Operación" autocomplete="off">'
                + '<span class ="help-block" id="error"></span>'
              + '</div>'
            + '</div>'

            + '<div class="col-xs-6 col-sm-3 col-md-2 col-lg-2 div-modal_datos_tarjeta_credito">'
              + '<label>Opcional</label>'
              + '<div class="form-group">'
                + '<input type="text" inputmode="numeric" id="tel-nu_ultimo_4_digitos_tarjeta" class="form-control input-number" minlength="4" maxlength="4" value="" placeholder="últimos 4 dígitos" autocomplete="off">'
                + '<span class ="help-block" id="error"></span>'
              + '</div>'
            + '</div>'

            + '<div class="col-xs-6 col-sm-4 col-md-3">'
              + '<div class="form-group">'
                + '<label>Almacen</label>'
                + '<select id="cbo-modal-almacen" class="form-control required" style="width: 100%;"></select>'
                + '<span class="help-block" id="error"></span>'
              + '</div>'
            + '</div>'

            +'<div class="col-xs-6 col-sm-2 col-md-2">'
              +'<div class="form-group">'
                +'<label>¿Stock?</label>'
                +'<select id="cbo-modal-descargar_stock" class="form-control required" style="width: 100%;"></select>'
                +'<span class="help-block" id="error"></span>'
              +'</div>'
            +'</div>'

            + '<div class="col-xs-6 col-sm-3 col-md-2">'
              + '<div class="form-group">'
                + '<label>PDF</label>'
                + '<select id="cbo-modal-formato_pdf" class="form-control required" style="width: 100%;">'
                  + '<option value="A4" selected="selected">A4</option>'
                  + '<option value="A5">A5</option>'
                  + '<option value="TICKET">Ticket</option>'
                + '</select>'
                + '<span class="help-block" id="error"></span>'
              + '</div>'
            + '</div>'

            + '<div class="col-xs-6 col-sm-3 col-md-2">'
              + '<div class="form-group">'
                + '<label>Recepción</label>'
                + '<select id="cbo-recepcion-modal" name="Nu_Tipo_Recepcion" class="form-control"></select>'
                + '<span class="help-block" id="error"></span>'
              + '</div>'
            + '</div>'

            + '<div class="col-xs-6 col-sm-4 col-md-3 col-lg-3 div-fecha_entrega">'
              + '<label>F. Entrega</label>'
              + '<div class="form-group">'
                + '<div class="input-group date" style="width:100%">'
                  + '<input type="text" id="txt-fe_entrega-modal" name="Fe_Entrega" class="form-control input-datepicker-today-to-more required" data-inputmask="alias: dd/mm/yyyy" data-mask>'
                + '</div>'
              + '</div>'
            + '</div>'
            
            + '<div class="col-xs-6 col-sm-3 col-md-3">'
            + '  <div class="form-group">'
            + '    <button type="button" id="btn-adicionales_fv" class="btn btn-link btn-lg btn-block">Adicionales</button>'
            + '  </div>'
            + '</div>'

            +'<div class="col-xs-12 col-sm-6 col-md-2 hidden">'
              +'<div class="form-group">'
                +'<label>Porcentaje</label>'
  		  				+'<select id="cbo-porcentaje-modal" name="Po_Comision" class="form-control"></select>'
                +'<span class="help-block" id="error"></span>'
              +'</div>'
            +'</div>'
          +'</div>'
        +'</div>'
      +'</div>'
    +'</div>'
  +'</div>';
  
  $( '#div-modal-body-orden' ).append(html_orden_cabecera);
  
  $( '#form-datos_adicionales_venta' )[0].reset();

  $('#radio-InactiveDetraccion').prop('checked', true).iCheck('update');
  $('#radio-ActiveDetraccion').prop('checked', false).iCheck('update');

  $('#radio-InactiveRetencion').prop('checked', true).iCheck('update');
  $('#radio-ActiveRetencion').prop('checked', false).iCheck('update');
  
  $('.div-detraccion').hide();
  $('#radio-ActiveDetraccion').on('ifChecked', function () {
    $('.div-detraccion').show();
  })

  $('#btn-adicionales_fv').click(function () {
    $('.modal-adicionales').modal('show');
    
    $('.modal-adicionales').css("z-index", "6000");
  })

  $('#radio-InactiveDetraccion').on('ifChecked', function () {
    $('.div-detraccion').hide();
  })
  
	$(document).on('click', '#radio-ActiveDetraccion', function () {
    $('.div-detraccion').show();
  })
  
	$(document).on('click', '#radio-InactiveDetraccion', function () {
    $('.div-detraccion').hide();
  })

  $( '.date-picker-invoice' ).datepicker({
    autoclose : true,
    endDate   : new Date(fYear, fToday.getMonth(), fDay),
    todayHighlight: true
  })
  $( '.date-picker-invoice' ).val( fDay + '/' + fMonth + '/' + fYear);

  var Fe_Emision = $('#txt-Fe_Vencimiento-modal').val(fDay + '/' + fMonth + '/' + fYear);
  $('#txt-Fe_Vencimiento-modal').datepicker({
    autoclose: true,
    todayHighlight: true
  })

  $('#txt-Fe_Emision_modal').datepicker({}).on('changeDate', function (selected) {
    var minDate = new Date(selected.date.valueOf());
    $('#txt-Fe_Vencimiento-modal').datepicker('setStartDate', minDate);
  });

  $('.div-modal_datos_tarjeta_credito').hide();
  $('.div-modal_credito').hide();

  $('.input-datepicker-today-to-more').inputmask('dd/mm/yyyy', { 'placeholder': 'dd/mm/yyyy' });
  $('.input-datepicker-today-to-more').val(fDay + '/' + fMonth + '/' + fYear);

  //Date picker invoice
  $('.input-datepicker-today-to-more').datepicker({
    autoclose: true,
    startDate: new Date(fYear, fToday.getMonth(), fDay),
    todayHighlight: true
  });
  
  //Detalle de la Orden Venta
  $( '#modal-loader' ).modal('show');
  var html_table_orden_detalle='';
  url = base_url + 'Ventas/OrdenVentaController/ajax_edit/' + ID;
  $.getJSON( url, function( response ) {  
    var arrParams = {
      'iIdComboxAlmacen': 'cbo-modal-almacen',
      'ID_Almacen': response.arrEdit[0].ID_Almacen
    };
    getAlmacenes(arrParams);

    $('#h3-modal_header-cliente').text(response.arrEdit[0].No_Tipo_Documento_Identidad_Breve + ': ' + response.arrEdit[0].Nu_Documento_Identidad + ' ' + response.arrEdit[0].No_Entidad);

    //Formato PDF
    var arrTipoRecepcion = [
      { "value": "5", "nombre": "Tienda" },
      { "value": "6", "nombre": "Delivery" },
      { "value": "7", "nombre": "Recojo en Tienda" },
    ];
    $('#cbo-recepcion-modal').html('');
    for (var i = 0; i < arrTipoRecepcion.length; i++) {
      $('#cbo-recepcion-modal').append('<option value="' + arrTipoRecepcion[i]['value'] + '">' + arrTipoRecepcion[i]['nombre'] + '</option>');
    }
    
    /* Personal de ventas */
    $( '#cbo-vendedor-modal' ).html('<option value="">- No hay personal -</option>');
    url = base_url + 'HelperController/getPersonalVentas';
    $.post( url, {}, function( responsePersonal ){
      if ( responsePersonal.sStatus == 'success' ) {
        var l = responsePersonal.arrData.length;
        if (l==1) {
          $( '#cbo-vendedor-modal' ).html('<option value="">- Seleccionar -</option>');
          selected = '';
          if(response.arrEdit[0].ID_Mesero == responsePersonal.arrData[0].ID)
            selected = 'selected="selected"';
          $( '#cbo-vendedor-modal' ).append( '<option value="' + responsePersonal.arrData[0].ID + '" ' + selected + '>' + responsePersonal.arrData[0].Nombre + '</option>' );
        } else {
          $( '#cbo-vendedor-modal' ).html('<option value="" selected="selected">- Seleccionar -</option>');
          for (var x = 0; x < l; x++) {
            selected = '';
            if(response.arrEdit[0].ID_Mesero == responsePersonal.arrData[x].ID)
              selected = 'selected="selected"';
            $( '#cbo-vendedor-modal' ).append( '<option value="' + responsePersonal.arrData[x].ID + '" ' + selected + '>' + responsePersonal.arrData[x].Nombre + '</option>' );
          }
        }
      } else {
        if( responsePersonal.sMessageSQL !== undefined ) {
          console.log(responsePersonal.sMessageSQL);
        }
        console.log(responsePersonal.sMessage);
      }
    }, 'JSON');
    /* /. Personal de ventas */

  	/* Porcentaje para ventas */
    $( '#cbo-porcentaje-modal' ).html('<option value="" selected="selected">- No hay porcentaje -</option>');
    url = base_url + 'HelperController/getDataGeneral';
    $.post( url, {sTipoData: 'Porcentaje_Comision_Vendedores'}, function( responsePorcentaje ){
      if ( responsePorcentaje.sStatus == 'success' ) {
        var l = responsePorcentaje.arrData.length;
        if (l==1) {
          $( '#cbo-porcentaje-modal' ).html('<option value="">- Seleccionar -</option>');
          selected = '';
          if(response.arrEdit[0].ID_Comision == responsePorcentaje.arrData[0].ID_Tabla_Dato)
            selected = 'selected="selected"';
          $( '#cbo-porcentaje-modal' ).append( '<option value="' + responsePorcentaje.arrData[0].ID_Tabla_Dato + '" ' + selected + '>' + responsePorcentaje.arrData[0].No_Descripcion + '</option>' );
        } else {
          $( '#cbo-porcentaje-modal' ).html('<option value="" selected="selected">- Seleccionar -</option>');
          for (var x = 0; x < l; x++) {
            selected = '';
            if(response.arrEdit[0].ID_Comision == responsePorcentaje.arrData[x].ID_Tabla_Dato)
              selected = 'selected="selected"';
            $( '#cbo-porcentaje-modal' ).append( '<option value="' + responsePorcentaje.arrData[x].ID_Tabla_Dato + '" ' + selected + '>' + responsePorcentaje.arrData[x].No_Descripcion + '</option>' );
          }
        }
      } else {
        if( responsePorcentaje.sMessageSQL !== undefined ) {
          console.log(responsePorcentaje.sMessageSQL);
        }
        console.log(responsePorcentaje.sMessage);
      }
    }, 'JSON');
    /* /. Porcentaje para ventas */
      
    url = base_url + 'HelperController/getMonedas';
    $.post( url , function( responseMonedas ){
      $( '#cbo-Monedas' ).html('');
      for (var i = 0; i < responseMonedas.length; i++){
        if(response.arrEdit[0].ID_Moneda == responseMonedas[i].ID_Moneda)
          $( '.span-signo' ).text( responseMonedas[i].No_Signo );
      }
    }, 'JSON');
      
    $('.div-modal_credito').hide();
    url = base_url + 'HelperController/getMediosPago';
    $.post(url, function (responseFormaPago) {
      $('#cbo-MediosPago-modal').html('');
      for (var i = 0; i < responseFormaPago.length; i++) {
        selected = '';
        if(response.arrEdit[0].ID_Medio_Pago == responseFormaPago[i]['ID_Medio_Pago'])
          selected = 'selected="selected"';
        $('#cbo-MediosPago-modal').append('<option value="' + responseFormaPago[i]['ID_Medio_Pago'] + '" data-nu_tipo="' + responseFormaPago[i]['Nu_Tipo'] + '" ' + selected + '>' + responseFormaPago[i]['No_Medio_Pago'] + '</option>');
      }
      
      if(response.arrEdit[0].Nu_Tipo_Forma_Pago==1){//1=credito
        $('.div-modal_credito').show();

        //Fecha de vencimiento
        if(parseInt(response.arrEdit[0].Nu_Dias_Credito) > 0) {
          var dNuevaFechaVencimiento = sumaFecha(response.arrEdit[0].Nu_Dias_Credito, $( '#txt-Fe_Emision_modal' ).val());
          $( '#txt-Fe_Vencimiento-modal' ).val( dNuevaFechaVencimiento );
        } else {
          var dNuevaFechaVencimiento = sumaFecha(1, $( '#txt-Fe_Emision_modal' ).val());
          $( '#txt-Fe_Vencimiento-modal' ).val( dNuevaFechaVencimiento );
        }
      }
    }, 'JSON');

	  $( '#table-DetalleProductosOrdenVentaModal thead' ).empty();
	  $( '#table-DetalleProductosOrdenVentaModal tbody' ).empty();

    html_table_orden_detalle +=
    '<input type="hidden" id="txt-ID_Empresa" value="' + response.arrEdit[0].ID_Empresa + '">'
    +'<input type="hidden" id="txt-ID_Documento_Cabecera" value="' + response.arrEdit[0].ID_Documento_Cabecera + '">'
    +'<input type="hidden" id="modal-txt-TiposDocumentoModificar" value="' + response.arrEdit[0].ID_Tipo_Documento + '">'
    +'<input type="hidden" id="modal-txt-ID_Entidad" value="' + response.arrEdit[0].ID_Entidad + '">'
    +'<input type="hidden" id="modal-txt-Txt_Direccion_Entidad" value="' + response.arrEdit[0].Txt_Direccion_Entidad + '">'
    +'<input type="hidden" id="modal-txt-Nu_Celular_Entidad" value="' + response.arrEdit[0].Nu_Celular_Entidad + '">'
    +'<input type="hidden" id="modal-txt-Txt_Email_Entidad" value="' + response.arrEdit[0].Txt_Email_Entidad + '">'
    +'<input type="hidden" id="txt-ID_Medio_Pago" value="' + response.arrEdit[0].ID_Medio_Pago + '">'
    +'<input type="hidden" id="txt-iTipoFormaPago" value="' + response.arrEdit[0].Nu_Tipo_Forma_Pago + '">'
    +'<input type="hidden" id="txt-ID_Moneda" value="' + response.arrEdit[0].ID_Moneda + '">'
    +'<input type="hidden" id="txt-ID_Entidad" value="' + response.arrEdit[0].ID_Entidad + '">'
    +'<input type="hidden" id="txt-ID_Contacto" value="' + response.arrEdit[0].ID_Contacto + '">'
    +'<input type="hidden" id="txt-Txt_Glosa" value="' + response.arrEdit[0].Txt_Glosa + '">'
    +'<input type="hidden" id="txt-Fe_Vencimiento_Modal" value="' + ParseDateString(response.arrEdit[0].Fe_Vencimiento, 6, '-') + '">'
    +'<input type="hidden" id="txt-ID_Lista_Precio_Cabecera" value="' + response.arrEdit[0].ID_Lista_Precio_Cabecera + '">'
    +'<input type="hidden" id="txt-Ss_Descuento_Impuesto-modal" value="' + response.arrEdit[0].Ss_Descuento_Impuesto + '">'
    +'<input type="hidden" id="txt-Ss_Descuento_Impuesto_Operacion-modal" value="' + response.arrEdit[0].Ss_Descuento_Impuesto + '">'
    +'<input type="hidden" id="txt-Ss_Descuento_Operacion-modal" value="' + response.arrEdit[0].Ss_Descuento + '">'
    + '<div class="row">'
      +'<div class="col-md-12">'
		    +'<div id="panel-DetalleProductosOrdenVenta_modal" class="panel panel-default">'
          +'<div class="panel-heading panel-heading_table"><i class="fa fa-shopping-cart"></i> <b>Detalle de items</b></div>'
          +'<div class="panel-body">'
            +'<div class="tab-panel_default_modal_table row">'
              +'<div class="col-md-12">'
                +'<div class="table-responsive">'
                  +'<table id="table-DetalleProductosOrdenVentaModal" class="table table-striped table-bordered">'
                    +'<thead>'
                      +'<tr>'
                        +'<th class="text-center"><input type="checkbox" class="flat-red" onclick="checkAllOrden();" id="check-AllOrden" checked></th>'
                        +'<th style="display:none;" class="text-left"></th>'
                        +'<th class="text-center">Cantidad</th>'
                        +'<th class="text-center">Item</th>'
                        +'<th class="text-center">Precio</th>'
                        +'<th class="text-center" style="width: 17%;">Impuesto</th>'
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
    +'</div>';
    
    $( '#div-modal-body-orden' ).append(html_table_orden_detalle);
	  
	  //Detalle
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
    var $fTotalCantidad=0.00;
    
    var $fDescuento_Producto = 0;
    var fDescuento_Total_Producto = 0;
    var globalImpuesto = 0;
    var $iDescuentoGravada = 0;
    var $iDescuentoExonerada = 0;
    var $iDescuentoInafecto = 0;
    var $iDescuentoGratuita = 0;
    var $iDescuentoGlobalImpuesto = 0, $fTotalIcbper=0.00;
    var selected;

    var iTotalRegistros = response.arrEdit.length;
    var iTotalRegistrosImpuestos = response.arrImpuesto.length;
    for (var i = 0; i < iTotalRegistros; i++) {
      if (_ID_Producto != response.arrEdit[i].ID_Producto) {
        _ID_Producto = response.arrEdit[i].ID_Producto;
        option_impuesto_producto = '';
      }
      
      $fTotalCantidad += parseFloat(response.arrEdit[i].Qt_Producto);

      $Ss_SubTotal_Producto = parseFloat(response.arrEdit[i].Ss_SubTotal_Producto);
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
      $fTotalIcbper += parseFloat(response.arrEdit[i].Ss_Icbper);
      
      for (var x = 0; x < iTotalRegistrosImpuestos; x++){
        selected = '';
        if (response.arrImpuesto[x].ID_Impuesto_Cruce_Documento == response.arrEdit[i].ID_Impuesto_Cruce_Documento)
          selected = 'selected="selected"';
        option_impuesto_producto += "<option value='" + response.arrImpuesto[x].ID_Impuesto_Cruce_Documento + "' data-nu_tipo_impuesto='" + response.arrImpuesto[x].Nu_Tipo_Impuesto + "' data-impuesto_producto='" + response.arrImpuesto[x].Ss_Impuesto + "' " + selected + ">" + response.arrImpuesto[x].No_Impuesto + "</option>";
      }
      
      table_detalle_producto += 
      "<tr id='tr_detalle_producto_modal" + response.arrEdit[i].ID_Producto + "'>"
        +"<td style='display:none;' class='text-left td-iIdItem'>" + response.arrEdit[i].ID_Producto + "</td>"
        +"<td class='text-center'><input type='checkbox' class='flat-red check-orden' onclick='calcularTotalChecked();' checked></td>"
        +"<td class='text-right td-fCantidad' data-ss_icbper_item='" + response.arrEdit[i].Ss_Icbper + "' data-ss_icbper='" + response.arrEdit[i].Ss_Icbper_Item + "' data-id_impuesto_icbper='" + response.arrEdit[i].ID_Impuesto_Icbper + "'>" + Math.round10(response.arrEdit[i].Qt_Producto, -6) + "</td>"
        +"<td class='text-left'>" + response.arrEdit[i].Nu_Codigo_Barra + " " + response.arrEdit[i].No_Producto + (response.arrEdit[i].Txt_Nota_Item != null ? " " + response.arrEdit[i].Txt_Nota_Item : "") + "</td>"
        +"<td class='text-right td-Ss_Precio'>" + Math.round10(response.arrEdit[i].Ss_Precio, -6) + "</td>"
        +"<td class='text-left'>" + response.arrEdit[i].No_Impuesto_Breve + " " + response.arrEdit[i].Po_Impuesto + " %</td>"
        +"<td style='display:none;' class='text-left td-iIdImpuestoDetalle'>" + response.arrEdit[i].ID_Impuesto_Cruce_Documento +"</td>"
        +"<td style='display:none;' class='text-left td-Nu_Tipo_Impuesto'>" + response.arrEdit[i].Nu_Tipo_Impuesto +"</td>"
        +"<td class='text-right td-Ss_SubTotal_Producto'>" + response.arrEdit[i].Ss_SubTotal_Producto + "</td>"
        +"<td class='text-right td-Ss_Descuento'>" + (response.arrEdit[i].Po_Descuento_Impuesto_Producto == 0.00 ? '' : response.arrEdit[i].Po_Descuento_Impuesto_Producto) + "</td>"
        +"<td class='text-right td-Ss_Total_Producto'>" + response.arrEdit[i].Ss_Total_Producto + "</td>"
        +"<td style='display:none;' class='text-left td-Ss_Impuesto_Item'>" + response.arrEdit[i].Ss_Impuesto + "</td>"
        +"<td style='display:none;' class='text-right td-fDescuentoSinImpuestosItem'>" + (response.arrEdit[i].Ss_Descuento_Producto == 0.00 ? '' : response.arrEdit[i].Ss_Descuento_Producto) + "</td>"
        +"<td style='display:none;' class='text-right td-fDescuentoImpuestosItem'>" + (response.arrEdit[i].Ss_Descuento_Impuesto_Producto == 0.00 ? '' : response.arrEdit[i].Ss_Descuento_Impuesto_Producto) + "</td>"
        +"<td style='display:none;' class='text-right td-fValorUnitario'>" + parseFloat(response.arrEdit[i].Ss_Precio / response.arrEdit[i].Ss_Impuesto).toFixed(2) + "</td>"
        +"<td style='display:none;' class='text-right td-sNotaItem'>" + response.arrEdit[i].Txt_Nota_Item + "</td>"
      +"</tr>";
    }
    
	  $( '#table-DetalleProductosOrdenVentaModal >tbody' ).append(table_detalle_producto);

    //descuento por total
    if( parseFloat(response.arrEdit[0].Ss_Descuento) > 0 ) {
      $Ss_Gravada = $Ss_Gravada - parseFloat(response.arrEdit[0].Ss_Descuento);
      $Ss_IGV = $Ss_IGV - parseFloat(response.arrEdit[0].Ss_Descuento_Impuesto);
    }

    //Orden totales
    var html_orden_totales='';
    html_orden_totales += 
    '<div class="row">'
      +'<div class="col-md-12">'
      +'<div class="panel panel-default">'
        +'<div class="panel-heading text-right"><b>TOTAL </b>CANTIDAD: <span id="modal-span-total_cantidad" style="font-size: 20px;font-weight: bold;">' + $fTotalCantidad.toFixed(2) + '</span>&nbsp;&nbsp;IMPORTE: <span class="span-signo" style="font-size: 20px;font-weight: bold;"></span> <span id="modal-span-total_importe" style="font-size: 20px;font-weight: bold;">' + parseFloat(response.arrEdit[0].Ss_Total).toFixed(2) + '</span><button type="button" id="modal-btn-ver_total_todo" class="btn btn-link" data-ver_total_todo="0">Ver</button></div>'
        +'<div class="panel-body modal-panel_body_total_todo">'
          +'<div class="row">'
          +'<div class="table-responsive">'
          +'<table class="table" id="table-OrdenVentaTotal">'
            +'<tr style="display:none;">'
              +'<td style="display:none;"><label>% Descuento</label></td>'
              +'<td style="display:none;" class="text-right">'
				        +'<input type="tel" class="form-control input-decimal" id="txt-Ss_Descuento-modal" name="Ss_Descuento" size="3" value="' + response.arrEdit[0].Po_Descuento + '" autocomplete="off" />'
              +'</td>'
            +'</tr>'
            +'<tr class="">'
              +'<td class="text-right hidden-xs hidden-sm"><label>OP. Gravadas</label></td>'
              +'<td class="text-right hidden-xs hidden-sm"><label>OP. Inafectas</label></td>'
              +'<td class="text-right hidden-xs hidden-sm"><label>OP. Exoneradas</label></td>'
              +'<td class="text-right hidden-xs hidden-sm"><label>Gratuitas</label></td>'
              +'<td class="text-right"><label>Descuento (-)</label></td>'
              +'<td class="text-right hidden-xs hidden-sm"><label>I.G.V. %</label></td>'
              +'<td class="text-right"><label>ICBPER</label></td>'
              +'<td class="text-right"><label>Total</label></td>'
            +'</tr>'
            +'<tr class="">'
              +'<td class="text-right hidden-xs hidden-sm">'
  			        +'<input type="hidden" class="form-control" id="txt-subTotal_modal" value="' + $Ss_Gravada.toFixed(2) + '"/>'
                +'<span class="span-signo"></span> <span id="span-subTotal_modal">' + $Ss_Gravada.toFixed(2) + '</span>'
              +'</td>'
              
              +'<td class="text-right hidden-xs hidden-sm">'
                +'<input type="hidden" class="form-control" id="txt-inafecto_modal" value="' + $Ss_Inafecto.toFixed(2) + '"/>'
                +'<span class="span-signo"></span> <span id="span-inafecto_modal">' + $Ss_Inafecto.toFixed(2) + '</span>'
              +'</td>'
              
              +'<td class="text-right hidden-xs hidden-sm">'
                +'<input type="hidden" class="form-control" id="txt-exonerada_modal" value="' + $Ss_Exonerada.toFixed(2) + '"/>'
                +'<span class="span-signo"></span> <span id="span-exonerada_modal">' + $Ss_Exonerada.toFixed(2) + '</span>'
              +'</td>'
              
              +'<td class="text-right hidden-xs hidden-sm">'
                +'<input type="hidden" class="form-control" id="txt-gratuita_modal" value="' + $Ss_Gratuita.toFixed(2) + '"/>'
                +'<span class="span-signo"></span> <span id="span-gratuita_modal">' + $Ss_Gratuita.toFixed(2) + '</span>'
              +'</td>'
              
              +'<td class="text-right">'
                +'<input type="hidden" class="form-control" id="txt-descuento_modal" value="' + response.arrEdit[0].Ss_Descuento + '"/>'
                +'<span class="span-signo"></span> <span id="span-descuento_modal">' + response.arrEdit[0].Ss_Descuento + '</span>'
              +'</td>'
              
              +'<td class="text-right hidden-xs hidden-sm">'
                +'<input type="hidden" class="form-control" id="txt-impuesto_modal" value="' + $Ss_IGV.toFixed(2) + '"/>'
                +'<span class="span-signo"></span> <span id="span-impuesto_modal">' + $Ss_IGV.toFixed(2) + '</span>'
              +'</td>'
              
              + '<td class="text-right">'
                + '<input type="hidden" class="form-control" id="txt-total_icbper_modal" value="' + $fTotalIcbper.toFixed(2) + '"/>'
                + '<span class="span-signo"></span> <span id="span-total_icbper_modal">' + $fTotalIcbper.toFixed(2) + '</span>'
              + '</td>'
              
              +'<td class="text-right">'
                + '<input type="hidden" class="form-control" id="txt-total_modal" value="' + parseFloat(response.arrEdit[0].Ss_Total).toFixed(2) + '"/>'
                + '<span class="span-signo"></span> <span id="span-total_modal">' + parseFloat(response.arrEdit[0].Ss_Total).toFixed(2) + '</span>'
              +'</td>'
            +'</tr>'
          +'</table>'
          +'</div>'
          +'</div>'
        +'</div>'
      +'</div>'
    +'</div>'
    ;
    
    $( '#div-modal-body-orden' ).append(html_orden_totales);
    $( '#modal-loader' ).modal('hide');
    
    $('.modal-panel_body_total_todo').css("display", "none");
    $('#modal-btn-ver_total_todo').data('ver_total_todo', 0);

    $('#modal-btn-ver_total_todo').click(function () {
      if ($(this).data('ver_total_todo') == 1) {
        //setter
        $('#modal-btn-ver_total_todo').data('ver_total_todo', 0);
        $('#modal-btn-ver_total_todo').text('ver');
      } else {
        $('#modal-btn-ver_total_todo').data('ver_total_todo', 1);
        $('#modal-btn-ver_total_todo').text('ocultar');
      }

      if ($(this).data('ver_total_todo') == 1) {
        $('.modal-panel_body_total_todo').css("display", "");
      } else {
        $('.modal-panel_body_total_todo').css("display", "none");
      }
    })
  })//Fin Get JSON
  //Fin orden detalle

  $( '#btn-modal-facturar-orden' ).off('click').click(function () {
    accion_orden_venta = 'add_orden_venta_modal';
    addVenta();
  });
}

function checkAllOrden(){
	if ( $( '#check-AllOrden' ).prop('checked') ) {
	  $( '.check-orden' ).prop('checked', true);
	  $( '#check-AllOrden' ).prop('checked', true);
	  calcularTotalChecked();
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
      $('#span-descuento_modal').text('0.00');

      $('#txt-total_icbper_modal').val(0.00);
      $('#span-total_icbper_modal').text('0.00');
    
    	$( '#txt-total_modal' ).val( 0.00 );
    	$( '#span-total_modal' ).text( '0.00' );
	  }
	}
}

function calcularTotalChecked(){
  var $Ss_SubTotal = 0.00;
  var $Ss_Exonerada = 0.00;
  var $Ss_Inafecto = 0.00;
  var $Ss_Gratuita = 0.00;
  var $Ss_IGV = 0.00;
  var $Ss_Total = 0.00;
  var iCantDescuento = 0;
  var globalImpuesto = 0;
  var $fTotalIcbper = 0.00;
  var $fTotalCantidad = 0.00;

  var $iCantidadItemsSeleccionados=0;

  $('#table-DetalleProductosOrdenVentaModal > tbody > tr').each(function () {
    var rows = $(this);
    
    if (rows.find('input[type="checkbox"]').is(':checked')) {
      var rows = $(this);

      //var $fDescuentoSinImpuestosItem = rows.find(".td-fDescuentoSinImpuestosItem").text();
      var $Nu_Tipo_Impuesto  = rows.find(".td-Nu_Tipo_Impuesto").text();
      var $ID_Impuesto_Cruce_Documento  = rows.find(".td-iIdImpuestoDetalle").text();
      var $Ss_SubTotal_Producto         = parseFloat(rows.find(".td-Ss_SubTotal_Producto").text());
      var $Ss_Descuento_Producto        = parseFloat(rows.find(".td-Ss_Descuento").text());
      var $Ss_Impuesto                  = parseFloat(rows.find(".td-Ss_Impuesto_Item").text());
      var $Ss_Total_Producto = parseFloat(rows.find(".td-Ss_Total_Producto").text());

      $fTotalIcbper = parseFloat(rows.find(".td-fCantidad").data('ss_icbper_item'));

      $fTotalCantidad += parseFloat(rows.find(".td-fCantidad").text());

      $Ss_Total += $Ss_Total_Producto;
      
      if ($Nu_Tipo_Impuesto == 1) {
        $Ss_SubTotal += $Ss_SubTotal_Producto;
        $Ss_IGV += $Ss_Total_Producto - $Ss_SubTotal_Producto;
        globalImpuesto = $Ss_Impuesto;
      } else if ($Nu_Tipo_Impuesto == 2) {
        $Ss_Inafecto += $Ss_SubTotal_Producto;
      } else if ($Nu_Tipo_Impuesto == 3) {
        $Ss_Exonerada += $Ss_SubTotal_Producto;
      } else {
        $Ss_Gratuita += $Ss_SubTotal_Producto;
      }
      
      if(isNaN($Ss_Descuento_Producto))
        $Ss_Descuento_Producto = 0;

      ++$iCantidadItemsSeleccionados;
    }
  });
  
  if ( parseFloat( $( '#txt-descuento_modal' ).val() ) > 0 ) {
    var $iCantidadItemsTable = 0, $fDescuentoTotalSinImpuesto = 0, $fDescuentoTotalIGV = 0;
    $( '#table-DetalleProductosOrdenVentaModal > tbody > tr' ).each(function(){
      fila = $(this);
      $iCantidadItemsTable += 1;
    });
    
    var $fDescuentoxTotalDividoEntreItem = (parseFloat( $( '#txt-Ss_Descuento_Operacion-modal' ).val() ) / $iCantidadItemsTable);
    var $fDescuentoIGVxTotalDividoEntreItem = (parseFloat( $( '#txt-Ss_Descuento_Impuesto_Operacion-modal' ).val() ) / $iCantidadItemsTable);

    console.log('fDescuentoxTotalDividoEntreItem' + $fDescuentoxTotalDividoEntreItem);

    $('#table-DetalleProductosOrdenVentaModal > tbody > tr').each(function () {
      var rows = $(this);
      
      if (rows.find('input[type="checkbox"]').is(':checked')) {
        var rows = $(this);
        
        var $Nu_Tipo_Impuesto  = rows.find(".td-Nu_Tipo_Impuesto").text();

        if ($Nu_Tipo_Impuesto == 1) {
          $Ss_SubTotal = $Ss_SubTotal - $fDescuentoxTotalDividoEntreItem;
          $Ss_IGV = $Ss_IGV - $fDescuentoIGVxTotalDividoEntreItem;
        } else if ($Nu_Tipo_Impuesto == 2) {
          $Ss_Inafecto = $Ss_Inafecto - $fDescuentoxTotalDividoEntreItem;
        } else if ($Nu_Tipo_Impuesto == 3) {
          $Ss_Exonerada = $Ss_Exonerada - $fDescuentoxTotalDividoEntreItem;
        }

        $Ss_Total = $Ss_Total - (parseFloat($fDescuentoxTotalDividoEntreItem) + parseFloat($fDescuentoIGVxTotalDividoEntreItem))

        $fDescuentoTotalSinImpuesto += $fDescuentoxTotalDividoEntreItem;
        $fDescuentoTotalIGV += $fDescuentoIGVxTotalDividoEntreItem;
      }
    })
  }

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
	
	$( '#txt-Ss_Descuento_Impuesto-modal' ).val( $fDescuentoTotalIGV );
	$( '#txt-descuento_modal' ).val( $fDescuentoTotalSinImpuesto );
	$( '#span-descuento_modal' ).text( $fDescuentoTotalSinImpuesto );

  $('#txt-total_icbper_modal').val($fTotalIcbper.toFixed(2));
  $('#span-total_icbper_modal').text($fTotalIcbper.toFixed(2));

  $('#txt-total_modal').val(($Ss_Total + $fTotalIcbper).toFixed(2));
  $('#span-total_modal').text(($Ss_Total + $fTotalIcbper).toFixed(2));

  $('#modal-span-total_cantidad').text($fTotalCantidad.toFixed(2));
  $('#modal-span-total_importe').text(($Ss_Total + $fTotalIcbper).toFixed(2));
}

function addVenta(){
  if (accion_orden_venta=='add_orden_venta_modal') {
    var arrDetalleVenta = [];
    
    $('#table-DetalleProductosOrdenVentaModal > tbody > tr').each(function () {
      var rows = $(this);
      
      if (rows.find('input[type="checkbox"]').is(':checked')) {
        var $ID_Producto                  = rows.find(".td-iIdItem").text();
        var $Qt_Producto                  = rows.find(".td-fCantidad").text();
        var $Ss_Precio                    = rows.find(".td-Ss_Precio").text();
        var $ID_Impuesto_Cruce_Documento  = rows.find(".td-iIdImpuestoDetalle").text();
        var $Ss_SubTotal                  = rows.find(".td-Ss_SubTotal_Producto").text();
        var $Ss_Descuento                 = rows.find(".td-Ss_Descuento").text();
        var $Ss_Total                     = rows.find(".td-Ss_Total_Producto").text();
        var $fDescuentoSinImpuestosItem = rows.find(".td-fDescuentoSinImpuestosItem").text();
        var $fDescuentoImpuestosItem = rows.find(".td-fDescuentoImpuestosItem").text();
        var $fValorUnitario = rows.find(".td-fValorUnitario").text();
        var $fIcbperItem = rows.find(".td-fCantidad").data('ss_icbper_item');
        var $sNotaItem = rows.find(".td-sNotaItem").text();
        
        var obj = {};
        
        obj.ID_Producto = $ID_Producto;
        obj.fValorUnitario = $fValorUnitario;
        obj.Ss_Precio	                  = $Ss_Precio;
        obj.Qt_Producto	                = $Qt_Producto;
        obj.ID_Impuesto_Cruce_Documento	= $ID_Impuesto_Cruce_Documento;
        obj.Ss_SubTotal	                = $Ss_SubTotal;
        obj.Ss_Descuento	              = $Ss_Descuento;
        obj.Ss_Impuesto	                = $Ss_Total - $Ss_SubTotal;
        obj.Ss_Total	                  = $Ss_Total;
        obj.fDescuentoSinImpuestosItem = $fDescuentoSinImpuestosItem;
        obj.fDescuentoImpuestosItem = $fDescuentoImpuestosItem;
        obj.fIcbperItem = $fIcbperItem;
        obj.Txt_Nota = $sNotaItem;
        arrDetalleVenta.push(obj);
      }
    });
      
    if ( $( '#cbo-tipo_documento_modal' ).val() == 0){
      $( '#cbo-tipo_documento_modal' ).closest('.form-group').find('.help-block').html('Seleccionar tipo');
      $( '#cbo-tipo_documento_modal' ).closest('.form-group').removeClass('has-success').addClass('has-error');

      scrollToError($('.modal-orden .modal-body'), $( '#cbo-tipo_documento_modal' ));
    } else if ( $( '#cbo-serie_documento_modal' ).val() == 0){
      $( '#cbo-serie_documento_modal' ).closest('.form-group').find('.help-block').html('Seleccionar serie');
      $( '#cbo-serie_documento_modal' ).closest('.form-group').removeClass('has-success').addClass('has-error');

      scrollToError($('.modal-orden .modal-body'), $( '#cbo-serie_documento_modal' ));
    } else if ( arrDetalleVenta.length == 0){
  		$( '#panel-DetalleProductosOrdenVenta_modal' ).removeClass('panel-default');
  		$( '#panel-DetalleProductosOrdenVenta_modal' ).addClass('panel-danger');
  		
    	$( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
    	
      $( '#modal-message' ).modal('show');
      
      $( '.modal-message' ).addClass( 'modal-danger' );
      $( '.modal-title-message' ).text( 'No ha seleccionado ningún producto' );
      
      $( '.modal-message' ).css("z-index", "2000");
      
      setTimeout(function() {$('#modal-message').modal('hide'); }, 1100);
    } else {
  		$( '#panel-DetalleProductosOrdenVenta_modal' ).removeClass('panel-danger');
  		$( '#panel-DetalleProductosOrdenVenta_modal' ).addClass('panel-default');
      
  		var arrVentaCabecera = Array();
  		arrVentaCabecera = {
  		  'esEnlace'                  : 1,//Este parametro se usa solo cuando se desea verificar ND o NC
  		  'EID_Empresa'               : '',
  		  'EID_Documento_Cabecera'    : '',
        'ID_Entidad': $('#txt-ID_Entidad').val(),
        'Txt_Email_Entidad': $('#modal-txt-Txt_Email_Entidad').val(),
        'Nu_Celular_Entidad': $('#modal-txt-Nu_Celular_Entidad').val(),
        'Txt_Direccion_Entidad': $('#modal-txt-Txt_Direccion_Entidad').val(),
        'ID_Sunat_Tipo_Transaction': $('#cbo-sunat_tipo_transaction-modal').val(),
  		  'ID_Tipo_Documento'         : $( '#cbo-tipo_documento_modal' ).val(),
        'ID_Serie_Documento_PK'   : $( '#cbo-serie_documento_modal' ).find(':selected').data('id_serie_documento_pk'),
  		  'ID_Serie_Documento'        : $( '#cbo-serie_documento_modal' ).val(),
  		  'ID_Numero_Documento'       : $( '#txt-ID_Numero_Documento' ).val(),
  		  'Fe_Emision'                : $( '#txt-Fe_Emision_modal' ).val(),
        'ID_Moneda': $('#txt-ID_Moneda').val(),
        'Fe_Vencimiento': $('#txt-Fe_Vencimiento-modal').val(),
  		  'Txt_Glosa'                 : $( '#txt-Txt_Glosa' ).val(),
  		  
        //'Nu_Detraccion'             : 0,
        //'Nu_Retencion': 0,
        
        'Nu_Detraccion'           : $( '[name="radio-addDetraccion"]:checked' ).attr('value'),
        'Po_Detraccion': $('[name="Po_Detraccion"]').val(),
        'Nu_Retencion': $('[name="radio-addRetencion"]:checked').attr('value'),

  		  'Po_Descuento'              : $( '#txt-Ss_Descuento-modal' ).val(),
  		  'Ss_Descuento'              : $( '#txt-descuento_modal' ).val(),
  		  'Ss_Total'                  : $( '#txt-total_modal' ).val(),
  		  'ID_Lista_Precio_Cabecera'  : $( '#txt-ID_Lista_Precio_Cabecera' ).val(),
  		  'ID_Documento_Cabecera_Orden' : $( '#txt-ID_Documento_Cabecera' ).val(),
  		  'Txt_Garantia' : '',
  		  'ID_Mesero' : $( '#cbo-vendedor-modal' ).val(),
  		  'ID_Comision' : $( '#cbo-porcentaje-modal' ).val(),
  		  'No_Formato_PDF' : $( '#cbo-modal-formato_pdf' ).val(),
        'Nu_Descargar_Inventario': $( '#cbo-modal-descargar_stock' ).val(),
        'ID_Almacen' : $( '#cbo-modal-almacen' ).val(),
        'ID_Medio_Pago': $('#cbo-MediosPago-modal').val(),
        'iTipoFormaPago': $('#cbo-MediosPago-modal').find(':selected').data('nu_tipo'),
        'iTipoCliente': '0',
        'ID_Tipo_Medio_Pago': $('#cbo-modal_tarjeta_credito').val(),
        'Nu_Transaccion': $('#tel-nu_referencia').val(),
        'Nu_Tarjeta': $('#tel-nu_ultimo_4_digitos_tarjeta').val(),
        'Nu_Tipo_Recepcion': $('#cbo-recepcion-modal').val(),
        'Fe_Entrega': $('#txt-fe_entrega-modal').val(),
        'ID_Transporte_Delivery': $('#modal-cbo-transporte').val(),
        'Txt_Direccion_Delivery': $('[name="Txt_Direccion_Delivery"]').val(),
        'Ss_Retencion' : 0,
        'Ss_Descuento_Impuesto': $('#txt-Ss_Descuento_Impuesto-modal').val(),

        'No_Orden_Compra_FE': $('[name="No_Orden_Compra_FE"]').val(),
        'No_Placa_FE': $('[name="No_Placa_FE"]').val(),
        'Nu_Expediente_FE': $('[name="Nu_Expediente_FE"]').val(),
        'Nu_Codigo_Unidad_Ejecutora_FE': $('[name="Nu_Codigo_Unidad_Ejecutora_FE"]').val()
  		};
  		
      var arrVentaModificar = Array();
      arrVentaModificar = {
        ID_Documento_Guardado: $('#txt-ID_Documento_Cabecera').val(),
        ID_Tipo_Documento_Modificar: $('#modal-txt-TiposDocumentoModificar').val(),
        ID_Serie_Documento_Modificar: null,
        ID_Numero_Documento_Modificar: null,
        Nu_Codigo_Motivo_Referencia: '',
        iIdEntidad: $('#modal-txt-ID_Entidad').val(),
        iTipoCliente: 0,
      };
  		var arrClienteNuevo = Array();
  		
      $( '#btn-modal-facturar-orden' ).text('');
      $( '#btn-modal-facturar-orden' ).attr('disabled', true);
      $( '#btn-modal-facturar-orden' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
      
      $( '#modal-loader' ).modal('show');
      $( '#modal-loader' ).css("z-index", "3000");
  
      url = base_url + 'Ventas/VentaController/crudVenta';
    	$.ajax({
        type		  : 'POST',
        dataType	: 'JSON',
    		url		    : url,
    		data		  : {
    		  arrVentaCabecera : arrVentaCabecera,
    		  arrDetalleVenta : arrDetalleVenta,
    		  arrVentaModificar : arrVentaModificar,
      	  arrClienteNuevo : arrClienteNuevo,
    		},
    		success : function( response ){
    		  $( '#modal-loader' ).modal('hide');
    		  
    	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
      	  $( '#modal-message' ).modal('show');
      	  $( '.modal-message' ).css("z-index", "4000");
      	  
    		  if (response.status == 'success'){
    		    accion_orden_venta='';
    		    
            $( '#form-datos_adicionales_venta' )[0].reset();

    		    $( '.modal-orden' ).modal('hide');
      	    $( '.modal-message' ).addClass(response.style_modal);
      	    $( '.modal-title-message' ).text(response.message);
      	    setTimeout(function() {$('#modal-message').modal('hide'); }, 1100);
      	    reload_table_orden_venta();
    		  } else {
      	    $( '.modal-message' ).addClass(response.style_modal);
            $( '.modal-title-message' ).text(response.message);
            if ( response.message_nubefact !== undefined && response.message_nubefact.length > 0 )
              $( '.modal-title-message' ).text(response.message_nubefact);
      	    setTimeout(function() {$('#modal-message').modal('hide');}, 6100);
    		  }
    		  
          $( '#btn-modal-facturar-orden' ).text('');
          $( '#btn-modal-facturar-orden' ).append( 'Generar Venta' );
          $( '#btn-modal-facturar-orden' ).attr('disabled', false);
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
    	    
          $( '#btn-modal-facturar-orden' ).text('');
          $( '#btn-modal-facturar-orden' ).append( 'Generar Venta' );
          $( '#btn-modal-facturar-orden' ).attr('disabled', false);
        }
    	});
    }
  }
}

function duplicarData($modal_delete, ID){
  $( '#modal-loader' ).modal('show');
    
  url = base_url + 'Ventas/OrdenVentaController/duplicarOrdenVenta/' + ID;
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
  	    reload_table_orden_venta();
		  } else {
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
		  }
    }
  });
}

function sendPDF($modal_delete, ID){
  $( '#modal-loader' ).modal('show');
  $modal_delete.modal('hide');
  url = base_url + 'Ventas/OrdenVentaController/getOrdenVentaPDF/' + ID;
  window.open(url,'_blank');
  $( '#modal-loader' ).modal('hide');
}

function _eliminarOrdenVenta($modal_delete, ID, Nu_Descargar_Inventario){
  $( '#modal-loader' ).modal('show');
    
  url = base_url + 'Ventas/OrdenVentaController/eliminarOrdenVenta/' + ID + '/' + Nu_Descargar_Inventario;
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
  	    reload_table_orden_venta();
		  } else {
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
		  }
		  accion_orden_venta='';
    },
    error: function (jqXHR, textStatus, errorThrown) {
		  accion_orden_venta='';
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

function validatePreviousDocumentToSaveOrderSale(){
  bEstadoValidacion = true;
  
  if ($('#cbo-TiposDocumento').val() == 0) {
    $('#cbo-TiposDocumento').closest('.form-group').find('.help-block').html('Seleccionar tipo');
    $('#cbo-TiposDocumento').closest('.form-group').removeClass('has-success').addClass('has-error');

    bEstadoValidacion = false;
    scrollToError($("html, body"), $('#cbo-TiposDocumento'));
  } else if ( $('[name="addCliente"]:checked').attr('value') == 0 && ($( '#txt-AID' ).val().length === 0 || $( '#txt-ANombre' ).val().length === 0)) {
    $( '#txt-ANombre' ).closest('.form-group').find('.help-block').html('Seleccionar cliente');
		$( '#txt-ANombre' ).closest('.form-group').removeClass('has-success').addClass('has-error');
		
	  bEstadoValidacion = false;
		scrollToError( $("html, body"), $( '#txt-ANombre' ) );
  } else if ( $( '#cbo-TiposDocumentoIdentidadCliente' ).val() == 4 && ($('[name="addCliente"]:checked').attr('value') == 1 && $( '#cbo-TiposDocumentoIdentidadCliente' ).find(':selected').data('nu_cantidad_caracteres') != $( '#txt-Nu_Documento_Identidad_Cliente').val().length) ) {
    $( '#txt-Nu_Documento_Identidad_Cliente' ).closest('.form-group').find('.help-block').html('Debe ingresar ' + $( '#cbo-TiposDocumentoIdentidadCliente' ).find(':selected').data('nu_cantidad_caracteres') + ' dígitos' );
	  $( '#txt-Nu_Documento_Identidad_Cliente' ).closest('.form-group').removeClass('has-success').addClass('has-error');
	  
	  bEstadoValidacion = false;
		scrollToError( $("html, body"), $( '#txt-Nu_Documento_Identidad_Cliente' ) );
  } else if ($('[name="addCliente"]:checked').attr('value') == 1 && ($('#txt-No_Entidad_Cliente').val().length == 1 || $('#txt-No_Entidad_Cliente').val().length == 2)) {//1 = Nuevo
    $('#txt-No_Entidad_Cliente').closest('.form-group').find('.help-block').html('Debes tener mínimo 3 carácteres');
    $('#txt-No_Entidad_Cliente').closest('.form-group').removeClass('has-success').addClass('has-error');

    bEstadoValidacion = false;
    scrollToError($("html, body"), $('#txt-No_Entidad_Cliente'));
  } else if ( $('[name="addContacto"]:checked').attr('value') == 1 && $( '#txt-No_Contacto' ).val().length === 0){
    $( '#txt-No_Contacto' ).closest('.form-group').find('.help-block').html('Ingresar nombre' );
	  $( '#txt-No_Contacto' ).closest('.form-group').removeClass('has-success').addClass('has-error');
	  
	  bEstadoValidacion = false;
		scrollToError( $("html, body"), $( '#txt-No_Contacto' ) );
  }
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

function enviarCorreo(ID, sCorreoUsuario, sCorreoContacto, sAsunto){
  var $modal_id = $( '#modal-orden_correo_sunat' );
  $modal_id.modal('show');
  
  $modal_id.removeClass('modal-default modal-danger modal-warning modal-success');
  $modal_id.addClass('modal-default');
  
  $( '#modal-header-orden-title' ).text('¿Enviar correo?');
  
  $( '#txt-orden-email_correo_sunat_de' ).val( sCorreoUsuario );
  $( '#txt-orden-email_correo_sunat_para' ).val( sCorreoContacto );
  $( '#txt-orden-email_correo_sunat_asunto' ).val( sAsunto );

	$modal_id.on('shown.bs.modal', function() {
		$( '#txt-orden-email_correo_sunat_para' ).focus();
	})
  
  $( '#btn-modal-footer-orden_correo_sunat-cancel' ).off('click').click(function () {
    $modal_id.modal('hide');
  });

	$( "#txt-orden-email_correo_sunat_de" ).blur(function() {
		caracteresCorreoValido($(this).val(), '#span-email');
  })

	$( "#txt-orden-email_correo_sunat_para" ).blur(function() {
		caracteresCorreoValido($(this).val(), '#span-email');
  })
  
  $( '#btn-modal-footer-orden_correo_sunat-send' ).off('click').click(function () {
    if (!caracteresCorreoValido($('#txt-orden-email_correo_sunat_de').val(), '#div-email') ) {
      scrollToError($('#modal-correo_sunat .modal-body'), $( '#txt-orden-email_correo_sunat_de' ));
    } else if (!caracteresCorreoValido($('#txt-orden-email_correo_sunat_para').val(), '#div-email') ) {
      scrollToError($('#modal-correo_sunat .modal-body'), $( '#txt-orden-email_correo_sunat_para' ));
    } else if ( $('#txt-orden-email_correo_sunat_asunto').val().length === 0 ) {
      $( '#txt-orden-email_correo_sunat_asunto' ).closest('.form-group').find('.help-block').html('Ingresar asunto');
      $( '#txt-orden-email_correo_sunat_asunto' ).closest('.form-group').removeClass('has-success').addClass('has-error');

      scrollToError($('#modal-correo_sunat .modal-body'), $( '#txt-orden-email_correo_sunat_asunto' ));
    } else {
      $( '#btn-modal-footer-orden_correo_sunat-cancel' ).attr('disabled', true);
      $( '#btn-modal-footer-orden_correo_sunat-send' ).text('');
      $( '#btn-modal-footer-orden_correo_sunat-send' ).attr('disabled', true);
      $( '#btn-modal-footer-orden_correo_sunat-send' ).append( 'Enviando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
        
      var sendPost = {
        iIdOrden : ID,
        sCorreoDe : $( '#txt-orden-email_correo_sunat_de' ).val(),
        sCorreoPara : $( '#txt-orden-email_correo_sunat_para' ).val(),
        sAsunto : $( '#txt-orden-email_correo_sunat_asunto' ).val(),
      };
      
      url = base_url + 'Ventas/OrdenVentaController/enviarCorreo';
      $.ajax({
        url       : url,
        type      : "POST",
        dataType  : "JSON",
        data      : sendPost,
        success: function( response ){
          $modal_id.modal('hide');
            
    	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
      	  $( '#modal-message' ).modal('show');
    		  
    		  if (response.status == 'success'){    		    
      	    $( '.modal-message' ).addClass(response.style_modal);
      	    $( '.modal-title-message' ).text(response.message);
      	    setTimeout(function() {$('#modal-message').modal('hide');}, 1200);
    		  } else {
      	    $( '.modal-message' ).addClass(response.style_modal);
      	    $( '.modal-title-message' ).text(response.message);
      	    setTimeout(function() {$('#modal-message').modal('hide');}, 1800);
          }
          
          $( '#btn-modal-footer-orden_correo_sunat-cancel' ).attr('disabled', false);
          $( '#btn-modal-footer-orden_correo_sunat-send' ).text('');
          $( '#btn-modal-footer-orden_correo_sunat-send' ).attr('disabled', false);
          $( '#btn-modal-footer-orden_correo_sunat-send' ).append( 'Enviar' );
        }
      });
    }
  });
}

function calcularIcbper() {
  var $fTotalIcbper = 0.00;
  $("#table-DetalleProductosOrdenVenta > tbody > tr").each(function () {
    var rows = $(this);
    var fCantidad = parseFloat(rows.find(".txt-Qt_Producto").val());
    var fIcbper = parseFloat(rows.find(".txt-Qt_Producto").data('ss_icbper'));
    var iIdIcbper = rows.find(".txt-Qt_Producto").data('id_impuesto_icbper');
    if (iIdIcbper == 1) {
      var fCalculoIcbperItem = (fCantidad * fIcbper);
      rows.find(".txt-Qt_Producto").data('ss_icbper_item', fCalculoIcbperItem);
      rows.find(".txt-Qt_Producto").attr('data-ss_icbper_item', fCalculoIcbperItem);
      $fTotalIcbper += fCalculoIcbperItem;
    }
  });

  $('#txt-total_icbper').val($fTotalIcbper.toFixed(2));
  $('#span-total_icbper').text($fTotalIcbper.toFixed(2));

  var $Ss_Total = parseFloat($('#txt-total').val());
  $('#txt-total').val(($Ss_Total + $fTotalIcbper).toFixed(2));
  $('#span-total').text(($Ss_Total + $fTotalIcbper).toFixed(2));
}

//WhatsApp
function sendWhatsapp(ID, iIdCliente, ID_Encode) {
  var $modal_id = $('#modal-whatsApp');
  $modal_id.modal('show');

  $('#modal-whatsApp').removeClass('modal-danger modal-warning modal-success');
  $('#modal-whatsApp').addClass('modal-success');

  $('.modal-header_message_whatsApp').text('¿Enviar WhatsApp?');

  // get cliente
  $('#txt-Nu_Celular_Entidad_Cliente_WhatsApp').val('');
  url = base_url + 'HelperController/getDataGeneral';
  $.post(url, { sTipoData: 'get_entidad', iTipoEntidad: 0, iIDEntidad: iIdCliente }, function (response) {
    if (response.sStatus == 'success') {
      $('#txt-Nu_Celular_Entidad_Cliente_WhatsApp').val(response.arrData[0].Nu_Celular_Entidad);
    } else {
      if (response.sMessageSQL !== undefined) {
        console.log(response.sMessageSQL);
      }
      console.log(response.sMessage);
    }
  }, 'JSON');
  // /. get cliente

  $('#modal-whatsApp').on('shown.bs.modal', function () {
    $('#txt-Nu_Celular_Entidad_Cliente_WhatsApp').focus();
  })

  $('#btn-modal_message_whatsApp-cancel').off('click').click(function () {
    $modal_id.modal('hide');
  });

  $('#btn-modal_message_whatsApp-send').off('click').click(function () {
    if (String(parseInt($('#txt-Nu_Celular_Entidad_Cliente_WhatsApp').val().replace(/ /g, ""))).length < 9) {
      $('#txt-Nu_Celular_Entidad_Cliente_WhatsApp').closest('.form-group').find('.help-block').html('Debes ingresar 9 dígitos');
      $('#txt-Nu_Celular_Entidad_Cliente_WhatsApp').closest('.form-group').removeClass('has-success').addClass('has-error');

      scrollToError($("html, body"), $('#txt-Nu_Celular_Entidad_Cliente_WhatsApp'));
      return false;
    } else {
      $('#btn-modal_message_whatsApp-send').text('');
      $('#btn-modal_message_whatsApp-send').attr('disabled', true);
      $('#btn-modal_message_whatsApp-send').append('Enviando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>');

      var sendPost = {
        ID: ID,
      };

      url = base_url + 'HelperController/getDatosCotizacionVentaWhatsApp/';
      $.ajax({
        url: url,
        type: "POST",
        dataType: "JSON",
        data: sendPost,
        success: function (response) {          
          $modal_id.modal('hide');

          $('.modal-message').removeClass('modal-danger modal-warning modal-success');
          $('#modal-message').modal('show');

          if (response.sStatus == 'success') {
            //Envío por whatsApp
            var sNumeroPeru = $('#txt-Nu_Celular_Entidad_Cliente_WhatsApp').val().replace(/ /g, "");
            var sDocumento = response.arrData[0].No_Tipo_Documento + ' *Nro. ' + response.arrData[0].ID_Numero_Documento + '*';

            //$sURLSendMessageWhatsapp = "https://wa.me/" . $phone . "?text=" . $message;
            var url_whatsapp = 'https://wa.me/';
            var url_whatsapp_cellphone_id_country = '51';
            var url_whatsapp_cellphone_entry = sNumeroPeru;
            var url_whatsapp_message = '?text=';

            url_whatsapp_message += 'Somos *' + (response.arrData[0].No_Empresa_Comercial != '' ? caracteresValidosWhatsApp(response.arrData[0].No_Empresa_Comercial) : caracteresValidosWhatsApp(response.arrData[0].No_Empresa)) + '*,\n';

            url_whatsapp_message += '\n*Cliente:* ' + caracteresValidosWhatsApp(response.arrData[0].No_Entidad);
            url_whatsapp_message += '\n*' + response.arrData[0].No_Tipo_Documento_Identidad_Breve + ':* ' + response.arrData[0].Nu_Documento_Identidad;
            url_whatsapp_message += '\n*Documento:* ' + sDocumento;
            url_whatsapp_message += '\n*Fecha de Emisión:* ' + ParseDate(response.arrData[0].Fecha_Emision);
            url_whatsapp_message += '\n*Fecha de Vencimiento:* ' + ParseDate(response.arrData[0].Fecha_Vencimiento);

            var iTotalRegistros = response.arrData.length;
            var responseDetalle = response.arrData;

            url_whatsapp_message += '\n\n*Detalle de Pedido*\n';
            url_whatsapp_message += '=============\n';
            for (var i = 0; i < iTotalRegistros; i++) {
              url_whatsapp_message += '✅ ' + number_format(responseDetalle[i].Qt_Producto, 2) + ' x *' + caracteresValidosWhatsApp(responseDetalle[i].sNombreItem.trim()) + '* - ' + responseDetalle[i].No_Signo + ' ' + number_format(responseDetalle[i].Ss_Precio, 2) + '\n';
            }

            url_whatsapp_message += '\n➡️ *Total:* ' + response.arrData[0].No_Signo + ' ' + number_format(response.arrData[0].Total, 2) + '\n';
            //Saldo
            if (parseFloat(response.arrData[0].Total_Saldo) > 0.00) {
              url_whatsapp_message += '\n➡️ Tiene un *saldo pendiente por pagar de ' + response.arrData[0].No_Signo + ' ' + number_format(response.arrData[0].Total_Saldo, 2) + '*\n';
            }

            //Vendedor
            if (response.arrData[0].No_Vendedor!='' && response.arrData[0].No_Vendedor!=null) {
              url_whatsapp_message += '\n*Atendido por*';
              url_whatsapp_message += '\n*' + caracteresValidosWhatsApp(response.arrData[0].No_Vendedor) + '*';
              if (response.arrData[0].Nu_Celular_Vendedor!='' && response.arrData[0].Nu_Celular_Vendedor!=null) {
                url_whatsapp_message += '\n*Celular:* ' + caracteresValidosWhatsApp(response.arrData[0].Nu_Celular_Vendedor);
              }
              if (response.arrData[0].Txt_Email_Vendedor!='' && response.arrData[0].Txt_Email_Vendedor!=null) {
                url_whatsapp_message += '\n*Email:* ' + caracteresValidosWhatsApp(response.arrData[0].Txt_Email_Vendedor);
              }
              url_whatsapp_message += '\n';
            }

            //visualiza tu pdf
            url_whatsapp_message += '\nVisualizar *PDF* en el siguiente enlace:\n' + base_url + 'Ventas/OrdenVentaController/getOrdenVentaPDF/' + ID_Encode;

            url_whatsapp_message += (response.arrData[0].sTerminosCondicionesTicket != '' && response.arrData[0].sTerminosCondicionesTicket != null ? '\n\n' + response.arrData[0].sTerminosCondicionesTicket.replace(/<br \/>/g, "") : '');
            url_whatsapp_message += '\n\nGenerado por laesystems.com';

            var url = url_whatsapp + url_whatsapp_cellphone_id_country + url_whatsapp_cellphone_entry + url_whatsapp_message;

            url = encodeURI(url);
            var win = window.open(url, '_blank');

            $('#modal-message').modal('hide');
          } else {
            $('.modal-message').addClass('modal-' + response.sStatus);
            $('.modal-title-message').text(response.sMessage);
            setTimeout(function () { $('#modal-message').modal('hide'); }, 1800);
          }

          $('#btn-modal_message_whatsApp-send').text('');
          $('#btn-modal_message_whatsApp-send').attr('disabled', false);
          $('#btn-modal_message_whatsApp-send').append('Enviar');
        }
      });
    }
  });
}

function calcularDescuentoTotal(fila) {
  var $Ss_Descuento = parseFloat($('#txt-Ss_Descuento').val());
  var $Ss_Descuento_Porcentaje = (!isNaN($Ss_Descuento) ? ($Ss_Descuento / 100) : 0);
  var $Ss_SubTotal = 0.00;
  var $Ss_Exonerada = 0.00;
  var $Ss_Inafecto = 0.00;
  var $Ss_Gratuita = 0.00;
  var $Ss_IGV = 0.00;
  var $Ss_Total = 0.00;
  var globalImpuesto = 0;
  var $Ss_Descuento_Item = 0.00, iTipoDescuentoIGV = 0;

  var $fDescuentoSinImpuestoNuevo =0;
  var $fDescuentoIGVNuevo =0;

  $("#table-DetalleProductosOrdenVenta > tbody > tr").each(function () {
    var rows = $(this);
    var fImpuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
    var iGrupoImpuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
    var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
    var $Ss_Total_Producto = parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
    var $Ss_Igv_Item = 0.00;

    var $Ss_Total_Producto_Operacion = ($Ss_Total_Producto - ($Ss_Total_Producto * $Ss_Descuento_Porcentaje));
    
    //nueva modificacion 17/08/2022
    $fDescuentoSinImpuestoNuevo = (($Ss_Total_Producto - $Ss_Total_Producto_Operacion) / fImpuesto);
    $Ss_Descuento_Item += $fDescuentoSinImpuestoNuevo;
    $fDescuentoIGVNuevo += (($Ss_Total_Producto - $Ss_Total_Producto_Operacion) - $fDescuentoSinImpuestoNuevo);  
    //fin

    $Ss_SubTotal_Producto_Operacion = ($Ss_Total_Producto_Operacion / fImpuesto);

    if (iGrupoImpuesto == 1) {
      $Ss_SubTotal += $Ss_SubTotal_Producto_Operacion;
      $Ss_Igv_Item = (($Ss_SubTotal_Producto_Operacion * fImpuesto) - $Ss_SubTotal_Producto_Operacion)
      $Ss_IGV += $Ss_Igv_Item;
      $Ss_Total += ($Ss_SubTotal_Producto_Operacion + $Ss_Igv_Item);
      globalImpuesto = fImpuesto;
      iTipoDescuentoIGV = 1;
    } else if (iGrupoImpuesto == 2) {
      $Ss_Inafecto += $Ss_SubTotal_Producto_Operacion;
      globalImpuesto += 0;
      $Ss_Total += $Ss_SubTotal_Producto_Operacion;
    } else if (iGrupoImpuesto == 3) {
      $Ss_Exonerada += $Ss_SubTotal_Producto_Operacion;
      globalImpuesto += 0;
      $Ss_Total += $Ss_SubTotal_Producto_Operacion;
    } else {
      $Ss_Gratuita += $Ss_SubTotal_Producto_Operacion;
      globalImpuesto += 0;
      $Ss_Total += $Ss_SubTotal_Producto_Operacion;
    }
  });

  var iTipoDescuentoOtrosImpuestos = 0;
  if ($('#cbo-TiposDocumento').val() != 2) {
    $("#table-DetalleProductosOrdenVenta > tbody > tr").each(function () {
      var rows = $(this);
      var iGrupoImpuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
      if (iGrupoImpuesto != 1 && parseFloat($('#txt-Ss_Descuento').val())>0.00 ) {
        alert('No se puede brindar descuento TOTAL solo por ÍTEM. Si es IGV si se puede por TOTAL o ÍTEM.');
        iTipoDescuentoOtrosImpuestos = 1;
        return false;
      }
    });
  }

  if (iTipoDescuentoOtrosImpuestos==0) {
    $('#txt-subTotal').val($Ss_SubTotal.toFixed(2));
    $('#span-subTotal').text($Ss_SubTotal.toFixed(2));

    $('#txt-exonerada').val($Ss_Exonerada.toFixed(2));
    $('#span-exonerada').text($Ss_Exonerada.toFixed(2));

    $('#txt-inafecto').val($Ss_Inafecto.toFixed(2));
    $('#span-inafecto').text($Ss_Inafecto.toFixed(2));

    $('#txt-gratuita').val($Ss_Gratuita.toFixed(2));
    $('#span-gratuita').text($Ss_Gratuita.toFixed(2));

    $('#txt-impuesto').val($Ss_IGV.toFixed(2));
    $('#span-impuesto').text($Ss_IGV.toFixed(2));

    $('#txt-descuento_igv').val($fDescuentoIGVNuevo.toFixed(2));
    
    $('#txt-descuento').val($Ss_Descuento_Item.toFixed(2));
    $('#span-descuento').text($Ss_Descuento_Item.toFixed(2));

    $('#txt-total').val($Ss_Total.toFixed(2));
    $('#span-total').text($Ss_Total.toFixed(2));

    //calcular ICBPER
    var $fTotalIcbper = 0.00;
    $("#table-DetalleProductosOrdenVenta > tbody > tr").each(function () {
      var rows = $(this);
      var fCantidad = parseFloat(rows.find(".txt-Qt_Producto").val());
      var fIcbper = parseFloat(rows.find(".txt-Qt_Producto").data('ss_icbper'));
      var iIdIcbper = rows.find(".txt-Qt_Producto").data('id_impuesto_icbper');
      if (iIdIcbper == 1) {
        var fCalculoIcbperItem = (fCantidad * fIcbper);
        rows.find(".txt-Qt_Producto").data('ss_icbper_item', fCalculoIcbperItem);
        rows.find(".txt-Qt_Producto").attr('data-ss_icbper_item', fCalculoIcbperItem);
        $fTotalIcbper += fCalculoIcbperItem;
      }
    });

    $('#txt-total_icbper').val($fTotalIcbper.toFixed(2));
    $('#span-total_icbper').text($fTotalIcbper.toFixed(2));

    var $Ss_Total = parseFloat($('#txt-total').val());
    $('#txt-total').val(($Ss_Total + $fTotalIcbper).toFixed(2));
    $('#span-total').text(($Ss_Total + $fTotalIcbper).toFixed(2));
  } else {
    $('#txt-Ss_Descuento').val('');
  }
}

function calcularImportexItemTemporal(fila){
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
    $( '#table-DetalleProductosOrdenVenta tfoot' ).empty();
    if (nu_tipo_impuesto == 1 && considerar_igv == 1) {//CON IGV
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
      $("#table-DetalleProductosOrdenVenta > tbody > tr").each(function(){
        var rows = $(this);
        var Ss_Impuesto           = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
        var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
        var $Ss_SubTotal_Producto = parseFloat($('.txt-Ss_SubTotal_Producto', this).val());
        var $Ss_Descuento_Producto = parseFloat($('.txt-Ss_Descuento', this).val());
        var $Ss_Total_Producto = parseFloat($('.txt-Ss_Total_Producto', this).val());
  
        $Ss_Total += $Ss_Total_Producto;

        if(isNaN($Ss_Descuento_Producto))
          $Ss_Descuento_Producto = 0;
          
        if (Nu_Tipo_Impuesto == 1){
          $Ss_SubTotal += $Ss_SubTotal_Producto;
          $Ss_IGV += $Ss_Total_Producto - $Ss_SubTotal_Producto;
        }

        $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat($('.txt-Qt_Producto', this).val()) * parseFloat($('.txt-Ss_Precio', this).val()))) / 100) / impuesto_producto);
      });
      $( '#txt-subTotal' ).val( $Ss_SubTotal.toFixed(2) );
      $( '#span-subTotal' ).text( $Ss_SubTotal.toFixed(2) );
      
      $( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
      $( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
      
      $( '#txt-impuesto' ).val( $Ss_IGV.toFixed(2) );
      $( '#span-impuesto' ).text( $Ss_IGV.toFixed(2) );
      
      $( '#txt-total' ).val( $Ss_Total.toFixed(2) );
      $( '#span-total' ).text( $Ss_Total.toFixed(2) );
      $( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
    } else if (nu_tipo_impuesto == 2) {//Inafecto
      fila.find(".td-fDescuentoSinImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
      fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
      fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
      fila.find(".txt-Ss_Total_Producto").val((parseFloat(((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)) * impuesto_producto).toFixed(2)).toString().split(". "));

      var $Ss_Inafecto = 0.00;
      var $Ss_Descuento = 0.00;
      var $Ss_Total = 0.00;
      
      $("#table-DetalleProductosOrdenVenta > tbody > tr").each(function(){
        var rows = $(this);
        var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
        var $Ss_SubTotal_Producto = parseFloat($('.txt-Ss_SubTotal_Producto', this).val());
        var $Ss_Descuento_Producto = parseFloat($('.txt-Ss_Descuento', this).val());

        if(isNaN($Ss_Descuento_Producto))
          $Ss_Descuento_Producto = 0;
          
        if (Nu_Tipo_Impuesto == 2)
          $Ss_Inafecto += $Ss_SubTotal_Producto;
        
        $Ss_Descuento += (($Ss_Descuento_Producto * (parseFloat($('.txt-Qt_Producto', this).val()) * parseFloat($('.txt-Ss_Precio', this).val()))) / 100);
        $Ss_Total += parseFloat($('.txt-Ss_Total_Producto', this).val());
      });
      
      $( '#txt-inafecto' ).val( $Ss_Inafecto.toFixed(2) );
      $( '#span-inafecto' ).text( $Ss_Inafecto.toFixed(2) );
      
      $( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
      $( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
      
      $( '#txt-total' ).val( $Ss_Total.toFixed(2) );
      $( '#span-total' ).text( $Ss_Total.toFixed(2) );
      $( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
    } else if (nu_tipo_impuesto == 3) {//Exonerada
      fila.find(".td-fDescuentoSinImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
      fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
      fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
      fila.find(".txt-Ss_Total_Producto").val((parseFloat(((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)) * impuesto_producto).toFixed(2)).toString().split(". "));
      
      var $Ss_Exonerada = 0.00;
      var $Ss_Descuento = 0.00;
      var $Ss_Total = 0.00;
      $("#table-DetalleProductosOrdenVenta > tbody > tr").each(function(){
        var rows = $(this);
        var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
        var $Ss_SubTotal_Producto = parseFloat($('.txt-Ss_SubTotal_Producto', this).val());
        var $Ss_Descuento_Producto = parseFloat($('.txt-Ss_Descuento', this).val());

        if(isNaN($Ss_Descuento_Producto))
          $Ss_Descuento_Producto = 0;
          
        if (Nu_Tipo_Impuesto == 3)
          $Ss_Exonerada += $Ss_SubTotal_Producto;
        
        $Ss_Descuento += (($Ss_Descuento_Producto * (parseFloat($('.txt-Qt_Producto', this).val()) * parseFloat($('.txt-Ss_Precio', this).val()))) / 100);
        $Ss_Total += parseFloat($('.txt-Ss_Total_Producto', this).val());
      });
      
      $( '#txt-exonerada' ).val( $Ss_Exonerada.toFixed(2) );
      $( '#span-exonerada' ).text( $Ss_Exonerada.toFixed(2) );
      
      $( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
      $( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
      
      $( '#txt-total' ).val( $Ss_Total.toFixed(2) );
      $( '#span-total' ).text( $Ss_Total.toFixed(2) );
      $( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
    } else if (nu_tipo_impuesto == 4) {//Gratuita
      fila.find(".td-fDescuentoSinImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
      fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
      fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
      fila.find(".txt-Ss_Total_Producto").val((parseFloat(((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)) * impuesto_producto).toFixed(2)).toString().split(". "));
      
      var $Ss_Gratuita = 0.00;
      var $Ss_Descuento = 0.00;
      var $Ss_Total = 0.00;
      $("#table-DetalleProductosOrdenVenta > tbody > tr").each(function(){
        var rows = $(this);
        var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
        var $Ss_SubTotal_Producto = parseFloat($('.txt-Ss_SubTotal_Producto', this).val());
        var $Ss_Descuento_Producto = parseFloat($('.txt-Ss_Descuento', this).val());

        if(isNaN($Ss_Descuento_Producto))
          $Ss_Descuento_Producto = 0;
          
        if (Nu_Tipo_Impuesto == 4)
          $Ss_Gratuita += $Ss_SubTotal_Producto;
        
        $Ss_Descuento += (($Ss_Descuento_Producto * (parseFloat($('.txt-Qt_Producto', this).val()) * parseFloat($('.txt-Ss_Precio', this).val()))) / 100);
        $Ss_Total += parseFloat($('.txt-Ss_Total_Producto', this).val());
      });
      
      $( '#txt-gratuita' ).val( $Ss_Gratuita.toFixed(2) );
      $( '#span-gratuita' ).text( $Ss_Gratuita.toFixed(2) );
      
      $( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
      $( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
      
      $( '#txt-total' ).val( $Ss_Total.toFixed(2) );
      $( '#span-total' ).text( $Ss_Total.toFixed(2) );
      $( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
    }
    calcularIcbper();
    calcularDescuentoTotal(0);
  } //PRECIO Y CANTIDAD > 0
}

function generarGuia(arrParams) {
  arrParams = JSON.parse(arrParams);

  $('.div-datos_guia_electronica').hide();

  $('#form-generar_guia')[0].reset();
  $('.form-group').removeClass('has-error');
  $('.form-group').removeClass('has-success');
  $('.help-block').empty();

  $('.modal-generar_guia').modal('show');

  $('#txt-Fe_Traslado').val(fDay + '/' + fMonth + '/' + fYear);
  var Fe_Emision = $('#txt-Fe_Traslado').val().split('/');
  $('#txt-Fe_Traslado').datepicker({
    autoclose: true,
    startDate: new Date(Fe_Emision[2], Fe_Emision[1] - 1, Fe_Emision[0]),
    todayHighlight: true
  })

  $('#txt-Fe_Traslado').val(fDay + '/' + fMonth + '/' + fYear);
  
  $('[name="Hidden_ID_Empresa"]').val(arrParams.ID_Empresa);
  $('[name="Hidden_ID_Organizacion"]').val(arrParams.ID_Organizacion);
  $('[name="Hidden_ID_Almacen"]').val(arrParams.ID_Almacen);
  $('[name="Hidden_ID_Moneda"]').val(arrParams.ID_Moneda);
  $('[name="Hidden_ID_Documento_Cabecera"]').val(arrParams.ID_Documento_Cabecera);
  $('[name="Hidden_ID_Entidad"]').val(arrParams.ID_Entidad);

  $('.div-tipoguia').show();

  $('#cbo-transporte').html('<option value="0" selected="selected">- Sin registro -</option>');
  url = base_url + 'HelperController/getDeliveryVentas';
  var arrPost = {};
  $.post(url, arrPost, function (response) {
    if (response.sStatus == 'success') {
      var l = response.arrData.length;
      if (l == 1) {
        $('#cbo-transporte').html('<option value="' + response.arrData[0].ID + '">' + response.arrData[0].Nombre + '</option>');
      } else {
        $('#cbo-transporte').html('<option value="0" selected="selected">- Seleccionar -</option>');
        for (var x = 0; x < l; x++) {
          $('#cbo-transporte').append('<option value="' + response.arrData[x].ID + '" data-id_tipo_documento_identidad="' + response.arrData[x].ID_Tipo_Documento_Identidad + '" data-numero_documento_identidad="' + response.arrData[x].Nu_Documento_Identidad + '">' + response.arrData[x].Nombre + '</option>');
        }
      }
    } else {
      if (response.sMessageSQL !== undefined) {
        console.log(response.sMessageSQL);
      }
    }
  }, 'JSON');

  $('#generar_guia-modal-body-cliente').text('Sin datos');
  $('[name="Txt_Direccion_Entidad-modal"]').val('');
  url = base_url + 'HelperController/getEntidad';
  var arrPost = { ID_Entidad : arrParams.ID_Entidad};
  $.post(url, arrPost, function (response) {
    if (response.sStatus == 'success') {
      $('#generar_guia-modal-body-cliente').text('Cliente: ' + response.arrData[0].Nu_Documento_Identidad + ' ' + response.arrData[0].No_Entidad);
      $('[name="Txt_Direccion_Entidad-modal"]').val(response.arrData[0].Txt_Direccion_Entidad);
    } else {
      if (response.sMessageSQL !== undefined) {
        console.log(response.sMessageSQL);
      }
    }
  }, 'JSON');

  $('[name="Hidden_ID_Lista_Precio_Cabecera"]').val(arrParams.ID_Lista_Precio_Cabecera);
  $('[name="Hidden_Fe_Emision"]').val(arrParams.Fe_Emision);
  $('[name="Hidden_Fe_Emision_Hora"]').val(arrParams.Fe_Emision);
  $('[name="Hidden_Ss_Total"]').val(arrParams.Ss_Total);

  var sTipoDocumento = 'Cotización';
  if (arrParams.ID_Tipo_Documento == 13)
    sTipoDocumento ='Orden de Pago';

  $('#modal-header-generar_guia-title').text(sTipoDocumento + ' - ' + arrParams.ID_Numero_Documento);

  $('#radio-guia_i').prop('checked', true).iCheck('update');
  $('#radio-guia_f').prop('checked', false).iCheck('update');
  $('#radio-guia_e').prop('checked', false).iCheck('update');

  $('#div-addFlete').show();
  $('#radio-flete_si').prop('checked', true).iCheck('update');
  $('#radio-flete_no').prop('checked', false).iCheck('update');

  $('#radio-tipo_transporte_publico').prop('checked', true).iCheck('update');
  $('#radio-tipo_transporte_privado').prop('checked', false).iCheck('update');
  
  $( '#radio-tipo_transporte_publico' ).on('ifChecked', function () {
    $( '#txt-No_Placa' ).attr('placeholder', 'Opcional');
    $( '#txt-No_Licencia' ).attr('placeholder', 'Opcional');
  })

  $( '#radio-tipo_transporte_privado' ).on('ifChecked', function () {
    $( '#txt-No_Placa' ).attr('placeholder', 'Opcional');
    $( '#txt-No_Licencia' ).attr('placeholder', 'Opcional');
    if($('[name="radio-TipoDocumento"]:checked').attr('value') == 8) {//8=guia electronica
      $( '#txt-No_Placa' ).attr('placeholder', 'Obligatorio');
      $( '#txt-No_Licencia' ).attr('placeholder', 'Obligatorio');
    }
  })
  
  $( '#radio-guia_i' ).on('ifChecked', function () {
    $( '#txt-No_Placa' ).attr('placeholder', 'Opcional');
    $( '#txt-No_Licencia' ).attr('placeholder', 'Opcional');
    $('.div-electronico').hide();
  })
  
  $( '#radio-guia_f' ).on('ifChecked', function () {
    $( '#txt-No_Placa' ).attr('placeholder', 'Opcional');
    $( '#txt-No_Licencia' ).attr('placeholder', 'Opcional');
    $('.div-electronico').hide();
  })
  
  $( '#radio-guia_e' ).on('ifChecked', function () {
    $( '#txt-No_Placa' ).attr('placeholder', 'Opcional');
    $( '#txt-No_Licencia' ).attr('placeholder', 'Opcional');
    if($('[name="radio-TipoTransporte"]:checked').attr('value') == '02') {//02=Privado
      $( '#txt-No_Placa' ).attr('placeholder', 'Obligatorio');
      $( '#txt-No_Licencia' ).attr('placeholder', 'Obligatorio');
    }
    $('.div-electronico').show();
  })

  url = base_url + 'HelperController/getValoresTablaDato';
  var arrParams = {
    sTipoData : 'Ubigeo_INEI',
  }
  $.post( url, arrParams, function( response ){
    if ( response.sStatus == 'success' ) {
      var iTotalRegistros = response.arrData.length, response=response.arrData;
      $( '#cbo-ubigeo_inei-modal' ).html( '<option value="" selected="selected">- Seleccionar -</option>');
      for (var i = 0; i < iTotalRegistros; i++) {
        selected = '';
        if(response[i].ID_Tabla_Dato == 1444)
          selected = 'selected="selected"';
        $( '#cbo-ubigeo_inei-modal' ).append( '<option value="' + response[i].ID_Tabla_Dato + '" ' + selected + '>' + response[i].Nu_Valor + ': ' + response[i].No_Descripcion + '</option>' );
      }
    } else {
      $( '#cbo-ubigeo_inei-modal' ).html( '<option value="" selected="selected">- Vacío -</option>');
      console.log( response );
    }
  }, 'JSON');

  $('.div-electronico').hide();
}