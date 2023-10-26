var url;
var table_provincia;
var accion_provincia;

$(function () {
	$(document).keyup(function(event){
    if(event.which==27){//ESC
      $( "#modal-Provincia" ).modal('hide');
    }
	});
	
  url = base_url + 'Configuracion/ProvinciaController/ajax_list';
  table_provincia = $( '#table-Provincia' ).DataTable({
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
      'url'     : url,
      'type'      : 'POST',
      'dataType'  : 'JSON',
      'data'      : function ( data ) {
        data.Filtros_Provincias = $( '#cbo-Filtros_Provincias' ).val(),
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
    table_provincia.search($(this).val()).draw();
  });
  
  $( '#form-Provincia' ).validate({
		rules:{
			ID_Departamento: {
				required: true
			},
			No_Provincia: {
				required: true,
			},
			No_Provincia_Breve: {
				minlength: 2,
				maxlength: 2
			},
		},
		messages:{
			ID_Departamento:{
				required: "Seleccionar departamento",
			},
			No_Provincia:{
				required: "Ingresar nombre",
			},
			No_Provincia_Breve:{
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
		submitHandler: form_Provincia
	});
	
	$( '#cbo-Paises' ).change(function(){
	  $( '#cbo-Departamentos' ).html('');
	  if ( $(this).val() > 0 ) {
      url = base_url + 'HelperController/getDepartamentos';
      $.post( url, {ID_Pais : $(this).val()}, function( response ){
        $( '#cbo-Departamentos' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
        for (var i = 0; i < response.length; i++)
          $( '#cbo-Departamentos' ).append( '<option value="' + response[i].ID_Departamento + '">' + response[i].No_Departamento + '</option>' );
      }, 'JSON');
	  }
	})
	
  $(document).bind('keydown', 'alt+a', function(){
    agregarProvincia();
  });
})

function agregarProvincia(){
  accion_provincia='add_provincia';
  
  $( '#form-Provincia' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  
  $( '.help-block' ).empty();
  
  $( '#modal-Provincia' ).modal('show');
  $( '.modal-title' ).text('Nueva Provincia');
  
  $( '#modal-Provincia' ).on('shown.bs.modal', function() {
    $( '#txt-No_Provincia' ).focus();
  })
  
  $( '[name="EID_Departamento"]' ).val('');
  $( '[name="EID_Provincia"]' ).val('');
  $( '[name="ENo_Provincia"]' ).val('');
  
  $( '#cbo-Departamentos' ).html('');
  
  url = base_url + 'HelperController/getPaises';
  $.post( url , function( response ){
    $( '#modal-loader' ).modal('hide');
    $( '#cbo-Paises' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-Paises' ).append( '<option value="' + response[i].ID_Pais + '">' + response[i].No_Pais + '</option>' );
  }, 'JSON');
  
  $( '.div-Estado' ).hide();
  $( '#cbo-Estado' ).html( '<option value="1">Activo</option>' );
}

function verProvincia(ID){
  accion_provincia='upd_provincia';
  
  $( '#form-Provincia' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '#modal-loader' ).modal('show');
 
  url = base_url + 'Configuracion/ProvinciaController/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $( '#modal-loader' ).modal('hide');
      
      $( '#modal-Provincia' ).modal('show');
      $( '.modal-title' ).text('Modifcar Provincia');
  
      $( '#modal-Provincia' ).on('shown.bs.modal', function() {
        $( '#txt-No_Provincia' ).focus();
      })
      
      $( '[name="EID_Departamento"]' ).val(response.ID_Departamento);
      $( '[name="EID_Provincia"]' ).val(response.ID_Provincia);
      $( '[name="ENo_Provincia"]' ).val(response.No_Provincia);
      
      var selected='';
      url = base_url + 'HelperController/getPaises';
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
      
      $('[name="No_Provincia"]').val(response.No_Provincia);
      $('[name="No_Provincia_Breve"]').val(response.No_Provincia_Breve);
      
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

function form_Provincia(){
  if ( accion_provincia=='add_provincia' || accion_provincia=='upd_provincia' ) {
    if ( $( '#cbo-Departamentos' ).val() == 0){
      $( '#cbo-Departamentos' ).closest('.form-group').find('.help-block').html('Seleccionar departamento');
      $( '#cbo-Departamentos' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      $( '#btn-save' ).text('');
      $( '#btn-save' ).attr('disabled', true);
      $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
    
      $( '#modal-loader' ).modal('show');
      
      url = base_url + 'Configuracion/ProvinciaController/crudProvincia';
    	$.ajax({
        type		  : 'POST',
        dataType	: 'JSON',
    		url		    : url,
    		data		  : $('#form-Provincia').serialize(),
    		success : function( response ){
    		  $( '#modal-loader' ).modal('hide');
    		  
    	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
      	  $( '#modal-message' ).modal('show');
    		  
    		  if (response.status == 'success'){
    		    accion_provincia='';
    		    $( '#modal-Provincia' ).modal('hide');
      	    $( '.modal-message' ).addClass(response.style_modal);
      	    $( '.modal-title-message' ).text(response.message);
      	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
      	    reload_table_provincia();
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
}

function eliminarProvincia(ID, accion_provincia){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });
  
  $(document).bind('keydown', 'alt+l', function(){
    if ( accion_provincia=='delete' ) {
      _eliminarProvincia($modal_delete, ID);
      accion_provincia='';
    }
  });
  
  $( '#btn-save-delete' ).off('click').click(function () {
    _eliminarProvincia($modal_delete, ID);
  });
}

function _eliminarProvincia($modal_delete, ID){
  $( '#modal-loader' ).modal('show');
  
  url = base_url + 'Configuracion/ProvinciaController/eliminarProvincia/' + ID;
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
		    accion_provincia='';
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
  	    reload_table_provincia();
		  } else {
		    accion_provincia='';
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
		  }
    },
    error: function (jqXHR, textStatus, errorThrown) {
		  accion_provincia='';
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

function reload_table_provincia(){
  table_provincia.ajax.reload(null,false);
}