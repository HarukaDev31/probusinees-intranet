var url, table_Entidad, div_items = '', iCounter = 1;
//AUTOCOMPLETE
var caractes_no_validos_global_autocomplete = "\"'~!@%^\|";
// Se puede crear un arreglo a partir de la cadena
let search_global_autocomplete = caractes_no_validos_global_autocomplete.split('');
// Solo tomé algunos caracteres, completa el arreglo
let replace_global_autocomplete = ['', '', '', '', '', '', '', '', ''];
//28 caracteres
// FIN AUTOCOMPLETE

var fToday = new Date(), fYear = fToday.getFullYear(), fMonth = fToday.getMonth() + 1, fDay = fToday.getDate();

$(function () {
  //Date picker invoice
  $( '.input-report' ).datepicker({
    autoclose : true,
    //startDate : new Date(fYear, fToday.getMonth(), '01'),
    todayHighlight  : true,
    dateFormat: 'dd/mm/yyyy',
    format: 'dd/mm/yyyy',
  });

  url = base_url + 'Curso/PedidosCurso/ajax_list';
  table_Entidad = $("#table-Pedidos").DataTable({
    dom: "<'row'<'col-sm-12 col-md-4'B><'col-sm-12 col-md-7'f><'col-sm-12 col-md-1'>>" +
    "<'row'<'col-sm-12'tr>>" +
    "<'row'<'col-sm-12 col-md-2'l><'col-sm-12 col-md-5'i><'col-sm-12 col-md-5'p>>",
    buttons     : [{
      extend    : 'excel',
      text      : '<i class="fa fa-file-excel color_icon_excel"></i> Excel',
      titleAttr : 'Excel',
      exportOptions: {
        columns: ':visible'
      },
      attr:{
        class:"hidden"
      }
    },
    {
      extend    : 'pdf',
      text      : '<i class="fa fa-file-pdf color_icon_pdf"></i> PDF',
      titleAttr : 'PDF',
      exportOptions: {
        columns: ':visible'
      },
      attr:{
        class:"hidden"
      }
    },
    {
      extend    : 'colvis',
      text      : '<i class="fa fa-ellipsis-v"></i> Columnas',
      titleAttr : 'Columnas',
      exportOptions: {
        columns: ':visible'
      },
      attr:{
        class:"hidden"
      }
    },
    {
      text:"Alumnos",
      action: function(){
      },
      className: "btn btn-light",
    },
    {
      text:"Pagos",
      action: function(){
      }
    },
  ],
    'searching'   : true,
    'bStateSave'  : true,
    "lengthChange": true,
    'processing'  : true,
    'serverSide'  : false,
    'info'        : true,
    'autoWidth'   : false,
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
        data.sMethod = $('#hidden-sMethod').val(),
        data.estado_pago = $( '#cbo-filtro-estado_pago' ).val(),
        data.Filtro_Fe_Inicio = ParseDateString($( '#txt-Fe_Inicio' ).val(), 'fecha', '/'),
        data.Filtro_Fe_Fin = ParseDateString($( '#txt-Fe_Fin' ).val(), 'fecha', '/');
        
      },
    },
    'columnDefs': [
      {
        targets: 'no-hidden',
        visible: false, 
      },{
      className : 'text-center',
      targets   : 'no-sort',
      orderable : false,
    },{
          targets: "",
          orderable: false,
        },],
    'lengthMenu': [[10, 100, 1000, -1], [10, 100, 1000, "Todos"]],
  });
  configurarBuscador('table-Pedidos', 'search-table', 'info-container');

  $('#table-Pedidos_filter input').removeClass('form-control-sm');
  $('#table-Pedidos_filter input').addClass('form-control-md');
  $('#table-Pedidos_filter input').addClass("width_full");

  $('#btn-html_reporte').click(function () {
    reload_table_Entidad();
  });
})

function reload_table_Entidad(){
  table_Entidad.ajax.reload(null,false);
}

function crearUsuarioCursosMoodle(id, ID_Pedido_Curso) {
  var $modal_delete = $('#modal-message-delete');
  $modal_delete.modal('show');

  $('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
  $('.modal-message-delete').addClass('modal-success');

  $('#modal-title').text('¿Deseas crear usuario Moodle?');

  $('#btn-save-delete').off('click').click(function () {
    
    $( '#btn-save-delete' ).text('');
    $( '#btn-save-delete' ).attr('disabled', true);
    $( '#btn-save-delete' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

    url = base_url + 'Curso/PedidosCurso/crearUsuarioCursosMoodle/' + id + '/' + ID_Pedido_Curso;
    $.ajax({
      url: url,
      type: "GET",
      dataType: "JSON",
      success: function (response) {
        $modal_delete.modal('hide');
        $( '#btn-save-delete' ).text('');
        $( '#btn-save-delete' ).append( 'Aceptar' );
        $( '#btn-save-delete' ).attr('disabled', false);

        $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
        $('#modal-message').modal('show');

        if (response.status == 'success') {
          $('#moda-message-content').addClass( 'bg-' + response.status);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 2100);
          reload_table_Entidad();
        } else {
          $('#moda-message-content').addClass( 'bg-danger' );
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
    
    $( '#btn-save-delete' ).text('');
    $( '#btn-save-delete' ).attr('disabled', true);
    $( '#btn-save-delete' ).append( 'Enviando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );

    url = base_url + 'Curso/PedidosCurso/enviarEmailUsuarioMoodle/' + id + '/' + ID_Pedido_Curso;
    $.ajax({
      url: url,
      type: "GET",
      dataType: "JSON",
      success: function (response) {
        $modal_delete.modal('hide');
        $( '#btn-save-delete' ).text('');
        $( '#btn-save-delete' ).append( 'Aceptar' );
        $( '#btn-save-delete' ).attr('disabled', false);

        $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
        $('#modal-message').modal('show');

        if (response.status == 'success') {
          $('#moda-message-content').addClass( 'bg-' + response.status);
          $('.modal-title-message').text(response.message);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 2100);
        } else {
          $('#moda-message-content').addClass( 'bg-danger' );
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
        if(response.data.usuario_moodle && response.data.password_moodle){
          $('#acceso-aula-virtual').show();
          $('#cliente-moodle-usuario').val(response.data.usuario_moodle);
          $('#cliente-moodle-password').val(response.data.password_moodle);
        } else{
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
                  <div id="btn-guardar-cliente" class="col-3 col-xl-10 py-sm-3 py-xl-0 py-md-0">
                    <button type="button" class="text-white bg-[#fd7e14] py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block btn-reporte btn-back-cotizacion"><i class="fa fa-save"></i> Guardar</button>
                  </div>
                </div>
              </div>
            </div>
            
        `);

        $('#section-listar-pedidos').hide();
        $('#section-datos-cliente').show();

        $('#btn-editar-cliente').on('click', function() {
          $('.cliente-input').prop('readonly', false);
          $('#cliente-moodle-usuario').prop('readonly', true);
          $('#cliente-moodle-password').prop('readonly', true);
          $('#btn-guardar-cliente').show();
          $('#btn-cancel').show();
          $(this).hide();
        });
        $('#btn-cancel').on('click', function() {
          $('.cliente-input').prop('readonly', true);
          $('#btn-editar-cliente').show();
          $(this).hide();
        });
        $('#btn-guardar-cliente').on('click', function() {
          $('.cliente-input').prop('readonly', true);
          $('#btn-editar-cliente').show();
          // Aquí tu lógica para guardar
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

  // Muestra la sección de la tabla y oculta la de cliente
  $('#section-listar-pedidos').show();
  $('#section-datos-cliente').hide();
}


let headerOriginal = '';
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