var url;
var table_impuesto_cruce_documento;

$(function () {
  $('.select2').select2();
  
	$(document).keyup(function(event){
    if(event.which == 27){//ESC
      $( "#modal-ValorImpuesto" ).modal('hide');
    }
  });

  url = base_url + 'Configuracion/ValorImpuestoController/ajax_list';
  table_impuesto_cruce_documento = $( '#table-ValorImpuesto' ).DataTable({
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
        data.filtro_empresa = $( '#cbo-filtro_empresa' ).val(),
        data.Filtros_Impuestos = $( '#cbo-Filtros_Impuestos' ).val(),
        data.Global_Filter = $( '#txt-Global_Filter' ).val();
      },
    },
    'columnDefs': [{
      'className' : 'text-center',
      'targets'   : 'no-sort',
      'orderable' : false,
    },],
    'lengthMenu': [[10, 100, 1000, -1], [10, 100, 1000, "Todos"]],
  });
  
  $( '#txt-Global_Filter' ).keyup(function() {
    table_impuesto_cruce_documento.search($(this).val()).draw();
  });
    
  $( '.dataTables_length' ).addClass('col-md-3');
  $( '.dataTables_paginate' ).addClass('col-md-9');

  $( '#form-ValorImpuesto' ).validate({
		rules:{
			No_Impuesto: {
				required: true
			},
			No_Impuesto_Breve: {
				required: true
			},
			Nu_Sunat_Codigo: {
				required: true
			},
			Nu_Tipo_Impuesto: {
				required: true
			},
		},
		messages:{
			No_Impuesto:{
				required: "Ingresar nombre",
			},
			No_Impuesto_Breve:{
				required: "Ingresar nombre breve",
			},
			Nu_Sunat_Codigo:{
				required: "Ingresar codigo",
			},
			Nu_Tipo_Impuesto:{
				required: "Ingresar tipo del 1 al 3",
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
		submitHandler: form_ValorImpuesto
	});
  
  $( '#cbo-filtro_empresa' ).html('<option value="0" selected="selected">- Todas -</option>');

  url = base_url + 'HelperController/getEmpresas';
  $.post( url , function( response ){
    $( '#cbo-filtro_empresa' ).html('<option value="0" selected="selected">- Todas -</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-filtro_empresa' ).append( '<option value="' + response[i].ID_Empresa + '">' + response[i].No_Empresa + '</option>' );
  }, 'JSON');
  
	$( '#cbo-filtro_empresa' ).change(function(){
    table_impuesto_cruce_documento.search($(this).val()).draw();
  });

	$( '#cbo-Empresas' ).change(function(){
	  $( '#cbo-TiposImpuesto' ).html('');
	  if ( $(this).val() > 0 ) {
      $( '#modal-loader' ).modal('show');
      url = base_url + 'HelperController/getImpuestos';
      var arrParams = {
        iIdEmpresa : $( '#cbo-Empresas' ).val()
      }
      $.post( url, arrParams, function( response ){
        $( '#modal-loader' ).modal('hide');
        $( '#cbo-TiposImpuesto' ).html('<option value="" selected="selected">- Seleccionar -</option>');
        for (var i = 0; i < response.length; i++)
          $( '#cbo-TiposImpuesto' ).append( '<option value="' + response[i].ID_Impuesto + '">' + ( response[i].No_Impuesto != null ? response[i].No_Impuesto : response[i].No_Impuesto_ )  + '</option>' );
      }, 'JSON');
	  }
	})
})

