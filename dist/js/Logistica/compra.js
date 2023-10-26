var url;
var table_compra;
var considerar_igv;
var nu_enlace;
var value_importes_cero = 0.00;
var texto_importes_cero = '0.00';
var arrImpuestosProducto = '{ "arrImpuesto" : [';
var arrImpuestosProductoDetalle;
var accion = '';
var bEstadoValidacion;
var iTipoImpuesto;

$('.date-picker-invoice').val(fDay + '/' + fMonth + '/' + fYear);
$('#txt-Filtro_Fe_Inicio').val(fDay + '/' + fMonth + '/' + fYear);

function agregarCompra(){
  accion = 'add_factura_compra';
  $( '#modal-loader' ).modal('show');

  $(".clearable__clear").toggle(false);

  $( '.div-Listar' ).hide();
  $( '.div-AgregarEditar' ).show();
  
	$( '#txt-EID_Empresa' ).focus();
	
  $( '#form-Compra' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '.title_Compra' ).text('Nuevo Compra');
  
  $('.div-tipo_compras').show();
  $('.div-compra_rapida').hide();
  
  $('#btn-configurar_compra_normal').data('mostrar_configurar_compra_normal', 1);
  $('#btn-configurar_compra_normal').removeClass('btn-default');
  $('#btn-configurar_compra_normal').addClass('btn-dark color-white');
  
  $('#btn-configurar_compra_rapida').data('mostrar_configurar_compra_rapida', 0);
  $('#btn-configurar_compra_rapida').removeClass('btn-dark color-white');
  $('#btn-configurar_compra_rapida').addClass('btn-default');
  
  $('#btn-configurar_compra_normal').click(function () {
    $('#btn-configurar_compra_normal').data('mostrar_configurar_compra_normal', 1);
    $('#btn-configurar_compra_normal').removeClass('btn-default');
    $('#btn-configurar_compra_normal').addClass('btn-dark color-white');
    
    $('#btn-configurar_compra_rapida').data('mostrar_configurar_compra_rapida', 0);
    $('#btn-configurar_compra_rapida').removeClass('btn-dark color-white');
    $('#btn-configurar_compra_rapida').addClass('btn-default');

    $('.div-compra_rapida').hide();
  })

  $('#btn-configurar_compra_rapida').click(function () {
    $('#btn-configurar_compra_rapida').data('mostrar_configurar_compra_rapida', 1);
    $('#btn-configurar_compra_rapida').removeClass('btn-default');
    $('#btn-configurar_compra_rapida').addClass('btn-dark color-white');

    $('#btn-configurar_compra_normal').data('mostrar_configurar_compra_rapida', 0);
    $('#btn-configurar_compra_normal').removeClass('btn-dark color-white');
    $('#btn-configurar_compra_normal').addClass('btn-default');

    //Colocar valores por defecto
		$('#cbo-TiposDocumento').val('2');
    $('#txt-ID_Serie_Documento').val($('#hidden-ID_Serie_Documento-Registro').val());
    $('#txt-ID_Numero_Documento').val($('#hidden-ID_Numero_Documento-Registro').val());
    
    if($('#hidden-ID_Entidad-Registro').val() != '') {
      $( '#radio-proveedor_existente' ).prop('checked', true).iCheck('update');
      $( '#radio-proveedor_nuevo' ).prop('checked', false).iCheck('update');
      $('#txt-AID').val($('#hidden-ID_Entidad-Registro').val());
      $('#txt-ACodigo').val($('#hidden-Nu_Documento_Identidad-Registro').val());
      $('#txt-ANombre').val($('#hidden-No_Entidad-Registro').val());
    } else {
      $( '#radio-proveedor_existente' ).prop('checked', false).iCheck('update');
      $( '#radio-proveedor_nuevo' ).prop('checked', true).iCheck('update');

      $('#txt-AID').val('');
      $('#txt-ACodigo').val('');
      $('#txt-ANombre').val('');
    }

    $('.div-compra_rapida').show();
  })
  
  $('.div-adicionales_ov_garantia_glosa').css("display", "none");
  $('#btn-adicionales_ov_garantia_glosa').data('ver_adicionales_ov_garantia_glosa', 0);

  $('.panel_body_total_todo').css("display", "none");
  $('#btn-ver_total_todo').data('ver_total_todo', 0);
  $('#btn-ver_total_todo').text('VER / DESCUENTO');

  $('[name="EID_Empresa"]').val('');
  $('[name="EID_Documento_Cabecera"]').val('');

  $('#btn-save').show();
  
  $( '.date-picker-invoice' ).val(fDay + '/' + fMonth + '/' + fYear);
  
  $( '#radio-InactiveDetraccion' ).prop('checked', true);
  $( '#radio-ActiveDetraccion' ).prop('checked', false);
    
  $( '#cbo-TiposDocumentoModificar' ).html('');
	
  $( '#txt-ID_Documento_Guardado' ).val(0);
  $( '.div-DocumentoModificar' ).removeClass('panel-warning panel-danger panel-success');
  $( '.div-mensaje_verificarExisteDocumento' ).removeClass('text-danger text-success');
  $( '.div-mensaje_verificarExisteDocumento' ).text('');
  $( '.div-DocumentoModificar' ).addClass('panel-default');
  
  $( '#cbo-OrganizacionesVenta' ).prop('disabled', false);
  
  $( '#radio-proveedor_existente' ).prop('checked', true).iCheck('update');
  $( '#radio-proveedor_nuevo' ).prop('checked', false).iCheck('update');
  $( '.div-proveedor_existente' ).show();
  $( '.div-proveedor_nuevo' ).hide();
  
  $( '.div-MediosPago' ).hide();
  
  $( '.div-Periodo' ).hide();
  
  $( '#div-addDetraccion').hide();
  
	$( '#table-DetalleProductos tbody' ).empty();
	
	$( '#panel-DetalleProductos' ).removeClass('panel-danger');
	$( '#panel-DetalleProductos' ).addClass('panel-default');
	
	$( '#txt-subTotal' ).val( value_importes_cero );
	$( '#span-subTotal' ).text( texto_importes_cero );
	
	$( '#txt-exonerada' ).val( value_importes_cero );
	$( '#span-exonerada' ).text( texto_importes_cero );
	
	$( '#txt-impuesto' ).val( value_importes_cero );
	$( '#span-impuesto' ).text( texto_importes_cero );
	
	$( '#txt-inafecto' ).val( value_importes_cero );
	$( '#span-inafecto' ).text( texto_importes_cero );
	
	$( '#txt-gratuita' ).val( value_importes_cero );
	$( '#span-gratuita' ).text( texto_importes_cero );
	
	$( '#txt-descuento' ).val( value_importes_cero );
	$( '#span-descuento' ).text( texto_importes_cero );
	
	$( '#txt-total' ).val( value_importes_cero );
	$( '#span-total' ).text( texto_importes_cero );
	
	$( '#span-total_importe' ).text( texto_importes_cero );

  $( '.span-signo' ).text( 'S/' );

  $( '#btn-save' ).attr('disabled', false);
  
  considerar_igv=0;
  
  url = base_url + 'HelperController/getTiposDocumentos';
  $.post( url, {Nu_Tipo_Filtro : 4}, function( response ){//2 = Compra
    $( '#cbo-TiposDocumento' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++) {
      if(response[i]['No_Tipo_Documento_Breve']=='Nota de Venta')
        response[i]['No_Tipo_Documento_Breve'] = 'Nota de Compra';
      $( '#cbo-TiposDocumento' ).append( '<option value="' + response[i]['ID_Tipo_Documento'] + '" data-nu_impuesto="' + response[i]['Nu_Impuesto'] + '" data-nu_enlace="' + response[i]['Nu_Enlace'] + '">' + response[i]['No_Tipo_Documento_Breve'] + '</option>' );
    }
  }, 'JSON');

  url = base_url + 'HelperController/getTiposDocumentoIdentidad';
  $.post( url , function( response ){
    $( '#cbo-TiposDocumentoIdentidadProveedor' ).html('');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-TiposDocumentoIdentidadProveedor' ).append( '<option value="' + response[i]['ID_Tipo_Documento_Identidad'] + '" data-nu_cantidad_caracteres="' + response[i]['Nu_Cantidad_Caracteres'] + '">' + response[i]['No_Tipo_Documento_Identidad_Breve'] + '</option>' );
  }, 'JSON');
  
	$( '.div-DocumentoModificar' ).hide();
	$( '#cbo-TiposDocumento' ).change(function(){
	  if( $(this).val() == 10 ) { //Recibos de agua
	    $("div.rubro select").val("3");
	    iTipoImpuesto = $( '#cbo-Rubros' ).find(':selected').data('nu_tipo_impuesto');
	    if (iTipoImpuesto == 2){//INAFECTO
	      $( '.div-DescargarInventario' ).hide();
	    }
	    $( '.div-MediosPago' ).show();
	  } else if ( $(this).val() == 11 ) {
	    $("div.rubro select").val("2");
	    iTipoImpuesto = $( '#cbo-Rubros' ).find(':selected').data('nu_tipo_impuesto');
	    if (iTipoImpuesto == 2){//INAFECTO
	      $( '.div-DescargarInventario' ).hide();
	    }
	    $( '.div-MediosPago' ).hide();
	  } else {
	    $("div.rubro select").val("1");
	    iTipoImpuesto = $( '#cbo-Rubros' ).find(':selected').data('nu_tipo_impuesto');
	    
      $( '.div-DescargarInventario' ).show();
      $( '.div-MediosPago' ).hide();
	  }
	  
    $( '.div-DocumentoModificar' ).hide();
	  if ( $(this).val() > 0 ) {
	    considerar_igv = $(this).find(':selected').data('nu_impuesto');
	    nu_enlace = $(this).find(':selected').data('nu_enlace');
	    if (nu_enlace == 1) {//Validar N/C y N/D
	      $( '.div-DocumentoModificar' ).show();

        url = base_url + 'HelperController/getTiposDocumentosModificar';
        $.post( url , function( response ){
          $( '#cbo-TiposDocumentoModificar' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
          for (var i = 0; i < response.length; i++)
            $( '#cbo-TiposDocumentoModificar' ).append( '<option value="' + response[i]['ID_Tipo_Documento'] + '">' + response[i]['No_Tipo_Documento_Breve'] + '</option>' );
        }, 'JSON');
	    }

      var $Ss_Descuento = parseFloat($('#txt-Ss_Descuento').val());
      var $Ss_SubTotal = 0.00;
      var $Ss_Inafecto = 0.00;
      var $Ss_Exonerada = 0.00;
      var $Ss_Gratuita = 0.00;
      var $Ss_IGV = 0.00;
      var $Ss_Total = 0.00;
      var iCantDescuento = 0;
      var globalImpuesto = 0;
      var $Ss_Descuento_p = 0;
      $("#table-DetalleProductos > tbody > tr").each(function(){
        var rows = $(this);
        var fImpuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
        var iGrupoImpuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
        var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
        var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());
        var $Ss_Total_Producto = parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());

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

        $Ss_Descuento_p += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / fImpuesto))) / 100);
      });
      
      $Ss_IGV = ($Ss_SubTotal * globalImpuesto) - $Ss_SubTotal;
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
          $Ss_Gratuita = ($Ss_Gratuita - $Ss_Descuento_Exonerada);
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

      $('#span-total_importe').text($Ss_Total.toFixed(2));
	  }
	})
	
  $( '#panel-DetalleProductos' ).show();
  
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

  $('.div-modal_datos_tarjeta_credito').hide();

  $( '#txt-Fe_Emision' ).datepicker({}).on('changeDate', function (selected) {
    var minDate = new Date(selected.date.valueOf());
    $( '#txt-Fe_Vencimiento' ).datepicker('setStartDate', minDate);
    $( '#txt-Fe_Periodo' ).datepicker('setStartDate', minDate);
  });

  var Fe_Emision = $( '#txt-Fe_Emision' ).val().split('/');
  $( '#txt-Fe_Vencimiento' ).datepicker({
    autoclose : true,
    startDate : new Date(Fe_Emision[2], Fe_Emision[1] - 1, Fe_Emision[0]),
    todayHighlight: true
  })
  
  $( '#txt-Fe_Vencimiento' ).val($( '#txt-Fe_Emision' ).val());
  
  $( '#txt-Fe_Periodo' ).datepicker({
    autoclose : true,
    startDate : new Date(Fe_Emision[2], Fe_Emision[1] - 1, Fe_Emision[0]),
    todayHighlight: true
  })
  $( '#txt-Fe_Periodo' ).val($( '#txt-Fe_Emision' ).val());
  
  $( '#cbo-Periodo' ).html( '<option value="0">No</option>' );
  $( '#cbo-Periodo' ).append( '<option value="1">Si</option>' );

  $( '#cbo-descargar_stock' ).html( '<option value="1">Si</option>' );
  $( '#cbo-descargar_stock' ).append( '<option value="0">No</option>' );
  $( '#cbo-almacen' ).show();

  var arrParams = {
    ID_Almacen: $('#cbo-almacen').val(),
  };
  getListaPrecios(arrParams);

  $( '#table-DetalleProductos' ).hide();
  
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

