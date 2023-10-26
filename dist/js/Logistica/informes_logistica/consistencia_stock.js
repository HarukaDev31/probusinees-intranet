var url;

$(function () {
  $('.select2').select2();
  
  $( '.div-fecha_stock_valorizado' ).hide();
  $( '.div-productos' ).hide();
  
  var arrParams = {};
  getAlmacenes(arrParams);
  
  url = base_url + 'HelperController/getDataGeneral';
  $.post(url, { sTipoData: 'categoria' }, function (response) {
    if (response.sStatus == 'success') {
      var iTotalRegistros = response.arrData.length, response = response.arrData;
      $('#cbo-Categorias_Stock_Valorizado').html('<option value="0" selected="selected">- Todos -</option>');
      for (var i = 0; i < iTotalRegistros; i++)
        $('#cbo-Categorias_Stock_Valorizado').append('<option value="' + response[i].ID + '">' + response[i].Nombre + '</option>');
    } else {
      $('#cbo-Categorias_Stock_Valorizado').html('<option value="0" selected="selected">- Vacío -</option>');
      console.log(response);
    }
    $('#modal-loader').modal('hide');
  }, 'JSON');

  $('#cbo-sub_categoria').html('<option value="0" selected="selected">- Todos -</option>');
  $('#cbo-Categorias_Stock_Valorizado').change(function () {
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

  $( '#txt-ID_Producto' ).val(0);
  $( '#cbo-FiltrosProducto' ).change(function() {
    $( '.div-productos' ).hide();
    $( '#txt-ID_Producto' ).val(0);
    $( '#txt-No_Producto' ).val('');
    if ( $(this).val() > 0 )
      $( '.div-productos' ).show();
  })
  
  $( '#div-stock_valorizado' ).hide();
  
  $( '.btn-generar_stock_valorizado' ).click(function(){
    $('.form-group').removeClass('has-error');
    $('.help-block').empty();
    
    var ID_Almacen, No_Almacen, iTipoFecha, Fe_Inicio = "", Fe_Fin = "", iTipoStock, ID_Familia, iIdSubFamilia, ID_Producto, Nu_Estado_Item, tr_body = "", tr_foot = "";

    ID_Almacen = $( '#cbo-Almacenes_Stock_Valorizado option:selected' ).val();
    No_Almacen = $( '#cbo-Almacenes_Stock_Valorizado option:selected' ).text();
    iTipoFecha = $('[name="radio-fecha"]:checked').attr('value');
    iIdSubFamilia = $('#cbo-sub_categoria').val();
    Nu_Estado_Item = $('#cbo-filtro_estado_producto option:selected').val();

    if ($('[name="radio-fecha"]:checked').attr('value') == 1) {
      Fe_Inicio = ParseDateString($( '#txt-Filtro_Fe_Inicio' ).val(), 1, '/');
      Fe_Fin    = ParseDateString($( '#txt-Filtro_Fe_Fin' ).val(), 1, '/');
    } else {
      Fe_Inicio = 0;
      Fe_Fin = 0;
    }
  
    iTipoStock  = $('[name="radio-stock"]:checked').attr('value');
    ID_Familia    = $( '#cbo-Categorias_Stock_Valorizado option:selected' ).val();
    ID_Producto = $('#txt-ID_Producto').val();
    iAgruparxCategoria = $('[name="radio-agrupar_x_categoria"]:checked').attr('value');

    var arrPost = {
      ID_Almacen  : ID_Almacen,
      iTipoFecha  : iTipoFecha,
      Fe_Inicio   : Fe_Inicio,
      Fe_Fin      : Fe_Fin,
      iTipoStock  : iTipoStock,
      ID_Familia    : ID_Familia,
      ID_Producto: ID_Producto,
      iIdSubFamilia: iIdSubFamilia,
      iAgruparxCategoria: iAgruparxCategoria,
      Nu_Estado_Item: Nu_Estado_Item,
    };
      
    if ($(this).data('type') == 'html') {
      $( '#btn-html_stock_valorizado' ).text('');
      $( '#btn-html_stock_valorizado' ).attr('disabled', true);
      $( '#btn-html_stock_valorizado' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
  
      $( '#table-stock_valorizado > tbody' ).empty();
      $( '#table-stock_valorizado > tfoot' ).empty();
      
      url = base_url + 'Logistica/informes_logistica/ConsistenciaStockController/sendReporte';
      $.post( url, arrPost, function( response ){
        if (response.length > 0) {
          var $ID_Almacen = 0, $counter_almacen = 0, $sum_almacen_linea_cantidad = 0.00, $sum_almacen_linea_cantidad_kardex = 0.00;
          var ID_Familia = '', counter = 0, sum_linea_cantidad = 0.000000, sum_cantidad = 0.000000, sum_linea_cantidad_kardex = 0.000000, sum_cantidad_kardex = 0.000000, $fCantidad = 0, $fCantidadKardex = 0;
          for (var i = 0, len = response.length; i < len; i++) {
            if ((ID_Familia != response[i].ID_Familia || $ID_Almacen != response[i].ID_Almacen) && iAgruparxCategoria == 1) {
              if (counter != 0) {
                tr_body +=
                  +"<tr>"
                  + "<th class='text-right' colspan='2'>Total Categoría</th>"
                  + "<th class='text-right'>" + (sum_linea_cantidad < 0 ? '-' : '') + number_format(sum_linea_cantidad, 3) + "</th>"
                  + "<th class='text-right'>" + (sum_linea_cantidad_kardex < 0 ? '-' : '') + number_format(sum_linea_cantidad_kardex, 3) + "</th>"
                + "</tr>";
              }
              sum_linea_cantidad = 0.000000;
              sum_linea_cantidad_kardex = 0.000000;

              if ($ID_Almacen != response[i].ID_Almacen) {
                if ($counter_almacen != 0) {
                  tr_body +=
                  +"<tr>"
                    +"<th class='text-right' colspan='2'>Total Almacén</th>"
                    + "<th class='text-right'>" + ($sum_almacen_linea_cantidad < 0 ? '-' : '') + number_format($sum_almacen_linea_cantidad, 3) + "</th>"
                    + "<th class='text-right'>" + ($sum_almacen_linea_cantidad_kardex < 0 ? '-' : '') + number_format($sum_almacen_linea_cantidad_kardex, 3) + "</th>"
                  + "</tr>";
                }

                $sum_almacen_linea_cantidad = 0.00;
                $sum_almacen_linea_cantidad_kardex = 0.00;

                tr_body +=
                  "<tr>"
                  + "<th class='text-right'><span style='font-size: 15px;'>Almacén</span></th>"
                  + "<th class='text-left' colspan='6'><span style='font-size: 15px;'>" + response[i].No_Almacen + "</span></th>"
                + "</tr>";

                $ID_Almacen = response[i].ID_Almacen;
              }// if almacen

              tr_body +=
              "<tr>"
                +"<th class='text-right'>Categoría</th>"
                +"<th class='text-left' colspan='6'>" + response[i].No_Familia + "</th>"
              +"</tr>";
              
              ID_Familia = response[i].ID_Familia;
            }
            
            $fCantidad = parseFloat(response[i].Qt_Producto);
            $fCantidadKardex = parseFloat(response[i].Qt_Producto_Kardex);

            tr_body +=
            "<tr class='text-left " + (parseFloat(response[i].Qt_Producto) != parseFloat(response[i].Qt_Producto_Kardex) ? 'danger' : '') + "'>"
              +"<td class='text-left'>" + response[i].Nu_Codigo_Barra + "</td>"
              +"<td class='text-left'>" + response[i].No_Producto + "</td>"
              +"<td class='text-right'>" + ($fCantidad < 0 ? '-' : '') + number_format($fCantidad, 3) + "</td>"
              +"<td class='text-right'>" + ($fCantidadKardex < 0 ? '-' : '') + number_format($fCantidadKardex, 3) + "</td>"
              +"<td class='text-left'>" + response[i].No_Familia + "</td>"
              +"<td class='text-left'>" + response[i].No_Sub_Familia + "</td>"
              +"<td class='text-left'>" + response[i].No_Marca + "</td>"
            +"</tr>";
            
            sum_linea_cantidad += parseFloat(response[i].Qt_Producto);
            $sum_almacen_linea_cantidad += parseFloat(response[i].Qt_Producto);
            sum_cantidad += parseFloat(response[i].Qt_Producto);
            
            sum_linea_cantidad_kardex += parseFloat(response[i].Qt_Producto_Kardex);
            $sum_almacen_linea_cantidad_kardex += parseFloat(response[i].Qt_Producto_Kardex);
            sum_cantidad_kardex += parseFloat(response[i].Qt_Producto_Kardex);

            counter++;
            $counter_almacen++;
          }
          
          tr_foot =
          "<tfoot>";
            if (iAgruparxCategoria == 1){
              tr_foot += "<tr>"
                +"<th class='text-right' colspan='2'>Total Categoría</th>"
                + "<th class='text-right'>" + (sum_linea_cantidad < 0 ? '-' : '') + number_format(sum_linea_cantidad, 3) + "</th>"
                + "<th class='text-right'>" + (sum_linea_cantidad_kardex < 0 ? '-' : '') + number_format(sum_linea_cantidad_kardex, 3) + "</th>"
              + "</tr>"
              + "<tr>"
                + "<th class='text-right' colspan='2'>Total Almacén</th>"
                + "<th class='text-right'>" + ($sum_almacen_linea_cantidad < 0 ? '-' : '') + number_format($sum_almacen_linea_cantidad, 3) + "</th>"
                + "<th class='text-right'>" + ($sum_almacen_linea_cantidad_kardex < 0 ? '-' : '') + number_format($sum_almacen_linea_cantidad_kardex, 3) + "</th>"
              + "</tr>";
            }
            tr_foot +="<tr>"
              +"<th class='text-right' colspan='2'>Total General</th>"
              + "<th class='text-right'>" + (sum_cantidad < 0 ? '-' : '') + number_format(sum_cantidad, 3) + "</th>"
              + "<th class='text-right'>" + (sum_cantidad_kardex < 0 ? '-' : '') + number_format(sum_cantidad_kardex, 3) + "</th>"
            +"</tr>"
          +"</tfoot>";
        } else {
          tr_body +=
          "<tr>"
            + "<td colspan='10' class='text-center'>No hay registros</td>"
          + "</tr>";
        }
        
        $( '#div-stock_valorizado' ).show();
        $( '#table-stock_valorizado > tbody' ).append(tr_body);
        $( '#table-stock_valorizado > tbody' ).after(tr_foot);
        
        $( '#btn-html_stock_valorizado' ).text('');
        $( '#btn-html_stock_valorizado' ).append( '<i class="fa fa-search color_white"></i> Buscar' );
        $( '#btn-html_stock_valorizado' ).attr('disabled', false);
      }, 'JSON')
      .fail(function (jqXHR, textStatus, errorThrown) {
        $('.modal-message').removeClass('modal-danger modal-warning modal-success');

        $('#modal-message').modal('show');
        $('.modal-message').addClass('modal-danger');
        $('.modal-title-message').text(textStatus + ' [' + jqXHR.status + ']: ' + errorThrown);
        setTimeout(function () { $('#modal-message').modal('hide'); }, 1700);

        //Message for developer
        console.log(jqXHR.responseText);

        $('#btn-html_stock_valorizado').text('');
        $('#btn-html_stock_valorizado').append('<i class="fa fa-search color_white"></i> Buscar');
        $('#btn-html_stock_valorizado').attr('disabled', false);
      });
    } else if ($(this).data('type') == 'pdf') {
      $( '#btn-pdf_stock_valorizado' ).text('');
      $( '#btn-pdf_stock_valorizado' ).attr('disabled', true);
      $( '#btn-pdf_stock_valorizado' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
          
      url = base_url + 'Logistica/informes_logistica/ConsistenciaStockController/sendReportePDF/' + ID_Almacen + '/' + iTipoFecha + '/' + Fe_Inicio + '/' + Fe_Fin + '/' + iTipoStock + '/' + ID_Familia + '/' + ID_Producto + '/' + No_Almacen + '/' + iIdSubFamilia + '/' + iAgruparxCategoria + '/' + Nu_Estado_Item;
      window.open(url,'_blank');
      
      $( '#btn-pdf_stock_valorizado' ).text('');
      $( '#btn-pdf_stock_valorizado' ).append( '<i class="fa fa-file-pdf-o color_white"></i> PDF' );
      $( '#btn-pdf_stock_valorizado' ).attr('disabled', false);
    } else if ($(this).data('type') == 'excel') {
      $( '#btn-excel_stock_valorizado' ).text('');
      $( '#btn-excel_stock_valorizado' ).attr('disabled', true);
      $( '#btn-excel_stock_valorizado' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
      
      url = base_url + 'Logistica/informes_logistica/ConsistenciaStockController/sendReporteEXCEL/' + ID_Almacen + '/' + iTipoFecha + '/' + Fe_Inicio + '/' + Fe_Fin + '/' + iTipoStock + '/' + ID_Familia + '/' + ID_Producto + '/' + No_Almacen + '/' + iIdSubFamilia + '/' + iAgruparxCategoria + '/' + Nu_Estado_Item;
      window.open(url,'_blank');
      
      $( '#btn-excel_stock_valorizado' ).text('');
      $( '#btn-excel_stock_valorizado' ).append( '<i class="fa fa-file-excel-o color_white"></i> Excel' );
      $( '#btn-excel_stock_valorizado' ).attr('disabled', false);
    }
  })//./ btn

  $('#radio-fe_actual').on('ifChecked', function () {
    $('.div-fecha_stock_valorizado').hide();
  })

  $('#radio-fe_seleccionada').on('ifChecked', function () {
    $('.div-fecha_stock_valorizado').show();
  })
})

function verFecha(tipo){
  $( '.div-fecha_stock_valorizado' ).hide();
  if (tipo === '1')
    $( '.div-fecha_stock_valorizado' ).show();
}

// Ayudas - combobox
function getAlmacenes(arrParams){
  $('#cbo-Almacenes_Stock_Valorizado').html('<option value="0">- Todos -</option>');
  url = base_url + 'HelperController/getAlmacenes';
  $.post( url, {}, function( responseAlmacen ){
    var iCantidadRegistros = responseAlmacen.length;
    var selected = '';
    var iIdAlmacen = 0;
    $('#cbo-Almacenes_Stock_Valorizado').html('<option value="0">- Todos -</option>');
    if (iCantidadRegistros == 1) {
      if (arrParams !== undefined) {
        iIdAlmacen = arrParams.ID_Almacen;
      }
      if (iIdAlmacen == responseAlmacen[0]['ID_Almacen']){
        selected = 'selected="selected"';
      }
      $('#cbo-Almacenes_Stock_Valorizado').append( '<option value="' + responseAlmacen[0]['ID_Almacen'] + '" ' + selected + ' data-direccion_almacen="' + responseAlmacen[0]['Txt_Direccion_Almacen'] + '">' + responseAlmacen[0]['No_Almacen'] + '</option>' );
    } else {
      for (var i = 0; i < iCantidadRegistros; i++) {
        if (arrParams !== undefined) {
          iIdAlmacen = arrParams.ID_Almacen;
        }
        if (iIdAlmacen == responseAlmacen[0]['ID_Almacen']){
          selected = 'selected="selected"';
        }
        $( '#cbo-Almacenes_Stock_Valorizado' ).append( '<option value="' + responseAlmacen[i]['ID_Almacen'] + '" ' + selected + ' data-direccion_almacen="' + responseAlmacen[0]['Txt_Direccion_Almacen'] + '">' + responseAlmacen[i]['No_Almacen'] + '</option>' );
      }
    }
    $( '#modal-loader' ).modal('hide');
  }, 'JSON');
}