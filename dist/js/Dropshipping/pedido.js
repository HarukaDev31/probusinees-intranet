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
      getReporteHTML();
    } else if ($(this).data('type') == 'pdf') {
      $( '#btn-pdf_ventas_detalladas_generales' ).text('');
      $( '#btn-pdf_ventas_detalladas_generales' ).attr('disabled', true);
      $( '#btn-pdf_ventas_detalladas_generales' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
          
      url = base_url + 'Dropshipping/PedidosDropshippingController/sendReportePDF/' + Fe_Inicio + '/' + Fe_Fin + '/' + ID_Tipo_Documento + '/' + ID_Pedido_Cabecera + '/' + Nu_Estado_Pedido + '/' + iIdCliente + '/' + sNombreCliente;
      window.open(url,'_blank');
      
      $( '#btn-pdf_ventas_detalladas_generales' ).text('');
      $( '#btn-pdf_ventas_detalladas_generales' ).append( '<i class="fa fa-file-pdf-o color_icon_pdf"></i> PDF' );
      $( '#btn-pdf_ventas_detalladas_generales' ).attr('disabled', false);
    } else if ($(this).data('type') == 'excel') {
      $( '#btn-excel_ventas_detalladas_generales' ).text('');
      $( '#btn-excel_ventas_detalladas_generales' ).attr('disabled', true);
      $( '#btn-excel_ventas_detalladas_generales' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
      
      url = base_url + 'Dropshipping/PedidosDropshippingController/sendReporteEXCEL/' + Fe_Inicio + '/' + Fe_Fin + '/' + ID_Tipo_Documento + '/' + ID_Pedido_Cabecera + '/' + Nu_Estado_Pedido + '/' + iIdCliente + '/' + sNombreCliente;
      window.open(url,'_blank');
      
      $( '#btn-excel_ventas_detalladas_generales' ).text('');
      $( '#btn-excel_ventas_detalladas_generales' ).append( '<i class="fa fa-file-excel-o color_icon_excel"></i> Excel' );
      $( '#btn-excel_ventas_detalladas_generales' ).attr('disabled', false);
    }// /. if
  })// /. btn


  // GENERAR VENTA
  $('#btn-modal-generar_venta-send').click(function () {
    if (parseFloat($('[name="fTotalDocumento"]').val()) < 0.10) {
      alert('El total no puede ser menor a 0.10 céntimos');
      return false;
    } if ($('[name="ID_Tipo_Documento"]').val() == 3 && $('[name="ID_Tipo_Documento_Identidad"]').val() == 2) {
      alert('No se puede FACTURAR a DNI');
    } else if ($('[name="ID_Tipo_Documento"]').val() == 3 && $('[name="ID_Tipo_Documento_Identidad"]').val() == 4 && $('[name="Nu_Documento_Identidad"]').val().length < 11) {
      alert('Ingresar 11 dígitos para RUC');
    } else {
      $('.help-block').empty();

      $('#btn-modal-generar_venta-send').text('');
      $('#btn-modal-generar_venta-send').attr('disabled', true);
      $('#btn-modal-generar_venta-send').append('Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>');
      $('#btn-modal-generar_venta-cancel').attr('disabled', true);

      url = base_url + 'Dropshipping/PedidosDropshippingController/generarVenta';
      $.ajax({
        type: 'POST',
        dataType: 'JSON',
        url: url,
        data: $('#form-generar_venta').serialize(),
        success: function (response) {
          $('.modal-message').removeClass('modal-danger modal-warning modal-success');
          $('#modal-message').modal('show');

          if (response.sStatus == 'success') {
            $('.modal-generar_venta').modal('hide');

            $('.modal-message').addClass('modal-' + response.sStatus);
            $('.modal-title-message').text(response.sMessage);
            setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
          } else {
            $('.modal-message').addClass('modal-' + response.sStatus);
            $('.modal-title-message').text(response.sMessage);
            setTimeout(function () { $('#modal-message').modal('hide'); }, 5100);
          }

          getReporteHTML();

          $('#btn-modal-generar_venta-send').text('');
          $('#btn-modal-generar_venta-send').append('Generar Venta');
          $('#btn-modal-generar_venta-send').attr('disabled', false);
          $('#btn-modal-generar_venta-cancel').attr('disabled', false);
        }
      })
        .fail(function (jqXHR, textStatus, errorThrown) {
          $('.modal-message').removeClass('modal-danger modal-warning modal-success');

          $('#modal-message').modal('show');
          $('.modal-message').addClass('modal-danger');
          $('.modal-title-message').text(textStatus + ' [' + jqXHR.status + ']: ' + errorThrown);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 3100);

          //Message for developer
          console.log(jqXHR.responseText);

          $('#btn-modal-generar_venta-send').text('');
          $('#btn-modal-generar_venta-send').attr('disabled', false);
          $('#btn-modal-generar_venta-send').append('Generar Venta');
          $('#btn-modal-generar_venta-cancel').attr('disabled', false);
        })
    }
  })
})

