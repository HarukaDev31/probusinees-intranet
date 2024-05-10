var url, table_Entidad;

$(function () {
  url = base_url + 'CargaConsolidada/Consolidado/ajax_list';
  table_Entidad = $( '#table-Cliente' ).DataTable({
    dom: "<'row'<'col-sm-12 col-md-4'B><'col-sm-12 col-md-7'f><'col-sm-12 col-md-1'>>" +
    "<'row'<'col-sm-12'tr>>" +
    "<'row'<'col-sm-12 col-md-2'l><'col-sm-12 col-md-5'i><'col-sm-12 col-md-5'p>>",
    buttons     : [{
      extend    : 'excel',
      text      : '<i class="fa fa-file-excel color_icon_excel"></i> Excel',
      titleAttr : 'Excel',
      exportOptions: {
        columns: ':visible'
      }
    },
    {
      extend    : 'pdf',
      text      : '<i class="fa fa-file-pdf color_icon_pdf"></i> PDF',
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
    "paging": true,
    "lengthChange": true,
    "searching": true,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "responsive": false,
    'pagingType'  : 'full_numbers',
    'oLanguage' : {
      'sInfo'              : 'Mostrando (_START_ - _END_) total de registros _TOTAL_',
      'sLengthMenu'        : '_MENU_',
      'sSearch'            : 'Buscar por: ',
      'sSearchPlaceholder' : '',
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
      'dataType'  : 'JSON',
      'data'      : function ( data ) {
        data.Filtros_Entidades = $( '#cbo-Filtros_Entidades' ).val(),
        data.Global_Filter = $( '#txt-Global_Filter' ).val();
      },
    },
    'columnDefs': [
      {
        'targets': 'no-hidden',
        "visible": false, 
      },{
      'className' : 'text-center',
      'targets'   : 'no-sort',
      'orderable' : false,
    },],
    'lengthMenu': [[10, 100, 1000, -1], [10, 100, 1000, "Todos"]],
  });

  $('#table-Cliente_filter input').removeClass('form-control-sm');
  $('#table-Cliente_filter input').addClass('form-control-md');
  $('#table-Cliente_filter input').addClass("width_full");
  
  $( "#form-Cliente" ).validate({
		rules:{
			ID_Moneda: {
				required: true
			},
			No_Carga_Consolidada: {
				required: true,
				maxlength: 100
			},
		},
		messages:{
			ID_Moneda:{
				required: "Elegir moneda",
			},
			No_Carga_Consolidada:{
				required: "Ingresar nombre",
				maxlength: "Máximo 100 carácteres"
			},
			Nu_Estado:{
				required: "Elegir estado",
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
		submitHandler: form_Entidad
	});
})

function agregarCliente(){
  $( '#form-Cliente' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '.div-Listar' ).hide();
  $( '.div-AgregarEditar' ).show();
		
  $( '[name="EID_Carga_Consolidada"]' ).val('');
  
  $( '#cbo-Estado' ).html( '<option value="0">En proceso</option>' );
  $( '#cbo-Estado' ).append( '<option value="1">Completado</option>' );
}

function verCliente(ID){  
  $( '.div-Listar' ).hide();
  $( '#form-Cliente' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();

  url = base_url + 'CargaConsolidada/Consolidado/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $( '.div-AgregarEditar' ).show();
            
      $( '[name="EID_Carga_Consolidada"]' ).val(response.ID_Carga_Consolidada);
      $( '[name="ENo_Carga_Consolidada"]' ).val(response.No_Carga_Consolidada);

      $( '[name="No_Carga_Consolidada"]' ).val(response.No_Carga_Consolidada);
      $( '[name="Txt_Nota"]' ).val(response.Txt_Nota);

      $( '#cbo-Estado' ).html( '' );
      for (var i = 0; i < 2; i++){
        selected = '';
        if(response.Nu_Estado == i)
          selected = 'selected="selected"';
        $( '#cbo-Estado' ).append( '<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'En proceso' : 'Completado') + '</option>' );
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
	    
  	  $( '#modal-message' ).modal('show');
	    $( '.modal-message' ).addClass( 'modal-danger' );
	    $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
	    setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
	    
	    //Message for developer
      console.log(jqXHR.responseText);
    }
  })
}

function form_Entidad(){
  $( '#btn-save' ).text('');
  $( '#btn-save' ).attr('disabled', true);
  $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
  
  url = base_url + 'CargaConsolidada/Consolidado/crudCliente';
  $.ajax({
    type		  : 'POST',
    dataType	: 'JSON',
    url		    : url,
    data		  : $('#form-Cliente').serialize(),
    success : function( response ){
      $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
      $( '#modal-message' ).modal('show');
      
      if (response.status == 'success'){
        $( '#form-Cliente' )[0].reset();
        
        $( '.div-AgregarEditar' ).hide();
        $( '.div-Listar' ).show();
        $( '.modal-message' ).addClass(response.style_modal);
        $( '.modal-title-message' ).text(response.message);
        setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
        reload_table_Entidad();
      } else {
        $( '.modal-message' ).addClass(response.style_modal);
        $( '.modal-title-message' ).text(response.message);
        setTimeout(function() {$('#modal-message').modal('hide');}, 1200);
      }
      
      $( '#btn-save' ).text('');
      $( '#btn-save' ).append( 'Guardar' );
      $( '#btn-save' ).attr('disabled', false);
    },
    error: function (jqXHR, textStatus, errorThrown) {
      $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
      
      $( '#modal-message' ).modal('show');
      $( '.modal-message' ).addClass( 'modal-danger' );
      $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
      setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
      
      //Message for developer
      console.log(jqXHR.responseText);
      
      $( '#btn-save' ).text('');
      $( '#btn-save' ).append( 'Guardar' );
      $( '#btn-save' ).attr('disabled', false);
    }
  });
}

function eliminarCliente(ID_Empresa, ID, Nu_Documento_Identidad){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');

  $( '#modal-title' ).html('¿Deseas eliminar?');

  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $( '#btn-save-delete' ).off('click').click(function () {
    _eliminarCliente($modal_delete, ID_Empresa, ID, Nu_Documento_Identidad);
  });
}

function reload_table_Entidad(){
  table_Entidad.ajax.reload(null,false);
}

function _eliminarCliente($modal_delete, ID_Empresa, ID, Nu_Documento_Identidad){    
  url = base_url + 'CargaConsolidada/Consolidado/eliminarCliente/' + ID_Empresa + '/' + ID + '/' + Nu_Documento_Identidad;
  $.ajax({
    url       : url,
    type      : "GET",
    dataType  : "JSON",
    success: function( response ){
      $modal_delete.modal('hide');
	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
  	  $( '#modal-message' ).modal('show');
		  
		  if (response.status == 'success'){
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
  	    reload_table_Entidad();
		  } else {
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
		  }
    },
    error: function (jqXHR, textStatus, errorThrown) {
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