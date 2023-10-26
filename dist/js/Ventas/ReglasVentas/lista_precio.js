var url;
var table_lista_precio_cabecera;
var table_lista_precio_detalle;
var accion_lista_precio_cabecera = '';
var accion_lista_precio_detalle = '';

function importarExcelListaPrecios(){
  $( '[name="modal-ID_Lista_Precio_Cabecera"]' ).val( '' );
  $( '[name="modal-ID_Lista_Precio_Cabecera"]' ).val( $( '[name="ID_Lista_Precio_Cabecera"]' ).val() );
  $( ".modal_importar_lista_precio" ).modal( "show" );
}

$( '#modal-loader' ).modal('hide');
$(function () {
  // Validate exist file excel product
	$( document ).on('click', '#btn-excel-importar_lista_precio', function(event) {
	  if ( $( "#my-file-selector_lista_precio" ).val().length === 0 ) {
      $( '#my-file-selector_lista_precio' ).closest('.form-group').find('.help-block').html('Seleccionar archivo');
		  $( '#my-file-selector_lista_precio' ).closest('.form-group').removeClass('has-success').addClass('has-error');
	  } else {
      $( '#btn-cancel-list_price' ).attr('disabled', true);
      $( '#a-download-list_price' ).attr('disabled', true);
	    
      $( '#btn-excel-importar_lista_precio' ).text('');
      $( '#btn-excel-importar_lista_precio' ).attr('disabled', true);
      $( '#btn-excel-importar_lista_precio' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
  
      $( '#modal-loader' ).modal('show');
	  }
  })
  
  $('.select2').select2();
  $('[data-mask]').inputmask();
  $( '.div-AgregarEditarPrecio' ).hide();
  
  $( '#modal-loader' ).modal('hide');
  
  url = base_url + 'Ventas/ReglasVenta/Lista_precio_controller/ajax_list';
  table_lista_precio_cabecera = $('#table-Lista_Precio').DataTable({
    'dom': 'B<"top">frt<"bottom"lip><"clear">',
    buttons     : [{
      extend    : 'excel',
      text      : '<i class="fa fa-file-excel-o color_icon_excel"></i> Excel',
      titleAttr : 'Excel',
      exportOptions: {
        columns: ':visible'
      }
    },
    {
      extend    : 'pdf',
      text      : '<i class="fa fa-file-pdf-o color_icon_pdf"></i> PDF',
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
        data.Filtros_Tabla = $( '#cbo-Filtros_Tabla' ).val(),
        data.Global_Filter = $( '#txt-Global_Filter' ).val();
      },
    },
    'columnDefs': [{
      'className' : 'text-center',
      'targets'   : 'no-sort',
      'orderable' : false,
    },],
    'lengthMenu': [[10, 100, 1000, -1], [10, 100, 500, 1000]],
  });

  $('.dataTables_length').addClass('col-xs-4 col-sm-5 col-md-1');
  $('.dataTables_info').addClass('col-xs-8 col-sm-7 col-md-4');
  $('.dataTables_paginate').addClass('col-xs-12 col-sm-12 col-md-7');
  
  $( '#txt-Global_Filter' ).keyup(function() {
    table_lista_precio_cabecera.search($(this).val()).draw();
  });
  
  $( '#form-Lista_Precio' ).validate({
		rules:{
			No_Lista_Precio: {
				required: true,
			},
			Nu_Tipo_Lista_Precio: {
				required: true,
			},
		},
		messages:{
			No_Lista_Precio:{
				required: "Ingresar nombre",
			},
			Nu_Tipo_Lista_Precio:{
				required: "Seleccionar tipo",
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
		submitHandler: form_Lista_Precio_Cabecera
	});
	
	//Lista precio por producto
  url = base_url + 'Ventas/ReglasVenta/Lista_precio_controller/ajax_list_producto';
  table_lista_precio_detalle = $('#table-Lista_Precio_Producto').DataTable({
    'dom': 'B<"top">frt<"bottom"lip><"clear">',
    buttons     : [{
      extend    : 'excel',
      text      : '<i class="fa fa-file-excel-o color_icon_excel"></i> Excel',
      titleAttr : 'Excel',
      exportOptions: {
        columns: ':visible'
      }
    },
    {
      extend    : 'pdf',
      text      : '<i class="fa fa-file-pdf-o color_icon_pdf"></i> PDF',
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
        data.Filtro_ID_Lista_Precio_Cabecera = $( '#txt-ID_Lista_Precio_Cabecera' ).val(),
        data.Filtros_Tabla = $( '#cbo-Filtros_Tabla_Precio' ).val(),
        data.Global_Filter = $( '#txt-Global_Filter_Producto' ).val();
      },
    },
    'columnDefs': [{
        'className' : 'text-center',
        'targets'   : 'no-sort',
        'orderable' : false,
      },
    ],
    'lengthMenu': [[10, 100, 1000, -1], [10, 100, 500, 1000]],
  });

  $('.dataTables_length').addClass('col-xs-4 col-sm-5 col-md-1');
  $('.dataTables_info').addClass('col-xs-8 col-sm-7 col-md-4');
  $('.dataTables_paginate').addClass('col-xs-12 col-sm-12 col-md-7');

  $( '#txt-Global_Filter_Producto' ).keyup(function() {
    table_lista_precio_detalle.search($(this).val()).draw();
  });
  
  $( '#form-Lista_Precio_Producto' ).validate({
		rules:{
			ID_Producto: {
				required: true,
			},
			Ss_Precio: {
				required: true,
			},
		},
		messages:{
			ID_Producto:{
				required: "Seleccionar producto",
			},
			Ss_Precio:{
				required: "Ingresar precio",
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
		submitHandler: form_Lista_Precio_Detalle
	});
  
  $( '#form-Lista_Precio_Producto_Editar' ).validate({
		rules:{
			Ss_Precio_Editar: {
				required: true,
			},
		},
		messages:{
			Ss_Precio_Editar:{
				required: "Ingresar precio",
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
		submitHandler: form_Lista_Precio_Detalle_Producto
	});
  
  $( '#cbo-tipos_lista_precio' ).change(function() {
    $( '#cbo-Socios' ).html( '<option value="" selected="selected">Sin datos</option>');
    if( $(this).val() > 0 ) {
      var sTipoSocio = $(this).val() == '1' ? 'Clientes' : 'Proveedores';
      $( '#label-tipo_socio' ).text( sTipoSocio );
      $( '#modal-loader' ).modal('show');
      url = base_url + 'AutocompleteController/sendData';
      $.post( url, {sTabla : 'entidad', iTipoSocio : $(this).val()}, function( response ){
        $( '#cbo-Socios' ).html( '<option value="0" selected="selected">- Seleccionar -</option>');
        for (var i = 0; i < response.length; i++)
          $( '#cbo-Socios' ).append( '<option value="' + response[i].ID + '">' + response[i].Nombre + '</option>' );
        $( '#modal-loader' ).modal('hide');
      }, 'JSON');
    }
  })
  
  $(document).bind('keydown', 'alt+a', function(){
    agregarLista_Precio();
  });

  //REPLICAR LISTA DE PRECIO
  $( '#modal_replicacion-cbo-almacen_destino' ).change(function() {
    $( '#modal-replicacion-cbo-lista_precios_destino' ).html('');
    url = base_url + 'HelperController/getListaPrecioxId';
    $.post( url, {ID_Almacen : $( '#modal_replicacion-cbo-almacen_destino' ).val()}, function( responseLista ){
      $('#modal-replicacion-cbo-lista_precios_destino').html('<option value="0">- Seleccionar -</option>');
      for (var i = 0; i < responseLista.length; i++) {
        $( '#modal-replicacion-cbo-lista_precios_destino' ).append( '<option value="' + responseLista[i].ID_Lista_Precio_Cabecera + '">' + responseLista[i].No_Lista_Precio + ' ' + (responseLista[i].Nu_Tipo_Lista_Precio == 1 ? 'Venta' : 'Compra') + '</option>' );
      }
    }, 'JSON');
  });

  $( '#btn-replicar_lista_precio' ).click(function(){
    $('.help-block').empty();
    $('.form-group').removeClass('has-error');
    if( $( '#modal_replicacion-cbo-almacen_destino' ).val() == '0' ) {
      $( '#modal_replicacion-cbo-almacen_destino' ).closest('.form-group').find('.help-block').html('Seleccionar almacén');
		  $( '#modal_replicacion-cbo-almacen_destino' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if( $( '#modal_replicacion-cbo-almacen_destino' ).val() == $('#modal_replicacion-cbo-almacen').val() ) {
      $( '#modal_replicacion-cbo-almacen_destino' ).closest('.form-group').find('.help-block').html('No puedes replicar al mismo almacén');
		  $( '#modal_replicacion-cbo-almacen_destino' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if( $( '#modal-replicacion-cbo-lista_precios_destino' ).val() == '0' ) {
      $( '#modal-replicacion-cbo-lista_precios_destino' ).closest('.form-group').find('.help-block').html('Seleccionar lista precio');
		  $( '#modal-replicacion-cbo-lista_precios_destino' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {      
      $( '#btn-replicar_lista_precio' ).text('');
      $( '#btn-replicar_lista_precio' ).attr('disabled', true);
      $( '#btn-replicar_lista_precio' ).append( 'Replicando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
      $( '#btn-salir' ).attr('disabled', true);

      url = base_url + 'Ventas/ReglasVenta/Lista_precio_controller/replicarListaPrecio';
      $.ajax({
        type : 'POST',
        dataType : 'JSON',
        url : url,
        data : $('#form-replicacion_precio').serialize(),
        success : function( response ){
          $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
          $( '#modal-message' ).modal('show');

          if ( response.sStatus=='success' ) {
            $( '.modal-replicacion_precio' ).modal('hide');

            $( '.modal-message' ).addClass( 'modal-' + response.sStatus);
            $( '.modal-title-message' ).text( response.sMessage );
            setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
          } else {
            $( '.modal-message' ).addClass( 'modal-' + response.sStatus );
            $( '.modal-title-message' ).text( response.sMessage );
            setTimeout(function() {$('#modal-message').modal('hide');}, 6200);
          }
          
          $( '#btn-replicar_lista_precio' ).text('');
          $( '#btn-replicar_lista_precio' ).append( 'Replicar' );
          $( '#btn-replicar_lista_precio' ).attr('disabled', false);
          $( '#btn-salir' ).attr('disabled', false);
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

        $( '#btn-replicar_lista_precio' ).text('');
        $( '#btn-replicar_lista_precio' ).attr('disabled', false);
        $( '#btn-replicar_lista_precio' ).append( 'Replicar' );
        $( '#btn-salir' ).attr('disabled', false);
      })
    }//validación de lista de precio
  })// ./ lista de precio
})

function agregarLista_Precio(){
  accion_lista_precio_cabecera = 'add';
  
  $( '[name="EID_Empresa"]' ).focus();
  
  $( '#form-Lista_Precio' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '#modal-loader' ).modal('show');
  
  $( '.div-Listar' ).hide();
  $( '.div-AgregarEditar' ).show();
		
  $( '.title_Entidad' ).text('Nueva Lista Precio');

  $( '[name="EID_Empresa"]' ).val('');
  $( '[name="EID_Organizacion"]' ).val('');
  $( '[name="EID_Lista_Precio_Cabecera"]' ).val('');
  $( '[name="ENo_Lista_Precio"]' ).val('');
  
  $( '#cbo-Almacenes' ).html('<option value="" selected="selected">- Seleccionar -</option>');
  url = base_url + 'HelperController/getAlmacenes';
  $.post( url, {ID_Organizacion: $( '#header-a-id_organizacion' ).val()}, function( responseAlmacenes ){
    var iCantidadRegistros = responseAlmacenes.length;
    if (iCantidadRegistros == 1) {
      $('#cbo-Almacenes').html( '<option value="' + responseAlmacenes[0].ID_Almacen + '">' + responseAlmacenes[0].No_Almacen + '</option>' );
    } else if (iCantidadRegistros > 1) {
      for (var i = 0; i < iCantidadRegistros; i++)
        $( '#cbo-Almacenes' ).append( '<option value="' + responseAlmacenes[i].ID_Almacen + '">' + responseAlmacenes[i].No_Almacen + '</option>' );
    } else {
      $( '#cbo-Almacenes' ).html('<option value="" selected="selected">- Sin Almacenes -</option>');
    }
    $( '#modal-loader' ).modal('hide');
  }, 'JSON');
  
  $( '#cbo-tipos_lista_precio' ).html( '<option value="">- Seleccionar -</option>' );
  $( '#cbo-tipos_lista_precio' ).append( '<option value="1">Venta</option>' );
  $( '#cbo-tipos_lista_precio' ).append( '<option value="2">Compra</option>' );

  url = base_url + 'HelperController/getMonedas';
  $.post( url , function( response ){
    $( '#cbo-Monedas' ).html('');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-Monedas' ).append( '<option value="' + response[i].ID_Moneda + '">' + response[i].No_Moneda + '</option>' );
  }, 'JSON');
  
  $( '#cbo-Estado' ).html( '<option value="1">Activo</option>' );
  $( '#cbo-Estado' ).append( '<option value="0">Inactivo</option>' );
}

function verLista_Precio(ID){
  accion_lista_precio_cabecera = 'upd';
  
  $( '[name="EID_Empresa"]' ).focus();
  
  $( '#modal-loader' ).modal('show');
  
  $( '.div-Listar' ).hide();
  
  $( '.div-TiposLista_Precio' ).hide();

  $( '#form-Lista_Precio' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
 
  var selected = '';
 
  url = base_url + 'Ventas/ReglasVenta/Lista_precio_controller/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $( '.div-AgregarEditar' ).show();
      
      $( '.title_Entidad' ).text('Modifcar Lista Precio');
      
      $('[name="EID_Empresa"]').val(response.ID_Empresa);
      $('[name="EID_Organizacion"]').val(response.ID_Organizacion);
      $('[name="EID_Lista_Precio_Cabecera"]').val(response.ID_Lista_Precio_Cabecera);
      $('[name="ENo_Lista_Precio"]').val(response.No_Lista_Precio);
            
      url = base_url + 'HelperController/getAlmacenes';
      $.post( url, {ID_Organizacion : response.ID_Organizacion} , function( responseAlmacenes ){
        $( '#cbo-Almacenes' ).html( '<option value="0" selected="selected">- Seleccionar -</option>');
        for (var i = 0; i < responseAlmacenes.length; i++) {
          selected = '';
          if(response.ID_Almacen == responseAlmacenes[i].ID_Almacen)
            selected = 'selected="selected"';
          $( '#cbo-Almacenes' ).append( '<option value="' + responseAlmacenes[i].ID_Almacen + '" ' + selected + '>' + responseAlmacenes[i].No_Almacen + '</option>' );
        }
      }, 'JSON');
      
      $('[name="No_Lista_Precio"]').val(response.No_Lista_Precio);

      $( '#cbo-tipos_lista_precio' ).html( '' );
      for (var i = 1; i < 3; i++){
        selected = '';
        if(response.Nu_Tipo_Lista_Precio == i)
          selected = 'selected="selected"';
        $( '#cbo-tipos_lista_precio' ).append( '<option value="' + i + '" ' + selected + '>' + (i == 1 ? 'Venta' : 'Compra') + '</option>' );
      }
      
      url = base_url + 'HelperController/getMonedas';
      $.post( url , function( responseMonedas ){
        $( '#cbo-Monedas' ).html('');
        for (var i = 0; i < responseMonedas.length; i++) {
          selected = '';
          if(response.ID_Moneda == responseMonedas[i].ID_Moneda)
            selected = 'selected="selected"';
          $( '#cbo-Monedas' ).append( '<option value="' + responseMonedas[i].ID_Moneda + '" ' + selected + '>' + responseMonedas[i].No_Moneda + '</option>' );
        }
      }, 'JSON');
      
      url = base_url + 'AutocompleteController/sendData';
      $.post( url, {sTabla : 'entidad', iTipoSocio : response.Nu_Tipo_Lista_Precio}, function( responseSocios ){
        $( '#cbo-Socios' ).html( '<option value="0" selected="selected">- Ninguno -</option>');
        for (var i = 0; i < responseSocios.length; i++) {
          selected = '';
          if(response.ID_Entidad == responseSocios[i].ID)
            selected = 'selected="selected"';
          $( '#cbo-Socios' ).append( '<option value="' + responseSocios[i].ID + '" ' + selected + '>' + responseSocios[i].Nombre + '</option>' );
        }
        $( '#modal-loader' ).modal('hide');
      }, 'JSON');
      
      $( '#cbo-Estado' ).html( '' );
      for (var i = 0; i < 2; i++){
        selected = '';
        if(response.Nu_Estado == i)
          selected = 'selected="selected"';
        $( '#cbo-Estado' ).append( '<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Inactivo' : 'Activo') + '</option>' );
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
}

function form_Lista_Precio_Cabecera(){
  if ( accion_lista_precio_cabecera == 'add' || accion_lista_precio_cabecera == 'upd' ) {
    $( '#btn-save' ).text('');
    $( '#btn-save' ).attr('disabled', true);
    $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
  
    $( '#modal-loader' ).modal('show');
    
    url = base_url + 'Ventas/ReglasVenta/Lista_precio_controller/crudLista_Precio';
  	$.ajax({
      type		  : 'POST',
      dataType	: 'JSON',
  		url		    : url,
  		data		  : $('#form-Lista_Precio').serialize(),
  		success : function( response ){
  		  $( '#modal-loader' ).modal('hide');
  		  
  	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
    	  $( '#modal-message' ).modal('show');
  		  
  		  if (response.status == 'success'){
  		    accion_lista_precio_cabecera='';
  		    
          $( '#form-Lista_Precio' )[0].reset();
          $( '.div-AgregarEditar' ).hide();
          $( '.div-Listar' ).show();
    	    $( '.modal-message' ).addClass(response.style_modal);
    	    $( '.modal-title-message' ).text(response.message);
    	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
    	    reload_table_lista_precio_cabecera();
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
}

function eliminarLista_Precio(ID, accion_lista_precio_cabecera){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });
  
  $(document).bind('keydown', 'alt+l', function(){
    if ( accion_lista_precio_cabecera=='delete' ) {
      _eliminarLista_Precio($modal_delete, ID);
      accion_lista_precio_cabecera='';
    }
  });
  
  $( '#btn-save-delete' ).off('click').click(function () {
    _eliminarLista_Precio($modal_delete, ID);
  });
}

function reload_table_lista_precio_cabecera(){
  table_lista_precio_cabecera.ajax.reload(null,false);
}

function _eliminarLista_Precio($modal_delete, ID){
  $( '#modal-loader' ).modal('show');
    
  url = base_url + 'Ventas/ReglasVenta/Lista_precio_controller/eliminarLista_Precio/' + ID;
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
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
  	    reload_table_lista_precio_cabecera();
		  } else {
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
		  }
		  accion_lista_precio_cabecera='';
    },
    error: function (jqXHR, textStatus, errorThrown) {
		  accion_lista_precio_cabecera='';
		  
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

function add_lista_precio_producto(ID){
  accion_lista_precio_detalle='add';
  
  $( '[name="ID_Lista_Precio_Cabecera"]' ).focus();
  
  $( '.div-AgregarEditar' ).hide();
  $( '.div-Listar' ).hide();
  $( '.div-AgregarEditarPrecio' ).show();
  
  $( '#modal-loader' ).modal('show');
	
  $( '[name="EID_Lista_Precio_Detalle"]' ).val( '' );
  $( '[name="EID_Producto"]' ).val( '' );
  
  url = base_url + 'Ventas/ReglasVenta/Lista_precio_controller/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      //Para filtrar según lista de precio cabecera
      $( '#txt-ID_Lista_Precio_Cabecera' ).val(response.ID_Lista_Precio_Cabecera);
      reload_table_lista_precio_detalle();
      
      $( '[name="ID_Lista_Precio_Cabecera"]' ).val( response.ID_Lista_Precio_Cabecera );
      $( '#title-lista_precio_detalle' ).text( response.No_Lista_Precio );
      
      /*
      url = base_url + 'AutocompleteController/sendData';
      $.post( url, {sTabla : 'producto', iTipoSocio : 0}, function( responseProducto ){
        $( '#cbo-Productos' ).html( '<option value="" selected="selected">- Seleccionar -</option>');
        for (var i = 0; i < responseProducto.length; i++)
          $( '#cbo-Productos' ).append( '<option value="' + responseProducto[i].ID + '">' + responseProducto[i].Nombre + '</option>' );
        $( '#modal-loader' ).modal('hide');
      }, 'JSON');
      */

      $( '#modal-loader' ).modal('hide');
      
      $( '#cbo-Estado_Precio' ).html( '<option value="1">Activo</option>' );
      $( '#cbo-Estado_Precio' ).append( '<option value="0">Inactivo</option>' );
    }
  })
  
  $( '[name="Ss_Precio_Interno"]' ).on('input', function () {
    var numero = parseFloat(this.value);
    var fDescuento = parseFloat($( '[name="Po_Descuento"]' ).val());
    if (fDescuento > 0.00)
      $( '[name="Ss_Precio"]' ).val( numero - ((fDescuento * numero) / 100) );
  })
  
  $( '[name="Po_Descuento"]' ).on('input', function () {
    var numero = parseFloat(this.value);
    var fPrecioInterno = parseFloat($( '[name="Ss_Precio_Interno"]' ).val());
    if (fPrecioInterno > 0.00)
      $( '[name="Ss_Precio"]' ).val( fPrecioInterno - ((fPrecioInterno * numero) / 100) );
  })
}

function form_Lista_Precio_Detalle(){
  //if ( accion_lista_precio_detalle=='add') {
    $( '#btn-save_precio' ).text('');
    $( '#btn-save_precio' ).attr('disabled', true);
    $( '#btn-save_precio' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
  
    $( '#modal-loader' ).modal('show');
    
    url = base_url + 'Ventas/ReglasVenta/Lista_precio_controller/crudLista_Precio_Producto';
  	$.ajax({
      type		  : 'POST',
      dataType	: 'JSON',
  		url		    : url,
  		data		  : $('#form-Lista_Precio_Producto').serialize(),
  		success : function( response ){
  		  $( '#modal-loader' ).modal('hide');
  		  
  	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
    	  $( '#modal-message' ).modal('show');
  		  
  		  if (response.status == 'success'){
  		    accion_lista_precio_detalle='add';
  		    
          $( '#form-Lista_Precio_Producto' )[0].reset();
    	    $( '.modal-message' ).addClass(response.style_modal);
    	    $( '.modal-title-message' ).text(response.message);
    	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
    	    reload_table_lista_precio_cabecera();
    	    reload_table_lista_precio_detalle();
  		  } else {
    	    $( '.modal-message' ).addClass(response.style_modal);
    	    $( '.modal-title-message' ).text(response.message);
    	    setTimeout(function() {$('#modal-message').modal('hide');}, 1200);
  		  }
  		  
        $( '#btn-save_precio' ).text('');
        $( '#btn-save_precio' ).append( 'Guardar' );
        $( '#btn-save_precio' ).attr('disabled', false);
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
  //}
}

function verLista_Precio_Producto(ID){
  accion_lista_precio_detalle='upd';
     
  $( '#form-Lista_Precio_Producto_Editar' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
 
  $( '#modal-loader' ).modal('show');
 
  url = base_url + 'Ventas/ReglasVenta/Lista_precio_controller/ajax_edit_producto/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $( '#modal-Lista_Precio_Producto_Editar' ).modal('show');
      $( '.modal-title' ).text('Modifcar Precio');
  
    	$( '#modal-Lista_Precio_Producto_Editar' ).on('shown.bs.modal', function() {
        $( '[name="Ss_Precio_Editar"]' ).focus();
        $('[name="Ss_Precio_Editar"]').select();
    	})
    	
      $('[name="ID_Lista_Precio_Cabecera"]').val(response.ID_Lista_Precio_Cabecera);
      $('[name="ID_Lista_Precio_Detalle"]').val(response.ID_Lista_Precio_Detalle);
      $('[name="EID_Producto"]').val(response.ID_Producto);
      
      var selected;
      $('#item-lista_precio-editar-nombre').val(response.No_Producto);

      /*
      url = base_url + 'AutocompleteController/sendData';
      $.post( url, {sTabla : 'producto', iTipoSocio : 0}, function( responseProducto ){
        $( '#cbo-Productos_Editar' ).html( '' );
        for (var i = 0; i < responseProducto.length; i++) {
          selected = '';
          if(response.ID_Producto == responseProducto[i].ID)
            selected = 'selected="selected"';
          $( '#cbo-Productos_Editar' ).append( '<option value="' + responseProducto[i].ID + '" ' + selected + '>' + responseProducto[i].Nombre + '</option>' );
        }
      }, 'JSON');
      */
      
      //CSS
      $("#item-lista_precio-editar-nombre").css("background-color", "#d2d6de");
      $("#item-lista_precio-editar-nombre").css("pointer-events", "none");

      $('[name="Ss_Precio_Interno_Editar"]').val(response.Ss_Precio_Interno);
      $('[name="Po_Descuento_Editar"]').val(response.Po_Descuento);
      $('[name="Ss_Precio_Editar"]').val(response.Ss_Precio);
      
      $( '#cbo-Estado_Precio_Editar' ).html( '' );
      for (var i = 0; i < 2; i++){
        selected = '';
        if(response.Nu_Estado == i)
          selected = 'selected="selected"';
        $( '#cbo-Estado_Precio_Editar' ).append( '<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Inactivo' : 'Activo') + '</option>' );
      }

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
  
  $( '[name="Ss_Precio_Interno_Editar"]' ).on('input', function () {
    var numero = parseFloat(this.value);
    var fDescuento = parseFloat($( '[name="Po_Descuento_Editar"]' ).val());
    if (fDescuento > 0.00)
      $( '[name="Ss_Precio_Editar"]' ).val( numero - ((fDescuento * numero) / 100) );
  })
  
  $( '[name="Po_Descuento_Editar"]' ).on('input', function () {
    var numero = parseFloat(this.value);
    var fPrecioInterno = parseFloat($( '[name="Ss_Precio_Interno_Editar"]' ).val());
    if (fPrecioInterno > 0.00)
      $( '[name="Ss_Precio_Editar"]' ).val( fPrecioInterno - ((fPrecioInterno * numero) / 100) );
  })
}

function form_Lista_Precio_Detalle_Producto(){
  if ( accion_lista_precio_detalle=='upd' ) {
    $( '#btn-save_precio_editar' ).text('');
    $( '#btn-save_precio_editar' ).attr('disabled', true);
    $( '#btn-save_precio_editar' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
  
    $( '#modal-loader' ).modal('show');
    
    url = base_url + 'Ventas/ReglasVenta/Lista_precio_controller/crudLista_Precio_Producto_Update';
  	$.ajax({
      type		  : 'POST',
      dataType	: 'JSON',
  		url		    : url,
  		data		  : $('#form-Lista_Precio_Producto_Editar').serialize(),
  		success : function( response ){
  		  $( '#modal-loader' ).modal('hide');
  		  
  	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
    	  $( '#modal-message' ).modal('show');
  		  
  		  if (response.status == 'success'){
  		    accion_lista_precio_detalle='upd';
  		    
    		  $( '#modal-Lista_Precio_Producto_Editar' ).modal('hide');
    	    $( '.modal-message' ).addClass(response.style_modal);
    	    $( '.modal-title-message' ).text(response.message);
    	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
    	    reload_table_lista_precio_detalle();
  		  } else {
    	    $( '.modal-message' ).addClass(response.style_modal);
    	    $( '.modal-title-message' ).text(response.message);
    	    setTimeout(function() {$('#modal-message').modal('hide');}, 1200);
  		  }
  		  
        $( '#btn-save_precio_editar' ).text('');
        $( '#btn-save_precio_editar' ).append( '<span class="fa fa-save"></span> Guardar (ENTER)' );
        $( '#btn-save_precio_editar' ).attr('disabled', false);
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
}

function eliminarLista_Precio_Producto(ID, accion_lista_precio_detalle){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });
  
  $(document).bind('keydown', 'alt+l', function(){
    if ( accion_lista_precio_detalle=='delete' ) {
      _eliminarLista_Precio_Producto($modal_delete, ID);
      accion_lista_precio_detalle='';
    }
  });

  $( '#btn-save-delete' ).off('click').click(function () {
    _eliminarLista_Precio_Producto($modal_delete, ID);
  });
}

function reload_table_lista_precio_detalle(){
  table_lista_precio_detalle.ajax.reload(null,false);
}

function _eliminarLista_Precio_Producto($modal_delete, ID){
  $( '#modal-loader' ).modal('show');
    
  url = base_url + 'Ventas/ReglasVenta/Lista_precio_controller/eliminarLista_Precio_Producto/' + ID;
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
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
  	    reload_table_lista_precio_cabecera();
  	    reload_table_lista_precio_detalle();
		  } else {
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
		  }
		  accion_lista_precio_detalle='';
    },
    error: function (jqXHR, textStatus, errorThrown) {
		  accion_lista_precio_detalle='';
		  
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

function replicarListaPrecio(ID_Almacen,ID_Lista_Precio_Cabecera){
  $( '.modal-replicacion_precio' ).modal('show');

  //$('[name="Replicacion_Precio-ID_Lista_Precio_Cabecera"]').val(ID);

  $('#modal-replicacion-cbo-lista_precios_destino').html('<option value="0">- Sin registros -</option>');

  //CSS
  $("#modal_replicacion-cbo-almacen").css("background-color", "#d2d6de");
  $("#modal-replicacion-cbo-almacen").css("pointer-events", "none");

  $("#modal-replicacion-cbo-lista_precios").css("background-color", "#d2d6de");
  $("#modal-replicacion-cbo-lista_precios").css("pointer-events", "none");

  $('#modal_replicacion-cbo-almacen').html('<option value="0">- Seleccionar -</option>');
  url = base_url + 'HelperController/getAlmacenes';
  $.post(url, {}, function (responseAlmacen) {
    var iCantidadRegistros = responseAlmacen.length;
    $('#modal_replicacion-cbo-almacen').html('<option value="0">- Seleccionar -</option>');
    if (iCantidadRegistros == 1) {
      $('#modal_replicacion-cbo-almacen').append('<option value="' + responseAlmacen[0]['ID_Almacen'] + '" selected="selected">' + responseAlmacen[0]['No_Almacen'] + '</option>');
    } else {
      for (var i = 0; i < iCantidadRegistros; i++) {
        selected='';
        if (ID_Almacen == responseAlmacen[i]['ID_Almacen']) {
          selected = 'selected="selected"';
        }
        $('#modal_replicacion-cbo-almacen').append('<option value="' + responseAlmacen[i]['ID_Almacen'] + '" ' + selected + '>' + responseAlmacen[i]['No_Almacen'] + '</option>');
      }
    }
  }, 'JSON');

  $( '#modal-replicacion-cbo-lista_precios' ).html('');
  url = base_url + 'HelperController/getListaPrecio';
  $.post( url, {ID_Almacen : ID_Almacen}, function( responseLista ){
    $('#modal-replicacion-cbo-lista_precios').html('<option value="0">- Seleccionar -</option>');
    for (var i = 0; i < responseLista.length; i++) {
      selected='';
      if (ID_Lista_Precio_Cabecera == responseLista[i].ID_Lista_Precio_Cabecera) {
        selected = 'selected="selected"';
      }
      $( '#modal-replicacion-cbo-lista_precios' ).append( '<option value="' + responseLista[i].ID_Lista_Precio_Cabecera + '" ' + selected + '>' + responseLista[i].No_Lista_Precio + ' ' + (responseLista[i].Nu_Tipo_Lista_Precio == 1 ? 'Venta' : 'Compra') + '</option>' );
    }
  }, 'JSON');

  //externo
  $('#modal_replicacion-cbo-almacen_destino').html('<option value="0">- Seleccionar -</option>');
  url = base_url + 'HelperController/getOrganizacionesAlcenesEmpresaExternos';
  $.post(url, { 'iIdAlmacen': ID_Almacen }, function (response) {
    if (response.sStatus == 'success') {
      var response = response.arrData;
      for (var i = 0; i < response.length; i++) {
        $('#modal_replicacion-cbo-almacen_destino').append('<option value="' + response[i]['ID_Almacen'] + '">' + response[i]['No_Almacen'] + '</option>');
      }
    }
  }, 'JSON');
}