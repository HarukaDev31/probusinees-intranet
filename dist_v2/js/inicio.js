$(function () {
  $('.select2').select2();

  /*
  $("#example1").DataTable({
    "responsive": true, "lengthChange": false, "autoWidth": false,
    "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
  }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

  $('#example2').DataTable({
    "paging": true,
    "lengthChange": false,
    "searching": false,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "responsive": true,
  });
  */
 
	$(".conversion-yuan_dolar").keyup(function (e) {
		e.preventDefault();

    var tipo_cambio = parseFloat($( '[name="costos_origen_china-Ss_Tipo_Cambio"]' ).val());
    var yuan = parseFloat($(this).val());

    console.log('tipo cambio > ' + tipo_cambio);
    console.log('yuan > ' + yuan);
    console.log( ' > ' + $( this ).data('conversion_dolar'));

    $('[name="' + $( this ).data('conversion_dolar') + '"]').val( (yuan / tipo_cambio).toFixed(2) );
	});
  
	$(document).on('click', '#btn-save_cliente', function (e) {
    e.preventDefault();

    $( '#btn-save_cliente' ).text('');
    $( '#btn-save_cliente' ).attr('disabled', true);
    $( '#btn-save_cliente' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

    //$( '#modal-loader' ).modal('show');

    url = base_url + 'InicioController/crudCliente';
      $.ajax({
      type		  : 'POST',
      dataType	: 'JSON',
      url		    : url,
      data		  : $('#form-cliente').serialize(),
      success : function( response ){
          //$( '#modal-loader' ).modal('hide');
          
          $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
          $('#modal-message').modal('show');
          
          if (response.status == 'success'){
            accion_cliente = '';
            $('.modal-cliente').modal('hide');
              
            $('#moda-message-content').addClass( 'bg-' + response.status);
            $('.modal-title-message').text(response.message);
            setTimeout(function() {$('#modal-message').modal('hide');}, 2100);
            
            location.reload();
          } else {
            $('#moda-message-content').addClass( 'bg-danger' );
            $('.modal-title-message').text(response.message);
            setTimeout(function() {$('#modal-message').modal('hide');}, 3200);
          }
          
          $( '#btn-save_cliente' ).text('');
          $( '#btn-save_cliente' ).append( 'Guardar' );
          $( '#btn-save_cliente' ).attr('disabled', false);
      },
      error: function (jqXHR, textStatus, errorThrown) {
          //$( '#modal-loader' ).modal('hide');
          $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
          
          $( '#modal-message' ).modal('show');
          $('#moda-message-content').addClass( 'bg-danger' );
          $('.modal-title-message').text(response.message);
          setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
          
          //Message for developer
          console.log(jqXHR.responseText);
          
          $( '#btn-save_cliente' ).text('');
          $( '#btn-save_cliente' ).append( 'Guardar' );
          $( '#btn-save_cliente' ).attr('disabled', false);
      }
    });
  });

	$(document).on('click', '#btn-save_cliente_modal_paso1', function (e) {
    e.preventDefault();

    $( '#btn-save_cliente_modal_paso1' ).text('');
    $( '#btn-save_cliente_modal_paso1' ).attr('disabled', true);
    $( '#btn-save_cliente_modal_paso1' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
    
    url = base_url + 'AgenteCompra/PedidosPagados/completarVerificacionOC/' + $( '#cliente_modal_paso1-ID_Pedido_Cabecera' ).val() + '/' + $( '#cliente_modal_paso1-iIdTareaPedido' ).val();
    $.ajax({
    url: url,
    type: "GET",
    dataType: "JSON",
      success : function( response ){
        //$( '#modal-loader' ).modal('hide');
        
        $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
        $('#modal-message').modal('show');
        
        if (response.status == 'success'){
          $('.modal-cliente_modal_paso1').modal('hide');
            
          $('#moda-message-content').addClass( 'bg-' + response.status);
          $('.modal-title-message').text(response.message);
          setTimeout(function() {$('#modal-message').modal('hide');}, 2100);

          location.reload();
        } else {
          $('#moda-message-content').addClass( 'bg-danger' );
          $('.modal-title-message').text(response.message);
          setTimeout(function() {$('#modal-message').modal('hide');}, 3200);
        }
        
        $( '#btn-save_cliente_modal_paso1' ).text('');
        $( '#btn-save_cliente_modal_paso1' ).append( 'Guardar' );
        $( '#btn-save_cliente_modal_paso1' ).attr('disabled', false);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        //$( '#modal-loader' ).modal('hide');
        $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
        
        $( '#modal-message' ).modal('show');
        $('#moda-message-content').addClass( 'bg-danger' );
        $('.modal-title-message').text('Problemas');
        setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
        
        //Message for developer
        console.log(jqXHR.responseText);
        
        $( '#btn-save_cliente_modal_paso1' ).text('');
        $( '#btn-save_cliente_modal_paso1' ).append( 'Guardar' );
        $( '#btn-save_cliente_modal_paso1' ).attr('disabled', false);
      }
    });
  });

	$(document).on('click', '#btn-save_booking_inspeccion', function (e) {
    e.preventDefault();

    $( '#btn-save_booking_inspeccion' ).text('');
    $( '#btn-save_booking_inspeccion' ).attr('disabled', true);
    $( '#btn-save_booking_inspeccion' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

    //$( '#modal-loader' ).modal('show');

    url = base_url + 'AgenteCompra/PedidosPagados/bookingInspeccion';
      $.ajax({
      type		  : 'POST',
      dataType	: 'JSON',
      url		    : url,
      data		  : $('#form-booking_inspeccion').serialize(),
      success : function( response ){
          //$( '#modal-loader' ).modal('hide');
          
          $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
          $('#modal-message').modal('show');
          
          if (response.status == 'success'){
            $('.modal-booking_inspeccion').modal('hide');
              
            $('#moda-message-content').addClass( 'bg-' + response.status);
            $('.modal-title-message').text(response.message);
            setTimeout(function() {$('#modal-message').modal('hide');}, 2100);
            
            location.reload();
          } else {
            $('#moda-message-content').addClass( 'bg-danger' );
            $('.modal-title-message').text(response.message);
            setTimeout(function() {$('#modal-message').modal('hide');}, 3200);
          }
          
          $( '#btn-save_booking_inspeccion' ).text('');
          $( '#btn-save_booking_inspeccion' ).append( 'Guardar' );
          $( '#btn-save_booking_inspeccion' ).attr('disabled', false);
      },
      error: function (jqXHR, textStatus, errorThrown) {
          //$( '#modal-loader' ).modal('hide');
          $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
          
          $( '#modal-message' ).modal('show');
          $('#moda-message-content').addClass( 'bg-danger' );
          $('.modal-title-message').text('Problemas');
          setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
          
          //Message for developer
          console.log(jqXHR.responseText);
          
          $( '#btn-save_booking_inspeccion' ).text('');
          $( '#btn-save_booking_inspeccion' ).append( 'Guardar' );
          $( '#btn-save_booking_inspeccion' ).attr('disabled', false);
      }
    });
  });
  
	$(document).on('click', '#btn-save_reserva_booking_trading', function (e) {
    e.preventDefault();

    $( '#btn-save_reserva_booking_trading' ).text('');
    $( '#btn-save_reserva_booking_trading' ).attr('disabled', true);
    $( '#btn-save_reserva_booking_trading' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

    //$( '#modal-loader' ).modal('show');

    url = base_url + 'AgenteCompra/PedidosPagados/reservaBookingTrading';
      $.ajax({
      type		  : 'POST',
      dataType	: 'JSON',
      url		    : url,
      data		  : $('#form-reserva_booking_trading').serialize(),
      success : function( response ){
          //$( '#modal-loader' ).modal('hide');
          
          $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
          $('#modal-message').modal('show');
          
          if (response.status == 'success'){
            $('.modal-reserva_booking_trading').modal('hide');
              
            $('#moda-message-content').addClass( 'bg-' + response.status);
            $('.modal-title-message').text(response.message);
            setTimeout(function() {$('#modal-message').modal('hide');}, 2100);
            
            location.reload();
          } else {
            $('#moda-message-content').addClass( 'bg-danger' );
            $('.modal-title-message').text(response.message);
            setTimeout(function() {$('#modal-message').modal('hide');}, 3200);
          }
          
          $( '#btn-save_reserva_booking_trading' ).text('');
          $( '#btn-save_reserva_booking_trading' ).append( 'Guardar' );
          $( '#btn-save_reserva_booking_trading' ).attr('disabled', false);
      },
      error: function (jqXHR, textStatus, errorThrown) {
          //$( '#modal-loader' ).modal('hide');
          $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
          
          $( '#modal-message' ).modal('show');
          $('#moda-message-content').addClass( 'bg-danger' );
          $('.modal-title-message').text('Problemas');
          setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
          
          //Message for developer
          console.log(jqXHR.responseText);
          
          $( '#btn-save_reserva_booking_trading' ).text('');
          $( '#btn-save_reserva_booking_trading' ).append( 'Guardar' );
          $( '#btn-save_reserva_booking_trading' ).attr('disabled', false);
      }
    });
  });

	$(document).on('click', '#btn-save_costos_origen_china', function (e) {
    e.preventDefault();

    $( '#btn-save_costos_origen_china' ).text('');
    $( '#btn-save_costos_origen_china' ).attr('disabled', true);
    $( '#btn-save_costos_origen_china' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

    //$( '#modal-loader' ).modal('show');

    url = base_url + 'AgenteCompra/PedidosPagados/costosOrigenTradingChina';
      $.ajax({
      type		  : 'POST',
      dataType	: 'JSON',
      url		    : url,
      data		  : $('#form-costos_origen_china').serialize(),
      success : function( response ){
          //$( '#modal-loader' ).modal('hide');
          
          $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
          $('#modal-message').modal('show');
          
          if (response.status == 'success'){
            $('.modal-costos_origen_china').modal('hide');
              
            $('#moda-message-content').addClass( 'bg-' + response.status);
            $('.modal-title-message').text(response.message);
            setTimeout(function() {$('#modal-message').modal('hide');}, 2100);
            
            location.reload();
          } else {
            $('#moda-message-content').addClass( 'bg-danger' );
            $('.modal-title-message').text(response.message);
            setTimeout(function() {$('#modal-message').modal('hide');}, 3200);
          }
          
          $( '#btn-save_costos_origen_china' ).text('');
          $( '#btn-save_costos_origen_china' ).append( 'Guardar' );
          $( '#btn-save_costos_origen_china' ).attr('disabled', false);
      },
      error: function (jqXHR, textStatus, errorThrown) {
          //$( '#modal-loader' ).modal('hide');
          $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
          
          $( '#modal-message' ).modal('show');
          $('#moda-message-content').addClass( 'bg-danger' );
          $('.modal-title-message').text('Problemas');
          setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
          
          //Message for developer
          console.log(jqXHR.responseText);
          
          $( '#btn-save_costos_origen_china' ).text('');
          $( '#btn-save_costos_origen_china' ).append( 'Guardar' );
          $( '#btn-save_costos_origen_china' ).attr('disabled', false);
      }
    });
  });
  
	$(document).on('click', '#btn-guardar_docs_exportacion', function (e) {
    e.preventDefault();

    $( '#btn-guardar_docs_exportacion' ).text('');
    $( '#btn-guardar_docs_exportacion' ).attr('disabled', true);
    $( '#btn-guardar_docs_exportacion' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

    //$( '#modal-loader' ).modal('show');

    var postData = new FormData($("#form-docs_exportacion")[0]);
    $.ajax({
      url: base_url + 'AgenteCompra/PedidosPagados/docsExportacion',
      type: "POST",
      dataType: "JSON",
      data: postData,
      processData: false,
      contentType: false,
      success : function( response ){
        //$( '#modal-loader' ).modal('hide');
        
        $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
        $('#modal-message').modal('show');
        
        if (response.status == 'success'){
          $('.modal-docs_exportacion').modal('hide');
            
          $('#moda-message-content').addClass( 'bg-' + response.status);
          $('.modal-title-message').text(response.message);
          setTimeout(function() {$('#modal-message').modal('hide');}, 2100);
          
          location.reload();
        } else {
          $('#moda-message-content').addClass( 'bg-danger' );
          $('.modal-title-message').text(response.message);
          setTimeout(function() {$('#modal-message').modal('hide');}, 3200);
        }
        
        $( '#btn-guardar_docs_exportacion' ).text('');
        $( '#btn-guardar_docs_exportacion' ).append( 'Guardar' );
        $( '#btn-guardar_docs_exportacion' ).attr('disabled', false);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        //$( '#modal-loader' ).modal('hide');
        $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
        
        $( '#modal-message' ).modal('show');
        $('#moda-message-content').addClass( 'bg-danger' );
        $('.modal-title-message').text('Problemas');
        setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
        
        //Message for developer
        console.log(jqXHR.responseText);
        
        $( '#btn-guardar_docs_exportacion' ).text('');
        $( '#btn-guardar_docs_exportacion' ).append( 'Guardar' );
        $( '#btn-guardar_docs_exportacion' ).attr('disabled', false);
      }
    });
  });
  

	$(document).on('click', '#btn-guardar_despacho_shipper', function (e) {
    e.preventDefault();
    if( !$('#inlineCheckbox1').prop('checked') ) {
      alert('Debes seleccionar entrega de Carga');
    } else if (!$('#inlineCheckbox2').prop('checked') ) {
      alert('Debes seleccionar entrega de Documentos');
    } else {
      $( '#btn-guardar_despacho_shipper' ).text('');
      $( '#btn-guardar_despacho_shipper' ).attr('disabled', true);
      $( '#btn-guardar_despacho_shipper' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

      //$( '#modal-loader' ).modal('show');

      url = base_url + 'AgenteCompra/PedidosPagados/despachoShipper';
        $.ajax({
        type		  : 'POST',
        dataType	: 'JSON',
        url		    : url,
        data		  : $('#form-despacho_shipper').serialize(),
        success : function( response ){
            //$( '#modal-loader' ).modal('hide');
            
            $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
            $('#modal-message').modal('show');
            
            if (response.status == 'success'){
              $('.modal-despacho_shipper').modal('hide');
                
              $('#moda-message-content').addClass( 'bg-' + response.status);
              $('.modal-title-message').text(response.message);
              setTimeout(function() {$('#modal-message').modal('hide');}, 2100);
              
              location.reload();
            } else {
              $('#moda-message-content').addClass( 'bg-danger' );
              $('.modal-title-message').text(response.message);
              setTimeout(function() {$('#modal-message').modal('hide');}, 3200);
            }
            
            $( '#btn-guardar_despacho_shipper' ).text('');
            $( '#btn-guardar_despacho_shipper' ).append( 'Guardar' );
            $( '#btn-guardar_despacho_shipper' ).attr('disabled', false);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            //$( '#modal-loader' ).modal('hide');
            $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
            
            $( '#modal-message' ).modal('show');
            $('#moda-message-content').addClass( 'bg-danger' );
            $('.modal-title-message').text('Problemas al guardar');
            setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
            
            //Message for developer
            console.log(jqXHR.responseText);
            
            $( '#btn-guardar_despacho_shipper' ).text('');
            $( '#btn-guardar_despacho_shipper' ).append( 'Guardar' );
            $( '#btn-guardar_despacho_shipper' ).attr('disabled', false);
        }
      });
    }
  });
  
	$(document).on('click', '#btn-save_revision_bl', function (e) {
    e.preventDefault();
    
    $( '#btn-save_revision_bl' ).text('');
    $( '#btn-save_revision_bl' ).attr('disabled', true);
    $( '#btn-save_revision_bl' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

    //$( '#modal-loader' ).modal('show');

    url = base_url + 'AgenteCompra/PedidosPagados/revisionBL';
      $.ajax({
      type		  : 'POST',
      dataType	: 'JSON',
      url		    : url,
      data		  : $('#form-revision_bl').serialize(),
      success : function( response ){
          //$( '#modal-loader' ).modal('hide');
          
          $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
          $('#modal-message').modal('show');
          
          if (response.status == 'success'){
            $('.modal-revision_bl').modal('hide');
              
            $('#moda-message-content').addClass( 'bg-' + response.status);
            $('.modal-title-message').text(response.message);
            setTimeout(function() {$('#modal-message').modal('hide');}, 2100);
            
            location.reload();
          } else {
            $('#moda-message-content').addClass( 'bg-danger' );
            $('.modal-title-message').text(response.message);
            setTimeout(function() {$('#modal-message').modal('hide');}, 3200);
          }
          
          $( '#btn-save_revision_bl' ).text('');
          $( '#btn-save_revision_bl' ).append( 'Guardar' );
          $( '#btn-save_revision_bl' ).attr('disabled', false);
      },
      error: function (jqXHR, textStatus, errorThrown) {
          //$( '#modal-loader' ).modal('hide');
          $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
          
          $( '#modal-message' ).modal('show');
          $('#moda-message-content').addClass( 'bg-danger' );
          $('.modal-title-message').text('Problemas al guardar');
          setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
          
          //Message for developer
          console.log(jqXHR.responseText);
          
          $( '#btn-save_revision_bl' ).text('');
          $( '#btn-save_revision_bl' ).append( 'Guardar' );
          $( '#btn-save_revision_bl' ).attr('disabled', false);
      }
    });
  });
  
	$(document).on('click', '#btn-guardar_entrega_docs_cliente', function (e) {
    e.preventDefault();

    /*
    if( !$('#entrega_docs_cliente-inlineCheckbox1').prop('checked') ) {
      alert('Debes seleccionar Commercial Invoice');
    } else if (($( '[name="entrega_docs_cliente-Nu_Tipo_Incoterms"]' ).val() ==3 || $( '[name="entrega_docs_cliente-Nu_Tipo_Incoterms"]' ).val() ==4) && !$('#entrega_docs_cliente-inlineCheckbox2').prop('checked') ) {
      alert('Debes seleccionar BL');
    } else if (!$('#entrega_docs_cliente-inlineCheckbox3').prop('checked') ) {
      alert('Debes seleccionar FTA Detalle');
    } else if (!$('#entrega_docs_cliente-inlineCheckbox4').prop('checked') ) {
      alert('Debes seleccionar Packing List');
    } else if (!$('#entrega_docs_cliente-inlineCheckbox5').prop('checked') ) {
      alert('Debes seleccionar FTA');
    } else {
    */
      $( '#btn-guardar_entrega_docs_cliente' ).text('');
      $( '#btn-guardar_entrega_docs_cliente' ).attr('disabled', true);
      $( '#btn-guardar_entrega_docs_cliente' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

      //$( '#modal-loader' ).modal('show');

      url = base_url + 'AgenteCompra/PedidosPagados/entregaDocsCliente';
        $.ajax({
        type		  : 'POST',
        dataType	: 'JSON',
        url		    : url,
        data		  : $('#form-entrega_docs_cliente').serialize(),
        success : function( response ){
            //$( '#modal-loader' ).modal('hide');
            
            $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
            $('#modal-message').modal('show');
            
            if (response.status == 'success'){
              $('.modal-entrega_docs_cliente').modal('hide');
                
              $('#moda-message-content').addClass( 'bg-' + response.status);
              $('.modal-title-message').text(response.message);
              setTimeout(function() {$('#modal-message').modal('hide');}, 2100);
              
              location.reload();
            } else {
              $('#moda-message-content').addClass( 'bg-danger' );
              $('.modal-title-message').text(response.message);
              setTimeout(function() {$('#modal-message').modal('hide');}, 3200);
            }
            
            $( '#btn-guardar_entrega_docs_cliente' ).text('');
            $( '#btn-guardar_entrega_docs_cliente' ).append( 'Guardar' );
            $( '#btn-guardar_entrega_docs_cliente' ).attr('disabled', false);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            //$( '#modal-loader' ).modal('hide');
            $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
            
            $( '#modal-message' ).modal('show');
            $('#moda-message-content').addClass( 'bg-danger' );
            $('.modal-title-message').text('Problemas al guardar');
            setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
            
            //Message for developer
            console.log(jqXHR.responseText);
            
            $( '#btn-guardar_entrega_docs_cliente' ).text('');
            $( '#btn-guardar_entrega_docs_cliente' ).append( 'Guardar' );
            $( '#btn-guardar_entrega_docs_cliente' ).attr('disabled', false);
        }
      });
    //}
  });
  
	$(document).on('click', '#btn-save_pagos_logisticos', function (e) {
    e.preventDefault();

    $( '#btn-save_pagos_logisticos' ).text('');
    $( '#btn-save_pagos_logisticos' ).attr('disabled', true);
    $( '#btn-save_pagos_logisticos' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

    //$( '#modal-loader' ).modal('show');

    var postData = new FormData($("#form-pagos_logisticos")[0]);
    $.ajax({
      url: base_url + 'AgenteCompra/PedidosPagados/pagosLogisticos',
      type: "POST",
      dataType: "JSON",
      data: postData,
      processData: false,
      contentType: false,
      success : function( response ){
          //$( '#modal-loader' ).modal('hide');
          
          $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
          $('#modal-message').modal('show');
          
          if (response.status == 'success'){
            $('.modal-pagos_logisticos').modal('hide');
              
            $('#moda-message-content').addClass( 'bg-' + response.status);
            $('.modal-title-message').text(response.message);
            setTimeout(function() {$('#modal-message').modal('hide');}, 2100);
            
            location.reload();
          } else {
            $('#moda-message-content').addClass( 'bg-danger' );
            $('.modal-title-message').text(response.message);
            setTimeout(function() {$('#modal-message').modal('hide');}, 3200);
          }
          
          $( '#btn-save_pagos_logisticos' ).text('');
          $( '#btn-save_pagos_logisticos' ).append( 'Guardar' );
          $( '#btn-save_pagos_logisticos' ).attr('disabled', false);
      },
      error: function (jqXHR, textStatus, errorThrown) {
          //$( '#modal-loader' ).modal('hide');
          $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
          
          $( '#modal-message' ).modal('show');
          $('#moda-message-content').addClass( 'bg-danger' );
          $('.modal-title-message').text('Problemas');
          setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
          
          //Message for developer
          console.log(jqXHR.responseText);
          
          $( '#btn-save_pagos_logisticos' ).text('');
          $( '#btn-save_pagos_logisticos' ).append( 'Guardar' );
          $( '#btn-save_pagos_logisticos' ).attr('disabled', false);
      }
    });
  });
  
  $("#form-documento_proveedor_exportacion").on('submit',function(e){
    e.preventDefault();

    $('.help-block').empty();
    $('.form-group').removeClass('has-error');

    if(document.getElementById('documento_proveedor_exportacion-Txt_Url_Imagen_Proveedor_Doc_Exportacion').files.length == 0) {
      $('#documento_proveedor_exportacion-Txt_Url_Imagen_Proveedor_Doc_Exportacion').closest('.form-group').find('.help-block').html('Empty file');
      $('#documento_proveedor_exportacion-Txt_Url_Imagen_Proveedor_Doc_Exportacion').closest('.form-group').removeClass('has-success').addClass('has-error');
    }else {
      var postData = new FormData($("#form-documento_proveedor_exportacion")[0]);
      $.ajax({
        url: base_url + 'AgenteCompra/PedidosPagados/addFileProveedorDocumentoExportacion',
        type: "POST",
        dataType: "JSON",
        data: postData,
        processData: false,
        contentType: false
      })
      .done(function(response) {
        $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
        $('#modal-message').modal('show');

        if(response.status == 'success') {
          $('#modal-documento_proveedor_exportacion').modal('hide');

          $('#moda-message-content').addClass( 'bg-' + response.status);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
          
          location.reload();
        } else {
          $('#moda-message-content').addClass( 'bg-danger' );
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 2100);
        }
      });
    }
  });
});

function editarCliente(ID_Entidad){
  //alert(ID_Entidad);
  $( '#form-cliente' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();

  $('.modal-cliente').modal('show');

  $( '[name="ID_Entidad"]' ).val('');
  $( '[name="ENo_Entidad"]' ).val('');
  
  url = base_url + 'Ventas/ReglasVenta/ClienteController/ajax_edit/' + ID_Entidad;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      console.log(response);

      $( '[name="ID_Entidad"]' ).val(response.ID_Entidad);
      $( '[name="ENo_Entidad"]' ).val(response.No_Entidad);

      //empresa
      $( '[name="No_Entidad"]' ).val(response.No_Entidad);
      $( '[name="Nu_Documento_Identidad"]' ).val(response.Nu_Documento_Identidad);

      //cliente
      $( '[name="No_Contacto"]' ).val(response.No_Contacto);
      $( '[name="Nu_Documento_Identidad_Externo"]' ).val(response.Nu_Documento_Identidad_Externo);
    },
    error: function (jqXHR, textStatus, errorThrown) {
      //$( '#modal-loader' ).modal('hide');
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

function verificarDatosExportacion(id, iIdTareaPedido){
  $( '#form-cliente_modal_paso1' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();

  $( '[name="cliente_modal_paso1-ID_Pedido_Cabecera"]' ).val(id);
  $( '[name="cliente_modal_paso1-iIdTareaPedido"]' ).val(iIdTareaPedido);

  $(' .modal-cliente_modal_paso1 ').modal('show');
  
  var selected = '';

  url = base_url + 'AgenteCompra/PedidosPagados/getBookingEntidad/' + id;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      console.log(response);
      
      $('.div-cliente_modal_paso1-trading').hide();
      $('.div-cliente_modal_paso1-consolidatrading').hide();
      if(response.Nu_Tipo_Servicio==1){//1=trading
        $('.div-cliente_modal_paso1-trading').show();
      } else if(response.Nu_Tipo_Servicio==1){//1=trading
        $('.div-cliente_modal_paso1-consolidatrading').show();
      }

      $( '#cliente_modal_paso1-No_Entidad' ).html(response.No_Entidad);
      $( '#cliente_modal_paso1-Nu_Documento_Identidad' ).html(response.Nu_Documento_Identidad);

      $( '#cliente_modal_paso1-No_Contacto' ).html(response.No_Contacto);
      $( '#cliente_modal_paso1-Nu_Documento_Identidad_Externo' ).html(response.Nu_Documento_Identidad_Externo);

      var sNombreExportador = 'INTERNATIONAL PRO TRADING CO., LIMITED';
      if(response.Nu_Tipo_Exportador==2){
        sNombreExportador = 'CHRIS FACTORY LIMITED';
      }

      $( '#cliente_modal_paso1-exportador' ).html(sNombreExportador);
      
      var sNombreIncoterms = 'EXW';
      if(response.Nu_Tipo_Incoterms==2){
        sNombreIncoterms = 'FOB';
      } else if(response.Nu_Tipo_Incoterms==3){
        sNombreIncoterms = 'CIF';
      } else if(response.Nu_Tipo_Incoterms==4){
        sNombreIncoterms = 'DDP';
      }
      $( '#cliente_modal_paso1-Nu_Tipo_Incoterms' ).html(sNombreIncoterms);

      var sNombreTransporteMaritimo = 'FCL';
      if(response.Nu_Tipo_Transporte_Maritimo==2){
        sNombreTransporteMaritimo = 'LCL';
      }
      $( '#cliente_modal_paso1-Nu_Tipo_Transporte_Maritimo' ).html(sNombreTransporteMaritimo);
    },
    error: function (jqXHR, textStatus, errorThrown) {
      //$( '#modal-loader' ).modal('hide');
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

function bookingInspeccion(id, iIdTareaPedido, ID_Usuario_Interno_China, sCorrelativoCotizacion){
  $( '#form-booking_inspeccion' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();

  $( '[name="booking_inspeccion-ID_Pedido_Cabecera"]' ).val(id);
  $( '[name="booking_inspeccion-Nu_ID_Interno"]' ).val(iIdTareaPedido);
  $( '[name="booking_inspeccion-ID_Usuario_Interno_China"]' ).val(ID_Usuario_Interno_China);
  $( '[name="booking_inspeccion-sCorrelativoCotizacion"]' ).val(sCorrelativoCotizacion);

  $(' .modal-booking_inspeccion ').modal('show');
  
  url = base_url + 'AgenteCompra/PedidosPagados/getBooking/' + id;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      console.log(response);

      $( '[name="booking_inspeccion-Qt_Caja_Total_Booking-Actual"]' ).val(response.Qt_Caja_Total_Booking);
      $( '[name="booking_inspeccion-Qt_Cbm_Total_Booking-Actual"]' ).val(response.Qt_Cbm_Total_Booking);
      $( '[name="booking_inspeccion-Qt_Peso_Total_Booking-Actual"]' ).val(response.Qt_Peso_Total_Booking);

      $( '[name="booking_inspeccion-Qt_Caja_Total_Booking"]' ).val(response.Qt_Caja_Total_Booking);
      $( '[name="booking_inspeccion-Qt_Cbm_Total_Booking"]' ).val(response.Qt_Cbm_Total_Booking);
      $( '[name="booking_inspeccion-Qt_Peso_Total_Booking"]' ).val(response.Qt_Peso_Total_Booking);
      
      $( '[name="booking_inspeccion-No_Observacion_Inspeccion"]' ).val(response.No_Observacion_Inspeccion);

      //$( '#booking_inspeccion-Qt_Caja_Total_Booking' ).html(response.Qt_Caja_Total_Booking);
      //$( '#booking_inspeccion-Qt_Cbm_Total_Booking' ).html(response.Qt_Cbm_Total_Booking);
      //$( '#booking_inspeccion-Qt_Peso_Total_Booking' ).html(response.Qt_Peso_Total_Booking);
    },
    error: function (jqXHR, textStatus, errorThrown) {
      //$( '#modal-loader' ).modal('hide');
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

function bookingTrading(id){
  $( '#form-reserva_booking_trading' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();

  $( '[name="reserva_booking_trading-ID_Pedido_Cabecera"]' ).val(id);

  $(' .modal-reserva_booking_trading ').modal('show');
  
  var selected = '';

  url = base_url + 'AgenteCompra/PedidosPagados/getBooking/' + id;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      console.log(response);

      var sNombreTransporteMaritimo = 'FCL';
      $('.div-tipo_contenedor').show();
      if(response.Nu_Tipo_Transporte_Maritimo==2){
        sNombreTransporteMaritimo = 'LCL';
        $('.div-tipo_contenedor').hide();
      }

      $( '#reserva_booking_trading-Qt_Cbm_Total_Booking' ).html(response.Qt_Cbm_Total_Booking);
      $( '#reserva_booking_trading-Nu_Tipo_Transporte_Maritimo' ).html(sNombreTransporteMaritimo);

      var sNombreIncoterms = 'EXW';
      if(response.Nu_Tipo_Incoterms==2){
        sNombreIncoterms = 'FOB';
      } else if(response.Nu_Tipo_Incoterms==3){
        sNombreIncoterms = 'CIF';
      } else if(response.Nu_Tipo_Incoterms==4){
        sNombreIncoterms = 'DDP';
      }
      $( '#reserva_booking_trading-Nu_Tipo_Incoterms' ).html(sNombreIncoterms);

      $('#cbo-shipper').html('<option value="0" selected="selected">Buscando...</option>');
      url = base_url + 'HelperImportacionController/getShipper';
      $.post(url, {}, function (responseShipper) {
        console.log(responseShipper);
        if (responseShipper.status == 'success') {
          $('#cbo-shipper').html('<option value="0" selected="selected">- Seleccionar -</option>');
          var l = responseShipper.result.length;
          for (var x = 0; x < l; x++) {
            selected = '';
            if(response.ID_Shipper == responseShipper.result[x].ID)
              selected = 'selected="selected"';
            $('#cbo-shipper').append('<option value="' + responseShipper.result[x].ID + '" ' + selected + '>' + responseShipper.result[x].Nombre + '</option>');
          }
        } else {
          $('#cbo-shipper').html('<option value="0" selected="selected">Sin registro</option>');
          if (responseShipper.sMessageSQL !== undefined) {
            console.log(responseShipper.sMessageSQL);
          }
          console.log(responseShipper.message);
        }
      }, 'JSON');

      $( '[name="reserva_booking_trading-No_Tipo_Contenedor"]' ).val(response.No_Tipo_Contenedor);
      $( '[name="reserva_booking_trading-No_Naviera"]' ).val(response.No_Naviera);
      $( '[name="reserva_booking_trading-No_Dias_Transito"]' ).val(response.No_Dias_Transito);
      $( '[name="reserva_booking_trading-No_Dias_Libres"]' ).val(response.No_Dias_Libres);
    },
    error: function (jqXHR, textStatus, errorThrown) {
      //$( '#modal-loader' ).modal('hide');
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

function costosOrigenTradingChina(id, iIdTareaPedido){
  $( '#form-costos_origen_china' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();

  $(' .modal-costos_origen_china ').modal('show');

  $( '[name="costos_origen_china-ID_Pedido_Cabecera"]' ).val(id);
  
  var selected = '';

  url = base_url + 'AgenteCompra/PedidosPagados/getBooking/' + id;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      console.log(response);

      $( '#costos_origen_china-tipo_cambio' ).html(response.yuan_venta);
      $( '[name="costos_origen_china-Ss_Tipo_Cambio"]' ).val(response.yuan_venta);

      if(parseFloat(response.Ss_Pago_Otros_Flete_China_Yuan)>0){
        $( '[name="costos_origen_china-Ss_Pago_Otros_Flete_China_Yuan"]' ).val(response.Ss_Pago_Otros_Flete_China_Yuan);
      }
      $( '[name="costos_origen_china-Ss_Pago_Otros_Flete_China_Dolar"]' ).val(response.Ss_Pago_Otros_Flete_China_Dolar);

      if(parseFloat(response.Ss_Pago_Otros_Costo_Origen_China_Yuan)>0){
        $( '[name="costos_origen_china-Ss_Pago_Otros_Costo_Origen_China_Yuan"]' ).val(response.Ss_Pago_Otros_Costo_Origen_China_Yuan);
      }
      $( '[name="costos_origen_china-Ss_Pago_Otros_Costo_Origen_China_Dolar"]' ).val(response.Ss_Pago_Otros_Costo_Origen_China_Dolar);

      if(parseFloat(response.Ss_Pago_Otros_Costo_Fta_China_Yuan)>0){
        $( '[name="costos_origen_china-Ss_Pago_Otros_Costo_Fta_China_Yuan"]' ).val(response.Ss_Pago_Otros_Costo_Fta_China_Yuan);
      }
      $( '[name="costos_origen_china-Ss_Pago_Otros_Costo_Fta_China_Dolar"]' ).val(response.Ss_Pago_Otros_Costo_Fta_China_Dolar);

      if(parseFloat(response.Ss_Pago_Otros_Cuadrilla_China_Yuan)>0){
        $( '[name="costos_origen_china-Ss_Pago_Otros_Cuadrilla_China_Yuan"]' ).val(response.Ss_Pago_Otros_Cuadrilla_China_Yuan);
      }
      $( '[name="costos_origen_china-Ss_Pago_Otros_Cuadrilla_China_Dolar"]' ).val(response.Ss_Pago_Otros_Cuadrilla_China_Dolar);

      if(parseFloat(response.Ss_Pago_Otros_Costos_China_Yuan)>0){
        $( '[name="costos_origen_china-Ss_Pago_Otros_Costos_China_Yuan"]' ).val(response.Ss_Pago_Otros_Costos_China_Yuan);
      }
      $( '[name="costos_origen_china-Ss_Pago_Otros_Costos_China_Dolar"]' ).val(response.Ss_Pago_Otros_Costos_China_Dolar);

      if(response.No_Concepto_Pago_Cuadrilla!='' && response.No_Concepto_Pago_Cuadrilla!=null){
        $( '[name="costos_origen_china-No_Concepto_Pago_Cuadrilla"]' ).val(response.No_Concepto_Pago_Cuadrilla);
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      //$( '#modal-loader' ).modal('hide');
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

function docsExportacion(id, iIdTareaPedido){
  $( '#form-docs_exportacion' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();

  $(' .modal-docs_exportacion ').modal('show');
  
  $( '[name="docs_exportacion-ID_Pedido_Cabecera"]' ).val(id);
  $( '[name="docs_exportacion-iIdTareaPedido"]' ).val(iIdTareaPedido);
  
  var selected = '';

  url = base_url + 'AgenteCompra/PedidosPagados/getBooking/' + id;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      console.log(response);

      $('.div-docs_shipper').hide();
      $('.div-bl').hide();
      if(response.Nu_Tipo_Incoterms == 3 || response.Nu_Tipo_Incoterms == 4){
        $('.div-docs_shipper').show();
        $('.div-bl').show();
      }
      
      var url_dowloand = response.Txt_Url_Archivo_Exportacion_Docs_Shipper;
      url_dowloand = url_dowloand.replace('https://','../../');
      url_dowloand = url_dowloand.replace('assets','public_html/assets');
      $("#docs_exportacion-Txt_Url_Archivo_Exportacion_Docs_Shipper-a").attr("href", url_dowloand);
      
      var url_dowloand = response.Txt_Url_Archivo_Exportacion_Commercial_Invoice;
      url_dowloand = url_dowloand.replace('https://','../../');
      url_dowloand = url_dowloand.replace('assets','public_html/assets');
      $("#docs_exportacion-Txt_Url_Archivo_Exportacion_Commercial_Invoice-a").attr("href", url_dowloand);
      
      var url_dowloand = response.Txt_Url_Archivo_Exportacion_Packing_List;
      url_dowloand = url_dowloand.replace('https://','../../');
      url_dowloand = url_dowloand.replace('assets','public_html/assets');
      $("#docs_exportacion-Txt_Url_Archivo_Exportacion_Packing_List-a").attr("href", url_dowloand);
      
      var url_dowloand = response.Txt_Url_Archivo_Exportacion_Bl;
      url_dowloand = url_dowloand.replace('https://','../../');
      url_dowloand = url_dowloand.replace('assets','public_html/assets');
      $("#docs_exportacion-Txt_Url_Archivo_Exportacion_Bl-a").attr("href", url_dowloand);
      
      var url_dowloand = response.Txt_Url_Archivo_Exportacion_Fta;
      url_dowloand = url_dowloand.replace('https://','../../');
      url_dowloand = url_dowloand.replace('assets','public_html/assets');
      $("#docs_exportacion-Txt_Url_Archivo_Exportacion_Fta-a").attr("href", url_dowloand);
      //falta descargar file
    },
    error: function (jqXHR, textStatus, errorThrown) {
      //$( '#modal-loader' ).modal('hide');
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

function despachoShipper(id, iIdTareaPedido){
  $( '#form-despacho_shipper' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();

  $( '[name="despacho_shipper-ID_Pedido_Cabecera"]' ).val(id);

  $(' .modal-despacho_shipper ').modal('show');
  $(' #form-despacho_shipper ' )[0].reset();
  
  url = base_url + 'AgenteCompra/PedidosPagados/getBookingEntidad/' + id;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      console.log(response);
      $( '#despacho_shipper-span-empresa' ).html(response.No_Shipper);
      $( '#despacho_shipper-span-coordinador' ).html(response.No_Coordinador);
    },
    error: function (jqXHR, textStatus, errorThrown) {
      //$( '#modal-loader' ).modal('hide');
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

function revisionBL(id, iIdTareaPedido){
  $( '#form-revision_bl' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();

  $( '[name="revision_bl-ID_Pedido_Cabecera"]' ).val(id);
  $( '[name="revision_bl-iIdTareaPedido"]' ).val(iIdTareaPedido);

  $(' .modal-revision_bl ').modal('show');
  
  var selected = '';

  url = base_url + 'AgenteCompra/PedidosPagados/getBookingEntidad/' + id;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      console.log(response);

      $( '[name="revision_bl-ID_Entidad"]' ).val(response.ID_Entidad);
      $( '[name="revision_bl-ENo_Entidad"]' ).val(response.No_Entidad);

      $( '[name="revision_bl-No_Entidad"]' ).val(response.No_Entidad);
      $( '[name="revision_bl-Nu_Documento_Identidad"]' ).val(response.Nu_Documento_Identidad);
      $( '[name="revision_bl-Txt_Direccion_Entidad"]' ).val(response.Txt_Direccion_Entidad);
      
      var sNombreExportador = 'ProBusiness Yiwu';
      if(response.Nu_Tipo_Exportador==2){
        sNombreExportador = 'CHRIS FACTORY LIMITED';
      }

      $( '#revision_bl-exportador' ).html(sNombreExportador);
      $( '#revision_bl-shipper' ).html(response.No_Shipper);

      $( '#revision_bl-Qt_Caja_Total_Booking' ).html(response.Qt_Caja_Total_Booking);
      $( '#revision_bl-Qt_Cbm_Total_Booking' ).html(response.Qt_Cbm_Total_Booking);
      $( '#revision_bl-Qt_Peso_Total_Booking' ).html(response.Qt_Peso_Total_Booking);
      
      var sNombreTransporteMaritimo = 'FCL';
      if(response.Nu_Tipo_Transporte_Maritimo==2){
        sNombreTransporteMaritimo = 'LCL';
      }
      $( '#revision_bl-Nu_Tipo_Transporte_Maritimo' ).html(sNombreTransporteMaritimo);

      $( '[name="revision_bl-Txt_Descripcion_BL_China"]' ).val(response.Txt_Descripcion_BL_China);
    },
    error: function (jqXHR, textStatus, errorThrown) {
      //$( '#modal-loader' ).modal('hide');
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

function entregaDocsCliente(id, iIdTareaPedido){
  $( '#form-entrega_docs_cliente' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();

  $( '[name="entrega_docs_cliente-ID_Pedido_Cabecera"]' ).val(id);

  $(' .modal-entrega_docs_cliente ').modal('show');
  var selected = '';

  url = base_url + 'AgenteCompra/PedidosPagados/getBooking/' + id;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      console.log(response);
      
      $('#entrega_docs_cliente-inlineCheckbox1').prop('checked', false);
      $('#entrega_docs_cliente-inlineCheckbox2').prop('checked', false);
      $('#entrega_docs_cliente-inlineCheckbox3').prop('checked', false);
      $('#entrega_docs_cliente-inlineCheckbox4').prop('checked', false);
      $('#entrega_docs_cliente-inlineCheckbox5').prop('checked', false);

      if(response.Nu_Commercial_Invoice==1){
        $('#entrega_docs_cliente-inlineCheckbox1').prop('checked', true);
      }
      
      if(response.Nu_Packing_List==1){
        $('#entrega_docs_cliente-inlineCheckbox2').prop('checked', true);
      }
      
      if(response.Nu_BL==1){
        $('#entrega_docs_cliente-inlineCheckbox3').prop('checked', true);
      }
      
      if(response.Nu_FTA==1){
        $('#entrega_docs_cliente-inlineCheckbox4').prop('checked', true);
      }
      
      if(response.Nu_FTA_Detalle==1){
        $('#entrega_docs_cliente-inlineCheckbox5').prop('checked', true);
      }

      $( '[name="entrega_docs_cliente-Nu_Tipo_Incoterms"]' ).val(response.Nu_Tipo_Incoterms);

      $('.div-bl-entrega_docs').hide();
      if(response.Nu_Tipo_Incoterms == 3 || response.Nu_Tipo_Incoterms == 4){
        $('.div-bl-entrega_docs').show();
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      //$( '#modal-loader' ).modal('hide');
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

function pagosLogisticos(id, iIdTareaPedido){
  $( '#form-pagos_logisticos' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();

  $(' .modal-pagos_logisticos ').modal('show');

  $( '[name="pagos_logisticos-ID_Pedido_Cabecera"]' ).val(id);
  
  var selected = '', url_dowloand = '';

  url = base_url + 'AgenteCompra/PedidosPagados/getBooking/' + id;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      console.log(response);

      $( '#pagos_logisticos-shipper' ).html(response.No_Shipper);
      
      $('[name="pagos_logisticos-Ss_Pago_Otros_Flete_China_Yuan"]').prop('disabled', true);
      $('[name="pagos_logisticos-Ss_Pago_Otros_Flete_China_Dolar"]').prop('disabled', true);

      $('[name="pagos_logisticos-Ss_Pago_Otros_Costo_Origen_China_Yuan"]').prop('disabled', true);
      $('[name="pagos_logisticos-Ss_Pago_Otros_Costo_Origen_China_Dolar"]').prop('disabled', true);

      $('[name="pagos_logisticos-Ss_Pago_Otros_Costo_Fta_China_Yuan"]').prop('disabled', true);
      $('[name="pagos_logisticos-Ss_Pago_Otros_Costo_Fta_China_Dolar"]').prop('disabled', true);
      
      $('[name="pagos_logisticos-Ss_Pago_Otros_Cuadrilla_China_Yuan"]').prop('disabled', true);
      $('[name="pagos_logisticos-Ss_Pago_Otros_Cuadrilla_China_Dolar"]').prop('disabled', true);
      
      $('[name="pagos_logisticos-Ss_Pago_Otros_Costos_China_Yuan"]').prop('disabled', true);
      $('[name="pagos_logisticos-Ss_Pago_Otros_Costos_China_Dolar"]').prop('disabled', true);

      $( '[name="pagos_logisticos-Ss_Pago_Otros_Flete_China_Yuan"]' ).val(response.Ss_Pago_Otros_Flete_China_Yuan);
      $( '[name="pagos_logisticos-Ss_Pago_Otros_Flete_China_Dolar"]' ).val(response.Ss_Pago_Otros_Flete_China_Dolar);

      $( '[name="pagos_logisticos-Ss_Pago_Otros_Costo_Origen_China_Yuan"]' ).val(response.Ss_Pago_Otros_Costo_Origen_China_Yuan);
      $( '[name="pagos_logisticos-Ss_Pago_Otros_Costo_Origen_China_Dolar"]' ).val(response.Ss_Pago_Otros_Costo_Origen_China_Dolar);

      $( '[name="pagos_logisticos-Ss_Pago_Otros_Costo_Fta_China_Yuan"]' ).val(response.Ss_Pago_Otros_Costo_Fta_China_Yuan);
      $( '[name="pagos_logisticos-Ss_Pago_Otros_Costo_Fta_China_Dolar"]' ).val(response.Ss_Pago_Otros_Costo_Fta_China_Dolar);

      var SubTotalYuan = ( parseFloat(response.Ss_Pago_Otros_Flete_China_Yuan) + parseFloat(response.Ss_Pago_Otros_Costo_Origen_China_Yuan) + parseFloat(response.Ss_Pago_Otros_Costo_Fta_China_Yuan) );
      var SubTotalDolar = ( parseFloat(response.Ss_Pago_Otros_Flete_China_Dolar) + parseFloat(response.Ss_Pago_Otros_Costo_Origen_China_Dolar) + parseFloat(response.Ss_Pago_Otros_Costo_Fta_China_Dolar) );

      $( '#pagos_logisticos-subtotal-yuan' ).html(SubTotalYuan);
      $( '#pagos_logisticos-subtotal-dolar' ).html(SubTotalDolar);

      $( '[name="pagos_logisticos-Ss_Pago_Otros_Cuadrilla_China_Yuan"]' ).val(response.Ss_Pago_Otros_Cuadrilla_China_Yuan);
      $( '[name="pagos_logisticos-Ss_Pago_Otros_Cuadrilla_China_Dolar"]' ).val(response.Ss_Pago_Otros_Cuadrilla_China_Dolar);

      $( '[name="pagos_logisticos-Ss_Pago_Otros_Costos_China_Yuan"]' ).val(response.Ss_Pago_Otros_Costos_China_Yuan);
      $( '[name="pagos_logisticos-Ss_Pago_Otros_Costos_China_Dolar"]' ).val(response.Ss_Pago_Otros_Costos_China_Dolar);

      var TotalYuan = ( parseFloat(response.Ss_Pago_Otros_Cuadrilla_China_Yuan) + parseFloat(response.Ss_Pago_Otros_Costos_China_Yuan) );
      var TotalDolar = ( parseFloat(response.Ss_Pago_Otros_Cuadrilla_China_Dolar) + parseFloat(response.Ss_Pago_Otros_Costos_China_Dolar) );

      $( '#pagos_logisticos-total-yuan' ).html(SubTotalYuan + TotalYuan);
      $( '#pagos_logisticos-total-dolar' ).html(SubTotalDolar + TotalDolar);

      $('.div-pagos_logisticos-cif_ddp').hide();
      if(response.Nu_Tipo_Incoterms == 3 || response.Nu_Tipo_Incoterms == 4){
        $('.div-pagos_logisticos-cif_ddp').show();
      }

      url_dowloand = response.Txt_Url_Pago_Otros_Flete_China;
      url_dowloand = url_dowloand.replace('https://','../../');
      url_dowloand = url_dowloand.replace('assets','public_html/assets');
      $("#pagos_logisticos-Txt_Url_Pago_Otros_Flete_China-a").attr("href", url_dowloand);
      
      url_dowloand = response.Txt_Url_Pago_Otros_Costo_Origen_China;
      url_dowloand = url_dowloand.replace('https://','../../');
      url_dowloand = url_dowloand.replace('assets','public_html/assets');
      $("#pagos_logisticos-Txt_Url_Pago_Otros_Costo_Origen_China-a").attr("href", url_dowloand);
      
      url_dowloand = response.Txt_Url_Pago_Otros_Costo_Fta_China;
      url_dowloand = url_dowloand.replace('https://','../../');
      url_dowloand = url_dowloand.replace('assets','public_html/assets');
      $("#pagos_logisticos-Txt_Url_Pago_Otros_Costo_Fta_China-a").attr("href", url_dowloand);
      
      url_dowloand = response.Txt_Url_Pago_Otros_Cuadrilla_China;
      url_dowloand = url_dowloand.replace('https://','../../');
      url_dowloand = url_dowloand.replace('assets','public_html/assets');
      $("#pagos_logisticos-Txt_Url_Pago_Otros_Cuadrilla_China-a").attr("href", url_dowloand);
      
      url_dowloand = response.Txt_Url_Pago_Otros_Costos_China;
      url_dowloand = url_dowloand.replace('https://','../../');
      url_dowloand = url_dowloand.replace('assets','public_html/assets');
      $("#pagos_logisticos-Txt_Url_Pago_Otros_Costos_China-a").attr("href", url_dowloand);
    },
    error: function (jqXHR, textStatus, errorThrown) {
      //$( '#modal-loader' ).modal('hide');
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

function documentoProveedorExportacion(id, sCorrelativo){
  $( '[name="documento_proveedor_exportacion-id_cabecera"]' ).val(id);
  $( '[name="documento_proveedor_exportacion-correlativo"]' ).val(sCorrelativo);

  $('#modal-documento_proveedor_exportacion').modal('show');
  $( '#form-documento_proveedor_exportacion' )[0].reset();
}