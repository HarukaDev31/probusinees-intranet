var url, arrMedioPagoBD = {}, arrMedioPagoTotales = {};

$(function () {
  //actualizar medio de pago

  $('.div-mas_opciones').hide();
  $('#checkbox-mas_filtros').on('ifChanged', function(){
    $('.div-mas_opciones').hide();
    var _this = jQuery(this);
    if(_this.is(':checked')){
      $('.div-mas_opciones').show();
    }
  });
  
  // COBRAR CREDITO MASIVO
  $( '#btn-medio_pago' ).click(function(){
    $( '.help-block' ).empty();
    
    $( '#btn-medio_pago' ).text('');
    $( '#btn-medio_pago' ).attr('disabled', true);
    $( '#btn-medio_pago' ).append( 'Actualizando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
    $( '#btn-salir' ).attr('disabled', true);

    url = base_url + 'Ventas/informes_venta/ReporteFormaPagoController/actualizarMedioPago';
    $.ajax({
      type : 'POST',
      dataType : 'JSON',
      url : url,
      data: $('#form-medio_pago').serialize(),
      success : function( response ){
        $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
        $( '#modal-message' ).modal('show');

        if ( response.sStatus=='success' ) {
          $( '.modal-medio_pago' ).modal('hide');

          $( '.modal-message' ).addClass( 'modal-' + response.sStatus);
          $( '.modal-title-message' ).text( response.sMessage );
          setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
          
          getReporteHTML();
        } else {
          $( '.modal-message' ).addClass( 'modal-' + response.sStatus );
          $( '.modal-title-message' ).text( response.sMessage );
          setTimeout(function() {$('#modal-message').modal('hide');}, 3100);
        }
        
        $( '#btn-medio_pago' ).text('');
        $( '#btn-medio_pago' ).append( 'Actualizar' );
        $( '#btn-medio_pago' ).attr('disabled', false);
        $( '#btn-salir' ).attr('disabled', false);
      }
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
      $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
      
      $( '#modal-message' ).modal('show');
      $( '.modal-message' ).addClass( 'modal-danger' );
      $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
      setTimeout(function() {$('#modal-message').modal('hide');}, 3100);
      
      //Message for developer
      console.log(jqXHR.responseText);

      $( '#btn-medio_pago' ).text('');
      $( '#btn-medio_pago' ).attr('disabled', false);
      $( '#btn-medio_pago' ).append( 'Actualizar' );
      $( '#btn-salir' ).attr('disabled', false);
    })
  })
  //FIN DE ACTUALIZAR MEDIO DE PAGO

  $( '#modal-loader' ).modal('show');
  $( '#div-ventas_detalladas_generales' ).hide();

  var arrParams = {};
  getAlmacenes(arrParams);

  //Global Autocomplete
  $('.autocompletar_personal').autoComplete({
    minChars: 0,
    source: function (term, response) {
      var term = term.toLowerCase();
      var global_class_method = $('.autocompletar_personal').data('global-class_method');
      var global_table = $('.autocompletar_personal').data('global-table');
      var filter_id_codigo = '';
      $.post(base_url + global_class_method, { global_table: global_table, global_search: term, filter_id_codigo: filter_id_codigo }, function (arrData) {
        response(arrData);
      }, 'JSON');
    },
    renderItem: function (item, search) {
      search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
      var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");
      return '<div class="autocomplete-suggestion" data-id="' + item.ID + '" data-nombre="' + item.Nombre + '" data-val="' + search + '">' + item.Nombre.replace(re, "<b>$1</b>") + '</div>';
    },
    onSelect: function (e, term, item) {
      $('#txt-AID_Personal').val(item.data('id'));
      $('#txt-Filtro_Personal').val(item.data('nombre'));
      $('#txt-Filtro_Personal').closest('.form-group').find('.help-block').html('');
      $('#txt-Filtro_Personal').closest('.form-group').removeClass('has-error');
    }
  });
  
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
          sTipoSerie = '(' + ( response[i].ID_POS > 0 ? 'Punto Venta' : 'Oficina' ) + ')';
          $( '#cbo-filtros_series_documento' ).append( '<option value="' + response[i].ID_Serie_Documento + '">' + response[i].ID_Serie_Documento + ' ' + sTipoSerie + '</option>' );
        }
      }, 'JSON');
	  }
  })
	
	url = base_url + 'HelperController/getMediosPago';
	var arrPost = {
	  iIdEmpresa : $( '#cbo-Empresas' ).val(),
	};
	$.post( url, arrPost, function( response ){
	  $( '#cbo-forma_pago' ).html('<option value="0">Todos</option>');
	  for (var i = 0; i < response.length; i++) {
		  $( '#cbo-forma_pago' ).append( '<option value="' + response[i].ID_Medio_Pago + '" data-nu_tipo_medio_pago="' + response[i].Nu_Tipo + '">' + response[i].No_Medio_Pago + '</option>' );
      /*
      arrMedioPagoBD[i] = {
        'ID_Medio_Pago':response[i].ID_Medio_Pago,
        'No_Medio_Pago':response[i].No_Medio_Pago
      };
      */
    }
    /*
    console.log(Object.keys(arrMedioPagoBD).length);
    */
  }, 'JSON');



  $('#cbo-tipo_tarjeta').html('<option value="0">Todos</option>');
  $( '.div-tipos_tarjetas').hide();
	$( '#cbo-forma_pago' ).change(function(){
		ID_Medio_Pago = $(this).val();
		Nu_Tipo_Medio_Pago = $(this).find(':selected').data('nu_tipo_medio_pago');
		$( '.div-tipos_tarjetas').hide();
		$( '#cbo-tipo_tarjeta' ).html('<option value="0">Todos</option>');
		if (Nu_Tipo_Medio_Pago==2){
			$( '.div-tipos_tarjetas').show();

			url = base_url + 'HelperController/getTiposTarjetaCredito';
			$.post( url, {ID_Medio_Pago : ID_Medio_Pago} , function( response ){
				$( '#cbo-tipo_tarjeta' ).html('<option value="0">Todos</option>');
				for (var i = 0; i < response.length; i++)
					$( '#cbo-tipo_tarjeta' ).append( '<option value="' + response[i].ID_Tipo_Medio_Pago + '">' + response[i].No_Tipo_Medio_Pago + '</option>' );
			}, 'JSON');
		}
  })
  
  $( '.btn-generar_ventas_detalladas_generales' ).click(function(){    
    var sMethod, ID_Almacen, Fe_Inicio, Fe_Fin, ID_Tipo_Documento, ID_Serie_Documento, ID_Numero_Documento, Nu_Estado_Documento, iIdCliente, sNombreCliente, iTipoVenta, iMedioPago, iIdPersonal, sNombrePersonal, iTipoTarjeta, Nu_Tipo_Impuesto;
    
    sMethod = $( '#hidden-sMethod' ).val();
    Fe_Inicio = ParseDateString($( '#txt-Filtro_Fe_Inicio' ).val(), 1, '/');
    Fe_Fin = ParseDateString($( '#txt-Filtro_Fe_Fin' ).val(), 1, '/');
    ID_Tipo_Documento = $( '#cbo-filtros_tipos_documento' ).val();
    ID_Serie_Documento = $( '#cbo-filtros_series_documento' ).val();
    ID_Numero_Documento = ($( '#txt-Filtro_NumeroDocumento' ).val().length == 0 ? '-' : $( '#txt-Filtro_NumeroDocumento' ).val());
    Nu_Estado_Documento = $( '#cbo-estado_documento' ).val();
    iIdCliente = ($( '#txt-AID' ).val().length === 0 ? '-' : $( '#txt-AID' ).val());
    sNombreCliente = ($('#txt-Filtro_Entidad').val().length === 0 ? '-' : $('#txt-Filtro_Entidad').val());
    iIdPersonal = ($('#txt-AID_Personal').val().length === 0 ? '-' : $('#txt-AID_Personal').val());
    sNombrePersonal = ($('#txt-Filtro_Personal').val().length === 0 ? '-' : $('#txt-Filtro_Personal').val());
    iTipoVenta = $( '#cbo-tipo_venta' ).val();
    iMedioPago = $( '#cbo-forma_pago' ).val();
    iTipoTarjeta = $('#cbo-tipo_tarjeta').val();
    ID_Almacen = $('#cbo-Almacenes_ReporteFormaPago').val();
    Nu_Tipo_Impuesto = $('#cbo-regalo').val();

    if ($(this).data('type') == 'html') {
      getReporteHTML();
    } else if ($(this).data('type') == 'pdf') {
      $( '#btn-pdf_ventas_detalladas_generales' ).text('');
      $( '#btn-pdf_ventas_detalladas_generales' ).attr('disabled', true);
      $( '#btn-pdf_ventas_detalladas_generales' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
          
      url = base_url + 'Ventas/informes_venta/ReporteFormaPagoController/sendReportePDF/' + Fe_Inicio + '/' + Fe_Fin + '/' + ID_Tipo_Documento + '/' + ID_Serie_Documento + '/' + ID_Numero_Documento + '/' + Nu_Estado_Documento + '/' + iIdCliente + '/' + encodeURIComponent(sNombreCliente) + '/' + iTipoVenta + '/' + iMedioPago + '/' + iIdPersonal + '/' + encodeURIComponent(sNombrePersonal) + '/' + iTipoTarjeta + '/' + ID_Almacen + '/' + Nu_Tipo_Impuesto;
      window.open(url,'_blank');
      
      $( '#btn-pdf_ventas_detalladas_generales' ).text('');
      $( '#btn-pdf_ventas_detalladas_generales' ).append( '<i class="fa fa-file-pdf-o color_icon_pdf"></i> PDF' );
      $( '#btn-pdf_ventas_detalladas_generales' ).attr('disabled', false);
    } else if ($(this).data('type') == 'excel') {
      $( '#btn-excel_ventas_detalladas_generales' ).text('');
      $( '#btn-excel_ventas_detalladas_generales' ).attr('disabled', true);
      $( '#btn-excel_ventas_detalladas_generales' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
      
      url = base_url + 'Ventas/informes_venta/ReporteFormaPagoController/sendReporteEXCEL/' + Fe_Inicio + '/' + Fe_Fin + '/' + ID_Tipo_Documento + '/' + ID_Serie_Documento + '/' + ID_Numero_Documento + '/' + Nu_Estado_Documento + '/' + iIdCliente + '/' + encodeURIComponent(sNombreCliente) + '/' + iTipoVenta + '/' + iMedioPago + '/' + iIdPersonal + '/' + encodeURIComponent(sNombrePersonal) + '/' + iTipoTarjeta + '/' + ID_Almacen + '/' + Nu_Tipo_Impuesto;
      window.open(url,'_blank');
      
      $( '#btn-excel_ventas_detalladas_generales' ).text('');
      $( '#btn-excel_ventas_detalladas_generales' ).append( '<i class="fa fa-file-excel-o color_icon_excel"></i> Excel' );
      $( '#btn-excel_ventas_detalladas_generales' ).attr('disabled', false);
    }// /. if
  })// /. btn
})

