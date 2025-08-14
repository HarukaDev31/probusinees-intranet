
var url, table_Entidad, div_items = '', iCounter = 1;
//AUTOCOMPLETE
var caractes_no_validos_global_autocomplete = "\"'~!@%^\|";
// Se puede crear un arreglo a partir de la cadena
let search_global_autocomplete = caractes_no_validos_global_autocomplete.split('');
// Solo tomé algunos caracteres, completa el arreglo
let replace_global_autocomplete = ['', '', '', '', '', '', '', '', ''];
const months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
const daysOfWeek = ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'];
var currentTableCurso = 'alumnos';
var tableCursoPagos;
var tableCursoPedidos;
var selectedDaysCampana = [];
var fToday = new Date(), fYear = fToday.getFullYear(), fMonth = fToday.getMonth() + 1, fDay = fToday.getDate();
function showImageModal(url) {
  ///create modal and show image
  const modal = document.createElement('div');
  modal.className = 'modal fade';
  modal.id = 'imageModal';
  modal.tabIndex = -1;
  modal.setAttribute('role', 'dialog');
  const modalDialog = document.createElement('div');
  modalDialog.className = 'modal-dialog modal-dialog-centered';
  const modalContent = document.createElement('div');
  modalContent.className = 'modal-content';
  const modalBody = document.createElement('div');
  modalBody.className = 'modal-body';
  modalBody.innerHTML = `<img src="${url}" alt="Image" class="img-fluid">`;
  modalContent.appendChild(modalBody);
  modalDialog.appendChild(modalContent);
  modal.appendChild(modalDialog);
  document.body.appendChild(modal);
  $(modal).modal('show');
  $(modal).on('hidden.bs.modal', function () {
    $(this).remove(); // Remove modal from DOM after closing
  });
}
async function viewClientePagosCurso(idPedidoCurso, No_Entidad) {
  //show modal with table of pagos coordination
  $("#modalClientePagosCoordination").modal("show");
  $("#modalClientePagosCoordination .modal-title").text(`Ver adelantos de - ${No_Entidad}`);
  url =
    base_url + "Curso/PedidosCurso/getPagosCurso/" + idPedidoCurso;
  if (!$.fn.DataTable.isDataTable("#table-pagos-tracking-coordinacion")) {
    tableCotizacionTrackingPagos = $("#table-pagos-tracking-coordinacion").DataTable({
      dom:
        "<'row'<'col-sm-12 col-md-7'B><'col-sm-12 col-md-4'f><'col-sm-12 col-md-1'>>" +
        "<'row'<'col-sm-12'tr>>" +
        "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-5'i><'col-sm-12 col-md-5'p>>",
      buttons: [],
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
        sInfo: "Mostrando (_START_ - _END_) total de registros _TOTAL_",
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
      order: [[1, "asc"]],
      ajax: {
        url: url,
        type: "POST",
        dataType: "JSON",
        data: function (data) {

        },
      },
      initComplete: function () {
      },
      complete: function () {


      },
      drawCallback: function (settings) {

      },
    });
  } else {
    tableCotizacionTrackingPagos.ajax.url(url).load();
  }

}

function abrirModalPagoCurso(idPedido, nombreCliente) {
  $('#modalPagoCursoLabel').text(`Registrar Pago de Curso - ${nombreCliente}`);
  $('#id-pedido-curso').val(idPedido);
  $('#form-pago-curso')[0].reset();
  $('#modal-pago-curso').modal('show');
  // Inicializa el input personalizado
  initSetupSingleFileUpload(
    "single-file-upload-pagos",
    "file-input-pagos",
    ['pdf', 'docx', 'xlsx', 'xls', 'xlsm', 'csv', 'xlsb', 'xltx', 'xlt', 'png', 'jpg', 'jpeg'],
    '.upload-button-pagos'
  );
  // Establecer la fecha de hoy en el campo fecha
  const hoy = new Date().toISOString().split('T')[0];
  $('#fecha_pago').val(hoy);
}

// Enviar el formulario por AJAX
$('#form-pago-curso').on('submit', function (e) {
  e.preventDefault();
  const fileInput = document.getElementById('file-input-pagos');
  if (!fileInput.files || fileInput.files.length === 0) {
    $('.file-upload-box').addClass('border-danger');
    Swal.fire("Error", "Debes seleccionar un archivo de voucher.", "warning");
    return;
  }
  const formData = new FormData(this);
  $.ajax({
    url: base_url + "Curso/PedidosCurso/saveClientePagosCurso",
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    success: function (response) {
      const result = typeof response === "string" ? JSON.parse(response) : response;
      if (result.status === "success") {
        Swal.fire("Correcto!", result.message, "success");
        $('#modal-pago-curso').modal('hide');
        tableCursoPagos.ajax.reload();
      } else {
        Swal.fire("Error!", result.message, "error");
      }
    }
  });
});

