var url;

$(function () {
  //Global autocomplete Producto
  $('.autocompletar_kardex').autoComplete({
    minChars: 0,
		tabDisabled:false,
    source: function (term, response) {
      term = term.trim();
      if (term.length > 2) {
        var global_class_method = $('.autocompletar_kardex').data('global-class_method');
        var global_table = $('.autocompletar_kardex').data('global-table');

        var filter_id_almacen = '';
        if ($('#cbo-DescargarInventario').val() == '1' && $('#txt-Nu_Tipo_Registro').val() == '1')
          filter_id_almacen = $('#cbo-Almacenes').val();

        var filter_id_tipo_movimiento = '';
        if ($('#txt-Nu_Tipo_Registro').val() !== undefined)
          filter_id_tipo_movimiento = $('#txt-Nu_Tipo_Registro').val();

        var filter_nu_compuesto = '';
        if ($('#txt-Nu_Compuesto').val() !== undefined)
          filter_nu_compuesto = $('#txt-Nu_Compuesto').val();

        var filter_nu_tipo_producto = '';
        if ($('#txt-Nu_Tipo_Producto').val() !== undefined)
          filter_nu_tipo_producto = $('#txt-Nu_Tipo_Producto').val();

        var filter_lista = 0;
        if ($('#cbo-lista_precios').val() !== undefined)
          filter_lista = $('#cbo-lista_precios').val();

        var send_post = {
          global_table: global_table,
          global_search: term,
          filter_id_almacen: filter_id_almacen,
          filter_nu_compuesto: filter_nu_compuesto,
          filter_nu_tipo_producto: filter_nu_tipo_producto,
          filter_lista: filter_lista,
          filter_id_tipo_movimiento: filter_id_tipo_movimiento,
        }

        $.post(base_url + global_class_method, send_post, function (arrData) {
          response(arrData);
        }, 'JSON');
      }
    },
    renderItem: function (item, search) {
      search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
      var Ss_Precio = 0;
      if ((item.Ss_Precio === null || item.Ss_Precio == 0.000000) && (item.Ss_Precio_Item !== null || item.Ss_Precio_Item != 0.000000) && $('#txt-Nu_Tipo_Registro').val() == '1')
        Ss_Precio = item.Ss_Precio_Item;
      if ((item.Ss_Precio === null || item.Ss_Precio == 0.000000) && (item.Ss_Costo_Item !== null || item.Ss_Costo_Item != 0.000000) && $('#txt-Nu_Tipo_Registro').val() == '0')
        Ss_Precio = item.Ss_Costo_Item;
      if (item.Ss_Precio !== null)
        Ss_Precio = item.Ss_Precio;
      var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");
			return '<div style="cursor:pointer;" title="' + caracteresValidosAutocomplete(item.Nombre) + ' / Precio: ' + parseFloat(Ss_Precio).toFixed(2) + '" class="autocomplete-suggestion" data-id="' + item.ID + '" data-nu_activar_precio_x_mayor="' + item.Nu_Activar_Precio_x_Mayor + '" data-no_variante_1="' + item.No_Variante_1 + '" data-no_valor_variante_1="' + item.No_Valor_Variante_1 + '" data-no_variante_2="' + item.No_Variante_2 + '" data-no_valor_variante_2="' + item.No_Valor_Variante_2 + '" data-no_variante_3="' + item.No_Variante_3 + '" data-no_valor_variante_3="' + item.No_Valor_Variante_3 + '" data-no_unidad_medida="' + item.No_Unidad_Medida + '" data-ss_icbper="' + item.Ss_Icbper + '" data-id_impuesto_icbper="' + item.ID_Impuesto_Icbper + '" data-no_codigo_interno="' + item.No_Codigo_Interno + '" data-nu_compuesto="' + item.Nu_Compuesto + '" data-nu_tipo_item="' + item.Nu_Tipo_Producto + '" data-codigo="' + item.Codigo + '" data-nombre="' + caracteresValidosAutocomplete(item.Nombre.replace('"', "''")) + '" data-precio="' + Ss_Precio + '" data-precio_interno="' + item.Ss_Precio_Interno + '" data-nu_tipo_impuesto="' + item.Nu_Tipo_Impuesto + '" data-id_impuesto_cruce_documento="' + item.ID_Impuesto_Cruce_Documento + '" data-ss_impuesto="' + item.Ss_Impuesto + '" data-qt_producto="' + item.Qt_Producto + '" data-txt_composicion="' + item.Txt_Composicion + '" data-val="' + search + '">' + (iIdTipoRubroEmpresaGlobal == 2 ? '[' + (item.No_Marca !== null ? item.No_Marca : 'Sin marca') + '] ' : '') + (item.No_Codigo_Interno !== null && item.No_Codigo_Interno != '' ? '[' + item.No_Codigo_Interno + '] ' : '') + caracteresValidosAutocomplete(item.Nombre).replace(re, "<b>$1</b>") + ($('#hidden-iTipoRubroEmpresa').val() == 6 ? (item.No_Valor_Variante_1 !== null && item.No_Variante_1 !== null ? ' / <strong>' + item.No_Variante_1 + '</strong>: ' + item.No_Valor_Variante_1 : '') + (item.No_Valor_Variante_2 !== null && item.No_Variante_2 !== null ? ' / <strong>' + item.No_Variante_2 + '</strong>: ' + item.No_Valor_Variante_2 : '') + (item.No_Valor_Variante_3 !== null && item.No_Variante_3 !== null ? ' / <strong>' + item.No_Variante_3 + '</strong>: ' + item.No_Valor_Variante_3 : '') + ' ' : '') + ' / <strong>P:</strong> ' + parseFloat(Ss_Precio).toFixed(2) + ' / <strong>S:</strong> ' + (isNaN(parseFloat(item.Qt_Producto)) ? 0 : parseFloat(item.Qt_Producto)) + '</div>';
		},
    onSelect: function (e, term, item) {
      $('#txt-ID_Producto').val(item.data('id'));
      $('#txt-Nu_Codigo_Barra').val(item.data('codigo'));
      $('#txt-No_Producto').val(item.data('nombre'));
    }
  });

  $('.select2').select2();
  $( '#modal-loader' ).modal('show');

  $('#checkbox-busqueda_producto').prop('checked', false).iCheck('update');

  $('.div-mas_opciones').hide();
  if ($('#hidden-iTipoRubroEmpresa').val()==6) {
    $('.div-mas_opciones-variantes').hide();
  }

  $('#checkbox-mas_filtros').on('ifChanged', function(){
    $('.div-mas_opciones').hide();
    if ($('#hidden-iTipoRubroEmpresa').val()==6) {
      $('.div-mas_opciones-variantes').hide();
    }
    var _this = jQuery(this);
    if(_this.is(':checked')){
      
      $('.div-mas_opciones').show();
      if ($('#hidden-iTipoRubroEmpresa').val()==6) {
        $('.div-mas_opciones-variantes').show();
      }
    }
  });

	url = base_url + 'HelperController/getDataGeneral';
	$.post( url, {sTipoData : 'categoria'}, function( response ){
    $( '#cbo-filtro_categoria' ).html( '<option value="0" selected="selected">- Vacío -</option>');
    if ( response.sStatus == 'success' ) {
      var iTotalRegistros = response.arrData.length, response=response.arrData;
      $( '#cbo-filtro_categoria' ).html( '<option value="0" selected="selected">- Todos -</option>');
      for (var i = 0; i < iTotalRegistros; i++)
        $( '#cbo-filtro_categoria' ).append( '<option value="' + response[i].ID + '">' + response[i].Nombre + '</option>' );
    } else {
      console.log( response );
    }
  }, 'JSON');
  
  $( '#cbo-filtro_sub_categoria' ).html('<option value="0" selected="selected">- Todos -</option>');
	$( '#cbo-filtro_categoria' ).change(function(){
    if($(this).val()>0) {
      url = base_url + 'HelperController/getDataGeneral';
      var arrParams = {
        sTipoData : 'subcategoria',
        sWhereIdCategoria : $( this ).val(),
      }
      $.post( url, arrParams, function( response ){
        $( '#cbo-filtro_sub_categoria' ).html('<option value="0" selected="selected">- No hay registros -</option>');
        if (response.sStatus == 'success') {
          $('#cbo-filtro_sub_categoria').html('<option value="0" selected="selected">- Todos -</option>');
          var l = response.arrData.length;
          if (l==1) {
            $('#cbo-filtro_sub_categoria').append( '<option value="' + response.arrData[0].ID + '">' + response.arrData[0].Nombre + '</option>' );
          } else {
            for (var x = 0; x < l; x++) {
              $( '#cbo-filtro_sub_categoria' ).append( '<option value="' + response.arrData[x].ID + '">' + response.arrData[x].Nombre + '</option>' );
            }
          }
        } else {
          if( response.sMessageSQL !== undefined ) {
            console.log(response.sMessageSQL);
          }
        }
      }, 'JSON');
    }
  });

  $('#cbo-filtro_marca').html('<option value="0">- Todos -</option>');
  url = base_url + 'HelperController/getMarcas';
  $.post(url, function (responseMarcas) {
    $('#cbo-filtro_marca').html('<option value="0">- Todos -</option>');
    if (responseMarcas.length == 1) {
      $('#cbo-filtro_marca').append('<option value="' + responseMarcas[0].ID_Marca + '">' + responseMarcas[0].No_Marca + '</option>');
    } else {
      for (var i = 0; i < responseMarcas.length; i++)
        $('#cbo-filtro_marca').append('<option value="' + responseMarcas[i].ID_Marca + '">' + responseMarcas[i].No_Marca + '</option>');
    }
  }, 'JSON');

  //Variante 1
  $('#cbo-filtro_variante_1').html('<option value="0" selected="selected">- Sin registros -</option>');
  $('#cbo-filtro_valor_1').html('<option value="0" selected="selected">- Sin registros -</option>');
  url = base_url + 'HelperController/getVariantexIDTablaDato';
  $.post(url, { ID_Tabla_Dato: 2084 }, function (response) {
    if (response.sStatus == 'success') {
      var l = response.arrData.length;
      if (l == 1) {
        $('#cbo-filtro_variante_1').html('<option value="0" selected="selected">- Todos -</option>');
        $('#cbo-filtro_variante_1').append('<option value="' + response.arrData[0].ID + '">' + response.arrData[0].Nombre + '</option>');

        $('#cbo-filtro_valor_1').html('<option value="0" selected="selected">- Sin registros -</option>');
        url = base_url + 'HelperController/getVarianteDetalle';
        var arrParams = {
          ID_Variante_Item: response.arrData[0].ID,
        }
        $.post(url, arrParams, function (responseDetalle) {
          $('#cbo-filtro_valor_1').html('<option value="0" selected="selected">- Todos -</option>');
          if (responseDetalle.sStatus == 'success') {
            var l = responseDetalle.arrData.length;
            for (var x = 0; x < l; x++)
              $('#cbo-filtro_valor_1').append('<option value="' + responseDetalle.arrData[x].ID + '">' + responseDetalle.arrData[x].Nombre + '</option>');
          } else {
            $('#cbo-filtro_valor_1').html('<option value="0" selected="selected">- vacío -</option>');
          }
        }, 'JSON');
      }
    } else {
      if (response.sMessageSQL !== undefined) {
        console.log(response.sMessageSQL);
      }
    }
  }, 'JSON');

  //Variante 2
  $('#cbo-filtro_variante_2').html('<option value="0" selected="selected">- Sin registros -</option>');
  $('#cbo-filtro_valor_2').html('<option value="0" selected="selected">- Sin registros -</option>');
  url = base_url + 'HelperController/getVariantexIDTablaDato';
  $.post(url, { ID_Tabla_Dato: 2085 }, function (response) {
    if (response.sStatus == 'success') {
      var l = response.arrData.length;
      if (l == 1) {
        $('#cbo-filtro_variante_2').html('<option value="0" selected="selected">- Todos -</option>');
        $('#cbo-filtro_variante_2').append('<option value="' + response.arrData[0].ID + '">' + response.arrData[0].Nombre + '</option>');

        $('#cbo-filtro_valor_2').html('<option value="0" selected="selected">- Sin registros -</option>');
        url = base_url + 'HelperController/getVarianteDetalle';
        var arrParams = {
          ID_Variante_Item: response.arrData[0].ID,
        }
        $.post(url, arrParams, function (responseDetalle) {
          $('#cbo-filtro_valor_2').html('<option value="0" selected="selected">- Todos -</option>');
          if (responseDetalle.sStatus == 'success') {
            var l = responseDetalle.arrData.length;
            for (var x = 0; x < l; x++)
              $('#cbo-filtro_valor_2').append('<option value="' + responseDetalle.arrData[x].ID + '">' + responseDetalle.arrData[x].Nombre + '</option>');
          } else {
            $('#cbo-filtro_valor_2').html('<option value="0" selected="selected">- vacío -</option>');
          }
        }, 'JSON');
      }
    } else {
      if (response.sMessageSQL !== undefined) {
        console.log(response.sMessageSQL);
      }
    }
  }, 'JSON');

  //Variante 3
  $('#cbo-filtro_variante_3').html('<option value="0" selected="selected">- Sin registros -</option>');
  $('#cbo-filtro_valor_3').html('<option value="0" selected="selected">- Sin registros -</option>');
  url = base_url + 'HelperController/getVariantexIDTablaDato';
  $.post(url, { ID_Tabla_Dato: 2086 }, function (response) {
    if (response.sStatus == 'success') {
      var l = response.arrData.length;
      if (l == 1) {
        $('#cbo-filtro_variante_3').html('<option value="0" selected="selected">- Todos -</option>');
        $('#cbo-filtro_variante_3').append('<option value="' + response.arrData[0].ID + '">' + response.arrData[0].Nombre + '</option>');

        $('#cbo-filtro_valor_3').html('<option value="0" selected="selected">- Sin registros -</option>');
        url = base_url + 'HelperController/getVarianteDetalle';
        var arrParams = {
          ID_Variante_Item: response.arrData[0].ID,
        }
        $.post(url, arrParams, function (responseDetalle) {
          $('#cbo-filtro_valor_3').html('<option value="0" selected="selected">- Todos -</option>');
          if (responseDetalle.sStatus == 'success') {
            var l = responseDetalle.arrData.length;
            for (var x = 0; x < l; x++)
              $('#cbo-filtro_valor_3').append('<option value="' + responseDetalle.arrData[x].ID + '">' + responseDetalle.arrData[x].Nombre + '</option>');
          } else {
            $('#cbo-filtro_valor_3').html('<option value="0" selected="selected">- vacío -</option>');
          }
        }, 'JSON');
      }
    } else {
      if (response.sMessageSQL !== undefined) {
        console.log(response.sMessageSQL);
      }
    }
  }, 'JSON');

  url = base_url + 'LibrosPLE/KardexTextilController/getTiposLibroSunat';
  $.post( url, {ID_Tipo_Asiento : 3}, function( response ){
    if ( response.length == 1 ) {
      $('#cbo-TiposLibroSunatKardex').append('<option value="' + response[0].ID_Tipo_Asiento_Detalle + '" data-id_tipo_asiento="' + response[0].ID_Tipo_Asiento + '" data-nu_codigo_libro_sunat="' + response[0].Nu_Codigo_Libro_Sunat + '" data-no_tipo_asiento_apertura="' + response[0].No_Tipo_Asiento_Apertura + '">' + response[0].No_Sub_Libro_Sunat + '</option>');
    } else {
      $( '#cbo-TiposLibroSunatKardex' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
      for (var i = 0, len = response.length; i < len; i++)
        $( '#cbo-TiposLibroSunatKardex' ).append( '<option value="' + response[i].ID_Tipo_Asiento_Detalle + '" data-id_tipo_asiento="' + response[i].ID_Tipo_Asiento + '" data-nu_codigo_libro_sunat="' + response[i].Nu_Codigo_Libro_Sunat + '" data-no_tipo_asiento_apertura="' + response[i].No_Tipo_Asiento_Apertura + '">' + response[i].No_Sub_Libro_Sunat + '</option>' );
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

  $( '.div-productos' ).hide();
  $( '#txt-ID_Producto' ).val(0);
  $( '#cbo-FiltrosProducto' ).change(function() {
    $( '.div-productos' ).hide();
    $( '#txt-ID_Producto' ).val(0);
    $( '#txt-No_Producto' ).val('');
    if ( $(this).val() > 0 )
      $( '.div-productos' ).show();
  })
  
  $( '#table-Kardex' ).hide();
  $( '#table-Kardex_sin_movimientos' ).hide();

  $("#cbo-Almacenes_filtro_kardex").select2({
    placeholder: "- Todos -",
    allowClear: true
  });

  $( '.btn-generar_kardex' ).click(function(){
    if ( $( '#cbo-TiposLibroSunatKardex' ).val() == 0 ) {
      $( '#cbo-TiposLibroSunatKardex' ).closest('.form-group').find('.help-block').html('Seleccionar libro');
		  $( '#cbo-TiposLibroSunatKardex' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      $('.help-block').empty();
      $('.form-group').removeClass('has-error');
		  
      var ID_Tipo_Asiento, ID_Tipo_Asiento_Detalle, ID_Almacen, dInicio, dFin, ID_Producto, Txt_Direccion_Almacen, Nu_Codigo_Libro_Sunat, No_Tipo_Asiento_Apertura, ID_Tipo_Movimiento, iFiltroBusquedaNombre, sNombreItem, ID_Familia, ID_Sub_Familia, ID_Marca, ID_Variante_Item, ID_Variante_Item_Detalle_1, ID_Variante_Item2, ID_Variante_Item_Detalle_2, ID_Variante_Item3, ID_Variante_Item_Detalle_3, iFiltroItemMovimiento;
      
      var arrAlmacen = ($( '#cbo-Almacenes_filtro_kardex' ).val().length==0 ? '-' : $( '#cbo-Almacenes_filtro_kardex' ).val());
      if(arrAlmacen!='-' && Array.isArray($( '#cbo-Almacenes_filtro_kardex' ).val())){
        var arrAlmacen = arrAlmacen.join();
      }

      ID_Tipo_Asiento = $( '#cbo-TiposLibroSunatKardex' ).find(':selected').data('id_tipo_asiento');
      ID_Tipo_Asiento_Detalle = $( '#cbo-TiposLibroSunatKardex' ).val();
      ID_Almacen = arrAlmacen;
      dInicio = ParseDateString($( '#txt-Filtro_Fe_Inicio' ).val(), 1, '/');
      dFin = ParseDateString($( '#txt-Filtro_Fe_Fin' ).val(), 1, '/');
      ID_Producto  = $( '#txt-ID_Producto' ).val();
      Txt_Direccion_Almacen = '-';
      Nu_Codigo_Libro_Sunat = $( '#cbo-TiposLibroSunatKardex' ).find(':selected').data('nu_codigo_libro_sunat');
      No_Tipo_Asiento_Apertura = $( '#cbo-TiposLibroSunatKardex' ).find(':selected').data('no_tipo_asiento_apertura');
      ID_Tipo_Movimiento = $('#cbo-filtro_tipo_movimiento').val();
      iFiltroBusquedaNombre = ($("#checkbox-busqueda_producto").prop("checked") == true ? 1 : 0);
      sNombreItem  = ($( '#txt-No_Producto' ).val().length == 0 ? '-' : $( '#txt-No_Producto' ).val());
      ID_Familia = $( '#cbo-filtro_categoria' ).val();
      ID_Sub_Familia = $( '#cbo-filtro_sub_categoria' ).val();
      ID_Marca = $( '#cbo-filtro_marca' ).val();
      ID_Variante_Item = $( '#cbo-filtro_variante_1' ).val();
      ID_Variante_Item_Detalle_1 = $( '#cbo-filtro_valor_1' ).val();
      ID_Variante_Item2 = $( '#cbo-filtro_variante_2' ).val();
      ID_Variante_Item_Detalle_2 = $( '#cbo-filtro_valor_2' ).val();
      ID_Variante_Item3 = $( '#cbo-filtro_variante_3' ).val();
      ID_Variante_Item_Detalle_3 = $( '#cbo-filtro_valor_3' ).val();
      iFiltroItemMovimiento = $('[name="radio-filtro_item_movimiento"]:checked').attr('value');
      
      sNombreItem = sNombreItem.replace('/', '-');

		  if ($(this).data('type') == 'html') {
        $( '#btn-html_kardex' ).text('');
        $( '#btn-html_kardex' ).attr('disabled', true);
        $( '#btn-html_kardex' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
  
  	    $( '#table-Kardex >tbody' ).empty();
  	    $( '#table-Kardex >tfoot' ).empty();
        
  	    $( '#table-Kardex_sin_movimientos >tbody' ).empty();
  	    
        var arrPost = {
          ID_Tipo_Asiento : ID_Tipo_Asiento,
          ID_Tipo_Asiento_Detalle : ID_Tipo_Asiento_Detalle,
          ID_Almacen : ID_Almacen,
          dInicio : dInicio,
          dFin : dFin,
          ID_Producto : ID_Producto,
          ID_Tipo_Movimiento: ID_Tipo_Movimiento,
          iFiltroBusquedaNombre: iFiltroBusquedaNombre,
          sNombreItem: sNombreItem,
          ID_Familia: ID_Familia,
          ID_Sub_Familia: ID_Sub_Familia,
          ID_Marca: ID_Marca,
          ID_Variante_Item: ID_Variante_Item,
          ID_Variante_Item_Detalle_1: ID_Variante_Item_Detalle_1,
          ID_Variante_Item2: ID_Variante_Item2,
          ID_Variante_Item_Detalle_2: ID_Variante_Item_Detalle_2,
          ID_Variante_Item3: ID_Variante_Item3,
          ID_Variante_Item_Detalle_3: ID_Variante_Item_Detalle_3,
          iFiltroItemMovimiento: iFiltroItemMovimiento
        };
        
        if (ID_Tipo_Asiento == 3) {
          url = base_url + 'LibrosPLE/KardexTextilController/kardex';
          $.post( url, arrPost, function( responseData ){
            if ( responseData.sStatus == 'success' ) {
              var iTotalRegistros = responseData.arrData.length, response=responseData.arrData, tr_body='', tr_foot='';
              var $ID_Almacen = 0, $counter_almacen = 0, $sum_Almacen_Producto_Qt_Entrada = 0.00, $sum_Almacen_Producto_Qt_Salida = 0.00;
              var $ID_Producto = 0, $counter = 0, $sum_Producto_Qt_Entrada = 0.00, $sum_Producto_Qt_Salida = 0.00, $sum_General_Qt_Entrada = 0.00, $sum_General_Qt_Salida = 0.00, $Qt_Producto_Saldo_Movimiento = 0.00;
              var sNombreAlmacen = '', fSumSaldoInicialxProductoAlmacen=0;
              for (var i = 0; i < iTotalRegistros; i++) {
                if ($ID_Producto != response[i].ID_Producto || $ID_Almacen != response[i].ID_Almacen) {
                  if ($counter != 0) {
                    tr_body +=
                      +"<tr style='background-color: #d0d0d08a !important;'>"
                      + "<th class='text-right' colspan='9' style='background-color: #d0d0d08a;'>TOTAL PRODUCTO</th>"
                      + "<th class='text-right' style='background-color: #d0d0d08a;'>" + Math.round10($sum_Producto_Qt_Entrada, -3) + "</th>"
                      + "<th class='text-right' style='background-color: #d0d0d08a;'>" + Math.round10($sum_Producto_Qt_Salida, -3) + "</th>"
                      + "<th class='text-right' style='background-color: #d0d0d08a;'>" + ($Qt_Producto_Saldo_Movimiento < 0 ? '-' : '') + number_format($Qt_Producto_Saldo_Movimiento, 3) + "</th>"
                      + "</tr>";
                  }

                  if ($ID_Almacen != response[i].ID_Almacen) {
                    if ($counter_almacen != 0) {
                      tr_body +=
                      "<tr style='background-color: #b3b3b3;'>"
                        + "<th class='text-right' colspan='9'>TOTAL ALMACÉN: " + response[i-1].No_Almacen + "</th>"
                        + "<th class='text-right'>" + Math.round10($sum_Almacen_Producto_Qt_Entrada, -3) + "</th>"
                        + "<th class='text-right'>" + Math.round10($sum_Almacen_Producto_Qt_Salida, -3) + "</th>"
                        + "<th class='text-right'>" + Math.round10(fSumSaldoInicialxProductoAlmacen, -3) + "</th>"
                        + "<th class='text-right'>" + Math.round10((fSumSaldoInicialxProductoAlmacen + $sum_Almacen_Producto_Qt_Entrada - $sum_Almacen_Producto_Qt_Salida), -3) + "</th>"
                        + "<th class='text-right' colspan='2'></th>"
                      + "</tr>";
                      sNombreAlmacen = response[i].No_Almacen;
                    }

                    $sum_Almacen_Producto_Qt_Entrada = 0.00;
                    $sum_Almacen_Producto_Qt_Salida = 0.00;

                    fSumSaldoInicialxProductoAlmacen = 0.00;

                    tr_body +=
                      "<tr style='background-color: #b3b3b3;'>"
                      + "<th class='text-right'><span style='font-size: 15px;'>ALMACÉN</span></th>"
                      + "<th class='text-left' colspan='15'><span style='font-size: 15px;'>" + response[i].No_Almacen + "</span></th>"
                      + "</tr>";

                    $ID_Almacen = response[i].ID_Almacen;
                  }// if almacen

                  $sum_Producto_Qt_Entrada = 0.00;
                  $sum_Producto_Qt_Salida = 0.00;

                  tr_body +=
                    "<tr>"
                    + "<th class='text-right'>UPC</th>"
                    + "<th class='text-left'>" + response[i].Nu_Codigo_Barra + "</th>"
                    + "<th class='text-right'>SKU</th>"
                    + "<th class='text-left'>" + response[i].No_Codigo_Interno + "</th>"
                    + "<th class='text-right'>Nombre</th>"
                    + "<th class='text-left' colspan='4'>" + response[i].No_Producto + "</th>"
                    + "<th class='text-right' colspan='2'>SALDO INICIAL</th>"
                    + "<th class='text-right'>" + (parseFloat(response[i].Qt_Producto_Inicial) < 0 ? '-' : '') + number_format(parseFloat(response[i].Qt_Producto_Inicial), 3) + "</th>"
                    + "</tr>";
                  $ID_Producto = response[i].ID_Producto;
                  $Qt_Producto_Saldo_Movimiento = parseFloat(response[i].Qt_Producto_Inicial);
                  
                  fSumSaldoInicialxProductoAlmacen += parseFloat(response[i].Qt_Producto_Inicial);
                }// if producto
                
                tr_body +=
                "<tr>"
                  +"<td class='text-center'>" + response[i].Fe_Emision + "</td>"
                  +"<td class='text-center'>" + response[i].Tipo_Documento_Sunat_Codigo + "</td>"
                  +"<td class='text-center'>" + response[i].No_Tipo_Documento_Breve + "</td>"
                  +"<td class='text-center'>" + response[i].ID_Serie_Documento + "</td>"
                  +"<td class='text-center'>" + response[i].ID_Numero_Documento + "</td>"
                  +"<td class='text-center'>" + response[i].Tipo_Operacion_Sunat_Codigo + "</td>"
                  +"<td class='text-center'>" + response[i].No_Tipo_Movimiento + "</td>"
                  +"<td class='text-left'>" + response[i].Nu_Documento_Identidad + "</td>"
                  +"<td class='text-left'>" + response[i].No_Entidad + "</td>";

                  if (response[i].Nu_Tipo_Movimiento == 0){//Entrada
                    tr_body +=
                    "<td class='text-right'>" + (parseFloat(response[i].Qt_Producto) < 0 ? '-' : '') + number_format(parseFloat(response[i].Qt_Producto), 3) + "</td>"
                    +"<td class='text-right'>0.00</td>";

                    $Qt_Producto_Saldo_Movimiento += parseFloat(response[i].Qt_Producto);

                    $sum_Producto_Qt_Entrada += parseFloat(response[i].Qt_Producto);
                    $sum_Almacen_Producto_Qt_Entrada += parseFloat(response[i].Qt_Producto);
                    $sum_General_Qt_Entrada += parseFloat(response[i].Qt_Producto);
                  } else { //Salida
                    tr_body +=
                    "<td class='text-right'>0.00</td>"
                    +"<td class='text-right'>" + (parseFloat(response[i].Qt_Producto) < 0 ? '-' : '') + number_format(parseFloat(response[i].Qt_Producto), 3) + "</td>";
                    
                    $Qt_Producto_Saldo_Movimiento -= parseFloat(response[i].Qt_Producto);

                    $sum_Producto_Qt_Salida += parseFloat(response[i].Qt_Producto);
                    $sum_Almacen_Producto_Qt_Salida += parseFloat(response[i].Qt_Producto);
                    $sum_General_Qt_Salida += parseFloat(response[i].Qt_Producto);
                  }

                  tr_body += "<td class='text-right'>" + ($Qt_Producto_Saldo_Movimiento < 0 ? '-' : '') + number_format($Qt_Producto_Saldo_Movimiento, 3) + "</td>"
                  tr_body += "<td class='text-center'><span class='label label-" + response[i].No_Class_Estado + "'>" + response[i].No_Estado + "</td>"
                +"</tr>";

                $counter++;
                $counter_almacen++;
              }// fin de for
            
              tr_foot =
              "<tfoot>"
                +"<tr style='background-color: #d0d0d08a !important;'>"
                  +"<th class='text-right' colspan='9' style='background-color: #d0d0d08a;'>TOTAL PRODUCTO</th>"
                  +"<th class='text-right' style='background-color: #d0d0d08a;'>" + Math.round10($sum_Producto_Qt_Entrada, -3) + "</th>"
                  +"<th class='text-right' style='background-color: #d0d0d08a;'>" + Math.round10($sum_Producto_Qt_Salida, -3) + "</th>"
                  +"<th class='text-right' style='background-color: #d0d0d08a;'>" + ($Qt_Producto_Saldo_Movimiento < 0 ? '-' : '') + number_format($Qt_Producto_Saldo_Movimiento, 3) + "</th>"
                + "</tr>"
                +"<tr style='background-color: #b3b3b3;'>"
                  + "<th class='text-right' colspan='9'>TOTAL ALMACÉN: " + sNombreAlmacen + "</th>"
                  + "<th class='text-right'>" + Math.round10($sum_Almacen_Producto_Qt_Entrada, -3) + "</th>"
                  + "<th class='text-right'>" + Math.round10($sum_Almacen_Producto_Qt_Salida, -3) + "</th>"
                  + "<th class='text-right'>" + Math.round10(fSumSaldoInicialxProductoAlmacen, -3) + "</th>"
                  + "<th class='text-right'>" + Math.round10((fSumSaldoInicialxProductoAlmacen + $sum_Almacen_Producto_Qt_Entrada - $sum_Almacen_Producto_Qt_Salida), -3) + "</th>"
                  + "<th class='text-right' colspan='2'></th>"
                + "</tr>"
                +"<tr>"
                  +"<th class='text-right' colspan='9'>TOTAL GENERAL C/M</th>"
                  +"<th class='text-right'>" + Math.round10($sum_General_Qt_Entrada, -3) + "</th>"
                  +"<th class='text-right'>" + Math.round10($sum_General_Qt_Salida, -3) + "</th>"
                +"</tr>"
              +"</tfoot>";
            } else {
              /*
              tr_body +=
              "<tr>"
                + "<td colspan='13' class='text-center'>No hay registros</td>"
              + "</tr>";
              */
            }
            
            $( '#table-Kardex' ).show();
            $( '#table-Kardex >tbody' ).append(tr_body);
            $( '#table-Kardex >tbody' ).after(tr_foot);
                     
            iTotalRegistros = responseData.arrDataAlmacenSinMovimiento.length;
            //response=responseData.arrDataAlmacenSinMovimiento;
            response_producto=responseData.arrDataAlmacenSinMovimiento;
            $ID_Almacen = 0;

            console.log(iTotalRegistros);
            console.log(response);

            $fSumSaldoInicialxProductoAlmacenSM=0;
            $counter_almacen=0;
            sNombreAlmacen='';
            $fSumGeneralProductoAlmacen=0;
            if(iTotalRegistros>0){
              tr_body='';
              //for (var p = 0; p < response[0].length; p++) {
                //response_producto = response[0];
              for (var p = 0; p < iTotalRegistros; p++) {
                if ($ID_Almacen != response_producto[p].ID_Almacen) {
                  if ($counter_almacen != 0) {
                    tr_body +=
                    "<tr style='background-color: #b3b3b3;'>"
                      + "<th class='text-right' colspan='8'>TOTAL ALMACÉN: " + response_producto[p-1].No_Almacen + "</th>"
                      + "<th class='text-right'>" + Math.round10($fSumSaldoInicialxProductoAlmacenSM, -3) + "</th>"
                      + "<th class='text-right' colspan='2'></th>"
                    + "</tr>";
                    sNombreAlmacen = response[i].No_Almacen;
                  }

                  tr_body += "<tr>"
                  + "<th class='text-right'><span style='font-size: 15px;'>ALMACÉN</span></th>"
                  + "<th class='text-left' colspan='15'><span style='font-size: 15px;'>" + response_producto[p].No_Almacen + "</span></th>"
                  + "</tr>";
                  $ID_Almacen = response_producto[p].ID_Almacen;
                  $fSumSaldoInicialxProductoAlmacenSM=0;
                }

                tr_body += "<tr>"
                + "<th class='text-right'>UPC</th>"
                + "<th class='text-left'>" + response_producto[p].Nu_Codigo_Barra + "</th>"
                + "<th class='text-right'>Nombre</th>"
                + "<th class='text-left' colspan='4'>"
                  + response_producto[p].No_Producto
                  + (response_producto[p].No_Valor_Variante_1 !== null && response_producto[p].No_Variante_1 !== null ? ' / <strong>' + response_producto[p].No_Variante_1 + '</strong>: ' + response_producto[p].No_Valor_Variante_1 : '')
                  + (response_producto[p].No_Valor_Variante_2 !== null && response_producto[p].No_Variante_2 !== null ? ' / <strong>' + response_producto[p].No_Variante_2 + '</strong>: ' + response_producto[p].No_Valor_Variante_2 : '')
                  + (response_producto[p].No_Valor_Variante_3 !== null && response_producto[p].No_Variante_3 !== null ? ' / <strong>' + response_producto[p].No_Variante_3 + '</strong>: ' + response_producto[p].No_Valor_Variante_3 : '')
                + "</th>"
                + "<th class='text-right'>SALDO INICIAL</th>"
                + "<th class='text-right'>" + (parseFloat(response_producto[p].Qt_Producto) < 0 ? '-' : '') + number_format(parseFloat(response_producto[p].Qt_Producto), 3) + "</th>"
                + "</tr>";
                
                $fSumSaldoInicialxProductoAlmacenSM += parseFloat(response_producto[p].Qt_Producto);
                $fSumGeneralProductoAlmacen += parseFloat(response_producto[p].Qt_Producto);
                ++$counter_almacen;
              }
              
              tr_foot =
              "<tfoot>"
                +"<tr style='background-color: #b3b3b3;'>"
                  + "<th class='text-right' colspan='8'>TOTAL ALMACÉN: " + sNombreAlmacen + "</th>"
                  + "<th class='text-right'>" + Math.round10($fSumSaldoInicialxProductoAlmacenSM, -3) + "</th>"
                  + "<th class='text-right' colspan='2'></th>"
                + "</tr>"
                +"<tr style='background-color: #b3b3b3;'>"
                  + "<th class='text-right' colspan='8'>TOTAL GENERAL S/M</th>"
                  + "<th class='text-right'>" + Math.round10($fSumGeneralProductoAlmacen, -3) + "</th>"
                  + "<th class='text-right' colspan='2'></th>"
                + "</tr>"
                +"<tr style='background-color: #b3b3b3;'>"
                  + "<th class='text-right' colspan='8'>TOTAL GENERAL</th>"
                  + "<th class='text-right'>" + Math.round10($fSumGeneralProductoAlmacen + (fSumSaldoInicialxProductoAlmacen + $sum_Almacen_Producto_Qt_Entrada - $sum_Almacen_Producto_Qt_Salida), -3) + "</th>"
                  + "<th class='text-right' colspan='2'></th>"
                + "</tr>"
              +"</tfoot>";

              $( '#table-Kardex_sin_movimientos' ).show();
              $( '#table-Kardex_sin_movimientos >tbody' ).append(tr_body);
              $( '#table-Kardex_sin_movimientos >tbody' ).after(tr_foot);
            }

            $( '#btn-html_kardex' ).text('');
            $( '#btn-html_kardex' ).append( '<i class="fa fa-table"></i> HTML' );
            $( '#btn-html_kardex' ).attr('disabled', false);
          }, 'JSON')
          .fail(function(jqXHR, textStatus, errorThrown) {
            $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
            
            $( '#modal-message' ).modal('show');
            $( '.modal-message' ).addClass( 'modal-danger' );
            $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
            setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
            
            //Message for developer
            console.log(jqXHR.responseText);
            
            $( '#btn-html_kardex' ).text('');
            $( '#btn-html_kardex' ).append( '<i class="fa fa-search"></i> Buscar' );
            $( '#btn-html_kardex' ).attr('disabled', false);
          });
        }
		  } else if ($(this).data('type') == 'pdf') {
        $( '#btn-pdf_kardex' ).text('');
        $( '#btn-pdf_kardex' ).attr('disabled', true);
        $( '#btn-pdf_kardex' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
            
        url = base_url + 'LibrosPLE/KardexTextilController/kardexPDF/' + ID_Tipo_Asiento + '/' + ID_Tipo_Asiento_Detalle + '/' + ID_Almacen + '/' + dInicio + '/' + dFin + '/' + ID_Producto + '/' + encodeURIComponent(Txt_Direccion_Almacen) + '/' + Nu_Codigo_Libro_Sunat + '/' + No_Tipo_Asiento_Apertura + '/' + ID_Tipo_Movimiento + '/' + iFiltroBusquedaNombre + '/' + encodeURIComponent(sNombreItem) + '/' + ID_Familia + '/' + ID_Sub_Familia + '/' + ID_Marca + '/' + ID_Variante_Item + '/' + ID_Variante_Item_Detalle_1 + '/' + ID_Variante_Item2 + '/' + ID_Variante_Item_Detalle_2 + '/' + ID_Variante_Item3 + '/' + ID_Variante_Item_Detalle_3 + '/' + iFiltroItemMovimiento;
        window.open(url,'_blank');

        $( '#btn-pdf_kardex' ).text('');
        $( '#btn-pdf_kardex' ).append( '<i class="fa fa-file-pdf-o color_white"></i> PDF' );
        $( '#btn-pdf_kardex' ).attr('disabled', false);
		  } else if ($(this).data('type') == 'excel') {
		    $( '#btn-excel_kardex' ).text('');
        $( '#btn-excel_kardex' ).attr('disabled', true);
        $( '#btn-excel_kardex' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
        
        url = base_url + 'LibrosPLE/KardexTextilController/kardexEXCEL/' + ID_Tipo_Asiento + '/' + ID_Tipo_Asiento_Detalle + '/' + ID_Almacen + '/' + dInicio + '/' + dFin + '/' + ID_Producto + '/' + encodeURIComponent(Txt_Direccion_Almacen) + '/' + Nu_Codigo_Libro_Sunat + '/' + No_Tipo_Asiento_Apertura + '/' + ID_Tipo_Movimiento + '/' + iFiltroBusquedaNombre + '/' + encodeURIComponent(sNombreItem) + '/' + ID_Familia + '/' + ID_Sub_Familia + '/' + ID_Marca + '/' + ID_Variante_Item + '/' + ID_Variante_Item_Detalle_1 + '/' + ID_Variante_Item2 + '/' + ID_Variante_Item_Detalle_2 + '/' + ID_Variante_Item3 + '/' + ID_Variante_Item_Detalle_3 + '/' + iFiltroItemMovimiento;
        window.open(url,'_blank');
        
        $( '#btn-excel_kardex' ).text('');
        $( '#btn-excel_kardex' ).append( '<i class="fa fa-file-excel-o color_white"></i> Excel' );
        $( '#btn-excel_kardex' ).attr('disabled', false);
		  } else if ($(this).data('type') == 'txt') {
		    $( '#btn-txt_kardex' ).text('');
        $( '#btn-txt_kardex' ).attr('disabled', true);
        $( '#btn-txt_kardex' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
        
        url = base_url + 'LibrosPLE/KardexTextilController/kardexTXT/' + ID_Tipo_Asiento + '/' + ID_Tipo_Asiento_Detalle + '/' + ID_Almacen + '/' + dInicio + '/' + dFin + '/' + ID_Producto + '/' + encodeURIComponent(Txt_Direccion_Almacen) + '/' + Nu_Codigo_Libro_Sunat + '/' + No_Tipo_Asiento_Apertura + '/' + ID_Tipo_Movimiento + '/' + iFiltroBusquedaNombre + '/' + encodeURIComponent(sNombreItem) + '/' + ID_Familia + '/' + ID_Sub_Familia + '/' + ID_Marca + '/' + ID_Variante_Item + '/' + ID_Variante_Item_Detalle_1 + '/' + ID_Variante_Item2 + '/' + ID_Variante_Item_Detalle_2 + '/' + ID_Variante_Item3 + '/' + ID_Variante_Item_Detalle_3;
        window.open(url,'_blank');

        $( '#btn-txt_kardex' ).text('');
        $( '#btn-txt_kardex' ).append( '<i class="fa fa-files-o"></i> Libro Electrónico' );
        $( '#btn-txt_kardex' ).attr('disabled', false);
		  }
    }
  })
})

// Ayudas - combobox
function getAlmacenes(arrParams){
  $('#cbo-Almacenes_filtro_kardex').html('<option value="0" data-direccion_almacen="todos">- Todos -</option>');
  url = base_url + 'HelperController/getAlmacenesEmpresa';
  $.post( url, {}, function( responseAlmacen ){
    var iCantidadRegistros = responseAlmacen.length;
    var selected = '';
    var iIdAlmacen = 0;
    $('#cbo-Almacenes_filtro_kardex').html('<option value="0" data-direccion_almacen="todos">- Todos -</option>');
    if (iCantidadRegistros == 1) {
      if (arrParams !== undefined) {
        iIdAlmacen = arrParams.ID_Almacen;
      }
      if (iIdAlmacen == responseAlmacen[0]['ID_Almacen']){
        selected = 'selected="selected"';
      }
      $('#cbo-Almacenes_filtro_kardex').append( '<option value="' + responseAlmacen[0]['ID_Almacen'] + '" ' + selected + ' data-direccion_almacen="' + responseAlmacen[0]['Txt_Direccion_Almacen'] + '">' + responseAlmacen[0]['No_Almacen'] + '</option>' );
    } else {
      for (var i = 0; i < iCantidadRegistros; i++) {
        if (arrParams !== undefined) {
          iIdAlmacen = arrParams.ID_Almacen;
        }
        if (iIdAlmacen == responseAlmacen[0]['ID_Almacen']){
          selected = 'selected="selected"';
        }
        $( '#cbo-Almacenes_filtro_kardex' ).append( '<option value="' + responseAlmacen[i]['ID_Almacen'] + '" ' + selected + ' data-direccion_almacen="' + responseAlmacen[i]['Txt_Direccion_Almacen'] + '">' + responseAlmacen[i]['No_Almacen'] + '</option>' );
      }
    }
    $( '#modal-loader' ).modal('hide');
  }, 'JSON');
}