var url;
var table_AjusteInventario;
var accion_AjusteInventario = '';

function importarExcelAjusteInventario(){
  $( ".modal_importar_excel_ajuste_inventario" ).modal( "show" );
}

$(function () {
  // IMPORTACION DE EXCEL
	$(document).on('click', '.back-history', function () {
		window.location = base_url + 'Logistica/AjusteInventarioController/listar';
	})

  // Validate exist file excel ajuste_inventario
	$( document ).on('click', '#btn-excel-importar_excel_ajuste_inventario', function(event) {
	  if ( $( "#my-file-selector-ajuste_inventario" ).val().length === 0 ) {
      $( '#my-file-selector-ajuste_inventario' ).closest('.form-group').find('.help-block').html('Seleccionar archivo');
		  $( '#my-file-selector-ajuste_inventario' ).closest('.form-group').removeClass('has-success').addClass('has-error');
	  } else {
      $( '#btn-cancel-ajuste_inventario' ).attr('disabled', true);
      $( '#a-download-ajuste_inventario' ).attr('disabled', true);
	    
      $( '#btn-excel-importar_excel_ajuste_inventario' ).text('');
      $( '#btn-excel-importar_excel_ajuste_inventario' ).attr('disabled', true);
      $( '#btn-excel-importar_excel_ajuste_inventario' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
  
      $( '#modal-loader' ).modal('show');
	  }
  })
  
  $( '#btn-save-excel-ajuste_inventario' ).click(function (e) {
    e.preventDefault();
    if ( $( '#h5-excel-registro_valido_excel' ).text() < 0 ) {
      alert('No hay registros validos para procesar');
    } else {
      var $modal_delete = $( '.modal-message-delete' );
      $modal_delete.modal('show');
      
      $( '.modal-message-delete' ).removeClass('modal-danger modal-warning modal-success');
      $( '.modal-message-delete' ).addClass('modal-success');
      
      $( '.modal-title-message-delete' ).text('¿Deseas guardar ajuste de inventario?');
      
      $( '#btn-cancel-delete' ).off('click').click(function () {
        $modal_delete.modal('hide');
      });
        
      $( '#btn-save-delete' ).off('click').click(function () {
        $modal_delete.modal('hide');

        $( '#btn-save-excel-ajuste_inventario' ).text('');
        $( '#btn-save-excel-ajuste_inventario' ).attr('disabled', true);
        $( '#btn-save-excel-ajuste_inventario' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
        $( '#a-salir-excel-ajuste_inventario' ).attr('disabled', true);

        url = base_url + 'Logistica/AjusteInventarioController/guardarAjusteInventarioExcel';
        $.ajax({
          type : 'POST',
          dataType : 'JSON',
          url : url,
          data : $('#form-AjusteInventarioExcel').serialize(),
          success : function( response ){
            $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
            $( '#modal-message' ).modal('show');

            if ( response.sStatus=='success' ) {
              $( '.modal-save-excel-ajuste_inventario' ).modal('hide');

              $( '.modal-message' ).addClass( 'modal-' + response.sStatus);
              $( '.modal-title-message' ).text( response.sMessage );
              setTimeout(function() {
                $('#modal-message').modal('hide');

                window.location = base_url + 'Logistica/AjusteInventarioController/listar';
              }, 2500);              
            } else {
              $( '.modal-message' ).addClass( 'modal-' + response.sStatus );
              $( '.modal-title-message' ).text( response.sMessage );
              setTimeout(function() {$('#modal-message').modal('hide');}, 6100);
            }
            
            $( '#btn-save-excel-ajuste_inventario' ).text('');
            $( '#btn-save-excel-ajuste_inventario' ).append( 'Guardar' );
            $( '#btn-save-excel-ajuste_inventario' ).attr('disabled', false);
            $( '#a-salir-excel-ajuste_inventario' ).attr('disabled', false);
          }
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
          $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
          
          $( '#modal-message' ).modal('show');
          $( '.modal-message' ).addClass( 'modal-danger' );
          $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
          setTimeout(function() {$('#modal-message').modal('hide');}, 3100);
          
          //Message for developer
          console.log(jqXHR.responseText);

          $( '#btn-save-excel-ajuste_inventario' ).text('');
          $( '#btn-save-excel-ajuste_inventario' ).attr('disabled', false);
          $( '#btn-save-excel-ajuste_inventario' ).append( 'Guardar' );
          $( '#a-salir-excel-ajuste_inventario' ).attr('disabled', false);
        })
      
      });
    }
  })
  // ./ IMPORTACION DE EXCEL

  $( '.div-Ver' ).hide();
  $('.select2').select2();
  $('[data-mask]').inputmask();
  
  $('#radio-ajuste').prop('checked', true).iCheck('update');
  $('#radio-ajuste_error').prop('checked', false).iCheck('update');

  $('#radio-ajuste').on('ifChecked', function () {
    $('#radio-ajuste').prop('checked', true).iCheck('update');
    $('#radio-ajuste_error').prop('checked', false).iCheck('update');
  })

  $('#radio-ajuste_error').on('ifChecked', function () {
    $('#radio-ajuste').prop('checked', false).iCheck('update');
    $('#radio-ajuste_error').prop('checked', true).iCheck('update');
  })

  url = base_url + 'HelperController/getAlmacenes';
  $.post( url, function( response ){
    $( '#cbo-filtro_almacen' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-filtro_almacen' ).append( '<option value="' + response[i].ID_Almacen + '">' + response[i].No_Almacen + '</option>' );
  }, 'JSON');

  url = base_url + 'Logistica/AjusteInventarioController/ajax_list';
  table_AjusteInventario = $('#table-AjusteInventario').DataTable({
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
        'dataType'  : 'json',
        'data'      : function ( data ) {
          data.Filtro_Fe_Inicio = ParseDateString($( '#txt-Filtro_Fe_Inicio' ).val(), 1, '/'),
          data.Filtro_Fe_Fin = ParseDateString($( '#txt-Filtro_Fe_Fin' ).val(), 1, '/')
        },
    },
    'columnDefs': [{
      'className' : 'text-center',
      'targets'   : 'no-sort',
      'orderable' : false,
    },],
    'lengthMenu': [[10, 100, 1000, -1], [10, 100, 500, 1000]],
  });

  $('.dataTables_length').addClass('col-xs-4 col-sm-5 col-md-1');
  $('.dataTables_info').addClass('col-xs-8 col-sm-7 col-md-4');
  $('.dataTables_paginate').addClass('col-xs-12 col-sm-12 col-md-7');
  
  $( '#btn-filter' ).click(function(){
    table_AjusteInventario.ajax.reload();
  });
  
	$(document).bind('keydown', 'f2', function(){
    agregarAjusteInventario();
  });
})

function agregarAjusteInventario(){
  accion_AjusteInventario = 'add_AjusteInventario';
  $( '[name="EID_Empresa"]' ).focus();
  
  $( '#form-AjusteInventario' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '.div-Listar' ).hide();
  $( '.div-Ver' ).hide();
  $( '.div-AgregarEditar' ).show();

  $('#radio-ajuste').prop('checked', true).iCheck('update');
  $('#radio-ajuste_error').prop('checked', false).iCheck('update');

  $( '#modal-loader' ).modal('show');

  url = base_url + 'Logistica/AjusteInventarioController/getItemsAjusteInvetario';
  $.post( url, function( response ){
    $( '#modal-loader' ).modal('hide');
    $( '#table-AjusteInventarioAgregar > tbody' ).empty();
    if ( response.sStatus == 'success' ) {
      var iTotalRegistros = response.arrData.length, response=response.arrData, tr_body = '';
      for (var i = 0; i < iTotalRegistros; i++) {
        tr_body +=
        '<tr>'
          +'<td class="text-left" style="display:none;"><input type="hidden" value="' + response[i].ID_Producto + '" name="arrAjusteInventario[' + i + '][iIdItem]">' + response[i].Nu_Codigo_Barra + '</td>'
          +'<td class="text-left">' + response[i].Nu_Codigo_Barra + '</td>'
          +'<td class="text-left">' + response[i].No_Producto + '</td>'
          +'<td class="text-right td-stock_actutal">' + parseFloat(response[i].Qt_Producto).toFixed(3) + '</td>'
          +'<td class="text-right"><input type="text" inputmode="decimal" value="" class="txt-stock_fisico form-control input-decimal txt-calcular_diferencia"></td>'
          +'<td class="text-right td-stock_diferencia"></td>'
          +'<td class="text-right" style="display:none;"><input type="hidden" name="arrAjusteInventario[' + i + '][fStockFisico]" class="txt-stock_diferencia"></td>'
        +'</tr>';
      }
      
      $( '#table-AjusteInventarioAgregar > tbody' ).append(tr_body);
  
      $( '#table-AjusteInventarioAgregar tbody' ).on('input', '.txt-calcular_diferencia', function(){
        fila = $(this).parents("tr");
        fStockActual = parseFloat(fila.find(".td-stock_actutal").text());

        fStockFisico = parseFloat(fila.find(".txt-stock_fisico").val());
        fStockDiferencia = (fStockFisico - fStockActual);
        fila.find(".td-stock_diferencia").text( !isNaN(fStockDiferencia) ? fStockDiferencia : '' );
        fila.find(".txt-stock_diferencia").val( !isNaN(fStockDiferencia) ? fStockDiferencia : '' );
      })

      validateDecimal();
    } else {
      $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
      
      $( '#modal-message' ).modal('show');
      $( '.modal-message' ).addClass( 'modal-' + response.sStatus );
      $( '.modal-title-message' ).text( response.sMessage );
      setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
    }
  }, 'JSON')
  .fail(function(jqXHR, textStatus, errorThrown) {
    $( '#modal-loader' ).modal('hide');
    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
    
    $( '#modal-message' ).modal('show');
    $( '.modal-message' ).addClass( 'modal-danger' );
    $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
    setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
    
    //Message for developer
    console.log(jqXHR.responseText);
  });
  
  $("#txt-Global_Filter_Producto").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#table-AjusteInventarioAgregar tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
}

function guardarAjusteInventario(){
  var arrData = $('#form-AjusteInventario').serializeArray(), dataObj = {};
  var iIdItemValue = '';
  $(arrData).each(function (i, field) {
    var arrInput = field.name.split('[');
    if(arrInput[1]!==undefined) {
      var iIdValue = arrInput[1].substring(0, arrInput[1].length - 1);
      if (field.name == 'arrAjusteInventario[' + iIdValue + '][iIdItem]' && (field.value)) {
        iIdItemValue = field.value;
      }
      if (field.name == 'arrAjusteInventario[' + iIdValue + '][fStockFisico]' && (field.value)) {
        dataObj[field.name] = field.value;
        dataObj['arrAjusteInventario[' + iIdValue + '][iIdItem]'] = iIdItemValue;
      }
    }
  });

  dataObj['iTipoMovimientoInventario'] = $('[name="radio-tipo_movimiento_inventario"]:checked').attr('value');

  var $modal_delete = $( '.modal-message-delete' );
  $modal_delete.modal('show');
  
  $modal_delete.removeClass('modal-danger modal-warning modal-success');
  $modal_delete.addClass('modal-warning');
  
  $( '.modal-title-message-delete' ).text('¿Estás seguro de procesar el ajuste?');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });
  
  $( '#btn-save-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');

    $( '#btn-procesar_ajuste' ).text('');
    $( '#btn-procesar_ajuste' ).attr('disabled', true);
    $( '#btn-procesar_ajuste' ).append( 'Procesando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
    $( '#btn-cancelar' ).attr('disabled', true);

    url = base_url + 'Logistica/AjusteInventarioController/guardarAjusteInventario';
    $.ajax({
      type : 'POST',
      dataType : 'JSON',
      url : url,
      data: dataObj,
      success : function( response ){      
        $( '#modal-loader' ).modal('hide');
            
        $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
        $( '#modal-message' ).modal('show');
        
        if (response.sStatus == 'success'){
          accion_AjusteInventario = '';
      
          $( '#form-AjusteInventario' )[0].reset();
          $( '.div-AgregarEditar' ).hide();
          $( '.div-Listar' ).show();
          $( '.modal-message' ).addClass( 'modal-' + response.sStatus );
          $( '.modal-title-message' ).text(response.sMessage);
          setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
          reload_table_AjusteInventario();
        } else {
          $( '.modal-message' ).addClass( 'modal-' + response.sStatus );
          $( '.modal-title-message' ).text(response.sMessage);
          setTimeout(function() {$('#modal-message').modal('hide');}, 1200);
        }
      
        $( '#btn-procesar_ajuste' ).text('');
        $( '#btn-procesar_ajuste' ).append( '<span class="fa fa-save"></span> Guardar' );
        $( '#btn-procesar_ajuste' ).attr('disabled', false);
        $( '#btn-cancelar' ).attr('disabled', false);
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
        $( '#btn-procesar_ajuste' ).attr('disabled', false);
        $( '#btn-cancelar' ).attr('disabled', false);
      }
    });
  });
}