function verCompra(ID, Nu_Documento_Identidad){
  accion = 'upd_factura_compra';
  $( '#modal-loader' ).modal('show');
  
  $( '.div-Listar' ).hide();
  
	$( '#txt-EID_Empresa' ).focus();
	
  $( '#form-Compra' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $('.div-tipo_compras').hide();

  $('.div-adicionales_ov_garantia_glosa').css("display", "none");
  $('#btn-adicionales_ov_garantia_glosa').data('ver_adicionales_ov_garantia_glosa', 0);

  $('.panel_body_total_todo').css("display", "none");
  $('#btn-ver_total_todo').data('ver_total_todo', 0);
  $('#btn-ver_total_todo').text('VER / DESCUENTO');

  $( '#radio-InactiveDetraccion' ).prop('checked', true);
  $( '#radio-ActiveDetraccion' ).prop('checked', false);
  
	$( '.div-DocumentoModificar' ).hide();
 
  $( '#txt-ID_Documento_Guardado' ).val(1);
  $( '.div-DocumentoModificar' ).removeClass('panel-warning panel-danger panel-success');
  $( '.div-mensaje_verificarExisteDocumento' ).removeClass('text-danger text-success');
  $( '.div-mensaje_verificarExisteDocumento' ).text('');
  $( '.div-DocumentoModificar' ).addClass('panel-default');

  $( '#cbo-OrganizacionesVenta' ).prop('disabled', true);
 
  $( '#radio-proveedor_existente' ).prop('checked', true).iCheck('update');
  $( '#radio-proveedor_nuevo' ).prop('checked', false).iCheck('update');
  $( '.div-proveedor_existente' ).show();
  $( '.div-proveedor_nuevo' ).hide();
  
  url = base_url + 'HelperController/getTiposDocumentoIdentidad';
  $.post( url , function( response ){
    $( '#cbo-TiposDocumentoIdentidadProveedor' ).html('');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-TiposDocumentoIdentidadProveedor' ).append( '<option value="' + response[i]['ID_Tipo_Documento_Identidad'] + '" data-nu_cantidad_caracteres="' + response[i]['Nu_Cantidad_Caracteres'] + '">' + response[i]['No_Tipo_Documento_Identidad_Breve'] + '</option>' );
  }, 'JSON');
  
  $( '#panel-DetalleProductos' ).removeClass('panel-danger');
  $( '#panel-DetalleProductos' ).addClass('panel-default');
  
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

  $( '#btn-save' ).attr('disabled', false);

  considerar_igv=0;
  
	$( '#cbo-TiposDocumento' ).change(function(){
	  if( $(this).val() == 10 ) { //Recibos de agua
	    $("div.rubro select").val("3");
	    iTipoImpuesto = $( '#cbo-Rubros' ).find(':selected').data('nu_tipo_impuesto');
	    if (iTipoImpuesto == 2){//INAFECTO
	      $( '.div-DescargarInventario' ).hide();
	    }
	    $( '.div-MediosPago' ).show();
	  } else if ( $(this).val() == 11 ) {
	    $("div.rubro select").val("2");
	    iTipoImpuesto = $( '#cbo-Rubros' ).find(':selected').data('nu_tipo_impuesto');
	    if (iTipoImpuesto == 2){//INAFECTO
	      $( '.div-DescargarInventario' ).hide();
	    }
	    $( '.div-MediosPago' ).hide();
	  } else {
	    $("div.rubro select").val("1");
	    iTipoImpuesto = $( '#cbo-Rubros' ).find(':selected').data('nu_tipo_impuesto');
	    
      $( '.div-DescargarInventario' ).show();
      $( '.div-MediosPago' ).hide();
	  }
	  
    $( '.div-DocumentoModificar' ).hide();
	  if ( $(this).val() > 0 ) {
	    considerar_igv = $(this).find(':selected').data('nu_impuesto');
	    nu_enlace = $(this).find(':selected').data('nu_enlace');
	    if (nu_enlace == 1) {//Validar N/C y N/D
	      $( '.div-DocumentoModificar' ).show();

        url = base_url + 'HelperController/getTiposDocumentosModificar';
        $.post( url , function( response ){
          $( '#cbo-TiposDocumentoModificar' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
          for (var i = 0; i < response.length; i++)
            $( '#cbo-TiposDocumentoModificar' ).append( '<option value="' + response[i]['ID_Tipo_Documento'] + '">' + response[i]['No_Tipo_Documento_Breve'] + '</option>' );
        }, 'JSON');
	    }

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
      $("#table-DetalleProductos > tbody > tr").each(function(){
        var rows = $(this);
        var fImpuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
        var iGrupoImpuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
        var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_Total_Producto', this).val() / fImpuesto);
        var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());
        var $Ss_Total_Producto = parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());

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
          
        $Ss_Descuento_p += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / fImpuesto))) / 100);
      });
      
      if ($Ss_Descuento > 0.00) {
        var $Ss_Descuento_Gravadas = 0, $Ss_Descuento_Inafecto = 0, $Ss_Descuento_Exonerada = 0, $Ss_Descuento_Gratuita;
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
	})
	
  url = base_url + 'Logistica/CompraController/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $( '.div-AgregarEditar' ).show();
      
      $( '.title_Compra' ).text('Modifcar Compra');
      
      $('[name="EID_Empresa"]').val(response.arrEdit[0].ID_Empresa);
      $('[name="EID_Documento_Cabecera"]').val(response.arrEdit[0].ID_Documento_Cabecera);

      $('[name="ID_Documento_Cabecera_Orden"]').val(response.arrEdit[0].ID_Documento_Cabecera_Enlace);

      nu_enlace = response.arrEdit[0].Nu_Enlace;
      $('[name="ID_Documento_Cabecera_Orden"]').val(response.arrEdit[0].ID_Documento_Cabecera_Enlace);
      if (response.arrEdit[0].ID_Documento_Cabecera_Enlace != '' && response.arrEdit[0].ID_Documento_Cabecera_Enlace != null) {
        nu_enlace = 1;

        $('#txt-ID_Documento_Guardado').val(response.arrEdit[0].ID_Documento_Cabecera_Enlace);
      }

      //Datos Proveedor
      $('[name="AID"]').val(response.arrEdit[0].ID_Entidad);
      $('[name="ANombre"]').val(response.arrEdit[0].No_Entidad);
      $('[name="ACodigo"]').val(response.arrEdit[0].Nu_Documento_Identidad);
      $('[name="Txt_Direccion_Entidad"]').val(response.arrEdit[0].Txt_Direccion_Entidad);
      
      //Datos Documento
      considerar_igv = response.arrEdit[0].Nu_Impuesto;
          
      url = base_url + 'HelperController/getTiposDocumentos';
      $.post( url, {Nu_Tipo_Filtro : 4}, function( responseTiposDocumento ){//2 = Compra
        $( '#cbo-TiposDocumento' ).html('');
        for (var i = 0; i < responseTiposDocumento.length; i++){
          selected = '';
          if(response.arrEdit[0].ID_Tipo_Documento == responseTiposDocumento[i]['ID_Tipo_Documento'])
            selected = 'selected="selected"';
          $( '#cbo-TiposDocumento' ).append( '<option value="' + responseTiposDocumento[i]['ID_Tipo_Documento'] + '" data-nu_impuesto="' + responseTiposDocumento[i]['Nu_Impuesto'] + '" data-nu_enlace="' + responseTiposDocumento[i]['Nu_Enlace'] + '" ' + selected + '>' + responseTiposDocumento[i]['No_Tipo_Documento_Breve'] + '</option>' );
        }
      }, 'JSON');
      
      $('[name="ID_Serie_Documento"]').val(response.arrEdit[0].ID_Serie_Documento);
      $('[name="ID_Numero_Documento"]').val(response.arrEdit[0].ID_Numero_Documento);
      console.log('1');
      $('[name="Fe_Emision"]').val(ParseDateString(response.arrEdit[0].Fe_Emision, 6, '-'));
      
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

      $('.div-modal_datos_tarjeta_credito').hide();
      $('#tel-nu_referencia').val('');
      $('#tel-nu_ultimo_4_digitos_tarjeta').val('');
      if (response.arrEdit[0].ID_Tipo_Medio_Pago > 0) {
        $('.div-modal_datos_tarjeta_credito').show();
        url = base_url + 'HelperController/getTiposTarjetaCredito';
        $.post(url, { ID_Medio_Pago: response.arrEdit[0].ID_Medio_Pago }, function (responseTipoTarjetCredito) {
          $('#cbo-tarjeta_credito').html('');
          for (var i = 0; i < responseTipoTarjetCredito.length; i++) {
            selected = '';
            if (response.arrEdit[0].ID_Tipo_Medio_Pago == responseTipoTarjetCredito[i].ID_Tipo_Medio_Pago)
              selected = 'selected="selected"';
            $('#cbo-tarjeta_credito').append('<option value="' + responseTipoTarjetCredito[i].ID_Tipo_Medio_Pago + '" ' + selected + '>' + responseTipoTarjetCredito[i].No_Tipo_Medio_Pago + '</option>');
          }
        }, 'JSON');

        $('#tel-nu_referencia').val(response.arrEdit[0].Nu_Tarjeta);
        $('#tel-nu_ultimo_4_digitos_tarjeta').val(response.arrEdit[0].Nu_Transaccion);
      }

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
  	  
      $('#btn-save').show();
      $('.div-MediosPago').hide();
  	  if ( response.arrEdit[0].Nu_Tipo == 1) {// Si es Crédito
        $('.div-MediosPago').show();
        if (response.arrEdit[0].Ss_Total != response.arrEdit[0].Ss_Total_Saldo)
          $('#btn-save').hide();
      }
  	  
      $( '#txt-Fe_Emision' ).datepicker({}).on('changeDate', function (selected) {
        var minDate = new Date(selected.date.valueOf());
        $( '#txt-Fe_Vencimiento' ).datepicker('setStartDate', minDate);
        $( '#txt-Fe_Periodo' ).datepicker('setStartDate', minDate);
      });
    
      var $Nu_Contabilizar_Otro_Periodo=0;
      if (response.arrEdit[0].Fe_Periodo != response.arrEdit[0].Fe_Emision){
        $( '.div-Periodo' ).show();
        $Nu_Contabilizar_Otro_Periodo=1;
      }
      
      $( '#cbo-Periodo' ).html('');
      for (var i = 0; i < 2; i++){
        selected = '';
        if($Nu_Contabilizar_Otro_Periodo == i)
          selected = 'selected="selected"';
        $( '#cbo-Periodo' ).append( '<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'No' : 'Si') + '</option>' );
      }
      
      var Fe_Emision = response.arrEdit[0].Fe_Emision.split('-');
      $( '#txt-Fe_Vencimiento' ).datepicker({
        autoclose : true,
        startDate : new Date(parseInt(Fe_Emision[0]), parseInt(Fe_Emision[1]) - 1, parseInt(Fe_Emision[2])),
        todayHighlight: true
      })
      
      $( '#txt-Fe_Periodo' ).datepicker({
        autoclose : true,
        startDate : new Date(parseInt(Fe_Emision[0]), parseInt(Fe_Emision[1]) - 1, parseInt(Fe_Emision[2])),
        todayHighlight: true
      })
      
      $( '#txt-Fe_Vencimiento' ).datepicker('setStartDate', new Date(Fe_Emision[0] + "/" + Fe_Emision[1] + "/" + Fe_Emision[2]) );
      $( '#txt-Fe_Periodo' ).datepicker('setStartDate', new Date(Fe_Emision[0] + "/" + Fe_Emision[1] + "/" + Fe_Emision[2]) );
      console.log('2');
      $( '#txt-Fe_Vencimiento' ).val(ParseDateString(response.arrEdit[0].Fe_Vencimiento, 6, '-'));
      console.log('3');
      $( '#txt-Fe_Periodo' ).val(ParseDateString(response.arrEdit[0].Fe_Periodo, 6, '-'));
      
      //Validar N/C y N/D
      $('#cbo-TiposDocumentoModificar').html('<option value="" selected="selected">- Seleccionar -</option>');
      $('#cbo-SeriesDocumentoModificar').html('<option value="" selected="selected">- Seleccionar -</option>');
      $('#txt-ID_Numero_Documento_Modificar').val('');
      $('#cbo-MotivoReferenciaModificar').html('<option value="" selected="selected">- Seleccionar -</option>');
	    if (response.arrEdit[0].Nu_Enlace == 1) {
        $( '.div-DocumentoModificar' ).show();
  
        url = base_url + 'HelperController/getTiposDocumentosModificar';
        $.post( url , function( responseTiposDocumentoModificar ){
          $( '#cbo-TiposDocumentoModificar' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
          for (var i = 0; i < responseTiposDocumentoModificar.length; i++){
            selected = '';
            if(response.arrEdit[0].ID_Tipo_Documento_Modificar == responseTiposDocumentoModificar[i]['ID_Tipo_Documento'])
              selected = 'selected="selected"';
            $( '#cbo-TiposDocumentoModificar' ).append( '<option value="' + responseTiposDocumentoModificar[i]['ID_Tipo_Documento'] + '" ' + selected + '>' + responseTiposDocumentoModificar[i]['No_Tipo_Documento_Breve'] + '</option>' );
          }
        }, 'JSON');
        
        $( '#txt-ID_Serie_Documento_Modificar' ).val(response.arrEdit[0].ID_Serie_Documento_Modificar);
        $( '#txt-ID_Numero_Documento_Modificar' ).val(response.arrEdit[0].ID_Numero_Documento_Modificar);
	    }
      
      if ( response.arrEdit[0].Nu_Descargar_Inventario == 1 ) {
        $( '#cbo-descargar_stock' ).html( '<option value="1" selected>Si</option>' );
        $( '#cbo-descargar_stock' ).append( '<option value="0">No</option>' );
      } else {
        $( '#cbo-descargar_stock' ).html( '<option value="1">Si</option>' );
        $( '#cbo-descargar_stock' ).append( '<option value="0" selected>No</option>' );
      }

	    $( '#cbo-lista_precios' ).html('');
      url = base_url + 'HelperController/getListaPrecio';
      $.post(url, { Nu_Tipo_Lista_Precio: $('[name="Nu_Tipo_Lista_Precio"]').val(), ID_Organizacion: response.arrEdit[0].ID_Organizacion, ID_Almacen: response.arrEdit[0].ID_Almacen }, function (responseLista) {
        $('#cbo-lista_precios').html('<option value="0">- Seleccionar -</option>');
        for (var i = 0; i < responseLista.length; i++) {
          selected = '';
          if(response.arrEdit[0].ID_Lista_Precio_Cabecera == responseLista[i].ID_Lista_Precio_Cabecera)
            selected = 'selected="selected"';
          $( '#cbo-lista_precios' ).append( '<option value="' + responseLista[i].ID_Lista_Precio_Cabecera + '" ' + selected + '>' + responseLista[i].No_Lista_Precio + '</option>' );
        }
      }, 'JSON');
      
      $('[name="Txt_Glosa"]').val( response.arrEdit[0].Txt_Glosa );
      
      $( '#div-addDetraccion').hide();
      if (response.arrEdit[0].Nu_Detraccion != '' && response.arrEdit[0].Nu_Detraccion != null) {
        console.log('4');
        $( '#radio-InactiveDetraccion' ).prop('checked', false);
        $( '#radio-ActiveDetraccion' ).prop('checked', true);
        $( '[name="Fe_Detraccion"]' ).val(ParseDateString(response.arrEdit[0].Fe_Detraccion, 6, '-'));
        $( '[name="Nu_Detraccion"]' ).val( response.arrEdit[0].Nu_Detraccion );
        $( '#div-addDetraccion').show();
      }
	    
      //Detalle
      $( '#table-DetalleProductos' ).show();
      $( '#table-DetalleProductos tbody' ).empty();
      
      var table_detalle_producto = '';
      var _ID_Producto = '';
      var $Ss_SubTotal_Producto = 0.00;
      var $Ss_IGV_Producto = 0.00;
      var $Ss_Descuento_Producto = 0.00;
      var $Ss_Total_Producto = 0.00;
      var $Ss_Gravada = 0.00;
      var $Ss_Inafecto = 0.00;
      var $Ss_Exonerada = 0.00;
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
      
      for (var i = 0; i < response.arrEdit.length; i++) {
        if (_ID_Producto != response.arrEdit[i].ID_Producto) {
          _ID_Producto = response.arrEdit[i].ID_Producto;
          option_impuesto_producto = '';
        }
        
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
        
	      for (var x = 0; x < iTotalRegistrosImpuestos; x++){
	        selected = '';
	        if (response.arrImpuesto[x].ID_Impuesto_Cruce_Documento == response.arrEdit[i].ID_Impuesto_Cruce_Documento)
	          selected = 'selected="selected"';
          option_impuesto_producto += "<option value='" + response.arrImpuesto[x].ID_Impuesto_Cruce_Documento + "' data-nu_tipo_impuesto='" + response.arrImpuesto[x].Nu_Tipo_Impuesto + "' data-impuesto_producto='" + response.arrImpuesto[x].Ss_Impuesto + "' " + selected + ">" + response.arrImpuesto[x].No_Impuesto + "</option>";
	      }
	      
        var sVarianteMultipleTmp='';
        sVarianteMultipleTmp += ($('#hidden-iTipoRubroEmpresa').val()==6 ? (response.arrEdit[i].No_Variante_1 !== undefined && response.arrEdit[i].No_Variante_1 !== null && response.arrEdit[i].No_Variante_1 !== '' ? '<br>' + response.arrEdit[i].No_Variante_1 + ': ' + response.arrEdit[i].No_Valor_Variante_1 : '') : '');
        sVarianteMultipleTmp += ($('#hidden-iTipoRubroEmpresa').val()==6 ? (response.arrEdit[i].No_Variante_2 !== undefined && response.arrEdit[i].No_Variante_2 !== null && response.arrEdit[i].No_Variante_2 !== '' ? '<br>' + response.arrEdit[i].No_Variante_2 + ': ' + response.arrEdit[i].No_Valor_Variante_2 : '') : '');
        sVarianteMultipleTmp += ($('#hidden-iTipoRubroEmpresa').val()==6 ? (response.arrEdit[i].No_Variante_3 !== undefined && response.arrEdit[i].No_Variante_3 !== null && response.arrEdit[i].No_Variante_3 !== '' ? '<br>' + response.arrEdit[i].No_Variante_3 + ': ' + response.arrEdit[i].No_Valor_Variante_3 : '') : '');
        table_detalle_producto += 
        "<tr id='tr_detalle_producto" + response.arrEdit[i].ID_Producto + "'>"
          +"<td style='display:none;' class='text-left td-iIdItem'>" + response.arrEdit[i].ID_Producto + "</td>"
          +"<td class='text-right'><input type='text' inputmode='decimal' class='pos-input txt-Qt_Producto form-control input-decimal input-size_cantidad hotkey-focus_item' value='" + Math.round10(response.arrEdit[i].Qt_Producto, -3) + "' autocomplete='off'></td>"
          +"<td class='text-left'>"
            + '<span style="font-size: 13px;font-weight:bold;">' + response.arrEdit[i].No_Producto + '</span>'
            + sVarianteMultipleTmp
            + (response.arrEdit[i].No_Unidad_Medida !== undefined && response.arrEdit[i].No_Unidad_Medida !== null && response.arrEdit[i].No_Unidad_Medida != '' ? ' <br><span style="font-size: 10px;font-weight:normal;">[' + response.arrEdit[i].No_Unidad_Medida + ']</span> ' : '')
          +"</td>"
          +"<td style='display:none;' class='text-right'><input type='text' inputmode='decimal' class='pos-input txt-fValorUnitario form-control input-decimal input-size_importe' value='" + Math.round10(parseFloat(response.arrEdit[i].Ss_Precio / response.arrEdit[i].Ss_Impuesto).toFixed(3), -3) + "' autocomplete='off'></td>"
          +"<td class='text-right'><input type='text' inputmode='decimal' class='pos-input txt-Ss_Precio form-control input-decimal input-size_importe' value='" + Math.round10(response.arrEdit[i].Ss_Precio, -3) + "' autocomplete='off'></td>"
          +"<td class='text-right'>"
            +"<select class='cbo-ImpuestosProducto form-control required' style='width: 100%;'>"
              +option_impuesto_producto
            +"</select>"
          +"</td>"
          +"<td style='display:none;' class='text-right'><input type='text' inputmode='decimal' class='pos-input txt-Ss_SubTotal_Producto form-control' value='" + response.arrEdit[i].Ss_SubTotal_Producto + "' autocomplete='off' disabled></td>"
          +"<td class='text-right'><input type='text' inputmode='decimal' class='pos-input txt-Ss_Descuento form-control input-decimal input-size_otros' value='" + (response.arrEdit[i].Po_Descuento_Impuesto_Producto == 0.00 ? '' : response.arrEdit[i].Po_Descuento_Impuesto_Producto) + "' autocomplete='off'></td>"
          +"<td class='text-right'><input type='text' inputmode='decimal' class='pos-input txt-Ss_Total_Producto form-control input-decimal input-size_importe' value='" + response.arrEdit[i].Ss_Total_Producto + "' autocomplete='off'></td>"
          +"<td class='text-right'><input type='text' inputmode='numeric' class='pos-input txt-Nu_Lote_Vencimiento form-control input-codigo_barra' placeholder='Opcional' value='" + (response.arrEdit[i].Nu_Lote_Vencimiento != null ? response.arrEdit[i].Nu_Lote_Vencimiento : '') + "' autocomplete='off'></td>"
          +"<td class='text-right'><input type='text' class='pos-input txt-Fe_Lote_Vencimiento form-control date-picker-invoice' placeholder='Opcional' value='" + ((response.arrEdit[i].Fe_Lote_Vencimiento !='' && response.arrEdit[i].Fe_Lote_Vencimiento != null) ? ParseDateString(response.arrEdit[i].Fe_Lote_Vencimiento, 6, '-') : '') + "' autocomplete='off'></td>"
          +"<td style='display:none;' class='text-right td-fDescuentoSinImpuestosItem'>" + (response.arrEdit[i].Ss_Descuento_Producto == 0.00 ? '' : response.arrEdit[i].Ss_Descuento_Producto) + "</td>"
          +"<td style='display:none;' class='text-right td-fDescuentoImpuestosItem'>" + (response.arrEdit[i].Ss_Descuento_Impuesto_Producto == 0.00 ? '' : response.arrEdit[i].Ss_Descuento_Impuesto_Producto) + "</td>"
          +"<td class='text-center'><button type='button' id='btn-deleteProducto' class='btn btn-sm btn-link' alt='Eliminar' title='Eliminar'><i class='fa fa-trash-o fa-2x' aria-hidden='true'> </i></button></td>"
        +"</tr>";
      }
      
		  $( '#table-DetalleProductos > tbody' ).append(table_detalle_producto);

      $(document).on('input.hotkey-focus_item').bind('keydown', 'F4', function () {
        $('#txt-No_Producto').focus();
      });
    
      $( '.txt-Fe_Lote_Vencimiento' ).datepicker({
        autoclose : true,
        startDate : new Date(fYear, fToday.getMonth(), fDay),
        todayHighlight: true
      })

      $( '#txt-subTotal' ).val( $Ss_Gravada.toFixed(2) );
      $( '#span-subTotal' ).text( $Ss_Gravada.toFixed(2) );
      
      $( '#txt-inafecto' ).val( $Ss_Inafecto.toFixed(2) );
      $( '#span-inafecto' ).text( $Ss_Inafecto.toFixed(2) );
      
      $( '#txt-exonerada' ).val( $Ss_Exonerada.toFixed(2) );
      $( '#span-exonerada' ).text( $Ss_Exonerada.toFixed(2) );
      
      $( '#txt-gratuita' ).val( $Ss_Gratuita.toFixed(2) );
      $( '#span-gratuita' ).text( $Ss_Gratuita.toFixed(2) );
      
      if (parseFloat(response.arrEdit[0].Ss_Descuento) > 0 && $Ss_Descuento_Producto == 0)
        $( '#txt-Ss_Descuento' ).val( response.arrEdit[0].Po_Descuento );
      else
        $( '#txt-Ss_Descuento' ).val( '' );

      $( '#txt-descuento' ).val( response.arrEdit[0].Ss_Descuento );
      $( '#span-descuento' ).text( response.arrEdit[0].Ss_Descuento );

      $( '#txt-impuesto' ).val( $Ss_IGV.toFixed(2) );
      $( '#span-impuesto' ).text( $Ss_IGV.toFixed(2) );
      
      if (parseFloat(response.arrEdit[0].Ss_Percepcion) > 0.00)
        $( '#txt-Ss_Percepcion' ).val( response.arrEdit[0].Ss_Percepcion );
      else
        $( '#txt-Ss_Percepcion' ).val( '' );
      
			$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
			$( '#span-total' ).text( $Ss_Total.toFixed(2) );

			$( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
			
  		validateDecimal();
  		validateNumber();
  		validateNumberOperation();
      validateCodigoBarra();
      
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

function anularCompra(ID, Nu_Enlace, Nu_Descargar_Inventario, accion){
  var $modal_delete = $( '.modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '.modal-message-delete' ).removeClass('modal-danger modal-warning modal-success');
  $( '.modal-message-delete' ).addClass('modal-warning');
  
  $( '.modal-title-message-delete' ).text('¿Deseas anular el documento?');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });
  
  $(document).bind('keydown', 'alt+k', function(){
    if ( accion=='anular' ) {
      _anularCompra($modal_delete, ID, Nu_Enlace, Nu_Descargar_Inventario);
      accion='';
    }
  });

  $( '#btn-save-delete' ).off('click').click(function () {
    _anularCompra($modal_delete, ID, Nu_Enlace, Nu_Descargar_Inventario);
  });
}

function eliminarCompra(ID, Nu_Enlace, Nu_Descargar_Inventario, accion){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });
  
  $(document).bind('keydown', 'alt+l', function(){
    if ( accion=='delete' ) {
      _eliminarCompra($modal_delete, ID, Nu_Enlace, Nu_Descargar_Inventario);
      accion='';
    }
  });
  
  $( '#btn-save-delete' ).off('click').click(function () {
    _eliminarCompra($modal_delete, ID, Nu_Enlace, Nu_Descargar_Inventario);
  });
}

