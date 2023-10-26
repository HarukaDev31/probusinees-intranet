var url;
var table_venta;
var considerar_igv;
var nu_enlace;
var value_importes_cero = 0.00;
var texto_importes_cero = '0.00';
var arrImpuestosProducto = '{ "arrImpuesto" : [';
var arrImpuestosProductoDetalle, accion = '', bEstadoValidacion, isLoading = false;

$('.date-picker-invoice').val(fDay + '/' + fMonth + '/' + fYear);
$('#txt-Filtro_Fe_Inicio').val(fDay + '/' + fMonth + '/' + fYear);

function agregarVenta(){
  accion = 'add_factura_venta';
  $( '#modal-loader' ).modal('show');

  $(".clearable__clear").toggle(false);

  $('.modal-adicionales').modal('hide');
  $('.modal-guias_remision').modal('hide');

  $( '.div-Listar' ).hide();
  $( '.div-AgregarEditar' ).show();
  
	$( '#txt-EID_Empresa' ).focus();
  
  $( '#form-Venta' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '.title_Venta' ).text('Nuevo Venta');
  
  $('[name="EID_Empresa"]').val('');
  $('[name="EID_Documento_Cabecera"]').val('');
  
  $('.div-actualizar_datos_cliente').hide();

  $( '.date-picker-invoice' ).val(fDay + '/' + fMonth + '/' + fYear);
  
  $('.panel_body_total_todo').css("display", "none");
  $('#btn-ver_total_todo').data('ver_total_todo', 0);
  $('#btn-ver_total_todo').text('VER / DESCUENTO');

	$( '#cbo-TiposDocumentoModificar' ).html('');
	$( '#cbo-SeriesDocumentoModificar' ).html('');
	$( '#cbo-MotivoReferenciaModificar' ).html('');
		
	$( '#txt-AID' ).val( '' );
	$( '#radio-cliente_varios' ).prop('checked', false).iCheck('update');
  $( '#radio-cliente_existente' ).prop('checked', true).iCheck('update');
  $( '#radio-cliente_nuevo' ).prop('checked', false).iCheck('update');
  
  $( '.div-cliente_existente' ).show();
  $( '.div-cliente_nuevo' ).hide();
  
  $( '#txt-ID_Documento_Guardado' ).val(0);
  $( '.div-DocumentoModificar' ).removeClass('panel-warning panel-danger panel-success');
  $( '.div-mensaje_verificarExisteDocumento' ).removeClass('text-danger text-success');
  $( '.div-mensaje_verificarExisteDocumento' ).text('');
  $( '.div-DocumentoModificar' ).addClass('panel-default');

  $('#table-DetalleProductosVenta tbody').empty();
  $('#table-DetalleProductosVenta tfoot').empty();
	
	$( '#panel-DetalleProductosVenta' ).removeClass('panel-danger');
	$( '#panel-DetalleProductosVenta' ).addClass('panel-default');
	
  $( '.div-MediosPago' ).hide();
  
  $('.div-detraccion').hide();

  $('#radio-InactiveDetraccion').prop('checked', true).iCheck('update');
  $('#radio-ActiveDetraccion').prop('checked', false).iCheck('update');

  $('#radio-InactiveRetencion').prop('checked', true).iCheck('update');
  $('#radio-ActiveRetencion').prop('checked', false).iCheck('update');
  
	$( '#txt-subTotal' ).val( value_importes_cero );
	$( '#span-subTotal' ).text( texto_importes_cero );
	
	$( '#txt-exonerada' ).val( value_importes_cero );
	$( '#span-exonerada' ).text( texto_importes_cero );
	
	$( '#txt-inafecto' ).val( value_importes_cero );
	$( '#span-inafecto' ).text( texto_importes_cero );
	
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

  considerar_igv=0;
  
  url = base_url + 'HelperController/getTiposDocumentos';
  $.post( url, {Nu_Tipo_Filtro : 3}, function( response ){//1 = Venta
    $( '#cbo-TiposDocumento' ).html('<option value="" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++) {
      if( $('#hidden-Nu_Tipo_Proveedor_FE').val()!=3) {
        $( '#cbo-TiposDocumento' ).append( '<option value="' + response[i].ID_Tipo_Documento + '" data-nu_impuesto="' + response[i].Nu_Impuesto + '" data-nu_enlace="' + response[i].Nu_Enlace + '">' + response[i].No_Tipo_Documento_Breve + '</option>' );
      }
      if( $('#hidden-Nu_Tipo_Proveedor_FE').val()==3 && response[i].ID_Tipo_Documento == 2) {
        $( '#cbo-TiposDocumento' ).append( '<option value="' + response[i].ID_Tipo_Documento + '" data-nu_impuesto="' + response[i].Nu_Impuesto + '" data-nu_enlace="' + response[i].Nu_Enlace + '">' + response[i].No_Tipo_Documento_Breve + '</option>' );
      }
    }
  }, 'JSON');

  url = base_url + 'HelperController/getTiposDocumentoIdentidad';
  $.post( url , function( response ){
    $( '#cbo-TiposDocumentoIdentidadCliente' ).html('');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-TiposDocumentoIdentidadCliente' ).append( '<option value="' + response[i]['ID_Tipo_Documento_Identidad'] + '" data-nu_cantidad_caracteres="' + response[i]['Nu_Cantidad_Caracteres'] + '">' + response[i]['No_Tipo_Documento_Identidad_Breve'] + '</option>' );
  }, 'JSON');
  
	// Tipos de comprobantes
  $('.div-DocumentoModificar').hide();
  $('#div-cliente_rapido').show();
  $('#cbo-TiposDocumento').change(function () {
    $('#div-cliente_rapido').show();
    //$("#div-cliente_rapido").css("visibility", "visible");

    if( $(this).val() == 3 ) {
      $('#div-cliente_rapido').hide();
      //$("#div-cliente_rapido").css("visibility", "hidden");

      $('#radio-cliente_varios').prop('checked', false).iCheck('update');
      $('#radio-cliente_existente').prop('checked', true).iCheck('update');
      $('#radio-cliente_nuevo').prop('checked', false).iCheck('update');
      $('.div-cliente_existente').show();
      $('.div-cliente_nuevo').hide();
    }

    if ( $( '#cbo-almacen' ).val() > 0 ) {
      $( '#cbo-SeriesDocumento' ).html('');
      $( '.div-DocumentoModificar' ).hide();
      if ( $(this).val() > 0 ) {
        considerar_igv = $(this).find(':selected').data('nu_impuesto');
        nu_enlace = $(this).find(':selected').data('nu_enlace');
        if (nu_enlace == 1) {//Validar N/C y N/D
          $( '.div-DocumentoModificar' ).show();

          url = base_url + 'HelperController/getTiposDocumentosModificar';
          $.post( url, {Nu_Tipo_Filtro : 1}, function( response ){
            $( '#cbo-TiposDocumentoModificar' ).html('<option value="" selected="selected">- Seleccionar -</option>');
            for (var i = 0; i < response.length; i++)
              $( '#cbo-TiposDocumentoModificar' ).append( '<option value="' + response[i].ID_Tipo_Documento + '">' + response[i].No_Tipo_Documento_Breve + '</option>' );
          }, 'JSON');
          
          //Motivos de referencia  
          url = base_url + 'HelperController/getMotivosReferenciaModificar';
          $.post( url, {ID_Tipo_Documento: $(this).val()}, function( response ){
            $( '#cbo-MotivoReferenciaModificar' ).html('');
            for (var i = 0; i < response.length; i++)
              $( '#cbo-MotivoReferenciaModificar' ).append( '<option value="' + response[i].Nu_Valor + '">' + response[i].No_Descripcion + '</option>' );
          }, 'JSON');
        }// /. Validación de N/C y N/D

        url = base_url + 'HelperController/getSeriesDocumentoxAlmacen';
        $.post( url, {ID_Organizacion : $( '#header-a-id_organizacion' ).val(), ID_Almacen : $( '#cbo-almacen' ).val(), ID_Tipo_Documento: $(this).val()}, function( response ){
          if (response.length === 1) {
            $( '#cbo-SeriesDocumento' ).html( '<option value="' + response[0].ID_Serie_Documento + '" data-id_serie_documento_pk=' + response[0].ID_Serie_Documento_PK + '>' + response[0].ID_Serie_Documento + '</option>' );	    
          } else if (response.length > 1) {
            $( '#cbo-SeriesDocumento' ).html('<option value="" selected="selected">- Seleccionar -</option>');
            for (var i = 0; i < response.length; i++)
              $( '#cbo-SeriesDocumento' ).append( '<option value="' + response[i].ID_Serie_Documento + '" data-id_serie_documento_pk=' + response[i].ID_Serie_Documento_PK + '>' + response[i].ID_Serie_Documento + '</option>' );
          } else
            $( '#cbo-SeriesDocumento' ).html('<option value="" selected="selected">Sin serie</option>');
        }, 'JSON');
        
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
        $("#table-DetalleProductosVenta > tbody > tr").each(function(){
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
            
          $Ss_Descuento_p += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / fImpuesto) )) / 100);
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
        
        $( '#txt-gratuita' ).val( $Ss_Inafecto.toFixed(2) );
        $( '#span-gratuita' ).text( $Ss_Inafecto.toFixed(2) );
          
        $( '#txt-impuesto' ).val( $Ss_IGV.toFixed(2) );
        $( '#span-impuesto' ).text( $Ss_IGV.toFixed(2) );
        
        $( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
        $( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
    
        $( '#txt-total' ).val( $Ss_Total.toFixed(2) );
        $( '#span-total' ).text( $Ss_Total.toFixed(2) );
        $( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
      }
    } else {
      $( '#cbo-almacen' ).closest('.form-group').find('.help-block').html('Seleccionar almacén');
  	  $( '#cbo-almacen' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    }// /. if - else validacion de seleccionar almacen
	})
	// /. Tipos de comprobantes
	
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
  });

  var Fe_Emision = $( '#txt-Fe_Emision' ).val().split('/');
  $( '#txt-Fe_Vencimiento' ).datepicker({
    autoclose : true,
    startDate : new Date(Fe_Emision[2], Fe_Emision[1] - 1, Fe_Emision[0]),
    todayHighlight: true
  })
  
  $( '#txt-Fe_Vencimiento' ).val($( '#txt-Fe_Emision' ).val());
  
	/* Sunat tipo de operacion */
  $( '#cbo-sunat_tipo_transaction' ).html('<option value="1" selected="selected">VENTA INTERNA</option>');
  url = base_url + 'HelperController/getSunatTipoOperacion';
  $.post(url, {}, function (response) {
    if (response.sStatus == 'success') {
      $('#cbo-sunat_tipo_transaction').html('');
      var l = response.arrData.length;
      for (var x = 0; x < l; x++) {
        $('#cbo-sunat_tipo_transaction').append('<option value="' + response.arrData[x].ID + '">' + response.arrData[x].Nombre + '</option>' );
      }
    } else {
      if( response.sMessageSQL !== undefined ) {
        console.log(response.sMessageSQL);
      }
      console.log(response.sMessage);
    }
  }, 'JSON');
  /* /. Sunat tipo de operacion */

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
	
  $( '#cbo-descargar_stock' ).html( '<option value="1">Si</option>' );
  $( '#cbo-descargar_stock' ).append( '<option value="0">No</option>' );

  var arrParams = {
    ID_Almacen: $('#cbo-almacen').val(),
  };
  getListaPrecios(arrParams);
  $( '.div-almacen' ).show();

  $( '#table-DetalleProductosVenta' ).hide();
  
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

  //CSS
  $("#cbo-TiposDocumento").css("background-color", "");
  $("#cbo-TiposDocumento").css("pointer-events", "");
  
  // Obtener canales de venta
  url = base_url + 'HelperController/getCanalesVenta';
  $.post(url, function (response) {
    if (response.sStatus == 'success') {
      var l = response.arrData.length;
      if (l == 1) {
        $('#cbo-canal_venta').html('<option value="' + response.arrData[0].ID + '">' + response.arrData[0].Nombre + '</option>');
      } else {
        $('#cbo-canal_venta').html('<option value="0" selected="selected">- Seleccionar -</option>');
        for (var x = 0; x < l; x++) {
          $('#cbo-canal_venta').append('<option value="' + response.arrData[x].ID + '">' + response.arrData[x].Nombre + '</option>');
        }
      }
    } else {
      if (response.sMessageSQL !== undefined) {
        console.log(response.sMessageSQL);
      }
    }
  }, 'JSON');

  $('.div-fecha_entrega').hide();

  $('#cbo-recepcion').html('<option value="5" selected="selected">Tienda</option>');
  $('#cbo-recepcion').append('<option value="6">Delivery</option>');
  $('#cbo-recepcion').append('<option value="7">Recojo en Tienda</option>');

  $('.input-datepicker-today-to-more').inputmask('dd/mm/yyyy', { 'placeholder': 'dd/mm/yyyy' });
  $('.input-datepicker-today-to-more').val(fDay + '/' + fMonth + '/' + fYear);

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
}

