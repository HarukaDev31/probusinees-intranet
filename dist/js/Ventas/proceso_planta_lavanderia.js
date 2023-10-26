var url;

$(function () {  
  $('.select2').select2();
  
  $( '#modal-loader' ).modal('show');

  $( '#check-AllMenuHeader' ).prop('checked', false);

  $( '#div-proceso_planta_lavanderia' ).hide();
  
  url = base_url + 'HelperController/getTiposDocumentos';
  $.post( url, {Nu_Tipo_Filtro : 3}, function( response ){
    $( '#cbo-filtros_tipos_documento' ).html('<option value="0" selected="selected">Todos</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-filtros_tipos_documento' ).append( '<option value="' + response[i].ID_Tipo_Documento + '">' + response[i].No_Tipo_Documento_Breve + '</option>' );
	  $( '#modal-loader' ).modal('hide');
  }, 'JSON');
  
	$( '#cbo-filtros_series_documento' ).html('<option value="0" selected="selected">Todos</option>');
	$( '#cbo-filtros_tipos_documento' ).change(function(){
	  $( '#cbo-filtros_series_documento' ).html('<option value="0" selected="selected">Todos</option>');
	  if ( $(this).val() > 0) {
		  url = base_url + 'HelperController/getSeriesDocumentoOficinaPuntoVenta';
      $.post( url, { ID_Tipo_Documento: $(this).val() }, function( response ){
        var l = response.length;
        var sTipoSerie = 'oficina';
        for (var i = 0; i < l; i++) {
          sTipoSerie = '(' + ( response[i].ID_POS > 0 ? 'pv' : 'oficina' ) + ')';
          $( '#cbo-filtros_series_documento' ).append( '<option value="' + response[i].ID_Serie_Documento + '">' + response[i].ID_Serie_Documento + ' ' + sTipoSerie + '</option>' );
        }
      }, 'JSON');
	  }
  })
  
  $( '.btn-proceso_planta_lavanderia' ).click(function(){
    if ( $( '#txt-Filtro_Entidad' ).val().length > 0 && $( '#txt-AID' ).val().length === 0 ) {
      $( '#txt-Filtro_Entidad' ).closest('.form-group').find('.help-block').html('Seleccionar cliente');
		  $( '#txt-Filtro_Entidad' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      $( '.help-block' ).empty();
    
      var Fe_Inicio, Fe_Fin, iIdTipoDocumento, iIdSerieDocumento, iNumeroDocumento, iEstado, iIdCliente, sNombreCliente;
      
      Fe_Inicio = ParseDateString($( '#txt-Filtro_Fe_Inicio' ).val(), 1, '/');
      Fe_Fin = ParseDateString($( '#txt-Filtro_Fe_Fin' ).val(), 1, '/');
      iIdTipoDocumento = $( '#cbo-filtros_tipos_documento' ).val();
      iIdSerieDocumento = $( '#cbo-filtros_series_documento' ).val();
      iNumeroDocumento = ($( '#txt-Filtro_NumeroDocumento' ).val().length === 0 ? '-' : $( '#txt-Filtro_NumeroDocumento' ).val());
      iEstado = $( '#cbo-estado_documento' ).val();
      iIdCliente = ($( '#txt-AID' ).val().length === 0 ? '-' : $( '#txt-AID' ).val());
      sNombreCliente = ($( '#txt-Filtro_Entidad' ).val().length === 0 ? '-' : $( '#txt-Filtro_Entidad' ).val());

      var arrPost = {
        Fe_Inicio : Fe_Inicio,
        Fe_Fin : Fe_Fin,
        iIdTipoDocumento : iIdTipoDocumento,
        iIdSerieDocumento : iIdSerieDocumento,
        iNumeroDocumento : iNumeroDocumento,
        iEstado : iEstado,
        iIdCliente : iIdCliente,
        sNombreCliente : sNombreCliente,
      };
        
      if ($(this).data('type') == 'html') {
        getReporteHTML(arrPost);
      } else if ($(this).data('type') == 'pdf') {
        $( '#btn-pdf_proceso_planta_lavanderia' ).text('');
        $( '#btn-pdf_proceso_planta_lavanderia' ).attr('disabled', true);
        $( '#btn-pdf_proceso_planta_lavanderia' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

        url = base_url + 'Ventas/ProcesoPlantaLavanderiaController/sendReportePDF/' + Fe_Inicio + '/' + Fe_Fin + '/' + iIdTipoDocumento + '/' + iIdSerieDocumento + '/' + iNumeroDocumento + '/' + iEstado + '/' + iIdCliente + '/' + sNombreCliente;
        window.open(url,'_blank');
        
        $( '#btn-pdf_proceso_planta_lavanderia' ).text('');
        $( '#btn-pdf_proceso_planta_lavanderia' ).append( '<i class="fa fa-file-pdf-o color_icon_pdf"></i> PDF' );
        $( '#btn-pdf_proceso_planta_lavanderia' ).attr('disabled', false);
      } else if ($(this).data('type') == 'excel') {
        $( '#btn-excel_proceso_planta_lavanderia' ).text('');
        $( '#btn-excel_proceso_planta_lavanderia' ).attr('disabled', true);
        $( '#btn-excel_proceso_planta_lavanderia' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
        
        url = base_url + 'Ventas/ProcesoPlantaLavanderiaController/sendReporteEXCEL/' + Fe_Inicio + '/' + Fe_Fin + '/' + iIdTipoDocumento + '/' + iIdSerieDocumento + '/' + iNumeroDocumento + '/' + iEstado + '/' + iIdCliente + '/' + sNombreCliente;
        window.open(url,'_blank');
        
        $( '#btn-excel_proceso_planta_lavanderia' ).text('');
        $( '#btn-excel_proceso_planta_lavanderia' ).append( '<i class="fa fa-file-excel-o color_icon_excel"></i> Excel' );
        $( '#btn-excel_proceso_planta_lavanderia' ).attr('disabled', false);
      }// /. if all button 
    }// /. if - else validacion
  })// /. btn
})

function checkAllMenuHeader(){
	if ( $( '#check-AllMenuHeader' ).prop('checked') ){
		$( '.check-iIdDocumentoCabecera' ).prop('checked', true);
		$( '#check-AllMenuFooter' ).prop('checked', true);
	}else{
		if( false == $( '#check-AllMenuHeader' ).prop('checked') ){
			$( '.check-iIdDocumentoCabecera' ).prop('checked', false);
			$( '#check-AllMenuFooter' ).prop('checked', false);
		}
	}
}

function checkAllMenuFooter(){
	if ( $( '#check-AllMenuFooter' ).prop('checked') ){
		$( '.check-iIdDocumentoCabecera' ).prop('checked', true);
		$( '#check-AllMenuHeader' ).prop('checked', true);
	}else{
		if( false == $( '#check-AllMenuFooter' ).prop('checked') ){
			$( '.check-iIdDocumentoCabecera' ).prop('checked', false);
			$( '#check-AllMenuHeader' ).prop('checked', false);
		}
	}
}

function cambiarProcesoPlantaLavanderia(){    
	$( '.btn-save' ).text('');
	$( '.btn-save' ).attr('disabled', true);
	$( '.btn-save' ).append( 'Enviando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

	$( '#modal-loader' ).modal('show');
	url = base_url + 'Ventas/ProcesoPlantaLavanderiaController/cambiarProcesoPlantaLavanderia';
	$.ajax({
		type : 'POST',
		dataType : 'JSON',
		url : url,
		data : $('#form-proceso_planta_lavanderia').serialize(),
		success : function( response ){
			$( '#modal-loader' ).modal('hide');
			$( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
			$( '#modal-message' ).modal('show');
			
			if (response.sStatus == 'success'){
				$( '.modal-message' ).addClass( 'modal-' + response.sStatus);
				$( '.modal-title-message' ).text( response.sMessage );
        setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
        
        var Fe_Inicio, Fe_Fin, iIdTipoDocumento, iIdSerieDocumento, iNumeroDocumento, iEstado, iIdCliente, sNombreCliente;
        
        Fe_Inicio = ParseDateString($( '#txt-Filtro_Fe_Inicio' ).val(), 1, '/');
        Fe_Fin = ParseDateString($( '#txt-Filtro_Fe_Fin' ).val(), 1, '/');
        iIdTipoDocumento = $( '#cbo-filtros_tipos_documento' ).val();
        iIdSerieDocumento = $( '#cbo-filtros_series_documento' ).val();
        iNumeroDocumento = ($( '#txt-Filtro_NumeroDocumento' ).val().length === 0 ? '-' : $( '#txt-Filtro_NumeroDocumento' ).val());
        iEstado = $( '#cbo-estado_documento' ).val();
        iIdCliente = ($( '#txt-AID' ).val().length === 0 ? '-' : $( '#txt-AID' ).val());
        sNombreCliente = ($( '#txt-Filtro_Entidad' ).val().length === 0 ? '-' : $( '#txt-Filtro_Entidad' ).val());

        var arrPost = {
          Fe_Inicio : Fe_Inicio,
          Fe_Fin : Fe_Fin,
          iIdTipoDocumento : iIdTipoDocumento,
          iIdSerieDocumento : iIdSerieDocumento,
          iNumeroDocumento : iNumeroDocumento,
          iEstado : iEstado,
          iIdCliente : iIdCliente,
          sNombreCliente : sNombreCliente,
        };

        getReporteHTML(arrPost);
			} else {
				$( '.modal-message' ).addClass( 'modal-' + response.sStatus);
				$( '.modal-title-message' ).text(response.sMessage);
				setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
			}

			$( '.btn-save' ).text('');
			$( '.btn-save' ).append( '<span class="fa fa-save"></span> Enviar' );
			$( '.btn-save' ).attr('disabled', false);
		}
  })
	.fail(function(jqXHR, textStatus, errorThrown) {
    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
    
    $( '#modal-message' ).modal('show');
    $( '.modal-message' ).addClass( 'modal-danger' );
    $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
		setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
		
		$( '#modal-loader' ).modal('hide');
	
		//Message for developer
		console.log(jqXHR.responseText);

		$( '.btn-save' ).text('');
		$( '.btn-save' ).append( '<span class="fa fa-save"></span> Enviar' );
		$( '.btn-save' ).attr('disabled', false);
	});
}

function getReporteHTML(arrPost){
  $( '#btn-html_proceso_planta_lavanderia' ).text('');
  $( '#btn-html_proceso_planta_lavanderia' ).attr('disabled', true);
  $( '#btn-html_proceso_planta_lavanderia' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

  $( '#table-proceso_planta_lavanderia > tbody' ).empty();
  $( '#table-proceso_planta_lavanderia > tfoot' ).empty();
  
  url = base_url + 'Ventas/ProcesoPlantaLavanderiaController/sendReporte';
  $.post( url, arrPost, function( response ){
    if ( response.sStatus == 'success' ) {
      var iTotalRegistros = response.arrData.length, tr_body = '', tr_foot = '', total_s = 0.00, total_s_saldo = 0.00, sum_total_s = 0.00, sum_total_s_saldo = 0.00;
      var response=response.arrData;
      for (var i = 0; i < iTotalRegistros; i++) {
        total_s = (!isNaN(parseFloat(response[i].Ss_Total)) ? parseFloat(response[i].Ss_Total) : 0);
        total_s_saldo = (!isNaN(parseFloat(response[i].Ss_Total_Saldo)) ? parseFloat(response[i].Ss_Total_Saldo) : 0);

        tr_body +=
        "<tr>"
          +"<td class='text-center'>" + response[i].Fe_Emision_Hora + "</td>"
          +"<td class='text-center'>" + response[i].No_Tipo_Documento_Breve + "</td>"
          +"<td class='text-center'>" + response[i].ID_Serie_Documento + "</td>"
          +"<td class='text-center'>" + response[i].ID_Numero_Documento + "</td>"
          +"<td class='text-left'>" + response[i].No_Entidad + "</td>"
          +"<td class='text-center'>" + response[i].No_Signo + "</td>"
          +"<td class='text-right'>" + (response[i].ID_Tipo_Documento != 5 ? '' : '-') + number_format(total_s, 2) + "</td>"
          +"<td class='text-right'>" + (response[i].ID_Tipo_Documento != 5 ? '' : '-') + number_format(total_s_saldo, 2) + "</td>"
          +"<td class='text-center'><span class='label label-" + response[i].No_Class_Estado + "'>" + response[i].No_Estado + "</span></td>"
          +"<td class='text-center'><span class='label label-" + response[i].No_Class_Estado_Lavado + "'>" + response[i].No_Estado_Lavado + "</span><i class='fa fa-refresh fa-spin fa-lg fa-fw'></i></td>"
          +"<td class='text-center'>" + (response[i].Nu_Estado == 6 || response[i].Nu_Estado == 8 ? response[i].sAccionVer : '') + "</td>"
          +"<td class='text-center'>" + (response[i].Nu_Estado == 6 || response[i].Nu_Estado == 8 ? response[i].sAccionImprimir : '') + "</td>"
          +"<td class='text-center'>" + (response[i].Nu_Estado == 6 || response[i].Nu_Estado == 8 ? response[i].sAccionVerComanda : '') + "</td>"
          +"<td class='text-center'>" + (response[i].Nu_Estado == 6 || response[i].Nu_Estado == 8 ? response[i].sAccionImprimirComanda : '') + "</td>"
        +"</tr>";
        
        sum_total_s += total_s;
        sum_total_s_saldo += total_s_saldo;
      }
    } else {
      if( response.sMessageSQL !== undefined ) {
        console.log(response.sMessageSQL);
      }
      tr_body +=
      "<tr>"
        +"<td colspan='9' class='text-center'>" + response.sMessage + "</td>"
      + "</tr>";
    } // ./ if arrData
    
    $( '#div-proceso_planta_lavanderia' ).show();
    $( '#table-proceso_planta_lavanderia > tbody' ).append(tr_body);
    $( '#table-proceso_planta_lavanderia > tbody' ).after(tr_foot);
    
    $( '#btn-html_proceso_planta_lavanderia' ).text('');
    $( '#btn-html_proceso_planta_lavanderia' ).append( '<i class="fa fa-search"></i> Buscar' );
    $( '#btn-html_proceso_planta_lavanderia' ).attr('disabled', false);
  }, 'JSON')
  .fail(function(jqXHR, textStatus, errorThrown) {
    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
    
    $( '#modal-message' ).modal('show');
    $( '.modal-message' ).addClass( 'modal-danger' );
    $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
    setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
    
    //Message for developer
    console.log(jqXHR.responseText);
    
    $( '#btn-html_proceso_planta_lavanderia' ).text('');
    $( '#btn-html_proceso_planta_lavanderia' ).append( '<i class="fa fa-search"></i> Buscar' );
    $( '#btn-html_proceso_planta_lavanderia' ).attr('disabled', false);
  });
}

function formatoImpresionTicketComandaLavado(arrPost){
  if ( arrPost.sAccion == undefined ) {
    arrPost = JSON.parse(arrPost);
  }

  if ( arrPost.sAccion != 'imprimir' ) {
    $( '.modal-ticket_comanda_lavado' ).modal('show');
  }

  url = base_url + 'ImprimirTicketController/formatoImpresionTicketComandaLavado';
  $.post( url, arrPost, function( response ) {
    if ( response.sStatus == 'success' ) {
      var iTotalRegistros = response.arrData.length, response=response.arrData;

      // Logo empresa
      if ( arrPost.sMostrarOcultarImagen == 'ocultar-img-logo_punto_venta_click' )
        $ ( '#img-logo_punto_venta_click_lavado' ).hide();

      if ( iMostrarLogoTicketGlobal == 1 ) {//1=Si
        if ( arrPost.sAccion != 'imprimir' ) {
          $ ( '#img-logo_punto_venta_lavado' ).hide();
          var url_logo_dominio = src_root_sitio_web_js + 'assets/images/logos/' + response[0].No_Logo_Empresa;
          $("#img-logo_punto_venta_click_lavado").attr({ "src": url_logo_dominio });
        }
      }

      if ( arrPost.sMostrarOcultarImagen == 'mostrar-img-logo_punto_venta' ) {
        $ ( '#img-logo_punto_venta_lavado' ).show();
        $ ( '#img-logo_punto_venta_click_lavado' ).hide();
      }
      // /. Logo empresa
      
      // Cabecera <p> Titulo Numero de Orden
      var p_cabecera_title_numero = '<strong>' + response[0].ID_Serie_Documento + '-' + response[0].ID_Numero_Documento + '</strong>';
      $( '#modal-body-p-title_numero' ).html(p_cabecera_title_numero);
      // /. Cabecera <p> Titulo Numero de Orden
      
      // Cabecera <p> Titulo Tipo envio lavado
      var p_cabecera_title_tipo_envio_lavado = '<strong>' + response[0].No_Estado_Pedido_Lavado + '</strong>';
      $( '#modal-body-p-title_tipo_envio_lavado' ).html(p_cabecera_title_tipo_envio_lavado);
      // /. Cabecera <p> Titulo Tipo envio lavado

      // Cabecera y detalle table
      var table_cabecera_detalle = '';
      table_cabecera_detalle += 
      '<thead>'
        +'<tr>'
          +'<td class="text-left">F. Emisión: </td>'
          +'<td class="text-left">' + response[0].Fe_Emision_Hora + '</td>'
        +'</tr>'
        +'<tr>'
          +'<td class="text-left">F. Entrega: </td>'
          +'<td class="text-left">' + response[0].Fe_Entrega + '</td>'
        +'</tr>'
        +'<tr>'
          +'<td class="text-left">Cliente: </td>'
          +'<td class="text-left">' + response[0].Nu_Documento_Identidad + ' - ' + response[0].No_Entidad + '</td>'
        +'</tr>'
        +'<tr>'
          +'<td class="text-left">Celular: </td>'
          +'<td class="text-left">' + response[0].Nu_Celular_Entidad + '</td>'
        +'</tr>'
        +'<tr>'
          +'<td class="text-left" colspan="2">&nbsp;</td>'
        +'</tr>'
      +'</thead>'
      +'<tbody>'
        +'<tr>'
          +'<td class="text-left" style="width: 3%; padding: 2px; border-top: 1px solid transparent !important; border-bottom: 1px solid black !important;">CANTIDAD</td>'
          +'<td class="text-left" style="width: 20%; padding: 2px; border-top: 1px solid transparent !important; border-bottom: 1px solid black !important;">DESCRIPCION</td>'
        +'</tr>';
      for (var i = 0; i < iTotalRegistros; i++) {
        table_cabecera_detalle +=
        '<tr>'
          +'<td class="text-left" style="padding: 0px; border-top: 1px solid transparent;">' + response[i].Qt_Producto + '</td>'
          +'<td class="text-left" style="padding: 0px;">' + response[i].No_Producto + ' ' + response[i].Txt_Nota_Item + '</td>'
        +'</tr>';
      }
      table_cabecera_detalle += 
      +'</tbody>'
      +'<tfoot>'
        +'<tr>'
          +'<td class="text-left" colspan="2">&nbsp;</td>'
        +'</tr>'
        +'<tr>'
          +'<td class="text-left">Cajero: </td>'
          +'<td class="text-left">' + response[0].No_Empleado + '</td>'
        +'</tr>'
      +'</tfoot>';
      // /. Cabecera y detalle table
      $( '#modal-table-ticket_comanda_lavado' ).html(table_cabecera_detalle);

      if (arrPost.sAccion == 'imprimir') {
        generarFormatoImpresionComanda('div-ticket_comanda_lavado');
      }
    } else {
      if( response.sMessageSQL !== undefined ) {
        console.log(response.sMessageSQL);
      }
      alert( response.sMessage );
    }
  }, 'JSON')
  .fail(function(jqXHR, textStatus, errorThrown) {
    console.log(jqXHR.responseText);
    $( '#modal-loader' ).modal('hide');
  });
}

function generarFormatoImpresionComanda(sIdFormatoImpresion){
  winPrintSunat = window.open("", "MsgWindow", "top=80,left=800,width=550,height=550");
  winPrintSunat.document.open();
	printContentsSunat = document.getElementById(sIdFormatoImpresion).innerHTML;
  winPrintSunat.document.write(printContentsSunat);
	winPrintSunat.document.close();
	winPrintSunat.focus();
	winPrintSunat.print();
	winPrintSunat.close();
}