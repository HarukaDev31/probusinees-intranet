var url, sMethod;

$(function () {
  $('[data-mask]').inputmask();
  $('.select2').select2();
  $( '.div-fecha_historica' ).hide();
  $( '#div-venta_punto_venta' ).hide();


  $('#hidden-estado_entidad').val('1');
  $('#cbo-Estado-modal').html('<option value="1">Activo</option>');
  $('#cbo-Estado-modal').append('<option value="0">Inactivo</option>');

  /* Tipo Documento Identidad */
  $('#cbo-modal-TiposDocumentoIdentidad').change(function () {
    $('#hidden-estado_entidad').val('1');
    $('#cbo-Estado-modal').html('<option value="1">Activo</option>');
    $('#cbo-Estado-modal').append('<option value="0">Inactivo</option>');

    $('#hidden-nu_numero_documento_identidad').val('');

    $('#txt-AID').val('');
    $('#txt-ACodigo').val('');
    $('#txt-Txt_Email_Entidad_Cliente').val('');
    $('#txt-Nu_Celular_Entidad_Cliente').val('');
    $('#txt-Txt_Direccion_Entidad').val('');
    $('#txt-ANombre').val('');
    $('#span-no_nombres_cargando').html('');

    if ($(this).val() == 2) {//DNI
      $('#label-tipo_documento_identidad').text('DNI');
      $('#label-No_Entidad').text('Nombre(s) y Apellidos');
      $('#txt-ACodigo').attr('maxlength', $(this).find(':selected').data('nu_cantidad_caracteres'));
    } else if ($(this).val() == 4) {//RUC
      $('#label-tipo_documento_identidad').text('RUC');
      $('#label-No_Entidad').text('Razón Social');
      $('#txt-ACodigo').attr('maxlength', $(this).find(':selected').data('nu_cantidad_caracteres'));
    }

    setTimeout(function () { $('#txt-ACodigo').focus(); }, 20);
  })

  $('#cbo-modal-TiposDocumentoIdentidad').html('');
  // Obtener tipos de documento de identidad
  url = base_url + 'HelperController/getTiposDocumentoIdentidad';
  $.post(url, function (response) {
    $('#cbo-modal-TiposDocumentoIdentidad').html('');
    for (var i = 0; i < response.length; i++)
      $('#cbo-modal-TiposDocumentoIdentidad').append('<option value="' + response[i]['ID_Tipo_Documento_Identidad'] + '" data-nu_cantidad_caracteres="' + response[i]['Nu_Cantidad_Caracteres'] + '">' + response[i]['No_Tipo_Documento_Identidad_Breve'] + '</option>');
  }, 'JSON');

  $( '#cbo-tipo_consulta_fecha' ).change(function(){
    $( '.div-fecha_historica' ).hide();
    if (  $(this).val() > 0 )
      $( '.div-fecha_historica' ).show();
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
  
  $( '.btn-generar_venta_punto_venta' ).click(function(){
    if ( $( '#cbo-tipo_consulta_fecha' ).val() == '1' || ($( '#cbo-tipo_consulta_fecha' ).val() == '0' && $('#header-a-id_matricula_empleado').length) ) {
      $( '.help-block' ).empty();
    
      var iTipoConsultaFecha, Fe_Inicio, Fe_Fin, ID_Tipo_Documento, ID_Serie_Documento, ID_Numero_Documento, Nu_Estado_Documento, iIdCliente, sNombreCliente, iTipoRecepcionCliente, iEstadoPago, sGlosa;

      iTipoConsultaFecha = $('#cbo-tipo_consulta_fecha').val();
      Fe_Inicio = ParseDateString($('#txt-Filtro_Fe_Inicio').val(), 1, '/');
      Fe_Fin = ParseDateString($('#txt-Filtro_Fe_Fin').val(), 1, '/');
      ID_Tipo_Documento = $('#cbo-filtros_tipos_documento').val();
      ID_Serie_Documento = $('#cbo-filtros_series_documento').val();
      ID_Numero_Documento = ($('#txt-Filtro_NumeroDocumento').val().length == 0 ? '-' : $('#txt-Filtro_NumeroDocumento').val());
      Nu_Estado_Documento = $('#cbo-estado_documento').val();
      iIdCliente = ($('#txt-AID').val().length === 0 ? '-' : $('#txt-AID').val());
      sNombreCliente = ($('#txt-Filtro_Entidad').val().length === 0 ? '-' : $('#txt-Filtro_Entidad').val());
      iTipoRecepcionCliente = $('#cbo-tipo_recepcion_cliente').val();
      iEstadoPago = $('#cbo-estado_pago').val();
      sMethod = $('#hidden-sMethod').val();
      sGlosa = ($('#txt-Filtro_Glosa').val().length === 0 ? '-' : $('#txt-Filtro_Glosa').val());
        
      if ($(this).data('type') == 'html') {
        getReporteHTML();
      } else if ($(this).data('type') == 'pdf') {
        $( '#btn-pdf_venta_punto_venta' ).text('');
        $( '#btn-pdf_venta_punto_venta' ).attr('disabled', true);
        $( '#btn-pdf_venta_punto_venta' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
        
        url = base_url + 'PuntoVenta/VentaPuntoVentaController/sendReportePDF/' + iTipoConsultaFecha + '/' + Fe_Inicio + '/' + Fe_Fin + '/' + ID_Tipo_Documento + '/' + ID_Serie_Documento + '/' + ID_Numero_Documento + '/' + Nu_Estado_Documento + '/' + iIdCliente + '/' + sNombreCliente + '/' + iTipoRecepcionCliente + '/' + iEstadoPago + '/' + sGlosa;
        window.open(url,'_blank');
        
        $( '#btn-pdf_venta_punto_venta' ).text('');
        $( '#btn-pdf_venta_punto_venta' ).append( '<i class="fa fa-file-pdf-o color_icon_pdf"></i> PDF' );
        $( '#btn-pdf_venta_punto_venta' ).attr('disabled', false);
      } else if ($(this).data('type') == 'excel') {
        $( '#btn-excel_venta_punto_venta' ).text('');
        $( '#btn-excel_venta_punto_venta' ).attr('disabled', true);
        $( '#btn-excel_venta_punto_venta' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
        
        url = base_url + 'PuntoVenta/VentaPuntoVentaController/sendReporteEXCEL/' + iTipoConsultaFecha + '/' + Fe_Inicio + '/' + Fe_Fin + '/' + ID_Tipo_Documento + '/' + ID_Serie_Documento + '/' + ID_Numero_Documento + '/' + Nu_Estado_Documento + '/' + iIdCliente + '/' + sNombreCliente + '/' + iTipoRecepcionCliente + '/' + iEstadoPago + '/' + sGlosa;
        window.open(url,'_blank');
        
        $( '#btn-excel_venta_punto_venta' ).text('');
        $( '#btn-excel_venta_punto_venta' ).append( '<i class="fa fa-file-excel-o color_icon_excel"></i> Excel' );
        $( '#btn-excel_venta_punto_venta' ).attr('disabled', false);
      }
    } else {
      alert('Primero se debe de aperturar caja');
    }// ./ if - else
  })//./ btn
  
  $( '#hidden-ID_Tipo_Documento_Identidad_Existente' ).val(2);
  
	// Tipo Documento Identidad
	$( '#modal-cbo-tipo_documento' ).change(function(){
    $('#label_correo').show();
    $('#span_correo').show();
    $('#txt-Txt_Email_Entidad_Cliente').show();

    $('#hidden-estado_entidad').val('1');
    $('#cbo-Estado-modal').html('<option value="1">Activo</option>');
    $('#cbo-Estado-modal').append('<option value="0">Inactivo</option>');

    $('#hidden-nu_numero_documento_identidad').val('');

    $('#txt-AID').val('');
    $('#txt-ACodigo').val('');
    $('#txt-Txt_Email_Entidad_Cliente').val('');
    $('#txt-Nu_Celular_Entidad_Cliente').val('');
    $('#txt-Txt_Direccion_Entidad').val('');
    $('#txt-ANombre').val('');
    $('#span-no_nombres_cargando').html('');

    if ($(this).val() == 4) {//Boleta
      $('#label-tipo_documento_identidad').text('DNI');
      $('#cbo-modal-TiposDocumentoIdentidad').val(2);//DNI
      $('#txt-ACodigo').attr('maxlength', 8);
    } else if ($(this).val() == 3) {//Factura
      $('#label-tipo_documento_identidad').text('RUC');
      $('#cbo-modal-TiposDocumentoIdentidad').val(4);//RUC
      $('#txt-ACodigo').attr('maxlength', 11);
    }

    setTimeout(function () { $('#txt-ACodigo').focus(); }, 20);	
  })
  
	$( "#txt-Txt_Email_Entidad_Cliente" ).blur(function() {
		caracteresCorreoValido($(this).val(), '#span-email');
  })

  // COBRAR CREDITO
  $('#btn-cobrar_cliente').click(function () {
    var fPagoClienteCobranza = parseFloat($('#tel-cobrar_cliente-fPagoCliente').val());
    if (fPagoClienteCobranza == 0.00 || isNaN(fPagoClienteCobranza)) {
      $('[name="fPagoCliente"]').closest('.form-group').find('.help-block').html('Ingresar monto');
      $('[name="fPagoCliente"]').closest('.form-group').removeClass('has-success').addClass('has-error');

      scrollToError($('.modal-cobrar_cliente .modal-body'), $('[name="fPagoCliente"]'));
    } else if ($( '#hidden-cobrar_cliente-detraccion' ).val()=='0' && fPagoClienteCobranza > parseFloat($('#hidden-cobrar_cliente-fsaldo').val())) {
      $('#tel-cobrar_cliente-fPagoCliente').closest('.form-group').find('.help-block').html('Debes de cobrar <b>' + $('#hidden-cobrar_cliente-fsaldo').val() + '</b>');
      $('#tel-cobrar_cliente-fPagoCliente').closest('.form-group').removeClass('has-success').addClass('has-error');

      scrollToError($('.modal-cobrar_cliente .modal-body'), $('#tel-cobrar_cliente-fPagoCliente'));
    } else if ( $( '#hidden-cobrar_cliente-detraccion' ).val()=='1' && (fPagoClienteCobranza < parseFloat($( '#hidden-cobrar_cliente-fsaldo' ).val()) || fPagoClienteCobranza > parseFloat($( '#hidden-cobrar_cliente-fsaldo' ).val())) ) {
      $( '#tel-cobrar_cliente-fPagoCliente' ).closest('.form-group').find('.help-block').html('Debes de cobrar <b>' + $( '#hidden-cobrar_cliente-fsaldo' ).val() + '</b>' );
      $( '#tel-cobrar_cliente-fPagoCliente' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    
      scrollToError($('.modal-cobrar_cliente .modal-body'), $( '#modal-tel-cobrar_cliente-fPagoCliente' ));
    } else if ($('#cbo-modal_quien_recibe').val() == 0 && $('[name="sNombreRecepcion"]').val().length === 0) {
      $('[name="sNombreRecepcion"]').closest('.form-group').find('.help-block').html('Ingresar datos');
      $('[name="sNombreRecepcion"]').closest('.form-group').removeClass('has-success').addClass('has-error');

      scrollToError($('.modal-cobrar_cliente .modal-body'), $('[name="sNombreRecepcion"]'));
    } else {
      $('.help-block').empty();
      $('[name="fPagoCliente"]').closest('.form-group').removeClass('has-error');
      $('[name="sNombreRecepcion"]').closest('.form-group').removeClass('has-error');

      $('#btn-cobrar_cliente').text('');
      $('#btn-cobrar_cliente').attr('disabled', true);
      $('#btn-cobrar_cliente').append('Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>');
      $('#btn-salir').attr('disabled', true);

      url = base_url + 'PuntoVenta/VentaPuntoVentaController/cobrarVenta';
      $.ajax({
        type: 'POST',
        dataType: 'JSON',
        url: url,
        data: $('#form-cobrar_cliente').serialize(),
        success: function (response) {
          $('.modal-message').removeClass('modal-danger modal-warning modal-success');
          $('#modal-message').modal('show');

          if (response.sStatus == 'success') {
            $('.modal-cobrar_cliente').modal('hide');

            $('.modal-message').addClass('modal-' + response.sStatus);
            $('.modal-title-message').text(response.sMessage);
            setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);

            getReporteHTML();
          } else {
            $('.modal-message').addClass('modal-' + response.sStatus);
            $('.modal-title-message').text(response.sMessage);
            setTimeout(function () { $('#modal-message').modal('hide'); }, 3100);
          }

          $('#btn-cobrar_cliente').text('');
          $('#btn-cobrar_cliente').append('Cobrar');
          $('#btn-cobrar_cliente').attr('disabled', false);
          $('#btn-salir').attr('disabled', false);
        }
      })
        .fail(function (jqXHR, textStatus, errorThrown) {
          $('.modal-message').removeClass('modal-danger modal-warning modal-success');

          $('#modal-message').modal('show');
          $('.modal-message').addClass('modal-danger');
          $('.modal-title-message').text(textStatus + ' [' + jqXHR.status + ']: ' + errorThrown);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 3100);

          //Message for developer
          console.log(jqXHR.responseText);

          $('#btn-cobrar_cliente').text('');
          $('#btn-cobrar_cliente').attr('disabled', false);
          $('#btn-cobrar_cliente').append('Cobrar');
          $('#btn-salir').attr('disabled', false);
        })
    }
  })

  // COBRAR CREDITO MASIVO
  $( '#btn-cobro_masivo_venta' ).click(function(){
    var fPagoClienteCobranza = parseFloat($( '#tel-cobro_masivo_venta-fPagoCliente' ).val());
    if ( fPagoClienteCobranza == 0.00 || isNaN(fPagoClienteCobranza) ) {
      $( '[name="fPagoCliente"]' ).closest('.form-group').find('.help-block').html( 'Ingresar monto' );
      $( '[name="fPagoCliente"]' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    
      scrollToError($('.modal-cobro_masivo_venta .modal-body'), $( '[name="fPagoCliente"]' ));
    } else if (fPagoClienteCobranza > parseFloat($( '#hidden-cobro_masivo_venta-fsaldo' ).val()) ) {
      $('#tel-cobro_masivo_venta-fPagoCliente').closest('.form-group').find('.help-block').html('Debes de cobrar <b>' + $( '#hidden-cobro_masivo_venta-fsaldo' ).val() + '</b>' );
      $( '#tel-cobro_masivo_venta-fPagoCliente' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    
      scrollToError($('.modal-cobro_masivo_venta .modal-body'), $( '#tel-cobro_masivo_venta-fPagoCliente' ));
    } else {
      $( '.help-block' ).empty();
      $( '[name="fPagoCliente"]' ).closest('.form-group').removeClass('has-error');
      $( '[name="sNombreRecepcion"]' ).closest('.form-group').removeClass('has-error');
      
      $( '#btn-cobro_masivo_venta' ).text('');
      $( '#btn-cobro_masivo_venta' ).attr('disabled', true);
      $( '#btn-cobro_masivo_venta' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
      $( '#btn-salir' ).attr('disabled', true);

      url = base_url + 'PuntoVenta/VentaPuntoVentaController/cobrarVentaMasiva';
      $.ajax({
        type : 'POST',
        dataType : 'JSON',
        url : url,
        data: $('#form-cobro_masivo_venta').serialize(),
        success : function( response ){
          $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
          $( '#modal-message' ).modal('show');

          if ( response.sStatus=='success' ) {
            $( '.modal-cobro_masivo_venta' ).modal('hide');

            $( '.modal-message' ).addClass( 'modal-' + response.sStatus);
            $( '.modal-title-message' ).text( response.sMessage );
            setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
            
            getReporteHTML();
          } else {
            $( '.modal-message' ).addClass( 'modal-' + response.sStatus );
            $( '.modal-title-message' ).text( response.sMessage );
            setTimeout(function() {$('#modal-message').modal('hide');}, 3100);
          }
          
          $( '#btn-cobro_masivo_venta' ).text('');
          $( '#btn-cobro_masivo_venta' ).append( 'Cobrar' );
          $( '#btn-cobro_masivo_venta' ).attr('disabled', false);
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

        $( '#btn-cobro_masivo_venta' ).text('');
        $( '#btn-cobro_masivo_venta' ).attr('disabled', false);
        $( '#btn-cobro_masivo_venta' ).append( 'Cobrar' );
        $( '#btn-salir' ).attr('disabled', false);
      })
    }
  })

  // ENTREGAR PEDIDO LAVANDERIA
  $( '#btn-entregar_pedido' ).click(function(){
		var fPagoClienteCobranza = parseFloat($( '#tel-entregar_pedido-fPagoCliente' ).val());
    if ( $( '#hidden-entregar_pedido-fsaldo' ).val() != '0' && (fPagoClienteCobranza == 0.00 || isNaN(fPagoClienteCobranza)) ) {
      $( '[name="fPagoCliente"]' ).closest('.form-group').find('.help-block').html( 'Ingresar monto' );
      $( '[name="fPagoCliente"]' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    
      scrollToError($('.modal-entregar_pedido .modal-body'), $( '[name="fPagoCliente"]' ));
    } else if ( $( '#hidden-entregar_pedido-fsaldo' ).val() != '0' && fPagoClienteCobranza > parseFloat($( '#hidden-entregar_pedido-fsaldo' ).val()) ) {
      $( '#tel-entregar_pedido-fPagoCliente' ).closest('.form-group').find('.help-block').html('Debes de cobrar <b>' + $( '#hidden-entregar_pedido-fsaldo' ).val() + '</b>' );
      $( '#tel-entregar_pedido-fPagoCliente' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    
      scrollToError($('.modal-entregar_pedido .modal-body'), $( '#tel-entregar_pedido-fPagoCliente' ));
    } else if ( $( '#cbo-modal_quien_recibe' ).val() == 0 && $( '[name="sNombreRecepcion"]' ).val().length === 0 ) {
      $( '[name="sNombreRecepcion"]' ).closest('.form-group').find('.help-block').html('Ingresar datos');
      $( '[name="sNombreRecepcion"]' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    
      scrollToError($('.modal-entregar_pedido .modal-body'), $( '[name="sNombreRecepcion"]' ));
    } else {
      $( '.help-block' ).empty();
      $( '[name="sNombreRecepcion"]' ).closest('.form-group').removeClass('has-error');
      
      $( '#btn-entregar_pedido' ).text('');
      $( '#btn-entregar_pedido' ).attr('disabled', true);
      $( '#btn-entregar_pedido' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
      $( '#btn-salir' ).attr('disabled', true);

      url = base_url + 'PuntoVenta/VentaPuntoVentaController/entregarPedidoLavado';
      $.ajax({
        type : 'POST',
        dataType : 'JSON',
        url : url,
        data : $('#form-entregar_pedido').serialize(),
        success : function( response ){
          $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
          $( '#modal-message' ).modal('show');

          if ( response.sStatus=='success' ) {
            $( '.modal-entregar_pedido' ).modal('hide');

            $( '.modal-message' ).addClass( 'modal-' + response.sStatus);
            $( '.modal-title-message' ).text( response.sMessage );
            setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
            
            getReporteHTML();
          } else {
            $( '.modal-message' ).addClass( 'modal-' + response.sStatus );
            $( '.modal-title-message' ).text( response.sMessage );
            setTimeout(function() {$('#modal-message').modal('hide');}, 3100);
          }
          
          $( '#btn-entregar_pedido' ).text('');
          $( '#btn-entregar_pedido' ).append( 'Entregar' );
          $( '#btn-entregar_pedido' ).attr('disabled', false);
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

        $( '#btn-entregar_pedido' ).text('');
        $( '#btn-entregar_pedido' ).attr('disabled', false);
        $( '#btn-entregar_pedido' ).append( 'Entregar' );
        $( '#btn-salir' ).attr('disabled', false);
      })
    }
  })
  
  // FACTURAR ORDEN LAVANDERIA
  $( '#btn-facturar_orden_lavanderia' ).click(function(){
    if (parseFloat($('[name="fTotalDocumento"]').val()) < 0.10) {
      alert('El total no puede ser menor a 0.10 céntimos');
      return false;
    } if ($('#modal-cbo-tipo_documento').val() == 3 && $('#cbo-modal-TiposDocumentoIdentidad').val() == 2) {
      $('#cbo-modal-TiposDocumentoIdentidad').closest('.form-group').find('.help-block').html('No se puede FACTURAR a DNI');
      $('#cbo-modal-TiposDocumentoIdentidad').closest('.form-group').removeClass('has-success').addClass('has-error');

      //scrollToError($('.modal-facturar_orden_lavanderia .modal-body'), $( '#txt-ACodigo' ));
    } else if ( $( '#txt-ACodigo' ).val().length === 0 ) {
      $( '#txt-ACodigo' ).closest('.form-group').find('.help-block').html('Ingresar datos');
      $( '#txt-ACodigo' ).closest('.form-group').removeClass('has-success').addClass('has-error');

      //scrollToError($('.modal-facturar_orden_lavanderia .modal-body'), $( '#txt-ACodigo' ));
    } else if ( $( '#txt-ANombre' ).val().length === 0 ) {
      $( '#txt-ANombre' ).closest('.form-group').find('.help-block').html('Ingresar datos');
      $( '#txt-ANombre' ).closest('.form-group').removeClass('has-success').addClass('has-error');

      //scrollToError($('.modal-facturar_orden_lavanderia .modal-body'), $( '#txt-ANombre' ));
    } else if ($('#modal-cbo-tipo_documento').val() == 3 && $('#cbo-modal-TiposDocumentoIdentidad').val() == 4 && $('#txt-ACodigo').val().length < 11 ) {
      $('#modal-cbo-tipo_documento').closest('.form-group').find('.help-block').html('Ingresar 11 dígitos para RUC');
      $('#modal-cbo-tipo_documento').closest('.form-group').removeClass('has-success').addClass('has-error');

      //scrollToError($('.modal-facturar_orden_lavanderia .modal-body'), $('#modal-cbo-tipo_documento'));
    } else {
      $( '.help-block' ).empty();
      
      $( '#btn-facturar_orden_lavanderia' ).text('');
      $( '#btn-facturar_orden_lavanderia' ).attr('disabled', true);
      $( '#btn-facturar_orden_lavanderia' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
      $( '#btn-salir' ).attr('disabled', true);

      url = base_url + 'PuntoVenta/VentaPuntoVentaController/facturarOrdenLavanderia';
      $.ajax({
        type : 'POST',
        dataType : 'JSON',
        url : url,
        data : $('#form-facturar_orden_lavanderia').serialize(),
        success : function( response ){
          $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
          $( '#modal-message' ).modal('show');

          if ( response.sStatus=='success' ) {
            $( '.modal-facturar_orden_lavanderia' ).modal('hide');

            $( '.modal-message' ).addClass( 'modal-' + response.sStatus);
            $( '.modal-title-message' ).text( response.sMessage );
            setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
            
            getReporteHTML();

            // Mandar a imprimir impresora
            //var Accion = 'imprimir', iIdDocumentoCabecera = response.iIdDocumentoCabecera, url_print = 'ocultar-img-logo_punto_venta_click';
            //formatoImpresionTicket(Accion, iIdDocumentoCabecera, url_print);

            // Mandar a imprimir impresora
            var Accion = 'imprimir', url_print = 'ocultar-img-logo_punto_venta_click', url_pdf = '';
            url_pdf = (response.arrResponseFE.enlace_del_pdf !== undefined ? response.arrResponseFE.enlace_del_pdf : '');
            formatoImpresionTicket(Accion, response.iIdDocumentoCabecera, url_print, url_pdf);
          } else {
            $( '.modal-message' ).addClass( 'modal-' + response.sStatus );
            $( '.modal-title-message' ).text( response.sMessage );
            setTimeout(function() {$('#modal-message').modal('hide');}, 5100);
          }
          
          $( '#btn-facturar_orden_lavanderia' ).text('');
          $( '#btn-facturar_orden_lavanderia' ).append( 'Facturar' );
          $( '#btn-facturar_orden_lavanderia' ).attr('disabled', false);
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

        $( '#btn-facturar_orden_lavanderia' ).text('');
        $( '#btn-facturar_orden_lavanderia' ).attr('disabled', false);
        $('#btn-facturar_orden_lavanderia').append( 'Facturar' );
        $( '#btn-salir' ).attr('disabled', false);
      })
    }
  })

  // MODIFICAR DATOS
  $('#btn-modificar_venta').click(function () {
    $('#btn-modificar_venta').text('');
    $('#btn-modificar_venta').attr('disabled', true);
    $('#btn-modificar_venta').append('Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>');
    $('#btn-salir').attr('disabled', true);

    url = base_url + 'PuntoVenta/VentaPuntoVentaController/modificarVenta';
    $.ajax({
      type: 'POST',
      dataType: 'JSON',
      url: url,
      data: $('#form-modificar_venta').serialize(),
      success: function (response) {
        $('.modal-message').removeClass('modal-danger modal-warning modal-success');
        $('#modal-message').modal('show');

        if (response.sStatus == 'success') {
          $('.modal-modificar_venta').modal('hide');

          $('.modal-message').addClass('modal-' + response.sStatus);
          $('.modal-title-message').text(response.sMessage);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);

          getReporteHTML();
        } else {
          $('.modal-message').addClass('modal-' + response.sStatus);
          $('.modal-title-message').text(response.sMessage);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 3100);
        }

        $('#btn-modificar_venta').text('');
        $('#btn-modificar_venta').append('Modificar');
        $('#btn-modificar_venta').attr('disabled', false);
        $('#btn-salir').attr('disabled', false);
      }
    })
    .fail(function (jqXHR, textStatus, errorThrown) {
      $('.modal-message').removeClass('modal-danger modal-warning modal-success');

      $('#modal-message').modal('show');
      $('.modal-message').addClass('modal-danger');
      $('.modal-title-message').text(textStatus + ' [' + jqXHR.status + ']: ' + errorThrown);
      setTimeout(function () { $('#modal-message').modal('hide'); }, 3100);

      //Message for developer
      console.log(jqXHR.responseText);

      $('#btn-modificar_venta').text('');
      $('#btn-modificar_venta').attr('disabled', false);
      $('#btn-modificar_venta').append('Modificar');
      $('#btn-salir').attr('disabled', false);
    })
  })
})

function getReporteHTML(){
  var Fe_Inicio, Fe_Fin, ID_Tipo_Documento, ID_Serie_Documento, ID_Numero_Documento, Nu_Estado_Documento, iIdCliente, sNombreCliente, iTipoRecepcionCliente, iEstadoPago, sGlosa;
  
  iTipoConsultaFecha  = $( '#cbo-tipo_consulta_fecha' ).val();
  Fe_Inicio           = ParseDateString($( '#txt-Filtro_Fe_Inicio' ).val(), 1, '/');
  Fe_Fin              = ParseDateString($( '#txt-Filtro_Fe_Fin' ).val(), 1, '/');
  ID_Tipo_Documento   = $( '#cbo-filtros_tipos_documento' ).val();
  ID_Serie_Documento  = $( '#cbo-filtros_series_documento' ).val();
  ID_Numero_Documento = ($( '#txt-Filtro_NumeroDocumento' ).val().length == 0 ? '-' : $( '#txt-Filtro_NumeroDocumento' ).val());
  Nu_Estado_Documento = $( '#cbo-estado_documento' ).val();
  iIdCliente = ($( '#txt-AID' ).val().length === 0 ? '-' : $( '#txt-AID' ).val());
  sNombreCliente = ($( '#txt-Filtro_Entidad' ).val().length === 0 ? '-' : $( '#txt-Filtro_Entidad' ).val());
  iTipoRecepcionCliente = $('#cbo-tipo_recepcion_cliente').val();
  iEstadoPago = $('#cbo-estado_pago').val();
  sGlosa = ($('#txt-Filtro_Glosa').val().length === 0 ? '-' : $('#txt-Filtro_Glosa').val());
  
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
    iTipoRecepcionCliente : iTipoRecepcionCliente,
    iEstadoPago: iEstadoPago,
    sGlosa : sGlosa,
    sMethod: sMethod
  };
  url = base_url + 'PuntoVenta/VentaPuntoVentaController/sendReporte';
  $.post( url, arrPost, function( response ){
    if ( response.sStatus == 'success' ) {
      var iTotalRegistros = response.arrData.length, response=response.arrData, tr_body = '', tr_foot = '', arrParams = '', sButtonCobrarPedido = '', sButtonCobrarDetraccion='', sButtonEntregarPedido='', sButtonFacturarLavanderia='';
      var subtotal_s = 0.00, descuento_s = 0.00, igv_s = 0.00, total_s = 0.00, total_d = 0.00, fTotalSaldo=0.00, fTotalDetraccion=0.00;
      var sum_general_subtotal_s = 0.00, sum_general_igv_s = 0.00, sum_general_descuento_s = 0.00, sum_general_total_s = 0.00, sum_general_total_d = 0.00, sum_general_total_saldo = 0.00;
      for (var i = 0; i < iTotalRegistros; i++) {
        fTotalSaldo = (!isNaN(parseFloat(response[i].Ss_Total_Saldo)) ? parseFloat(response[i].Ss_Total_Saldo) : 0);
        fTotalDetraccion = (!isNaN(parseFloat(response[i].Ss_Detraccion)) ? parseFloat(response[i].Ss_Detraccion) : 0);
        total_s = (!isNaN(parseFloat(response[i].Ss_Total)) ? parseFloat(response[i].Ss_Total) : 0);
        total_d = (!isNaN(parseFloat(response[i].Ss_Total_Extranjero)) ? parseFloat(response[i].Ss_Total_Extranjero) : 0);
        
        arrParams = {
          'iIdEmpresa' : response[i].ID_Empresa,
          'iIdDocumentoCabecera' : response[i].ID_Documento_Cabecera,
          'iIdTipoDocumento': ID_Tipo_Documento,
          'fTotal': total_s,
          'iIdEntidadCliente': response[i].ID_Entidad,
          'iIdTipoDocumentoIdentidadCliente': response[i].ID_Tipo_Documento_Identidad,
          'sNumeroDocumentoIdentidadCliente': response[i].Nu_Documento_Identidad,
          'sCliente' : response[i].No_Entidad,
          'sEmailCliente': response[i].Txt_Email_Entidad,
          'Nu_Celular_Entidad': response[i].Nu_Celular_Entidad,
          'iEstadoCliente': response[i].Nu_Estado_Entidad,
          'sTipoDocumento' : response[i].No_Tipo_Documento_Breve,
          'sSerieDocumento' : response[i].ID_Serie_Documento,
          'sNumeroDocumento' : response[i].ID_Numero_Documento,
          'iEstadoLavadoRecepcionCliente' : response[i].Nu_Estado_Lavado_Recepcion_Cliente,
          'sSignoMoneda' : response[i].No_Signo,
          'iIdDocumentoMedioPago' : response[i].ID_Documento_Medio_Pago,
        }
        arrParams = JSON.stringify(arrParams);

        sButtonCobrarPedido = '';
        if ( fTotalSaldo > 0 ) {
          var arrParams = JSON.parse(arrParams); //change to obj
          arrParams.fTotalSaldo = fTotalSaldo; //add something
          arrParams = JSON.stringify(arrParams); //change back to string         
          sButtonCobrarPedido = "<button type='button' class='btn btn-xs btn-link' alt='Cobrar' title='Cobrar' href='javascript:void(0)' onclick='cobrarPedido(" + arrParams + ")'><i class='fa fa-2x fa-money'></i></button>";
        }

        sButtonCobrarDetraccion = '';
        if ( fTotalDetraccion > 0 ) {
          var arrParams = JSON.parse(arrParams); //change to obj
          arrParams.fTotalSaldo = fTotalDetraccion; //add something
          arrParams = JSON.stringify(arrParams); //change back to string
          sButtonCobrarDetraccion = "<br><button type='button' class='btn btn-xs btn-link' alt='Cobrar Detraccion' title='Cobrar Detraccion' href='javascript:void(0)' onclick='cobrarPedido(" + arrParams + ")'>Cobrar Detraccion</button>";
        }

        //arrParams = JSON.stringify(arrParams);

        sButtonEntregarPedido = '';
        if ( response[i].Nu_Estado_Lavado_Recepcion_Cliente != 3 ) {
          sButtonEntregarPedido = "<button type='button' class='btn btn-xs btn-link' alt='Entregar pedido' title='Entregar pedido' href='javascript:void(0)' onclick='entregarPedido(" + arrParams + ")'>Entregar pedido</button>";
        }

        sButtonFacturarLavanderia = '';
        if (response[i].ID_Tipo_Documento == 2 && $('#hidden-Nu_Tipo_Proveedor_FE').val()!=3 && response[i].Nu_Estado == 6 ) {
          sButtonFacturarLavanderia = "<button type='button' class='btn btn-xs btn-link' alt='Facturar' title='Facturar' href='javascript:void(0)' onclick='facturarOrdenLavanderia(" + arrParams + ")'><i class='fa fa-2x fa-book'></i></button>";
        }

        tr_body +=
        "<tr data-id_entidad=" + response[i].ID_Entidad + ">"
          +"<td class='text-center'>" + (sButtonCobrarPedido != '' ? "<input type='checkbox' data-id_empresa=" + response[i].ID_Empresa + " data-no_signo='" + response[i].No_Signo + "' data-id_entidad=" + response[i].ID_Entidad + " data-no_entidad='" + response[i].No_Entidad + "' data-f_total_saldo=" + fTotalSaldo + " id='" + response[i].ID_Documento_Cabecera + "' class='check-iIdDocumentoCabecera' name='arrIdDocumentoCabecera[" + response[i].ID_Documento_Cabecera + "]'>" : "") + "</td>"
          //+"<td class='text-left'>" + response[i].No_Empleado + "</td>"
          +"<td class='text-center'>" + response[i].No_Tipo_Recepcion + "</td>"
          +"<td class='text-center'>" + response[i].Fe_Emision_Hora + "</td>"
          +"<td class='text-center'>" + response[i].No_Tipo_Documento_Breve + "<br> " + response[i].ID_Serie_Documento + " - " + response[i].ID_Numero_Documento + "</td>"
          /*
          +"<td class='text-center'>" + response[i].No_Tipo_Documento_Breve + "</td>"
          +"<td class='text-center'>" + response[i].ID_Serie_Documento + "</td>"
          +"<td class='text-center'>" + response[i].ID_Numero_Documento + "</td>"
          */
          +"<td class='text-left'>" + response[i].No_Entidad + "</td>"
          //+"<td class='text-right'>" + number_format(response[i].Ss_Tipo_Cambio, 3) + "</td>"
          +"<td class='text-right'>" + (response[i].ID_Tipo_Documento != 5 ? '' : '-') + number_format(total_s, 2) + "</td>"
          //+"<td class='text-right'>" + (response[i].ID_Tipo_Documento != 5 ? '' : '-') + number_format(total_d, 2) + "</td>"
          +"<td class='text-right'>" + (response[i].ID_Tipo_Documento != 5 ? '' : '-') + number_format(fTotalSaldo, 2) + "</td>"
          //+"<td class='text-center'><span class='label label-" + response[i].No_Class_Estado_Pago + "'>" + response[i].No_Estado_Pago + "</span></td>"
          +"<td class='text-center'><span class='label label-" + response[i].No_Class_Estado + "'>" + response[i].No_Estado + "</span></td>"
          +"<td class='text-center'>" + response[i].btn_modificar + "</td>"
          +"<td class='text-center'>" + response[i].btn_anular + "</td>"
          //+"<td class='text-center'>" + (response[i].Nu_Estado == 6 || response[i].Nu_Estado == 8 ? response[i].sAccionImprimir : '') + "</td>"
          +"<td class='text-center'>" + (response[i].Nu_Estado != 9 || response[i].Nu_Estado != 11 ? response[i].sAccionImprimir : '') + "</td>"
          +"<td class='text-center'>" + sButtonCobrarPedido + sButtonCobrarDetraccion + "</td>"
          +"<td class='text-center'>" + sButtonFacturarLavanderia + "</td>"
          +"<td class='text-center'>" + (response[i].Nu_Estado == 6 || response[i].Nu_Estado == 8 ? response[i].sAccionVer : '') + "</td>"
          +"<td class='text-left'>" + response[i].Txt_Glosa + "</td>"
          +"<td class='text-left'>" + response[i].Txt_Garantia + "</td>"
        +"</tr>";
        
        sum_general_total_s += total_s;
        sum_general_total_d += total_d;
        sum_general_total_saldo += fTotalSaldo;
      }
      
      tr_foot =
      "<tfoot>"
        +"<tr>"
          +"<th class='text-right' colspan='5'>Total</th>"
          +"<th class='text-right'>" + number_format(sum_general_total_s, 2) + "</th>"
          //+"<th class='text-right'>" + number_format(sum_general_total_d, 2) + "</th>"
          +"<th class='text-right'>" + number_format(sum_general_total_saldo, 2) + "</th>"
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
    
    $( '#div-venta_punto_venta' ).show();
    $( '#table-venta_punto_venta > tbody' ).append(tr_body);
    $( '#table-venta_punto_venta > tbody' ).after(tr_foot);

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

function cobrarPedido(arrParams){  
  $( '#form-cobrar_cliente' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();

  $( '.div-forma_pago').hide();
  $( '.div-modal_datos_tarjeta_credito').hide();
  $( '.div-estado_lavado_recepcion_cliente' ).hide();
  $( '.div-recibe_otra_persona' ).hide();

  $( '.modal-cobrar_cliente' ).modal('show');

  $( '[name="iIdDocumentoCabecera"]' ).val( arrParams.iIdDocumentoCabecera );
  $( '[name="iIdDocumentoMedioPagoCobrarCliente"]' ).val( arrParams.iIdDocumentoMedioPago );
  $( '#hidden-cobrar_cliente-fsaldo' ).val( arrParams.fTotalSaldo );

  $( '#hidden-cobrar_cliente-detraccion' ).val( 0 );
  if(arrParams.iDetraccion !== undefined && arrParams.iDetraccion == 1)
    $( '#hidden-cobrar_cliente-detraccion' ).val( arrParams.iDetraccion );

  $( '#modal-header-cobrar_cliente-title' ).text(arrParams.sTipoDocumento + ' - ' + arrParams.sSerieDocumento + ' - ' + arrParams.sNumeroDocumento);  
  $( '#cobrar_cliente-modal-body-cliente' ).text('Cliente: ' + arrParams.sCliente);
  $( '#cobrar_cliente-modal-body-saldo_cliente' ).text('Saldo: ' + arrParams.sSignoMoneda + ' ' + arrParams.fTotalSaldo);

  $( '.div-forma_pago').show();
  $( '.modal-cobrar_cliente' ).on('shown.bs.modal', function() {
    $( '[name="fPagoCliente"]' ).focus();
    $( '[name="fPagoCliente"]' ).val( arrParams.fTotalSaldo );
  });

  url = base_url + 'HelperController/getMediosPago';
  var arrPost = {
    iIdEmpresa : arrParams.iIdEmpresa,
  };
  $.post( url, arrPost, function( response ){
    $( '#cbo-modal_forma_pago' ).html('');
    for (var i = 0; i < response.length; i++) {
      if ( response[i].Nu_Tipo != 1 )
        $( '#cbo-modal_forma_pago' ).append( '<option value="' + response[i].ID_Medio_Pago + '" data-nu_tipo_medio_pago="' + response[i].Nu_Tipo + '">' + response[i].No_Medio_Pago + '</option>' );
    }
  }, 'JSON');
  
  // Modal de cobranza al cliente
  $( '#cbo-modal_forma_pago' ).change(function(){
    ID_Medio_Pago = $(this).val();
    Nu_Tipo_Medio_Pago = $(this).find(':selected').data('nu_tipo_medio_pago');
    $( '.div-modal_datos_tarjeta_credito').hide();
    $( '#cbo-cobrar_cliente-modal_tarjeta_credito' ).html('');
    $( '#tel-nu_referencia' ).val('');
    $( '#tel-nu_ultimo_4_digitos_tarjeta' ).val('');
    if (Nu_Tipo_Medio_Pago==2){
      $( '.div-modal_datos_tarjeta_credito').show();

      url = base_url + 'HelperController/getTiposTarjetaCredito';
      $.post( url, {ID_Medio_Pago : ID_Medio_Pago} , function( response ){
        $( '#cbo-cobrar_cliente-modal_tarjeta_credito' ).html('');
        for (var i = 0; i < response.length; i++)
          $( '#cbo-cobrar_cliente-modal_tarjeta_credito' ).append( '<option value="' + response[i].ID_Tipo_Medio_Pago + '">' + response[i].No_Tipo_Medio_Pago + '</option>' );
      }, 'JSON');
    }

    setTimeout(function(){ $( '[name="fPagoCliente"]' ).focus(); $( '[name="fPagoCliente"]' ).select(); }, 20);		
  })
}

function entregarPedido(arrParams){
  $( '#form-entregar_pedido' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();

  $( '.div-forma_pago').hide();
  $( '.div-modal_datos_tarjeta_credito').hide();
  $( '.div-estado_lavado_recepcion_cliente' ).hide();
  $( '.div-recibe_otra_persona' ).hide();

  $( '.modal-entregar_pedido' ).modal('show');

  $( '[name="iIdDocumentoCabecera"]' ).val( arrParams.iIdDocumentoCabecera );
  $( '[name="iIdDocumentoMedioPago"]' ).val( arrParams.iIdDocumentoMedioPago );
  $( '#hidden-entregar_pedido-fsaldo' ).val( arrParams.fTotalSaldo );

  $( '#modal-header-entregar_pedido-title' ).text(arrParams.sTipoDocumento + ' - ' + arrParams.sSerieDocumento + ' - ' + arrParams.sNumeroDocumento);  
  $( '#entregar_pedido-modal-body-cliente' ).text('Cliente: ' + arrParams.sCliente);
  $( '#entregar_pedido-modal-body-saldo_cliente' ).text('Saldo: ' + arrParams.sSignoMoneda + ' ' + arrParams.fTotalSaldo);

  if ( arrParams.fTotalSaldo > 0 ) {
    $( '.div-forma_pago').show();
    $( '.modal-entregar_pedido' ).on('shown.bs.modal', function() {
      $( '[name="fPagoCliente"]' ).focus();
      $( '[name="fPagoCliente"]' ).val( arrParams.fTotalSaldo );
    });

    url = base_url + 'HelperController/getMediosPago';
    var arrPost = {
      iIdEmpresa : arrParams.iIdEmpresa,
    };
    $.post( url, arrPost, function( response ){
      $( '#cbo-modal_forma_pago_entrega_pedido' ).html('');
      for (var i = 0; i < response.length; i++) {
        if (  response[i].Nu_Tipo != 1 ) {
          $( '#cbo-modal_forma_pago_entrega_pedido' ).append( '<option value="' + response[i].ID_Medio_Pago + '" data-nu_tipo_medio_pago="' + response[i].Nu_Tipo + '">' + response[i].No_Medio_Pago + '</option>' );
        }
      }
    }, 'JSON');
    
    // Modal de cobranza al cliente
    $( '#cbo-modal_forma_pago_entrega_pedido' ).change(function(){
      ID_Medio_Pago = $(this).val();
      Nu_Tipo_Medio_Pago = $(this).find(':selected').data('nu_tipo_medio_pago');
      $( '.div-modal_datos_tarjeta_credito').hide();
      $( '#cbo-entregar_pedido-modal_tarjeta_credito' ).html('');
      $( '#tel-nu_referencia' ).val('');
      $( '#tel-nu_ultimo_4_digitos_tarjeta' ).val('');
      if (Nu_Tipo_Medio_Pago==2){
        $( '.div-modal_datos_tarjeta_credito').show();

        url = base_url + 'HelperController/getTiposTarjetaCredito';
        $.post( url, {ID_Medio_Pago : ID_Medio_Pago} , function( response ){
          $( '#cbo-entregar_pedido-modal_tarjeta_credito' ).html('');
          for (var i = 0; i < response.length; i++)
            $( '#cbo-entregar_pedido-modal_tarjeta_credito' ).append( '<option value="' + response[i].ID_Tipo_Medio_Pago + '">' + response[i].No_Tipo_Medio_Pago + '</option>' );
        }, 'JSON');
      }

      setTimeout(function(){ $( '[name="fPagoCliente"]' ).focus(); $( '[name="fPagoCliente"]' ).select(); }, 20);		
    })
  }// /. if saldo > 0

  $( '#cbo-estado_lavado_recepcion_cliente' ).change(function(){
    $( '.div-estado_lavado_recepcion_cliente' ).hide();
    if($(this).val() == 3) {
      $( '.div-estado_lavado_recepcion_cliente' ).show();
    }
  })

  $( '#cbo-modal_quien_recibe' ).change(function(){
    $( '.div-recibe_otra_persona' ).hide();
    if($(this).val() == 0) {
      $( '.div-recibe_otra_persona' ).show();
      $( '[name="sNombreRecepcion"]' ).focus();
    }
  })
}

function facturarOrdenLavanderia(arrParams) {
  $('#form-facturar_orden_lavanderia')[0].reset();

  $('.form-group').removeClass('has-error');
  $('.form-group').removeClass('has-success');
  $('.help-block').empty();

  $( '.modal-facturar_orden_lavanderia' ).modal('show');

  $('.div-fecha_convertir').show();
  //$('.div-fecha_convertir').hide();
  if ( parseFloat(arrParams.fTotalSaldo) > 0.00 )
    $('.div-fecha_convertir').show();

  $('.date-picker-invoice').val(fDay + '/' + fMonth + '/' + fYear);
  $('.input-datepicker-today-to-more').val(fDay + '/' + fMonth + '/' + fYear);

  $('#txt-fe_emision_convertir').datepicker({}).on('changeDate', function (selected) {
    var minDate = new Date(selected.date.valueOf());
    $('#txt-fe_vencimiento_convertir').datepicker('setStartDate', minDate);
  });

  var dNuevaFechaVencimiento = sumaFecha(1, $( '#txt-fe_vencimiento_convertir' ).val());
  $( '#txt-fe_vencimiento_convertir' ).val( dNuevaFechaVencimiento );

  $('[name="fTotalDocumento"]').val(arrParams.fTotal);

  $( '[name="iIdDocumentoCabecera"]' ).val( arrParams.iIdDocumentoCabecera );
  $( '[name="iIdDocumentoMedioPago"]' ).val( arrParams.iIdDocumentoMedioPago );
  $( '#hidden-facturar_oden_lavanderia-iEstadoLavadoRecepcionCliente' ).val( arrParams.iEstadoLavadoRecepcionCliente );

  $( '#modal-header-facturar_orden_lavanderia-title' ).text(arrParams.sTipoDocumento + ' - ' + arrParams.sSerieDocumento + ' - ' + arrParams.sNumeroDocumento);
  $('#facturar_orden_lavanderia-modal-body-cliente').text('Cliente: ' + arrParams.sNumeroDocumentoIdentidadCliente + ' - ' + arrParams.sCliente);

  $('[name="AID"]').val(arrParams.iIdEntidadCliente);

  $('#modal-cbo-tipo_documento').val(4);//2=boleta
  if (arrParams.iIdTipoDocumentoIdentidadCliente==4)//4=RUC
    $('#modal-cbo-tipo_documento').val(3);//3=factura

  $('#cbo-modal-TiposDocumentoIdentidad').val(arrParams.iIdTipoDocumentoIdentidadCliente);

  $('[name="Nu_Documento_Identidad"]').val(arrParams.sNumeroDocumentoIdentidadCliente);
  $('[name="No_Entidad"]').val(arrParams.sCliente);
  $('[name="Txt_Email_Entidad"]').val(arrParams.sEmailCliente);

  $('[name="Nu_Celular_Entidad"]').val(arrParams.Nu_Celular_Entidad);
  
  $('#hidden-nu_numero_documento_identidad').val(arrParams.sNumeroDocumentoIdentidadCliente);
  $('#hidden-estado_entidad').val(arrParams.iEstadoCliente);
}

// Funciones para LAE API
function api_sunat_reniec(n) {
  var iIdTipoDocumento = $('#cbo-modal-TiposDocumentoIdentidad').val();
  var iCantidadCaracteresIngresados = n.length;
  var iNumeroDocumentoIdentidad = parseFloat($('#txt-ACodigo').val());

  if (iIdTipoDocumento == 2) {//2=DNI
    getDatosxDNI(iIdTipoDocumento, iCantidadCaracteresIngresados, 8, iNumeroDocumentoIdentidad);
  } else if (iIdTipoDocumento == 4) {//4=RUC
    getDatosxRUC(iIdTipoDocumento, iCantidadCaracteresIngresados, 11, iNumeroDocumentoIdentidad);
  }
}

function getDatosxDNI(iIdTipoDocumento, iCantidadCaracteresIngresados, iCantidadCaracteres, iNumeroDocumentoIdentidad) {
  if (iCantidadCaracteresIngresados == iCantidadCaracteres && isNaN(iNumeroDocumentoIdentidad) == false && $('#txt-ACodigo').val() != $('#hidden-nu_numero_documento_identidad').val()) {//Si cumple con los caracteres para DNI / RUC
    var arrPost = {
      sNumeroDocumentoIdentidad: $('#txt-ACodigo').val(),
    };
    $('#span-no_nombres_cargando').html('<i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>');

    $('#txt-ACodigo').closest('.form-group').find('.help-block').html('');
    $('#txt-ACodigo').closest('.form-group').removeClass('has-error');
    // Consulta Cliente en BD local
    $.post(base_url + 'AutocompleteController/getClienteEspecifico', arrPost, function (response) {
      $('[name="AID"]').val('');
      $('#txt-ANombre').val('');
      $('#label-txt_estado_cliente').text('');

      $('#txt-Nu_Celular_Entidad_Cliente').val('');
      $('#txt-Txt_Email_Entidad_Cliente').val('');
      $('#span-celular').hide();
      $('#span-email').hide();

      if (response.sStatus == 'success') {
        $('#span-no_nombres_cargando').html('');
        $('#hidden-nu_numero_documento_identidad').val($('#txt-ACodigo').val());

        var arrData = response.arrData;

        $('[name="AID"]').val(arrData[0].ID);
        $('[name="No_Entidad"]').val(arrData[0].Nombre);
        $('[name="Txt_Direccion_Entidad"]').val(arrData[0].Txt_Direccion_Entidad);

        $('#txt-ANombre').val(arrData[0].Nombre);
        $('#label-txt_estado_cliente').text('Existe en B.D. local');

        $('#hidden-estado_entidad').val(arrData[0].Nu_Estado);

        $('#cbo-Estado-modal').html('');
        for (var i = 0; i < 2; i++) {
          selected = '';
          if (arrData[0].Nu_Estado == i)
            selected = 'selected="selected"';
          $('#cbo-Estado-modal').append('<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Inactivo' : 'Activo') + '</option>');
        }

        $('#txt-ACodigo').closest('.form-group').find('.help-block').html('');
        $('#txt-ACodigo').closest('.form-group').removeClass('has-error');
      } else if (response.sStatus == 'warning') {// Si no existe en nuestra BD local, consultamos en el LAE API V1
        // Consulta LAE API V1 - RENIEC / SUNAT
        $('[name="AID"]').val('');
        $('#txt-ANombre').val('');
        $('#txt-Txt_Direccion_Entidad').val('');

        var url_api = 'https://www.laesystems.com/librerias/reniec/partner/format/json/x-api-key/';
        url_api = url_api + sTokenGlobal;
        var data = {
          ID_Tipo_Documento_Identidad: 4,
          Nu_Documento_Identidad: $('#txt-ACodigo').val(),
        };
        $.ajax({
          url: url_api,
          type: 'POST',
          data: data,
          success: function (response) {
            $('#span-no_nombres_cargando').html('');
            $('#hidden-nu_numero_documento_identidad').val($('#txt-ACodigo').val());

            if (response.success == true) {
              $('#txt-ACodigo').closest('.form-group').find('.help-block').html('');
              $('#txt-ACodigo').closest('.form-group').removeClass('has-error');

              $('[name="No_Entidad"]').val(response.data.No_Names);
              $('#txt-ANombre').val(response.data.No_Names);

              $('[name="Txt_Direccion_Entidad"]').val(response.data.Txt_Address);

              $('#label-txt_estado_cliente').text('Nube');

              $('#hidden-estado_entidad').val(response.data.Nu_Status);

              $('#cbo-Estado-modal').html('');
              for (var i = 0; i < 2; i++) {
                selected = '';
                if (response.data.Nu_Status == i)
                  selected = 'selected="selected"';
                $('#cbo-Estado-modal').append('<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Inactivo' : 'Activo') + '</option>');
              }
            } else {
              $('[name="No_Entidad"]').val('');
              $('[name="Txt_Direccion_Entidad"]').val('');
              $('[name="Txt_Direccion_Delivery"]').val('');

              $('#txt-ACodigo').closest('.form-group').find('.help-block').html('DNI inválido');
              $('#txt-ACodigo').closest('.form-group').removeClass('has-success').addClass('has-error');

              $('#txt-ACodigo').focus();
              $('#txt-ACodigo').select();

              $('#txt-ANombre').val('');
              $('#label-txt_estado_cliente').text('');

              $('#hidden-estado_entidad').val(0);

              $('#cbo-Estado-modal').html('');
              for (var i = 0; i < 2; i++) {
                selected = '';
                if (0 == i)
                  selected = 'selected="selected"';
                $('#cbo-Estado-modal').append('<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Inactivo' : 'Activo') + '</option>');
              }
            }
          },
          error: function (response) {
            $('#hidden-nu_numero_documento_identidad').val($('#txt-ACodigo').val());

            $('#txt-ACodigo').closest('.form-group').find('.help-block').html('Sin acceso');
            $('#txt-ACodigo').closest('.form-group').removeClass('has-success').addClass('has-error');

            $('[name="No_Entidad"]').val('');
            $('[name="Txt_Direccion_Entidad"]').val('');
            $('[name="Txt_Direccion_Delivery"]').val('');

            $('#span-no_nombres_cargando').html('');
            $('#txt-ANombre').val('');
            $('#label-txt_estado_cliente').text('');

            $('#hidden-estado_entidad').val(0);

            $('#cbo-Estado-modal').html('');
            for (var i = 0; i < 2; i++) {
              selected = '';
              if (0 == i)
                selected = 'selected="selected"';
              $('#cbo-Estado-modal').append('<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Inactivo' : 'Activo') + '</option>');
            }
          }
        });
        // ./ Consulta LAE API V1 - RENIEC / SUNAT
      } else {
        $('#label-txt_estado_cliente').text(response.sMessage);
      }
    }, 'JSON')
    // ./ Consulta de Cliente en BD local
  } else {
    $('#hidden-estado_entidad').val(1);
    $('#cbo-Estado-modal').html('<option value="1">Activo</option>');
    $('#cbo-Estado-modal').append('<option value="0">Inactivo</option>');

    $('#txt-ACodigo').closest('.form-group').find('.help-block').html('');
    $('#txt-ACodigo').closest('.form-group').removeClass('has-error');
  }
}

function getDatosxRUC(iIdTipoDocumento, iCantidadCaracteresIngresados, iCantidadCaracteres, iNumeroDocumentoIdentidad) {
  if (iCantidadCaracteresIngresados == iCantidadCaracteres && isNaN(iNumeroDocumentoIdentidad) == false && $('#txt-ACodigo').val() != $('#hidden-nu_numero_documento_identidad').val()) {//Si cumple con los caracteres para DNI / RUC
    var arrPost = {
      sNumeroDocumentoIdentidad: $('#txt-ACodigo').val(),
    };
    $('#span-no_nombres_cargando').html('<i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>');

    $('#txt-ACodigo').closest('.form-group').find('.help-block').html('');
    $('#txt-ACodigo').closest('.form-group').removeClass('has-error');
    // Consulta Cliente en BD local
    $.post(base_url + 'AutocompleteController/getClienteEspecifico', arrPost, function (response) {
      $('[name="AID"]').val('');
      $('#txt-ANombre').val('');
      $('#label-txt_estado_cliente').text('');

      $('#txt-Nu_Celular_Entidad_Cliente').val('');
      $('#txt-Txt_Email_Entidad_Cliente').val('');
      $('#span-celular').hide();
      $('#span-email').hide();

      if (response.sStatus == 'success') {
        $('#span-no_nombres_cargando').html('');
        $('#hidden-nu_numero_documento_identidad').val($('#txt-ACodigo').val());

        var arrData = response.arrData;

        $('[name="AID"]').val(arrData[0].ID);
        $('[name="No_Entidad"]').val(arrData[0].Nombre);
        $('[name="Txt_Direccion_Entidad"]').val(arrData[0].Txt_Direccion_Entidad);

        $('#txt-ANombre').val(arrData[0].Nombre);
        $('#label-txt_estado_cliente').text('Existe en B.D. local');

        $('#hidden-estado_entidad').val(arrData[0].Nu_Estado);

        $('#cbo-Estado-modal').html('');
        for (var i = 0; i < 2; i++) {
          selected = '';
          if (arrData[0].Nu_Estado == i)
            selected = 'selected="selected"';
          $('#cbo-Estado-modal').append('<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Inactivo' : 'Activo') + '</option>');
        }

        $('#txt-ACodigo').closest('.form-group').find('.help-block').html('');
        $('#txt-ACodigo').closest('.form-group').removeClass('has-error');
      } else if (response.sStatus == 'warning') {// Si no existe en nuestra BD local, consultamos en el LAE API V1
        // Consulta LAE API V1 - RENIEC / SUNAT
        $('[name="AID"]').val('');
        $('#txt-ANombre').val('');
        $('#txt-Txt_Direccion_Entidad').val('');

        var url_api = 'https://www.laesystems.com/librerias/sunat/partner/format/json/x-api-key/';
        url_api = url_api + sTokenGlobal;
        var data = {
          ID_Tipo_Documento_Identidad: 4,
          Nu_Documento_Identidad: $('#txt-ACodigo').val(),
        };
        $.ajax({
          url: url_api,
          type: 'POST',
          data: data,
          success: function (response) {
            $('#span-no_nombres_cargando').html('');
            $('#hidden-nu_numero_documento_identidad').val($('#txt-ACodigo').val());

            if (response.success == true) {
              $('#txt-ACodigo').closest('.form-group').find('.help-block').html('');
              $('#txt-ACodigo').closest('.form-group').removeClass('has-error');

              $('[name="No_Entidad"]').val(response.data.No_Names);
              $('#txt-ANombre').val(response.data.No_Names);

              $('[name="Txt_Direccion_Entidad"]').val(response.data.Txt_Address);

              $('#label-txt_estado_cliente').text('Nube');

              $('#hidden-estado_entidad').val(response.data.Nu_Status);

              $('#cbo-Estado-modal').html('');
              for (var i = 0; i < 2; i++) {
                selected = '';
                if (response.data.Nu_Status == i)
                  selected = 'selected="selected"';
                $('#cbo-Estado-modal').append('<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Inactivo' : 'Activo') + '</option>');
              }

              if (response.data.Nu_Status == 0) {
                $('#modal-message').modal('show');
                $('.modal-message').removeClass('modal-danger modal-warning modal-success');
                $('.modal-message').addClass('modal-danger');
                $('.modal-title-message').text('El cliente se encuentra con BAJA DE OFICIO / NO HABIDO');
                setTimeout(function () { $('#modal-message').modal('hide'); }, 2500);
              }
            } else {
              $('[name="No_Entidad"]').val('');
              $('[name="Txt_Direccion_Entidad"]').val('');
              $('[name="Txt_Direccion_Delivery"]').val('');

              $('#txt-ACodigo').closest('.form-group').find('.help-block').html('RUC inválido');
              $('#txt-ACodigo').closest('.form-group').removeClass('has-success').addClass('has-error');

              $('#modal-message').modal('show');
              $('.modal-message').removeClass('modal-danger modal-warning modal-success');
              $('.modal-message').addClass('modal-danger');
              $('.modal-title-message').text('El cliente se encuentra con BAJA DE OFICIO / NO HABIDO');
              setTimeout(function () { $('#modal-message').modal('hide'); }, 2500);

              $('#txt-ACodigo').focus();
              $('#txt-ACodigo').select();

              $('#txt-ANombre').val('');
              $('#label-txt_estado_cliente').text('');

              $('#hidden-estado_entidad').val(0);

              $('#cbo-Estado-modal').html('');
              for (var i = 0; i < 2; i++) {
                selected = '';
                if (0 == i)
                  selected = 'selected="selected"';
                $('#cbo-Estado-modal').append('<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Inactivo' : 'Activo') + '</option>');
              }
            }
          },
          error: function (response) {
            $('#hidden-nu_numero_documento_identidad').val($('#txt-ACodigo').val());

            $('#txt-ACodigo').closest('.form-group').find('.help-block').html('Sin acceso');
            $('#txt-ACodigo').closest('.form-group').removeClass('has-success').addClass('has-error');

            $('[name="No_Entidad"]').val('');
            $('[name="Txt_Direccion_Entidad"]').val('');
            $('[name="Txt_Direccion_Delivery"]').val('');

            $('#span-no_nombres_cargando').html('');
            $('#txt-ANombre').val('');
            $('#label-txt_estado_cliente').text('');

            $('#hidden-estado_entidad').val(0);

            $('#cbo-Estado-modal').html('');
            for (var i = 0; i < 2; i++) {
              selected = '';
              if (0 == i)
                selected = 'selected="selected"';
              $('#cbo-Estado-modal').append('<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Inactivo' : 'Activo') + '</option>');
            }
          }
        });
        return;
        // ./ Consulta LAE API V1 - RENIEC / SUNAT
      } else {
        $('#txt-ACodigo').closest('.form-group').find('.help-block').html(response.sMessage);
        $('#txt-ACodigo').closest('.form-group').removeClass('has-success').addClass('has-error');
      }
    }, 'JSON')
    // ./ Consulta de Cliente en BD local
  }
}

function checkAllMenuHeader() {
  if ($('#check-AllMenuHeader').prop('checked')) {
    $('.check-iIdDocumentoCabecera').prop('checked', true);
    $('#check-AllMenuFooter').prop('checked', true);
  } else {
    if (false == $('#check-AllMenuHeader').prop('checked')) {
      $('.check-iIdDocumentoCabecera').prop('checked', false);
      $('#check-AllMenuFooter').prop('checked', false);
    }
  }
}

function checkAllMenuFooter() {
  if ($('#check-AllMenuFooter').prop('checked')) {
    $('.check-iIdDocumentoCabecera').prop('checked', true);
    $('#check-AllMenuHeader').prop('checked', true);
  } else {
    if (false == $('#check-AllMenuFooter').prop('checked')) {
      $('.check-iIdDocumentoCabecera').prop('checked', false);
      $('#check-AllMenuHeader').prop('checked', false);
    }
  }
}

function cobroMasivoVenta() {
  if ($('#form-cobro_masivo_venta').serialize() == '') {
    $('.modal-message').removeClass('modal-danger modal-warning modal-success');
    $('#modal-message').modal('show');
    $('.modal-message').addClass('modal-danger');
    $('.modal-title-message').text('Debe seleccionar al menos 1 fila');
    setTimeout(function () { $('#modal-message').modal('hide'); }, 1500);
  } else {
    var arrIdCliente = [], i = 0, iIdEmpresa = 0, sSignoMoneda = '', iIdCliente = 0, sCliente = '', fTotalSaldo = 0.00;
    $("input:checkbox:checked").each(function () {
      iIdEmpresa = $(this).data('id_empresa');
      fTotalSaldo += parseFloat($(this).data('f_total_saldo'));
      if( i == 0) {
        iIdCliente = $(this).data('id_entidad');
        sCliente = $(this).data('no_entidad');
        sSignoMoneda = $(this).data('no_signo');
      }
      ++i;
      var obj = {};
      obj.iIdCliente = $(this).data('id_entidad');
      arrIdCliente.push(obj);
    });

    for (var i = 0; i < arrIdCliente.length; i++) {
      if (arrIdCliente[i].iIdCliente != iIdCliente) {
        $('.modal-cobro_masivo_venta').modal('hide');
        alert('Debes de seleccionar al mismo cliente ' + sCliente);
        return false;
      }
    }

    $('.modal-cobro_masivo_venta').modal('show');

    $('.div-forma_pago_masivo').hide();
    $('.div-modal_datos_tarjeta_credito_masivo').hide();

    $('#cobrar_cliente-modal-body-cliente_masivo').text('Cliente: ' + sCliente);
    $('#cobrar_cliente-modal-body-saldo_cliente_masivo').text('Total Saldo: ' + sSignoMoneda + ' ' + fTotalSaldo);
    $('#hidden-cobro_masivo_venta-fsaldo').val(fTotalSaldo);

    $('.div-forma_pago_masivo').show();
    $('.modal-cobro_masivo_venta').on('shown.bs.modal', function () {
      $('[name="fPagoCliente"]').focus();
      $('[name="fPagoCliente"]').val(fTotalSaldo);
    });

    url = base_url + 'HelperController/getMediosPago';
    var arrPost = {
      iIdEmpresa: iIdEmpresa,
    };
    $.post(url, arrPost, function (response) {
      $('#cbo-modal_forma_pago_masivo').html('');
      for (var i = 0; i < response.length; i++) {
        if (response[i].Nu_Tipo != 1)
          $('#cbo-modal_forma_pago_masivo').append('<option value="' + response[i].ID_Medio_Pago + '" data-nu_tipo_medio_pago="' + response[i].Nu_Tipo + '">' + response[i].No_Medio_Pago + '</option>');
      }
    }, 'JSON');

    // Modal de cobranza al cliente
    $('#cbo-modal_forma_pago_masivo').change(function () {
      ID_Medio_Pago = $(this).val();
      Nu_Tipo_Medio_Pago = $(this).find(':selected').data('nu_tipo_medio_pago');
      $('.div-modal_datos_tarjeta_credito_masivo').hide();
      $('#cbo-cobrar_cliente-modal_tarjeta_credito_masivo').html('');
      $('#tel-nu_referencia').val('');
      $('#tel-nu_ultimo_4_digitos_tarjeta').val('');
      if (Nu_Tipo_Medio_Pago == 2) {
        $('.div-modal_datos_tarjeta_credito_masivo').show();

        url = base_url + 'HelperController/getTiposTarjetaCredito';
        $.post(url, { ID_Medio_Pago: ID_Medio_Pago }, function (response) {
          $('#cbo-cobrar_cliente-modal_tarjeta_credito_masivo').html('');
          for (var i = 0; i < response.length; i++)
            $('#cbo-cobrar_cliente-modal_tarjeta_credito_masivo').append('<option value="' + response[i].ID_Tipo_Medio_Pago + '">' + response[i].No_Tipo_Medio_Pago + '</option>');
        }, 'JSON');
      }

      setTimeout(function () { $('[name="fPagoCliente"]').focus(); $('[name="fPagoCliente"]').select(); }, 20);
    })

    $('#btn-salir').off('click').click(function () {
      $('.modal-cobro_masivo_venta').modal('hide');
    });
  } // if - else validacion de checkbox
}

function modificarVenta(ID_Documento_Cabecera, sDocumento, Nu_Tipo_Recepcion, Fe_Entrega, ID_Transporte_Delivery, ID_Entidad, Fe_Emision, ID_Tipo_Documento, Fe_Vencimiento, Ss_Total_Saldo, Nu_Transporte_Lavanderia_Hoy){
  $( '#form-modificar_venta' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();

  $( '.modal-modificar_venta' ).modal('show');

  $('#modal-header-modificar_venta-title').text(sDocumento);

  $('[name="ID_Documento_Cabecera-Modificar"]').val(ID_Documento_Cabecera);
  $('[name="ID_Entidad-Modificar"]').val(ID_Entidad);
  $('[name="ID_Tipo_Documento-Modificar"]').val(ID_Tipo_Documento);

  //Validaciones
  $('.div-delivery').hide();
  $('.div-recojo_tienda').hide();
  if( Nu_Tipo_Recepcion == 6 )
    $('.div-delivery').show();

  if (Nu_Tipo_Recepcion == 7)
    $('.div-recojo_tienda').show();

  var selected='';
  $('#modal-cbo-tipo_recepcion-modificar').html('- Sin registros -');
  url = base_url + 'HelperController/getTipoRecepcionNegocio';
  $.post(url, {}, function (response) {
    $('#modal-cbo-tipo_recepcion-modificar').html('');
    for (var i = 0; i < response.length; i++) {
      selected = '';
      if (response[i].Nu_Valor == Nu_Tipo_Recepcion)
        selected='selected';
      var sDescripcion = response[i].No_Descripcion.split('-');
      $('#modal-cbo-tipo_recepcion-modificar').append('<option value="' + response[i].Nu_Valor + '" ' + selected + '>' + sDescripcion[1] + '</option>');
    }
  }, 'JSON');
  
  var arrTipoEnvioLavadoEmpresa = [{"ID":"1","Valor":"Transporte"},{"ID":"2","Valor":"Servicio Externo"},{"ID":"3","Valor":"Servicio Interno"},{"ID":"4","Valor":"Empresa"}];
  $('#modal-cbo-tipo_envio_lavado-modificar').html('- Sin registros -');
  console.log(arrTipoEnvioLavadoEmpresa);
  for (var i = 0; i < arrTipoEnvioLavadoEmpresa.length; i++) {
    selected = '';
    if (arrTipoEnvioLavadoEmpresa[i].ID == Nu_Transporte_Lavanderia_Hoy)
      selected='selected';
    $('#modal-cbo-tipo_envio_lavado-modificar').append('<option value="' + arrTipoEnvioLavadoEmpresa[i].ID + '" ' + selected + '>' + arrTipoEnvioLavadoEmpresa[i].Valor + '</option>');
  }

  $('#modal-cbo-tipo_recepcion-modificar').change(function () {
    $('.div-delivery').hide();
    $('.div-recojo_tienda').hide();
    if ($(this).val() == 6)
      $('.div-delivery').show();

    if ($(this).val() == 7)
      $('.div-recojo_tienda').show();
  })

  $('#txt-fe_entrega').val(ParseDateString(Fe_Entrega, 6, '-'));

  $('#txt-fe_vencimiento').val(ParseDateString(Fe_Vencimiento, 6, '-'));
  $('.div-credito').hide();
  if (parseFloat(Ss_Total_Saldo) > 0.00)
    $('.div-credito').show();

  $('.div-interno').hide();
  if (ID_Tipo_Documento == 2)
    $('.div-interno').show();
  $('#txt-fe_emision_interno').val(ParseDateString(Fe_Emision, 6, '-'));
  
  $('#cbo-transporte').html('<option value="0" selected="selected">- Sin registro -</option>');
  url = base_url + 'HelperController/getDeliveryVentas';
  var arrPost = {};
  $.post(url, arrPost, function (response) {
    if (response.sStatus == 'success') {
      var l = response.arrData.length;
      if (l == 1) {
        $('#cbo-transporte').html('<option value="' + response.arrData[0].ID + '">' + response.arrData[0].Nombre + '</option>');
      } else {
        $('#cbo-transporte').html('<option value="0" selected="selected">- Seleccionar -</option>');
        for (var x = 0; x < l; x++) {
          selected = '';
          if (response.arrData[x].ID == ID_Transporte_Delivery)
            selected = 'selected';
          $('#cbo-transporte').append('<option value="' + response.arrData[x].ID + '" ' + selected + '>' + response.arrData[x].Nombre + '</option>');
        }
      }
    } else {
      if (response.sMessageSQL !== undefined) {
        console.log(response.sMessageSQL);
      }
    }
  }, 'JSON');

  url = base_url + 'PuntoVenta/VentaPuntoVentaController/getDocumentoVenta';
  $.post(url, { ID: ID_Documento_Cabecera }, function (response) {
    console.log(response);
    $('[name="Txt_Direccion_Delivery"]').val(response.Txt_Direccion_Entidad);
    $('[name="Txt_Glosa"]').val(response.Txt_Glosa);
  }, 'JSON');
}

function anularFacturaVenta(ID, Nu_Enlace, Nu_Descargar_Inventario, accion, iEstado, sTipoBajaSunat, dEmision, sSerieDocumento) {
  var $modal_delete = $('.modal-message-delete');
  $modal_delete.modal('show');

  $('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
  $('.modal-message-delete').addClass('modal-warning');

  $('.modal-title-message-delete').text('¿Deseas anular el documento?');

  $('#btn-cancel-delete').off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $(document).bind('keydown', 'alt+k', function () {
    if (accion == 'anular') {
      _anularFacturaVenta($modal_delete, ID, Nu_Enlace, Nu_Descargar_Inventario, iEstado, sTipoBajaSunat, dEmision, sSerieDocumento);
      accion = '';
    }
  });

  $('#btn-save-delete').off('click').click(function () {
    _anularFacturaVenta($modal_delete, ID, Nu_Enlace, Nu_Descargar_Inventario, iEstado, sTipoBajaSunat, dEmision, sSerieDocumento);
  });
}

function _anularFacturaVenta($modal_delete, ID, Nu_Enlace, Nu_Descargar_Inventario, iEstado, sTipoBajaSunat, dEmision, sSerieDocumento) {
  $('#modal-loader').modal('show');

  url = base_url + 'Ventas/VentaController/anularVenta/' + ID + '/' + Nu_Enlace + '/' + Nu_Descargar_Inventario + '/' + iEstado + '/' + sTipoBajaSunat + '/' + dEmision + '/' + sSerieDocumento;
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
      } else {
        $('.modal-message').addClass(response.style_modal);
        $('.modal-title-message').text(response.message);
        setTimeout(function () { $('#modal-message').modal('hide'); }, 3100);
      }
      getReporteHTML();
      accion = '';
    },
    error: function (jqXHR, textStatus, errorThrown) {
      accion = '';
      $('#modal-loader').modal('hide');
      $modal_delete.modal('hide');
      $('.modal-message').removeClass('modal-danger modal-warning modal-success');

      $('#modal-message').modal('show');
      $('.modal-message').addClass('modal-danger');
      $('.modal-title-message').text(textStatus + ' [' + jqXHR.status + ']: ' + errorThrown);
      setTimeout(function () { $('#modal-message').modal('hide'); }, 1700);

      //Message for developer
      console.log(jqXHR.responseText);
    },
  });
}

function eliminarFacturaVenta(ID, Nu_Enlace, Nu_Descargar_Inventario, accion) {
  var $modal_delete = $('#modal-message-delete');
  $modal_delete.modal('show');

  $('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
  $('.modal-message-delete').addClass('modal-danger');

  $('.modal-title-message-delete').text('¿Deseas eliminar el documento?');

  $('#btn-cancel-delete').off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $(document).bind('keydown', 'alt+l', function () {
    if (accion == 'delete') {
      _eliminarFacturaVenta($modal_delete, ID, Nu_Enlace, Nu_Descargar_Inventario);
      accion = '';
    }
  });

  $('#btn-save-delete').off('click').click(function () {
    _eliminarFacturaVenta($modal_delete, ID, Nu_Enlace, Nu_Descargar_Inventario);
  });
}

function _eliminarFacturaVenta($modal_delete, ID, Nu_Enlace, Nu_Descargar_Inventario) {
  $('#modal-loader').modal('show');

  url = base_url + 'Ventas/VentaController/eliminarVenta/' + ID + '/' + Nu_Enlace + '/' + Nu_Descargar_Inventario;
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
      accion = '';
    },
    error: function (jqXHR, textStatus, errorThrown) {
      accion = '';
      $('#modal-loader').modal('hide');
      $modal_delete.modal('hide');
      $('.modal-message').removeClass('modal-danger modal-warning modal-success');

      $('#modal-message').modal('show');
      $('.modal-message').addClass('modal-danger');
      $('.modal-title-message').text(textStatus + ' [' + jqXHR.status + ']: ' + errorThrown);
      setTimeout(function () { $('#modal-message').modal('hide'); }, 1700);

      //Message for developer
      console.log(jqXHR.responseText);
    },
  });
}

function consultarDocumentoElectronicoSunat(ID, iEstado) {
  $('#btn-sunat-cdr-' + ID).attr('disabled', true);
  $('#span-sunat-cdr-' + ID).append('<i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>');

  var $modal_id = $('#modal-correo_sunat');

  var sendPost = {
    ID: ID,
    iEstado: iEstado,
    sTipoRespuesta: 'json',
  };

  url = base_url + 'DocumentoElectronicoController/consultarDocumentoElectronicoSunat';
  $.ajax({
    url: url,
    type: "POST",
    dataType: "JSON",
    data: sendPost,
    success: function (response) {
      $modal_id.modal('hide');

      $('.modal-message').removeClass('modal-danger modal-warning modal-success');
      $('#modal-message').modal('show');

      $('#btn-sunat-cdr-' + ID).attr('disabled', false);

      $('#span-sunat-cdr-' + ID).html('');

      if (response.status == 'success') {
        $('#txt-email_correo_sunat').val('');

        $('.modal-message').addClass(response.style_modal);
        $('.modal-title-message').text(response.message);
        setTimeout(function () { $('#modal-message').modal('hide'); }, 1200);
      } else {
        $('.modal-message').addClass(response.style_modal);
        $('.modal-title-message').text(response.message);
        setTimeout(function () { $('#modal-message').modal('hide'); }, 1800);
      }
      getReporteHTML();
    }
  });
}

//Correo

function sendCorreoFacturaVentaSUNAT(ID, iIdCliente) {
  var $modal_id = $('#modal-correo_sunat');
  $modal_id.modal('show');

  $('#modal-correo_sunat').removeClass('modal-danger modal-warning modal-success');
  $('#modal-correo_sunat').addClass('modal-success');

  $('.modal-header_message_correo_sunat').text('¿Enviar correo?');

  // get cliente
  $('#txt-email_correo_sunat').val('');
  url = base_url + 'HelperController/getDataGeneral';
  $.post(url, { sTipoData: 'get_entidad', iTipoEntidad: 0, iIDEntidad: iIdCliente }, function (response) {
    if (response.sStatus == 'success') {
      $('#txt-email_correo_sunat').val(response.arrData[0].Txt_Email_Entidad);
    } else {
      if (response.sMessageSQL !== undefined) {
        console.log(response.sMessageSQL);
      }
      console.log(response.sMessage);
    }
  }, 'JSON');
  // /. get cliente

  $('#modal-correo_sunat').on('shown.bs.modal', function () {
    $('#txt-email_correo_sunat').focus();
  })

  $('#btn-modal_message_correo_sunat-cancel').off('click').click(function () {
    $modal_id.modal('hide');
  });

  $("#txt-email_correo_sunat").blur(function () {
    caracteresCorreoValido($(this).val(), '#span-email');
  })

  $('#btn-modal_message_correo_sunat-send').off('click').click(function () {
    if (!caracteresCorreoValido($('#txt-email_correo_sunat').val(), '#div-email')) {
      scrollToError($('#modal-correo_sunat .modal-body'), $('#txt-email_correo_sunat'));
    } else {
      $('#btn-modal_message_correo_sunat-send').text('');
      $('#btn-modal_message_correo_sunat-send').attr('disabled', true);
      $('#btn-modal_message_correo_sunat-send').append('Enviando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>');

      var sendPost = {
        ID: ID,
        Txt_Email: $('#txt-email_correo_sunat').val(),
      };

      url = base_url + 'Ventas/VentaController/sendCorreoFacturaVentaSUNAT/';
      $.ajax({
        url: url,
        type: "POST",
        dataType: "JSON",
        data: sendPost,
        success: function (response) {
          $modal_id.modal('hide');

          $('.modal-message').removeClass('modal-danger modal-warning modal-success');
          $('#modal-message').modal('show');

          if (response.status == 'success') {
            $('#txt-email_correo_sunat').val('');

            $('.modal-message').addClass(response.style_modal);
            $('.modal-title-message').text(response.message);
            setTimeout(function () { $('#modal-message').modal('hide'); }, 1200);
          } else {
            $('.modal-message').addClass(response.style_modal);
            $('.modal-title-message').text(response.message);
            setTimeout(function () { $('#modal-message').modal('hide'); }, 1800);
          }

          $('#btn-modal_message_correo_sunat-send').text('');
          $('#btn-modal_message_correo_sunat-send').attr('disabled', false);
          $('#btn-modal_message_correo_sunat-send').append('Enviar');
        }
      });
    }
  });
}

//WhatsApp
function sendWhatsapp(ID, iIdCliente) {
  var $modal_id = $('#modal-whatsApp');
  $modal_id.modal('show');

  $('#modal-whatsApp').removeClass('modal-danger modal-warning modal-success');
  $('#modal-whatsApp').addClass('modal-success');

  $('.modal-header_message_whatsApp').text('¿Enviar WhatsApp?');

  // get cliente
  $('#txt-Nu_Celular_Entidad_Cliente_WhatsApp').val('');
  url = base_url + 'HelperController/getDataGeneral';
  $.post(url, { sTipoData: 'get_entidad', iTipoEntidad: 0, iIDEntidad: iIdCliente }, function (response) {
    if (response.sStatus == 'success') {
      $('#txt-Nu_Celular_Entidad_Cliente_WhatsApp').val(response.arrData[0].Nu_Celular_Entidad);
    } else {
      if (response.sMessageSQL !== undefined) {
        console.log(response.sMessageSQL);
      }
      console.log(response.sMessage);
    }
  }, 'JSON');
  // /. get cliente

  $('#modal-whatsApp').on('shown.bs.modal', function () {
    $('#txt-Nu_Celular_Entidad_Cliente_WhatsApp').focus();
  })

  $('#btn-modal_message_whatsApp-cancel').off('click').click(function () {
    $modal_id.modal('hide');
  });

  $('#btn-modal_message_whatsApp-send').off('click').click(function () {
    if (String(parseInt($('#txt-Nu_Celular_Entidad_Cliente_WhatsApp').val().replace(/ /g, ""))).length < 9) {
      $('#txt-Nu_Celular_Entidad_Cliente_WhatsApp').closest('.form-group').find('.help-block').html('Debes ingresar 9 dígitos');
      $('#txt-Nu_Celular_Entidad_Cliente_WhatsApp').closest('.form-group').removeClass('has-success').addClass('has-error');

      scrollToError($("html, body"), $('#txt-Nu_Celular_Entidad_Cliente_WhatsApp'));
      return false;
    } else {
      $('#btn-modal_message_whatsApp-send').text('');
      $('#btn-modal_message_whatsApp-send').attr('disabled', true);
      $('#btn-modal_message_whatsApp-send').append('Enviando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>');

      var sendPost = {
        ID: ID,
      };

      url = base_url + 'HelperController/getDatosDocumentoVentaWhatsApp/';
      $.ajax({
        url: url,
        type: "POST",
        dataType: "JSON",
        data: sendPost,
        success: function (response) {
          console.log(response);

          $modal_id.modal('hide');

          $('.modal-message').removeClass('modal-danger modal-warning modal-success');
          $('#modal-message').modal('show');

          if (response.sStatus == 'success') {
            //Envío por whatsApp
            var sNumeroPeru = $('#txt-Nu_Celular_Entidad_Cliente_WhatsApp').val().replace(/ /g, "");
            var sDocumento = response.arrData[0].No_Tipo_Documento + (response.arrData[0].ID_Tipo_Documento != '2' ? ' Electrónica' : '') + ' - ' + response.arrData[0].ID_Serie_Documento + ' - ' + response.arrData[0].ID_Numero_Documento;

            url = 'https://api.whatsapp.com/send?phone=51' + sNumeroPeru + '&text=';
            url += 'Somos *' + (response.arrData[0].No_Empresa_Comercial != '' ? caracteresValidosWhatsApp(response.arrData[0].No_Empresa_Comercial) : caracteresValidosWhatsApp(response.arrData[0].No_Empresa)) + '*,\n';

            url += '\n*Nombre:* ' + caracteresValidosWhatsApp(response.arrData[0].No_Entidad);
            url += '\n*' + response.arrData[0].No_Tipo_Documento_Identidad_Breve + ':* ' + response.arrData[0].Nu_Documento_Identidad;
            url += '\n*Documento:* ' + sDocumento;
            url += '\n*Fecha de Emisión:* ' + ParseDate(response.arrData[0].Fecha_Emision);

            var iTotalRegistros = response.arrData.length;
            var responseDetalle = response.arrData;

            url += '\n\n*Detalle de Pedido*\n';
            url += '=============\n';
            for (var i = 0; i < iTotalRegistros; i++) {
              url += '✅ ' + number_format(responseDetalle[i].Qt_Producto, 2) + ' x *' + caracteresValidosWhatsApp(responseDetalle[i].sNombreItem.trim()) + '* - ' + responseDetalle[i].No_Signo + ' ' + number_format(responseDetalle[i].Ss_Precio, 2) + '\n';
            }
            if (response.arrData[0].Nu_Tipo_Recepcion != '5') {
              url += '\n*Recepción:* ' + response.arrData[0].No_Tipo_Recepcion + '\n';
              if (response.arrData[0].Nu_Tipo_Recepcion == 6 && response.arrData[0].Txt_Direccion_Entidad != '')
                url += '*Dirección:* ' + response.arrData[0].Txt_Direccion_Entidad + '\n';
            }
            url += '\n➡️ *Total:* ' + response.arrData[0].No_Signo + ' ' + number_format(response.arrData[0].Total, 2) + '\n';
            //Saldo
            if (parseFloat(response.arrData[0].Total_Saldo) > 0.00) {
              url += '➡️ *Fecha de Vencimiento:* ' + ParseDate(response.arrData[0].Fecha_Vencimiento);
              url += '\n➡️ Tiene un *saldo pendiente por pagar de ' + response.arrData[0].No_Signo + ' ' + number_format(response.arrData[0].Total_Saldo, 2) + '*\n';
            }

            if (response.arrData[0].enlace_del_pdf !== undefined && response.arrData[0].enlace_del_pdf != null) {
              url += '\nDescarga tu *PDF electrónico* en el siguiente enlace:\n' + response.arrData[0].enlace_del_pdf;
            }

            url += (response.arrData[0].sTerminosCondicionesTicket != '' && response.arrData[0].sTerminosCondicionesTicket != null ? '\n\n' + response.arrData[0].sTerminosCondicionesTicket.replace(/<br \/>/g, "") : '');
            url += '\n\nGenerado por laesystems.com';
            
            url = encodeURI(url);
            window.open(url, '_blank');
            window.close();

            $('#modal-message').modal('hide');
          } else {
            $('.modal-message').addClass('modal-' + response.sStatus);
            $('.modal-title-message').text(response.sMessage);
            setTimeout(function () { $('#modal-message').modal('hide'); }, 1800);
          }

          $('#btn-modal_message_whatsApp-send').text('');
          $('#btn-modal_message_whatsApp-send').attr('disabled', false);
          $('#btn-modal_message_whatsApp-send').append('Enviar');
        }
      });
    }
  });
}