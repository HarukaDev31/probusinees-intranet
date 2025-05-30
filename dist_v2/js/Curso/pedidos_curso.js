var url, table_Entidad, div_items = '', iCounter = 1;
//AUTOCOMPLETE
var caractes_no_validos_global_autocomplete = "\"'~!@%^\|";
// Se puede crear un arreglo a partir de la cadena
let search_global_autocomplete = caractes_no_validos_global_autocomplete.split('');
// Solo tomé algunos caracteres, completa el arreglo
let replace_global_autocomplete = ['', '', '', '', '', '', '', '', ''];
//28 caracteres
// FIN AUTOCOMPLETE
var currentTableCurso = 'alumnos';
var tableCursoPagos;
var tableCursoPedidos;
var fToday = new Date(), fYear = fToday.getFullYear(), fMonth = fToday.getMonth() + 1, fDay = fToday.getDate();
async function addPagosCurso(idPedido, nombreCliente) {
  const { value: formValues } = await Swal.fire({
    title: `Pagos de Curso - ${nombreCliente}`,
    html: `
      <input type="number" id="monto" class="swal2-input" placeholder="Monto" step="0.01" required>
      <select id="banco" class="swal2-input" required>
        <option value="" disabled selected>Seleccione un banco</option>
        <option value="BCP" class="bg-primary">BCP</option>
        <option value="INTERBANK" class="bg-success">INTERBANK</option>
        </select>
      <input type="file" id="voucher" class="swal2-input" accept="image/*;application/pdf" required>
      <input type="date" id="fecha" class="swal2-input" required>

    `,
    focusConfirm: false,
    preConfirm: () => {
      const monto = $("#monto").val();
      const banco = $("#banco").val();
      const voucher = $("#voucher")[0].files[0];
      const fecha = $("#fecha").val();
      if (!monto || !banco || !voucher || !fecha) {
        Swal.showValidationMessage("Por favor, completa todos los campos.");
      } else {
        const formData = new FormData();
        formData.append("monto", monto);
        formData.append("banco", banco);
        formData.append("voucher", voucher);
        formData.append("fecha", fecha);
        formData.append("idPedido", idPedido);

        return formData;
      }
    },
    showCancelButton: true,
    confirmButtonText: "Guardar",
    cancelButtonText: "Cancelar",
  });
  if (formValues) {
    const formData = formValues;
    url =
      base_url + "Curso/PedidosCurso/saveClientePagosCurso"
    $.ajax({
      url: url,
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        const result = JSON.parse(response);
        if (result.status == "success") {
          Swal.fire("Correcto!", result.message, "success");
          // Reload the table or perform any other action needed
          tableCursoPagos.ajax.reload();
        } else {
          Swal.fire("Error!", result.message, "error");
        }
      },
    });
  }
}
$(function () {
  tableCursoPagos = $("#table-curso-pagos");
  tableCursoPedidos = $("#table-curso-pedidos");
  $(".tab-curso").removeClass("active");
  $(".tab-curso").off("click").click(function () {
    $(".tab-curso").removeClass("active");
    $("#table-curso-pagos_wrapper").hide();
    $("#table-curso-variacion_wrapper").hide();
    $("#table-curso-pagos_wrapper").hide();

    let table = this.getAttribute("data-table");
    this.classList.add("active");

    if (table == "alumnos") {
      $("#table-curso-pedidos").attr("style", "");
      $("#table-curso-pagos").hide();
      url = base_url + 'Curso/PedidosCurso/ajax_list';

      if ($.fn.DataTable.isDataTable("#table-curso-pedidos")) {

        $("#table-curso-pedidos").show();
        $("#table-curso-pedidos_wrapper").show();
        tableCursoPedidos.ajax.reload(null, false);
      } else {
        tableCursoPedidos = $("#table-curso-pedidos").DataTable({
          dom: "<'row'<'col-sm-12 col-md-4'B><'col-sm-12 col-md-7'f><'col-sm-12 col-md-1'>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-12 col-md-2'l><'col-sm-12 col-md-5'i><'col-sm-12 col-md-5'p>>",
          buttons: [{
            extend: 'excel',
            text: '<i class="fa fa-file-excel color_icon_excel"></i> Excel',
            titleAttr: 'Excel',
            exportOptions: {
              columns: ':visible'
            },
            attr: {
              class: "hidden"
            }
          },
          {
            extend: 'pdf',
            text: '<i class="fa fa-file-pdf color_icon_pdf"></i> PDF',
            titleAttr: 'PDF',
            exportOptions: {
              columns: ':visible'
            },
            attr: {
              class: "hidden"
            }
          },
          {
            extend: 'colvis',
            text: '<i class="fa fa-ellipsis-v"></i> Columnas',
            titleAttr: 'Columnas',
            exportOptions: {
              columns: ':visible'
            },
            attr: {
              class: "hidden"
            }
          },

          ],
          'searching': true,
          'bStateSave': true,
          "lengthChange": true,
          'processing': true,
          'serverSide': false,
          'info': true,
          'autoWidth': false,
          'pagingType': 'full_numbers',
          'oLanguage': {
            'sInfo': 'Mostrando (_START_ - _END_) total de registros _TOTAL_',
            'sLengthMenu': '_MENU_',
            'sSearch': 'Buscar por: ',
            'sSearchPlaceholder': '',
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
              data.sMethod = $('#hidden-sMethod').val(),
                data.estado_pago = $('#cbo-filtro-estado_pago').val(),
                data.Filtro_Fe_Inicio = ParseDateString($('#txt-Fe_Inicio').val(), 'fecha', '/'),
                data.Filtro_Fe_Fin = ParseDateString($('#txt-Fe_Fin').val(), 'fecha', '/');
              data.tipoTabla = "alumnos";
            },
          },
          'columnDefs': [
            {
              targets: 'no-hidden',
              visible: false,
            }, {
              className: 'text-center',
              targets: 'no-sort',
              orderable: false,
            }, {
              targets: "",
              orderable: false,
            },],
          'lengthMenu': [[10, 100, 1000, -1], [10, 100, 1000, "Todos"]],
        });

        // FUNCION PARA CONFIGURAR EL BUSCADOR
        configurarBuscador(
          "table-curso-pedidos",
          "search-table",
          "table-curso-pedidos_info"
        );
        //Funcion para exportar a excel

        $("#table-curso-pedidos").show();
      }
      currentTableCurso = "alumnos";
    }

    else if (table == "pagos") {
      $("#table-curso-pedidos").hide();
      $("#table-curso-pedidos_wrapper").hide();

      if ($.fn.DataTable.isDataTable("#table-curso-pagos")) {

        $("#table-curso-pagos").show();
        $("#table-curso-pagos_wrapper").show();
        tableCursoPagos.ajax.reload(null, false);
      } else {
        url = base_url + 'Curso/PedidosCurso/ajax_list';

        tableCursoPagos = $("#table-curso-pagos").DataTable({
          dom: "<'row'<'col-sm-12 col-md-4'B><'col-sm-12 col-md-7'f><'col-sm-12 col-md-1'>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-12 col-md-2'l><'col-sm-12 col-md-5'i><'col-sm-12 col-md-5'p>>",
          buttons: [],
          columnDefs: [
            {
              targets: "no-hidden",
              visible: false,
            },
            {
              className: "text-center",
              targets: "no-sort",
              orderable: false,
            },
            {
              targets: "",
              orderable: false,
            },
            {
              targets: "sorting_asc",
              orderable: false,
            },
          ],
          pageLength: 100, // Mostrar 100 elementos por página
          lengthMenu: [
            [100, 1000, -1],
            [100, 1000, "Todos"],
          ],
          paging: true,
          lengthChange: true,
          searching: true,
          ordering: true,
          info: true,
          autoWidth: false,
          responsive: false,
          serverSide: false,
          pagingType: "full_numbers",
          oLanguage: {
            sInfo:
              "Mostrando (_START_ - _END_) total de registros _TOTAL_",
            sLengthMenu: "_MENU_",
            sSearch: "Buscar por: ",
            sSearchPlaceholder: "",
            sZeroRecords: "No se encontraron registros",
            sInfoEmpty: "No hay registros",
            sLoadingRecords: "Cargando...",
            sProcessing: "Procesando...",
            oPaginate: {
              sFirst: "<<",
              sLast: ">>",
              sPrevious: "<",
              sNext: ">",
            },
          },
          ajax: {
            url: url,
            type: "POST",
            dataType: "JSON",
            data: function (data) {
              data.sMethod = $('#hidden-sMethod').val();
              data.estado_pago = $('#cbo-filtro-estado_pago').val();
              data.Filtro_Fe_Inicio = ParseDateString($('#txt-Fe_Inicio').val(), 'fecha', '/');
              data.Filtro_Fe_Fin = ParseDateString($('#txt-Fe_Fin').val(), 'fecha', '/');
              data.tipoTabla = "pagos";

            },
          },
        });
        // FUNCION PARA CONFIGURAR EL BUSCADOR
        configurarBuscador(
          "table-curso-pagos",
          "search-table",
          "table-curso-pagos_info"
        );
        //Funcion para exportar a excel

        $("#table-curso-pagos").show();
        currentTableCurso = "pagos";

      }
    }
  });
  $(".tab-curso").first().click();

  $('.input-report').datepicker({
    autoclose: true,
    //startDate : new Date(fYear, fToday.getMonth(), '01'),
    todayHighlight: true,
    dateFormat: 'dd/mm/yyyy',
    format: 'dd/mm/yyyy',
  });

  // table_Entidad = $("#table-curso-pedidos").DataTable({
  //   dom: "<'row'<'col-sm-12 col-md-4'B><'col-sm-12 col-md-7'f><'col-sm-12 col-md-1'>>" +
  //     "<'row'<'col-sm-12'tr>>" +
  //     "<'row'<'col-sm-12 col-md-2'l><'col-sm-12 col-md-5'i><'col-sm-12 col-md-5'p>>",
  //   buttons: [{
  //     extend: 'excel',
  //     text: '<i class="fa fa-file-excel color_icon_excel"></i> Excel',
  //     titleAttr: 'Excel',
  //     exportOptions: {
  //       columns: ':visible'
  //     },
  //     attr: {
  //       class: "hidden"
  //     }
  //   },
  //   {
  //     extend: 'pdf',
  //     text: '<i class="fa fa-file-pdf color_icon_pdf"></i> PDF',
  //     titleAttr: 'PDF',
  //     exportOptions: {
  //       columns: ':visible'
  //     },
  //     attr: {
  //       class: "hidden"
  //     }
  //   },
  //   {
  //     extend: 'colvis',
  //     text: '<i class="fa fa-ellipsis-v"></i> Columnas',
  //     titleAttr: 'Columnas',
  //     exportOptions: {
  //       columns: ':visible'
  //     },
  //     attr: {
  //       class: "hidden"
  //     }
  //   },

  //   ],
  //   'searching': true,
  //   'bStateSave': true,
  //   "lengthChange": true,
  //   'processing': true,
  //   'serverSide': false,
  //   'info': true,
  //   'autoWidth': false,
  //   'pagingType': 'full_numbers',
  //   'oLanguage': {
  //     'sInfo': 'Mostrando (_START_ - _END_) total de registros _TOTAL_',
  //     'sLengthMenu': '_MENU_',
  //     'sSearch': 'Buscar por: ',
  //     'sSearchPlaceholder': '',
  //     'sZeroRecords': 'No se encontraron registros',
  //     'sInfoEmpty': 'No hay registros',
  //     'sLoadingRecords': 'Cargando...',
  //     'sProcessing': 'Procesando...',
  //     'oPaginate': {
  //       'sFirst': '<<',
  //       'sLast': '>>',
  //       'sPrevious': '<',
  //       'sNext': '>',
  //     },
  //   },
  //   'order': [],
  //   'ajax': {
  //     'url': url,
  //     'type': 'POST',
  //     'dataType': 'JSON',
  //     'data': function (data) {
  //       data.sMethod = $('#hidden-sMethod').val(),
  //         data.estado_pago = $('#cbo-filtro-estado_pago').val(),
  //         data.Filtro_Fe_Inicio = ParseDateString($('#txt-Fe_Inicio').val(), 'fecha', '/'),
  //         data.Filtro_Fe_Fin = ParseDateString($('#txt-Fe_Fin').val(), 'fecha', '/');

  //     },
  //   },
  //   'columnDefs': [
  //     {
  //       targets: 'no-hidden',
  //       visible: false,
  //     }, {
  //       className: 'text-center',
  //       targets: 'no-sort',
  //       orderable: false,
  //     }, {
  //       targets: "",
  //       orderable: false,
  //     },],
  //   'lengthMenu': [[10, 100, 1000, -1], [10, 100, 1000, "Todos"]],
  // });
  // configurarBuscador('table-curso-pedidos', 'search-table', 'table-curso-pedidos_info');

  // $('#table-curso-pedidos_filter input').removeClass('form-control-sm');
  // $('#table-curso-pedidos_filter input').addClass('form-control-md');
  // $('#table-curso-pedidos_filter input').addClass("width_full");

  $('#btn-html_reporte').click(function () {
    reload_table_Entidad();
  });
})

