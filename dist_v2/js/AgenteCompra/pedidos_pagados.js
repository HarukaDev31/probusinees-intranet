var url, table_Entidad;
//AUTOCOMPLETE
var caractes_no_validos_global_autocomplete = "\"'~!@%^|";
// Se puede crear un arreglo a partir de la cadena
let search_global_autocomplete =
  caractes_no_validos_global_autocomplete.split("");
// Solo tomé algunos caracteres, completa el arreglo
let replace_global_autocomplete = ["", "", "", "", "", "", "", "", ""];
//28 caracteres
// FIN AUTOCOMPLETE
let priviligesPersonalPeru = 1;
let priviligesPersonalChina = 2;
let priviligesJefeChina = 5;
let priviligesAlmacen = 6;
let currentPrivilege = null;
let pagosCount = 2;
const editIcon = `<?xml version="1.0" encoding="utf-8"?>
<svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<g id="Edit / Edit_Pencil_02">
<path id="Vector" d="M4 16.0001V20.0001L8 20.0001L18.8686 9.13146L18.8695 9.13061C19.265 8.73516 19.4628 8.53736 19.5369 8.3092C19.6021 8.10835 19.6022 7.89201 19.5369 7.69117C19.4627 7.46284 19.2646 7.26474 18.8686 6.86872L17.1288 5.12892C16.7345 4.7346 16.5369 4.53704 16.3091 4.46301C16.1082 4.39775 15.8919 4.39775 15.691 4.46301C15.463 4.53709 15.2652 4.73488 14.8704 5.12976L14.8686 5.13146L4 16.0001Z" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
</g>
</svg>`;
var fToday = new Date(),
  fYear = fToday.getFullYear(),
  fMonth = fToday.getMonth() + 1,
  fDay = fToday.getDate();

if (fMonth < 10) {
  fMonth = "0" + fMonth;
}
let containerVer = null;
let containerListar = null;
let containerOrdenCompra = null;
let sectionTitle = null;
let containerRotulado = null;
let idPedido = null;
let productoSelected = null;
let selectedStep = null;
let containerSteps = null;
let containerPagos = null;
let containerCoordination = null;
let containerAlmacen = null;
let containerInspection = null;
let currentServicio = 1;
$(function () {
  sectionTitle = $("#section-title");
  containerVer = $("#container-ver");
  containerVer.hide();
  containerListar = $("#container-listar");
  containerOrdenCompra = $("#container_orden-compra");
  containerOrdenCompra.hide();
  containerRotulado = $("#container-rotulado");
  containerSteps = $("#steps");
  containerPagos = $("#container-pagos");
  containerPagos.hide();
  containerAlmacen = $("#container_almacen");
  containerAlmacen.hide();
  containerCoordination = $("#container-coordination");
  containerCoordination.hide();
  containerInspection = $("#container-inspeccion");
  containerInspection.hide();
  $(".select2").select2();

  $("#cbo-proveedor-Nu_Tipo_Pay_Proveedor_China").change(function () {
    $(".div-banco_china").hide();
    if ($(this).val() == 1) {
      //DNI
      $(".div-banco_china").show();
    }
  });

  $(document).on("click", "#btn-save_pagos_logisticos", function (e) {
    e.preventDefault();

    $("#btn-save_pagos_logisticos").text("");
    $("#btn-save_pagos_logisticos").attr("disabled", true);
    $("#btn-save_pagos_logisticos").append(
      'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>'
    );

    //$( '#modal-loader' ).modal('show');

    var postData = new FormData($("#form-pagos_logisticos")[0]);
    $.ajax({
      url: base_url + "AgenteCompra/PedidosPagados/pagosLogisticos",
      type: "POST",
      dataType: "JSON",
      data: postData,
      processData: false,
      contentType: false,
      success: function (response) {
        //$( '#modal-loader' ).modal('hide');

        $("#moda-message-content").removeClass(
          "bg-danger bg-warning bg-success"
        );
        $("#modal-message").modal("show");

        if (response.status == "success") {
          $(".modal-pagos_logisticos").modal("hide");

          $("#moda-message-content").addClass("bg-" + response.status);
          $(".modal-title-message").text(response.message);
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 2100);
        } else {
          $("#moda-message-content").addClass("bg-danger");
          $(".modal-title-message").text(response.message);
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 3200);
        }

        $("#btn-save_pagos_logisticos").text("");
        $("#btn-save_pagos_logisticos").append("Guardar");
        $("#btn-save_pagos_logisticos").attr("disabled", false);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        //$( '#modal-loader' ).modal('hide');
        $("#moda-message-content").removeClass(
          "bg-danger bg-warning bg-success"
        );

        $("#modal-message").modal("show");
        $("#moda-message-content").addClass("bg-danger");
        $(".modal-title-message").text("Problemas");
        setTimeout(function () {
          $("#modal-message").modal("hide");
        }, 1700);

        console.error(jqXHR.responseText);

        $("#btn-save_pagos_logisticos").text("");
        $("#btn-save_pagos_logisticos").append("Guardar");
        $("#btn-save_pagos_logisticos").attr("disabled", false);
      },
    });
  });

  $(document).on("click", "#btn-guardar_entrega_docs_cliente", function (e) {
    e.preventDefault();

    if (!$("#entrega_docs_cliente-inlineCheckbox1").prop("checked")) {
      alert("Debes seleccionar Commercial Invoice");
    } else if (
      ($('[name="entrega_docs_cliente-Nu_Tipo_Incoterms"]').val() == 3 ||
        $('[name="entrega_docs_cliente-Nu_Tipo_Incoterms"]').val() == 4) &&
      !$("#entrega_docs_cliente-inlineCheckbox2").prop("checked")
    ) {
      alert("Debes seleccionar BL");
    } else if (!$("#entrega_docs_cliente-inlineCheckbox3").prop("checked")) {
      alert("Debes seleccionar FTA Detalle");
    } else if (!$("#entrega_docs_cliente-inlineCheckbox4").prop("checked")) {
      alert("Debes seleccionar Packing List");
    } else if (!$("#entrega_docs_cliente-inlineCheckbox5").prop("checked")) {
      alert("Debes seleccionar FTA");
    } else {
      $("#btn-guardar_entrega_docs_cliente").text("");
      $("#btn-guardar_entrega_docs_cliente").attr("disabled", true);
      $("#btn-guardar_entrega_docs_cliente").append(
        'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>'
      );

      //$( '#modal-loader' ).modal('show');

      url = base_url + "AgenteCompra/PedidosPagados/entregaDocsCliente";
      $.ajax({
        type: "POST",
        dataType: "JSON",
        url: url,
        data: $("#form-entrega_docs_cliente").serialize(),
        success: function (response) {
          //$( '#modal-loader' ).modal('hide');

          $("#moda-message-content").removeClass(
            "bg-danger bg-warning bg-success"
          );
          $("#modal-message").modal("show");

          if (response.status == "success") {
            $(".modal-entrega_docs_cliente").modal("hide");

            $("#moda-message-content").addClass("bg-" + response.status);
            $(".modal-title-message").text(response.message);
            setTimeout(function () {
              $("#modal-message").modal("hide");
            }, 2100);
          } else {
            $("#moda-message-content").addClass("bg-danger");
            $(".modal-title-message").text(response.message);
            setTimeout(function () {
              $("#modal-message").modal("hide");
            }, 3200);
          }

          $("#btn-guardar_entrega_docs_cliente").text("");
          $("#btn-guardar_entrega_docs_cliente").append("Guardar");
          $("#btn-guardar_entrega_docs_cliente").attr("disabled", false);
        },
        error: function (jqXHR, textStatus, errorThrown) {
          //$( '#modal-loader' ).modal('hide');
          $("#moda-message-content").removeClass(
            "bg-danger bg-warning bg-success"
          );

          $("#modal-message").modal("show");
          $("#moda-message-content").addClass("bg-danger");
          $(".modal-title-message").text("Problemas al guardar");
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 1700);
          console.error(jqXHR.responseText);
          $("#btn-guardar_entrega_docs_cliente").text("");
          $("#btn-guardar_entrega_docs_cliente").append("Guardar");
          $("#btn-guardar_entrega_docs_cliente").attr("disabled", false);
        },
      });
    }
  });

  $(document).on("click", "#btn-save_revision_bl", function (e) {
    e.preventDefault();

    $("#btn-save_revision_bl").text("");
    $("#btn-save_revision_bl").attr("disabled", true);
    $("#btn-save_revision_bl").append(
      'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>'
    );

    //$( '#modal-loader' ).modal('show');

    url = base_url + "AgenteCompra/PedidosPagados/revisionBL";
    $.ajax({
      type: "POST",
      dataType: "JSON",
      url: url,
      data: $("#form-revision_bl").serialize(),
      success: function (response) {
        //$( '#modal-loader' ).modal('hide');

        $("#moda-message-content").removeClass(
          "bg-danger bg-warning bg-success"
        );
        $("#modal-message").modal("show");

        if (response.status == "success") {
          $(".modal-revision_bl").modal("hide");

          $("#moda-message-content").addClass("bg-" + response.status);
          $(".modal-title-message").text(response.message);
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 2100);
        } else {
          $("#moda-message-content").addClass("bg-danger");
          $(".modal-title-message").text(response.message);
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 3200);
        }

        $("#btn-save_revision_bl").text("");
        $("#btn-save_revision_bl").append("Guardar");
        $("#btn-save_revision_bl").attr("disabled", false);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        //$( '#modal-loader' ).modal('hide');
        $("#moda-message-content").removeClass(
          "bg-danger bg-warning bg-success"
        );

        $("#modal-message").modal("show");
        $("#moda-message-content").addClass("bg-danger");
        $(".modal-title-message").text("Problemas al guardar");
        setTimeout(function () {
          $("#modal-message").modal("hide");
        }, 1700);

        //Message for developer
        console.log(jqXHR.responseText);

        $("#btn-save_revision_bl").text("");
        $("#btn-save_revision_bl").append("Guardar");
        $("#btn-save_revision_bl").attr("disabled", false);
      },
    });
  });

  $(document).on("click", "#btn-guardar_despacho_shipper", function (e) {
    e.preventDefault();
    if (!$("#inlineCheckbox1").prop("checked")) {
      alert("Debes seleccionar entrega de Carga");
    } else if (!$("#inlineCheckbox2").prop("checked")) {
      alert("Debes seleccionar entrega de Documentos");
    } else {
      $("#btn-guardar_despacho_shipper").text("");
      $("#btn-guardar_despacho_shipper").attr("disabled", true);
      $("#btn-guardar_despacho_shipper").append(
        'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>'
      );

      //$( '#modal-loader' ).modal('show');

      url = base_url + "AgenteCompra/PedidosPagados/despachoShipper";
      $.ajax({
        type: "POST",
        dataType: "JSON",
        url: url,
        data: $("#form-despacho_shipper").serialize(),
        success: function (response) {
          //$( '#modal-loader' ).modal('hide');

          $("#moda-message-content").removeClass(
            "bg-danger bg-warning bg-success"
          );
          $("#modal-message").modal("show");

          if (response.status == "success") {
            $(".modal-despacho_shipper").modal("hide");

            $("#moda-message-content").addClass("bg-" + response.status);
            $(".modal-title-message").text(response.message);
            setTimeout(function () {
              $("#modal-message").modal("hide");
            }, 2100);
          } else {
            $("#moda-message-content").addClass("bg-danger");
            $(".modal-title-message").text(response.message);
            setTimeout(function () {
              $("#modal-message").modal("hide");
            }, 3200);
          }

          $("#btn-guardar_despacho_shipper").text("");
          $("#btn-guardar_despacho_shipper").append("Guardar");
          $("#btn-guardar_despacho_shipper").attr("disabled", false);
        },
        error: function (jqXHR, textStatus, errorThrown) {
          //$( '#modal-loader' ).modal('hide');
          $("#moda-message-content").removeClass(
            "bg-danger bg-warning bg-success"
          );

          $("#modal-message").modal("show");
          $("#moda-message-content").addClass("bg-danger");
          $(".modal-title-message").text("Problemas al guardar");
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 1700);

          //Message for developer
          console.log(jqXHR.responseText);

          $("#btn-guardar_despacho_shipper").text("");
          $("#btn-guardar_despacho_shipper").append("Guardar");
          $("#btn-guardar_despacho_shipper").attr("disabled", false);
        },
      });
    }
  });

  $(document).on("click", "#btn-guardar_docs_exportacion", function (e) {
    e.preventDefault();

    $("#btn-guardar_docs_exportacion").text("");
    $("#btn-guardar_docs_exportacion").attr("disabled", true);
    $("#btn-guardar_docs_exportacion").append(
      'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>'
    );

    //$( '#modal-loader' ).modal('show');

    var postData = new FormData($("#form-docs_exportacion")[0]);
    $.ajax({
      url: base_url + "AgenteCompra/PedidosPagados/docsExportacion",
      type: "POST",
      dataType: "JSON",
      data: postData,
      processData: false,
      contentType: false,
      success: function (response) {
        //$( '#modal-loader' ).modal('hide');

        $("#moda-message-content").removeClass(
          "bg-danger bg-warning bg-success"
        );
        $("#modal-message").modal("show");

        if (response.status == "success") {
          $(".modal-docs_exportacion").modal("hide");

          $("#moda-message-content").addClass("bg-" + response.status);
          $(".modal-title-message").text(response.message);
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 2100);
        } else {
          $("#moda-message-content").addClass("bg-danger");
          $(".modal-title-message").text(response.message);
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 3200);
        }

        $("#btn-guardar_docs_exportacion").text("");
        $("#btn-guardar_docs_exportacion").append("Guardar");
        $("#btn-guardar_docs_exportacion").attr("disabled", false);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        //$( '#modal-loader' ).modal('hide');
        $("#moda-message-content").removeClass(
          "bg-danger bg-warning bg-success"
        );

        $("#modal-message").modal("show");
        $("#moda-message-content").addClass("bg-danger");
        $(".modal-title-message").text("Problemas");
        setTimeout(function () {
          $("#modal-message").modal("hide");
        }, 1700);

        //Message for developer
        console.log(jqXHR.responseText);

        $("#btn-guardar_docs_exportacion").text("");
        $("#btn-guardar_docs_exportacion").append("Guardar");
        $("#btn-guardar_docs_exportacion").attr("disabled", false);
      },
    });
  });

  $(document).on("click", "#btn-save_costos_origen_china", function (e) {
    e.preventDefault();

    $("#btn-save_costos_origen_china").text("");
    $("#btn-save_costos_origen_china").attr("disabled", true);
    $("#btn-save_costos_origen_china").append(
      'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>'
    );

    //$( '#modal-loader' ).modal('show');

    url = base_url + "AgenteCompra/PedidosPagados/costosOrigenTradingChina";
    $.ajax({
      type: "POST",
      dataType: "JSON",
      url: url,
      data: $("#form-costos_origen_china").serialize(),
      success: function (response) {
        //$( '#modal-loader' ).modal('hide');

        $("#moda-message-content").removeClass(
          "bg-danger bg-warning bg-success"
        );
        $("#modal-message").modal("show");

        if (response.status == "success") {
          $(".modal-costos_origen_china").modal("hide");

          $("#moda-message-content").addClass("bg-" + response.status);
          $(".modal-title-message").text(response.message);
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 2100);
        } else {
          $("#moda-message-content").addClass("bg-danger");
          $(".modal-title-message").text(response.message);
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 3200);
        }

        $("#btn-save_costos_origen_china").text("");
        $("#btn-save_costos_origen_china").append("Guardar");
        $("#btn-save_costos_origen_china").attr("disabled", false);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        //$( '#modal-loader' ).modal('hide');
        $("#moda-message-content").removeClass(
          "bg-danger bg-warning bg-success"
        );

        $("#modal-message").modal("show");
        $("#moda-message-content").addClass("bg-danger");
        $(".modal-title-message").text("Problemas");
        setTimeout(function () {
          $("#modal-message").modal("hide");
        }, 1700);

        //Message for developer
        console.log(jqXHR.responseText);

        $("#btn-save_costos_origen_china").text("");
        $("#btn-save_costos_origen_china").append("Guardar");
        $("#btn-save_costos_origen_china").attr("disabled", false);
      },
    });
  });

  $(document).on("click", "#btn-save_reserva_booking_trading", function (e) {
    e.preventDefault();

    $("#btn-save_reserva_booking_trading").text("");
    $("#btn-save_reserva_booking_trading").attr("disabled", true);
    $("#btn-save_reserva_booking_trading").append(
      'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>'
    );

    //$( '#modal-loader' ).modal('show');

    url = base_url + "AgenteCompra/PedidosPagados/reservaBookingTrading";
    $.ajax({
      type: "POST",
      dataType: "JSON",
      url: url,
      data: $("#form-reserva_booking_trading").serialize(),
      success: function (response) {
        //$( '#modal-loader' ).modal('hide');

        $("#moda-message-content").removeClass(
          "bg-danger bg-warning bg-success"
        );
        $("#modal-message").modal("show");

        if (response.status == "success") {
          $(".modal-reserva_booking_trading").modal("hide");

          $("#moda-message-content").addClass("bg-" + response.status);
          $(".modal-title-message").text(response.message);
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 2100);
        } else {
          $("#moda-message-content").addClass("bg-danger");
          $(".modal-title-message").text(response.message);
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 3200);
        }

        $("#btn-save_reserva_booking_trading").text("");
        $("#btn-save_reserva_booking_trading").append("Guardar");
        $("#btn-save_reserva_booking_trading").attr("disabled", false);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        //$( '#modal-loader' ).modal('hide');
        $("#moda-message-content").removeClass(
          "bg-danger bg-warning bg-success"
        );

        $("#modal-message").modal("show");
        $("#moda-message-content").addClass("bg-danger");
        $(".modal-title-message").text("Problemas");
        setTimeout(function () {
          $("#modal-message").modal("hide");
        }, 1700);

        //Message for developer
        console.log(jqXHR.responseText);

        $("#btn-save_reserva_booking_trading").text("");
        $("#btn-save_reserva_booking_trading").append("Guardar");
        $("#btn-save_reserva_booking_trading").attr("disabled", false);
      },
    });
  });

  $(document).on(
    "click",
    "#btn-guardar_supervisar_llenado_contenedor",
    function (e) {
      e.preventDefault();

      $("#btn-guardar_supervisar_llenado_contenedor").text("");
      $("#btn-guardar_supervisar_llenado_contenedor").attr("disabled", true);
      $("#btn-guardar_supervisar_llenado_contenedor").append(
        'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>'
      );

      url = base_url + "AgenteCompra/PedidosPagados/supervisarContenedor";
      $.ajax({
        type: "POST",
        dataType: "JSON",
        url: url,
        data: $("#form-supervisar_llenado_contenedor").serialize(),
        success: function (response) {
          //$( '#modal-loader' ).modal('hide');

          $("#moda-message-content").removeClass(
            "bg-danger bg-warning bg-success"
          );
          $("#modal-message").modal("show");

          if (response.status == "success") {
            $(".modal-supervisar_llenado_contenedor").modal("hide");

            $("#moda-message-content").addClass("bg-" + response.status);
            $(".modal-title-message").text(response.message);
            setTimeout(function () {
              $("#modal-message").modal("hide");
            }, 2100);
            reload_table_Entidad();
          } else {
            $("#moda-message-content").addClass("bg-danger");
            $(".modal-title-message").text(response.message);
            setTimeout(function () {
              $("#modal-message").modal("hide");
            }, 3200);
          }

          $("#btn-guardar_supervisar_llenado_contenedor").text("");
          $("#btn-guardar_supervisar_llenado_contenedor").append("Guardar");
          $("#btn-guardar_supervisar_llenado_contenedor").attr(
            "disabled",
            false
          );
        },
        error: function (jqXHR, textStatus, errorThrown) {
          //$( '#modal-loader' ).modal('hide');
          $("#moda-message-content").removeClass(
            "bg-danger bg-warning bg-success"
          );

          $("#modal-message").modal("show");
          $("#moda-message-content").addClass("bg-danger");
          $(".modal-title-message").text("Problemas");
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 1700);

          //Message for developer
          console.log(jqXHR.responseText);

          $("#btn-guardar_supervisar_llenado_contenedor").text("");
          $("#btn-guardar_supervisar_llenado_contenedor").append("Guardar");
          $("#btn-guardar_supervisar_llenado_contenedor").attr(
            "disabled",
            false
          );
        },
      });
    }
  );

  $(document).on("click", "#btn-save_booking_consolidado", function (e) {
    e.preventDefault();

    $("#btn-save_booking_consolidado").text("");
    $("#btn-save_booking_consolidado").attr("disabled", true);
    $("#btn-save_booking_consolidado").append(
      'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>'
    );

    //$( '#modal-loader' ).modal('show');

    url = base_url + "AgenteCompra/PedidosPagados/reservaBookingConsolidado";
    $.ajax({
      type: "POST",
      dataType: "JSON",
      url: url,
      data: $("#form-booking_consolidado").serialize(),
      success: function (response) {
        //$( '#modal-loader' ).modal('hide');

        $("#moda-message-content").removeClass(
          "bg-danger bg-warning bg-success"
        );
        $("#modal-message").modal("show");

        if (response.status == "success") {
          $(".modal-booking_consolidado").modal("hide");

          $("#moda-message-content").addClass("bg-" + response.status);
          $(".modal-title-message").text(response.message);
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 2100);
        } else {
          $("#moda-message-content").addClass("bg-danger");
          $(".modal-title-message").text(response.message);
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 3200);
        }

        $("#btn-save_booking_consolidado").text("");
        $("#btn-save_booking_consolidado").append("Guardar");
        $("#btn-save_booking_consolidado").attr("disabled", false);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        //$( '#modal-loader' ).modal('hide');
        $("#moda-message-content").removeClass(
          "bg-danger bg-warning bg-success"
        );

        $("#modal-message").modal("show");
        $("#moda-message-content").addClass("bg-danger");
        $(".modal-title-message").text("Problemas");
        setTimeout(function () {
          $("#modal-message").modal("hide");
        }, 1700);

        //Message for developer
        console.log(jqXHR.responseText);

        $("#btn-save_booking_consolidado").text("");
        $("#btn-save_booking_consolidado").append("Guardar");
        $("#btn-save_booking_consolidado").attr("disabled", false);
      },
    });
  });

  $(document).on("click", "#btn-save_booking_inspeccion", function (e) {
    e.preventDefault();

    $("#btn-save_booking_inspeccion").text("");
    $("#btn-save_booking_inspeccion").attr("disabled", true);
    $("#btn-save_booking_inspeccion").append(
      'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>'
    );

    //$( '#modal-loader' ).modal('show');

    url = base_url + "AgenteCompra/PedidosPagados/bookingInspeccion";
    $.ajax({
      type: "POST",
      dataType: "JSON",
      url: url,
      data: $("#form-booking_inspeccion").serialize(),
      success: function (response) {
        //$( '#modal-loader' ).modal('hide');

        $("#moda-message-content").removeClass(
          "bg-danger bg-warning bg-success"
        );
        $("#modal-message").modal("show");

        if (response.status == "success") {
          $(".modal-booking_inspeccion").modal("hide");

          $("#moda-message-content").addClass("bg-" + response.status);
          $(".modal-title-message").text(response.message);
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 2100);
        } else {
          $("#moda-message-content").addClass("bg-danger");
          $(".modal-title-message").text(response.message);
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 3200);
        }

        $("#btn-save_booking_inspeccion").text("");
        $("#btn-save_booking_inspeccion").append("Guardar");
        $("#btn-save_booking_inspeccion").attr("disabled", false);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        //$( '#modal-loader' ).modal('hide');
        $("#moda-message-content").removeClass(
          "bg-danger bg-warning bg-success"
        );

        $("#modal-message").modal("show");
        $("#moda-message-content").addClass("bg-danger");
        $(".modal-title-message").text("Problemas");
        setTimeout(function () {
          $("#modal-message").modal("hide");
        }, 1700);

        //Message for developer
        console.log(jqXHR.responseText);

        $("#btn-save_booking_inspeccion").text("");
        $("#btn-save_booking_inspeccion").append("Guardar");
        $("#btn-save_booking_inspeccion").attr("disabled", false);
      },
    });
  });

  $(document).on("click", "#btn-guardar_personal_china", function (e) {
    e.preventDefault();
    if ($("#cbo-guardar_personal_china-ID_Usuario").val() == 0) {
      $("#cbo-guardar_personal_china-ID_Usuario")
        .closest(".form-group")
        .find(".help-block")
        .html("Seleccionar usuario");
      $("#cbo-guardar_personal_china-ID_Usuario")
        .closest(".form-group")
        .removeClass("has-success")
        .addClass("has-error");
    } else {
      $("#btn-guardar_personal_china").text("");
      $("#btn-guardar_personal_china").attr("disabled", true);
      $("#btn-guardar_personal_china").html(
        'Guardando <div class="spinner-border" role="status"><span class="sr-only"></span></div>'
      );

      url = base_url + "AgenteCompra/PedidosPagados/asignarUsuarioPedidoChina";
      $.ajax({
        type: "POST",
        dataType: "JSON",
        url: url,
        data: $("#form-guardar_personal_china").serialize(),
        success: function (response) {
          $("#moda-message-content").removeClass(
            "bg-danger bg-warning bg-success"
          );
          $("#modal-message").modal("show");

          if (response.status == "success") {
            $(".modal-guardar_personal_china").modal("hide");

            $("#moda-message-content").addClass("bg-" + response.status);
            $(".modal-title-message").text(response.message);
            setTimeout(function () {
              $("#modal-message").modal("hide");
            }, 1100);

            reload_table_Entidad();
          } else {
            $("#moda-message-content").addClass("bg-danger");
            $(".modal-title-message").text(response.message);
            setTimeout(function () {
              $("#modal-message").modal("hide");
            }, 1200);
          }

          $("#btn-guardar_personal_china").text("");
          $("#btn-guardar_personal_china").html("Guardar");
          $("#btn-guardar_personal_china").attr("disabled", false);
        },
        error: function (jqXHR, textStatus, errorThrown) {
          $(".modal-message").removeClass(
            "modal-danger modal-warning modal-success"
          );

          $("#modal-message").modal("show");
          $(".modal-message").addClass("modal-danger");
          $(".modal-title-message").text(
            textStatus + " [" + jqXHR.status + "]: " + errorThrown
          );
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 1700);

          //Message for developer
          console.log(jqXHR.responseText);

          $("#btn-guardar_personal_china").text("");
          $("#btn-guardar_personal_china").append("Guardar");
          $("#btn-guardar_personal_china").attr("disabled", false);
        },
      });
    }
  });

  $(document).on("click", "#btn-guardar_fecha_entrega_shipper", function (e) {
    e.preventDefault();

    $("#btn-guardar_fecha_entrega_shipper").text("");
    $("#btn-guardar_fecha_entrega_shipper").attr("disabled", true);
    $("#btn-guardar_fecha_entrega_shipper").append(
      'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>'
    );

    url = base_url + "AgenteCompra/PedidosPagados/despacho";
    $.ajax({
      type: "POST",
      dataType: "JSON",
      url: url,
      data: $("#form-fecha_entrega_shipper").serialize(),
      success: function (response) {
        //$( '#modal-loader' ).modal('hide');

        $("#moda-message-content").removeClass(
          "bg-danger bg-warning bg-success"
        );
        $("#modal-message").modal("show");

        if (response.status == "success") {
          $(".modal-fecha_entrega_shipper").modal("hide");

          $("#moda-message-content").addClass("bg-" + response.status);
          $(".modal-title-message").text(response.message);
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 2100);
          reload_table_Entidad();
        } else {
          $("#moda-message-content").addClass("bg-danger");
          $(".modal-title-message").text(response.message);
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 3200);
        }

        $("#btn-guardar_fecha_entrega_shipper").text("");
        $("#btn-guardar_fecha_entrega_shipper").append("Guardar");
        $("#btn-guardar_fecha_entrega_shipper").attr("disabled", false);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        //$( '#modal-loader' ).modal('hide');
        $("#moda-message-content").removeClass(
          "bg-danger bg-warning bg-success"
        );

        $("#modal-message").modal("show");
        $("#moda-message-content").addClass("bg-danger");
        $(".modal-title-message").text(response.message);
        setTimeout(function () {
          $("#modal-message").modal("hide");
        }, 1700);

        //Message for developer
        console.log(jqXHR.responseText);

        $("#btn-guardar_fecha_entrega_shipper").text("");
        $("#btn-guardar_fecha_entrega_shipper").append("Guardar");
        $("#btn-guardar_fecha_entrega_shipper").attr("disabled", false);
      },
    });
  });

  $(document).on("click", "#btn-save_proveedor", function (e) {
    e.preventDefault();

    $("#btn-save_proveedor").text("");
    $("#btn-save_proveedor").attr("disabled", true);
    $("#btn-save_proveedor").append(
      'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>'
    );

    //$( '#modal-loader' ).modal('show');

    url = base_url + "AgenteCompra/PedidosPagados/crudProveedor";
    $.ajax({
      type: "POST",
      dataType: "JSON",
      url: url,
      data: $("#form-proveedor").serialize(),
      success: function (response) {
        //$( '#modal-loader' ).modal('hide');

        $("#moda-message-content").removeClass(
          "bg-danger bg-warning bg-success"
        );
        $("#modal-message").modal("show");

        if (response.status == "success") {
          $(".modal-proveedor").modal("hide");

          $("#moda-message-content").addClass("bg-" + response.status);
          $(".modal-title-message").text(response.message);
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 2100);
        } else {
          $("#moda-message-content").addClass("bg-danger");
          $(".modal-title-message").text(response.message);
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 3200);
        }

        $("#btn-save_proveedor").text("");
        $("#btn-save_proveedor").append("Guardar");
        $("#btn-save_proveedor").attr("disabled", false);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        //$( '#modal-loader' ).modal('hide');
        $("#moda-message-content").removeClass(
          "bg-danger bg-warning bg-success"
        );

        $("#modal-message").modal("show");
        $("#moda-message-content").addClass("bg-danger");
        $(".modal-title-message").text(response.message);
        setTimeout(function () {
          $("#modal-message").modal("hide");
        }, 1700);

        //Message for developer
        console.log(jqXHR.responseText);

        $("#btn-save_proveedor").text("");
        $("#btn-save_proveedor").append("Guardar");
        $("#btn-save_proveedor").attr("disabled", false);
      },
    });
  });

  $(document).on("click", "#btn-save_booking", function (e) {
    e.preventDefault();

    $("#btn-save_booking").text("");
    $("#btn-save_booking").attr("disabled", true);
    $("#btn-save_booking").append(
      'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>'
    );

    //$( '#modal-loader' ).modal('show');

    url = base_url + "AgenteCompra/PedidosPagados/reservaBooking";
    $.ajax({
      type: "POST",
      dataType: "JSON",
      url: url,
      data: $("#form-booking").serialize(),
      success: function (response) {
        //$( '#modal-loader' ).modal('hide');

        $("#moda-message-content").removeClass(
          "bg-danger bg-warning bg-success"
        );
        $("#modal-message").modal("show");

        if (response.status == "success") {
          $(".modal-booking").modal("hide");

          $("#moda-message-content").addClass("bg-" + response.status);
          $(".modal-title-message").text(response.message);
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 2100);
        } else {
          $("#moda-message-content").addClass("bg-danger");
          $(".modal-title-message").text(response.message);
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 3200);
        }

        $("#btn-save_booking").text("");
        $("#btn-save_booking").append("Guardar");
        $("#btn-save_booking").attr("disabled", false);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        //$( '#modal-loader' ).modal('hide');
        $("#moda-message-content").removeClass(
          "bg-danger bg-warning bg-success"
        );

        $("#modal-message").modal("show");
        $("#moda-message-content").addClass("bg-danger");
        $(".modal-title-message").text(response.message);
        setTimeout(function () {
          $("#modal-message").modal("hide");
        }, 1700);

        //Message for developer
        console.log(jqXHR.responseText);

        $("#btn-save_booking").text("");
        $("#btn-save_booking").append("Guardar");
        $("#btn-save_booking").attr("disabled", false);
      },
    });
  });

  //cambiar precio delivery
  $("#btn-save_comision_trading")
    .off("click")
    .click(function () {
      if ($("#txt-modal-precio_comision_trading").val().length == 0) {
        $("#txt-modal-precio_comision_trading")
          .closest(".form-group")
          .find(".help-block")
          .html("Ingresar precio");
        $("#txt-modal-precio_comision_trading")
          .closest(".form-group")
          .removeClass("has-success")
          .addClass("has-error");
      } else if (
        parseFloat($("#txt-modal-precio_comision_trading").val()) <= 0.0 ||
        isNaN(parseFloat($("#txt-modal-precio_comision_trading").val()))
      ) {
        $("#txt-modal-precio_comision_trading")
          .closest(".form-group")
          .find(".help-block")
          .html("Ingresar precio");
        $("#txt-modal-precio_comision_trading")
          .closest(".form-group")
          .removeClass("has-success")
          .addClass("has-error");
      } else {
        $("#btn-save_comision_trading").text("");
        $("#btn-save_comision_trading").attr("disabled", true);
        $("#btn-save_comision_trading").append(
          'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>'
        );

        var arrData = Array();
        arrData = {
          id_pedido_cabecera: $(
            "#hidden-modal-id_pedido_cabecera_comision_trading"
          ).val(),
          precio_comision_trading: $(
            "#txt-modal-precio_comision_trading"
          ).val(),
        };

        url = base_url + "AgenteCompra/PedidosPagados/agregarComisionTrading";
        $.ajax({
          type: "POST",
          dataType: "JSON",
          url: url,
          data: {
            arrData: arrData,
          },
          success: function (response) {
            $(".modal-comision_trading").modal("hide");

            $(".modal-message").removeClass(
              "modal-danger modal-warning modal-success"
            );
            $("#modal-message").modal("show");

            if (response.status == "success") {
              $(".modal-message").addClass(response.style_modal);
              $(".modal-title-message").text(response.message);
              setTimeout(function () {
                $("#modal-message").modal("hide");
              }, 1100);
              reload_table_Entidad();
            } else {
              $(".modal-message").addClass(response.style_modal);
              $(".modal-title-message").text(response.message);
              setTimeout(function () {
                $("#modal-message").modal("hide");
              }, 1500);
            }

            $("#btn-save_comision_trading").text("");
            $("#btn-save_comision_trading").append("Guardar");
            $("#btn-save_comision_trading").attr("disabled", false);
          },
          error: function (jqXHR, textStatus, errorThrown) {
            $(".modal-message").removeClass(
              "modal-danger modal-warning modal-success"
            );

            $("#modal-message").modal("show");
            $(".modal-message").addClass("modal-danger");
            $(".modal-title-message").text(
              textStatus + " [" + jqXHR.status + "]: " + errorThrown
            );
            setTimeout(function () {
              $("#modal-message").modal("hide");
            }, 1700);

            //Message for developer
            console.log(jqXHR.responseText);

            $("#btn-save_comision_trading").text("");
            $("#btn-save_comision_trading").append("Guardar");
            $("#btn-save_comision_trading").attr("disabled", false);
          },
        });
      }
    });

  //Date picker invoice
  $(".input-report").datepicker({
    autoclose: true,
    startDate: new Date("2023", "10", "01"),
    todayHighlight: true,
    dateFormat: "dd/mm/yyyy",
    format: "dd/mm/yyyy",
  });

  //Date picker invoice
  $(".input-datepicker-pay").datepicker({
    autoclose: true,
    startDate: new Date(fYear, fToday.getMonth(), fDay),
    todayHighlight: true,
    dateFormat: "dd/mm/yyyy",
    format: "dd/mm/yyyy",
  });

  //Global Autocomplete
  $(".autocompletar").autoComplete({
    minChars: 0,
    source: function (term, response) {
      term = term.trim();
      if (term.length > 2) {
        var term = term.toLowerCase();
        $.post(
          base_url +
            "AutocompleteImportacionController/globalAutocompleteItemxUnidad",
          { global_search: term },
          function (arrData) {
            response(arrData);
          },
          "JSON"
        );
      }
    },
    renderItem: function (item, search) {
      search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, "\\$&");
      var re = new RegExp("(" + search.split(" ").join("|") + ")", "gi");
      return (
        '<div title="' +
        caracteresValidosAutocomplete(item.Nombre) +
        '" class="autocomplete-suggestion" data-id="' +
        item.ID +
        '" data-id_item="' +
        item.id_item +
        '" data-id_unidad_medida="' +
        item.ID_Unidad_Medida +
        '" data-id_unidad_medida_2="' +
        item.ID_Unidad_Medida_Precio +
        '" data-precio_importacion="' +
        item.precio_importacion +
        '" data-cantidad_configurada_item="' +
        item.cantidad_configurada_item +
        '" data-nombre_unidad_medida="' +
        item.nombre_unidad_medida +
        '" data-nombre_item="' +
        caracteresValidosAutocomplete(item.nombre_item) +
        '" data-nombre="' +
        caracteresValidosAutocomplete(item.Nombre) +
        '" data-val="' +
        search +
        '">' +
        caracteresValidosAutocomplete(item.Nombre).replace(re, "<b>$1</b>") +
        "</div>"
      );
    },
    onSelect: function (e, term, item) {
      $("#txt-AID").val(item.data("id"));
      $("#txt-ID_Producto").val(item.data("id_item"));
      $("#txt-ID_Unidad_Medida").val(item.data("id_unidad_medida"));
      $("#txt-ID_Unidad_Medida_2").val(item.data("id_unidad_medida_2"));
      $("#txt-Precio_Producto").val(item.data("precio_importacion"));
      $("#txt-Nombre_Producto").val(item.data("nombre_item"));
      $("#txt-Cantidad_Configurada_Producto").val(
        item.data("cantidad_configurada_item")
      );
      $("#txt-Nombre_Unidad_Medida").val(item.data("nombre_unidad_medida"));
      $("#txt-ANombre").val(item.data("nombre"));

      arrItemVentaTemporal = {
        id: item.data("id"),
        id_item: item.data("id_item"),
        id_unidad_medida: item.data("id_unidad_medida"),
        id_unidad_medida_2: item.data("id_unidad_medida_2"),
        precio_item: item.data("precio_importacion"),
        nombre_interno: item.data("nombre"),
        nombre: item.data("nombre_item"),
        cantidad_configurada_item: item.data("cantidad_configurada_item"),
        nombre_unidad_medida: item.data("nombre_unidad_medida"),
      };

      //agregarItemVentaTemporal(arrItemVentaTemporal);
    },
  });

  url = base_url + "AgenteCompra/PedidosPagados/ajax_list";
  var sort_col = $("#table-Pedidos").find("th:contains('Fecha')")[0].cellIndex;
  console.log(sort_col);

  table_Entidad = $("#table-Pedidos").DataTable({
    dom:
      "<'row'<'col-sm-12 col-md-4'B><'col-sm-12 col-md-7'f><'col-sm-12 col-md-1'>>" +
      "<'row'<'col-sm-12'tr>>" +
      "<'row'<'col-sm-12 col-md-2'l><'col-sm-12 col-md-5'i><'col-sm-12 col-md-5'p>>",
    buttons: [
      {
        extend: "excel",
        text: '<i class="fa fa-file-excel color_icon_excel"></i> Excel',
        titleAttr: "Excel",
        exportOptions: {
          columns: ":visible",
        },
      },
      {
        extend: "pdf",
        text: '<i class="fa fa-file-pdf color_icon_pdf"></i> PDF',
        titleAttr: "PDF",
        exportOptions: {
          columns: ":visible",
        },
      },
      {
        extend: "colvis",
        text: '<i class="fa fa-ellipsis-v"></i> Columnas',
        titleAttr: "Columnas",
        exportOptions: {
          columns: ":visible",
        },
      },
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
    order: [[sort_col, "desc"]],
    ajax: {
      url: url,
      type: "POST",
      dataType: "JSON",
      data: function (data) {
        (data.sCorrelativoCotizacion = $(
          "#hidden-sCorrelativoCotizacion"
        ).val()),
          (data.ID_Pedido_Cabecera = $("#hidden-ID_Pedido_Cabecera").val()),
          (data.Filtros_Entidades = $("#cbo-Filtros_Entidades").val()),
          (data.Global_Filter = $("#txt-Global_Filter").val()),
          (data.Filtro_Fe_Inicio = ParseDateString(
            $("#txt-Fe_Inicio").val(),
            "fecha",
            "/"
          )),
          (data.Filtro_Fe_Fin = ParseDateString(
            $("#txt-Fe_Fin").val(),
            "fecha",
            "/"
          ));
        data.Filtro_Estado = $("#txt-ID_Estado").val();
      },
      complete: function () {
        $(".width_full").val($("#hidden-sCorrelativoCotizacion").val());
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
    ],
    lengthMenu: [
      [10, 100, 1000, -1],
      [10, 100, 1000, "Todos"],
    ],
  });

  // jQuery(document).on("keyup", ".width_full", function (ev) {
  //   $("#hidden-sCorrelativoCotizacion").val("");
  //   $("#hidden-ID_Pedido_Cabecera").val("");
  //   reload_table_Entidad();
  // });

  $("#table-Pedidos_filter input").removeClass("form-control-sm");
  $("#table-Pedidos_filter input").addClass("form-control-md");
  $("#table-Pedidos_filter input").addClass("width_full");

  $("#btn-html_reporte").click(function () {
    reload_table_Entidad();
  });

  $(".div-AgregarEditar").hide();

  $("#btn-addProductosEnlaces").click(function () {
    var $ID_Producto = $("#txt-AID").val();
    var $ID_Producto_BD = $("#txt-ID_Producto").val();
    var $ID_Unidad_Medida = $("#txt-ID_Unidad_Medida").val();
    var $ID_Unidad_Medida_2 = $("#txt-ID_Unidad_Medida_2").val();
    var $No_Producto_Enlace = $("#txt-ANombre").val();
    var $Nombre_Producto = $("#txt-Nombre_Producto").val();
    var $Qt_Producto_Enlace = $("#txt-Qt_Producto_Descargar").val();
    var $Cantidad_Configurada_Producto = $(
      "#txt-Cantidad_Configurada_Producto"
    ).val();
    var $Nombre_Unidad_Medida = $("#txt-Nombre_Unidad_Medida").val();
    var $Precio_Producto = $("#txt-Precio_Producto").val();

    if ($ID_Producto.length === 0 || $No_Producto_Enlace.length === 0) {
      $("#txt-ANombre")
        .closest(".form-group")
        .find(".help-block")
        .html("Seleccionar producto");
      $("#txt-ANombre")
        .closest(".form-group")
        .removeClass("has-success")
        .addClass("has-error");
    } else if ($Qt_Producto_Enlace.length === 0) {
      $("#txt-Qt_Producto_Descargar")
        .closest(".form-group")
        .find(".help-block")
        .html("Ingresar cantidad");
      $("#txt-Qt_Producto_Descargar")
        .closest(".form-group")
        .removeClass("has-success")
        .addClass("has-error");
    } else if ($Qt_Producto_Enlace == 0) {
      $("#txt-Qt_Producto_Descargar")
        .closest(".form-group")
        .find(".help-block")
        .html("La cantidad mayor 0");
      $("#txt-Qt_Producto_Descargar")
        .closest(".form-group")
        .removeClass("has-success")
        .addClass("has-error");
    } else {
      var cantidad_item =
        parseFloat($Cantidad_Configurada_Producto) *
        parseFloat($Qt_Producto_Enlace);
      var total_item = cantidad_item * parseFloat($Precio_Producto);
      arrItemVentaTemporal = {
        id: $ID_Producto,
        id_item: $ID_Producto_BD,
        id_unidad_medida: $ID_Unidad_Medida,
        id_unidad_medida_2: $ID_Unidad_Medida_2,
        nombre_interno: $No_Producto_Enlace,
        nombre: $Nombre_Producto,
        cantidad_item: cantidad_item,
        precio_item: $Precio_Producto,
        total_item: total_item,
        nombre_unidad_medida: $Nombre_Unidad_Medida,
      };

      agregarItemVentaTemporal(arrItemVentaTemporal);
    }
  });

  $("#table-Producto_Enlace tbody").on(
    "click",
    "#btn-deleteProductoEnlace",
    function () {
      $(this).closest("tr").remove();
      calcularTotales();
      if ($("#table-Producto_Enlace >tbody >tr").length == 0)
        $("#table-Producto_Enlace").hide();
    }
  );

  $("#form-pedido").validate({
    rules: {
      No_Entidad: {
        required: true,
      },
    },
    messages: {
      No_Entidad: {
        required: "Ingresar nombre",
      },
    },
    errorPlacement: function (error, element) {
      $(element).closest(".form-group").find(".help-block").html(error.html());
    },
    highlight: function (element) {
      $(element)
        .closest(".form-group")
        .removeClass("has-success")
        .addClass("has-error");
    },
    unhighlight: function (element, errorClass, validClass) {
      $(element)
        .closest(".form-group")
        .removeClass("has-error")
        .addClass("has-success");
      $(element).closest(".form-group").find(".help-block").html("");
    },
    submitHandler: form_pedido,
  });

  $("#table-Producto_Enlace").on("click", ".img-table_item", function () {
    $(".img-responsive").attr("src", "");

    $(".modal-ver_item").modal("show");
    $(".img-responsive").attr("src", $(this).data("url_img"));
    $("#a-download_image").attr("data-id_item", $(this).data("id_item"));
  });

  $(document).on("click", ".btn-ver_pago_proveedor", function (e) {
    $(".img-responsive").attr("src", "");

    $(".modal-ver_item").modal("show");
    $(".img-responsive").attr("src", $(this).data("url_img"));
    $("#a-download_image").attr("data-id_item", $(this).data("id"));
  });

  $("#a-download_image").click(function () {
    id = $(this).data("id_item");
    url = base_url + "AgenteCompra/PedidosPagados/downloadImage/" + id;
    src = $(this).data("src");
    filename = src.split("/").pop();

    if (src) {
      $.ajax({
        url: src,
        method: "GET",
        xhrFields: {
          responseType: "blob", // Important
        },
        success: function (data) {
          const blobUrl = window.URL.createObjectURL(data);
          const link = document.createElement("a");
          link.href = blobUrl;
          link.download = filename;
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);
          window.URL.revokeObjectURL(blobUrl);
        },
        error: function (jqXHR, textStatus, errorThrown) {
          console.error("Error downloading file:", textStatus, errorThrown);
        },
      });
      return;
    }
    var popupwin = window.open(url);
    setTimeout(function () {
      popupwin.close();
    }, 2000);
  });

  $(document).on("click", ".btn-agregar_pago_proveedor", function (e) {
    e.preventDefault();

    $("#form-agregar_pago_proveedor")[0].reset();

    $("#img_producto-preview1").html("");
    $("#img_producto-preview1").attr("src", "");

    var id_empresa = $(this).data("id_empresa");
    var id_organizacion = $(this).data("id_organizacion");
    var id_cabecera = $(this).data("id_pedido_cabecera");
    var id_detalle = $(this).data("id_pedido_detalle");
    var id = $(this).data("id");
    var tipo_pago = $(this).data("tipo_pago");
    var correlativo = $(this).data("correlativo");

    $('[name="proveedor-id_empresa"]').val(id_empresa);
    $('[name="proveedor-id_organizacion"]').val(id_organizacion);
    $('[name="proveedor-id_cabecera"]').val(id_cabecera);
    $('[name="proveedor-id_detalle"]').val(id_detalle);
    $('[name="proveedor-id"]').val(id);
    $('[name="proveedor-tipo_pago"]').val(tipo_pago);
    $('[name="proveedor-correlativo"]').val(correlativo);

    $("#modal-agregar_pago").modal("show");

    $("#modal-agregar_pago").on("shown.bs.modal", function () {
      $("#amount_proveedor").focus();
    });
  });

  $(document).on("click", ".btn-agregar_inspeccion", function (e) {
    e.preventDefault();

    $("#form-agregar_inspeccion")[0].reset();

    var id_empresa = $(this).data("id_empresa");
    var id_organizacion = $(this).data("id_organizacion");
    var id_cabecera = $(this).data("id_pedido_cabecera");
    var id_detalle = $(this).data("id_pedido_detalle");
    var id = $(this).data("id");
    var tipo_pago = $(this).data("tipo_pago");
    var correlativo = $(this).data("correlativo");

    $('[name="proveedor-id_empresa"]').val(id_empresa);
    $('[name="proveedor-id_organizacion"]').val(id_organizacion);
    $('[name="proveedor-id_cabecera"]').val(id_cabecera);
    $('[name="proveedor-id_detalle"]').val(id_detalle);
    $('[name="proveedor-id"]').val(id);
    $('[name="proveedor-tipo_pago"]').val(tipo_pago);
    $('[name="proveedor-correlativo"]').val(correlativo);

    $("#modal-agregar_inspeccion").modal("show");
  });

  $(document).on("click", ".btn-eliminar_item_proveedor", function (e) {
    e.preventDefault();

    var id = $(this).data("id");
    var id_pedido_cabecera = $(this).data("id_pedido_cabecera");
    var correlativo = $(this).data("correlativo");
    var name_item = $(this).data("name_item");

    var $modal_delete = $("#modal-message-delete");
    $modal_delete.modal("show");

    $("#modal-title").html("¿Estás seguro de eliminar?");

    $("#btn-cancel-delete")
      .off("click")
      .click(function () {
        $modal_delete.modal("hide");
      });

    $("#btn-save-delete")
      .off("click")
      .click(function () {
        $("#btn-save-delete").text("");
        $("#btn-save-delete").attr("disabled", true);
        $("#btn-save-delete").append(
          'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>'
        );

        url =
          base_url +
          "AgenteCompra/PedidosPagados/elminarItemProveedor/" +
          id +
          "/" +
          correlativo +
          "/" +
          name_item;
        $.ajax({
          url: url,
          type: "GET",
          dataType: "JSON",
          success: function (response) {
            $("#moda-message-content").removeClass(
              "bg-danger bg-warning bg-success"
            );
            $("#modal-message").modal("show");

            if (response.status == "success") {
              $modal_delete.modal("hide");

              verPedido(id_pedido_cabecera);

              $("#moda-message-content").addClass("bg-" + response.status);
              $(".modal-title-message").text(response.message);
              setTimeout(function () {
                $("#modal-message").modal("hide");
              }, 1100);
            } else {
              $("#moda-message-content").addClass("bg-danger");
              $(".modal-title-message").text(response.message);
              setTimeout(function () {
                $("#modal-message").modal("hide");
              }, 1200);
            }

            $("#btn-save-delete").text("");
            $("#btn-save-delete").append("Guardar");
            $("#btn-save-delete").attr("disabled", false);
          },
        });
      });
  });

  $("#form-agregar_inspeccion").on("submit", function (e) {
    e.preventDefault();

    $(".help-block").empty();
    $(".form-group").removeClass("has-error");

    if (document.getElementById("image_inspeccion").files.length == 0) {
      $("#image_inspeccion")
        .closest(".form-group")
        .find(".help-block")
        .html("Empty image");
      $("#image_inspeccion")
        .closest(".form-group")
        .removeClass("has-success")
        .addClass("has-error");
    } else {
      var postData = new FormData($("#form-agregar_inspeccion")[0]);
      $.ajax({
        url: base_url + "AgenteCompra/PedidosPagados/addInspeccionProveedor",
        type: "POST",
        dataType: "JSON",
        data: postData,
        processData: false,
        contentType: false,
      }).done(function (response) {
        if (response.status == "success") {
          $("#modal-agregar_inspeccion").modal("hide");
          subirInspeccion($("#proveedor-id_cabecera").val());
        } else {
          alert(response.message);
        }
      });
    }
  });

  $("#form-agregar_pago_proveedor").on("submit", function (e) {
    e.preventDefault();

    $(".help-block").empty();
    $(".form-group").removeClass("has-error");

    /*
    const amount_proveedor = parseFloat($('#amount_proveedor').val())

    if( isNaN(amount_proveedor) || amount_proveedor<=0.00 || amount_proveedor<=0) {
      $('#amount_proveedor').closest('.form-group').find('.help-block').html('Empty Amount');
      $('#amount_proveedor').closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if(document.getElementById('voucher_proveedor').files.length == 0) {
      $('#voucher_proveedor').closest('.form-group').find('.help-block').html('Empty image');
      $('#voucher_proveedor').closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
    */
    var postData = new FormData($("#form-agregar_pago_proveedor")[0]);
    $.ajax({
      url: base_url + "AgenteCompra/PedidosPagados/addPagoProveedor",
      type: "POST",
      dataType: "JSON",
      data: postData,
      processData: false,
      contentType: false,
    }).done(function (response) {
      if (response.status == "success") {
        $("#modal-agregar_pago").modal("hide");
        //verPedido($('#proveedor-id_cabecera').val());
        pagarProveedores(
          $("#proveedor-id_cabecera").val(),
          $("#proveedor-tipo_pago").val()
        );
      } else {
        alert(response.message);
      }
    });
    //}
  });

  $("#form-documento_entrega").on("submit", function (e) {
    e.preventDefault();

    $(".help-block").empty();
    $(".form-group").removeClass("has-error");

    if (document.getElementById("image_documento").files.length == 0) {
      $("#image_documento")
        .closest(".form-group")
        .find(".help-block")
        .html("Empty file");
      $("#image_documento")
        .closest(".form-group")
        .removeClass("has-success")
        .addClass("has-error");
    } else if (
      document.getElementById("image_documento_detalle").files.length == 0
    ) {
      $("#image_documento_detalle")
        .closest(".form-group")
        .find(".help-block")
        .html("Empty file");
      $("#image_documento_detalle")
        .closest(".form-group")
        .removeClass("has-success")
        .addClass("has-error");
    } else {
      var postData = new FormData($("#form-documento_entrega")[0]);
      $.ajax({
        url: base_url + "AgenteCompra/PedidosPagados/addFileProveedor",
        type: "POST",
        dataType: "JSON",
        data: postData,
        processData: false,
        contentType: false,
      }).done(function (response) {
        $("#moda-message-content").removeClass(
          "bg-danger bg-warning bg-success"
        );
        $("#modal-message").modal("show");

        if (response.status == "success") {
          $("#modal-documento_entrega").modal("hide");

          $("#moda-message-content").addClass("bg-" + response.status);
          $(".modal-title-message").text(response.message);
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 1100);
          reload_table_Entidad();
        } else {
          $("#moda-message-content").addClass("bg-danger");
          $(".modal-title-message").text(response.message);
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 2100);
        }
      });
    }
  });

  $("#form-pago_cliente_30").on("submit", function (e) {
    e.preventDefault();

    $(".help-block").empty();
    $(".form-group").removeClass("has-error");

    if (document.getElementById("pago_cliente_30").files.length == 0) {
      $("#pago_cliente_30")
        .closest(".form-group")
        .find(".help-block")
        .html("Empty file");
      $("#pago_cliente_30")
        .closest(".form-group")
        .removeClass("has-success")
        .addClass("has-error");
    } else {
      var postData = new FormData($("#form-pago_cliente_30")[0]);
      $.ajax({
        url: base_url + "AgenteCompra/PedidosPagados/addPagoCliente30",
        type: "POST",
        dataType: "JSON",
        data: postData,
        processData: false,
        contentType: false,
      }).done(function (response) {
        $("#moda-message-content").removeClass(
          "bg-danger bg-warning bg-success"
        );
        $("#modal-message").modal("show");

        if (response.status == "success") {
          $("#modal-pago_cliente_30").modal("hide");

          verPedido($("#pago_cliente_30-id_cabecera").val());

          $("#moda-message-content").addClass("bg-" + response.status);
          $(".modal-title-message").text(response.message);
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 1100);
        } else {
          $("#moda-message-content").addClass("bg-danger");
          $(".modal-title-message").text(response.message);
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 2100);
        }
      });
    }
  });

  $("#form-pago_cliente_100").on("submit", function (e) {
    e.preventDefault();

    $(".help-block").empty();
    $(".form-group").removeClass("has-error");

    if (document.getElementById("pago_cliente_100").files.length == 0) {
      $("#pago_cliente_100")
        .closest(".form-group")
        .find(".help-block")
        .html("Empty file");
      $("#pago_cliente_100")
        .closest(".form-group")
        .removeClass("has-success")
        .addClass("has-error");
    } else {
      var postData = new FormData($("#form-pago_cliente_100")[0]);
      $.ajax({
        url: base_url + "AgenteCompra/PedidosPagados/addPagoCliente100",
        type: "POST",
        dataType: "JSON",
        data: postData,
        processData: false,
        contentType: false,
      }).done(function (response) {
        $("#moda-message-content").removeClass(
          "bg-danger bg-warning bg-success"
        );
        $("#modal-message").modal("show");

        if (response.status == "success") {
          $("#modal-pago_cliente_100").modal("hide");

          verPedido($("#pago_cliente_100-id_cabecera").val());

          $("#moda-message-content").addClass("bg-" + response.status);
          $(".modal-title-message").text(response.message);
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 1100);
        } else {
          $("#moda-message-content").addClass("bg-danger");
          $(".modal-title-message").text(response.message);
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 2100);
        }
      });
    }
  });

  $("#form-pago_cliente_servicio").on("submit", function (e) {
    e.preventDefault();

    $(".help-block").empty();
    $(".form-group").removeClass("has-error");

    if (document.getElementById("pago_cliente_servicio").files.length == 0) {
      $("#pago_cliente_servicio")
        .closest(".form-group")
        .find(".help-block")
        .html("Empty file");
      $("#pago_cliente_servicio")
        .closest(".form-group")
        .removeClass("has-success")
        .addClass("has-error");
    } else {
      var postData = new FormData($("#form-pago_cliente_servicio")[0]);
      $.ajax({
        url: base_url + "AgenteCompra/PedidosPagados/addPagoClienteServicio",
        type: "POST",
        dataType: "JSON",
        data: postData,
        processData: false,
        contentType: false,
      }).done(function (response) {
        $("#moda-message-content").removeClass(
          "bg-danger bg-warning bg-success"
        );
        $("#modal-message").modal("show");

        if (response.status == "success") {
          $("#modal-pago_cliente_servicio").modal("hide");

          verPedido($("#pago_cliente_servicio-id_cabecera").val());

          $("#moda-message-content").addClass("bg-" + response.status);
          $(".modal-title-message").text(response.message);
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 1100);
        } else {
          $("#moda-message-content").addClass("bg-danger");
          $(".modal-title-message").text(response.message);
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 2100);
        }
      });
    }
  });

  $("#form-pago_flete").on("submit", function (e) {
    e.preventDefault();

    $(".help-block").empty();
    $(".form-group").removeClass("has-error");

    if (document.getElementById("pago_flete").files.length == 0) {
      $("#pago_flete")
        .closest(".form-group")
        .find(".help-block")
        .html("Empty file");
      $("#pago_flete")
        .closest(".form-group")
        .removeClass("has-success")
        .addClass("has-error");
    } else {
      var postData = new FormData($("#form-pago_flete")[0]);
      $.ajax({
        url: base_url + "AgenteCompra/PedidosPagados/addPagoFlete",
        type: "POST",
        dataType: "JSON",
        data: postData,
        processData: false,
        contentType: false,
      }).done(function (response) {
        $("#moda-message-content").removeClass(
          "bg-danger bg-warning bg-success"
        );
        $("#modal-message").modal("show");

        if (response.status == "success") {
          $("#modal-pago_flete").modal("hide");

          verPedido($("#pago_flete-id_cabecera").val());

          $("#moda-message-content").addClass("bg-" + response.status);
          $(".modal-title-message").text(response.message);
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 1100);
        } else {
          $("#moda-message-content").addClass("bg-danger");
          $(".modal-title-message").text(response.message);
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 2100);
        }
      });
    }
  });

  $("#form-costos_origen").on("submit", function (e) {
    e.preventDefault();

    $(".help-block").empty();
    $(".form-group").removeClass("has-error");

    if (document.getElementById("costos_origen").files.length == 0) {
      $("#costos_origen")
        .closest(".form-group")
        .find(".help-block")
        .html("Empty file");
      $("#costos_origen")
        .closest(".form-group")
        .removeClass("has-success")
        .addClass("has-error");
    } else {
      var postData = new FormData($("#form-costos_origen")[0]);
      $.ajax({
        url: base_url + "AgenteCompra/PedidosPagados/addPagoCostosOrigen",
        type: "POST",
        dataType: "JSON",
        data: postData,
        processData: false,
        contentType: false,
      }).done(function (response) {
        $("#moda-message-content").removeClass(
          "bg-danger bg-warning bg-success"
        );
        $("#modal-message").modal("show");

        if (response.status == "success") {
          $("#modal-costos_origen").modal("hide");

          verPedido($("#costos_origen-id_cabecera").val());

          $("#moda-message-content").addClass("bg-" + response.status);
          $(".modal-title-message").text(response.message);
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 1100);
        } else {
          $("#moda-message-content").addClass("bg-danger");
          $(".modal-title-message").text(response.message);
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 2100);
        }
      });
    }
  });

  $("#form-pago_fta").on("submit", function (e) {
    e.preventDefault();

    $(".help-block").empty();
    $(".form-group").removeClass("has-error");

    if (document.getElementById("pago_fta").files.length == 0) {
      $("#pago_fta")
        .closest(".form-group")
        .find(".help-block")
        .html("Empty file");
      $("#pago_fta")
        .closest(".form-group")
        .removeClass("has-success")
        .addClass("has-error");
    } else {
      var postData = new FormData($("#form-pago_fta")[0]);
      $.ajax({
        url: base_url + "AgenteCompra/PedidosPagados/addPagoFta",
        type: "POST",
        dataType: "JSON",
        data: postData,
        processData: false,
        contentType: false,
      }).done(function (response) {
        $("#moda-message-content").removeClass(
          "bg-danger bg-warning bg-success"
        );
        $("#modal-message").modal("show");

        if (response.status == "success") {
          $("#modal-pago_fta").modal("hide");

          verPedido($("#pago_fta-id_cabecera").val());

          $("#moda-message-content").addClass("bg-" + response.status);
          $(".modal-title-message").text(response.message);
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 1100);
        } else {
          $("#moda-message-content").addClass("bg-danger");
          $(".modal-title-message").text(response.message);
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 2100);
        }
      });
    }
  });

  $("#form-otros_cuadrilla").on("submit", function (e) {
    e.preventDefault();

    $(".help-block").empty();
    $(".form-group").removeClass("has-error");

    if (document.getElementById("otros_cuadrilla").files.length == 0) {
      $("#otros_cuadrilla")
        .closest(".form-group")
        .find(".help-block")
        .html("Empty file");
      $("#otros_cuadrilla")
        .closest(".form-group")
        .removeClass("has-success")
        .addClass("has-error");
    } else {
      var postData = new FormData($("#form-otros_cuadrilla")[0]);
      $.ajax({
        url: base_url + "AgenteCompra/PedidosPagados/addOtrosCuadrilla",
        type: "POST",
        dataType: "JSON",
        data: postData,
        processData: false,
        contentType: false,
      }).done(function (response) {
        $("#moda-message-content").removeClass(
          "bg-danger bg-warning bg-success"
        );
        $("#modal-message").modal("show");

        if (response.status == "success") {
          $("#modal-otros_cuadrilla").modal("hide");

          verPedido($("#otros_cuadrilla-id_cabecera").val());

          $("#moda-message-content").addClass("bg-" + response.status);
          $(".modal-title-message").text(response.message);
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 1100);
        } else {
          $("#moda-message-content").addClass("bg-danger");
          $(".modal-title-message").text(response.message);
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 2100);
        }
      });
    }
  });

  $("#form-otros_costos").on("submit", function (e) {
    e.preventDefault();

    $(".help-block").empty();
    $(".form-group").removeClass("has-error");

    if (document.getElementById("otros_costos").files.length == 0) {
      $("#otros_costos")
        .closest(".form-group")
        .find(".help-block")
        .html("Empty file");
      $("#otros_costos")
        .closest(".form-group")
        .removeClass("has-success")
        .addClass("has-error");
    } else {
      var postData = new FormData($("#form-otros_costos")[0]);
      $.ajax({
        url: base_url + "AgenteCompra/PedidosPagados/addOtrosCostos",
        type: "POST",
        dataType: "JSON",
        data: postData,
        processData: false,
        contentType: false,
      }).done(function (response) {
        $("#moda-message-content").removeClass(
          "bg-danger bg-warning bg-success"
        );
        $("#modal-message").modal("show");

        if (response.status == "success") {
          $("#modal-otros_costos").modal("hide");

          verPedido($("#otros_costos-id_cabecera").val());

          $("#moda-message-content").addClass("bg-" + response.status);
          $(".modal-title-message").text(response.message);
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 1100);
        } else {
          $("#moda-message-content").addClass("bg-danger");
          $(".modal-title-message").text(response.message);
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 2100);
        }
      });
    }
  });

  $("#span-id_pedido").html("");

  $(document).on("click", ".btn-estado_item_proveedor", function (e) {
    e.preventDefault();

    var id = $(this).data("id");
    var id_pedido_cabecera = $(this).data("id_pedido_cabecera");
    var cantidad = $("#input-cantidad" + id).val();
    var estado = $(this).data("estado");

    $(".btn-cargando_item_proveedor" + id).text("");
    $(".btn-cargando_item_proveedor" + id).attr("disabled", true);
    $(".btn-cargando_item_proveedor" + id).html(
      'Guardando <div class="spinner-border" role="status"><span class="sr-only"></span></div>'
    );

    url =
      base_url +
      "AgenteCompra/PedidosPagados/actualizarRecepcionCargaItemProveedor";
    $.ajax({
      type: "POST",
      dataType: "JSON",
      url: url,
      data: {
        id: id,
        cantidad: cantidad,
        estado: estado,
      },
      success: function (response) {
        $("#moda-message-content").removeClass(
          "bg-danger bg-warning bg-success"
        );
        $("#modal-message").modal("show");

        if (response.status == "success") {
          recepcionCarga(id_pedido_cabecera);

          $("#moda-message-content").addClass("bg-" + response.status);
          $(".modal-title-message").text(response.message);
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 1100);
        } else {
          $("#moda-message-content").addClass("bg-danger");
          $(".modal-title-message").text(response.message);
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 1200);
        }

        $(".btn-cargando_item_proveedor" + id).text("");
        $(".btn-cargando_item_proveedor" + id).html("Guardar");
        $(".btn-cargando_item_proveedor" + id).attr("disabled", false);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        $(".modal-message").removeClass(
          "modal-danger modal-warning modal-success"
        );

        $("#modal-message").modal("show");
        $(".modal-message").addClass("modal-danger");
        $(".modal-title-message").text(
          textStatus + " [" + jqXHR.status + "]: " + errorThrown
        );
        setTimeout(function () {
          $("#modal-message").modal("hide");
        }, 1700);

        //Message for developer
        console.log(jqXHR.responseText);

        $(".btn-cargando_item_proveedor" + id).text("");
        $(".btn-cargando_item_proveedor" + id).append("Guardar");
        $(".btn-cargando_item_proveedor" + id).attr("disabled", false);
      },
    });
  });

  $(document).on("click", ".btn-finalizar_item_proveedor", function (e) {
    e.preventDefault();

    var id = $(this).data("id");
    var id_pedido_cabecera = $(this).data("id_pedido_cabecera");
    var nota = $("#textarea-nota" + id).val();

    $("#btn-finalizar_item_proveedor" + id).text("");
    $("#btn-finalizar_item_proveedor" + id).attr("disabled", true);
    $("#btn-finalizar_item_proveedor" + id).html(
      'Guardando <div class="spinner-border" role="status"><span class="sr-only"></span></div>'
    );

    url =
      base_url +
      "AgenteCompra/PedidosPagados/actualizarRecepcionCargaProveedor";
    $.ajax({
      type: "POST",
      dataType: "JSON",
      url: url,
      data: {
        id: id,
        nota: nota,
      },
      success: function (response) {
        $("#moda-message-content").removeClass(
          "bg-danger bg-warning bg-success"
        );
        $("#modal-message").modal("show");

        if (response.status == "success") {
          recepcionCarga(id_pedido_cabecera);

          $("#moda-message-content").addClass("bg-" + response.status);
          $(".modal-title-message").text(response.message);
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 1100);
        } else {
          $("#moda-message-content").addClass("bg-danger");
          $(".modal-title-message").text(response.message);
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 1200);
        }

        $("#btn-finalizar_item_proveedor" + id).text("");
        $("#btn-finalizar_item_proveedor" + id).html("Guardar");
        $("#btn-finalizar_item_proveedor" + id).attr("disabled", false);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        $(".modal-message").removeClass(
          "modal-danger modal-warning modal-success"
        );

        $("#modal-message").modal("show");
        $(".modal-message").addClass("modal-danger");
        $(".modal-title-message").text(
          textStatus + " [" + jqXHR.status + "]: " + errorThrown
        );
        setTimeout(function () {
          $("#modal-message").modal("hide");
        }, 1700);

        //Message for developer
        console.log(jqXHR.responseText);

        $("#btn-finalizar_item_proveedor" + id).text("");
        $("#btn-finalizar_item_proveedor" + id).append("Guardar");
        $("#btn-finalizar_item_proveedor" + id).attr("disabled", false);
      },
    });
  });

  $(document).on("click", ".btn-image_documento", function (e) {
    e.preventDefault();

    var id = $(this).data("id");
    var id_pedido_cabecera = $(this).data("id_pedido_cabecera");

    $("#btn-image_documento" + id).text("");
    $("#btn-image_documento" + id).attr("disabled", true);
    $("#btn-image_documento" + id).html(
      'Guardando <div class="spinner-border" role="status"><span class="sr-only"></span></div>'
    );

    var postData = new FormData($("#form-invoice_pl_proveedor" + id)[0]);
    url = base_url + "AgenteCompra/PedidosPagados/subirInvoicePlProveedor";
    $.ajax({
      type: "POST",
      dataType: "JSON",
      url: url,
      data: postData,
      processData: false,
      contentType: false,
      success: function (response) {
        $("#moda-message-content").removeClass(
          "bg-danger bg-warning bg-success"
        );
        $("#modal-message").modal("show");

        if (response.status == "success") {
          invoiceProveedor(id_pedido_cabecera);

          $("#moda-message-content").addClass("bg-" + response.status);
          $(".modal-title-message").text(response.message);
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 1100);
        } else {
          $("#moda-message-content").addClass("bg-danger");
          $(".modal-title-message").text(response.message);
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 1200);
        }

        $("#btn-finalizar_item_proveedor" + id).text("");
        $("#btn-finalizar_item_proveedor" + id).html("Guardar");
        $("#btn-finalizar_item_proveedor" + id).attr("disabled", false);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        $(".modal-message").removeClass(
          "modal-danger modal-warning modal-success"
        );

        $("#modal-message").modal("show");
        $(".modal-message").addClass("modal-danger");
        $(".modal-title-message").text(
          textStatus + " [" + jqXHR.status + "]: " + errorThrown
        );
        setTimeout(function () {
          $("#modal-message").modal("hide");
        }, 1700);

        //Message for developer
        console.log(jqXHR.responseText);

        $("#btn-finalizar_item_proveedor" + id).text("");
        $("#btn-finalizar_item_proveedor" + id).append("Guardar");
        $("#btn-finalizar_item_proveedor" + id).attr("disabled", false);
      },
    });
  });

  $(document).on("click", ".btn-cambiar_item_proveedor", function (e) {
    e.preventDefault();

    var id = $(this).data("id");
    var id_pedido_cabecera = $(this).data("id_pedido_cabecera");

    $('[name="cambio_item_proveedor-id_item"]').val(id);
    $('[name="cambio_item_proveedor-id_cabecera"]').val(id_pedido_cabecera);

    $("#modal-cambio_item_proveedor").modal("show");
    $("#form-cambio_item_proveedor")[0].reset();
  });
});

function reload_table_Entidad() {
  table_Entidad.ajax.reload(null, false);
}

function invoiceProveedor(ID) {
  $(".div-Listar").hide();

  $("#form-pedido")[0].reset();
  $(".form-group").removeClass("has-error");
  $(".form-group").removeClass("has-success");
  $(".help-block").empty();

  $(".div-Compuesto").hide();
  $(".div-Producto_Recepcion_Carga").hide();
  $("#table-Producto_Enlace tbody").empty();
  $("#table-Producto_Recepcion_Carga tbody").empty();

  $(".div-Invoice_Proveedor").show();
  $("#table-Invoice_Proveedor tbody").empty();
  $("#table-Invoice_Proveedor").show();

  $(".div-Pago_Proveedor").hide();
  $("#table-Pago_Proveedor tbody").empty();

  //$('#span-id_pedido').html('Nro. ' + ID);

  url = base_url + "AgenteCompra/PedidosPagados/ajax_edit/" + ID;
  $.ajax({
    url: url,
    type: "GET",
    dataType: "JSON",
    success: function (response) {
      console.log(response);
      var detalle = response;
      response = response[0];

      $("#span-id_pedido").html(response.sCorrelativoCotizacion);

      $(".div-AgregarEditar").show();

      $('[name="EID_Pedido_Cabecera"]').val(response.ID_Pedido_Cabecera);
      $('[name="EID_Entidad"]').val(response.ID_Entidad);
      $('[name="EID_Empresa"]').val(response.ID_Empresa);
      $('[name="EID_Organizacion"]').val(response.ID_Organizacion);

      $('[name="No_Contacto"]').val(response.No_Contacto);
      $('[name="Txt_Email_Contacto"]').val(response.Txt_Email_Contacto);
      $('[name="Nu_Celular_Contacto"]').val(response.Nu_Celular_Contacto);
      $('[name="No_Entidad"]').val(response.No_Entidad);
      $('[name="Nu_Documento_Identidad"]').val(response.Nu_Documento_Identidad);

      //$( '#btn-excel_order_tracking' ).data('id_pedido', response.ID_Pedido_Cabecera);
      $("#btn-excel_order_tracking").attr(
        "data-id_pedido",
        response.ID_Pedido_Cabecera
      ); // sets

      $("#btn-descargar_pago_30").hide();
      $("#span-pago_30").html("");
      if (
        response.Txt_Url_Pago_30_Cliente != "" &&
        response.Txt_Url_Pago_30_Cliente != null
      ) {
        $("#btn-descargar_pago_30").show();
        $("#btn-descargar_pago_30").removeClass("d-none");

        $("#span-pago_30").html("$ " + response.Ss_Pago_30_Cliente);
      }

      $("#btn-descargar_pago_100").hide();
      $("#span-pago_100").html("");
      if (
        response.Txt_Url_Pago_100_Cliente != "" &&
        response.Txt_Url_Pago_100_Cliente != null
      ) {
        $("#btn-descargar_pago_100").show();
        $("#btn-descargar_pago_100").removeClass("d-none");

        $("#span-pago_100").html("$ " + response.Ss_Pago_100_Cliente);
      }

      $("#btn-descargar_pago_servicio").hide();
      $("#span-pago_servicio").html("");
      if (
        response.Txt_Url_Pago_Servicio_Cliente != "" &&
        response.Txt_Url_Pago_Servicio_Cliente != null
      ) {
        $("#btn-descargar_pago_servicio").show();
        $("#btn-descargar_pago_servicio").removeClass("d-none");

        $("#span-pago_servicio").html("$ " + response.Ss_Pago_Servicio_Cliente);
      }

      $("#btn-descargar_flete").hide();
      $("#span-flete").html("");
      if (
        response.Txt_Url_Pago_Otros_Flete != "" &&
        response.Txt_Url_Pago_Otros_Flete != null
      ) {
        $("#btn-descargar_flete").show();
        $("#btn-descargar_flete").removeClass("d-none");

        $("#span-flete").html("$ " + response.Ss_Pago_Otros_Flete);
      }

      $("#btn-descargar_costo_origen").hide();
      $("#span-costo_origen").html("");
      if (
        response.Txt_Url_Pago_Otros_Costo_Origen != "" &&
        response.Txt_Url_Pago_Otros_Costo_Origen != null
      ) {
        $("#btn-descargar_costo_origen").show();
        $("#btn-descargar_costo_origen").removeClass("d-none");

        $("#span-costo_origen").html(
          "$ " + response.Ss_Pago_Otros_Costo_Origen
        );
      }

      $("#btn-descargar_fta").hide();
      $("#span-fta").html("");
      if (
        response.Txt_Url_Pago_Otros_Costo_Fta != "" &&
        response.Txt_Url_Pago_Otros_Costo_Fta != null
      ) {
        $("#btn-descargar_fta").show();
        $("#btn-descargar_fta").removeClass("d-none");

        $("#span-fta").html("$ " + response.Ss_Pago_Otros_Costo_Fta);
      }

      $("#btn-descargar_pago_cuadrilla").hide();
      $("#span-cuadrilla").html("");
      if (
        response.Txt_Url_Pago_Otros_Cuadrilla != "" &&
        response.Txt_Url_Pago_Otros_Cuadrilla != null
      ) {
        $("#btn-descargar_pago_cuadrilla").show();
        $("#btn-descargar_pago_cuadrilla").removeClass("d-none");

        $("#span-cuadrilla").html("$ " + response.Ss_Pago_Otros_Cuadrilla);
      }

      $("#btn-descargar_otros_costos").hide();
      $("#span-otros_costo").html("");
      if (
        response.Txt_Url_Pago_Otros_Costos != "" &&
        response.Txt_Url_Pago_Otros_Costos != null
      ) {
        $("#btn-descargar_otros_costos").show();
        $("#btn-descargar_otros_costos").removeClass("d-none");

        $("#span-otros_costo").html("$ " + response.Ss_Pago_Otros_Costos);
      }

      var sNombreEstado =
        '<span class="badge badge-pill badge-secondary">Pendiente</span>';
      if (response.Nu_Estado_Pedido == 2)
        sNombreEstado =
          '<span class="badge badge-pill badge-primary">Confirmado</span>';
      else if (response.Nu_Estado_Pedido == 3)
        sNombreEstado =
          '<span class="badge badge-pill badge-success">Entregado</span>';
      else if (response.Nu_Estado_Pedido == 4)
        sNombreEstado =
          '<span class="badge badge-pill badge-danger">Confirmado</span>';
      $("#div-estado").html(sNombreEstado);

      var table_enlace_producto = "",
        iDiasVencimiento = 0,
        sClassColorTr = "",
        fTotalCliente = 0,
        ID_Entidad = "";
      for (i = 0; i < detalle.length; i++) {
        var cantidad_item_final_recepcion_carga = parseFloat(
          detalle[i]["Qt_Producto_Caja_Final_Verificada"]
        );
        var cantidad_item = parseFloat(detalle[i]["Qt_Producto"]);
        var precio_china = parseFloat(detalle[i]["Ss_Precio"]);

        fTotalCliente +=
          cantidad_item * (precio_china * parseFloat(response.Ss_Tipo_Cambio));

        var id_item = detalle[i]["ID_Pedido_Detalle_Producto_Proveedor"];
        var voucher_1 = detalle[i]["Txt_Url_Archivo_Pago_1_Proveedor"];
        var voucher_2 = detalle[i]["Txt_Url_Archivo_Pago_2_Proveedor"];
        //max-height: 350px;width: 100%; cursor:pointer

        var fTotal = cantidad_item * precio_china;
        var Ss_Pago_1_Proveedor = parseFloat(detalle[i]["Ss_Pago_1_Proveedor"]);
        var Ss_Pago_2_Proveedor = parseFloat(detalle[i]["Ss_Pago_2_Proveedor"]);

        sClassColorTr = "";
        iDiasVencimiento = 0;
        if (
          detalle[i]["Fe_Entrega_Proveedor"] != "" &&
          detalle[i]["Fe_Entrega_Proveedor"] != null
        ) {
          var fechaInicio = new Date(
            fYear + "-" + fMonth + "-" + fDay
          ).getTime();
          var fechaFin = new Date(detalle[i]["Fe_Entrega_Proveedor"]).getTime();

          var diff = fechaFin - fechaInicio;
          iDiasVencimiento = diff / (1000 * 60 * 60 * 24); // --> milisegundos -> segundos -> minutos -> horas -> días
          if (iDiasVencimiento < 5) sClassColorTr = "table-warning";
        }

        var fecha_entrega_proveedor =
          detalle[i]["Fe_Entrega_Proveedor"] != "" &&
          detalle[i]["Fe_Entrega_Proveedor"] != null
            ? ParseDateString(
                detalle[i]["Fe_Entrega_Proveedor"],
                "fecha_bd",
                "-"
              )
            : "";

        var nota_final =
          detalle[i]["Txt_Nota_Recepcion_Carga_Proveedor"] != "" &&
          detalle[i]["Txt_Nota_Recepcion_Carga_Proveedor"] != null
            ? detalle[i]["Txt_Nota_Recepcion_Carga_Proveedor"]
            : "";

        if (ID_Entidad != detalle[i].ID_Entidad_Proveedor) {
          table_enlace_producto += "<tr>";
          table_enlace_producto +=
            "<th class='text-left'>" +
            detalle[i].No_Contacto_Proveedor +
            "</th>";
          table_enlace_producto += "<th class='text-left'>"; //Txt_Url_Archivo_Invoice_Pl_Recepcion_Carga_Proveedor si es diferente de vacio descargar
          if (
            detalle[i].Txt_Url_Archivo_Invoice_Pl_Recepcion_Carga_Proveedor !=
              "" &&
            detalle[i].Txt_Url_Archivo_Invoice_Pl_Recepcion_Carga_Proveedor !=
              null
          ) {
            table_enlace_producto +=
              '<button class="btn btn-link" alt="Descargar Invoice y PL" title="Descargar Invoice y PL" href="javascript:void(0)" onclick="descargarInvoicePlProveedor(' +
              id_item +
              ')">Descargar</button>';
          } else {
            table_enlace_producto +=
              '<form action="' +
              base_url +
              'AgenteCompra/PedidosPagados/listar" id="form-invoice_pl_proveedor' +
              id_item +
              '" method="post" accept-charset="utf-8">';
            table_enlace_producto +=
              '<input type="hidden" id="documento-id" name="documento-id" value="' +
              id_item +
              '" class="form-control"></input>';
            table_enlace_producto +=
              '<input class="form-control" id="image_documento' +
              id_item +
              '" name="image_documento" type="file" accept="application/msword, application/vnd.ms-excel, application/pdf, .csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"></input>';
            table_enlace_producto += "</form>";
          }
          //table_enlace_producto += '<textarea id="textarea-nota' + id_item + '" name="addProducto[' + id_item + '][nota]" class="form-control required nota" placeholder="Observaciones" rows="1" style="height: 50px;">' + clearHTMLTextArea(nota_final) + '</textarea>';
          table_enlace_producto += "</th>";
          table_enlace_producto += "<th class='text-center'>";
          if (
            detalle[i].Txt_Url_Archivo_Invoice_Pl_Recepcion_Carga_Proveedor !=
              "" &&
            detalle[i].Txt_Url_Archivo_Invoice_Pl_Recepcion_Carga_Proveedor !=
              null
          ) {
            table_enlace_producto += "";
          } else {
            table_enlace_producto +=
              '<button type="button" id="btn-image_documento' +
              id_item +
              '" data-name_item="' +
              detalle[i]["Txt_Producto"] +
              '" data-id_pedido_cabecera="' +
              response.ID_Pedido_Cabecera +
              '" data-id="' +
              id_item +
              '" data-correlativo="' +
              response.sCorrelativoCotizacion +
              '" class="text-left btn btn-primary btn-image_documento"> Subir archivo </button>';
          }
          table_enlace_producto += "</th>";
          +"</tr>";
          ID_Entidad = detalle[i].ID_Entidad_Proveedor;
        }
      }

      $("#span-total_cantidad_items").html(i);
      $("#table-Invoice_Proveedor").append(table_enlace_producto);

      $("#span-total_cliente").html("$ " + fTotalCliente.toFixed(2));

      $("#span-saldo_cliente").html(
        "$ " +
          (fTotalCliente -
            (parseFloat(response.Ss_Pago_30_Cliente) +
              parseFloat(response.Ss_Pago_100_Cliente) +
              parseFloat(response.Ss_Pago_Servicio_Cliente)))
      );

      //Date picker invoice
      $(".input-datepicker-today-to-more").datepicker({
        autoclose: true,
        startDate: new Date(fYear, fToday.getMonth(), fDay),
        todayHighlight: true,
        dateFormat: "dd/mm/yyyy",
        format: "dd/mm/yyyy",
      });
    },
    error: function (jqXHR, textStatus, errorThrown) {
      $(".modal-message").removeClass(
        "modal-danger modal-warning modal-success"
      );

      $("#modal-message").modal("show");
      $(".modal-message").addClass("modal-danger");
      $(".modal-title-message").text(
        textStatus + " [" + jqXHR.status + "]: " + errorThrown
      );
      setTimeout(function () {
        $("#modal-message").modal("hide");
      }, 1700);

      //Message for developer
      console.log(jqXHR.responseText);
    },
  });
}

function recepcionCarga(ID) {
  $(".div-Listar").hide();

  $("#form-pedido")[0].reset();
  $(".form-group").removeClass("has-error");
  $(".form-group").removeClass("has-success");
  $(".help-block").empty();

  $(".div-Compuesto").hide();
  $(".div-Producto_Recepcion_Carga").hide();
  $("#table-Producto_Enlace tbody").empty();
  $("#table-Producto_Recepcion_Carga tbody").empty();

  $(".div-Producto_Recepcion_Carga").show();
  $("#table-Producto_Recepcion_Carga tbody").empty();
  $("#table-Producto_Recepcion_Carga").show();

  $(".div-Invoice_Proveedor").hide();
  $("#table-Invoice_Proveedor tbody").empty();

  $(".div-Pago_Proveedor").hide();
  $("#table-Pago_Proveedor tbody").empty();

  //$('#span-id_pedido').html('Nro. ' + ID);

  url = base_url + "AgenteCompra/PedidosPagados/ajax_edit/" + ID;
  $.ajax({
    url: url,
    type: "GET",
    dataType: "JSON",
    success: function (response) {
      console.log(response);
      var detalle = response;
      response = response[0];

      $("#span-id_pedido").html(response.sCorrelativoCotizacion);

      $(".div-AgregarEditar").show();

      $('[name="EID_Pedido_Cabecera"]').val(response.ID_Pedido_Cabecera);
      $('[name="EID_Entidad"]').val(response.ID_Entidad);
      $('[name="EID_Empresa"]').val(response.ID_Empresa);
      $('[name="EID_Organizacion"]').val(response.ID_Organizacion);

      $('[name="No_Contacto"]').val(response.No_Contacto);
      $('[name="Txt_Email_Contacto"]').val(response.Txt_Email_Contacto);
      $('[name="Nu_Celular_Contacto"]').val(response.Nu_Celular_Contacto);
      $('[name="No_Entidad"]').val(response.No_Entidad);
      $('[name="Nu_Documento_Identidad"]').val(response.Nu_Documento_Identidad);

      //$( '#btn-excel_order_tracking' ).data('id_pedido', response.ID_Pedido_Cabecera);
      $("#btn-excel_order_tracking").attr(
        "data-id_pedido",
        response.ID_Pedido_Cabecera
      ); // sets

      $("#btn-descargar_pago_30").hide();
      $("#span-pago_30").html("");
      if (
        response.Txt_Url_Pago_30_Cliente != "" &&
        response.Txt_Url_Pago_30_Cliente != null
      ) {
        $("#btn-descargar_pago_30").show();
        $("#btn-descargar_pago_30").removeClass("d-none");

        $("#span-pago_30").html("$ " + response.Ss_Pago_30_Cliente);
      }

      $("#btn-descargar_pago_100").hide();
      $("#span-pago_100").html("");
      if (
        response.Txt_Url_Pago_100_Cliente != "" &&
        response.Txt_Url_Pago_100_Cliente != null
      ) {
        $("#btn-descargar_pago_100").show();
        $("#btn-descargar_pago_100").removeClass("d-none");

        $("#span-pago_100").html("$ " + response.Ss_Pago_100_Cliente);
      }

      $("#btn-descargar_pago_servicio").hide();
      $("#span-pago_servicio").html("");
      if (
        response.Txt_Url_Pago_Servicio_Cliente != "" &&
        response.Txt_Url_Pago_Servicio_Cliente != null
      ) {
        $("#btn-descargar_pago_servicio").show();
        $("#btn-descargar_pago_servicio").removeClass("d-none");

        $("#span-pago_servicio").html("$ " + response.Ss_Pago_Servicio_Cliente);
      }

      $("#btn-descargar_flete").hide();
      $("#span-flete").html("");
      if (
        response.Txt_Url_Pago_Otros_Flete != "" &&
        response.Txt_Url_Pago_Otros_Flete != null
      ) {
        $("#btn-descargar_flete").show();
        $("#btn-descargar_flete").removeClass("d-none");

        $("#span-flete").html("$ " + response.Ss_Pago_Otros_Flete);
      }

      $("#btn-descargar_costo_origen").hide();
      $("#span-costo_origen").html("");
      if (
        response.Txt_Url_Pago_Otros_Costo_Origen != "" &&
        response.Txt_Url_Pago_Otros_Costo_Origen != null
      ) {
        $("#btn-descargar_costo_origen").show();
        $("#btn-descargar_costo_origen").removeClass("d-none");

        $("#span-costo_origen").html(
          "$ " + response.Ss_Pago_Otros_Costo_Origen
        );
      }

      $("#btn-descargar_fta").hide();
      $("#span-fta").html("");
      if (
        response.Txt_Url_Pago_Otros_Costo_Fta != "" &&
        response.Txt_Url_Pago_Otros_Costo_Fta != null
      ) {
        $("#btn-descargar_fta").show();
        $("#btn-descargar_fta").removeClass("d-none");

        $("#span-fta").html("$ " + response.Ss_Pago_Otros_Costo_Fta);
      }

      $("#btn-descargar_pago_cuadrilla").hide();
      $("#span-cuadrilla").html("");
      if (
        response.Txt_Url_Pago_Otros_Cuadrilla != "" &&
        response.Txt_Url_Pago_Otros_Cuadrilla != null
      ) {
        $("#btn-descargar_pago_cuadrilla").show();
        $("#btn-descargar_pago_cuadrilla").removeClass("d-none");

        $("#span-cuadrilla").html("$ " + response.Ss_Pago_Otros_Cuadrilla);
      }

      $("#btn-descargar_otros_costos").hide();
      $("#span-otros_costo").html("");
      if (
        response.Txt_Url_Pago_Otros_Costos != "" &&
        response.Txt_Url_Pago_Otros_Costos != null
      ) {
        $("#btn-descargar_otros_costos").show();
        $("#btn-descargar_otros_costos").removeClass("d-none");

        $("#span-otros_costo").html("$ " + response.Ss_Pago_Otros_Costos);
      }

      var sNombreEstado =
        '<span class="badge badge-pill badge-secondary">Pendiente</span>';
      if (response.Nu_Estado_Pedido == 2)
        sNombreEstado =
          '<span class="badge badge-pill badge-primary">Confirmado</span>';
      else if (response.Nu_Estado_Pedido == 3)
        sNombreEstado =
          '<span class="badge badge-pill badge-success">Entregado</span>';
      else if (response.Nu_Estado_Pedido == 4)
        sNombreEstado =
          '<span class="badge badge-pill badge-danger">Confirmado</span>';
      $("#div-estado").html(sNombreEstado);

      var iCounterSupplier = 1,
        table_enlace_producto = "",
        iDiasVencimiento = 0,
        sClassColorTr = "",
        fTotalCliente = 0,
        ID_Entidad = "";
      for (i = 0; i < detalle.length; i++) {
        var cantidad_item_final_recepcion_carga = parseFloat(
          detalle[i]["Qt_Producto_Caja_Final_Verificada"]
        );
        var cantidad_item = parseFloat(detalle[i]["Qt_Producto"]);
        var precio_china = parseFloat(detalle[i]["Ss_Precio"]);

        fTotalCliente +=
          cantidad_item * (precio_china * parseFloat(response.Ss_Tipo_Cambio));

        var id_item = detalle[i]["ID_Pedido_Detalle_Producto_Proveedor"];
        var voucher_1 = detalle[i]["Txt_Url_Archivo_Pago_1_Proveedor"];
        var voucher_2 = detalle[i]["Txt_Url_Archivo_Pago_2_Proveedor"];
        //max-height: 350px;width: 100%; cursor:pointer

        var fTotal = cantidad_item * precio_china;
        var Ss_Pago_1_Proveedor = parseFloat(detalle[i]["Ss_Pago_1_Proveedor"]);
        var Ss_Pago_2_Proveedor = parseFloat(detalle[i]["Ss_Pago_2_Proveedor"]);

        sClassColorTr = "";
        iDiasVencimiento = 0;
        if (
          detalle[i]["Fe_Entrega_Proveedor"] != "" &&
          detalle[i]["Fe_Entrega_Proveedor"] != null
        ) {
          var fechaInicio = new Date(
            fYear + "-" + fMonth + "-" + fDay
          ).getTime();
          var fechaFin = new Date(detalle[i]["Fe_Entrega_Proveedor"]).getTime();

          var diff = fechaFin - fechaInicio;
          iDiasVencimiento = diff / (1000 * 60 * 60 * 24); // --> milisegundos -> segundos -> minutos -> horas -> días
          if (iDiasVencimiento < 5) sClassColorTr = "table-warning";
        }

        var fecha_entrega_proveedor =
          detalle[i]["Fe_Entrega_Proveedor"] != "" &&
          detalle[i]["Fe_Entrega_Proveedor"] != null
            ? ParseDateString(
                detalle[i]["Fe_Entrega_Proveedor"],
                "fecha_bd",
                "-"
              )
            : "";

        var nota_final =
          detalle[i]["Txt_Nota_Recepcion_Carga_Proveedor"] != "" &&
          detalle[i]["Txt_Nota_Recepcion_Carga_Proveedor"] != null
            ? detalle[i]["Txt_Nota_Recepcion_Carga_Proveedor"]
            : "";

        if (ID_Entidad != detalle[i].ID_Entidad_Proveedor) {
          table_enlace_producto +=
            "<tr class='table-active'>" +
            "<th class='text-right'>" +
            iCounterSupplier +
            ". Supplier</th>";
          table_enlace_producto +=
            "<th class='text-left'>" +
            detalle[i].No_Contacto_Proveedor +
            "</th>";
          table_enlace_producto += "<th class='text-left' colspan='2'>";
          table_enlace_producto += "Observaciones<br>";
          table_enlace_producto +=
            '<textarea id="textarea-nota' +
            id_item +
            '" name="addProducto[' +
            id_item +
            '][nota]" class="form-control required nota" placeholder="Observaciones" rows="1" style="height: 50px;">' +
            clearHTMLTextArea(nota_final) +
            "</textarea>";
          table_enlace_producto += "</th>";
          table_enlace_producto += "<th class='text-center'>";
          table_enlace_producto +=
            '<button type="button" id="btn-finalizar_item_proveedor' +
            id_item +
            '" data-name_item="' +
            detalle[i]["Txt_Producto"] +
            '" data-id_pedido_cabecera="' +
            response.ID_Pedido_Cabecera +
            '" data-id="' +
            id_item +
            '" data-correlativo="' +
            response.sCorrelativoCotizacion +
            '" class="text-left btn btn-primary btn-finalizar_item_proveedor"> Finalizar </button>';
          table_enlace_producto += "</th>";
          +"</tr>";
          ID_Entidad = detalle[i].ID_Entidad_Proveedor;
          ++iCounterSupplier;
        }

        table_enlace_producto +=
          "<tr id='tr_enlace_producto" +
          id_item +
          "'>" +
          "<td style='display:none;' class='text-left td-id_item'>" +
          id_item +
          "</td>" +
          "<td class='text-center td-name' width='50px'>" +
          "<img style='' data-id_item='" +
          id_item +
          "' data-url_img='" +
          detalle[i]["Txt_Url_Imagen_Producto"] +
          "' src='" +
          detalle[i]["Txt_Url_Imagen_Producto"] +
          "' alt='" +
          detalle[i]["Txt_Producto"] +
          "' class='img-thumbnail img-table_item img-fluid img-resize mb-2'>";

        cantidad_item =
          !isNaN(cantidad_item_final_recepcion_carga) &&
          cantidad_item_final_recepcion_carga > 0 &&
          cantidad_item_final_recepcion_carga != ""
            ? cantidad_item_final_recepcion_carga
            : cantidad_item;

        table_enlace_producto +=
          "</td>" +
          "<td class='text-left td-name'>" +
          detalle[i]["Txt_Producto"] +
          "</td>" +
          "<td class='text-right td-qty'  width='150px'>";
        table_enlace_producto +=
          '<input type="text" inputmode="decimal" class="form-control input-decimal" id="input-cantidad' +
          id_item +
          '" name="addProducto[' +
          id_item +
          '][cantidad]" value="' +
          Math.round10(cantidad_item, -2) +
          '">';
        table_enlace_producto += "</td>";

        table_enlace_producto += "<td>";
        table_enlace_producto += "1";
        table_enlace_producto += "</td>";

        table_enlace_producto += "<td class='text-center'>";
        if (detalle[i]["Nu_Estado_Recepcion_Carga_Proveedor_Item"] == 0) {
          //pendiente
          table_enlace_producto +=
            '<button type="button" id="btn-confirmado_item_proveedor' +
            id_item +
            '" data-estado="1" data-name_item="' +
            detalle[i]["Txt_Producto"] +
            '" data-id_pedido_cabecera="' +
            response.ID_Pedido_Cabecera +
            '" data-id="' +
            id_item +
            '" data-correlativo="' +
            response.sCorrelativoCotizacion +
            '" class="text-left btn btn-success btn-estado_item_proveedor btn-cargando_item_proveedor' +
            id_item +
            '"> Confirmado </button>';
          table_enlace_producto +=
            ' <button type="button" id="btn-faltante_item_proveedor' +
            id_item +
            '" data-estado="2" data-name_item="' +
            detalle[i]["Txt_Producto"] +
            '" data-id_pedido_cabecera="' +
            response.ID_Pedido_Cabecera +
            '" data-id="' +
            id_item +
            '" data-correlativo="' +
            response.sCorrelativoCotizacion +
            '" class="text-left btn btn-warning btn-estado_item_proveedor btn-cargando_item_proveedor' +
            id_item +
            '"> Faltante </button>';
        } else {
          if (detalle[i]["Nu_Estado_Recepcion_Carga_Proveedor_Item"] == 1) {
            table_enlace_producto +=
              '<span class="badge bg-success">Confirmado</span>';
          } else if (
            detalle[i]["Nu_Estado_Recepcion_Carga_Proveedor_Item"] == 2
          ) {
            table_enlace_producto +=
              '<span class="badge bg-warning">Faltante</span>';
          }
        }
        table_enlace_producto += "</td>";

        table_enlace_producto +=
          '<input type="hidden" name="addProducto[' +
          id_item +
          '][id_item]" value="' +
          id_item +
          '">';
        table_enlace_producto += "</tr>";

        table_enlace_producto += "</tr>";
      }

      $("#span-total_cantidad_items").html(i);
      $("#table-Producto_Recepcion_Carga").append(table_enlace_producto);

      $("#span-total_cliente").html("$ " + fTotalCliente.toFixed(2));

      //PAGOS
      //Ss_Pago_30_Cliente
      //Ss_Pago_100_Cliente
      //Ss_Pago_Servicio_Cliente

      //OTROS
      //Ss_Pago_Otros_Flete
      //Ss_Pago_Otros_Costo_Origen
      //Ss_Pago_Otros_Costo_Fta
      //Ss_Pago_Otros_Cuadrilla
      //Ss_Pago_Otros_Costos
      $("#span-saldo_cliente").html(
        "$ " +
          (fTotalCliente -
            (parseFloat(response.Ss_Pago_30_Cliente) +
              parseFloat(response.Ss_Pago_100_Cliente) +
              parseFloat(response.Ss_Pago_Servicio_Cliente)))
      );

      //Date picker invoice
      $(".input-datepicker-today-to-more").datepicker({
        autoclose: true,
        startDate: new Date(fYear, fToday.getMonth(), fDay),
        todayHighlight: true,
        dateFormat: "dd/mm/yyyy",
        format: "dd/mm/yyyy",
      });
    },
    error: function (jqXHR, textStatus, errorThrown) {
      $(".modal-message").removeClass(
        "modal-danger modal-warning modal-success"
      );

      $("#modal-message").modal("show");
      $(".modal-message").addClass("modal-danger");
      $(".modal-title-message").text(
        textStatus + " [" + jqXHR.status + "]: " + errorThrown
      );
      setTimeout(function () {
        $("#modal-message").modal("hide");
      }, 1700);

      //Message for developer
      console.log(jqXHR.responseText);
    },
  });
}

function coordinarPagosProveedor(ID) {
  $(".div-Listar").hide();

  $("#form-pedido")[0].reset();
  $(".form-group").removeClass("has-error");
  $(".form-group").removeClass("has-success");
  $(".help-block").empty();

  $(".div-Compuesto").show();
  $("#table-Producto_Enlace tbody").empty();
  $("#table-Producto_Enlace").show();

  $(".div-Producto_Recepcion_Carga").hide();
  $("#table-Producto_Recepcion_Carga tbody").empty();

  $(".div-Invoice_Proveedor").hide();
  $("#table-Invoice_Proveedor tbody").empty();

  $(".div-Pago_Proveedor").hide();
  $("#table-Pago_Proveedor tbody").empty();

  //$('#span-id_pedido').html('Nro. ' + ID);

  url = base_url + "AgenteCompra/PedidosPagados/ajax_edit/" + ID;
  $.ajax({
    url: url,
    type: "GET",
    dataType: "JSON",
    success: function (response) {
      console.log(response);
      var detalle = response;
      response = response[0];

      $("#span-id_pedido").html(response.sCorrelativoCotizacion);

      $(".div-AgregarEditar").show();

      $('[name="EID_Pedido_Cabecera"]').val(response.ID_Pedido_Cabecera);
      $('[name="EID_Entidad"]').val(response.ID_Entidad);
      $('[name="EID_Empresa"]').val(response.ID_Empresa);
      $('[name="EID_Organizacion"]').val(response.ID_Organizacion);

      $('[name="No_Contacto"]').val(response.No_Contacto);
      $('[name="Txt_Email_Contacto"]').val(response.Txt_Email_Contacto);
      $('[name="Nu_Celular_Contacto"]').val(response.Nu_Celular_Contacto);
      $('[name="No_Entidad"]').val(response.No_Entidad);
      $('[name="Nu_Documento_Identidad"]').val(response.Nu_Documento_Identidad);

      //$( '#btn-excel_order_tracking' ).data('id_pedido', response.ID_Pedido_Cabecera);
      $("#btn-excel_order_tracking").attr(
        "data-id_pedido",
        response.ID_Pedido_Cabecera
      ); // sets

      $("#btn-descargar_pago_30").hide();
      $("#span-pago_30").html("");
      if (
        response.Txt_Url_Pago_30_Cliente != "" &&
        response.Txt_Url_Pago_30_Cliente != null
      ) {
        $("#btn-descargar_pago_30").show();
        $("#btn-descargar_pago_30").removeClass("d-none");

        $("#span-pago_30").html("$ " + response.Ss_Pago_30_Cliente);
      }

      $("#btn-descargar_pago_100").hide();
      $("#span-pago_100").html("");
      if (
        response.Txt_Url_Pago_100_Cliente != "" &&
        response.Txt_Url_Pago_100_Cliente != null
      ) {
        $("#btn-descargar_pago_100").show();
        $("#btn-descargar_pago_100").removeClass("d-none");

        $("#span-pago_100").html("$ " + response.Ss_Pago_100_Cliente);
      }

      $("#btn-descargar_pago_servicio").hide();
      $("#span-pago_servicio").html("");
      if (
        response.Txt_Url_Pago_Servicio_Cliente != "" &&
        response.Txt_Url_Pago_Servicio_Cliente != null
      ) {
        $("#btn-descargar_pago_servicio").show();
        $("#btn-descargar_pago_servicio").removeClass("d-none");

        $("#span-pago_servicio").html("$ " + response.Ss_Pago_Servicio_Cliente);
      }

      $("#btn-descargar_flete").hide();
      $("#span-flete").html("");
      if (
        response.Txt_Url_Pago_Otros_Flete != "" &&
        response.Txt_Url_Pago_Otros_Flete != null
      ) {
        $("#btn-descargar_flete").show();
        $("#btn-descargar_flete").removeClass("d-none");

        $("#span-flete").html("$ " + response.Ss_Pago_Otros_Flete);
      }

      $("#btn-descargar_costo_origen").hide();
      $("#span-costo_origen").html("");
      if (
        response.Txt_Url_Pago_Otros_Costo_Origen != "" &&
        response.Txt_Url_Pago_Otros_Costo_Origen != null
      ) {
        $("#btn-descargar_costo_origen").show();
        $("#btn-descargar_costo_origen").removeClass("d-none");

        $("#span-costo_origen").html(
          "$ " + response.Ss_Pago_Otros_Costo_Origen
        );
      }

      $("#btn-descargar_fta").hide();
      $("#span-fta").html("");
      if (
        response.Txt_Url_Pago_Otros_Costo_Fta != "" &&
        response.Txt_Url_Pago_Otros_Costo_Fta != null
      ) {
        $("#btn-descargar_fta").show();
        $("#btn-descargar_fta").removeClass("d-none");

        $("#span-fta").html("$ " + response.Ss_Pago_Otros_Costo_Fta);
      }

      $("#btn-descargar_pago_cuadrilla").hide();
      $("#span-cuadrilla").html("");
      if (
        response.Txt_Url_Pago_Otros_Cuadrilla != "" &&
        response.Txt_Url_Pago_Otros_Cuadrilla != null
      ) {
        $("#btn-descargar_pago_cuadrilla").show();
        $("#btn-descargar_pago_cuadrilla").removeClass("d-none");

        $("#span-cuadrilla").html("$ " + response.Ss_Pago_Otros_Cuadrilla);
      }

      $("#btn-descargar_otros_costos").hide();
      $("#span-otros_costo").html("");
      if (
        response.Txt_Url_Pago_Otros_Costos != "" &&
        response.Txt_Url_Pago_Otros_Costos != null
      ) {
        $("#btn-descargar_otros_costos").show();
        $("#btn-descargar_otros_costos").removeClass("d-none");

        $("#span-otros_costo").html("$ " + response.Ss_Pago_Otros_Costos);
      }

      var sNombreEstado =
        '<span class="badge badge-pill badge-secondary">Pendiente</span>';
      if (response.Nu_Estado_Pedido == 2)
        sNombreEstado =
          '<span class="badge badge-pill badge-primary">Confirmado</span>';
      else if (response.Nu_Estado_Pedido == 3)
        sNombreEstado =
          '<span class="badge badge-pill badge-success">Entregado</span>';
      else if (response.Nu_Estado_Pedido == 4)
        sNombreEstado =
          '<span class="badge badge-pill badge-danger">Confirmado</span>';
      $("#div-estado").html(sNombreEstado);

      var iCounterSupplier = 1,
        table_enlace_producto = "",
        iDiasVencimiento = 0,
        sClassColorTr = "",
        fTotalCliente = 0,
        ID_Entidad = "";
      $("#btn-save_proveedor").show();
      for (i = 0; i < detalle.length; i++) {
        var cantidad_item = parseFloat(detalle[i]["Qt_Producto"]);
        var precio_china = parseFloat(detalle[i]["Ss_Precio"]);

        fTotalCliente +=
          cantidad_item * (precio_china * parseFloat(response.Ss_Tipo_Cambio));

        var id_item = detalle[i]["ID_Pedido_Detalle_Producto_Proveedor"];
        var voucher_1 = detalle[i]["Txt_Url_Archivo_Pago_1_Proveedor"];
        var voucher_2 = detalle[i]["Txt_Url_Archivo_Pago_2_Proveedor"];
        //max-height: 350px;width: 100%; cursor:pointer

        var fTotal = cantidad_item * precio_china;
        var Ss_Pago_1_Proveedor = parseFloat(detalle[i]["Ss_Pago_1_Proveedor"]);
        var Ss_Pago_2_Proveedor = parseFloat(detalle[i]["Ss_Pago_2_Proveedor"]);

        sClassColorTr = "";
        iDiasVencimiento = 0;
        if (
          detalle[i]["Fe_Entrega_Proveedor"] != "" &&
          detalle[i]["Fe_Entrega_Proveedor"] != null
        ) {
          var fechaInicio = new Date(
            fYear + "-" + fMonth + "-" + fDay
          ).getTime();
          var fechaFin = new Date(detalle[i]["Fe_Entrega_Proveedor"]).getTime();

          var diff = fechaFin - fechaInicio;
          iDiasVencimiento = diff / (1000 * 60 * 60 * 24); // --> milisegundos -> segundos -> minutos -> horas -> días
          if (iDiasVencimiento < 5) sClassColorTr = "table-warning";
        }

        var fecha_entrega_proveedor =
          detalle[i]["Fe_Entrega_Proveedor"] != "" &&
          detalle[i]["Fe_Entrega_Proveedor"] != null
            ? ParseDateString(
                detalle[i]["Fe_Entrega_Proveedor"],
                "fecha_bd",
                "-"
              )
            : "";

        if (ID_Entidad != detalle[i].ID_Entidad_Proveedor) {
          table_enlace_producto +=
            "<tr class='table-active'>" +
            "<th class='text-right'>" +
            iCounterSupplier +
            ". Supplier</th>";
          table_enlace_producto += "<th class='text-left'>";
          table_enlace_producto +=
            detalle[i].No_Contacto_Proveedor + "&nbsp;&nbsp;&nbsp;";
          if (
            detalle[i]["Txt_Url_Imagen_Proveedor"] != "" &&
            detalle[i]["Txt_Url_Imagen_Proveedor"] != null
          ) {
            table_enlace_producto +=
              "<img style='' data-id_item='" +
              id_item +
              "' data-url_img='" +
              detalle[i]["Txt_Url_Imagen_Proveedor"] +
              "' src='" +
              detalle[i]["Txt_Url_Imagen_Proveedor"] +
              "' alt='" +
              detalle[i]["Txt_Producto"] +
              "' class='img-thumbnail img-table_item img-fluid img-resize_supplier mb-2'>";
          }
          table_enlace_producto += "</th>";
          table_enlace_producto += "<th class='text-left' colspan='3'>";
          table_enlace_producto +=
            '<button type="button" class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="editarProveedor(' +
            detalle[i].ID_Entidad_Proveedor +
            ", " +
            id_item +
            ')">agregar datos &nbsp;<i class="far fa-edit" aria-hidden="true"></i></button>';
          table_enlace_producto += "</th>";
          table_enlace_producto += "<th class='text-left' colspan='7'>";
          table_enlace_producto +=
            "Costo delivery: " + detalle[i]["Ss_Costo_Delivery"];
          table_enlace_producto += "</th>";
          +"</tr>";
          ID_Entidad = detalle[i].ID_Entidad_Proveedor;
          ++iCounterSupplier;
        }

        table_enlace_producto +=
          "<tr id='tr_enlace_producto" +
          id_item +
          "'>" +
          "<td style='display:none;' class='text-left td-id_item'>" +
          id_item +
          "</td>" +
          "<td class='text-center td-name' width='10%'>" +
          "<img style='' data-id_item='" +
          id_item +
          "' data-url_img='" +
          detalle[i]["Txt_Url_Imagen_Producto"] +
          "' src='" +
          detalle[i]["Txt_Url_Imagen_Producto"] +
          "' alt='" +
          detalle[i]["Txt_Producto"] +
          "' class='img-thumbnail img-table_item img-fluid img-resize_v2 mb-2'>";

        table_enlace_producto +=
          "</td>" +
          "<td class='text-left td-name'>" +
          detalle[i]["Txt_Producto"] +
          "</td>" +
          "<td class='text-right td-qty'>" +
          Math.round10(cantidad_item, -2) +
          "</td>" +
          "<td class='text-right td-price'>" +
          Math.round10(precio_china, -2) +
          "</td>" +
          "<td class='text-right td-amount'>" +
          Math.round10(fTotal, -2) +
          "</td>" +
          //+"<td class='text-right td-pay1'>" + Math.round10(Ss_Pago_1_Proveedor, -2) + "</td>"
          //+"<td class='text-right td-pay1'>" + Math.round10(detalle[i]['Ss_Pago_Importe_1'], -2) + "</td>"
          //+"<td class='text-right td-pay1'>" + Math.round10(detalle[i]['Ss_Pago_Importe_2'], -2) + "</td>"
          //+"<td class='text-right td-balance'>" + Math.round10(fTotal - Ss_Pago_1_Proveedor, -2) + "</td>"
          //+"<td class='text-right td-pay2'>" + Math.round10(Ss_Pago_2_Proveedor, -2) + "</td>"
          "<td class='text-left td-delivery_date'>" +
          detalle[i]["Nu_Dias_Delivery"] +
          "</td>";
        //+"<td class='text-left td-costo_delivery'>" + detalle[i]['Ss_Costo_Delivery'] + "</td>";

        table_enlace_producto += "<td class='text-left td-supplier'>";
        table_enlace_producto +=
          '<div class="input-group date" style="width:100%">';
        table_enlace_producto +=
          '<input type="text" id="txt-fecha_entrega_proveedor' +
          i +
          '" name="addProducto[' +
          id_item +
          '][fecha_entrega_proveedor]" class="form-control input-datepicker-today-to-more required" value="' +
          fecha_entrega_proveedor +
          '">';
        table_enlace_producto += "</div>";
        table_enlace_producto += "</td>";

        table_enlace_producto += "<td class='text-left'>";
        table_enlace_producto +=
          '<button type="button" id="btn-cambiar_item_proveedor' +
          id_item +
          '" data-name_item="' +
          detalle[i]["Txt_Producto"] +
          '" data-id_pedido_cabecera="' +
          response.ID_Pedido_Cabecera +
          '" data-id="' +
          id_item +
          '" data-correlativo="' +
          response.sCorrelativoCotizacion +
          '" class="text-left btn btn-primary btn-block btn-cambiar_item_proveedor"> change </button>';
        table_enlace_producto += "</td>";

        //table_enlace_producto += "<td class='text-left td-supplier'>" + detalle[i]['No_Contacto_Proveedor'] + "</td>"
        /*
          table_enlace_producto += "<td class='text-left td-phone'>";
          if(detalle[i]['Txt_Url_Imagen_Proveedor'] != '' && detalle[i]['Txt_Url_Imagen_Proveedor'] != null){
            table_enlace_producto += "<img style='' data-id_item='" + id_item + "' data-url_img='" + detalle[i]['Txt_Url_Imagen_Proveedor'] + "' src='" + detalle[i]['Txt_Url_Imagen_Proveedor'] + "' alt='" + detalle[i]['Txt_Producto'] + "' class='img-thumbnail img-table_item img-fluid img-resize_v2 mb-2'>";
          }
          table_enlace_producto += "</td>";
          */

        table_enlace_producto += "<td class='text-left td-eliminar'>";
        table_enlace_producto +=
          '<button type="button" id="btn-eliminar_item_proveedor' +
          id_item +
          '" data-name_item="' +
          detalle[i]["Txt_Producto"] +
          '" data-id_pedido_cabecera="' +
          response.ID_Pedido_Cabecera +
          '" data-id="' +
          id_item +
          '" data-correlativo="' +
          response.sCorrelativoCotizacion +
          '" class="text-left btn btn-danger btn-block btn-eliminar_item_proveedor"> X </button>';
        table_enlace_producto += "</td>";

        table_enlace_producto +=
          '<input type="hidden" name="addProducto[' +
          id_item +
          '][id_item]" value="' +
          id_item +
          '">';
        table_enlace_producto += "</tr>";

        /*
        table_enlace_producto += "<tr>";
          table_enlace_producto += "<td class='text-left' colspan='12'>";
            table_enlace_producto += '<button type="button" id="btn-cambiar_item_proveedor' + id_item + '" data-name_item="' + detalle[i]['Txt_Producto'] + '" data-id_pedido_cabecera="' + response.ID_Pedido_Cabecera + '" data-id="' + id_item + '" data-correlativo="' + response.sCorrelativoCotizacion + '" class="text-left btn btn-danger btn-block btn-cambiar_item_proveedor"> Cambiar proveedor </button>';
          table_enlace_producto += "</td>";
        table_enlace_producto += "</tr>";
        */
      }

      $("#span-total_cantidad_items").html(i);
      $("#table-Producto_Enlace").append(table_enlace_producto);

      $("#span-total_cliente").html("$ " + fTotalCliente.toFixed(2));

      //PAGOS
      //Ss_Pago_30_Cliente
      //Ss_Pago_100_Cliente
      //Ss_Pago_Servicio_Cliente

      //OTROS
      //Ss_Pago_Otros_Flete
      //Ss_Pago_Otros_Costo_Origen
      //Ss_Pago_Otros_Costo_Fta
      //Ss_Pago_Otros_Cuadrilla
      //Ss_Pago_Otros_Costos
      $("#span-saldo_cliente").html(
        "$ " +
          (fTotalCliente -
            (parseFloat(response.Ss_Pago_30_Cliente) +
              parseFloat(response.Ss_Pago_100_Cliente) +
              parseFloat(response.Ss_Pago_Servicio_Cliente)))
      );

      //Date picker invoice
      $(".input-datepicker-today-to-more").datepicker({
        autoclose: true,
        startDate: new Date(fYear, fToday.getMonth(), fDay),
        todayHighlight: true,
        dateFormat: "dd/mm/yyyy",
        format: "dd/mm/yyyy",
      });
    },
    error: function (jqXHR, textStatus, errorThrown) {
      $(".modal-message").removeClass(
        "modal-danger modal-warning modal-success"
      );

      $("#modal-message").modal("show");
      $(".modal-message").addClass("modal-danger");
      $(".modal-title-message").text(
        textStatus + " [" + jqXHR.status + "]: " + errorThrown
      );
      setTimeout(function () {
        $("#modal-message").modal("hide");
      }, 1700);

      //Message for developer
      console.log(jqXHR.responseText);
    },
  });
}

function verPedido(ID) {
  $(".div-Listar").hide();

  $("#form-pedido")[0].reset();
  $(".form-group").removeClass("has-error");
  $(".form-group").removeClass("has-success");
  $(".help-block").empty();

  $(".div-Compuesto").show();
  $("#table-Producto_Enlace tbody").empty();
  $("#table-Producto_Enlace").show();

  $(".div-Producto_Recepcion_Carga").hide();
  $("#table-Producto_Recepcion_Carga tbody").empty();

  $(".div-Invoice_Proveedor").hide();
  $("#table-Invoice_Proveedor tbody").empty();

  $(".div-Pago_Proveedor").hide();
  $("#table-Pago_Proveedor tbody").empty();

  //$('#span-id_pedido').html('Nro. ' + ID);

  url = base_url + "AgenteCompra/PedidosPagados/ajax_edit/" + ID;
  $.ajax({
    url: url,
    type: "GET",
    dataType: "JSON",
    success: function (response) {
      console.log(response);
      var detalle = response;
      response = response[0];

      $("#span-id_pedido").html(response.sCorrelativoCotizacion);

      $(".div-AgregarEditar").show();

      $('[name="EID_Pedido_Cabecera"]').val(response.ID_Pedido_Cabecera);
      $('[name="EID_Entidad"]').val(response.ID_Entidad);
      $('[name="EID_Empresa"]').val(response.ID_Empresa);
      $('[name="EID_Organizacion"]').val(response.ID_Organizacion);

      $('[name="No_Contacto"]').val(response.No_Contacto);
      $('[name="Txt_Email_Contacto"]').val(response.Txt_Email_Contacto);
      $('[name="Nu_Celular_Contacto"]').val(response.Nu_Celular_Contacto);
      $('[name="No_Entidad"]').val(response.No_Entidad);
      $('[name="Nu_Documento_Identidad"]').val(response.Nu_Documento_Identidad);

      //$( '#btn-excel_order_tracking' ).data('id_pedido', response.ID_Pedido_Cabecera);
      $("#btn-excel_order_tracking").attr(
        "data-id_pedido",
        response.ID_Pedido_Cabecera
      ); // sets

      $("#btn-descargar_pago_30").hide();
      $("#span-pago_30").html("");
      if (
        response.Txt_Url_Pago_30_Cliente != "" &&
        response.Txt_Url_Pago_30_Cliente != null
      ) {
        $("#btn-descargar_pago_30").show();
        $("#btn-descargar_pago_30").removeClass("d-none");

        $("#span-pago_30").html("$ " + response.Ss_Pago_30_Cliente);
      }

      $("#btn-descargar_pago_100").hide();
      $("#span-pago_100").html("");
      if (
        response.Txt_Url_Pago_100_Cliente != "" &&
        response.Txt_Url_Pago_100_Cliente != null
      ) {
        $("#btn-descargar_pago_100").show();
        $("#btn-descargar_pago_100").removeClass("d-none");

        $("#span-pago_100").html("$ " + response.Ss_Pago_100_Cliente);
      }

      $("#btn-descargar_pago_servicio").hide();
      $("#span-pago_servicio").html("");
      if (
        response.Txt_Url_Pago_Servicio_Cliente != "" &&
        response.Txt_Url_Pago_Servicio_Cliente != null
      ) {
        $("#btn-descargar_pago_servicio").show();
        $("#btn-descargar_pago_servicio").removeClass("d-none");

        $("#span-pago_servicio").html("$ " + response.Ss_Pago_Servicio_Cliente);
      }

      $("#btn-descargar_flete").hide();
      $("#span-flete").html("");
      if (
        response.Txt_Url_Pago_Otros_Flete != "" &&
        response.Txt_Url_Pago_Otros_Flete != null
      ) {
        $("#btn-descargar_flete").show();
        $("#btn-descargar_flete").removeClass("d-none");

        $("#span-flete").html("$ " + response.Ss_Pago_Otros_Flete);
      }

      $("#btn-descargar_costo_origen").hide();
      $("#span-costo_origen").html("");
      if (
        response.Txt_Url_Pago_Otros_Costo_Origen != "" &&
        response.Txt_Url_Pago_Otros_Costo_Origen != null
      ) {
        $("#btn-descargar_costo_origen").show();
        $("#btn-descargar_costo_origen").removeClass("d-none");

        $("#span-costo_origen").html(
          "$ " + response.Ss_Pago_Otros_Costo_Origen
        );
      }

      $("#btn-descargar_fta").hide();
      $("#span-fta").html("");
      if (
        response.Txt_Url_Pago_Otros_Costo_Fta != "" &&
        response.Txt_Url_Pago_Otros_Costo_Fta != null
      ) {
        $("#btn-descargar_fta").show();
        $("#btn-descargar_fta").removeClass("d-none");

        $("#span-fta").html("$ " + response.Ss_Pago_Otros_Costo_Fta);
      }

      $("#btn-descargar_pago_cuadrilla").hide();
      $("#span-cuadrilla").html("");
      if (
        response.Txt_Url_Pago_Otros_Cuadrilla != "" &&
        response.Txt_Url_Pago_Otros_Cuadrilla != null
      ) {
        $("#btn-descargar_pago_cuadrilla").show();
        $("#btn-descargar_pago_cuadrilla").removeClass("d-none");

        $("#span-cuadrilla").html("$ " + response.Ss_Pago_Otros_Cuadrilla);
      }

      $("#btn-descargar_otros_costos").hide();
      $("#span-otros_costo").html("");
      if (
        response.Txt_Url_Pago_Otros_Costos != "" &&
        response.Txt_Url_Pago_Otros_Costos != null
      ) {
        $("#btn-descargar_otros_costos").show();
        $("#btn-descargar_otros_costos").removeClass("d-none");

        $("#span-otros_costo").html("$ " + response.Ss_Pago_Otros_Costos);
      }

      var sNombreEstado =
        '<span class="badge badge-pill badge-secondary">Pendiente</span>';
      if (response.Nu_Estado_Pedido == 2)
        sNombreEstado =
          '<span class="badge badge-pill badge-primary">Confirmado</span>';
      else if (response.Nu_Estado_Pedido == 3)
        sNombreEstado =
          '<span class="badge badge-pill badge-success">Entregado</span>';
      else if (response.Nu_Estado_Pedido == 4)
        sNombreEstado =
          '<span class="badge badge-pill badge-danger">Confirmado</span>';
      $("#div-estado").html(sNombreEstado);

      var table_enlace_producto = "",
        iDiasVencimiento = 0,
        sClassColorTr = "",
        fTotalCliente = 0,
        ID_Entidad = 0;
      for (i = 0; i < detalle.length; i++) {
        var cantidad_item = parseFloat(detalle[i]["Qt_Producto"]);
        var precio_china = parseFloat(detalle[i]["Ss_Precio"]);

        fTotalCliente +=
          cantidad_item * (precio_china * parseFloat(response.Ss_Tipo_Cambio));

        var id_item = detalle[i]["ID_Pedido_Detalle_Producto_Proveedor"];
        var voucher_1 = detalle[i]["Txt_Url_Archivo_Pago_1_Proveedor"];
        var voucher_2 = detalle[i]["Txt_Url_Archivo_Pago_2_Proveedor"];
        //max-height: 350px;width: 100%; cursor:pointer

        var fTotal = cantidad_item * precio_china;
        var Ss_Pago_1_Proveedor = parseFloat(detalle[i]["Ss_Pago_1_Proveedor"]);
        var Ss_Pago_2_Proveedor = parseFloat(detalle[i]["Ss_Pago_2_Proveedor"]);

        sClassColorTr = "";
        iDiasVencimiento = 0;
        if (
          detalle[i]["Fe_Entrega_Proveedor"] != "" &&
          detalle[i]["Fe_Entrega_Proveedor"] != null
        ) {
          var fechaInicio = new Date(
            fYear + "-" + fMonth + "-" + fDay
          ).getTime();
          var fechaFin = new Date(detalle[i]["Fe_Entrega_Proveedor"]).getTime();

          var diff = fechaFin - fechaInicio;
          iDiasVencimiento = diff / (1000 * 60 * 60 * 24); // --> milisegundos -> segundos -> minutos -> horas -> días
          if (iDiasVencimiento < 5) sClassColorTr = "table-warning";
        }

        var fecha_entrega_proveedor =
          detalle[i]["Fe_Entrega_Proveedor"] != "" &&
          detalle[i]["Fe_Entrega_Proveedor"] != null
            ? ParseDateString(
                detalle[i]["Fe_Entrega_Proveedor"],
                "fecha_bd",
                "-"
              )
            : "";

        if (ID_Entidad != detalle[i].ID_Entidad_Proveedor) {
          table_enlace_producto +=
            "<tr>" +
            "<th class='text-right'>Supplier </th>" +
            "<th class='text-left' colspan='14'>" +
            detalle[i].No_Contacto_Proveedor +
            "</th>" +
            "</tr>";
          ID_Entidad = detalle[i].ID_Entidad_Proveedor;
        }

        table_enlace_producto +=
          "<tr id='tr_enlace_producto" +
          id_item +
          "'>" +
          "<td style='display:none;' class='text-left td-id_item'>" +
          id_item +
          "</td>" +
          "<td class='text-center td-name' width='50%'>" +
          "<img style='' data-id_item='" +
          id_item +
          "' data-url_img='" +
          detalle[i]["Txt_Url_Imagen_Producto"] +
          "' src='" +
          detalle[i]["Txt_Url_Imagen_Producto"] +
          "' alt='" +
          detalle[i]["Txt_Producto"] +
          "' class='img-thumbnail img-table_item img-fluid img-resize mb-2'>";

        table_enlace_producto +=
          "</td>" +
          "<td class='text-left td-name'>" +
          detalle[i]["Txt_Producto"] +
          "</td>" +
          "<td class='text-right td-qty'>" +
          Math.round10(cantidad_item, -2) +
          "</td>" +
          "<td class='text-right td-price'>" +
          Math.round10(precio_china, -2) +
          "</td>" +
          "<td class='text-right td-amount'>" +
          Math.round10(fTotal, -2) +
          "</td>" +
          "<td class='text-right td-pay1'>" +
          Math.round10(Ss_Pago_1_Proveedor, -2) +
          "</td>" +
          "<td class='text-right td-balance'>" +
          Math.round10(fTotal - Ss_Pago_1_Proveedor, -2) +
          "</td>" +
          "<td class='text-right td-pay2'>" +
          Math.round10(Ss_Pago_2_Proveedor, -2) +
          "</td>" +
          "<td class='text-left td-delivery_date'>" +
          detalle[i]["Nu_Dias_Delivery"] +
          "</td>" +
          "<td class='text-left td-costo_delivery'>" +
          detalle[i]["Ss_Costo_Delivery"] +
          "</td>";

        table_enlace_producto += "<td class='text-left td-supplier'>";
        table_enlace_producto +=
          '<div class="input-group date" style="width:100%">';
        table_enlace_producto +=
          '<input type="text" id="txt-fecha_entrega_proveedor' +
          i +
          '" name="addProducto[' +
          id_item +
          '][fecha_entrega_proveedor]" class="form-control input-datepicker-today-to-more required" value="' +
          fecha_entrega_proveedor +
          '">';
        table_enlace_producto += "</div>";
        table_enlace_producto += "</td>";

        table_enlace_producto +=
          "<td class='text-left td-supplier'>" +
          detalle[i]["No_Contacto_Proveedor"] +
          "</td>" +
          "<td class='text-left td-phone'>";
        if (
          detalle[i]["Txt_Url_Imagen_Proveedor"] != "" &&
          detalle[i]["Txt_Url_Imagen_Proveedor"] != null
        ) {
          table_enlace_producto +=
            "<img style='' data-id_item='" +
            id_item +
            "' data-url_img='" +
            detalle[i]["Txt_Url_Imagen_Proveedor"] +
            "' src='" +
            detalle[i]["Txt_Url_Imagen_Proveedor"] +
            "' alt='" +
            detalle[i]["Txt_Producto"] +
            "' class='img-thumbnail img-table_item img-fluid img-resize mb-2'>";
        }
        table_enlace_producto += "</td>";

        table_enlace_producto += "<td class='text-left td-eliminar'>";
        table_enlace_producto +=
          '<button type="button" id="btn-eliminar_item_proveedor' +
          id_item +
          '" data-name_item="' +
          detalle[i]["Txt_Producto"] +
          '" data-id_pedido_cabecera="' +
          response.ID_Pedido_Cabecera +
          '" data-id="' +
          id_item +
          '" data-correlativo="' +
          response.sCorrelativoCotizacion +
          '" class="text-left btn btn-danger btn-block btn-eliminar_item_proveedor"> X </button>';
        table_enlace_producto += "</td>";

        table_enlace_producto +=
          '<input type="hidden" name="addProducto[' +
          id_item +
          '][id_item]" value="' +
          id_item +
          '">';
        table_enlace_producto += "</tr>";

        table_enlace_producto += "<tr><td class='text-left' colspan='14'>";
        if (voucher_1 == "" || voucher_1 == null) {
          table_enlace_producto +=
            '<button type="button" id="btn-agregar_pago_proveedor' +
            id_item +
            '" data-tipo_pago="1" data-id="' +
            id_item +
            '" class="text-left btn btn-primary btn-block btn-agregar_pago_proveedor" data-id_empresa="' +
            response.ID_Empresa +
            '" data-id_organizacion="' +
            response.ID_Organizacion +
            '" data-id_pedido_cabecera="' +
            response.ID_Pedido_Cabecera +
            '" data-id_pedido_detalle="' +
            response.ID_Pedido_Detalle +
            '" data-correlativo="' +
            response.sCorrelativoCotizacion +
            '"><i class="fas fa-money-bill-alt"></i>&nbsp; Pagar Proveedor (Deposit_#1)</button>';
        } else {
          //table_enlace_producto += '<button type="button" id="btn-ver_pago_proveedor' + id_item + '" data-url_img="' + voucher_1 + '" data-id="' + id_item + '" class="text-left btn btn-secondary btn-block btn-ver_pago_proveedor" data-id_empresa="' + response.ID_Empresa + '" data-id_organizacion="' + response.ID_Organizacion + '" data-id_pedido_cabecera="' + response.ID_Pedido_Cabecera + '" data-id_pedido_detalle="' + response.ID_Pedido_Detalle + '" data-correlativo="' + response.sCorrelativoCotizacion + '"><i class="fas fa-money-bill-alt"></i>&nbsp; Pago ¥ ' + Ss_Pago_1_Proveedor +  ' (Deposit_#1)</button>';
          table_enlace_producto +=
            '<button type="button" id="btn-ver_pago_proveedor' +
            id_item +
            '" data-url_img="' +
            voucher_1 +
            '" data-id="' +
            id_item +
            '" class="text-left btn btn-secondary btn-block btn-ver_pago_proveedor" data-id_empresa="' +
            response.ID_Empresa +
            '" data-id_organizacion="' +
            response.ID_Organizacion +
            '" data-id_pedido_cabecera="' +
            response.ID_Pedido_Cabecera +
            '" data-id_pedido_detalle="' +
            response.ID_Pedido_Detalle +
            '" data-correlativo="' +
            response.sCorrelativoCotizacion +
            '"><i class="fas fa-money-bill-alt"></i>&nbsp; Pago ¥ (Deposit_#1)</button>';
          if (voucher_2 == "" || voucher_2 == null) {
            table_enlace_producto +=
              '<button type="button" id="btn-agregar_pago_proveedor' +
              id_item +
              '" data-tipo_pago="2" data-id="' +
              id_item +
              '" class="text-left btn btn-primary btn-block btn-agregar_pago_proveedor" data-id_empresa="' +
              response.ID_Empresa +
              '" data-id_organizacion="' +
              response.ID_Organizacion +
              '" data-id_pedido_cabecera="' +
              response.ID_Pedido_Cabecera +
              '" data-id_pedido_detalle="' +
              response.ID_Pedido_Detalle +
              '" data-correlativo="' +
              response.sCorrelativoCotizacion +
              '"><i class="fas fa-money-bill-alt"></i>&nbsp; Pagar Proveedor (Deposit_#2)</button>';
          } else {
            //table_enlace_producto += '<button type="button" id="btn-ver_pago_proveedor' + id_item + '" data-url_img="' + voucher_2 + '" data-id="' + id_item + '" class="text-left btn btn-secondary btn-block btn-ver_pago_proveedor" data-id_empresa="' + response.ID_Empresa + '" data-id_organizacion="' + response.ID_Organizacion + '" data-id_pedido_cabecera="' + response.ID_Pedido_Cabecera + '" data-id_pedido_detalle="' + response.ID_Pedido_Detalle + '" data-correlativo="' + response.sCorrelativoCotizacion + '"><i class="fas fa-money-bill-alt"></i>&nbsp; Pago ¥ ' + Ss_Pago_2_Proveedor + ' (Deposit_#2)</button>';
            table_enlace_producto +=
              '<button type="button" id="btn-ver_pago_proveedor' +
              id_item +
              '" data-url_img="' +
              voucher_2 +
              '" data-id="' +
              id_item +
              '" class="text-left btn btn-secondary btn-block btn-ver_pago_proveedor" data-id_empresa="' +
              response.ID_Empresa +
              '" data-id_organizacion="' +
              response.ID_Organizacion +
              '" data-id_pedido_cabecera="' +
              response.ID_Pedido_Cabecera +
              '" data-id_pedido_detalle="' +
              response.ID_Pedido_Detalle +
              '" data-correlativo="' +
              response.sCorrelativoCotizacion +
              '"><i class="fas fa-money-bill-alt"></i>&nbsp; Pago ¥ (Deposit_#2)</button>';
          }
        }
        table_enlace_producto += "</td></tr>";
      }

      $("#span-total_cantidad_items").html(i);
      $("#table-Producto_Enlace").append(table_enlace_producto);

      $("#span-total_cliente").html("$ " + fTotalCliente.toFixed(2));

      //PAGOS
      //Ss_Pago_30_Cliente
      //Ss_Pago_100_Cliente
      //Ss_Pago_Servicio_Cliente

      //OTROS
      //Ss_Pago_Otros_Flete
      //Ss_Pago_Otros_Costo_Origen
      //Ss_Pago_Otros_Costo_Fta
      //Ss_Pago_Otros_Cuadrilla
      //Ss_Pago_Otros_Costos
      $("#span-saldo_cliente").html(
        "$ " +
          (fTotalCliente -
            (parseFloat(response.Ss_Pago_30_Cliente) +
              parseFloat(response.Ss_Pago_100_Cliente) +
              parseFloat(response.Ss_Pago_Servicio_Cliente)))
      );

      //Date picker invoice
      $(".input-datepicker-today-to-more").datepicker({
        autoclose: true,
        startDate: new Date(fYear, fToday.getMonth(), fDay),
        todayHighlight: true,
        dateFormat: "dd/mm/yyyy",
        format: "dd/mm/yyyy",
      });
    },
    error: function (jqXHR, textStatus, errorThrown) {
      $(".modal-message").removeClass(
        "modal-danger modal-warning modal-success"
      );

      $("#modal-message").modal("show");
      $(".modal-message").addClass("modal-danger");
      $(".modal-title-message").text(
        textStatus + " [" + jqXHR.status + "]: " + errorThrown
      );
      setTimeout(function () {
        $("#modal-message").modal("hide");
      }, 1700);

      //Message for developer
      console.log(jqXHR.responseText);
    },
  });
}

function cambiarEstado(ID, Nu_Estado, id_pedido_cabecera, sCorrelativo) {
  var $modal_delete = $("#modal-message-delete");
  $modal_delete.modal("show");

  $(".modal-message-delete").removeClass(
    "modal-danger modal-warning modal-success"
  );
  $(".modal-message-delete").addClass("modal-success");

  var sNombreEstado = "Pago 30%";
  if (Nu_Estado == 7) sNombreEstado = "Pago 70%";
  else if (Nu_Estado == 9) sNombreEstado = "Pago servicio";
  else if (Nu_Estado == 3) sNombreEstado = "Volver a Garantizado";

  $("#modal-title").html(
    "¿Deseas cambiar a <strong>" + sNombreEstado + "</strong>?"
  );

  $("#btn-cancel-delete")
    .off("click")
    .click(function () {
      $modal_delete.modal("hide");
    });

  $("#btn-save-delete")
    .off("click")
    .click(function () {
      $("#btn-save-delete").text("");
      $("#btn-save-delete").attr("disabled", true);
      $("#btn-save-delete").append(
        'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>'
      );

      url =
        base_url +
        "AgenteCompra/PedidosPagados/cambiarEstado/" +
        ID +
        "/" +
        Nu_Estado +
        "/" +
        sCorrelativo;
      $.ajax({
        url: url,
        type: "GET",
        dataType: "JSON",
        success: function (response) {
          $modal_delete.modal("hide");

          $("#btn-save-delete").text("");
          $("#btn-save-delete").append("Aceptar");
          $("#btn-save-delete").attr("disabled", false);

          $(".modal-message").removeClass(
            "modal-danger modal-warning modal-success"
          );
          $("#modal-message").modal("show");

          if (response.status == "success") {
            $(".modal-message").addClass(response.style_modal);
            $(".modal-title-message").text(response.message);
            setTimeout(function () {
              $("#modal-message").modal("hide");
            }, 1100);
            reload_table_Entidad();
          } else {
            $(".modal-message").addClass(response.style_modal);
            $(".modal-title-message").text(response.message);
            setTimeout(function () {
              $("#modal-message").modal("hide");
            }, 1500);
          }
        },
      });
    });
}

function caracteresValidosAutocomplete(msg) {
  // Recorrer todos los caracteres
  search_global_autocomplete.forEach((char, index) => {
    // Remplazar cada caracter en la cadena
    msg = msg.replaceAll(char, replace_global_autocomplete[index]);
  });
  return msg;
}

function agregarItemVentaTemporal(arrParams) {
  var table_enlace_producto =
    "<tr id='tr_enlace_producto" +
    arrParams.id +
    "'>" +
    "<td style='display:none;' class='text-left td-id_item'>" +
    arrParams.id +
    "</td>" +
    "<td style='display:none;' class='text-left td-id_item_bd'>" +
    arrParams.id_item +
    "</td>" +
    "<td style='display:none;' class='text-left td-id_unidad_medida_bd'>" +
    arrParams.id_unidad_medida +
    "</td>" +
    "<td style='display:none;' class='text-left td-id_unidad_medida_precio_bd'>" +
    arrParams.id_unidad_medida_2 +
    "</td>" +
    "<td class='text-left td-name'>" +
    arrParams.nombre +
    "</td>" +
    "<td class='text-left td-unidad_medida'>" +
    arrParams.nombre_unidad_medida +
    "</td>" +
    "<td class='text-right td-cantidad'>" +
    arrParams.cantidad_item +
    "</td>" +
    "<td class='text-right td-precio'>" +
    arrParams.precio_item +
    "</td>" +
    "<td class='text-right td-total'>" +
    arrParams.total_item +
    "</td>" +
    "<td class='text-center'><button type='button' id='btn-deleteProductoEnlace' class='btn btn-xs btn-link text-danger' alt='Eliminar' title='Eliminar'><i class='fas fa-trash-alt fa-2x' aria-hidden='true'></i></button></td>";
  table_enlace_producto +=
    '<input type="hidden" name="addProducto[' +
    arrParams.id +
    '][id_item]" value="' +
    arrParams.id_item +
    '">';
  table_enlace_producto +=
    '<input type="hidden" name="addProducto[' +
    arrParams.id +
    '][id_unidad_medida]" value="' +
    arrParams.id_unidad_medida +
    '">';
  table_enlace_producto +=
    '<input type="hidden" name="addProducto[' +
    arrParams.id +
    '][id_unidad_medida_2]" value="' +
    arrParams.id_unidad_medida_2 +
    '">';
  table_enlace_producto +=
    '<input type="hidden" name="addProducto[' +
    arrParams.id +
    '][cantidad_item]" value="' +
    arrParams.cantidad_item +
    '">';
  table_enlace_producto +=
    '<input type="hidden" name="addProducto[' +
    arrParams.id +
    '][precio_item]" value="' +
    arrParams.precio_item +
    '">';
  table_enlace_producto +=
    '<input type="hidden" name="addProducto[' +
    arrParams.id +
    '][total_item]" value="' +
    arrParams.total_item +
    '">';
  table_enlace_producto += "</tr>";

  if (isExistTableTemporalProducto(arrParams.id)) {
    $("#txt-ANombre")
      .closest(".form-group")
      .find(".help-block")
      .html("Ya existe <b>" + arrParams.nombre_interno + "</b>");
    $("#txt-ANombre")
      .closest(".form-group")
      .removeClass("has-success")
      .addClass("has-error");
    $("#txt-AID").val("");
    $("#txt-ID_Producto").val("");
    $("#txt-ID_Unidad_Medida").val("");
    $("#txt-ID_Unidad_Medida_2").val("");
    $("#txt-Precio_Producto").val("");
    $("#txt-Cantidad_Configurada_Producto").val("");
    $("#txt-Nombre_Producto").val("");
    $("#txt-Nombre_Unidad_Medida").val("");
    $("#txt-ANombre").val("");

    $("#txt-ANombre").focus();
  } else {
    $("#table-Producto_Enlace").show();
    $("#table-Producto_Enlace").append(table_enlace_producto);
    $("#txt-AID").val("");
    $("#txt-ID_Producto").val("");
    $("#txt-ID_Unidad_Medida").val("");
    $("#txt-ID_Unidad_Medida_2").val("");
    $("#txt-Precio_Producto").val("");
    $("#txt-Cantidad_Configurada_Producto").val("");
    $("#txt-Nombre_Producto").val("");
    $("#txt-Nombre_Unidad_Medida").val("");
    $("#txt-ANombre").val("");

    $("#txt-ANombre").focus();

    //totalizar items
    calcularTotales();
  }
}

function isExistTableTemporalProducto($id) {
  return Array.from($("tr[id*=tr_enlace_producto]")).some(
    (element) => $("td:nth(0)", $(element)).html() == $id
  );
}

function form_pedido() {
  if ($("#table-Producto_Enlace >tbody >tr").length == 0) {
    $("#txt-ANombre")
      .closest(".form-group")
      .find(".help-block")
      .html("Elegir al menos 1 producto");
    $("#txt-ANombre")
      .closest(".form-group")
      .removeClass("has-success")
      .addClass("has-error");
    $("#txt-ANombre").focus();
  } else {
    $("#btn-save").text("");
    $("#btn-save").attr("disabled", true);
    $("#btn-save").append(
      'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>'
    );

    url = base_url + "AgenteCompra/PedidosPagados/crudPedidoGrupal";
    $.ajax({
      type: "POST",
      dataType: "JSON",
      url: url,
      data: $("#form-pedido").serialize(),
      success: function (response) {
        $("#moda-message-content").removeClass(
          "bg-danger bg-warning bg-success"
        );
        $("#modal-message").modal("show");

        if (response.status == "success") {
          $("#form-pedido")[0].reset();

          $(".div-AgregarEditar").hide();
          $(".div-Listar").show();

          $("#moda-message-content").addClass("bg-" + response.status);
          $(".modal-title-message").text(response.message);
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 1100);
          reload_table_Entidad();
        } else {
          $("#moda-message-content").addClass("bg-danger");
          $(".modal-title-message").text(response.message);
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 1200);
        }

        $("#btn-save").text("");
        $("#btn-save").append("Guardar");
        $("#btn-save").attr("disabled", false);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        $(".modal-message").removeClass(
          "modal-danger modal-warning modal-success"
        );

        $("#modal-message").modal("show");
        $(".modal-message").addClass("modal-danger");
        $(".modal-title-message").text(
          textStatus + " [" + jqXHR.status + "]: " + errorThrown
        );
        setTimeout(function () {
          $("#modal-message").modal("hide");
        }, 1700);

        //Message for developer
        console.log(jqXHR.responseText);

        $("#btn-save").text("");
        $("#btn-save").append("Guardar");
        $("#btn-save").attr("disabled", false);
      },
    });
  }
}

function calcularTotales() {
  var fCantidadTotal = 0,
    fImporteTotal = 0;
  $("#table-Producto_Enlace > tbody > tr").each(function () {
    fila = $(this);
    const fCantidad = parseFloat(fila.find(".td-cantidad").text());
    const fPrecio = parseFloat(fila.find(".td-precio").text());

    fCantidadTotal += fCantidad;
    fImporteTotal += fCantidad * fPrecio;
  });

  $("#label-total_cantidad").text(Math.round10(fCantidadTotal, -2));
  $("#label-total_importe").text(Math.round10(fImporteTotal, -2));
}

function generarExcelOrderTracking(ID) {
  var ID = $("#btn-excel_order_tracking").data("id_pedido");

  var $modal_delete = $("#modal-message-delete");
  $modal_delete.modal("show");

  $(".modal-message-delete").removeClass(
    "modal-danger modal-warning modal-success"
  );
  $(".modal-message-delete").addClass("modal-success");

  $("#modal-title").text("¿Deseas genera EXCEL?");

  $("#btn-cancel-delete")
    .off("click")
    .click(function () {
      $modal_delete.modal("hide");
    });

  $("#btn-save-delete")
    .off("click")
    .click(function () {
      _generarExcelOrderTracking($modal_delete, ID);
    });
}

function _generarExcelOrderTracking($modal_delete, ID) {
  $modal_delete.modal("hide");
  url =
    base_url + "AgenteCompra/PedidosPagados/generarExcelOrderTracking/" + ID;
  window.open(url, "_blank");
}

function loadFile(event, id) {
  var output = document.getElementById("img_producto-preview" + id);
  output.src = URL.createObjectURL(event.target.files[0]);
  output.onload = function () {
    URL.revokeObjectURL(output.src); // free memory
  };
}

function cambiarEstadoChina(ID, Nu_Estado, iIdCorrelativo, sCorrelativo) {
  var $modal_delete = $("#modal-message-delete");
  $modal_delete.modal("show");

  $(".modal-message-delete").removeClass(
    "modal-danger modal-warning modal-success"
  );
  $(".modal-message-delete").addClass("modal-success");

  var sNombreEstado = "Producción";
  if (Nu_Estado == 5) sNombreEstado = "Inspección";
  else if (Nu_Estado == 6) sNombreEstado = "Entregado";

  $("#modal-title").html(
    "¿Deseas cambiar estado a <strong>" + sNombreEstado + "</strong>?"
  );

  $("#btn-cancel-delete")
    .off("click")
    .click(function () {
      $modal_delete.modal("hide");
    });

  $("#btn-save-delete")
    .off("click")
    .click(function () {
      $("#btn-save-delete").text("");
      $("#btn-save-delete").attr("disabled", true);
      $("#btn-save-delete").append(
        'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>'
      );

      url =
        base_url +
        "AgenteCompra/PedidosPagados/cambiarEstadoChina/" +
        ID +
        "/" +
        Nu_Estado +
        "/" +
        sCorrelativo;
      $.ajax({
        url: url,
        type: "GET",
        dataType: "JSON",
        success: function (response) {
          $modal_delete.modal("hide");

          $("#btn-save-delete").text("");
          $("#btn-save-delete").append("Aceptar");
          $("#btn-save-delete").attr("disabled", false);

          $(".modal-message").removeClass(
            "modal-danger modal-warning modal-success"
          );
          $("#modal-message").modal("show");

          if (response.status == "success") {
            $(".modal-message").addClass(response.style_modal);
            $(".modal-title-message").text(response.message);
            setTimeout(function () {
              $("#modal-message").modal("hide");
            }, 1100);
            reload_table_Entidad();
          } else {
            $(".modal-message").addClass(response.style_modal);
            $(".modal-title-message").text(response.message);
            setTimeout(function () {
              $("#modal-message").modal("hide");
            }, 1500);
          }
        },
      });
    });
}

function subirInspeccion(ID) {
  $(".div-Listar").hide();

  $("#form-pedido")[0].reset();
  $(".form-group").removeClass("has-error");
  $(".form-group").removeClass("has-success");
  $(".help-block").empty();

  $(".div-Compuesto").show();
  $("#table-Producto_Enlace tbody").empty();
  $("#table-Producto_Enlace").show();

  $(".div-Producto_Recepcion_Carga").hide();
  $("#table-Producto_Recepcion_Carga tbody").empty();

  $(".div-Invoice_Proveedor").hide();
  $("#table-Invoice_Proveedor tbody").empty();

  $(".div-Pago_Proveedor").hide();
  $("#table-Pago_Proveedor tbody").empty();

  //$('#span-id_pedido').html('Nro. ' + ID);

  url = base_url + "AgenteCompra/PedidosPagados/ajax_edit/" + ID;
  $.ajax({
    url: url,
    type: "GET",
    dataType: "JSON",
    success: function (response) {
      console.log(response);
      var detalle = response;
      response = response[0];

      $("#span-id_pedido").html(response.sCorrelativoCotizacion);

      $(".div-AgregarEditar").show();

      $('[name="EID_Pedido_Cabecera"]').val(response.ID_Pedido_Cabecera);
      $('[name="EID_Entidad"]').val(response.ID_Entidad);
      $('[name="EID_Empresa"]').val(response.ID_Empresa);
      $('[name="EID_Organizacion"]').val(response.ID_Organizacion);

      $('[name="No_Contacto"]').val(response.No_Contacto);
      $('[name="Txt_Email_Contacto"]').val(response.Txt_Email_Contacto);
      $('[name="Nu_Celular_Contacto"]').val(response.Nu_Celular_Contacto);
      $('[name="No_Entidad"]').val(response.No_Entidad);
      $('[name="Nu_Documento_Identidad"]').val(response.Nu_Documento_Identidad);

      var sNombreEstado =
        '<span class="badge badge-pill badge-secondary">Pendiente</span>';
      if (response.Nu_Estado_Pedido == 2)
        sNombreEstado =
          '<span class="badge badge-pill badge-primary">Confirmado</span>';
      else if (response.Nu_Estado_Pedido == 3)
        sNombreEstado =
          '<span class="badge badge-pill badge-success">Entregado</span>';
      else if (response.Nu_Estado_Pedido == 4)
        sNombreEstado =
          '<span class="badge badge-pill badge-danger">Confirmado</span>';
      $("#div-estado").html(sNombreEstado);

      var table_enlace_producto = "",
        iDiasVencimiento = 0,
        sClassColorTr = "",
        iCounterSupplier = 1,
        ID_Entidad = "";
      for (i = 0; i < detalle.length; i++) {
        var cantidad_item = parseFloat(detalle[i]["Qt_Producto"]);
        var precio_china = parseFloat(detalle[i]["Ss_Precio"]);

        var id_item = detalle[i]["ID_Pedido_Detalle_Producto_Proveedor"];
        var voucher_1 = detalle[i]["Txt_Url_Archivo_Pago_1_Proveedor"];
        var voucher_2 = detalle[i]["Txt_Url_Archivo_Pago_2_Proveedor"];
        var fTotal = precio_china * cantidad_item;

        var Ss_Pago_1_Proveedor = parseFloat(detalle[i]["Ss_Pago_1_Proveedor"]);
        var Ss_Pago_2_Proveedor = parseFloat(detalle[i]["Ss_Pago_2_Proveedor"]);

        sClassColorTr = "";
        iDiasVencimiento = 0;
        if (
          detalle[i]["Fe_Entrega_Proveedor"] != "" &&
          detalle[i]["Fe_Entrega_Proveedor"] != null
        ) {
          var fechaInicio = new Date(
            fYear + "-" + fMonth + "-" + fDay
          ).getTime();
          var fechaFin = new Date(detalle[i]["Fe_Entrega_Proveedor"]).getTime();

          var diff = fechaFin - fechaInicio;
          iDiasVencimiento = diff / (1000 * 60 * 60 * 24); // --> milisegundos -> segundos -> minutos -> horas -> días
          if (iDiasVencimiento < 5) sClassColorTr = "table-warning";
        }

        var fecha_entrega_proveedor =
          detalle[i]["Fe_Entrega_Proveedor"] != "" &&
          detalle[i]["Fe_Entrega_Proveedor"] != null
            ? ParseDateString(
                detalle[i]["Fe_Entrega_Proveedor"],
                "fecha_bd",
                "-"
              )
            : "";

        if (ID_Entidad != detalle[i].ID_Entidad_Proveedor) {
          table_enlace_producto +=
            "<tr class='table-active'>" +
            "<th class='text-right'>" +
            iCounterSupplier +
            ". Supplier</th>";
          table_enlace_producto += "<th class='text-left' colspan='8'>";
          table_enlace_producto += detalle[i].No_Contacto_Proveedor;
          +"</tr>";
          ID_Entidad = detalle[i].ID_Entidad_Proveedor;
          ++iCounterSupplier;
        }

        table_enlace_producto +=
          "<tr id='tr_enlace_producto" +
          id_item +
          "' class='" +
          sClassColorTr +
          "'>" +
          "<td style='display:none;' class='text-left td-id_item'>" +
          id_item +
          "</td>" +
          "<td class='text-center td-name' width='30%'>" +
          "<img data-id_item='" +
          id_item +
          "' data-url_img='" +
          detalle[i]["Txt_Url_Imagen_Producto"] +
          "' src='" +
          detalle[i]["Txt_Url_Imagen_Producto"] +
          "' alt='" +
          detalle[i]["Txt_Producto"] +
          "' class='img-thumbnail img-table_item img-fluid img-resize mb-2'>";

        table_enlace_producto +=
          "</td>" +
          "<td class='text-left td-name'>" +
          detalle[i]["Txt_Producto"] +
          "</td>" +
          "<td class='text-right td-qty'>" +
          Math.round10(cantidad_item, -2) +
          "</td>" +
          "<td class='text-right td-price'>" +
          Math.round10(precio_china, -2) +
          "</td>" +
          "<td class='text-right td-amount'>" +
          Math.round10(fTotal, -2) +
          "</td>" +
          //+"<td class='text-right td-pay1'>" + Math.round10(Ss_Pago_1_Proveedor, -2) + "</td>"
          //+"<td class='text-right td-balance'>" + Math.round10(fTotal - Ss_Pago_1_Proveedor, -2) + "</td>"
          //+"<td class='text-right td-pay2'>" + Math.round10(Ss_Pago_2_Proveedor, -2) + "</td>"
          "<td class='text-left td-delivery_date'>" +
          detalle[i]["Nu_Dias_Delivery"] +
          "</td>";
        //+"<td class='text-left td-costo_delivery'>" + detalle[i]['Ss_Costo_Delivery'] + "</td>";

        table_enlace_producto += "<td class='text-left td-supplier'>";
        table_enlace_producto +=
          '<div class="input-group date" style="width:100%">';
        table_enlace_producto +=
          '<input type="text" id="txt-fecha_entrega_proveedor' +
          i +
          '" name="addProducto[' +
          id_item +
          '][fecha_entrega_proveedor]" class="form-control input-datepicker-today-to-more required" value="' +
          fecha_entrega_proveedor +
          '">';
        table_enlace_producto += "</div>";
        table_enlace_producto += "</td>";

        table_enlace_producto += "<td class='text-left td-supplier'></td>";
        //table_enlace_producto += "<td class='text-left td-supplier'>" + detalle[i]['No_Contacto_Proveedor'] + "</td>"
        /*
          table_enlace_producto += "<td class='text-left td-phone'>";
          if(detalle[i]['Txt_Url_Imagen_Proveedor'] != '' && detalle[i]['Txt_Url_Imagen_Proveedor'] != null){
            table_enlace_producto += "<img style='' data-id_item='" + id_item + "' data-url_img='" + detalle[i]['Txt_Url_Imagen_Proveedor'] + "' src='" + detalle[i]['Txt_Url_Imagen_Proveedor'] + "' alt='" + detalle[i]['Txt_Producto'] + "' class='img-thumbnail img-table_item img-fluid img-resize mb-2'>";
          }
          */
        table_enlace_producto += "</td>";

        table_enlace_producto += "<td class='text-left td-eliminar'>";
        //table_enlace_producto += '<button type="button" id="btn-eliminar_item_proveedor' + id_item + '" data-name_item="' + detalle[i]['Txt_Producto'] + '" data-id_pedido_cabecera="' + response.ID_Pedido_Cabecera + '" data-id="' + id_item + '" data-correlativo="' + response.sCorrelativoCotizacion + '" class="text-left btn btn-danger btn-block btn-eliminar_item_proveedor"> X </button>';
        table_enlace_producto += "</td>";

        table_enlace_producto +=
          '<input type="hidden" name="addProducto[' +
          id_item +
          '][id_item]" value="' +
          id_item +
          '">';
        table_enlace_producto += "</tr>";

        //table_enlace_producto += '<input type="hidden" name="addProducto[' + id_item + '][id_item]" value="' + id_item + '">';
        //table_enlace_producto += "</tr>";

        table_enlace_producto += "<tr><td class='text-left' colspan='15'>";
        if (detalle[i]["Nu_Agrego_Inspeccion"] == 0) {
          //0=No
          table_enlace_producto +=
            '<button type="button" id="btn-agregar_inspeccion' +
            id_item +
            '" data-tipo_pago="1" data-id="' +
            id_item +
            '" data-correlativo="' +
            response.sCorrelativoCotizacion +
            '" class="text-left btn btn-primary btn-block btn-agregar_inspeccion" data-id_empresa="' +
            response.ID_Empresa +
            '" data-id_organizacion="' +
            response.ID_Organizacion +
            '" data-id_pedido_cabecera="' +
            response.ID_Pedido_Cabecera +
            '" data-id_pedido_detalle="' +
            response.ID_Pedido_Detalle +
            '"><i class="fas fa-images"></i>&nbsp; Subir fotos</button>';
        } else {
          table_enlace_producto +=
            '<button type="button" id="btn-agregar_inspeccion' +
            id_item +
            '" data-tipo_pago="1" data-id="' +
            id_item +
            '" data-correlativo="' +
            response.sCorrelativoCotizacion +
            '" class="text-left btn btn-primary btn-block btn-agregar_inspeccion" data-id_empresa="' +
            response.ID_Empresa +
            '" data-id_organizacion="' +
            response.ID_Organizacion +
            '" data-id_pedido_cabecera="' +
            response.ID_Pedido_Cabecera +
            '" data-id_pedido_detalle="' +
            response.ID_Pedido_Detalle +
            '"><i class="fas fa-images"></i>&nbsp; Subir fotos</button>';
          table_enlace_producto +=
            '<button type="button" id="btn-ver_inspeccion' +
            id_item +
            '" onclick=verInspeccion(' +
            id_item +
            ') class="text-left btn btn-secondary btn-block btn-ver_inspeccion"><i class="fas fa-search"></i>&nbsp; Ver fotos</button>';
        }
        table_enlace_producto += "</td></tr>";
      }

      $("#span-total_cantidad_items").html(i);
      $("#table-Producto_Enlace").append(table_enlace_producto);

      //Date picker invoice
      $(".input-datepicker-today-to-more").datepicker({
        autoclose: true,
        startDate: new Date(fYear, fToday.getMonth(), fDay),
        todayHighlight: true,
        dateFormat: "dd/mm/yyyy",
        format: "dd/mm/yyyy",
      });
    },
    error: function (jqXHR, textStatus, errorThrown) {
      $(".modal-message").removeClass(
        "modal-danger modal-warning modal-success"
      );

      $("#modal-message").modal("show");
      $(".modal-message").addClass("modal-danger");
      $(".modal-title-message").text(
        textStatus + " [" + jqXHR.status + "]: " + errorThrown
      );
      setTimeout(function () {
        $("#modal-message").modal("hide");
      }, 1700);

      //Message for developer
      console.log(jqXHR.responseText);
    },
  });
}

function verInspeccion(ID) {
  $("#div-img_inspeccion_item").html("");

  url = base_url + "AgenteCompra/PedidosPagados/ajax_edit_inspeccion/" + ID;
  $.ajax({
    url: url,
    type: "GET",
    dataType: "JSON",
    success: function (response) {
      console.log(response);

      $("#modal-ver_inspeccion_item").modal("show");

      var detalle = response;
      response = response[0];

      var table_enlace_producto = "";
      for (i = 0; i < detalle.length; i++) {
        var id_item = detalle[i]["ID_Pedido_Detalle_Producto_Inspeccion"]; //max-height: 350px;width: 100%; cursor:pointer
        table_enlace_producto +=
          "<img data-id_item='" +
          id_item +
          "' data-url_img='" +
          detalle[i]["Txt_Url_Imagen_Producto"] +
          "' src='" +
          detalle[i]["Txt_Url_Imagen_Producto"] +
          "' alt='' class='img-thumbnail img-table_item img-fluid img-resize mb-2'>";
      }
      $("#div-img_inspeccion_item").html(table_enlace_producto);
    },
    error: function (jqXHR, textStatus, errorThrown) {
      $(".modal-message").removeClass(
        "modal-danger modal-warning modal-success"
      );

      $("#modal-message").modal("show");
      $(".modal-message").addClass("modal-danger");
      $(".modal-title-message").text(
        textStatus + " [" + jqXHR.status + "]: " + errorThrown
      );
      setTimeout(function () {
        $("#modal-message").modal("hide");
      }, 1700);

      //Message for developer
      console.log(jqXHR.responseText);
    },
  });
}

function documentoEntregado(id, sCorrelativo) {
  $('[name="documento-id_cabecera"]').val(id);
  $('[name="documento-correlativo"]').val(sCorrelativo);

  $("#modal-documento_entrega").modal("show");
  $("#form-documento_entrega")[0].reset();
}

function descargarDocumentoEntregado(id) {
  url =
    base_url + "AgenteCompra/PedidosPagados/descargarDocumentoEntregado/" + id;

  var popupwin = window.open(url);
  setTimeout(function () {
    popupwin.close();
  }, 2000);
}

function descargarDocumentoDetalle(id) {
  url =
    base_url + "AgenteCompra/PedidosPagados/descargarDocumentoDetalle/" + id;

  var popupwin = window.open(url);
  setTimeout(function () {
    popupwin.close();
  }, 2000);
}

function subirPago30() {
  $('[name="pago_cliente_30-id_cabecera"]').val(
    $("#txt-EID_Pedido_Cabecera").val()
  );

  $("#modal-pago_cliente_30").modal("show");
  $("#form-pago_cliente_30")[0].reset();
}

function descargarPago30() {
  var id = $("#txt-EID_Pedido_Cabecera").val();
  url = base_url + "AgenteCompra/PedidosPagados/descargarPago30/" + id;

  var popupwin = window.open(url);
  setTimeout(function () {
    popupwin.close();
  }, 2000);
}

function subirPago100() {
  $('[name="pago_cliente_100-id_cabecera"]').val(
    $("#txt-EID_Pedido_Cabecera").val()
  );

  $("#modal-pago_cliente_100").modal("show");
  $("#form-pago_cliente_100")[0].reset();
}

function descargarPago100() {
  var id = $("#txt-EID_Pedido_Cabecera").val();
  url = base_url + "AgenteCompra/PedidosPagados/descargarPago100/" + id;

  var popupwin = window.open(url);
  setTimeout(function () {
    popupwin.close();
  }, 2000);
}

function cambiarTipoServicio(
  ID,
  Nu_Tipo_Servicio,
  ID_Usuario_Interno_Empresa_China
) {
  // if (ID_Usuario_Interno_Empresa_China == 0) {
  //   //3 - Enviado
  //   $("#moda-message-content").removeClass("bg-danger bg-warning bg-success");
  //   $("#modal-message").modal("show");

  //   $("#moda-message-content").addClass("bg-warning");
  //   $(".modal-title-message").html("Primero asignar Jefe de China");

  //   setTimeout(function () {
  //     $("#modal-message").modal("hide");
  //   }, 3100);
  // } else {
  var $modal_delete = $("#modal-message-delete");
  $modal_delete.modal("show");

  $(".modal-message-delete").removeClass(
    "modal-danger modal-warning modal-success"
  );
  $(".modal-message-delete").addClass("modal-success");

  var sNombreEstado = "Trading";
  if (Nu_Tipo_Servicio == 2) sNombreEstado = "C. Trading";

  $("#modal-title").html(
    "¿Deseas cambiar estado a <strong>" + sNombreEstado + "</strong>?"
  );

  $("#btn-cancel-delete")
    .off("click")
    .click(function () {
      $modal_delete.modal("hide");
    });

  $("#btn-save-delete")
    .off("click")
    .click(function () {
      $("#btn-save-delete").text("");
      $("#btn-save-delete").attr("disabled", true);
      $("#btn-save-delete").append(
        'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>'
      );

      url =
        base_url +
        "AgenteCompra/PedidosPagados/cambiarTipoServicio/" +
        ID +
        "/" +
        Nu_Tipo_Servicio +
        "/" +
        ID_Usuario_Interno_Empresa_China;
      $.ajax({
        url: url,
        type: "GET",
        dataType: "JSON",
        success: function (response) {
          $modal_delete.modal("hide");

          $("#btn-save-delete").text("");
          $("#btn-save-delete").append("Aceptar");
          $("#btn-save-delete").attr("disabled", false);

          $(".modal-message").removeClass(
            "modal-danger modal-warning modal-success"
          );
          $("#modal-message").modal("show");

          if (response.status == "success") {
            $(".modal-message").addClass(response.style_modal);
            $(".modal-title-message").text(response.message);
            setTimeout(function () {
              $("#modal-message").modal("hide");
            }, 1100);
            reload_table_Entidad();
          } else {
            $(".modal-message").addClass(response.style_modal);
            $(".modal-title-message").text(response.message);
            setTimeout(function () {
              $("#modal-message").modal("hide");
            }, 1500);
          }
        },
      });
    });
}
// }

function clearHTMLTextArea(str) {
  if (str == null) return "";
  str = str.replace(/<br>/gi, "");
  str = str.replace(/<br\s\/>/gi, "");
  str = str.replace(/<br\/>/gi, "");
  str = str.replace(/<\/button>/gi, "");
  str = str.replace(/<br >/gi, "");
  str = str.replace(/"/g, "&quot;").replace(/'/g, "&#39;");
  return str;
}
const changeStatusOrden = (estado, id_pedido) => {
  $.ajax({
    url: base_url + "AgenteCompra/PedidosPagados/cambiarEstadoOrden",
    type: "POST",
    data: {
      id_pedido,
      estado,
    },
    success: function (response) {
      if (response == "success") {
        reload_table_Entidad();
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.log(jqXHR.responseText);
    },
  });
};
function subirPagoServicio() {
  $('[name="pago_cliente_servicio-id_cabecera"]').val(
    $("#txt-EID_Pedido_Cabecera").val()
  );

  $("#modal-pago_cliente_servicio").modal("show");
  $("#form-pago_cliente_servicio")[0].reset();
}

function descargarPagoServicio() {
  var id = $("#txt-EID_Pedido_Cabecera").val();
  url = base_url + "AgenteCompra/PedidosPagados/descargarPagoServicio/" + id;

  var popupwin = window.open(url);
  setTimeout(function () {
    popupwin.close();
  }, 2000);
}

function cambiarIncoterms(ID, Nu_Estado, id_pedido_cabecera, sCorrelativo) {
  var $modal_delete = $("#modal-message-delete");
  $modal_delete.modal("show");

  $(".modal-message-delete").removeClass(
    "modal-danger modal-warning modal-success"
  );
  $(".modal-message-delete").addClass("modal-success");

  var sNombreEstado = "EXW";
  if (Nu_Estado == 2) sNombreEstado = "FOB";
  else if (Nu_Estado == 3) sNombreEstado = "CIF";
  else if (Nu_Estado == 4) sNombreEstado = "DAP";
  else if (Nu_Estado == 5) sNombreEstado = "FCA";
  else if (Nu_Estado == 6) sNombreEstado = "CFR";
  $("#modal-title").html(
    "¿Deseas cambiar a <strong>" + sNombreEstado + "</strong>?"
  );

  $("#btn-cancel-delete")
    .off("click")
    .click(function () {
      $modal_delete.modal("hide");
    });

  $("#btn-save-delete")
    .off("click")
    .click(function () {
      $("#btn-save-delete").text("");
      $("#btn-save-delete").attr("disabled", true);
      $("#btn-save-delete").append(
        'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>'
      );

      url =
        base_url +
        "AgenteCompra/PedidosPagados/cambiarIncoterms/" +
        ID +
        "/" +
        Nu_Estado +
        "/" +
        sCorrelativo;
      $.ajax({
        url: url,
        type: "GET",
        dataType: "JSON",
        success: function (response) {
          $modal_delete.modal("hide");

          $("#btn-save-delete").text("");
          $("#btn-save-delete").append("Aceptar");
          $("#btn-save-delete").attr("disabled", false);

          $(".modal-message").removeClass(
            "modal-danger modal-warning modal-success"
          );
          $("#modal-message").modal("show");

          if (response.status == "success") {
            $(".modal-message").addClass(response.style_modal);
            $(".modal-title-message").text(response.message);
            setTimeout(function () {
              $("#modal-message").modal("hide");
            }, 1100);
            reload_table_Entidad();
          } else {
            $(".modal-message").addClass(response.style_modal);
            $(".modal-title-message").text(response.message);
            setTimeout(function () {
              $("#modal-message").modal("hide");
            }, 1500);
          }
        },
      });
    });
}

function cambiarTransporte(ID, Nu_Estado, id_pedido_cabecera, sCorrelativo) {
  var $modal_delete = $("#modal-message-delete");
  $modal_delete.modal("show");

  $(".modal-message-delete").removeClass(
    "modal-danger modal-warning modal-success"
  );
  $(".modal-message-delete").addClass("modal-success");

  var sNombreEstado = "FCL";
  if (Nu_Estado == 2) sNombreEstado = "LCL";

  $("#modal-title").html(
    "¿Deseas cambiar a <strong>" + sNombreEstado + "</strong>?"
  );

  $("#btn-cancel-delete")
    .off("click")
    .click(function () {
      $modal_delete.modal("hide");
    });

  $("#btn-save-delete")
    .off("click")
    .click(function () {
      $("#btn-save-delete").text("");
      $("#btn-save-delete").attr("disabled", true);
      $("#btn-save-delete").append(
        'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>'
      );

      url =
        base_url +
        "AgenteCompra/PedidosPagados/cambiarTransporte/" +
        ID +
        "/" +
        Nu_Estado +
        "/" +
        sCorrelativo;
      $.ajax({
        url: url,
        type: "GET",
        dataType: "JSON",
        success: function (response) {
          $modal_delete.modal("hide");

          $("#btn-save-delete").text("");
          $("#btn-save-delete").append("Aceptar");
          $("#btn-save-delete").attr("disabled", false);

          $(".modal-message").removeClass(
            "modal-danger modal-warning modal-success"
          );
          $("#modal-message").modal("show");

          if (response.status == "success") {
            $(".modal-message").addClass(response.style_modal);
            $(".modal-title-message").text(response.message);
            setTimeout(function () {
              $("#modal-message").modal("hide");
            }, 1100);
            reload_table_Entidad();
          } else {
            $(".modal-message").addClass(response.style_modal);
            $(".modal-title-message").text(response.message);
            setTimeout(function () {
              $("#modal-message").modal("hide");
            }, 1500);
          }
        },
      });
    });
}

function agregarComisionTrading(ID) {
  $(".form-group").removeClass("has-error");
  $(".form-group").removeClass("has-success");
  $(".help-block").empty();

  $("#hidden-modal-id_pedido_cabecera_comision_trading").val(ID);
  $(".modal-comision_trading").modal("show");
  $("#txt-modal-precio_comision_trading").val("");
  $(".modal-comision_trading").on("shown.bs.modal", function () {
    $("#txt-modal-precio_comision_trading").focus();
  });
}

function subirPagoFlete() {
  $('[name="pago_flete-id_cabecera"]').val($("#txt-EID_Pedido_Cabecera").val());

  $("#modal-pago_flete").modal("show");
  $("#form-pago_flete")[0].reset();
}

function descargarPagoFlete() {
  var id = $("#txt-EID_Pedido_Cabecera").val();
  url = base_url + "AgenteCompra/PedidosPagados/descargarPagoFlete/" + id;

  var popupwin = window.open(url);
  setTimeout(function () {
    popupwin.close();
  }, 2000);
}

function subirPagoCostoOrigen() {
  $('[name="costos_origen-id_cabecera"]').val(
    $("#txt-EID_Pedido_Cabecera").val()
  );

  $("#modal-costos_origen").modal("show");
  $("#form-costos_origen")[0].reset();
}

function descargarPagoCostosOrigen() {
  var id = $("#txt-EID_Pedido_Cabecera").val();
  url =
    base_url + "AgenteCompra/PedidosPagados/descargarPagoCostosOrigen/" + id;

  var popupwin = window.open(url);
  setTimeout(function () {
    popupwin.close();
  }, 2000);
}

function subirPagoFTA() {
  $('[name="pago_fta-id_cabecera"]').val($("#txt-EID_Pedido_Cabecera").val());

  $("#modal-pago_fta").modal("show");
  $("#form-pago_fta")[0].reset();
}

function descargarPagoFTA() {
  var id = $("#txt-EID_Pedido_Cabecera").val();
  url = base_url + "AgenteCompra/PedidosPagados/descargarPagoFTA/" + id;

  var popupwin = window.open(url);
  setTimeout(function () {
    popupwin.close();
  }, 2000);
}

function subirPagoCuadrilla() {
  $('[name="otros_cuadrilla-id_cabecera"]').val(
    $("#txt-EID_Pedido_Cabecera").val()
  );

  $("#modal-otros_cuadrilla").modal("show");
  $("#form-otros_cuadrilla")[0].reset();
}

function descargarPagoCuadrilla() {
  var id = $("#txt-EID_Pedido_Cabecera").val();
  url = base_url + "AgenteCompra/PedidosPagados/descargarPagoCuadrilla/" + id;

  var popupwin = window.open(url);
  setTimeout(function () {
    popupwin.close();
  }, 2000);
}

function subirPagoOtrosCostos() {
  $('[name="otros_costos-id_cabecera"]').val(
    $("#txt-EID_Pedido_Cabecera").val()
  );

  $("#modal-otros_costos").modal("show");
  $("#form-otros_costos")[0].reset();
}

function descargarPagoOtrosCostos() {
  var id = $("#txt-EID_Pedido_Cabecera").val();
  url = base_url + "AgenteCompra/PedidosPagados/descargarPagoOtrosCostos/" + id;

  var popupwin = window.open(url);
  setTimeout(function () {
    popupwin.close();
  }, 2000);
}

function editarProveedor(ID_Entidad, id_item) {
  //alert(ID_Entidad);
  $("#form-proveedor")[0].reset();
  $(".form-group").removeClass("has-error");
  $(".form-group").removeClass("has-success");
  $(".help-block").empty();

  $(".modal-proveedor").modal("show");

  $('[name="proveedor-ID_Entidad"]').val(ID_Entidad);
  $('[name="proveedor-ID_Pedido_Detalle_Producto_Proveedor"]').val(id_item);

  url = base_url + "AgenteCompra/PedidosPagados/getPedidoProveedor/" + id_item;
  $.ajax({
    url: url,
    type: "GET",
    dataType: "JSON",
    success: function (response) {
      console.log(response);

      $('[name="proveedor-No_Contacto"]').val(response.No_Vendedor_Proveedor);
      $('[name="proveedor-No_Titular_Cuenta_Bancaria"]').val(
        response.No_Titular_Cuenta_Bancaria
      );

      $('[name="proveedor-No_Rubro"]').val(response.No_Rubro);

      $('[name="proveedor-Ss_Pago_Importe_1"]').val(response.Ss_Pago_Importe_1);

      $("#cbo-proveedor-Nu_Tipo_Pay_Proveedor_China").html(
        '<option value="0">Seleccionar</option>'
      );

      var selected = "";
      $(".div-banco_china").hide();
      if (response.Nu_Tipo_Pay_Proveedor_China == "1") {
        selected = 'selected="selected"';
        $(".div-banco_china").show();
      }
      $("#cbo-proveedor-Nu_Tipo_Pay_Proveedor_China").append(
        '<option value="1" ' + selected + ">Cuenta Bancaria</option>"
      );

      selected = "";
      if (response.Nu_Tipo_Pay_Proveedor_China == "2")
        selected = 'selected="selected"';
      $("#cbo-proveedor-Nu_Tipo_Pay_Proveedor_China").append(
        '<option value="2" ' + selected + ">AliPay</option>"
      );

      selected = "";
      if (response.Nu_Tipo_Pay_Proveedor_China == "3")
        selected = 'selected="selected"';
      $("#cbo-proveedor-Nu_Tipo_Pay_Proveedor_China").append(
        '<option value="3" ' + selected + ">WeChat</option>"
      );

      $('[name="proveedor-No_Cuenta_Bancaria"]').val(
        response.No_Cuenta_Bancaria
      );

      $('[name="proveedor-No_Banco_China"]').val(response.No_Banco_China);
    },
    error: function (jqXHR, textStatus, errorThrown) {
      //$( '#modal-loader' ).modal('hide');
      $(".modal-message").removeClass(
        "modal-danger modal-warning modal-success"
      );

      $("#modal-message").modal("show");
      $(".modal-message").addClass("modal-danger");
      $(".modal-title-message").text(
        textStatus + " [" + jqXHR.status + "]: " + errorThrown
      );
      setTimeout(function () {
        $("#modal-message").modal("hide");
      }, 1700);

      //Message for developer
      console.log(jqXHR.responseText);
    },
  });
}

function booking(id) {
  $("#form-booking")[0].reset();
  $(".form-group").removeClass("has-error");
  $(".form-group").removeClass("has-success");
  $(".help-block").empty();

  $('[name="booking-ID_Pedido_Cabecera"]').val(id);

  $(" .modal-booking ").modal("show");
  $(" #form-booking ")[0].reset();

  url = base_url + "AgenteCompra/PedidosPagados/getBooking/" + id;
  $.ajax({
    url: url,
    type: "GET",
    dataType: "JSON",
    success: function (response) {
      console.log(response);

      $('[name="booking-Qt_Caja_Total_Booking"]').val(
        response.Qt_Caja_Total_Booking
      );
      $('[name="booking-Qt_Cbm_Total_Booking"]').val(
        response.Qt_Cbm_Total_Booking
      );
      $('[name="booking-Qt_Peso_Total_Booking"]').val(
        response.Qt_Peso_Total_Booking
      );
    },
    error: function (jqXHR, textStatus, errorThrown) {
      //$( '#modal-loader' ).modal('hide');
      $(".modal-message").removeClass(
        "modal-danger modal-warning modal-success"
      );

      $("#modal-message").modal("show");
      $(".modal-message").addClass("modal-danger");
      $(".modal-title-message").text(
        textStatus + " [" + jqXHR.status + "]: " + errorThrown
      );
      setTimeout(function () {
        $("#modal-message").modal("hide");
      }, 1700);

      //Message for developer
      console.log(jqXHR.responseText);
    },
  });
}

function descargarInvoicePlProveedor(id) {
  url =
    base_url + "AgenteCompra/PedidosPagados/descargarInvoicePlProveedor/" + id;

  var popupwin = window.open(url);
  setTimeout(function () {
    popupwin.close();
  }, 2000);
}

function despacho(id, sCorrelativo) {
  $('[name="despacho-id_cabecera"]').val(id);
  $('[name="despacho-correlativo"]').val(sCorrelativo);

  $("#modal-fecha_entrega_shipper").modal("show");
  $("#form-fecha_entrega_shipper")[0].reset();
}

//chat de novedades de producto
function asignarPedido(ID_Pedido_Cabecera, Nu_Estado) {
  /*
  if(Nu_Estado!=3) {//3 - Enviado
    $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
    $('#modal-message').modal('show');

    $('#moda-message-content').addClass( 'bg-warning');
    $('.modal-title-message').html('Primero el estado debe ser <strong>ENVIADO</strong> para asignar.');

    setTimeout(function () { $('#modal-message').modal('hide'); }, 3100);
  } else {
    */
  $("#txt-guardar_personal_china-ID_Pedido_Cabecera").val(ID_Pedido_Cabecera);
  $(".modal-guardar_personal_china").modal("show");

  $("#cbo-guardar_personal_china-ID_Usuario").html(
    '<option value="0" selected="selected">Buscando...</option>'
  );
  //url = base_url + 'HelperImportacionController/getUsuarioChina';
  url = base_url + "HelperImportacionController/getUsuarioJefeChina";
  $.post(
    url,
    {},
    function (response) {
      console.log(response);
      if (response.status == "success") {
        $("#cbo-guardar_personal_china-ID_Usuario").html(
          '<option value="0" selected="selected">- Seleccionar -</option>'
        );
        var l = response.result.length;
        for (var x = 0; x < l; x++) {
          $("#cbo-guardar_personal_china-ID_Usuario").append(
            '<option value="' +
              response.result[x].ID +
              '">' +
              response.result[x].Nombre +
              "</option>"
          );
        }
      } else {
        $("#cbo-guardar_personal_china-ID_Usuario").html(
          '<option value="0" selected="selected">Sin registro</option>'
        );
        if (response.sMessageSQL !== undefined) {
          console.log(response.sMessageSQL);
        }
        console.log(response.message);
      }
    },
    "JSON"
  );
  //}
}

function removerAsignarPedido(ID, id_usuario) {
  var $modal_delete = $("#modal-message-delete");
  $modal_delete.modal("show");

  $(".modal-message-delete").removeClass(
    "modal-danger modal-warning modal-success"
  );
  $(".modal-message-delete").addClass("modal-success");

  $("#modal-title").text("¿Deseas quitar asignación Nro. Pedido " + ID + " ?");

  $("#btn-save-delete")
    .off("click")
    .click(function () {
      $("#btn-save-delete").text("");
      $("#btn-save-delete").attr("disabled", true);
      $("#btn-save-delete").html(
        'Guardando <div class="spinner-border" role="status"><span class="sr-only"></span></div>'
      );

      url =
        base_url +
        "AgenteCompra/PedidosPagados/removerAsignarPedido/" +
        ID +
        "/" +
        id_usuario;
      $.ajax({
        url: url,
        type: "GET",
        dataType: "JSON",
        success: function (response) {
          $modal_delete.modal("hide");
          $("#btn-save-delete").text("");
          $("#btn-save-delete").append("Aceptar");
          $("#btn-save-delete").attr("disabled", false);

          $("#moda-message-content").removeClass(
            "bg-danger bg-warning bg-success"
          );
          $("#modal-message").modal("show");

          if (response.status == "success") {
            $("#moda-message-content").addClass("bg-" + response.status);
            $(".modal-title-message").text(response.message);
            setTimeout(function () {
              $("#modal-message").modal("hide");
            }, 1100);
            reload_table_Entidad();
          } else {
            $("#moda-message-content").addClass("bg-danger");
            $(".modal-title-message").text(response.message);
            setTimeout(function () {
              $("#modal-message").modal("hide");
            }, 2100);
          }
        },
      });
    });
}

function completarVerificacionOC(ID, iIdTareaPedido) {
  var $modal_delete = $("#modal-message-delete");
  $modal_delete.modal("show");

  $(".modal-message-delete").removeClass(
    "modal-danger modal-warning modal-success"
  );
  $(".modal-message-delete").addClass("modal-success");

  $("#modal-title").html("¿Deseas <strong>completar</strong> tarea?");

  $("#btn-cancel-delete")
    .off("click")
    .click(function () {
      $modal_delete.modal("hide");
    });

  $("#btn-save-delete")
    .off("click")
    .click(function () {
      $("#btn-save-delete").text("");
      $("#btn-save-delete").attr("disabled", true);
      $("#btn-save-delete").append(
        'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>'
      );

      url =
        base_url +
        "AgenteCompra/PedidosPagados/completarVerificacionOC/" +
        ID +
        "/" +
        iIdTareaPedido;
      $.ajax({
        url: url,
        type: "GET",
        dataType: "JSON",
        success: function (response) {
          $modal_delete.modal("hide");

          $("#btn-save-delete").text("");
          $("#btn-save-delete").append("Aceptar");
          $("#btn-save-delete").attr("disabled", false);

          $(".modal-message").removeClass(
            "modal-danger modal-warning modal-success"
          );
          $("#modal-message").modal("show");

          if (response.status == "success") {
            $(".modal-message").addClass(response.style_modal);
            $(".modal-title-message").text(response.message);
            setTimeout(function () {
              $("#modal-message").modal("hide");
            }, 1100);
            reload_table_Entidad();
          } else {
            $(".modal-message").addClass(response.style_modal);
            $(".modal-title-message").text(response.message);
            setTimeout(function () {
              $("#modal-message").modal("hide");
            }, 1500);
          }
        },
      });
    });
}

function pagarProveedores(ID, tipo_pago) {
  $(".div-Listar").hide();

  $("#form-pedido")[0].reset();
  $(".form-group").removeClass("has-error");
  $(".form-group").removeClass("has-success");
  $(".help-block").empty();

  $(".div-Pago_Proveedor").show();
  $("#table-Pago_Proveedor tbody").empty();
  $("#table-Pago_Proveedor").show();

  $(".div-Compuesto").hide();
  $("#table-Producto_Enlace tbody").empty();

  $(".div-Producto_Recepcion_Carga").hide();
  $("#table-Producto_Recepcion_Carga tbody").empty();

  $(".div-Invoice_Proveedor").hide();
  $("#table-Invoice_Proveedor tbody").empty();

  //$('#span-id_pedido').html('Nro. ' + ID);

  url = base_url + "AgenteCompra/PedidosPagados/ajax_edit/" + ID;
  $.ajax({
    url: url,
    type: "GET",
    dataType: "JSON",
    success: function (response) {
      console.log(response);
      var detalle = response;
      response = response[0];

      $("#span-id_pedido").html(response.sCorrelativoCotizacion);

      $(".div-AgregarEditar").show();

      $('[name="EID_Pedido_Cabecera"]').val(response.ID_Pedido_Cabecera);
      $('[name="EID_Entidad"]').val(response.ID_Entidad);
      $('[name="EID_Empresa"]').val(response.ID_Empresa);
      $('[name="EID_Organizacion"]').val(response.ID_Organizacion);

      $('[name="No_Contacto"]').val(response.No_Contacto);
      $('[name="Txt_Email_Contacto"]').val(response.Txt_Email_Contacto);
      $('[name="Nu_Celular_Contacto"]').val(response.Nu_Celular_Contacto);
      $('[name="No_Entidad"]').val(response.No_Entidad);
      $('[name="Nu_Documento_Identidad"]').val(response.Nu_Documento_Identidad);

      //$( '#btn-excel_order_tracking' ).data('id_pedido', response.ID_Pedido_Cabecera);
      $("#btn-excel_order_tracking").attr(
        "data-id_pedido",
        response.ID_Pedido_Cabecera
      ); // sets

      $("#btn-descargar_pago_30").hide();
      $("#span-pago_30").html("");
      if (
        response.Txt_Url_Pago_30_Cliente != "" &&
        response.Txt_Url_Pago_30_Cliente != null
      ) {
        $("#btn-descargar_pago_30").show();
        $("#btn-descargar_pago_30").removeClass("d-none");

        $("#span-pago_30").html("$ " + response.Ss_Pago_30_Cliente);
      }

      $("#btn-descargar_pago_100").hide();
      $("#span-pago_100").html("");
      if (
        response.Txt_Url_Pago_100_Cliente != "" &&
        response.Txt_Url_Pago_100_Cliente != null
      ) {
        $("#btn-descargar_pago_100").show();
        $("#btn-descargar_pago_100").removeClass("d-none");

        $("#span-pago_100").html("$ " + response.Ss_Pago_100_Cliente);
      }

      $("#btn-descargar_pago_servicio").hide();
      $("#span-pago_servicio").html("");
      if (
        response.Txt_Url_Pago_Servicio_Cliente != "" &&
        response.Txt_Url_Pago_Servicio_Cliente != null
      ) {
        $("#btn-descargar_pago_servicio").show();
        $("#btn-descargar_pago_servicio").removeClass("d-none");

        $("#span-pago_servicio").html("$ " + response.Ss_Pago_Servicio_Cliente);
      }

      $("#btn-descargar_flete").hide();
      $("#span-flete").html("");
      if (
        response.Txt_Url_Pago_Otros_Flete != "" &&
        response.Txt_Url_Pago_Otros_Flete != null
      ) {
        $("#btn-descargar_flete").show();
        $("#btn-descargar_flete").removeClass("d-none");

        $("#span-flete").html("$ " + response.Ss_Pago_Otros_Flete);
      }

      $("#btn-descargar_costo_origen").hide();
      $("#span-costo_origen").html("");
      if (
        response.Txt_Url_Pago_Otros_Costo_Origen != "" &&
        response.Txt_Url_Pago_Otros_Costo_Origen != null
      ) {
        $("#btn-descargar_costo_origen").show();
        $("#btn-descargar_costo_origen").removeClass("d-none");

        $("#span-costo_origen").html(
          "$ " + response.Ss_Pago_Otros_Costo_Origen
        );
      }

      $("#btn-descargar_fta").hide();
      $("#span-fta").html("");
      if (
        response.Txt_Url_Pago_Otros_Costo_Fta != "" &&
        response.Txt_Url_Pago_Otros_Costo_Fta != null
      ) {
        $("#btn-descargar_fta").show();
        $("#btn-descargar_fta").removeClass("d-none");

        $("#span-fta").html("$ " + response.Ss_Pago_Otros_Costo_Fta);
      }

      $("#btn-descargar_pago_cuadrilla").hide();
      $("#span-cuadrilla").html("");
      if (
        response.Txt_Url_Pago_Otros_Cuadrilla != "" &&
        response.Txt_Url_Pago_Otros_Cuadrilla != null
      ) {
        $("#btn-descargar_pago_cuadrilla").show();
        $("#btn-descargar_pago_cuadrilla").removeClass("d-none");

        $("#span-cuadrilla").html("$ " + response.Ss_Pago_Otros_Cuadrilla);
      }

      $("#btn-descargar_otros_costos").hide();
      $("#span-otros_costo").html("");
      if (
        response.Txt_Url_Pago_Otros_Costos != "" &&
        response.Txt_Url_Pago_Otros_Costos != null
      ) {
        $("#btn-descargar_otros_costos").show();
        $("#btn-descargar_otros_costos").removeClass("d-none");

        $("#span-otros_costo").html("$ " + response.Ss_Pago_Otros_Costos);
      }

      var sNombreEstado =
        '<span class="badge badge-pill badge-secondary">Pendiente</span>';
      if (response.Nu_Estado_Pedido == 2)
        sNombreEstado =
          '<span class="badge badge-pill badge-primary">Confirmado</span>';
      else if (response.Nu_Estado_Pedido == 3)
        sNombreEstado =
          '<span class="badge badge-pill badge-success">Entregado</span>';
      else if (response.Nu_Estado_Pedido == 4)
        sNombreEstado =
          '<span class="badge badge-pill badge-danger">Confirmado</span>';
      $("#div-estado").html(sNombreEstado);

      var iCounterSupplier = 1,
        table_enlace_producto = "",
        iDiasVencimiento = 0,
        sClassColorTr = "",
        fTotalCliente = 0,
        ID_Entidad = "";
      $("#btn-save_proveedor").hide();
      for (i = 0; i < detalle.length; i++) {
        var cantidad_item_final_recepcion_carga = parseFloat(
          detalle[i]["Qt_Producto_Caja_Final_Verificada"]
        );
        var cantidad_item = parseFloat(detalle[i]["Qt_Producto"]);
        var precio_china = parseFloat(detalle[i]["Ss_Precio"]);
        var voucher_1 = detalle[i]["Txt_Url_Archivo_Pago_1_Proveedor"];

        fTotalCliente +=
          cantidad_item * (precio_china * parseFloat(response.Ss_Tipo_Cambio));

        var id_item = detalle[i]["ID_Pedido_Detalle_Producto_Proveedor"];
        var voucher_1 = detalle[i]["Txt_Url_Archivo_Pago_1_Proveedor"];
        var voucher_2 = detalle[i]["Txt_Url_Archivo_Pago_2_Proveedor"];
        //max-height: 350px;width: 100%; cursor:pointer

        var fTotal = cantidad_item * precio_china;
        var Ss_Pago_1_Proveedor = parseFloat(detalle[i]["Ss_Pago_1_Proveedor"]);
        var Ss_Pago_2_Proveedor = parseFloat(detalle[i]["Ss_Pago_2_Proveedor"]);

        sClassColorTr = "";
        iDiasVencimiento = 0;
        if (
          detalle[i]["Fe_Entrega_Proveedor"] != "" &&
          detalle[i]["Fe_Entrega_Proveedor"] != null
        ) {
          var fechaInicio = new Date(
            fYear + "-" + fMonth + "-" + fDay
          ).getTime();
          var fechaFin = new Date(detalle[i]["Fe_Entrega_Proveedor"]).getTime();

          var diff = fechaFin - fechaInicio;
          iDiasVencimiento = diff / (1000 * 60 * 60 * 24); // --> milisegundos -> segundos -> minutos -> horas -> días
          if (iDiasVencimiento < 5) sClassColorTr = "table-warning";
        }

        var fecha_entrega_proveedor =
          detalle[i]["Fe_Entrega_Proveedor"] != "" &&
          detalle[i]["Fe_Entrega_Proveedor"] != null
            ? ParseDateString(
                detalle[i]["Fe_Entrega_Proveedor"],
                "fecha_bd",
                "-"
              )
            : "";

        var nota_final =
          detalle[i]["Txt_Nota_Recepcion_Carga_Proveedor"] != "" &&
          detalle[i]["Txt_Nota_Recepcion_Carga_Proveedor"] != null
            ? detalle[i]["Txt_Nota_Recepcion_Carga_Proveedor"]
            : "";

        if (ID_Entidad != detalle[i].ID_Entidad_Proveedor) {
          table_enlace_producto += "<tr>";
          //table_enlace_producto += "<th class='text-left'>" + iCounterSupplier + ". " + detalle[i].No_Contacto_Proveedor + "</th>";
          table_enlace_producto += "<th class='text-left'>";
          table_enlace_producto +=
            iCounterSupplier + ". " + detalle[i].No_Contacto_Proveedor;
          table_enlace_producto +=
            '<button type="button" class="btn btn-xs btn-link" alt="Modificar" title="Modificar" href="javascript:void(0)" onclick="editarProveedor(' +
            detalle[i].ID_Entidad_Proveedor +
            ", " +
            id_item +
            ')">&nbsp;<i class="far fa-edit" aria-hidden="true"></i></button>';
          table_enlace_producto += "</th>";
          //table_enlace_producto += "<th class='text-left'>" + detalle[i].No_Cuenta_Bancaria + "</th>";

          if (tipo_pago == 1) {
            table_enlace_producto +=
              "<th class='text-left'>" + detalle[i].Ss_Pago_Importe_1 + "</th>";
          } else if (tipo_pago == 2) {
            table_enlace_producto +=
              "<th class='text-left'>" + detalle[i].Ss_Pago_Importe_2 + "</th>";
          }

          if (tipo_pago == 1) {
            table_enlace_producto += "<td class='text-left'>";
            if (voucher_1 == "" || voucher_1 == null) {
              table_enlace_producto +=
                '<button type="button" id="btn-agregar_pago_proveedor' +
                id_item +
                '" data-tipo_pago="1" data-id="' +
                id_item +
                '" class="text-left btn btn-primary btn-block btn-agregar_pago_proveedor" data-id_empresa="' +
                response.ID_Empresa +
                '" data-id_organizacion="' +
                response.ID_Organizacion +
                '" data-id_pedido_cabecera="' +
                response.ID_Pedido_Cabecera +
                '" data-id_pedido_detalle="' +
                response.ID_Pedido_Detalle +
                '" data-correlativo="' +
                response.sCorrelativoCotizacion +
                '"><i class="fas fa-money-bill-alt"></i>&nbsp; Pagar Proveedor (Deposit_#1)</button>';
            } else {
              //table_enlace_producto += '<button type="button" id="btn-ver_pago_proveedor' + id_item + '" data-url_img="' + voucher_1 + '" data-id="' + id_item + '" class="text-left btn btn-secondary btn-block btn-ver_pago_proveedor" data-id_empresa="' + response.ID_Empresa + '" data-id_organizacion="' + response.ID_Organizacion + '" data-id_pedido_cabecera="' + response.ID_Pedido_Cabecera + '" data-id_pedido_detalle="' + response.ID_Pedido_Detalle + '" data-correlativo="' + response.sCorrelativoCotizacion + '"><i class="fas fa-money-bill-alt"></i>&nbsp; Pago ¥ ' + detalle[i].Ss_Pago_Importe_1 +  ' (Deposit_#1)</button>';
              table_enlace_producto +=
                '<button type="button" id="btn-ver_pago_proveedor' +
                id_item +
                '" data-url_img="' +
                voucher_1 +
                '" data-id="' +
                id_item +
                '" class="text-left btn btn-secondary btn-block btn-ver_pago_proveedor" data-id_empresa="' +
                response.ID_Empresa +
                '" data-id_organizacion="' +
                response.ID_Organizacion +
                '" data-id_pedido_cabecera="' +
                response.ID_Pedido_Cabecera +
                '" data-id_pedido_detalle="' +
                response.ID_Pedido_Detalle +
                '" data-correlativo="' +
                response.sCorrelativoCotizacion +
                '"><i class="fas fa-money-bill-alt"></i>&nbsp; Pagado</button>';
            }
            table_enlace_producto += "</td>";
          } else if (tipo_pago == 2) {
            table_enlace_producto += "<td class='text-left'>";
            if (voucher_2 == "" || voucher_2 == null) {
              table_enlace_producto +=
                '<button type="button" id="btn-agregar_pago_proveedor' +
                id_item +
                '" data-tipo_pago="2" data-id="' +
                id_item +
                '" class="text-left btn btn-primary btn-block btn-agregar_pago_proveedor" data-id_empresa="' +
                response.ID_Empresa +
                '" data-id_organizacion="' +
                response.ID_Organizacion +
                '" data-id_pedido_cabecera="' +
                response.ID_Pedido_Cabecera +
                '" data-id_pedido_detalle="' +
                response.ID_Pedido_Detalle +
                '" data-correlativo="' +
                response.sCorrelativoCotizacion +
                '"><i class="fas fa-money-bill-alt"></i>&nbsp; Pagar Proveedor (Deposit_#2)</button>';
            } else {
              //table_enlace_producto += '<button type="button" id="btn-ver_pago_proveedor' + id_item + '" data-url_img="' + voucher_2 + '" data-id="' + id_item + '" class="text-left btn btn-secondary btn-block btn-ver_pago_proveedor" data-id_empresa="' + response.ID_Empresa + '" data-id_organizacion="' + response.ID_Organizacion + '" data-id_pedido_cabecera="' + response.ID_Pedido_Cabecera + '" data-id_pedido_detalle="' + response.ID_Pedido_Detalle + '" data-correlativo="' + response.sCorrelativoCotizacion + '"><i class="fas fa-money-bill-alt"></i>&nbsp; Pago ¥ ' + detalle[i].Ss_Pago_Importe_2 + ' (Deposit_#2)</button>';
              table_enlace_producto +=
                '<button type="button" id="btn-ver_pago_proveedor' +
                id_item +
                '" data-url_img="' +
                voucher_2 +
                '" data-id="' +
                id_item +
                '" class="text-left btn btn-secondary btn-block btn-ver_pago_proveedor" data-id_empresa="' +
                response.ID_Empresa +
                '" data-id_organizacion="' +
                response.ID_Organizacion +
                '" data-id_pedido_cabecera="' +
                response.ID_Pedido_Cabecera +
                '" data-id_pedido_detalle="' +
                response.ID_Pedido_Detalle +
                '" data-correlativo="' +
                response.sCorrelativoCotizacion +
                '"><i class="fas fa-money-bill-alt"></i>&nbsp; Pagado</button>';
            }
            table_enlace_producto += "</td>";
          }
          +"</tr>";
          ID_Entidad = detalle[i].ID_Entidad_Proveedor;
          ++iCounterSupplier;
        }
      }

      /*
      var table_enlace_producto = "", iDiasVencimiento = 0, sClassColorTr = "", fTotalCliente = 0, ID_Entidad = 0;
      for (i = 0; i < detalle.length; i++) {
        var cantidad_item = parseFloat(detalle[i]['Qt_Producto']);
        var precio_china = parseFloat(detalle[i]['Ss_Precio']);

        fTotalCliente += (cantidad_item * (precio_china * parseFloat(response.Ss_Tipo_Cambio)));

        var id_item = detalle[i]['ID_Pedido_Detalle_Producto_Proveedor'];
        var voucher_1 = detalle[i]['Txt_Url_Archivo_Pago_1_Proveedor'];
        var voucher_2 = detalle[i]['Txt_Url_Archivo_Pago_2_Proveedor'];
        //max-height: 350px;width: 100%; cursor:pointer

        var fTotal = (cantidad_item * precio_china);
        var Ss_Pago_1_Proveedor = parseFloat(detalle[i]['Ss_Pago_1_Proveedor']);
        var Ss_Pago_2_Proveedor = parseFloat(detalle[i]['Ss_Pago_2_Proveedor']);

        sClassColorTr = '';
        iDiasVencimiento = 0;
        if((detalle[i]['Fe_Entrega_Proveedor'] != '' && detalle[i]['Fe_Entrega_Proveedor'] != null)){
          var fechaInicio = new Date(fYear + '-' + fMonth + '-' + fDay).getTime();
          var fechaFin    = new Date(detalle[i]['Fe_Entrega_Proveedor']).getTime();

          var diff = fechaFin - fechaInicio;
          iDiasVencimiento = (diff / (1000*60*60*24));// --> milisegundos -> segundos -> minutos -> horas -> días
          if(iDiasVencimiento<5)
            sClassColorTr = 'table-warning';
        }

        var fecha_entrega_proveedor = ( (detalle[i]['Fe_Entrega_Proveedor'] != '' && detalle[i]['Fe_Entrega_Proveedor'] != null) ? ParseDateString(detalle[i]['Fe_Entrega_Proveedor'], 'fecha_bd', '-') : '');

        if (ID_Entidad != detalle[i].ID_Entidad_Proveedor) {
          table_enlace_producto +=
          "<tr>"
            +"<th class='text-right'>Supplier </th>"
            +"<th class='text-left' colspan='14'>" + detalle[i].No_Contacto_Proveedor + "</th>"
          +"</tr>";
          ID_Entidad = detalle[i].ID_Entidad_Proveedor;
        }

        table_enlace_producto +=
        "<tr id='tr_enlace_producto" + id_item + "'>"
          + "<td style='display:none;' class='text-left td-id_item'>" + id_item + "</td>"
          + "<td class='text-center td-name' width='50%'>"
            + "<img style='' data-id_item='" + id_item + "' data-url_img='" + detalle[i]['Txt_Url_Imagen_Producto'] + "' src='" + detalle[i]['Txt_Url_Imagen_Producto'] + "' alt='" + detalle[i]['Txt_Producto'] + "' class='img-thumbnail img-table_item img-fluid img-resize mb-2'>";

          table_enlace_producto += "</td>"
          + "<td class='text-left td-name'>" + detalle[i]['Txt_Producto'] + "</td>"
          + "<td class='text-right td-qty'>" + Math.round10(cantidad_item, -2) + "</td>"
          + "<td class='text-right td-price'>" + Math.round10(precio_china, -2) + "</td>"
          +"<td class='text-right td-amount'>" + Math.round10(fTotal, -2) + "</td>"
          +"<td class='text-right td-pay1'>" + Math.round10(Ss_Pago_1_Proveedor, -2) + "</td>"
          +"<td class='text-right td-balance'>" + Math.round10(fTotal - Ss_Pago_1_Proveedor, -2) + "</td>"
          +"<td class='text-right td-pay2'>" + Math.round10(Ss_Pago_2_Proveedor, -2) + "</td>"
          +"<td class='text-left td-delivery_date'>" + detalle[i]['Nu_Dias_Delivery'] + "</td>"
          +"<td class='text-left td-costo_delivery'>" + detalle[i]['Ss_Costo_Delivery'] + "</td>";

          table_enlace_producto += "<td class='text-left td-supplier'>";
            table_enlace_producto += '<div class="input-group date" style="width:100%">';
              table_enlace_producto += '<input type="text" id="txt-fecha_entrega_proveedor'+i+'" name="addProducto[' + id_item + '][fecha_entrega_proveedor]" class="form-control input-datepicker-today-to-more required" value="' + fecha_entrega_proveedor + '">';
            table_enlace_producto += '</div>';
          table_enlace_producto += "</td>";

          table_enlace_producto += "<td class='text-left td-supplier'>" + detalle[i]['No_Contacto_Proveedor'] + "</td>"
          +"<td class='text-left td-phone'>";
          if(detalle[i]['Txt_Url_Imagen_Proveedor'] != '' && detalle[i]['Txt_Url_Imagen_Proveedor'] != null){
            table_enlace_producto += "<img style='' data-id_item='" + id_item + "' data-url_img='" + detalle[i]['Txt_Url_Imagen_Proveedor'] + "' src='" + detalle[i]['Txt_Url_Imagen_Proveedor'] + "' alt='" + detalle[i]['Txt_Producto'] + "' class='img-thumbnail img-table_item img-fluid img-resize mb-2'>";
          }
          table_enlace_producto += "</td>";

          table_enlace_producto += "<td class='text-left td-eliminar'>";
            table_enlace_producto += '<button type="button" id="btn-eliminar_item_proveedor' + id_item + '" data-name_item="' + detalle[i]['Txt_Producto'] + '" data-id_pedido_cabecera="' + response.ID_Pedido_Cabecera + '" data-id="' + id_item + '" data-correlativo="' + response.sCorrelativoCotizacion + '" class="text-left btn btn-danger btn-block btn-eliminar_item_proveedor"> X </button>';
          table_enlace_producto += "</td>";

          table_enlace_producto += '<input type="hidden" name="addProducto[' + id_item + '][id_item]" value="' + id_item + '">';
        table_enlace_producto += "</tr>";

        table_enlace_producto +=
        "<tr><td class='text-left' colspan='14'>"
          if( voucher_1 == '' || voucher_1 == null ){
            table_enlace_producto += '<button type="button" id="btn-agregar_pago_proveedor' + id_item + '" data-tipo_pago="1" data-id="' + id_item + '" class="text-left btn btn-primary btn-block btn-agregar_pago_proveedor" data-id_empresa="' + response.ID_Empresa + '" data-id_organizacion="' + response.ID_Organizacion + '" data-id_pedido_cabecera="' + response.ID_Pedido_Cabecera + '" data-id_pedido_detalle="' + response.ID_Pedido_Detalle + '" data-correlativo="' + response.sCorrelativoCotizacion + '"><i class="fas fa-money-bill-alt"></i>&nbsp; Pagar Proveedor</button>';
          } else {
            table_enlace_producto += '<button type="button" id="btn-ver_pago_proveedor' + id_item + '" data-url_img="' + voucher_1 + '" data-id="' + id_item + '" class="text-left btn btn-secondary btn-block btn-ver_pago_proveedor" data-id_empresa="' + response.ID_Empresa + '" data-id_organizacion="' + response.ID_Organizacion + '" data-id_pedido_cabecera="' + response.ID_Pedido_Cabecera + '" data-id_pedido_detalle="' + response.ID_Pedido_Detalle + '" data-correlativo="' + response.sCorrelativoCotizacion + '"><i class="fas fa-money-bill-alt"></i>&nbsp; Pago ¥ ' + Ss_Pago_1_Proveedor +  ' (Deposit_#1)</button>';
            if( voucher_2 == '' || voucher_2 == null ){
              table_enlace_producto += '<button type="button" id="btn-agregar_pago_proveedor' + id_item + '" data-tipo_pago="2" data-id="' + id_item + '" class="text-left btn btn-primary btn-block btn-agregar_pago_proveedor" data-id_empresa="' + response.ID_Empresa + '" data-id_organizacion="' + response.ID_Organizacion + '" data-id_pedido_cabecera="' + response.ID_Pedido_Cabecera + '" data-id_pedido_detalle="' + response.ID_Pedido_Detalle + '" data-correlativo="' + response.sCorrelativoCotizacion + '"><i class="fas fa-money-bill-alt"></i>&nbsp; Pagar Proveedor</button>';
            } else {
              table_enlace_producto += '<button type="button" id="btn-ver_pago_proveedor' + id_item + '" data-url_img="' + voucher_2 + '" data-id="' + id_item + '" class="text-left btn btn-secondary btn-block btn-ver_pago_proveedor" data-id_empresa="' + response.ID_Empresa + '" data-id_organizacion="' + response.ID_Organizacion + '" data-id_pedido_cabecera="' + response.ID_Pedido_Cabecera + '" data-id_pedido_detalle="' + response.ID_Pedido_Detalle + '" data-correlativo="' + response.sCorrelativoCotizacion + '"><i class="fas fa-money-bill-alt"></i>&nbsp; Pago ¥ ' + Ss_Pago_2_Proveedor + ' (Deposit_#2)</button>';
            }
          }
        table_enlace_producto +=
        "</td></tr>";
      }
      */

      $("#span-total_cantidad_items").html(i);
      $("#table-Pago_Proveedor").append(table_enlace_producto);

      $("#span-total_cliente").html("$ " + fTotalCliente.toFixed(2));

      //PAGOS
      //Ss_Pago_30_Cliente
      //Ss_Pago_100_Cliente
      //Ss_Pago_Servicio_Cliente

      //OTROS
      //Ss_Pago_Otros_Flete
      //Ss_Pago_Otros_Costo_Origen
      //Ss_Pago_Otros_Costo_Fta
      //Ss_Pago_Otros_Cuadrilla
      //Ss_Pago_Otros_Costos
      $("#span-saldo_cliente").html(
        "$ " +
          (fTotalCliente -
            (parseFloat(response.Ss_Pago_30_Cliente) +
              parseFloat(response.Ss_Pago_100_Cliente) +
              parseFloat(response.Ss_Pago_Servicio_Cliente)))
      );

      //Date picker invoice
      $(".input-datepicker-today-to-more").datepicker({
        autoclose: true,
        startDate: new Date(fYear, fToday.getMonth(), fDay),
        todayHighlight: true,
        dateFormat: "dd/mm/yyyy",
        format: "dd/mm/yyyy",
      });
    },
    error: function (jqXHR, textStatus, errorThrown) {
      $(".modal-message").removeClass(
        "modal-danger modal-warning modal-success"
      );

      $("#modal-message").modal("show");
      $(".modal-message").addClass("modal-danger");
      $(".modal-title-message").text(
        textStatus + " [" + jqXHR.status + "]: " + errorThrown
      );
      setTimeout(function () {
        $("#modal-message").modal("hide");
      }, 1700);

      //Message for developer
      console.log(jqXHR.responseText);
    },
  });
}

function bookingConsolidado(id) {
  $("#form-booking_consolidado")[0].reset();
  $(".form-group").removeClass("has-error");
  $(".form-group").removeClass("has-success");
  $(".help-block").empty();

  $('[name="booking_consolidado-ID_Pedido_Cabecera"]').val(id);

  $(" .modal-booking_consolidado ").modal("show");

  url = base_url + "AgenteCompra/PedidosPagados/getBooking/" + id;
  $.ajax({
    url: url,
    type: "GET",
    dataType: "JSON",
    success: function (response) {
      console.log(response);
      $('[name="booking_consolidado-No_Numero_Consolidado"]').val(
        response.No_Numero_Consolidado
      );
      $("#booking_consolidado-Qt_Cbm_Total_Booking").html(
        response.Qt_Cbm_Total_Booking
      );
    },
    error: function (jqXHR, textStatus, errorThrown) {
      //$( '#modal-loader' ).modal('hide');
      $(".modal-message").removeClass(
        "modal-danger modal-warning modal-success"
      );

      $("#modal-message").modal("show");
      $(".modal-message").addClass("modal-danger");
      $(".modal-title-message").text(
        textStatus + " [" + jqXHR.status + "]: " + errorThrown
      );
      setTimeout(function () {
        $("#modal-message").modal("hide");
      }, 1700);

      //Message for developer
      console.log(jqXHR.responseText);
    },
  });
}

function bookingInspeccion(
  id,
  iIdTareaPedido,
  ID_Usuario_Interno_China,
  sCorrelativoCotizacion
) {
  $("#form-booking_inspeccion")[0].reset();
  $(".form-group").removeClass("has-error");
  $(".form-group").removeClass("has-success");
  $(".help-block").empty();

  $('[name="booking_inspeccion-ID_Pedido_Cabecera"]').val(id);
  $('[name="booking_inspeccion-Nu_ID_Interno"]').val(iIdTareaPedido);
  $('[name="booking_inspeccion-ID_Usuario_Interno_China"]').val(
    ID_Usuario_Interno_China
  );
  $('[name="booking_inspeccion-sCorrelativoCotizacion"]').val(
    sCorrelativoCotizacion
  );

  $(" .modal-booking_inspeccion ").modal("show");

  url = base_url + "AgenteCompra/PedidosPagados/getBooking/" + id;
  $.ajax({
    url: url,
    type: "GET",
    dataType: "JSON",
    success: function (response) {
      console.log(response);

      $('[name="booking_inspeccion-Qt_Caja_Total_Booking-Actual"]').val(
        response.Qt_Caja_Total_Booking
      );
      $('[name="booking_inspeccion-Qt_Cbm_Total_Booking-Actual"]').val(
        response.Qt_Cbm_Total_Booking
      );
      $('[name="booking_inspeccion-Qt_Peso_Total_Booking-Actual"]').val(
        response.Qt_Peso_Total_Booking
      );

      $('[name="booking_inspeccion-Qt_Caja_Total_Booking"]').val(
        response.Qt_Caja_Total_Booking
      );
      $('[name="booking_inspeccion-Qt_Cbm_Total_Booking"]').val(
        response.Qt_Cbm_Total_Booking
      );
      $('[name="booking_inspeccion-Qt_Peso_Total_Booking"]').val(
        response.Qt_Peso_Total_Booking
      );

      $('[name="booking_inspeccion-No_Observacion_Inspeccion"]').val(
        response.No_Observacion_Inspeccion
      );

      //$( '#booking_inspeccion-Qt_Caja_Total_Booking' ).html(response.Qt_Caja_Total_Booking);
      //$( '#booking_inspeccion-Qt_Cbm_Total_Booking' ).html(response.Qt_Cbm_Total_Booking);
      //$( '#booking_inspeccion-Qt_Peso_Total_Booking' ).html(response.Qt_Peso_Total_Booking);
    },
    error: function (jqXHR, textStatus, errorThrown) {
      //$( '#modal-loader' ).modal('hide');
      $(".modal-message").removeClass(
        "modal-danger modal-warning modal-success"
      );

      $("#modal-message").modal("show");
      $(".modal-message").addClass("modal-danger");
      $(".modal-title-message").text(
        textStatus + " [" + jqXHR.status + "]: " + errorThrown
      );
      setTimeout(function () {
        $("#modal-message").modal("hide");
      }, 1700);

      //Message for developer
      console.log(jqXHR.responseText);
    },
  });
}

function supervisarContenedor(id, sCorrelativo) {
  $('[name="supervisar_llenado_contenedor-id_cabecera"]').val(id);
  $('[name="supervisar_llenado_contenedor-correlativo"]').val(sCorrelativo);

  $("#modal-supervisar_llenado_contenedor").modal("show");
  $("#form-supervisar_llenado_contenedor")[0].reset();
}

function bookingTrading(id) {
  $("#form-reserva_booking_trading")[0].reset();
  $(".form-group").removeClass("has-error");
  $(".form-group").removeClass("has-success");
  $(".help-block").empty();

  $('[name="reserva_booking_trading-ID_Pedido_Cabecera"]').val(id);

  $(" .modal-reserva_booking_trading ").modal("show");

  var selected = "";

  url = base_url + "AgenteCompra/PedidosPagados/getBooking/" + id;
  $.ajax({
    url: url,
    type: "GET",
    dataType: "JSON",
    success: function (response) {
      console.log(response);

      var sNombreTransporteMaritimo = "FCL";
      $(".div-tipo_contenedor").show();
      if (response.Nu_Tipo_Transporte_Maritimo == 2) {
        sNombreTransporteMaritimo = "LCL";
        $(".div-tipo_contenedor").hide();
      }

      $("#reserva_booking_trading-Qt_Cbm_Total_Booking").html(
        response.Qt_Cbm_Total_Booking
      );
      $("#reserva_booking_trading-Nu_Tipo_Transporte_Maritimo").html(
        sNombreTransporteMaritimo
      );

      $("#cbo-shipper").html(
        '<option value="0" selected="selected">Buscando...</option>'
      );
      url = base_url + "HelperImportacionController/getShipper";
      $.post(
        url,
        {},
        function (responseShipper) {
          console.log(responseShipper);
          if (responseShipper.status == "success") {
            $("#cbo-shipper").html(
              '<option value="0" selected="selected">- Seleccionar -</option>'
            );
            var l = responseShipper.result.length;
            for (var x = 0; x < l; x++) {
              selected = "";
              if (response.ID_Shipper == responseShipper.result[x].ID)
                selected = 'selected="selected"';
              $("#cbo-shipper").append(
                '<option value="' +
                  responseShipper.result[x].ID +
                  '" ' +
                  selected +
                  ">" +
                  responseShipper.result[x].Nombre +
                  "</option>"
              );
            }
          } else {
            $("#cbo-shipper").html(
              '<option value="0" selected="selected">Sin registro</option>'
            );
            if (responseShipper.sMessageSQL !== undefined) {
              console.log(responseShipper.sMessageSQL);
            }
            console.log(responseShipper.message);
          }
        },
        "JSON"
      );

      $('[name="reserva_booking_trading-No_Tipo_Contenedor"]').val(
        response.No_Tipo_Contenedor
      );
      $('[name="reserva_booking_trading-No_Naviera"]').val(response.No_Naviera);
      $('[name="reserva_booking_trading-No_Dias_Transito"]').val(
        response.No_Dias_Transito
      );
      $('[name="reserva_booking_trading-No_Dias_Libres"]').val(
        response.No_Dias_Libres
      );
    },
    error: function (jqXHR, textStatus, errorThrown) {
      //$( '#modal-loader' ).modal('hide');
      $(".modal-message").removeClass(
        "modal-danger modal-warning modal-success"
      );

      $("#modal-message").modal("show");
      $(".modal-message").addClass("modal-danger");
      $(".modal-title-message").text(
        textStatus + " [" + jqXHR.status + "]: " + errorThrown
      );
      setTimeout(function () {
        $("#modal-message").modal("hide");
      }, 1700);

      //Message for developer
      console.log(jqXHR.responseText);
    },
  });
}

function costosOrigenTradingChina(id, iIdTareaPedido) {
  $("#form-costos_origen_china")[0].reset();
  $(".form-group").removeClass("has-error");
  $(".form-group").removeClass("has-success");
  $(".help-block").empty();

  $(" .modal-costos_origen_china ").modal("show");

  $('[name="costos_origen_china-ID_Pedido_Cabecera"]').val(id);

  var selected = "";

  url = base_url + "AgenteCompra/PedidosPagados/getBooking/" + id;
  $.ajax({
    url: url,
    type: "GET",
    dataType: "JSON",
    success: function (response) {
      console.log(response);

      $('[name="costos_origen_china-Ss_Pago_Otros_Flete_China_Yuan"]').val(
        response.Ss_Pago_Otros_Flete_China_Yuan
      );
      $('[name="costos_origen_china-Ss_Pago_Otros_Flete_China_Dolar"]').val(
        response.Ss_Pago_Otros_Flete_China_Dolar
      );

      $(
        '[name="costos_origen_china-Ss_Pago_Otros_Costo_Origen_China_Yuan"]'
      ).val(response.Ss_Pago_Otros_Costo_Origen_China_Yuan);
      $(
        '[name="costos_origen_china-Ss_Pago_Otros_Costo_Origen_China_Dolar"]'
      ).val(response.Ss_Pago_Otros_Costo_Origen_China_Dolar);

      $('[name="costos_origen_china-Ss_Pago_Otros_Costo_Fta_China_Yuan"]').val(
        response.Ss_Pago_Otros_Costo_Fta_China_Yuan
      );
      $('[name="costos_origen_china-Ss_Pago_Otros_Costo_Fta_China_Dolar"]').val(
        response.Ss_Pago_Otros_Costo_Fta_China_Dolar
      );

      $('[name="costos_origen_china-Ss_Pago_Otros_Cuadrilla_China_Yuan"]').val(
        response.Ss_Pago_Otros_Cuadrilla_China_Yuan
      );
      $('[name="costos_origen_china-Ss_Pago_Otros_Cuadrilla_China_Dolar"]').val(
        response.Ss_Pago_Otros_Cuadrilla_China_Dolar
      );

      $('[name="costos_origen_china-Ss_Pago_Otros_Costos_China_Yuan"]').val(
        response.Ss_Pago_Otros_Costos_China_Yuan
      );
      $('[name="costos_origen_china-Ss_Pago_Otros_Costos_China_Dolar"]').val(
        response.Ss_Pago_Otros_Costos_China_Dolar
      );

      $('[name="costos_origen_china-No_Concepto_Pago_Cuadrilla"]').val(
        response.No_Concepto_Pago_Cuadrilla
      );
    },
    error: function (jqXHR, textStatus, errorThrown) {
      //$( '#modal-loader' ).modal('hide');
      $(".modal-message").removeClass(
        "modal-danger modal-warning modal-success"
      );

      $("#modal-message").modal("show");
      $(".modal-message").addClass("modal-danger");
      $(".modal-title-message").text(
        textStatus + " [" + jqXHR.status + "]: " + errorThrown
      );
      setTimeout(function () {
        $("#modal-message").modal("hide");
      }, 1700);

      //Message for developer
      console.log(jqXHR.responseText);
    },
  });
}

function docsExportacion(id, iIdTareaPedido) {
  $("#form-docs_exportacion")[0].reset();
  $(".form-group").removeClass("has-error");
  $(".form-group").removeClass("has-success");
  $(".help-block").empty();

  $(" .modal-docs_exportacion ").modal("show");

  $('[name="docs_exportacion-ID_Pedido_Cabecera"]').val(id);
  $('[name="docs_exportacion-iIdTareaPedido"]').val(iIdTareaPedido);

  var selected = "";

  url = base_url + "AgenteCompra/PedidosPagados/getBooking/" + id;
  $.ajax({
    url: url,
    type: "GET",
    dataType: "JSON",
    success: function (response) {
      console.log(response);

      $(".div-docs_shipper").hide();
      $(".div-bl").hide();
      if (response.Nu_Tipo_Incoterms == 3 || response.Nu_Tipo_Incoterms == 4) {
        $(".div-docs_shipper").show();
        $(".div-bl").show();
      }

      var url_dowloand = response.Txt_Url_Archivo_Exportacion_Docs_Shipper;
      url_dowloand = url_dowloand.replace("https://", "../../");
      url_dowloand = url_dowloand.replace("assets", "public_html/assets");
      $("#docs_exportacion-Txt_Url_Archivo_Exportacion_Docs_Shipper-a").attr(
        "href",
        url_dowloand
      );

      var url_dowloand =
        response.Txt_Url_Archivo_Exportacion_Commercial_Invoice;
      url_dowloand = url_dowloand.replace("https://", "../../");
      url_dowloand = url_dowloand.replace("assets", "public_html/assets");
      $(
        "#docs_exportacion-Txt_Url_Archivo_Exportacion_Commercial_Invoice-a"
      ).attr("href", url_dowloand);

      var url_dowloand = response.Txt_Url_Archivo_Exportacion_Packing_List;
      url_dowloand = url_dowloand.replace("https://", "../../");
      url_dowloand = url_dowloand.replace("assets", "public_html/assets");
      $("#docs_exportacion-Txt_Url_Archivo_Exportacion_Packing_List-a").attr(
        "href",
        url_dowloand
      );

      var url_dowloand = response.Txt_Url_Archivo_Exportacion_Bl;
      url_dowloand = url_dowloand.replace("https://", "../../");
      url_dowloand = url_dowloand.replace("assets", "public_html/assets");
      $("#docs_exportacion-Txt_Url_Archivo_Exportacion_Bl-a").attr(
        "href",
        url_dowloand
      );

      var url_dowloand = response.Txt_Url_Archivo_Exportacion_Fta;
      url_dowloand = url_dowloand.replace("https://", "../../");
      url_dowloand = url_dowloand.replace("assets", "public_html/assets");
      $("#docs_exportacion-Txt_Url_Archivo_Exportacion_Fta-a").attr(
        "href",
        url_dowloand
      );
      //falta descargar file
    },
    error: function (jqXHR, textStatus, errorThrown) {
      //$( '#modal-loader' ).modal('hide');
      $(".modal-message").removeClass(
        "modal-danger modal-warning modal-success"
      );

      $("#modal-message").modal("show");
      $(".modal-message").addClass("modal-danger");
      $(".modal-title-message").text(
        textStatus + " [" + jqXHR.status + "]: " + errorThrown
      );
      setTimeout(function () {
        $("#modal-message").modal("hide");
      }, 1700);

      //Message for developer
      console.log(jqXHR.responseText);
    },
  });
}

function despachoShipper(id, iIdTareaPedido) {
  $("#form-despacho_shipper")[0].reset();
  $(".form-group").removeClass("has-error");
  $(".form-group").removeClass("has-success");
  $(".help-block").empty();

  $('[name="despacho_shipper-ID_Pedido_Cabecera"]').val(id);

  $(" .modal-despacho_shipper ").modal("show");
  $(" #form-despacho_shipper ")[0].reset();
}

function revisionBL(id, iIdTareaPedido) {
  $("#form-revision_bl")[0].reset();
  $(".form-group").removeClass("has-error");
  $(".form-group").removeClass("has-success");
  $(".help-block").empty();

  $('[name="revision_bl-ID_Pedido_Cabecera"]').val(id);
  $('[name="revision_bl-iIdTareaPedido"]').val(iIdTareaPedido);

  $(" .modal-revision_bl ").modal("show");

  var selected = "";

  url = base_url + "AgenteCompra/PedidosPagados/getBookingEntidad/" + id;
  $.ajax({
    url: url,
    type: "GET",
    dataType: "JSON",
    success: function (response) {
      console.log(response);

      $('[name="revision_bl-ID_Entidad"]').val(response.ID_Entidad);
      $('[name="revision_bl-ENo_Entidad"]').val(response.No_Entidad);

      $('[name="revision_bl-No_Entidad"]').val(response.No_Entidad);
      $('[name="revision_bl-Nu_Documento_Identidad"]').val(
        response.Nu_Documento_Identidad
      );
      $('[name="revision_bl-Txt_Direccion_Entidad"]').val(
        response.Txt_Direccion_Entidad
      );

      var sNombreExportador = "ProBusiness Yiwu";
      if (response.Nu_Tipo_Exportador == 2) {
        sNombreExportador = "Criss Factory";
      }

      $("#revision_bl-exportador").html(sNombreExportador);
      $("#revision_bl-shipper").html(response.No_Shipper);

      $("#revision_bl-Qt_Caja_Total_Booking").html(
        response.Qt_Caja_Total_Booking
      );
      $("#revision_bl-Qt_Cbm_Total_Booking").html(
        response.Qt_Cbm_Total_Booking
      );
      $("#revision_bl-Qt_Peso_Total_Booking").html(
        response.Qt_Peso_Total_Booking
      );

      var sNombreTransporteMaritimo = "FCL";
      if (response.Nu_Tipo_Transporte_Maritimo == 2) {
        sNombreTransporteMaritimo = "LCL";
      }
      $("#revision_bl-Nu_Tipo_Transporte_Maritimo").html(
        sNombreTransporteMaritimo
      );

      $('[name="revision_bl-Txt_Descripcion_BL_China"]').val(
        response.Txt_Descripcion_BL_China
      );
    },
    error: function (jqXHR, textStatus, errorThrown) {
      //$( '#modal-loader' ).modal('hide');
      $(".modal-message").removeClass(
        "modal-danger modal-warning modal-success"
      );

      $("#modal-message").modal("show");
      $(".modal-message").addClass("modal-danger");
      $(".modal-title-message").text(
        textStatus + " [" + jqXHR.status + "]: " + errorThrown
      );
      setTimeout(function () {
        $("#modal-message").modal("hide");
      }, 1700);

      //Message for developer
      console.log(jqXHR.responseText);
    },
  });
}

function entregaDocsCliente(id, iIdTareaPedido) {
  $("#form-entrega_docs_cliente")[0].reset();
  $(".form-group").removeClass("has-error");
  $(".form-group").removeClass("has-success");
  $(".help-block").empty();

  $('[name="entrega_docs_cliente-ID_Pedido_Cabecera"]').val(id);

  $(" .modal-entrega_docs_cliente ").modal("show");
  var selected = "";

  url = base_url + "AgenteCompra/PedidosPagados/getBooking/" + id;
  $.ajax({
    url: url,
    type: "GET",
    dataType: "JSON",
    success: function (response) {
      console.log(response);

      $('[name="entrega_docs_cliente-Nu_Tipo_Incoterms"]').val(
        response.Nu_Tipo_Incoterms
      );

      $(".div-bl-entrega_docs").hide();
      if (response.Nu_Tipo_Incoterms == 3 || response.Nu_Tipo_Incoterms == 4) {
        $(".div-bl-entrega_docs").show();
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      //$( '#modal-loader' ).modal('hide');
      $(".modal-message").removeClass(
        "modal-danger modal-warning modal-success"
      );

      $("#modal-message").modal("show");
      $(".modal-message").addClass("modal-danger");
      $(".modal-title-message").text(
        textStatus + " [" + jqXHR.status + "]: " + errorThrown
      );
      setTimeout(function () {
        $("#modal-message").modal("hide");
      }, 1700);

      //Message for developer
      console.log(jqXHR.responseText);
    },
  });
}

function pagosLogisticos(id, iIdTareaPedido) {
  $("#form-pagos_logisticos")[0].reset();
  $(".form-group").removeClass("has-error");
  $(".form-group").removeClass("has-success");
  $(".help-block").empty();

  $(" .modal-pagos_logisticos ").modal("show");

  $('[name="pagos_logisticos-ID_Pedido_Cabecera"]').val(id);

  var selected = "",
    url_dowloand = "";

  url = base_url + "AgenteCompra/PedidosPagados/getBooking/" + id;
  $.ajax({
    url: url,
    type: "GET",
    dataType: "JSON",
    success: function (response) {
      console.log(response);

      $("#pagos_logisticos-shipper").html(response.No_Shipper);

      $('[name="pagos_logisticos-Ss_Pago_Otros_Flete_China_Yuan"]').prop(
        "disabled",
        true
      );
      $('[name="pagos_logisticos-Ss_Pago_Otros_Flete_China_Dolar"]').prop(
        "disabled",
        true
      );

      $('[name="pagos_logisticos-Ss_Pago_Otros_Costo_Origen_China_Yuan"]').prop(
        "disabled",
        true
      );
      $(
        '[name="pagos_logisticos-Ss_Pago_Otros_Costo_Origen_China_Dolar"]'
      ).prop("disabled", true);

      $('[name="pagos_logisticos-Ss_Pago_Otros_Costo_Fta_China_Yuan"]').prop(
        "disabled",
        true
      );
      $('[name="pagos_logisticos-Ss_Pago_Otros_Costo_Fta_China_Dolar"]').prop(
        "disabled",
        true
      );

      $('[name="pagos_logisticos-Ss_Pago_Otros_Cuadrilla_China_Yuan"]').prop(
        "disabled",
        true
      );
      $('[name="pagos_logisticos-Ss_Pago_Otros_Cuadrilla_China_Dolar"]').prop(
        "disabled",
        true
      );

      $('[name="pagos_logisticos-Ss_Pago_Otros_Costos_China_Yuan"]').prop(
        "disabled",
        true
      );
      $('[name="pagos_logisticos-Ss_Pago_Otros_Costos_China_Dolar"]').prop(
        "disabled",
        true
      );

      $('[name="pagos_logisticos-Ss_Pago_Otros_Flete_China_Yuan"]').val(
        response.Ss_Pago_Otros_Flete_China_Yuan
      );
      $('[name="pagos_logisticos-Ss_Pago_Otros_Flete_China_Dolar"]').val(
        response.Ss_Pago_Otros_Flete_China_Dolar
      );

      $('[name="pagos_logisticos-Ss_Pago_Otros_Costo_Origen_China_Yuan"]').val(
        response.Ss_Pago_Otros_Costo_Origen_China_Yuan
      );
      $('[name="pagos_logisticos-Ss_Pago_Otros_Costo_Origen_China_Dolar"]').val(
        response.Ss_Pago_Otros_Costo_Origen_China_Dolar
      );

      $('[name="pagos_logisticos-Ss_Pago_Otros_Costo_Fta_China_Yuan"]').val(
        response.Ss_Pago_Otros_Costo_Fta_China_Yuan
      );
      $('[name="pagos_logisticos-Ss_Pago_Otros_Costo_Fta_China_Dolar"]').val(
        response.Ss_Pago_Otros_Costo_Fta_China_Dolar
      );

      var SubTotalYuan =
        parseFloat(response.Ss_Pago_Otros_Flete_China_Yuan) +
        parseFloat(response.Ss_Pago_Otros_Costo_Origen_China_Yuan) +
        parseFloat(response.Ss_Pago_Otros_Costo_Fta_China_Yuan);
      var SubTotalDolar =
        parseFloat(response.Ss_Pago_Otros_Flete_China_Dolar) +
        parseFloat(response.Ss_Pago_Otros_Costo_Origen_China_Dolar) +
        parseFloat(response.Ss_Pago_Otros_Costo_Fta_China_Dolar);

      $("#pagos_logisticos-subtotal-yuan").html(SubTotalYuan);
      $("#pagos_logisticos-subtotal-dolar").html(SubTotalDolar);

      $('[name="pagos_logisticos-Ss_Pago_Otros_Cuadrilla_China_Yuan"]').val(
        response.Ss_Pago_Otros_Cuadrilla_China_Yuan
      );
      $('[name="pagos_logisticos-Ss_Pago_Otros_Cuadrilla_China_Dolar"]').val(
        response.Ss_Pago_Otros_Cuadrilla_China_Dolar
      );

      $('[name="pagos_logisticos-Ss_Pago_Otros_Costos_China_Yuan"]').val(
        response.Ss_Pago_Otros_Costos_China_Yuan
      );
      $('[name="pagos_logisticos-Ss_Pago_Otros_Costos_China_Dolar"]').val(
        response.Ss_Pago_Otros_Costos_China_Dolar
      );

      var TotalYuan =
        parseFloat(response.Ss_Pago_Otros_Cuadrilla_China_Yuan) +
        parseFloat(response.Ss_Pago_Otros_Costos_China_Yuan);
      var TotalDolar =
        parseFloat(response.Ss_Pago_Otros_Cuadrilla_China_Dolar) +
        parseFloat(response.Ss_Pago_Otros_Costos_China_Dolar);

      $("#pagos_logisticos-total-yuan").html(SubTotalYuan + TotalYuan);
      $("#pagos_logisticos-total-dolar").html(SubTotalDolar + TotalDolar);

      $(".div-pagos_logisticos-cif_ddp").hide();
      if (response.Nu_Tipo_Incoterms == 3 || response.Nu_Tipo_Incoterms == 4) {
        $(".div-pagos_logisticos-cif_ddp").show();
      }

      url_dowloand = response.Txt_Url_Pago_Otros_Flete_China;
      url_dowloand = url_dowloand.replace("https://", "../../");
      url_dowloand = url_dowloand.replace("assets", "public_html/assets");
      $("#pagos_logisticos-Txt_Url_Pago_Otros_Flete_China-a").attr(
        "href",
        url_dowloand
      );

      url_dowloand = response.Txt_Url_Pago_Otros_Costo_Origen_China;
      url_dowloand = url_dowloand.replace("https://", "../../");
      url_dowloand = url_dowloand.replace("assets", "public_html/assets");
      $("#pagos_logisticos-Txt_Url_Pago_Otros_Costo_Origen_China-a").attr(
        "href",
        url_dowloand
      );

      url_dowloand = response.Txt_Url_Pago_Otros_Costo_Fta_China;
      url_dowloand = url_dowloand.replace("https://", "../../");
      url_dowloand = url_dowloand.replace("assets", "public_html/assets");
      $("#pagos_logisticos-Txt_Url_Pago_Otros_Costo_Fta_China-a").attr(
        "href",
        url_dowloand
      );

      url_dowloand = response.Txt_Url_Pago_Otros_Cuadrilla_China;
      url_dowloand = url_dowloand.replace("https://", "../../");
      url_dowloand = url_dowloand.replace("assets", "public_html/assets");
      $("#pagos_logisticos-Txt_Url_Pago_Otros_Cuadrilla_China-a").attr(
        "href",
        url_dowloand
      );

      url_dowloand = response.Txt_Url_Pago_Otros_Costos_China;
      url_dowloand = url_dowloand.replace("https://", "../../");
      url_dowloand = url_dowloand.replace("assets", "public_html/assets");
      $("#pagos_logisticos-Txt_Url_Pago_Otros_Costos_China-a").attr(
        "href",
        url_dowloand
      );
    },
    error: function (jqXHR, textStatus, errorThrown) {
      //$( '#modal-loader' ).modal('hide');
      $(".modal-message").removeClass(
        "modal-danger modal-warning modal-success"
      );

      $("#modal-message").modal("show");
      $(".modal-message").addClass("modal-danger");
      $(".modal-title-message").text(
        textStatus + " [" + jqXHR.status + "]: " + errorThrown
      );
      setTimeout(function () {
        $("#modal-message").modal("hide");
      }, 1700);

      //Message for developer
      console.log(jqXHR.responseText);
    },
  });
}

function generarAgenteCompra(ID) {
  var $modal_delete = $("#modal-message-delete");
  $modal_delete.modal("show");

  $(".modal-message-delete").removeClass(
    "modal-danger modal-warning modal-success"
  );
  $(".modal-message-delete").addClass("modal-success");

  $("#modal-title").text("¿Deseas genera EXCEL?");

  $("#btn-cancel-delete")
    .off("click")
    .click(function () {
      $modal_delete.modal("hide");
    });

  $("#btn-save-delete")
    .off("click")
    .click(function () {
      _generarAgenteCompra($modal_delete, ID);
    });
}

function _generarAgenteCompra($modal_delete, ID) {
  $modal_delete.modal("hide");
  url = base_url + "AgenteCompra/PedidosPagados/generarAgenteCompra/" + ID;
  // $.ajax({
  //   url: url,
  //   type: "GET",
  //   dataType: "JSON",
  //   success: function (response) {
  //     console.log(response);
  //   },
  //   error: function (jqXHR, textStatus, errorThrown) {
  //     //$( '#modal-loader' ).modal('hide');
  //   },
  // });
  window.open(url, "_blank");
}

function generarConsolidaTrading(ID) {
  var $modal_delete = $("#modal-message-delete");
  $modal_delete.modal("show");

  $(".modal-message-delete").removeClass(
    "modal-danger modal-warning modal-success"
  );
  $(".modal-message-delete").addClass("modal-success");

  $("#modal-title").text("¿Deseas genera EXCEL?");

  $("#btn-cancel-delete")
    .off("click")
    .click(function () {
      $modal_delete.modal("hide");
    });

  $("#btn-save-delete")
    .off("click")
    .click(function () {
      _generarConsolidaTrading($modal_delete, ID);
    });
}

function _generarConsolidaTrading($modal_delete, ID) {
  $modal_delete.modal("hide");
  url = base_url + "AgenteCompra/PedidosPagados/generarConsolidaTrading/" + ID;
  window.open(url, "_blank");
}
//get order progress section
const getOrderProgress = (id, idServicio = null) => {
  currentServicio = idServicio;
  if (idServicio != 2) {
    $("#cotizacionOrdenContainer").hide();
  } else {
    $("#cotizacionOrdenContainer").show();
  }
  $(".step-column").remove();
  idPedido = id;
  url = base_url + "AgenteCompra/PedidosPagados/getOrderProgress";
  const steps = $("#steps");
  const loading = $("#loading-steps");
  //set name attr  =orden['id']

  $("#consolidadoOrden").attr("name", `orden[${id}]`);
  $.post(url, { idPedido: id }, function (response) {
    //parse response
    try {
      response = JSON.parse(response);
      //check if response is success
      if (response.status == "success") {
        //get data
        const data = response.data;
        const consolidadoOrden = $("#consolidadoOrden");
        consolidadoOrden.val(response.consolidadoCode);
        containerListar.hide();
        containerVer.show();
        loading.hide();
        data.forEach((step, i) => {
          steps.append(stepTemplate(step, i));
          console.log(step);
          if (step.status == "COMPLETED") {
            $(`#step-${i}`)
              .removeClass("step-container")
              .addClass("step-container-completed");
          }
          if (step.status == "PROGRESS") {
            $(`#step-${i}`)
              .removeClass("step-container")
              .addClass("step-container-progress");
          }
        });
        $(".steps-buttons").empty();
        const configButtons = {
          btnCancel: {
            text: "Regresar",
            action: "hideSteps()",
          },
          btnSave: {
            text: "Guardar",
            action: "saveOrderProgress()",
          },
        };
        $(".steps-buttons").append(getActionButtons(configButtons));

        //set data to modal
      } else {
        loading.hide();
        steps.append(`<div class="alert alert-danger">Hubo un error</div>`);
        //show error message
      }
    } catch (e) {
      loading.hide();
      steps.append(`<div class="alert alert-danger">Hubo un error</div>`);

      //show error message
    }
  });
};
const saveOrderProgress = () => {
  const url = base_url + "AgenteCompra/PedidosPagados/updateOrdenPedido";
  //from  name ordern['id'] get id
  hideSteps();
  idPedido = $("#consolidadoOrden").attr("name").split("[")[1].split("]")[0];
  const data = {
    idPedido,
    value: $("#consolidadoOrden").val(),
  };
  $.ajax({
    url,
    type: "POST",
    data,
    success: function (response) {
      try {
        response = JSON.parse(response);
      } catch (e) {
        console.log(e);
      }
    },
  });
};
const stepTemplate = (step, i) => {
  const stepHTML = `
      <div class="step-container" onclick="openStepFunction(${i + 1},${
    step.id
  })"  id="step-${i}">
      <span class="step">${step.name}</span>
      <img src="${step.iconURL} " class="step-icon w-100" />
      </div>
  `;
  return stepHTML;
};

const getItemTemplate = (i, mode, detalle) => {
  div_items = `
  <div id="card"${i}" class="card border-0 rounded shadow-sm mt-3">
    <input type="hidden" id="modal-detalle${i}" data-correlativo="${i}" inputmode="decimal" name="addProducto[${i}][proovedor-id]" class="arrProducto form-control required precio input-decimal" placeholder="" value="" autocomplete="off" />
    <input type="hidden" id="modal-pedido-cabecera${i}" data-correlativo="${i}" inputmode="decimal" name="addProducto[${i}][pedido-cabecera]" class="arrProducto form-control required precio input-decimal" placeholder="" value="" autocomplete="off" />
    <input type="hidden" id="modal_proveedor-id-${i}" name="addProducto[${i}][detalle-id]" />
    <input type="hidden" class="modal_coordination_id" name="modal_coordination_id"/>
    <div class = "row" >
      <div class="col-6 col-md-3 col-lg-2">
        <span class="fw-bold">Precio ¥<span class="label-advertencia text-danger"> *</span><span/>
        <div class="form-group">
          <input type="text" id="modal-precio${i}" data-correlativo="${i}" inputmode="decimal" name="addProducto[${i}][precio]" class="arrProducto form-control required precio input-decimal" placeholder="" value="" autocomplete="off" />
        <span class="help-block text-danger" id="error"></span>
        </div>
      </div>
      <div class="col-6 col-md-3 col-lg-2">
        <span class="fw-bold">Moq<span class="label-advertencia text-danger"> *</span><span/>
        <div class="form-group">
          <input type="text" id="modal-moq${i}" data-correlativo="${i}" inputmode="decimal" name="addProducto[${i}][moq]" class="arrProducto form-control required moq input-decimal" placeholder="" value="" autocomplete="off" />
          <span class="help-block text-danger" id="error"></span>
        </div>
      </div>
      <div class="col-6 col-md-3 col-lg-2">
        <span class="fw-bold">Qty_caja<span class="label-advertencia text-danger"> *</span><span/>
        <div class="form-group">
          <input type="text" id="modal-qty_caja${i}" data-correlativo="${i}" inputmode="decimal" name="addProducto[${i}][qty_caja]" class="arrProducto form-control required qty_caja input-decimal" placeholder="" value="" autocomplete="off" />
          <span class="help-block text-danger" id="error"></span>
        </div>
      </div>
      <div class="col-6 col-md-3 col-lg-2">
        <span class="fw-bold">Cbm<span class="label-advertencia text-danger"> *</span><span/>
        <div class="form-group">
          <input type="text" id="modal-cbm${i}" data-correlativo="${i}" inputmode="decimal" name="addProducto[${i}][cbm]" class="arrProducto form-control required cbm input-decimal" placeholder="" value="" autocomplete="off" />
          <span class="help-block text-danger" id="error"></span>
        </div>
      </div>
      <div class="col-6 col-md-3 col-lg-2">
        <span class="fw-bold">Delivery<span class="label-advertencia text-danger"> *</span><span/>
        <div class="form-group">
          <input type="text" id="modal-delivery${i}" data-correlativo="${i}" inputmode="decimal" name="addProducto[${i}][delivery]" class="arrProducto form-control required delivery input-decimal" placeholder="" value="" autocomplete="off" />
          <span class="help-block text-danger" id="error"></span>
        </div>
      </div>
      <div class="col-6 col-md-3 col-lg-2">
        <span class="fw-bold">Shipping Cost<span class="label-advertencia text-danger"> *</span><span/>
        <div class="form-group">
          <input type="text" id="modal-costo_delivery${i}" data-correlativo="${i}" inputmode="decimal" name="addProducto[${i}][shipping_cost]" class="arrProducto form-control required shipping_cost input-decimal" placeholder="" value="" autocomplete="off" />
          <span class="help-block text-danger" id="error"></span>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12 col-md-6 col-lg-6">
        <div class="row h-100">
          <div class="col-12 col-md-8 col-lg-8 d-flex flex-column justify-content-center">
            <!--Upload Icon-->
            <div class="form-group mx-auto " id="container-uploadprimaryimg-${i}">
            <label>Imagen Principal</label>
            ${
              detalle["main_photo"] == null
                ? ""
                : `<span class="fw-bold  btn btn-danger"
              onclick="deleteImage('${i}',1)">Eliminar</span>`
            }  
            </br>
            <input type="hidden" name="addProducto[${i}][main_photo]" id="btn-uploadprimaryimg-URL-${i}"/>
            <input type="file" name="file[${i}][main_photo]" class="btn btn-outline-primary btn-block" id="btn-uploadprimaryimg-${i}" data-correlativo="${i}" data-toggle="modal" data-target="#modal-upload${i}"></input>
               
            </div>
          </div>
          <div class="col-12 col-md-4 col-lg-4 d-flex flex-column justify-content-center">
          <div class="form-group" id="container-uploadimg2-${i}">
          <label>Imagen 2</label>
          <input type="hidden" name="addProducto[${i}][secondary_photo]" id="btn-uploadimg2-URL-${i}"/>  
          ${
            detalle["secondary_photo"] == null
              ? ""
              : `<span class="fw-bold  btn btn-danger"
              onclick="deleteImage('${i}',2)">Eliminar</span>`
          }          
          <input type="file" name="file[${i}][secondary_photo]" class="btn btn-outline-primary btn-block" id="btn-uploadimg2-${i}" data-correlativo="${i}" data-toggle="modal" data-target="#modal-upload${i}"></input>
          </div>
            <div class="form-group" id="container-uploadimg3-${i}">
            <label>Imagen 3</label>
            <input type="hidden" name="addProducto[${i}][terciary_photo]" id="btn-uploadimg3-URL-${i}"/>
            ${
              detalle["terciary_photo"] == null
                ? ""
                : `<span class="fw-bold  btn btn-danger"
              onclick="deleteImage('${i}',3)">Eliminar</span>`
            }
            <input type="file" name="file[${i}][terciary_photo]" class="btn btn-outline-primary btn-block" id="btn-uploadimg3-${i}" data-correlativo="${i}" data-toggle="modal" data-target="#modal-upload${i}"></input></div>
            <div class="form-group" id="container-uploadvideo1-${i}">
            <label>Video 1</label>
            <input type="hidden" name="addProducto[${i}][primary_video]" id="btn-uploadvideo1-URL-${i}"/>
            <input type="file" name="file[${i}][primary_video]" class="btn btn-outline-primary btn-block" id="btn-uploadvideo1-${i}" data-correlativo="${i}" data-toggle="modal" data-target="#modal-upload${i}"></input></div>
            <div class="form-group"  id="container-uploadvideo2-${i}">
            <label>Video 2</label>
            <input type="hidden" name="addProducto[${i}][secondary_video]"  id="btn-uploadvideo2-URL-${i}"/>
            <input type="file" name="file[${i}][secondary_video]" class="btn btn-outline-primary btn-block" id="btn-uploadvideo2-${i}" data-correlativo="${i}" data-toggle="modal" data-target="#modal-upload${i}"></input></div>

          </div>
        </div>
      </div>
      <div class="col-12 col-md-6 col-lg-6">
        <span class="fw-bold">Nombre Proveedor<span class="label-advertencia text-danger"> *</span><span/>
        <div class="form-group">
          <input type="text" id="modal-nombre_proveedor${i}" data-correlativo="${i}" name="addProducto[${i}][nombre_proveedor]" class="arrProducto form-control required nombre_proveedor" placeholder="" value="" autocomplete="off" />
          <span class="help-block text-danger" id="error"></span>
        </div>
        <span class="fw-bold">N° Celular<span class="label-advertencia text-danger"> *</span><span/>
        <div class="form-group">
          <input type="text" id="modal-celular_proveedor${i}" data-correlativo="${i}" name="addProducto[${i}][celular_proveedor]" class="arrProducto form-control required celular_proveedor" placeholder="" value="" autocomplete="off" />
          <span class="help-block text-danger" id="error"></span>
        </div>
        <span class="fw-bold">Notas <span class="label-advertencia text-danger"> </span><span/>
        <div class="form-group">
          <textarea id="modal-notas${i}" data-correlativo="${i}" name="addProducto[${i}][notas]" class="arrProducto form-control required notas" placeholder="" value="" autocomplete="off" ></textarea>
        </div>

      </div>
    </div>
    `;
  // var id_detalle = detalle["ID_Pedido_Detalle"];
  // var id_item = detalle["ID_Pedido_Detalle_Producto_Proveedor"];
  // var id_supplier = detalle["id_supplier"];
  return div_items;
};
const deleteImage = (i, imgIndex) => {
  if (imgIndex == 1) {
    $(`#container-uploadprimaryimg-${i}`).find("img").remove();
    $(`#btn-uploadprimaryimg-${i}`).css("display", "flex");
    $(`#btn-uploadprimaryimg-URL-${i}`).val("null");
    return;
  }
  $(`#container-uploadimg${imgIndex}-${i}`).find("img").remove();
  // set #btn-uploadimg3-${i} display flex
  $(`#btn-uploadimg${imgIndex}-${i}`).css("display", "flex");
  $(`#btn-uploadimg${imgIndex}-URL-${i}`).val("null");
};

const openStepFunction = (i, stepId) => {
  $("#container-ver").hide();
  $("#inspection-photos").hide();
  containerOrdenCompra.show();
  selectedStep = stepId;
  url = base_url + "AgenteCompra/PedidosPagados/getStepByRole";
  //ajax post
  $.post(url, { idPedido: idPedido, step: i }, function (response) {
    const responseParsed = JSON.parse(response);
    currentPrivilege = responseParsed.priviligie;
    if (i == 1) {
      openOrdenCompra(response);
    }
    if (i == 2) {
      if (currentPrivilege == 1) {
        openPagos(response);
      } else {
        openCoordination(response);
      }
    }
    if (i == 3) {
      if (currentPrivilege == 2 || currentPrivilege == 5) {
        openAlmacenView(responseParsed.data, idPedido, currentPrivilege);
      }
    }
    if (i == 4) {
      if (currentPrivilege == 2 || currentPrivilege == 5) {
        openInspectionView(responseParsed.data, idPedido, currentPrivilege);
      }
    }
  });
};
const openInspectionView = (data, idPedido, currentPrivilege) => {
  $("#container_orden-compra").hide();
  let cotizacionCode = 0;
  if (data) {
    cotizacionCode = data[0].cotizacionCode;
  }
  const inspectionTitle = getInspectionTitle(cotizacionCode);
  const inspectionTable = getInspectionTableTemplate(
    data,
    currentPrivilege,
    cotizacionCode
  );
  containerInspection.append(inspectionTitle).append(inspectionTable);
  containerInspection.show();
};
const getInspectionTitle = (cotizacionCode) => {
  return `
  <div class="d-flex flex-row justify-content-between">
    <h3>Inspección</h3>
    <h3>Cotización: ${cotizacionCode}</h3>
  </div>
  `;
};
const getInspectionTableTemplate = (data, currentPrivilege, cotizacionCode) => {
  html = `
    <div class="inspection-table d-flex flex-column">`;
  html +=
    '<div class="inspection-table-header d-flex flex-row"><div class="inspection-column inspection-img">IMAGEN</div><div class="inspection-column inspection-name">NOMBRE PRODUCTO</div><div class="inspection-column inspection-caracteristicas">CARACTERISTICAS</div><div class="inspection-column inspection-code">ITEM</div><div class="inspection-column inspection-qty-box">QTY CAJAS</div><div class="inspection-column inspection-photos" >INSPECCION</div><div class="inspection-column inspection-estado">ESTADO</div><div class="inspection-column inspection-notas">NOTAS</div>';
  html += "</div>";
  data.forEach((item, i) => {
    html += `
        <div class="inspection-row d-flex flex-row">
          <div class="inspection-column inspection-img">
            <img src="${item.Txt_Url_Imagen_Producto}" 
            
            class="img-table_item w-100 px-2" data-id_item="${
              item.id_item
            }" data-url_img="${item.Txt_Url_Imagen_Producto}" />
          </div>
          <div class="inspection-column inspection-name">${
            item.Txt_Producto
          }</div>
          <div class="inspection-column inspection-caracteristicas">
          <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" disabled>
         ${htmlDecode(item.Txt_Descripcion)}
          </textarea>
          </div>
          <div class="inspection-column inspection-code">${
            item.product_code
          }</div>
          <div class="inspection-column inspection-qty-box" >
          <span class="fw-bold">${
            item.total_box ? parseInt(item.total_box) : 0
          }</span>
          </div>
          <div class="inspection-column inspection-photos"><svg 
          onclick="viewInspeccionPhotos(${
            item.ID_Pedido_Detalle_Producto_Proveedor
          },'${cotizacionCode}','${item.ID_Pedido_Cabecera}',
          '${currentPrivilege}')"
          class="${
            item.personal_china_inspeccion_estado != "PENDIENTE"
              ? "camera-filled"
              : "camera-not-filled"
          }"
          viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 16C13.6569 16 15 14.6569 15 13C15 11.3431 13.6569 10 12 10C10.3431 10 9 11.3431 9 13C9 14.6569 10.3431 16 12 16Z" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M3 16.8V9.2C3 8.0799 3 7.51984 3.21799 7.09202C3.40973 6.71569 3.71569 6.40973 4.09202 6.21799C4.51984 6 5.0799 6 6.2 6H7.25464C7.37758 6 7.43905 6 7.49576 5.9935C7.79166 5.95961 8.05705 5.79559 8.21969 5.54609C8.25086 5.49827 8.27836 5.44328 8.33333 5.33333C8.44329 5.11342 8.49827 5.00346 8.56062 4.90782C8.8859 4.40882 9.41668 4.08078 10.0085 4.01299C10.1219 4 10.2448 4 10.4907 4H13.5093C13.7552 4 13.8781 4 13.9915 4.01299C14.5833 4.08078 15.1141 4.40882 15.4394 4.90782C15.5017 5.00345 15.5567 5.11345 15.6667 5.33333C15.7216 5.44329 15.7491 5.49827 15.7803 5.54609C15.943 5.79559 16.2083 5.95961 16.5042 5.9935C16.561 6 16.6224 6 16.7454 6H17.8C18.9201 6 19.4802 6 19.908 6.21799C20.2843 6.40973 20.5903 6.71569 20.782 7.09202C21 7.51984 21 8.0799 21 9.2V16.8C21 17.9201 21 18.4802 20.782 18.908C20.5903 19.2843 20.2843 19.5903 19.908 19.782C19.4802 20 18.9201 20 17.8 20H6.2C5.0799 20 4.51984 20 4.09202 19.782C3.71569 19.5903 3.40973 19.2843 3.21799 18.908C3 18.4802 3 17.9201 3 16.8Z" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg></div>
          <div class="inspection-column inspection-estado">
          <select class="form-select" >
          <option value="PENDIENTE" ${
            item.estado == "PENDIENTE" ? "selected" : ""
          }>PENDIENTE</option>
          <option value="INSPECCIONADO" ${
            item.estado == "INSPECCIONADO" ? "selected" : ""
          }>INSPECCIONADO</option>
          </select>
          </div>
          <div class="inspection-column inspection-notas">
          <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" value="${htmlDecode(
            item.empleado_china_notas
          )}"></textarea>
          </div>
        </div>
        `;
  });
  html += "</div>";
  return html;
};

const viewInspeccionPhotos = (
  idItem,
  cotizacionCode,
  idPedido,
  currentPrivilege
) => {
  const containerInspection = $("#container-inspeccion");
  containerInspection.empty();
  const content = getInspectionPhotosTemplate();
  containerInspection.append(content);

  const maxFileSize = 20 * 1024 * 1024; // 20 MB en bytes

  // Cargar archivos existentes
  $.ajax({
    url: base_url + "AgenteCompra/PedidosPagados/getInspectionPhotos",
    type: "POST",
    data: { idItem },
    success: function (response) {
      try {
        response = JSON.parse(response);
        if (response.status === "success") {
          const data = response.data;
          ["foto1", "foto2", "foto3", "video1", "video2"].forEach((key) => {
            if (data[`inspeccion_${key}`]) {
              const input = $(`input[name="${key}"]`);
              const preview = input.siblings(".preview");
              preview.attr("src", data[`inspeccion_${key}`]).show();
              input.siblings("span").hide();
              input
                .closest("label")
                .append(
                  '<div class="reupload-text btn btn-outline-secondary">Volver a subir</div>'
                );
            }
          });
        }
      } catch (e) {
        console.log(e);
      }
    },
  });

  // Limpiar eventos previos
  containerInspection.off("click", ".image-large, .image-small");
  containerInspection.off("change", 'input[type="file"]');
  containerInspection.off("click", ".close-button");

  // Manejar clic en el contenedor para abrir el input
  containerInspection.on("click", ".image-large, .image-small", function (e) {
    e.stopPropagation();
    e.preventDefault();
    $(this).off('click');
    console.log("click container ");
    const input = $(this).find('input[type="file"]');
    if (input.length) {
      con
      setTimeout(() => {
        input.trigger("click");
      }, 100); // Agregar un pequeño retraso
    }
    $(this).on('click', function(e) {
      e.stopPropagation();
      e.preventDefault();
    });
  });

  // Manejar cambio en los inputs de archivo
  containerInspection.on("change", 'input[type="file"]', function (e) {
    console.log("change");
    e.stopPropagation();
    e.stopImmediatePropagation();

    e.preventDefault();
    const file = this.files[0];
    const label = $(this).closest("label");
    const preview = label.find(".preview");
    const spinner = label.find(".spinner-border");
    const errorContainer = label.find(".error-message");

    if (file) {
      if (file.size > maxFileSize) {
        errorContainer
          .text(
            "El archivo es demasiado grande. El tamaño máximo permitido es 20 MB."
          )
          .show();
        $(this).val("");
        setTimeout(() => {
          errorContainer.hide();
        }, 3000);
        return;
      }

      spinner.show();
      preview.hide();
      label.find("span:not(.reupload-text)").hide();
      $(this).prop("disabled", true);

      const reader = new FileReader();
      reader.onload = (e) => {
        preview.attr("src", e.target.result).show();
        spinner.hide();
        $(this).prop("disabled", false);
      };

      if (file.type.startsWith("image/")) {
        reader.readAsDataURL(file);
      } else if (file.type.startsWith("video/")) {
        reader.readAsDataURL(file);
      }
    }
  });

  // Manejar clic en el botón de cerrar
  containerInspection.on("click", ".close-button", function (e) {
    console.log("click");
    e.stopPropagation();
    e.stopImmediatePropagation();
    e.preventDefault();

    const label = $(this).closest("label");
    const fileInput = label.find('input[type="file"]');
    const preview = label.find(".preview");

    fileInput.val("");
    preview.hide().attr("src", "");
    label.find("span").show();
    label.find(".reupload-text").remove();
  });

  // Agregar botones de acción
  const btns = {
    btnCancel: {
      text: "Regresar",
      action: `openStepFunction(4,${selectedStep})`,
    },
    btnSave: {
      text: "Guardar",
      action: `saveInspectionPhotos(${idItem},${cotizacionCode},${idPedido},${currentPrivilege})`,
    },
  };
  const actionButtons = getActionButtons(btns);
  $("#inspection-buttons").append(actionButtons);
};

const getInspectionPhotosTemplate = () => {
  return `
    <div class="container mt-5" id="inspection-photos">
      <div id="inspectionForm" enctype="multipart/form-data">
        <div class="row">
          <div class="col-md-8">
            <label class="image-large position-relative">
              <div class="close-button">×</div>
              <span>Imagen</span>
              <div class="error-message" style="color:red; display:none;"></div>
              <div class="spinner-border" role="status" style="display:none;">
                <span class="sr-only">Cargando...</span>
              </div>
              <input type="file" name="foto1" accept="image/*" style="display:none;">
              <img class="preview" src="" alt="" style="display: none;">
            </label>
          </div>
          <div class="col-md-4">
            <label class="image-small position-relative">
              <div class="close-button">×</div>
              <span>Imagen</span>
              <div class="error-message" style="color:red; display:none;"></div>
              <div class="spinner-border" role="status" style="display:none;">
                <span class="sr-only">Cargando...</span>
              </div>
              <input type="file" name="foto2" accept="image/*" style="display:none;">
              <img class="preview" src="" alt="" style="display: none;">
            </label>
            <label class="image-small position-relative">
              <div class="close-button">×</div>
              <span>Imagen</span>
              <div class="error-message" style="color:red; display:none;"></div>
              <div class="spinner-border" role="status" style="display:none;">
                <span class="sr-only">Cargando...</span>
              </div>
              <input type="file" name="foto3" accept="image/*" style="display:none;">
              <img class="preview" src="" alt="" style="display: none;">
            </label>
            <label class="image-small position-relative">
              <div class="close-button">×</div>
              <span>Video</span>
              <div class="error-message" style="color:red; display:none;"></div>
              <div class="spinner-border" role="status" style="display:none;">
                <span class="sr-only">Cargando...</span>
              </div>
              <input type="file" name="video1" accept="video/*" style="display:none;">
              <video class="preview" controls style="display: none;"></video>
            </label>
            <label class="image-small position-relative">
              <div class="close-button">×</div>
              <span>Video</span>
              <div class="error-message" style="color:red; display:none;"></div>
              <div class="spinner-border" role="status" style="display:none;">
                <span class="sr-only">Cargando...</span>
              </div>
              <input type="file" name="video2" accept="video/*" style="display:none;">
              <video class="preview" controls style="display: none;"></video>
            </label>
          </div>
        </div>
      </div>
      <div class="d-flex justify-content-center mt-3" id="inspection-buttons">
      </div>
    </div>`;
};

const saveInspectionPhotos = (
  idItem,
  cotizacionCode,
  idPedido,
  currentPrivilege
) => {
  const form = $("#container-inspeccion");
  const formData = new FormData(form[0]);
  formData.append("idItem", idItem);
  formData.append("cotizacionCode", cotizacionCode);
  formData.append("idPedido", idPedido);
  formData.append("currentPrivilege", currentPrivilege);
  for (let pair of formData.entries()) {
    if (pair[1] instanceof File) {
      console.log(`File field: ${pair[0]}`);
      console.log(`File name: ${pair[1].name}`);
      console.log(`File size: ${pair[1].size}`);
      console.log(`File type: ${pair[1].type}`);
    } else {
      console.log(`${pair[0]}: ${pair[1]}`);
    }
  }
  url = base_url + "AgenteCompra/PedidosPagados/saveInspectionPhotos";

  $.ajax({
    url: url,
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    success: function (response) {
      console.log(response);
      const responseParsed = JSON.parse(response);
      if (responseParsed.status == "success") {
        containerInspection.empty();
        $(".step-container").remove();
        $(".step-container-completed").remove();
        getOrderProgress(idPedido);
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.log(jqXHR.responseText);
    },
  });
};

const openCoordination = (response) => {
  containerOrdenCompra.hide();
  let data = JSON.parse(response).data;

  const headerDiv = getSupplierCoordinationHeader(data);
  console.log(headerDiv);
  containerCoordination.append(headerDiv);
  const bodyDIV = getSupplierCoordinationTableTemplate(data);
  containerCoordination.append(bodyDIV);
  const configButtons = {
    btnCancel: {
      text: "Regresar",
      action: "hideCoordination()",
    },
    btnSave: {
      text: "Guardar",
      action: "saveCoordination()",
    },
  };
  containerCoordination.append(getActionButtons(configButtons));
  containerCoordination.show();
};
const hideCoordination = () => {
  containerCoordination.empty();
  //show steps
  containerVer.show();
};
const saveCoordination = () => {
  const form = $("#form-coordination");
  $("orden-compra_header").hide();
  const formData = new FormData(form[0]);
  $(".input-total").each(function () {
    let id = $(this).attr("id");
    console.log(id);
    let value = $(this).html().trim();
    console.log(value);
    formData.append(id, value);
  });
  url = base_url + "AgenteCompra/PedidosPagados/saveCoordination";
  $.ajax({
    url: url,
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    success: function (response) {
      console.log(response);
      const responseParsed = JSON.parse(response);
      if (responseParsed.status == "success") {
        containerCoordination.empty();
        $(".step-container").remove();
        $(".step-container-completed").remove();
        getOrderProgress(idPedido);
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.log(jqXHR.responseText);
    },
  });
};
/**
 * This function is used to get the header of the table
 * @returns {string} html
 */
const getSupplierCoordinationTableHeader = () => {
  let template = `
  <div class="d-flex flex-row supplier-table-header">
          <div class="coordination-supplier-column c-supplier-column">SUPPLIER</div>
          <div class="coordination-imagen-column c-imagen-column">IMAGEN</div>
          <div class="coordination-nombre-column c-nombre-column">NOMBRE</div>
          <div class="coordination-qty-column c-qty-column ">QTY</div>
          <div class="coordination-precio-column c-precio-column">PRECIO</div>
          <div class="coordination-total-column c-total-column">TOTAL</div>
          
          <div class="coordination-pago1-column c-pago1-column">PAGO 1</div>
          <div class="coordination-pago2-column c-pago2-column">PAGO 2</div>
          <div class="coordination-tproducto-column c-tproducto-column">DELIVERY</div>
          <div class="coordination-tentrega-column c-tentrega-column">F.ENTREGA</div>
  
  `;
  if (currentPrivilege == priviligesJefeChina) {
    template += `<div class="coordination-estado-column c-estado-column">NEGOCIACION</div>`;
    template += `<div class="coordination-c-negociacion-column c-negociacion-column">ESTADO</div>`;
  } else {
    template += `<div class="coordination-estado-column c-estado-column">ESTADO</div>`;
  }
  template += `</div>`;
  return template;
};
/**
 *  This function is used to get the body of the table
 * @param {Array} data
 * @returns {string} html
 */
$("#table-elegir_productos_proveedor").on(
  "click",
  ".img-table_item",
  function () {
    $(".img-responsive").attr("src", "");
    $(".modal-ver_item").modal("show");
    console.log($(this));
    $(".img-responsive").attr(
      "src",
      $(this).data("url_img") || $(this)[0].currentSrc
    );
    $("#a-download_image").attr("data-id_item", $(this).data("id_item"));
    $("#a-download_image").attr("data-src", $(this)[0].currentSrc);
  }
);
const getSupplierCoordinationTableTemplate = (data) => {
  const header = getSupplierCoordinationTableHeader();
  let html = `
  <form id="form-coordination">
  <div class="supplier-table">`;
  html += header;
  const defaultHeight = 200;

  data.forEach((supplier) => {
    const detalles = JSON.parse(supplier.detalles);
    const sumDelivery = detalles.reduce(
      (acc, detail) => acc + parseFloat(detail.shipping_cost),
      0
    );
    let total = detalles.reduce(
      (acc, detail) => acc + detail.qty_product * detail.price_product,
      0
    );
    total = total + sumDelivery;
    const detailsCount = detalles.length;
    const detailsImgs = detalles.map((detail) => {
      return detail.imagenURL;
    });
    const detailsDelivery = detalles.map((detail) => {
      return detail.delivery;
    });
    html += `
      <div class="supplier-row">
        <div class="supplier-info supplier-column" style="height:${
          detailsCount * defaultHeight
        }px">
          <div>Nombre: ${supplier.name}</div>
          <div>Teléfono: ${supplier.phone}</div>
          <div>Costo shipping:  ¥${sumDelivery}</div>
          <input type="hidden" name="id-pedido" value="${supplier.id_pedido}"/>
          <input type="hidden" name="current-step" value="${selectedStep}"/>
          <div class="btn btn-outline-secondary btn-coordinar mb-1" onclick="openSupplierItems(
          ${supplier.id_pedido},${supplier.id_supplier},${
      supplier.id_coordination
    })">Cambiar</div>
          <div class="btn btn-outline-secondary btn-coordinar" onclick="downloadSupplierExcel(
          ${supplier.id_pedido},${supplier.id_supplier},${
      supplier.id_coordination
    })">Invoice</div>
        </div>`;
    html += `<div class="imgs-container c-imagen-column supplier-column">`;
    detailsImgs.forEach((img) => {
      html += ` <div class="c-imagen-column supplier-column">
          <img src="${img}" alt="imagen" class="img-thumbnail" />
          </div>`;
    });
    html += `</div>`;
    html += `<div class="c-nombre-container c-nombre-column supplier-column">`;
    detalles.forEach((detail) => {
      html += `
          <div class=" h-100 item px-2" style="height:${defaultHeight}px">
            <span>${detail.nombre_producto} </span>
            <div class="input-group mt-3 d-flex flex-row justify-content-center align-items-center mb-1 ">
              <span class="input-group-text " id="basic-addon1"> CODE:</span>
              <input type="text" class="form-control" name="item[${detail.ID_Pedido_Detalle}]['code']" aria-describedby="basic-addon1" value="${detail.product_code}" id="item['${detail.ID_Pedido_Detalle}']['code']">
            </div>
            <div class="btn btn-success" onclick="openRotuladofromCoordination(${detail.ID_Pedido_Detalle})">Rotulado
            </div>
           
          </div>`;
    });
    html += `</div>`;
    //qty container
    html += `<div class="c-qty-container c-qty-column supplier-column">`;
    detalles.forEach((detail) => {
      html += `<div class="h-100 item px-2">
              <input type="number" class="form-control" value="${parseFloat(
                detail.qty_product
              )}" name="proveedor[${
        detail.ID_Pedido_Detalle_Producto_Proveedor
      }][qty_product]"/>
            </div>
           `;
    });
    html += `</div>`;
    //precio container
    html += `<div class="c-precio-container c-precio-column supplier-column">`;
    detalles.forEach((detail) => {
      html += ` 
       <div class="h-100 item px-2">
              <div class="input-group d-flex flex-row">
                <span class="input-group-text d-flex w-auto">¥</span>
                <input type="number" class="form-control" value="${parseFloat(
                  detail.price_product
                )}" name="proveedor[${
        detail.ID_Pedido_Detalle_Producto_Proveedor
      }][price_product]"/>
             </div>
            </div>
          `;
    });
    html += `</div>`;
    html += `
        <div class="c-total-column px-2 supplier-column" style="height:${
          detailsCount * defaultHeight
        }px">
          <div class="input-group d-flex flex-row"><span class="input-group-text d-flex w-auto">¥</span>
        <span id="coordination[${
          supplier.id_coordination
        }][total]" class="input-total form-control" value="${parseFloat(
      total
    ).toFixed(2)}"> ${parseFloat(total).toFixed(2)}</span>
        </div>
        </div>`;

    if (currentPrivilege == priviligesJefeChina) {
      html += `
          <div class="c-pago1-column px-2 supplier-column">
          <div class="input-group d-flex flex-row">
            <span class="input-group-text d-flex w-auto">¥</span>

          <input type="number" class="form-control" value="${
            supplier.pago_1_value
          }" name="coordination[${supplier.id_coordination}][pago_1_value]"/>
          </div>
          <div class="btn mt-1 mx-auto ${
            supplier.pago_1_URL == null ? "btn-primary" : "btn-outline-primary"
          }" onclick='openInputFile("input-pago1-${
        supplier.id_coordination
      }","${supplier.pago_1_URL}")' id="btn-pago1-${
        supplier.id_coordination
      }">Voucher</div>
          <span class="btn btn-danger mt-1 mx-auto" onclick="setInputFileToNull('pago1','${
            supplier.id_coordination
          }')">Quitar</span>
          <input type="hidden" id="input-pago1-url-${
            supplier.id_coordination
          }" name="coordination[${
        supplier.id_coordination
      }][pago_1_url]" value="${supplier.pago_1_URL}"/>
          <input type="file" class="form-control d-none" id="input-pago1-${
            supplier.id_coordination
          }" name="coordination[${supplier.id_coordination}][pago_1_file]"/>
        </div>`;
    } else {
      html += `
          <div class="c-pago1-column  px-2 supplier-column">
          <div class="input-group d-flex flex-row">
            <span class="input-group-text d-flex w-auto">¥</span>
          <input type="number" class="form-control" value="${supplier.pago_1_value}" name="coordination[${supplier.id_coordination}][pago_1_value]"/>
          </div>
          </div>`;
    }
    if (currentPrivilege == priviligesJefeChina) {
      html += `<div class="c-pago2-column supplier-column">
      <div class="input-group d-flex flex-row">
            <span class="input-group-text d-flex w-auto">¥</span>
          <input type="number" class="form-control" disabled value="${
            parseFloat(total) - parseFloat(supplier.pago_1_value)
          }" name="coordination[${supplier.id_coordination}][pago_2_value]"/>
          <div class="btn mt-1 mx-auto ${
            supplier.pago_2_URL == null ? "btn-primary" : "btn-outline-primary"
          }" onclick='openInputFile("input-pago2-${
        supplier.id_coordination
      }","${supplier.pago_2_URL}")' id="btn-pago2-${
        supplier.id_coordination
      }">Voucher</div>
          <span class="btn btn-danger mt-1 mx-auto" onclick="setInputFileToNull('pago2','${
            supplier.id_coordination
          }')">Quitar</span>
          <input type="file" class="form-control d-none" id="input-pago2-${
            supplier.id_coordination
          }" name="coordination[${supplier.id_coordination}][pago_2_file]"/>
          <input type="hidden" id="input-pago2-url-${
            supplier.id_coordination
          }" name="coordination[${
        supplier.id_coordination
      }][pago_2_url]" value="${supplier.pago_2_URL}"/>
        </div>
          </div>`;
    } else {
      html += `<div class="c-pago2-column supplier-column">
      <div class="input-group d-flex flex-row">
            <span class="input-group-text d-flex w-auto">¥</span>
          <input type="number" class="form-control" disabled value="${
            parseFloat(total) - parseFloat(supplier.pago_1_value)
          }" name="coordination[${supplier.id_coordination}][pago_2_value]"/>
          </div>
          </div>`;
    }
    html += `<div class="c-tproducto-column supplier-column">`;
    detalles.forEach((detalle) => {
      html += ` <div class="h-100 item px-2">
          <input type="text" class="form-control" value="${detalle.delivery}" name="proveedor[${detalle.ID_Pedido_Detalle_Producto_Proveedor}][delivery]"/>
        </div>`;
    });
    html += `</div>`;
    html += `
    <div class="c-tentrega-column supplier-column"  style="height:${
      detailsCount * defaultHeight
    }px">
      <input type="date" class="form-control" value="${
        detalles[0].tentrega.split(" ")[0]
      }" name="proveedor[${
      detalles[0].ID_Pedido_Detalle_Producto_Proveedor
    }][tentrega]"/>
    </div>`;
    html += `
    <div class="c-estado-column supplier-column"> 
          <select class="form-select" aria-label="Default select example" name="coordination[${
            supplier.id_coordination
          }][estado]"

          ${
            currentPrivilege == priviligesJefeChina
              ? "style='pointer-events:none'"
              : ""
          } >
            <option value="PENDIENTE" ${
              supplier.estado == "PENDIENTE" ? "selected" : ""
            }>PENDIENTE</option>
            <option value="CONFORME" ${
              supplier.estado == "CONFORME" ? "selected" : ""
            }>CONFORME</option>
          </select>
        </div>
        `;
    if (currentPrivilege == priviligesJefeChina) {
      html += `
          <div class="c-negociacion-column supplier-column">
          <select class="form-select" aria-label="Default select example" name="coordination[${
            supplier.id_coordination
          }][estado_negociacion]">
            <option value="PENDIENTE" ${
              supplier.estado_negociacion == "PENDIENTE" ? "selected" : ""
            }>PENDIENTE</option>
            <option value="ADELANTADO" ${
              supplier.estado_negociacion == "ADELANTADO" ? "selected" : ""
            }>ADELANTADO</option>
            <option value="PAGADO" ${
              supplier.estado_negociacion == "PAGADO" ? "selected" : ""
            }>PAGADO</option>
          </select>
        </div>
        `;
    }
    html += `</div>`;
    // detalles.forEach((detail) => {
    //   html += `
    //   <div class="detail-row">

    //   </div>`;
    // });
  });

  html += `</div></form>`;
  return html;
};

const downloadSupplierExcel = (id_pedido, id_supplier, id_coordination) => {
  url = base_url + "AgenteCompra/PedidosPagados/downloadSupplierExcel";
  $.ajax({
    url: url,
    type: "POST",
    xhrFields: {
      responseType: "blob",
    },

    data: {
      idPedido: id_pedido,
      idSupplier: id_supplier,
      idCoordination: id_coordination,
    },
    success: function (response) {
      //return blob to download
      var blob = new Blob([response], {
        type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
      });
      var link = document.createElement("a");
      link.href = window.URL.createObjectURL(blob);
      const currentDate = new Date();
      //format date to dd_mm_yyyy
      const formattedDate = `${currentDate.getDate()}_${
        currentDate.getMonth() + 1
      }_${currentDate.getFullYear()}`;
      link.download = "Cotizacion_" + formattedDate + ".xlsx";
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.log(jqXHR.responseText);
    },
  });
};
const openSupplierItems = (id_pedido, id_supplier, id_coordination) => {
  console.log(id_pedido, id_supplier, id_coordination, "openSupplierItems");
  $.ajax({
    url: base_url + "AgenteCompra/PedidosPagados/getSupplierItems",
    type: "POST",
    data: {
      idPedido: id_pedido,
      idSupplier: id_supplier,
      idCoordination: id_coordination,
    },
    success: function (response) {
      response = JSON.parse(response);
      if (response.status == "success") {
        const data = response.data;
        const btnsConfig = {
          btnSave: {
            text: "Guardar",
            action: `saveSupplierItems(${id_pedido},${id_supplier},${id_coordination})`,
          },
          btnCancel: {
            text: "Cancelar",
            action: "returnToCoordination()",
          },
        };
        console.log(data);
        openSupplierItemsView(data, btnsConfig, id_coordination);
        containerCoordination.hide();
        $(".orden-compra_header").hide();
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.log(jqXHR.responseText);
    },
  });
};
const saveSupplierItems = (id_pedido, id_supplier, id_coordination) => {
  const form = $("#table-elegir_productos_proveedor");
  const formData = new FormData(form[0]);
  url = base_url + "AgenteCompra/PedidosPagados/saveSupplierItems";
  $.ajax({
    url: url,
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    success: function (response) {
      response = JSON.parse(response);
      if (response.status == "success") {
        returnToCoordination();
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.log(jqXHR.responseText);
    },
  });
};
const openSupplierItemsView = (detalles, btnsConfig, idCoordination) => {
  console.log(detalles, "openSupplierItemsView", idCoordination);
  const container = $("#table-elegir_productos_proveedor");
  container.empty();
  container.show();
  $(".orden-compra_header").hide();
  for (i = 0; i < detalles.length; i++) {
    let item = getItemTemplate(i + 1, "select", detalles[i], idCoordination);
    container.append(item);
    container.find(`#modal-precio${i + 1}`).val(detalles[i]["Ss_Precio"]);
    container.find(`#modal-moq${i + 1}`).val(detalles[i]["Qt_Producto_Moq"]);
    container.find(".modal_coordination_id").val(idCoordination);
    container
      .find(`#modal_proveedor-id-${i + 1}`)
      .val(detalles[i]["ID_Pedido_Detalle"]);
    container
      .find(`#modal-qty_caja${i + 1}`)
      .val(detalles[i]["Qt_Producto_Caja"]);
    container.find(`#modal-cbm${i + 1}`).val(detalles[i]["Qt_Cbm"]);
    container
      .find(`#modal-delivery${i + 1}`)
      .val(detalles[i]["Nu_Dias_Delivery"]);
    container
      .find(`#modal-costo_delivery${i + 1}`)
      .val(detalles[i]["Ss_Costo_Delivery"]);
    container
      .find(`#modal-nombre_proveedor${i + 1}`)
      .val(detalles[i]["No_Contacto_Proveedor"]);
    container
      .find(`#modal-celular_proveedor${i + 1}`)
      .val(detalles[i]["No_Celular_Contacto_Proveedor"]);
    container
      .find(`#modal-detalle${i + 1}`)
      .val(detalles[i]["ID_Pedido_Detalle_Producto_Proveedor"]);
    container
      .find(`#modal-pedido-cabecera${i + 1}`)
      .val(detalles[i]["ID_Pedido_Cabecera"]);
    container
      .find(`#modal-nombre_proveedor${i + 1}`)
      .val(detalles[i]["nombre_proveedor"]);
    container
      .find(`#modal-celular_proveedor${i + 1}`)
      .val(detalles[i]["celular_proveedor"]);

    container
      .find(`#btn-uploadprimaryimg-URL-${i + 1}`)
      .val(detalles[i]["main_photo"]);
    if (detalles[i]["main_photo"] != null) {
      container
        .find(`#btn-uploadprimaryimg-URL-${i + 1}`)
        .val(detalles[i]["main_photo"]);
      container.find(`#btn-uploadprimaryimg-${i + 1}`).hide();
      container
        .find(`#container-uploadprimaryimg-${i + 1}`)
        .append(
          `<img src="${detalles[i]["main_photo"]}" class="img-thumbnail img-table_item img-fluid img-resize mb-2">`
        );
    }
    if (detalles[i]["secondary_photo"] != null) {
      container
        .find(`#btn-uploadimg2-URL-${i + 1}`)
        .val(detalles[i]["secondary_photo"]);
      container.find(`#btn-uploadimg2-${i + 1}`).hide();
      container
        .find(`#container-uploadimg2-${i + 1}`)
        .append(
          `<img src="${detalles[i]["secondary_photo"]}" class="img-thumbnail img-table_item img-fluid img-resize mb-2">`
        );
    }
    if (detalles[i]["terciary_photo"] != null) {
      container
        .find(`#btn-uploadimg3-URL-${i + 1}`)
        .val(detalles[i]["terciary_photo"]);
      container.find(`#btn-uploadimg3-${i + 1}`).hide();
      container
        .find(`#container-uploadimg3-${i + 1}`)
        .append(
          `<img src="${detalles[i]["terciary_photo"]}" class="img-thumbnail img-table_item img-fluid img-resize mb-2">`
        );
    }
    if (detalles[i]["primary_video"] != null) {
      container
        .find(`#btn-uploadvideo1-URL-${i + 1}`)
        .val(detalles[i]["primary_video"]);
      container.find(`#btn-uploadvideo1-${i + 1}`).hide();
      container
        .find(`#container-uploadvideo1-${i + 1}`)
        .append(
          `<video src="${detalles[i]["primary_video"]}" class="img-thumbnail img-table_item img-fluid img-resize mb-2 w-100" controls></video>`
        );
    }
    if (detalles[i]["secondary_video"] != null) {
      container
        .find(`#btn-uploadvideo2-URL-${i + 1}`)
        .val(detalles[i]["secondary_video"]);
      container.find(`#btn-uploadvideo2-${i + 1}`).hide();
      container
        .find(`#container-uploadvideo2-${i + 1}`)
        .append(
          "<video src='" +
            detalles[i]["secondary_video"] +
            "' class='img-thumbnail img-table_item img-fluid img-resize mb-2 w-100' controls></video>"
        );
    }
  }
  container.append(getActionButtons(btnsConfig));
};
const getSupplierCoordinationHeader = (data) => {
  let montoTotal = 0;
  let primerPago = 0;
  let segundoPago = 0;
  data.forEach((supplier) => {
    console.log(supplier.detalles);
    const detalles = JSON.parse(supplier.detalles);
    detalles.forEach((detail) => {
      montoTotal +=
        parseFloat(detail.total_producto) + parseFloat(detail.shipping_cost);
    });
    primerPago += parseFloat(supplier.pago_1_value);
  });
  segundoPago = montoTotal - primerPago;
  return `
  <div class="row coordination-header ">
    <div class="col-12 col-lg-3">
      <label>MONTO TOTAL:</label>
         <div class="input-group d-flex flex-row">
            <span class="input-group-text d-flex w-auto">¥</span>
      <input type="number" class="form-control" value="${montoTotal}" disabled>
      </div>
    </div>
    <div class="col-12 col-lg-3">
      <label>PRIMER PAGO:</label>
         <div class="input-group d-flex flex-row">
            <span class="input-group-text d-flex w-auto">¥</span>
      <input type="number" class="form-control" value="${primerPago}" disabled>
      </div>
    </div>
    <div class="col-12 col-lg-3">
      <label>SEGUNDO PAGO:</label>
         <div class="input-group d-flex flex-row">
            <span class="input-group-text d-flex w-auto">¥</span>
      <input type="number" class="form-control" value="${segundoPago}" disabled>
      </div>
    </div>
  </div>
  `;
};

const openInputFile = (idInput, fileURL) => {
  if (fileURL != null && fileURL != "" && fileURL != "null") {
    //open in new tab
    window.open(fileURL);
  } else {
    $(`#${idInput}`).click();
    //remove change event and add new one if file is selected change class of button btn-primary to btn-outline-primary

    $(`#${idInput}`).change(function () {
      const idButton = idInput.replace("input", "btn");
      $(`#${idButton}`).removeClass("btn-primary");
      $(`#${idButton}`).addClass("btn-outline-primary");
    });
  }
};
const openRotuladofromCoordination = (id) => {
  $.ajax({
    url: base_url + "AgenteCompra/PedidosPagados/getProductData",
    type: "POST",
    data: { idProducto: id },
    success: function (response) {
      response = JSON.parse(response);
      if (response.status == "success") {
        const data = response.data;

        containerCoordination.hide();
        const btnsConfig = {
          btnSave: {
            text: "Guardar",
            action: `saveRotuladoProducto(${JSON.stringify(productoSelected)})`,
          },
          btnCancel: {
            text: "Cancelar",
            action: "returnToCoordination()",
          },
        };
        openRotuladoView(data, btnsConfig);
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.error(jqXHR.responseText);
    },
  });
};
const returnToCoordination = () => {
  hideRotuladoView();
  const container = $("#table-elegir_productos_proveedor");
  $(".orden-compra_header").hide();
  container.hide();
  containerCoordination.show();
};
const getPagosTemplate = (data = null) => {
  let html = `
  <div class="py-1 px-4">
      <div class="row ">
      <div class="col-12 col-md-3">
        <h2 class="add-payments-container">Garantia</h2>
        </div>
        <div class="col-12 col-md-3">
        <h2 class="add-payments-container">Liquidacion</h2>
        </div>
        <div class="col-12 col-md-3">
        <h2 class="add-payments-container">Notas</h2>
        </div>
      </div>
      <div class="row liquidacion-garantia-container mb-2">
        <div class="col-12 col-md-3">

          <div class="payment-container" style="height:100%!important">
            <div class="garantia-container  upload-payment  container-div not-filled garantia-container" onclick="openFileSelector('garantia-file')">
                
                <?xml version="1.0" encoding="utf-8"?><!-- Uploaded to: SVG Repo, www.svgrepo.com, Generator: SVG Repo Mixer Tools -->
                <svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 15L12 2M12 2L15 5.5M12 2L9 5.5" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M8 22.0002H16C18.8284 22.0002 20.2426 22.0002 21.1213 21.1215C22 20.2429 22 18.8286 22 16.0002V15.0002C22 12.1718 22 10.7576 21.1213 9.8789C20.3529 9.11051 19.175 9.01406 17 9.00195M7 9.00195C4.82497 9.01406 3.64706 9.11051 2.87868 9.87889C2 10.7576 2 12.1718 2 15.0002L2 16.0002C2 18.8286 2 20.2429 2.87868 21.1215C3.17848 21.4213 3.54062 21.6188 4 21.749" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </div>
            <input type="file" id="garantia-file" class="d-none" name="garantia[file]"/>
            <div id="garantia-name" class="payment-name container-div garantia-container">
            Seleccionar archivo</div>
            <div class="input-group ">
            <span class="input-group-text">$</span>
            <input type="number" class="form-control " name="garantia[value]" id="garantia-value">
          </div>
          </div>
          </div>
        <div class="col-12 col-md-3">

          <div class="payment-container" style="height:100%!important">
            <div class="liquidacion-container  upload-payment  container-div not-filled liquidacion-container" onclick="openFileSelector('liquidacion-file')">
                
                <?xml version="1.0" encoding="utf-8"?><!-- Uploaded to: SVG Repo, www.svgrepo.com, Generator: SVG Repo Mixer Tools -->
                <svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 15L12 2M12 2L15 5.5M12 2L9 5.5" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M8 22.0002H16C18.8284 22.0002 20.2426 22.0002 21.1213 21.1215C22 20.2429 22 18.8286 22 16.0002V15.0002C22 12.1718 22 10.7576 21.1213 9.8789C20.3529 9.11051 19.175 9.01406 17 9.00195M7 9.00195C4.82497 9.01406 3.64706 9.11051 2.87868 9.87889C2 10.7576 2 12.1718 2 15.0002L2 16.0002C2 18.8286 2 20.2429 2.87868 21.1215C3.17848 21.4213 3.54062 21.6188 4 21.749" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </div>
            <input type="file" id="liquidacion-file" class="d-none" name="liquidacion[file]"/>
            <div id="liquidacion-name" class="payment-name container-div liquidacion-container">
            Seleccionar archivo</div>
            <input type="hidden" id="liquidacion-value" name="liquidacion[value]" value="0">
          </div>
        </div>
         <div class="col-12 col-md-6">
         <textarea class="form-control" name="notas" id="liquidacion-description" rows="3" style="height:100%"></textarea>
         </div>
        </div>
          <div class="row mt-5">
          <h2 class="add-payments-container">Pagos <span class="add-payments-button" onclick="addPago()">+</h2>
          </span>
        </div>
    <div class="row payments-container">
      <div class="col-12 col-md-3">    
        <div class="payment-container">
          <div class="upload-payment container-div not-filled pago1-container" id="pago1-container" onclick="openFileSelector('pago1-file')">
              
              <?xml version="1.0" encoding="utf-8"?><!-- Uploaded to: SVG Repo, www.svgrepo.com, Generator: SVG Repo Mixer Tools -->
              <svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M12 15L12 2M12 2L15 5.5M12 2L9 5.5" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M8 22.0002H16C18.8284 22.0002 20.2426 22.0002 21.1213 21.1215C22 20.2429 22 18.8286 22 16.0002V15.0002C22 12.1718 22 10.7576 21.1213 9.8789C20.3529 9.11051 19.175 9.01406 17 9.00195M7 9.00195C4.82497 9.01406 3.64706 9.11051 2.87868 9.87889C2 10.7576 2 12.1718 2 15.0002L2 16.0002C2 18.8286 2 20.2429 2.87868 21.1215C3.17848 21.4213 3.54062 21.6188 4 21.749" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"/>
              </svg>
          </div>
          <input type="file" id="pago1-file" class="d-none" name="file[1][file]"/>
          <div id="pago1-name" class="payment-name container-div pago1-container">
          Seleccionar archivo</div>
          <div class="input-group ">
            <span class="input-group-text" >$</span>
            <input type="text" class="form-control pago-value" name="file[1][value]" id="pago1-value" value="0">
          </div>
        </div>
      </div>
      <div class="col-12 col-md-3 ">    
        <div class="payment-container">
          <div class="upload-payment container-div not-filled pago2-container" id="pago2-container" onclick="openFileSelector('pago2-file')">
              
              <?xml version="1.0" encoding="utf-8"?><!-- Uploaded to: SVG Repo, www.svgrepo.com, Generator: SVG Repo Mixer Tools -->
              <svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M12 15L12 2M12 2L15 5.5M12 2L9 5.5" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M8 22.0002H16C18.8284 22.0002 20.2426 22.0002 21.1213 21.1215C22 20.2429 22 18.8286 22 16.0002V15.0002C22 12.1718 22 10.7576 21.1213 9.8789C20.3529 9.11051 19.175 9.01406 17 9.00195M7 9.00195C4.82497 9.01406 3.64706 9.11051 2.87868 9.87889C2 10.7576 2 12.1718 2 15.0002L2 16.0002C2 18.8286 2 20.2429 2.87868 21.1215C3.17848 21.4213 3.54062 21.6188 4 21.749" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"/>
              </svg>
          </div>
          <input type="file" id="pago2-file" class="d-none" name="file[2][file]" />
          <div id="pago2-name" class="payment-name container-div pago2-container">
          Seleccionar archivo</div>
          <div class="input-group ">
            <span class="input-group-text" >$</span>
            <input type="number" class="form-control pago-value" name="file[2][value]" id="pago2-value" value="0">
          </div>
        </div>
      </div>
    </div>
    <div class="row" id="pagos-buttons">
    </div>

      
  </div>
  `;

  return html;
};
const openPagos = (response) => {
  $("#container_orden-compra").hide();
  $("#pago-garantia-container").hide();
  response = JSON.parse(response);
  containerPagos.show();
  $("#pagos-form").append(getPagosTemplate());
  const downloadSvg = `<?xml version="1.0" encoding="utf-8"?><!-- Uploaded to: SVG Repo, www.svgrepo.com, Generator: SVG Repo Mixer Tools -->
<svg width="120px" height="120px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M8 22.0002H16C18.8284 22.0002 20.2426 22.0002 21.1213 21.1215C22 20.2429 22 18.8286 22 16.0002V15.0002C22 12.1718 22 10.7576 21.1213 9.8789C20.3529 9.11051 19.175 9.01406 17 9.00195M7 9.00195C4.82497 9.01406 3.64706 9.11051 2.87868 9.87889C2 10.7576 2 12.1718 2 15.0002L2 16.0002C2 18.8286 2 20.2429 2.87868 21.1215C3.17848 21.4213 3.54062 21.6188 4 21.749" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"/>
<path d="M12 2L12 15M12 15L9 11.5M12 15L15 11.5" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
</svg>`;

  pagosCount = 2;
  if (response.status == "success") {
    const data = response.data;

    $("#orden_total").html("$" + data.orden_total.toFixed(2));
    $("#pago_cliente").html("$" + data.pago_cliente.toFixed(2));
    const pagoRestante = data.orden_total - data.pago_cliente;
    $("#pago_restante").html("$" + pagoRestante.toFixed(2));
    //set font bold
    $("#pago_restante").css("font-weight", "bold");
    if (pagoRestante <= 0) {
      //add class text success
      $("#pago_restante").addClass("text-success");
    } else {
      $("#pago_restante").removeClass("text-success");
      $("#pago_restante").addClass("text-danger");
    }
    const ordenTotal = data.orden_total;
    //on input file change set filename to div
    $(".pago-value").each(function () {
      $(this).on("input", function () {
        const value = $(this).val();
        if (value == "") value = 0;
        let pagoClienteSum = 0;
        //if is number
        if (!isNaN(value)) {
          $(".pago-value").each(function () {
            const value = $(this).val();
            if (!isNaN(value)) {
              pagoClienteSum += parseFloat(value);
              $("#pago_cliente").html("$" + pagoClienteSum);
              const restante = ordenTotal - pagoClienteSum;
              if (restante <= 0) {
                $("#pago_restante").removeClass("text-danger");
                $("#pago_restante").addClass("text-success");
              } else {
                $("#pago_restante").removeClass("text-success");
                $("#pago_restante").addClass("text-danger");
              }
              $("#pago_restante").html("$" + restante);
            }
          });
        }
      });
    });
    $("#liquidacion-file").change(function () {
      const file = $(this)[0].files[0];
      if (file) {
        const fileName = file.name;
        if (fileName.length > 15) {
          $("#liquidacion-name").html(fileName.substring(0, 15) + "...");
        } else {
          $("#liquidacion-name").html(fileName);
        }
        $(".liquidacion-container")
          .removeClass("not-filled")
          .addClass("filled");
      } else {
        $("#liquidacion-name").html("Seleccionar archivo");
        $(".liquidacion-container")
          .removeClass("filled")
          .addClass("not-filled");
      }
    });
    $("#garantia-file").change(function () {
      const file = $(this)[0].files[0];
      if (file) {
        const fileName = file.name;
        if (fileName.length > 15) {
          $("#garantia-name").html(fileName.substring(0, 15) + "...");
        } else {
          $("#garantia-name").html(fileName);
        }
        $(".garantia-container").removeClass("not-filled").addClass("filled");
      } else {
        $("#garantia-name").html("Seleccionar archivo");
        $(".garantia-container").removeClass("filled").addClass("not-filled");
      }
    });
    $("#pago1-file").change(function () {
      const file = $(this)[0].files[0];
      if (file) {
        const fileName = file.name;
        if (fileName.length > 15) {
          $("#pago1-name").html(fileName.substring(0, 15) + "...");
        } else {
          $("#pago1-name").html(fileName);
        }
        $(".pago1-container").removeClass("not-filled").addClass("filled");
      } else {
        $("#pago1-name").html("Seleccionar archivo");
        $(".pago1-container").removeClass("filled").addClass("not-filled");
      }
    });
    $("#pago2-file").change(function () {
      const file = $(this)[0].files[0];
      if (file) {
        const fileName = file.name;
        if (fileName.length > 15) {
          $("#pago2-name").html(fileName.substring(0, 15) + "...");
        } else {
          $("#pago2-name").html(fileName);
        }
        $(".pago2-container").removeClass("not-filled").addClass("filled");
      } else {
        $("#pago2-name").html("Seleccionar archivo");
        $(".pago2-container").removeClass("filled").addClass("not-filled");
      }
    });
    //pagosData iterate

    if (response.pagosData) {
      const pagosData = response.pagosData;
      let pagocontainer;
      let pagosN = pagosData.filter((pago) => pago.name == "normal");
      let pagosG = pagosData.filter((pago) => pago.name == "garantia");
      let pagosL = pagosData.filter((pago) => pago.name == "liquidacion");
      pagosL.forEach((pago, i) => {
        pagocontainer = $(`.liquidacion-container`);
        pagocontainer.empty();
        $(`#liquidacion-description`).val(pago.description);
        pagocontainer.append(
          `<span class="remove-item" onclick="openFileSelector('liquidacion-file')">${editIcon}</span>`
        );
        if (pago.idPayment) {
          $(`.liquidacion-container`).append(
            `<input type="hidden" name="liquidacion[id]" value="${pago.idPayment}">`
          );
        }
        if (pago.file_url != null) {
          pagocontainer.append(downloadSvg);

          const fileName = pago.file_url.split("_").pop();
          if (fileName.length > 15) {
            $(`#liquidacion-name`).html(fileName.substring(0, 15) + "...");
          } else {
            $(`#liquidacion-name`).html(fileName);
          }
          $(`.liquidacion-container`)
            .removeClass("not-filled")
            .addClass("filled");

          //remove click ontag property
          $(`.liquidacion-container`).removeAttr("onclick");
          $(`.liquidacion-container`).click(function () {
            const fileExtension = pago.file_url.split(".").pop();
            if (fileExtension == "pdf") {
              downloadFile(pago.file_url);
              return;
            }
            openInputFile(`liquidacion-file`, pago.file_url);
          });
        }
      });
      pagosG.forEach((pago, i) => {
        pagocontainer = $(`.garantia-container`);

        if (pago.idPayment) {
          $(`.garantia-container`).append(
            `<input type="hidden" name="garantia[id]" value="${pago.idPayment}">`
          );
        }
        $(`#garantia-value`).val(pago.value);
        if (pago.file_url != null) {
          pagocontainer.empty();
          pagocontainer.append(downloadSvg);
          if (pago.idPayment) {
            $(`.garantia-container`).append(
              `<input type="hidden" name="garantia[id]" value="${pago.idPayment}">`
            );
          }
          const fileName = pago.file_url.split("_").pop();
          if (fileName.length > 15) {
            $(`#garantia-name`).html(fileName.substring(0, 15) + "...");
          } else {
            $(`#garantia-name`).html(fileName);
          }
          $(`.garantia-container`).removeClass("not-filled").addClass("filled");
          $(`.garantia-container`).append(
            `<input type="hidden" name="garantia[id]" value="${pago.idPayment}">`
          );
          //remove click ontag property
          $(`.garantia-container`).removeAttr("onclick");
          $(`.garantia-container`).click(function () {
            const fileExtension = pago.file_url.split(".").pop();
            if (fileExtension == "pdf") {
              downloadFile(pago.file_url);
              return;
            }
            openInputFile(`garantia-file`, pago.file_url);
          });
        }
      });
      pagosN.forEach((pago, i) => {
        const currenIndex = i + 1;
        console.log(currenIndex);
        if (currenIndex <= 2) {
          pagocontainer = $(`.pago${currenIndex}-container`);

          if (pago.idPayment) {
            $(`#pago${currenIndex}-container`).append(
              `<input type="hidden" name="file[${currenIndex}][id]" value="${pago.idPayment}">`
            );
          }

          // $(`.pago${currenIndex}-container`).val(pago.value);
          $(`#pago${currenIndex}-value`).val(pago.value);

          if (pago.file_url != null) {
            pagocontainer.empty();
            if (pago.idPayment) {
              $(`#pago${currenIndex}-container`).append(
                `<input type="hidden" name="file[${currenIndex}][id]" value="${pago.idPayment}">`
              );
            }
            pagocontainer.append(
              `<span class="remove-item" onclick="openFileSelector('pago${currenIndex}-file')">${editIcon}</span>`
            );

            pagocontainer.append(downloadSvg);
            const fileName = pago.file_url.split("_").pop();
            if (fileName.length > 15) {
              $(`#pago${currenIndex}-name`).html(
                fileName.substring(0, 15) + "..."
              );
            } else {
              $(`#pago${currenIndex}-name`).html(fileName);
            }
            $(`.pago${currenIndex}-container`)
              .removeClass("not-filled")
              .addClass("filled");

            //remove click ontag property
            $(`.pago${currenIndex}-container`).removeAttr("onclick");
            $(`.pago${currenIndex}-container`).click(function () {
              const fileExtension = pago.file_url.split(".").pop();
              if (fileExtension == "pdf") {
                downloadFile(pago.file_url);
                return;
              }
              openInputFile(`pago${currenIndex}-file`, pago.file_url);
            });
          }
        } else {
          addPago();
          pagocontainer = $(`.pago${currenIndex}-container`);
          $(`#pago${currenIndex}-remove`)
            .unbind("click")
            .click(function () {
              deletePago(currenIndex, pago.idPayment);
            });
          $(`#pago${currenIndex}-value`).val(pago.value);
          if (pago.idPayment) {
            $(`.pago${currenIndex}-container`).append(
              `<input type="hidden" name="file[${currenIndex}][id]" value="${pago.idPayment}">`
            );
          }
          if (pago.file_url != null) {
            pagocontainer.empty();
            if (pago.idPayment) {
              $(`.pago${currenIndex}-container`).append(
                `<input type="hidden" name="file[${currenIndex}][id]" value="${pago.idPayment}">`
              );
            }
            pagocontainer.append(downloadSvg);
            const fileName = pago.file_url.split("_").pop();
            if (fileName.length > 20) {
              $(`#pago${currenIndex}-name`).html(
                fileName.substring(0, 20) + "..."
              );
            } else {
              $(`#pago${currenIndex}-name`).html(fileName);
            }
            $(`.pago${currenIndex}-container`)
              .removeClass("not-filled")
              .addClass("filled");
            $(`.pago${currenIndex}-container`).removeAttr("onclick");
            $(`.pago${currenIndex}-container`).click(function () {
              const fileExtension = pago.file_url.split(".").pop();
              if (fileExtension == "pdf") {
                downloadFile(pago.file_url);
                return;
              }
              openInputFile(`pago${currenIndex}-file`, pago.file_url);
            });
          }
        }
      });
    }
    //   const pagosData = response.pagosData;
    //   const existsGarantia = pagosData.some((pago) => pago.name == "garantia");
    //   let indexPagos = 1;
    //   $("#pago-1-value").val(0);
    //   $("#pago-1_ID").remove();
    //   $("#pago-1-btnlink").remove();
    //   // $('#pago-1-div').append('<input type="file" name="pago-1" id="pago-1" class="">');
    //   $("#pago-2-value").val(0);
    //   $("#pago-2-btnlink").remove();
    //   $("#pago-2_ID").remove();
    //   // $('#pago-2-div').append('<input type="file" name="pago-2" id="pago-2" class="">');
    //   pagosData.forEach((pago, i) => {
    //     if (pago.name == "garantia") {
    //       $("#pago-garantia").hide();
    //       $("#pago-garantia_ID").remove();
    //       $("#pago-garantia-container").append(
    //         `<input type="hidden" name="pago-garantia_ID" id="pago-garantia_ID" value="${pago.idPayment}">`
    //       );
    //       $("#pago-garantia_URL").val(pago.file_url);
    //       $("#pago-garantia-div").append(`
    //         <a href="${pago.file_url}" id="pago-garantia-btnlink" class="btn btn-primary btn-ver-pago" target="_blank">Ver Garantia</a>`);
    //       $("#pago-garantia-value").val(pago.value);
    //       $(`#pago-${indexPagos}-btnlink`).remove();

    //       $(`#pago-${indexPagos}-div`).append(`
    //         <a href="${pago.file_url}" id="pago-${indexPagos}-btnlink" class="btn btn-outline-secondary btn-ver-pago" target="_blank">Ver Pago</a>`);
    //     } else if (pago.name == "normal") {
    //       $(`#pago-${indexPagos}`).show();
    //       if (pago.file_url != null && indexPagos <= 2) {
    //         $(`#pago-${indexPagos}_URL`).val(pago.file_url);
    //         $(`#pago-${indexPagos}`).hide();
    //         $(`#pago-${indexPagos}-div`).append(
    //           `<input type="hidden" name="pago-${indexPagos}_ID" id="pago-${indexPagos}_ID" value="${pago.idPayment}">`
    //         );
    //         $(`#pago-${indexPagos}-div`).append(`
    //           <a href="${pago.file_url}" id="pago-${indexPagos}-btnlink" class="btn btn-outline-secondary btn-ver-pago" target="_blank">Ver Pago</a>`);
    //         $(`#pago-${indexPagos}-div`).append(`
    //         <input type="hidden" name="pago-${indexPagos}_ID" id="pago-${indexPagos}_ID" value="${pago.idPayment}">`);
    //       }

    //       if (indexPagos > 2) {
    //         if (!$(`#pago-${indexPagos}-div`)) {
    //           $(`#pago-${indexPagos}-div`).append(
    //             `<input type="number" name="pago-${indexPagos}-value" id="pago-${indexPagos}-value" class="form-control w-25" placeholder="Valor" value="" autocomplete="off" />`
    //           );
    //         }
    //       }
    //       $(`#pago-${indexPagos}-value`).val(pago.value);

    //       indexPagos++;
    //     } else if (pago.name == "liquidacion") {
    //       $(`#liquidacion_URL`).val(pago.file_url);
    //       $(`#liquidacion`).hide();
    //       $(`#liquidacion_ID`).remove();
    //       $(`#liquidacion-container`).append(
    //         `<input type="hidden" name="liquidacion_ID" id="liquidacion_ID" value="${pago.idPayment}">`
    //       );
    //       $("#liquidacion_btnlink").remove();
    //       $(`#liquidacion-container`).append(`
    //         <a href="${pago.file_url}" id="liquidacion_btnlink" class="btn btn-outline-secondary btn-ver-pago" target="_blank">Ver Liquidacion</a>`);
    //     }
    //   });
    //   const pago_switch_3 = $(`#pago3_URL_switch`);
    //   const pago3Div = $(`#pago-3-div`);
    //   const pago_switch_4 = $(`#pago4_URL_switch`);
    //   const pago4Div = $(`#pago-4-div`);
    //   console.log(indexPagos);
    //   if (indexPagos > 3) {
    //     pago_switch_3.prop("checked", true);
    //     if (pagosData[2].file_url != null) {
    //       //append input hide with pago_ID
    //       if ($("#pago-3_ID").length == 0) {
    //         pago3Div.append(
    //           `<input type="hidden" name="pago-3_ID" id="pago-3_ID" value="${pagosData[2].idPayment}">`
    //         );
    //       }
    //       if ($("#pago-3_value").length == 0) {
    //         pago3Div.append(
    //           `<input type="number" name="pago-3-value" id="pago-3_value" class="form-control w-25" placeholder="Valor" value="${pagosData[2].value}" autocomplete="off" />`
    //         );
    //       }
    //       if ($(`#pago-3-btnlink`).length == 0) {
    //         $(`#pago-3-div`).append(`
    //             <a href="${pagosData[2].file_url}" id="pago-3-btnlink" class="btn btn-outline-secondary btn-ver-pago" target="_blank">Ver Pago</a>`);
    //       }
    //       if ($("#pago-3_URL").length == 0) {
    //         $(`#pago-3-div`).append(`
    //         <input type="hidden" name="pago-3_URL" id="pago-3_URL" value="${pagosData[2].file_url}">`);
    //       }
    //     } else {
    //       if ($("#pago3-file").length == 0) {
    //         pago3Div.append(
    //           '<input type="file" name="pago-3" id="pago3-file" class="" placeholder="" value="" autocomplete="off"></input>'
    //         );
    //       }
    //       if ($("#pago-3_value").length == 0) {
    //         pago3Div.append(
    //           `<input type="number" name="pago-3-value" id="pago-3_value" class="form-control w-25" placeholder="Valor" value="${pagosData[2].value}" autocomplete="off" />`
    //         );
    //       }
    //     }
    //   } else {
    //     pago_switch_3.prop("checked", false);
    //     $("pago3-file").remove();
    //     $("#pago-3-btnlink").remove();
    //   }
    //   pago_switch_3.change(function () {
    //     if ($(this).is(":checked")) {
    //       if (indexPagos > 3) {
    //         if (pagosData[2].file_url) {

    //           if ($("#pago-3_value").length == 0) {
    //             pago3Div.append(
    //               `<input type="number" name="pago-3-value" id="pago-3_value" class="form-control w-25" placeholder="Valor" value="" autocomplete="off" value="${pagosData[2].value}"/>`
    //             );
    //           }
    //           if ($(`#pago-3-btnlink`).length == 0) {
    //             $(`#pago-3-div`).append(`
    //               <a href="${pagosData[2].file_url}" id="pago-3-btnlink" class="btn btn-outline-secondary btn-ver-pago" target="_blank">Ver Pago</a>`);
    //           }
    //           if ($("#pago-3_URL").length == 0) {
    //             $(`#pago-3-div`).append(`
    //             <input type="hidden" name="pago-3_URL" id="pago-3_URL" value="${pagosData[2].file_url}">`);
    //           }
    //         } else {
    //           if ($("#pago3-file").length == 0) {
    //             pago3Div.append(
    //               '<input type="file" name="pago-3" id="pago3-file" class="" placeholder="" value="" autocomplete="off"></input>'
    //             );
    //           }
    //           if ($("#pago-3_value").length == 0) {
    //             pago3Div.append(
    //               `<input type="number" name="pago-3-value" id="pago-3_value" class="form-control w-25" placeholder="Valor" value="" autocomplete="off" value="${pagosData[2].value}"/>`
    //             );
    //           }
    //         }
    //       } else {
    //         if ($("#pago3-file").length == 0) {
    //           pago3Div.append(
    //             `<input type="file" name="pago-3" id="pago3-file" class="" placeholder="" value="" autocomplete="off" />`
    //           );
    //         }
    //         if ($("#pago-3_value").length == 0) {
    //           pago3Div.append(
    //             `<input type="number" name="pago-3-value" id="pago-3_value" class="form-control w-25" placeholder="Valor" value="" autocomplete="off" />`
    //           );
    //         }
    //         if ($(`#pago-3-btnlink`).length == 0) {
    //           $(`#pago-3-div`).append(`
    //             <a href="${pagosData[2].file_url}" id="pago-3-btnlink" class="btn btn-outline-secondary btn-ver-pago" target="_blank">Ver Pago</a>`);
    //         }
    //       }
    //     } else {
    //       pago3Div.find("#pago-3-btnlink").remove();
    //       pago3Div.find("#pago3-file").remove();
    //       pago3Div.find("#pago-3_value").remove();
    //       pago3Div.find(`#pago-3-btnlink`).remove();
    //     }
    //   });
    //   ///append an a tag with the link to the file

    //   if (indexPagos > 4) {
    //     pago_switch_4.prop("checked", false);

    //     if (pagosData[3].file_url != null) {
    //       pago_switch_4.prop("checked", true);
    //       if ($("#pago-4_ID").length == 0) {
    //         pago4Div.append(
    //           `<input type="hidden" name="pago-4_ID" id="pago-4_ID" value="${pagosData[3].idPayment}">`
    //         );
    //       }
    //       if ($("#pago-4_value").length == 0) {
    //         pago4Div.append(
    //           `<input type="number" name="pago-4-value" id="pago-4_value" class="form-control w-25" placeholder="Valor" value="${pagosData[3].value}" autocomplete="off" />`
    //         );
    //       }
    //       if ($(`#pago-4-btnlink`).length == 0) {
    //         $(`#pago-4-div`).append(`
    //           <a href="${pagosData[3].file_url}" id="pago-4-btnlink" class="btn btn-outline-secondary btn-ver-pago" target="_blank">Ver Pago</a>`);
    //       }
    //       if ($("#pago-4_URL").length == 0) {
    //         $(`#pago-4-div`).append(`
    //         <input type="hidden" name="pago-4_URL" id="pago-4_URL" value="${pagosData[2].file_url}">`);
    //       }
    //     } else {
    //       pago_switch_4.prop("checked", false);
    //       $("pago4-file").remove();
    //       $("#pago-4-btnlink").remove();
    //     }
    //   }
    //   pago_switch_4.change(function () {
    //     if ($(this).is(":checked")) {
    //       if (indexPagos > 4) {
    //         if (pagosData[3].file_url) {
    //           pago4Div.append(
    //             `<input type="number" name="pago-4-value" id="pago-4_value" class="form-control w-25" placeholder="Valor" value="" autocomplete="off"
    //             value="${pagosData[3].value}"/>`
    //           );
    //           pago4Div.append(
    //             `<a href="${pagosData[3].file_url}" id="pago-4-btnlink" class="btn btn-outline-secondary btn-ver-pago" target="_blank" name="pago-4_URL">Ver Pago</a>`
    //           );

    //           if ($(`#pago-4-btnlink`).length == 0 && indexPagos > 4) {
    //             $(`#pago-4-div`).append(`
    //               <a href="${pagosData[3].file_url}" id="pago-4-btnlink" class="btn btn-outline-secondary btn-ver-pago" target="_blank">Ver Pago</a>`);
    //           }
    //         }
    //       } else {
    //         console.log("append", pago4Div);
    //         if ($("#pago4-file").length == 0) {
    //           pago4Div.append(
    //             `<input type="file" name="pago-4" id="pago4-file" class="" placeholder="" value="" autocomplete="off" />`
    //           );
    //         }
    //         //append input type number
    //         if ($("#pago-4_value").length == 0) {
    //           pago4Div.append(
    //             `<input type="number" name="pago-4-value" id="pago-4_value" class="form-control w-25" placeholder="Valor" value="" autocomplete="off" />`
    //           );
    //         }
    //         if ($(`#pago-4-btnlink`).length == 0 && indexPagos > 4) {
    //           $(`#pago-4-div`).append(`
    //             <a href="${pagosData[3].file_url}" id="pago-4-btnlink" class="btn btn-outline-secondary btn-ver-pago" target="_blank">Ver Pago</a>`);
    //         }
    //       }
    //     } else {
    //       pago4Div.find("#pago-4-btnlink").remove();
    //       pago4Div.find("#pago4-file").remove();
    //       pago4Div.find("#pago-4_value").remove();
    //       pago4Div.find(`#pago-4-btnlink`).remove();
    //     }
    //   });
    //   //append a with
    //   if (!existsGarantia) {
    //     $("#pago-garantia-container").html("<div></div>");
    //   }
    const buttonsConfig = {
      btnSave: {
        text: "Guardar",
        action: "savePagos()",
      },
      btnCancel: {
        text: "Regresar",
        action: "hidePagos2()",
      },
    };
    const buttonsHTML = getActionButtons(buttonsConfig);
    $("#pagos-buttons").append(buttonsHTML);
    // }
  }
  $("#liquidacion-description").val(response.data.pagos_notas);
};
const addPago = () => {
  const pagoContainer = $(".payments-container");
  if (pagosCount < 4) {
    let currentPagoIndex = pagosCount + 1;
    const pagoTemplate = `<div class="col-12 col-md-3 "
  id="pago${currentPagoIndex}-container" >

        <div class="payment-container">
          <span class="remove-item" id="pago${currentPagoIndex}-remove">X</span>
          <div class="upload-payment container-div not-filled pago${currentPagoIndex}-container" onclick="openFileSelector('pago${currentPagoIndex}-file')">
            <?xml version="1.0" encoding="utf-8"?><!-- Uploaded to: SVG Repo, www.svgrepo.com, Generator: SVG Repo Mixer Tools -->
              <svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M12 15L12 2M12 2L15 5.5M12 2L9 5.5" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M8 22.0002H16C18.8284 22.0002 20.2426 22.0002 21.1213 21.1215C22 20.2429 22 18.8286 22 16.0002V15.0002C22 12.1718 22 10.7576 21.1213 9.8789C20.3529 9.11051 19.175 9.01406 17 9.00195M7 9.00195C4.82497 9.01406 3.64706 9.11051 2.87868 9.87889C2 10.7576 2 12.1718 2 15.0002L2 16.0002C2 18.8286 2 20.2429 2.87868 21.1215C3.17848 21.4213 3.54062 21.6188 4 21.749" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"/>
              </svg>
          </div>
          <input type="file" id="pago${currentPagoIndex}-file" class="d-none" name="file[${currentPagoIndex}][file]" />
          <div id="pago${currentPagoIndex}-name" class="payment-name pago${currentPagoIndex}-container" >
          Seleccionar archivo
          </div>
          <div class="input-group">
            <span class="input-group-text" >$</span>
            <input type="text"id="pago${currentPagoIndex}-value" class="form-control pago-value" name="file[${currentPagoIndex}][value]">
          </div>
        </div>
      </div>`;
    pagoContainer.append(pagoTemplate);

    const ordenTotal = parseFloat($("#orden_total").html().replace("$", ""));
    $(`#pago${currentPagoIndex}-file`).change(function () {
      const file = $(this)[0].files[0];
      if (file) {
        const fileName = file.name;
        if (fileName.length > 20) {
          $(`#pago${currentPagoIndex}-name`).html(
            fileName.substring(0, 20) + "..."
          );
        } else {
          $(`#pago${currentPagoIndex}-name`).html(fileName);
        }
        $(`.pago${currentPagoIndex}-container`)
          .removeClass("not-filled")
          .addClass("filled");
      } else {
        $(`#pago${currentPagoIndex}-name`).html("Seleccionar archivo");
        $(`.pago${currentPagoIndex}-container`)
          .removeClass("filled")
          .addClass("not-filled");
      }
    });
    $(".pago-value").each(function () {
      $(this).on("input", function () {
        const value = $(this).val();
        if (value == "") value = 0;
        let pagoClienteSum = 0;
        //if is number
        if (!isNaN(value)) {
          $(".pago-value").each(function () {
            const value = $(this).val();
            if (!isNaN(value)) {
              pagoClienteSum += parseFloat(value);
              $("#pago_cliente").html("$" + pagoClienteSum);
              const restante = ordenTotal - pagoClienteSum;
              if (restante <= 0) {
                $("#pago_restante").removeClass("text-danger");
                $("#pago_restante").addClass("text-success");
              } else {
                $("#pago_restante").removeClass("text-success");
                $("#pago_restante").addClass("text-danger");
              }
              $("#pago_restante").html("$" + restante);
            }
          });
        }
      });
    });
    $(`#pago${currentPagoIndex}-remove`).on("click", function () {
      deletePago(currentPagoIndex, null);
    });
    pagosCount++;
  }
};
const deletePago = (currentPagoIndex, idPayment = null) => {
  if (pagosCount > 2) {
    if (idPayment == null) {
      const pagoContainer = $("#pago" + currentPagoIndex + "-container");
      pagoContainer.remove();
      pagosCount--;
      return;
    }
    $(`#modal-confirmation`).modal("show");
    $(`#btn-confirmation`).html("Eliminar");
    $(`#btn-confirmation`).on("click", function () {
      $.ajax({
        url: base_url + "AgenteCompra/PedidosPagados/deletePago",
        type: "POST",
        data: {
          idPayment: idPayment,
        },
        success: function (response) {
          response = JSON.parse(response);
          if (response.status == "success") {
            const pagoContainer = $("#pago" + currentPagoIndex + "-container");
            pagoContainer.remove();
            pagosCount--;
            $(`#modal-confirmation`).modal("hide");
          }
        },
      });
    });
  }
};

const hidePagos2 = () => {
  hidePagos();

  getOrderProgress(idPedido, currentServicio);
};
const savePagos = () => {
  const form = $("#pagos-form");
  const formData = new FormData(form[0]);
  formData.append("idPedido", idPedido);
  formData.append("step", selectedStep);
  const url = base_url + "AgenteCompra/PedidosPagados/savePagos";
  $.ajax({
    url: url,
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    success: function (response) {
      response = JSON.parse(response);
      if (response.status == "success") {
        hidePagos();
        getOrderProgress(idPedido, currentServicio);
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.error(jqXHR.responseText);
    },
  });
};

const openOrdenCompra = (response) => {
  const { status, data, priviligie, pedidoData } = JSON.parse(response);

  if (status == "success") {
    //remove all the elements from the container with class row producto and row button
    $(".row.producto").remove();
    $(".row.buttons").remove();
    $(".orden-compra_header").show();
    currentPrivilege = parseInt(priviligie);
    // $(".orden-compra_header").show();
    // $(".orden-compra_header_china").append(getProductsTemplateHeader());
    data.forEach((producto, index) => {
      //escape special chars product.Txt_Descripcion

      containerOrdenCompra.append(getProductTemplate(producto, index));
      const toolbarOptions = [
        [], // toggled buttons
        // remove formatting button
      ];
      const quill = new Quill(`#quill-container-${index}`, {
        theme: "snow",
        readOnly: true,
        modules: {
          toolbar: null,
        },
      });
      quill.root.innerHTML = clearHTMLTextArea(producto.Txt_Descripcion);
      if (producto.caja_master_URL) {
        $(`#btn-rotulado-${index}`)
          .removeClass("btn-primary")
          .addClass("btn-outline-secondary");
      }
    });
    if (typeof pedidoData != "undefined") {
      pedidoData.total_rmb = pedidoData.total_rmb ?? 0;
      pedidoData.Ss_Tipo_Cambio = pedidoData.Ss_Tipo_Cambio ?? 0;
      const totalUSD =
        pedidoData.Ss_Tipo_Cambio == 0
          ? 0
          : pedidoData.total_rmb / pedidoData.Ss_Tipo_Cambio;
      $("#total-rmb").val(pedidoData.total_rmb);
      $("#tc").val(pedidoData.Ss_Tipo_Cambio);
      $("#total-usd").val(totalUSD);
    }
    let buttonsData = {};
    if (
      [priviligesPersonalChina, priviligesJefeChina].includes(currentPrivilege)
    ) {
      $("#btn-rotulado").hide();
      buttonsData = {
        btnSave: {
          text: "Verificar",
          action: `saveOrdenCompra()`,
        },
        btnCancel: {
          text: "Regresar",
          action: "hideOrdenCompra()",
        },
      };
      const butttonsTemplate = getActionButtons(buttonsData);
      containerOrdenCompra.append(butttonsTemplate);
    } else {
      buttonsData = {
        btnSave: {
          text: "Guardar",
          action: "saveOrdenCompra()",
        },
        btnCancel: {
          text: "Regresar",
          action: "hideOrdenCompra()",
        },
      };
      const btnsTemplate = getActionButtons(buttonsData);
      containerOrdenCompra.append(btnsTemplate);
    }
  }
};
function clearHTMLTextArea(str) {
  if (str == null) return "";
  str = str.replace(/<br>/gi, "");
  str = str.replace(/<br\s\/>/gi, "");
  str = str.replace(/<br\/>/gi, "");
  str = str.replace(/<\/button>/gi, "");
  str = str.replace(/<br >/gi, "");
  return str;
}
const escapeHtml = (unsafe) => {
  return unsafe
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
};
const htmlDecode = (input) => {
  const doc = new DOMParser().parseFromString(input, "text/html");
  return doc.documentElement.textContent;
};
const getProductsTemplateHeader = () => {
  let templateHeader = ``;

  if (
    currentPrivilege == priviligesPersonalChina ||
    currentPrivilege == priviligesJefeChina
  ) {
    templateHeader = `
    <form class="row" id="pedido-form">
      <!-- Column -->
      <div class="col-12 col-md-4">
        <div class="form-group">
          <label>TOTAL RMB:</label>
          <input type="number" name="total-rmb" class="form-control"  id="total-rmb">
        </div>
      </div>
      <!-- Column -->
      <div class="col-12 col-md-4">
        <div class="form-group">
          <label>T.C:</label>
          <input type="number" name="tc" class="form-control"  id="tc">
        </div>
      </div>
      <!-- Column -->
      <div class="col-12 col-md-4">
        <div class="form-group">
          <label>TOTAL USD:</label>
          <input type="number" name="total-usd" class="form-control"  id="total-usd" disabled>
        </div>
      </div>
    </form>`;
  }
  return templateHeader;
};
const htmltoTextAndLineBreaks = (html) => {
  const replacedText = html.replace(/&amp;lt;/g, "<").replace(/&amp;gt;/g, ">");
  const textWithLineBreaks = replacedText.replace(/<br \/>/g, "\n");
  const decodedText = htmlDecode(textWithLineBreaks);
  return decodedText;
};
const getProductTemplate = (producto, index) => {
  const productoCopy = { ...producto };
  productoCopy.Txt_Producto = "";
  productoCopy.Txt_Descripcion = "";
  productoCopy.Txt_Description_Ingles = "";

  const productoJson = JSON.stringify(productoCopy);
  const template = `
  <div class="row producto">
    <div class="col-12 col-lg-3">
      <img src="${producto.Txt_Url_Imagen_Producto}" alt="${
    producto.Txt_Producto
  }" class="img-cuz">
    </div>
    <div class="col-12 col-lg-2 d-flex flex-column justify-content-center">
      <span>${htmlDecode(escapeHtml(producto.Txt_Producto))}</span>
      ${
        currentPrivilege == priviligesPersonalPeru
          ? `<div class="btn btn-primary btn-rotulado " id="btn-rotulado-${index}"  onclick='openRotuladoView(${productoJson})'>Rotulado</div>`
          : `
      <span class="badge badge-success">ITEM CODE :${
        producto.product_code ? producto.product_code : ""
      }</span>`
      }
    </div>
    <div class="col-12 col-lg-2">
      <input class="form-control text-center input-cantidad w-100" type="number"
      ${currentPrivilege == priviligesPersonalPeru ? "" : "disabled"}
      name="addProducto[${
        producto.ID_Pedido_Detalle
      }][cantidad]" value="${parseInt(producto.Qt_Producto)}"/>
    </div>
    <div class="col-12 col-lg-3 d-flex flex-column">
          <div id="quill-container-${index}" 
          class="d-block w-100" ></div>
    </div>
    <div class="col-12 col-lg-2">
      <a href="${
        producto.Txt_Url_Link_Pagina_Producto
      }" target="_blank" class="btn btn-link" style="word-break: break-word;overflow:auto;max-height:200px">${
    producto.Txt_Url_Link_Pagina_Producto
  }</a>
    </div>
  </div>`;
  return template;
};
const openRotuladoView = (producto, btsconfig = null) => {
  $.ajax({
    url: base_url + "AgenteCompra/PedidosPagados/openRotuladoView",
    type: "POST",
    data: { ID_Detalle: producto.ID_Pedido_Detalle },
    success: function (response) {
      response = JSON.parse(response);
      const item = response.data;
      const stringItem = JSON.stringify(item);
      containerOrdenCompra.hide();
      containerRotulado.append(getContainerRotuladoView(item));
      let buttonsData = {
        btnSave: {
          text: "Guardar",
          action: `saveRotuladoProducto(${stringItem})`,
        },
        btnCancel: {
          text: "Regresar",
          action: "hideRotuladoView() ",
        },
      };
      if (btsconfig) {
        buttonsData = btsconfig;
      }
      const btnSection = $("#btns-section");
      btnSection.append(getActionButtons(buttonsData));
      const switchEmpaque = $("#empaque_URL_switch");
      if (item.empaque_URL) {
        switchEmpaque.prop("checked", true);
        const empaqueDiv = $("#empaque_container");
        empaqueDiv.append(`
          <div id="empaque_input-container">
            <input name="empaque_URL" type="hidden" value="${item.empaque_URL}">
            <div class="d-flex flex-row w-100">
              <div id="input-empaque" onclick="downloadFile('${item.empaque_URL})"  class="btn btn-outline-secondary d-block text-center w-75">Descargar</div>
              <button class="btn btn-outline-danger ml-2" id="delete-empaque"onclick="setRotuladoInputToNull('empaque')">X</button>
            </div>
          </div>
        `);
      }
      switchEmpaque.on("change", function () {
        const isChecked = $(this).prop("checked");
        const empaqueDiv = $("#empaque_container");

        if (isChecked) {
          $("#input-empaque").remove();
          empaqueDiv.append(`
            <div id="empaque_input-container">
              <input name="empaque_URL" type="hidden" value="${
                item.empaque_URL
              }">
              <div class="d-flex flex-row w-100">
              ${
                item.empaque_URL
                  ? `<div  id="input-empaque" onclick="downloadFile('${item.empaque_URL}')"  class="btn btn-outline-secondary d-block text-center w-75">Descargar</div>`
                  : `<input id="input-empaque" type="file" name="empaque" class="">`
              }
              <button class="btn btn-outline-danger ml-2" id="delete-empaque"onclick="setRotuladoInputToNull('empaque')">X</button>
              </div>
            </div>
          `);
        } else {
          empaqueDiv.find("#empaque_input-container").remove();
        }
      });
      const switchVim = $("#vim_motor_URL_switch");
      if (item.vim_motor_URL) {
        switchVim.prop("checked", true);
        const vimDiv = $("#vim_motor_container");
        vimDiv.append(`
          <div id="vim_motor_input-container">
            <input name="vim_motor_URL" type="hidden" value="${item.vim_motor_URL}">
            <div class="d-flex flex-row w-100">
              <div onclick="downloadFile('${item.vim_motor_URL}')" id="input-vim_motor" class="btn btn-outline-secondary d-block text-center w-75">Descargar</div>
              <button class="btn btn-outline-danger ml-2" id="delete-vim_motor"onclick="setRotuladoInputToNull('vim_motor')">X</button>
            </div>
          </div>
        `);
      }
      switchVim.on("change", function () {
        const isChecked = $(this).prop("checked");
        const vimDiv = $("#vim_motor_container");

        if (isChecked) {
          $("#input-vim_motor").remove();
          vimDiv.append(`
            <div id="vim_motor_input-container">
              <input name="vim_motor_URL" type="hidden" value="${
                item.vim_motor_URL
              }">
              <div class="d-flex flex-row w-100">
              ${
                item.vim_motor_URL
                  ? `<div onclick="downloadFile('${item.vim_motor_URL}')" id="input-vim_motor" class="btn btn-outline-secondary d-block text-center w-75">Descargar</div>`
                  : `<input type="file" name="vim_motor" class="">`
              }
              <button class="btn btn-outline-danger ml-2" id="delete-vim_motor"onclick="setRotuladoInputToNull('vim_motor')">X</button>
              </div>
            </div>
          `);
        } else {
          vimDiv.find("#vim_motor_input-container").remove();
        }
      });
      if (currentPrivilege != 1) {
        //if empaque_input-container not exists hide empaque-container
        if (!$("#empaque_input-container").length) {
          $("#empaque_container").hide();
        }
        //if vim_motor_input-container not exists hide vim_motor_container
        if (!$("#vim_motor_input-container").length) {
          $("#vim_motor_container").hide();
        }
        //find button save in rotulado view and hide it
        containerRotulado.find(".button-save").hide();
        containerRotulado.find(".switch").hide();
      }
      containerRotulado.show();
    },
  });

  //if currentprivilege is !=1
};
const saveRotuladoProducto = (producto) => {
  const url = base_url + "AgenteCompra/PedidosPagados/saveRotuladoProducto";
  //get form-rotulado
  const form = $("#form-rotulado");
  //get form data
  const formData = new FormData(form[0]);
  //append idPedido
  formData.append("idPedido", producto.ID_Pedido_Cabecera);
  formData.append("idProducto", producto.ID_Pedido_Detalle);
  formData.append("stepID", selectedStep);
  //post data
  $.ajax({
    url,
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (response) {
      const { status, data } = JSON.parse(response);
      if (status == "success") {
        hideRotuladoView();
        openStepFunction(1, selectedStep);
      } else {
        alert(message);
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.error(jqXHR.responseText);
    },
  });
};
const getContainerRotuladoView = (producto) => {
  const rotuladoTemplate = `
    <form class="row" id="form-rotulado">
      <div class="col-12 col-md-5">
        <div class="form-group" id="caja_master_container">
          <label>CAJA MASTER:</label>
          <input name="caja_master_URL" 
          id="caja_master-url"
          type="hidden" value="${producto.caja_master_URL}">
          <div class="d-flex flex-row w-100">
          ${
            producto.caja_master_URL
              ? `<div onclick="downloadFile('${producto.caja_master_URL}')" id="input-caja_master" class="btn btn-outline-secondary  w-75 d-block text-center" >Descargar</div>`
              : '<input type="file" name="caja_master" class="">'
          }
          ${
            producto.caja_master_URL
              ? "<div class='btn btn-outline-danger ml-2' id='delete-caja_master' onclick='setRotuladoInputToNull(\"caja_master\")'>X</div>"
              : ""
          }
          </div>
        </div>
        <div class="form-group" id="empaque_container">
          <div class="conditional-field">
            <label>EMPAQUE</label>
            <label class="switch">
              <input type="checkbox" id="empaque_URL_switch" >
              <span class="slider"></span>
            </label>
          </div>
        </div>
        <div class="form-group" id="vim_motor_container">
          <div class="conditional-field">
            <label>VIM/MOTOR</label>
            
            <label class="switch">
              <input type="checkbox" id="vim_motor_URL_switch" >
              <span class="slider"></span>
            </label>
          </div>
        </div>
        <div class="form-group" id="btns-section">
        </div>
        
      </div>
      <div class="col-12 col-md-7">
          <label>Notas</label>
          <textarea name="notas_rotulado" class="form-control" rows="5">${
            producto.notas_rotulado ?? ""
          }</textarea>
      </div>
    </form>
  `;
  return rotuladoTemplate;
};
const getActionButtons = (data) => {
  try {
    let buttons = "";

    if (data.hasOwnProperty("btnSave") && data.hasOwnProperty("btnCancel")) {
      buttons = `
      <div class="row buttons mt-2" style="row-gap:1em">
        <div class="col-12 col-md-6 d-flex">
          <div class="btn mx-auto btn-primary button-save" onclick='${data.btnSave.action}'>${data.btnSave.text}</div>
        </div>
        <div class="col-12 col-md-6 d-flex">
          <div class="btn mx-auto btn-secondary button-cancel" onclick='${data.btnCancel.action}'>${data.btnCancel.text}</div>
        </div>
      </div>`;
    } else if (data.hasOwnProperty("btnSave")) {
      buttons = `
      <div class="row buttons w-100 mt-2">
        <div class="col-12 col-md-12 d-flex align-items-center justiy-content-center">
          <div class="btn btn-primary mx-auto button-save" onclick='${data.btnSave.action}'>${data.btnSave.text}</div>
        </div>
      </div>`;
    } else if (data.hasOwnProperty("btnCancel")) {
      buttons = `
      <div class="row buttons w-100 mt-2">
        <div class="col-12 col-md-12 d-flex align-items-center justiy-content-center">
          <div class="btn btn-secondary mx-auto w-50 button-cancel" onclick='${data.btnCancel.action}'>${data.btnCancel.text}</div>
        </div>
      </div>`;
    }
    return buttons;
  } catch (e) {
    console.error(e);
    return "";
  }
};

const hideRotuladoView = () => {
  containerRotulado.empty();
  containerRotulado.hide();
  containerOrdenCompra.show();
};
const hideOrdenCompra = () => {
  containerOrdenCompra.hide();
  $(".orden-compra_header_china").empty();
  $(".orden-compra_header").hide();
  $(".producto").remove();
  $(".buttons").remove();
  // containerVer.hide();
  // containerListar.show();
  containerSteps.empty();
  getOrderProgress(idPedido, currentServicio);

  reload_table_Entidad();
};
const hidePagos = () => {
  containerPagos.hide();
  $(".step-container").remove();
  $(".step-container-completed").remove();
  $("#pagos-form").empty();
  $("#pagos-buttons").empty();
  $("#pago-garantia-btnlink").remove();
  $("#container-ver").hide();

  containerVer.show();
};
const saveOrdenCompra = () => {
  const url = base_url + "AgenteCompra/PedidosPagados/saveOrdenCompra";
  const form = $("#pedido-form");
  const formData = new FormData(form[0]);
  formData.append("idPedido", idPedido);
  formData.append("stepID", selectedStep);
  if (currentPrivilege == priviligesPersonalPeru) {
    const inputsCantidad = document.querySelectorAll(".input-cantidad");
    inputsCantidad.forEach((input) => {
      formData.append(input.name, input.value);
    });
  }
  $.ajax({
    url,
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (response) {
      const { status, data } = JSON.parse(response);
      if (status == "success") {
        hideOrdenCompra();
      } else {
        alert(message);
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.error(jqXHR.responseText);
    },
  });
};
const setRotuladoInputToNull = (idComponent) => {
  $(`#${idComponent}-url`).val("");
  $(`#input-${idComponent}`).remove();
  $(`#${idComponent}_container`).append(
    `<input type="file" name="${idComponent}"  class="" id="input-${idComponent}">`
  );
  $(`#delete-${idComponent}`).remove();
};

const setInputFileToNull = (file, id) => {
  const inputURL = `input-${file}-url-${id}`;
  const btnInput = `btn-${file}-${id}`;
  $(`#${inputURL}`).val("");
  //remove btn-outline-primary class and add btn-primary
  $(`#${btnInput}`).removeClass("btn-outline-primary").addClass("btn-primary");
  //set onclick to openInputFile
  $(`#${btnInput}`).attr(
    "onclick",
    `openInputFile('input-${file}-${id}',null)`
  );
  //set fileinput onclick
};
const hideSteps = () => {
  containerSteps.empty();
  $(".steps-buttons").empty();
  containerVer.hide();
  containerListar.show();
  reload_table_Entidad();
};
const openFileSelector = (id) => {
  console.log("open file selector", id);
  $(`#${id}`).click();
};
const downloadFile = (url) => {
  //download pdf with a tag but prevent default
  const link = document.createElement("a");
  link.href = url;
  link.download = url.split("/").pop();
  link.click();
  link.remove();
};
const getAlmacenData = (idPedido) => {
  $.ajax({
    url: base_url + "AgenteCompra/PedidosPagados/getAlmacenData",
    type: "POST",
    data: { idPedido },
    success: function (response) {
      response = JSON.parse(response);
      if (response.status == "success") {
        openAlmacenView(response.data, idPedido, null);
      }
    },
  });
};
//Almacen China functions

const getAlmacenViewTitle = (cotizacionCode = "") => {
  return `
  <h2 class="mb-2">
    <strong>RECEPCION DE CARGA: 
      ${
        cotizacionCode
          ? `<span class="text-primary">${cotizacionCode}</span>`
          : ""
      }
    </strong>
  </h2>
`;
};
const getAlmacenViewHeader = () => {
  return `
  <div id="almacen_header" class="row w-100">
    <div class="col-12 col-md-3">
      <div class="input-group mb-3">
        <span class="input-group-text" >TOTAL BOX</span>
        <input type="text" class="form-control" id="total-box" disabled> 
      </div>
    </div>
    <div class="col-12 col-md-3">
      <div class="input-group mb-3">
        <span class="input-group-text" >TOTAL CBM</span>
        <input type="text" class="form-control" id="total-cbm" disabled>
      </div>
    </div>
    <div class="col-12 col-md-3">
      <div class="input-group mb-3">
        <span class="input-group-text" >TOTAL KG</span>
        <input type="text" class="form-control" id="total-kg" disabled>
      </div>
    </div>
    <div class="col-12 col-md-3">
      <div class="input-group mb-3">
        <input type="text" class="form-control" id="almacen-search" > 
        <span class="btn btn-primary">Buscar</span>
        
      </div>
    </div>
  </div>
  `;
};
const getAlmacenTableHeader = (permiso) => {
  console.log("permiso", permiso);
  let html = `<div class="almacen-table d-flex flex-column">
          <div class="almacen-header d-flex flex-row">
            <div class="imagen-column column">IMAGEN</div>
            <div class="nombre-column column">NOMBRE PRODUCTO</div>
            <div class="fentrega-column column">F.ENTREGA</div>
            <div class="totalbox-column column">TOTAL BOX</div>
            <div class="totalcbm-column column">TOTAL CBM</div>
            <div class="totalkg-column column">TOTAL KG</div>
            <div class="fotos-column column">FOTOS</div>
            <div class="estado-column column">ESTADO</div>
            <div class="notas-column column">NOTAS</div>
            `;
  return html;
};
const getAlmacenTableBody = (
  data,
  cotizacionCode,
  idPedido,
  permiso = null
) => {
  let tableBody = "";
  if (!data) return tableBody;
  let totalCBM = 0;
  let totalKG = 0;
  let totalBox = 0;
  data.forEach((producto) => {
    if (producto.total_cbm) totalCBM += parseFloat(producto.total_cbm);
    if (producto.total_kg) totalKG += parseFloat(producto.total_kg);
    if (producto.total_box) totalBox += parseFloat(producto.total_box);
    tableBody += `<div class="almacen-row d-flex flex-row">
      <div class="imagen-column column">
        <img class="w-100" src="${producto.Txt_Url_Imagen_Producto}" alt="${
      producto.Txt_Producto
    }" class="img-cuz">
      </div>
      <div class="nombre-column column">
        <div class="nombre-container">
          <strong class="nombre-producto">${producto.Txt_Producto}</strong>
        </div>
       <div class="input-group mt-3 d-flex flex-row justify-content-center align-items-center mb-1 ">
            <span class="input-group-text" > CODE:</span>
            <input type="text" class="form-control"  disabled value="${
              producto.product_code
            }">
        </div>
        <div class="btn btn-success w-100" onclick="openSupplierDetails('${
          producto.name
        }','${producto.phone}')">
            <svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M14.9536 14.9458L21 21M17 10C17 13.866 13.866 17 10 17C6.13401 17 3 13.866 3 10C3 6.13401 6.13401 3 10 3C13.866 3 17 6.13401 17 10Z" stroke="#FFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>Proveedor</span>
        </div>
      </div>
      <div class="fentrega-column column">
      <input type="date" class="form-control" disabled value="${
        producto.Fe_Entrega_Proveedor.split(" ")[0]
      }"/>
      </div>
      <div class="totalbox-column column">
        <input type="number" class="form-control total_box" 
        name="almacen[${
          producto.ID_Pedido_Detalle_Producto_Proveedor
        }][total_box]"
        value="${parseInt(producto.total_box)}"
        ${permiso ? "disabled" : ""}
        >
      
      </div>
      <div class="totalcbm-column column">
        <input type="number" class="form-control total_cbm" 
        name="almacen[${
          producto.ID_Pedido_Detalle_Producto_Proveedor
        }][total_cbm]"
        
        value="${producto.total_cbm}"
         ${permiso ? "disabled" : ""}>
      </div>
      <div class="totalkg-column column">
        <input type="number" class="form-control total_kg"
        name="almacen[${
          producto.ID_Pedido_Detalle_Producto_Proveedor
        }][total_kg]"
        value="${producto.total_kg}"
         ${permiso ? "disabled" : ""}>
      </div>
      <div class="fotos-column column">
          <svg 
          onclick="viewSupplierPhotos(${
            producto.ID_Pedido_Detalle_Producto_Proveedor
          },'${cotizacionCode}','${idPedido}',
          '${permiso}')"
          class="${
            producto.almacen_estado != "PENDIENTE"
              ? "camera-filled"
              : "camera-not-filled"
          }"
          viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 16C13.6569 16 15 14.6569 15 13C15 11.3431 13.6569 10 12 10C10.3431 10 9 11.3431 9 13C9 14.6569 10.3431 16 12 16Z" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M3 16.8V9.2C3 8.0799 3 7.51984 3.21799 7.09202C3.40973 6.71569 3.71569 6.40973 4.09202 6.21799C4.51984 6 5.0799 6 6.2 6H7.25464C7.37758 6 7.43905 6 7.49576 5.9935C7.79166 5.95961 8.05705 5.79559 8.21969 5.54609C8.25086 5.49827 8.27836 5.44328 8.33333 5.33333C8.44329 5.11342 8.49827 5.00346 8.56062 4.90782C8.8859 4.40882 9.41668 4.08078 10.0085 4.01299C10.1219 4 10.2448 4 10.4907 4H13.5093C13.7552 4 13.8781 4 13.9915 4.01299C14.5833 4.08078 15.1141 4.40882 15.4394 4.90782C15.5017 5.00345 15.5567 5.11345 15.6667 5.33333C15.7216 5.44329 15.7491 5.49827 15.7803 5.54609C15.943 5.79559 16.2083 5.95961 16.5042 5.9935C16.561 6 16.6224 6 16.7454 6H17.8C18.9201 6 19.4802 6 19.908 6.21799C20.2843 6.40973 20.5903 6.71569 20.782 7.09202C21 7.51984 21 8.0799 21 9.2V16.8C21 17.9201 21 18.4802 20.782 18.908C20.5903 19.2843 20.2843 19.5903 19.908 19.782C19.4802 20 18.9201 20 17.8 20H6.2C5.0799 20 4.51984 20 4.09202 19.782C3.71569 19.5903 3.40973 19.2843 3.21799 18.908C3 18.4802 3 17.9201 3 16.8Z" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
      </div>
      <div class="estado-column column">
        <select class="form-select" disabled id="estado-${
          producto.ID_Pedido_Detalle
        }">
          <option value="PENDIENTE" ${
            producto.almacen_estado == "PENDIENTE" ? "selected" : ""
          }>PENDIENTE</option>
          <option value="RECIBIDO" ${
            producto.almacen_estado == "RECIBIDO" ? "selected" : ""
          }>RECIBIDO</option>
        </select>
      </div>
    `;
    tableBody += `<div class="notas-column column">
        <textarea class="form-control" name="almacen[${
          producto.ID_Pedido_Detalle_Producto_Proveedor
        }][notas]" rows="3"
        ${permiso ? "disabled" : ""}
        >${producto.almacen_notas ?? ""}</textarea>
      </div>`;
    // }else if(permiso==2){
    //   tableBody += `<div class="notas-column column">
    //     <textarea class="form-control" name="almacen[${
    //       producto.ID_Pedido_Detalle_Producto_Proveedor
    //     }][personal_notas]" rows="3">${producto.almacen_notas??''}</textarea>
    //   </div>`;
    // }
    tableBody += `</div>`;
  });
  $("#total-box").val(totalBox);
  $("#total-cbm").val(totalCBM);
  $("#total-kg").val(totalKG);
  return tableBody + "</div>";
};
/**
 * This function opens  the almacen table view
 * @param {*} data
 */
const openAlmacenView = (data, idPedido, permiso = null) => {
  $("#container_orden-compra").hide();

  let id_pedido = 0;
  let cotizacionCode = 0;
  if (data) {
    id_pedido = data[0].id_pedido;
    cotizacionCode = data[0].cotizacionCode;
  }
  const almacenTitle = getAlmacenViewTitle(cotizacionCode);
  const almacenHeader = getAlmacenViewHeader();
  const almacenTableHeader = getAlmacenTableHeader(permiso);
  containerListar.hide();
  containerAlmacen
    .empty()
    .append(almacenTitle)
    .append(almacenHeader)
    .append(almacenTableHeader)
    .show();
  const almacenTableBody = getAlmacenTableBody(
    data,
    cotizacionCode,
    idPedido,
    permiso
  );
  containerAlmacen.append(almacenTableBody);
  const actionButtons = {
    btnSave: {
      text: "Guardar",
      action: `saveAlmacenData(${id_pedido})`,
    },
    btnCancel: {
      text: "Regresar",
      action: `hideAlmacenView(${id_pedido})`,
    },
  };
  const buttonsTemplate = getActionButtons(actionButtons);
  containerAlmacen.append(buttonsTemplate);
  updateTotal(".total_box", "#total-box");
  updateTotal(".total_cbm", "#total-cbm");
  updateTotal(".total_kg", "#total-kg");
  $("#almacen-search").on("input", function () {
    const value = $(this).val();
    console.log(value);
    const rows = $(".almacen-row");
    console.log(rows);
    rows.each((i, row) => {
      const text = $(row)
        .children(".nombre-column")
        .children(".nombre-container")
        .children(".nombre-producto")
        .text()
        .toLowerCase();
      if (text.includes(value.toLowerCase())) {
        $(row).removeClass("d-none").addClass("d-flex");
      } else {
        $(row).removeClass("d-flex").addClass("d-none");
      }
    });
  });
};
const updateTotal = (inputClass, totalId) => {
  $(document).on("input", inputClass, function () {
    let totalSum = 0;

    $(inputClass).each(function () {
      const value = parseFloat($(this).val());
      if (!isNaN(value)) {
        totalSum += value;
      }
    });
    $(totalId).val(totalSum);
  });
};
const saveAlmacenData = (idSupplier, idPedido) => {
  const url = base_url + "AgenteCompra/PedidosPagados/saveAlmacenData";
  const form = $(".almacen-form");
  const formData = new FormData(form[0]);
  formData.append("idPedido", idSupplier);
  $.ajax({
    url,
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (response) {
      const { status, data } = JSON.parse(response);
      if (status == "success") {
        hideAlmacenView();
      } else {
        alert(message);
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.error(jqXHR.responseText);
    },
  });
};
const changeStatusAlmacen = (estado, id_pedido) => {
  $.ajax({
    url: base_url + "AgenteCompra/PedidosPagados/cambiarEstadoAlmacen",
    type: "POST",
    data: {
      id_pedido,
      estado,
    },
    success: function (response) {
      if (response == "success") {
        reload_table_Entidad();
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.log(jqXHR.responseText);
    },
  });
};
const viewSupplierPhotos = (id, cotizacionCode, idPedido, permiso = null) => {
  containerAlmacen.empty();
  const almacenTitle = getAlmacenViewTitle(cotizacionCode);
  containerAlmacen.append(almacenTitle);
  const photosContainer = getviewSupplierPhotosTemplate(permiso);
  containerAlmacen.append(photosContainer);
  handleFileChange("#foto1-file", "#foto1-name", ".foto1-container");
  handleFileChange("#foto2-file", "#foto2-name", ".foto2-container");
  $.ajax({
    url: base_url + "AgenteCompra/PedidosPagados/getSupplierPhotos",
    type: "POST",
    data: { idSupplier: id },
    success: function (response) {
      response = JSON.parse(response);
      if (response.status == "success") {
        const { almacen_foto1, almacen_foto2, idSupplier } = response.data;
        if (almacen_foto1 != null) {
          handleFileDisplay(
            almacen_foto1,
            "#foto1-name",
            ".foto1-container",
            "#foto1-container",
            idSupplier,
            "foto1-file",
            permiso
          );
        }
        if (almacen_foto2 != null) {
          handleFileDisplay(
            almacen_foto2,
            "#foto2-name",
            ".foto2-container",
            "#foto2-container",
            idSupplier,
            "foto2-file",
            permiso
          );
        } else {
          if (permiso != null) {
            //set hide foto2-container parent
            $("#foto2-container").parent().hide();
          }
        }
        // if(almacen_foto2){
        //   const foto2Name=almacen_foto2.split("/").pop();
        //   $("#foto2-name").text(foto2Name);
        //   $(".foto2-container").addClass("filled");
        // }
      }
    },
  });
  let actionButtons = {
    btnSave: {
      text: "Guardar",
      action: `saveSupplierPhotos(${id},${idPedido})`,
    },
    btnCancel: {
      text: "Regresar",
      action: `getAlmacenData(${idPedido})`,
    },
  };
  if (permiso != null) {
    actionButtons = {
      btnCancel: {
        text: "Regresar",
        action: `openStepFunction(3,${selectedStep})`,
      },
    };
  }
  const buttonsTemplate = getActionButtons(actionButtons);
  containerAlmacen.append(buttonsTemplate);
};
const saveSupplierPhotos = (id, idPedido) => {
  const url = base_url + "AgenteCompra/PedidosPagados/saveSupplierPhotos";
  const form = $("#form-photos");
  const formData = new FormData(form[0]);
  formData.append("idSupplier", id);
  formData.append("idPedido", idPedido);
  $.ajax({
    url,
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (response) {
      const { status, data } = JSON.parse(response);
      if (status == "success") {
        getAlmacenData(idPedido);
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.error(jqXHR.responseText);
    },
  });
};

const getviewSupplierPhotosTemplate = () => {
  let html = `
  <form class="py-1 px-4" id="form-photos">

    <div class="row fotos-container">
      <div class="col-12 col-md-3">    
        <div class="fotos-container">
          <div class="upload-payment container-div not-filled foto1-container "  id="foto1-container" onclick="openFileSelector('foto1-file')">
              
              <?xml version="1.0" encoding="utf-8"?><!-- Uploaded to: SVG Repo, www.svgrepo.com, Generator: SVG Repo Mixer Tools -->
              <svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M12 15L12 2M12 2L15 5.5M12 2L9 5.5" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M8 22.0002H16C18.8284 22.0002 20.2426 22.0002 21.1213 21.1215C22 20.2429 22 18.8286 22 16.0002V15.0002C22 12.1718 22 10.7576 21.1213 9.8789C20.3529 9.11051 19.175 9.01406 17 9.00195M7 9.00195C4.82497 9.01406 3.64706 9.11051 2.87868 9.87889C2 10.7576 2 12.1718 2 15.0002L2 16.0002C2 18.8286 2 20.2429 2.87868 21.1215C3.17848 21.4213 3.54062 21.6188 4 21.749" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"/>
              </svg>
          </div>
          <input type="file" id="foto1-file" class="d-none" name="foto1"/>
          <div id="foto1-name" class="payment-name container-div foto1-container">
          Seleccionar archivo</div>

        </div>
      </div>
      <div class="col-12 col-md-3 ">    
        <div class="fotos-container">
          <div class="upload-payment container-div not-filled foto2-container"  id="foto2-container" onclick="openFileSelector('foto2-file')">
              
              <?xml version="1.0" encoding="utf-8"?><!-- Uploaded to: SVG Repo, www.svgrepo.com, Generator: SVG Repo Mixer Tools -->
              <svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M12 15L12 2M12 2L15 5.5M12 2L9 5.5" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M8 22.0002H16C18.8284 22.0002 20.2426 22.0002 21.1213 21.1215C22 20.2429 22 18.8286 22 16.0002V15.0002C22 12.1718 22 10.7576 21.1213 9.8789C20.3529 9.11051 19.175 9.01406 17 9.00195M7 9.00195C4.82497 9.01406 3.64706 9.11051 2.87868 9.87889C2 10.7576 2 12.1718 2 15.0002L2 16.0002C2 18.8286 2 20.2429 2.87868 21.1215C3.17848 21.4213 3.54062 21.6188 4 21.749" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"/>
              </svg>
          </div>
          <input type="file" id="foto2-file" class="d-none" name="foto2" />
          <div id="foto2-name" class="payment-name container-div foto2-container">
          Seleccionar archivo</div>

        </div>
      </div>
    </div>
    <div class="row" id="pagos-buttons">
    </div>

      
  </form>
  `;
  return html;
};

const hideAlmacenView = () => {
  containerAlmacen.empty();
  containerAlmacen.hide();
  containerListar.show();
  reload_table_Entidad();
};
const handleFileChange = (fileInputId, fileNameId, containerClass) => {
  $(fileInputId).change(function () {
    const file = $(this)[0].files[0];
    if (file) {
      const fileName = file.name;
      if (fileName.length > 15) {
        $(fileNameId).html(fileName.substring(0, 15) + "...");
      } else {
        $(fileNameId).html(fileName);
      }
      $(containerClass).removeClass("not-filled").addClass("filled");
    } else {
      $(fileNameId).html("Seleccionar archivo");
      $(containerClass).removeClass("filled").addClass("not-filled");
    }
  });
};
const handleFileDisplay = (
  filePath,
  fileNameId,
  containerClass,
  containerId,
  idSupplier,
  input,
  permiso
) => {
  console.log("permiso", permiso, containerId, containerClass);
  if (filePath != null) {
    const fileName = filePath.split("/").pop();
    if (fileName.length > 20) {
      $(fileNameId).html(fileName.substring(0, 20) + "...");
    } else {
      $(fileNameId).html(fileName);
    }
    $(containerClass).removeClass("not-filled").addClass("filled");
    $(containerId).append(
      `<input type="hidden" name="file[idSupplier]" value="${idSupplier}">`
    );
    if (!permiso) {
      $(containerId).append(
        `<span class="remove-item" onclick="handleSpanClick(event, '${input}')">${editIcon}</span>`
      );
      console.log("permiso", permiso, containerId, containerClass);
      $(containerClass).removeAttr("onclick");
    }
    // Remove existing click handler if any
    $(containerId).off("click");
    $(containerId).removeAttr("onclick");

    $(containerId).click(function () {
      downloadFile(filePath);
    });
  }
};
function handleSpanClick(event, input) {
  event.stopPropagation();
  openFileSelector(input);
}
const openSupplierDetails = (name, phone) => {
  $("#modalsupplier-data").modal("show");
  $("#modal-supplier-name").text(name);
  $("#modal-supplier-phone").text(phone);
  $("#modaldetail-close").click(function () {
    $("#modalsupplier-data").modal("hide");
  });
};