function verFacturaVenta(ID, Nu_Documento_Identidad){
  accion = 'upd_factura_venta';
  $( '#modal-loader' ).modal('show');
  
  $( '.div-Listar' ).hide();

	$( '#txt-EID_Empresa' ).focus();
  
  $('.modal-adicionales').modal('hide');
  $('.modal-guias_remision').modal('hide');  

  $( '#form-Venta' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $('.div-actualizar_datos_cliente').hide();

  $('.panel_body_total_todo').css("display", "none");
  $('#btn-ver_total_todo').data('ver_total_todo', 0);
  $('#btn-ver_total_todo').text('VER / DESCUENTO');

  $( '#txt-AID' ).val( '' );
  $( '#radio-cliente_varios' ).prop('checked', false).iCheck('update');
  $( '#radio-cliente_existente' ).prop('checked', true).iCheck('update');
  $( '#radio-cliente_nuevo' ).prop('checked', false).iCheck('update');
  
  $( '.div-cliente_existente' ).show();
  $( '.div-cliente_nuevo' ).hide();
  
	$( '.div-DocumentoModificar' ).hide();
 
  $('.div-detraccion').hide();

  $( '#txt-ID_Documento_Guardado' ).val(1);
  $( '.div-DocumentoModificar' ).removeClass('panel-warning panel-danger panel-success');
  $( '.div-mensaje_verificarExisteDocumento' ).removeClass('text-danger text-success');
  $( '.div-mensaje_verificarExisteDocumento' ).text('');
  $( '.div-DocumentoModificar' ).addClass('panel-default');
 
	$( '#panel-DetalleProductosVenta' ).removeClass('panel-danger');
	$( '#panel-DetalleProductosVenta' ).addClass('panel-default');
  
	$( '#txt-subTotal' ).val( value_importes_cero );
	$( '#span-subTotal' ).text( texto_importes_cero );
	
	$( '#txt-exonerada' ).val( value_importes_cero );
	$( '#span-exonerada' ).text( texto_importes_cero );
	
	$( '#txt-inafecto' ).val( value_importes_cero );
	$( '#span-inafecto' ).text( texto_importes_cero );
	
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
  
  url = base_url + 'HelperController/getTiposDocumentoIdentidad';
  $.post( url , function( response ){
    $( '#cbo-TiposDocumentoIdentidadCliente' ).html('');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-TiposDocumentoIdentidadCliente' ).append( '<option value="' + response[i]['ID_Tipo_Documento_Identidad'] + '" data-nu_cantidad_caracteres="' + response[i]['Nu_Cantidad_Caracteres'] + '">' + response[i]['No_Tipo_Documento_Identidad_Breve'] + '</option>' );
  }, 'JSON');

  $('#div-cliente_rapido').show();
  $('#cbo-TiposDocumento').change(function () {
    $('#div-cliente_rapido').show();
    if ($(this).val() == 3)
      $('#div-cliente_rapido').hide();

    if ( $( '#cbo-almacen' ).val() > 0 ) {
      $( '#cbo-SeriesDocumento' ).html('');
      $( '.div-DocumentoModificar' ).hide();
      if ( $(this).val() > 0 ) {
        considerar_igv = $(this).find(':selected').data('nu_impuesto');
        nu_enlace = $(this).find(':selected').data('nu_enlace');
        if (nu_enlace == 1) {//Validar N/C y N/D
          $( '.div-DocumentoModificar' ).show();

          url = base_url + 'HelperController/getTiposDocumentosModificar';
          $.post( url, {Nu_Tipo_Filtro : 1}, function( response ){
            $( '#cbo-TiposDocumentoModificar' ).html('<option value="" selected="selected">- Seleccionar -</option>');
            for (var i = 0; i < response.length; i++)
              $( '#cbo-TiposDocumentoModificar' ).append( '<option value="' + response[i]['ID_Tipo_Documento'] + '">' + response[i]['No_Tipo_Documento_Breve'] + '</option>' );
          }, 'JSON');
          
          //Motivos de referencia  
          url = base_url + 'HelperController/getMotivosReferenciaModificar';
          $.post( url, {ID_Tipo_Documento: $(this).val()}, function( response ){
            $( '#cbo-MotivoReferenciaModificar' ).html('');
            for (var i = 0; i < response.length; i++)
              $( '#cbo-MotivoReferenciaModificar' ).append( '<option value="' + response[i].Nu_Valor + '">' + response[i].No_Descripcion + '</option>' );
          }, 'JSON');
        }// /. Validación de N/C y N/D

        url = base_url + 'HelperController/getSeriesDocumentoxAlmacen';
        $.post( url, {ID_Organizacion : $( '#header-a-id_organizacion' ).val(), ID_Almacen : $( '#cbo-almacen' ).val(), ID_Tipo_Documento: $(this).val()}, function( response ){
          if (response.length === 1) {
            $( '#cbo-SeriesDocumento' ).html( '<option value="' + response[0].ID_Serie_Documento + '" data-id_serie_documento_pk=' + response[0].ID_Serie_Documento_PK + '>' + response[0].ID_Serie_Documento + '</option>' );
          } else if (response.length > 1) {
            $( '#cbo-SeriesDocumento' ).html('<option value="" selected="selected">- Seleccionar -</option>');
            for (var i = 0; i < response.length; i++)
              $( '#cbo-SeriesDocumento' ).append( '<option value="' + response[i].ID_Serie_Documento + '"data-id_serie_documento_pk=' + response[i].ID_Serie_Documento_PK + '>' + response[i].ID_Serie_Documento + '</option>' );
          } else
            $( '#cbo-SeriesDocumento' ).html('<option value="" selected="selected">Sin serie</option>');
        }, 'JSON');
        
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
        $("#table-DetalleProductosVenta > tbody > tr").each(function(){
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
            
          $Ss_Descuento_p += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / fImpuesto) )) / 100);
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
        $( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
      }
    } else {
      $( '#cbo-almacen' ).closest('.form-group').find('.help-block').html('Seleccionar almacén');
      $( '#cbo-almacen' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    }// /. if - else validacion de seleccionar almacen
	})
	
  //CSS
  $("#cbo-TiposDocumento").css("background-color", "#d2d6de");
  $("#cbo-TiposDocumento").css("pointer-events", "none");

  url = base_url + 'Ventas/VentaController/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $( '.div-AgregarEditar' ).show();
      
      $( '.title_Venta' ).text('Modifcar Venta');
      
      $('[name="EID_Empresa"]').val(response.arrEdit[0].ID_Empresa);
      $('[name="EID_Documento_Cabecera"]').val(response.arrEdit[0].ID_Documento_Cabecera);
      
      nu_enlace = response.arrEdit[0].Nu_Enlace;
      $('[name="ID_Documento_Cabecera_Orden"]').val(response.arrEdit[0].ID_Documento_Cabecera_Enlace);
      if (response.arrEdit[0].ID_Documento_Cabecera_Enlace != '' && response.arrEdit[0].ID_Documento_Cabecera_Enlace != null) {
        nu_enlace = 1;

        $('#txt-ID_Documento_Guardado').val(response.arrEdit[0].ID_Documento_Cabecera_Enlace);
      }

      //Datos Cliente
      $('[name="ID_Tipo_Documento_Identidad_Existente"]').val(response.arrEdit[0].ID_Tipo_Documento_Identidad);
      $('[name="AID"]').val(response.arrEdit[0].ID_Entidad);
      $('[name="ANombre"]').val(response.arrEdit[0].No_Entidad);
      $('[name="ACodigo"]').val(response.arrEdit[0].Nu_Documento_Identidad);
      $('[name="Txt_Direccion_Entidad"]').val(response.arrEdit[0].Txt_Direccion_Entidad);
      $('[name="Txt_Email_Entidad"]').val(response.arrEdit[0].Txt_Email_Entidad);
      $('[name="Nu_Celular_Entidad"]').val(response.arrEdit[0].Nu_Celular_Entidad);

      $('#div-cliente_rapido').show();
      if (response.arrEdit[0].ID_Tipo_Documento == 3)
        $('#div-cliente_rapido').hide();

      //Datos Documento
      considerar_igv = response.arrEdit[0].Nu_Impuesto;
  
      url = base_url + 'HelperController/getOrganizaciones';
      $.post( url, function( responseOrganizaciones ){
        $( '#cbo-OrganizacionesVenta' ).html('');
        for (var i = 0; i < responseOrganizaciones.length; i++){
          selected = '';
          if(response.arrEdit[0].ID_Organizacion == responseOrganizaciones[i].ID_Organizacion)
            selected = 'selected="selected"';
          $( '#cbo-OrganizacionesVenta' ).append( '<option value="' + responseOrganizaciones[i].ID_Organizacion + '" ' + selected + '>' + responseOrganizaciones[i].No_Organizacion + '</option>' );
        }
      }, 'JSON');
	    
      url = base_url + 'HelperController/getTiposDocumentos';
      $.post( url, {Nu_Tipo_Filtro : 3}, function( responseTiposDocumento ){
        $( '#cbo-TiposDocumento' ).html('');
        for (var i = 0; i < responseTiposDocumento.length; i++){
          selected = '';
          if(response.arrEdit[0].ID_Tipo_Documento == responseTiposDocumento[i]['ID_Tipo_Documento'])
            selected = 'selected="selected"';
          $( '#cbo-TiposDocumento' ).append( '<option value="' + responseTiposDocumento[i]['ID_Tipo_Documento'] + '" data-nu_impuesto="' + responseTiposDocumento[i]['Nu_Impuesto'] + '" data-nu_enlace="' + responseTiposDocumento[i]['Nu_Enlace'] + '" ' + selected + '>' + responseTiposDocumento[i]['No_Tipo_Documento_Breve'] + '</option>' );
        }
      }, 'JSON');
  
		  url = base_url + 'HelperController/getSeriesDocumentoOficinaPuntoVenta';
      $.post( url, { ID_Organizacion : response.arrEdit[0].ID_Organizacion, ID_Tipo_Documento: response.arrEdit[0].ID_Tipo_Documento }, function( responseSeriesDocumento ){
        $( '#cbo-SeriesDocumento' ).html( '' );
        var sTipoSerie = 'Factura Venta';
        for (var i = 0; i < responseSeriesDocumento.length; i++){
          sTipoSerie = ' (' + ( responseSeriesDocumento[i].ID_POS > 0 ? 'Punto Venta' : 'Factura Venta' ) + ')';
          selected = '';
          if(response.arrEdit[0].ID_Serie_Documento_PK == responseSeriesDocumento[i]['ID_Serie_Documento_PK'])
            selected = 'selected="selected"';
          $( '#cbo-SeriesDocumento' ).append( '<option value="' + responseSeriesDocumento[i]['ID_Serie_Documento'] + '" ' + selected + ' data-id_serie_documento_pk=' + responseSeriesDocumento[i].ID_Serie_Documento_PK + '>' + responseSeriesDocumento[i]['ID_Serie_Documento'] + sTipoSerie + '</option>' );
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

        $('#tel-nu_referencia').val(response.arrEdit[0].Nu_Transaccion);
        $('#tel-nu_ultimo_4_digitos_tarjeta').val(response.arrEdit[0].Nu_Tarjeta);
      }

      // Obtener canales de venta
      url = base_url + 'HelperController/getCanalesVenta';
      $.post(url, function (responseCanalVenta) {
        if (responseCanalVenta.sStatus == 'success') {
          var l = responseCanalVenta.arrData.length;
          if (l == 1) {
            $('#cbo-canal_venta').html('<option value="' + responseCanalVenta.arrData[0].ID + '">' + responseCanalVenta.arrData[0].Nombre + '</option>');
          } else {
            $('#cbo-canal_venta').html('<option value="0" selected="selected">- Seleccionar -</option>');
            for (var x = 0; x < l; x++) {
              selected = '';
              if (response.arrEdit[0].ID_Canal_Venta_Tabla_Dato == responseCanalVenta.arrData[x].ID)
                selected = 'selected="selected"';
              $('#cbo-canal_venta').append('<option value="' + responseCanalVenta.arrData[x].ID + '" ' + selected + '>' + responseCanalVenta.arrData[x].Nombre + '</option>');
            }
          }
        } else {
          if (response.sMessageSQL !== undefined) {
            console.log(response.sMessageSQL);
          }
        }
      }, 'JSON');

  	  if ( response.arrEdit[0].Nu_Tipo == 1)// Si es Crédito
  	    $( '.div-MediosPago' ).show();
      
      $( '#txt-Fe_Emision' ).datepicker({}).on('changeDate', function (selected) {
        var minDate = new Date(selected.date.valueOf());
        $( '#txt-Fe_Vencimiento' ).datepicker('setStartDate', minDate);
      });
    
      var Fe_Emision = response.arrEdit[0].Fe_Emision.split('-');
      $( '#txt-Fe_Vencimiento' ).datepicker({
        autoclose : true,
        startDate : new Date(parseInt(Fe_Emision[0]), parseInt(Fe_Emision[1]) - 1, parseInt(Fe_Emision[2])),
        todayHighlight: true
      })
      $( '#txt-Fe_Vencimiento' ).datepicker('setStartDate', new Date(Fe_Emision[0] + "/" + Fe_Emision[1] + "/" + Fe_Emision[2]) );
      $( '#txt-Fe_Vencimiento' ).val(ParseDateString(response.arrEdit[0].Fe_Vencimiento, 6, '-'));

      var arrFechaVencimiento = response.arrEdit[0].Fe_Vencimiento.split('-');
      var dEmisionComparar = new Date(Fe_Emision[2], Fe_Emision[1], Fe_Emision[0]);
      var dVencimientoComparar = new Date(arrFechaVencimiento[2], arrFechaVencimiento[1], arrFechaVencimiento[0]);
          
      if(dVencimientoComparar <= dEmisionComparar){
        var dNuevaFechaVencimiento = sumaFecha(1, $( '#txt-Fe_Emision' ).val());
        $( '#txt-Fe_Vencimiento' ).val( dNuevaFechaVencimiento );
      }

      //Formato PDF
      var arrTipoRecepcion = [
        { "value": "5", "nombre": "Tienda" },
        { "value": "6", "nombre": "Delivery" },
        { "value": "7", "nombre": "Recojo en Tienda" },
      ];
      $('#cbo-recepcion').html('');
      for (var i = 0; i < arrTipoRecepcion.length; i++) {
        selected = '';
        if (response.arrEdit[0].Nu_Tipo_Recepcion == arrTipoRecepcion[i]['value'])
          selected = 'selected="selected"';
        $('#cbo-recepcion').append('<option value="' + arrTipoRecepcion[i]['value'] + '" ' + selected + '>' + arrTipoRecepcion[i]['nombre'] + '</option>');
      }

      $('.div-fecha_entrega').hide();
      if (response.arrEdit[0].Nu_Tipo_Recepcion != 5){
        $('.div-fecha_entrega').show();
      }

      //Tipo de recepcion
      $('#txt-fe_entrega').val(ParseDateString(response.arrEdit[0].Fe_Entrega, 6, '-'));
      $('[name="Txt_Direccion_Delivery"]').val(response.arrEdit[0].Txt_Direccion_Delivery);

      url = base_url + 'HelperController/getDeliveryVentas';
      var arrPost = {};
      $.post(url, arrPost, function (responseDelivery) {
        if (responseDelivery.sStatus == 'success') {
          var l = responseDelivery.arrData.length;
          if (l == 1) {
            $('#modal-cbo-transporte').html('<option value="' + responseDelivery.arrData[0].ID + '">' + responseDelivery.arrData[0].Nombre + '</option>');
          } else {
            $('#modal-cbo-transporte').html('<option value="0" selected="selected">- Seleccionar -</option>');
            for (var x = 0; x < l; x++) {
              selected = '';
              if (response.arrEdit[0].ID_Transporte_Delivery == responseDelivery.arrData[x].ID)
                selected = 'selected="selected"';
              $('#modal-cbo-transporte').append('<option value="' + responseDelivery.arrData[x].ID + '" ' + selected + '>' + responseDelivery.arrData[x].Nombre + '</option>');
            }
          }
        } else {
          if (responseDelivery.sMessageSQL !== undefined) {
            console.log(responseDelivery.sMessageSQL);
          }
        }
      }, 'JSON');

      //Validar N/C y N/D
      $('#cbo-TiposDocumentoModificar').html('<option value="" selected="selected">- Seleccionar -</option>');
      $('#cbo-SeriesDocumentoModificar').html('<option value="" selected="selected">- Seleccionar -</option>');
      $('#txt-ID_Numero_Documento_Modificar').val('');
      $('#cbo-MotivoReferenciaModificar').html('<option value="" selected="selected">- Seleccionar -</option>');
	    if (response.arrEdit[0].Nu_Enlace == 1) {
	      $( '.div-DocumentoModificar' ).show();

        url = base_url + 'HelperController/getTiposDocumentosModificar';
        $.post( url, {Nu_Tipo_Filtro : 1}, function( responseTiposDocumentoModificar ){
          $( '#cbo-TiposDocumentoModificar' ).html('<option value="" selected="selected">- Seleccionar -</option>');
          for (var i = 0; i < responseTiposDocumentoModificar.length; i++){
            selected = '';
            if(response.arrEdit[0].ID_Tipo_Documento_Modificar == responseTiposDocumentoModificar[i]['ID_Tipo_Documento'])
              selected = 'selected="selected"';
            $( '#cbo-TiposDocumentoModificar' ).append( '<option value="' + responseTiposDocumentoModificar[i]['ID_Tipo_Documento'] + '" ' + selected + '>' + responseTiposDocumentoModificar[i]['No_Tipo_Documento_Breve'] + '</option>' );
          }
        }, 'JSON');

        url = base_url + 'HelperController/getMotivosReferenciaModificar';
        $.post( url, { ID_Tipo_Documento: response.arrEdit[0].ID_Tipo_Documento }, function( responseMotivosReferencia ){
          $( '#cbo-MotivoReferenciaModificar' ).html('');
          for (var i = 0; i < responseMotivosReferencia.length; i++){
            selected = '';
            if(response.arrEdit[0].Nu_Codigo_Motivo_Referencia == responseMotivosReferencia[i]['Nu_Valor'])
              selected = 'selected="selected"';
            $( '#cbo-MotivoReferenciaModificar' ).append( '<option value="' + responseMotivosReferencia[i]['Nu_Valor'] + '" ' + selected + '>' + responseMotivosReferencia[i]['No_Descripcion'] + '</option>' );
          }
        }, 'JSON');
        
  		  url = base_url + 'HelperController/getSeriesDocumentoModificar';
        $.post( url, { ID_Organizacion : response.arrEdit[0].ID_Organizacion, ID_Tipo_Documento: response.arrEdit[0].ID_Tipo_Documento_Modificar }, function( responseSeriesDocumentoModificar ){
          for (var i = 0; i < responseSeriesDocumentoModificar.length; i++){
            selected = '';
            if(response.arrEdit[0].ID_Serie_Documento_Modificar_PK == responseSeriesDocumentoModificar[i]['ID_Serie_Documento_PK'])
              selected = 'selected="selected"';
            $( '#cbo-SeriesDocumentoModificar' ).append( '<option value="' + responseSeriesDocumentoModificar[i]['ID_Serie_Documento'] + '" ' + selected + ' data-id_serie_documento_pk="' + responseSeriesDocumentoModificar[i]['ID_Serie_Documento_PK'] + '">' + responseSeriesDocumentoModificar[i]['ID_Serie_Documento'] + '</option>' );
          }
        }, 'JSON');
        
        $( '#txt-ID_Numero_Documento_Modificar' ).val(response.arrEdit[0].ID_Numero_Documento_Modificar);
	    }

      /*
      var arrParams = {
        ID_Almacen: response.arrEdit[0].ID_Almacen,
      };
      getListaPrecios(arrParams);
      */
      
      if ( response.arrEdit[0].Nu_Descargar_Inventario == 1 ) {
        $( '#cbo-descargar_stock' ).html( '<option value="1" selected>Si</option>' );
        $( '#cbo-descargar_stock' ).append( '<option value="0">No</option>' );
      } else {
        $( '#cbo-descargar_stock' ).html( '<option value="1">Si</option>' );
        $( '#cbo-descargar_stock' ).append( '<option value="0" selected>No</option>' );
      }

      /* Adicionales */
      $('[name="No_Orden_Compra_FE"]').val(response.arrEdit[0].No_Orden_Compra_FE);
      $('[name="No_Placa_FE"]').val(response.arrEdit[0].No_Placa_FE);
      $('[name="Po_Detraccion"]').val(response.arrEdit[0].Po_Detraccion);
      $('[name="Nu_Expediente_FE"]').val(response.arrEdit[0].Nu_Expediente_FE);
      $('[name="Nu_Codigo_Unidad_Ejecutora_FE"]').val(response.arrEdit[0].Nu_Codigo_Unidad_Ejecutora_FE);

      $('#radio-InactiveDetraccion').prop('checked', true).iCheck('update');
      $('#radio-ActiveDetraccion').prop('checked', false).iCheck('update');
      $('.div-detraccion').hide();
      if ( response.arrEdit[0].Nu_Detraccion == 1 ) {
        $('.div-detraccion').show();
        
        $('#radio-InactiveDetraccion').prop('checked', false).iCheck('update');
        $('#radio-ActiveDetraccion').prop('checked', true).iCheck('update');
      }

      $('#cbo-sunat_tipo_transaction').html('<option value="1" selected="selected">VENTA INTERNA</option>');
      url = base_url + 'HelperController/getSunatTipoOperacion';
      $.post(url, {}, function (responseSunatTipoTransaction) {
        if (responseSunatTipoTransaction.sStatus == 'success') {
          $('#cbo-sunat_tipo_transaction').html('');
          var l = responseSunatTipoTransaction.arrData.length;
          for (var x = 0; x < l; x++) {
            selected = '';
            if (response.arrEdit[0].ID_Sunat_Tipo_Transaction == responseSunatTipoTransaction.arrData[x].ID)
              selected = 'selected="selected"';
            $('#cbo-sunat_tipo_transaction').append('<option value="' + responseSunatTipoTransaction.arrData[x].ID + '" ' + selected + '>' + responseSunatTipoTransaction.arrData[x].Nombre + '</option>');
          }
        } else {
          if (responseSunatTipoTransaction.sMessageSQL !== undefined) {
            console.log(responseSunatTipoTransaction.sMessageSQL);
          }
          console.log(responseSunatTipoTransaction.sMessage);
        }
      }, 'JSON');

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
      /* ./ Adicionales */
    
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
      
      $('[name="Txt_Glosa"]').val( '' );
      if (response.arrEdit[0].Txt_Glosa != '' && response.arrEdit[0].Txt_Glosa != null)
        $('[name="Txt_Glosa"]').val( response.arrEdit[0].Txt_Glosa );
      
      $('[name="Txt_Garantia"]').val( response.arrEdit[0].Txt_Garantia );
      
      // Detracción
      $( '#radio-InactiveDetraccion' ).prop('checked', true);
      $( '#radio-ActiveDetraccion' ).prop('checked', false);
      if (response.arrEdit[0].Nu_Detraccion != '0') {//0 = No y 1 = Si
        $( '#radio-InactiveDetraccion' ).prop('checked', false);
        $( '#radio-ActiveDetraccion' ).prop('checked', true);
      }

      // Retencion
      $('#radio-InactiveRetencion').prop('checked', true).iCheck('update');
      $('#radio-ActiveRetencion').prop('checked', false).iCheck('update');
      if (response.arrEdit[0].Nu_Retencion != '0') {//0 = No y 1 = Si
        $('#radio-InactiveRetencion').prop('checked', false).iCheck('update');
        $('#radio-ActiveRetencion').prop('checked', true).iCheck('update');
      }

      //Formato PDF
      var arrFormatoPDF = [
        {"No_Formato_PDF": "A4"},
        {"No_Formato_PDF": "A5"},
        {"No_Formato_PDF": "TICKET"},
      ];
      $( '#cbo-formato_pdf' ).html('');
      for (var i = 0; i < arrFormatoPDF.length; i++) {
        selected = '';
        if(response.arrEdit[0].No_Formato_PDF == arrFormatoPDF[i]['No_Formato_PDF'])
          selected = 'selected="selected"';
        $( '#cbo-formato_pdf' ).append( '<option value="' + arrFormatoPDF[i]['No_Formato_PDF'] + '" ' + selected + '>' + arrFormatoPDF[i]['No_Formato_PDF'] + '</option>' );
      }

      //Detalle
      $( '#table-DetalleProductosVenta' ).show();
      $( '#table-DetalleProductosVenta tbody' ).empty();
      
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
        if (response.arrEdit[i].Nu_Tipo_Impuesto == 1){
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
        
        var sVarianteMultipleTmp='';
        sVarianteMultipleTmp += ($('#hidden-iTipoRubroEmpresa').val()==6 ? (response.arrEdit[i].No_Variante_1 !== undefined && response.arrEdit[i].No_Variante_1 !== null && response.arrEdit[i].No_Variante_1 !== '' ? '<br>' + response.arrEdit[i].No_Variante_1 + ': ' + response.arrEdit[i].No_Valor_Variante_1 : '') : '');
        sVarianteMultipleTmp += ($('#hidden-iTipoRubroEmpresa').val()==6 ? (response.arrEdit[i].No_Variante_2 !== undefined && response.arrEdit[i].No_Variante_2 !== null && response.arrEdit[i].No_Variante_2 !== '' ? '<br>' + response.arrEdit[i].No_Variante_2 + ': ' + response.arrEdit[i].No_Valor_Variante_2 : '') : '');
        sVarianteMultipleTmp += ($('#hidden-iTipoRubroEmpresa').val()==6 ? (response.arrEdit[i].No_Variante_3 !== undefined && response.arrEdit[i].No_Variante_3 !== null && response.arrEdit[i].No_Variante_3 !== '' ? '<br>' + response.arrEdit[i].No_Variante_3 + ': ' + response.arrEdit[i].No_Valor_Variante_3 : '') : '');

        table_detalle_producto += 
        "<tr id='tr_detalle_producto" + response.arrEdit[i].ID_Producto + "'>"
          +"<td style='display:none;' class='text-left td-iIdItem'>" + response.arrEdit[i].ID_Producto + "</td>"
          +"<td class='text-right'><input type='text' class='pos-input txt-Qt_Producto form-control input-decimal input-size_cantidad' inputmode='decimal' value='" + Math.round10(response.arrEdit[i].Qt_Producto, -3) + "' " + (response.arrEdit[i].Nu_Tipo_Producto == 1 ? 'onkeyup=validateStockNow(event);' : '') + "  data-nu_activar_precio_x_mayor=" + response.arrEdit[i].Nu_Activar_Precio_x_Mayor + " data-id_item='" + response.arrEdit[i].ID_Producto + "' data-ss_icbper_item='" + response.arrEdit[i].Ss_Icbper + "' data-ss_icbper='" + response.arrEdit[i].Ss_Icbper_Item + "' data-id_impuesto_icbper='" + response.arrEdit[i].ID_Impuesto_Icbper + "' data-id_producto='" + response.arrEdit[i].ID_Producto + "' autocomplete='off'></td>"
          +"<td class='text-left'>"
            + '<span style="font-size: 11px;font-weight:normal;">[' + response.arrEdit[i].Nu_Codigo_Barra + ']<br>'
            + '<span style="font-size: 13px;font-weight:bold;">' + response.arrEdit[i].No_Producto + '</span>'
            + sVarianteMultipleTmp
            + (response.arrEdit[i].No_Unidad_Medida !== undefined && response.arrEdit[i].No_Unidad_Medida !== null && response.arrEdit[i].No_Unidad_Medida != '' ? ' <br><span style="font-size: 10px;font-weight:normal;">[' + response.arrEdit[i].No_Unidad_Medida + ']</span> ' : '')
          +"</td>"
          +"<td style='display:none; width: 39%' class='text-right td-sNotaItem' data-estado='mostrar' data-id_item=" + response.arrEdit[i].ID_Producto + " id='td-sNotaItem" + response.arrEdit[i].ID_Producto + "'>"
            +"<textarea class='pos-input form-control input-sNotaItem hotkey-cobrar_cliente hotkey-cancelar_venta hotkey-limpiar_item hotkey-focus_item' placeholder='' maxlength='250' autocomplete='off'>" + (response.arrEdit[i].Txt_Nota_Item != null ? response.arrEdit[i].Txt_Nota_Item : '') + "</textarea></td>"
          +"</td>"
          +"<td class='text-center'>"
            +"<button type='button' id='btn-add_nota_producto_pos' class='btn btn-sm btn-link' alt='Nota' title='Nota'><i class='fa fa-edit fa-2x' aria-hidden='true'></i></button>"
          +"</td>"
          +"<td class='text-right'><input type='text' class='pos-input txt-fValorUnitario form-control input-decimal input-size_importe' inputmode='decimal' value='" + Math.round10(parseFloat(response.arrEdit[i].Ss_Precio / response.arrEdit[i].Ss_Impuesto).toFixed(3), -3) + "' autocomplete='off'></td>"
          +"<td class='text-right'><input type='text' class='pos-input txt-Ss_Precio form-control input-decimal input-size_importe' inputmode='decimal' data-precio_actual='" + Math.round10(response.arrEdit[i].Ss_Precio, -3) + "' value='" + Math.round10(response.arrEdit[i].Ss_Precio, -3) + "' autocomplete='off'></td>"
          +"<td class='text-right'>"
            +"<select class='cbo-ImpuestosProducto form-control required' style='width: 100%;'>"
              +option_impuesto_producto
            +"</select>"
          +"</td>"
          +"<td style='display:none;' class='text-right'><input type='text' inputmode='decimal' class='pos-input txt-Ss_SubTotal_Producto form-control input-decimal' value='" + response.arrEdit[i].Ss_SubTotal_Producto + "' autocomplete='off' disabled></td>"
          +"<td class='text-right'><input type='text' inputmode='decimal' class='pos-input txt-Ss_Descuento form-control input-decimal input-size_otros' value='" + (response.arrEdit[i].Po_Descuento_Impuesto_Producto == 0.00 ? '' : response.arrEdit[i].Po_Descuento_Impuesto_Producto) + "' autocomplete='off'></td>"
          +"<td class='text-right'><input type='text' inputmode='decimal' class='pos-input txt-Ss_Total_Producto form-control input-decimal input-size_importe' value='" + response.arrEdit[i].Ss_Total_Producto + "' autocomplete='off'></td>"
          +"<td style='display:none;' class='text-right'><input type='text' inputmode='numeric' class='pos-input txt-Nu_Lote_Vencimiento form-control input-codigo_barra' placeholder='Opcional' value='" + (response.arrEdit[i].Nu_Lote_Vencimiento != null ? response.arrEdit[i].Nu_Lote_Vencimiento : '') + "' autocomplete='off'></td>"
          +"<td style='display:none;' class='text-right'><input type='text' class='pos-input txt-Fe_Lote_Vencimiento form-control date-picker-invoice' placeholder='Opcional' value='" + (response.arrEdit[i].Fe_Lote_Vencimiento != null ? ParseDateString(response.arrEdit[i].Fe_Lote_Vencimiento, 6, '-') : '') + "' autocomplete='off'></td>"
          +"<td style='display:none;' class='text-right td-fDescuentoSinImpuestosItem'>" + (response.arrEdit[i].Ss_Descuento_Producto == 0.00 ? '' : response.arrEdit[i].Ss_Descuento_Producto) + "</td>"
          +"<td style='display:none;' class='text-right td-fDescuentoImpuestosItem'>" + (response.arrEdit[i].Ss_Descuento_Impuesto_Producto == 0.00 ? '' : response.arrEdit[i].Ss_Descuento_Impuesto_Producto) + "</td>"
          +"<td class='text-center'><button type='button' id='btn-deleteProducto' class='btn btn-sm btn-link' alt='Eliminar' title='Eliminar'><i class='fa fa-trash-o fa-2x' aria-hidden='true'> </i></button></td>"
        +"</tr>";
      }
      
      $( '#table-DetalleProductosVenta > tbody' ).append(table_detalle_producto);

      $('.txt-Fe_Lote_Vencimiento').datepicker({
        autoclose: true,
        startDate: new Date(fYear, fToday.getMonth(), fDay),
        todayHighlight: true
      })

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
    
      $('#txt-total').val(parseFloat(response.arrEdit[0].Ss_Total).toFixed(2) );
      $('#span-total').text(parseFloat(response.arrEdit[0].Ss_Total).toFixed(2) );
      $( '#span-total_importe' ).text(parseFloat(response.arrEdit[0].Ss_Total).toFixed(2) );
  			
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

function imprimirVenta(ID){
  var $modal_imprimir = $( '.modal-message-delete' );
  $modal_imprimir.modal('show');
  
  $( '.modal-message-delete' ).removeClass('modal-danger modal-warning modal-success');
  $( '.modal-message-delete' ).addClass('modal-success');
  
  $( '.modal-title-message-delete' ).text('¿Deseas imprimir el documento?');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_imprimir.modal('hide');
  });
  
  $( '#btn-save-delete' ).off('click').click(function () {
    url = base_url + 'Ventas/VentaController/imprimirVenta/' + ID;
    //window.open(url,'_blank');
    window.location.href = url;
  });
}

function eliminarFacturaVenta(ID, Nu_Enlace, Nu_Descargar_Inventario, accion){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '.modal-message-delete' ).removeClass('modal-danger modal-warning modal-success');
  $( '.modal-message-delete' ).addClass('modal-danger');
  
  $( '.modal-title-message-delete' ).text('¿Deseas eliminar el documento?');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });
  
  $(document).bind('keydown', 'alt+l', function(){
    if ( accion=='delete' ) {
      _eliminarFacturaVenta($modal_delete, ID, Nu_Enlace, Nu_Descargar_Inventario);
      accion='';
    }
  });

  $( '#btn-save-delete' ).off('click').click(function () {
    _eliminarFacturaVenta($modal_delete, ID, Nu_Enlace, Nu_Descargar_Inventario);
  });
}

