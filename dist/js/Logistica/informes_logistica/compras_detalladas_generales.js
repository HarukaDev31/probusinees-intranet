var url;

$(function () {
  $( '#modal-loader' ).modal('show');
  $('#div-compras_detalladas_generales').hide();

  var arrParams = {};
  getAlmacenes(arrParams);
    
  url = base_url + 'HelperController/getTiposDocumentos';
  $.post( url, {Nu_Tipo_Filtro : 9}, function( response ){
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
          sTipoSerie = '(' + ( response[i].ID_POS > 0 ? 'Punto Venta' : 'Oficina' ) + ')';
          $( '#cbo-filtros_series_documento' ).append( '<option value="' + response[i].ID_Serie_Documento + '">' + response[i].ID_Serie_Documento + ' ' + sTipoSerie + '</option>' );
        }
      }, 'JSON');
	  }
  })

  $( '.btn-generar_compras_detalladas_generales' ).click(function(){    
    var ID_Almacen, Fe_Inicio, Fe_Fin, ID_Tipo_Documento, ID_Serie_Documento, ID_Numero_Documento, Nu_Estado_Documento, iIdCliente, sNombreCliente, iIdItem, sNombreItem, iTipoVenta;
    
    Fe_Inicio = ParseDateString($( '#txt-Filtro_Fe_Inicio' ).val(), 1, '/');
    Fe_Fin = ParseDateString($( '#txt-Filtro_Fe_Fin' ).val(), 1, '/');
    ID_Tipo_Documento = $( '#cbo-filtros_tipos_documento' ).val();
    ID_Serie_Documento = ($( '#txt-Filtro_SerieDocumento' ).val().length == 0 ? '-' : $( '#txt-Filtro_SerieDocumento' ).val());
    ID_Numero_Documento = ($( '#txt-Filtro_NumeroDocumento' ).val().length == 0 ? '-' : $( '#txt-Filtro_NumeroDocumento' ).val());
    Nu_Estado_Documento = $( '#cbo-estado_documento' ).val();
    iIdCliente = ($( '#txt-AID' ).val().length === 0 ? '-' : $( '#txt-AID' ).val());
    sNombreCliente = ($( '#txt-Filtro_Entidad' ).val().length === 0 ? '-' : $( '#txt-Filtro_Entidad' ).val());
    iIdItem = ($( '#txt-ID_Producto' ).val().length === 0 ? '-' : $( '#txt-ID_Producto' ).val());
    sNombreItem = ($( '#txt-No_Producto' ).val().length === 0 ? '-' : $( '#txt-No_Producto' ).val());
    iTipoVenta = $('#cbo-tipo_venta').val();
    ID_Almacen = $('#cbo-Almacenes_ReporteFormaPagoProveedor').val();

    if ($(this).data('type') == 'html') {
      $( '#btn-html_compras_detalladas_generales' ).text('');
      $( '#btn-html_compras_detalladas_generales' ).attr('disabled', true);
      $( '#btn-html_compras_detalladas_generales' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
  
      $( '#table-compras_detalladas_generales > tbody' ).empty();
      $( '#table-compras_detalladas_generales > tfoot' ).empty();

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
        ID_Almacen: ID_Almacen,
      };      
      url = base_url + 'Logistica/informes_logistica/ComprasDetalladasGeneralesController/sendReporte';
      $.post( url, arrPost, function( response ){
        if ( response.sStatus == 'success' ) {
          var iTotalRegistros = response.arrData.length, tr_body = '', tr_foot = '';
          var fCantidadItem = 0.00, fPrecioItem = 0.00, fSubtotalItem = 0.00, fImpuestoItem = 0.00, fTotalItem = 0.00;
          var fCantidadTotalGeneral = 0.00, fSubtotalGeneral = 0.00, fImpuestoGeneral = 0.00, fTotalGeneral = 0.00;
          var $ID_Almacen = 0, $counter_almacen=0, $fCantidadTotalGeneralAlmacen = 0.00, $fSubtotalGeneralAlmacen = 0.00, $fImpuestoGeneralAlmacen = 0.00, $fTotalGeneralAlmacen = 0.00;
          var response=response.arrData;
          for (var i = 0; i < iTotalRegistros; i++) {
            fCantidadItem = (!isNaN(parseFloat(response[i].Qt_Producto)) ? parseFloat(response[i].Qt_Producto) : 0);
            fPrecioItem = (!isNaN(parseFloat(response[i].Ss_Precio)) ? parseFloat(response[i].Ss_Precio) : 0);
            fSubtotalItem = (!isNaN(parseFloat(response[i].Ss_Subtotal)) ? parseFloat(response[i].Ss_Subtotal) : 0);
            fImpuestoItem = (!isNaN(parseFloat(response[i].Ss_Impuesto)) ? parseFloat(response[i].Ss_Impuesto) : 0);
            fTotalItem = (!isNaN(parseFloat(response[i].Ss_Total)) ? parseFloat(response[i].Ss_Total) : 0);

            if ($ID_Almacen != response[i].ID_Almacen) {
              if ($counter_almacen != 0) {
                tr_body += "<tr>"
                  + "<th class='text-right' colspan='17'>Total Almacén</th>"
                  + "<th class='text-right'>" + number_format($fCantidadTotalGeneralAlmacen, 2) + "</th>"
                  + "<th class='text-right'>&nbsp;</th>"
                  + "<th class='text-right'>&nbsp;</th>"
                  + "<th class='text-right'>" + number_format($fSubtotalGeneralAlmacen, 2) + "</th>"
                  + "<th class='text-right'>" + number_format($fImpuestoGeneralAlmacen, 2) + "</th>"
                  + "<th class='text-right'>" + number_format($fTotalGeneralAlmacen, 2) + "</th>"
                  + "<th class='text-right'></th>"
                +"</tr>";
              }

              $fCantidadTotalGeneralAlmacen = 0.00;
              $fSubtotalGeneralAlmacen = 0.00;
              $fImpuestoGeneralAlmacen = 0.00;
              $fTotalGeneralAlmacen = 0.00;

              tr_body +=
              "<tr>"
                + "<th class='text-right'><span style='font-size: 15px;'>Almacén</span></th>"
                + "<th class='text-left' colspan='24'><span style='font-size: 15px;'>" + response[i].No_Almacen + "</span></th>"
              + "</tr>";

              $ID_Almacen = response[i].ID_Almacen;
            }// if almacen

            tr_body +=
            +"<tr>"
              +"<td class='text-center'>" + response[i].Fe_Emision_Hora + "</td>"
              +"<td class='text-center'>" + response[i].Fe_Hora + "</td>"
              +"<td class='text-center'>" + response[i].No_Tipo_Documento_Breve + "</td>"
              +"<td class='text-center'>" + response[i].ID_Serie_Documento + "</td>"
              +"<td class='text-center'>" + response[i].ID_Numero_Documento + "</td>"
              +"<td class='text-center'>" + response[i].No_Tipo_Documento_Identidad_Breve + "</td>"
              +"<td class='text-center'>" + response[i].Nu_Documento_Identidad + "</td>"
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
              +"<td class='text-left'>" + response[i].Txt_Nota + "</td>"
              +"<td class='text-center'><span class='label label-" + response[i].No_Class_Estado + "'>" + response[i].No_Estado + "</span></td>"
            +"</tr>";
            
            fCantidadTotalGeneral += fCantidadItem;
            fSubtotalGeneral += fSubtotalItem;
            fImpuestoGeneral += fImpuestoItem;
            fTotalGeneral += fTotalItem;

            $fCantidadTotalGeneralAlmacen += fCantidadItem;
            $fSubtotalGeneralAlmacen += fSubtotalItem;
            $fImpuestoGeneralAlmacen += fImpuestoItem;
            $fTotalGeneralAlmacen += fTotalItem;

            $counter_almacen++;
          }
          
          tr_foot =
          "<tfoot>"
            +"<tr>"
              +"<th class='text-right' colspan='17'>Total Almacén</th>"
              +"<th class='text-right'>" + number_format($fCantidadTotalGeneralAlmacen, 2) + "</th>"
              +"<th class='text-right'>&nbsp;</th>"
              +"<th class='text-right'>&nbsp;</th>"
              + "<th class='text-right'>" + number_format($fSubtotalGeneralAlmacen, 2) + "</th>"
              + "<th class='text-right'>" + number_format($fImpuestoGeneralAlmacen, 2) + "</th>"
              + "<th class='text-right'>" + number_format($fTotalGeneralAlmacen, 2) + "</th>"
              +"<th class='text-right'></th>"
            + "</tr>"
            + "<tr>"
              + "<th class='text-right' colspan='17'>Total</th>"
              + "<th class='text-right'>" + number_format(fCantidadTotalGeneral, 2) + "</th>"
              + "<th class='text-right'>&nbsp;</th>"
              + "<th class='text-right'>&nbsp;</th>"
              + "<th class='text-right'>" + number_format(fSubtotalGeneral, 2) + "</th>"
              + "<th class='text-right'>" + number_format(fImpuestoGeneral, 2) + "</th>"
              + "<th class='text-right'>" + number_format(fTotalGeneral, 2) + "</th>"
              + "<th class='text-right'></th>"
            + "</tr>"
          +"</tfoot>";
        } else {
          if( response.sMessageSQL !== undefined ) {
            console.log(response.sMessageSQL);
          }
          tr_body +=
          "<tr>"
            +"<td colspan='26' class='text-center'>" + response.sMessage + "</td>"
          + "</tr>";
        } // ./ if arrData
        
        $( '#div-compras_detalladas_generales' ).show();
        $( '#table-compras_detalladas_generales > tbody' ).append(tr_body);
        $( '#table-compras_detalladas_generales > tbody' ).after(tr_foot);
        
        $( '#btn-html_compras_detalladas_generales' ).text('');
        $( '#btn-html_compras_detalladas_generales' ).append( '<i class="fa fa-search"></i> Buscar' );
        $( '#btn-html_compras_detalladas_generales' ).attr('disabled', false);
      }, 'JSON')
      .fail(function(jqXHR, textStatus, errorThrown) {
        $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
        
        $( '#modal-message' ).modal('show');
        $( '.modal-message' ).addClass( 'modal-danger' );
        $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
        setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
        
        //Message for developer
        console.log(jqXHR.responseText);
        
        $( '#btn-html_compras_detalladas_generales' ).text('');
        $( '#btn-html_compras_detalladas_generales' ).append( '<i class="fa fa-search"></i> Buscar' );
        $( '#btn-html_compras_detalladas_generales' ).attr('disabled', false);
      });
    } else if ($(this).data('type') == 'pdf') {
      $( '#btn-pdf_compras_detalladas_generales' ).text('');
      $( '#btn-pdf_compras_detalladas_generales' ).attr('disabled', true);
      $( '#btn-pdf_compras_detalladas_generales' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
          
      url = base_url + 'Logistica/informes_logistica/ComprasDetalladasGeneralesController/sendReportePDF/' + Fe_Inicio + '/' + Fe_Fin + '/' + ID_Tipo_Documento + '/' + ID_Serie_Documento + '/' + ID_Numero_Documento + '/' + Nu_Estado_Documento + '/' + iIdCliente + '/' + sNombreCliente + '/' + iIdItem + '/' + sNombreItem + '/' + iTipoVenta + '/' + ID_Almacen;
      window.open(url,'_blank');
      
      $( '#btn-pdf_compras_detalladas_generales' ).text('');
      $( '#btn-pdf_compras_detalladas_generales' ).append( '<i class="fa fa-file-pdf-o color_icon_pdf"></i> PDF' );
      $( '#btn-pdf_compras_detalladas_generales' ).attr('disabled', false);
    } else if ($(this).data('type') == 'excel') {
      $( '#btn-excel_compras_detalladas_generales' ).text('');
      $( '#btn-excel_compras_detalladas_generales' ).attr('disabled', true);
      $( '#btn-excel_compras_detalladas_generales' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
      
      url = base_url + 'Logistica/informes_logistica/ComprasDetalladasGeneralesController/sendReporteEXCEL/' + Fe_Inicio + '/' + Fe_Fin + '/' + ID_Tipo_Documento + '/' + ID_Serie_Documento + '/' + ID_Numero_Documento + '/' + Nu_Estado_Documento + '/' + iIdCliente + '/' + sNombreCliente + '/' + iIdItem + '/' + sNombreItem + '/' + iTipoVenta + '/' + ID_Almacen;
      window.open(url,'_blank');
      
      $( '#btn-excel_compras_detalladas_generales' ).text('');
      $( '#btn-excel_compras_detalladas_generales' ).append( '<i class="fa fa-file-excel-o color_icon_excel"></i> Excel' );
      $( '#btn-excel_compras_detalladas_generales' ).attr('disabled', false);
    }// /. if
  })// /. btn
})

