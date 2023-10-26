var url;

$(function () {  
  $('.select2').select2();
  
  $( '#modal-loader' ).modal('show');

  $('#div-ventas_x_familia').hide();

  $('#cbo-sede_musica').html('<option value="0" selected="selected">- Sin registros -</option>');
  url = base_url + 'HelperController/getSedexEmpresa';
  var arrParams = {
    iIdEmpresa: $('#header-a-id_empresa').val(),
  };
  $.post(url, arrParams, function (response) {
    if (response.sStatus == 'success') {
      $('#cbo-sede_musica').html('<option value="0" selected="selected">- Seleccionar -</option>');
      var l = response.arrData.length;
      for (var x = 0; x < l; x++) {
        $('#cbo-sede_musica').append('<option value="' + response.arrData[x].ID + '">' + response.arrData[x].Nombre + '</option>');
      }
    } else {
      if (response.sMessageSQL !== undefined) {
        console.log(response.sMessageSQL);
      }
      console.log(response.sMessage);
    }

    $('#modal-loader').modal('hide');
  }, 'JSON');

  $('#cbo-salon').html('<option value="0" selected="selected">- Todos -</option>');
  $('#cbo-sede_musica').change(function () {
    $('#cbo-salon').html('<option value="0" selected="selected">- Todos -</option>');
    url = base_url + 'HelperController/getSalonxEmpresa';
    var arrParams = {
      iIdEmpresa: $('#header-a-id_empresa').val(),
      ID_Sede_Musica: $(this).val(),
    }
    $.post(url, arrParams, function (response) {
      if (response.sStatus == 'success') {
        var l = response.arrData.length;
        $('#cbo-salon').html('<option value="0" selected="selected">- Todos -</option>');
        for (var x = 0; x < l; x++) {
          $('#cbo-salon').append('<option value="' + response.arrData[x].ID + '">' + response.arrData[x].Nombre + '</option>');
        }
      } else {
        if (response.sMessageSQL !== undefined) {
          console.log(response.sMessageSQL);
        }
        $('#modal-message').modal('show');
        $('.modal-message').addClass('modal-' + response.sStatus);
        $('.modal-title-message').text(response.sMessage);
        setTimeout(function () { $('#modal-message').modal('hide'); }, 1200);
      }
    }, 'JSON');
  });

  $('.btn-generar_ventas_x_familia').click(function () {
    if ($('#cbo-sede_musica').val() == '0') {
      $('#cbo-sede_musica').closest('.form-group').find('.help-block').html('Seleccionar sede');
      $('#cbo-sede_musica').closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      $('.form-group').removeClass('has-error');
      $('.help-block').empty();
    
      var ID_Sede_Musica, ID_Salon;
      
      ID_Sede_Musica = $( '#cbo-sede_musica' ).val();
      ID_Salon = $('#cbo-salon').val();

      var arrPost = {
        ID_Sede_Musica: ID_Sede_Musica,
        ID_Salon: ID_Salon,
      };
        
      if ($(this).data('type') == 'html') {
        $( '#btn-html_ventas_x_familia' ).text('');
        $( '#btn-html_ventas_x_familia' ).attr('disabled', true);
        $( '#btn-html_ventas_x_familia' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
    
        $( '#table-ventas_x_familia > tbody' ).empty();
        $( '#table-ventas_x_familia > tfoot' ).empty();
        
        url = base_url + 'Escuela/ReporteMatriculaAlumnoController/sendReporte';
        $.post( url, arrPost, function( response ){
          console.log(response);
          if ( response.sStatus == 'success' ) {
            var iTotalRegistrosB = response.arrData.length;
            var response_data = response.arrData;

            var iTotalRegistros = response.arrDataHorarioClase.length, tr_body = '';
            var response = response.arrDataHorarioClase;

            var xHorarioPintoHora = 0;

            var xHorarioPintoLunes = 0;
            var xHorarioPintoMartes = 0;
            var xHorarioPintoMiercoles = 0;
            var xHorarioPintoJueves = 0;
            var xHorarioPintoViernes = 0;
            var xHorarioPintoSabado = 0;
            var xHorarioPintoDomingo = 0;

            for (var i = 0; i < iTotalRegistros; i++) {
              tr_body += "<tr>";
              tr_body += "<td class='text-center'>" + response[i].Nombre_Hora + "</td>";

              xHorarioPintoHora = 0;
              for (var x = 0; x < iTotalRegistrosB; x++) {
                if (response[i].Nombre_Hora == response_data[x].Nombre_Hora) {
                  if (xHorarioPintoHora > 0)
                    tr_body += "<td class='text-center'></td>";

                  if (response_data[x].ID_Dia_Semana == 1) {//1=Lunes                  
                    tr_body += "<td class='text-center' style='background-color: #" + response_data[x].No_Html_Color + "'>" + response_data[x].No_Contacto + " Edad: " + response_data[x].Nu_Edad;
                    tr_body += "<br><div style='border:2px; border-color: #" + response_data[x].No_Class_Color_Tipo_Clase + "; border-style: solid; background-color: #" + response_data[x].No_Class_Color_Tipo_Clase + "; margin-top: 5%;'>" + response_data[x].No_Familia + "</div>";
                    tr_body += "<label style='font-size:15px; margin-top: 4%;'>Salon: " + response_data[x].No_Salon + "</label>";
                    tr_body += "</td>";
                    xHorarioPintoHora = 1;
                  }
                  
                  if (response_data[x].ID_Dia_Semana == 2) {
                    tr_body += "<td class='text-center'></td>";
                    tr_body += "<td class='text-center' style='background-color: #" + response_data[x].No_Html_Color + "'>" + response_data[x].No_Contacto + " Edad: " + response_data[x].Nu_Edad;
                    tr_body += "<br><div style='border:2px; border-color: #" + response_data[x].No_Class_Color_Tipo_Clase + "; border-style: solid; background-color: #" + response_data[x].No_Class_Color_Tipo_Clase + "; margin-top: 5%;'>" + response_data[x].No_Familia + "</div>";
                    tr_body += "<label style='font-size:15px; margin-top: 4%;'>Salon: " + response_data[x].No_Salon + "</label>";
                    tr_body += "</td>";
                    xHorarioPintoHora = 1;
                  }

                  if (response_data[x].ID_Dia_Semana == 3) {
                    tr_body += "<td class='text-center'></td>";
                    tr_body += "<td class='text-center'></td>";
                    tr_body += "<td class='text-center' style='background-color: #" + response_data[x].No_Html_Color + "'>" + response_data[x].No_Contacto + " Edad: " + response_data[x].Nu_Edad;
                    tr_body += "<br><div style='border:2px; border-color: #" + response_data[x].No_Class_Color_Tipo_Clase + "; border-style: solid; background-color: #" + response_data[x].No_Class_Color_Tipo_Clase + "; margin-top: 5%;'>" + response_data[x].No_Familia + "</div>";
                    tr_body += "<label style='font-size:15px; margin-top: 4%;'>Salon: " + response_data[x].No_Salon + "</label>";
                    tr_body += "</td>";
                    xHorarioPintoHora = 1;
                  }

                  if (response_data[x].ID_Dia_Semana == 4) {
                    tr_body += "<td class='text-center'></td>";
                    tr_body += "<td class='text-center'></td>";
                    tr_body += "<td class='text-center'></td>";
                    tr_body += "<td class='text-center' style='background-color: #" + response_data[x].No_Html_Color + "'>" + response_data[x].No_Contacto + " Edad: " + response_data[x].Nu_Edad;
                    tr_body += "<br><div style='border:2px; border-color: #" + response_data[x].No_Class_Color_Tipo_Clase + "; border-style: solid; background-color: #" + response_data[x].No_Class_Color_Tipo_Clase + "; margin-top: 5%;'>" + response_data[x].No_Familia + "</div>";
                    tr_body += "<label style='font-size:15px; margin-top: 4%;'>Salon: " + response_data[x].No_Salon + "</label>";
                    tr_body += "</td>";
                    xHorarioPintoHora = 1;
                  }

                  if (response_data[x].ID_Dia_Semana == 5) {
                    tr_body += "<td class='text-center'></td>";
                    tr_body += "<td class='text-center'></td>";
                    tr_body += "<td class='text-center'></td>";
                    tr_body += "<td class='text-center'></td>";
                    tr_body += "<td class='text-center' style='background-color: #" + response_data[x].No_Html_Color + "'>" + response_data[x].No_Contacto + " Edad: " + response_data[x].Nu_Edad;
                    tr_body += "<br><div style='border:2px; border-color: #" + response_data[x].No_Class_Color_Tipo_Clase + "; border-style: solid; background-color: #" + response_data[x].No_Class_Color_Tipo_Clase + "; margin-top: 5%;'>" + response_data[x].No_Familia + "</div>";
                    tr_body += "<label style='font-size:15px; margin-top: 4%;'>Salon: " + response_data[x].No_Salon + "</label>";
                    tr_body += "</td>";
                    xHorarioPintoHora = 1;
                  }

                  if (response_data[x].ID_Dia_Semana == 6) {
                    tr_body += "<td class='text-center'></td>";
                    tr_body += "<td class='text-center'></td>";
                    tr_body += "<td class='text-center'></td>";
                    tr_body += "<td class='text-center'></td>";
                    tr_body += "<td class='text-center'></td>";
                    tr_body += "<td class='text-center'></td>";
                    tr_body += "<td class='text-center' style='background-color: #" + response_data[x].No_Html_Color + "'>" + response_data[x].No_Contacto + " Edad: " + response_data[x].Nu_Edad;
                    tr_body += "<br><div style='border:2px; border-color: #" + response_data[x].No_Class_Color_Tipo_Clase + "; border-style: solid; background-color: #" + response_data[x].No_Class_Color_Tipo_Clase + "; margin-top: 5%;'>" + response_data[x].No_Familia + "</div>";
                    tr_body += "<label style='font-size:15px; margin-top: 4%;'>Salon: " + response_data[x].No_Salon + "</label>";
                    tr_body += "</td>";
                    xHorarioPintoHora = 1;
                  }

                  if (response_data[x].ID_Dia_Semana == 7) {
                    tr_body += "<td class='text-center'></td>";
                    tr_body += "<td class='text-center'></td>";
                    tr_body += "<td class='text-center'></td>";
                    tr_body += "<td class='text-center'></td>";
                    tr_body += "<td class='text-center'></td>";
                    tr_body += "<td class='text-center'></td>";
                    tr_body += "<td class='text-center' style='background-color: #" + response_data[x].No_Html_Color + "'>" + response_data[x].No_Contacto + " Edad: " + response_data[x].Nu_Edad;
                    tr_body += "<br><div style='border:2px; border-color: #" + response_data[x].No_Class_Color_Tipo_Clase + "; border-style: solid; background-color: #" + response_data[x].No_Class_Color_Tipo_Clase + "; margin-top: 5%;'>" + response_data[x].No_Familia + "</div>";
                    tr_body += "<label style='font-size:15px; margin-top: 4%;'>Salon: " + response_data[x].No_Salon + "</label>";
                    tr_body += "</td>";
                    xHorarioPintoHora = 1;
                  }
                  tr_body += "</tr>";
                }
              }              
            }
          } else {
            if( response.sMessageSQL !== undefined ) {
              console.log(response.sMessageSQL);
            }
            tr_body +=
            "<tr>"
              +"<td colspan='15' class='text-center'>" + response.sMessage + "</td>"
            + "</tr>";
          } // ./ if arrData
          
          $( '#div-ventas_x_familia' ).show();
          $( '#table-ventas_x_familia > tbody' ).append(tr_body);
          
          $( '#btn-html_ventas_x_familia' ).text('');
          $( '#btn-html_ventas_x_familia' ).append( '<i class="fa fa-search"></i> Buscar' );
          $( '#btn-html_ventas_x_familia' ).attr('disabled', false);
        }, 'JSON')
        .fail(function(jqXHR, textStatus, errorThrown) {
          $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
          
          $( '#modal-message' ).modal('show');
          $( '.modal-message' ).addClass( 'modal-danger' );
          $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
          setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
          
          //Message for developer
          console.log(jqXHR.responseText);
          
          $( '#btn-html_ventas_x_familia' ).text('');
          $( '#btn-html_ventas_x_familia' ).append( '<i class="fa fa-search"></i> Buscar' );
          $( '#btn-html_ventas_x_familia' ).attr('disabled', false);
        });
      } else if ($(this).data('type') == 'pdf') {
        $( '#btn-pdf_ventas_x_familia' ).text('');
        $( '#btn-pdf_ventas_x_familia' ).attr('disabled', true);
        $( '#btn-pdf_ventas_x_familia' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
            
        url = base_url + 'Escuela/ReporteMatriculaAlumnoController/sendReportePDF/' + ID_Sede_Musica + '/' + ID_Salon;
        window.open(url,'_blank');
        
        $( '#btn-pdf_ventas_x_familia' ).text('');
        $( '#btn-pdf_ventas_x_familia' ).append( '<i class="fa fa-file-pdf-o color_icon_pdf"></i> PDF' );
        $( '#btn-pdf_ventas_x_familia' ).attr('disabled', false);
      } else if ($(this).data('type') == 'excel') {
        $( '#btn-excel_ventas_x_familia' ).text('');
        $( '#btn-excel_ventas_x_familia' ).attr('disabled', true);
        $( '#btn-excel_ventas_x_familia' ).append( 'Cargando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
        
        url = base_url + 'Escuela/ReporteMatriculaAlumnoController/sendReporteEXCEL/' + ID_Sede_Musica + '/' + ID_Salon;
        window.open(url,'_blank');
        
        $( '#btn-excel_ventas_x_familia' ).text('');
        $( '#btn-excel_ventas_x_familia' ).append( '<i class="fa fa-file-excel-o color_icon_excel"></i> Excel' );
        $( '#btn-excel_ventas_x_familia' ).attr('disabled', false);
      }// ./ if
    }
  })//./ btn
})