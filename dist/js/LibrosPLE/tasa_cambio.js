var url;
var table_tasa_cambio;
var accion_tasa_cambio = '';

$(function () {
  $('[data-mask]').inputmask();
  
  $( '#cbo-FiltroMonedas' ).html( '<option value="" selected="selected">- Todos -</option>' );
  url = base_url + 'HelperController/getMonedas';
  $.post( url , function( response ){
    $( '#cbo-FiltroMonedas' ).html( '<option value="" selected="selected">- Todos -</option>' );
    for (var i = 0; i < response.length; i++)
      $( '#cbo-FiltroMonedas' ).append( '<option value="' + response[i].ID_Moneda + '">' + response[i].No_Moneda + '</option>' );
  }, 'JSON');
  
  $( '#btn-cloud-api_tasa_cambio' ).click(function() {
    if ( $( '#cbo-FiltroMonedas' ).val() == '' ) {
      $( '#cbo-FiltroMonedas' ).closest('.form-group').find('.help-block').html('Seleccionar moneda');
	    $( '#cbo-FiltroMonedas' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
		  $( '.help-block' ).empty();
		  
      var arrData = {
        fe_inicio : ParseDateString($( '#txt-Filtro_Fe_Inicio' ).val(), 1, '/'),
        fe_fin    : ParseDateString($( '#txt-Filtro_Fe_Fin' ).val(), 1, '/')
      };
      
      var url_api = 'https://www.laesystems.com/librerias/Sunat_Tasa_Cambio/exchangerate/format/json/x-api-key/';
      var url_api = url_api + sTokenGlobal;
      
      $( '#btn-cloud-api_tasa_cambio' ).text('');
      $( '#btn-cloud-api_tasa_cambio' ).attr('disabled', true);
      $( '#btn-cloud-api_tasa_cambio' ).append( '<i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
      
      $.ajax({
        url   : url_api,
        type  :'POST',
        data  : arrData,
        success: function(response){
          url = '';
          if (response.success){
            url = base_url + 'LibrosPLE/TasaCambioController/save_exchange_rate';
            
            var arrParams = {
              iIdEmpresa : $( '#cbo-filtro_empresa' ).val(),
              ID_Moneda : $( '#cbo-FiltroMonedas' ).val(),
              arrData : response.data,
            };
            $.post( url, arrParams, function( response ) {
        	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
          	  $( '#modal-message' ).modal('show');
        		  
        		  if (response.status == 'success'){
          	    $( '.modal-message' ).addClass(response.style_modal);
          	    $( '.modal-title-message' ).text(response.message);
          	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
          	    reload_table_tasa_cambio();
        		  } else {
          	    $( '.modal-message' ).addClass(response.style_modal);
          	    $( '.modal-title-message' ).text(response.message);
          	    setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
        		  }
            }, "json");
          } else {
            $( '#btn-cloud-api_tasa_cambio' ).closest('.form-group').find('.help-block').html( response.msg );
      	    $( '#btn-cloud-api_tasa_cambio' ).closest('.form-group').removeClass('has-success').addClass('has-error');
          }
          
          $( '#btn-cloud-api_tasa_cambio' ).text('');
          $( '#btn-cloud-api_tasa_cambio' ).attr('disabled', false);
          $( '#btn-cloud-api_tasa_cambio' ).append( '<i class="fa fa-cloud-download fa-lg"></i>' );
        },
        error: function(response){
          $( '#btn-cloud-api_tasa_cambio' ).closest('.form-group').find('.help-block').html('Sin acceso');
      	  $( '#btn-cloud-api_tasa_cambio' ).closest('.form-group').removeClass('has-success').addClass('has-error');
      	  
          $( '#btn-cloud-api_tasa_cambio' ).text('');
          $( '#btn-cloud-api_tasa_cambio' ).attr('disabled', false);
          $( '#btn-cloud-api_tasa_cambio' ).append( '<i class="fa fa-cloud-download fa-lg"></i>' );
        }
      });
    }
  });
    
  url = base_url + 'LibrosPLE/TasaCambioController/ajax_list';
  table_tasa_cambio = $( '#table-TasaCambio' ).DataTable({
    'dom'       : 'B<"top">frt<"bottom"lp><"clear">',
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
    'autoWidth'   : false,
    'pagingType'  : 'full_numbers',
    'oLanguage' : {
      'sInfo'              : 'Mostrando (_START_ - _END_) total de registros _TOTAL_',
      'sLengthMenu'        : '_MENU_',
      'sSearch'            : 'Buscar por: ',
      'sSearchPlaceholder' : 'UPC / Nombre',
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
      'url'     : url,
      'type'      : 'POST',
      'dataType'  : 'JSON',
      'data'      : function ( data ) {
        data.filtro_empresa = $( '#cbo-filtro_empresa' ).val(),
        data.Filtro_Moneda = $( '#cbo-FiltroMonedas' ).val(),
        data.Filtro_Fe_Inicio = ParseDateString($( '#txt-Filtro_Fe_Inicio' ).val(), 1, '/'),
        data.Filtro_Fe_Fin = ParseDateString($( '#txt-Filtro_Fe_Fin' ).val(), 1, '/');
      },
    },
    'columnDefs': [{
      'className'  : 'text-center',
      'targets'    : 'no-sort',
      'orderable'  : false,
    },],
    'lengthMenu': [[10, 100, 1000, -1], [10, 100, 1000, "Todos"]],
  });
  
  $( '.dataTables_length' ).addClass('col-md-3');
  $( '.dataTables_paginate' ).addClass('col-md-9');

  $( '#btn-filter' ).click(function(){
    table_tasa_cambio.ajax.reload();
  });
  
  $( '#form-TasaCambio' ).validate({
		rules:{
			Fe_Ingreso: {
				required: true
			},
			Ss_Venta_Oficial: {
				required: true
			},
			Ss_Compra_Oficial:{
				required: true,
			},
		},
		messages:{
			Fe_Ingreso:{
				required: "Ingresar fecha",
			},
			Ss_Venta_Oficial:{
				required: "Ingresar t.c venta",
			},
			Ss_Compra_Oficial:{
				required: "Ingresar t.c compra",
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
		submitHandler: form_TasaCambio
	});
  
  $( '#cbo-filtro_empresa' ).html('<option value="" selected="selected">- Todas -</option>');

  url = base_url + 'HelperController/getEmpresas';
  $.post( url , function( response ){
    $( '#cbo-filtro_empresa' ).html('<option value="" selected="selected">- Todas -</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-filtro_empresa' ).append( '<option value="' + response[i].ID_Empresa + '">' + response[i].No_Empresa + '</option>' );
  }, 'JSON');
  
	$( '#cbo-filtro_empresa' ).change(function(){
    table_tasa_cambio.search($(this).val()).draw();
  });
	
  $(document).bind('keydown', 'f2', function(){
    agregarTasaCambio();
  });
})

function agregarTasaCambio(){
  accion_tasa_cambio = 'add_tasa_cambio';
  $( '#form-TasaCambio' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  
  $( '.help-block' ).empty();
  
  $( '#modal-TasaCambio' ).modal('show');
  
  $( '.modal-title' ).text('Nueva Tasa Cambio');
  
  $( '[name="EID_Empresa"]' ).val('');
  $( '[name="EID_Tasa_Cambio"]' ).val('');
  $( '[name="EID_Moneda"]' ).val('');
  $( '[name="EFe_Ingreso"]' ).val('');
  
  $( '#modal-loader' ).modal('show');
  url = base_url + 'HelperController/getEmpresas';
  $.post( url , function( response ){
    $( '#cbo-Empresas' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-Empresas' ).append( '<option value="' + response[i].ID_Empresa + '">' + response[i].No_Empresa + '</option>' );
    $( '#modal-loader' ).modal('hide');
  }, 'JSON');

  url = base_url + 'HelperController/getMonedas';
  $.post( url , function( response ){
    $( '#cbo-Monedas' ).html('');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-Monedas' ).append( '<option value="' + response[i].ID_Moneda + '">' + response[i].No_Moneda + '</option>' );
  }, 'JSON');
	
  $( '.date-picker-invoice' ).val(fDay + '/' + fMonth + '/' + fYear);
}

function verTasaCambio(ID){
  accion_tasa_cambio = 'upd_tasa_cambio';
  $( '#form-TasaCambio' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '#modal-loader' ).modal('show');
 
  url = base_url + 'LibrosPLE/TasaCambioController/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function( response ){
      $( '#modal-loader' ).modal('hide');
      
      $( '#modal-TasaCambio' ).modal('show');
      $( '.modal-title' ).text('Modifcar Tasa Cambio');
      
      $( '[name="EID_Empresa"]' ).val( response.ID_Empresa );
      $( '[name="EID_Tasa_Cambio"]' ).val( response.ID_Tasa_Cambio );
      $( '[name="EID_Moneda"]' ).val( response.ID_Moneda );
      $( '[name="EFe_Ingreso"]' ).val( response.Fe_Ingreso );
      
      var selected;
      url = base_url + 'HelperController/getEmpresas';
      $.post( url , function( responseEmpresa ){
        $( '#cbo-Empresas' ).html('');
        for (var i = 0; i < responseEmpresa.length; i++){
          selected = '';
          if(response.ID_Empresa == responseEmpresa[i].ID_Empresa)
            selected = 'selected="selected"';
          $( '#cbo-Empresas' ).append( '<option value="' + responseEmpresa[i].ID_Empresa + '" ' + selected + '>' + responseEmpresa[i].No_Empresa + '</option>' );
        }
      }, 'JSON');

      url = base_url + 'HelperController/getMonedas';
      $.post( url , function( responseMoneda ){
        $( '#cbo-Monedas' ).html('');
        selected = '';
        for (var i = 0; i < responseMoneda.length; i++){
           selected = '';
          if (response.ID_Moneda == responseMoneda[i].ID_Moneda)
            selected = 'selected="selected"';
          $( '#cbo-Monedas' ).append( '<option value="' + responseMoneda[i].ID_Moneda + '" ' + selected + '>' + responseMoneda[i].No_Moneda + '</option>' );
        }
      }, 'JSON');
	
      //$( '[name="Fe_Ingreso"]' ).val(response.Fe_Ingreso);
      $('[name="Fe_Ingreso"]').val(ParseDateString(response.Fe_Ingreso, 6, '-'));
      $( '[name="Ss_Compra_Oficial"]' ).val(response.Ss_Compra_Oficial);
      $( '[name="Ss_Venta_Oficial"]' ).val(response.Ss_Venta_Oficial);
      
      //$( '.date-picker-invoice' ).val(fDay + '/' + fMonth + '/' + fYear);
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

function form_TasaCambio(){
  if (accion_tasa_cambio == 'add_tasa_cambio' || accion_tasa_cambio == 'upd_tasa_cambio') {
    $( '#btn-save' ).text('');
    $( '#btn-save' ).attr('disabled', true);
    $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
  
    $( '#modal-loader' ).modal('show');
    
    url = base_url + 'LibrosPLE/TasaCambioController/crudTasaCambio';
  	$.ajax({
      type		  : 'POST',
      dataType	: 'JSON',
  		url		    : url,
  		data		  : $('#form-TasaCambio').serialize(),
  		success : function( response ){
  		  $( '#modal-loader' ).modal('hide');
  		  
  	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
    	  $( '#modal-message' ).modal('show');
  		  
  		  if (response.status == 'success'){
		      accion_tasa_cambio='';
		    
  		    $('#modal-TasaCambio').modal('hide');
    	    $( '.modal-message' ).addClass(response.style_modal);
    	    $( '.modal-title-message' ).text(response.message);
    	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
    	    reload_table_tasa_cambio();
  		  } else {
    	    $( '.modal-message' ).addClass(response.style_modal);
    	    $( '.modal-title-message' ).text(response.message);
    	    setTimeout(function() {$('#modal-message').modal('hide');}, 1200);
  		  }
  		  
        $( '#btn-save' ).text('');
        $( '#btn-save' ).append( '<span class="fa fa-save"></span> Guardar' );
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
  }// /. Validacion guardar / upd con tecla ENTER
}

function eliminarTasaCambio(ID, accion_tasa_cambio){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });
  
  $(document).bind('keydown', 'esc', function(){
    if ( accion_tasa_cambio=='delete' ) {
      _eliminarTasaCambio($modal_delete, ID);
      accion_tasa_cambio='';
    }
  });

  $( '#btn-save-delete' ).off('click').click(function () {
    _eliminarTasaCambio($modal_delete, ID);
  });
}

function _eliminarTasaCambio($modal_delete, ID){
  $( '#modal-loader' ).modal('show');
  
  url = base_url + 'LibrosPLE/TasaCambioController/eliminarTasaCambio/' + ID;
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
  	    reload_table_tasa_cambio();
		  } else {
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
		  }
		  accion_tasa_cambio='';
    },
    error: function (jqXHR, textStatus, errorThrown) {
      accion_tasa_cambio='';
      
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

function reload_table_tasa_cambio(){
  table_tasa_cambio.ajax.reload(null,false);
}