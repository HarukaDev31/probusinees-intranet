var url;
var table_producto;
var accion_producto = '';

function importarExcelProductos(){
  $( ".modal_importar_producto" ).modal( "show" );
}

$(function () {
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
    } else if ( $Qt_Producto_x_Mayor <= 0) {
      $( '#txt-Qt_Producto_x_Mayor' ).closest('.form-group').find('.help-block').html('Cantidad debe ser mayor a 0 unidades');
      $( '#txt-Qt_Producto_x_Mayor' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $Qt_Producto_x_Mayor == 1) {
      $( '#txt-Qt_Producto_x_Mayor' ).closest('.form-group').find('.help-block').html('Cantidad no puede ser 1');
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
  
  url = base_url + 'Logistica/ReglasLogistica/ProductoController/ajax_list';
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
    }
  ],
    'searching'   : false,
    //'bStateSave'  : true,
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
    'columnDefs': [
      {
        'targets': 'no-hidden',
        "visible": false, 
      },
      {
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
      Ss_Precio_Ecommerce_Online: {
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
				required: "ingresar UPC",
			},
			No_Producto:{
				required: "Ingresar nombre",
      },
      Ss_Precio: {
        required: "Ingresar precio",
      },
			ID_Impuesto:{
				required: "Seleccionar impuesto",
			},
			ID_Unidad_Medida:{
				required: "Seleccionar unidad medida",
			},
			ID_Familia:{
				required: "Seleccionar categoría",
			},
			Ss_Precio_Ecommerce_Online:{
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
		//$('#modal-body-ver_item').html('');
    $('.img-responsive').attr('src', '');

    $('.modal-ver_item').modal('show');
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
    url = base_url + 'HelperController/getDataGeneral';
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
  });
  
	$( '#cbo-categoria_marketplace' ).change(function(){
    url = base_url + 'HelperController/getSubCategoriasMarketplace';
    var arrParams = {'iIdFamilia' : $(this).val()}
    $.post( url, arrParams, function( response ){
      if ( response.sStatus == 'success' ) {
        var l = response.arrData.length;
        if (l==1) {
          $( '#cbo-sub_categoria_marketplace' ).html( '<option value="' + response.arrData[0].ID_Sub_Familia + '">' + response.arrData[0].No_Sub_Familia + '</option>' );
        } else {
          $( '#cbo-sub_categoria_marketplace' ).html('<option value="" selected="selected">- Seleccionar -</option>');
          for (var x = 0; x < l; x++) {
            $( '#cbo-sub_categoria_marketplace' ).append( '<option value="' + response.arrData[x].ID_Sub_Familia + '">' + response.arrData[x].No_Sub_Familia + '</option>' );
          }
        }
      } else {
        if( response.sMessageSQL !== undefined ) {
          console.log(response.sMessageSQL);
        }
        console.log(response.sMessage);
      }
    }, 'JSON');
	});

  $('#btn-mostrar_campos_adicionales').click(function () {
    if ($(this).data('mostrar_campos_adicionales') == 1) {
      //setter
      $('#btn-mostrar_campos_adicionales').data('mostrar_campos_adicionales', 0);
    } else {
      $('#btn-mostrar_campos_adicionales').data('mostrar_campos_adicionales', 1);
    }

    if ($(this).data('mostrar_campos_adicionales') == 1) {
      $('.div-campos_adicionales').css("display", "");
      $('#btn-mostrar_campos_adicionales').text('Ocultar campos adicionales');
    } else {
      $('#btn-mostrar_campos_adicionales').text('Mostrar campos adicionales');
      $('.div-campos_adicionales').css("display", "none");
    }
  })
})

function isExistTableTemporalEnlacesProducto($ID_Producto_Enlace){
  return Array.from($('tr[id*=tr_enlace_producto]'))
    .some(element => ($('td:nth(0)',$(element)).html()===$ID_Producto_Enlace));
}