function agregarValorImpuesto(){
  $( '#form-ValorImpuesto' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  
  $( '.help-block' ).empty();
  
  $( '#modal-ValorImpuesto' ).modal('show');
  
  $( '.modal-title' ).text('Nuevo Impuesto');
  
  $( '[name="EID_Impuesto"]' ).val('');
  $( '[name="EID_Impuesto_Cruce_Documento"]' ).val('');
  $( '[name="ESs_Impuesto"]' ).val('');
  
  $( '#modal-loader' ).modal('show');
  url = base_url + 'HelperController/getEmpresas';
  $.post( url , function( response ){
    $( '#cbo-Empresas' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-Empresas' ).append( '<option value="' + response[i].ID_Empresa + '">' + response[i].No_Empresa + '</option>' );
    $( '#modal-loader' ).modal('hide');
  }, 'JSON');

  url = base_url + 'HelperController/getImpuestos';
  var arrParams = {
    iIdEmpresa : $( '#cbo-Empresas' ).val()
  }
  $.post( url, arrParams, function( response ){
    $( '#cbo-TiposImpuesto' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-TiposImpuesto' ).append( '<option value="' + response[i].ID_Impuesto + '">' + ( response[i].No_Impuesto != null ? response[i].No_Impuesto : response[i].No_Impuesto_ )  + '</option>' );
    $( '#modal-loader' ).modal('hide');
  }, 'JSON');
  
  $( '.div-Estado' ).hide();
  $( '#cbo-Estado' ).html( '<option value="1">Activo</option>' );
}

function verValorImpuesto(ID){
  $( '#form-ValorImpuesto' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '#modal-loader' ).modal('show');
 
  url = base_url + 'Configuracion/ValorImpuestoController/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $( '#modal-ValorImpuesto' ).modal('show');
      $( '.modal-title' ).text('Modifcar Impuesto');
      
      $( '[name="EID_Impuesto"]' ).val(response.ID_Impuesto);
      $( '[name="EID_Impuesto_Cruce_Documento"]' ).val(response.ID_Impuesto_Cruce_Documento);
      $( '[name="ESs_Impuesto"]' ).val(response.Ss_Impuesto);

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
      
      url = base_url + 'HelperController/getImpuestos';
      var arrParams = {
        'iIdEmpresa' : response.ID_Empresa,
      }
      $.post( url, arrParams, function( responseTiposImpuesto ){
        $( '#cbo-TiposImpuesto' ).html('');
        for (var i = 0; i < responseTiposImpuesto.length; i++){
          selected = '';
          if(response.ID_Impuesto == responseTiposImpuesto[i].ID_Impuesto)
            selected = 'selected="selected"';
          $( '#cbo-TiposImpuesto' ).append( '<option value="' + responseTiposImpuesto[i].ID_Impuesto + '" ' + selected + '>' + responseTiposImpuesto[i].No_Impuesto + '</option>' );
        }
      }, 'JSON');

      $('[name="Ss_Impuesto"]').val(response.Ss_Impuesto);
      $('[name="Po_Impuesto"]').val(response.Po_Impuesto);
      
      $( '.div-Estado' ).show();
      $( '#cbo-Estado' ).html( '' );
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

function form_ValorImpuesto(){
  if ( $( '#cbo-TiposImpuesto' ).val() == 0){
    $( '#cbo-TiposImpuesto' ).closest('.form-group').find('.help-block').html('Seleccionar tipo impuesto');
    $( '#cbo-TiposImpuesto' ).closest('.form-group').removeClass('has-success').addClass('has-error');
  } else {
    $( '#btn-save' ).text('');
    $( '#btn-save' ).attr('disabled', true);
    $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
  
    $( '#modal-loader' ).modal('show');
    
    url = base_url + 'Configuracion/ValorImpuestoController/crudValorImpuesto';
  	$.ajax({
      type		  : 'POST',
      dataType	: 'JSON',
  		url		    : url,
  		data		  : $('#form-ValorImpuesto').serialize(),
  		success : function( response ){
  		  $( '#modal-loader' ).modal('hide');
  		  
  	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
    	  $( '#modal-message' ).modal('show');
  		  
  		  if (response.status == 'success'){
  		    $('#modal-ValorImpuesto').modal('hide');
    	    $( '.modal-message' ).addClass(response.style_modal);
    	    $( '.modal-title-message' ).text(response.message);
    	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
    	    reload_table_impuesto_cruce_documento();
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

function eliminarValorImpuesto(ID){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });
  
  $( '#btn-save-delete' ).off('click').click(function () {
    $( '#modal-loader' ).modal('show');
    
    url = base_url + 'Configuracion/ValorImpuestoController/eliminarValorImpuesto/' + ID;
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
    	    $( '.modal-message' ).addClass(response.style_modal);
    	    $( '.modal-title-message' ).text(response.message);
    	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
    	    reload_table_impuesto_cruce_documento();
  		  } else {
    	    $( '.modal-message' ).addClass(response.style_modal);
    	    $( '.modal-title-message' ).text(response.message);
    	    setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
  		  }
      },
      error: function (jqXHR, textStatus, errorThrown) {
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
  });
}

function reload_table_impuesto_cruce_documento(){
  table_impuesto_cruce_documento.ajax.reload(null,false);
}