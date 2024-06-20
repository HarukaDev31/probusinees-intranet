var url, table_Entidad;
//AUTOCOMPLETE
var caractes_no_validos_global_autocomplete = "\"'~!@%^\|";
// Se puede crear un arreglo a partir de la cadena
let search_global_autocomplete = caractes_no_validos_global_autocomplete.split('');
// Solo tomé algunos caracteres, completa el arreglo
let replace_global_autocomplete = ['', '', '', '', '', '', '', '', ''];
//28 caracteres
// FIN AUTOCOMPLETE

var fToday = new Date(), fYear = fToday.getFullYear(), fMonth = fToday.getMonth() + 1, fDay = fToday.getDate();

if (fMonth < 10) {
  fMonth = '0' + fMonth;
}

$(function () {
  $(document).on('click', '#btn-guardar_personal_china', function (e) {
    e.preventDefault();
    if ( $( '#cbo-guardar_personal_china-ID_Usuario' ).val() == 0){
      $( '#cbo-guardar_personal_china-ID_Usuario' ).closest('.form-group').find('.help-block').html('Seleccionar usuario');
      $( '#cbo-guardar_personal_china-ID_Usuario' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      $( '#btn-guardar_personal_china' ).text('');
      $( '#btn-guardar_personal_china' ).attr('disabled', true);
      $( '#btn-guardar_personal_china' ).html( 'Guardando <div class="spinner-border" role="status"><span class="sr-only"></span></div>' );

      url = base_url + 'AgenteCompra/PedidosGarantizados/asignarUsuarioPedidoChina';
      $.ajax({
        type		  : 'POST',
        dataType	: 'JSON',
        url		    : url,
        data		  : $('#form-guardar_personal_china').serialize(),
        success : function( response ){
          $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
          $('#modal-message').modal('show');
          
          if (response.status == 'success'){
            $('.modal-guardar_personal_china').modal('hide');

            $('#moda-message-content').addClass( 'bg-' + response.status);
            $('.modal-title-message').text(response.message);
            setTimeout(function() {$('#modal-message').modal('hide');}, 1100);

            reload_table_Entidad();
          } else {
            $('#moda-message-content').addClass( 'bg-danger' );
            $('.modal-title-message').text(response.message);
            setTimeout(function() {$('#modal-message').modal('hide');}, 1200);
          }
          
          $( '#btn-guardar_personal_china' ).text('');
          $( '#btn-guardar_personal_china' ).html( 'Guardar' );
          $( '#btn-guardar_personal_china' ).attr('disabled', false);
        },
        error: function (jqXHR, textStatus, errorThrown) {
          $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
          
          $( '#modal-message' ).modal('show');
          $( '.modal-message' ).addClass( 'modal-danger' );
          $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
          setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
          
          //Message for developer
          console.log(jqXHR.responseText);
          
          $( '#btn-guardar_personal_china' ).text('');
          $( '#btn-guardar_personal_china' ).append( 'Guardar' );
          $( '#btn-guardar_personal_china' ).attr('disabled', false);
        }
      });
    }
  });
  
	$(document).on('click', '#btn-guardar_fecha_entrega_shipper', function (e) {
    e.preventDefault();

    $( '#btn-guardar_fecha_entrega_shipper' ).text('');
    $( '#btn-guardar_fecha_entrega_shipper' ).attr('disabled', true);
    $( '#btn-guardar_fecha_entrega_shipper' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

    url = base_url + 'AgenteCompra/PedidosAprobadosPagados/despacho';
      $.ajax({
      type		  : 'POST',
      dataType	: 'JSON',
      url		    : url,
      data		  : $('#form-fecha_entrega_shipper').serialize(),
      success : function( response ){
          //$( '#modal-loader' ).modal('hide');
          
          $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
          $('#modal-message').modal('show');
          
          if (response.status == 'success'){
            $('.modal-fecha_entrega_shipper').modal('hide');
              
            $('#moda-message-content').addClass( 'bg-' + response.status);
            $('.modal-title-message').text(response.message);
            setTimeout(function() {$('#modal-message').modal('hide');}, 2100);
            reload_table_Entidad();
          } else {
            $('#moda-message-content').addClass( 'bg-danger' );
            $('.modal-title-message').text(response.message);
            setTimeout(function() {$('#modal-message').modal('hide');}, 3200);
          }
          
          $( '#btn-guardar_fecha_entrega_shipper' ).text('');
          $( '#btn-guardar_fecha_entrega_shipper' ).append( 'Guardar' );
          $( '#btn-guardar_fecha_entrega_shipper' ).attr('disabled', false);
      },
      error: function (jqXHR, textStatus, errorThrown) {
          //$( '#modal-loader' ).modal('hide');
          $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
          
          $( '#modal-message' ).modal('show');
          $('#moda-message-content').addClass( 'bg-danger' );
          $('.modal-title-message').text(response.message);
          setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
          
          //Message for developer
          console.log(jqXHR.responseText);
          
          $( '#btn-guardar_fecha_entrega_shipper' ).text('');
          $( '#btn-guardar_fecha_entrega_shipper' ).append( 'Guardar' );
          $( '#btn-guardar_fecha_entrega_shipper' ).attr('disabled', false);
      }
    });
  });

	$(document).on('click', '#btn-save_proveedor', function (e) {
    e.preventDefault();

    $( '#btn-save_proveedor' ).text('');
    $( '#btn-save_proveedor' ).attr('disabled', true);
    $( '#btn-save_proveedor' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

    //$( '#modal-loader' ).modal('show');

    url = base_url + 'AgenteCompra/PedidosAprobadosPagados/crudProveedor';
      $.ajax({
      type		  : 'POST',
      dataType	: 'JSON',
      url		    : url,
      data		  : $('#form-proveedor').serialize(),
      success : function( response ){
          //$( '#modal-loader' ).modal('hide');
          
          $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
          $('#modal-message').modal('show');
          
          if (response.status == 'success'){
            $('.modal-proveedor').modal('hide');
              
            $('#moda-message-content').addClass( 'bg-' + response.status);
            $('.modal-title-message').text(response.message);
            setTimeout(function() {$('#modal-message').modal('hide');}, 2100);
            
          } else {
            $('#moda-message-content').addClass( 'bg-danger' );
            $('.modal-title-message').text(response.message);
            setTimeout(function() {$('#modal-message').modal('hide');}, 3200);
          }
          
          $( '#btn-save_proveedor' ).text('');
          $( '#btn-save_proveedor' ).append( 'Guardar' );
          $( '#btn-save_proveedor' ).attr('disabled', false);
      },
      error: function (jqXHR, textStatus, errorThrown) {
          //$( '#modal-loader' ).modal('hide');
          $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
          
          $( '#modal-message' ).modal('show');
          $('#moda-message-content').addClass( 'bg-danger' );
          $('.modal-title-message').text(response.message);
          setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
          
          //Message for developer
          console.log(jqXHR.responseText);
          
          $( '#btn-save_proveedor' ).text('');
          $( '#btn-save_proveedor' ).append( 'Guardar' );
          $( '#btn-save_proveedor' ).attr('disabled', false);
      }
    });
  });

	$(document).on('click', '#btn-save_booking', function (e) {
    e.preventDefault();

    $( '#btn-save_booking' ).text('');
    $( '#btn-save_booking' ).attr('disabled', true);
    $( '#btn-save_booking' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

    //$( '#modal-loader' ).modal('show');

    url = base_url + 'AgenteCompra/PedidosAprobadosPagados/reservaBooking';
      $.ajax({
      type		  : 'POST',
      dataType	: 'JSON',
      url		    : url,
      data		  : $('#form-booking').serialize(),
      success : function( response ){
          //$( '#modal-loader' ).modal('hide');
          
          $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
          $('#modal-message').modal('show');
          
          if (response.status == 'success'){
            $('.modal-booking').modal('hide');
              
            $('#moda-message-content').addClass( 'bg-' + response.status);
            $('.modal-title-message').text(response.message);
            setTimeout(function() {$('#modal-message').modal('hide');}, 2100);
          } else {
            $('#moda-message-content').addClass( 'bg-danger' );
            $('.modal-title-message').text(response.message);
            setTimeout(function() {$('#modal-message').modal('hide');}, 3200);
          }
          
          $( '#btn-save_booking' ).text('');
          $( '#btn-save_booking' ).append( 'Guardar' );
          $( '#btn-save_booking' ).attr('disabled', false);
      },
      error: function (jqXHR, textStatus, errorThrown) {
          //$( '#modal-loader' ).modal('hide');
          $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
          
          $( '#modal-message' ).modal('show');
          $('#moda-message-content').addClass( 'bg-danger' );
          $('.modal-title-message').text(response.message);
          setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
          
          //Message for developer
          console.log(jqXHR.responseText);
          
          $( '#btn-save_booking' ).text('');
          $( '#btn-save_booking' ).append( 'Guardar' );
          $( '#btn-save_booking' ).attr('disabled', false);
      }
    });
  });

  //cambiar precio delivery
  $('#btn-save_comision_trading').off('click').click(function () {
    if($( '#txt-modal-precio_comision_trading' ).val().length==0){
      $( '#txt-modal-precio_comision_trading' ).closest('.form-group').find('.help-block').html('Ingresar precio');
      $( '#txt-modal-precio_comision_trading' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if(parseFloat($( '#txt-modal-precio_comision_trading' ).val())<=0.00 || isNaN(parseFloat($( '#txt-modal-precio_comision_trading' ).val()))){
      $( '#txt-modal-precio_comision_trading' ).closest('.form-group').find('.help-block').html('Ingresar precio');
      $( '#txt-modal-precio_comision_trading' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      $( '#btn-save_comision_trading' ).text('');
      $( '#btn-save_comision_trading' ).attr('disabled', true);
      $( '#btn-save_comision_trading' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

      var arrData = Array();
      arrData = {
        'id_pedido_cabecera' : $( '#hidden-modal-id_pedido_cabecera_comision_trading' ).val(),
        'precio_comision_trading' : $( '#txt-modal-precio_comision_trading' ).val()
      }

      url = base_url + 'AgenteCompra/PedidosAprobadosPagados/agregarComisionTrading';
      $.ajax({
        type		  : 'POST',
        dataType	: 'JSON',
        url		    : url,
        data		  : {
          arrData : arrData
        },
        success: function (response) {
          $('.modal-comision_trading').modal('hide');

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
        
          $( '#btn-save_comision_trading' ).text('');
          $( '#btn-save_comision_trading' ).append( 'Guardar' );
          $( '#btn-save_comision_trading' ).attr('disabled', false);
        },
        error: function (jqXHR, textStatus, errorThrown) {
          $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
          
          $( '#modal-message' ).modal('show');
          $( '.modal-message' ).addClass( 'modal-danger' );
          $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
          setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
          
          //Message for developer
          console.log(jqXHR.responseText);
          
          $( '#btn-save_comision_trading' ).text('');
          $( '#btn-save_comision_trading' ).append( 'Guardar' );
          $( '#btn-save_comision_trading' ).attr('disabled', false);
        }
      });
    }
  });

  //Date picker invoice
  $( '.input-report' ).datepicker({
    autoclose : true,
    startDate : new Date('2023', '10', '01'),
    todayHighlight  : true,
    dateFormat: 'dd/mm/yyyy',
    format: 'dd/mm/yyyy',
  });

  //Date picker invoice
  $( '.input-datepicker-pay' ).datepicker({
    autoclose : true,
    startDate : new Date(fYear, fToday.getMonth(), fDay),
    todayHighlight  : true,
    dateFormat: 'dd/mm/yyyy',
    format: 'dd/mm/yyyy',
  });

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

  url = base_url + 'AgenteCompra/PedidosAprobadosPagados/ajax_list';
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
        data.sCorrelativoCotizacion = $( '#hidden-sCorrelativoCotizacion' ).val(),
        data.ID_Pedido_Cabecera = $( '#hidden-ID_Pedido_Cabecera' ).val(),
        data.Filtros_Entidades = $( '#cbo-Filtros_Entidades' ).val(),
        data.Global_Filter = $( '#txt-Global_Filter' ).val(),
        data.Filtro_Fe_Inicio       = ParseDateString($( '#txt-Fe_Inicio' ).val(), 'fecha', '/'),
        data.Filtro_Fe_Fin          = ParseDateString($( '#txt-Fe_Fin' ).val(), 'fecha', '/');
      },
      complete: function () {
        $('.width_full').val($('#hidden-sCorrelativoCotizacion').val());
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
  
  jQuery(document).on('keyup', '.width_full', function (ev) {
    $('#hidden-sCorrelativoCotizacion').val('');
    $('#hidden-ID_Pedido_Cabecera').val('');
    reload_table_Entidad();
  })

  $('#table-Pedidos_filter input').removeClass('form-control-sm');
  $('#table-Pedidos_filter input').addClass('form-control-md');
  $('#table-Pedidos_filter input').addClass("width_full");

  $('#btn-html_reporte').click(function () {
    reload_table_Entidad();
  });

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
    url = base_url + 'AgenteCompra/PedidosAprobadosPagados/downloadImage/' + id;
    
    var popupwin = window.open(url);
    setTimeout(function() { popupwin.close();}, 2000);
  })

	$(document).on('click', '.btn-agregar_pago_proveedor', function (e) {
    e.preventDefault();

    $( '#form-agregar_pago_proveedor' )[0].reset();

    $('#img_producto-preview1').html('');
    $('#img_producto-preview1').attr('src', '');

    var id_empresa = $(this).data('id_empresa');
    var id_organizacion = $(this).data('id_organizacion');
    var id_cabecera = $(this).data('id_pedido_cabecera');
    var id_detalle = $(this).data('id_pedido_detalle');
    var id = $(this).data('id');
    var tipo_pago = $(this).data('tipo_pago');
    var correlativo = $(this).data('correlativo');

    $( '[name="proveedor-id_empresa"]' ).val(id_empresa);
    $( '[name="proveedor-id_organizacion"]' ).val(id_organizacion);
    $( '[name="proveedor-id_cabecera"]' ).val(id_cabecera);
    $( '[name="proveedor-id_detalle"]' ).val(id_detalle);
    $( '[name="proveedor-id"]' ).val(id);
    $( '[name="proveedor-tipo_pago"]' ).val(tipo_pago);
    $( '[name="proveedor-correlativo"]' ).val(correlativo);

    $('#modal-agregar_pago').modal('show');

    $( '#modal-agregar_pago' ).on('shown.bs.modal', function() {
      $( '#amount_proveedor' ).focus();
    })
  })

	$(document).on('click', '.btn-agregar_inspeccion', function (e) {
    e.preventDefault();

    $( '#form-agregar_inspeccion' )[0].reset();

    var id_empresa = $(this).data('id_empresa');
    var id_organizacion = $(this).data('id_organizacion');
    var id_cabecera = $(this).data('id_pedido_cabecera');
    var id_detalle = $(this).data('id_pedido_detalle');
    var id = $(this).data('id');
    var tipo_pago = $(this).data('tipo_pago');
    var correlativo = $(this).data('correlativo');

    $( '[name="proveedor-id_empresa"]' ).val(id_empresa);
    $( '[name="proveedor-id_organizacion"]' ).val(id_organizacion);
    $( '[name="proveedor-id_cabecera"]' ).val(id_cabecera);
    $( '[name="proveedor-id_detalle"]' ).val(id_detalle);
    $( '[name="proveedor-id"]' ).val(id);
    $( '[name="proveedor-tipo_pago"]' ).val(tipo_pago);
    $( '[name="proveedor-correlativo"]' ).val(correlativo);

    $('#modal-agregar_inspeccion').modal('show');
  })

	$(document).on('click', '.btn-eliminar_item_proveedor', function (e) {
    e.preventDefault();

    var id = $(this).data('id');
    var id_pedido_cabecera = $(this).data('id_pedido_cabecera');
    var correlativo = $(this).data('correlativo');
    var name_item = $(this).data('name_item');

    var $modal_delete = $( '#modal-message-delete' );
    $modal_delete.modal('show');

    $( '#modal-title' ).html('¿Estás seguro de eliminar?');

    $( '#btn-cancel-delete' ).off('click').click(function () {
      $modal_delete.modal('hide');
    });

    $( '#btn-save-delete' ).off('click').click(function () {
      $( '#btn-save-delete' ).text('');
      $( '#btn-save-delete' ).attr('disabled', true);
      $( '#btn-save-delete' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

      url = base_url + 'AgenteCompra/PedidosAprobadosPagados/elminarItemProveedor/' + id + '/' + correlativo + '/' + name_item;
      $.ajax({
        url : url,
        type: "GET",
        dataType: "JSON",
        success: function(response){
          $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
          $('#modal-message').modal('show');
          
          if (response.status == 'success'){
            $modal_delete.modal('hide');

            verPedido(id_pedido_cabecera);

            $('#moda-message-content').addClass( 'bg-' + response.status);
            $('.modal-title-message').text(response.message);
            setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
          } else {
            $('#moda-message-content').addClass( 'bg-danger' );
            $('.modal-title-message').text(response.message);
            setTimeout(function() {$('#modal-message').modal('hide');}, 1200);
          }
          
          $( '#btn-save-delete' ).text('');
          $( '#btn-save-delete' ).append( 'Guardar' );
          $( '#btn-save-delete' ).attr('disabled', false);
        }
      })
    });
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
        url: base_url + 'AgenteCompra/PedidosAprobadosPagados/addInspeccionProveedor',
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
        url: base_url + 'AgenteCompra/PedidosAprobadosPagados/addPagoProveedor',
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
        url: base_url + 'AgenteCompra/PedidosAprobadosPagados/addFileProveedor',
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
  
  $("#form-pago_cliente_30").on('submit',function(e){
    e.preventDefault();

    $('.help-block').empty();
    $('.form-group').removeClass('has-error');

    if(document.getElementById('pago_cliente_30').files.length == 0) {
      $('#pago_cliente_30').closest('.form-group').find('.help-block').html('Empty file');
      $('#pago_cliente_30').closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      var postData = new FormData($("#form-pago_cliente_30")[0]);
      $.ajax({
        url: base_url + 'AgenteCompra/PedidosAprobadosPagados/addPagoCliente30',
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
          $('#modal-pago_cliente_30').modal('hide');

          verPedido($('#pago_cliente_30-id_cabecera').val());

          $('#moda-message-content').addClass( 'bg-' + response.status);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
          
        } else {
          $('#moda-message-content').addClass( 'bg-danger' );
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 2100);
        }
      });
    }
  });

  $("#form-pago_cliente_100").on('submit',function(e){
    e.preventDefault();

    $('.help-block').empty();
    $('.form-group').removeClass('has-error');

    if(document.getElementById('pago_cliente_100').files.length == 0) {
      $('#pago_cliente_100').closest('.form-group').find('.help-block').html('Empty file');
      $('#pago_cliente_100').closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      var postData = new FormData($("#form-pago_cliente_100")[0]);
      $.ajax({
        url: base_url + 'AgenteCompra/PedidosAprobadosPagados/addPagoCliente100',
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
          $('#modal-pago_cliente_100').modal('hide');

          verPedido($('#pago_cliente_100-id_cabecera').val());

          $('#moda-message-content').addClass( 'bg-' + response.status);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
          
        } else {
          $('#moda-message-content').addClass( 'bg-danger' );
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 2100);
        }
      });
    }
  });

  $("#form-pago_cliente_servicio").on('submit',function(e){
    e.preventDefault();

    $('.help-block').empty();
    $('.form-group').removeClass('has-error');

    if(document.getElementById('pago_cliente_servicio').files.length == 0) {
      $('#pago_cliente_servicio').closest('.form-group').find('.help-block').html('Empty file');
      $('#pago_cliente_servicio').closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      var postData = new FormData($("#form-pago_cliente_servicio")[0]);
      $.ajax({
        url: base_url + 'AgenteCompra/PedidosAprobadosPagados/addPagoClienteServicio',
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
          $('#modal-pago_cliente_servicio').modal('hide');

          verPedido($('#pago_cliente_servicio-id_cabecera').val());

          $('#moda-message-content').addClass( 'bg-' + response.status);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
          
        } else {
          $('#moda-message-content').addClass( 'bg-danger' );
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 2100);
        }
      });
    }
  });

  $("#form-pago_flete").on('submit',function(e){
    e.preventDefault();

    $('.help-block').empty();
    $('.form-group').removeClass('has-error');

    if(document.getElementById('pago_flete').files.length == 0) {
      $('#pago_flete').closest('.form-group').find('.help-block').html('Empty file');
      $('#pago_flete').closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      var postData = new FormData($("#form-pago_flete")[0]);
      $.ajax({
        url: base_url + 'AgenteCompra/PedidosAprobadosPagados/addPagoFlete',
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
          $('#modal-pago_flete').modal('hide');

          verPedido($('#pago_flete-id_cabecera').val());

          $('#moda-message-content').addClass( 'bg-' + response.status);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
          
        } else {
          $('#moda-message-content').addClass( 'bg-danger' );
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 2100);
        }
      });
    }
  });

  $("#form-costos_origen").on('submit',function(e){
    e.preventDefault();

    $('.help-block').empty();
    $('.form-group').removeClass('has-error');

    if(document.getElementById('costos_origen').files.length == 0) {
      $('#costos_origen').closest('.form-group').find('.help-block').html('Empty file');
      $('#costos_origen').closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      var postData = new FormData($("#form-costos_origen")[0]);
      $.ajax({
        url: base_url + 'AgenteCompra/PedidosAprobadosPagados/addPagoCostosOrigen',
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
          $('#modal-costos_origen').modal('hide');

          verPedido($('#costos_origen-id_cabecera').val());

          $('#moda-message-content').addClass( 'bg-' + response.status);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
          
        } else {
          $('#moda-message-content').addClass( 'bg-danger' );
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 2100);
        }
      });
    }
  });

  $("#form-pago_fta").on('submit',function(e){
    e.preventDefault();

    $('.help-block').empty();
    $('.form-group').removeClass('has-error');

    if(document.getElementById('pago_fta').files.length == 0) {
      $('#pago_fta').closest('.form-group').find('.help-block').html('Empty file');
      $('#pago_fta').closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      var postData = new FormData($("#form-pago_fta")[0]);
      $.ajax({
        url: base_url + 'AgenteCompra/PedidosAprobadosPagados/addPagoFta',
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
          $('#modal-pago_fta').modal('hide');

          verPedido($('#pago_fta-id_cabecera').val());

          $('#moda-message-content').addClass( 'bg-' + response.status);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
          
        } else {
          $('#moda-message-content').addClass( 'bg-danger' );
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 2100);
        }
      });
    }
  });

  $("#form-otros_cuadrilla").on('submit',function(e){
    e.preventDefault();

    $('.help-block').empty();
    $('.form-group').removeClass('has-error');

    if(document.getElementById('otros_cuadrilla').files.length == 0) {
      $('#otros_cuadrilla').closest('.form-group').find('.help-block').html('Empty file');
      $('#otros_cuadrilla').closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      var postData = new FormData($("#form-otros_cuadrilla")[0]);
      $.ajax({
        url: base_url + 'AgenteCompra/PedidosAprobadosPagados/addOtrosCuadrilla',
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
          $('#modal-otros_cuadrilla').modal('hide');

          verPedido($('#otros_cuadrilla-id_cabecera').val());

          $('#moda-message-content').addClass( 'bg-' + response.status);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
          
        } else {
          $('#moda-message-content').addClass( 'bg-danger' );
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 2100);
        }
      });
    }
  });

  $("#form-otros_costos").on('submit',function(e){
    e.preventDefault();

    $('.help-block').empty();
    $('.form-group').removeClass('has-error');

    if(document.getElementById('otros_costos').files.length == 0) {
      $('#otros_costos').closest('.form-group').find('.help-block').html('Empty file');
      $('#otros_costos').closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      var postData = new FormData($("#form-otros_costos")[0]);
      $.ajax({
        url: base_url + 'AgenteCompra/PedidosAprobadosPagados/addOtrosCostos',
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
          $('#modal-otros_costos').modal('hide');

          verPedido($('#otros_costos-id_cabecera').val());

          $('#moda-message-content').addClass( 'bg-' + response.status);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
          
        } else {
          $('#moda-message-content').addClass( 'bg-danger' );
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 2100);
        }
      });
    }
  });
  
  $('#span-id_pedido').html('');
  
	$(document).on('click', '.btn-estado_item_proveedor', function (e) {
    e.preventDefault();

    var id = $(this).data('id');
    var id_pedido_cabecera = $(this).data('id_pedido_cabecera');
    var cantidad = $('#input-cantidad' + id).val();
    var estado = $(this).data('estado');
    
    $( '.btn-cargando_item_proveedor' + id ).text('');
    $( '.btn-cargando_item_proveedor' + id ).attr('disabled', true);
    $( '.btn-cargando_item_proveedor' + id ).html( 'Guardando <div class="spinner-border" role="status"><span class="sr-only"></span></div>' );

    url = base_url + 'AgenteCompra/PedidosAprobadosPagados/actualizarRecepcionCargaItemProveedor';
    $.ajax({
      type		  : 'POST',
      dataType	: 'JSON',
      url		    : url,
      data		  : {
        id: id,
        cantidad: cantidad,
        estado: estado,
      },
      success : function( response ){
        $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
        $('#modal-message').modal('show');
        
        if (response.status == 'success'){
          recepcionCarga(id_pedido_cabecera);

          $('#moda-message-content').addClass( 'bg-' + response.status);
          $('.modal-title-message').text(response.message);
          setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
        } else {
          $('#moda-message-content').addClass( 'bg-danger' );
          $('.modal-title-message').text(response.message);
          setTimeout(function() {$('#modal-message').modal('hide');}, 1200);
        }
        
        $( '.btn-cargando_item_proveedor' + id ).text('');
        $( '.btn-cargando_item_proveedor' + id ).html( 'Guardar' );
        $( '.btn-cargando_item_proveedor' + id ).attr('disabled', false);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
        
        $( '#modal-message' ).modal('show');
        $( '.modal-message' ).addClass( 'modal-danger' );
        $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
        setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
        
        //Message for developer
        console.log(jqXHR.responseText);
        
        $( '.btn-cargando_item_proveedor' + id ).text('');
        $( '.btn-cargando_item_proveedor' + id ).append( 'Guardar' );
        $( '.btn-cargando_item_proveedor' + id ).attr('disabled', false);
      }
    });
  })
  
	$(document).on('click', '.btn-finalizar_item_proveedor', function (e) {
    e.preventDefault();
    
    var id = $(this).data('id');
    var id_pedido_cabecera = $(this).data('id_pedido_cabecera');
    var nota = $('#textarea-nota' + id).val();
    
    $( '#btn-finalizar_item_proveedor' + id ).text('');
    $( '#btn-finalizar_item_proveedor' + id ).attr('disabled', true);
    $( '#btn-finalizar_item_proveedor' + id ).html( 'Guardando <div class="spinner-border" role="status"><span class="sr-only"></span></div>' );

    url = base_url + 'AgenteCompra/PedidosAprobadosPagados/actualizarRecepcionCargaProveedor';
    $.ajax({
      type		  : 'POST',
      dataType	: 'JSON',
      url		    : url,
      data		  : {
        id: id,
        nota: nota,
      },
      success : function( response ){
        $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
        $('#modal-message').modal('show');
        
        if (response.status == 'success'){
          recepcionCarga(id_pedido_cabecera);

          $('#moda-message-content').addClass( 'bg-' + response.status);
          $('.modal-title-message').text(response.message);
          setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
        } else {
          $('#moda-message-content').addClass( 'bg-danger' );
          $('.modal-title-message').text(response.message);
          setTimeout(function() {$('#modal-message').modal('hide');}, 1200);
        }
        
        $( '#btn-finalizar_item_proveedor' + id ).text('');
        $( '#btn-finalizar_item_proveedor' + id ).html( 'Guardar' );
        $( '#btn-finalizar_item_proveedor' + id ).attr('disabled', false);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
        
        $( '#modal-message' ).modal('show');
        $( '.modal-message' ).addClass( 'modal-danger' );
        $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
        setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
        
        //Message for developer
        console.log(jqXHR.responseText);
        
        $( '#btn-finalizar_item_proveedor' + id ).text('');
        $( '#btn-finalizar_item_proveedor' + id ).append( 'Guardar' );
        $( '#btn-finalizar_item_proveedor' + id ).attr('disabled', false);
      }
    });
  })
  
  
	$(document).on('click', '.btn-image_documento', function (e) {
    e.preventDefault();
    
    var id = $(this).data('id');
    var id_pedido_cabecera = $(this).data('id_pedido_cabecera');
    
    $( '#btn-image_documento' + id ).text('');
    $( '#btn-image_documento' + id ).attr('disabled', true);
    $( '#btn-image_documento' + id ).html( 'Guardando <div class="spinner-border" role="status"><span class="sr-only"></span></div>' );

    var postData = new FormData($("#form-invoice_pl_proveedor" + id)[0]);
    url = base_url + 'AgenteCompra/PedidosAprobadosPagados/subirInvoicePlProveedor';
    $.ajax({
      type		  : 'POST',
      dataType	: 'JSON',
      url		    : url,
      data: postData,
      processData: false,
      contentType: false,
      success : function( response ){
        $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
        $('#modal-message').modal('show');
        
        if (response.status == 'success'){
          invoiceProveedor(id_pedido_cabecera);

          $('#moda-message-content').addClass( 'bg-' + response.status);
          $('.modal-title-message').text(response.message);
          setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
        } else {
          $('#moda-message-content').addClass( 'bg-danger' );
          $('.modal-title-message').text(response.message);
          setTimeout(function() {$('#modal-message').modal('hide');}, 1200);
        }
        
        $( '#btn-finalizar_item_proveedor' + id ).text('');
        $( '#btn-finalizar_item_proveedor' + id ).html( 'Guardar' );
        $( '#btn-finalizar_item_proveedor' + id ).attr('disabled', false);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
        
        $( '#modal-message' ).modal('show');
        $( '.modal-message' ).addClass( 'modal-danger' );
        $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
        setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
        
        //Message for developer
        console.log(jqXHR.responseText);
        
        $( '#btn-finalizar_item_proveedor' + id ).text('');
        $( '#btn-finalizar_item_proveedor' + id ).append( 'Guardar' );
        $( '#btn-finalizar_item_proveedor' + id ).attr('disabled', false);
      }
    });
  })
  
	$(document).on('click', '.btn-cambiar_item_proveedor', function (e) {
    e.preventDefault();
    
    var id = $(this).data('id');
    var id_pedido_cabecera = $(this).data('id_pedido_cabecera');

    $( '[name="cambio_item_proveedor-id_item"]' ).val(id);
    $( '[name="cambio_item_proveedor-id_cabecera"]' ).val(id_pedido_cabecera);
  
    $('#modal-cambio_item_proveedor').modal('show');
    $( '#form-cambio_item_proveedor' )[0].reset();
  });
})