// Ayudas - combobox
function getAlmacenes(arrParams) {
  $('#cbo-Almacenes_ReporteFormaPago').html('<option value="0">- Todos -</option>');
  url = base_url + 'HelperController/getAlmacenes';
  $.post(url, {}, function (responseAlmacen) {
    var iCantidadRegistros = responseAlmacen.length;
    var selected = '';
    var iIdAlmacen = 0;
    $('#cbo-Almacenes_ReporteFormaPago').html('<option value="0">- Todos -</option>');
    if (iCantidadRegistros == 1) {
      if (arrParams !== undefined) {
        iIdAlmacen = arrParams.ID_Almacen;
      }
      if (iIdAlmacen == responseAlmacen[0]['ID_Almacen']) {
        selected = 'selected="selected"';
      }
      $('#cbo-Almacenes_ReporteFormaPago').append('<option value="' + responseAlmacen[0]['ID_Almacen'] + '" ' + selected + ' data-direccion_almacen="' + responseAlmacen[0]['Txt_Direccion_Almacen'] + '">' + responseAlmacen[0]['No_Almacen'] + '</option>');
    } else {
      for (var i = 0; i < iCantidadRegistros; i++) {
        if (arrParams !== undefined) {
          iIdAlmacen = arrParams.ID_Almacen;
        }
        if (iIdAlmacen == responseAlmacen[0]['ID_Almacen']) {
          selected = 'selected="selected"';
        }
        $('#cbo-Almacenes_ReporteFormaPago').append('<option value="' + responseAlmacen[i]['ID_Almacen'] + '" ' + selected + ' data-direccion_almacen="' + responseAlmacen[0]['Txt_Direccion_Almacen'] + '">' + responseAlmacen[i]['No_Almacen'] + '</option>');
      }
    }
    $('#modal-loader').modal('hide');
  }, 'JSON');
}

