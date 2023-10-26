var url, iIDCaja, sEstadoxCaja, iIDMatriculaEmpleado, iIdMonedaCajaPos, html_saldo_x_caja_y_moneda='', sIpBD='';

$(function () {
	$(".toggle-password").click(function () {
		$(this).toggleClass("fa-eye fa-eye-slash");
		var $pwd = $(".pwd");
		if ($pwd.attr('type') == "password") {
			$pwd.attr('type', 'text');
		} else {
			$pwd.attr('type', 'password');
		}
	});

	$( '.select2' ).select2();
	$( '#span-verificar_autorizacion_venta' ).html( '' );
	// INICIO Punto Venta
	// Modal Get monedas
	url = base_url + 'HelperController/getMonedas';
	$.post( url , function( response ){
		$( '#cbo-moneda' ).html('');
		var iCantidadRegistros=response.length;
		for (var i = 0; i < iCantidadRegistros; i++)
			$( '#cbo-moneda' ).append( '<option value="' + response[i]['ID_Moneda'] + '" data-no_signo="' + response[i]['No_Signo'] + '">' + response[i]['No_Moneda'] + '</option>' );
	}, 'JSON');

	url = base_url + 'HelperController/getTipoOperacionCaja';
	$.post( url, {Nu_Tipo:3}, function( response ){//3 = Ingreso de caja
	  $( '#hidden-id_tipo_operacion_caja' ).val( response[0].ID_Tipo_Operacion_Caja );
	}, 'JSON');

	$.post( base_url + 'HelperController/getToken', function( response ){
		if ( response.Nu_Verificar_Autorizacion_Venta == 0 ) {
			var arrParams = {};
			getPos(arrParams);
		} else {// /. if verificar autorizacion de venta
			$( '#h4-verificar_autorizacion_venta' ).html( 'Verificando caja  <i class="fa fa-refresh fa-spin"></i>' );
			
			getUserIP(function(ip){
				var arrParams = {
					'sIpClient' : ip,
				};
				getPos(arrParams);
				$( '#h4-verificar_autorizacion_venta' ).html( '' );
			});
		}
	}, 'JSON');

	// Li Modal Matricular personal y aperturar caja
	$(".ul-lista_pos").on("click", ".li-pos", function(event){
		sIpBD = $(this).data('ip');
		if ( $(this).data('estado_caja') == 'danger' ) {//danger = cierre de caja
			$( '#hidden-id_pos' ).val( $(this).val() );//ID_Pos
			$.post( base_url + 'HelperController/getToken', function( response ){
				if ( response.Nu_Verificar_Autorizacion_Venta == 0 ) {
					$( '.modal-personal' ).modal('show');
					
					$( '.modal-personal' ).on('shown.bs.modal', function() {
						$( '#tel-nu_documento_identidad_personal' ).focus();
						$('#form-matricula_personal_apertura_caja').attr('autocomplete', 'off');

						$('input.hotkey-btn-add_matricular_personal_apertua_caja').bind('keydown', 'return', function(e) {
							e.preventDefault();
							addMatriculaPersonalAperturaCaja();
						});
					})
				} else {
					getUserIP(function(ip){
						if ( ip == sIpBD ) {
							$( '.modal-personal' ).modal('show');
				
							$( '.modal-personal' ).on('shown.bs.modal', function() {
								$( '#tel-nu_documento_identidad_personal' ).focus();
								$('#form-matricula_personal_apertura_caja').attr('autocomplete', 'off');
		
								$('input.hotkey-btn-add_matricular_personal_apertua_caja').bind('keydown', 'return', function(e) {
									e.preventDefault();
									addMatriculaPersonalAperturaCaja();
								});
							})
						} else {
							$modal_msg_stock = $( '.modal-message' );
							$modal_msg_stock.modal('show');
					
							$modal_msg_stock.removeClass('modal-danger modal-warning modal-success');
							$modal_msg_stock.addClass('modal-warning');
					
							$( '.modal-title-message' ).text('Caja no autorizada');
					
							setTimeout(function() {$modal_msg_stock.modal('hide');}, 1300);
						}
					})
				}
			}, 'JSON');
		} else {
			$( '#hidden-id_matricula_empleado' ).val( $(this).data('id_matricula_empleado') );
			$( '#hidden-id_moneda_caja_pos' ).val( $(this).data('id_moneda_caja_pos') );
			$.post( base_url + 'HelperController/getToken', function( response ){
				if ( response.Nu_Verificar_Autorizacion_Venta == 0 ) {
					$( '.modal-inicio_sesion_caja_x_personal' ).modal('show');

					$( '.modal-inicio_sesion_caja_x_personal' ).on('shown.bs.modal', function() {
						$( '#tel-Nu_Pin_Caja' ).focus();
					
						$('input.hotkey-btn-add_matricular_personal_apertua_caja').bind('keydown', 'return', function(e) {
							e.preventDefault();
							
							var arrParams = {
								'iIdMatriculaEmpleado' : $( '#hidden-id_matricula_empleado' ).val(),
								'iIdMonedaCajaPos' : $( '#hidden-id_moneda_caja_pos' ).val(),
								'iPin' : $( '#tel-Nu_Pin_Caja' ).val(),
							}

							verificarPersonalxPIN( arrParams );
						});
					})
				} else {
					getUserIP(function(ip){
						if ( ip == sIpBD ) {
							$( '.modal-inicio_sesion_caja_x_personal' ).modal('show');
		
							$( '.modal-inicio_sesion_caja_x_personal' ).on('shown.bs.modal', function() {
								$( '#tel-Nu_Pin_Caja' ).focus();
							
								$('input.hotkey-btn-add_matricular_personal_apertua_caja').bind('keydown', 'return', function(e) {
									e.preventDefault();
									var arrParams = {
										'iIdMatriculaEmpleado' : $( '#hidden-id_matricula_empleado' ).val(),
										'iIdMonedaCajaPos' : $( '#hidden-id_moneda_caja_pos' ).val(),
										'iPin' : $( '#tel-Nu_Pin_Caja' ).val(),
									}
		
									verificarPersonalxPIN( arrParams );
								});
							})
						} else {
							$modal_msg_stock = $( '.modal-message' );
							$modal_msg_stock.modal('show');
					
							$modal_msg_stock.removeClass('modal-danger modal-warning modal-success');
							$modal_msg_stock.addClass('modal-warning');
					
							$( '.modal-title-message' ).text('Caja no autorizada');
					
							setTimeout(function() {$modal_msg_stock.modal('hide');}, 1300);
						}
					});
				}
			}, 'JSON');
		}
	});

	// Button guardar personal y apertura de caja
	$( '#btn-save_personal' ).click(function (e) {
		e.preventDefault();
		addMatriculaPersonalAperturaCaja();
	});
	// FIN Punto Venta - Matricula Personal - Apertura Caja
	
	$( '#btn-ingresar_punto_venta' ).click(function (e) {
		e.preventDefault();
		var arrParams = {
			'iIdMatriculaEmpleado' : $( '#hidden-id_matricula_empleado' ).val(),
			'iIdMonedaCajaPos' : $( '#hidden-id_moneda_caja_pos' ).val(),
			'iPin' : $( '#tel-Nu_Pin_Caja' ).val(),
		}
		verificarPersonalxPIN(arrParams);
	});
}) // /. $(function)

