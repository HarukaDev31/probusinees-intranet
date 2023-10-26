var url;

$(function () {  
  $('.select2').select2();
  
  $( '#modal-loader' ).modal('show');

  $('#div-saldo_cliente').hide();

  var arrParams = {};
  getAlmacenes(arrParams);
  
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
  
  $( '.btn-saldo_cliente' ).click(function(){
    if ( $( '#txt-Filtro_Entidad' ).val().length > 0 && $( '#txt-AID' ).val().length === 0 ) {
      $( '#txt-Filtro_Entidad' ).closest('.form-group').find('.help-block').html('Seleccionar cliente');
		  $( '#txt-Filtro_Entidad' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      $( '.help-block' ).empty();
    
      var ID_Almacen, Fe_Inicio, Fe_Fin, iIdTipoDocumento, iIdSerieDocumento, iNumeroDocumento, iEstadoPago, iIdCliente, sNombreCliente;
      
      Fe_Inicio = ParseDateString($( '#txt-Filtro_Fe_Inicio' ).val(), 1, '/');
      Fe_Fin = ParseDateString($( '#txt-Filtro_Fe_Fin' ).val(), 1, '/');
      iIdTipoDocumento = $( '#cbo-filtros_tipos_documento' ).val();
      iIdSerieDocumento = $( '#cbo-filtros_series_documento' ).val();
      iNumeroDocumento = ($( '#txt-Filtro_NumeroDocumento' ).val().length === 0 ? '-' : $( '#txt-Filtro_NumeroDocumento' ).val());
      iEstadoPago = $( '#cbo-estado_pago' ).val();
      iIdCliente = ($( '#txt-AID' ).val().length === 0 ? '-' : $( '#txt-AID' ).val());
      sNombreCliente = ($('#txt-Filtro_Entidad').val().length === 0 ? '-' : $('#txt-Filtro_Entidad').val());
      ID_Almacen = $('#cbo-Almacenes_SaldoCliente').val();

      var arrPost = {
        Fe_Inicio : Fe_Inicio,
        Fe_Fin : Fe_Fin,
        iIdTipoDocumento : iIdTipoDocumento,
        iIdSerieDocumento : iIdSerieDocumento,
        iNumeroDocumento : iNumeroDocumento,
        iEstadoPago : iEstadoPago,
        iIdCliente : iIdCliente,
        sNombreCliente: sNombreCliente,
        ID_Almacen: ID_Almacen,
      };
        
      if ($(this).data('type') == 'html') {
        $( '#btn-html_saldo_cliente' ).text('');
        $( '#btn-html_saldo_cliente' ).attr('disabled', true);
        $( '#btn-html_saldo_cliente' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
    
        $( '#table-saldo_cliente > tbody' ).empty();
        $( '#table-saldo_cliente > tfoot' ).empty();
        
        url = base_url + 'Ventas/informes_venta/SaldoClienteController/sendReporte';
        $.post( url, arrPost, function( response ){
          if ( response.sStatus == 'success' ) {
            var iTotalRegistros = response.arrData.length, tr_body = '', tr_foot = '', total_s = 0.00, total_s_saldo = 0.00, sum_total_s = 0.00, sum_total_s_saldo = 0.00, total_retencion;
            var $ID_Almacen = 0, $sum_almacen_total_s = 0.00, $sum_almacen_total_s_saldo = 0.00, $counter_almacen = 0;
            var response=response.arrData;
            for (var i = 0; i < iTotalRegistros; i++) {
              total_s = (!isNaN(parseFloat(response[i].Ss_Total)) ? parseFloat(response[i].Ss_Total) : 0);
              total_s_saldo = (!isNaN(parseFloat(response[i].Ss_Total_Saldo)) ? parseFloat(response[i].Ss_Total_Saldo) : 0);
              total_retencion = (!isNaN(parseFloat(response[i].Ss_Retencion)) ? parseFloat(response[i].Ss_Retencion) : 0);

              if ($ID_Almacen != response[i].ID_Almacen) {
                if ($counter_almacen != 0) {
                  tr_body +=
                    +"<tr>"
                    + "<th class='text-right' colspan='8'>Total Almacén</th>"
                    + "<th class='text-right'>" + number_format($sum_almacen_total_s, 2) + "</th>"
                    + "<th class='text-right'>" + number_format($sum_almacen_total_s_saldo, 2) + "</th>"
                    + "</tr>";
                }

                $sum_almacen_total_s = 0.000000;
                $sum_almacen_total_s_saldo = 0.00;

                tr_body +=
                  "<tr>"
                  + "<th class='text-right'><span style='font-size: 15px;'>Almacén</span></th>"
                  + "<th class='text-left' colspan='9'><span style='font-size: 15px;'>" + response[i].No_Almacen + "</span></th>"
                  + "</tr>";

                $ID_Almacen = response[i].ID_Almacen;
              }// if almacen

              tr_body +=
              "<tr>"
                +"<td class='text-center'>" + response[i].Fe_Emision + "</td>"
                +"<td class='text-center'>" + response[i].Fe_Vencimiento + "</td>"
                +"<td class='text-center'>" + response[i].Dias_Vencimiento + "</td>"
                +"<td class='text-center'>" + response[i].No_Tipo_Documento_Breve + "</td>"
                +"<td class='text-center'>" + response[i].ID_Serie_Documento + "</td>"
                +"<td class='text-center'>" + response[i].ID_Numero_Documento + "</td>"
                +"<td class='text-left'>" + response[i].No_Entidad + "</td>"
                +"<td class='text-center'>" + response[i].No_Signo + "</td>"
                +"<td class='text-right'>" + (response[i].ID_Tipo_Documento != 5 ? '' : '-') + number_format(total_s, 2) + "</td>"
                +"<td class='text-right'>" + (response[i].ID_Tipo_Documento != 5 ? '' : '-') + number_format(total_s_saldo, 2) + "</td>"
                +"<td class='text-center'><span class='label label-" + response[i].No_Class_Estado_Pago + "'>" + response[i].No_Estado_Pago + "</span></td>"
                +"<td class='text-right'>" + (response[i].ID_Tipo_Documento != 5 ? '' : '-') + number_format(total_retencion, 2) + "</td>"
                +"<td class='text-center'><span class='label label-" + response[i].No_Class_Estado + "'>" + response[i].No_Estado + "</span></td>"
              +"</tr>";
              
              sum_total_s += total_s;
              sum_total_s_saldo += total_s_saldo;

              $sum_almacen_total_s += total_s;
              $sum_almacen_total_s_saldo += total_s_saldo;
              $counter_almacen++;
            }
            
            tr_foot =
            "<tfoot>"
              + "<tr>"
                + "<th class='text-right' colspan='8'>Total Almacén</th>"
                + "<th class='text-right'>" + number_format($sum_almacen_total_s, 2) + "</th>"
                + "<th class='text-right'>" + number_format($sum_almacen_total_s_saldo, 2) + "</th>"
              + "</tr>"
              +"<tr>"
                +"<th class='text-right' colspan='8'>Total</th>"
                +"<th class='text-right'>" + number_format(sum_total_s, 2) + "</th>"
                +"<th class='text-right'>" + number_format(sum_total_s_saldo, 2) + "</th>"
              +"</tr>"
            +"</tfoot>";
          } else {
            if( response.sMessageSQL !== undefined ) {
              console.log(response.sMessageSQL);
            }
            tr_body +=
            "<tr>"
              +"<td colspan='12' class='text-center'>" + response.sMessage + "</td>"
            + "</tr>";
          } // ./ if arrData
          
          $( '#div-saldo_cliente' ).show();
          $( '#table-saldo_cliente > tbody' ).append(tr_body);
          $( '#table-saldo_cliente > tbody' ).after(tr_foot);
          
          $( '#btn-html_saldo_cliente' ).text('');
          $( '#btn-html_saldo_cliente' ).append( '<i class="fa fa-search"></i> Buscar' );
          $( '#btn-html_saldo_cliente' ).attr('disabled', false);
        }, 'JSON')
        .fail(function(jqXHR, textStatus, errorThrown) {
          $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
          
          $( '#modal-message' ).modal('show');
          $( '.modal-message' ).addClass( 'modal-danger' );
          $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
          setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
          
          //Message for developer
          console.log(jqXHR.responseText);
          
          $( '#btn-html_saldo_cliente' ).text('');
          $( '#btn-html_saldo_cliente' ).append( '<i class="fa fa-search"></i> Buscar' );
          $( '#btn-html_saldo_cliente' ).attr('disabled', false);
        });
      } else if ($(this).data('type') == 'pdf') {
        $( '#btn-pdf_saldo_cliente' ).text('');
        $( '#btn-pdf_saldo_cliente' ).attr('disabled', true);
        $( '#btn-pdf_saldo_cliente' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

        url = base_url + 'Ventas/informes_venta/SaldoClienteController/sendReportePDF/' + Fe_Inicio + '/' + Fe_Fin + '/' + iIdTipoDocumento + '/' + iIdSerieDocumento + '/' + iNumeroDocumento + '/' + iEstadoPago + '/' + iIdCliente + '/' + encodeURIComponent(sNombreCliente) + '/' + ID_Almacen;
        window.open(url,'_blank');
        
        $( '#btn-pdf_saldo_cliente' ).text('');
        $( '#btn-pdf_saldo_cliente' ).append( '<i class="fa fa-file-pdf-o color_icon_pdf"></i> PDF' );
        $( '#btn-pdf_saldo_cliente' ).attr('disabled', false);
      } else if ($(this).data('type') == 'excel') {
        $( '#btn-excel_saldo_cliente' ).text('');
        $( '#btn-excel_saldo_cliente' ).attr('disabled', true);
        $( '#btn-excel_saldo_cliente' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
        
        url = base_url + 'Ventas/informes_venta/SaldoClienteController/sendReporteEXCEL/' + Fe_Inicio + '/' + Fe_Fin + '/' + iIdTipoDocumento + '/' + iIdSerieDocumento + '/' + iNumeroDocumento + '/' + iEstadoPago + '/' + iIdCliente + '/' + encodeURIComponent(sNombreCliente) + '/' + ID_Almacen;
        window.open(url,'_blank');
        
        $( '#btn-excel_saldo_cliente' ).text('');
        $( '#btn-excel_saldo_cliente' ).append( '<i class="fa fa-file-excel-o color_icon_excel"></i> Excel' );
        $( '#btn-excel_saldo_cliente' ).attr('disabled', false);
      }// /. if all button 
    }// /. if - else validacion
  })// /. btn
})

// Ayudas - combobox
function getAlmacenes(arrParams) {
  $('#cbo-Almacenes_SaldoCliente').html('<option value="0">- Todos -</option>');
  url = base_url + 'HelperController/getAlmacenes';
  $.post(url, {}, function (responseAlmacen) {
    var iCantidadRegistros = responseAlmacen.length;
    var selected = '';
    var iIdAlmacen = 0;
    $('#cbo-Almacenes_SaldoCliente').html('<option value="0">- Todos -</option>');
    if (iCantidadRegistros == 1) {
      if (arrParams !== undefined) {
        iIdAlmacen = arrParams.ID_Almacen;
      }
      if (iIdAlmacen == responseAlmacen[0]['ID_Almacen']) {
        selected = 'selected="selected"';
      }
      $('#cbo-Almacenes_SaldoCliente').append('<option value="' + responseAlmacen[0]['ID_Almacen'] + '" ' + selected + ' data-direccion_almacen="' + responseAlmacen[0]['Txt_Direccion_Almacen'] + '">' + responseAlmacen[0]['No_Almacen'] + '</option>');
    } else {
      for (var i = 0; i < iCantidadRegistros; i++) {
        if (arrParams !== undefined) {
          iIdAlmacen = arrParams.ID_Almacen;
        }
        if (iIdAlmacen == responseAlmacen[0]['ID_Almacen']) {
          selected = 'selected="selected"';
        }
        $('#cbo-Almacenes_SaldoCliente').append('<option value="' + responseAlmacen[i]['ID_Almacen'] + '" ' + selected + ' data-direccion_almacen="' + responseAlmacen[0]['Txt_Direccion_Almacen'] + '">' + responseAlmacen[i]['No_Almacen'] + '</option>');
      }
    }
    $('#modal-loader').modal('hide');
  }, 'JSON');
}