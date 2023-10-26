var iTipoConsultaFecha = '0', iTipoRecepcionClienteEstado = '-', url;

$(function () {
  $('.select2').select2();
  $( '.div-fecha_historica' ).hide();
  $( '#div-venta_punto_venta' ).hide();

  $('.div-datos_guia_electronica').hide();
  $('[name="radio-TipoDocumento"]').on('ifChecked', function () {
    $('.div-datos_guia_electronica').hide();
    if ($('[name="radio-TipoDocumento"]:checked').attr('value') == 8)
      $('.div-datos_guia_electronica').show();
  });

  $('#div-addFlete').hide();
  $('#radio-flete_si').on('ifChecked', function () {
    $('#div-addFlete').show();
  })

  $('#radio-flete_no').on('ifChecked', function () {
    $('#div-addFlete').hide();
  })

  $( '#cbo-tipo_consulta_fecha' ).change(function(){
    $( '.div-fecha_historica' ).hide();
    if (  $(this).val() > 0 )
      $( '.div-fecha_historica' ).show();
  })

  $('.div-delivery').show();
  $('.div-recepcion').show();
  $('#cbo-filtro_tipo_recepcion').change(function () {
    $('.div-delivery').show();
    $('.div-recepcion').show();
    if ($(this).val() == 6)
      $('.div-recepcion').hide();
    if ($(this).val() == 7)
      $('.div-delivery').hide();
  })

	$( '#cbo-filtros_series_documento' ).html('<option value="0" selected="selected">Todos</option>');
	$( '#cbo-filtros_tipos_documento' ).change(function(){
	  $( '#cbo-filtros_series_documento' ).html('<option value="0" selected="selected">Todos</option>');
	  if ( $(this).val() > 0) {
		  url = base_url + 'HelperController/getSeriesDocumentoPuntoVenta';
      $.post( url, { ID_Tipo_Documento: $(this).val() }, function( response ){
        for (var i = 0; i < response.length; i++)
          $( '#cbo-filtros_series_documento' ).append( '<option value="' + response[i].ID_Serie_Documento + '">' + response[i].ID_Serie_Documento + '</option>' );
      }, 'JSON');
	  }
  })

  if ($('#header-a-id_matricula_empleado').length > 0) {
    var Fe_Inicio, Fe_Fin, ID_Tipo_Documento, ID_Serie_Documento, ID_Numero_Documento, Nu_Estado_Documento, iIdCliente, sNombreCliente, iEstadoPago, iTipoRecepcionCliente;

    iTipoConsultaFecha = 'actual';
    Fe_Inicio = ParseDateString($('#txt-Filtro_Fe_Inicio').val(), 1, '/');
    Fe_Fin = ParseDateString($('#txt-Filtro_Fe_Fin').val(), 1, '/');
    ID_Tipo_Documento = $('#cbo-filtros_tipos_documento').val();
    ID_Serie_Documento = $('#cbo-filtros_series_documento').val();
    ID_Numero_Documento = ($('#txt-Filtro_NumeroDocumento').val().length == 0 ? '-' : $('#txt-Filtro_NumeroDocumento').val());
    Nu_Estado_Documento = $('#cbo-estado_documento').val();
    iIdCliente = ($('#txt-AID').val().length === 0 ? '-' : $('#txt-AID').val());
    sNombreCliente = ($('#txt-Filtro_Entidad').val().length === 0 ? '-' : $('#txt-Filtro_Entidad').val());
    iTipoRecepcionClienteEstado = '-';
    iTipoRecepcionCliente = $('#cbo-filtro_tipo_recepcion').val();
    iEstadoPago = $('#cbo-estado_pago').val();

    getReporteHTML();
  } else {
    alert('Primero se debe de aperturar caja');
  }

  $( '.btn-generar_venta_punto_venta' ).click(function(){
    //alert('actual ' + $(this).data('tipo_recepcion'));
    //alert('reemplazo ' + $(this).data('tipo_recepcion_actual'));
    $(this).data('tipo_recepcion', $(this).data('tipo_recepcion_actual'));
    //alert('nuevo ' + $(this).data('tipo_recepcion'));

    $('.btn-generar_venta_punto_venta_recepcion').data('fecha', $(this).data('fecha'));
    //alert('fecha ' + $(this).data('fecha'));

    $('.btn-generar_venta_punto_venta').removeClass('btn-success');
    $('.btn-generar_venta_punto_venta').addClass('btn-default');

    $('#btn-html_venta_punto_venta_' + $(this).data('fecha')).addClass('btn-success');
    
    if ($(this).data('fecha') != 'actual' || ($(this).data('fecha') == 'actual' && $('#header-a-id_matricula_empleado').length > 0)) {
      $('.help-block').empty();
      $('.form-group').removeClass('has-error');
    
      var Fe_Inicio, Fe_Fin, ID_Tipo_Documento, ID_Serie_Documento, ID_Numero_Documento, Nu_Estado_Documento, iIdCliente, sNombreCliente, iEstadoPago, iTipoRecepcionCliente;

      iTipoConsultaFecha = $(this).data('fecha');
      Fe_Inicio = ParseDateString($('#txt-Filtro_Fe_Inicio').val(), 1, '/');
      Fe_Fin = ParseDateString($('#txt-Filtro_Fe_Fin').val(), 1, '/');
      ID_Tipo_Documento = $('#cbo-filtros_tipos_documento').val();
      ID_Serie_Documento = $('#cbo-filtros_series_documento').val();
      ID_Numero_Documento = ($('#txt-Filtro_NumeroDocumento').val().length == 0 ? '-' : $('#txt-Filtro_NumeroDocumento').val());
      Nu_Estado_Documento = $('#cbo-estado_documento').val();
      iIdCliente = ($('#txt-AID').val().length === 0 ? '-' : $('#txt-AID').val());
      sNombreCliente = ($('#txt-Filtro_Entidad').val().length === 0 ? '-' : $('#txt-Filtro_Entidad').val());
      iTipoRecepcionClienteEstado = $(this).data('tipo_recepcion');
      iTipoRecepcionCliente = $('#cbo-filtro_tipo_recepcion').val();
      iEstadoPago = $('#cbo-estado_pago').val();
        
      if ($(this).data('type') == 'html') {
        getReporteHTML();
      } else if ($(this).data('type') == 'pdf') {
        $( '#btn-pdf_venta_punto_venta' ).text('');
        $( '#btn-pdf_venta_punto_venta' ).attr('disabled', true);
        $( '#btn-pdf_venta_punto_venta' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
        
        url = base_url + 'PuntoVenta/DespachoPOSController/sendReportePDF/' + iTipoConsultaFecha + '/' + Fe_Inicio + '/' + Fe_Fin + '/' + ID_Tipo_Documento + '/' + ID_Serie_Documento + '/' + ID_Numero_Documento + '/' + Nu_Estado_Documento + '/' + iIdCliente + '/' + sNombreCliente + '/' + iTipoRecepcionCliente + '/' + iEstadoPago;
        window.open(url,'_blank');
        
        $( '#btn-pdf_venta_punto_venta' ).text('');
        $( '#btn-pdf_venta_punto_venta' ).append( '<i class="fa fa-file-pdf-o color_icon_pdf"></i> PDF' );
        $( '#btn-pdf_venta_punto_venta' ).attr('disabled', false);
      } else if ($(this).data('type') == 'excel') {
        $( '#btn-excel_venta_punto_venta' ).text('');
        $( '#btn-excel_venta_punto_venta' ).attr('disabled', true);
        $( '#btn-excel_venta_punto_venta' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
        
        url = base_url + 'PuntoVenta/DespachoPOSController/sendReporteEXCEL/' + iTipoConsultaFecha + '/' + Fe_Inicio + '/' + Fe_Fin + '/' + ID_Tipo_Documento + '/' + ID_Serie_Documento + '/' + ID_Numero_Documento + '/' + Nu_Estado_Documento + '/' + iIdCliente + '/' + sNombreCliente + '/' + iTipoRecepcionCliente + '/' + iEstadoPago;
        window.open(url,'_blank');
        
        $( '#btn-excel_venta_punto_venta' ).text('');
        $( '#btn-excel_venta_punto_venta' ).append( '<i class="fa fa-file-excel-o color_icon_excel"></i> Excel' );
        $( '#btn-excel_venta_punto_venta' ).attr('disabled', false);
      }
    } else {
      alert('Primero se debe de aperturar caja');
    }// ./ if - else
    
  })//./ btn
    
  // Generar Guía
  $('#btn-generar_guia').click(function () {
    $('.help-block').empty();
    $('.form-group').removeClass('has-error');
    if ($('#cbo-transporte').val() == 0) {
      $('#cbo-transporte').closest('.form-group').find('.help-block').html('Seleccionar transporte');
      $('#cbo-transporte').closest('.form-group').removeClass('has-success').addClass('has-error');

      scrollToError($('.modal-generar_guia .modal-body'), $('#cbo-transporte'));
    } else if ($('[name="radio-TipoDocumento"]:checked').attr('value') == 8 && $('#txt-Txt_Direccion_Entidad-modal').val().length == 0) {
      $('#txt-Txt_Direccion_Entidad-modal').closest('.form-group').find('.help-block').html('Ingresar dirección');
      $('#txt-Txt_Direccion_Entidad-modal').closest('.form-group').removeClass('has-success').addClass('has-error');

      scrollToError($('.modal-generar_guia .modal-body'), $('#txt-No_Placa-modal'));
    } else if ($('[name="radio-TipoDocumento"]:checked').attr('value') == 8 && $('#cbo-ubigeo_inei-modal').val() == 0) {
      $('#cbo-ubigeo_inei-modal').closest('.form-group').find('.help-block').html('Seleccionar ubigeo');
      $('#cbo-ubigeo_inei-modal').closest('.form-group').removeClass('has-success').addClass('has-error');

      scrollToError($('.modal-generar_guia .modal-body'), $('#cbo-ubigeo_inei-modal'));
    } else if ($('[name="radio-TipoDocumento"]:checked').attr('value') == 8 && (parseFloat($('#txt-Ss_Peso_Bruto').val()) < 1 || isNaN(parseFloat($('#txt-Ss_Peso_Bruto').val())))) {
      $('#txt-Ss_Peso_Bruto').closest('.form-group').find('.help-block').html('Peso bruto debe ser 1 o mayor');
      $('#txt-Ss_Peso_Bruto').closest('.form-group').removeClass('has-success').addClass('has-error');

      scrollToError($('.modal-generar_guia .modal-body'), $('#txt-Ss_Peso_Bruto'));
    } else if ($('[name="radio-TipoDocumento"]:checked').attr('value') == 8 && $('[name="radio-TipoTransporte"]:checked').attr('value') == '01' && $( '#cbo-transporte' ).find(':selected').data('id_tipo_documento_identidad') != '4') {//01=publico y id tipo de documento = 4
      alert('TRANSPORTE PUBLICO: Transportista debe ser tipo RUC');
    } else if ($('[name="radio-TipoDocumento"]:checked').attr('value') == 8 && $('[name="radio-TipoTransporte"]:checked').attr('value') == '01' && $('#hidden-Nu_Documento_Identidad-empresa').val() == $( '#cbo-transporte' ).find(':selected').data('numero_documento_identidad')) {//01=publico
      alert('TRANSPORTE PUBLICO: El RUC de Transportista no puede ser igual al RUC de la Empresa');
    } else if ($('[name="radio-TipoDocumento"]:checked').attr('value') == 8 && $('[name="radio-TipoTransporte"]:checked').attr('value') == '02' && $('#txt-No_Placa').val().length < 6) {
      $('#txt-No_Placa').closest('.form-group').find('.help-block').html('Ingresar Placa mínimo 6 dígitos');
      $('#txt-No_Placa').closest('.form-group').removeClass('has-success').addClass('has-error');

      scrollToError($('.modal-generar_guia .modal-body'), $('#txt-No_Placa'));
    } else if ($('[name="radio-TipoDocumento"]:checked').attr('value') == 8 && $('[name="radio-TipoTransporte"]:checked').attr('value') == '02' && $('#txt-No_Licencia').val().length < 9) {
      $('#txt-No_Licencia').closest('.form-group').find('.help-block').html('Ingresar Licencia mínimo 9 dígitos');
      $('#txt-No_Licencia').closest('.form-group').removeClass('has-success').addClass('has-error');

      scrollToError($('.modal-generar_guia .modal-body'), $('#txt-No_Licencia'));
    } else {
      $('.help-block').empty();
      $('.form-group').removeClass('has-error');

      $('#btn-generar_guia').text('');
      $('#btn-generar_guia').attr('disabled', true);
      $('#btn-generar_guia').append('Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>');
      $('#btn-salir').attr('disabled', true);

      url = base_url + 'PuntoVenta/DespachoPOSController/generarGuia';
      $.ajax({
        type: 'POST',
        dataType: 'JSON',
        url: url,
        data: $('#form-generar_guia').serialize(),
        success: function (response) {
          $('.modal-message').removeClass('modal-danger modal-warning modal-success');
          $('#modal-message').modal('show');

          if (response.sStatus == 'success') {
            $('.modal-generar_guia').modal('hide');

            $('.modal-message').addClass('modal-' + response.sStatus);
            $('.modal-title-message').text(response.sMessage);
            setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);

            getReporteHTML();

            // Mandar a imprimir impresora
            var Accion = 'imprimir', iIdDocumentoCabecera = response.iIdDocumentoCabecera, url_print = 'ocultar-img-logo_punto_venta_click';
            formatoImpresionTicketGuia(Accion, iIdDocumentoCabecera, url_print);
          } else {
            $('.modal-message').addClass('modal-' + response.sStatus);
            $('.modal-title-message').text(response.sMessage);
            if (response.sStatus == 'danger') {
              $('.modal-generar_guia').modal('hide');
              setTimeout(function () { $('#modal-message').modal('hide'); }, 3100);
            }
          }

          $('#btn-generar_guia').text('');
          $('#btn-generar_guia').append('Generar Guía');
          $('#btn-generar_guia').attr('disabled', false);
          $('#btn-salir').attr('disabled', false);
        }
      })
      .fail(function (jqXHR, textStatus, errorThrown) {
        $('.modal-message').removeClass('modal-danger modal-warning modal-success');

        $('#modal-message').modal('show');
        $('.modal-message').addClass('modal-danger');
        $('.modal-title-message').text(textStatus + ' [' + jqXHR.status + ']: ' + errorThrown);
        setTimeout(function () { $('#modal-message').modal('hide'); }, 4100);

        //Message for developer
        console.log(jqXHR.responseText);

        $('#btn-generar_guia').text('');
        $('#btn-generar_guia').attr('disabled', false);
        $('#btn-generar_guia').append('Generar Guía');
        $('#btn-salir').attr('disabled', false);
      })
    }// ./ if - else validacion
  })// ./ Generar Guía
})

function getReporteHTML(){
  var Fe_Inicio, Fe_Fin, ID_Tipo_Documento, ID_Serie_Documento, ID_Numero_Documento, Nu_Estado_Documento, iIdCliente, sNombreCliente, iEstadoPago, iTipoRecepcionCliente;
  
  Fe_Inicio           = ParseDateString($( '#txt-Filtro_Fe_Inicio' ).val(), 1, '/');
  Fe_Fin              = ParseDateString($( '#txt-Filtro_Fe_Fin' ).val(), 1, '/');
  ID_Tipo_Documento   = $( '#cbo-filtros_tipos_documento' ).val();
  ID_Serie_Documento  = $( '#cbo-filtros_series_documento' ).val();
  ID_Numero_Documento = ($( '#txt-Filtro_NumeroDocumento' ).val().length == 0 ? '-' : $( '#txt-Filtro_NumeroDocumento' ).val());
  Nu_Estado_Documento = $( '#cbo-estado_documento' ).val();
  iIdCliente = ($( '#txt-AID' ).val().length === 0 ? '-' : $( '#txt-AID' ).val());
  sNombreCliente = ($( '#txt-Filtro_Entidad' ).val().length === 0 ? '-' : $( '#txt-Filtro_Entidad' ).val());
  iEstadoPago = $('#cbo-estado_pago').val();
  iTipoRecepcionCliente = $('#cbo-filtro_tipo_recepcion').val();
  
  $( '#btn-html_venta_punto_venta' ).text('');
  $( '#btn-html_venta_punto_venta' ).attr('disabled', true);
  $( '#btn-html_venta_punto_venta' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

  $( '#table-venta_punto_venta > tbody' ).empty();
  $( '#table-venta_punto_venta > tfoot' ).empty();
  
  var arrPost = {
    iTipoConsultaFecha  : iTipoConsultaFecha,
    Fe_Inicio           : Fe_Inicio,
    Fe_Fin              : Fe_Fin,
    ID_Tipo_Documento   : ID_Tipo_Documento,
    ID_Serie_Documento  : ID_Serie_Documento,
    ID_Numero_Documento : ID_Numero_Documento,
    Nu_Estado_Documento : Nu_Estado_Documento,
    iIdCliente : iIdCliente,
    sNombreCliente : sNombreCliente,
    iTipoRecepcionCliente: iTipoRecepcionCliente,
    iTipoRecepcionClienteEstado: iTipoRecepcionClienteEstado,
    iEstadoPago: iEstadoPago,
  };
  url = base_url + 'PuntoVenta/DespachoPOSController/sendReporte';
  $.post( url, arrPost, function( response ){
    if ( response.sStatus == 'success' ) {
      //Totales
      var responseTotal = response.arrDataTotal;
      $('#btn-html_venta_punto_venta_dp').text('Pendiente ' + responseTotal[0].Total_Delivery_Pendiente);
      $('#btn-html_venta_punto_venta_dpr').text('Preparando ' + responseTotal[0].Total_Delivery_Preparando);
      $('#btn-html_venta_punto_venta_de').text('Enviado ' + responseTotal[0].Total_Delivery_Enviado);
      $('#btn-html_venta_punto_venta_en').text('Entregado ' + responseTotal[0].Total_Delivery_Entregado);
      $('#btn-html_venta_punto_venta_dr').text('Rechazado ' + responseTotal[0].Total_Delivery_Rechazado);

      $('#btn-html_venta_punto_venta_rp').text('Pendiente ' + responseTotal[0].Total_Recojo_Pendiente);
      $('#btn-html_venta_punto_venta_rpr').text('Preparando ' + responseTotal[0].Total_Recojo_Preparando);
      $('#btn-html_venta_punto_venta_re').text('Enviado ' + responseTotal[0].Total_Recojo_Enviado);
      $('#btn-html_venta_punto_venta_ren').text('Entregado ' + responseTotal[0].Total_Recojo_Entregado);
      $('#btn-html_venta_punto_venta_rr').text('Rechazado ' + responseTotal[0].Total_Recojo_Rechazado);

      var iTotalRegistros = response.arrData.length, response=response.arrData, tr_body = '', arrParams = '';
      for (var i = 0; i < iTotalRegistros; i++) {
        arrParams = {
          'ID_Empresa': response[i].ID_Empresa,
          'ID_Organizacion': response[i].ID_Organizacion,
          'ID_Almacen': response[i].ID_Almacen,
          'ID_Moneda': response[i].ID_Moneda,
          'ID_Documento_Cabecera': response[i].ID_Documento_Cabecera,
          'ID_Tipo_Documento': response[i].ID_Tipo_Documento,
          'ID_Lista_Precio_Cabecera': response[i].ID_Lista_Precio_Cabecera,
          'ID_Transporte_Delivery': response[i].ID_Transporte_Delivery,
          'Fe_Emision': response[i].Fe_Emision,
          'Fe_Emision_Hora': response[i].Fe_Emision_Hora_Hidden,
          'Ss_Total': response[i].Ss_Total,
          'ID_Entidad': response[i].ID_Entidad,
          'No_Tipo_Documento_Breve': response[i].No_Tipo_Documento_Breve,
          'ID_Serie_Documento': response[i].ID_Serie_Documento,
          'ID_Numero_Documento': response[i].ID_Numero_Documento,
          'sCliente': response[i].No_Entidad
        }
        arrParams = JSON.stringify(arrParams);

        sButtonGenerarGuia = '';
        if (response[i].No_Tipo_Documento_Breve_Guia != null && response[i].No_Tipo_Documento_Breve_Guia != '')
          sButtonGenerarGuia = response[i].No_Tipo_Documento_Breve_Guia + '-' + response[i].ID_Serie_Documento_Guia + '-' + response[i].ID_Numero_Documento_Guia;
        else if (response[i].Nu_Tipo_Recepcion==6 && (response[i].Nu_Estado == 6 || response[i].Nu_Estado == 8))
          sButtonGenerarGuia = "<button type='button' class='btn btn-xs btn-link' alt='Generar Guía' title='Generar Guía' href='javascript:void(0)' onclick='generarGuia(" + arrParams + ")'>Guía</button>";

        if (response[i].Nu_Tipo_Recepcion!=6) {
          sButtonGenerarGuia += 'Solo puedes generar guía si la RECEPCIÓN es DELIVERY';
        }

        tr_body +=
        "<tr data-id_entidad=" + response[i].ID_Entidad + ">"
          +"<td class='text-center'>" + response[i].No_Tipo_Recepcion + "</td>"
          +"<td class='text-left'>" + response[i].No_Delivery + "</td>"
          //+"<td class='text-center'>" + response[i].Fe_Emision_Hora + "</td>"
          +"<td class='text-center'>" + response[i].Fe_Entrega + "</td>"
          +"<td class='text-center'>" + response[i].Dias_Transcurridos + "</td>"
          +"<td class='text-left'>" + response[i].Documento + "</td>"
          //+"<td class='text-center'>" + response[i].No_Tipo_Documento_Breve + "</td>"
          //+"<td class='text-center'>" + response[i].ID_Serie_Documento + "</td>"
          //+"<td class='text-center'>" + response[i].ID_Numero_Documento + "</td>"
          +"<td class='text-left'>" + response[i].No_Entidad + "</td>"
          +"<td class='text-left'>" + response[i].Nu_Celular_Entidad + "</td>"
          +"<td class='text-left'>" + response[i].Txt_Direccion_Entidad + "</td>"
          +"<td class='text-left'>" + response[i].No_Signo + "</td>"
          +"<td class='text-left'>" + response[i].Ss_Total + "</td>"
          +"<td class='text-left'>" + response[i].No_Estado_Delivery + "</td>"
          //+"<td class='text-center'><span class='label label-" + response[i].No_Class_Estado + "'>" + response[i].No_Estado + "</span></td>"
          +"<td class='text-center' title='Ver guias en Logistica > Guia / Salida de Inventario'>" + sButtonGenerarGuia + "</td>"
          +"<td class='text-center'>" + (response[i].Nu_Estado == 6 || response[i].Nu_Estado == 8 ? response[i].sAccionVer : '') + "</td>"
          +"<td class='text-center'>" + (response[i].Nu_Estado == 6 || response[i].Nu_Estado == 8 ? response[i].sAccionImprimir : '') + "</td>"
        +"</tr>";
      }
    } else {
      if( response.sMessageSQL !== undefined ) {
        console.log(response.sMessageSQL);
      }
      tr_body +=
      "<tr>"
        +"<td colspan='15' class='text-center'>" + response.sMessage + "</td>"
      + "</tr>";
    } // ./ if arrData
    
    $( '#div-venta_punto_venta' ).show();
    $( '#table-venta_punto_venta > tbody' ).append(tr_body);

    $( '#btn-html_venta_punto_venta' ).text('');
    $( '#btn-html_venta_punto_venta' ).append( '<i class="fa fa-search"></i> Buscar' );
    $( '#btn-html_venta_punto_venta' ).attr('disabled', false);
  }, 'JSON')
  .fail(function(jqXHR, textStatus, errorThrown) {
    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
    
    $( '#modal-message' ).modal('show');
    $( '.modal-message' ).addClass( 'modal-danger' );
    $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
    setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
    
    //Message for developer
    console.log(jqXHR.responseText);
    
    $( '#btn-html_venta_punto_venta' ).text('');
    $( '#btn-html_venta_punto_venta' ).append( '<i class="fa fa-search"></i> Buscar' );
    $( '#btn-html_venta_punto_venta' ).attr('disabled', false);
  });
}

function generarGuia(arrParams){
  $( '#form-generar_guia' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();

  $('#txt-Fe_Traslado').val(fDay + '/' + fMonth + '/' + fYear);
  var Fe_Emision = $('#txt-Fe_Traslado').val().split('/');
  $('#txt-Fe_Traslado').datepicker({
    autoclose: true,
    startDate: new Date(Fe_Emision[2], Fe_Emision[1] - 1, Fe_Emision[0]),
    todayHighlight: true
  })

  $('#txt-Fe_Traslado').val(fDay + '/' + fMonth + '/' + fYear);

  $( '.modal-generar_guia' ).modal('show');

  $('[name="Hidden_ID_Empresa"]' ).val(arrParams.ID_Empresa);
  $('[name="Hidden_ID_Organizacion"]').val(arrParams.ID_Organizacion);
  $('[name="Hidden_ID_Almacen"]').val(arrParams.ID_Almacen);
  $('[name="Hidden_ID_Moneda"]').val(arrParams.ID_Moneda);
  $('[name="Hidden_ID_Documento_Cabecera"]').val(arrParams.ID_Documento_Cabecera);
  $('[name="Hidden_ID_Entidad"]').val(arrParams.ID_Entidad);

  $('.div-tipoguia').show();
  if (arrParams.ID_Tipo_Documento == 2) {
    $('.div-tipoguia').hide();
  }

  $('#cbo-transporte').html('<option value="0" selected="selected">- Sin registro -</option>');
  url = base_url + 'HelperController/getDeliveryVentas';
  var arrPost = {};
  $.post(url, arrPost, function (response) {
    if (response.sStatus == 'success') {
      var l = response.arrData.length;
      if (l == 1) {
        $('#cbo-transporte').html('<option value="' + response.arrData[0].ID + '" data-id_tipo_documento_identidad="' + response.arrData[0].ID_Tipo_Documento_Identidad + '" data-numero_documento_identidad="' + response.arrData[0].Nu_Documento_Identidad + '">' + response.arrData[0].Nombre + '</option>');
      } else {
        $('#cbo-transporte').html('<option value="0" selected="selected">- Seleccionar -</option>');
        for (var x = 0; x < l; x++) {
          $('#cbo-transporte').append('<option value="' + response.arrData[x].ID + '" data-id_tipo_documento_identidad="' + response.arrData[x].ID_Tipo_Documento_Identidad + '" data-numero_documento_identidad="' + response.arrData[x].Nu_Documento_Identidad + '">' + response.arrData[x].Nombre + '</option>');
        }
      }
    } else {
      if (response.sMessageSQL !== undefined) {
        console.log(response.sMessageSQL);
      }
    }
  }, 'JSON');

  $('#generar_guia-modal-body-cliente').text('Sin datos');
  $('[name="Txt_Direccion_Entidad-modal"]').val('');
  url = base_url + 'HelperController/getEntidad';
  var arrPost = { ID_Entidad : arrParams.ID_Entidad};
  $.post(url, arrPost, function (response) {
    if (response.sStatus == 'success') {
      $('#generar_guia-modal-body-cliente').text('Cliente: ' + response.arrData[0].Nu_Documento_Identidad + ' ' + response.arrData[0].No_Entidad);
      $('[name="Txt_Direccion_Entidad-modal"]').val(response.arrData[0].Txt_Direccion_Entidad);
    } else {
      if (response.sMessageSQL !== undefined) {
        console.log(response.sMessageSQL);
      }
    }
  }, 'JSON');

  $('[name="Hidden_ID_Lista_Precio_Cabecera"]').val(arrParams.ID_Lista_Precio_Cabecera);
  $('[name="Hidden_Fe_Emision"]').val(arrParams.Fe_Emision);
  $('[name="Hidden_Fe_Emision_Hora"]').val(arrParams.Fe_Emision_Hora);
  $('[name="Hidden_Ss_Total"]').val(arrParams.Ss_Total);
  var sTipoDocumento = 'Boleta';
  if (arrParams.ID_Tipo_Documento == 3)
    sTipoDocumento ='Factura';
  else if (arrParams.ID_Tipo_Documento == 2)
    sTipoDocumento ='Nota de Venta';

  //$('#modal-header-generar_guia-title').text(arrParams.No_Tipo_Documento_Breve + ' - ' + arrParams.ID_Serie_Documento + ' - ' + arrParams.ID_Numero_Documento);
  $('#modal-header-generar_guia-title').text(sTipoDocumento + ' - ' + arrParams.ID_Serie_Documento + ' - ' + arrParams.ID_Numero_Documento);
  //$('#generar_guia-modal-body-cliente').text('');

  $('#radio-guia_i').prop('checked', true).iCheck('update');
  $('#radio-guia_f').prop('checked', false).iCheck('update');
  $('#radio-guia_e').prop('checked', false).iCheck('update');

  $('#div-addFlete').show();
  $('#radio-flete_si').prop('checked', true).iCheck('update');
  $('#radio-flete_no').prop('checked', false).iCheck('update');

  $('#radio-tipo_transporte_publico').prop('checked', true).iCheck('update');
  $('#radio-tipo_transporte_privado').prop('checked', false).iCheck('update');
  
  $( '#radio-tipo_transporte_publico' ).on('ifChecked', function () {
    $( '#txt-No_Placa' ).attr('placeholder', 'Opcional');
    $( '#txt-No_Licencia' ).attr('placeholder', 'Opcional');
  })

  $( '#radio-tipo_transporte_privado' ).on('ifChecked', function () {
    $( '#txt-No_Placa' ).attr('placeholder', 'Opcional');
    $( '#txt-No_Licencia' ).attr('placeholder', 'Opcional');
    if($('[name="radio-TipoDocumento"]:checked').attr('value') == 8) {//8=guia electronica
      $( '#txt-No_Placa' ).attr('placeholder', 'Obligatorio');
      $( '#txt-No_Licencia' ).attr('placeholder', 'Obligatorio');
    }
  })
  
  $( '#radio-guia_i' ).on('ifChecked', function () {
    $( '#txt-No_Placa' ).attr('placeholder', 'Opcional');
    $( '#txt-No_Licencia' ).attr('placeholder', 'Opcional');
    $('.div-electronico').hide();
  })
  
  $( '#radio-guia_f' ).on('ifChecked', function () {
    $( '#txt-No_Placa' ).attr('placeholder', 'Opcional');
    $( '#txt-No_Licencia' ).attr('placeholder', 'Opcional');
    $('.div-electronico').hide();
  })
  
  $( '#radio-guia_e' ).on('ifChecked', function () {
    $( '#txt-No_Placa' ).attr('placeholder', 'Opcional');
    $( '#txt-No_Licencia' ).attr('placeholder', 'Opcional');
    if($('[name="radio-TipoTransporte"]:checked').attr('value') == '02') {//02=Privado
      $( '#txt-No_Placa' ).attr('placeholder', 'Obligatorio');
      $( '#txt-No_Licencia' ).attr('placeholder', 'Obligatorio');
    }
    $('.div-electronico').show();
  })

  url = base_url + 'HelperController/getValoresTablaDato';
  var arrParams = {
    sTipoData : 'Ubigeo_INEI',
  }
  $.post( url, arrParams, function( response ){
    if ( response.sStatus == 'success' ) {
      var iTotalRegistros = response.arrData.length, response=response.arrData;
      $( '#cbo-ubigeo_inei-modal' ).html( '<option value="" selected="selected">- Seleccionar -</option>');
      for (var i = 0; i < iTotalRegistros; i++) {
        selected = '';
        if(response[i].ID_Tabla_Dato == 1444)
          selected = 'selected="selected"';
        $( '#cbo-ubigeo_inei-modal' ).append( '<option value="' + response[i].ID_Tabla_Dato + '" ' + selected + '>' + response[i].Nu_Valor + ': ' + response[i].No_Descripcion + '</option>' );
      }
    } else {
      $( '#cbo-ubigeo_inei-modal' ).html( '<option value="" selected="selected">- Vacío -</option>');
      console.log( response );
    }
  }, 'JSON');

  $('.div-electronico').hide();
}

function estadoPedido(ID, Nu_Estado) {
  var $modal_delete = $('#modal-message-delete');
  $modal_delete.modal('show');

  $('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
  $('.modal-message-delete').addClass('modal-success');

  $('.modal-title-message-delete').text('¿Deseas cambiar el estado?');

  $('#btn-cancel-delete').off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $('#btn-save-delete').off('click').click(function () {
    $('#modal-loader').modal('show');

    url = base_url + 'PuntoVenta/DespachoPOSController/estadoPedido/' + ID + '/' + Nu_Estado;
    $.ajax({
      url: url,
      type: "GET",
      dataType: "JSON",
      success: function (response) {
        $('#modal-loader').modal('hide');

        $modal_delete.modal('hide');
        $('.modal-message').removeClass('modal-danger modal-warning modal-success');
        $('#modal-message').modal('show');

        if (response.status == 'success') {
          $('.modal-message').addClass(response.style_modal);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);

          getReporteHTML();
        } else {
          $('.modal-message').addClass(response.style_modal);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1500);
        }
      }
    });
  });
}