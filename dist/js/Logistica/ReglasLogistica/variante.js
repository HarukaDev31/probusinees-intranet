var save_method;
var url;
var table_marca, table_marca_detalle;
var accion_marca;

$(function () {
	$(document).keyup(function(event){
    if(event.which == 27){//ESC
      $( "#modal-Marca" ).modal('hide');
    }
	});
	
  $('.select2').select2();
  $('[data-mask]').inputmask()
  
  url = base_url + 'Logistica/ReglasLogistica/VarianteController/ajax_list';
  table_marca = $( '#table-Marca' ).DataTable({
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
        data.Filtros_Marcas = $( '#cbo-Filtros_Marcas' ).val(),
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
    table_marca.search($(this).val()).draw();
  });
  
  $( '#form-Marca' ).validate({
    rules: {
      ID_Tabla_Dato: {
        required: true,
      },
			No_Variante: {
				required: true,
			},
		},
    messages: {
      ID_Tabla_Dato: {
        required: "Seleccionar tipo"
      },
			No_Variante:{
				required: "Ingresar nombre"
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
		submitHandler: form_Marca
	});
	
  $(document).bind('keydown', 'f2', function(){
    agregarMarca();
  });



  $('#form-MarcaDetalle').validate({
    rules: {
      ID_Variante_Item: {
        required: true,
      },
      No_Valor: {
        required: true,
      },
    },
    messages: {
      ID_Variante_Item: {
        required: "Seleccionar variante"
      },
      No_Valor: {
        required: "Ingresar valor"
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
    submitHandler: form_Marca_Detalle
  });


  url = base_url + 'Logistica/ReglasLogistica/VarianteController/ajax_list_detalle';
  table_marca_detalle = $('#table-Marca_detalle').DataTable({
    'dom': 'B<"top">frt<"bottom"lp><"clear">',
    buttons: [{
      extend: 'excel',
      text: '<i class="fa fa-file-excel-o color_icon_excel"></i> Excel',
      titleAttr: 'Excel',
      exportOptions: {
        columns: ':visible'
      }
    },
    {
      extend: 'pdf',
      text: '<i class="fa fa-file-pdf-o color_icon_pdf"></i> PDF',
      titleAttr: 'PDF',
      exportOptions: {
        columns: ':visible'
      }
    },
    {
      extend: 'colvis',
      text: '<i class="fa fa-ellipsis-v"></i> Columnas',
      titleAttr: 'Columnas',
      exportOptions: {
        columns: ':visible'
      }
    }],
    'searching': false,
    'bStateSave': true,
    'processing': true,
    'serverSide': true,
    'info': true,
    'autoWidth': false,
    'pagingType': 'full_numbers',
    'oLanguage': {
      'sInfo': 'Mostrando (_START_ - _END_) total de registros _TOTAL_',
      'sLengthMenu': '_MENU_',
      'sSearch': 'Buscar por: ',
      'sSearchPlaceholder': 'UPC / Nombre',
      'sZeroRecords': 'No se encontraron registros',
      'sInfoEmpty': 'No hay registros',
      'sLoadingRecords': 'Cargando...',
      'sProcessing': 'Procesando...',
      'oPaginate': {
        'sFirst': '<<',
        'sLast': '>>',
        'sPrevious': '<',
        'sNext': '>',
      },
    },
    'order': [],
    'ajax': {
      'url': url,
      'type': 'POST',
      'dataType': 'json',
      'data': function (data) {
        data.Filtros_Marcas_Detalle = $('#cbo-Filtros_Marcas_Detalle').val(),
        data.Global_Filter_Detalle = $('#txt-Global_Filter_Detalle').val();
      },
    },
    'columnDefs': [{
      'className': 'text-center',
      'targets': 'no-sort',
      'orderable': false,
    },],
  });

  $('.dataTables_length').addClass('col-md-3');
  $('.dataTables_paginate').addClass('col-md-9');

  $('#txt-Global_Filter_Detalle').keyup(function () {
    table_marca_detalle.search($(this).val()).draw();
  });
})

function agregarMarca(){
  accion_marca='add_marca';
  
  $( '#modal-loader' ).modal('show');
  
  $( '#form-Marca' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '#modal-Marca' ).modal('show');
  $( '.modal-title' ).text('Nueva Variante');
  
	$( '#modal-Marca' ).on('shown.bs.modal', function() {
		$( '#txt-No_Variante' ).focus();
	})
  
  $( '[name="EID_Empresa"]' ).val('');
  $('[name="EID_Variante_Item"]').val('');
  $('[name="ENo_Variante"]').val('');
  $( '[name="EID_Tabla_Dato"]' ).val('');
  
  $( '#modal-loader' ).modal('show');
  url = base_url + 'HelperController/getEmpresas';
  $.post( url , function( response ){
    $( '#cbo-Empresas' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-Empresas' ).append( '<option value="' + response[i].ID_Empresa + '">' + response[i].No_Empresa + '</option>' );
    $( '#modal-loader' ).modal('hide');
  }, 'JSON');

  $('#cbo-tipo_variante').html('<option value="" selected="selected">- Sin registro -</option>');
  url = base_url + 'HelperController/getTipoVarianteTablaDato';
  var arrPost = {};
  $.post(url, arrPost, function (response) {
    if (response.sStatus == 'success') {
      var l = response.arrData.length;
      if (l == 1) {
        $('#cbo-tipo_variante').html('<option value="' + response.arrData[0].ID + '">' + response.arrData[0].Nombre + '</option>');
      } else {
        $('#cbo-tipo_variante').html('<option value="" selected="selected">- Seleccionar -</option>');
        for (var x = 0; x < l; x++)
          $('#cbo-tipo_variante').append('<option value="' + response.arrData[x].ID + '">' + response.arrData[x].Nombre + '</option>');
      }
    } else {
      if (response.sMessageSQL !== undefined) {
        console.log(response.sMessageSQL);
      }
    }
  }, 'JSON');

  $( '.div-Estado' ).hide();
  $( '#cbo-Estado' ).html( '<option value="1">Activo</option>' );
 
  save_method = 'add';
}

function agregarDetalle() {
  accion_marca = 'add_marca';

  $('#form-MarcaDetalle')[0].reset();
  $('.form-group').removeClass('has-error');
  $('.form-group').removeClass('has-success');
  $('.help-block').empty();

  $('#modal-MarcaDetalle').modal('show');
  $('.modal-title').text('Nueva Variante Detalle');

  $('#modal-MarcaDetalle').on('shown.bs.modal', function () {
    $('#txt-No_Valor').focus();
  })

  $('[name="EID_Empresa_Detalle"]').val('');
  $('[name="EID_Variante_Item"]').val('');
  $('[name="EID_Variante_Item_Detalle"]').val('');
  $('[name="ENo_Valor"]').val('');

  $('#modal-loader').modal('show');
  url = base_url + 'HelperController/getEmpresas';
  $.post(url, function (response) {
    $('#cbo-Empresas_Detalle').html('<option value="0" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++)
      $('#cbo-Empresas_Detalle').append('<option value="' + response[i].ID_Empresa + '">' + response[i].No_Empresa + '</option>');
    $('#modal-loader').modal('hide');
  }, 'JSON');

  $('#cbo-variante').html('<option value="" selected="selected">- Sin registro -</option>');
  url = base_url + 'HelperController/getVariante';
  var arrPost = {};
  $.post(url, arrPost, function (response) {
    if (response.sStatus == 'success') {
      var l = response.arrData.length;
      if (l == 1) {
        $('#cbo-variante').html('<option value="' + response.arrData[0].ID + '">' + response.arrData[0].Nombre + '</option>');
      } else {
        $('#cbo-variante').html('<option value="" selected="selected">- Seleccionar -</option>');
        for (var x = 0; x < l; x++)
          $('#cbo-variante').append('<option value="' + response.arrData[x].ID + '">' + response.arrData[x].Nombre + '</option>');
      }
    } else {
      if (response.sMessageSQL !== undefined) {
        console.log(response.sMessageSQL);
      }
    }
  }, 'JSON');

  $('.div-Estado_Detalle').hide();
  $('#cbo-Estado_Detalle').html('<option value="1">Activo</option>');

  save_method = 'add';
}

function verMarca(ID){
  accion_marca='upd_marca';
  
  $( '#form-Marca' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '#modal-loader' ).modal('show');
  
	save_method = 'update';
	
  url = base_url + 'Logistica/ReglasLogistica/VarianteController/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $( '#modal-Marca' ).modal('show');
      $('.modal-title').text('Modificar Variante Detalle');
  
    	$( '#modal-Marca' ).on('shown.bs.modal', function() {
    		$( '#txt-No_Variante' ).focus();
    	})
      
      $( '[name="EID_Empresa"]' ).val(response.ID_Empresa);
      $( '[name="EID_Variante_Item"]' ).val(response.ID_Variante_Item);
      $( '[name="ENo_Variante"]' ).val(response.No_Variante);
      $('[name="EID_Tabla_Dato"]').val(response.ID_Tabla_Dato);
      
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

      $('#cbo-tipo_variante').html('<option value="" selected="selected">- Sin registro -</option>');
      url = base_url + 'HelperController/getTipoVarianteTablaDato';
      var arrPost = {};
      $.post(url, arrPost, function (responseTipoVariante) {
        if (responseTipoVariante.sStatus == 'success') {
          var l = responseTipoVariante.arrData.length;
          if (l == 1) {
            $('#cbo-tipo_variante').html('<option value="' + responseTipoVariante.arrData[0].ID + '">' + responseTipoVariante.arrData[0].Nombre + '</option>');
          } else {
            $('#cbo-tipo_variante').html('<option value="" selected="selected">- Seleccionar -</option>');
            for (var x = 0; x < l; x++) {
              selected = '';
              if (responseTipoVariante.arrData[x].ID == response.ID_Tabla_Dato)
                selected = 'selected';
              $('#cbo-tipo_variante').append('<option value="' + responseTipoVariante.arrData[x].ID + '" ' + selected + '>' + responseTipoVariante.arrData[x].Nombre + '</option>');
            }
          }
        } else {
          if (responseTipoVariante.sMessageSQL !== undefined) {
            console.log(responseTipoVariante.sMessageSQL);
          }
        }
      }, 'JSON');

      $( '[name="No_Variante"]' ).val(response.No_Variante);
      
      $( '.div-Estado' ).show();
      $( '#cbo-Estado' ).html( '' );
      
      var selected='';
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

function verMarcaDetalle(ID) {
  accion_marca = 'upd_marca';

  $('#form-MarcaDetalle')[0].reset();
  $('.form-group').removeClass('has-error');
  $('.form-group').removeClass('has-success');
  $('.help-block').empty();

  $('#modal-loader').modal('show');

  save_method = 'update';

  url = base_url + 'Logistica/ReglasLogistica/VarianteController/ajax_edit_detalle/' + ID;
  $.ajax({
    url: url,
    type: "GET",
    dataType: "JSON",
    success: function (response) {
      $('#modal-MarcaDetalle').modal('show');
      $('.modal-title').text('Modificar Variante Detalle');

      $('#modal-MarcaDetalle').on('shown.bs.modal', function () {
        $('#txt-No_Valor').focus();
      })

      $('[name="EID_Empresa_Detalle"]').val(response.EID_Empresa);
      $('[name="EID_Variante_Item"]').val(response.ID_Variante_Item);
      $('[name="EID_Variante_Item_Detalle"]').val(response.ID_Variante_Item_Detalle);
      $('[name="ENo_Valor"]').val(response.No_Valor);

      var selected;
      url = base_url + 'HelperController/getEmpresas';
      $.post(url, function (responseEmpresa) {
        $('#cbo-Empresas_Detalle').html('');
        for (var i = 0; i < responseEmpresa.length; i++) {
          selected = '';
          if (response.ID_Empresa == responseEmpresa[i].ID_Empresa)
            selected = 'selected="selected"';
          $('#cbo-Empresas_Detalle').append('<option value="' + responseEmpresa[i].ID_Empresa + '" ' + selected + '>' + responseEmpresa[i].No_Empresa + '</option>');
        }
      }, 'JSON');

      $('#cbo-variante').html('<option value="" selected="selected">- Sin registro -</option>');
      url = base_url + 'HelperController/getVariante';
      var arrPost = {};
      $.post(url, arrPost, function (responseVariante) {
        if (responseVariante.sStatus == 'success') {
          var l = responseVariante.arrData.length;
          if (l == 1) {
            $('#cbo-variante').html('<option value="' + responseVariante.arrData[0].ID + '">' + responseVariante.arrData[0].Nombre + '</option>');
          } else {
            $('#cbo-variante').html('<option value="" selected="selected">- Seleccionar -</option>');
            for (var x = 0; x < l; x++) {
              selected = '';
              if (responseVariante.arrData[x].ID == response.ID_Variante_Item)
                selected = 'selected';
              $('#cbo-variante').append('<option value="' + responseVariante.arrData[x].ID + '" ' + selected + '>' + responseVariante.arrData[x].Nombre + '</option>');
            }
          }
        } else {
          if (responseVariante.sMessageSQL !== undefined) {
            console.log(responseVariante.sMessageSQL);
          }
        }
      }, 'JSON');

      $('[name="No_Valor"]').val(response.No_Valor);

      $('.div-Estado_Detalle').show();
      $('#cbo-Estado_Detalle').html('');

      var selected = '';
      for (var i = 0; i < 2; i++) {
        selected = '';
        if (response.Nu_Estado == i)
          selected = 'selected="selected"';
        $('#cbo-Estado_Detalle').append('<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Inactivo' : 'Activo') + '</option>');
      }

      $('#modal-loader').modal('hide');
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
    }
  });
}

function form_Marca(){
  if ( accion_marca=='add_marca' || accion_marca=='upd_marca' ) {
    $( '#btn-save' ).text('');
    $( '#btn-save' ).attr('disabled', true);
    $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
    
    $( '#modal-loader' ).modal('show');
    
    url = base_url + 'Logistica/ReglasLogistica/VarianteController/crudMarca';
    
    $.ajax({
      dataType  : 'JSON',
      type      : 'POST',
      url       : url,
		  data		  : $( '#form-Marca' ).serialize(),
      success: function(response) {
  		  $( '#modal-loader' ).modal('hide');
  		  
  	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
    	  $( '#modal-message' ).modal('show');
  		  
  		  if (response.status == 'success'){
  		    accion_marca='';
  		    $('#modal-Marca').modal('hide');
    	    $( '.modal-message' ).addClass(response.style_modal);
    	    $( '.modal-title-message' ).text(response.message);
    	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
    	    reload_table_marca();
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

function eliminarMarca(ID, accion_marca){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });
  
  $(document).bind('keydown', 'alt+l', function(){
    if ( accion_marca=='delete' ) {
      _eliminarMarca($modal_delete, ID);
      accion_marca='';
    }
  });

  $( '#btn-save-delete' ).off('click').click(function () {
    _eliminarMarca($modal_delete, ID);
  });
}

function _eliminarMarca($modal_delete, ID){
  $( '#modal-loader' ).modal('show');
  
  url = base_url + 'Logistica/ReglasLogistica/VarianteController/eliminarMarca/' + ID;
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
		    accion_marca='';
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
  	    reload_table_marca();
		  } else {
		    accion_marca='';
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
		  }
    },
    error: function (jqXHR, textStatus, errorThrown) {
		  accion_marca='';
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

function reload_table_marca(){
  table_marca.ajax.reload(null,false);
}


function form_Marca_Detalle() {
  if (accion_marca == 'add_marca' || accion_marca == 'upd_marca') {
    $('#btn-save').text('');
    $('#btn-save').attr('disabled', true);
    $('#btn-save').append('Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>');

    $('#modal-loader').modal('show');

    url = base_url + 'Logistica/ReglasLogistica/VarianteController/crudMarcaDetalle';

    $.ajax({
      dataType: 'JSON',
      type: 'POST',
      url: url,
      data: $('#form-MarcaDetalle').serialize(),
      success: function (response) {
        $('#modal-loader').modal('hide');

        $('.modal-message').removeClass('modal-danger modal-warning modal-success');
        $('#modal-message').modal('show');

        if (response.status == 'success') {
          //accion_marca = '';
          //$('#modal-MarcaDetalle').modal('hide');
          $('.modal-message').addClass(response.style_modal);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
          reload_table_marca_detalle();
        } else {
          $('.modal-message').addClass(response.style_modal);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 5200);
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

function eliminarMarcaDetalle(ID, accion_marca) {
  var $modal_delete = $('#modal-message-delete');
  $modal_delete.modal('show');

  $('#btn-cancel-delete').off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $(document).bind('keydown', 'alt+l', function () {
    if (accion_marca == 'delete') {
      _eliminarMarcaDetalle($modal_delete, ID);
      accion_marca = '';
    }
  });

  $('#btn-save-delete').off('click').click(function () {
    _eliminarMarcaDetalle($modal_delete, ID);
  });
}

function _eliminarMarcaDetalle($modal_delete, ID) {
  $('#modal-loader').modal('show');

  url = base_url + 'Logistica/ReglasLogistica/VarianteController/eliminarMarcaDetalle/' + ID;
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
        accion_marca = '';
        $('.modal-message').addClass(response.style_modal);
        $('.modal-title-message').text(response.message);
        setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
        reload_table_marca_detalle();
      } else {
        accion_marca = '';
        $('.modal-message').addClass(response.style_modal);
        $('.modal-title-message').text(response.message);
        setTimeout(function () { $('#modal-message').modal('hide'); }, 1500);
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      accion_marca = '';
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
}

function reload_table_marca_detalle() {
  table_marca_detalle.ajax.reload(null, false);
}