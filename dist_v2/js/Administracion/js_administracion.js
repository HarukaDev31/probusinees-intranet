var paymentSection;
var sectionListaPedidos;
var currentCotizacion = 0;
var aPagar = 0;
var pagado = 0;
var currentCurso = 0;
var currentCliente = "";
var currentTableCurso = "consolidado"; // Default table type
var tableCursoPedidos;
var tableCursoPagos;
async function configurarBuscador(tableId, searchInputClass, infoContainerId) {
  // Obtener la instancia de DataTable
  var table = $("#" + tableId).DataTable();
  console.log(table);

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
function limpiarFiltrosTabla() {
  $("#txt-Fe_Inicio").val(ParseDateString(new Date(new Date().setMonth(new Date().getMonth() - 2)).toLocaleDateString('es-ES', { year: 'numeric', month: '2-digit', day: '2-digit' })));
  $("#txt-Fe_Fin").val(ParseDateString(new Date().toLocaleDateString('es-ES', { year: 'numeric', month: '2-digit', day: '2-digit' })));
  $("#txt-ID_Estado_Cotizacion").val('0');
  $("#txt-ID_Campana").val('0');
  if (currentTableCurso === 'consolidado') {
    $("#cbo-filtro-estado_pago").val('0').trigger('change');
    tableCursoPedidos.ajax.reload(null, false);
  }
  else if (currentTableCurso === 'curso') {
    $("#cbo-filtro-estado_pago").val('0').trigger('change');
    tableCursoPagos.ajax.reload(null, false);
  }

  //dropdown-menu  remove show class
  $('.dropdown-menu').removeClass('show');

}
async function backToList() {
  sectionListaPedidos.show(); // Show the section with the list of orders
  paymentSection.hide(); // Hide the payment section
  currentCotizacion = 0; // Reset current cotizacion ID
  currentCurso = 0; // Reset current course ID
  aPagar = 0; // Reset amount to be paid
  pagado = 0; // Reset amount already paid
  $("#payment-tracking-section-cards").empty();
  if (currentTableCurso == "consolidado") {
    tableCursoPedidos.ajax.reload(null, false); // Reload the consolidated payments table
  }
  else if (currentTableCurso == "curso") {
    tableCursoPagos.ajax.reload(null, false); // Reload the course payments table
  }
}
async function viewDetailsPagosCurso(idPedidoCurso, apagar, pago, nombreCliente) {
  currentCliente = nombreCliente ?? currentCliente; // Store the current client name
  currentCurso = idPedidoCurso; // Store the current course ID
  aPagar = apagar; // Store the amount to be paid
  pagado = pago; // Store the amount already paid
  sectionListaPedidos.hide(); // Hide the section with the list of orders
  $("#payment-tracking-title").text(`${nombreCliente}`); // Set the title of the payment section
  const paymentSectionCards = $("#payment-tracking-section-cards");
  paymentSectionCards.empty(); // Clear previous content
  const url = base_url + "Administracion/Administracion/getDetailsPagosCurso/" + idPedidoCurso;
  try {
    const response = await fetch(url);
    if (!response.ok) {
      throw new Error("Network response was not ok");
    }
    const data = await response.json();

    if (data.status == 'success') {
      $("#total-amount").text(`S/.${Number(apagar).toFixed(2)}`);
      $("#paid-amount").text(`S/.${Number(pago).toFixed(2)}`);
      console.log(data.data.nota);
      let index = 1; // Initialize index for payment cards
      $("#nota").val(data.data.nota || ""); // Set the note input value
      data.data.data.forEach(detail => {

        paymentSectionCards.append(`<div class="payment-card ${detail.status == "CONFIRMADO" ? "bg-green-100" : detail.status == "OBSERVADO" ? "bg-red-100" : "bg-white-100"
          } rounded-xl ">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Pago ${index}</h3>
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <span class="text-gray-600 w-20">Monto:</span>
                            <input disabled type="text" value="${Number((detail.monto)).toFixed(2)}" class="bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500" readonly>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-gray-600 w-20">Banco:</span>
                            <input disabled type="text" value="${detail.banco}" class="bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500" readonly>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-gray-600 w-20">Voucher:</span>
                            ${getIconByExtension(detail.voucher_url)}
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-gray-600 w-20">Fecha:</span>
                            <input disabled type="text" value="${detail.payment_date}" class="bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500" readonly>
                        </div>
                    </div>
                    <select class="form-select mt-4 w-100" id="cbo-estado-pago-${detail.id}" onchange="confirmPayment('${detail.id}', this.value, 'handlePaymentCurso')">
                      <option value="PENDIENTE" ${detail.status == "PENDIENTE" ? "selected" : ""} class="bg-light">Pendiente</option>
                      <option value="CONFIRMADO" ${detail.status == "CONFIRMADO" ? "selected" : ""} class="bg-success">Confirmado</option>
                      <option value="OBSERVADO" ${detail.status == "OBSERVADO" ? "selected" : ""} class="bg-danger">Observado</option>
                  </select>
                </div>
            </div>`);
        index++; // Increment index for next payment card
      });
      const cotizacionSection = $("#cotizaciones-container");
      cotizacionSection.empty(); // Clear previous content in cotizacion section
      cotizacionSection.hide(); // Hide the cotizacion section initially
      paymentSection.show(); // Show the payment section
    }
  } catch (error) {
    console.error("Error fetching payment details:", error);
  }
}
async function viewDetailsPagosConsolidado(idCotizacion, apagar, pago, nombreCliente) {
  currentCotizacion = idCotizacion; // Store the current cotizacion ID
  aPagar = apagar; // Store the amount to be paid
  pagado = pago; // Store the amount already paid
  currentCliente = nombreCliente ?? currentCliente; // Store the current client name
  sectionListaPedidos.hide(); // Hide the section with the list of orders
  $("#payment-tracking-title").text(`${nombreCliente}`); // Set the title of the payment section
  const paymentSectionCards = $("#payment-tracking-section-cards");
  $("#cotizaciones-container").show(); // Show the cotizaciones section
  paymentSectionCards.empty(); // Clear previous content
  const url = base_url + "Administracion/Administracion/getDetailsPagosConsolidado/" + idCotizacion;
  try {
    const response = await fetch(url);
    if (!response.ok) {
      throw new Error("Network response was not ok");
    }
    const data = await response.json();

    if (data.status == 'success') {
      $("#total-amount").text(`$${Number(pago).toFixed(2)}`);
      $("#paid-amount").text(`$${Number(apagar).toFixed(2)}`);
      console.log(data.data.nota);
      let index = 1; // Initialize index for payment cards
      $("#nota").val(data.data.nota || ""); // Set the note input value
      data.data.data.forEach(detail => {

        paymentSectionCards.append(`<div class="payment-card ${detail.status == "CONFIRMADO" ? "bg-green-100" : detail.status == "OBSERVADO" ? "bg-red-100" : "bg-white-100"
          } rounded-xl ">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Pago ${index}</h3>
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <span class="text-gray-600 w-20">Monto:</span>
                            <input disabled type="text" value="${Number((detail.monto)).toFixed(2)}" class="bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500" readonly>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-gray-600 w-20">Banco:</span>
                            <input disabled type="text" value="${detail.banco}" class="bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500" readonly>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-gray-600 w-20">Voucher:</span>
                            ${getIconByExtension(detail.voucher_url)}
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-gray-600 w-20">Fecha:</span>
                            <input disabled type="text" value="${detail.payment_date}" class="bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500" readonly>
                        </div>
                    </div>
                     <select class="form-select mt-4 w-100" id="cbo-estado-pago-${detail.id}" onchange="confirmPayment('${detail.id}', this.value, 'handlePayment')">
                      <option value="PENDIENTE" ${detail.status == "PENDIENTE" ? "selected" : ""} class="bg-light">Pendiente</option>
                      <option value="CONFIRMADO" ${detail.status == "CONFIRMADO" ? "selected" : ""} class="bg-success">Confirmado</option>
                      <option value="OBSERVADO" ${detail.status == "OBSERVADO" ? "selected" : ""} class="bg-danger">Observado</option>
                  </select>
                </div>
            </div>`);
        index++; // Increment index for next payment card
      });
      const cotizacionContainer = $("#cotizaciones-container");

      const cotizacionSection = $("#cotizacion-card");
      cotizacionSection.empty(); // Clear previous content in cotizacion section
      const cotizacionInicial = data.data.cotizacion_inicial_url;
      if (cotizacionInicial) {
        cotizacionSection.append(`<button 
          onclick="window.open('${cotizacionInicial}', '_blank')"
          class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold rounded-lg py-3 transition-all duration-300 flex items-center justify-center gap-2">
                        <span>COTIZACION INICIAL</span>
                        <i class="fas fa-file-invoice"></i>
                    </button>`);
      }
      const cotizacionFinal = data.data.cotizacion_final_url;
      if (cotizacionFinal) {
        cotizacionSection.append(`<button
          onclick="window.open('${cotizacionFinal}', '_blank')"
          class="w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold rounded-lg py-3 transition-all duration-300 flex items-center justify-center gap-2">
                        <span>COTIZACION FINAL</span>
                        <i class="fas fa-file-invoice"></i>
                    </button>`);
      }
    }
    paymentSection.show(); // Show the payment section

  } catch (error) {
    console.error("Error fetching payment details:", error);
  }
}
async function saveNote() {
  const note = $("#nota").val().trim();
  if (note.length === 0) {
    alert("Por favor, ingrese una nota.");
    return;
  }

  let url = base_url + "Administracion/Administracion/";
  if (currentCurso > 0) {
    url += "saveNoteCurso";
  }
  else {
    url += "saveNote";
  }
  try {
    const formData = new FormData();
    formData.append("idCotizacion", currentCotizacion == 0 ? currentCurso : currentCotizacion);
    formData.append("note", note);

    const response = await fetch(url, {
      method: "POST",
      body: formData
    });
    if (!response.ok) {
      throw new Error("Network response was not ok");
    }
    const data = await response.json();
    console.log(data);
    if (data.status == 'success') {
      alert("Nota guardada exitosamente.");
      $("#note-input").val(""); // Clear the input field
      if (currentCurso > 0) {
        viewDetailsPagosCurso(currentCurso, aPagar, pagado, currentCliente); // Refresh the payment details for course
      } else {

        viewDetailsPagosConsolidado(currentCotizacion, aPagar, pagado, currentCliente);
      }// Refresh the payment details
    } else {
      alert("Error al guardar la nota: " + data.message);
    }
  } catch (error) {
    console.error("Error saving note:", error);
  }
}
async function getTableHeaders(table) {
  url = base_url + "Administracion/Administracion/";
  if (table == "consolidado") {
    url += "getHeadersConsolidado";
  } else if (table == "curso") {
    url += "getHeadersCurso";
  }
  try {
    const formData = new FormData();
    formData.append("Filtro_Fe_Inicio", ParseDateString($('#txt-Fe_Inicio').val() == "" ?
      //fin inicio 2 meses antes
      new Date(new Date().setMonth(new Date().getMonth() - 2)).toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
      })
      : $('#txt-Fe_Inicio').val(), 'fecha', '/'),
    );
    formData.append("Filtro_Fe_Fin", ParseDateString($('#txt-Fe_Fin').val() == "" ?
      new Date(new Date().setDate(new Date().getDate() + 1)).toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
      })
      : $('#txt-Fe_Fin').val(), 'fecha', '/'),
    );
    const response = await fetch(url,
      {
        method: "POST",

        body: formData
      }
    );
    if (!response.ok) {
      throw new Error("Network response was not ok");
    }
    const data = await response.json();
    if (data.status == 'success') {
      let symbol = table == "consolidado" ? "$" : "S/";
      $("#span-total-importe").text(symbol + Number(data.data.total_importe).toFixed(2));
    } else {
      console.error("Error fetching headers:", data.message);
      return [];
    }
  } catch (error) {
    console.error("Error fetching headers:", error);
    return [];
  }

}
async function confirmPayment(idPago, value, type = "handlePayment") {
  const url = base_url + "Administracion/Administracion/" + type;
  try {
    const formData = new FormData();

    formData.append("idPago", idPago);
    formData.append("status", value); // Toggle confirmation status

    const response = await fetch(url, {
      method: "POST",
      body: formData
    });
    if (!response.ok) {
      throw new Error("Network response was not ok");
    }
    const data = await response.json();
    console.log(data);
    if (data.status == 'success') {
      if (type == "handlePayment") {
        viewDetailsPagosConsolidado(currentCotizacion, aPagar, pagado, currentCliente);
      } else if (type == "handlePaymentCurso") {
        viewDetailsPagosCurso(currentCurso, aPagar, pagado, currentCliente);
      }
      // Refresh the payment details
    }
  } catch (error) {
    console.error("Error confirming payment:", error);
  }
}

