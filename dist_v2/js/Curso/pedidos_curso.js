var url, table_Entidad, div_items = '', iCounter = 1;
//AUTOCOMPLETE
var caractes_no_validos_global_autocomplete = "\"'~!@%^\|";
// Se puede crear un arreglo a partir de la cadena
let search_global_autocomplete = caractes_no_validos_global_autocomplete.split('');
// Solo tomé algunos caracteres, completa el arreglo
let replace_global_autocomplete = ['', '', '', '', '', '', '', '', ''];
//28 caracteres
// FIN AUTOCOMPLETE

var fToday = new Date(), fYear = fToday.getFullYear(), fMonth = fToday.getMonth() + 1, fDay = fToday.getDate();

$(function () {
  //Date picker invoice
  $( '.input-report' ).datepicker({
    autoclose : true,
    //startDate : new Date(fYear, fToday.getMonth(), '01'),
    todayHighlight  : true,
    dateFormat: 'dd/mm/yyyy',
    format: 'dd/mm/yyyy',
  });

  $(document).on('click', '.btn-quitar_item', function (e) {
    e.preventDefault();
    //alert($(this).data('id'));
    $('#card' + $(this).data('id')).remove();
	})

  $(document).on('click', '#btn-add_item', function (e) {
    e.preventDefault();
    addItems();

    $( '.div-articulos' ).show();
    //$('#div-button-add_item').removeClass('mt-2');
    //$('#div-button-add_item').addClass('mt-4');
  })

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

  url = base_url + 'Curso/PedidosCurso/ajax_list';
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
    'searching'   : false,
    'bStateSave'  : true,
    'processing'  : true,
    'serverSide'  : true,
    'info'        : true,
    'autoWidth'   : false,
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
        data.sMethod = $('#hidden-sMethod').val(),
        data.Filtros_Entidades = $( '#cbo-Filtros_Entidades' ).val(),
        data.Global_Filter = $( '#txt-Global_Filter' ).val(),
        data.Filtro_Fe_Inicio       = ParseDateString($( '#txt-Fe_Inicio' ).val(), 'fecha', '/'),
        data.Filtro_Fe_Fin          = ParseDateString($( '#txt-Fe_Fin' ).val(), 'fecha', '/');
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

  $('#btn-html_reporte').click(function () {
    reload_table_Entidad();
  });

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

    //$row->Txt_Url_Imagen_Producto = str_replace("https://", "../../", $row->Txt_Url_Imagen_Producto);
    //$row->Txt_Url_Imagen_Producto = str_replace("assets","public_html/assets", $row->Txt_Url_Imagen_Producto);

    /*
    var img_item = $(this).data('url_img');
    img_item = img_item.replace("https://", "../../");
    img_item = img_item.replace("assets", "public_html/assets");

    $("#a-download_image").attr("href", img_item);
    */
  })
  
	$( '#a-download_image' ).click(function(){
    id = $(this).data('id_item');
    url = base_url + 'Curso/PedidosCurso/downloadImage/' + id;
    
    var popupwin = window.open(url);
    setTimeout(function() { popupwin.close();}, 2000);
  })

  $('#span-id_pedido').html('');
})

function reload_table_Entidad(){
  table_Entidad.ajax.reload(null,false);
}

