var url;

$(function () {
  $('.select2').select2();
  $( '#modal-loader' ).modal('show');
  $( '#div-ventas_x_trabajador' ).hide();
   
  $('.div-mas_opciones').hide();
  $('#checkbox-mas_filtros').on('ifChanged', function(){
    $('.div-mas_opciones').hide();
    var _this = jQuery(this);
    if(_this.is(':checked')){
      $('.div-mas_opciones').show();
    }
  });

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

  $( '.btn-generar_ventas_x_trabajador' ).click(function(){
    var Fe_Inicio, Fe_Fin, ID_Tipo_Documento, ID_Serie_Documento, ID_Numero_Documento, Nu_Estado_Documento, iIdEmpleado, sNombreEmpleado, iIdItem, sNombreItem, iTipoReporte, ID_Transporte_Delivery, Nu_Estado_Despacho_Pos, Nu_Tipo_Impuesto;
    
    Fe_Inicio = ParseDateString($( '#txt-Filtro_Fe_Inicio' ).val(), 1, '/');
    Fe_Fin = ParseDateString($( '#txt-Filtro_Fe_Fin' ).val(), 1, '/');
    ID_Tipo_Documento = $( '#cbo-filtros_tipos_documento' ).val();
    ID_Serie_Documento = $( '#cbo-filtros_series_documento' ).val();
    ID_Numero_Documento = ($( '#txt-Filtro_NumeroDocumento' ).val().length == 0 ? '-' : $( '#txt-Filtro_NumeroDocumento' ).val());
    Nu_Estado_Documento = $( '#cbo-estado_documento' ).val();
    iIdEmpleado = '-';
    sNombreEmpleado = '-';
    iIdItem = ($( '#txt-ID_Producto' ).val().length === 0 ? '-' : $( '#txt-ID_Producto' ).val());
    sNombreItem = ($( '#txt-No_Producto' ).val().length === 0 ? '-' : $( '#txt-No_Producto' ).val());
    iTipoReporte = $('[name="radio-tipo-reporte-ventas_x_trabajador"]:checked').attr('value');
    ID_Transporte_Delivery = $('#cbo-transporte').val();
    Nu_Estado_Despacho_Pos = $('#cbo-tipo_estado_pedido').val();
    Nu_Tipo_Impuesto = $('#cbo-regalo').val();
      
    if ($(this).data('type') == 'html') {
      $( '#btn-html_ventas_x_trabajador' ).text('');
      $( '#btn-html_ventas_x_trabajador' ).attr('disabled', true);
      $( '#btn-html_ventas_x_trabajador' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
  
      $( '#table-ventas_x_trabajador > tbody' ).empty();
      $( '#table-ventas_x_trabajador > tfoot' ).empty();

      var arrPost = {
        Fe_Inicio : Fe_Inicio,
        Fe_Fin : Fe_Fin,
        ID_Tipo_Documento : ID_Tipo_Documento,
        ID_Serie_Documento : ID_Serie_Documento,
        ID_Numero_Documento : ID_Numero_Documento,
        Nu_Estado_Documento : Nu_Estado_Documento,
        iIdEmpleado : iIdEmpleado,
        sNombreEmpleado : sNombreEmpleado,
        iIdItem : iIdItem,
        sNombreItem : sNombreItem,
        ID_Transporte_Delivery: ID_Transporte_Delivery,
        Nu_Estado_Despacho_Pos: Nu_Estado_Despacho_Pos,
        Nu_Tipo_Impuesto:Nu_Tipo_Impuesto
      };      
      url = base_url + 'Ventas/informes_venta/VentasXDeliveryController/sendReporte';
      $.post( url, arrPost, function( response ){
        if ( response.sStatus == 'success' ) {
          var iTotalRegistros = response.arrData.length, response=response.arrData, tr_body = '', tr_foot = '';
          var ID_Entidad = '', counter = 0, sum_cantidad = 0.000000, sum_subtotal_s = 0.00, sum_descuento_s = 0.00, sum_igv_s = 0.00, sum_total_s = 0.00, sum_total_d = 0.00;
          var subtotal_s = 0.00, descuento_s = 0.00, igv_s = 0.00, total_s = 0.00;
          var sum_general_cantidad = 0.000000, sum_general_subtotal_s = 0.00, sum_general_descuento_s = 0.00, sum_general_igv_s = 0.00, sum_general_total_s = 0.00, sum_general_total_d = 0.00;
          for (var i = 0; i < iTotalRegistros; i++) {
            if (ID_Entidad != response[i].ID_Entidad) {
              if (counter != 0) {
                tr_body +=
                +"<tr>"
                  +"<th class='text-right' colspan='7'>Total </th>"
                  +"<th class='text-right'>" + number_format(sum_cantidad, 6) + "</th>"
                  +"<th class='text-right'></th>"
                  +"<th class='text-right'>" + number_format(sum_subtotal_s, 2) + "</th>"
                  +"<th class='text-right'>" + number_format(sum_igv_s, 2) + "</th>"
                  +"<th class='text-right'>" + number_format(sum_descuento_s, 2) + "</th>"
                  +"<th class='text-right'>" + number_format(sum_total_s, 2) + "</th>"
                  +"<th class='text-right'>" + number_format(sum_total_d, 2) + "</th>"
                  +"<th class='text-right'></th>"
                +"</tr>";0.000000;
                sum_cantidad = 0.000000;
                sum_subtotal_s = 0.00;
                sum_igv_s = 0.00;
                sum_descuento_s = 0.00;
                sum_total_s = 0.00;
                sum_total_d = 0.00;
              }
              
              tr_body +=
              "<tr>"
                +"<th class='text-center'>Delivery </th>"
                +"<th class='text-left'>" + response[i].Nu_Documento_Identidad + "</th>"
                +"<th class='text-left' colspan='13'>" + response[i].No_Entidad + "</th>"
              +"</tr>";
              ID_Entidad = response[i].ID_Entidad;
            }
            
            subtotal_s = (!isNaN(parseFloat(response[i].Ss_SubTotal)) ? parseFloat(response[i].Ss_SubTotal) : 0);
            igv_s = (!isNaN(parseFloat(response[i].Ss_IGV)) ? parseFloat(response[i].Ss_IGV) : 0);
            descuento_s = (!isNaN(parseFloat(response[i].Ss_Descuento)) ? parseFloat(response[i].Ss_Descuento) : 0);
            total_s = (!isNaN(parseFloat(response[i].Ss_Total)) ? parseFloat(response[i].Ss_Total) : 0);
            
            if (iTipoReporte==0) {//0=Detallado
              tr_body +=
                "<tr>"
                  +"<td class='text-center'>" + response[i].Fe_Emision + "</td>"
                  +"<td class='text-center'>" + response[i].No_Tipo_Documento_Breve + "</td>"
                  +"<td class='text-center'>" + response[i].ID_Serie_Documento + "</td>"
                  +"<td class='text-right'>" + response[i].ID_Numero_Documento + "</td>"
                  +"<td class='text-center'>" + response[i].No_Signo + "</td>"
                  +"<td class='text-right'>" + number_format(response[i].Ss_Tipo_Cambio, 3) + "</td>"
                  +"<td class='text-left'>" + (response[i].Nu_Codigo_Barra != '' ? (response[i].Nu_Codigo_Barra + " - ") : "") + response[i].No_Producto + "</td>"
                  +"<td class='text-right'>" + (response[i].ID_Tipo_Documento != 5 ? '' : '-') + number_format(response[i].Qt_Producto, 6) + "</td>"
                  +"<td class='text-right'>" + (response[i].ID_Tipo_Documento != 5 ? '' : '-') + number_format(response[i].Ss_Precio, 6) + "</td>"
                  +"<td class='text-right'>" + (response[i].ID_Tipo_Documento != 5 ? '' : '-') + number_format(subtotal_s, 2) + "</td>"
                  +"<td class='text-right'>" + (response[i].ID_Tipo_Documento != 5 ? '' : '-') + number_format(igv_s, 2) + "</td>"
                  +"<td class='text-right'>" + (response[i].ID_Tipo_Documento != 5 ? '' : '-') + number_format(descuento_s, 2) + "</td>"
                  +"<td class='text-right'>" + (response[i].ID_Tipo_Documento != 5 ? '' : '-') + number_format(total_s, 2) + "</td>"
                  +"<td class='text-right'>" + (response[i].ID_Tipo_Documento != 5 ? '' : '-') + number_format(response[i].Ss_Total_Extranjero, 2) + "</td>"
                  +"<td class='text-center'><span class='label label-" + response[i].No_Class_Estado + "'>" + response[i].No_Estado + "</span></td>"
                  +"<td class='text-center'><span class='label label-" + response[i].No_Class_Estado_Delivery + "'>" + response[i].No_Estado_Delivery + "</span></td>"
                +"</tr>";
            }
            
            sum_cantidad += (!isNaN(parseFloat(response[i].Qt_Producto)) ? parseFloat(response[i].Qt_Producto) : 0);
            sum_subtotal_s += subtotal_s;
            sum_igv_s += igv_s;
            sum_descuento_s += descuento_s;
            sum_total_s += total_s;
            sum_total_d += parseFloat(response[i].Ss_Total_Extranjero);
            
            sum_general_cantidad += (!isNaN(parseFloat(response[i].Qt_Producto)) ? parseFloat(response[i].Qt_Producto) : 0);
            sum_general_subtotal_s += subtotal_s;
            sum_general_igv_s += igv_s;
            sum_general_descuento_s += descuento_s;
            sum_general_total_s += total_s;
            sum_general_total_d += parseFloat(response[i].Ss_Total_Extranjero);

            counter++;
          }
          
          tr_foot =
          "<tfoot>"
            +"<tr>"
              +"<th class='text-right' colspan='7'>Total</th>"
              +"<th class='text-right'>" + number_format(sum_cantidad, 6) + "</th>"
              +"<th class='text-right'></th>"
              +"<th class='text-right'>" + number_format(sum_subtotal_s, 2) + "</th>"
              +"<th class='text-right'>" + number_format(sum_igv_s, 2) + "</th>"
              +"<th class='text-right'>" + number_format(sum_descuento_s, 2) + "</th>"
              +"<th class='text-right'>" + number_format(sum_total_s, 2) + "</th>"
              +"<th class='text-right'>" + number_format(sum_total_d, 2) + "</th>"
              +"<th class='text-right'></th>"
            +"</tr>"
            +"<tr>"
              +"<th class='text-right' colspan='7'>Total General</th>"
              +"<th class='text-right'>" + number_format(sum_general_cantidad, 6) + "</th>"
              +"<th class='text-right'></th>"
              +"<th class='text-right'>" + number_format(sum_general_subtotal_s, 2) + "</th>"
              +"<th class='text-right'>" + number_format(sum_general_igv_s, 2) + "</th>"
              +"<th class='text-right'>" + number_format(sum_general_descuento_s, 2) + "</th>"
              +"<th class='text-right'>" + number_format(sum_general_total_s, 2) + "</th>"
              +"<th class='text-right'>" + number_format(sum_general_total_d, 2) + "</th>"
              +"<th class='text-right'></th>"
            +"</tr>"
          +"</tfoot>";
        } else {
          if( response.sMessageSQL !== undefined ) {
            console.log(response.sMessageSQL);
          }
          tr_body +=
          "<tr>"
            + "<td colspan='15' class='text-center'>No hay registros</td>"
          + "</tr>";
        }
        
        $( '#div-ventas_x_trabajador' ).show();
        $( '#table-ventas_x_trabajador > tbody' ).append(tr_body);
        $( '#table-ventas_x_trabajador > tbody' ).after(tr_foot);
        
        $( '#btn-html_ventas_x_trabajador' ).text('');
        $( '#btn-html_ventas_x_trabajador' ).append( '<i class="fa fa-search"></i> Buscar' );
        $( '#btn-html_ventas_x_trabajador' ).attr('disabled', false);
      }, 'JSON')
      .fail(function(jqXHR, textStatus, errorThrown) {
        $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
        
        $( '#modal-message' ).modal('show');
        $( '.modal-message' ).addClass( 'modal-danger' );
        $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
        setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
        
        //Message for developer
        console.log(jqXHR.responseText);
        
        $( '#btn-html_ventas_x_trabajador' ).text('');
        $( '#btn-html_ventas_x_trabajador' ).append( '<i class="fa fa-search"></i> Buscar' );
        $( '#btn-html_ventas_x_trabajador' ).attr('disabled', false);
      });
    } else if ($(this).data('type') == 'pdf') {
      $( '#btn-pdf_ventas_x_trabajador' ).text('');
      $( '#btn-pdf_ventas_x_trabajador' ).attr('disabled', true);
      $( '#btn-pdf_ventas_x_trabajador' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
          
      url = base_url + 'Ventas/informes_venta/VentasXDeliveryController/sendReportePDF/' + Fe_Inicio + '/' + Fe_Fin + '/' + ID_Tipo_Documento + '/' + ID_Serie_Documento + '/' + ID_Numero_Documento + '/' + Nu_Estado_Documento + '/' + iIdEmpleado + '/' + encodeURIComponent(sNombreEmpleado) + '/' + iIdItem + '/' + encodeURIComponent(sNombreItem) + '/' + iTipoReporte + '/' + ID_Transporte_Delivery + '/' + Nu_Estado_Despacho_Pos + '/' + Nu_Tipo_Impuesto;
      window.open(url,'_blank');
      
      $( '#btn-pdf_ventas_x_trabajador' ).text('');
      $( '#btn-pdf_ventas_x_trabajador' ).append( '<i class="fa fa-file-pdf-o color_icon_pdf"></i> PDF' );
      $( '#btn-pdf_ventas_x_trabajador' ).attr('disabled', false);
    } else if ($(this).data('type') == 'excel') {
      $( '#btn-excel_ventas_x_trabajador' ).text('');
      $( '#btn-excel_ventas_x_trabajador' ).attr('disabled', true);
      $( '#btn-excel_ventas_x_trabajador' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
      
      url = base_url + 'Ventas/informes_venta/VentasXDeliveryController/sendReporteEXCEL/' + Fe_Inicio + '/' + Fe_Fin + '/' + ID_Tipo_Documento + '/' + ID_Serie_Documento + '/' + ID_Numero_Documento + '/' + Nu_Estado_Documento + '/' + iIdEmpleado + '/' + encodeURIComponent(sNombreEmpleado) + '/' + iIdItem + '/' + encodeURIComponent(sNombreItem) + '/' + iTipoReporte + '/' + ID_Transporte_Delivery + '/' + Nu_Estado_Despacho_Pos + '/' + Nu_Tipo_Impuesto
      window.open(url,'_blank');
      
      $( '#btn-excel_ventas_x_trabajador' ).text('');
      $( '#btn-excel_ventas_x_trabajador' ).append( '<i class="fa fa-file-excel-o color_icon_excel"></i> Excel' );
      $( '#btn-excel_ventas_x_trabajador' ).attr('disabled', false);
    }// /. if button
  })// /. btn
})