function reload_table_Entidad() {
  table_Entidad.ajax.reload(null, false);
}

function crearUsuarioCursosMoodle(id, ID_Pedido_Curso) {
  var $modal_delete = $('#modal-message-delete');
  $modal_delete.modal('show');

  $('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
  $('.modal-message-delete').addClass('modal-success');

  $('#modal-title').text('¿Deseas crear usuario Moodle?');

  $('#btn-save-delete').off('click').click(function () {

    $('#btn-save-delete').text('');
    $('#btn-save-delete').attr('disabled', true);
    $('#btn-save-delete').append('Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>');

    url = base_url + 'Curso/PedidosCurso/crearUsuarioCursosMoodle/' + id + '/' + ID_Pedido_Curso;
    $.ajax({
      url: url,
      type: "GET",
      dataType: "JSON",
      success: function (response) {
        $modal_delete.modal('hide');
        $('#btn-save-delete').text('');
        $('#btn-save-delete').append('Aceptar');
        $('#btn-save-delete').attr('disabled', false);

        $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
        $('#modal-message').modal('show');

        if (response.status == 'success') {
          $('#moda-message-content').addClass('bg-' + response.status);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 2100);
          reload_table_Entidad();
        } else {
          $('#moda-message-content').addClass('bg-danger');
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 4100);
        }
      }
    });
  });
}

