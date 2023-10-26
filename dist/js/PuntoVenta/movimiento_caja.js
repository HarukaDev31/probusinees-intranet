var url, iTipoReporte = 0;

$(function () {
  $('.select2').select2();

  $( '#div-movimiento_caja' ).hide();
  
  getReporteHTML();
  
  // Modal - precargar datos para monedas
	url = base_url + 'HelperController/getMonedas';
	$.post( url , function( response ){
		$( '#cbo-moneda' ).html('');
		var iCantidadRegistros=response.length;
		for (var i = 0; i < iCantidadRegistros; i++)
			$( '#cbo-moneda' ).append( '<option value="' + response[i]['ID_Moneda'] + '" data-no_signo="' + response[i]['No_Signo'] + '">' + response[i]['No_Moneda'] + '</option>' );
	}, 'JSON');
  // /. Modal - precargar datos para monedas
  
  $('#btn-ingreso_movimiento_caja').click(function (e) {
    $('#form-movimiento_caja')[0].reset();
    $('.form-group').removeClass('has-error');
    $('.form-group').removeClass('has-success');
    $('.help-block').empty();
    $('#hidden-id_tipo_operacion_caja').val( '' );

    e.preventDefault();
    if ($('#header-a-id_matricula_empleado').length) {
      url = base_url + 'HelperController/getTipoOperacionCaja';
      $.post( url, {Nu_Tipo:5}, function( response ){//5 = Ingreso de caja
        $( '.modal-movimiento_caja' ).modal('show');
        $( '#hidden-id_tipo_operacion_caja' ).val( response[0].ID_Tipo_Operacion_Caja );
      }, 'JSON');
      
      $( '#h3-title' ).text('Entrada de Dinero');

      $( '.modal-movimiento_caja' ).on('shown.bs.modal', function() {
        $( '#txt-ss_monto_caja' ).focus();
        $( '#txt-ss_monto_caja' ).select();
      })
    } else {
      alert('Primero se debe de aperturar caja');
    }// ./ if - else
  });
  
  $('#btn-salida_movimiento_caja').click(function (e) {
    $('#form-movimiento_caja')[0].reset();
    $('.form-group').removeClass('has-error');
    $('.form-group').removeClass('has-success');
    $('.help-block').empty();
    $('#hidden-id_tipo_operacion_caja').val('');

    e.preventDefault();
    if ($('#header-a-id_matricula_empleado').length) {
      url = base_url + 'HelperController/getTipoOperacionCaja';
      $.post( url, {Nu_Tipo:6}, function( response ){//6 = Salida de caja
        $( '.modal-movimiento_caja' ).modal('show');
        $( '#hidden-id_tipo_operacion_caja' ).val( response[0].ID_Tipo_Operacion_Caja );
      }, 'JSON');
      
      $( '#h3-title' ).text('Salida de Dinero');

      $( '.modal-movimiento_caja' ).on('shown.bs.modal', function() {
        $( '#txt-ss_monto_caja' ).focus();
        $( '#txt-ss_monto_caja' ).select();
      })
    } else {
      alert('Primero se debe de aperturar caja');
    }// ./ if - else
  });
  
  $('#btn-guardar_movimiento_caja').click(function (e) {
    e.preventDefault();
    guardarMovimientoCaja();
  });
})

function guardarMovimientoCaja(){
  if ( $( '#txt-ss_monto_caja' ).val().length == 0){
    $( '#txt-ss_monto_caja' ).closest('.form-group').find('.help-block').html('Ingresar monto');
    $( '#txt-ss_monto_caja' ).closest('.form-group').removeClass('has-success').addClass('has-error');
  
    scrollToError($('.modal-movimiento_caja .modal-body'), $( '#txt-ss_monto_caja' ));
  } else {
    $('.help-block').empty();
    $('.form-group').removeClass('has-error');

    $( '#btn-salir' ).attr('disabled', true);

    $( '#btn-guardar_movimiento_caja' ).text('');
    $( '#btn-guardar_movimiento_caja' ).attr('disabled', true);
    $( '#btn-guardar_movimiento_caja' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

    url = base_url + 'PuntoVenta/MovimientoCajaController/addMovimientoCaja';
    $.ajax({
      dataType: 'JSON',
      type: 'POST',
      url: url,
      data: $('#form-movimiento_caja').serialize(),
      success: function (response) {
        $('#modal-loader').modal('hide');

        $('.modal-message').removeClass('modal-danger modal-warning modal-success');
        $('#modal-message').modal('show');

        if (response.sStatus == 'success') {
          $('#txt-ss_monto_caja').val('');
          $('[name="area-txt_nota_caja"]').val('');

          $('.modal-movimiento_caja').modal('hide');
          $('.modal-message').addClass('modal-' + response.sStatus);
          $('.modal-title-message').text(response.sMessage);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);

          getReporteHTML();
        } else {
          $('.modal-message').addClass('modal-' + response.sStatus);
          $('.modal-title-message').text(response.sMessage);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1700);
        }

        $('#btn-guardar_movimiento_caja').text('');
        $('#btn-guardar_movimiento_caja').append('Guardar');
        $('#btn-guardar_movimiento_caja').attr('disabled', false);

        $('#btn-salir').attr('disabled', false);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        $('.modal-message').removeClass('modal-danger modal-warning modal-success');

        $('#modal-message').modal('show');
        $('.modal-message').addClass('modal-danger');
        $('.modal-title-message').text(textStatus + ' [' + jqXHR.status + ']: ' + errorThrown);
        setTimeout(function () { $('#modal-message').modal('hide'); }, 1700);

        //Message for developer
        console.log(jqXHR.responseText);

        $('#btn-guardar_movimiento_caja').text('');
        $('#btn-guardar_movimiento_caja').append('Guardar');
        $('#btn-guardar_movimiento_caja').attr('disabled', false);
      }
    });
  }
}

