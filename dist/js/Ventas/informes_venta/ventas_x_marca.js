var url;

$(function () {  
  $('.select2').select2();
  
  $( '#modal-loader' ).modal('show');

  $('#div-ventas_x_familia').hide();

  var arrParams = {};
  getAlmacenes(arrParams);

  url = base_url + 'HelperController/getMonedas';
  $.post(url, function (response) {
    $('#cbo-filtro_monedas').html('<option value="0" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++)
      $('#cbo-filtro_monedas').append('<option value="' + response[i].ID_Moneda + '">' + response[i].No_Moneda + '</option>');
  }, 'JSON');

  url = base_url + 'HelperController/getMarcasV2';
	$.post( url, {}, function( response ){
    if ( response.sStatus == 'success' ) {
      var iTotalRegistros = response.arrData.length, response=response.arrData;
      $( '#cbo-familia' ).html( '<option value="0" selected="selected">- Todos -</option>');
      for (var i = 0; i < iTotalRegistros; i++)
        $( '#cbo-familia' ).append( '<option value="' + response[i].ID + '">' + response[i].Nombre + '</option>' );
    } else {
      $( '#cbo-familia' ).html( '<option value="0" selected="selected">- Vacío -</option>');
      console.log( response );
    }
		$( '#modal-loader' ).modal('hide');
  }, 'JSON');
  
  $('.btn-generar_ventas_x_familia').click(function () {
    if ($('#cbo-filtro_monedas').val() == '0') {
      $('#cbo-filtro_monedas').closest('.form-group').find('.help-block').html('Seleccionar moneda');
      $('#cbo-filtro_monedas').closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      $('.form-group').removeClass('has-error');
      $('.help-block').empty();
    
      var Fe_Inicio, Fe_Fin, iIdMoneda, iIdFamilia, iIdItem, sNombreItem, iIdSubFamilia, Nu_Tipo_Impuesto;
      
      Fe_Inicio = ParseDateString($( '#txt-Filtro_Fe_Inicio' ).val(), 1, '/');
      Fe_Fin = ParseDateString($( '#txt-Filtro_Fe_Fin' ).val(), 1, '/');
      iIdMoneda = $( '#cbo-filtro_monedas' ).val();
      iIdFamilia = $('#cbo-familia').val();
      iIdItem = ($('#txt-ID_Producto').val().length === 0 ? '-' : $('#txt-ID_Producto').val());
      sNombreItem = ($('#txt-No_Producto').val().length === 0 ? '-' : $('#txt-No_Producto').val());
      iIdSubFamilia = $('#cbo-sub_categoria').val();
      ID_Almacen = $('#cbo-Almacenes_VentasxMarca').val();
      Nu_Tipo_Impuesto = $('#cbo-regalo').val();

      var arrPost = {
        Fe_Inicio : Fe_Inicio,
        Fe_Fin: Fe_Fin,
        iIdMoneda: iIdMoneda,
        iIdFamilia: iIdFamilia,
        iIdItem: iIdItem,
        sNombreItem: sNombreItem,
        iIdSubFamilia: iIdSubFamilia,
        ID_Almacen: ID_Almacen,
        Nu_Tipo_Impuesto:Nu_Tipo_Impuesto
      };
        
      if ($(this).data('type') == 'html') {
        $( '#btn-html_ventas_x_familia' ).text('');
        $( '#btn-html_ventas_x_familia' ).attr('disabled', true);
        $( '#btn-html_ventas_x_familia' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
    
        $( '#table-ventas_x_familia > tbody' ).empty();
        $( '#table-ventas_x_familia > tfoot' ).empty();
        
        url = base_url + 'Ventas/informes_venta/VentasxMarcaController/sendReporte';
        $.post( url, arrPost, function( response ){
          if ( response.sStatus == 'success' ) {
            var iTotalRegistros = response.arrData.length, tr_body = '', tr_foot = '', counter = 0, ID_Marca = '', cantidad = 0.00, subtotal = 0.00, impuesto = 0.00, total_s = 0.00;
            var sum_cantidad = 0.00, sum_subtotal = 0.00, sum_impuesto = 0.00, sum_total_s = 0.00;
            var sum_general_cantidad = 0.00, sum_general_subtotal = 0.00, sum_general_impuesto = 0.00, sum_general_total_s = 0.00;
            var $ID_Almacen = 0, $counter_almacen = 0, $sum_almacen_cantidad = 0.000000, $sum_almacen_subtotal = 0.00, $sum_impuesto = 0.00, $sum_almacen_total_s = 0.00;
            var response=response.arrData;
            for (var i = 0; i < iTotalRegistros; i++) {
              if (ID_Marca != response[i].ID_Marca || $ID_Almacen != response[i].ID_Almacen) {
                if (counter != 0) {
                  tr_body +=
                  +"<tr>"
                    +"<th class='text-right' colspan='9'>Total </th>"
                    +"<th class='text-right'>" + number_format(sum_cantidad, 3) + "</th>"
                    +"<th class='text-right'></th>"
                    +"<th class='text-right'>" + number_format(sum_subtotal, 2) + "</th>"
                    +"<th class='text-right'>" + number_format(sum_impuesto, 2) + "</th>"
                    +"<th class='text-right'>" + number_format(sum_total_s, 2) + "</th>"
                  +"</tr>";
                  sum_cantidad = 0.00;
                  sum_subtotal = 0.00;
                  sum_impuesto = 0.00;
                  sum_total_s = 0.00;
                }

                if ($ID_Almacen != response[i].ID_Almacen) {
                  if ($counter_almacen != 0) {
                    tr_body +=
                      +"<tr>"
                      + "<th class='text-right' colspan='9'>Total Almacén</th>"
                      + "<th class='text-right'>" + number_format($sum_almacen_cantidad, 3) + "</th>"
                      + "<th class='text-right'></th>"
                      + "<th class='text-right'>" + number_format($sum_almacen_subtotal, 2) + "</th>"
                      + "<th class='text-right'>" + number_format($sum_almacen_impuesto, 2) + "</th>"
                      + "<th class='text-right'>" + number_format($sum_almacen_total_s, 2) + "</th>"
                      + "</tr>";
                  }

                  $sum_almacen_cantidad = 0.000000;
                  $sum_almacen_subtotal = 0.00;
                  $sum_almacen_impuesto = 0.00;
                  $sum_almacen_total_s = 0.00;

                  tr_body +=
                    "<tr>"
                    + "<th class='text-right'><span style='font-size: 15px;'>Almacén</span></th>"
                    + "<th class='text-left' colspan='14'><span style='font-size: 15px;'>" + response[i].No_Almacen + "</span></th>"
                    + "</tr>";

                  $ID_Almacen = response[i].ID_Almacen;
                }// if almacen

                tr_body +=
                "<tr>"
                  + "<th class='text-center'>Marca</th>"
                  + "<th class='text-left' colspan='14'>" + response[i].No_Marca + "</th>"
                + "</tr>";
                ID_Marca = response[i].ID_Marca;
              }

              cantidad = (!isNaN(parseFloat(response[i].Qt_Producto)) ? parseFloat(response[i].Qt_Producto) : 0);
              subtotal = (!isNaN(parseFloat(response[i].Ss_Subtotal)) ? parseFloat(response[i].Ss_Subtotal) : 0);
              impuesto = (!isNaN(parseFloat(response[i].Ss_Impuesto)) ? parseFloat(response[i].Ss_Impuesto) : 0);
              total_s = (!isNaN(parseFloat(response[i].Ss_Total)) ? parseFloat(response[i].Ss_Total) : 0);

              tr_body +=
              "<tr>"
                +"<td class='text-center'>" + response[i].Fe_Emision_Hora + "</td>"
                +"<td class='text-center'>" + response[i].No_Tipo_Documento_Breve + "</td>"
                +"<td class='text-center'>" + response[i].ID_Serie_Documento + "</td>"
                +"<td class='text-center'>" + response[i].ID_Numero_Documento + "</td>"
                +"<td class='text-left'>" + response[i].No_Entidad + "</td>"
                +"<td class='text-center'>" + response[i].No_Signo + "</td>"
                +"<td class='text-right'>" + number_format(response[i].Ss_Tipo_Cambio, 3) + "</td>"
                +"<td class='text-center'>" + response[i].No_Unidad_Medida + "</td>"
                +"<td class='text-left'>" + response[i].No_Producto + "</td>"
                +"<td class='text-right'>" + (response[i].ID_Tipo_Documento != 5 ? '' : '-') + number_format(cantidad, 3) + "</td>"
                +"<td class='text-right'>" + (response[i].ID_Tipo_Documento != 5 ? '' : '-') + number_format(response[i].Ss_Precio, 2) + "</td>"
                +"<td class='text-right'>" + (response[i].ID_Tipo_Documento != 5 ? '' : '-') + number_format(subtotal, 2) + "</td>"
                +"<td class='text-right'>" + (response[i].ID_Tipo_Documento != 5 ? '' : '-') + number_format(impuesto, 2) + "</td>"
                +"<td class='text-right'>" + (response[i].ID_Tipo_Documento != 5 ? '' : '-') + number_format(total_s, 2) + "</td>"
                +"<td class='text-center'><span class='label label-" + response[i].No_Class_Estado + "'>" + response[i].No_Estado + "</span></td>"
              +"</tr>";
              
              sum_cantidad += cantidad;
              sum_subtotal += subtotal;
              sum_impuesto += impuesto;
              sum_total_s += total_s;

              $sum_almacen_cantidad += cantidad;
              $sum_almacen_subtotal += subtotal;
              $sum_almacen_impuesto += impuesto;
              $sum_almacen_total_s += total_s;
              
              sum_general_cantidad += cantidad;
              sum_general_subtotal += subtotal;
              sum_general_impuesto += impuesto;
              sum_general_total_s += total_s;

              counter++;
              $counter_almacen++;
            }
            
            tr_foot =
            "<tfoot>"
              +"<tr>"
                +"<th class='text-right' colspan='9'>Total</th>"
                +"<th class='text-right'>" + number_format(sum_cantidad, 3) + "</th>"
                +"<th class='text-right'></th>"
                +"<th class='text-right'>" + number_format(sum_subtotal, 2) + "</th>"
                +"<th class='text-right'>" + number_format(sum_impuesto, 2) + "</th>"
                +"<th class='text-right'>" + number_format(sum_total_s, 2) + "</th>"
                +"<th class='text-right'></th>"
              + "</tr>"
              + "<tr>"
                + "<th class='text-right' colspan='9'>Total Almacén</th>"
                + "<th class='text-right'>" + number_format($sum_almacen_cantidad, 3) + "</th>"
                + "<th class='text-right'></th>"
                + "<th class='text-right'>" + number_format($sum_almacen_subtotal, 2) + "</th>"
                + "<th class='text-right'>" + number_format($sum_almacen_impuesto, 2) + "</th>"
                + "<th class='text-right'>" + number_format($sum_almacen_total_s, 2) + "</th>"
              + "</tr>"
              +"<tr>"
                +"<th class='text-right' colspan='9'>Total General</th>"
                +"<th class='text-right'>" + number_format(sum_general_cantidad, 3) + "</th>"
                +"<th class='text-right'></th>"
                +"<th class='text-right'>" + number_format(sum_general_subtotal, 2) + "</th>"
                +"<th class='text-right'>" + number_format(sum_general_impuesto, 2) + "</th>"
                +"<th class='text-right'>" + number_format(sum_general_total_s, 2) + "</th>"
                +"<th class='text-right'></th>"
              +"</tr>"
            +"</tfoot>";
          } else {
            if( response.sMessageSQL !== undefined ) {
              console.log(response.sMessageSQL);
            }
            tr_body +=
            "<tr>"
              +"<td colspan='15' class='text-center'>" + response.sMessage + "</td>"
            + "</tr>";
          } // ./ if arrData
          
          $( '#div-ventas_x_familia' ).show();
          $( '#table-ventas_x_familia > tbody' ).append(tr_body);
          $( '#table-ventas_x_familia > tbody' ).after(tr_foot);
          
          $( '#btn-html_ventas_x_familia' ).text('');
          $( '#btn-html_ventas_x_familia' ).append( '<i class="fa fa-search"></i> Buscar' );
          $( '#btn-html_ventas_x_familia' ).attr('disabled', false);
        }, 'JSON')
        .fail(function(jqXHR, textStatus, errorThrown) {
          $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
          
          $( '#modal-message' ).modal('show');
          $( '.modal-message' ).addClass( 'modal-danger' );
          $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
          setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
          
          //Message for developer
          console.log(jqXHR.responseText);
          
          $( '#btn-html_ventas_x_familia' ).text('');
          $( '#btn-html_ventas_x_familia' ).append( '<i class="fa fa-search"></i> Buscar' );
          $( '#btn-html_ventas_x_familia' ).attr('disabled', false);
        });
      } else if ($(this).data('type') == 'pdf') {
        $( '#btn-pdf_ventas_x_familia' ).text('');
        $( '#btn-pdf_ventas_x_familia' ).attr('disabled', true);
        $( '#btn-pdf_ventas_x_familia' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
            
        url = base_url + 'Ventas/informes_venta/VentasxMarcaController/sendReportePDF/' + Fe_Inicio + '/' + Fe_Fin + '/' + iIdMoneda + '/' + iIdFamilia + '/' + iIdItem + '/' + encodeURIComponent(sNombreItem) + '/' + iIdSubFamilia + '/' + ID_Almacen + '/' + Nu_Tipo_Impuesto;
        window.open(url,'_blank');
        
        $( '#btn-pdf_ventas_x_familia' ).text('');
        $( '#btn-pdf_ventas_x_familia' ).append( '<i class="fa fa-file-pdf-o color_icon_pdf"></i> PDF' );
        $( '#btn-pdf_ventas_x_familia' ).attr('disabled', false);
      } else if ($(this).data('type') == 'excel') {
        $( '#btn-excel_ventas_x_familia' ).text('');
        $( '#btn-excel_ventas_x_familia' ).attr('disabled', true);
        $( '#btn-excel_ventas_x_familia' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
        
        url = base_url + 'Ventas/informes_venta/VentasxMarcaController/sendReporteEXCEL/' + Fe_Inicio + '/' + Fe_Fin + '/' + iIdMoneda + '/' + iIdFamilia + '/' + iIdItem + '/' + encodeURIComponent(sNombreItem) + '/' + iIdSubFamilia + '/' + ID_Almacen + '/' + Nu_Tipo_Impuesto;
        window.open(url,'_blank');
        
        $( '#btn-excel_ventas_x_familia' ).text('');
        $( '#btn-excel_ventas_x_familia' ).append( '<i class="fa fa-file-excel-o color_icon_excel"></i> Excel' );
        $( '#btn-excel_ventas_x_familia' ).attr('disabled', false);
      }// ./ if
    }
  })//./ btn
})

// Ayudas - combobox
function getAlmacenes(arrParams) {
  $('#cbo-Almacenes_VentasxMarca').html('<option value="0">- Todos -</option>');
  url = base_url + 'HelperController/getAlmacenes';
  $.post(url, {}, function (responseAlmacen) {
    var iCantidadRegistros = responseAlmacen.length;
    var selected = '';
    var iIdAlmacen = 0;
    $('#cbo-Almacenes_VentasxMarca').html('<option value="0">- Todos -</option>');
    if (iCantidadRegistros == 1) {
      if (arrParams !== undefined) {
        iIdAlmacen = arrParams.ID_Almacen;
      }
      if (iIdAlmacen == responseAlmacen[0]['ID_Almacen']) {
        selected = 'selected="selected"';
      }
      $('#cbo-Almacenes_VentasxMarca').append('<option value="' + responseAlmacen[0]['ID_Almacen'] + '" ' + selected + ' data-direccion_almacen="' + responseAlmacen[0]['Txt_Direccion_Almacen'] + '">' + responseAlmacen[0]['No_Almacen'] + '</option>');
    } else {
      for (var i = 0; i < iCantidadRegistros; i++) {
        if (arrParams !== undefined) {
          iIdAlmacen = arrParams.ID_Almacen;
        }
        if (iIdAlmacen == responseAlmacen[0]['ID_Almacen']) {
          selected = 'selected="selected"';
        }
        $('#cbo-Almacenes_VentasxMarca').append('<option value="' + responseAlmacen[i]['ID_Almacen'] + '" ' + selected + ' data-direccion_almacen="' + responseAlmacen[0]['Txt_Direccion_Almacen'] + '">' + responseAlmacen[i]['No_Almacen'] + '</option>');
      }
    }
    $('#modal-loader').modal('hide');
  }, 'JSON');
}