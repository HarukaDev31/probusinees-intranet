var url, iIDCaja, sEstadoxCaja, html_saldo_x_caja_y_moneda='';

$(function () {
	$( '#modal-loader' ).modal('show');

	url = base_url + 'HelperController/getTipoOperacionCaja';
	$.post( url, {Nu_Tipo:3}, function( response ){//3 = Apertura de caja
		$( '#hidden-id_tipo_operacion_caja_apertura' ).val( response[0].ID_Tipo_Operacion_Caja );
	}, 'JSON');

	url = base_url + 'HelperController/getTipoOperacionCaja';
	$.post( url, {Nu_Tipo:4}, function( response ){//4 = Cierre de caja
		$( '#hidden-id_tipo_operacion_caja' ).val( response[0].ID_Tipo_Operacion_Caja );
  		$( '#modal-loader' ).modal('hide');
	}, 'JSON');

	$( "#txt-ss_total_depositado" ).focus();
	$( "#txt-ss_total_depositado" ).select();

	$( "#txt-ss_total_depositado" ).keyup(function(){
		$( '#txt-ss_total_diferencia' ).val( parseFloat($(this).val()) - parseFloat($('#hidden-ss_total_liquidar').val()) );
	});

	// Button guardar cierre de caja
	$( '#btn-save_cierre_caja' ).click(function(){
		addCierreCaja();
	});
	// ./ Button guardar cierre de caja

	// Combinacion de tecla - cierre de caja
	$(document).bind('keydown', 'return', function(){
		addCierreCaja();
	});
	
	$('input.hotkey-save_cierre_caja').bind('keydown', 'return', function(){
		addCierreCaja();
	});
	// Combinacion de tecla - cierre de caja
})

function addCierreCaja(){
	var $modal_delete = $('.modal-message-delete');
	$modal_delete.modal('show');

	$('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
	$('.modal-message-delete').addClass('modal-success');

	$('.modal-title-message-delete').text('¿Deseas cerrar caja?');

	$('#btn-cancel-delete').off('click').click(function () {
	    $modal_delete.modal('hide');
  	});

  	$('#btn-save-delete').off('click').click(function () {
	    $modal_delete.modal('hide');
	    
		if ( $( '#txt-ss_total_depositado' ).val().length == 0){
			$( '#txt-ss_total_depositado' ).closest('.form-group').find('.help-block').html('Ingresar monto');
			$( '#txt-ss_total_depositado' ).closest('.form-group').removeClass('has-success').addClass('has-error');
	  
			scrollToError($('html, body'), $( '#txt-ss_total_depositado' ));
		} else {
			var arrParams = {
				iIdTipoOperacionCajaApertura : $( '#hidden-id_tipo_operacion_caja_apertura' ).val(),
				iIdTipoOperacionCaja : $( '#hidden-id_tipo_operacion_caja' ).val(),
				iIdMoneda : $( '#cbo-moneda' ).val(),
				fTotalLiquidar : $( '#hidden-ss_total_liquidar' ).val(),
				fTotalDepositado : $( '#txt-ss_total_depositado' ).val(),
				sNotaCaja : $( '[name="area-txt_cierre_caja"]' ).val(),
			}
			
			$( '#btn-save_cierre_caja' ).text('');
			$( '#btn-save_cierre_caja' ).attr('disabled', true);
			$( '#btn-save_cierre_caja' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
			
			$( '#modal-loader' ).modal('show');

			url = base_url + 'PuntoVenta/CierreCajaController/addCierreCaja';
			$.post( url, arrParams, function( response ){
				$( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
				$( '#modal-message' ).modal('show');

				if ( response.sStatus=='success' ) {
					$( '#hidden-ss_total_liquidar' ).val( '' );
					$( '#txt-ss_total_liquidar_referencial' ).val( '' );
					$( '#txt-ss_total_depositado' ).val( '' );
					$( '#txt-ss_total_diferencia' ).val( '' );
					$( '[name="area-txt_cierre_caja"]' ).val( '' );

					$( '#table-ventas_por_categoria tbody' ).empty();
					$( '#table-movimientos_caja tbody' ).empty();
					$( '#table-ventas_generales tbody' ).empty();

					$( '.modal-message' ).addClass( 'modal-' + response.sStatus );
					$( '.modal-title-message' ).text( response.sMessage );
					setTimeout(function() {$('#modal-message').modal('hide');}, 1100);

					// Dirigir a la opción apertura de caja
					setTimeout(function() {
						// Mandar a imprimir impresora
						var arrParams = {
							'sTipoCodificacion' : 'normal',
							'sAccion' : 'imprimir',
							'iIdMatriculaEmpleado' : response.iIdMatriculaEmpleado,
							'iIdEnlaceAperturaCaja' : response.iIdEnlaceAperturaCaja,
							'iIdEnlaceCierreCaja': response.iIdEnlaceCierreCaja,
							'sUrlAperturaCaja' : base_url + 'PuntoVenta/AperturaCajaController/listar',
						};
						if (Nu_Tipo_Lenguaje_Impresion_Pos == 1) {//1=HTML
							formatoImpresionLiquidacionCaja(arrParams);
						} else {
							window.open(base_url + "Ventas/VentaController/generarArqueoPOSPDF/" + response.iIdMatriculaEmpleado + '/' + response.iIdEnlaceAperturaCaja + '/' + response.iIdEnlaceCierreCaja, "_blank", "location=yes,top=80,left=800,width=720,height=550,scrollbars=yes,status=yes");
							window.location = base_url + 'PuntoVenta/AperturaCajaController/listar';
						}
						$( '#modal-loader' ).modal('hide');
					}, 1500);
				} else {
					$('#modal-loader').modal('hide');

					$( '.modal-message' ).addClass( 'modal-' + response.sStatus );
					$( '.modal-title-message' ).text( response.sMessage );
					setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
				}
				
				$( '#btn-save_cierre_caja' ).text('');
				$( '#btn-save_cierre_caja' ).append( 'Guardar' );
				$( '#btn-save_cierre_caja' ).attr('disabled', false);
			}, 'JSON')
			.fail(function(jqXHR, textStatus, errorThrown) {
				$( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
				
				$( '#modal-message' ).modal('show');
				$( '.modal-message' ).addClass( 'modal-danger' );
				$( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
				setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
				
				//Message for developer
				console.log(jqXHR.responseText);
				
				$( '#btn-save_cierre_caja' ).text('');
				$( '#btn-save_cierre_caja' ).append( 'Guardar' );
				$( '#btn-save_cierre_caja' ).attr('disabled', false);
			});
		} // ./ if - else
    
  	});
}