function eliminarPagoCurso(idPagoCurso) {
  Swal.fire({
    title: "¿Estás seguro?",
    text: "Esta acción eliminará el pago.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, eliminar",
    cancelButtonText: "Cancelar"
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: base_url + "Curso/PedidosCurso/eliminarPagoCurso/" + idPagoCurso,
        type: "POST",
        dataType: "json",
        success: function (response) {
          if (response.status === "success") {
            Swal.fire("Eliminado", response.message, "success");
            // Recarga las tablas
            if (typeof tableCursoPagos !== "undefined" && tableCursoPagos && typeof tableCursoPagos.ajax !== "undefined") tableCursoPagos.ajax.reload();
            if (typeof tableClientesPagos !== "undefined" && tableClientesPagos && typeof tableClientesPagos.ajax !== "undefined") tableClientesPagos.ajax.reload();
            if (typeof tableCotizacionPagos !== "undefined" && tableCotizacionPagos && typeof tableCotizacionPagos.ajax !== "undefined") tableCotizacionPagos.ajax.reload();
            if (typeof tableCotizacionTrackingPagos !== "undefined" && tableCotizacionTrackingPagos && typeof tableCotizacionTrackingPagos.ajax !== "undefined") tableCotizacionTrackingPagos.ajax.reload();
          } else {
            Swal.fire("Error", response.message, "error");
          }
        }
      });
    }
  });
}

$(function () {
  tableCursoPagos = $("#table-curso-pagos");
  tableCursoPedidos = $("#table-curso-pedidos");
  $(".tab-curso").removeClass("active");
  $(".tab-curso").off("click").click(async function () {

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
        limpiarFiltrosContenedor();

        $("#table-curso-pedidos").show();
        $("#table-curso-pedidos_wrapper").show();
        tableCursoPedidos.ajax.reload();
      } else {
        $("#txt-Fe_Inicio").val(ParseDateString(new Date(new Date().setMonth(new Date().getMonth() - 2)).toLocaleDateString('es-ES', {
          day: '2-digit',
          month: '2-digit',
          year: 'numeric'
        }), 'fecha', '/'));
        $("#txt-Fe_Fin").val(ParseDateString(new Date(new Date().setDate(new Date().getDate() + 1)).toLocaleDateString('es-ES', {
          day: '2-digit',
          month: '2-digit',
          year: 'numeric'
        }), 'fecha', '/'));
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
              data.sMethod = $('#hidden-sMethod').val();
              data.estado_pago = $('#cbo-filtro-estado_pago').val();
              data.Filtro_Fe_Inicio = $('#txt-Fe_Inicio').val() == "" 
                ? new Date(new Date().setFullYear(new Date().getFullYear() - 5)).toISOString().slice(0, 10)
                : $('#txt-Fe_Inicio').val().split('/').reverse().join('-');
              data.Filtro_Fe_Fin = $('#txt-Fe_Fin').val() == "" 
                ? new Date(new Date().setDate(new Date().getDate() + 1)).toISOString().slice(0, 10)
                : $('#txt-Fe_Fin').val().split('/').reverse().join('-');
              data.tipoTabla = "alumnos";
              data.campana = $('#txt-ID_Campana_Curso').val() ?? 0;
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
          'lengthMenu': [[100, 1000, -1], [100, 1000, "Todos"]],
        });
        tableCursoPedidos.on('draw', async function () {
          await getCursosHeader();
        });

        // FUNCION PARA CONFIGURAR EL BUSCADOR


        $("#table-curso-pedidos").show();

      }
      tableCursoPedidos.on('draw', async function () {
        initConfigurarBuscador(
          "table-curso-pedidos",
          "search-table",
          "table-curso-pedidos_info"
        );
        $('.input-report').datepicker({
          autoclose: true,
          //startDate : new Date(fYear, fToday.getMonth(), '01'),
          todayHighlight: true,
          dateFormat: 'yyyy-mm-dd',
          format: 'yyyy-mm-dd',
        });
      })
    }

    else if (table == "pagos") {
      currentTableCurso = "pagos";
      $("#table-curso-pedidos").hide();
      $("#table-curso-pedidos_wrapper").hide();

      if ($.fn.DataTable.isDataTable("#table-curso-pagos")) {
        limpiarFiltrosContenedor();
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
          'createdRow': function (row, data, dataIndex) {
            // Por ejemplo, para la columna de importe (busca el índice correcto)
            $('td', row).eq(5).addClass('w-20');
          },
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
              data.Filtro_Fe_Inicio = $('#txt-Fe_Inicio').val() == "" 
                ? new Date(new Date().setFullYear(new Date().getFullYear() - 5)).toISOString().slice(0, 10)
                : $('#txt-Fe_Inicio').val().split('/').reverse().join('-');
              data.Filtro_Fe_Fin = $('#txt-Fe_Fin').val() == "" 
                ? new Date(new Date().setDate(new Date().getDate() + 1)).toISOString().slice(0, 10)
                : $('#txt-Fe_Fin').val().split('/').reverse().join('-');
              data.tipoTabla = "pagos";
              data.campana = $('#txt-ID_Campana_Curso').val() ?? 0;


            },
          },
        });
        tableCursoPagos.on('draw', async function () {
          initConfigurarBuscador(
            "table-curso-pagos",
            "search-table",
            "table-curso-pagos_info"
          );
          $('.input-report').datepicker({
            autoclose: true,
            //startDate : new Date(fYear, fToday.getMonth(), '01'),
            todayHighlight: true,
            dateFormat: 'yyyy-mm-dd',
            format: 'yyyy-mm-dd',
          });
        })
        // FUNCION PARA CONFIGURAR EL BUSCADOR

        //Funcion para exportar a excel

        $("#table-curso-pagos").show();

      }

    }
    getCampanasActivas();
  });
  $(".tab-curso").first().click();

  $('.input-report').datepicker({
    autoclose: true,
    //startDate : new Date(fYear, fToday.getMonth(), '01'),
    todayHighlight: true,
    dateFormat: 'yyyy-mm-dd',
    format: 'yyyy-mm-dd',
  });


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