$(function () {
  $('[data-mask]').inputmask();
  $('#txt-Filtro_Fe_Inicio').val(fDay + '/' + fMonth + '/' + fYear);
  $('#txt-Filtro_Fe_Fin').val(fDay + '/' + fMonth + '/' + fYear);

  // Focus item
  $(document).bind('keydown', 'F4', function () {
    $('#txt-No_Producto').focus();
  });

  $('input.hotkey-focus_item').bind('keydown', 'F4', function () {
    $('#txt-No_Producto').focus();
  });
	// ./ Focus item

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

  //Medios de pago - tipos de medios de pago
  $('.div-modal_datos_tarjeta_credito').hide();
  $('#cbo-MediosPago').change(function () {
    ID_Medio_Pago = $(this).val();
    Nu_Tipo_Medio_Pago = $(this).find(':selected').data('nu_tipo');
    $('.div-modal_datos_tarjeta_credito').hide();
    $('#cbo-tarjeta_credito').html('');
    $('#tel-nu_referencia').val('');
    $('#tel-nu_ultimo_4_digitos_tarjeta').val('');
    if (Nu_Tipo_Medio_Pago == 2) {
      $('.div-modal_datos_tarjeta_credito').show();

      url = base_url + 'HelperController/getTiposTarjetaCredito';
      $.post(url, { ID_Medio_Pago: ID_Medio_Pago }, function (response) {
        $('#cbo-tarjeta_credito').html('');
        for (var i = 0; i < response.length; i++)
          $('#cbo-tarjeta_credito').append('<option value="' + response[i].ID_Tipo_Medio_Pago + '">' + response[i].No_Tipo_Medio_Pago + '</option>');
      }, 'JSON');
    }
  })

  // Button - Verificar si existe documento de enlace
  $( '#btn-verificarExisteDocumento' ).click(function(){
    var Input_Serie_Documento_Modificar;
    var ID_Serie_Documento_Modificar;
    if ($( '#cbo-SeriesDocumentoModificar' ).val() !== undefined){
      ID_Serie_Documento_Modificar = $( '#cbo-SeriesDocumentoModificar' ).val();
      Input_Serie_Documento_Modificar = 'cbo-SeriesDocumentoModificar';
    }
    if ($( '#txt-ID_Serie_Documento_Modificar' ).val() !== undefined){
      ID_Serie_Documento_Modificar = $( '#txt-ID_Serie_Documento_Modificar' ).val();
      Input_Serie_Documento_Modificar = 'txt-ID_Serie_Documento_Modificar';
    }
    if ($('[name="ID_Documento_Cabecera_Orden"]').val().length == 0 && nu_enlace == 1 && $( '#cbo-TiposDocumentoModificar' ).val() == '0'){
      $( '#cbo-TiposDocumentoModificar' ).closest('.form-group').find('.help-block').html('Seleccionar documento');
  	  $( '#cbo-TiposDocumentoModificar' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ($('[name="ID_Documento_Cabecera_Orden"]').val().length == 0 && nu_enlace == 1 && ID_Serie_Documento_Modificar == '0'){
      $( '#' + Input_Serie_Documento_Modificar ).closest('.form-group').find('.help-block').html('Seleccionar serie');
  	  $( '#' + Input_Serie_Documento_Modificar ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ($('[name="ID_Documento_Cabecera_Orden"]').val().length == 0 && nu_enlace == 1 && $( '#txt-ID_Numero_Documento_Modificar' ).val().length === 0){
      $( '#txt-ID_Numero_Documento_Modificar' ).closest('.form-group').find('.help-block').html('Ingresar numero');
  	  $( '#txt-ID_Numero_Documento_Modificar' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ($('[name="addProveedor"]:checked').attr('value') == 0 && ($('#txt-AID').val().length === 0 || $('#txt-ANombre').val().length === 0 || $('#txt-ACodigo').val().length === 0)) {
      $('#txt-ANombre').closest('.form-group').find('.help-block').html('Seleccionar proveedor');
      $('#txt-ANombre').closest('.form-group').removeClass('has-success').addClass('has-error');

      bEstadoValidacion = false;
      scrollToError($("html, body"), $('#txt-ANombre'));
    } else {
      $( '#btn-verificarExisteDocumento' ).text('');
      $( '#btn-verificarExisteDocumento' ).attr('disabled', true);
      $( '#btn-verificarExisteDocumento' ).append( 'Verificando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

      url = base_url + 'HelperController/documentExistVerify';
      $.post( url, {
        ID_Documento_Guardado : $( '#txt-ID_Documento_Guardado' ).val(),
        ID_Tipo_Documento_Modificar : $( '#cbo-TiposDocumentoModificar' ).val(),
        ID_Serie_Documento_Modificar : ID_Serie_Documento_Modificar,
        ID_Numero_Documento_Modificar : $( '#txt-ID_Numero_Documento_Modificar' ).val(),
        iIdEntidad : $( '#txt-AID' ).val(),
        iTipoCliente : 0,
      }, function( response ){
  	    $( '.div-DocumentoModificar' ).removeClass('panel-default panel-warning panel-danger panel-success');
  	    $( '.div-mensaje_verificarExisteDocumento' ).removeClass('text-warning text-danger text-success');
  		  if (response.status == 'error'){
  		    $( '#btn-save' ).attr('disabled', true);
    	    $( '.div-DocumentoModificar' ).addClass(response.style_panel);
    	    $( '.div-mensaje_verificarExisteDocumento' ).addClass(response.style_p);
  		    $( '.div-mensaje_verificarExisteDocumento' ).text(response.message);
  		  } else {
          $( '#btn-save' ).attr('disabled', false);
  		    $( '.div-DocumentoModificar' ).addClass(response.style_panel);
  		    $( '.div-mensaje_verificarExisteDocumento' ).addClass(response.style_p);
  		    $( '.div-mensaje_verificarExisteDocumento' ).text(response.message);
  		  }
        $( '#btn-verificarExisteDocumento' ).text('');
        $( '#btn-verificarExisteDocumento' ).append( 'Verificar <span class="fa fa-check"></span>' );
        $( '#btn-verificarExisteDocumento' ).attr('disabled', false);
      }, 'JSON');
    }
  })
  // ./ Button - Verificar si existe documento de enlace
  
  $( '#radio-proveedor_existente' ).on('ifChecked', function () {
    $( '.div-proveedor_existente' ).show();
    $( '.div-proveedor_nuevo' ).hide();
  })
  
  $( '#radio-proveedor_nuevo' ).on('ifChecked', function () {
    $( '.div-proveedor_existente' ).hide();
    $( '.div-proveedor_nuevo' ).show();
  })
  
  //LAE API SUNAT / RENIEC - PROVEEDOR
  $( '#btn-cloud-api_compra_proveedor' ).click(function(){
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
      $( '#btn-cloud-api_compra_proveedor' ).text('');
      $( '#btn-cloud-api_compra_proveedor' ).attr('disabled', true);
      $( '#btn-cloud-api_compra_proveedor' ).append( '<i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
      
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
          $( '#btn-cloud-api_compra_proveedor' ).closest('.form-group').find('.help-block').html('');
      	  $( '#btn-cloud-api_compra_proveedor' ).closest('.form-group').removeClass('has-success').addClass('has-error');
      	  
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
  		  	
          $( '#btn-cloud-api_compra_proveedor' ).text('');
          $( '#btn-cloud-api_compra_proveedor' ).attr('disabled', false);
          $( '#btn-cloud-api_compra_proveedor' ).append( '<i class="fa fa-cloud-download fa-lg"></i>' );
        },
        error: function(response){
          $( '#btn-cloud-api_compra_proveedor' ).closest('.form-group').find('.help-block').html('Sin acceso');
      	  $( '#btn-cloud-api_compra_proveedor' ).closest('.form-group').removeClass('has-success').addClass('has-error');
      	  
          $( '#txt-No_Entidad_Proveedor' ).val( '' );
          $( '#txt-Txt_Direccion_Entidad_Proveedor' ).val( '' );
          $( '#txt-Nu_Telefono_Entidad_Proveedor' ).val( '' );
          $( '#txt-Nu_Celular_Entidad_Proveedor' ).val( '' );

          $( '#btn-cloud-api_compra_proveedor' ).text('');
          $( '#btn-cloud-api_compra_proveedor' ).attr('disabled', false);
          $( '#btn-cloud-api_compra_proveedor' ).append( '<i class="fa fa-cloud-download fa-lg"></i>' );
        }
      });
    }
  })
  
	/* Tipo Documento Identidad Proveedor */
	$( '#cbo-TiposDocumentoIdentidadProveedor' ).change(function(){
	  if ( $(this).val() == 2 ) {//DNI
		  $( '#label-Nombre_Documento_Identidad_Proveedor' ).text('DNI');
		  $( '#label-No_Entidad_Proveeedor' ).text('Nombre(s) y Apellidos');
			$( '#txt-Nu_Documento_Identidad_Proveedor' ).attr('maxlength', $(this).find(':selected').data('nu_cantidad_caracteres'));
	  } else if ( $(this).val() == 4 ) {//RUC
		  $( '#label-Nombre_Documento_Identidad_Proveedor' ).text('RUC');
		  $( '#label-No_Entidad_Proveeedor' ).text('Razón Social');
			$( '#txt-Nu_Documento_Identidad_Proveedor' ).attr('maxlength', $(this).find(':selected').data('nu_cantidad_caracteres'));
	  } else {
	    $( '#label-Nombre_Documento_Identidad_Proveedor' ).text('# Documento Identidad');
		  $( '#label-No_Entidad_Proveeedor' ).text('Nombre(s) y Apellidos');
			$( '#txt-Nu_Documento_Identidad_Proveedor' ).attr('maxlength', $(this).find(':selected').data('nu_cantidad_caracteres'));
	  }
	})
  
  url = base_url + 'HelperController/getTiposDocumentos';
  $.post( url, {Nu_Tipo_Filtro : 4}, function( response ){//2 = Compra
    $( '#cbo-Filtro_TiposDocumento' ).html('<option value="0" selected="selected">Todos</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-Filtro_TiposDocumento' ).append( '<option value="' + response[i]['ID_Tipo_Documento'] + '">' + response[i]['No_Tipo_Documento_Breve'] + '</option>' );
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
  
  url = base_url + 'Logistica/CompraController/ajax_list';
  table_compra = $('#table-Compra').DataTable({
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
      titleAttr : 'Columnas'
    }],
    'searching'   : false,
    //'bStateSave'  : true,
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
        data.Filtro_Fe_Inicio       = ParseDateString($( '#txt-Filtro_Fe_Inicio' ).val(), 1, '/'),
        data.Filtro_Fe_Fin          = ParseDateString($( '#txt-Filtro_Fe_Fin' ).val(), 1, '/'),
        data.Filtro_TiposDocumento  = $( '#cbo-Filtro_TiposDocumento' ).val(),
        data.Filtro_SerieDocumento  = $( '#txt-Filtro_SerieDocumento' ).val(),
        data.Filtro_NumeroDocumento = $( '#txt-Filtro_NumeroDocumento' ).val(),
        data.Filtro_Estado = $('#cbo-Filtro_Estado').val(),
        data.Filtro_Estado_Pago = $('#cbo-Filtro_Estado_Pago').val(),
        data.Filtro_ID_Entidad = $('#txt-AID_Doble').val(),
        data.Filtro_Entidad = $('#txt-Filtro_Entidad').val();
      },
    },
    'columnDefs': [
      {
        'targets': 'no-hidden',
        "visible": false, 
      },{
      'className' : 'text-center',
      'targets'   : 'no-sort',
      'orderable' : false,
    },
    {
      'className' : 'text-left',
      'targets'   : 'no-sort_left',
      'orderable' : false,
    },
    {
      'className' : 'text-right',
      'targets'   : 'no-sort_right',
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
    'lengthMenu': [[10, 100, 1000, -1], [10, 100, 500, 1000]],
  });

  $('.dataTables_length').addClass('col-xs-4 col-sm-5 col-md-1');
  $('.dataTables_info').addClass('col-xs-8 col-sm-7 col-md-4');
  $('.dataTables_paginate').addClass('col-xs-12 col-sm-12 col-md-7');

  $( '#btn-filter' ).click(function(){
    table_compra.ajax.reload();
  });
  
  $( '#form-Compra' ).validate({
		rules:{
			ID_Serie_Documento: {
				required: true,
			},
			ID_Numero_Documento: {
				required: true,
			},
			Fe_Emision:{
				required: true,
			},
			Fe_Detraccion: {
				required: true,
			},
			Nu_Detraccion: {
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
			Fe_Detraccion:{
				required: "Ingresar F. Detracción",
			},
			Nu_Detraccion:{
				required: "Ingresar número",
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
		submitHandler: form_Compra
  });

  $('#btn-save').click(function () {
    form_Compra();
  })

  // COBRAR CREDITO
  $('#btn-cobrar_proveedor').click(function () {
    var fPagoProveedorCobranza = parseFloat($('#modal-tel-cobrar_proveedor-fPagoProveedor').val());
    if (fPagoProveedorCobranza == 0.00 || isNaN(fPagoProveedorCobranza)) {
      $('[name="fPagoProveedor"]').closest('.form-group').find('.help-block').html('Ingresar monto');
      $('[name="fPagoProveedor"]').closest('.form-group').removeClass('has-success').addClass('has-error');

      scrollToError($('.modal-cobrar_proveedor .modal-body'), $('[name="fPagoProveedor"]'));
    } else if (fPagoProveedorCobranza > parseFloat($('#hidden-cobrar_proveedor-fsaldo').val())) {
      $('#modal-tel-cobrar_proveedor-fPagoProveedor').closest('.form-group').find('.help-block').html('Debes de pagar <b>' + $('#hidden-cobrar_proveedor-fsaldo').val() + '</b>');
      $('#modal-tel-cobrar_proveedor-fPagoProveedor').closest('.form-group').removeClass('has-success').addClass('has-error');

      scrollToError($('.modal-cobrar_proveedor .modal-body'), $('#modal-tel-cobrar_proveedor-fPagoProveedor'));
    } else if ($('#cbo-modal_quien_recibe').val() == 0 && $('[name="sNombreRecepcion"]').val().length === 0) {
      $('[name="sNombreRecepcion"]').closest('.form-group').find('.help-block').html('Ingresar datos');
      $('[name="sNombreRecepcion"]').closest('.form-group').removeClass('has-success').addClass('has-error');

      scrollToError($('.modal-cobrar_proveedor .modal-body'), $('[name="sNombreRecepcion"]'));
    } else {
      $('.help-block').empty();
      $('[name="fPagoProveedor"]').closest('.form-group').removeClass('has-error');
      $('[name="sNombreRecepcion"]').closest('.form-group').removeClass('has-error');

      $('#btn-cobrar_proveedor').text('');
      $('#btn-cobrar_proveedor').attr('disabled', true);
      $('#btn-cobrar_proveedor').append('Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>');
      $('#btn-salir').attr('disabled', true);

      url = base_url + 'HelperController/cobranzaProveedorPuntoVenta';
      $.ajax({
        type: 'POST',
        dataType: 'JSON',
        url: url,
        data: $('#form-cobrar_proveedor').serialize(),
        success: function (response) {
          $('.modal-message').removeClass('modal-danger modal-warning modal-success');
          $('#modal-message').modal('show');

          if (response.sStatus == 'success') {
            $('.modal-cobrar_proveedor').modal('hide');

            $('.modal-message').addClass('modal-' + response.sStatus);
            $('.modal-title-message').text(response.sMessage);
            setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);

            table_compra.ajax.reload();
          } else {
            $('.modal-message').addClass('modal-' + response.sStatus);
            $('.modal-title-message').text(response.sMessage);
            setTimeout(function () { $('#modal-message').modal('hide'); }, 3100);
          }

          $('#btn-cobrar_proveedor').text('');
          $('#btn-cobrar_proveedor').append('Pagar');
          $('#btn-cobrar_proveedor').attr('disabled', false);
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

          $('#btn-cobrar_proveedor').text('');
          $('#btn-cobrar_proveedor').attr('disabled', false);
          $('#btn-cobrar_proveedor').append('Pagar');
          $('#btn-salir').attr('disabled', false);
        })
    }
  })

	$( '#cbo-Monedas' ).change(function(){
	  if ( $(this).val() > 0 )
	    $( '.span-signo' ).text( $(this).find(':selected').data('no_signo') );
	})
	
	$( '.div-MediosPago' ).hide();
	$( '#cbo-MediosPago' ).change(function(){
	  $( '.div-MediosPago' ).hide();
	  if ( $(this).find(':selected').data('nu_tipo') == 1)// Si es crédito
	    $( '.div-MediosPago' ).show();
	})
	
	$( '.div-Periodo' ).hide();
	$( '#cbo-Periodo' ).change(function(){
	  $( '.div-Periodo' ).hide();
	  if ( $(this).val() == 1 )
	    $( '.div-Periodo' ).show();
	})

  $( '#cbo-almacen' ).change(function(){
    if ( $(this).val() > 0 ) {
      var arrParams = {
        ID_Almacen: $('#cbo-almacen').val(),
      };
      getListaPrecios(arrParams);
    }
  })

  $( '#div-addDetraccion').hide();
  
  var _ID_Producto = '';
  var option_impuesto_producto = '';
  $( '#btn-addProductoCompra' ).click(function(){
    var $ID_Producto = $( '#txt-ID_Producto' ).val();
    var $Ss_Precio = parseFloat($( '#txt-Ss_Precio' ).val());
    var $No_Producto = $( '#txt-No_Producto' ).val();
    var $ID_Impuesto_Cruce_Documento = $( '#txt-ID_Impuesto_Cruce_Documento' ).val();
    var $Nu_Tipo_Impuesto = $( '#txt-Nu_Tipo_Impuesto' ).val();
    var $Ss_Impuesto = $('#txt-Ss_Impuesto').val();
    var $No_Unidad_Medida = $('#txt-No_Unidad_Medida').val();
    var $Ss_SubTotal_Producto = 0.00;
    var $Ss_Total_Producto = 0.00;
    var $no_variante_1 = $('#txt-no_variante_1').val();
    var $no_valor_variante_1 = $('#txt-no_valor_variante_1').val();
    var $no_variante_2 = $('#txt-no_variante_2').val();
    var $no_valor_variante_2 = $('#txt-no_valor_variante_2').val();
    var $no_variante_3 = $('#txt-no_variante_3').val();
    var $no_valor_variante_3 = $('#txt-no_valor_variante_3').val();
    
    var arrDataAdicionalTmpDetalleItem = {
      'no_variante_1' : $no_variante_1,
      'no_valor_variante_1' : $no_valor_variante_1,
      'no_variante_2' : $no_variante_2,
      'no_valor_variante_2' : $no_valor_variante_2,
      'no_variante_3' : $no_variante_3,
      'no_valor_variante_3' : $no_valor_variante_3,
    };

    bEstadoValidacion = validatePreviousDocumentToSavePurchase();
    
    if ( bEstadoValidacion == true && $ID_Producto.length === 0 || $No_Producto.length === 0) {
	    $( '#txt-No_Producto' ).closest('.form-group').find('.help-block').html('Ingresar producto');
			$( '#txt-No_Producto' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if (bEstadoValidacion) {
      if ($('[name="addProveedor"]:checked').attr('value') == 1){//Agregar proveedor
        if ( $( '#cbo-Estado' ).val() == 1 ) {//1 = Activo
          generarTablaTemporalItems($ID_Producto, $No_Producto, $Ss_Precio, $ID_Impuesto_Cruce_Documento, $Nu_Tipo_Impuesto, $Ss_Impuesto, $No_Unidad_Medida, arrDataAdicionalTmpDetalleItem);
        } else {
        	$( '#modal-message' ).modal('show');
          $( '.modal-message' ).addClass('modal-danger');
          $( '.modal-title-message' ).text( 'El proveedor se encuentra con BAJA DE OFICIO / NO HABIDO' );
          setTimeout(function() {$('#modal-message').modal('hide');}, 2500);
        }
      } else {
        var arrPOST = {
    		  sTipoData : 'get_entidad',
          iIDEntidad : $( '#txt-AID' ).val(),
          iTipoEntidad : 1,
    		};
        url = base_url + 'HelperController/getDataGeneral';
        $.post(url, arrPOST, function(response){
        	$( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
          if ( response.sStatus == 'success' ) {// Si el RUC es válido
            if ( response.arrData[0].Nu_Estado == '1' ){
              generarTablaTemporalItems($ID_Producto, $No_Producto, $Ss_Precio, $ID_Impuesto_Cruce_Documento, $Nu_Tipo_Impuesto, $Ss_Impuesto, $No_Unidad_Medida, arrDataAdicionalTmpDetalleItem);
      	    } else if ( response.arrData[0].Nu_Estado != '1' ){
          	  $( '#modal-message' ).modal('show');
              $( '.modal-message' ).addClass('modal-danger');
              $( '.modal-title-message' ).text( 'El proveedor se encuentra con BAJA DE OFICIO / NO HABIDO' );
              setTimeout(function() {$('#modal-message').modal('hide');}, 2500);
            }
          } else {
            if( response.sMessageSQL !== undefined ) {
              console.log(response.sMessageSQL);
            }
        	  $( '#modal-message' ).modal('show');
            $( '.modal-message' ).addClass(response.sClassModal);
            $( '.modal-title-message' ).text(response.sMessage);
            setTimeout(function() {$('#modal-message').modal('hide');}, 2500);
          }
        }, 'json');// Obtener informacion de una entidad, para saber si esta HABIDO y SIN BAJA DE OFICIO
      }// /. Verificar si es un proveedor existente o nuevo
    }// /. Validaciones previas
	})

  $('#table-DetalleProductos tbody' ).on('input', '.txt-Ss_Precio', function(){
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

    fila.find(".txt-fValorUnitario").val(parseFloat(precio / impuesto_producto).toFixed(2));

    if ( parseFloat(precio) > 0.00 && parseFloat(cantidad) > 0){
      $('#tr_detalle_producto' + $ID_Producto).removeClass('danger');
	    $( '#table-DetalleProductos tfoot' ).empty();
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
        $("#table-DetalleProductos > tbody > tr").each(function(){
          var rows = $(this);
          var Ss_Impuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
          var Nu_Tipo_Impuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_Total_Producto', this).val() / Ss_Impuesto);
          var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());
          var $Ss_Total_Producto = parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());

          $Ss_Total += $Ss_Total_Producto;
          
          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
            
          if (Nu_Tipo_Impuesto == 1){
            $Ss_SubTotal += $Ss_SubTotal_Producto;
            $Ss_IGV += $Ss_Total_Producto - $Ss_SubTotal_Producto;
          }

          $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto))) / 100);
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
        $("#table-DetalleProductos > tbody > tr").each(function(){
          var rows = $(this);
          var Ss_Impuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
          var Nu_Tipo_Impuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
          var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());

          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
            
          if (Nu_Tipo_Impuesto == 2)
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

        $( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
      } else if (nu_tipo_impuesto == 3) {//Exonerada
        fila.find(".td-fDescuentoSinImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_Total_Producto").val((parseFloat(((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)) * impuesto_producto).toFixed(2)).toString().split(". "));
        
        var $Ss_Exonerada = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleProductos > tbody > tr").each(function(){
          var rows = $(this);
          var Ss_Impuesto           = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
          var Nu_Tipo_Impuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
          var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());

          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
            
          if (Nu_Tipo_Impuesto == 3)
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

        $( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
      } else if (nu_tipo_impuesto == 4) {//Gratuita
        fila.find(".td-fDescuentoSinImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_Total_Producto").val((parseFloat(((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)) * impuesto_producto).toFixed(2)).toString().split(". "));
        
        var $Ss_Gratuita = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleProductos > tbody > tr").each(function(){
          var rows = $(this);
          var Ss_Impuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
          var Nu_Tipo_Impuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
          var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());

          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
            
          if (Nu_Tipo_Impuesto == 4)
            $Ss_Gratuita += $Ss_SubTotal_Producto;

          $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto))) / 100);
          $Ss_Total += parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
        });
        
        $( '#txt-gratuita' ).val( $Ss_Gratuita.toFixed(2) );
    		$( '#span-gratuita' ).text( $Ss_Gratuita.toFixed(2) );
    		
    		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
    		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
    		
    		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
    		$( '#span-total' ).text( $Ss_Total.toFixed(2) );

        $( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
  		}
    }
  })
	
  $('#table-DetalleProductos tbody' ).on('input', '.txt-Qt_Producto', function(){
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
      $( '#table-DetalleProductos tfoot' ).empty();
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
        $("#table-DetalleProductos > tbody > tr").each(function(){
          var rows = $(this);
          var Ss_Impuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
          var Nu_Tipo_Impuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_Total_Producto', this).val() / Ss_Impuesto);
          var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());
          var $Ss_Total_Producto = parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());

          $Ss_Total += $Ss_Total_Producto;

          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
            
          if (Nu_Tipo_Impuesto == 1){
            $Ss_SubTotal += $Ss_SubTotal_Producto;
            $Ss_IGV += $Ss_Total_Producto - $Ss_SubTotal_Producto;
          }

          $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto))) / 100);
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
        $("#table-DetalleProductos > tbody > tr").each(function(){
          var rows = $(this);
          var Ss_Impuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
          var Nu_Tipo_Impuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
          var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());

          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
            
          if (Nu_Tipo_Impuesto == 2)
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

        $( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
      } else if (nu_tipo_impuesto == 3) {//Exonerada
        fila.find(".td-fDescuentoSinImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_Total_Producto").val((parseFloat(((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)) * impuesto_producto).toFixed(2)).toString().split(". "));
        
        var $Ss_Exonerada = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleProductos > tbody > tr").each(function(){
          var rows = $(this);
          var Ss_Impuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
          var Nu_Tipo_Impuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
          var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());

          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
            
          if (Nu_Tipo_Impuesto == 3)
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

        $( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
      } else if (nu_tipo_impuesto == 4) {//Gratuita
        fila.find(".td-fDescuentoSinImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_Total_Producto").val((parseFloat(((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)) * impuesto_producto).toFixed(2)).toString().split(". "));
        
        var $Ss_Gratuita = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleProductos > tbody > tr").each(function(){
          var rows = $(this);
          var Ss_Impuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
          var Nu_Tipo_Impuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
          var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());

          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
            
          if (Nu_Tipo_Impuesto == 4)
            $Ss_Gratuita += $Ss_SubTotal_Producto;

          $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto))) / 100);
          $Ss_Total += parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
        });
        
        $( '#txt-gratuita' ).val( $Ss_Gratuita.toFixed(2) );
    		$( '#span-gratuita' ).text( $Ss_Gratuita.toFixed(2) );
    		
    		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
    		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
    		
    		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
    		$( '#span-total' ).text( $Ss_Total.toFixed(2) );

        $( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
      }
    }
  })

  $('#table-DetalleProductos tbody' ).on('change', '.cbo-ImpuestosProducto', function(){
    var fila = $(this).parents("tr");
    var fValorUnitario = fila.find(".txt-fValorUnitario").val();
    var precio = fila.find(".txt-Ss_Precio").val();
    var cantidad = fila.find(".txt-Qt_Producto").val();
    var subtotal_producto = fila.find(".txt-Ss_SubTotal_Producto").val();
    var impuesto_producto = fila.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto');
    var nu_tipo_impuesto = fila.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
    var total_producto = fila.find(".txt-Ss_Total_Producto").val();
    var descuento = fila.find(".txt-Ss_Descuento").val();
    var fDescuento_SubTotal_Producto = 0, fDescuento_Total_Producto = 0;

    if ( parseFloat(precio) > 0.00 && parseFloat(cantidad) > 0 && parseFloat(total_producto) > 0){
      if (nu_tipo_impuesto == 1) {//CON IGV
        fila.find(".td-fDescuentoSinImpuestosItem").text((parseFloat(((descuento * (subtotal_producto)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (subtotal_producto * impuesto_producto)) / 100) - ((descuento * (subtotal_producto)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_Precio").val((parseFloat(fValorUnitario * impuesto_producto).toFixed(6)).toString().split(". "));
        fila.find(".txt-Ss_Total_Producto").val((parseFloat(subtotal_producto * impuesto_producto).toFixed(2)).toString().split(". "));

        var $Ss_SubTotal = 0.00;
        var $Ss_Inafecto = 0.00;
        var $Ss_Exonerada = 0.00;
        var $Ss_Gratuita = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_IGV = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleProductos > tbody > tr").each(function(){
          var rows = $(this);
          var Ss_Impuesto           = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
          var Nu_Tipo_Impuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_Total_Producto', this).val() / Ss_Impuesto);
          var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());
          var $Ss_Total_Producto = parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());

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

          $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto))) / 100);
        });
        
        $( '#txt-subTotal' ).val( $Ss_SubTotal.toFixed(2) );
        $( '#span-subTotal' ).text( $Ss_SubTotal.toFixed(2) );
        
        $( '#txt-inafecto' ).val( $Ss_Inafecto.toFixed(2) );
        $( '#span-inafecto' ).text( $Ss_Inafecto.toFixed(2) );
        
        $( '#txt-exonerada' ).val( $Ss_Exonerada.toFixed(2) );
        $( '#span-exonerada' ).text( $Ss_Exonerada.toFixed(2) );
        
        $( '#txt-gratuita' ).val( $Ss_Gratuita.toFixed(2) );
        $( '#span-gratuita' ).text( $Ss_Gratuita.toFixed(2) );
        
        $( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
        $( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
        
        $( '#txt-impuesto' ).val( $Ss_IGV.toFixed(2) );
        $( '#span-impuesto' ).text( $Ss_IGV.toFixed(2) );
        
        $( '#txt-total' ).val( $Ss_Total.toFixed(2) );
        $( '#span-total' ).text( $Ss_Total.toFixed(2) );

        $( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
      } else if (nu_tipo_impuesto == 2) {//Inafecto
        fila.find(".td-fDescuentoSinImpuestosItem").text((parseFloat(((descuento * (subtotal_producto)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (subtotal_producto * impuesto_producto)) / 100) - ((descuento * (subtotal_producto)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_Precio").val((parseFloat(fValorUnitario * impuesto_producto).toFixed(6)).toString().split(". "));
        fila.find(".txt-Ss_Total_Producto").val((parseFloat(subtotal_producto * impuesto_producto).toFixed(2)).toString().split(". "));

        fila.find(".txt-Ss_SubTotal_Producto").val(parseFloat(subtotal_producto).toFixed(2));

        var $Ss_SubTotal = 0.00;
        var $Ss_Inafecto = 0.00;
        var $Ss_Exonerada = 0.00;
        var $Ss_Gratuita = 0.00;
        var $Ss_IGV = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleProductos > tbody > tr").each(function(){
          var rows = $(this);
          var Ss_Impuesto           = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
          var Nu_Tipo_Impuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
          var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());

          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
          
          if (Nu_Tipo_Impuesto == 1){
            $Ss_SubTotal += $Ss_SubTotal_Producto;
            $Ss_IGV += (($Ss_SubTotal_Producto * Ss_Impuesto) - $Ss_SubTotal_Producto);
          } else if (Nu_Tipo_Impuesto == 2) {
            $Ss_Inafecto += $Ss_SubTotal_Producto;
          } else if (Nu_Tipo_Impuesto == 3) {
            $Ss_Exonerada += $Ss_SubTotal_Producto;
          } else if (Nu_Tipo_Impuesto == 4) {
            $Ss_Gratuita += $Ss_SubTotal_Producto;
          }

          $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto))) / 100);
          $Ss_Total += parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
        });
        
        $( '#txt-subTotal' ).val( $Ss_SubTotal.toFixed(2) );
        $( '#span-subTotal' ).text( $Ss_SubTotal.toFixed(2) );
        
        $( '#txt-inafecto' ).val( $Ss_Inafecto.toFixed(2) );
        $( '#span-inafecto' ).text( $Ss_Inafecto.toFixed(2) );
        
        $( '#txt-exonerada' ).val( $Ss_Exonerada.toFixed(2) );
        $( '#span-exonerada' ).text( $Ss_Exonerada.toFixed(2) );
        
        $( '#txt-gratuita' ).val( $Ss_Gratuita.toFixed(2) );
        $( '#span-gratuita' ).text( $Ss_Gratuita.toFixed(2) );
        
        $( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
        $( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
        
        $( '#txt-impuesto' ).val( $Ss_IGV.toFixed(2) );
        $( '#span-impuesto' ).text( $Ss_IGV.toFixed(2) );
        
        $( '#txt-total' ).val( $Ss_Total.toFixed(2) );
        $( '#span-total' ).text( $Ss_Total.toFixed(2) );

        $( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
      } else if (nu_tipo_impuesto == 3) {//Exonerada
        fila.find(".td-fDescuentoSinImpuestosItem").text((parseFloat(((descuento * (subtotal_producto)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (subtotal_producto * impuesto_producto)) / 100) - ((descuento * (subtotal_producto)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_Precio").val((parseFloat(fValorUnitario * impuesto_producto).toFixed(6)).toString().split(". "));
        fila.find(".txt-Ss_Total_Producto").val((parseFloat(subtotal_producto * impuesto_producto).toFixed(2)).toString().split(". "));

        fila.find(".txt-Ss_SubTotal_Producto").val(parseFloat(subtotal_producto).toFixed(2));

        var $Ss_SubTotal = 0.00;
        var $Ss_Inafecto = 0.00;
        var $Ss_Exonerada = 0.00;
        var $Ss_Gratuita = 0.00;
        var $Ss_IGV = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleProductos > tbody > tr").each(function(){
          var rows = $(this);
          var Ss_Impuesto           = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
          var Nu_Tipo_Impuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
          var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());
  
          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;

          if (Nu_Tipo_Impuesto == 1){
            $Ss_SubTotal += $Ss_SubTotal_Producto;
            $Ss_IGV += (($Ss_SubTotal_Producto * Ss_Impuesto) - $Ss_SubTotal_Producto);
          } else if (Nu_Tipo_Impuesto == 2) {
            $Ss_Inafecto += $Ss_SubTotal_Producto;
          } else if (Nu_Tipo_Impuesto == 3) {
            $Ss_Exonerada += $Ss_SubTotal_Producto;
          } else if (Nu_Tipo_Impuesto == 4) {
            $Ss_Gratuita += $Ss_SubTotal_Producto;
          }
          $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto))) / 100);
          $Ss_Total += parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
        });
        
        $( '#txt-subTotal' ).val( $Ss_SubTotal.toFixed(2) );
        $( '#span-subTotal' ).text( $Ss_SubTotal.toFixed(2) );
        
        $( '#txt-inafecto' ).val( $Ss_Inafecto.toFixed(2) );
        $( '#span-inafecto' ).text( $Ss_Inafecto.toFixed(2) );
        
        $( '#txt-exonerada' ).val( $Ss_Exonerada.toFixed(2) );
        $( '#span-exonerada' ).text( $Ss_Exonerada.toFixed(2) );
        
        $( '#txt-gratuita' ).val( $Ss_Gratuita.toFixed(2) );
        $( '#span-gratuita' ).text( $Ss_Gratuita.toFixed(2) );
        
        $( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
        $( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
        
        $( '#txt-impuesto' ).val( $Ss_IGV.toFixed(2) );
        $( '#span-impuesto' ).text( $Ss_IGV.toFixed(2) );
        
        $( '#txt-total' ).val( $Ss_Total.toFixed(2) );
        $( '#span-total' ).text( $Ss_Total.toFixed(2) );

        $( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
      } else if (nu_tipo_impuesto == 4) {//Gratuita
        fila.find(".td-fDescuentoSinImpuestosItem").text((parseFloat(((descuento * (subtotal_producto)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (subtotal_producto * impuesto_producto)) / 100) - ((descuento * (subtotal_producto)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_Precio").val((parseFloat(fValorUnitario * impuesto_producto).toFixed(6)).toString().split(". "));
        fila.find(".txt-Ss_Total_Producto").val((parseFloat(subtotal_producto * impuesto_producto).toFixed(2)).toString().split(". "));

        fila.find(".txt-Ss_SubTotal_Producto").val(parseFloat(subtotal_producto).toFixed(2));

        var $Ss_SubTotal = 0.00;
        var $Ss_Inafecto = 0.00;
        var $Ss_Exonerada = 0.00;
        var $Ss_Gratuita = 0.00;
        var $Ss_IGV = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleProductos > tbody > tr").each(function(){
          var rows = $(this);
          var Ss_Impuesto           = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
          var Nu_Tipo_Impuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
          var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());
  
          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
          
          if (Nu_Tipo_Impuesto == 1){
            $Ss_SubTotal += $Ss_SubTotal_Producto;
            $Ss_IGV += (($Ss_SubTotal_Producto * Ss_Impuesto) - $Ss_SubTotal_Producto);
          } else if (Nu_Tipo_Impuesto == 2) {
            $Ss_Inafecto += $Ss_SubTotal_Producto;
          } else if (Nu_Tipo_Impuesto == 3) {
            $Ss_Exonerada += $Ss_SubTotal_Producto;
          } else if (Nu_Tipo_Impuesto == 4) {
            $Ss_Gratuita += $Ss_SubTotal_Producto;
          }

          $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto))) / 100);
          $Ss_Total += parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
        });
        
        $( '#txt-subTotal' ).val( $Ss_SubTotal.toFixed(2) );
        $( '#span-subTotal' ).text( $Ss_SubTotal.toFixed(2) );
        
        $( '#txt-inafecto' ).val( $Ss_Inafecto.toFixed(2) );
        $( '#span-inafecto' ).text( $Ss_Inafecto.toFixed(2) );
        
        $( '#txt-exonerada' ).val( $Ss_Exonerada.toFixed(2) );
        $( '#span-exonerada' ).text( $Ss_Exonerada.toFixed(2) );
        
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
    }
  })
  
  $('#table-DetalleProductos tbody' ).on('input', '.txt-Ss_Descuento', function(){
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
          $("#table-DetalleProductos > tbody > tr").each(function(){
            var rows = $(this);
            var Ss_Impuesto           = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
            var Nu_Tipo_Impuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
            var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_Total_Producto', this).val() / Ss_Impuesto);
            var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());
            var $Ss_Total_Producto = parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
  
            $Ss_Total += $Ss_Total_Producto;
  
            if(isNaN($Ss_Descuento_Producto))
              $Ss_Descuento_Producto = 0;
          
            if (Nu_Tipo_Impuesto == 1){
              $Ss_SubTotal += $Ss_SubTotal_Producto;
              $Ss_IGV += $Ss_Total_Producto - $Ss_SubTotal_Producto;
            }

            $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto))) / 100);
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
          $("#table-DetalleProductos > tbody > tr").each(function(){
            var rows = $(this);
            var Ss_Impuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
            var Nu_Tipo_Impuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
            var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
            var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());
  
            if(isNaN($Ss_Descuento_Producto))
              $Ss_Descuento_Producto = 0;
              
            if (Nu_Tipo_Impuesto == 2)
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

          $( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
        } else if (nu_tipo_impuesto == 3) {//Exonerada
          fila.find(".td-fDescuentoSinImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
          fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
          fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
          fila.find(".txt-Ss_Total_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));

          var $Ss_Exonerada = 0.00;
          var $Ss_Descuento = 0.00;
          var $Ss_Total = 0.00;
          
          $("#table-DetalleProductos > tbody > tr").each(function(){
            var rows = $(this);
            var Ss_Impuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
            var Nu_Tipo_Impuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
            var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
            var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());
  
            if(isNaN($Ss_Descuento_Producto))
              $Ss_Descuento_Producto = 0;
              
            if (Nu_Tipo_Impuesto == 3)
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

          $( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
        } else if (nu_tipo_impuesto == 4) {//Gratuita
          fila.find(".td-fDescuentoSinImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
          fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
          fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
          fila.find(".txt-Ss_Total_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));

          var $Ss_Gratuita = 0.00;
          var $Ss_Descuento = 0.00;
          var $Ss_Total = 0.00;
          $("#table-DetalleProductos > tbody > tr").each(function(){
            var rows = $(this);
            var Ss_Impuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
            var Nu_Tipo_Impuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
            var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
            var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());
  
            if(isNaN($Ss_Descuento_Producto))
              $Ss_Descuento_Producto = 0;
              
            if (Nu_Tipo_Impuesto == 4)
              $Ss_Gratuita += $Ss_SubTotal_Producto;

            $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto))) / 100);
            $Ss_Total += parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
          });
          
          $( '#txt-gratuita' ).val( $Ss_Gratuita.toFixed(2) );
      		$( '#span-gratuita' ).text( $Ss_Gratuita.toFixed(2) );
      		
      		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
      		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
      		
      		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
      		$( '#span-total' ).text( $Ss_Total.toFixed(2) );

          $( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
    		}
      }
    }
  })

  $('#table-DetalleProductos tbody' ).on('input', '.txt-Ss_Total_Producto', function(){
    var fila = $(this).parents("tr");
    var $ID_Producto = fila.find(".txt-Ss_Precio").data('id_producto');
    var precio = fila.find(".txt-Ss_Precio").val();
    var cantidad = fila.find(".txt-Qt_Producto").val();
    var subtotal_producto = fila.find(".txt-Ss_SubTotal_Producto").val();
    var impuesto_producto = fila.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto');
    var nu_tipo_impuesto = fila.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
    var descuento = fila.find(".txt-Ss_Descuento").val();
    var total_producto = fila.find(".txt-Ss_Total_Producto").val();
    
    if ( parseFloat(cantidad) > 0 && parseFloat(total_producto) > 0 ) {
      $('#tr_detalle_producto' + $ID_Producto).removeClass('danger');
      $( '#table-DetalleProductos tfoot' ).empty();
      if (nu_tipo_impuesto == 1) {//CON IGV
        fila.find(".txt-Ss_Precio").val((parseFloat(total_producto / cantidad).toFixed(6)).toString().split(". "));
        fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat(total_producto / impuesto_producto).toFixed(6)).toString().split(". "));
        fila.find(".txt-fValorUnitario").val(parseFloat(fila.find(".txt-Ss_Precio").val() / impuesto_producto).toFixed(6));

        var $Ss_SubTotal = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_IGV = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleProductos > tbody > tr").each(function(){
          var rows = $(this);
          var Ss_Impuesto           = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
          var Nu_Tipo_Impuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
          var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());
          var $Ss_Total_Producto = parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());

          $Ss_Total += $Ss_Total_Producto;

          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
          
          if (Nu_Tipo_Impuesto == 1){
            $Ss_SubTotal += $Ss_SubTotal_Producto;
            $Ss_IGV += $Ss_Total_Producto - $Ss_SubTotal_Producto;
          }

          $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto))) / 100);
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
        fila.find(".txt-Ss_Precio").val((parseFloat((total_producto / cantidad) / impuesto_producto).toFixed(6)).toString().split(". "));
        fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat(total_producto / impuesto_producto).toFixed(2)).toString().split(". "));
        fila.find(".txt-fValorUnitario").val(parseFloat(fila.find(".txt-Ss_Precio").val()).toFixed(6));
        
        var $Ss_Inafecto = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleProductos > tbody > tr").each(function(){
          var rows = $(this);
          var Ss_Impuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
          var Nu_Tipo_Impuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
          var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());

          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
          
          if (Nu_Tipo_Impuesto == 2)
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

        $( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
      } else if (nu_tipo_impuesto == 3) {//Exonerada
        fila.find(".txt-Ss_Precio").val((parseFloat((total_producto / cantidad) / impuesto_producto).toFixed(6)).toString().split(". "));
        fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat(total_producto / impuesto_producto).toFixed(2)).toString().split(". "));
        fila.find(".txt-fValorUnitario").val(parseFloat(fila.find(".txt-Ss_Precio").val()).toFixed(6));
        
        var $Ss_Exonerada = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleProductos > tbody > tr").each(function(){
          var rows = $(this);
          var Ss_Impuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
          var Nu_Tipo_Impuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
          var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());

          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
          
          if (Nu_Tipo_Impuesto == 3)
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

        $( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
      } else if (nu_tipo_impuesto == 4) {//Gratuita
        fila.find(".txt-Ss_Precio").val((parseFloat((total_producto / cantidad) / impuesto_producto).toFixed(6)).toString().split(". "));
        fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat(total_producto / impuesto_producto).toFixed(2)).toString().split(". "));
        fila.find(".txt-fValorUnitario").val(parseFloat(fila.find(".txt-Ss_Precio").val()).toFixed(6));
        
        var $Ss_Gratuita = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleProductos > tbody > tr").each(function(){
          var rows = $(this);
          var Ss_Impuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
          var Nu_Tipo_Impuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
          var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());

          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
          
          if (Nu_Tipo_Impuesto == 4)
            $Ss_Gratuita += $Ss_SubTotal_Producto;

          $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto))) / 100);
          $Ss_Total += parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
        });
        
        $( '#txt-gratuita' ).val( $Ss_Gratuita.toFixed(2) );
    		$( '#span-gratuita' ).text( $Ss_Gratuita.toFixed(2) );
    		
    		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
    		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
    		
    		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
    		$( '#span-total' ).text( $Ss_Total.toFixed(2) );

        $( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
  		}
    }
  })
  
	$( '#table-DetalleProductos tbody' ).on('click', '#btn-deleteProducto', function(){
    $(this).closest('tr').remove ();
    
    var $Ss_Descuento = parseFloat($('#txt-Ss_Descuento').val());
    var $Ss_SubTotal = 0.00;
    var $Ss_Inafecto = 0.00;
    var $Ss_Exonerada = 0.00;
    var $Ss_Gratuita = 0.00;
    var $Ss_IGV = 0.00;
    var $Ss_Total = 0.00;
    var iCantDescuento = 0;
    var globalImpuesto = 0;
    var $Ss_Descuento_p = 0;
    $("#table-DetalleProductos > tbody > tr").each(function(){
      var rows = $(this);
      var fImpuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
      var iGrupoImpuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
      var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
      var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());
      var $Ss_Total_Producto = parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());

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

      $Ss_Descuento_p += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / fImpuesto))) / 100);
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

    if ($( '#table-DetalleProductos >tbody >tr' ).length == 0)
	      $( '#table-DetalleProductos' ).hide();
	})
	
  $('#table-CompraTotal' ).on('input', '#txt-Ss_Descuento', function(){
    var $Ss_Descuento_Producto = 0.00;
    $("#table-DetalleProductos > tbody > tr").each(function(){
      var rows = $(this);
      $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());
      
      if(isNaN($Ss_Descuento_Producto))
        $Ss_Descuento_Producto = 0;
      
      $Ss_Descuento_Producto += $Ss_Descuento_Producto;
    })
    
    if ($Ss_Descuento_Producto == 0) {
  		var $Ss_Descuento = parseFloat($(this).val());
      var $Ss_SubTotal = 0.00;
      var $Ss_Inafecto = 0.00;
      var $Ss_Exonerada = 0.00;
      var $Ss_Gratuita = 0.00;
      var $Ss_IGV = 0.00;
      var $Ss_Total = 0.00;
      var globalImpuesto = 0;
      $("#table-DetalleProductos > tbody > tr").each(function(){
        var rows = $(this);
        var fImpuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
        var iGrupoImpuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
        var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
        var $Ss_Total_Producto = parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());

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
  		$( '#span-total' ).text( $Ss_Total.toFixed(2) );

			$( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
    }
  })
})

