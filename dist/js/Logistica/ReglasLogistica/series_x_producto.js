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

  url = base_url + 'Logistica/ReglasLogistica/SeriesxProductoController/ajax_list';
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
        data.Filtros_Marcas = $('#cbo-Filtros_Marcas').val(),
        data.Global_Filter = $('#txt-Global_Filter').val();
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

  $('#txt-Global_Filter').keyup(function () {
    table_serie.search($(this).val()).draw();
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
  
  $('#btn-addProductosEnlaces').click(function () {
    var $ID_Producto = $('#txt-AID').val();
    var $ID_Producto_Enlace = $('#txt-ACodigo').val();
    var $No_Producto_Enlace = $('#txt-ANombre').val();
    var $No_Serie_Producto = $('#txt-No_Serie_Producto').val().trim();
    $No_Serie_Producto = $No_Serie_Producto.toUpperCase();

    if ($ID_Producto.length === 0 || $No_Producto_Enlace.length === 0) {
      $('#txt-ANombre').closest('.form-group').find('.help-block').html('Ingresar producto');
      $('#txt-ANombre').closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ($No_Serie_Producto.length === 0) {
      $('#txt-No_Serie_Producto').closest('.form-group').find('.help-block').html('Ingresar serie');
      $('#txt-No_Serie_Producto').closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      var table_enlace_producto =
        "<tr id='tr_series_x_producto_add" + $ID_Producto_Enlace + "'>"
        + "<td class='text-left td-sSerieProducto' style='display:none;'>" + $No_Serie_Producto + "</td>"
        + "<td class='text-left'>" + $No_Producto_Enlace + "</td>"
        + "<td class='text-left'>" + $No_Serie_Producto + "</td>"
        + "<td class='text-left td-iIdProducto' style='display:none;'>" + $ID_Producto + "</td>"
        + "<td class='text-center'><button type='button' id='btn-deleteProductoEnlace' class='btn btn-xs btn-link' alt='Eliminar' title='Eliminar'><i class='fa fa-2x fa-trash-o' aria-hidden='true'></i></button></td>"
        + "</tr>";

      if (isExistTableTemporalEnlacesProducto($No_Serie_Producto)) {
        $('#txt-No_Serie_Producto').closest('.form-group').find('.help-block').html('Ya existe serie <b>' + $No_Serie_Producto + '</b>');
        $('#txt-No_Serie_Producto').closest('.form-group').removeClass('has-success').addClass('has-error');
        $('#txt-No_Serie_Producto').val('');
        $('#txt-No_Serie_Producto').focus();
      } else {
        $('.div-series_x_producto_add').show();
        $('#table-series_x_producto_add').show();
        $('#table-series_x_producto_add').append(table_enlace_producto);
        $('#txt-No_Serie_Producto').val('');
        $('#txt-No_Serie_Producto').focus();
      }
    }
  })

  $('#table-series_x_producto_add tbody').on('click', '#btn-deleteProductoEnlace', function () {
    $(this).closest('tr').remove();
    if ($('#table-series_x_producto_add >tbody >tr').length == 0)
      $('#table-series_x_producto_add').hide();
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

  $( '[name="EID_Series_x_Producto"]' ).val('');

  url = base_url + 'HelperController/getEmpresas';
  $.post(url, function (response) {
    $('#cbo-Empresas').html('<option value="0" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++)
      $('#cbo-Empresas').append('<option value="' + response[i].ID_Empresa + '">' + response[i].No_Empresa + '</option>');
  }, 'JSON');

  $('.div-boton_agregar_serie').show();
  //recorrer un for de todos los alumnos matriculados en el sede, salon y profesor seleccionado

  $('.div-series_x_producto_add').hide();
  $('#table-series_x_producto_add tbody').empty();
}

function isExistTableTemporalEnlacesProducto($No_Serie_Producto) {
  return Array.from($('tr[id*=tr_series_x_producto_add]')).some(element => ($('td:nth(0)', $(element)).html() === $No_Serie_Producto));
}

function verSeriexProducto(ID){
  accion_serie='upd_serie';
  
  $( '#form-Pos' )[0].reset();
  
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  
  $( '.help-block' ).empty();

  $('.div-Listar').hide();
  $('.div-AgregarEditar').show();

  $( '#modal-loader' ).modal('show');

  $('.div-series_x_producto_add').hide();
  $('#table-series_x_producto_add tbody').empty();

  url = base_url + 'Logistica/ReglasLogistica/SeriesxProductoController/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $( '#modal-loader' ).modal('hide');

      $( '.modal-title' ).text('Modificar Sede');
      
      $('[name="EID_Series_x_Producto"]').val(response.ID_Series_x_Producto);
      $('[name="ENo_Serie_Producto"]').val(response.No_Serie_Producto);
      
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

      $('.div-boton_agregar_serie').hide();

      $('[name="AID"]').val(response.ID_Producto);
      $('[name="ACodigo"]').val(response.Nu_Codigo_Barra);
      $('[name="ANombre"]').val(response.No_Producto);
      $('[name="No_Serie_Producto"]').val(response.No_Serie_Producto);
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

    var arrHeader = Array(), arrSeriesxProducto = Array();

    arrHeader = {
      'EID_Series_x_Producto': $('[name="EID_Series_x_Producto"]').val(),
      'ENo_Serie_Producto': $('[name="ENo_Serie_Producto"]').val(),
      'ID_Empresa': $('#cbo-Empresas').val(),
      'ID_Producto': $('#txt-AID').val(),
      'No_Serie_Producto': $('#txt-No_Serie_Producto').val(),
    }

    $("#table-series_x_producto_add > tbody > tr").each(function () {
      fila = $(this);

      $No_Serie_Producto = fila.find(".td-sSerieProducto").text();
      $ID_Producto = fila.find(".td-iIdProducto").text();

      obj = {};

      obj.ID_Producto = $ID_Producto;
      obj.No_Serie_Producto = $No_Serie_Producto;

      arrSeriesxProducto.push(obj);
    })

      $( '#btn-save' ).text('');
      $( '#btn-save' ).attr('disabled', true);
      $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
    
      $( '#modal-loader' ).modal('show');
      
      url = base_url + 'Logistica/ReglasLogistica/SeriesxProductoController/crudPos';
    	$.ajax({
        type		  : 'POST',
        dataType	: 'JSON',
    		url		    : url,
        data: {
          arrHeader: arrHeader,
          arrSeriesxProducto: arrSeriesxProducto,
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

function reload_table_serie(){
  table_serie.ajax.reload(null,false);
}