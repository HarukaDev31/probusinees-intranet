var url;
var table_serie;
var accion_serie;

$(function () {
	$(document).keyup(function(event){
    if(event.which == 27){//ESC
      $( "#modal-Pos" ).modal('hide');
    }
	});

  url = base_url + 'Escuela/MatriculaAlumnoController/ajax_list';
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
        data.Filtro_Fe_Inicio = ParseDateString($('#txt-Filtro_Fe_Inicio').val(), 1, '/'),
        data.Filtro_Fe_Fin = ParseDateString($('#txt-Filtro_Fe_Fin').val(), 1, '/');
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
      ID_Horario_Clase: {
        required: true
      },
      ID_Entidad_Profesor: {
        required: true
      },
      ID_Entidad_Alumno: {
        required: true
      },
      ID_Familia: {
        required: true
      },
      ID_Grupo_Clase: {
        required: true
      },
      ID_Tipo_Clase: {
        required: true
      },
		},
		messages:{
      ID_Sede_Musica:{
				required: "Seleccionar",
      },
      ID_Salon: {
        required: "Seleccionar",
      },
      ID_Horario_Clase: {
        required: "Seleccionar",
      },
      ID_Entidad_Profesor: {
        required: "Seleccionar",
      },
      ID_Entidad_Alumno: {
        required: "Seleccionar",
      },
      ID_Familia: {
        required: "Seleccionar",
      },
      ID_Grupo_Clase: {
        required: "Seleccionar",
      },
      ID_Tipo_Clase: {
        required: "Seleccionar",
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

  $('#cbo-sede_musica').change(function () {
    $('#cbo-salon').html('<option value="" selected="selected">- Sin registros -</option>');
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

    $('#cbo-horario_clase').html('<option value="" selected="selected">- Sin registros -</option>');
    url = base_url + 'HelperController/getHorarioClase';
    var arrParams = {
      iIdEmpresa: $('#header-a-id_empresa').val(),
      ID_Sede_Musica: $(this).val(),
    }
    $.post(url, arrParams, function (response) {
      if (response.sStatus == 'success') {
        var l = response.arrData.length;
        $('#cbo-horario_clase').html('<option value="" selected="selected">- Seleccionar -</option>');
        for (var x = 0; x < l; x++) {
          $('#cbo-horario_clase').append('<option value="' + response.arrData[x].ID + '">' + response.arrData[x].Nombre + '</option>');
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
  });

  //Multiple
  $("#form-Pos_Multiple").validate({
    rules: {
      ID_Sede_Musica2: {
        required: true
      },
      ID_Salon2: {
        required: true
      },
      ID_Entidad_Profesor2: {
        required: true
      },
      ID_Familia2: {
        required: true
      },
      ID_Grupo_Clase2: {
        required: true
      },
      ID_Tipo_Clase2: {
        required: true
      },
    },
    messages: {
      ID_Sede_Musica2: {
        required: "Seleccionar",
      },
      ID_Salon2: {
        required: "Seleccionar",
      },
      ID_Entidad_Profesor2: {
        required: "Seleccionar",
      },
      ID_Familia2: {
        required: "Seleccionar",
      },
      ID_Grupo_Clase2: {
        required: "Seleccionar",
      },
      ID_Tipo_Clase2: {
        required: "Seleccionar",
      },
    },
    errorPlacement: function (error, element) {
      $(element).closest('.form-group').find('.help-block').html(error.html());
    },
    highlight: function (element) {
      $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
    },
    unhighlight: function (element, errorClass, validClass) {
      $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
      $(element).closest('.form-group').find('.help-block').html('');
    },
    submitHandler: form_Pos_Multiple
  });

  $('#cbo-sede_musica2').change(function () {
    $('#cbo-salon2').html('<option value="" selected="selected">- Sin registros -</option>');
    url = base_url + 'HelperController/getSalonxEmpresa';
    var arrParams = {
      iIdEmpresa: $('#header-a-id_empresa').val(),
      ID_Sede_Musica: $(this).val(),
    }
    $.post(url, arrParams, function (response) {
      if (response.sStatus == 'success') {
        var l = response.arrData.length;
        $('#cbo-salon2').html('<option value="" selected="selected">- Seleccionar -</option>');
        for (var x = 0; x < l; x++) {
          $('#cbo-salon2').append('<option value="' + response.arrData[x].ID + '">' + response.arrData[x].Nombre + '</option>');
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

    $('#cbo-horario_clase2').html('<option value="" selected="selected">- Sin registros -</option>');
    url = base_url + 'HelperController/getHorarioClase';
    var arrParams = {
      iIdEmpresa: $('#header-a-id_empresa').val(),
      ID_Sede_Musica: $(this).val(),
    }
    $.post(url, arrParams, function (response) {
      if (response.sStatus == 'success') {
        var l = response.arrData.length;
        $('#cbo-horario_clase2').html('<option value="" selected="selected">- Seleccionar -</option>');
        for (var x = 0; x < l; x++) {
          $('#cbo-horario_clase2').append('<option value="' + response.arrData[x].ID + '">' + response.arrData[x].Nombre + '</option>');
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
  });

  $('#btn-agregarAlumnoHorario').click(function () {
    var iIdHorarioClase = $('#cbo-horario_clase2').val();
    var sHorarioClase = $('#cbo-horario_clase2 :selected').text();
    var iIdAlumno = $('#cbo-alumno2').val();
    var sNombreAlumno = $('#cbo-alumno2 :selected').text();    
    var iIdAgrupado = iIdHorarioClase + iIdAlumno;

    $('.help-block').empty();
    $('.form-group').removeClass('has-error');
    $('#cbo-horario_clase2').closest('.form-group').find('.help-block').html('');
    
    if (iIdHorarioClase == null || iIdHorarioClase == '') {
      $('#cbo-horario_clase2').closest('.form-group').find('.help-block').html('Seleccionar horario');
      $('#cbo-horario_clase2').closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if (iIdAlumno == null || iIdAlumno == '') {
      $('#cbo-alumno2').closest('.form-group').find('.help-block').html('Seleccionar alumno');
      $('#cbo-alumno2').closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      var table_enlace_producto =
        "<tr id='tr_enlace_horario_alumno" + iIdAgrupado + "'>"
        + "<td class='text-left' style='display:none;'>" + iIdAgrupado + "</td>"
        + "<td class='text-left td-ID_Horario_Clase2' style='display:none;'>" + iIdHorarioClase + "</td>"
        + "<td class='text-left td-ID_Entidad_Alumno2' style='display:none;'>" + iIdAlumno + "</td>"
        + "<td class='text-left'>" + sHorarioClase + "</td>"
        + "<td class='text-left'>" + sNombreAlumno + "</td>"
        + "<td class='text-center'><button type='button' id='btn-deleteProductoEnlace' class='btn btn-xs btn-link' alt='Eliminar' title='Eliminar'><i class='fa fa-2x fa-trash-o' aria-hidden='true'></i></button></td>"
        + "</tr>";

      if (isExistTableTemporalEnlacesProducto(iIdAgrupado)) {
        $('#cbo-horario_clase2').closest('.form-group').find('.help-block').html('Ya existe alumno: <b>' + sNombreAlumno + ' y horario: ' + sHorarioClase + '</b>');
        $('#cbo-horario_clase2').closest('.form-group').removeClass('has-success').addClass('has-error');
      } else {
        $('.div-alumno_horarios').show();
        $('#table-alumno_horarios').show();
        $('#table-alumno_horarios').append(table_enlace_producto);
      }
    }
  })

  $('#table-alumno_horarios tbody').on('click', '#btn-deleteProductoEnlace', function () {
    $(this).closest('tr').remove();
    if ($('#table-alumno_horarios >tbody >tr').length == 0)
      $('#table-alumno_horarios').hide();
  })

  $(document).bind('keydown', 'f2', function(){
    agregarPos();
  });
})

function isExistTableTemporalEnlacesProducto(iIdAgrupado) {
  return Array.from($('tr[id*=tr_enlace_horario_alumno]'))
    .some(element => ($('td:nth(0)', $(element)).html() === iIdAgrupado));
}

function agregarMultiplePos() {
  accion_serie = 'add_serie';

  $('#form-Pos_Multiple')[0].reset();

  $('.form-group').removeClass('has-error');
  $('.form-group').removeClass('has-success');

  $('.help-block').empty();

  $('.div-Listar').hide();
  $('.div-AgregarEditar').show();

  $('.modal-title').text('Nueva Sede');

  $('.div-alumno_horarios').hide();
  $('#table-alumno_horarios tbody').empty();

  $('[name="EID_Matricula_Alumno"]').val('');
  $('[name="EID_Horario_Clase"]').val('');
  $('[name="EID_Entidad_Alumno"]').val('');

  $('.date-picker-invoice').val(fDay + '/' + fMonth + '/' + fYear);

  url = base_url + 'HelperController/getEmpresas';
  $.post(url, function (response) {
    $('#cbo-Empresa2').html('<option value="0" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++)
      $('#cbo-Empresas2').append('<option value="' + response[i].ID_Empresa + '">' + response[i].No_Empresa + '</option>');
  }, 'JSON');

  $('#cbo-sede_musica2').html('<option value="" selected="selected">- Sin registros -</option>');
  url = base_url + 'HelperController/getSedexEmpresa';
  var arrParams = {
    iIdEmpresa: $('#header-a-id_empresa').val(),
  };
  $.post(url, arrParams, function (response) {
    if (response.sStatus == 'success') {
      $('#cbo-sede_musica2').html('<option value="" selected="selected">- Seleccionar -</option>');
      var l = response.arrData.length;
      for (var x = 0; x < l; x++) {
        $('#cbo-sede_musica2').append('<option value="' + response.arrData[x].ID + '">' + response.arrData[x].Nombre + '</option>');
      }
    } else {
      if (response.sMessageSQL !== undefined) {
        console.log(response.sMessageSQL);
      }
      console.log(response.sMessage);
    }
  }, 'JSON');

  $('#cbo-salon2').html('<option value="" selected="selected">- Sin registros -</option>');
  $('#cbo-horario_clase2').html('<option value="" selected="selected">- Sin registros -</option>');

  $('#cbo-profesor2').html('<option value="0" selected="selected">- Sin registro -</option>');
  url = base_url + 'HelperController/getDataGeneral';
  var arrPost = { sTipoData: 'entidad', iTipoEntidad: '10' };
  $.post(url, arrPost, function (response) {
    if (response.sStatus == 'success') {
      var l = response.arrData.length;
      $('#cbo-profesor2').html('<option value="0" selected="selected">- Seleccionar -</option>');
      var selected = '';
      for (var x = 0; x < l; x++) {
        $('#cbo-profesor2').append('<option value="' + response.arrData[x].ID + '">' + response.arrData[x].Nombre + '</option>');
      }
    } else {
      if (response.sMessageSQL !== undefined) {
        console.log(response.sMessageSQL);
      }
    }
  }, 'JSON');

  $('#cbo-alumno2').html('<option value="0" selected="selected">- Sin registro -</option>');
  url = base_url + 'HelperController/getAlumnoxEntidad';
  var arrPost = { iTipoEntidad: '0' };
  $.post(url, arrPost, function (response) {
    if (response.sStatus == 'success') {
      var l = response.arrData.length;
      $('#cbo-alumno2').html('<option value="0" selected="selected">- Seleccionar -</option>');
      var selected = '';
      for (var x = 0; x < l; x++) {
        $('#cbo-alumno2').append('<option value="' + response.arrData[x].ID + '">' + response.arrData[x].Nombre + '</option>');
      }
    } else {
      if (response.sMessageSQL !== undefined) {
        console.log(response.sMessageSQL);
      }
    }
  }, 'JSON');

  $('#cbo-categoria2').html('<option value="" selected="selected">- Sin registros -</option>');
  url = base_url + 'HelperController/getDataGeneral';
  $.post(url, { sTipoData: 'categoria' }, function (response) {
    if (response.sStatus == 'success') {
      var l = response.arrData.length;
      $('#cbo-categoria2').html('<option value="" selected="selected">- Seleccionar -</option>');
      for (var x = 0; x < l; x++) {
        $('#cbo-categoria2').append('<option value="' + response.arrData[x].ID + '">' + response.arrData[x].Nombre + '</option>');
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

  $('#cbo-tipos_grupo_clase2').html('<option value="0" selected="selected">- Sin registro -</option>');
  url = base_url + 'HelperController/getValoresTablaDato';
  $.post(url, { sTipoData: 'Tipos_Grupo_Clase' }, function (response) {
    if (response.sStatus == 'success') {
      var l = response.arrData.length;
      $('#cbo-tipos_grupo_clase2').html('<option value="0" selected="selected">- Seleccionar -</option>');
      for (var x = 0; x < l; x++) {
        $('#cbo-tipos_grupo_clase2').append('<option value="' + response.arrData[x].ID_Tabla_Dato + '">' + response.arrData[x].No_Descripcion + '</option>');
      }
    } else {
      if (response.sMessageSQL !== undefined) {
        console.log(response.sMessageSQL);
      }
    }
  }, 'JSON');

  $('#cbo-tipos_clase2').html('<option value="0" selected="selected">- Sin registro -</option>');
  url = base_url + 'HelperController/getValoresTablaDato';
  $.post(url, { sTipoData: 'Tipos_Clase' }, function (response) {
    if (response.sStatus == 'success') {
      var l = response.arrData.length;
      $('#cbo-tipos_clase2').html('<option value="0" selected="selected">- Seleccionar -</option>');
      for (var x = 0; x < l; x++) {
        $('#cbo-tipos_clase2').append('<option value="' + response.arrData[x].ID_Tabla_Dato + '">' + response.arrData[x].No_Descripcion + '</option>');
      }
    } else {
      if (response.sMessageSQL !== undefined) {
        console.log(response.sMessageSQL);
      }
    }
  }, 'JSON');

  $('#cbo-Estado2').html('<option value="1">Activo</option>');
  $('#cbo-Estado2').append('<option value="0">Inactivo</option>');
}

function agregarPos(){
  accion_serie='add_serie';
  
  $( '#form-Pos' )[0].reset();
  
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  
  $( '.help-block' ).empty();
  
  $( '#modal-Pos' ).modal('show');
  
  $( '.modal-title' ).text('Nueva Matrícula');
  
  $('[name="EID_Matricula_Alumno"]').val('');
  $('[name="EID_Horario_Clase"]').val('');
  $('[name="EID_Entidad_Alumno"]').val('');

  $('.date-picker-invoice').val(fDay + '/' + fMonth + '/' + fYear);

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
  $('#cbo-horario_clase').html('<option value="" selected="selected">- Sin registros -</option>');

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

  $('#cbo-alumno').html('<option value="0" selected="selected">- Sin registro -</option>');
  url = base_url + 'HelperController/getAlumnoxEntidad';
  var arrPost = { iTipoEntidad: '0' };
  $.post(url, arrPost, function (response) {
    if (response.sStatus == 'success') {
      var l = response.arrData.length;
      $('#cbo-alumno').html('<option value="0" selected="selected">- Seleccionar -</option>');
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

  $('#cbo-categoria').html('<option value="" selected="selected">- Sin registros -</option>');
  url = base_url + 'HelperController/getDataGeneral';
  $.post(url, { sTipoData: 'categoria' }, function (response) {
    if (response.sStatus == 'success') {
      var l = response.arrData.length;
      $('#cbo-categoria').html('<option value="" selected="selected">- Seleccionar -</option>');
      for (var x = 0; x < l; x++) {
        $('#cbo-categoria').append('<option value="' + response.arrData[x].ID + '">' + response.arrData[x].Nombre + '</option>');
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

  $('#cbo-tipos_grupo_clase').html('<option value="0" selected="selected">- Sin registro -</option>');
  url = base_url + 'HelperController/getValoresTablaDato';
  $.post(url, { sTipoData: 'Tipos_Grupo_Clase' }, function (response) {
    if (response.sStatus == 'success') {
      var l = response.arrData.length;
      $('#cbo-tipos_grupo_clase').html('<option value="0" selected="selected">- Seleccionar -</option>');
      for (var x = 0; x < l; x++) {
        $('#cbo-tipos_grupo_clase').append('<option value="' + response.arrData[x].ID_Tabla_Dato + '">' + response.arrData[x].No_Descripcion + '</option>');
      }
    } else {
      if (response.sMessageSQL !== undefined) {
        console.log(response.sMessageSQL);
      }
    }
  }, 'JSON');

  $('#cbo-tipos_clase').html('<option value="0" selected="selected">- Sin registro -</option>');
  url = base_url + 'HelperController/getValoresTablaDato';
  $.post(url, { sTipoData: 'Tipos_Clase' }, function (response) {
    if (response.sStatus == 'success') {
      var l = response.arrData.length;
      $('#cbo-tipos_clase').html('<option value="0" selected="selected">- Seleccionar -</option>');
      for (var x = 0; x < l; x++) {
        $('#cbo-tipos_clase').append('<option value="' + response.arrData[x].ID_Tabla_Dato + '">' + response.arrData[x].No_Descripcion + '</option>');
      }
    } else {
      if (response.sMessageSQL !== undefined) {
        console.log(response.sMessageSQL);
      }
    }
  }, 'JSON');

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
 
  url = base_url + 'Escuela/MatriculaAlumnoController/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $( '#modal-loader' ).modal('hide');
      
      $( '#modal-Pos' ).modal('show');
      $('.modal-title').text('Modifcar Matrícula');
      
      $( '[name="EID_Matricula_Alumno"]' ).val( response.ID_Matricula_Alumno );
      $('[name="EID_Horario_Clase"]').val(response.ID_Horario_Clase);
      $('[name="EID_Entidad_Alumno"]').val(response.ID_Entidad_Alumno);
      
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
      url = base_url + 'HelperController/getSalonxEmpresa';
      var arrParams = {
        iIdEmpresa: $('#header-a-id_empresa').val(),
        ID_Sede_Musica: response.ID_Sede_Musica,
      }
      $.post(url, arrParams, function (responseEdit) {
        if (responseEdit.sStatus == 'success') {
          var l = responseEdit.arrData.length;
          $('#cbo-salon').html('<option value="" selected="selected">- Seleccionar -</option>');
          for (var x = 0; x < l; x++) {
            selected = '';
            if (response.ID_Salon == responseEdit.arrData[x].ID)
              selected = 'selected="selected"';
            $('#cbo-salon').append('<option value="' + responseEdit.arrData[x].ID + '" ' + selected + '>' + responseEdit.arrData[x].Nombre + '</option>');
          }
        } else {
          if (responseEdit.sMessageSQL !== undefined) {
            console.log(responseEdit.sMessageSQL);
          }
          $('#modal-message').modal('show');
          $('.modal-message').addClass(responseEdit.sClassModal);
          $('.modal-title-message').text(responseEdit.sMessage);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1200);
        }
      }, 'JSON');

      $('#cbo-horario_clase').html('<option value="" selected="selected">- Sin registros -</option>');
      url = base_url + 'HelperController/getHorarioClase';
      var arrParams = {
        iIdEmpresa: $('#header-a-id_empresa').val(),
        ID_Sede_Musica: response.ID_Sede_Musica,
      }
      $.post(url, arrParams, function (responseEdit) {
        if (responseEdit.sStatus == 'success') {
          var l = responseEdit.arrData.length;
          $('#cbo-horario_clase').html('<option value="" selected="selected">- Seleccionar -</option>');
          for (var x = 0; x < l; x++) {
            selected = '';
            if (response.ID_Horario_Clase == responseEdit.arrData[x].ID)
              selected = 'selected="selected"';
            $('#cbo-horario_clase').append('<option value="' + responseEdit.arrData[x].ID + '" ' + selected + '>' + responseEdit.arrData[x].Nombre + '</option>');
          }
        } else {
          if (responseEdit.sMessageSQL !== undefined) {
            console.log(responseEdit.sMessageSQL);
          }
          $('#modal-message').modal('show');
          $('.modal-message').addClass(responseEdit.sClassModal);
          $('.modal-title-message').text(responseEdit.sMessage);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1200);
        }
      }, 'JSON');
      
      $('#cbo-profesor').html('<option value="0" selected="selected">- Sin registro -</option>');
      url = base_url + 'HelperController/getDataGeneral';
      var arrPost = { sTipoData: 'entidad', iTipoEntidad: '10' };
      $.post(url, arrPost, function (responseEdit) {
        if (responseEdit.sStatus == 'success') {
          var l = responseEdit.arrData.length;
          $('#cbo-profesor').html('<option value="0" selected="selected">- Seleccionar -</option>');
          var selected = '';
          for (var x = 0; x < l; x++) {
            selected = '';
            if (response.ID_Entidad_Profesor == responseEdit.arrData[x].ID)
              selected = 'selected="selected"';
            $('#cbo-profesor').append('<option value="' + responseEdit.arrData[x].ID + '" ' + selected + '>' + responseEdit.arrData[x].Nombre + '</option>');
          }
        } else {
          if (responseEdit.sMessageSQL !== undefined) {
            console.log(responseEdit.sMessageSQL);
          }
        }
      }, 'JSON');

      $('#cbo-alumno').html('<option value="0" selected="selected">- Sin registro -</option>');
      url = base_url + 'HelperController/getAlumnoxEntidad';
      var arrPost = { iTipoEntidad: '0' };
      $.post(url, arrPost, function (responseEdit) {
        if (responseEdit.sStatus == 'success') {
          var l = responseEdit.arrData.length;
          $('#cbo-alumno').html('<option value="0" selected="selected">- Seleccionar -</option>');
          var selected = '';
          for (var x = 0; x < l; x++) {
            selected = '';
            if (response.ID_Entidad_Alumno == responseEdit.arrData[x].ID)
              selected = 'selected="selected"';
            $('#cbo-alumno').append('<option value="' + responseEdit.arrData[x].ID + '" ' + selected + '>' + responseEdit.arrData[x].Nombre + '</option>');
          }
        } else {
          if (responseEdit.sMessageSQL !== undefined) {
            console.log(responseEdit.sMessageSQL);
          }
        }
      }, 'JSON');

      $('#cbo-categoria').html('<option value="" selected="selected">- Sin registros -</option>');
      url = base_url + 'HelperController/getDataGeneral';
      $.post(url, { sTipoData: 'categoria' }, function (responseEdit) {
        if (responseEdit.sStatus == 'success') {
          var l = responseEdit.arrData.length;
          $('#cbo-categoria').html('<option value="" selected="selected">- Seleccionar -</option>');
          for (var x = 0; x < l; x++) {
            selected = '';
            if (response.ID_Familia == responseEdit.arrData[x].ID)
              selected = 'selected="selected"';
            $('#cbo-categoria').append('<option value="' + responseEdit.arrData[x].ID + '" ' + selected + '>' + responseEdit.arrData[x].Nombre + '</option>');
          }
        } else {
          if (responseEdit.sMessageSQL !== undefined) {
            console.log(responseEdit.sMessageSQL);
          }
          $('#modal-message').modal('show');
          $('.modal-message').addClass(responseEdit.sClassModal);
          $('.modal-title-message').text(responseEdit.sMessage);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1200);
        }
      }, 'JSON');

      $('#cbo-tipos_grupo_clase').html('<option value="0" selected="selected">- Sin registro -</option>');
      url = base_url + 'HelperController/getValoresTablaDato';
      $.post(url, { sTipoData: 'Tipos_Grupo_Clase' }, function (responseEdit) {
        if (responseEdit.sStatus == 'success') {
          var l = responseEdit.arrData.length;
          $('#cbo-tipos_grupo_clase').html('<option value="0" selected="selected">- Seleccionar -</option>');
          for (var x = 0; x < l; x++) {
            selected = '';
            if (response.ID_Grupo_Clase == responseEdit.arrData[x].ID_Tabla_Dato)
              selected = 'selected="selected"';
            $('#cbo-tipos_grupo_clase').append('<option value="' + responseEdit.arrData[x].ID_Tabla_Dato + '" ' + selected + '>' + responseEdit.arrData[x].No_Descripcion + '</option>');
          }
        } else {
          if (responseEdit.sMessageSQL !== undefined) {
            console.log(responseEdit.sMessageSQL);
          }
        }
      }, 'JSON');

      $('#cbo-tipos_clase').html('<option value="0" selected="selected">- Sin registro -</option>');
      url = base_url + 'HelperController/getValoresTablaDato';
      $.post(url, { sTipoData: 'Tipos_Clase' }, function (responseEdit) {
        if (responseEdit.sStatus == 'success') {
          var l = responseEdit.arrData.length;
          $('#cbo-tipos_clase').html('<option value="0" selected="selected">- Seleccionar -</option>');
          for (var x = 0; x < l; x++) {
            selected = '';
            if (response.ID_Tipo_Clase == responseEdit.arrData[x].ID_Tabla_Dato)
              selected = 'selected="selected"';
            $('#cbo-tipos_clase').append('<option value="' + responseEdit.arrData[x].ID_Tabla_Dato + '" ' + selected + '>' + responseEdit.arrData[x].No_Descripcion + '</option>');
          }
        } else {
          if (responseEdit.sMessageSQL !== undefined) {
            console.log(responseEdit.sMessageSQL);
          }
        }
      }, 'JSON');

      $('[name="Fe_Matricula"]').val(ParseDateString(response.Fe_Matricula, 6, '-'));
      $('[name="Txt_Glosa"]').val(response.Txt_Glosa);

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
      
      url = base_url + 'Escuela/MatriculaAlumnoController/crudPos';
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
  
  url = base_url + 'Escuela/MatriculaAlumnoController/eliminarPos/' + ID;
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
		    $( '#txt-ID_Matricula_Alumno_Documento' ).val('');
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

function form_Pos_Multiple() {
  if (accion_serie == 'add_serie') {
    var arrHeader = Array(), arrAlumnoHorario = Array();

    arrHeader = {
      'ID_Empresa2': $('#cbo-Empresas2').val(),
      'ID_Sede_Musica2': $('#cbo-sede_musica2').val(),
      'ID_Salon2': $('#cbo-salon2').val(),
      'ID_Entidad_Profesor2': $('#cbo-profesor2').val(),
      'ID_Familia2': $('#cbo-categoria2').val(),
      'ID_Grupo_Clase2': $('#cbo-tipos_grupo_clase2').val(),
      'ID_Tipo_Clase2': $('#cbo-tipos_clase2').val(),
      'Fe_Matricula2': $('#txt-Fe_Matricula2').val(),
      'Nu_Estado2': $('#cbo-Estado2').val(),
      'Txt_Glosa2': $('[name="Txt_Glosa2"]').val(),
    }

    $("#table-alumno_horarios > tbody > tr").each(function () {
      fila = $(this);

      $ID_Horario_Clase2 = fila.find(".td-ID_Horario_Clase2").text();
      $ID_Entidad_Alumno2 = fila.find(".td-ID_Entidad_Alumno2").text();

      obj = {};

      obj.ID_Horario_Clase2 = $ID_Horario_Clase2;
      obj.ID_Entidad_Alumno2 = $ID_Entidad_Alumno2;

      arrAlumnoHorario.push(obj);
    })

    $('#btn-save').text('');
    $('#btn-save').attr('disabled', true);
    $('#btn-save').append('Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>');

    $('#modal-loader').modal('show');

    url = base_url + 'Escuela/MatriculaAlumnoController/crudPosMultiple';
    $.ajax({
      type: 'POST',
      dataType: 'JSON',
      url: url,
      data: {
        arrHeader: arrHeader,
        arrAlumnoHorario: arrAlumnoHorario,
      },
      success: function (response) {
        $('#modal-loader').modal('hide');

        $('.modal-message').removeClass('modal-danger modal-warning modal-success');
        $('#modal-message').modal('show');

        if (response.status == 'success') {
          accion_serie = '';

          $('.div-AgregarEditar').hide();
          $('.div-Listar').show();
          $('.modal-message').addClass(response.style_modal);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
          reload_table_serie();
        } else {
          $('.modal-message').addClass(response.style_modal);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1200);
        }

        $('#btn-save').text('');
        $('#btn-save').append('<span class="fa fa-save"></span> Guardar');
        $('#btn-save').attr('disabled', false);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        $('#modal-loader').modal('hide');
        $('.modal-message').removeClass('modal-danger modal-warning modal-success');

        $('#modal-message').modal('show');
        $('.modal-message').addClass('modal-danger');
        $('.modal-title-message').text(textStatus + ' [' + jqXHR.status + ']: ' + errorThrown);
        setTimeout(function () { $('#modal-message').modal('hide'); }, 1700);

        //Message for developer
        console.log(jqXHR.responseText);

        $('#btn-save').text('');
        $('#btn-save').append('<span class="fa fa-save"></span> Guardar');
        $('#btn-save').attr('disabled', false);
      }
    });
  }
}