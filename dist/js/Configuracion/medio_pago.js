var url;
var table_medio_pago;

$(function () {
  $('.select2').select2();
  
	$(document).keyup(function(event){
    if(event.which == 27){//ESC
      $( "#modal-MedioPago" ).modal('hide');
    }
  });

  url = base_url + 'Configuracion/MedioPagoController/ajax_list';
  table_medio_pago = $( '#table-MedioPago' ).DataTable({
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
      'url'       : url,
      'type'      : 'POST',
      'dataType'  : 'JSON',
      'data'      : function ( data ) {
        data.filtro_empresa = $( '#cbo-filtro_empresa' ).val(),
        data.Filtros_MedioPago = $( '#cbo-Filtros_MedioPago' ).val(),
        data.Global_Filter = $( '#txt-Global_Filter' ).val();
      },
    },
    'columnDefs': [{
      'className' : 'text-center',
      'targets'   : 'no-sort',
      'orderable' : false,
    },{
      'className': 'text-left',
      'targets': 'no-sort_left',
      'orderable': false,
    },],
    'lengthMenu': [[10, 100, 1000, -1], [10, 100, 1000, "Todos"]],
  });
    
  $( '.dataTables_length' ).addClass('col-md-3');
  $( '.dataTables_paginate' ).addClass('col-md-9');

  $( '#txt-Global_Filter' ).keyup(function() {
    table_medio_pago.search($(this).val()).draw();
  });
  
  $( '#form-MedioPago' ).validate({
		rules:{
			No_Medio_Pago: {
				required: true
			},
			No_Signo: {
				required: true
			},
			Nu_Sunat_Codigo:{
				required: true,
			},
			Nu_Valor_FE:{
				required: true,
			},
		},
		messages:{
			No_Medio_Pago:{
				required: "Ingresar nombre",
			},
			No_Signo:{
				required: "Ingresar signo",
			},
			Nu_Sunat_Codigo:{
				required: "Ingresar codigo",
			},
			Nu_Valor_FE:{
				required: "Ingresar del 1 al 3",
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
		submitHandler: form_MedioPago
  });
  
  $( '#cbo-filtro_empresa' ).html('<option value="0" selected="selected">- Todas -</option>');

  url = base_url + 'HelperController/getEmpresas';
  $.post( url , function( response ){
    $( '#cbo-filtro_empresa' ).html('<option value="0" selected="selected">- Todas -</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-filtro_empresa' ).append( '<option value="' + response[i].ID_Empresa + '">' + response[i].No_Empresa + '</option>' );
  }, 'JSON');
  
	$( '#cbo-filtro_empresa' ).change(function(){
    table_medio_pago.search($(this).val()).draw();
  });
})

function agregarMedioPago(){
  $( '#form-MedioPago' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  
  $( '.help-block' ).empty();
  
  $( '#modal-MedioPago' ).modal('show');
  
  $( '.modal-title' ).text('Nuevo Medio Pago');
  
  $( '[name="EID_Empresa"]' ).val('');
  $( '[name="EID_Medio_Pago"]' ).val('');
  $( '[name="ENo_Medio_Pago"]' ).val('');

  $( '#modal-loader' ).modal('show');
  url = base_url + 'HelperController/getEmpresas';
  $.post( url , function( response ){
    $( '#cbo-Empresas' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-Empresas' ).append( '<option value="' + response[i].ID_Empresa + '">' + response[i].No_Empresa + '</option>' );
    $( '#modal-loader' ).modal('hide');
  }, 'JSON');

  $( '#cbo-dinero_caja_pv' ).html( '<option value="0">Físico</option>' );
  $( '#cbo-dinero_caja_pv' ).append( '<option value="1">Virtual</option>' );

  $('#cbo-tipo_vista').html('<option value="0">Efectivo</option>');
  $('#cbo-tipo_vista').append('<option value="1">Crédito</option>');
  $('#cbo-tipo_vista').append('<option value="2">Tarjeta / Depósito Bancario</option>');

  $( '.div-Estado' ).hide();
  $( '#cbo-Estado' ).html( '<option value="1">Activo</option>' );
}

function verMedioPago(ID){
  $( '#form-MedioPago' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '#modal-loader' ).modal('show');
 
  url = base_url + 'Configuracion/MedioPagoController/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $( '#modal-loader' ).modal('hide');
      
      $( '#modal-MedioPago' ).modal('show');
      $( '.modal-title' ).text('Modifcar Medio Pago');
      
      $( '[name="EID_Empresa"]' ).val(response.ID_Empresa);
      $( '[name="EID_Medio_Pago"]' ).val(response.ID_Medio_Pago);
      $( '[name="ENo_Medio_Pago"]' ).val(response.No_Medio_Pago);
      
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
      
      $( '[name="No_Medio_Pago"]' ).val(response.No_Medio_Pago);
      $( '[name="Txt_Medio_Pago"]' ).val(response.Txt_Medio_Pago);
      $( '[name="No_Codigo_Sunat_FE"]' ).val(response.No_Codigo_Sunat_FE);
      $( '[name="No_Codigo_Sunat_PLE"]' ).val(response.No_Codigo_Sunat_PLE);
      
      var selected;
      $( '#cbo-dinero_caja_pv' ).html( '' );
      for (var i = 0; i < 2; i++){
        selected = '';
        if(response.Nu_Tipo_Caja == i)
          selected = 'selected="selected"';
        $('#cbo-dinero_caja_pv').append('<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Físico' : 'Virtual') + '</option>' );
      }

      $( '[name="Nu_Orden"]' ).val(response.Nu_Orden);

      var selected_efectivo = '', selected_credito = '', selected_tarjetaydeposito = '';
      if (response.Nu_Tipo == 0)
        selected_efectivo = 'selected="selected"';
      if (response.Nu_Tipo == 1)
        selected_credito = 'selected="selected"';
      if (response.Nu_Tipo == 2)
        selected_tarjetaydeposito = 'selected="selected"';

      $('#cbo-tipo_vista').html('<option value="0" ' + selected_efectivo + '>Efectivo</option>');
      $('#cbo-tipo_vista').append('<option value="1" ' + selected_credito + '>Crédito</option>');
      $('#cbo-tipo_vista').append('<option value="2" ' + selected_tarjetaydeposito + '>Tarjeta / Depósito Bancario</option>');

      $( '.div-Estado' ).show();
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
  });
}