async function crearUsuarioCursosMoodle(id, ID_Pedido_Curso) {
  event.preventDefault();
  url = base_url + 'Curso/PedidosCurso/crearUsuarioCursosMoodle/' + id + '/' + ID_Pedido_Curso;
  await $.ajax({
    url: url,
    type: "GET",
    dataType: "JSON",
    success: async function (response) {
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
        if (response.debug_data) {
          //usuario existe show No_Password  No_Usuario
          $('#moda-message-content').addClass('bg-warning');
          $('.modal-title-message').text(`Usuario ya existe en Moodle,
            Usuario: ${response.debug_data.No_Usuario || 'No disponible'},
            Password: ${response.debug_data.No_Password || 'No disponible'}.
            `);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 4100);
          await enviarEmailUsuarioMoodle(id, ID_Pedido_Curso);
          return;
        }
        $('#moda-message-content').addClass('bg-danger');
        $('.modal-title-message').text(response.message);
        setTimeout(function () { $('#modal-message').modal('hide'); }, 4100);
      }
      await enviarEmailUsuarioMoodle(id, ID_Pedido_Curso);

    }
  });

}

async function enviarEmailUsuarioMoodle(id, ID_Pedido_Curso) {
  event.preventDefault();
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
        console.log(response, "console log");

        $('#moda-message-content').addClass('bg-' + response.status);
        $('.modal-title-message').text(response.message);
        setTimeout(function () { $('#modal-message').modal('hide'); }, 2100);
      } else {
        $('#moda-message-content').addClass('bg-danger');
        $('.modal-title-message').text(response.debug_data || response.message);
        setTimeout(function () { $('#modal-message').modal('hide'); }, 4100);
      }
    }
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
        if (response.data.nu_estado_usuario_externo == "2") {
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
        if(response.data.url_constancia){
          console.log(response.data.url_constancia, "console log");
          $('#btn-descargar-constancia').html(
            `<button type="button" class="btn btn-primary px-5 py-3" onclick="window.open('${response.data.url_constancia}', '_blank')">
                <i class="fa fa-download"></i> Descargar Constancia
            </button>`
          );
        }else {
          $('#btn-descargar-constancia').empty();
          console.log("No hay constancia disponible");
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
                 
                </div>
              </div>
            </div>
            
        `);

        $('#section-datos-cliente').show();

        $('#btn-editar-cliente').on('click', function () {
          event.preventDefault();
          $('.cliente-input').prop('readonly', false);
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
          event.preventDefault();
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
    dateFormat: 'yyyy-mm-dd',
    format: 'yyyy-mm-dd',
  });
  // Oculta la sección de campañas y cursos
  $('#section-campanas-cursos').hide();
  $('.dropdown-menu').on('click', function (event) {
    event.stopPropagation(); // Evita que el evento se propague
  });
  getCampanasActivas();

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
  $("#txt-Fe_Inicio").val('');
  $("#txt-Fe_Fin").val('');
  $("#txt-ID_Campana_Curso").val('0');
  $("#txt-ID_Estado").val('0');
  //emit event to set search input to empty
  $(".search-table").val('');
  $(".search-table").trigger('input');
  if (currentTableCurso === 'alumnos') {
    $("#cbo-filtro-estado_pago").val('0').trigger('change');
    tableCursoPedidos.ajax.reload(null, false);
  }
  else if (currentTableCurso === 'pagos') {
    $("#cbo-filtro-estado_pago").val('0').trigger('change');
    tableCursoPagos.ajax.reload(null, false);
  }


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
    "lengthChange": true,
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
  initConfigurarBuscador('table-campanas', 'search-table-campanas', 'table-campanas_info');
}


function editarCampana(id) {
  $('.month-selector').empty().off('click'); // Eliminar eventos también
  $('.month-container').empty();

  // Limpiar las fechas seleccionadas
  $('#fecha-inicio-campana-span, #fecha-fin-campana-span').text('--/--/----');
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

        $('#modal-nueva-campana').modal('show');
        const months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        const currentDate = new Date();
        const currentMonth = currentDate.getMonth();
        const currentYear = currentDate.getFullYear();

        months.forEach((month, index) => {
          $('.month-selector').append(`
      <div class="form-group">
        <span data-month="${index}" class="${index < currentMonth ? 'month-span-disabled' : 'month-span'}">
          ${month}
        </span>
      </div>
    `);
        });

        // Usar delegación de eventos para los clicks
        $('.month-selector').on('click', '.month-span:not(.month-span-disabled)', function () {
          const monthIndex = $(this).data('month');
          const year = currentYear;

          if ($(this).hasClass('selected')) {
            $(this).removeClass('selected');
            $(`.month-container [data-month="${monthIndex}"]`).remove();
          } else {
            if ($('.month-container').children().length >= 2) {
              Swal.fire({
                icon: 'warning',
                title: 'Solo puedes seleccionar dos meses',
                text: 'Por favor, deselecciona un mes antes de seleccionar otro.',
              });
              return;
            }
            $(this).addClass('selected');

            // Obtener primer y último día del mes seleccionado
            const firstDay = new Date(year, monthIndex, 1);
            const lastDay = new Date(year, monthIndex + 1, 0);

            // Generar el calendario
            const calendarHtml = `
        <div class="month-calendar" data-month="${monthIndex}">
          <h4 class="text-center mb-3">${months[monthIndex]} ${year}</h4>
          ${createHtmlCalendar(firstDay, lastDay)}
        </div>
      `;

            $(".month-container").append(calendarHtml);
          }
        });
        //from fe_inicio and fe_fin select one or two months click months
        const feInicio = new Date(response.data.Fe_Inicio);
        const feFin = new Date(response.data.Fe_Fin);
        //click on months with data-month=fe_inicio.getMonth() and fe_fin.getMonth()
        if (feInicio.getMonth() === feFin.getMonth()) {
          $(`.month-selector .month-span[data-month="${feInicio.getMonth()}"]`).trigger('click');
        } else {
          $(`.month-selector .month-span[data-month="${feInicio.getMonth()}"]`).trigger('click');
          $(`.month-selector .month-span[data-month="${feFin.getMonth()}"]`).trigger('click');
        }
        const days = response.data.dias;
        if (days) {
          const parseDays = JSON.parse(days);
          parseDays.forEach(date => {
            console.log(date, "date");
            console.log(Date.parse(date.fecha), "date fecha");
            const year = date.fecha.split('-')[0];
            const month = date.fecha.split('-')[1] - 1; // Los meses en
            // JavaScript son 0-indexados
            const day = date.fecha.split('-')[2];
            $(`.month-container .day-button[data-day="${day}"][data-month="${month}"][data-year="${year}"]`).trigger('click')
          });
        }

        table_Campanas.ajax.reload();
      } else {
        Swal.fire('Error', 'No se pudo obtener la campaña', 'error');
      }
    }
  });
}


function createHtmlCalendar(startDate, endDate) {
  const daysOfWeek = ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'];
  let calendarHtml = [];
  calendarHtml.push('<div class="calendar-grid">');

  // Encabezados de días
  daysOfWeek.forEach(day => {
    calendarHtml.push(`<div class="day-header">${day}</div>`);
  });

  // Rellenar espacios hasta el primer día del mes
  let current = new Date(startDate);
  for (let i = 0; i < current.getDay(); i++) {
    calendarHtml.push('<div class="day-empty"></div>');
  }

  // Agregar los días del mes
  while (current <= endDate) {
    const isToday = current.getDate() === new Date().getDate() &&
      current.getMonth() === new Date().getMonth() &&
      current.getFullYear() === new Date().getFullYear();

    calendarHtml.push(`
      <div class="day-cell">
        <button class="day-button ${false ? 'day-today' : ''}" 
                data-day="${current.getDate()}" 
                data-month="${current.getMonth()}" 
                data-year="${current.getFullYear()}">
          ${current.getDate()}
        </button>
      </div>
    `);

    current.setDate(current.getDate() + 1);
  }

  // Rellenar espacios hasta el final de la semana
  if (current.getDay() !== 0) {
    while (current.getDay() > 0) {
      calendarHtml.push('<div class="day-empty"></div>');
      current.setDate(current.getDate() + 1);
    }
  }

  calendarHtml.push('</div>');
  return calendarHtml.join('');
}

$('.month-container').on('click', '.day-button', function (event) {
  event.stopPropagation();
  event.preventDefault();

  // Obtener datos del día clickeado
  const clickedDay = $(this).data('day');
  const clickedMonth = $(this).data('month');
  const clickedYear = $(this).data('year');
  const clickedDate = new Date(clickedYear, clickedMonth, clickedDay);

  // Verificar si ya está seleccionado
  if ($(this).hasClass('day-selected')) {
    $(this).removeClass('day-selected');
    updateDateSpans(); // Actualizar spans al deseleccionar
    return;
  }

  // Obtener todos los días seleccionados
  const selectedDays = $('.day-selected');

  const selectedInOtherMonths = selectedDays.filter(function () {
    return $(this).data('month') !== clickedMonth;
  });

  if (selectedInOtherMonths.length > 0 && selectedDays.length >= 6) {
    // Deseleccionar el más antiguo
    const oldest = findOldestSelectedDay();
    if (oldest) oldest.removeClass('day-selected');
  }
  // Si estamos en el mismo mes y ya hay 6 selecciones
  else if (selectedDays.length >= 6) {
    // Deseleccionar el más antiguo de este mes
    const oldestInMonth = findOldestSelectedDay(clickedMonth);
    if (oldestInMonth) oldestInMonth.removeClass('day-selected');
  }

  // Seleccionar el nuevo día
  $(this).addClass('day-selected');

  // Actualizar los spans con las fechas
  updateDateSpans();
});

// Función para encontrar el día seleccionado más antiguo
function findOldestSelectedDay(specificMonth = null) {
  let oldestDay = null;
  let oldestDate = new Date(9999, 11, 31); // Fecha futura inicial

  $('.day-selected').each(function () {
    const dayData = $(this).data();
    // Si se especificó un mes, solo considerar días de ese mes
    if (specificMonth !== null && dayData.month !== specificMonth) return;

    const dayDate = new Date(dayData.year, dayData.month, dayData.day);

    if (dayDate < oldestDate) {
      oldestDate = dayDate;
      oldestDay = $(this);
    }
  });

  return oldestDay;
}

// Función para actualizar los spans con las fechas seleccionadas
function updateDateSpans() {
  const selectedDays = getSelectedDaysSorted();
  console.log(selectedDays, "selectedDays");
  // Formatear fechas (ej: "15/06/2024")
  const formatDate = (date) => {
    const day = date.getDate().toString().padStart(2, '0');
    const month = (date.getMonth() + 1).toString().padStart(2, '0');
    return `${day}/${month}/${date.getFullYear()}`;
  };

  // Actualizar los spans según la cantidad de selecciones
  if (selectedDays.length === 0) {
    $('#fecha-inicio-campana-span, #fecha-fin-campana-span').text('--/--/----');
  }
  else if (selectedDays.length === 1) {
    $('#fecha-inicio-campana-span').text(formatDate(selectedDays[0].date));
    $('#fecha-fin-campana-span').text('--/--/----');
  }
  else {
    // Ordenar las fechas cronológicamente
    const sortedDates = selectedDays.map(d => d.date).sort((a, b) => a - b);
    selectedDaysCampana = sortedDates;

    $('#fecha-inicio-campana-span').text(formatDate(sortedDates[0]));
    $('#fecha-fin-campana-span').text(formatDate(sortedDates[selectedDays.length - 1]));
  }
}

// Función auxiliar para obtener días seleccionados ordenados
function getSelectedDaysSorted() {
  const selected = [];

  $('.day-selected').each(function () {
    selected.push({
      element: $(this),
      date: new Date(
        $(this).data('year'),
        $(this).data('month'),
        $(this).data('day')
      )
    });
  });

  // Ordenar por fecha (más antiguo primero)
  selected.sort((a, b) => a.date - b.date);

  return selected;
}
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
//onclick close-modal-comprobante hide modal-image-preview
$(document).on('click', '.close-modal-comprobante', function () {
  event.preventDefault();
  $('#modal-image-preview').modal('hide');
});
$(document).on('click', '.close-modal-pago', function () {
  event.preventDefault();
  $('#modal-pago').modal('hide');
});
function showPago(monto, banco, fecha, voucher_url, id) {
  event.preventDefault();

  // Set basic info
  $('#monto-pago').text('S/ ' + parseFloat(monto).toFixed(2));
  $('#banco-pago').text(banco);
  $('#fecha-pago').text(fecha);

  // Handle voucher URL
  const voucherContainer = $('#voucher-container');
  voucherContainer.empty();
  $('#delete-pago-btn').off('click').on('click', function () {
    event.preventDefault();
    Swal.fire({
      title: '¿Estás seguro?',
      text: "Esta acción eliminará el pago.",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        const formData = new FormData();
        formData.append('idPagoCurso', id);
        $.ajax({
          url: base_url + "Curso/PedidosCurso/borrarPagoCurso",
          type: "POST",
          data: formData,
          processData: false,
          contentType: false,
          dataType: "json",
          success: function (response) {
            if (response.status === "success") {
              Swal.fire('¡Eliminado!', response.message, 'success');
              //close modal
              $('#modal-pago').modal('hide');
              // Reload the table
              if (typeof tableCursoPagos !== 'undefined' && tableCursoPagos !== null
              ) {
                tableCursoPagos.ajax.reload(null, false);
              }
              
            } else {
              Swal.fire('Error', response.message, 'error');
            }
          }
        });
      }
    })
  });
  if (voucher_url) {
    const fileExt = voucher_url.split('.').pop().toLowerCase();
    const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileExt);

    if (isImage) {
      // For images - show thumbnail that opens preview modal
      const imgThumb = $('<div class="cursor-pointer group">')
        .append($('<img>')
          .attr('src', voucher_url)
          .addClass('w-24 h-24 object-cover rounded-lg border border-gray-200 group-hover:border-blue-500 transition')
          .on('click', function () {
            $('#image-preview-comprobante').attr('src', voucher_url);
            $('#download-btn').attr('href', voucher_url);
            $('#modal-image-preview').modal('show');
          })
        )
        .append($('<div class="text-xs text-center mt-1 text-blue-600">Ver comprobante</div>'));

      voucherContainer.append(imgThumb);
    } else {
      // For non-images - show download link with file icon
      const fileIcon = $('<i class="fas fa-file-download text-3xl text-gray-400 mb-2"></i>');
      const downloadLink = $('<a>')
        .attr('href', voucher_url)
        .attr('download', '')
        .addClass('inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500')
        .text('Descargar comprobante');

      voucherContainer.append(fileIcon, $('<div class="mt-2"></div>').append(downloadLink));
    }
  } else {
    voucherContainer.append($('<span class="text-gray-400">Sin comprobante</span>'));
  }

  // Show modal
  $('#modal-pago').modal('show');
}
// Botón para mostrar campañas
$(document).on('click', '#btn-nueva-campana', function () {
  $('#section-listar-pedidos').hide();
  $('#section-campanas-cursos').show();
  $('#contenedor-campanas').html('');
  $('.content-header').html(`
            <div class="container-fluid">
              <div class="row mb-2 px-3 d-flex justify-content-between">
                <div class="col-3 col-xl-2 py-sm-3 py-xl-0 py-md-0">
                  <button type="button" class="bg-white hover:bg-white-200 text-black-200 py-3 px-2 border border-transparent hover:border-orange-600 rounded btn-block btn-reporte btn-back-campanas" onclick="ocultarSectionDatosCliente()"><i class="fa fa-arrow-left"></i> Regresar</button>
                </div>
                <div class="col-xl-3 col-md-2"></div>
                <div class="col-6 col-md-0 col-xl-1">
                </div>
                <div class="col-12 col-md-2 col-xl-1">
                </div>
                <div class="col-12 col-md-0 col-xl-2 justify-content-center d-flex">
                  <div class="col-3 col-xl-10 py-sm-3 py-xl-0 py-md-0">
                    <button id="btn-crear-campana" type="button" class="text-white bg-[#FF500B] py-3 px-2 border border-transparent hover:border-orange-600 rounded btn-block btn-reporte ">Crear <i class="fa fa-plus"></i></button>
                  </div>
                </div>
              </div>
            </div>
            
        `);
  $("#btn-crear-campana").off("click").on("click", function () {
    $("#modal-nueva-campana").modal("show");
    $('#modalNuevaCampanaLabel').text('Crear Campaña');
    $('#form-nueva-campana')[0].reset();
    $('#id-campana-editar').val('');
    //set empty all div in modal-body-nueva-campana
    $('.month-selector').empty().off('click'); // Eliminar eventos también
    $('.month-container').empty();

    // Limpiar las fechas seleccionadas
    $('#fecha-inicio-campana-span, #fecha-fin-campana-span').text('--/--/----');
    const months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    const currentDate = new Date();
    const currentMonth = currentDate.getMonth();
    const currentYear = currentDate.getFullYear();

    months.forEach((month, index) => {
      $('.month-selector').append(`
      <div class="form-group">
        <span data-month="${index}" class="${index < currentMonth ? 'month-span-disabled' : 'month-span'}">
          ${month}
        </span>
      </div>
    `);
    });

    // Usar delegación de eventos para los clicks
    $('.month-selector').on('click', '.month-span:not(.month-span-disabled)', function () {
      const monthIndex = $(this).data('month');
      const year = currentYear;

      if ($(this).hasClass('selected')) {
        $(this).removeClass('selected');
        $(`.month-container [data-month="${monthIndex}"]`).remove();
      } else {
        if ($('.month-container').children().length >= 2) {
          Swal.fire({
            icon: 'warning',
            title: 'Solo puedes seleccionar dos meses',
            text: 'Por favor, deselecciona un mes antes de seleccionar otro.',
          });
          return;
        }
        $(this).addClass('selected');

        // Obtener primer y último día del mes seleccionado
        const firstDay = new Date(year, monthIndex, 1);
        const lastDay = new Date(year, monthIndex + 1, 0);

        // Generar el calendario
        const calendarHtml = `
        <div class="month-calendar" data-month="${monthIndex}">
          <h4 class="text-center mb-3">${months[monthIndex]} ${year}</h4>
          ${createHtmlCalendar(firstDay, lastDay)}
        </div>
      `;

        $(".month-container").append(calendarHtml);
      }
    });
  });
  cargarTablaCampanas();
});





// Guardar la campaña al enviar el formulario
$('#form-nueva-campana').on('submit', function (e) {
  e.preventDefault();

  // Obtener días seleccionados ordenados por fecha
  const selectedDays = [];
  $('.day-selected').each(function () {
    selectedDays.push({
      day: $(this).data('day'),
      month: $(this).data('month') + 1, // Meses de 1-12
      year: $(this).data('year'),
      date: new Date($(this).data('year'), $(this).data('month'), $(this).data('day'))
    });
  });

  // Ordenar por fecha (más antigua primero)
  selectedDays.sort((a, b) => a.date - b.date);

  // Validar que hay al menos un día seleccionado
  if (selectedDays.length === 0) {
    Swal.fire('Error', 'Debes seleccionar al menos un día en el calendario', 'error');
    return;
  }

  const id = $('#id-campana-editar').val();
  const url = id ? base_url + "Curso/PedidosCurso/editarCampana" : base_url + "Curso/PedidosCurso/crearCampana";

  // Crear FormData con todos los campos del formulario
  const form = new FormData(this);

  // Agregar campos adicionales
  form.append("ID_Campana", id || '0');

  // Formatear fechas como YYYY-MM-DD
  const formatDate = (year, month, day) => {
    return `${year}-${month.toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
  };

  // Fecha de inicio (siempre la más antigua)
  const startDate = selectedDays[0];
  form.append("Fe_Inicio", formatDate(startDate.year, startDate.month, startDate.day));

  // Fecha fin (la más reciente si hay más de una selección)
  const endDate = selectedDays.length > 1 ? selectedDays[selectedDays.length - 1] : startDate;
  form.append("Fe_Fin", formatDate(endDate.year, endDate.month, endDate.day));

  // Enviar todos los días seleccionados como array
  const allDates = selectedDays.map(d => formatDate(d.year, d.month, d.day));
  form.append("Dias_Seleccionados", JSON.stringify(allDates));

  // Configuración AJAX
  $.ajax({
    url: url,
    type: "POST",
    data: form,
    processData: false,
    contentType: false,
    dataType: "json",
    success: function (response) {
      if (response.status === "success") {
        $('#modal-nueva-campana').modal('hide');
        Swal.fire('Éxito', response.message, 'success');
        if (typeof table_Campanas !== 'undefined') {
          table_Campanas.ajax.reload(null, false);
        }
      } else {
        Swal.fire('Error', response.message || 'Error al procesar la solicitud', 'error');
      }
    },
    error: function (xhr) {
      let errorMsg = 'Error en la solicitud';
      if (xhr.responseJSON && xhr.responseJSON.message) {
        errorMsg = xhr.responseJSON.message;
      }
      Swal.fire('Error', errorMsg, 'error');
    }
  });
});

