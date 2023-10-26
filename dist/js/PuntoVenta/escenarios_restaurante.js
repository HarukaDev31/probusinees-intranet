var url, iIdPedidoCabecera = 0;

// /. Variables Globales

$(function () {
	$( '.select2' ).select2();
	$( '[data-mask]' ).inputmask();

	$('.div-escenario').hide();
	$('.div-escenario-no_creado').show();

	allEscenarioOptions( 0 );
	allEscenarioMesas( $('#hidden-iIdEscenarioRestaurante').val() );

	$(".div-lista_escenario_mesas").on("click", ".li-item_mesa_view", function (event) {
		iIdPedidoCabecera = ($(this).data('id_pedido_cabecera') != null ? $(this).data('id_pedido_cabecera') : 0);
		if ($(this).data('id_mesa_restaurante') > 0 )
			window.location = base_url + 'PuntoVenta/POSRestauranteController/verPOSRestaurante/' + $('#hidden-iIdEscenarioRestaurante').val() + '/' + $(this).data('id_mesa_restaurante') + '/' + iIdPedidoCabecera;
		else
			alert('No hay mesa seleccionada');
	})

	$(".div-lista_escenario_mesas").on("click", ".li-add-item_mesa", function (event) {
		$('[name="EID_Mesa_Restaurante"]').val('');
		$('[name="ENo_Mesa_Restaurante"]').val('');
		$('[name="No_Mesa_Restaurante"]').val('');

		url = base_url + 'PuntoVenta/POSRestauranteController/allEscenario/0';
		$.post(url, function (response) {
			$('#btn-modal-footer-add-mesas').attr('disabled', true);
			if (response.status == 'success') {
				$('.div-escenario').show();
				$('.div-escenario-no_creado').hide();
				$('#modal-add-mesas').modal('show');

				$('#btn-modal-footer-add-mesas').attr('disabled', false);

				$('#cbo-escenario').html('<option value="" selected="selected">- Seleccionar -</option>');
				var iTotalRegistros = response.result.length, response = response.result, selected = '';
				for (var i = 0; i < iTotalRegistros; i++) {
					selected = '';
					if ($('#hidden-iIdEscenarioRestaurante').val() == response[i].ID_Escenario_Restaurante)
						selected = 'selected="selected"';
					$('#cbo-escenario').append('<option value="' + response[i].ID_Escenario_Restaurante + '" ' + selected + '>' + response[i].No_Escenario_Restaurante + '</option>');
				}

				$('#modal-add-mesas').on('shown.bs.modal', function () {
					$('[name="No_Mesa_Restaurante"]').focus();
				})

				$('#cbo-estado').html('<option value="1">Activo</option>');
				$('#cbo-estado').append('<option value="0">Inactivo</option>');
			} else {
				$( '.div-escenario' ).hide();
				$( '.div-escenario-no_creado' ).show();

				$('#modal-add-mesas').modal('show');
			}
		}, 'JSON')
		.fail(function (jqXHR, textStatus, errorThrown) {
			$('.modal-message').removeClass('modal-danger modal-warning modal-success');

			$('#modal-message').modal('show');
			$('.modal-message').addClass('modal-danger');
			$('.modal-title-message').text(textStatus + ' [' + jqXHR.status + ']: ' + errorThrown);
			setTimeout(function () { $('#modal-message').modal('hide'); }, 6000);

			//Message for developer
			console.log(jqXHR.responseText);
		});
	});

	$('#btn-modal-footer-add-mesas').click(function () {
		crudEscenarioMesa();
	})

	$('.btn-add-escenario_mesas').click(function () {
		$('#modal-add-mesas').modal('hide');
		$('#modal-crud-escenario_mesas').on('shown.bs.modal', function () {
			$('[name="No_Escenario_Restaurante"]').focus();

			$('.modal-title').text('Agregar Escenario');

			$('[name="EID_Mesa_Restaurante"]').val('');
			$('[name="ENo_Mesa_Restaurante"]').val('');
			$('[name="No_Mesa_Restaurante"]').val('');
		})
	})

	$('#btn-modal-footer-add-escenario_mesas').click(function () {
		crudEscenario();
	})

	$(document).on('click', '.btn-view-escenario_mesas', function () {
		$('#hidden-iIdEscenarioRestaurante').val( $(this).data('id') );
		allEscenarioMesas( $(this).data('id') );
	})

	$(document).on('click', '.btn-all-escenario_mesas', function () {
		var $url = base_url + 'PuntoVenta/POSRestauranteController/allEscenario/0';
		$('#table-administrar_escenarios > tbody').empty();
		$.post($url, function (response) {
			html_escenario_mesas = '';
			if (response.status == 'success') {
				var iTotalRegistros = response.result.length, response = response.result, tr_body = '';
				for (var i = 0; i < iTotalRegistros; i++) {
					tr_body += '<tr>'
						+ '<td class="text-left" style="display: none;">' + response[i].ID_Escenario_Restaurante + '</td>'
						+'<td class="text-center">' + response[i].No_Escenario_Restaurante + '</td>'
						+'<td class="text-center"><a onclick="verEscenario(' + response[i].ID_Escenario_Restaurante + ')" href="#">Modificar</a></td>'
						+'<td class="text-center"><a onclick="eliminarEscenario(' + response[i].ID_Escenario_Restaurante + ')" href="#">Eliminar</a></td>'
					+ "</tr>";
				}
			} else {
				if (response.sMessageSQL !== undefined) {
					console.log(response.sMessageSQL);
				}
				tr_body +=
					"<tr>"
					+ "<td colspan='10' class='text-center'>" + response.sMessage + "</td>"
					+ "</tr>";
			} // ./ if arrData

			$('#table-administrar_escenarios > tbody').append(tr_body);
		}, 'json')
		.fail(function (jqXHR, textStatus, errorThrown) {
			$('.modal-message').removeClass('modal-danger modal-warning modal-success');

			$('#modal-message').modal('show');
			$('.modal-message').addClass('modal-danger');
			$('.modal-title-message').text(textStatus + ' [' + jqXHR.status + ']: ' + errorThrown);
			setTimeout(function () { $('#modal-message').modal('hide'); }, 6000);

			//Message for developer
			console.log(jqXHR.responseText);
		})
	})

	$(document).on('click', '.btn-group > .btn', function () {
		$(this).addClass("active").siblings().removeClass("active");
	});
}); // ./ document-ready