async function viewClientePagosCoordination(idCotizacion, nombreCliente) {
  //show modal with table of pagos coordination
  $("#modalClientePagosCoordination").modal("show");
  $("#modalClientePagosCoordination .modal-title").text(`Pagos de Coordinación - ${nombreCliente}`);
  url =
    base_url + "Administracion/Administracion/getPagosCoordination/" + idCotizacion;
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
async function viewClientePagosCurso(idPedidoCurso, nombreCliente) {
  //show modal with table of pagos coordination
  $("#modalClientePagosCoordination").modal("show");
  $("#modalClientePagosCoordination .modal-title").text(`Pagos de Coordinación - ${nombreCliente}`);
  url =
    base_url + "Administracion/Administracion/getPagosCurso/" + idPedidoCurso;
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
$(".tab-administracion").removeClass("active");
$(".tab-administracion").off("click").click(function () {
  $(".tab-administracion").removeClass("active");

  let table = this.getAttribute("data-table");
  this.classList.add("active");

  if (table == "consolidado") {
    //append to txt-ID_Campana select options from #1 to #50 with value just number
    $("#txt-ID_Campana").empty();
    // Add an empty option
    $("#txt-ID_Campana").append('<option value="0">Seleccione una campaña</option>');
    for (let i = 1; i <= 50; i++) {
      $("#txt-ID_Campana").append(`<option value="${i}">#${i}</option>`);
    }
    getTableHeaders("consolidado");
    $("#table-pagos-consolidado").attr("style", "");
    $("#table-pagos-curso").hide();
    $("#table-pagos-curso_wrapper").hide();
    url = base_url + 'Administracion/Administracion/getConsolidadoPagos';
    currentTableCurso = "consolidado";
    if ($.fn.DataTable.isDataTable("#table-pagos-consolidado")) {
      limpiarFiltrosTabla();

      $("#table-pagos-consolidado").show();
      $("#table-pagos-consolidado_wrapper").show();
      tableCursoPedidos.ajax.reload(null, false);
    } else {
      $("#txt-Fe_Inicio").val(ParseDateString(new Date(new Date().setMonth(new Date().getMonth() - 2)).toLocaleDateString('es-ES', { year: 'numeric', month: '2-digit', day: '2-digit' })));
      $("#txt-Fe_Fin").val(ParseDateString(new Date().toLocaleDateString('es-ES', { year: 'numeric', month: '2-digit', day: '2-digit' })));
      $("#txt-ID_Estado_Cotizacion").val('0');
      $("#txt-ID_Campana").val('0');
      tableCursoPedidos = $("#table-pagos-consolidado").DataTable({
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
            data.sMethod = $('#hidden-sMethod').val();
            data.estado_pago = $('#txt-ID_Estado_Cotizacion').val() ?? 0;
            console.log($('#txt-Fe_Inicio').val());
            data.Filtro_Fe_Inicio = ParseDateString($('#txt-Fe_Inicio').val() == "" ?
              //fin inicio 2 meses antes 
              new Date(new Date().setMonth(new Date().getMonth() - 2)).toLocaleDateString('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
              })
              : $('#txt-Fe_Inicio').val(), 'fecha', '/');
            data.Filtro_Fe_Fin = ParseDateString($('#txt-Fe_Fin').val() == "" ?
              new Date(new Date().setDate(new Date().getDate() + 1)).toLocaleDateString('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
              })
              : $('#txt-Fe_Fin').val(), 'fecha', '/');
            data.tipoTabla = "consolidado";
            data.campana = $('#txt-ID_Campana').val() || 0; // Get the selected campaign ID
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
        "table-pagos-consolidado",
        "search-table",
        "table-pagos-consolidado_info"
      );
      //Funcion para exportar a excel

      $("#table-pagos-consolidado").show();
    }
  }

  else if (table == "cursos") {
    currentTableCurso = "curso"; // Set current table type to curso
    getTableHeaders("curso");
    url = base_url + 'Administracion/Administracion/getCursosPagos';
    $("#table-pagos-consolidado").hide();
    $("#table-pagos-consolidado_wrapper").hide();
    $("#table-pagos-curso").attr("style", "");
    $("#table-pagos-curso_wrapper").show();

    if ($.fn.DataTable.isDataTable("#table-pagos-curso")) {
      limpiarFiltrosTabla();
      $("#table-pagos-curso").show();
      $("#table-pagos-curso_wrapper").show();
      tableCursoPagos.ajax.reload(null, false);
    } else {
      $("#txt-Fe_Inicio").val(ParseDateString(new Date(new Date().setMonth(new Date().getMonth() - 2)).toLocaleDateString('es-ES', { year: 'numeric', month: '2-digit', day: '2-digit' })));
      $("#txt-Fe_Fin").val(ParseDateString(new Date().toLocaleDateString('es-ES', { year: 'numeric', month: '2-digit', day: '2-digit' })));
      $("#txt-ID_Estado_Cotizacion").val('0');
      $("#txt-ID_Campana").val('0');
      tableCursoPagos = $("#table-pagos-curso").DataTable({
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
            data.estado_pago = $('#txt-ID_Estado_Cotizacion').val();
            data.Filtro_Fe_Inicio = ParseDateString($('#txt-Fe_Inicio').val() == "" ?
              new Date(new Date().setMonth(new Date().getMonth() - 2)).toLocaleDateString('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
              })
              : $('#txt-Fe_Inicio').val(), 'fecha', '/');
            data.Filtro_Fe_Fin = ParseDateString($('#txt-Fe_Fin').val() == "" ?
              new Date(new Date().setDate(new Date().getDate() + 1)).toLocaleDateString('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
              })
              : $('#txt-Fe_Fin').val(), 'fecha', '/');
            // Agregar tipoTabla para identificar el tipo de tabla
            data.tipoTabla = "pagos";
            data.campana = $('#txt-ID_Campana').val() || 0; // Get the selected campaign ID 

          },
        },
      });
      // FUNCION PARA CONFIGURAR EL BUSCADOR
      configurarBuscador(
        "table-pagos-curso",
        "search-table",
        "table-pagos-curso_info"
      );
      //Funcion para exportar a excel

      $("#table-pagos-curso").show();
      currentTableCurso = "curso";

    }
    getCampanasActivas();

  }
});
$(".tab-administracion").first().click();
//getIconByExtension get div with icon based on file extension and if image show image in modal else download link
function getIconByExtension(url) {
  if (!url) return ''; // Return empty if no URL is provided
  const extension = url.split('.').pop().toLowerCase();
  let icon = '';

  if (['jpg', 'jpeg', 'png', 'gif'].includes(extension)) {
    icon = `<i class="fas fa-file-image" onclick="showImageModal('${url}')" style="cursor: pointer;"></i>`;
  } else {
    icon = `<a href="${url}" download class="btn btn-primary">Descargar</a>`;
  }

  return icon;
}
async function getCampanasActivas() {
  const url = base_url + "Administracion/Administracion/getCampanasActivas";
  try {
    const response = await fetch(url);
    if (!response.ok) {
      throw new Error("Network response was not ok");
    }
    const data = await response.json();
    if (data.status == 'success') {
      const campanasSelect = $("#txt-ID_Campana");
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
function viewNote(nota) {
  //show nota in modal
  const modal = document.createElement('div');
  modal.className = 'modal fade';
  modal.id = 'notaModal';
  modal.tabIndex = -1;
  modal.setAttribute('role', 'dialog');
  const modalDialog = document.createElement('div');
  modalDialog.className = 'modal-dialog modal-dialog-centered';
  const modalContent = document.createElement('div');
  modalContent.className = 'modal-content';
  const modalHeader = document.createElement('div');
  modalHeader.className = 'modal-header';
  modalHeader.innerHTML = `<h5 class="modal-title">Nota</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>`;
  const modalBody = document.createElement('div');
  modalBody.className = 'modal-body';
  modalBody.innerHTML = `<p>${nota}</p>`;
  modalContent.appendChild(modalHeader);
  modalContent.appendChild(modalBody);
  modalDialog.appendChild(modalContent);
  modal.appendChild(modalDialog);
  document.body.appendChild(modal);
  $(modal).modal('show');
  $(modal).on('hidden.bs.modal', function () {
    $(this).remove(); // Remove modal from DOM after closing
  });
}
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

$(document).ready(function () {
  paymentSection = $("#payment-tracking-section");
  sectionListaPedidos = $("#section-listar-pedidos");
  $('.dropdown-menu').on('click', function (event) {
    event.stopPropagation(); // Evita que el evento se propague
  });
  $("#aplicar-btn-cotizacion").on("click", function () {
    if (currentTableCurso == "consolidado") {
      tableCursoPedidos.ajax.reload(null, false);
      getTableHeaders("consolidado");
    }
    else if (currentTableCurso == "curso") {
      tableCursoPagos.ajax.reload(null, false);
      getTableHeaders("curso");
    }
    $(".dropdown-menu").removeClass("show"); // Remove the show class from the dropdown menu
  });
});
$('.input-report').datepicker({
  autoclose: true,
  //startDate : new Date(fYear, fToday.getMonth(), '01'),
  todayHighlight: true,
  dateFormat: 'dd/mm/yyyy',
  format: 'dd/mm/yyyy',
});
$("#cancelar-btn").on("click", function () {
  limpiarFiltrosTabla();
  if (currentTableCurso === 'consolidado') {
    $("#cbo-filtro-estado_pago").val('0').trigger('change');
    tableCursoPedidos.ajax.reload(null, false); // Reload the consolidated payments table
  } else if (currentTableCurso === 'curso') {
    $("#cbo-filtro-estado_pago").val('0').trigger('change');
    tableCursoPagos.ajax.reload(null, false); // Reload the course payments table
  }
  $(".dropdown-menu").removeClass("show"); // Remove the show class from the dropdown menu
});