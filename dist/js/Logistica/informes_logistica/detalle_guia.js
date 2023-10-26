var url;

$(function () {
  $('.select2').select2();
  $( '.div-proveedores' ).hide();
  $( '.div-clientes' ).hide();
  $( '.div-productos' ).hide();

  //Global Autocomplete
  $( '.autocompletar_2' ).autoComplete({
    minChars: 0,
    source: function (term, response) {
      term = term.trim();
      if (term.length > 2) {
        var term                = term.toLowerCase();
        var global_class_method = $( '.autocompletar_2' ).data('global-class_method');
        var global_table        = $( '.autocompletar_2' ).data('global-table');
        
        var filter_id_codigo = '';
        if ($( '#txt-EID_Producto' ).val() !== undefined)
          filter_id_codigo = $( '#txt-EID_Producto' ).val();
        
        $.post( base_url + global_class_method, { global_table: global_table, global_search : term, filter_id_codigo : filter_id_codigo }, function( arrData ){
          response(arrData);
        }, 'JSON');
      }
    },
    renderItem: function (item, search){
      search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
      var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");
      var data_direccion = '';
      if ($( '#txt-Txt_Direccion_Entidad' ).val() != undefined)
        data_direccion = 'data-direccion_cliente="' + item.Txt_Direccion_Entidad + '"';
      var data_telefono = '';
      if ($( '#txt-Nu_Telefono_Entidad_Cliente' ).val() != undefined)
        data_telefono = 'data-telefono="' + item.Nu_Telefono_Entidad + '"';
      var data_celular = '';
      if ($( '#txt-Nu_Celular_Entidad_Cliente' ).val() != undefined)
        data_celular = 'data-celular="' + item.Nu_Celular_Entidad + '"';
      var data_email = '';
      if ($( '#txt-Txt_Email_Entidad_Cliente' ).val() != undefined)
        data_email = 'data-email="' + item.Txt_Email_Entidad + '"';
      var data_id_tipo_documento_identidad = '';
      if ($('#hidden-ID_Tipo_Documento_Identidad_Existente').val() != undefined)
        data_id_tipo_documento_identidad = 'data-id_tipo_documento_identidad="' + item.ID_Tipo_Documento_Identidad + '"';
      var data_dias_credito = '';
      if ( $( '#txt-Fe_Vencimiento' ).val() != undefined && ($( '#cbo-MediosPago' ).val() != undefined && $( '#cbo-MediosPago' ).find(':selected').data('nu_tipo') == 1) )
        data_dias_credito = 'data-dias_credito="' + item.Nu_Dias_Credito + '"';
      return '<div title="' + caracteresValidosAutocomplete(item.Nombre) + '" class="autocomplete-suggestion" data-id="' + item.ID + '" data-codigo="' + item.Codigo + '" data-nombre="' + caracteresValidosAutocomplete(item.Nombre) + '" data-estado="' + item.Nu_Estado + '" data-id_departamento="' + item.ID_Departamento + '" data-id_provincia="' + item.ID_Provincia + '" data-id_distrito="' + item.ID_Distrito + '" data-val="' + search + '" ' + data_direccion + ' ' + data_telefono + ' ' + data_celular + ' ' + data_email + ' ' + data_dias_credito + ' ' + data_id_tipo_documento_identidad + '>' + caracteresValidosAutocomplete(item.Nombre).replace(re, "<b>$1</b>") + '</div>';
    },
    onSelect: function(e, term, item){
      $( '#txt-AID' ).val(item.data('id'));
      if ($('#txt-AID_Doble').val() !== undefined)
        $('#txt-AID_Doble').val(item.data('id'));
      $( '#txt-ACodigo' ).val(item.data('codigo'));
      $('#txt-ANombre').val(item.data('nombre'));
      if ($('#hidden-ID_Tipo_Documento_Identidad_Existente').val() !== undefined)
        $('#hidden-ID_Tipo_Documento_Identidad_Existente').val(item.data('id_tipo_documento_identidad'));
      if ( $( '#txt-Filtro_Entidad' ).val() !== undefined )
        $( '#txt-Filtro_Entidad' ).val(item.data('nombre'));
      if ( $( '#txt-Txt_Direccion_Entidad' ).val() !== undefined )
        $('#txt-Txt_Direccion_Entidad').val(item.data('direccion_cliente'));
      if ($('#txt-Txt_Direccion_Entidad-modal').val() !== undefined)
        $('#txt-Txt_Direccion_Entidad-modal').val(item.data('direccion_cliente'));
      if ( $( '#txt-Nu_Telefono_Entidad_Cliente' ).val() !== undefined )
        $( '#txt-Nu_Telefono_Entidad_Cliente' ).val(item.data('telefono'));
      if ( $( '#txt-Nu_Celular_Entidad_Cliente' ).val() !== undefined )
        $( '#txt-Nu_Celular_Entidad_Cliente' ).val(item.data('celular'));
      if ( $( '#txt-Txt_Email_Entidad_Cliente' ).val() !== undefined )
        $('#txt-Txt_Email_Entidad_Cliente').val(item.data('email'));
      if ($('#txt-Nu_Celular_Entidad').val() !== undefined)
        $('#txt-Nu_Celular_Entidad').val(item.data('celular'));
      if ($('#txt-Txt_Email_Entidad').val() !== undefined)
        $('#txt-Txt_Email_Entidad').val(item.data('email'));
      if ( $( '#label-no_nombres' ).val() !== undefined )
        $('#label-no_nombres').text(item.data('nombre'));
      if ( $( '#hidden-nu_numero_documento_identidad' ).val() !== undefined )
        $('#hidden-nu_numero_documento_identidad').val(item.data('codigo'));
      if ( $( '#hidden-estado_entidad' ).val() !== undefined )
        $('#hidden-estado_entidad').val(item.data('estado'));
      if ( $( '#txt-Fe_Vencimiento' ).val() != undefined && ($( '#cbo-MediosPago' ).val() != undefined && $( '#cbo-MediosPago' ).find(':selected').data('nu_tipo') == 1) ) {
        var iDiasCredito = item.data('dias_credito');
        if (item.data('dias_credito') == null)
          iDiasCredito = 0;
        dNuevaFechaVencimiento = sumaFecha(iDiasCredito, $( '#txt-Fe_Emision' ).val());
        $( '#txt-Fe_Vencimiento' ).val( dNuevaFechaVencimiento );
      }
      $( '#txt-ACodigo' ).closest('.form-group').find('.help-block').html( '' );
      $('#txt-ACodigo').closest('.form-group').removeClass('has-error');
      if ($('[name="Txt_Direccion_Delivery"]').val() !== undefined)
        $('[name="Txt_Direccion_Delivery"]').val(item.data('direccion_cliente'));
      if ($('[name="AID"]').val() !== undefined)
        $('[name="AID"]').val(item.data('id'));

      //Proceso para verificar lista de precio si tiene el cliente o no
      if ($('#txt-Obtener_Lista_Precio').val() !== undefined) {
        $.post(base_url + 'HelperController/getListaPrecioxCliente', { Nu_Tipo_Lista_Precio: 1, ID_Entidad: item.data('id') }, function (responseListaxCliente) {
          if (responseListaxCliente.sStatus == 'success') {
            $('#cbo-lista_precios').val(responseListaxCliente.arrData[0].ID_Lista_Precio_Cabecera);
            var arrParams = {
              sUrl: 'HelperController/getItems',
              ID_Almacen: $('#cbo-almacen').val(),
              iIdListaPrecio: responseListaxCliente.arrData[0].ID_Lista_Precio_Cabecera,
              ID_Linea: 'favorito',
            };
            getItems(arrParams);
          } else {
            $('#cbo-lista_precios').val(0);
            var arrParams = {
              sUrl: 'HelperController/getItems',
              ID_Almacen: $('#cbo-almacen').val(),
              iIdListaPrecio: 0,
              ID_Linea: 'favorito',
            };
            getItems(arrParams);
          }
        }, 'JSON');
      }
    }
  });


  var arrParams = {};
  getAlmacenes(arrParams);

  url = base_url + 'HelperController/getTiposDocumentos';
  $.post(url, { Nu_Tipo_Filtro: 7 }, function (response) {//2 = Compra
    $('#cbo-filtros_tipos_documento').html('<option value="0" selected="selected">- Todos -</option>');
    for (var i = 0; i < response.length; i++)
      $('#cbo-filtros_tipos_documento').append('<option value="' + response[i]['ID_Tipo_Documento'] + '" data-nu_impuesto="' + response[i]['Nu_Impuesto'] + '" data-nu_enlace="' + response[i]['Nu_Enlace'] + '">' + response[i]['No_Tipo_Documento_Breve'] + '</option>');
  }, 'JSON');

  $( '#txt-AID' ).val(0);
  $( '#cbo-FiltrosProveedorGuias' ).change(function() {
    $( '.div-proveedores' ).hide();
    $( '#txt-AID' ).val(0);
    $( '#txt-Filtro_Entidad' ).val('');
    if ( $(this).val() > 0 )
      $( '.div-proveedores' ).show();
  })

  $( '#txt-AID_Doble' ).val(0);
  $( '#cbo-FiltrosClientesGuias' ).change(function() {
    $( '.div-clientes' ).hide();
    $( '#txt-AID_Doble' ).val(0);
    $( '#txt-ANombre' ).val('');
    if ( $(this).val() > 0 )
      $( '.div-clientes' ).show();
  })
  
  $( '#txt-ID_Producto' ).val(0);
  $( '#cbo-FiltrosProductoGuias' ).change(function() {
    $( '.div-productos' ).hide();
    $( '#txt-ID_Producto' ).val(0);
    $( '#txt-No_Producto' ).val('');
    if ( $(this).val() > 0 )
      $( '.div-productos' ).show();
  })
  
  $( '#div-detalle_guia' ).hide();
  
  $( '.btn-generar_detalle_guia' ).click(function(){
    $( '.help-block' ).empty();
  
    var ID_Almacen, ID_Almacen_Externo, No_Almacen, Fe_Inicio, Fe_Fin, ID_Serie_Documento, ID_Numero_Documento, Nu_Tipo_Movimiento, Nu_Estado_Documento, ID_Proveedor, ID_Cliente, ID_Producto, tr_body = "", tr_foot = "";
    
    ID_Almacen          = $( '#cbo-Almacenes_Detalle_Guia option:selected' ).val();
    No_Almacen          = $( '#cbo-Almacenes_Detalle_Guia option:selected' ).text();
    Fe_Inicio           = ParseDateString($( '#txt-Filtro_Fe_Inicio' ).val(), 1, '/');
    Fe_Fin              = ParseDateString($( '#txt-Filtro_Fe_Fin' ).val(), 1, '/');
    ID_Tipo_Documento = $('#cbo-filtros_tipos_documento option:selected').val();
    ID_Serie_Documento  = ($( '#txt-Filtro_SerieDocumento' ).val().length === 0 ? '-' : $( '#txt-Filtro_SerieDocumento' ).val());
    ID_Numero_Documento = ($( '#txt-Filtro_NumeroDocumento' ).val().length === 0 ? '-' : $( '#txt-Filtro_NumeroDocumento' ).val());
    Nu_Tipo_Movimiento  = $( '#cbo-tipo_movimiento option:selected' ).val();
    Nu_Estado_Documento = $( '#cbo-estado_documento option:selected' ).val();
    ID_Proveedor        = $( '#txt-AID' ).val();
    ID_Cliente        = $( '#txt-AID_Doble' ).val();
    ID_Producto         = $( '#txt-ID_Producto' ).val();
    ID_Almacen_Externo = $('#cbo-Almacenes_Externos_Detalle_Guia option:selected').val();

    var arrPost = {
      ID_Almacen          : ID_Almacen,
      Fe_Inicio           : Fe_Inicio,
      Fe_Fin              : Fe_Fin,
      ID_Tipo_Documento : ID_Tipo_Documento,
      ID_Serie_Documento  : ID_Serie_Documento,
      ID_Numero_Documento : ID_Numero_Documento,
      Nu_Tipo_Movimiento  : Nu_Tipo_Movimiento,
      Nu_Estado_Documento : Nu_Estado_Documento,
      ID_Proveedor        : ID_Proveedor,
      ID_Producto         : ID_Producto,
      ID_Almacen_Externo: ID_Almacen_Externo,
      ID_Cliente        : ID_Cliente,
    };
      
    if ($(this).data('type') == 'html') {
      $( '#btn-html_detalle_guia' ).text('');
      $( '#btn-html_detalle_guia' ).attr('disabled', true);
      $( '#btn-html_detalle_guia' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
  
      $( '#table-detalle_guia > tbody' ).empty();
      $( '#table-detalle_guia > tfoot' ).empty();
      
      url = base_url + 'Logistica/informes_logistica/DetalleGuiaController/sendReporte';
      $.post( url, arrPost, function( response ){
        if (response.length > 0) {
          var $ID_Almacen = 0, $counter_almacen = 0, $sum_almacen_guia_cantidad = 0.00, $sum_almacen_guia_subtotal_s = 0.00, $sum_almacen_guia_impuesto_s = 0.00, $sum_almacen_guia_total_s = 0.00, $sum_almacen_guia_total_d = 0.00;
          var ID_Tipo_Documento = '', ID_Serie_Documento = '', ID_Numero_Documento = '', counter = 0, sum_guia_cantidad = 0.000000, sum_guia_subtotal_s = 0.00, sum_guia_impuesto_s = 0.00, sum_guia_total_s = 0.00, sum_guia_total_d = 0.00;
          for (var i = 0, len = response.length; i < len; i++) {
            if (ID_Tipo_Documento != response[i].ID_Tipo_Documento+response[i].ID_Serie_Documento+response[i].ID_Numero_Documento) {
              if (counter != 0) {
                tr_body +=
                +"<tr>"
                  +"<th class='text-right' colspan='11'>Total Guía</th>"
                  +"<th class='text-right'>" + number_format(sum_guia_cantidad, 3) + "</th>"
                  +"<th class='text-right'></th>"
                  +"<th class='text-right'>" + number_format(sum_guia_subtotal_s, 2) + "</th>"
                  +"<th class='text-right'>" + number_format(sum_guia_impuesto_s, 2) + "</th>"
                  +"<th class='text-right'>" + number_format(sum_guia_total_s, 2) + "</th>"
                  +"<th class='text-right'>" + number_format(sum_guia_total_d, 2) + "</th>"
                +"</tr>";
                sum_guia_cantidad = 0.000000;
                sum_guia_subtotal_s = 0.00;
                sum_guia_impuesto_s = 0.00;
                sum_guia_total_s = 0.00;
                sum_guia_total_d = 0.00;
              }

              if ($ID_Almacen != response[i].ID_Almacen) {
                if ($counter_almacen != 0) {
                  tr_body +=
                  +"<tr>"
                    + "<th class='text-right' colspan='11'>Total Almacén</th>"
                    + "<th class='text-right'>" + number_format($sum_almacen_guia_cantidad, 3) + "</th>"
                    + "<th class='text-right'></th>"
                    + "<th class='text-right'>" + number_format($sum_almacen_guia_total_s, 2) + "</th>"
                    + "<th class='text-right'>" + number_format($sum_almacen_guia_total_d, 2) + "</th>"
                  + "</tr>";
                }

                $sum_almacen_guia_cantidad = 0.00;
                $sum_almacen_guia_subtotal_s = 0.00;
                $sum_almacen_guia_impuesto_s = 0.00;
                $sum_almacen_guia_total_s = 0.00;
                $sum_almacen_guia_total_d = 0.00;

                tr_body +=
                  "<tr>"
                  + "<th class='text-right'><span style='font-size: 15px;'>Almacén</span></th>"
                  + "<th class='text-left' colspan='14'><span style='font-size: 15px;'>" + response[i].No_Almacen + "</span></th>"
                  + "</tr>";

                $ID_Almacen = response[i].ID_Almacen;
              }// if almacen
              
              tr_body +=
              "<tr>"
                +"<th class='text-left'>" + response[i].ID_Serie_Documento + "</th>"
                +"<th class='text-left'>" + response[i].ID_Numero_Documento + "</th>"
                +"<th class='text-center'>" + response[i].Fe_Emision + "</th>"
                +"<th class='text-center'>" + response[i].Nu_Documento_Identidad + "</th>"
                +"<th class='text-left'>" + response[i].No_Entidad + "</th>"
                +"<th class='text-left'>" + (response[i].ID_Serie_Documento_Factura !== null ? response[i].ID_Serie_Documento_Factura : '') + "</th>"
                +"<th class='text-left'>" + (response[i].ID_Numero_Documento_Factura !== null ? response[i].ID_Numero_Documento_Factura : '') + "</th>"
                +"<th class='text-right' colspan='10'></th>"
                +"<th class='text-left'>" + response[i].Txt_Glosa + "</th>"
                +"<th class='text-center'>" + response[i].No_Estado + "</th>"
                +"<th class='text-center'>" + response[i].No_Tipo_Movimiento + "</th>"
                +"<th class='text-center'>" + response[i].No_Tipo_Movimiento_Detallado + "</th>"
              +"</tr>";
              ID_Tipo_Documento = response[i].ID_Tipo_Documento+response[i].ID_Serie_Documento+response[i].ID_Numero_Documento;
            }
            
            if ( response[i].Qt_Producto !== null && response[i].Ss_Precio !== null) {
              tr_body +=
              "<tr>"
                +"<td class='text-right' colspan='7'></td>"
                +"<td class='text-right'>" + response[i].No_Signo + "</td>"
                +"<td class='text-right'>" + number_format(response[i].Ss_Tipo_Cambio, 3) + "</td>"
                +"<td class='text-left'>" + response[i].Nu_Codigo_Barra + "</td>"
                +"<td class='text-left'>" + response[i].No_Producto + "</td>"
                +"<td class='text-right'>" + number_format(response[i].Qt_Producto, 3) + "</td>"
                +"<td class='text-right'>" + number_format(response[i].Ss_Precio, 2) + "</td>"
                +"<td class='text-right'>" + number_format(response[i].Ss_SubTotal, 2) + "</td>"
                +"<td class='text-right'>" + number_format(response[i].Ss_Impuesto, 2) + "</td>"
                +"<td class='text-right'>" + number_format(response[i].Ss_Total, 2) + "</td>"
                +"<td class='text-right'>" + (response[i].MONE_Nu_Sunat_Codigo == 'PEN' ? '0.00' : number_format(parseFloat(response[i].Ss_Total) * parseFloat(response[i].Ss_Tipo_Cambio), 2)) + "</td>"
              +"</tr>";
              sum_guia_cantidad += parseFloat(response[i].Qt_Producto);
              $sum_almacen_guia_cantidad += parseFloat(response[i].Qt_Producto);

              sum_guia_subtotal_s += parseFloat(response[i].Ss_SubTotal);
              $sum_almacen_guia_subtotal_s += parseFloat(response[i].Ss_SubTotal);
              sum_guia_impuesto_s += parseFloat(response[i].Ss_Impuesto);
              $sum_almacen_guia_impuesto_s += parseFloat(response[i].Ss_Impuesto);
              sum_guia_total_s += parseFloat(response[i].Ss_Total);
              $sum_almacen_guia_total_s += parseFloat(response[i].Ss_Total);

              sum_guia_total_d += (response[i].MONE_Nu_Sunat_Codigo != 'PEN' ? parseFloat(response[i].Ss_Total) : 0);
              $sum_almacen_guia_total_d += (response[i].MONE_Nu_Sunat_Codigo != 'PEN' ? parseFloat(response[i].Ss_Total) : 0);
            }
            
            counter++;
            $counter_almacen++;
          }
          
          tr_foot =
          "<tfoot>"
            +"<tr>"
              +"<th class='text-right' colspan='11'>Total Guía</th>"
              +"<th class='text-right'>" + number_format(sum_guia_cantidad, 3) + "</th>"
              +"<th class='text-right'></th>"
              +"<th class='text-right'>" + number_format(sum_guia_subtotal_s, 2) + "</th>"
              +"<th class='text-right'>" + number_format(sum_guia_impuesto_s, 2) + "</th>"
              +"<th class='text-right'>" + number_format(sum_guia_total_s, 2) + "</th>"
              +"<th class='text-right'>" + number_format(sum_guia_total_d, 2) + "</th>"
            + "</tr>"
            + "<tr>"
              + "<th class='text-right' colspan='11'>Total Almacén</th>"
              + "<th class='text-right'>" + number_format($sum_almacen_guia_cantidad, 3) + "</th>"
              + "<th class='text-right'></th>"
              + "<th class='text-right'>" + number_format($sum_almacen_guia_subtotal_s, 2) + "</th>"
              + "<th class='text-right'>" + number_format($sum_almacen_guia_impuesto_s, 2) + "</th>"
              + "<th class='text-right'>" + number_format($sum_almacen_guia_total_s, 2) + "</th>"
              + "<th class='text-right'>" + number_format($sum_almacen_guia_total_d, 2) + "</th>"
            + "</tr>"
          +"</tfoot>";
        } else {
          tr_body +=
          "<tr>"
            + "<td colspan='24' class='text-center'>No hay registros</td>"
          + "</tr>";
        }
        
        $( '#div-detalle_guia' ).show();
        $( '#table-detalle_guia > tbody' ).append(tr_body);
        $( '#table-detalle_guia > tbody' ).after(tr_foot);
        
        $( '#btn-html_detalle_guia' ).text('');
        $( '#btn-html_detalle_guia' ).append( '<i class="fa fa-search"></i> Buscar' );
        $( '#btn-html_detalle_guia' ).attr('disabled', false);
      }, 'JSON')
      .fail(function (jqXHR, textStatus, errorThrown) {
        $('.modal-message').removeClass('modal-danger modal-warning modal-success');

        $('#modal-message').modal('show');
        $('.modal-message').addClass('modal-danger');
        $('.modal-title-message').text(textStatus + ' [' + jqXHR.status + ']: ' + errorThrown);
        setTimeout(function () { $('#modal-message').modal('hide'); }, 1700);

        //Message for developer
        console.log(jqXHR.responseText);

        $('#btn-html_detalle_guia').text('');
        $('#btn-html_detalle_guia').append('<i class="fa fa-search"></i> Buscar');
        $('#btn-html_detalle_guia').attr('disabled', false);
      });
    } else if ($(this).data('type') == 'pdf') {
      $( '#btn-pdf_detalle_guia' ).text('');
      $( '#btn-pdf_detalle_guia' ).attr('disabled', true);
      $( '#btn-pdf_detalle_guia' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
          
      url = base_url + 'Logistica/informes_logistica/DetalleGuiaController/sendReportePDF/' + ID_Almacen + '/' + Fe_Inicio + '/' + Fe_Fin + '/' + ID_Serie_Documento + '/' + ID_Numero_Documento + '/' + Nu_Tipo_Movimiento + '/' + Nu_Estado_Documento + '/' + ID_Proveedor + '/' + ID_Producto + '/' + No_Almacen + '/' + ID_Tipo_Documento + '/' + ID_Almacen_Externo + '/' + ID_Cliente;
      window.open(url,'_blank');
      
      $( '#btn-pdf_detalle_guia' ).text('');
      $( '#btn-pdf_detalle_guia' ).append( '<i class="fa fa-file-pdf-o color_white"></i> PDF' );
      $( '#btn-pdf_detalle_guia' ).attr('disabled', false);
    } else if ($(this).data('type') == 'excel') {
      $( '#btn-excel_detalle_guia' ).text('');
      $( '#btn-excel_detalle_guia' ).attr('disabled', true);
      $( '#btn-excel_detalle_guia' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
      
      url = base_url + 'Logistica/informes_logistica/DetalleGuiaController/sendReporteEXCEL/' + ID_Almacen + '/' + Fe_Inicio + '/' + Fe_Fin + '/' + ID_Serie_Documento + '/' + ID_Numero_Documento + '/' + Nu_Tipo_Movimiento + '/' + Nu_Estado_Documento + '/' + ID_Proveedor + '/' + ID_Producto + '/' + No_Almacen + '/' + ID_Tipo_Documento + '/' + ID_Almacen_Externo + '/' + ID_Cliente;
      window.open(url,'_blank');
      
      $( '#btn-excel_detalle_guia' ).text('');
      $( '#btn-excel_detalle_guia' ).append( '<i class="fa fa-file-excel-o color_white"></i> Excel' );
      $( '#btn-excel_detalle_guia' ).attr('disabled', false);
    }
  })//./ btn
})

// Ayudas - combobox
function getAlmacenes(arrParams) {
  $('#cbo-Almacenes_Detalle_Guia').html('<option value="0">- Todos -</option>');
  $('#cbo-Almacenes_Externos_Detalle_Guia').html('<option value="0">- Todos -</option>');
  url = base_url + 'HelperController/getAlmacenes';
  $.post(url, {}, function (responseAlmacen) {
    var iCantidadRegistros = responseAlmacen.length;
    var selected = '';
    var iIdAlmacen = 0;
    $('#cbo-Almacenes_Detalle_Guia').html('<option value="0">- Todos -</option>');
    $('#cbo-Almacenes_Externos_Detalle_Guia').html('<option value="0">- Todos -</option>');
    if (iCantidadRegistros == 1) {
      if (arrParams !== undefined) {
        iIdAlmacen = arrParams.ID_Almacen;
      }
      if (iIdAlmacen == responseAlmacen[0]['ID_Almacen']) {
        selected = 'selected="selected"';
      }
      $('#cbo-Almacenes_Detalle_Guia').append('<option value="' + responseAlmacen[0]['ID_Almacen'] + '" ' + selected + ' data-direccion_almacen="' + responseAlmacen[0]['Txt_Direccion_Almacen'] + '">' + responseAlmacen[0]['No_Almacen'] + '</option>');
      $('#cbo-Almacenes_Externos_Detalle_Guia').append('<option value="' + responseAlmacen[0]['ID_Almacen'] + '" ' + selected + ' data-direccion_almacen="' + responseAlmacen[0]['Txt_Direccion_Almacen'] + '">' + responseAlmacen[0]['No_Almacen'] + '</option>');
    } else {
      for (var i = 0; i < iCantidadRegistros; i++) {
        if (arrParams !== undefined) {
          iIdAlmacen = arrParams.ID_Almacen;
        }
        if (iIdAlmacen == responseAlmacen[0]['ID_Almacen']) {
          selected = 'selected="selected"';
        }
        $('#cbo-Almacenes_Detalle_Guia').append('<option value="' + responseAlmacen[i]['ID_Almacen'] + '" ' + selected + ' data-direccion_almacen="' + responseAlmacen[0]['Txt_Direccion_Almacen'] + '">' + responseAlmacen[i]['No_Almacen'] + '</option>');
        $('#cbo-Almacenes_Externos_Detalle_Guia').append('<option value="' + responseAlmacen[i]['ID_Almacen'] + '" ' + selected + ' data-direccion_almacen="' + responseAlmacen[0]['Txt_Direccion_Almacen'] + '">' + responseAlmacen[i]['No_Almacen'] + '</option>');
      }
    }
    $('#modal-loader').modal('hide');
  }, 'JSON');
}