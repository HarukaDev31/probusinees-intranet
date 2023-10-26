var url;

$(function () {  
  $( '#modal-loader' ).modal('show');

  $( '#check-AllMenuHeader' ).prop('checked', false);
  $( '#check-AllMenuFooter' ).prop('checked', false);

	url = base_url + 'HelperController/getDataGeneral';
	var arrPost = {
		sTipoData : 'entidad',
		iTipoEntidad : '6',
	};
	$.post( url, arrPost, function( response ){
    $( '#cbo-transporte' ).html('<option value="0" selected="selected">- Vacío -</option>');
		if ( response.sStatus == 'success' ) {
			var l = response.arrData.length;
			if (l==1) {
				$( '#cbo-transporte' ).html( '<option value="' + response.arrData[0].ID + '">' + response.arrData[0].Nombre + '</option>' );
			} else {
				$( '#cbo-transporte' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
				for (var x = 0; x < l; x++) {
					$( '#cbo-transporte' ).append( '<option value="' + response.arrData[x].ID + '">' + response.arrData[x].Nombre + '</option>' );
				}
			}
		} else {
			if( response.sMessageSQL !== undefined ) {
				console.log(response.sMessageSQL);
			}
			console.log(response.sMessage);
		}
  }, 'JSON');
  
  $( '#div-estado_lavado' ).hide();
  
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
  
  $( '.btn-estado_lavado' ).click(function(){
    if ( $( '#txt-Filtro_Entidad' ).val().length > 0 && $( '#txt-AID' ).val().length === 0 ) {
      $( '#txt-Filtro_Entidad' ).closest('.form-group').find('.help-block').html('Seleccionar cliente');
		  $( '#txt-Filtro_Entidad' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      $( '.help-block' ).empty();
    
      var Fe_Inicio, Fe_Fin, iIdTipoDocumento, iIdSerieDocumento, iNumeroDocumento, iEstado, iIdCliente, sNombreCliente, iTipoRecepcionCliente, iEstadoLavado, Nu_Transporte_Lavanderia_Hoy;
      
      Fe_Inicio = ParseDateString($( '#txt-Filtro_Fe_Inicio' ).val(), 1, '/');
      Fe_Fin = ParseDateString($( '#txt-Filtro_Fe_Fin' ).val(), 1, '/');
      iIdTipoDocumento = $( '#cbo-filtros_tipos_documento' ).val();
      iIdSerieDocumento = $( '#cbo-filtros_series_documento' ).val();
      iNumeroDocumento = ($( '#txt-Filtro_NumeroDocumento' ).val().length === 0 ? '-' : $( '#txt-Filtro_NumeroDocumento' ).val());
      iEstado = $( '#cbo-estado_documento' ).val();
      iIdCliente = ($( '#txt-AID' ).val().length === 0 ? '-' : $( '#txt-AID' ).val());
      sNombreCliente = ($( '#txt-Filtro_Entidad' ).val().length === 0 ? '-' : $( '#txt-Filtro_Entidad' ).val());
      iTipoRecepcionCliente = $( '#cbo-tipo_recepcion_cliente' ).val();
      iEstadoLavado = $( '#cbo-estado_lavado' ).val();
      Nu_Transporte_Lavanderia_Hoy = $( '#cbo-tipo_envio_empresa' ).val();
        
      if ($(this).data('type') == 'html') {
        getReporteHTML();
      } else if ($(this).data('type') == 'pdf') {
        $( '#btn-pdf_estado_lavado' ).text('');
        $( '#btn-pdf_estado_lavado' ).attr('disabled', true);
        $( '#btn-pdf_estado_lavado' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

        url = base_url + 'Ventas/EstadoLavadoController/sendReportePDF/' + Fe_Inicio + '/' + Fe_Fin + '/' + iIdTipoDocumento + '/' + iIdSerieDocumento + '/' + iNumeroDocumento + '/' + iEstado + '/' + iIdCliente + '/' + sNombreCliente + '/' + iTipoRecepcionCliente + '/' + iEstadoLavado + '/' + Nu_Transporte_Lavanderia_Hoy;
        window.open(url,'_blank');
        
        $( '#btn-pdf_estado_lavado' ).text('');
        $( '#btn-pdf_estado_lavado' ).append( '<i class="fa fa-file-pdf-o color_icon_pdf"></i> PDF' );
        $( '#btn-pdf_estado_lavado' ).attr('disabled', false);
      } else if ($(this).data('type') == 'excel') {
        $( '#btn-excel_estado_lavado' ).text('');
        $( '#btn-excel_estado_lavado' ).attr('disabled', true);
        $( '#btn-excel_estado_lavado' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
        
        url = base_url + 'Ventas/EstadoLavadoController/sendReporteEXCEL/' + Fe_Inicio + '/' + Fe_Fin + '/' + iIdTipoDocumento + '/' + iIdSerieDocumento + '/' + iNumeroDocumento + '/' + iEstado + '/' + iIdCliente + '/' + sNombreCliente + '/' + iTipoRecepcionCliente + '/' + iEstadoLavado + '/' + Nu_Transporte_Lavanderia_Hoy;
        window.open(url,'_blank');
        
        $( '#btn-excel_estado_lavado' ).text('');
        $( '#btn-excel_estado_lavado' ).append( '<i class="fa fa-file-excel-o color_icon_excel"></i> Excel' );
        $( '#btn-excel_estado_lavado' ).attr('disabled', false);
      }// /. if all button 
    }// /. if - else validacion
  })// /. btn
  
  $( '#btn-verificar_pedido' ).click(function(){
    $( '#btn-verificar_pedido' ).text('');
    $( '#btn-verificar_pedido' ).attr('disabled', true);
    $( '#btn-verificar_pedido' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
    $( '#btn-salir' ).attr('disabled', true);

    url = base_url + 'Ventas/EstadoLavadoController/enviarAlertaPedido';
    $.ajax({
      type : 'POST',
      dataType : 'JSON',
      url : url,
      data : $('#form-verificar_pedido').serialize(),
      success : function( response ){
        $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
        $( '#modal-message' ).modal('show');

        if ( response.sStatus=='success' ) {
          $( '.modal-verificar_pedido' ).modal('hide');

          $( '.modal-message' ).addClass( 'modal-' + response.sStatus);
          $( '.modal-title-message' ).text( response.sMessage );
          setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
          
          getReporteHTML();
        } else {
          $( '.modal-message' ).addClass( 'modal-' + response.sStatus );
          $( '.modal-title-message' ).text( response.sMessage );
          setTimeout(function() {$('#modal-message').modal('hide');}, 3100);
        }
        
        $( '#btn-verificar_pedido' ).text('');
        $( '#btn-verificar_pedido' ).append( 'Enviar alerta' );
        $( '#btn-verificar_pedido' ).attr('disabled', false);
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

      $( '#btn-verificar_pedido' ).text('');
      $( '#btn-verificar_pedido' ).attr('disabled', false);
      $( '#btn-verificar_pedido' ).append( 'Enviar alerta' );
      $( '#btn-salir' ).attr('disabled', false);
    })
  })

  $( '#btn-agregar_nota_orden_lavado' ).click(function(){
    $( '#btn-agregar_nota_orden_lavado' ).text('');
    $( '#btn-agregar_nota_orden_lavado' ).attr('disabled', true);
    $( '#btn-agregar_nota_orden_lavado' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
    $( '#btn-salir' ).attr('disabled', true);

    url = base_url + 'Ventas/EstadoLavadoController/agregarNotaOrdenLavado';
    $.ajax({
      type : 'POST',
      dataType : 'JSON',
      url : url,
      data : $('#form-agregar_nota_orden_lavado').serialize(),
      success : function( response ){
        $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
        $( '#modal-message' ).modal('show');

        if ( response.sStatus=='success' ) {
          $( '.modal-agregar_nota_orden_lavado' ).modal('hide');

          $( '.modal-message' ).addClass( 'modal-' + response.sStatus);
          $( '.modal-title-message' ).text( response.sMessage );
          setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
          
          getReporteHTML();
        } else {
          $( '.modal-message' ).addClass( 'modal-' + response.sStatus );
          $( '.modal-title-message' ).text( response.sMessage );
          setTimeout(function() {$('#modal-message').modal('hide');}, 3100);
        }
        
        $( '#btn-agregar_nota_orden_lavado' ).text('');
        $( '#btn-agregar_nota_orden_lavado' ).append( 'Guardar Nota' );
        $( '#btn-agregar_nota_orden_lavado' ).attr('disabled', false);
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

      $( '#btn-agregar_nota_orden_lavado' ).text('');
      $( '#btn-agregar_nota_orden_lavado' ).attr('disabled', false);
      $( '#btn-agregar_nota_orden_lavado' ).append( 'Guardar Nota' );
      $( '#btn-salir' ).attr('disabled', false);
    })
  })

  $( '#btn-cobrar_cliente' ).click(function(){
		var fPagoClienteCobranza = parseFloat($( '#tel-cobrar_cliente-fPagoCliente' ).val());
    if ( fPagoClienteCobranza == 0.00 || isNaN(fPagoClienteCobranza) ) {
      $( '[name="fPagoCliente"]' ).closest('.form-group').find('.help-block').html( 'Ingresar monto' );
      $( '[name="fPagoCliente"]' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    
      scrollToError($('.modal-cobrar_cliente .modal-body'), $( '[name="fPagoCliente"]' ));
    } else if ( fPagoClienteCobranza > parseFloat($( '#hidden-cobrar_cliente-fsaldo' ).val()) ) {
      $( '#tel-cobrar_cliente-fPagoCliente' ).closest('.form-group').find('.help-block').html('Debes de cobrar <b>' + $( '#hidden-cobrar_cliente-fsaldo' ).val() + '</b>' );
      $( '#tel-cobrar_cliente-fPagoCliente' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    
      scrollToError($('.modal-cobrar_cliente .modal-body'), $( '#tel-cobrar_cliente-fPagoCliente' ));
    } else if ( $( '#cbo-modal_quien_recibe' ).val() == 0 && $( '[name="sNombreRecepcion"]' ).val().length === 0 ) {
      $( '[name="sNombreRecepcion"]' ).closest('.form-group').find('.help-block').html('Ingresar datos');
      $( '[name="sNombreRecepcion"]' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    
      scrollToError($('.modal-cobrar_cliente .modal-body'), $( '[name="sNombreRecepcion"]' ));
    } else {
      $( '.help-block' ).empty();
      $( '[name="fPagoCliente"]' ).closest('.form-group').removeClass('has-error');
      $( '[name="sNombreRecepcion"]' ).closest('.form-group').removeClass('has-error');
      
      $( '#btn-cobrar_cliente' ).text('');
      $( '#btn-cobrar_cliente' ).attr('disabled', true);
      $( '#btn-cobrar_cliente' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
      $( '#btn-salir' ).attr('disabled', true);

      url = base_url + 'Ventas/EstadoLavadoController/entregarPedidoLavado';
      $.ajax({
        type : 'POST',
        dataType : 'JSON',
        url : url,
        data : $('#form-cobrar_cliente').serialize(),
        success : function( response ){
          $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
          $( '#modal-message' ).modal('show');

          if ( response.sStatus=='success' ) {
            $( '.modal-cobrar_cliente' ).modal('hide');

            $( '.modal-message' ).addClass( 'modal-' + response.sStatus);
            $( '.modal-title-message' ).text( response.sMessage );
            setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
            
            getReporteHTML();
          } else {
            $( '.modal-message' ).addClass( 'modal-' + response.sStatus );
            $( '.modal-title-message' ).text( response.sMessage );
            setTimeout(function() {$('#modal-message').modal('hide');}, 3100);
          }
          
          $( '#btn-cobrar_cliente' ).text('');
          $( '#btn-cobrar_cliente' ).append( 'Cobrar' );
          $( '#btn-cobrar_cliente' ).attr('disabled', false);
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

        $( '#btn-cobrar_cliente' ).text('');
        $( '#btn-cobrar_cliente' ).attr('disabled', false);
        $( '#btn-cobrar_cliente' ).append( 'Cobrar' );
        $( '#btn-salir' ).attr('disabled', false);
      })
    }
  })

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

      url = base_url + 'Ventas/EstadoLavadoController/entregarPedidoLavado';
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
})

function checkAllMenuHeader(){
	if ( $( '#check-AllMenuHeader' ).prop('checked') ){
		$( '.check-iIdDocumentoCabecera' ).prop('checked', true);
		$( '#check-AllMenuFooter' ).prop('checked', true);
	}else{
		if( false == $( '#check-AllMenuHeader' ).prop('checked') ){
			$( '.check-iIdDocumentoCabecera' ).prop('checked', false);
			$( '#check-AllMenuFooter' ).prop('checked', false);
		}
	}
}

function checkAllMenuFooter(){
	if ( $( '#check-AllMenuFooter' ).prop('checked') ){
		$( '.check-iIdDocumentoCabecera' ).prop('checked', true);
		$( '#check-AllMenuHeader' ).prop('checked', true);
	}else{
		if( false == $( '#check-AllMenuFooter' ).prop('checked') ){
			$( '.check-iIdDocumentoCabecera' ).prop('checked', false);
			$( '#check-AllMenuHeader' ).prop('checked', false);
		}
	}
}

function cambiarEstadoLavado(){
  if ( $('#form-estado_lavado').serialize() == '' ) {
    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
    $( '#modal-message' ).modal('show');
    $( '.modal-message' ).addClass( 'modal-danger' );
    $( '.modal-title-message' ).text( 'Debe seleccionar al menos 1 fila' );
    setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
  } else {
    $( '.modal-delivery' ).modal('show');
    $( '#btn-salir' ).off('click').click(function () {
      $( '.modal-delivery' ).modal('hide');
    });
    
    $( '#btn-enviar_planta_transporte' ).off('click').click(function () {
      if ( $( '#cbo-transporte' ).val() == 0 ) {
        $( '#cbo-transporte' ).closest('.form-group').find('.help-block').html('Seleccionar transportista');
        $( '#cbo-transporte' ).closest('.form-group').removeClass('has-success').addClass('has-error');
        
        scrollToError($('.modal-delivery .modal-body'), $( '#cbo-transporte' ));        
      } else {
        $( '.help-block' ).empty();
        $( '#cbo-transporte' ).closest('.form-group').removeClass('has-error');
        $( '.modal-delivery' ).modal('hide');

        $( '.btn-save' ).text('');
        $( '.btn-save' ).attr('disabled', true);
        $( '.btn-save' ).append( 'Enviando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

        $( '#modal-loader' ).modal('show');
        url = base_url + 'Ventas/EstadoLavadoController/cambiarEstadoLavado';
        $.ajax({
          type : 'POST',
          dataType : 'JSON',
          url : url,
          data : $('#form-estado_lavado').serialize(),
          success : function( response ){
            $( '#modal-loader' ).modal('hide');
            $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
            $( '#modal-message' ).modal('show');
            
            if (response.sStatus == 'success'){
              $('#cbo-transporte').val('0');

              $( '.modal-message' ).addClass( 'modal-' + response.sStatus);
              $( '.modal-title-message' ).text( response.sMessage );
              setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
              
              getReporteHTML();
            } else {
              $( '.modal-message' ).addClass( 'modal-' + response.sStatus);
              $( '.modal-title-message' ).text(response.sMessage);
              setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
            }

            $( '.btn-save' ).text('');
            $( '.btn-save' ).append( '<span class="fa fa-truck"></span> Enviar' );
            $( '.btn-save' ).attr('disabled', false);
          }
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
          $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
          
          $( '#modal-message' ).modal('show');
          $( '.modal-message' ).addClass( 'modal-danger' );
          $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
          setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
          
          $( '#modal-loader' ).modal('hide');
        
          //Message for developer
          console.log(jqXHR.responseText);

          $( '.btn-save' ).text('');
          $( '.btn-save' ).append( '<span class="fa fa-truck"></span> Enviar' );
          $( '.btn-save' ).attr('disabled', false);
        });
      } // if - else validacion de seleccionar transporte 
    });
  } // if - else validacion de checkbox
}

function getReporteHTML(){
  $( '#check-AllMenuHeader' ).prop('checked', false);
  $( '#check-AllMenuFooter' ).prop('checked', false);

  var Fe_Inicio, Fe_Fin, iIdTipoDocumento, iIdSerieDocumento, iNumeroDocumento, iEstado, iIdCliente, sNombreCliente, iTipoRecepcionCliente, iEstadoLavado, Nu_Transporte_Lavanderia_Hoy;
  
  Fe_Inicio = ParseDateString($( '#txt-Filtro_Fe_Inicio' ).val(), 1, '/');
  Fe_Fin = ParseDateString($( '#txt-Filtro_Fe_Fin' ).val(), 1, '/');
  iIdTipoDocumento = $( '#cbo-filtros_tipos_documento' ).val();
  iIdSerieDocumento = $( '#cbo-filtros_series_documento' ).val();
  iNumeroDocumento = ($( '#txt-Filtro_NumeroDocumento' ).val().length === 0 ? '-' : $( '#txt-Filtro_NumeroDocumento' ).val());
  iEstado = $( '#cbo-estado_documento' ).val();
  iIdCliente = ($( '#txt-AID' ).val().length === 0 ? '-' : $( '#txt-AID' ).val());
  sNombreCliente = ($( '#txt-Filtro_Entidad' ).val().length === 0 ? '-' : $( '#txt-Filtro_Entidad' ).val());
  iTipoRecepcionCliente = $( '#cbo-tipo_recepcion_cliente' ).val();
  iEstadoLavado = $( '#cbo-estado_lavado' ).val();
  Nu_Transporte_Lavanderia_Hoy = $( '#cbo-tipo_envio_empresa' ).val();

  var arrPost = {
    Fe_Inicio : Fe_Inicio,
    Fe_Fin : Fe_Fin,
    iIdTipoDocumento : iIdTipoDocumento,
    iIdSerieDocumento : iIdSerieDocumento,
    iNumeroDocumento : iNumeroDocumento,
    iEstado : iEstado,
    iIdCliente : iIdCliente,
    sNombreCliente : sNombreCliente,
    iTipoRecepcionCliente : iTipoRecepcionCliente,
    iEstadoLavado : iEstadoLavado,
    Nu_Transporte_Lavanderia_Hoy : Nu_Transporte_Lavanderia_Hoy
  };

  $( '#btn-html_estado_lavado' ).text('');
  $( '#btn-html_estado_lavado' ).attr('disabled', true);
  $( '#btn-html_estado_lavado' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

  $( '#table-estado_lavado > tbody' ).empty();
  $( '#table-estado_lavado > tfoot' ).empty();
  
  url = base_url + 'Ventas/EstadoLavadoController/sendReporte';
  $.post( url, arrPost, function( response ){
    if ( response.sStatus == 'success' ) {
      var iTotalRegistros = response.arrData.length, tr_body = '', tr_foot = '', total_s = 0.00, total_s_saldo = 0.00, sum_total_s = 0.00, sum_total_s_saldo = 0.00;
      var response=response.arrData, sButton = '', sButtonNotas = '', sButtonEstadoLavadoItems = '', arrParams = '', arrDataNotas = '';
      for (var i = 0; i < iTotalRegistros; i++) {
        total_s = (!isNaN(parseFloat(response[i].Ss_Total)) ? parseFloat(response[i].Ss_Total) : 0);
        total_s_saldo = (!isNaN(parseFloat(response[i].Ss_Total_Saldo)) ? parseFloat(response[i].Ss_Total_Saldo) : 0);

        arrParams = {
          'iIdEmpresa' : response[i].ID_Empresa,
          'iIdDocumentoCabecera' : response[i].ID_Documento_Cabecera,
          'fTotalSaldo' : total_s_saldo,
          'sCliente' : response[i].No_Entidad,
          'sTipoDocumento' : response[i].No_Tipo_Documento_Breve,
          'sSerieDocumento' : response[i].ID_Serie_Documento,
          'sNumeroDocumento' : response[i].ID_Numero_Documento,
          'iEstadoLavadoRecepcionCliente' : response[i].Nu_Estado_Lavado_Recepcion_Cliente,
          'sSignoMoneda' : response[i].No_Signo,
          'iIdDetalleEstadoLavado' : response[i].ID_Documento_Estado_Lavado,
          'sGarantia' : btoa(response[i].Txt_Garantia),
          'iIdDocumentoMedioPago' : response[i].ID_Documento_Medio_Pago,
        }
        arrParams = JSON.stringify(arrParams);

        arrDataNotas = {
          'Txt_Final_Prelavado' : response[i].Txt_Final_Prelavado,
          'Txt_Final_Lavado_Seco' : response[i].Txt_Final_Lavado_Seco,
          'Txt_Planchado' : response[i].Txt_Planchado,
          'Txt_Doblado' : response[i].Txt_Doblado,
          'Txt_Embolsado' : response[i].Txt_Embolsado,
        };
        arrDataNotas = JSON.stringify(arrDataNotas);

        sButton = '';
        if ( total_s_saldo > 0 ) {
          sButton = "<button type='button' class='btn btn-xs btn-link' alt='Cobrar pedido' title='Cobrar pedido' href='javascript:void(0)' onclick='cobrarPedido(" + arrParams + ")'>Cobrar pedido</button>";
        } if ( total_s_saldo == 0 && response[i].Nu_Estado_Lavado_Recepcion_Cliente == 1 && (response[i].Nu_Estado_Lavado != 1 && response[i].Nu_Estado_Lavado != 2) ) {
          sButton = '';
        } else if ( (response[i].Nu_Estado_Lavado == 15 || response[i].Nu_Estado_Lavado == 18) && (response[i].Nu_Estado_Lavado_Recepcion_Cliente == 2 || response[i].Nu_Estado_Lavado_Recepcion_Cliente == 4) ) {
          sButton = "<button type='button' class='btn btn-xs btn-link' alt='Entregar pedido' title='Entregar pedido' href='javascript:void(0)' onclick='entregarPedido(" + arrParams + ")'>Entregar pedido</button>";
        }

        sButtonNotasCajero = "<button type='button' class='btn btn-xs btn-link' alt='Ver notas' title='Ver notas' href='javascript:void(0)' onclick='verNotasOrdenLavado(" + arrParams + ")'>Agregar Nota</button>";

        sButtonEstadoLavadoItems = '';
        if ( response[i].ID_Documento_Estado_Lavado != '' ) {
          sButtonEstadoLavadoItems = "<button type='button' class='btn btn-xs btn-link' data-toggle='tooltip' data-placement='bottom' title='Ver historial de orden de lavado' href='javascript:void(0)' onclick='verEstadoLavadoItems(" + arrParams + ")'>Ver Historial</button>";
        }

        tr_body +=
        "<tr>"
          +"<td class='text-center'>" + (response[i].Nu_Estado_Lavado == 1 && (response[i].Nu_Transporte_Lavanderia_Hoy != 2 && response[i].Nu_Transporte_Lavanderia_Hoy != 4) ? "<input type='checkbox' id='" + response[i].ID_Documento_Cabecera + "' class='check-iIdDocumentoCabecera' name='arrIdDocumentoCabecera[" + response[i].ID_Documento_Cabecera + "]'>" : '') + "</td>"
          +"<td class='text-center'>" + response[i].No_Empleado + "</td>"
          +"<td class='text-center'>" + response[i].Fe_Emision_Hora + "</td>"
          +"<td class='text-center'>" + response[i].Fe_Entrega + "</td>"
          +"<td class='text-center'>" + response[i].No_Tipo_Documento_Breve + "</td>"
          +"<td class='text-center'>" + response[i].ID_Serie_Documento + "</td>"
          +"<td class='text-center'>" + response[i].ID_Numero_Documento + "</td>"
          +"<td class='text-left'>" + response[i].No_Entidad + "</td>"
          +"<td class='text-center'>" + response[i].Nu_Celular_Entidad + "</td>"
          +"<td class='text-center'>" + response[i].No_Signo + "</td>"
          +"<td class='text-right'>" + (response[i].ID_Tipo_Documento != 5 ? '' : '-') + number_format(total_s, 2) + "</td>"
          +"<td class='text-right'>" + (response[i].ID_Tipo_Documento != 5 ? '' : '-') + number_format(total_s_saldo, 2) + "</td>"
          +"<td class='text-center'>" + response[i].Txt_Glosa + "</td>"
          +"<td class='text-center'><span class='label label-" + response[i].No_Class_Estado_Lavado_Recepcion_Cliente + "'>" + response[i].No_Estado_Lavado_Recepcion_Cliente + "</span>" + "</td>"
          +"<td class='text-left'>" + response[i].No_Entidad_Recepcion_Lavado + "</td>"
          +"<td class='text-left'>" + response[i].No_Entidad_Lavado + "</td>"
          +"<td class='text-center'>" + sButtonNotasCajero + "</td>"
          +"<td class='text-center'>" + sButton + "</td>"
          +"<td class='text-center'>" + sButtonEstadoLavadoItems + "</td>"
          +"<td class='text-center'>" + ( ((response[i].Nu_Estado == 6 || response[i].Nu_Estado == 8) && response[i].Nu_Estado_Lavado != 9) ? response[i].sAccionVerComanda : '') + "</td>"
          +"<td class='text-center'>" + ( ((response[i].Nu_Estado == 6 || response[i].Nu_Estado == 8) && response[i].Nu_Estado_Lavado != 9) ? response[i].sAccionImprimirComanda : '') + "</td>"
        +"</tr>";
        
        sum_total_s += total_s;
        sum_total_s_saldo += total_s_saldo;
      }
      
      tr_foot =
      "<tfoot>"
        +"<tr>"
          +"<th class='text-center'><input type='checkbox' onclick='checkAllMenuFooter();' id='check-AllMenuFooter'></th>"
          +"<th class='text-right' colspan='14'></th>"
          +"<th class='text-center' colspan='4'><button type='button' class='btn btn-success btn-block btn-save' onclick='cambiarEstadoLavado();'><span class='fa fa-truck'></span> Enviar</button></th>"
        +"</tr>"
      +"</tfoot>";
    } else {
      if( response.sMessageSQL !== undefined ) {
        console.log(response.sMessageSQL);
      }
      tr_body +=
      "<tr>"
        +"<td colspan='19' class='text-center'>" + response.sMessage + "</td>"
      + "</tr>";
    } // ./ if arrData
    
    $( '#div-estado_lavado' ).show();
    $( '#table-estado_lavado > tbody' ).append(tr_body);
    $( '#table-estado_lavado > tbody' ).after(tr_foot);
    
    $( '#btn-html_estado_lavado' ).text('');
    $( '#btn-html_estado_lavado' ).append( '<i class="fa fa-search"></i> Buscar' );
    $( '#btn-html_estado_lavado' ).attr('disabled', false);
  }, 'JSON')
  .fail(function(jqXHR, textStatus, errorThrown) {
    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
    
    $( '#modal-message' ).modal('show');
    $( '.modal-message' ).addClass( 'modal-danger' );
    $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
    setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
    
    //Message for developer
    console.log(jqXHR.responseText);
    
    $( '#btn-html_estado_lavado' ).text('');
    $( '#btn-html_estado_lavado' ).append( '<i class="fa fa-search"></i> Buscar' );
    $( '#btn-html_estado_lavado' ).attr('disabled', false);
  });
}

function verNotas(arrDataNotas){
  $( '.modal-notas_planta' ).modal('show');
  $( '#div-final_prelavado' ).hide();
  $( '#div-final_lavado_seco' ).hide();
  $( '#div-planchado' ).hide();
  $( '#div-doblado' ).hide();
  $( '#div-embolsado' ).hide();
  if ( arrDataNotas.Txt_Final_Prelavado != null && arrDataNotas.Txt_Final_Prelavado != '' ) {
    $( '#div-final_prelavado' ).show();
    $( '#div-final_prelavado' ).html( '<label>Lavado al agua</label><p>' + arrDataNotas.Txt_Final_Prelavado + '</p>');
  }
  if ( arrDataNotas.Txt_Final_Lavado_Seco != null && arrDataNotas.Txt_Final_Lavado_Seco != '' ) {
    $( '#div-final_lavado_seco' ).show();
    $( '#div-final_lavado_seco' ).html( '<label>Lavado al seco</label><p>' + arrDataNotas.Txt_Final_Lavado_Seco + '</p>');
  }
  if ( arrDataNotas.Txt_Planchado != null && arrDataNotas.Txt_Planchado != '') {
    $( '#div-planchado' ).show();
    $( '#div-planchado' ).html( '<label>Planchado</label><p>' + arrDataNotas.Txt_Planchado + '</p>');
  }
  if ( arrDataNotas.Txt_Doblado != null && arrDataNotas.Txt_Doblado != '' ) {
    $( '#div-doblado' ).show();
    $( '#div-doblado' ).html( '<label>Doblado</label><p>' + arrDataNotas.Txt_Doblado + '</p>');
  }
  if ( arrDataNotas.Txt_Embolsado != null && arrDataNotas.Txt_Embolsado != '' ) {
    $( '#div-embolsado' ).show();
    $( '#div-embolsado' ).html( '<label>Embolsado</label><p>' + arrDataNotas.Txt_Embolsado + '</p>');
  }
}

function verNotasOrdenLavado(arrParams){
  $( '#form-agregar_nota_orden_lavado' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();

  $( '.modal-agregar_nota_orden_lavado' ).modal('show');

  $( '[name="iIdDocumentoCabecera"]' ).val( arrParams.iIdDocumentoCabecera );
  $( '[name="iIdDocumentoMedioPago"]' ).val( arrParams.iIdDocumentoMedioPago );

  $( '[name="Txt_Garantia"]' ).val( atob(arrParams.sGarantia) );
}

function verEstadoLavadoItems(arrParams){
  $( '#table-estado_lavado_items tbody' ).empty();
  url = base_url + 'HelperController/getDocumentoDetalleEstadoLavado';
  var arrPost = {
    iIdDocumentoCabecera : arrParams.iIdDocumentoCabecera,
  };
  
  $.post( url, arrPost, function( response ){
    if ( response.sStatus == 'success' ) {
      $( '.modal-estado_pedido' ).modal('show');
      var iTotalRegistros = response.arrData.length, table_estado_lavado_items = '', response=response.arrData;
      for (var i = 0; i < iTotalRegistros; i++) {
        table_estado_lavado_items +=
        '<tr>'
          +'<td class="text-left">' + response[i].No_Tipo_Envio_Transporte_Detalle + '</td>'
          +'<td class="text-left">' + response[i].No_Tipo_Pedido_Lavado + '</td>'
          +'<td class="text-right">' + number_format(response[i].Qt_Producto, 2) + '</td>'
          +'<td class="text-left">' + response[i].No_Producto + '</td>'
          +'<td class="text-center">'
            +'<span class="label label-' + response[i].No_Class_Estado_Lavado_Detalle + '">' + ((response[i].No_Estado_Lavado_Detalle != null && response[i].No_Estado_Lavado_Detalle != '') ? response[i].No_Estado_Lavado_Detalle : '') + '</span>'
          +'</td>'
          +'<td class="text-left">';
            if ( response[i].No_Entidad_Lavado_Iniciado != null && response[i].No_Entidad_Lavado_Iniciado != '' )
              table_estado_lavado_items +='<b>Nombre:</b> ' + response[i].No_Entidad_Lavado_Iniciado + '<br><br>';
            if ( response[i].No_Entidad_Lavado_Finalizado != null && response[i].No_Entidad_Lavado_Finalizado != '' )
              table_estado_lavado_items +='<b>Nombre:</b> ' + response[i].No_Entidad_Lavado_Finalizado + '<br>'; table_estado_lavado_items += (((response[i].Txt_Final_Prelavado!=null && response[i].Txt_Final_Prelavado != '') || (response[i].Txt_Final_Lavado_Seco!=null && response[i].Txt_Final_Lavado_Seco != '')) ? '<b>Nota:</b> ' + response[i].Txt_Final_Prelavado + ' ' + response[i].Txt_Final_Lavado_Seco + '<br><br>' : '<br>');
            if ( response[i].No_Entidad_Lavado_Planchado != null && response[i].No_Entidad_Lavado_Planchado != '' )
              table_estado_lavado_items +='<b>Nombre:</b> ' + response[i].No_Entidad_Lavado_Planchado + '<br>'; table_estado_lavado_items += ((response[i].Txt_Planchado!=null && response[i].Txt_Planchado != '') ? '<b>Nota:</b> ' + response[i].Txt_Planchado + '<br><br>' : '<br>');
            if ( response[i].No_Entidad_Lavado_Doblado != null && response[i].No_Entidad_Lavado_Doblado != '' )              
              table_estado_lavado_items +='<b>Nombre:</b> ' + response[i].No_Entidad_Lavado_Doblado + '<br>'; table_estado_lavado_items += ((response[i].Txt_Doblado!=null && response[i].Txt_Doblado != '') ? '<b>Nota:</b> ' + response[i].Txt_Doblado + '<br><br>' : '<br>');
            if ( response[i].No_Entidad_Lavado_Embolsado != null && response[i].No_Entidad_Lavado_Embolsado != '' )              
              table_estado_lavado_items +='<b>Nombre:</b> ' + response[i].No_Entidad_Lavado_Embolsado + '<br> <b>Nota:</b> ' + response[i].Txt_Embolsado;
          table_estado_lavado_items += '</td>'
          +'<td class="text-left">' + ((response[i].No_Entidad_Transporte != null && response[i].No_Entidad_Transporte != '') ? response[i].No_Entidad_Transporte : '') + '</td>'
        +'</tr>';
      }
      $( '#table-estado_lavado_items' ).show();
      $( '#table-estado_lavado_items' ).append(table_estado_lavado_items);
    } else {
      if( response.sMessageSQL !== undefined ) {
        console.log(response.sMessageSQL);
      }
      $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
      $( '#modal-message' ).modal('show');
      $( '.modal-message' ).addClass( 'modal-' + response.sStatus );
      $( '.modal-title-message' ).text( response.sMessage );
      setTimeout(function() {$('#modal-message').modal('hide');}, 3100);
    }
  }, 'JSON');
}

function verificarPedido(arrParams){
  $( '#form-verificar_pedido' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();

  $( '.modal-verificar_pedido' ).modal('show');

  $( '[name="iIdDocumentoCabecera"]' ).val( arrParams.iIdDocumentoCabecera );
  $( '[name="iIdDocumentoMedioPago"]' ).val( arrParams.iIdDocumentoMedioPago );
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
  $( '[name="iIdDocumentoMedioPago"]' ).val( arrParams.iIdDocumentoMedioPago );

  $( '#hidden-cobrar_cliente-fsaldo' ).val( arrParams.fTotalSaldo );

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
    $( '#cbo-modal_tarjeta_credito' ).html('');
    $( '#tel-nu_referencia' ).val('');
    $( '#tel-nu_ultimo_4_digitos_tarjeta' ).val('');
    if (Nu_Tipo_Medio_Pago==2){
      $( '.div-modal_datos_tarjeta_credito').show();

      url = base_url + 'HelperController/getTiposTarjetaCredito';
      $.post( url, {ID_Medio_Pago : ID_Medio_Pago} , function( response ){
        $( '#cbo-modal_tarjeta_credito' ).html('');
        for (var i = 0; i < response.length; i++)
          $( '#cbo-modal_tarjeta_credito' ).append( '<option value="' + response[i].ID_Tipo_Medio_Pago + '">' + response[i].No_Tipo_Medio_Pago + '</option>' );
      }, 'JSON');
    }

    setTimeout(function(){ $( '[name="fPagoCliente"]' ).focus(); $( '[name="fPagoCliente"]' ).select(); }, 20);		
  })

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

function entregarPedido(arrParams){
  $( '#form-entregar_pedido' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();

  $( '.div-forma_pago').show();
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

  $( '[name="fPagoCliente"]' ).val( '0' );

  arrParams.fTotalSaldo = parseFloat(arrParams.fTotalSaldo);

  $( '.div-forma_pago' ).hide();
  $( '.div-modal_datos_tarjeta_credito' ).hide();

  if ( arrParams.fTotalSaldo > 0 ) {
    $( '.div-forma_pago' ).show();
    $( '.div-modal_datos_tarjeta_credito' ).show();

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
      $( '#cbo-modal_tarjeta_credito' ).html('');
      $( '#tel-nu_referencia' ).val('');
      $( '#tel-nu_ultimo_4_digitos_tarjeta' ).val('');
      if (Nu_Tipo_Medio_Pago==2){
        $( '.div-modal_datos_tarjeta_credito').show();

        url = base_url + 'HelperController/getTiposTarjetaCredito';
        $.post( url, {ID_Medio_Pago : ID_Medio_Pago} , function( response ){
          $( '#cbo-modal_tarjeta_credito' ).html('');
          for (var i = 0; i < response.length; i++)
            $( '#cbo-modal_tarjeta_credito' ).append( '<option value="' + response[i].ID_Tipo_Medio_Pago + '">' + response[i].No_Tipo_Medio_Pago + '</option>' );
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

function formatoImpresionTicketComandaLavado(arrPost){
  if ( arrPost.sAccion == undefined ) {
    arrPost = JSON.parse(arrPost);
  }

  if ( arrPost.sAccion != 'imprimir' ) {
    $( '.modal-ticket_comanda_lavado' ).modal('show');
  }

  url = base_url + 'ImprimirTicketController/formatoImpresionTicketComandaLavado';
  $.post( url, arrPost, function( response ) {
    if ( response.sStatus == 'success' ) {
      var iTotalRegistros = response.arrData.length, response=response.arrData;

      // Logo empresa
      if ( arrPost.sMostrarOcultarImagen == 'ocultar-img-logo_punto_venta_click' )
        $ ( '#img-logo_punto_venta_click_lavado' ).hide();

      if ( iMostrarLogoTicketGlobal == 1 ) {//1=Si
        if ( arrPost.sAccion != 'imprimir' ) {
          $ ( '#img-logo_punto_venta_lavado' ).hide();
          var url_logo_dominio = src_root_sitio_web_js + 'assets/images/logos/' + response[0].No_Logo_Empresa;
          $("#img-logo_punto_venta_click_lavado").attr({ "src": url_logo_dominio });
        }
      }

      if ( arrPost.sMostrarOcultarImagen == 'mostrar-img-logo_punto_venta' ) {
        $ ( '#img-logo_punto_venta_lavado' ).show();
        $ ( '#img-logo_punto_venta_click_lavado' ).hide();
      }
      // /. Logo empresa
      
      // Cabecera <p> Titulo Numero de Orden
      var p_cabecera_title_numero = '<strong>' + response[0].ID_Serie_Documento + '-' + response[0].ID_Numero_Documento + '</strong>';
      $( '#modal-body-p-title_numero' ).html(p_cabecera_title_numero);
      // /. Cabecera <p> Titulo Numero de Orden
      
      // Cabecera <p> Titulo Tipo envio lavado
      var sNombreEstadoPedidoLavado='';
      if (response[0].Nu_Transporte_Lavanderia_Hoy==1)//Transporte HOY
        sNombreEstadoPedidoLavado = 'Transporte HOY';

      if (response[0].Nu_Transporte_Lavanderia_Hoy == 2)//Servicio Tercerizado Externo
        sNombreEstadoPedidoLavado = 'Servicio Tercerizado Externo';

      if (response[0].Nu_Transporte_Lavanderia_Hoy == 3)//Servicio Tercerizado Interno
        sNombreEstadoPedidoLavado = 'Servicio Tercerizado Interno';

      if (response[0].Nu_Transporte_Lavanderia_Hoy == 4)//Empresa
        sNombreEstadoPedidoLavado = 'Empresa';

      var p_cabecera_title_tipo_envio_lavado = '<strong>' + sNombreEstadoPedidoLavado + '</strong>';
      $( '#modal-body-p-title_tipo_envio_lavado' ).html(p_cabecera_title_tipo_envio_lavado);
      // /. Cabecera <p> Titulo Tipo envio lavado

      // Cabecera y detalle table
      var table_cabecera_detalle = '';
      table_cabecera_detalle += 
      '<thead>'
        +'<tr>'
          +'<td class="text-left">F. Emisión: </td>'
          +'<td class="text-left">' + ParseDateHour(response[0].Fe_Emision_Hora) + '</td>'
        +'</tr>'
        +'<tr>'
          +'<td class="text-left">F. Entrega: </td>'
          +'<td class="text-left">' + ParseDate(response[0].Fe_Entrega) + '</td>'
        +'</tr>'
        +'<tr>'
          +'<td class="text-left">Cliente: </td>'
          +'<td class="text-left">' + response[0].Nu_Documento_Identidad + ' - ' + response[0].No_Entidad + '</td>'
        +'</tr>'
        +'<tr>'
          +'<td class="text-left">Celular: </td>'
          +'<td class="text-left">' + response[0].Nu_Celular_Entidad + '</td>'
        +'</tr>'
        +'<tr>'
          +'<td class="text-left" colspan="2">&nbsp;</td>'
        +'</tr>'
      +'</thead>'
      +'<tbody>'
        +'<tr>'
          +'<td class="text-left" style="width: 3%; padding: 2px; border-top: 1px solid transparent !important; border-bottom: 1px solid black !important;">CANTIDAD</td>'
          +'<td class="text-left" style="width: 20%; padding: 2px; border-top: 1px solid transparent !important; border-bottom: 1px solid black !important;">DESCRIPCION</td>'
        +'</tr>';
      for (var i = 0; i < iTotalRegistros; i++) {
        table_cabecera_detalle +=
        '<tr>'
          +'<td class="text-left" style="padding: 0px; border-top: 1px solid transparent;">' + number_format(response[i].Qt_Producto, 2) + '</td>'
          +'<td class="text-left" style="padding: 0px;">' + response[i].No_Producto + ' ' + response[i].Txt_Nota_Item + '</td>'
        +'</tr>';
      }
      table_cabecera_detalle += 
      +'</tbody>'
      +'<tfoot>'
        +'<tr>'
          +'<td class="text-left" colspan="2">&nbsp;</td>'
        +'</tr>'
        +'<tr>'
          +'<td class="text-left">Cajero: </td>'
          +'<td class="text-left">' + response[0].No_Empleado + '</td>'
        +'</tr>'
      +'</tfoot>';
      // /. Cabecera y detalle table
      $( '#modal-table-ticket_comanda_lavado' ).html(table_cabecera_detalle);

      if (arrPost.sAccion == 'imprimir') {
        generarFormatoImpresionComanda('div-ticket_comanda_lavado');
      }
    } else {
      if( response.sMessageSQL !== undefined ) {
        console.log(response.sMessageSQL);
      }
      alert( response.sMessage );
    }
  }, 'JSON')
  .fail(function(jqXHR, textStatus, errorThrown) {
    console.log(jqXHR.responseText);
    $( '#modal-loader' ).modal('hide');
  });
}

function generarFormatoImpresionComanda(sIdFormatoImpresion){
  winPrintSunat = window.open("", "MsgWindow", "top=80,left=800,width=550,height=550");
  winPrintSunat.document.open();
	printContentsSunat = document.getElementById(sIdFormatoImpresion).innerHTML;
  winPrintSunat.document.write(printContentsSunat);
	winPrintSunat.document.close();
	winPrintSunat.focus();
	winPrintSunat.print();
	winPrintSunat.close();
}