function allEscenarioOptions( iIdEscenarioRestaurante ){
	$('.div-crud-escenario_mesas').html( '' );
	var html_escenario_mesas = '', html_escenario_mesas_crud = '';
	var $url = base_url + 'PuntoVenta/POSRestauranteController/allEscenario/' + iIdEscenarioRestaurante;
	$.post($url, function (response) {
		html_escenario_mesas = '';
		if (response.status == 'success') {
			var iTotalRegistros = response.result.length, response = response.result;
			for (var i = 0; i < iTotalRegistros; i++)
				html_escenario_mesas += '<button type="button" id="btn-escenario-' + response[i].ID_Escenario_Restaurante + '" data-id="' + response[i].ID_Escenario_Restaurante + '" class="btn btn-success btn-view-escenario_mesas">' + response[i].No_Escenario_Restaurante + '</button>';
			$('.div-crud-escenario_mesas').append(html_escenario_mesas);
		} else {
			$('.modal-message').addClass('modal-' + response.status);
			$('.modal-title-message').text(response.message);
			setTimeout(function () { $('#modal-message').modal('hide'); }, 6500);
		}
		html_escenario_mesas_crud += '<button type="button" class="btn btn-success btn-add-escenario_mesas active" data-toggle="modal" data-target="#modal-crud-escenario_mesas" data-toggle="tooltip" data-placement="bottom" title="Agregar escenarios">Agregar &nbsp; <i aria-hidden="true" class="fa fa-plus"></i></button>'
			+ '<button type="button" class="btn btn-success btn-all-escenario_mesas" data-toggle="modal" data-target="#modal-all-escenario_mesas" data-toggle="tooltip" data-placement="bottom" title="Modifcar / Eliminar escenarios">Administrar <i aria-hidden="true" class="fa fa-cog"></i></button>';
		$('.div-crud-escenario_mesas').append(html_escenario_mesas_crud);
	}, 'json')
	.fail(function (jqXHR, textStatus, errorThrown) {
		$('.modal-message').removeClass('modal-danger modal-warning modal-success');

		$('#modal-message').modal('show');
		$('.modal-message').addClass('modal-danger');
		$('.modal-title-message').text(textStatus + ' [' + jqXHR.status + ']: ' + errorThrown);
		setTimeout(function () { $('#modal-message').modal('hide'); }, 6000);

		//Message for developer
		console.log(jqXHR.responseText);
	})
}