/**
 * Get the user IP throught the webkitRTCPeerConnection
 * @param onNewIP {Function} listener function to expose the IP locally
 * @return undefined
 */
function getUserIP(onNewIP) { //  onNewIp - your listener function for new IPs
    //compatibility for firefox and chrome
    var myPeerConnection = window.RTCPeerConnection || window.mozRTCPeerConnection || window.webkitRTCPeerConnection;
    var pc = new myPeerConnection({
        iceServers: []
    }),
    noop = function() {},
    localIPs = {},
    ipRegex = /([0-9]{1,3}(\.[0-9]{1,3}){3}|[a-f0-9]{1,4}(:[a-f0-9]{1,4}){7})/g,
    key;

    function iterateIP(ip) {
        if (!localIPs[ip]) onNewIP(ip);
        localIPs[ip] = true;
    }

     //create a bogus data channel
    pc.createDataChannel("");

    // create offer and set local description
    pc.createOffer(function(sdp) {
        sdp.sdp.split('\n').forEach(function(line) {
            if (line.indexOf('candidate') < 0) return;
            line.match(ipRegex).forEach(iterateIP);
        });
        
        pc.setLocalDescription(sdp, noop, noop);
    }, noop); 

    //listen for candidate events
    pc.onicecandidate = function(ice) {
        if (!ice || !ice.candidate || !ice.candidate.candidate || !ice.candidate.candidate.match(ipRegex)) return;
        ice.candidate.candidate.match(ipRegex).forEach(iterateIP);
    };
}


