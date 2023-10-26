var url;
var table_familia;
var accion_familia;

$(function () {
	$(document).keyup(function(event){
    if(event.which == 27){//ESC
      $( "#modal-Categoria" ).modal('hide');
    }
	});

  $('.select2').select2();
  $('[data-mask]').inputmask();
  
  url = base_url + 'TiendaVirtual/SubCategoriasTiendaVirtualController/ajax_list';
  table_familia = $( '#table-Categoria' ).DataTable({
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
      'dataType'  : 'json',
      'data'      : function ( data ) {
        data.Filtros_Categorias = $( '#cbo-Filtros_Categorias' ).val(),
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
    table_familia.search($(this).val()).draw();
  });
  
  $( '#form-Categoria' ).validate({
    rules: {
      ID_Familia: {
        required: true,
      },
			No_Sub_Familia: {
				required: true,
			},
		},
    messages: {
      ID_Familia: {
        required: "Seleccionar categoría"
      },
			No_Sub_Familia:{
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
		submitHandler: form_Categoria
	});
	
  $(document).bind('keydown', 'f2', function(){
    agregarCategoria();
  });
})

function agregarCategoria(){
  accion_familia='add_familia';
  
  $( '#modal-loader' ).modal('show');
  
  $( '#form-Categoria' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '#modal-Categoria' ).modal('show');
  $( '.modal-title' ).text('Nueva Categoría');
  
  $('[name="EID_Empresa"]').val('');
  $('[name="EID_Sub_Familia"]').val('');
  $('[name="ENo_Sub_Familia"]').val('');
  
	$( '#modal-Categoria' ).on('shown.bs.modal', function() {
		$( '#txt-No_Sub_Familia' ).focus();
	})
  
  $( '#modal-loader' ).modal('show');
  url = base_url + 'HelperController/getEmpresas';
  $.post( url , function( response ){
    $( '#cbo-Empresas' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-Empresas' ).append( '<option value="' + response[i].ID_Empresa + '">' + response[i].No_Empresa + '</option>' );
    $( '#modal-loader' ).modal('hide');
  }, 'JSON');

  $('.div-Estado').hide();
  $('#cbo-Estado').html('<option value="1">Activo</option>');

  url = base_url + 'HelperTiendaVirtualController/getCategorias';
  $.post(url, { sTipoData: 'categoria' }, function (response) {
    if (response.sStatus == 'success') {
      var l = response.arrData.length;
      if (l == 1) {
        $('#cbo-categoria').html('<option value="' + response.arrData[0].ID + '">' + response.arrData[0].Nombre + '</option>');
      } else {
        $('#cbo-categoria').html('<option value="" selected="selected">- Seleccionar -</option>');
        for (var x = 0; x < l; x++) {
          $('#cbo-categoria').append('<option value="' + response.arrData[x].ID + '">' + response.arrData[x].Nombre + '</option>');
        }
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
    $('#modal-loader').modal('hide');
  }, 'JSON');

  $( '#modal-loader' ).modal('hide');
}

function verCategoria(ID){
  accion_familia='upd_familia';
  
  $( '#form-Categoria' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '#modal-loader' ).modal('show');
 
  url = base_url + 'TiendaVirtual/SubCategoriasTiendaVirtualController/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $( '#modal-Categoria' ).modal('show');
      $( '.modal-title' ).text('Modifcar Categoría');
  
    	$( '#modal-Categoria' ).on('shown.bs.modal', function() {
    		$( '#txt-No_Sub_Familia' ).focus();
    	})
      
      $( '[name="EID_Empresa"]' ).val(response.ID_Empresa);
      $( '[name="EID_Sub_Familia"]' ).val(response.ID_Sub_Familia);
      $( '[name="ENo_Sub_Familia"]').val(response.No_Sub_Familia);

      url = base_url + 'HelperTiendaVirtualController/getCategorias';
      $.post(url, { sTipoData: 'categoria' }, function (responseCategoria) {
        $('#cbo-categoria').html('<option value="">- Seleccionar -</option>');
        if (responseCategoria.sStatus == 'success') {
          var l = responseCategoria.arrData.length;
          for (var x = 0; x < l; x++) {
            selected = '';
            if (response.ID_Familia == responseCategoria.arrData[x].ID)
              selected = 'selected="selected"';
            $('#cbo-categoria').append('<option value="' + responseCategoria.arrData[x].ID + '" ' + selected + '>' + responseCategoria.arrData[x].Nombre + '</option>');
          }
        } else {
          if (responseCategoria.sMessageSQL !== undefined) {
            console.log(responseCategoria.sMessageSQL);
          }
          if (responseCategoria.sStatus == 'warning')
            $('#cbo-categoria').html('<option value="0" selected="selected">- Vacío -</option>');
        }
      }, 'JSON');

      $('[name="No_Sub_Familia"]').val(response.No_Sub_Familia);
      
      $( '.div-Estado' ).show();
      $( '#cbo-Estado' ).html( '' );
      
      var selected='';
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

      for (var i = 0; i < 2; i++){
        selected = '';
        if (response.Nu_Activar_Familia_Lae_Shop == i)
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

function form_Categoria(){
  if ( accion_familia=='add_familia' || accion_familia=='upd_familia' ) {
    $( '#btn-save' ).text('');
    $( '#btn-save' ).attr('disabled', true);
    $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
  
    $( '#modal-loader' ).modal('show');
    
    url = base_url + 'TiendaVirtual/SubCategoriasTiendaVirtualController/crudCategoria';
  	$.ajax({
      type		  : 'POST',
      dataType	: 'JSON',
  		url		    : url,
  		data		  : $('#form-Categoria').serialize(),
  		success : function( response ){
  		  $( '#modal-loader' ).modal('hide');
  		  
  	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
    	  $( '#modal-message' ).modal('show');
  		  
  		  if (response.status == 'success'){
  		    accion_familia='';
  		    $('#modal-Categoria').modal('hide');
    	    $( '.modal-message' ).addClass(response.style_modal);
    	    $( '.modal-title-message' ).text(response.message);
    	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
    	    reload_table_familia();
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

function eliminarCategoria(ID, accion_familia){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });
  
  $(document).bind('keydown', 'esc', function(){
    if ( accion_familia=='delete' ) {
      _eliminarCategoria($modal_delete, ID);
      accion_familia='';
    }
  });

  $( '#btn-save-delete' ).off('click').click(function () {
    _eliminarCategoria($modal_delete, ID);
  });
}

function _eliminarCategoria($modal_delete, ID){
  $( '#modal-loader' ).modal('show');
  
  url = base_url + 'TiendaVirtual/SubCategoriasTiendaVirtualController/eliminarCategoria/' + ID;
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
		    accion_familia='';
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
  	    reload_table_familia();
		  } else {
		    accion_familia='';
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
		  }
    },
    error: function (jqXHR, textStatus, errorThrown) {
		  accion_familia='';
		  
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

function reload_table_familia(){
  table_familia.ajax.reload(null,false);
}