$(function () {
  $('[data-mask]').inputmask();
  $('.select2').select2();
  
  //Nota en detalle de cada ítem
  $('#table-DetalleProductosVenta > tbody').on('click', '#btn-add_nota_producto_pos', function () {
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

  $('#txt-Filtro_Fe_Inicio').val(fDay + '/' + fMonth + '/' + fYear);
  $('#txt-Filtro_Fe_Fin').val(fDay + '/' + fMonth + '/' + fYear);

  $('#btn-adicionales_fv').click(function () {
    $('.modal-adicionales').modal('show');
  })

  $('#btn-guias_remision_fv').click(function () {
    $('.modal-guias_remision').modal('show');
  })

  $('#div-addFlete').hide();
  $('#radio-flete_si').on('ifChecked', function () {
    $('#div-addFlete').show();
  })

  $('#radio-flete_no').on('ifChecked', function () {
    $('#div-addFlete').hide();
  })
  
  $('.div-detraccion').hide();
  $('#radio-ActiveDetraccion').on('ifChecked', function () {
    $('.div-detraccion').show();
  })

  $('#radio-InactiveDetraccion').on('ifChecked', function () {
    $('.div-detraccion').hide();
  })

  $('[name="radio-TipoDocumento"]').on('ifChecked', function () {
    $('.div-datos_guia_electronica').hide();
    if ($('[name="radio-TipoDocumento"]:checked').attr('value') == 8)
      $('.div-datos_guia_electronica').show();
  });

  $('.div-fecha_entrega').hide();
  $('#cbo-recepcion').change(function () {
    $('.div-fecha_entrega').hide();
    if ($(this).val() != 5)
      $('.div-fecha_entrega').show();

    $('.modal-delivery').modal('hide');
    if ($(this).val() == 6)
      $('.modal-delivery').modal('show');
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

  $('#btn-actualizar_datos_cliente').click(function () {
    if ($(this).data('display_data_cliente') == 1) {
      //setter
      $('#btn-actualizar_datos_cliente').data('display_data_cliente', 0);
    } else {
      $('#btn-actualizar_datos_cliente').data('display_data_cliente', 1);
    }

    if ($(this).data('display_data_cliente') == 1) {
      $('.div-actualizar_datos_cliente').show();
    } else {
      $('.div-actualizar_datos_cliente').hide();
    }
  })

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

            reload_table_venta();

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
    } else if ( Nu_Tipo_Medio_Pago==1 ) {//1=credito
      var dNuevaFechaVencimiento = sumaFecha(1, $( '#txt-Fe_Emision' ).val());
      $( '#txt-Fe_Vencimiento' ).val( dNuevaFechaVencimiento );
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
    } else if ($('[name="addCliente"]:checked').attr('value') == 0 && ($('#txt-AID').val().length === 0 || $('#txt-ANombre').val().length === 0)) {
      $('#txt-ANombre').closest('.form-group').find('.help-block').html('Seleccionar cliente');
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
        iTipoCliente : $('[name="addCliente"]:checked').attr('value'),
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
  
  $( '#radio-cliente_varios' ).on('ifChecked', function () {
    $( '.div-cliente_existente' ).hide();
    $( '.div-cliente_nuevo' ).hide();
    
    $( '#txt-AID' ).val( '1' );
  })
  
  $( '#radio-cliente_existente' ).on('ifChecked', function () {
    $( '.div-cliente_existente' ).show();
    $( '.div-cliente_nuevo' ).hide();
    
    $( '#txt-AID' ).val( '' );
    $( '#hidden-ID_Tipo_Documento_Identidad_Existente' ).val( '' );
    $( '#txt-ANombre' ).val( '' );
    $( '#txt-ACodigo' ).val( '' );
    $( '#txt-Txt_Email_Entidad' ).val( '' );
    $( '#txt-Nu_Celular_Entidad' ).val( '' );
    $( '#txt-Txt_Direccion_Entidad' ).val( '' );
  })
  
  $( '#radio-cliente_nuevo' ).on('ifChecked', function () {
    $( '.div-cliente_existente' ).hide();
    $( '.div-cliente_nuevo' ).show();
    
    $( '.div-tipo_cliente_1' ).hide();
    if($('#hidden-iTipoRubroEmpresa').val()=='17'){//la cava de baco
      $( '.div-tipo_cliente_1' ).show();
    }
    $( '#txt-AID' ).val( '' );
  })

  //LAE API SUNAT / RENIEC - CLIENTE
  $( '#btn-cloud-api_venta_cliente' ).click(function(){
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
      $( '#btn-cloud-api_venta_cliente' ).text('');
      $( '#btn-cloud-api_venta_cliente' ).attr('disabled', true);
      $( '#btn-cloud-api_venta_cliente' ).append( '<i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
      
      // Obtener datos de SUNAT y RENIEC
      var url_api = 'https://www.laesystems.com/librerias/sunat/partner/format/json/x-api-key/';
			if ( $( '#cbo-TiposDocumentoIdentidadCliente' ).val() == 2 )//2=RENIEC, API SUNAT
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
          $( '#btn-cloud-api_venta_cliente' ).closest('.form-group').find('.help-block').html('');
      	  $( '#btn-cloud-api_venta_cliente' ).closest('.form-group').removeClass('has-success').addClass('has-error');
      	  
          if (response.success==true){
            $( '#txt-No_Entidad_Cliente' ).val( response.data.No_Names );
            if ( $( '#cbo-TiposDocumentoIdentidadCliente' ).val() == 4) {//RUC
              $( '#txt-Txt_Direccion_Entidad_Cliente' ).val( response.data.Txt_Address );
              $( '#txt-Nu_Telefono_Entidad_Cliente' ).val( response.data.Nu_Phone );
              $( '#txt-Nu_Celular_Entidad_Cliente' ).val( response.data.Nu_Cellphone );
              if ( response.data.Nu_Status == 1)
                $("div.estado select").val("1");
              else
                $("div.estado select").val("0");
            }
          } else {
            $( '#txt-No_Entidad_Cliente' ).val( '' );
            if ( $( '#cbo-TiposDocumentoIdentidadCliente' ).val() == 4) {//RUC
              $( '#txt-Txt_Direccion_Entidad_Cliente' ).val( '' );
              $( '#txt-Nu_Telefono_Entidad_Cliente' ).val( '' );
              $( '#txt-Nu_Celular_Entidad_Cliente' ).val( '' );
            }
            $( '#txt-Nu_Documento_Identidad_Cliente' ).closest('.form-group').find('.help-block').html(response.msg);
        	  $( '#txt-Nu_Documento_Identidad_Cliente' ).closest('.form-group').removeClass('has-success').addClass('has-error');
        	  
  		  	  $( '#txt-Nu_Documento_Identidad_Cliente' ).focus();
  		  	  $( '#txt-Nu_Documento_Identidad_Cliente' ).select();
          }
  		  	
          $( '#btn-cloud-api_venta_cliente' ).text('');
          $( '#btn-cloud-api_venta_cliente' ).attr('disabled', false);
          $( '#btn-cloud-api_venta_cliente' ).append( '<i class="fa fa-cloud-download fa-lg"></i>' );
        },
        error: function(response){
          $( '#btn-cloud-api_venta_cliente' ).closest('.form-group').find('.help-block').html('Sin acceso');
      	  $( '#btn-cloud-api_venta_cliente' ).closest('.form-group').removeClass('has-success').addClass('has-error');
      	  
          $( '#txt-No_Entidad_Cliente' ).val( '' );
          $( '#txt-Txt_Direccion_Entidad_Cliente' ).val( '' );
          $( '#txt-Nu_Telefono_Entidad_Cliente' ).val( '' );
          $( '#txt-Nu_Celular_Entidad_Cliente' ).val( '' );

          $( '#btn-cloud-api_venta_cliente' ).text('');
          $( '#btn-cloud-api_venta_cliente' ).attr('disabled', false);
          $( '#btn-cloud-api_venta_cliente' ).append( '<i class="fa fa-cloud-download fa-lg"></i>' );
        }
      });
    }
  })
  
	/* Tipo Documento Identidad Cliente */
	$( '#cbo-TiposDocumentoIdentidadCliente' ).change(function(){
	  if ( $(this).val() == 2 ) {//DNI
		  $( '#label-Nombre_Documento_Identidad_Cliente' ).text('DNI');
		  $( '#label-No_Entidad_Cliente' ).text('Nombre(s) y Apellidos');
			$( '#txt-Nu_Documento_Identidad_Cliente' ).attr('maxlength', $(this).find(':selected').data('nu_cantidad_caracteres'));
	  } else if ( $(this).val() == 4 ) {//RUC
		  $( '#label-Nombre_Documento_Identidad_Cliente' ).text('RUC');
		  $( '#label-No_Entidad_Cliente' ).text('Razón Social');
			$( '#txt-Nu_Documento_Identidad_Cliente' ).attr('maxlength', $(this).find(':selected').data('nu_cantidad_caracteres'));
	  } else {
	    $( '#label-Nombre_Documento_Identidad_Cliente' ).text('# Documento Identidad');
		  $( '#label-No_Entidad_Cliente' ).text('Nombre(s) y Apellidos');
			$( '#txt-Nu_Documento_Identidad_Cliente' ).attr('maxlength', $(this).find(':selected').data('nu_cantidad_caracteres'));
	  }
	})
  
  url = base_url + 'HelperController/getOrganizaciones';
  $.post( url , function( response ){
    if (response.length == 1) {
      $( '#cbo-filtro-organizacion' ).html( '<option value="' + response[0].ID_Organizacion + '">' + response[0].No_Organizacion + '</option>' );
    } else {
      $( '#cbo-filtro-organizacion' ).html('<option value="" selected="selected">- Seleccionar -</option>');
      for (var i = 0; i < response.length; i++)
        $( '#cbo-filtro-organizacion' ).append( '<option value="' + response[i].ID_Organizacion + '">' + response[i].No_Organizacion + '</option>' );
    }
  }, 'JSON');

  $('#cbo-Filtro_TiposDocumento').html('<option value="" selected="selected">Todos</option>');
  url = base_url + 'HelperController/getTiposDocumentos';
  $.post( url, {Nu_Tipo_Filtro : 3}, function( response ){//1 = Venta
    $( '#cbo-Filtro_TiposDocumento' ).html('<option value="" selected="selected">Todos</option>');
    for (var i = 0; i < response.length; i++) {
      if( $('#hidden-Nu_Tipo_Proveedor_FE').val()!=3) {
        $( '#cbo-Filtro_TiposDocumento' ).append( '<option value="' + response[i].ID_Tipo_Documento + '">' + response[i].No_Tipo_Documento_Breve + '</option>' );
      }
      if( $('#hidden-Nu_Tipo_Proveedor_FE').val()==3 && response[i].ID_Tipo_Documento == 2) {
        $( '#cbo-Filtro_TiposDocumento' ).append( '<option value="' + response[i].ID_Tipo_Documento + '">' + response[i].No_Tipo_Documento_Breve + '</option>' );
      }
    }
  }, 'JSON');
  
	$( '#cbo-Filtro_SeriesDocumento' ).html('<option value="" selected="selected">Todos</option>');
	$( '#cbo-Filtro_TiposDocumento' ).change(function(){
	  $( '#cbo-Filtro_SeriesDocumento' ).html('<option value="" selected="selected">Todos</option>');
	  if ( $(this).val() > 0) {
		  url = base_url + 'HelperController/getSeriesDocumento';
      $.post( url, { ID_Organizacion: $( '#cbo-filtro-organizacion' ).val(), ID_Tipo_Documento: $(this).val() }, function( response ){
	      $( '#cbo-Filtro_SeriesDocumento' ).html('<option value="" selected="selected">- Todos -</option>');
        if (response.length > 0) {
          for (var i = 0; i < response.length; i++)
            $( '#cbo-Filtro_SeriesDocumento' ).append( '<option value="' + response[i].ID_Serie_Documento + '">' + response[i].ID_Serie_Documento + '</option>' );
        }
      }, 'JSON');
	  }
	})
	
	// Mostrar tipo de producto para inventarios
	$( '.div-Producto' ).hide();
	$( '#cbo-modal-grupoItem' ).change(function(){
  	$( '.div-Producto' ).show();
	  if ( $(this).val() == 0 ){//Servicio
    	$( '.div-Producto' ).hide();
	  }
	})

  $('#cbo-filtro_empresa').html('<option value="0" selected="selected">- Todas -</option>');
  $('#cbo-filtro_organizacion').html('<option value="0" selected="selected">- Todas -</option>');  

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

  url = base_url + 'Ventas/VentaController/ajax_list';
  table_venta = $('#table-Venta').DataTable({
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
        data.filtro_tipo_sistema = $('#cbo-filtro-tipo_sistema').val(),
        data.filtro_estado_sistema = $('#cbo-filtro-estado_sistema').val(),
        data.filtro_empresa = $('#cbo-filtro_empresa').val(),
        data.filtro_organizacion = $('#cbo-filtro_organizacion').val(),
        data.filtro_almacen = $('#cbo-filtro_almacen').val(),
        data.Filtro_Fe_Inicio       = ParseDateString($( '#txt-Filtro_Fe_Inicio' ).val(), 1, '/'),
        data.Filtro_Fe_Fin          = ParseDateString($( '#txt-Filtro_Fe_Fin' ).val(), 1, '/'),
        data.Filtro_TiposDocumento  = $( '#cbo-Filtro_TiposDocumento' ).val(),
        data.Filtro_SeriesDocumento = $( '#cbo-Filtro_SeriesDocumento' ).val(),
        data.Filtro_NumeroDocumento = $( '#txt-Filtro_NumeroDocumento' ).val(),
        data.Filtro_Estado          = $( '#cbo-Filtro_Estado' ).val(),
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

  url = base_url + 'HelperController/getEmpresas';
  $.post(url, function (response) {
    $('#cbo-filtro_empresa').html('<option value="0" selected="selected">- Todas -</option>');
    for (var i = 0; i < response.length; i++)
      $('#cbo-filtro_empresa').append('<option value="' + response[i].ID_Empresa + '">' + response[i].No_Empresa + '</option>');
  }, 'JSON');

  $('#cbo-filtro_empresa').change(function () {
    if ($(this).val() > 0) {
      url = base_url + 'HelperController/getOrganizaciones';
      var arrParams = {
        iIdEmpresa: $(this).val(),
      };
      $.post(url, arrParams, function (response) {
        $('#cbo-filtro_organizacion').html('<option value="0" selected="selected">- Todas -</option>');
        for (var i = 0; i < response.length; i++)
          $('#cbo-filtro_organizacion').append('<option value="' + response[i].ID_Organizacion + '">' + response[i].No_Organizacion + '</option>');
      }, 'JSON');
    }
  });

  $('#cbo-filtro_organizacion').change(function () {
    if ($(this).val() > 0) {
      url = base_url + 'HelperController/getAlmacenes';
      var arrParams = {
        iIdOrganizacion: $(this).val(),
      };
      $.post(url, arrParams, function (response) {
        $('#cbo-filtro_almacen').html('<option value="0" selected="selected">- Todos -</option>');
        for (var i = 0; i < response.length; i++)
          $('#cbo-filtro_almacen').append('<option value="' + response[i].ID_Almacen + '">' + response[i].No_Almacen + '</option>');
      }, 'JSON');
    }
  });

  $( '#btn-filter' ).click(function(){
    table_venta.ajax.reload();
  });
  
  $( '#form-Venta' ).validate({
    rules: {
      ID_Tipo_Documento: {
        required: true,
      },
      ID_Serie_Documento: {
        required: true,
      },
      ID_Numero_Documento: {
        required: true,
      },
			Fe_Emision: {
				required: true,
      },
      Txt_Email_Entidad_Cliente: {
        validemail: true
      },
		},
    messages: {
      ID_Tipo_Documento: {
        required: "Ingresar tipo",
      },
      ID_Serie_Documento: {
        required: "Ingresar serie",
      },
      ID_Numero_Documento: {
        required: "Ingresar número",
      },
			Fe_Emision:{
				required: "Ingresar F. Emisión",
      },
      Txt_Email_Entidad_Cliente: {
        validemail: "Ingresar email válido",
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
		submitHandler: form_Venta
	});
	
  // COBRAR CREDITO
  $( '#btn-cobrar_cliente' ).click(function(){
    var fPagoClienteCobranza = parseFloat($( '#modal-tel-cobrar_cliente-fPagoCliente' ).val());
    if ( fPagoClienteCobranza == 0.00 || isNaN(fPagoClienteCobranza) ) {
      $( '[name="fPagoCliente"]' ).closest('.form-group').find('.help-block').html( 'Ingresar monto' );
      $( '[name="fPagoCliente"]' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    
      scrollToError($('.modal-cobrar_cliente .modal-body'), $( '[name="fPagoCliente"]' ));
    } else if ( $( '#hidden-cobrar_cliente-detraccion' ).val()=='0' && fPagoClienteCobranza > parseFloat($( '#hidden-cobrar_cliente-fsaldo' ).val()) ) {
      $( '#modal-tel-cobrar_cliente-fPagoCliente' ).closest('.form-group').find('.help-block').html('Debes de cobrar <b>' + $( '#hidden-cobrar_cliente-fsaldo' ).val() + '</b>' );
      $( '#modal-tel-cobrar_cliente-fPagoCliente' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    
      scrollToError($('.modal-cobrar_cliente .modal-body'), $( '#modal-tel-cobrar_cliente-fPagoCliente' ));
    } else if ( $( '#hidden-cobrar_cliente-detraccion' ).val()=='1' && (fPagoClienteCobranza < parseFloat($( '#hidden-cobrar_cliente-fsaldo' ).val()) || fPagoClienteCobranza > parseFloat($( '#hidden-cobrar_cliente-fsaldo' ).val())) ) {
      $( '#modal-tel-cobrar_cliente-fPagoCliente' ).closest('.form-group').find('.help-block').html('Debes de cobrar <b>' + $( '#hidden-cobrar_cliente-fsaldo' ).val() + '</b>' );
      $( '#modal-tel-cobrar_cliente-fPagoCliente' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    
      scrollToError($('.modal-cobrar_cliente .modal-body'), $( '#modal-tel-cobrar_cliente-fPagoCliente' ));
    } else {
      $( '.help-block' ).empty();
      $( '[name="fPagoCliente"]' ).closest('.form-group').removeClass('has-error');
      $( '[name="sNombreRecepcion"]' ).closest('.form-group').removeClass('has-error');
      
      $( '#btn-cobrar_cliente' ).text('');
      $( '#btn-cobrar_cliente' ).attr('disabled', true);
      $( '#btn-cobrar_cliente' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
      $( '#btn-salir' ).attr('disabled', true);

      url = base_url + 'HelperController/cobranzaClientePuntoVenta';
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
            
            table_venta.ajax.reload();
          } else {
            $( '.modal-message' ).addClass( 'modal-' + response.sStatus );
            $( '.modal-title-message' ).text( response.sMessage );
            setTimeout(function() {$('#modal-message').modal('hide');}, 3100);
          }
          
          $( '#btn-cobrar_cliente' ).text('');
          $( '#btn-cobrar_cliente' ).append( 'Cobrar' );
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
        $( '#btn-cobrar_cliente' ).append( 'Cobrar' );
        $( '#btn-salir' ).attr('disabled', false);
      })
    }
  })
	
	$( '#cbo-Monedas' ).change(function(){
	  if ( $(this).val() > 0 )
	    $( '.span-signo' ).text( $(this).find(':selected').data('no_signo') );
	})

  $('#cbo-almacen').change(function () {
    var arrParams = {
      ID_Almacen: $('#cbo-almacen').val(),
    };
    getListaPrecios(arrParams);
  })
  
	$( '.div-MediosPago' ).hide();
	$( '#cbo-MediosPago' ).change(function(){
	  $( '.div-MediosPago' ).hide();
	  if ( $(this).find(':selected').data('nu_tipo') == 1 )// Si es Crédito
	    $( '.div-MediosPago' ).show();
	})
	
	//Validar N/C y N/D
	$( '#cbo-TiposDocumentoModificar' ).change(function(){
	  $( '#cbo-SeriesDocumentoModificar' ).html('');
	  if ( $(this).val() > 0 ) {
		  url = base_url + 'HelperController/getSeriesDocumentoModificarxAlmacen';
      $.post( url, {ID_Organizacion: $( '#header-a-id_organizacion' ).val(), ID_Almacen : $( '#cbo-almacen' ).val(), ID_Tipo_Documento: $(this).val() }, function( response ){
        if (response.length == 0)
          $( '#cbo-SeriesDocumentoModificar' ).html('<option value="" selected="selected">Sin serie</option>');
        else {
          $( '#cbo-SeriesDocumentoModificar' ).html('<option value="" selected="selected">- Seleccionar -</option>');
          for (var i = 0; i < response.length; i++)
            $( '#cbo-SeriesDocumentoModificar' ).append( '<option value="' + response[i]['ID_Serie_Documento'] + '" data-id_serie_documento_pk="' + response[i]['ID_Serie_Documento_PK'] + '">' + response[i]['ID_Serie_Documento'] + '</option>' );
        }
      }, 'JSON');
	  }
	})

	$( '#btn-crearItem' ).click(function(){
    bEstadoValidacion = validatePreviousDocumentToSaveSale();
	  if (bEstadoValidacion){
	   crearItem();
	  }
	})

	$( '#btn-addProducto' ).click(function(){
    var $Nu_Codigo_Barra = $('#txt-Nu_Codigo_Barra').val();  
    var $no_variante_1 = $('#txt-no_variante_1').val();
    var $no_valor_variante_1 = $('#txt-no_valor_variante_1').val();
    var $no_variante_2 = $('#txt-no_variante_2').val();
    var $no_valor_variante_2 = $('#txt-no_valor_variante_2').val();
    var $no_variante_3 = $('#txt-no_variante_3').val();
    var $no_valor_variante_3 = $('#txt-no_valor_variante_3').val();
    
    var arrDataAdicionalTmpDetalleItem = {
      'Nu_Codigo_Barra' : $Nu_Codigo_Barra,
      'no_variante_1' : $no_variante_1,
      'no_valor_variante_1' : $no_valor_variante_1,
      'no_variante_2' : $no_variante_2,
      'no_valor_variante_2' : $no_valor_variante_2,
      'no_variante_3' : $no_variante_3,
      'no_valor_variante_3' : $no_valor_variante_3,
    };

    addItemDetail($('#txt-ID_Producto').val(), $('#txt-No_Producto').val(), parseFloat($('#txt-Ss_Precio').val()), $('#txt-ID_Impuesto_Cruce_Documento').val(), $('#txt-Nu_Tipo_Impuesto').val(), $('#txt-Ss_Impuesto').val(), parseFloat($('#txt-Qt_Producto').val()), $('#txt-nu_tipo_item').val(), $('#txt-ID_Impuesto_Icbper').val(), $('#txt-Ss_Icbper').val(), $('#txt-No_Unidad_Medida').val(), $('#txt-nu_activar_precio_x_mayor').val(), arrDataAdicionalTmpDetalleItem);
	})

  $('#table-DetalleProductosVenta tbody' ).on('input', '.txt-fValorUnitario', function(){
    var fila = $(this).parents("tr");
    var impuesto_producto = fila.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto');
    var nu_tipo_impuesto = fila.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
    var $ID_Producto = fila.find(".txt-Ss_Precio").data('id_producto');
    var fValorUnitario = fila.find(".txt-fValorUnitario").val();
    var fPrecioVenta = 0.00;
    fPrecioVenta = parseFloat(fValorUnitario * impuesto_producto).toFixed(6);
    var precio = fPrecioVenta;
    var cantidad = fila.find(".txt-Qt_Producto").val();
    var subtotal_producto = fila.find(".txt-Ss_SubTotal_Producto").val();
    var descuento = fila.find(".txt-Ss_Descuento").val();
    var total_producto = fila.find(".txt-Ss_Total_Producto").val();
    var fDescuento_SubTotal_Producto = 0, fDescuento_Total_Producto = 0;
    var fIcbper = fila.find(".txt-Qt_Producto").data('ss_icbper');
    
    fila.find(".txt-Ss_Precio").val( fPrecioVenta );

    if (parseFloat(precio) > 0.00 && parseFloat(cantidad) > 0) {
      $('#tr_detalle_producto' + $ID_Producto).removeClass('danger');
	    $( '#table-DetalleProductosVenta tfoot' ).empty();
      if (nu_tipo_impuesto == 1){//CON IGV
        fDescuento_SubTotal_Producto = parseFloat(((precio * cantidad) - (((descuento * (precio * cantidad)) / 100))) / impuesto_producto);
        fDescuento_Total_Producto = parseFloat(((precio * cantidad) - (((descuento * (precio * cantidad)) / 100))));
        fila.find(".td-fDescuentoSinImpuestosItem").text( (parseFloat((((descuento * (precio * cantidad)) / 100) * impuesto_producto)).toFixed(2)).toString().split(". ") );
        fila.find(".td-fDescuentoImpuestosItem").text( (parseFloat(((descuento * (precio * cantidad)) / 100) - (((descuento * (precio * cantidad)) / 100) * impuesto_producto)).toFixed(2)).toString().split(". ") );
        fila.find(".txt-Ss_SubTotal_Producto").val( (parseFloat(fDescuento_SubTotal_Producto).toFixed(6)).toString().split(". ") );
  		  fila.find(".txt-Ss_Total_Producto").val( (parseFloat(fDescuento_Total_Producto).toFixed(2)).toString().split(". ") );
        
        var $Ss_SubTotal = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_IGV = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleProductosVenta > tbody > tr").each(function(){
          var rows = $(this);
          var Ss_Impuesto           = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
          var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
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
          
          $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto) )) / 100);
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
        fila.find(".td-fDescuentoSinImpuestosItem").text( (parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". ") );
        fila.find(".td-fDescuentoImpuestosItem").text( (parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". ") );
        fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_Total_Producto").val((parseFloat(((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)) * impuesto_producto).toFixed(2)).toString().split(". "));
        
        var $Ss_Inafecto = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleProductosVenta > tbody > tr").each(function(){
          var rows = $(this);
          var Ss_Impuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
          var Nu_Tipo_Impuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
          var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());

          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
            
          if (Nu_Tipo_Impuesto == 2)
            $Ss_Inafecto += $Ss_SubTotal_Producto;
          
          $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto) )) / 100);
          $Ss_Total += parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
        });
        
        $( '#txt-inafecto' ).val( $Ss_Inafecto.toFixed(2) );
    		$( '#span-inafecto' ).text( $Ss_Inafecto.toFixed(2) );
    		
    		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
    		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
    		
    		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
    		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
    		$( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
      } else if (nu_tipo_impuesto == 3){//Exonerada
        fila.find(".td-fDescuentoSinImpuestosItem").text( (parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". ") );
        fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_Total_Producto").val((parseFloat(((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)) * impuesto_producto).toFixed(2)).toString().split(". "));
        
        var $Ss_Exonerada = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleProductosVenta > tbody > tr").each(function(){
          var rows = $(this);
          var Ss_Impuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
          var Nu_Tipo_Impuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
          var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());

          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
            
          if (Nu_Tipo_Impuesto == 3)
            $Ss_Exonerada += $Ss_SubTotal_Producto;
          
          $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto) )) / 100);
          $Ss_Total += parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
        });
        
        $( '#txt-exonerada' ).val( $Ss_Exonerada.toFixed(2) );
    		$( '#span-exonerada' ).text( $Ss_Exonerada.toFixed(2) );
    		
    		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
    		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
    		
    		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
    		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
    		$( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
      } else if (nu_tipo_impuesto == 4){//Gratuita
        fila.find(".td-fDescuentoSinImpuestosItem").text( (parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". ") );
        fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_Total_Producto").val((parseFloat(((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)) * impuesto_producto).toFixed(2)).toString().split(". "));
        
        var $Ss_Gratuita = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleProductosVenta > tbody > tr").each(function(){
          var rows = $(this);
          var Ss_Impuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
          var Nu_Tipo_Impuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
          var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());

          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
            
          if (Nu_Tipo_Impuesto == 4)
            $Ss_Gratuita += $Ss_SubTotal_Producto;
          
          $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto) )) / 100);
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
      calcularIcbper();
      calcularDescuentoTotal(0);
    }
  })

  $('#table-DetalleProductosVenta tbody' ).on('input', '.txt-Ss_Precio', function(){
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
    
    fila.find(".txt-fValorUnitario").val( parseFloat(precio / impuesto_producto).toFixed(2) );

    if ( parseFloat(precio) > 0.00 && parseFloat(cantidad) > 0){
      $('#tr_detalle_producto' + $ID_Producto).removeClass('danger');
	    $( '#table-DetalleProductosVenta tfoot' ).empty();
      if (nu_tipo_impuesto == 1){//CON IGV
        fDescuento_SubTotal_Producto = parseFloat(((precio * cantidad) - (((descuento * (precio * cantidad)) / 100))) / impuesto_producto);
        fDescuento_Total_Producto = parseFloat(((precio * cantidad) - (((descuento * (precio * cantidad)) / 100))));
        fila.find(".td-fDescuentoSinImpuestosItem").text( (parseFloat((((descuento * (precio * cantidad)) / 100) / impuesto_producto)).toFixed(2)).toString().split(". ") );
        fila.find(".td-fDescuentoImpuestosItem").text( (parseFloat(((descuento * (precio * cantidad)) / 100) - (((descuento * (precio * cantidad)) / 100) / impuesto_producto)).toFixed(2)).toString().split(". ") );
        fila.find(".txt-Ss_SubTotal_Producto").val( (parseFloat(fDescuento_SubTotal_Producto).toFixed(6)).toString().split(". ") );
  		  fila.find(".txt-Ss_Total_Producto").val( (parseFloat(fDescuento_Total_Producto).toFixed(2)).toString().split(". ") );
        
        var $Ss_SubTotal = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_IGV = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleProductosVenta > tbody > tr").each(function(){
          var rows = $(this);
          var Ss_Impuesto           = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
          var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
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
          
          $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto) )) / 100);
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
        fila.find(".td-fDescuentoSinImpuestosItem").text( (parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". ") );
        fila.find(".td-fDescuentoImpuestosItem").text( (parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". ") );
        fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". ") );
        fila.find(".txt-Ss_Total_Producto").val((parseFloat(((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)) * impuesto_producto).toFixed(2)).toString().split(". ") );
        
        var $Ss_Inafecto = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleProductosVenta > tbody > tr").each(function(){
          var rows = $(this);
          var Ss_Impuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
          var Nu_Tipo_Impuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
          var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());

          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
            
          if (Nu_Tipo_Impuesto == 2)
            $Ss_Inafecto += $Ss_SubTotal_Producto;
          
          $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto) )) / 100);
          $Ss_Total += parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
        });
        
        $( '#txt-inafecto' ).val( $Ss_Inafecto.toFixed(2) );
    		$( '#span-inafecto' ).text( $Ss_Inafecto.toFixed(2) );
    		
    		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
    		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
    		
    		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
    		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
    		$( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
      } else if (nu_tipo_impuesto == 3){//Exonerada
        fila.find(".td-fDescuentoSinImpuestosItem").text( (parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". ") );
        fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_Total_Producto").val((parseFloat(((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)) * impuesto_producto).toFixed(2)).toString().split(". "));
        
        var $Ss_Exonerada = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleProductosVenta > tbody > tr").each(function(){
          var rows = $(this);
          var Ss_Impuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
          var Nu_Tipo_Impuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
          var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());

          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
            
          if (Nu_Tipo_Impuesto == 3)
            $Ss_Exonerada += $Ss_SubTotal_Producto;
          
          $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto) )) / 100);
          $Ss_Total += parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
        });
        
        $( '#txt-exonerada' ).val( $Ss_Exonerada.toFixed(2) );
    		$( '#span-exonerada' ).text( $Ss_Exonerada.toFixed(2) );
    		
    		$( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
    		$( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
    		
    		$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
    		$( '#span-total' ).text( $Ss_Total.toFixed(2) );
    		$( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
      } else if (nu_tipo_impuesto == 4){//Gratuita
        fila.find(".td-fDescuentoSinImpuestosItem").text( (parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". ") );
        fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_Total_Producto").val((parseFloat(((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)) * impuesto_producto).toFixed(2)).toString().split(". "));
        
        var $Ss_Gratuita = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_Total = 0.00;
        $("#table-DetalleProductosVenta > tbody > tr").each(function(){
          var rows = $(this);
          var Ss_Impuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
          var Nu_Tipo_Impuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
          var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
          var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());

          if(isNaN($Ss_Descuento_Producto))
            $Ss_Descuento_Producto = 0;
            
          if (Nu_Tipo_Impuesto == 4)
            $Ss_Gratuita += $Ss_SubTotal_Producto;
          
          $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto) )) / 100);
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
      calcularIcbper();
      calcularDescuentoTotal(0);
    }
  })
	
  $('#table-DetalleProductosVenta tbody' ).on('input', '.txt-Qt_Producto', function(){
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
              
              calcularImportexItem(fila);
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

              calcularImportexItem(fila);
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
        $( '#table-DetalleProductosVenta tfoot' ).empty();
        if (nu_tipo_impuesto == 1){//CON IGV
          fDescuento_SubTotal_Producto = parseFloat(((precio * cantidad) - (((descuento * (precio * cantidad)) / 100))) / impuesto_producto);
          fDescuento_Total_Producto = parseFloat(((precio * cantidad) - (((descuento * (precio * cantidad)) / 100))));
          fila.find(".td-fDescuentoSinImpuestosItem").text( (parseFloat((((descuento * (precio * cantidad)) / 100) / impuesto_producto)).toFixed(2)).toString().split(". ") );
          fila.find(".td-fDescuentoImpuestosItem").text( (parseFloat(((descuento * (precio * cantidad)) / 100) - (((descuento * (precio * cantidad)) / 100) / impuesto_producto)).toFixed(2)).toString().split(". ") );
          fila.find(".txt-Ss_SubTotal_Producto").val( (parseFloat(fDescuento_SubTotal_Producto).toFixed(6)).toString().split(". ") );
          fila.find(".txt-Ss_Total_Producto").val( (parseFloat(fDescuento_Total_Producto).toFixed(2)).toString().split(". ") );
          
          var $Ss_SubTotal = 0.00;
          var $Ss_Descuento = 0.00;
          var $Ss_IGV = 0.00;
          var $Ss_Total = 0.00;
          $("#table-DetalleProductosVenta > tbody > tr").each(function(){
            var rows = $(this);
            var Ss_Impuesto           = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
            var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
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
            
            $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto) )) / 100);
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
          fila.find(".td-fDescuentoSinImpuestosItem").text( (parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". ") );
          fila.find(".td-fDescuentoImpuestosItem").text( (parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". ") );
          fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
          fila.find(".txt-Ss_Total_Producto").val((parseFloat(((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)) * impuesto_producto).toFixed(2)).toString().split(". "));

          var $Ss_Inafecto = 0.00;
          var $Ss_Descuento = 0.00;
          var $Ss_Total = 0.00;
          $("#table-DetalleProductosVenta > tbody > tr").each(function(){
            var rows = $(this);
            var Ss_Impuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
            var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
            var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
            var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());

            if(isNaN($Ss_Descuento_Producto))
              $Ss_Descuento_Producto = 0;
              
            if (Nu_Tipo_Impuesto == 2)
              $Ss_Inafecto += $Ss_SubTotal_Producto;
            
            $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto) )) / 100);
            $Ss_Total += parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
          });
          
          $( '#txt-inafecto' ).val( $Ss_Inafecto.toFixed(2) );
          $( '#span-inafecto' ).text( $Ss_Inafecto.toFixed(2) );
          
          $( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
          $( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
          
          $( '#txt-total' ).val( $Ss_Total.toFixed(2) );
          $( '#span-total' ).text( $Ss_Total.toFixed(2) );
          $( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
        } else if (nu_tipo_impuesto == 3){//Exonerada
          fila.find(".td-fDescuentoSinImpuestosItem").text( (parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". ") );
          fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
          fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
          fila.find(".txt-Ss_Total_Producto").val((parseFloat(((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)) * impuesto_producto).toFixed(2)).toString().split(". "));
          
          var $Ss_Exonerada = 0.00;
          var $Ss_Descuento = 0.00;
          var $Ss_Total = 0.00;
          $("#table-DetalleProductosVenta > tbody > tr").each(function(){
            var rows = $(this);
            var Ss_Impuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
            var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
            var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
            var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());

            if(isNaN($Ss_Descuento_Producto))
              $Ss_Descuento_Producto = 0;
              
            if (Nu_Tipo_Impuesto == 3)
              $Ss_Exonerada += $Ss_SubTotal_Producto;
            
            $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto) )) / 100);
            $Ss_Total += parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
          });
          
          $( '#txt-exonerada' ).val( $Ss_Exonerada.toFixed(2) );
          $( '#span-exonerada' ).text( $Ss_Exonerada.toFixed(2) );
          
          $( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
          $( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
          
          $( '#txt-total' ).val( $Ss_Total.toFixed(2) );
          $( '#span-total' ).text( $Ss_Total.toFixed(2) );
          $( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
        } else if (nu_tipo_impuesto == 4){//Gratuita
          fila.find(".td-fDescuentoSinImpuestosItem").text( (parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". ") );
          fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
          fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
          fila.find(".txt-Ss_Total_Producto").val((parseFloat(((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)) * impuesto_producto).toFixed(2)).toString().split(". "));
          
          var $Ss_Gratuita = 0.00;
          var $Ss_Descuento = 0.00;
          var $Ss_Total = 0.00;
          $("#table-DetalleProductosVenta > tbody > tr").each(function(){
            var rows = $(this);
            var Ss_Impuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
            var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
            var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
            var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());

            if(isNaN($Ss_Descuento_Producto))
              $Ss_Descuento_Producto = 0;
              
            if (Nu_Tipo_Impuesto == 4)
              $Ss_Gratuita += $Ss_SubTotal_Producto;
            
            $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto) )) / 100);
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
        
        calcularIcbper();
        calcularDescuentoTotal(0);
      }// IF - ELSE PRECIO Y CANTIDAD > 0
    }
    //PRECIOS X MAYOR
  })

  $('#table-DetalleProductosVenta tbody' ).on('change', '.cbo-ImpuestosProducto', function(){
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
        if (nu_tipo_impuesto == 1){//CON IGV
          fila.find(".td-fDescuentoSinImpuestosItem").text( (parseFloat(((descuento * (subtotal_producto)) / 100)).toFixed(2)).toString().split(". ") );
          fila.find(".td-fDescuentoImpuestosItem").text( (parseFloat(((descuento * (subtotal_producto * impuesto_producto)) / 100) - ((descuento * (subtotal_producto)) / 100)).toFixed(2)).toString().split(". ") );
          fila.find(".txt-Ss_Precio").val((parseFloat(fValorUnitario * impuesto_producto).toFixed(6)).toString().split(". ") );
    		  fila.find(".txt-Ss_Total_Producto").val( (parseFloat(subtotal_producto * impuesto_producto).toFixed(2)).toString().split(". ") );
    		  
          var $Ss_SubTotal = 0.00;
          var $Ss_Exonerada = 0.00;
          var $Ss_Inafecto = 0.00;
          var $Ss_Gratuita = 0.00;
          var $Ss_Descuento = 0.00;
          var $Ss_IGV = 0.00;
          var $Ss_Total = 0.00;
          $("#table-DetalleProductosVenta > tbody > tr").each(function(){
            var rows = $(this);
            var Ss_Impuesto           = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
            var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
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
            
            $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto) )) / 100);
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
        } else if (nu_tipo_impuesto == 2){//Inafecto
          fila.find(".td-fDescuentoSinImpuestosItem").text( (parseFloat(((descuento * (subtotal_producto)) / 100)).toFixed(2)).toString().split(". ") );
          fila.find(".td-fDescuentoImpuestosItem").text( (parseFloat(((descuento * (subtotal_producto * impuesto_producto)) / 100) - ((descuento * (subtotal_producto)) / 100)).toFixed(2)).toString().split(". ") );
          fila.find(".txt-Ss_Precio").val((parseFloat(fValorUnitario * impuesto_producto).toFixed(6)).toString().split(". "));
          fila.find(".txt-Ss_Total_Producto").val((parseFloat(subtotal_producto * impuesto_producto).toFixed(2)).toString().split(". "));
          
          fila.find(".txt-Ss_SubTotal_Producto").val(parseFloat(subtotal_producto).toFixed(2));

          var $Ss_SubTotal = 0.00;
          var $Ss_Exonerada = 0.00;
          var $Ss_Inafecto = 0.00;
          var $Ss_Gratuita = 0.00;
          var $Ss_IGV = 0.00;
          var $Ss_Descuento = 0.00;
          var $Ss_Total = 0.00;
          $("#table-DetalleProductosVenta > tbody > tr").each(function(){
            var rows = $(this);
            var Ss_Impuesto           = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
            var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
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
            
            $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto) )) / 100);
            $Ss_Total += parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
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
        } else if (nu_tipo_impuesto == 3){//Exonerada
          fila.find(".td-fDescuentoSinImpuestosItem").text( (parseFloat(((descuento * (subtotal_producto)) / 100)).toFixed(2)).toString().split(". ") );
          fila.find(".td-fDescuentoImpuestosItem").text( (parseFloat(((descuento * (subtotal_producto * impuesto_producto)) / 100) - ((descuento * (subtotal_producto)) / 100)).toFixed(2)).toString().split(". ") );
          fila.find(".txt-Ss_Precio").val((parseFloat(fValorUnitario * impuesto_producto).toFixed(6)).toString().split(". "));
          fila.find(".txt-Ss_Total_Producto").val((parseFloat(subtotal_producto * impuesto_producto).toFixed(2)).toString().split(". "));

          fila.find(".txt-Ss_SubTotal_Producto").val(parseFloat(subtotal_producto).toFixed(2));

          var $Ss_SubTotal = 0.00;
          var $Ss_Exonerada = 0.00;
          var $Ss_Inafecto = 0.00;
          var $Ss_Gratuita = 0.00;
          var $Ss_IGV = 0.00;
          var $Ss_Descuento = 0.00;
          var $Ss_Total = 0.00;
          $("#table-DetalleProductosVenta > tbody > tr").each(function(){
            var rows = $(this);
            var Ss_Impuesto           = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
            var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
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
            
            $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto) )) / 100);
            $Ss_Total += parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
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
        } else if (nu_tipo_impuesto == 4){//Gratuita
          fila.find(".td-fDescuentoSinImpuestosItem").text( (parseFloat(((descuento * (subtotal_producto)) / 100)).toFixed(2)).toString().split(". ") );
          fila.find(".td-fDescuentoImpuestosItem").text( (parseFloat(((descuento * (subtotal_producto * impuesto_producto)) / 100) - ((descuento * (subtotal_producto)) / 100)).toFixed(2)).toString().split(". ") );
          fila.find(".txt-Ss_Precio").val((parseFloat(fValorUnitario * impuesto_producto).toFixed(6)).toString().split(". ") );
          fila.find(".txt-Ss_Total_Producto").val((parseFloat(subtotal_producto * impuesto_producto).toFixed(2)).toString().split(". "));

          fila.find(".txt-Ss_SubTotal_Producto").val(parseFloat(subtotal_producto).toFixed(2));

          var $Ss_SubTotal = 0.00;
          var $Ss_Exonerada = 0.00;
          var $Ss_Inafecto = 0.00;
          var $Ss_Gratuita = 0.00;
          var $Ss_IGV = 0.00;
          var $Ss_Descuento = 0.00;
          var $Ss_Total = 0.00;
          $("#table-DetalleProductosVenta > tbody > tr").each(function(){
            var rows = $(this);
            var Ss_Impuesto           = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
            var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
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
            
            $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto) )) / 100);
            $Ss_Total += parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
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
  
  $('#table-DetalleProductosVenta tbody' ).on('input', '.txt-Ss_Descuento', function(){
    if ($('#txt-Ss_Descuento').val().length == 0 ) {
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
          if (nu_tipo_impuesto == 1){//CON IGV
            fDescuento_SubTotal_Producto = parseFloat(((precio * cantidad) - (((descuento * (precio * cantidad)) / 100))) / impuesto_producto );
            fDescuento_Total_Producto = parseFloat(((precio * cantidad) - (((descuento * (precio * cantidad)) / 100))));
            fila.find(".td-fDescuentoSinImpuestosItem").text( (parseFloat((((descuento * (precio * cantidad)) / 100) / impuesto_producto)).toFixed(2)).toString().split(". ") );
            fila.find(".td-fDescuentoImpuestosItem").text( (parseFloat(((descuento * (precio * cantidad)) / 100) - (((descuento * (precio * cantidad)) / 100) / impuesto_producto)).toFixed(2)).toString().split(". ") );
            fila.find(".txt-Ss_SubTotal_Producto").val( (parseFloat(fDescuento_SubTotal_Producto).toFixed(6)).toString().split(". ") );
            fila.find(".txt-Ss_Total_Producto").val( (parseFloat(fDescuento_Total_Producto).toFixed(2)).toString().split(". ") );
            
            var $Ss_SubTotal = 0.00;
            var $Ss_Descuento = 0.00;
            var $Ss_IGV = 0.00;
            var $Ss_Total = 0.00;
            var $fDescuento_Producto = 0;
            $("#table-DetalleProductosVenta > tbody > tr").each(function(){
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
              
              $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto) )) / 100);
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
            fila.find(".td-fDescuentoSinImpuestosItem").text( (parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". ") );
            fila.find(".td-fDescuentoImpuestosItem").text( (parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". ") );
            fila.find(".txt-Ss_SubTotal_Producto").val( (parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". ") );
            fila.find(".txt-Ss_Total_Producto").val( (parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". ") );

            var $Ss_Inafecto = 0.00;
            var $Ss_Descuento = 0.00;
            var $Ss_Total = 0.00;
            $("#table-DetalleProductosVenta > tbody > tr").each(function(){
              var rows = $(this);
              var Ss_Impuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
              var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
              var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
              var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());
              var $Ss_Total_Producto = parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
    
              if(isNaN($Ss_Descuento_Producto))
                $Ss_Descuento_Producto = 0;
                
              if (Nu_Tipo_Impuesto == 2)
                $Ss_Inafecto += $Ss_SubTotal_Producto;
              
              $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto) )) / 100);
              $Ss_Total += parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
            });
            
            $( '#txt-inafecto' ).val( $Ss_Inafecto.toFixed(2) );
            $( '#span-inafecto' ).text( $Ss_Inafecto.toFixed(2) );
            
            $( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
            $( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
            
            $( '#txt-total' ).val( $Ss_Total.toFixed(2) );
            $( '#span-total' ).text( $Ss_Total.toFixed(2) );
            $( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
          } else if (nu_tipo_impuesto == 3){//Exonerada
            fila.find(".td-fDescuentoSinImpuestosItem").text( (parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". ") );
            fila.find(".td-fDescuentoImpuestosItem").text( (parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". ") );
            fila.find(".txt-Ss_SubTotal_Producto").val( (parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". ") );
            fila.find(".txt-Ss_Total_Producto").val( (parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". ") );

            var $Ss_Exonerada = 0.00;
            var $Ss_Descuento = 0.00;
            var $Ss_Total = 0.00;
            $("#table-DetalleProductosVenta > tbody > tr").each(function(){
              var rows = $(this);
              var Ss_Impuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
              var Nu_Tipo_Impuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
              var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
              var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());
              var $Ss_Total_Producto = parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
    
              if(isNaN($Ss_Descuento_Producto))
                $Ss_Descuento_Producto = 0;
                
              if (Nu_Tipo_Impuesto == 3)
                $Ss_Exonerada += $Ss_SubTotal_Producto;
              
              $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto) )) / 100);
              $Ss_Total += parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
            });
            
            $( '#txt-exonerada' ).val( $Ss_Exonerada.toFixed(2) );
            $( '#span-exonerada' ).text( $Ss_Exonerada.toFixed(2) );
            
            $( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
            $( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
            
            $( '#txt-total' ).val( $Ss_Total.toFixed(2) );
            $( '#span-total' ).text( $Ss_Total.toFixed(2) );
            $( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
          } else if (nu_tipo_impuesto == 4){//Gratuita
            fila.find(".td-fDescuentoSinImpuestosItem").text( (parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". ") );
            fila.find(".td-fDescuentoImpuestosItem").text( (parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". ") );
            fila.find(".txt-Ss_SubTotal_Producto").val( (parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". ") );
            fila.find(".txt-Ss_Total_Producto").val( (parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". ") );

            var $Ss_Gratuita = 0.00;
            var $Ss_Descuento = 0.00;
            var $Ss_Total = 0.00;
            $("#table-DetalleProductosVenta > tbody > tr").each(function(){
              var rows = $(this);
              var Ss_Impuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
              var Nu_Tipo_Impuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
              var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
              var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());
              var $Ss_Total_Producto = parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
    
              if(isNaN($Ss_Descuento_Producto))
                $Ss_Descuento_Producto = 0;
                
              if (Nu_Tipo_Impuesto == 4)
                $Ss_Gratuita += $Ss_SubTotal_Producto;
              
              $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto) )) / 100);
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
          calcularIcbper();
          calcularDescuentoTotal(0);
        }
      }
    } else {
      $(this).val('');
      alert('No puedes tener doble descuento por ítem y global, solo escoger uno');
    }// if else descuento total
  })// descuento por item campo

  $('#table-DetalleProductosVenta tbody' ).on('input', '.txt-Ss_Total_Producto', function(){
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
      $( '#table-DetalleProductosVenta tfoot' ).empty();
      if (nu_tipo_impuesto == 1){//IGV
        if ($('#hidden-iTipoRubroEmpresa').val() != 13) {//13=Grifos
          fila.find(".txt-Ss_Precio").val( (parseFloat(total_producto / cantidad).toFixed(6)).toString().split(". ") );
          fila.find(".txt-Ss_SubTotal_Producto").val( (parseFloat(total_producto / impuesto_producto).toFixed(6)).toString().split(". ") );
          fila.find(".txt-fValorUnitario").val(parseFloat(fila.find(".txt-Ss_Precio").val() / impuesto_producto).toFixed(6));

          var $Ss_SubTotal = 0.00;
          var $Ss_Descuento = 0.00;
          var $Ss_IGV = 0.00;
          var $Ss_Total = 0.00;
          $("#table-DetalleProductosVenta > tbody > tr").each(function(){
            var rows = $(this);
            var Ss_Impuesto           = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
            var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
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
            
            $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto) )) / 100);
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
        } else {
          fila.find(".txt-Qt_Producto").val((parseFloat(total_producto / precio).toFixed(2)).toString().split(". "));
          fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat(total_producto / impuesto_producto).toFixed(6)).toString().split(". "));

          var $Ss_SubTotal = 0.00;
          var $Ss_Descuento = 0.00;
          var $Ss_IGV = 0.00;
          var $Ss_Total = 0.00;
          $("#table-DetalleProductosVenta > tbody > tr").each(function () {
            var rows = $(this);
            var Ss_Impuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
            var Nu_Tipo_Impuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
            var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
            var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());
            var $Ss_Total_Producto = parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());

            $Ss_Total += $Ss_Total_Producto;

            if (isNaN($Ss_Descuento_Producto))
              $Ss_Descuento_Producto = 0;

            if (Nu_Tipo_Impuesto == 1) {
              $Ss_SubTotal += $Ss_SubTotal_Producto;
              $Ss_IGV += $Ss_Total_Producto - $Ss_SubTotal_Producto;
            }

            $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto))) / 100);
          });

          $('#txt-subTotal').val($Ss_SubTotal.toFixed(2));
          $('#span-subTotal').text($Ss_SubTotal.toFixed(2));

          $('#txt-descuento').val($Ss_Descuento.toFixed(2));
          $('#span-descuento').text($Ss_Descuento.toFixed(2));

          $('#txt-impuesto').val($Ss_IGV.toFixed(2));
          $('#span-impuesto').text($Ss_IGV.toFixed(2));

          $('#txt-total').val($Ss_Total.toFixed(2));
          $('#span-total').text($Ss_Total.toFixed(2));
          $( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
        }
      } else if (nu_tipo_impuesto == 2) {//Inafecto
        if ($('#hidden-iTipoRubroEmpresa').val() != 13) {//13=Grifos
          fila.find(".txt-Ss_Precio").val( (parseFloat((total_producto / cantidad) / impuesto_producto).toFixed(6)).toString().split(". ") );
          fila.find(".txt-Ss_SubTotal_Producto").val( (parseFloat(total_producto / impuesto_producto).toFixed(2)).toString().split(". ") );
          fila.find(".txt-fValorUnitario").val(parseFloat(fila.find(".txt-Ss_Precio").val()).toFixed(6));

          var $Ss_Inafecto = 0.00;
          var $Ss_Descuento = 0.00;
          var $Ss_Total = 0.00;
          $("#table-DetalleProductosVenta > tbody > tr").each(function(){
            var rows = $(this);
            var Ss_Impuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
            var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
            var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
            var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());

            if(isNaN($Ss_Descuento_Producto))
              $Ss_Descuento_Producto = 0;
            
            if (Nu_Tipo_Impuesto == 2)
              $Ss_Inafecto += $Ss_SubTotal_Producto;
            
            $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto) )) / 100);
            $Ss_Total += parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
          });
          
          $( '#txt-inafecto' ).val( $Ss_Inafecto.toFixed(2) );
          $( '#span-inafecto' ).text( $Ss_Inafecto.toFixed(2) );
          
          $( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
          $( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
          
          $( '#txt-total' ).val( $Ss_Total.toFixed(2) );
          $( '#span-total' ).text( $Ss_Total.toFixed(2) );
          $( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
        } else {
          fila.find(".txt-Qt_Producto").val((parseFloat(total_producto / precio).toFixed(2)).toString().split(". "));
          fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat(total_producto / impuesto_producto).toFixed(6)).toString().split(". "));

          var $Ss_Inafecto = 0.00;
          var $Ss_Descuento = 0.00;
          var $Ss_Total = 0.00;
          $("#table-DetalleProductosVenta > tbody > tr").each(function () {
            var rows = $(this);
            var Ss_Impuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
            var Nu_Tipo_Impuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
            var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
            var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());

            if (isNaN($Ss_Descuento_Producto))
              $Ss_Descuento_Producto = 0;

            if (Nu_Tipo_Impuesto == 2)
              $Ss_Inafecto += $Ss_SubTotal_Producto;

            $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto))) / 100);
            $Ss_Total += parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
          });

          $('#txt-inafecto').val($Ss_Inafecto.toFixed(2));
          $('#span-inafecto').text($Ss_Inafecto.toFixed(2));

          $('#txt-descuento').val($Ss_Descuento.toFixed(2));
          $('#span-descuento').text($Ss_Descuento.toFixed(2));

          $('#txt-total').val($Ss_Total.toFixed(2));
          $('#span-total').text($Ss_Total.toFixed(2));
          $( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );

        }
      } else if (nu_tipo_impuesto == 3) {//Exonerada
        if ($('#hidden-iTipoRubroEmpresa').val() != 13) {//13=Grifos
          fila.find(".txt-Ss_Precio").val( (parseFloat((total_producto / cantidad) / impuesto_producto).toFixed(6)).toString().split(". ") );
          fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat(total_producto / impuesto_producto).toFixed(2)).toString().split(". "));
          fila.find(".txt-fValorUnitario").val(parseFloat(fila.find(".txt-Ss_Precio").val()).toFixed(6));
          
          var $Ss_Exonerada = 0.00;
          var $Ss_Descuento = 0.00;
          var $Ss_IGV = 0.00;
          var $Ss_Total = 0.00;
          $("#table-DetalleProductosVenta > tbody > tr").each(function(){
            var rows = $(this);
            var Ss_Impuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
            var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
            var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
            var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());

            if(isNaN($Ss_Descuento_Producto))
              $Ss_Descuento_Producto = 0;
            
            if (Nu_Tipo_Impuesto == 3)
              $Ss_Exonerada += $Ss_SubTotal_Producto;
            
            $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto) )) / 100);
            $Ss_Total += parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
          });
          
          $( '#txt-exonerada' ).val( $Ss_Exonerada.toFixed(2) );
          $( '#span-exonerada' ).text( $Ss_Exonerada.toFixed(2) );
          
          $( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
          $( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
          
          $( '#txt-total' ).val( $Ss_Total.toFixed(2) );
          $( '#span-total' ).text( $Ss_Total.toFixed(2) );
          $( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
        } else {
          fila.find(".txt-Qt_Producto").val((parseFloat(total_producto / precio).toFixed(2)).toString().split(". "));
          fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat(total_producto / impuesto_producto).toFixed(6)).toString().split(". "));

          var $Ss_Exonerada = 0.00;
          var $Ss_Descuento = 0.00;
          var $Ss_IGV = 0.00;
          var $Ss_Total = 0.00;
          $("#table-DetalleProductosVenta > tbody > tr").each(function () {
            var rows = $(this);
            var Ss_Impuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
            var Nu_Tipo_Impuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
            var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
            var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());

            if (isNaN($Ss_Descuento_Producto))
              $Ss_Descuento_Producto = 0;

            if (Nu_Tipo_Impuesto == 3)
              $Ss_Exonerada += $Ss_SubTotal_Producto;

            $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto))) / 100);
            $Ss_Total += parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
          });

          $('#txt-exonerada').val($Ss_Exonerada.toFixed(2));
          $('#span-exonerada').text($Ss_Exonerada.toFixed(2));

          $('#txt-descuento').val($Ss_Descuento.toFixed(2));
          $('#span-descuento').text($Ss_Descuento.toFixed(2));

          $('#txt-total').val($Ss_Total.toFixed(2));
          $('#span-total').text($Ss_Total.toFixed(2));
          $( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
        }
      } else if (nu_tipo_impuesto == 4) {//Gratuita
        if ($('#hidden-iTipoRubroEmpresa').val() != 13) {//13=Grifos
          fila.find(".txt-Ss_Precio").val( (parseFloat((total_producto / cantidad) / impuesto_producto).toFixed(6)).toString().split(". ") );
          fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat(total_producto / impuesto_producto).toFixed(2)).toString().split(". "));
          fila.find(".txt-fValorUnitario").val(parseFloat(fila.find(".txt-Ss_Precio").val()).toFixed(6));
          
          var $Ss_Gratuita = 0.00;
          var $Ss_Descuento = 0.00;
          var $Ss_IGV = 0.00;
          var $Ss_Total = 0.00;
          $("#table-DetalleProductosVenta > tbody > tr").each(function(){
            var rows = $(this);
            var Ss_Impuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
            var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
            var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
            var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());

            if(isNaN($Ss_Descuento_Producto))
              $Ss_Descuento_Producto = 0;
            
            if (Nu_Tipo_Impuesto == 4)
              $Ss_Gratuita += $Ss_SubTotal_Producto;
            
            $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto) )) / 100);
            $Ss_Total += parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
          });
          
          $( '#txt-gratuita' ).val( $Ss_Gratuita.toFixed(2) );
          $( '#span-gratuita' ).text( $Ss_Gratuita.toFixed(2) );
          
          $( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
          $( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
          
          $( '#txt-total' ).val( $Ss_Total.toFixed(2) );
          $( '#span-total' ).text( $Ss_Total.toFixed(2) );
          $( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
        } else {
          fila.find(".txt-Qt_Producto").val((parseFloat(total_producto / precio).toFixed(2)).toString().split(". "));
          fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat(total_producto / impuesto_producto).toFixed(6)).toString().split(". "));

          var $Ss_Gratuita = 0.00;
          var $Ss_Descuento = 0.00;
          var $Ss_IGV = 0.00;
          var $Ss_Total = 0.00;
          $("#table-DetalleProductosVenta > tbody > tr").each(function () {
            var rows = $(this);
            var Ss_Impuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
            var Nu_Tipo_Impuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
            var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
            var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());

            if (isNaN($Ss_Descuento_Producto))
              $Ss_Descuento_Producto = 0;

            if (Nu_Tipo_Impuesto == 4)
              $Ss_Gratuita += $Ss_SubTotal_Producto;

            $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto))) / 100);
            $Ss_Total += parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
          });

          $('#txt-gratuita').val($Ss_Gratuita.toFixed(2));
          $('#span-gratuita').text($Ss_Gratuita.toFixed(2));

          $('#txt-descuento').val($Ss_Descuento.toFixed(2));
          $('#span-descuento').text($Ss_Descuento.toFixed(2));

          $('#txt-total').val($Ss_Total.toFixed(2));
          $('#span-total').text($Ss_Total.toFixed(2));
          $( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
        }
  		}
      calcularIcbper();
      calcularDescuentoTotal(0);
    }
  })
  
	$( '#table-DetalleProductosVenta tbody' ).on('click', '#btn-deleteProducto', function(){
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
    $("#table-DetalleProductosVenta > tbody > tr").each(function(){
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
        
      $Ss_Descuento_p += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / fImpuesto) )) / 100);
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
        $Ss_Gratuita = $Ss_Gratuita - $Ss_Descuento_Gratuita;
      }
      
      $Ss_Total = ($Ss_SubTotal * globalImpuesto) + $Ss_Inafecto + $Ss_Exonerada + $Ss_Gratuita;
      $Ss_Descuento = $Ss_Descuento_Gravadas + $Ss_Descuento_Inafecto + $Ss_Descuento_Gratuita;
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
    $( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
		
    if ($( '#table-DetalleProductosVenta >tbody >tr' ).length == 0) {
	    $( '#table-DetalleProductosVenta' ).hide();
      $('#txt-Ss_Descuento').val('');
    }

    calcularIcbper();
    calcularDescuentoTotal(0);
	})
	
  //Calcular porcentaje - Pendiente con fio, cuando tengo varios items y algunos son inafectos ó igv.
  $('#table-VentaTotal tbody').on('input', '#txt-Ss_Descuento', function () {
    var $Ss_Descuento_Producto_Detail_Total = 0;
    $("#table-DetalleProductosVenta > tbody > tr").each(function () {
      var rows = $(this);
      $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());
      if (isNaN($Ss_Descuento_Producto))
        $Ss_Descuento_Producto = 0;
      $Ss_Descuento_Producto_Detail_Total += $Ss_Descuento_Producto;
    })

    if ($Ss_Descuento_Producto_Detail_Total == 0) {
      calcularDescuentoTotal($(this).parents("tr"));
    } else {
      alert('No se puede brindar descuento por ítem y total, solo escoger uno');
      $('#txt-Ss_Descuento').val('');
    }
  })
})