function enviarEmailUsuarioMoodle(id, ID_Pedido_Curso) {
  var $modal_delete = $('#modal-message-delete');
  $modal_delete.modal('show');

  $('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
  $('.modal-message-delete').addClass('modal-success');

  $('#modal-title').text('¿Deseas enviar credenciales Moodle?');

  $('#btn-save-delete').off('click').click(function () {

    $('#btn-save-delete').text('');
    $('#btn-save-delete').attr('disabled', true);
    $('#btn-save-delete').append('Enviando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>');

    url = base_url + 'Curso/PedidosCurso/enviarEmailUsuarioMoodle/' + id + '/' + ID_Pedido_Curso;
    $.ajax({
      url: url,
      type: "GET",
      dataType: "JSON",
      success: function (response) {
        $modal_delete.modal('hide');
        $('#btn-save-delete').text('');
        $('#btn-save-delete').append('Aceptar');
        $('#btn-save-delete').attr('disabled', false);

        $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
        $('#modal-message').modal('show');

        if (response.status == 'success') {
          $('#moda-message-content').addClass('bg-' + response.status);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 2100);
        } else {
          $('#moda-message-content').addClass('bg-danger');
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 4100);
        }
      }
    });
  });
}

async function viewCliente(id) {
  var url = base_url + 'Curso/PedidosCurso/ViewCliente/' + id;
  const response = await fetch(url);
  $.ajax({
    url: url,
    type: "GET",
    dataType: "JSON",
    success: function (response) {
      if (response.status == 'success') {
        $('#cliente-id').val(response.data.id_entidad);
        $("#cliente-nombres").val(response.data.nombres);
        $("#cliente-sexo").val(response.data.sexo);
        $("#cliente-dni").val(response.data.dni);
        $("#cliente-redsocial").val(response.data.red_social);
        $("#cliente-correo").val(response.data.correo);
        $("#cliente-pais").val(response.data.pais);
        $("#cliente-whatsapp").val(response.data.whatsapp);
        $("#cliente-departamento").val(response.data.departamento);
        $("#cliente-provincia").val(response.data.provincia);
        $("#cliente-distrito").val(response.data.distrito);
        $("#cliente-edad").val(response.data.nacimiento);
        if (response.data.usuario_moodle && response.data.password_moodle) {
          $('#acceso-aula-virtual').show();
          $('#cliente-moodle-usuario').val(response.data.usuario_moodle);
          $('#cliente-moodle-password').val(response.data.password_moodle);
        } else {
          $('#acceso-aula-virtual').hide();
          $('#cliente-moodle-usuario').val('');
          $('#cliente-moodle-password').val('');
        }

        // Siempre deja los campos en readonly y muestra solo el botón editar
        $('.cliente-input').prop('readonly', true);
        $('#btn-editar-cliente').show();
        $('#btn-guardar-cliente').hide();

        // Cambia el content-header
        $('.content-header').html(`
            <div class="container-fluid">
              <div class="row mb-2 px-3 d-flex justify-content-between">
                <div class="col-3 col-xl-1 py-sm-3 py-xl-0 py-md-0">
                  <button type="button" class="bg-white hover:bg-white-200 text-black-200 py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block btn-reporte btn-back-cotizacion"onclick="ocultarSectionDatosCliente()"><i class="fa fa-arrow-left"></i> Regresar</button>
                </div>
                <div class="col-xl-3 col-md-2"></div>
                <div class="col-6 col-md-0 col-xl-1">
                </div>
                <div class="col-12 col-md-2 col-xl-1">
                </div>
                <div class="col-12 col-md-0 col-xl-2 justify-content-center d-flex">
                  <button id="btn-editar-cliente" class="btn p-1 ml-4" title="Editar">
                      <i class="fas fa-edit"></i>
                  </button>
                  <button id="btn-cancel" class="hidden btn p-1 ml-4" title="Cancelar">
                      <i class="fas fa-times"></i>
                  </button>
                  <div class="col-3 col-xl-10 py-sm-3 py-xl-0 py-md-0">
                    <button id="btn-guardar-cliente" type="button" class="text-white bg-[#fd7e14] py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block btn-reporte btn-back-cotizacion"><i class="fa fa-save"></i> Guardar</button>
                  </div>
                </div>
              </div>
            </div>
            
        `);

        $('#section-listar-pedidos').hide();
        $('#section-datos-cliente').show();

        $('#btn-editar-cliente').on('click', function () {
          $('.cliente-input').prop('readonly', false);
          $('#cliente-moodle-usuario').prop('readonly', true);
          $('#cliente-moodle-password').prop('readonly', true);
          $('#btn-guardar-cliente').show();
          $('#btn-cancel').show();
          $(this).hide();
          $('#cliente-pais').hide();
          $('#select-pais').show();
          $('#cliente-departamento').hide();
          $('#select-departamento').show();
          $('#cliente-provincia').hide();
          $('#select-provincia').show();
          $('#cliente-distrito').hide();
          $('#select-distrito').show();
          $("#select-pais").val(response.data.id_pais).trigger('change');
          setTimeout(() => {
            $("#select-departamento").val(response.data.id_departamento).trigger('change');
            setTimeout(() => {
              $("#select-provincia").val(response.data.id_provincia).trigger('change');
              setTimeout(() => {
                $("#select-distrito").val(response.data.id_distrito);
              }, 200);
            }, 200);
          }, 200);
        });
        $('#btn-cancel').on('click', function () {
          $('.cliente-input').prop('readonly', true);
          $('#btn-editar-cliente').show();
          $(this).hide();
          $('#cliente-pais').show();
          $('#select-pais').hide();
          $('#cliente-departamento').show();
          $('#select-departamento').hide();
          $('#cliente-provincia').show();
          $('#select-provincia').hide();
          $('#cliente-distrito').show();
          $('#select-distrito').hide();
        });
        $('#btn-guardar-cliente').on('click', function (e) {
          console.log($('#cliente-id').val());
          console.log($('#form-datos-cliente').serialize());
          e.preventDefault();
          Swal.fire({
            title: 'Guardando...',
            html: '<div class="spinner-border text-warning" role="status"><span class="sr-only">Cargando...</span></div>',
            showConfirmButton: false,
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
              Swal.showLoading();
            }
          });
          $.ajax({
            url: base_url + "Curso/PedidosCurso/actualizarDatosCliente",
            type: "POST",
            data: $('#form-datos-cliente').serialize(),
            dataType: "json",
            success: function (response) {
              if (response.status === "success") {
                Swal.close();
                $('#cliente-pais').val($('#select-pais option:selected').text());
                $('#cliente-departamento').val($('#select-departamento option:selected').text());
                $('#cliente-provincia').val($('#select-provincia option:selected').text());
                $('#cliente-distrito').val($('#select-distrito option:selected').text());
                Swal.fire({
                  icon: 'success',
                  title: '¡Actualizado!',
                  text: 'Datos actualizados correctamente',
                  timer: 1800,
                  showConfirmButton: false
                });
              } else {
                Swal.fire({
                  icon: 'error',
                  title: 'Error',
                  text: response.message
                });
              }
            }
          });
          $('.cliente-input').prop('readonly', true);
          $('#btn-editar-cliente').show();
          $('#btn-cancel').hide();
          $('#select-pais').hide();
          $('#cliente-pais').show();
          $('#select-departamento').hide();
          $('#cliente-departamento').show();
          $('#select-provincia').hide();
          $('#cliente-provincia').show();
          $('#select-distrito').hide();
          $('#cliente-distrito').show();
        });

      } else {
        ocultarSectionDatosCliente();
      }
    }
  });
}

