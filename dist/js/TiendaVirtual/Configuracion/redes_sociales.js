var url;
var table_medio_pago;

$(function () {
  $('.select2').select2();

  $(document).keyup(function (event) {
    if (event.which == 27) {//ESC
      $("#modal-MedioPago").modal('hide');
    }
  });

  url = base_url + 'TiendaVirtual/Configuracion/RedesSocialesTiendaVirtualController/ajax_list';
  table_medio_pago = $('#table-MedioPago').DataTable({
    'dom': '<"top">frt<"bottom"><"clear">',
    'searching': false,
    'bStateSave': true,
    'processing': true,
    'serverSide': true,
    'info': true,
    'autoWidth': false,
    'pagingType': 'full_numbers',
    'oLanguage': {
      'sInfo': 'Mostrando (_START_ - _END_) total de registros _TOTAL_',
      'sLengthMenu': '_MENU_',
      'sSearch': 'Buscar por: ',
      'sSearchPlaceholder': 'UPC / Nombre',
      'sZeroRecords': 'No se encontraron registros',
      'sInfoEmpty': 'No hay registros',
      'sLoadingRecords': 'Cargando...',
      'sProcessing': 'Procesando...',
      'oPaginate': {
        'sFirst': '<<',
        'sLast': '>>',
        'sPrevious': '<',
        'sNext': '>',
      },
    },
    'order': [],
    'ajax': {
      'url': url,
      'type': 'POST',
      'dataType': 'JSON',
      'data': function (data) {
        data.filtro_empresa = $('#cbo-filtro_empresa').val(),
          data.Filtros_MedioPago = $('#cbo-Filtros_MedioPago').val(),
          data.Global_Filter = $('#txt-Global_Filter').val();
      },
    },
    'columnDefs': [{
      'className': 'text-center',
      'targets': 'no-sort',
      'orderable': false,
    },],
    'lengthMenu': [[10, 100, 1000, -1], [10, 100, 1000, "Todos"]],
  });

  $('.dataTables_length').addClass('col-md-3');
  $('.dataTables_paginate').addClass('col-md-9');

  $('#txt-Global_Filter').keyup(function () {
    table_medio_pago.search($(this).val()).draw();
  });

  $('#form-MedioPago').validate({
    rules: {
    },
    messages: {
    },
    errorPlacement: function (error, element) {
      $(element).closest('.form-group').find('.help-block').html(error.html());
    },
    highlight: function (element) {
      $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
    },
    unhighlight: function (element, errorClass, validClass) {
      $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
      $(element).closest('.form-group').find('.help-block').html('');
    },
    submitHandler: form_MedioPago
  });

  $('#cbo-filtro_empresa').html('<option value="0" selected="selected">- Todas -</option>');

  url = base_url + 'HelperController/getEmpresas';
  $.post(url, function (response) {
    $('#cbo-filtro_empresa').html('<option value="0" selected="selected">- Todas -</option>');
    for (var i = 0; i < response.length; i++)
      $('#cbo-filtro_empresa').append('<option value="' + response[i].ID_Empresa + '">' + response[i].No_Empresa + '</option>');
  }, 'JSON');

  $('#cbo-filtro_empresa').change(function () {
    table_medio_pago.search($(this).val()).draw();
  });


  $(".input-replace_red_social").keyup(function(){
    var nombre_red_social = $(this).val().trim();
    var url_tienda_red_social = $(this).data('url') + nombre_red_social;
    if (nombre_red_social.length > 0) {
      $('#' + $(this).data('id_url')).text(url_tienda_red_social);
      $("#" + $(this).data('id_url')).attr("href", url_tienda_red_social);
    } else {
      $('#' + $(this).data('id_url')).text('');
      $("#" + $(this).data('id_url')).attr("href", '');
    }
  })
})