function form_MedioPago(){
  $( '#btn-save' ).text('');
  $( '#btn-save' ).attr('disabled', true);
  $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

  $( '#modal-loader' ).modal('show');
  
  url = base_url + 'Configuracion/MedioPagoController/crudMedioPago';
	$.ajax({
    type		  : 'POST',
    dataType	: 'JSON',
		url		    : url,
		data		  : $('#form-MedioPago').serialize(),
		success : function( response ){
		  $( '#modal-loader' ).modal('hide');
		  
	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
  	  $( '#modal-message' ).modal('show');
		  
		  if (response.status == 'success'){
		    $('#modal-MedioPago').modal('hide');
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
  	    reload_table_medio_pago();
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
}

function eliminarMedioPago(ID){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });
  
  $( '#btn-save-delete' ).off('click').click(function () {
    $( '#modal-loader' ).modal('show');
    
    url = base_url + 'Configuracion/MedioPagoController/eliminarMedioPago/' + ID;
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
    	    reload_table_medio_pago();
  		  } else {
    	    $( '.modal-message' ).addClass(response.style_modal);
    	    $( '.modal-title-message' ).text(response.message);
    	    setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
  		  }
      },
      error: function (jqXHR, textStatus, errorThrown) {
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

function reload_table_medio_pago(){
  table_medio_pago.ajax.reload(null,false);
}