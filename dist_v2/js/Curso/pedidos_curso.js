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
  $(".tab-curso").off("click").click(async function () {
    await getCursosHeader();
    $(".tab-curso").removeClass("active");
    $("#table-curso-pagos_wrapper").hide();
    $("#table-curso-variacion_wrapper").hide();
    $("#table-curso-pagos_wrapper").hide();

    let table = this.getAttribute("data-table");
    this.classList.add("active");

    if (table == "alumnos") {
      currentTableCurso = "alumnos";

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
          'searching': false,
          'bStateSave': true,
          "lengthChange": true,
          'processing': true,
          'serverSide': true,
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
                data.Filtro_Fe_Inicio = $('#txt-Fe_Inicio_Carga').val() != "" ? ParseDateString($('#txt-Fe_Inicio_Carga').val(), 'fecha', '/') : null;
              data.Filtro_Fe_Fin = $('#txt-Fe_Fin_Carga').val() != "" ? ParseDateString($('#txt-Fe_Fin_Carga').val(), 'fecha', '/') : null;
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
        tableCursoPedidos.on('draw', async function () {
          await getCursosHeader();
        });

        // FUNCION PARA CONFIGURAR EL BUSCADOR

        //Funcion para exportar a excel

        $("#table-curso-pedidos").show();
        tableCursoPedidos.on('init', async function () {
          configurarBuscador(
            "table-curso-pedidos",
            "search-table",
            "table-curso-pedidos_info"
          );
          $('.input-report').datepicker({
            autoclose: true,
            //startDate : new Date(fYear, fToday.getMonth(), '01'),
            todayHighlight: true,
            dateFormat: 'dd/mm/yyyy',
            format: 'dd/mm/yyyy',
          });
        })
      }

    }

    else if (table == "pagos") {
      currentTableCurso = "pagos";
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
          ordering: false,
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
              data.Filtro_Fe_Inicio = $('#txt-Fe_Inicio_Carga').val() != "" ? ParseDateString($('#txt-Fe_Inicio_Carga').val(), 'fecha', '/') : null;
              data.Filtro_Fe_Fin = $('#txt-Fe_Fin_Carga').val() != "" ? ParseDateString($('#txt-Fe_Fin_Carga').val(), 'fecha', '/') : null;
              data.tipoTabla = "pagos";

            },
          },
        });
        // FUNCION PARA CONFIGURAR EL BUSCADOR
        tableCursoPagos.on('init', async function () {
          configurarBuscador(
            "table-curso-pagos",
            "search-table",
            "table-curso-pagos_info"
          );
          $('.input-report').datepicker({
            autoclose: true,
            //startDate : new Date(fYear, fToday.getMonth(), '01'),
            todayHighlight: true,
            dateFormat: 'dd/mm/yyyy',
            format: 'dd/mm/yyyy',
          });
        })
        //Funcion para exportar a excel

        $("#table-curso-pagos").show();

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
  // })

  $('#btn-html_reporte').click(function () {
    reload_table_Entidad();
  });
})

function reload_table_Entidad() {
  if (typeof tableCursoPedidos !== 'undefined' && tableCursoPedidos !== null) {
    if (currentTableCurso === 'alumnos') {
      tableCursoPedidos.ajax.reload(null, false);
    }
    else if (currentTableCurso === 'pagos') {
      tableCursoPagos.ajax.reload(null, false);
    }
  }
}