function verPedido(ID){
  $( '.div-articulos' ).hide();
  $( '.div-Listar' ).hide();
  
  $('#div-arrItems').html('');

  $( '#form-pedido' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();

	$( '#table-Producto_Enlace tbody' ).empty();
  $( '#table-Producto_Enlace' ).show();

  $('#span-id_pedido').html('Nro. ' + ID);

  url = base_url + 'Curso/PedidosCurso/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      var detalle = response;
      response = response[0];

      $( '.div-AgregarEditar' ).show();
            
      $( '[name="EID_Pedido_Curso"]' ).val(response.ID_Pedido_Curso);
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
        var cantidad_item = detalle[i]['Qt_Producto'];
        var id_item = detalle[i]['ID_Pedido_Detalle'];
        var href_link = (detalle[i]['Txt_Url_Link_Pagina_Producto'] != '' && detalle[i]['Txt_Url_Link_Pagina_Producto'] != null ? "<a class='btn btn-link p-0 m-0' target='_blank' rel='noopener noreferrer' href='" + detalle[i]['Txt_Url_Link_Pagina_Producto'] + "' role='button'>Link</a>" : "");
        table_enlace_producto +=
        "<tr id='tr_enlace_producto" + id_item + "'>"
          + "<td style='display:none;' class='text-left td-id_item'>" + id_item + "</td>"
          + "<td class='text-center td-name' width='50%'>";
          
            table_enlace_producto += "<h6 class='font-weight-bold font-medium'>" + detalle[i]['Txt_Producto'] + "</h6>";
            
            if(!isNaN(cantidad_item) && cantidad_item > 0 && cantidad_item!=''){
              //table_enlace_producto += "<span class='mt-3'>Cantidad: </span><span class='font-weight-bold'>" + Math.round10(cantidad_item, -2) + "</span><br>";
              table_enlace_producto += '<div class="row">';
                table_enlace_producto += '<div class="col text-right">';
                  table_enlace_producto += "<span class='mt-3'>Cantidad</span>";
                table_enlace_producto += '</div>';
                table_enlace_producto += '<div class="col">';
                  table_enlace_producto += '<input type="hidden" name="addProductoTable[' + id_item + '][id_item]" value="' + id_item + '">';
                  table_enlace_producto += '<input type="text" inputmode="decimal" class="form-control input-decimal" name="addProductoTable[' + id_item + '][cantidad]" value="' + Math.round10(cantidad_item, -2) + '"><br>';
                table_enlace_producto += '</div>';
              table_enlace_producto += '</div>';
            }

            table_enlace_producto += "<img data-id_item='" + id_item + "' data-url_img='" + detalle[i]['Txt_Url_Imagen_Producto'] + "' src='" + detalle[i]['Txt_Url_Imagen_Producto'] + "' alt='" + detalle[i]['Txt_Producto'] + "' class='img-thumbnail img-table_item img-fluid img-resize mb-2'>";
            
          table_enlace_producto += "</td>";
          //+ "<td class='text-left td-name' width='20%'>" + detalle[i]['Txt_Producto'] + "</td>"
          table_enlace_producto += "<td class='text-left td-name' width='20%'>";
          table_enlace_producto += '<textarea class="form-control" placeholder="" name="addProductoTable[' + id_item + '][caracteristicas]" style="height: 200px;">' + clearHTMLTextArea(detalle[i]['Txt_Descripcion']) + '</textarea>';
          table_enlace_producto += "</td>";
          //+ "<td class='text-left td-name' width='20%'>" + detalle[i]['Txt_Descripcion'] + "</td>"
          table_enlace_producto += "<td class='text-left td-name' width='10%'>" + href_link + "</td>";
          //table_enlace_producto += '<input type="hidden" name="addProducto[' + id_item + '][nombre_comercial]" value="' + detalle[i]['Txt_Producto'] + '">';
          //table_enlace_producto += '<input type="hidden" name="addProducto[' + id_item + '][caracteristicas]" value="' + detalle[i]['Txt_Descripcion'] + '">';
        table_enlace_producto += "</tr>";
      }
      
      $('#span-total_cantidad_items').html(i);
      $( '#table-Producto_Enlace' ).append(table_enlace_producto);
      
      validateDecimal();
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

function cambiarEstado(ID, Nu_Estado, id_correlativo, id_usuario_pedido) {
  if(Nu_Estado==2 && id_usuario_pedido==0){
    $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
    $('#modal-message').modal('show');

    $('#moda-message-content').addClass( 'bg-danger' );
    $('.modal-title-message').text('Primero asignar pedido a cliente');

    setTimeout(function () { $('#modal-message').modal('hide'); }, 3100);
    return 0;
  }

  var $modal_delete = $('#modal-message-delete');
  $modal_delete.modal('show');

  $('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
  $('.modal-message-delete').addClass('modal-success');

  var sNombreEstado = 'Pendiente';
  if(Nu_Estado==2)
    sNombreEstado = 'Garantizado';

  $('#modal-title').html('¿Deseas cambiar estado a <strong>' + sNombreEstado + '</strong>?');

  $('#btn-cancel-delete').off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $('#btn-save-delete').off('click').click(function () {
    
    $( '#btn-save-delete' ).text('');
    $( '#btn-save-delete' ).attr('disabled', true);
    $( '#btn-save-delete' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

    url = base_url + 'Curso/PedidosCurso/cambiarEstado/' + ID + '/' + Nu_Estado + '/' + id_correlativo;
    $.ajax({
      url: url,
      type: "GET",
      dataType: "JSON",
      success: function (response) {
        $modal_delete.modal('hide');

        $( '#btn-save-delete' ).text('');
        $( '#btn-save-delete' ).append( 'Aceptar' );
        $( '#btn-save-delete' ).attr('disabled', false);

        $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
        $('#modal-message').modal('show');

        if (response.status == 'success') {
          $('#moda-message-content').addClass( 'bg-' + response.status);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
          reload_table_Entidad();
        } else {
          $('#moda-message-content').addClass( 'bg-danger' );
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
    
    var postData = new FormData($("#form-pedido")[0]);
    //$('#form-pedido').serialize(),

    url = base_url + 'Curso/PedidosCurso/crudPedidoGrupal';
    $.ajax({
      type		    : 'POST',
      dataType	  : 'JSON',
  		url		      : url,
  		data		    : postData,
      mimeType    : "multipart/form-data",
      contentType : false,
      cache       : false,
      processData : false,
      success : function( response ){
        $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
        $('#modal-message').modal('show');
        
        if (response.status == 'success'){
          $( '#form-pedido' )[0].reset();
          
          $( '.div-AgregarEditar' ).hide();
          $( '.div-Listar' ).show();
          $('#moda-message-content').addClass( 'bg-' + response.status);
          $('.modal-title-message').text(response.message);
          setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
          reload_table_Entidad();
        } else {
          $('#moda-message-content').addClass( 'bg-danger' );
          $('.modal-title-message').text(response.message);
          setTimeout(function() {$('#modal-message').modal('hide');}, 2100);
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
  url = base_url + 'Curso/PedidosCurso/generarPDFPedidoCliente/' + ID;
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
  url = base_url + 'Curso/PedidosCurso/generarExcelPedidoCliente/' + ID;
  window.open(url,'_blank');
}

function asignarPedido(ID) {
  var $modal_delete = $('#modal-message-delete');
  $modal_delete.modal('show');

  $('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
  $('.modal-message-delete').addClass('modal-success');

  $('#modal-title').text('¿Deseas asignar Nro. Pedido ' + ID + ' ?');

  $('#btn-save-delete').off('click').click(function () {
    
    $( '#btn-save-delete' ).text('');
    $( '#btn-save-delete' ).attr('disabled', true);
    $( '#btn-save-delete' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

    url = base_url + 'Curso/PedidosCurso/asignarPedido/' + ID;
    $.ajax({
      url: url,
      type: "GET",
      dataType: "JSON",
      success: function (response) {
        $modal_delete.modal('hide');
        $( '#btn-save-delete' ).text('');
        $( '#btn-save-delete' ).append( 'Aceptar' );
        $( '#btn-save-delete' ).attr('disabled', false);

        $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
        $('#modal-message').modal('show');

        if (response.status == 'success') {
          $('#moda-message-content').addClass( 'bg-' + response.status);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
          reload_table_Entidad();
        } else {
          $('#moda-message-content').addClass( 'bg-danger' );
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 2100);
        }
      }
    });
  });
}

function removerAsignarPedido(ID, id_usuario) {
  var $modal_delete = $('#modal-message-delete');
  $modal_delete.modal('show');

  $('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
  $('.modal-message-delete').addClass('modal-success');

  $('#modal-title').text('¿Deseas quitar asignación Nro. Pedido ' + ID + ' ?');

  $('#btn-save-delete').off('click').click(function () {
    
    $( '#btn-save-delete' ).text('');
    $( '#btn-save-delete' ).attr('disabled', true);
    $( '#btn-save-delete' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

    url = base_url + 'Curso/PedidosCurso/removerAsignarPedido/' + ID + '/' + id_usuario;
    $.ajax({
      url: url,
      type: "GET",
      dataType: "JSON",
      success: function (response) {
        $modal_delete.modal('hide');
        $( '#btn-save-delete' ).text('');
        $( '#btn-save-delete' ).append( 'Aceptar' );
        $( '#btn-save-delete' ).attr('disabled', false);

        $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
        $('#modal-message').modal('show');

        if (response.status == 'success') {
          $('#moda-message-content').addClass( 'bg-' + response.status);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
          reload_table_Entidad();
        } else {
          $('#moda-message-content').addClass( 'bg-danger' );
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 2100);
        }
      }
    });
  });
}

function addItems(){
  div_items = '';

  //visible para mostrar a publico
  div_items += '<div id="card' + iCounter + '" class="card border-0 rounded shadow mt-3">';
    div_items += '<div class="row">';
      div_items += '<div class="col-sm-4 position-relative text-center ps-4 pe-3 pe-sm-0">';
        div_items += '<div class="col-sm-12">';
          div_items += '<h6 class="text-left card-title pt-3" style="text-align: left;">';
            div_items += '<label class="fw-bold">Imagen</label>';
          div_items += '</h6>';
          div_items += '<div class="form-group">';
            div_items += '<label class="btn btn btn-outline-secondary" for="voucher' + iCounter + '" style="width: 100%;">';
              div_items += '<input class="arrProducto form-control voucher" id="voucher' + iCounter + '" type="file" style="display:none" name="voucher[]" data-id="' + iCounter + '" onchange="loadFile(event, ' + iCounter + ')" placeholder="sin archivo" accept="image/*">Agregar foto';
            div_items += '</label>';
            div_items += '<span class="help-block text-danger" id="error"></span>';
          div_items += '</div>';
        div_items += '</div>';
        div_items += '<img id="img_producto-preview' + iCounter + '" src="" class="arrProducto img-thumbnail border-0 rounded" alt="">'; //cart-size-img
      div_items += '</div>';
    
      div_items += '<div class="col-sm-8">';
        div_items += '<div class="card-body pb-0">';
          div_items += '<div class="row">';
            div_items += '<div class="col-sm-12 mb-3">';
              div_items += '<h6 class="card-title">';
                div_items += '<label class="fw-bold">Nombre Comercial</label>';
              div_items += '</h6>';
              div_items += '<input type="text" inputmode="text" id="modal-nombre_comercial' + iCounter + '" name="addProducto[' + iCounter + '][nombre_comercial]" class="arrProducto form-control" placeholder="" maxlength="255" autocomplete="off">';
            div_items += '</div>';
            
            div_items += '<div class="col-sm-12 mb-0">';
              div_items += '<h6 class="card-title">';
                div_items += '<label class="fw-bold">Características</label>';
              div_items += '</h6>';
              div_items += '<div class="form-group">';
                div_items += '<textarea class="arrProducto form-control required caracteristicas" placeholder="" id="modal-caracteristicas' + iCounter + '" name="addProducto[' + iCounter + '][caracteristicas]" style="height: 100px"></textarea>';
                div_items += '<span class="help-block text-danger" id="error"></span>';
              div_items += '</div>';
            div_items += '</div>';
            
            div_items += '<div class="col-12 col-sm-3 col-md-3 col-lg-2 mb-0">';
              div_items += '<h6 class="card-title">';
                div_items += '<label class="fw-bold">Cantidad</label>';
              div_items += '</h6>';
              div_items += '<div class="form-group">';
                div_items += '<input type="text" id="modal-cantidad' + iCounter + '" inputmode="decimal" name="addProducto[' + iCounter + '][cantidad]" class="arrProducto form-control cantidad input-decimal" placeholder="" value="" autocomplete="off">';
                div_items += '<span class="help-block text-danger" id="error"></span>';
              div_items += '</div>';
            div_items += '</div>';
            
            div_items += '<div class="col-12 col-sm-9 col-md-9 col-lg-10 mb-0">';
              div_items += '<h6 class="card-title">';
                div_items += '<label class="fw-bold">Link</label>';
              div_items += '</h6>';
              div_items += '<div class="form-group">';
                div_items += '<input type="text" inputmode="url" id="modal-link' + iCounter + '" name="addProducto[' + iCounter + '][link]" class="arrProducto form-control link" placeholder="" autocomplete="off" autocapitalize="none">';
                div_items += '<span class="help-block text-danger" id="error"></span>';
              div_items += '</div>';
            div_items += '</div>';
          div_items += '</div>';
        div_items += '</div>';
      div_items += '</div>';

      div_items += '<div class="col-sm-12 ps-4 mb-3 pe-4">';
        div_items += '<div class="d-grid gap">';
          div_items += '<button type="button" id="btn-quitar_item_'+iCounter+'" class="btn btn-outline-danger btn-quitar_item col" data-id="'+iCounter+'">Quitar</button>';
        div_items += '</div>';
      div_items += '</div>';
    div_items += '</div>';
  div_items += '</div>';

  $( '#div-arrItems' ).append(div_items);
  
  validateNumberLetter();
  validateDecimal();

  ++iCounter;
}

function loadFile(event, id){
  var output = document.getElementById('img_producto-preview' + id);
  output.src = URL.createObjectURL(event.target.files[0]);
  output.onload = function() {
    URL.revokeObjectURL(output.src) // free memory
  }

  window.mobileCheck = function() {
    let check = false;
    (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
    return check;
  };

  //if(iOS==true && window.mobileCheck()==true){
  if(window.mobileCheck()==true){
    scrollToIOS($("html, body"), $('#modal-nombre_comercial' + id));
  }

  $('#modal-nombre_comercial' + id).focus();
  $('#modal-nombre_comercial' + id).select();
}

function iOS() {
  return [
    'iPad Simulator',
    'iPhone Simulator',
    'iPod Simulator',
    'iPad',
    'iPhone',
    'iPod'
  ].includes(navigator.platform)
  // iPad on iOS 13 detection
  || (navigator.userAgent.includes("Mac") && "ontouchend" in document)
}

function scrollToError( $sMetodo, $IdElemento ){
  $sMetodo.animate({
    scrollTop: $IdElemento.offset().top - 100
  }, 'slow');
}

function scrollToErrorHTML( $sMetodo, $IdElemento ){
  $sMetodo.animate({
    scrollTop: $IdElemento.offset().top + 450
  }, 'slow');
}

function scrollToIOS( $sMetodo, $IdElemento ){
  $sMetodo.animate({
    scrollTop: $IdElemento.offset().top
  }, 'slow');
}

function clearHTMLTextArea(str){
  str=str.replace(/<br>/gi, "");
  str=str.replace(/<br\s\/>/gi, "");
  str=str.replace(/<br\/>/gi, "");
  str=str.replace(/<\/button>/gi, "");
  str=str.replace(/<br >/gi, "");
  return str;
}