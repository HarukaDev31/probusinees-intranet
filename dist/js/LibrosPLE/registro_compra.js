var url;

function ReloadReporte(){		  
  $( '#btn-reload' ).text('');
  $( '#btn-reload' ).attr('disabled', true);
  $( '#btn-reload' ).append( 'Actulizando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
  url = base_url + 'LibrosPLE/RegistroCompraController/ReporteComprasLista';
  $.post( url, {}, function( response ){
    var tpl = _.template($("#TemplateReporte").html());
    var tplString = tpl(response);
    $("#CuerpoReporte").html(tplString);
    $( '#btn-reload' ).text('');
    $( '#btn-reload' ).attr('disabled', false);
    $( '#btn-reload' ).append( 'Actualizar Estado Reporte' );
  },"json");
}

$(function () {  
  url = base_url + 'LibrosPLE/RegistroCompraController/getTiposLibroSunat';
  $.post( url, {ID_Tipo_Asiento : 2}, function( response ){
    $( '#cbo-TiposLibroSunatCompra' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
    for (var i = 0, len = response.length; i < len; i++) {
      $( '#cbo-TiposLibroSunatCompra' ).append( '<option value="' + response[i].ID_Tipo_Asiento_Detalle + '" data-id_tipo_asiento="' + response[i].ID_Tipo_Asiento + '" data-nu_codigo_libro_sunat="' + response[i].Nu_Codigo_Libro_Sunat + '" data-no_tipo_asiento_apertura="' + response[i].No_Tipo_Asiento_Apertura + '">' + response[i].No_Sub_Libro_Sunat + '</option>' );
    }
  }, 'JSON');
  
  $( '#cbo-organizaciones' ).html( '<option value="0" selected="selected">- Todas -</option>');
  url = base_url + 'HelperController/getOrganizaciones';
  var arrParams = {
    iIdEmpresa : $( '#header-a-id_empresa' ).val(),
  }
  $.post( url, arrParams, function( response ){
    if ( response.length == 1 ) //única organización
      $( '#cbo-organizaciones' ).append( '<option value="' + response[0].ID_Organizacion + '">' + response[0].No_Organizacion + '</option>' );
    else if (response.length > 1 ) {
      for (var i = 0; i < response.length; i++)
        $( '#cbo-organizaciones' ).append( '<option value="' + response[i].ID_Organizacion + '">' + response[i].No_Organizacion + '</option>' );
    }
  }, 'JSON');
  
  $( '#btn-modificar' ).click(function(){
    var fYear, fMonth, iOrdenar;

    fYear = $( '#cbo-year' ).val();
    fMonth = $( '#cbo-mes' ).val();
    iOrdenar = $( '#cbo-ordenar' ).val();

    url = base_url + 'LibrosPLE/RegistroCompraController/modificarCorrelativo';
    $.post( url, {
      fYear : fYear,
      fMonth : fMonth,
      iOrdenar : iOrdenar,
    }, function( response ){
      if ( response.sStatus=='success' ){
        alert( response.sMessage );
      } else {
        alert( response.sMessage );
      }
    }, 'json');
  })

  $( '#btn-reload' ).click(ReloadReporte);
  
  $( '#btn-generar' ).click(function(){
    if ( $( '#cbo-TiposLibroSunatCompra' ).val() == 0 ) {
      $( '#cbo-TiposLibroSunatCompra' ).closest('.form-group').find('.help-block').html('Seleccionar libro');
		  $( '#cbo-TiposLibroSunatCompra' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      $( '#cbo-TiposLibroSunatCompra' ).closest('.form-group').find('.help-block').html('');
		  $( '#cbo-TiposLibroSunatCompra' ).closest('.form-group').removeClass('has-error');
		  
      $( '#btn-generar' ).text('');
      $( '#btn-generar' ).attr('disabled', true);
      $( '#btn-generar' ).append( 'Generando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

      var ID_Tipo_Asiento, ID_Tipo_Asiento_Detalle, sNombreLibroSunat, ID_Organizacion, ID_Tipo_Vista, Nu_Codigo_Libro_Sunat, No_Tipo_Asiento_Apertura, fYear, fMonth, fMonthText;

      ID_Tipo_Asiento = $( '#cbo-TiposLibroSunatCompra' ).find(':selected').data('id_tipo_asiento');
      ID_Tipo_Asiento_Detalle = $( '#cbo-TiposLibroSunatCompra' ).val();
      sNombreLibroSunat = $( '#cbo-TiposLibroSunatCompra :selected' ).text();
      ID_Organizacion = $( '#cbo-organizaciones' ).val();
      ID_Tipo_Vista = $( '#cbo-tipo_vista_venta' ).val();
      Nu_Codigo_Libro_Sunat = $( '#cbo-TiposLibroSunatCompra' ).find(':selected').data('nu_codigo_libro_sunat');
      No_Tipo_Asiento_Apertura = $( '#cbo-TiposLibroSunatCompra' ).find(':selected').data('no_tipo_asiento_apertura');
      fYear = $( '#cbo-year' ).val();
      fMonth = $( '#cbo-mes :selected' ).val();
      fMonthText = $( '#cbo-mes :selected' ).text();
      Nu_Tipo_Formato = $("input[name='Nu_Tipo_Formato']:checked").val();
      
      var arrPost = {
        ID_Tipo_Asiento : ID_Tipo_Asiento,
        ID_Tipo_Asiento_Detalle:ID_Tipo_Asiento_Detalle,
        sNombreLibroSunat:sNombreLibroSunat,
        ID_Organizacion : ID_Organizacion,
        ID_Tipo_Vista : ID_Tipo_Vista,
        Nu_Codigo_Libro_Sunat : Nu_Codigo_Libro_Sunat,
        No_Tipo_Asiento_Apertura : No_Tipo_Asiento_Apertura,
        fYear : fYear,
        fMonth : fMonth,
        fMonthText:fMonthText,
        Nu_Tipo_Formato:Nu_Tipo_Formato
      }

      url = base_url + 'LibrosPLE/RegistroCompraController/CrearReporteCompras';

      $.post( url, arrPost, function( response ){
        ReloadReporte();
        $('#modal-compra').modal('show');
        $( '#btn-generar' ).text('');
        $( '#btn-generar' ).attr('disabled', false);
        $( '#btn-generar' ).append( 'Generar Reporte' );
      },"json");
    }
  })

  $(document).on("click",".btn-download",function(){
    window.open(base_url + 'LibrosPLE/RegistroCompraController/BajarReporte/'+$(this).data("valor"), "_blank");
  });

  $(document).on("click",".btn-cancelar",function(){
    url = base_url + 'LibrosPLE/RegistroCompraController/CancelarReporte';
    $.post( url, {"ID_Reporte":$(this).data("valor")}, function( response ){
      ReloadReporte();
    },"json");
  });

  $( '#btn-reload' ).trigger("click");
})