function getPos(arrParams){
	url = base_url + 'HelperController/getPos';
	var sendData = {};
	$( '.ul-lista_pos' ).empty();
	$.post( url, sendData, function( response ){
		if ( response.sStatus=='success' ) {
			var arrPos = '', i = response.arrData.length, o=0, sSignoMoneda=0.00, fTotalSaldo=0.00, iIDMatriculaEmpleado=0, iIdMonedaCajaPos=0;
			if (i > 0) {
				for (var x = 0; x < i; x++) {
					if ( arrParams.sIpClient == response.arrData[x].Txt_Autorizacion_Venta_Serie_Disco_Duro || iVerificarAutorizacionVentaGlobal == 0 ) { // Verificar Serie HDD Localhost vs Serie HDD Cloud
						o=response.arrDataSaldoPos[response.arrData[x].ID_POS].length;
						for(var y=0; y < o; y++){// Ultimo saldo por POS
							html_saldo_x_caja_y_moneda = '';
							if ( response.arrDataSaldoPos[response.arrData[x].ID_POS][y].sStatus=='success') {
								sSignoMoneda=response.arrDataSaldoPos[response.arrData[x].ID_POS][y].arrData[0].No_Signo;
								fTotalSaldo=response.arrDataSaldoPos[response.arrData[x].ID_POS][y].arrData[0].Ss_Total;
								iIDMatriculaEmpleado=response.arrDataSaldoPos[response.arrData[x].ID_POS][y].arrData[0].ID_Matricula_Empleado;
								iIdMonedaCajaPos=response.arrDataSaldoPos[response.arrData[x].ID_POS][y].arrData[0].ID_Moneda_Caja_Pos;
								html_saldo_x_caja_y_moneda += '<label style="font-size: 14px;">Fecha &nbsp;</label>';
								html_saldo_x_caja_y_moneda += '<label style="font-size: 14px; font-weight: normal;">';
								html_saldo_x_caja_y_moneda += ParseDateHour(response.arrDataSaldoPos[response.arrData[x].ID_POS][y].arrData[0].Fe_Movimiento);
								html_saldo_x_caja_y_moneda += '</label>';
								html_saldo_x_caja_y_moneda += '<br>';
								html_saldo_x_caja_y_moneda += '<label style="font-size: 14px;">Saldo &nbsp;</label>';
								html_saldo_x_caja_y_moneda += '<label style="font-size: 14px; font-weight: normal;">';
								html_saldo_x_caja_y_moneda += sSignoMoneda;
								html_saldo_x_caja_y_moneda += '&nbsp';
								html_saldo_x_caja_y_moneda += fTotalSaldo;
								html_saldo_x_caja_y_moneda += '&nbsp';
								html_saldo_x_caja_y_moneda += '</label>';
								if ( response.arrDataSaldoPos[response.arrData[x].ID_POS][y].sStatus=='success') {
									sSignoMoneda=response.arrDataSaldoPos[response.arrData[x].ID_POS][y].arrData[0].No_Signo;
									fTotalSaldo=response.arrDataSaldoPos[response.arrData[x].ID_POS][y].arrData[0].Ss_Total;
								}
								if ( response.arrDataSaldoPos[response.arrData[x].ID_POS][y].sStatus=='success') {
									html_saldo_x_caja_y_moneda += '<br>';
									html_saldo_x_caja_y_moneda +='<span class="div-apertura_caja-estado label label-'+ response.arrDataSaldoPos[response.arrData[x].ID_POS][y].arrData[0].No_Class_Estado +'" >'+response.arrDataSaldoPos[response.arrData[x].ID_POS][y].arrData[0].No_Tipo_Operacion_Caja+'</span>';
									sEstadoxCaja=response.arrDataSaldoPos[response.arrData[x].ID_POS][y].arrData[0].No_Class_Estado;
								}
							} else {
								html_saldo_x_caja_y_moneda='<label>No hay dinero</label>';
								sEstadoxCaja='danger';//Porque nunca se aperturo caja
							}
						}// ./ Ultimo Saldo por POS

						arrPos +='<div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 div-caja_chica">';
						arrPos +='<li style="height: 220px; margin: 2%; box-shadow: 0 5px 7px rgba(0,0,0,0.12), 0 3px 4px rgba(0,0,0,0.24);" class="li-pos list-group-item caja_chica" value='+response.arrData[x].ID_POS+' data-id_matricula_empleado="'+iIDMatriculaEmpleado+'" data-id_moneda_caja_pos="'+iIdMonedaCajaPos+'" data-estado_caja="'+sEstadoxCaja+'" data-ip="' + response.arrData[x].Txt_Autorizacion_Venta_Serie_Disco_Duro + '">';
						arrPos +='<div style="text-align:center;"><label style="font-size: 20px; text-align:center;">';
						if (response.arrData[x].No_Pos == null || response.arrData[x].No_Pos.length == 0)
							arrPos += 'Caja '+response.arrData[x].Nu_Pos;
						else
							arrPos += '(' + response.arrData[x].Nu_Pos + ') ' + response.arrData[x].No_Pos;
						arrPos +='</label></div>';
						o=response.arrDataMatriculaPersonal[response.arrData[x].ID_POS].length;
						for(var y=0; y < o; y++){// Matricula de personal por POS
							var iIDPersonal=0;
							var sNombrePersonal='Ninguno';

							if ( response.arrDataMatriculaPersonal[response.arrData[x].ID_POS][y].sStatus=='success') {
								iIDPersonal=response.arrDataMatriculaPersonal[response.arrData[x].ID_POS][y].arrData[0].ID_Entidad;
								sNombrePersonal=response.arrDataMatriculaPersonal[response.arrData[x].ID_POS][y].arrData[0].No_Entidad;
							}
							arrPos +='<input type="hidden" id="hidden-id_personal" value="'+iIDPersonal+'">';
							arrPos +='<label style="font-size: 14px; font-weight: normal;">' + sNombrePersonal + '</label>';
						}
						arrPos +='<br>';
						arrPos += html_saldo_x_caja_y_moneda;// Ultimo saldo por POS
						arrPos +='<br>';
						arrPos +='</li>';
						arrPos +='</div>';
					} else {
						//$( '#h4-msg_punto_venta' ).html( 'Caja ' + response.arrData[x].Nu_Pos + ' no autorizada' );
						console.log( 'Caja ' + response.arrData[x].Nu_Pos + ' no autorizada' );
					} // /. if - else -> Verificar Serie HDD Localhost vs Serie HDD Cloud
				}// ./ for -> listar cajas
			} else
				arrPos = '';
			$( '.ul-lista_pos' ).append(arrPos);
		} else {
			if( response.sMessageSQL !== undefined ) {
				console.log(response.sMessageSQL);
			}
			console.log(response.sMessage);
			$( '.ul-lista_pos' ).empty();
		}// /. if - else listar pos
	}, 'JSON');
}