function verAjusteInventario(ID){
  accion_AjusteInventario = 'upd_AjusteInventario';
  
  $( '.div-Listar' ).hide();
  $( '.div-AgregarEditar' ).hide();
  $( '.div-Ver' ).show();
  
  $( '#modal-loader' ).modal('show');
 
  url = base_url + 'Logistica/AjusteInventarioController/verAjusteProcesado/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $( '#modal-loader' ).modal('hide');
      $( '#table-AjusteInventarioVer > tbody' ).empty();
      if ( response.sStatus == 'success' ) {
        var iTotalRegistros = response.arrData.length, response=response.arrData, tr_body = '';
        $( '#h4-title-ver_ajuste_inventario' ).text( 'Fecha Ajuste ' + ParseDateHour(response[0].Fe_Emision_Hora));
        for (var i = 0; i < iTotalRegistros; i++) {
          tr_body +=
          '<tr>'
            +'<td class="text-left">' + response[i].Nu_Codigo_Barra + '</td>'
            +'<td class="text-left">' + response[i].No_Producto + '</td>'
            +'<td class="text-right td-stock_actutal">' + (parseFloat(response[i].Qt_Producto) < 0 ? '-' : '') + number_format(parseFloat(response[i].Qt_Producto), 3) + '</td>'
          +'</tr>';
        }
        
        $( '#table-AjusteInventarioVer > tbody' ).append(tr_body);
    
      } else {
        $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
        
        $( '#modal-message' ).modal('show');
        $( '.modal-message' ).addClass( 'modal-' + response.sStatus );
        $( '.modal-title-message' ).text( response.sMessage );
        setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
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
  
  $( '#btn-cancelar_ver_ajuste_inventario' ).click(function() {
    $( '.div-Ver' ).hide();
    $( '.div-AgregarEditar' ).hide();
    $( '.div-Listar' ).show();
  })
}

function reload_table_AjusteInventario(){
  table_AjusteInventario.ajax.reload(null,false);
}