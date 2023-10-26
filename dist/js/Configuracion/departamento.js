var url;
var table_departamento;
var accion_departamento;

$(function () {
	$(document).keyup(function(event){
    if(event.which==27){//ESC
      $( "#modal-Departamento" ).modal('hide');
    }
	});
	
  url = base_url + 'Configuracion/DepartamentoController/ajax_list';
  table_departamento = $( '#table-Departamento' ).DataTable({
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
        data.Filtros_Departamentos = $( '#cbo-Filtros_Departamentos' ).val(),
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
    table_departamento.search($(this).val()).draw();
  });
  
  $( '#form-Departamento' ).validate({
		rules:{
			No_Departamento: {
				required: true
			},
			No_Departamento_Breve: {
				minlength: 2,
				maxlength: 2
			},
		},
		messages:{
			No_Departamento:{
				required: "Ingresar nombre",
			},
			No_Departamento_Breve:{
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
		submitHandler: form_Departamento
	});
	
	$(document).bind('keydown', 'alt+a', function(){
    agregarDepartamento();
  });
})

function agregarDepartamento(){
  accion_departamento='add_departamento';
  
  $( '#form-Departamento' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  
  $( '.help-block' ).empty();
  
  $( '#modal-Departamento' ).modal('show');
  $( '.modal-title' ).text('Nuevo Departamento');
  
  $( '#modal-Departamento' ).on('shown.bs.modal', function() {
    $( '#txt-No_Departamento' ).focus();
  })
  
  $( '[name="EID_Pais"]' ).val('');
  $( '[name="EID_Departamento"]' ).val('');
  $( '[name="ENo_Departamento"]' ).val('');
  
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

function verDepartamento(ID){
  accion_departamento='upd_departamento';
  
  $( '#form-Departamento' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '#modal-loader' ).modal('show');
 
  url = base_url + 'Configuracion/DepartamentoController/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $( '#modal-loader' ).modal('hide');
      
      $( '#modal-Departamento' ).modal('show');
      $( '.modal-title' ).text('Modifcar Departamento');
  
    	$( '#modal-Departamento' ).on('shown.bs.modal', function() {
    		$( '#txt-No_Departamento' ).focus();
    	})
      
      $( '[name="EID_Pais"]' ).val(response.ID_Pais);
      $( '[name="EID_Departamento"]' ).val(response.ID_Departamento);
      $( '[name="ENo_Departamento"]' ).val(response.No_Departamento);
      
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
      
      $('[name="No_Departamento"]').val(response.No_Departamento);
      $('[name="No_Departamento_Breve"]').val(response.No_Departamento_Breve);
      
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

function form_Departamento(){
  if ( accion_departamento=='add_departamento' || accion_departamento=='upd_departamento' ) {
    if ( $( '#cbo-Paises' ).val() == 0){
      $( '#cbo-Paises' ).closest('.form-group').find('.help-block').html('Seleccionar pais');
      $( '#cbo-Paises' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      $( '#btn-save' ).text('');
      $( '#btn-save' ).attr('disabled', true);
      $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
    
      $( '#modal-loader' ).modal('show');
      
      url = base_url + 'Configuracion/DepartamentoController/crudDepartamento';
    	$.ajax({
        type		  : 'POST',
        dataType	: 'JSON',
    		url		    : url,
    		data		  : $('#form-Departamento').serialize(),
    		success : function( response ){
    		  $( '#modal-loader' ).modal('hide');
    		  
    	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
      	  $( '#modal-message' ).modal('show');
    		  
    		  if (response.status=='success'){
    		    accion_departamento='';
    		    $( '#modal-Departamento' ).modal('hide');
      	    $( '.modal-message' ).addClass(response.style_modal);
      	    $( '.modal-title-message' ).text(response.message);
      	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
      	    reload_table_departamento();
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

function eliminarDepartamento(ID, accion_departamento){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });
  
  $(document).bind('keydown', 'alt+l', function(){
    if ( accion_departamento=='delete' ) {
      _eliminarDepartamento($modal_delete, ID);
      accion_departamento='';
    }
  });
  
  $( '#btn-save-delete' ).off('click').click(function () {
    _eliminarDepartamento($modal_delete, ID);
  });
}
function _eliminarDepartamento($modal_delete, ID){
  $( '#modal-loader' ).modal('show');
  url = base_url + 'Configuracion/DepartamentoController/eliminarDepartamento/' + ID;
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
		    accion_departamento='';
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
  	    reload_table_departamento();
		  } else {
		    accion_departamento='';
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
		  }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      accion_departamento='';
		    
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

function reload_table_departamento(){
  table_departamento.ajax.reload(null,false);
}