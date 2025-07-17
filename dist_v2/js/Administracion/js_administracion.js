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
  const cotizacionSection = $("#cotizaciones-container");
  cotizacionSection.hide();
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
        const cardBg = detail.status == "CONFIRMADO" ? "bg-green-100" : detail.status == "OBSERVADO" ? "bg-red-100" : "bg-white";
        paymentSectionCards.append(`<div class="payment-card ${cardBg} rounded-xl ">
                <div class="">
                    <h3 class="text-lg text-center text-gray-800 mb-2 p-4">Adelanto N° ${index}</h3>
                        <div class="space-y-3 p-4 border-b border-t border-gray-200">
                            <div class="flex items-center gap-3 px-3">
                                <span class="text-gray-600 w-20">Monto:</span>
                                <input disabled type="text" value="S/.${Number((detail.monto)).toFixed(2)}" class="border-0 ${cardBg} px-3 w-full " readonly>
                            </div>
                            <div class="flex items-center gap-3 px-3">
                                <span class="text-gray-600 w-20">Fecha:</span>
                                <input disabled type="text" value="${detail.payment_date}" class="border-0 ${cardBg} px-3 py-2 w-full" readonly>
                            </div>
                            <div class="flex items-center gap-3 px-3">
                                <span class="text-gray-600 w-20">Banco:</span>
                                <input disabled type="text" value="${detail.banco}" class="border-0 ${cardBg} px-3 py-2 w-full" readonly>
                            </div>
                            <div class="flex items-center gap-3 px-3">
                                <span class="text-gray-600 w-20">Voucher:</span>
                                <a  style="cursor:pointer" onclick="showImageModal('${detail.voucher_url}')">
                                ${initGetIconByType(detail.voucher_url.split('.').pop())}</a>
                            </div>
                        </div>
                        <div class="p-4">
                          <select class="hidden w-100 py-2 border-2 border-gray-200 rounded-lg select-pago-estado" data-id="${detail.id}" id="cbo-estado-pago-${detail.id}">
                            <option value="PENDIENTE" ${detail.status == "PENDIENTE" ? "selected" : ""}>Pendiente</option>
                            <option value="CONFIRMADO" ${detail.status == "CONFIRMADO" ? "selected" : ""}>Conforme</option>
                            <option value="OBSERVADO" ${detail.status == "OBSERVADO" ? "selected" : ""}>Observar</option>
                          </select>
                        </div>
                </div>
            </div>`);
        setTimeout(() => {
          $(`#cbo-estado-pago-${detail.id}`).select2({
            dropdownParent: $(`#cbo-estado-pago-${detail.id}`).parent(),
            templateResult: function (state) {
              if (!state.id) return state.text;
              if (state.id === "CONFIRMADO") {
                return $(`<span class="flex">
                    <svg width="20" height="20" viewBox="0 0 14 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M1 6.47826L4.31034 10L13 1" stroke="#00D680" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>&nbsp;
                    Conforme
                </span>`);
              }
              if (state.id === "PENDIENTE") {
                return $(`<span class="flex">
                  <svg width="20" height="20" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8 15C11.866 15 15 11.866 15 8C15 4.13401 11.866 1 8 1C4.13401 1 1 4.13401 1 8C1 11.866 4.13401 15 8 15Z" stroke="#585858" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M8 3.80078V8.00078L10.8 9.40078" stroke="#585858" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>&nbsp;
                  Pendiente
                </span>`);
              }
              if (state.id === "OBSERVADO") {
                return $(`<span class="flex">
                  <svg width="20" height="20" viewBox="0 0 17 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7.04177 1.66567L1.18532 11.4426C1.06457 11.6517 1.00068 11.8887 1.00001 12.1302C0.999329 12.3717 1.06189 12.6091 1.18146 12.8189C1.30104 13.0287 1.47346 13.2035 1.68157 13.3259C1.88967 13.4484 2.12622 13.5142 2.36767 13.5169H14.0806C14.322 13.5142 14.5586 13.4484 14.7667 13.3259C14.9748 13.2035 15.1472 13.0287 15.2668 12.8189C15.3864 12.6091 15.4489 12.3717 15.4482 12.1302C15.4476 11.8887 15.3837 11.6517 15.2629 11.4426L9.40647 1.66567C9.28321 1.46247 9.10966 1.29446 8.90255 1.17786C8.69545 1.06126 8.46179 1 8.22412 1C7.98645 1 7.75279 1.06126 7.54569 1.17786C7.33859 1.29446 7.16503 1.46247 7.04177 1.66567Z" stroke="#D71009" stroke-width="1.38287" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M8.22437 5.21875V7.98449" stroke="#D71009" stroke-width="1.38287" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M8.22437 10.75H8.23224" stroke="#D71009" stroke-width="1.38287" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>&nbsp;
                  Observar
                </span>`);
              }
              return state.text;
            },
            templateSelection: function (state) {
              return state.text;
            },
            escapeMarkup: function (markup) { return markup; },
            minimumResultsForSearch: Infinity
          });
        }, 0);
        index++;
      });
      $(".select-pago-estado").off("change").on("change", function() {
        const id = $(this).data("id");
        const estado = $(this).val();
        confirmPayment(id, estado,'handlePaymentCurso');
      });
      paymentSection.show();
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
  const cotizacionContainer = $("#cotizaciones-container");
  cotizacionContainer.show(); // Show the cotizacion section

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
        const cardBg= detail.status == "CONFIRMADO" ? "bg-green-100" : detail.status == "OBSERVADO" ? "bg-red-100" : "bg-white";
        paymentSectionCards.append(`<div class="payment-card ${cardBg} rounded-xl ">
                <div class="">
                    <h3 class="text-lg text-center text-gray-800 mb-2 p-4">Adelanto N° ${index}</h3>
                        <div class="space-y-3 p-4 border-b border-t border-gray-200">
                            <div class="flex items-center gap-3 px-3">
                                <span class="text-gray-600 w-20">Monto:</span>
                                <input disabled type="text" value="$${Number((detail.monto)).toFixed(2)}" class="border-0 ${cardBg} px-3 w-full " readonly>
                            </div>
                            <div class="flex items-center gap-3 px-3">
                                <span class="text-gray-600 w-20">Fecha:</span>
                                <input disabled type="text" value="${detail.payment_date}" class="border-0 ${cardBg} px-3 py-2 w-full" readonly>
                            </div>
                            <div class="flex items-center gap-3 px-3">
                                <span class="text-gray-600 w-20">Banco:</span>
                                <input disabled type="text" value="${detail.banco}" class="border-0 ${cardBg} px-3 py-2 w-full" readonly>
                            </div>
                            <div class="flex items-center gap-3 px-3">
                                <span class="text-gray-600 w-20">Voucher:</span>
                                <a  style="cursor:pointer" onclick="showImageModal('${detail.voucher_url}')">
                                ${initGetIconByType(detail.voucher_url.split('.').pop())}</a> 
                                <span class="filename-truncate"title="${detail.voucher_url ? decodeURIComponent(detail.voucher_url.split('/').pop()) : ''}">
                                  ${detail.voucher_url ? decodeURIComponent(detail.voucher_url.split('/').pop()) : ''}
                                </span>
                            </div>
                        </div>
                        <div class="p-4">
                          <select class="hidden w-100 py-2 border-2 border-gray-200 rounded-lg select-pago-estado" data-id="${detail.id}" id="cbo-estado-pago-${detail.id}">
                            <option value="PENDIENTE" ${detail.status == "PENDIENTE" ? "selected" : ""}>Pendiente</option>
                            <option value="CONFIRMADO" ${detail.status == "CONFIRMADO" ? "selected" : ""}>Conforme</option>
                            <option value="OBSERVADO" ${detail.status == "OBSERVADO" ? "selected" : ""}>Observar</option>
                          </select>
                        </div>
                </div>
            </div>`);
            // Inicializa Select2 después de agregar el select
      setTimeout(() => {
        $(`#cbo-estado-pago-${detail.id}`).select2({
          dropdownParent: $(`#cbo-estado-pago-${detail.id}`).parent(),
          templateResult: function (state) {
            if (!state.id) return state.text;
            if (state.id === "CONFIRMADO") {
              return $(`<span class="flex">
                  <svg width="20" height="20" viewBox="0 0 14 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 6.47826L4.31034 10L13 1" stroke="#00D680" stroke-width="1.5" stroke-linecap="round"/>
                  </svg>&nbsp;
                  Conforme
              </span>`);
            }
            if (state.id === "PENDIENTE") {
              return $(`<span class="flex">
                <svg width="20" height="20" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M8 15C11.866 15 15 11.866 15 8C15 4.13401 11.866 1 8 1C4.13401 1 1 4.13401 1 8C1 11.866 4.13401 15 8 15Z" stroke="#585858" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M8 3.80078V8.00078L10.8 9.40078" stroke="#585858" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>&nbsp;


                Pendiente
              </span>`);
            }
            if (state.id === "OBSERVADO") {
              return $(`<span class="flex">
                <svg width="20" height="20" viewBox="0 0 17 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M7.04177 1.66567L1.18532 11.4426C1.06457 11.6517 1.00068 11.8887 1.00001 12.1302C0.999329 12.3717 1.06189 12.6091 1.18146 12.8189C1.30104 13.0287 1.47346 13.2035 1.68157 13.3259C1.88967 13.4484 2.12622 13.5142 2.36767 13.5169H14.0806C14.322 13.5142 14.5586 13.4484 14.7667 13.3259C14.9748 13.2035 15.1472 13.0287 15.2668 12.8189C15.3864 12.6091 15.4489 12.3717 15.4482 12.1302C15.4476 11.8887 15.3837 11.6517 15.2629 11.4426L9.40647 1.66567C9.28321 1.46247 9.10966 1.29446 8.90255 1.17786C8.69545 1.06126 8.46179 1 8.22412 1C7.98645 1 7.75279 1.06126 7.54569 1.17786C7.33859 1.29446 7.16503 1.46247 7.04177 1.66567Z" stroke="#D71009" stroke-width="1.38287" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M8.22437 5.21875V7.98449" stroke="#D71009" stroke-width="1.38287" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M8.22437 10.75H8.23224" stroke="#D71009" stroke-width="1.38287" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>&nbsp;
                Observar
              </span>`);
            }
            return state.text;
          },
          templateSelection: function (state) {
            // Solo texto en el input cerrado (limitación de Select2)
            return state.text;
          },
          escapeMarkup: function (markup) { return markup; },
          minimumResultsForSearch: Infinity
        });
      }, 0);
        index++; // Increment index for next payment card
      });
      $(".select-pago-estado").off("change").on("change", function() {
        const id = $(this).data("id");
        const estado = $(this).val();
        confirmPayment(id, estado);
      });

      const cotizacionCard = $("#cotizacion-card");
      cotizacionCard.empty(); // Clear previous content in cotizacion section
      const cotizacionInicial = data.data.cotizacion_inicial_url;
      if (cotizacionInicial) {
        cotizacionCard.append(`

                    <div class="file-item">
                        <div class="file-preview d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="40" height="40" viewBox="0 0 48 48">
                                <path fill="#169154" d="M29,6H15.744C14.781,6,14,6.781,14,7.744v7.259h15V6z"></path><path fill="#18482a" d="M14,33.054v7.202C14,41.219,14.781,42,15.743,42H29v-8.946H14z"></path><path fill="#0c8045" d="M14 15.003H29V24.005000000000003H14z"></path><path fill="#17472a" d="M14 24.005H29V33.055H14z"></path><g><path fill="#29c27f" d="M42.256,6H29v9.003h15V7.744C44,6.781,43.219,6,42.256,6z"></path><path fill="#27663f" d="M29,33.054V42h13.257C43.219,42,44,41.219,44,40.257v-7.202H29z"></path><path fill="#19ac65" d="M29 15.003H44V24.005000000000003H29z"></path><path fill="#129652" d="M29 24.005H44V33.055H29z"></path></g><path fill="#0c7238" d="M22.319,34H5.681C4.753,34,4,33.247,4,32.319V15.681C4,14.753,4.753,14,5.681,14h16.638 C23.247,14,24,14.753,24,15.681v16.638C24,33.247,23.247,34,22.319,34z"></path><path fill="#fff" d="M9.807 19L12.193 19 14.129 22.754 16.175 19 18.404 19 15.333 24 18.474 29 16.123 29 14.013 25.07 11.912 29 9.526 29 12.719 23.982z"></path>
                            </svg>
                                &nbsp;
                            <p>Cotización Inicial.xslx</p>
                        </div>
                        <div class="file-actions d-flex flex-row">
                            <button class="btn-sm download-btn" onclick="window.open('${cotizacionInicial}', '_blank')">
                                <i class="fas fa-download"></i> 
                            </button>
                        </div>
                    </div>
                    
                    
                    
                    `);
      }
      const cotizacionFinal = data.data.cotizacion_final_url;
      if (cotizacionFinal) {
        cotizacionCard.append(`
                    <div class="file-item">
                        <div class="file-preview d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="40" height="40" viewBox="0 0 48 48">
                                <path fill="#169154" d="M29,6H15.744C14.781,6,14,6.781,14,7.744v7.259h15V6z"></path><path fill="#18482a" d="M14,33.054v7.202C14,41.219,14.781,42,15.743,42H29v-8.946H14z"></path><path fill="#0c8045" d="M14 15.003H29V24.005000000000003H14z"></path><path fill="#17472a" d="M14 24.005H29V33.055H14z"></path><g><path fill="#29c27f" d="M42.256,6H29v9.003h15V7.744C44,6.781,43.219,6,42.256,6z"></path><path fill="#27663f" d="M29,33.054V42h13.257C43.219,42,44,41.219,44,40.257v-7.202H29z"></path><path fill="#19ac65" d="M29 15.003H44V24.005000000000003H29z"></path><path fill="#129652" d="M29 24.005H44V33.055H29z"></path></g><path fill="#0c7238" d="M22.319,34H5.681C4.753,34,4,33.247,4,32.319V15.681C4,14.753,4.753,14,5.681,14h16.638 C23.247,14,24,14.753,24,15.681v16.638C24,33.247,23.247,34,22.319,34z"></path><path fill="#fff" d="M9.807 19L12.193 19 14.129 22.754 16.175 19 18.404 19 15.333 24 18.474 29 16.123 29 14.013 25.07 11.912 29 9.526 29 12.719 23.982z"></path>
                            </svg>
                                &nbsp;
                            <p>Cotización Final.xslx</p>
                        </div>
                        <div class="file-actions d-flex flex-row">
                            <button class="btn-sm download-btn" onclick="window.open('${cotizacionFinal}', '_blank')">
                                <i class="fas fa-download"></i> 
                            </button>
                        </div>
                    </div>
                    
                    `);
      }
    }
    paymentSection.show(); // Show the payment section

  } catch (error) {
    console.error("Error fetching payment details:", error);
  }
}
async function saveNote() {
  const note = $("#nota").val().trim();

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
      swal.fire({
        icon: 'success',
        title: 'Nota guardada correctamente',
        confirmButtonText: 'OK'
      });
      $("#note-input").val(""); // Clear the input field
      if (currentCurso > 0) {
        viewDetailsPagosCurso(currentCurso, aPagar, pagado, currentCliente); // Refresh the payment details for course
      } else {

        viewDetailsPagosConsolidado(currentCotizacion, aPagar, pagado, currentCliente);
      }// Refresh the payment details
    } else {
      swal.fire({
        icon: 'error',
        title: 'Error al guardar la nota',
        text: data.message,
        confirmButtonText: 'OK'
      });
    }
  } catch (error) {
    console.error("Error saving note:", error);
  }
}
async function getContainersAvailable() {
  const url = base_url + "Administracion/Administracion/getContainersAvailable";
  try {
    const response = await fetch(url);
    if (!response.ok) {
      throw new Error("Network response was not ok");
    }
    const data = await response.json();
    if (data.status == 'success') {
      return data.data; // Return the available containers
    } else {
      console.error("Error fetching containers:", data.message);
      return [];
    }
  } catch (error) {
    console.error("Error fetching containers:", error);
    return [];
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
    formData.append("Filtro_Fe_Inicio", $('#txt-Fe_Inicio').val() == "" ? ParseDateString(      //fin inicio 2 meses antes
      new Date(new Date().setMonth(new Date().getMonth() - 2)).toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
      }), 'fecha', '/')
      : $('#txt-Fe_Inicio').val());
    formData.append("Filtro_Fe_Fin", $('#txt-Fe_Fin').val() == "" ? ParseDateString(
      new Date(new Date().setDate(new Date().getDate() + 1)).toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
      }), 'fecha', '/')
      : $('#txt-Fe_Fin').val());

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
      $("#span-total-importe").val(symbol + Number(data.data.total_importe).toFixed(2));
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
$(".tab-administracion").off("click").click(async function () {
  $(".tab-administracion").removeClass("active");

  let table = this.getAttribute("data-table");
  this.classList.add("active");

  if (table == "consolidado") {
    //append to txt-ID_Campana select options from #1 to #50 with value just number
    $("#txt-ID_Campana").empty();
    // Add an empty option
    $("#txt-ID_Campana").append('<option value="0">Seleccione un Contenedor</option>');
    const data = await getContainersAvailable();
    if (data.length > 0) {
      data.forEach(container => {
        $("#txt-ID_Campana").append(`<option value="${container.carga}">Contenedor #${container.carga}</option>`);
      });
    } else {
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
            data.Filtro_Fe_Inicio = $('#txt-Fe_Inicio').val() == "" ?
              //fin inicio 2 meses antes 
              new Date(new Date().setMonth(new Date().getMonth() - 2)).toLocaleDateString('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
              })
              : $('#txt-Fe_Inicio').val();
            data.Filtro_Fe_Fin = ($('#txt-Fe_Fin').val() == "" ?
              new Date(new Date().setDate(new Date().getDate() + 1)).toLocaleDateString('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
              })
              : $('#txt-Fe_Fin').val());
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
            data.Filtro_Fe_Inicio = $('#txt-Fe_Inicio').val() == "" ?
              new Date(new Date().setMonth(new Date().getMonth() - 2)).toLocaleDateString('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
              })
              : $('#txt-Fe_Inicio').val();
            data.Filtro_Fe_Fin = $('#txt-Fe_Fin').val() == "" ?
              new Date(new Date().setDate(new Date().getDate() + 1)).toLocaleDateString('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
              })
              : $('#txt-Fe_Fin').val();
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
  modalBody.className = 'modal-body d-flex justify-content-center align-items-center';
  modalBody.innerHTML = `
    <img src="${url}" alt="Image" class="img-fluid image-zoom"
      style="max-width:80vw; max-height:50vh; min-width:300px; min-height:200px; object-fit:contain; transition:transform 0.3s; cursor:zoom-in; display:block; margin:auto;">
  `;
  modalContent.appendChild(modalBody);
  modalDialog.appendChild(modalContent);
  modal.appendChild(modalDialog);
  document.body.appendChild(modal);
  $(modal).modal('show');
  $(modal).on('hidden.bs.modal', function () {
    $(this).remove(); // Remove modal from DOM after closing
  });
  // Zoom al hacer click
  modalBody.querySelector('.image-zoom').addEventListener('click', function () {
    if (this.style.transform === "scale(2)") {
      this.style.transform = "scale(1)";
      this.style.cursor = "zoom-in";
    } else {
      this.style.transform = "scale(2)";
      this.style.cursor = "zoom-out";
    }
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
  dateFormat: 'yyyy-mm-dd',
  format: 'yyyy-mm-dd',
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