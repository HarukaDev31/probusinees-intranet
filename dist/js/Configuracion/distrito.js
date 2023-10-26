var url;
var table_distrito;
var accion_distrito;

$(function () {
	$(document).keyup(function(event){
    if(event.which==27){//ESC
      $( "#modal-Distrito" ).modal('hide');
    }
	});
	
  url = base_url + 'Configuracion/DistritoController/ajax_list';
  table_distrito = $( '#table-Distrito' ).DataTable({
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
      'dataType'  : 'JSON',
      'data'      : function ( data ) {
        data.Filtros_Distritos = $( '#cbo-Filtros_Distritos' ).val(),
        data.Global_Filter = $( '#txt-Global_Filter' ).val();
      },
    },
    'columnDefs': [{
      'className' : 'text-center',
      'targets'   : 'no-sort',
      'orderable' : false,
    },],
  });
  
  $( '.dataTables_length' ).addClass('col-md-3');
  $( '.dataTables_paginate' ).addClass('col-md-9');
  
  $( '#txt-Global_Filter' ).keyup(function() {
    table_distrito.search($(this).val()).draw();
  });
  
  $( '#form-Distrito' ).validate({
		rules:{
			ID_Pais: {
				required: true
			},
			ID_Departamento: {
				required: true
			},
			ID_Provincia: {
				required: true
			},
			No_Distrito: {
				required: true
			},
			No_Distrito_Breve: {
				minlength: 2,
				maxlength: 2
			},
		},
		messages:{
			ID_Pais:{
				required: "Seleccionar país",
			},
			ID_Departamento:{
				required: "Seleccionar departamento",
			},
			ID_Provincia:{
				required: "Seleccionar provincia",
			},
			No_Distrito:{
				required: "Ingresar nombre",
			},
			No_Distrito_Breve:{
				minlength: "Debe ingresar 2 dígitos",
				maxlength: "Debe ingresar 2 dígitos"
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
		submitHandler: form_Distrito
	});
	
	$( '#cbo-Paises' ).change(function(){
    $( '#modal-loader' ).modal('show');
	  $( '#cbo-Departamentos' ).html('');
	  if ( $(this).val() > 0 ) {
      url = base_url + 'HelperController/getDepartamentos';
      $.post( url, {ID_Pais : $(this).val()}, function( response ){
        $( '#modal-loader' ).modal('hide');
        $( '#cbo-Departamentos' ).html('<option value="" selected="selected">- Seleccionar -</option>');
        for (var i = 0; i < response.length; i++)
          $( '#cbo-Departamentos' ).append( '<option value="' + response[i].ID_Departamento + '">' + response[i].No_Departamento + '</option>' );
      }, 'JSON');
	  }
	})
	
	$( '#cbo-Departamentos' ).change(function(){
	  $( '#modal-loader' ).modal('show');
	  $( '#cbo-Provincias' ).html('');
	  if ( $(this).val() > 0 ) {
      url = base_url + 'HelperController/getProvincias';
      $.post( url, {ID_Departamento : $(this).val()}, function( response ){
        $( '#modal-loader' ).modal('hide');
        $( '#cbo-Provincias' ).html('<option value="" selected="selected">- Seleccionar -</option>');
        for (var i = 0; i < response.length; i++)
          $( '#cbo-Provincias' ).append( '<option value="' + response[i].ID_Provincia + '">' + response[i].No_Provincia + '</option>' );
      }, 'JSON');
	  }
	})
	
  $(document).bind('keydown', 'alt+a', function(){
    agregarDistrito();
  });
})

function agregarDistrito(){
  accion_distrito='add_distrito';
  $( '#form-Distrito' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  
  $( '.help-block' ).empty();
  
  $( '#modal-Distrito' ).modal('show');
  $( '#modal-loader' ).modal('show');
  
  $( '.modal-title' ).text('Nuevo Distrito');
  
  $('[name="EID_Provincia"]').val('');
  $('[name="EID_Distrito"]').val('');
  $('[name="ENo_Distrito"]').val('');
  
  $( '#cbo-Departamentos' ).html('');
  $( '#cbo-Provincias' ).html('');
  
  url = base_url + 'HelperDropshippingController/listarTodosPaises';
  $.post( url , function( response ){
    $( '#modal-loader' ).modal('hide');
    $( '#cbo-Paises' ).html('<option value="" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-Paises' ).append( '<option value="' + response[i].ID_Pais + '">' + response[i].No_Pais + '</option>' );
  }, 'JSON');
  
  $( '#cbo-habilitar_ecommerce' ).html( '<option value="0">No</option>' );
  $( '#cbo-habilitar_ecommerce' ).html( '<option value="1">Si</option>' );

  $( '.div-Estado' ).hide();
  $( '#cbo-Estado' ).html( '<option value="1">Activo</option>' );
  $( '#modal-loader' ).modal('hide');
}

function verDistrito(ID){
  accion_distrito='upd_distrito';
  
  $( '#form-Distrito' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '#modal-loader' ).modal('show');
 
  url = base_url + 'Configuracion/DistritoController/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $( '#modal-Distrito' ).modal('show');
      $( '.modal-title' ).text('Modificar Distrito');
      
      $( '[name="EID_Provincia"]' ).val(response.ID_Provincia);
      $( '[name="EID_Distrito"]' ).val(response.ID_Distrito);
      $( '[name="ENo_Distrito"]' ).val(response.No_Distrito);
      
      var selected='';
      url = base_url + 'HelperDropshippingController/listarTodosPaises';
      $.post( url , function( responsePais ){
        $( '#cbo-Paises' ).html('');
        for (var i = 0; i < responsePais.length; i++){
          selected = '';
          if(response.ID_Pais == responsePais[i].ID_Pais)
            selected = 'selected="selected"';
          $( '#cbo-Paises' ).append( '<option value="' + responsePais[i].ID_Pais + '" ' + selected + '>' + responsePais[i].No_Pais + '</option>' );
        }
      }, 'JSON');
      
      url = base_url + 'HelperController/getDepartamentos';
      $.post( url, {ID_Pais : response.ID_Pais}, function( responseDepartamentos ){
        $( '#cbo-Departamentos' ).html('');
        for (var i = 0; i < responseDepartamentos.length; i++){
          selected = '';
          if(response.ID_Departamento == responseDepartamentos[i].ID_Departamento)
            selected = 'selected="selected"';
          $( '#cbo-Departamentos' ).append( '<option value="' + responseDepartamentos[i].ID_Departamento + '" ' + selected + '>' + responseDepartamentos[i].No_Departamento + '</option>' );
        }
      }, 'JSON');
      
      url = base_url + 'HelperController/getProvincias';
      $.post( url, {ID_Departamento : response.ID_Departamento}, function( responseProvincia ){
        $( '#cbo-Provincias' ).html('');
        for (var i = 0; i < responseProvincia.length; i++){
          selected = '';
          if(response.ID_Provincia == responseProvincia[i].ID_Provincia)
            selected = 'selected="selected"';
          $( '#cbo-Provincias' ).append( '<option value="' + responseProvincia[i].ID_Provincia + '" ' + selected + '>' + responseProvincia[i].No_Provincia + '</option>' );
        }
      }, 'JSON');
      
      $( '[name="No_Distrito"]' ).val(response.No_Distrito);
      $( '[name="No_Distrito_Breve"]' ).val(response.No_Distrito_Breve);
      $( '[name="Ss_Delivery"]' ).val(response.Ss_Delivery);
      
      $( '#cbo-habilitar_ecommerce' ).html( '' );
      for (var i = 0; i < 2; i++){
        selected = '';
        if(response.Nu_Habilitar_Ecommerce == i)
          selected = 'selected="selected"';
        $( '#cbo-habilitar_ecommerce' ).append( '<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'No' : 'Si') + '</option>' );
      }

      $( '.div-Estado' ).show();
      $( '#cbo-Estado' ).html( '' );
      for (var i = 0; i < 2; i++){
        selected = '';
        if(response.Nu_Estado == i)
          selected = 'selected="selected"';
        $( '#cbo-Estado' ).append( '<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Inactivo' : 'Activo') + '</option>' );
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
}

function form_Distrito(){
  if ( accion_distrito=='add_distrito' || accion_distrito=='upd_distrito' ) {
    $( '#btn-save' ).text('');
    $( '#btn-save' ).attr('disabled', true);
    $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
  
    $( '#modal-loader' ).modal('show');
    
    url = base_url + 'Configuracion/DistritoController/crudDistrito';
  	$.ajax({
      type		  : 'POST',
      dataType	: 'JSON',
  		url		    : url,
  		data		  : $('#form-Distrito').serialize(),
  		success : function( response ){
  		  $( '#modal-loader' ).modal('hide');
  		  
  	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
    	  $( '#modal-message' ).modal('show');
  		  
  		  if (response.status == 'success'){
  		    accion_distrito='';
  		    $('#modal-Distrito').modal('hide');
    	    $( '.modal-message' ).addClass(response.style_modal);
    	    $( '.modal-title-message' ).text(response.message);
    	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
    	    reload_table_distrito();
  		  } else {
  		    $( '#txt-No_Distrito' ).val('');
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
}

function eliminarDistrito(ID, accion_distrito){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });
  
  $(document).bind('keydown', 'alt+l', function(){
    if ( accion_distrito=='delete' ) {
      _eliminarDistrito($modal_delete, ID);
      accion_distrito='';
    }
  });
  
  $( '#btn-save-delete' ).off('click').click(function () {
    _eliminarDistrito($modal_delete, ID);
  });
}

function _eliminarDistrito($modal_delete, ID){
  $( '#modal-loader' ).modal('show');
  
  url = base_url + 'Configuracion/DistritoController/eliminarDistrito/' + ID;
  $.ajax({
    url       : url,
    type      : "GET",
    dataType  : "JSON",
    success: function( response ){
      $( '#modal-loader' ).modal('hide');
      
      $modal_delete.modal('hide');
	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
  	  $( '#modal-message' ).modal('show');
		  
		  if (response.status=='success'){
		    accion_distrito='';
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
  	    reload_table_distrito();
		  } else {
		    accion_distrito='';
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
		  }
    },
    error: function (jqXHR, textStatus, errorThrown) {
		  accion_distrito='';
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

function reload_table_distrito(){
  table_distrito.ajax.reload(null,false);
}