function allEscenarioMesas(iIdEscenarioRestaurante) {
	var $url = base_url + 'PuntoVenta/POSRestauranteController/allEscenarioMesas/' + iIdEscenarioRestaurante;
	$.post($url, function (response) {
		$('.div-lista_escenario_mesas').empty();
		var mesas_items = '', Accion, iIdDocumentoCabecera, url_print;
		if (response.status == 'success') {
			var iTotalRegistros = response.result.length, response = response.result;
			for (var i = 0; i < iTotalRegistros; i++) {
				mesas_items += '<div class="div-container-lis-mesa-and-actions col-xs-6 col-sm-4 col-md-3 col-lg-3">';
					mesas_items += '<div class="btn-group btn-group-mesas text-right">'
						mesas_items += '<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"> <i aria-hidden="true" class="fa fa-ellipsis-h"></i></button>';
						mesas_items += '<ul class="dropdown-menu" role="menu">';
							mesas_items += '<li><a onclick="verMesa(' + response[i].ID_Mesa_Restaurante + ')" href="#">Modificar</a></li>';
							mesas_items += '<li><a onclick="eliminarMesa(' + response[i].ID_Mesa_Restaurante + ')" href="#">Eliminar</a></li>';
							if (response[i].Ss_Total != null) {
								Accion = 'ver', url_print = 'ocultar-img-logo_punto_venta_click';
								mesas_items += '<li><a onclick="formatoImpresionTicketPreCuenta(\'' + Accion + '\', ' + response[i].ID_Pedido_Cabecera + ', \'' + url_print + '\')" href="#">Ver pre-cuenta</a></li>';
								
								Accion = 'imprimir', url_print = 'ocultar-img-logo_punto_venta_click';
								mesas_items += '<li><a onclick="formatoImpresionTicketPreCuenta(\'' + Accion + '\', ' + response[i].ID_Pedido_Cabecera + ', \'' + url_print + '\')" href="#">Imprimir pre-cuenta</a></li>';

								Accion = 'imprimir', url_print = 'ocultar-img-logo_punto_venta_click';
								mesas_items += '<li><a onclick="formatoImpresionComandaCocina(\'' + Accion + '\', ' + response[i].ID_Pedido_Cabecera + ', \'' + url_print + '\')" href="#">Imprimir Comanda</a></li>';
							}
						mesas_items += '</ul>';
					mesas_items += '</div>';
					mesas_items += '<li style="' + (response[i].Ss_Total != null ? 'border-color:#ffecb5; background-color: #fffcf1;' : 'border-color:#b9b9b9; background-color: #e4e4e7') + '" class="li-item_mesa li-item_mesa_view list-group-item col-xs-12 col-sm-12 col-md-12 col-lg-12" data-id_mesa_restaurante="' + response[i].ID_Mesa_Restaurante + '" data-id_pedido_cabecera="' + response[i].ID_Pedido_Cabecera + '" value=' + response[i].ID_Mesa_Restaurante + '>';
						mesas_items += '<div class="row">';
							mesas_items += '<div class="col-xs-12">';
								//mesas_items += '<b><h4><i aria-hidden="true" class="fa fa-cutlery"></i> &nbsp; ' + response[i].No_Mesa_Restaurante + ' &nbsp;&nbsp; ';
								mesas_items += '<div><h5><b>' + response[i].No_Mesa_Restaurante + '</b></h5></div>';
							mesas_items += '</div>';
						mesas_items += '</div>';
						mesas_items += '<div class="row row-list-mesas">';
						mesas_items += '<div class="col-xs-12"><b>Mozo:</b> ' + (response[i].No_Mesero != null ? response[i].No_Mesero : '') + ' </div>';
							mesas_items += '</div>';
						mesas_items += '<div class="row row-list-mesas">';
						mesas_items += '<div class="col-xs-12"><b>Cliente:</b> ' + (response[i].No_Cliente != null ? response[i].No_Cliente : '') + ' </div>';
							mesas_items += '</div>';
						mesas_items += '<div class="row row-list-mesas">';
						mesas_items += '<div class="col-xs-12"><b>N° personas:</b> ' + (response[i].Nu_Cantidad_Personas_Restaurante != null ? response[i].Nu_Cantidad_Personas_Restaurante : '') + ' </div>';
							mesas_items += '</div>';
						mesas_items += '<div class="row row-list-mesas">';
							if ( response[i].Ss_Total != null ) {
								mesas_items += '<div class="col-xs-6"><h3 style="margin-top: 7%; font-size: 20px;"><span class="label label-warning_v2" style="color:#664d03; border-radius: 6px !important;"> &nbsp; ' + response[i].No_Signo + ' ' + response[i].Ss_Total + ' &nbsp; </span></h3></div>';
								mesas_items += '<div class="col-xs-6"><h5 style="margin-top: 15%;" class="text-right escenario-restaurante-time">' + (response[i].Fe_Transcurrida_Dia > 0 ? response[i].Fe_Transcurrida_Dia + ' días ' : '') + (response[i].Fe_Transcurrida_Hora > 0 ? response[i].Fe_Transcurrida_Hora + ' H ' : '') + (response[i].Fe_Transcurrida_Minuto > 0 ? response[i].Fe_Transcurrida_Minuto + ' m' : '') + '</h5></div>';
							} else {
								mesas_items += '<div class="col-xs-12"><b><h3><span class="label label-dark" style="border-radius:6px !important;"> &nbsp; Mesa vacía &nbsp; </span></h3></b></div>';
							}
						mesas_items += '</div>';
					mesas_items += '</li>';
				mesas_items += '</div>';
			}
		}
		mesas_items += '<div class="div-container-lis-mesa-and-actions col-xs-6 col-sm-4 col-md-3 col-lg-3"><li class="li-item_mesa li-add-item_mesa list-group-item col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center" value="0"><br><br><h2><span class="fa fa-plus"></span> Agregar</h2></b><br></li></div>';
		$('.div-lista_escenario_mesas').append(mesas_items);
	}, 'json')
	.fail(function (jqXHR, textStatus, errorThrown) {
		$('.modal-message').removeClass('modal-danger modal-warning modal-success');

		$('#modal-message').modal('show');
		$('.modal-message').addClass('modal-danger');
		$('.modal-title-message').text(textStatus + ' [' + jqXHR.status + ']: ' + errorThrown);
		setTimeout(function () { $('#modal-message').modal('hide'); }, 6000);

		//Message for developer
		console.log(jqXHR.responseText);
	})
}