function addMatriculaPersonalAperturaCaja(){
	if ( $( '#tel-nu_documento_identidad_personal' ).val().length == 0){
		$( '#tel-nu_documento_identidad_personal' ).closest('.form-group').find('.help-block').html('Ingresar valor');
		$( '#tel-nu_documento_identidad_personal' ).closest('.form-group').removeClass('has-success').addClass('has-error');
  
		scrollToError($('.modal-personal .modal-body'), $( '#tel-nu_documento_identidad_personal' ));
	} else {
		$( '.help-block' ).empty();
		$( '#tel-nu_documento_identidad_personal' ).closest('.form-group').removeClass('has-error');

		var fAperturaCaja = parseFloat($( '#txt-ss_apertura_caja' ).val());
		if(isNaN(fAperturaCaja))
			fAperturaCaja = 0;

		var arrParams = {
			iIdPos : $( '#hidden-id_pos' ).val(),
			iPin : $( '#tel-nu_documento_identidad_personal' ).val(),
			iIdTipoOperacionCaja : $( '#hidden-id_tipo_operacion_caja' ).val(),
			iIdMoneda : $( '#cbo-moneda' ).val(),
			fAperturaCaja : fAperturaCaja,
			sNotaCaja : $( '[name="area-txt_nota_caja"]' ).val(),
		}
		
		$( '#btn-save_personal' ).text('');
		$( '#btn-save_personal' ).attr('disabled', true);
		$( '#btn-save_personal' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

		url = base_url + 'PuntoVenta/AperturaCajaController/addMatriculaPersonal';
		$.post( url, arrParams, function( response ){
			$( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
			$( '#modal-message' ).modal('show');

			if ( response.sStatus=='success' ) {
				$( '#form-matricula_personal_apertura_caja' )[0].reset();

				$( '.modal-personal' ).modal('hide');
				$( '.modal-message' ).addClass( 'modal-' + response.sStatus );
				$( '.modal-title-message' ).text( response.sMessage );
				setTimeout(function() {$('#modal-message').modal('hide');}, 1100);

				if (iIdTipoRubroEmpresaGlobal == 11)//11 = Restaurante
					window.location = base_url + 'PuntoVenta/POSRestauranteController/verEscenariosRestaurante';
				else if (iIdTipoRubroEmpresaGlobal == 3)//Lavanderia VAPI
					window.location = base_url + 'PuntoVenta/POSLavanderiaController/verPOS';
				else if (iIdTipoRubroEmpresaGlobal == 1)//Farmacia
					window.location = base_url + 'PuntoVenta/POSFarmaciaController/verPOS';
				else
					window.location = base_url + 'PuntoVenta/POSController/verPOS';
			} else {
				$( '.modal-message' ).addClass( 'modal-' + response.sStatus );
				$( '.modal-title-message' ).text( response.sMessage );
				setTimeout(function() {$('#modal-message').modal('hide');}, 5400);
			}
			
			$( '#btn-save_personal' ).text('');
			$( '#btn-save_personal' ).append( 'Vender' );
			$( '#btn-save_personal' ).attr('disabled', false);
		}, 'JSON')
		.fail(function(jqXHR, textStatus, errorThrown) {
			$( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
			
			$( '#modal-message' ).modal('show');
			$( '.modal-message' ).addClass( 'modal-danger' );
			$( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
			setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
			
			//Message for developer
			console.log(jqXHR.responseText);
			
			$( '#btn-save_personal' ).text('');
			$('#btn-save_personal').append( 'Vender' );
			$( '#btn-save_personal' ).attr('disabled', false);
		});
	}
}

function verificarPersonalxPIN(arrParams){
	if ( $( '#tel-Nu_Pin_Caja' ).val().length == 0){
		$( '#tel-Nu_Pin_Caja' ).closest('.form-group').find('.help-block').html('Ingresar PIN');
		$( '#tel-Nu_Pin_Caja' ).closest('.form-group').removeClass('has-success').addClass('has-error');
  
		scrollToError($('.modal-inicio_sesion_caja_x_personal .modal-body'), $( '#tel-Nu_Pin_Caja' ));
	} else {
		$( '.help-block' ).empty();
		$( '#tel-Nu_Pin_Caja' ).closest('.form-group').removeClass('has-error');

		$( '#btn-ingresar_punto_venta' ).text('');
		$( '#btn-ingresar_punto_venta' ).attr('disabled', true);
		$( '#btn-ingresar_punto_venta' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

		url = base_url + 'PuntoVenta/AperturaCajaController/verificarPersonalxPIN';
		$.post( url, arrParams, function( response ){
			$( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
			$( '#modal-message' ).modal('show');
			$( '.modal-message' ).css("z-index", "2000");

			if ( response.sStatus=='success' ) {
				$( '#hidden-id_matricula_empleado' ).val( '' );
				$( '#hidden-id_moneda_caja_pos' ).val( '' );
				$( '#tel-Nu_Pin_Caja' ).val( '' );

				$( '.modal-inicio_sesion_caja_x_personal' ).modal('hide');
				$( '.modal-message' ).addClass( 'modal-' + response.sStatus );
				$( '.modal-title-message' ).text( response.sMessage );
				setTimeout(function() {$('#modal-message').modal('hide');}, 1100);

				if (iIdTipoRubroEmpresaGlobal == 11)//11 = Restaurante
					window.location = base_url + 'PuntoVenta/POSRestauranteController/verEscenariosRestaurante';
				else if (iIdTipoRubroEmpresaGlobal == 3)//Lavanderia VAPI
					window.location = base_url + 'PuntoVenta/POSLavanderiaController/verPOS';
				else if (iIdTipoRubroEmpresaGlobal == 1)//Farmacia
					window.location = base_url + 'PuntoVenta/POSFarmaciaController/verPOS';
				else
					window.location = base_url + 'PuntoVenta/POSController/verPOS';
			} else {
				$( '.modal-message' ).addClass( 'modal-' + response.sStatus );
				$( '.modal-title-message' ).text( response.sMessage );
				setTimeout(function() {$('#modal-message').modal('hide');}, 5400);
			}
			
			$( '#btn-ingresar_punto_venta' ).text('');
			$('#btn-ingresar_punto_venta').append( 'Vender' );
			$( '#btn-ingresar_punto_venta' ).attr('disabled', false);
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
			
			$( '#btn-ingresar_punto_venta' ).text('');
			$('#btn-ingresar_punto_venta').append( 'Vender' );
			$( '#btn-ingresar_punto_venta' ).attr('disabled', false);
		});
	}// /. if - else validacion caja aperturada
}