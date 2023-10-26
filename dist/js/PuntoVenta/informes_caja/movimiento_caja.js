var url;

$(function () {
  $( '#modal-loader' ).modal('show');
  $( '#div-ingresos_egresos_caja_pos' ).hide();
  
  $('#cbo-tipo_operacion_caja_ingresos_egresos_caja_pos').html('<option value="0" selected="selected">- Vacío -</option>');
	url = base_url + 'HelperController/getDataGeneral';
	$.post( url, {sTipoData : 'movimiento_caja_pv'}, function( response ){
    if ( response.sStatus == 'success' ) {
      var iTotalRegistros = response.arrData.length, response=response.arrData;
      $( '#cbo-tipo_operacion_caja_ingresos_egresos_caja_pos' ).html( '<option value="0" selected="selected">- Todos -</option>');
      for (var i = 0; i < iTotalRegistros; i++)
        $( '#cbo-tipo_operacion_caja_ingresos_egresos_caja_pos' ).append( '<option value="' + response[i].ID + '">' + response[i].Nombre + '</option>' );
    } else {
      $( '#cbo-tipo_operacion_caja_ingresos_egresos_caja_pos' ).html( '<option value="0" selected="selected">- Vacío -</option>');
      console.log( response );
    }
		$( '#modal-loader' ).modal('hide');
  }, 'JSON');

  $('#cbo-filtro_almacen').html('<option value="0" selected="selected">- Todos -</option>');
  url = base_url + 'HelperController/getAlmacenes';
  var arrParams = {
    iIdOrganizacion: $('#header-a-id_organizacion').val(),
  };
  $.post(url, arrParams, function (response) {
    $('#cbo-filtro_almacen').html('<option value="0" selected="selected">- Todos -</option>');
    for (var i = 0; i < response.length; i++)
      $('#cbo-filtro_almacen').append('<option value="' + response[i].ID_Almacen + '">' + response[i].No_Almacen + '</option>');
  }, 'JSON');
  
  $( '.btn-generar_ingresos_egresos_caja_pos' ).click(function(){
    var ID_Almacen, Fe_Inicio, Fe_Fin, ID_Tipo_Operacion_Caja, iIdEmpleado, sNombreEmpleado;

    ID_Almacen = $('#cbo-filtro_almacen').val();
    Fe_Inicio = ParseDateString($( '#txt-Filtro_Fe_Inicio' ).val(), 1, '/');
    Fe_Fin = ParseDateString($( '#txt-Filtro_Fe_Fin' ).val(), 1, '/');
    ID_Tipo_Operacion_Caja = $( '#cbo-tipo_operacion_caja_ingresos_egresos_caja_pos' ).val();
    iIdEmpleado = ($( '#txt-AID' ).val().length === 0 ? '-' : $( '#txt-AID' ).val());
    sNombreEmpleado = ($( '#txt-Filtro_Entidad' ).val().length === 0 ? '-' : $( '#txt-Filtro_Entidad' ).val());
      
    if ($(this).data('type') == 'html') {
      $( '#btn-html_ingresos_egresos_caja_pos' ).text('');
      $( '#btn-html_ingresos_egresos_caja_pos' ).attr('disabled', true);
      $( '#btn-html_ingresos_egresos_caja_pos' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
  
      $( '#table-ingresos_egresos_caja_pos > tbody' ).empty();
      $( '#table-ingresos_egresos_caja_pos > tfoot' ).empty();

      var arrPost = {
        ID_Almacen: ID_Almacen,
        Fe_Inicio : Fe_Inicio,
        Fe_Fin : Fe_Fin,
        ID_Tipo_Operacion_Caja : ID_Tipo_Operacion_Caja,
        iIdEmpleado : iIdEmpleado,
        sNombreEmpleado : sNombreEmpleado,
      };

      url = base_url + 'PuntoVenta/informes_caja/InformeMovimientoCajaController/sendReporte';
      $.post( url, arrPost, function( response ){
        if ( response.sStatus == 'success' ) {
          var tr_body = '', tr_foot = '', iCounter = 0, iTotalRegistros = response.arrData.length, response=response.arrData;
          var ID_Empleado = '', fTotal = 0.00, fTotalIngresos = 0.00, fTotalEgresos = 0.00, fSumGeneralTotalIngresos = 0.00, fSumGeneralTotalEgresos = 0.00;
          for (var i = 0; i < iTotalRegistros; i++) {
            if (ID_Empleado != response[i].ID_Empleado) {
              if (iCounter != 0) {
                tr_body +=
                +"<tr>"
                  +"<th class='text-right' colspan='4'>Total Ingresos</th>"
                  +"<th class='text-right'>" + number_format(fTotalIngresos, 2) + "</th>"
                +"</tr>"
                +"<tr>"
                  +"<th class='text-right' colspan='4'>Total Egresos 1</th>"
                  +"<th class='text-right'>" + number_format(fTotalEgresos, 2) + "</th>"
                +"</tr>";
                fTotalIngresos = 0.00;
                fTotalEgresos = 0.00;
              }
              
              tr_body +=
              "<tr>"
                +"<th class='text-center'>Personal </th>"
                +"<th class='text-left' colspan='6'>" + response[i].No_Empleado + "</th>"
              +"</tr>";
              ID_Empleado = response[i].ID_Empleado;
            }
            
            fTotal = (!isNaN(parseFloat(response[i].Ss_Total)) ? parseFloat(response[i].Ss_Total) : 0);
            if (response[i].Nu_Tipo == '5'){//Ingresos
              fTotalIngresos += fTotal;
              fSumGeneralTotalIngresos += fTotal;
            } else {
              fTotalEgresos += fTotal;
              fSumGeneralTotalEgresos += fTotal;
            }
            tr_body +=
            "<tr>"
              +"<td class='text-center'>" + response[i].No_Almacen + "</td>"
              +"<td class='text-center'><span class='label label-" + response[i].No_Class_Estado + "'>" + response[i].No_Tipo_Operacion_Caja + "</td>"
              +"<td class='text-center'>" + response[i].Fe_Movimiento + "</td>"
              +"<td class='text-center'>" + response[i].No_Signo + "</td>"
              +"<td class='text-right'>" + number_format(fTotal, 2) + "</td>"
              +"<td class='text-left'>" + response[i].Txt_Nota + "</td>"
              +"<td class='text-center'>" + response[i].sImpresion + "</td>"
            +"</tr>";
            
            iCounter++;
          }
          
          tr_foot =
          "<tfoot>"
            +"<tr>"
              +"<th class='text-right' colspan='4'>Total Ingresos</th>"
              +"<th class='text-right'>" + number_format(fTotalIngresos, 2) + "</th>"
            +"</tr>"
            +"<tr>"
              +"<th class='text-right' colspan='4'>Total Egresos</th>"
              +"<th class='text-right'>" + number_format(fTotalEgresos, 2) + "</th>"
            +"</tr>"
            +"<tr>"
              +"<th class='text-right' colspan='4'>Total General Ingresos</th>"
              +"<th class='text-right'>" + number_format(fSumGeneralTotalIngresos, 2) + "</th>"
            +"</tr>"
            +"<tr>"
              +"<th class='text-right' colspan='4'>Total General Egresos</th>"
              +"<th class='text-right'>" + number_format(fSumGeneralTotalEgresos, 2) + "</th>"
            +"</tr>"
          +"</tfoot>";
        } else {
          if( response.sMessageSQL !== undefined ) {
            console.log(response.sMessageSQL);
          }
          tr_body +=
          "<tr>"
            +"<td colspan='10' class='text-center'>" + response.sMessage + "</td>"
          + "</tr>";
        } // ./ if arrData
        
        $( '#div-ingresos_egresos_caja_pos' ).show();
        $( '#table-ingresos_egresos_caja_pos > tbody' ).append(tr_body);
        $( '#table-ingresos_egresos_caja_pos > tbody' ).after(tr_foot);
        
        $( '#btn-html_ingresos_egresos_caja_pos' ).text('');
        $( '#btn-html_ingresos_egresos_caja_pos' ).append( '<i class="fa fa-search"></i> Buscar' );
        $( '#btn-html_ingresos_egresos_caja_pos' ).attr('disabled', false);
      }, 'JSON')
      .fail(function(jqXHR, textStatus, errorThrown) {
        $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
        
        $( '#modal-message' ).modal('show');
        $( '.modal-message' ).addClass( 'modal-danger' );
        $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
        setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
        
        //Message for developer
        console.log(jqXHR.responseText);
        
        $( '#btn-html_ingresos_egresos_caja_pos' ).text('');
        $( '#btn-html_ingresos_egresos_caja_pos' ).append( '<i class="fa fa-search color_white"></i> Buscar' );
        $( '#btn-html_ingresos_egresos_caja_pos' ).attr('disabled', false);
      });
    } else if ($(this).data('type') == 'pdf') {
      $( '#btn-pdf_ingresos_egresos_caja_pos' ).text('');
      $( '#btn-pdf_ingresos_egresos_caja_pos' ).attr('disabled', true);
      $( '#btn-pdf_ingresos_egresos_caja_pos' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
          
      url = base_url + 'PuntoVenta/informes_caja/InformeMovimientoCajaController/sendReportePDF/' + ID_Almacen + '/' + Fe_Inicio + '/' + Fe_Fin + '/' + ID_Tipo_Operacion_Caja + '/' + iIdEmpleado + '/' + sNombreEmpleado;
      window.open(url,'_blank');
      
      $( '#btn-pdf_ingresos_egresos_caja_pos' ).text('');
      $( '#btn-pdf_ingresos_egresos_caja_pos' ).append( '<i class="fa fa-file-pdf-o color_white"></i> PDF' );
      $( '#btn-pdf_ingresos_egresos_caja_pos' ).attr('disabled', false);
    } else if ($(this).data('type') == 'excel') {
      $( '#btn-excel_ingresos_egresos_caja_pos' ).text('');
      $( '#btn-excel_ingresos_egresos_caja_pos' ).attr('disabled', true);
      $( '#btn-excel_ingresos_egresos_caja_pos' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
      
      url = base_url + 'PuntoVenta/informes_caja/InformeMovimientoCajaController/sendReporteEXCEL/' + ID_Almacen + '/' + Fe_Inicio + '/' + Fe_Fin + '/' + ID_Tipo_Operacion_Caja + '/' + iIdEmpleado + '/' + sNombreEmpleado;
      window.open(url,'_blank');
      
      $( '#btn-excel_ingresos_egresos_caja_pos' ).text('');
      $( '#btn-excel_ingresos_egresos_caja_pos' ).append( '<i class="fa fa-file-excel-o color_white"></i> Excel' );
      $( '#btn-excel_ingresos_egresos_caja_pos' ).attr('disabled', false);
    }// ./ if
  })// /. btn
})

function imprimirMovimientoCaja(ID) {
  window.open(base_url + "PuntoVenta/MovimientoCajaController/imprimirMovimientoCaja/" + ID, "_blank", "location=yes,top=80,left=800,width=720,height=550,scrollbars=yes,status=yes");
}