function getReporteHTML() {
  var Fe_Inicio, Fe_Fin, ID_Tipo_Documento, ID_Pedido_Cabecera, Nu_Estado_Pedido, iIdCliente, sNombreCliente, iIdItem, sNombreItem, iTipoVenta, Nu_Tipo_Recepcion;

  Fe_Inicio = ParseDateString($('#txt-Filtro_Fe_Inicio').val(), 1, '/');
  Fe_Fin = ParseDateString($('#txt-Filtro_Fe_Fin').val(), 1, '/');
  ID_Tipo_Documento = $('#cbo-filtros_tipos_documento').val();
  ID_Pedido_Cabecera = ($('#txt-Filtro_NumeroDocumento').val().length == 0 ? '-' : $('#txt-Filtro_NumeroDocumento').val());
  Nu_Estado_Pedido = $('#cbo-estado_pedido').val();
  iIdCliente = ($('#txt-AID').val().length === 0 ? '-' : $('#txt-AID').val());
  sNombreCliente = ($('#txt-Filtro_Entidad').val().length === 0 ? '-' : $('#txt-Filtro_Entidad').val());
  Nu_Tipo_Recepcion = $('#cbo-estado_recepcion').val();

  $('#btn-html_ventas_detalladas_generales').text('');
  $('#btn-html_ventas_detalladas_generales').attr('disabled', true);
  $('#btn-html_ventas_detalladas_generales').append('Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>');

  $('#table-ventas_detalladas_generales > tbody').empty();
  $('#table-ventas_detalladas_generales > tfoot').empty();

  var arrPost = {
    Fe_Inicio: Fe_Inicio,
    Fe_Fin: Fe_Fin,
    ID_Tipo_Documento: ID_Tipo_Documento,
    ID_Pedido_Cabecera: ID_Pedido_Cabecera,
    Nu_Estado_Pedido: Nu_Estado_Pedido,
    iIdCliente: iIdCliente,
    sNombreCliente: sNombreCliente,
    Nu_Tipo_Recepcion: Nu_Tipo_Recepcion,
  };
  url = base_url + 'Dropshipping/PedidosDropshippingController/sendReporte';
  $.post(url, arrPost, function (response) {
    if (response.sStatus == 'success') {
      var iTotalRegistros = response.arrData.length, response = response.arrData, tr_body = '', tr_foot = '', fTotalItem = 0.00, fTotalGeneral = 0.00
      for (var i = 0; i < iTotalRegistros; i++) {
        fTotal = (!isNaN(parseFloat(response[i].Ss_Total)) ? parseFloat(response[i].Ss_Total) : 0);

        tr_body +=
        "<tr>"
          +"<td class='text-center'>" + response[i].Fe_Emision_Hora + "</td>"
          +"<td class='text-center'>" + response[i].ID_Pedido_Cabecera + "</td>"
          +"<td class='text-left'>" + response[i].No_Entidad + "</td>"
          +"<td class='text-right'>" + number_format(fTotal, 2) + "</td>"
          +"<td class='text-center'><span class='label label-" + response[i].No_Class_Estado_Recepcion + "'>" + response[i].No_Estado_Recepcion + "</span></td>"
          +"<td class='text-center'>" + response[i].No_Estado_Pedido + "</td>"
          +"<td class='text-center'>" + response[i].sAccionVer + "</td>"
          +"<td class='text-center'>" + response[i].sAccionFacturar + "</td>"
          +"<td class='text-center'>" + response[i].sAccionEliminar + "</td>"
        +"</tr>";

        fTotalGeneral += fTotal;
      }

      tr_foot =
        "<tfoot>"
        + "<tr>"
        + "<th class='text-right' colspan='3'>Total</th>"
        + "<th class='text-right'>" + number_format(fTotalGeneral, 2) + "</th>"
        + "<th class='text-right'></th>"
        + "</tr>"
        + "</tfoot>";
    } else {
      if (response.sMessageSQL !== undefined) {
        console.log(response.sMessageSQL);
      }
      tr_body +=
        "<tr>"
        + "<td colspan='9' class='text-center'>" + response.sMessage + "</td>"
        + "</tr>";
    } // ./ if arrData

    $('#div-ventas_detalladas_generales').show();
    $('#table-ventas_detalladas_generales > tbody').append(tr_body);
    $('#table-ventas_detalladas_generales > tbody').after(tr_foot);

    $('#btn-html_ventas_detalladas_generales').text('');
    $('#btn-html_ventas_detalladas_generales').append('<i class="fa fa-search"></i> Buscar');
    $('#btn-html_ventas_detalladas_generales').attr('disabled', false);
  }, 'JSON')
  .fail(function (jqXHR, textStatus, errorThrown) {
    $('.modal-message').removeClass('modal-danger modal-warning modal-success');

    $('#modal-message').modal('show');
    $('.modal-message').addClass('modal-danger');
    $('.modal-title-message').text(textStatus + ' [' + jqXHR.status + ']: ' + errorThrown);
    setTimeout(function () { $('#modal-message').modal('hide'); }, 1700);

    //Message for developer
    console.log(jqXHR.responseText);

    $('#btn-html_ventas_detalladas_generales').text('');
    $('#btn-html_ventas_detalladas_generales').append('<i class="fa fa-search"></i> Buscar');
    $('#btn-html_ventas_detalladas_generales').attr('disabled', false);
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
  url = base_url + 'Dropshipping/PedidosDropshippingController/verPedido/' + iIdPedido;
  $.getJSON( url, function( response ) {
    $( '#table-modal_detalle thead' ).empty();
	  $( '#table-modal_detalle tbody' ).empty();
	  $( '#table-modal_detalle tfoot' ).empty();
    if ( response.sStatus =='success' ) {
      response = response.arrData;

      var table = "", sTipoDocumentoIdentidadCliente="", sClassEstadoRecepcion="";

      sClassEstadoRecepcion = (response[0].Nu_Tipo_Recepcion == 6 ? 'danger' : 'success');

      sTipoDocumentoIdentidadCliente = 'OTROS';
      if (response[0].Nu_Documento_Identidad.length == 8)
      sTipoDocumentoIdentidadCliente = 'DNI';
      else if (response[0].Nu_Documento_Identidad.length == 12)
        sTipoDocumentoIdentidadCliente = 'CARNET EXTRANJERIA';

      if (response[0].ID_Tipo_Documento==3 && response[0].Nu_Documento_Identidad.length == 11)
        sTipoDocumentoIdentidadCliente = 'RUC';
      
      table +=
      "<thead>"
        +"<tr>"
          +"<th class='text-left'>FECHA</th>"
          +"<th class='text-left'>DOCUMENTO</th>"
          +"<th class='text-left'>PEDIDO</th>"
          +"<th class='text-left'>F. PAGO</th>"
          +"<th class='text-left'>RECEPCIÓN</th>"
        +"</tr>"
        +"<tr>"
          +"<td class='text-left'>" + ParseDateHour(response[0].Fe_Emision_Hora) + "</td>"
          +"<td class='text-left'>" + response[0].No_Tipo_Documento_Breve + "</td>"
          +"<td class='text-left'>" + response[0].ID_Pedido_Cabecera + "</td>"
          +"<td class='text-left'>" + response[0].No_Medio_Pago_Tienda_Virtual + "</td>"
          +"<td class='text-left'><span class='label label-" + sClassEstadoRecepcion + "'>" + response[0].No_Estado_Recepcion + "</span></td>"
        +"</tr>"
        +"<tr>"
          +"<th class='text-left'>CLIENTE</th>"
          +"<th class='text-left'>Tipo Doc. Iden.</th>"
          +"<th class='text-left' colspan='2'>NÚMERO DOC IDEN.</th>"
          +"<th class='text-center'>CELULAR</th>"
        +"</tr>"
        +"<tr>"
          +"<td class='text-left'>" + response[0].No_Entidad + "</td>"
          +"<td class='text-left'>" + sTipoDocumentoIdentidadCliente + "</td>"
          +"<td class='text-left' colspan='2'>" + response[0].Nu_Documento_Identidad + "</td>"
          +"<td class='text-left' style='font-weight: normal; background: #FFFFFF;'>" + (response[0].Nu_Celular_Referencia != '' ? response[0].Nu_Celular_Referencia : response[0].Nu_Celular_Entidad) + "</td>"
        +"</tr>"
        +"<tr>"
          +"<th class='text-left'>CORREO</th>"
          +"<th class='text-left' colspan='4' style='font-weight: normal; background: #FFFFFF;'>" + response[0].Txt_Email_Entidad + "</th>"
        +"</tr>";
      if (response[0].Nu_Tipo_Recepcion == 6) {//Delivery
        table +=
        +"<tr>"
          +"<th class='text-left'>DEPARTAMENTO - PROVINCIA - DISTRITO</th>"
          +"<th class='text-left' colspan='4' style='font-weight: normal; background: #FFFFFF;'>" + response[0].No_Departamento + ' - ' + response[0].No_Provincia + ' - ' + response[0].No_Distrito + "</th>"
        +"</tr>"
        +"<tr>"
          +"<th class='text-left'>DIRECCIÓN</th>"
          +"<th class='text-left' colspan='4' style='font-weight: normal; background: #FFFFFF;'>" + response[0].Txt_Direccion + "</th>"
        +"</tr>"
        +"<tr>"
          +"<th class='text-left'>REFERENCIA DIRECCIÓN</th>"
          +"<th class='text-left' colspan='4' style='font-weight: normal; background: #FFFFFF;'>" + response[0].Txt_Direccion_Referencia + "</th>"
        +"</tr>";
      }
      table +=
        +"<tr>"
          +"<th class='text-left' colspan='2'>PRODUCTO</th>"
          +"<th class='text-center'>CANTIDAD</th>"
          +"<th class='text-center'>PRECIO</th>"
          +"<th class='text-center'>TOTAL</th>"
        +"</tr>"
      +"</thead>";
      
      table += "<tbody>";
        for (var i = 0, len = response.length; i < len; i++) {
          table +=
          +"<tr>"
            +"<td class='text-left' colspan='2'>" + response[i].No_Producto + "</td>"
            +"<td class='text-right'>" + number_format(response[i].Qt_Producto, 3) + "</td>"
            +"<td class='text-right'>" + number_format(response[i].Ss_Precio, 2) + "</td>"
            +"<td class='text-right'>" + number_format(response[i].Ss_SubTotal, 2) + "</td>"
          +"</tr>";
        }
      table += "</tbody>";
      
      table +=
      "<tfoot>";

      if (response[0].Nu_Tipo_Recepcion == 6) {//Delivery
        table +=
        "<tr>"
          +"<th colspan='4' class='text-right'>DELIVERY</th>"
          +"<th class='text-right'>" + number_format(response[0].Ss_Precio_Delivery, 2) + "</th>"
        +"</tr>";
      }

      if (parseFloat(response[0].Ss_Descuento) > 0.00) {//Cupon
        table +=
        "<tr>"
          +"<th colspan='3' class='text-right'>CUPON DE DESCUENTO:</th>"
          +"<th class='text-left'>" + response[0].No_Codigo_Cupon_Descuento + "</th>"
          +"<th class='text-right'>" + number_format(response[0].Ss_Descuento, 2) + "</th>"
        +"</tr>";
      }

      table +=
        "<tr>"
          +"<th colspan='4' class='text-right'>TOTAL A PAGAR</th>"
          +"<th class='text-right'>" + number_format(response[0].Ss_Total, 2) + "</th>"
        + "</tr>";

      if (response[0].Txt_Glosa != null && response[0].Txt_Glosa != '') {
        table +=
        "<tr>"
          + "<th colspan='4' class='text-left' class='text-right'>GLOSA</th>"
        + "</tr>"
        +"<tr>"
        + "<td colspan='4' class='text-left' class='text-right'>" + response[0].Txt_Glosa + "</td>"
        + "</tr>";
      }

      table+="</tfoot>";
      
      $( '#table-modal_detalle ' ).append(table);
    } else {
      alert('Problemas');
    }
    $( '#modal-loader' ).modal('hide');
  }, 'JSON');
}

function cambiarEstado(ID, Nu_Estado) {
  var $modal_delete = $('#modal-message-delete');
  $modal_delete.modal('show');

  $('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
  $('.modal-message-delete').addClass('modal-success');

  $('.modal-title-message-delete').text('¿Deseas cambiar estado del pedido Nro. ' + ID + '?');

  $('#btn-cancel-delete').off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $('#btn-save-delete').off('click').click(function () {
    $('#modal-loader').modal('show');

    url = base_url + 'Dropshipping/PedidosDropshippingController/cambiarEstado/' + ID + '/' + Nu_Estado;
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
          getReporteHTML();
        } else {
          $('.modal-message').addClass(response.style_modal);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1500);
        }
      }
    });
  });
}