function crudEscenario() {
	if ($("#txt-No_Escenario_Restaurante").val().length === 0) {
		$('#txt-No_Escenario_Restaurante').closest('.form-group').find('.help-block').html('Ingresar escenario');
		$('#txt-No_Escenario_Restaurante').closest('.form-group').removeClass('has-success').addClass('has-error');

		scrollToError($('#modal-crud-escenario_mesas .modal-body'), $('#txt-No_Escenario_Restaurante'));
	} else {
		$('#btn-modal-footer-add-escenario_mesas').text('');
		$('#btn-modal-footer-cancel-escenario_mesas').attr('disabled', true);
		$('#btn-modal-footer-add-escenario_mesas').attr('disabled', true);
		$('#btn-modal-footer-add-escenario_mesas').append('Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>');

		var $url = base_url + 'PuntoVenta/POSRestauranteController/crudEscenario';
		var $arrParamsPost = {
			'EID_Escenario_Restaurante': $('[name="EID_Escenario_Restaurante"]').val(),
			'ENo_Escenario_Restaurante': $('[name="ENo_Escenario_Restaurante"]').val(),
			'No_Escenario_Restaurante': $('[name="No_Escenario_Restaurante"]').val()
		};
		$.post($url, $arrParamsPost, function (response) {
			$('.modal-message').removeClass('modal-danger modal-warning modal-success');
			$('#modal-message').modal('show');

			if (response.status == 'success') {
				$('#modal-crud-escenario_mesas').modal('hide');

				$('.modal-message').addClass('modal-' + response.status);
				$('.modal-title-message').text(response.message);
				setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);

				window.location = base_url + 'PuntoVenta/POSRestauranteController/verEscenariosRestaurante/' + response.iIdEscenarioRestaurante;
			} else {
				$('.modal-message').addClass('modal-' + response.status);
				$('.modal-title-message').text(response.message);
				setTimeout(function () { $('#modal-message').modal('hide'); }, 6500);
			}

			$('#btn-modal-footer-add-escenario_mesas').text('');
			$('#btn-modal-footer-add-escenario_mesas').append('Guardar');
			$('#btn-modal-footer-cancel-escenario_mesas').attr('disabled', false);
			$('#btn-modal-footer-add-escenario_mesas').attr('disabled', false);
		}, 'json')
		.fail(function (jqXHR, textStatus, errorThrown) {
			$('#modal-crud-escenario_mesas').css("z-index", "3000");

			$('.modal-message').removeClass('modal-danger modal-warning modal-success');

			$('#modal-message').modal('show');
			$('.modal-message').addClass('modal-danger');
			$('.modal-title-message').text(textStatus + ' [' + jqXHR.status + ']: ' + errorThrown);
			setTimeout(function () { $('#modal-message').modal('hide'); }, 6000);

			//Message for developer
			console.log(jqXHR.responseText);

			$('#btn-modal-footer-add-escenario_mesas').text('');
			$('#btn-modal-footer-add-escenario_mesas').append('Guardar');
			$('#btn-modal-footer-cancel-escenario_mesas').attr('disabled', false);
			$('#btn-modal-footer-add-escenario_mesas').attr('disabled', false);
		})
	}
}