function getReporteHTML(){
  var sMethod, ID_Almacen, Fe_Inicio, Fe_Fin, ID_Tipo_Documento, ID_Serie_Documento, ID_Numero_Documento, Nu_Estado_Documento, iIdCliente, sNombreCliente, iTipoVenta, iMedioPago, iIdPersonal, sNombrePersonal, iTipoTarjeta, Nu_Tipo_Impuesto;
  
  sMethod = $( '#hidden-sMethod' ).val();
  Fe_Inicio = ParseDateString($( '#txt-Filtro_Fe_Inicio' ).val(), 1, '/');
  Fe_Fin = ParseDateString($( '#txt-Filtro_Fe_Fin' ).val(), 1, '/');
  ID_Tipo_Documento = $( '#cbo-filtros_tipos_documento' ).val();
  ID_Serie_Documento = $( '#cbo-filtros_series_documento' ).val();
  ID_Numero_Documento = ($( '#txt-Filtro_NumeroDocumento' ).val().length == 0 ? '-' : $( '#txt-Filtro_NumeroDocumento' ).val());
  Nu_Estado_Documento = $( '#cbo-estado_documento' ).val();
  iIdCliente = ($( '#txt-AID' ).val().length === 0 ? '-' : $( '#txt-AID' ).val());
  sNombreCliente = ($('#txt-Filtro_Entidad').val().length === 0 ? '-' : $('#txt-Filtro_Entidad').val());
  iIdPersonal = ($('#txt-AID_Personal').val().length === 0 ? '-' : $('#txt-AID_Personal').val());
  sNombrePersonal = ($('#txt-Filtro_Personal').val().length === 0 ? '-' : $('#txt-Filtro_Personal').val());
  iTipoVenta = $( '#cbo-tipo_venta' ).val();
  iMedioPago = $( '#cbo-forma_pago' ).val();
  iTipoTarjeta = $('#cbo-tipo_tarjeta').val();
  ID_Almacen = $('#cbo-Almacenes_ReporteFormaPago').val();
  Nu_Tipo_Impuesto = $('#cbo-regalo').val();

  $( '#btn-html_ventas_detalladas_generales' ).text('');
  $( '#btn-html_ventas_detalladas_generales' ).attr('disabled', true);
  $( '#btn-html_ventas_detalladas_generales' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

  $( '#table-ventas_detalladas_generales > tbody' ).empty();
  $( '#table-ventas_detalladas_generales > tfoot' ).empty();

  var arrPost = {
    sMethod : sMethod,
    Fe_Inicio : Fe_Inicio,
    Fe_Fin : Fe_Fin,
    ID_Tipo_Documento : ID_Tipo_Documento,
    ID_Serie_Documento : ID_Serie_Documento,
    ID_Numero_Documento : ID_Numero_Documento,
    Nu_Estado_Documento : Nu_Estado_Documento,
    iIdCliente : iIdCliente,
    sNombreCliente: sNombreCliente,
    iIdPersonal: iIdPersonal,
    sNombrePersonal: sNombrePersonal,
    iTipoVenta : iTipoVenta,
    iMedioPago: iMedioPago,
    iTipoTarjeta: iTipoTarjeta,
    ID_Almacen: ID_Almacen,
    Nu_Tipo_Impuesto:Nu_Tipo_Impuesto
  };      
  url = base_url + 'Ventas/informes_venta/ReporteFormaPagoController/sendReporte';
  $.post( url, arrPost, function( response ){
    if ( response.sStatus == 'success' ) {
      var iTotalRegistros = response.arrData.length, response=response.arrData, tr_body = '', tr_foot = '';
      var fTotalItem = 0.00, fTotalGeneral = 0.00;
      var $ID_Almacen = 0, $fTotalGeneralAlmacen = 0.00, $counter_almacen = 0;
      
      // Declare a new array
      let newArray = [];
  
      // Declare an empty object
      let uniqueObject = {};

      for (var i = 0; i < iTotalRegistros; i++) {
        fTotalItem = (!isNaN(parseFloat(response[i].Ss_Total)) ? parseFloat(response[i].Ss_Total) : 0);

        if ($ID_Almacen != response[i].ID_Almacen) {
          if ($counter_almacen != 0) {
            tr_body +=
              +"<tr>"
              + "<th class='text-right' colspan='15'>Total Almacén</th>"
              + "<th class='text-right'>" + number_format($fTotalGeneralAlmacen, 2) + "</th>"
              + "</tr>";
          }

          $fTotalGeneralAlmacen = 0.00;

          tr_body +=
            "<tr>"
            + "<th class='text-right'><span style='font-size: 15px;'>Almacén</span></th>"
            + "<th class='text-left' colspan='15'><span style='font-size: 15px;'>" + response[i].No_Almacen + "</span></th>"
            + "</tr>";

          $ID_Almacen = response[i].ID_Almacen;
        }// if almacen

        tr_body +=
        "<tr>"
          +"<td class='text-center'>" + response[i].Fe_Emision_Hora + "</td>"
          +"<td class='text-center'>" + response[i].Fe_Emision_Hora_Pago + "</td>"
          +"<td class='text-left'>" + response[i].No_Empleado + "</td>"
          +"<td class='text-center'>" + response[i].No_Tipo_Documento_Breve + "</td>"
          +"<td class='text-center'>" + response[i].ID_Serie_Documento + "</td>"
          +"<td class='text-center'>" + response[i].ID_Numero_Documento + "</td>"
          +"<td class='text-center'>" + response[i].No_Tipo_Documento_Identidad_Breve + "</td>"
          +"<td class='text-center'>" + response[i].Nu_Documento_Identidad + "</td>"
          +"<td class='text-left'>" + response[i].No_Entidad + "</td>"
          +"<td class='text-center'>" + response[i].No_Signo + "</td>"
          +"<td class='text-right'>" + number_format(response[i].Ss_Tipo_Cambio, 3) + "</td>"
          +"<td class='text-center'>" + response[i].No_Medio_Pago + "</td>"
          +"<td class='text-center'>" + response[i].No_Tipo_Medio_Pago + "</td>"
          +"<td class='text-center'>" + response[i].Nu_Tarjeta + "</td>"
          +"<td class='text-center'>" + response[i].Nu_Transaccion + "</td>"
          +"<td class='text-right'>" + (response[i].ID_Tipo_Documento != 5 ? '' : '-') + number_format(fTotalItem, 2) + "</td>"
          +"<td class='text-center'><span class='label label-" + response[i].No_Class_Estado + "'>" + response[i].No_Estado + "</span></td>"
          +"<td class='text-center'>" + ((response[i].Nu_Estado == 6 || response[i].Nu_Estado == 8 || response[i].Nu_Estado == 9) ? response[i].btn_editar : '') + "</td>"
          +"<td class='text-center'>" + ((response[i].Nu_Estado == 6 || response[i].Nu_Estado == 8 || response[i].Nu_Estado == 9) ? response[i].btn_eliminar : '') + "</td>"
        +"</tr>";
        
        /*
        //for para buscar los medios que usaron
        for (var x = 0; x < Object.keys(arrMedioPagoBD).length; x++) {
          if(arrMedioPagoBD[x].ID_Medio_Pago == response[i].ID_Medio_Pago){
            //console.log(arrMedioPagoTotales[x].No_Medio_Pago);
            //creo array nuevo de solo los medios que se encontraron
            // Extract the title
            objTitle = arrMedioPagoBD[x].ID_Medio_Pago;

            // Use the title as the index
            uniqueObject[objTitle] = arrMedioPagoBD[x];
          }
        }
        */

        fTotalGeneral += fTotalItem;
        $fTotalGeneralAlmacen += fTotalItem;
        $counter_almacen++;
      }

      /*
      //Loop to push unique object into array
      //eliminamos duplicado
      for (i in uniqueObject) {
        newArray.push(uniqueObject[i]);
      }
      
      // Display the unique objects
      console.log(newArray);
      */

      // array is sorted by band in descending order
      response.sort(compareValues('No_Medio_Pago', 'desc'));
      //console.log(response);

      tr_foot =
      "<tfoot>"
        + "<tr>"
          + "<th class='text-right' colspan='15'>Total Almacén</th>"
          + "<th class='text-right'>" + number_format($fTotalGeneralAlmacen, 2) + "</th>"
        + "</tr>"
        +"<tr>"
          +"<th class='text-right' colspan='15'>Total General</th>"
          +"<th class='text-right'>" + number_format(fTotalGeneral, 2) + "</th>"
          +"<th class='text-right'></th>"
        +"</tr>"
        +"<tr>"
          +"<th class='text-right' colspan='16'>TOTAL X MEDIO PAGO</th>"
        +"</tr>";

      var iIdMedioPagoCompare = 0, sMedioPago, $fTotalXMedioPago=0, $fTotalItem=0, $counter=0;
      for (let index = 0; index < response.length; index++) {
        const element = response[index];
        if(iIdMedioPagoCompare != element.ID_Medio_Pago){
          if ($counter != 0) {
            tr_foot += "<tr>";
              tr_foot += "<th class='text-right' colspan='15'>" + response[index-1].No_Medio_Pago + "</th>";
              tr_foot += "<th class='text-right'>" + number_format($fTotalXMedioPago, 2) + "</th>";
              tr_foot += "<th class='text-right'></th>";
            tr_foot += "</tr>";
            sMedioPago = response[index].No_Medio_Pago;
            $fTotalXMedioPago=0;
          }
          iIdMedioPagoCompare=element.ID_Medio_Pago;
        }
        $fTotalItem = (!isNaN(parseFloat(element.Ss_Total)) ? parseFloat(element.Ss_Total) : 0);
        $fTotalXMedioPago+=$fTotalItem;
        $counter++;
      }
      tr_foot += "<tr>";
        tr_foot += "<th class='text-right' colspan='15'>" + sMedioPago + "</th>";
        tr_foot += "<th class='text-right'>" + number_format($fTotalXMedioPago, 2) + "</th>";
        tr_foot += "<th class='text-right'></th>";
      tr_foot += "</tr>";
      
      tr_foot += "</tfoot>";
    } else {
      if( response.sMessageSQL !== undefined ) {
        console.log(response.sMessageSQL);
      }
      tr_body +=
      "<tr>"
        +"<td colspan='18' class='text-center'>" + response.sMessage + "</td>"
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
}

function eliminarFormaPago(ID){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $( '#btn-save-delete' ).off('click').click(function () {
    _eliminarFormaPago($modal_delete, ID);
  });
}
function _eliminarFormaPago($modal_delete, ID){
  $( '#modal-loader' ).modal('show');
    
  url = base_url + 'Ventas/informes_venta/ReporteFormaPagoController/eliminarFormaPago/' + ID;
  $.ajax({
    url       : url,
    type      : "GET",
    dataType  : "JSON",
    success: function( response ){
      $( '#modal-loader' ).modal('hide');
      
      $modal_delete.modal('hide');
	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
  	  $( '#modal-message' ).modal('show');
		  
		  if (response.status == 'success'){
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
        getReporteHTML();
		  } else {
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
		  }
		  accion_cliente = '';
    },
    error: function (jqXHR, textStatus, errorThrown) {
		  accion_cliente = '';
      $( '#modal-loader' ).modal('hide');
      $modal_delete.modal('hide');
      $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
      
  	  $( '#modal-message' ).modal('show');
      $( '.modal-message' ).addClass( 'modal-danger' );
      $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
      setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
      
      //Message for developer
      console.log(jqXHR.responseText);
    },
  });
}

function EditarFormaPago(arrParams){
  arrParams = JSON.parse(arrParams);

  $( '#form-medio_pago' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();

	$('#txt-Fe_Vencimiento').val(fDay + '/' + fMonth + '/' + fYear);
	var Fe_Emision = $('#txt-Fe_Vencimiento').val().split('/');
	$('#txt-Fe_Vencimiento').datepicker({
		autoclose: true,
		startDate: new Date(Fe_Emision[2], Fe_Emision[1] - 1, Fe_Emision[0]),
		todayHighlight: true
	})

  $('#txt-Fe_Vencimiento').val(fDay + '/' + fMonth + '/' + fYear);
  if(arrParams.Nu_Tipo_Operacion_Actual==1) {//1=crédito
	  $('#txt-Fe_Vencimiento').val(ParseDateString(arrParams.Fe_Vencimiento, 6, '-'));
  }

  $( '.modal-medio_pago' ).modal('show');

  $( '[name="iIdDocumentoCabecera"]' ).val( arrParams.ID_Documento_Cabecera );
  $( '[name="iIdDocumentoMedioPago"]' ).val( arrParams.ID_Documento_Medio_Pago );
  $( '[name="sTotalDocumento"]' ).val( arrParams.Ss_Total );
  $( '[name="sTotalSaldo"]' ).val( arrParams.Ss_Total_Saldo );

  $( '[name="fTotalMedioPago"]' ).val( arrParams.Ss_Total );

  $( '#modal-header-medio_pago-title' ).text(arrParams.ID_Documento);

  $( '.div-credito').hide();

  $( '[name="iTipoMedioPagoOperacionActual"]' ).val( arrParams.Nu_Tipo_Operacion_Actual );
  $( '[name="iTipoMedioPagoOperacion"]' ).val( arrParams.Nu_Tipo_Operacion_Actual );//luego este valor se setea según el cliente coloca el valor
  $( '[name="iIdMedioPagoActual"]' ).val( arrParams.ID_Medio_Pago );
  $( '[name="iIdTipoMedioPagoActual"]' ).val( arrParams.ID_Tipo_Medio_Pago );

  url = base_url + 'HelperController/getMediosPago';
  var arrPost = {
    iIdEmpresa : arrParams.ID_Empresa,
  };
  $.post( url, arrPost, function( response ){
    $( '#cbo-modal_forma_pago' ).html('');
    for (var i = 0; i < response.length; i++) {
      selected = '';
      if(arrParams.ID_Medio_Pago == response[i]['ID_Medio_Pago']) {
        selected = 'selected="selected"';
        if(response[i].Nu_Tipo==1)//1=credito
          $( '.div-credito').show();
      }
      $( '#cbo-modal_forma_pago' ).append( '<option value="' + response[i].ID_Medio_Pago + '" data-nu_tipo_medio_pago="' + response[i].Nu_Tipo + '" ' + selected + '>' + response[i].No_Medio_Pago + '</option>' );
    }
  }, 'JSON');
  
  $( '.div-modal_datos_tarjeta_credito').hide();
  $( '#cbo-medio_pago-modal_tarjeta_credito' ).html('<option value="0">Nada</option>');
  $( '[name="iNumeroTransaccion"]' ).val( '' );
  $( '[name="iNumeroTarjeta"]' ).val( '' );
  //mostrar tipo de medio de pago si tiene data ID_Tipo_Medio_Pago
  if (arrParams.ID_Tipo_Medio_Pago != '' && arrParams.ID_Tipo_Medio_Pago!='0' && arrParams.ID_Tipo_Medio_Pago!=null) {
    $( '.div-modal_datos_tarjeta_credito').show();
    url = base_url + 'HelperController/getTiposTarjetaCredito';
    $.post( url, {ID_Medio_Pago : arrParams.ID_Medio_Pago} , function( response ){
      $( '#cbo-medio_pago-modal_tarjeta_credito' ).html('');
      for (var i = 0; i < response.length; i++) {
        selected = '';
        if(arrParams.ID_Tipo_Medio_Pago == response[i]['ID_Tipo_Medio_Pago'])
          selected = 'selected="selected"';
        $( '#cbo-medio_pago-modal_tarjeta_credito' ).append( '<option value="' + response[i].ID_Tipo_Medio_Pago + '" ' + selected + '>' + response[i].No_Tipo_Medio_Pago + '</option>' );
      }
    }, 'JSON');

    $( '[name="iNumeroTransaccion"]' ).val( arrParams.Nu_Tarjeta );
    $( '[name="iNumeroTarjeta"]' ).val( arrParams.Nu_Transaccion );
  }

  $( '#cbo-modal_forma_pago' ).change(function(){
    $( '.div-credito').hide();

    ID_Medio_Pago = $(this).val();
    Nu_Tipo_Medio_Pago = $(this).find(':selected').data('nu_tipo_medio_pago');
    
    $( '[name="iTipoMedioPagoOperacion"]' ).val( Nu_Tipo_Medio_Pago );

    $( '.div-modal_datos_tarjeta_credito').hide();
    $( '#cbo-medio_pago-modal_tarjeta_credito' ).html('<option value="0">Nada</option>');
    $( '#tel-nu_referencia' ).val('');
    $( '#tel-nu_ultimo_4_digitos_tarjeta' ).val('');
    if (Nu_Tipo_Medio_Pago==2){
      $( '.div-modal_datos_tarjeta_credito').show();

      url = base_url + 'HelperController/getTiposTarjetaCredito';
      $.post( url, {ID_Medio_Pago : ID_Medio_Pago} , function( response ){
        $( '#cbo-medio_pago-modal_tarjeta_credito' ).html('');
        for (var i = 0; i < response.length; i++)
          $( '#cbo-medio_pago-modal_tarjeta_credito' ).append( '<option value="' + response[i].ID_Tipo_Medio_Pago + '">' + response[i].No_Tipo_Medio_Pago + '</option>' );
      }, 'JSON');
    }

    if(Nu_Tipo_Medio_Pago==1)
      $( '.div-credito').show();
  })

  //si cualquier medio de pago a se cambia a crédito deberé actualizar la tabla documento_cabecera y agregarle importe de saldo
  //si es crédito y pasa a otro medio de pago quitar saldo OJO pero deberé revisar si no tiene pagos a cuenta
}

function compareValues(key, order = 'asc') {
  return function innerSort(a, b) {
    if (!a.hasOwnProperty(key) || !b.hasOwnProperty(key)) {
      // property doesn't exist on either object
      return 0;
    }

    const varA = (typeof a[key] === 'string')
      ? a[key].toUpperCase() : a[key];
    const varB = (typeof b[key] === 'string')
      ? b[key].toUpperCase() : b[key];

    let comparison = 0;
    if (varA > varB) {
      comparison = 1;
    } else if (varA < varB) {
      comparison = -1;
    }
    return (
      (order === 'desc') ? (comparison * -1) : comparison
    );
  };
}