function isExistTableTemporalProducto($ID_Producto){
  return Array.from($('tr[id*=tr_detalle_producto]'))
    .some(element => ($('td:nth(0)',$(element)).html()===$ID_Producto));
}

function form_Compra(){
  if (accion == 'add_factura_compra' || accion == 'upd_factura_compra') {//Accion para validar tecla ENTER
    var arrDetalleCompra = [];
    var arrValidarNumerosEnCero = [];
    var $counterNumerosEnCero = 0;
    var tr_foot = '';
    
    $("#table-DetalleProductos > tbody > tr").each(function(){
      var rows = $(this);

      var $ID_Producto = rows.find(".td-iIdItem").text();
      var $Qt_Producto = $('.txt-Qt_Producto', this).val();
      var $fValorUnitario = $('.txt-fValorUnitario', this).val();
      var $Ss_Precio = $('.txt-Ss_Precio', this).val();
      var $ID_Impuesto_Cruce_Documento = $('.cbo-ImpuestosProducto option:selected', this).val();
      var $Ss_SubTotal = $('.txt-Ss_SubTotal_Producto', this).val();
      var $Ss_Descuento = $('.txt-Ss_Descuento', this).val();
      var $Ss_Total = $('.txt-Ss_Total_Producto', this).val();
      var $Nu_Lote_Vencimiento = $('.txt-Nu_Lote_Vencimiento', this).val();
      var $Fe_Lote_Vencimiento = $('.txt-Fe_Lote_Vencimiento', this).val();
      var $fDescuentoSinImpuestosItem = rows.find(".td-fDescuentoSinImpuestosItem").text();
      var $fDescuentoImpuestosItem = rows.find(".td-fDescuentoImpuestosItem").text();
      
      if (parseFloat($Ss_Precio) == 0 || parseFloat($Qt_Producto) == 0 || parseFloat($Ss_Total) == 0){
        arrValidarNumerosEnCero[$counterNumerosEnCero] = $ID_Producto;
        $('#tr_detalle_producto' + $ID_Producto).addClass('danger');
      }
      
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
      obj.Nu_Lote_Vencimiento	= $Nu_Lote_Vencimiento;
      obj.Fe_Lote_Vencimiento = $Fe_Lote_Vencimiento;
      obj.fDescuentoSinImpuestosItem = $fDescuentoSinImpuestosItem;
      obj.fDescuentoImpuestosItem	= $fDescuentoImpuestosItem;
      arrDetalleCompra.push(obj);
      $counterNumerosEnCero++;
    });
    
    bEstadoValidacion = validatePreviousDocumentToSavePurchase();

    if ( arrDetalleCompra.length == 0){
  		$( '#panel-DetalleProductos' ).removeClass('panel-default');
  		$( '#panel-DetalleProductos' ).addClass('panel-danger');
  		
      $( '#txt-No_Producto' ).closest('.form-group').find('.help-block').html('Documento <b>sin detalle</b>');
  	  $( '#txt-No_Producto' ).closest('.form-group').removeClass('has-success').addClass('has-error');
  	  
		  scrollToError( $("html, body"), $( '#txt-No_Producto' ) );
    } else if ($('[name="radio-addDetraccion"]:checked').attr('value') == 1 && $( '[name="Nu_Detraccion"]' ).val().length === 0) {
      $( '#txt-Nu_Detraccion' ).closest('.form-group').find('.help-block').html('Ingresar numero');
  	  $( '#txt-Nu_Detraccion' ).closest('.form-group').removeClass('has-success').addClass('has-error');
  	  
		  scrollToError( $("html, body"), $( '#txt-Nu_Detraccion' ) );
    } else if (bEstadoValidacion) {
      $( '.div-mensaje_verificarExisteDocumento' ).text('');
      
  		$( '#panel-DetalleProductos' ).removeClass('panel-danger');
  		$( '#panel-DetalleProductos' ).addClass('panel-default');
      
  		var $Fe_Periodo = $( '#txt-Fe_Emision' ).val();
  		if ($( '#cbo-Periodo' ).val() == 1) //Soles
  		  $Fe_Periodo = $( '#txt-Fe_Periodo' ).val();  
  
      var $Fe_Detraccion = $( '#txt-Fe_Emision' ).val();
      var $Nu_Detraccion = '';
  		if ($('[name="radio-addDetraccion"]:checked').attr('value') == 1){//Si
  		  $Fe_Detraccion = $( '[name="Fe_Detraccion"]' ).val();
  		  $Nu_Detraccion = $( '[name="Nu_Detraccion"]' ).val();
  		}
  		
  		var arrCompraCabecera = Array();
  		arrCompraCabecera = {
  		  'esEnlace'                : nu_enlace,
  		  'EID_Empresa'             : $( '#txt-EID_Empresa' ).val(),
  		  'EID_Documento_Cabecera'  : $( '#txt-EID_Documento_Cabecera' ).val(),
  		  'ID_Entidad'              : $( '#txt-AID' ).val(),
  		  'ID_Tipo_Documento'       : $( '#cbo-TiposDocumento' ).val(),
  		  'ID_Serie_Documento'      : $( '#txt-ID_Serie_Documento' ).val(),
  		  'ID_Numero_Documento'     : $( '#txt-ID_Numero_Documento' ).val(),
  		  'Fe_Emision'              : $( '#txt-Fe_Emision' ).val(),
  		  'ID_Medio_Pago'           : $( '#cbo-MediosPago' ).val(),
  		  'ID_Moneda'               : $( '#cbo-Monedas' ).val(),
  		  'Fe_Vencimiento'          : $( '#txt-Fe_Vencimiento' ).val(),
  		  'Fe_Periodo'              : $Fe_Periodo,
  		  'Txt_Glosa'               : $( '[name="Txt_Glosa"]' ).val(),
  		  'Po_Descuento'            : $( '#txt-Ss_Descuento' ).val(),
  		  'Ss_Descuento'            : $( '#txt-descuento' ).val(),
  		  'Ss_Total'                : $( '#txt-total' ).val(),
  		  'Ss_Percepcion'           : $( '#txt-Ss_Percepcion' ).val(),
  		  'ID_Lista_Precio_Cabecera' : $( '#cbo-lista_precios' ).val(),
  		  'Fe_Detraccion'           : $Fe_Detraccion,
        'Nu_Detraccion': $Nu_Detraccion,
        'Nu_Descargar_Inventario': $('#cbo-descargar_stock').val(),
        'iTipoFormaPago': $('#cbo-MediosPago').find(':selected').data('nu_tipo'),
        'ID_Documento_Cabecera_Orden': $('[name="ID_Documento_Cabecera_Orden"]').val(),
        'iTipoCliente': $('[name="addProveedor"]:checked').attr('value'),
        'ID_Almacen': $('#cbo-almacen').val(),
        'ID_Tipo_Medio_Pago': $('#cbo-tarjeta_credito').val(),
        'Nu_Transaccion': $('#tel-nu_referencia').val(),
        'Nu_Tarjeta': $('#tel-nu_ultimo_4_digitos_tarjeta').val(),
  		};
  		
  		var arrCompraModificar = Array();
  		if (nu_enlace == 1) {
        arrCompraModificar = {
          ID_Documento_Guardado : $( '#txt-ID_Documento_Guardado' ).val(),
          ID_Tipo_Documento_Modificar : $( '#cbo-TiposDocumentoModificar' ).val(),
          ID_Serie_Documento_Modificar : $( '#txt-ID_Serie_Documento_Modificar' ).val(),
          ID_Numero_Documento_Modificar : $( '#txt-ID_Numero_Documento_Modificar' ).val(),
          iIdEntidad: $('#txt-AID').val(),
          iTipoCliente: $('[name="addProveedor"]:checked').attr('value'),
        };
  		}
  		
  		var arrProveedorNuevo = {};
  		if ($('[name="addProveedor"]:checked').attr('value') == 1){//Agregar proveedor
    		arrProveedorNuevo = {
    		  'ID_Tipo_Documento_Identidad' : $( '#cbo-TiposDocumentoIdentidadProveedor' ).val(),
    		  'Nu_Documento_Identidad'      : $( '#txt-Nu_Documento_Identidad_Proveedor' ).val(),
    		  'No_Entidad'                  : $( '#txt-No_Entidad_Proveedor' ).val(),
    		  'Txt_Direccion_Entidad'       : $( '#txt-Txt_Direccion_Entidad_Proveedor' ).val(),
    		  'Nu_Telefono_Entidad'         : $( '#txt-Nu_Telefono_Entidad_Proveedor' ).val(),
    		  'Nu_Celular_Entidad'          : $( '#txt-Nu_Celular_Entidad_Proveedor' ).val(),
        };
  		}
  		
      $( '#btn-save' ).text('');
      $( '#btn-save' ).attr('disabled', true);
      $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
    
      $( '#modal-loader' ).modal('show');

      $('#txt-AID_Doble').val('');
      $('#txt-Filtro_Entidad').val('');
      
      url = base_url + 'Logistica/CompraController/crudCompra';
    	$.ajax({
        type		  : 'POST',
        dataType	: 'JSON',
    		url		    : url,
    		data		  : {
    		  arrCompraCabecera   : arrCompraCabecera,
    		  arrDetalleCompra    : arrDetalleCompra,
    		  arrCompraModificar  : arrCompraModificar,
    		  arrProveedorNuevo   : arrProveedorNuevo,
    		},
    		success : function( response ){
    		  $( '#modal-loader' ).modal('hide');
    		  
    	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
      	  $( '#modal-message' ).modal('show');
      	  
    	    $( '.div-DocumentoModificar' ).removeClass('panel-warning panel-danger panel-success');
    	    $( '.div-mensaje_verificarExisteDocumento' ).removeClass('text-danger text-success');
    		  $( '.div-mensaje_verificarExisteDocumento' ).text('');

    		  if (response.status == 'success') {
    		    accion = '';

            $( '.div-AgregarEditar' ).hide();
            $( '.div-Listar' ).show();
      	    $( '.modal-message' ).addClass(response.style_modal);
      	    $( '.modal-title-message' ).text(response.message);
      	    setTimeout(function() {$('#modal-message').modal('hide'); }, 1100);
      	    
    		    $( '#form-Compra' )[0].reset();
      	    reload_table_compra();
    		  } else {
    		    if ( nu_enlace == 1 ){//Para notas de crédito y débito
      		    $( '.div-DocumentoModificar' ).addClass(response.style_panel);
      		    $( '.div-mensaje_verificarExisteDocumento' ).addClass(response.style_p);
      		    $( '.div-mensaje_verificarExisteDocumento' ).text(response.message);
    		    }
    		    
      	    $( '.modal-message' ).addClass(response.style_modal);
      	    $( '.modal-title-message' ).text(response.message);
      	    setTimeout(function() {$('#modal-message').modal('hide');}, 6200);
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

function reload_table_compra(){
  table_compra.ajax.reload(null,false);
}

//Detraccion
function addDetraccion(tipo){
  $( '#div-addDetraccion' ).hide();
  if (tipo == 1)
    $( '#div-addDetraccion' ).show();
}

function _anularCompra($modal_delete, ID, Nu_Enlace, Nu_Descargar_Inventario){
  $( '#modal-loader' ).modal('show');
    
  url = base_url + 'Logistica/CompraController/anularCompra/' + ID + '/' + Nu_Enlace + '/' + Nu_Descargar_Inventario;
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
  	    reload_table_compra();
		  } else {
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
		  }
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

function _eliminarCompra($modal_delete, ID, Nu_Enlace, Nu_Descargar_Inventario){
  $( '#modal-loader' ).modal('show');
    
  url = base_url + 'Logistica/CompraController/eliminarCompra/' + ID + '/' + Nu_Enlace + '/' + Nu_Descargar_Inventario;
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
  	    reload_table_compra();
		  } else {
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
		  }
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

function validatePreviousDocumentToSavePurchase(){
	var bEstadoValidacion = true;
	
  if ( $( '#cbo-TiposDocumento' ).val() == 0 ){
    $( '#cbo-TiposDocumento' ).closest('.form-group').find('.help-block').html('Seleccionar documento');
    $( '#cbo-TiposDocumento' ).closest('.form-group').removeClass('has-success').addClass('has-error');
	  
	  bEstadoValidacion = false;
		scrollToError( $("html, body"), $( '#cbo-TiposDocumento' ) );
  } else if ( $( '#txt-ID_Serie_Documento' ).val().length === 0){
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
  } else if ($('[name="ID_Documento_Cabecera_Orden"]').val().length == 0 && ($('#cbo-TiposDocumento').val() == 5 || $('#cbo-TiposDocumento').val() == 6) && nu_enlace == 1 && $( '#cbo-TiposDocumentoModificar' ).val() == 0){
    $( '#cbo-TiposDocumentoModificar' ).closest('.form-group').find('.help-block').html('Seleccionar documento');
    $( '#cbo-TiposDocumentoModificar' ).closest('.form-group').removeClass('has-success').addClass('has-error');

    bEstadoValidacion = false;
		scrollToError( $("html, body"), $( '#cbo-TiposDocumentoModificar' ) );
  } else if ($('[name="ID_Documento_Cabecera_Orden"]').val().length == 0 && ($('#cbo-TiposDocumento').val() == 5 || $('#cbo-TiposDocumento').val() == 6) && nu_enlace == 1 && $( '#txt-ID_Serie_Documento_Modificar' ).val() == 0){
    $( '#txt-ID_Serie_Documento_Modificar' ).closest('.form-group').find('.help-block').html('Ingresar serie');
    $( '#txt-ID_Serie_Documento_Modificar' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    
    bEstadoValidacion = false;
		scrollToError( $("html, body"), $( '#txt-ID_Serie_Documento_Modificar' ) );
  } else if ($('[name="ID_Documento_Cabecera_Orden"]').val().length == 0 && ($('#cbo-TiposDocumento').val() == 5 || $('#cbo-TiposDocumento').val() == 6) && nu_enlace == 1 && $( '#txt-ID_Numero_Documento_Modificar' ).val().length === 0){
    $( '#txt-ID_Numero_Documento_Modificar' ).closest('.form-group').find('.help-block').html('Ingresar numero');
    $( '#txt-ID_Numero_Documento_Modificar' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    
    bEstadoValidacion = false;
		scrollToError( $("html, body"), $( '#txt-ID_Numero_Documento_Modificar' ) );
  } else if ( $('[name="addProveedor"]:checked').attr('value') == 1 && $( '#cbo-Estado' ).val() == 0 ) {
    $( '#modal-message' ).modal('show');
    $( '.modal-message' ).addClass('modal-danger');
    $( '.modal-title-message' ).text( 'El proveedor se encuentra con BAJA DE OFICIO / NO HABIDO' );
    setTimeout(function() {$('#modal-message').modal('hide');}, 2500);
		
    bEstadoValidacion = false;
  }
  return bEstadoValidacion;
}

function generarTablaTemporalItems($ID_Producto, $No_Producto, $Ss_Precio, $ID_Impuesto_Cruce_Documento, $Nu_Tipo_Impuesto, $Ss_Impuesto, $No_Unidad_Medida, arrDataAdicionalTmpDetalleItem){
  var _ID_Producto = '', option_impuesto_producto = '', $Ss_SubTotal_Producto = 0.00, $Ss_Total_Producto = 0.00;
  
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
  
  var sVarianteMultipleTmp='';
  sVarianteMultipleTmp += ($('#hidden-iTipoRubroEmpresa').val()==6 ? (arrDataAdicionalTmpDetalleItem.no_variante_1 !== undefined && arrDataAdicionalTmpDetalleItem.no_variante_1 !== null && arrDataAdicionalTmpDetalleItem.no_variante_1 !== '' ? '<br>' + arrDataAdicionalTmpDetalleItem.no_variante_1 + ': ' + arrDataAdicionalTmpDetalleItem.no_valor_variante_1 : '') : '');
  sVarianteMultipleTmp += ($('#hidden-iTipoRubroEmpresa').val()==6 ? (arrDataAdicionalTmpDetalleItem.no_variante_2 !== undefined && arrDataAdicionalTmpDetalleItem.no_variante_2 !== null && arrDataAdicionalTmpDetalleItem.no_variante_2 !== '' ? '<br>' + arrDataAdicionalTmpDetalleItem.no_variante_2 + ': ' + arrDataAdicionalTmpDetalleItem.no_valor_variante_2 : '') : '');
  sVarianteMultipleTmp += ($('#hidden-iTipoRubroEmpresa').val()==6 ? (arrDataAdicionalTmpDetalleItem.no_variante_3 !== undefined && arrDataAdicionalTmpDetalleItem.no_variante_3 !== null && arrDataAdicionalTmpDetalleItem.no_variante_3 !== '' ? '<br>' + arrDataAdicionalTmpDetalleItem.no_variante_3 + ': ' + arrDataAdicionalTmpDetalleItem.no_valor_variante_3 : '') : '');

  var table_detalle_producto =
  "<tr id='tr_detalle_producto" + $ID_Producto + "'>"
    +"<td style='display:none;' class='text-left td-iIdItem'>" + $ID_Producto + "</td>"
    +"<td class='text-right'><input type='text' inputmode='decimal' id=" + $ID_Producto + " class='pos-input txt-Qt_Producto form-control input-decimal input-size_cantidad hotkey-focus_item' data-id_producto='" + $ID_Producto + "' value='1' autocomplete='off'></td>"
    +"<td class='text-left'>"
      + '<span style="font-size: 13px;font-weight:bold;">' + $No_Producto + '</span>'
      + sVarianteMultipleTmp
      + ($No_Unidad_Medida !== undefined && $No_Unidad_Medida !== null && $No_Unidad_Medida != '' ? ' <br><span style="font-size: 10px;font-weight:normal;">[' + $No_Unidad_Medida + ']</span> ' : '')
    +"</td>"
    +"<td style='display:none;' class='text-right'><input type='text' class='pos-input txt-fValorUnitario form-control input-decimal input-size_importe' data-id_producto='" + $ID_Producto + "' value='" + $Ss_SubTotal_Producto.toFixed(2) + "' autocomplete='off'></td>"
    +"<td class='text-right'><input type='text' inputmode='decimal' class='pos-input txt-Ss_Precio form-control input-decimal input-size_importe' data-id_producto='" + $ID_Producto + "' value='" + $Ss_Precio + "' autocomplete='off'></td>"
    +"<td class='text-right'>"
      +"<select class='cbo-ImpuestosProducto form-control required' style='width: 100%;'>"
        +option_impuesto_producto
      +"</select>"
    +"</td>"
    +"<td style='display:none;' class='text-right'><input type='text' inputmode='decimal' class='pos-input txt-Ss_SubTotal_Producto form-control input-size_importe' value='" + $Ss_SubTotal_Producto.toFixed(2) + "' autocomplete='off' disabled></td>"
    +"<td class='text-right'><input type='text' inputmode='decimal' class='pos-input txt-Ss_Descuento form-control input-decimal input-size_otros' data-id_producto='" + $ID_Producto + "' value='' autocomplete='off'></td>"
    +"<td class='text-right'><input type='text' inputmode='decimal' class='pos-input txt-Ss_Total_Producto form-control input-decimal input-size_importe' data-id_producto='" + $ID_Producto + "' value='" + $Ss_Total_Producto.toFixed(2) + "' autocomplete='off'></td>"
    +"<td class='text-right'><input type='text' inputmode='numeric' class='pos-input txt-Nu_Lote_Vencimiento form-control input-codigo_barra' placeholder='Opcional' data-id_producto='" + $ID_Producto + "' value='' autocomplete='off'></td>"
    +"<td class='text-right'><input type='text' class='pos-input txt-Fe_Lote_Vencimiento form-control date-picker-invoice' placeholder='Opcional' data-id_producto='" + $ID_Producto + "' value='' autocomplete='off'></td>"
    +"<td style='display:none;' class='text-right td-fDescuentoSinImpuestosItem'>0.00</td>"
    +"<td style='display:none;' class='text-right td-fDescuentoImpuestosItem'>0.00</td>"
    +"<td class='text-center'><button type='button' id='btn-deleteProducto' class='btn btn-sm btn-link' alt='Eliminar' title='Eliminar'><i class='fa fa-trash-o fa-2x' aria-hidden='true'> </i></button></td>"
  + "</tr>";
  
  if( isExistTableTemporalProducto($ID_Producto) ){
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
		
    $( '#table-DetalleProductos' ).show();
	  $( '#table-DetalleProductos >tbody' ).append(table_detalle_producto);
	  
    $( '.txt-Fe_Lote_Vencimiento' ).datepicker({
      autoclose : true,
      startDate : new Date(fYear, fToday.getMonth(), fDay),
      todayHighlight: true
    })

		$( '#' + $ID_Producto ).focus();
	  $( '#' + $ID_Producto ).select();
		
		var $Ss_Descuento = parseFloat($('#txt-Ss_Descuento').val());
    var $Ss_SubTotal = 0.00;
    var $Ss_Inafecto = 0.00;
    var $Ss_Exonerada = 0.00;
    var $Ss_Gratuita = 0.00;
    var $Ss_IGV = 0.00;
    var $Ss_Total = 0.00;
    var iCantDescuento = 0;
    var globalImpuesto = 0;
    var $Ss_Descuento_p = 0;
    $("#table-DetalleProductos > tbody > tr").each(function(){
      var rows = $(this);
      var fImpuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
      var iGrupoImpuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
      var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_Total_Producto', this).val() / fImpuesto);
      var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());
      var $Ss_Total_Producto = parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());

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
        
      $Ss_Descuento_p += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / fImpuesto))) / 100);
    });
    
    if ($Ss_SubTotal > 0.00 || $Ss_Inafecto > 0.00 || $Ss_Exonerada > 0.00) {
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
    validateCodigoBarra();
  }
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
      $('#cbo-lista_precios').append( '<option value="' + responseListaPrecio[0].ID_Lista_Precio_Cabecera + '">' + responseListaPrecio[0].No_Lista_Precio + '</option>' );
    } else if ( iCantidadRegistrosListaPrecios > 1 ) {
      $( '#cbo-lista_precios' ).html( '<option value="0">- Seleccionar -</option>');
      for (var i = 0; i < iCantidadRegistrosListaPrecios; i++)
        $( '#cbo-lista_precios' ).append( '<option value="' + responseListaPrecio[i].ID_Lista_Precio_Cabecera + '">' + responseListaPrecio[i].No_Lista_Precio + '</option>' );
    } else {
      $( '#cbo-lista_precios' ).html( '<option value="0">- Sin lista precio -</option>');
    }
  }, 'JSON');
}

function pagarProveedor(ID_Documento_Cabecera, Ss_Total_Saldo, No_Entidad, No_Tipo_Documento_Breve, ID_Serie_Documento, ID_Numero_Documento, sSignoMoneda) {
  arrParams = {
    'iIdDocumentoCabecera': ID_Documento_Cabecera,
    'fTotalSaldo': Ss_Total_Saldo,
    'sProveedor': No_Entidad,
    'sTipoDocumento': No_Tipo_Documento_Breve,
    'sSerieDocumento': ID_Serie_Documento,
    'sNumeroDocumento': ID_Numero_Documento,
    'sSignoMoneda': sSignoMoneda,
  }

  $('#form-cobrar_proveedor')[0].reset();
  $('.form-group').removeClass('has-error');
  $('.form-group').removeClass('has-success');
  $('.help-block').empty();

  $('.div-forma_pago').hide();
  $('.div-modal_datos_tarjeta_credito').hide();
  $('.div-estado_lavado_recepcion_proveedor').hide();
  $('.div-recibe_otra_persona').hide();

  $('.modal-cobrar_proveedor').modal('show');

  $('.date-picker-invoice').val(fDay + '/' + fMonth + '/' + fYear);
  $('[name="iIdDocumentoCabecera"]').val(arrParams.iIdDocumentoCabecera);

  $('#hidden-cobrar_proveedor-fsaldo').val(arrParams.fTotalSaldo);

  $('#modal-header-cobrar_proveedor-title').text(arrParams.sTipoDocumento + ' - ' + arrParams.sSerieDocumento + ' - ' + arrParams.sNumeroDocumento);
  $('#cobrar_proveedor-modal-body-proveedor').text('Proveedor: ' + arrParams.sProveedor);
  $('#cobrar_proveedor-modal-body-saldo_proveedor').text('Saldo: ' + sSignoMoneda + ' ' + arrParams.fTotalSaldo);

  $('.div-forma_pago').show();
  $('.modal-cobrar_proveedor').on('shown.bs.modal', function () {
    $('[name="fPagoProveedor"]').focus();
    $('[name="fPagoProveedor"]').val(arrParams.fTotalSaldo);
  });

  url = base_url + 'HelperController/getMediosPago';
  var arrPost = {
    iIdEmpresa: arrParams.iIdEmpresa,
  };
  $.post(url, arrPost, function (response) {
    $('#cbo-modal_forma_pago').html('');
    for (var i = 0; i < response.length; i++) {
      if (response[i].Nu_Tipo != 1)
        $('#cbo-modal_forma_pago').append('<option value="' + response[i].ID_Medio_Pago + '" data-nu_tipo_medio_pago="' + response[i].Nu_Tipo + '">' + response[i].No_Medio_Pago + '</option>');
    }
  }, 'JSON');

  // Modal de cobranza al proveedor
  $('#cbo-modal_forma_pago').change(function () {
    ID_Medio_Pago = $(this).val();
    Nu_Tipo_Medio_Pago = $(this).find(':selected').data('nu_tipo_medio_pago');
    $('.div-modal_datos_tarjeta_credito').hide();
    $('#cbo-cobrar_proveedor-modal_tarjeta_credito').html('');
    $('#tel-nu_referencia').val('');
    $('#tel-nu_ultimo_4_digitos_tarjeta').val('');
    if (Nu_Tipo_Medio_Pago == 2) {
      $('.div-modal_datos_tarjeta_credito').show();

      url = base_url + 'HelperController/getTiposTarjetaCredito';
      $.post(url, { ID_Medio_Pago: ID_Medio_Pago }, function (response) {
        $('#cbo-cobrar_proveedor-modal_tarjeta_credito').html('');
        for (var i = 0; i < response.length; i++)
          $('#cbo-cobrar_proveedor-modal_tarjeta_credito').append('<option value="' + response[i].ID_Tipo_Medio_Pago + '">' + response[i].No_Tipo_Medio_Pago + '</option>');
      }, 'JSON');
    }

    setTimeout(function () { $('[name="fPagoProveedor"]').focus(); $('[name="fPagoProveedor"]').select(); }, 20);
  })

  $('#cbo-estado_lavado_recepcion_proveedor').change(function () {
    $('.div-estado_lavado_recepcion_proveedor').hide();
    if ($(this).val() == 3) {
      $('.div-estado_lavado_recepcion_proveedor').show();
    }
  })

  $('#cbo-modal_quien_recibe').change(function () {
    $('.div-recibe_otra_persona').hide();
    if ($(this).val() == 0) {
      $('.div-recibe_otra_persona').show();
      $('[name="sNombreRecepcion"]').focus();
    }
  })
}