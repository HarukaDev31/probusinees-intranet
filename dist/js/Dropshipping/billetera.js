var url;
var table_sistema, table_desembolso, table_pagos;

$(function () {
  $('.select2').select2();
  $('[data-mask]').inputmask();

  url = base_url + 'Dropshipping/BilleteraDropshippingController/ajax_list';
  table_sistema = $('#table-Sistema').DataTable({
    'dom': '<"top">frt<"bottom"l><"clear">',
    'searching'   : false,
    'bStateSave'  : true,
    'processing'  : true,
    'serverSide'  : true,
    'info'        : true,
    'autoWidth'   : false,
    'pagingType'  : 'full_numbers',
    'oLanguage' : {
      'sInfo'                 : 'Mostrando (_START_ - _END_) total de registros _TOTAL_',
      'sLengthMenu'           : '_MENU_',
      'sSearch'               : 'Buscar por: ',
      'sSearchPlaceholder'    : 'UPC / Nombre',
      'sZeroRecords'          : 'No se encontraron registros',
      'sInfoEmpty'            : 'No hay registros',
      'sLoadingRecords'       : 'Cargando...',
      'sProcessing'           : 'Procesando...',
      'oPaginate'             : {
        'sFirst'    : '<<',
        'sLast'     : '>>',
        'sPrevious' : '<',
        'sNext'     : '>',
      },
    },
    'order': [],
    'ajax': {
      'url'       : url,
      'type'      : 'POST',
      'dataType'  : 'json',
      'data'      : function ( data ) {
        data.filtro_empresa = $( '#cbo-filtro_empresa' ).val(),
        data.filtro_organizacion = $( '#cbo-filtro_organizacion' ).val(),
        data.Filtros_Sistemas = $( '#cbo-Filtros_Sistemas' ).val(),
        data.Global_Filter = $( '#txt-Global_Filter' ).val();
      },
    },
    'columnDefs': [{
      'className' : 'text-center',
      'targets'   : 'no-sort',
      'orderable' : false,
    },],
    'lengthMenu': [[10, 100, 1000, -1], [10, 100, 1000, "Todos"]],
  });
  
  $('.dataTables_length').addClass('col-xs-4 col-sm-5 col-md-1');
  $('.dataTables_info').addClass('col-xs-8 col-sm-7 col-md-4');
  $('.dataTables_paginate').addClass('col-xs-12 col-sm-12 col-md-7');

  //FORM PARA AGREGAR
  $('#form-cuenta_bancaria').validate({
    rules: {
      ID_Empresa: {
        required: true
      },
      ID_Banco: {
        required: true
      },
      Nu_Tipo_Cuenta: {
        required: true
      },
      ID_Moneda: {
        required: true
      },
      No_Cuenta_Bancaria: {
        required: true
      },
      No_Cuenta_Interbancario: {
        required: true
      },
      No_Titular_Cuenta: {
        required: true
      },
    },
    messages: {
      ID_Empresa: {
        required: "Seleccionar Empresa",
      },
      ID_Banco: {
        required: "Seleccionar Banco",
      },
      Nu_Tipo_Cuenta: {
        required: "Seleccionar Tipo",
      },
      ID_Moneda: {
        required: "Seleccionar Moneda",
      },
      No_Cuenta_Bancaria: {
        required: "Ingresar Cuenta Bancaria",
      },
      No_Cuenta_Interbancario: {
        required: "Ingresar CCI",
      },
      No_Titular_Cuenta: {
        required: "Ingresar Titular",
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
    submitHandler: form_CuentaBancaria
  });

  //LISTAR DESEMBOLSOS PENDIENTES
  url = base_url + 'Dropshipping/BilleteraDropshippingController/ajax_list_desembolso_pendiente';
  table_desembolso = $('#table-desembolsos').DataTable({
    'dom': '<"top">frt<"bottom"l><"clear">',
    'searching'   : false,
    'bStateSave'  : true,
    'processing'  : true,
    'serverSide'  : true,
    'info'        : true,
    'autoWidth'   : false,
    'pagingType'  : 'full_numbers',
    'oLanguage' : {
      'sInfo'                 : 'Mostrando (_START_ - _END_) total de registros _TOTAL_',
      'sLengthMenu'           : '_MENU_',
      'sSearch'               : 'Buscar por: ',
      'sSearchPlaceholder'    : 'UPC / Nombre',
      'sZeroRecords'          : 'No se encontraron registros',
      'sInfoEmpty'            : 'No hay registros',
      'sLoadingRecords'       : 'Cargando...',
      'sProcessing'           : 'Procesando...',
      'oPaginate'             : {
        'sFirst'    : '<<',
        'sLast'     : '>>',
        'sPrevious' : '<',
        'sNext'     : '>',
      },
    },
    'order': [],
    'ajax': {
      'url'       : url,
      'type'      : 'POST',
      'dataType'  : 'json',
      'data'      : function ( data ) {
        data.filtro_empresa = $( '#cbo-filtro_empresa' ).val(),
        data.filtro_organizacion = $( '#cbo-filtro_organizacion' ).val();
      },
    },
    'columnDefs': [{
      'className' : 'text-center',
      'targets'   : 'no-sort',
      'orderable' : false,
    },],
    'lengthMenu': [[10, 100, 1000, -1], [10, 100, 1000, "Todos"]],
  });
  
  //LISTAR DESEMBOLSOS PAGADOS
  url = base_url + 'Dropshipping/BilleteraDropshippingController/ajax_list_desembolso_pago';
  table_desembolso = $('#table-pagos').DataTable({
    'dom': '<"top">frt<"bottom"l><"clear">',
    'searching'   : false,
    'bStateSave'  : true,
    'processing'  : true,
    'serverSide'  : true,
    'info'        : true,
    'autoWidth'   : false,
    'pagingType'  : 'full_numbers',
    'oLanguage' : {
      'sInfo'                 : 'Mostrando (_START_ - _END_) total de registros _TOTAL_',
      'sLengthMenu'           : '_MENU_',
      'sSearch'               : 'Buscar por: ',
      'sSearchPlaceholder'    : 'UPC / Nombre',
      'sZeroRecords'          : 'No se encontraron registros',
      'sInfoEmpty'            : 'No hay registros',
      'sLoadingRecords'       : 'Cargando...',
      'sProcessing'           : 'Procesando...',
      'oPaginate'             : {
        'sFirst'    : '<<',
        'sLast'     : '>>',
        'sPrevious' : '<',
        'sNext'     : '>',
      },
    },
    'order': [],
    'ajax': {
      'url'       : url,
      'type'      : 'POST',
      'dataType'  : 'json',
      'data'      : function ( data ) {
        data.filtro_empresa = $( '#cbo-filtro_empresa' ).val(),
        data.filtro_organizacion = $( '#cbo-filtro_organizacion' ).val();
      },
    },
    'columnDefs': [{
      'className' : 'text-center',
      'targets'   : 'no-sort',
      'orderable' : false,
    },],
    'lengthMenu': [[10, 100, 1000, -1], [10, 100, 1000, "Todos"]],
  });

})

/* CREAR DESEMBOLSO */
function solicitarDesembolso() {
  alert('Primeros debe de crear tu cuenta bancaria');
}

/* CUENTAS BANCARIAS */
function agregarCuentaBancaria() {
  $('#form-cuenta_bancaria')[0].reset();
  $('.form-group').removeClass('has-error');
  $('.form-group').removeClass('has-success');

  $('.help-block').empty();

  $('#modal-cuenta_bancaria').modal('show');

  //listar empresa
  url = base_url + 'HelperController/getEmpresas';
  var selected = '';
  $.post(url, function (response) {
    $('#cbo-Empresas').html('<option value="0" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++) {
      selected = '';
      if ($('#header-a-id_empresa').val() == response[i].ID_Empresa)
        selected = 'selected="selected"';
      $('#cbo-Empresas').append('<option value="' + response[i].ID_Empresa + '" ' + selected + '>' + response[i].No_Empresa + '</option>');
    }
  }, 'JSON');

  //listar banco
  $('#cbo-banco').html('<option value="" selected="selected">- Sin registro -</option>');
  url = base_url + 'HelperDropshippingController/listarBanco';
  $.post(url, {}, function (response) {
    if (response.status == 'success') {
      var l = response.result.length;
      $('#cbo-banco').html('<option value="" selected="selected">- Seleccionar -</option>');
      for (var x = 0; x < l; x++) {
        $('#cbo-banco').append('<option value="' + response.result[x].ID_Banco + '" ' + selected + '>' + response.result[x].No_Banco_Siglas + '</option>');
      }
    } else {
      if (response.sMessageSQL !== undefined) {
        console.log(response.sMessageSQL);
      }
      alert(response.message);      
      setTimeout(function () { $('#modal-message').modal('hide'); }, 1200);
    }
  }, 'JSON');

  //CUENTA DE AHORRO
  $('#cbo-tipo_cuenta').html('<option value="">- Seleccionar -</option>');
  $('#cbo-tipo_cuenta').append('<option value="1">Cuenta de Ahorro</option>');
  $('#cbo-tipo_cuenta').append('<option value="2">Cuenta Corriente</option>');

  //MONEDA  
  $('#cbo-moneda').html('<option value="" selected="selected">- Sin registro -</option>');
  url = base_url + 'HelperController/getMonedas';
  $.post(url, function (response) {
    $('#cbo-moneda').html('<option value="" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++)
      $('#cbo-moneda').append('<option value="' + response[i]['ID_Moneda'] + '" data-no_signo="' + response[i]['No_Signo'] + '">' + response[i]['No_Moneda'] + '</option>');
  }, 'JSON');
}

//CREAR CUENTA BANCARIA
function form_CuentaBancaria() {
  $('#btn-save').text('');
  $('#btn-save').attr('disabled', true);
  $('#btn-save').append('Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>');

  $('#modal-loader').modal('show');

  url = base_url + 'Dropshipping/BilleteraDropshippingController/crudCuentaBancaria';
  $.ajax({
    type: 'POST',
    dataType: 'JSON',
    url: url,
    data: $('#form-cuenta_bancaria').serialize(),
    success: function (response) {
      $('#modal-loader').modal('hide');

      $('.modal-message').removeClass('modal-danger modal-warning modal-success');
      $('#modal-message').modal('show');

      if (response.status == 'success') {
        $('#modal-MedioPago').modal('hide');
        $('.modal-message').addClass(response.style_modal);
        $('.modal-title-message').text(response.message);
        setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
        reload_table_cuenta_bancaria();
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

function reload_table_cuenta_bancaria(){
  table_sistema.ajax.reload(null,false);
}

function reload_table_desembolso(){
  table_desembolso.ajax.reload(null,false);
}

function reload_table_pagos(){
  table_pagos.ajax.reload(null,false);
}