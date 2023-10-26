var url;

$(function () {
  $( '#div-ventas_detalladas_generales' ).hide();

  $( '.btn-generar_ventas_detalladas_generales' ).click(function(){    
    var Fe_Inicio, Fe_Fin, ID_Tipo_Documento, ID_Pedido_Cabecera, Nu_Estado_Pedido, iIdCliente, sNombreCliente, iIdItem, sNombreItem, iTipoVenta;
    
    Fe_Inicio = ParseDateString($( '#txt-Filtro_Fe_Inicio' ).val(), 1, '/');
    Fe_Fin = ParseDateString($( '#txt-Filtro_Fe_Fin' ).val(), 1, '/');
    ID_Tipo_Documento = $( '#cbo-filtros_tipos_documento' ).val();
    ID_Pedido_Cabecera = ($( '#txt-Filtro_NumeroDocumento' ).val().length == 0 ? '-' : $( '#txt-Filtro_NumeroDocumento' ).val());
    Nu_Estado_Pedido = $( '#cbo-estado_pedido' ).val();
    iIdCliente = ($( '#txt-AID' ).val().length === 0 ? '-' : $( '#txt-AID' ).val());
    sNombreCliente = ($( '#txt-Filtro_Entidad' ).val().length === 0 ? '-' : $( '#txt-Filtro_Entidad' ).val());

    if ($(this).data('type') == 'html') {
      var arrPost = {
        Fe_Inicio : Fe_Inicio,
        Fe_Fin : Fe_Fin,
        ID_Tipo_Documento : ID_Tipo_Documento,
        ID_Pedido_Cabecera : ID_Pedido_Cabecera,
        Nu_Estado_Pedido : Nu_Estado_Pedido,
        iIdCliente : iIdCliente,
        sNombreCliente : sNombreCliente,
      };
      getReporteHTML(arrPost);
    } else if ($(this).data('type') == 'pdf') {
      $( '#btn-pdf_ventas_detalladas_generales' ).text('');
      $( '#btn-pdf_ventas_detalladas_generales' ).attr('disabled', true);
      $( '#btn-pdf_ventas_detalladas_generales' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
          
      url = base_url + 'Ventas/PedidosController/sendReportePDF/' + Fe_Inicio + '/' + Fe_Fin + '/' + ID_Tipo_Documento + '/' + ID_Pedido_Cabecera + '/' + Nu_Estado_Pedido + '/' + iIdCliente + '/' + sNombreCliente;
      window.open(url,'_blank');
      
      $( '#btn-pdf_ventas_detalladas_generales' ).text('');
      $( '#btn-pdf_ventas_detalladas_generales' ).append( '<i class="fa fa-file-pdf-o color_icon_pdf"></i> PDF' );
      $( '#btn-pdf_ventas_detalladas_generales' ).attr('disabled', false);
    } else if ($(this).data('type') == 'excel') {
      $( '#btn-excel_ventas_detalladas_generales' ).text('');
      $( '#btn-excel_ventas_detalladas_generales' ).attr('disabled', true);
      $( '#btn-excel_ventas_detalladas_generales' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
      
      url = base_url + 'Ventas/PedidosController/sendReporteEXCEL/' + Fe_Inicio + '/' + Fe_Fin + '/' + ID_Tipo_Documento + '/' + ID_Pedido_Cabecera + '/' + Nu_Estado_Pedido + '/' + iIdCliente + '/' + sNombreCliente;
      window.open(url,'_blank');
      
      $( '#btn-excel_ventas_detalladas_generales' ).text('');
      $( '#btn-excel_ventas_detalladas_generales' ).append( '<i class="fa fa-file-excel-o color_icon_excel"></i> Excel' );
      $( '#btn-excel_ventas_detalladas_generales' ).attr('disabled', false);
    }// /. if
  })// /. btn

  // COMBOBOX - Cambiar estado de pedido
  $(document).on('change', '.cbo-estado_pedido', function() {
    var iIdPedido = $(this).find(':selected').data('id');
    var sEstadoActual = $(this).find(':selected').data('estado_actual');
    var sEstadoNuevo = $("#cbo-estado_pedido" + iIdPedido + " option:selected").text();
    var iEstadoNuevo = $(this).val();
    
    if ( sEstadoActual != sEstadoNuevo ) {
      var $modal_delete = $( '.modal-message-delete' );
      $modal_delete.modal('show');
      
      $( '.modal-message-delete' ).removeClass('modal-danger modal-warning modal-success');
      $( '.modal-message-delete' ).addClass('modal-success');
      
      $( '.modal-title-message-delete' ).text('¿Deseas cambiar el estado del pedido de (' + sEstadoActual + ') a (' + sEstadoNuevo + ')?');
      
      $( '#btn-cancel-delete' ).off('click').click(function () {
        $modal_delete.modal('hide');
      });
  
      $( '#btn-save-delete' ).off('click').click(function () {
        url = base_url + 'Ventas/PedidosController/updEstadoPedido/' + iIdPedido + '/' + iEstadoNuevo;
        $.getJSON(url, function( response ) {
          $( '#modal-loader' ).modal('hide');
          
          $modal_delete.modal('hide');
          $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
          $( '#modal-message' ).modal('show');
          
          if (response.sStatus == 'success'){
            $( '.modal-message' ).addClass( 'modal-' + response.sStatus );
            $( '.modal-title-message' ).text(response.sMessage);
            setTimeout(function() {$('#modal-message').modal('hide');}, 1100);

            Fe_Inicio = ParseDateString($( '#txt-Filtro_Fe_Inicio' ).val(), 1, '/');
            Fe_Fin = ParseDateString($( '#txt-Filtro_Fe_Fin' ).val(), 1, '/');
            ID_Tipo_Documento = $( '#cbo-filtros_tipos_documento' ).val();
            ID_Pedido_Cabecera = ($( '#txt-Filtro_NumeroDocumento' ).val().length == 0 ? '-' : $( '#txt-Filtro_NumeroDocumento' ).val());
            Nu_Estado_Pedido = $( '#cbo-estado_pedido' ).val();
            iIdCliente = ($( '#txt-AID' ).val().length === 0 ? '-' : $( '#txt-AID' ).val());
            sNombreCliente = ($( '#txt-Filtro_Entidad' ).val().length === 0 ? '-' : $( '#txt-Filtro_Entidad' ).val());
        
            var arrPost = {
              Fe_Inicio : Fe_Inicio,
              Fe_Fin : Fe_Fin,
              ID_Tipo_Documento : ID_Tipo_Documento,
              ID_Pedido_Cabecera : ID_Pedido_Cabecera,
              Nu_Estado_Pedido : Nu_Estado_Pedido,
              iIdCliente : iIdCliente,
              sNombreCliente : sNombreCliente,
            };

            getReporteHTML(arrPost);
          } else {
            $( '.modal-message' ).addClass( 'modal-' + response.sStatus );
            $( '.modal-title-message' ).text(response.sMessage);
            setTimeout(function() {$('#modal-message').modal('hide');}, 3100);
          }
        })
        .error(function() {
          $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
          
          $( '#modal-message' ).modal('show');
          $( '.modal-message' ).addClass( 'modal-danger' );
          $( '.modal-title-message' ).text( '¡Oops! Algo salió mal. Inténtalo mas tarde' );
          setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
        })
      });
    }
  });
})

function getReporteHTML(arrPost){
  $( '#btn-html_ventas_detalladas_generales' ).text('');
  $( '#btn-html_ventas_detalladas_generales' ).attr('disabled', true);
  $( '#btn-html_ventas_detalladas_generales' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

  $( '#table-ventas_detalladas_generales > tbody' ).empty();
  $( '#table-ventas_detalladas_generales > tfoot' ).empty();

  url = base_url + 'Ventas/PedidosController/sendReporte';
  $.post( url, arrPost, function( response ){
    if ( response.sStatus == 'success' ) {
      var iTotalRegistros = response.arrData.length, response=response.arrData, tr_body = '', tr_foot = '', fTotalItem = 0.00, fTotalGeneral = 0.00
      for (var i = 0; i < iTotalRegistros; i++) {
        fTotal = (!isNaN(parseFloat(response[i].Ss_Total)) ? parseFloat(response[i].Ss_Total) : 0);

        tr_body +=
        "<tr>"
          +"<td class='text-center'>" + response[i].Fe_Emision_Hora + "</td>"
          +"<td class='text-center'>" + response[i].No_Tipo_Documento_Breve + "</td>"
          +"<td class='text-center'>" + response[i].ID_Pedido_Cabecera + "</td>"
          +"<td class='text-center'>" + response[i].No_Tipo_Documento_Identidad_Breve + "</td>"
          +"<td class='text-center'>" + response[i].Nu_Documento_Identidad + "</td>"
          +"<td class='text-left'>" + response[i].No_Entidad + "</td>"
          +"<td class='text-right'>" + (response[i].ID_Tipo_Documento != 5 ? '' : '-') + number_format(fTotal, 2) + "</td>"
          +"<td class='text-center'><span class='label label-" + response[i].No_Class_Estado_Recepcion + "'>" + response[i].No_Estado_Recepcion + "</span></td>"
          +"<td class='text-center'>" + response[i].sComboboxEstadoPedido + "</td>"
          //+"<td class='text-center'><span class='label label-" + response[i].No_Class_Estado + "'>" + response[i].No_Estado + "</span></td>"
          +"<td class='text-center'>" + response[i].sAccionVer + "</td>"
        +"</tr>";
        
        fTotalGeneral += fTotal;
      }
      
      tr_foot =
      "<tfoot>"
        +"<tr>"
          +"<th class='text-right' colspan='6'>Total</th>"
          +"<th class='text-right'>" + number_format(fTotalGeneral, 2) + "</th>"
          +"<th class='text-right'></th>"
        +"</tr>"
      +"</tfoot>";
    } else {
      if( response.sMessageSQL !== undefined ) {
        console.log(response.sMessageSQL);
      }
      tr_body +=
      "<tr>"
        +"<td colspan='9' class='text-center'>" + response.sMessage + "</td>"
      + "</tr>";
    } // ./ if arrData
    
    $( '#div-ventas_detalladas_generales' ).show();
    $( '#table-ventas_detalladas_generales > tbody' ).append(tr_body);
    $( '#table-ventas_detalladas_generales > tbody' ).after(tr_foot);
    
    $( '#btn-html_ventas_detalladas_generales' ).text('');
    $( '#btn-html_ventas_detalladas_generales' ).append( '<i class="fa fa-search"></i> Buscar' );
    $( '#btn-html_ventas_detalladas_generales' ).attr('disabled', false);
  }, 'JSON')
  .fail(function(jqXHR, textStatus, errorThrown) {
    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
    
    $( '#modal-message' ).modal('show');
    $( '.modal-message' ).addClass( 'modal-danger' );
    $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
    setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
    
    //Message for developer
    console.log(jqXHR.responseText);
    
    $( '#btn-html_ventas_detalladas_generales' ).text('');
    $( '#btn-html_ventas_detalladas_generales' ).append( '<i class="fa fa-search"></i> Buscar' );
    $( '#btn-html_ventas_detalladas_generales' ).attr('disabled', false);
  });
}

function verPedido(iIdPedido){
  var $modal_detalle = $( '.modal_detalle' );
  $modal_detalle.modal('show');

  $( '#modal-title_detalle' ).text('Nro. Pedido ' + iIdPedido);
  
  $( '#btn-salir' ).off('click').click(function () {
    $modal_detalle.modal('hide');
  });
  
  $( '#modal-loader' ).modal('show');
  url = base_url + 'Ventas/PedidosController/verPedido/' + iIdPedido;
  $.getJSON( url, function( response ) {
    $( '#table-modal_detalle thead' ).empty();
	  $( '#table-modal_detalle tbody' ).empty();
	  $( '#table-modal_detalle tfoot' ).empty();
    if ( response.sStatus =='success' ) {
      response = response.arrData;

      var table = "";
      
      table +=
      "<thead>"
        +"<tr>"
          +"<th class='text-left'>FECHA</th>"
          +"<th class='text-left'>DOC</th>"
          +"<th class='text-left'>NRO. PEDIDO</th>"
          +"<th class='text-left'>RECEPCIÓN</th>"
        +"</tr>"
        +"<tr>"
          +"<td class='text-left'>" + ParseDateHour(response[0].Fe_Emision_Hora) + "</td>"
          +"<td class='text-left'>" + response[0].No_Tipo_Documento_Breve + "</td>"
          +"<td class='text-left'>" + response[0].ID_Pedido_Cabecera + "</td>"
          +"<td class='text-left'><span class='label label-" + response[0].No_Class_Estado_Recepcion + "'>" + response[0].No_Estado_Recepcion + "</span></td>"
        +"</tr>"
        +"<tr>"
          +"<th class='text-left'>TIPO</th>"
          +"<th class='text-left'>NRO.</th>"
          +"<th class='text-left' colspan='2'>CLIENTE</th>"
        +"</tr>"
        +"<tr>"
          +"<td class='text-left'>" + response[0].No_Tipo_Documento_Identidad_Breve + "</td>"
          +"<td class='text-left'>" + response[0].Nu_Documento_Identidad + "</td>"
          +"<td class='text-left' colspan='2'>" + response[0].No_Entidad + "</td>"
        +"</tr>"
        +"<tr>"
          +"<th class='text-left'>CELULAR</th>"
          +"<th class='text-left' colspan='3' style='font-weight: normal;'>" + (response[0].Nu_Celular_Referencia != '' ? response[0].Nu_Celular_Referencia : response[0].Nu_Celular_Entidad) + "</th>"
        +"</tr>"
        +"<tr>"
          +"<th class='text-left'>CORREO</th>"
          +"<th class='text-left' colspan='3' style='font-weight: normal;'>" + response[0].Txt_Email_Entidad + "</th>"
        +"</tr>";
      if (response[0].Nu_Tipo_Recepcion == 2) {
      table +=
        +"<tr>"
          +"<th class='text-left'>DISTRITO</th>"
          +"<th class='text-left' colspan='3' style='font-weight: normal;'>" + response[0].No_Distrito + "</th>"
        +"</tr>"
        +"<tr>"
          +"<th class='text-left'>DIRECCIÓN ENVIÓ</th>"
          +"<th class='text-left' colspan='3' style='font-weight: normal;'>" + response[0].Txt_Direccion + "</th>"
        +"</tr>"
        +"<tr>"
          +"<th class='text-left'>REF. DIRECCIÓN ENVIÓ</th>"
          +"<th class='text-left' colspan='3' style='font-weight: normal;'>" + response[0].Txt_Direccion_Referencia + "</th>"
        +"</tr>";
      }
      table +=
        +"<tr>"
          +"<th class='text-center'>PRODUCTO</th>"
          +"<th class='text-center'>CANTIDAD</th>"
          +"<th class='text-center'>PRECIO</th>"
          +"<th class='text-center'>TOTAL</th>"
        +"</tr>"
      +"</thead>";
      
      table += "<tbody>";
        for (var i = 0, len = response.length; i < len; i++) {
          table +=
          +"<tr>"
            +"<td class='text-left'>" + response[i].No_Producto + "</td>"
            +"<td class='text-right'>" + number_format(response[i].Qt_Producto, 3) + "</td>"
            +"<td class='text-right'>" + number_format(response[i].Ss_Precio, 2) + "</td>"
            +"<td class='text-right'>" + number_format(response[i].Ss_SubTotal, 2) + "</td>"
          +"</tr>";
        }
      table += "</tbody>";
      
      table +=
      "<tfoot>"
        +"<tr>"
          +"<th colspan='3' class='text-right'>TOTAL A PAGAR</th>"
          +"<th class='text-right'>" + number_format(response[0].Ss_Total, 2) + "</th>"
        +"</tr>"
      +"</tfoot>";
      
      $( '#table-modal_detalle ' ).append(table);
    } else {
      alert('Problemas');
    }
    $( '#modal-loader' ).modal('hide');
  }, 'JSON');
}
