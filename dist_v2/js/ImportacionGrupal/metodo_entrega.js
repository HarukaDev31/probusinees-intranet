var url;
var table_metodo_entrega;

$(function () {

  getPromoDelivery();

  //$('.select2').select2();

  $(document).keyup(function (event) {
    if (event.which == 27) {//ESC
      $("#modal-MedioPago").modal('hide');
    }
  });

  url = base_url + 'ImportacionGrupal/MetodoEntregaGrupal/ajax_list';
  table_metodo_entrega = $('#table-MedioPago').DataTable({
    dom: "<'row'<'col-sm-12 col-md-5'><'col-sm-12 col-md-2'><'col-sm-12 col-md-5'f>>" +
    "<'row'<'col-sm-12'tr>>" +
    "<'row'<'col-sm-12 col-md-2'l><'col-sm-12 col-md-5'i><'col-sm-12 col-md-5'p>>",
    "paging": true,
    "lengthChange": true,
    "searching": true,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "responsive": false,
    'pagingType'  : 'full_numbers',
    'oLanguage' : {
      'sInfo'              : 'Mostrando (_START_ - _END_) total de registros _TOTAL_',
      'sLengthMenu'        : '_MENU_',
      'sSearch'            : 'Buscar por: ',
      'sSearchPlaceholder' : '',
      'sZeroRecords'       : 'No se encontraron registros',
      'sInfoEmpty'         : 'No hay registros',
      'sLoadingRecords'    : 'Cargando...',
      'sProcessing'        : 'Procesando...',
      'oPaginate'          : {
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
      'dataType'  : 'JSON',
      'data'      : function ( data ) {
      },
    },
    'columnDefs': [
      {
        'targets': 'no-hidden',
        "visible": false, 
      },{
      'className' : 'text-center',
      'targets'   : 'no-sort',
      'orderable' : false,
    },],
    'lengthMenu': [[10, 100, 1000, -1], [10, 100, 1000, "Todos"]],
  });

  $('#txt-Global_Filter').keyup(function () {
    table_metodo_entrega.search($(this).val()).draw();
  });

  $('#form-MedioPago').validate({
    rules: {
      No_Metodo_Entrega_Tienda_Virtual: {
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
      No_Metodo_Entrega_Tienda_Virtual: {
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

  //DISTRITO

  url = base_url + 'ImportacionGrupal/MetodoEntregaGrupal/ajax_list_distrito';
  table_distrito = $('#table-Distrito').DataTable({
    dom: "<'row'<'col-sm-12 col-md-5'><'col-sm-12 col-md-2'><'col-sm-12 col-md-5'f>>" +
    "<'row'<'col-sm-12'tr>>" +
    "<'row'<'col-sm-12 col-md-2'l><'col-sm-12 col-md-5'i><'col-sm-12 col-md-5'p>>",
    "paging": true,
    "lengthChange": true,
    "searching": true,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "responsive": false,
    'pagingType'  : 'full_numbers',
    'oLanguage' : {
      'sInfo'              : 'Mostrando (_START_ - _END_) total de registros _TOTAL_',
      'sLengthMenu'        : '_MENU_',
      'sSearch'            : 'Buscar por: ',
      'sSearchPlaceholder' : '',
      'sZeroRecords'       : 'No se encontraron registros',
      'sInfoEmpty'         : 'No hay registros',
      'sLoadingRecords'    : 'Cargando...',
      'sProcessing'        : 'Procesando...',
      'oPaginate'          : {
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
      'dataType'  : 'JSON',
      'data'      : function ( data ) {
        data.Filtros_Distritos = $('#cbo-Filtros_Distritos').val(),
        data.Global_Filter = $('#txt-Global_Filter_Distrito').val();
      },
    },
    'columnDefs': [
      {
        'targets': 'no-hidden',
        "visible": false, 
      },{
      'className' : 'text-center',
      'targets'   : 'no-sort',
      'orderable' : false,
    },],
    'lengthMenu': [[10, 100, 1000, -1], [10, 100, 1000, "Todos"]],
  });

  $( '.custom-select' ).removeClass('custom-select-sm form-control-sm');

  $('#txt-Global_Filter_Distrito').keyup(function () {
    table_distrito.search($(this).val()).draw();
  });

  $('#form-Distrito').validate({
    rules: {
      ID_Pais: {
        required: true
      },
      ID_Departamento: {
        required: true
      },
      ID_Provincia: {
        required: true
      },
      No_Distrito: {
        required: true
      },
      No_Distrito_Breve: {
        minlength: 2,
        maxlength: 2
      },
    },
    messages: {
      ID_Pais: {
        required: "Seleccionar país",
      },
      ID_Departamento: {
        required: "Seleccionar departamento",
      },
      ID_Provincia: {
        required: "Seleccionar provincia",
      },
      No_Distrito: {
        required: "Ingresar nombre",
      },
      No_Distrito_Breve: {
        minlength: "Debe ingresar 2 dígitos",
        maxlength: "Debe ingresar 2 dígitos"
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
    submitHandler: form_Distrito
  });

  //CONFIGURACION DE ENVIO X RECOJO EN TIENDA
  $( '#cbo-Paises-recojo_tienda' ).change(function(){
    $( '#cbo-Departamentos-recojo_tienda' ).html('');
    if ( $(this).val() > 0 ) {
      url = base_url + 'HelperController/getDepartamentos';
      $.post( url, {ID_Pais : $(this).val()}, function( response ){
        $( '#cbo-Departamentos-recojo_tienda' ).html('<option value="" selected="selected">- Seleccionar -</option>');
        for (var i = 0; i < response.length; i++)
          $( '#cbo-Departamentos-recojo_tienda' ).append( '<option value="' + response[i].ID_Departamento + '">' + response[i].No_Departamento + '</option>' );
      }, 'JSON');
    }
  })
  
  $( '#cbo-Departamentos-recojo_tienda' ).change(function(){
    $( '#cbo-Provincias-recojo_tienda' ).html('');
    if ( $(this).val() > 0 ) {
      url = base_url + 'HelperController/getProvincias';
      $.post( url, {ID_Departamento : $(this).val()}, function( response ){
        $( '#cbo-Provincias-recojo_tienda' ).html('<option value="" selected="selected">- Seleccionar -</option>');
        for (var i = 0; i < response.length; i++)
          $( '#cbo-Provincias-recojo_tienda' ).append( '<option value="' + response[i].ID_Provincia + '">' + response[i].No_Provincia + '</option>' );
      }, 'JSON');
    }
  })
  
  $( '#cbo-Provincias-recojo_tienda' ).change(function(){
    $( '#cbo-Distritos-recojo_tienda' ).html('');
    if ( $(this).val() > 0 ) {
      url = base_url + 'HelperController/getDistritos';
      $.post( url, {ID_Provincia : $(this).val()}, function( response ){
        $( '#cbo-Distritos-recojo_tienda' ).html('<option value="" selected="selected">- Seleccionar -</option>');
        for (var i = 0; i < response.length; i++)
          $( '#cbo-Distritos-recojo_tienda' ).append( '<option value="' + response[i].ID_Distrito + '">' + response[i].No_Distrito + '</option>' );
      }, 'JSON');
    }
  })

  //CONFIGURACION DE ENVIO X DELIVERY
  $('#cbo-Paises').change(function () {
    $('#cbo-Departamentos').html('');
    if ($(this).val() > 0) {
      url = base_url + 'HelperController/getDepartamentos';
      $.post(url, { ID_Pais: $(this).val() }, function (response) {
        $('#cbo-Departamentos').html('<option value="" selected="selected">- Seleccionar -</option>');
        for (var i = 0; i < response.length; i++)
          $('#cbo-Departamentos').append('<option value="' + response[i].ID_Departamento + '">' + response[i].No_Departamento + '</option>');
      }, 'JSON');
    }
  })

  $('#cbo-Departamentos').change(function () {
    $('#cbo-Provincias').html('');
    if ($(this).val() > 0) {
      url = base_url + 'HelperController/getProvincias';
      $.post(url, { ID_Departamento: $(this).val() }, function (response) {
        $('#cbo-Provincias').html('<option value="" selected="selected">- Seleccionar -</option>');
        for (var i = 0; i < response.length; i++)
          $('#cbo-Provincias').append('<option value="' + response[i].ID_Provincia + '">' + response[i].No_Provincia + '</option>');
      }, 'JSON');
    }
  })
  //FIN DISTRITO

  $('#btn-modificar_precio_estandar_delivery').click(function () {
    if($('#txt-Ss_Precio_Estandar_Delivery').val().length==0){
      alert('Ingresar precio');
    } else {
      $('#btn-modificar_precio_estandar_delivery').text('');
      $('#btn-modificar_precio_estandar_delivery').attr('disabled', true);
      $('#btn-modificar_precio_estandar_delivery').append('Actualizando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>');

      url = base_url + 'ImportacionGrupal/MetodoEntregaGrupal/updPrecioEstandarDelivery';
      var arrPost = {
        ID_Empresa : $( '[name="ID_Empresa_Estandar_Delivery"]' ).val(),
        Ss_Precio : parseFloat($('#txt-Ss_Precio_Estandar_Delivery').val())
      };
      $.post( url, arrPost, function( response ){
        $('.modal-message').removeClass('modal-danger modal-warning modal-success');
        $('#modal-message').modal('show');
      
        if (response.status == 'success') {
          $('.modal-message').addClass(response.style_modal);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
          reload_table_distrito();
        } else {
          $('.modal-message').addClass(response.style_modal);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1200);
        }

        $('#btn-modificar_precio_estandar_delivery').text('');
        $('#btn-modificar_precio_estandar_delivery').attr('disabled', false);
        $('#btn-modificar_precio_estandar_delivery').append('Activar');

      }, 'JSON');
    }
  })
})

function verMetodoEntrega(ID) {
  $('#form-MedioPago')[0].reset();

  $('[name="EID_Almacen"]').val('');
  $('[name="Txt_Direccion_Almacen"]').val('');

  $('.form-group').removeClass('has-error');
  $('.form-group').removeClass('has-success');
  $('.help-block').empty();

  url = base_url + 'ImportacionGrupal/MetodoEntregaGrupal/ajax_edit/' + ID;
  $.ajax({
    url: url,
    type: "GET",
    dataType: "JSON",
    success: function (response) {
      $('#modal-MedioPago').modal('show');
      $('.modal-title').text('Modificar Método de Entrega');

      $('[name="EID_Empresa"]').val(response.ID_Empresa);
      $('[name="EID_Metodo_Entrega_Tienda_Virtual"]').val(response.ID_Metodo_Entrega_Tienda_Virtual);
      $('[name="ENu_Tipo_Metodo_Entrega_Tienda_Virtual"]').val(response.Nu_Tipo_Metodo_Entrega_Tienda_Virtual);
      $('[name="ENo_Metodo_Entrega_Tienda_Virtual"]').val(response.No_Metodo_Entrega_Tienda_Virtual);
      
      $('[name="No_Metodo_Entrega_Tienda_Virtual"]').val(response.No_Metodo_Entrega_Tienda_Virtual);
      var selected;
      $('.div-Estado').show();
      $('#cbo-Estado').html('');
      for (var i = 0; i < 2; i++) {
        selected = '';
        if (response.Nu_Estado == i)
          selected = 'selected="selected"';
        $('#cbo-Estado').append('<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Inactivo' : 'Activo') + '</option>');
      }

      $('.div-recojo_tienda').hide();
      //Mostrar campos de recojo en tienda
      if(response.Nu_Tipo_Metodo_Entrega_Tienda_Virtual==7){
        $('.div-recojo_tienda').show();

        //buscar almacen por token de tienda
        url = base_url + 'HelperTiendaVirtualController/getAlmacenxTokenTienda';
        $.ajax({
          url : url,
          type: "POST",
          dataType: "JSON",
          success: function(responseAlmacen){
            $('[name="EID_Almacen"]').val(responseAlmacen.ID_Almacen);
            $('[name="Txt_Direccion_Almacen"]').val(responseAlmacen.Txt_Direccion_Almacen);

            url = base_url + 'HelperController/getPaises';
            $.post( url , function( responsePais ){
              $( '#cbo-Paises-recojo_tienda' ).html('');
              for (var i = 0; i < responsePais.length; i++){
                selected = '';
                if(responseAlmacen.ID_Pais == responsePais[i]['ID_Pais'])
                  selected = 'selected="selected"';
                $( '#cbo-Paises-recojo_tienda' ).append( '<option value="' + responsePais[i].ID_Pais + '" ' + selected + '>' + responsePais[i].No_Pais + '</option>' );
              }
            }, 'JSON');
            
            url = base_url + 'HelperController/getDepartamentos';
            $.post( url, {ID_Pais : responseAlmacen.ID_Pais}, function( responseDepartamentos ){
              $( '#cbo-Departamentos-recojo_tienda' ).html('');
              for (var i = 0; i < responseDepartamentos.length; i++){
                selected = '';
                if(responseAlmacen.ID_Departamento == responseDepartamentos[i].ID_Departamento)
                  selected = 'selected="selected"';
                $( '#cbo-Departamentos-recojo_tienda' ).append( '<option value="' + responseDepartamentos[i].ID_Departamento + '" ' + selected + '>' + responseDepartamentos[i].No_Departamento + '</option>' );
              }
            }, 'JSON');
            
            url = base_url + 'HelperController/getProvincias';
            $.post( url, {ID_Departamento : responseAlmacen.ID_Departamento}, function( responseProvincia ){
              $( '#cbo-Provincias-recojo_tienda' ).html('');
              for (var i = 0; i < responseProvincia.length; i++){
                selected = '';
                if(responseAlmacen.ID_Provincia == responseProvincia[i].ID_Provincia)
                  selected = 'selected="selected"';
                $( '#cbo-Provincias-recojo_tienda' ).append( '<option value="' + responseProvincia[i].ID_Provincia + '" ' + selected + '>' + responseProvincia[i].No_Provincia + '</option>' );
              }
            }, 'JSON');
            
            url = base_url + 'HelperController/getDistritos';
            $.post( url, {ID_Provincia : responseAlmacen.ID_Provincia}, function( responseDistrito ){
              $( '#cbo-Distritos-recojo_tienda' ).html('');
              for (var i = 0; i < responseDistrito.length; i++){
                selected = '';
                if(responseAlmacen.ID_Distrito == responseDistrito[i].ID_Distrito)
                  selected = 'selected="selected"';
                $( '#cbo-Distritos-recojo_tienda' ).append( '<option value="' + responseDistrito[i].ID_Distrito + '" ' + selected + '>' + responseDistrito[i].No_Distrito + '</option>' );
              }
            }, 'JSON');
          },
          error: function (jqXHR, textStatus, errorThrown) {
            $('.modal-message').removeClass('modal-danger modal-warning modal-success');

            $('#modal-message').modal('show');
            $('.modal-message').addClass('modal-danger');
            $('.modal-title-message').text(textStatus + ' [' + jqXHR.status + ']: ' + errorThrown);
            setTimeout(function () { $('#modal-message').modal('hide'); }, 1700);

            //Message for developer
            console.log(jqXHR.responseText);
          }
        });
      }// if - recojo en tienda
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

  url = base_url + 'ImportacionGrupal/MetodoEntregaGrupal/crudMedioPago';
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
        reload_table_metodo_entrega();
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

function reload_table_metodo_entrega() {
  table_metodo_entrega.ajax.reload(null, false);
}


//DISTRITO
function verDistrito(ID) {
  accion_distrito = 'upd_distrito';

  $('#form-Distrito')[0].reset();
  $('.form-group').removeClass('has-error');
  $('.form-group').removeClass('has-success');
  $('.help-block').empty();

  $('#modal-loader').modal('show');

  $("#cbo-Paises").css("background-color", "#d2d6de");
  $("#cbo-Paises").css("pointer-events", "none");

  $("#cbo-Departamentos").css("background-color", "#d2d6de");
  $("#cbo-Departamentos").css("pointer-events", "none");

  $("#cbo-Provincias").css("background-color", "#d2d6de");
  $("#cbo-Provincias").css("pointer-events", "none");

  $('[name="No_Distrito"]').css("background-color", "#d2d6de");
  $('[name="No_Distrito"]').css("pointer-events", "none");

  url = base_url + 'ImportacionGrupal/MetodoEntregaGrupal/ajax_edit_distrito/' + ID;
  $.ajax({
    url: url,
    type: "GET",
    dataType: "JSON",
    success: function (response) {
      $('#modal-Distrito').modal('show');
      $('.modal-title').text('Modificar Distrito');

      $('[name="EID_Distrito"]').val(response.ID_Distrito);

      var selected = '';
      url = base_url + 'HelperController/getPaises';
      $.post(url, function (responsePais) {
        $('#cbo-Paises').html('');
        for (var i = 0; i < responsePais.length; i++) {
          selected = '';
          if (response.ID_Pais == responsePais[i].ID_Pais)
            selected = 'selected="selected"';
          $('#cbo-Paises').append('<option value="' + responsePais[i].ID_Pais + '" ' + selected + '>' + responsePais[i].No_Pais + '</option>');
        }
      }, 'JSON');

      url = base_url + 'HelperController/getDepartamentos';
      $.post(url, { ID_Pais: response.ID_Pais }, function (responseDepartamentos) {
        $('#cbo-Departamentos').html('');
        for (var i = 0; i < responseDepartamentos.length; i++) {
          selected = '';
          if (response.ID_Departamento == responseDepartamentos[i].ID_Departamento)
            selected = 'selected="selected"';
          $('#cbo-Departamentos').append('<option value="' + responseDepartamentos[i].ID_Departamento + '" ' + selected + '>' + responseDepartamentos[i].No_Departamento + '</option>');
        }
      }, 'JSON');

      url = base_url + 'HelperController/getProvincias';
      $.post(url, { ID_Departamento: response.ID_Departamento }, function (responseProvincia) {
        $('#cbo-Provincias').html('');
        for (var i = 0; i < responseProvincia.length; i++) {
          selected = '';
          if (response.ID_Provincia == responseProvincia[i].ID_Provincia)
            selected = 'selected="selected"';
          $('#cbo-Provincias').append('<option value="' + responseProvincia[i].ID_Provincia + '" ' + selected + '>' + responseProvincia[i].No_Provincia + '</option>');
        }
      }, 'JSON');

      $('[name="No_Distrito"]').val(response.No_Distrito);
      $('[name="No_Distrito_Breve"]').val(response.No_Distrito_Breve);
      $('[name="Ss_Delivery"]').val(response.Ss_Delivery);

      $('#cbo-habilitar_ecommerce').html('');
      for (var i = 0; i < 2; i++) {
        selected = '';
        if (response.Nu_Habilitar_Ecommerce == i)
          selected = 'selected="selected"';
        $('#cbo-habilitar_ecommerce').append('<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'No' : 'Si') + '</option>');
      }
      $('#modal-loader').modal('hide');
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

function form_Distrito() {
  if (accion_distrito == 'add_distrito' || accion_distrito == 'upd_distrito') {
    $('#btn-save').text('');
    $('#btn-save').attr('disabled', true);
    $('#btn-save').append('Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>');

    $('#modal-loader').modal('show');

    //url = base_url + 'Configuracion/DistritoController/crudDistrito';
    url = base_url + 'ImportacionGrupal/MetodoEntregaGrupal/crudDistrito';
    $.ajax({
      type: 'POST',
      dataType: 'JSON',
      url: url,
      data: $('#form-Distrito').serialize(),
      success: function (response) {
        $('#modal-loader').modal('hide');

        $('.modal-message').removeClass('modal-danger modal-warning modal-success');
        $('#modal-message').modal('show');

        if (response.status == 'success') {
          accion_distrito = '';
          $('#modal-Distrito').modal('hide');
          $('.modal-message').addClass(response.style_modal);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
          reload_table_distrito();
        } else {
          $('#txt-No_Distrito').val('');
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
}

function reload_table_distrito() {
  table_distrito.ajax.reload(null, false);
}
// FIN DISTRITO

//BOTON PARA CONFIGURAR DELIVERY ESTANDAR
$( '#btn-configurar_delivery_estandar' ).on("click", function(e){
  $( this ).toggleClass('btn-success');
  $('.div-configurar_delivery_estandar').toggleClass('hidden');
  //
  $( '#btn-configurar_promo_delivery' ).removeClass('btn-success');
  $( '#btn-configurar_promo_delivery' ).addClass('btn-primary');
  $('.div-configurar_promo_delivery').addClass('hidden');
  //
  $( '#btn-configurar_delivery_manual' ).removeClass('btn-success');
  $( '#btn-configurar_delivery_manual' ).addClass('btn-primary');
  $('.div-configurar_delivery_manual').addClass('hidden');
});
//FIN BOTON PARA CONFIGURAR DELIVERY ESTANDAR

//BOTON PARA CONFIGURAR DELIVERY MANUAL
$( '#btn-configurar_delivery_manual' ).on("click", function(e){
  $( this ).toggleClass('btn-success');
  $('.div-configurar_delivery_manual').toggleClass('hidden');
  //
  $( '#btn-configurar_promo_delivery' ).removeClass('btn-success');
  $( '#btn-configurar_promo_delivery' ).addClass('btn-primary');
  $('.div-configurar_promo_delivery').addClass('hidden');
  //
  $( '#btn-configurar_delivery_estandar' ).removeClass('btn-success');
  $( '#btn-configurar_delivery_estandar' ).addClass('btn-primary');
  $('.div-configurar_delivery_estandar').addClass('hidden');
});
//FIN BOTON PARA CONFIGURAR DELIVERY MANUAL

//BOTON PARA CONFIGURAR PROMOCION DE DELIVERY
$( '#btn-configurar_promo_delivery' ).on("click", function(e){
  $( this ).toggleClass('btn-success');
  $('.div-configurar_promo_delivery').toggleClass('hidden');
  //
  $( '#btn-configurar_delivery_estandar' ).removeClass('btn-success');
  $( '#btn-configurar_delivery_estandar' ).addClass('btn-primary');
  $('.div-configurar_delivery_estandar').addClass('hidden');
  //
  $( '#btn-configurar_delivery_manual' ).removeClass('btn-success');
  $( '#btn-configurar_delivery_manual' ).addClass('btn-primary');
  $('.div-configurar_delivery_manual').addClass('hidden');  
});
//FIN BOTON PARA CONFIGURAR PROMOCION DE DELIVERY

//CHECKBOX PARA ACTIVAR PROMOCION DE DELIVERY
$( '#chk-ID_Estatus_Promo' ).on('change', function(e) {
  if($( this ).is(':checked')) {
    $('.check-title').text('Desactivar promoción');
    $('.input-configurar_promo_delivery').prop('disabled', false);
  } else {
    $('.check-title').text('Activar promoción');
    $('.input-configurar_promo_delivery').prop('disabled', true);
  }
});
//FIN CHECKBOX PARA ACTIVAR PROMOCION DE DELIVERY

$( "#form-ConfigurarPromoDelivery" ).validate({
  rules: {
    Nu_Monto_Compra: {
      required: true,
      number: true,
      min: 0.01
    },
    Nu_Costo_Envio: {
      //required: true,
      number: true,
      min: 0.00
    }
  },
  messages: {
    Nu_Monto_Compra:{
      required: "Ingresar el monto de compra.",
      number: "Ingresar números.",
      min: 'EL monto de la compra debe ser mayor a cero.'
    },
    Nu_Costo_Envio:{
      //required: "Ingresar el costo del envío.",
      number: "Ingresar números.",
      min: "El costo de envío no debe ser menor a cero.",
    }
  },
  errorPlacement : function(error, element) {
    $(element).closest('.form-group').find('.help-block').html(error.html());
  },
  highlight : function(element) {
    $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
  },
  unhighlight: function(element, errorClass, validClass) {
    $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
    $(element).closest('.form-group').find('.help-block').html('');
  },
  submitHandler: form_PromoDelivery
});

function form_PromoDelivery(){
  $( '.btn-configurar_promo_delivery' ).attr('disabled', true);
  $( '#modal-loader' ).modal('show');

  if($( '#chk-ID_Estatus_Promo' ).is(':checked') && !$.trim($('#txt-Nu_Costo_Envio').val())) {
    $('#txt-Nu_Costo_Envio').val(0);
  }

  var formData = new FormData($('#form-ConfigurarPromoDelivery')[0]);
    
  url = base_url + 'ImportacionGrupal/MetodoEntregaGrupal/crudPromoDelivery';
  $.ajax({
    type		    : 'POST',
    dataType	  : 'JSON',
    url		      : url,
    data		    : formData,
    mimeType    : "multipart/form-data",
    contentType : false,
    cache       : false,
    processData : false,
    success : function( response ){
      $( '#modal-loader' ).modal('hide');        
  	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
    	  $( '#modal-message' ).modal('show');
      
      if (response.status == 'success'){
        $( '.modal-message' ).addClass(response.style_modal);
        $( '.modal-title-message' ).text(response.message);
        setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
        //reload_table_sistema();
      } else {
        $( '.modal-message' ).addClass(response.style_modal);
        $( '.modal-title-message' ).text(response.message);
        setTimeout(function() {$('#modal-message').modal('hide');}, 1200);
      }
      
      $( '.btn-configurar_promo_delivery' ).attr('disabled', false);
    },
    error: function (jqXHR, textStatus, errorThrown) {
      $( '#modal-loader' ).modal('hide');
      $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
      
      $( '#modal-message' ).modal('show');
      $( '.modal-message' ).addClass( 'modal-danger' );
      $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
      setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
      
      //Message for developer
      console.log(jqXHR.responseText);
      
      $( '.btn-configurar_promo_delivery' ).attr('disabled', false);
    }
  });
}

function getPromoDelivery() {
  url = base_url + 'ImportacionGrupal/MetodoEntregaGrupal/ajax_promoDelivery';
  $.ajax({
    type		    : 'GET',
    dataType	  : 'JSON',
    url		      : url,
    contentType : false,
    cache       : false,
    processData : false,
    success : function( response ){
      if (response.status == 'success'){
        if(response.data.ID_Estatus_Promo == 1) {
          $( '#chk-ID_Estatus_Promo' ).prop( "checked", true );
          $('.check-title').text('Desactivar promoción');
          $('.input-configurar_promo_delivery').prop('disabled', false);
        }
        const Nu_Monto_Compra = Number.parseFloat(response.data.Nu_Monto_Compra).toFixed(2);
        const Nu_Costo_Envio = Number.parseFloat(response.data.Nu_Costo_Envio).toFixed(2);
        $('#txt-Nu_Monto_Compra').val(Nu_Monto_Compra);
        $('#txt-Nu_Costo_Envio').val(Nu_Costo_Envio);
        $('#txt-Txt_Terminos').val(response.data.Txt_Terminos);
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {

      
      //Message for developer
      console.log(jqXHR.responseText);
      
    }
  });
}

function cambiarEstadoTienda(ID, Nu_Estado) {
  var $modal_delete = $('#modal-message-delete');
  $modal_delete.modal('show');

  $('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
  $('.modal-message-delete').addClass('modal-success');

  var sNombreEstado = (Nu_Estado == 1 ? 'mostrar' : 'ocultar');

  $('.modal-title-message-delete').text('¿Deseas ' + sNombreEstado + ' ítem en la tienda?');

  $('#btn-cancel-delete').off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $('#btn-save-delete').off('click').click(function () {
    $('#modal-loader').modal('show');

    url = base_url + 'ImportacionGrupal/MetodoEntregaGrupal/cambiarEstadoTienda/' + ID + '/' + Nu_Estado;
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
          reload_table_distrito();
        } else {
          $('.modal-message').addClass(response.style_modal);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1500);
        }
      }
    });
  });
}

/*
$('.div-configurar_delivery_estandar').hide();
$('.div-configurar_delivery_manual').hide();

  $('#btn-configurar_delivery_estandar').click(function () {
    $('.div-configurar_delivery_manual').hide();
    if ($(this).data('mostrar_configurar_delivery_estandar') == 1) {
      //setter
      $('#btn-configurar_delivery_estandar').data('mostrar_configurar_delivery_estandar', 0);
      $('#btn-configurar_delivery_estandar').removeClass('btn-success');
      $('#btn-configurar_delivery_estandar').addClass('btn-primary');

    $('.div-configurar_delivery_estandar').hide();
    } else {
      $('#btn-configurar_delivery_estandar').data('mostrar_configurar_delivery_estandar', 1);
      $('#btn-configurar_delivery_estandar').removeClass('btn-primary');
      $('#btn-configurar_delivery_estandar').addClass('btn-success');

      $('#btn-configurar_delivery_manual').data('mostrar_configurar_delivery_manual', 0);
      $('#btn-configurar_delivery_manual').removeClass('btn-success');
      $('#btn-configurar_delivery_manual').addClass('btn-primary');

    $('.div-configurar_delivery_estandar').show();
    }
  })

  $('#btn-configurar_delivery_manual').click(function () {
      $('.div-configurar_delivery_estandar').hide();
    if ($(this).data('mostrar_configurar_delivery_manual') == 1) {
      //setter
      $('#btn-configurar_delivery_manual').data('mostrar_configurar_delivery_manual', 0);
      $('#btn-configurar_delivery_manual').removeClass('btn-success');
      $('#btn-configurar_delivery_manual').addClass('btn-primary');
      $('.div-configurar_delivery_manual').hide();
    } else {
      $('.div-configurar_delivery_manual').show();

      $('#btn-configurar_delivery_manual').data('mostrar_configurar_delivery_manual', 1);
      $('#btn-configurar_delivery_manual').removeClass('btn-primary');
      $('#btn-configurar_delivery_manual').addClass('btn-success');
      
      $('#btn-configurar_delivery_estandar').data('mostrar_configurar_delivery_estandar', 0);
      $('#btn-configurar_delivery_estandar').removeClass('btn-success');
      $('#btn-configurar_delivery_estandar').addClass('btn-primary');
    }
  })



*/