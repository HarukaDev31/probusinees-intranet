var url, table_Entidad;

$(function () {
  //Date picker
  /*
  $('#fecha_inicio').datetimepicker({
    minDate: moment(),
    allowInputToggle: true,
    enabledHours : false,
    locale: moment().local('es'),
    format: 'DD/MM/YYYY'
  });

  $('#fecha_fin').datetimepicker({
    minDate: moment(),
    allowInputToggle: true,
    enabledHours : false,
    locale: moment().local('es'),
    format: 'DD/MM/YYYY'
  });
  */

  url = base_url + 'ImportacionGrupal/PedidosGrupal/ajax_list';
  table_Entidad = $( '#table-Pedidos' ).DataTable({
    dom: "<'row'<'col-sm-12 col-md-5'B><'col-sm-12 col-md-2'><'col-sm-12 col-md-5'f>>" +
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
  
  $( '.custom-select' ).removeClass('custom-select-sm form-control-sm');

  $( '.div-AgregarEditar' ).hide();
})

function reload_table_Entidad(){
  table_Entidad.ajax.reload(null,false);
}

function verPedido(ID){  
  $( '.div-Listar' ).hide();
  
  $( '#form-pedido' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();

	$( '#table-Producto_Enlace tbody' ).empty();
  $( '#table-Producto_Enlace' ).show();

  url = base_url + 'ImportacionGrupal/PedidosGrupal/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      var detalle = response;
      response = response[0];

      $( '.div-AgregarEditar' ).show();
            
      $( '[name="EID_Pedido_Cabecera"]' ).val(response.ID_Pedido_Cabecera);
            
      url = base_url + 'HelperController/getMonedas';
      $.post( url , function( responseMonedas ){
        $( '#cbo-Monedas' ).html('');
        for (var i = 0; i < responseMonedas.length; i++){
          selected = '';
          if(response.ID_Moneda == responseMonedas[i]['ID_Moneda']){
            selected = 'selected="selected"';
          }
          $( '#cbo-Monedas' ).append( '<option value="' + responseMonedas[i]['ID_Moneda'] + '" data-no_signo="' + responseMonedas[i]['No_Signo'] + '" ' + selected + '>' + responseMonedas[i]['No_Moneda'] + '</option>' );
        }
      }, 'JSON');
      $( '[name="No_Entidad"]' ).val(response.No_Entidad);
      $( '[name="Fe_Emision"]' ).val(ParseDateString(response.Fe_Emision, 'fecha_bd', '-'));

      var sNombreEstado = 'Pendiente';
      if(response.Nu_Estado == 2)
        sNombreEstado = 'Confirmado';
      if(response.Nu_Estado == 2)
        sNombreEstado = 'Completado';
      if(response.Nu_Estado == 2)
        sNombreEstado = 'Rechazado';
      $( '[name="No_Estado"]' ).val(sNombreEstado);
      
      $( '[name="Nu_Documento_Identidad"]' ).val(response.Nu_Documento_Identidad);
      $( '[name="Nu_Celular_Entidad"]' ).val(response.Nu_Celular_Entidad);
      $( '[name="Txt_Email_Entidad"]' ).val(response.Txt_Email_Entidad);

      var table_enlace_producto = "";
      for (i = 0; i < detalle.length; i++) {
        table_enlace_producto +=
        "<tr id='tr_enlace_producto" + detalle[i]['ID_Producto'] + "'>"
          + "<td style='display:none;' class='text-left td-id_item'>" + detalle[i]['ID_Producto'] + "</td>"
          + "<td class='text-left td-name'>" + detalle[i]['No_Producto'] + "</td>"
          + "<td class='text-left td-name'>" + ((detalle[i]['No_Unidad_Medida'] != '' && detalle[i]['No_Unidad_Medida'] != null) ? detalle[i]['No_Unidad_Medida'] : detalle[i]['No_Unidad_Medida_2']) + "</td>"
          + "<td class='text-left td-name'>" + Math.round10(detalle[i]['Qt_Producto'], -2) + "</td>"
          + "<td class='text-left td-name'>" + detalle[i]['Ss_Precio'] + "</td>"
          + "<td class='text-left td-name'>" + detalle[i]['Ss_Total'] + "</td>"
          //+ "<td class='text-center'><button type='button' id='btn-deleteProductoEnlace' class='btn btn-xs btn-link' alt='Eliminar' title='Eliminar'><i class='fas fa-trash-alt fa-2x' aria-hidden='true'></i></button></td>";
          table_enlace_producto += '<input type="hidden" name="addProducto[' + detalle[i]['ID_Producto'] + '][id_item]" value="' + detalle[i]['ID_Producto'] + '">';
        table_enlace_producto += "</tr>";
      }
      $( '#table-Producto_Enlace' ).append(table_enlace_producto);
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

function cambiarEstado(ID, Nu_Estado, sNombreEstado) {
  var $modal_delete = $('#modal-message-delete');
  $modal_delete.modal('show');

  $('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
  $('.modal-message-delete').addClass('modal-success');

  $('#modal-title').text('¿Deseas cambiar estado?');

  $('#btn-cancel-delete').off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $('#btn-save-delete').off('click').click(function () {
    $('#modal-loader').modal('show');

    url = base_url + 'ImportacionGrupal/PedidosGrupal/cambiarEstado/' + ID + '/' + Nu_Estado;
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