function ocultarSectionDatosCliente() {
  $('#section-listar-pedidos').show();
  $('#section-datos-cliente').hide();

  // Cambia el content-header
  $('.content-header').html(headerOriginal);
  reload_table_Entidad();

  // Oculta la sección de campañas y cursos
  $('#section-campanas-cursos').hide();
}

// Botón para crear campaña
$(document).on('click', '#btn-crear-campana', function () {
  estadoCampanas = [];
  $('#section-listar-pedidos').hide();
  $('#section-campanas-cursos').show();
  $('#contenedor-campanas').html('');
  $('.content-header').html(`
            <div class="container-fluid">
              <div class="row mb-2 px-3 d-flex justify-content-between">
                <div class="col-3 col-xl-1 py-sm-3 py-xl-0 py-md-0">
                  <button type="button" class="bg-white hover:bg-white-200 text-black-200 py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block btn-reporte btn-back-campanas" onclick="ocultarSectionDatosCliente()"><i class="fa fa-arrow-left"></i> Regresar</button>
                </div>
                <div class="col-xl-3 col-md-2"></div>
                <div class="col-6 col-md-0 col-xl-1">
                </div>
                <div class="col-12 col-md-2 col-xl-1">
                </div>
                <div class="col-12 col-md-0 col-xl-2 justify-content-center d-flex">
                  <div class="col-3 col-xl-10 py-sm-3 py-xl-0 py-md-0">
                    <button id="btn-guardar-campana" type="button" class="text-white bg-[#fd7e14] py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block btn-reporte "><i class="fa fa-save"></i> Guardar</button>
                  </div>
                </div>
              </div>
            </div>
            
        `);
  cargarCampanas();
});


