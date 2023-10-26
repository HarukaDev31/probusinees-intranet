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
  	
  $( '#form-Compra' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '.title_Compra' ).text('Nuevo Compra');

  $('.div-adicionales_ov_garantia_glosa').css("display", "none");
  $('#btn-adicionales_ov_garantia_glosa').data('ver_adicionales_ov_garantia_glosa', 0);

  $('.panel_body_total_todo').css("display", "none");
  $('#btn-ver_total_todo').data('ver_total_todo', 0);
  $('#btn-ver_total_todo').text('VER / DESCUENTO');

  $('#radio-tipo_transporte_publico').prop('checked', true).iCheck('update');
  $('#radio-tipo_transporte_privado').prop('checked', false).iCheck('update');

  $('[name="EID_Empresa"]').val('');
  $('[name="EID_Guia_Cabecera"]').val('');

  $('#cbo-almacen_externo').html('');
  
  $( '.date-picker-invoice' ).val(fDay + '/' + fMonth + '/' + fYear);
  $('[name="Fe_Traslado"]').val(fDay + '/' + fMonth + '/' + fYear);

  $('#txt-Fe_Emision').datepicker({}).on('changeDate', function (selected) {
    var minDate = new Date(selected.date.valueOf());
    $('[name="Fe_Traslado"]').datepicker('setStartDate', minDate);
  });

  var Fe_Emision = $('#txt-Fe_Emision').val().split('/');
  $('[name="Fe_Traslado"]').datepicker({
    autoclose: true,
    startDate: new Date(Fe_Emision[2], Fe_Emision[1] - 1, Fe_Emision[0]),
    todayHighlight: true
  })

  $('#radio-ActiveFlete').prop('checked', true);
  $( '#radio-InActiveFlete' ).prop('checked', false);
    
  $( '#cbo-OrganizacionesVenta' ).prop('disabled', false);
  
  $( '#radio-cliente_existente' ).prop('checked', true).iCheck('update');
  $( '#radio-cliente_nuevo' ).prop('checked', false).iCheck('update');
  $( '.div-cliente_existente' ).show();
  $( '.div-cliente_nuevo' ).hide();

  $('#div-addFlete').show();
  $('#radio-flete_si').prop('checked', true).iCheck('update');
  $('#radio-flete_no').prop('checked', false).iCheck('update');
  
  $('#table-DetalleProductos tbody').empty();
  $('#table-DetalleProductos tfoot').empty();
  $('#table-DetalleProductosOrdenVentaModal tbody').empty();
  $('#table-DetalleProductosOrdenVentaModal tfoot').empty();
	
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
  $( '#span-total_cantidad' ).text( '0' );

  $( '.span-signo' ).text( 'S/' );

  $( '#btn-save' ).attr('disabled', false);
  
  considerar_igv=0;
  
  url = base_url + 'HelperController/getTiposDocumentos';
  $.post( url, {Nu_Tipo_Filtro : 7}, function( response ){//2 = Compra
    $( '#cbo-TiposDocumento' ).html('<option value="" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-TiposDocumento' ).append( '<option value="' + response[i]['ID_Tipo_Documento'] + '" data-nu_impuesto="' + response[i]['Nu_Impuesto'] + '" data-nu_enlace="' + response[i]['Nu_Enlace'] + '">' + response[i]['No_Tipo_Documento_Breve'] + '</option>' );
  }, 'JSON');

  url = base_url + 'HelperController/getTiposDocumentoIdentidad';
  $.post( url , function( response ){
    $( '#cbo-TiposDocumentoIdentidadCliente' ).html('');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-TiposDocumentoIdentidadCliente' ).append( '<option value="' + response[i]['ID_Tipo_Documento_Identidad'] + '" data-nu_cantidad_caracteres="' + response[i]['Nu_Cantidad_Caracteres'] + '">' + response[i]['No_Tipo_Documento_Identidad_Breve'] + '</option>' );
  }, 'JSON');
	
  $( '#panel-DetalleProductos' ).show();
  
  url = base_url + 'HelperController/getMonedas';
  $.post( url , function( response ){
    $( '#cbo-Monedas' ).html('');
    $( '.span-signo' ).text(response[0]['No_Signo']);
    for (var i = 0; i < response.length; i++)
      $( '#cbo-Monedas' ).append( '<option value="' + response[i]['ID_Moneda'] + '" data-no_signo="' + response[i]['No_Signo'] + '">' + response[i]['No_Moneda'] + '</option>' );
  }, 'JSON');
	  
  $( '#cbo-descargar_stock' ).html( '<option value="1">Si</option>' );
  $( '#cbo-descargar_stock' ).append( '<option value="0">No</option>' );
  $( '#cbo-almacen' ).show();

  var arrParams = {
    ID_Almacen: $('#cbo-almacen').val(),
  };
  getListaPrecios(arrParams);

  url = base_url + 'HelperController/getTipoMovimiento';
  $.post(url, { Nu_Tipo_Movimiento: 1 }, function (response) {
    $('#cbo-tipo_movimiento').html('<option value="" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++)
      $('#cbo-tipo_movimiento').append('<option value="' + response[i]['ID_Tipo_Movimiento'] + '">' + response[i]['No_Tipo_Movimiento'] + '</option>');
  }, 'JSON');

  url = base_url + 'HelperController/getValoresTablaDato';
  $.post(url, { sTipoData: 'Motivo_Traslado'}, function (response) {
    $('#cbo-motivo_traslado').html('<option value="0" selected="selected">- Sin datos -</option>');
    if ( response.sStatus == 'success' ) {
      $('#cbo-motivo_traslado').html('<option value="0" selected="selected">- Seleccionar -</option>');
      var response = response.arrData;
      for (var i = 0; i < response.length; i++)
        $('#cbo-motivo_traslado').append('<option value="' + response[i].ID_Tabla_Dato + '">' + response[i].No_Descripcion + '</option>');
    }
  }, 'JSON');

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
  $('#cbo-formato_pdf').val('A4');
  $('#btn-save').show();
  
  $('#div-addFlete').hide();

  $('#radio-flete_si').prop('checked', false).iCheck('update');
  $('#radio-flete_no').prop('checked', true).iCheck('update');

  //UBIGEO
  url = base_url + 'HelperController/getValoresTablaDato';
  var arrParams = {
    sTipoData : 'Ubigeo_INEI',
  }
  $.post( url, arrParams, function( response ){
    if ( response.sStatus == 'success' ) {
      var iTotalRegistros = response.arrData.length, response=response.arrData;
      $( '#cbo-ubigeo_inei' ).html( '<option value="" selected="selected">- Seleccionar -</option>');
      for (var i = 0; i < iTotalRegistros; i++) {
        selected = '';
        if(response[i].ID_Tabla_Dato == 1444)
          selected = 'selected="selected"';
        $( '#cbo-ubigeo_inei' ).append( '<option value="' + response[i].ID_Tabla_Dato + '" ' + selected + '>' + response[i].Nu_Valor + ': ' + response[i].No_Descripcion + '</option>' );
      }
    } else {
      $( '#cbo-ubigeo_inei' ).html( '<option value="" selected="selected">- Vacío -</option>');
      console.log( response );
    }
  }, 'JSON');

  //UBIGEO
  $('.div-electronico').hide();
}