function eliminarPedido(ID) {
  var $modal_delete = $('#modal-message-delete');
  $modal_delete.modal('show');

  $('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
  $('.modal-message-delete').addClass('modal-success');

  $('.modal-title-message-delete').text('¿Deseas eliminar el pedido Nro. ' + ID + '?');

  $('#btn-cancel-delete').off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $('#btn-save-delete').off('click').click(function () {
    $('#modal-loader').modal('show');

    url = base_url + 'Dropshipping/PedidosDropshippingController/eliminarPedido/' + ID;
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
          getReporteHTML();
        } else {
          $('.modal-message').addClass(response.style_modal);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1500);
        }
      }
    });
  });
}

function generarVenta(ID_Pedido_Cabecera){
  $('#modal-generar_venta').modal('show');

  $('.modal-header-title-generar_venta').text('Nro. Pedido: ' + ID_Pedido_Cabecera);

	$('#cbo-tipo_documento_pedido').val("2");
	$('#cbo-tipo_documento_pedido').val("2").change();

  $("div.id_100 select").val("val2").change();
  $('#modal-loader').modal('show');
  url = base_url + 'Dropshipping/PedidosDropshippingController/verPedido/' + ID_Pedido_Cabecera;
  $.getJSON(url, function (response) {
    if (response.sStatus == 'success') {
      var arrData = response.arrData[0], sNombreCliente = '', ID_Tipo_Documento_Identidad = 2, sTipoDocumentoIdentidadCliente='';//2=DNI

      $('.modal-header-title-generar_venta').html(
        'Nro. Pedido: ' + ID_Pedido_Cabecera + ' <br> '
        + 'Almacen: ' + arrData.No_Almacen
      );

      sNombreCliente = arrData.No_Entidad;
      if (sNombreCliente==null)
        sNombreCliente = arrData.Nu_Documento_Identidad;
      
      sTipoDocumentoIdentidadCliente = 'OTROS';
      ID_Tipo_Documento_Identidad = 1;
      if (arrData.Nu_Documento_Identidad.length == 8){
        sTipoDocumentoIdentidadCliente = 'DNI';
        ID_Tipo_Documento_Identidad = 2;
      } else if (arrData.Nu_Documento_Identidad.length == 12) {
        sTipoDocumentoIdentidadCliente = 'CARNET EXTRANJERIA';
        ID_Tipo_Documento_Identidad = 3;//3 = CARNET EXTRANJERIA
      }

      if (arrData.ID_Tipo_Documento==3 && arrData.Nu_Documento_Identidad.length == 11) {
        sTipoDocumentoIdentidadCliente = 'RUC';
        ID_Tipo_Documento_Identidad = 4;//4 = ruc
      }

      $('#info-generar_venta').html(
        '<strong>Cliente:</strong> ' + sNombreCliente + '<br>'
        + '<strong>' + sTipoDocumentoIdentidadCliente + ':</strong> ' + arrData.Nu_Documento_Identidad + '<br>'
        + '<strong>Forma Pago:</strong> ' + arrData.No_Medio_Pago_Tienda_Virtual + '<br>'
        + '<strong>Total:</strong> ' + arrData.Ss_Total
      );

      $('[name="ID_Pedido_Cabecera"]').val(ID_Pedido_Cabecera);
      $('[name="ID_Almacen"]').val( arrData.ID_Almacen );//CAMBIAR POR ID_ALMACEN QUE SE GUARDA EN PEDIDO
      $('[name="fTotalDocumento"]').val( arrData.Ss_Total );
      
      if($('#hidden-Nu_Tipo_Proveedor_FE').val()==3){
        $('[name="ID_Tipo_Documento"]').val(2);
      } else
        $('[name="ID_Tipo_Documento"]').val(arrData.ID_Tipo_Documento);

      $('[name="ID_Tipo_Documento_Identidad"]').val(ID_Tipo_Documento_Identidad);
      $('[name="Nu_Documento_Identidad"]').val(arrData.Nu_Documento_Identidad);
      $('[name="Nu_Celular_Entidad_Order_Address_Entry"]').val(arrData.Nu_Celular_Entidad);
      $('[name="ID_Distrito_Delivery"]').val(arrData.ID_Distrito_Delivery);
      $('[name="Txt_Direccion_Entidad_Order_Address_Entry"]').val(arrData.Txt_Direccion);
      $('[name="Txt_Direccion_Referencia_Entidad_Order_Address_Entry"]').val(arrData.Txt_Direccion_Referencia);
      $('[name="Nu_Tipo_Recepcion"]').val(arrData.Nu_Tipo_Recepcion);
      $('[name="ID_Moneda"]').val(arrData.ID_Moneda);
      $('[name="ID_Medio_Pago"]').val(arrData.ID_Medio_Pago);
      $('[name="No_Entidad"]').val(arrData.No_Entidad);
      $('[name="ID_Entidad"]').val(arrData.ID_Entidad);
    } else {
      alert('Problemas');
    }
    $('#modal-loader').modal('hide');
  }, 'JSON');
}