function renderizarCampanas(meses) {
  let html = '';
  meses.forEach(mes => {
    // Días seleccionados como badges
    let seleccionadosHtml = '';
    if (mes.seleccionados.length) {
      seleccionadosHtml = mes.seleccionados.map(dia =>
        `<span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-blue-100 text-blue-800 text-sm font-medium">${dia}</span>`
      ).join('');
    } else {
      seleccionadosHtml = '<span class="text-muted pb-2">No hay días seleccionados</span>';
    }

    // Renderiza el calendario
    let diasHtml = '';
    let diasSemana = ['D', 'L', 'M', 'X', 'J', 'V', 'S'];
    diasHtml += '<div class="row mb-1">';
    diasSemana.forEach(dia => {
      diasHtml += `<div class="col text-center font-weight-bold">${dia}</div>`;
    });
    diasHtml += '</div>';

    let primerDia = new Date(new Date().getFullYear(), mes.numero - 1, 1).getDay();
    let totalDias = mes.dias.length;
    let diaActual = 1;
    let filas = Math.ceil((primerDia + totalDias) / 7);

    for (let f = 0; f < filas; f++) {
      diasHtml += '<div class="row mb-1">';
      for (let d = 0; d < 7; d++) {
        let cell = '';
        let cellClass = 'text-center';
        if (f === 0 && d < primerDia) {
          cell = '';
        } else if (diaActual <= totalDias) {
          let selected = mes.seleccionados.includes(diaActual) ? 'inline-flex items-center justify-center h-8 w-8 rounded-full bg-blue-100 text-blue-800 text-sm font-medium' : '';
          cell = `<button type="button" class="btn btn-sm btn-dia ${selected}" data-mes="${mes.numero}" data-dia="${diaActual}">${diaActual}</button>`;
          diaActual++;
        }
        diasHtml += `<div class="col ${cellClass}">${cell || '&nbsp;'}</div>`;
      }
      diasHtml += '</div>';
    }

    html += `
      <div class="card m-4 w-[40%]" data-mes="${mes.numero}">
        <div class="card-header d-flex align-items-center">
          <i class="fa fa-calendar mr-2"></i>
          <b class="mr-auto">${mes.nombre.toUpperCase()}</b>
          <a href="#" class="ml-auto seleccionar-todos" data-mes="${mes.numero}">Seleccionar todos</a>
        </div>
        <div class="card-body">
          <div class="mb-2 flex flex-wrap gap-2 seleccionados-badges">${seleccionadosHtml}</div>
          <div>${diasHtml}</div>
        </div>
      </div>
    `;
  });
  $('#contenedor-campanas').html(html);
}