// Ayudas - combobox
function getAlmacenes(arrParams) {
  $('#cbo-Almacenes_ComprasDetalladasGenerales').html('<option value="0">- Todos -</option>');
  url = base_url + 'HelperController/getAlmacenes';
  $.post(url, {}, function (responseAlmacen) {
    var iCantidadRegistros = responseAlmacen.length;
    var selected = '';
    var iIdAlmacen = 0;
    $('#cbo-Almacenes_ComprasDetalladasGenerales').html('<option value="0">- Todos -</option>');
    if (iCantidadRegistros == 1) {
      if (arrParams !== undefined) {
        iIdAlmacen = arrParams.ID_Almacen;
      }
      if (iIdAlmacen == responseAlmacen[0]['ID_Almacen']) {
        selected = 'selected="selected"';
      }
      $('#cbo-Almacenes_ComprasDetalladasGenerales').append('<option value="' + responseAlmacen[0]['ID_Almacen'] + '" ' + selected + ' data-direccion_almacen="' + responseAlmacen[0]['Txt_Direccion_Almacen'] + '">' + responseAlmacen[0]['No_Almacen'] + '</option>');
    } else {
      for (var i = 0; i < iCantidadRegistros; i++) {
        if (arrParams !== undefined) {
          iIdAlmacen = arrParams.ID_Almacen;
        }
        if (iIdAlmacen == responseAlmacen[0]['ID_Almacen']) {
          selected = 'selected="selected"';
        }
        $('#cbo-Almacenes_ComprasDetalladasGenerales').append('<option value="' + responseAlmacen[i]['ID_Almacen'] + '" ' + selected + ' data-direccion_almacen="' + responseAlmacen[0]['Txt_Direccion_Almacen'] + '">' + responseAlmacen[i]['No_Almacen'] + '</option>');
      }
    }
    $('#modal-loader').modal('hide');
  }, 'JSON');
}