var url;
var table_producto;
var accion_producto = '';


function importarExcelProductos(){
  $( ".modal_importar_producto-laeshop" ).modal( "show" );
}

$(function () {
  //PRECIOS AL POR MAYOR
  let dropZoneGloblal;

  $('.div-precios_x_mayor').hide();
  $('#checkbox-precios_x_mayor').on('ifChanged', function(){
    $('.div-precios_x_mayor').hide();
    var _this = jQuery(this);
    if(_this.is(':checked')){
      $('.div-precios_x_mayor').show();
    }
  });

  $( '#table-precios_x_mayor' ).hide();
  //TABLA DE PRECIOS AL POR MAYOR  
  $( '#btn-addProductoPrecioxMayor' ).click(function(){
    var $Qt_Producto_x_Mayor = parseFloat($( '#txt-Qt_Producto_x_Mayor' ).val());
    var $Ss_Precio_x_Mayor = parseFloat($( '#txt-Ss_Precio_x_Mayor' ).val());
  
    if ( $Qt_Producto_x_Mayor.length === 0) {
      $( '#txt-Qt_Producto_x_Mayor' ).closest('.form-group').find('.help-block').html('Ingresar cantidad');
      $( '#txt-Qt_Producto_x_Mayor' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $Qt_Producto_x_Mayor < 2) {
      $( '#txt-Qt_Producto_x_Mayor' ).closest('.form-group').find('.help-block').html('Cantidad debe ser mayor o igual a 2 unidades');
      $( '#txt-Qt_Producto_x_Mayor' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $Ss_Precio_x_Mayor.length === 0) {
      $( '#txt-Ss_Precio_x_Mayor' ).closest('.form-group').find('.help-block').html('Ingresar precio');
      $( '#txt-Ss_Precio_x_Mayor' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $Ss_Precio_x_Mayor == 0 || isNaN($Ss_Precio_x_Mayor)) {
      $( '#txt-Ss_Precio_x_Mayor' ).closest('.form-group').find('.help-block').html('Precio debe de ser mayor a 0');
      $( '#txt-Ss_Precio_x_Mayor' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      //falta otra validación si la cantidad ya existe debe de rechazar
      var iStatusValidacionFilaCantidad = 0, $fCantidadxMayor=0;
      $("#table-precios_x_mayor > tbody > tr").each(function(){
        fila = $(this);
        $fCantidadxMayor = parseFloat(fila.find(".td-cantidad_x_mayor").text());
        
        iStatusValidacionFilaCantidad = 0;
        if($fCantidadxMayor > $Qt_Producto_x_Mayor){          
          iStatusValidacionFilaCantidad = 1;
          return 1;
        }
      })

      console.log(iStatusValidacionFilaCantidad);
      if(iStatusValidacionFilaCantidad==0) {
        $('.help-block').empty();
        $('.form-group').removeClass('has-error');

        var table_temporal_precio_x_mayor =
        "<tr id='tr_producto_precio_x_mayor" + $Qt_Producto_x_Mayor + "'>"
          + "<td class='text-left' style='display:none;'>" + $Qt_Producto_x_Mayor + "</td>"
          + "<td class='text-right td-cantidad_x_mayor'>" + $Qt_Producto_x_Mayor + "</td>"
          + "<td class='text-right td-precio_x_mayor'>" + $Ss_Precio_x_Mayor + "</td>"
          + "<td class='text-center'><button type='button' id='btn-delete_precio_x_mayor' class='btn btn-xs btn-link' alt='Eliminar' title='Eliminar'><i class='fa fa-2x fa-trash-o' aria-hidden='true'></i></button></td>"
        + "</tr>";
        
        if( isExistTableTemporalPreciosxMayor($Qt_Producto_x_Mayor) ){
          $( '#txt-Qt_Producto_x_Mayor' ).closest('.form-group').find('.help-block').html('Ya existe cantidad <b>' + $Qt_Producto_x_Mayor + '</b>');
          $( '#txt-Qt_Producto_x_Mayor' ).closest('.form-group').removeClass('has-success').addClass('has-error');
          $( '#txt-Qt_Producto_x_Mayor' ).val('');
          $( '#txt-Ss_Precio_x_Mayor' ).val('');
          
          $( '#txt-Qt_Producto_x_Mayor' ).focus();
        } else {
          $( '#table-precios_x_mayor' ).show();
          $( '#table-precios_x_mayor' ).append(table_temporal_precio_x_mayor);
          $( '#txt-Qt_Producto_x_Mayor' ).val('');
          $( '#txt-Ss_Precio_x_Mayor' ).val('');
          
          $( '#txt-Qt_Producto_x_Mayor' ).focus();
        }
      } else {
        alert('La cantidad debe ser mayor a la última fila');
        $( '#txt-Qt_Producto_x_Mayor' ).closest('.form-group').find('.help-block').html('La cantidad debe ser mayor a la última fila');
        $( '#txt-Qt_Producto_x_Mayor' ).closest('.form-group').removeClass('has-success').addClass('has-error');
      }
    }
  })
  
  $( '#table-precios_x_mayor tbody' ).on('click', '#btn-delete_precio_x_mayor', function(){
    $(this).closest('tr').remove();
    if ($( '#table-precios_x_mayor >tbody >tr' ).length == 0)
      $( '#table-precios_x_mayor' ).hide();
  })

  // Validate exist file excel product
  $( document ).on('click', '#btn-excel-importar_producto', function(event) {
    if ( $( "#my-file-selector" ).val().length === 0 ) {
      $( '#my-file-selector' ).closest('.form-group').find('.help-block').html('Seleccionar archivo');
      $( '#my-file-selector' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      $( '#btn-cancel-product' ).attr('disabled', true);
      $( '#a-download-product' ).attr('disabled', true);
      
      $( '#btn-excel-importar_producto' ).text('');
      $( '#btn-excel-importar_producto' ).attr('disabled', true);
      $( '#btn-excel-importar_producto' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
  
      $( '#modal-loader' ).modal('show');
    }
  })
  
  $( '.div-AgregarEditar' ).hide();
  
  $('.select2').select2();
  
  url = base_url + 'Dropshipping/ProductosProveedoresDropshippingController/ajax_list';
  table_producto = $('#table-Producto').DataTable({
    'dom': '<"top">frt<"bottom"lip><"clear">',
    'searching'   : false,
    'bStateSave'  : true,
    'processing'  : true,
    'serverSide'  : true,
    'info'        : true,
    'autoWidth'   : false,
    'pagingType'  : 'full_numbers',
    'oLanguage' : {
      'sInfo'               : 'Mostrando (_START_ - _END_) total de registros _TOTAL_',
      'sLengthMenu'         : '_MENU_',
      'sSearch'             : 'Buscar por: ',
      'sSearchPlaceholder'  : 'UPC / Nombre',
      'sZeroRecords'        : 'No se encontraron registros',
      'sInfoEmpty'          : 'No hay registros',
      'sLoadingRecords'     : 'Cargando...',
      'sProcessing'         : 'Procesando...',
      'oPaginate'           : {
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
      'dataType'  : 'json',
      'data'      : function ( data ) {
        data.Filtros_Productos = $( '#cbo-Filtros_Productos' ).val(),
        data.Global_Filter = $( '#txt-Global_Filter' ).val(),
        data.Filtro_Nu_Estado = $('#cbo-filtro-estado_producto').val();
      },
    },
    'columnDefs': [{
      'className' : 'text-center',
      'targets'   : 'no-sort',
      'orderable' : false,
    },{
      'className' : 'text-right',
      'targets'   : 'sort_right',
      'orderable' : true,
    },],
    'lengthMenu': [[10, 100, 1000, -1], [10, 100, 500, 1000]],
    "createdRow": function (row, data, dataIndex) {
      if (data[0] == 'Producto' && parseFloat(data[13]) >= parseFloat(data[9]))
        $(row).addClass('danger');
    }
  });

  $("#btn-cancelar").click(function(){
     table_producto.ajax.reload();
  });

  $('.dataTables_length').addClass('col-xs-4 col-sm-5 col-md-1');
  $('.dataTables_info').addClass('col-xs-8 col-sm-7 col-md-4');
  $('.dataTables_paginate').addClass('col-xs-12 col-sm-12 col-md-7');

  $( '#txt-Global_Filter' ).keyup(function() {
    table_producto.search($(this).val()).draw();
  });

  $('#cbo-filtro-estado_producto').change(function () {
    table_producto.search($(this).val()).draw();
  });

  $( '#form-Producto' ).validate({
    rules:{
      Nu_Tipo_Producto: {
        required: true,
      },
      Nu_Codigo_Barra: {
        required: true,
      },
      ID_Impuesto: {
        required: true,
      },
      No_Producto: {
        required: true,
      },
      Ss_Precio: {
        required: true,
      },
      ID_Unidad_Medida: {
        required: true,
      },
      ID_Familia: {
        required: true,
      },
      Ss_Precio_Ecommerce_Online_Regular: {
        required: true,
      },
      ID_Familia_Marketplace: {
        required: true,
      },
    },
    messages:{
      Nu_Tipo_Producto:{
        required: "Seleccionar grupo",
      },
      Nu_Codigo_Barra:{
        required: "ingresar código",
      },
      ID_Impuesto: {
        required: "Seleccionar impuesto",
      },
      No_Producto:{
        required: "Ingresar nombre",
      },
      Ss_Precio: {
        required: "Ingresar precio",
      },
      ID_Familia: {
        required: "Seleccionar categoría",
      },
      ID_Unidad_Medida:{
        required: "Seleccionar unidad medida",
      },
      Ss_Precio_Ecommerce_Online_Regular: {
        required: "Ingresar precio",
      },
      ID_Familia_Marketplace:{
        required: "Seleccionar categoría",
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
    submitHandler: form_Producto
  });

  $("#table-Producto").on('click', '.img-fluid', function () {
    //$('.img-fluid').data('url_img');
    $('.img-responsive').attr('src', '');
    $('.modal-info_item').modal('show');
    $('.img-responsive').attr('src', $(this).data('url_img'));
  })

  $( '.div-Producto' ).show();
  $( '#cbo-TiposItem' ).change(function(){
    $( '.div-Producto' ).show();
    if ( $(this).val() == 0 || $(this).val() == 2 ){//Servicio o (Interno) No para la venta
      $( '.div-Producto' ).hide();
    }
  })
  
  $( '.div-Compuesto' ).hide();
  $( '#table-Producto_Enlace' ).hide();
  $( '#cbo-Compuesto' ).change(function(){
    $( '.div-Compuesto' ).hide();  
    if( $(this).val() == 1 ){
      $( '.div-Compuesto' ).show();
      $( '#txt-ANombre' ).focus();
    }
  })
  
  $( '#btn-addProductosEnlaces' ).click(function(){
    var $ID_Producto        = $( '#txt-AID' ).val();
    var $ID_Producto_Enlace = $( '#txt-ACodigo' ).val();
    var $No_Producto_Enlace = $( '#txt-ANombre' ).val();
    var $Qt_Producto_Enlace = $( '#txt-Qt_Producto_Descargar' ).val();
  
    if ( $ID_Producto.length === 0 || $No_Producto_Enlace.length === 0) {
      $( '#txt-ANombre' ).closest('.form-group').find('.help-block').html('Ingresar producto');
      $( '#txt-ANombre' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $Qt_Producto_Enlace.length === 0) {
      $( '#txt-Qt_Producto_Descargar' ).closest('.form-group').find('.help-block').html('Ingresar cantidad');
      $( '#txt-Qt_Producto_Descargar' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $Qt_Producto_Enlace == 0) {
      $( '#txt-Qt_Producto_Descargar' ).closest('.form-group').find('.help-block').html('La cantidad debe de ser mayor 0');
      $( '#txt-Qt_Producto_Descargar' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      var table_enlace_producto =
      "<tr id='tr_enlace_producto" + $ID_Producto + "'>"
        + "<td class='text-left' style='display:none;'>" + $ID_Producto + "</td>"
        + "<td class='text-left'>" + $ID_Producto_Enlace + "</td>"
        + "<td class='text-left'>" + $No_Producto_Enlace + "</td>"
        + "<td class='text-right'>" + $Qt_Producto_Enlace + "</td>"
        + "<td class='text-center'><button type='button' id='btn-deleteProductoEnlace' class='btn btn-xs btn-link' alt='Eliminar' title='Eliminar'><i class='fa fa-trash-o' aria-hidden='true'> Eliminar</i></button></td>"
      + "</tr>";
      
      if( isExistTableTemporalEnlacesProducto($ID_Producto) ){
        $( '#txt-ANombre' ).closest('.form-group').find('.help-block').html('Ya existe producto <b>' + $No_Producto_Enlace + '</b>');
        $( '#txt-ANombre' ).closest('.form-group').removeClass('has-success').addClass('has-error');
        $( '#txt-AID' ).val('');
        $( '#txt-ACodigo' ).val('');
        $( '#txt-ANombre' ).val('');
        
        $( '#txt-ANombre' ).focus();
      } else {
        $( '#table-Producto_Enlace' ).show();
        $( '#table-Producto_Enlace' ).append(table_enlace_producto);
        $( '#txt-AID' ).val('');
        $( '#txt-ACodigo' ).val('');
        $( '#txt-ANombre' ).val('');
        
        $( '#txt-ANombre' ).focus();
      }
    }
  })
  
  $( '#table-Producto_Enlace tbody' ).on('click', '#btn-deleteProductoEnlace', function(){
    $(this).closest ('tr').remove ();
    if ($( '#table-Producto_Enlace >tbody >tr' ).length == 0)
        $( '#table-Producto_Enlace' ).hide();
  })
  
  $(document).bind('keydown', 'f2', function(){
    agregarProducto();
  });
  
  /* Categorías */
  $('#cbo-categoria').change(function () {
    $('#cbo-sub_categoria').html('<option value="" selected="selected">- Sin registros -</option>');
    if ($(this).val()>0) {
      url = base_url + 'HelperTiendaVirtualController/getSubCategorias';
      var arrParams = {
        sTipoData : 'subcategoria',
        sWhereIdCategoria : $( this ).val(),
      }
      $.post( url, arrParams, function( response ){
        $('#cbo-sub_categoria').html('<option value="" selected="selected">- Seleccionar -</option>');
        if ( response.sStatus == 'success' ) {
          var l = response.arrData.length;
          if (l==1) {
            $('#cbo-sub_categoria').append( '<option value="' + response.arrData[0].ID + '">' + response.arrData[0].Nombre + '</option>' );
          } else {
            for (var x = 0; x < l; x++) {
              $( '#cbo-sub_categoria' ).append( '<option value="' + response.arrData[x].ID + '">' + response.arrData[x].Nombre + '</option>' );
            }
          }
        } else {
          if( response.sMessageSQL !== undefined ) {
            console.log(response.sMessageSQL);
          }
          if ( response.sStatus != 'warning' ) {
            $( '#modal-message' ).modal('show');
            $( '.modal-message' ).addClass(response.sClassModal);
            $( '.modal-title-message' ).text(response.sMessage);
            setTimeout(function() {$('#modal-message').modal('hide');}, 1200);
          }
        }
      }, 'JSON');
    }
  });

  $('.div-mas_opciones').hide();

  $('#btn-mostrar_campos_adicionales').click(function () {
    if ($(this).data('mostrar_campos_adicionales') == 1) {
      //setter
      $('#btn-mostrar_campos_adicionales').data('mostrar_campos_adicionales', 0);
    } else {
      $('#btn-mostrar_campos_adicionales').data('mostrar_campos_adicionales', 1);
    }

    if ($(this).data('mostrar_campos_adicionales') == 1) {
      $('.div-campos_adicionales').css("display", "");
      $('#btn-mostrar_campos_adicionales').text('Ocultar mas opciones');
      $('.div-mas_opciones').show();
    } else {
      $('#btn-mostrar_campos_adicionales').text('Ver mas opciones');
      $('.div-campos_adicionales').css("display", "none");
      $('.div-mas_opciones').hide();
    }
  })

  $('#btn-activar_producto_masivamente').click(function () {
    var $modal_message = $('.modal-message-delete');
    $modal_message.modal('show');

    $('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
    $('.modal-message-delete').addClass('modal-success');

    $('.modal-title-message-delete').text('¿Estás seguro de mostrar todos los productos de tu tienda?');

    $('#btn-cancel-delete').off('click').click(function () {
      $modal_message.modal('hide');
    });

    $('#btn-save-delete').off('click').click(function () {
      $modal_message.modal('hide');
      
      $('#btn-activar_producto_masivamente').text('');
      $('#btn-activar_producto_masivamente').attr('disabled', true);
      $('#btn-activar_producto_masivamente').append('Actualizando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>');

      url = base_url + 'Dropshipping/ProductosProveedoresDropshippingController/updActivarMasivamenteProductos';
      var arrPost = {
        ID_Empresa : $( '[name="ID_Empresa_Item"]' ).val(),
        iEstado:1
      };
      $.post( url, arrPost, function( response ){
        $('.modal-message').removeClass('modal-danger modal-warning modal-success');
        $('#modal-message').modal('show');
      
        if (response.status == 'success') {
          $('.modal-message').addClass(response.style_modal);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
          reload_table_producto();
        } else {
          $('.modal-message').addClass(response.style_modal);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1200);
        }

        $('#btn-activar_producto_masivamente').text('');
        $('#btn-activar_producto_masivamente').attr('disabled', false);
        $('#btn-activar_producto_masivamente').append('Mostrar todos');

      }, 'JSON');
    });
  })

  //OCULTAS TODOS LOS PRODUCTOS DE TIENDA
  $('#btn-ocultar_producto_masivamente').click(function () {
    var $modal_message = $('.modal-message-delete');
    $modal_message.modal('show');

    $('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
    $('.modal-message-delete').addClass('modal-danger');

    $('.modal-title-message-delete').text('¿Estás seguro de ocultar todos los productos de tu tienda?');

    $('#btn-cancel-delete').off('click').click(function () {
      $modal_message.modal('hide');
    });

    $('#btn-save-delete').off('click').click(function () {
      $modal_message.modal('hide');
      
      $('#btn-ocultar_producto_masivamente').text('');
      $('#btn-ocultar_producto_masivamente').attr('disabled', true);
      $('#btn-ocultar_producto_masivamente').append('Actualizando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>');

      url = base_url + 'Dropshipping/ProductosProveedoresDropshippingController/updActivarMasivamenteProductos';
      var arrPost = {
        ID_Empresa : $( '[name="ID_Empresa_Item"]' ).val(),
        iEstado:0
      };
      $.post( url, arrPost, function( response ){
        $('.modal-message').removeClass('modal-danger modal-warning modal-success');
        $('#modal-message').modal('show');
      
        if (response.status == 'success') {
          $('.modal-message').addClass(response.style_modal);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
          reload_table_producto();
        } else {
          $('.modal-message').addClass(response.style_modal);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1200);
        }

        $('#btn-ocultar_producto_masivamente').text('');
        $('#btn-ocultar_producto_masivamente').attr('disabled', false);
        $('#btn-ocultar_producto_masivamente').append('Ocultar todos');

      }, 'JSON');
    });
  })
})

function isExistTableTemporalEnlacesProducto($ID_Producto_Enlace){
  return Array.from($('tr[id*=tr_enlace_producto]'))
    .some(element => ($('td:nth(0)',$(element)).html()===$ID_Producto_Enlace));
}

function agregarProducto(){
  accion_producto = 'add_producto';
  $( '[name="EID_Empresa"]' ).focus();
  
  $( '#form-Producto' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '#modal-loader' ).modal('show');
  
  $( '.div-Listar' ).hide();
  $( '.div-AgregarEditar' ).show();
      
  $( '.title_Producto' ).text('Nuevo Producto');

  $('[name="EID_Empresa"]').val('');
  $('[name="EID_Producto"]').val('');
  $('[name="ENu_Codigo_Barra"]').val('');
  $('[name="ENo_Codigo_Interno"]').val('');

  $('#checkbox-precios_x_mayor').prop('checked', false).iCheck('update');
  $('.div-precios_x_mayor').hide();
  $('#table-precios_x_mayor').hide();
  $( '#table-precios_x_mayor tbody' ).empty();
  
  $( '.div-Producto' ).show();
  
  $( '#cbo-TiposItem' ).prop('disabled', false);
  
  url = base_url + 'HelperController/getTiposProducto';
  $.post( url , function( responseTiposProducto ){
    $( '#cbo-TiposItem' ).html( '<option value="">- Seleccionar -</option>' );    
    for (var i = 0; i < responseTiposProducto.length; i++)
      $( '#cbo-TiposItem' ).append( '<option value="' + responseTiposProducto[i].Nu_Valor + '">' + responseTiposProducto[i].No_Descripcion + '</option>' );
  }, 'JSON');
    
  url = base_url + 'HelperController/getImpuestos';
  $.post( url , function( response ){
    $( '#cbo-Impuestos' ).html( '<option value="">- Vacío -</option>' );
    if ( response.length == 1 ) {
      $( '#cbo-Impuestos' ).html( '<option value="' + response[0].ID_Impuesto + '" data-ss_impuesto="' + response[0].Ss_Impuesto + '" data-nu_tipo_impuesto="' + response[0].Nu_Tipo_Impuesto + '">' + response[0].No_Impuesto + '</option>' );
    } else if ( response.length > 1 ) {
      $( '#cbo-Impuestos' ).html( '<option value="">- Seleccionar -</option>' );
      for (var i = 0; i < response.length; i++)
        $( '#cbo-Impuestos' ).append( '<option value="' + response[i].ID_Impuesto + '" data-ss_impuesto="' + response[i].Ss_Impuesto + '" data-nu_tipo_impuesto="' + response[i].Nu_Tipo_Impuesto + '">' + response[i].No_Impuesto + '</option>' );
    }
  }, 'JSON');

  $('#cbo-sub_categoria').html('<option value="" selected="selected">- Sin registro -</option>');
  url = base_url + 'HelperTiendaVirtualController/getCategorias';
  $.post(url, { sTipoData: 'categoria' }, function (response) {
    if (response.sStatus == 'success') {
      var l = response.arrData.length;
      if (l == 1) {
        $('#cbo-categoria').html('<option value="' + response.arrData[0].ID + '">' + response.arrData[0].Nombre + '</option>');

        $('#cbo-sub_categoria').html('<option value="" selected="selected">- Sin registro -</option>');
        url = base_url + 'HelperTiendaVirtualController/getSubCategorias';
        var arrParams = {
          sTipoData: 'subcategoria',
          sWhereIdCategoria: response.arrData[0].ID,
        }
        $.post(url, arrParams, function (responseSubCategoria) {
          $('#cbo-sub_categoria').html('<option value="" selected="selected">- Sin registro -</option>');
          if (responseSubCategoria.sStatus == 'success') {
            var l = responseSubCategoria.arrData.length;
            if (l == 1) {
              $('#cbo-sub_categoria').append('<option value="' + responseSubCategoria.arrData[0].ID + '">' + responseSubCategoria.arrData[0].Nombre + '</option>');
            } else {
              for (var x = 0; x < l; x++) {
                $('#cbo-sub_categoria').append('<option value="' + responseSubCategoria.arrData[x].ID + '">' + responseSubCategoria.arrData[x].Nombre + '</option>');
              }
            }
          } else {
            $('#cbo-sub_categoria').html('<option value="" selected="selected">- vacío -</option>');
          }
        }, 'JSON');
      } else {
        $('#cbo-categoria').html('<option value="" selected="selected">- Seleccionar -</option>');
        for (var x = 0; x < l; x++) {
          $('#cbo-categoria').append('<option value="' + response.arrData[x].ID + '">' + response.arrData[x].Nombre + '</option>');
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
    $('#modal-loader').modal('hide');
  }, 'JSON');

  $('#cbo-Marcas').html('<option value="">- Sin registros -</option>');
  url = base_url + 'HelperTiendaVirtualController/getMarcas';
  $.post(url, function (responseMarcas) {
    $('#cbo-Marcas').html('<option value="">- Seleccionar -</option>');
    if (responseMarcas.length == 1) {
      $('#cbo-Marcas').append('<option value="' + responseMarcas[0].ID_Marca + '">' + responseMarcas[0].No_Marca + '</option>');
    } else {
      for (var i = 0; i < responseMarcas.length; i++)
        $('#cbo-Marcas').append('<option value="' + responseMarcas[i].ID_Marca + '">' + responseMarcas[i].No_Marca + '</option>');
    }
  }, 'JSON');

  url = base_url + 'HelperController/getUnidadesMedida';
  $.post( url , function( responseUnidadMedidas ){
    if ( responseUnidadMedidas.length == 1 ) {
      $( '#cbo-UnidadesMedida' ).html( '<option value="' + responseUnidadMedidas[0].ID_Unidad_Medida + '" selected>' + responseUnidadMedidas[0].No_Unidad_Medida + '</option>' );
    } else {
      $( '#cbo-UnidadesMedida' ).html( '<option value="">- Seleccionar -</option>' );
      for (var i = 0; i < responseUnidadMedidas.length; i++) {
        $( '#cbo-UnidadesMedida' ).append( '<option value="' + responseUnidadMedidas[i].ID_Unidad_Medida + '">' + responseUnidadMedidas[i].No_Unidad_Medida + '</option>' );
      }
    }
    $('#modal-loader').modal('hide');
  }, 'JSON');

  $( '#cbo-Estado' ).html( '<option value="1">Mostrar</option>' );
  $( '#cbo-Estado' ).append( '<option value="0">Ocultar</option>' );

  /* obtener imagen guardada(s) */
  // $( '.divDropzone' ).html(
  // '<div id="id-divDropzone" class="dropzone div-dropzone">'
  //   +'<div class="dz-message">'
  //     +'Arrastrar o presionar click para subir imágen'
  //   +'</div>'
  // +'</div>'
  // );


/*$( "#jony" ).off();
$( "#jony" ).click(function(){
  //console.log(dropZoneGloblal);
  console.log(dropZoneGloblal._getProductos());
  console.log("predeterminado");
  console.log(dropZoneGloblal._predeterminado());
});*/

if (Dropzone.instances.length > 0) 
  Dropzone.instances.forEach(drop => drop.destroy());

  Dropzone.autoDiscover = false;
  Dropzone.prototype.defaultOptions.dictDefaultMessage = "Arrastrar o presionar click para subir imágen";
  Dropzone.prototype.defaultOptions.dictFallbackMessage = "Tu navegador no soporta la función arrastrar la imágen";
  Dropzone.prototype.defaultOptions.dictFileTooBig = "La imágen pesa ({{filesize}}MiB). El tamaño máximo es: {{maxFilesize}}MiB.";
  Dropzone.prototype.defaultOptions.dictInvalidFileType = "Solo se permite imágenes PNG / JPG / JPEG";
  Dropzone.prototype.defaultOptions.dictCancelUpload = "Cancelar";
  Dropzone.prototype.defaultOptions.dictCancelUploadConfirmation = "¿Estás seguro de cancelar la subida?";
  Dropzone.prototype.defaultOptions.dictRemoveFile = "Eliminar";
  Dropzone.prototype.defaultOptions.dictMaxFilesExceeded = "Solo se puede subir 1 imágen";
  
  Dropzone.prototype._getProductos = function(){
    let productos = [];
    //console.log(this.files);
    for(i=0;i<this.files.length;i++){
      productos.push({"No_Producto_Imagen":this.files[i].name_,"Imagen_Tamano":this.files[i].size,"ID_Predeterminado":this.files[i].predeterminado});
    }
    return productos;
  }

  Dropzone.prototype._remove= function(e){
    //console.log("_remove");
    let file          = e.data.obj;
    let root          = e.data.root;
  
    root.removeFile(file);
    //console.log(file);
}

 Dropzone.prototype._predeterminado= function(){
    if( typeof this.PredeterminadoFile !== 'undefined' ) 
      return this.PredeterminadoFile.name_;
    else
      return "";
}

Dropzone.prototype._default= function(e){
    //console.log("_default");
  
    let file            = e.data.obj;
    let root            = e.data.root;
    
    for(i=0;i<root.files.length;i++){

      root.files[i].predeterminado = 0;
      $(root.files[i].previewElement).find(".dz-image").removeClass("predeterminado");
      //console.log(root.files[i]);
    }

    $(file.previewElement).find(".dz-image").addClass("predeterminado");
    file.predeterminado = 1;
    root.PredeterminadoFile = file;
        
}

  url = base_url + 'Dropshipping/ProductosProveedoresDropshippingController/uploadMultiple';
  dropZoneGloblal = new Dropzone("#id-divDropzone", {
    url: url,
    params: {
      iVersionImage: 1
    },
    acceptedFiles: ".jpeg,.jpg,.png,.webp",
    uploadMultiple: false,
    maxFilesize: 1,//Peso en MB
    thumbnailHeight: 200,
    thumbnailWidth: 200,
    parallelUploads: 1,
    previewsContainer: "#dropzone-previews",
    previewTemplate:document.querySelector('#template-container').innerHTML,
    init : function() {
       let root = this;
      let dpzMultipleFiles = this;
      $('.dz-preview').remove();
      this.removeAllFiles;
      root.id_imagen = 0;
      this.on("success", function(file, response) {
          var resp = jQuery.parseJSON(response);
          if(resp.sStatus=="success"){
            file.name_= resp.NombreImagen;
            //console.log("llego bien upload");
          }
          // console.log(response);
          //   console.log(resp);
          // console.log("meotod success");
      })

      this.on("addedfile", file => {
          file.predeterminado = 0;
          //console.log("function addedfile");
          $( "#dropzone-previews" ).scrollTop( 999999 );
          $( file.previewElement ).on( "click", ".remove",{obj: file,root:root}, this._remove);
          $( file.previewElement ).on( "click", ".default",{obj: file,root:root}, this._default);

       });

    },
  })

}// agregarProducto

function verProducto(ID, No_Imagen_Item, Nu_Version_Imagen){
  console.log('aaaaaaa');
  accion_producto = 'upd_producto';
  $( '[name="EID_Empresa"]' ).focus();
  
  $( '#modal-loader' ).modal('show');
  
  $( '.div-Listar' ).hide();
  
  $( '.div-Compuesto' ).hide();
  $( '#table-Producto_Enlace tbody' ).empty();

  $( '#form-Producto' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
 
  $( '.div-Producto' ).show();
  
  $( '#cbo-TiposItem' ).prop('disabled', true);
  
  url = base_url + 'Dropshipping/ProductosProveedoresDropshippingController/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $( '.div-AgregarEditar' ).show();
      
      $( '.title_Producto' ).text('Modifcar Producto');
      
      $( '[name="EID_Empresa"]' ).val(response.ID_Empresa);
      $( '[name="EID_Producto"]' ).val(response.ID_Producto);
      $('[name="ENu_Codigo_Barra"]').val(response.Nu_Codigo_Barra);
      $('[name="ENo_Codigo_Interno"]').val(response.No_Codigo_Interno);
      $( '#hidden-nombre_imagen' ).val(response.No_Imagen_Item);
      
      if ( response.Nu_Tipo_Producto == 0 || response.Nu_Tipo_Producto == 2){//Servicio
        $( '.div-Producto' ).hide();
      }
      
      var selected='';
      url = base_url + 'HelperController/getTiposProducto';
      $.post( url , function( responseTiposProducto ){
        $( '#cbo-TiposItem' ).html( '<option value="">- Seleccionar -</option>' );
        for (var i = 0; i < responseTiposProducto.length; i++){
          selected = '';
          if(response.Nu_Tipo_Producto == responseTiposProducto[i].Nu_Valor)
            selected = 'selected="selected"';
          $( '#cbo-TiposItem' ).append( '<option value="' + responseTiposProducto[i].Nu_Valor + '" ' + selected + '>' + responseTiposProducto[i].No_Descripcion + '</option>' );
        }
      }, 'JSON');
            
      $('[name="No_Codigo_Interno"]').val(response.No_Codigo_Interno);
      $('[name="Nu_Codigo_Barra"]').val(response.Nu_Codigo_Barra);
    
      $('[name="No_Producto"]').val( clearHTMLTextArea(response.No_Producto) );

      $('[name="Ss_Precio_Ecommerce_Online_Regular"]').val( Math.round10(response.Ss_Precio_Ecommerce_Online_Regular, -2) );
      $('[name="Ss_Precio_Ecommerce_Online"]').val( Math.round10(response.Ss_Precio_Ecommerce_Online, -2) );
      
      //DROPSHIPPING
      $('[name="Ss_Precio_Proveedor_Dropshipping"]').val( Math.round10(response.Ss_Precio_Proveedor_Dropshipping, -2) );
      $('[name="Ss_Precio_Vendedor_Dropshipping"]').val( Math.round10(response.Ss_Precio_Vendedor_Dropshipping, -2) );

      url = base_url + 'HelperController/getImpuestos';
      $.post( url , function( responseImpuestos ){
        console.log(responseImpuestos);
        if(responseImpuestos.length==1) {
          $( '#cbo-Impuestos' ).html( '' );
          for (var i = 0; i < responseImpuestos.length; i++){
            selected = '';
            if(response.ID_Impuesto == responseImpuestos[i].ID_Impuesto)
              selected = 'selected="selected"';
            $( '#cbo-Impuestos' ).append( '<option value="' + responseImpuestos[i].ID_Impuesto + '" data-ss_impuesto="' + responseImpuestos[i].Ss_Impuesto + '" data-nu_tipo_impuesto="' + responseImpuestos[i]['Nu_Tipo_Impuesto'] + '" ' + selected + '>' + responseImpuestos[i]['No_Impuesto'] + '</option>' );
          }
        } else {
          $( '#cbo-Impuestos' ).html( '<option value="">- Seleccionar -</option>' );
          for (var i = 0; i < responseImpuestos.length; i++){
            selected = '';
            if(response.ID_Impuesto == responseImpuestos[i].ID_Impuesto)
              selected = 'selected="selected"';
            $( '#cbo-Impuestos' ).append( '<option value="' + responseImpuestos[i].ID_Impuesto + '" data-ss_impuesto="' + responseImpuestos[i].Ss_Impuesto + '" data-nu_tipo_impuesto="' + responseImpuestos[i]['Nu_Tipo_Impuesto'] + '" ' + selected + '>' + responseImpuestos[i]['No_Impuesto'] + '</option>' );
          }
        }
      }, 'JSON');

      url = base_url + 'HelperTiendaVirtualController/getCategorias';
      $.post(url, { sTipoData: 'categoria' }, function (responseCategoria) {
        $('#cbo-categoria').html('<option value="">- Seleccionar -</option>');
        if (responseCategoria.sStatus == 'success') {
          var l = responseCategoria.arrData.length;
          for (var x = 0; x < l; x++) {
            selected = '';
            if (response.ID_Familia == responseCategoria.arrData[x].ID)
              selected = 'selected="selected"';
            $('#cbo-categoria').append('<option value="' + responseCategoria.arrData[x].ID + '" ' + selected + '>' + responseCategoria.arrData[x].Nombre + '</option>');
          }
        } else {
          if (responseCategoria.sMessageSQL !== undefined) {
            console.log(responseCategoria.sMessageSQL);
          }
          if (responseCategoria.sStatus == 'warning')
            $('#cbo-categoria').html('<option value="0" selected="selected">- Vacío -</option>');
        }
      }, 'JSON');

      $('#cbo-sub_categoria').html('<option value="">- Sin registro -</option>');
      url = base_url + 'HelperTiendaVirtualController/getSubCategorias';
      $.post(url, { sTipoData: 'subcategoria', sWhereIdCategoria: response.ID_Familia }, function (responseSubCategoria) {
        if (responseSubCategoria.sStatus == 'success') {
          $('#cbo-sub_categoria').html('<option value="">- Seleccionar -</option>');
          var l = responseSubCategoria.arrData.length;
          for (var x = 0; x < l; x++) {
            selected = '';
            if (response.ID_Sub_Familia == responseSubCategoria.arrData[x].ID)
              selected = 'selected="selected"';
            $('#cbo-sub_categoria').append('<option value="' + responseSubCategoria.arrData[x].ID + '" ' + selected + '>' + responseSubCategoria.arrData[x].Nombre + '</option>');
          }
        } else {
          if (responseSubCategoria.sMessageSQL !== undefined) {
            console.log(responseSubCategoria.sMessageSQL);
          }
          if (responseSubCategoria.sStatus == 'warning')
            $('#cbo-sub_categoria').html('<option value="0" selected="selected">- Vacío -</option>');
        }
        $('#modal-loader').modal('hide');
      }, 'JSON');

      $('#cbo-Marcas').html('<option value="">- Sin registro -</option>');
      url = base_url + 'HelperTiendaVirtualController/getMarcas';
      $.post(url, function (responseMarcas) {
        $('#cbo-Marcas').html('<option value="">- Seleccionar -</option>');
        for (var i = 0; i < responseMarcas.length; i++) {
          selected = '';
          if (response.ID_Marca == responseMarcas[i].ID_Marca)
            selected = 'selected="selected"';
          $('#cbo-Marcas').append('<option value="' + responseMarcas[i].ID_Marca + '" ' + selected + '>' + responseMarcas[i].No_Marca + '</option>');
        }
      }, 'JSON');
      
      url = base_url + 'HelperController/getUnidadesMedida';
      $.post( url , function( responseUnidadMedidas ){
        $( '#cbo-UnidadesMedida' ).html( '<option value="">- Seleccionar -</option>' );
        for (var i = 0; i < responseUnidadMedidas.length; i++){
          selected = '';
          if(response.ID_Unidad_Medida == responseUnidadMedidas[i].ID_Unidad_Medida)
            selected = 'selected="selected"';
          $( '#cbo-UnidadesMedida' ).append( '<option value="' + responseUnidadMedidas[i].ID_Unidad_Medida + '" ' + selected + '>' + responseUnidadMedidas[i].No_Unidad_Medida + '</option>' );
        }
      }, 'JSON');
                
      $( '#cbo-Estado' ).html('');
      for (var i = 0; i < 2; i++){
        selected = '';
        if (response.Nu_Activar_Item_Lae_Shop == i)
          selected = 'selected="selected"';
        $( '#cbo-Estado' ).append( '<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Ocultar' : 'Mostrar') + '</option>' );
      }

      $( '[name="Txt_Producto"]' ).val( response.Txt_Producto );
      
      $('#checkbox-precios_x_mayor').prop('checked', false).iCheck('update');
      $('.div-precios_x_mayor').hide();
      $('#table-precios_x_mayor').hide();
      $( '#table-precios_x_mayor tbody' ).empty();
      /* PRECIOS AL POR MAYOR */
      if (response.Nu_Activar_Precio_x_Mayor == 1) {
        $('#checkbox-precios_x_mayor').prop('checked', true).iCheck('update');
        url = base_url + 'Dropshipping/ProductosProveedoresDropshippingController/ajax_edit_precios_por_mayor/' + ID;
        $.getJSON( url, function( data_precios_x_mayor ){
          if ( data_precios_x_mayor.length > 0 ){
            $('.div-precios_x_mayor').show();
            $('#table-precios_x_mayor').show();
            var table_temporal_precio_x_mayor = "", fCantidadxMayor=0;
            for (i = 0; i < data_precios_x_mayor.length; i++) {
              fCantidadxMayor = parseFloat(data_precios_x_mayor[i]['Qt_Producto_x_Mayor']);
              table_temporal_precio_x_mayor += 
              "<tr id='tr_producto_precio_x_mayor" + fCantidadxMayor + "'>"
                + "<td class='text-left' style='display:none;'>" + fCantidadxMayor + "</td>"
                + "<td class='text-right td-cantidad_x_mayor'>" + fCantidadxMayor + "</td>"
                + "<td class='text-right td-precio_x_mayor'>" + number_format(data_precios_x_mayor[i]['Ss_Precio_x_Mayor'], 3) + "</td>"
                + "<td class='text-center'><button type='button' id='btn-delete_precio_x_mayor' class='btn btn-xs btn-link' alt='Eliminar' title='Eliminar'><i class='fa fa-2x fa-trash-o' aria-hidden='true'></i></button></td>"
              + "</tr>";
            }
            $( '#table-precios_x_mayor' ).append(table_temporal_precio_x_mayor);
          }
        }, 'JSON');
      }
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
  })
  

if (Dropzone.instances.length > 0) 
  Dropzone.instances.forEach(drop => drop.destroy());

  Dropzone.autoDiscover = false;
  Dropzone.options.myAwesomeDropzone = false;
  Dropzone.prototype.defaultOptions.dictDefaultMessage = "Arrastrar o presionar click para subir imágen";
  Dropzone.prototype.defaultOptions.dictFallbackMessage = "Tu navegador no soporta la función arrastrar la imágen";
  Dropzone.prototype.defaultOptions.dictFileTooBig = "La imágen pesa ({{filesize}}MiB). El tamaño máximo es: {{maxFilesize}}MiB.";
  Dropzone.prototype.defaultOptions.dictInvalidFileType = "Solo se permite imágenes PNG / JPG / JPEG";
  Dropzone.prototype.defaultOptions.dictCancelUpload = "Cancelar";
  Dropzone.prototype.defaultOptions.dictCancelUploadConfirmation = "¿Estás seguro de cancelar la subida?";
  Dropzone.prototype.defaultOptions.dictRemoveFile = "Eliminar";
  Dropzone.prototype.defaultOptions.dictMaxFilesExceeded = "Solo se puede subir 1 imágen";
  
  Dropzone.prototype._getProductos = function(){ 
    return [];
  }

  Dropzone.prototype._predeterminado= function(){
    if( typeof this.PredeterminadoFile !== 'undefined' ) 
      return this.PredeterminadoFile.name_;
    else
      return "";
}

  Dropzone.prototype._remove= function(e){
    //console.log("_remove");
    let imagen          = e.data.obj;
    let predeterminado  = $(e.data.obj).data("id_predeterminado");
  
    url = base_url + 'Dropshipping/ProductosProveedoresDropshippingController/removeFileImage';
    $.ajax({
      url : url,
      type: "POST",
      dataType: "JSON",
      data: { iIdProducto: ID,IdImagen: $(imagen).data("id_imagen"),"Predeterminado":predeterminado},
      success: function (response) {
        
        if(response.status=="success")
          $(imagen).remove();

      },
      error: function (jqXHR, textStatus, errorThrown) {
        
      }
    })
}

Dropzone.prototype._default= function(e){
    //console.log("_default");
    let imagen          = e.data.obj;
    let root            = e.data.root;
    let file            = e.data.file;
    let predeterminado  = $(e.data.obj).data("id_predeterminado");
  
    url = base_url + 'Dropshipping/ProductosProveedoresDropshippingController/DefaultImagen';
    // console.log(imagen);
    // console.log("iIdProducto: "+ID);
    // console.log("IdImagen: "+$(imagen).data("id_imagen"));

    if(root.id_imagen==$(imagen).data("id_imagen"))
        return false;

    $.ajax({
      url : url,
      type: "POST",
      dataType: "JSON",
      data: { iIdProducto: ID,IdImagen: $(imagen).data("id_imagen")},
      success: function (response) {
         
        var images = $(e.data.obj).parent().find(".dz-preview");

        if(response.status="success"){

            for(i=0;i<images.length;i++){

            //console.log(images[i]);
            $(images[i]).find(".dz-image").removeClass("predeterminado");
            }

            $(e.data.obj).find(".dz-image").addClass("predeterminado");
            root.id_imagen = $(imagen).data("id_imagen");
            root.PredeterminadoFile = file;
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        
      }
    })
}

  url = base_url + 'Dropshipping/ProductosProveedoresDropshippingController/uploadMultiple';
  dropZoneGloblal = new Dropzone("#id-divDropzone", {
    url: url,
    params: {
      iIdProducto: ID,
    },
    acceptedFiles: ".jpeg,.jpg,.png,.webp",
    uploadMultiple: false,
    maxFilesize: 1,//Peso en MB
    thumbnailHeight: 200,
    thumbnailWidth: 200,
    parallelUploads: 1,
    previewsContainer: "#dropzone-previews",
    previewTemplate:document.querySelector('#template-container').innerHTML,
    success: function(file, response) {
     //console.log("success XXXX")
     //console.log(response);
      response = JSON.parse(response);
      //console.log("function success");
      $(file.previewElement).data("id_imagen",response.id_imagen);
      $(file.previewElement).data("id_predeterminado",response.id_predeterminado);
      
      if(response.id_predeterminado==1)
        $(file.previewElement).find(".dz-image").addClass("predeterminado");

    },
    init : function() {
      let root = this;
      let dpzMultipleFiles = this;
      $('.dz-preview').remove();
      this.removeAllFiles;
      root.id_imagen = 0;
      //console.log(root);
      url = base_url + 'Dropshipping/ProductosProveedoresDropshippingController/get_image';
      $.ajax({
        url : url,
        type: "POST",
        dataType: "JSON",
        data: { iIdProducto: ID },
        success: function (response) {
          // console.log("reset init");
          // console.log(root);
          for(var k in response) {
            //console.log(k, response[k]);
            let Miniatura = { name: response[k].No_Producto_Imagen, size: response[k].Imagen_Tamano };
            root.emit("addedfile", Miniatura);
            root.emit("thumbnail", Miniatura, response[k].No_Producto_Imagen_url);
            root.emit("complete", Miniatura);
            root.emit("success", Miniatura,'{ "id_imagen": '+response[k].ID_Producto_Imagen+', "name_": "'+response[k].No_Producto_Imagen+'", "name": "'+response[k].No_Producto_Imagen+'","id_predeterminado":'+response[k].ID_Predeterminado+'}' );
            }
          $( "#dropzone-previews" ).scrollTop( 0 );
        },
        error: function (jqXHR, textStatus, errorThrown) {
          
        }
      })

       this.on("addedfile", file => {
          //console.log("function addedfile");
          $( "#dropzone-previews" ).scrollTop( 999999 );
          $( file.previewElement ).on( "click", ".remove",{obj: file.previewElement,root:root}, this._remove);
          $( file.previewElement ).on( "click", ".default",{obj: file.previewElement,file:file,root:root}, this._default);

       });

    }
  })
}

function form_Producto(){
  if (accion_producto == 'add_producto' || accion_producto == 'upd_producto') {
    var arrProductoEnlace = [];
    $( "#table-Producto_Enlace tbody tr" ).each(function(){
      var rows                = $(this);
      var $ID_Producto_Enlace = rows.find("td:eq(0)").text();
      var $Qt_Producto_Enlace = rows.find("td:eq(3)").text();
      var obj                 = {};
      
      obj.ID_Producto_Enlace  = $ID_Producto_Enlace;
      obj.Qt_Producto_Descargar = $Qt_Producto_Enlace;
      arrProductoEnlace.push(obj);
    });

    var arrProductoPrecioxMayor = [];
    $( "#table-precios_x_mayor tbody tr" ).each(function(){
      var rows = $(this);
      var $Qt_Producto_x_Mayor  = parseFloat(rows.find(".td-cantidad_x_mayor").text());
      var $Ss_Precio_x_Mayor    = parseFloat(rows.find(".td-precio_x_mayor").text());
      var obj                   = {};
      
      obj.Qt_Producto_x_Mayor = $Qt_Producto_x_Mayor;
      obj.Ss_Precio_x_Mayor = $Ss_Precio_x_Mayor;
      arrProductoPrecioxMayor.push(obj);
    });

    $( '.help-block' ).empty();
    if ( $( '#cbo-TiposItem' ).val() != '2' && $( '#cbo-Impuestos' ).val() == '0'){
      $( '#cbo-Impuestos' ).closest('.form-group').find('.help-block').html('Seleccionar impuesto');
      $( '#cbo-Impuestos' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $( '#cbo-TiposItem' ).val() != '2' && $( '#cbo-UnidadesMedida' ).val() == '0'){
      $( '#cbo-UnidadesMedida' ).closest('.form-group').find('.help-block').html('Seleccionar Unidad');
      $( '#cbo-UnidadesMedida' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $( '#cbo-TiposItem' ).val() != '2' && $( '#cbo-categoria' ).val() == '0'){
      $( '#cbo-categoria' ).closest('.form-group').find('.help-block').html('Seleccionar categoría');
      $( '#cbo-categoria' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ($("#checkbox-precios_x_mayor").prop("checked") == true && arrProductoPrecioxMayor.length === 0){
      alert('Activaste la opción precios por mayor debes de registrar las opciones');
    } else {
      arrProductoEnlace = null;
      iIDTipoExistenciaProducto = 1;
      
      var arrProducto = Array();
      arrProducto = {
        'EID_Empresa'               : $( '#txt-EID_Empresa' ).val(),
        'EID_Producto'              : $( '#txt-EID_Producto' ).val(),
        'ENu_Codigo_Barra': $('#txt-ENu_Codigo_Barra').val(),
        'ENo_Codigo_Interno': $('#txt-ENo_Codigo_Interno').val(),
        'Nu_Tipo_Producto'          : $( '#cbo-TiposItem' ).val(),
        'ID_Tipo_Producto'          : iIDTipoExistenciaProducto,
        'ID_Ubicacion_Inventario'   : $( '#cbo-UbicacionesInventario' ).val(),
        'Nu_Codigo_Barra'           : $( '#txt-Nu_Codigo_Barra' ).val(),
        'No_Producto'               : $( '[name="No_Producto"]' ).val(),
        'Ss_Precio' : $( '[name="Ss_Precio"]' ).val(),
        'Ss_Costo' : $( '[name="Ss_Costo"]' ).val(),
        'No_Codigo_Interno'         : $( '#txt-No_Codigo_Interno' ).val(),
        'ID_Impuesto'               : $( '#cbo-Impuestos' ).val(),
        'Nu_Lote_Vencimiento'       : $( '#cbo-lote_vencimiento' ).val(),
        'ID_Unidad_Medida'          : $( '#cbo-UnidadesMedida' ).val(),
        'ID_Marca'                  : $( '#cbo-Marcas' ).val(),
        'ID_Familia'                : $( '#cbo-categoria' ).val(),
        'ID_Sub_Familia'            : $( '#cbo-sub_categoria' ).val(),
        'Nu_Stock_Minimo'           : $( '#tel-Nu_Stock_Minimo' ).val(),
        'Nu_Stock_Maximo': $('#tel-Nu_Stock_Maximo').val(),
        'Qt_CO2_Producto'           : $( '#tel-Qt_CO2_Producto' ).val(),
        'Nu_Receta_Medica'          : $( '#cbo-receta_medica' ).val(),
        'ID_Laboratorio'            : $( '#cbo-laboratorio' ).val(),
        'Txt_Composicion'           : $( '#cbo-composicion' ).val(),
        'ID_Impuesto_Icbper': $('#cbo-impuesto_icbper').val(),
        'Nu_Favorito': $('#cbo-favorito').val(),
        'Nu_Compuesto'              : $( '#cbo-Compuesto' ).val(),
        'Nu_Estado'                 : $( '#cbo-Estado' ).val(),
        'Txt_Ubicacion_Producto_Tienda' : $( '#txt-Txt_Ubicacion_Producto_Tienda' ).val(),
        'Txt_Producto'              : $( '[name="Txt_Producto"]' ).val(),
        'ID_Producto_Sunat'         : $( '#hidden-ID_Tabla_Dato' ).val(),
        'ID_Tipo_Pedido_Lavado' : $( '#cbo-tipo_pedido_lavado' ).val(),
        'No_Imagen_Item' : dropZoneGloblal._predeterminado(),
        'Ss_Precio_Ecommerce_Online' : $( '[name="Ss_Precio_Ecommerce_Online"]' ).val(),
        'Ss_Precio_Ecommerce_Online_Regular' : $( '[name="Ss_Precio_Ecommerce_Online_Regular"]' ).val(),
        'ID_Familia_Marketplace' : $( '#cbo-categoria_marketplace' ).val(),
        'ID_Sub_Familia_Marketplace' : $( '#cbo-sub_categoria_marketplace' ).val(),
        'ID_Marca_Marketplace' : $( '#cbo-marca_marketplace' ).val(),
        'ID_Variante_Item_1': $('#cbo-variante_1').val(),
        'ID_Variante_Item_Detalle_1': $('#cbo-valor_1').val(),
        'ID_Variante_Item_2': $('#cbo-variante_2').val(),
        'ID_Variante_Item_Detalle_2': $('#cbo-valor_2').val(),
        'ID_Variante_Item_3': $('#cbo-variante_3').val(),
        'ID_Variante_Item_Detalle_3': $('#cbo-valor_3').val(),
        'Nu_Activar_Precio_x_Mayor': $("#checkbox-precios_x_mayor").prop("checked"),
        'Ss_Precio_Proveedor_Dropshipping' : $( '[name="Ss_Precio_Proveedor_Dropshipping"]' ).val(),
        'Ss_Precio_Vendedor_Dropshipping' : $( '[name="Ss_Precio_Vendedor_Dropshipping"]' ).val(),
      };

      $( '#btn-save' ).text('');
      $( '#btn-save' ).attr('disabled', true);
      $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
    
      $( '#modal-loader' ).modal('show');
      
      arrProductoImagen = dropZoneGloblal._getProductos();

      url = base_url + 'Dropshipping/ProductosProveedoresDropshippingController/crudProducto';
      $.ajax({
        type      : 'POST',
        dataType  : 'JSON',
        url       : url,
        data      : {
          arrProducto : arrProducto,
          arrProductoEnlace : arrProductoEnlace,
          arrProductoPrecioxMayor : arrProductoPrecioxMayor,
          arrProductoImagen :arrProductoImagen
        },
        success : function( response ){
          $( '#modal-loader' ).modal('hide');
          
          $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
          $( '#modal-message' ).modal('show');
          
          if (response.status == 'success'){
            accion_producto = '';
            
            $( '#form-Producto' )[0].reset();
            $( '.div-AgregarEditar' ).hide();
            $( '.div-Listar' ).show();
            $( '.modal-message' ).addClass(response.style_modal);
            $( '.modal-title-message' ).text(response.message);
            reload_table_producto();
            setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);            
          } else {
            $( '.modal-message' ).addClass(response.style_modal);
            $( '.modal-title-message' ).text(response.message);
            setTimeout(function() {$('#modal-message').modal('hide');}, 3100);
          }
          
          $( '#btn-save' ).text('');
          $( '#btn-save' ).append( '<span class="fa fa-save"></span> Guardar (ENTER)' );
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
          $( '#btn-save' ).append( '<span class="fa fa-save"></span> Guardar' );
          $( '#btn-save' ).attr('disabled', false);
        }
      });
    }
  }// /. if de accion_producto
}

function eliminarProducto(ID_Empresa, ID, Nu_Codigo_Barra, Nu_Compuesto, accion_producto, sNombreImagenItem){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });
  
  $(document).bind('keydown', 'alt+l', function(){
    if ( accion_producto=='delete' ) {
      _eliminarProducto($modal_delete, ID_Empresa, ID, Nu_Codigo_Barra, Nu_Compuesto, sNombreImagenItem);
      accion_producto='';
    }
  });

  $( '#btn-save-delete' ).off('click').click(function () {
    _eliminarProducto($modal_delete, ID_Empresa, ID, Nu_Codigo_Barra, Nu_Compuesto, sNombreImagenItem);
  });
}

function reload_table_producto() {
  table_producto.ajax.reload(null, false);
}

function _eliminarProducto($modal_delete, ID_Empresa, ID, Nu_Codigo_Barra, Nu_Compuesto, sNombreImagenItem){
  $( '#modal-loader' ).modal('show');
  
  if (Nu_Codigo_Barra == '')
    Nu_Codigo_Barra = '-';
  
  url = base_url + 'Dropshipping/ProductosProveedoresDropshippingController/eliminarProducto/' + ID_Empresa + '/' + ID + '/' + Nu_Codigo_Barra + '/' + Nu_Compuesto + '/' + sNombreImagenItem;
  $.ajax({
    url       : url,
    type      : "GET",
    dataType  : "JSON",
    success: function( response ){
      $( '#modal-loader' ).modal('hide');
      
      $modal_delete.modal('hide');
      $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
      $( '#modal-message' ).modal('show');
      
      if (response.status == 'success'){
        accion_producto = '';
            
        $( '.modal-message' ).addClass(response.style_modal);
        $( '.modal-title-message' ).text(response.message);
        setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
        reload_table_producto();
      } else {
        accion_producto = '';
        $( '.modal-message' ).addClass(response.style_modal);
        $( '.modal-title-message' ).text(response.message);
        setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      accion_producto = '';
      $( '#modal-loader' ).modal('hide');
      $modal_delete.modal('hide');
      $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
      
      $( '#modal-message' ).modal('show');
      $( '.modal-message' ).addClass( 'modal-danger' );
      $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
      setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
      
      //Message for developer
      console.log(jqXHR.responseText);
    },
  });
}

function cambiarEstadoTienda(ID, Nu_Estado) {
  var $modal_delete = $('#modal-message-delete');
  $modal_delete.modal('show');

  $('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
  $('.modal-message-delete').addClass('modal-success');

  var sNombreEstado = (Nu_Estado == 1 ? 'mostrar' : 'ocultar');

  $('.modal-title-message-delete').text('¿Deseas ' + sNombreEstado + ' ítem en la tienda?');

  $('#btn-cancel-delete').off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $('#btn-save-delete').off('click').click(function () {
    $('#modal-loader').modal('show');

    url = base_url + 'Dropshipping/ProductosProveedoresDropshippingController/cambiarEstadoTienda/' + ID + '/' + Nu_Estado;
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
          reload_table_producto();
        } else {
          $('.modal-message').addClass(response.style_modal);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1500);
        }
      }
    });
  });
}

function cambiarEstadoDestacado(ID, Nu_Estado) {
  var $modal_delete = $('#modal-message-delete');
  $modal_delete.modal('show');

  $('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
  $('.modal-message-delete').addClass('modal-success');
  
  var sNombreEstado = (Nu_Estado == 1 ? 'mostrar' : 'ocultar');

  $('.modal-title-message-delete').text('¿Deseas ' + sNombreEstado + ' en la sección destacado este ítem?');

  $('#btn-cancel-delete').off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $('#btn-save-delete').off('click').click(function () {
    $('#modal-loader').modal('show');

    url = base_url + 'Dropshipping/ProductosProveedoresDropshippingController/cambiarEstadoDestacado/' + ID + '/' + Nu_Estado;
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
          reload_table_producto();
        } else {
          $('.modal-message').addClass(response.style_modal);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1500);
        }
      }
    });
  });
}

function isExistTableTemporalPreciosxMayor($Qt_Producto_x_Mayor){
  return Array.from($('tr[id*=tr_producto_precio_x_mayor]'))
    .some(element => ($('td:nth(0)',$(element)).html()==$Qt_Producto_x_Mayor));
}