//Correo
function sendCorreoFacturaVentaSUNAT(ID, iIdCliente) {
  var $modal_id = $('#modal-correo_sunat');
  $modal_id.modal('show');

  $('#modal-correo_sunat').removeClass('modal-danger modal-warning modal-success');
  $('#modal-correo_sunat').addClass('modal-success');

  $('.modal-header_message_correo_sunat').text('¿Enviar correo?');

  // get cliente
  $('#txt-email_correo_sunat').val('');
  url = base_url + 'HelperController/getDataGeneral';
  $.post(url, { sTipoData: 'get_entidad', iTipoEntidad: 0, iIDEntidad: iIdCliente }, function (response) {
    if (response.sStatus == 'success') {
      $('#txt-email_correo_sunat').val(response.arrData[0].Txt_Email_Entidad);
    } else {
      if (response.sMessageSQL !== undefined) {
        console.log(response.sMessageSQL);
      }
      console.log(response.sMessage);
    }
  }, 'JSON');
  // /. get cliente

  $('#modal-correo_sunat').on('shown.bs.modal', function () {
    $('#txt-email_correo_sunat').focus();
  })

  $('#btn-modal_message_correo_sunat-cancel').off('click').click(function () {
    $modal_id.modal('hide');
  });

  $("#txt-email_correo_sunat").blur(function () {
    caracteresCorreoValido($(this).val(), '#span-email');
  })

  $('#btn-modal_message_correo_sunat-send').off('click').click(function () {
    if (!caracteresCorreoValido($('#txt-email_correo_sunat').val(), '#div-email')) {
      scrollToError($('#modal-correo_sunat .modal-body'), $('#txt-email_correo_sunat'));
    } else {
      $('#btn-modal_message_correo_sunat-send').text('');
      $('#btn-modal_message_correo_sunat-send').attr('disabled', true);
      $('#btn-modal_message_correo_sunat-send').append('Enviando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>');

      var sendPost = {
        ID: ID,
        Txt_Email: $('#txt-email_correo_sunat').val(),
      };

      url = base_url + 'Ventas/VentaController/sendCorreoFacturaVentaSUNAT/';
      $.ajax({
        url: url,
        type: "POST",
        dataType: "JSON",
        data: sendPost,
        success: function (response) {
          $modal_id.modal('hide');

          $('.modal-message').removeClass('modal-danger modal-warning modal-success');
          $('#modal-message').modal('show');

          if (response.status == 'success') {
            $('#txt-email_correo_sunat').val('');

            $('.modal-message').addClass(response.style_modal);
            $('.modal-title-message').text(response.message);
            setTimeout(function () { $('#modal-message').modal('hide'); }, 1200);
          } else {
            $('.modal-message').addClass(response.style_modal);
            $('.modal-title-message').text(response.message);
            setTimeout(function () { $('#modal-message').modal('hide'); }, 1800);
          }

          $('#btn-modal_message_correo_sunat-send').text('');
          $('#btn-modal_message_correo_sunat-send').attr('disabled', false);
          $('#btn-modal_message_correo_sunat-send').append('Enviar');
        }
      });
    }
  });
}

