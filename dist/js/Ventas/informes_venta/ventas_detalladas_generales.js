var url;

function ReloadReporte(){     
  $( '#btn-reload' ).text('');
  $( '#btn-reload' ).attr('disabled', true);
  $( '#btn-reload' ).append( 'Actulizando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
  
  $( '#div-RegistroVentaeIngresos' ).show();

  //url = base_url + 'Ventas/informes_venta/ReporteUtilidadBrutaController/ReporteUtilidadLista';
  url = base_url + 'Ventas/informes_venta/VentasDetalladasGeneralesController/ReporteVentasDetalladasLista';
  $.post( url, {}, function( response ){
    var tpl = _.template($("#TemplateReporte").html());
    var tplString = tpl(response);
    $("#CuerpoReporte").html(tplString);
    $( '#btn-reload' ).text('');
    $( '#btn-reload' ).attr('disabled', false);
    $( '#btn-reload' ).append( 'Actualizar Estado Reporte' );
  },"json");
}

$(function () {
  $('.select2').select2();
  $( '#modal-loader' ).modal('show');
  $( '#div-ventas_detalladas_generales' ).hide();

  $('.div-mas_opciones').hide();
  $('#checkbox-mas_filtros').on('ifChanged', function(){
    $('.div-mas_opciones').hide();
    var _this = jQuery(this);
    if(_this.is(':checked')){
      $('.div-mas_opciones').show();
    }
  });
  
  var arrParams = {};
  getAlmacenes(arrParams);

  url = base_url + 'HelperController/getTiposDocumentos';
  $.post( url, {Nu_Tipo_Filtro : 3}, function( response ){
    $( '#cbo-filtros_tipos_documento' ).html('<option value="0" selected="selected">Todos</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-filtros_tipos_documento' ).append( '<option value="' + response[i].ID_Tipo_Documento + '">' + response[i].No_Tipo_Documento_Breve + '</option>' );
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
          sTipoSerie = '(' + ( response[i].ID_POS > 0 ? 'Punto Venta' : 'Oficina' ) + ')';
          $( '#cbo-filtros_series_documento' ).append( '<option value="' + response[i].ID_Serie_Documento + '">' + response[i].ID_Serie_Documento + ' ' + sTipoSerie + '</option>' );
        }
      }, 'JSON');
	  }
  })

  url = base_url + 'HelperController/getDataGeneral';
  $.post(url, { sTipoData: 'categoria' }, function (response) {
    if (response.sStatus == 'success') {
      var iTotalRegistros = response.arrData.length, response = response.arrData;
      $('#cbo-familia').html('<option value="0" selected="selected">- Todos -</option>');
      for (var i = 0; i < iTotalRegistros; i++)
        $('#cbo-familia').append('<option value="' + response[i].ID + '">' + response[i].Nombre + '</option>');
    } else {
      $('#cbo-familia').html('<option value="0" selected="selected">- Vacío -</option>');
      console.log(response);
    }
    $('#modal-loader').modal('hide');
  }, 'JSON');

  $('#cbo-sub_categoria').html('<option value="0" selected="selected">- Todos -</option>');
  $('#cbo-familia').change(function () {
    url = base_url + 'HelperController/getDataGeneral';
    var arrParams = {
      sTipoData: 'subcategoria',
      sWhereIdCategoria: $(this).val(),
    }
    $.post(url, arrParams, function (response) {
      $('#cbo-sub_categoria').html('<option value="0" selected="selected">- No hay registros -</option>');
      if (response.sStatus == 'success') {
        $('#cbo-sub_categoria').html('<option value="0" selected="selected">- Todos -</option>');
        var l = response.arrData.length;
        if (l == 1) {
          $('#cbo-sub_categoria').append('<option value="' + response.arrData[0].ID + '">' + response.arrData[0].Nombre + '</option>');
        } else {
          for (var x = 0; x < l; x++) {
            $('#cbo-sub_categoria').append('<option value="' + response.arrData[x].ID + '">' + response.arrData[x].Nombre + '</option>');
          }
        }
      } else {
        if (response.sMessageSQL !== undefined) {
          console.log(response.sMessageSQL);
        }
        if (response.sStatus != 'warning') {
          $('#modal-message').modal('show');
          $('.modal-message').addClass(response.sClassModal);
          $('.modal-title-message').text(response.sMessage);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1200);
        }
      }
    }, 'JSON');
  });

  $('#cbo-marca').html('<option value="0">- Todos -</option>');
  url = base_url + 'HelperController/getMarcas';
  $.post(url, function (responseMarcas) {
    $('#cbo-marca').html('<option value="0">- Todos -</option>');
    if (responseMarcas.length == 1) {
      $('#cbo-marca').append('<option value="' + responseMarcas[0].ID_Marca + '">' + responseMarcas[0].No_Marca + '</option>');
    } else {
      for (var i = 0; i < responseMarcas.length; i++)
        $('#cbo-marca').append('<option value="' + responseMarcas[i].ID_Marca + '">' + responseMarcas[i].No_Marca + '</option>');
    }
  }, 'JSON');

  $('#cbo-lista_precios').html('<option value="0" selected="selected">- Todos -</option>');
  url = base_url + 'HelperController/getListaPrecio';
  $.post(url, { Nu_Tipo_Lista_Precio: 1, ID_Organizacion: $('#header-a-id_organizacion').val(), ID_Almacen: $('#cbo-almacen').val() }, function (responseLista) {
    if (responseLista.length == 1) {
      $('#cbo-lista_precios').append('<option value="' + responseLista[0].ID_Lista_Precio_Cabecera + '">' + responseLista[0].No_Lista_Precio + '</option>');
    } else {
      $('#cbo-lista_precios').html('<option value="0" selected="selected">- Todos -</option>');
      for (var i = 0; i < responseLista.length; i++)
        $('#cbo-lista_precios').append('<option value="' + responseLista[i].ID_Lista_Precio_Cabecera + '">' + responseLista[i].No_Lista_Precio + '</option>');
    }
  }, 'JSON');

  $('#cbo-transporte').html('<option value="0" selected="selected">- Todos -</option>');
  url = base_url + 'HelperController/getDeliveryVentas';
  var arrPost = {};
  $.post(url, arrPost, function (response) {
    $('#cbo-transporte').html('<option value="0" selected="selected">- Todos -</option>');
    if (response.sStatus == 'success') {
      var l = response.arrData.length;
      if (l == 1) {
        $('#cbo-transporte').append('<option value="' + response.arrData[0].ID + '">' + response.arrData[0].Nombre + '</option>');
      } else {
        for (var x = 0; x < l; x++) {
          $('#cbo-transporte').append('<option value="' + response.arrData[x].ID + '">' + response.arrData[x].Nombre + '</option>');
        }
      }
    } else {
      if (response.sMessageSQL !== undefined) {
        console.log(response.sMessageSQL);
      }
    }
  }, 'JSON');

  // Obtener canales de venta
  url = base_url + 'HelperController/getCanalesVenta';
  $.post(url, function (response) {
    if (response.sStatus == 'success') {
      var l = response.arrData.length;
      if (l == 1) {
        $('#cbo-canal_venta').html('<option value="' + response.arrData[0].ID + '">' + response.arrData[0].Nombre + '</option>');
      } else {
        $('#cbo-canal_venta').html('<option value="0" selected="selected">- Todos -</option>');
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


 $(document).on("click",".btn-download",function(){
    window.open(base_url + 'Ventas/informes_venta/VentasDetalladasGeneralesController/BajarReporte/'+$(this).data("valor"), "_blank");
  });

  $(document).on("click",".btn-cancelar",function(){
    url = base_url + 'Ventas/informes_venta/VentasDetalladasGeneralesController/CancelarReporte';
    $.post( url, {"ID_Reporte":$(this).data("valor")}, function( response ){
      ReloadReporte();
    },"json");
  });

  

$( '#btn-reload' ).click(ReloadReporte);
$( '#btn-reload' ).trigger("click");

 $("#btn-generar").click(function(){

     var ID_Almacen, Fe_Inicio, Fe_Fin, ID_Tipo_Documento, ID_Serie_Documento, ID_Numero_Documento, Nu_Estado_Documento, iIdCliente, sNombreCliente, iIdItem, sNombreItem, iTipoVenta, Nu_Tipo_Recepcion, Nu_Estado_Despacho_Pos, ID_Transporte_Delivery, ID_Lista_Precio_Cabecera, ID_Canal_Venta_Tabla_Dato, Nu_Tipo_Impuesto;
    
    Fe_Inicio = ParseDateString($( '#txt-Filtro_Fe_Inicio' ).val(), 1, '/');
    Fe_Fin = ParseDateString($( '#txt-Filtro_Fe_Fin' ).val(), 1, '/');
    ID_Tipo_Documento = $( '#cbo-filtros_tipos_documento' ).val();
    ID_Serie_Documento = $( '#cbo-filtros_series_documento' ).val();
    ID_Numero_Documento = ($( '#txt-Filtro_NumeroDocumento' ).val().length == 0 ? '-' : $( '#txt-Filtro_NumeroDocumento' ).val());
    Nu_Estado_Documento = $( '#cbo-estado_documento' ).val();
    iIdCliente = ($( '#txt-AID' ).val().length === 0 ? '-' : $( '#txt-AID' ).val());
    sNombreCliente = ($( '#txt-Filtro_Entidad' ).val().length === 0 ? '-' : $( '#txt-Filtro_Entidad' ).val());
    iIdItem = ($( '#txt-ID_Producto' ).val().length === 0 ? '-' : $( '#txt-ID_Producto' ).val());
    sNombreItem = ($( '#txt-No_Producto' ).val().length === 0 ? '-' : $( '#txt-No_Producto' ).val());
    iTipoVenta = $( '#cbo-tipo_venta' ).val();
    ID_Familia = $('#cbo-familia').val();
    ID_Sub_Familia = $('#cbo-sub_categoria').val();
    ID_Marca = $('#cbo-marca').val();
    Nu_Tipo_Recepcion = $('#cbo-tipo_recepcion_cliente').val();
    Nu_Estado_Despacho_Pos = $('#cbo-tipo_estado_pedido').val();
    ID_Transporte_Delivery = $('#cbo-transporte').val();
    ID_Lista_Precio_Cabecera = $('#cbo-lista_precios').val();
    ID_Canal_Venta_Tabla_Dato = $('#cbo-canal_venta').val();
    ID_Almacen = $('#cbo-Almacenes_VentasDetalladasGenerales').val();
    Nu_Tipo_Impuesto = $('#cbo-regalo').val();
    Nu_Tipo_Formato = $("input[name='Nu_Tipo_Formato']:checked").val();

    var arrPost = {
        Fe_Inicio : Fe_Inicio,
        Fe_Fin : Fe_Fin,
        ID_Tipo_Documento : ID_Tipo_Documento,
        ID_Serie_Documento : ID_Serie_Documento,
        ID_Numero_Documento : ID_Numero_Documento,
        Nu_Estado_Documento : Nu_Estado_Documento,
        iIdCliente : iIdCliente,
        sNombreCliente : sNombreCliente,
        iIdItem : iIdItem,
        sNombreItem : sNombreItem,
        iTipoVenta : iTipoVenta,
        ID_Familia: ID_Familia,
        ID_Sub_Familia: ID_Sub_Familia,
        ID_Marca: ID_Marca,
        Nu_Tipo_Recepcion: Nu_Tipo_Recepcion,
        Nu_Estado_Despacho_Pos: Nu_Estado_Despacho_Pos,
        ID_Transporte_Delivery: ID_Transporte_Delivery,
        ID_Lista_Precio_Cabecera: ID_Lista_Precio_Cabecera,
        ID_Canal_Venta_Tabla_Dato: ID_Canal_Venta_Tabla_Dato,
        Nu_Tipo_Impuesto:Nu_Tipo_Impuesto,
        ID_Almacen: ID_Almacen,
        Nu_Tipo_Formato:Nu_Tipo_Formato
      };

      url = base_url + 'Ventas/informes_venta/VentasDetalladasGeneralesController/CrearReporte';
        // window.open(url,'_blank');
        //arrPost.sNombreItem = encodeURIComponent(sNombreItem);
        // $( '#btn-excel_ventas_detalladas_generales' ).text('');
        // $( '#btn-excel_ventas_detalladas_generales' ).append( '<i class="fa fa-file-excel-o color_white"></i> Excel' );
        // $( '#btn-excel_ventas_detalladas_generales' ).attr('disabled', false);

        $.post( url, arrPost, function( data ) {
           ReloadReporte();
           $('#modal-venta').modal('show');
           $( '#btn-generar' ).text('');
           $( '#btn-generar' ).attr('disabled', false);
           $( '#btn-generar' ).append( 'Generar Reporte' );
        }, "json");


  });

  $( '.btn-generar_ventas_detalladas_generales' ).click(function(){    
    var ID_Almacen, Fe_Inicio, Fe_Fin, ID_Tipo_Documento, ID_Serie_Documento, ID_Numero_Documento, Nu_Estado_Documento, iIdCliente, sNombreCliente, iIdItem, sNombreItem, iTipoVenta, Nu_Tipo_Recepcion, Nu_Estado_Despacho_Pos, ID_Transporte_Delivery, ID_Lista_Precio_Cabecera, ID_Canal_Venta_Tabla_Dato, Nu_Tipo_Impuesto;
    
    Fe_Inicio = ParseDateString($( '#txt-Filtro_Fe_Inicio' ).val(), 1, '/');
    Fe_Fin = ParseDateString($( '#txt-Filtro_Fe_Fin' ).val(), 1, '/');
    ID_Tipo_Documento = $( '#cbo-filtros_tipos_documento' ).val();
    ID_Serie_Documento = $( '#cbo-filtros_series_documento' ).val();
    ID_Numero_Documento = ($( '#txt-Filtro_NumeroDocumento' ).val().length == 0 ? '-' : $( '#txt-Filtro_NumeroDocumento' ).val());
    Nu_Estado_Documento = $( '#cbo-estado_documento' ).val();
    iIdCliente = ($( '#txt-AID' ).val().length === 0 ? '-' : $( '#txt-AID' ).val());
    sNombreCliente = ($( '#txt-Filtro_Entidad' ).val().length === 0 ? '-' : $( '#txt-Filtro_Entidad' ).val());
    iIdItem = ($( '#txt-ID_Producto' ).val().length === 0 ? '-' : $( '#txt-ID_Producto' ).val());
    sNombreItem = ($( '#txt-No_Producto' ).val().length === 0 ? '-' : $( '#txt-No_Producto' ).val());
    iTipoVenta = $( '#cbo-tipo_venta' ).val();
    ID_Familia = $('#cbo-familia').val();
    ID_Sub_Familia = $('#cbo-sub_categoria').val();
    ID_Marca = $('#cbo-marca').val();
    Nu_Tipo_Recepcion = $('#cbo-tipo_recepcion_cliente').val();
    Nu_Estado_Despacho_Pos = $('#cbo-tipo_estado_pedido').val();
    ID_Transporte_Delivery = $('#cbo-transporte').val();
    ID_Lista_Precio_Cabecera = $('#cbo-lista_precios').val();
    ID_Canal_Venta_Tabla_Dato = $('#cbo-canal_venta').val();
    ID_Almacen = $('#cbo-Almacenes_VentasDetalladasGenerales').val();
    Nu_Tipo_Impuesto = $('#cbo-regalo').val();

    if ($(this).data('type') == 'html') {
      $( '#btn-html_ventas_detalladas_generales' ).text('');
      $( '#btn-html_ventas_detalladas_generales' ).attr('disabled', true);
      $( '#btn-html_ventas_detalladas_generales' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
  
      $( '#table-ventas_detalladas_generales > tbody' ).empty();
      $( '#table-ventas_detalladas_generales > tfoot' ).empty();

      var arrPost = {
        Fe_Inicio : Fe_Inicio,
        Fe_Fin : Fe_Fin,
        ID_Tipo_Documento : ID_Tipo_Documento,
        ID_Serie_Documento : ID_Serie_Documento,
        ID_Numero_Documento : ID_Numero_Documento,
        Nu_Estado_Documento : Nu_Estado_Documento,
        iIdCliente : iIdCliente,
        sNombreCliente : sNombreCliente,
        iIdItem : iIdItem,
        sNombreItem : sNombreItem,
        iTipoVenta : iTipoVenta,
        ID_Familia: ID_Familia,
        ID_Sub_Familia: ID_Sub_Familia,
        ID_Marca: ID_Marca,
        Nu_Tipo_Recepcion: Nu_Tipo_Recepcion,
        Nu_Estado_Despacho_Pos: Nu_Estado_Despacho_Pos,
        ID_Transporte_Delivery: ID_Transporte_Delivery,
        ID_Lista_Precio_Cabecera: ID_Lista_Precio_Cabecera,
        ID_Canal_Venta_Tabla_Dato: ID_Canal_Venta_Tabla_Dato,
        Nu_Tipo_Impuesto:Nu_Tipo_Impuesto,
        ID_Almacen: ID_Almacen,
      };
      url = base_url + 'Ventas/informes_venta/VentasDetalladasGeneralesController/sendReporte';
      $.post( url, arrPost, function( response ){
        if ( response.sStatus == 'success' ) {
          var iTotalRegistros = response.arrData.length, tr_body = '', tr_foot = '';
          var fCantidadItem = 0.00, fPrecioItem = 0.00, fSubtotalItem = 0.00, fImpuestoItem = 0.00, fTotalItem = 0.00, fTotalDescuentoItem = 0.00, fTotalDescuento = 0.00;
          var fCantidadTotalGeneral = 0.00, fSubtotalGeneral = 0.00, fImpuestoGeneral = 0.00, fTotalGeneral = 0.00, fTotalDescuentoItemGeneral = 0.00, fTotalDescuentoGeneral = 0.00;
          var $ID_Almacen = 0, $counter_almacen = 0, $fCantidadTotalGeneralAlmacen = 0.00, $fSubtotalGeneralAlmacen = 0.00, $fImpuestoGeneralAlmacen = 0.00, $fTotalGeneralAlmacen = 0.00, $fTotalDescuentoItemAlmacen = 0.00, $fTotalDescuentoAlmacen = 0.00;
          var response=response.arrData;
          for (var i = 0; i < iTotalRegistros; i++) {
            fCantidadItem = (!isNaN(parseFloat(response[i].Qt_Producto)) ? parseFloat(response[i].Qt_Producto) : 0);
            fPrecioItem = (!isNaN(parseFloat(response[i].Ss_Precio)) ? parseFloat(response[i].Ss_Precio) : 0);
            fSubtotalItem = (!isNaN(parseFloat(response[i].Ss_Subtotal)) ? parseFloat(response[i].Ss_Subtotal) : 0);
            fImpuestoItem = (!isNaN(parseFloat(response[i].Ss_Impuesto)) ? parseFloat(response[i].Ss_Impuesto) : 0);
            fTotalItem = (!isNaN(parseFloat(response[i].Ss_Total)) ? parseFloat(response[i].Ss_Total) : 0);
            fTotalDescuentoItem = (!isNaN(parseFloat(response[i].Ss_Descuento_Producto)) ? parseFloat(response[i].Ss_Descuento_Producto) : 0);
            fTotalDescuento = (!isNaN(parseFloat(response[i].Ss_Descuento_Global)) ? parseFloat(response[i].Ss_Descuento_Global) : 0);

            if ($ID_Almacen != response[i].ID_Almacen) {
              if ($counter_almacen != 0) {
                tr_body += "<tr>"
                + "<th class='text-right' colspan='18'>Total Almacén</th>"
                  + "<th class='text-right'>" + number_format($fCantidadTotalGeneralAlmacen, 2) + "</th>"
                  + "<th class='text-right'>&nbsp;</th>"
                  + "<th class='text-right'>&nbsp;</th>"
                  + "<th class='text-right'>" + number_format($fSubtotalGeneralAlmacen, 2) + "</th>"
                  + "<th class='text-right'>" + number_format($fImpuestoGeneralAlmacen, 2) + "</th>"
                  + "<th class='text-right'>" + number_format($fTotalGeneralAlmacen, 2) + "</th>"
                  +"<th class='text-right' colspan='18'>&nbsp;</th>"
                  + "<th class='text-right'>" + number_format($fTotalDescuentoItemAlmacen, 2) + "</th>"
                  + "<th class='text-right'>" + number_format($fTotalDescuentoAlmacen, 2) + "</th>"
                + "</tr>";
              }

              $fCantidadTotalGeneralAlmacen = 0.00;
              $fSubtotalGeneralAlmacen = 0.00;
              $fImpuestoGeneralAlmacen = 0.00;
              $fTotalGeneralAlmacen = 0.00;
              $fTotalDescuentoItemAlmacen = 0.00;
              $fTotalDescuentoAlmacen = 0.00;

              tr_body +=
                "<tr>"
                + "<th class='text-right'><span style='font-size: 15px;'>Almacén</span></th>"
                + "<th class='text-left' colspan='34'><span style='font-size: 15px;'>" + response[i].No_Almacen + "</span></th>"
                + "</tr>";

              $ID_Almacen = response[i].ID_Almacen;
            }// if almacen

            tr_body +=
            "<tr>"
              +"<td class='text-center'>" + response[i].Fe_Emision_Hora + "</td>"
              +"<td class='text-center'>" + response[i].Fe_Hora + "</td>"
              +"<td class='text-left'>" + response[i].No_Empleado + "</td>"
              +"<td class='text-center'>" + response[i].No_Tipo_Documento_Breve + "</td>"
              +"<td class='text-center'>" + response[i].ID_Serie_Documento + "</td>"
              +"<td class='text-center'>" + response[i].ID_Numero_Documento + "</td>"
              +"<td class='text-center'>" + response[i].No_Tipo_Documento_Identidad_Breve + "</td>"
              +"<td class='text-left'>" + response[i].Nu_Documento_Identidad + "</td>"
              +"<td class='text-left'>" + response[i].No_Entidad + "</td>"
              +"<td class='text-center'>" + response[i].No_Signo + "</td>"
              +"<td class='text-right'>" + number_format(response[i].Ss_Tipo_Cambio, 3) + "</td>"
              +"<td class='text-left'>" + response[i].No_Marca + "</td>"
              +"<td class='text-left'>" + response[i].No_Familia + "</td>"
              +"<td class='text-left'>" + response[i].No_Sub_Familia + "</td>"
              +"<td class='text-left'>" + response[i].No_Unidad_Medida + "</td>"
              +"<td class='text-left'>" + response[i].Nu_Codigo_Barra + "</td>"
              +"<td class='text-left'>" + response[i].No_Producto + "</td>"
              +"<td class='text-left'>" + response[i].Txt_Nota_Item + "</td>"
              +"<td class='text-right'>" + (response[i].ID_Tipo_Documento != 5 ? '' : '-') + number_format(fCantidadItem, 2) + "</td>"
              +"<td class='text-left'>" + response[i].Qt_CO2_Producto + "</td>"
              +"<td class='text-right'>" + (response[i].ID_Tipo_Documento != 5 ? '' : '-') + number_format(fPrecioItem, 2) + "</td>"
              +"<td class='text-right'>" + (response[i].ID_Tipo_Documento != 5 ? '' : '-') + number_format(fSubtotalItem, 2) + "</td>"
              +"<td class='text-right'>" + (response[i].ID_Tipo_Documento != 5 ? '' : '-') + number_format(fImpuestoItem, 2) + "</td>"
              +"<td class='text-right'>" + (response[i].ID_Tipo_Documento != 5 ? '' : '-') + number_format(fTotalItem, 2) + "</td>"
              +"<td class='text-left'>" + response[i].No_Tipo_Recepcion + "</td>"
              +"<td class='text-left'>" + response[i].No_Delivery + "</td>"
              +"<td class='text-left'>" + response[i].Fe_Entrega + "</td>"
              +"<td class='text-center'><span class='label label-" + response[i].No_Class_Estado_Delivery + "'>" + response[i].No_Estado_Delivery + "</span></td>"
              +"<td class='text-left'>" + response[i].No_Tipo_Documento_Breve_Guia + "</td>"
              +"<td class='text-left'>" + response[i].ID_Serie_Documento_Guia + "</td>"
              +"<td class='text-left'>" + response[i].ID_Numero_Documento_Guia + "</td>"
              +"<td class='text-left'>" + response[i].Txt_Nota + "</td>"
              +"<td class='text-left'>" + response[i].No_Lista_Precio + "</td>"
              +"<td class='text-left'>" + response[i].No_Canal_Venta + "</td>"
              +"<td class='text-left'>" + response[i].No_Orden_Compra_FE + "</td>"
              +"<td class='text-left'>" + response[i].No_Placa_FE + "</td>"
              +"<td class='text-left'>" + response[i].Txt_Direccion_Entidad + "</td>"
              +"<td class='text-left'>" + response[i].No_Distrito + "</td>"
              +"<td class='text-left'>" + response[i].No_Provincia + "</td>"
              +"<td class='text-left'>" + response[i].No_Departamento + "</td>"
              +"<td class='text-left'>" + response[i].Nu_Celular_Entidad + "</td>"
              +"<td class='text-left'>" + response[i].Txt_Email_Entidad + "</td>"
              +"<td class='text-right'>" + (response[i].ID_Tipo_Documento != 5 ? '' : '-') + number_format(fTotalDescuentoItem, 2) + "</td>"
              +"<td class='text-right'>" + (response[i].ID_Tipo_Documento != 5 ? '' : '-') + number_format(fTotalDescuento, 2) + "</td>"
              +"<td class='text-center'><span class='label label-" + response[i].No_Class_Estado + "'>" + response[i].No_Estado + "</span></td>"
            +"</tr>";
            
            fCantidadTotalGeneral += fCantidadItem;
            fSubtotalGeneral += fSubtotalItem;
            fImpuestoGeneral += fImpuestoItem;
            fTotalGeneral += fTotalItem;
            fTotalDescuentoItemGeneral += fTotalDescuentoItem;
            fTotalDescuentoGeneral += fTotalDescuento;

            $fCantidadTotalGeneralAlmacen += fCantidadItem;
            $fSubtotalGeneralAlmacen += fSubtotalItem;
            $fImpuestoGeneralAlmacen += fImpuestoItem;
            $fTotalGeneralAlmacen += fTotalItem;
            $fTotalDescuentoItemAlmacen += fTotalDescuentoItem;
            $fTotalDescuentoAlmacen += fTotalDescuento;

            $counter_almacen++;
          }
          
          tr_foot =
          "<tfoot>"
            +"<tr>"
              +"<th class='text-right' colspan='18'>Total Almacén</th>"
              + "<th class='text-right'>" + number_format($fCantidadTotalGeneralAlmacen, 2) + "</th>"
              +"<th class='text-right'>&nbsp;</th>"
              +"<th class='text-right'>&nbsp;</th>"
              + "<th class='text-right'>" + number_format($fSubtotalGeneralAlmacen, 2) + "</th>"
              + "<th class='text-right'>" + number_format($fImpuestoGeneralAlmacen, 2) + "</th>"
              + "<th class='text-right'>" + number_format($fTotalGeneralAlmacen, 2) + "</th>"
              +"<th class='text-right' colspan='18'>&nbsp;</th>"
              + "<th class='text-right'>" + number_format($fTotalDescuentoItemAlmacen, 2) + "</th>"
              + "<th class='text-right'>" + number_format($fTotalDescuentoAlmacen, 2) + "</th>"
            + "</tr>"
            + "<tr>"
              + "<th class='text-right' colspan='18'>Total</th>"
              + "<th class='text-right'>" + number_format(fCantidadTotalGeneral, 2) + "</th>"
              + "<th class='text-right'>&nbsp;</th>"
              + "<th class='text-right'>&nbsp;</th>"
              + "<th class='text-right'>" + number_format(fSubtotalGeneral, 2) + "</th>"
              + "<th class='text-right'>" + number_format(fImpuestoGeneral, 2) + "</th>"
              + "<th class='text-right'>" + number_format(fTotalGeneral, 2) + "</th>"
              +"<th class='text-right' colspan='18'>&nbsp;</th>"
              + "<th class='text-right'>" + number_format(fTotalDescuentoItemGeneral, 2) + "</th>"
              + "<th class='text-right'>" + number_format(fTotalDescuentoGeneral, 2) + "</th>"
            + "</tr>"
          +"</tfoot>";
        } else {
          if( response.sMessageSQL !== undefined ) {
            console.log(response.sMessageSQL);
          }
          tr_body +=
          "<tr>"
            +"<td colspan='27' class='text-center'>" + response.sMessage + "</td>"
          + "</tr>";
        } // ./ if arrData
        
        $( '#div-ventas_detalladas_generales' ).show();
        $( '#table-ventas_detalladas_generales > tbody' ).append(tr_body);
        $( '#table-ventas_detalladas_generales > tbody' ).after(tr_foot);
        
        $( '#btn-html_ventas_detalladas_generales' ).text('');
        $( '#btn-html_ventas_detalladas_generales' ).append( '<i class="fa fa-search"></i> Buscar' );
        $( '#btn-html_ventas_detalladas_generales' ).attr('disabled', false);
      }, 'JSON')
      .fail(function(jqXHR, textStatus, errorThrown) {
        $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
        
        $( '#modal-message' ).modal('show');
        $( '.modal-message' ).addClass( 'modal-danger' );
        $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
        setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
        
        //Message for developer
        console.log(jqXHR.responseText);
        
        $( '#btn-html_ventas_detalladas_generales' ).text('');
        $( '#btn-html_ventas_detalladas_generales' ).append( '<i class="fa fa-search"></i> Buscar' );
        $( '#btn-html_ventas_detalladas_generales' ).attr('disabled', false);
      });
    } else if ($(this).data('type') == 'pdf') {
      $( '#btn-pdf_ventas_detalladas_generales' ).text('');
      $( '#btn-pdf_ventas_detalladas_generales' ).attr('disabled', true);
      $( '#btn-pdf_ventas_detalladas_generales' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
          
      url = base_url + 'Ventas/informes_venta/VentasDetalladasGeneralesController/sendReportePDF/' + Fe_Inicio + '/' + Fe_Fin + '/' + ID_Tipo_Documento + '/' + ID_Serie_Documento + '/' + ID_Numero_Documento + '/' + Nu_Estado_Documento + '/' + iIdCliente + '/' + encodeURIComponent(sNombreCliente) + '/' + iIdItem + '/' + encodeURIComponent(sNombreItem) + '/' + iTipoVenta + '/' + ID_Familia + '/' + ID_Sub_Familia + '/' + ID_Marca + '/' + Nu_Tipo_Recepcion + '/' + Nu_Estado_Despacho_Pos + '/' + ID_Transporte_Delivery + '/' + ID_Lista_Precio_Cabecera + '/' + ID_Canal_Venta_Tabla_Dato + '/' + ID_Almacen  + '/' + Nu_Tipo_Impuesto;
      window.open(url,'_blank');
      
      $( '#btn-pdf_ventas_detalladas_generales' ).text('');
      $( '#btn-pdf_ventas_detalladas_generales' ).append( '<i class="fa fa-file-pdf-o color_icon_pdf"></i> PDF' );
      $( '#btn-pdf_ventas_detalladas_generales' ).attr('disabled', false);
    } else if ($(this).data('type') == 'excel') {
      $( '#btn-excel_ventas_detalladas_generales' ).text('');
      $( '#btn-excel_ventas_detalladas_generales' ).attr('disabled', true);
      $( '#btn-excel_ventas_detalladas_generales' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
      
      url = base_url + 'Ventas/informes_venta/VentasDetalladasGeneralesController/sendReporteEXCEL/' + Fe_Inicio + '/' + Fe_Fin + '/' + ID_Tipo_Documento + '/' + ID_Serie_Documento + '/' + ID_Numero_Documento + '/' + Nu_Estado_Documento + '/' + iIdCliente + '/' + encodeURIComponent(sNombreCliente) + '/' + iIdItem + '/' + encodeURIComponent(sNombreItem) + '/' + iTipoVenta + '/' + ID_Familia + '/' + ID_Sub_Familia + '/' + ID_Marca + '/' + Nu_Tipo_Recepcion + '/' + Nu_Estado_Despacho_Pos + '/' + ID_Transporte_Delivery + '/' + ID_Lista_Precio_Cabecera + '/' + ID_Canal_Venta_Tabla_Dato + '/' + ID_Almacen + '/' + Nu_Tipo_Impuesto;
      window.open(url,'_blank');
      
      $( '#btn-excel_ventas_detalladas_generales' ).text('');
      $( '#btn-excel_ventas_detalladas_generales' ).append( '<i class="fa fa-file-excel-o color_icon_excel"></i> Excel' );
      $( '#btn-excel_ventas_detalladas_generales' ).attr('disabled', false);
    }// /. if
  })// /. btn
})

// Ayudas - combobox
function getAlmacenes(arrParams) {
  $('#cbo-Almacenes_VentasDetalladasGenerales').html('<option value="0">- Todos -</option>');
  url = base_url + 'HelperController/getAlmacenes';
  $.post(url, {}, function (responseAlmacen) {
    var iCantidadRegistros = responseAlmacen.length;
    var selected = '';
    var iIdAlmacen = 0;
    $('#cbo-Almacenes_VentasDetalladasGenerales').html('<option value="0">- Todos -</option>');
    if (iCantidadRegistros == 1) {
      if (arrParams !== undefined) {
        iIdAlmacen = arrParams.ID_Almacen;
      }
      if (iIdAlmacen == responseAlmacen[0]['ID_Almacen']) {
        selected = 'selected="selected"';
      }
      $('#cbo-Almacenes_VentasDetalladasGenerales').append('<option value="' + responseAlmacen[0]['ID_Almacen'] + '" ' + selected + ' data-direccion_almacen="' + responseAlmacen[0]['Txt_Direccion_Almacen'] + '">' + responseAlmacen[0]['No_Almacen'] + '</option>');
    } else {
      for (var i = 0; i < iCantidadRegistros; i++) {
        if (arrParams !== undefined) {
          iIdAlmacen = arrParams.ID_Almacen;
        }
        if (iIdAlmacen == responseAlmacen[0]['ID_Almacen']) {
          selected = 'selected="selected"';
        }
        $('#cbo-Almacenes_VentasDetalladasGenerales').append('<option value="' + responseAlmacen[i]['ID_Almacen'] + '" ' + selected + ' data-direccion_almacen="' + responseAlmacen[0]['Txt_Direccion_Almacen'] + '">' + responseAlmacen[i]['No_Almacen'] + '</option>');
      }
    }
    $('#modal-loader').modal('hide');
  }, 'JSON');
}