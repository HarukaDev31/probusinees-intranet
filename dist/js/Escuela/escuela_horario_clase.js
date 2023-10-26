var url;
var table_serie;
var accion_serie;
var arrHoraMinuto =
  '{' +
  '"hora":[' +
  '{"value":"00"},' +
  '{"value":"01"},' +
  '{"value":"02"},' +
  '{"value":"03"},' +
  '{"value":"04"},' +
  '{"value":"05"},' +
  '{"value":"06"},' +
  '{"value":"07"},' +
  '{"value":"08"},' +
  '{"value":"09"},' +
  '{"value":"10"},' +
  '{"value":"11"},' +
  '{"value":"12"},' +
  '{"value":"13"},' +
  '{"value":"14"},' +
  '{"value":"15"},' +
  '{"value":"16"},' +
  '{"value":"17"},' +
  '{"value":"18"},' +
  '{"value":"19"},' +
  '{"value":"20"},' +
  '{"value":"21"},' +
  '{"value":"22"},' +
  '{"value":"23"}' +
  '],' +
  '"minuto":[' +
  '{"value":"00"},' +
  '{"value":"15"},' +
  '{"value":"30"},' +
  '{"value":"45"}' +
  ']' +
  '}';
arrHoraMinuto = JSON.parse(arrHoraMinuto);

