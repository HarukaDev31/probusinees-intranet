var url, arrItemVentaTemporal, global_class_method, global_table, filter_lista, $modal_msg_stock, ID_Item, fila, precio, cantidad, tr_body_table_detalle_productos_pos='', $fPrecioItem=0.00, $Sum_Ss_Total_POS=0.00, $fDescuentoTotalItem=0.00;
var fToday = new Date(),
fYear = fToday.getFullYear(),
fMonth = fToday.getMonth() + 1,
fDay = fToday.getDate(),
isLoading = false;

$(function () {
	$( '.select2' ).select2();
	$( '[data-mask]' ).inputmask();
	$( '#txt-No_Producto' ).focus();
	$('.label-signo_total_detalle_productos_pos').text( $('#hidden-no_signo_caja_pos').val() );

	$('.div-modal_credito').hide();
	$('#txt-Fe_Vencimiento').val(fDay + '/' + fMonth + '/' + fYear);
	var Fe_Emision = $('#txt-Fe_Vencimiento').val().split('/');
	$('#txt-Fe_Vencimiento').datepicker({
		autoclose: true,
		startDate: new Date(Fe_Emision[2], Fe_Emision[1] - 1, Fe_Emision[0]),
		todayHighlight: true
	})

	$('#txt-Fe_Vencimiento').val(fDay + '/' + fMonth + '/' + fYear);

	$('[name="radio-addWhatsapp"]').on('ifChecked', function () {
		$('#txt-Nu_Celular_Entidad_Cliente').focus();
	})

	// Descuento Total
	$('#btn-add_descuento_total').prop('disabled', true);
	if ($('#hidden-Nu_Activar_Descuento_Punto_Venta').val() == 1)
		$('#btn-add_descuento_total').prop('disabled', false);

	$('.span-descuento_total_tipo').text('importe');
	$('#cbo-descuento').change(function () {
		//$('#txt-Ss_Descuento').val( '' );
		calcularTotales();
		$('.span-descuento_total_tipo').text('importe');
		if ($(this).val() == 2)
			$('.span-descuento_total_tipo').text('porcentaje');
	})

	$(document).on('keyup', '#txt-Ss_Descuento', function () {
		validarDescuentoTotalPorImpuestoTributario();
		calcularTotales();
	})

	$('#btn-add_descuento_total').click(function () {
		var fDescuentoItem = 0;
		$('#table-detalle_productos_pos > tbody > tr').each(function () {
			fila = $(this);
			fDescuentoItem += parseFloat(fila.find(".input-fDescuentoItem").val());
		})

		if (fDescuentoItem > 0) {
			alert('No se puede agregar doble tipo de descuento, elegir descuento x ítem o total');
		} else {
			$('.modal-add_descuento_total').modal('show');
			$('.modal-add_descuento_total').on('shown.bs.modal', function () {
				$('#txt-Ss_Descuento').focus();
			})
		}
	})
	// ./ Descuento Total

	var isCtrl = false;
	$(".autocompletar_lector_codigo_barra").keyup(function (e) {
		if (e.which == 13) buscarItem($('.autocompletar_lector_codigo_barra').val());
		if (e.which == 17) isCtrl = true;
		if (e.which == 86 && isCtrl == true) {
			if ( $('.autocompletar_lector_codigo_barra').val().length == 13 ) buscarItem($('.autocompletar_lector_codigo_barra').val());
		}
	});

	$('#btn-datosAdicionalesEntidad').click(function () {
		$('.modal-datos_adicionales_entidad').modal('show');

		$('#h4-label_cliente').html($('#txt-ANombre').val());
	})

	// crear item modal
	url = base_url + 'HelperController/getTiposExistenciaProducto';
	$.post( url , function( responseTiposExistenciaProducto ){
		$( '#cbo-modal-tipoItem' ).html( '' );
		for (var i = 0; i < responseTiposExistenciaProducto.length; i++)
		$( '#cbo-modal-tipoItem' ).append( '<option value="' + responseTiposExistenciaProducto[i].ID_Tipo_Producto + '">' + responseTiposExistenciaProducto[i].No_Tipo_Producto + '</option>' );
	}, 'JSON');

	url = base_url + 'HelperController/getImpuestos';
	$.post( url , function( response ){
	  $( '#cbo-modal-impuestoItem' ).html( '' );
	  for (var i = 0; i < response.length; i++)
		$( '#cbo-modal-impuestoItem' ).append( '<option value="' + response[i].ID_Impuesto + '" data-ss_impuesto="' + response[i].Ss_Impuesto + '" data-nu_tipo_impuesto="' + response[i].Nu_Tipo_Impuesto + '">' + response[i].No_Impuesto + '</option>' );
	}, 'JSON');
	
	url = base_url + 'HelperController/getUnidadesMedida';
	$.post( url , function( responseUnidadMedidas ){
	  $( '#cbo-modal-unidad_medidaItem' ).html( '' );
	  for (var i = 0; i < responseUnidadMedidas.length; i++)
		$( '#cbo-modal-unidad_medidaItem' ).append( '<option value="' + responseUnidadMedidas[i].ID_Unidad_Medida + '">' + responseUnidadMedidas[i].No_Unidad_Medida + '</option>' );
	}, 'JSON');

	/* Categorías */
	url = base_url + 'HelperController/getDataGeneral';
	$.post(url, { sTipoData: 'categoria' }, function (response) {
		if (response.sStatus == 'success') {
			var l = response.arrData.length;
			if (l == 1) {
				$('#cbo-modal-categoria').html('<option value="' + response.arrData[0].ID + '">' + response.arrData[0].Nombre + '</option>');
			} else {
				$('#cbo-modal-categoria').html('<option value="" selected="selected">- Seleccionar -</option>');
				for (var x = 0; x < l; x++) {
					$('#cbo-modal-categoria').append('<option value="' + response.arrData[x].ID + '">' + response.arrData[x].Nombre + '</option>');
				}
			}
		} else {
			if (response.sMessageSQL !== undefined) {
				console.log(response.sMessageSQL);
			}
			$('#modal-message').modal('show');
			$('.modal-message').addClass(response.sClassModal);
			$('.modal-title-message').text(response.sMessage);
			setTimeout(function () { $('#modal-message').modal('hide'); }, 1200);
		}
	}, 'JSON');
	// ./ crear item modal

	$( '#btn-crear_item' ).click(function(){
		$( '.modal-crear_item' ).modal('show');
		$( '#form-crear_item' )[0].reset();
	})

	// Mostrar tipo de producto para inventarios
	$( '.div-Producto' ).show();
	$( '#cbo-modal-grupoItem' ).change(function(){
  	$( '.div-Producto' ).show();
	  if ( $(this).val() == 0 ){//Servicio
    	$( '.div-Producto' ).hide();
	  }
	})
	
	$( '#btn-modal-crear_item' ).off('click').click(function () {
		$('.help-block').empty();
		var fPrecio = parseFloat($('#txt-modal-precioItem').val());
		if ($('#txt-modal-upcItem').val().length === 0) {
			$('#txt-modal-upcItem').closest('.form-group').find('.help-block').html('Ingresar cod. barra');
			$('#txt-modal-upcItem').closest('.form-group').removeClass('has-success').addClass('has-error');
		} else if ($('[name="textarea-modal-nombreItem"]').val().length === 0) {
			$('[name="textarea-modal-nombreItem"]').closest('.form-group').find('.help-block').html('Ingresar nombre');
			$('[name="textarea-modal-nombreItem"]').closest('.form-group').removeClass('has-success').addClass('has-error');
		} else if (fPrecio == 0.00 || isNaN(fPrecio)) {
			$('#txt-modal-precioItem').closest('.form-group').find('.help-block').html('Ingresar precio');
			$('#txt-modal-precioItem').closest('.form-group').removeClass('has-success').addClass('has-error');
		} else if ($('#cbo-modal-categoria').val() == '0') {
			$('#cbo-modal-categoria').closest('.form-group').find('.help-block').html('Seleccionar categoría');
			$('#cbo-modal-categoria').closest('.form-group').removeClass('has-success').addClass('has-error');
		} else {
			crearItemModal();
		}
	});

	// Valores inicio
	$( '.div-nuevo_cliente' ).hide();
	$( '#cbo-TiposDocumentoIdentidad' ).val(2);
	$( '#btn-pagar' ).prop('disabled', true);
	$( '#cbo-tipo_envio_lavado' ).prop('disabled', true);
	$( '#txt-fe_entrega' ).prop('disabled', true);
	$('#cbo-recepcion').prop('disabled', true);

	$("#txt-Nu_Celular_Entidad_Cliente").blur(function () {
		validarNumeroCelular($(this).val(), '#span-celular');

		$("#txt-Nu_Celular_Entidad_Cliente-modal").val($(this).val());
	})

	$("#txt-Txt_Email_Entidad_Cliente").blur(function () {
		caracteresCorreoValido($(this).val(), '#span-email');

		$("#txt-Txt_Email_Entidad_Cliente-modal").val($(this).val());
	})

	$("#txt-Txt_Email_Entidad_Cliente-modal").blur(function () {
		caracteresCorreoValido($(this).val(), '#span-email-modal');

		$("#txt-Txt_Email_Entidad_Cliente").val($(this).val());
	})

	$("#txt-Nu_Celular_Entidad_Cliente-modal").blur(function () {

		$("#txt-Nu_Celular_Entidad_Cliente").val($(this).val());
	})

	$("#txt-Txt_Direccion_Entidad-modal").blur(function () {

		$("#txt-Txt_Direccion_Entidad").val($(this).val());
		$('[name="Txt_Direccion_Delivery"]').val($(this).val());
	})

	$('[name="Txt_Direccion_Delivery"]').blur(function () {
		$('[name="Txt_Direccion_Entidad"]').val($(this).val());
	})

	$('#cbo-Estado-modal').change(function () {
		$('#hidden-estado_entidad').val($(this).val());
	})

	// Combinacion de teclas
	// Cancelar Venta
	$("#btn-cancelar_venta").click(function () {
		limpiarValoresVenta(1);
	});
	
	$(document).bind('keydown', 'esc', function(){
		limpiarValoresVenta(1);
	});

	$('input.hotkey-cancelar_venta').bind('keydown', 'esc', function(){
		limpiarValoresVenta(1);
	});
	// ./ Cancelar Venta

	// Limpiar item
	$(document).bind('keydown', 'F2', function(){
		$( '#txt-ID_Producto' ).val( '' );
		$( '#txt-No_Producto' ).val( '' );
	});

	$('input.hotkey-limpiar_item').bind('keydown', 'F2', function(){
		$( '#txt-ID_Producto' ).val( '' );
		$( '#txt-No_Producto' ).val( '' );
	});
	// ./ Limpiar item

	// Focus item
	$(document).bind('keydown', 'F4', function(){
		$( '#txt-No_Producto' ).focus();
	});

	$('input.hotkey-focus_item').bind('keydown', 'F4', function () {
		$('#txt-No_Producto').focus();
	});
	// ./ Focus item

	// Button cobrar
	$(document).bind('keydown', 'return', function(){
		if ( $('#btn-pagar').prop('disabled') == false ){
			cobrarCliente();
		}
	});

	$('input.autocompletar_lector_codigo_barra').bind('keydown', 'return', function(){
		if ( $('#btn-pagar').prop('disabled') == false ){
			cobrarCliente();
		}
	});

	$('input.input-codigo_barra').bind('keydown', 'return', function(){
		if ( $('#btn-pagar').prop('disabled') == false ){
			cobrarCliente();
		}
	});

	$('input.hotkey-cobrar_cliente').bind('keydown', 'return', function(){
		if ( $('#btn-pagar').prop('disabled') == false ){
			cobrarCliente();
		}
	});

	$('select.hotkey-cobrar_cliente').bind('keydown', 'return', function(){
		if ( $('#btn-pagar').prop('disabled') == false ){
			cobrarCliente();
		}
	});
	// ./ Button cobrar
	
	// Button add forma de pago y generar ticket
	$(document).bind('keydown', 'return', function(){
		$Sum_Ss_Monto_Total = 0.00;
		$("#table-modal_forma_pago > tbody > tr").each(function(){
			fila = $(this);
			$Sum_Ss_Monto_Total += parseFloat(fila.find(".fTotal").text());
		});
	});
	// ./ Button add forma de pago y generar ticket
	// ./ Combinacion de teclas

	/* Tipo Documento Identidad */
	$('#cbo-TiposDocumentoIdentidad').change(function () {
		$('#hidden-estado_entidad').val('1');
		$('#cbo-Estado-modal').html('<option value="1">Activo</option>');
		$('#cbo-Estado-modal').append('<option value="0">Inactivo</option>');

		$('#hidden-nu_numero_documento_identidad').val('');

		$('#txt-AID').val('');
		$('#txt-ACodigo').val('');
		$('#txt-Txt_Email_Entidad_Cliente').val('');
		$('#txt-Txt_Email_Entidad_Cliente-modal').val('');
		$('#txt-Nu_Celular_Entidad_Cliente').val('');
		$('#txt-Nu_Celular_Entidad_Cliente-modal').val('');
		$('#txt-Txt_Direccion_Entidad').val('');
		$('#txt-Txt_Direccion_Entidad-modal').val('');
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
		} else {
			$('#label-tipo_documento_identidad').text('OTROS');
			$('#label-No_Entidad').text('Nombre(s) y Apellidos');
			$('#txt-ACodigo').attr('maxlength', $(this).find(':selected').data('nu_cantidad_caracteres'));
		}

		setTimeout(function () { $('#txt-ACodigo').focus(); }, 20);
	})

	// Obtener tipos de documento de identidad
	url = base_url + 'HelperController/getTiposDocumentoIdentidad';
	$.post(url, function (response) {
		$('#cbo-TiposDocumentoIdentidad').html('');
		for (var i = 0; i < response.length; i++)
			$('#cbo-TiposDocumentoIdentidad').append('<option value="' + response[i]['ID_Tipo_Documento_Identidad'] + '" data-nu_cantidad_caracteres="' + response[i]['Nu_Cantidad_Caracteres'] + '">' + response[i]['No_Tipo_Documento_Identidad_Breve'] + '</option>');
	}, 'JSON');

	// Obtener canales de venta
	url = base_url + 'HelperController/getCanalesVenta';
	$.post(url, function (response) {
		if (response.sStatus == 'success') {
			var l = response.arrData.length;
			if (l == 1) {
				$('#cbo-canal_venta').html('<option value="' + response.arrData[0].ID + '">' + response.arrData[0].Nombre + '</option>');
			} else {
				$('#cbo-canal_venta').html('<option value="0" selected="selected">- Seleccionar -</option>');
				for (var x = 0; x < l; x++) {
					$('#cbo-canal_venta').append('<option value="' + response.arrData[x].ID + '">' + response.arrData[x].Nombre + '</option>');
				}
			}
		} else {
			if (response.sMessageSQL !== undefined) {
				console.log(response.sMessageSQL);
			}
		}
	}, 'JSON');

	// MODAL Precargar datos de cobranza de cliente
	$( '.div-billete_soles' ).show();
	$( '.div-modal_datos_tarjeta_credito' ).hide();
	$( '#div-modal_forma_pago' ).hide();

	url = base_url + 'HelperController/getDeliveryVentas';
	var arrPost = {};
	$.post( url, arrPost, function( response ){
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

	$('.div-nuevo_cliente').show();

	$('#cbo-TiposDocumentoIdentidad').val(2);
	$('#txt-AID').val('');
	$('#hidden-nu_numero_documento_identidad').val('');

	$('#label-tipo_documento_identidad').text('DNI');
	$('#txt-ACodigo').attr('maxlength', 8);

	$('#hidden-estado_entidad').val('1');
	$('#cbo-Estado-modal').html('<option value="1">Activo</option>');
	$('#cbo-Estado-modal').append('<option value="0">Inactivo</option>');

	$('#txt-ACodigo').val('');
	$('#txt-ANombre').val('');

	$('#label_correo').show();
	$('#span_correo').show();
	$('#txt-Txt_Email_Entidad_Cliente').show();

	url = base_url + 'HelperController/getMediosPago';
	var arrPost = {
		iIdEmpresa: $('#header-a-id_empresa').val(),
	};
	$.post( url, arrPost, function( response ){
	  	$( '#cbo-modal_forma_pago' ).html('');
	  	for (var i = 0; i < response.length; i++)
			$( '#cbo-modal_forma_pago' ).append( '<option value="' + response[i].ID_Medio_Pago + '" data-nu_tipo_medio_pago="' + response[i].Nu_Tipo + '">' + response[i].No_Medio_Pago + '</option>' );
	}, 'JSON');
	// ./ MODAL Precargar datos de cobranza de cliente

	url = base_url + 'HelperController/getListaPrecio';
	$.post( url, {Nu_Tipo_Lista_Precio : 1, ID_Organizacion: $('#header-a-id_organizacion').val(), ID_Almacen : $( '#cbo-almacen' ).val()}, function( responseLista ){
		if (responseLista.length == 1) {
			//$('#cbo-lista_precios').html('<option value="0" selected="selected">- Seleccionar -</option>');
			$('#cbo-lista_precios').html( '<option value="' + responseLista[0].ID_Lista_Precio_Cabecera + '">' + responseLista[0].No_Lista_Precio + '</option>' );

			var arrParams = {
				sUrl: 'HelperController/getItems',
				ID_Almacen: $('#cbo-almacen').val(),
				iIdListaPrecio: responseLista[0].ID_Lista_Precio_Cabecera,
				ID_Linea: 'favorito',
			};
			getItems(arrParams);
		} else {
			$( '#cbo-lista_precios' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
			for (var i = 0; i < responseLista.length; i++)
				$( '#cbo-lista_precios' ).append( '<option value="' + responseLista[i].ID_Lista_Precio_Cabecera + '">' + responseLista[i].No_Lista_Precio + '</option>' );

			var arrParams = {
				sUrl: 'HelperController/getItems',
				ID_Almacen: $('#cbo-almacen').val(),
				iIdListaPrecio: $('#cbo-lista_precios').val(),
				ID_Linea: 'favorito',
			};
			getItems(arrParams);
		}
	}, 'JSON');

	$('#cbo-almacen').change(function () {
		if ($(this).val() > 0) {
			url = base_url + 'HelperController/getListaPrecio';
			$.post(url, { Nu_Tipo_Lista_Precio: 1, ID_Organizacion: $('#header-a-id_organizacion').val(), ID_Almacen: $('#cbo-almacen').val() }, function (responseLista) {
				if (responseLista.length == 1) {
					//$('#cbo-lista_precios').html('<option value="0" selected="selected">- Seleccionar -</option>');
					$('#cbo-lista_precios').html('<option value="' + responseLista[0].ID_Lista_Precio_Cabecera + '">' + responseLista[0].No_Lista_Precio + '</option>');

					var arrParams = {
						sUrl: 'HelperController/getItems',
						ID_Almacen: $('#cbo-almacen').val(),
						iIdListaPrecio: responseLista[0].ID_Lista_Precio_Cabecera,
						ID_Linea: 'favorito',
					};
					getItems(arrParams);
				} else {
					$('#cbo-lista_precios').html('<option value="0" selected="selected">- Seleccionar -</option>');
					for (var i = 0; i < responseLista.length; i++)
						$('#cbo-lista_precios').append('<option value="' + responseLista[i].ID_Lista_Precio_Cabecera + '">' + responseLista[i].No_Lista_Precio + '</option>');

					var arrParams = {
						sUrl: 'HelperController/getItems',
						ID_Almacen: $('#cbo-almacen').val(),
						iIdListaPrecio: $('#cbo-lista_precios').val(),
						ID_Linea: 'favorito',
					};
					getItems(arrParams);
				}
			}, 'JSON');
		}
	});

  	//Global autocomplete Producto
  	$( '.autocompletar_lector_codigo_barra' ).autoComplete({
		minChars: 0,
		tabDisabled:false,
		source: function (term, response) {
			term = term.trim();
			if (term.length > 2) {
				global_class_method = $( '.autocompletar_lector_codigo_barra' ).data('global-class_method');
				global_table = $( '.autocompletar_lector_codigo_barra' ).data('global-table');

				var send_post = {
					global_table : global_table,
					global_search : term,
					filter_id_almacen : '',
					filter_nu_compuesto : '',
					filter_nu_tipo_producto : 2,//2=Producto
					filter_lista : $( '#cbo-lista_precios' ).val(),
				}

				$.post( base_url + global_class_method, send_post, function( arrData ){
					response(arrData);
				}, 'JSON');
			}
		},
		renderItem: function (item, search){
			search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
			var Ss_Precio = 0;
			if ( (item.Ss_Precio === null || item.Ss_Precio == 0.000000) && (item.Ss_Precio_Item !== null || item.Ss_Precio_Item != 0.000000) )
			  Ss_Precio = item.Ss_Precio_Item;
			if ( item.Ss_Precio !== null )
			  Ss_Precio = item.Ss_Precio;

			var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");	
			return '<div title="' + caracteresValidosAutocomplete(item.Nombre) + '" class="autocomplete-suggestion" data-id="' + item.ID + '" data-ss_icbper="' + item.Ss_Icbper + '" data-id_impuesto_icbper="' + item.ID_Impuesto_Icbper + '" data-nu_tipo_item="' + item.Nu_Tipo_Producto + '" data-no_codigo_interno="' + item.No_Codigo_Interno + '" data-nu_compuesto="' + item.Nu_Compuesto + '" data-codigo="' + item.Codigo + '" data-nombre="' + caracteresValidosAutocomplete(item.Nombre) + '" data-precio="' + Ss_Precio + '" data-precio_interno="' + item.Ss_Precio_Interno + '" data-nu_tipo_impuesto="' + item.Nu_Tipo_Impuesto + '" data-id_impuesto_cruce_documento="' + item.ID_Impuesto_Cruce_Documento + '" data-ss_impuesto="' + item.Ss_Impuesto + '" data-qt_producto="' + item.Qt_Producto + '" data-txt_composicion="' + item.Txt_Composicion + '" data-val="' + search + '">' + caracteresValidosAutocomplete(item.Nombre).replace(re, "<b>$1</b>") + ' / <strong>P.V:</strong> ' + parseFloat(Ss_Precio).toFixed(2) + ' / <strong>S:</strong> ' + (isNaN(parseFloat(item.Qt_Producto)) ? 0 : parseFloat(item.Qt_Producto)) + '</div>';
		},
		onSelect: function(e, term, item){
			$( '#txt-ID_Producto' ).val(item.data('id'));
			$( '#txt-Nu_Codigo_Barra' ).val(item.data('codigo'));
			$( '#txt-No_Producto' ).val(item.data('nombre'));
			$( '#txt-Qt_Producto' ).val(item.data('qt_producto'));
			$( '#txt-Ss_Precio_Interno' ).val(item.data('precio_interno'));
			$( '#txt-Ss_Precio' ).val(item.data('precio'));
			$( '#txt-ID_Impuesto_Cruce_Documento' ).val(item.data('id_impuesto_cruce_documento'));
			$( '#txt-Nu_Tipo_Impuesto' ).val(item.data('nu_tipo_impuesto'));
			$( '#txt-Ss_Impuesto' ).val(item.data('ss_impuesto'));
			$( '#txt-Ss_Impuesto' ).val(item.data('ss_impuesto'));
			$( '#txt-nu_tipo_item' ).val(item.data('nu_tipo_item'));

			arrItemVentaTemporal={
				iIdItem:item.data('id'),
				iTipoProducto:item.data('nu_tipo_item'),
				iCodigoItem:item.data('codigo'),
				sNombreItem:item.data('nombre'),
				qItem:item.data('qt_producto'),
				fPrecio:item.data('precio'),
				iIdImpuestoCruceDocumento:item.data('id_impuesto_cruce_documento'),
				iTipoImpuesto:item.data('nu_tipo_impuesto'),
				fImpuesto: item.data('ss_impuesto'),
				iIdAlmacen: $('#cbo-almacen').val(),
				iCompuesto: item.data('nu_compuesto'),
				sCodigoInterno: item.data('no_codigo_interno'),
				iIdImpuestoIcbper: item.data('id_impuesto_icbper'),
				Ss_Icbper_item: item.data('ss_icbper'),
			}
			agregarItemVentaTemporal(arrItemVentaTemporal);
			
			$( '#table-items_alternativos tbody' ).empty();

			if ( iIdTipoRubroEmpresaGlobal == 1 ) {// 1 = Farmacia
				if ( item.data('txt_composicion') != '' && item.data('txt_composicion') != null ){
					arrDataAlternativos={
						iIdListaPrecio : $( '#cbo-lista_precios' ).val(),
						iIdItem:item.data('id'),
						sComposicion:item.data('txt_composicion'),
					}
					buscarItemAlternativos(arrDataAlternativos);
				}
			}
    	}// ./ OnSelect Item
	});// ./ Autocomplet de item

	$(".div-lista_cuadro_items").on("click", ".li-item_pos", function(event){
		buscarItem( $(this).val() );
	});
	// ./ Lateral Izquierdo

	// Lateral Derecho

	// Tipo Documento Identidad
	$( '#cbo-tipo_documento' ).change(function(){
		$( '#label_correo' ).show();
		$( '#txt-Txt_Email_Entidad_Cliente' ).show();

		$('#txt-AID').val('');
		$('#txt-ACodigo').val( '' );
		$('#txt-Nu_Celular_Entidad_Cliente').val( '' );
		$('#txt-Txt_Email_Entidad_Cliente').val('');
		$('#txt-ANombre').val('');
		$('#span-no_nombres_cargando').html('');

		if ($(this).val() == 4) {//Boleta
			$('#label-tipo_documento_identidad').text('DNI');
			$('#cbo-TiposDocumentoIdentidad').val(2);//DNI
			$('#txt-ACodigo').attr('maxlength', $(this).find(':selected').data('nu_cantidad_caracteres'));
			$('#txt-ACodigo').attr('maxlength', 15);
		} else if ($(this).val() == 5) {//Boleta
			$('#label-tipo_documento_identidad').text('OTROS');
			$('#cbo-TiposDocumentoIdentidad').val(1);//OTROS
			$('#txt-ACodigo').attr('maxlength', $(this).find(':selected').data('nu_cantidad_caracteres'));
			$('#txt-ACodigo').attr('maxlength', 15);
		} else if ($(this).val() == 3) {//Factura
			$('#label-tipo_documento_identidad').text('RUC');
			$('#cbo-TiposDocumentoIdentidad').val(4);//RUC
			$('#txt-ACodigo').attr('maxlength', $(this).find(':selected').data('nu_cantidad_caracteres'));
		} else {
			$('#cbo-TiposDocumentoIdentidad').val(1);//OTROS
			$('#label-tipo_documento_identidad').text('OTROS');
			$('#txt-ACodigo').attr('maxlength', 15);
			//$('#label_correo').hide();
			//$('#txt-Txt_Email_Entidad_Cliente').hide();
		}

		setTimeout(function () { $('#txt-ACodigo').focus(); }, 20);	
	})
  
 	// Lista de productos a comprar
  	$( '#table-detalle_productos_pos tbody' ).on('input', '.txt-Qt_Producto', function(){
		calcularImportexItem($(this).parents("tr"));
		calcularIcbper();
  	})

  	$( '#table-detalle_productos_pos tbody' ).on('input', '.input-fDescuentoItem', function(){
		if (parseFloat($('#txt-Ss_Descuento').val()) > 0 && parseFloat($(this).parents("tr").find(".input-fDescuentoItem").val()) > 0) {
			$('.input-fDescuentoItem').val('');
			alert('No se puede agregar doble tipo de descuento, elegir descuento x ítem o total');
		} else {
			calcularImportexItem($(this).parents("tr"));
			calcularIcbper();
		}
	})
	  
	$( '#table-detalle_productos_pos tbody' ).on('click', '#btn-delete_producto_pos', function(){
		$(this).closest('tr').remove();
		$('.div-col-alerta').remove();
    
		calcularTotales();
		calcularIcbper();

		$( '#btn-pagar' ).prop('disabled', false);
		$( '#cbo-tipo_envio_lavado' ).prop('disabled', false);
		$( '#txt-fe_entrega' ).prop('disabled', false);
		$( '#cbo-recepcion' ).prop('disabled', false);
		if ($( '#table-detalle_productos_pos > tbody > tr' ).length == 0){
			$( '#table-detalle_productos_pos' ).hide();
			$( '#btn-pagar' ).prop('disabled', true);
			$( '#cbo-tipo_envio_lavado' ).prop('disabled', true);
			$( '#txt-fe_entrega' ).prop('disabled', true);
			$( '#cbo-recepcion' ).prop('disabled', true);
			$( '#cbo-descuento' ).attr('disabled', false);
		}
	})
	
	$( '#table-detalle_productos_pos tbody' ).on('click', '#btn-ver_producto_pos', function(){
		var arrParams = {
			'sTipoData' : 'item',
			'iIdItem' : $(this).data('id_item'),
			'iIdListaPrecioCabecera' : $('#cbo-lista_precios').val(),
		};
		url = base_url + 'HelperController/getDataGeneral';
		$.post( url, arrParams, function( arrResponse ){
			$( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');

			if (arrResponse.sStatus == 'success') {
				$( '#modal-table-info_item' ).remove();

				$( '.modal-info_item' ).modal('show');

				var arrData = arrResponse.arrData[0], Ss_Precio = 0;
				$( '#modal-header-info_item-title' ).html( arrData.No_Producto );

				//obtener composicion por item
				var sHtmlTableInfoItem = '';
				var arrParams = {
					'sTipoData' : 'composicion',
					'sWhereIdComposicion' : arrData.Txt_Composicion,
				};
				url = base_url + 'HelperController/getDataGeneral';
				$.post( url, arrParams, function( arrResponseComposicion ){
					if ( arrResponseComposicion.sStatus == 'success' || arrResponseComposicion.sStatus == 'warning' ) {
						var Txt_Composicion = '';
						if ( arrData.Txt_Composicion.length > 0 && arrResponseComposicion.sStatus == 'success' ) {
							var iCantidadRegistros = arrResponseComposicion.arrData.length, Txt_Composicion = '';
							if ( iCantidadRegistros > 1 ) {
								for (var i=0; i < iCantidadRegistros; i++) {
									Txt_Composicion += arrResponseComposicion.arrData[i].Nombre + ', ';
								}
								Txt_Composicion = Txt_Composicion.slice(0);
							} else {
								Txt_Composicion += arrResponseComposicion.arrData[0].Nombre;
							}
						}
						Ss_Precio = 0;
						if ( arrData.Ss_Precio_Item !== null || arrData.Ss_Precio_Item != 0.000000 )
						  Ss_Precio = arrData.Ss_Precio_Item;
						if ( (arrData.Ss_Precio_Item === null || arrData.Ss_Precio_Item == 0.000000) && arrData.Ss_Precio !== null )
						  Ss_Precio = arrData.Ss_Precio;
						sHtmlTableInfoItem +=
						'<table id="modal-table-info_item" class="table table-hover">'+
							'<tbody>'+
								'<tr>'+
									'<td class="text-left" style="width: 25%"><b>Precio</b></td>'+
									'<td class="text-left" style="width: 75%">' + parseFloat(Ss_Precio).toFixed(2) + '</td>'+
								'</tr>';
								if (iIdTipoRubroEmpresaGlobal == 1) {//1=Farmacia
									sHtmlTableInfoItem +=
									'<tr>'+
										'<td class="text-left" style="width: 25%"><b>Composición</b></td>'+
										'<td class="text-left" style="width: 75%">' + (arrData.Txt_Composicion != null ? Txt_Composicion : '-') + '</td>'+
									'</tr>'+
									'<tr>'+
										'<td class="text-left" style="width: 25%"><b>Receta Médica</b></td>'+
										'<td class="text-left" style="width: 75%">' + (arrData.Nu_Receta_Medica == 0 ? 'No' : 'Si') + '</td>'+
									'</tr>';
								}
								sHtmlTableInfoItem +=
								'<tr>'+
									'<td class="text-left" style="width: 25%"><b>Ubicación</b></td>'+
									'<td class="text-left" style="width: 75%">' + (arrData.Txt_Ubicacion_Producto_Tienda != null ? arrData.Txt_Ubicacion_Producto_Tienda : '-') + '</td>'+
								'</tr>'+
								'<tr>'+
									'<td class="text-left" style="width: 25%"><b>Descripción</b></td>'+
									'<td class="text-left" style="width: 75%">' + (arrData.Txt_Producto != null ? arrData.Txt_Producto : '-') + '</td>'+
								'</tr>';
								sHtmlTableInfoItem +=
							'</tbody>'+
						'</table>';
						$( '#modal-body-info_item' ).append( sHtmlTableInfoItem );
					} else {
						console.log(arrResponseComposicion);
					}
				}, 'json'); // /. obtener composicion por item
			} else {
				$( '#modal-message' ).modal('show');
				$( '.modal-message' ).css("z-index", "2000");
				$( '.modal-message' ).addClass( 'modal-' + arrResponse.sStatus );
				$( '.modal-title-message' ).text( arrResponse.sMessage );
				setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
			}
		}, 'JSON');
	})
  	// ./ Lista de productos a compra

	// Button agregar items alternativos a venta temporal
	$( '#table-items_alternativos tbody' ).on('click', '#btn-agregar_item_venta_temporal', function(){
		arrItemVentaTemporal={
			iIdItem:$(this).data('id'),
			sNombreItem:$(this).data('nombre'),
			qItem:$(this).data('qt_producto'),
			fPrecio:$(this).data('precio'),
			iIdImpuestoCruceDocumento:$(this).data('id_impuesto_cruce_documento'),
			iTipoImpuesto:$(this).data('nu_tipo_impuesto'),
			fImpuesto: $(this).data('ss_impuesto'),
			iIdAlmacen: $('#cbo-almacen').val(),
			iCompuesto: 0,
			sCodigoInterno: $(this).data('no_codigo_interno'),
			iIdImpuestoIcbper: item.data('id_impuesto_icbper'),
			Ss_Icbper_item: $(this).data('ss_icbper'),
		}
		agregarItemVentaTemporal(arrItemVentaTemporal);
	})
	// ./ Button agregar items alternativos a venta temporal

	// Aperturar Modal de Cobranza del cliente
	$( "#btn-pagar" ).click(function(){
		cobrarCliente();
	})

	// Modal de cobranza al cliente
	$( '#cbo-modal_forma_pago' ).change(function(){
		$( '.div-billete_soles' ).show();
		$('.div-modal_credito').hide();

		ID_Medio_Pago = $(this).val();
		Nu_Tipo_Medio_Pago = $(this).find(':selected').data('nu_tipo_medio_pago');
		$( '.div-modal_datos_tarjeta_credito').hide();
		$( '#cbo-modal_tarjeta_credito' ).html('');
		$( '#tel-nu_referencia' ).val('');
		$( '#tel-nu_ultimo_4_digitos_tarjeta' ).val('');
		if (Nu_Tipo_Medio_Pago==2){
			$('.div-modal_credito').hide();
			$( '.div-billete_soles' ).hide();
			$( '.div-modal_datos_tarjeta_credito').show();

			url = base_url + 'HelperController/getTiposTarjetaCredito';
			$.post( url, {ID_Medio_Pago : ID_Medio_Pago} , function( response ){
				$( '#cbo-modal_tarjeta_credito' ).html('');
				for (var i = 0; i < response.length; i++)
					$( '#cbo-modal_tarjeta_credito' ).append( '<option value="' + response[i].ID_Tipo_Medio_Pago + '">' + response[i].No_Tipo_Medio_Pago + '</option>' );
			}, 'JSON');
		} else if ( Nu_Tipo_Medio_Pago==1 ) {
			$( '.div-billete_soles' ).hide();
			$('.div-modal_credito').show();

			var iDiasCreditoBD = 1;
			if ( $( '#txt-Nu_Dias_Credito' ).val() !== undefined && parseInt($( '#txt-Nu_Dias_Credito' ).val()) )
				iDiasCreditoBD=$( '#txt-Nu_Dias_Credito' ).val();

			var dNuevaFechaVencimiento = sumaFecha(iDiasCreditoBD, $( '#txt-Fe_Emision' ).val());
			$( '#txt-Fe_Vencimiento' ).val( dNuevaFechaVencimiento );
		}

		setTimeout(function(){ $( '.input-modal_monto' ).focus(); $( '.input-modal_monto' ).select(); }, 20);		
	})
  
	// Agregar pagos de cliente
	$( '#btn-add_forma_pago' ).click(function(){
		agregarFormasPagoCliente();
	})

	$( '#table-modal_forma_pago thead' ).on('click', '.icon-clear_all_forma_pago_pos', function(){
		$( '#div-modal_forma_pago' ).hide();
		
		$( '#table-modal_forma_pago tbody' ).empty();
	  
		$( '.label-modal_forma_pago_monto_total' ).text('0.00');
		$( '.input-modal_forma_pago_monto_total' ).val(0.00);
		
		$( '.label-vuelto_pos' ).text('0.00');
		$( '.input-vuelto_pos' ).val(0.00);

		$( '.label-saldo_pos_cliente' ).text('0.00');
		$( '.input-saldo_pos_cliente' ).val(0.00);
		
		$( '.input-modal_monto' ).val(parseFloat($( '.input-total_detalle_productos_pos' ).val()));//Monto restante a cobrar
		$( '.input-modal_monto' ).focus();
		$( '.input-modal_monto' ).select();
		
		$( '#btn-add_forma_pago' ).prop('disabled', false);
		$( '.btn-generar_pedido' ).prop('disabled', true);
	})

	$Sum_Ss_Monto_Total = 0.00;
	$( '#table-modal_forma_pago tbody' ).on('click', '#btn-delete_forma_pago_pos', function(){
    	$(this).closest('tr').remove();
    
		$Sum_Ss_Monto_Total = 0.00;
		$( '#table-modal_forma_pago > tbody > tr' ).each(function(){
			var fila = $(this);
			$Sum_Ss_Monto_Total += parseFloat(fila.find(".fTotal").text());
		});

		$Sum_Ss_Monto_Total = $Sum_Ss_Monto_Total.toFixed(2)
		$( '.label-modal_forma_pago_monto_total' ).text($Sum_Ss_Monto_Total);
		$( '.input-modal_forma_pago_monto_total' ).val($Sum_Ss_Monto_Total);
	
		$( '.input-modal_monto' ).val(parseFloat($( '.input-total_detalle_productos_pos' ).val()) - $Sum_Ss_Monto_Total);//Monto restante a cobrar
		$( '.input-modal_monto' ).focus();
	    
		$( '#btn-add_forma_pago' ).prop('disabled', false);
			
	    $( '.btn-generar_pedido' ).prop('disabled', false);
	    if ($( '#table-modal_forma_pago > tbody > tr' ).length == 0){
		    $( '#div-modal_forma_pago' ).hide();
		    
			$( '.label-modal_forma_pago_monto_total' ).text('0.00');
			$( '.input-modal_forma_pago_monto_total' ).val(0.00);
			
			$( '.label-vuelto_pos' ).text('0.00');
			$( '.input-vuelto_pos' ).val(0.00);
			
			$( '.label-saldo_pos_cliente' ).text('0.00');
			$( '.input-saldo_pos_cliente' ).val(0.00);
			
			$( '.input-modal_monto' ).val(parseFloat($( '.input-total_detalle_productos_pos' ).val()));//Monto restante a cobrar
			$( '.input-modal_monto' ).focus();
			$( '.input-modal_monto' ).select();
			
			$( '.btn-generar_pedido' ).prop('disabled', true);
	    }
	})// ./ btn Agregar formas de pago del cliente
	
	$( '.billete-soles' ).click(function(){
		$( '.input-modal_monto' ).val( $(this).val() );
		$( '.input-modal_monto' ).focus();
		$( '.input-modal_monto' ).select();
	})

	// Modal - Generar comprobante
	$('#btn-ticket').click(function (e) {
		e.preventDefault();
		if (!isLoading)
			generarComprobante();
	})
	
	$( '#cbo-recepcion' ).change(function(){
		$( '.modal-delivery' ).modal('hide');
		if ( $(this).val() == 6 ) {
			$( '.modal-delivery' ).modal('show');
		}
	})
	
	$( '#btn-atajos_teclado' ).click(function(){
		$( '.modal-atajos_teclado' ).modal('show');
	})
	
	$( '#btn-add_nota_global' ).click(function(){
		$( '.modal-add_nota_global' ).modal('show');
	})

	$('#btn-guias_remision_pos').click(function () {
		$('.modal-guias_remision').modal('show');
	})
	
	$( '#table-detalle_productos_pos > tbody' ).on('click', '#btn-add_nota_producto_pos', function(){
		var fila = $(this).parents("tr");
		var id_item = fila.find( ".td-sNotaItem" ).data( "id_item" );
		var estado = fila.find( ".td-sNotaItem" ).data( "estado" );

		if (estado == 'mostrar') {
			fila.find( "#td-sNotaItem" + id_item ).show();
			fila.find( ".td-sNotaItem" ).data( "estado", "ocultar" );
			fila.find( ".td-sNombreItem" ).css( "width", "5%");
			fila.find( ".input-sNotaItem" ).focus();
		} else {
			fila.find( "#td-sNotaItem" + id_item ).hide();
			fila.find( ".td-sNotaItem" ).data( "estado", "mostrar" );
			fila.find( ".td-sNombreItem" ).css( "width", "44%");
		}
	})

	$(document).on('click', '.nav-tabs-lista_categorias > .li-hijo_categorie', function () {
		$(this).addClass("active").siblings().removeClass("active");
		var arrParams = {
			sUrl: 'HelperController/getItems',
			ID_Almacen: $('#cbo-almacen').val(),
			iIdListaPrecio: $('#cbo-lista_precios').val(),
			ID_Linea: $(this).data('id_categorie'),
		}
		getItems(arrParams);
	});

	$(document).on('click', '.nav-tabs-lista_categorias > .li-top_sale', function () {
		var arrParams = {
			sUrl: 'HelperController/getItems',
			ID_Almacen: $('#cbo-almacen').val(),
			iIdListaPrecio: $('#cbo-lista_precios').val(),
			ID_Linea: 'top_sale',
		}
		getItems(arrParams);
	});

	$(document).on('click', '.nav-tabs-lista_categorias > .li-favorito', function () {
		var arrParams = {
			sUrl: 'HelperController/getItems',
			ID_Almacen: $('#cbo-almacen').val(),
			iIdListaPrecio: $('#cbo-lista_precios').val(),
			ID_Linea: 'favorito',
		}
		getItems(arrParams);
	});

	$('#cbo-lista_precios').change(function () {
		var arrParams = {
			sUrl: 'HelperController/getItems',
			ID_Almacen: $('#cbo-almacen').val(),
			iIdListaPrecio: $(this).val(),
			ID_Linea: 'favorito',
		};
		getItems(arrParams);
	});
}); // ./ document-ready

// Cargar items para ventas del mas vendido a menor
function getItems(arrParams) {
	url = base_url + arrParams.sUrl;
	var sendData = {
		ID_Almacen: arrParams.ID_Almacen,
		ID_Lista_Precio_Cabecera: arrParams.iIdListaPrecio,
		ID_Linea: arrParams.ID_Linea,
	}

	$.post(url, sendData, function (response) {
		$('.nav-tabs-lista_categorias').empty();
		var nav_lista_categorias = '', i = response.arrAllCategorie.length, nav_lista_categorias_class_active = '';
		if (i > 0) {
			//nav_lista_categorias += '<li class="li-top_sale ' + (arrParams.ID_Linea == 'top_sale' ? 'active' : '') + '"><a href="#" style="color: #514d4c;font-weight:bold;">Más vendido</a></li>';
			//nav_lista_categorias += '<li class="li-hijo_categorie ' + (arrParams.ID_Linea == 0 ? 'active' : '') + '" data-id_categorie=0><a href="#" style="color: #514d4c;font-weight:bold;">Todos</a></li>';
			nav_lista_categorias += '<li class="li-favorito ' + (arrParams.ID_Linea == 'favorito' ? 'active' : '') + '" title="Para crear favoritos ir a Logística > Reglas de Logística > Producto"><a href="#" style="cursor:pointer; color: #514d4c;font-weight:bold;">Favoritos</a></li>';
			for (var x = 0; x < i; x++) {
				nav_lista_categorias_class_active = '';
				if (arrParams.ID_Linea == response.arrAllCategorie[x].ID_Familia)
					nav_lista_categorias_class_active = 'active';
				nav_lista_categorias += '<li class="li-hijo_categorie ' + nav_lista_categorias_class_active + '" data-id_categorie=' + response.arrAllCategorie[x].ID_Familia + '>';
					nav_lista_categorias += '<a href="#" style="color: #514d4c;font-weight:bold;"> ' + response.arrAllCategorie[x].No_Familia + '</a>';
				nav_lista_categorias += '</li>';
			}
		} else
			nav_lista_categorias = '';
		$('.nav-tabs-lista_categorias').append(nav_lista_categorias);

		$('.div-lista_cuadro_items').empty();
		var pos_items = '', Ss_Precio_Panel = 0.00, qStockActual = 0.00;
		i = response.arrAllItemsCategorie.length;
		if (i > 0) {
			for (var x = 0; x < i; x++) {
				Ss_Precio_Panel = parseFloat(response.arrAllItemsCategorie[x].Ss_Precio);
				if (parseFloat(response.arrAllItemsCategorie[x].Ss_Precio_Lista) > 0.00)
					Ss_Precio_Panel = parseFloat(response.arrAllItemsCategorie[x].Ss_Precio_Lista);
				qStockActual = parseFloat(response.arrAllItemsCategorie[x].Qt_Producto).toFixed(0);
				pos_items += '<li class="li-item_pos list-group-item col-xs-6 col-sm-6 col-md-4 col-lg-3" style="padding: 0px 5px;" value=' + response.arrAllItemsCategorie[x].ID_Producto + '>';
					pos_items += '<div class="div-pos-img_item">';
						if (response.arrAllItemsCategorie[x].No_Imagen_Item != null && response.arrAllItemsCategorie[x].No_Imagen_Item != '' && response.arrAllItemsCategorie[x].No_Imagen_Item !== undefined)
							pos_items += '<img id="' + response.arrAllItemsCategorie[x].ID_Producto + '" class="img-responsive img-pos-view" src="' + response.arrAllItemsCategorie[x].No_Imagen_Item + '">';
						else
							pos_items += '<img id="' + response.arrAllItemsCategorie[x].ID_Producto + '" class="img-responsive img-pos-view" src="../../assets/img/imagen_nodisponible.png">';
					pos_items += '</div>';
					pos_items += '<div style="height: 50px;">';
						pos_items += '<label style="font-weight: normal;" class="multiple-line">' + response.arrAllItemsCategorie[x].No_Producto + '</label>';
					pos_items += '</div>';
					pos_items += '<div>';
						pos_items += '<span class="label label-success" style="font-weight: normal; font-size: 13px; border-radius: .25rem; display: block; padding-top: 6px !important; text-align:left; background-color: #4f4f4f !important;">';
						pos_items += $('#hidden-no_signo_caja_pos').val() + ' ' + Ss_Precio_Panel.toFixed(2) + ' &nbsp;&nbsp;<label style="font-weight: normal; font-size: 12px;">&nbsp;';
						if (response.arrAllItemsCategorie[x].Nu_Tipo_Producto == 1)
							pos_items += '<span title="Stock: ' + (!isNaN(qStockActual) ? qStockActual : 'Sin Stock') + '"><b>S:</b> ' + (!isNaN(qStockActual) ? qStockActual : 'Sin Stock') + '</span>';
						pos_items += '</label></span>';
					pos_items += '</div>';
				pos_items += '</li>';
			}
		} else
			pos_items = '<li class="list-group-item col-sm-12">No hay registros.</li>';
		$('.div-lista_cuadro_items').append(pos_items);
	}, 'JSON');
} // ./ cargar items por li cuadro para pantalla touch

// buscar item por evento clic
function buscarItem(n) {
	var sendData = {
        global_table : 'producto',
        global_search : n,
        filter_id_almacen : '',
        filter_nu_compuesto : '',
        filter_nu_tipo_producto : 2,//2 = Producto
        filter_lista : $( '#cbo-lista_precios' ).val(),
	};
	
  	var tr_body_table_detalle_productos_pos = "", $Ss_Total_Producto_POS = 0.00, $Sum_Ss_Total_POS = 0.00, Ss_Precio = 0;
	$.post( base_url + 'AutocompleteController/getAllProductClic', sendData, function( response ){
		if ( response.length > 0 ) {
			response = response[0];//Me devuelve un arreglo

			Ss_Precio = 0;
			if ((response.Ss_Precio === null || response.Ss_Precio == 0.000000) && (response.Ss_Precio_Item !== null || response.Ss_Precio_Item != 0.000000))
				Ss_Precio = response.Ss_Precio_Item;
			if ( response.Ss_Precio !== null )
				Ss_Precio = response.Ss_Precio;
			arrItemVentaTemporal={
				iIdItem: response.ID,
				iTipoProducto: response.Nu_Tipo_Producto,
				iCodigoItem:response.Codigo,
				sNombreItem:response.Nombre,
				qItem:response.Qt_Producto,
				fPrecio:Ss_Precio,
				iIdImpuestoCruceDocumento:response.ID_Impuesto_Cruce_Documento,
				iTipoImpuesto:response.Nu_Tipo_Impuesto,
				fImpuesto: response.Ss_Impuesto,
				iIdAlmacen: $('#cbo-almacen').val(),
				iCompuesto: response.Nu_Compuesto,
				sCodigoInterno: response.No_Codigo_Interno,
				iIdImpuestoIcbper: response.ID_Impuesto_Icbper,
				Ss_Icbper_item: response.Ss_Icbper,
			}
			agregarItemVentaTemporal(arrItemVentaTemporal);
			
			if (iIdTipoRubroEmpresaGlobal == 1) {// 1 = Farmacia
				$('#table-items_alternativos tbody').empty();

				if (response.Txt_Composicion != '' && response.Txt_Composicion != null) {
					arrDataAlternativos = {
						iIdListaPrecio: $('#cbo-lista_precios').val(),
						iIdItem: ID,
						sComposicion: response.Txt_Composicion,
					}
					buscarItemAlternativos(arrDataAlternativos);
				}
			}
		} else
			alert('No hay datos');
	}, 'JSON')
}// ./ buscar item por evento clic

function agregarItemVentaTemporal(arrParams){
	$fPrecioItem=(arrParams.fPrecio !== null ? arrParams.fPrecio : '0')
	$fPrecioItem=(parseFloat($fPrecioItem).toFixed(2)).toString().split(". ");

	validacionAlertas(arrParams);

	if (iValidarStockGlobal == 1 && ($('#txt-nu_tipo_item').val() == 1 || arrParams.iTipoProducto == 1) && (parseFloat(arrParams.qItem) <= 0.000000 || arrParams.qItem == null)) {
		$modal_msg_stock = $( '.modal-message' );
		$modal_msg_stock.modal('show');

		$modal_msg_stock.removeClass('modal-danger modal-warning modal-success');
		$modal_msg_stock.addClass('modal-warning');

		$( '.modal-title-message' ).text('Sin stock disponible');

		setTimeout(function() {$modal_msg_stock.modal('hide');}, 1300);
	} else {
		$( '#txt-ID_Producto' ).val('');
		$( '#txt-Nu_Codigo_Barra' ).val('');
		$( '#txt-No_Producto' ).val('');
		$( '#txt-Ss_Precio' ).val('');
		$( '#txt-Ss_Precio_Interno' ).val('');
		$( '#txt-ID_Impuesto_Cruce_Documento' ).val('');
		$( '#txt-Nu_Tipo_Impuesto' ).val('');
		$( '#txt-Ss_Impuesto' ).val('');
		$( '#txt-Qt_Producto' ).val('');

		if (isExistElementProductoPOS(arrParams.iIdItem)) {//Si existe el item, sumamos
			var ID_Item = arrParams.iIdItem;
			$('#table-detalle_productos_pos > tbody > tr').each(function () {
				var fila = $(this);
				var iTableIdItem = fila.find(".td-iIdItem").text();
				if (iTableIdItem == ID_Item) {
					var cantidad = parseFloat(fila.find(".txt-Qt_Producto_Class_Unico").val()) + 1;
					fila.find(".txt-Qt_Producto_Class_Unico").val(cantidad);
					var precio = parseFloat(fila.find(".input-fPrecioItem").val());
					var cantidad = fila.find(".txt-Qt_Producto_Class_Unico").val();
					fila.find(".td-Ss_Total_Producto").text(((precio * cantidad).toFixed(2)).toString().split(". "));
				}

				calcularTotales();
			});

			$('#txt-No_Producto').focus();
			$('#txt-No_Producto').select();
		} else {
			var sCssDisplayPrecio = 'display:none;';
			if (iPrecioPuntoVenta == 1 )
				sCssDisplayPrecio='';

			var sCssDisplayDsctoPV = 'display:none;';
			if ( iActivarDescuentoPuntoVenta == 1 )
				sCssDisplayDsctoPV='';

			tr_body_table_detalle_productos_pos += 
			"<tr id='tr_detalle_producto_pos" + arrParams.iIdItem + "' style='background-color: white !important;'>"
				+"<td style='display:none;' class='text-left td-iIdItem'>" + arrParams.iIdItem + "</td>"
				+"<td style='width: 15%' class='text-right'><input type='text' inputmode='decimal' id=" + arrParams.iIdItem + " class='txt-Qt_Producto_Class_Unico pos-input hotkey-limpiar_item hotkey-cancelar_venta hotkey-focus_item hotkey-cobrar_cliente txt-Qt_Producto form-control input-decimal input-qItem' " + (arrParams.iTipoProducto == 1 ? 'onkeyup=validateStockNow(event);' : '') + " value='1' data-id_item=" + arrParams.iIdItem + " data-ss_icbper_item='0.00' data-ss_icbper='" + arrParams.Ss_Icbper_item + "' data-id_impuesto_icbper='" + arrParams.iIdImpuestoIcbper + "' data-id_impuesto_cruce_documento='" + arrParams.iIdImpuestoCruceDocumento + "' data-nu_tipo_impuesto='" + arrParams.iTipoImpuesto + "' data-ss_impuesto='" + arrParams.fImpuesto + "' autocomplete='off' title='cantidad'></td>"
				+"<td style='width:35%' class='text-left td-sNombreItem' title='Nombre item'>" + arrParams.sNombreItem + "</td>"
				+"<td style='width: 15%; " + sCssDisplayPrecio + "' class='text-right'><input type='text' inputmode='decimal' class='pos-input hotkey-limpiar_item hotkey-cancelar_venta hotkey-focus_item hotkey-cobrar_cliente txt-Qt_Producto form-control input-decimal input-fPrecioItem' value='" + $fPrecioItem + "' data-id_item=" + arrParams.iIdItem + " data-id_impuesto_icbper='" + arrParams.iIdImpuestoIcbper + "' data-id_impuesto_cruce_documento='" + arrParams.iIdImpuestoCruceDocumento + "' data-nu_tipo_impuesto='" + arrParams.iTipoImpuesto + "' data-ss_impuesto='" + arrParams.fImpuesto + "' autocomplete='off' title='precio'></td>"
				+"<td style='width: 15%; " + sCssDisplayDsctoPV + "' class='text-right'><input type='text' inputmode='decimal' class='pos-input hotkey-limpiar_item hotkey-cancelar_venta hotkey-focus_item hotkey-cobrar_cliente form-control input-decimal input-fDescuentoItem' autocomplete='off' placeholder='Dscto.' title='Descuento (opcional)'></td>"
				+"<td style='width: 15%' class='text-right td-Ss_Total_Producto' title='total'>" + $fPrecioItem + "</td>"
				+"<td style='display:none; width: 39%' class='text-right td-sNotaItem' data-estado='mostrar' data-id_item=" + arrParams.iIdItem + " id='td-sNotaItem" + arrParams.iIdItem + "'>"
					+"<textarea class='pos-input form-control input-sNotaItem hotkey-cobrar_cliente hotkey-cancelar_venta hotkey-limpiar_item hotkey-focus_item' placeholder='' maxlength='250' autocomplete='off'></textarea></td>"
				+"</td>"
				+"<td class='text-center'>"
					+"<button type='button' id='btn-add_nota_producto_pos' class='btn btn-sm btn-link' alt='Nota' title='Nota'><i class='fa fa-edit fa-2x' aria-hidden='true'></i></button>"
				+"</td>"
				+"<td style='display:none;' class='text-right td-fDescuentoPorcentajeItem'>0</td>"
				+"<td style='display:none;' class='text-right td-fDescuentoSinImpuestosItem'>0</td>"
				+"<td style='display:none;' class='text-right td-fDescuentoImpuestosItem'>0</td>"
				+"<td style='display:none;' class='text-right td-fSubtotalItem'>" + ($fPrecioItem / parseFloat(arrParams.fImpuesto)).toFixed(6) + "</td>"
				+"<td style='display:none;' class='text-right td-fImpuestoItem'>" + ($fPrecioItem - ($fPrecioItem / parseFloat(arrParams.fImpuesto))).toFixed(6) + "</td>"
				+"<td style='width: 6%' class='text-center'><button type='button' id='btn-delete_producto_pos' class='btn btn-sm btn-link' alt='Eliminar' title='Eliminar'><i class='fa fa-trash-o fa-lg' aria-hidden='true'></i></button></td>"
				+"<td style='width: 6%' class='text-center'><button type='button' id='btn-ver_producto_pos' data-id_item="+arrParams.iIdItem+" class='btn btn-sm btn-link' alt='Ver' title='Ver'>ver</button></td>"
			+"</tr>";

			$( '#table-detalle_productos_pos' ).show();
			$( '#table-detalle_productos_pos > tbody ' ).append(tr_body_table_detalle_productos_pos);
			tr_body_table_detalle_productos_pos='';

			$( '#txt-No_Producto' ).focus();
			$( '#txt-No_Producto' ).select();
			setTimeout(function(){ 
				$( '#txt-No_Producto' ).focus(); $( '#txt-No_Producto' ).select(); 
			}, 30);

			calcularTotales();
			calcularIcbper();
	
			validateDecimal();
			
			// Combinacion de teclas
			$('input.hotkey-limpiar_item').bind('keydown', 'F2', function(){
				$( '#txt-ID_Producto' ).val( '' );
				$( '#txt-No_Producto' ).val( '' );
			});

			// Cancelar venta
			$('input.hotkey-cancelar_venta').bind('keydown', 'esc', function(){
				limpiarValoresVenta(1);
			});

			// Focus item
			$('input.hotkey-focus_item').bind('keydown', 'F4', function(){
				$( '#txt-No_Producto' ).focus();
			});
			// ./ Focus item

			// Button cobrar
			$('input.hotkey-cobrar_cliente').bind('keydown', 'return', function(){
				if ( $('#btn-pagar').prop('disabled') == false ){
					cobrarCliente();
				}
			});
			// ./ Button cobrar
			// ./ Combinacion de tecla
		}// ./ Verificacion si el item fue seleccionado para comprar
	}// ./ Validacion de Stock
}// Agregar venta temporal del cliente

// Buscar items alternativos
function buscarItemAlternativos(arrDataAlternativos){
	$.post( base_url + 'AutocompleteController/getItemAlternativos', arrDataAlternativos, function( response ){
		agregarItemAlternativos(response);
	}, 'JSON')
}// ./ Buscar items alternativos

// Buscar items alternativos de autocomplete
function autocompleteItemsAlternativos(sValue) {
	if ($('#hidden-iTipoRubroEmpresa').val() == 1) {//1=Farmacia
		if ( sValue.length > 0 ) {
			arrDataAlternativos={
				iIdListaPrecio : $( '#cbo-lista_precios' ).val(),
				sNombreUpcSkuItem : sValue,
				iValidarStockGlobal : iValidarStockGlobal,
			}
			$.post( base_url + 'AutocompleteController/autocompleteItemAlternativos', arrDataAlternativos, function( response ){
				agregarItemAlternativos(response);
			}, 'JSON')
		}
	}
}

// Generar tabla de alternativos por composicion
function agregarItemAlternativos(arrResponse){
	$( '#table-items_alternativos tbody' ).empty();

	var tr_body_table_alternativo = '';
	if ( arrResponse.sStatus=='success' ) {
		var l = arrResponse.arrData.length, $fPrecioItem = 0;
		for (var x=0; x < l; x++){
			arrData = arrResponse.arrData;
			$fPrecioItem = 0;
			if ( arrData[x].Ss_Precio_Item !== null || arrData[x].Ss_Precio_Item != 0.000000 )
				$fPrecioItem = arrData[x].Ss_Precio_Item;
			if ( (arrData[x].Ss_Precio_Item === null || arrData[x].Ss_Precio_Item == 0.000000) && arrData[x].Ss_Precio !== null )
				$fPrecioItem = arrData[x].Ss_Precio;

			tr_body_table_alternativo += 
			"<tr id='tr_item_alternativo" + arrData[x].ID + "'>"
				+"<td style='display:none;' class='text-left'>" + arrData[x].iIdItem + "</td>"
				+"<td class='text-center' style='width: 10%'>" + arrData[x].Qt_Producto + "</td>"
				+"<td class='text-left' style='width: 60%'>" + arrData[x].Nombre + "</td>"
				+"<td class='text-right td-fPrecioItem' style='width: 20%'>" + $fPrecioItem + "</td>"
				+"<td class='text-center' style='width: 10%'><button type='button' id='btn-agregar_item_venta_temporal' class='btn btn-sm btn-link' alt='Agregar' title='Agregar' data-id='" + arrData[x].ID + "' data-ss_icbper_item='0.00' data-ss_icbper='" + arrData[x].Ss_Icbper_item + "' data-id_impuesto_icbper='" + arrData[x].ID_Impuesto_Icbper + "' data-no_codigo_interno='" + arrData[x].No_Codigo_Interno + "' data-nombre='" + arrData[x].Nombre + "' data-qt_producto='" + arrData[x].Qt_Producto + "' data-precio='" + $fPrecioItem + "' data-id_impuesto_cruce_documento='" + arrData[x].ID_Impuesto_Cruce_Documento + "' data-nu_tipo_impuesto='" + arrData[x].Nu_Tipo_Impuesto + "' data-ss_impuesto='" + arrData[x].Ss_Impuesto + "'>Agregar</button></td>"
			+"</tr>";
		}
    } else {
		tr_body_table_alternativo += 
		"<tr>"
			+"<td colspan='4' class='text-center'>" + arrResponse.sMessage + "</td>"
		+"</tr>";
	}

	$( '#table-items_alternativos' ).show();
	$( '#table-items_alternativos > tbody ' ).append(tr_body_table_alternativo);
	tr_body_table_alternativo='';
}// ./ Generar tabla de alternativos por composicion

function isExistElementProductoPOS($ID_Producto){
	return Array.from($('tr[id*=tr_detalle_producto_pos]'))
	.some(element => ($('td:nth(0)',$(element)).html()==$ID_Producto));
}

function validarVentaPrevia(){
	var arrValidarNumerosEnCero = [], $counterNumerosEnCero = 0, arrValidarDescuentoMayorTotal = [], $counterDescuentoMayorTotal = 0;
	$("#table-detalle_productos_pos > tbody > tr").each(function(){
		fila = $(this);
		
		$ID_Producto = fila.find(".td-iIdItem").text();
		$Qt_Producto = fila.find(".input-qItem").val();
		$Ss_Precio = parseFloat(fila.find(".input-fPrecioItem").val());
		$Ss_Total_Producto = fila.find(".td-Ss_Total_Producto").text();

		$fDescuentoItem = parseFloat(fila.find(".td-fDescuentoSinImpuestosItem").text()) + parseFloat(fila.find(".td-fDescuentoImpuestosItem").text());
		
		$('#tr_detalle_producto_pos' + $ID_Producto).removeClass('danger');
		if (parseFloat($Qt_Producto) == 0 || parseFloat($Ss_Precio) == 0 || parseFloat($Ss_Total_Producto) == 0){
			arrValidarNumerosEnCero[$counterNumerosEnCero] = $ID_Producto;
			$('#tr_detalle_producto_pos' + $ID_Producto).addClass('danger');
		}
		$counterNumerosEnCero++;
	});
	$( '#table-detalle_productos_pos tfoot' ).empty();

	$('.form-group').removeClass('has-error');
	$('.help-block').empty();
	if ($('#cbo-almacen').val() == 0) {
		$('#cbo-almacen').closest('.form-group').find('.help-block').html('Seleccionar almacén');
		$('#cbo-almacen').closest('.form-group').removeClass('has-success').addClass('has-error');

		scrollToError($("html, body"), $('#cbo-almacen'));
		return false;
	} else if ( $('#cbo-tipo_documento').val() == 4 && $('.input-total_detalle_productos_pos').val() >= 700.00 && $('#cbo-TiposDocumentoIdentidad').val() == 2 && ($('#txt-ACodigo').val().length < 8 || $('#txt-ANombre').val().length === 0)) {//4=Boleta
		$('#cbo-tipo_documento').closest('.form-group').find('.help-block').html('La venta es mayor igual a <b>S/ 700.00</b> ingresar DNI y Nombres');
		$('#cbo-tipo_documento').closest('.form-group').removeClass('has-success').addClass('has-error');

		scrollToError($("html, body"), $('#cbo-tipo_documento'));
		return false;
	} else if ( ($('#cbo-tipo_documento').val() == 3 || $('#cbo-tipo_documento').val() == 4) && $('#hidden-estado_entidad').val() == 0) {
		$('#txt-ACodigo').closest('.form-group').find('.help-block').html(($('#cbo-tipo_documento').val() == 4 ? 'DNI' : 'RUC') + ' inválido');
		$('#txt-ACodigo').closest('.form-group').removeClass('has-success').addClass('has-error');

		scrollToError($("html, body"), $('#txt-ACodigo'));
		return false;
	} else if ( $('#cbo-tipo_documento').val() == 3 && $('#txt-ACodigo').val().length === 0) {//3=Factura
		$('#txt-ACodigo').closest('.form-group').find('.help-block').html('Ingresar RUC');
		$('#txt-ACodigo').closest('.form-group').removeClass('has-success').addClass('has-error');

		scrollToError($("html, body"), $('#txt-ACodigo'));
		return false;
	} else if ( $('#cbo-TiposDocumentoIdentidad').val() == 4 && ($('#cbo-TiposDocumentoIdentidad').find(':selected').data('nu_cantidad_caracteres') != $('#txt-ACodigo').val().length)) {
		$('#txt-ACodigo').closest('.form-group').find('.help-block').html('Debe ingresar ' + $('#cbo-TiposDocumentoIdentidad').find(':selected').data('nu_cantidad_caracteres') + ' dígitos');
		$('#txt-ACodigo').closest('.form-group').removeClass('has-success').addClass('has-error');

		scrollToError($("html, body"), $('#txt-ACodigo'));
		return false;
	} else if ( $('#txt-Txt_Email_Entidad_Cliente').val().length > 0 && !caracteresCorreoValido($('[name="Txt_Email_Entidad"]').val(), '#div-email')) {//3=Cliente Nuevo
		scrollToError($("html, body"), $('#txt-Txt_Email_Entidad_Cliente'));
		alert('ingresa un correo válido');
		return false;
	} else if ($('#cbo-tipo_documento').val() == 3 && $('#cbo-TiposDocumentoIdentidad').val() != 4) {
		$('#txt-ACodigo').closest('.form-group').find('.help-block').html('Solo se puede emitir Facturas con RUC');
		$('#txt-ACodigo').closest('.form-group').removeClass('has-success').addClass('has-error');

		scrollToError($("html, body"), $('#txt-ACodigo'));
		return false;
	} else if ($('#cbo-tipo_documento').val() == 4 && $('#cbo-TiposDocumentoIdentidad').val() == 2 && ($('#txt-ACodigo').val().length > 0 && $('#txt-ACodigo').val().length < 8)) {
		$('#txt-ACodigo').closest('.form-group').find('.help-block').html('Debe ingresar ' + $('#cbo-TiposDocumentoIdentidad').find(':selected').data('nu_cantidad_caracteres') + ' dígitos');
		$('#txt-ACodigo').closest('.form-group').removeClass('has-success').addClass('has-error');

		scrollToError($("html, body"), $('#txt-ACodigo'));
		return false;
	} else if (($('#cbo-tipo_documento').val() == 3 || $('#cbo-tipo_documento').val() == 2) && $('#cbo-TiposDocumentoIdentidad').val() == 4 && $('#txt-ACodigo').val().length == 11 && !caracteresDNIRUCValido($('#txt-ACodigo').val())) {
		$('#txt-ACodigo').closest('.form-group').find('.help-block').html('Debe ingresar solo números');
		$('#txt-ACodigo').closest('.form-group').removeClass('has-success').addClass('has-error');

		scrollToError($("html, body"), $('#txt-ACodigo'));
		return false;
	} else if (($('#cbo-tipo_documento').val() == 4 || $('#cbo-tipo_documento').val() == 2) && $('#cbo-TiposDocumentoIdentidad').val() == 2 && $('#txt-ACodigo').val().length == 8 && !caracteresDNIRUCValido($('#txt-ACodigo').val())) {
		$('#txt-ACodigo').closest('.form-group').find('.help-block').html('Debe ingresar solo números');
		$('#txt-ACodigo').closest('.form-group').removeClass('has-success').addClass('has-error');

		scrollToError($("html, body"), $('#txt-ACodigo'));
		return false;
	} else if (($('#cbo-tipo_documento').val() == 4 || $('#cbo-tipo_documento').val() == 2) && $('#cbo-TiposDocumentoIdentidad').val() == 2 && $('.input-total_detalle_productos_pos').val() >= 700.00 && $('#txt-ANombre').val().trim().length < 3) {
		$('#txt-ANombre').closest('.form-group').find('.help-block').html('Debes tener mínimo 3 carácteres');
		$('#txt-ANombre').closest('.form-group').removeClass('has-success').addClass('has-error');

		//scrollToError($("html, body"), $('#txt-ANombre'));
		return false;
	} else if (($('#cbo-tipo_documento').val() == 4 || $('#cbo-tipo_documento').val() == 2) && $('#cbo-TiposDocumentoIdentidad').val() != 2 && $('#txt-ANombre').val().trim().length < 3) {
		$('#txt-ANombre').closest('.form-group').find('.help-block').html('Debes tener mínimo 3 carácteres');
		$('#txt-ANombre').closest('.form-group').removeClass('has-success').addClass('has-error');

		//scrollToError($("html, body"), $('#txt-ANombre'));
		return false;
	} else if ($('[name="radio-addWhatsapp"]:checked').attr('value') == 1 && String(parseInt($('#txt-Nu_Celular_Entidad_Cliente').val().replace(/ /g, ""))).length < 9) {
		$('#txt-Nu_Celular_Entidad_Cliente').closest('.form-group').find('.help-block').html('Debes ingresar 9 dígitos');
		$('#txt-Nu_Celular_Entidad_Cliente').closest('.form-group').removeClass('has-success').addClass('has-error');

		scrollToError($("html, body"), $('#txt-Nu_Celular_Entidad_Cliente'));
		return false;
	} else if (arrValidarNumerosEnCero.length > 0) {
		var tr_foot = '';
		tr_foot +=
		"<tfoot>"
			+"<tr class='danger'>"
				+"<td colspan='9' class='text-center'>Item(s) con <b>precio / cantidad / total en cero</b></td>"
			+"</tr>"
		+"<tfoot>";
		$( '#table-detalle_productos_pos > tbody' ).after(tr_foot);
		return false;
	} else if (parseFloat($('.input-total_descuento').val()) >= parseFloat($('.input-total_detalle_productos_pos').val())) {
		alert('El descuento total no puede ser mayor o igual al total');
		return false;
	} else if (validarDescuentoTotalPorImpuestoTributario() == 1) {//1=que tiene items diferente a IGV y no se puede brindar descuento TOTAL
		calcularTotales();
		alert('No se puede brindar descuento TOTAL solo por ÍTEM. Si es IGV si se puede por TOTAL o ÍTEM.');
		return false;
	} else if ($('#cbo-tipo_documento').val() != 2 && parseFloat($('.input-total_detalle_productos_pos').val()) < 0.10) {
		alert('El total no puede ser menor a 0.10 céntimos');
		return false;
	}
    $( '#table-detalle_productos_pos tfoot' ).empty();
	$( '.help-block' ).empty();
	return true;
}

function cobrarCliente(){
	// Validacion venta previa
	bEstadoValidacionPOS = validarVentaPrevia();
	if ( bEstadoValidacionPOS ){
		$( '.modal_forma_pago' ).modal('show');

		var $ID_Medio_Pago, $Nu_Tipo_Medio_Pago, $ID_Tarjeta_Credito, $No_Medio_Pago, $No_Tarjeta_Credito, $Nu_Tarjeta_Credito, $Nu_Transaccion, $Ss_Monto=0.00, $ID_Medio_Pago_Tarjeta_Credito, $Ss_Monto_Restante_Cobrar=0.00, tr_body_modal_forma_pago, $iVerificarIdMedioPagoGuardado = 0, $Sum_Ss_Monto_Total = 0.00;
		$("#table-modal_forma_pago > tbody > tr").each(function(){
			fila = $(this);
			$iVerificarIdMedioPagoGuardado = fila.find(".iIdMedioPago").text();
			$Sum_Ss_Monto_Total += parseFloat(fila.find(".fTotal").text());
		});
		
		$Ss_Monto_Restante_Cobrar = parseFloat($( '.input-total_detalle_productos_pos' ).val()) - $Sum_Ss_Monto_Total;
		
		if ( 
			(parseFloat($( '.input-modal_forma_pago_monto_total' ).val()) >= parseFloat($( '.input-total_detalle_productos_pos' ).val()))
			|| ($Ss_Monto_Restante_Cobrar > 0.00 && $iVerificarIdMedioPagoGuardado == 4)
		) {
			$( '.input-modal_monto' ).val( '' );
			$( '#btn-ticket' ).prop('disabled', false);
			
			$Sum_Ss_Monto_Total = 0.00;
			$("#table-modal_forma_pago > tbody > tr").each(function(){
				fila = $(this);
				$Sum_Ss_Monto_Total += parseFloat(fila.find(".fTotal").text());
			});
	
			$Ss_Vuelto_Pos = 0.00;
			$Ss_Vuelto_Pos = $Sum_Ss_Monto_Total - parseFloat($( '.input-total_detalle_productos_pos' ).val());
			
			if ($Ss_Vuelto_Pos >= 0){
				$( '.label-vuelto_pos' ).text( $Ss_Vuelto_Pos.toFixed(2) );
				$( '.input-vuelto_pos' ).val( $Ss_Vuelto_Pos.toFixed(2) );
				
				$( '#btn-add_forma_pago' ).prop('disabled', true);
				$( '.btn-generar_pedido' ).prop('disabled', false);
			}
		} else {
			$( '.input-modal_monto' ).val($( '.input-total_detalle_productos_pos' ).val());
			$( '#btn-add_forma_pago' ).prop('disabled', false);
			
			$( '.label-vuelto_pos' ).text( '0.00' );
			$( '.input-vuelto_pos' ).val( '0.00' );

			$('#btn-ticket').prop('disabled', true);
		}

		$( '.modal_forma_pago' ).on('shown.bs.modal', function() {
			$( '.input-modal_monto' ).focus();
			$( '.input-modal_monto' ).select();
			
			// Combinacion de tecla
			// Button add forma de pago y generar ticket			
			$('input.input-modal_monto').bind('keydown', 'return', function (e) {
				$Sum_Ss_Monto_Total = 0.00;
				$("#table-modal_forma_pago > tbody > tr").each(function(){
					fila = $(this);
					$iVerificarIdMedioPagoGuardado = fila.find(".iIdMedioPago").text();
					$Sum_Ss_Monto_Total += parseFloat(fila.find(".fTotal").text());
				});

				if ( $('#btn-add_forma_pago').prop('disabled') == false ){
					agregarFormasPagoCliente();
				}

				if ( 
					($Sum_Ss_Monto_Total >= parseFloat($('.input-total_detalle_productos_pos').val()) || ($Ss_Monto_Restante_Cobrar > 0.00 && $iVerificarIdMedioPagoGuardado == 4))
					&& $('#btn-ticket').prop('disabled') == false
					&& $( '#table-detalle_productos_pos > tbody > tr' ).length > 0
					&& $( '#table-modal_forma_pago > tbody > tr' ).length > 0
				) {
					e.preventDefault();
					if (!isLoading)
						generarComprobante();
				}
			});
			// ./ Button generar ticket
			// ./ Combinacion de tecla
		})
	}
}

// Modal verificar si el cajero ingreso 2 veces el mismo medio de pago
function isExistElementFormaPago($ID_Medio_Pago_Tarjeta_Credito){
	return Array.from($('tr[id*=tr_forma_pago]'))
	.some(element => ($('td:nth(0)',$(element)).html()===$ID_Medio_Pago_Tarjeta_Credito));
}

function agregarFormasPagoCliente(){
	$ID_Medio_Pago = $( '#cbo-modal_forma_pago option:selected' ).val();
	$ID_Tarjeta_Credito = $( '#cbo-modal_tarjeta_credito option:selected' ).val();
	$No_Medio_Pago = $( '#cbo-modal_forma_pago option:selected' ).text();
	$No_Tarjeta_Credito = $( '#cbo-modal_tarjeta_credito option:selected' ).text();
	$Ss_Monto = parseFloat($( '.input-modal_monto' ).val());
	$sNumeroOperacion = $( '#tel-nu_referencia' ).val();
	$sUltimosDigitosTarjeta = $( '#tel-nu_ultimo_4_digitos_tarjeta' ).val();
	
	$Nu_Tipo_Medio_Pago = $( '#cbo-modal_forma_pago' ).find(':selected').data('nu_tipo_medio_pago');
	if ( $Nu_Tipo_Medio_Pago != 1 ) {
		$( '.th-label-vuelto' ).show();
		$( '.th-label-saldo' ).hide();
	} else {
		$( '.th-label-vuelto' ).hide();
		$( '.th-label-saldo' ).show();
	}

	$ID_Medio_Pago_Tarjeta_Credito = $ID_Medio_Pago + $ID_Tarjeta_Credito;

	$iVerificarIdMedioPagoGuardado = 0;
	$Sum_Ss_Monto_Total = 0.00;
	$("#table-modal_forma_pago > tbody > tr").each(function(){
		fila = $(this);
		$iVerificarIdMedioPagoGuardado = fila.find(".iIdMedioPago").text();
		$Sum_Ss_Monto_Total += parseFloat(fila.find(".fTotal").text());
	});
	
	if ($Nu_Tipo_Medio_Pago == 0)//Efectivo
		$ID_Tarjeta_Credito = 0;
	
	$( '.help-block' ).empty();
	if (isNaN($Ss_Monto)) {
		$( '.input-modal_monto' ).closest('.form-group').find('.help-block').html('Ingresar monto');
		$( '.input-modal_monto' ).closest('.form-group').removeClass('has-success').addClass('has-error');
	} else if ( ($iVerificarIdMedioPagoGuardado == 1 || $iVerificarIdMedioPagoGuardado == 2 || $iVerificarIdMedioPagoGuardado == 3) && ($ID_Medio_Pago == 4) ) {
		$( '.input-modal_monto' ).closest('.form-group').find('.help-block').html('No se puede mezclar crédito con otras formas de pago');
		$( '.input-modal_monto' ).closest('.form-group').removeClass('has-success').addClass('has-error');
	} else if ($Nu_Tipo_Medio_Pago != 1 && $Ss_Monto <= 0) {
		$( '.input-modal_monto' ).closest('.form-group').find('.help-block').html('Monto debe ser mayor 0');
		$( '.input-modal_monto' ).closest('.form-group').removeClass('has-success').addClass('has-error');
	} else if( isExistElementFormaPago($ID_Medio_Pago_Tarjeta_Credito) ){
		$( '#cbo-modal_forma_pago' ).closest('.form-group').find('.help-block').html('Ya existe medio pago');
		$( '#cbo-modal_forma_pago' ).closest('.form-group').removeClass('has-success').addClass('has-error');
	} else if (
		(
			($Nu_Tipo_Medio_Pago == 2) ||
			($Nu_Tipo_Medio_Pago == 1)
		) && ($Ss_Monto > parseFloat((parseFloat($('.input-total_detalle_productos_pos').val()) - parseFloat($('.input-modal_forma_pago_monto_total').val()))).toFixed(2))
		) {
		$( '.input-modal_monto' ).closest('.form-group').find('.help-block').html('El monto a pagar es menor');
		$( '.input-modal_monto' ).closest('.form-group').removeClass('has-success').addClass('has-error');
	}  else {
		$( '.input-modal_monto' ).closest('.form-group').removeClass('has-error');

		tr_body_modal_forma_pago = "";
		tr_body_modal_forma_pago += 
		"<tr id='tr_forma_pago" + $ID_Medio_Pago_Tarjeta_Credito + "'>"
			+"<td style='display:none;' class='text-left'>" + $ID_Medio_Pago_Tarjeta_Credito + "</td>"
			+"<td style='display:none;' class='text-left iIdMedioPago'>" + $ID_Medio_Pago + "</td>"
			+"<td style='display:none;' class='text-left'>" + $ID_Tarjeta_Credito + "</td>"
			+"<td class='text-left'>" + $No_Medio_Pago + "</td>"
			+"<td class='text-left'>" + $No_Tarjeta_Credito + "</td>"
			+"<td class='text-right fTotal'>" + $Ss_Monto + "</td>"
			+"<td class='text-center'><button type='button' id='btn-delete_forma_pago_pos' class='btn btn-sm btn-link' alt='Eliminar' title='Eliminar'><i class='fa fa-trash-o fa-lg' aria-hidden='true'></i></button></td>"
			+"<td style='display:none;' class='text-left'>" + $sNumeroOperacion + "</td>"
			+"<td style='display:none;' class='text-left'>" + $sUltimosDigitosTarjeta + "</td>"
			+"<td style='display:none;' class='text-left td-iTipoVista'>" + $Nu_Tipo_Medio_Pago + "</td>"
		+"</tr>";

		$( '#div-modal_forma_pago' ).show();
		$( '#table-modal_forma_pago > tbody ' ).append(tr_body_modal_forma_pago);
		
		$Sum_Ss_Monto_Total = 0.00;
		$("#table-modal_forma_pago > tbody > tr").each(function(){
			fila = $(this);
			$Sum_Ss_Monto_Total += parseFloat(fila.find(".fTotal").text());
		});

		$( '.label-modal_forma_pago_monto_total' ).text($Sum_Ss_Monto_Total);
		$( '.input-modal_forma_pago_monto_total' ).val($Sum_Ss_Monto_Total);
		
		$Ss_Monto_Restante_Cobrar = parseFloat(parseFloat($('.input-total_detalle_productos_pos').val()) - $Sum_Ss_Monto_Total).toFixed(2);
		
		$( '.input-modal_monto' ).val('');
		if ($Ss_Monto_Restante_Cobrar > 0.00)
			$( '.input-modal_monto' ).val($Ss_Monto_Restante_Cobrar);
		
		$( '#btn-add_forma_pago' ).prop('disabled', false);
		$( '.btn-generar_pedido' ).prop('disabled', true);
		$Ss_Vuelto_Pos = 0.00;
		$Ss_Vuelto_Pos = $Sum_Ss_Monto_Total - parseFloat($( '.input-total_detalle_productos_pos' ).val());
		
		if ($Ss_Vuelto_Pos >= 0 && $Nu_Tipo_Medio_Pago != 1){//1 = Credito
			$( '.label-vuelto_pos' ).text( $Ss_Vuelto_Pos.toFixed(2) );
			$( '.input-vuelto_pos' ).val( $Ss_Vuelto_Pos.toFixed(2) );
			
			$( '#btn-add_forma_pago' ).prop('disabled', true);
			$('.btn-generar_pedido').prop('disabled', false);
		}

		if ($Ss_Vuelto_Pos < 0 && $Nu_Tipo_Medio_Pago != 1) {//1 = Credito
			$('.input-modal_monto').focus();
			$('.input-modal_monto').select();
		}
		
		if ($Ss_Monto_Restante_Cobrar > 0.00 && $Nu_Tipo_Medio_Pago == 1) {//1 = Credito
			$('.label-saldo_pos_cliente').text($Ss_Monto_Restante_Cobrar);
			$('.input-saldo_pos_cliente').val($Ss_Monto_Restante_Cobrar);
			
			$( '#btn-add_forma_pago' ).prop('disabled', true);
			$('.btn-generar_pedido').prop('disabled', false);
		}
	}
}

function generarComprobante(){
	var arrCliente = Array(), arrCabecera = Array(), arrDetalle = Array(), arrFormaPago = Array(), obj = {}, $ID_Producto, $Qt_Producto, $Ss_Precio, $fDescuentoPorcentajeItem, $fDescuentoSinImpuestosItem, $fDescuentoImpuestosItem, $fSubtotalItem, $fImpuestoItem, $Ss_Total_Producto, $ID_Impuesto_Cruce_Documento, $sNotaItem, $fTotalGlobalDescuento = 0.00, $fIcbperItem, $sNombreItem;

	$("#table-detalle_productos_pos > tbody > tr").each(function(){
		fila = $(this);
		
		$ID_Producto = fila.find(".td-iIdItem").text();
		$Qt_Producto = fila.find(".input-qItem").val();
		$Ss_Precio = parseFloat(fila.find(".input-fPrecioItem").val());
		$fDescuentoPorcentajeItem = fila.find(".td-fDescuentoPorcentajeItem").text();
		$fDescuentoSinImpuestosItem = fila.find(".td-fDescuentoSinImpuestosItem").text();
		$fDescuentoImpuestosItem = fila.find(".td-fDescuentoImpuestosItem").text();
		$fSubtotalItem = fila.find(".td-fSubtotalItem").text();
		$fImpuestoItem = fila.find(".td-fImpuestoItem").text();
		$Ss_Total_Producto = parseFloat(fila.find(".td-Ss_Total_Producto").text());
		$ID_Impuesto_Cruce_Documento = fila.find(".txt-Qt_Producto").data('id_impuesto_cruce_documento');
		$sNotaItem = fila.find(".input-sNotaItem").val();
		$fIcbperItem = fila.find(".input-qItem").data('ss_icbper_item');
		$sNombreItem = fila.find(".td-sNombreItem").text();

		obj = {};
		
		obj.ID_Producto = $ID_Producto;
		obj.Qt_Producto = $Qt_Producto;
		obj.Ss_Precio = $Ss_Precio;
		obj.fDescuentoPorcentajeItem = $fDescuentoPorcentajeItem;
		obj.fDescuentoSinImpuestosItem = $fDescuentoSinImpuestosItem;
		obj.fDescuentoImpuestosItem = $fDescuentoImpuestosItem;
		obj.fSubtotalItem = $fSubtotalItem;
		obj.fImpuestoItem = $fImpuestoItem;
		obj.Ss_Total_Producto = $Ss_Total_Producto;
		obj.ID_Impuesto_Cruce_Documento	= $ID_Impuesto_Cruce_Documento;
		obj.Txt_Nota = $sNotaItem;
		obj.fIcbperItem = $fIcbperItem;
		obj.sNombreItem = $sNombreItem;
		
		arrDetalle.push(obj);

		$fTotalGlobalDescuento += parseFloat($fDescuentoSinImpuestosItem);
	});

	arrCliente = {
		'ID_Tipo_Documento_Identidad': $('#cbo-TiposDocumentoIdentidad').val(),
		'Nu_Documento_Identidad' : $( '#txt-ACodigo' ).val(),
		'No_Entidad' : $( '#txt-ANombre' ).val(),
		'Nu_Estado' : 1,//1=Activo o 0=Inactivo
		'Nu_Celular_Entidad' : $('[name="Nu_Celular_Entidad"]').val(),
		'Txt_Email_Entidad' : $('[name="Txt_Email_Entidad"]').val(),
	};
	
	var iIdTipoDocumento = $( '#cbo-tipo_documento' ).val();

	arrCabecera = {
		'ID_Matricula_Empleado' : $( '#hidden-id_matricula_personal' ).val(),
		'ID_Moneda' : $( '#hidden-id_moneda_caja_pos' ).val(),
		'ID_Tipo_Documento' : iIdTipoDocumento,
		'ID_Entidad' : $( '#txt-AID' ).val(),
		'Ss_Total' : parseFloat($( '.input-total_detalle_productos_pos' ).val()),
		'Ss_Total_Saldo' : parseFloat($( '.input-saldo_pos_cliente' ).val()),
		'ID_Lista_Precio_Cabecera' : $( '#cbo-lista_precios' ).val(),
		'Nu_Tipo_Recepcion' : $( '#cbo-recepcion' ).val(),
		'ID_Transporte_Delivery' : ($( '#cbo-recepcion' ).val() !=6 ? 0 : $( '#cbo-transporte' ).val()),
		'Nu_Transporte_Lavanderia_Hoy' : $( '#cbo-tipo_envio_lavado' ).val(),
		'sDireccionDelivery': $('[name="Txt_Direccion_Delivery"]').val(),
		'Fe_Entrega' : $( '#txt-fe_entrega' ).val(),
		'sGlosa' : $('[name="Txt_Glosa"]').val(),
		'iIdAlmacen' : $( '#cbo-almacen' ).val(),
		'fTotalGlobalDescuento': $fTotalGlobalDescuento,
		'No_Orden_Compra_FE': $('[name="No_Orden_Compra_FE"]').val(),
		'No_Placa_FE': $('[name="No_Placa_FE"]').val(),
		'Txt_Garantia': $('[name="Txt_Garantia"]').val(),
		'Nu_Detraccion': $('[name="radio-addDetraccion"]:checked').attr('value'),
		'iTipoDescuento': $('#cbo-descuento').val(),
		'Ss_Descuento_Total': $('.input-total_descuento').val(),
		'Ss_Descuento_Impuesto': $('.input-total_descuento_sin_impuestos').val(),
		'Ss_Descuento_Total_Input': $('#txt-Ss_Descuento').val(),
		'ID_Canal_Venta_Tabla_Dato': $('#cbo-canal_venta').val(),
		'No_Tipo_Recepcion': $('#cbo-recepcion :selected').text(),
		'Fe_Vencimiento': $('#txt-Fe_Vencimiento').val(),
		'Ss_Vuelto': $('.input-vuelto_pos').val()
	}

	var arrFormaPago = [], $ID_Medio_Pago, $ID_Tarjeta_Credito, $Ss_Monto, $sNumeroOperacion, $sUltimosDigitosTarjeta;
	$("#table-modal_forma_pago > tbody > tr").each(function(){
		fila = $(this);
		
		$ID_Medio_Pago = fila.find("td:eq(1)").text();
		$ID_Tarjeta_Credito = fila.find("td:eq(2)").text();
		$Ss_Monto = fila.find("td:eq(5)").text();
		$sNumeroOperacion = fila.find("td:eq(7)").text();
		$sUltimosDigitosTarjeta = fila.find("td:eq(8)").text();
		$iTipoVista = fila.find(".td-iTipoVista").text();
	
		obj = {};
		
		obj.ID_Medio_Pago = $ID_Medio_Pago;
		obj.ID_Tarjeta_Credito = $ID_Tarjeta_Credito;
		obj.Ss_Total = $Ss_Monto;
		obj.Nu_Transaccion = $sNumeroOperacion;
		obj.Nu_Tarjeta = $sUltimosDigitosTarjeta;
		obj.iTipoVista = $iTipoVista;
		arrFormaPago.push(obj);
	});

	var $url = base_url + 'PuntoVenta/POSLavanderiaController/agregarVentaPos';
	var $arrParamsPost = {
		arrCliente : arrCliente,
		arrCabecera : arrCabecera,
		arrDetalle : arrDetalle,
		arrFormaPago : arrFormaPago,
	};

	$( '#btn-ticket' ).text('');
	$( '#btn-salir' ).attr('disabled', true);
	$( '#btn-ticket' ).attr('disabled', true);
	$( '#btn-ticket' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

	isLoading = true;
	$.post($url, $arrParamsPost, function (response) {

		isLoading = false;
		$( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
		$( '#modal-message' ).modal('show');

		if (response.sStatus == 'success') {
			$('.modal_forma_pago').modal('hide');

			$('.modal-message').addClass('modal-' + response.sStatus);
			$('.modal-title-message').text(response.sMessage);
			setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);

			if ($('[name="radio-addWhatsapp"]:checked').attr('value') == 0) {
				// Mandar a imprimir impresora
				var Accion = 'imprimir', url_print = 'ocultar-img-logo_punto_venta_click';
				formatoImpresionTicket(Accion, response.iIdDocumentoCabecera, url_print);
			} else {
				//Envío por whatsApp
				var sNumeroPeru = $('[name="Nu_Celular_Entidad"]').val().replace(/ /g, "");
				var iTotalRegistros = 0, responseDetalle = '';

				url = 'https://api.whatsapp.com/send?phone=51' + sNumeroPeru + '&text=';
				url += 'Somos *' + (response.arrResponseFE.No_Empresa_Comercial != '' ? caracteresValidosWhatsApp(response.arrResponseFE.No_Empresa_Comercial) : caracteresValidosWhatsApp(response.arrResponseFE.No_Empresa)) + '*,\n';
				
				url += '\n*Cliente:* ' + caracteresValidosWhatsApp($( '#txt-ANombre' ).val());
				url += '\n*' + $( '#cbo-TiposDocumentoIdentidad' ).find(':selected').text() + ':* ' + $( '#txt-ACodigo' ).val();
				url += '\n*Documento:* ' + response.arrResponseFE.Documento;
				url += '\n*Fecha de Emisión:* ' + response.arrResponseFE.Fecha_Emision;

				iTotalRegistros = response.arrResponseFE.arrDetalle.length;
				responseDetalle = response.arrResponseFE.arrDetalle;

				url += '\n\n*Detalle de Pedido*\n';
				url += '=============\n';
				for (var i = 0; i < iTotalRegistros; i++) {
					url += '✅ ' + number_format(responseDetalle[i].Qt_Producto, 2) + ' x *' + caracteresValidosWhatsApp(responseDetalle[i].sNombreItem.trim()) + '* - ' + $('#hidden-no_signo_caja_pos').val() + ' ' + number_format(responseDetalle[i].Ss_Precio, 2) + '\n';
				}
				
				if (response.arrResponseFE.Nu_Tipo_Recepcion != '5') {
					url += '\n*Recepción:* ' + response.arrResponseFE.No_Tipo_Recepcion + '\n';
					if (response.arrResponseFE.Nu_Tipo_Recepcion == 6 && response.arrResponseFE.sDireccionDelivery != '')
						url += '*Dirección:* ' + response.arrResponseFE.sDireccionDelivery + '\n';
				}
				
				url += '\n➡️ *Total:* ' + $('#hidden-no_signo_caja_pos').val() + ' ' + number_format(response.arrResponseFE.Total, 2) + '\n';
				//Saldo
				if (parseFloat($('.input-saldo_pos_cliente').val()) > 0.00) {
					url += '➡️ *Fecha de Vencimiento:* ' + $('#txt-Fe_Vencimiento').val();
					url += '\n➡️ Tiene un *saldo pendiente por pagar de ' + $('#hidden-no_signo_caja_pos').val() + ' ' + number_format($('.input-saldo_pos_cliente').val(), 2) + '*\n';
				}

				if (response.arrResponseFE.enlace_del_pdf !== undefined) {
					url += '\nDescarga tu *PDF electrónico* en el siguiente enlace:\n' + response.arrResponseFE.enlace_del_pdf + '\n';
				} 

				url += (sTerminosCondicionesTicket != '' ? '\n' + sTerminosCondicionesTicket.replace(/<br \/>/g, "") : '\n');
				url += '\n\nGenerado por laesystems.com';
				
				url = encodeURI(url);
				window.open(url, '_blank');
			}

			limpiarValoresVenta(2);
		} else {			
			if (response.sStatus=='danger') {
				limpiarValoresVenta(2);
				
				$( '.modal-message' ).addClass( 'modal-' + response.sStatus );
				$( '.modal-title-message' ).text( response.sMessage );
				setTimeout(function() {$('#modal-message').modal('hide');}, 6500);
			} else {
				$('.modal-message').addClass('modal-' + response.sStatus);
				$('.modal-title-message').text(response.sMessage);
				setTimeout(function () { $('#modal-message').modal('hide'); }, 6500);
			}
		}
		
		$( '#btn-ticket' ).text('');
		$( '#btn-ticket' ).append( 'Generar venta' );
		$( '#btn-ticket' ).attr('disabled', false);
		$( '#btn-salir' ).attr('disabled', false);
	}, 'json')
	.fail(function(jqXHR, textStatus, errorThrown) {
		$( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
		
		$( '#modal-message' ).modal('show');
		$( '.modal-message' ).addClass( 'modal-danger' );
		$( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
		setTimeout(function() {$('#modal-message').modal('hide');}, 6000);
		
		//Message for developer
		console.log(jqXHR.responseText);

		$( '#btn-ticket' ).text('');
		$( '#btn-ticket' ).attr('disabled', false);
		$('#btn-ticket').append( 'Generar venta' );
		$('#btn-salir').attr('disabled', false);

		isLoading = false;
	})
}

// Funciones para LAE API
function api_sunat_reniec(n) {
	var iIdTipoDocumento = $('#cbo-TiposDocumentoIdentidad').val();
	var iCantidadCaracteresIngresados = n.length;
	var iNumeroDocumentoIdentidad = parseFloat($('#txt-ACodigo').val());

	if (iIdTipoDocumento == 2) {//2=DNI
		getDatosxDNI(iIdTipoDocumento, iCantidadCaracteresIngresados, 8, iNumeroDocumentoIdentidad);
	} else if (iIdTipoDocumento == 4) {//4=RUC
		getDatosxRUC(iIdTipoDocumento, iCantidadCaracteresIngresados, 11, iNumeroDocumentoIdentidad);
	}
}

function getDatosxDNI(iIdTipoDocumento, iCantidadCaracteresIngresados, iCantidadCaracteres, iNumeroDocumentoIdentidad){
	if( iCantidadCaracteresIngresados == iCantidadCaracteres && isNaN(iNumeroDocumentoIdentidad) == false && $( '#txt-ACodigo' ).val() != $( '#hidden-nu_numero_documento_identidad' ).val() ) {//Si cumple con los caracteres para DNI / RUC
		var arrPost = {
			sNumeroDocumentoIdentidad : $( '#txt-ACodigo' ).val(),
		};
		$('#span-no_nombres_cargando').html('<i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>');
		
		$( '#txt-ACodigo' ).closest('.form-group').find('.help-block').html( '' );
		$( '#txt-ACodigo' ).closest('.form-group').removeClass('has-error');
		// Consulta Cliente en BD local
		$.post( base_url + 'AutocompleteController/getClienteEspecifico', arrPost, function( response ){
			$('#txt-ANombre').val('');
			$('#label-txt_estado_cliente').text('');

			$('#txt-Nu_Celular_Entidad_Cliente').val( '' );
			$('#txt-Txt_Email_Entidad_Cliente').val( '' );
			$('#span-celular').hide();
			$('#span-email').hide();

			if ( response.sStatus=='success' ) {
				$('#span-no_nombres_cargando').html('');
				$('#hidden-nu_numero_documento_identidad').val( $( '#txt-ACodigo' ).val() );

				var arrData = response.arrData;
				
				$('[name="AID"]').val( arrData[0].ID );
				$('[name="No_Entidad"]').val( arrData[0].Nombre );
				$('[name="Txt_Direccion_Entidad"]').val(arrData[0].Txt_Direccion_Entidad);
				$('[name="Txt_Direccion_Delivery"]').val(arrData[0].Txt_Direccion_Entidad);

				$('#txt-ANombre').val(arrData[0].Nombre);
				$('#label-txt_direccion').text(arrData[0].Txt_Direccion_Entidad);
				$('#label-txt_estado_cliente').text('Existe en B.D. local');
				
				/*
				$('#hidden-estado_entidad').val(arrData[0].Nu_Estado);

				$('#cbo-Estado-modal').html('');
				for (var i = 0; i < 2; i++) {
					selected = '';
					if (arrData[0].Nu_Estado == i)
						selected = 'selected="selected"';
					$('#cbo-Estado-modal').append('<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Inactivo' : 'Activo') + '</option>');
				}
				*/

				$( '#txt-ACodigo' ).closest('.form-group').find('.help-block').html( '' );
				$( '#txt-ACodigo' ).closest('.form-group').removeClass('has-error');
			} else if ( response.sStatus=='warning' ) {// Si no existe en nuestra BD local, consultamos en el LAE API V1
				// Consulta LAE API V1 - RENIEC / SUNAT
				$( '#txt-AID' ).val('');
				$( '#txt-ANombre' ).val('');
				$('#txt-Txt_Direccion_Entidad').val('');
				$('[name="Txt_Direccion_Delivery"]').val('');

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
							$('[name="Txt_Direccion_Delivery"]').val(response.data.Txt_Address);
							$('#label-txt_direccion').text(response.data.Txt_Address);

							$('#label-txt_estado_cliente').text('Nube');

							/*
							$('#hidden-estado_entidad').val(response.data.Nu_Status);

							$('#cbo-Estado-modal').html('');
							for (var i = 0; i < 2; i++) {
								selected = '';
								if (response.data.Nu_Status == i)
									selected = 'selected="selected"';
								$('#cbo-Estado-modal').append('<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Inactivo' : 'Activo') + '</option>');
							}
							*/
						} else {
							$('[name="No_Entidad"]').val('');
							$('[name="Txt_Direccion_Entidad"]').val('');
							$('[name="Txt_Direccion_Delivery"]').val('');

							$('#txt-ACodigo').closest('.form-group').find('.help-block').html(response.msg);
							$('#txt-ACodigo').closest('.form-group').removeClass('has-success').addClass('has-error');

							$('#txt-ACodigo').focus();
							$('#txt-ACodigo').select();

							$('#txt-ANombre').val('');
							$('#label-txt_direccion').text('');
							$('#label-txt_estado_cliente').text('');

							/*
							$('#hidden-estado_entidad').val(0);

							$('#cbo-Estado-modal').html('');
							for (var i = 0; i < 2; i++) {
								selected = '';
								if (0 == i)
									selected = 'selected="selected"';
								$('#cbo-Estado-modal').append('<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Inactivo' : 'Activo') + '</option>');
							}
							*/
						}
					},
					error: function (response) {
						$('#hidden-nu_numero_documento_identidad').val($('#txt-ACodigo').val());

						$('#txt-ACodigo').closest('.form-group').find('.help-block').html('Sin acceso');
						$('#txt-ACodigo').closest('.form-group').removeClass('has-success').addClass('has-error');

						$('[name="No_Entidad"]').val('');
						$('[name="Txt_Direccion_Entidad"]').val('');

						$('#span-no_nombres_cargando').html('');
						$('#txt-ANombre').val('');
						$('#label-txt_direccion').text('');
						$('#label-txt_estado_cliente').text('');

						/*
						$('#hidden-estado_entidad').val(0);

						$('#cbo-Estado-modal').html('');
						for (var i = 0; i < 2; i++) {
							selected = '';
							if (0 == i)
								selected = 'selected="selected"';
							$('#cbo-Estado-modal').append('<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Inactivo' : 'Activo') + '</option>');
						}
						*/
					}
				});
				// ./ Consulta LAE API V1 - RENIEC / SUNAT
			} else {
				$('#label-txt_direccion').text(response.sMessage);
			}
		}, 'JSON')
		// ./ Consulta de Cliente en BD local
	} else {
		$('#hidden-estado_entidad').val(1);

		$('#txt-ACodigo').closest('.form-group').find('.help-block').html('');
		$('#txt-ACodigo').closest('.form-group').removeClass('has-error');
	}
}

function getDatosxRUC(iIdTipoDocumento, iCantidadCaracteresIngresados, iCantidadCaracteres, iNumeroDocumentoIdentidad){
	if( iCantidadCaracteresIngresados == iCantidadCaracteres && isNaN(iNumeroDocumentoIdentidad) == false && $( '#txt-ACodigo' ).val() != $( '#hidden-nu_numero_documento_identidad' ).val() ) {//Si cumple con los caracteres para DNI / RUC
		var arrPost = {
			sNumeroDocumentoIdentidad : $( '#txt-ACodigo' ).val(),
		};
		$('#span-no_nombres_cargando').html('<i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>');

		$( '#txt-ACodigo' ).closest('.form-group').find('.help-block').html( '' );
		$( '#txt-ACodigo' ).closest('.form-group').removeClass('has-error');
		// Consulta Cliente en BD local
		$.post( base_url + 'AutocompleteController/getClienteEspecifico', arrPost, function( response ){
			$('#txt-ANombre').val('');
			$('#label-txt_estado_cliente').text('');

			$('#txt-Nu_Celular_Entidad_Cliente').val( '' );
			$('#txt-Txt_Email_Entidad_Cliente').val( '' );
			$('#span-celular').hide();
			$('#span-email').hide();

			if ( response.sStatus=='success' ) {
				$('#span-no_nombres_cargando').html('');
				$('#hidden-nu_numero_documento_identidad').val( $( '#txt-ACodigo' ).val() );

				var arrData = response.arrData;
				
				$('[name="AID"]').val( arrData[0].ID );
				$('[name="No_Entidad"]').val( arrData[0].Nombre );


				$('#txt-ANombre').val(arrData[0].Nombre);
				$('[name="Txt_Direccion_Entidad"]').val(response.data.Txt_Address);
				$('[name="Txt_Direccion_Delivery"]').val(response.data.Txt_Address);
				$('#label-txt_direccion').text(arrData[0].Txt_Direccion_Entidad);
				$('#label-txt_estado_cliente').text('Existe en B.D. local');
				
				$('#hidden-estado_entidad').val(arrData[0].Nu_Estado);

				$('#cbo-Estado-modal').html('');
				for (var i = 0; i < 2; i++) {
					selected = '';
					if (arrData[0].Nu_Estado == i)
						selected = 'selected="selected"';
					$('#cbo-Estado-modal').append('<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Inactivo' : 'Activo') + '</option>');
				}

				$( '#txt-ACodigo' ).closest('.form-group').find('.help-block').html( '' );
				$( '#txt-ACodigo' ).closest('.form-group').removeClass('has-error');
			} else if ( response.sStatus=='warning' ) {// Si no existe en nuestra BD local, consultamos en el LAE API V1
				// Consulta LAE API V1 - RENIEC / SUNAT
				$( '#txt-AID' ).val('');
				$( '#txt-ANombre' ).val('');
				$('#txt-Txt_Direccion_Entidad').val('');
				$('[name="Txt_Direccion_Delivery"]').val('');
				
				var url_api = 'https://www.laesystems.com/librerias/sunat/partner/format/json/x-api-key/';
				url_api = url_api + sTokenGlobal;
				var data = {
					ID_Tipo_Documento_Identidad : 4,
					Nu_Documento_Identidad : $( '#txt-ACodigo' ).val(),
				};		
				$.ajax({
					url   : url_api,
					type  : 'POST',
					data  : data,
					success: function(response){
						$('#span-no_nombres_cargando').html('');
						$('#hidden-nu_numero_documento_identidad').val( $( '#txt-ACodigo' ).val() );

						if (response.success == true){
							$( '#txt-ACodigo' ).closest('.form-group').find('.help-block').html( '' );
							$( '#txt-ACodigo' ).closest('.form-group').removeClass('has-error');

							$('[name="No_Entidad"]').val( response.data.No_Names );
							$('#txt-ANombre').val(response.data.No_Names);

							$('[name="Txt_Direccion_Entidad"]').val(response.data.Txt_Address);
							$('[name="Txt_Direccion_Delivery"]').val(response.data.Txt_Address);
							$('#label-txt_direccion').text(response.data.Txt_Address);
							
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
							$('[name="No_Entidad"]').val( '' );
							$('[name="Txt_Direccion_Entidad"]').val( '' );
			
							$( '#txt-ACodigo' ).closest('.form-group').find('.help-block').html( response.msg );
							$('#txt-ACodigo').closest('.form-group').removeClass('has-success').addClass('has-error');

							$('#modal-message').modal('show');
							$('.modal-message').removeClass('modal-danger modal-warning modal-success');
							$('.modal-message').addClass('modal-danger');
							$('.modal-title-message').text('El cliente se encuentra con BAJA DE OFICIO / NO HABIDO');
							setTimeout(function () { $('#modal-message').modal('hide'); }, 2500);
							
							$( '#txt-ACodigo' ).focus();
							$( '#txt-ACodigo' ).select();
		
							$('#txt-ANombre').val('');
							$('#label-txt_direccion').text('');
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
					error: function(response){
						$('#hidden-nu_numero_documento_identidad').val( $( '#txt-ACodigo' ).val() );

						$( '#txt-ACodigo' ).closest('.form-group').find('.help-block').html( 'Sin acceso' );
						$( '#txt-ACodigo' ).closest('.form-group').removeClass('has-success').addClass('has-error');
						
						$( '[name="No_Entidad"]' ).val( '' );
						$( '[name="Txt_Direccion_Entidad"]' ).val( '' );
		
						$('#span-no_nombres_cargando').html('');
						$('#txt-ANombre').val('');
						$('#label-txt_direccion').text('');
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
				$( '#txt-ACodigo' ).closest('.form-group').find('.help-block').html( response.sMessage );
				$( '#txt-ACodigo' ).closest('.form-group').removeClass('has-success').addClass('has-error');
			}
		}, 'JSON')
		// ./ Consulta de Cliente en BD local
	}
}

function limpiarValoresVenta(iTipoEnvio) {
	if (iTipoEnvio == 2) {
		limpiarValoresVentaClic();
	} else {
		$('.modal-message-delete').modal('show');
		$('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
		$('.modal-message-delete').addClass('modal-warning');

		$('.modal-title-message-delete').text('¿Estás seguro de cancelar la venta?');

		$('#btn-save-delete').off('click').click(function () {
			limpiarValoresVentaClic();
		});
	}
}

function limpiarValoresVentaClic() {
	$('.modal-message-delete').modal('hide');

	$('#label_correo').show();
	$('#txt-Txt_Email_Entidad_Cliente').show();
	$('#form-modal_venta_pos_forma_pago')[0].reset();

	$('#table-items_alternativos tbody').empty();
	$('#table-detalle_productos_pos tbody').empty();
	$('#table-modal_forma_pago tbody').empty();

	// Limpiar alertas
	$('.div-col-alerta').remove();

	$('#cbo-lista_precios').val($('#cbo-lista_precios').val());
	//$('#cbo-lista_precios').val('0');
	//$('#cbo-lista_precios').select().trigger('change');
	$('#cbo-canal_venta').val('0');
	$('#cbo-canal_venta').select().trigger('change');

	// Limpiar cliente
	// Set selected
	$('.div-nuevo_cliente').show();//tipo de documento y numero de documento
	$('#cbo-tipo_documento').val('2');// Tipo comprobante

	$('#cbo-TiposDocumentoIdentidad').val(2);

	$('#hidden-nu_numero_documento_identidad').val('');
	$('#hidden-estado_entidad').val('');
	
	$('#txt-AID').val('');
	$('#txt-Txt_Direccion_Entidad').val('');
	$('#txt-Txt_Direccion_Entidad-modal').val('');
	$('#txt-ANombre').val('');
	$('#txt-ACodigo').val('');
	$('#span-no_nombres_cargando').html('');
	$('#txt-Nu_Celular_Entidad_Cliente').val('');
	$('#txt-Txt_Email_Entidad_Cliente').val('');
	$('#span-celular').hide();
	$('#span-email').hide();

	$('#label-tipo_documento_identidad').text('DNI');
	$('#cbo-TiposDocumentoIdentidad').val(2);//DNI

	// Tipo envio y recepcion
	$('#cbo-transporte').val('0');//Aqui se malogro pendiente cuando hay un personal
	$('#cbo-transporte').select().trigger('change');
	$('[name="Txt_Direccion_Delivery"]').val('');
	$('#cbo-tipo_envio_lavado').val('1');

	$('#cbo-recepcion').val('5');
	$('#radio-InactiveDetraccion').prop('checked', true).iCheck('update');
	$('#radio-ActiveDetraccion').prop('checked', false).iCheck('update');

	$('#radio-InactiveWhatsapp').prop('checked', true).iCheck('update');
	$('#radio-ActiveWhatsapp').prop('checked', false).iCheck('update');

	// Limpiar modal forma de pago
	$('.modal_forma_pago').modal('hide');
	$('#div-modal_forma_pago').hide();
	$('#cbo-modal_forma_pago').select().trigger('change');
	$('#txt-Fe_Vencimiento').val(fDay + '/' + fMonth + '/' + fYear);
	$('.div-modal_credito').hide();

	$('.input-modal_monto').closest('.form-group').removeClass('has-error');

	$('.label-modal_forma_pago_monto_total').text('0.00');
	$('.input-modal_forma_pago_monto_total').val(0.00);

	$('.label-total_detalle_productos_pos').text($('#hidden-no_signo_caja_pos').val() + ' 0.00');
	$('.input-total_detalle_productos_pos').val(0.00);

	$('.label-total_descuento').text($('#hidden-no_signo_caja_pos').val() + ' 0.00');
	$('.input-total_descuento').val(0.00);

	$('.label-total_descuento_sin_impuestos').text($('#hidden-no_signo_caja_pos').val() + ' 0.00');
	$('.input-total_descuento_sin_impuestos').val(0.00);

	$('#txt-Ss_Descuento').val('');
	$('.span-descuento_total_tipo').text('importe');

	$('.label-vuelto_pos').text('0.00');
	$('.input-vuelto_pos').val(0.00);

	$('.label-saldo_pos_cliente').text('0.00');
	$('.input-saldo_pos_cliente').val(0.00);

	$('#cbo-descuento').val('1');
	$('#cbo-descuento').attr('disabled', false);
	$('#btn-pagar').attr('disabled', true);
	$('#cbo-tipo_envio_lavado').prop('disabled', true);
	$('#txt-fe_entrega').prop('disabled', true);
	$('#cbo-recepcion').prop('disabled', true);
	$('#btn-add_forma_pago').attr('disabled', false);

	$('[name="Txt_Glosa"]').val('');
	$('[name="No_Orden_Compra_FE"]').val('');
	$('[name="No_Placa_FE"]').val('');
	$('[name="Txt_Garantia"]').val('');

	setTimeout(function () { $('#txt-No_Producto').focus(); }, 20);
	$('#txt-ID_Producto').val('');
	$('#txt-No_Producto').val('');
	
	if (screen.width > 991) {
		setTimeout(function () { $('#txt-No_Producto').focus(); }, 20);
	}

	$(window).resize(function () {
		if ($(window).height() > 991) {
			setTimeout(function () { $('#txt-No_Producto').focus(); }, 20);
		}
	})
}

function validacionAlertas(arrParams){
	// Validaciones de alertas
	$('.div-col-alerta').remove();

	// alerta stock mínimo
	var arrParamsAlertas = {
		'sTipoAlerta' : 'Stock_Minimo',
		'iIdItem' : arrParams.iIdItem,
		'fCantidadItem' : arrParams.qItem,
	};
	$.post( base_url + 'HelperController/validacionAlertaItem', arrParamsAlertas, function( arrResponse ){
		if (arrResponse.sStatus == 'success') {
			var sHtmlAlertaStockMinimo = '';
			sHtmlAlertaStockMinimo += '<div id="div-col-alerta-stock_minimo" class="div-col-alerta alert alert-danger col-xs-12" style="padding: 5px; margin-bottom: 5px;">' + arrResponse.sMessage + ' ' + arrResponse.arrData[0].Nu_Stock_Minimo + ' vs Stock Actual ' + arrParams.qItem + '</div>';
			$( '#div-row-alertas' ).append( sHtmlAlertaStockMinimo );
		} else {
			console.log(arrResponse.sMessage);
		}
	}, 'JSON')// ./ alerta stock mínimo
}

function calcularImportexItem(fila){
	fila = fila;
	cantidad = parseFloat(fila.find(".txt-Qt_Producto").val());
	fImpuestoTributario = parseFloat(fila.find(".txt-Qt_Producto").data('ss_impuesto'));
	precio = parseFloat(fila.find(".input-fPrecioItem").val());

	fTotalItem = (precio * cantidad);
	fDescuentoItem = parseFloat(fila.find(".input-fDescuentoItem").val());
	fila.find(".td-fDescuentoPorcentajeItem").text( ($( '#cbo-descuento' ).val() == 1 ? 0 : fDescuentoItem) );

	fDescuentoItem = ($( '#cbo-descuento' ).val() == 1 ? fDescuentoItem : (fDescuentoItem * (fTotalItem / 100)));
	if ( fTotalItem > fDescuentoItem )
		fila.find(".td-Ss_Total_Producto").text( (fTotalItem - fDescuentoItem).toFixed(2) );
	else if (isNaN(fDescuentoItem))
		fila.find(".td-Ss_Total_Producto").text( fTotalItem.toFixed(2) );
	else if ( fDescuentoItem > fTotalItem ) {
		alert( 'El descuento es mayor que el total' );
		fila.find(".input-fDescuentoItem").focus();
	}

	fTotalItem = parseFloat(fila.find(".td-Ss_Total_Producto").text());
	fSubtotalItem = (fTotalItem / fImpuestoTributario).toFixed(6);
	fila.find(".td-fDescuentoSinImpuestosItem").text( parseFloat(fDescuentoItem / fImpuestoTributario).toFixed(2) );
	fila.find(".td-fDescuentoImpuestosItem").text( parseFloat(fDescuentoItem - (parseFloat(fDescuentoItem / fImpuestoTributario))).toFixed(6) );
	fila.find(".td-fSubtotalItem").text( fSubtotalItem );
	fila.find(".td-fImpuestoItem").text( (fila.find(".td-Ss_Total_Producto").text() - fila.find(".td-fSubtotalItem").text()).toFixed(6) );

	calcularTotales();
}

function calcularTotales() {
	var $fDescuentoTotal = parseFloat($('#txt-Ss_Descuento').val());
	var $fDescuentoTotal_Tipo = 0;
	$fDescuentoTotal_Tipo = (!isNaN($fDescuentoTotal) ? $fDescuentoTotal : 0);
	if ($('#cbo-descuento').val() == 2)
		$fDescuentoTotal_Tipo = (!isNaN($fDescuentoTotal) ? ($fDescuentoTotal / 100) : 0);
	$fDescuentoTotal_Tipo = parseFloat($fDescuentoTotal_Tipo);

	$fGravadaTotal = 0.00;
	$fExoneradaTotal = 0.00;
	$fInafectaTotal = 0.00;
	$fGratuitaTotal = 0.00;
	$fTotal = 0.00;
	$fDescuentoTotalItem = 0.00;
	$fDescuentoGlobal = 0.00;
	$fTotalItem = 0.00;

	//operacion
	$fDescuentoGlobalOperacion = 0.00;
	$fDescuentoGlobalSinImpuestos = 0.00;
	$fDescuentoGlobalIGV = 0.00;

	$bStatus = true;
	$( '#table-detalle_productos_pos > tbody > tr' ).each(function(){
		fila = $(this);
		fImpuestoTributario = parseFloat(fila.find(".txt-Qt_Producto").data('ss_impuesto'));
		iGrupoImpuestoTributario = fila.find(".txt-Qt_Producto").data('nu_tipo_impuesto');
		$fTotalItem = parseFloat(fila.find(".td-Ss_Total_Producto").text());

		if ($fDescuentoTotal_Tipo > 0) {
			if ($('#cbo-descuento').val() == 1) {
				$fDescuentoGlobalOperacion = ($fDescuentoTotal_Tipo / $('#table-detalle_productos_pos > tbody > tr').length);
				$fDescuentoGlobalSinImpuestos = ($fDescuentoGlobalOperacion / fImpuestoTributario);
				$fDescuentoGlobal += $fDescuentoGlobalSinImpuestos;
				$fDescuentoGlobalIGV += ($fDescuentoGlobalOperacion - $fDescuentoGlobalSinImpuestos);

				$fTotalItem = $fTotalItem - ($fDescuentoTotal_Tipo / $('#table-detalle_productos_pos > tbody > tr').length);
			} else if ($('#cbo-descuento').val() == 2) {
				$fDescuentoGlobalOperacion = ($fTotalItem * $fDescuentoTotal_Tipo);
				$fDescuentoGlobalSinImpuestos = ($fDescuentoGlobalOperacion / fImpuestoTributario);
				$fDescuentoGlobal += $fDescuentoGlobalSinImpuestos;
				$fDescuentoGlobalIGV += ($fDescuentoGlobalOperacion - $fDescuentoGlobalSinImpuestos);

				$fTotalItem = ($fTotalItem - ($fTotalItem * $fDescuentoTotal_Tipo));
			}
		}

		fSubtotalItem = ($fTotalItem / fImpuestoTributario).toFixed(6);
		fImpuestoItem = ($fTotalItem - fSubtotalItem);

		if (iGrupoImpuestoTributario == 1) {
			$fGravadaTotal += parseFloat(fSubtotalItem);
		} else if (iGrupoImpuestoTributario == 2) {
			$fExoneradaTotal += $fTotalItem;
		} else if (iGrupoImpuestoTributario == 3) {
			$fInafectaTotal += $fTotalItem;
		} else if (iGrupoImpuestoTributario == 4) {
			$fGratuitaTotal += $fTotalItem;
		}

		$fTotal += (parseFloat(fSubtotalItem) + parseFloat(fImpuestoItem));
		$fDescuentoTotalItem += (isNaN(parseFloat(fila.find(".input-fDescuentoItem").val())) ? 0 : parseFloat(fila.find(".input-fDescuentoItem").val()));
	});
	
	$( '.hidden-gravada' ).val($fGravadaTotal.toFixed(2));
	$( '.hidden-exonerada' ).val($fExoneradaTotal.toFixed(2));
	$( '.hidden-inafecta' ).val($fInafectaTotal.toFixed(2));
	$( '.hidden-gratuita' ).val($fGratuitaTotal.toFixed(2));

	//meno de 0.5 es a favor del cliente y mas de 0.5 a favor de la tienda
	var $fGranTotal = $fTotal.toFixed(2);
	if ($('#hidden-Nu_Activar_Redondeo').val() == 1)
		$fGranTotal = Math.round10($fGranTotal, -1);

	$('.input-total_detalle_productos_pos').val($fGranTotal);
	$('.label-total_detalle_productos_pos').text($('#hidden-no_signo_caja_pos').val() + ' ' + $fGranTotal);

	$('.input-total_descuento').val($fDescuentoGlobal.toFixed(2));
	$('.label-total_descuento').text($('#hidden-no_signo_caja_pos').val() + ' ' + $fDescuentoGlobal.toFixed(2));
	$('.input-total_descuento_sin_impuestos').val($fDescuentoGlobalIGV.toFixed(2));
	$('.label-total_descuento_sin_impuestos').text($('#hidden-no_signo_caja_pos').val() + ' ' + $fDescuentoGlobalIGV.toFixed(2));
	
	$( '#cbo-descuento' ).attr('disabled', false);
	if ( $fDescuentoTotalItem > 0 )
		$( '#cbo-descuento' ).attr('disabled', true);
	
	$( '#btn-pagar' ).prop('disabled', true);
	$( '#cbo-tipo_envio_lavado' ).prop('disabled', true);
	$( '#txt-fe_entrega' ).prop('disabled', true);
	$( '#cbo-recepcion' ).prop('disabled', true);
	if ($fGranTotal > 0.00) {
		$( '#btn-pagar' ).prop('disabled', false);
		$( '#cbo-tipo_envio_lavado' ).prop('disabled', false);
		$( '#txt-fe_entrega' ).prop('disabled', false);
		$( '#cbo-recepcion' ).prop('disabled', false);				
	}
}

function crearItemModal(){
	$( '.help-block' ).empty();
	
	var iIDTipoExistenciaProducto = $( '#cbo-modal-tipoItem' ).val();
	if ( $( '#cbo-modal-grupoItem' ).val() == '0' || $( '#cbo-modal-grupoItem' ).val() == '2' )//Servicio o interno
		iIDTipoExistenciaProducto = 4;//Otros
	
	var arrProducto = Array();
	arrProducto = {
		'EID_Empresa'               : '',
		'EID_Producto'              : '',
		'ENu_Codigo_Barra': '',
		'ENo_Codigo_Interno': '',
		'Nu_Tipo_Producto'          : $( '#cbo-modal-grupoItem' ).val(),
		'ID_Tipo_Producto'          : iIDTipoExistenciaProducto,
		'ID_Ubicacion_Inventario'   : 1,
		'Nu_Codigo_Barra'           : $( '#txt-modal-upcItem' ).val(),
		'No_Producto'               : $( '[name="textarea-modal-nombreItem"]' ).val(),
		'No_Codigo_Interno'         : '',
		'ID_Impuesto'               : $( '#cbo-modal-impuestoItem' ).val(),
		'ID_Unidad_Medida'          : $( '#cbo-modal-unidad_medidaItem' ).val(),
		'ID_Marca': '',
		'ID_Familia': $('#cbo-modal-categoria').val(),
		'Nu_Favorito': $('#cbo-modal-favorito').val(),
		'ID_Sub_Familia'            : '',
		'Nu_Compuesto'              : 0,
		'Nu_Estado'                 : 1,
		'Txt_Producto'              : '',
		'ID_Producto_Sunat'         : $( '#hidden-ID_Tabla_Dato' ).val(),
		'Nu_Stock_Minimo': '0',
		'Nu_Stock_Maximo': '0',
		'Nu_Receta_Medica'          : '',
		'ID_Laboratorio'            : '',
		'Nu_Lote_Vencimiento'       : '',
		'Txt_Ubicacion_Producto_Tienda' : '',
		'Ss_Precio' : 0,
		'Ss_Costo' : 0,
		'ID_Impuesto_Icbper' : 0,
		'Qt_CO2_Producto' : 0,
		'ID_Tipo_Pedido_Lavado' : 0,
        'Ss_Precio' : $( '#txt-modal-precioItem' ).val(),
        'Ss_Costo' : $( '#txt-modal-costoItem' ).val(),
		'No_Imagen_Item': '',
		'Ss_Precio_Ecommerce_Online_Regular': 0,
		'Ss_Precio_Ecommerce_Online': 0,
		'ID_Familia_Marketplace': 0,
		'ID_Sub_Familia_Marketplace': 0,
		'ID_Marca_Marketplace': 0,
	};
		
	$( '#btn-modal-salir' ).attr('disabled', true);
	
	$( '#btn-modal-crear_item' ).text('');
	$( '#btn-modal-crear_item' ).attr('disabled', true);
	$( '#btn-modal-crear_item' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

	$( '#modal-loader' ).modal('show');
	$( '#modal-loader' ).css("z-index", "3000");  

	url = base_url + 'Logistica/ReglasLogistica/ProductoController/crudProducto';
	$.ajax({
		type : 'POST',
		dataType : 'JSON',
		url	: url,
		data : {
			arrProducto : arrProducto,
			arrProductoEnlace : null,
		},
		success : function( response ){    		  
			$( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
			$( '#modal-message' ).modal('show');
			$( '.modal-message' ).css("z-index", "3000");
			if (response.status == 'success'){
				$( '.modal-message' ).addClass(response.style_modal);
				$( '.modal-title-message' ).text(response.message);
				setTimeout(function() {$('#modal-message').modal('hide');}, 1200);

				$('.modal-crear_item').modal('hide');

				var arrParams = {
					sUrl: 'HelperController/getItems',
					ID_Almacen: $('#cbo-almacen').val(),
					iIdListaPrecio: $('#cbo-lista_precios').val(),
					ID_Linea: 'favorito',
				}
				getItems(arrParams);
			} else {
				$( '.modal-message' ).addClass(response.style_modal);
				$( '.modal-title-message' ).text(response.message);
				setTimeout(function() {$('#modal-message').modal('hide');}, 1200);
			}// /. if - else crear item modal
			$( '#modal-loader' ).modal('hide');

			$( '#btn-modal-salir' ).attr('disabled', false);
				
			$( '#btn-modal-crear_item' ).text('');
			$( '#btn-modal-crear_item' ).append( '<span class="fa fa-save"></span> Guardar' );
			$( '#btn-modal-crear_item' ).attr('disabled', false);
		},
		error: function (jqXHR, textStatus, errorThrown) {
			$( '#modal-loader' ).modal('hide');
			$( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
			
			$( '#modal-message' ).modal('show');
			$( '.modal-message' ).addClass( 'modal-danger' );
			$( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
			setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
			
			//Message for developer
			console.log(jqXHR.responseText);
			
			$( '#btn-modal-salir' ).attr('disabled', false);
				
			$( '#btn-modal-crear_item' ).text('');
			$( '#btn-modal-crear_item' ).append( '<span class="fa fa-save"></span> Guardar' );
			$( '#btn-modal-crear_item' ).attr('disabled', false);
		}
	});
}

function calcularIcbper() {
	var $fTotalIcbper = 0.00;
	$("#table-detalle_productos_pos > tbody > tr").each(function () {
		var rows = $(this);
		var fCantidad = parseFloat(rows.find(".input-qItem").val());
		var fIcbper = parseFloat(rows.find(".input-qItem").data('ss_icbper'));
		var iIdIcbper = rows.find(".input-qItem").data('id_impuesto_icbper');
		if (iIdIcbper == 1) {
			var fCalculoIcbperItem = (fCantidad * fIcbper);
			rows.find(".input-qItem").data('ss_icbper_item', fCalculoIcbperItem);
			rows.find(".input-qItem").attr('data-ss_icbper_item', fCalculoIcbperItem);
			$fTotalIcbper += fCalculoIcbperItem;
		}
	});

	$('#hidden-total_icbper').val($fTotalIcbper.toFixed(2));

	var $Ss_Total = parseFloat($('.input-total_detalle_productos_pos').val());
	var $fSumTotal = ($Ss_Total + $fTotalIcbper).toFixed(2);

	if ($('#hidden-Nu_Activar_Redondeo').val() == 1)
		$fSumTotal = Math.round10($fSumTotal, -1);

	$('.input-total_detalle_productos_pos').val($fSumTotal);
	$('.label-total_detalle_productos_pos').text($('#hidden-no_signo_caja_pos').val() + ' ' + $fSumTotal);
}

function validarDescuentoTotalPorImpuestoTributario() {
	var iTipoDescuentoOtrosImpuestos = 0;
	if ($('#cbo-tipo_documento').val() != 2) {
		$('#table-detalle_productos_pos > tbody > tr').each(function () {
			fila = $(this);
			iGrupoImpuestoTributario = fila.find(".txt-Qt_Producto").data('nu_tipo_impuesto');
			if (iGrupoImpuestoTributario != 1 && parseFloat($('#txt-Ss_Descuento').val()) > 0.00) {
				alert('No se puede brindar descuento TOTAL solo por ítem. Si es IGV si se puede por TOTAL o ÍTEM.');
				iTipoDescuentoOtrosImpuestos = 1;
				$('#txt-Ss_Descuento').val('');
				return false;
			}
		});
	}
	return iTipoDescuentoOtrosImpuestos;
}