// Función auxiliar para obtener días seleccionados ordenados
function getSelectedDaysSorted() {
  const selected = [];

  $('.day-selected').each(function () {
    selected.push({
      day: $(this).data('day'),
      month: $(this).data('month'),
      year: $(this).data('year'),
      date: new Date($(this).data('year'), $(this).data('month'), $(this).data('day'))
    });
  });

  // Ordenar por fecha (más antigua primero)
  selected.sort((a, b) => a.date - b.date);

  return selected;
}

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
  const formData = new FormData();
  formData.append("Filtro_Fe_Inicio", $('#txt-Fe_Inicio').val() == "" || typeof $('#txt-Fe_Inicio').val() === 'undefined'
  ? new Date(new Date().setFullYear(new Date().getFullYear() - 5)).toISOString().slice(0, 10)
  : $('#txt-Fe_Inicio').val().split('/').reverse().join('-'));
formData.append("Filtro_Fe_Fin", $('#txt-Fe_Fin').val() == "" || typeof $('#txt-Fe_Fin').val() === 'undefined'
  ? new Date(new Date().setDate(new Date().getDate() + 1)).toISOString().slice(0, 10)
  : $('#txt-Fe_Fin').val().split('/').reverse().join('-'));
  const response = await fetch(base_url + "Curso/PedidosCurso/getCursosHeader", {
    method: "POST",
    body: formData
  });
  const data = await response.json();
  if (data.status === "success") {
    $("#span-total-importe").text("S/." + (data.data.total_importe));
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
async function eliminarPedido(idPedido) {
  const confirm = await Swal.fire({
    title: '¿Estás seguro?',
    text: "No podrás deshacer esta acción",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar'
  });

  if (confirm.isConfirmed) {
    $.ajax({
      url: base_url + "Curso/PedidosCurso/eliminarPedido/" + idPedido,
      type: "GET",
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          Swal.fire('¡Eliminado!', response.message, 'success');
          if (currentTableCurso === 'alumnos') {
            tableCursoPedidos.ajax.reload(null, false);
          } else if (currentTableCurso === 'pagos') {
            tableCursoPagos.ajax.reload(null, false);
          }
        } else {
          Swal.fire('Error', response.message || 'No se pudo eliminar el pedido', 'error');
        }
      },
      error: function () {
        Swal.fire('Error', 'Error al eliminar el pedido', 'error');
      }
    });
  }
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

async function getCampanasActivas() {
  const url = base_url + "Administracion/Administracion/getCampanasActivas";
  try {
    const response = await fetch(url);
    if (!response.ok) {
      throw new Error("Network response was not ok");
    }
    const data = await response.json();
    if (data.status == 'success') {
      const campanasSelect = $("#txt-ID_Campana_Curso");
      campanasSelect.empty(); // Clear previous options
      //add empty option
      campanasSelect.append('<option value="0">Seleccione una campaña</option>');
      data.data.forEach(campana => {
        campanasSelect.append(`<option value="${campana.ID_Campana}">${campana.nombre_campana}</option>`);
      });
    } else {
      console.error("Error fetching active campaigns:", data.message);
    }
  } catch (error) {
    console.error("Error fetching active campaigns:", error);
  }
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




$(document).on('change', '.select-usuario-externo', async function () {
  var estado = $(this).val();
  var idUsuario = $(this).data('id-usuario');
  var idPedido = $(this).data('id-pedido');
  if (estado == '2') {
    try {
      await crearUsuarioCursosMoodle(idUsuario, idPedido);
    } catch (error) {
      console.error("Error al crear usuario en Moodle:", error);
    }
  }
});