function isExistTableTemporalProducto($ID_Producto){
  return Array.from($('tr[id*=tr_detalle_producto]'))
    .some(element => ($('td:nth(0)',$(element)).html()===$ID_Producto));
}

function form_Venta(){
  if (accion == 'add_factura_venta' || accion == 'upd_factura_venta') {//Accion para validar tecla ENTER
    var arrDetalleVenta = [], arrValidarNumerosEnCero = [], $counterNumerosEnCero = 0, tr_foot = '';
    
    $("#table-DetalleProductosVenta > tbody > tr").each(function(){
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
      
      obj.ID_Producto = $ID_Producto;
      obj.fValorUnitario = $fValorUnitario;
      obj.Ss_Precio	= $Ss_Precio;
      obj.Qt_Producto	= $Qt_Producto;
      obj.ID_Impuesto_Cruce_Documento	= $ID_Impuesto_Cruce_Documento;
      obj.Ss_SubTotal	= $Ss_SubTotal;
      obj.Ss_Descuento = $Ss_Descuento;
      obj.Ss_Impuesto	= $Ss_Total - $Ss_SubTotal;
      obj.Ss_Total = $Ss_Total;
      obj.Nu_Lote_Vencimiento = $Nu_Lote_Vencimiento;
      obj.Fe_Lote_Vencimiento = $Fe_Lote_Vencimiento;
      obj.fDescuentoSinImpuestosItem = $fDescuentoSinImpuestosItem;
      obj.fDescuentoImpuestosItem = $fDescuentoImpuestosItem;
      obj.fIcbperItem = $fIcbperItem;
      obj.Txt_Nota = $sNotaItem;
      arrDetalleVenta.push(obj);
      $counterNumerosEnCero++;
    });
    
    bEstadoValidacion = validatePreviousDocumentToSaveSale();

    if ( arrDetalleVenta.length === 0){
  		$( '#panel-DetalleProductosVenta' ).removeClass('panel-default');
  		$( '#panel-DetalleProductosVenta' ).addClass('panel-danger');
  		
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
      $( '#table-DetalleProductosVenta > tbody' ).after(tr_foot);
    } else if (bEstadoValidacion) {
      if ($('[name="addCliente"]:checked').attr('value') == 1){//Agregar cliente
        if ( $( '#cbo-Estado' ).val() == 1 ) {//1 = Activo
          saveFacturaVenta(arrDetalleVenta);
        } else {
          $('#modal-message').modal('show');
          $('.modal-message').removeClass('modal-danger modal-warning modal-success');
          $( '.modal-message' ).addClass('modal-danger');
          $( '.modal-title-message' ).text( 'El cliente se encuentra con BAJA DE OFICIO / NO HABIDO' );
          setTimeout(function() {$('#modal-message').modal('hide');}, 2500);
        }
      } else {
        var sNombreEntidad = $( '#txt-AID' ).val() == 1 ? 'clientes varios' : '';
        var arrPOST = {
          sTipoData : 'get_entidad',
          iTipoEntidad : '0',//0=Cliente
          iIDEntidad : $( '#txt-AID' ).val(),
          sNombreEntidad : sNombreEntidad,
        };
        url = base_url + 'HelperController/getDataGeneral';
        $.post(url, arrPOST, function(response){
          $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
          if ( response.sStatus == 'success' ) {// Si el RUC es válido
            if ( response.arrData[0].Nu_Estado == '1' ){
              saveFacturaVenta(arrDetalleVenta);
            } else if ( response.arrData[0].Nu_Estado != '1' ){
              $('#modal-message').modal('show');
              $('.modal-message').removeClass('modal-danger modal-warning modal-success');
              $( '.modal-message' ).addClass('modal-danger');
              $( '.modal-title-message' ).text( 'El cliente se encuentra con BAJA DE OFICIO / NO HABIDO' );
              setTimeout(function() {$('#modal-message').modal('hide');}, 2500);
            }
          } else {
            if( response.sMessageSQL !== undefined ) {
              console.log(response.sMessageSQL);
            }
            $( '#modal-message' ).modal('show');
            $( '.modal-message' ).addClass(response.sClassModal);
            $( '.modal-title-message' ).text(response.sMessage + ' ' + sNombreEntidad);
            setTimeout(function() {$('#modal-message').modal('hide');}, 2500);
          }
        }, 'json');// Obtener informacion de una entidad, para saber si esta HABIDO y SIN BAJA DE OFICIO
      }// /. Verificar si es un cliente existente o nuevo
    }
  }
}

function reload_table_venta(){
  table_venta.ajax.reload(null,false);
}

function _eliminarFacturaVenta($modal_delete, ID, Nu_Enlace, Nu_Descargar_Inventario){
  $( '#modal-loader' ).modal('show');
  
  url = base_url + 'Ventas/VentaController/eliminarVenta/' + ID + '/' + Nu_Enlace + '/' + Nu_Descargar_Inventario;
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
  	    reload_table_venta();
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

function anularFacturaVenta(ID, Nu_Enlace, Nu_Descargar_Inventario, accion, iEstado, sTipoBajaSunat, dEmision, sSerieDocumento) {
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
      _anularFacturaVenta($modal_delete, ID, Nu_Enlace, Nu_Descargar_Inventario, iEstado, sTipoBajaSunat, dEmision, sSerieDocumento);
      accion = '';
    }
  });

  $('#btn-save-delete').off('click').click(function () {
    _anularFacturaVenta($modal_delete, ID, Nu_Enlace, Nu_Descargar_Inventario, iEstado, sTipoBajaSunat, dEmision, sSerieDocumento);
  });
}

function _anularFacturaVenta($modal_delete, ID, Nu_Enlace, Nu_Descargar_Inventario, iEstado, sTipoBajaSunat, dEmision, sSerieDocumento){
  $( '#modal-loader' ).modal('show');
    
  url = base_url + 'Ventas/VentaController/anularVenta/' + ID + '/' + Nu_Enlace + '/' + Nu_Descargar_Inventario + '/' + iEstado + '/' + sTipoBajaSunat + '/' + dEmision + '/' + sSerieDocumento;
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
		  } else {
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 3100);
      }
      reload_table_venta();
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

function sendFacturaVentaSunat(ID, Nu_Estado, sTipoBajaSunat){
  var $modal_delete = $( '.modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '.modal-message-delete' ).removeClass('modal-danger modal-warning modal-success');
  $( '.modal-message-delete' ).addClass('modal-success');
  
  $( '.modal-title-message-delete' ).text('¿Enviar a sunat?');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $( '#btn-save-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
    
    $( '#btn-sunat-' + ID ).text('');
    $( '#btn-sunat-' + ID ).attr('disabled', true);
    $( '#btn-sunat-' + ID ).append( 'Enviando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
      
    _sendFacturaVentaSunat(ID, Nu_Estado, 'json', sTipoBajaSunat);
  });
}

function _sendFacturaVentaSunat(ID, Nu_Estado, sTypeResponse, sTipoBajaSunat){
  url = base_url + 'Ventas/VentaController/sendFacturaVentaSunat/' + ID + '/' + Nu_Estado + '/' + sTypeResponse + '/' + sTipoBajaSunat;
  $.ajax({
    url       : url,
    type      : "GET",
    dataType  : "JSON",
    success: function( response ){
      $( '.modal-title-message' ).text( '' );
	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
  	  $( '#modal-message' ).modal('show');
		  
		  if (response.status == 'success'){
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    reload_table_venta();
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1200);
		  } else {
  	    $( '.modal-message' ).addClass(response.style_modal);
        $( '.modal-title-message' ).text( (response.message_nubefact!=null ? response.message_nubefact : response.message) );
        console.log( response.arrMessagePSE );
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 4100);
  	    reload_table_venta();
      }
      
      $( '#btn-sunat-' + ID ).text('');
      $( '#btn-sunat-' + ID ).attr('disabled', false);
      $( '#btn-sunat-' + ID ).append( '<i class="fa fa-cloud-upload"></i> Sunat' );
    }
  });
}

