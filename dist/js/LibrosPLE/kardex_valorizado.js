var url;

function ReloadReporte(){     
  $( '#btn-reload' ).text('');
  $( '#btn-reload' ).attr('disabled', true);
  $( '#btn-reload' ).append( 'Actualizando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
  
  $( '#div-RegistroVentaeIngresos' ).show();
  url = base_url + 'LibrosPLE/KardexValorizadoController/ReporteKardexValorizadoLista';
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

  url = base_url + 'LibrosPLE/KardexController/getTiposLibroSunat';
  $.post(url, { ID_Tipo_Asiento: 4 }, function (response) {
    if (response.length == 1) {
      $('#cbo-TiposLibroSunatKardex').append('<option value="' + response[0].ID_Tipo_Asiento_Detalle + '" data-id_tipo_asiento="' + response[0].ID_Tipo_Asiento + '" data-nu_codigo_libro_sunat="' + response[0].Nu_Codigo_Libro_Sunat + '" data-no_tipo_asiento_apertura="' + response[0].No_Tipo_Asiento_Apertura + '">' + response[0].No_Sub_Libro_Sunat + '</option>');
    } else {
      $('#cbo-TiposLibroSunatKardex').html('<option value="0" selected="selected">- Seleccionar -</option>');
      for (var i = 0, len = response.length; i < len; i++)
        $('#cbo-TiposLibroSunatKardex').append('<option value="' + response[i].ID_Tipo_Asiento_Detalle + '" data-id_tipo_asiento="' + response[i].ID_Tipo_Asiento + '" data-nu_codigo_libro_sunat="' + response[i].Nu_Codigo_Libro_Sunat + '" data-no_tipo_asiento_apertura="' + response[i].No_Tipo_Asiento_Apertura + '">' + response[i].No_Sub_Libro_Sunat + '</option>');
    }
  }, 'JSON');

  var arrParams = {};
  getAlmacenes(arrParams);

  url = base_url + 'HelperController/getTipoMovimiento';
  $.post(url, { Nu_Tipo_Movimiento: 3 }, function (responseTiposMovimiento) {
    $('#cbo-filtro_tipo_movimiento').html('<option value="0" selected="selected">- Todos -</option>');
    for (var i = 0; i < responseTiposMovimiento.length; i++) {
      $('#cbo-filtro_tipo_movimiento').append('<option value="' + responseTiposMovimiento[i]['ID_Tipo_Movimiento'] + '">' + responseTiposMovimiento[i]['No_Tipo_Movimiento'] + '</option>');
    }
  }, 'JSON');

  var arrParams = {};
  getAlmacenes(arrParams);

  $('.div-productos').hide();
  $('#txt-ID_Producto').val(0);
  $('#cbo-FiltrosProducto').change(function () {
    $('.div-productos').hide();
    $('#txt-ID_Producto').val(0);
    $('#txt-No_Producto').val('');
    if ($(this).val() > 0)
      $('.div-productos').show();
  })

  $( '#btn-reload' ).click(ReloadReporte);

  $("#btn-generar").click(function(){

     var ID_Tipo_Asiento, ID_Tipo_Asiento_Detalle, ID_Almacen, dInicio, dFin, ID_Producto, Txt_Direccion_Almacen, Nu_Codigo_Libro_Sunat, No_Tipo_Asiento_Apertura, ID_Tipo_Movimiento;

   if ($('#cbo-TiposLibroSunatKardex').val() == 0) {
      $('#cbo-TiposLibroSunatKardex').closest('.form-group').find('.help-block').html('Seleccionar libro');
      $('#cbo-TiposLibroSunatKardex').closest('.form-group').removeClass('has-success').addClass('has-error');
      return false;
    } 
      $('.help-block').empty();
      $('.form-group').removeClass('has-error');

      $( '#btn-generar' ).text('');
      $( '#btn-generar' ).attr('disabled', true);
      $( '#btn-generar' ).append( 'Generando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

       ID_Tipo_Asiento = $('#cbo-TiposLibroSunatKardex').find(':selected').data('id_tipo_asiento');
      ID_Tipo_Asiento_Detalle = $('#cbo-TiposLibroSunatKardex').val();
      ID_Almacen = $('#cbo-Almacenes_filtro_kardex').val();
      dInicio = ParseDateString($('#txt-Filtro_Fe_Inicio').val(), 1, '/');
      dFin = ParseDateString($('#txt-Filtro_Fe_Fin').val(), 1, '/');
      ID_Producto = $('#txt-ID_Producto').val();
      Txt_Direccion_Almacen = $('#cbo-Almacenes_filtro_kardex').find(':selected').data('direccion_almacen');
      Nu_Codigo_Libro_Sunat = $('#cbo-TiposLibroSunatKardex').find(':selected').data('nu_codigo_libro_sunat');
      No_Tipo_Asiento_Apertura = $('#cbo-TiposLibroSunatKardex').find(':selected').data('no_tipo_asiento_apertura');
      ID_Tipo_Movimiento = $('#cbo-filtro_tipo_movimiento').val();
      Nu_Tipo_Formato = $("input[name='Nu_Tipo_Formato']:checked").val();

      var arrPost = {
          ID_Tipo_Asiento: ID_Tipo_Asiento,
          ID_Tipo_Asiento_Detalle: ID_Tipo_Asiento_Detalle,
          ID_Almacen: ID_Almacen,
          dInicio: dInicio,
          dFin: dFin,
          ID_Producto: ID_Producto,
          ID_Tipo_Movimiento: ID_Tipo_Movimiento,
          Nu_Tipo_Formato:Nu_Tipo_Formato
        };     

     url = base_url + 'LibrosPLE/KardexValorizadoController/CrearReporte';
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

  $(document).on("click",".btn-download",function(){
    window.open(base_url + 'LibrosPLE/KardexValorizadoController/BajarReporte/'+$(this).data("valor"), "_blank");
  });

  $(document).on("click",".btn-cancelar",function(){
    url = base_url + 'LibrosPLE/KardexValorizadoController/CancelarReporte';
    $.post( url, {"ID_Reporte":$(this).data("valor")}, function( response ){
      ReloadReporte();
    },"json");
  });
  

  $('#table-Kardex').hide();

  $('.btn-generar_kardex').click(function () {
    if ($('#cbo-TiposLibroSunatKardex').val() == 0) {
      $('#cbo-TiposLibroSunatKardex').closest('.form-group').find('.help-block').html('Seleccionar libro');
      $('#cbo-TiposLibroSunatKardex').closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      $('.help-block').empty();
      $('.form-group').removeClass('has-error');

      var ID_Tipo_Asiento, ID_Tipo_Asiento_Detalle, ID_Almacen, dInicio, dFin, ID_Producto, Txt_Direccion_Almacen, Nu_Codigo_Libro_Sunat, No_Tipo_Asiento_Apertura, ID_Tipo_Movimiento;

      ID_Tipo_Asiento = $('#cbo-TiposLibroSunatKardex').find(':selected').data('id_tipo_asiento');
      ID_Tipo_Asiento_Detalle = $('#cbo-TiposLibroSunatKardex').val();
      ID_Almacen = $('#cbo-Almacenes_filtro_kardex').val();
      dInicio = ParseDateString($('#txt-Filtro_Fe_Inicio').val(), 1, '/');
      dFin = ParseDateString($('#txt-Filtro_Fe_Fin').val(), 1, '/');
      ID_Producto = $('#txt-ID_Producto').val();
      Txt_Direccion_Almacen = $('#cbo-Almacenes_filtro_kardex').find(':selected').data('direccion_almacen');
      Nu_Codigo_Libro_Sunat = $('#cbo-TiposLibroSunatKardex').find(':selected').data('nu_codigo_libro_sunat');
      No_Tipo_Asiento_Apertura = $('#cbo-TiposLibroSunatKardex').find(':selected').data('no_tipo_asiento_apertura');
      ID_Tipo_Movimiento = $('#cbo-filtro_tipo_movimiento').val();

      if ($(this).data('type') == 'html') {
        $('#btn-html_kardex').text('');
        $('#btn-html_kardex').attr('disabled', true);
        $('#btn-html_kardex').append('Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>');

        $('#table-Kardex >tbody').empty();
        $('#table-Kardex >tfoot').empty();

        var arrPost = {
          ID_Tipo_Asiento: ID_Tipo_Asiento,
          ID_Tipo_Asiento_Detalle: ID_Tipo_Asiento_Detalle,
          ID_Almacen: ID_Almacen,
          dInicio: dInicio,
          dFin: dFin,
          ID_Producto: ID_Producto,
          ID_Tipo_Movimiento: ID_Tipo_Movimiento,
        };

        if (ID_Tipo_Asiento == 4) {
          url = base_url + 'LibrosPLE/KardexValorizadoController/kardex';
          $.post(url, arrPost, function (response) {
            if (response.sStatus == 'success') {
              var iTotalRegistros = response.arrData.length, response = response.arrData, tr_body = '', tr_foot = '';
              var $ID_Almacen = 0, $counter_almacen = 0, $sum_Almacen_Producto_Qt_Entrada = 0.00, $sum_Almacen_Producto_Ss_SubTotal_Entrada = 0.00, $sum_Almacen_Producto_Qt_Salida = 0.00, $sum_Almacen_Producto_Ss_SubTotal_Salida = 0.00;
              var $ID_Producto = 0, $counter = 0, $sum_Producto_Qt_Entrada = 0.00, $sum_Producto_Ss_SubTotal_Entrada = 0.00, $sum_Producto_Qt_Salida = 0.00, $sum_Producto_Ss_SubTotal_Salida = 0.00, $sum_General_Qt_Entrada = 0.00, $sum_General_Ss_SubTotal_Entrada = 0.00, $sum_General_Qt_Salida = 0.00, $sum_General_Ss_SubTotal_Salida = 0.00, $Qt_Producto_Saldo_Movimiento = 0.00;
              for (var i = 0; i < iTotalRegistros; i++) {
                if ($ID_Producto != response[i].ID_Producto || $ID_Almacen != response[i].ID_Almacen) {
                  if ($counter != 0) {
                    tr_body +=
                    +"<tr style='background-color: #d0d0d08a !important;'>"
                      + "<th class='text-right' colspan='9' style='background-color: #d0d0d08a;'>TOTAL PRODUCTO</th>"
                      + "<th class='text-right' style='background-color: #d0d0d08a;'>" + Math.round10($sum_Producto_Qt_Entrada, -3) + "</th>"
                      + "<th class='text-right' style='background-color: #d0d0d08a;'></th>"
                      + "<th class='text-right' style='background-color: #d0d0d08a;'>" + Math.round10($sum_Producto_Ss_SubTotal_Entrada, -3) + "</th>"
                      + "<th class='text-right' style='background-color: #d0d0d08a;'>" + Math.round10($sum_Producto_Qt_Salida, -3) + "</th>"
                      + "<th class='text-right' style='background-color: #d0d0d08a;'></th>"
                      + "<th class='text-right' style='background-color: #d0d0d08a;'>" + Math.round10($sum_Producto_Ss_SubTotal_Salida, -3) + "</th>"
                    + "</tr>";
                  }

                  if ($ID_Almacen != response[i].ID_Almacen) {
                    if ($counter_almacen != 0) {
                      tr_body +=
                      "<tr style='background-color: #b3b3b3;'>"
                        + "<th class='text-right' colspan='9'>TOTAL ALMACÉN</th>"
                        + "<th class='text-right'>" + Math.round10($sum_Almacen_Producto_Qt_Entrada, -3) + "</th>"
                        + "<th class='text-right'></th>"
                        + "<th class='text-right'>" + Math.round10($sum_Almacen_Producto_Ss_SubTotal_Entrada, -3) + "</th>"
                        + "<th class='text-right'>" + Math.round10($sum_Almacen_Producto_Qt_Salida, -3) + "</th>"
                        + "<th class='text-right'></th>"
                        + "<th class='text-right'>" + Math.round10($sum_Almacen_Producto_Ss_SubTotal_Salida, -3) + "</th>"
                      + "</tr>";
                    }

                    $sum_Almacen_Producto_Qt_Entrada = 0.00;
                    $sum_Almacen_Producto_Ss_SubTotal_Entrada = 0.00;
                    $sum_Almacen_Producto_Qt_Salida = 0.00;
                    $sum_Almacen_Producto_Ss_SubTotal_Salida = 0.00;

                    tr_body +=
                    "<tr style='background-color: #b3b3b3;'>"
                      + "<th class='text-right'><span style='font-size: 15px;'>ALMACÉN</span></th>"
                      + "<th class='text-left' colspan='15'><span style='font-size: 15px;'>" + response[i].No_Almacen + "</span></th>"
                    +"</tr>";

                    $ID_Almacen = response[i].ID_Almacen;
                  }                    

                  $sum_Producto_Qt_Entrada = 0.00;
                  $sum_Producto_Ss_SubTotal_Entrada = 0.00;
                  $sum_Producto_Qt_Salida = 0.00;
                  $sum_Producto_Ss_SubTotal_Salida = 0.00;

                  tr_body +=
                    "<tr>"
                    + "<th class='text-right'>UPC</th>"
                    + "<th class='text-left'>" + response[i].Nu_Codigo_Barra + "</th>"
                    + "<th class='text-right'>SKU</th>"
                    + "<th class='text-left'>" + response[i].No_Codigo_Interno + "</th>"
                    + "<th class='text-right'>Nombre</th>"
                    + "<th class='text-left' colspan='3'>" + response[i].No_Producto + "</th>"
                    + "<th class='text-right' colspan='7'>SALDO INICIAL</th>"
                    + "<th class='text-right'>" + (parseFloat(response[i].Qt_Producto_Inicial) < 0 ? '-' : '') + number_format(parseFloat(response[i].Qt_Producto_Inicial), 3) + "</th>"
                    + "<th class='text-right'>" + (parseFloat(response[i].Ss_Costo_Prev_Rango_Fecha) < 0 ? '-' : '') + number_format(parseFloat(response[i].Ss_Costo_Prev_Rango_Fecha), 2) + "</th>"
                    + "<th class='text-right'>" + (parseFloat(response[i].Ss_Importe_Inicial) < 0 ? '-' : '') + number_format(parseFloat(response[i].Ss_Importe_Inicial), 2) + "</th>"
                  + "</tr>";
                  $ID_Producto = response[i].ID_Producto;
                  $Qt_Producto_Saldo_Movimiento = parseFloat(response[i].Qt_Producto_Inicial);
                }

                tr_body +=
                  "<tr>"
                  + "<td class='text-center'>" + response[i].Fe_Emision + "</td>"
                  + "<td class='text-center'>" + response[i].Tipo_Documento_Sunat_Codigo + "</td>"
                  + "<td class='text-center'>" + response[i].No_Tipo_Documento_Breve + "</td>"
                  + "<td class='text-center'>" + response[i].ID_Serie_Documento + "</td>"
                  + "<td class='text-center'>" + response[i].ID_Numero_Documento + "</td>"
                  + "<td class='text-center'>" + response[i].Tipo_Operacion_Sunat_Codigo + "</td>"
                  + "<td class='text-center'>" + response[i].No_Tipo_Movimiento + "</td>"
                  + "<td class='text-left'>" + response[i].Nu_Documento_Identidad + "</td>"
                  + "<td class='text-left'>" + response[i].No_Entidad + "</td>";

                if (response[i].Nu_Tipo_Movimiento == 0) {//Entrada
                  tr_body +=
                    "<td class='text-right'>" + (parseFloat(response[i].Qt_Producto) < 0 ? '-' : '') + number_format(parseFloat(response[i].Qt_Producto), 3) + "</td>"
                    + "<td class='text-right'>" + (parseFloat(response[i].Qt_Producto) < 0 ? '-' : '') + number_format(parseFloat(response[i].Ss_Precio), 2) + "</td>"
                    + "<td class='text-right'>" + (parseFloat(response[i].Qt_Producto) < 0 ? '-' : '') + number_format(parseFloat(response[i].Ss_SubTotal), 2) + "</td>"
                    + "<td class='text-right'>0.00</td>"
                    + "<td class='text-right'>0.00</td>"
                    + "<td class='text-right'>0.00</td>";

                  $Qt_Producto_Saldo_Movimiento += parseFloat(response[i].Qt_Producto);
                  $sum_Producto_Qt_Entrada += parseFloat(response[i].Qt_Producto);
                  $sum_Producto_Ss_SubTotal_Entrada += parseFloat(response[i].Ss_SubTotal);

                  $sum_Almacen_Producto_Qt_Entrada += parseFloat(response[i].Qt_Producto);
                  $sum_Almacen_Producto_Ss_SubTotal_Entrada += parseFloat(response[i].Ss_SubTotal);

                  $sum_General_Qt_Entrada += parseFloat(response[i].Qt_Producto);
                  $sum_General_Ss_SubTotal_Entrada += parseFloat(response[i].Ss_SubTotal);
                } else { //Salida
                  tr_body +=
                    "<td class='text-right'>0.00</td>"
                    + "<td class='text-right'>0.00</td>"
                    + "<td class='text-right'>0.00</td>"
                    + "<td class='text-right'>" + (parseFloat(response[i].Qt_Producto) < 0 ? '-' : '') + number_format(parseFloat(response[i].Qt_Producto), 3) + "</td>"
                    + "<td class='text-right'>" + (parseFloat(response[i].Qt_Producto) < 0 ? '-' : '') + number_format(parseFloat(response[i].Ss_Precio), 2) + "</td>"
                    + "<td class='text-right'>" + (parseFloat(response[i].Qt_Producto) < 0 ? '-' : '') + number_format(parseFloat(response[i].Ss_SubTotal), 2) + "</td>";

                  $Qt_Producto_Saldo_Movimiento -= parseFloat(response[i].Qt_Producto);
                  $sum_Producto_Qt_Salida += parseFloat(response[i].Qt_Producto);
                  $sum_Producto_Ss_SubTotal_Salida += parseFloat(response[i].Ss_SubTotal);

                  $sum_Almacen_Producto_Qt_Salida += parseFloat(response[i].Qt_Producto);
                  $sum_Almacen_Producto_Ss_SubTotal_Salida += parseFloat(response[i].Ss_SubTotal);

                  $sum_General_Qt_Salida += parseFloat(response[i].Qt_Producto);
                  $sum_General_Ss_SubTotal_Salida += parseFloat(response[i].Ss_SubTotal);
                }

                tr_body +=
                "<td class='text-right'>" + ($Qt_Producto_Saldo_Movimiento < 0 ? '-' : '') + number_format($Qt_Producto_Saldo_Movimiento, 3) + "</td>"
                  + "<td class='text-right'>" + ($Qt_Producto_Saldo_Movimiento < 0 ? '-' : '') + number_format(parseFloat(response[i].Ss_Costo_Promedio), 6) + "</td>"
                  + "<td class='text-right'>" + ($Qt_Producto_Saldo_Movimiento < 0 ? '-' : '') + number_format(parseFloat($Qt_Producto_Saldo_Movimiento) * parseFloat(response[i].Ss_Costo_Promedio), 2) + "</td>"
                  + "<td class='text-center'><span class='label label-" + response[i].No_Class_Estado + "'>" + response[i].No_Estado + "</td>"
                 + "</tr>";
                $counter++;
                $counter_almacen++;
              }

              tr_foot =
              "<tfoot>"
                +"<tr style='background-color: #d0d0d08a !important;'>"
                  + "<th class='text-right' colspan='9' style='background-color: #d0d0d08a;'>TOTAL PRODUCTO</th>"
                  + "<th class='text-right' style='background-color: #d0d0d08a;'>" + Math.round10($sum_Producto_Qt_Entrada, -3) + "</th>"
                  + "<th class='text-right' style='background-color: #d0d0d08a;'></th>"
                  + "<th class='text-right' style='background-color: #d0d0d08a;'>" + Math.round10($sum_Producto_Ss_SubTotal_Entrada, -3) + "</th>"
                  + "<th class='text-right' style='background-color: #d0d0d08a;'>" + Math.round10($sum_Producto_Qt_Salida, -3) + "</th>"
                  + "<th class='text-right' style='background-color: #d0d0d08a;'></th>"
                  + "<th class='text-right' style='background-color: #d0d0d08a;'>" + Math.round10($sum_Producto_Ss_SubTotal_Salida, -3) + "</th>"
                + "</tr>"
                +"<tr style='background-color: #b3b3b3;'>"
                  + "<th class='text-right' colspan='9'>TOTAL ALMACÉN</th>"
                  + "<th class='text-right'>" + Math.round10($sum_Almacen_Producto_Qt_Entrada, -3) + "</th>"
                  + "<th class='text-right'></th>"
                  + "<th class='text-right'>" + Math.round10($sum_Almacen_Producto_Ss_SubTotal_Entrada, -3) + "</th>"
                  + "<th class='text-right'>" + Math.round10($sum_Almacen_Producto_Qt_Salida, -3) + "</th>"
                  + "<th class='text-right'></th>"
                  + "<th class='text-right'>" + Math.round10($sum_Almacen_Producto_Ss_SubTotal_Salida, -3) + "</th>"
                + "</tr>"
                + "<tr>"
                  + "<th class='text-right' colspan='9'>TOTAL GENERAL</th>"
                  + "<th class='text-right'>" + Math.round10($sum_General_Qt_Entrada, -3) + "</th>"
                  + "<th class='text-right'></th>"
                  + "<th class='text-right'>" + Math.round10($sum_General_Ss_SubTotal_Entrada, -3) + "</th>"
                  + "<th class='text-right'>" + Math.round10($sum_General_Qt_Salida, -3) + "</th>"
                  + "<th class='text-right'></th>"
                  + "<th class='text-right'>" + Math.round10($sum_General_Ss_SubTotal_Salida, -3) + "</th>"
                + "</tr>"
                +"<tr>"
                  +"<th class='text-right' colspan='9'><span style='font-size: 15px;'>TOTAL CANTIDAD (ENTRADA - SALIDA)</span></th>"
                  +"<th class='text-right'><span style='font-size: 15px;'>" + Math.round10(parseFloat($sum_General_Qt_Entrada) - parseFloat($sum_General_Qt_Salida), -3) + "</span></th>"
                +"</tr>"
                +"<tr>"
                  +"<th class='text-right' colspan='9'><span style='font-size: 15px;'>TOTAL IMPORTE (ENTRADA - SALIDA)</span></th>"
                  +"<th class='text-right'><span style='font-size: 15px;'>" + Math.round10(parseFloat($sum_General_Ss_SubTotal_Entrada) - parseFloat($sum_General_Ss_SubTotal_Salida), -3) + "</span></th>"
                +"</tr>"
              + "</tfoot>";
            } else {
              tr_body +=
                "<tr>"
                + "<td colspan='13' class='text-center'>No hay registros</td>"
                + "</tr>";
            }

            $('#table-Kardex').show();
            $('#table-Kardex >tbody').append(tr_body);
            $('#table-Kardex >tbody').after(tr_foot);

            $('#btn-html_kardex').text('');
            $('#btn-html_kardex').append('<i class="fa fa-table"></i> HTML');
            $('#btn-html_kardex').attr('disabled', false);
          }, 'JSON')
            .fail(function (jqXHR, textStatus, errorThrown) {
              $('.modal-message').removeClass('modal-danger modal-warning modal-success');

              $('#modal-message').modal('show');
              $('.modal-message').addClass('modal-danger');
              $('.modal-title-message').text(textStatus + ' [' + jqXHR.status + ']: ' + errorThrown);
              setTimeout(function () { $('#modal-message').modal('hide'); }, 1700);

              //Message for developer
              console.log(jqXHR.responseText);

              $('#btn-html_kardex').text('');
              $('#btn-html_kardex').append('<i class="fa fa-search"></i> Buscar');
              $('#btn-html_kardex').attr('disabled', false);
            });
        }
      } else if ($(this).data('type') == 'pdf') {
        $('#btn-pdf_kardex').text('');
        $('#btn-pdf_kardex').attr('disabled', true);
        $('#btn-pdf_kardex').append('Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>');

        url = base_url + 'LibrosPLE/KardexValorizadoController/kardexPDF/' + ID_Tipo_Asiento + '/' + ID_Tipo_Asiento_Detalle + '/' + ID_Almacen + '/' + dInicio + '/' + dFin + '/' + ID_Producto + '/' + Txt_Direccion_Almacen + '/' + Nu_Codigo_Libro_Sunat + '/' + No_Tipo_Asiento_Apertura + '/' + ID_Tipo_Movimiento;
        window.open(url, '_blank');

        $('#btn-pdf_kardex').text('');
        $('#btn-pdf_kardex').append('<i class="fa fa-file-pdf-o color_white"></i> PDF');
        $('#btn-pdf_kardex').attr('disabled', false);
      } else if ($(this).data('type') == 'excel') {
        $('#btn-excel_kardex').text('');
        $('#btn-excel_kardex').attr('disabled', true);
        $('#btn-excel_kardex').append('Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>');

        url = base_url + 'LibrosPLE/KardexValorizadoController/kardexEXCEL/' + ID_Tipo_Asiento + '/' + ID_Tipo_Asiento_Detalle + '/' + ID_Almacen + '/' + dInicio + '/' + dFin + '/' + ID_Producto + '/' + Txt_Direccion_Almacen + '/' + Nu_Codigo_Libro_Sunat + '/' + No_Tipo_Asiento_Apertura + '/' + ID_Tipo_Movimiento;
        window.open(url, '_blank');

        $('#btn-excel_kardex').text('');
        $('#btn-excel_kardex').append('<i class="fa fa-file-excel-o color_white"></i> Excel');
        $('#btn-excel_kardex').attr('disabled', false);
      } else if ($(this).data('type') == 'txt') {
        $('#btn-txt_kardex').text('');
        $('#btn-txt_kardex').attr('disabled', true);
        $('#btn-txt_kardex').append('Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>');

        url = base_url + 'LibrosPLE/KardexValorizadoController/kardexTXT/' + ID_Tipo_Asiento + '/' + ID_Tipo_Asiento_Detalle + '/' + ID_Almacen + '/' + dInicio + '/' + dFin + '/' + ID_Producto + '/' + Txt_Direccion_Almacen + '/' + Nu_Codigo_Libro_Sunat + '/' + No_Tipo_Asiento_Apertura + '/' + ID_Tipo_Movimiento;
        window.open(url, '_blank');

        $('#btn-txt_kardex').text('');
        $('#btn-txt_kardex').append('<i class="fa fa-files-o"></i> Libro Electrónico');
        $('#btn-txt_kardex').attr('disabled', false);
      }
    }
  })
})

// Ayudas - combobox
function getAlmacenes(arrParams) {
  $('#cbo-Almacenes_filtro_kardex').html('<option value="0">- Todos -</option>');
  url = base_url + 'HelperController/getAlmacenes';
  $.post(url, {}, function (responseAlmacen) {
    var iCantidadRegistros = responseAlmacen.length;
    var selected = '';
    var iIdAlmacen = 0;
    $('#cbo-Almacenes_filtro_kardex').html('<option value="0">- Todos -</option>');
    if (iCantidadRegistros == 1) {
      if (arrParams !== undefined) {
        iIdAlmacen = arrParams.ID_Almacen;
      }
      if (iIdAlmacen == responseAlmacen[0]['ID_Almacen']) {
        selected = 'selected="selected"';
      }
      $('#cbo-Almacenes_filtro_kardex').append('<option value="' + responseAlmacen[0]['ID_Almacen'] + '" ' + selected + ' data-direccion_almacen="' + responseAlmacen[0]['Txt_Direccion_Almacen'] + '">' + responseAlmacen[0]['No_Almacen'] + '</option>');
    } else {
      for (var i = 0; i < iCantidadRegistros; i++) {
        if (arrParams !== undefined) {
          iIdAlmacen = arrParams.ID_Almacen;
        }
        if (iIdAlmacen == responseAlmacen[0]['ID_Almacen']) {
          selected = 'selected="selected"';
        }
        $('#cbo-Almacenes_filtro_kardex').append('<option value="' + responseAlmacen[i]['ID_Almacen'] + '" ' + selected + ' data-direccion_almacen="' + responseAlmacen[0]['Txt_Direccion_Almacen'] + '">' + responseAlmacen[i]['No_Almacen'] + '</option>');
      }
    }
    $('#modal-loader').modal('hide');
  }, 'JSON');
}