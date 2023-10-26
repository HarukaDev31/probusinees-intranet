var url;
var table_serie;
var accion_serie;

$(function () {
	$(document).keyup(function(event){
    if(event.which == 27){//ESC
      $( "#modal-Pos" ).modal('hide');
    }
	});

  $('#cbo-alumno').html('<option value="0" selected="selected">- Todos -</option>');
  url = base_url + 'HelperController/getAlumnoxEntidad';
  var arrPost = { iTipoEntidad: '0' };
  $.post(url, arrPost, function (response) {
    if (response.sStatus == 'success') {
      var l = response.arrData.length;
      $('#cbo-alumno').html('<option value="0" selected="selected">- Todos -</option>');
      var selected = '';
      for (var x = 0; x < l; x++) {
        $('#cbo-alumno').append('<option value="' + response.arrData[x].ID + '">' + response.arrData[x].Nombre + '</option>');
      }
    } else {
      if (response.sMessageSQL !== undefined) {
        console.log(response.sMessageSQL);
      }
    }
  }, 'JSON');

  url = base_url + 'Escuela/AsistenciaAlumnoController/ajax_list';
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
        data.ID_Entidad_Alumno = $('#cbo-alumno').val(),
        data.Filtro_Fe_Inicio = ParseDateString($('#txt-Filtro_Fe_Inicio').val(), 1, '/'),
        data.Filtro_Fe_Fin = ParseDateString($('#txt-Filtro_Fe_Fin').val(), 1, '/');
      },
    },
    'columnDefs': [{
      'className' : 'text-center',
      'targets'   : 'no-sort',
      'orderable' : false,
    },],
    'lengthMenu': [[10, 100, 1000, -1], [10, 100, 1000, "Todos"]],
  });

  $('.dataTables_length').addClass('col-xs-4 col-sm-5 col-md-1');
  $('.dataTables_info').addClass('col-xs-8 col-sm-7 col-md-4');
  $('.dataTables_paginate').addClass('col-xs-12 col-sm-12 col-md-7');

  $('#btn-filter').click(function () {
    table_serie.ajax.reload();
  });
  
  $( "#form-Pos" ).validate({
		rules:{
      ID_Sede_Musica: {
				required: true
      },
      ID_Salon: {
        required: true
      },
      ID_Entidad_Profesor: {
        required: true
      },
      Fe_Asistencia: {
        required: true
      },
		},
    messages: {
      ID_Sede_Musica: {
        required: "Seleccionar",
      },
      ID_Salon: {
        required: "Seleccionar",
      },
      ID_Entidad_Profesor: {
        required: "Seleccionar",
      },
      Fe_Asistencia:{
				required: "Fecha",
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

  $('#cbo-sede_musica').change(function () {
    $('#cbo-salon').html('<option value="" selected="selected">- Sin registros -</option>');
    if ( $(this).val()>0 ) {
      url = base_url + 'HelperController/getSalonxEmpresa';
      var arrParams = {
        iIdEmpresa: $('#header-a-id_empresa').val(),
        ID_Sede_Musica: $(this).val(),
      }
      $.post(url, arrParams, function (response) {
        if (response.sStatus == 'success') {
          var l = response.arrData.length;
          $('#cbo-salon').html('<option value="" selected="selected">- Seleccionar -</option>');
          for (var x = 0; x < l; x++) {
            $('#cbo-salon').append('<option value="' + response.arrData[x].ID + '">' + response.arrData[x].Nombre + '</option>');
          }
        } else {
          if (response.sMessageSQL !== undefined) {
            console.log(response.sMessageSQL);
          }
          $('#modal-message').modal('show');
          $('.modal-message').addClass(response.sClassModal);
          $('.modal-title-message').text(response.sMessage);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1200);
        }
      }, 'JSON');
    }
  });

  $('#table-alumno_matriculados tbody').on('click', '#btn-delete_alumno', function () {
    $(this).closest('tr').remove();

    //if ($('#table-modal_forma_pago > tbody > tr').length == 0) {
    //}
  })// ./ btn Agregar formas de pago del cliente

  $('#cbo-profesor').change(function () {
    $('.div-alumno_matriculados').hide();
    $('#table-alumno_matriculados tbody').empty();
    if ($(this).val() > 0) {
      url = base_url + 'HelperController/getMatriculaAlumno';
      var arrPost = {
        iIdEmpresa: $('#header-a-id_empresa').val(),
        ID_Sede_Musica: $('#cbo-sede_musica').val(),
        ID_Salon: $('#cbo-salon').val(),
        ID_Entidad_Profesor: $('#cbo-profesor').val(),
      };
      $.post(url, arrPost, function (response) {
        if (response.sStatus == 'success') {
          $('.div-alumno_matriculados').show();

          var l = response.arrData.length;
          var response = response.arrData, table_alumno_matriculados='';
          for (var x = 0; x < l; x++) {
            table_alumno_matriculados +=
            "<tr>"
              +"<td class='text-left td-ID_Entidad' style='display:none;'>" + response[x]['ID_Entidad'] + "</td>"
              +"<td class='text-left'>" + response[x]['No_Contacto'] + "</td>"
              +"<td class='text-center'>"
                +"<select class='cbo-asistio form-control' style='width: 100%;'>"
                  +"<option value='1'>Si</option>"
                  +"<option value='0'>No</option>"
                  +"<option value='2'>Recuperar</option>"
                +"</select>"
              + "</td>"
              + "<td class='text-left'><input type='text' name='Txt_Glosa' class='form-control input-Txt_Glosa' autocomplete='off' maxlength='100' value=''></td>"
              + "<td class='text-center'><button type='button' id='btn-delete_alumno' class='btn btn-sm btn-link' alt='Eliminar' title='Eliminar'><i class='fa fa-trash-o fa-lg' aria-hidden='true'></i></button></td>"
            +"</tr>";
          }
          $('#table-alumno_matriculados tbody').append(table_alumno_matriculados);
        } else {
          if (response.sMessageSQL !== undefined) {
            console.log(response.sMessageSQL);
          }
        }
      }, 'JSON');
    }
  })
})

function agregarPos(){
  accion_serie='add_serie';
  
  $( '#form-Pos' )[0].reset();
  
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  
  $( '.help-block' ).empty();

  $('.div-Listar').hide();
  $('.div-AgregarEditar').show();
  
  $( '.modal-title' ).text('Nueva Sede');

  $('.date-picker-invoice').val(fDay + '/' + fMonth + '/' + fYear);

  $( '[name="EID_Control_Asistencia_Alumno"]' ).val('');
 
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

  $('#cbo-salon').html('<option value="" selected="selected">- Sin registros -</option>');

  $('#cbo-profesor').html('<option value="0" selected="selected">- Sin registro -</option>');
  url = base_url + 'HelperController/getDataGeneral';
  var arrPost = { sTipoData: 'entidad', iTipoEntidad: '10' };
  $.post(url, arrPost, function (response) {
    if (response.sStatus == 'success') {
      var l = response.arrData.length;
      $('#cbo-profesor').html('<option value="0" selected="selected">- Seleccionar -</option>');
      var selected = '';
      for (var x = 0; x < l; x++) {
        $('#cbo-profesor').append('<option value="' + response.arrData[x].ID + '">' + response.arrData[x].Nombre + '</option>');
      }
    } else {
      if (response.sMessageSQL !== undefined) {
        console.log(response.sMessageSQL);
      }
    }
  }, 'JSON');

  //recorrer un for de todos los alumnos matriculados en el sede, salon y profesor seleccionado

  $('.div-alumno_matriculados').hide();
  $('#table-alumno_matriculados tbody').empty();
  

  //id entidad - nombre - Asistio (s/n) - Nota
}

function verPos(ID){
  accion_serie='upd_serie';
  
  $( '#form-Pos' )[0].reset();
  
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  
  $( '.help-block' ).empty();

  $('.div-Listar').hide();
  $('.div-AgregarEditar').show();

  $( '#modal-loader' ).modal('show');
 
  url = base_url + 'Escuela/AsistenciaAlumnoController/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $( '#modal-loader' ).modal('hide');

      $( '.modal-title' ).text('Modifcar Sede');
      
      $( '[name="EID_Control_Asistencia_Alumno"]' ).val( response.ID_Control_Asistencia_Alumno );
      
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
  if (accion_serie == 'add_serie' || accion_serie == 'upd_serie') {

    var arrHeader = Array(), arrAsistencia = Array();

    arrHeader = {
      'ID_Empresa': $('#cbo-Empresas').val(),
      'ID_Sede_Musica': $('#cbo-sede_musica').val(),
      'ID_Salon': $('#cbo-salon').val(),
      'ID_Entidad_Profesor': $('#cbo-profesor').val(),
      'Fe_Asistencia': $('#txt-Fe_Asistencia').val(),
    }

    $("#table-alumno_matriculados > tbody > tr").each(function () {
      fila = $(this);

      $ID_Entidad = fila.find(".td-ID_Entidad").text();
      $Nu_Asistio = fila.find(".cbo-asistio").find(':selected').val();
      $Txt_Glosa = fila.find(".input-Txt_Glosa").val();

      obj = {};

      obj.ID_Entidad = $ID_Entidad;
      obj.Nu_Asistio = $Nu_Asistio;
      obj.Txt_Glosa = $Txt_Glosa;

      arrAsistencia.push(obj);
    })

      $( '#btn-save' ).text('');
      $( '#btn-save' ).attr('disabled', true);
      $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
    
      $( '#modal-loader' ).modal('show');
      
      url = base_url + 'Escuela/AsistenciaAlumnoController/crudPos';
    	$.ajax({
        type		  : 'POST',
        dataType	: 'JSON',
    		url		    : url,
    		//data		  : $('#form-Pos').serialize(),
        data: {
          arrHeader: arrHeader,
          arrAsistencia: arrAsistencia,
        },
    		success : function( response ){
    		  $( '#modal-loader' ).modal('hide');
  
    	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
      	  $( '#modal-message' ).modal('show');
    		  
    		  if (response.status == 'success'){
    		    accion_serie='';

            $('.div-AgregarEditar').hide();
            $('.div-Listar').show();
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
  
  url = base_url + 'Escuela/AsistenciaAlumnoController/eliminarPos/' + ID;
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
		    $( '#txt-ID_Control_Asistencia_Alumno_Documento' ).val('');
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

function estadoAsistencia(ID, Nu_Asistencia) {
  var $modal_delete = $('.modal-message-delete');
  $modal_delete.modal('show');

  $('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
  $('.modal-message-delete').addClass('modal-warning');

  $('.modal-title-message-delete').text('¿Cambiar estado asistencia?');

  $('#btn-cancel-delete').off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $('#btn-save-delete').off('click').click(function () {
    url = base_url + 'Escuela/AsistenciaAlumnoController/estadoAsistencia/' + ID + '/' + Nu_Asistencia;
    $.ajax({
      url: url,
      type: "GET",
      dataType: "JSON",
      success: function (response) {
        $('#modal-loader').modal('hide');

        $modal_delete.modal('hide');
        $('.modal-message').removeClass('modal-danger modal-warning modal-success');
        $('#modal-message').modal('show');

        if (response.status == 'success') {
          accion_serie = '';

          $('.modal-message').addClass(response.style_modal);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
          reload_table_serie();
        } else {
          accion_serie = '';
          $('.modal-message').addClass(response.style_modal);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1500);
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        accion_serie = '';
        $('#modal-loader').modal('hide');
        $modal_delete.modal('hide');
        $('.modal-message').removeClass('modal-danger modal-warning modal-success');

        $('#modal-message').modal('show');
        $('.modal-message').addClass('modal-danger');
        $('.modal-title-message').text(textStatus + ' [' + jqXHR.status + ']: ' + errorThrown);
        setTimeout(function () { $('#modal-message').modal('hide'); }, 1700);

        //Message for developer
        console.log(jqXHR.responseText);
      },
    });
  });
}