function crearUsuarioCursosMoodle(id, ID_Pedido_Curso) {
  event.preventDefault();
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
          console.log(response,"console log");
          crearUsuarioCursosMoodle(idUsuario, idPedido);

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
        if (response.data.nu_estado == 2 && response.data.nu_estado_usuario_externo != "2") {
          $('#contenedor-boton-usuario').html(
            `<button class="btn btn-primary" onclick="crearUsuarioCursosMoodle('${response.data.id_usuario}', '${response.data.id_pedido_curso}')">
              Crear usuario
            </button>`
          );
        } else {
          $('#contenedor-boton-usuario').empty();
        }

        // Siempre deja los campos en readonly y muestra solo el botón editar
        $('.cliente-input').prop('readonly', true);
        $('#section-listar-pedidos').hide();
        $('#btn-editar-cliente').show();
        $('#btn-guardar-cliente').hide();

        // Cambia el content-header
        $('.content-header').html(`
            <div class="container-fluid">
              <div class="row mb-2 px-3 d-flex justify-content-between">
                <div class="col-3 col-xl-1 py-sm-3 py-xl-0 py-md-0">
                  <button type="button" class="bg-white hover:bg-white-200 text-black-200 py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block btn-reporte btn-back-cotizacion" onclick="ocultarSectionDatosCliente()"><i class="fa fa-arrow-left"></i> Regresar</button>
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
  $('#aplicar-btn-cotizacion').off('click').on('click', function () {

    if (typeof tableCursoPedidos !== 'undefined' && tableCursoPedidos !== null) {
      if (currentTableCurso === 'alumnos') {
        tableCursoPedidos.ajax.reload(null, false);
      }
      else if (currentTableCurso === 'pagos') {
        tableCursoPagos.ajax.reload(null, false);
      }
    }
  });
  $("#cancelar-btn").off("click").on("click", function () {
    limpiarFiltrosContenedor();
  });
  $('.input-report').datepicker({
    autoclose: true,
    //startDate : new Date(fYear, fToday.getMonth(), '01'),
    todayHighlight: true,
    dateFormat: 'dd/mm/yyyy',
    format: 'dd/mm/yyyy',
  });
  // Oculta la sección de campañas y cursos
  $('#section-campanas-cursos').hide();

}
// Función para aplicar los filtros y recargar la tabla
function aplicarFiltrosContenedor() {
  try {
    console.log(currentTableCurso)
    if (currentTableCurso === 'alumnos') {
      tableCursoPedidos.ajax.reload(null, false);
    }
    else if (currentTableCurso === 'pagos') {
      console.log("Recargando tabla de pagos");
      tableCursoPagos.ajax.reload(null, false);
    }

  } catch (e) {
    console.error("Error al aplicar filtros:", e);
  }
}

// Función para limpiar los filtros y recargar la tabla
function limpiarFiltrosContenedor() {
  $("#txt-Fe_Inicio_Carga").val('');
  $("#txt-Fe_Fin_Carga").val('');
  $("#txt-ID_Estado").val('0');
  if (currentTableCurso === 'alumnos') {
    $("#cbo-filtro-estado_pago").val('0').trigger('change');
    tableCursoPedidos.ajax.reload(null, false);
  }
  else if (currentTableCurso === 'pagos') {
    $("#cbo-filtro-estado_pago").val('0').trigger('change');
    tableCursoPagos.ajax.reload(null, false);
  }
  //dropdown-menu  remove show class
  $('.dropdown-menu').removeClass('show');

}

// Asocia los eventos a los botones
$("#aplicar-btn").off("click").on("click", function () {
  aplicarFiltrosContenedor();
});
$("#cancelar-btn").off("click").on("click", function () {
  limpiarFiltrosContenedor();
});

// Opcional: recargar automáticamente al cambiar un filtro
$(".input-date, .input-estado").on("change", function () {
  aplicarFiltrosContenedor();
});


function cargarTablaCampanas() {
  if ($.fn.DataTable.isDataTable('#table-campanas')) {
    table_Campanas.ajax.reload();
    return;
  }
  $('#table-campanas').show();
  url = base_url + 'Curso/PedidosCurso/getCampanasTabla';
  table_Campanas = $("#table-campanas").DataTable({
    dom: "<'row'<'col-sm-12 col-md-4'B><'col-sm-12 col-md-7'f><'col-sm-12 col-md-1'>>" +
      "<'row'<'col-sm-12'tr>>" +
      "<'row'<'col-sm-12 col-md-2'l><'col-sm-12 col-md-5'i><'col-sm-12 col-md-5'p>>",
    buttons: [
      {
        extend: 'excel',
        text: '<i class="fa fa-file-excel color_icon_excel"></i> Excel',
        titleAttr: 'Exportar a Excel',
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
        titleAttr: 'Exportar a PDF',
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
      }
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
      'type': 'GET',
      'dataType': 'JSON',
      'data': function (data) {
        data.sMethod = $('#hidden-sMethod').val();
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
  configurarBuscador('table-campanas', 'search-table-campanas', 'table-campanas_info');
}


function editarCampana(id) {
  $.ajax({
    url: base_url + "Curso/PedidosCurso/getCampanaById",
    type: "POST",
    data: { ID_Campana: id },
    dataType: "json",
    success: function (response) {
      if (response.status === "success") {
        // Llena los campos del modal
        $('#modalNuevaCampanaLabel').text('Editar Campaña');
        $('#id-campana-editar').val(response.data.ID_Campana);
        $('#fecha-inicio-campana').val(response.data.Fe_Inicio);
        $('#fecha-fin-campana').val(response.data.Fe_Fin);
        // Abre el modal
        $('#modal-nueva-campana').modal('show');
        table_Campanas.ajax.reload();
      } else {
        Swal.fire('Error', 'No se pudo obtener la campaña', 'error');
      }
    }
  });
}
// Para crear, limpia el formulario y cambia el título
$('#btn-crear-campana').on('click', function () {
  $('#modalNuevaCampanaLabel').text('Registrar Nueva Campaña');
  $('#form-nueva-campana')[0].reset();
  $('#id-campana-editar').val('');
});


function borrarCampana(id) {
  Swal.fire({
    title: '¿Estás seguro?',
    text: "Esta acción eliminará la campaña.",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: base_url + "Curso/PedidosCurso/borrarCampana",
        type: "POST",
        data: { ID_Campana: id },
        dataType: "json",
        success: function (response) {
          if (response.status === "success") {
            table_Campanas.ajax.reload();
            Swal.fire('¡Eliminado!', response.message, 'success');
          } else {
            Swal.fire('Error', response.message, 'error');
          }
        }
      });
    }
  });
}

// Botón para mostrar campañas
$(document).on('click', '#btn-crear-campana', function () {
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
                    <button id="btn-nueva-campana" type="button" class="text-white bg-[#fd7e14] py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block btn-reporte "><i class="fa fa-plus"></i>Nuevo</button>
                  </div>
                </div>
              </div>
            </div>
            
        `);
  cargarTablaCampanas();
});




