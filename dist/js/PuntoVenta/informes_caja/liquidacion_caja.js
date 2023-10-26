var url;

$(function () {  
  $('.select2').select2();

  $( '#div-liquidacion_caja' ).hide();

  $('#cbo-filtro_organizacion').html('');
  url = base_url + 'HelperController/getOrganizacionxUsuarioSessionEmpresa';
  $.post(url, function (response) {
    console.log(response);
    if ( response.sStatus=='success' ){
      if (response.arrData.length == 1){
        $('#cbo-filtro_organizacion').html('<option value="' + response.arrData[0].ID_Organizacion + '">' + response.arrData[0].No_Organizacion + '</option>');

        //get Almacenes
        url = base_url + 'HelperController/getAlmacenes';
        var arrParams = {
          iIdOrganizacion: response.arrData[0].ID_Organizacion,
        };
        $.post(url, arrParams, function (response) {
          $('#cbo-filtro_almacen').html('<option value="0" selected="selected">- Todos -</option>');
          for (var i = 0; i < response.length; i++)
            $('#cbo-filtro_almacen').append('<option value="' + response[i].ID_Almacen + '">' + response[i].No_Almacen + '</option>');
        }, 'JSON');
      } else {
        $('#cbo-filtro_organizacion').html('<option value="0" selected="selected">- Todos -</option>');
        for (var i = 0; i < response.arrData.length; i++)
          $('#cbo-filtro_organizacion').append('<option value="' + response.arrData[i].ID_Organizacion + '">' + response.arrData[i].No_Organizacion + '</option>');
      }
    } else {
      alert( response.sMessage );
    }
  }, 'JSON');

  $('#cbo-filtro_almacen').html('<option value="0" selected="selected">- Todos -</option>');
  $('#cbo-filtro_organizacion').change(function () {
    url = base_url + 'HelperController/getAlmacenes';
    var arrParams = {
      iIdOrganizacion: $('#cbo-filtro_organizacion').val(),
    };
    $.post(url, arrParams, function (response) {
      $('#cbo-filtro_almacen').html('<option value="0" selected="selected">- Todos -</option>');
      for (var i = 0; i < response.length; i++)
        $('#cbo-filtro_almacen').append('<option value="' + response[i].ID_Almacen + '">' + response[i].No_Almacen + '</option>');
    }, 'JSON');
  })

  $( '.btn-generar_liquidacion_caja' ).click(function(){
    $( '.help-block' ).empty();
  
    var ID_Organizacion, ID_Almacen, Fe_Inicio, Fe_Fin, iIdEmpleado, sNombreEmpleado, tr_body = '';
    
    ID_Almacen = $('#cbo-filtro_almacen').val();
    Fe_Inicio = ParseDateString($( '#txt-Filtro_Fe_Inicio' ).val(), 1, '/');
    Fe_Fin = ParseDateString($('#txt-Filtro_Fe_Fin').val(), 1, '/');
    iIdEmpleado = ($('#txt-AID').val().length === 0 ? '-' : $('#txt-AID').val());
    sNombreEmpleado = ($('#txt-Filtro_Entidad').val().length === 0 ? '-' : $('#txt-Filtro_Entidad').val());
    ID_Organizacion = $('#cbo-filtro_organizacion').val();

    var arrPost = {
      ID_Almacen: ID_Almacen,
      Fe_Inicio : Fe_Inicio,
      Fe_Fin : Fe_Fin,
      iIdEmpleado: iIdEmpleado,
      sNombreEmpleado: sNombreEmpleado,
      ID_Organizacion: ID_Organizacion,
    };
      
    if ($(this).data('type') == 'html') {
      $( '#btn-html_liquidacion_caja' ).text('');
      $( '#btn-html_liquidacion_caja' ).attr('disabled', true);
      $( '#btn-html_liquidacion_caja' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
  
      $( '#table-liquidacion_caja > tbody' ).empty();
      $( '#table-liquidacion_caja > tfoot' ).empty();
      
      url = base_url + 'PuntoVenta/informes_caja/LiquidacionCajaController/sendReporte';
      $.post( url, arrPost, function( response ){
        if ( response.sStatus == 'success' ) {
          var iTotalRegistros = response.arrData.length, fLiquidar = 0.00, fDepositar = 0.00, response=response.arrData;
          for (var i = 0; i < iTotalRegistros; i++) {
            fLiquidar = (!isNaN(parseFloat(response[i].Ss_Expectativa)) ? parseFloat(response[i].Ss_Expectativa) : 0);
            fDepositar = (!isNaN(parseFloat(response[i].Ss_Total)) ? parseFloat(response[i].Ss_Total) : 0);
            tr_body +=
            "<tr class='tr-" + response[i].No_Diferencia + "'>"
              +"<td class='text-left'>" + response[i].No_Organizacion + "</td>"
              +"<td class='text-left'>" + response[i].No_Almacen + "</td>"
              +"<td class='text-left'>" + response[i].No_Entidad + "</td>"
              +"<td class='text-center'>" + response[i].Fe_Apertura + "</td>"
              +"<td class='text-center'>" + response[i].Fe_Cierre + "</td>"
              +"<td class='text-right'>" + response[i].No_Signo + "</td>"
              +"<td class='text-right'>" + number_format(fLiquidar, 2) + "</td>"
              +"<td class='text-right'>" + number_format(fDepositar, 2) + "</td>"
              +"<td class='text-right'>" + response[i].Ss_Diferencia + "</td>"
              +"<td class='text-left'>" + response[i].Txt_Nota + "</td>"
              +"<td class='text-center'>" + response[i].sAccionVer + "</td>"
              +"<td class='text-center'>" + response[i].sAccionImprimir + "</td>"
            +"</tr>";
          }
        } else {
          if( response.sMessageSQL !== undefined ) {
            console.log(response.sMessageSQL);
          }
          tr_body +=
          "<tr>"
            +"<td colspan='12' class='text-center'>" + response.sMessage + "</td>"
          + "</tr>";
        } // ./ if arrData
        
        $( '#div-liquidacion_caja' ).show();
        $( '#table-liquidacion_caja > tbody' ).append(tr_body);
        
        $( '#btn-html_liquidacion_caja' ).text('');
        $( '#btn-html_liquidacion_caja' ).append( '<i class="fa fa-search"></i> Buscar' );
        $( '#btn-html_liquidacion_caja' ).attr('disabled', false);
      }, 'JSON')
      .fail(function(jqXHR, textStatus, errorThrown) {
        $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
        
        $( '#modal-message' ).modal('show');
        $( '.modal-message' ).addClass( 'modal-danger' );
        $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
        setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
        
        //Message for developer
        console.log(jqXHR.responseText);
        
        $( '#btn-html_liquidacion_caja' ).text('');
        $( '#btn-html_liquidacion_caja' ).append( '<i class="fa fa-search"></i> Buscar' );
        $( '#btn-html_liquidacion_caja' ).attr('disabled', false);
      });
    } else if ($(this).data('type') == 'pdf') {
      $( '#btn-pdf_liquidacion_caja' ).text('');
      $( '#btn-pdf_liquidacion_caja' ).attr('disabled', true);
      $( '#btn-pdf_liquidacion_caja' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
          
      url = base_url + 'PuntoVenta/informes_caja/LiquidacionCajaController/sendReportePDF/' + ID_Almacen + '/' + Fe_Inicio + '/' + Fe_Fin + '/' + iIdEmpleado + '/' + sNombreEmpleado + '/' + ID_Organizacion;
      window.open(url,'_blank');
      
      $( '#btn-pdf_liquidacion_caja' ).text('');
      $( '#btn-pdf_liquidacion_caja' ).append( '<i class="fa fa-file-pdf-o color_icon_pdf"></i> PDF' );
      $( '#btn-pdf_liquidacion_caja' ).attr('disabled', false);
    } else if ($(this).data('type') == 'excel') {
      $( '#btn-excel_liquidacion_caja' ).text('');
      $( '#btn-excel_liquidacion_caja' ).attr('disabled', true);
      $( '#btn-excel_liquidacion_caja' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
      
      url = base_url + 'PuntoVenta/informes_caja/LiquidacionCajaController/sendReporteEXCEL/' + ID_Almacen + '/' + Fe_Inicio + '/' + Fe_Fin + '/' + iIdEmpleado + '/' + sNombreEmpleado + '/' + ID_Organizacion;
      window.open(url,'_blank');
      
      $( '#btn-excel_liquidacion_caja' ).text('');
      $( '#btn-excel_liquidacion_caja' ).append( '<i class="fa fa-file-excel-o color_icon_excel"></i> Excel' );
      $( '#btn-excel_liquidacion_caja' ).attr('disabled', false);
    }// ./ if
  })//./ btn
})