function consultarDocumentoElectronicoSunat(ID, iEstado) {
  $('#btn-sunat-cdr-' + ID).attr('disabled', true);
  $('#span-sunat-cdr-' + ID).append('<i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>');

  var $modal_id = $('#modal-correo_sunat');

  var sendPost = {
    ID: ID,
    iEstado: iEstado,
    sTipoRespuesta: 'json',
  };

  url = base_url + 'DocumentoElectronicoController/consultarDocumentoElectronicoSunat';
  $.ajax({
    url: url,
    type: "POST",
    dataType: "JSON",
    data: sendPost,
    success: function (response) {
      $modal_id.modal('hide');

      $('.modal-message').removeClass('modal-danger modal-warning modal-success');
      $('#modal-message').modal('show');

      $('#btn-sunat-cdr-' + ID).attr('disabled', false);

      $('#span-sunat-cdr-' + ID).html('');

      if (response.status == 'success') {
        $('#txt-email_correo_sunat').val('');

        $('.modal-message').addClass(response.style_modal);
        $('.modal-title-message').text(response.message);
        setTimeout(function () { $('#modal-message').modal('hide'); }, 1200);
        reload_table_venta();
      } else {
        $('.modal-message').addClass(response.style_modal);
        $('.modal-title-message').text(response.message);
        setTimeout(function () { $('#modal-message').modal('hide'); }, 1800);
        reload_table_venta();
      }
    }
  });
}

function sendCorreoFacturaVentaSUNAT(ID, iIdCliente){
  var $modal_id = $( '#modal-correo_sunat' );
  $modal_id.modal('show');
  
  $( '#modal-correo_sunat' ).removeClass('modal-danger modal-warning modal-success');
  $( '#modal-correo_sunat' ).addClass('modal-success');
  
  $( '.modal-header_message_correo_sunat' ).text('¿Enviar correo?');
  
  // get cliente
	$( '#txt-email_correo_sunat' ).val( '' );
  url = base_url + 'HelperController/getDataGeneral';
  $.post( url, {sTipoData: 'get_entidad', iTipoEntidad: 0, iIDEntidad: iIdCliente}, function( response ){
    if ( response.sStatus == 'success' ) {
		  $( '#txt-email_correo_sunat' ).val( response.arrData[0].Txt_Email_Entidad );
    } else {
      if( response.sMessageSQL !== undefined ) {
        console.log(response.sMessageSQL);
      }
      console.log(response.sMessage);
    }
  }, 'JSON');
  // /. get cliente

	$( '#modal-correo_sunat' ).on('shown.bs.modal', function() {
		$( '#txt-email_correo_sunat' ).focus();
	})
  
  $( '#btn-modal_message_correo_sunat-cancel' ).off('click').click(function () {
    $modal_id.modal('hide');
  });

	$( "#txt-email_correo_sunat" ).blur(function() {
		caracteresCorreoValido($(this).val(), '#span-email');
  })
  
  $( '#btn-modal_message_correo_sunat-send' ).off('click').click(function () {
    if (!caracteresCorreoValido($('#txt-email_correo_sunat').val(), '#div-email') ) {
      scrollToError($('#modal-correo_sunat .modal-body'), $( '#txt-email_correo_sunat' ));
    } else {
      $( '#btn-modal_message_correo_sunat-send' ).text('');
      $( '#btn-modal_message_correo_sunat-send' ).attr('disabled', true);
      $( '#btn-modal_message_correo_sunat-send' ).append( 'Enviando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
        
      var sendPost = {
        ID : ID,
        Txt_Email : $( '#txt-email_correo_sunat' ).val(),
      };
      
      url = base_url + 'Ventas/VentaController/sendCorreoFacturaVentaSUNAT/';
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
    		    $( '#txt-email_correo_sunat' ).val( '' );
    		    
      	    $( '.modal-message' ).addClass(response.style_modal);
      	    $( '.modal-title-message' ).text(response.message);
      	    setTimeout(function() {$('#modal-message').modal('hide');}, 1200);
    		  } else {
      	    $( '.modal-message' ).addClass(response.style_modal);
      	    $( '.modal-title-message' ).text(response.message);
      	    setTimeout(function() {$('#modal-message').modal('hide');}, 1800);
          }
          
          $( '#btn-modal_message_correo_sunat-send' ).text('');
          $( '#btn-modal_message_correo_sunat-send' ).attr('disabled', false);
          $( '#btn-modal_message_correo_sunat-send' ).append( 'Enviar' );
        }
      });
    }
  });
}

