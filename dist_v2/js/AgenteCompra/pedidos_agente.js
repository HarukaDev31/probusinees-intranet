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

  url = base_url + 'AgenteCompra/PedidosAgente/ajax_list';
  table_Entidad = $( '#table-Pedidos' ).DataTable({
    dom: "<'row'<'col-sm-12 col-md-5'B><'col-sm-12 col-md-2'><'col-sm-12 col-md-5'f>>" +
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
  
  $( '.custom-select' ).removeClass('custom-select-sm form-control-sm');

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

  url = base_url + 'AgenteCompra/PedidosAgente/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
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
        var id_item = detalle[i]['ID_Pedido_Detalle'];
        var href_link = (detalle[i]['Txt_Url_Link_Pagina_Producto'] != '' && detalle[i]['Txt_Url_Link_Pagina_Producto'] != null ? "<a class='btn btn-link p-0 m-0' target='_blank' rel='noopener noreferrer' href='" + detalle[i]['Txt_Url_Link_Pagina_Producto'] + "' role='button'>" + detalle[i]['Txt_Url_Link_Pagina_Producto'] + "</a>" : "");
        table_enlace_producto +=
        "<tr id='tr_enlace_producto" + id_item + "'>"
          + "<td style='display:none;' class='text-left td-id_item'>" + id_item + "</td>"
          + "<td class='text-center td-name'><img style='max-height: 350px;width: 350px;' src='" + detalle[i]['Txt_Url_Imagen_Producto'] + "' alt='" + detalle[i]['Txt_Producto'] + "' class='img-thumbnail img-fluid mb-2'></td>"
          + "<td class='text-left td-name'>" + detalle[i]['Txt_Producto'] + "</td>"
          + "<td class='text-left td-name'>" + detalle[i]['Txt_Descripcion'] + "</td>"
          + "<td class='text-right td-cantidad'>" + Math.round10(detalle[i]['Qt_Producto'], -2) + "</td>"
          + "<td class='text-left td-name'>" + href_link + "</td>"
          //+ "<td class='text-center'><button type='button' id='btn-deleteProductoEnlace' class='btn btn-xs btn-link text-danger' alt='Eliminar' title='Eliminar'><i class='fas fa-trash-alt fa-2x' aria-hidden='true'></i></button></td>";
          table_enlace_producto += '<input type="hidden" name="addProducto[' + id_item + '][nombre_comercial]" value="' + detalle[i]['Txt_Producto'] + '">';
          table_enlace_producto += '<input type="hidden" name="addProducto[' + id_item + '][caracteristicas]" value="' + detalle[i]['Txt_Descripcion'] + '">';
        table_enlace_producto += "</tr>";
      }
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

function cambiarEstado(ID, Nu_Estado, sNombreEstado) {
  var $modal_delete = $('#modal-message-delete');
  $modal_delete.modal('show');

  $('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
  $('.modal-message-delete').addClass('modal-success');

  $('#modal-title').text('¿Deseas cambiar estado?');

  $('#btn-cancel-delete').off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $('#btn-save-delete').off('click').click(function () {
    $('#modal-loader').modal('show');

    url = base_url + 'AgenteCompra/PedidosAgente/cambiarEstado/' + ID + '/' + Nu_Estado;
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
    
    url = base_url + 'AgenteCompra/PedidosAgente/crudPedidoGrupal';
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

function generarPDFPedidoCliente(ID){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '.modal-message-delete' ).removeClass('modal-danger modal-warning modal-success');
  $( '.modal-message-delete' ).addClass('modal-success');

  $('#modal-title').text('¿Deseas genera PDF?');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });
  
  $( '#btn-save-delete' ).off('click').click(function () {
    _generarPDFPedidoCliente($modal_delete, ID);
  });
}

function _generarPDFPedidoCliente($modal_delete, ID){
  $modal_delete.modal('hide');
  url = base_url + 'AgenteCompra/PedidosAgente/generarPDFPedidoCliente/' + ID;
  window.open(url,'_blank');
}

function generarExcelPedidoCliente(ID){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '.modal-message-delete' ).removeClass('modal-danger modal-warning modal-success');
  $( '.modal-message-delete' ).addClass('modal-success');

  $('#modal-title').text('¿Deseas genera EXCEL?');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });
  
  $( '#btn-save-delete' ).off('click').click(function () {
    _generarExcelPedidoCliente($modal_delete, ID);
  });
}

function _generarExcelPedidoCliente($modal_delete, ID){
  $modal_delete.modal('hide');
  url = base_url + 'AgenteCompra/PedidosAgente/generarExcelPedidoCliente/' + ID;
  window.open(url,'_blank');
}