function reload_table_Entidad(){
  table_Entidad.ajax.reload(null,false);
}

function invoiceProveedor(ID){
  $( '.div-Listar' ).hide();
  
  $( '#form-pedido' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();

  $( '.div-Compuesto' ).hide();
  $( '.div-Producto_Recepcion_Carga' ).hide();
	$( '#table-Producto_Enlace tbody' ).empty();
	$( '#table-Producto_Recepcion_Carga tbody' ).empty();

  $( '.div-Invoice_Proveedor' ).show();
	$( '#table-Invoice_Proveedor tbody' ).empty();
  $( '#table-Invoice_Proveedor' ).show();

  //$('#span-id_pedido').html('Nro. ' + ID);

  url = base_url + 'AgenteCompra/PedidosAprobadosPagados/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      console.log(response);
      var detalle = response;
      response = response[0];

      $('#span-id_pedido').html(response.sCorrelativoCotizacion);
      
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
      
      //$( '#btn-excel_order_tracking' ).data('id_pedido', response.ID_Pedido_Cabecera);
      $( '#btn-excel_order_tracking' ).attr('data-id_pedido', response.ID_Pedido_Cabecera); // sets 

      $('#btn-descargar_pago_30').hide();
      $('#span-pago_30').html('');
      if( response.Txt_Url_Pago_30_Cliente != '' && response.Txt_Url_Pago_30_Cliente != null ){
        $('#btn-descargar_pago_30').show();
        $('#btn-descargar_pago_30').removeClass('d-none');

        $('#span-pago_30').html('$ ' + response.Ss_Pago_30_Cliente);
      }

      $('#btn-descargar_pago_100').hide();
      $('#span-pago_100').html('');
      if( response.Txt_Url_Pago_100_Cliente != '' && response.Txt_Url_Pago_100_Cliente != null ){
        $('#btn-descargar_pago_100').show();
        $('#btn-descargar_pago_100').removeClass('d-none');

        $('#span-pago_100').html('$ ' + response.Ss_Pago_100_Cliente);
      }

      $('#btn-descargar_pago_servicio').hide();
      $('#span-pago_servicio').html('');
      if( response.Txt_Url_Pago_Servicio_Cliente != '' && response.Txt_Url_Pago_Servicio_Cliente != null ){
        $('#btn-descargar_pago_servicio').show();
        $('#btn-descargar_pago_servicio').removeClass('d-none');

        $('#span-pago_servicio').html('$ ' + response.Ss_Pago_Servicio_Cliente);
      }

      $('#btn-descargar_flete').hide();
      $('#span-flete').html('');
      if( response.Txt_Url_Pago_Otros_Flete != '' && response.Txt_Url_Pago_Otros_Flete != null ){
        $('#btn-descargar_flete').show();
        $('#btn-descargar_flete').removeClass('d-none');

        $('#span-flete').html('$ ' + response.Ss_Pago_Otros_Flete);
      }

      $('#btn-descargar_costo_origen').hide();
      $('#span-costo_origen').html('');
      if( response.Txt_Url_Pago_Otros_Costo_Origen != '' && response.Txt_Url_Pago_Otros_Costo_Origen != null ){
        $('#btn-descargar_costo_origen').show();
        $('#btn-descargar_costo_origen').removeClass('d-none');

        $('#span-costo_origen').html('$ ' + response.Ss_Pago_Otros_Costo_Origen);
      }

      $('#btn-descargar_fta').hide();
      $('#span-fta').html('');
      if( response.Txt_Url_Pago_Otros_Costo_Fta != '' && response.Txt_Url_Pago_Otros_Costo_Fta != null ){
        $('#btn-descargar_fta').show();
        $('#btn-descargar_fta').removeClass('d-none');

        $('#span-fta').html('$ ' + response.Ss_Pago_Otros_Costo_Fta);
      }

      $('#btn-descargar_pago_cuadrilla').hide();
      $('#span-cuadrilla').html('');
      if( response.Txt_Url_Pago_Otros_Cuadrilla != '' && response.Txt_Url_Pago_Otros_Cuadrilla != null ){
        $('#btn-descargar_pago_cuadrilla').show();
        $('#btn-descargar_pago_cuadrilla').removeClass('d-none');

        $('#span-cuadrilla').html('$ ' + response.Ss_Pago_Otros_Cuadrilla);
      }

      $('#btn-descargar_otros_costos').hide();
      $('#span-otros_costo').html('');
      if( response.Txt_Url_Pago_Otros_Costos != '' && response.Txt_Url_Pago_Otros_Costos != null ){
        $('#btn-descargar_otros_costos').show();
        $('#btn-descargar_otros_costos').removeClass('d-none');

        $('#span-otros_costo').html('$ ' + response.Ss_Pago_Otros_Costos);
      }

      var sNombreEstado = '<span class="badge badge-pill badge-secondary">Pendiente</span>';
      if(response.Nu_Estado_Pedido == 2)
        sNombreEstado = '<span class="badge badge-pill badge-primary">Confirmado</span>';
      else if(response.Nu_Estado_Pedido == 3)
        sNombreEstado = '<span class="badge badge-pill badge-success">Entregado</span>';
      else if(response.Nu_Estado_Pedido == 4)
        sNombreEstado = '<span class="badge badge-pill badge-danger">Confirmado</span>';
      $( '#div-estado' ).html(sNombreEstado);
      
      var table_enlace_producto = "", iDiasVencimiento = 0, sClassColorTr = "", fTotalCliente = 0, ID_Entidad = '';
      for (i = 0; i < detalle.length; i++) {
        var cantidad_item_final_recepcion_carga = parseFloat(detalle[i]['Qt_Producto_Caja_Final_Verificada']);
        var cantidad_item = parseFloat(detalle[i]['Qt_Producto']);
        var precio_china = parseFloat(detalle[i]['Ss_Precio']);

        fTotalCliente += (cantidad_item * (precio_china * parseFloat(response.Ss_Tipo_Cambio)));

        var id_item = detalle[i]['ID_Pedido_Detalle_Producto_Proveedor'];
        var voucher_1 = detalle[i]['Txt_Url_Archivo_Pago_1_Proveedor'];
        var voucher_2 = detalle[i]['Txt_Url_Archivo_Pago_2_Proveedor'];
        //max-height: 350px;width: 100%; cursor:pointer

        var fTotal = (cantidad_item * precio_china);
        var Ss_Pago_1_Proveedor = parseFloat(detalle[i]['Ss_Pago_1_Proveedor']);
        var Ss_Pago_2_Proveedor = parseFloat(detalle[i]['Ss_Pago_2_Proveedor']);

        sClassColorTr = '';
        iDiasVencimiento = 0;
        if((detalle[i]['Fe_Entrega_Proveedor'] != '' && detalle[i]['Fe_Entrega_Proveedor'] != null)){
          var fechaInicio = new Date(fYear + '-' + fMonth + '-' + fDay).getTime();
          var fechaFin    = new Date(detalle[i]['Fe_Entrega_Proveedor']).getTime();

          var diff = fechaFin - fechaInicio;
          iDiasVencimiento = (diff / (1000*60*60*24));// --> milisegundos -> segundos -> minutos -> horas -> días
          if(iDiasVencimiento<5)
            sClassColorTr = 'table-warning';
        }

        var fecha_entrega_proveedor = ( (detalle[i]['Fe_Entrega_Proveedor'] != '' && detalle[i]['Fe_Entrega_Proveedor'] != null) ? ParseDateString(detalle[i]['Fe_Entrega_Proveedor'], 'fecha_bd', '-') : '');

        var nota_final = (detalle[i]['Txt_Nota_Recepcion_Carga_Proveedor'] != '' && detalle[i]['Txt_Nota_Recepcion_Carga_Proveedor'] != null ? detalle[i]['Txt_Nota_Recepcion_Carga_Proveedor'] : '');

        if (ID_Entidad != detalle[i].ID_Entidad_Proveedor) {
          table_enlace_producto +="<tr>";
            table_enlace_producto += "<th class='text-left'>" + detalle[i].No_Contacto_Proveedor + "</th>";
            table_enlace_producto += "<th class='text-left'>";//Txt_Url_Archivo_Invoice_Pl_Recepcion_Carga_Proveedor si es diferente de vacio descargar
              if(detalle[i].Txt_Url_Archivo_Invoice_Pl_Recepcion_Carga_Proveedor != '' && detalle[i].Txt_Url_Archivo_Invoice_Pl_Recepcion_Carga_Proveedor != null) {
                table_enlace_producto += '<button class="btn btn-link" alt="Descargar Invoice y PL" title="Descargar Invoice y PL" href="javascript:void(0)" onclick="descargarInvoicePlProveedor(' + id_item + ')">Descargar</button>';
              } else {
                table_enlace_producto += '<form action="' + base_url + 'AgenteCompra/PedidosAprobadosPagados/listar" id="form-invoice_pl_proveedor' + id_item + '" method="post" accept-charset="utf-8">';
                  table_enlace_producto += '<input type="hidden" id="documento-id" name="documento-id" value="' + id_item + '" class="form-control"></input>';
                  table_enlace_producto += '<input class="form-control" id="image_documento' + id_item + '" name="image_documento" type="file" accept="application/msword, application/vnd.ms-excel, application/pdf, .csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"></input>';
                table_enlace_producto += '</form>';
              }
              //table_enlace_producto += '<textarea id="textarea-nota' + id_item + '" name="addProducto[' + id_item + '][nota]" class="form-control required nota" placeholder="Observaciones" rows="1" style="height: 50px;">' + clearHTMLTextArea(nota_final) + '</textarea>';
            table_enlace_producto += '</th>';
            table_enlace_producto += "<th class='text-center'>";
              if(detalle[i].Txt_Url_Archivo_Invoice_Pl_Recepcion_Carga_Proveedor != '' && detalle[i].Txt_Url_Archivo_Invoice_Pl_Recepcion_Carga_Proveedor != null) {
                table_enlace_producto += '';
              } else {
                table_enlace_producto += '<button type="button" id="btn-image_documento' + id_item + '" data-name_item="' + detalle[i]['Txt_Producto'] + '" data-id_pedido_cabecera="' + response.ID_Pedido_Cabecera + '" data-id="' + id_item + '" data-correlativo="' + response.sCorrelativoCotizacion + '" class="text-left btn btn-primary btn-image_documento"> Subir archivo </button>';
              }
            table_enlace_producto += '</th>';
          +"</tr>";
          ID_Entidad = detalle[i].ID_Entidad_Proveedor;
        }
      }
      
      $('#span-total_cantidad_items').html(i);
      $( '#table-Invoice_Proveedor' ).append(table_enlace_producto);

      $( '#span-total_cliente' ).html('$ ' + fTotalCliente.toFixed(2));

      $( '#span-saldo_cliente' ).html('$ ' + (fTotalCliente - (parseFloat(response.Ss_Pago_30_Cliente) + parseFloat(response.Ss_Pago_100_Cliente) + parseFloat(response.Ss_Pago_Servicio_Cliente))));
      
      //Date picker invoice
      $( '.input-datepicker-today-to-more' ).datepicker({
        autoclose : true,
        startDate : new Date(fYear, fToday.getMonth(), fDay),
        todayHighlight  : true,
        dateFormat: 'dd/mm/yyyy',
        format: 'dd/mm/yyyy',
      });
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

function recepcionCarga(ID){
  $( '.div-Listar' ).hide();
  
  $( '#form-pedido' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();

  $( '.div-Compuesto' ).hide();
  $( '.div-Producto_Recepcion_Carga' ).hide();
	$( '#table-Producto_Enlace tbody' ).empty();
	$( '#table-Producto_Recepcion_Carga tbody' ).empty();

  $( '.div-Producto_Recepcion_Carga' ).show();
	$( '#table-Producto_Recepcion_Carga tbody' ).empty();
  $( '#table-Producto_Recepcion_Carga' ).show();

  $( '.div-Invoice_Proveedor' ).hide();
	$( '#table-Invoice_Proveedor tbody' ).empty();
  
  //$('#span-id_pedido').html('Nro. ' + ID);

  url = base_url + 'AgenteCompra/PedidosAprobadosPagados/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      console.log(response);
      var detalle = response;
      response = response[0];

      $('#span-id_pedido').html(response.sCorrelativoCotizacion);
      
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
      
      //$( '#btn-excel_order_tracking' ).data('id_pedido', response.ID_Pedido_Cabecera);
      $( '#btn-excel_order_tracking' ).attr('data-id_pedido', response.ID_Pedido_Cabecera); // sets 

      $('#btn-descargar_pago_30').hide();
      $('#span-pago_30').html('');
      if( response.Txt_Url_Pago_30_Cliente != '' && response.Txt_Url_Pago_30_Cliente != null ){
        $('#btn-descargar_pago_30').show();
        $('#btn-descargar_pago_30').removeClass('d-none');

        $('#span-pago_30').html('$ ' + response.Ss_Pago_30_Cliente);
      }

      $('#btn-descargar_pago_100').hide();
      $('#span-pago_100').html('');
      if( response.Txt_Url_Pago_100_Cliente != '' && response.Txt_Url_Pago_100_Cliente != null ){
        $('#btn-descargar_pago_100').show();
        $('#btn-descargar_pago_100').removeClass('d-none');

        $('#span-pago_100').html('$ ' + response.Ss_Pago_100_Cliente);
      }

      $('#btn-descargar_pago_servicio').hide();
      $('#span-pago_servicio').html('');
      if( response.Txt_Url_Pago_Servicio_Cliente != '' && response.Txt_Url_Pago_Servicio_Cliente != null ){
        $('#btn-descargar_pago_servicio').show();
        $('#btn-descargar_pago_servicio').removeClass('d-none');

        $('#span-pago_servicio').html('$ ' + response.Ss_Pago_Servicio_Cliente);
      }

      $('#btn-descargar_flete').hide();
      $('#span-flete').html('');
      if( response.Txt_Url_Pago_Otros_Flete != '' && response.Txt_Url_Pago_Otros_Flete != null ){
        $('#btn-descargar_flete').show();
        $('#btn-descargar_flete').removeClass('d-none');

        $('#span-flete').html('$ ' + response.Ss_Pago_Otros_Flete);
      }

      $('#btn-descargar_costo_origen').hide();
      $('#span-costo_origen').html('');
      if( response.Txt_Url_Pago_Otros_Costo_Origen != '' && response.Txt_Url_Pago_Otros_Costo_Origen != null ){
        $('#btn-descargar_costo_origen').show();
        $('#btn-descargar_costo_origen').removeClass('d-none');

        $('#span-costo_origen').html('$ ' + response.Ss_Pago_Otros_Costo_Origen);
      }

      $('#btn-descargar_fta').hide();
      $('#span-fta').html('');
      if( response.Txt_Url_Pago_Otros_Costo_Fta != '' && response.Txt_Url_Pago_Otros_Costo_Fta != null ){
        $('#btn-descargar_fta').show();
        $('#btn-descargar_fta').removeClass('d-none');

        $('#span-fta').html('$ ' + response.Ss_Pago_Otros_Costo_Fta);
      }

      $('#btn-descargar_pago_cuadrilla').hide();
      $('#span-cuadrilla').html('');
      if( response.Txt_Url_Pago_Otros_Cuadrilla != '' && response.Txt_Url_Pago_Otros_Cuadrilla != null ){
        $('#btn-descargar_pago_cuadrilla').show();
        $('#btn-descargar_pago_cuadrilla').removeClass('d-none');

        $('#span-cuadrilla').html('$ ' + response.Ss_Pago_Otros_Cuadrilla);
      }

      $('#btn-descargar_otros_costos').hide();
      $('#span-otros_costo').html('');
      if( response.Txt_Url_Pago_Otros_Costos != '' && response.Txt_Url_Pago_Otros_Costos != null ){
        $('#btn-descargar_otros_costos').show();
        $('#btn-descargar_otros_costos').removeClass('d-none');

        $('#span-otros_costo').html('$ ' + response.Ss_Pago_Otros_Costos);
      }

      var sNombreEstado = '<span class="badge badge-pill badge-secondary">Pendiente</span>';
      if(response.Nu_Estado_Pedido == 2)
        sNombreEstado = '<span class="badge badge-pill badge-primary">Confirmado</span>';
      else if(response.Nu_Estado_Pedido == 3)
        sNombreEstado = '<span class="badge badge-pill badge-success">Entregado</span>';
      else if(response.Nu_Estado_Pedido == 4)
        sNombreEstado = '<span class="badge badge-pill badge-danger">Confirmado</span>';
      $( '#div-estado' ).html(sNombreEstado);
      
      var iCounterSupplier=1, table_enlace_producto = "", iDiasVencimiento = 0, sClassColorTr = "", fTotalCliente = 0, ID_Entidad = '';
      for (i = 0; i < detalle.length; i++) {
        var cantidad_item_final_recepcion_carga = parseFloat(detalle[i]['Qt_Producto_Caja_Final_Verificada']);
        var cantidad_item = parseFloat(detalle[i]['Qt_Producto']);
        var precio_china = parseFloat(detalle[i]['Ss_Precio']);

        fTotalCliente += (cantidad_item * (precio_china * parseFloat(response.Ss_Tipo_Cambio)));

        var id_item = detalle[i]['ID_Pedido_Detalle_Producto_Proveedor'];
        var voucher_1 = detalle[i]['Txt_Url_Archivo_Pago_1_Proveedor'];
        var voucher_2 = detalle[i]['Txt_Url_Archivo_Pago_2_Proveedor'];
        //max-height: 350px;width: 100%; cursor:pointer

        var fTotal = (cantidad_item * precio_china);
        var Ss_Pago_1_Proveedor = parseFloat(detalle[i]['Ss_Pago_1_Proveedor']);
        var Ss_Pago_2_Proveedor = parseFloat(detalle[i]['Ss_Pago_2_Proveedor']);

        sClassColorTr = '';
        iDiasVencimiento = 0;
        if((detalle[i]['Fe_Entrega_Proveedor'] != '' && detalle[i]['Fe_Entrega_Proveedor'] != null)){
          var fechaInicio = new Date(fYear + '-' + fMonth + '-' + fDay).getTime();
          var fechaFin    = new Date(detalle[i]['Fe_Entrega_Proveedor']).getTime();

          var diff = fechaFin - fechaInicio;
          iDiasVencimiento = (diff / (1000*60*60*24));// --> milisegundos -> segundos -> minutos -> horas -> días
          if(iDiasVencimiento<5)
            sClassColorTr = 'table-warning';
        }

        var fecha_entrega_proveedor = ( (detalle[i]['Fe_Entrega_Proveedor'] != '' && detalle[i]['Fe_Entrega_Proveedor'] != null) ? ParseDateString(detalle[i]['Fe_Entrega_Proveedor'], 'fecha_bd', '-') : '');

        var nota_final = (detalle[i]['Txt_Nota_Recepcion_Carga_Proveedor'] != '' && detalle[i]['Txt_Nota_Recepcion_Carga_Proveedor'] != null ? detalle[i]['Txt_Nota_Recepcion_Carga_Proveedor'] : '');

        if (ID_Entidad != detalle[i].ID_Entidad_Proveedor) {
          table_enlace_producto +=
          "<tr class='table-active'>"
            +"<th class='text-right'>"  + iCounterSupplier + ". Supplier</th>";
            table_enlace_producto += "<th class='text-left'>" + detalle[i].No_Contacto_Proveedor + "</th>";
            table_enlace_producto += "<th class='text-left' colspan='2'>";
              table_enlace_producto += "Observaciones<br>";
              table_enlace_producto += '<textarea id="textarea-nota' + id_item + '" name="addProducto[' + id_item + '][nota]" class="form-control required nota" placeholder="Observaciones" rows="1" style="height: 50px;">' + clearHTMLTextArea(nota_final) + '</textarea>';
            table_enlace_producto += '</th>';
            table_enlace_producto += "<th class='text-center'>";
              table_enlace_producto += '<button type="button" id="btn-finalizar_item_proveedor' + id_item + '" data-name_item="' + detalle[i]['Txt_Producto'] + '" data-id_pedido_cabecera="' + response.ID_Pedido_Cabecera + '" data-id="' + id_item + '" data-correlativo="' + response.sCorrelativoCotizacion + '" class="text-left btn btn-primary btn-finalizar_item_proveedor"> Finalizar </button>';
            table_enlace_producto += '</th>';
          +"</tr>";
          ID_Entidad = detalle[i].ID_Entidad_Proveedor;
          ++iCounterSupplier;
        }

        table_enlace_producto +=
        "<tr id='tr_enlace_producto" + id_item + "'>"
          + "<td style='display:none;' class='text-left td-id_item'>" + id_item + "</td>"
          + "<td class='text-center td-name' width='50px'>"
            + "<img style='' data-id_item='" + id_item + "' data-url_img='" + detalle[i]['Txt_Url_Imagen_Producto'] + "' src='" + detalle[i]['Txt_Url_Imagen_Producto'] + "' alt='" + detalle[i]['Txt_Producto'] + "' class='img-thumbnail img-table_item img-fluid img-resize mb-2'>";
            
          cantidad_item = ((!isNaN(cantidad_item_final_recepcion_carga) && cantidad_item_final_recepcion_carga > 0 && cantidad_item_final_recepcion_carga!='') ? cantidad_item_final_recepcion_carga : cantidad_item);

          table_enlace_producto += "</td>"
          + "<td class='text-left td-name'>" + detalle[i]['Txt_Producto'] + "</td>"
          + "<td class='text-right td-qty'  width='150px'>";
          table_enlace_producto += '<input type="text" inputmode="decimal" class="form-control input-decimal" id="input-cantidad' + id_item + '" name="addProducto[' + id_item + '][cantidad]" value="' + Math.round10(cantidad_item, -2) + '">';
          table_enlace_producto += "</td>";

          table_enlace_producto += "<td>";
          table_enlace_producto += '1';
          table_enlace_producto += "</td>";

          table_enlace_producto += "<td class='text-center'>";
            if(detalle[i]['Nu_Estado_Recepcion_Carga_Proveedor_Item']==0){//pendiente
              table_enlace_producto += '<button type="button" id="btn-confirmado_item_proveedor' + id_item + '" data-estado="1" data-name_item="' + detalle[i]['Txt_Producto'] + '" data-id_pedido_cabecera="' + response.ID_Pedido_Cabecera + '" data-id="' + id_item + '" data-correlativo="' + response.sCorrelativoCotizacion + '" class="text-left btn btn-success btn-estado_item_proveedor btn-cargando_item_proveedor' + id_item + '"> Confirmado </button>';
              table_enlace_producto += ' <button type="button" id="btn-faltante_item_proveedor' + id_item + '" data-estado="2" data-name_item="' + detalle[i]['Txt_Producto'] + '" data-id_pedido_cabecera="' + response.ID_Pedido_Cabecera + '" data-id="' + id_item + '" data-correlativo="' + response.sCorrelativoCotizacion + '" class="text-left btn btn-warning btn-estado_item_proveedor btn-cargando_item_proveedor' + id_item + '"> Faltante </button>';
            } else {
              if(detalle[i]['Nu_Estado_Recepcion_Carga_Proveedor_Item']==1){
                table_enlace_producto += '<span class="badge bg-success">Confirmado</span>';
              } else if(detalle[i]['Nu_Estado_Recepcion_Carga_Proveedor_Item']==2) {
                table_enlace_producto += '<span class="badge bg-warning">Faltante</span>';
              }
            }
          table_enlace_producto += "</td>";

          table_enlace_producto += '<input type="hidden" name="addProducto[' + id_item + '][id_item]" value="' + id_item + '">';
        table_enlace_producto += "</tr>";
        
        table_enlace_producto += "</tr>";
      }
      
      $('#span-total_cantidad_items').html(i);
      $( '#table-Producto_Recepcion_Carga' ).append(table_enlace_producto);

      $( '#span-total_cliente' ).html('$ ' + fTotalCliente.toFixed(2));

      //PAGOS
      //Ss_Pago_30_Cliente
      //Ss_Pago_100_Cliente
      //Ss_Pago_Servicio_Cliente

      //OTROS
      //Ss_Pago_Otros_Flete
      //Ss_Pago_Otros_Costo_Origen
      //Ss_Pago_Otros_Costo_Fta
      //Ss_Pago_Otros_Cuadrilla
      //Ss_Pago_Otros_Costos
      $( '#span-saldo_cliente' ).html('$ ' + (fTotalCliente - (parseFloat(response.Ss_Pago_30_Cliente) + parseFloat(response.Ss_Pago_100_Cliente) + parseFloat(response.Ss_Pago_Servicio_Cliente))));
      
      //Date picker invoice
      $( '.input-datepicker-today-to-more' ).datepicker({
        autoclose : true,
        startDate : new Date(fYear, fToday.getMonth(), fDay),
        todayHighlight  : true,
        dateFormat: 'dd/mm/yyyy',
        format: 'dd/mm/yyyy',
      });
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

function coordinarPagosProveedor(ID){
  $( '.div-Listar' ).hide();
  
  $( '#form-pedido' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();

  $( '.div-Compuesto' ).show();
	$( '#table-Producto_Enlace tbody' ).empty();
  $( '#table-Producto_Enlace' ).show();

  $( '.div-Producto_Recepcion_Carga' ).hide();
	$( '#table-Producto_Recepcion_Carga tbody' ).empty();

  $( '.div-Invoice_Proveedor' ).hide();
	$( '#table-Invoice_Proveedor tbody' ).empty();

  //$('#span-id_pedido').html('Nro. ' + ID);

  url = base_url + 'AgenteCompra/PedidosAprobadosPagados/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      console.log(response);
      var detalle = response;
      response = response[0];

      $('#span-id_pedido').html(response.sCorrelativoCotizacion);
      
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
      
      //$( '#btn-excel_order_tracking' ).data('id_pedido', response.ID_Pedido_Cabecera);
      $( '#btn-excel_order_tracking' ).attr('data-id_pedido', response.ID_Pedido_Cabecera); // sets 

      $('#btn-descargar_pago_30').hide();
      $('#span-pago_30').html('');
      if( response.Txt_Url_Pago_30_Cliente != '' && response.Txt_Url_Pago_30_Cliente != null ){
        $('#btn-descargar_pago_30').show();
        $('#btn-descargar_pago_30').removeClass('d-none');

        $('#span-pago_30').html('$ ' + response.Ss_Pago_30_Cliente);
      }

      $('#btn-descargar_pago_100').hide();
      $('#span-pago_100').html('');
      if( response.Txt_Url_Pago_100_Cliente != '' && response.Txt_Url_Pago_100_Cliente != null ){
        $('#btn-descargar_pago_100').show();
        $('#btn-descargar_pago_100').removeClass('d-none');

        $('#span-pago_100').html('$ ' + response.Ss_Pago_100_Cliente);
      }

      $('#btn-descargar_pago_servicio').hide();
      $('#span-pago_servicio').html('');
      if( response.Txt_Url_Pago_Servicio_Cliente != '' && response.Txt_Url_Pago_Servicio_Cliente != null ){
        $('#btn-descargar_pago_servicio').show();
        $('#btn-descargar_pago_servicio').removeClass('d-none');

        $('#span-pago_servicio').html('$ ' + response.Ss_Pago_Servicio_Cliente);
      }

      $('#btn-descargar_flete').hide();
      $('#span-flete').html('');
      if( response.Txt_Url_Pago_Otros_Flete != '' && response.Txt_Url_Pago_Otros_Flete != null ){
        $('#btn-descargar_flete').show();
        $('#btn-descargar_flete').removeClass('d-none');

        $('#span-flete').html('$ ' + response.Ss_Pago_Otros_Flete);
      }

      $('#btn-descargar_costo_origen').hide();
      $('#span-costo_origen').html('');
      if( response.Txt_Url_Pago_Otros_Costo_Origen != '' && response.Txt_Url_Pago_Otros_Costo_Origen != null ){
        $('#btn-descargar_costo_origen').show();
        $('#btn-descargar_costo_origen').removeClass('d-none');

        $('#span-costo_origen').html('$ ' + response.Ss_Pago_Otros_Costo_Origen);
      }

      $('#btn-descargar_fta').hide();
      $('#span-fta').html('');
      if( response.Txt_Url_Pago_Otros_Costo_Fta != '' && response.Txt_Url_Pago_Otros_Costo_Fta != null ){
        $('#btn-descargar_fta').show();
        $('#btn-descargar_fta').removeClass('d-none');

        $('#span-fta').html('$ ' + response.Ss_Pago_Otros_Costo_Fta);
      }

      $('#btn-descargar_pago_cuadrilla').hide();
      $('#span-cuadrilla').html('');
      if( response.Txt_Url_Pago_Otros_Cuadrilla != '' && response.Txt_Url_Pago_Otros_Cuadrilla != null ){
        $('#btn-descargar_pago_cuadrilla').show();
        $('#btn-descargar_pago_cuadrilla').removeClass('d-none');

        $('#span-cuadrilla').html('$ ' + response.Ss_Pago_Otros_Cuadrilla);
      }

      $('#btn-descargar_otros_costos').hide();
      $('#span-otros_costo').html('');
      if( response.Txt_Url_Pago_Otros_Costos != '' && response.Txt_Url_Pago_Otros_Costos != null ){
        $('#btn-descargar_otros_costos').show();
        $('#btn-descargar_otros_costos').removeClass('d-none');

        $('#span-otros_costo').html('$ ' + response.Ss_Pago_Otros_Costos);
      }

      var sNombreEstado = '<span class="badge badge-pill badge-secondary">Pendiente</span>';
      if(response.Nu_Estado_Pedido == 2)
        sNombreEstado = '<span class="badge badge-pill badge-primary">Confirmado</span>';
      else if(response.Nu_Estado_Pedido == 3)
        sNombreEstado = '<span class="badge badge-pill badge-success">Entregado</span>';
      else if(response.Nu_Estado_Pedido == 4)
        sNombreEstado = '<span class="badge badge-pill badge-danger">Confirmado</span>';
      $( '#div-estado' ).html(sNombreEstado);
      
      var iCounterSupplier = 1, table_enlace_producto = "", iDiasVencimiento = 0, sClassColorTr = "", fTotalCliente = 0, ID_Entidad = '';
      for (i = 0; i < detalle.length; i++) {
        var cantidad_item = parseFloat(detalle[i]['Qt_Producto']);
        var precio_china = parseFloat(detalle[i]['Ss_Precio']);

        fTotalCliente += (cantidad_item * (precio_china * parseFloat(response.Ss_Tipo_Cambio)));

        var id_item = detalle[i]['ID_Pedido_Detalle_Producto_Proveedor'];
        var voucher_1 = detalle[i]['Txt_Url_Archivo_Pago_1_Proveedor'];
        var voucher_2 = detalle[i]['Txt_Url_Archivo_Pago_2_Proveedor'];
        //max-height: 350px;width: 100%; cursor:pointer

        var fTotal = (cantidad_item * precio_china);
        var Ss_Pago_1_Proveedor = parseFloat(detalle[i]['Ss_Pago_1_Proveedor']);
        var Ss_Pago_2_Proveedor = parseFloat(detalle[i]['Ss_Pago_2_Proveedor']);

        sClassColorTr = '';
        iDiasVencimiento = 0;
        if((detalle[i]['Fe_Entrega_Proveedor'] != '' && detalle[i]['Fe_Entrega_Proveedor'] != null)){
          var fechaInicio = new Date(fYear + '-' + fMonth + '-' + fDay).getTime();
          var fechaFin    = new Date(detalle[i]['Fe_Entrega_Proveedor']).getTime();

          var diff = fechaFin - fechaInicio;
          iDiasVencimiento = (diff / (1000*60*60*24));// --> milisegundos -> segundos -> minutos -> horas -> días
          if(iDiasVencimiento<5)
            sClassColorTr = 'table-warning';
        }

        var fecha_entrega_proveedor = ( (detalle[i]['Fe_Entrega_Proveedor'] != '' && detalle[i]['Fe_Entrega_Proveedor'] != null) ? ParseDateString(detalle[i]['Fe_Entrega_Proveedor'], 'fecha_bd', '-') : '');

        if (ID_Entidad != detalle[i].ID_Entidad_Proveedor) {
          table_enlace_producto +=
          "<tr class='table-active'>"
            +"<th class='text-right'>" + iCounterSupplier + ". Supplier</th>";
            table_enlace_producto += "<th class='text-left'>";
            table_enlace_producto += detalle[i].No_Contacto_Proveedor + "&nbsp;&nbsp;&nbsp;";
            if(detalle[i]['Txt_Url_Imagen_Proveedor'] != '' && detalle[i]['Txt_Url_Imagen_Proveedor'] != null){
              table_enlace_producto += "<img style='' data-id_item='" + id_item + "' data-url_img='" + detalle[i]['Txt_Url_Imagen_Proveedor'] + "' src='" + detalle[i]['Txt_Url_Imagen_Proveedor'] + "' alt='" + detalle[i]['Txt_Producto'] + "' class='img-thumbnail img-table_item img-fluid img-resize_supplier mb-2'>";
            }
            table_enlace_producto += "</th>";
            table_enlace_producto += "<th class='text-left'>";
            table_enlace_producto += 'agregar datos <button type="button" class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="editarProveedor(' + detalle[i].ID_Entidad_Proveedor + ', ' + id_item + ')"><i class="far fa-edit" aria-hidden="true"></i></button>';
            table_enlace_producto += "</th>";
            table_enlace_producto += "<th class='text-left' colspan='10'>";
            table_enlace_producto += 'Costo delivery: ' + detalle[i]['Ss_Costo_Delivery'];
            table_enlace_producto += "</th>";
          +"</tr>";
          ID_Entidad = detalle[i].ID_Entidad_Proveedor;
          ++iCounterSupplier;
        }

        table_enlace_producto +=
        "<tr id='tr_enlace_producto" + id_item + "'>"
          + "<td style='display:none;' class='text-left td-id_item'>" + id_item + "</td>"
          + "<td class='text-center td-name' width='10%'>"
            + "<img style='' data-id_item='" + id_item + "' data-url_img='" + detalle[i]['Txt_Url_Imagen_Producto'] + "' src='" + detalle[i]['Txt_Url_Imagen_Producto'] + "' alt='" + detalle[i]['Txt_Producto'] + "' class='img-thumbnail img-table_item img-fluid img-resize_v2 mb-2'>";
            
          table_enlace_producto += "</td>"
          + "<td class='text-left td-name'>" + detalle[i]['Txt_Producto'] + "</td>"
          + "<td class='text-right td-qty'>" + Math.round10(cantidad_item, -2) + "</td>"
          + "<td class='text-right td-price'>" + Math.round10(precio_china, -2) + "</td>"
          +"<td class='text-right td-amount'>" + Math.round10(fTotal, -2) + "</td>"
          //+"<td class='text-right td-pay1'>" + Math.round10(Ss_Pago_1_Proveedor, -2) + "</td>"
          //+"<td class='text-right td-pay1'>" + Math.round10(detalle[i]['Ss_Pago_Importe_1'], -2) + "</td>"
          //+"<td class='text-right td-pay1'>" + Math.round10(detalle[i]['Ss_Pago_Importe_2'], -2) + "</td>"
          //+"<td class='text-right td-balance'>" + Math.round10(fTotal - Ss_Pago_1_Proveedor, -2) + "</td>"
          //+"<td class='text-right td-pay2'>" + Math.round10(Ss_Pago_2_Proveedor, -2) + "</td>"
          +"<td class='text-left td-delivery_date'>" + detalle[i]['Nu_Dias_Delivery'] + "</td>"
          //+"<td class='text-left td-costo_delivery'>" + detalle[i]['Ss_Costo_Delivery'] + "</td>";

          table_enlace_producto += "<td class='text-left td-supplier'>";
            table_enlace_producto += '<div class="input-group date" style="width:100%">';
              table_enlace_producto += '<input type="text" id="txt-fecha_entrega_proveedor'+i+'" name="addProducto[' + id_item + '][fecha_entrega_proveedor]" class="form-control input-datepicker-today-to-more required" value="' + fecha_entrega_proveedor + '">';
            table_enlace_producto += '</div>';
          table_enlace_producto += "</td>";

          table_enlace_producto += "<td class='text-left'>";
            table_enlace_producto += '<button type="button" id="btn-cambiar_item_proveedor' + id_item + '" data-name_item="' + detalle[i]['Txt_Producto'] + '" data-id_pedido_cabecera="' + response.ID_Pedido_Cabecera + '" data-id="' + id_item + '" data-correlativo="' + response.sCorrelativoCotizacion + '" class="text-left btn btn-primary btn-block btn-cambiar_item_proveedor"> change </button>';
          table_enlace_producto += "</td>";

          //table_enlace_producto += "<td class='text-left td-supplier'>" + detalle[i]['No_Contacto_Proveedor'] + "</td>"
          /*
          table_enlace_producto += "<td class='text-left td-phone'>";
          if(detalle[i]['Txt_Url_Imagen_Proveedor'] != '' && detalle[i]['Txt_Url_Imagen_Proveedor'] != null){
            table_enlace_producto += "<img style='' data-id_item='" + id_item + "' data-url_img='" + detalle[i]['Txt_Url_Imagen_Proveedor'] + "' src='" + detalle[i]['Txt_Url_Imagen_Proveedor'] + "' alt='" + detalle[i]['Txt_Producto'] + "' class='img-thumbnail img-table_item img-fluid img-resize_v2 mb-2'>";
          }
          table_enlace_producto += "</td>";
          */
          
          table_enlace_producto += "<td class='text-left td-eliminar'>";
            table_enlace_producto += '<button type="button" id="btn-eliminar_item_proveedor' + id_item + '" data-name_item="' + detalle[i]['Txt_Producto'] + '" data-id_pedido_cabecera="' + response.ID_Pedido_Cabecera + '" data-id="' + id_item + '" data-correlativo="' + response.sCorrelativoCotizacion + '" class="text-left btn btn-danger btn-block btn-eliminar_item_proveedor"> X </button>';
          table_enlace_producto += "</td>";

          table_enlace_producto += '<input type="hidden" name="addProducto[' + id_item + '][id_item]" value="' + id_item + '">';
        table_enlace_producto += "</tr>";
        
        /*
        table_enlace_producto += "<tr>";
          table_enlace_producto += "<td class='text-left' colspan='12'>";
            table_enlace_producto += '<button type="button" id="btn-cambiar_item_proveedor' + id_item + '" data-name_item="' + detalle[i]['Txt_Producto'] + '" data-id_pedido_cabecera="' + response.ID_Pedido_Cabecera + '" data-id="' + id_item + '" data-correlativo="' + response.sCorrelativoCotizacion + '" class="text-left btn btn-danger btn-block btn-cambiar_item_proveedor"> Cambiar proveedor </button>';
          table_enlace_producto += "</td>";
        table_enlace_producto += "</tr>";
        */
      }
      
      $('#span-total_cantidad_items').html(i);
      $( '#table-Producto_Enlace' ).append(table_enlace_producto);

      $( '#span-total_cliente' ).html('$ ' + fTotalCliente.toFixed(2));

      //PAGOS
      //Ss_Pago_30_Cliente
      //Ss_Pago_100_Cliente
      //Ss_Pago_Servicio_Cliente

      //OTROS
      //Ss_Pago_Otros_Flete
      //Ss_Pago_Otros_Costo_Origen
      //Ss_Pago_Otros_Costo_Fta
      //Ss_Pago_Otros_Cuadrilla
      //Ss_Pago_Otros_Costos
      $( '#span-saldo_cliente' ).html('$ ' + (fTotalCliente - (parseFloat(response.Ss_Pago_30_Cliente) + parseFloat(response.Ss_Pago_100_Cliente) + parseFloat(response.Ss_Pago_Servicio_Cliente))));
      
      //Date picker invoice
      $( '.input-datepicker-today-to-more' ).datepicker({
        autoclose : true,
        startDate : new Date(fYear, fToday.getMonth(), fDay),
        todayHighlight  : true,
        dateFormat: 'dd/mm/yyyy',
        format: 'dd/mm/yyyy',
      });
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

function verPedido(ID){
  $( '.div-Listar' ).hide();
  
  $( '#form-pedido' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();

  $( '.div-Compuesto' ).show();
	$( '#table-Producto_Enlace tbody' ).empty();
  $( '#table-Producto_Enlace' ).show();

  $( '.div-Producto_Recepcion_Carga' ).hide();
	$( '#table-Producto_Recepcion_Carga tbody' ).empty();

  $( '.div-Invoice_Proveedor' ).hide();
	$( '#table-Invoice_Proveedor tbody' ).empty();

  //$('#span-id_pedido').html('Nro. ' + ID);

  url = base_url + 'AgenteCompra/PedidosAprobadosPagados/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      console.log(response);
      var detalle = response;
      response = response[0];

      $('#span-id_pedido').html(response.sCorrelativoCotizacion);
      
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
      
      //$( '#btn-excel_order_tracking' ).data('id_pedido', response.ID_Pedido_Cabecera);
      $( '#btn-excel_order_tracking' ).attr('data-id_pedido', response.ID_Pedido_Cabecera); // sets 

      $('#btn-descargar_pago_30').hide();
      $('#span-pago_30').html('');
      if( response.Txt_Url_Pago_30_Cliente != '' && response.Txt_Url_Pago_30_Cliente != null ){
        $('#btn-descargar_pago_30').show();
        $('#btn-descargar_pago_30').removeClass('d-none');

        $('#span-pago_30').html('$ ' + response.Ss_Pago_30_Cliente);
      }

      $('#btn-descargar_pago_100').hide();
      $('#span-pago_100').html('');
      if( response.Txt_Url_Pago_100_Cliente != '' && response.Txt_Url_Pago_100_Cliente != null ){
        $('#btn-descargar_pago_100').show();
        $('#btn-descargar_pago_100').removeClass('d-none');

        $('#span-pago_100').html('$ ' + response.Ss_Pago_100_Cliente);
      }

      $('#btn-descargar_pago_servicio').hide();
      $('#span-pago_servicio').html('');
      if( response.Txt_Url_Pago_Servicio_Cliente != '' && response.Txt_Url_Pago_Servicio_Cliente != null ){
        $('#btn-descargar_pago_servicio').show();
        $('#btn-descargar_pago_servicio').removeClass('d-none');

        $('#span-pago_servicio').html('$ ' + response.Ss_Pago_Servicio_Cliente);
      }

      $('#btn-descargar_flete').hide();
      $('#span-flete').html('');
      if( response.Txt_Url_Pago_Otros_Flete != '' && response.Txt_Url_Pago_Otros_Flete != null ){
        $('#btn-descargar_flete').show();
        $('#btn-descargar_flete').removeClass('d-none');

        $('#span-flete').html('$ ' + response.Ss_Pago_Otros_Flete);
      }

      $('#btn-descargar_costo_origen').hide();
      $('#span-costo_origen').html('');
      if( response.Txt_Url_Pago_Otros_Costo_Origen != '' && response.Txt_Url_Pago_Otros_Costo_Origen != null ){
        $('#btn-descargar_costo_origen').show();
        $('#btn-descargar_costo_origen').removeClass('d-none');

        $('#span-costo_origen').html('$ ' + response.Ss_Pago_Otros_Costo_Origen);
      }

      $('#btn-descargar_fta').hide();
      $('#span-fta').html('');
      if( response.Txt_Url_Pago_Otros_Costo_Fta != '' && response.Txt_Url_Pago_Otros_Costo_Fta != null ){
        $('#btn-descargar_fta').show();
        $('#btn-descargar_fta').removeClass('d-none');

        $('#span-fta').html('$ ' + response.Ss_Pago_Otros_Costo_Fta);
      }

      $('#btn-descargar_pago_cuadrilla').hide();
      $('#span-cuadrilla').html('');
      if( response.Txt_Url_Pago_Otros_Cuadrilla != '' && response.Txt_Url_Pago_Otros_Cuadrilla != null ){
        $('#btn-descargar_pago_cuadrilla').show();
        $('#btn-descargar_pago_cuadrilla').removeClass('d-none');

        $('#span-cuadrilla').html('$ ' + response.Ss_Pago_Otros_Cuadrilla);
      }

      $('#btn-descargar_otros_costos').hide();
      $('#span-otros_costo').html('');
      if( response.Txt_Url_Pago_Otros_Costos != '' && response.Txt_Url_Pago_Otros_Costos != null ){
        $('#btn-descargar_otros_costos').show();
        $('#btn-descargar_otros_costos').removeClass('d-none');

        $('#span-otros_costo').html('$ ' + response.Ss_Pago_Otros_Costos);
      }

      var sNombreEstado = '<span class="badge badge-pill badge-secondary">Pendiente</span>';
      if(response.Nu_Estado_Pedido == 2)
        sNombreEstado = '<span class="badge badge-pill badge-primary">Confirmado</span>';
      else if(response.Nu_Estado_Pedido == 3)
        sNombreEstado = '<span class="badge badge-pill badge-success">Entregado</span>';
      else if(response.Nu_Estado_Pedido == 4)
        sNombreEstado = '<span class="badge badge-pill badge-danger">Confirmado</span>';
      $( '#div-estado' ).html(sNombreEstado);
      
      var table_enlace_producto = "", iDiasVencimiento = 0, sClassColorTr = "", fTotalCliente = 0, ID_Entidad = 0;
      for (i = 0; i < detalle.length; i++) {
        var cantidad_item = parseFloat(detalle[i]['Qt_Producto']);
        var precio_china = parseFloat(detalle[i]['Ss_Precio']);

        fTotalCliente += (cantidad_item * (precio_china * parseFloat(response.Ss_Tipo_Cambio)));

        var id_item = detalle[i]['ID_Pedido_Detalle_Producto_Proveedor'];
        var voucher_1 = detalle[i]['Txt_Url_Archivo_Pago_1_Proveedor'];
        var voucher_2 = detalle[i]['Txt_Url_Archivo_Pago_2_Proveedor'];
        //max-height: 350px;width: 100%; cursor:pointer

        var fTotal = (cantidad_item * precio_china);
        var Ss_Pago_1_Proveedor = parseFloat(detalle[i]['Ss_Pago_1_Proveedor']);
        var Ss_Pago_2_Proveedor = parseFloat(detalle[i]['Ss_Pago_2_Proveedor']);

        sClassColorTr = '';
        iDiasVencimiento = 0;
        if((detalle[i]['Fe_Entrega_Proveedor'] != '' && detalle[i]['Fe_Entrega_Proveedor'] != null)){
          var fechaInicio = new Date(fYear + '-' + fMonth + '-' + fDay).getTime();
          var fechaFin    = new Date(detalle[i]['Fe_Entrega_Proveedor']).getTime();

          var diff = fechaFin - fechaInicio;
          iDiasVencimiento = (diff / (1000*60*60*24));// --> milisegundos -> segundos -> minutos -> horas -> días
          if(iDiasVencimiento<5)
            sClassColorTr = 'table-warning';
        }

        var fecha_entrega_proveedor = ( (detalle[i]['Fe_Entrega_Proveedor'] != '' && detalle[i]['Fe_Entrega_Proveedor'] != null) ? ParseDateString(detalle[i]['Fe_Entrega_Proveedor'], 'fecha_bd', '-') : '');

        if (ID_Entidad != detalle[i].ID_Entidad_Proveedor) {
          table_enlace_producto +=
          "<tr>"
            +"<th class='text-right'>Supplier </th>"
            +"<th class='text-left' colspan='14'>" + detalle[i].No_Contacto_Proveedor + "</th>"
          +"</tr>";
          ID_Entidad = detalle[i].ID_Entidad_Proveedor;
        }

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
          +"<td class='text-left td-costo_delivery'>" + detalle[i]['Ss_Costo_Delivery'] + "</td>";

          table_enlace_producto += "<td class='text-left td-supplier'>";
            table_enlace_producto += '<div class="input-group date" style="width:100%">';
              table_enlace_producto += '<input type="text" id="txt-fecha_entrega_proveedor'+i+'" name="addProducto[' + id_item + '][fecha_entrega_proveedor]" class="form-control input-datepicker-today-to-more required" value="' + fecha_entrega_proveedor + '">';
            table_enlace_producto += '</div>';
          table_enlace_producto += "</td>";

          table_enlace_producto += "<td class='text-left td-supplier'>" + detalle[i]['No_Contacto_Proveedor'] + "</td>"
          +"<td class='text-left td-phone'>";
          if(detalle[i]['Txt_Url_Imagen_Proveedor'] != '' && detalle[i]['Txt_Url_Imagen_Proveedor'] != null){
            table_enlace_producto += "<img style='' data-id_item='" + id_item + "' data-url_img='" + detalle[i]['Txt_Url_Imagen_Proveedor'] + "' src='" + detalle[i]['Txt_Url_Imagen_Proveedor'] + "' alt='" + detalle[i]['Txt_Producto'] + "' class='img-thumbnail img-table_item img-fluid img-resize mb-2'>";
          }
          table_enlace_producto += "</td>";
          
          table_enlace_producto += "<td class='text-left td-eliminar'>";
            table_enlace_producto += '<button type="button" id="btn-eliminar_item_proveedor' + id_item + '" data-name_item="' + detalle[i]['Txt_Producto'] + '" data-id_pedido_cabecera="' + response.ID_Pedido_Cabecera + '" data-id="' + id_item + '" data-correlativo="' + response.sCorrelativoCotizacion + '" class="text-left btn btn-danger btn-block btn-eliminar_item_proveedor"> X </button>';
          table_enlace_producto += "</td>";

          table_enlace_producto += '<input type="hidden" name="addProducto[' + id_item + '][id_item]" value="' + id_item + '">';
        table_enlace_producto += "</tr>";
        
        table_enlace_producto +=
        "<tr><td class='text-left' colspan='14'>"
          if( voucher_1 == '' || voucher_1 == null ){
            table_enlace_producto += '<button type="button" id="btn-agregar_pago_proveedor' + id_item + '" data-tipo_pago="1" data-id="' + id_item + '" class="text-left btn btn-primary btn-block btn-agregar_pago_proveedor" data-id_empresa="' + response.ID_Empresa + '" data-id_organizacion="' + response.ID_Organizacion + '" data-id_pedido_cabecera="' + response.ID_Pedido_Cabecera + '" data-id_pedido_detalle="' + response.ID_Pedido_Detalle + '" data-correlativo="' + response.sCorrelativoCotizacion + '"><i class="fas fa-money-bill-alt"></i>&nbsp; Pagar Proveedor</button>';
          } else {
            table_enlace_producto += '<button type="button" id="btn-ver_pago_proveedor' + id_item + '" data-url_img="' + voucher_1 + '" data-id="' + id_item + '" class="text-left btn btn-secondary btn-block btn-ver_pago_proveedor" data-id_empresa="' + response.ID_Empresa + '" data-id_organizacion="' + response.ID_Organizacion + '" data-id_pedido_cabecera="' + response.ID_Pedido_Cabecera + '" data-id_pedido_detalle="' + response.ID_Pedido_Detalle + '" data-correlativo="' + response.sCorrelativoCotizacion + '"><i class="fas fa-money-bill-alt"></i>&nbsp; Pago ¥ ' + Ss_Pago_1_Proveedor +  ' (Deposit_#1)</button>';
            if( voucher_2 == '' || voucher_2 == null ){
              table_enlace_producto += '<button type="button" id="btn-agregar_pago_proveedor' + id_item + '" data-tipo_pago="2" data-id="' + id_item + '" class="text-left btn btn-primary btn-block btn-agregar_pago_proveedor" data-id_empresa="' + response.ID_Empresa + '" data-id_organizacion="' + response.ID_Organizacion + '" data-id_pedido_cabecera="' + response.ID_Pedido_Cabecera + '" data-id_pedido_detalle="' + response.ID_Pedido_Detalle + '" data-correlativo="' + response.sCorrelativoCotizacion + '"><i class="fas fa-money-bill-alt"></i>&nbsp; Pagar Proveedor</button>';
            } else {
              table_enlace_producto += '<button type="button" id="btn-ver_pago_proveedor' + id_item + '" data-url_img="' + voucher_2 + '" data-id="' + id_item + '" class="text-left btn btn-secondary btn-block btn-ver_pago_proveedor" data-id_empresa="' + response.ID_Empresa + '" data-id_organizacion="' + response.ID_Organizacion + '" data-id_pedido_cabecera="' + response.ID_Pedido_Cabecera + '" data-id_pedido_detalle="' + response.ID_Pedido_Detalle + '" data-correlativo="' + response.sCorrelativoCotizacion + '"><i class="fas fa-money-bill-alt"></i>&nbsp; Pago ¥ ' + Ss_Pago_2_Proveedor + ' (Deposit_#2)</button>';
            }
          }
        table_enlace_producto +=
        "</td></tr>";
      }
      
      $('#span-total_cantidad_items').html(i);
      $( '#table-Producto_Enlace' ).append(table_enlace_producto);

      $( '#span-total_cliente' ).html('$ ' + fTotalCliente.toFixed(2));

      //PAGOS
      //Ss_Pago_30_Cliente
      //Ss_Pago_100_Cliente
      //Ss_Pago_Servicio_Cliente

      //OTROS
      //Ss_Pago_Otros_Flete
      //Ss_Pago_Otros_Costo_Origen
      //Ss_Pago_Otros_Costo_Fta
      //Ss_Pago_Otros_Cuadrilla
      //Ss_Pago_Otros_Costos
      $( '#span-saldo_cliente' ).html('$ ' + (fTotalCliente - (parseFloat(response.Ss_Pago_30_Cliente) + parseFloat(response.Ss_Pago_100_Cliente) + parseFloat(response.Ss_Pago_Servicio_Cliente))));
      
      //Date picker invoice
      $( '.input-datepicker-today-to-more' ).datepicker({
        autoclose : true,
        startDate : new Date(fYear, fToday.getMonth(), fDay),
        todayHighlight  : true,
        dateFormat: 'dd/mm/yyyy',
        format: 'dd/mm/yyyy',
      });
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

function cambiarEstado(ID, Nu_Estado, id_pedido_cabecera, sCorrelativo) {
  var $modal_delete = $('#modal-message-delete');
  $modal_delete.modal('show');

  $('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
  $('.modal-message-delete').addClass('modal-success');

  var sNombreEstado = 'Pago 30%';
  if(Nu_Estado==7)
    sNombreEstado = 'Pago 70%';
  else if(Nu_Estado==9)
    sNombreEstado = 'Pago servicio';
  else if(Nu_Estado==3)
    sNombreEstado = 'Volver a Garantizado';

  $('#modal-title').html('¿Deseas cambiar a <strong>' + sNombreEstado + '</strong>?');

  $('#btn-cancel-delete').off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $('#btn-save-delete').off('click').click(function () {
    
    $( '#btn-save-delete' ).text('');
    $( '#btn-save-delete' ).attr('disabled', true);
    $( '#btn-save-delete' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

    url = base_url + 'AgenteCompra/PedidosAprobadosPagados/cambiarEstado/' + ID + '/' + Nu_Estado + '/' + sCorrelativo;
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
    
    url = base_url + 'AgenteCompra/PedidosAprobadosPagados/crudPedidoGrupal';
    $.ajax({
      type		  : 'POST',
      dataType	: 'JSON',
      url		    : url,
      data		  : $('#form-pedido').serialize(),
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
  var ID = $('#btn-excel_order_tracking').data('id_pedido');
  
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
  url = base_url + 'AgenteCompra/PedidosAprobadosPagados/generarExcelOrderTracking/' + ID;
  window.open(url,'_blank');
}

function loadFile(event, id){
  var output = document.getElementById('img_producto-preview' + id);
  output.src = URL.createObjectURL(event.target.files[0]);
  output.onload = function() {
    URL.revokeObjectURL(output.src) // free memory
  }
}

function cambiarEstadoChina(ID, Nu_Estado, iIdCorrelativo, sCorrelativo) {
  var $modal_delete = $('#modal-message-delete');
  $modal_delete.modal('show');

  $('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
  $('.modal-message-delete').addClass('modal-success');

  var sNombreEstado = 'Producción';
  if(Nu_Estado==5)
    sNombreEstado = 'Inspección';
  else if(Nu_Estado==6)
    sNombreEstado = 'Entregado';

  $('#modal-title').html('¿Deseas cambiar estado a <strong>' + sNombreEstado + '</strong>?');

  $('#btn-cancel-delete').off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $('#btn-save-delete').off('click').click(function () {
    
    $( '#btn-save-delete' ).text('');
    $( '#btn-save-delete' ).attr('disabled', true);
    $( '#btn-save-delete' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

    url = base_url + 'AgenteCompra/PedidosAprobadosPagados/cambiarEstadoChina/' + ID + '/' + Nu_Estado + '/' + sCorrelativo;
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

  $( '.div-Compuesto' ).show();
	$( '#table-Producto_Enlace tbody' ).empty();
  $( '#table-Producto_Enlace' ).show();

  $( '.div-Producto_Recepcion_Carga' ).hide();
	$( '#table-Producto_Recepcion_Carga tbody' ).empty();

  $( '.div-Invoice_Proveedor' ).hide();
	$( '#table-Invoice_Proveedor tbody' ).empty();
  
  //$('#span-id_pedido').html('Nro. ' + ID);

  url = base_url + 'AgenteCompra/PedidosAprobadosPagados/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      console.log(response);
      var detalle = response;
      response = response[0];

      $('#span-id_pedido').html(response.sCorrelativoCotizacion);

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

      var table_enlace_producto = "", iDiasVencimiento=0, sClassColorTr = '';
      for (i = 0; i < detalle.length; i++) {
        var cantidad_item = parseFloat(detalle[i]['Qt_Producto']);
        var precio_china = parseFloat(detalle[i]['Ss_Precio']);

        var id_item = detalle[i]['ID_Pedido_Detalle_Producto_Proveedor'];
        var voucher_1 = detalle[i]['Txt_Url_Archivo_Pago_1_Proveedor'];
        var voucher_2 = detalle[i]['Txt_Url_Archivo_Pago_2_Proveedor'];
        var fTotal = (precio_china * cantidad_item);

        var Ss_Pago_1_Proveedor = parseFloat(detalle[i]['Ss_Pago_1_Proveedor']);
        var Ss_Pago_2_Proveedor = parseFloat(detalle[i]['Ss_Pago_2_Proveedor']);
        
        sClassColorTr = '';
        iDiasVencimiento = 0;
        if((detalle[i]['Fe_Entrega_Proveedor'] != '' && detalle[i]['Fe_Entrega_Proveedor'] != null)){
          var fechaInicio = new Date(fYear + '-' + fMonth + '-' + fDay).getTime();
          var fechaFin    = new Date(detalle[i]['Fe_Entrega_Proveedor']).getTime();

          var diff = fechaFin - fechaInicio;
          iDiasVencimiento = (diff / (1000*60*60*24));// --> milisegundos -> segundos -> minutos -> horas -> días
          if(iDiasVencimiento<5)
            sClassColorTr = 'table-warning';
        }

        var fecha_entrega_proveedor = ( (detalle[i]['Fe_Entrega_Proveedor'] != '' && detalle[i]['Fe_Entrega_Proveedor'] != null) ? ParseDateString(detalle[i]['Fe_Entrega_Proveedor'], 'fecha_bd', '-') : '');

        table_enlace_producto +=
        "<tr id='tr_enlace_producto" + id_item + "' class='" + sClassColorTr + "'>"
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
          +"<td class='text-left td-costo_delivery'>" + detalle[i]['Ss_Costo_Delivery'] + "</td>";
          
          table_enlace_producto += "<td class='text-left td-supplier'>";
            table_enlace_producto += '<div class="input-group date" style="width:100%">';
              table_enlace_producto += '<input type="text" id="txt-fecha_entrega_proveedor'+i+'" name="addProducto[' + id_item + '][fecha_entrega_proveedor]" class="form-control input-datepicker-today-to-more required" value="' + fecha_entrega_proveedor + '">';
            table_enlace_producto += '</div>';
          table_enlace_producto += "</td>";

          table_enlace_producto += "<td class='text-left td-supplier'>" + detalle[i]['No_Contacto_Proveedor'] + "</td>"
          +"<td class='text-left td-phone'>";
          if(detalle[i]['Txt_Url_Imagen_Proveedor'] != '' && detalle[i]['Txt_Url_Imagen_Proveedor'] != null){
            table_enlace_producto += "<img style='' data-id_item='" + id_item + "' data-url_img='" + detalle[i]['Txt_Url_Imagen_Proveedor'] + "' src='" + detalle[i]['Txt_Url_Imagen_Proveedor'] + "' alt='" + detalle[i]['Txt_Producto'] + "' class='img-thumbnail img-table_item img-fluid img-resize mb-2'>";
          }
          table_enlace_producto += "</td>";

          
          table_enlace_producto += "<td class='text-left td-eliminar'>";
            table_enlace_producto += '<button type="button" id="btn-eliminar_item_proveedor' + id_item + '" data-name_item="' + detalle[i]['Txt_Producto'] + '" data-id_pedido_cabecera="' + response.ID_Pedido_Cabecera + '" data-id="' + id_item + '" data-correlativo="' + response.sCorrelativoCotizacion + '" class="text-left btn btn-danger btn-block btn-eliminar_item_proveedor"> X </button>';
          table_enlace_producto += "</td>";

          table_enlace_producto += '<input type="hidden" name="addProducto[' + id_item + '][id_item]" value="' + id_item + '">';
        table_enlace_producto += "</tr>";

          //table_enlace_producto += '<input type="hidden" name="addProducto[' + id_item + '][id_item]" value="' + id_item + '">';
        //table_enlace_producto += "</tr>";

        table_enlace_producto +=
        "<tr><td class='text-left' colspan='15'>"
          if(detalle[i]['Nu_Agrego_Inspeccion']==0) {//0=No
            table_enlace_producto += '<button type="button" id="btn-agregar_inspeccion' + id_item + '" data-tipo_pago="1" data-id="' + id_item + '" data-correlativo="' + response.sCorrelativoCotizacion + '" class="text-left btn btn-primary btn-block btn-agregar_inspeccion" data-id_empresa="' + response.ID_Empresa + '" data-id_organizacion="' + response.ID_Organizacion + '" data-id_pedido_cabecera="' + response.ID_Pedido_Cabecera + '" data-id_pedido_detalle="' + response.ID_Pedido_Detalle + '"><i class="fas fa-images"></i>&nbsp; Subir fotos</button>';
          } else {
            table_enlace_producto += '<button type="button" id="btn-agregar_inspeccion' + id_item + '" data-tipo_pago="1" data-id="' + id_item + '" data-correlativo="' + response.sCorrelativoCotizacion + '" class="text-left btn btn-primary btn-block btn-agregar_inspeccion" data-id_empresa="' + response.ID_Empresa + '" data-id_organizacion="' + response.ID_Organizacion + '" data-id_pedido_cabecera="' + response.ID_Pedido_Cabecera + '" data-id_pedido_detalle="' + response.ID_Pedido_Detalle + '"><i class="fas fa-images"></i>&nbsp; Subir fotos</button>';
            table_enlace_producto += '<button type="button" id="btn-ver_inspeccion' + id_item + '" onclick=verInspeccion(' + id_item + ') class="text-left btn btn-secondary btn-block btn-ver_inspeccion"><i class="fas fa-search"></i>&nbsp; Ver fotos</button>';
          }
        table_enlace_producto += "</td></tr>";
      }
      
      $('#span-total_cantidad_items').html(i);
      $( '#table-Producto_Enlace' ).append(table_enlace_producto);
      
      //Date picker invoice
      $( '.input-datepicker-today-to-more' ).datepicker({
        autoclose : true,
        startDate : new Date(fYear, fToday.getMonth(), fDay),
        todayHighlight  : true,
        dateFormat: 'dd/mm/yyyy',
        format: 'dd/mm/yyyy',
      });
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

  url = base_url + 'AgenteCompra/PedidosAprobadosPagados/ajax_edit_inspeccion/' + ID;
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

function documentoEntregado(id, sCorrelativo){
  $( '[name="documento-id_cabecera"]' ).val(id);
  $( '[name="documento-correlativo"]' ).val(sCorrelativo);

  $('#modal-documento_entrega').modal('show');
  $( '#form-documento_entrega' )[0].reset();
}

function descargarDocumentoEntregado(id){
  url = base_url + 'AgenteCompra/PedidosAprobadosPagados/descargarDocumentoEntregado/' + id;
  
  var popupwin = window.open(url);
  setTimeout(function() { popupwin.close();}, 2000);
}

function subirPago30(){
  $( '[name="pago_cliente_30-id_cabecera"]' ).val($('#txt-EID_Pedido_Cabecera').val());

  $('#modal-pago_cliente_30').modal('show');
  $( '#form-pago_cliente_30' )[0].reset();
}

function descargarPago30(){
  var id = $('#txt-EID_Pedido_Cabecera').val();
  url = base_url + 'AgenteCompra/PedidosAprobadosPagados/descargarPago30/' + id;
  
  var popupwin = window.open(url);
  setTimeout(function() { popupwin.close();}, 2000);
}

function subirPago100(){
  $( '[name="pago_cliente_100-id_cabecera"]' ).val($('#txt-EID_Pedido_Cabecera').val());

  $('#modal-pago_cliente_100').modal('show');
  $( '#form-pago_cliente_100' )[0].reset();
}

function descargarPago100(){
  var id = $('#txt-EID_Pedido_Cabecera').val();
  url = base_url + 'AgenteCompra/PedidosAprobadosPagados/descargarPago100/' + id;
  
  var popupwin = window.open(url);
  setTimeout(function() { popupwin.close();}, 2000);
}

function cambiarTipoServicio(ID, Nu_Tipo_Servicio, ID_Usuario_Interno_Empresa_China) {
  if(ID_Usuario_Interno_Empresa_China==0) {//3 - Enviado
    $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
    $('#modal-message').modal('show');

    $('#moda-message-content').addClass( 'bg-warning');
    $('.modal-title-message').html('Primero asignar Jefe de China');

    setTimeout(function () { $('#modal-message').modal('hide'); }, 3100);
  } else {
    var $modal_delete = $('#modal-message-delete');
    $modal_delete.modal('show');

    $('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
    $('.modal-message-delete').addClass('modal-success');

    var sNombreEstado = 'Trading';
    if(Nu_Tipo_Servicio==2)
      sNombreEstado = 'C. Trading';

    $('#modal-title').html('¿Deseas cambiar estado a <strong>' + sNombreEstado + '</strong>?');

    $('#btn-cancel-delete').off('click').click(function () {
      $modal_delete.modal('hide');
    });

    $('#btn-save-delete').off('click').click(function () {
      $( '#btn-save-delete' ).text('');
      $( '#btn-save-delete' ).attr('disabled', true);
      $( '#btn-save-delete' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

      url = base_url + 'AgenteCompra/PedidosAprobadosPagados/cambiarTipoServicio/' + ID + '/' + Nu_Tipo_Servicio + '/' + ID_Usuario_Interno_Empresa_China;
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
}

function clearHTMLTextArea(str){
  str=str.replace(/<br>/gi, "");
  str=str.replace(/<br\s\/>/gi, "");
  str=str.replace(/<br\/>/gi, "");
  str=str.replace(/<\/button>/gi, "");
  str=str.replace(/<br >/gi, "");
  return str;
}

function subirPagoServicio(){
  $( '[name="pago_cliente_servicio-id_cabecera"]' ).val($('#txt-EID_Pedido_Cabecera').val());

  $('#modal-pago_cliente_servicio').modal('show');
  $( '#form-pago_cliente_servicio' )[0].reset();
}

function descargarPagoServicio(){
  var id = $('#txt-EID_Pedido_Cabecera').val();
  url = base_url + 'AgenteCompra/PedidosAprobadosPagados/descargarPagoServicio/' + id;
  
  var popupwin = window.open(url);
  setTimeout(function() { popupwin.close();}, 2000);
}

function cambiarIncoterms(ID, Nu_Estado, id_pedido_cabecera, sCorrelativo) {
  var $modal_delete = $('#modal-message-delete');
  $modal_delete.modal('show');

  $('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
  $('.modal-message-delete').addClass('modal-success');

  var sNombreEstado = 'EXW';
  if(Nu_Estado==2)
    sNombreEstado = 'FOB';
  else if(Nu_Estado==3)
    sNombreEstado = 'CIF';
  else if(Nu_Estado==4)
    sNombreEstado = 'DAP';
  else if(Nu_Estado==5)
    sNombreEstado = 'FCA';
  else if(Nu_Estado==6)
    sNombreEstado = 'CFR';
  $('#modal-title').html('¿Deseas cambiar a <strong>' + sNombreEstado + '</strong>?');

  $('#btn-cancel-delete').off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $('#btn-save-delete').off('click').click(function () {
    
    $( '#btn-save-delete' ).text('');
    $( '#btn-save-delete' ).attr('disabled', true);
    $( '#btn-save-delete' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

    url = base_url + 'AgenteCompra/PedidosAprobadosPagados/cambiarIncoterms/' + ID + '/' + Nu_Estado + '/' + sCorrelativo;
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

function cambiarTransporte(ID, Nu_Estado, id_pedido_cabecera, sCorrelativo) {
  var $modal_delete = $('#modal-message-delete');
  $modal_delete.modal('show');

  $('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
  $('.modal-message-delete').addClass('modal-success');

  var sNombreEstado = 'FCL';
  if(Nu_Estado==2)
    sNombreEstado = 'LCL';

  $('#modal-title').html('¿Deseas cambiar a <strong>' + sNombreEstado + '</strong>?');

  $('#btn-cancel-delete').off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $('#btn-save-delete').off('click').click(function () {
    
    $( '#btn-save-delete' ).text('');
    $( '#btn-save-delete' ).attr('disabled', true);
    $( '#btn-save-delete' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

    url = base_url + 'AgenteCompra/PedidosAprobadosPagados/cambiarTransporte/' + ID + '/' + Nu_Estado + '/' + sCorrelativo;
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

function agregarComisionTrading(ID){
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();

  $( '#hidden-modal-id_pedido_cabecera_comision_trading' ).val(ID);
	$( '.modal-comision_trading' ).modal('show');
  $( '#txt-modal-precio_comision_trading' ).val('');
  $( '.modal-comision_trading' ).on('shown.bs.modal', function() {
    $( '#txt-modal-precio_comision_trading' ).focus();
  })
}

function subirPagoFlete(){
  $( '[name="pago_flete-id_cabecera"]' ).val($('#txt-EID_Pedido_Cabecera').val());

  $('#modal-pago_flete').modal('show');
  $( '#form-pago_flete' )[0].reset();
}

function descargarPagoFlete(){
  var id = $('#txt-EID_Pedido_Cabecera').val();
  url = base_url + 'AgenteCompra/PedidosAprobadosPagados/descargarPagoFlete/' + id;
  
  var popupwin = window.open(url);
  setTimeout(function() { popupwin.close();}, 2000);
}

function subirPagoCostoOrigen(){
  $( '[name="costos_origen-id_cabecera"]' ).val($('#txt-EID_Pedido_Cabecera').val());

  $('#modal-costos_origen').modal('show');
  $( '#form-costos_origen' )[0].reset();
}

function descargarPagoCostosOrigen(){
  var id = $('#txt-EID_Pedido_Cabecera').val();
  url = base_url + 'AgenteCompra/PedidosAprobadosPagados/descargarPagoCostosOrigen/' + id;
  
  var popupwin = window.open(url);
  setTimeout(function() { popupwin.close();}, 2000);
}

function subirPagoFTA(){
  $( '[name="pago_fta-id_cabecera"]' ).val($('#txt-EID_Pedido_Cabecera').val());

  $('#modal-pago_fta').modal('show');
  $( '#form-pago_fta' )[0].reset();
}

function descargarPagoFTA(){
  var id = $('#txt-EID_Pedido_Cabecera').val();
  url = base_url + 'AgenteCompra/PedidosAprobadosPagados/descargarPagoFTA/' + id;
  
  var popupwin = window.open(url);
  setTimeout(function() { popupwin.close();}, 2000);
}

function subirPagoCuadrilla(){
  $( '[name="otros_cuadrilla-id_cabecera"]' ).val($('#txt-EID_Pedido_Cabecera').val());

  $('#modal-otros_cuadrilla').modal('show');
  $( '#form-otros_cuadrilla' )[0].reset();
}

function descargarPagoCuadrilla(){
  var id = $('#txt-EID_Pedido_Cabecera').val();
  url = base_url + 'AgenteCompra/PedidosAprobadosPagados/descargarPagoCuadrilla/' + id;
  
  var popupwin = window.open(url);
  setTimeout(function() { popupwin.close();}, 2000);
}

function subirPagoOtrosCostos(){
  $( '[name="otros_costos-id_cabecera"]' ).val($('#txt-EID_Pedido_Cabecera').val());

  $('#modal-otros_costos').modal('show');
  $( '#form-otros_costos' )[0].reset();
}

function descargarPagoOtrosCostos(){
  var id = $('#txt-EID_Pedido_Cabecera').val();
  url = base_url + 'AgenteCompra/PedidosAprobadosPagados/descargarPagoOtrosCostos/' + id;
  
  var popupwin = window.open(url);
  setTimeout(function() { popupwin.close();}, 2000);
}

function editarProveedor(ID_Entidad, id_item){
  //alert(ID_Entidad);
  $( '#form-proveedor' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();

  $('.modal-proveedor').modal('show');

  $( '[name="proveedor-ID_Entidad"]' ).val(ID_Entidad);
  $( '[name="proveedor-ID_Pedido_Detalle_Producto_Proveedor"]' ).val(id_item);
  
  url = base_url + 'AgenteCompra/PedidosAprobadosPagados/getPedidoProveedor/' + id_item;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      console.log(response);

      $( '[name="proveedor-No_Wechat"]' ).val(response.No_Wechat);
      $( '[name="proveedor-No_Rubro"]' ).val(response.No_Rubro);
      $( '[name="proveedor-No_Cuenta_Bancaria"]' ).val(response.No_Cuenta_Bancaria);
      $( '[name="proveedor-Ss_Pago_Importe_1"]' ).val(response.Ss_Pago_Importe_1);
    },
    error: function (jqXHR, textStatus, errorThrown) {
      //$( '#modal-loader' ).modal('hide');
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

function booking(id){
  $( '#form-booking' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();

  $( '[name="booking-ID_Pedido_Cabecera"]' ).val(id);

  $(' .modal-booking ').modal('show');
  $(' #form-booking ' )[0].reset();
  
  url = base_url + 'AgenteCompra/PedidosAprobadosPagados/getBooking/' + id;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      console.log(response);

      $( '[name="booking-Qt_Caja_Total_Booking"]' ).val(response.Qt_Caja_Total_Booking);
      $( '[name="booking-Qt_Cbm_Total_Booking"]' ).val(response.Qt_Cbm_Total_Booking);
      $( '[name="booking-Qt_Peso_Total_Booking"]' ).val(response.Qt_Peso_Total_Booking);
    },
    error: function (jqXHR, textStatus, errorThrown) {
      //$( '#modal-loader' ).modal('hide');
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

function descargarInvoicePlProveedor(id){
  url = base_url + 'AgenteCompra/PedidosAprobadosPagados/descargarInvoicePlProveedor/' + id;
  
  var popupwin = window.open(url);
  setTimeout(function() { popupwin.close();}, 2000);
}

function despacho(id, sCorrelativo){
  $( '[name="despacho-id_cabecera"]' ).val(id);
  $( '[name="despacho-correlativo"]' ).val(sCorrelativo);

  $('#modal-fecha_entrega_shipper').modal('show');
  $( '#form-fecha_entrega_shipper' )[0].reset();
}
  
//chat de novedades de producto
function asignarPedido(ID_Pedido_Cabecera,Nu_Estado){
  /*
  if(Nu_Estado!=3) {//3 - Enviado
    $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
    $('#modal-message').modal('show');

    $('#moda-message-content').addClass( 'bg-warning');
    $('.modal-title-message').html('Primero el estado debe ser <strong>ENVIADO</strong> para asignar.');

    setTimeout(function () { $('#modal-message').modal('hide'); }, 3100);
  } else {
    */
    $('#txt-guardar_personal_china-ID_Pedido_Cabecera').val(ID_Pedido_Cabecera);
    $('.modal-guardar_personal_china').modal('show');

    $('#cbo-guardar_personal_china-ID_Usuario').html('<option value="0" selected="selected">Buscando...</option>');
    url = base_url + 'HelperImportacionController/getUsuarioChina';
    $.post(url, {}, function (response) {
      console.log(response);
      if (response.status == 'success') {
        $('#cbo-guardar_personal_china-ID_Usuario').html('<option value="0" selected="selected">- Seleccionar -</option>');
        var l = response.result.length;
        for (var x = 0; x < l; x++) {
          $('#cbo-guardar_personal_china-ID_Usuario').append('<option value="' + response.result[x].ID + '">' + response.result[x].Nombre + '</option>');
        }
      } else {
        $('#cbo-guardar_personal_china-ID_Usuario').html('<option value="0" selected="selected">Sin registro</option>');
        if (response.sMessageSQL !== undefined) {
          console.log(response.sMessageSQL);
        }
        console.log(response.message);
      }
    }, 'JSON');
  //}
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
    $( '#btn-save-delete' ).html( 'Guardando <div class="spinner-border" role="status"><span class="sr-only"></span></div>' );

    url = base_url + 'AgenteCompra/PedidosGarantizados/removerAsignarPedido/' + ID + '/' + id_usuario;
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