function verEscenario(ID) {
	url = base_url + 'PuntoVenta/POSRestauranteController/verEscenario/' + ID;
	$.ajax({
		url: url,
		type: "GET",
		dataType: "JSON",
		success: function (response) {
			if (response.status == "success") {
				response = response.result[0];

				$('#modal-crud-escenario_mesas').css("z-index", "2000");
				$('#modal-crud-escenario_mesas').modal('show');
				$('.modal-title').text('Modifcar Escenario');

				$('[name="EID_Escenario_Restaurante"]').val(response.ID_Escenario_Restaurante);
				$('[name="ENo_Escenario_Restaurante"]').val(response.No_Escenario_Restaurante);

				$('#modal-Marca').on('shown.bs.modal', function () {
					$('#txt-No_Escenario_Restaurante').focus();
				})
				$('[name="No_Escenario_Restaurante"]').val(response.No_Escenario_Restaurante);
			} else {
				$('.modal-message').removeClass('modal-danger modal-warning modal-success');
				$('#modal-message').modal('show');

				$('.modal-message').addClass('modal-' + response.status);
				$('.modal-title-message').text(response.message);
				setTimeout(function () { $('#modal-message').modal('hide'); }, 6500);
			}
		},
		error: function (jqXHR, textStatus, errorThrown) {
			$('.modal-message').removeClass('modal-danger modal-warning modal-success');

			$('#modal-crud-escenario_mesas').css("z-index", "3000");

			$('#modal-message').modal('show');
			$('.modal-message').addClass('modal-danger');
			$('.modal-title-message').text(textStatus + ' [' + jqXHR.status + ']: ' + errorThrown);
			setTimeout(function () { $('#modal-message').modal('hide'); }, 1700);

			//Message for developer
			console.log(jqXHR.responseText);
		}
	});
}