function verMedioPago(ID) {
  $('#form-MedioPago')[0].reset();
  $('.form-group').removeClass('has-error');
  $('.form-group').removeClass('has-success');
  $('.help-block').empty();

  $('#modal-loader').modal('show');
  
  $('#a-facebook').text('');
  $('#a-instagram').text('');
  $('#a-tiktok').text('');
  $('#a-youtube').text('');
  $('#a-twitter').text('');
  $('#a-linkedin').text('');
  $('#a-pinterest').text('');

  url = base_url + 'TiendaVirtual/Configuracion/RedesSocialesTiendaVirtualController/ajax_edit/' + ID;
  $.ajax({
    url: url,
    type: "GET",
    dataType: "JSON",
    success: function (response) {
      $('#modal-loader').modal('hide');

      $('#modal-MedioPago').modal('show');
      $('.modal-title').text('Redes Sociales');

      $('[name="EID_Empresa"]').val(response.ID_Empresa);
      $('[name="EID_Configuracion"]').val(response.ID_Configuracion);

      var selected, url_tienda_red_social;
      url = base_url + 'HelperController/getEmpresas';
      $.post(url, function (responseEmpresa) {
        $('#cbo-Empresas').html('');
        for (var i = 0; i < responseEmpresa.length; i++) {
          selected = '';
          if (response.ID_Empresa == responseEmpresa[i].ID_Empresa)
            selected = 'selected="selected"';
          $('#cbo-Empresas').append('<option value="' + responseEmpresa[i].ID_Empresa + '" ' + selected + '>' + responseEmpresa[i].No_Empresa + '</option>');
        }
      }, 'JSON');

      $('[name="No_Red_Social_Facebook"]').val(response.No_Red_Social_Facebook);
      if (response.No_Red_Social_Facebook != null && response.No_Red_Social_Facebook !='') {
        url_tienda_red_social = "https://www.facebook.com/" + response.No_Red_Social_Facebook;
        $('#a-facebook').text(url_tienda_red_social);
        $("#a-facebook").attr("href", url_tienda_red_social);
      }

      $('[name="No_Red_Social_Instagram"]').val(response.No_Red_Social_Instagram);
      if (response.No_Red_Social_Instagram != null && response.No_Red_Social_Instagram !='') {
        url_tienda_red_social = "https://www.instagram.com/" + response.No_Red_Social_Instagram;
        $('#a-instagram').text(url_tienda_red_social);
        $("#a-instagram").attr("href", url_tienda_red_social);
      }

      $('[name="No_Red_Social_Tiktok"]').val(response.No_Red_Social_Tiktok);
      if (response.No_Red_Social_Tiktok != null && response.No_Red_Social_Tiktok !='') {
        url_tienda_red_social = "https://www.tiktok.com/" + response.No_Red_Social_Tiktok;
        $('#a-tiktok').text(url_tienda_red_social);
        $("#a-tiktok").attr("href", url_tienda_red_social);
      }

      $('[name="No_Red_Social_Youtube"]').val(response.No_Red_Social_Youtube);
      if (response.No_Red_Social_Youtube != null && response.No_Red_Social_Youtube !='') {
        url_tienda_red_social = "https://www.youtube.com/channel/" + response.No_Red_Social_Youtube;
        $('#a-youtube').text(url_tienda_red_social);
        $("#a-youtube").attr("href", url_tienda_red_social);
      }

      $('[name="No_Red_Social_Linkedin"]').val(response.No_Red_Social_Linkedin);
      if (response.No_Red_Social_Linkedin != null && response.No_Red_Social_Linkedin !='') {
        url_tienda_red_social = "https://www.linkedin.com/company/" + response.No_Red_Social_Linkedin;
        $('#a-linkedin').text(url_tienda_red_social);
        $("#a-linkedin").attr("href", url_tienda_red_social);
      }

      $('[name="No_Red_Social_Twitter"]').val(response.No_Red_Social_Twitter);
      if (response.No_Red_Social_Twitter != null && response.No_Red_Social_Twitter !='') {
        url_tienda_red_social = "https://www.twitter.com/" + response.No_Red_Social_Twitter;
        $('#a-twitter').text(url_tienda_red_social);
        $("#a-twitter").attr("href", url_tienda_red_social);
      }

      $('[name="No_Red_Social_Pinterest"]').val(response.No_Red_Social_Pinterest);
      if (response.No_Red_Social_Pinterest != null && response.No_Red_Social_Pinterest !='') {
        url_tienda_red_social = "https://www.pinterest.com/" + response.No_Red_Social_Pinterest;
        $('#a-pinterest').text(url_tienda_red_social);
        $("#a-pinterest").attr("href", url_tienda_red_social);
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      $('#modal-loader').modal('hide');
      $('.modal-message').removeClass('modal-danger modal-warning modal-success');

      $('#modal-message').modal('show');
      $('.modal-message').addClass('modal-danger');
      $('.modal-title-message').text(textStatus + ' [' + jqXHR.status + ']: ' + errorThrown);
      setTimeout(function () { $('#modal-message').modal('hide'); }, 1700);

      //Message for developer
      console.log(jqXHR.responseText);
    }
  });
}

function form_MedioPago() {
  $('#btn-save').text('');
  $('#btn-save').attr('disabled', true);
  $('#btn-save').append('Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>');

  $('#modal-loader').modal('show');

  url = base_url + 'TiendaVirtual/Configuracion/RedesSocialesTiendaVirtualController/crudMedioPago';
  $.ajax({
    type: 'POST',
    dataType: 'JSON',
    url: url,
    data: $('#form-MedioPago').serialize(),
    success: function (response) {
      $('#modal-loader').modal('hide');

      $('.modal-message').removeClass('modal-danger modal-warning modal-success');
      $('#modal-message').modal('show');

      if (response.status == 'success') {
        $('#modal-MedioPago').modal('hide');
        $('.modal-message').addClass(response.style_modal);
        $('.modal-title-message').text(response.message);
        setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
        reload_table_medio_pago();
      } else {
        $('.modal-message').addClass(response.style_modal);
        $('.modal-title-message').text(response.message);
        setTimeout(function () { $('#modal-message').modal('hide'); }, 1200);
      }

      $('#btn-save').text('');
      $('#btn-save').append('Guardar');
      $('#btn-save').attr('disabled', false);
    },
    error: function (jqXHR, textStatus, errorThrown) {
      $('#modal-loader').modal('hide');
      $('.modal-message').removeClass('modal-danger modal-warning modal-success');

      $('#modal-message').modal('show');
      $('.modal-message').addClass('modal-danger');
      $('.modal-title-message').text(textStatus + ' [' + jqXHR.status + ']: ' + errorThrown);
      setTimeout(function () { $('#modal-message').modal('hide'); }, 1700);

      //Message for developer
      console.log(jqXHR.responseText);

      $('#btn-save').text('');
      $('#btn-save').append('Guardar');
      $('#btn-save').attr('disabled', false);
    }
  });
}

function reload_table_medio_pago() {
  table_medio_pago.ajax.reload(null, false);
}