let estadoCampanas = [];

function cargarCampanas() {
  $.ajax({
    url: base_url + "Curso/PedidosCurso/getCampanas",
    type: "GET",
    dataType: "json",
    success: function (response) {
      console.log('Respuesta de getCampanas:', response);
      if (response.status === "success") {
        estadoCampanas = response.data; // Guardamos el estado inicial
        renderizarCampanas(estadoCampanas);
      } else {
        $('#contenedor-campanas').html('<div class="col-12 text-center text-danger">No hay campañas disponibles.</div>');
      }
    }
  });
}

// Delegación de eventos para los días
$('#contenedor-campanas').on('click', '.btn-dia', function () {
  const mes = parseInt($(this).data('mes'));
  const dia = parseInt($(this).data('dia'));
  const mesObj = estadoCampanas.find(m => m.numero === mes);
  if (!mesObj) return;
  const idx = mesObj.seleccionados.indexOf(dia);
  if (idx === -1) {
    mesObj.seleccionados.push(dia);
  } else {
    mesObj.seleccionados.splice(idx, 1);
  }
  // Ordena los días seleccionados
  mesObj.seleccionados.sort((a, b) => a - b);
  renderizarCampanas(estadoCampanas);
});
$('#contenedor-campanas').on('click', '.seleccionar-todos', function (e) {
  e.preventDefault();
  const mes = parseInt($(this).data('mes'));
  const mesObj = estadoCampanas.find(m => m.numero === mes);
  if (!mesObj) return;
  if (mesObj.seleccionados.length === mesObj.dias.length) {
    mesObj.seleccionados = [];
  } else {
    mesObj.seleccionados = [...mesObj.dias];
  }
  renderizarCampanas(estadoCampanas);
});