$(document).on('click', '#btn-nueva-campana', function () {
  $('#form-nueva-campana')[0].reset();
  $('#modal-nueva-campana').modal('show');
});

// Guardar la campaña al enviar el formulario
$('#form-nueva-campana').on('submit', function (e) {
  e.preventDefault();
  var id = $('#id-campana-editar').val();
  var url = id ? base_url + "Curso/PedidosCurso/editarCampana" : base_url + "Curso/PedidosCurso/crearCampana";
  $.ajax({
    url: url,
    type: "POST",
    data: $(this).serialize(),
    dataType: "json",
    success: function (response) {
      if (response.status === "success") {
        $('#modal-nueva-campana').modal('hide');
        Swal.fire('¡Guardado!', response.message, 'success');
        table_Campanas.ajax.reload(null, false);
      } else {
        Swal.fire('Error', response.message || 'No se pudo guardar.', 'error');
      }
    }
  });
});

// Asignar campaña al pedido
$(document).on('change', 'select[name="ID_Campana"]', function () {
  var idCampana = $(this).val();
  // Encuentra el ID del pedido en la misma fila
  var idPedido = $(this).closest('tr').find('td').eq(0).text(); // Ajusta el índice si tu ID está en otra columna

  if (idCampana) {
    $.ajax({
      url: base_url + "Curso/PedidosCurso/asignarCampanaPedido",
      type: "POST",
      data: { ID_Pedido_Curso: idPedido, ID_Campana: idCampana },
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          Swal.fire('¡Guardado!', response.message, 'success');
        } else {
          Swal.fire('Error', response.message, 'error');
        }
      }
    });
  }
});


async function getCursosHeader() {
  const response = await fetch(base_url + "Curso/PedidosCurso/getCursosHeader");
  const data = await response.json();
  if (data.status === "success") {
    console.log(data.data);
    $("#span-total-importe").text((data.data.total_importe));
  }
}
// Delegación para inputs de importe en la tabla
$(document).on('keydown', 'input[name="importe_pedido"]', function (e) {
  // Solo permitir números y máximo 2 decimales
  if (
    // Permitir teclas de control
    $.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
    // Permitir Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
    ((e.keyCode == 65 || e.keyCode == 67 || e.keyCode == 86 || e.keyCode == 88) && (e.ctrlKey === true || e.metaKey === true)) ||
    // Permitir flechas
    (e.keyCode >= 35 && e.keyCode <= 39)
  ) {
    // Enter: guardar
    if (e.keyCode === 13) {
      e.preventDefault();
      var $input = $(this);
      var valor = $input.val();
      // Validar formato decimal
      if (!/^\d+(\.\d{1,2})?$/.test(valor)) {
        Swal.fire('Error', 'Solo se permiten números con hasta 2 decimales.', 'error');
        return false;
      }
      // Obtener ID del pedido (ajusta el índice si tu tabla cambia)
      var idPedido = $input.closest('tr').find('td').eq(0).text();
      $.ajax({
        url: base_url + "Curso/PedidosCurso/actualizarImportePedido",
        type: "POST",
        data: { ID_Pedido_Curso: idPedido, importe: valor },
        dataType: "json",
        success: async function (response) {
          if (response.status === "success") {
            Swal.fire('¡Guardado!', response.message, 'success');
            await getCursosHeader();
          } else {
            Swal.fire('Error', response.message, 'error');
          }
        }
      });
    }
    return; // Permitir
  }
  // Bloquear cualquier otra tecla que no sea número o punto
  if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
    e.preventDefault();
  }
});