function getReporteHTML(){
  if ( $('#header-a-id_matricula_empleado').length ) {
    $( '#modal-loader' ).modal('show');

    $( '#table-movimiento_caja > tbody' ).empty();
    $( '#table-movimiento_caja > tfoot' ).empty();

    var arrPost = {};
    url = base_url + 'PuntoVenta/MovimientoCajaController/sendReporte';
    $.post( url, arrPost, function( response ){
      if ( response.sStatus == 'success' ) {
        var iTotalRegistros = response.arrData.length, response=response.arrData, tr_body='', sSignoMoneda='S/', fTotal = 0.00, fTotalGeneralEntrada=0.00, fTotalGeneralSalida=0.00, fTotalGeneral=0.00;
        for (var i = 0; i < iTotalRegistros; i++) {
          fTotal = (!isNaN(parseFloat(response[i].Ss_Total)) ? parseFloat(response[i].Ss_Total) : 0);

          if(response[i].Nu_Tipo==5)
            fTotalGeneralEntrada += parseFloat(fTotal);
          if(response[i].Nu_Tipo==6)
            fTotalGeneralSalida += parseFloat(fTotal);

          sSignoMoneda = response[i].No_Signo;

          tr_body +=
          "<tr>"
            +"<td class='text-center'><span class='label label-" + response[i].No_Class_Estado + "'>" + response[i].No_Tipo_Operacion_Caja + "</td>"
            +"<td class='text-center'>" + response[i].Fe_Movimiento + "</td>"
            +"<td class='text-center'>" + response[i].No_Signo + "</td>"
            +"<td class='text-right'>" + number_format(fTotal, 2) + "</td>"
            +"<td class='text-left'>" + response[i].Txt_Nota + "</td>"
            +"<td class='text-center'>" + response[i].sImpresion + "</td>"
          +"</tr>";
          
          tr_foot =
          "<tfoot>"
            +"<tr>"
              +"<th class='text-right' colspan='3'>Total ENTRADA</th>"
              +"<th class='text-right'>" + sSignoMoneda + " " + Math.round10(parseFloat(fTotalGeneralEntrada), -3) + "</th>"
            +"</tr>"
            +"<tr>"
              +"<th class='text-right' colspan='3'>Total SALIDA</th>"
              +"<th class='text-right'>" + sSignoMoneda + " " + Math.round10(parseFloat(fTotalGeneralSalida), -3) + "</th>"
            +"</tr>"
            +"<tr>"
              +"<th class='text-right' colspan='3'>Total ENTRADA - SALIDA</th>"
              +"<th class='text-right'>" + sSignoMoneda + " " + Math.round10(parseFloat(fTotalGeneralEntrada) - parseFloat(fTotalGeneralSalida), -3) + "</th>"
            +"</tr>"
          +"</tfoot>";
        }
      } else {
        if( response.sMessageSQL !== undefined ) {
          console.log(response.sMessageSQL);
        }
        tr_body +=
        "<tr>"
          +"<td colspan='7' class='text-center'>" + response.sMessage + "</td>"
        + "</tr>";
      } // ./ if arrData
      
      $( '#modal-loader' ).modal('hide');

      $( '#div-movimiento_caja' ).show();
      $( '#table-movimiento_caja > tbody' ).append(tr_body);
      $( '#table-movimiento_caja > tbody' ).after(tr_foot);
      
      $( '#btn-html_movimiento_caja' ).text('');
      $( '#btn-html_movimiento_caja' ).append( '<i class="fa fa-search"></i> Buscar' );
      $( '#btn-html_movimiento_caja' ).attr('disabled', false);
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
  } else {
    alert('Primero se debe de aperturar caja');
  }// ./ if - else
}

function imprimirMovimientoCaja(ID) {
  window.open(base_url + "PuntoVenta/MovimientoCajaController/imprimirMovimientoCaja/" + ID, "_blank", "location=yes,top=80,left=800,width=720,height=550,scrollbars=yes,status=yes");
}