function validatePreviousDocumentToSaveSale(){
  var bEstadoValidacion = true;
  
  $('.form-group').removeClass('has-error');
  $('.help-block').empty();

  var dEmisionCompare = $('#txt-Fe_Emision').val().split("/");
  var dEmisionCompare = new Date(parseInt(dEmisionCompare[2]), parseInt(dEmisionCompare[1] - 1), parseInt(dEmisionCompare[0]));

  var dVencimiento = $('#txt-Fe_Vencimiento').val().split("/");
  var dVencimiento = new Date(parseInt(dVencimiento[2]), parseInt(dVencimiento[1] - 1), parseInt(dVencimiento[0]));

	if ( $( '#cbo-TiposDocumento' ).val() == 0){
    $( '#cbo-TiposDocumento' ).closest('.form-group').find('.help-block').html('Seleccionar tipo');
    $( '#cbo-TiposDocumento' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    
    bEstadoValidacion = false;
		scrollToError( $("html, body"), $( '#cbo-TiposDocumento' ) );
  } else if ( $( '#cbo-SeriesDocumento' ).val() == 0){
    $( '#cbo-SeriesDocumento' ).closest('.form-group').find('.help-block').html('Seleccionar serie');
    $( '#cbo-SeriesDocumento' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    
    bEstadoValidacion = false;
		scrollToError( $("html, body"), $( '#cbo-SeriesDocumento' ) );
  } else if ( $('[name="addCliente"]:checked').attr('value') == 0 && ($( '#txt-AID' ).val().length === 0 || $( '#txt-ANombre' ).val().length === 0)) {
    $( '#txt-ANombre' ).closest('.form-group').find('.help-block').html('Seleccionar cliente');
		$( '#txt-ANombre' ).closest('.form-group').removeClass('has-success').addClass('has-error');
		
    bEstadoValidacion = false;
		scrollToError( $("html, body"), $( '#txt-ANombre' ) );
  //} else if ( $( '#cbo-TiposDocumentoIdentidadCliente' ).val() != 1 && $( '#cbo-TiposDocumentoIdentidadCliente' ).val() != 2 && ($('[name="addCliente"]:checked').attr('value') == 1 && $( '#cbo-TiposDocumentoIdentidadCliente' ).find(':selected').data('nu_cantidad_caracteres') != $( '#txt-Nu_Documento_Identidad_Cliente').val().length) ) {
  } else if ($('#cbo-TiposDocumentoIdentidadCliente').val() == 4 && ($('[name="addCliente"]:checked').attr('value') == 1 && $('#cbo-TiposDocumentoIdentidadCliente').find(':selected').data('nu_cantidad_caracteres') != $('#txt-Nu_Documento_Identidad_Cliente').val().length)) {
    $( '#txt-Nu_Documento_Identidad_Cliente' ).closest('.form-group').find('.help-block').html('Debe ingresar ' + $( '#cbo-TiposDocumentoIdentidadCliente' ).find(':selected').data('nu_cantidad_caracteres') + ' dígitos' );
	  $( '#txt-Nu_Documento_Identidad_Cliente' ).closest('.form-group').removeClass('has-success').addClass('has-error');
	  
    bEstadoValidacion = false;
		scrollToError( $("html, body"), $( '#txt-Nu_Documento_Identidad_Cliente' ) );
  } else if ( $( '#cbo-almacen' ).val() == 0 ){
    $( '#cbo-almacen' ).closest('.form-group').find('.help-block').html('Seleccionar almacén');
    $( '#cbo-almacen' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    
    bEstadoValidacion = false;
		scrollToError( $("html, body"), $( '#cbo-almacen' ) );
  } else if ($('[name="ID_Documento_Cabecera_Orden"]').val().length == 0 && ($('#cbo-TiposDocumento').val() == 5 || $('#cbo-TiposDocumento').val() == 6) && nu_enlace == 1 && $( '#cbo-TiposDocumentoModificar' ).val() == 0){
    $( '#cbo-TiposDocumentoModificar' ).closest('.form-group').find('.help-block').html('Seleccionar tipo');
    $( '#cbo-TiposDocumentoModificar' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    
    bEstadoValidacion = false;
		scrollToError( $("html, body"), $( '#cbo-TiposDocumentoModificar' ) );
  } else if ($('[name="ID_Documento_Cabecera_Orden"]').val().length == 0 && ($('#cbo-TiposDocumento').val() == 5 || $('#cbo-TiposDocumento').val() == 6) && nu_enlace == 1 && $( '#cbo-SeriesDocumentoModificar' ).val() == 0){
    $( '#cbo-SeriesDocumentoModificar' ).closest('.form-group').find('.help-block').html('Seleccionar serie');
    $( '#cbo-SeriesDocumentoModificar' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    
    bEstadoValidacion = false;
		scrollToError( $("html, body"), $( '#cbo-SeriesDocumentoModificar' ) );
  } else if ($('[name="ID_Documento_Cabecera_Orden"]').val().length == 0 && ($('#cbo-TiposDocumento').val() == 5 || $('#cbo-TiposDocumento').val() == 6) && nu_enlace == 1 && $( '#txt-ID_Numero_Documento_Modificar' ).val().length === 0){
    $( '#txt-ID_Numero_Documento_Modificar' ).closest('.form-group').find('.help-block').html('Ingresar numero');
    $( '#txt-ID_Numero_Documento_Modificar' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    
    bEstadoValidacion = false;
		scrollToError( $("html, body"), $( '#txt-ID_Numero_Documento_Modificar' ) );
  } else if ($('[name="ID_Documento_Cabecera_Orden"]').val().length == 0 && ($('#cbo-TiposDocumento').val() == 5 || $('#cbo-TiposDocumento').val() == 6) && nu_enlace == 1 && $( '#cbo-MotivoReferenciaModificar' ).val() == 0 && ($( '#cbo-SeriesDocumentoModificar' ).val().substr(0,1) == "B" || $( '#cbo-SeriesDocumentoModificar' ).val().substr(0,1) == "F") ){
    $( '#cbo-MotivoReferenciaModificar' ).closest('.form-group').find('.help-block').html('Seleccionar motivo');
    $( '#cbo-MotivoReferenciaModificar' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    
    bEstadoValidacion = false;
		scrollToError( $("html, body"), $( '#cbo-MotivoReferenciaModificar' ) );
  } else if ( $('[name="addCliente"]:checked').attr('value') == 1 && $( '#cbo-Estado' ).val() == 0 ) {
    $('#modal-message').modal('show');
    $('.modal-message').removeClass('modal-danger modal-warning modal-success');
    $( '.modal-message' ).addClass('modal-danger');
    $( '.modal-title-message' ).text( 'El cliente se encuentra con BAJA DE OFICIO / NO HABIDO' );
    setTimeout(function() {$('#modal-message').modal('hide');}, 2500);
		
    bEstadoValidacion = false;
  } else if ($('[name="addCliente"]:checked').attr('value') == 3 && $('#cbo-TiposDocumento').val() == 3 ) {
    $('#modal-message').modal('show');
    $('.modal-message').addClass('modal-danger');
    $('.modal-title-message').text('No se puede generar FACTURA a cliente varios / rápido');
    setTimeout(function () { $('#modal-message').modal('hide'); }, 2500);

    bEstadoValidacion = false;
  } else if ($('[name="addCliente"]:checked').attr('value') == 0 && $('#cbo-TiposDocumento').val() == 3 && ($('#hidden-ID_Tipo_Documento_Identidad_Existente').val() != 4 && $('#hidden-ID_Tipo_Documento_Identidad_Existente').val() != 1)) {//0 = existente
    $('#modal-message').modal('show');
    $('.modal-message').addClass('modal-danger');
    $('.modal-title-message').text('No se puede generar FACTURA a cliente que no tenga RUC');
    setTimeout(function () { $('#modal-message').modal('hide'); }, 2500);

    bEstadoValidacion = false;
  } else if ($('[name="addCliente"]:checked').attr('value') == 1 && $('#cbo-TiposDocumento').val() != 2 && ($('#txt-No_Entidad_Cliente').val().length === 0 || $('#txt-Nu_Documento_Identidad_Cliente').val().length === 0)) {//1 = Nuevo
    $('#modal-message').modal('show');
    $('.modal-message').addClass('modal-danger');
    $('.modal-title-message').text('Cliente nuevo, falta registrar Nro. Documento identidad / Nombre');
    setTimeout(function () { $('#modal-message').modal('hide'); }, 2500);

    bEstadoValidacion = false;
  } else if ($('[name="addCliente"]:checked').attr('value') == 1 && $('#cbo-TiposDocumento').val() == 3 && ($('#cbo-TiposDocumentoIdentidadCliente').val() != 4 && $('#cbo-TiposDocumentoIdentidadCliente').val() != 1) ) {//1 = Nuevo
    $('#modal-message').modal('show');
    $('.modal-message').addClass('modal-danger');
    $('.modal-title-message').text('No se puede generar FACTURA a cliente que no tenga RUC');
    setTimeout(function () { $('#modal-message').modal('hide'); }, 2500);

    bEstadoValidacion = false;
  } else if ($('[name="addCliente"]:checked').attr('value') == 1 && ($('#cbo-TiposDocumento').val() == 4 || $('#cbo-TiposDocumento').val() == 2) && $('#txt-No_Entidad_Cliente').val().trim().length < 3) {//1 = Nuevo
    $('#txt-No_Entidad_Cliente').closest('.form-group').find('.help-block').html('Debes tener mínimo 3 carácteres');
    $('#txt-No_Entidad_Cliente').closest('.form-group').removeClass('has-success').addClass('has-error');

    bEstadoValidacion = false;
    scrollToError($("html, body"), $('#txt-No_Entidad_Cliente'));
  } else if ($('#cbo-MediosPago').find(':selected').data('nu_tipo') == 1 && ($('#txt-Fe_Vencimiento').val() == '' || dEmisionCompare > dVencimiento)) {//1 = Nuevo
    $('#modal-message').modal('show');
    $('.modal-message').addClass('modal-danger');
    $('.modal-title-message').text('La fecha de vcto. no puede ser menor a la fecha de emisión o estar vacía');
    setTimeout(function () { $('#modal-message').modal('hide'); }, 3100);

    bEstadoValidacion = false;
  }
	return bEstadoValidacion;
}

function generarTablaTemporalItems($ID_Producto, $No_Producto, $Ss_Precio, $ID_Impuesto_Cruce_Documento, $Nu_Tipo_Impuesto, $Ss_Impuesto, $Qt_Producto, $iTipoItem, $ID_Impuesto_Icbper, $Ss_Icbper, $No_Unidad_Medida, $Nu_Activar_Precio_x_Mayor, arrDataAdicionalTmpDetalleItem){
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
    +"<td class='text-right'><input type='text' inputmode='decimal' id=" + $ID_Producto + " class='pos-input txt-Qt_Producto form-control input-decimal input-size_cantidad' " + ($iTipoItem == 1 ? 'onkeyup=validateStockNow(event);' : '') + " data-nu_activar_precio_x_mayor=" + $Nu_Activar_Precio_x_Mayor + " data-id_item='" + $ID_Producto + "' data-ss_icbper_item='0.00' data-ss_icbper='" + $Ss_Icbper + "' data-id_impuesto_icbper='" + $ID_Impuesto_Icbper + "' data-id_producto='" + $ID_Producto + "' value='1' autocomplete='off'></td>"
    +"<td class='text-left'>"
      + '<span style="font-size: 11px;font-weight:normal;">[' + arrDataAdicionalTmpDetalleItem.Nu_Codigo_Barra + ']<br>'
      + '<span style="font-size: 13px;font-weight:bold;">' + $No_Producto + '</span>'
      + sVarianteMultipleTmp
      + ($No_Unidad_Medida !== undefined && $No_Unidad_Medida !== null && $No_Unidad_Medida != '' ? ' <br><span style="font-size: 10px;font-weight:normal;">[' + $No_Unidad_Medida + ']</span> ' : '')
    +"</td>"
    +"<td style='display:none; width: 39%' class='text-right td-sNotaItem' data-estado='mostrar' data-id_item=" + $ID_Producto + " id='td-sNotaItem" + $ID_Producto + "'>"
      +"<textarea class='pos-input form-control input-sNotaItem hotkey-cobrar_cliente hotkey-cancelar_venta hotkey-limpiar_item hotkey-focus_item' placeholder='' maxlength='250' autocomplete='off'>"+sVarianteMultipleTmp+"</textarea></td>"
    +"</td>"
    +"<td class='text-center'>"
      +"<button type='button' id='btn-add_nota_producto_pos' class='btn btn-sm btn-link' alt='Nota' title='Nota'><i class='fa fa-edit fa-2x' aria-hidden='true'></i></button>"
    +"</td>"
    +"<td class='text-right'><input type='text' inputmode='decimal' class='pos-input txt-fValorUnitario form-control input-decimal input-size_importe' data-id_producto='" + $ID_Producto + "' value='" + $Ss_SubTotal_Producto.toFixed(2) + "' autocomplete='off'></td>"
    +"<td class='text-right'><input type='text' inputmode='decimal' class='pos-input txt-Ss_Precio form-control input-decimal input-size_importe' data-id_producto='" + $ID_Producto + "' data-precio_actual='" + $Ss_Precio + "' value='" + $Ss_Precio + "' autocomplete='off'></td>"
    +"<td class='text-right'>"
    +"<select class='cbo-ImpuestosProducto form-control required input-size_otros' style='width: 100%;'>"
        +option_impuesto_producto
      +"</select>"
    +"</td>"
    +"<td style='display:none;' class='text-right'><input type='text' inputmode='decimal' class='pos-input txt-Ss_SubTotal_Producto form-control input-decimal' value='" + $Ss_SubTotal_Producto.toFixed(2) + "' autocomplete='off' disabled></td>"
    +"<td class='text-right'><input type='text' inputmode='decimal' class='pos-input txt-Ss_Descuento form-control input-decimal input-size_otros' data-id_producto='" + $ID_Producto + "' value='' autocomplete='off'></td>"
    +"<td class='text-right'><input type='text' inputmode='decimal' class='pos-input txt-Ss_Total_Producto form-control input-decimal input-size_importe' data-id_producto='" + $ID_Producto + "' value='" + $Ss_Total_Producto.toFixed(2) + "' autocomplete='off'></td>"
    +"<td style='display:none;' class='text-right'><input type='text' inputmode='numeric' class='pos-input txt-Nu_Lote_Vencimiento form-control input-codigo_barra' placeholder='Opcional' data-id_producto='" + $ID_Producto + "' value='' autocomplete='off'></td>"
    +"<td style='display:none;' class='text-right'><input type='text' class='pos-input txt-Fe_Lote_Vencimiento form-control date-picker-invoice' placeholder='Opcional' data-id_producto='" + $ID_Producto + "' value='' autocomplete='off'></td>"
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
		$( '#txt-Qt_Producto' ).val('');
  } else {
	  $( '#txt-ID_Producto' ).val('');
		$( '#txt-No_Producto' ).val('');
		$( '#txt-Ss_Precio' ).val('');
		$( '#txt-Qt_Producto' ).val('');
		
    $( '#table-DetalleProductosVenta' ).show();
	  $( '#table-DetalleProductosVenta >tbody' ).append(table_detalle_producto);

    $('.txt-Fe_Lote_Vencimiento').datepicker({
      autoclose: true,
      startDate: new Date(fYear, fToday.getMonth(), fDay),
      todayHighlight: true
    })

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
    $("#table-DetalleProductosVenta > tbody > tr").each(function(){
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
        
      $Ss_Descuento_p += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / fImpuesto) )) / 100);
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
    $( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
		
	  validateDecimal();
	  validateNumber();
	  validateNumberOperation();

    calcularIcbper();
    calcularDescuentoTotal(0);
  }
}

function verRepresentacionInternaPDF(ID){
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

function sendPDF($modal_delete, ID){
  $( '#modal-loader' ).modal('show');
  $modal_delete.modal('hide');
  url = base_url + 'Ventas/VentaController/generarRepresentacionInternaPDF/' + ID;
  window.open(url,'_blank');
  $( '#modal-loader' ).modal('hide');
}

function crearItem() {
  $('.select2').select2();

  $( '#modal-loader' ).modal('show');
  var $modal_crearItem = $( '#modal-crearItem' );
  $modal_crearItem.modal('show');

	$( '.help-block' ).empty();
	
  $( ".div-Producto" ).show();
  $( "div.div-modal-grupoItem select" ).val("1");

  //Limpiar datos
  $( '#txt-modal-upcItem' ).val( '' );
  $( '#hidden-ID_Tabla_Dato' ).val( '' );
  $( '#txt-No_Descripcion' ).val( '' );
  $( '#txt-modal-precioItem' ).val( '' );
  $( '[name="textarea-modal-nombreItem"]' ).val( '' );
  
  /* Cargar datos para modal de item */
  url = base_url + 'HelperController/getTiposExistenciaProducto';
  $.post( url , function( responseTiposExistenciaProducto ){
    $( '#cbo-modal-tipoItem' ).html( '' );
    for (var i = 0; i < responseTiposExistenciaProducto.length; i++)
      $( '#cbo-modal-tipoItem' ).append( '<option value="' + responseTiposExistenciaProducto[i].ID_Tipo_Producto + '">' + responseTiposExistenciaProducto[i].No_Tipo_Producto + '</option>' );
  }, 'JSON');
  
  url = base_url + 'HelperController/getImpuestos';
  $.post( url , function( response ){
    $( '#cbo-modal-impuestoItem' ).html( '' );
    for (var i = 0; i < response.length; i++)
      $( '#cbo-modal-impuestoItem' ).append( '<option value="' + response[i].ID_Impuesto + '" data-ss_impuesto="' + response[i].Ss_Impuesto + '" data-nu_tipo_impuesto="' + response[i].Nu_Tipo_Impuesto + '">' + response[i].No_Impuesto + '</option>' );
  }, 'JSON');
  
  url = base_url + 'HelperController/getUnidadesMedida';
  $.post( url , function( responseUnidadMedidas ){
    $( '#cbo-modal-unidad_medidaItem' ).html( '' );
    for (var i = 0; i < responseUnidadMedidas.length; i++)
      $( '#cbo-modal-unidad_medidaItem' ).append( '<option value="' + responseUnidadMedidas[i].ID_Unidad_Medida + '">' + responseUnidadMedidas[i].No_Unidad_Medida + '</option>' );
    $( '#modal-loader' ).modal('hide');
  }, 'JSON');

  /* Categorías */
  url = base_url + 'HelperController/getDataGeneral';
  $.post(url, { sTipoData: 'categoria' }, function (response) {
    if (response.sStatus == 'success') {
      var l = response.arrData.length;
      if (l == 1) {
        $('#cbo-modal-categoria').html('<option value="' + response.arrData[0].ID + '">' + response.arrData[0].Nombre + '</option>');
      } else {
        //$('#cbo-modal-categoria').html('<option value="" selected="selected">- Seleccionar -</option>');
        $('#cbo-modal-categoria').html('');
        for (var x = 0; x < l; x++) {
          $('#cbo-modal-categoria').append('<option value="' + response.arrData[x].ID + '">' + response.arrData[x].Nombre + '</option>');
        }
      }
    } else {
      if (response.sMessageSQL !== undefined) {
        console.log(response.sMessageSQL);
      }
      $('#modal-message').modal('show');
      $('.modal-message').addClass(response.sClassModal);
      $('.modal-title-message').text(response.sMessage);
      setTimeout(function () { $('#modal-message').modal('hide'); }, 1200);
    }
  }, 'JSON');

  $( '#modal-header-crearItem' ).text('Crear Item');
  
  $( '#btn-modal-salir' ).off('click').click(function () {
    $modal_crearItem.modal('hide');
  });
  
  $( '#btn-modal-crearItem' ).off('click').click(function () {
    var fPrecio = parseFloat($( '#txt-modal-precioItem' ).val());
    $( '.help-block' ).empty();
    if ( fPrecio == 0.00 || isNaN(fPrecio) ){
      $( '#txt-modal-precioItem' ).closest('.form-group').find('.help-block').html('Ingresar precio');
      $( '#txt-modal-precioItem' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ($( '[name="textarea-modal-nombreItem"]' ).val().length === 0){
      $( '[name="textarea-modal-nombreItem"]' ).closest('.form-group').find('.help-block').html('Ingresar datos');
      $( '[name="textarea-modal-nombreItem"]' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      $( '.help-block' ).empty();
      
  		var iIDTipoExistenciaProducto = $( '#cbo-modal-tipoItem' ).val();
      if ( $( '#cbo-modal-grupoItem' ).val() == '0' || $( '#cbo-modal-grupoItem' ).val() == '2' )//Servicio o interno
        iIDTipoExistenciaProducto = 4;//Otros
      
      console.log($('#cbo-modal-unidad_medidaItem option:selected').text());

      var arrProducto = Array();
  		arrProducto = {
  		  'EID_Empresa'               : '',
  		  'EID_Producto'              : '',
        'ENu_Codigo_Barra': '',
        'ENo_Codigo_Interno': '',
  		  'Nu_Tipo_Producto'          : $( '#cbo-modal-grupoItem' ).val(),
  		  'ID_Tipo_Producto'          : iIDTipoExistenciaProducto,
  		  'ID_Ubicacion_Inventario'   : 1,
  			'Nu_Codigo_Barra'           : $( '#txt-modal-upcItem' ).val(),
  			'No_Producto'               : $( '[name="textarea-modal-nombreItem"]' ).val(),
  			'No_Codigo_Interno'         : '',
  			'ID_Impuesto'               : $( '#cbo-modal-impuestoItem' ).val(),
  			'ID_Unidad_Medida'          : $( '#cbo-modal-unidad_medidaItem' ).val(),
        'ID_Marca': '',
        'ID_Familia': $('#cbo-modal-categoria').val(),
        'Nu_Favorito': $('#cbo-modal-favorito').val(),
  			'ID_Sub_Familia'            : '',
  			'Nu_Compuesto'              : 0,
  			'Nu_Estado'                 : 1,
  			'Txt_Producto'              : '',
        'ID_Producto_Sunat'         : $( '#hidden-ID_Tabla_Dato' ).val(),
  			'Nu_Stock_Minimo': '0',
        'Nu_Stock_Maximo': '0',
  			'Nu_Receta_Medica'          : '',
  			'ID_Laboratorio'            : '',
  			'Nu_Lote_Vencimiento'       : '',
  			'Txt_Ubicacion_Producto_Tienda' : '',
        'Ss_Precio' : 0,
        'Ss_Costo' : 0,
        'ID_Impuesto_Icbper' : 0,
        'Qt_CO2_Producto' : 0,
        'ID_Tipo_Pedido_Lavado' : 0,
        'Ss_Precio': $('#txt-modal-precioItem').val(),
        'Ss_Costo': $('#txt-modal-costoItem').val(),
        'No_Imagen_Item' : '',
        'Ss_Precio_Ecommerce_Online_Regular' : 0,
        'Ss_Precio_Ecommerce_Online': 0,
        'ID_Familia_Marketplace' : 0,
        'ID_Sub_Familia_Marketplace': 0,
        'ID_Marca_Marketplace': 0,
  		};
  		
      $( '#btn-modal-salir' ).attr('disabled', true);
      
      $( '#btn-modal-crearItem' ).text('');
      $( '#btn-modal-crearItem' ).attr('disabled', true);
      $( '#btn-modal-crearItem' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
    
      $( '#modal-loader' ).modal('show');
      $( '#modal-loader' ).css("z-index", "5000");  

      url = base_url + 'Logistica/ReglasLogistica/ProductoController/crudProducto';
    	$.ajax({
        type		  : 'POST',
        dataType	: 'JSON',
    		url		    : url,
    		data		  : {
    		  arrProducto : arrProducto,
    		  arrProductoEnlace : null,
    		},
    		success : function( response ){    		  
    	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
      	  $( '#modal-message' ).modal('show');
      	  $( '.modal-message' ).css("z-index", "5000");
      	  
    		  if (response.status == 'success'){
		        if ( (!isNaN(fPrecio) && fPrecio > 0.00) && $( '#cbo-lista_precios' ).val() != 0 ){
        	    //Creando precio
              url = base_url + 'Ventas/ReglasVenta/Lista_precio_controller/crudLista_Precio_Producto';
            	$.ajax({
                type		  : 'POST',
                dataType	: 'JSON',
            		url		    : url,
            		data		  : {
            			'ID_Lista_Precio_Cabecera' : $( '#cbo-lista_precios' ).val(),
            			'ID_Producto' : response.iIDItem,
            			'Ss_Precio_Interno' : 0,
            			'Po_Descuento' : 0,
            			'Ss_Precio' : $( '#txt-modal-precioItem' ).val(),
            			'Nu_Estado' : 1,
            		},
            		success : function( responsePrecio ){
                  $( '#modal-loader' ).modal('hide');

                  $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
                  $( '#modal-message' ).modal('show');
                  $( '.modal-message' ).css("z-index", "5000");

        		      if (responsePrecio.status == 'success'){
                    $( '.modal-message' ).addClass(responsePrecio.style_modal);
                    $( '.modal-title-message' ).text(responsePrecio.message);
                    setTimeout(function() {$('#modal-message').modal('hide');}, 1500);

                    var $Nu_Codigo_Barra = $('#txt-modal-upcItem').val();                    
                    var arrDataAdicionalTmpDetalleItem = {
                      'Nu_Codigo_Barra' : $Nu_Codigo_Barra
                    };
                    addItemDetail(response.iIDItem, $('[name="textarea-modal-nombreItem"]').val(), fPrecio, $('#cbo-modal-impuestoItem').val(), $('#cbo-modal-impuestoItem').find(':selected').data('nu_tipo_impuesto'), $('#cbo-modal-impuestoItem').find(':selected').data('ss_impuesto'), 0, $('#cbo-modal-grupoItem').val(), 0, 0, $('#cbo-modal-unidad_medidaItem option:selected').text(), 0, arrDataAdicionalTmpDetalleItem);
  
              	    $modal_crearItem.modal('hide');
              		} else {
              	    $( '.modal-message' ).addClass(responsePrecio.style_modal);
              	    $( '.modal-title-message' ).text(responsePrecio.message);
              	    setTimeout(function() {$('#modal-message').modal('hide');}, 2100);
            		  }
            		  
                  $( '#btn-modal-salir' ).attr('disabled', false);
            		  
                  $( '#btn-modal-crearItem' ).text('');
                  $( '#btn-modal-crearItem' ).append( 'Guardar' );
                  $( '#btn-modal-crearItem' ).attr('disabled', false);
            		},
                error: function (jqXHR, textStatus, errorThrown) {
                  $( '#modal-loader' ).modal('hide');
            	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
            	    
              	  $( '#modal-message' ).modal('show');
            	    $( '.modal-message' ).addClass( 'modal-danger' );
            	    $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
            	    setTimeout(function() {$('#modal-message').modal('hide');}, 2100);
            	    
            	    //Message for developer
                  console.log(jqXHR.responseText);
                  
                  $( '#btn-modal-salir' ).attr('disabled', false);
            		  
                  $( '#btn-modal-crearItem' ).text('');
                  $( '#btn-modal-crearItem' ).append( 'Guardar' );
                  $( '#btn-modal-crearItem' ).attr('disabled', false);
                }
            	})
            	// /. Creando precio
		        } else {          
              $( '.modal-message' ).addClass(response.style_modal);
              $( '.modal-title-message' ).text(response.message);
              setTimeout(function() {$('#modal-message').modal('hide'); }, 2100);

              var $Nu_Codigo_Barra = $('#txt-modal-upcItem').val();                    
              var arrDataAdicionalTmpDetalleItem = {
                'Nu_Codigo_Barra' : $Nu_Codigo_Barra
              };
              addItemDetail(response.iIDItem, $('[name="textarea-modal-nombreItem"]').val(), fPrecio, $('#cbo-modal-impuestoItem').val(), $('#cbo-modal-impuestoItem').find(':selected').data('nu_tipo_impuesto'), $('#cbo-modal-impuestoItem').find(':selected').data('ss_impuesto'), 0, $('#cbo-modal-grupoItem').val(), 0, 0, $('#cbo-modal-unidad_medidaItem option:selected').text(), 0, arrDataAdicionalTmpDetalleItem);

        	    $modal_crearItem.modal('hide');
		        }
            $( '#modal-loader' ).modal('hide');
    		  } else {
      	    $( '.modal-message' ).addClass(response.style_modal);
      	    $( '.modal-title-message' ).text(response.message);
      	    setTimeout(function() {$('#modal-message').modal('hide');}, 2100);
    		  }
    		  
          $( '#modal-loader' ).modal('hide');

          $( '#btn-modal-salir' ).attr('disabled', false);
    		  
          $( '#btn-modal-crearItem' ).text('');
          $( '#btn-modal-crearItem' ).append( 'Guardar' );
          $( '#btn-modal-crearItem' ).attr('disabled', false);
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
          
          $( '#btn-modal-salir' ).attr('disabled', false);
    		  
          $( '#btn-modal-crearItem' ).text('');
          $( '#btn-modal-crearItem' ).append( 'Guardar' );
          $( '#btn-modal-crearItem' ).attr('disabled', false);
        }
    	});
    }
  });
}

function addItemDetail($ID_Producto, $No_Producto, $Ss_Precio, $ID_Impuesto_Cruce_Documento, $Nu_Tipo_Impuesto, $Ss_Impuesto, $Qt_Producto, $iTipoItem, $ID_Impuesto_Icbper, $Ss_Icbper, $No_Unidad_Medida, $Nu_Activar_Precio_x_Mayor, arrDataAdicionalTmpDetalleItem){
  var bEstadoValidacion=false;
  bEstadoValidacion = validatePreviousDocumentToSaveSale();
  if ( bEstadoValidacion == true && $ID_Producto.length === 0 || $No_Producto.length === 0 ) {
    $( '#txt-No_Producto' ).closest('.form-group').find('.help-block').html('Ingresar producto');
		$( '#txt-No_Producto' ).closest('.form-group').removeClass('has-success').addClass('has-error');
  } else if (bEstadoValidacion) {
    if(iValidarStockGlobal == 0) {
      return generarTablaTemporalItems($ID_Producto, $No_Producto, $Ss_Precio, $ID_Impuesto_Cruce_Documento, $Nu_Tipo_Impuesto, $Ss_Impuesto, $Qt_Producto, $iTipoItem, $ID_Impuesto_Icbper, $Ss_Icbper, $No_Unidad_Medida, $Nu_Activar_Precio_x_Mayor, arrDataAdicionalTmpDetalleItem);
    } else if (iValidarStockGlobal == 1 && $iTipoItem == 0) {
      return generarTablaTemporalItems($ID_Producto, $No_Producto, $Ss_Precio, $ID_Impuesto_Cruce_Documento, $Nu_Tipo_Impuesto, $Ss_Impuesto, $Qt_Producto, $iTipoItem, $ID_Impuesto_Icbper, $Ss_Icbper, $No_Unidad_Medida, $Nu_Activar_Precio_x_Mayor, arrDataAdicionalTmpDetalleItem);
    } else if ($( '#cbo-descargar_stock' ).val() == 1 && iValidarStockGlobal == 1 && $iTipoItem == 1) {

      //si tiene activo validar stock
      var arrParamsTipoEnlaceItem = {
        ID_Producto: $ID_Producto,
      };
      $.post(base_url + 'HelperController/getItem', arrParamsTipoEnlaceItem, function (responseTipoEnlaceItem) {
        if(responseTipoEnlaceItem.Nu_Compuesto==0){

          var arrParamsValidate = {
            ID_Almacen: $( '#cbo-almacen' ).val(),
            ID_Producto: $ID_Producto,
          };
          $.post(base_url + 'HelperController/getStockXItem', arrParamsValidate, function (responseValidateStock) {
            if(responseValidateStock.Qt_Producto !== undefined && responseValidateStock.Qt_Producto !== null && parseFloat(responseValidateStock.Qt_Producto) > 0.000000) {
              return generarTablaTemporalItems($ID_Producto, $No_Producto, $Ss_Precio, $ID_Impuesto_Cruce_Documento, $Nu_Tipo_Impuesto, $Ss_Impuesto, $Qt_Producto, $iTipoItem, $ID_Impuesto_Icbper, $Ss_Icbper, $No_Unidad_Medida, $Nu_Activar_Precio_x_Mayor, arrDataAdicionalTmpDetalleItem);     
            } else {
              $modal_msg_stock = $( '.modal-message' );
              $modal_msg_stock.modal('show');
          
              $modal_msg_stock.removeClass('modal-danger modal-warning modal-success');
              $modal_msg_stock.addClass('modal-warning');
              $( '.modal-title-message' ).text('');
              $( '.modal-title-message' ).text('Sin stock disponible ' + responseValidateStock.No_Producto);
          
              setTimeout(function() {$modal_msg_stock.modal('hide');}, 3100);
            }
          }, 'JSON');

        } else {          
          var arrParamsValidate = {
            iIdAlmacen: $( '#cbo-almacen' ).val(),
            iIdItem: $ID_Producto,
          };
          $.post(base_url + 'HelperController/getStockXEnlaceItem', arrParamsValidate, function (responseValidateStock) {
            var iDataCompuestoLength = responseValidateStock.length;
            for (i = 0; i < iDataCompuestoLength; i++) {
						  if (parseFloat(responseValidateStock[i].Qt_Producto) > 0.000000) {
                return generarTablaTemporalItems($ID_Producto, $No_Producto, $Ss_Precio, $ID_Impuesto_Cruce_Documento, $Nu_Tipo_Impuesto, $Ss_Impuesto, $Qt_Producto, $iTipoItem, $ID_Impuesto_Icbper, $Ss_Icbper, $No_Unidad_Medida, $Nu_Activar_Precio_x_Mayor, arrDataAdicionalTmpDetalleItem);
              } else {
                $modal_msg_stock = $( '.modal-message' );
                $modal_msg_stock.modal('show');
            
                $modal_msg_stock.removeClass('modal-danger modal-warning modal-success');
                $modal_msg_stock.addClass('modal-warning');
            
                $( '.modal-title-message' ).text('');
                $( '.modal-title-message' ).text('Sin stock disponible ' + responseValidateStock[i].No_Producto);
            
                setTimeout(function() {$modal_msg_stock.modal('hide');}, 3100);
              }
            }
          }, 'JSON');

        }
			}, 'JSON');
    } else if($( '#cbo-descargar_stock' ).val() == 0) {//no valida stock
      return generarTablaTemporalItems($ID_Producto, $No_Producto, $Ss_Precio, $ID_Impuesto_Cruce_Documento, $Nu_Tipo_Impuesto, $Ss_Impuesto, $Qt_Producto, $iTipoItem, $ID_Impuesto_Icbper, $Ss_Icbper, $No_Unidad_Medida, $Nu_Activar_Precio_x_Mayor, arrDataAdicionalTmpDetalleItem);
    }
  }// /. Validaciones previas
}

function saveFacturaVenta(arrDetalleVenta) {
  if (!isLoading) {
    $( '.div-mensaje_verificarExisteDocumento' ).text('');
                
    $( '#panel-DetalleProductosVenta' ).removeClass('panel-danger');
    $( '#panel-DetalleProductosVenta' ).addClass('panel-default');
    
    var arrVentaCabecera = Array();
    arrVentaCabecera = {
      'esEnlace'                : nu_enlace,
      'EID_Empresa'             : $( '#txt-EID_Empresa' ).val(),
      'EID_Documento_Cabecera'  : $( '#txt-EID_Documento_Cabecera' ).val(),
      'ID_Entidad'              : $( '#txt-AID' ).val(),
      'Txt_Email_Entidad': $( '#txt-Txt_Email_Entidad' ).val(),
      'Nu_Celular_Entidad': $('#txt-Nu_Celular_Entidad').val(),
      'Txt_Direccion_Entidad': $('#txt-Txt_Direccion_Entidad').val(),
      'ID_Tipo_Documento'       : $( '#cbo-TiposDocumento' ).val(),
      'ID_Serie_Documento_PK'   : $( '#cbo-SeriesDocumento' ).find(':selected').data('id_serie_documento_pk'),
      'ID_Serie_Documento'      : $( '#cbo-SeriesDocumento' ).val(),
      'Fe_Emision'              : $( '#txt-Fe_Emision' ).val(),
      'ID_Moneda'               : $( '#cbo-Monedas' ).val(),
      'ID_Medio_Pago'           : $( '#cbo-MediosPago' ).val(),
      'Fe_Vencimiento'          : $( '#txt-Fe_Vencimiento' ).val(),
      'Txt_Glosa'               : $( '[name="Txt_Glosa"]' ).val(),
      'Nu_Detraccion'           : $( '[name="radio-addDetraccion"]:checked' ).attr('value'),
      'Nu_Retencion': $('[name="radio-addRetencion"]:checked').attr('value'),
      'Po_Descuento'            : $( '#txt-Ss_Descuento' ).val(),
      'Ss_Descuento'            : $( '#txt-descuento' ).val(),
      'Ss_Total'                : $( '#txt-total' ).val(),
      'ID_Lista_Precio_Cabecera' : $( '#cbo-lista_precios' ).val(),
      'No_Formato_PDF'           : $( '#cbo-formato_pdf' ).val(),
      'Txt_Garantia' : $( '[name="Txt_Garantia"]' ).val(),
      'ID_Mesero' : $( '#cbo-vendedor' ).val(),
      'ID_Comision' : $( '#cbo-porcentaje' ).val(),
      'Nu_Descargar_Inventario' : $( '#cbo-descargar_stock' ).val(),
      'ID_Almacen' : $( '#cbo-almacen' ).val(),
      'iTipoFormaPago' : $( '#cbo-MediosPago' ).find(':selected').data('nu_tipo'),
      'ID_Documento_Cabecera_Orden' : $( '[name="ID_Documento_Cabecera_Orden"]' ).val(),
      'iTipoCliente': $('[name="addCliente"]:checked').attr('value'),
      'ID_Tipo_Medio_Pago': $('#cbo-tarjeta_credito').val(),
      'Nu_Transaccion': $('#tel-nu_referencia').val(),
      'Nu_Tarjeta': $('#tel-nu_ultimo_4_digitos_tarjeta').val(),
      'No_Orden_Compra_FE': $('[name="No_Orden_Compra_FE"]').val(),
      'No_Placa_FE': $('[name="No_Placa_FE"]').val(),
      'Nu_Expediente_FE': $('[name="Nu_Expediente_FE"]').val(),
      'Nu_Codigo_Unidad_Ejecutora_FE': $('[name="Nu_Codigo_Unidad_Ejecutora_FE"]').val(),
      'ID_Canal_Venta_Tabla_Dato': $('#cbo-canal_venta').val(),
      'ID_Sunat_Tipo_Transaction': $('#cbo-sunat_tipo_transaction').val(),
      'Nu_Tipo_Recepcion': $('#cbo-recepcion').val(),
      'Fe_Entrega': $('#txt-fe_entrega').val(),
      'ID_Transporte_Delivery': $('#modal-cbo-transporte').val(),
      'Txt_Direccion_Delivery': $('[name="Txt_Direccion_Delivery"]').val(),
      'Po_Detraccion': $('[name="Po_Detraccion"]').val(),
      'Ss_Descuento_Impuesto': $('#txt-descuento_igv').val(),
    };
    
    var arrVentaModificar = Array();
    if (nu_enlace == 1) {
      arrVentaModificar = {
        ID_Documento_Guardado : $( '#txt-ID_Documento_Guardado' ).val(),
        ID_Tipo_Documento_Modificar : $( '#cbo-TiposDocumentoModificar' ).val(),
        ID_Serie_Documento_Modificar : $( '#cbo-SeriesDocumentoModificar' ).val(),
        ID_Numero_Documento_Modificar : $( '#txt-ID_Numero_Documento_Modificar' ).val(),
        Nu_Codigo_Motivo_Referencia : $( '#cbo-MotivoReferenciaModificar' ).val(),
        iIdEntidad : $( '#txt-AID' ).val(),
        iTipoCliente : $('[name="addCliente"]:checked').attr('value'),
      };
    }
    
    var arrClienteNuevo = {};
    if ($('[name="addCliente"]:checked').attr('value') == 1){//Agregar cliente
      arrClienteNuevo = {
        'ID_Tipo_Documento_Identidad' : $( '#cbo-TiposDocumentoIdentidadCliente' ).val(),
        'Nu_Documento_Identidad'      : $( '#txt-Nu_Documento_Identidad_Cliente' ).val(),
        'No_Entidad'                  : $( '#txt-No_Entidad_Cliente' ).val(),
        'Txt_Direccion_Entidad'       : $( '#txt-Txt_Direccion_Entidad_Cliente' ).val(),
        'Nu_Telefono_Entidad'         : $( '#txt-Nu_Telefono_Entidad_Cliente' ).val(),
        'Nu_Celular_Entidad'          : $( '#txt-Nu_Celular_Entidad_Cliente' ).val(),
        'Txt_Email_Entidad': $('#txt-Txt_Email_Entidad_Cliente').val(),
        'Nu_Estado' : $( '#cbo-Estado' ).val(),
        'ID_Tipo_Cliente_1': $('#cbo-tipo_cliente_1-nuevo').val()
      };
    }
    
    $( '#btn-save' ).text('');
    $( '#btn-save' ).attr('disabled', true);
    $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
    
    $( '#modal-loader' ).modal('show');

    $('#txt-AID_Doble').val('');
    $('#txt-Filtro_Entidad').val('');

    isLoading = true;
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
      success: function (response) {
        isLoading = false;
        
        $( '#modal-loader' ).modal('hide');
        
        $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
        $( '#modal-message' ).modal('show');
        
        $( '.div-DocumentoModificar' ).removeClass('panel-warning panel-danger panel-success');
        $( '.div-mensaje_verificarExisteDocumento' ).removeClass('text-danger text-success');
        $( '.div-mensaje_verificarExisteDocumento' ).text('');
        
        if (response.status == 'success'){
          accion = '';
          if ( response.sEnviarSunatAutomatic=='No' ) {
            $( '.div-AgregarEditar' ).hide();
            $( '.div-Listar' ).show();
            $( '.modal-message' ).addClass(response.style_modal);
            $( '.modal-title-message' ).text(response.message);
            setTimeout(function() {$('#modal-message').modal('hide'); }, 1100);
            
            $( '#form-Venta' )[0].reset();
          } else {
            if (response.status == 'success'){
              $( '.div-AgregarEditar' ).hide();
              $( '.div-Listar' ).show();
              $( '.modal-message' ).addClass(response.style_modal);
              $( '.modal-title-message' ).text(response.message);
              reload_table_venta();
              setTimeout(function() {$('#modal-message').modal('hide');}, 1200);
              
              $( '#form-Venta' )[0].reset();
            } else {
              $( '.modal-message' ).addClass(response.style_modal);
              $( '.modal-title-message' ).text(response.message_nubefact);
              setTimeout(function() {$('#modal-message').modal('hide');}, 5200);
            }
          }
          reload_table_venta();
        } else {
          if ( nu_enlace == 1 ){//Para notas de crédito y débito
            $( '.div-DocumentoModificar' ).addClass(response.style_panel);
            $( '.div-mensaje_verificarExisteDocumento' ).addClass(response.style_p);
            $( '.div-mensaje_verificarExisteDocumento' ).text(response.message);
          }
          
          $( '.modal-message' ).addClass(response.style_modal);
          $( '.modal-title-message' ).text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 5200);
        }
        
        $( '#btn-save' ).text('');
        $( '#btn-save' ).append( 'Guardar' );
        $( '#btn-save' ).attr('disabled', false);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        isLoading = false;
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

function getListaPrecios(arrParams){
  url = base_url + 'HelperController/getListaPrecio';
  var arrPost = {
    Nu_Tipo_Lista_Precio : $( '[name="Nu_Tipo_Lista_Precio"]' ).val(),
    ID_Organizacion: $( '#header-a-id_organizacion' ).val(),
    ID_Almacen : arrParams.ID_Almacen,
  }
  $.post( url, arrPost, function( responseListaPrecio ){
    var iCantidadRegistrosListaPrecios = responseListaPrecio.length;
    if ( iCantidadRegistrosListaPrecios == 1 ) {
      $( '#cbo-lista_precios' ).html( '<option value="' + responseListaPrecio[0].ID_Lista_Precio_Cabecera + '">' + responseListaPrecio[0].No_Lista_Precio + '</option>' );
    } else if ( iCantidadRegistrosListaPrecios > 1 ) {
      $( '#cbo-lista_precios' ).html( '<option value="0">- Seleccionar -</option>');
      for (var i = 0; i < iCantidadRegistrosListaPrecios; i++)
        $( '#cbo-lista_precios' ).append( '<option value="' + responseListaPrecio[i].ID_Lista_Precio_Cabecera + '">' + responseListaPrecio[i].No_Lista_Precio + '</option>' );
    } else {
      $( '#cbo-lista_precios' ).html( '<option value="0">- Sin lista precio -</option>');
    }
  }, 'JSON');
}

function cobrarCliente(ID_Documento_Cabecera, Ss_Total_Saldo, No_Entidad, No_Tipo_Documento_Breve, ID_Serie_Documento, ID_Numero_Documento, sSignoMoneda, iDetraccion){
  arrParams = {
    'iIdDocumentoCabecera' : ID_Documento_Cabecera,
    'fTotalSaldo' : Ss_Total_Saldo,
    'sCliente' : No_Entidad,
    'sTipoDocumento' : No_Tipo_Documento_Breve,
    'sSerieDocumento' : ID_Serie_Documento,
    'sNumeroDocumento' : ID_Numero_Documento,
    'sSignoMoneda' : sSignoMoneda,
    'iDetraccion' : iDetraccion,
  }

  $( '#form-cobrar_cliente' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();

  $( '.div-forma_pago').hide();
  $( '.div-modal_datos_tarjeta_credito').hide();
  $( '.div-estado_lavado_recepcion_cliente' ).hide();
  $( '.div-recibe_otra_persona' ).hide();

  $( '.modal-cobrar_cliente' ).modal('show');

  $('.date-picker-invoice').val(fDay + '/' + fMonth + '/' + fYear);
  $( '[name="iIdDocumentoCabecera"]' ).val( arrParams.iIdDocumentoCabecera );

  $( '#hidden-cobrar_cliente-fsaldo' ).val( arrParams.fTotalSaldo );
  $( '#hidden-cobrar_cliente-detraccion' ).val( arrParams.iDetraccion );

  $( '#modal-header-cobrar_cliente-title' ).text(arrParams.sTipoDocumento + ' - ' + arrParams.sSerieDocumento + ' - ' + arrParams.sNumeroDocumento);  
  $( '#cobrar_cliente-modal-body-cliente' ).text('Cliente: ' + arrParams.sCliente);
  $( '#cobrar_cliente-modal-body-saldo_cliente' ).text('Saldo: ' + sSignoMoneda + ' ' + arrParams.fTotalSaldo);

  $( '.div-forma_pago').show();
  $( '.modal-cobrar_cliente' ).on('shown.bs.modal', function() {
    $( '[name="fPagoCliente"]' ).focus();
    $( '[name="fPagoCliente"]' ).val( arrParams.fTotalSaldo );
  });

  $('#cbo-modal_forma_pago').html('- No hay registros -');
  url = base_url + 'HelperController/getMediosPago';
  var arrPost = {
    iIdEmpresa : arrParams.iIdEmpresa,
  };
  $.post( url, arrPost, function( response ){
    var iCantidadRegistros = response.length;
    if (iCantidadRegistros > 0) {
      $('.div-modal_datos_tarjeta_credito').hide();
      $('#cbo-cobrar_cliente-modal_tarjeta_credito').html('');
      $('#tel-nu_referencia').val('');
      $('#tel-nu_ultimo_4_digitos_tarjeta').val('');
      if (response[0].Nu_Tipo == 2) {
        $('.div-modal_datos_tarjeta_credito').show();

        url = base_url + 'HelperController/getTiposTarjetaCredito';
        $.post(url, { ID_Medio_Pago: response[0].ID_Medio_Pago }, function (responseTCyBanco) {
          $('#cbo-cobrar_cliente-modal_tarjeta_credito').html('');
          for (var i = 0; i < responseTCyBanco.length; i++)
            $('#cbo-cobrar_cliente-modal_tarjeta_credito').append('<option value="' + responseTCyBanco[i].ID_Tipo_Medio_Pago + '">' + responseTCyBanco[i].No_Tipo_Medio_Pago + '</option>');
        }, 'JSON');
      }

      $( '#cbo-modal_forma_pago' ).html('');
      for (var i = 0; i < iCantidadRegistros; i++) {
        if ( response[i].Nu_Tipo != 1 )
          $( '#cbo-modal_forma_pago' ).append( '<option value="' + response[i].ID_Medio_Pago + '" data-nu_tipo_medio_pago="' + response[i].Nu_Tipo + '">' + response[i].No_Medio_Pago + '</option>' );
      }
    }
  }, 'JSON');
  
  // Modal de cobranza al cliente
  $( '#cbo-modal_forma_pago' ).change(function(){
    ID_Medio_Pago = $(this).val();
    Nu_Tipo_Medio_Pago = $(this).find(':selected').data('nu_tipo_medio_pago');
    $( '.div-modal_datos_tarjeta_credito').hide();
    $( '#cbo-cobrar_cliente-modal_tarjeta_credito' ).html('');
    $( '#tel-nu_referencia' ).val('');
    $( '#tel-nu_ultimo_4_digitos_tarjeta' ).val('');
    if (Nu_Tipo_Medio_Pago==2){
      $( '.div-modal_datos_tarjeta_credito').show();

      url = base_url + 'HelperController/getTiposTarjetaCredito';
      $.post( url, {ID_Medio_Pago : ID_Medio_Pago} , function( response ){
        $( '#cbo-cobrar_cliente-modal_tarjeta_credito' ).html('');
        for (var i = 0; i < response.length; i++)
          $( '#cbo-cobrar_cliente-modal_tarjeta_credito' ).append( '<option value="' + response[i].ID_Tipo_Medio_Pago + '">' + response[i].No_Tipo_Medio_Pago + '</option>' );
      }, 'JSON');
    }

    setTimeout(function(){ $( '[name="fPagoCliente"]' ).focus(); $( '[name="fPagoCliente"]' ).select(); }, 20);		
  })
}

function generarTareaRepetirMensual(ID) {
  var $modal_delete = $('.modal-message-repetir_mensualmente');
  $modal_delete.modal('show');

  $('#cbo-modal-tipo_tiempo_repetir').html('<option value="0">- Seleccionar -</option>');
  url = base_url + 'HelperController/getTipoTiempoRepetir';
  $.post(url, {}, function (response) {
    $('#cbo-modal-tipo_tiempo_repetir').html('<option value="0">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++)
      $('#cbo-modal-tipo_tiempo_repetir').append('<option value="' + response[i].ID_Tipo_Tiempo_Repetir + '">' + response[i].No_Tipo_Tiempo_Repetir + '</option>');
  }, 'JSON');

  $('#cbo-modal-repetir_mensualmente-dia').html('');
  var selected = '';
  for (var i = 0; i < arrDiasMes.dias_mes.length; i++) {
    selected='';
    if (arrDiasMes.dias_mes[i].value == fDay)
      selected = 'selected';
    $('#cbo-modal-repetir_mensualmente-dia').append('<option value="' + arrDiasMes.dias_mes[i].value + '" ' + selected + '>' + (arrDiasMes.dias_mes[i].value != 0 ? arrDiasMes.dias_mes[i].value : 'Último día') + '</option>');
  }

  $('.div-modal-repetir_mensualmente-mes').hide();
  $('#cbo-modal-tipo_tiempo_repetir').change(function () {
    $('.div-modal-repetir_mensualmente-mes').hide();
    if ($(this).val() == 2)
      $('.div-modal-repetir_mensualmente-mes').show();
  });

  var arrMonth =
  '{' +
    '"mes_year":[' +
      '{"value":"01", "nombre": "Enero"},' +
      '{"value":"02", "nombre": "Febrero"},' +
      '{"value":"03", "nombre": "Marzo"},' +
      '{"value":"04", "nombre": "Abril"},' +
      '{"value":"05", "nombre": "Mayo"},' +
      '{"value":"06", "nombre": "Junio"},' +
      '{"value":"07", "nombre": "Julio"},' +
      '{"value":"08", "nombre": "Agosto"},' +
      '{"value":"09", "nombre": "Setiembre"},' +
      '{"value":"10", "nombre": "Octubre"},' +
      '{"value":"11", "nombre": "Noviembre"},' +
      '{"value":"12", "nombre": "Diciembre"}' +
    ']' +
  '}';
  arrMonth = JSON.parse(arrMonth);

  $('#cbo-modal-repetir_mensualmente-mes').html('');
  selected = '';
  for (var i = 0; i < arrMonth.mes_year.length; i++) {
    selected = '';
    if (arrMonth.mes_year[i].value == fMonth)
      selected = 'selected';
    $('#cbo-modal-repetir_mensualmente-mes').append('<option value="' + arrMonth.mes_year[i].value + '" ' + selected + '>' + arrMonth.mes_year[i].nombre + '</option>');
  }

  $('#btn-cancel-repetir_mensualmente').off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $(document).bind('keydown', 'alt+k', function () {
    if (accion == 'anular') {
      _generarTareaRepetirMensual($modal_delete, ID);
      accion = '';
    }
  });

  $('#btn-save-repetir_mensualmente').off('click').click(function () {
    if ($('#cbo-modal-tipo_tiempo_repetir').val() == 0) {
      $('#cbo-modal-tipo_tiempo_repetir').closest('.form-group').find('.help-block').html('Seleccionar tiempo');
      $('#cbo-modal-tipo_tiempo_repetir').closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      _generarTareaRepetirMensual($modal_delete, ID);
    }
  });
}

function _generarTareaRepetirMensual($modal_delete, ID) {
  $('#modal-loader').modal('show');

  var sendPost = {
    ID: ID,
    ID_Tipo_Tiempo_Repetir: $('#cbo-modal-tipo_tiempo_repetir').val(),
    Nu_Month: $('#cbo-modal-repetir_mensualmente-mes').val(),
    Nu_Dia: $('#cbo-modal-repetir_mensualmente-dia').val(),
    sTipoRespuesta: 'json',
  };

  url = base_url + 'Ventas/VentaController/generarTareaRepetirMensual';
  $.ajax({
    url: url,
    type: "POST",
    dataType: "JSON",
    data: sendPost,
    success: function (response) {
      $modal_delete.modal('hide');

      $('.modal-message').removeClass('modal-danger modal-warning modal-success');
      $('#modal-message').modal('show');

      if (response.status == 'success') {
        $('.modal-message').addClass(response.style_modal);
        $('.modal-title-message').text(response.message);
        setTimeout(function () { $('#modal-message').modal('hide'); }, 1200);
      } else {
        $('.modal-message').addClass(response.style_modal);
        $('.modal-title-message').text(response.message);
        setTimeout(function () { $('#modal-message').modal('hide'); }, 1800);
      }
      $('#modal-loader').modal('hide');
      reload_table_venta();
      accion = '';
    },
    error: function (jqXHR, textStatus, errorThrown) {
      accion = '';
      $('#modal-loader').modal('hide');
      $modal_delete.modal('hide');
      $('.modal-message').removeClass('modal-danger modal-warning modal-success');

      $('#modal-message').modal('show');
      $('.modal-message').addClass('modal-danger');
      $('.modal-title-message').text(textStatus + ' [' + jqXHR.status + ']: ' + errorThrown);
      setTimeout(function () { $('#modal-message').modal('hide'); }, 1700);

      //Message for developer
      console.log(jqXHR.responseText);
    },
  });
}

function eliminarTareaRepetirMensual(ID) {
  var $modal_delete = $('.modal-message-delete');
  $modal_delete.modal('show');

  $('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
  $('.modal-message-delete').addClass('modal-danger');

  $('.modal-title-message-delete').text('¿Deseas eliminar repetir mensualmente la tarea?');

  $('#btn-cancel-delete').off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $(document).bind('keydown', 'alt+k', function () {
    if (accion == 'anular') {
      _eliminarTareaRepetirMensual($modal_delete, ID);
      accion = '';
    }
  });

  $('#btn-save-delete').off('click').click(function () {
    _eliminarTareaRepetirMensual($modal_delete, ID);
  });
}

function _eliminarTareaRepetirMensual($modal_delete, ID) {
  $('#modal-loader').modal('show');

  url = base_url + 'Ventas/VentaController/eliminarTareaRepetirMensual/' + ID;
  $.ajax({
    url: url,
    type: "GET",
    dataType: "JSON",
    success: function (response) {
      $('#modal-loader').modal('hide');

      $modal_delete.modal('hide');
      $('.modal-message').removeClass('modal-danger modal-warning modal-success');
      $('#modal-message').modal('show');

      if (response.status == 'success') {
        $('.modal-message').addClass(response.style_modal);
        $('.modal-title-message').text(response.message);
        setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
      } else {
        $('.modal-message').addClass(response.style_modal);
        $('.modal-title-message').text(response.message);
        setTimeout(function () { $('#modal-message').modal('hide'); }, 3100);
      }
      reload_table_venta();
      accion = '';
    },
    error: function (jqXHR, textStatus, errorThrown) {
      accion = '';
      $('#modal-loader').modal('hide');
      $modal_delete.modal('hide');
      $('.modal-message').removeClass('modal-danger modal-warning modal-success');

      $('#modal-message').modal('show');
      $('.modal-message').addClass('modal-danger');
      $('.modal-title-message').text(textStatus + ' [' + jqXHR.status + ']: ' + errorThrown);
      setTimeout(function () { $('#modal-message').modal('hide'); }, 1700);

      //Message for developer
      console.log(jqXHR.responseText);
    },
  });
}

function calcularIcbper(){
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

  $("#table-DetalleProductosVenta > tbody > tr").each(function () {
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
    $("#table-DetalleProductosVenta > tbody > tr").each(function () {
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
    $( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );

    //calcular ICBPER
    var $fTotalIcbper = 0.00;
    $("#table-DetalleProductosVenta > tbody > tr").each(function () {
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
    $( '#span-total_importe' ).text(($Ss_Total + $fTotalIcbper).toFixed(2));
  } else {
    $('#txt-Ss_Descuento').val('');
  }
}

//WhatsApp
function sendWhatsapp(ID, iIdCliente) {
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

      url = base_url + 'HelperController/getDatosDocumentoVentaWhatsApp/';
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
            var sDocumento = response.arrData[0].No_Tipo_Documento + (response.arrData[0].ID_Tipo_Documento != '2' ? ' Electrónica' : '') + ' - ' + response.arrData[0].ID_Serie_Documento + ' - ' + response.arrData[0].ID_Numero_Documento;

            url = 'https://api.whatsapp.com/send?phone=51' + sNumeroPeru + '&text=';
            url += 'Somos *' + (response.arrData[0].No_Empresa_Comercial != '' ? caracteresValidosWhatsApp(response.arrData[0].No_Empresa_Comercial) : caracteresValidosWhatsApp(response.arrData[0].No_Empresa)) + '*,\n';

            url += '\n*Nombre:* ' + caracteresValidosWhatsApp(response.arrData[0].No_Entidad);
            url += '\n*' + response.arrData[0].No_Tipo_Documento_Identidad_Breve + ':* ' + response.arrData[0].Nu_Documento_Identidad;
            url += '\n*Documento:* ' + sDocumento;
            url += '\n*Fecha de Emisión:* ' + ParseDate(response.arrData[0].Fecha_Emision);

            var iTotalRegistros = response.arrData.length;
            var responseDetalle = response.arrData;

            url += '\n\n*Detalle de Pedido*\n';
            url += '=============\n';
            for (var i = 0; i < iTotalRegistros; i++) {
              url += '✅ ' + number_format(responseDetalle[i].Qt_Producto, 2) + ' x *' + caracteresValidosWhatsApp(responseDetalle[i].sNombreItem.trim()) + '* - ' + responseDetalle[i].No_Signo + ' ' + number_format(responseDetalle[i].Ss_Precio, 2) + '\n';
            }

            url += '\n➡️ *Total:* ' + response.arrData[0].No_Signo + ' ' + number_format(response.arrData[0].Total, 2) + '\n';
            //Saldo
            if (parseFloat(response.arrData[0].Total_Saldo) > 0.00) {
              url += '➡️ *Fecha de Vencimiento:* ' + ParseDate(response.arrData[0].Fecha_Vencimiento);
              url += '\n➡️ Tiene un *saldo pendiente por pagar de ' + response.arrData[0].No_Signo + ' ' + number_format(response.arrData[0].Total_Saldo, 2) + '*\n';
            }

            if (response.arrData[0].enlace_del_pdf !== undefined && response.arrData[0].enlace_del_pdf != null) {
              url += '\nDescarga tu *PDF electrónico* en el siguiente enlace:\n' + response.arrData[0].enlace_del_pdf;
            }

            url += (response.arrData[0].sTerminosCondicionesTicket != '' && response.arrData[0].sTerminosCondicionesTicket != null ? '\n\n' + response.arrData[0].sTerminosCondicionesTicket.replace(/<br \/>/g, "") : '');
            url += '\n\nGenerado por laesystems.com';

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


function generarDocumentoReferencia(ID, iIdCliente, ID_Tipo_Documento, ID_Tipo_Documento_Modificar, ID_Serie_Documento_Modificar, ID_Numero_Documento_Modificar, ID_Serie_Documento_Modificar_PK) {
  var $modal_delete = $('.modal-message-delete');
  $modal_delete.modal('show');

  $('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
  $('.modal-message-delete').addClass('modal-warning');

  var sNombreTipoDocumento = 'Nota de crédito';
  if (ID_Tipo_Documento==6)
    sNombreTipoDocumento = 'Nota de Débito';
  else if (ID_Tipo_Documento == 7)
    sNombreTipoDocumento = 'Guia de Remisión';
  $('.modal-title-message-delete').text('¿Deseas generar ' + sNombreTipoDocumento + ' ?');

  $('#btn-cancel-delete').off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $(document).bind('keydown', 'alt+k', function () {
    if (accion == 'anular') {
      _generarDocumentoReferencia($modal_delete, ID, iIdCliente, ID_Tipo_Documento, ID_Tipo_Documento_Modificar, ID_Serie_Documento_Modificar, ID_Numero_Documento_Modificar, ID_Serie_Documento_Modificar_PK);
      accion = '';
    }
  });

  $('#btn-save-delete').off('click').click(function () {
    $modal_delete.modal('hide');
    _generarDocumentoReferencia($modal_delete, ID, iIdCliente, ID_Tipo_Documento, ID_Tipo_Documento_Modificar, ID_Serie_Documento_Modificar, ID_Numero_Documento_Modificar, ID_Serie_Documento_Modificar_PK);
  });
}

function _generarDocumentoReferencia($modal_delete, ID, iIdCliente, ID_Tipo_Documento, ID_Tipo_Documento_Modificar, ID_Serie_Documento_Modificar, ID_Numero_Documento_Modificar, ID_Serie_Documento_Modificar_PK) {
  accion = 'upd_factura_venta';
  $('#modal-loader').modal('show');

  $('.div-Listar').hide();

  $('#txt-EID_Empresa').focus();

  $('.modal-adicionales').modal('hide');
  $('.modal-guias_remision').modal('hide');

  $('#form-Venta')[0].reset();
  $('.form-group').removeClass('has-error');
  $('.form-group').removeClass('has-success');
  $('.help-block').empty();

  $('#txt-AID').val('');
  $('#radio-cliente_varios').prop('checked', false).iCheck('update');
  $('#radio-cliente_existente').prop('checked', true).iCheck('update');
  $('#radio-cliente_nuevo').prop('checked', false).iCheck('update');

  $('.panel_body_total_todo').css("display", "none");
  $('#btn-ver_total_todo').data('ver_total_todo', 0);
  $('#btn-ver_total_todo').text('VER / DESCUENTO');

  $('.div-cliente_existente').show();
  $('.div-cliente_nuevo').hide();

  $('.div-DocumentoModificar').hide();

  $('#txt-ID_Documento_Guardado').val(1);
  $('.div-DocumentoModificar').removeClass('panel-warning panel-danger panel-success');
  $('.div-mensaje_verificarExisteDocumento').removeClass('text-danger text-success');
  $('.div-mensaje_verificarExisteDocumento').text('');
  $('.div-DocumentoModificar').addClass('panel-default');

  $('#panel-DetalleProductosVenta').removeClass('panel-danger');
  $('#panel-DetalleProductosVenta').addClass('panel-default');

  $('#txt-subTotal').val(value_importes_cero);
  $('#span-subTotal').text(texto_importes_cero);

  $('#txt-exonerada').val(value_importes_cero);
  $('#span-exonerada').text(texto_importes_cero);

  $('#txt-inafecto').val(value_importes_cero);
  $('#span-inafecto').text(texto_importes_cero);

  $('#txt-gratuita').val(value_importes_cero);
  $('#span-gratuita').text(texto_importes_cero);

  $('#txt-impuesto').val(value_importes_cero);
  $('#span-impuesto').text(texto_importes_cero);

  $('#txt-descuento').val(value_importes_cero);
  $('#span-descuento').text(texto_importes_cero);

  $('#txt-total').val(value_importes_cero);
  $('#span-total').text(texto_importes_cero);
  $( '#span-total_importe' ).text(texto_importes_cero);

  $('#btn-save').attr('disabled', false);

  considerar_igv = 0;

  url = base_url + 'HelperController/getTiposDocumentoIdentidad';
  $.post(url, function (response) {
    $('#cbo-TiposDocumentoIdentidadCliente').html('');
    for (var i = 0; i < response.length; i++)
      $('#cbo-TiposDocumentoIdentidadCliente').append('<option value="' + response[i]['ID_Tipo_Documento_Identidad'] + '" data-nu_cantidad_caracteres="' + response[i]['Nu_Cantidad_Caracteres'] + '">' + response[i]['No_Tipo_Documento_Identidad_Breve'] + '</option>');
  }, 'JSON');

  $('#div-cliente_rapido').show();
  $('#cbo-TiposDocumento').change(function () {
    $('#div-cliente_rapido').show();
    if ($(this).val() == 3)
      $('#div-cliente_rapido').hide();

    if ($('#cbo-almacen').val() > 0) {
      $('#cbo-SeriesDocumento').html('');
      $('.div-DocumentoModificar').hide();
      if ($(this).val() > 0) {
        considerar_igv = $(this).find(':selected').data('nu_impuesto');
        nu_enlace = $(this).find(':selected').data('nu_enlace');
        if (nu_enlace == 1) {//Validar N/C y N/D
          $('.div-DocumentoModificar').show();

          url = base_url + 'HelperController/getTiposDocumentosModificar';
          $.post(url, { Nu_Tipo_Filtro: 1 }, function (response) {
            $('#cbo-TiposDocumentoModificar').html('<option value="" selected="selected">- Seleccionar -</option>');
            for (var i = 0; i < response.length; i++)
              $('#cbo-TiposDocumentoModificar').append('<option value="' + response[i]['ID_Tipo_Documento'] + '">' + response[i]['No_Tipo_Documento_Breve'] + '</option>');
          }, 'JSON');

          //Motivos de referencia  
          url = base_url + 'HelperController/getMotivosReferenciaModificar';
          $.post(url, { ID_Tipo_Documento: $(this).val() }, function (response) {
            $('#cbo-MotivoReferenciaModificar').html('');
            for (var i = 0; i < response.length; i++)
              $('#cbo-MotivoReferenciaModificar').append('<option value="' + response[i].Nu_Valor + '">' + response[i].No_Descripcion + '</option>');
          }, 'JSON');
        }// /. Validación de N/C y N/D

        url = base_url + 'HelperController/getSeriesDocumentoxAlmacen';
        $.post(url, { ID_Organizacion: $('#header-a-id_organizacion').val(), ID_Almacen: $('#cbo-almacen').val(), ID_Tipo_Documento: $(this).val() }, function (response) {
          if (response.length === 1) {
            $('#cbo-SeriesDocumento').html('<option value="' + response[0].ID_Serie_Documento + '" data-id_serie_documento_pk=' + response[0].ID_Serie_Documento_PK + '>' + response[0].ID_Serie_Documento + '</option>');
          } else if (response.length > 1) {
            $('#cbo-SeriesDocumento').html('<option value="" selected="selected">- Seleccionar -</option>');
            for (var i = 0; i < response.length; i++)
              $('#cbo-SeriesDocumento').append('<option value="' + response[i].ID_Serie_Documento + '"data-id_serie_documento_pk=' + response[i].ID_Serie_Documento_PK + '>' + response[i].ID_Serie_Documento + '</option>');
          } else
            $('#cbo-SeriesDocumento').html('<option value="" selected="selected">Sin serie</option>');
        }, 'JSON');

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
        $("#table-DetalleProductosVenta > tbody > tr").each(function () {
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

          if (isNaN($Ss_Descuento_Producto))
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

        if (isNaN($Ss_Descuento))
          $Ss_Descuento = 0.00;

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

        $('#txt-descuento').val($Ss_Descuento.toFixed(2));
        $('#span-descuento').text($Ss_Descuento.toFixed(2));

        $('#txt-total').val($Ss_Total.toFixed(2));
        $('#span-total').text($Ss_Total.toFixed(2));
        $('#span-total_importe').text($Ss_Total.toFixed(2));
      }
    } else {
      $('#cbo-almacen').closest('.form-group').find('.help-block').html('Seleccionar almacén');
      $('#cbo-almacen').closest('.form-group').removeClass('has-success').addClass('has-error');
    }// /. if - else validacion de seleccionar almacen
  })

  //CSS
  $("#cbo-TiposDocumento").css("background-color", "#d2d6de");
  $("#cbo-TiposDocumento").css("pointer-events", "none");

  url = base_url + 'Ventas/VentaController/generarDocumentoReferencia/' + ID;
  $.ajax({
    url: url,
    type: "GET",
    dataType: "JSON",
    success: function (response) {
      if (response.status == 'success') {
        //SET RESPONSE
        response.arrEdit[0].ID_Tipo_Documento = ID_Tipo_Documento;
        response.arrEdit[0].ID_Tipo_Documento_Modificar = ID_Tipo_Documento_Modificar;
        response.arrEdit[0].ID_Serie_Documento_Modificar = ID_Serie_Documento_Modificar;
        response.arrEdit[0].ID_Serie_Documento_Modificar_PK = ID_Serie_Documento_Modificar_PK;
        response.arrEdit[0].ID_Numero_Documento_Modificar = ID_Numero_Documento_Modificar;
        response.arrEdit[0].Nu_Enlace = 1;

        $('.div-AgregarEditar').show();

        $('.title_Venta').text('Modifcar Venta');

        $('[name="EID_Empresa"]').val('');//SET
        $('[name="EID_Documento_Cabecera"]').val('');//SET

        nu_enlace = response.arrEdit[0].Nu_Enlace;
        $('[name="ID_Documento_Cabecera_Orden"]').val(response.arrEdit[0].ID_Documento_Cabecera_Enlace);
        if (response.arrEdit[0].ID_Documento_Cabecera_Enlace != '' && response.arrEdit[0].ID_Documento_Cabecera_Enlace != null) {
          nu_enlace = 1;

          $('#txt-ID_Documento_Guardado').val(response.arrEdit[0].ID_Documento_Cabecera_Enlace);
        }

        //Datos Cliente
        $('[name="ID_Tipo_Documento_Identidad_Existente"]').val(response.arrEdit[0].ID_Tipo_Documento_Identidad);
        $('[name="AID"]').val(response.arrEdit[0].ID_Entidad);
        $('[name="ANombre"]').val(response.arrEdit[0].No_Entidad);
        $('[name="ACodigo"]').val(response.arrEdit[0].Nu_Documento_Identidad);
        $('[name="Txt_Direccion_Entidad"]').val(response.arrEdit[0].Txt_Direccion_Entidad);

        $('#div-cliente_rapido').show();
        if (response.arrEdit[0].ID_Tipo_Documento == 3)
          $('#div-cliente_rapido').hide();

        //Datos Documento
        considerar_igv = response.arrEdit[0].Nu_Impuesto;

        url = base_url + 'HelperController/getOrganizaciones';
        $.post(url, function (responseOrganizaciones) {
          $('#cbo-OrganizacionesVenta').html('');
          for (var i = 0; i < responseOrganizaciones.length; i++) {
            selected = '';
            if (response.arrEdit[0].ID_Organizacion == responseOrganizaciones[i].ID_Organizacion)
              selected = 'selected="selected"';
            $('#cbo-OrganizacionesVenta').append('<option value="' + responseOrganizaciones[i].ID_Organizacion + '" ' + selected + '>' + responseOrganizaciones[i].No_Organizacion + '</option>');
          }
        }, 'JSON');

        url = base_url + 'HelperController/getTiposDocumentos';
        $.post(url, { Nu_Tipo_Filtro: 3 }, function (responseTiposDocumento) {
          $('#cbo-TiposDocumento').html('');
          for (var i = 0; i < responseTiposDocumento.length; i++) {
            selected = '';
            if (response.arrEdit[0].ID_Tipo_Documento == responseTiposDocumento[i]['ID_Tipo_Documento'])
              selected = 'selected="selected"';
            $('#cbo-TiposDocumento').append('<option value="' + responseTiposDocumento[i]['ID_Tipo_Documento'] + '" data-nu_impuesto="' + responseTiposDocumento[i]['Nu_Impuesto'] + '" data-nu_enlace="' + responseTiposDocumento[i]['Nu_Enlace'] + '" ' + selected + '>' + responseTiposDocumento[i]['No_Tipo_Documento_Breve'] + '</option>');
          }
        }, 'JSON');

        url = base_url + 'HelperController/getSeriesDocumento';
        $.post(url, { ID_Organizacion: response.arrEdit[0].ID_Organizacion, ID_Tipo_Documento: response.arrEdit[0].ID_Tipo_Documento }, function (responseSeriesDocumento) {
          $('#cbo-SeriesDocumento').html('');
          for (var i = 0; i < responseSeriesDocumento.length; i++) {
            if (ID_Tipo_Documento_Modificar==4 && responseSeriesDocumento[i]['ID_Serie_Documento'].charAt(0)=='B') {
              $('#cbo-SeriesDocumento').append('<option value="' + responseSeriesDocumento[i]['ID_Serie_Documento'] + '" ' + selected + ' data-id_serie_documento_pk=' + responseSeriesDocumento[i].ID_Serie_Documento_PK + '>' + responseSeriesDocumento[i]['ID_Serie_Documento'] + '</option>');
            } else if (ID_Tipo_Documento_Modificar==3 && responseSeriesDocumento[i]['ID_Serie_Documento'].charAt(0) == 'F') {
              $('#cbo-SeriesDocumento').append('<option value="' + responseSeriesDocumento[i]['ID_Serie_Documento'] + '" ' + selected + ' data-id_serie_documento_pk=' + responseSeriesDocumento[i].ID_Serie_Documento_PK + '>' + responseSeriesDocumento[i]['ID_Serie_Documento'] + '</option>');
            }
          }
        }, 'JSON');

        $('[name="Fe_Emision"]').val(ParseDateString(response.arrEdit[0].Fe_Emision, 6, '-'));

        url = base_url + 'HelperController/getMonedas';
        $.post(url, function (responseMonedas) {
          $('#cbo-Monedas').html('');
          for (var i = 0; i < responseMonedas.length; i++) {
            selected = '';
            if (response.arrEdit[0].ID_Moneda == responseMonedas[i]['ID_Moneda']) {
              selected = 'selected="selected"';
              $('.span-signo').text(responseMonedas[i]['No_Signo']);
            }
            $('#cbo-Monedas').append('<option value="' + responseMonedas[i]['ID_Moneda'] + '" data-no_signo="' + responseMonedas[i]['No_Signo'] + '" ' + selected + '>' + responseMonedas[i]['No_Moneda'] + '</option>');
          }
        }, 'JSON');

        url = base_url + 'HelperController/getMediosPago';
        $.post(url, function (responseMediosPago) {
          $('#cbo-MediosPago').html('');
          for (var i = 0; i < responseMediosPago.length; i++) {
            selected = '';
            /*
            if (response.arrEdit[0].ID_Medio_Pago == responseMediosPago[i]['ID_Medio_Pago'])
              selected = 'selected="selected"';
              */
            //if (responseMediosPago[i].No_Codigo_Sunat_PLE == '008' && response.arrEdit[0].Nu_Enlace == 1)
              //selected = 'selected="selected"';
            if(responseMediosPago[i]['Nu_Tipo']!=1)//1=credito
              $('#cbo-MediosPago').append('<option value="' + responseMediosPago[i]['ID_Medio_Pago'] + '" data-nu_tipo="' + responseMediosPago[i]['Nu_Tipo'] + '" ' + selected + '>' + responseMediosPago[i]['No_Medio_Pago'] + '</option>');
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

        // Obtener canales de venta
        url = base_url + 'HelperController/getCanalesVenta';
        $.post(url, function (responseCanalVenta) {
          if (responseCanalVenta.sStatus == 'success') {
            var l = responseCanalVenta.arrData.length;
            if (l == 1) {
              $('#cbo-canal_venta').html('<option value="' + responseCanalVenta.arrData[0].ID + '">' + responseCanalVenta.arrData[0].Nombre + '</option>');
            } else {
              $('#cbo-canal_venta').html('<option value="0" selected="selected">- Seleccionar -</option>');
              for (var x = 0; x < l; x++) {
                selected = '';
                if (response.arrEdit[0].ID_Canal_Venta_Tabla_Dato == responseCanalVenta.arrData[x].ID)
                  selected = 'selected="selected"';
                $('#cbo-canal_venta').append('<option value="' + responseCanalVenta.arrData[x].ID + '" ' + selected + '>' + responseCanalVenta.arrData[x].Nombre + '</option>');
              }
            }
          } else {
            if (response.sMessageSQL !== undefined) {
              console.log(response.sMessageSQL);
            }
          }
        }, 'JSON');

        if (response.arrEdit[0].Nu_Tipo == 1)// Si es Crédito
          $('.div-MediosPago').show();

        $('#txt-Fe_Emision').val(fDay + '/' + fMonth + '/' + fYear);

        $('#txt-Fe_Emision').datepicker({}).on('changeDate', function (selected) {
          var minDate = new Date(selected.date.valueOf());
          $('#txt-Fe_Vencimiento').datepicker('setStartDate', minDate);
        });
        
        var Fe_Emision = $('#txt-Fe_Emision').val().split('/');
        $('#txt-Fe_Vencimiento').datepicker({
          autoclose: true,
          startDate: new Date(Fe_Emision[2], Fe_Emision[1] - 1, Fe_Emision[0]),
          todayHighlight: true
        })

        $('#txt-Fe_Vencimiento').val($('#txt-Fe_Emision').val());

        //Validar N/C y N/D
        $('#cbo-TiposDocumentoModificar').html('<option value="" selected="selected">- Seleccionar -</option>');
        $('#cbo-SeriesDocumentoModificar').html('<option value="" selected="selected">- Seleccionar -</option>');
        $('#txt-ID_Numero_Documento_Modificar').val('');
        $('#cbo-MotivoReferenciaModificar').html('<option value="" selected="selected">- Seleccionar -</option>');
        if (response.arrEdit[0].Nu_Enlace == 1) {
          $('.div-DocumentoModificar').show();

          url = base_url + 'HelperController/getTiposDocumentosModificar';
          $.post(url, { Nu_Tipo_Filtro: 1 }, function (responseTiposDocumentoModificar) {
            $('#cbo-TiposDocumentoModificar').html('<option value="" selected="selected">- Seleccionar -</option>');
            for (var i = 0; i < responseTiposDocumentoModificar.length; i++) {
              selected = '';
              if (response.arrEdit[0].ID_Tipo_Documento_Modificar == responseTiposDocumentoModificar[i]['ID_Tipo_Documento'])
                selected = 'selected="selected"';
              $('#cbo-TiposDocumentoModificar').append('<option value="' + responseTiposDocumentoModificar[i]['ID_Tipo_Documento'] + '" ' + selected + '>' + responseTiposDocumentoModificar[i]['No_Tipo_Documento_Breve'] + '</option>');
            }
          }, 'JSON');

          url = base_url + 'HelperController/getMotivosReferenciaModificar';
          $.post(url, { ID_Tipo_Documento: response.arrEdit[0].ID_Tipo_Documento }, function (responseMotivosReferencia) {
            $('#cbo-MotivoReferenciaModificar').html('');
            for (var i = 0; i < responseMotivosReferencia.length; i++) {
              selected = '';
              if (response.arrEdit[0].Nu_Codigo_Motivo_Referencia == responseMotivosReferencia[i]['Nu_Valor'])
                selected = 'selected="selected"';
              $('#cbo-MotivoReferenciaModificar').append('<option value="' + responseMotivosReferencia[i]['Nu_Valor'] + '" ' + selected + '>' + responseMotivosReferencia[i]['No_Descripcion'] + '</option>');
            }
          }, 'JSON');

          url = base_url + 'HelperController/getSeriesDocumentoModificar';
          $.post(url, { ID_Organizacion: response.arrEdit[0].ID_Organizacion, ID_Tipo_Documento: response.arrEdit[0].ID_Tipo_Documento_Modificar }, function (responseSeriesDocumentoModificar) {
            for (var i = 0; i < responseSeriesDocumentoModificar.length; i++) {
              sTipoSerie = ' (' + ( responseSeriesDocumentoModificar[i].ID_POS > 0 ? 'Punto Venta' : 'Factura Venta' ) + ')';
              selected = '';
              //if (response.arrEdit[0].ID_Serie_Documento_Modificar_PK == responseSeriesDocumentoModificar[i]['ID_Serie_Documento_PK'])
                //selected = 'selected="selected"';
              if (response.arrEdit[0].ID_Serie_Documento_Modificar == responseSeriesDocumentoModificar[i]['ID_Serie_Documento'])
                selected = 'selected="selected"';
              $('#cbo-SeriesDocumentoModificar').append('<option value="' + responseSeriesDocumentoModificar[i]['ID_Serie_Documento'] + '" ' + selected + ' data-id_serie_documento_pk="' + responseSeriesDocumentoModificar[i]['ID_Serie_Documento_PK'] + '">' + responseSeriesDocumentoModificar[i]['ID_Serie_Documento'] + sTipoSerie + '</option>');
            }
          }, 'JSON');

          $('#txt-ID_Numero_Documento_Modificar').val(response.arrEdit[0].ID_Numero_Documento_Modificar);
        }

        var arrParams = {
          ID_Almacen: response.arrEdit[0].ID_Almacen,
        };
        getListaPrecios(arrParams);

        if (response.arrEdit[0].Nu_Descargar_Inventario == 1) {
          $('#cbo-descargar_stock').html('<option value="1" selected>Si</option>');
          $('#cbo-descargar_stock').append('<option value="0">No</option>');
        } else {
          $('#cbo-descargar_stock').html('<option value="1">Si</option>');
          $('#cbo-descargar_stock').append('<option value="0" selected>No</option>');
        }

        /* Adicionales */
        $('[name="No_Orden_Compra_FE"]').val(response.arrEdit[0].No_Orden_Compra_FE);
        $('[name="No_Placa_FE"]').val(response.arrEdit[0].No_Placa_FE);
        $('[name="Po_Detraccion"]').val(response.arrEdit[0].Po_Detraccion);
        $('[name="Nu_Expediente_FE"]').val(response.arrEdit[0].Nu_Expediente_FE);
        $('[name="Nu_Codigo_Unidad_Ejecutora_FE"]').val(response.arrEdit[0].Nu_Codigo_Unidad_Ejecutora_FE);

        $('.div-detraccion').hide();
        if ( response.arrEdit[0].Nu_Detraccion == 1 )
          $('.div-detraccion').show();
          
        $('#cbo-sunat_tipo_transaction').html('<option value="1" selected="selected">VENTA INTERNA</option>');
        url = base_url + 'HelperController/getSunatTipoOperacion';
        $.post(url, {}, function (responseSunatTipoTransaction) {
          if (responseSunatTipoTransaction.sStatus == 'success') {
            $('#cbo-sunat_tipo_transaction').html('');
            var l = responseSunatTipoTransaction.arrData.length;
            for (var x = 0; x < l; x++) {
              selected = '';
              if (response.arrEdit[0].ID_Sunat_Tipo_Transaction == responseSunatTipoTransaction.arrData[x].ID)
                selected = 'selected="selected"';
              $('#cbo-sunat_tipo_transaction').append('<option value="' + responseSunatTipoTransaction.arrData[x].ID + '" ' + selected + '>' + responseSunatTipoTransaction.arrData[x].Nombre + '</option>');
            }
          } else {
            if (responseSunatTipoTransaction.sMessageSQL !== undefined) {
              console.log(responseSunatTipoTransaction.sMessageSQL);
            }
            console.log(responseSunatTipoTransaction.sMessage);
          }
        }, 'JSON');

        /* Personal de ventas */
        $('#cbo-vendedor').html('<option value="">- No hay personal -</option>');
        url = base_url + 'HelperController/getPersonalVentas';
        $.post(url, {}, function (responsePersonal) {
          if (responsePersonal.sStatus == 'success') {
            var l = responsePersonal.arrData.length;
            if (l == 1) {
              $('#cbo-vendedor').html('<option value="">- Seleccionar -</option>');
              selected = '';
              if (response.arrEdit[0].ID_Mesero == responsePersonal.arrData[0].ID)
                selected = 'selected="selected"';
              $('#cbo-vendedor').append('<option value="' + responsePersonal.arrData[0].ID + '" ' + selected + '>' + responsePersonal.arrData[0].Nombre + '</option>');
            } else {
              $('#cbo-vendedor').html('<option value="" selected="selected">- Seleccionar -</option>');
              for (var x = 0; x < l; x++) {
                selected = '';
                if (response.arrEdit[0].ID_Mesero == responsePersonal.arrData[x].ID)
                  selected = 'selected="selected"';
                $('#cbo-vendedor').append('<option value="' + responsePersonal.arrData[x].ID + '" ' + selected + '>' + responsePersonal.arrData[x].Nombre + '</option>');
              }
            }
          } else {
            if (responsePersonal.sMessageSQL !== undefined) {
              console.log(responsePersonal.sMessageSQL);
            }
            console.log(responsePersonal.sMessage);
          }
        }, 'JSON');
        /* /. Personal de ventas */

        /* Porcentaje para ventas */
        $('#cbo-porcentaje').html('<option value="" selected="selected">- No hay porcentaje -</option>');
        url = base_url + 'HelperController/getDataGeneral';
        $.post(url, { sTipoData: 'Porcentaje_Comision_Vendedores' }, function (responsePorcentaje) {
          if (responsePorcentaje.sStatus == 'success') {
            var l = responsePorcentaje.arrData.length;
            if (l == 1) {
              $('#cbo-porcentaje').html('<option value="">- Seleccionar -</option>');
              selected = '';
              if (response.arrEdit[0].ID_Comision == responsePorcentaje.arrData[0].ID_Tabla_Dato)
                selected = 'selected="selected"';
              $('#cbo-porcentaje').append('<option value="' + responsePorcentaje.arrData[0].ID_Tabla_Dato + '" ' + selected + '>' + responsePorcentaje.arrData[0].No_Descripcion + '</option>');
            } else {
              $('#cbo-porcentaje').html('<option value="" selected="selected">- Seleccionar -</option>');
              for (var x = 0; x < l; x++) {
                selected = '';
                if (response.arrEdit[0].ID_Comision == responsePorcentaje.arrData[x].ID_Tabla_Dato)
                  selected = 'selected="selected"';
                $('#cbo-porcentaje').append('<option value="' + responsePorcentaje.arrData[x].ID_Tabla_Dato + '" ' + selected + '>' + responsePorcentaje.arrData[x].No_Descripcion + '</option>');
              }
            }
          } else {
            if (responsePorcentaje.sMessageSQL !== undefined) {
              console.log(responsePorcentaje.sMessageSQL);
            }
            console.log(responsePorcentaje.sMessage);
          }
        }, 'JSON');
        /* /. Porcentaje para ventas */
        /* ./ Adicionales */

        $('#cbo-lista_precios').html('');
        url = base_url + 'HelperController/getListaPrecio';
        $.post(url, { Nu_Tipo_Lista_Precio: $('[name="Nu_Tipo_Lista_Precio"]').val(), ID_Organizacion: response.arrEdit[0].ID_Organizacion, ID_Almacen: response.arrEdit[0].ID_Almacen }, function (responseLista) {
          for (var i = 0; i < responseLista.length; i++) {
            selected = '';
            if (response.arrEdit[0].ID_Lista_Precio_Cabecera == responseLista[i].ID_Lista_Precio_Cabecera)
              selected = 'selected="selected"';
            $('#cbo-lista_precios').append('<option value="' + responseLista[i].ID_Lista_Precio_Cabecera + '" ' + selected + '>' + responseLista[i].No_Lista_Precio + '</option>');
          }
        }, 'JSON');

        $('[name="Txt_Glosa"]').val('');
        if (response.arrEdit[0].Txt_Glosa != '' && response.arrEdit[0].Txt_Glosa != null)
          $('[name="Txt_Glosa"]').val(response.arrEdit[0].Txt_Glosa);

        $('[name="Txt_Garantia"]').val(response.arrEdit[0].Txt_Garantia);

        // Detracción
        $('#radio-InactiveDetraccion').prop('checked', true);
        $('#radio-ActiveDetraccion').prop('checked', false);
        if (response.arrEdit[0].Nu_Detraccion != '0') {//0 = No y 1 = Si
          $('#radio-InactiveDetraccion').prop('checked', false);
          $('#radio-ActiveDetraccion').prop('checked', true);
        }

        // Retención
        $('#radio-InactiveRetencion').prop('checked', true).iCheck('update');
        $('#radio-ActiveRetencion').prop('checked', false).iCheck('update');
        if (response.arrEdit[0].Nu_Retencion != '0') {//0 = No y 1 = Si
          $('#radio-InactiveRetencion').prop('checked', false).iCheck('update');
          $('#radio-ActiveRetencion').prop('checked', true).iCheck('update');
        }

        //Formato PDF
        var arrFormatoPDF = [
          { "No_Formato_PDF": "A4" },
          { "No_Formato_PDF": "A5" },
          { "No_Formato_PDF": "TICKET" },
        ];
        $('#cbo-formato_pdf').html('');
        for (var i = 0; i < arrFormatoPDF.length; i++) {
          selected = '';
          if (response.arrEdit[0].No_Formato_PDF == arrFormatoPDF[i]['No_Formato_PDF'])
            selected = 'selected="selected"';
          $('#cbo-formato_pdf').append('<option value="' + arrFormatoPDF[i]['No_Formato_PDF'] + '" ' + selected + '>' + arrFormatoPDF[i]['No_Formato_PDF'] + '</option>');
        }

        //Detalle
        $('#table-DetalleProductosVenta').show();
        $('#table-DetalleProductosVenta tbody').empty();

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

        //descuento de total
        var $fDescuentoNCxItemTotal=parseFloat(response.arrEdit[0].Ss_Descuento) / parseInt(response.arrEdit.length);
        var $fDescuentoIGVNCxItemTotal=parseFloat(response.arrEdit[0].Ss_Descuento_Impuesto) / parseInt(response.arrEdit.length);

        for (var i = 0; i < iTotalRegistros; i++) {
          if (_ID_Producto != response.arrEdit[i].ID_Producto) {
            _ID_Producto = response.arrEdit[i].ID_Producto;
            option_impuesto_producto = '';
          }

          //Descuento de operacion para NC
          if(parseFloat($fDescuentoNCxItemTotal) > 0){
            response.arrEdit[i].Ss_Total_Producto = (response.arrEdit[i].Ss_Total_Producto - (parseFloat($fDescuentoNCxItemTotal) + parseFloat($fDescuentoIGVNCxItemTotal)));
            response.arrEdit[i].Ss_Impuesto_Producto = (response.arrEdit[i].Ss_Impuesto_Producto - parseFloat($fDescuentoIGVNCxItemTotal));
            response.arrEdit[i].Ss_SubTotal_Producto = (response.arrEdit[i].Ss_SubTotal_Producto - parseFloat($fDescuentoNCxItemTotal));
          }
            
          $Ss_SubTotal_Producto = parseFloat(response.arrEdit[i].Ss_SubTotal_Producto)
          if (response.arrEdit[i].Nu_Tipo_Impuesto == 1) {
            $Ss_Impuesto = parseFloat(response.arrEdit[i].Ss_Impuesto);
            $Ss_IGV += parseFloat(response.arrEdit[i].Ss_Impuesto_Producto);
            $Ss_Gravada += $Ss_SubTotal_Producto;
          } else if (response.arrEdit[i].Nu_Tipo_Impuesto == 2) {
            $Ss_Inafecto += $Ss_SubTotal_Producto;
          } else if (response.arrEdit[i].Nu_Tipo_Impuesto == 3) {
            $Ss_Exonerada += $Ss_SubTotal_Producto;
          } else if (response.arrEdit[i].Nu_Tipo_Impuesto == 4) {
            $Ss_Gratuita += $Ss_SubTotal_Producto;
          }

          $Ss_Descuento_Producto += parseFloat(response.arrEdit[i].Ss_Descuento_Producto);
          $Ss_Total += parseFloat(response.arrEdit[i].Ss_Total_Producto);
          $fTotalIcbper += parseFloat(response.arrEdit[i].Ss_Icbper);

          for (var x = 0; x < iTotalRegistrosImpuestos; x++) {
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
            +"<td class='text-right'><input type='text' inputmode='decimal' class='pos-input txt-Qt_Producto form-control input-decimal input-size_cantidad' value='" + Math.round10(response.arrEdit[i].Qt_Producto, -3) + "' " + (response.arrEdit[i].Nu_Tipo_Producto == 1 ? 'onkeyup=validateStockNow(event);' : '') + " data-nu_activar_precio_x_mayor=" + response.arrEdit[i].Nu_Activar_Precio_x_Mayor + " data-id_item='" + response.arrEdit[i].ID_Producto + "' data-ss_icbper_item='" + response.arrEdit[i].Ss_Icbper + "' data-ss_icbper='" + response.arrEdit[i].Ss_Icbper_Item + "' data-id_impuesto_icbper='" + response.arrEdit[i].ID_Impuesto_Icbper + "' data-id_producto='" + response.arrEdit[i].ID_Producto + "' autocomplete='off'></td>"
            +"<td class='text-left'>"
              + '<span style="font-size: 11px;font-weight:normal;">[' + response.arrEdit[i].Nu_Codigo_Barra + ']<br>'
              + '<span style="font-size: 13px;font-weight:bold;">' + response.arrEdit[i].No_Producto + '</span>'
              + sVarianteMultipleTmp
              + (response.arrEdit[i].No_Unidad_Medida !== undefined && response.arrEdit[i].No_Unidad_Medida !== null && response.arrEdit[i].No_Unidad_Medida != '' ? ' <br><span style="font-size: 10px;font-weight:normal;">[' + response.arrEdit[i].No_Unidad_Medida + ']</span> ' : '')
            + "</td>"
            +"<td style='display:none; width: 39%' class='text-right td-sNotaItem' data-estado='mostrar' data-id_item=" + response.arrEdit[i].ID_Producto + " id='td-sNotaItem" + response.arrEdit[i].ID_Producto + "'>"
              +"<textarea class='pos-input form-control input-sNotaItem hotkey-cobrar_cliente hotkey-cancelar_venta hotkey-limpiar_item hotkey-focus_item' placeholder='' maxlength='250' autocomplete='off'>" + (response.arrEdit[i].Txt_Nota_Item != null ? sVarianteMultipleTmp + ' ' + response.arrEdit[i].Txt_Nota_Item : sVarianteMultipleTmp) + "</textarea></td>"
            +"</td>"
            +"<td class='text-center'>"
              +"<button type='button' id='btn-add_nota_producto_pos' class='btn btn-sm btn-link' alt='Nota' title='Nota'><i class='fa fa-edit fa-2x' aria-hidden='true'></i></button>"
            +"</td>"
            +"<td class='text-right'><input type='text' class='pos-input txt-fValorUnitario form-control input-decimal input-size_importe' value='" + parseFloat((parseFloat(response.arrEdit[i].Ss_Total_Producto) / parseFloat(response.arrEdit[i].Qt_Producto)) / response.arrEdit[i].Ss_Impuesto).toFixed(3) + "' autocomplete='off'></td>"
            +"<td class='text-right'><input type='text' class='pos-input txt-Ss_Precio form-control input-decimal input-size_importe' data-precio_actual='" + Math.round10(parseFloat(response.arrEdit[i].Ss_Total_Producto) / parseFloat(response.arrEdit[i].Qt_Producto), -3) + "' value='" + Math.round10(parseFloat(response.arrEdit[i].Ss_Total_Producto) / parseFloat(response.arrEdit[i].Qt_Producto), -3) + "' autocomplete='off'></td>"
            +"<td class='text-right'>"
              +"<select class='cbo-ImpuestosProducto form-control required input-size_otros' style='width: 100%;'>"
                + option_impuesto_producto
              +"</select>"
            +"</td>"
            +"<td style='display:none;' class='text-right'><input type='tel' inputmode='decimal' class='pos-input txt-Ss_SubTotal_Producto form-control input-decimal' value='" + response.arrEdit[i].Ss_SubTotal_Producto + "' autocomplete='off' disabled></td>"
            +"<td class='text-right'><input type='text' inputmode='decimal' class='pos-input txt-Ss_Descuento form-control input-decimal input-size_otros' value='' autocomplete='off'></td>"
            +"<td class='text-right'><input type='text' inputmode='decimal' class='pos-input txt-Ss_Total_Producto form-control input-decimal input-size_importe' value='" + response.arrEdit[i].Ss_Total_Producto + "' autocomplete='off'></td>"
            +"<td style='display:none;' class='text-right'><input type='text' inputmode='numeric' class='pos-input txt-Nu_Lote_Vencimiento form-control input-codigo_barra' placeholder='Opcional' value='" + (response.arrEdit[i].Nu_Lote_Vencimiento != null ? response.arrEdit[i].Nu_Lote_Vencimiento : '') + "' autocomplete='off'></td>"
            +"<td style='display:none;' class='text-right'><input type='text' class='pos-input txt-Fe_Lote_Vencimiento form-control date-picker-invoice' placeholder='Opcional' value='" + (response.arrEdit[i].Fe_Lote_Vencimiento != null ? ParseDateString(response.arrEdit[i].Fe_Lote_Vencimiento, 6, '-') : '') + "' autocomplete='off'></td>"
            +"<td style='display:none;' class='text-right td-fDescuentoSinImpuestosItem'>0</td>"
            +"<td style='display:none;' class='text-right td-fDescuentoImpuestosItem'>0</td>"
            +"<td class='text-center'><button type='button' id='btn-deleteProducto' class='btn btn-sm btn-link' alt='Eliminar' title='Eliminar'><i class='fa fa-trash-o fa-2x' aria-hidden='true'> </i></button></td>"
          +"</tr>";
        }

        $('#table-DetalleProductosVenta > tbody').append(table_detalle_producto);

        $('.txt-Fe_Lote_Vencimiento').datepicker({
          autoclose: true,
          startDate: new Date(fYear, fToday.getMonth(), fDay),
          todayHighlight: true
        })

        /*
        if (parseFloat(response.arrEdit[0].Ss_Descuento) > 0.00 && $Ss_Impuesto > 0) {
          $Ss_Gravada = parseFloat(response.arrEdit[0].Ss_Total) / $Ss_Impuesto;
          $Ss_IGV = parseFloat(response.arrEdit[0].Ss_Total) - $Ss_Gravada;
        }
        */
        //modificado el 21/08/2022

        $('#txt-subTotal').val($Ss_Gravada.toFixed(2));
        $('#span-subTotal').text($Ss_Gravada.toFixed(2));

        $('#txt-exonerada').val($Ss_Exonerada.toFixed(2));
        $('#span-exonerada').text($Ss_Exonerada.toFixed(2));

        $('#txt-inafecto').val($Ss_Inafecto.toFixed(2));
        $('#span-inafecto').text($Ss_Inafecto.toFixed(2));

        $('#txt-gratuita').val($Ss_Gratuita.toFixed(2));
        $('#span-gratuita').text($Ss_Gratuita.toFixed(2));

        /*
        if (parseFloat(response.arrEdit[0].Ss_Descuento) > 0 && $Ss_Descuento_Producto == 0)
          $('#txt-Ss_Descuento').val(response.arrEdit[0].Po_Descuento);
        else
          $('#txt-Ss_Descuento').val('');

        $('#txt-descuento').val(response.arrEdit[0].Ss_Descuento);
        $('#span-descuento').text(response.arrEdit[0].Ss_Descuento);
        */
        //modificado el 21/08/2022
        $('#txt-Ss_Descuento').val('0.00');
        $('#txt-descuento_igv').val('0.00');
        $('#txt-descuento').val('0.00');
        $('#span-descuento').text('0.00');

        $('#txt-impuesto').val($Ss_IGV.toFixed(2));
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
        $.post(url, function (response) {
          arrImpuestosProducto = '';
          arrImpuestosProductoDetalle = '';
          for (var i = 0; i < response.length; i++)
            arrImpuestosProductoDetalle += '{"ID_Impuesto_Cruce_Documento" : "' + response[i].ID_Impuesto_Cruce_Documento + '", "Ss_Impuesto":"' + response[i].Ss_Impuesto + '", "Nu_Tipo_Impuesto":"' + response[i].Nu_Tipo_Impuesto + '", "No_Impuesto":"' + response[i].No_Impuesto + '"},';
          arrImpuestosProducto = '{ "arrImpuesto" : [' + arrImpuestosProductoDetalle.slice(0, -1) + ']}';

          $('#modal-loader').modal('hide');
        }, 'JSON');

        var _ID_Producto = '';
        var option_impuesto_producto = '';
      } else {
        $('#modal-loader').modal('hide');
        $('.div-Listar').show();

        $('.modal-message').removeClass('modal-danger modal-warning modal-success');

        $('#modal-message').modal('show');
        $('.modal-message').addClass('modal-danger');
        $('.modal-title-message').text(response.message);
        setTimeout(function () { $('#modal-message').modal('hide'); }, 6000);

        //Message for developer
        console.log(jqXHR.responseText);

        $('#btn-ticket').text('');
        $('#btn-ticket').attr('disabled', false);
        $('#btn-ticket').append('Generar venta');
        $('#btn-salir').attr('disabled', false);
      }
    }//success
  })
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
  if (arrParams.ID_Tipo_Documento == 2) {
    $('.div-tipoguia').hide();
  }

  $('#cbo-transporte').html('<option value="0" selected="selected">- Sin registro -</option>');
  url = base_url + 'HelperController/getDeliveryVentas';
  var arrPost = {};
  $.post(url, arrPost, function (response) {
    if (response.sStatus == 'success') {
      var l = response.arrData.length;
      if (l == 1) {
        $('#cbo-transporte').html('<option value="' + response.arrData[0].ID + '" data-id_tipo_documento_identidad="' + response.arrData[0].ID_Tipo_Documento_Identidad + '" data-numero_documento_identidad="' + response.arrData[0].Nu_Documento_Identidad + '">' + response.arrData[0].Nombre + '</option>');
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

  var sTipoDocumento = 'Boleta';
  if (arrParams.ID_Tipo_Documento == 3)
    sTipoDocumento ='Factura';
  else if (arrParams.ID_Tipo_Documento == 2)
    sTipoDocumento ='Nota de Venta';

  //$('#modal-header-generar_guia-title').text(arrParams.No_Tipo_Documento_Breve + ' - ' + arrParams.ID_Serie_Documento + ' - ' + arrParams.ID_Numero_Documento);
  $('#modal-header-generar_guia-title').text(sTipoDocumento + ' - ' + arrParams.ID_Serie_Documento + ' - ' + arrParams.ID_Numero_Documento);
  //$('#generar_guia-modal-body-cliente').text('');

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
  var arrParamsUbigeo = {
    sTipoData : 'Ubigeo_INEI',
  }
  $.post( url, arrParamsUbigeo, function( response ){
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
  
  $('#span-stock').html('');
  $("#cbo-descargar_stock-modal").css("background-color", "");
  $("#cbo-descargar_stock-modal").css("pointer-events", "");
  if (arrParams.Nu_Descargar_Inventario==1) {
    $('#cbo-descargar_stock-modal').val(0);//no descargo nuevamente
    $("#cbo-descargar_stock-modal").css("background-color", "#d2d6de");
    $("#cbo-descargar_stock-modal").css("pointer-events", "none");

    $('#span-stock').html('Ya se realizo la descarga de stock');
  }
}

function calcularImportexItem(fila){  
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
    $( '#table-DetalleProductosVenta tfoot' ).empty();
    if (nu_tipo_impuesto == 1){//CON IGV
      fDescuento_SubTotal_Producto = parseFloat(((precio * cantidad) - (((descuento * (precio * cantidad)) / 100))) / impuesto_producto);
      fDescuento_Total_Producto = parseFloat(((precio * cantidad) - (((descuento * (precio * cantidad)) / 100))));
      fila.find(".td-fDescuentoSinImpuestosItem").text( (parseFloat((((descuento * (precio * cantidad)) / 100) / impuesto_producto)).toFixed(2)).toString().split(". ") );
      fila.find(".td-fDescuentoImpuestosItem").text( (parseFloat(((descuento * (precio * cantidad)) / 100) - (((descuento * (precio * cantidad)) / 100) / impuesto_producto)).toFixed(2)).toString().split(". ") );
      fila.find(".txt-Ss_SubTotal_Producto").val( (parseFloat(fDescuento_SubTotal_Producto).toFixed(6)).toString().split(". ") );
      fila.find(".txt-Ss_Total_Producto").val( (parseFloat(fDescuento_Total_Producto).toFixed(2)).toString().split(". ") );
      
      var $Ss_SubTotal = 0.00;
      var $Ss_Descuento = 0.00;
      var $Ss_IGV = 0.00;
      var $Ss_Total = 0.00;
      $("#table-DetalleProductosVenta > tbody > tr").each(function(){
        var rows = $(this);
        var Ss_Impuesto           = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
        var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
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
        
        $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto) )) / 100);
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
      fila.find(".td-fDescuentoSinImpuestosItem").text( (parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". ") );
      fila.find(".td-fDescuentoImpuestosItem").text( (parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". ") );
      fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
      fila.find(".txt-Ss_Total_Producto").val((parseFloat(((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)) * impuesto_producto).toFixed(2)).toString().split(". "));

      var $Ss_Inafecto = 0.00;
      var $Ss_Descuento = 0.00;
      var $Ss_Total = 0.00;
      $("#table-DetalleProductosVenta > tbody > tr").each(function(){
        var rows = $(this);
        var Ss_Impuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
        var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
        var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
        var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());

        if(isNaN($Ss_Descuento_Producto))
          $Ss_Descuento_Producto = 0;
          
        if (Nu_Tipo_Impuesto == 2)
          $Ss_Inafecto += $Ss_SubTotal_Producto;
        
        $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto) )) / 100);
        $Ss_Total += parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
      });
      
      $( '#txt-inafecto' ).val( $Ss_Inafecto.toFixed(2) );
      $( '#span-inafecto' ).text( $Ss_Inafecto.toFixed(2) );
      
      $( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
      $( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
      
      $( '#txt-total' ).val( $Ss_Total.toFixed(2) );
      $( '#span-total' ).text( $Ss_Total.toFixed(2) );
      $( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
    } else if (nu_tipo_impuesto == 3){//Exonerada
      fila.find(".td-fDescuentoSinImpuestosItem").text( (parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". ") );
      fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
      fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
      fila.find(".txt-Ss_Total_Producto").val((parseFloat(((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)) * impuesto_producto).toFixed(2)).toString().split(". "));
      
      var $Ss_Exonerada = 0.00;
      var $Ss_Descuento = 0.00;
      var $Ss_Total = 0.00;
      $("#table-DetalleProductosVenta > tbody > tr").each(function(){
        var rows = $(this);
        var Ss_Impuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
        var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
        var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
        var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());

        if(isNaN($Ss_Descuento_Producto))
          $Ss_Descuento_Producto = 0;
          
        if (Nu_Tipo_Impuesto == 3)
          $Ss_Exonerada += $Ss_SubTotal_Producto;
        
        $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto) )) / 100);
        $Ss_Total += parseFloat(rows.find('.txt-Ss_Total_Producto', this).val());
      });
      
      $( '#txt-exonerada' ).val( $Ss_Exonerada.toFixed(2) );
      $( '#span-exonerada' ).text( $Ss_Exonerada.toFixed(2) );
      
      $( '#txt-descuento' ).val( $Ss_Descuento.toFixed(2) );
      $( '#span-descuento' ).text( $Ss_Descuento.toFixed(2) );
      
      $( '#txt-total' ).val( $Ss_Total.toFixed(2) );
      $( '#span-total' ).text( $Ss_Total.toFixed(2) );
      $( '#span-total_importe' ).text( $Ss_Total.toFixed(2) );
    } else if (nu_tipo_impuesto == 4){//Gratuita
      fila.find(".td-fDescuentoSinImpuestosItem").text( (parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". ") );
      fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
      fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
      fila.find(".txt-Ss_Total_Producto").val((parseFloat(((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)) * impuesto_producto).toFixed(2)).toString().split(". "));
      
      var $Ss_Gratuita = 0.00;
      var $Ss_Descuento = 0.00;
      var $Ss_Total = 0.00;
      $("#table-DetalleProductosVenta > tbody > tr").each(function(){
        var rows = $(this);
        var Ss_Impuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
        var Nu_Tipo_Impuesto      = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
        var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val());
        var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());

        if(isNaN($Ss_Descuento_Producto))
          $Ss_Descuento_Producto = 0;
          
        if (Nu_Tipo_Impuesto == 4)
          $Ss_Gratuita += $Ss_SubTotal_Producto;
        
        $Ss_Descuento += (($Ss_Descuento_Producto * ((parseFloat(rows.find('.txt-Qt_Producto', this).val()) * parseFloat(rows.find('.txt-Ss_Precio', this).val()) / Ss_Impuesto) )) / 100);
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
    
    calcularIcbper();
    calcularDescuentoTotal(0);
  }// IF - ELSE PRECIO Y CANTIDAD > 0
}

function recuperarPDFVentaSunat(ID) {
  $('#btn-sunat-pdf-' + ID).attr('disabled', true);
  $('#span-sunat-pdf-' + ID).append('<i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>');

  var $modal_id = $('#modal-correo_sunat');

  var sendPost = {
    iIdDocumentoCabecera: ID,
    sTipoRespuesta: 'json'
  };

  url = base_url + 'DocumentoElectronicoController/recuperarPDFVentaSunat';
  $.ajax({
    url: url,
    type: "POST",
    dataType: "JSON",
    data: sendPost,
    success: function (response) {
      $modal_id.modal('hide');

      $('.modal-message').removeClass('modal-danger modal-warning modal-success');
      $('#modal-message').modal('show');

      $('#btn-sunat-pdf-' + ID).attr('disabled', false);

      $('#span-sunat-pdf-' + ID).html('');

      if (response.status == 'success') {
        $('#txt-email_correo_sunat').val('');

        $('.modal-message').addClass('modal-' + response.status);
        $('.modal-title-message').text(response.message);
        setTimeout(function () { $('#modal-message').modal('hide'); }, 1200);
        reload_table_venta();
      } else {
        $('.modal-message').addClass('modal-' + response.status);
        $('.modal-title-message').text(response.message);
        setTimeout(function () { $('#modal-message').modal('hide'); }, 1800);
        reload_table_venta();
      }
    }
  });
}