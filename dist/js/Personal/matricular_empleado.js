var url;
var table_matricula_empleado;
var acccion_matricula_empleado;

$(function () {
  $('.select2').select2();
  $('[data-mask]').inputmask();

	//Personal
	url = base_url + 'HelperController/getDataGeneral';
	$.post( url, {sTipoData : 'entidad', iTipoEntidad : 4}, function( response ){
    if ( response.sStatus == 'success' ) {
      var l = response.arrData.length;
      if (l==1) {
        $( '#cbo-filtro_empleados' ).html( '<option value="' + response.arrData[0].ID + '">' + response.arrData[0].Nombre + '</option>' );
      } else {
        $( '#cbo-filtro_empleados' ).html('<option value="" selected="selected">- Todos -</option>');
        for (var x = 0; x < l; x++) {
          $( '#cbo-filtro_empleados' ).append( '<option value="' + response.arrData[x].ID + '">' + response.arrData[x].Nombre + '</option>' );
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
	}, 'JSON');
	
  url = base_url + 'Personal/Matricular_empleado_controller/ajax_list';
  table_matricula_empleado = $( '#table-Matricula_Empleado' ).DataTable({
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
        'dataType'  : 'json',
        'data'      : function ( data ) {
          data.Filtro_Empleado = $( '#cbo-filtro_empleados' ).val(),
          data.Filtro_Fe_Inicio = ParseDateString($( '#txt-Filtro_Fe_Inicio' ).val(), 1, '/'),
          data.Filtro_Fe_Fin = ParseDateString($( '#txt-Filtro_Fe_Fin' ).val(), 1, '/');
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
  
  $( '#btn-filter' ).click(function(){
    table_matricula_empleado.ajax.reload();
  });
  
  $( '#form-Matricula_Empleado' ).validate({
		rules:{
			ID_Entidad: {
				required: true
			},
		},
		messages:{
			ID_Entidad:{
				required: "Seleccionar empleado",
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
		submitHandler: form_Matricula_Empleado
	});
	
  $(document).bind('keydown', 'alt+a', function(){
    agregarMatricula_Empleado();
  });
  
  $(document).bind('keydown', 'esc', function(){
    $( "#modal-Matricula_Empleado" ).modal('hide');
	});
})

function agregarMatricula_Empleado(){
  acccion_matricula_empleado='add_matricula_empleado';
  $( '#form-Matricula_Empleado' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '#modal-Matricula_Empleado' ).modal('show');
  $( '.modal-title' ).text('Nueva matrícula');
  
  $( '[name="EID_Empresa"]' ).val('');
  $( '[name="EID_Matricula_Empleado"]' ).val('');
  
	$( '#modal-Matricula_Empleado' ).on('shown.bs.modal', function() {
		$( '#txt-Fe_Matricula' ).focus();
	})
	
  $( '#txt-Fe_Matricula' ).val(fDay + '/' + fMonth + '/' + fYear);
  
	//Personal
	url = base_url + 'HelperController/getDataGeneral';
	$.post( url, {sTipoData : 'entidad', iTipoEntidad : 4}, function( response ){
    if ( response.sStatus == 'success' ) {
      var l = response.arrData.length;
      if (l==1) {
        $( '#cbo-matricula_empleados' ).html( '<option value="' + response.arrData[0].ID + '">' + response.arrData[0].Nombre + '</option>' );
      } else {
        $( '#cbo-matricula_empleados' ).html('<option value="" selected="selected">- Seleccionar -</option>');
        for (var x = 0; x < l; x++) {
          $( '#cbo-matricula_empleados' ).append( '<option value="' + response.arrData[x].ID + '">' + response.arrData[x].Nombre + '</option>' );
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
	}, 'JSON');
	
  $( '#cbo-hora' ).html( '' );
  for (var i = 0; i < arrHoraMinuto.hora.length; i++)
    $( '#cbo-hora' ).append( '<option value="' + arrHoraMinuto.hora[i].value + '">' + arrHoraMinuto.hora[i].value + '</option>' );

  $( '#cbo-minuto' ).html( '' );
  for (var i = 0; i < arrHoraMinuto.minuto.length; i++)
    $( '#cbo-minuto' ).append( '<option value="' + arrHoraMinuto.minuto[i].value + '">' + arrHoraMinuto.minuto[i].value + '</option>' );

}

function verMatricula_Empleado(ID){
  acccion_matricula_empleado='upd_matricula_empleado';
  $( '#form-Matricula_Empleado' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '#modal-loader' ).modal('show');
 
  url = base_url + 'Personal/Matricular_empleado_controller/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $( '#modal-Matricula_Empleado' ).modal('show');
      $( '.modal-title' ).text('Modifcar matrícula');
      
      $('[name="EID_Empresa"]').val(response.ID_Empresa);
      $('[name="EID_Matricula_Empleado"]').val(response.ID_Matricula_Empleado);
        
    	$( '#modal-Matricula_Empleado' ).on('shown.bs.modal', function() {
    		$( '#txt-Fe_Matricula' ).focus();
    	})
    	
      $('[name="Fe_Matricula"]').val(ParseDateString(response.Fe_Matricula, 3, '-'));
      
      var selected;

      $( '#cbo-hora' ).html('');
      for (var i = 0; i < arrHoraMinuto.hora.length; i++) {
        selected = '';
        if(ParseDateString(response.Fe_Matricula, 4, '-') == arrHoraMinuto.hora[i].value)
            selected = 'selected="selected"';
        $( '#cbo-hora' ).append( '<option value="' + arrHoraMinuto.hora[i].value + '" ' + selected + '>' + arrHoraMinuto.hora[i].value + '</option>' );
      }
      
      $( '#cbo-minuto' ).html('');
      for (var i = 0; i < arrHoraMinuto.minuto.length; i++) {
        selected = '';
        if(ParseDateString(response.Fe_Matricula, 5, '-') == arrHoraMinuto.minuto[i].value)
            selected = 'selected="selected"';
        $( '#cbo-minuto' ).append( '<option value="' + arrHoraMinuto.minuto[i].value + '" ' + selected + '>' + arrHoraMinuto.minuto[i].value + '</option>' );
      }
    	
      url = base_url + 'HelperController/getDataGeneral';
	    $.post( url, {sTipoData : 'entidad', iTipoEntidad : 4}, function( responseEmpleado ){
        $( '#cbo-matricula_empleados' ).html('');
        if ( responseEmpleado.sStatus == 'success' ) {
          var l = responseEmpleado.arrData.length;
          for (var x = 0; x < l; x++) {
            selected = '';
            if(response.ID_Entidad == responseEmpleado.arrData[x].ID)
              selected = 'selected="selected"';
            $( '#cbo-matricula_empleados' ).append( '<option value="' + responseEmpleado.arrData[x].ID + '" ' + selected + '>' + responseEmpleado.arrData[x].Nombre + '</option>' );
          }
        } else {
          console.log(response.sMessageSQL);
          $( '.modal-message' ).addClass(response.sClassModal);
          $( '.modal-title-message' ).text(response.sMessage);
          setTimeout(function() {$('#modal-message').modal('hide');}, 1200);
        }
      }, 'JSON');
      
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

function form_Matricula_Empleado(){
  if ( acccion_matricula_empleado=='add_matricula_empleado' || acccion_matricula_empleado=='upd_matricula_empleado' ) {
    $( '#btn-save' ).text('');
    $( '#btn-save' ).attr('disabled', true);
    $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
  
    $( '#modal-loader' ).modal('show');
    
    url = base_url + 'Personal/Matricular_empleado_controller/crudMatricula_Empleado';
  	$.ajax({
      type		  : 'POST',
      dataType	: 'JSON',
  		url		    : url,
  		data		  : $('#form-Matricula_Empleado').serialize(),
  		success : function( response ){
  		  $( '#modal-loader' ).modal('hide');
  		  
  	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
    	  $( '#modal-message' ).modal('show');
  		  
  		  if (response.status == 'success'){
  		    acccion_matricula_empleado='';
  		    $('#modal-Matricula_Empleado').modal('hide');
    	    $( '.modal-message' ).addClass(response.style_modal);
    	    $( '.modal-title-message' ).text(response.message);
    	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
    	    reload_table_matricula_empleado();
  		  } else {
  		    $( '#txt-Nu_Documento_Identidad' ).val('');
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

function eliminarMatricula_Empleado(ID, acccion_matricula_empleado){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });
  
  $(document).bind('keydown', 'alt+l', function(){
    if ( acccion_matricula_empleado=='delete' ) {
      _eliminarMatricula_Empleado($modal_delete, ID);
      acccion_matricula_empleado='';
    }
  });
  
  $( '#btn-save-delete' ).off('click').click(function () {
    _eliminarMatricula_Empleado($modal_delete, ID);
  });
}

function reload_table_matricula_empleado(){
  table_matricula_empleado.ajax.reload(null,false);
}

function _eliminarMatricula_Empleado($modal_delete, ID){
  $( '#modal-loader' ).modal('show');
    
  url = base_url + 'Personal/Matricular_empleado_controller/eliminarMatricula_Empleado/' + ID;
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
		    acccion_matricula_empleado='';
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
  	    reload_table_matricula_empleado();
		  } else {
		    acccion_matricula_empleado='';
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
		  }
    },
    error: function (jqXHR, textStatus, errorThrown) {
		  acccion_matricula_empleado='';
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