$(function () {
	$(document).keyup(function(event){
    if(event.which == 27){//ESC
      $( "#modal-Pos" ).modal('hide');
    }
	});

  url = base_url + 'Escuela/HorarioClaseController/ajax_list';
  table_serie = $('#table-Pos').DataTable({
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
      'data': function (data) {
        data.Filtros = $('#cbo-Filtros').val(),
        data.Global_Filter = $('#txt-Global_Filter').val();
      },
    },
    'columnDefs': [{
      'className' : 'text-center',
      'targets'   : 'no-sort',
      'orderable' : false,
    },],
    'lengthMenu': [[90, 100, 1000, -1], [90, 100, 1000, "Todos"]],
  });

  $('.dataTables_length').addClass('col-xs-4 col-sm-5 col-md-1');
  $('.dataTables_info').addClass('col-xs-8 col-sm-7 col-md-4');
  $('.dataTables_paginate').addClass('col-xs-12 col-sm-12 col-md-7');

  $('#txt-Global_Filter').keyup(function () {
    table_serie.search($(this).val()).draw();
  });
  
  $( "#form-Pos" ).validate({
		rules:{
      ID_Sede_Musica: {
				required: true
      },
      ID_Dia_Semana: {
        required: true
      },
		},
		messages:{
      ID_Sede_Musica:{
				required: "Seleccionar sede",
      },
      ID_Dia_Semana: {
        required: "Seleccionar día",
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
		submitHandler: form_Pos
	});
	
  $(document).bind('keydown', 'f2', function(){
    agregarPos();
  });
})

function agregarPos(){
  accion_serie='add_serie';
  
  $( '#form-Pos' )[0].reset();
  
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  
  $( '.help-block' ).empty();
  
  $( '#modal-Pos' ).modal('show');
  
  $( '.modal-title' ).text('Nuevo Horario de Clase');
  
  $( '[name="EID_Horario_Clase"]' ).val('');
  
  
  url = base_url + 'HelperController/getEmpresas';
  $.post( url , function( response ){
    $( '#cbo-Empresas' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-Empresas' ).append( '<option value="' + response[i].ID_Empresa + '">' + response[i].No_Empresa + '</option>' );
  }, 'JSON');

  $('#cbo-sede_musica').html('<option value="" selected="selected">- Sin registros -</option>');
  url = base_url + 'HelperController/getSedexEmpresa';
  var arrParams = {
    iIdEmpresa: $('#header-a-id_empresa').val(),
  };
  $.post(url, arrParams, function (response) {
    if (response.sStatus == 'success') {
      $('#cbo-sede_musica').html('<option value="" selected="selected">- Seleccionar -</option>');
      var l = response.arrData.length;
      for (var x = 0; x < l; x++) {
        $('#cbo-sede_musica').append('<option value="' + response.arrData[x].ID + '">' + response.arrData[x].Nombre + '</option>');
      }
    } else {
      if (response.sMessageSQL !== undefined) {
        console.log(response.sMessageSQL);
      }
      console.log(response.sMessage);
    }
  }, 'JSON');

  $('#cbo-dia_semana').html('<option value="" selected="selected">- Sin registros -</option>');
  url = base_url + 'HelperController/getDiasSemana';
  $.post(url, {}, function (response) {
    if (response.sStatus == 'success') {
      $('#cbo-dia_semana').html('<option value="" selected="selected">- Seleccionar -</option>');
      var l = response.arrData.length;
      for (var x = 0; x < l; x++) {
        $('#cbo-dia_semana').append('<option value="' + response.arrData[x].ID + '">' + response.arrData[x].Nombre + '</option>');
      }
    } else {
      if (response.sMessageSQL !== undefined) {
        console.log(response.sMessageSQL);
      }
      console.log(response.sMessage);
    }
  }, 'JSON');

  $('#cbo-hora_ini').html('');
  for (var i = 0; i < arrHoraMinuto.hora.length; i++)
    $('#cbo-hora_ini').append('<option value="' + arrHoraMinuto.hora[i].value + '">' + arrHoraMinuto.hora[i].value + '</option>');

  $('#cbo-minuto_ini').html('');
  for (var i = 0; i < arrHoraMinuto.minuto.length; i++)
    $('#cbo-minuto_ini').append('<option value="' + arrHoraMinuto.minuto[i].value + '">' + arrHoraMinuto.minuto[i].value + '</option>');

  $('#cbo-hora_fin').html('');
  for (var i = 0; i < arrHoraMinuto.hora.length; i++)
    $('#cbo-hora_fin').append('<option value="' + arrHoraMinuto.hora[i].value + '">' + arrHoraMinuto.hora[i].value + '</option>');

  $('#cbo-minutofin').html('');
  for (var i = 0; i < arrHoraMinuto.minuto.length; i++)
    $('#cbo-minuto_fin').append('<option value="' + arrHoraMinuto.minuto[i].value + '">' + arrHoraMinuto.minuto[i].value + '</option>');

  $( '#cbo-Estado' ).html( '<option value="1">Activo</option>' );
  $( '#cbo-Estado' ).append( '<option value="0">Inactivo</option>' );
}

function verPos(ID){
  accion_serie='upd_serie';
  
  $( '#form-Pos' )[0].reset();
  
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  
  $( '.help-block' ).empty();
  
  $( '#modal-loader' ).modal('show');
 
  url = base_url + 'Escuela/HorarioClaseController/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $( '#modal-loader' ).modal('hide');
      
      $( '#modal-Pos' ).modal('show');
      $('.modal-title').text('Modifcar Horario de Clase');
      
      $( '[name="EID_Horario_Clase"]' ).val( response.ID_Horario_Clase );
      $('[name="EID_Sede_Musica"]').val(response.ID_Sede_Musica);
      $('[name="EID_Dia_Semana"]').val(response.ID_Dia_Semana);
      
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

      $('#cbo-sede_musica').html('<option value="" selected="selected">- Sin registros -</option>');
      url = base_url + 'HelperController/getSedexEmpresa';
      var arrParams = {
        iIdEmpresa: response.ID_Empresa,
      };
      $.post(url, arrParams, function (responseSalon) {
        if (responseSalon.sStatus == 'success') {
          $('#cbo-sede_musica').html('');
          var l = responseSalon.arrData.length;
          for (var x = 0; x < l; x++) {
            selected = '';
            if (response.ID_Sede_Musica == responseSalon.arrData[x].ID)
              selected = 'selected="selected"';
            $('#cbo-sede_musica').append('<option value="' + responseSalon.arrData[x].ID + '" ' + selected + '>' + responseSalon.arrData[x].Nombre + '</option>');
          }
        } else {
          if (responseSalon.sMessageSQL !== undefined) {
            console.log(responseSalon.sMessageSQL);
          }
          console.log(responseSalon.sMessage);
        }
      }, 'JSON');

      $('#cbo-dia_semana').html('<option value="" selected="selected">- Sin registros -</option>');
      url = base_url + 'HelperController/getDiasSemana';
      $.post(url, {}, function (responseSalon) {
        if (responseSalon.sStatus == 'success') {
          $('#cbo-dia_semana').html('');
          var l = responseSalon.arrData.length;
          for (var x = 0; x < l; x++) {
            selected = '';
            if (response.ID_Dia_Semana == responseSalon.arrData[x].ID)
              selected = 'selected="selected"';
            $('#cbo-dia_semana').append('<option value="' + responseSalon.arrData[x].ID + '" ' + selected + '>' + responseSalon.arrData[x].Nombre + '</option>');
          }
        } else {
          if (responseSalon.sMessageSQL !== undefined) {
            console.log(responseSalon.sMessageSQL);
          }
          console.log(responseSalon.sMessage);
        }
      }, 'JSON');

      $('#cbo-hora_ini').html('');
      for (var i = 0; i < arrHoraMinuto.hora.length; i++) {
        selected = '';
        if (response.Nu_Hora_Desde == arrHoraMinuto.hora[i].value)
          selected = 'selected="selected"';
        $('#cbo-hora_ini').append('<option value="' + arrHoraMinuto.hora[i].value + '" ' + selected + '>' + arrHoraMinuto.hora[i].value + '</option>');
      }

      $('#cbo-minuto_ini').html('');
      for (var i = 0; i < arrHoraMinuto.minuto.length; i++) {
        selected = '';
        if (response.Nu_Minuto_Desde == arrHoraMinuto.minuto[i].value)
          selected = 'selected="selected"';
        $('#cbo-minuto_ini').append('<option value="' + arrHoraMinuto.minuto[i].value + '" ' + selected + '>' + arrHoraMinuto.minuto[i].value + '</option>');
      }

      $('#cbo-hora_fin').html('');
      for (var i = 0; i < arrHoraMinuto.hora.length; i++) {
        selected = '';
        if (response.Nu_Hora_Hasta == arrHoraMinuto.hora[i].value)
          selected = 'selected="selected"';
        $('#cbo-hora_fin').append('<option value="' + arrHoraMinuto.hora[i].value + '" ' + selected + '>' + arrHoraMinuto.hora[i].value + '</option>');
      }

      $('#cbo-minuto_fin').html('');
      for (var i = 0; i < arrHoraMinuto.minuto.length; i++) {
        selected = '';
        if (response.Nu_Minuto_Hasta == arrHoraMinuto.minuto[i].value)
          selected = 'selected="selected"';
        $('#cbo-minuto_fin').append('<option value="' + arrHoraMinuto.minuto[i].value + '" ' + selected + '>' + arrHoraMinuto.minuto[i].value + '</option>');
      }

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

function form_Pos(){
  if ( accion_serie=='add_serie' || accion_serie=='upd_serie' ) {
      $( '#btn-save' ).text('');
      $( '#btn-save' ).attr('disabled', true);
      $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
    
      $( '#modal-loader' ).modal('show');
      
      url = base_url + 'Escuela/HorarioClaseController/crudPos';
    	$.ajax({
        type		  : 'POST',
        dataType	: 'JSON',
    		url		    : url,
    		data		  : $('#form-Pos').serialize(),
    		success : function( response ){
    		  $( '#modal-loader' ).modal('hide');
  
    	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
      	  $( '#modal-message' ).modal('show');
    		  
    		  if (response.status == 'success'){
    		    accion_serie='';
    		    
    		    $('#modal-Pos').modal('hide');
      	    $( '.modal-message' ).addClass(response.style_modal);
      	    $( '.modal-title-message' ).text(response.message);
      	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
      	    reload_table_serie();
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

function eliminarPos(ID, accion_serie){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });
  
  $(document).bind('keydown', 'return', function(){
    if ( accion_serie=='delete' ) {
      _eliminarPos($modal_delete, ID);
      accion_serie='';
    }
  });

  $( '#btn-save-delete' ).off('click').click(function () {
    _eliminarPos($modal_delete, ID);
  });
}

function _eliminarPos($modal_delete, ID){
  $( '#modal-loader' ).modal('show');
  
  url = base_url + 'Escuela/HorarioClaseController/eliminarPos/' + ID;
  $.ajax({
    url       : url,
    type      : "GET",
    dataType  : "JSON",
    success: function( response ){
      $( '#modal-loader' ).modal('hide');//modal del cargador
      $modal_delete.modal('hide');//modal del mensaje de eliminación
      
	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
  	  $( '#modal-message' ).modal('show');
		  
		  if (response.status == 'success'){
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
  	    reload_table_serie();
		  } else {
		    $( '#txt-ID_Horario_Clase_Documento' ).val('');
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
		  }
		  accion_serie='';
    },
    error: function (jqXHR, textStatus, errorThrown) {
		  accion_serie='';
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

function reload_table_serie(){
  table_serie.ajax.reload(null,false);
}
