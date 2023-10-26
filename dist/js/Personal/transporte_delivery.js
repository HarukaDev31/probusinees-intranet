var url;
var table_transporte_delivery;

$(function () {
  $('.select2').select2();
  $('[data-mask]').inputmask()
  
  //LAE API SUNAT / RENIEC
  $( '#btn-cloud-api_transporte_delivery' ).click(function(){
    if ( $( '#txt-Nu_Documento_Identidad').val().length < 8 ) {
      $( '#txt-Nu_Documento_Identidad' ).closest('.form-group').find('.help-block').html('Debe ingresar 8 dígitos' );
  	  $( '#txt-Nu_Documento_Identidad' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      $( '#btn-cloud-api_transporte_delivery' ).text('');
      $( '#btn-cloud-api_transporte_delivery' ).attr('disabled', true);
      $( '#btn-cloud-api_transporte_delivery' ).append( '<i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
      
      $.post( base_url + 'HelperController/getToken', function( arrData ){
        var url_api = 'https://www.laesystems.com/librerias/reniec/partner/format/json/x-api-key/' + arrData.Txt_Token;
        
        var data = {
          ID_Tipo_Documento_Identidad : 2,
          Nu_Documento_Identidad      : $( '#txt-Nu_Documento_Identidad' ).val(),
        };
        
        $.ajax({
          url   : url_api,
          type  :'POST',
          data  : data,
          success: function(response){
            if (response.success === true){
              $('[name="No_Transportista"]').val( response.data.No_Names );
            } else {
              $('[name="No_Transportista"]').val( '' );
              $( '#txt-Nu_Documento_Identidad' ).closest('.form-group').find('.help-block').html(response.msg);
          	  $( '#txt-Nu_Documento_Identidad' ).closest('.form-group').removeClass('has-success').addClass('has-error');
            }
            
    		  	$( '#txt-Nu_Documento_Identidad' ).focus();
    		  	
            $( '#btn-cloud-api_transporte_delivery' ).text('');
            $( '#btn-cloud-api_transporte_delivery' ).attr('disabled', false);
            $( '#btn-cloud-api_transporte_delivery' ).append( '<i class="fa fa-cloud-download fa-lg"></i>' );
          },
          error: function(response){
            $( '#btn-cloud-api_transporte_delivery' ).closest('.form-group').find('.help-block').html('Sin acceso');
        	  $( '#btn-cloud-api_transporte_delivery' ).closest('.form-group').removeClass('has-success').addClass('has-error');
        	  
            $( '[name="No_Transportista"]' ).val( '' );
            
            $( '#btn-cloud-api_transporte_delivery' ).text('');
            $( '#btn-cloud-api_transporte_delivery' ).attr('disabled', false);
            $( '#btn-cloud-api_transporte_delivery' ).append( '<i class="fa fa-cloud-download fa-lg"></i>' );
          }
        });
      }, 'JSON');
    }
  })
  
  url = base_url + 'Personal/Transporte_delivery_controller/ajax_list';
  table_transporte_delivery = $( '#table-Transporte_Delivery' ).DataTable({
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
      'sInfo'                 : 'Mostrando (_START_ - _END_) total de registros _TOTAL_',
      'sLengthMenu'           : '_MENU_',
      'sSearch'               : 'Buscar por: ',
      'sSearchPlaceholder'    : 'UPC / Nombre',
      'sZeroRecords'          : 'No se encontraron registros',
      'sInfoEmpty'            : 'No hay registros',
      'sLoadingRecords'       : 'Cargando...',
      'sProcessing'           : 'Procesando...',
      'oPaginate'             : {
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
          data.Filtros_Transporte_Deliverys = $( '#cbo-Filtros_Transporte_Deliverys' ).val(),
          data.Global_Filter = $( '#txt-Global_Filter' ).val();
        },
    },
    'columnDefs': [{
        'className'     : 'text-center',
        'targets'       : 'no-sort',
        'orderable'     : false,
    },],
  });
  
  $( '.dataTables_length' ).addClass('col-md-3');
  $( '.dataTables_info' ).addClass('col-md-3');
  $( '.dataTables_paginate' ).addClass('col-md-6');
  
  $( '#txt-Global_Filter' ).keyup(function() {
    table_transporte_delivery.search($(this).val()).draw();
  });
  
  $( '#form-Transporte_Delivery' ).validate({
		rules:{
			Nu_Documento_Identidad: {
				required: true,
				minlength: 8,
				maxlength: 8
			},
			No_Transportista: {
				required: true
			},
		},
		messages:{
			Nu_Documento_Identidad:{
				required: "Ingresar número",
				minlength: "Debe ingresar 8 dígitos",
				maxlength: "Debe ingresar 8 dígitos"
			},
			No_Transportista:{
				required: "Ingresar nombre",
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
		submitHandler: form_Transporte_Delivery
	});
})

function agregarTransporte_Delivery(){
  $( '#form-Transporte_Delivery' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '#modal-Transporte_Delivery' ).modal('show');
  $( '.modal-title' ).text('Nuevo Personal');
  
  $('[name="EID_Empresa"]').val('');
  $('[name="EID_Transporte_Delivery"]').val('');
  $('[name="ENu_Documento_Identidad"]').val('');
  
  $( '#modal-loader' ).modal('show');
  
  $( '#cbo-Estado' ).html( '<option value="1">Activo</option>' );
  $( '#cbo-Estado' ).append( '<option value="0">Inactivo</option>' );
  
  $( '#modal-loader' ).modal('hide');
}

function verTransporte_Delivery(ID){
  $( '#form-Transporte_Delivery' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '#modal-loader' ).modal('show');
 
  url = base_url + 'Personal/Transporte_delivery_controller/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $( '#modal-Transporte_Delivery' ).modal('show');
      $( '.modal-title' ).text('Modifcar Personal');
      
      $('[name="EID_Empresa"]').val(response.ID_Empresa);
      $('[name="EID_Transporte_Delivery"]').val(response.ID_Transporte_Delivery);
      $('[name="ENu_Documento_Identidad"]').val(response.Nu_Documento_Identidad);
      
      $('[name="Nu_Documento_Identidad"]').val(response.Nu_Documento_Identidad);
      $('[name="No_Transportista"]').val(response.No_Transportista);
  
      $('[name="Nu_Celular"]').val(response.Nu_Celular);
      
      $('[name="Txt_Direccion"]').val(response.Txt_Direccion);
      
      $( '#cbo-Estado' ).html( '' );
      for (var i = 0; i < 2; i++){
        selected = '';
        if(response.Nu_Estado == i)
          selected = 'selected="selected"';
        $( '#cbo-Estado' ).append( '<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Inactivo' : 'Activo') + '</option>' );
      }
      
      $( '#modal-loader' ).modal('hide');
    }
  });
}

