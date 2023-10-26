var url;

function ReloadReporte(){     
  $( '#btn-reload' ).text('');
  $( '#btn-reload' ).attr('disabled', true);
  $( '#btn-reload' ).append( 'Actulizando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
  
  $( '#div-RegistroVentaeIngresos' ).show();
  url = base_url + 'Ventas/informes_venta/ReporteUtilidadBrutaController/ReporteUtilidadLista';
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

  $('#modal-loader').modal('show');

  $('#div-ventas_detalladas_generales').hide();

  var arrParams = {};
  getAlmacenes(arrParams);

  url = base_url + 'HelperController/getMonedas';
  $.post(url, function (response) {
    $('#cbo-filtro_monedas').html('<option value="0" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++)
      $('#cbo-filtro_monedas').append('<option value="' + response[i].ID_Moneda + '">' + response[i].No_Moneda + '</option>');
  }, 'JSON');

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

 $( '#btn-reload' ).click(ReloadReporte);

  $("#btn-generar").click(function(){

     var ID_Almacen, Fe_Inicio, Fe_Fin, ID_Moneda, Nu_Impuesto, iIdFamilia;

     if ($('#cbo-filtro_monedas').val() == '0') {
      $('#cbo-filtro_monedas').closest('.form-group').find('.help-block').html('Seleccionar moneda');
      $('#cbo-filtro_monedas').closest('.form-group').removeClass('has-success').addClass('has-error');
      return false;
    } else {
      $('.form-group').removeClass('has-error');
      $('.help-block').empty();
    }

      $( '#btn-generar' ).text('');
      $( '#btn-generar' ).attr('disabled', true);
      $( '#btn-generar' ).append( 'Generando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

      Fe_Inicio = ParseDateString($( '#txt-Filtro_Fe_Inicio' ).val(), 1, '/');
      Fe_Fin = ParseDateString($( '#txt-Filtro_Fe_Fin' ).val(), 1, '/');
      ID_Moneda = $('#cbo-filtro_monedas').val();
      Nu_Impuesto = $('#cbo-filtro_impuesto').val();
      iIdFamilia = $('#cbo-familia').val();
      iIdItem = ($('#txt-ID_Producto').val().length === 0 ? '-' : $('#txt-ID_Producto').val());
      sNombreItem = ($('#txt-No_Producto').val().length === 0 ? '-' : $('#txt-No_Producto').val());
      iIdSubFamilia = $('#cbo-sub_categoria').val();
      ID_Almacen = $('#cbo-Almacenes_ReporteUtilidadBruta').val();
      Nu_Tipo_Formato = $("input[name='Nu_Tipo_Formato']:checked").val();

        var arrPost = {
          Fe_Inicio : Fe_Inicio,
          Fe_Fin : Fe_Fin,
          ID_Moneda: ID_Moneda,
          iIdFamilia: iIdFamilia,
          Nu_Impuesto: Nu_Impuesto,
          iIdItem: iIdItem,
          sNombreItem: sNombreItem,
          iIdSubFamilia: iIdSubFamilia,
          ID_Almacen: ID_Almacen,
          Nu_Tipo_Formato:Nu_Tipo_Formato
        };      

     url = base_url + 'Ventas/informes_venta/ReporteUtilidadBrutaController/CrearReporte';
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

  $('.btn-generar_ventas_detalladas_generales').click(function () {
    if ($('#cbo-filtro_monedas').val() == '0') {
      $('#cbo-filtro_monedas').closest('.form-group').find('.help-block').html('Seleccionar moneda');
      $('#cbo-filtro_monedas').closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      $('.form-group').removeClass('has-error');
      $('.help-block').empty();

      var ID_Almacen, Fe_Inicio, Fe_Fin, ID_Moneda, Nu_Impuesto, iIdFamilia;
      
      Fe_Inicio = ParseDateString($( '#txt-Filtro_Fe_Inicio' ).val(), 1, '/');
      Fe_Fin = ParseDateString($( '#txt-Filtro_Fe_Fin' ).val(), 1, '/');
      ID_Moneda = $('#cbo-filtro_monedas').val();
      Nu_Impuesto = $('#cbo-filtro_impuesto').val();
      iIdFamilia = $('#cbo-familia').val();
      iIdItem = ($('#txt-ID_Producto').val().length === 0 ? '-' : $('#txt-ID_Producto').val());
      sNombreItem = ($('#txt-No_Producto').val().length === 0 ? '-' : $('#txt-No_Producto').val());
      iIdSubFamilia = $('#cbo-sub_categoria').val();
      ID_Almacen = $('#cbo-Almacenes_ReporteUtilidadBruta').val();
      Nu_Tipo_Formato = $("input[name='Nu_Tipo_Formato']:checked").val();

      if ($(this).data('type') == 'html') {
        $( '#btn-html_ventas_detalladas_generales' ).text('');
        $( '#btn-html_ventas_detalladas_generales' ).attr('disabled', true);
        $( '#btn-html_ventas_detalladas_generales' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
    
        $( '#table-ventas_detalladas_generales > tbody' ).empty();
        $( '#table-ventas_detalladas_generales > tfoot' ).empty();

        var arrPost = {
          Fe_Inicio : Fe_Inicio,
          Fe_Fin : Fe_Fin,
          ID_Moneda: ID_Moneda,
          iIdFamilia: iIdFamilia,
          Nu_Impuesto: Nu_Impuesto,
          iIdItem: iIdItem,
          sNombreItem: sNombreItem,
          iIdSubFamilia: iIdSubFamilia,
          ID_Almacen: ID_Almacen,
          Nu_Tipo_Formato:Nu_Tipo_Formato
        };      
        url = base_url + 'Ventas/informes_venta/ReporteUtilidadBrutaController/sendReporte';
        $.post( url, arrPost, function( response ){
          console.log( response );
          if ( response.sStatus == 'success' ) {
            var iTotalRegistros = response.arrData.length, response=response.arrData, tr_body = '', tr_foot = '', ID_Familia = '', counter = 0;
            var sum_cantidad = 0.00, sum_total = 0.00, sum_total_descuento = 0.00, sum_total_utilidad_neta = 0.00;
            var fTtotalCantidad = 0.00, fTotalGeneral = 0.00, fTotalDescuentoGeneral = 0.00, fTotalUtilidadNetaGeneral = 0.00;
            var $ID_Almacen = 0, $fTtotalCantidadAlmacen = 0.00, $fTotalGeneralAlmacen = 0.00, $fTotalDescuentoGeneralAlmacen = 0.00, $fTotalUtilidadNetaGeneralAlmacen = 0.00, $counter_almacen = 0;
            for (var i = 0; i < iTotalRegistros; i++) {
              if (ID_Familia != response[i].ID_Familia || $ID_Almacen != response[i].ID_Almacen) {
                if (counter != 0) {
                  tr_body +=
                    +"<tr>"
                    + "<th class='text-right' colspan='7'>Total</th>"
                    + "<th class='text-right'>" + number_format(sum_cantidad, 3) + "</th>"
                    + "<th class='text-right'>" + number_format(sum_total, 2) + "</th>"
                    + "<th class='text-right'>" + number_format(sum_total_descuento, 2) + "</th>"
                    + "<th class='text-right'>" + number_format(sum_total_utilidad_neta, 2) + "</th>"
                    + "</tr>";
                  sum_cantidad = 0.00;
                  sum_total = 0.00;
                  sum_total_descuento = 0.00;
                  sum_total_utilidad_neta = 0.00;
                }

                if ($ID_Almacen != response[i].ID_Almacen) {
                  if ($counter_almacen != 0) {
                    tr_body +=
                    +"<tr>"
                      + "<th class='text-right' colspan='7'>Total Almacén</th>"
                      + "<th class='text-right'>" + number_format($fTtotalCantidadAlmacen, 3) + "</th>"
                      + "<th class='text-right'>" + number_format($fTotalGeneralAlmacen, 2) + "</th>"
                      + "<th class='text-right'>" + number_format($fTotalDescuentoGeneralAlmacen, 2) + "</th>"
                      + "<th class='text-right'>" + number_format($fTotalUtilidadNetaGeneralAlmacen, 2) + "</th>"
                    + "</tr>";
                  }

                  $fTtotalCantidadAlmacen = 0.00;
                  $fTotalGeneralAlmacen = 0.00;
                  $fTotalDescuentoGeneralAlmacen = 0.00;
                  $fTotalUtilidadNetaGeneralAlmacen = 0.00;

                  tr_body +=
                    "<tr>"
                    + "<th class='text-right'><span style='font-size: 15px;'>Almacén</span></th>"
                    + "<th class='text-left' colspan='15'><span style='font-size: 15px;'>" + response[i].No_Almacen + "</span></th>"
                    + "</tr>";

                  $ID_Almacen = response[i].ID_Almacen;
                }// if almacen

                tr_body +=
                  "<tr>"
                  + "<th class='text-center'>Categoría</th>"
                  + "<th class='text-left' colspan='13'>" + response[i].No_Familia + "</th>"
                  + "</tr>";
                ID_Familia = response[i].ID_Familia;
              }

              tr_body +=
              "<tr>"
                +"<td class='text-left'>" + response[i].Nu_Codigo_Barra + "</td>"
                +"<td class='text-left'>" + response[i].No_Producto + "</td>"
                +"<td class='text-center'>" + response[i].No_Signo + "</td>"
                + "<td class='text-right'>" + (parseFloat(response[i].Ss_Costo) < 0 ? '-' : '') + number_format(parseFloat(response[i].Ss_Costo), 2) + "</td>"
                + "<td class='text-right'>" + (parseFloat(response[i].Ss_Precio) < 0 ? '-' : '') + number_format(parseFloat(response[i].Ss_Precio), 2) + "</td>"
                +"<td class='text-right'>" + response[i].Ss_Ganancia + "</td>"
                +"<td class='text-right'>" + response[i].Po_Margen_Ganancia + "</td>"
                +"<td class='text-right'>" + (parseFloat(response[i].Qt_Producto) < 0 ? '-' : '') + number_format(parseFloat(response[i].Qt_Producto), 3) + "</td>"
                +"<td class='text-right'>" + Math.round10(response[i].Ss_Utilidad, -2) + "</td>"
                +"<td class='text-right'>" + Math.round10(response[i].Ss_Descuento, -2) + "</td>"
                +"<td class='text-right'>" + Math.round10(response[i].Ss_Utilidad_Neta, -2) + "</td>"
              +"</tr>";

              sum_cantidad += parseFloat(response[i].Qt_Producto);
              sum_total += parseFloat(response[i].Ss_Utilidad);
              sum_total_descuento+= parseFloat(response[i].Ss_Descuento);
              sum_total_utilidad_neta += parseFloat(response[i].Ss_Utilidad_Neta);

              $fTtotalCantidadAlmacen += parseFloat(response[i].Qt_Producto);
              $fTotalGeneralAlmacen += parseFloat(response[i].Ss_Utilidad);
              $fTotalDescuentoGeneralAlmacen += parseFloat(response[i].Ss_Descuento);
              $fTotalUtilidadNetaGeneralAlmacen += parseFloat(response[i].Ss_Utilidad_Neta);

              fTtotalCantidad += parseFloat(response[i].Qt_Producto);
              fTotalGeneral += parseFloat(response[i].Ss_Utilidad);
              fTotalDescuentoGeneral += parseFloat(response[i].Ss_Descuento);
              fTotalUtilidadNetaGeneral += parseFloat(response[i].Ss_Utilidad_Neta);

              counter++;
              $counter_almacen++;
            }            
            tr_foot =
            "<tfoot>"
              + "<tr>"
                + "<th class='text-right' colspan='7'>Total</th>"
                + "<th class='text-right'>" + Math.round10(sum_cantidad, -3) + "</th>"
                + "<th class='text-right'>" + Math.round10(sum_total, -2) + "</th>"
                + "<th class='text-right'>" + Math.round10(sum_total_descuento, -2) + "</th>"
                + "<th class='text-right'>" + Math.round10(sum_total_utilidad_neta, -2) + "</th>"
              + "</tr>"
              + "<tr>"
                + "<th class='text-right' colspan='7'>Total Almacén</th>"
                + "<th class='text-right'>" + Math.round10($fTtotalCantidadAlmacen, -3) + "</th>"
                + "<th class='text-right'>" + Math.round10($fTotalGeneralAlmacen, -2) + "</th>"
                + "<th class='text-right'>" + Math.round10($fTotalDescuentoGeneralAlmacen, -2) + "</th>"
                + "<th class='text-right'>" + Math.round10($fTotalUtilidadNetaGeneralAlmacen, -2) + "</th>"
              + "</tr>"
              +"<tr>"
                + "<th class='text-right' colspan='7'>Total General</th>"
                + "<th class='text-right'>" + Math.round10(fTtotalCantidad, -3) + "</th>"
                +"<th class='text-right'>" + Math.round10(fTotalGeneral, -2) + "</th>"
                +"<th class='text-right'>" + Math.round10(fTotalDescuentoGeneral, -2) + "</th>"
                +"<th class='text-right'>" + Math.round10(fTotalUtilidadNetaGeneral, -2) + "</th>"
              +"</tr>"
            +"</tfoot>";
          } else {
            if( response.sMessageSQL !== undefined ) {
              console.log(response.sMessageSQL);
            }
            tr_body +=
            "<tr>"
              +"<td colspan='9' class='text-center'>" + response.sMessage + "</td>"
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
        // $( '#btn-pdf_ventas_detalladas_generales' ).text('');
        // $( '#btn-pdf_ventas_detalladas_generales' ).attr('disabled', true);
        // $( '#btn-pdf_ventas_detalladas_generales' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
            
        // url = base_url + 'Ventas/informes_venta/ReporteUtilidadBrutaController/sendReportePDF/' + Fe_Inicio + '/' + Fe_Fin + '/' + ID_Moneda + '/' + iIdFamilia + '/' + Nu_Impuesto + '/' + iIdItem + '/' + encodeURIComponent(sNombreItem) + '/' + iIdSubFamilia + '/' + ID_Almacen;
        // window.open(url,'_blank');
        
        // $( '#btn-pdf_ventas_detalladas_generales' ).text('');
        // $( '#btn-pdf_ventas_detalladas_generales' ).append( '<i class="fa fa-file-pdf-o color_white"></i> PDF' );
        // $( '#btn-pdf_ventas_detalladas_generales' ).attr('disabled', false);
      } else if ($(this).data('type') == 'excel') {
        // $( '#btn-excel_ventas_detalladas_generales' ).text('');
        // $( '#btn-excel_ventas_detalladas_generales' ).attr('disabled', true);
        // $( '#btn-excel_ventas_detalladas_generales' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
        
         url = base_url + 'Ventas/informes_venta/ReporteUtilidadBrutaController/CrearReporte';
        // window.open(url,'_blank');
        //arrPost.sNombreItem = encodeURIComponent(sNombreItem);
        // $( '#btn-excel_ventas_detalladas_generales' ).text('');
        // $( '#btn-excel_ventas_detalladas_generales' ).append( '<i class="fa fa-file-excel-o color_white"></i> Excel' );
        // $( '#btn-excel_ventas_detalladas_generales' ).attr('disabled', false);

        $.post( url, arrPost, function( data ) {
          
          
        }, "json");

      }// /. if
    }
  })// /. btn


 $(document).on("click",".btn-download",function(){
    window.open(base_url + 'Ventas/informes_venta/ReporteUtilidadBrutaController/BajarReporte/'+$(this).data("valor"), "_blank");
  });

  $(document).on("click",".btn-cancelar",function(){
    url = base_url + 'Ventas/informes_venta/ReporteUtilidadBrutaController/CancelarReporte';
    $.post( url, {"ID_Reporte":$(this).data("valor")}, function( response ){
      ReloadReporte();
    },"json");
  });

  $( '#btn-reload' ).trigger("click");

})

// Ayudas - combobox
function getAlmacenes(arrParams) {
  $('#cbo-Almacenes_ReporteUtilidadBruta').html('<option value="0">- Todos -</option>');
  url = base_url + 'HelperController/getAlmacenes';
  $.post(url, {}, function (responseAlmacen) {
    var iCantidadRegistros = responseAlmacen.length;
    var selected = '';
    var iIdAlmacen = 0;
    $('#cbo-Almacenes_ReporteUtilidadBruta').html('<option value="0">- Todos -</option>');
    if (iCantidadRegistros == 1) {
      if (arrParams !== undefined) {
        iIdAlmacen = arrParams.ID_Almacen;
      }
      if (iIdAlmacen == responseAlmacen[0]['ID_Almacen']) {
        selected = 'selected="selected"';
      }
      $('#cbo-Almacenes_ReporteUtilidadBruta').append('<option value="' + responseAlmacen[0]['ID_Almacen'] + '" ' + selected + ' data-direccion_almacen="' + responseAlmacen[0]['Txt_Direccion_Almacen'] + '">' + responseAlmacen[0]['No_Almacen'] + '</option>');
    } else {
      for (var i = 0; i < iCantidadRegistros; i++) {
        if (arrParams !== undefined) {
          iIdAlmacen = arrParams.ID_Almacen;
        }
        if (iIdAlmacen == responseAlmacen[0]['ID_Almacen']) {
          selected = 'selected="selected"';
        }
        $('#cbo-Almacenes_ReporteUtilidadBruta').append('<option value="' + responseAlmacen[i]['ID_Almacen'] + '" ' + selected + ' data-direccion_almacen="' + responseAlmacen[0]['Txt_Direccion_Almacen'] + '">' + responseAlmacen[i]['No_Almacen'] + '</option>');
      }
    }
    $('#modal-loader').modal('hide');
  }, 'JSON');
}