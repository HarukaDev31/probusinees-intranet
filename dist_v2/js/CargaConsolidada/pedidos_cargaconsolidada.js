var url, table_Entidad, div_items = '', iCounter = 1;
//AUTOCOMPLETE
var caractes_no_validos_global_autocomplete = "\"'~!@%^\|";
// Se puede crear un arreglo a partir de la cadena
let search_global_autocomplete = caractes_no_validos_global_autocomplete.split('');
// Solo tomé algunos caracteres, completa el arreglo
let replace_global_autocomplete = ['', '', '', '', '', '', '', '', ''];
//28 caracteres
// FIN AUTOCOMPLETE

var fToday = new Date(), fYear = fToday.getFullYear(), fMonth = fToday.getMonth() + 1, fDay = fToday.getDate();

$(function () {
  
  $("#form-enviar_mensaje").on('submit',function(e){
    e.preventDefault();

    $('.help-block').empty();
    $('.form-group').removeClass('has-error');
    
    if ($( '[name="enviar_mensaje-No_Seguimiento"]' ).val() == '' || $( '[name="enviar_mensaje-No_Seguimiento"]' ).val() == null || $( '[name="enviar_mensaje-No_Seguimiento"]' ).val().length==0) {
      $( '[name="enviar_mensaje-No_Seguimiento"]' ).closest('.form-group').find('.help-block').html('Debes escribir mensaje');
      $( '[name="enviar_mensaje-No_Seguimiento"]' ).closest('.form-group').removeClass('has-success').addClass('has-error');
      $( '[name="enviar_mensaje-No_Seguimiento"]' ).focus();
    } else {
      $( '#btn-enviar_mensaje' ).text('');
      $( '#btn-enviar_mensaje' ).attr('disabled', true);
      $( '#btn-enviar_mensaje' ).html( 'Enviando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
      
      url = base_url + 'CargaConsolidada/PedidosCargaConsolidada/sendMessage';
      $.ajax({
        type		  : 'POST',
        dataType	: 'JSON',
        url		    : url,
        data		  : $('#form-enviar_mensaje').serialize(),
        success : function( response ){  
          $( '.modal-enviar_mensaje' ).modal('hide');

          $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
          $('#modal-message').modal('show');  
          
          if (response.status == 'success'){
            $( '#form-enviar_mensaje' )[0].reset();
            
            $('#moda-message-content').addClass( 'bg-' + response.status);
            $('.modal-title-message').text(response.message);
            setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
            reload_table_Entidad();
          } else {
            $('#moda-message-content').addClass( 'bg-danger' );
            $('.modal-title-message').text(response.message);
            setTimeout(function() {$('#modal-message').modal('hide');}, 1200);
          }
          
          $( '#btn-enviar_mensaje' ).text('');
          $( '#btn-enviar_mensaje' ).html( 'Enviar' );
          $( '#btn-enviar_mensaje' ).attr('disabled', false);
        },
        error: function (jqXHR, textStatus, errorThrown) {
          $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
          
          $( '#modal-message' ).modal('show');
          $( '.modal-message' ).addClass( 'modal-danger' );
          $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
          setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
          
          //Message for developer
          console.log(jqXHR.responseText);
          
          $( '#btn-enviar_mensaje' ).text('');
          $( '#btn-enviar_mensaje' ).html( 'Enviar' );
          $( '#btn-enviar_mensaje' ).attr('disabled', false);
        }
      });
    }
  })
  
  //Date picker invoice
  $( '.input-report' ).datepicker({
    autoclose : true,
    startDate : new Date('2023', '10', '01'),
    todayHighlight  : true,
    dateFormat: 'dd/mm/yyyy',
    format: 'dd/mm/yyyy',
  });

  //Date picker invoice
  $( '.input-datepicker-pay' ).datepicker({
    autoclose : true,
    startDate : new Date(fYear, fToday.getMonth(), fDay),
    todayHighlight  : true,
    dateFormat: 'dd/mm/yyyy',
    format: 'dd/mm/yyyy',
  });

  //Global Autocomplete
  $( '.autocompletar' ).autoComplete({
    minChars: 0,
    source: function (term, response) {
      term = term.trim();
      if (term.length > 2) {
        var term                = term.toLowerCase();
        var global_class_method = $( '.autocompletar' ).data('global-class_method');
        var global_table        = $( '.autocompletar' ).data('global-table');
        
        var filter_id_codigo = '';
        if ($( '#txt-EID_Producto' ).val() !== undefined)
          filter_id_codigo = $( '#txt-EID_Producto' ).val();
        
        $.post( base_url + global_class_method, { global_table: global_table, global_search : term, filter_id_codigo : filter_id_codigo }, function( arrData ){
          response(arrData);
        }, 'JSON');
      }
    },
    renderItem: function (item, search){
      search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
      var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");
      return '<div title="' + caracteresValidosAutocomplete(item.Nombre) + '" class="autocomplete-suggestion" data-id="' + item.ID + '" data-nombre="' + caracteresValidosAutocomplete(item.Nombre) + '" data-val="' + search + '">' + caracteresValidosAutocomplete(item.Nombre).replace(re, "<b>$1</b>") + '</div>';
    },
    onSelect: function(e, term, item){
      $( '#txt-ID_Entidad' ).val(item.data('id'));
      $( '#txt-No_Entidad' ).val(item.data('nombre'));

			arrTemporal={
				ID_Entidad:item.data('id'),
				No_Entidad:item.data('nombre')
      }
      
			agregarRegistroTemporal(arrTemporal);
    }
  });

  url = base_url + 'CargaConsolidada/PedidosCargaConsolidada/ajax_list';
  table_Entidad = $( '#table-Pedidos' ).DataTable({
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
        data.sMethod                = $('#hidden-sMethod').val(),
        data.Filtros_Entidades      = $( '#cbo-Filtros_Entidades' ).val(),
        data.Global_Filter          = $( '#txt-Global_Filter' ).val(),
        data.Filtro_Fe_Inicio       = ParseDateString($( '#txt-Fe_Inicio' ).val(), 'fecha', '/'),
        data.Filtro_Fe_Fin          = ParseDateString($( '#txt-Fe_Fin' ).val(), 'fecha', '/');
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

  $('#table-Pedidos_filter input').removeClass('form-control-sm');
  $('#table-Pedidos_filter input').addClass('form-control-md');
  $('#table-Pedidos_filter input').addClass("width_full");

  $( '.div-AgregarEditar' ).hide();

  $('#btn-html_reporte').click(function () {
    reload_table_Entidad();
  });
  
  $( "#form-pedido" ).validate({
		rules:{
			No_Entidad: {
				required: true
			},
		},
		messages:{
			No_Entidad:{
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
		submitHandler: form_pedido
	});
  
  $('.div-clientes').hide();

  $( '#table-clientes tbody' ).on('click', '#btn-delete_cliente', function(){
    $(this).closest('tr').remove();
    if ($( '#table-clientes >tbody >tr' ).length == 0) {
      $('.div-clientes').hide();
      $( '#table-clientes' ).hide();
    }
  })
})

function verPedido(ID){  
  $( '.div-Listar' ).hide();

  $( '#form-pedido' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '#table-clientes tbody' ).empty();

  url = base_url + 'CargaConsolidada/PedidosCargaConsolidada/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      console.log(response);
      $( '.div-AgregarEditar' ).show();
      
      $( '.title_Entidad' ).text('Modifcar Pedido');
      
      $( '[name="EID_Pedido_Cabecera"]' ).val(response[0].ID_Pedido_Cabecera);
      
      $( '[name="No_Carga_Consolidada"]' ).val(response[0].No_Carga_Consolidada);

      $( '[name="Fe_Inicio"]' ).val(ParseDateString(response[0].Fe_Inicio, 'fecha_bd', '-'));
      $( '[name="Fe_Termino"]' ).val(ParseDateString(response[0].Fe_Termino, 'fecha_bd', '-'));
      $( '[name="Fe_Carga"]' ).val(ParseDateString(response[0].Fe_Carga, 'fecha_bd', '-'));
      $( '[name="Fe_Zarpe"]' ).val(ParseDateString(response[0].Fe_Zarpe, 'fecha_bd', '-'));
      $( '[name="Fe_Llegada"]' ).val(ParseDateString(response[0].Fe_Llegada, 'fecha_bd', '-'));

      var table_temporal_cliente = '';
      for (let index = 0; index < response.length; index++) {
        const element = response[index];
        table_temporal_cliente +=
        "<tr id='tr_entidad" + element.ID_Entidad + "'>"
          + "<td class='text-left' style='display:none;'>" + element.ID_Entidad + "</td>"
          + "<td class='text-left'>" + element.No_Entidad + "</td>"
          + "<td class='text-center'><button type='button' id='btn-delete_cliente' class='btn btn-xs btn-link' alt='Eliminar' title='Eliminar'><i class='fas fa-trash-alt fa-2x' aria-hidden='true'></i></button></td>"
          + "<input type='hidden' name='arrEntidad[][ID_Entidad]' value='" + element.ID_Entidad + "' class='form-control'>"
        + "</tr>";
      }
      
      $('.div-clientes').show();
      $( '#table-clientes' ).show();
      $( '#table-clientes' ).append(table_temporal_cliente);
    }
  })
}

function reload_table_Entidad(){
  table_Entidad.ajax.reload(null,false);
}

function caracteresValidosAutocomplete(msg) {
  // Recorrer todos los caracteres
  search_global_autocomplete.forEach((char, index) => {
    // Remplazar cada caracter en la cadena
    msg = msg.replaceAll(char, replace_global_autocomplete[index]);
  });
  return msg;
}

function agregarRegistroTemporal(arrParams){
  //console.log(arrParams);
  
  var table_temporal_cliente =
  "<tr id='tr_entidad" + arrParams.ID_Entidad + "'>"
    + "<td class='text-left' style='display:none;'>" + arrParams.ID_Entidad + "</td>"
    + "<td class='text-left'>" + arrParams.No_Entidad + "</td>"
    + "<td class='text-center'><button type='button' id='btn-delete_cliente' class='btn btn-xs btn-link' alt='Eliminar' title='Eliminar'><i class='fas fa-trash-alt fa-2x' aria-hidden='true'></i></button></td>"
    + "<input type='hidden' name='arrEntidad[][ID_Entidad]' value='" + arrParams.ID_Entidad + "' class='form-control'>"
  + "</tr>";
  
  if( isExistTableTemporalId(arrParams.ID_Entidad) ){
    $( '#txt-No_Entidad' ).closest('.form-group').find('.help-block').html('Ya existe cliente <b>' + arrParams.No_Entidad + '</b>');
    $( '#txt-No_Entidad' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    $( '#txt-No_Entidad' ).val('');
    $( '#txt-ID_Entidad' ).val('');
    
    $( '#txt-No_Entidad' ).focus();
  } else {
    $('.div-clientes').show();
    $( '#table-clientes' ).show();
    $( '#table-clientes' ).append(table_temporal_cliente);
    $( '#txt-No_Entidad' ).val('');
    $( '#txt-ID_Entidad' ).val('');
    
    $( '#txt-No_Entidad' ).focus();
  }
}

function isExistTableTemporalId($id){
  return Array.from($('tr[id*=tr_entidad]')).some(element => ($('td:nth(0)',$(element)).html()==$id));
}

function form_pedido(){
  if ($( '#table-clientes >tbody >tr' ).length == 0) {
    $( '#txt-No_Entidad' ).closest('.form-group').find('.help-block').html('Agregar cliente');
    $( '#txt-No_Entidad' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    $( '#txt-No_Entidad' ).focus();
  } else {
    $( '#btn-save' ).text('');
    $( '#btn-save' ).attr('disabled', true);
    $( '#btn-save' ).html( 'Guardando <div class="spinner-border" role="status"><span class="sr-only"></span></div>' );
    
    var postData = new FormData($("#form-pedido")[0]);
    //$('#form-pedido').serialize(),

    url = base_url + 'CargaConsolidada/PedidosCargaConsolidada/crudPedidoGrupal';
    $.ajax({
      type		    : 'POST',
      dataType	  : 'JSON',
  		url		      : url,
  		data		    : postData,
      mimeType    : "multipart/form-data",
      contentType : false,
      cache       : false,
      processData : false,
      success : function( response ){
        $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
        $('#modal-message').modal('show');
        
        if (response.status == 'success'){
          $( '#form-pedido' )[0].reset();
          
          $( '.div-AgregarEditar' ).hide();
          $( '.div-Listar' ).show();
          $('#moda-message-content').addClass( 'bg-' + response.status);
          $('.modal-title-message').text(response.message);
          setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
          reload_table_Entidad();
        } else {
          $('#moda-message-content').addClass( 'bg-danger' );
          $('.modal-title-message').text(response.message);
          setTimeout(function() {$('#modal-message').modal('hide');}, 2100);
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
}

function agregarPedido(){
  accion_cliente = 'add_cliente';
  $( '#form-pedido' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '.div-Listar' ).hide();
  $( '.div-AgregarEditar' ).show();
        
  $( '.title_Entidad' ).text('Nuevo Pedido');

  $( '[name="EID_Pedido_Cabecera"]' ).val('');

  $( '#table-clientes tbody' ).empty();
}

function eliminarPedido(ID){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '.modal-message-delete' ).removeClass('modal-danger modal-warning modal-success');
  $( '.modal-message-delete' ).addClass('modal-success');

  $('#modal-title').text('¿Deseas eliminar?');

  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $( '#btn-save-delete' ).off('click').click(function () {
    _eliminarPedido($modal_delete, ID);
  });
}

function _eliminarPedido($modal_delete, ID){
  //$( '#modal-loader' ).modal('show');
    
  url = base_url + 'CargaConsolidada/PedidosCargaConsolidada/eliminarPedido/' + ID;
  $.ajax({
    url       : url,
    type      : "GET",
    dataType  : "JSON",
    success: function( response ){
      //$( '#modal-loader' ).modal('hide');
      
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
		  accion_cliente = '';
    },
    error: function (jqXHR, textStatus, errorThrown) {
		  accion_cliente = '';
      //$( '#modal-loader' ).modal('hide');
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

function cambiarEstado(ID, Nu_Estado) {
  var $modal_delete = $('#modal-message-delete');
  $modal_delete.modal('show');

  $('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
  $('.modal-message-delete').addClass('modal-success');

  var sNombreEstado = 'Verde';
  if(Nu_Estado==2)
    sNombreEstado = 'Naranja';
  else if(Nu_Estado==3)
    sNombreEstado = 'Rojo';

  $('#modal-title').html('¿Deseas cambiar canal a <strong>' + sNombreEstado + '</strong>?');

  $('#btn-cancel-delete').off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $('#btn-save-delete').off('click').click(function () {
    $( '#btn-save-delete' ).text('');
    $( '#btn-save-delete' ).attr('disabled', true);
    $( '#btn-save-delete' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

    url = base_url + 'CargaConsolidada/PedidosCargaConsolidada/cambiarEstado/' + ID + '/' + Nu_Estado;
    $.ajax({
      url: url,
      type: "GET",
      dataType: "JSON",
      success: function (response) {
        $modal_delete.modal('hide');

        $( '#btn-save-delete' ).text('');
        $( '#btn-save-delete' ).append( 'Aceptar' );
        $( '#btn-save-delete' ).attr('disabled', false);

        $('.modal-message').removeClass('modal-danger modal-warning modal-success');
        $('#modal-message').modal('show');

        if (response.status == 'success') {
          $('.modal-message').addClass(response.style_modal);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
          reload_table_Entidad();
        } else {
          $('.modal-message').addClass(response.style_modal);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1500);
        }
      }
    });
  });
}

function enviarSeguimiento(ID){
  $( '#form-enviar_mensaje' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();

  $( '#enviar_mensaje-id_pedido_cabecera' ).val(ID);

  $('#modal-enviar_mensaje').modal('show');
  $( '#form-enviar_mensaje' )[0].reset();
}