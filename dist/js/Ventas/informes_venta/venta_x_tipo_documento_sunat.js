var url;

$(function () {
  $('#div-venta_x_tipo_documento_sunat').hide();

  var arrParams = {};
  getAlmacenes(arrParams);
  
  $( '.btn-generar_venta_sunat' ).click(function(){
    var ID_Almacen, Fe_Inicio, Fe_Fin, iDocumentStatus, Nu_Tipo_Impuesto, tr_body = "", tr_foot = "";

    Fe_Inicio = ParseDateString($( '#txt-Filtro_Fe_Inicio' ).val(), 1, '/');
    Fe_Fin    = ParseDateString($( '#txt-Filtro_Fe_Fin' ).val(), 1, '/');
    iDocumentStatus = $('#cbo-Filtro_Estado').val();
    ID_Almacen = $('#cbo-Almacenes_VentasTiposDocumentoSunat').val();
    Nu_Tipo_Impuesto = $('#cbo-regalo').val();

    var arrPost = {
      Fe_Inicio : Fe_Inicio,
      Fe_Fin    : Fe_Fin,
      iDocumentStatus: iDocumentStatus,
      ID_Almacen: ID_Almacen,
      Nu_Tipo_Impuesto:Nu_Tipo_Impuesto
    };
      
    if ($(this).data('type') == 'html') {
      $( '#btn-html_venta_sunat' ).text('');
      $( '#btn-html_venta_sunat' ).attr('disabled', true);
      $( '#btn-html_venta_sunat' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
  
      $( '#table-venta_x_tipo_documento_sunat > tbody' ).empty();
      $( '#table-venta_x_tipo_documento_sunat > tfoot' ).empty();
      
      url = base_url + 'Ventas/informes_venta/VentasTiposDocumentoSunatController/sendReporte';
      $.post( url, arrPost, function( response ){
        if (response.length > 0){
          var iCantTransBoleta = 0, fTotalBoleta = 0.00, iCantTransFactura = 0, fTotalFactura = 0.00, iCantTransNC = 0, fTotalNC = 0.00, iCantTransND = 0, fTotalND = 0.00, sum_cantidad_trans_b = 0,
          sum_total_b = 0.00,
          sum_cantidad_trans_f = 0,
          sum_total_f = 0.00,
          sum_cantidad_trans_nc = 0,
          sum_total_nc = 0.00,
          sum_cantidad_trans_nd = 0,
          sum_total_nd = 0.00;
          var $ID_Almacen = 0, $counter_almacen = 0, $sum_cantidad_trans_b_almacen = 0, $sum_total_b_almacen = 0.00, $sum_cantidad_trans_f_almacen = 0, $sum_total_f_almacen = 0.00, $sum_cantidad_trans_nc_almacen = 0, $sum_total_nc_almacen = 0.00, $sum_cantidad_trans_nd_almacen = 0, $sum_total_nd_almacen = 0.00;
          for (var i = 0, len = response.length; i < len; i++) {
            iCantTransBoleta = (!isNaN(parseInt(response[i].Nu_Cantidad_Trans_BOL)) ? parseInt(response[i].Nu_Cantidad_Trans_BOL) : 0 );
            fTotalBoleta = (!isNaN(parseFloat(response[i].Ss_Total_BOL)) ? parseFloat(response[i].Ss_Total_BOL) : 0 );
            iCantTransFactura = (!isNaN(parseInt(response[i].Nu_Cantidad_Trans_FACT)) ? parseInt(response[i].Nu_Cantidad_Trans_FACT) : 0 );
            fTotalFactura = (!isNaN(parseFloat(response[i].Ss_Total_FACT)) ? parseFloat(response[i].Ss_Total_FACT) : 0 );
            iCantTransNC = (!isNaN(parseInt(response[i].Nu_Cantidad_Trans_NC)) ? parseInt(response[i].Nu_Cantidad_Trans_NC) : 0 );
            fTotalNC = (!isNaN(parseFloat(response[i].Ss_Total_NC)) ? parseFloat(response[i].Ss_Total_NC) : 0 );
            iCantTransND = (!isNaN(parseInt(response[i].Nu_Cantidad_Trans_ND)) ? parseInt(response[i].Nu_Cantidad_Trans_ND) : 0 );
            fTotalND = (!isNaN(parseFloat(response[i].Ss_Total_ND)) ? parseFloat(response[i].Ss_Total_ND) : 0 );

            if ($ID_Almacen != response[i].ID_Almacen) {
              if ($counter_almacen != 0) {
                tr_body += "<tr>"
                  + "<th class='text-right'>Total Almacén</th>"
                  + "<th class='text-right'>" + $sum_cantidad_trans_b_almacen + "</th>"
                  + "<th class='text-right'>" + number_format($sum_total_b_almacen, 2) + "</th>"
                  + "<th class='text-right'>" + $sum_cantidad_trans_f_almacen + "</th>"
                  + "<th class='text-right'>" + number_format($sum_total_f_almacen, 2) + "</th>"
                  + "<th class='text-right'>" + $sum_cantidad_trans_nc_almacen + "</th>"
                  + "<th class='text-right'>" + number_format($sum_total_nc_almacen, 2) + "</th>"
                  + "<th class='text-right'>" + $sum_cantidad_trans_nd_almacen + "</th>"
                  + "<th class='text-right'>" + number_format($sum_total_nd_almacen, 2) + "</th>"
                  + "<th class='text-right'>" + (parseInt($sum_cantidad_trans_b_almacen) + parseInt($sum_cantidad_trans_f_almacen) + parseInt($sum_cantidad_trans_nc_almacen) + parseInt($sum_cantidad_trans_nd_almacen)) + "</th>"
                  + "<th class='text-right'>" + number_format(parseFloat($sum_total_b_almacen) + parseFloat($sum_total_f_almacen) - parseFloat($sum_total_nc_almacen) + parseFloat($sum_total_nd_almacen), 2) + "</th>"
                + "</tr>";
              }

              $sum_cantidad_trans_b_almacen = 0;
              $sum_total_b_almacen = 0;
              $sum_cantidad_trans_f_almacen = 0;
              $sum_total_f_almacen = 0;
              $sum_cantidad_trans_nc_almacen = 0;
              $sum_total_nc_almacen = 0;
              $sum_cantidad_trans_nd_almacen = 0;
              $sum_total_nd_almacen = 0;

              tr_body +=
                "<tr>"
                + "<th class='text-right'><span style='font-size: 15px;'>Almacén</span></th>"
                + "<th class='text-left' colspan='34'><span style='font-size: 15px;'>" + response[i].No_Almacen + "</span></th>"
                + "</tr>";

              $ID_Almacen = response[i].ID_Almacen;
            }// if almacen

            tr_body +=
            "<tr>"
              +"<td class='text-center'>" + response[i].Fe_Emision + "</td>"
              +"<td class='text-right'>" + iCantTransBoleta + "</td>"
              +"<td class='text-right'>" + number_format(fTotalBoleta, 2) + "</td>"
              +"<td class='text-right'>" + iCantTransFactura + "</td>"
              +"<td class='text-right'>" + number_format(fTotalFactura, 2) + "</td>"
              +"<td class='text-right'>" + iCantTransNC + "</td>"
              +"<td class='text-right'>" + number_format(fTotalNC, 2) + "</td>"
              +"<td class='text-right'>" + iCantTransND + "</td>"
              +"<td class='text-right'>" + number_format(fTotalND, 2) + "</td>"
              +"<td class='text-right'>" + (iCantTransBoleta + iCantTransFactura + iCantTransNC + iCantTransND) + "</td>"
              +"<td class='text-right'>" + number_format(fTotalBoleta + fTotalFactura - fTotalNC + fTotalND, 2) + "</td>"
            +"</tr>";
            
            sum_cantidad_trans_b += iCantTransBoleta;
            sum_total_b += fTotalBoleta;
            sum_cantidad_trans_f += iCantTransFactura;
            sum_total_f += fTotalFactura;
            sum_cantidad_trans_nc += iCantTransNC;
            sum_total_nc += fTotalNC;
            sum_cantidad_trans_nd += iCantTransND;
            sum_total_nd += fTotalND;

            $sum_cantidad_trans_b_almacen += iCantTransBoleta;
            $sum_total_b_almacen += fTotalBoleta;
            $sum_cantidad_trans_f_almacen += iCantTransFactura;
            $sum_total_f_almacen += fTotalFactura;
            $sum_cantidad_trans_nc_almacen += iCantTransNC;
            $sum_total_nc_almacen += fTotalNC;
            $sum_cantidad_trans_nd_almacen += iCantTransND;
            $sum_total_nd_almacen += fTotalND;

            $counter_almacen++;
          }
          
          tr_foot =
          "<tfoot>"
            +"<tr>"
              +"<th class='text-right'>Total Almacén</th>"
              +"<th class='text-right'>" + $sum_cantidad_trans_b_almacen + "</th>"
              + "<th class='text-right'>" + number_format($sum_total_b_almacen, 2) + "</th>"
              + "<th class='text-right'>" + $sum_cantidad_trans_f_almacen + "</th>"
              + "<th class='text-right'>" + number_format($sum_total_f_almacen, 2) + "</th>"
              + "<th class='text-right'>" + $sum_cantidad_trans_nc_almacen + "</th>"
              + "<th class='text-right'>" + number_format($sum_total_nc_almacen, 2) + "</th>"
              + "<th class='text-right'>" + $sum_cantidad_trans_nd_almacen + "</th>"
              + "<th class='text-right'>" + number_format($sum_total_nd_almacen, 2) + "</th>"
              + "<th class='text-right'>" + (parseInt($sum_cantidad_trans_b_almacen) + parseInt($sum_cantidad_trans_f_almacen) + parseInt($sum_cantidad_trans_nc_almacen) + parseInt($sum_cantidad_trans_nd_almacen)) + "</th>"
              + "<th class='text-right'>" + number_format(parseFloat($sum_total_b_almacen) + parseFloat($sum_total_f_almacen) - parseFloat($sum_total_nc_almacen) + parseFloat($sum_total_nd_almacen), 2) + "</th>"
            + "</tr>"
            + "<tr>"
              + "<th class='text-right'>Total</th>"
              + "<th class='text-right'>" + sum_cantidad_trans_b + "</th>"
              + "<th class='text-right'>" + number_format(sum_total_b, 2) + "</th>"
              + "<th class='text-right'>" + sum_cantidad_trans_f + "</th>"
              + "<th class='text-right'>" + number_format(sum_total_f, 2) + "</th>"
              + "<th class='text-right'>" + sum_cantidad_trans_nc + "</th>"
              + "<th class='text-right'>" + number_format(sum_total_nc, 2) + "</th>"
              + "<th class='text-right'>" + sum_cantidad_trans_nd + "</th>"
              + "<th class='text-right'>" + number_format(sum_total_nd, 2) + "</th>"
              + "<th class='text-right'>" + (parseInt(sum_cantidad_trans_b) + parseInt(sum_cantidad_trans_f) + parseInt(sum_cantidad_trans_nc) + parseInt(sum_cantidad_trans_nd)) + "</th>"
              + "<th class='text-right'>" + number_format(parseFloat(sum_total_b) + parseFloat(sum_total_f) - parseFloat(sum_total_nc) + parseFloat(sum_total_nd), 2) + "</th>"
            + "</tr>"
          +"</tfoot>";
        } else {
          tr_body +=
          "<tr>"
            + "<td colspan='10' class='text-center'>No hay registros</td>"
          + "</tr>";
        }
        
        $( '#div-venta_x_tipo_documento_sunat' ).show();
        $( '#table-venta_x_tipo_documento_sunat > tbody' ).append(tr_body);
        $( '#table-venta_x_tipo_documento_sunat > tbody' ).after(tr_foot);
        
        $( '#btn-html_venta_sunat' ).text('');
        $( '#btn-html_venta_sunat' ).append( '<i class="fa fa-search"></i> Buscar' );
        $( '#btn-html_venta_sunat' ).attr('disabled', false);
      }, 'JSON');
    } else if ($(this).data('type') == 'pdf') {
      $( '#btn-pdf_venta_sunat' ).text('');
      $( '#btn-pdf_venta_sunat' ).attr('disabled', true);
      $( '#btn-pdf_venta_sunat' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
          
      url = base_url + 'Ventas/informes_venta/VentasTiposDocumentoSunatController/sendReportePDF/' + Fe_Inicio + '/' + Fe_Fin + '/' + iDocumentStatus + '/' + ID_Almacen + '/' + Nu_Tipo_Impuesto;
      window.open(url,'_blank');
      
      $( '#btn-pdf_venta_sunat' ).text('');
      $( '#btn-pdf_venta_sunat' ).append( '<i class="fa fa-file-pdf-o color_white"></i> PDF' );
      $( '#btn-pdf_venta_sunat' ).attr('disabled', false);
    } else if ($(this).data('type') == 'excel') {
	    $( '#btn-excel_venta_sunat' ).text('');
      $( '#btn-excel_venta_sunat' ).attr('disabled', true);
      $( '#btn-excel_venta_sunat' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
      
      url = base_url + 'Ventas/informes_venta/VentasTiposDocumentoSunatController/sendReporteEXCEL/' + Fe_Inicio + '/' + Fe_Fin + '/' + iDocumentStatus + '/' + ID_Almacen + '/' + Nu_Tipo_Impuesto;
      window.open(url,'_blank');
      
      $( '#btn-excel_venta_sunat' ).text('');
      $( '#btn-excel_venta_sunat' ).append( '<i class="fa fa-file-excel-o color_white"></i> Excel' );
      $( '#btn-excel_venta_sunat' ).attr('disabled', false);
	  }
  })
})

// Ayudas - combobox
function getAlmacenes(arrParams) {
  $('#cbo-Almacenes_VentasTiposDocumentoSunat').html('<option value="0">- Todos -</option>');
  url = base_url + 'HelperController/getAlmacenes';
  $.post(url, {}, function (responseAlmacen) {
    var iCantidadRegistros = responseAlmacen.length;
    var selected = '';
    var iIdAlmacen = 0;
    $('#cbo-Almacenes_VentasTiposDocumentoSunat').html('<option value="0">- Todos -</option>');
    if (iCantidadRegistros == 1) {
      if (arrParams !== undefined) {
        iIdAlmacen = arrParams.ID_Almacen;
      }
      if (iIdAlmacen == responseAlmacen[0]['ID_Almacen']) {
        selected = 'selected="selected"';
      }
      $('#cbo-Almacenes_VentasTiposDocumentoSunat').append('<option value="' + responseAlmacen[0]['ID_Almacen'] + '" ' + selected + ' data-direccion_almacen="' + responseAlmacen[0]['Txt_Direccion_Almacen'] + '">' + responseAlmacen[0]['No_Almacen'] + '</option>');
    } else {
      for (var i = 0; i < iCantidadRegistros; i++) {
        if (arrParams !== undefined) {
          iIdAlmacen = arrParams.ID_Almacen;
        }
        if (iIdAlmacen == responseAlmacen[0]['ID_Almacen']) {
          selected = 'selected="selected"';
        }
        $('#cbo-Almacenes_VentasTiposDocumentoSunat').append('<option value="' + responseAlmacen[i]['ID_Almacen'] + '" ' + selected + ' data-direccion_almacen="' + responseAlmacen[0]['Txt_Direccion_Almacen'] + '">' + responseAlmacen[i]['No_Almacen'] + '</option>');
      }
    }
    $('#modal-loader').modal('hide');
  }, 'JSON');
}