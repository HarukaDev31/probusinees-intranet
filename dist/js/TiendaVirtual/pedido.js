var url;

function importarExcelPedidos(){
  $( ".modal_importar_excel_pedidos" ).modal( "show" );
}

$(function () {
  
  $('.div-transportadora').show();
  $('[name="radio-forma_pago"]').on('ifChecked', function () {
    $('.div-transportadora').show();
    if ($('[name="radio-forma_pago"]:checked').attr('value') == 2)
      $('.div-transportadora').hide();
  });

  $('#textarea-ver_novedades').summernote({
    placeholder: 'Opcional',
    toolbar: [],
    tabsize: 4,
    height: 300
  });

  $('#textarea-descripcion_item').summernote({
    placeholder: 'Opcional',
    toolbar: [
      ['style', ['style']],
      ['style', ['bold', 'italic', 'underline', 'clear']],
      ['fontname', ['fontname']],
      ['fontsize', ['fontsize']],
      ['color', ['color']],
    ],
    tabsize: 4,
    height: 100
  });
  
  $( '#btn-cancelar_pedido' ).click(function() {
    $( '.div-AgregarEditar' ).hide();
    $( '.div-Listar' ).show();

    console.log($( '#txt-EID_Pedido_Cabecera' ).val());
    if ( !isNaN(parseInt($( '#txt-EID_Pedido_Cabecera' ).val())) && parseInt($( '#txt-EID_Pedido_Cabecera' ).val()) > 0 ) {
      console.log('entro');
      $('html, body').animate({
        scrollTop: $('tr[data-id="' + $('#txt-EID_Pedido_Cabecera').val() + '"]').offset().top
      },'slow');
    }
  })

  // IMPORTACION DE EXCEL
	$(document).on('click', '.back-history', function () {
		window.location = base_url + 'TiendaVirtual/PedidosTiendaVirtualController/listar';
	})

  // Validate exist file excel ajuste_inventario
	$( document ).on('click', '#btn-excel-importar_excel_pedidos', function(event) {
	  if ( $( "#my-file-selector-pedidos" ).val().length === 0 ) {
      $( '#my-file-selector-pedidos' ).closest('.form-group').find('.help-block').html('Seleccionar archivo');
		  $( '#my-file-selector-pedidos' ).closest('.form-group').removeClass('has-success').addClass('has-error');
	  } else {
      $( '#btn-cancel-pedidos' ).attr('disabled', true);
      $( '#a-download-pedidos' ).attr('disabled', true);
	    
      $( '#btn-excel-importar_excel_pedidos' ).text('');
      $( '#btn-excel-importar_excel_pedidos' ).attr('disabled', true);
      $( '#btn-excel-importar_excel_pedidos' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
  
      $( '#modal-loader' ).modal('show');
	  }
  })

  $( '#btn-save-excel-pedido_manual' ).click(function (e) {
    e.preventDefault();

    if ( isNaN(parseInt($( '#hidden-registros_validos' ).val())) || parseInt($( '#hidden-registros_validos' ).val()) <= 0 ) {
      alert('No hay registros validos para procesar');
    } else {
      var $modal_delete = $( '.modal-message-delete' );
      $modal_delete.modal('show');
      
      $( '.modal-message-delete' ).removeClass('modal-danger modal-warning modal-success');
      $( '.modal-message-delete' ).addClass('modal-success');
      
      $( '.modal-title-message-delete' ).text('¿Deseas guardar pedidos manuales?');
      
      $( '#btn-cancel-delete' ).off('click').click(function () {
        $modal_delete.modal('hide');
      });
        
      $( '#btn-save-delete' ).off('click').click(function () {
        $modal_delete.modal('hide');

        $( '#btn-save-excel-pedido_manual' ).text('');
        $( '#btn-save-excel-pedido_manual' ).attr('disabled', true);
        $( '#btn-save-excel-pedido_manual' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
        $( '#a-salir-excel-pedido_manual' ).attr('disabled', true);

        url = base_url + 'TiendaVirtual/PedidosTiendaVirtualController/guardarPedidosManualExcel';
        $.ajax({
          type : 'POST',
          dataType : 'JSON',
          url : url,
          data : $('#form-PedidoExcel').serialize(),
          success : function( response ){
            $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
            $( '#modal-message' ).modal('show');
            if ( response.status=='success' ) {
              $( '.modal-save-excel-pedido_manual' ).modal('hide');

              $( '.modal-message' ).addClass( 'modal-' + response.status);
              $( '.modal-title-message' ).text( response.message );
              setTimeout(function() {
                $('#modal-message').modal('hide');

                window.location = base_url + 'TiendaVirtual/PedidosTiendaVirtualController/listar';
              }, 2500);
            } else {
              $( '.modal-message' ).addClass( 'modal-' + response.status );
              $( '.modal-title-message' ).text( response.message );
              setTimeout(function() {$('#modal-message').modal('hide');}, 6100);
            }
            
            $( '#btn-save-excel-pedido_manual' ).text('');
            $( '#btn-save-excel-pedido_manual' ).append( 'Guardar' );
            $( '#btn-save-excel-pedido_manual' ).attr('disabled', false);
            $( '#a-salir-excel-pedido_manual' ).attr('disabled', false);
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

          $( '#btn-save-excel-pedido_manual' ).text('');
          $( '#btn-save-excel-pedido_manual' ).attr('disabled', false);
          $( '#btn-save-excel-pedido_manual' ).append( 'Guardar' );
          $( '#a-salir-excel-pedido_manual' ).attr('disabled', false);
        })
      
      });
    }
  })
  // ./ IMPORTACION DE EXCEL

  //Global Autocomplete
  $( '.autocompletar_dropshipping' ).autoComplete({
    minChars: 0,
    source: function (term, response) {
      term = term.trim();
      if (term.length > 2) {
        var term                = term.toLowerCase();
        var global_class_method = $( '.autocompletar_dropshipping' ).data('global-class_method');
        
        var filter_id_codigo = '';
        if ($( '#txt-EID_Producto' ).val() !== undefined)
          filter_id_codigo = $( '#txt-EID_Producto' ).val();
        
        $.post( base_url + global_class_method, { global_search : term, filter_id_codigo : filter_id_codigo }, function( arrData ){
          response(arrData);
        }, 'JSON');
      }
    },
    renderItem: function (item, search){
      search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
      var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");
      return '<div title="' + caracteresValidosAutocomplete(item.Nombre) + '" class="autocomplete-suggestion" data-precio_empresa_proveedor="' + item.Ss_Precio_Proveedor_Dropshipping + '" data-name_almacen_proveedor="' + item.No_Almacen + '" data-name_empresa_proveedor="' + item.No_Empresa + '" data-id_empresa_proveedor="' + item.ID_Empresa + '" data-id_almacen_proveedor="' + item.ID_Almacen + '" data-id_impuesto_detalle="' + item.ID_Impuesto_Cruce_Documento + '" data-id="' + item.ID + '" data-codigo="' + item.Codigo + '" data-nombre="' + caracteresValidosAutocomplete(item.Nombre) + '" data-val="' + search + '"><span class="hidden-xs"><strong>Producto: </strong></span> ' + caracteresValidosAutocomplete(item.Nombre).replace(re, "<b>$1</b>") + ' <br><strong>Stock: </strong>' + item.Qt_Producto + ' <br><strong>Proveedor: </strong>' + item.No_Empresa + ' | <span class="hidden-sm hidden-md hidden-lg"><br></span><strong>Almacen: </strong>' + item.No_Almacen + ' <br><strong>Precio Proveedor: </strong>' + item.Ss_Precio_Proveedor_Dropshipping + ' | <span class="hidden-sm hidden-md hidden-lg"><br></span><strong>Precio Sugerido: </strong>' + item.Ss_Precio_Vendedor_Dropshipping + '<div style="border-bottom-style: ridge; border-bottom-color: black; border-bottom-width: 1px"></div></div>';
    },
    onSelect: function(e, term, item){
      $( '#txt-AID_Producto' ).val(item.data('id'));
      $( '#txt-ACodigo' ).val(item.data('codigo'));
      $( '#txt-AID_Impuesto_Cruce_Documento' ).val(item.data('id_impuesto_detalle'));
      $( '#txt-ANombre' ).val(item.data('nombre'));
      
      $( '#hidden-precio_empresa_proveedor' ).val(item.data('precio_empresa_proveedor'));

      $( '#hidden-id_empresa_proveedor' ).val(item.data('id_empresa_proveedor'));
      $( '#hidden-id_almacen_proveedor' ).val(item.data('id_almacen_proveedor'));
      
      $( '#hidden-name_empresa_proveedor' ).val(item.data('name_empresa_proveedor'));
      $( '#hidden-name_almacen_proveedor' ).val(item.data('name_almacen_proveedor'));
    }
  });

  $( '#div-pedido_productos' ).hide();
  $( '#table-pedido_productos' ).hide();

	$( '#btn-addProductosEnlaces' ).click(function(){
	  var $ID_Producto        = $( '#txt-AID_Producto' ).val();
    var $No_Producto_Enlace = $( '#txt-ANombre' ).val();
    var $Qt_Producto_Enlace = $( '#txt-Qt_Producto_Descargar' ).val();
    var $ID_Impuesto_Cruce_Documento = $( '#txt-AID_Impuesto_Cruce_Documento' ).val();
    var $Precio_Pedido = $( '#txt-Precio_Pedido' ).val();
    var $Total_Pedido = $( '#txt-Total_Pedido' ).val();
    var $id_empresa_proveedor = $( '#hidden-id_empresa_proveedor' ).val();
    var $id_almacen_proveedor = $( '#hidden-id_almacen_proveedor' ).val();
    var $No_Empresa = $( '#hidden-name_empresa_proveedor' ).val();
    var $No_Almacen = $( '#hidden-name_almacen_proveedor' ).val();
    var $precio_empresa_proveedor = $( '#hidden-precio_empresa_proveedor' ).val();
  
    $Precio_Pedido = (parseFloat($Total_Pedido) / parseFloat($Qt_Producto_Enlace));
    if ( $ID_Producto.length === 0 || $No_Producto_Enlace.length === 0) {
	    $( '#txt-ANombre' ).closest('.form-group').find('.help-block').html('Ingresar producto');
			$( '#txt-ANombre' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $Qt_Producto_Enlace.length === 0) {
      $( '#txt-Qt_Producto_Descargar' ).closest('.form-group').find('.help-block').html('Ingresar Cantidad');
		  $( '#txt-Qt_Producto_Descargar' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $Qt_Producto_Enlace == 0) {
      $( '#txt-Qt_Producto_Descargar' ).closest('.form-group').find('.help-block').html('Cantidad debe ser mayor 0');
		  $( '#txt-Qt_Producto_Descargar' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $Total_Pedido.length === 0) {
      $( '#txt-Total_Pedido' ).closest('.form-group').find('.help-block').html('Ingresar Total');
		  $( '#txt-Total_Pedido' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $Total_Pedido == 0) {
      $( '#txt-Total_Pedido' ).closest('.form-group').find('.help-block').html('Total debe ser mayor 0');
		  $( '#txt-Total_Pedido' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $Precio_Pedido.length === 0) {
      $( '#txt-Precio_Pedido' ).closest('.form-group').find('.help-block').html('Ingresar Precio');
		  $( '#txt-Precio_Pedido' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $Precio_Pedido == 0) {
      $( '#txt-Precio_Pedido' ).closest('.form-group').find('.help-block').html('Precio debe ser mayor 0');
		  $( '#txt-Precio_Pedido' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      var table_enlace_producto =
      "<tr id='tr_enlace_producto" + $ID_Producto + "'>"
        + "<td class='text-left text-id_producto' style='display:none;'>" + $ID_Producto + "</td>"
        + "<td class='text-left text-id_impuesto' style='display:none;'>" + $ID_Impuesto_Cruce_Documento + "</td>"
        + "<td class='text-left text-id_empresa_proveedor' style='display:none;'>" + $id_empresa_proveedor + "</td>"
        + "<td class='text-left text-id_almacen_proveedor' style='display:none;'>" + $id_almacen_proveedor + "</td>"
        + "<td class='text-left'>" + $No_Empresa + "</td>"
        + "<td class='text-left'>" + $No_Almacen + "</td>"
        + "<td class='text-left'>" + $No_Producto_Enlace + "</td>"
        + "<td class='text-right text-cantidad'>" + $Qt_Producto_Enlace + "</td>"
        + "<td class='text-right text-precio'>" + $Precio_Pedido + "</td>"
        + "<td class='text-right text-total'>" + $Total_Pedido + "</td>"
        + "<td class='text-right text-precio_empresa_proveedor'>" + $precio_empresa_proveedor + "</td>"
        + "<td class='text-right text-total_empresa_proveedor'>" + (parseFloat($precio_empresa_proveedor) * parseFloat($Qt_Producto_Enlace)) + "</td>"
        + "<td class='text-center'><button type='button' id='btn-deleteProductoEnlace' class='btn btn-xs btn-link' alt='Quitar' title='Quitar'><i class='fa fa-trash-o fa-2x' aria-hidden='true'></i></button></td>"
      + "</tr>";
      
	    if( isExistTableTemporalEnlacesProducto($ID_Producto) ){
  	    $( '#txt-ANombre' ).closest('.form-group').find('.help-block').html('Ya registró producto: <strong>' + $No_Producto_Enlace + '</strong>');
  			$( '#txt-ANombre' ).closest('.form-group').removeClass('has-success').addClass('has-error');
  			$( '#txt-AID_Producto' ).val('');
  			$( '#txt-ACodigo' ).val('');
  			$( '#txt-ANombre' ).val('');
  			$( '#txt-AID_Impuesto_Cruce_Documento' ).val('');
        $( '#txt-Precio_Pedido' ).val('');
        //$( '#txt-Qt_Producto_Descargar' ).val('');
        //$( '#txt-Total_Pedido' ).val('');
  			
  			$( '#txt-ANombre' ).focus();
	    } else {
        $( '#div-pedido_productos' ).show();
        $( '#table-pedido_productos' ).show();
			  $( '#table-pedido_productos' ).append(table_enlace_producto);
			  $( '#txt-AID_Producto' ).val('');
			  $( '#txt-ACodigo' ).val('');
  			$( '#txt-ANombre' ).val('');
        $( '#txt-AID_Impuesto_Cruce_Documento' ).val('');
        $( '#txt-Precio_Pedido' ).val('');
        $( '#txt-Qt_Producto_Descargar' ).val('1');
        $( '#txt-Total_Pedido' ).val('');
  			
  			$( '#txt-ANombre' ).focus();
	    }
    }
	})
	
	$( '#table-pedido_productos tbody' ).on('click', '#btn-deleteProductoEnlace', function(){
    $(this).closest ('tr').remove ();
    if ($( '#table-pedido_productos >tbody >tr' ).length == 0){
      $( '#div-pedido_productos' ).hide();
	    $( '#table-pedido_productos' ).hide();
    }
	})

  //AGREGAR PEDIDO MANUAL
  $( "#form-agregar_pedido" ).validate({
		rules:{
			No_Entidad_Order_Address_Entry: {
				required: true,
				maxlength: 100
			},
			Nu_Celular_Entidad_Order_Address_Entry: {
				required: true
			},
			No_Ciudad_Dropshipping: {
				required: true,
				maxlength: 100
			},
			Txt_Direccion_Entidad_Order_Address_Entry: {
				required: true,
				maxlength: 255
			},
			Fe_Entrega: {
				required: true,
				maxlength: 100
			},
		},
		messages:{
			No_Entidad_Order_Address_Entry:{
				required: "Ingresar Nombre Completo",
				maxlength: "Máximo 100 dígitos"
			},
			Nu_Celular_Entidad_Order_Address_Entry:{
				required: "Ingresar 15 dígitos máximo"
			},
			No_Ciudad_Dropshipping:{
				required: "Ingresar Ciudad",
				maxlength: "Máximo 100 dígitos"
			},
			Txt_Direccion_Entidad_Order_Address_Entry:{
				required: "Ingresar Dirección",
				maxlength: "Máximo 255 dígitos"
			},
			Fe_Entrega:{
				required: "Ingresar Fecha"
			},
		},
		errorPlacement : function(error, element) {
			$(element).closest('.form-group').find('.help-block').html(error.html());
    },
		highlight : function(element) {
			$(element).closest('.form-group').removeClass('has-success').addClass('has-error');
	  },
	  unhighlight: function(element, errorClass, validClass) {
			$(element).closest('.form-group').removeClass('has-error').addClass('has-success');
			$(element).closest('.form-group').find('.help-block').html('');
	  },
		submitHandler: form_AgregarPedido
	});

  //FILTRO POR EMPRESA MIS PEDIDOS
  url = base_url + 'HelperController/getEmpresas';
  var selected = '';
  $.post(url, function (response) {
    $('#cbo-filtro_empresa').html('<option value="0" selected="selected">- TODOS -</option>');
    for (var i = 0; i < response.length; i++) {
      //selected = '';
      //if ($('#header-a-id_empresa').val() == response[i].ID_Empresa)
        //selected = 'selected="selected"';
      $('#cbo-filtro_empresa').append('<option value="' + response[i].ID_Empresa + '" ' + selected + '>' + response[i].No_Empresa + '</option>');
    }
  }, 'JSON');

  $( '#div-ventas_detalladas_generales' ).hide();

  $( '.btn-generar_ventas_detalladas_generales' ).click(function(){
    var Fe_Inicio, Fe_Fin, ID_Tipo_Documento, ID_Pedido_Cabecera, Nu_Estado_Pedido, iIdCliente, sNombreCliente, iIdItem, sNombreItem, iTipoVenta, ID_Filtro_Empresa, Nu_Estado_Pedido_Empresa, sNombreCiudad;
    
    Fe_Inicio = ParseDateString($( '#txt-Filtro_Fe_Inicio' ).val(), 1, '/');
    Fe_Fin = ParseDateString($( '#txt-Filtro_Fe_Fin' ).val(), 1, '/');
    ID_Tipo_Documento = $( '#cbo-filtros_tipos_documento' ).val();
    ID_Pedido_Cabecera = ($( '#txt-Filtro_NumeroDocumento' ).val().length == 0 ? '-' : $( '#txt-Filtro_NumeroDocumento' ).val());
    Nu_Estado_Pedido = $( '#cbo-estado_pedido' ).val();
    iIdCliente = ($( '#txt-AID' ).val().length === 0 ? '-' : $( '#txt-AID' ).val());
    sNombreCliente = ($( '#txt-Filtro_Entidad' ).val().length === 0 ? '-' : $( '#txt-Filtro_Entidad' ).val());
    ID_Filtro_Empresa = $( '#cbo-filtro_empresa' ).val();
    Nu_Estado_Pedido_Empresa = $( '#cbo-estado_pedido_empresa' ).val();
    sNombreCiudad = ($( '#txt-Filtro_Ciudad_Manual' ).val().length === 0 ? '-' : $( '#txt-Filtro_Ciudad_Manual' ).val());

    if ($(this).data('type') == 'html') {
      getReporteHTML();
    } else if ($(this).data('type') == 'pdf') {
      $( '#btn-pdf_ventas_detalladas_generales' ).text('');
      $( '#btn-pdf_ventas_detalladas_generales' ).attr('disabled', true);
      $( '#btn-pdf_ventas_detalladas_generales' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
          
      url = base_url + 'TiendaVirtual/PedidosTiendaVirtualController/sendReportePDF/' + Fe_Inicio + '/' + Fe_Fin + '/' + ID_Tipo_Documento + '/' + ID_Pedido_Cabecera + '/' + Nu_Estado_Pedido + '/' + iIdCliente + '/' + encodeURIComponent(sNombreCliente) + '/' + ID_Filtro_Empresa + '/' + Nu_Estado_Pedido_Empresa + '/' + encodeURIComponent(sNombreCiudad);
      window.open(url,'_blank');
      
      $( '#btn-pdf_ventas_detalladas_generales' ).text('');
      $( '#btn-pdf_ventas_detalladas_generales' ).append( '<i class="fa fa-file-pdf-o color_icon_pdf"></i> PDF' );
      $( '#btn-pdf_ventas_detalladas_generales' ).attr('disabled', false);
    } else if ($(this).data('type') == 'excel') {
      $( '#btn-excel_ventas_detalladas_generales' ).text('');
      $( '#btn-excel_ventas_detalladas_generales' ).attr('disabled', true);
      $( '#btn-excel_ventas_detalladas_generales' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
      
      url = base_url + 'TiendaVirtual/PedidosTiendaVirtualController/sendReporteEXCEL/' + Fe_Inicio + '/' + Fe_Fin + '/' + ID_Tipo_Documento + '/' + ID_Pedido_Cabecera + '/' + Nu_Estado_Pedido + '/' + iIdCliente + '/' + encodeURIComponent(sNombreCliente) + '/' + ID_Filtro_Empresa + '/' + Nu_Estado_Pedido_Empresa +  '/' + encodeURIComponent(sNombreCiudad);
      window.open(url,'_blank');
      
      $( '#btn-excel_ventas_detalladas_generales' ).text('');
      $( '#btn-excel_ventas_detalladas_generales' ).append( '<i class="fa fa-file-excel-o color_icon_excel"></i> Reporte Excel' );
      $( '#btn-excel_ventas_detalladas_generales' ).attr('disabled', false);
    }// /. if
  })// /. btn


  // GENERAR VENTA
  $('#btn-modal-generar_venta-send').click(function () {
    if (parseFloat($('[name="fTotalDocumento"]').val()) < 0.10) {
      alert('El total no puede ser menor a 0.10 céntimos');
      return false;
    } if ($('[name="ID_Tipo_Documento"]').val() == 3 && $('[name="ID_Tipo_Documento_Identidad"]').val() == 2) {
      alert('No se puede FACTURAR a DNI');
    } else if ($('[name="ID_Tipo_Documento"]').val() == 3 && $('[name="ID_Tipo_Documento_Identidad"]').val() == 4 && $('[name="Nu_Documento_Identidad"]').val().length < 11) {
      alert('Ingresar 11 dígitos para RUC');
    } else {
      $('.help-block').empty();

      $('#btn-modal-generar_venta-send').text('');
      $('#btn-modal-generar_venta-send').attr('disabled', true);
      $('#btn-modal-generar_venta-send').append('Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>');
      $('#btn-modal-generar_venta-cancel').attr('disabled', true);

      url = base_url + 'TiendaVirtual/PedidosTiendaVirtualController/generarVenta';
      $.ajax({
        type: 'POST',
        dataType: 'JSON',
        url: url,
        data: $('#form-generar_venta').serialize(),
        success: function (response) {
          $('.modal-message').removeClass('modal-danger modal-warning modal-success');
          $('#modal-message').modal('show');

          if (response.sStatus == 'success') {
            $('.modal-generar_venta').modal('hide');

            $('.modal-message').addClass('modal-' + response.sStatus);
            $('.modal-title-message').text(response.sMessage);
            setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
          } else {
            $('.modal-message').addClass('modal-' + response.sStatus);
            $('.modal-title-message').text(response.sMessage);
            setTimeout(function () { $('#modal-message').modal('hide'); }, 5100);
          }

          getReporteHTML();

          $('#btn-modal-generar_venta-send').text('');
          $('#btn-modal-generar_venta-send').append('Generar Venta');
          $('#btn-modal-generar_venta-send').attr('disabled', false);
          $('#btn-modal-generar_venta-cancel').attr('disabled', false);
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

          $('#btn-modal-generar_venta-send').text('');
          $('#btn-modal-generar_venta-send').attr('disabled', false);
          $('#btn-modal-generar_venta-send').append('Generar Venta');
          $('#btn-modal-generar_venta-cancel').attr('disabled', false);
        })
    }
  })
})

function getReporteHTML() {
  var Fe_Inicio, Fe_Fin, ID_Tipo_Documento, ID_Pedido_Cabecera, Nu_Estado_Pedido, iIdCliente, sNombreCliente, iIdItem, sNombreItem, iTipoVenta, ID_Filtro_Empresa, Nu_Estado_Pedido_Empresa, sNombreCiudad;

  Fe_Inicio = ParseDateString($('#txt-Filtro_Fe_Inicio').val(), 1, '/');
  Fe_Fin = ParseDateString($('#txt-Filtro_Fe_Fin').val(), 1, '/');
  ID_Tipo_Documento = $('#cbo-filtros_tipos_documento').val();
  ID_Pedido_Cabecera = ($('#txt-Filtro_NumeroDocumento').val().length == 0 ? '-' : $('#txt-Filtro_NumeroDocumento').val());
  Nu_Estado_Pedido = $('#cbo-estado_pedido').val();
  iIdCliente = ($('#txt-AID').val().length === 0 ? '-' : $('#txt-AID').val());
  sNombreCliente = ($('#txt-Filtro_Entidad').val().length === 0 ? '-' : $('#txt-Filtro_Entidad').val());
  ID_Filtro_Empresa = $( '#cbo-filtro_empresa' ).val();
  Nu_Estado_Pedido_Empresa = $( '#cbo-estado_pedido_empresa' ).val();
  sNombreCiudad = ($( '#txt-Filtro_Ciudad_Manual' ).val().length === 0 ? '-' : $( '#txt-Filtro_Ciudad_Manual' ).val());

  $('#btn-html_ventas_detalladas_generales').text('');
  $('#btn-html_ventas_detalladas_generales').attr('disabled', true);
  $('#btn-html_ventas_detalladas_generales').append('Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>');

  $('#table-ventas_detalladas_generales > tbody').empty();
  $('#table-ventas_detalladas_generales > tfoot').empty();

  var arrPost = {
    Fe_Inicio: Fe_Inicio,
    Fe_Fin: Fe_Fin,
    ID_Tipo_Documento: ID_Tipo_Documento,
    ID_Pedido_Cabecera: ID_Pedido_Cabecera,
    Nu_Estado_Pedido: Nu_Estado_Pedido,
    iIdCliente: iIdCliente,
    sNombreCliente: sNombreCliente,
    ID_Filtro_Empresa:ID_Filtro_Empresa,
    Nu_Estado_Pedido_Empresa: Nu_Estado_Pedido_Empresa,
    sNombreCiudad: sNombreCiudad
  };
  url = base_url + 'TiendaVirtual/PedidosTiendaVirtualController/sendReporte';
  $.post(url, arrPost, function (response) {
    if (response.sStatus == 'success') {
      var iTotalRegistros = response.arrData.length, response = response.arrData, tr_body = '', tr_foot = '', fTotalItem = 0.00, fTotalGeneral = 0.00, sClassStatusPedidoEmpresa='', fGanancia=0, fGananciaTotal=0;
      for (var i = 0; i < iTotalRegistros; i++) {
        fTotal = (!isNaN(parseFloat(response[i].Ss_Total)) ? parseFloat(response[i].Ss_Total) : 0);
        fGanancia = (!isNaN(parseFloat(response[i].ganancia)) ? parseFloat(response[i].ganancia) : 0);

        if(response[i].sEstadoPedidoEmpresa == 'Pendiente'){
          sClassStatusPedidoEmpresa='warning';
        } else if(response[i].sEstadoPedidoEmpresa == 'Completado'){
          sClassStatusPedidoEmpresa='success';
        } else if(response[i].sEstadoPedidoEmpresa == 'Pago realizado'){
          sClassStatusPedidoEmpresa='primary';
        } else {
          sClassStatusPedidoEmpresa='info';
        }

        tr_body +=
        "<tr data-id='"+response[i].ID_Pedido_Cabecera+"'>"
          +"<td class='text-center'>" + response[i].sTipoVenta + "</td>"
          +"<td class='text-center'>" + response[i].Fe_Emision_Hora + "</td>"
          +"<td class='text-center'>" + response[i].ID_Pedido_Cabecera + "</td>"
          +"<td class='text-left'>" + response[i].No_Entidad + "</td>"
          +"<td class='text-left'>" + response[i].No_Ciudad_Dropshipping + "</td>"
          +"<td class='text-right'>" + $('#hidden-No_Signo_Global').val() + ' ' + number_format(fTotal, 2) + "</td>"
          //+"<td class='text-center'>" + response[i].Fe_Entrega + "</td>"//quite el 07/09/2023
          +"<td class='text-center'>" + response[i].Nu_Forma_Pago_Dropshipping + "</td>"
          +"<td class='text-center'>" + response[i].Nu_Servicio_Transportadora_Dropshipping + "</td>"
          //+"<td class='text-center'><span class='label label-" + sClassStatusPedidoEmpresa + "'>" + response[i].sEstadoPedidoEmpresa + "</span></td>"//quite el 07/09/2023
          //+"<td class='text-center'><span class='label label-" + response[i].No_Class_Estado_Recepcion + "'>" + response[i].No_Estado_Recepcion + "</span></td>"
          +"<td class='text-center'>" + response[i].No_Estado_Pedido + "</td>"
          //+"<td class='text-center'>" + response[i].sAccionVer + "</td>"
          +"<td class='text-center'>" + response[i].sAccionEditar + "</td>"
          +"<td class='text-left'>" + response[i].Txt_Glosa + "</td>"
          //+"<td class='text-center'>" + response[i].sVerNovedades + "</td>"
          +"<td class='text-center'>" + $('#hidden-No_Signo_Global').val() + ' ' + response[i].Ss_Precio_Delivery + "</td>"
          +"<td class='text-right'>" + $('#hidden-No_Signo_Global').val() + ' ' + response[i].Ss_Precio_CallCenter + "</td>"
          +"<td class='text-right'>" + response[i].Ss_Precio_Dropshipping + "</td>"
          +"<td class='text-center'>" + response[i].sGenerarTicket + "</td>"
          +"<td class='text-center'>" + response[i].btn_generar_guia_99 + "</td>"
          +"<td class='text-center'>" + $('#hidden-No_Signo_Global').val() + ' ' + fGanancia + "</td>"
          +"<td class='text-center'>" + response[i].sAccionEliminar + "</td>"
        +"</tr>";

        fTotalGeneral += fTotal;
        fGananciaTotal += fGanancia;
      }

      tr_foot =
        "<tfoot>"
        + "<tr>"
        + "<th class='text-right' colspan='5'>Total</th>"
        + "<th class='text-right'>" + number_format(fTotalGeneral, 2) + "</th>"
        + "<th class='text-right' colspan='11'></th>"
        + "<th class='text-right'>" + Math.round10(fGananciaTotal, -2) + "</th>"
        + "</tr>"
        + "</tfoot>";
    } else {
      if (response.sMessageSQL !== undefined) {
        console.log(response.sMessageSQL);
      }
      tr_body +=
        "<tr>"
        + "<td colspan='15' class='text-center'>" + response.sMessage + "</td>"
        + "</tr>";
    } // ./ if arrData

    $('#div-ventas_detalladas_generales').show();
    $('#table-ventas_detalladas_generales > tbody').append(tr_body);
    $('#table-ventas_detalladas_generales > tbody').after(tr_foot);

    $('#btn-html_ventas_detalladas_generales').text('');
    $('#btn-html_ventas_detalladas_generales').append('<i class="fa fa-search"></i> Buscar');
    $('#btn-html_ventas_detalladas_generales').attr('disabled', false);
  }, 'JSON')
  .fail(function (jqXHR, textStatus, errorThrown) {
    $('.modal-message').removeClass('modal-danger modal-warning modal-success');

    $('#modal-message').modal('show');
    $('.modal-message').addClass('modal-danger');
    $('.modal-title-message').text(textStatus + ' [' + jqXHR.status + ']: ' + errorThrown);
    setTimeout(function () { $('#modal-message').modal('hide'); }, 1700);

    //Message for developer
    console.log(jqXHR.responseText);

    $('#btn-html_ventas_detalladas_generales').text('');
    $('#btn-html_ventas_detalladas_generales').append('<i class="fa fa-search"></i> Buscar');
    $('#btn-html_ventas_detalladas_generales').attr('disabled', false);
  });
}

function verPedido(iIdPedido){
  var $modal_detalle = $( '.modal_detalle' );
  $modal_detalle.modal('show');

  $( '#modal-title_detalle' ).text('Nro. Pedido ' + iIdPedido);
  
  $( '#btn-salir' ).off('click').click(function () {
    $modal_detalle.modal('hide');
  });
  
  $( '#modal-loader' ).modal('show');
  url = base_url + 'TiendaVirtual/PedidosTiendaVirtualController/verPedido/' + iIdPedido;
  $.getJSON( url, function( response ) {
    $( '#table-modal_detalle thead' ).empty();
	  $( '#table-modal_detalle tbody' ).empty();
	  $( '#table-modal_detalle tfoot' ).empty();
    if ( response.sStatus =='success' ) {
      response = response.arrData;

      var table = "", sTipoDocumentoIdentidadCliente="", sClassEstadoRecepcion="";

      sClassEstadoRecepcion = (response[0].Nu_Tipo_Recepcion == 6 ? 'danger' : 'success');

      sTipoDocumentoIdentidadCliente = 'OTROS';
      if (response[0].Nu_Documento_Identidad.length == 8)
      sTipoDocumentoIdentidadCliente = 'DNI';
      else if (response[0].Nu_Documento_Identidad.length == 12)
        sTipoDocumentoIdentidadCliente = 'CARNET EXTRANJERIA';

      if (response[0].ID_Tipo_Documento==3 && response[0].Nu_Documento_Identidad.length == 11)
        sTipoDocumentoIdentidadCliente = 'RUC';
      
      table +=
      "<thead>"
        +"<tr>"
          +"<th class='text-left'>FECHA</th>"
          +"<th class='text-left'>DOCUMENTO</th>"
          +"<th class='text-left'>PEDIDO</th>"
          +"<th class='text-left'>F. PAGO</th>"
          +"<th class='text-left'>RECEPCIÓN</th>"
        +"</tr>"
        +"<tr>"
          +"<td class='text-left'>" + ParseDateHour(response[0].Fe_Emision_Hora) + "</td>"
          +"<td class='text-left'>" + response[0].No_Tipo_Documento_Breve + "</td>"
          +"<td class='text-left'>" + response[0].ID_Pedido_Cabecera + "</td>"
          +"<td class='text-left'>" + response[0].No_Medio_Pago_Tienda_Virtual + "</td>"
          +"<td class='text-left'><span class='label label-" + sClassEstadoRecepcion + "'>" + response[0].No_Estado_Recepcion + "</span></td>"
        +"</tr>"
        +"<tr>"
          +"<th class='text-left'>CLIENTE</th>"
          +"<th class='text-left'>Tipo Doc. Iden.</th>"
          +"<th class='text-left' colspan='2'>NÚMERO DOC IDEN.</th>"
          +"<th class='text-center'>CELULAR</th>"
        +"</tr>"
        +"<tr>"
          +"<td class='text-left'>" + response[0].No_Entidad + "</td>"
          +"<td class='text-left'>" + sTipoDocumentoIdentidadCliente + "</td>"
          +"<td class='text-left' colspan='2'>" + response[0].Nu_Documento_Identidad + "</td>"
          +"<td class='text-left' style='font-weight: normal; background: #FFFFFF;'>" + (response[0].Nu_Celular_Referencia != '' ? response[0].Nu_Celular_Referencia : response[0].Nu_Celular_Entidad) + "</td>"
        +"</tr>"
        +"<tr>"
          +"<th class='text-left'>CORREO</th>"
          +"<th class='text-left' colspan='4' style='font-weight: normal; background: #FFFFFF;'>" + response[0].Txt_Email_Entidad + "</th>"
        +"</tr>";
      if (response[0].Nu_Tipo_Recepcion == 6) {//Delivery
        table +=
        +"<tr>"
          +"<th class='text-left'>DEPARTAMENTO - PROVINCIA - DISTRITO</th>"
          +"<th class='text-left' colspan='4' style='font-weight: normal; background: #FFFFFF;'>" + response[0].No_Departamento + ' - ' + response[0].No_Provincia + ' - ' + response[0].No_Distrito + "</th>"
        +"</tr>"
        +"<tr>"
          +"<th class='text-left'>DIRECCIÓN</th>"
          +"<th class='text-left' colspan='4' style='font-weight: normal; background: #FFFFFF;'>" + response[0].Txt_Direccion + "</th>"
        +"</tr>"
        +"<tr>"
          +"<th class='text-left'>REFERENCIA DIRECCIÓN</th>"
          +"<th class='text-left' colspan='4' style='font-weight: normal; background: #FFFFFF;'>" + response[0].Txt_Direccion_Referencia + "</th>"
        +"</tr>";
      }
      table +=
        +"<tr>"
          +"<th class='text-left' colspan='2'>PRODUCTO</th>"
          +"<th class='text-center'>CANTIDAD</th>"
          +"<th class='text-center'>PRECIO</th>"
          +"<th class='text-center'>TOTAL</th>"
        +"</tr>"
      +"</thead>";
      
      table += "<tbody>";
        for (var i = 0, len = response.length; i < len; i++) {
          table +=
          +"<tr>"
            +"<td class='text-left' colspan='2'>" + response[i].No_Producto + "</td>"
            +"<td class='text-right'>" + number_format(response[i].Qt_Producto, 3) + "</td>"
            +"<td class='text-right'>" + number_format(response[i].Ss_Precio, 2) + "</td>"
            +"<td class='text-right'>" + number_format(response[i].Ss_SubTotal, 2) + "</td>"
          +"</tr>";
        }
      table += "</tbody>";
      
      table +=
      "<tfoot>";

      if (response[0].Nu_Tipo_Recepcion == 6) {//Delivery
        table +=
        "<tr>"
          +"<th colspan='4' class='text-right'>DELIVERY</th>"
          +"<th class='text-right'>" + number_format(response[0].Ss_Precio_Delivery, 2) + "</th>"
        +"</tr>";
      }

      if (parseFloat(response[0].Ss_Descuento) > 0.00) {//Cupon
        table +=
        "<tr>"
          +"<th colspan='3' class='text-right'>CUPON DE DESCUENTO:</th>"
          +"<th class='text-left'>" + response[0].No_Codigo_Cupon_Descuento + "</th>"
          +"<th class='text-right'>" + number_format(response[0].Ss_Descuento, 2) + "</th>"
        +"</tr>";
      }

      table +=
        "<tr>"
          +"<th colspan='4' class='text-right'>TOTAL A PAGAR</th>"
          +"<th class='text-right'>" + number_format(response[0].Ss_Total, 2) + "</th>"
        + "</tr>";

      if (response[0].Txt_Glosa != null && response[0].Txt_Glosa != '') {
        table +=
        "<tr>"
          + "<th colspan='4' class='text-left' class='text-right'>GLOSA</th>"
        + "</tr>"
        +"<tr>"
        + "<td colspan='4' class='text-left' class='text-right'>" + response[0].Txt_Glosa + "</td>"
        + "</tr>";
      }

      table+="</tfoot>";
      
      $( '#table-modal_detalle ' ).append(table);
    } else {
      alert('Problemas');
    }
    $( '#modal-loader' ).modal('hide');
  }, 'JSON');
}

function cambiarEstado(ID, Nu_Estado) {
  var $modal_delete = $('#modal-message-delete');
  $modal_delete.modal('show');

  $('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
  $('.modal-message-delete').addClass('modal-success');

  $('.modal-title-message-delete').text('¿Deseas cambiar estado del pedido Nro. ' + ID + '?');

  $('#btn-cancel-delete').off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $('#btn-save-delete').off('click').click(function () {
    $('#modal-loader').modal('show');

    url = base_url + 'TiendaVirtual/PedidosTiendaVirtualController/cambiarEstado/' + ID + '/' + Nu_Estado;
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

function eliminarPedido(ID) {
  var $modal_delete = $('#modal-message-delete');
  $modal_delete.modal('show');

  $('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
  $('.modal-message-delete').addClass('modal-success');

  $('.modal-title-message-delete').text('¿Deseas eliminar el pedido Nro. ' + ID + '?');

  $('#btn-cancel-delete').off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $('#btn-save-delete').off('click').click(function () {
    $('#modal-loader').modal('show');

    url = base_url + 'TiendaVirtual/PedidosTiendaVirtualController/eliminarPedido/' + ID;
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

function generarVenta(ID_Pedido_Cabecera){
  $('#modal-generar_venta').modal('show');

  $('.modal-header-title-generar_venta').text('Nro. Pedido: ' + ID_Pedido_Cabecera);

	$('#cbo-tipo_documento_pedido').val("2");
	$('#cbo-tipo_documento_pedido').val("2").change();

  $("div.id_100 select").val("val2").change();
  $('#modal-loader').modal('show');
  url = base_url + 'TiendaVirtual/PedidosTiendaVirtualController/verPedido/' + ID_Pedido_Cabecera;
  $.getJSON(url, function (response) {
    if (response.sStatus == 'success') {
      var arrData = response.arrData[0], sNombreCliente = '', ID_Tipo_Documento_Identidad = 2, sTipoDocumentoIdentidadCliente='';//2=DNI

      $('.modal-header-title-generar_venta').html(
        'Nro. Pedido: ' + ID_Pedido_Cabecera + ' <br> '
        + 'Almacen: ' + arrData.No_Almacen
      );

      sNombreCliente = arrData.No_Entidad;
      if (sNombreCliente==null)
        sNombreCliente = arrData.Nu_Documento_Identidad;
      
      sTipoDocumentoIdentidadCliente = 'OTROS';
      ID_Tipo_Documento_Identidad = 1;
      if (arrData.Nu_Documento_Identidad.length == 8){
        sTipoDocumentoIdentidadCliente = 'DNI';
        ID_Tipo_Documento_Identidad = 2;
      } else if (arrData.Nu_Documento_Identidad.length == 12) {
        sTipoDocumentoIdentidadCliente = 'CARNET EXTRANJERIA';
        ID_Tipo_Documento_Identidad = 3;//3 = CARNET EXTRANJERIA
      }

      if (arrData.ID_Tipo_Documento==3 && arrData.Nu_Documento_Identidad.length == 11) {
        sTipoDocumentoIdentidadCliente = 'RUC';
        ID_Tipo_Documento_Identidad = 4;//4 = ruc
      }

      $('#info-generar_venta').html(
        '<strong>Cliente:</strong> ' + sNombreCliente + '<br>'
        + '<strong>' + sTipoDocumentoIdentidadCliente + ':</strong> ' + arrData.Nu_Documento_Identidad + '<br>'
        + '<strong>Forma Pago:</strong> ' + arrData.No_Medio_Pago_Tienda_Virtual + '<br>'
        + '<strong>Total:</strong> ' + arrData.Ss_Total
      );

      $('[name="ID_Pedido_Cabecera"]').val(ID_Pedido_Cabecera);
      $('[name="ID_Almacen"]').val( arrData.ID_Almacen );//CAMBIAR POR ID_ALMACEN QUE SE GUARDA EN PEDIDO
      $('[name="fTotalDocumento"]').val( arrData.Ss_Total );
      
      if($('#hidden-Nu_Tipo_Proveedor_FE').val()==3){
        $('[name="ID_Tipo_Documento"]').val(2);
      } else
        $('[name="ID_Tipo_Documento"]').val(arrData.ID_Tipo_Documento);

      $('[name="ID_Tipo_Documento_Identidad"]').val(ID_Tipo_Documento_Identidad);
      $('[name="Nu_Documento_Identidad"]').val(arrData.Nu_Documento_Identidad);
      $('[name="Nu_Celular_Entidad_Order_Address_Entry"]').val(arrData.Nu_Celular_Entidad);
      $('[name="ID_Distrito_Delivery"]').val(arrData.ID_Distrito_Delivery);
      $('[name="Txt_Direccion_Entidad_Order_Address_Entry"]').val(arrData.Txt_Direccion);
      $('[name="Txt_Direccion_Referencia_Entidad_Order_Address_Entry"]').val(arrData.Txt_Direccion_Referencia);
      $('[name="Nu_Tipo_Recepcion"]').val(arrData.Nu_Tipo_Recepcion);
      $('[name="ID_Moneda"]').val(arrData.ID_Moneda);
      $('[name="ID_Medio_Pago"]').val(arrData.ID_Medio_Pago);
      $('[name="No_Entidad"]').val(arrData.No_Entidad);
      $('[name="ID_Entidad"]').val(arrData.ID_Entidad);
    } else {
      alert('Problemas');
    }
    $('#modal-loader').modal('hide');
  }, 'JSON');
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

//AGREGAR PEDIDO MANUALMENTE PORQUE PUEDEN USAR OTRA PLATAFORMA
function agregarPedido(){
  $( '#form-agregar_pedido' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '.div-Listar' ).hide();
  $( '.div-AgregarEditar' ).show();

  $( '[name="EID_Pedido_Cabecera"]' ).val('');
  $( '[name="ENu_Estado_Pedido_Empresa"]' ).val('');

	$( '.input-datepicker-today-to-more' ).inputmask('dd/mm/yyyy', { 'placeholder': 'dd/mm/yyyy' });
	$( '.input-datepicker-today-to-more' ).val(fDay + '/' + fMonth + '/' + fYear);
  
  $('#radio-forma_pago-contraentrega').prop('checked', true).iCheck('update');
  $('#radio-forma_pago-dropshipping').prop('checked', false).iCheck('update');
  
	$( '#radio-paqueteria-ecxlae' ).prop('checked', true).iCheck('update');
  $( '#radio-paqueteria-99' ).prop('checked', false).iCheck('update');
  $( '#radio-paqueteria-quiken' ).prop('checked', false).iCheck('update');

  $('#radio-servicio_callcenter').prop('checked', true).iCheck('update');
  $('#radio-servicio_coordinado').prop('checked', false).iCheck('update');
  
	$( '#table-pedido_productos tbody' ).empty();

  $('.status-pedido').hide();
  $('#btn-save').show();

  $("#textarea-descripcion_item").summernote("code", "");

  $('#span-pedido_pendiente').show();
  $('#span-pedido_completado').hide();
}

function form_AgregarPedido(){
  var arrDetallePedido = [];  
  $( "#table-pedido_productos tbody tr" ).each(function(){
		fila = $(this);
		var $id_producto = fila.find(".text-id_producto").text();
		var $id_impuesto = fila.find(".text-id_impuesto").text();
		var $cantidad = fila.find(".text-cantidad").text();
    var $precio = fila.find(".text-precio").text();
    var $id_empresa_proveedor = fila.find(".text-id_empresa_proveedor").text();
    var $id_almacen_proveedor = fila.find(".text-id_almacen_proveedor").text();
    var $precio_empresa_proveedor = fila.find(".text-precio_empresa_proveedor").text();
    var $total = fila.find(".text-total").text();
    var obj = {};
    
    obj.id_producto	= $id_producto;
    obj.id_impuesto	= $id_impuesto;
    obj.cantidad	= $cantidad;
    obj.precio	= $precio;
    obj.id_empresa_proveedor = $id_empresa_proveedor;
    obj.id_almacen_proveedor = $id_almacen_proveedor;
    obj.precio_empresa_proveedor = $precio_empresa_proveedor;
    obj.total = $total;
    arrDetallePedido.push(obj);
  });

  if ( $( '#txt-EID_Pedido_Cabecera' ).val().length==0 && arrDetallePedido.length === 0){
    $( '#txt-ANombre' ).closest('.form-group').find('.help-block').html('Debes agregar productos');
    $( '#txt-ANombre' ).closest('.form-group').removeClass('has-success').addClass('has-error');
  } else {
    var arrCabeceraPedido = Array();
    arrCabeceraPedido = {
      'EID_Empresa_Pedido' : $( '[name="EID_Empresa_Pedido"]' ).val(),
      'EID_Pedido_Cabecera' : $( '[name="EID_Pedido_Cabecera"]' ).val(),
      'ENu_Estado_Pedido_Empresa' : $( '[name="ENu_Estado_Pedido_Empresa"]' ).val(),
      'No_Entidad_Order_Address_Entry' : $( '[name="No_Entidad_Order_Address_Entry"]' ).val(),
      'Nu_Celular_Entidad_Order_Address_Entry' : $( '[name="Nu_Celular_Entidad_Order_Address_Entry"]' ).val(),
      'Txt_Email_Dropshipping' : $( '[name="Txt_Email_Dropshipping"]' ).val(),
      'No_Ciudad_Dropshipping' : $( '[name="No_Ciudad_Dropshipping"]' ).val(),
      'Txt_Direccion_Entidad_Order_Address_Entry' : $( '[name="Txt_Direccion_Entidad_Order_Address_Entry"]' ).val(),
      'Txt_Direccion_Referencia_Entidad_Order_Address_Entry' : $( '[name="Txt_Direccion_Referencia_Entidad_Order_Address_Entry"]' ).val(),
      'Fe_Entrega' : $( '[name="Fe_Entrega"]' ).val(),
      'forma_pago' : $('[name="radio-forma_pago"]:checked').attr('value'),
      'paqueteria' : $('[name="radio-paqueteria"]:checked').attr('value'),
      'servicio_transportadora' : $('[name="radio-servicio_transportadora"]:checked').attr('value'),
      'Txt_Glosa' : $( '#textarea-descripcion_item' ).summernote('code'),
      //'Txt_Glosa' : $( '[name="Txt_Glosa"]' ).val(),
    }
    
    $( '#btn-save' ).text('');
    $( '#btn-save' ).attr('disabled', true);
    $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

    $( '#modal-loader' ).modal('show');
    
    url = base_url + 'TiendaVirtual/PedidosTiendaVirtualController/crudPedido';
    $.ajax({
      type		  : 'POST',
      dataType	: 'JSON',
      url		    : url,
      data		  : {
        arrCabeceraPedido : arrCabeceraPedido,
        arrDetallePedido : arrDetallePedido
      },
      success : function( response ){
        $( '#modal-loader' ).modal('hide');
        
        $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
        $( '#modal-message' ).modal('show');
        
        if (response.status == 'success'){
          accion_cliente = '';
                  
          $( '.div-AgregarEditar' ).hide();
          $( '.div-Listar' ).show();
          $( '.modal-message' ).addClass('modal-success');
          $( '.modal-title-message' ).text(response.message);
          setTimeout(function() {$('#modal-message').modal('hide');}, 2100);
          getReporteHTML();
        } else {
          $( '.modal-message' ).addClass('modal-danger');
          $( '.modal-title-message' ).text(response.message);
          setTimeout(function() {$('#modal-message').modal('hide');}, 6100);
        }
        
        $( '#btn-save' ).text('');
        $( '#btn-save' ).append( 'Guardar' );
        $( '#btn-save' ).attr('disabled', false);
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
        
        $( '#btn-save' ).text('');
        $( '#btn-save' ).append( 'Guardar' );
        $( '#btn-save' ).attr('disabled', false);
      }
    });
  }
}

function editarPedido(ID){
  $( '#form-agregar_pedido' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '.div-Listar' ).hide();

  $( '#modal-loader' ).modal('show');
  
	save_method = 'update';
  
	$( '#table-pedido_productos tbody' ).empty();
	
  url = base_url + 'TiendaVirtual/PedidosTiendaVirtualController/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      var arrCabeceraPedido = response[0];
      
      $( '.div-AgregarEditar' ).show();
      
      $('#span-pedido_pendiente').show();
      $('#span-pedido_completado').hide();
      $('#btn-save').show();
      if(arrCabeceraPedido.Nu_Estado_Pedido_Empresa!=0){//pedido completado ya no se puede modificar hasta coordinar con call center o coordinado para que liberen
        $('#btn-save').hide();
        $('#span-pedido_pendiente').hide();
        $('#span-pedido_completado').show();
      }

      $( '[name="EID_Empresa_Pedido"]' ).val(arrCabeceraPedido.ID_Empresa);
      $( '[name="EID_Pedido_Cabecera"]' ).val(arrCabeceraPedido.ID_Pedido_Cabecera);
      $( '[name="ENu_Estado_Pedido_Empresa"]' ).val(arrCabeceraPedido.Nu_Estado_Pedido_Empresa);
      $('[name="No_Entidad_Order_Address_Entry"]').val(arrCabeceraPedido.No_Entidad_Order_Address_Entry);
      $('[name="Nu_Celular_Entidad_Order_Address_Entry"]').val(arrCabeceraPedido.Nu_Celular_Entidad_Order_Address_Entry);
      $('[name="Txt_Email_Dropshipping"]').val(arrCabeceraPedido.Txt_Email_Dropshipping);
      $('[name="No_Ciudad_Dropshipping"]').val(arrCabeceraPedido.No_Ciudad_Dropshipping);
      $('[name="Txt_Direccion_Entidad_Order_Address_Entry"]').val(arrCabeceraPedido.Txt_Direccion_Entidad_Order_Address_Entry);
      $('[name="Txt_Direccion_Referencia_Entidad_Order_Address_Entry"]').val(arrCabeceraPedido.Txt_Direccion_Referencia_Entidad_Order_Address_Entry);
     
      if(arrCabeceraPedido.Fe_Entrega!='' && arrCabeceraPedido.Fe_Entrega!=null){
        $( '[name="Fe_Entrega"]' ).val(ParseDateString(arrCabeceraPedido.Fe_Entrega, 6, '-'));
      }

      $('#radio-forma_pago-contraentrega').prop('checked', true).iCheck('update');
      $('#radio-forma_pago-dropshipping').prop('checked', false).iCheck('update');
      if ( arrCabeceraPedido.Nu_Forma_Pago_Dropshipping == 2 ) {
        $('#radio-forma_pago-contraentrega').prop('checked', false).iCheck('update');
        $('#radio-forma_pago-dropshipping').prop('checked', true).iCheck('update');
      }
      
      $( '#radio-paqueteria-ecxlae' ).prop('checked', false).iCheck('update');
      $( '#radio-paqueteria-99' ).prop('checked', false).iCheck('update');
      $( '#radio-paqueteria-quiken' ).prop('checked', false).iCheck('update');
      if ( arrCabeceraPedido.Nu_Tipo_Guia_Api == 1 ) {
        $( '#radio-paqueteria-99' ).prop('checked', true).iCheck('update');
      } else if ( arrCabeceraPedido.Nu_Tipo_Guia_Api == 2 ) {
        $( '#radio-paqueteria-quiken' ).prop('checked', true).iCheck('update');
      } else if ( arrCabeceraPedido.Nu_Tipo_Guia_Api == 3 ) {
        $( '#radio-paqueteria-ecxlae' ).prop('checked', true).iCheck('update');
      }

      $('#radio-servicio_callcenter').prop('checked', true).iCheck('update');
      $('#radio-servicio_coordinado').prop('checked', false).iCheck('update');
      if ( arrCabeceraPedido.Nu_Servicio_Transportadora_Dropshipping == 2 ) {
        $('#radio-servicio_callcenter').prop('checked', false).iCheck('update');
        $('#radio-servicio_coordinado').prop('checked', true).iCheck('update');
      }

      //$('[name="Txt_Glosa"]').val(arrCabeceraPedido.Txt_Glosa);
      $("#textarea-descripcion_item").summernote("code", arrCabeceraPedido.Txt_Glosa);

      $( '#div-pedido_productos' ).show();
      $( '#table-pedido_productos' ).show();
      var table_enlace_producto = "";
      for (i = 0; i < response.length; i++) {
        table_enlace_producto += 
        "<tr id='tr_enlace_producto" + response[i]['ID_Producto'] + "'>"
          + "<td class='text-left text-id_producto' style='display:none;'>" + response[i]['ID_Producto'] + "</td>"
          + "<td class='text-left text-id_impuesto' style='display:none;'>" + response[i]['ID_Impuesto_Cruce_Documento'] + "</td>"
          + "<td class='text-left text-id_empresa_proveedor' style='display:none;'>" + response[i]['ID_Empresa_Proveedor'] + "</td>"
          + "<td class='text-left text-id_almacen_proveedor' style='display:none;'>" + response[i]['ID_Almacen_Proveedor'] + "</td>"
          + "<td class='text-left'>" + response[i]['No_Empresa'] + "</td>"
          + "<td class='text-left'>" + response[i]['No_Almacen'] + "</td>"
          + "<td class='text-left'>" + response[i]['No_Producto'] + ' ' + response[i]['Txt_Nota']  + "</td>"
          + "<td class='text-right text-cantidad'>" + number_format(response[i]['Qt_Producto'], 0) + "</td>"
          + "<td class='text-right text-precio'>" + number_format(response[i]['Ss_Precio'], 2) + "</td>"
          + "<td class='text-right text-total'>" + number_format(response[i]['Ss_Total'], 2) + "</td>"
          + "<td class='text-right text-precio_empresa_proveedor'>" + response[i]['Ss_Precio_Empresa_Proveedor'] + "</td>"
          + "<td class='text-right text-total_empresa_proveedor'>" + (parseFloat(response[i]['Qt_Producto']) * parseFloat(response[i]['Ss_Precio_Empresa_Proveedor'])) + "</td>"
          + "<td class='text-center'><button type='button' id='btn-deleteProductoEnlace' class='btn btn-xs btn-link' alt='Quitar' title='Quitar'><i class='fa fa-trash-o fa-2x' aria-hidden='true'></i></button></td>"
        + "</tr>";
      }
      $( '#table-pedido_productos' ).append(table_enlace_producto);

      $( '#modal-loader' ).modal('hide');
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
    }
  });
}

function isExistTableTemporalEnlacesProducto($ID_Producto){
  return Array.from($('tr[id*=tr_enlace_producto]'))
    .some(element => ($('td:nth(0)',$(element)).html()===$ID_Producto));
}

function verCuentasBancariasDropshipping(){
	$( '.modal-cuentas_bancarias_dropshipping' ).modal('show');
}

function verNovedades(ID){
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();

  $('#btn-save_nota_pedido').hide();
  $( '#hidden-modal-id_pedido_cabecera_nota' ).val(ID);
  $("#textarea-ver_novedades").summernote("code", "");
	$( '.modal-nota_pedido' ).modal('show');
  url = base_url + 'DeliveryDropshippingController/getPedidoCliente';
  var arrParams = {iIdPedidoCabecera : ID};
  $.post( url, arrParams, function( response ){
    if(response!=null && response!='') {
      $("#textarea-ver_novedades").summernote("code", response.Txt_Glosa);
    }
  }, 'JSON');
}

function pdfGuia99(ID){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '.modal-message-delete' ).removeClass('modal-danger modal-warning modal-success');
  $( '.modal-message-delete' ).addClass('modal-success');
  
  $( '.modal-title-message-delete' ).text('¿Deseas generar PDF?');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });
  
  $( '#btn-save-delete' ).off('click').click(function () {
    sendPDF($modal_delete, ID);
  });
}

function sendPDF($modal_delete, ID){
  $( '#modal-loader' ).modal('show');
  $modal_delete.modal('hide');
  url = base_url + 'CoordinadoController/sendPDF/' + ID;
  window.open(url,'_blank');
  $( '#modal-loader' ).modal('hide');
}

function pdfGuiaEcxlae(ID){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '.modal-message-delete' ).removeClass('modal-danger modal-warning modal-success');
  $( '.modal-message-delete' ).addClass('modal-success');
  
  $( '.modal-title-message-delete' ).text('¿Deseas generar PDF?');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });
  
  $( '#btn-save-delete' ).off('click').click(function () {
    sendPDFEcxlae($modal_delete, ID);
  });
}

function sendPDFEcxlae($modal_delete, ID){
  $( '#modal-loader' ).modal('show');
  $modal_delete.modal('hide');
  url = base_url + 'CoordinadoController/sendPDFEcxlae/' + ID;
  window.open(url,'_blank');
  $( '#modal-loader' ).modal('hide');
}