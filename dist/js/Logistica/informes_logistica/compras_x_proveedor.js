var url;

$(function () {
  $( '#modal-loader' ).modal('show');
  $( '#div-compras_x_proveedor' ).hide();

  var arrParams = {};
  getAlmacenes(arrParams);

  url = base_url + 'HelperController/getTiposDocumentos';
  $.post( url, {Nu_Tipo_Filtro : 2}, function( response ){
    $( '#cbo-filtros_tipos_documento' ).html('<option value="0" selected="selected">Todos</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-filtros_tipos_documento' ).append( '<option value="' + response[i].ID_Tipo_Documento + '">' + response[i].No_Tipo_Documento_Breve + '</option>' );
    $( '#modal-loader' ).modal('hide');
  }, 'JSON');
  
  $( '.btn-generar_compras_x_proveedor' ).click(function(){
    var ID_Almacen, Fe_Inicio, Fe_Fin, ID_Tipo_Documento, ID_Serie_Documento, ID_Numero_Documento, Nu_Estado_Documento, sNombreProveedor, iIdItem, sNombreItem, iTipoReporte;
    
    Fe_Inicio = ParseDateString($( '#txt-Filtro_Fe_Inicio' ).val(), 1, '/');
    Fe_Fin = ParseDateString($( '#txt-Filtro_Fe_Fin' ).val(), 1, '/');
    ID_Tipo_Documento = $( '#cbo-filtros_tipos_documento' ).val();
    ID_Serie_Documento = ($( '#txt-Filtro_SerieDocumento' ).val().length == 0 ? '-' : $( '#txt-Filtro_SerieDocumento' ).val());
    ID_Numero_Documento = ($( '#txt-Filtro_NumeroDocumento' ).val().length == 0 ? '-' : $( '#txt-Filtro_NumeroDocumento' ).val());
    Nu_Estado_Documento = $( '#cbo-estado_documento option:selected' ).val();
    iIdProveedor = ($( '#txt-AID' ).val().length === 0 ? '-' : $( '#txt-AID' ).val());
    sNombreProveedor = ($( '#txt-Filtro_Entidad' ).val().length === 0 ? '-' : $( '#txt-Filtro_Entidad' ).val());
    iIdItem = ($( '#txt-ID_Producto' ).val().length === 0 ? '-' : $( '#txt-ID_Producto' ).val());
    sNombreItem = ($( '#txt-No_Producto' ).val().length === 0 ? '-' : $( '#txt-No_Producto' ).val());
    iTipoReporte = $('[name="radio-tipo-reporte-compras_x_proveedor"]:checked').attr('value');
    ID_Almacen = $('#cbo-Almacenes_Compras_x_Proveedor option:selected').val();
      
    if ($(this).data('type') == 'html') {
      $( '#btn-html_compras_x_proveedor' ).text('');
      $( '#btn-html_compras_x_proveedor' ).attr('disabled', true);
      $( '#btn-html_compras_x_proveedor' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
  
      $( '#table-compras_x_proveedor > tbody' ).empty();
      $( '#table-compras_x_proveedor > tfoot' ).empty();
        
      var arrPost = {
        Fe_Inicio : Fe_Inicio,
        Fe_Fin : Fe_Fin,
        ID_Tipo_Documento : ID_Tipo_Documento,
        ID_Serie_Documento : ID_Serie_Documento,
        ID_Numero_Documento : ID_Numero_Documento,
        Nu_Estado_Documento : Nu_Estado_Documento,
        iIdProveedor : iIdProveedor,
        sNombreProveedor : sNombreProveedor,
        iIdItem : iIdItem,
        sNombreItem : sNombreItem,
        ID_Almacen: ID_Almacen
      };

      url = base_url + 'Logistica/informes_logistica/Compras_x_proveedor_controller/sendReporte';
      $.post( url, arrPost, function( response ){
        if ( response.sStatus == 'success' ) {
          var iTotalRegistros = response.arrData.length, tr_body = '', tr_foot = '';
          var $ID_Almacen = 0, $counter_almacen = 0, $sum_almacen_compras_cantidad = 0.000000, $sum_almacen_compras_subtotal_s = 0.00, $sum_almacen_compras_descuento_s = 0.00, $sum_almacen_compras_igv_s = 0.00, $sum_almacen_compras_total_s = 0.00, $sum_almacen_compras_total_d = 0.00;
          var ID_Entidad = '', counter = 0, sum_compras_cantidad = 0.000000, sum_compras_subtotal_s = 0.00, sum_compras_descuento_s = 0.00, sum_compras_igv_s = 0.00, sum_compras_total_s = 0.00, sum_compras_total_d = 0.00;
          var subtotal_s = 0.00, descuento_s = 0.00, igv_s = 0.00, total_s = 0.00;
          var sum_general_compras_cantidad = 0.000000, sum_general_compras_subtotal_s = 0.00, sum_general_compras_descuento_s = 0.00, sum_general_compras_igv_s = 0.00, sum_general_compras_total_s = 0.00, sum_general_compras_total_d = 0.00;
          var response=response.arrData;
          for (var i = 0; i < iTotalRegistros; i++) {
            if (ID_Entidad != response[i].ID_Entidad || $ID_Almacen != response[i].ID_Almacen) {
              if (counter != 0) {
                tr_body +=
                +"<tr>"
                  + "<th class='text-right' colspan='8'>Total </th>"
                  + "<th class='text-right'>" + number_format(sum_compras_cantidad, 3) + "</th>"
                  + "<th class='text-right'></th>"
                  + "<th class='text-right'>" + number_format(sum_compras_subtotal_s, 2) + "</th>"
                  + "<th class='text-right'>" + number_format(sum_compras_igv_s, 2) + "</th>"
                  + "<th class='text-right'>" + number_format(sum_compras_descuento_s, 2) + "</th>"
                  + "<th class='text-right'>" + number_format(sum_compras_total_s, 2) + "</th>"
                  + "<th class='text-right'>" + number_format(sum_compras_total_d, 2) + "</th>"
                + "</tr>";
                sum_compras_cantidad = 0.000000;
                sum_compras_subtotal_s = 0.00;
                sum_compras_igv_s = 0.00;
                sum_compras_descuento_s = 0.00;
                sum_compras_total_s = 0.00;
                sum_compras_total_d = 0.00;
              }// counter entidad

              if ($ID_Almacen != response[i].ID_Almacen) {
                if ($counter_almacen != 0) {
                  tr_body +=
                  +"<tr>"
                    + "<th class='text-right' colspan='8'>Total Almacén</th>"
                    + "<th class='text-right'>" + number_format($sum_almacen_compras_cantidad, 3) + "</th>"
                    + "<th class='text-right'></th>"
                    + "<th class='text-right'>" + number_format($sum_almacen_compras_subtotal_s, 2) + "</th>"
                    + "<th class='text-right'>" + number_format($sum_almacen_compras_igv_s, 2) + "</th>"
                    + "<th class='text-right'>" + number_format($sum_almacen_compras_descuento_s, 2) + "</th>"
                    + "<th class='text-right'>" + number_format($sum_almacen_compras_total_s, 2) + "</th>"
                    + "<th class='text-right'>" + number_format($sum_almacen_compras_total_d, 2) + "</th>"
                  + "</tr>";
                }

                $sum_almacen_compras_cantidad = 0.000000;
                $sum_almacen_compras_subtotal_s = 0.00;
                $sum_almacen_compras_igv_s = 0.00;
                $sum_almacen_compras_descuento_s = 0.00;
                $sum_almacen_compras_total_s = 0.00;
                $sum_almacen_compras_total_d = 0.00;

                tr_body +=
                "<tr>"
                  +"<th class='text-right'><span style='font-size: 15px;'>Almacén</span></th>"
                  +"<th class='text-left' colspan='14'><span style='font-size: 15px;'>" + response[i].No_Almacen + "</span></th>"
                + "</tr>";

                $ID_Almacen = response[i].ID_Almacen;
              }// if almacen

              tr_body +=
              "<tr>"
                +"<th class='text-right'>Proveedor </th>"
                +"<th class='text-left'>" + response[i].Nu_Documento_Identidad + "</th>"
                +"<th class='text-left' colspan='15'>" + response[i].No_Entidad + "</th>"
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
                +"<td class='text-left'>" + response[i].Nu_Codigo_Barra + "</td>"
                +"<td class='text-left'>" + response[i].No_Producto + "</td>"
                +"<td class='text-right'>" + (response[i].ID_Tipo_Documento != 5 ? '' : '-') + number_format(response[i].Qt_Producto, 3) + "</td>"
                +"<td class='text-right'>" + (response[i].ID_Tipo_Documento != 5 ? '' : '-') + number_format(response[i].Ss_Precio, 3) + "</td>"
                +"<td class='text-right'>" + (response[i].ID_Tipo_Documento != 5 ? '' : '-') + number_format(subtotal_s, 2) + "</td>"
                +"<td class='text-right'>" + (response[i].ID_Tipo_Documento != 5 ? '' : '-') + number_format(igv_s, 2) + "</td>"
                +"<td class='text-right'>" + (response[i].ID_Tipo_Documento != 5 ? '' : '-') + number_format(descuento_s, 2) + "</td>"
                +"<td class='text-right'>" + (response[i].ID_Tipo_Documento != 5 ? '' : '-') + number_format(total_s, 2) + "</td>"
                +"<td class='text-right'>" + (response[i].ID_Tipo_Documento != 5 ? '' : '-') + number_format(response[i].Ss_Total_Extranjero, 2) + "</td>"
                +"<td class='text-center'>" + response[i].Nu_Lote_Vencimiento + "</td>"
                +"<td class='text-center'>" + response[i].Fe_Lote_Vencimiento + "</td>"
                +"<td class='text-right'>" + (response[i].ID_Tipo_Documento != 5 ? '' : '-') + number_format(response[i].Ss_Percepcion, 2) + "</td>"
                +"<td class='text-center'><span class='label label-" + response[i].No_Class_Estado + "'>" + response[i].No_Estado + "</span></td>"
              +"</tr>";
            }
            
            sum_compras_cantidad += (!isNaN(parseFloat(response[i].Qt_Producto)) ? parseFloat(response[i].Qt_Producto) : 0);
            sum_compras_subtotal_s += subtotal_s;
            sum_compras_igv_s += igv_s;
            sum_compras_descuento_s += descuento_s;
            sum_compras_total_s += total_s;
            sum_compras_total_d += parseFloat(response[i].Ss_Total_Extranjero);

            $sum_almacen_compras_cantidad += (!isNaN(parseFloat(response[i].Qt_Producto)) ? parseFloat(response[i].Qt_Producto) : 0);
            $sum_almacen_compras_subtotal_s += subtotal_s;
            $sum_almacen_compras_igv_s += igv_s;
            $sum_almacen_compras_descuento_s += descuento_s;
            $sum_almacen_compras_total_s += total_s;
            $sum_almacen_compras_total_d += parseFloat(response[i].Ss_Total_Extranjero);
            
            sum_general_compras_cantidad += (!isNaN(parseFloat(response[i].Qt_Producto)) ? parseFloat(response[i].Qt_Producto) : 0);
            sum_general_compras_subtotal_s += subtotal_s;
            sum_general_compras_igv_s += igv_s;
            sum_general_compras_descuento_s += descuento_s;
            sum_general_compras_total_s += total_s;
            sum_general_compras_total_d += parseFloat(response[i].Ss_Total_Extranjero);

            counter++;
            $counter_almacen++;
          }// /. for
          
          tr_foot =
          "<tfoot>"
            +"<tr>"
              +"<th class='text-right' colspan='8'>Total</th>"
              +"<th class='text-right'>" + number_format(sum_compras_cantidad, 3) + "</th>"
              +"<th class='text-right'></th>"
              +"<th class='text-right'>" + number_format(sum_compras_subtotal_s, 2) + "</th>"
              +"<th class='text-right'>" + number_format(sum_compras_igv_s, 2) + "</th>"
              +"<th class='text-right'>" + number_format(sum_compras_descuento_s, 2) + "</th>"
              +"<th class='text-right'>" + number_format(sum_compras_total_s, 2) + "</th>"
              +"<th class='text-right'>" + number_format(sum_compras_total_d, 2) + "</th>"
            + "</tr>"
            + "<tr>"
              + "<th class='text-right' colspan='8'>Total Almacén</th>"
              + "<th class='text-right'>" + number_format($sum_almacen_compras_cantidad, 3) + "</th>"
              + "<th class='text-right'></th>"
              + "<th class='text-right'>" + number_format($sum_almacen_compras_subtotal_s, 2) + "</th>"
              + "<th class='text-right'>" + number_format($sum_almacen_compras_igv_s, 2) + "</th>"
              + "<th class='text-right'>" + number_format($sum_almacen_compras_descuento_s, 2) + "</th>"
              + "<th class='text-right'>" + number_format($sum_almacen_compras_total_s, 2) + "</th>"
              + "<th class='text-right'>" + number_format($sum_almacen_compras_total_d, 2) + "</th>"
            + "</tr>"
            +"<tr>"
              +"<th class='text-right' colspan='8'>Total General</th>"
              +"<th class='text-right'>" + number_format(sum_general_compras_cantidad, 3) + "</th>"
              +"<th class='text-right'></th>"
              +"<th class='text-right'>" + number_format(sum_general_compras_subtotal_s, 2) + "</th>"
              +"<th class='text-right'>" + number_format(sum_general_compras_igv_s, 2) + "</th>"
              +"<th class='text-right'>" + number_format(sum_general_compras_descuento_s, 2) + "</th>"
              +"<th class='text-right'>" + number_format(sum_general_compras_total_s, 2) + "</th>"
              +"<th class='text-right'>" + number_format(sum_general_compras_total_d, 2) + "</th>"
            +"</tr>"
          +"</tfoot>";
        } else {
          tr_body +=
          "<tr>"
            + "<td colspan='15' class='text-center'>No hay registros</td>"
          + "</tr>";
        }
        
        $( '#div-compras_x_proveedor' ).show();
        $( '#table-compras_x_proveedor > tbody' ).append(tr_body);
        $( '#table-compras_x_proveedor > tbody' ).after(tr_foot);
        
        $( '#btn-html_compras_x_proveedor' ).text('');
        $( '#btn-html_compras_x_proveedor' ).append( '<i class="fa fa-search"></i> Buscar' );
        $( '#btn-html_compras_x_proveedor' ).attr('disabled', false);
      }, 'JSON')
      .fail(function(jqXHR, textStatus, errorThrown) {
        $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
        
        $( '#modal-message' ).modal('show');
        $( '.modal-message' ).addClass( 'modal-danger' );
        $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
        setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
        
        //Message for developer
        console.log(jqXHR.responseText);
        
        $( '#btn-html_compras_x_proveedor' ).text('');
        $( '#btn-html_compras_x_proveedor' ).append( '<i class="fa fa-search"></i> Buscar' );
        $( '#btn-html_compras_x_proveedor' ).attr('disabled', false);
      });
    } else if ($(this).data('type') == 'pdf') {
      $( '#btn-pdf_compras_x_proveedor' ).text('');
      $( '#btn-pdf_compras_x_proveedor' ).attr('disabled', true);
      $( '#btn-pdf_compras_x_proveedor' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
          
      url = base_url + 'Logistica/informes_logistica/Compras_x_proveedor_controller/sendReportePDF/' + Fe_Inicio + '/' + Fe_Fin + '/' + ID_Tipo_Documento + '/' + ID_Serie_Documento + '/' + ID_Numero_Documento + '/' + Nu_Estado_Documento + '/' + iIdProveedor + '/' + sNombreProveedor + '/' + iIdItem + '/' + sNombreItem + '/' + iTipoReporte + '/' + ID_Almacen;
      window.open(url,'_blank');
      
      $( '#btn-pdf_compras_x_proveedor' ).text('');
      $( '#btn-pdf_compras_x_proveedor' ).append( '<i class="fa fa-file-pdf-o"></i> PDF' );
      $( '#btn-pdf_compras_x_proveedor' ).attr('disabled', false);
    } else if ($(this).data('type') == 'excel') {
      $( '#btn-excel_compras_x_proveedor' ).text('');
      $( '#btn-excel_compras_x_proveedor' ).attr('disabled', true);
      $( '#btn-excel_compras_x_proveedor' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
      
      url = base_url + 'Logistica/informes_logistica/Compras_x_proveedor_controller/sendReporteEXCEL/' + Fe_Inicio + '/' + Fe_Fin + '/' + ID_Tipo_Documento + '/' + ID_Serie_Documento + '/' + ID_Numero_Documento + '/' + Nu_Estado_Documento + '/' + iIdProveedor + '/' + sNombreProveedor + '/' + iIdItem + '/' + sNombreItem + '/' + iTipoReporte + '/' + ID_Almacen;
      window.open(url,'_blank');
      
      $( '#btn-excel_compras_x_proveedor' ).text('');
      $( '#btn-excel_compras_x_proveedor' ).append( '<i class="fa fa-file-excel-o"></i> Excel' );
      $( '#btn-excel_compras_x_proveedor' ).attr('disabled', false);
    }
  })//./ btn
})

// Ayudas - combobox
function getAlmacenes(arrParams) {
  $('#cbo-Almacenes_Compras_x_Proveedor').html('<option value="0">- Todos -</option>');
  url = base_url + 'HelperController/getAlmacenes';
  $.post(url, {}, function (responseAlmacen) {
    var iCantidadRegistros = responseAlmacen.length;
    var selected = '';
    var iIdAlmacen = 0;
    $('#cbo-Almacenes_Compras_x_Proveedor').html('<option value="0">- Todos -</option>');
    if (iCantidadRegistros == 1) {
      if (arrParams !== undefined) {
        iIdAlmacen = arrParams.ID_Almacen;
      }
      if (iIdAlmacen == responseAlmacen[0]['ID_Almacen']) {
        selected = 'selected="selected"';
      }
      $('#cbo-Almacenes_Compras_x_Proveedor').append('<option value="' + responseAlmacen[0]['ID_Almacen'] + '" ' + selected + ' data-direccion_almacen="' + responseAlmacen[0]['Txt_Direccion_Almacen'] + '">' + responseAlmacen[0]['No_Almacen'] + '</option>');
    } else {
      for (var i = 0; i < iCantidadRegistros; i++) {
        if (arrParams !== undefined) {
          iIdAlmacen = arrParams.ID_Almacen;
        }
        if (iIdAlmacen == responseAlmacen[0]['ID_Almacen']) {
          selected = 'selected="selected"';
        }
        $('#cbo-Almacenes_Compras_x_Proveedor').append('<option value="' + responseAlmacen[i]['ID_Almacen'] + '" ' + selected + ' data-direccion_almacen="' + responseAlmacen[0]['Txt_Direccion_Almacen'] + '">' + responseAlmacen[i]['No_Almacen'] + '</option>');
      }
    }
    $('#modal-loader').modal('hide');
  }, 'JSON');
}