function verCompra(ID, iEnlace){
  accion = 'upd_factura_compra';
  $( '#modal-loader' ).modal('show');
  
  $( '.div-Listar' ).hide();
  	
  $( '#form-Compra' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();

  $('.div-adicionales_ov_garantia_glosa').css("display", "none");
  $('#btn-adicionales_ov_garantia_glosa').data('ver_adicionales_ov_garantia_glosa', 0);

  $('.panel_body_total_todo').css("display", "none");
  $('#btn-ver_total_todo').data('ver_total_todo', 0);
  $('#btn-ver_total_todo').text('VER / DESCUENTO');

  $('#btn-save').show();
  //if (iEnlace == 1)
    //$('#btn-save').hide();

  $('#radio-ActiveFlete').prop('checked', true);
  $( '#radio-InActiveFlete' ).prop('checked', false);
  
  $( '#cbo-OrganizacionesVenta' ).prop('disabled', true);
 
  $( '#radio-cliente_existente' ).prop('checked', true).iCheck('update');
  $( '#radio-cliente_nuevo' ).prop('checked', false).iCheck('update');
  $( '.div-cliente_existente' ).show();
  $( '.div-cliente_nuevo' ).hide();
  
  url = base_url + 'HelperController/getTiposDocumentoIdentidad';
  $.post( url , function( response ){
    $( '#cbo-TiposDocumentoIdentidadCliente' ).html('');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-TiposDocumentoIdentidadCliente' ).append( '<option value="' + response[i]['ID_Tipo_Documento_Identidad'] + '" data-nu_cantidad_caracteres="' + response[i]['Nu_Cantidad_Caracteres'] + '">' + response[i]['No_Tipo_Documento_Identidad_Breve'] + '</option>' );
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
  $( '#span-total_cantidad' ).text( texto_importes_cero );

  $( '#btn-save' ).attr('disabled', false);

  considerar_igv=0;

  $('#table-DetalleProductos tbody').empty();
  $('#table-DetalleProductos tfoot').empty();
  $('#table-DetalleProductosOrdenVentaModal tbody').empty();
  $('#table-DetalleProductosOrdenVentaModal tfoot').empty();

  url = base_url + 'Logistica/SalidaInventarioController/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $( '.div-AgregarEditar' ).show();
      
      $( '.title_Compra' ).text('Modifcar Compra');
      
      $('[name="EID_Empresa"]').val(response.arrEdit[0].ID_Empresa);
      $('[name="EID_Guia_Cabecera"]').val(response.arrEdit[0].ID_Guia_Cabecera);
      
      //Datos Documento
      considerar_igv = response.arrEdit[0].Nu_Impuesto;

	    nu_enlace = response.arrEdit[0].Nu_Enlace;
      url = base_url + 'HelperController/getTiposDocumentos';
      $.post( url, {Nu_Tipo_Filtro : 7}, function( responseTiposDocumento ){//2 = Compra
        $( '#cbo-TiposDocumento' ).html('');
        for (var i = 0; i < responseTiposDocumento.length; i++){
          selected = '';
          if(response.arrEdit[0].ID_Tipo_Documento == responseTiposDocumento[i]['ID_Tipo_Documento'])
            selected = 'selected="selected"';
          $( '#cbo-TiposDocumento' ).append( '<option value="' + responseTiposDocumento[i]['ID_Tipo_Documento'] + '" data-nu_impuesto="' + responseTiposDocumento[i]['Nu_Impuesto'] + '" data-nu_enlace="' + responseTiposDocumento[i]['Nu_Enlace'] + '" ' + selected + '>' + responseTiposDocumento[i]['No_Tipo_Documento_Breve'] + '</option>' );
        }
      }, 'JSON');
      
      $('#btn-save').hide();      
      url = base_url + 'HelperController/getSeriesDocumento';
      $.post(url, { ID_Organizacion: response.arrEdit[0].ID_Organizacion, ID_Tipo_Documento: response.arrEdit[0].ID_Tipo_Documento }, function (responseSeriesDocumento) {
        $('#cbo-SeriesDocumento').html('');
        for (var i = 0; i < responseSeriesDocumento.length; i++) {
          selected = '';
          //if(responseSeriesDocumento[i]['ID_POS']>0)
          if (response.arrEdit[0].ID_Serie_Documento == responseSeriesDocumento[i]['ID_Serie_Documento']) {
            $('#btn-save').show();
            selected = 'selected="selected"';
          }
          $('#cbo-SeriesDocumento').append('<option value="' + responseSeriesDocumento[i]['ID_Serie_Documento'] + '" ' + selected + ' data-id_serie_documento_pk=' + responseSeriesDocumento[i].ID_Serie_Documento_PK + '>' + responseSeriesDocumento[i]['ID_Serie_Documento'] + '</option>');
        }
      }, 'JSON');
      
      //UBIGEO
      $('.div-electronico').hide();
      if(response.arrEdit[0].ID_Serie_Documento.substr(0, 1)=='T'){
        $('.div-electronico').show();
      }

      $('[name="ID_Numero_Documento"]').val(response.arrEdit[0].ID_Numero_Documento);
      
      $('[name="Fe_Emision"]').val(ParseDateString(response.arrEdit[0].Fe_Emision, 6, '-'));
      
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
      
      if ( response.arrEdit[0].Nu_Descargar_Inventario == 1 ) {
        $( '#cbo-descargar_stock' ).html( '<option value="1" selected>Si</option>' );
        $( '#cbo-descargar_stock' ).append( '<option value="0">No</option>' );
      } else {
        $( '#cbo-descargar_stock' ).html( '<option value="1">Si</option>' );
        $( '#cbo-descargar_stock' ).append( '<option value="0" selected>No</option>' );
      }

      $('#cbo-tipo_movimiento').html('<option value="" selected="selected">- Seleccionar -</option>');
      url = base_url + 'HelperController/getTipoMovimiento';
      $.post(url, { Nu_Tipo_Movimiento: 1 }, function (responseTiposMovimiento) {
        for (var i = 0; i < responseTiposMovimiento.length; i++) {
          selected = '';
          if (response.arrEdit[0].ID_Tipo_Movimiento == responseTiposMovimiento[i]['ID_Tipo_Movimiento'])
            selected = 'selected="selected"';
          $('#cbo-tipo_movimiento').append('<option value="' + responseTiposMovimiento[i]['ID_Tipo_Movimiento'] + '" ' + selected + '>' + responseTiposMovimiento[i]['No_Tipo_Movimiento'] + '</option>');
        }
      }, 'JSON');

      //Datos Cliente
      $('[name="AID"]').val(response.arrEdit[0].ID_Entidad);
      $('[name="ANombre"]').val(response.arrEdit[0].No_Entidad);
      $('[name="ACodigo"]').val(response.arrEdit[0].Nu_Documento_Identidad);
      $('[name="Txt_Email_Entidad"]').val(response.arrEdit[0].Txt_Email_Entidad);
      $('[name="Nu_Celular_Entidad"]').val(response.arrEdit[0].Nu_Celular_Entidad);
      $('[name="Txt_Direccion_Entidad"]').val(response.arrEdit[0].Txt_Direccion_Destino);

      //Transferencia entre almacenes
      $('#cbo-almacen_externo').html('');
      if ( response.arrEdit[0].ID_Tipo_Movimiento == 15 ) {
        url = base_url + 'HelperController/getOrganizacionesAlcenesEmpresaExternos';
        $.post(url, { 'iIdAlmacen': response.arrEdit[0].ID_Almacen }, function (responseAlmacenTransferencia) {
          $('#cbo-almacen_externo').html('<option value="0">- Seleccionar -</option>');
          if (responseAlmacenTransferencia.sStatus == 'success') {
            responseAlmacenTransferencia = responseAlmacenTransferencia.arrData;
            for (var i = 0; i < responseAlmacenTransferencia.length; i++) {
              selected = '';
              if (response.arrEdit[0].ID_Almacen_Transferencia == responseAlmacenTransferencia[i]['ID_Almacen'])
                selected = 'selected="selected"';
              $('#cbo-almacen_externo').append('<option value="' + responseAlmacenTransferencia[i]['ID_Almacen'] + '" data-direccion="' + responseAlmacenTransferencia[i]['Txt_Direccion_Almacen'] + '" ' + selected + '>' + responseAlmacenTransferencia[i]['No_Almacen'] + '</option>');
            }
          }
        }, 'JSON');
      }
      
      //UBIGEO
      url = base_url + 'HelperController/getValoresTablaDato';
      var arrParams = {
        sTipoData : 'Ubigeo_INEI',
      }
      $.post( url, arrParams, function( responseUbigeo ){
        if ( responseUbigeo.sStatus == 'success' ) {
          var iTotalRegistros = responseUbigeo.arrData.length, responseUbigeo=responseUbigeo.arrData;
          $( '#cbo-ubigeo_inei' ).html( '<option value="" selected="selected">- Seleccionar -</option>');
          for (var i = 0; i < iTotalRegistros; i++){
            selected = '';
            if(response.arrEdit[0].ID_Ubigeo_Inei_Llegada == responseUbigeo[i].ID_Tabla_Dato)
              selected = 'selected="selected"';
            $( '#cbo-ubigeo_inei' ).append( '<option value="' + responseUbigeo[i].ID_Tabla_Dato + '" ' + selected + '>' + responseUbigeo[i].Nu_Valor + ': ' + responseUbigeo[i].No_Descripcion + '</option>' );
          }

          //1444 = LIMA LIMA LIMA UBIGEO
          if(response.arrEdit[0].ID_Ubigeo_Inei_Llegada==0) {
            $('#cbo-ubigeo_inei').val('1444');
            $('#cbo-ubigeo_inei').select().trigger('change');
          }
        } else {
          $( '#cbo-ubigeo_inei' ).html( '<option value="" selected="selected">- Vacío -</option>');
          console.log( responseUbigeo );
        }
      }, 'JSON');

      // Flete
      $('#div-addFlete').hide();

      $('#radio-flete_si').prop('checked', false).iCheck('update');
      $('#radio-flete_no').prop('checked', true).iCheck('update');
      if (response.arrEdit[0].ID_Entidad_Transportista != '' && response.arrEdit[0].ID_Entidad_Transportista != null) {
        $('#div-addFlete').show();

        $('#radio-flete_si').prop('checked', true).iCheck('update');
        $('#radio-flete_no').prop('checked', false).iCheck('update');

        $('[name="AID_Transportista"]').val(response.arrEdit[0].ID_Entidad_Transportista);
        $('[name="Transportista-ID_Tipo_Documento_Identidad"]').val(response.arrEdit[0].ID_Tipo_Documento_Identidad_Transportista);
        $('[name="ANombre_Transportista"]').val(response.arrEdit[0].No_Entidad_Transportista);
        $('[name="ACodigo_Transportista"]').val(response.arrEdit[0].Nu_Documento_Identidad_Transportista);

        $('[name="No_Placa"]').val(response.arrEdit[0].No_Placa);

        $('#txt-Fe_Emision').datepicker({}).on('changeDate', function (selected) {
          var minDate = new Date(selected.date.valueOf());
          $('[name="Fe_Traslado"]').datepicker('setStartDate', minDate);
        });

        var Fe_Emision = response.arrEdit[0].Fe_Emision.split('-');
        $('[name="Fe_Traslado"]').datepicker({
          autoclose: true,
          startDate: new Date(parseInt(Fe_Emision[0]), parseInt(Fe_Emision[1]) - 1, parseInt(Fe_Emision[2])),
          todayHighlight: true
        })

        $('[name="Fe_Traslado"]').datepicker('setStartDate', new Date(Fe_Emision[0] + "/" + Fe_Emision[1] + "/" + Fe_Emision[2]));
        $('[name="Fe_Traslado"]').val(ParseDateString(response.arrEdit[0].Fe_Traslado, 6, '-'));

        $('[name="Ss_Peso_Bruto"]').val(response.arrEdit[0].Ss_Peso_Bruto);
        $('[name="Nu_Bulto"]').val(response.arrEdit[0].Nu_Bulto);
        $('[name="No_Licencia"]').val(response.arrEdit[0].No_Licencia);
        $('[name="No_Certificado_Inscripcion"]').val(response.arrEdit[0].No_Certificado_Inscripcion);
      }

      url = base_url + 'HelperController/getMotivosTraslado';
      $.post(url, function (responseMT) {
        $('#cbo-motivo_traslado').html('');
        for (var i = 0; i < responseMT.length; i++) {
          selected = '';
          if (response.arrEdit[0].ID_Motivo_Traslado == responseMT[i].ID_Tabla_Dato)
            selected = 'selected="selected"';
          $('#cbo-motivo_traslado').append('<option value="' + responseMT[i].ID_Tabla_Dato + '" ' + selected + '>' + responseMT[i].No_Descripcion + '</option>');
        }
      }, 'JSON');

      //Formato PDF
      var arrFormatoPDF = [
        { "No_Formato_PDF": "A4" },
        { "No_Formato_PDF": "TICKET" },
      ];
      $('#cbo-formato_pdf').html('');
      for (var i = 0; i < arrFormatoPDF.length; i++) {
        selected = '';
        if (response.arrEdit[0].No_Formato_PDF == arrFormatoPDF[i]['No_Formato_PDF'])
          selected = 'selected="selected"';
        $('#cbo-formato_pdf').append('<option value="' + arrFormatoPDF[i]['No_Formato_PDF'] + '" ' + selected + '>' + arrFormatoPDF[i]['No_Formato_PDF'] + '</option>');
      }

      $('#radio-tipo_transporte_publico').prop('checked', true).iCheck('update');
      $('#radio-tipo_transporte_privado').prop('checked', false).iCheck('update');
      if (response.arrEdit[0].No_Tipo_Transporte=='02'){//privado
        $('#radio-tipo_transporte_publico').prop('checked', false).iCheck('update');
        $('#radio-tipo_transporte_privado').prop('checked', true).iCheck('update');
      }

      //Detalle
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
      var iTotalRegistrosImpuestos = response.arrImpuesto.length, iCantidadItem=0;
      
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

        iCantidadItem += parseFloat(response.arrEdit[i].Qt_Producto);

        table_detalle_producto += 
        "<tr id='tr_detalle_producto" + response.arrEdit[i].ID_Producto + "'>"
          +"<td style='display:none;' class='text-left td-iIdItem'>" + response.arrEdit[i].ID_Producto + "</td>"
          +"<td class='text-right'><input type='text' inputmode='decimal' class='pos-input txt-Qt_Producto form-control input-decimal input-size_cantidad' " + (response.arrEdit[i].Nu_Tipo_Producto == 1 ? 'onkeyup=validateStockNow(event);' : '') + " data-id_almacen='" + $('#cbo-almacen').val() + "' data-id_impuesto_icbper='" + response.arrEdit[i].ID_Impuesto_Icbper + "' data-id_item='" + response.arrEdit[i].ID_Producto + "' data-id_producto='" + response.arrEdit[i].ID_Producto + "' value='" + Math.round10(response.arrEdit[i].Qt_Producto, -3) + "' autocomplete='off'></td>"
          +"<td class='text-left'>"
            + '[' + response.arrEdit[i].Nu_Codigo_Barra + ']<br>'
            + '<span style="font-size: 13px;font-weight:bold;">' + response.arrEdit[i].No_Producto + '</span>'
            + sVarianteMultipleTmp
            + (response.arrEdit[i].No_Unidad_Medida !== undefined && response.arrEdit[i].No_Unidad_Medida !== null && response.arrEdit[i].No_Unidad_Medida != '' ? ' <br><span style="font-size: 10px;font-weight:normal;">[' + response.arrEdit[i].No_Unidad_Medida + ']</span> ' : '')
          +"</td>"
          +"<td style='display:none;' class='text-right'><input type='text' class='pos-input txt-fValorUnitario form-control input-decimal input-size_importe' value='" + parseFloat(response.arrEdit[i].Ss_Precio / response.arrEdit[i].Ss_Impuesto).toFixed(3) + "' autocomplete='off'></td>"
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
          +"<td class='text-right'><input type='text' class='pos-input txt-Fe_Lote_Vencimiento form-control date-picker-invoice' placeholder='Opcional' value='" + (response.arrEdit[i].Fe_Lote_Vencimiento != null ? ParseDateString(response.arrEdit[i].Fe_Lote_Vencimiento, 6, '-') : '') + "' autocomplete='off'></td>"
          +"<td style='display:none;' class='text-right td-fDescuentoSinImpuestosItem'>" + (response.arrEdit[i].Ss_Descuento_Producto == 0.00 ? '' : response.arrEdit[i].Ss_Descuento_Producto) + "</td>"
          +"<td style='display:none;' class='text-right td-fDescuentoImpuestosItem'>" + (response.arrEdit[i].Ss_Descuento_Impuesto_Producto == 0.00 ? '' : response.arrEdit[i].Ss_Descuento_Impuesto_Producto) + "</td>"
          +"<td class='text-center'><button type='button' id='btn-deleteProducto' class='btn btn-sm btn-link' alt='Eliminar' title='Eliminar'><i class='fa fa-trash-o fa-2x' aria-hidden='true'> </i></button></td>"
        +"</tr>";
      }
      
		  $( '#table-DetalleProductos > tbody' ).append(table_detalle_producto);
    
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
            
			$( '#txt-total' ).val( $Ss_Total.toFixed(2) );
			$( '#span-total' ).text( $Ss_Total.toFixed(2) );
		
      $('#span-total_importe').text($Ss_Total.toFixed(2));
      $('#span-total_cantidad').text( iCantidadItem);

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

function anularCompra(ID, Nu_Enlace, Nu_Descargar_Inventario, accion, sPrimerCaracterSerie){
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
      _anularCompra($modal_delete, ID, Nu_Enlace, Nu_Descargar_Inventario, sPrimerCaracterSerie);
      accion='';
    }
  });

  $( '#btn-save-delete' ).off('click').click(function () {
    _anularCompra($modal_delete, ID, Nu_Enlace, Nu_Descargar_Inventario, sPrimerCaracterSerie);
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

  $('#cbo-TiposDocumento').change(function () {
    //proceso más rápido para guía interna
    if ($('#header-a-id_empresa').val() != 2 && $('#header-a-id_empresa').val() != 175) {//2=RAICES GRANEL y DEMODE
      if(($('#txt-EID_Guia_Cabecera').val() == '' || $('#txt-EID_Guia_Cabecera').val() == null) && $('#cbo-TiposDocumento').val()==14 ){
        if($('#hidden-ID_Entidad-Registro').val() != '') {
          $( '#radio-cliente_existente' ).prop('checked', true).iCheck('update');
          $( '#radio-cliente_nuevo' ).prop('checked', false).iCheck('update');
          $('#txt-AID').val($('#hidden-ID_Entidad-Registro').val());
          $('#txt-ACodigo').val($('#hidden-Nu_Documento_Identidad-Registro').val());
          $('#txt-ANombre').val($('#hidden-No_Entidad-Registro').val());

          $('#cbo-tipo_movimiento').val('1');
        } else {
          $( '#radio-cliente_existente' ).prop('checked', false).iCheck('update');
          $( '#radio-cliente_nuevo' ).prop('checked', true).iCheck('update');

          $('#txt-AID').val('');
          $('#txt-ACodigo').val('');
          $('#txt-ANombre').val('');
        }

        //flete
        $('#div-addFlete').hide();
        $('#radio-flete_si').prop('checked', false).iCheck('update');
        $('#radio-flete_no').prop('checked', true).iCheck('update');
      } else if(($('#txt-EID_Guia_Cabecera').val() == '' || $('#txt-EID_Guia_Cabecera').val() == null) && $('#cbo-TiposDocumento').val()!=14 ){
        $( '#radio-cliente_existente' ).prop('checked', true).iCheck('update');
        $( '#radio-cliente_nuevo' ).prop('checked', false).iCheck('update');

        $('#txt-AID').val('');
        $('#txt-ACodigo').val('');
        $('#txt-ANombre').val('');
      }
    } else {
      //flete
      $('#div-addFlete').hide();
      $('#radio-flete_si').prop('checked', false).iCheck('update');
      $('#radio-flete_no').prop('checked', true).iCheck('update'); 
    }

    if ($(this).val() > 0) {
      considerar_igv = $(this).find(':selected').data('nu_impuesto');
      nu_enlace = $(this).find(':selected').data('nu_enlace');

      url = base_url + 'HelperController/getSeriesDocumentoxAlmacen';
      $.post(url, { ID_Organizacion: $('#header-a-id_organizacion').val(), ID_Almacen: $('#cbo-almacen').val(), ID_Tipo_Documento: $(this).val() }, function (response) {
        if (response.length === 1) {
          $('#cbo-SeriesDocumento').html('<option value="' + response[0].ID_Serie_Documento + '" data-id_serie_documento_pk=' + response[0].ID_Serie_Documento_PK + '>' + response[0].ID_Serie_Documento + '</option>');
          //Get número cuando solo haya una serie por un tipo de documento
          $('#txt-ID_Numero_Documento').val('');
          url = base_url + 'HelperController/getNumeroDocumento';
          $.post(url, { ID_Organizacion: $('#header-a-id_organizacion').val(), ID_Almacen: $('#cbo-almacen').val(), ID_Tipo_Documento: $('#cbo-TiposDocumento').val(), ID_Serie_Documento: response[0].ID_Serie_Documento }, function (responseNumeros) {
            if (responseNumeros.length === 0)
              $('#txt-ID_Numero_Documento').val('');
            else
              $('#txt-ID_Numero_Documento').val(responseNumeros.ID_Numero_Documento);
          }, 'JSON');
          //Fin número
          $('#cbo-SeriesDocumento').html('<option value="' + response[0].ID_Serie_Documento + '" data-id_serie_documento_pk=' + response[0].ID_Serie_Documento_PK + '>' + response[0].ID_Serie_Documento + '</option>');
        } else if (response.length > 1) {
          $('#cbo-SeriesDocumento').html('<option value="" selected="selected">- Seleccionar -</option>');
          for (var i = 0; i < response.length; i++)
            $('#cbo-SeriesDocumento').append('<option value="' + response[i].ID_Serie_Documento + '" data-id_serie_documento_pk=' + response[i].ID_Serie_Documento_PK + '>' + response[i].ID_Serie_Documento + '</option>');
        } else
          $('#cbo-SeriesDocumento').html('<option value="" selected="selected">Sin serie</option>');
      }, 'JSON');

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
      $("#table-DetalleProductos > tbody > tr").each(function () {
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

        if (isNaN($Ss_Descuento_Producto))
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

      if (isNaN($Ss_Descuento))
        $Ss_Descuento = 0.00;

      $('#txt-subTotal').val($Ss_SubTotal.toFixed(2));
      $('#span-subTotal').text($Ss_SubTotal.toFixed(2));

      $('#txt-inafecto').val($Ss_Inafecto.toFixed(2));
      $('#span-inafecto').text($Ss_Inafecto.toFixed(2));

      $('#txt-exonerada').val($Ss_Exonerada.toFixed(2));
      $('#span-exonerada').text($Ss_Exonerada.toFixed(2));

      $('#txt-gratuita').val($Ss_Gratuita.toFixed(2));
      $('#span-gratuita').text($Ss_Gratuita.toFixed(2));

      $('#txt-impuesto').val($Ss_IGV.toFixed(2));
      $('#span-impuesto').text($Ss_IGV.toFixed(2));

      $('#txt-descuento').val($Ss_Descuento.toFixed(2));
      $('#span-descuento').text($Ss_Descuento.toFixed(2));

      $('#txt-total').val($Ss_Total.toFixed(2));
      $('#span-total').text($Ss_Total.toFixed(2));
    }
  })

  $( '#radio-cliente_existente' ).on('ifChecked', function () {
    $( '.div-cliente_existente' ).show();
    $( '.div-cliente_nuevo' ).hide();
  })
  
  $( '#radio-cliente_nuevo' ).on('ifChecked', function () {
    $( '.div-cliente_existente' ).hide();
    $( '.div-cliente_nuevo' ).show();

    $('#txt-Txt_Email_Entidad_Cliente').val('');
    $('#txt-Nu_Celular_Entidad_Cliente').val('');
    $('#txt-Txt_Direccion_Entidad_Cliente').val('');
  })

  $(".div-flete").click(function () {
    $('#div-addFlete').show();

    $('#radio-flete_si').prop('checked', true).iCheck('update');
    $('#radio-flete_no').prop('checked', false).iCheck('update');
    if( $(this).data('estado') == 0 ) {
      $('#div-addFlete').hide();

      $('#radio-flete_si').prop('checked', false).iCheck('update');
      $('#radio-flete_no').prop('checked', true).iCheck('update');
    }
  });

  $('#radio-flete_si').on('ifChecked', function () {
    $('#div-addFlete').show();
  })

  $('#radio-flete_no').on('ifChecked', function () {
    $('#div-addFlete').hide();
  })

  //Global Autocomplete
  $('.autocompletar_transportista').autoComplete({
    minChars: 0,
    source: function (term, response) {
      var term = term.toLowerCase();
      var global_class_method = $('.autocompletar_transportista').data('global-class_method');
      var global_table = $('.autocompletar_transportista').data('global-table');
      $.post(base_url + global_class_method, { global_table: global_table, global_search: term }, function (arrData) {
        if (arrData.message === undefined )
          response(arrData);
        else {
          $('#txt-AID_Transportista').val('');
        }
      }, 'JSON');
    },
    renderItem: function (item, search) {
      search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
      var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");
      var data_direccion = '';
      if ($('#txt-Txt_Direccion_Entidad').val() != undefined)
        data_direccion = 'data-direccion_cliente="' + item.Txt_Direccion_Entidad + '"';
      var data_telefono = '';
      if ($('#txt-Nu_Telefono_Entidad_Cliente').val() != undefined)
        data_telefono = 'data-telefono="' + item.Nu_Telefono_Entidad + '"';
      var data_celular = '';
      if ($('#txt-Nu_Celular_Entidad_Cliente').val() != undefined)
        data_celular = 'data-celular="' + item.Nu_Celular_Entidad + '"';
      var data_email = '';
      if ($('#txt-Txt_Email_Entidad_Cliente').val() != undefined)
        data_email = 'data-email="' + item.Txt_Email_Entidad + '"';
      var data_dias_credito = '';
      if ($('#txt-Fe_Vencimiento').val() != undefined && ($('#cbo-MediosPago').val() != undefined && $('#cbo-MediosPago').find(':selected').data('nu_tipo') == 1))
        data_dias_credito = 'data-dias_credito="' + item.Nu_Dias_Credito + '"';
      return '<div class="autocomplete-suggestion" data-id="' + item.ID + '" data-codigo="' + item.Codigo + '" data-nombre="' + item.Nombre + '" data-id_tipo_documento_identidad="' + item.ID_Tipo_Documento_Identidad + '" data-estado="' + item.Nu_Estado + '" data-val="' + search + '" ' + data_direccion + ' ' + data_telefono + ' ' + data_celular + ' ' + data_email + ' ' + data_dias_credito + '>' + item.Nombre.replace(re, "<b>$1</b>") + '</div>';
    },
    onSelect: function (e, term, item) {
      $('#txt-AID_Transportista').val(item.data('id'));
      $('#txt-ACodigo_Transportista').val(item.data('codigo'));
      $('#txt-ANombre_Transportista').val(item.data('nombre'));
      $('#txt-transportista-ID_Tipo_Documento_Identidad').val(item.data('id_tipo_documento_identidad'));
      $('#txt-ACodigo_Transportista').closest('.form-group').find('.help-block').html('');
      $('#txt-ACodigo_Transportista').closest('.form-group').removeClass('has-error');
    }
  });

  $( '#radio-tipo_transporte_publico' ).on('ifChecked', function () {
    $( '#txt-No_Placa' ).attr('placeholder', 'Opcional');
    $( '#txt-No_Licencia' ).attr('placeholder', 'Opcional');
  })

  $( '#radio-tipo_transporte_privado' ).on('ifChecked', function () {
    $( '#txt-No_Placa' ).attr('placeholder', 'Opcional');
    $( '#txt-No_Licencia' ).attr('placeholder', 'Opcional');
    if($('#cbo-TiposDocumento').val() == 7 && $('#cbo-SeriesDocumento').find(':selected').text().substr(0, 1) == 'T' && $('[name="radio-TipoTransporte"]:checked').attr('value') == '02'){
      $( '#txt-No_Placa' ).attr('placeholder', 'Obligatorio');
      $( '#txt-No_Licencia' ).attr('placeholder', 'Obligatorio');
    }
  })

  //LAE API SUNAT / RENIEC - CLIENTE
  $( '#btn-cloud-api_compra_cliente' ).click(function(){
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
      $( '#btn-cloud-api_compra_cliente' ).text('');
      $( '#btn-cloud-api_compra_cliente' ).attr('disabled', true);
      $( '#btn-cloud-api_compra_cliente' ).append( '<i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
      
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
          $( '#btn-cloud-api_compra_cliente' ).closest('.form-group').find('.help-block').html('');
      	  $( '#btn-cloud-api_compra_cliente' ).closest('.form-group').removeClass('has-success').addClass('has-error');
      	  
          if (response.success == true){
            $( '#txt-No_Entidad_Cliente' ).val( response.data.No_Names );
            if ( $( '#cbo-TiposDocumentoIdentidadCliente' ).val() == 4) {//RUC
              $( '#txt-Txt_Direccion_Entidad_Cliente' ).val( response.data.Txt_Address );
              $( '#txt-Nu_Telefono_Entidad_Cliente' ).val( response.data.Nu_Phone );
              $('#txt-Nu_Celular_Entidad_Cliente').val(response.data.Nu_Cellphone);
              if (response.data.Nu_Status == 1)
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
  		  	
          $( '#btn-cloud-api_compra_cliente' ).text('');
          $( '#btn-cloud-api_compra_cliente' ).attr('disabled', false);
          $( '#btn-cloud-api_compra_cliente' ).append( '<i class="fa fa-cloud-download fa-lg"></i>' );
        },
        error: function(response){
          $( '#btn-cloud-api_compra_cliente' ).closest('.form-group').find('.help-block').html('Sin acceso');
      	  $( '#btn-cloud-api_compra_cliente' ).closest('.form-group').removeClass('has-success').addClass('has-error');
      	  
          $( '#txt-No_Entidad_Cliente' ).val( '' );
          $( '#txt-Txt_Direccion_Entidad_Cliente' ).val( '' );
          $( '#txt-Nu_Telefono_Entidad_Cliente' ).val( '' );
          $( '#txt-Nu_Celular_Entidad_Cliente' ).val( '' );

          $( '#btn-cloud-api_compra_cliente' ).text('');
          $( '#btn-cloud-api_compra_cliente' ).attr('disabled', false);
          $( '#btn-cloud-api_compra_cliente' ).append( '<i class="fa fa-cloud-download fa-lg"></i>' );
        }
      });
    }
  })
  
	/* Tipo Documento Identidad Cliente */
	$( '#cbo-TiposDocumentoIdentidadCliente' ).change(function(){
	  if ( $(this).val() == 2 ) {//DNI
		  $( '#label-Nombre_Documento_Identidad_Cliente' ).text('DNI');
		  $( '#label-No_Entidad_Proveeedor' ).text('Nombre(s) y Apellidos');
			$( '#txt-Nu_Documento_Identidad_Cliente' ).attr('maxlength', $(this).find(':selected').data('nu_cantidad_caracteres'));
	  } else if ( $(this).val() == 4 ) {//RUC
		  $( '#label-Nombre_Documento_Identidad_Cliente' ).text('RUC');
		  $( '#label-No_Entidad_Proveeedor' ).text('Razón Social');
			$( '#txt-Nu_Documento_Identidad_Cliente' ).attr('maxlength', $(this).find(':selected').data('nu_cantidad_caracteres'));
	  } else {
	    $( '#label-Nombre_Documento_Identidad_Cliente' ).text('Nro. Documento');
		  $( '#label-No_Entidad_Proveeedor' ).text('Nombre(s) y Apellidos');
			$( '#txt-Nu_Documento_Identidad_Cliente' ).attr('maxlength', $(this).find(':selected').data('nu_cantidad_caracteres'));
	  }
	})
  
  url = base_url + 'HelperController/getTiposDocumentos';
  $.post( url, {Nu_Tipo_Filtro : 7}, function( response ){//2 = Compra
    $( '#cbo-Filtro_TiposDocumento' ).html('<option value="0" selected="selected">Todos</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-Filtro_TiposDocumento' ).append( '<option value="' + response[i]['ID_Tipo_Documento'] + '">' + response[i]['No_Tipo_Documento_Breve'] + '</option>' );
  }, 'JSON');

  $('#cbo-Filtro_SeriesDocumento').html('<option value="0" selected="selected">Todos</option>');
  $('#cbo-Filtro_TiposDocumento').change(function () {
    $('#cbo-Filtro_SeriesDocumento').html('<option value="0" selected="selected">Todos</option>');
    if ($(this).val() > 0) {
      url = base_url + 'HelperController/getSeriesDocumento';
      $.post(url, { ID_Organizacion: $('#cbo-filtro-organizacion').val(), ID_Tipo_Documento: $(this).val() }, function (response) {
        $('#cbo-Filtro_SeriesDocumento').html('<option value="0" selected="selected">- Todos -</option>');
        if (response.length > 0) {
          for (var i = 0; i < response.length; i++)
            $('#cbo-Filtro_SeriesDocumento').append('<option value="' + response[i].ID_Serie_Documento + '">' + response[i].ID_Serie_Documento + '</option>');
        }
      }, 'JSON');
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
  url = base_url + 'Logistica/SalidaInventarioController/ajax_list';
  table_compra = $( '#table-Compra' ).DataTable({
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
        data.filtro_almacen = $('#cbo-filtro_almacen').val(),
        data.Filtro_Fe_Inicio       = ParseDateString($( '#txt-Filtro_Fe_Inicio' ).val(), 1, '/'),
        data.Filtro_Fe_Fin          = ParseDateString($( '#txt-Filtro_Fe_Fin' ).val(), 1, '/'),
        data.Filtro_TiposDocumento  = $( '#cbo-Filtro_TiposDocumento' ).val(),
        data.Filtro_SerieDocumento  = $( '#cbo-Filtro_SeriesDocumento' ).val(),
        data.Filtro_NumeroDocumento = $( '#txt-Filtro_NumeroDocumento' ).val(),
        data.Filtro_Estado = $('#cbo-Filtro_Estado').val(),
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
			Fe_Emision:{
				required: true,
      },
      ID_Tipo_Movimiento: {
        required: true,
      },
      ANombre: {
        required: true,
      },
      No_Entidad_Cliente: {
        required: true,
      },
      ANombre_Transportista: {
        required: true,
      },
      No_Placa: {
        required: true,
      },
      ID_Motivo_Traslado: {
        required: true,
      },
      Fe_Traslado: {
        required: true,
      },
			No_Licencia: {
				minlength: 9,
				maxlength: 10
			},
		},
    messages: {
      ID_Tipo_Documento: {
        required: "Seleccionar tipo",
      },
			ID_Serie_Documento:{
				required: "Ingresar serie",
			},
			ID_Numero_Documento:{
				required: "Ingresar número",
				minlength: "Debe ingresar 4 dígitos",
				maxlength: "Debe ingresar 4 dígitos",
			},
			Fe_Emision:{
				required: "Ingresar F. Emisión",
      },
      ID_Tipo_Movimiento: {
        required: "Seleccionar movimiento",
      },
      ANombre: {
        required: "Ingresar cliente",
      },
      No_Entidad_Cliente: {
        required: "Ingresar cliente",
      },
      ANombre_Transportista: {
        required: "Ingresar transportista",
      },
      No_Placa: {
        required: "Ingresar Placa",
      },
      ID_Motivo_Traslado: {
        required: "Seleccionar motivo de traslado",
      },
      Fe_Traslado: {
        required: "Ingresar F. Traslado",
      },
			No_Licencia:{
				minlength: "Mínimo 9 dígitos",
				maxlength: "Máximo 10 dígitos"
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

	$( '#cbo-SeriesDocumento' ).change(function(){
	  $( '#txt-ID_Numero_Documento' ).val('');
	  if ( $(this).val() != '') {
		  url = base_url + 'HelperController/getNumeroDocumento';
      $.post( url, { ID_Organizacion : $( '#header-a-id_organizacion' ).val(), ID_Tipo_Documento: $( '#cbo-TiposDocumento' ).val(), ID_Serie_Documento: $(this).val() }, function( response ){
        if (response.length == 0)
          $( '#txt-ID_Numero_Documento' ).val('');
        else
          $( '#txt-ID_Numero_Documento' ).val(response.ID_Numero_Documento);
      }, 'JSON');
    }

    $('#div-addFlete').hide();
    $('.div-electronico').hide();//UBIGEO

    $('#radio-flete_si').prop('checked', false).iCheck('update');
    $('#radio-flete_no').prop('checked', true).iCheck('update');
    if($('#cbo-TiposDocumento').val() == 7 && $('#cbo-SeriesDocumento').find(':selected').text().substr(0, 1) == 'T'){
      $('#div-addFlete').show();

      $('#radio-flete_si').prop('checked', true).iCheck('update');
      $('#radio-flete_no').prop('checked', false).iCheck('update');
      
      $('.div-electronico').show();
    }
    
    //validar campos obligatorios para guia electronica
    $( '#txt-No_Placa' ).attr('placeholder', 'Opcional');
    $( '#txt-No_Licencia' ).attr('placeholder', 'Opcional');
    if($('#cbo-TiposDocumento').val() == 7 && $('#cbo-SeriesDocumento').find(':selected').text().substr(0, 1) == 'T' && $('[name="radio-TipoTransporte"]:checked').attr('value') == '02'){
      $( '#txt-No_Placa' ).attr('placeholder', 'Obligatorio');
      $( '#txt-No_Licencia' ).attr('placeholder', 'Obligatorio');
    }
	})

	$( '#cbo-Monedas' ).change(function(){
	  if ( $(this).val() > 0 )
	    $( '.span-signo' ).text( $(this).find(':selected').data('no_signo') );
	})
		
  $( '#cbo-almacen' ).change(function(){
    if ( $(this).val() > 0 ) {
      var arrParams = {
        ID_Almacen : 0,
      };
      getListaPrecios(arrParams);
    }
  })

  $('#cbo-almacen_externo').html('');
  $('#cbo-tipo_movimiento').change(function () {
    if ($('#cbo-almacen').val() > 0 ) {
      if ($(this).val() == 15) {
        $('#cbo-almacen_externo').html('<option value="0">- Sin datos -</option>');

        url = base_url + 'HelperController/getOrganizacionesAlcenesEmpresaExternos';
        $.post(url, { 'iIdAlmacen': $( '#cbo-almacen' ).val() }, function (response) {
          if (response.sStatus == 'success') {
            response = response.arrData;
            if ( response.length == 1 ) {
              $('#cbo-almacen_externo').html('<option value="' + response[0]['ID_Almacen'] + '">' + response[0]['No_Almacen'] + '</option>');
              $('#txt-Txt_Direccion_Entidad').val(response[0]['Txt_Direccion_Almacen']);
            } else {
              $('#cbo-almacen_externo').html('<option value="0">- Seleccionar -</option>');
              
              for (var i = 0; i < response.length; i++)
                $('#cbo-almacen_externo').append('<option value="' + response[i]['ID_Almacen'] + '" data-direccion="' + response[i]['Txt_Direccion_Almacen'] + '">' + response[i]['No_Almacen'] + '</option>');
            }
          }
        }, 'JSON');
      } else {
        $('#cbo-almacen_externo').html('<option value="0">- Sin datos -</option>');
        //$('#txt-Txt_Direccion_Entidad').val('');
      }
    } else {
      $('#cbo-almacen').closest('.form-group').find('.help-block').html('Seleccionar almacén');
      $('#cbo-almacen').closest('.form-group').removeClass('has-success').addClass('has-error');
      scrollToError($("html, body"), $('#cbo-almacen'));
    }
  })

  $('#cbo-almacen_externo').change(function () {
    $('#txt-Txt_Direccion_Entidad').val($( '#cbo-almacen_externo' ).find(':selected').data('direccion'));
  })

  $( '#div-addFlete').hide();
  
  var _ID_Producto = '';
  var option_impuesto_producto = '';
  $( '#btn-addProductoCompra' ).click(function(){
    var $ID_Producto                  = $( '#txt-ID_Producto' ).val();
    var $Ss_Precio = parseFloat($('#txt-Ss_Precio').val());
    var $Nu_Codigo_Barra = $('#txt-Nu_Codigo_Barra').val();
    var $No_Producto                  = $( '#txt-No_Producto' ).val();
    var $ID_Impuesto_Cruce_Documento  = $( '#txt-ID_Impuesto_Cruce_Documento' ).val();
    var $Nu_Tipo_Impuesto             = $( '#txt-Nu_Tipo_Impuesto' ).val();
    var $Ss_Impuesto                  = $( '#txt-Ss_Impuesto' ).val();
    var $Ss_SubTotal_Producto         = 0.00;
    var $Ss_Total_Producto            = 0.00;
    var $Qt_Producto = $( '#txt-Qt_Producto' ).val();
    var $iTipoItem = $('#txt-nu_tipo_item').val();
    var $No_Codigo_Interno = $('#txt-No_Codigo_Interno').val();
    var $ID_Impuesto_Icbper = $('#txt-ID_Impuesto_Icbper').val();
    var $No_Unidad_Medida = $('#txt-No_Unidad_Medida').val();
    var $no_variante_1 = $('#txt-no_variante_1').val();
    var $no_valor_variante_1 = $('#txt-no_valor_variante_1').val();
    var $no_variante_2 = $('#txt-no_variante_2').val();
    var $no_valor_variante_2 = $('#txt-no_valor_variante_2').val();
    var $no_variante_3 = $('#txt-no_variante_3').val();
    var $no_valor_variante_3 = $('#txt-no_valor_variante_3').val();
    
    var arrDataAdicionalTmpDetalleItem = {
      'No_Unidad_Medida' : $No_Unidad_Medida,
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
      if (iValidarStockGlobal == 1 && $iTipoItem == 1 && (parseFloat($Qt_Producto) <= 0.000000 || isNaN($Qt_Producto)==true) ) {
        $modal_msg_stock = $( '.modal-message' );
        $modal_msg_stock.modal('show');
    
        $modal_msg_stock.removeClass('modal-danger modal-warning modal-success');
        $modal_msg_stock.addClass('modal-warning');
    
        $( '.modal-title-message' ).text('Sin stock disponible');
    
        setTimeout(function() {$modal_msg_stock.modal('hide');}, 1300);
      } else {
        if ($('[name="addCliente"]:checked').attr('value') == 1){//Agregar cliente
          if ( $( '#cbo-Estado' ).val() == 1 ) {//1 = Activo
            generarTablaTemporalItems($ID_Producto, $No_Producto, $Ss_Precio, $ID_Impuesto_Cruce_Documento, $Nu_Tipo_Impuesto, $Ss_Impuesto, $iTipoItem, $Nu_Codigo_Barra, $No_Codigo_Interno, $ID_Impuesto_Icbper, arrDataAdicionalTmpDetalleItem);
          } else {
            $('#modal-message').modal('show');
            $('.modal-message').removeClass('modal-danger modal-warning modal-success');
            $( '.modal-message' ).addClass('modal-danger');
            $( '.modal-title-message' ).text( 'El cliente se encuentra con BAJA DE OFICIO / NO HABIDO' );
            setTimeout(function() {$('#modal-message').modal('hide');}, 2500);
          }
        } else {
          var arrPOST = {
            sTipoData : 'get_entidad',
            iIDEntidad : $( '#txt-AID' ).val(),
            iTipoEntidad : 0,
          };
          url = base_url + 'HelperController/getDataGeneral';
          $.post(url, arrPOST, function(response){
            $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
            if ( response.sStatus == 'success' ) {// Si el RUC es válido
              if ( response.arrData[0].Nu_Estado == '1' ){
                generarTablaTemporalItems($ID_Producto, $No_Producto, $Ss_Precio, $ID_Impuesto_Cruce_Documento, $Nu_Tipo_Impuesto, $Ss_Impuesto, $iTipoItem, $Nu_Codigo_Barra, $No_Codigo_Interno, $ID_Impuesto_Icbper, arrDataAdicionalTmpDetalleItem);
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
              $( '.modal-title-message' ).text(response.sMessage);
              setTimeout(function() {$('#modal-message').modal('hide');}, 2500);
            }
          }, 'json');// Obtener informacion de una entidad, para saber si esta HABIDO y SIN BAJA DE OFICIO
        }// /. Verificar si es un cliente existente o nuevo
      }
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

    //if ( parseFloat(precio) > 0.00 && parseFloat(cantidad) > 0){
    if ( parseFloat(cantidad) > 0){
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
        var $Ss_Total = 0.00, iCantidadItem=0;
        $("#table-DetalleProductos > tbody > tr").each(function(){
          var rows = $(this);
          iCantidadItem += parseFloat(rows.find(".txt-Qt_Producto").val());
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

        $('#span-total_importe').text($Ss_Total.toFixed(2));
        $('#span-total_cantidad').text(iCantidadItem);
      } else if (nu_tipo_impuesto == 2) {//Inafecto
        fila.find(".td-fDescuentoSinImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_Total_Producto").val((parseFloat(((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)) * impuesto_producto).toFixed(2)).toString().split(". "));
        
        var $Ss_Inafecto = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_Total = 0.00, iCantidadItem=0;
        $("#table-DetalleProductos > tbody > tr").each(function(){
          var rows = $(this);
          iCantidadItem += parseFloat(rows.find(".txt-Qt_Producto").val());
          var Ss_Impuesto           = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
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

        $('#span-total_importe').text($Ss_Total.toFixed(2));
        $('#span-total_cantidad').text(iCantidadItem);
      } else if (nu_tipo_impuesto == 3) {//Exonerada
        fila.find(".td-fDescuentoSinImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_Total_Producto").val((parseFloat(((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)) * impuesto_producto).toFixed(2)).toString().split(". "));
        
        var $Ss_Exonerada = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_Total = 0.00, iCantidadItem=0;
        $("#table-DetalleProductos > tbody > tr").each(function(){
          var rows = $(this);
          iCantidadItem += parseFloat(rows.find(".txt-Qt_Producto").val());
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

        $('#span-total_importe').text($Ss_Total.toFixed(2));
        $('#span-total_cantidad').text(iCantidadItem);
      } else if (nu_tipo_impuesto == 4) {//Gratuita
        fila.find(".td-fDescuentoSinImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_Total_Producto").val((parseFloat(((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)) * impuesto_producto).toFixed(2)).toString().split(". "));
        
        var $Ss_Gratuita = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_Total = 0.00, iCantidadItem=0;
        $("#table-DetalleProductos > tbody > tr").each(function(){
          var rows = $(this);
          iCantidadItem += parseFloat(rows.find(".txt-Qt_Producto").val());
          var Ss_Impuesto           = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
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

        $('#span-total_importe').text($Ss_Total.toFixed(2));
        $('#span-total_cantidad').text(iCantidadItem);
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
    
    //if ( parseFloat(precio) > 0.00 && parseFloat(cantidad) > 0){
    if ( parseFloat(cantidad) > 0){
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
        var $Ss_Total = 0.00, iCantidadItem=0;
        $("#table-DetalleProductos > tbody > tr").each(function(){
          var rows = $(this);
          iCantidadItem += parseFloat(rows.find(".txt-Qt_Producto").val());
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

        $('#span-total_importe').text($Ss_Total.toFixed(2));
        $('#span-total_cantidad').text(iCantidadItem);
      } else if (nu_tipo_impuesto == 2) {//Inafecto
        fila.find(".td-fDescuentoSinImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_Total_Producto").val((parseFloat(((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)) * impuesto_producto).toFixed(2)).toString().split(". "));

        var $Ss_Inafecto = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_Total = 0.00, iCantidadItem=0;
        $("#table-DetalleProductos > tbody > tr").each(function(){
          var rows = $(this);
          iCantidadItem += parseFloat(rows.find(".txt-Qt_Producto").val());
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

        $('#span-total_importe').text($Ss_Total.toFixed(2));
        $('#span-total_cantidad').text(iCantidadItem);
      } else if (nu_tipo_impuesto == 3) {//Exonerada
        fila.find(".td-fDescuentoSinImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_Total_Producto").val((parseFloat(((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)) * impuesto_producto).toFixed(2)).toString().split(". "));
        
        var $Ss_Exonerada = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_Total = 0.00, iCantidadItem=0;
        $("#table-DetalleProductos > tbody > tr").each(function(){
          var rows = $(this);
          iCantidadItem += parseFloat(rows.find(".txt-Qt_Producto").val());
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

        $('#span-total_importe').text($Ss_Total.toFixed(2));
        $('#span-total_cantidad').text(iCantidadItem);
      } else if (nu_tipo_impuesto == 4) {//Gratuita
        fila.find(".td-fDescuentoSinImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
        fila.find(".txt-Ss_Total_Producto").val((parseFloat(((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)) * impuesto_producto).toFixed(2)).toString().split(". "));
        
        var $Ss_Gratuita = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_Total = 0.00, iCantidadItem=0;
        $("#table-DetalleProductos > tbody > tr").each(function(){
          var rows = $(this);
          iCantidadItem += parseFloat(rows.find(".txt-Qt_Producto").val());
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

        $('#span-total_importe').text($Ss_Total.toFixed(2));
        $('#span-total_cantidad').text(iCantidadItem);
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
          var $Ss_Total = 0.00, iCantidadItem=0;
          $("#table-DetalleProductos > tbody > tr").each(function(){
            var rows = $(this);
            iCantidadItem += parseFloat(rows.find(".txt-Qt_Producto").val());
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

          $('#span-total_importe').text($Ss_Total.toFixed(2));
          $('#span-total_cantidad').text(iCantidadItem);
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
          var $Ss_Total = 0.00, iCantidadItem=0;
          $("#table-DetalleProductos > tbody > tr").each(function(){
            var rows = $(this);
            iCantidadItem += parseFloat(rows.find(".txt-Qt_Producto").val());
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

          $('#span-total_importe').text($Ss_Total.toFixed(2));
          $('#span-total_cantidad').text(iCantidadItem);
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
          var $Ss_Total = 0.00, iCantidadItem=0;
          $("#table-DetalleProductos > tbody > tr").each(function(){
            var rows = $(this);
            iCantidadItem += parseFloat(rows.find(".txt-Qt_Producto").val());
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

          $('#span-total_importe').text($Ss_Total.toFixed(2));
          $('#span-total_cantidad').text(iCantidadItem);
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
          var $Ss_Total = 0.00, iCantidadItem=0;
          $("#table-DetalleProductos > tbody > tr").each(function(){
            var rows = $(this);
            iCantidadItem += parseFloat(rows.find(".txt-Qt_Producto").val());
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

          $('#span-total_importe').text($Ss_Total.toFixed(2));
          $('#span-total_cantidad').text(iCantidadItem);
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
          var $Ss_Total = 0.00, iCantidadItem=0;
          $("#table-DetalleProductos > tbody > tr").each(function(){
            var rows = $(this);
            iCantidadItem += parseFloat(rows.find(".txt-Qt_Producto").val());
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

          $('#span-total_importe').text($Ss_Total.toFixed(2));
          $('#span-total_cantidad').text(iCantidadItem);
        } else if (nu_tipo_impuesto == 2) {//Inafecto
          fila.find(".td-fDescuentoSinImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
          fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
          fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
          fila.find(".txt-Ss_Total_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));

          var $Ss_Inafecto = 0.00;
          var $Ss_Descuento = 0.00;
          var $Ss_Total = 0.00, iCantidadItem=0;
          $("#table-DetalleProductos > tbody > tr").each(function(){
            var rows = $(this);
            iCantidadItem += parseFloat(rows.find(".txt-Qt_Producto").val());
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

          $('#span-total_importe').text($Ss_Total.toFixed(2));
          $('#span-total_cantidad').text(iCantidadItem);
        } else if (nu_tipo_impuesto == 3) {//Exonerada
          fila.find(".td-fDescuentoSinImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
          fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
          fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
          fila.find(".txt-Ss_Total_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));

          var $Ss_Exonerada = 0.00;
          var $Ss_Descuento = 0.00;
          var $Ss_Total = 0.00, iCantidadItem=0;
          $("#table-DetalleProductos > tbody > tr").each(function(){
            var rows = $(this);
            iCantidadItem += parseFloat(rows.find(".txt-Qt_Producto").val());
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

          $('#span-total_importe').text($Ss_Total.toFixed(2));
          $('#span-total_cantidad').text(iCantidadItem);
        } else if (nu_tipo_impuesto == 4) {//Gratuita
          fila.find(".td-fDescuentoSinImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
          fila.find(".td-fDescuentoImpuestosItem").text((parseFloat(((descuento * (precio * cantidad)) / 100) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
          fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));
          fila.find(".txt-Ss_Total_Producto").val((parseFloat((precio * cantidad) - ((descuento * (precio * cantidad)) / 100)).toFixed(2)).toString().split(". "));

          var $Ss_Gratuita = 0.00;
          var $Ss_Descuento = 0.00;
          var $Ss_Total = 0.00, iCantidadItem=0;
          $("#table-DetalleProductos > tbody > tr").each(function(){
            var rows = $(this);
            iCantidadItem += parseFloat(rows.find(".txt-Qt_Producto").val());
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

          $('#span-total_importe').text($Ss_Total.toFixed(2));
          $('#span-total_cantidad').text(iCantidadItem);
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
        var $Ss_Total = 0.00, iCantidadItem=0;
        $("#table-DetalleProductos > tbody > tr").each(function(){
          var rows = $(this);
          iCantidadItem += parseFloat(rows.find(".txt-Qt_Producto").val());
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

        $('#span-total_importe').text($Ss_Total.toFixed(2));
        $('#span-total_cantidad').text(iCantidadItem);
      } else if (nu_tipo_impuesto == 2) {//Inafecto
        fila.find(".txt-Ss_Precio").val((parseFloat((total_producto / cantidad) / impuesto_producto).toFixed(6)).toString().split(". "));
        fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat(total_producto / impuesto_producto).toFixed(2)).toString().split(". "));
        fila.find(".txt-fValorUnitario").val(parseFloat(fila.find(".txt-Ss_Precio").val()).toFixed(6));
        
        var $Ss_Inafecto = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_Total = 0.00, iCantidadItem=0;
        $("#table-DetalleProductos > tbody > tr").each(function(){
          var rows = $(this);
          iCantidadItem += parseFloat(rows.find(".txt-Qt_Producto").val());
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

        $('#span-total_importe').text($Ss_Total.toFixed(2));
        $('#span-total_cantidad').text(iCantidadItem);
      } else if (nu_tipo_impuesto == 3) {//Exonerada
        fila.find(".txt-Ss_Precio").val((parseFloat((total_producto / cantidad) / impuesto_producto).toFixed(6)).toString().split(". "));
        fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat(total_producto / impuesto_producto).toFixed(2)).toString().split(". "));
        fila.find(".txt-fValorUnitario").val(parseFloat(fila.find(".txt-Ss_Precio").val()).toFixed(6));
        
        var $Ss_Exonerada = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_Total = 0.00, iCantidadItem=0;
        $("#table-DetalleProductos > tbody > tr").each(function(){
          var rows = $(this);
          iCantidadItem += parseFloat(rows.find(".txt-Qt_Producto").val());
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

        $('#span-total_importe').text($Ss_Total.toFixed(2));
        $('#span-total_cantidad').text(iCantidadItem);
      } else if (nu_tipo_impuesto == 4) {//Gratuita
        fila.find(".txt-Ss_Precio").val((parseFloat((total_producto / cantidad) / impuesto_producto).toFixed(6)).toString().split(". "));
        fila.find(".txt-Ss_SubTotal_Producto").val((parseFloat(total_producto / impuesto_producto).toFixed(2)).toString().split(". "));
        fila.find(".txt-fValorUnitario").val(parseFloat(fila.find(".txt-Ss_Precio").val()).toFixed(6));
        
        var $Ss_Gratuita = 0.00;
        var $Ss_Descuento = 0.00;
        var $Ss_Total = 0.00, iCantidadItem=0;
        $("#table-DetalleProductos > tbody > tr").each(function(){
          var rows = $(this);
          iCantidadItem += parseFloat(rows.find(".txt-Qt_Producto").val());
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

        $('#span-total_importe').text($Ss_Total.toFixed(2));
        $('#span-total_cantidad').text(iCantidadItem);
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
    var $Ss_Descuento_p = 0, iCantidadItem=0;
    $("#table-DetalleProductos > tbody > tr").each(function(){
      var rows = $(this);
      iCantidadItem += parseFloat(rows.find(".txt-Qt_Producto").val());
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

    $('#span-total_importe').text($Ss_Total.toFixed(2));
    $('#span-total_cantidad').text(iCantidadItem);

    if ($( '#table-DetalleProductos >tbody >tr' ).length == 0)
	      $( '#table-DetalleProductos' ).hide();
	})
	
  $('#table-CompraTotal' ).on('input', '#txt-Ss_Descuento', function(){
    var $Ss_Descuento_Producto = 0.00, iCantidadItem=0;
    $("#table-DetalleProductos > tbody > tr").each(function(){
      var rows = $(this);
      iCantidadItem += parseFloat(rows.find(".txt-Qt_Producto").val());
      var $Ss_Descuento_Producto = parseFloat(rows.find('.txt-Ss_Descuento', this).val());
      
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
      var globalImpuesto = 0, iCantidadItem=0;
      $("#table-DetalleProductos > tbody > tr").each(function(){
        var rows = $(this);
        iCantidadItem += parseFloat(rows.find(".txt-Qt_Producto").val());
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

      $('#span-total_importe').text($Ss_Total.toFixed(2));
      $('#span-total_cantidad').text(iCantidadItem);
    }
  })
})

function isExistTableTemporalProducto($ID_Producto){
  return Array.from($('tr[id*=tr_detalle_producto]'))
    .some(element => ($('td:nth(0)',$(element)).html()===$ID_Producto));
}

function form_Compra(){
  if (accion == 'add_factura_compra' || accion == 'upd_factura_compra') {//Accion para validar tecla ENTER
    var $counterNoIcbper = 0, $counterIcbper = 0, arrDetalleCompra = [];
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
      
      if (parseFloat($Qt_Producto) == 0){
        arrValidarNumerosEnCero[$counterNumerosEnCero] = $ID_Producto;
        $('#tr_detalle_producto' + $ID_Producto).addClass('danger');
      }

      if (rows.find(".txt-Qt_Producto").data('id_impuesto_icbper') == 1) {
        $counterIcbper++;
      }

      if (rows.find(".txt-Qt_Producto").data('id_impuesto_icbper') == 0) {
        $counterNoIcbper++;
      }
      
      var obj = {};
      
      obj.ID_Producto = $ID_Producto;
      obj.fValorUnitario = $fValorUnitario;
      obj.Ss_Precio	= $Ss_Precio;
      obj.Qt_Producto = $Qt_Producto;
      obj.ID_Impuesto_Cruce_Documento	= $ID_Impuesto_Cruce_Documento;
      obj.Ss_SubTotal = $Ss_SubTotal;
      obj.Ss_Descuento = $Ss_Descuento;
      obj.Ss_Impuesto	= $Ss_Total - $Ss_SubTotal;
      obj.Ss_Total = $Ss_Total;
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
    } else if (bEstadoValidacion) {
  		$( '#panel-DetalleProductos' ).removeClass('panel-danger');
      $( '#panel-DetalleProductos' ).addClass('panel-default');
      
  		var arrCompraCabecera = Array();
  		arrCompraCabecera = {
        'EID_Guia_Cabecera': $('#txt-EID_Guia_Cabecera').val(),
        'ID_Almacen': $('#cbo-almacen').val(),
        'ID_Tipo_Documento': $( '#cbo-TiposDocumento' ).val(),
        'ID_Serie_Documento': $('#cbo-SeriesDocumento').val(),
        'ID_Serie_Documento_PK': $('#cbo-SeriesDocumento').find(':selected').data('id_serie_documento_pk'),
  		  'ID_Numero_Documento' : $( '#txt-ID_Numero_Documento' ).val(),
  		  'Fe_Emision' : $( '#txt-Fe_Emision' ).val(),
        'ID_Moneda': $('#cbo-Monedas').val(),
        'Nu_Descargar_Inventario': $('#cbo-descargar_stock').val(),
        'ID_Tipo_Movimiento': $('#cbo-tipo_movimiento').val(),
        'ID_Entidad': $('#txt-AID').val(),
        'Txt_Email_Entidad': $('#txt-Txt_Email_Entidad').val(),
        'Nu_Celular_Entidad': $('#txt-Nu_Celular_Entidad').val(),
        'Txt_Direccion_Entidad': $('#txt-Txt_Direccion_Entidad').val(),
        'iFlete': $('[name="radio-addFlete"]:checked').attr('value'),//Flete
        'ID_Entidad_Transportista': $('#txt-AID_Transportista').val(),
        'No_Placa': $('[name="No_Placa"]').val(),
        'Fe_Traslado': $('[name="Fe_Traslado"]').val(),
        'ID_Motivo_Traslado': $('#cbo-motivo_traslado').val(),
        'Ss_Peso_Bruto': $('[name="Ss_Peso_Bruto"]').val(),
        'Nu_Bulto': $('[name="Nu_Bulto"]').val(),
        'No_Licencia': $('[name="No_Licencia"]').val(),
        'No_Certificado_Inscripcion': $('[name="No_Certificado_Inscripcion"]').val(),
        'ID_Lista_Precio_Cabecera': $('#cbo-lista_precios').val(),
  		  'Txt_Glosa' : $( '[name="Txt_Glosa"]' ).val(),
  		  'Po_Descuento' : $( '#txt-Ss_Descuento' ).val(),
  		  'Ss_Descuento' : $( '#txt-descuento' ).val(),
        'Ss_Total': $('#txt-total').val(),
        'ID_Almacen_Transferencia': $('#cbo-almacen_externo').val(),
        'iTipoCliente': $('[name="addCliente"]:checked').attr('value'),
        'No_Formato_PDF': $('#cbo-formato_pdf').val(),
        'No_Tipo_Transporte': $('[name="radio-TipoTransporte"]:checked').attr('value'),
        'ID_Ubigeo_Inei_Llegada': $('#cbo-ubigeo_inei').val()//UBIGEO
  		};

      var arrClienteNuevo = {};
  		if ($('[name="addCliente"]:checked').attr('value') == 1){//Agregar cliente
    		arrClienteNuevo = {
    		  'ID_Tipo_Documento_Identidad' : $( '#cbo-TiposDocumentoIdentidadCliente' ).val(),
    		  'Nu_Documento_Identidad' : $( '#txt-Nu_Documento_Identidad_Cliente' ).val(),
    		  'No_Entidad' : $( '#txt-No_Entidad_Cliente' ).val(),
    		  'Txt_Direccion_Entidad' : $( '#txt-Txt_Direccion_Entidad_Cliente' ).val(),
    		  'Nu_Telefono_Entidad' : $( '#txt-Nu_Telefono_Entidad_Cliente' ).val(),
    		  'Nu_Celular_Entidad' : $( '#txt-Nu_Celular_Entidad_Cliente' ).val(),
          'Txt_Email_Entidad': $('#txt-Txt_Email_Entidad_Cliente').val(),
          'Nu_Estado' : $( '#cbo-Estado' ).val(),
        };
  		}
  		
      $( '#btn-save' ).text('');
      $( '#btn-save' ).attr('disabled', true);
      $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
    
      $( '#modal-loader' ).modal('show');

      $('#txt-AID_Doble').val('');
      $('#txt-Filtro_Entidad').val('');

      url = base_url + 'Logistica/SalidaInventarioController/crudCompra';
    	$.ajax({
        type		  : 'POST',
        dataType	: 'JSON',
    		url		    : url,
    		data		  : {
          arrCompraCabecera: arrCompraCabecera,
          arrClienteNuevo: arrClienteNuevo,
    		  arrDetalleCompra: arrDetalleCompra,
    		},
    		success : function( response ){
    		  $( '#modal-loader' ).modal('hide');
    		  
    	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
      	  $( '#modal-message' ).modal('show');
    		  
    		  if (response.sStatus == 'success') {
            accion = '';
    		    
            $( '.div-AgregarEditar' ).hide();
            $( '.div-Listar' ).show();
      	    $( '.modal-message' ).addClass( 'modal-' + response.sStatus);
      	    $( '.modal-title-message' ).text(response.sMessage);
      	    setTimeout(function() {$('#modal-message').modal('hide'); }, 1100);
      	    
    		    $( '#form-Compra' )[0].reset();
      	    reload_table_compra();
    		  } else {
            if (response.sStatus == 'warning2') {
              $( '.modal-message' ).addClass( 'modal-warning');
              $( '.modal-title-message' ).text(response.sMessage);
              setTimeout(function() {$('#modal-message').modal('hide');}, 4000);
            } else {
              $( '.div-AgregarEditar' ).hide();
              $( '.div-Listar' ).show();
              $( '.modal-message' ).addClass( 'modal-' + response.sStatus);
              $( '.modal-title-message' ).text(response.sMessage);
              setTimeout(function() {$('#modal-message').modal('hide');}, 4000);
            }
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

function _anularCompra($modal_delete, ID, Nu_Enlace, Nu_Descargar_Inventario, sPrimerCaracterSerie){
  $( '#modal-loader' ).modal('show');
    
  url = base_url + 'Logistica/SalidaInventarioController/anularCompra/' + ID + '/' + Nu_Enlace + '/' + Nu_Descargar_Inventario + '/' + sPrimerCaracterSerie;
  $.ajax({
    url       : url,
    type      : "GET",
    dataType  : "JSON",
    success: function( response ){
      $( '#modal-loader' ).modal('hide');
      
      $modal_delete.modal('hide');
	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
  	  $( '#modal-message' ).modal('show');
		  
		  if (response.sStatus == 'success'){
  	    $( '.modal-message' ).addClass('modal-' + response.sStatus);
  	    $( '.modal-title-message' ).text(response.sMessage);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
  	    reload_table_compra();
		  } else {
  	    $( '.modal-message' ).addClass('modal-' + response.sStatus);
  	    $( '.modal-title-message' ).text(response.sMessage);
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
    
  url = base_url + 'Logistica/SalidaInventarioController/eliminarCompra/' + ID + '/' + Nu_Enlace + '/' + Nu_Descargar_Inventario;
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

  $('.form-group').removeClass('has-error');
  $('.help-block').empty();
  
  if ( $( '#cbo-TiposDocumento' ).val() == 0 ){
    $( '#cbo-TiposDocumento' ).closest('.form-group').find('.help-block').html('Seleccionar documento');
    $( '#cbo-TiposDocumento' ).closest('.form-group').removeClass('has-success').addClass('has-error');
	  
	  bEstadoValidacion = false;
		scrollToError( $("html, body"), $( '#cbo-TiposDocumento' ) );
  } else if ($('[name="addCliente"]:checked').attr('value') == 0 && ($('#txt-AID').val().length === 0 || $('#txt-ANombre').val().length === 0)) {
    $('#txt-ANombre').closest('.form-group').find('.help-block').html('Seleccionar cliente');
    $('#txt-ANombre').closest('.form-group').removeClass('has-success').addClass('has-error');

    bEstadoValidacion = false;
    scrollToError($("html, body"), $('#txt-ANombre'));
  } else if ($('#cbo-tipo_movimiento').val() == 0) {
    $('#cbo-tipo_movimiento').closest('.form-group').find('.help-block').html('Seleccionar tipo de movimiento');
    $('#cbo-tipo_movimiento').closest('.form-group').removeClass('has-success').addClass('has-error');

    bEstadoValidacion = false;
    scrollToError($("html, body"), $('#cbo-tipo_movimiento'));
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
  } else if ( $('[name="addCliente"]:checked').attr('value') == 1 && $( '#cbo-Estado' ).val() == 0 ) {
    $('#modal-message').modal('show');
    $('.modal-message').removeClass('modal-danger modal-warning modal-success');
    $( '.modal-message' ).addClass('modal-danger');
    $( '.modal-title-message' ).text( 'El cliente se encuentra con BAJA DE OFICIO / NO HABIDO' );
    setTimeout(function() {$('#modal-message').modal('hide');}, 2500);
		
    bEstadoValidacion = false;
  } else if ($('#cbo-SeriesDocumento').val() == 0) {
    $('#cbo-SeriesDocumento').closest('.form-group').find('.help-block').html('Seleccionar');
    $('#cbo-SeriesDocumento').closest('.form-group').removeClass('has-success').addClass('has-error');

    bEstadoValidacion = false;
    scrollToError($("html, body"), $('#cbo-SeriesDocumento'));
  } else if ($('#cbo-SeriesDocumento').find(':selected').text().substr(0, 1) == 'T' && $('[name="radio-addFlete"]:checked').attr('value') == 0) {
    $('#cbo-SeriesDocumento').closest('.form-group').find('.help-block').html('Guía electrónica obligatorio Flete: Si');
    $('#cbo-SeriesDocumento').closest('.form-group').removeClass('has-success').addClass('has-error');

    bEstadoValidacion = false;
    scrollToError($("html, body"), $('#cbo-SeriesDocumento'));
  } else if ($('[name="radio-addFlete"]:checked').attr('value') == 1 && $('[name="Fe_Traslado"]').val().length===0 ) {
    $('[name="Fe_Traslado"]').closest('.form-group').find('.help-block').html('F. Entrega no debe estar vacio');
    $('[name="Fe_Traslado"]').closest('.form-group').removeClass('has-success').addClass('has-error');

    bEstadoValidacion = false;
    scrollToError($("html, body"), $('[name="Fe_Traslado"]'));
  } else if ($('[name="radio-addFlete"]:checked').attr('value') == 1 && $('[name="Fe_Traslado"]').val() < $('[name="Fe_Emision"]').val() ) {
    $('[name="Fe_Traslado"]').closest('.form-group').find('.help-block').html('F. Entrega debe de ser igual o mayor a la F. Emisión');
    $('[name="Fe_Traslado"]').closest('.form-group').removeClass('has-success').addClass('has-error');

    bEstadoValidacion = false;
    scrollToError($("html, body"), $('[name="Fe_Traslado"]'));
  } else if ($('#cbo-SeriesDocumento').find(':selected').text().substr(0, 1) == 'T' && $('#cbo-SeriesDocumento').find(':selected').text().length != 4) {
    $('#cbo-SeriesDocumento').closest('.form-group').find('.help-block').html('Serie debe tener 4 dígitos');
    $('#cbo-SeriesDocumento').closest('.form-group').removeClass('has-success').addClass('has-error');

    bEstadoValidacion = false;
    scrollToError($("html, body"), $('#cbo-SeriesDocumento'));
  } else if ($('#cbo-SeriesDocumento').find(':selected').text().substr(0, 1) == 'T' && ($('#txt-AID_Transportista').val().length === 0 || $('#txt-ANombre_Transportista').val().length === 0)) {
    $('#txt-ANombre_Transportista').closest('.form-group').find('.help-block').html('Seleccionar');
    $('#txt-ANombre_Transportista').closest('.form-group').removeClass('has-success').addClass('has-error');

    bEstadoValidacion = false;
    scrollToError($("html, body"), $('#txt-ANombre_Transportista'));
  } else if ($('#cbo-SeriesDocumento').find(':selected').text().substr(0, 1) == 'T' && $('#cbo-motivo_traslado').val() == 0) {
    $('#cbo-motivo_traslado').closest('.form-group').find('.help-block').html('Seleccionar Motivo Traslado');
    $('#cbo-motivo_traslado').closest('.form-group').removeClass('has-success').addClass('has-error');

    bEstadoValidacion = false;
    scrollToError($("html, body"), $('#txt-motivo_traslado'));
  } else if ($('[name="addCliente"]:checked').attr('value') == 1 && $('#cbo-TiposDocumento').val() == 7 && $('#cbo-SeriesDocumento').find(':selected').text().substring(0, 1) == 'T' && ($('#txt-No_Entidad_Cliente').val().length === 0 || $('#txt-Nu_Documento_Identidad_Cliente').val().length === 0)) {//1 = Nuevo
    $('#modal-message').modal('show');
    $('.modal-message').addClass('modal-danger');
    $('.modal-title-message').text('Cliente nuevo, falta registrar Nro. Documento identidad / Nombre');
    setTimeout(function () { $('#modal-message').modal('hide'); }, 2500);
    bEstadoValidacion = false;
  } else if ($('[name="addCliente"]:checked').attr('value') == 1 && $('#cbo-TiposDocumento').val() == 7 && $('#cbo-SeriesDocumento').find(':selected').text().substr(0, 1) == 'T' && $('#txt-No_Entidad_Cliente').val().trim().length < 3) {//1 = Nuevo
    $('#txt-No_Entidad_Cliente').closest('.form-group').find('.help-block').html('Debes tener mínimo 3 carácteres');
    $('#txt-No_Entidad_Cliente').closest('.form-group').removeClass('has-success').addClass('has-error');

    bEstadoValidacion = false;
    scrollToError($("html, body"), $('#txt-No_Entidad_Cliente'));
  } else if ($('[name="addCliente"]:checked').attr('value') == 1 && $('#cbo-TiposDocumento').val() == 7 && $('#cbo-SeriesDocumento').find(':selected').text().substr(0, 1) == 'T' && $('#txt-Txt_Direccion_Entidad_Cliente').val().length === 0) {//1 = Nuevo
    $('#txt-Txt_Direccion_Entidad_Cliente').closest('.form-group').find('.help-block').html('Guía electrónica es obligatorio dirección destino');
    $('#txt-Txt_Direccion_Entidad_Cliente').closest('.form-group').removeClass('has-success').addClass('has-error');

    bEstadoValidacion = false;
    scrollToError($("html, body"), $('#txt-No_Entidad_Cliente'));
  } else if ($('[name="addCliente"]:checked').attr('value') == 0 && $('#cbo-TiposDocumento').val() == 7 && $('#cbo-SeriesDocumento').find(':selected').text().substr(0, 1) == 'T' && $('#txt-Txt_Direccion_Entidad').val().length === 0) {//0 = Existente
    $('#txt-Txt_Direccion_Entidad').closest('.form-group').find('.help-block').html('Guía electrónica es obligatorio dirección destino');
    $('#txt-Txt_Direccion_Entidad').closest('.form-group').removeClass('has-success').addClass('has-error');

    bEstadoValidacion = false;
    scrollToError($("html, body"), $('#txt-Txt_Direccion_Entidad'));
  } else if ($('#cbo-TiposDocumento').val() == 7 && $('#cbo-SeriesDocumento').find(':selected').text().substr(0, 1) == 'T' && (parseFloat($('#txt-Ss_Peso_Bruto').val()) < 1 || isNaN(parseFloat($('#txt-Ss_Peso_Bruto').val())))) {
    $('#txt-Ss_Peso_Bruto').closest('.form-group').find('.help-block').html('Peso bruto debe ser 1 o mayor');
    $('#txt-Ss_Peso_Bruto').closest('.form-group').removeClass('has-success').addClass('has-error');

    bEstadoValidacion = false;
    scrollToError($("html, body"), $('#txt-Ss_Peso_Bruto'));
  } else if ($('#cbo-TiposDocumento').val() == 7 && $('#cbo-SeriesDocumento').find(':selected').text().substr(0, 1) == 'T' && $('[name="radio-TipoTransporte"]:checked').attr('value') == '01' && $('#txt-transportista-ID_Tipo_Documento_Identidad').val() != '4') {
    $('#txt-ANombre_Transportista').closest('.form-group').find('.help-block').html('TRANSPORTE PUBLICO: Transportista debe ser tipo RUC');
    $('#txt-ANombre_Transportista').closest('.form-group').removeClass('has-success').addClass('has-error');

    bEstadoValidacion = false;
    scrollToError($("html, body"), $('#txt-ANombre_Transportista'));
  } else if ($('#cbo-TiposDocumento').val() == 7 && $('#cbo-SeriesDocumento').find(':selected').text().substr(0, 1) == 'T' && $('[name="radio-TipoTransporte"]:checked').attr('value') == '01' && $('#txt-transportista-ID_Tipo_Documento_Identidad').val() == $('#hidden-Nu_Documento_Identidad-empresa').val()) {
    $('#txt-ANombre_Transportista').closest('.form-group').find('.help-block').html('TRANSPORTE PUBLICO: El RUC de Transportista no puede ser igual al RUC de la Empresa');
    $('#txt-ANombre_Transportista').closest('.form-group').removeClass('has-success').addClass('has-error');

    bEstadoValidacion = false;
    scrollToError($("html, body"), $('#txt-ANombre_Transportista'));
  } else if ($('#cbo-TiposDocumento').val() == 7 && $('#cbo-SeriesDocumento').find(':selected').text().substr(0, 1) == 'T' && $('[name="radio-TipoTransporte"]:checked').attr('value') == '02' && $('#txt-No_Placa').val().length < 6) {
    $('#txt-No_Placa').closest('.form-group').find('.help-block').html('Ingresar Placa mínimo 6 dígitos');
    $('#txt-No_Placa').closest('.form-group').removeClass('has-success').addClass('has-error');

    bEstadoValidacion = false;
    scrollToError($("html, body"), $('#txt-No_Placa'));
  } else if ($('#cbo-TiposDocumento').val() == 7 && $('#cbo-SeriesDocumento').find(':selected').text().substr(0, 1) == 'T' && $('[name="radio-TipoTransporte"]:checked').attr('value') == '02' && $('#txt-No_Licencia').val().length < 9) {
    $('#txt-No_Licencia').closest('.form-group').find('.help-block').html('Ingresar licencia mínimo 9 dígitos');
    $('#txt-No_Licencia').closest('.form-group').removeClass('has-success').addClass('has-error');

    bEstadoValidacion = false;
    scrollToError($("html, body"), $('#txt-No_Licencia'));
  }
  return bEstadoValidacion;
}

function generarTablaTemporalItems($ID_Producto, $No_Producto, $Ss_Precio, $ID_Impuesto_Cruce_Documento, $Nu_Tipo_Impuesto, $Ss_Impuesto, $iTipoItem, $Nu_Codigo_Barra, $No_Codigo_Interno, $ID_Impuesto_Icbper, arrDataAdicionalTmpDetalleItem){
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
    +"<td class='text-right'><input type='text' inputmode='decimal' id=" + $ID_Producto + " class='pos-input txt-Qt_Producto form-control input-decimal input-size_cantidad' " + ($iTipoItem == 1 ? 'onkeyup=validateStockNow(event);' : '') + " data-id_impuesto_icbper='" + $ID_Impuesto_Icbper + "' data-id_item='" + $ID_Producto + "' data-id_producto='" + $ID_Producto + "' value='1' autocomplete='off'></td>"
    +"<td class='text-left'>"
      + '[' + $Nu_Codigo_Barra + ']<br>'
      + '<span style="font-size: 13px;font-weight:bold;">' + $No_Producto + '</span>'
      + sVarianteMultipleTmp
      + (arrDataAdicionalTmpDetalleItem.No_Unidad_Medida !== undefined && arrDataAdicionalTmpDetalleItem.No_Unidad_Medida !== null && arrDataAdicionalTmpDetalleItem.No_Unidad_Medida != '' ? ' <br><span style="font-size: 10px;font-weight:normal;">[' + arrDataAdicionalTmpDetalleItem.No_Unidad_Medida + ']</span> ' : '')
    +"</td>"
    +"<td style='display:none;' class='text-right'><input type='text' inputmode='decimal' class='pos-input txt-fValorUnitario form-control input-decimal input-size_importe' data-id_producto='" + $ID_Producto + "' value='" + $Ss_SubTotal_Producto.toFixed(2) + "' autocomplete='off'></td>"
    +"<td class='text-right'><input type='text' inputmode='decimal' class='pos-input txt-Ss_Precio form-control input-decimal input-size_importe' data-id_producto='" + $ID_Producto + "' value='" + $Ss_Precio + "' autocomplete='off'></td>"
    +"<td class='text-right'>"
      +"<select class='cbo-ImpuestosProducto form-control required' style='width: 100%;'>"
        +option_impuesto_producto
      +"</select>"
    +"</td>"
    +"<td style='display:none;' class='text-right'><input type='text' inputmode='decimal' class='pos-input txt-Ss_SubTotal_Producto form-control' value='" + $Ss_SubTotal_Producto.toFixed(2) + "' autocomplete='off' disabled></td>"
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
    var $Ss_Descuento_p = 0, iCantidadItem=0;
    $("#table-DetalleProductos > tbody > tr").each(function(){
      var rows = $(this);
      iCantidadItem += parseFloat(rows.find(".txt-Qt_Producto").val());
      var fImpuesto = parseFloat(rows.find('.cbo-ImpuestosProducto option:selected').data('impuesto_producto'));
      var iGrupoImpuesto = rows.find('.cbo-ImpuestosProducto option:selected').data('nu_tipo_impuesto');
      var $Ss_SubTotal_Producto = parseFloat(rows.find('.txt-Ss_SubTotal_Producto', this).val() / fImpuesto);
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

      $('#span-total_importe').text($Ss_Total.toFixed(2));
      $('#span-total_cantidad').text(iCantidadItem);
    }
    
	  validateDecimal();
	  validateNumber();
    validateNumberOperation();
    validateCodigoBarra();
  }
}

// Ayudas - combobox
function getAlmacenes(arrParams){
  var arrParamsData = arrParams;
  url = base_url + 'HelperController/getAlmacenes';
  $.post( url, {}, function( responseAlmacen ){
    var iCantidadRegistros = responseAlmacen.length;
    var selected = '';
    var iIdAlmacen = 0;
    if (iCantidadRegistros == 1) {
      if (arrParamsData !== undefined) {
        iIdAlmacen = arrParamsData.ID_Almacen;
      }
      if (iIdAlmacen == responseAlmacen[0]['ID_Almacen']){
        selected = 'selected="selected"';
      }
      $('#' + arrParamsData.iIdComboxAlmacen).html('<option value="' + responseAlmacen[0]['ID_Almacen'] + '" ' + selected + '>' + responseAlmacen[0]['No_Almacen'] + '</option>');
      var arrParams = {
        ID_Almacen : responseAlmacen[0]['ID_Almacen'],
      }
      getListaPrecios(arrParams);
    } else {
      $('#' + arrParamsData.iIdComboxAlmacen).html( '<option value="0">- Seleccionar -</option>');
      for (var i = 0; i < iCantidadRegistros; i++) {
        selected='';
        if (arrParamsData !== undefined) {
          iIdAlmacen = arrParamsData.ID_Almacen;
        }
        if (iIdAlmacen == responseAlmacen[i]['ID_Almacen']) {
          selected = 'selected="selected"';
        }        
        $('#' + arrParamsData.iIdComboxAlmacen).append( '<option value="' + responseAlmacen[i]['ID_Almacen'] + '" ' + selected + '>' + responseAlmacen[i]['No_Almacen'] + '</option>' );
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

function sendDocumentoSunat(ID, Nu_Estado, sTipoBajaSunat, ID_Tipo_Documento){
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
      
    _sendDocumentoSunat(ID, Nu_Estado, 'json', sTipoBajaSunat, ID_Tipo_Documento);
  });
}

function _sendDocumentoSunat(ID, Nu_Estado, sTypeResponse, sTipoBajaSunat, ID_Tipo_Documento){
  url = base_url + 'Logistica/SalidaInventarioController/sendDocumentoSunat/' + ID + '/' + Nu_Estado + '/' + sTypeResponse + '/' + sTipoBajaSunat + '/' + ID_Tipo_Documento;
  $.ajax({
    url       : url,
    type      : "GET",
    dataType  : "JSON",
    success: function( response ){
      $( '.modal-title-message' ).text( '' );
	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
  	  $( '#modal-message' ).modal('show');
		  
		  if (response.status == 'success'){
  	    $( '.modal-message' ).addClass('modal-' + response.sStatus);
  	    $( '.modal-title-message' ).text(response.sMessage);
  	    reload_table_compra();
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 300);
		  } else {
  	    $( '.modal-message' ).addClass('modal-' + response.sStatus);
        $( '.modal-title-message' ).text( response.sMessage );
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 4100);
  	    reload_table_compra();
      }
      
      $( '#btn-sunat-' + ID ).text('');
      $( '#btn-sunat-' + ID ).attr('disabled', false);
      $( '#btn-sunat-' + ID ).append( '<i class="fa fa-cloud-upload"></i> Sunat' );
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
      
      url = base_url + 'Logistica/SalidaInventarioController/sendCorreoFacturaVentaSUNAT/';
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

function facturarGuia(ID) {
  var $modal = $('.modal-orden');
  $modal.modal('show');

  $modal.on('shown.bs.modal', function () {
    $('.hidden-modal_orden').focus();
  })

  //Salir modal
  $('#btn-modal-salir-orden').off('click').click(function () {
    $modal.modal('hide');
  });
  //Fin Salir modal

  //Limpiar modal
  $('#div-modal-body-orden').empty();
  
  $(document).ready(function () {
    $('#cbo-modal-descargar_stock').html('<option value="1">Si</option>');
    $('#cbo-modal-descargar_stock').append('<option value="0">No</option>');

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

    $('#cbo-tipo_documento_modal').html('<option value="0" selected="selected">- Seleccionar -</option>');
    if( $('#hidden-Nu_Tipo_Proveedor_FE').val()!=3 ) {
      $('#cbo-tipo_documento_modal').append('<option value="3" data-nu_impuesto="1">Factura</option>');
      $('#cbo-tipo_documento_modal').append('<option value="4" data-nu_impuesto="1">Boleta</option>');
    }
    $('#cbo-tipo_documento_modal').append('<option value="2" data-nu_impuesto="0">Nota Venta</option>');

    $('#cbo-tipo_documento_modal').change(function () {
      $('#cbo-serie_documento_modal').html('');
      if ($(this).val() > 0) {
        url = base_url + 'HelperController/getSeriesDocumento';
        $.post(url, { ID_Organizacion: $('#header-a-id_organizacion').val(), ID_Tipo_Documento: $(this).val() }, function (response) {
          if (response.length == 0)
            $('#cbo-serie_documento_modal').html('<option value="0" selected="selected">Sin serie</option>');
          else {
            $('#cbo-serie_documento_modal').html('<option value="0" selected="selected">- Seleccionar -</option>');
            for (var i = 0; i < response.length; i++)
              $('#cbo-serie_documento_modal').append('<option value="' + response[i].ID_Serie_Documento + '" data-id_serie_documento_pk=' + response[i].ID_Serie_Documento_PK + '>' + response[i].ID_Serie_Documento + '</option>');
          }
        }, 'JSON');
      }
    })

    url = base_url + 'HelperController/getMediosPago';
    $.post(url, function (response) {
      $('#cbo-MediosPago-modal').html('');
      for (var i = 0; i < response.length; i++)
        $('#cbo-MediosPago-modal').append('<option value="' + response[i]['ID_Medio_Pago'] + '" data-nu_tipo="' + response[i]['Nu_Tipo'] + '">' + response[i]['No_Medio_Pago'] + '</option>');
    }, 'JSON');

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
      }
    })
    
  });

  var html_orden_cabecera = '';
  html_orden_cabecera +=
    '<div class="row">'
    + '<div class="col-sm-12 col-md-12">'
    + '<div class="panel panel-default">'
    + '<div class="panel-heading"><i class="fa fa-book"></i> <b>Documento</b></div>'
    + '<div class="panel-body">'
    + '<div class="row">'
    + '<div class="col-xs-7 col-sm-3 col-md-3">'
      + '<div class="form-group">'
          +'<label>Operación</label>'
          +'<select id="cbo-sunat_tipo_transaction-modal" class="form-control required" style="width: 100%;"></select>'
        + '<span class="help-block" id="error"></span>'
      + '</div>'
    + '</div>'
    + '<div class="col-xs-7 col-sm-3 col-md-2">'
    + '<div class="form-group">'
    + '<label>Tipo <span class="label-advertencia">*</span></label>'
    + '<select id="cbo-tipo_documento_modal" class="form-control required" style="width: 100%;"></select>'
    + '<span class="help-block" id="error"></span>'
    + '</div>'
    + '</div>'
    + '<div class="col-xs-5 col-sm-3 col-md-2">'
    + '<div class="form-group">'
    + '<label>Series <span class="label-advertencia">*</span></label>'
    + '<select id="cbo-serie_documento_modal" class="form-control required" style="width: 100%;"></select>'
    + '<span class="help-block" id="error"></span>'
    + '</div>'
    + '</div>'
    + '<div class="col-xs-6 col-sm-3 col-md-2">'
    + '<div class="form-group">'
    + '<label>F. Emisión <span class="label-advertencia">*</span></label>'
    + '<div class="input-group date">'
    + '<input type="text" id="txt-Fe_Emision_modal" name="Fe_Emision" class="form-control date-picker-invoice required">'
    + '</div>'
    + '<span class="help-block" id="error"></span>'
    + '</div>'
    + '</div>'
    + '<div class="col-xs-12 col-sm-4 col-md-3">'
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

    + '<div class="col-xs-12 col-sm-12 col-md-2 col-lg-2 div-modal_credito">'
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
    
    + '<div class="col-xs-12 col-sm-6 col-md-3">'
    + '<div class="form-group">'
    + '<label>Almacen</label>'
    + '<select id="cbo-modal-almacen" class="form-control required" style="width: 100%;"></select>'
    + '<span class="help-block" id="error"></span>'
    + '</div>'
    + '</div>'
    + '<div class="col-xs-12 col-sm-4 col-md-2">'
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
    + '<div class="col-xs-12 col-sm-6 col-md-2">'
    + '<div class="form-group">'
    + '<label>¿Stock?</label>'
    + '<select id="cbo-modal-descargar_stock" class="form-control required" style="width: 100%;"></select>'
    + '<span class="help-block" id="error"></span>'
    + '<span class="help-block" id="span-stock"></span>'
    + '</div>'
    + '</div>'
    + '<div class="col-xs-12 col-sm-6 col-md-3">'
    + '<div class="form-group">'
    + '<label>Personal</label>'
    + '<select id="cbo-vendedor-modal" name="ID_Mesero" class="form-control"></select>'
    + '<span class="help-block" id="error"></span>'
    + '</div>'
    + '</div>'
    + '<div class="col-xs-12 col-sm-6 col-md-2">'
    + '<div class="form-group">'
    + '<label>Porcentaje</label>'
    + '<select id="cbo-porcentaje-modal" name="Po_Comision" class="form-control"></select>'
    + '<span class="help-block" id="error"></span>'
    + '</div>'
    + '</div>'
    + '</div>'
    + '</div>'
    + '</div>'
    + '</div>'
    + '</div>'
    ;

  $('#div-modal-body-orden').append(html_orden_cabecera);
  $('.date-picker-invoice').datepicker({
    autoclose: true,
    endDate: new Date(fYear, fToday.getMonth(), fDay),
    todayHighlight: true
  })
  $('.date-picker-invoice').val(fDay + '/' + fMonth + '/' + fYear);

  var Fe_Emision = $('#txt-Fe_Vencimiento-modal').val(fDay + '/' + fMonth + '/' + fYear);
  $('#txt-Fe_Vencimiento-modal').datepicker({
    autoclose: true,
    startDate: new Date(fYear + '-' + fMonth + '-' + (parseInt(fDay) + 1)),
    todayHighlight: true
  })

  $('#txt-Fe_Emision_modal').datepicker({}).on('changeDate', function (selected) {
    var minDate = new Date(selected.date.valueOf());
    $('#txt-Fe_Vencimiento-modal').datepicker('setStartDate', minDate);
  });

  $('.div-modal_datos_tarjeta_credito').hide();
  $('.div-modal_credito').hide();

  //Detalle de la Orden Venta
  $('#modal-loader').modal('show');
  var html_table_orden_detalle = '';
  url = base_url + 'Logistica/SalidaInventarioController/ajax_edit/' + ID;
  $.getJSON(url, function (response) {
    var arrParams = {
      'iIdComboxAlmacen': 'cbo-modal-almacen',
      'ID_Almacen': response.arrEdit[0].ID_Almacen
    };
    getAlmacenes(arrParams);

    /* Personal de ventas */
    $('#cbo-vendedor-modal').html('<option value="">- No hay personal -</option>');
    url = base_url + 'HelperController/getDataGeneral';
    $.post(url, { sTipoData: 'entidad', iTipoEntidad: 4 }, function (responsePersonal) {
      if (responsePersonal.sStatus == 'success') {
        var l = responsePersonal.arrData.length;
        if (l == 1) {
          $('#cbo-vendedor-modal').html('<option value="">- Seleccionar -</option>');
          selected = '';
          if (response.arrEdit[0].ID_Mesero == responsePersonal.arrData[0].ID)
            selected = 'selected="selected"';
          $('#cbo-vendedor-modal').append('<option value="' + responsePersonal.arrData[0].ID + '" ' + selected + '>' + responsePersonal.arrData[0].Nombre + '</option>');
        } else {
          $('#cbo-vendedor-modal').html('<option value="" selected="selected">- Seleccionar -</option>');
          for (var x = 0; x < l; x++) {
            selected = '';
            if (response.arrEdit[0].ID_Mesero == responsePersonal.arrData[x].ID)
              selected = 'selected="selected"';
            $('#cbo-vendedor-modal').append('<option value="' + responsePersonal.arrData[x].ID + '" ' + selected + '>' + responsePersonal.arrData[x].Nombre + '</option>');
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
    $('#cbo-porcentaje-modal').html('<option value="" selected="selected">- No hay porcentaje -</option>');
    url = base_url + 'HelperController/getDataGeneral';
    $.post(url, { sTipoData: 'Porcentaje_Comision_Vendedores' }, function (responsePorcentaje) {
      if (responsePorcentaje.sStatus == 'success') {
        var l = responsePorcentaje.arrData.length;
        if (l == 1) {
          $('#cbo-vendedor-modal').html('<option value="">- Seleccionar -</option>');
          selected = '';
          if (response.arrEdit[0].ID_Comision == responsePorcentaje.arrData[0].ID_Tabla_Dato)
            selected = 'selected="selected"';
          $('#cbo-porcentaje-modal').append('<option value="' + responsePorcentaje.arrData[0].ID_Tabla_Dato + '" ' + selected + '>' + responsePorcentaje.arrData[0].No_Descripcion + '</option>');
        } else {
          $('#cbo-porcentaje-modal').html('<option value="" selected="selected">- Seleccionar -</option>');
          for (var x = 0; x < l; x++) {
            selected = '';
            if (response.arrEdit[0].ID_Comision == responsePorcentaje.arrData[x].ID_Tabla_Dato)
              selected = 'selected="selected"';
            $('#cbo-porcentaje-modal').append('<option value="' + responsePorcentaje.arrData[x].ID_Tabla_Dato + '" ' + selected + '>' + responsePorcentaje.arrData[x].No_Descripcion + '</option>');
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

    url = base_url + 'HelperController/getMonedas';
    $.post(url, function (responseMonedas) {
      $('#cbo-Monedas').html('');
      for (var i = 0; i < responseMonedas.length; i++) {
        if (response.arrEdit[0].ID_Moneda == responseMonedas[i].ID_Moneda)
          $('.span-signo').text(responseMonedas[i].No_Signo);
      }
    }, 'JSON');

    $('#span-stock').html('');
    $('#cbo-modal-descargar_stock').prop('disabled', false);
    if (response.arrEdit[0].Nu_Descargar_Inventario==1) {
      $('#cbo-modal-descargar_stock').val(0);//no descargo nuevamente
      $('#cbo-modal-descargar_stock').prop('disabled', true);

      $('#span-stock').html('Ya se realizo la descarga de stock');
    }
    
    $('#table-DetalleProductosOrdenVentaModal tbody').empty();
    $('#table-DetalleProductosOrdenVentaModal tfoot').empty();

    html_table_orden_detalle +=
      '<input type="hidden" id="txt-ID_Empresa" value="' + response.arrEdit[0].ID_Empresa + '">'
      + '<input type="hidden" id="txt-ID_Guia_Cabecera" value="' + response.arrEdit[0].ID_Guia_Cabecera + '">'
      + '<input type="hidden" id="modal-txt-TiposDocumentoModificar" value="' + response.arrEdit[0].ID_Tipo_Documento + '">'
      + '<input type="hidden" id="modal-txt-ID_Entidad" value="' + response.arrEdit[0].ID_Entidad + '">'
      + '<input type="hidden" id="txt-ID_Medio_Pago" value="' + response.arrEdit[0].ID_Medio_Pago + '">'
      + '<input type="hidden" id="txt-ID_Moneda" value="' + response.arrEdit[0].ID_Moneda + '">'
      + '<input type="hidden" id="txt-ID_Entidad" value="' + response.arrEdit[0].ID_Entidad + '">'
      + '<input type="hidden" id="txt-ID_Contacto" value="' + response.arrEdit[0].ID_Contacto + '">'
      + '<input type="hidden" id="txt-Txt_Glosa" value="' + response.arrEdit[0].Txt_Glosa + '">'
      + '<input type="hidden" id="txt-ID_Lista_Precio_Cabecera" value="' + response.arrEdit[0].ID_Lista_Precio_Cabecera + '">'
      + '<input type="hidden" id="hidden-Txt_Email_Entidad-modal" value="' + response.arrEdit[0].Txt_Email_Entidad + '">'
      + '<input type="hidden" id="hidden-Nu_Celular_Entidad-modal" value="' + response.arrEdit[0].Nu_Celular_Entidad + '">'
      + '<input type="hidden" id="hidden-Txt_Direccion_Entidad-modal" value="' + response.arrEdit[0].Txt_Direccion_Destino + '">'
      + '<div class="row">'
      + '<div class="col-md-12">'
      + '<div id="panel-DetalleProductosOrdenVenta_modal" class="panel panel-default">'
      + '<div class="panel-heading panel-heading_table"><i class="fa fa-shopping-cart"></i> <b>Detalle de items</b></div>'
      + '<div class="panel-body">'
      + '<div class="tab-panel_default_modal_table row">'
      + '<div class="col-md-12">'
      + '<div class="table-responsive">'
      + '<table id="table-DetalleProductosOrdenVentaModal" class="table table-striped table-bordered">'
      + '<thead>'
      + '<tr>'
      + '<th class="text-center"><input type="checkbox" class="flat-red" onclick="checkAllOrden();" id="check-AllOrden" checked></th>'
      + '<th style="display:none;" class="text-left"></th>'
      + '<th class="text-center">Cantidad</th>'
      + '<th class="text-center">Item</th>'
      + '<th class="text-center">Precio</th>'
      + '<th class="text-left" style="width: 17%;">Impuesto</th>'
      + '<th style="display:none;" class="text-center"></th>'
      + '<th class="text-center">Sub Total</th>'
      + '<th class="text-center">% Dscto</th>'
      + '<th class="text-center">Total</th>'
      + '<th style="display:none;" class="text-left"></th>'
      + '</tr>'
      + '</thead>'
      + '<tbody>'
      + '</tbody>'
      + '</table>'
      + '</div>'
      + '</div>'
      + '</div>'
      + '</div>'//Fin panel body
      + '</div>'//Fin panel padre
      + '</div>'
      + '</div>';

    $('#div-modal-body-orden').append(html_table_orden_detalle);

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
      if (response.arrEdit[i].Nu_Tipo_Impuesto == 1) {
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

      for (var x = 0; x < iTotalRegistrosImpuestos; x++) {
        selected = '';
        if (response.arrImpuesto[x].ID_Impuesto_Cruce_Documento == response.arrEdit[i].ID_Impuesto_Cruce_Documento)
          selected = 'selected="selected"';
        option_impuesto_producto += "<option value='" + response.arrImpuesto[x].ID_Impuesto_Cruce_Documento + "' data-nu_tipo_impuesto='" + response.arrImpuesto[x].Nu_Tipo_Impuesto + "' data-impuesto_producto='" + response.arrImpuesto[x].Ss_Impuesto + "' " + selected + ">" + response.arrImpuesto[x].No_Impuesto + "</option>";
      }

      table_detalle_producto +=
        "<tr id='tr_detalle_producto_modal" + response.arrEdit[i].ID_Producto + "'>"
        + "<td style='display:none;' class='text-left'>" + response.arrEdit[i].ID_Producto + "</td>"
        + "<td class='text-center'><input type='checkbox' class='flat-red check-orden' onclick='calcularTotalChecked();' checked></td>"
        + "<td class='text-right'>" + response.arrEdit[i].Qt_Producto + "</td>"
        + "<td class='text-left'>" + response.arrEdit[i].Nu_Codigo_Barra + " " + response.arrEdit[i].No_Producto + "</td>"
        + "<td class='text-right'>" + response.arrEdit[i].Ss_Precio + "</td>"
        + "<td class='text-left'>" + response.arrEdit[i].No_Impuesto_Breve + " " + response.arrEdit[i].Po_Impuesto + " %</td>"
        + "<td style='display:none;' class='text-left'>" + response.arrEdit[i].ID_Impuesto_Cruce_Documento + "</td>"
        + "<td class='text-right'>" + response.arrEdit[i].Ss_SubTotal_Producto + "</td>"
        + "<td class='text-right'>" + (response.arrEdit[i].Po_Descuento_Impuesto_Producto == 0.00 ? '' : response.arrEdit[i].Po_Descuento_Impuesto_Producto) + "</td>"
        + "<td class='text-right'>" + response.arrEdit[i].Ss_Total_Producto + "</td>"
        + "<td style='display:none;' class='text-left'>" + response.arrEdit[i].Ss_Impuesto + "</td>"
        + "<td style='display:none;' class='text-right td-fDescuentoSinImpuestosItem'>" + (response.arrEdit[i].Ss_Descuento_Producto == 0.00 ? '' : response.arrEdit[i].Ss_Descuento_Producto) + "</td>"
        + "<td style='display:none;' class='text-right td-fDescuentoImpuestosItem'>" + (response.arrEdit[i].Ss_Descuento_Impuesto_Producto == 0.00 ? '' : response.arrEdit[i].Ss_Descuento_Impuesto_Producto) + "</td>"
        + "<td style='display:none;' class='text-right td-fValorUnitario'>" + parseFloat(response.arrEdit[i].Ss_Precio / response.arrEdit[i].Ss_Impuesto).toFixed(2) + "</td>"
        + "</tr>";
    }

    $('#table-DetalleProductosOrdenVentaModal >tbody').append(table_detalle_producto);

    //Orden totales
    var html_orden_totales = '';
    html_orden_totales +=
      '<div class="row">'
      + '<div class="col-md-8"></div>'
      + '<div class="col-md-4">'
      + '<div class="panel panel-default">'
      + '<div class="panel-heading"><i class="fa fa-money"></i> <b>Totales</b></div>'
      + '<div class="panel-body">'
      + '<table class="table" id="table-OrdenVentaTotal">'
      + '<tr style="display:none;">'
      + '<td style="display:none;"><label>% Descuento</label></td>'
      + '<td style="display:none;" class="text-right">'
      + '<input type="tel" class="form-control input-decimal" id="txt-Ss_Descuento" name="Ss_Descuento" size="3" value="' + response.arrEdit[0].Po_Descuento + '" autocomplete="off" />'
      + '</td>'
      + '</tr>'
      + '<tr>'
      + '<td><label>OP. Gravadas</label></td>'
      + '<td class="text-right">'
      + '<input type="hidden" class="form-control" id="txt-subTotal_modal" value="' + $Ss_Gravada.toFixed(2) + '"/>'
      + '<span class="span-signo"></span> <span id="span-subTotal_modal">' + $Ss_Gravada.toFixed(2) + '</span>'
      + '</td>'
      + '</tr>'
      + '<tr>'
      + '<td><label>OP. Inafectas</label></td>'
      + '<td class="text-right">'
      + '<input type="hidden" class="form-control" id="txt-inafecto_modal" value="' + $Ss_Inafecto.toFixed(2) + '"/>'
      + '<span class="span-signo"></span> <span id="span-inafecto_modal">' + $Ss_Inafecto.toFixed(2) + '</span>'
      + '</td>'
      + '</tr>'
      + '<tr>'
      + '<td><label>OP. Exoneradas</label></td>'
      + '<td class="text-right">'
      + '<input type="hidden" class="form-control" id="txt-exonerada_modal" value="' + $Ss_Exonerada.toFixed(2) + '"/>'
      + '<span class="span-signo"></span> <span id="span-exonerada_modal">' + $Ss_Exonerada.toFixed(2) + '</span>'
      + '</td>'
      + '</tr>'
      + '<tr>'
      + '<td><label>Gratuitas</label></td>'
      + '<td class="text-right">'
      + '<input type="hidden" class="form-control" id="txt-gratuita_modal" value="' + $Ss_Gratuita.toFixed(2) + '"/>'
      + '<span class="span-signo"></span> <span id="span-gratuita_modal">' + $Ss_Gratuita.toFixed(2) + '</span>'
      + '</td>'
      + '</tr>'
      + '<tr>'
      + '<td><label>Descuento Total (-)</label></td>'
      + '<td class="text-right">'
      + '<input type="hidden" class="form-control" id="txt-descuento_modal" value="' + response.arrEdit[0].Ss_Descuento + '"/>'
      + '<span class="span-signo"></span> <span id="span-descuento_modal">' + response.arrEdit[0].Ss_Descuento + '</span>'
      + '</td>'
      + '</tr>'
      + '<tr>'
      + '<td><label>I.G.V. %</label></td>'
      + '<td class="text-right">'
      + '<input type="hidden" class="form-control" id="txt-impuesto_modal" value="' + $Ss_IGV.toFixed(2) + '"/>'
      + '<span class="span-signo"></span> <span id="span-impuesto_modal">' + $Ss_IGV.toFixed(2) + '</span>'
      + '</td>'
      + '</tr>'
      + '<tr>'
      + '<td><label>Total</label></td>'
      + '<td class="text-right">'
      + '<input type="hidden" class="form-control" id="txt-total_modal" value="' + $Ss_Total.toFixed(2) + '"/>'
      + '<span class="span-signo"></span> <span id="span-total_modal">' + $Ss_Total.toFixed(2) + '</span>'
      + '</td>'
      + '</tr>'
      + '</table>'
      + '</div>'
      + '</div>'
      + '</div>'
      ;

    $('#div-modal-body-orden').append(html_orden_totales);
    $('#modal-loader').modal('hide');
  })//Fin Get JSON
  //Fin orden detalle

  $('#btn-modal-facturar-orden').off('click').click(function () {
    accion_orden_venta = 'add_orden_venta_modal';
    addVenta();
  });
}

function checkAllOrden() {
  if ($('#check-AllOrden').prop('checked')) {
    $('.check-orden').prop('checked', true);
    $('#check-AllOrden').prop('checked', true);
    calcularTotalChecked();
  } else {
    if (false == $('#check-AllOrden').prop('checked')) {
      $('.check-orden').prop('checked', false);
      $('#check-AllOrden').prop('checked', false);

      $('#txt-subTotal_modal').val(0.00);
      $('#span-subTotal_modal').text('0.00');

      $('#txt-inafecto_modal').val(0.00);
      $('#span-inafecto_modal').text('0.00');

      $('#txt-exonerada_modal').val(0.00);
      $('#span-exonerada_modal').text('0.00');

      $('#txt-gratuita_modal').val(0.00);
      $('#span-gratuita_modal').text('0.00');

      $('#txt-impuesto_modal').val(0.00);
      $('#span-impuesto_modal').text('0.00');

      $('#txt-descuento_modal').val(0.00);
      $('#span-descuento_modal').text('0.00');

      $('#txt-total_modal').val(0.00);
      $('#span-total_modal').text('0.00');
    }
  }
}

function calcularTotalChecked() {
  var $Ss_SubTotal = 0.00;
  var $Ss_Exonerada = 0.00;
  var $Ss_Inafecto = 0.00;
  var $Ss_Gratuita = 0.00;
  var $Ss_IGV = 0.00;
  var $Ss_Total = 0.00;
  var iCantDescuento = 0;
  var globalImpuesto = 0;

  $('#table-DetalleProductosOrdenVentaModal > tbody > tr').each(function () {
    var rows = $(this);

    if (rows.find('input[type="checkbox"]').is(':checked')) {
      var rows = $(this);

      var $ID_Impuesto_Cruce_Documento = rows.find("td:eq(6)").text();
      var $Ss_SubTotal_Producto = parseFloat(rows.find("td:eq(7)").text());
      var $Ss_Descuento_Producto = parseFloat(rows.find("td:eq(8)").text());
      var $Ss_Impuesto = parseFloat(rows.find("td:eq(10)").text());
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

      if (isNaN($Ss_Descuento_Producto))
        $Ss_Descuento_Producto = 0;
    }
  });

  $('#txt-subTotal_modal').val($Ss_SubTotal.toFixed(2));
  $('#span-subTotal_modal').text($Ss_SubTotal.toFixed(2));

  $('#txt-inafecto_modal').val($Ss_Inafecto.toFixed(2));
  $('#span-inafecto_modal').text($Ss_Inafecto.toFixed(2));

  $('#txt-exonerada_modal').val($Ss_Exonerada.toFixed(2));
  $('#span-exonerada_modal').text($Ss_Exonerada.toFixed(2));

  $('#txt-gratuita_modal').val($Ss_Gratuita.toFixed(2));
  $('#span-gratuita_modal').text($Ss_Gratuita.toFixed(2));

  $('#txt-impuesto_modal').val($Ss_IGV.toFixed(2));
  $('#span-impuesto_modal').text($Ss_IGV.toFixed(2));

  $('#txt-descuento_modal').val(0);
  $('#span-descuento_modal').text(0);

  $('#txt-total_modal').val($Ss_Total.toFixed(2));
  $('#span-total_modal').text($Ss_Total.toFixed(2));
}

function addVenta() {
  if (accion_orden_venta == 'add_orden_venta_modal') {
    var arrDetalleVenta = [];

    $('#table-DetalleProductosOrdenVentaModal > tbody > tr').each(function () {
      var rows = $(this);

      if (rows.find('input[type="checkbox"]').is(':checked')) {
        var $ID_Producto = rows.find("td:eq(0)").text();
        var $Qt_Producto = rows.find("td:eq(2)").text();
        var $Ss_Precio = rows.find("td:eq(4)").text();
        var $ID_Impuesto_Cruce_Documento = rows.find("td:eq(6)").text();
        var $Ss_SubTotal = rows.find("td:eq(7)").text();
        var $Ss_Descuento = rows.find("td:eq(8)").text();
        var $Ss_Total = rows.find("td:eq(9)").text();
        var $fDescuentoSinImpuestosItem = rows.find(".td-fDescuentoSinImpuestosItem").text();
        var $fDescuentoImpuestosItem = rows.find(".td-fDescuentoImpuestosItem").text();
        var $fValorUnitario = rows.find(".td-fValorUnitario").text();
        var $fIcbperItem=0.00;

        var obj = {};

        obj.ID_Producto = $ID_Producto;
        obj.fValorUnitario = $fValorUnitario;
        obj.Ss_Precio = $Ss_Precio;
        obj.Qt_Producto = $Qt_Producto;
        obj.ID_Impuesto_Cruce_Documento = $ID_Impuesto_Cruce_Documento;
        obj.Ss_SubTotal = $Ss_SubTotal;
        obj.Ss_Descuento = $Ss_Descuento;
        obj.Ss_Impuesto = $Ss_Total - $Ss_SubTotal;
        obj.Ss_Total = $Ss_Total;
        obj.fDescuentoSinImpuestosItem = $fDescuentoSinImpuestosItem;
        obj.fDescuentoImpuestosItem = $fDescuentoImpuestosItem;
        obj.fIcbperItem = $fIcbperItem;
        arrDetalleVenta.push(obj);
      }
    });

    if ($('#cbo-tipo_documento_modal').val() == 0) {
      $('#cbo-tipo_documento_modal').closest('.form-group').find('.help-block').html('Seleccionar tipo');
      $('#cbo-tipo_documento_modal').closest('.form-group').removeClass('has-success').addClass('has-error');

      scrollToError($('.modal-orden .modal-body'), $('#cbo-tipo_documento_modal'));
    } else if ($('#cbo-serie_documento_modal').val() == 0) {
      $('#cbo-serie_documento_modal').closest('.form-group').find('.help-block').html('Seleccionar serie');
      $('#cbo-serie_documento_modal').closest('.form-group').removeClass('has-success').addClass('has-error');

      scrollToError($('.modal-orden .modal-body'), $('#cbo-serie_documento_modal'));
    } else if (arrDetalleVenta.length == 0) {
      $('#panel-DetalleProductosOrdenVenta_modal').removeClass('panel-default');
      $('#panel-DetalleProductosOrdenVenta_modal').addClass('panel-danger');

      $('.modal-message').removeClass('modal-danger modal-warning modal-success');

      $('#modal-message').modal('show');

      $('.modal-message').addClass('modal-danger');
      $('.modal-title-message').text('No ha seleccionado ningún producto');

      $('.modal-message').css("z-index", "2000");

      setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
    } else {
      $('#panel-DetalleProductosOrdenVenta_modal').removeClass('panel-danger');
      $('#panel-DetalleProductosOrdenVenta_modal').addClass('panel-default');

      var iDescargarStock = ($('#cbo-modal-descargar_stock').val() == 1 ? 0 : $('#cbo-modal-descargar_stock').val());

      var arrVentaCabecera = Array();
      arrVentaCabecera = {
        'esEnlace': 2,//Este parametro se usa solo cuando se desea verificar ND o NC
        'EID_Empresa': '',
        'EID_Documento_Cabecera': '',
        'ID_Guia_Cabecera': $('#txt-ID_Guia_Cabecera').val(),
        'ID_Entidad': $('#txt-ID_Entidad').val(),
        
        'Txt_Email_Entidad': $('#hidden-Txt_Email_Entidad-modal').val(),
        'Nu_Celular_Entidad': $('#hidden-Nu_Celular_Entidad-modal').val(),
        'Txt_Direccion_Entidad': $('#hidden-Txt_Direccion_Entidad-modal').val(),
        'ID_Sunat_Tipo_Transaction': $('#cbo-sunat_tipo_transaction-modal').val(),

        'ID_Tipo_Documento': $('#cbo-tipo_documento_modal').val(),
        'ID_Serie_Documento_PK': $('#cbo-serie_documento_modal').find(':selected').data('id_serie_documento_pk'),
        'ID_Serie_Documento': $('#cbo-serie_documento_modal').val(),
        'ID_Numero_Documento': '',
        'Fe_Emision': $('#txt-Fe_Emision_modal').val(),
        'ID_Moneda': $('#txt-ID_Moneda').val(),
        'Fe_Vencimiento': $('#txt-Fe_Vencimiento-modal').val(),
        'Txt_Glosa': $('#txt-Txt_Glosa').val(),
        'Nu_Detraccion': 0,
        'Po_Descuento': $('#txt-Ss_Descuento').val(),
        'Ss_Descuento': $('#txt-descuento_modal').val(),
        'Ss_Total': $('#txt-total_modal').val(),
        'ID_Lista_Precio_Cabecera': $('#txt-ID_Lista_Precio_Cabecera').val(),
        'ID_Documento_Cabecera_Orden': $('#txt-ID_Documento_Cabecera').val(),
        'Txt_Garantia': '',
        'ID_Mesero': $('#cbo-vendedor-modal').val(),
        'ID_Comision': $('#cbo-porcentaje-modal').val(),
        'No_Formato_PDF': $('#cbo-modal-formato_pdf').val(),
        'Nu_Descargar_Inventario': iDescargarStock,
        'ID_Almacen': $('#cbo-modal-almacen').val(),
        'ID_Medio_Pago': $('#cbo-MediosPago-modal').val(),
        'iTipoFormaPago': $('#cbo-MediosPago-modal').find(':selected').data('nu_tipo'),
        iTipoCliente: 0,
        'ID_Tipo_Medio_Pago': $('#cbo-modal_tarjeta_credito').val(),
        'Nu_Transaccion': $('#tel-nu_referencia').val(),
        'Nu_Tarjeta': $('#tel-nu_ultimo_4_digitos_tarjeta').val(),
        'No_Orden_Compra_FE': '',
        'No_Placa_FE': '',
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

      $('#btn-modal-facturar-orden').text('');
      $('#btn-modal-facturar-orden').attr('disabled', true);
      $('#btn-modal-facturar-orden').append('Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>');

      $('#modal-loader').modal('show');
      $('#modal-loader').css("z-index", "3000");

      url = base_url + 'Ventas/VentaController/crudVenta';
      $.ajax({
        type: 'POST',
        dataType: 'JSON',
        url: url,
        data: {
          arrVentaCabecera: arrVentaCabecera,
          arrDetalleVenta: arrDetalleVenta,
          arrVentaModificar: arrVentaModificar,
          arrClienteNuevo: arrClienteNuevo,
        },
        success: function (response) {
          $('#modal-loader').modal('hide');

          $('.modal-message').removeClass('modal-danger modal-warning modal-success');
          $('#modal-message').modal('show');
          $('.modal-message').css("z-index", "4000");

          if (response.status == 'success') {
            accion_orden_venta = '';

            $('.modal-orden').modal('hide');
            $('.modal-message').addClass(response.style_modal);
            $('.modal-title-message').text(response.message);
            setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
            reload_table_compra();
          } else {
            $('.modal-message').addClass(response.style_modal);
            $('.modal-title-message').text(response.message);
            if (response.message_nubefact.length > 0)
              $('.modal-title-message').text(response.message_nubefact);
            setTimeout(function () { $('#modal-message').modal('hide'); }, 4000);
          }

          $('#btn-modal-facturar-orden').text('');
          $('#btn-modal-facturar-orden').append('Facturar (ENTER)');
          $('#btn-modal-facturar-orden').attr('disabled', false);
        },
        error: function (jqXHR, textStatus, errorThrown) {
          $('#modal-loader').modal('hide');
          $('.modal-message').removeClass('modal-danger modal-warning modal-success');

          $('#modal-message').modal('show');
          $('.modal-message').addClass('modal-danger');
          $('.modal-title-message').text(textStatus + ' [' + jqXHR.status + ']: ' + errorThrown);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1700);

          //Message for developer
          console.log(jqXHR.responseText);

          $('#btn-modal-facturar-orden').text('');
          $('#btn-modal-facturar-orden').append('<span class="fa fa-save"></span> Facturar (ENTER)');
          $('#btn-modal-facturar-orden').attr('disabled', false);
        }
      });
    }
  }
}

function verRepresentacionInternaPDF(ID) {
  var $modal_delete = $('#modal-message-delete');
  $modal_delete.modal('show');

  $('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
  $('.modal-message-delete').addClass('modal-success');

  $('.modal-title-message-delete').text('¿Deseas generar PDF?');

  $('#btn-cancel-delete').off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $('#btn-save-delete').off('click').click(function () {
    sendPDF($modal_delete, ID);
  });
}

function sendPDF($modal_delete, ID) {
  $('#modal-loader').modal('show');
  $modal_delete.modal('hide');
  url = base_url + 'Logistica/SalidaInventarioController/generarRepresentacionInternaPDF/' + ID;
  window.open(url, '_blank');
  $('#modal-loader').modal('hide');
}

function imprimirRegistro(ID) {
  var $modal_imprimir = $('.modal-message-delete');
  $modal_imprimir.modal('show');

  $('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
  $('.modal-message-delete').addClass('modal-success');

  $('.modal-title-message-delete').text('¿Deseas imprimir el documento?');

  $('#btn-cancel-delete').off('click').click(function () {
    $modal_imprimir.modal('hide');
  });

  $('#btn-save-delete').off('click').click(function () {
    url = base_url + 'Logistica/SalidaInventarioController/imprimirRegistro/' + ID;
    //window.open(url, '_blank');
    window.location.href = url;
  });
}

function mensajeSUNAT(message){
alert(message);
}

function consultarGuiaElectronicoPSENubefactReseller(ID, ID_Tipo_Documento, ID_Serie_Documento, ID_Numero_Documento, iEstado) {
  $('#btn-sunat-cdr-' + ID).attr('disabled', true);
  $('#span-sunat-cdr-' + ID).append('<i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>');

  var sendPost = {
    ID: ID,
    iEstado: iEstado,
    ID_Tipo_Documento: ID_Tipo_Documento,
    ID_Serie_Documento: ID_Serie_Documento,
    ID_Numero_Documento: ID_Numero_Documento,
    sTipoRespuesta: 'json',
  };

  url = base_url + 'DocumentoElectronicoController/consultarGuiaElectronicoPSENubefactReseller';
  $.ajax({
    url: url,
    type: "POST",
    dataType: "JSON",
    data: sendPost,
    success: function (response) {
      $('.modal-message').removeClass('modal-danger modal-warning modal-success');
      $('#modal-message').modal('show');

      $('#btn-sunat-cdr-' + ID).attr('disabled', false);
      $('#span-sunat-cdr-' + ID).html('');

      if (response.status == 'success') {
        $('.modal-message').addClass(response.style_modal);
        $('.modal-title-message').text(response.message);
        setTimeout(function () { $('#modal-message').modal('hide'); }, 1200);
        reload_table_compra();
      } else {
        $('.modal-message').addClass(response.style_modal);
        $('.modal-title-message').text(response.message);
        setTimeout(function () { $('#modal-message').modal('hide'); }, 6200);
        reload_table_compra();
      }
    }
  });
}

function consultarGuiaElectronicoSunatV2(ID, ID_Tipo_Documento, ID_Serie_Documento, ID_Numero_Documento, iEstado) {
  $('#btn-sunat-cdr-' + ID).attr('disabled', true);
  $('#span-sunat-cdr-' + ID).append('<i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>');

  var sendPost = {
    ID: ID,
    iEstado: iEstado,
    ID_Tipo_Documento: ID_Tipo_Documento,
    ID_Serie_Documento: ID_Serie_Documento,
    ID_Numero_Documento: ID_Numero_Documento,
    sTipoRespuesta: 'json',
  };

  url = base_url + 'DocumentoElectronicoController/consultarGuiaElectronicoSunatV2';
  $.ajax({
    url: url,
    type: "POST",
    dataType: "JSON",
    data: sendPost,
    success: function (response) {
      $('.modal-message').removeClass('modal-danger modal-warning modal-success');
      $('#modal-message').modal('show');

      $('#btn-sunat-cdr-' + ID).attr('disabled', false);
      $('#span-sunat-cdr-' + ID).html('');

      if (response.status == 'success') {
        $('.modal-message').addClass(response.style_modal);
        $('.modal-title-message').text(response.message);
        setTimeout(function () { $('#modal-message').modal('hide'); }, 1200);
        reload_table_compra();
      } else {
        $('.modal-message').addClass(response.style_modal);
        $('.modal-title-message').text(response.message);
        setTimeout(function () { $('#modal-message').modal('hide'); }, 6200);
        reload_table_compra();
      }
    }
  });
}