function agregarProducto(){
  accion_producto = 'add_producto';
  
  $( '#form-Producto' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '#modal-loader' ).modal('show');
  
  $( '.div-Listar' ).hide();
  $( '.div-AgregarEditar' ).show();
  
  $( '.div-Compuesto' ).hide();
	$( '#table-Producto_Enlace tbody' ).empty();
		
  $( '.title_Producto' ).text('Nuevo Producto');

  $('#checkbox-precios_x_mayor').prop('checked', false).iCheck('update');
  $('.div-precios_x_mayor').hide();
  $('#table-precios_x_mayor').hide();
  $( '#table-precios_x_mayor tbody' ).empty();

  $('#btn-mostrar_campos_adicionales').data('mostrar_campos_adicionales', 0);
  $('#btn-mostrar_campos_adicionales').text('Mostrar campos adicionales');
  $('.div-campos_adicionales').css("display", "none");

  $('[name="EID_Empresa"]').val('');
  $('[name="EID_Producto"]').val('');
  $('[name="ENu_Codigo_Barra"]').val('');
  $('[name="ENo_Codigo_Interno"]').val('');
  
	$( '.div-Producto' ).show();
	
  $('.div-campos_adicionales').hide();

  $( '#cbo-TiposItem' ).prop('disabled', false);
  
  url = base_url + 'HelperController/getTiposProducto';
  $.post( url , function( responseTiposProducto ){
    $( '#cbo-TiposItem' ).html( '<option value="">- Seleccionar -</option>' );    
    for (var i = 0; i < responseTiposProducto.length; i++)
      $( '#cbo-TiposItem' ).append( '<option value="' + responseTiposProducto[i].Nu_Valor + '">' + responseTiposProducto[i].No_Descripcion + '</option>' );
  }, 'JSON');
  
  $('#cbo-TiposExistenciaProducto').html('');
  url = base_url + 'HelperController/getTiposExistenciaProducto';
  $.post( url , function( responseTiposExistenciaProducto ){
    //$( '#cbo-TiposExistenciaProducto' ).html( '<option value="0">- Seleccionar -</option>' );
    $('#cbo-TiposExistenciaProducto').html('');
    for (var i = 0; i < responseTiposExistenciaProducto.length; i++)
      $( '#cbo-TiposExistenciaProducto' ).append( '<option value="' + responseTiposExistenciaProducto[i].ID_Tipo_Producto + '">' + responseTiposExistenciaProducto[i].No_Tipo_Producto + '</option>' );
  }, 'JSON');
  
  url = base_url + 'HelperController/getUbicacionesInventario';
  $.post( url , function( responseUbicacionesInventario ){
    $( '#cbo-UbicacionesInventario' ).html( '' );
    for (var i = 0; i < responseUbicacionesInventario.length; i++)
      $( '#cbo-UbicacionesInventario' ).append( '<option value="' + responseUbicacionesInventario[i].ID_Ubicacion_Inventario + '">' + responseUbicacionesInventario[i].No_Ubicacion_Inventario + '</option>' );
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
  
  $( '#cbo-lote_vencimiento' ).html( '<option value="0">No</option>' );
  $( '#cbo-lote_vencimiento' ).append( '<option value="1">Si</option>' );

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
  }, 'JSON');

  $('#cbo-Marcas').html('<option value="">- Sin registros -</option>');
  url = base_url + 'HelperController/getMarcas';
  $.post(url, function (responseMarcas) {
    $('#cbo-Marcas').html('<option value="">- Seleccionar -</option>');
    if (responseMarcas.length == 1) {
      $('#cbo-Marcas').append('<option value="' + responseMarcas[0].ID_Marca + '">' + responseMarcas[0].No_Marca + '</option>');
    } else {
      for (var i = 0; i < responseMarcas.length; i++)
        $( '#cbo-Marcas' ).append( '<option value="' + responseMarcas[i].ID_Marca + '">' + responseMarcas[i].No_Marca + '</option>' );
    }
  }, 'JSON');

  $('#cbo-sub_categoria').html('<option value="" selected="selected">- Sin registro -</option>');
  url = base_url + 'HelperController/getDataGeneral';
  $.post( url, {sTipoData : 'categoria'}, function( response ){
    if ( response.sStatus == 'success' ) {
      var l = response.arrData.length;
      if ( l == 1 ) {
        $( '#cbo-categoria' ).html( '<option value="' + response.arrData[0].ID + '">' + response.arrData[0].Nombre + '</option>' );

        $('#cbo-sub_categoria').html('<option value="" selected="selected">- Sin registro -</option>');
        url = base_url + 'HelperController/getDataGeneral';
        var arrParams = {
          sTipoData : 'subcategoria',
          sWhereIdCategoria : response.arrData[0].ID,
        }
        $.post(url, arrParams, function (responseSubCategoria) {
          $('#cbo-sub_categoria').html('<option value="" selected="selected">- Seleccionar -</option>');
          if ( responseSubCategoria.sStatus == 'success' ) {
            var l = responseSubCategoria.arrData.length;
            if (l==1) {
              $('#cbo-sub_categoria').append( '<option value="' + responseSubCategoria.arrData[0].ID + '">' + responseSubCategoria.arrData[0].Nombre + '</option>' );
            } else {
              for (var x = 0; x < l; x++) {
                $( '#cbo-sub_categoria' ).append( '<option value="' + responseSubCategoria.arrData[x].ID + '">' + responseSubCategoria.arrData[x].Nombre + '</option>' );
              }
            }
          } else {
            $( '#cbo-sub_categoria' ).html('<option value="" selected="selected">- vacío -</option>');
          }
        }, 'JSON');
      } else {
        $( '#cbo-categoria' ).html('<option value="" selected="selected">- Seleccionar -</option>');
        for (var x = 0; x < l; x++) {
          $( '#cbo-categoria' ).append( '<option value="' + response.arrData[x].ID + '">' + response.arrData[x].Nombre + '</option>' );
        }
      }
    } else {
      if( response.sMessageSQL !== undefined ) {
        console.log(response.sMessageSQL);
      }
  	  $( '#modal-message' ).modal('show');
      $( '.modal-message' ).addClass(response.sClassModal);
      $( '.modal-title-message' ).text(response.sMessage);
      setTimeout(function() {$('#modal-message').modal('hide');}, 1200);
    }
    $( '#modal-loader' ).modal('hide');
  }, 'JSON');

  
  /* Ecommerce Marketplace */
  url = base_url + 'HelperController/getCategoriasMarketplace';
  $.post( url, {}, function( response ){
    if ( response.sStatus == 'success' ) {
      var l = response.arrData.length;
      $( '#cbo-categoria_marketplace' ).html('<option value="" selected="selected">- Seleccionar -</option>');
      for (var x = 0; x < l; x++) {
        $( '#cbo-categoria_marketplace' ).append( '<option value="' + response.arrData[x].ID_Familia + '">' + response.arrData[x].No_Familia + '</option>' );
      }
    } else {
      if( response.sMessageSQL !== undefined ) {
        console.log(response.sMessageSQL);
      }
      console.log(response.sMessage);
    }
  }, 'JSON');

  $( '#cbo-sub_categoria_marketplace' ).html('<option value="" selected="selected">- Vacío -</option>');
  
  url = base_url + 'HelperController/getMarcasMarketplace';
  $.post( url, {}, function( response ){
    if ( response.sStatus == 'success' ) {
      var l = response.arrData.length;
      $( '#cbo-marca_marketplace' ).html('<option value="" selected="selected">- Seleccionar -</option>');
      for (var x = 0; x < l; x++) {
        $( '#cbo-marca_marketplace' ).append( '<option value="' + response.arrData[x].ID_Marca + '">' + response.arrData[x].No_Marca + '</option>' );
      }
    } else {
      if( response.sMessageSQL !== undefined ) {
        console.log(response.sMessageSQL);
      }
      console.log(response.sMessage);
      $( '#cbo-marca_marketplace' ).html('<option value="" selected="selected">- Vacío -</option>');
    }
  }, 'JSON');
  /* End Ecommerce Marketplace */

  $( '#cbo-receta_medica' ).html( '<option value="0">No</option>' );
  $( '#cbo-receta_medica' ).append( '<option value="1">Si</option>' );
  
  if (iIdTipoRubroEmpresaGlobal == '1') {
    url = base_url + 'HelperController/getDataGeneral';
    $.post( url, {sTipoData : 'laboratorio'}, function( response ){
      if ( response.sStatus == 'success' ) {
        var l = response.arrData.length;
        $( '#cbo-laboratorio' ).html('<option value="" selected="selected">- Seleccionar -</option>');
        for (var x = 0; x < l; x++) {
          $( '#cbo-laboratorio' ).append( '<option value="' + response.arrData[x].ID + '">' + response.arrData[x].Nombre + '</option>' );
        }
      } else {
        if( response.sMessageSQL !== undefined ) {
          console.log(response.sMessageSQL);
        }
        $( '#modal-message' ).modal('show');
        $( '.modal-message' ).addClass(response.sClassModal);
        $( '.modal-title-message' ).text(response.sMessage);
        setTimeout(function() {$('#modal-message').modal('hide');}, 1200);
      }
      $( '#modal-loader' ).modal('hide');
    }, 'JSON');

    $("#cbo-composicion").select2({
      placeholder: "- Seleccionar -",
      allowClear: true
    });

    url = base_url + 'HelperController/getDataGeneral';
    $.post( url, {sTipoData : 'composicion'}, function( response ){
      if ( response.sStatus == 'success' ) {
        var l = response.arrData.length;
        $( '#cbo-composicion' ).html('');
        for (var x = 0; x < l; x++) {
          $( '#cbo-composicion' ).append( '<option value="' + response.arrData[x].ID + '">' + response.arrData[x].Nombre + '</option>' );
        }
      } else {
        if( response.sMessageSQL !== undefined ) {
          console.log(response.sMessageSQL);
        }
        $( '#modal-message' ).modal('show');
        $( '.modal-message' ).addClass(response.sClassModal);
        $( '.modal-title-message' ).text(response.sMessage);
        setTimeout(function() {$('#modal-message').modal('hide');}, 1200);
      }
      $( '#modal-loader' ).modal('hide');
    }, 'JSON');
  }

  url = base_url + 'HelperController/getValoresTablaDato';
  $.post( url, {sTipoData : 'Tipos_PedidoLavado'}, function( response ){
    if ( response.sStatus == 'success' ) {
      var l = response.arrData.length;
      $( '#cbo-tipo_pedido_lavado' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
      for (var x = 0; x < l; x++) {
        $( '#cbo-tipo_pedido_lavado' ).append( '<option value="' + response.arrData[x].Nu_Valor + '">' + response.arrData[x].No_Descripcion + '</option>' );
      }
    } else {
      if( response.sMessageSQL !== undefined ) {
        console.log(response.sMessageSQL);
      }
      if ( response.sStatus == 'warning')
        $( '#cbo-tipo_pedido_lavado' ).html('<option value="0" selected="selected">- Vacío -</option>');
    }
  }, 'JSON');
  
  $( '#cbo-impuesto_icbper' ).html( '<option value="0">No</option>' );
  $('#cbo-impuesto_icbper').append('<option value="1">Si</option>');

  $('#cbo-favorito').html('<option value="0">No</option>');
  $('#cbo-favorito').append('<option value="1">Si</option>');
  
  $( '#cbo-Compuesto' ).html( '<option value="0">No</option>' );
  $( '#cbo-Compuesto' ).append( '<option value="1">Si</option>' );

  $( '#cbo-Estado' ).html( '<option value="1">Activo</option>' );
  $( '#cbo-Estado' ).append( '<option value="0">Inactivo</option>' );

  /* obtener imagen guardada(s) */
  $( '.divDropzone' ).html(
  '<div id="id-divDropzone" class="dropzone div-dropzone">'
    +'<div class="dz-message">'
      +'Arrastrar o presionar click para subir imágen'
    +'</div>'
  +'</div>'
  );

  Dropzone.autoDiscover = false;
  Dropzone.prototype.defaultOptions.dictDefaultMessage = "Arrastrar o presionar click para subir imágen";
  Dropzone.prototype.defaultOptions.dictFallbackMessage = "Tu navegador no soporta la función arrastrar la imágen";
  Dropzone.prototype.defaultOptions.dictFileTooBig = "La imágen pesa ({{filesize}}MiB). El tamaño máximo es: {{maxFilesize}}MiB.";
  Dropzone.prototype.defaultOptions.dictInvalidFileType = "Solo se permite imágenes PNG / JPG / JPEG";
  Dropzone.prototype.defaultOptions.dictCancelUpload = "Cancelar";
  Dropzone.prototype.defaultOptions.dictCancelUploadConfirmation = "¿Estás seguro de cancelar la subida?";
  Dropzone.prototype.defaultOptions.dictRemoveFile = "Eliminar";
  Dropzone.prototype.defaultOptions.dictMaxFilesExceeded = "Solo se puede subir 1 imágen";
  
  url = base_url + 'Logistica/ReglasLogistica/ProductoController/uploadMultiple';
  var myDropzone = new Dropzone("#id-divDropzone", {
    url: url,
    params: {
      iVersionImage: 1,
    },
    acceptedFiles: ".jpeg,.jpg,.png,.webp",
    addRemoveLinks: true,
    uploadMultiple: false,
    maxFilesize: 1,//Peso en MB
    maxFiles: 1,
    thumbnailHeight: 200,
    thumbnailWidth: 200,
    parallelUploads: 1,
    thumbnail: function(file, dataUrl) {
      if (file.previewElement) {
        file.previewElement.classList.remove("dz-file-preview");
        var images = file.previewElement.querySelectorAll("[data-dz-thumbnail]");
        for (var i = 0; i < images.length; i++) {
          var thumbnailElement = images[i];
          thumbnailElement.alt = file.name;
          thumbnailElement.src = dataUrl;
        }
        setTimeout(function() { file.previewElement.classList.add("dz-image-preview"); }, 1);
      }
    },
    removedfile: function(file){
      var nameFileImage = file.name;
      url = base_url + 'Logistica/ReglasLogistica/ProductoController/removeFileImage';
      $.ajax({
        url : url,
        type: "POST",
        dataType: "JSON",
        data: { IdProducto: 1, nameFileImage : nameFileImage},
        success: function (response) {
          $('.modal-message').removeClass('modal-danger modal-warning modal-success');
          $('#modal-message').modal('show');

          if (response.status == 'success') {
            $('.modal-message').addClass(response.style_modal);
            $('.modal-title-message').text(response.message);
            setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
            $('#hidden-nombre_imagen').val('');
            //reload_table_producto();
          } else {
            $('.modal-message').addClass(response.style_modal);
            $('.modal-title-message').text(response.message);
            setTimeout(function () { $('#modal-message').modal('hide'); }, 1200);
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
      })
      var previewElement;
      return (previewElement = file.previewElement) != null ? (previewElement.parentNode.removeChild(file.previewElement)) : (void 0);
    },
    init : function() {
      //Verificar respuesta del servidor al subir archivo
      this.on("success", function(file, response) {
        var response = jQuery.parseJSON(response);
        
        $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
        $( '#modal-message' ).modal('show');
        
        if (response.sStatus != 'error'){
          $( '.modal-message' ).addClass(response.sClassModal);
          $( '.modal-title-message' ).text(response.sMessage);

          $( '#hidden-nombre_imagen' ).val( response.sNombreImagenItem );
          $( '#hidden-tamano_imagen' ).val( response.sTamanoImagenItem );

          setTimeout(function() {$('#modal-message').modal('hide'); }, 1100);
        } else {
          $( '.modal-message' ).addClass(response.sClassModal);
          $( '.modal-title-message' ).text(response.sMessage);
          setTimeout(function() {$('#modal-message').modal('hide');}, 2100);
        }
      })
    },
  })

  //Variante 1
  $('#cbo-variante_1').html('<option value="" selected="selected">- Sin registros -</option>');
  $('#cbo-valor_1').html('<option value="" selected="selected">- Sin registros -</option>');
  url = base_url + 'HelperController/getVariantexIDTablaDato';
  $.post(url, { ID_Tabla_Dato: 2084 }, function (response) {
    if (response.sStatus == 'success') {
      var l = response.arrData.length;
      if (l == 1) {
        $('#cbo-variante_1').html('<option value="" selected="selected">- Seleccionar -</option>');
        $('#cbo-variante_1').append('<option value="' + response.arrData[0].ID + '">' + response.arrData[0].Nombre + '</option>');

        $('#cbo-valor_1').html('<option value="" selected="selected">- Sin registros -</option>');
        url = base_url + 'HelperController/getVarianteDetalle';
        var arrParams = {
          ID_Variante_Item: response.arrData[0].ID,
        }
        $.post(url, arrParams, function (responseDetalle) {
          $('#cbo-valor_1').html('<option value="" selected="selected">- Seleccionar -</option>');
          if (responseDetalle.sStatus == 'success') {
            var l = responseDetalle.arrData.length;
            for (var x = 0; x < l; x++)
              $('#cbo-valor_1').append('<option value="' + responseDetalle.arrData[x].ID + '">' + responseDetalle.arrData[x].Nombre + '</option>');
          } else {
            $('#cbo-valor_1').html('<option value="" selected="selected">- vacío -</option>');
          }
        }, 'JSON');
      }
    } else {
      if (response.sMessageSQL !== undefined) {
        console.log(response.sMessageSQL);
      }
    }
  }, 'JSON');

  //Variante 2
  $('#cbo-variante_2').html('<option value="" selected="selected">- Sin registros -</option>');
  $('#cbo-valor_2').html('<option value="" selected="selected">- Sin registros -</option>');
  url = base_url + 'HelperController/getVariantexIDTablaDato';
  $.post(url, { ID_Tabla_Dato: 2085 }, function (response) {
    if (response.sStatus == 'success') {
      var l = response.arrData.length;
      if (l == 1) {
        $('#cbo-variante_2').html('<option value="" selected="selected">- Seleccionar -</option>');
        $('#cbo-variante_2').append('<option value="' + response.arrData[0].ID + '">' + response.arrData[0].Nombre + '</option>');

        $('#cbo-valor_2').html('<option value="" selected="selected">- Sin registros -</option>');
        url = base_url + 'HelperController/getVarianteDetalle';
        var arrParams = {
          ID_Variante_Item: response.arrData[0].ID,
        }
        $.post(url, arrParams, function (responseDetalle) {
          $('#cbo-valor_2').html('<option value="" selected="selected">- Seleccionar -</option>');
          if (responseDetalle.sStatus == 'success') {
            var l = responseDetalle.arrData.length;
            for (var x = 0; x < l; x++)
              $('#cbo-valor_2').append('<option value="' + responseDetalle.arrData[x].ID + '">' + responseDetalle.arrData[x].Nombre + '</option>');
          } else {
            $('#cbo-valor_2').html('<option value="" selected="selected">- vacío -</option>');
          }
        }, 'JSON');
      }
    } else {
      if (response.sMessageSQL !== undefined) {
        console.log(response.sMessageSQL);
      }
    }
  }, 'JSON');

  //Variante 3
  $('#cbo-variante_3').html('<option value="" selected="selected">- Sin registros -</option>');
  $('#cbo-valor_3').html('<option value="" selected="selected">- Sin registros -</option>');
  url = base_url + 'HelperController/getVariantexIDTablaDato';
  $.post(url, { ID_Tabla_Dato: 2086 }, function (response) {
    if (response.sStatus == 'success') {
      var l = response.arrData.length;
      if (l == 1) {
        $('#cbo-variante_3').html('<option value="" selected="selected">- Seleccionar -</option>');
        $('#cbo-variante_3').append('<option value="' + response.arrData[0].ID + '">' + response.arrData[0].Nombre + '</option>');

        $('#cbo-valor_3').html('<option value="" selected="selected">- Sin registros -</option>');
        url = base_url + 'HelperController/getVarianteDetalle';
        var arrParams = {
          ID_Variante_Item: response.arrData[0].ID,
        }
        $.post(url, arrParams, function (responseDetalle) {
          $('#cbo-valor_3').html('<option value="" selected="selected">- Seleccionar -</option>');
          if (responseDetalle.sStatus == 'success') {
            var l = responseDetalle.arrData.length;
            for (var x = 0; x < l; x++)
              $('#cbo-valor_3').append('<option value="' + responseDetalle.arrData[x].ID + '">' + responseDetalle.arrData[x].Nombre + '</option>');
          } else {
            $('#cbo-valor_3').html('<option value="" selected="selected">- vacío -</option>');
          }
        }, 'JSON');
      }
    } else {
      if (response.sMessageSQL !== undefined) {
        console.log(response.sMessageSQL);
      }
    }
  }, 'JSON');

}// agregarProducto

function verProducto(ID, No_Imagen_Item, Nu_Version_Imagen){
  accion_producto = 'upd_producto';
  
  $( '#modal-loader' ).modal('show');
  
  $( '.div-Listar' ).hide();
  
  $( '.div-Compuesto' ).hide();
	$( '#table-Producto_Enlace tbody' ).empty();

  $( '#form-Producto' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
 
	$( '.div-Producto' ).show();
  
  $('#btn-mostrar_campos_adicionales').data('mostrar_campos_adicionales', 0);
  $('#btn-mostrar_campos_adicionales').text('Mostrar campos adicionales');
  $('.div-campos_adicionales').css("display", "none");

  $( '#cbo-TiposItem' ).prop('disabled', true);
  
  url = base_url + 'Logistica/ReglasLogistica/ProductoController/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $( '.div-AgregarEditar' ).show();
      
      $( '.title_Producto' ).text('Modificar Producto');
      
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
      
      url = base_url + 'HelperController/getTiposExistenciaProducto';
      $.post( url , function( responseTiposExistenciaProducto ){
        $( '#cbo-TiposExistenciaProducto' ).html( '<option value="">- Seleccionar -</option>' );
        for (var i = 0; i < responseTiposExistenciaProducto.length; i++){
          selected = '';
          if(response.ID_Tipo_Producto == responseTiposExistenciaProducto[i].ID_Tipo_Producto)
            selected = 'selected="selected"';
          $( '#cbo-TiposExistenciaProducto' ).append( '<option value="' + responseTiposExistenciaProducto[i].ID_Tipo_Producto + '" ' + selected + '>' + responseTiposExistenciaProducto[i].No_Tipo_Producto + '</option>' );
        }
      }, 'JSON');
      
      url = base_url + 'HelperController/getUbicacionesInventario';
      $.post( url , function( responseUbicacionesInventario ){
        $( '#cbo-UbicacionesInventario' ).html( '<option value="">- Seleccionar -</option>' );
        for (var i = 0; i < responseUbicacionesInventario.length; i++){
          selected = '';
          if(response.ID_Ubicacion_Inventario == responseUbicacionesInventario[i].ID_Ubicacion_Inventario)
            selected = 'selected="selected"';
          $( '#cbo-UbicacionesInventario' ).append( '<option value="' + responseUbicacionesInventario[i].ID_Ubicacion_Inventario + '" ' + selected + '>' + responseUbicacionesInventario[i].No_Ubicacion_Inventario + '</option>' );
        }
      }, 'JSON');
      
      $('[name="No_Codigo_Interno"]').val(response.No_Codigo_Interno);
      $('[name="Nu_Codigo_Barra"]').val(response.Nu_Codigo_Barra);
      
      $('[name="ID_Tabla_Dato"]').val(response.ID_Producto_Sunat);
      $('[name="No_Descripcion"]').val(response.No_Producto_Sunat);
      $('#txt-Txt_Ubicacion_Producto_Tienda').val(response.Txt_Ubicacion_Producto_Tienda);
      $('[name="No_Producto"]').val( clearHTMLTextArea(response.No_Producto) );

      $('[name="Ss_Precio"]').val( Math.round10(response.Ss_Precio, -3) );
      $('[name="Ss_Costo"]').val( Math.round10(response.Ss_Costo, -3) );

      $('#cbo-Marcas').html('<option value="">- Sin registro -</option>');
      url = base_url + 'HelperController/getMarcas';
      $.post( url , function( responseMarcas ){
        $( '#cbo-Marcas' ).html( '<option value="">- Seleccionar -</option>' );
        for (var i = 0; i < responseMarcas.length; i++){
          selected = '';
          if(response.ID_Marca == responseMarcas[i].ID_Marca)
            selected = 'selected="selected"';
          $( '#cbo-Marcas' ).append( '<option value="' + responseMarcas[i].ID_Marca + '" ' + selected + '>' + responseMarcas[i].No_Marca + '</option>' );
        }
      }, 'JSON');
      
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
      
      $( '#cbo-lote_vencimiento' ).html('');
      for (var i = 0; i < 2; i++){
        selected = '';
        if(response.Nu_Lote_Vencimiento == i)
          selected = 'selected="selected"';
        $( '#cbo-lote_vencimiento' ).append( '<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'No' : 'Si') + '</option>' );
      }

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
      
      url = base_url + 'HelperController/getDataGeneral';
      $.post( url, {sTipoData : 'categoria'}, function( responseCategoria ){
        $( '#cbo-categoria' ).html( '<option value="">- Seleccionar -</option>' );
        if ( responseCategoria.sStatus == 'success' ) {
          var l = responseCategoria.arrData.length;
          for (var x = 0; x < l; x++) {
            selected = '';
            if(response.ID_Familia == responseCategoria.arrData[x].ID)
              selected = 'selected="selected"';
            $( '#cbo-categoria' ).append( '<option value="' + responseCategoria.arrData[x].ID + '" ' + selected + '>' + responseCategoria.arrData[x].Nombre + '</option>' );
          }
        } else {
          if( responseCategoria.sMessageSQL !== undefined ) {
            console.log(responseCategoria.sMessageSQL);
          }
          if ( responseCategoria.sStatus == 'warning')
            $( '#cbo-categoria' ).html('<option value="0" selected="selected">- Vacío -</option>');
        }
      }, 'JSON');

      $('#cbo-Marcas').html('<option value="">- Sin registro -</option>');
      url = base_url + 'HelperController/getDataGeneral';
      $.post( url, {sTipoData : 'subcategoria', sWhereIdCategoria : response.ID_Familia}, function( responseSubCategoria ){
        if ( responseSubCategoria.sStatus == 'success' ) {
          $( '#cbo-sub_categoria' ).html( '<option value="">- Seleccionar -</option>' );
          var l = responseSubCategoria.arrData.length;
          for (var x = 0; x < l; x++) {
            selected = '';
            if(response.ID_Sub_Familia == responseSubCategoria.arrData[x].ID)
              selected = 'selected="selected"';
            $( '#cbo-sub_categoria' ).append( '<option value="' + responseSubCategoria.arrData[x].ID + '" ' + selected + '>' + responseSubCategoria.arrData[x].Nombre + '</option>' );
          }
        } else {
          if( responseSubCategoria.sMessageSQL !== undefined ) {
            console.log(responseSubCategoria.sMessageSQL);
          }
          if ( responseSubCategoria.sStatus == 'warning')
            $( '#cbo-sub_categoria' ).html('<option value="0" selected="selected">- Vacío -</option>');
        }
        $( '#modal-loader' ).modal('hide');
      }, 'JSON');
      
      /* Ecommerce Marketplace */
      $( '[name="Ss_Precio_Ecommerce_Online_Regular"]' ).val( response.Ss_Precio_Ecommerce_Online_Regular );
      $( '[name="Ss_Precio_Ecommerce_Online"]' ).val( response.Ss_Precio_Ecommerce_Online );

      url = base_url + 'HelperController/getCategoriasMarketplace';
      $.post( url, {}, function( responseCategoria ){
        if ( responseCategoria.sStatus == 'success' ) {
          var l = responseCategoria.arrData.length;
          $( '#cbo-categoria_marketplace' ).html('<option value="" selected="selected">- Seleccionar -</option>');
          for (var x = 0; x < l; x++) {
            selected = '';
            if(response.ID_Familia_Marketplace == responseCategoria.arrData[x].ID_Familia)
              selected = 'selected="selected"';
            $( '#cbo-categoria_marketplace' ).append( '<option value="' + responseCategoria.arrData[x].ID_Familia + '" ' + selected + '>' + responseCategoria.arrData[x].No_Familia + '</option>' );
          }
        } else {
          if( responseCategoria.sMessageSQL !== undefined ) {
            console.log(responseCategoria.sMessageSQL);
          }
          console.log(responseCategoria.sMessage);
        }
      }, 'JSON');

      url = base_url + 'HelperController/getSubCategoriasMarketplace';
      var arrParams = {'iIdFamilia' : response.ID_Familia_Marketplace}
      $.post( url, arrParams, function( responseSubCategoria ){
        if ( responseSubCategoria.sStatus == 'success' ) {
          var l = responseSubCategoria.arrData.length;
          if (l==1) {
            $( '#cbo-sub_categoria_marketplace' ).html( '<option value="' + responseSubCategoria.arrData[0].ID_Sub_Familia + '">' + responseSubCategoria.arrData[0].No_Sub_Familia + '</option>' );
          } else {
            $( '#cbo-sub_categoria_marketplace' ).html('<option value="" selected="selected">- Seleccionar -</option>');
            for (var x = 0; x < l; x++) {
              selected = '';
              if(response.ID_Sub_Familia_Marketplace == responseSubCategoria.arrData[x].ID_Sub_Familia)
                selected = 'selected="selected"';
              $( '#cbo-sub_categoria_marketplace' ).append( '<option value="' + responseSubCategoria.arrData[x].ID_Sub_Familia + '" ' + selected + '>' + responseSubCategoria.arrData[x].No_Sub_Familia + '</option>' );
            }
          }
        } else {
          if( responseSubCategoria.sMessageSQL !== undefined ) {
            console.log(responseSubCategoria.sMessageSQL);
          }
          console.log(responseSubCategoria.sMessage);
        }
      }, 'JSON');

      url = base_url + 'HelperController/getMarcasMarketplace';
      $.post( url, {}, function( responseMarca ){
        if ( responseMarca.sStatus == 'success' ) {
          var l = responseMarca.arrData.length;
          $( '#cbo-marca_marketplace' ).html('<option value="" selected="selected">- Seleccionar -</option>');
          for (var x = 0; x < l; x++) {
            selected = '';
            if(response.ID_Marca_Marketplace == responseMarca.arrData[x].ID_Marca)
              selected = 'selected="selected"';
            $( '#cbo-marca_marketplace' ).append( '<option value="' + responseMarca.arrData[x].ID_Marca + '" ' + selected + '>' + responseMarca.arrData[x].No_Marca + '</option>' );
          }
        } else {
          if( responseMarca.sMessageSQL !== undefined ) {
            console.log(responseMarca.sMessageSQL);
          }
          console.log(responseMarca.sMessage);
        }
      }, 'JSON');
      /* End Ecommerce Marketplace */

      url = base_url + 'HelperController/getValoresTablaDato';
      $.post( url, {sTipoData : 'Tipos_PedidoLavado'}, function( responseTipoPedidoLavado ){
        $( '#cbo-tipo_pedido_lavado' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
        if ( responseTipoPedidoLavado.sStatus == 'success' ) {
          var l = responseTipoPedidoLavado.arrData.length;
          $( '#cbo-tipo_pedido_lavado' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
          for (var x = 0; x < l; x++) {
            selected = '';
            if(response.ID_Tipo_Pedido_Lavado == responseTipoPedidoLavado.arrData[x].Nu_Valor)
              selected = 'selected="selected"';
            $( '#cbo-tipo_pedido_lavado' ).append( '<option value="' + responseTipoPedidoLavado.arrData[x].Nu_Valor + '" ' + selected + '>' + responseTipoPedidoLavado.arrData[x].No_Descripcion + '</option>' );
          }
        } else {
          if( responseTipoPedidoLavado.sMessageSQL !== undefined ) {
            console.log(responseTipoPedidoLavado.sMessageSQL);
          }
          if ( responseTipoPedidoLavado.sStatus == 'warning')
            $( '#cbo-tipo_pedido_lavado' ).html('<option value="0" selected="selected">- Vacío -</option>');
        }
      }, 'JSON');

      $('#tel-Nu_Stock_Minimo').val(response.Nu_Stock_Minimo);
      $('#tel-Nu_Stock_Maximo').val(response.Nu_Stock_Maximo);
      $('#tel-Qt_CO2_Producto').val(response.Qt_CO2_Producto);

      $( '#cbo-receta_medica' ).html('');
      for (var i = 0; i < 2; i++){
        selected = '';
        if(response.Nu_Receta_Medica == i)
          selected = 'selected="selected"';
        $( '#cbo-receta_medica' ).append( '<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'No' : 'Si') + '</option>' );
      }

      url = base_url + 'HelperController/getDataGeneral';
      $.post( url, {sTipoData : 'laboratorio'}, function( responseLaboratorio ){
        $( '#cbo-laboratorio' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
        if ( responseLaboratorio.sStatus == 'success' ) {
          var l = responseLaboratorio.arrData.length;
          for (var x = 0; x < l; x++) {
            selected = '';
            if(response.ID_Laboratorio == responseLaboratorio.arrData[x].ID)
              selected = 'selected="selected"';
            $( '#cbo-laboratorio' ).append( '<option value="' + responseLaboratorio.arrData[x].ID + '" ' + selected + '>' + responseLaboratorio.arrData[x].Nombre + '</option>' );
          }
        } else {
          if( responseLaboratorio.sMessageSQL !== undefined ) {
            console.log(responseLaboratorio.sMessageSQL);
          }
          if ( responseLaboratorio.sStatus == 'warning')
            $( '#cbo-laboratorio' ).html('<option value="0" selected="selected">- Vacío -</option>');          
        }
      }, 'JSON');
  
      $( '#cbo-composicion' ).html('');
      url = base_url + 'HelperController/getDataGeneral';
      $.post( url, {sTipoData : 'composicion'}, function( responseComposicion ){
        if ( responseComposicion.sStatus == 'success' ) {
          var l = responseComposicion.arrData.length;
          if ( response.Txt_Composicion != '' && response.Txt_Composicion != null ) {
            var arrComposicion=response.Txt_Composicion.split(',');
            var iCountComposicion=arrComposicion.length;     
            for (var x = 0; x < l; x++) {
              selected = '';
              for (var y=0; y < iCountComposicion; y++) {
                if(arrComposicion[y] == responseComposicion.arrData[x].ID)
                  selected = 'selected="selected"';
              }
              $( '#cbo-composicion' ).append( '<option value="' + responseComposicion.arrData[x].ID + '" ' + selected + '>' + responseComposicion.arrData[x].Nombre + '</option>' );
            }
          } else {
            for (var x = 0; x < l; x++) {
              $( '#cbo-composicion' ).append( '<option value="' + responseComposicion.arrData[x].ID + '">' + responseComposicion.arrData[x].Nombre + '</option>' );
            }
          }
        } else {
          console.log(response.sMessageSQL);
          $( '.modal-message' ).addClass(response.sClassModal);
          $( '.modal-title-message' ).text(response.sMessage);
          setTimeout(function() {$('#modal-message').modal('hide');}, 1200);
        }
      }, 'JSON');
      
      $( '#cbo-impuesto_icbper' ).html('');
      for (var i = 0; i < 2; i++){
        selected = '';
        if(response.ID_Impuesto_Icbper == i)
          selected = 'selected="selected"';
        $( '#cbo-impuesto_icbper' ).append( '<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'No' : 'Si') + '</option>' );
      }

      $('#cbo-favorito').html('');
      for (var i = 0; i < 2; i++) {
        selected = '';
        if (response.Nu_Favorito == i)
          selected = 'selected="selected"';
        $('#cbo-favorito').append('<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'No' : 'Si') + '</option>');
      }

      $( '#cbo-Compuesto' ).html('');
      for (var i = 0; i < 2; i++){
        selected = '';
        if(response.Nu_Compuesto == i)
          selected = 'selected="selected"';
        $( '#cbo-Compuesto' ).append( '<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'No' : 'Si') + '</option>' );
      }
      
      /* Enlaces de Productos */
      if (response.Nu_Compuesto == 1) {
        url = base_url + 'Logistica/ReglasLogistica/ProductoController/ajax_edit_enlace/' + ID;
        $.getJSON( url, function( data ){
          if ( data.length > 0 ){
	          $( '.div-Compuesto' ).show();
	          $( '#table-Producto_Enlace' ).show();
            var table_enlace_producto = "";
            for (i = 0; i < data.length; i++) {
              table_enlace_producto += 
              "<tr id='tr_enlace_producto" + data[i]['ID_Producto'] + "'>"
                + "<td class='text-left' style='display:none;'>" + data[i]['ID_Producto'] + "</td>"
                + "<td class='text-left'>" + data[i]['Nu_Codigo_Barra'] + "</td>"
                + "<td class='text-left'>" + data[i]['No_Producto'] + "</td>"
                + "<td class='text-right'>" + number_format(data[i]['Qt_Producto_Descargar'], 3) + "</td>"
                + "<td class='text-center'><button type='button' id='btn-deleteProductoEnlace' class='btn btn-xs btn-link' alt='Eliminar' title='Eliminar'><i class='fa fa-trash-o' aria-hidden='true'> Eliminar</i></button></td>"
              + "</tr>";
            }
			      $( '#table-Producto_Enlace' ).append(table_enlace_producto);
          }
        }, 'JSON');
        
        $('#btn-mostrar_campos_adicionales').data('mostrar_campos_adicionales', 1);
        $('#btn-mostrar_campos_adicionales').text('Ocultar campos adicionales');
        $('.div-campos_adicionales').css("display", "");
      }
	    
      $( '#cbo-Estado' ).html('');
      for (var i = 0; i < 2; i++){
        selected = '';
        if(response.Nu_Estado == i)
          selected = 'selected="selected"';
        $( '#cbo-Estado' ).append( '<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Inactivo' : 'Activo') + '</option>' );
      }

      $( '[name="Txt_Producto"]' ).val( clearHTMLTextArea(response.Txt_Producto) );
      
      //Variante 1
      $('#cbo-variante_1').html('<option value="" selected="selected">- Sin registros -</option>');
      $('#cbo-valor_1').html('<option value="" selected="selected">- Sin registros -</option>');
      url = base_url + 'HelperController/getVariantexIDTablaDato';
      $.post(url, { ID_Tabla_Dato: 2084 }, function (responseVariante) {
        if (responseVariante.sStatus == 'success') {
          var l = responseVariante.arrData.length;
          if (l == 1) {
            $('#cbo-variante_1').html('<option value="" selected="selected">- Seleccionar -</option>');

            selected = '';
            if (response.ID_Variante_Item_1 == responseVariante.arrData[0].ID)
              selected = 'selected="selected"';
            $('#cbo-variante_1').append('<option value="' + responseVariante.arrData[0].ID + '" ' + selected + '>' + responseVariante.arrData[0].Nombre + '</option>');

            $('#cbo-valor_1').html('<option value="" selected="selected">- Sin registros -</option>');
            url = base_url + 'HelperController/getVarianteDetalle';
            var arrParams = {
              ID_Variante_Item: responseVariante.arrData[0].ID,
            }
            $.post(url, arrParams, function (responseDetalle) {
              $('#cbo-valor_1').html('<option value="" selected="selected">- Seleccionar -</option>');
              if (responseDetalle.sStatus == 'success') {
                var l = responseDetalle.arrData.length;
                for (var x = 0; x < l; x++) {
                  selected = '';
                  if (response.ID_Variante_Item_Detalle_1 == responseDetalle.arrData[x].ID)
                    selected = 'selected="selected"';
                  $('#cbo-valor_1').append('<option value="' + responseDetalle.arrData[x].ID + '" ' + selected + '>' + responseDetalle.arrData[x].Nombre + '</option>');
                }
              } else {
                $('#cbo-valor_1').html('<option value="" selected="selected">- vacío -</option>');
              }
            }, 'JSON');
          }
        } else {
          if (responseVariante.sMessageSQL !== undefined) {
            console.log(responseVariante.sMessageSQL);
          }
        }
      }, 'JSON');
      
      //Variante 2
      $('#cbo-variante_2').html('<option value="" selected="selected">- Sin registros -</option>');
      $('#cbo-valor_2').html('<option value="" selected="selected">- Sin registros -</option>');
      url = base_url + 'HelperController/getVariantexIDTablaDato';
      $.post(url, { ID_Tabla_Dato: 2085 }, function (responseVariante) {
        if (responseVariante.sStatus == 'success') {
          var l = responseVariante.arrData.length;
          if (l == 1) {
            $('#cbo-variante_2').html('<option value="" selected="selected">- Seleccionar -</option>');
            selected = '';
            if (response.ID_Variante_Item_2 == responseVariante.arrData[0].ID)
              selected = 'selected="selected"';
            $('#cbo-variante_2').append('<option value="' + responseVariante.arrData[0].ID + '" ' + selected + '>' + responseVariante.arrData[0].Nombre + '</option>');

            $('#cbo-valor_2').html('<option value="" selected="selected">- Sin registros -</option>');
            url = base_url + 'HelperController/getVarianteDetalle';
            var arrParams = {
              ID_Variante_Item: responseVariante.arrData[0].ID,
            }
            $.post(url, arrParams, function (responseDetalle) {
              $('#cbo-valor_2').html('<option value="" selected="selected">- Seleccionar -</option>');
              if (responseDetalle.sStatus == 'success') {
                var l = responseDetalle.arrData.length;
                for (var x = 0; x < l; x++) {
                  selected = '';
                  if (response.ID_Variante_Item_Detalle_2 == responseDetalle.arrData[x].ID)
                    selected = 'selected="selected"';
                  $('#cbo-valor_2').append('<option value="' + responseDetalle.arrData[x].ID + '" ' + selected + '>' + responseDetalle.arrData[x].Nombre + '</option>');
                }
              } else {
                $('#cbo-valor_2').html('<option value="" selected="selected">- vacío -</option>');
              }
            }, 'JSON');
          }
        } else {
          if (responseVariante.sMessageSQL !== undefined) {
            console.log(responseVariante.sMessageSQL);
          }
        }
      }, 'JSON');

      //Variante 3
      $('#cbo-variante_3').html('<option value="" selected="selected">- Sin registros -</option>');
      $('#cbo-valor_3').html('<option value="" selected="selected">- Sin registros -</option>');
      url = base_url + 'HelperController/getVariantexIDTablaDato';
      $.post(url, { ID_Tabla_Dato: 2086 }, function (responseVariante) {
        if (responseVariante.sStatus == 'success') {
          var l = responseVariante.arrData.length;
          if (l == 1) {
            $('#cbo-variante_3').html('<option value="" selected="selected">- Seleccionar -</option>');
            selected = '';
            if (response.ID_Variante_Item_3 == responseVariante.arrData[0].ID)
              selected = 'selected="selected"';
            $('#cbo-variante_3').append('<option value="' + responseVariante.arrData[0].ID + '" ' + selected + '>' + responseVariante.arrData[0].Nombre + '</option>');

            $('#cbo-valor_3').html('<option value="" selected="selected">- Sin registros -</option>');
            url = base_url + 'HelperController/getVarianteDetalle';
            var arrParams = {
              ID_Variante_Item: responseVariante.arrData[0].ID,
            }
            $.post(url, arrParams, function (responseDetalle) {
              $('#cbo-valor_3').html('<option value="" selected="selected">- Seleccionar -</option>');
              if (responseDetalle.sStatus == 'success') {
                var l = responseDetalle.arrData.length;
                for (var x = 0; x < l; x++) {
                  selected = '';
                  if (response.ID_Variante_Item_Detalle_3 == responseDetalle.arrData[x].ID)
                    selected = 'selected="selected"';
                  $('#cbo-valor_3').append('<option value="' + responseDetalle.arrData[x].ID + '" ' + selected + '>' + responseDetalle.arrData[x].Nombre + '</option>');
                }
              } else {
                $('#cbo-valor_3').html('<option value="" selected="selected">- vacío -</option>');
              }
            }, 'JSON');
          }
        } else {
          if (responseVariante.sMessageSQL !== undefined) {
            console.log(responseVariante.sMessageSQL);
          }
        }
      }, 'JSON');

      $('#checkbox-precios_x_mayor').prop('checked', false).iCheck('update');
      $('.div-precios_x_mayor').hide();
      $('#table-precios_x_mayor').hide();
      $( '#table-precios_x_mayor tbody' ).empty();
      /* PRECIOS AL POR MAYOR */
      if (response.Nu_Activar_Precio_x_Mayor == 1) {
        $('#checkbox-precios_x_mayor').prop('checked', true).iCheck('update');
        url = base_url + 'TiendaVirtual/ItemsTiendaVirtualController/ajax_edit_precios_por_mayor/' + ID;
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
  
  /* obtener imagen guardada(s) */
  $( '.divDropzone' ).html(
  '<div id="id-divDropzone" class="dropzone div-dropzone">'
    +'<div class="dz-message">'
      +'Arrastrar o presionar click para subir imágen'
    +'</div>'
  +'</div>'
  );
  
  Dropzone.autoDiscover = false;
  Dropzone.prototype.defaultOptions.dictDefaultMessage = "Arrastrar o presionar click para subir imágen";
  Dropzone.prototype.defaultOptions.dictFallbackMessage = "Tu navegador no soporta la función arrastrar la imágen";
  Dropzone.prototype.defaultOptions.dictFileTooBig = "La imágen pesa ({{filesize}}MiB). El tamaño máximo es: {{maxFilesize}}MiB.";
  Dropzone.prototype.defaultOptions.dictInvalidFileType = "Solo se permite imágenes PNG / JPG / JPEG";
  Dropzone.prototype.defaultOptions.dictCancelUpload = "Cancelar";
  Dropzone.prototype.defaultOptions.dictCancelUploadConfirmation = "¿Estás seguro de cancelar la subida?";
  Dropzone.prototype.defaultOptions.dictRemoveFile = "Eliminar";
  Dropzone.prototype.defaultOptions.dictMaxFilesExceeded = "Solo se puede subir 1 imágen";
  
  url = base_url + 'Logistica/ReglasLogistica/ProductoController/uploadMultiple';
  var myDropzone = new Dropzone("#id-divDropzone", {
    url: url,
    params: {
      iIdProducto: ID,
    },
    acceptedFiles: ".jpeg,.jpg,.png,.webp",
    addRemoveLinks: true,
    uploadMultiple: false,
    maxFilesize: 1,//Peso en MB
    maxFiles: 1,
    thumbnailHeight: 200,
    thumbnailWidth: 200,
    parallelUploads: 1,
    thumbnail: function(file, dataUrl) {
      if (file.previewElement) {
        file.previewElement.classList.remove("dz-file-preview");
        var images = file.previewElement.querySelectorAll("[data-dz-thumbnail]");
        for (var i = 0; i < images.length; i++) {
          var thumbnailElement = images[i];
          thumbnailElement.alt = file.name;
          thumbnailElement.src = dataUrl;
        }
        setTimeout(function() { file.previewElement.classList.add("dz-image-preview"); }, 1);
      }
    },
    removedfile: function(file){
      url = base_url + 'Logistica/ReglasLogistica/ProductoController/removeFileImage';
      $.ajax({
        url : url,
        type: "POST",
        dataType: "JSON",
        data: { iIdProducto: ID, iIdImagen: $('#hidden-id_imagen').val() },
        success: function (response) {
          $('.modal-message').removeClass('modal-danger modal-warning modal-success');
          $('#modal-message').modal('show');

          if (response.status == 'success') {
            $('.modal-message').addClass(response.style_modal);
            $('.modal-title-message').text(response.message);
            setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
            $('#hidden-id_imagen').val('');
            $('#hidden-nombre_imagen').val('');
            //reload_table_producto();
          } else {
            $('.modal-message').addClass(response.style_modal);
            $('.modal-title-message').text(response.message);
            setTimeout(function () { $('#modal-message').modal('hide'); }, 1200);
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
      })
      var previewElement;
      return (previewElement = file.previewElement) != null ? (previewElement.parentNode.removeChild(file.previewElement)) : (void 0);
    },
    init : function() {

      let root = this;
      root.id_imagen = 0;
      url = base_url + 'Logistica/ReglasLogistica/ProductoController/get_image';
      $.ajax({
        url : url,
        type: "POST",
        dataType: "JSON",
        data: { iIdProducto: ID },
        success: function (response) {
          $('#hidden-id_imagen').val(response.ID_Producto_Imagen);
          $('#hidden-nombre_imagen').val(response.No_Producto_Imagen_url);
          let Miniatura = { name:response.No_Producto_Imagen, size: response.Imagen_Tamano };
          root.emit("addedfile", Miniatura);
          root.emit("thumbnail", Miniatura, response.No_Producto_Imagen_url, response.ID_Producto_Imagen);
          root.emit("complete", Miniatura);
          root.emit("success", Miniatura,'{ "id_imagen": '+response.ID_Producto_Imagen+', "name_": "'+response.No_Producto_Imagen+'", "name": "'+response.No_Producto_Imagen+'","id_predeterminado":'+response.ID_Predeterminado+'}' );

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
      
      obj.ID_Producto_Enlace	= $ID_Producto_Enlace;
      obj.Qt_Producto_Descargar	= $Qt_Producto_Enlace;
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
    if ( $( '#cbo-TiposItem' ).val() == '1' && $( '#cbo-TiposExistenciaProducto' ).val() == '0'){
      $( '#cbo-TiposExistenciaProducto' ).closest('.form-group').find('.help-block').html('Seleccionar tipo producto');
  	  $( '#cbo-TiposExistenciaProducto' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $( '#cbo-TiposItem' ).val() == '1' && $( '#cbo-Impuestos' ).val() == '0'){
      $( '#cbo-Impuestos' ).closest('.form-group').find('.help-block').html('Seleccionar impuesto');
  	  $( '#cbo-Impuestos' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $( '#cbo-TiposItem' ).val() == '1' && $( '#cbo-UnidadesMedida' ).val() == '0'){
      $( '#cbo-UnidadesMedida' ).closest('.form-group').find('.help-block').html('Seleccionar unidad medida');
  	  $( '#cbo-UnidadesMedida' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( $( '#cbo-TiposItem' ).val() == '1' && $( '#cbo-Compuesto' ).val() == '1' && arrProductoEnlace.length === 0){
      $( '#cbo-Compuesto' ).closest('.form-group').find('.help-block').html('Seleccionar productos para enlazar');
  	  $( '#cbo-Compuesto' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ($("#checkbox-precios_x_mayor").prop("checked") == true && arrProductoPrecioxMayor.length === 0){
      alert('Activaste la opción precios por mayor debes de registrar las opciones');
    } else {
      if ( $( '#cbo-Compuesto' ).val()=='0')//No
  			arrProductoEnlace = null;
  		var iIDTipoExistenciaProducto = $( '#cbo-TiposExistenciaProducto' ).val();
      if ( $( '#cbo-TiposItem' ).val() == '0' || $( '#cbo-TiposItem' ).val() == '2' )//Servicio o interno
        iIDTipoExistenciaProducto = 4;//Otros
      
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
          'No_Imagen_Item' : $( '#hidden-nombre_imagen' ).val(),
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
          'Nu_Activar_Precio_x_Mayor': $("#checkbox-precios_x_mayor").prop("checked")
        };
        
      
      $( '#btn-save' ).text('');
      $( '#btn-save' ).attr('disabled', true);
      $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
    
      $( '#modal-loader' ).modal('show');
      
      url = base_url + 'Logistica/ReglasLogistica/ProductoController/crudProducto';
    	$.ajax({
        type		  : 'POST',
        dataType	: 'JSON',
    		url		    : url,
    		data		  : {
    		  arrProducto : arrProducto,
    		  arrProductoEnlace : arrProductoEnlace,
          arrProductoPrecioxMayor : arrProductoPrecioxMayor
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
      	    setTimeout(function() {$('#modal-message').modal('hide');}, 1200);
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
  //location.reload();
}

function _eliminarProducto($modal_delete, ID_Empresa, ID, Nu_Codigo_Barra, Nu_Compuesto, sNombreImagenItem){
  $( '#modal-loader' ).modal('show');
  
  if (Nu_Codigo_Barra == '')
    Nu_Codigo_Barra = '-';
  
  url = base_url + 'Logistica/ReglasLogistica/ProductoController/eliminarProducto/' + ID_Empresa + '/' + ID + '/' + Nu_Codigo_Barra + '/' + Nu_Compuesto + '/' + sNombreImagenItem;
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

function estadoxAlmacen(ID, Nu_Estado_Stock) {
  var $modal_delete = $('.modal-message-delete');
  $modal_delete.modal('show');

  $('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
  $('.modal-message-delete').addClass('modal-warning');

  $('.modal-title-message-delete').text('¿Cambiar estado x almacen?');

  $('#btn-cancel-delete').off('click').click(function () {
    $modal_delete.modal('hide');
  });
  
  $( '#btn-save-delete' ).off('click').click(function () {
  url = base_url + 'Logistica/ReglasLogistica/ProductoController/estadoxAlmacen/' + ID + '/' + Nu_Estado_Stock;
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
  });
}

function generarBarcode(ID) {
  var $modal_delete = $('#modal-print_codigo_barra');
  $modal_delete.modal('show');

  $('#btn-cancel-delete').off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $('#btn-modal-print_codigo_barra').off('click').click(function () {
    var iTipoFormatoPrint = $('#cbo-modal-print_codigo_barra-formato').val().trim();
    var iNumeroColuma = $('#txt-modal-print_codigo_barra-columna').val().trim();
    var iPrintSku = $('#txt-modal-print_codigo_barra-imprimir_sku').val().trim();

    if( iTipoFormatoPrint == 0 ){
      alert('Selecciona un formato');
    } else if( iTipoFormatoPrint == 1 ){
      sendHTML($modal_delete, ID, iTipoFormatoPrint, iNumeroColuma, iPrintSku);
    } else if( iTipoFormatoPrint == 2 ){
      sendPDF($modal_delete, ID, iTipoFormatoPrint, iNumeroColuma, iPrintSku);
    }
  });
}

function sendHTML($modal_delete, ID, iTipoFormatoPrint, iNumeroColuma, iPrintSku) {
  $modal_delete.modal('hide');
  $.ajax({
    url: base_url + "Logistica/ReglasLogistica/ProductoController/generarBarcodeHTML/" + ID + "/" + iTipoFormatoPrint + "/" + iNumeroColuma + "/" + iPrintSku,
    type: "GET",
    dataType: "html",
    success: function (data) {
      winPrintSunat = window.open("", "MsgWindow", "top=80,left=800,width=550,height=550");
      winPrintSunat.document.open();
      winPrintSunat.document.write(data);
      winPrintSunat.document.close();
      winPrintSunat.focus();
      winPrintSunat.print();
      winPrintSunat.close();
    },
    error: function (xhr, status) {
      alert("Problemas al imprimir");
    },
    complete: function (xhr, status) {
      $modal_delete.modal('hide');
    }
  });
}

function sendPDF($modal_delete, ID, iTipoFormatoPrint, iNumeroColuma, iPrintSku) {
  $('#modal-loader').modal('show');
  $modal_delete.modal('hide');
  url = base_url + 'Logistica/ReglasLogistica/ProductoController/generarBarcode/' + ID + "/" + iTipoFormatoPrint + "/" + iNumeroColuma + "/" + iPrintSku;
  window.open(url, '_blank');
  $('#modal-loader').modal('hide');
}

function agregarVarianteItem(iIdItem) {
  $( '#modal-loader' ).modal('show');
  url = base_url + 'Logistica/ReglasLogistica/ProductoController/ajax_edit/' + iIdItem;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $( '#modal-loader' ).modal('hide');
      
      //console.log(response);

      //Variante 1
      $('#cbo-variante_1-modal').html('<option value="" selected="selected">- Sin registros -</option>');
      $('#cbo-valor_1-modal').html('<option value="" selected="selected">- Sin registros -</option>');
      url = base_url + 'HelperController/getVariantexIDTablaDato';
      $.post(url, { ID_Tabla_Dato: 2084 }, function (responseVariante1) {
        if (responseVariante1.sStatus == 'success') {
          var l = responseVariante1.arrData.length;
          if (l == 1) {
            $('#cbo-variante_1-modal').html('<option value="" selected="selected">- Seleccionar -</option>');
            $('#cbo-variante_1-modal').append('<option value="' + responseVariante1.arrData[0].ID + '">' + responseVariante1.arrData[0].Nombre + '</option>');

            $('#cbo-valor_1-modal').html('<option value="" selected="selected">- Sin registros -</option>');
            url = base_url + 'HelperController/getVarianteDetalle';
            var arrParams = {
              ID_Variante_Item: responseVariante1.arrData[0].ID,
            }
            $.post(url, arrParams, function (responseDetalle) {
              $('#cbo-valor_1-modal').html('<option value="" selected="selected">- Seleccionar -</option>');
              if (responseDetalle.sStatus == 'success') {
                var l = responseDetalle.arrData.length;
                for (var x = 0; x < l; x++)
                  $('#cbo-valor_1-modal').append('<option value="' + responseDetalle.arrData[x].ID + '">' + responseDetalle.arrData[x].Nombre + '</option>');
              } else {
                $('#cbo-valor_1-modal').html('<option value="" selected="selected">- vacío -</option>');
              }
            }, 'JSON');
          }
        } else {
          if (responseVariante1.sMessageSQL !== undefined) {
            console.log(responseVariante1.sMessageSQL);
          }
        }
      }, 'JSON');

      //Variante 2
      $('#cbo-variante_2-modal').html('<option value="" selected="selected">- Sin registros -</option>');
      $('#cbo-valor_2-modal').html('<option value="" selected="selected">- Sin registros -</option>');
      url = base_url + 'HelperController/getVariantexIDTablaDato';
      $.post(url, { ID_Tabla_Dato: 2085 }, function (responseVariante1) {
        if (responseVariante1.sStatus == 'success') {
          var l = responseVariante1.arrData.length;
          if (l == 1) {
            $('#cbo-variante_2-modal').html('<option value="" selected="selected">- Seleccionar -</option>');
            $('#cbo-variante_2-modal').append('<option value="' + responseVariante1.arrData[0].ID + '">' + responseVariante1.arrData[0].Nombre + '</option>');

            $('#cbo-valor_2-modal').html('<option value="" selected="selected">- Sin registros -</option>');
            url = base_url + 'HelperController/getVarianteDetalle';
            var arrParams = {
              ID_Variante_Item: responseVariante1.arrData[0].ID,
            }
            $.post(url, arrParams, function (responseDetalle) {
              $('#cbo-valor_2-modal').html('<option value="" selected="selected">- Seleccionar -</option>');
              if (responseDetalle.sStatus == 'success') {
                var l = responseDetalle.arrData.length;
                for (var x = 0; x < l; x++)
                  $('#cbo-valor_2-modal').append('<option value="' + responseDetalle.arrData[x].ID + '">' + responseDetalle.arrData[x].Nombre + '</option>');
              } else {
                $('#cbo-valor_2-modal').html('<option value="" selected="selected">- vacío -</option>');
              }
            }, 'JSON');
          }
        } else {
          if (responseVariante1.sMessageSQL !== undefined) {
            console.log(responseVariante1.sMessageSQL);
          }
        }
      }, 'JSON');

      //Variante 3
      $('#cbo-variante_3-modal').html('<option value="" selected="selected">- Sin registros -</option>');
      $('#cbo-valor_3-modal').html('<option value="" selected="selected">- Sin registros -</option>');
      url = base_url + 'HelperController/getVariantexIDTablaDato';
      $.post(url, { ID_Tabla_Dato: 2086 }, function (responseVariante1) {
        if (responseVariante1.sStatus == 'success') {
          var l = responseVariante1.arrData.length;
          if (l == 1) {
            $('#cbo-variante_3-modal').html('<option value="" selected="selected">- Seleccionar -</option>');
            $('#cbo-variante_3-modal').append('<option value="' + responseVariante1.arrData[0].ID + '">' + responseVariante1.arrData[0].Nombre + '</option>');

            $('#cbo-valor_3-modal').html('<option value="" selected="selected">- Sin registros -</option>');
            url = base_url + 'HelperController/getVarianteDetalle';
            var arrParams = {
              ID_Variante_Item: responseVariante1.arrData[0].ID,
            }
            $.post(url, arrParams, function (responseDetalle) {
              $('#cbo-valor_3-modal').html('<option value="" selected="selected">- Seleccionar -</option>');
              if (responseDetalle.sStatus == 'success') {
                var l = responseDetalle.arrData.length;
                for (var x = 0; x < l; x++)
                  $('#cbo-valor_3-modal').append('<option value="' + responseDetalle.arrData[x].ID + '">' + responseDetalle.arrData[x].Nombre + '</option>');
              } else {
                $('#cbo-valor_3-modal').html('<option value="" selected="selected">- vacío -</option>');
              }
            }, 'JSON');
          }
        } else {
          if (responseVariante1.sMessageSQL !== undefined) {
            console.log(responseVariante1.sMessageSQL);
          }
        }
      }, 'JSON');

      
			$( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
			$('#modal-body-info_item').html('');
      $( '#modal-table-info_item' ).remove();
      $( '.modal-info_item' ).modal('show');

      $('#modal-header-info_item-title').html('Agregar Variante');

      var sHtmlTableInfoItem ='';
				if (response.No_Imagen_Item != null && response.No_Imagen_Item != '' && response.No_Imagen_Item !== undefined) {
					sHtmlTableInfoItem += '<div class="row">';
            sHtmlTableInfoItem += '<div class="col-xs-6 col-sm-6">';
              sHtmlTableInfoItem += '<div class="thumbnail">';
                sHtmlTableInfoItem += '<img class="img-responsive" style="max-height:80px" text-center" src="' + response.No_Imagen_Item + '">';
              sHtmlTableInfoItem += '</div>';
            sHtmlTableInfoItem += '</div>';
            sHtmlTableInfoItem += '<div class="col-xs-6 col-sm-6">';
              sHtmlTableInfoItem += 'Nombre: <label style="font-size: 18px;">' + response.No_Producto + '</label>';
              sHtmlTableInfoItem += '<br>Codigo: <label style="font-size: 16px;">' + response.Nu_Codigo_Barra + '</label>';
              if (response.No_Codigo_Interno != null && response.No_Codigo_Interno != '' && response.No_Codigo_Interno !== undefined) {
                sHtmlTableInfoItem += '<br>SKU: <label>' + response.No_Codigo_Interno + '</label>';
              }
              sHtmlTableInfoItem += '<br>Precio: <label style="font-size: 15px;">' + Math.round10(response.Ss_Precio, -3) + '</label>';
            sHtmlTableInfoItem += '</div>';
          sHtmlTableInfoItem += '</div><br>';
				} else {
					sHtmlTableInfoItem += '<div class="row text-center">';
            sHtmlTableInfoItem += '<div class="col-xs-12 col-sm-12 text-center">';
              sHtmlTableInfoItem += 'Nombre: <label style="font-size: 18px;">' + response.No_Producto + '</label>';
              sHtmlTableInfoItem += '<br>Codigo: <label style="font-size: 16px;">' + response.Nu_Codigo_Barra + '</label>';
              if (response.No_Codigo_Interno != null && response.No_Codigo_Interno != '' && response.No_Codigo_Interno !== undefined) {
                sHtmlTableInfoItem += '<br>SKU: <label>' + response.No_Codigo_Interno + '</label>';
              }
              sHtmlTableInfoItem += '<br>Precio: <label style="font-size: 15px;">' + Math.round10(response.Ss_Precio, -3) + '</label>';
            sHtmlTableInfoItem += '</div>';
          sHtmlTableInfoItem += '</div><br>';
        }

				sHtmlTableInfoItem +=
				'<div class="col-xs-12 col-sm-12">'+
          '<div class="row">'+
            '<div class="col-xs-6 col-sm-4 col-md-4">'+
              '<label>Codigo</label>'+
              '<div class="form-group">'+
              '<input type="text" id="txt-Nu_Codigo_Barra-modal" class="form-control input-codigo_barra input-Mayuscula" placeholder="Ingresar Codigo" maxlength="20" autocomplete="off">'+
              '<span class="help-block" id="error"></span>'+
              '</div>'+
            '</div>'+

            '<div class="col-xs-6 col-sm-4 col-md-4">'+
              '<label>SKU</label>'+
              '<div class="form-group">'+
              '<input type="text" id="txt-No_Codigo_Interno-modal" class="form-control input-codigo_barra input-Mayuscula" placeholder="SKU (opcional)" maxlength="20" autocomplete="off">'+
              '<span class="help-block" id="error"></span>'+
              '</div>'+
            '</div>'+

            '<div class="col-xs-12 col-sm-4 col-md-4">'+
              '<label>Precio</label>'+
              '<div class="form-group">'+
              '<input type="text" id="txt-Ss_Precio-modal" inputmode="decimal" class="form-control required input-decimal" maxlength="13" autocomplete="off" placeholder="Ingresar Precio" value="' + Math.round10(response.Ss_Precio, -3) + '">'+
              '<span class="help-block" id="error"></span>'+
              '</div>'+
            '</div>'+
          '</div>'+

          '<div class="row">'+
            '<div class="col-xs-6 col-sm-3 col-md-2 col-lg-2">'+
              '<label>Variante 1</label>'+
              '<div class="form-group">'+
              '<select id="cbo-variante_1-modal" class="form-control select2" style="width: 100%;"></select>'+
              '<span class="help-block" id="error"></span>'+
              '</div>'+
            '</div>'+
            
            '<div class="col-xs-6 col-sm-3 col-md-2 col-lg-2">'+
              '<label>Valor 1</label>'+
              '<span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Para crear ir a Logística > Reglas de Logística > Variante">'+
              '<i class="fa fa-info-circle"></i>'+
              '</span>'+
              '<div class="form-group">'+
              '<select id="cbo-valor_1-modal" class="form-control select2" style="width: 100%;"></select>'+
              '<span class="help-block" id="error"></span>'+
              '</div>'+
            '</div>'+

            '<div class="col-xs-6 col-sm-3 col-md-2 col-lg-2">'+
              '<label>Variante 2</label>'+
              '<div class="form-group">'+
              '<select id="cbo-variante_2-modal" class="form-control select2" style="width: 100%;"></select>'+
              '<span class="help-block" id="error"></span>'+
              '</div>'+
            '</div>'+
            
            '<div class="col-xs-6 col-sm-3 col-md-2 col-lg-2">'+
              '<label>Valor 2</label>'+
              '<span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Para crear ir a Logística > Reglas de Logística > Variante">'+
              '<i class="fa fa-info-circle"></i>'+
              '</span>'+
              '<div class="form-group">'+
              '<select id="cbo-valor_2-modal" class="form-control select2" style="width: 100%;"></select>'+
              '<span class="help-block" id="error"></span>'+
              '</div>'+
            '</div>'+

            '<div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">'+
              '<label>Variante 3</label>'+
              '<div class="form-group">'+
              '<select id="cbo-variante_3-modal" class="form-control select2" style="width: 100%;"></select>'+
              '<span class="help-block" id="error"></span>'+
              '</div>'+
            '</div>'+
            
            '<div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">'+
              '<label>Valor 3</label>'+
              '<span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Para crear ir a Logística > Reglas de Logística > Variante">'+
              '<i class="fa fa-info-circle"></i>'+
              '</span>'+
              '<div class="form-group">'+
              '<select id="cbo-valor_3-modal" class="form-control select2" style="width: 100%;"></select>'+
              '<span class="help-block" id="error"></span>'+
              '</div>'+
            '</div>'+
          '</div>'+

          '<div class="row">'+
            '<div class="col-xs-12 col-sm-12 col-md-12">'+
              '<label class="hidden-xs">&nbsp;</label>'+
              '<div class="form-group">'+
              '<button type="button" id="btn-addVariante-modal" class="btn btn-primary btn-block">Agregar variante</button>'+
              '</div>'+
            '</div>'+
          '</div>'+
          
          '<div class="table-responsive div-variante-modal">'+
            '<table id="table-variante-modal" class="table table-striped table-bordered">'+
              '<thead>'+
                '<tr>'+
                  '<th style="display:none;" class="text-left">ID</th>'+
                  '<th style="display:none;" class="text-left">ID Variante 1</th>'+
                  '<th style="display:none;" class="text-left">ID Valor Variante 1</th>'+
                  '<th style="display:none;" class="text-left">ID Variante 2</th>'+
                  '<th style="display:none;" class="text-left">ID Valor Variante 2</th>'+
                  '<th style="display:none;" class="text-left">ID Variante 3</th>'+
                  '<th style="display:none;" class="text-left">ID Valor Variante 3</th>'+
                  '<th class="text-left">Código</th>'+
                  '<th class="text-right">SKU</th>'+
                  '<th class="text-right">Precio</th>'+
                  '<th class="text-center">Variante 1</th>'+
                  '<th class="text-center">Valor 1</th>'+
                  '<th class="text-center">Variante 2</th>'+
                  '<th class="text-center">Valor 2</th>'+
                  '<th class="text-center">Variante 3</th>'+
                  '<th class="text-center">Valor 3</th>'+
                  '<th class="text-center"></th>'+
                '</tr>'+
              '</thead>'+
              '<tbody>'+
              '</tbody>'+
              '<tfoot>'+
                '<th class="text-left" colspan="16"><button type="button" id="btn-guardar-variante-modal" class="btn btn-success btn-lg btn-block">Guardar</button></th>'+
              '</tfoot>'+
            '</table>'+
          '</div>'+
        '</div>';

				$( '#modal-body-info_item' ).append( sHtmlTableInfoItem );
        $( '.div-variante-modal' ).hide();

        validateDecimal();
        validateNumber();
        validateNumberOperation();
        validateCodigoBarra();

        $( '#table-variante-modal body' ).empty();

        /* AGREGAR VARIANTE MODAL TEMPORAL */
        $( '#btn-addVariante-modal' ).click(function(e) {
          e.preventDefault();
          
          var $sCodigoItem = $( '#txt-Nu_Codigo_Barra-modal' ).val().trim().toUpperCase();
          var $sSkuItem = $( '#txt-No_Codigo_Interno-modal' ).val().trim().toUpperCase();
          var $fPrecio = $( '#txt-Ss_Precio-modal' ).val().trim();

          var $iIdVariante1 = $( '#cbo-variante_1-modal' ).val();
          var $iIdValorVariante1 = $( '#cbo-valor_1-modal' ).val();
          var $iTextVariante1 = $( '#cbo-variante_1-modal :selected' ).text();
          var $iTextValorVariante1 = $( '#cbo-valor_1-modal :selected' ).text();

          var $iIdVariante2 = $( '#cbo-variante_2-modal' ).val();
          var $iIdValorVariante2 = $( '#cbo-valor_2-modal' ).val();
          var $iTextVariante2 = $( '#cbo-variante_2-modal :selected' ).text();
          var $iTextValorVariante2 = $( '#cbo-valor_2-modal :selected' ).text();

          var $iIdVariante3 = $( '#cbo-variante_3-modal' ).val();
          var $iIdValorVariante3 = $( '#cbo-valor_3-modal' ).val();
          var $iTextVariante3 = $( '#cbo-variante_3-modal :selected' ).text();
          var $iTextValorVariante3 = $( '#cbo-valor_3-modal :selected' ).text();
        
          var $iIdRegistro = $sCodigoItem + $iIdVariante1 + $iIdVariante2 + $iIdVariante3;

          if($iTextVariante1 == '- Seleccionar -')
            $iTextVariante1 = '';
          if($iTextValorVariante1 == '- Seleccionar -')
            $iTextValorVariante1 = '';
          if($iTextVariante2 == '- Seleccionar -')
            $iTextVariante2 = '';
          if($iTextValorVariante2 == '- Seleccionar -')
            $iTextValorVariante2 = '';
          if($iTextVariante3 == '- Seleccionar -')
            $iTextVariante3 = '';
          if($iTextValorVariante3 == '- Seleccionar -')
            $iTextValorVariante3 = '';

          if ( $sCodigoItem.length === 0) {
            alert('Ingresar codigo');
            $( '#txt-Nu_Codigo_Barra-modal' ).focus();
          } else if ( $sCodigoItem == 0) {
            alert('Codigo no puede ser 0');
            $( '#txt-Nu_Codigo_Barra-modal' ).focus();
          } else if ( $fPrecio.length === 0) {
            alert('Ingresar precio');
            $( '#txt-Nu_Codigo_Barra-modal' ).focus();
          } else if ( $fPrecio == 0) {
            alert('Precio debe ser mayor a 0');
            $( '#txt-Nu_Codigo_Barra-modal' ).focus();
          } else {
            var table_enlace_producto =
            "<tr id='tr_variante_producto" + $iIdRegistro + "'>"
              + "<td class='text-left' style='display:none;'>" + $iIdRegistro + "</td>"
              + "<td class='text-left td-iIdVariante1' style='display:none;'>" + $iIdVariante1 + "</td>"
              + "<td class='text-left td-iIdValorVariante1' style='display:none;'>" + $iIdValorVariante1 + "</td>"
              + "<td class='text-left td-iIdVariante2' style='display:none;'>" + $iIdVariante2 + "</td>"
              + "<td class='text-left td-iIdValorVariante2' style='display:none;'>" + $iIdValorVariante2 + "</td>"
              + "<td class='text-left td-iIdVariante3' style='display:none;'>" + $iIdVariante3 + "</td>"
              + "<td class='text-left td-iIdValorVariante3' style='display:none;'>" + $iIdValorVariante3 + "</td>"
              + "<td class='text-left td-sCodigo'>" + $sCodigoItem + "</td>"
              + "<td class='text-left td-sSku'>" + $sSkuItem + "</td>"
              + "<td class='text-left td-fPrecio'>" + $fPrecio + "</td>"
              + "<td class='text-left'>" + $iTextVariante1 + "</td>"
              + "<td class='text-left'>" + $iTextValorVariante1 + "</td>"
              + "<td class='text-left'>" + $iTextVariante2 + "</td>"
              + "<td class='text-left'>" + $iTextValorVariante2 + "</td>"
              + "<td class='text-left'>" + $iTextVariante3 + "</td>"
              + "<td class='text-left'>" + $iTextValorVariante3 + "</td>"
              + "<td class='text-center'><button type='button' id='btn-delete_variante-modal' class='btn btn-xs btn-link' alt='Eliminar' title='Eliminar'><i class='fa fa-2x fa-trash-o' aria-hidden='true'></i></button></td>"
            + "</tr>";
            
            if( isExistTableTemporalVarianteModel($iIdRegistro) ){
              alert('Ya existe codigo: ' + $sCodigoItem);
              
              $( '#txt-Nu_Codigo_Barra-modal' ).focus();
            } else {
              $( '.div-variante-modal' ).show();
              $( '#table-variante-modal' ).append(table_enlace_producto);
              $( '#txt-Nu_Codigo_Barra-modal' ).val('');
              $( '#txt-No_Codigo_Interno-modal' ).val('');
              //$( '#txt-Ss_Precio-modal' ).val('');
              
              $( '#txt-Nu_Codigo_Barra-modal' ).focus();
            }
          }
        })
        
        $( '#table-variante-modal tbody' ).on('click', '#btn-delete_variante-modal', function(){
          $(this).closest ('tr').remove ();
          if ($( '#table-variante-modal >tbody >tr' ).length == 0)
            $( '.div-variante-modal' ).hide();
        })
        /* FIN AGREGAR VARIANTE MODAL TEMPORAL */

        $( '#btn-guardar-variante-modal' ).click(function(e) {
          var arrVarianteModal = [], $Nu_Codigo_Barra, $No_Codigo_Interno, $Ss_Precio, $ID_Variante_Item_1, $ID_Variante_Item_Detalle_1, $ID_Variante_Item_2, $ID_Variante_Item_Detalle_2, $ID_Variante_Item_3, $ID_Variante_Item_Detalle_3;
          $("#table-variante-modal > tbody > tr").each(function(){
            fila = $(this);
            
		        $Nu_Codigo_Barra = fila.find(".td-sCodigo").text();
		        $No_Codigo_Interno = fila.find(".td-sSku").text();
		        $Ss_Precio = fila.find(".td-fPrecio").text();
		        $ID_Variante_Item_1 = fila.find(".td-iIdVariante1").text();
		        $ID_Variante_Item_Detalle_1 = fila.find(".td-iIdValorVariante1").text();
		        $ID_Variante_Item_2 = fila.find(".td-iIdVariante2").text();
		        $ID_Variante_Item_Detalle_2 = fila.find(".td-iIdValorVariante2").text();
		        $ID_Variante_Item_3 = fila.find(".td-iIdVariante3").text();
		        $ID_Variante_Item_Detalle_3 = fila.find(".td-iIdValorVariante3").text();
          
            obj = {};
            
            obj.Nu_Codigo_Barra = $Nu_Codigo_Barra;
            obj.No_Codigo_Interno = $No_Codigo_Interno;
            obj.Ss_Precio = $Ss_Precio;
            obj.ID_Variante_Item_1 = $ID_Variante_Item_1;
            obj.ID_Variante_Item_Detalle_1 = $ID_Variante_Item_Detalle_1;
            obj.ID_Variante_Item_2 = $ID_Variante_Item_2;
            obj.ID_Variante_Item_Detalle_2 = $ID_Variante_Item_Detalle_2;
            obj.ID_Variante_Item_3 = $ID_Variante_Item_3;
            obj.ID_Variante_Item_Detalle_3 = $ID_Variante_Item_Detalle_3;
            arrVarianteModal.push(obj);
          });
          
          $( '#btn-guardar-variante-modal' ).text('');
          $( '#btn-guardar-variante-modal' ).attr('disabled', true);
          $( '#btn-guardar-variante-modal' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
        
          $( '#modal-loader' ).modal('show');
          
          url = base_url + 'Logistica/ReglasLogistica/ProductoController/crudProductoxVarianteModal';
          $.ajax({
            type		  : 'POST',
            dataType	: 'JSON',
            url		    : url,
            data		  : {
              iIdItem : iIdItem,
              arrVarianteModal : arrVarianteModal,
            },
            success : function( response ){

              $( '#modal-loader' ).modal('hide');
              
              $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
              $( '#modal-message' ).modal('show');
              
              if (response.status == 'success'){
                $( '.modal-info_item' ).modal('hide');

                accion_producto = '';
                
                $( '.modal-message' ).addClass(response.style_modal);
                $( '.modal-title-message' ).text(response.message);
                reload_table_producto();
                setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);            
              } else {
                $( '.modal-message' ).addClass(response.style_modal);
                $( '.modal-title-message' ).text(response.message);
                setTimeout(function() {$('#modal-message').modal('hide');}, 1200);
              }
              
              $( '#btn-guardar-variante-modal' ).text('');
              $( '#btn-guardar-variante-modal' ).append( 'Guardar' );
              $( '#btn-guardar-variante-modal' ).attr('disabled', false);
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
              
              $( '#btn-guardar-variante-modal' ).text('');
              $( '#btn-guardar-variante-modal' ).append( 'Guardar' );
              $( '#btn-guardar-variante-modal' ).attr('disabled', false);
            }
          });
        })
    },
    error: function (jqXHR, textStatus, errorThrown) {
      accion_producto = '';
      $( '#modal-loader' ).modal('hide');
      
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

function isExistTableTemporalVarianteModel($ID){
  return Array.from($('tr[id*=tr_variante_producto]'))
    .some(element => ($('td:nth(0)',$(element)).html()===$ID));
}

function isExistTableTemporalPreciosxMayor($Qt_Producto_x_Mayor){
  return Array.from($('tr[id*=tr_producto_precio_x_mayor]'))
    .some(element => ($('td:nth(0)',$(element)).html()==$Qt_Producto_x_Mayor));
}