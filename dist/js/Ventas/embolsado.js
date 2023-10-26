var url;

$(function () {
  $( '#modal-loader' ).modal('show');
  $( '#div-proceso_planta_lavanderia' ).hide();
  $( '#div-pin' ).show();
  $( '#div-pin_finalizado' ).show();
  $( '#div-detalle_item_pedido' ).hide();
  $( '.div-detalle_item_pedido_finalizado' ).hide();
  $( '#table-detalle_item_pedido' ).hide();
  $( '#table-detalle_item_pedido_finalizado' ).hide();
  $( '#btn-procesar_pedido' ).hide();
  
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
  
  url = base_url + 'HelperController/getTiposDocumentos';
  $.post( url, {Nu_Tipo_Filtro : 3}, function( response ){
    $( '#cbo-filtros_tipos_documento' ).html('<option value="0" selected="selected">Todos</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-filtros_tipos_documento' ).append( '<option value="' + response[i].ID_Tipo_Documento + '">' + response[i].No_Tipo_Documento_Breve + '</option>' );
	  $( '#modal-loader' ).modal('hide');
  
    $( '#txt-Filtro_Entidad' ).val('');
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
  
	url = base_url + 'HelperController/getDataGeneral';
	$.post( url, {sTipoData : 'categoria'}, function( response ){
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

  $( '.btn-proceso_planta_lavanderia' ).click(function(){
    if ( $( '#txt-Filtro_Entidad' ).val().length > 0 && $( '#txt-AID' ).val().length === 0 ) {
      $( '#txt-Filtro_Entidad' ).closest('.form-group').find('.help-block').html('Seleccionar cliente');
		  $( '#txt-Filtro_Entidad' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      $( '.help-block' ).empty();
    
      var Fe_Inicio, Fe_Fin, iIdTipoDocumento, iIdSerieDocumento, iNumeroDocumento, iEstado, iEstadoLavado, iIdFamilia, iIdItem, sNombreItem, iIdCliente, sNombreCliente;
      
      Fe_Inicio = ParseDateString($( '#txt-Filtro_Fe_Inicio' ).val(), 1, '/');
      Fe_Fin = ParseDateString($( '#txt-Filtro_Fe_Fin' ).val(), 1, '/');
      iIdTipoDocumento = $( '#cbo-filtros_tipos_documento' ).val();
      iIdSerieDocumento = $( '#cbo-filtros_series_documento' ).val();
      iNumeroDocumento = ($( '#txt-Filtro_NumeroDocumento' ).val().length === 0 ? '-' : $( '#txt-Filtro_NumeroDocumento' ).val());
      iEstado = $( '#cbo-estado_documento' ).val();
      iEstadoLavado = $( '#cbo-estado_orden_lavado' ).val();
      iIdFamilia = $( '#cbo-familia' ).val();
      iIdItem = ($( '#txt-ID_Producto' ).val().length === 0 ? '-' : $( '#txt-ID_Producto' ).val());
      sNombreItem = ($( '#txt-No_Producto' ).val().length === 0 ? '-' : $( '#txt-No_Producto' ).val());
      iIdCliente = ($( '#txt-AID' ).val().length === 0 ? '-' : $( '#txt-AID' ).val());
      sNombreCliente = ($( '#txt-Filtro_Entidad' ).val().length === 0 ? '-' : $( '#txt-Filtro_Entidad' ).val());

      if ($(this).data('type') == 'html') {
        getReporteHTML();
      } else if ($(this).data('type') == 'pdf') {
        $( '#btn-pdf_proceso_planta_lavanderia' ).text('');
        $( '#btn-pdf_proceso_planta_lavanderia' ).attr('disabled', true);
        $( '#btn-pdf_proceso_planta_lavanderia' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

        url = base_url + 'Ventas/EmbolsadoController/sendReportePDF/' + Fe_Inicio + '/' + Fe_Fin + '/' + iIdTipoDocumento + '/' + iIdSerieDocumento + '/' + iNumeroDocumento + '/' + iEstado + '/' + iEstadoLavado + '/' + iIdFamilia + '/' + iIdItem + '/' + sNombreItem + '/' + iIdCliente + '/' + sNombreCliente;
        window.open(url,'_blank');
        
        $( '#btn-pdf_proceso_planta_lavanderia' ).text('');
        $( '#btn-pdf_proceso_planta_lavanderia' ).append( '<i class="fa fa-file-pdf-o color_icon_pdf"></i> PDF' );
        $( '#btn-pdf_proceso_planta_lavanderia' ).attr('disabled', false);
      } else if ($(this).data('type') == 'excel') {
        $( '#btn-excel_proceso_planta_lavanderia' ).text('');
        $( '#btn-excel_proceso_planta_lavanderia' ).attr('disabled', true);
        $( '#btn-excel_proceso_planta_lavanderia' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
        
        url = base_url + 'Ventas/EmbolsadoController/sendReporteEXCEL/' + Fe_Inicio + '/' + Fe_Fin + '/' + iIdTipoDocumento + '/' + iIdSerieDocumento + '/' + iNumeroDocumento + '/' + iEstado + '/' + iEstadoLavado + '/' + iIdFamilia + '/' + iIdItem + '/' + sNombreItem + '/' + iIdCliente + '/' + sNombreCliente;
        window.open(url,'_blank');
        
        $( '#btn-excel_proceso_planta_lavanderia' ).text('');
        $( '#btn-excel_proceso_planta_lavanderia' ).append( '<i class="fa fa-file-excel-o color_icon_excel"></i> Excel' );
        $( '#btn-excel_proceso_planta_lavanderia' ).attr('disabled', false);
      }// /. if all button 
    }// /. if - else validacion
  })// /. btn
  
  // FINALIZAR PEDIDO
  $( '#btn-validar_pin_personal_finalizado' ).off('click').click(function () {
    if ( $( '#tel-pin_finalizado' ).val().length===0 ) {
      $( '#tel-pin_finalizado' ).closest('.form-group').find('.help-block').html('Ingresar PIN');
      $( '#tel-pin_finalizado' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    
      scrollToError($('.modal-finalizar_pedido_Embolsado .modal-body'), $( '#tel-pin_finalizado' ));
    } else {
      $( '.help-block' ).empty();
      $( '#tel-pin_finalizado' ).closest('.form-group').removeClass('has-error');

      $( '#btn-validar_pin_personal' ).text('');
      $( '#btn-validar_pin_personal' ).attr('disabled', true);
      $( '#btn-validar_pin_personal' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

      url = base_url + 'Ventas/EmbolsadoController/verificarPersonalxPIN';
      var arrParams = {
        iPin : $( '#tel-pin_finalizado' ).val(),
      }
      $.post( url, arrParams, function( response ){
        var responsePersonal = response;
        $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
        $( '.modal-message' ).css("z-index", "2000");

        $( '#table-detalle_item_pedido_finalizado tbody' ).empty();
        if ( responsePersonal.sStatus=='success' ) {
          url = base_url + 'HelperController/getDocumentoDetalleEstadoLavadoxDocumentoDetalle';
          var arrPost = {
            iIdDocumentoDetalle : $( '#hidden-iIdDocumentoDetalleFinalizado' ).val(),
          };
          $.post( url, arrPost, function( response ){
            if ( response.sStatus == 'success' ) {
              $( '#div-pin_finalizado' ).hide();
              $( '.div-detalle_item_pedido_finalizado' ).show();

              $( '#h4-datos_personal_finalizado' ).text( '' + responsePersonal.arrData[0].No_Entidad );
              $( '#hidden-iIdEntidadFinalizado' ).val( responsePersonal.arrData[0].ID_Entidad );

              var iTotalRegistros = response.arrData.length, table_item_detalle_pedido_finalizado = '', response=response.arrData;
              for (var i = 0; i < iTotalRegistros; i++) {
                table_item_detalle_pedido_finalizado +=
                "<tr>"
                  + "<td class='text-center td-fCantidad'>" + response[i].Qt_Producto + "</td>"
                  + "<td class='text-left td-sNombreItem'>" + response[i].Txt_Item + "</td>"
                + "</tr>";
              }
              $( '#table-detalle_item_pedido_finalizado' ).show();
              $( '#table-detalle_item_pedido_finalizado' ).append(table_item_detalle_pedido_finalizado);
            } else {
              if( response.sMessageSQL !== undefined ) {
                console.log(response.sMessageSQL);
              }
              $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
              $( '#modal-message' ).modal('show');
              $( '.modal-message' ).addClass( 'modal-danger' );
              $( '.modal-title-message' ).text( response.sMessage );
              setTimeout(function() {$('#modal-message').modal('hide');}, 3100);
            }
          }, 'JSON');
        } else {
          $( '#modal-message' ).modal('show');
          $( '.modal-message' ).addClass( 'modal-' + responsePersonal.sStatus );
          $( '.modal-title-message' ).text( responsePersonal.sMessage );
          setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
        } // /. if - else ajax post personal
        
        $( '#btn-validar_pin_personal' ).text('');
        $( '#btn-validar_pin_personal' ).append( 'Iniciar' );
        $( '#btn-validar_pin_personal' ).attr('disabled', false);
      }, 'JSON')
      .fail(function(jqXHR, textStatus, errorThrown) {
        $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
        
        $( '#modal-message' ).modal('show');
        $( '.modal-message' ).css("z-index", "2000");
        $( '.modal-message' ).addClass( 'modal-danger' );
        $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
        setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
        
        //Message for developer
        console.log(jqXHR.responseText);
        
        $( '#btn-validar_pin_personal' ).text('');
        $( '#btn-validar_pin_personal' ).append( 'Iniciar' );
        $( '#btn-validar_pin_personal' ).attr('disabled', false);
      });
    }
  });
  // /. FINALIZAR PEDIDO

  // CAMBIAR ESTADO DE LAVADO
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
      url = base_url + 'Ventas/EmbolsadoController/cambiarEstadoLavado';
      $.ajax({
        type : 'POST',
        dataType : 'JSON',
        url : url,
        data : $('#form-proceso_planta_lavanderia').serialize(),
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
  // /. CAMBIAR ESTADO DE LAVADO

	$( '#btn-finalizar_pedido' ).click(function(){
    generarPedidoFinalizado();
  });
})// /. document ready

function getReporteHTML(){
  $( '#check-AllMenuHeader' ).prop('checked', false);
  $( '#check-AllMenuFooter' ).prop('checked', false);

  var Fe_Inicio, Fe_Fin, iIdTipoDocumento, iIdSerieDocumento, iNumeroDocumento, iEstado, iEstadoLavado, iIdFamilia, iIdItem, sNombreItem, iIdCliente, sNombreCliente;
  
  Fe_Inicio = ParseDateString($( '#txt-Filtro_Fe_Inicio' ).val(), 1, '/');
  Fe_Fin = ParseDateString($( '#txt-Filtro_Fe_Fin' ).val(), 1, '/');
  iIdTipoDocumento = $( '#cbo-filtros_tipos_documento' ).val();
  iIdSerieDocumento = $( '#cbo-filtros_series_documento' ).val();
  iNumeroDocumento = ($( '#txt-Filtro_NumeroDocumento' ).val().length === 0 ? '-' : $( '#txt-Filtro_NumeroDocumento' ).val());
  iEstado = $( '#cbo-estado_documento' ).val();
  iEstadoLavado = $( '#cbo-estado_orden_lavado' ).val();
  iIdFamilia = $( '#cbo-familia' ).val();
  iIdItem = ($( '#txt-ID_Producto' ).val().length === 0 ? '-' : $( '#txt-ID_Producto' ).val());
  sNombreItem = ($( '#txt-No_Producto' ).val().length === 0 ? '-' : $( '#txt-No_Producto' ).val());
  iIdCliente = ($( '#txt-AID' ).val().length === 0 ? '-' : $( '#txt-AID' ).val());
  sNombreCliente = ($( '#txt-Filtro_Entidad' ).val().length === 0 ? '-' : $( '#txt-Filtro_Entidad' ).val());

  var arrPost = {
    Fe_Inicio : Fe_Inicio,
    Fe_Fin : Fe_Fin,
    iIdTipoDocumento : iIdTipoDocumento,
    iIdSerieDocumento : iIdSerieDocumento,
    iNumeroDocumento : iNumeroDocumento,
    iEstado : iEstado,
    iEstadoLavado : iEstadoLavado,
    iIdFamilia : iIdFamilia,
    iIdItem : iIdItem,
    sNombreItem : sNombreItem,
    iIdCliente : iIdCliente,
    sNombreCliente : sNombreCliente,
  };

  $( '#btn-html_proceso_planta_lavanderia' ).text('');
  $( '#btn-html_proceso_planta_lavanderia' ).attr('disabled', true);
  $( '#btn-html_proceso_planta_lavanderia' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

  $( '#table-proceso_planta_lavanderia > tbody' ).empty();
  $( '#table-proceso_planta_lavanderia > tfoot' ).empty();
  
  url = base_url + 'Ventas/EmbolsadoController/sendReporte';
  $.post( url, arrPost, function( response ){
    if ( response.sStatus == 'success' ) {
      var iTotalRegistros = response.arrData.length, tr_body = '', tr_foot = '', total_s = 0.00, total_s_saldo = 0.00, sum_total_s = 0.00, sum_total_s_saldo = 0.00;
      var response=response.arrData, sAccionButton = '';
      for (var i = 0; i < iTotalRegistros; i++) {
        //total_s = (!isNaN(parseFloat(response[i].Ss_Total)) ? parseFloat(response[i].Ss_Total) : 0);
        //total_s_saldo = (!isNaN(parseFloat(response[i].Ss_Total_Saldo)) ? parseFloat(response[i].Ss_Total_Saldo) : 0);

        arrParams = {
          'iIdDocumentoCabecera' : response[i].ID_Documento_Cabecera,
          'iIdDocumentoDetalle' : response[i].ID_Documento_Detalle,
          'sTipoDocumento' : response[i].No_Tipo_Documento_Breve,
          'sSerieDocumento' : response[i].ID_Serie_Documento,
          'sNumeroDocumento' : response[i].ID_Numero_Documento,
          'fCantidadItem' : response[i].Qt_Producto,
          'sNombreItem' : response[i].No_Producto,
          'iIdEstadoLavado' : response[i].Nu_Estado_Lavado,
          'sNota' : response[i].Txt_Glosa,
        };
        arrParams = JSON.stringify(arrParams);

        sAccionButton = '';
        if ( response[i].Nu_Estado_Lavado==8 || response[i].Nu_Estado_Lavado==9 ) {
          sAccionButton = "<button type='button' class='btn btn-xs btn-link' alt='Finalizar pedido' title='Finalizar pedido' href='javascript:void(0)' onclick='finalizarPedido(" + arrParams + ")'>Finalizar pedido</button>";
        }

        tr_body +=
        "<tr>"
          +"<td style='display:none;' class='text-center'>" + ((response[i].Nu_Estado_Lavado == 17 || response[i].Nu_Estado_Lavado==9) ? "<input type='hidden' id='" + i + "' name='arrIdDocumentoDetalle[" + response[i].ID_Documento_Detalle + "]'>" : '') + "</td>"
          +"<td class='text-center'>" + ((response[i].Nu_Estado_Lavado == 17 || response[i].Nu_Estado_Lavado==9) ? "<input type='checkbox' id='" + response[i].ID_Documento_Cabecera + "' class='check-iIdDocumentoCabecera' name='arrIdDocumentoCabecera[" + response[i].ID_Documento_Cabecera + "]'>" : '') + "</td>"
          +"<td class='text-center'>" + response[i].Fe_Emision_Hora + "</td>"
          +"<td class='text-center'>" + response[i].Fe_Entrega + "</td>"
          +"<td class='text-center'>" + response[i].No_Tipo_Documento_Breve + "</td>"
          +"<td class='text-center'>" + response[i].ID_Serie_Documento + "</td>"
          +"<td class='text-center'>" + response[i].ID_Numero_Documento + "</td>"
          +"<td class='text-left'>" + response[i].No_Entidad + "</td>"
          +"<td class='text-left'>" + response[i].No_Producto + "</td>"
          //+"<td class='text-center'>" + response[i].No_Signo + "</td>"
          //+"<td class='text-right'>" + (response[i].ID_Tipo_Documento != 5 ? '' : '-') + number_format(total_s, 2) + "</td>"
          //+"<td class='text-right'>" + (response[i].ID_Tipo_Documento != 5 ? '' : '-') + number_format(total_s_saldo, 2) + "</td>"
          +"<td class='text-center'>" + sAccionButton + "</td>"
          +"<td class='text-center'><span class='label label-" + response[i].No_Class_Estado_Lavado + "'>" + response[i].No_Estado_Lavado + "</span><i class='fa fa-refresh fa-spin fa-lg fa-fw'></i></td>"
        +"</tr>";
        
        //sum_total_s += total_s;
        //sum_total_s_saldo += total_s_saldo;
      }
      
      tr_foot =
      "<tfoot>"
        +"<tr>"
          +"<th class='text-center'><input type='checkbox' onclick='checkAllMenuFooter();' id='check-AllMenuFooter'></th>"
          +"<th class='text-right' colspan='10'></th>"
          +"<th class='text-center'><button type='button' class='btn btn-success btn-block btn-save' onclick='cambiarEstadoLavado();'><span class='fa fa-truck'></span> Enviar</button></th>"
        +"</tr>"
      +"</tfoot>";
    } else {
      if( response.sMessageSQL !== undefined ) {
        console.log(response.sMessageSQL);
      }
      tr_body +=
      "<tr>"
        +"<td colspan='12' class='text-center'>" + response.sMessage + "</td>"
      + "</tr>";
    } // ./ if arrData
    
    $( '#div-proceso_planta_lavanderia' ).show();
    $( '#table-proceso_planta_lavanderia > tbody' ).append(tr_body);
    $( '#table-proceso_planta_lavanderia > tbody' ).after(tr_foot);
    
    $( '#btn-html_proceso_planta_lavanderia' ).text('');
    $( '#btn-html_proceso_planta_lavanderia' ).append( '<i class="fa fa-search"></i> Buscar' );
    $( '#btn-html_proceso_planta_lavanderia' ).attr('disabled', false);
  }, 'JSON')
  .fail(function(jqXHR, textStatus, errorThrown) {
    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
    
    $( '#modal-message' ).modal('show');
    $( '.modal-message' ).addClass( 'modal-danger' );
    $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
    setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
    
    //Message for developer
    console.log(jqXHR.responseText);
    
    $( '#btn-html_proceso_planta_lavanderia' ).text('');
    $( '#btn-html_proceso_planta_lavanderia' ).append( '<i class="fa fa-search"></i> Buscar' );
    $( '#btn-html_proceso_planta_lavanderia' ).attr('disabled', false);
  });
}

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
  if ( $('#form-proceso_planta_lavanderia').serialize() == '' ) {
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
  } // if - else validacion de checkbox
}

function finalizarPedido(arrParams){
  limpiarValores();

  $( '#hidden-iIdDocumentoCabeceraFinalizado' ).val( arrParams.iIdDocumentoCabecera );
  $( '#hidden-iIdDocumentoDetalleFinalizado' ).val( arrParams.iIdDocumentoDetalle );

  $( '#modal-header-label-title_finalizado' ).text( arrParams.sTipoDocumento + ' - ' + arrParams.sSerieDocumento + ' - ' + arrParams.sNumeroDocumento);

  //$( '#h4-datos_item_finalizado' ).text( 'Cantidad: ' + arrParams.fCantidadItem + ' - Item: ' + arrParams.sNombreItem );

  $( '.modal-header-label-subtitle_nota_finalizado' ).hide();
  if ( arrParams.sNota != null && arrParams.sNota.length>0 ) {
    $( '.modal-header-label-subtitle_nota_finalizado' ).show();
    $( '#modal-header-label-subtitle_nota_finalizado' ).text( 'Nota de caja: ' + clearHTMLTextArea(arrParams.sNota) );
  }

  $( '.modal-finalizar_pedido_Embolsado' ).modal('show');
  $( '.modal-finalizar_pedido_Embolsado' ).on('shown.bs.modal', function() {
    $( '#tel-pin_finalizado' ).focus();
  });
  $( '#div-pin_finalizado' ).show();

  $( '#btn-salir' ).off('click').click(function () {
    limpiarValores();
  });
}

function generarPedidoFinalizado(){
  var arrCabecera=Array();
  arrCabecera = {
    'iIdDocumentoCabecera' : $( '#hidden-iIdDocumentoCabeceraFinalizado' ).val(),
    'iIdDocumentoDetalle' : $( '#hidden-iIdDocumentoDetalleFinalizado' ).val(),
    'iIdEntidad' : $( '#hidden-iIdEntidadFinalizado' ).val(),
		'sFinalEmbolsado' : $('[name="Txt_Embolsado"]').val(),
  };
  var $url = base_url + 'Ventas/EmbolsadoController/actualizarPedido';
  var $arrParamsPost = {
    arrCabecera : arrCabecera,
  };

  $( '#btn-finalizar_pedido' ).text('');
  $( '#btn-salir' ).attr('disabled', true);
  $( '#btn-finalizar_pedido' ).attr('disabled', true);
  $( '#btn-finalizar_pedido' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

  $.post( $url, $arrParamsPost, function( response ) {
    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
    $( '#modal-message' ).modal('show');

    if ( response.sStatus=='success' ) {
      getReporteHTML();

      $( '.modal-tomar_pedido_Embolsado' ).modal('hide');

      $( '.modal-message' ).addClass( 'modal-' + response.sStatus );
      $( '.modal-title-message' ).text( response.sMessage );
      setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
      
      limpiarValores();
    } else {
      $( '.modal-message' ).addClass( 'modal-' + response.sStatus );
      $( '.modal-title-message' ).text( response.sMessage );
      setTimeout(function() {$('#modal-message').modal('hide');}, 3100);
    }
    
    $( '#btn-finalizar_pedido' ).text('');
    $( '#btn-finalizar_pedido' ).append( 'Generar comprobante' );
    $( '#btn-finalizar_pedido' ).attr('disabled', false);
    $( '#btn-salir' ).attr('disabled', false);
  }, 'json')
  .fail(function(jqXHR, textStatus, errorThrown) {
    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
    
    $( '#modal-message' ).modal('show');
    $( '.modal-message' ).addClass( 'modal-danger' );
    $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
    setTimeout(function() {$('#modal-message').modal('hide');}, 3100);
    
    //Message for developer
    console.log(jqXHR.responseText);

    $( '#btn-finalizar_pedido' ).text('');
    $( '#btn-finalizar_pedido' ).attr('disabled', false);
    $( '#btn-finalizar_pedido' ).append( 'Iniciar Pedido' );
    $( '#btn-salir' ).attr('disabled', false);
  })
}

function limpiarValores(){
  $( '.modal-tomar_pedido_Embolsado' ).modal('hide');
  $( '.modal-finalizar_pedido_Embolsado' ).modal('hide');

  $( '#div-pin' ).show();
  $( '#hidden-iIdDocumentoCabecera' ).val( '' );
  $( '#hidden-iIdEntidad' ).val( '' );
  $( '#tel-pin' ).val( '' );

  $( '#h4-datos_personal_finalizado' ).text( '' );
  $( '#div-detalle_item_pedido' ).hide();
  $( '#txt-cantidad' ).val( '1' );
  $( '#txt-item' ).val( '' );
  $( '#table-detalle_item_pedido' ).hide();
  $( '#table-detalle_item_pedido tbody' ).empty();
  
  $( '#div-pin_finalizado' ).show();
  $( '#tel-pin_finalizado' ).val( '' );
  $( '#hidden-iIdDocumentoCabeceraFinalizado' ).val( '' );
  $( '#hidden-iIdEntidadFinalizado' ).val( '' );
  $( '.div-detalle_item_pedido_finalizado' ).hide();
  $('[name="Txt_Embolsado"]').val( '' );
  $( '#table-detalle_item_pedido_finalizado' ).hide();
  $( '#table-detalle_item_pedido_finalizado tbody' ).empty();

  $( '#btn-procesar_pedido' ).hide();
}