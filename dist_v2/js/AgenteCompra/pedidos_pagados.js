var url, table_Entidad;
//AUTOCOMPLETE
var caractes_no_validos_global_autocomplete = "\"'~!@%^\|";
// Se puede crear un arreglo a partir de la cadena
let search_global_autocomplete = caractes_no_validos_global_autocomplete.split('');
// Solo tomé algunos caracteres, completa el arreglo
let replace_global_autocomplete = ['', '', '', '', '', '', '', '', ''];
//28 caracteres
// FIN AUTOCOMPLETE

$(function () {
  //Global Autocomplete
  $( '.autocompletar' ).autoComplete({
    minChars: 0,
    source: function (term, response) {
      term = term.trim();
      if (term.length > 2) {
        var term = term.toLowerCase();
        $.post( base_url + 'AutocompleteImportacionController/globalAutocompleteItemxUnidad', { global_search : term }, function( arrData ){
          response(arrData);
        }, 'JSON');
      }
    },
    renderItem: function (item, search){
      search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
      var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");
      return '<div title="' + caracteresValidosAutocomplete(item.Nombre) + '" class="autocomplete-suggestion" data-id="' + item.ID + '" data-id_item="' + item.id_item + '" data-id_unidad_medida="' + item.ID_Unidad_Medida + '" data-id_unidad_medida_2="' + item.ID_Unidad_Medida_Precio + '" data-precio_importacion="' + item.precio_importacion + '" data-cantidad_configurada_item="' + item.cantidad_configurada_item + '" data-nombre_unidad_medida="' + item.nombre_unidad_medida + '" data-nombre_item="' + caracteresValidosAutocomplete(item.nombre_item) + '" data-nombre="' + caracteresValidosAutocomplete(item.Nombre) + '" data-val="' + search + '">' + caracteresValidosAutocomplete(item.Nombre).replace(re, "<b>$1</b>") + '</div>';
    },
    onSelect: function(e, term, item){
      $( '#txt-AID' ).val(item.data('id'));
      $( '#txt-ID_Producto' ).val(item.data('id_item'));
      $( '#txt-ID_Unidad_Medida' ).val(item.data('id_unidad_medida'));
      $( '#txt-ID_Unidad_Medida_2' ).val(item.data('id_unidad_medida_2'));
      $( '#txt-Precio_Producto' ).val(item.data('precio_importacion'));
      $( '#txt-Nombre_Producto' ).val(item.data('nombre_item'));
      $( '#txt-Cantidad_Configurada_Producto' ).val(item.data('cantidad_configurada_item'));
      $( '#txt-Nombre_Unidad_Medida' ).val(item.data('nombre_unidad_medida'));
      $( '#txt-ANombre' ).val(item.data('nombre'));

			arrItemVentaTemporal={
				id:item.data('id'),
				id_item:item.data('id_item'),
				id_unidad_medida:item.data('id_unidad_medida'),
				id_unidad_medida_2:item.data('id_unidad_medida_2'),
				precio_item:item.data('precio_importacion'),
				nombre_interno:item.data('nombre'),
        nombre:item.data('nombre_item'),
        cantidad_configurada_item:item.data('cantidad_configurada_item'),
        nombre_unidad_medida:item.data('nombre_unidad_medida'),
      }
      
			//agregarItemVentaTemporal(arrItemVentaTemporal);
    }
  });

  url = base_url + 'AgenteCompra/PedidosPagados/ajax_list';
  table_Entidad = $( '#table-Pedidos' ).DataTable({
    dom: "<'row'<'col-sm-12 col-md-4'B><'col-sm-12 col-md-7'f><'col-sm-12 col-md-1'>>" +
    "<'row'<'col-sm-12'tr>>" +
    "<'row'<'col-sm-12 col-md-2'l><'col-sm-12 col-md-5'i><'col-sm-12 col-md-5'p>>",
    buttons     : [{
      extend    : 'excel',
      text      : '<i class="fa fa-file-excel color_icon_excel"></i> Excel',
      titleAttr : 'Excel',
      exportOptions: {
        columns: ':visible'
      }
    },
    {
      extend    : 'pdf',
      text      : '<i class="fa fa-file-pdf color_icon_pdf"></i> PDF',
      titleAttr : 'PDF',
      exportOptions: {
        columns: ':visible'
      }
    },
    {
      extend    : 'colvis',
      text      : '<i class="fa fa-ellipsis-v"></i> Columnas',
      titleAttr : 'Columnas',
      exportOptions: {
        columns: ':visible'
      }
    }],
    "paging": true,
    "lengthChange": true,
    "searching": true,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "responsive": false,
    'pagingType'  : 'full_numbers',
    'oLanguage' : {
      'sInfo'              : 'Mostrando (_START_ - _END_) total de registros _TOTAL_',
      'sLengthMenu'        : '_MENU_',
      'sSearch'            : 'Buscar por: ',
      'sSearchPlaceholder' : '',
      'sZeroRecords'       : 'No se encontraron registros',
      'sInfoEmpty'         : 'No hay registros',
      'sLoadingRecords'    : 'Cargando...',
      'sProcessing'        : 'Procesando...',
      'oPaginate'          : {
        'sFirst'    : '<<',
        'sLast'     : '>>',
        'sPrevious' : '<',
        'sNext'     : '>',
      },
    },
    'order': [],
    'ajax': {
      'url'       : url,
      'type'      : 'POST',
      'dataType'  : 'JSON',
      'data'      : function ( data ) {
        data.Filtros_Entidades = $( '#cbo-Filtros_Entidades' ).val(),
        data.Global_Filter = $( '#txt-Global_Filter' ).val();
      },
    },
    'columnDefs': [
      {
        'targets': 'no-hidden',
        "visible": false, 
      },{
      'className' : 'text-center',
      'targets'   : 'no-sort',
      'orderable' : false,
    },],
    'lengthMenu': [[10, 100, 1000, -1], [10, 100, 1000, "Todos"]],
  });
  
  $('#table-Pedidos_filter input').removeClass('form-control-sm');
  $('#table-Pedidos_filter input').addClass('form-control-md');
  $('#table-Pedidos_filter input').addClass("width_full");

  $( '.div-AgregarEditar' ).hide();

	$( '#btn-addProductosEnlaces' ).click(function(){
	  var $ID_Producto        = $( '#txt-AID' ).val();
    var $ID_Producto_BD     = $( '#txt-ID_Producto' ).val();
    var $ID_Unidad_Medida   = $( '#txt-ID_Unidad_Medida' ).val();
    var $ID_Unidad_Medida_2 = $( '#txt-ID_Unidad_Medida_2' ).val();
    var $No_Producto_Enlace = $( '#txt-ANombre' ).val();
    var $Nombre_Producto = $( '#txt-Nombre_Producto' ).val();
    var $Qt_Producto_Enlace = $( '#txt-Qt_Producto_Descargar' ).val();
    var $Cantidad_Configurada_Producto = $( '#txt-Cantidad_Configurada_Producto' ).val();
    var $Nombre_Unidad_Medida = $( '#txt-Nombre_Unidad_Medida' ).val();
    var $Precio_Producto = $( '#txt-Precio_Producto' ).val();
  
    if ( $ID_Producto.length === 0 || $No_Producto_Enlace.length === 0) {
	    $( '#txt-ANombre' ).closest('.form-group').find('.help-block').html('Seleccionar producto');
			$( '#txt-ANombre' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $Qt_Producto_Enlace.length === 0) {
      $( '#txt-Qt_Producto_Descargar' ).closest('.form-group').find('.help-block').html('Ingresar cantidad');
		  $( '#txt-Qt_Producto_Descargar' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $Qt_Producto_Enlace == 0) {
      $( '#txt-Qt_Producto_Descargar' ).closest('.form-group').find('.help-block').html('La cantidad mayor 0');
		  $( '#txt-Qt_Producto_Descargar' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      var cantidad_item = (parseFloat($Cantidad_Configurada_Producto) * parseFloat($Qt_Producto_Enlace));
      var total_item = (cantidad_item * parseFloat($Precio_Producto));
			arrItemVentaTemporal={
				id : $ID_Producto,
				id_item : $ID_Producto_BD,
        id_unidad_medida : $ID_Unidad_Medida,
        id_unidad_medida_2 : $ID_Unidad_Medida_2,
        nombre_interno : $No_Producto_Enlace,
				nombre : $Nombre_Producto,
        cantidad_item : cantidad_item,
        precio_item : $Precio_Producto,
        total_item : total_item,
        nombre_unidad_medida : $Nombre_Unidad_Medida
      }
      
			agregarItemVentaTemporal(arrItemVentaTemporal);
    }
  });
  
	$( '#table-Producto_Enlace tbody' ).on('click', '#btn-deleteProductoEnlace', function(){
    $(this).closest ('tr').remove ();
    calcularTotales();
    if ($( '#table-Producto_Enlace >tbody >tr' ).length == 0)
	    $( '#table-Producto_Enlace' ).hide();
	})
  
  $( "#form-pedido" ).validate({
		rules:{
			No_Entidad: {
				required: true
			},
		},
		messages:{
			No_Entidad:{
				required: "Ingresar nombre",
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
		submitHandler: form_pedido
	});
  
  $("#table-Producto_Enlace").on('click', '.img-table_item', function () {
    $('.img-responsive').attr('src', '');

    $('.modal-ver_item').modal('show');
    $('.img-responsive').attr('src', $(this).data('url_img'));
    $("#a-download_image").attr("data-id_item", $(this).data('id_item'));
  })

	$(document).on('click', '.btn-ver_pago_proveedor', function (e) {
    $('.img-responsive').attr('src', '');

    $('.modal-ver_item').modal('show');
    $('.img-responsive').attr('src', $(this).data('url_img'));
    $("#a-download_image").attr("data-id_item", $(this).data('id'));
  })
  
	$( '#a-download_image' ).click(function(){
    id = $(this).data('id_item');
    url = base_url + 'AgenteCompra/PedidosPagados/downloadImage/' + id;
    
    var popupwin = window.open(url);
    setTimeout(function() { popupwin.close();}, 2000);
  })

	$(document).on('click', '.btn-agregar_pago_proveedor', function (e) {
    e.preventDefault();

    var id_empresa = $(this).data('id_empresa');
    var id_organizacion = $(this).data('id_organizacion');
    var id_cabecera = $(this).data('id_pedido_cabecera');
    var id_detalle = $(this).data('id_pedido_detalle');
    var id = $(this).data('id');
    var tipo_pago = $(this).data('tipo_pago');

    $( '[name="proveedor-id_empresa"]' ).val(id_empresa);
    $( '[name="proveedor-id_organizacion"]' ).val(id_organizacion);
    $( '[name="proveedor-id_cabecera"]' ).val(id_cabecera);
    $( '[name="proveedor-id_detalle"]' ).val(id_detalle);
    $( '[name="proveedor-id"]' ).val(id);
    $( '[name="proveedor-tipo_pago"]' ).val(tipo_pago);

    $('#modal-agregar_pago').modal('show');

    $( '#modal-agregar_pago' ).on('shown.bs.modal', function() {
      $( '#amount_proveedor' ).focus();
    })
  })

	$(document).on('click', '.btn-agregar_inspeccion', function (e) {
    e.preventDefault();

    var id_empresa = $(this).data('id_empresa');
    var id_organizacion = $(this).data('id_organizacion');
    var id_cabecera = $(this).data('id_pedido_cabecera');
    var id_detalle = $(this).data('id_pedido_detalle');
    var id = $(this).data('id');
    var tipo_pago = $(this).data('tipo_pago');

    $( '[name="proveedor-id_empresa"]' ).val(id_empresa);
    $( '[name="proveedor-id_organizacion"]' ).val(id_organizacion);
    $( '[name="proveedor-id_cabecera"]' ).val(id_cabecera);
    $( '[name="proveedor-id_detalle"]' ).val(id_detalle);
    $( '[name="proveedor-id"]' ).val(id);
    $( '[name="proveedor-tipo_pago"]' ).val(tipo_pago);

    $('#modal-agregar_inspeccion').modal('show');
  })

  $("#form-agregar_inspeccion").on('submit',function(e){
    e.preventDefault();

    $('.help-block').empty();
    $('.form-group').removeClass('has-error');

    if(document.getElementById('image_inspeccion').files.length == 0) {
      $('#image_inspeccion').closest('.form-group').find('.help-block').html('Empty image');
      $('#image_inspeccion').closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      var postData = new FormData($("#form-agregar_inspeccion")[0]);
      $.ajax({
        url: base_url + 'AgenteCompra/PedidosPagados/addInspeccionProveedor',
        type: "POST",
        dataType: "JSON",
        data: postData,
        processData: false,
        contentType: false
      })
      .done(function(response) {
        if(response.status == 'success') {
          $('#modal-agregar_inspeccion').modal('hide');
          subirInspeccion($('#proveedor-id_cabecera').val());
        } else {
          alert(response.message);
        }
      });
    }
  });

  $("#form-agregar_pago_proveedor").on('submit',function(e){
    e.preventDefault();

    $('.help-block').empty();
    $('.form-group').removeClass('has-error');

    const amount_proveedor = parseFloat($('#amount_proveedor').val())

    if( isNaN(amount_proveedor) || amount_proveedor<=0.00 || amount_proveedor<=0) {
      $('#amount_proveedor').closest('.form-group').find('.help-block').html('Empty Amount');
      $('#amount_proveedor').closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if(document.getElementById('voucher_proveedor').files.length == 0) {
      $('#voucher_proveedor').closest('.form-group').find('.help-block').html('Empty image');
      $('#voucher_proveedor').closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      var postData = new FormData($("#form-agregar_pago_proveedor")[0]);
      $.ajax({
        url: base_url + 'AgenteCompra/PedidosPagados/addPagoProveedor',
        type: "POST",
        dataType: "JSON",
        data: postData,
        processData: false,
        contentType: false
      })
      .done(function(response) {
        if(response.status == 'success') {
          $('#modal-agregar_pago').modal('hide');
          verPedido($('#proveedor-id_cabecera').val());
        } else {
          alert(response.message);
        }
      });
    }
  });
  

  $("#form-documento_entrega").on('submit',function(e){
    e.preventDefault();

    $('.help-block').empty();
    $('.form-group').removeClass('has-error');

    if(document.getElementById('image_documento').files.length == 0) {
      $('#image_documento').closest('.form-group').find('.help-block').html('Empty file');
      $('#image_documento').closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      var postData = new FormData($("#form-documento_entrega")[0]);
      $.ajax({
        url: base_url + 'AgenteCompra/PedidosPagados/addFileProveedor',
        type: "POST",
        dataType: "JSON",
        data: postData,
        processData: false,
        contentType: false
      })
      .done(function(response) {
        $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
        $('#modal-message').modal('show');

        if(response.status == 'success') {
          $('#modal-documento_entrega').modal('hide');

          $('#moda-message-content').addClass( 'bg-' + response.status);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
          reload_table_Entidad();
        } else {
          $('#moda-message-content').addClass( 'bg-danger' );
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 2100);
        }
      });
    }
  });

  $('#span-id_pedido').html('');
})

function reload_table_Entidad(){
  table_Entidad.ajax.reload(null,false);
}

function verPedido(ID){
  $( '.div-Listar' ).hide();
  
  $( '#form-pedido' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();

	$( '#table-Producto_Enlace tbody' ).empty();
  $( '#table-Producto_Enlace' ).show();

  $('#span-id_pedido').html('Nro. ' + ID);

  url = base_url + 'AgenteCompra/PedidosPagados/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      console.log(response);
      var detalle = response;
      response = response[0];

      $( '.div-AgregarEditar' ).show();
            
      $( '[name="EID_Pedido_Cabecera"]' ).val(response.ID_Pedido_Cabecera);
      $( '[name="EID_Entidad"]' ).val(response.ID_Entidad);
      $( '[name="EID_Empresa"]' ).val(response.ID_Empresa);
      $( '[name="EID_Organizacion"]' ).val(response.ID_Organizacion);

      $( '[name="No_Contacto"]' ).val(response.No_Contacto);
      $( '[name="Txt_Email_Contacto"]' ).val(response.Txt_Email_Contacto);
      $( '[name="Nu_Celular_Contacto"]' ).val(response.Nu_Celular_Contacto);
      $( '[name="No_Entidad"]' ).val(response.No_Entidad);
      $( '[name="Nu_Documento_Identidad"]' ).val(response.Nu_Documento_Identidad);

      var sNombreEstado = '<span class="badge badge-pill badge-secondary">Pendiente</span>';
      if(response.Nu_Estado_Pedido == 2)
        sNombreEstado = '<span class="badge badge-pill badge-primary">Confirmado</span>';
      else if(response.Nu_Estado_Pedido == 3)
        sNombreEstado = '<span class="badge badge-pill badge-success">Entregado</span>';
      else if(response.Nu_Estado_Pedido == 4)
        sNombreEstado = '<span class="badge badge-pill badge-danger">Confirmado</span>';
      $( '#div-estado' ).html(sNombreEstado);

      var table_enlace_producto = "";
      for (i = 0; i < detalle.length; i++) {
        var cantidad_item = parseFloat(detalle[i]['Qt_Producto']);
        var precio_china = parseFloat(detalle[i]['Ss_Precio']);

        var id_item = detalle[i]['ID_Pedido_Detalle_Producto_Proveedor'];
        var voucher_1 = detalle[i]['Txt_Url_Archivo_Pago_1_Proveedor'];
        var voucher_2 = detalle[i]['Txt_Url_Archivo_Pago_2_Proveedor'];
        //max-height: 350px;width: 100%; cursor:pointer

        var fTotal = (cantidad_item * precio_china);
        var Ss_Pago_1_Proveedor = parseFloat(detalle[i]['Ss_Pago_1_Proveedor']);
        var Ss_Pago_2_Proveedor = parseFloat(detalle[i]['Ss_Pago_2_Proveedor']);
        table_enlace_producto +=
        "<tr id='tr_enlace_producto" + id_item + "'>"
          + "<td style='display:none;' class='text-left td-id_item'>" + id_item + "</td>"
          + "<td class='text-center td-name' width='50%'>"
            + "<img style='' data-id_item='" + id_item + "' data-url_img='" + detalle[i]['Txt_Url_Imagen_Producto'] + "' src='" + detalle[i]['Txt_Url_Imagen_Producto'] + "' alt='" + detalle[i]['Txt_Producto'] + "' class='img-thumbnail img-table_item img-fluid img-resize mb-2'>";
            
          table_enlace_producto += "</td>"
          + "<td class='text-left td-name'>" + detalle[i]['Txt_Producto'] + "</td>"
          + "<td class='text-right td-qty'>" + Math.round10(cantidad_item, -2) + "</td>"
          + "<td class='text-right td-price'>" + Math.round10(precio_china, -2) + "</td>"
          +"<td class='text-right td-amount'>" + Math.round10(fTotal, -2) + "</td>"
          +"<td class='text-right td-pay1'>" + Math.round10(Ss_Pago_1_Proveedor, -2) + "</td>"
          +"<td class='text-right td-balance'>" + Math.round10(fTotal - Ss_Pago_1_Proveedor, -2) + "</td>"
          +"<td class='text-right td-pay2'>" + Math.round10(Ss_Pago_2_Proveedor, -2) + "</td>"
          +"<td class='text-left td-delivery_date'>" + detalle[i]['Nu_Dias_Delivery'] + "</td>"
          +"<td class='text-left td-supplier'></td>"
          +"<td class='text-left td-phone'></td>"
          table_enlace_producto += '<input type="hidden" name="addProducto[' + id_item + '][id_item]" value="' + id_item + '">';
        table_enlace_producto += "</tr>";
        
        table_enlace_producto +=
        "<tr><td class='text-left' colspan='12'>"
          if( voucher_1 == '' || voucher_1 == null ){
            table_enlace_producto += '<button type="button" id="btn-agregar_pago_proveedor' + id_item + '" data-tipo_pago="1" data-id="' + id_item + '" class="text-left btn btn-primary btn-block btn-agregar_pago_proveedor" data-id_empresa="' + response.ID_Empresa + '" data-id_organizacion="' + response.ID_Organizacion + '" data-id_pedido_cabecera="' + response.ID_Pedido_Cabecera + '" data-id_pedido_detalle="' + response.ID_Pedido_Detalle + '"><i class="fas fa-money-bill-alt"></i>&nbsp; Pagar Proveedor</button>';
          } else {
            table_enlace_producto += '<button type="button" id="btn-ver_pago_proveedor' + id_item + '" data-url_img="' + voucher_1 + '" data-id="' + id_item + '" class="text-left btn btn-secondary btn-block btn-ver_pago_proveedor" data-id_empresa="' + response.ID_Empresa + '" data-id_organizacion="' + response.ID_Organizacion + '" data-id_pedido_cabecera="' + response.ID_Pedido_Cabecera + '" data-id_pedido_detalle="' + response.ID_Pedido_Detalle + '"><i class="fas fa-money-bill-alt"></i>&nbsp; (1) Pago ¥ ' + Ss_Pago_1_Proveedor +  '</button>';
            if( voucher_2 == '' || voucher_2 == null ){
              table_enlace_producto += '<button type="button" id="btn-agregar_pago_proveedor' + id_item + '" data-tipo_pago="2" data-id="' + id_item + '" class="text-left btn btn-primary btn-block btn-agregar_pago_proveedor" data-id_empresa="' + response.ID_Empresa + '" data-id_organizacion="' + response.ID_Organizacion + '" data-id_pedido_cabecera="' + response.ID_Pedido_Cabecera + '" data-id_pedido_detalle="' + response.ID_Pedido_Detalle + '"><i class="fas fa-money-bill-alt"></i>&nbsp; Pagar Proveedor</button>';
            } else {
              table_enlace_producto += '<button type="button" id="btn-ver_pago_proveedor' + id_item + '" data-url_img="' + voucher_2 + '" data-id="' + id_item + '" class="text-left btn btn-secondary btn-block btn-ver_pago_proveedor" data-id_empresa="' + response.ID_Empresa + '" data-id_organizacion="' + response.ID_Organizacion + '" data-id_pedido_cabecera="' + response.ID_Pedido_Cabecera + '" data-id_pedido_detalle="' + response.ID_Pedido_Detalle + '"><i class="fas fa-money-bill-alt"></i>&nbsp; (2) Pago ¥ ' + Ss_Pago_2_Proveedor + '</button>';
            }
          }
        table_enlace_producto +=
        "</td></tr>";
      }
      
      $('#span-total_cantidad_items').html(i);
      $( '#table-Producto_Enlace' ).append(table_enlace_producto);
    },
    error: function (jqXHR, textStatus, errorThrown) {
	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
	    
  	  $( '#modal-message' ).modal('show');
	    $( '.modal-message' ).addClass( 'modal-danger' );
	    $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
	    setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
	    
	    //Message for developer
      console.log(jqXHR.responseText);
    }
  })
}

function cambiarEstado(ID, Nu_Estado, id_correlativo) {
  var $modal_delete = $('#modal-message-delete');
  $modal_delete.modal('show');

  $('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
  $('.modal-message-delete').addClass('modal-success');

  var sNombreEstado = 'Pendiente';
  if(Nu_Estado==2)
    sNombreEstado = 'Garantizado';

  $('#modal-title').text('¿Deseas cambiar estado a ' + sNombreEstado + '?');

  $('#btn-cancel-delete').off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $('#btn-save-delete').off('click').click(function () {
    
    $( '#btn-save-delete' ).text('');
    $( '#btn-save-delete' ).attr('disabled', true);
    $( '#btn-save-delete' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

    url = base_url + 'AgenteCompra/PedidosPagados/cambiarEstado/' + ID + '/' + Nu_Estado + '/' + id_correlativo;
    $.ajax({
      url: url,
      type: "GET",
      dataType: "JSON",
      success: function (response) {
        $modal_delete.modal('hide');

        $( '#btn-save-delete' ).text('');
        $( '#btn-save-delete' ).append( 'Aceptar' );
        $( '#btn-save-delete' ).attr('disabled', false);

        $('.modal-message').removeClass('modal-danger modal-warning modal-success');
        $('#modal-message').modal('show');

        if (response.status == 'success') {
          $('.modal-message').addClass(response.style_modal);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
          reload_table_Entidad();
        } else {
          $('.modal-message').addClass(response.style_modal);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1500);
        }
      }
    });
  });
}

function caracteresValidosAutocomplete(msg) {
  // Recorrer todos los caracteres
  search_global_autocomplete.forEach((char, index) => {
    // Remplazar cada caracter en la cadena
    msg = msg.replaceAll(char, replace_global_autocomplete[index]);
  });
  return msg;
}

function agregarItemVentaTemporal(arrParams){
  var table_enlace_producto =
  "<tr id='tr_enlace_producto" + arrParams.id + "'>"
    + "<td style='display:none;' class='text-left td-id_item'>" + arrParams.id + "</td>"
    + "<td style='display:none;' class='text-left td-id_item_bd'>" + arrParams.id_item + "</td>"
    + "<td style='display:none;' class='text-left td-id_unidad_medida_bd'>" + arrParams.id_unidad_medida + "</td>"
    + "<td style='display:none;' class='text-left td-id_unidad_medida_precio_bd'>" + arrParams.id_unidad_medida_2 + "</td>"
    + "<td class='text-left td-name'>" + arrParams.nombre + "</td>"
    + "<td class='text-left td-unidad_medida'>" + arrParams.nombre_unidad_medida + "</td>"
    + "<td class='text-right td-cantidad'>" + arrParams.cantidad_item + "</td>"
    + "<td class='text-right td-precio'>" + arrParams.precio_item + "</td>"
    + "<td class='text-right td-total'>" + arrParams.total_item + "</td>"
    + "<td class='text-center'><button type='button' id='btn-deleteProductoEnlace' class='btn btn-xs btn-link text-danger' alt='Eliminar' title='Eliminar'><i class='fas fa-trash-alt fa-2x' aria-hidden='true'></i></button></td>";
    table_enlace_producto += '<input type="hidden" name="addProducto[' + arrParams.id + '][id_item]" value="' + arrParams.id_item + '">';
    table_enlace_producto += '<input type="hidden" name="addProducto[' + arrParams.id + '][id_unidad_medida]" value="' + arrParams.id_unidad_medida + '">';
    table_enlace_producto += '<input type="hidden" name="addProducto[' + arrParams.id + '][id_unidad_medida_2]" value="' + arrParams.id_unidad_medida_2 + '">';
    table_enlace_producto += '<input type="hidden" name="addProducto[' + arrParams.id + '][cantidad_item]" value="' + arrParams.cantidad_item + '">';
    table_enlace_producto += '<input type="hidden" name="addProducto[' + arrParams.id + '][precio_item]" value="' + arrParams.precio_item + '">';
    table_enlace_producto += '<input type="hidden" name="addProducto[' + arrParams.id + '][total_item]" value="' + arrParams.total_item + '">';
  table_enlace_producto += "</tr>";
  
  if( isExistTableTemporalProducto(arrParams.id) ){
    $( '#txt-ANombre' ).closest('.form-group').find('.help-block').html('Ya existe <b>' + arrParams.nombre_interno + '</b>');
    $( '#txt-ANombre' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    $( '#txt-AID' ).val('');
    $( '#txt-ID_Producto' ).val('');
    $( '#txt-ID_Unidad_Medida' ).val('');
    $( '#txt-ID_Unidad_Medida_2' ).val('');
    $( '#txt-Precio_Producto' ).val('');
    $( '#txt-Cantidad_Configurada_Producto' ).val('');
    $( '#txt-Nombre_Producto' ).val('');
    $( '#txt-Nombre_Unidad_Medida' ).val('');
    $( '#txt-ANombre' ).val('');

    $( '#txt-ANombre' ).focus();
  } else {
    $( '#table-Producto_Enlace' ).show();
    $( '#table-Producto_Enlace' ).append(table_enlace_producto);
    $( '#txt-AID' ).val('');
    $( '#txt-ID_Producto' ).val('');
    $( '#txt-ID_Unidad_Medida' ).val('');
    $( '#txt-ID_Unidad_Medida_2' ).val('');
    $( '#txt-Precio_Producto' ).val('');
    $( '#txt-Cantidad_Configurada_Producto' ).val('');
    $( '#txt-Nombre_Producto' ).val('');
    $( '#txt-Nombre_Unidad_Medida' ).val('');
    $( '#txt-ANombre' ).val('');
    
    $( '#txt-ANombre' ).focus();

    //totalizar items
    calcularTotales();
  }
}

function isExistTableTemporalProducto($id){
  return Array.from($('tr[id*=tr_enlace_producto]')).some(element => ($('td:nth(0)',$(element)).html()==$id));
}

function form_pedido(){
  if ($( '#table-Producto_Enlace >tbody >tr' ).length == 0) {
    $( '#txt-ANombre' ).closest('.form-group').find('.help-block').html('Elegir al menos 1 producto');
    $( '#txt-ANombre' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    $( '#txt-ANombre' ).focus();
  } else {
    $( '#btn-save' ).text('');
    $( '#btn-save' ).attr('disabled', true);
    $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
    
    url = base_url + 'AgenteCompra/PedidosPagados/crudPedidoGrupal';
    $.ajax({
      type		  : 'POST',
      dataType	: 'JSON',
      url		    : url,
      data		  : $('#form-pedido').serialize(),
      success : function( response ){      
        $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
        $( '#modal-message' ).modal('show');
        
        if (response.status == 'success'){
          $( '#form-pedido' )[0].reset();
          
          $( '.div-AgregarEditar' ).hide();
          $( '.div-Listar' ).show();
          $( '.modal-message' ).addClass(response.style_modal);
          $( '.modal-title-message' ).text(response.message);
          setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
          reload_table_Entidad();
        } else {
          $( '.modal-message' ).addClass(response.style_modal);
          $( '.modal-title-message' ).text(response.message);
          setTimeout(function() {$('#modal-message').modal('hide');}, 1200);
        }
        
        $( '#btn-save' ).text('');
        $( '#btn-save' ).append( 'Guardar' );
        $( '#btn-save' ).attr('disabled', false);
      },
      error: function (jqXHR, textStatus, errorThrown) {
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

function calcularTotales(){
  var fCantidadTotal = 0, fImporteTotal = 0;
	$( '#table-Producto_Enlace > tbody > tr' ).each(function(){
		fila = $(this);
		const fCantidad = parseFloat(fila.find(".td-cantidad").text());
		const fPrecio = parseFloat(fila.find(".td-precio").text());
    
    fCantidadTotal += fCantidad;
    fImporteTotal += (fCantidad * fPrecio);
  })

  $( '#label-total_cantidad' ).text( Math.round10(fCantidadTotal, -2));
  $( '#label-total_importe' ).text( Math.round10(fImporteTotal, -2));
}

function generarExcelOrderTracking(ID){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '.modal-message-delete' ).removeClass('modal-danger modal-warning modal-success');
  $( '.modal-message-delete' ).addClass('modal-success');

  $('#modal-title').text('¿Deseas genera EXCEL?');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });
  
  $( '#btn-save-delete' ).off('click').click(function () {
    _generarExcelOrderTracking($modal_delete, ID);
  });
}

function _generarExcelOrderTracking($modal_delete, ID){
  $modal_delete.modal('hide');
  url = base_url + 'AgenteCompra/PedidosPagados/generarExcelOrderTracking/' + ID;
  window.open(url,'_blank');
}

function loadFile(event, id){
  var output = document.getElementById('img_producto-preview' + id);
  output.src = URL.createObjectURL(event.target.files[0]);
  output.onload = function() {
    URL.revokeObjectURL(output.src) // free memory
  }
}

function cambiarEstadoChina(ID, Nu_Estado) {
  var $modal_delete = $('#modal-message-delete');
  $modal_delete.modal('show');

  $('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
  $('.modal-message-delete').addClass('modal-success');

  var sNombreEstado = 'Pendiente';
  if(Nu_Estado==2)
    sNombreEstado = 'En proceso';
  else if(Nu_Estado==2)
    sNombreEstado = 'Cotizado';

  $('#modal-title').text('¿Deseas cambiar estado a ' + sNombreEstado + '?');

  $('#btn-cancel-delete').off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $('#btn-save-delete').off('click').click(function () {
    
    $( '#btn-save-delete' ).text('');
    $( '#btn-save-delete' ).attr('disabled', true);
    $( '#btn-save-delete' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

    url = base_url + 'AgenteCompra/PedidosGarantizados/cambiarEstadoChina/' + ID + '/' + Nu_Estado;
    $.ajax({
      url: url,
      type: "GET",
      dataType: "JSON",
      success: function (response) {
        $modal_delete.modal('hide');

        $( '#btn-save-delete' ).text('');
        $( '#btn-save-delete' ).append( 'Aceptar' );
        $( '#btn-save-delete' ).attr('disabled', false);

        $('.modal-message').removeClass('modal-danger modal-warning modal-success');
        $('#modal-message').modal('show');

        if (response.status == 'success') {
          $('.modal-message').addClass(response.style_modal);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
          reload_table_Entidad();
        } else {
          $('.modal-message').addClass(response.style_modal);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1500);
        }
      }
    });
  });
}

function subirInspeccion(ID){
  $( '.div-Listar' ).hide();
  
  $( '#form-pedido' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();

	$( '#table-Producto_Enlace tbody' ).empty();
  $( '#table-Producto_Enlace' ).show();

  $('#span-id_pedido').html('Nro. ' + ID);

  url = base_url + 'AgenteCompra/PedidosPagados/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      console.log(response);
      var detalle = response;
      response = response[0];

      $( '.div-AgregarEditar' ).show();
            
      $( '[name="EID_Pedido_Cabecera"]' ).val(response.ID_Pedido_Cabecera);
      $( '[name="EID_Entidad"]' ).val(response.ID_Entidad);
      $( '[name="EID_Empresa"]' ).val(response.ID_Empresa);
      $( '[name="EID_Organizacion"]' ).val(response.ID_Organizacion);

      $( '[name="No_Contacto"]' ).val(response.No_Contacto);
      $( '[name="Txt_Email_Contacto"]' ).val(response.Txt_Email_Contacto);
      $( '[name="Nu_Celular_Contacto"]' ).val(response.Nu_Celular_Contacto);
      $( '[name="No_Entidad"]' ).val(response.No_Entidad);
      $( '[name="Nu_Documento_Identidad"]' ).val(response.Nu_Documento_Identidad);

      var sNombreEstado = '<span class="badge badge-pill badge-secondary">Pendiente</span>';
      if(response.Nu_Estado_Pedido == 2)
        sNombreEstado = '<span class="badge badge-pill badge-primary">Confirmado</span>';
      else if(response.Nu_Estado_Pedido == 3)
        sNombreEstado = '<span class="badge badge-pill badge-success">Entregado</span>';
      else if(response.Nu_Estado_Pedido == 4)
        sNombreEstado = '<span class="badge badge-pill badge-danger">Confirmado</span>';
      $( '#div-estado' ).html(sNombreEstado);

      var table_enlace_producto = "";
      for (i = 0; i < detalle.length; i++) {
        var cantidad_item = parseFloat(detalle[i]['Qt_Producto']);
        var precio_china = parseFloat(detalle[i]['Ss_Precio']);

        var id_item = detalle[i]['ID_Pedido_Detalle_Producto_Proveedor'];
        var voucher_1 = detalle[i]['Txt_Url_Archivo_Pago_1_Proveedor'];
        var voucher_2 = detalle[i]['Txt_Url_Archivo_Pago_2_Proveedor'];
        var fTotal = (precio_china * cantidad_item);

        var Ss_Pago_1_Proveedor = parseFloat(detalle[i]['Ss_Pago_1_Proveedor']);
        var Ss_Pago_2_Proveedor = parseFloat(detalle[i]['Ss_Pago_2_Proveedor']);
        table_enlace_producto +=
        "<tr id='tr_enlace_producto" + id_item + "'>"
          + "<td style='display:none;' class='text-left td-id_item'>" + id_item + "</td>"
          + "<td class='text-center td-name' width='30%'>"
            + "<img data-id_item='" + id_item + "' data-url_img='" + detalle[i]['Txt_Url_Imagen_Producto'] + "' src='" + detalle[i]['Txt_Url_Imagen_Producto'] + "' alt='" + detalle[i]['Txt_Producto'] + "' class='img-thumbnail img-table_item img-fluid img-resize mb-2'>";
            
          table_enlace_producto += "</td>"
          +"<td class='text-left td-name'>" + detalle[i]['Txt_Producto'] + "</td>"
          +"<td class='text-right td-qty'>" + Math.round10(cantidad_item, -2) + "</td>"
          +"<td class='text-right td-price'>" + Math.round10(precio_china, -2) + "</td>"
          +"<td class='text-right td-amount'>" + Math.round10(fTotal, -2) + "</td>"
          +"<td class='text-right td-pay1'>" + Math.round10(Ss_Pago_1_Proveedor, -2) + "</td>"
          +"<td class='text-right td-balance'>" + Math.round10(fTotal - Ss_Pago_1_Proveedor, -2) + "</td>"
          +"<td class='text-right td-pay2'>" + Math.round10(Ss_Pago_2_Proveedor, -2) + "</td>"
          +"<td class='text-left td-delivery_date'>" + detalle[i]['Nu_Dias_Delivery'] + "</td>"
          +"<td class='text-left td-supplier'></td>"
          +"<td class='text-left td-phone'></td>"
          table_enlace_producto += '<input type="hidden" name="addProducto[' + id_item + '][id_item]" value="' + id_item + '">';
        table_enlace_producto += "</tr>";

        table_enlace_producto +=
        "<tr><td class='text-left' colspan='12'>"
          if(detalle[i]['Nu_Agrego_Inspeccion']==0) {//0=No
            table_enlace_producto += '<button type="button" id="btn-agregar_inspeccion' + id_item + '" data-tipo_pago="1" data-id="' + id_item + '" class="text-left btn btn-primary btn-block btn-agregar_inspeccion" data-id_empresa="' + response.ID_Empresa + '" data-id_organizacion="' + response.ID_Organizacion + '" data-id_pedido_cabecera="' + response.ID_Pedido_Cabecera + '" data-id_pedido_detalle="' + response.ID_Pedido_Detalle + '"><i class="fas fa-images"></i>&nbsp; Subir fotos</button>';
          } else {
            table_enlace_producto += '<button type="button" id="btn-ver_inspeccion' + id_item + '" onclick=verInspeccion(' + id_item + ') class="text-left btn btn-secondary btn-block btn-ver_inspeccion"><i class="fas fa-search"></i>&nbsp; Ver fotos</button>';
          }
        table_enlace_producto += "</td></tr>";
      }
      
      $('#span-total_cantidad_items').html(i);
      $( '#table-Producto_Enlace' ).append(table_enlace_producto);
    },
    error: function (jqXHR, textStatus, errorThrown) {
	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
	    
  	  $( '#modal-message' ).modal('show');
	    $( '.modal-message' ).addClass( 'modal-danger' );
	    $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
	    setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
	    
	    //Message for developer
      console.log(jqXHR.responseText);
    }
  })
}

function verInspeccion(ID){
	$( '#div-img_inspeccion_item' ).html('');

  url = base_url + 'AgenteCompra/PedidosPagados/ajax_edit_inspeccion/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      console.log(response);

      $( '#modal-ver_inspeccion_item' ).modal('show');

      var detalle = response;
      response = response[0];
      
      var table_enlace_producto = "";
      for (i = 0; i < detalle.length; i++) {
        var id_item = detalle[i]['ID_Pedido_Detalle_Producto_Inspeccion'];//max-height: 350px;width: 100%; cursor:pointer
        table_enlace_producto += "<img data-id_item='" + id_item + "' data-url_img='" + detalle[i]['Txt_Url_Imagen_Producto'] + "' src='" + detalle[i]['Txt_Url_Imagen_Producto'] + "' alt='' class='img-thumbnail img-table_item img-fluid img-resize mb-2'>";
      }
      $( '#div-img_inspeccion_item' ).html(table_enlace_producto);
    },
    error: function (jqXHR, textStatus, errorThrown) {
	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
	    
  	  $( '#modal-message' ).modal('show');
	    $( '.modal-message' ).addClass( 'modal-danger' );
	    $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
	    setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
	    
	    //Message for developer
      console.log(jqXHR.responseText);
    }
  })
}

function documentoEntregado(id){
  $( '[name="documento-id_cabecera"]' ).val(id);

  $('#modal-documento_entrega').modal('show');
  $( '#form-documento_entrega' )[0].reset();
}

function descargarDocumentoEntregado(id){
  url = base_url + 'AgenteCompra/PedidosPagados/descargarDocumentoEntregado/' + id;
  
  var popupwin = window.open(url);
  setTimeout(function() { popupwin.close();}, 2000);
}