$('#section-campanas-cursos').on('click', '#btn-guardar-campana', function () {
  // Puedes enviar solo los días seleccionados por mes
  const dataToSend = estadoCampanas.map(mes => ({
    mes: mes.numero,
    seleccionados: mes.seleccionados
  }));
  $.ajax({
    url: base_url + "Curso/PedidosCurso/guardarCampanas",
    type: "POST",
    data: { campanas: JSON.stringify(dataToSend) },
    dataType: "json",
    success: function (response) {
      if (response.status === "success") {
        Swal.fire('¡Guardado!', 'Las campañas se guardaron correctamente.', 'success');
      } else {
        Swal.fire('Error', response.message || 'No se pudo guardar.', 'error');
      }
    }
  });
});



let headerOriginal = '';
function cargarPaises() {
  $.ajax({
    url: base_url + "HelperController/getPaises",
    type: "POST",
    dataType: "json",
    success: function (paises) {
      $("#select-pais").empty().append('<option value="">Seleccione un país</option>');
      paises.forEach(function (pais) {
        $("#select-pais").append(`<option value="${pais.ID_Pais}">${pais.No_Pais}</option>`);
      });
    }
  });
}

function cargarDepartamentos(idPais) {
  $.ajax({
    url: base_url + "HelperController/getDepartamentos",
    type: "POST",
    data: { ID_Pais: idPais },
    dataType: "json",
    success: function (departamentos) {
      $("#select-departamento").empty().append('<option value="">Seleccione un departamento</option>');
      departamentos.forEach(function (dep) {
        $("#select-departamento").append(`<option value="${dep.ID_Departamento}">${dep.No_Departamento}</option>`);
      });
    }
  });
}

