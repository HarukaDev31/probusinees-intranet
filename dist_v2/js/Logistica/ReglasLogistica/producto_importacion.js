var url;
var table_producto;
var accion_producto = '', editorText='';
function importarExcelProductos(){
  $( ".modal_importar_producto-laeshop" ).modal( "show" );
}

$(function () {
  $( '.btn-agregar_variante' ).on('click', agregarVariante);
  $( '.chk-variantes' ).on('click', verificarCheckboxVariantes);
  $( '.chk-productos_relacionados' ).on('click', verificarCheckboxProductosRelacionados);
  $( '.btn-tipo_productos_relacionados' ).on('click', getNuTipoProductosRelacionados);
  $( '.btn-agregar_producto_relacionado').on('click', clickProductoRelacionado);
  //PRECIOS AL POR MAYOR
  let dropZoneGloblal;
  
  $( '.autocompletar_dropshipping_mis_productos' ).autoComplete({
    minChars: 0,
    source: function (term, response) {
      term = term.trim();
      if (term.length > 2) {
        var term                = term.toLowerCase();
        var global_class_method = $( '.autocompletar_dropshipping_mis_productos' ).data('global-class_method');
        
        $.post( base_url + global_class_method, { global_search : term }, function( arrData ){
          response(arrData);
        }, 'JSON');
      }
    },
    renderItem: function (item, search){
      search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
      var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");
      return '<div title="' + caracteresValidosAutocomplete(item.Nombre) + '" class="autocomplete-suggestion" data-id="' + item.ID + '" data-codigo="' + item.Codigo + '" data-nombre="' + caracteresValidosAutocomplete(item.Nombre) + '" data-val="' + search + '"><span class="hidden-xs"><strong>Producto: </strong></span> ' + caracteresValidosAutocomplete(item.Nombre).replace(re, "<b>$1</b>") + ' | <span class="hidden-sm hidden-md hidden-lg"><br></span><strong>Precio: </strong>' + item.Ss_Precio_Ecommerce_Online_Regular + '<div style="border-bottom-style: ridge; border-bottom-color: black; border-bottom-width: 1px"></div></div>';
    },
    onSelect: function(e, term, item){
      $( '#txt-AID_Producto_Mis_Productos' ).val(item.data('id'));
      $( '#txt-ACodigo_Mis_Productos' ).val(item.data('codigo'));
      $( '#txt-ANombre_Mis_Productos' ).val(item.data('nombre'));
    }
  });

  $( '#txt-ANombre_Mis_Productos' ).on('keypress', enterProductoRelacionado);

  //editor = $('.textarea-descripcion_item').wysihtml5();
  $('#textarea-descripcion_item').summernote({
    placeholder: 'Opcional',
    toolbar: [
      ['style', ['style']],
      ['style', ['bold', 'italic', 'underline', 'clear']],
      ['fontname', ['fontname']],
      ['fontsize', ['fontsize']],
      ['color', ['color']],
      ['para', ['ul', 'ol', 'paragraph']],
      ['insert', ['link', 'picture', 'video']],
      ['view', ['fullscreen', 'codeview', 'help']],
    ],
    tabsize: 4,
    height: 200
  });
  
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
  
  $( '.div-AgregarEditar' ).hide();
  
  $('.select2').select2();
  
  url = base_url + 'Logistica/ReglasLogistica/ProductoImportacion/ajax_list';
  table_producto = $('#table-Producto').DataTable({
    'dom': 'B<"top">frt<"bottom"lip><"clear">',
    buttons     : [{
      extend: 'excel',
      text      : '<i class="fa fa-file-excel-o color_icon_excel"></i> Excel',
      titleAttr : 'Excel',
      exportOptions: {
        columns: ':visible'
      }
    },
    {
      extend: 'pdfHtml5',
      text      : '<i class="fa fa-file-pdf-o color_icon_pdf"></i> PDF',
      orientation: 'landscape',//portrait
      customize: function (doc) {
        doc.styles.tableHeader.fontSize = 7;
        doc.defaultStyle.fontSize = 6.5; //<-- set fontsize to 16 instead of 10 
        
        var arr2 = $('.img-fluid').map(function () {
          return this.src;
        }).get();

        var imas = 0;
        for (var x = 1; x < doc.content[1].table.body.length; x++) {
          var sUrlImage = doc.content[1].table.body[x][$('#table-Producto thead tr th').length - 1];
          if (sUrlImage.text != 'Sin imagen') {
            for (var i = imas, c = 1; i < arr2.length; i++, c++) {
              doc.content[1].table.body[x][$('#table-Producto thead tr th').length - 1] = {
                image: arr2[i],
                width: 100,
              };
              break;
            }
            imas++;
          }
        }
      },
      titleAttr : 'PDF',
      exportOptions: {
        columns: ':visible',
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
      'className' : 'text-left',
      'targets'   : 'no-sort_left',
      'orderable' : false,
    },{
      'className' : 'text-left',
      'targets'   : 'no-sort_right',
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

  url = base_url + 'HelperController/getEmpresas';
  var selected = '';
  $.post(url, function (response) {
    $('#cbo-Empresas').html('<option value="0" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++) {
      selected = '';
      if ($('#header-a-id_empresa').val() == response[i].ID_Empresa)
        selected = 'selected="selected"';
      $('#cbo-Empresas').append('<option value="' + response[i].ID_Empresa + '" ' + selected + '>' + response[i].No_Empresa + '</option>');
    }
  }, 'JSON');
  
  $("#btn-cancelar").click(function(){
     //table_producto.ajax.reload();
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

      url = base_url + 'Logistica/ReglasLogistica/ProductoImportacion/updActivarMasivamenteProductos';
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

      url = base_url + 'Logistica/ReglasLogistica/ProductoImportacion/updActivarMasivamenteProductos';
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

  $("#textarea-descripcion_item").summernote("code", '');

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
    //$( '#cbo-TiposItem' ).html( '<option value="">- Seleccionar -</option>' );    
    for (var i = 0; i < responseTiposProducto.length; i++) {
      if(responseTiposProducto[i].Nu_Valor==1){//1=producto
        $( '#cbo-TiposItem' ).html( '<option value="' + responseTiposProducto[i].Nu_Valor + '">' + responseTiposProducto[i].No_Descripcion + '</option>' );
      }
    }
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
  url = base_url + 'HelperImportacionController/getCategorias';
  $.post(url, { sTipoData: 'categoria' }, function (response) {
    if (response.sStatus == 'success') {
      var l = response.arrData.length;
      if (l == 1) {
        $('#cbo-categoria').html('<option value="' + response.arrData[0].ID + '">' + response.arrData[0].Nombre + '</option>');
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
  
  url = base_url + 'HelperController/getUnidadesMedida';
  $.post( url , function( responseUnidadMedidas ){
    if ( responseUnidadMedidas.length == 1 ) {
      $( '#cbo-UnidadesMedida_Precio' ).html( '<option value="' + responseUnidadMedidas[0].ID_Unidad_Medida + '" selected>' + responseUnidadMedidas[0].No_Unidad_Medida + '</option>' );
    } else {
      $( '#cbo-UnidadesMedida_Precio' ).html( '<option value="">- Seleccionar -</option>' );
      for (var i = 0; i < responseUnidadMedidas.length; i++) {
        $( '#cbo-UnidadesMedida_Precio' ).append( '<option value="' + responseUnidadMedidas[i].ID_Unidad_Medida + '">' + responseUnidadMedidas[i].No_Unidad_Medida + '</option>' );
      }
    }
    $('#modal-loader').modal('hide');
  }, 'JSON');

  $( '#cbo-Estado' ).html( '<option value="1">Visible</option>' );
  $( '#cbo-Estado' ).append( '<option value="0">Oculto</option>' );

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
  Dropzone.prototype.defaultOptions.dictDefaultMessage = "Presionar para subir imágen";
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

  url = base_url + 'Logistica/ReglasLogistica/ProductoImportacion/uploadMultiple';
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

      this.on("error", function (file, message) {
            $( '#modal-message' ).modal('show');
            $( '.modal-message' ).addClass( 'modal-danger' );
            $( '.modal-title-message' ).text( message );
            setTimeout(function() {$('#modal-message').modal('hide');}, 2500);
            this.removeFile(file);
        }); 

    },
  })
  
}// agregarProducto

function verProducto(ID, No_Imagen_Item, Nu_Version_Imagen){
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
  
  url = base_url + 'Logistica/ReglasLogistica/ProductoImportacion/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $( '.div-variantes' ).html('');
      $( '.table-productos_variante_valores tbody' ).html('');
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
        $( '#cbo-Impuestos' ).html( '<option value="">- Seleccionar -</option>' );
        for (var i = 0; i < responseImpuestos.length; i++){
          selected = '';
          if(response.ID_Impuesto == responseImpuestos[i].ID_Impuesto)
            selected = 'selected="selected"';
          $( '#cbo-Impuestos' ).append( '<option value="' + responseImpuestos[i].ID_Impuesto + '" data-ss_impuesto="' + responseImpuestos[i].Ss_Impuesto + '" data-nu_tipo_impuesto="' + responseImpuestos[i]['Nu_Tipo_Impuesto'] + '" ' + selected + '>' + responseImpuestos[i]['No_Impuesto'] + '</option>' );
        }
      }, 'JSON');

      url = base_url + 'HelperImportacionController/getCategorias';
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
        $( '#cbo-UnidadesMedida' ).html( '' );
        for (var i = 0; i < responseUnidadMedidas.length; i++){
          selected = '';
          if (response.ID_Unidad_Medida == responseUnidadMedidas[i].ID_Unidad_Medida)
            selected = 'selected="selected"';
          $( '#cbo-UnidadesMedida' ).append( '<option value="' + responseUnidadMedidas[i].ID_Unidad_Medida + '" ' + selected + '>' + responseUnidadMedidas[i].No_Unidad_Medida + '</option>' );
        }
      }, 'JSON');
      
      url = base_url + 'HelperController/getUnidadesMedida';
      $.post( url , function( responseUnidadMedidasPrecio ){
        $( '#cbo-UnidadesMedida_Precio' ).html( '' );
        for (var i = 0; i < responseUnidadMedidasPrecio.length; i++){
          selected = '';
          if (response.ID_Unidad_Medida_Precio == responseUnidadMedidasPrecio[i].ID_Unidad_Medida)
            selected = 'selected="selected"';
          $( '#cbo-UnidadesMedida_Precio' ).append( '<option value="' + responseUnidadMedidasPrecio[i].ID_Unidad_Medida + '" ' + selected + '>' + responseUnidadMedidasPrecio[i].No_Unidad_Medida + '</option>' );
        }
      }, 'JSON');

      $( '#cbo-Estado' ).html('');
      for (var i = 0; i < 2; i++){
        selected = '';
        if (response.Nu_Activar_Item_Lae_Shop == i)
          selected = 'selected="selected"';
        $( '#cbo-Estado' ).append( '<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Oculto' : 'Visible') + '</option>' );
      }
      
      $("#textarea-descripcion_item").summernote("code", response.Txt_Producto);
      $( '[name="Txt_Url_Video_Lae_Shop"]' ).val( response.Txt_Url_Video_Lae_Shop );
      $('[name="Txt_Url_Recurso_Drive"]').val(response.Txt_Url_Recurso_Drive);

      $('.chk-variantes').prop('checked', false);
      if(response.Nu_Estado_Variantes == 1) {
        $('.chk-variantes').prop('checked', true);
        
        $( '.table-productos_variante_valores tbody' ).html('<tr><td colspan="5" class="text-center"><i class="fa fa-refresh fa-spin fa-lg fa-fw"></i> Cargando...</td></tr>');
        url = base_url + 'Logistica/ReglasLogistica/ProductoImportacion/getVariantes/' + ID;
        $.ajax({
          url : url,
          type: "GET",
          dataType: "JSON",
          success: function(responseVariantes){
            llenarVariantes(responseVariantes['variantes']);
            llenarProductosVarianteValores(responseVariantes['productosVarianteValores']);
          },
          error:function(jqXHR, textStatus, errorThrown) {
          }
        });
      }

      verificarCheckboxVariantes();

      $( '.div-productos_relacionados_cuerpo' ).addClass('hidden');
      $( '.div-productos_relacionados_aleatorio' ).addClass('hidden');
      $( '.div-productos_relacionados_manual' ).addClass('hidden');
      $( '.table-productos_relacionados_manual tbody' ).html('');
      $( '.btn-tipo_productos_relacionados' ).removeClass('btn-success');
      $( '[name="Nu_Tipo_Productos_Relacionados"]' ).val(response.Nu_Tipo_Productos_Relacionados);
      $( '[name="Nu_Cantidad_Productos_Relacionados"]' ).val(response.Nu_Cantidad_Productos_Relacionados);
      $( '.chk-productos_relacionados' ).prop('checked', false);
      switch (response.Nu_Tipo_Productos_Relacionados) {
        case '1':
          $( '.btn-tipo_aleatorio' ).addClass('btn-success');
          break;
      
        case '2':
          $( '.btn-tipo_manual' ).addClass('btn-success');
          break;
      }
      if(response.Nu_Estado_Productos_Relacionados == 1) {
        $('.chk-productos_relacionados').prop('checked', true);
      }
      mostrarDivTipoProductoRelacionado(response.Nu_Tipo_Productos_Relacionados);
      verificarCheckboxProductosRelacionados();
      
      $('#modal-loader').modal('hide');
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
  Dropzone.prototype.defaultOptions.dictDefaultMessage = "Presionar para subir imágen";
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
  
    url = base_url + 'Logistica/ReglasLogistica/ProductoImportacion/removeFileImage';
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
  
    url = base_url + 'Logistica/ReglasLogistica/ProductoImportacion/DefaultImagen';
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

  url = base_url + 'Logistica/ReglasLogistica/ProductoImportacion/uploadMultiple';
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
      url = base_url + 'Logistica/ReglasLogistica/ProductoImportacion/get_image';
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

        this.on("error", function (file, message) {
            $( '#modal-message' ).modal('show');
            $( '.modal-message' ).addClass( 'modal-danger' );
            $( '.modal-title-message' ).text( message );
            setTimeout(function() {$('#modal-message').modal('hide');}, 2500);
            this.removeFile(file);
                       
        }); 
    }
  })
}

function isValidURL(string, social) {
  let res = null;
  switch(social) {
    case 'youtube':
      res = string.match(/(?:https?:\/\/)?(?:www\.)?youtu(?:\.be\/|be.com\/\S*(?:watch|embed)(?:(?:(?=\/[-a-zA-Z0-9_]{11,}(?!\S))\/)|(?:\S*v=|v\/)))([-a-zA-Z0-9_]{11,})/g);
      break;
    case 'tiktok':
      res = string.match(/(?:https?:\/\/)?(?:(?:www)\.)(?:tiktok.com)\/(?:embed)\/\w{19}/g);
      break;
  } 
  return (res !== null)
}



function form_Producto(){
  if (accion_producto == 'add_producto' || accion_producto == 'upd_producto') {
    if($( '[name="Txt_Url_Video_Lae_Shop"]' ).val()) {
      if(!isValidURL($( '[name="Txt_Url_Video_Lae_Shop"]' ).val(), 'youtube') && !isValidURL($( '[name="Txt_Url_Video_Lae_Shop"]' ).val(), 'tiktok')) {
          $( '[name="Txt_Url_Video_Lae_Shop"]' ).closest('.form-group').find('.help-block').html('Ingrese url válida');
        $( '[name="Txt_Url_Video_Lae_Shop"]' ).closest('.form-group').removeClass('has-success').addClass('has-error');
        return;
      }
    }
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
        'Txt_Producto'              : $( '#textarea-descripcion_item' ).summernote('code'),
        'Txt_Url_Video_Lae_Shop'              : $( '[name="Txt_Url_Video_Lae_Shop"]' ).val(),
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
        'Txt_Url_Recurso_Drive' : $( '[name="Txt_Url_Recurso_Drive"]' ).val(),
        'Nu_Estado_Variantes': $( '.chk-variantes' ).prop('checked') ? 1 : 0,
        'Nu_Estado_Productos_Relacionados': $( '.chk-productos_relacionados' ).prop('checked') ? 1 : 0,
        'Nu_Tipo_Productos_Relacionados': $( '[name="Nu_Tipo_Productos_Relacionados"]' ).val(),
        'Nu_Cantidad_Productos_Relacionados': $( '[name="Nu_Cantidad_Productos_Relacionados"]' ).val(),
        'ID_Unidad_Medida_Precio' : $( '#cbo-UnidadesMedida_Precio' ).val(),
      };
      $( '#btn-save' ).text('');
      $( '#btn-save' ).attr('disabled', true);
      $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
    
      $( '#modal-loader' ).modal('show');
      
      arrProductoImagen = dropZoneGloblal._getProductos();
      if(!validarPreciosProductosVariantesValores()) {
        $( '#modal-loader' ).modal('hide');
        $( '#btn-save' ).text('');
        $( '#btn-save' ).append( 'Guardar' );
        $( '#btn-save' ).attr('disabled', false);
        return false;
      }
      url = base_url + 'Logistica/ReglasLogistica/ProductoImportacion/crudProducto';
      $.ajax({
        type      : 'POST',
        dataType  : 'JSON',
        url       : url,
        data      : {
          arrProducto : arrProducto,
          arrProductoEnlace : arrProductoEnlace,
          arrProductoPrecioxMayor : arrProductoPrecioxMayor,
          arrProductoImagen :arrProductoImagen,
          arrVariantes: getObjetoVariantes(),
          arrProductosVarianteValores: getObjetoProductosVarianteValores(),
          arrProductosRelacionados: getArregloProductosRelacionados()
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
  
  url = base_url + 'Logistica/ReglasLogistica/ProductoImportacion/eliminarProducto/' + ID_Empresa + '/' + ID + '/' + Nu_Codigo_Barra + '/' + Nu_Compuesto + '/' + sNombreImagenItem;
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

    url = base_url + 'Logistica/ReglasLogistica/ProductoImportacion/cambiarEstadoTienda/' + ID + '/' + Nu_Estado;
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

    url = base_url + 'Logistica/ReglasLogistica/ProductoImportacion/cambiarEstadoDestacado/' + ID + '/' + Nu_Estado;
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

//VARIANTES
function noLanzarSubmit(e) {
  if(e.keyCode == 13 || e.which == 13 || e.code == 'Enter') {
    e.preventDefault();
  }
} 

function limpiarMensajeErrorVariantes() {
  const divVariantes = document.querySelectorAll('.div-variante_datos');
  for (let i = 0; i < divVariantes.length; i++) {
    divVariantes[i].querySelector('.help-block').innerHTML = '';
    divVariantes[i].classList.remove('has-error');
  }
}

function mensajeErrorVariante(inputVariante, mensaje) {
  inputVariante.value = inputVariante.value.trim();
  const divVariante = inputVariante.closest('.div-variante_datos');
  divVariante.querySelector('.help-block').innerHTML = mensaje;
  divVariante.classList.add('has-error');
}

function agregarVariante() {  
  if(!variantesVacias() && !variantesRepetidas()){
    document.querySelector('.div-variantes').insertAdjacentHTML('BeforeEnd',getPlantillaVariante());
  }  
}

function llenarVariantes(variantes) {
  let plantillaVariantes = '';
  if(variantes.length > 0) {
    for (let i = 0; i < variantes.length; i++) {
      const ID_Variante = variantes[i]['ID_Variante'];
      const No_Variante = variantes[i]['No_Variante'];
      const varianteValores = variantes[i]['valores'];
      plantillaVariantes = plantillaVariantes + getPlantillaVariante(ID_Variante, No_Variante, varianteValores);
    }
  } else {
    plantillaVariantes = getPlantillaVariante();
  }
  document.querySelector('.div-variantes').insertAdjacentHTML('BeforeEnd', plantillaVariantes);
  const inputVariantesValores = getInputVariantesValores();
  for (let i = 0; i < inputVariantesValores.length; i++) {
    inputVariantesValores[i].disabled = true;    
  }
}

function getInputVariantes() {
  return Array.from(document.querySelectorAll('[name="No_Variante"]'));
}

function getInputVariantesValores() {
  return Array.from(document.querySelectorAll('[name="No_Variante_Valor"]'));
}


function varianteVacia(variante) {
  let resultado = false;
  if(variante.value.trim().length === 0){
    mensajeErrorVariante(variante, 'Ingresar el nombre de la variante');
    resultado = true;
  }
  return resultado;
}

function variantesVacias() {
  let contador = 0;
  const variantes = getInputVariantes();
  for (let i = 0; i < variantes.length; i++) {
    if(varianteVacia(variantes[i])) {
      contador++;
    }
  }
  return contador > 0 ? true : false;
}

function variantesRepetidas() {
  let contador = 0;
  const variantes = getInputVariantes();
  const totalVariantes = variantes.length;
  if(totalVariantes > 0) {
    let punteroUno = 0;
    let punteroDos = punteroUno + 1;
    while(punteroUno < totalVariantes - 1) {
      if(variantes[punteroUno].value.trim().toLowerCase() === variantes[punteroDos].value.trim().toLowerCase()){
        mensajeErrorVariante(variantes[punteroUno], 'Nombre de la variante repetido');
        mensajeErrorVariante(variantes[punteroDos], 'Nombre de la variante repetido');
        punteroUno += 1;
        punteroDos = punteroUno + 1;
        contador++;
      } else {
        if(punteroDos=== totalVariantes - 1){
          punteroUno += 1;
        } else {
          punteroDos += 1;
        }
      }
    }
  }  
  return contador > 0 ? true : false;
}

function getPlantillaVariante(ID_Variante = 0, No_Variante = '', varianteValores = []) {
  return `<div class="div-variante">
    <div class="row">
      <div class="col-md-6">
        <div class="div-variante_datos">
          <label>Nombre de Variante</label>
          <div class="input-group">
          <input type="hidden" name="ID_Variante" value="${ID_Variante}" />
          <input type="text" name="No_Variante" autocomplete="off" value="${No_Variante}" class="form-control input-No_Variante" onkeypress="noLanzarSubmit(event)" onkeyup="verificarVariantes()" placeholder="Obligatorio">
            <span class="input-group-btn">
              <button class="btn btn-default" onclick="eliminarVariante(event)" type="button"><i class="fa fa-trash"></i></button>
            </span>
          </div>
          <span class="help-block"></span>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="div-variante_valores">
          ${varianteValores.length > 0 ? getPlantillaVarianteValores(varianteValores) : ''}
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="div-variante_valores_boton text-right">
          <button type="button" class="btn btn-default" onclick ="agregarVarianteValor(event)"><i class="fa fa-plus-circle"></i> Agregar Valor</button>
        </div>
      </div>
    </div>
  </div>`;
}

function verificarVariantes() {
  limpiarMensajeErrorVariantes();
  variantesVacias();
  variantesRepetidas();
}

function eliminarVariante(e) {
  const divVariante = e.target.closest('.div-variante');
  divVariante.remove();
  verificarVariantes();
  generarArregloProductosVarianteValores();
}

function getPlantillaVarianteValores(varianteValores) {
  let templateVarianteValores = '';
  for (let i = 0; i < varianteValores.length; i++) {
    templateVarianteValores = templateVarianteValores + getPlantillaVarianteValor(varianteValores[i]['ID_Variante_Valor'], varianteValores[i]['No_Variante_Valor']);
  }
  return templateVarianteValores;
}

function limpiarMensajeErrorVarianteValores(variante) {
  let divVarianteValores = variante.querySelectorAll('.div-variante_valor');
  for (let i = 0; i < divVarianteValores.length; i++) {
    divVarianteValores[i].querySelector('.help-block').innerHTML = '';
    divVarianteValores[i].classList.remove('has-error');
  }
}

function mensajeErrorVarianteValor(inputVarianteValor, mensaje) {
  inputVarianteValor.value = inputVarianteValor.value.trim();
  const divVarianteValor = inputVarianteValor.closest('.div-variante_valor');
  divVarianteValor.querySelector('.help-block').innerHTML = mensaje;
  divVarianteValor.classList.add('has-error');
}

function agregarVarianteValor(e) {
  const divVariante = e.target.closest('.div-variante');  
  const varianteClaseError = divVariante.querySelector('.div-variante_datos').classList.contains("has-error");
  if(!varianteVacia(divVariante.querySelector('[name="No_Variante"]')) && !varianteClaseError && !varianteValoresVacios(e) && !varianteValoresRepetidos(e)){
    const inputsVarianteValores = Array.from(divVariante.querySelectorAll('[name="No_Variante_Valor"'));
    for (let i = 0; i < inputsVarianteValores.length; i++) {
      inputsVarianteValores[i].disabled = true;      
    }
    generarArregloProductosVarianteValores();
    divVariante.querySelector('.div-variante_valores').insertAdjacentHTML('BeforeEnd',getPlantillaVarianteValor());
  }
}

function getInputVarianteValores(e) {
  return Array.from(e.target.closest('.div-variante').querySelectorAll('[name="No_Variante_Valor"]'));
}


function varianteValoresVacios(e) {
  let contador = 0;
  const varianteValores = getInputVarianteValores(e);
  for (let i = 0; i < varianteValores.length; i++) {
    if(varianteValores[i].value.trim().length === 0){
      mensajeErrorVarianteValor(varianteValores[i], 'Ingresar el valor de la variante');
      contador++;
    }
  }
  return contador > 0 ? true : false;
}

function varianteValoresRepetidos(e) {
  let contador = 0;
  const varianteValores = getInputVarianteValores(e);
  const totalVarianteValores = varianteValores.length;
  if(totalVarianteValores > 0) {
    let punteroUno = 0;
    let punteroDos = punteroUno + 1;
    while(punteroUno < totalVarianteValores - 1) {
      if(varianteValores[punteroUno].value.trim().toLowerCase() === varianteValores[punteroDos].value.trim().toLowerCase()){
        mensajeErrorVarianteValor(varianteValores[punteroUno], 'Valor de la variante repetido');
        mensajeErrorVarianteValor(varianteValores[punteroDos], 'Valor de la variante repetido');
        punteroUno += 1;
        punteroDos = punteroUno + 1;
        contador++;
      } else {
        if(punteroDos=== totalVarianteValores - 1){
          punteroUno += 1;
        } else {
          punteroDos += 1;
        }
      }
    }
  }  
  return contador > 0 ? true : false;
}

function getPlantillaVarianteValor(ID_Variante_Valor = 0, No_Variante_Valor = '') {
  return `<div class="div-variante_valor">
    <label>Valor de Variante</label>
    <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-html="true" data-placement="bottom" title="Ingresar el valor de la variante y presionar la tecla ENTER. Ej. Rojo (Enter)" data-original-title="Ingresar el valor de la variante y presionar la tecla ENTER. Ej. Rojo (Enter)">
      <i class="fa fa-info-circle"></i>
    </span>
    <div class="input-group">
      <input type="hidden" name="ID_Variante_Valor" value="${ID_Variante_Valor}">
      <input type="text" name="No_Variante_Valor" autocomplete="off" value="${No_Variante_Valor}" class="form-control input-No_Variante_Valor" onkeypress="noLanzarSubmit(event)" onkeyup="verificarVarianteValor(event)" data-no-variante-valor="" placeholder="Obligatorio">
      <span class="input-group-btn">
        <button class="btn btn-default" onclick="eliminarVarianteValor(event)" type="button"><i class="fa fa-trash"></i></button>
      </span>
    </div>
    <span class="help-block"></span>
  </div>`;
}

function verificarVarianteValor(e) {
  const inputVariableValor = e.target;
  const divVarianteValores = inputVariableValor.closest('.div-variante_valores');
  if(e.keyCode == 13 || e.which == 13 || e.code == 'Enter') {
    if(!inputVariableValor.closest('.div-variante_valor').classList.contains('has-error')) {
      inputVariableValor.disabled = true;
      generarArregloProductosVarianteValores();
    }
  }
  limpiarMensajeErrorVarianteValores(divVarianteValores);
  varianteValoresVacios(e);
  varianteValoresRepetidos(e);
}

function eliminarVarianteValor(e) {
  const divVarianteValor = e.target.closest('.div-variante_valor');
  divVarianteValor.remove();
  generarArregloProductosVarianteValores();
}

function getObjetoVariantes() {
  return Array.from(document.querySelectorAll('.div-variante'))
  .filter(variante => variante.querySelector('[name="No_Variante"]').value.trim().length > 0)
  .map(variante => {
    return {
      'ID_Variante': variante.querySelector('[name="ID_Variante"]').value,
      'No_Variante': variante.querySelector('[name="No_Variante"]').value.trim(),
      'valores': Array.from(variante.querySelectorAll('.div-variante_valor'))
      .filter(varianteValor => varianteValor.querySelector('[name="No_Variante_Valor"]').value.trim().length > 0)
      .map(varianteValor => {
        return {
          'ID_Variante_Valor': varianteValor.querySelector('[name="ID_Variante_Valor').value,
          'No_Variante_Valor': varianteValor.querySelector('[name="No_Variante_Valor').value.trim()
        }
      })
    }
  });
}

function llenarProductosVarianteValores(productosVarianteValores) {
  let plantillaProductoVarianteValores = '';
  const tbody = document.querySelector('.table-productos_variante_valores tbody');
  if(productosVarianteValores.length > 0) {
    for (let i = 0; i < productosVarianteValores.length; i++) {
      let noProducto = productosVarianteValores[i]['No_Producto'].split('|');
      noProducto.shift();
      let Nu_Imagen_Producto_Variante_Valores = productosVarianteValores[i]['No_Imagen_Item'] !== null ? buscarIndiceImagenProductoVarianteValores(productosVarianteValores[i]['No_Imagen_Item']) : '';
      plantillaProductoVarianteValores += getPlantillaProductoVarianteValores(noProducto.join('|'), productosVarianteValores[i]['ID_Producto'], productosVarianteValores[i]['Nu_Codigo_Barra'], productosVarianteValores[i]['Ss_Precio_Ecommerce_Online_Regular'], productosVarianteValores[i]['Ss_Precio_Ecommerce_Online'], productosVarianteValores[i]['Nu_Activar_Item_Lae_Shop'], Nu_Imagen_Producto_Variante_Valores);
    }
    tbody.innerHTML = plantillaProductoVarianteValores;
  }
}

function buscarIndiceImagenProductoVarianteValores(Nu_Imagen_Producto_Variante_Valores) {
  const indice = Array.from(document.querySelectorAll('.dz-image')).findIndex(imagen => imagen.querySelector('img').src === Nu_Imagen_Producto_Variante_Valores);
  return indice > -1 ? indice : '';
}

function generarArregloProductosVarianteValores() {
  const variantes = getObjetoVariantes();
  const arrProductoVarianteValores = getObjetoProductosVarianteValores();
  document.querySelector('.table-productos_variante_valores tbody').innerHTML = '';
  if(variantes.length > 0) {
    let existenValores = false;
    let productosVarianteValores = [''];
    for (let i = 0; i < variantes.length; i++) {
      if(variantes[i]['valores'].length > 0){
        productosVarianteValores = compararVarianteValores(variantes[i]['valores'], productosVarianteValores);
        existenValores = true;
      }      
    }
    if(existenValores){
      getPlantillaProductosVarianteValores(productosVarianteValores, arrProductoVarianteValores);
    }
  }
}

function compararVarianteValores(varianteValores, arrInicial) {
  let arrNuevo = []
  arrNuevo = [];
  let p1 = p2 = 0;
  let continuar = true;
  while(continuar) {
    arrNuevo.push(arrInicial[p1].length > 0 ? arrInicial[p1] + '|' + varianteValores[p2]['No_Variante_Valor']: arrInicial[p1] + varianteValores[p2]['No_Variante_Valor']);
    if(p2 + 1 < varianteValores.length) {
      p2 += 1;
    } else {
      if(p1 + 1 < arrInicial.length) {
        p1 += 1;
        p2 = 0;
      }
      else {
        continuar = false;
      }
    }
  }
  return arrNuevo;
}

function getFilasProductosVarianteValores() {
  return Array.from(document.querySelectorAll('.table-productos_variante_valores .tr-producto_variante_valores'));
}

function getObjetoProductosVarianteValores() {
  let filaProductosVarianteValores = getFilasProductosVarianteValores();
  if(filaProductosVarianteValores.length > 0) {
    return filaProductosVarianteValores.map(variante => {
      let Nu_Estado_Variante_Valores = 1;
      const opciones = variante.querySelector('[name="Nu_Estado_Variante_Valores"]');
      Nu_Estado_Variante_Valores = opciones.options[opciones.selectedIndex].value;
      return {
        'ID_Producto_Variante_Valores': variante.querySelector('[name="ID_Producto_Variante_Valores"]').value,
        'No_Producto_Variante_Valores': variante.querySelector('[name="No_Producto_Variante_Valores"]').value.trim(),
        'Nu_Codigo_Barra_Variante_Valores': variante.querySelector('[name="Nu_Codigo_Barra_Variante_Valores"]').value.trim(),
        'Ss_Precio_Ecommerce_Online_Regular_Variante_Valores':variante.querySelector('[name="Ss_Precio_Ecommerce_Online_Regular_Variante_Valores"]').value,
        'Ss_Precio_Ecommerce_Online_Variante_Valores':variante.querySelector('[name="Ss_Precio_Ecommerce_Online_Variante_Valores"]').value,
        'Nu_Estado_Variante_Valores': Nu_Estado_Variante_Valores,
        'Nu_Imagen_Producto_Variante_Valores': variante.querySelector('[name="Nu_Imagen_Producto_Variante_Valores"]').value
      };
    });
  } else {
    return [];
  }  
}

function getPlantillaProductosVarianteValores(productosVarianteValores, arrProductoVarianteValores) {
  let plantillaProductoVarianteValores = '';
  let indice = -1;
  for (let i = 0; i < productosVarianteValores.length; i++) {
    indice = arrProductoVarianteValores.findIndex(producto => producto['No_Producto_Variante_Valores'].trim().toLowerCase() == productosVarianteValores[i].trim().toLowerCase());
    if(indice > -1) {
      plantillaProductoVarianteValores += getPlantillaProductoVarianteValores(arrProductoVarianteValores[indice]['No_Producto_Variante_Valores'], arrProductoVarianteValores[indice]['ID_Producto_Variante_Valores'], arrProductoVarianteValores[indice]['Nu_Codigo_Barra_Variante_Valores'], arrProductoVarianteValores[indice]['Ss_Precio_Ecommerce_Online_Regular_Variante_Valores'], arrProductoVarianteValores[indice]['Ss_Precio_Ecommerce_Online_Variante_Valores'], arrProductoVarianteValores[indice]['Nu_Estado_Variante_Valores'], arrProductoVarianteValores[indice]['Nu_Imagen_Producto_Variante_Valores']);
    } else {
      plantillaProductoVarianteValores += getPlantillaProductoVarianteValores(productosVarianteValores[i]);
    }    
  }
  document.querySelector('.table-productos_variante_valores tbody').innerHTML = plantillaProductoVarianteValores;
}

function getPlantillaProductoVarianteValores(No_Producto_Variante_Valores, ID_Producto_Variante_Valores = 0, Nu_Codigo_Barra_Variante_Valores = '', Ss_Precio_Ecommerce_Online_Regular_Variante_Valores = '', Ss_Precio_Ecommerce_Online_Variante_Valores = '', Nu_Estado_Variante_Valores = 1, Nu_Imagen_Producto_Variante_Valores = '') {
  if(Ss_Precio_Ecommerce_Online_Regular_Variante_Valores == 0 && ID_Producto_Variante_Valores == 0) {
    Ss_Precio_Ecommerce_Online_Regular_Variante_Valores = document.querySelector('[name="Ss_Precio_Ecommerce_Online_Regular"]').value;
  }
  Ss_Precio_Ecommerce_Online_Variante_Valores = 0;
  return `<tr class="tr-producto_variante_valores">
  <td class="td-imagen_producto_variante_valores">${manejarImagen(Nu_Imagen_Producto_Variante_Valores)}</td>
  <td><span class="No_Producto_Variante_Valores">${No_Producto_Variante_Valores.trim()}</span><input type="hidden" name="ID_Producto_Variante_Valores" value="${ID_Producto_Variante_Valores}" /><input type="hidden" name="No_Producto_Variante_Valores" value="${No_Producto_Variante_Valores.trim()}" /><input type="hidden" name="Nu_Codigo_Barra_Variante_Valores" value="${Nu_Codigo_Barra_Variante_Valores}" /></td>
  <td><div class="div-precio_oferta_regular_producto_variante_valores"><input type="text" name="Ss_Precio_Ecommerce_Online_Regular_Variante_Valores" onkeypress="return checkNumber(event)" inputmode="decimal" class="form-control input-decimal input-precio_ecommerce_online_regular" maxlength="13" autocomplete="off" value="${Ss_Precio_Ecommerce_Online_Regular_Variante_Valores}" placeholder="Obligatorio"><span class="help-block"></span></div>
  <input type="hidden" name="Ss_Precio_Ecommerce_Online_Variante_Valores" value="${Ss_Precio_Ecommerce_Online_Variante_Valores}" /></td>
  <td><select name="Nu_Estado_Variante_Valores" class="form-control"><option value="1" ${Nu_Estado_Variante_Valores == 1 ? 'selected' : ''}>Visible</option><option value="0" ${Nu_Estado_Variante_Valores == 0 ? 'selected' : ''}>Oculto</option></select></td>
  </tr>`;
}

function manejarImagen(Nu_Imagen_Producto_Variante_Valores) {
  let url = 'https://placehold.co/200x200?text=sin+imagen';
  if(Array.from(document.querySelectorAll(".dz-image")).length > 0) {
    if(Nu_Imagen_Producto_Variante_Valores.length === 0){
      const resultado = buscarImagenPredeterminada();
      Nu_Imagen_Producto_Variante_Valores = resultado.indice;
      url = resultado.url; 
    } else {
      const resultado = buscarImagen(Nu_Imagen_Producto_Variante_Valores);
      Nu_Imagen_Producto_Variante_Valores = resultado.indice;
      url = resultado.url;
    }
  }
  return `<input type="hidden" name="Nu_Imagen_Producto_Variante_Valores" value="${Nu_Imagen_Producto_Variante_Valores}" />
  <a onclick="mostrarModalGaleria(event)" >
    <img style="cursor:pointer;" class="col-xs-12" src="${url}" />
  </a>
  `;
}

function buscarImagenPredeterminada() {
  const imagenesProductoPadre = Array.from(document.querySelectorAll('.dz-image'));
  let url = imagenesProductoPadre[0].querySelector('img').src;
  let indice = 0;
  for (let i = 0; i < imagenesProductoPadre.length; i++) {
    if(imagenesProductoPadre[i].classList.contains('predeterminado')) {
      url = imagenesProductoPadre[i].querySelector('img').src;
      indice = i;
      break;
    }    
  }
  return {url, indice};
}

function buscarImagen(indice) {
  let url =  'https://placehold.co/200x200?text=sin+imagen';
  const imagenesProductoPadre = Array.from(document.querySelectorAll('.dz-image'));
  if(imagenesProductoPadre.length > indice) {
    url = imagenesProductoPadre[indice].querySelector('img').src;
  } else {
    indice = '';
  }
  return {url, indice};
}


function checkNumber(e) {
  const inputPrecioProductoVarianteValores = e.target;
  limpiarMensajeErrorPrecioProductoVarianteValores(inputPrecioProductoVarianteValores,'.div-precio_oferta_regular_producto_variante_valores');
  var ASCIICode = (e.which) ? e.which : e.keyCode
  if (ASCIICode == 46 || (ASCIICode > 47 && ASCIICode < 58)){
    return true;
  }
  return false;
}

function mostrarModalGaleria(e) {
  e.preventDefault();
  Array.from(document.querySelectorAll('.td-imagen_producto_variante_valores')).map(imagenProducto => imagenProducto.classList.remove('galeria_abierta'));
  const tdImagenProductoVarianteValores = e.target.closest('.td-imagen_producto_variante_valores');
  tdImagenProductoVarianteValores.classList.add("galeria_abierta");
  imagesProducto = document.querySelector('.dropzone').cloneNode(true);
  imagesProducto.classList.remove('col-md-8', 'col-lg-8');
  const imagePreviews = Array.from(imagesProducto.querySelectorAll('.dz-preview'));
  for (let i = 0; i < imagePreviews.length; i++) {
    imagePreviews[i].querySelector('.remove').remove();
    imagePreviews[i].querySelector('.filepond--file-action-button').classList.remove('.default')
    imagePreviews[i].querySelector('.filepond--file-action-button').addEventListener('click', function(e) {
      e.preventDefault();
      asingarImagen(i);
    });   
  }
  document.querySelector('#modal-body-galeria_producto_variante_valores').innerHTML = '';
  document.querySelector('#modal-body-galeria_producto_variante_valores').insertAdjacentElement('beforeend',imagesProducto)
  $( '.modal-galeria_producto_variante_valores' ).modal('show');
}

function asingarImagen(indice) {
  document.querySelector('.galeria_abierta [name="Nu_Imagen_Producto_Variante_Valores"]').value = indice;
  document.querySelector('.galeria_abierta img').src = Array.from(document.querySelectorAll('.dz-image'))[indice].querySelector('img').src;
}; 

function limpiarMensajeErrorPrecioProductoVarianteValores(inputPrecioProductoVarianteValores,div) {
  const divPrecioProductoVarianteValores = inputPrecioProductoVarianteValores.closest(div);//closest('.div-precio_producto_variante_valores');
  divPrecioProductoVarianteValores.querySelector('.help-block').innerHTML = '';
  divPrecioProductoVarianteValores.classList.remove('has-error');
}

function mensajeErrorPrecioProductoVarianteValor(inputPrecioProductoVarianteValores, div, mensaje) {
  inputPrecioProductoVarianteValores.value = inputPrecioProductoVarianteValores.value.trim();
  const divPrecioProductoVarianteValor = inputPrecioProductoVarianteValores.closest(div);
  divPrecioProductoVarianteValor.querySelector('.help-block').innerHTML = mensaje;
  divPrecioProductoVarianteValor.classList.add('has-error');
}

function validarPreciosProductosVariantesValores() {
  let correcto = true;
  const productosVarianteValores = getObjetoProductosVarianteValores();
  const filasProductosVarianteValores = getFilasProductosVarianteValores();
  for (let i = 0; i < productosVarianteValores.length; i++) {
    Ss_Precio_Ecommerce_Online_Regular_Variante_Valores = parseFloat(productosVarianteValores[i]['Ss_Precio_Ecommerce_Online_Regular_Variante_Valores']);
    if(isNaN(Ss_Precio_Ecommerce_Online_Regular_Variante_Valores)) {    //PRECIO TIENDA NO VACIO
      mensajeErrorPrecioProductoVarianteValor(filasProductosVarianteValores[i].querySelector('[name="Ss_Precio_Ecommerce_Online_Regular_Variante_Valores"]'), '.div-precio_oferta_regular_producto_variante_valores', 'El Precio Tienda no puede estar vacío');
      correcto = false;      
    } else if(Ss_Precio_Ecommerce_Online_Regular_Variante_Valores == 0) { //PRECIO TIENDA NO CERO
      mensajeErrorPrecioProductoVarianteValor(filasProductosVarianteValores[i].querySelector('[name="Ss_Precio_Ecommerce_Online_Regular_Variante_Valores"]'), '.div-precio_oferta_regular_producto_variante_valores', 'El Precio Tienda debe ser mayor a cero');
      correcto = false;      
    }
  }
  return correcto;
}

function verificarCheckboxVariantes() {
  $('.div-variantes_cuerpo').addClass('hidden');
  if( $('.chk-variantes').prop('checked') ) {
    $('.div-variantes_cuerpo').removeClass('hidden');
  }
}

// ------------------PRODUCTOS VARIANTES VALORES ---------------------- //

function verificarCheckboxProductosRelacionados() {
  $('.div-productos_relacionados_cuerpo').addClass('hidden');
  if( $('.chk-productos_relacionados').prop('checked') ) {
    $('.div-productos_relacionados_cuerpo').removeClass('hidden');
  }
}

function getNuTipoProductosRelacionados(e) {
  $( '.btn-tipo_productos_relacionados' ).removeClass('btn-success');
  const btnTipoProductosRelacionados = e.target;
  $(btnTipoProductosRelacionados).addClass('btn-success');
  const NuTipoProductosRelacionados = $(btnTipoProductosRelacionados).attr('data-nu_tipo_productos_relacionados');
  $('[name="Nu_Tipo_Productos_Relacionados"]').val(NuTipoProductosRelacionados);
  const NuCantidadProductosRelacionados = $(btnTipoProductosRelacionados).attr('data-nu_cantidad_productos_relacionados');
  $('[name="Nu_Cantidad_Productos_Relacionados"]').val(NuCantidadProductosRelacionados);
  mostrarDivTipoProductoRelacionado(NuTipoProductosRelacionados);
}

function mostrarDivTipoProductoRelacionado(NuTipoProductosRelacionados) {
  $('.div-productos_relacionados_aleatorio').addClass('hidden');
  $('.div-productos_relacionados_manual').addClass('hidden');
  switch (parseInt(NuTipoProductosRelacionados)) {
    case 1:
      $('.div-productos_relacionados_aleatorio').removeClass('hidden');
      break;
      
    case 2:
      $('.div-productos_relacionados_manual').removeClass('hidden');
      getProductosRelacionados();
      break;
  }
}

function enterProductoRelacionado(e) {
  const ASCIICode = (e.which) ? e.which : e.keyCode;  
  if (ASCIICode == 13){
    e.preventDefault();
    verificarProductoRelacionado();
  }
  $( '#txt-AID_Producto_Mis_Productos' ).val('');
  $( '#txt-ACodigo_Mis_Productos' ).val('');
}

function clickProductoRelacionado(e) {
  e.preventDefault();
  e.target.disabled = true;
  verificarProductoRelacionado();
  $( '#txt-AID_Producto_Mis_Productos' ).val('');
  $( '#txt-ACodigo_Mis_Productos' ).val('');
  e.target.disabled = false;
}

function verificarProductoRelacionado() {
  const IDProducto = $( '#txt-AID_Producto_Mis_Productos' ).val();
    const NoCodigoBarra = $( '#txt-ACodigo_Mis_Productos' ).val();
    const NoProducto = $( '#txt-ANombre_Mis_Productos' ).val();
    if(IDProducto.length === 0 || NoCodigoBarra.length === 0 || NoProducto.length === 0){
      $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');          
      $( '#modal-message' ).modal('show');
      $( '.modal-message' ).addClass( 'modal-danger' );
      $( '.modal-title-message' ).text( 'Debe seleccionar un producto en la lista de opciones.' );
      setTimeout(function() {$('#modal-message').modal('hide');}, 2000);
      return false;      
    }
    if(buscarProductoRelacionado(IDProducto)){
      $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');          
      $( '#modal-message' ).modal('show');
      $( '.modal-message' ).addClass( 'modal-danger' );
      $( '.modal-title-message' ).text( 'El producto ya se encuentra en la lista' );
      setTimeout(function() {$('#modal-message').modal('hide');}, 2000);
      return false;
    }
    if(IDProducto == $( '[name="EID_Producto"]' ).val()){
      $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');          
      $( '#modal-message' ).modal('show');
      $( '.modal-message' ).addClass( 'modal-danger' );
      $( '.modal-title-message' ).text( 'Debe seleccionar un producto distinto a este.' );
      setTimeout(function() {$('#modal-message').modal('hide');}, 2000);
      return false;
    }
    const producto = {IDProducto, NoCodigoBarra, NoProducto}
    agregarProductoRelacionado(producto);
    $( '#txt-ANombre_Mis_Productos' ).val('');
}


function buscarProductoRelacionado(IDProducto) {
  return $('[name="ID_Producto_Relacionado"]').toArray().map(productoRelacionado => productoRelacionado.value).includes(IDProducto);
}

function agregarProductoRelacionado(productoRelacionado) {  
  if($(".tr-producto_relacionado_manual").toArray().length < $('[name="Nu_Cantidad_Productos_Relacionados"]').val()) {
    $('.table-productos_relacionados_manual tbody').append(getPlantillaProductoRelacionado(productoRelacionado));
  } else {
    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');          
    $( '#modal-message' ).modal('show');
    $( '.modal-message' ).addClass( 'modal-danger' );
    $( '.modal-title-message' ).text( 'Ha llegado al máximo de productos relacionados manualmente.' );
    setTimeout(function() {$('#modal-message').modal('hide');}, 2000);
  }
}

function getPlantillaProductoRelacionado(productoRelacionado) {
  const {IDProducto, NoCodigoBarra, NoProducto} = productoRelacionado;
  return `<tr class="tr-producto_relacionado_manual">
    <td>${NoCodigoBarra}</td>
    <td>${NoProducto}</td>
    <td class="text-center">
      <button class="btn btn btn-default" onclick="eliminarProductoRelacionado(event)" title="Eliminar Producto Relacionado"><i class="fa fa-trash"></i></button>
      <input type="hidden" name="ID_Producto_Relacionado" value="${IDProducto}" />
    </td>
  </tr>`;
}

function eliminarProductoRelacionado(e) {
  e.preventDefault();
  e.target.closest('tr').remove();
}

function getArregloProductosRelacionados() {
  let productosRelacionados = [];
  if( $('.chk-productos_relacionados').prop('checked') ) {
    productosRelacionados = $('[name="ID_Producto_Relacionado"]').toArray().map(productoRelacionado => productoRelacionado.value);
  }
  return productosRelacionados;
}

function getProductosRelacionados() {
  const IDProductoPrincipal = $('[name="EID_Producto"]').val();
  if( IDProductoPrincipal > 0) {
    $('.table-productos_relacionados_manual tbody').html('');
    $( '#modal-loader' ).modal('show');
    url = base_url + 'Logistica/ReglasLogistica/ProductoImportacion/getProductosRelacionados/' + IDProductoPrincipal;
    $.ajax({
      url : url,
      type: "GET",
      dataType: "JSON",
      success: function(response){
        if(response['status'] == 'success') {
          if(response['result'].length > 0) {
            const productosRelacionados = response['result'];
            for (let i = 0; i < productosRelacionados.length; i++) {
              const producto = {
                IDProducto: productosRelacionados[i]['ID_Producto'], 
                NoCodigoBarra: productosRelacionados[i]['No_Codigo_Barra'], 
                NoProducto:  productosRelacionados[i]['No_Producto']
              }
              agregarProductoRelacionado(producto);
            }
          }
        }
        $( '#modal-loader' ).modal('hide');
      },
      error:function(jqXHR, textStatus, errorThrown) {
      }
    });
  }
}