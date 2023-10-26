var url;
var table_medio_pago;

$(function () {
  $('.select2').select2();
  
  $('.date-picker-inicio').inputmask('dd/mm/yyyy', { 'placeholder': 'dd/mm/yyyy' });
  $('.date-picker-fin').inputmask('dd/mm/yyyy', { 'placeholder': 'dd/mm/yyyy' });

  var startDate = fYear + '-' + fMonth + '-' + (parseInt(fDay) + 1);

  $('.date-picker-inicio').datepicker({
    autoclose: true,
    startDate: new Date(startDate),
    todayHighlight: true
  });

  $('.date-picker-fin').datepicker({
    autoclose: true,
    startDate: new Date(startDate),
    todayHighlight: true
  });

  $('.date-picker-inicio').datepicker({}).on('changeDate', function (selected) {
    var minDate = new Date(selected.date.valueOf());
    $('.date-picker-fin').datepicker('setStartDate', minDate);
  });

  $(document).keyup(function (event) {
    if (event.which == 27) {//ESC
      $("#modal-CuponDescuento").modal('hide');
    }
  });

  url = base_url + 'TiendaVirtual/Configuracion/CuponDescuentoTiendaVirtualController/ajax_list';
  table_medio_pago = $('#table-CuponDescuento').DataTable({
    'dom': '<"top">frt<"bottom"lp><"clear">',
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
          data.Filtros_CuponDescuento = $('#cbo-Filtros_CuponDescuento').val(),
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

  $('#form-CuponDescuento').validate({
    rules: {
      No_Codigo_Cupon_Descuento: {
        required: true,
      },
      Nu_Tipo_Cupon_Descuento: {
        required: true,
      },
      Ss_Valor_Cupon_Descuento: {
        required: true,
      },
    },
    messages: {
      No_Codigo_Cupon_Descuento: {
        required: "Ingresar código"
      },
      Nu_Tipo_Cupon_Descuento: {
        required: "Seleccionar tipo de cupón"
      },
      Ss_Valor_Cupon_Descuento: {
        required: "Ingresar valor"
      },
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
    submitHandler: form_CuponDescuento
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
})

function agregarCuponDescuento() {
  $('#form-CuponDescuento')[0].reset();
  $('.form-group').removeClass('has-error');
  $('.form-group').removeClass('has-success');
  $('.help-block').empty();

  $('#modal-CuponDescuento').modal('show');
  $('.modal-title').text('Nuevo Cupón');

  $('[name="EID_Cupon_Descuento"]').val('');
  $('[name="ENo_Codigo_Cupon_Descuento"]').val('');

  url = base_url + 'HelperController/getEmpresas';
  $.post(url, function (response) {
    $('#cbo-Empresas').html('<option value="0" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++)
      $('#cbo-Empresas').append('<option value="' + response[i].ID_Empresa + '">' + response[i].No_Empresa + '</option>');
  }, 'JSON');

  $('#cbo-tipo_cupon_descuento').html('<option value="">- Seleccionar -</option>');
  $('#cbo-tipo_cupon_descuento').append('<option value="1">Descuento x Importe</option>');
  $('#cbo-tipo_cupon_descuento').append('<option value="2">Descuento x Porcentaje</option>');

  //CSS
  $("#No_Codigo_Cupon_Descuento").css("background-color", "");
  $("#No_Codigo_Cupon_Descuento").css("pointer-events", "");
}

function verCuponDescuento(ID) {
  $('#form-CuponDescuento')[0].reset();
  $('.form-group').removeClass('has-error');
  $('.form-group').removeClass('has-success');
  $('.help-block').empty();

  $('#modal-loader').modal('show');

  //CSS
  $("#No_Codigo_Cupon_Descuento").css("background-color", "#d2d6de");
  $("#No_Codigo_Cupon_Descuento").css("pointer-events", "none");

  url = base_url + 'TiendaVirtual/Configuracion/CuponDescuentoTiendaVirtualController/ajax_edit/' + ID;
  $.ajax({
    url: url,
    type: "GET",
    dataType: "JSON",
    success: function (response) {
      $('#modal-loader').modal('hide');

      $('#modal-CuponDescuento').modal('show');
      $('.modal-title').text('Modificar Cupón');

      $('[name="EID_Cupon_Descuento"]').val(response.ID_Cupon_Descuento);
      $('[name="ENo_Codigo_Cupon_Descuento"]').val(response.No_Codigo_Cupon_Descuento);

      var selected;
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

      $('[name="No_Codigo_Cupon_Descuento"]').val(response.No_Codigo_Cupon_Descuento);
      $('[name="Txt_Cupon_Descuento"]').val(response.Txt_Cupon_Descuento);

      $('#cbo-tipo_cupon_descuento').html('<option value="0">- Seleccionar -</option>');
      selected = '';
      if (response.Nu_Tipo_Cupon_Descuento == '1')
        selected = 'selected="selected"';
      $('#cbo-tipo_cupon_descuento').html('<option value="1" ' + selected + '>Descuento x Importe</option>');

      selected = '';
      if (response.Nu_Tipo_Cupon_Descuento == '2')
        selected = 'selected="selected"';
      $('#cbo-tipo_cupon_descuento').append('<option value="2" ' + selected + '>Descuento x Porcentaje</option>');

      $('[name="Ss_Valor_Cupon_Descuento"]').val(response.Ss_Valor_Cupon_Descuento);
      $('[name="Ss_Gasto_Minimo_Compra"]').val(response.Ss_Gasto_Minimo_Compra);
      $('[name="Fe_Inicio"]').val(ParseDateString(response.Fe_Inicio, 6, '-'));

      var Fe_Inicio = response.Fe_Inicio.split('-');
      $('[name="Fe_Vencimiento"]').datepicker('setStartDate', new Date(Fe_Inicio[0] + "/" + Fe_Inicio[1] + "/" + Fe_Inicio[2]));
      $('[name="Fe_Vencimiento"]').val(ParseDateString(response.Fe_Vencimiento, 6, '-'));
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

function form_CuponDescuento() {
  $('#btn-save').text('');
  $('#btn-save').attr('disabled', true);
  $('#btn-save').append('Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>');

  $('#modal-loader').modal('show');

  url = base_url + 'TiendaVirtual/Configuracion/CuponDescuentoTiendaVirtualController/crudCuponDescuento';
  $.ajax({
    type: 'POST',
    dataType: 'JSON',
    url: url,
    data: $('#form-CuponDescuento').serialize(),
    success: function (response) {
      $('#modal-loader').modal('hide');

      $('.modal-message').removeClass('modal-danger modal-warning modal-success');
      $('#modal-message').modal('show');

      if (response.status == 'success') {
        $('#modal-CuponDescuento').modal('hide');
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
      $('#btn-save').append('<span class="fa fa-save"></span> Guardar');
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
      $('#btn-save').append('<span class="fa fa-save"></span> Guardar');
      $('#btn-save').attr('disabled', false);
    }
  });
}

function reload_table_medio_pago() {
  table_medio_pago.ajax.reload(null, false);
}