function cargarProvincias(idDepartamento) {
  $.ajax({
    url: base_url + "HelperController/getProvincias",
    type: "POST",
    data: { ID_Departamento: idDepartamento },
    dataType: "json",
    success: function (provincias) {
      $("#select-provincia").empty().append('<option value="">Seleccione una provincia</option>');
      provincias.forEach(function (prov) {
        $("#select-provincia").append(`<option value="${prov.ID_Provincia}">${prov.No_Provincia}</option>`);
      });
    }
  });
}

function cargarDistritos(idProvincia) {
  $.ajax({
    url: base_url + "HelperController/getDistritos",
    type: "POST",
    data: { ID_Provincia: idProvincia },
    dataType: "json",
    success: function (distritos) {
      $("#select-distrito").empty().append('<option value="">Seleccione un distrito</option>');
      distritos.forEach(function (dist) {
        $("#select-distrito").append(`<option value="${dist.ID_Distrito}">${dist.No_Distrito}</option>`);
      });
    }
  });
}
$(document).ready(async function () {
  $('.dropdown-menu').on('click', function (event) {
    event.stopPropagation(); // Evita que el evento se propague
  });

  // Cierra el menú al hacer clic en "Cancelar" o "Aplicar"
  $('#cancelar-btn, #aplicar-btn').on('click', function () {
    $('#filtros-btn').dropdown('hide'); // Cierra el menú
  });

  // Cierra el menú al hacer clic en el botón "Filtros" si ya está abierto
  $('#filtros-btn').on('click', function (event) {
    if ($(this).attr('aria-expanded') === 'true') {
      $(this).dropdown('hide'); // Cierra el menú si ya está abierto
    }
  });
  headerOriginal = $('.content-header').html();
  $sectionlistarpedidos = $('#section-listar-pedidos');
  $sectionlistarpedidos.show();
  $sectiondatoscliente = $('#section-datos-cliente');
  $sectiondatoscliente.hide();

  // Cargar los select de país, departamento, provincia y distrito
  cargarPaises();
  $("#select-pais").on("change", function () {
    cargarDepartamentos($(this).val());
    $("#select-provincia").empty();
    $("#select-distrito").empty();
  });
  $("#select-departamento").on("change", function () {
    cargarProvincias($(this).val());
    $("#select-distrito").empty();
  });
  $("#select-provincia").on("change", function () {
    cargarDistritos($(this).val());
  });
});
async function configurarBuscador(tableId, searchInputClass, infoContainerId) {
  // Obtener la instancia de DataTable
  var table = $("#" + tableId).DataTable();

  // Escuchar el evento "input" en el buscador
  $("." + searchInputClass).on("input", function () {
    var searchTerm = $(this).val(); // Obtener el valor del buscador
    table.search(searchTerm).draw(); // Aplicar la búsqueda y redibujar la tabla
  });

  // Función para limpiar el buscador y el filtro
  window["resetBuscador_" + tableId] = function () {
    $("." + searchInputClass).val("");
    table.search("").draw();
  };

  // Actualizar el mensaje de información después de cada búsqueda
  table.on("draw", function () {
    var info = table.page.info();
    if (infoContainerId) {
      $("#" + infoContainerId).html(
        `Mostrando ${info.start + 1} a ${info.end} de ${info.recordsTotal
        } registros`
      );
    }
  });

}