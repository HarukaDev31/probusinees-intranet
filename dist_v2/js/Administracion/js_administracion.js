var paymentSection;
var sectionListaPedidos;
var currentCotizacion = 0;
var aPagar = 0;
var pagado = 0;
var currentCurso = 0;
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
async function backToList() {
  sectionListaPedidos.show(); // Show the section with the list of orders
  paymentSection.hide(); // Hide the payment section
  currentCotizacion = 0; // Reset current cotizacion ID
  currentCurso = 0; // Reset current course ID
  aPagar = 0; // Reset amount to be paid
  pagado = 0; // Reset amount already paid
  $("#payment-tracking-section-cards").empty(); // Clear previous content in payment cards section
}
async function viewDetailsPagosCurso(idPedidoCurso, apagar, pago) {
  currentCurso = idPedidoCurso; // Store the current course ID
  aPagar = apagar; // Store the amount to be paid
  pagado = pago; // Store the amount already paid
  sectionListaPedidos.hide(); // Hide the section with the list of orders
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
      $("#total-amount").text(`$${Number(apagar).toFixed(2)}`);
      $("#paid-amount").text(`$${Number(pago).toFixed(2)}`);
      console.log(data.data.nota);
      let index = 1; // Initialize index for payment cards
      $("#nota").val(data.data.nota || ""); // Set the note input value
      data.data.data.forEach(detail => {

        paymentSectionCards.append(`<div class="payment-card bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300">
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
                    <button
                    onclick="confirmPayment('${detail.id}', '${detail.is_confirmed}','handlePaymentCurso')"
                    id="payment-card-${detail.id}"
                    class="confirm-btn w-full mt-6
          font-semibold rounded-lg py-3 transition-all duration-300 transform hover:-translate-y-1
            text-white text-lg
                    ${detail.is_confirmed == "1" ? "bg-green-500 hover:bg-green-600" : "bg-blue-500 hover:bg-blue-600"}">
                        ${detail.is_confirmed == "1" ? "Confirmado" : "Confirmar Pago"}
                    </button>
                </div>
            </div>`); 7
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
async function viewDetailsPagosConsolidado(idCotizacion, apagar, pago) {
  currentCotizacion = idCotizacion; // Store the current cotizacion ID
  aPagar = apagar; // Store the amount to be paid
  pagado = pago; // Store the amount already paid
  sectionListaPedidos.hide(); // Hide the section with the list of orders
  const paymentSectionCards = $("#payment-tracking-section-cards");
  paymentSectionCards.empty(); // Clear previous content
  const url = base_url + "Administracion/Administracion/getDetailsPagosConsolidado/" + idCotizacion;
  try {
    const response = await fetch(url);
    if (!response.ok) {
      throw new Error("Network response was not ok");
    }
    const data = await response.json();

    if (data.status == 'success') {
      $("#total-amount").text(`$${Number(apagar).toFixed(2)}`);
      $("#paid-amount").text(`$${Number(pago).toFixed(2)}`);
      console.log(data.data.nota);
      let index = 1; // Initialize index for payment cards
      $("#nota").val(data.data.nota || ""); // Set the note input value
      data.data.data.forEach(detail => {

        paymentSectionCards.append(`<div class="payment-card bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300">
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
                    <button 
                    onclick="confirmPayment('${detail.id}', '${detail.is_confirmed}')"
                    id="payment-card-${detail.id}"
                    class="confirm-btn w-full mt-6 
          font-semibold rounded-lg py-3 transition-all duration-300 transform hover:-translate-y-1
            text-white text-lg
                    ${detail.is_confirmed == "1" ? "bg-green-500 hover:bg-green-600" : "bg-blue-500 hover:bg-blue-600"}">
                        ${detail.is_confirmed == "1" ? "Confirmado" : "Confirmar Pago"}
                    </button>
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
        viewDetailsPagosCurso(currentCurso, aPagar, pagado); // Refresh the payment details for course
      } else {

        viewDetailsPagosConsolidado(currentCotizacion, aPagar, pagado);
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
    const response = await fetch(url);
    if (!response.ok) {
      throw new Error("Network response was not ok");
    }
    const data = await response.json();
    if (data.status == 'success') {
      $("#span-total-importe").text("$"+Number(data.data.total_importe).toFixed(2));
    } else {
      console.error("Error fetching headers:", data.message);
      return [];
    }
  } catch (error) {
    console.error("Error fetching headers:", error);
    return [];
  }

}
async function confirmPayment(idPago, isConfirmed, type = "handlePayment") {
  const url = base_url + "Administracion/Administracion/" + type;
  try {
    const formData = new FormData();
    let confirmed = isConfirmed == "1" ? "0" : "1";
    formData.append("idPago", idPago);
    formData.append("isConfirmed", confirmed); // Toggle confirmation status

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
        viewDetailsPagosConsolidado(currentCotizacion, aPagar, pagado);
      } else if (type == "handlePaymentCurso") {
        viewDetailsPagosCurso(currentCurso, aPagar, pagado);
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
  // $("#table-administracion-pagos_wrapper").hide();
  // $("#table-administracion-variacion_wrapper").hide();
  // $("#table-administracion-pagos_wrapper").hide();

  let table = this.getAttribute("data-table");
  this.classList.add("active");

  if (table == "consolidado") {
    getTableHeaders("consolidado");
    $("#table-pagos-consolidado").attr("style", "");
    $("#table-pagos-curso").hide();
    $("#table-pagos-curso_wrapper").hide();
    url = base_url + 'Administracion/Administracion/getConsolidadoPagos';

    if ($.fn.DataTable.isDataTable("#table-pagos-consolidado")) {

      $("#table-pagos-consolidado").show();
      $("#table-pagos-consolidado_wrapper").show();
      tableCursoPedidos.ajax.reload(null, false);
    } else {
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
        "table-pagos-consolidado",
        "search-table",
        "table-pagos-consolidado_info"
      );
      //Funcion para exportar a excel

      $("#table-pagos-consolidado").show();
    }
    currentTableCurso = "consolidado";
  }

  else if (table == "cursos") {
    getTableHeaders("curso");
    url = base_url + 'Administracion/Administracion/getCursosPagos';
    $("#table-pagos-consolidado").hide();
    $("#table-pagos-consolidado_wrapper").hide();
    $("#table-pagos-curso").attr("style", "");
    $("#table-pagos-curso_wrapper").show();

    if ($.fn.DataTable.isDataTable("#table-pagos-curso")) {

      $("#table-pagos-curso").show();
      $("#table-pagos-curso_wrapper").show();
      tableCursoPagos.ajax.reload(null, false);
    } else {

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
            data.estado_pago = $('#cbo-filtro-estado_pago').val();
            data.Filtro_Fe_Inicio = ParseDateString($('#txt-Fe_Inicio').val(), 'fecha', '/');
            data.Filtro_Fe_Fin = ParseDateString($('#txt-Fe_Fin').val(), 'fecha', '/');
            data.tipoTabla = "pagos";

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
});