function form_Transporte_Delivery(){
  $( '#btn-save' ).text('');
  $( '#btn-save' ).attr('disabled', true);
  $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

  $( '#modal-loader' ).modal('show');
  
  url = base_url + 'Personal/Transporte_delivery_controller/crudTransporte_Delivery';
	$.ajax({
    type		  : 'POST',
    dataType	: 'JSON',
		url		    : url,
		data		  : $('#form-Transporte_Delivery').serialize(),
		success : function( response ){
		  $( '#modal-loader' ).modal('hide');
		  
	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
  	  $( '#modal-message' ).modal('show');
		  
		  if (response.status == 'success'){
		    $('#modal-Transporte_Delivery').modal('hide');
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
  	    reload_table_transporte_delivery();
		  } else {
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1200);
		  }
	  
      $( '#btn-save' ).text('');
      $( '#btn-save' ).append( '<span class="fa fa-save"></span> Guardar' );
      $( '#btn-save' ).attr('disabled', false);
		}
	});
}

function eliminarTransporte_Delivery(ID){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $(document).keyup(function(event){
    if(event.which === 13)//Tecla ENTER
      eliminarData_Transporte_Delivery($modal_delete, ID);
  });

  $( '#btn-save-delete' ).off('click').click(function () {
    eliminarData_Transporte_Delivery($modal_delete, ID);
  });
}

function reload_table_transporte_delivery(){
  table_transporte_delivery.ajax.reload(null,false);
}

function eliminarData_Transporte_Delivery($modal_delete, ID){
  $( '#modal-loader' ).modal('show');
  
  url = base_url + 'Personal/Transporte_delivery_controller/eliminarTransporte_Delivery/' + ID;
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
  	    reload_table_transporte_delivery();
		  } else {
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
		  }
    }
  });
}