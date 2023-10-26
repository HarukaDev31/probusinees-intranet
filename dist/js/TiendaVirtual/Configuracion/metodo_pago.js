var url;
var table_medio_pago, table_medio_pago_cuentas_bancarias;

$(function () {
  $('.select2').select2();

  $(document).keyup(function (event) {
    if (event.which == 27) {//ESC
      $("#modal-MedioPago").modal('hide');
    }
  });

	$(".toggle-password").click(function() {
		$(this).toggleClass("fa-eye fa-eye-slash");
    var $pwd = $(".pwd");
    if ($pwd.attr('type') == 'password') {
      $pwd.attr('type', 'text');
    } else {
      $pwd.attr('type', 'password');
    }
  });

  url = base_url + 'TiendaVirtual/Configuracion/MetodosPagoTiendaVirtualController/ajax_list';
  table_medio_pago = $('#table-MedioPago').DataTable({
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
        data.filtro_empresa = $('#cbo-filtro_empresa').val();
      },
    },
    'columnDefs': [{
      'className': 'text-center',
      'targets': 'no-sort',
      'orderable': false,
    },{
      'className': 'text-left',
      'targets': 'sort_left',
      'orderable': false,
    },],
    'lengthMenu': [[10, 100, 1000, -1], [10, 100, 1000, "Todos"]],
  });

  $('.dataTables_length').addClass('col-md-3');
  $('.dataTables_paginate').addClass('col-md-9');

  $('#form-MedioPago').validate({
    rules: {
      No_Medio_Pago_Tienda_Virtual: {
        required: true
      },
      No_Signo: {
        required: true
      },
      Nu_Sunat_Codigo: {
        required: true,
      },
      Nu_Valor_FE: {
        required: true,
      },
    },
    messages: {
      No_Medio_Pago_Tienda_Virtual: {
        required: "Ingresar nombre",
      },
      No_Signo: {
        required: "Ingresar signo",
      },
      Nu_Sunat_Codigo: {
        required: "Ingresar codigo",
      },
      Nu_Valor_FE: {
        required: "Ingresar del 1 al 3",
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

  /* NRO. CUENTAS BANCARIAS */
  url = base_url + 'TiendaVirtual/Configuracion/MetodosPagoTiendaVirtualController/ajax_list_cuentas_bancarias';
  table_medio_pago_cuentas_bancarias = $('#table-MedioPago-CuentasBancarias').DataTable({
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
        data.filtro_empresa = $('#cbo-filtro_empresa').val();
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

  $('#form-MedioPago-CuentasBancarias').validate({
    rules: {
      ID_Tipo_Medio_Pago: {
        required: true
      },
      ID_Banco: {
        required: true
      },
      Nu_Tipo_Cuenta: {
        required: true
      },
      ID_Moneda: {
        required: true,
      },
      No_Titular_Cuenta: {
        required: true,
      },
      No_Cuenta_Bancaria: {
        required: true,
      },
    },
    messages: {
      ID_Tipo_Medio_Pago: {
        required: "Seleccionar Banco",
      },
      ID_Banco: {
        required: "Seleccionar Banco",
      },
      Nu_Tipo_Cuenta: {
        required: "Seleccionar tipo",
      },
      ID_Moneda: {
        required: "Seleccionar Moneda",
      },
      No_Titular_Cuenta: {
        required: "Ingresar Titular",
      },
      No_Cuenta_Bancaria: {
        required: "Ingresar Número Cuenta",
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
    submitHandler: form_MedioPago_cuentas_bancarias
  });

  $('#cbo-banco_tipo_medio_pago').change(function () {
    $("#cbo-banco option").filter(function () {
      //may want to use $.trim in here
      return $(this).text().trim() == $('#cbo-banco_tipo_medio_pago :selected').text();
    }).prop('selected', true);
  });
  /* FIN NRO. CUENTAS BANCARIAS */
})

function agregarMedioPago() {
  $('#form-MedioPago')[0].reset();
  $('.form-group').removeClass('has-error');
  $('.form-group').removeClass('has-success');

  $('.help-block').empty();

  $('#modal-MedioPago').modal('show');

  $('.modal-title').text('Nuevo Medio Pago');

  $('[name="EID_Empresa"]').val('');
  $('[name="EID_Medio_Pago"]').val('');
  $('[name="ENo_Medio_Pago_Tienda_Virtual"]').val('');

  $('#modal-loader').modal('show');
  url = base_url + 'HelperController/getEmpresas';
  $.post(url, function (response) {
    $('#cbo-Empresas').html('<option value="0" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++)
      $('#cbo-Empresas').append('<option value="' + response[i].ID_Empresa + '">' + response[i].No_Empresa + '</option>');
    $('#modal-loader').modal('hide');
  }, 'JSON');

  $('#cbo-dinero_caja_pv').html('<option value="0">Si</option>');
  $('#cbo-dinero_caja_pv').append('<option value="1">No</option>');

  //$('#cbo-tipo_forma_pago').html('<option value="1">Pago por WhatsApp</option>');
  $('#cbo-tipo_forma_pago').append('<option value="2">Pago Online - Pasarela</option>');
  $('#cbo-tipo_forma_pago').append('<option value="3">Pago Contra Entrega Efectivo</option>');
  $('#cbo-tipo_forma_pago').append('<option value="4">Pago por Transferencia</option>');

  $('#cbo-cierre_venta_pago').html('<option value="1">Por WhatsApp</option>');
  $('#cbo-cierre_venta_pago').append('<option value="2">Por Web</option>');

  $('.div-Estado').hide();
  $('#cbo-Estado').html('<option value="1">Activo</option>');
}

function verMedioPago(ID) {
  $('#form-MedioPago')[0].reset();
  $('.form-group').removeClass('has-error');
  $('.form-group').removeClass('has-success');
  $('.help-block').empty();

  $('#modal-loader').modal('show');

  url = base_url + 'TiendaVirtual/Configuracion/MetodosPagoTiendaVirtualController/ajax_edit/' + ID;
  $.ajax({
    url: url,
    type: "GET",
    dataType: "JSON",
    success: function (response) {
      $('#modal-loader').modal('hide');

      $('#modal-MedioPago').modal('show');
      $('.modal-title').text('Modifcar Medio Pago');

      $('[name="EID_Empresa"]').val(response.ID_Empresa);
      $('[name="EID_Medio_Pago"]').val(response.ID_Medio_Pago);
      $('[name="ENo_Medio_Pago_Tienda_Virtual"]').val(response.No_Medio_Pago_Tienda_Virtual);

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

      $('[name="No_Medio_Pago"]').val(response.No_Medio_Pago);
      $('[name="No_Medio_Pago_Tienda_Virtual"]').val(response.No_Medio_Pago_Tienda_Virtual);

      $('[name="Txt_Medio_Pago"]').val(response.Txt_Medio_Pago);
      $('[name="No_Codigo_Sunat_FE"]').val(response.No_Codigo_Sunat_FE);
      $('[name="No_Codigo_Sunat_PLE"]').val(response.No_Codigo_Sunat_PLE);
      $('[name="Nu_Tipo"]').val(response.Nu_Tipo);

      var selected;
      $('#cbo-dinero_caja_pv').html('');
      for (var i = 0; i < 2; i++) {
        selected = '';
        if (response.Nu_Tipo_Caja == i)
          selected = 'selected="selected"';
        $('#cbo-dinero_caja_pv').append('<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Si' : 'No') + '</option>');
      }

      $('[name="Nu_Orden"]').val(response.Nu_Orden);

      var selected_whatsapp = '', selected_pasarela = '', selected_contraentrega = '', selected_deposito = '';
      /*
      if (response.Nu_Tipo_Forma_Pago_Lae_Shop == 1)
        selected_whatsapp = 'selected="selected"';
        */
      if (response.Nu_Tipo_Forma_Pago_Lae_Shop == 2)
        selected_pasarela = 'selected="selected"';
      if (response.Nu_Tipo_Forma_Pago_Lae_Shop == 3)
        selected_contraentrega = 'selected="selected"';
      if (response.Nu_Tipo_Forma_Pago_Lae_Shop == 4)
        selected_deposito = 'selected="selected"';

      $('#cbo-tipo_forma_pago').html('<option value="0">- Seleccionar -</option>');
      //$('#cbo-tipo_forma_pago').append('<option value="1" ' + selected_whatsapp + '>Pago por WhatsApp</option>');
      $('#cbo-tipo_forma_pago').append('<option value="2" ' + selected_pasarela + '>Pago Online - MERCADO PAGO</option>');
      $('#cbo-tipo_forma_pago').append('<option value="3" ' + selected_contraentrega + '>Pago Contra entrega Efectivo</option>');
      $('#cbo-tipo_forma_pago').append('<option value="4" ' + selected_deposito + '>Pago por Transferencia</option>');

      var selected_cierre_whatsapp = '', selected_cierre_web = '';
      if (response.Nu_Cierre_Venta_Pago_Lae_Shop == 1)
        selected_cierre_whatsapp = 'selected="selected"';
      if (response.Nu_Cierre_Venta_Pago_Lae_Shop == 2)
        selected_cierre_web = 'selected="selected"';
      $('#cbo-cierre_venta_pago').html('<option value="1" ' + selected_cierre_whatsapp + '>Por WhatsApp</option>');
      $('#cbo-cierre_venta_pago').append('<option value="2" ' + selected_cierre_web + '>Por Web</option>');

      $('.div-Estado').show();
      $('#cbo-Estado').html('');
      for (var i = 0; i < 2; i++) {
        selected = '';
        if (response.Nu_Activar_Medio_Pago_Lae_Shop == i)
          selected = 'selected="selected"';
        $('#cbo-Estado').append('<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Inactivo' : 'Activo') + '</option>');
      }

      //MERCADO PAGO
      $('.div-mercado_pago').hide();
      $('[name="Txt_Pasarela_Pago_Key"]').val('');
      $('[name="Txt_Pasarela_Pago_Token"]').val('');
      if (response.Nu_Tipo_Forma_Pago_Lae_Shop == 2) {
        $('.div-mercado_pago').show();

        $('[name="Txt_Pasarela_Pago_Key"]').val(response.Txt_Pasarela_Pago_Key);
        $('[name="Txt_Pasarela_Pago_Token"]').val(response.Txt_Pasarela_Pago_Token);
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

  url = base_url + 'TiendaVirtual/Configuracion/MetodosPagoTiendaVirtualController/crudMedioPago';
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

function eliminarMedioPago(ID) {
  var $modal_delete = $('#modal-message-delete');
  $modal_delete.modal('show');

  $('#btn-cancel-delete').off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $('#btn-save-delete').off('click').click(function () {
    $('#modal-loader').modal('show');

    url = base_url + 'TiendaVirtual/Configuracion/MetodosPagoTiendaVirtualController/eliminarMedioPago/' + ID;
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
          reload_table_medio_pago();
        } else {
          $('.modal-message').addClass(response.style_modal);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1500);
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        $('#modal-loader').modal('hide');
        $modal_delete.modal('hide');
        $('.modal-message').removeClass('modal-danger modal-warning modal-success');

        $('#modal-message').modal('show');
        $('.modal-message').addClass('modal-danger');
        $('.modal-title-message').text(textStatus + ' [' + jqXHR.status + ']: ' + errorThrown);
        setTimeout(function () { $('#modal-message').modal('hide'); }, 1700);

        //Message for developer
        console.log(jqXHR.responseText);
      },
    });
  });
}

function reload_table_medio_pago() {
  table_medio_pago.ajax.reload(null, false);
}

/* CUENTAS BANCARIAS */
function agregarMedioPago_cuentas_bancarias(ID_Medio_Pago, No_Medio_Pago) {
  $('#form-MedioPago-CuentasBancarias')[0].reset();
  $('.form-group').removeClass('has-error');
  $('.form-group').removeClass('has-success');

  $('.help-block').empty();

  $('#modal-MedioPago-CuentasBancarias').modal('show');

  $('.modal-title').text('Nueva Cuenta Bancaria');

  $('[name="EID_Cuenta_Bancaria"]').val('');
  $('[name="EID_Medio_Pago"]').val(ID_Medio_Pago);
  $('#h4-title-cuenta_bancarias').text(No_Medio_Pago);

  $('#modal-loader').modal('show');
  url = base_url + 'HelperController/getEmpresas';
  $.post(url, function (response) {
    $('#cbo-Empresas').html('<option value="0" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++)
      $('#cbo-Empresas').append('<option value="' + response[i].ID_Empresa + '">' + response[i].No_Empresa + '</option>');
  }, 'JSON');

  $('#cbo-banco_tipo_medio_pago').html('<option value="" selected="selected">- Sin registro -</option>');
  url = base_url + 'HelperController/getTiposTarjetaCredito';
  $.post(url, { ID_Medio_Pago: ID_Medio_Pago }, function (response) {
    if (response.length==1) {
      $('#cbo-banco_tipo_medio_pago').html('<option value="' + response[0].ID_Tipo_Medio_Pago + '">' + response[0].No_Tipo_Medio_Pago + '</option>');
    } else {
      $('#cbo-banco_tipo_medio_pago').html('<option value="" selected="selected">- Seleccionar -</option>');
      for (var i = 0; i < response.length; i++){
        $('#cbo-banco_tipo_medio_pago').append('<option value="' + response[i].ID_Tipo_Medio_Pago + '">' + response[i].No_Tipo_Medio_Pago + '</option>');
      }
    }
    $('#modal-loader').modal('hide');
  }, 'JSON');

  $('.div-moneda').show();
  $('.div-tipo_cuenta').show();
  $('.div-cci').show();
  No_Medio_Pago = No_Medio_Pago.toUpperCase();
  if(No_Medio_Pago.includes("YAPE")){
    $('#cbo-banco').html('<option value="" selected="selected">- Sin registro -</option>');
    url = base_url + 'HelperTiendaVirtualController/getBancos';
    $.post(url, {}, function (response) {
      if (response.sStatus == 'success') {
        var l = response.arrData.length;
        $('#cbo-banco').html('<option value="" selected="selected">- Seleccionar -</option>');
        for (var x = 0; x < l; x++) {
          response.arrData[x].Nombre = response.arrData[x].Nombre.toUpperCase();
          selected = '';
          if (response.arrData[x].Nombre.includes("YAPE"))
            selected = 'selected="selected"';
          $('#cbo-banco').append('<option value="' + response.arrData[x].ID + '" ' + selected + '>' + response.arrData[x].Nombre + '</option>');
        }
      } else {
        if (response.sMessageSQL !== undefined) {
          console.log(response.sMessageSQL);
        }
        $('#modal-message').modal('show');
        $('.modal-message').addClass(response.sClassModal);
        $('.modal-title-message').text(response.sMessage);
        setTimeout(function () { $('#modal-message').modal('hide'); }, 1200);
      }
    }, 'JSON');

    $('#label-nro_cuenta').text('Celular');
    // set moneda cuando sea YAPE
    $('#cbo-moneda').html('<option value="" selected="selected">- Sin registro -</option>');
    url = base_url + 'HelperController/getMonedas';
    $.post(url, function (response) {
      $('#cbo-moneda').html('<option value="" selected="selected">- Seleccionar -</option>');
      for (var i = 0; i < response.length; i++) {
        selected = '';
        if (response[i]['Nu_Sunat_Codigo'] == 'PEN')
          selected = 'selected="selected"';
        $('#cbo-moneda').append('<option value="' + response[i]['ID_Moneda'] + '" data-no_signo="' + response[i]['No_Signo'] + '" ' + selected + '>' + response[i]['No_Moneda'] + '</option>');
      }
    }, 'JSON');
    
    $('#cbo-tipo_cuenta').html('<option value="2">Cuenta Ahorros</option>');

    $('.div-moneda').hide();
    $('.div-tipo_cuenta').hide();
    $('.div-cci').hide();
  } else if(No_Medio_Pago.includes("PLIN")){
    
    $('#cbo-banco').html('<option value="" selected="selected">- Sin registro -</option>');
    url = base_url + 'HelperTiendaVirtualController/getBancos';
    $.post(url, {}, function (response) {
      if (response.sStatus == 'success') {
        var l = response.arrData.length;
        $('#cbo-banco').html('<option value="" selected="selected">- Seleccionar -</option>');
        for (var x = 0; x < l; x++) {
          response.arrData[x].Nombre = response.arrData[x].Nombre.toUpperCase();
          selected = '';
          if (response.arrData[x].Nombre.includes("PLIN"))
            selected = 'selected="selected"';
          $('#cbo-banco').append('<option value="' + response.arrData[x].ID + '" ' + selected + '>' + response.arrData[x].Nombre + '</option>');
        }
      } else {
        if (response.sMessageSQL !== undefined) {
          console.log(response.sMessageSQL);
        }
        $('#modal-message').modal('show');
        $('.modal-message').addClass(response.sClassModal);
        $('.modal-title-message').text(response.sMessage);
        setTimeout(function () { $('#modal-message').modal('hide'); }, 1200);
      }
    }, 'JSON');

    $('#label-nro_cuenta').text('Celular');
    // set moneda cuando sea PLIN
    $('#cbo-moneda').html('<option value="" selected="selected">- Sin registro -</option>');
    url = base_url + 'HelperController/getMonedas';
    $.post(url, function (response) {
      $('#cbo-moneda').html('<option value="" selected="selected">- Seleccionar -</option>');
      for (var i = 0; i < response.length; i++) {
        selected = '';
        if (response[i]['Nu_Sunat_Codigo'] == 'PEN')
          selected = 'selected="selected"';
        $('#cbo-moneda').append('<option value="' + response[i]['ID_Moneda'] + '" data-no_signo="' + response[i]['No_Signo'] + '" ' + selected + '>' + response[i]['No_Moneda'] + '</option>');
      }
    }, 'JSON');
    
    $('#cbo-tipo_cuenta').html('<option value="2">Cuenta Ahorros</option>');

    $('.div-moneda').hide();
    $('.div-tipo_cuenta').hide();
    $('.div-cci').hide();
  } else {//tiene más de un banco
    //tabla de banco
    $('#cbo-banco').html('<option value="" selected="selected">- Sin registro -</option>');
    url = base_url + 'HelperTiendaVirtualController/getBancos';
    $.post(url, {}, function (response) {
      if (response.sStatus == 'success') {
        var l = response.arrData.length;
        $('#cbo-banco').html('<option value="" selected="selected">- Seleccionar -</option>');
        for (var x = 0; x < l; x++) {
          $('#cbo-banco').append('<option value="' + response.arrData[x].ID + '">' + response.arrData[x].Nombre + '</option>');
        }
      } else {
        if (response.sMessageSQL !== undefined) {
          console.log(response.sMessageSQL);
        }
        $('#modal-message').modal('show');
        $('.modal-message').addClass(response.sClassModal);
        $('.modal-title-message').text(response.sMessage);
        setTimeout(function () { $('#modal-message').modal('hide'); }, 1200);
      }
    }, 'JSON');

    $('#label-nro_cuenta').text('Nro. de Cuenta');
    $('#cbo-moneda').html('<option value="" selected="selected">- Sin registro -</option>');
    url = base_url + 'HelperController/getMonedas';
    $.post(url, function (response) {
      $('#cbo-moneda').html('<option value="" selected="selected">- Seleccionar -</option>');
      for (var i = 0; i < response.length; i++)
        $('#cbo-moneda').append('<option value="' + response[i]['ID_Moneda'] + '" data-no_signo="' + response[i]['No_Signo'] + '">' + response[i]['No_Moneda'] + '</option>');
    }, 'JSON');

    $('#cbo-tipo_cuenta').html('<option value="">- Seleccionar -</option>');
    $('#cbo-tipo_cuenta').append('<option value="1">Cuenta Corriente</option>');
    $('#cbo-tipo_cuenta').append('<option value="2">Cuenta Ahorros</option>');
  }

}

function verMedioPago_cuentas_bancarias(ID) {
  $('#form-MedioPago-CuentasBancarias')[0].reset();
  $('.form-group').removeClass('has-error');
  $('.form-group').removeClass('has-success');
  $('.help-block').empty();

  $('#modal-loader').modal('show');

  url = base_url + 'TiendaVirtual/Configuracion/MetodosPagoTiendaVirtualController/ajax_edit_cuentas_bancarias/' + ID;
  $.ajax({
    url: url,
    type: "GET",
    dataType: "JSON",
    success: function (response) {
      $('#modal-loader').modal('hide');

      $('#modal-MedioPago-CuentasBancarias').modal('show');
      $('.modal-title').text('Modifcar Cuenta Bancaria');

      $('[name="EID_Cuenta_Bancaria"]').val(response.ID_Cuenta_Bancaria);
      $('[name="EID_Medio_Pago"]').val(response.ID_Medio_Pago);

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

      $('#cbo-banco_tipo_medio_pago').html('<option value="" selected="selected">- Sin registro -</option>');
      url = base_url + 'HelperController/getTiposTarjetaCredito';
      $.post(url, { ID_Medio_Pago: response.ID_Medio_Pago }, function (responseTipoMedioPago) {
        $('#cbo-banco_tipo_medio_pago').html('<option value="" selected="selected">- Seleccionar -</option>');
        for (var i = 0; i < responseTipoMedioPago.length; i++) {
          selected = '';
          if (response.ID_Tipo_Medio_Pago == responseTipoMedioPago[i].ID_Tipo_Medio_Pago)
            selected = 'selected="selected"';
          $('#cbo-banco_tipo_medio_pago').append('<option value="' + responseTipoMedioPago[i].ID_Tipo_Medio_Pago + '" ' + selected + '>' + responseTipoMedioPago[i].No_Tipo_Medio_Pago + '</option>');
        }
      }, 'JSON');

      $('#cbo-banco').html('Sin registro');
      url = base_url + 'HelperTiendaVirtualController/getBancos';
      $.post(url, function (responseBancos) {
        if (responseBancos.sStatus == 'success') {
          var l = responseBancos.arrData.length;
          $('#cbo-banco').html('');
          for (var x = 0; x < l; x++) {
            selected = '';
            if (response.ID_Banco == responseBancos.arrData[x].ID)
              selected = 'selected="selected"';
            $('#cbo-banco').append('<option value="' + responseBancos.arrData[x].ID + '" ' + selected + '>' + responseBancos.arrData[x].Nombre + '</option>');
          }
        } else {
          if (responseBancos.sMessageSQL !== undefined) {
            console.log(responseBancos.sMessageSQL);
          }
          $('#modal-message').modal('show');
          $('.modal-message').addClass(responseBancos.sClassModal);
          $('.modal-title-message').text(responseBancos.sMessage);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1200);
        }
      }, 'JSON');

      var selected_cuenta_ninguna = 'selected="selected"', selected_cuenta_corriente = '', selected_cuenta_ahorro = '';
      if (response.Nu_Cierre_Venta_Pago_Lae_Shop == 1)
        selected_cuenta_corriente = 'selected="selected"';
      if (response.Nu_Cierre_Venta_Pago_Lae_Shop == 2)
        selected_cuenta_ahorro = 'selected="selected"';
      $('#cbo-tipo_cuenta').html('<option value="" ' + selected_cuenta_ninguna + '>- Seleccionar -</option>');
      $('#cbo-tipo_cuenta').html('<option value="1" ' + selected_cuenta_corriente + '>Cuenta Corriente</option>');
      $('#cbo-tipo_cuenta').append('<option value="2" ' + selected_cuenta_ahorro + '>Cuenta Ahorros</option>');

      $('#cbo-moneda').html('Sin registro');
      url = base_url + 'HelperController/getMonedas';
      $.post(url, function (responseMonedas) {
        for (var i = 0; i < responseMonedas.length; i++) {
          selected = '';
          if (response.ID_Moneda == responseMonedas[i]['ID_Moneda'])
            selected = 'selected="selected"';
          $('#cbo-moneda').append('<option value="' + responseMonedas[i]['ID_Moneda'] + '" data-no_signo="' + responseMonedas[i]['No_Signo'] + '" ' + selected + '>' + responseMonedas[i]['No_Moneda'] + '</option>');
        }
      }, 'JSON');

      $('[name="No_Titular_Cuenta"]').val(response.No_Titular_Cuenta);
      $('[name="No_Cuenta_Bancaria"]').val(response.No_Cuenta_Bancaria);
      $('[name="No_Cuenta_Interbancario"]').val(response.No_Cuenta_Interbancario);
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

function form_MedioPago_cuentas_bancarias() {
  $('#btn-save').text('');
  $('#btn-save').attr('disabled', true);
  $('#btn-save').append('Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>');

  $('#modal-loader').modal('show');

  url = base_url + 'TiendaVirtual/Configuracion/MetodosPagoTiendaVirtualController/crudMedioPago_cuentas_bancarias';
  $.ajax({
    type: 'POST',
    dataType: 'JSON',
    url: url,
    data: $('#form-MedioPago-CuentasBancarias').serialize(),
    success: function (response) {
      $('#modal-loader').modal('hide');

      $('.modal-message').removeClass('modal-danger modal-warning modal-success');
      $('#modal-message').modal('show');

      if (response.status == 'success') {
        $('#modal-MedioPago-CuentasBancarias').modal('hide');
        $('.modal-message').addClass(response.style_modal);
        $('.modal-title-message').text(response.message);
        setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
        reload_table_medio_pago_cuentas_bancarias();
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

function eliminarMedioPago_cuentas_bancarias(ID) {
  var $modal_delete = $('#modal-message-delete');
  $modal_delete.modal('show');

  $('#btn-cancel-delete').off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $('#btn-save-delete').off('click').click(function () {
    $('#modal-loader').modal('show');

    url = base_url + 'TiendaVirtual/Configuracion/MetodosPagoTiendaVirtualController/eliminarMedioPago_cuentas_bancarias/' + ID;
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
          reload_table_medio_pago_cuentas_bancarias();
        } else {
          $('.modal-message').addClass(response.style_modal);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1500);
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        $('#modal-loader').modal('hide');
        $modal_delete.modal('hide');
        $('.modal-message').removeClass('modal-danger modal-warning modal-success');

        $('#modal-message').modal('show');
        $('.modal-message').addClass('modal-danger');
        $('.modal-title-message').text(textStatus + ' [' + jqXHR.status + ']: ' + errorThrown);
        setTimeout(function () { $('#modal-message').modal('hide'); }, 1700);

        //Message for developer
        console.log(jqXHR.responseText);
      },
    });
  });
}

function reload_table_medio_pago_cuentas_bancarias() {
  table_medio_pago_cuentas_bancarias.ajax.reload(null, false);
}
/* FIN CUEMTAS BANCARIAS */

function activarMercadoPago(){
  var $modal_delete = $('#modal-message-delete');
  $modal_delete.modal('show');

  $('#btn-cancel-delete').off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
  $('.modal-message-delete').addClass('modal-success');
  $('.modal-title-message-delete').text('¿Deseas activar mercado pago?');

  $('#btn-save-delete').off('click').click(function () {
    $('#modal-loader').modal('show');

    url = base_url + 'TiendaVirtual/Configuracion/MetodosPagoTiendaVirtualController/activarMercadoPago';
    $.ajax({
      url: url,
      type: "POST",
      dataType: "JSON",
      data: {},
      success: function (response) {
        $('#modal-loader').modal('hide');

        $modal_delete.modal('hide');
        $('.modal-message').removeClass('modal-danger modal-warning modal-success');
        $('#modal-message').modal('show');

        if (response.status == 'success') {
          $('.modal-message').addClass(response.style_modal);
          $('.modal-title-message').text(response.message);
          setTimeout(function () {
            $('#modal-message').modal('hide');
            $('.btn-mercado_pago').hide();
          }, 5100);
          reload_table_medio_pago();
        } else {
          $('.modal-message').addClass(response.style_modal);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 2100);
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        $('#modal-loader').modal('hide');
        $modal_delete.modal('hide');
        $('.modal-message').removeClass('modal-danger modal-warning modal-success');

        $('#modal-message').modal('show');
        $('.modal-message').addClass('modal-danger');
        $('.modal-title-message').text(textStatus + ' [' + jqXHR.status + ']: ' + errorThrown);
        setTimeout(function () { $('#modal-message').modal('hide'); }, 1700);

        //Message for developer
        console.log(jqXHR.responseText);
      },
    });
  });
}