// Delegación para el cambio de tipo de curso
$(document).on('change', '.select-tipo-curso', function () {
  var idPedido = $(this).data('id');
  var idTipoCurso = $(this).val();

  $.ajax({
    url: base_url + 'Curso/PedidosCurso/asignarTipoCurso',
    type: 'POST',
    data: {
      id_pedido: idPedido,
      id_tipo_curso: idTipoCurso
    },
    dataType: 'json',
    success: function (response) {
      if (response.status === 'success') {
        Swal.fire({
          icon: 'success',
          title: '¡Guardado!',
          text: 'Tipo de curso actualizado correctamente',
          timer: 1500,
          showConfirmButton: false
        });
        // Opcional: recarga la tabla
        // tableCursoPedidos.ajax.reload();
      } else {
        Swal.fire('Error', response.message || 'No se pudo actualizar el tipo de curso', 'error');
      }
    },
    error: function () {
      Swal.fire('Error', 'Error al actualizar el tipo de curso', 'error');
    }
  });
});

$(document).on('change', '.select-estado-pago', function () {
  var idPedido = $(this).data('id');
  var estado = $(this).val();
  $.ajax({
    url: base_url + 'Curso/PedidosCurso/asignarEstadoPago',
    type: 'POST',
    data: { id_pedido: idPedido, estado_pago: estado },
    dataType: 'json',
    success: function (response) {
      if (response.status === 'success') {
        Swal.fire('¡Guardado!', 'Estado de pago actualizado', 'success');
      } else {
        Swal.fire('Error', response.message || 'No se pudo actualizar el estado', 'error');
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

  $('#aplicar-btn-cotizacion').off('click').on('click', function () {

    if (typeof tableCursoPedidos !== 'undefined' && tableCursoPedidos !== null) {
      if (currentTableCurso === 'alumnos') {
        tableCursoPedidos.ajax.reload(null, false);
      }
      else if (currentTableCurso === 'pagos') {
        tableCursoPagos.ajax.reload(null, false);
      }
    }
  });
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
  table_Campanas = $("#table-campanas");
  table_Campanas.hide();

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
async function configurarBuscador(tableId, searchInputId, infoContainerId) {
  // Esperar a que la tabla esté completamente inicializada
  await new Promise(resolve => setTimeout(resolve, 100));

  // Obtener la instancia de DataTable
  var table = $('#' + tableId).DataTable();

  // Limpiar eventos previos para evitar duplicados
  $('#' + searchInputId).off('keyup input');

  // Escuchar el evento "input" y "keyup" en el buscador personalizado
  $('#' + searchInputId).on('input keyup', function () {
    var searchValue = this.value;
    console.log('Buscando:', searchValue); // Para debug
    table.search(searchValue).draw();
  });

  // Función para limpiar el buscador
  window['resetBuscador_' + tableId] = function () {
    $('#' + searchInputId).val('');
    table.search('').draw();
  };

  // Actualizar el mensaje de información
  table.on('draw', function () {
    if (infoContainerId) {
      var info = table.page.info();
      $('#' + infoContainerId).html(
        `Mostrando ${info.start + 1} a ${info.end} de ${info.recordsTotal} registros`
      );
    }
  });

  // Ocultar el buscador nativo de DataTables para evitar conflictos
  $('#' + tableId + '_filter').hide();
}
async function guardarCambiosPedido(ID_Pedido_Curso) {
  var nuevoImporte = $('#importe_pedido_' + ID_Pedido_Curso).val();
  $.ajax({
    url: base_url + "Curso/PedidosCurso/actualizarImportePedido",
    type: "POST",
    data: { ID_Pedido_Curso: ID_Pedido_Curso, importe: nuevoImporte },
    dataType: "json",
    success: async function (response) {
      if (response.status === "success") {
        Swal.fire('¡Guardado!', response.message, 'success');
        await getCursosHeader();
      } else {
        Swal.fire('Error', response.message, 'error');
      }
    }
  });
}
$(document).on('change', '.select-usuario-externo', function () {
  var estado = $(this).val();
  var idUsuario = $(this).data('id-usuario');
  var idPedido = $(this).data('id-pedido');
  if (estado == '2') {
    // Ejecuta la función de compartir (enviar email Moodle)
    enviarEmailUsuarioMoodle(idUsuario, idPedido);
  }
});