function eliminarEscenario(ID) {
	var $modal_delete = $('#modal-message-delete');
	$modal_delete.modal('show');

	$('#btn-cancel-delete').off('click').click(function () {
		$modal_delete.modal('hide');
	});

	$('#btn-save-delete').off('click').click(function () {
		$('#modal-loader').modal('show');

		url = base_url + 'PuntoVenta/POSRestauranteController/eliminarEscenario/' + ID;
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

					window.location = base_url + 'PuntoVenta/POSRestauranteController/verEscenariosRestaurante/' + $('#hidden-iIdEscenarioRestaurante').val();
				} else {
					$('.modal-message').addClass(response.style_modal);
					$('.modal-title-message').text(response.message);
					setTimeout(function () { $('#modal-message').modal('hide'); }, 1500);
				}
			}
		});
	});
}

function crudEscenarioMesa() {
	if ($('#cbo-escenario').val() == 0) {
		$('#cbo-escenario').closest('.form-group').find('.help-block').html('Seleccionar escenario');
		$('#cbo-escenario').closest('.form-group').removeClass('has-success').addClass('has-error');

		scrollToError($('#modal-add-mesas .modal-body'), $('#cbo-escenario'));
	} else if ($("#txt-No_Mesa_Restaurante").val().length === 0) {
		$('#txt-No_Mesa_Restaurante').closest('.form-group').find('.help-block').html('Ingresar mesa');
		$('#txt-No_Mesa_Restaurante').closest('.form-group').removeClass('has-success').addClass('has-error');

		scrollToError($('#modal-add-mesas .modal-body'), $('#txt-No_Mesa_Restaurante'));
	} else {
		$('#btn-modal-footer-add-mesas').text('');
		$('#btn-modal-footer-cancel-mesas').attr('disabled', true);
		$('#btn-modal-footer-add-mesas').attr('disabled', true);
		$('#btn-modal-footer-add-mesas').append('Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>');

		var $url = base_url + 'PuntoVenta/POSRestauranteController/crudEscenarioMesa';
		var $arrParamsPost = {
			'EID_Mesa_Restaurante': $('[name="EID_Mesa_Restaurante"]').val(),
			'ENo_Mesa_Restaurante': $('[name="ENo_Mesa_Restaurante"]').val(),
			'ID_Escenario_Restaurante': $('#cbo-escenario').val(),
			'No_Mesa_Restaurante': $('[name="No_Mesa_Restaurante"]').val(),
			'Nu_Estado': $('#cbo-estado').val(),
		};
		$.post($url, $arrParamsPost, function (response) {
			$('.modal-message').removeClass('modal-danger modal-warning modal-success');
			$('#modal-message').modal('show');

			if (response.status == 'success') {
				$('#modal-add-mesas').modal('hide');

				$('.modal-message').addClass('modal-' + response.status);
				$('.modal-title-message').text(response.message);
				setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);

				$('#hidden-iIdEscenarioRestaurante').val( $('#hidden-iIdEscenarioRestaurante').val() )
				allEscenarioMesas( $('#hidden-iIdEscenarioRestaurante').val() );
			} else {
				$('.modal-message').addClass('modal-' + response.status);
				$('.modal-title-message').text(response.message);
				setTimeout(function () { $('#modal-message').modal('hide'); }, 6500);
			}

			$('#btn-modal-footer-add-mesas').text('');
			$('#btn-modal-footer-add-mesas').append('Guardar');
			$('#btn-modal-footer-cancel-mesas').attr('disabled', false);
			$('#btn-modal-footer-add-mesas').attr('disabled', false);
		}, 'json')
			.fail(function (jqXHR, textStatus, errorThrown) {
				$('.modal-message').removeClass('modal-danger modal-warning modal-success');

				$('#modal-message').modal('show');
				$('.modal-message').addClass('modal-danger');
				$('.modal-title-message').text(textStatus + ' [' + jqXHR.status + ']: ' + errorThrown);
				setTimeout(function () { $('#modal-message').modal('hide'); }, 6000);

				//Message for developer
				console.log(jqXHR.responseText);

				$('#btn-modal-footer-add-mesas').text('');
				$('#btn-modal-footer-add-mesas').append('Guardar');
				$('#btn-modal-footer-cancel-mesas').attr('disabled', false);
				$('#btn-modal-footer-add-mesas').attr('disabled', false);
			})
	}
}

