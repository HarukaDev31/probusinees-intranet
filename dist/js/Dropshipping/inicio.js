var startDate = fYear + '-' + fMonth + '-01', starGetNew = '', starFilter = '';
var endDate = fYear + '-' + fMonth + '-' + fDay, endGetNew = '', endFilter = '';
var iIDMoneda = 0;

jQuery(function($) {
  url = base_url + 'HelperController/getMonedas';
  $.post(url, function (response) {
    iIDMoneda = response[0].ID_Moneda;
    $('#cbo-filtro_moneda').html('');
    for (var i = 0; i < response.length; i++)
      $('#cbo-filtro_moneda').append('<option value="' + response[i].ID_Moneda + '">' + response[i].No_Moneda + '</option>');
  }, 'JSON');

  //Date range as a button
  $('#daterange-btn').daterangepicker(
    {
      ranges   : {
        'Hoy'           : [moment(), moment()],
        'Ayer'          : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Último 7 Días' : [moment().subtract(6, 'days'), moment()],
        'Último 30 Días': [moment().subtract(29, 'days'), moment()],
        'Mes Actual'    : [moment().startOf('month'), moment().endOf('month')],
        'Último Mes'    : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
      },
      startDate: new Date(),//Hoy
      endDate: new Date(),//Hoy
      //startDate : moment().startOf('month'),
      //endDate  : moment().endOf('month'),
      "locale": {
        "format": "DD/MM/YYYY",
        "separator": " - ",
        "applyLabel": "Buscar",
        "cancelLabel": "Salir",
        "fromLabel": "Desde",
        "toLabel": "Hasta",
        "customRangeLabel": "Seleccionar rango fecha",
        "daysOfWeek": [
          "Do",
          "Lu",
          "Ma",
          "Mié",
          "Ju",
          "Vi",
          "Sá"
        ],
        "monthNames": [
          "Enero",
          "Febrero",
          "Marzo",
          "Abril",
          "Mayo",
          "Junio",
          "Julio",
          "Agosto",
          "Setiembre",
          "Octubre",
          "Noviembre",
          "Diciembre"
        ],
        "firstDay": 1
      }
    },
    function (start, end) {
      $('#daterange-btn span').html(start.format('D MMMM, YYYY') + ' - ' + end.format('D MMMM, YYYY'));
      startDate = start;
      endDate = end;
      starGetNew = start;
      endGetNew = end;

      var arrReporteGrafico = {
        dInicial:start.format('YYYY-MM-DD'),
        dFinal:end.format('YYYY-MM-DD'),
        iIDMoneda: $('#cbo-filtro_moneda').val(),
        iImpuesto: $('#cbo-filtro_impuesto').val(),
      };
      
      reporteGraficoInicio(arrReporteGrafico);  
    }
  );

  $('#cbo-filtro_moneda').change(function () {
    starFilter = startDate;
    endFilter = endDate;
    if (typeof startDate === 'object') {
      $('#daterange-btn span').html(starGetNew.format('D MMMM, YYYY') + ' - ' + endGetNew.format('D MMMM, YYYY'));
      starFilter = starGetNew.format('YYYY-MM-DD');
      endFilter = endGetNew.format('YYYY-MM-DD');
    }

    var arrReporteGrafico = {
      dInicial: startDate,
      dFinal: endDate,
      iIDMoneda: $('#cbo-filtro_moneda').val(),
      iImpuesto: $('#cbo-filtro_impuesto').val(),
    };

    reporteGraficoInicio(arrReporteGrafico);
  });

  $('#cbo-filtro_impuesto').change(function () {
    starFilter = startDate;
    endFilter = endDate;
    if (typeof startDate === 'object') {
      $('#daterange-btn span').html(starGetNew.format('D MMMM, YYYY') + ' - ' + endGetNew.format('D MMMM, YYYY'));
      starFilter = starGetNew.format('YYYY-MM-DD');
      endFilter = endGetNew.format('YYYY-MM-DD');
    }

    var arrReporteGrafico = {
      dInicial: starFilter,
      dFinal: endFilter,
      iIDMoneda: $('#cbo-filtro_moneda').val(),
      iImpuesto: $('#cbo-filtro_impuesto').val(),
    };

    reporteGraficoInicio(arrReporteGrafico);
  });

	// Modal de change log de versiones
  $( '#btn-modal-actualizar_version_sistema' ).off('click').click(function () {
    enviarCorreoSoporteMigracionSistema();
  });
})

function reporteGraficoInicio(arrReporteGrafico){
  $( '#modal-loader' ).modal('show');
  $("#div-inicio-reporte-grafico").load(base_url + 'Dropshipping/InicioDropshippingController/Ajax/Reporte',
    {
      arrReporteGrafico
    },
    function(response, status, xhr) {
      $( '#modal-loader' ).modal('hide');
      
      $( '.modal-message' ).removeClass( "modal-danger modal-warning modal-success" );
      
      if (status == "error"){
        $( '#modal-message' ).modal('show');
        $( '.modal-message' ).addClass( "modal-danger" );
        $( '.modal-title-message' ).text( xhr.status + " " + xhr.statusText );
      }
    }
  );
}   
            