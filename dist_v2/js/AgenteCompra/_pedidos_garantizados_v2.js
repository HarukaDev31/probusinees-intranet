var url, table_Entidad, div_items = '', iCounterItems = 1;
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

  url = base_url + 'AgenteCompra/PedidosGarantizados/ajax_list';
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
    url = base_url + 'AgenteCompra/PedidosGarantizados/downloadImage/' + id;
    
    var popupwin = window.open(url);
    setTimeout(function() { popupwin.close();}, 2000);
  })

  $('#span-id_pedido').html('');
  
  $('#div-add_item_proveedor').hide();
	$(document).on('click', '.btn-add_proveedor', function (e) {
    e.preventDefault();
    
    //console.log( 'id_empresa > ' + $(this).data('id_empresa'));
    //console.log( 'id_organizacion > ' + $(this).data('id_organizacion'));
    //console.log( 'id_pedido_cabecera > ' + $(this).data('id_pedido_cabecera'));
    //console.log( 'id_pedido_detalle > ' + $(this).data('id_pedido_detalle'));
    
    $('#div-arrItems').html('');

    $('.div-Listar').hide();
    $('.div-AgregarEditar').hide();
    $('#div-add_item_proveedor').show();

    $('#modal-precio1').focus();

    iCounterItems=1;
    addItems();

    $( '#txt-EID_Empresa_item' ).val($(this).data('id_empresa'));
    $( '#txt-EID_Organizacion_item' ).val($(this).data('id_organizacion'));
    $( '#txt-EID_Pedido_Cabecera_item' ).val($(this).data('id_pedido_cabecera'));
    $( '#txt-EID_Pedido_Detalle_item' ).val($(this).data('id_pedido_detalle'));
  })
  
  $('#div-elegir_item_proveedor').hide();
	$(document).on('click', '.btn-elegir_proveedor', function (e) {
    e.preventDefault();

    $('.div-Listar').hide();
    $('.div-AgregarEditar').hide();
    $('#div-elegir_item_proveedor').show();

	  $( '#table-elegir_productos_proveedor tbody' ).empty();

    var id = $(this).data('id_pedido_cabecera');
    var id_detalle = $(this).data('id_pedido_detalle');

    $( '#txt-EID_Empresa_item' ).val($(this).data('id_empresa'));
    $( '#txt-EID_Organizacion_item' ).val($(this).data('id_organizacion'));
    $( '#txt-EID_Pedido_Cabecera_item' ).val(id);
    $( '#txt-EID_Pedido_Detalle_item' ).val(id_detalle);

    url = base_url + 'AgenteCompra/PedidosGarantizados/getItemProveedor/' + id_detalle;
    $.ajax({
      url : url,
      type: "GET",
      dataType: "JSON",
      success: function(response){
        var detalle = response;

        console.log(detalle);
        var table_enlace_producto = "", id_item_tmp = 0;
        for (i = 0; i < detalle.length; i++) {
          var id_item = detalle[i]['ID_Pedido_Detalle_Producto_Proveedor'];

          if(id_item_tmp != id_item){
            table_enlace_producto +=
            "<tr id='tr_enlace_producto" + id_item + "'>"
              + "<td style='display:none;' class='text-left td-id_item'>" + id_item + "</td>"
              + "<td class='text-left td-imagen'>";

              /*
              console.log('entrando > ');
              url = base_url + 'AgenteCompra/PedidosGarantizados/getItemImagenProveedor/' + id_item;
              $.ajax({
                url : url,
                type: "GET",
                dataType: "JSON",
                success: function(response_image){
                  for (x = 0; x < response_image.length; x++) {
                    console.log('pinto');
                    console.log(response_image[x]);
                    table_enlace_producto += "hola";
                    table_enlace_producto += "<img style='max-height: 350px;width: 100%; cursor:pointer' data-id_item='" + id_item + "' data-url_img='" + response_image[x]['Txt_Url_Imagen_Producto'] + "' src='" + response_image[x]['Txt_Url_Imagen_Producto'] + "' class='img-thumbnail img-table_item img-fluid mb-2'>";
                    table_enlace_producto += "<br>";
                  }
                  console.log('pinto si');
                }
              })
              */
              table_enlace_producto += "</td>";

              table_enlace_producto +=
              "<td class='text-left td-precio'>" + detalle[i]['Ss_Precio'] + "</td>"
              + "<td class='text-left td-moq'>" + detalle[i]['Qt_Producto_Moq'] + "</td>"
              + "<td class='text-left td-caja'>" + detalle[i]['Qt_Producto_Caja'] + "</td>"
              + "<td class='text-left td-cbm'>" + detalle[i]['Qt_Cbm'] + "</td>"
              + "<td class='text-left td-delivery'>" + detalle[i]['Nu_Dias_Delivery'] + "</td>"
              + "<td class='text-left td-nota'>" + detalle[i]['Txt_Nota'] + "</td>"
            table_enlace_producto += "</tr>";
            id_item_tmp = id_item;
          }
        }
        
        $( '#table-elegir_productos_proveedor' ).append(table_enlace_producto);
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
  })
  
  $(document).on('click', '.btn-quitar_item', function (e) {
    e.preventDefault();
    //alert($(this).data('id'));
    $('#card' + $(this).data('id')).remove();
	})
  
  $(document).on('click', '#btn-add_item', function (e) {
    e.preventDefault();
    addItems();

    $('#div-button-add_item').removeClass('mt-2');
    $('#div-button-add_item').addClass('mt-0');
  })
  
  $(document).on('click', '#btn-cancel_detalle_item_proveedor', function (e) {
    e.preventDefault();
    
    $('.div-Listar').hide();
    $('.div-AgregarEditar').show();
    $('#div-add_item_proveedor').hide();
  })
  
  $(document).on('click', '#btn-cancel_detalle_elegir_proveedor', function (e) {
    e.preventDefault();
    
    $('.div-Listar').hide();
    $('.div-AgregarEditar').show();
    $('#div-elegir_item_proveedor').hide();
  })

  $("#form-arrItems").on('submit',function(e){
    e.preventDefault();

    $('.help-block').empty();
    $('.form-group').removeClass('has-error');

    //validacion de articulos
    var sEstadoArticulos = true;
    $("#form-arrItems").find(':input').each(function () {
      var elemento = this;

      if(elemento.dataset.correlativo!==undefined){
        if (elemento.classList[0]=='arrProducto'){
          if(elemento.type=='text'){
            if (elemento.classList[2]=='required'){
              if(
                (elemento.classList[3]=='precio' || elemento.classList[3]=='moq' || elemento.classList[3]=='qty_caja' || elemento.classList[3]=='cbm')
                && (isNaN(parseFloat($('#' + elemento.id).val())) || parseFloat($('#' + elemento.id).val()) < 0.00)
              ){
                $('#' + elemento.id).closest('.form-group').find('.help-block').html('Ingresar ' + elemento.classList[3]);
                $('#' + elemento.id).closest('.form-group').removeClass('has-success').addClass('has-error');

                scrollToError($("html, body"), $('#' + elemento.id));

								$('#' + elemento.id).focus();
								$('#' + elemento.id).select();
								setTimeout(function () {
									$('#' + elemento.id).focus(); $('#' + elemento.id).select();
								}, 30);

                sEstadoArticulos = false;
                return false;
              }
            }
          }
        }
      }
    });
    //validacion de articulos
    
    if(sEstadoArticulos==true) {
      //$('#btn-save_detalle_item_proveedor').prop('disabled', true);
      //$('#btn-save_detalle_item_proveedor').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enviando');

      var postData = new FormData($("#form-arrItems")[0]);
      console.log(postData);
      $.ajax({
        url: base_url + 'AgenteCompra/PedidosGarantizados/addPedidoItemProveedor',
        type: "POST",
        dataType: "JSON",
        data: postData,
        processData: false,
        contentType: false
      })
      .done(function(response) {
        //$('#btn-save_detalle_item_proveedor').prop('disabled', false);
        //$('#btn-save_detalle_item_proveedor').html('Guardar');

        console.log(response);
        if(response.status=='success'){
          alert(response.message);

          $('.div-Listar').hide();
          $('.div-AgregarEditar').show();
          $('#div-add_item_proveedor').hide();
        } else {
          alert(response.message);
        }
      });
    }
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

  $('#span-id_pedido').html('Nro. ' + ID);

  url = base_url + 'AgenteCompra/PedidosGarantizados/ajax_edit/' + ID;
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
      $( '[name="Ss_Tipo_Cambio"]' ).val(response.Ss_Tipo_Cambio);

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
        var href_link = (detalle[i]['Txt_Url_Link_Pagina_Producto'] != '' && detalle[i]['Txt_Url_Link_Pagina_Producto'] != null ? "<a class='btn btn-link p-0 m-0' target='_blank' rel='noopener noreferrer' href='" + detalle[i]['Txt_Url_Link_Pagina_Producto'] + "' role='button'>" + detalle[i]['Txt_Url_Link_Pagina_Producto'] + "</a>" : "");
        table_enlace_producto +=
        "<tr id='tr_enlace_producto" + id_item + "'>"
          + "<td style='display:none;' class='text-left td-id_item'>" + id_item + "</td>"
          + "<td class='text-center td-name' width='50%'>"
            + "<img style='max-height: 350px;width: 100%; cursor:pointer' data-id_item='" + id_item + "' data-url_img='" + detalle[i]['Txt_Url_Imagen_Producto'] + "' src='" + detalle[i]['Txt_Url_Imagen_Producto'] + "' alt='" + detalle[i]['Txt_Producto'] + "' class='img-thumbnail img-table_item img-fluid mb-2'>";
            if(!isNaN(cantidad_item) && cantidad_item > 0 && cantidad_item!=''){
              table_enlace_producto += "<span class='mt-3'>Cantidad: </span><span class='font-weight-bold'>" + Math.round10(cantidad_item, -2) + "</span>"
            }
            if(response.Nu_Estado_China!=3) {//cotizacio china
              table_enlace_producto += '<button type="button" id="btn-add_proveedor' + id_item + '" data-id_empresa="' + response.ID_Empresa + '" data-id_organizacion="' + response.ID_Organizacion + '" data-id_pedido_cabecera="' + response.ID_Pedido_Cabecera + '" data-id_pedido_detalle="' + id_item + '" class="btn btn-danger btn-block btn-add_proveedor"><i class="fas fa-plus-square"></i>&nbsp; Proveedor</button>';
            } else {
              table_enlace_producto += '<button type="button" id="btn-elegir_proveedor' + id_item + '" data-id_empresa="' + response.ID_Empresa + '" data-id_organizacion="' + response.ID_Organizacion + '" data-id_pedido_cabecera="' + response.ID_Pedido_Cabecera + '" data-id_pedido_detalle="' + id_item + '" class="btn btn-success btn-block btn-elegir_proveedor"><i class="fas fa-check"></i>&nbsp; Elegir proveedor</button>';
            }
          table_enlace_producto += "</td>"
          + "<td class='text-left td-name' width='20%'>" + detalle[i]['Txt_Producto'] + "</td>"
          + "<td class='text-left td-name' width='20%'>" + detalle[i]['Txt_Descripcion'] + "</td>"
          //+ "<td class='text-right td-cantidad'>" + Math.round10(detalle[i]['Qt_Producto'], -2) + "</td>"
          + "<td class='text-left td-name' width='10%'>" + href_link + "</td>"
          //+ "<td class='text-center'><button type='button' id='btn-deleteProductoEnlace' class='btn btn-xs btn-link text-danger' alt='Eliminar' title='Eliminar'><i class='fas fa-trash-alt fa-2x' aria-hidden='true'></i></button></td>";
          table_enlace_producto += '<input type="hidden" name="addProducto[' + id_item + '][nombre_comercial]" value="' + detalle[i]['Txt_Producto'] + '">';
          table_enlace_producto += '<input type="hidden" name="addProducto[' + id_item + '][caracteristicas]" value="' + detalle[i]['Txt_Descripcion'] + '">';
        table_enlace_producto += "</tr>";
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

function cambiarEstado(ID, Nu_Estado) {
  var $modal_delete = $('#modal-message-delete');
  $modal_delete.modal('show');

  $('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
  $('.modal-message-delete').addClass('modal-success');

  var sNombreEstado = 'Garantizado';
  if(Nu_Estado==2)
    sNombreEstado = 'Enviado';
  else if(Nu_Estado==2)
    sNombreEstado = 'Rechazado';
  else if(Nu_Estado==2)
    sNombreEstado = 'Confirmado';

  $('#modal-title').text('¿Deseas cambiar estado a ' + sNombreEstado + '?');

  $('#btn-cancel-delete').off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $('#btn-save-delete').off('click').click(function () {
    
    $( '#btn-save-delete' ).text('');
    $( '#btn-save-delete' ).attr('disabled', true);
    $( '#btn-save-delete' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

    url = base_url + 'AgenteCompra/PedidosGarantizados/cambiarEstado/' + ID + '/' + Nu_Estado;
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
    
    url = base_url + 'AgenteCompra/PedidosGarantizados/crudPedidoGrupal';
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
  url = base_url + 'AgenteCompra/PedidosGarantizados/generarPDFPedidoCliente/' + ID;
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
  
  /*
  $('.div-agregar_proveedor').show();
  $( '.btn-subir_archivos' ).off('click').click(function () {
    $('.div-agregar_proveedor').hide();
  });
  */
  
  $( '#btn-save-delete' ).off('click').click(function () {
    _generarExcelPedidoCliente($modal_delete, ID);
  });
}

function _generarExcelPedidoCliente($modal_delete, ID){
  $modal_delete.modal('hide');
  url = base_url + 'AgenteCompra/PedidosGarantizados/generarExcelPedidoCliente/' + ID;
  window.open(url,'_blank');
}

function loadFile(event, id){
  /*
  var output = document.getElementById('img_producto-preview' + id);
  output.src = URL.createObjectURL(event.target.files[0]);
  output.onload = function() {
    URL.revokeObjectURL(output.src) // free memory
  }
  */
}

function addItems(){
  div_items = '';

  div_items += '<div id="card' + iCounterItems + '" class="card border-0 rounded shadow-sm mt-3">';
    div_items += '<div class="row">';
      div_items += '<div class="col-sm-12">';
      div_items += '<div class="card-body">';
      div_items += '<div class="row">';
        div_items += '<div class="col-11 col-sm-11 col-md-11 col-lg-11 mb-0 mb-sm-0">';
          div_items += '<h6 class="text-left card-title mb-2 pt-3" style="text-align: left;">';
            div_items += '<span class="fw-bold" style="font-weight: bold;">Imagen<span class="label-advertencia text-danger"> *</span></span>';
          div_items += '</h6>';
          div_items += '<div class="form-group">';
            div_items += '<input class="form-control" name="voucher[' + iCounterItems + '][]" type="file" accept="image/*" multiple></input>';
            //div_items += '<input class="form-control" name="addProducto[' + iCounterItems + '][voucher][]" type="file" accept="image/*" multiple></input>';
            div_items += '<span class="help-block text-danger" id="error"></span>';
          div_items += '</div>';
        div_items += '</div>';
        
        div_items += '<div class="col-1 col-sm-1 col-md-1 col-lg-1">';
          div_items += '<div class="d-grid gap"><button type="button" id="btn-quitar_item_' + iCounterItems + '" class="btn btn-outline-danger btn-quitar_item col" data-id="' + iCounterItems + '">X</div>';
        div_items += '</div>';

        div_items += '<div class="col-6 col-sm-3 col-md-3 col-lg-2 mb-0 mb-sm-0">';
          div_items += '<h6 class="card-title mb-2" style="font-weight:bold">';
          div_items += '<span class="fw-bold">Precio<span class="label-advertencia text-danger"> *</span></span>';
          div_items += '</h6>';
          div_items += '<div class="form-group">';
            div_items += '<input type="text" id="modal-precio' + iCounterItems + '" data-correlativo="' + iCounterItems + '" inputmode="decimal" name="addProducto[' + iCounterItems + '][precio]" class="arrProducto form-control required precio input-decimal" placeholder="" value="" autocomplete="off" />';
            div_items += '<span class="help-block text-danger" id="error"></span>';
          div_items += '</div>';
        div_items += '</div>';
                
      div_items += '<div class="col-6 col-sm-3 col-md-3 col-lg-2 mb-0 mb-sm-0">';
      div_items += '<h6 class="card-title mb-2" style="font-weight:bold">';
      div_items += '<span class="fw-bold">moq<span class="label-advertencia text-danger"> *</span></span>';
      div_items += '</h6>';
      div_items += '<div class="form-group">';
      div_items += '<input type="text" id="modal-moq' + iCounterItems + '" data-correlativo="' + iCounterItems + '" inputmode="decimal" name="addProducto[' + iCounterItems + '][moq]" class="arrProducto form-control required moq input-decimal" placeholder="" value="" autocomplete="off" />';
      div_items += '<span class="help-block text-danger" id="error"></span>';
      div_items += '</div>';
      div_items += '</div>';
                
      div_items += '<div class="col-6 col-sm-3 col-md-3 col-lg-2 mb-0 mb-sm-0">';
      div_items += '<h6 class="card-title mb-2" style="font-weight:bold">';
      div_items += '<span class="fw-bold">qty_caja<span class="label-advertencia text-danger"> *</span></span>';
      div_items += '</h6>';
      div_items += '<div class="form-group">';
      div_items += '<input type="text" id="modal-qty_caja' + iCounterItems + '" data-correlativo="' + iCounterItems + '" inputmode="decimal" name="addProducto[' + iCounterItems + '][qty_caja]" class="arrProducto form-control required qty_caja input-decimal" placeholder="" value="" autocomplete="off" />';
      div_items += '<span class="help-block text-danger" id="error"></span>';
      div_items += '</div>';
      div_items += '</div>';
                
      div_items += '<div class="col-6 col-sm-3 col-md-3 col-lg-2 mb-0 mb-sm-0">';
      div_items += '<h6 class="card-title mb-2" style="font-weight:bold">';
      div_items += '<span class="fw-bold">cbm<span class="label-advertencia text-danger"> *</span></span>';
      div_items += '</h6>';
      div_items += '<div class="form-group">';
      div_items += '<input type="text" id="modal-cbm' + iCounterItems + '" data-correlativo="' + iCounterItems + '" inputmode="decimal" name="addProducto[' + iCounterItems + '][cbm]" class="arrProducto form-control required input-decimal" cbm placeholder="" value="" autocomplete="off" />';
      div_items += '<span class="help-block text-danger" id="error"></span>';
      div_items += '</div>';
      div_items += '</div>';

      div_items += '<div class="col-12 col-sm-3 col-md-3 col-lg-4 mb-3 mb-sm-0">';
      div_items += '<h6 class="card-title mb-2" style="font-weight:bold">';
      div_items += '<span class="fw-bold">Delivery</span>';
      div_items += '</h6>';
      div_items += '<input type="text" inputmode="numeric" id="modal-delivery' + iCounterItems + '" name="addProducto[' + iCounterItems + '][delivery]" class="arrProducto form-control input-number" placeholder="" minlength="1" maxlength="90" autocomplete="off" />';
      div_items += '</div>';

      div_items += '<div class="col-sm-12 mb-3">';
      div_items += '<h6 class="card-title mb-2" style="font-weight:bold">';
      div_items += '<span class="fw-bold">Observaciones</span>';
      div_items += '</h6>';
      div_items += '<div class="form-group">';
      div_items += '<textarea class="arrProducto form-control required nota" placeholder="Opcional" id="modal-nota' + iCounterItems + '" name="addProducto[' + iCounterItems + '][nota]" style="height: 100px;"></textarea>';
      div_items += '<span class="help-block text-danger" id="error"></span>';
      div_items += '</div>';
      div_items += '</div>';

      div_items += '<div class="col-12 col-sm-6 mb-3">';
        div_items += '<h6 class="card-title mb-2" style="font-weight:bold">';
        div_items += '<span class="fw-bold">Nombre Provedor</span>';
        div_items += '</h6>';
        div_items += '<div class="form-group">';
        div_items += '<input type="text" inputmode="text" id="modal-contacto_proveedor' + iCounterItems + '" name="addProducto[' + iCounterItems + '][contacto_proveedor]" class="arrProducto form-control" placeholder="" maxlength="255" autocomplete="off" />';
        div_items += '<span class="help-block text-danger" id="error"></span>';
        div_items += '</div>';
      div_items += '</div>';

      div_items += '<div class="col-12 col-sm-6 mb-3">';
        div_items += '<h6 class="card-title mb-2" style="font-weight:bold">';
        div_items += '<span class="fw-bold">Foto Provedor</span>';
        div_items += '</h6>';
        div_items += '<div class="form-group">';
        div_items += '<input class="form-control" id="modal-foto_proveedor' + iCounterItems + '" name="proveedor['+iCounterItems+']" type="file" accept="image/*"></input>';
        //div_items += '<input type="text" inputmode="text" id="modal-foto_proveedor' + iCounterItems + '" name="addProducto[' + iCounterItems + '][foto_proveedor]" class="arrProducto form-control input-number" placeholder="" maxlength="255" autocomplete="off" />';
        div_items += '<span class="help-block text-danger" id="error"></span>';
        div_items += '</div>';
      div_items += '</div>';

      div_items += '</div>';
      div_items += '</div>';
      div_items += '</div>';
    div_items += '</div>';
  div_items += '</div>';

  $( '#div-arrItems' ).append(div_items);
  
  $('#modal-precio' + iCounterItems).focus();

  validateNumberLetter();
  validateDecimal();
  validateNumber();

  ++iCounterItems;
}

function validateNumberLetter(){
  $( '.input-number_letter' ).unbind();
  $( '.input-number_letter' ).on('input', function () {
    this.value = this.value.replace(/[^a-zA-Z0-9]/g,'');
  });
}

function validateDecimal(){
  $( '.input-decimal' ).unbind();
  $( '.input-decimal' ).on('input', function () {
    numero = parseFloat(this.value);
    if(!isNaN(numero)){
      this.value = this.value.replace(/[^0-9\.]/g,'');
      if (numero < 0)
        this.value = '';
    } else
      this.value = this.value.replace(/[^0-9\.]/g,'');
  });
}

function validateNumber(){
  $( '.input-number' ).unbind();
  $( '.input-number' ).on('input', function () {
    this.value = this.value.replace(/[^0-9]/g,'');
  });
}

function scrollToError( $sMetodo, $IdElemento ){
  $sMetodo.animate({
    scrollTop: $IdElemento.offset().top - 100
  }, 'slow');
}