function verMesa(ID) {
	url = base_url + 'PuntoVenta/POSRestauranteController/verEscenarioMesa/' + ID;
	$.ajax({
		url: url,
		type: "GET",
		dataType: "JSON",
		success: function (response) {
			if ( response.status == "success" ) {
				response = response.result[0];

				$('.div-escenario').show();
				$('.div-escenario-no_creado').hide();

				$('#modal-add-mesas').modal('show');
				$('.modal-title').text('Modifcar Mesa');
				
				$('[name="EID_Mesa_Restaurante"]').val(response.ID_Mesa_Restaurante);
				$('[name="ENo_Mesa_Restaurante"]').val(response.No_Mesa_Restaurante);

				$('#modal-Marca').on('shown.bs.modal', function () {
					$('#txt-No_Mesa_Restaurante').focus();
				})

				var selected;
				$('#cbo-escenario').html('<option value="" selected="selected">- Seleccionar -</option>');
				url = base_url + 'PuntoVenta/POSRestauranteController/allEscenario/' + 0;
				$.post(url, function (responseEscenarios) {
					var iTotalRegistros = responseEscenarios.result.length, responseEscenarios = responseEscenarios.result;
					for (var i = 0; i < iTotalRegistros; i++) {
						selected = '';
						if (response.ID_Escenario_Restaurante == responseEscenarios[i].ID_Escenario_Restaurante)
							selected = 'selected="selected"';
						$('#cbo-escenario').append('<option value="' + responseEscenarios[i].ID_Escenario_Restaurante + '" ' + selected + '>' + responseEscenarios[i].No_Escenario_Restaurante + '</option>');
					}
				}, 'json')

				$('[name="No_Mesa_Restaurante"]').val(response.No_Mesa_Restaurante);

				selected = '';
				$('#cbo-estado').html('');
				for (var i = 0; i < 2; i++) {
					selected = '';
					if (response.Nu_Estado == i)
						selected = 'selected="selected"';
					$('#cbo-estado').append('<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Inactivo' : 'Activo') + '</option>');
				}
			} else {
				$('.modal-message').removeClass('modal-danger modal-warning modal-success');
				$('#modal-message').modal('show');

				$('.modal-message').addClass('modal-' + response.status);
				$('.modal-title-message').text(response.message);
				setTimeout(function () { $('#modal-message').modal('hide'); }, 6500);
			}
		},
		error: function (jqXHR, textStatus, errorThrown) {
			$('.modal-message').removeClass('modal-danger modal-warning modal-success');

			$('#modal-message').modal('show');
			$('.modal-message').addClass('modal-danger');
			$('.modal-title-message').text(textStatus + ' [' + jqXHR.status + ']: ' + errorThrown);
			setTimeout(function () { $('#modal-message').modal('hide'); }, 1700);

			//Message for developer
			console.log(jqXHR.responseText);
		}
	});
}

function eliminarMesa(ID) {
	var $modal_delete = $('#modal-message-delete');
	$modal_delete.modal('show');

	$('#btn-cancel-delete').off('click').click(function () {
		$modal_delete.modal('hide');
	});

	$('#btn-save-delete').off('click').click(function () {
		$('#modal-loader').modal('show');

		url = base_url + 'PuntoVenta/POSRestauranteController/eliminarMesa/' + ID;
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

					$('#hidden-iIdEscenarioRestaurante').val($('#hidden-iIdEscenarioRestaurante').val())
					allEscenarioMesas($('#hidden-iIdEscenarioRestaurante').val());
				} else {
					$('.modal-message').addClass(response.style_modal);
					$('.modal-title-message').text(response.message);
					setTimeout(function () { $('#modal-message').modal('hide'); }, 1500);
				}
			}
		});
	});
}