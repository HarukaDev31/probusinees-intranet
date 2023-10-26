var url;
var table_pais;
var accion_pais;

$(function () {
	$(document).keyup(function(event){
    if(event.which == 27){//ESC
      $( "#modal-Pais" ).modal('hide');
    }
	});
	
  url = base_url + 'Configuracion/PaisController/ajax_list';
  table_pais = $( '#table-Pais' ).DataTable({
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
        data.Filtros_Paises = $( '#cbo-Filtros_Paises' ).val(),
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
    table_pais.search($(this).val()).draw();
  });
  
  $( '#form-Pais' ).validate({
		rules:{
			No_Pais: {
				required: true
			},
			Nu_Codigo_Sunat_ISO: {
				required: true,
				minlength: 2,
				maxlength: 2
			},
			Nu_Codigo_Sunat_Pais:{
				required: true,
			},
		},
		messages:{
			No_Pais:{
				required: "Ingresar nombre",
			},
			Nu_Codigo_Sunat_ISO:{
				required: "Ingresar ISO",
				minlength: "Debe ingresar 2 dígitos",
				maxlength: "Debe ingresar 2 dígitos"
			},
			Nu_Codigo_Sunat_Pais:{
				required: "Ingresar código",
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
		submitHandler: form_Pais
	});
	
	$(document).bind('keydown', 'alt+a', function(){
    agregarPais();
  });
})

function agregarPais(){
  accion_pais='add_pais';
  
  $( '#form-Pais' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  
  $( '.help-block' ).empty();
  
  $( '#modal-Pais' ).modal('show');
  $( '.modal-title' ).text('Nuevo País');
  
	$( '#modal-Pais' ).on('shown.bs.modal', function() {
		$( '#txt-No_Pais' ).focus();
	})
  
  $( '[name="EID_Pais"]' ).val('');
  $( '[name="ENo_Pais"]' ).val('');
  
  $( '.div-Estado' ).hide();
  $( '#cbo-Estado' ).html( '<option value="1">Activo</option>' );
}

function verPais(ID){
  accion_pais='upd_pais';
  
  $( '#form-Pais' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '#modal-loader' ).modal('show');
 
  url = base_url + 'Configuracion/PaisController/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $( '#modal-loader' ).modal('hide');
      
      $( '#modal-Pais' ).modal('show');
      $( '.modal-title' ).text('Modifcar País');
  
    	$( '#modal-Pais' ).on('shown.bs.modal', function() {
    		$( '#txt-No_Pais' ).focus();
    	})
      
      $( '[name="EID_Pais"]' ).val(response.ID_Pais);
      $( '[name="ENo_Pais"]' ).val(response.No_Pais);
      
      $( '[name="No_Pais"]' ).val(response.No_Pais);
      $( '[name="Nu_Codigo_Sunat_Pais"]' ).val(response.Nu_Codigo_Sunat_Pais);
      $( '[name="Nu_Codigo_Sunat_ISO"]' ).val(response.Nu_Codigo_Sunat_ISO);
      
      $( '.div-Estado' ).show();
      $( '#cbo-Estado' ).html( '' );
      var selected='';
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

function form_Pais(){
  if ( accion_pais=='add_pais' || accion_pais=='upd_pais' ) {
    $( '#btn-save' ).text('');
    $( '#btn-save' ).attr('disabled', true);
    $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
  
    $( '#modal-loader' ).modal('show');
    
    url = base_url + 'Configuracion/PaisController/crudPais';
  	$.ajax({
      type		  : 'POST',
      dataType	: 'JSON',
  		url		    : url,
  		data		  : $('#form-Pais').serialize(),
  		success : function( response ){
  		  $( '#modal-loader' ).modal('hide');
  		  
  	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
    	  $( '#modal-message' ).modal('show');
  		  
  		  if (response.status=='success'){
    		  accion_pais='';
    		    
  		    $('#modal-Pais').modal('hide');
    	    $( '.modal-message' ).addClass(response.style_modal);
    	    $( '.modal-title-message' ).text(response.message);
    	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
    	    reload_table_pais();
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
}

function eliminarPais(ID, accion_pais){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });
  
  $(document).bind('keydown', 'alt+l', function(){
    if ( accion_pais=='delete' ) {
      _eliminarPais($modal_delete, ID);
      accion_pais='';
    }
  });

  $( '#btn-save-delete' ).off('click').click(function () {
    _eliminarPais($modal_delete, ID);
  });
}

function _eliminarPais($modal_delete, ID){
  $( '#modal-loader' ).modal('show');
  
  url = base_url + 'Configuracion/PaisController/eliminarPais/' + ID;
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
		    accion_pais='';
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
  	    reload_table_pais();
		  } else {
		    accion_pais='';
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
		  }
    },
    error: function (jqXHR, textStatus, errorThrown) {
		  accion_pais='';
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

function reload_table_pais(){
  table_pais.ajax.reload(null,false);
}