//WhatsApp
function sendWhatsapp(ID, iIdCliente) {
  var $modal_id = $('#modal-whatsApp');
  $modal_id.modal('show');

  $('#modal-whatsApp').removeClass('modal-danger modal-warning modal-success');
  $('#modal-whatsApp').addClass('modal-success');

  $('.modal-header_message_whatsApp').text('¿Enviar WhatsApp?');

  // get cliente
  $('#txt-Nu_Celular_Entidad_Cliente_WhatsApp').val('');
  url = base_url + 'HelperController/getDataGeneral';
  $.post(url, { sTipoData: 'get_entidad', iTipoEntidad: 0, iIDEntidad: iIdCliente }, function (response) {
    if (response.sStatus == 'success') {
      $('#txt-Nu_Celular_Entidad_Cliente_WhatsApp').val(response.arrData[0].Nu_Celular_Entidad);
    } else {
      if (response.sMessageSQL !== undefined) {
        console.log(response.sMessageSQL);
      }
      console.log(response.sMessage);
    }
  }, 'JSON');
  // /. get cliente

  $('#modal-whatsApp').on('shown.bs.modal', function () {
    $('#txt-Nu_Celular_Entidad_Cliente_WhatsApp').focus();
  })

  $('#btn-modal_message_whatsApp-cancel').off('click').click(function () {
    $modal_id.modal('hide');
  });

  $('#btn-modal_message_whatsApp-send').off('click').click(function () {
    if (String(parseInt($('#txt-Nu_Celular_Entidad_Cliente_WhatsApp').val().replace(/ /g, ""))).length < 9) {
      $('#txt-Nu_Celular_Entidad_Cliente_WhatsApp').closest('.form-group').find('.help-block').html('Debes ingresar 9 dígitos');
      $('#txt-Nu_Celular_Entidad_Cliente_WhatsApp').closest('.form-group').removeClass('has-success').addClass('has-error');

      scrollToError($("html, body"), $('#txt-Nu_Celular_Entidad_Cliente_WhatsApp'));
      return false;
    } else {
      $('#btn-modal_message_whatsApp-send').text('');
      $('#btn-modal_message_whatsApp-send').attr('disabled', true);
      $('#btn-modal_message_whatsApp-send').append('Enviando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>');

      var sendPost = {
        ID: ID,
      };

      url = base_url + 'HelperController/getDatosDocumentoVentaWhatsApp/';
      $.ajax({
        url: url,
        type: "POST",
        dataType: "JSON",
        data: sendPost,
        success: function (response) {
          console.log(response);

          $modal_id.modal('hide');

          $('.modal-message').removeClass('modal-danger modal-warning modal-success');
          $('#modal-message').modal('show');

          if (response.sStatus == 'success') {
            //Envío por whatsApp
            var sNumeroPeru = $('#txt-Nu_Celular_Entidad_Cliente_WhatsApp').val().replace(/ /g, "");
            var sDocumento = response.arrData[0].No_Tipo_Documento + (response.arrData[0].ID_Tipo_Documento != '2' ? ' Electrónica' : '') + ' - ' + response.arrData[0].ID_Serie_Documento + ' - ' + response.arrData[0].ID_Numero_Documento;

            url = 'https://api.whatsapp.com/send?phone=51' + sNumeroPeru + '&text=';
            url += 'Somos *' + (response.arrData[0].No_Empresa_Comercial != '' ? caracteresValidosWhatsApp(response.arrData[0].No_Empresa_Comercial) : caracteresValidosWhatsApp(response.arrData[0].No_Empresa)) + '*,\n';

            url += '\n*Nombre:* ' + caracteresValidosWhatsApp(response.arrData[0].No_Entidad);
            url += '\n*' + response.arrData[0].No_Tipo_Documento_Identidad_Breve + ':* ' + response.arrData[0].Nu_Documento_Identidad;
            url += '\n*Documento:* ' + sDocumento;
            url += '\n*Fecha de Emisión:* ' + ParseDate(response.arrData[0].Fecha_Emision);

            var iTotalRegistros = response.arrData.length;
            var responseDetalle = response.arrData;

            url += '\n\n*Detalle de Pedido*\n';
            url += '=============\n';
            for (var i = 0; i < iTotalRegistros; i++) {
              url += '✅ ' + number_format(responseDetalle[i].Qt_Producto, 2) + ' x *' + caracteresValidosWhatsApp(responseDetalle[i].sNombreItem.trim()) + '* - ' + responseDetalle[i].No_Signo + ' ' + number_format(responseDetalle[i].Ss_Precio, 2) + '\n';
            }
            if (response.arrData[0].Nu_Tipo_Recepcion != '5') {
              url += '\n*Recepción:* ' + response.arrData[0].No_Tipo_Recepcion + '\n';
              if (response.arrData[0].Nu_Tipo_Recepcion == 6 && response.arrData[0].Txt_Direccion_Entidad != '')
                url += '*Dirección:* ' + response.arrData[0].Txt_Direccion_Entidad + '\n';
            }
            url += '\n➡️ *Total:* ' + response.arrData[0].No_Signo + ' ' + number_format(response.arrData[0].Total, 2) + '\n';
            //Saldo
            if (parseFloat(response.arrData[0].Total_Saldo) > 0.00) {
              url += '➡️ *Fecha de Vencimiento:* ' + ParseDate(response.arrData[0].Fecha_Vencimiento);
              url += '\n➡️ Tiene un *saldo pendiente por pagar de ' + response.arrData[0].No_Signo + ' ' + number_format(response.arrData[0].Total_Saldo, 2) + '*\n';
            }

            if (response.arrData[0].enlace_del_pdf !== undefined && response.arrData[0].enlace_del_pdf != null) {
              url += '\nDescarga tu *PDF electrónico* en el siguiente enlace:\n' + response.arrData[0].enlace_del_pdf;
            }

            url += (response.arrData[0].sTerminosCondicionesTicket != '' && response.arrData[0].sTerminosCondicionesTicket != null ? '\n\n' + response.arrData[0].sTerminosCondicionesTicket.replace(/<br \/>/g, "") : '');
            url += '\n\nGenerado por laesystems.com';
            
            url = encodeURI(url);
            window.open(url, '_blank');
            window.close();

            $('#modal-message').modal('hide');
          } else {
            $('.modal-message').addClass('modal-' + response.sStatus);
            $('.modal-title-message').text(response.sMessage);
            setTimeout(function () { $('#modal-message').modal('hide'); }, 1800);
          }

          $('#btn-modal_message_whatsApp-send').text('');
          $('#btn-modal_message_whatsApp-send').attr('disabled', false);
          $('#btn-modal_message_whatsApp-send').append('Enviar');
        }
      });
    }
  });
}