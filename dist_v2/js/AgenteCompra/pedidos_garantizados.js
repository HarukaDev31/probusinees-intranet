var url,
  table_Entidad,
  div_chat_item = "",
  div_items = "",
  iCounter = 0,
  iCounterItems = 1;
//AUTOCOMPLETE
var caractes_no_validos_global_autocomplete = "\"'~!@%^|";
// Se puede crear un arreglo a partir de la cadena
let search_global_autocomplete =
  caractes_no_validos_global_autocomplete.split("");
// Solo tomé algunos caracteres, completa el arreglo
let replace_global_autocomplete = ["", "", "", "", "", "", "", "", ""];
//28 caracteres
// FIN AUTOCOMPLETE

var fToday = new Date(),
  fYear = fToday.getFullYear(),
  fMonth = fToday.getMonth() + 1,
  fDay = fToday.getDate();

//cancelar agregar productos, add onclick on btn-cancelar
$(document).on("click", "#btn-cancelar", function (e) {
  e.preventDefault();
  $(".div-Listar").show();
  $(".div-AgregarEditar").hide();
  //remove btn-file_cotizacion
  $("#btn-file_cotizacion").remove();
});
$(function () {
  $(".select2").select2();

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
      //show confirm message
      $("#modal-guardar_personal_china").modal("hide");

      $("#modal-confirmation").modal("show");
      $("#btn-cancel-confirmation").on("click", function (e) {
        console.log("cancel");
        e.preventDefault();

        $("#modal-confirmation").modal("hide");
        $("#modal-guardar_personal_china").modal("show");
      });
      //if click  $("btn-confirmation") then call function
      $("#btn-confirmation").on("click", function () {
        $("#modal-confirmation").modal("hide");

        $("#btn-guardar_personal_china").text("");
        $("#btn-guardar_personal_china").attr("disabled", true);
        $("#btn-guardar_personal_china").html(
          'Guardando <div class="spinner-border" role="status"><span class="sr-only"></span></div>'
        );

        url =
          base_url +
          "AgenteCompra/PedidosGarantizados/asignarUsuarioPedidoChina";
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

  $(document).on("click", ".btn-quitar_item", function (e) {
    e.preventDefault();
    //alert($(this).data('id'));
    $("#card" + $(this).data("id")).remove();
  });

  $(document).on("click", "#btn-add_item", function (e) {
    e.preventDefault();
    addItemsPedido();

    $(".div-articulos").show();
  });

  $("#form-documento_pago_garantizado").on("submit", function (e) {
    e.preventDefault();

    $(".help-block").empty();
    $(".form-group").removeClass("has-error");

    $("#btn-guardar_documento_pago_garantizado").text("");
    $("#btn-guardar_documento_pago_garantizado").attr("disabled", true);
    $("#btn-guardar_documento_pago_garantizado").append(
      'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>'
    );

    if (document.getElementById("image_documento").files.length == 0) {
      $("#image_documento")
        .closest(".form-group")
        .find(".help-block")
        .html("Empty file");
      $("#image_documento")
        .closest(".form-group")
        .removeClass("has-success")
        .addClass("has-error");
    } else {
      var postData = new FormData($("#form-documento_pago_garantizado")[0]);
      $.ajax({
        url: base_url + "AgenteCompra/PedidosGarantizados/addFileProveedor",
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

        $("#btn-guardar_documento_pago_garantizado").text("");
        $("#btn-guardar_documento_pago_garantizado").append("Guardar");
        $("#btn-guardar_documento_pago_garantizado").attr("disabled", false);

        if (response.status == "success") {
          $("#modal-documento_pago_garantizado").modal("hide");

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

  url = base_url + "AgenteCompra/PedidosGarantizados/ajax_list";
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
    order: [],
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

  /*
	$(".width_full").keyup(function (e) {
    console.log('ga');
    $('#hidden-sCorrelativoCotizacion').val('');
  })
  */
  jQuery(document).on("keyup", ".width_full", function (ev) {
    $("#hidden-sCorrelativoCotizacion").val("");
    $("#hidden-ID_Pedido_Cabecera").val("");
    reload_table_Entidad();
  });

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

  $("#table-elegir_productos_proveedor").on(
    "click",
    ".img-table_item",
    function () {
      $(".img-responsive").attr("src", "");

      $(".modal-ver_item").modal("show");
      $(".img-responsive").attr("src", $(this).data("url_img"));
      $("#a-download_image").attr("data-id_item", $(this).data("id_item"));
    }
  );

  $("#a-download_image").click(function () {
    id = $(this).data("id_item");
    url = base_url + "AgenteCompra/PedidosGarantizados/downloadImage/" + id;

    var popupwin = window.open(url);
    setTimeout(function () {
      popupwin.close();
    }, 2000);
  });

  $("#span-id_pedido").html("");

  $("#div-add_item_proveedor").hide();
  $(document).on("click", ".btn-add_proveedor", function (e) {
    e.preventDefault();

    $("#div-arrItems").html("");

    $(".div-Listar").hide();
    $(".div-AgregarEditar").hide();
    $("#div-add_item_proveedor").show();

    $("#modal-precio1").focus();

    iCounterItems = 1;
    addItems();

    $("#txt-EID_Empresa_item").val($(this).data("id_empresa"));
    $("#txt-EID_Organizacion_item").val($(this).data("id_organizacion"));
    $("#txt-EID_Pedido_Cabecera_item").val($(this).data("id_pedido_cabecera"));
    $("#txt-EID_Pedido_Detalle_item").val($(this).data("id_pedido_detalle"));
    $("#txt-Item_ECorrelativo").val($(this).data("correlativo"));
    $("#txt-Item_Ename_producto").val($(this).data("name_producto"));
  });

  $(document).on("click", ".btn-seleccionar_proveedor", function (e) {
    e.preventDefault();
    var id_detalle = $(this).data("id_detalle");
    var id = $(this).data("id");
    var correlativo = $(this).data("correlativo");
    var name_item = $(this).data("name_item");
    var id_supplier = $(this).data("id_supplier");
    let pedidoID=$("#txt-EID_Pedido_Cabecera_item").val()
 
    $("#btn-confirmation").on("click", function () {
      $("#modal-confirmation").modal("hide");

      url =
        base_url +
        "AgenteCompra/PedidosGarantizados/elegirItemProveedor/" +
        id_detalle +
        "/" +
        id +
        "/" +
        1 +
        "/" +
        correlativo +
        "/" +
        encodeURIComponent(name_item)+
        "/"+pedidoID+"/"+id_supplier;
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
            //alert(response.message);
            $("#moda-message-content").addClass("bg-" + response.status);
            $(".modal-title-message").text(response.message);
            setTimeout(function () {
              $("#modal-message").modal("hide");
            }, 1100);

            $("#table-elegir_productos_proveedor tbody").empty();
            getItemProveedor(id_detalle);
          } else {
            $("#moda-message-content").addClass("bg-danger");
            $(".modal-title-message").text(response.message);
            setTimeout(function () {
              $("#modal-message").modal("hide");
            }, 2100);
          }
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
    });
    $("#modal-confirmation").modal("show");
    //modal-message-confirmation set title
    $("#modal-message-confirmation-title").html("Desea seleccionar proveedor?");
  });

  $(document).on("click", ".btn-desmarcar_proveedor", function (e) {
    e.preventDefault();

    var id_detalle = $(this).data("id_detalle");
    var id = $(this).data("id");
    var correlativo = $(this).data("correlativo");
    var name_item = $(this).data("name_item");
    var id_supplier = $(this).data("id_supplier");
    let pedidoID=$("#txt-EID_Pedido_Cabecera_item").val()
    console.log("correlativo " + correlativo);
    console.log("name_item " + name_item);

    url =
      base_url +
      "AgenteCompra/PedidosGarantizados/elegirItemProveedor/" +
      id_detalle +
      "/" +
      id +
      "/" +
      0 +
      "/" +
      correlativo +
      "/" +
      encodeURIComponent(name_item)+
      "/"+pedidoID+"/"+id_supplier;;
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
          //alert(response.message);
          $("#moda-message-content").addClass("bg-" + response.status);
          $(".modal-title-message").text(response.message);
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 1100);

          $("#table-elegir_productos_proveedor tbody").empty();
          getItemProveedor(id_detalle);
        } else {
          $("#moda-message-content").addClass("bg-danger");
          $(".modal-title-message").text(response.message);
          setTimeout(function () {
            $("#modal-message").modal("hide");
          }, 2100);
          //alert(response.message);
        }
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
  });

  $("#div-elegir_item_proveedor").hide();
  $(document).on("click", ".btn-elegir_proveedor", function (e) {
    e.preventDefault();

    $(".div-Listar").hide();
    $(".div-AgregarEditar").hide();
    $("#div-elegir_item_proveedor").show();

    $("#table-elegir_productos_proveedor tbody").empty();

    var html = "";
    html =
      '<tr><td class="text-left" colspan="12">Cargando <div class="spinner-border" role="status"><span class="sr-only"></span></div></td></tr>';
    $("#table-elegir_productos_proveedor").append(html);

    var id = $(this).data("id_pedido_cabecera");
    var id_detalle = $(this).data("id_pedido_detalle");

    $("#txt-EID_Empresa_item").val($(this).data("id_empresa"));
    $("#txt-EID_Organizacion_item").val($(this).data("id_organizacion"));
    $("#txt-EID_Pedido_Cabecera_item").val(id);
    $("#txt-EID_Pedido_Detalle_item").val(id_detalle);
    $("#txt-Item_ECorrelativo_Editar").val($(this).data("correlativo"));
    $("#txt-Item_Ename_producto_Editar").val($(this).data("name_producto"));

    getItemProveedor(id_detalle);
  });

  $(document).on("click", ".btn-quitar_item", function (e) {
    e.preventDefault();
    $("#card" + $(this).data("id")).remove();
  });

  $(document).on("click", "#btn-add_item", function (e) {
    e.preventDefault();
    addItems();

    $("#div-button-add_item").removeClass("mt-2");
    $("#div-button-add_item").addClass("mt-0");
  });

  $(document).on("click", "#btn-cancel_detalle_item_proveedor", function (e) {
    e.preventDefault();

    $(".div-Listar").hide();
    $(".div-AgregarEditar").show();
    $("#div-add_item_proveedor").hide();
  });

  $(document).on("click", "#btn-cancel_detalle_elegir_proveedor", function (e) {
    e.preventDefault();

    $(".div-Listar").hide();
    $(".div-AgregarEditar").show();
    $("#div-elegir_item_proveedor").hide();
  });

  $(document).on("click", "#btn-save_detalle_elegir_proveedor", function (e) {
    e.preventDefault();

    $("#btn-save_detalle_elegir_proveedor").text("");
    $("#btn-save_detalle_elegir_proveedor").attr("disabled", true);
    $("#btn-save_detalle_elegir_proveedor").html(
      'Guardando <div class="spinner-border" role="status"><span class="sr-only"></span></div>'
    );

    var postData = new FormData($("#form-arrItemsProveedor")[0]);
    url =
      base_url +
      "AgenteCompra/PedidosGarantizados/actualizarElegirItemProductos";
    $.ajax({
      type: "POST",
      dataType: "JSON",
      url: url,
      data: postData,
      processData: false,
      contentType: false,
      //data		  : $('#form-arrItemsProveedor').serialize(),
      success: function (response) {
        $("#moda-message-content").removeClass(
          "bg-danger bg-warning bg-success"
        );
        $("#modal-message").modal("show");

        if (response.status == "success") {
          $(".div-Listar").hide();
          $(".div-AgregarEditar").show();
          $("#div-elegir_item_proveedor").hide();

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

        $("#btn-save_detalle_elegir_proveedor").text("");
        $("#btn-save_detalle_elegir_proveedor").html("Guardar");
        $("#btn-save_detalle_elegir_proveedor").attr("disabled", false);
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

        $("#btn-save_detalle_elegir_proveedor").text("");
        $("#btn-save_detalle_elegir_proveedor").append("Guardar");
        $("#btn-save_detalle_elegir_proveedor").attr("disabled", false);
      },
    });
  });

  $("#form-arrItems").on("submit", function (e) {
    e.preventDefault();

    $(".help-block").empty();
    $(".form-group").removeClass("has-error");

    //validacion de articulos
    var sEstadoArticulos = true;
    var firstError = null;

    $("#form-arrItems")
      .find(":input")
      .each(function () {
        var elemento = this;
        console.log(elemento);
        if (elemento.dataset.correlativo !== undefined) {
          if (elemento.classList[0] == "arrProducto") {
            if (elemento.type == "text") {
              if (elemento.classList[2] == "required") {
                if (
                  ((elemento.classList[3] == "precio" ||
                    elemento.classList[3] == "moq" ||
                    elemento.classList[3] == "qty_caja" ||
                    elemento.classList[3] == "cbm" ||
                    elemento.classList[3] == "delivery" ||
                    elemento.classList[3] == "shipping_cost" ||
                    elemento.classList[3] == "celular_proveedor") &&
                    (isNaN(parseFloat($("#" + elemento.id).val())) ||
                      parseFloat($("#" + elemento.id).val()) < 0.0)) ||
                  (elemento.classList[3] == "nombre_proveedor" &&
                    $("#" + elemento.id).val().length == 0)
                ) {
                  $("#" + elemento.id)
                    .closest(".form-group")
                    .find(".help-block")
                    .html("Ingresar " + elemento.classList[3]);
                  $("#" + elemento.id)
                    .closest(".form-group")
                    .removeClass("has-success")
                    .addClass("has-error");
                  if (firstError == null) {
                    firstError = elemento.id;
                  }
                  // scrollToError($("html, body"), $("#" + elemento.id));

                  $("#" + elemento.id).focus();
                  $("#" + elemento.id).select();
                  setTimeout(function () {
                    $("#" + elemento.id).focus();
                    $("#" + elemento.id).select();
                  }, 30);

                  sEstadoArticulos = false;
                }
              }
            }
          }
        }
      });
    if (firstError != null) {
      scrollToError($("html, body"), $("#" + firstError));
    }
    //validacion de articulos

    if (sEstadoArticulos == true) {
      $("#btn-save_detalle_item_proveedor").prop("disabled", true);
      $("#btn-save_detalle_item_proveedor").html(
        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enviando'
      );

      var postData = new FormData($("#form-arrItems")[0]);
      console.log(postData);
      $.ajax({
        url:
          base_url + "AgenteCompra/PedidosGarantizados/addPedidoItemProveedor",
        type: "POST",
        dataType: "JSON",
        data: postData,
        processData: false,
        contentType: false,
      }).done(function (response) {
        $("#btn-save_detalle_item_proveedor").prop("disabled", false);
        $("#btn-save_detalle_item_proveedor").html("Guardar");

        $("#moda-message-content").removeClass(
          "bg-danger bg-warning bg-success"
        );
        $("#modal-message").modal("show");

        console.log(response);
        if (response.status == "success") {
          $("#moda-message-content").addClass("bg-" + response.status);
          $(".modal-title-message").text(response.message);

          $(".div-Listar").hide();
          $(".div-AgregarEditar").show();
          $("#div-add_item_proveedor").hide();
        } else {
          $("#moda-message-content").addClass("bg-danger");
          $(".modal-title-message").text(response.message);
          //alert(response.message);
        }
      });
    }
  });

  //chat de novedades de producto
  $(document).on("click", ".btn-chat_producto", function (e) {
    e.preventDefault();

    /*
    $("#form-chat_producto").keypress(function(e) {
      if (e.which == 13) {
        return false;
      }
    });
    */

    $(".modal-chat_producto").modal("show");
    var id_item = $(this).data("id_pedido_detalle");
    $("#txt-chat_producto-ID_Empresa_item").val($(this).data("id_empresa"));
    $("#txt-chat_producto-ID_Organizacion_item").val(
      $(this).data("id_organizacion")
    );
    $("#txt-chat_producto-ID_Pedido_Cabecera_item").val(
      $(this).data("id_pedido_cabecera")
    );
    $("#txt-chat_producto-ID_Pedido_Detalle_item").val(
      $(this).data("id_pedido_detalle")
    );
    $("#title-chat_producto").html($(this).data("nombre_producto"));

    $(".modal-chat_producto").on("shown.bs.modal", function () {
      $('[name="message_chat"]').focus();
    });

    //buscar data
    viewChatItem(id_item);
  });

  //chat de novedades de producto
  $(document).on("click", "#btn-enviar_mensaje", function (e) {
    e.preventDefault();

    $(".help-block").empty();
    $(".form-group").removeClass("has-error");

    if (
      $('[name="message_chat"]').val() == "" ||
      $('[name="message_chat"]').val() == null ||
      $('[name="message_chat"]').val().length == 0
    ) {
      $('[name="message_chat"]')
        .closest(".form-group")
        .find(".help-block")
        .html("Mensaje vacío");
      $('[name="message_chat"]')
        .closest(".form-group")
        .removeClass("has-success")
        .addClass("has-error");
      $('[name="message_chat"]').focus();
    } else {
      $("#btn-enviar_mensaje").text("");
      $("#btn-enviar_mensaje").attr("disabled", true);
      $("#btn-enviar_mensaje").html(
        'Enviando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>'
      );

      url = base_url + "AgenteCompra/PedidosGarantizados/sendMessage";
      $.ajax({
        type: "POST",
        dataType: "JSON",
        url: url,
        data: $("#form-chat_producto").serialize(),
        success: function (response) {
          $(".modal-chat_producto").modal("hide");

          $("#moda-message-content").removeClass(
            "bg-danger bg-warning bg-success"
          );
          $("#modal-message").modal("show");

          if (response.status == "success") {
            $("#form-chat_producto")[0].reset();

            $("#moda-message-content").addClass("bg-" + response.status);
            $(".modal-title-message").text(response.message);
            setTimeout(function () {
              $("#modal-message").modal("hide");
            }, 1100);
            verPedido($("#txt-chat_producto-ID_Pedido_Cabecera_item").val());
          } else {
            $("#moda-message-content").addClass("bg-danger");
            $(".modal-title-message").text(response.message);
            setTimeout(function () {
              $("#modal-message").modal("hide");
            }, 1200);
          }

          $("#btn-enviar_mensaje").text("");
          $("#btn-enviar_mensaje").html("Enviar");
          $("#btn-enviar_mensaje").attr("disabled", false);
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

          $("#btn-enviar_mensaje").text("");
          $("#btn-enviar_mensaje").html("Enviar");
          $("#btn-enviar_mensaje").attr("disabled", false);
        },
      });
    }
  });
});

function reload_table_Entidad() {
  table_Entidad.ajax.reload(null, false);
}

function verPedido(ID) {
  $(".div-Listar").hide();

  $("#form-pedido")[0].reset();
  $(".form-group").removeClass("has-error");
  $(".form-group").removeClass("has-success");
  $(".help-block").empty();

  $("#table-Producto_Enlace tbody").empty();
  $("#table-Producto_Enlace").show();

  $("#div-arrItemsPedidos").html("");

  //$('#span-id_pedido').html('Nro. ' + ID);

  $("#span-id_pedido").html("");

  url = base_url + "AgenteCompra/PedidosGarantizados/ajax_edit/" + ID;
  $.ajax({
    url: url,
    type: "GET",
    dataType: "JSON",
    success: function (response) {
      var detalle = response;
      response = response[0];

      $("#span-id_pedido").html(response.sCorrelativoCotizacion);

      $(".div-AgregarEditar").show();

      $('[name="EID_Pedido_Cabecera"]').val(response.ID_Pedido_Cabecera);
      $('[name="EID_Entidad"]').val(response.ID_Entidad);
      $('[name="EID_Empresa"]').val(response.ID_Empresa);
      $('[name="EID_Organizacion"]').val(response.ID_Organizacion);
      $('[name="ECorrelativo"]').val(response.sCorrelativoCotizacion);
      $('[name="Item_ECorrelativo"]').val(response.Item_ECorrelativo);

      $('[name="No_Contacto"]').val(response.No_Contacto);
      $('[name="Txt_Email_Contacto"]').val(response.Txt_Email_Contacto);
      $('[name="Nu_Celular_Contacto"]').val(response.Nu_Celular_Contacto);
      $('[name="No_Entidad"]').val(response.No_Entidad);
      $('[name="Nu_Documento_Identidad"]').val(response.Nu_Documento_Identidad);
      if (response.file_cotizacion != "" && response.file_cotizacion != null) {
        //remove file_cotizacion
        $("#file_cotizacion").hide();
        $("#container-file_cotizacion").append(`
          <a id="btn-file_cotizacion" href="${response.file_cotizacion}" target="_blank" class="btn btn-primary" role="button">
          <i class="fa fa-download"></i>
          Descargar Cotizacion</a>`)
      }

      var yuan_venta = response.Ss_Tipo_Cambio;
      //console.log(parseFloat(response.yuan_venta));
      //console.log(parseFloat(response.Ss_Tipo_Cambio));
      if (
        parseFloat(response.yuan_venta) > 0 &&
        parseFloat(response.Ss_Tipo_Cambio) == 0
      ) {
        yuan_venta = response.yuan_venta;
      }

      $('[name="Ss_Tipo_Cambio"]').val(yuan_venta);

      $('[name="Txt_Observaciones_Garantizado"]').val(
        response.Txt_Observaciones_Garantizado
      );

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
          '<span class="badge badge-pill badge-danger">Rechazado</span>';
      $("#div-estado").html(sNombreEstado);

      var table_enlace_producto = "",
        ID_Entidad = 0;
      for (i = 0; i < detalle.length; i++) {
        var cantidad_item = detalle[i]["Qt_Producto"];
        var id_item = detalle[i]["ID_Pedido_Detalle"];
        var href_link =
          detalle[i]["Txt_Url_Link_Pagina_Producto"] != "" &&
          detalle[i]["Txt_Url_Link_Pagina_Producto"] != null
            ? "<a class='btn btn-link p-0 m-0' target='_blank' rel='noopener noreferrer' href='" +
              detalle[i]["Txt_Url_Link_Pagina_Producto"] +
              "' role='button'>Link</a>"
            : "";
        var nombre_producto =
          detalle[i]["Txt_Producto"] != "" && detalle[i]["Txt_Producto"] != null
            ? detalle[i]["Txt_Producto"]
            : "";

        /*
        if (ID_Entidad != response[i].ID_Entidad_Proveedor) {
          table_enlace_producto +=
          "<tr>"
            +"<th class='text-right'>Proveedor </th>"
            +"<th class='text-left' colspan='14'>" + response[i].No_Contacto_Proveedor + "</th>"
          +"</tr>";
          ID_Entidad = response[i].ID_Entidad_Proveedor;
        }
        */

        table_enlace_producto +=
          "<tr id='tr_enlace_producto" +
          id_item +
          "'>" +
          "<td style='display:none;' class='text-left td-id_item'>" +
          id_item +
          "</td>" +
          "<td class='text-center td-name' width='30%'>";

        table_enlace_producto +=
          "<h6 class='font-weight-bold font-medium'>" +
          nombre_producto +
          "</h6>";

        cantidad_item =
          !isNaN(cantidad_item) && cantidad_item > 0 && cantidad_item != ""
            ? cantidad_item
            : 0;
        //if(!isNaN(cantidad_item) && cantidad_item > 0 && cantidad_item!=''){
        table_enlace_producto += '<div class="row mb-2">';
        table_enlace_producto += '<div class="col col-sm-6 text-right">';
        table_enlace_producto += "<span class='mt-3'>Cantidad</span>";
        table_enlace_producto += "</div>";
        table_enlace_producto += '<div class="col col-sm-2">';
        table_enlace_producto +=
          '<input type="hidden" name="addProductoTable[' +
          id_item +
          '][id_item]" value="' +
          id_item +
          '">';
        table_enlace_producto +=
          '<input type="text" inputmode="decimal" class="form-control input-decimal" name="addProductoTable[' +
          id_item +
          '][cantidad]" value="' +
          Math.round10(cantidad_item, -2) +
          '">';
        table_enlace_producto += "</div>";
        table_enlace_producto += "</div>";
        //}

        /*
            if(!isNaN(cantidad_item) && cantidad_item > 0 && cantidad_item!=''){
              table_enlace_producto += "<span class='mt-3'>Cantidad: </span><span class='font-weight-bold'>" + Math.round10(cantidad_item, -2) + "</span><br>";
            }
            */

        table_enlace_producto +=
          "<img data-id_item='" +
          id_item +
          "' data-url_img='" +
          detalle[i]["Txt_Url_Imagen_Producto"] +
          "' src='" +
          detalle[i]["Txt_Url_Imagen_Producto"] +
          "' alt='" +
          detalle[i]["Txt_Producto"] +
          "' class='img-thumbnail img-table_item img-fluid img-resize mb-2'>";

        table_enlace_producto += "</td>";
        //+ "<td class='text-left td-name' width='20%'>" + detalle[i]['Txt_Producto'] + "</td>"
        //+ "<td class='text-left td-name' width='20%'>" + detalle[i]['Txt_Descripcion'] + "</td>"
        table_enlace_producto += "<td class='text-left td-name' width='20%'>";
        table_enlace_producto +=
          '<textarea class="form-control" placeholder="" name="addProductoTable[' +
          id_item +
          '][caracteristicas]" style="height: 200px;">' +
          clearHTMLTextArea(detalle[i]["Txt_Descripcion"]) +
          "</textarea>";
        //button de chat que abre un modal
        var class_button_chat =
          parseInt(detalle[i]["Nu_Envio_Mensaje_Chat_Producto"]) > 0
            ? "warning"
            : "success";
        table_enlace_producto +=
          '<button type="button" id="btn-chat_producto' +
          id_item +
          '" data-id_empresa="' +
          response.ID_Empresa +
          '" data-id_organizacion="' +
          response.ID_Organizacion +
          '" data-id_pedido_cabecera="' +
          response.ID_Pedido_Cabecera +
          '" data-id_pedido_detalle="' +
          id_item +
          '" data-nombre_producto="' +
          nombre_producto +
          '" class="mt-3 btn btn-' +
          class_button_chat +
          ' btn-block btn-chat_producto"><i class="fas fa-comments"></i>&nbsp; Novedades <span class="badge bg-danger">' +
          detalle[i]["Nu_Envio_Mensaje_Chat_Producto"] +
          "</span></button>";
        table_enlace_producto += "</td>";

        table_enlace_producto +=
          "<td class='text-left td-name' width='10%'>" + href_link + "</td>";
        table_enlace_producto +=
          '<input type="hidden" name="addProductoTable[' +
          id_item +
          '][cantidad_oculta]" value="' +
          cantidad_item +
          '">';
        table_enlace_producto +=
          '<input type="hidden" name="addProductoTable[' +
          id_item +
          '][nombre_comercial_oculta]" value="' +
          detalle[i]["Txt_Producto"] +
          '">';
        table_enlace_producto +=
          '<input type="hidden" name="addProductoTable[' +
          id_item +
          '][caracteristicas_oculta]" value="' +
          detalle[i]["Txt_Descripcion"] +
          '">';
        table_enlace_producto += "</tr>";

        table_enlace_producto += "<tr><td class='text-center' colspan='4'>";
        if (response.Nu_Estado_China != 3) {
          //cotizacio china
          table_enlace_producto += '<div class="row">';
          table_enlace_producto += '<div class="col">';
          table_enlace_producto +=
            '<button type="button" id="btn-add_proveedor' +
            id_item +
            '" data-name_producto="' +
            nombre_producto +
            '" data-id_empresa="' +
            response.ID_Empresa +
            '" data-id_organizacion="' +
            response.ID_Organizacion +
            '" data-id_pedido_cabecera="' +
            response.ID_Pedido_Cabecera +
            '" data-correlativo="' +
            response.sCorrelativoCotizacion +
            '" data-id_pedido_detalle="' +
            id_item +
            '" class="btn btn-danger btn-block btn-add_proveedor"><i class="fas fa-plus-square"></i>&nbsp; Agregar Proveedor</button>';
          table_enlace_producto += "</div>";
          table_enlace_producto += '<div class="col">';
          table_enlace_producto +=
            '<button type="button" id="btn-elegir_proveedor' +
            id_item +
            '" data-name_producto="' +
            nombre_producto +
            '" data-id_empresa="' +
            response.ID_Empresa +
            '" data-id_organizacion="' +
            response.ID_Organizacion +
            '" data-id_pedido_cabecera="' +
            response.ID_Pedido_Cabecera +
            '" data-correlativo="' +
            response.sCorrelativoCotizacion +
            '" data-id_pedido_detalle="' +
            id_item +
            '" class="btn btn-secondary btn-block btn-elegir_proveedor"><i class="far fa-edit"></i>&nbsp; Editar Proveedor</button>';
          table_enlace_producto += "</div>";
          table_enlace_producto += "</div>";
        } else {
          table_enlace_producto +=
            '<button type="button" id="btn-elegir_proveedor' +
            id_item +
            '" data-name_producto="' +
            nombre_producto +
            '" data-id_empresa="' +
            response.ID_Empresa +
            '" data-id_organizacion="' +
            response.ID_Organizacion +
            '" data-id_pedido_cabecera="' +
            response.ID_Pedido_Cabecera +
            '" data-correlativo="' +
            response.sCorrelativoCotizacion +
            '" data-id_pedido_detalle="' +
            id_item +
            '" class="btn btn-danger btn-block btn-elegir_proveedor"><i class="fas fa-check"></i>&nbsp; Elegir proveedor</button>';
        }
        table_enlace_producto += "</td></tr>";
      }

      $("#span-total_cantidad_items").html(i);
      $("#table-Producto_Enlace").append(table_enlace_producto);
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

function cambiarEstado(ID, Nu_Estado, ID_Usuario_Interno_Empresa_China) {
  var sNombreEstado = "Garantizado";
  if (Nu_Estado == 3) {
    sNombreEstado = "Enviado";
    /*
    if(ID_Usuario_Interno_Empresa_China==0){
      $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
      $('#modal-message').modal('show');
      $('#moda-message-content').addClass( 'bg-warning' );
      $('.modal-title-message').text('Primero asignar personal de china');
    }
    */
  } else if (Nu_Estado == 4) sNombreEstado = "Rechazado";
  else if (Nu_Estado == 5) {
    sNombreEstado = "Aprobado";

    if (ID_Usuario_Interno_Empresa_China == 0) {
      $("#moda-message-content").removeClass("bg-danger bg-warning bg-success");
      $("#modal-message").modal("show");
      $("#moda-message-content").addClass("bg-warning");
      $(".modal-title-message").text("Primero asignar personal de china");
    }
  } else if (Nu_Estado == 8) sNombreEstado = "Obervado";

  var $modal_delete = $("#modal-message-delete");
  $modal_delete.modal("show");

  $(".modal-message-delete").removeClass(
    "modal-danger modal-warning modal-success"
  );
  $(".modal-message-delete").addClass("modal-success");

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
        "AgenteCompra/PedidosGarantizados/cambiarEstado/" +
        ID +
        "/" +
        Nu_Estado +
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

function cambiarEstadoChina(ID, Nu_Estado, sCorrelativo) {
  var $modal_delete = $("#modal-message-delete");
  $modal_delete.modal("show");

  $(".modal-message-delete").removeClass(
    "modal-danger modal-warning modal-success"
  );
  $(".modal-message-delete").addClass("modal-success");

  var sNombreEstado = "Pendiente";
  if (Nu_Estado == 2) sNombreEstado = "En proceso";
  else if (Nu_Estado == 3) sNombreEstado = "Cotizado";

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
        "AgenteCompra/PedidosGarantizados/cambiarEstadoChina/" +
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
    $("#btn-save").html(
      'Guardando <div class="spinner-border" role="status"><span class="sr-only"></span></div>'
    );
    var postData = new FormData($("#form-pedido")[0]);
      url = base_url + "AgenteCompra/PedidosGarantizados/crudPedidoGrupal";
    $.ajax({
      type: "POST",
      dataType: "JSON",
      url: url,
      data: postData,
      mimeType: "multipart/form-data",
      contentType: false,
      cache: false,
      processData: false,
      //data		  : $('#form-pedido').serialize(),
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
  url = base_url + "AgenteCompra/PedidosGarantizados/generarAgenteCompra/" + ID;
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
  url =
    base_url + "AgenteCompra/PedidosGarantizados/generarConsolidaTrading/" + ID;
  window.open(url, "_blank");
}

function addItems() {
  div_items = `
  <div id="card"${iCounterItems}" class="card border-0 rounded shadow-sm mt-3">
    <div class = "row" >
      <div class="col-6 col-md-3 col-lg-2">
        <span class="fw-bold">Precio ¥<span class="label-advertencia text-danger"> *</span><span/>
        <div class="form-group">
          <input type="text" id="modal-precio${iCounterItems}" data-correlativo="${iCounterItems}" inputmode="decimal" name="addProducto[${iCounterItems}][precio]" class="arrProducto form-control required precio input-decimal" placeholder="" value="" autocomplete="off" />
         <span class="help-block text-danger" id="error"></span>
        </div>
      </div>
      <div class="col-6 col-md-3 col-lg-2">
        <span class="fw-bold">Moq<span class="label-advertencia text-danger"> *</span><span/>
        <div class="form-group">
          <input type="text" id="modal-moq${iCounterItems}" data-correlativo="${iCounterItems}" inputmode="decimal" name="addProducto[${iCounterItems}][moq]" class="arrProducto form-control required moq input-decimal" placeholder="" value="" autocomplete="off" />
          <span class="help-block text-danger" id="error"></span>
        </div>
      </div>
      <div class="col-6 col-md-3 col-lg-2">
        <span class="fw-bold">Qty_caja<span class="label-advertencia text-danger"> *</span><span/>
        <div class="form-group">
          <input type="text" id="modal-qty_caja${iCounterItems}" data-correlativo="${iCounterItems}" inputmode="decimal" name="addProducto[${iCounterItems}][qty_caja]" class="arrProducto form-control required qty_caja input-decimal" placeholder="" value="" autocomplete="off" />
          <span class="help-block text-danger" id="error"></span>
        </div>
      </div>
      <div class="col-6 col-md-3 col-lg-2">
        <span class="fw-bold">Cbm<span class="label-advertencia text-danger"> *</span><span/>
        <div class="form-group">
          <input type="text" id="modal-cbm${iCounterItems}" data-correlativo="${iCounterItems}" inputmode="decimal" name="addProducto[${iCounterItems}][cbm]" class="arrProducto form-control required cbm input-decimal" placeholder="" value="" autocomplete="off" />
          <span class="help-block text-danger" id="error"></span>
        </div>
      </div>
      <div class="col-6 col-md-3 col-lg-2">
        <span class="fw-bold">Delivery<span class="label-advertencia text-danger"> *</span><span/>
        <div class="form-group">
          <input type="text" id="modal-delivery${iCounterItems}" data-correlativo="${iCounterItems}" inputmode="decimal" name="addProducto[${iCounterItems}][delivery]" class="arrProducto form-control required delivery input-decimal" placeholder="" value="" autocomplete="off" />
          <span class="help-block text-danger" id="error"></span>
        </div>
      </div>
      <div class="col-6 col-md-3 col-lg-2">
        <span class="fw-bold">Shipping Cost<span class="label-advertencia text-danger"> *</span><span/>
        <div class="form-group">
          <input type="text" id="modal-costo_delivery${iCounterItems}" data-correlativo="${iCounterItems}" inputmode="decimal" name="addProducto[${iCounterItems}][shipping_cost]" class="arrProducto form-control required shipping_cost input-decimal" placeholder="" value="" autocomplete="off" />
          <span class="help-block text-danger" id="error"></span>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12 col-md-6 col-lg-6">
        <div class="row h-100">
          <div class="col-12 col-md-8 col-lg-8 d-flex flex-column justify-content-center">
            <!--Upload Icon-->
            <input type="file" name="file[${iCounterItems}][main_photo]" class="btn btn-outline-primary btn-block" id="btn-uploadprimaryimg-${iCounterItems}" data-correlativo="${iCounterItems}" data-toggle="modal" data-target="#modal-upload${iCounterItems}"><i class="fas fa-upload"></i> Subir Imagen Principal</input>
          </div>
          <div class="col-12 col-md-4 col-lg-4 d-flex flex-column justify-content-center">
            <input type="file" name="file[${iCounterItems}][secondary_photo]" class="btn btn-outline-primary btn-block" id="btn-uploadimg2-${iCounterItems}" data-correlativo="${iCounterItems}" data-toggle="modal" data-target="#modal-upload${iCounterItems}"><i class="fas fa-upload"></i> Subir Imagen 2</input>
            <input type="file" name="file[${iCounterItems}][terciary_photo]" class="btn btn-outline-primary btn-block" id="btn-uploadimg3-${iCounterItems}" data-correlativo="${iCounterItems}" data-toggle="modal" data-target="#modal-upload${iCounterItems}"><i class="fas fa-upload"></i> Subir Imagen 3</input>
            <input type="file" name="file[${iCounterItems}][primary_video]"class="btn btn-outline-primary btn-block" id="btn-uploadvideo1-${iCounterItems}" data-correlativo="${iCounterItems}" data-toggle="modal" data-target="#modal-upload${iCounterItems}"><i class="fas fa-upload"></i> Subir Video 1</input>
            <input type="file" name="file[${iCounterItems}][secondary_video]"class="btn btn-outline-primary btn-block" id="btn-uploadvideo2-${iCounterItems}" data-correlativo="${iCounterItems}" data-toggle="modal" data-target="#modal-upload${iCounterItems}"><i class="fas fa-upload"></i> Subir Video 2</input>

          </div>
        </div>
      </div>
      <div class="col-12 col-md-6 col-lg-6">
        <span class="fw-bold">Nombre Proveedor<span class="label-advertencia text-danger"> *</span><span/>
        <div class="form-group">
          <input type="text" id="modal-nombre_proveedor${iCounterItems}" data-correlativo="${iCounterItems}" name="addProducto[${iCounterItems}][nombre_proveedor]" class="arrProducto form-control required nombre_proveedor" placeholder="" value="" autocomplete="off" />
          <span class="help-block text-danger" id="error"></span>
        </div>
        <span class="fw-bold">N° Celular<span class="label-advertencia text-danger"> *</span><span/>
        <div class="form-group">
          <input type="text" id="modal-celular_proveedor${iCounterItems}" data-correlativo="${iCounterItems}" name="addProducto[${iCounterItems}][celular_proveedor]" class="arrProducto form-control required celular_proveedor" placeholder="" value="" autocomplete="off" />
          <span class="help-block text-danger" id="error"></span>
        </div>
        <span class="fw-bold">Notas <span class="label-advertencia text-danger"> </span><span/>
        <div class="form-group">
          <textarea id="modal-notas${iCounterItems}" data-correlativo="${iCounterItems}" name="addProducto[${iCounterItems}][notas]" class="arrProducto form-control required notas" placeholder="" value="" autocomplete="off" ></textarea>
        </div>

      </div>
    </div>
  </div>
  `;

  // div_items +=
  //   '<div id="card' +
  //   iCounterItems +
  //   '" class="card border-0 rounded shadow-sm mt-3">';
  // div_items += '<div class="row">';
  // div_items += '<div class="col-sm-12">';
  // div_items += '<div class="card-body pt-3">';
  // div_items += '<div class="row">';
  // div_items +=
  //   '<div class="col-11 col-sm-11 col-md-11 col-lg-11 mb-0 mb-sm-0">';
  // div_items +=
  //   '<h6 class="text-left card-title mb-2 pt-0" style="text-align: left;">';
  // div_items +=
  //   '<span class="fw-bold" style="font-weight: bold;">Imagen<span class="label-advertencia text-danger"> *</span></span>';
  // div_items += "</h6>";
  // div_items += '<div class="form-group">';
  // div_items +=
  //   '<input class="form-control" name="voucher[' +
  //   iCounterItems +
  //   '][]" type="file" accept="image/*" multiple></input>';
  // //div_items += '<input class="form-control" name="addProducto[' + iCounterItems + '][voucher][]" type="file" accept="image/*" multiple></input>';
  // div_items += '<span class="help-block text-danger" id="error"></span>';
  // div_items += "</div>";
  // div_items += "</div>";

  // div_items += '<div class="col-1 col-sm-1 col-md-1 col-lg-1">';
  // div_items += '<span class="fw-bold" style="font-weight: bold;">&nbsp;</span>';
  // div_items +=
  //   '<div class="d-grid gap"><button type="button" id="btn-quitar_item_' +
  //   iCounterItems +
  //   '" class="btn btn-outline-danger btn-quitar_item col" data-id="' +
  //   iCounterItems +
  //   '">X</div>';
  // div_items += "</div>";

  // div_items += '<div class="col-6 col-sm-3 col-md-3 col-lg-2 mb-0 mb-sm-0">';
  // div_items += '<h6 class="card-title mb-2" style="font-weight:bold">';
  // div_items +=
  //   '<span class="fw-bold">Precio ¥<span class="label-advertencia text-danger"> *</span></span>';
  // div_items += "</h6>";
  // div_items += '<div class="form-group">';
  // div_items +=
  //   '<input type="text" id="modal-precio' +
  //   iCounterItems +
  //   '" data-correlativo="' +
  //   iCounterItems +
  //   '" inputmode="decimal" name="addProducto[' +
  //   iCounterItems +
  //   '][precio]" class="arrProducto form-control required precio input-decimal" placeholder="" value="" autocomplete="off" />';
  // div_items += '<span class="help-block text-danger" id="error"></span>';
  // div_items += "</div>";
  // div_items += "</div>";

  // div_items += '<div class="col-6 col-sm-3 col-md-3 col-lg-2 mb-0 mb-sm-0">';
  // div_items += '<h6 class="card-title mb-2" style="font-weight:bold">';
  // div_items +=
  //   '<span class="fw-bold">moq<span class="label-advertencia text-danger"> *</span></span>';
  // div_items += "</h6>";
  // div_items += '<div class="form-group">';
  // div_items +=
  //   '<input type="text" id="modal-moq' +
  //   iCounterItems +
  //   '" data-correlativo="' +
  //   iCounterItems +
  //   '" inputmode="decimal" name="addProducto[' +
  //   iCounterItems +
  //   '][moq]" class="arrProducto form-control required moq input-decimal" placeholder="" value="" autocomplete="off" />';
  // div_items += '<span class="help-block text-danger" id="error"></span>';
  // div_items += "</div>";
  // div_items += "</div>";

  // div_items += '<div class="col-6 col-sm-3 col-md-3 col-lg-2 mb-0 mb-sm-0">';
  // div_items += '<h6 class="card-title mb-2" style="font-weight:bold">';
  // div_items +=
  //   '<span class="fw-bold">qty_caja<span class="label-advertencia text-danger"> *</span></span>'; //qty_caja_actual
  // div_items += "</h6>";
  // div_items += '<div class="form-group">';
  // div_items +=
  //   '<input type="text" id="modal-qty_caja' +
  //   iCounterItems +
  //   '" data-correlativo="' +
  //   iCounterItems +
  //   '" inputmode="decimal" name="addProducto[' +
  //   iCounterItems +
  //   '][qty_caja]" class="arrProducto form-control required qty_caja input-decimal" placeholder="" value="" autocomplete="off" />';
  // div_items += '<span class="help-block text-danger" id="error"></span>';
  // div_items += "</div>";
  // div_items += "</div>";

  // div_items += '<div class="col-6 col-sm-3 col-md-3 col-lg-2 mb-0 mb-sm-0">';
  // div_items += '<h6 class="card-title mb-2" style="font-weight:bold">';
  // div_items +=
  //   '<span class="fw-bold">cbm<span class="label-advertencia text-danger"> *</span></span>';
  // div_items += "</h6>";
  // div_items += '<div class="form-group">';
  // div_items +=
  //   '<input type="text" id="modal-cbm' +
  //   iCounterItems +
  //   '" data-correlativo="' +
  //   iCounterItems +
  //   '" inputmode="decimal" name="addProducto[' +
  //   iCounterItems +
  //   '][cbm]" class="arrProducto form-control required input-decimal" cbm placeholder="" value="" autocomplete="off" />';
  // div_items += '<span class="help-block text-danger" id="error"></span>';
  // div_items += "</div>";
  // div_items += "</div>";

  // div_items += '<div class="col-12 col-sm-3 col-md-3 col-lg-2 mb-3 mb-sm-3">';
  // div_items += '<h6 class="card-title mb-2" style="font-weight:bold">';
  // div_items += '<span class="fw-bold">Delivery</span>';
  // div_items += "</h6>";
  // div_items +=
  //   '<input type="text" inputmode="numeric" id="modal-delivery' +
  //   iCounterItems +
  //   '" name="addProducto[' +
  //   iCounterItems +
  //   '][delivery]" class="arrProducto form-control input-number" placeholder="" minlength="1" maxlength="90" autocomplete="off" />';
  // div_items += "</div>";

  // div_items += '<div class="col-12 col-sm-3 col-md-3 col-lg-2 mb-3 mb-sm-3">';
  // div_items += '<h6 class="card-title mb-2" style="font-weight:bold">';
  // div_items += '<span class="fw-bold">Shipping Cost</span>';
  // div_items += "</h6>";
  // div_items +=
  //   '<input type="text" inputmode="decimal" id="modal-costo_delivery' +
  //   iCounterItems +
  //   '" name="addProducto[' +
  //   iCounterItems +
  //   '][costo_delivery]" class="arrProducto form-control input-decimal" placeholder="" minlength="1" maxlength="90" autocomplete="off" />';
  // div_items += "</div>";

  // div_items += '<div class="col-sm-12 mb-1">';
  // div_items += '<h6 class="card-title mb-2" style="font-weight:bold">';
  // div_items += '<span class="fw-bold">Observaciones</span>';
  // div_items += "</h6>";
  // div_items += '<div class="form-group">';
  // div_items +=
  //   '<textarea class="arrProducto form-control required nota" rows="1" placeholder="Opcional" id="modal-nota' +
  //   iCounterItems +
  //   '" name="addProducto[' +
  //   iCounterItems +
  //   '][nota]" style="height: 50px;"></textarea>';
  // div_items += '<span class="help-block text-danger" id="error"></span>';
  // div_items += "</div>";
  // div_items += "</div>";

  // div_items += '<div class="col-12 col-sm-6 mb-1">';
  // div_items += '<h6 class="card-title mb-2" style="font-weight:bold">';
  // div_items += '<span class="fw-bold">Nombre Proveedor</span>';
  // div_items += "</h6>";
  // div_items += '<div class="form-group">';
  // div_items +=
  //   '<input type="text" inputmode="text" id="modal-contacto_proveedor' +
  //   iCounterItems +
  //   '" name="addProducto[' +
  //   iCounterItems +
  //   '][contacto_proveedor]" class="arrProducto form-control" placeholder="" maxlength="255" autocomplete="off" />';
  // div_items += '<span class="help-block text-danger" id="error"></span>';
  // div_items += "</div>";
  // div_items += "</div>";

  // div_items += '<div class="col-12 col-sm-6 mb-0">';
  // div_items += '<h6 class="card-title mb-2" style="font-weight:bold">';
  // div_items += '<span class="fw-bold">Foto Proveedor</span>';
  // div_items += "</h6>";
  // div_items += '<div class="form-group">';
  // div_items +=
  //   '<input class="form-control" id="modal-foto_proveedor' +
  //   iCounterItems +
  //   '" name="proveedor[' +
  //   iCounterItems +
  //   ']" type="file" accept="image/*"></input>';
  // //div_items += '<input type="text" inputmode="text" id="modal-foto_proveedor' + iCounterItems + '" name="addProducto[' + iCounterItems + '][foto_proveedor]" class="arrProducto form-control input-number" placeholder="" maxlength="255" autocomplete="off" />';
  // div_items += '<span class="help-block text-danger" id="error"></span>';
  // div_items += "</div>";
  // div_items += "</div>";

  // div_items += "</div>";
  // div_items += "</div>";
  // div_items += "</div>";
  // div_items += "</div>";
  // div_items += "</div>";

  $("#div-arrItems").append(div_items);

  $("#modal-precio" + iCounterItems).focus();

  validateNumberLetter();
  validateDecimal();
  validateNumber();

  ++iCounterItems;
}

function validateNumberLetter() {
  $(".input-number_letter").unbind();
  $(".input-number_letter").on("input", function () {
    this.value = this.value.replace(/[^a-zA-Z0-9]/g, "");
  });
}

function validateDecimal() {
  $(".input-decimal").unbind();
  $(".input-decimal").on("input", function () {
    numero = parseFloat(this.value);
    if (!isNaN(numero)) {
      this.value = this.value.replace(/[^0-9\.]/g, "");
      if (numero < 0) this.value = "";
    } else this.value = this.value.replace(/[^0-9\.]/g, "");
  });
}

function validateNumber() {
  $(".input-number").unbind();
  $(".input-number").on("input", function () {
    this.value = this.value.replace(/[^0-9]/g, "");
  });
}

function scrollToError($sMetodo, $IdElemento) {
  $sMetodo.animate(
    {
      scrollTop: $IdElemento.offset().top - 100,
    },
    "slow"
  );
}
function getItemTemplate(i, mode, detalle) {
  div_items = `
    <div id="card"${i}" class="card border-0 rounded shadow-sm mt-3">
      <input type="hidden" id="modal-detalle${i}" data-correlativo="${i}" inputmode="decimal" name="addProducto[${i}][id_detalle]" class="arrProducto form-control required precio input-decimal" placeholder="" value="" autocomplete="off" />
      <input type="hidden" id="modal-pedido-cabecera${i}" data-correlativo="${i}" inputmode="decimal" name="addProducto[${i}][pedido-cabecera]" class="arrProducto form-control required precio input-decimal" placeholder="" value="" autocomplete="off" />
      <input type="hidden" id="modal_proveedor-id-${i}" value="${detalle.id_pedido}"/>
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
              </br>
              <input type="hidden" name="addProducto[${i}][main_photo]" id="btn-uploadprimaryimg-URL-${i}"/>
              <input type="file" name="file[${i}][main_photo]" class="btn btn-outline-primary btn-block" id="btn-uploadprimaryimg-${i}" data-correlativo="${i}" data-toggle="modal" data-target="#modal-upload${i}"></input>
              </div>
            </div>
            <div class="col-12 col-md-4 col-lg-4 d-flex flex-column justify-content-center">
            <div class="form-group" id="container-uploadimg2-${i}">
            <label>Imagen 2</label>
            <input type="hidden" name="addProducto[${i}][secondary_photo]" id="btn-uploadimg2-URL-${i}"/>            
            <input type="file" name="file[${i}][secondary_photo]" class="btn btn-outline-primary btn-block" id="btn-uploadimg2-${i}" data-correlativo="${i}" data-toggle="modal" data-target="#modal-upload${i}"></input>
            </div>
              <div class="form-group" id="container-uploadimg3-${i}">
              <label>Imagen 3</label>
              <input type="hidden" name="addProducto[${i}][terciary_photo]" id="btn-uploadimg3-URL-${i}"/>
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
  var id_detalle = detalle[i - 1]["ID_Pedido_Detalle"];
  var id_item = detalle[i - 1]["ID_Pedido_Detalle_Producto_Proveedor"];
  var id_supplier = detalle[i - 1]["id_supplier"];
  if (mode == "edit") {
  } else {
    if (detalle[i - 1]["Nu_Selecciono_Proveedor"] == 0) {
      div_items += `
            <button type="button" id="btn-seleccionar_proveedor${id_item}" 
              data-id_detalle="${id_detalle}" 
              data-id="${id_item}" 
              data-correlativo="${$("#txt-Item_ECorrelativo_Editar").val()}" 
              data-name_item="${$("#txt-Item_Ename_producto_Editar").val()}" 
              data-id_supplier="${id_supplier}"
              class="btn btn-danger btn-block btn-seleccionar_proveedor">
              <i class="fas fa-check"></i>&nbsp; Seleccionar proveedor
            </button>`;
    } else {
      div_items += `
            <button type="button" id="btn-desmarcar_proveedor${id_item}" 
              data-id_detalle="${id_detalle}" 
              data-id="${id_item}" 
              data-id_supplier="${id_supplier}"
              data-correlativo="${$("#txt-Item_ECorrelativo_Editar").val()}" 
              data-name_item="${$("#txt-Item_Ename_producto_Editar").val()}" 
              class="btn btn-secondary btn-block btn-desmarcar_proveedor">
              <i class="fas fa-times"></i>&nbsp; Deseleccionar proveedor
            </button>`;
    }
  }
  div_items += `</div>`;
  return div_items;
}
function getItemProveedor(id_detalle) {
  url =
    base_url +
    "AgenteCompra/PedidosGarantizados/getItemProveedor/" +
    id_detalle;
  $.ajax({
    url: url,
    type: "GET",
    dataType: "JSON",
    success: function (response) {
      var detalle = response;
      const container = $("#table-elegir_productos_proveedor tbody");
      container.empty();

      for (i = 0; i < detalle.length; i++) {
        let item = getItemTemplate(i + 1, "select", detalle);

        container.append(item);
        container.find(`#modal-precio${i + 1}`).val(detalle[i]["Ss_Precio"]);
        container.find(`#modal-moq${i + 1}`).val(detalle[i]["Qt_Producto_Moq"]);
        container
          .find(`#modal-qty_caja${i + 1}`)
          .val(detalle[i]["Qt_Producto_Caja"]);
        container.find(`#modal-cbm${i + 1}`).val(detalle[i]["Qt_Cbm"]);
        container
          .find(`#modal-delivery${i + 1}`)
          .val(detalle[i]["Nu_Dias_Delivery"]);
        container
          .find(`#modal-costo_delivery${i + 1}`)
          .val(detalle[i]["Ss_Costo_Delivery"]);
        container
          .find(`#modal-nombre_proveedor${i + 1}`)
          .val(detalle[i]["No_Contacto_Proveedor"]);
        container
          .find(`#modal-celular_proveedor${i + 1}`)
          .val(detalle[i]["No_Celular_Contacto_Proveedor"]);
        container
          .find(`#modal-detalle${i + 1}`)
          .val(detalle[i]["ID_Pedido_Detalle_Producto_Proveedor"]);
        container
          .find(`#modal-pedido-cabecera${i + 1}`)
          .val(detalle[i]["ID_Pedido_Cabecera"]);
        container
          .find(`#modal-nombre_proveedor${i + 1}`)
          .val(detalle[i]["nombre_proveedor"]);
        container
          .find(`#modal-celular_proveedor${i + 1}`)
          .val(detalle[i]["celular_proveedor"]);

        container
          .find(`#btn-uploadprimaryimg-URL-${i + 1}`)
          .val(detalle[i]["main_photo"]);
        if (detalle[i]["main_photo"] != null) {
          container
            .find(`#btn-uploadprimaryimg-URL-${i + 1}`)
            .val(detalle[i]["main_photo"]);
          container.find(`#btn-uploadprimaryimg-${i + 1}`).hide();
          container
            .find(`#container-uploadprimaryimg-${i + 1}`)
            .append(
              `<img src="${detalle[i]["main_photo"]}" class="img-thumbnail img-table_item img-fluid img-resize mb-2">`
            );
        }
        if (detalle[i]["secondary_photo"] != null) {
          container
            .find(`#btn-uploadimg2-URL-${i + 1}`)
            .val(detalle[i]["secondary_photo"]);
          container.find(`#btn-uploadimg2-${i + 1}`).hide();
          container
            .find(`#container-uploadimg2-${i + 1}`)
            .append(
              `<img src="${detalle[i]["secondary_photo"]}" class="img-thumbnail img-table_item img-fluid img-resize mb-2">`
            );
        }
        if (detalle[i]["terciary_photo"] != null) {
          container
            .find(`#btn-uploadimg3-URL-${i + 1}`)
            .val(detalle[i]["terciary_photo"]);
          container.find(`#btn-uploadimg3-${i + 1}`).hide();
          container
            .find(`#container-uploadimg3-${i + 1}`)
            .append(
              `<img src="${detalle[i]["terciary_photo"]}" class="img-thumbnail img-table_item img-fluid img-resize mb-2">`
            );
        }
        if (detalle[i]["primary_video"] != null) {
          container
            .find(`#btn-uploadvideo1-URL-${i + 1}`)
            .val(detalle[i]["primary_video"]);
          container.find(`#btn-uploadvideo1-${i + 1}`).hide();
          container
            .find(`#container-uploadvideo1-${i + 1}`)
            .append(
              `<video src="${detalle[i]["primary_video"]}" class="img-thumbnail img-table_item img-fluid img-resize mb-2" controls></video>`
            );
        }
        if (detalle[i]["secondary_video"] != null) {
          container
            .find(`#btn-uploadvideo2-URL-${i + 1}`)
            .val(detalle[i]["secondary_video"]);
          container.find(`#btn-uploadvideo2-${i + 1}`).hide();
          container
            .find(`#container-uploadvideo2-${i + 1}`)
            .append(
              "<video src='" +
                detalle[i]["secondary_video"] +
                "' class='img-thumbnail img-table_item img-fluid img-resize mb-2' controls></video>"
            );
        }

        // container.find(`btn-uploadprimaryimg-${i}`).val(detalle[i]["main_photo"]);
      }
      return;
      var table_enlace_producto = "",
        id_item_tmp = 0;
      for (i = 0; i < detalle.length; i++) {
        var id_detalle = detalle[i]["ID_Pedido_Detalle"];
        var id_item = detalle[i]["ID_Pedido_Detalle_Producto_Proveedor"];
        var cantidad = detalle[i]["Qt_Producto_Caja"];
        var nota = detalle[i]["Txt_Nota"];
        var cantidad_final = detalle[i]["Qt_Producto_Caja_Final"];
        var nota_final =
          detalle[i]["Txt_Nota_Final"] != "" &&
          detalle[i]["Txt_Nota_Final"] != null
            ? detalle[i]["Txt_Nota_Final"]
            : "";
        var cantidad_html =
          parseFloat(cantidad_final) > parseFloat(cantidad)
            ? cantidad_final
            : cantidad;
        var contacto_proveedor = detalle[i]["No_Contacto_Proveedor"];
        var imagen_proveedor = detalle[i]["Txt_Url_Imagen_Proveedor"];

        if (id_item_tmp != id_item) {
          table_enlace_producto +=
            "<tr id='tr_enlace_producto" +
            id_item +
            "'>" +
            "<td style='display:none;' class='text-left td-id_item'>" +
            id_item +
            "</td>" +
            "<td class='text-left td-imagen'>";

          for (x = 0; x < detalle.length; x++) {
            if (id_item == detalle[x]["ID_Pedido_Detalle_Producto_Proveedor"]) {
              table_enlace_producto +=
                "<img data-id_item='" +
                id_item +
                "' data-url_img='" +
                detalle[x]["Txt_Url_Imagen_Producto"] +
                "' src='" +
                detalle[x]["Txt_Url_Imagen_Producto"] +
                "' class='img-thumbnail img-table_item img-fluid img-resize mb-2'>";
              table_enlace_producto += "<br>";
            }
          }

          table_enlace_producto += "</td>";

          table_enlace_producto +=
            "<td class='text-left td-precio'>" +
            (
              parseFloat(detalle[i]["Ss_Precio"]) *
              parseFloat(detalle[i]["Ss_Tipo_Cambio"])
            ).toPrecision(6) +
            "</td>";

          //+ "<td class='text-left td-precio'>" + detalle[i]['Ss_Precio'] + "</td>"
          table_enlace_producto += "<td class='text-left td-precio'>";
          table_enlace_producto +=
            '<input type="text" id="modal-precio' +
            i +
            '" data-correlativo="' +
            i +
            '" inputmode="decimal" name="addProducto[' +
            i +
            '][precio]" class="arrProducto form-control required precio input-decimal mt-0" placeholder="precio" value="' +
            detalle[i]["Ss_Precio"] +
            '" autocomplete="off" />';
          table_enlace_producto += "</td>";

          //table_enlace_producto += "<td class='text-left td-moq'>" + detalle[i]['Qt_Producto_Moq'] + "</td>"
          table_enlace_producto += "<td class='text-left td-moq'>";
          table_enlace_producto +=
            '<input type="text" id="modal-moq' +
            i +
            '" data-correlativo="' +
            i +
            '" inputmode="decimal" name="addProducto[' +
            i +
            '][moq]" class="arrProducto form-control required moq input-decimal mt-0" placeholder="moq" value="' +
            detalle[i]["Qt_Producto_Moq"] +
            '" autocomplete="off" />';
          table_enlace_producto += "</td>";

          //+ "<td class='text-left td-caja'>" + detalle[i]['Qt_Producto_Caja'] + "</td>"
          table_enlace_producto += "<td class='text-left td-caja'>";
          table_enlace_producto +=
            '<input type="text" id="modal-caja' +
            i +
            '" data-correlativo="' +
            i +
            '" inputmode="decimal" name="addProducto[' +
            i +
            '][caja]" class="arrProducto form-control required caja input-decimal mt-0" placeholder="caja" value="' +
            detalle[i]["Qt_Producto_Caja"] +
            '" autocomplete="off" />';
          table_enlace_producto += "</td>";

          //+ "<td class='text-left td-cbm'>" + detalle[i]['Qt_Cbm'] + "</td>"
          table_enlace_producto += "<td class='text-left td-cbm'>";
          table_enlace_producto +=
            '<input type="text" id="modal-cbm' +
            i +
            '" data-correlativo="' +
            i +
            '" inputmode="decimal" name="addProducto[' +
            i +
            '][cbm]" class="arrProducto form-control required cbm input-decimal mt-0" placeholder="cbm" value="' +
            detalle[i]["Qt_Cbm"] +
            '" autocomplete="off" />';
          table_enlace_producto += "</td>";

          //+ "<td class='text-left td-delivery'>" + detalle[i]['Nu_Dias_Delivery'] + "</td>"
          table_enlace_producto += "<td class='text-left td-delivery'>";
          table_enlace_producto +=
            '<input type="text" id="modal-delivery' +
            i +
            '" data-correlativo="' +
            i +
            '" inputmode="decimal" name="addProducto[' +
            i +
            '][delivery]" class="arrProducto form-control required delivery input-decimal mt-0" placeholder="delivery" value="' +
            detalle[i]["Nu_Dias_Delivery"] +
            '" autocomplete="off" />';
          table_enlace_producto += "</td>";

          //+ "<td class='text-left td-costo_delivery'>" + detalle[i]['Ss_Costo_Delivery'] + "</td>"
          table_enlace_producto += "<td class='text-left td-costo_delivery'>";
          table_enlace_producto +=
            '<input type="text" id="modal-costo_delivery' +
            i +
            '" data-correlativo="' +
            i +
            '" inputmode="decimal" name="addProducto[' +
            i +
            '][costo_delivery]" class="arrProducto form-control required costo_delivery input-decimal mt-0" placeholder="costo_delivery" value="' +
            detalle[i]["Ss_Costo_Delivery"] +
            '" autocomplete="off" />';
          table_enlace_producto += "</td>";

          //+ "<td class='text-left td-nota'>" + detalle[i]['Txt_Nota'] + "</td>"
          table_enlace_producto += "<td class='text-left td-nota_historica'>";
          table_enlace_producto +=
            '<textarea id="modal-nota_historica' +
            i +
            '" name="addProducto[' +
            i +
            '][nota_historica]" class="mt-0 form-control required nota_historica" placeholder="Observaciones" rows="1" style="height: 60px;">' +
            clearHTMLTextArea(detalle[i]["Txt_Nota"]) +
            "</textarea>";
          table_enlace_producto += "</td>";

          //+ "<td class='text-left td-nota'>" + detalle[i]['Txt_Nota'] + "</td>"
          table_enlace_producto +=
            "<td class='text-left td-contacto_proveedor'>";
          table_enlace_producto +=
            '<input type="text" inputmode="text" id="modal-contacto_proveedor' +
            i +
            '" name="addProducto[' +
            i +
            '][contacto_proveedor]" class="mt-0 form-control required contacto_proveedor" placeholder="" maxlength="255" autocomplete="off" value="' +
            contacto_proveedor +
            '">';
          table_enlace_producto += "</td>";

          //+ "<td class='text-left td-nota'>" + detalle[i]['Txt_Nota'] + "</td>"
          table_enlace_producto += "<td class='text-left td-foto_proveedor'>";
          table_enlace_producto +=
            '<input class="form-control" id="modal-foto_proveedor' +
            i +
            '" name="addProveedor[' +
            i +
            ']" type="file" accept="image/*">';
          if (imagen_proveedor != "" && imagen_proveedor != null) {
            table_enlace_producto +=
              "<img data-id_item='" +
              id_item +
              "' data-url_img='" +
              imagen_proveedor +
              "' src='" +
              imagen_proveedor +
              "' class='img-thumbnail img-table_item img-fluid img-resize mb-2'>";
          }
          table_enlace_producto += "</td>";
          table_enlace_producto += "</tr>";

          table_enlace_producto += "<tr><td class='text-center' colspan='11'>";
          if (detalle[i]["Nu_Selecciono_Proveedor"] == 0) {
            table_enlace_producto +=
              '<button type="button" id="btn-seleccionar_proveedor' +
              id_item +
              '" data-id_detalle="' +
              id_detalle +
              '" data-id="' +
              id_item +
              '" data-correlativo="' +
              $("#txt-Item_ECorrelativo_Editar").val() +
              '" data-name_item="' +
              $("#txt-Item_Ename_producto_Editar").val() +
              '" class="btn btn-danger btn-block btn-seleccionar_proveedor"><i class="fas fa-check"></i>&nbsp; Seleccionar proveedor</button>';
          } else {
            table_enlace_producto +=
              '<button type="button" id="btn-desmarcar_proveedor' +
              id_item +
              '" data-id_detalle="' +
              id_detalle +
              '" data-id="' +
              id_item +
              '" data-correlativo="' +
              $("#txt-Item_ECorrelativo_Editar").val() +
              '" data-name_item="' +
              $("#txt-Item_Ename_producto_Editar").val() +
              '" class="btn btn-secondary btn-block btn-desmarcar_proveedor"><i class="fas fa-times"></i>&nbsp; Deseleccionar proveedor</button>';

            table_enlace_producto += '<div class="row">';
            table_enlace_producto += '<div class="col-1 col-sm-1 mt-3">';
            table_enlace_producto += "<label>qty_total</label>"; //qty_caja_actual
            table_enlace_producto +=
              '<input type="text" id="modal-cantidad' +
              i +
              '" data-correlativo="' +
              i +
              '" inputmode="numeric" name="addProducto[' +
              i +
              '][cantidad]" class="arrProducto form-control required cantidad input-numeric" placeholder="Cantidad" value="' +
              cantidad_html +
              '" autocomplete="off" />';
            table_enlace_producto += "</div>";
            table_enlace_producto +=
              '<div class="text-left col-11 col-sm-11 mt-3">';
            table_enlace_producto += "<label>Observaciones</label>";
            table_enlace_producto +=
              '<textarea id="modal-nota' +
              i +
              '" name="addProducto[' +
              i +
              '][nota]" class="form-control required nota" placeholder="Observaciones" rows="1" style="height: 50px;">' +
              clearHTMLTextArea(nota_final) +
              "</textarea>";
            table_enlace_producto += "</div>";
          }
          table_enlace_producto +=
            '<input type="hidden" id="modal-id_detalle' +
            i +
            '" name="addProducto[' +
            i +
            '][id_detalle]" class="form-control" value="' +
            id_item +
            '">';
          table_enlace_producto +=
            '<input type="hidden" id="modal-cantidad_oculta' +
            i +
            '" name="addProducto[' +
            i +
            '][cantidad_oculta]" class="form-control" value="' +
            detalle[i]["Qt_Producto_Caja"] +
            '">';
          table_enlace_producto +=
            '<input type="hidden" id="modal-precio_oculta' +
            i +
            '" name="addProducto[' +
            i +
            '][precio_oculta]" class="form-control" value="' +
            detalle[i]["Ss_Precio"] +
            '">';
          table_enlace_producto +=
            '<input type="hidden" id="modal-moq_oculta' +
            i +
            '" name="addProducto[' +
            i +
            '][moq_oculta]" class="form-control" value="' +
            detalle[i]["Qt_Producto_Moq"] +
            '">';
          table_enlace_producto +=
            '<input type="hidden" id="modal-caja_oculta' +
            i +
            '" name="addProducto[' +
            i +
            '][caja_oculta]" class="form-control" value="' +
            detalle[i]["Qt_Producto_Caja"] +
            '">';
          table_enlace_producto +=
            '<input type="hidden" id="modal-cbm_oculta' +
            i +
            '" name="addProducto[' +
            i +
            '][cbm_oculta]" class="form-control" value="' +
            detalle[i]["Qt_Cbm"] +
            '">';
          table_enlace_producto +=
            '<input type="hidden" id="modal-delivery_oculta' +
            i +
            '" name="addProducto[' +
            i +
            '][delivery_oculta]" class="form-control" value="' +
            detalle[i]["Nu_Dias_Delivery"] +
            '">';
          table_enlace_producto +=
            '<input type="hidden" id="modal-costo_delivery_oculta' +
            i +
            '" name="addProducto[' +
            i +
            '][costo_delivery_oculta]" class="form-control" value="' +
            detalle[i]["Ss_Costo_Delivery"] +
            '">';
          table_enlace_producto +=
            '<input type="hidden" id="modal-nota_historica_oculta' +
            i +
            '" name="addProducto[' +
            i +
            '][nota_historica_oculta]" class="form-control" value="' +
            detalle[i]["Txt_Nota"] +
            '">';
          table_enlace_producto += "</td></tr>";

          id_item_tmp = id_item;
        }
      }

      $("#table-elegir_productos_proveedor").append(table_enlace_producto);

      validateDecimal();
      validateNumber();
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

function documentoPagoGarantizado(id, sCorrelativo) {
  $('[name="documento_pago_garantizado-id_cabecera"]').val(id);
  $('[name="documento_pago_garantizado-correlativo"]').val(sCorrelativo);

  $("#modal-documento_pago_garantizado").modal("show");
  $("#form-documento_pago_garantizado")[0].reset();
}

function descargarDocumentoPagoGarantizado(id) {
  url =
    base_url +
    "AgenteCompra/PedidosGarantizados/descargarDocumentoPagoGarantizadov2/" +
    id;
  $.ajax({
    url: url,
    type: "GET",
    dataType: "JSON",
    success: function (response) {
      console.log(response);

      if (response.status == "success") {
        $(".modal-ver_pago_garantizado").modal("show");

        $(".img-pago_garantizado").attr("src", "");
        $(".img-pago_garantizado").attr("src", response.url_image);

        url =
          base_url +
          "AgenteCompra/PedidosGarantizados/descargarDocumentoPagoGarantizado/" +
          id;
        $("#a-download_image_pago_garantizado").attr("href", response.url_image);
        //$("#a-download_image_pago_garantizado").attr("data-id_pago", id);
      } else {
        alert(response.message);
      }
    },
  });

  /*
  url = base_url + 'AgenteCompra/PedidosGarantizados/descargarDocumentoPagoGarantizado/' + id;
  
  var popupwin = window.open(url);
  setTimeout(function() { popupwin.close();}, 2000);
  */
}

function clearHTMLTextArea(str) {
  str = str.replace(/<br>/gi, "");
  str = str.replace(/<br\s\/>/gi, "");
  str = str.replace(/<br\/>/gi, "");
  str = str.replace(/<\/button>/gi, "");
  str = str.replace(/<br >/gi, "");
  return str;
}

function addItemsPedido() {
  div_items = "";

  //visible para mostrar a publico
  div_items +=
    '<div id="card' + iCounter + '" class="card border-0 rounded shadow mt-3">';
  div_items += '<div class="row">';
  div_items +=
    '<div class="col-sm-4 position-relative text-center ps-4 pe-3 pe-sm-0">';
  div_items += '<div class="col-sm-12">';
  div_items +=
    '<h6 class="text-left card-title pt-3" style="text-align: left;">';
  div_items += '<label class="fw-bold">Imagen</label>';
  div_items += "</h6>";
  div_items += '<div class="form-group">';
  div_items +=
    '<label class="btn btn btn-outline-secondary" for="voucher' +
    iCounter +
    '" style="width: 100%;">';
  div_items +=
    '<input class="arrProducto form-control voucher" id="voucher' +
    iCounter +
    '" type="file" style="display:none" name="voucher[]" data-id="' +
    iCounter +
    '" onchange="loadFile(event, ' +
    iCounter +
    ')" placeholder="sin archivo" accept="image/*">Agregar foto';
  div_items += "</label>";
  div_items += '<span class="help-block text-danger" id="error"></span>';
  div_items += "</div>";
  div_items += "</div>";
  div_items +=
    '<img id="img_producto-preview' +
    iCounter +
    '" src="" class="arrProducto img-thumbnail border-0 rounded" alt="">'; //cart-size-img
  div_items += "</div>";

  div_items += '<div class="col-sm-8">';
  div_items += '<div class="card-body pb-0">';
  div_items += '<div class="row">';
  div_items += '<div class="col-sm-12 mb-3">';
  div_items += '<h6 class="card-title">';
  div_items += '<label class="fw-bold">Nombre Comercial</label>';
  div_items += "</h6>";
  div_items +=
    '<input type="text" inputmode="text" id="modal-nombre_comercial' +
    iCounter +
    '" name="addProducto[' +
    iCounter +
    '][nombre_comercial]" class="arrProducto form-control" placeholder="" maxlength="255" autocomplete="off">';
  div_items += "</div>";

  div_items += '<div class="col-sm-12 mb-0">';
  div_items += '<h6 class="card-title">';
  div_items += '<label class="fw-bold">Características</label>';
  div_items += "</h6>";
  div_items += '<div class="form-group">';
  div_items +=
    '<textarea class="arrProducto form-control required caracteristicas" placeholder="" id="modal-caracteristicas' +
    iCounter +
    '" name="addProducto[' +
    iCounter +
    '][caracteristicas]" style="height: 100px"></textarea>';
  div_items += '<span class="help-block text-danger" id="error"></span>';
  div_items += "</div>";
  div_items += "</div>";

  div_items += '<div class="col-12 col-sm-3 col-md-3 col-lg-2 mb-0">';
  div_items += '<h6 class="card-title">';
  div_items += '<label class="fw-bold">Cantidad</label>';
  div_items += "</h6>";
  div_items += '<div class="form-group">';
  div_items +=
    '<input type="text" id="modal-cantidad' +
    iCounter +
    '" inputmode="decimal" name="addProducto[' +
    iCounter +
    '][cantidad]" class="arrProducto form-control cantidad input-decimal" placeholder="" value="" autocomplete="off">';
  div_items += '<span class="help-block text-danger" id="error"></span>';
  div_items += "</div>";
  div_items += "</div>";

  div_items += '<div class="col-12 col-sm-9 col-md-9 col-lg-10 mb-0">';
  div_items += '<h6 class="card-title">';
  div_items += '<label class="fw-bold">Link</label>';
  div_items += "</h6>";
  div_items += '<div class="form-group">';
  div_items +=
    '<input type="text" inputmode="url" id="modal-link' +
    iCounter +
    '" name="addProducto[' +
    iCounter +
    '][link]" class="arrProducto form-control link" placeholder="" autocomplete="off" autocapitalize="none">';
  div_items += '<span class="help-block text-danger" id="error"></span>';
  div_items += "</div>";
  div_items += "</div>";
  div_items += "</div>";
  div_items += "</div>";
  div_items += "</div>";

  div_items += '<div class="col-sm-12 ps-4 mb-3 pe-4">';
  div_items += '<div class="d-grid gap">';
  div_items +=
    '<button type="button" id="btn-quitar_item_' +
    iCounter +
    '" class="btn btn-outline-danger btn-quitar_item col" data-id="' +
    iCounter +
    '">Quitar</button>';
  div_items += "</div>";
  div_items += "</div>";
  div_items += "</div>";
  div_items += "</div>";

  $("#div-arrItemsPedidos").append(div_items);

  validateNumberLetter();
  validateDecimal();

  ++iCounter;
}

function loadFile(event, id) {
  var output = document.getElementById("img_producto-preview" + id);
  output.src = URL.createObjectURL(event.target.files[0]);
  output.onload = function () {
    URL.revokeObjectURL(output.src); // free memory
  };

  window.mobileCheck = function () {
    let check = false;
    (function (a) {
      if (
        /(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(
          a
        ) ||
        /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(
          a.substr(0, 4)
        )
      )
        check = true;
    })(navigator.userAgent || navigator.vendor || window.opera);
    return check;
  };

  //if(iOS==true && window.mobileCheck()==true){
  if (window.mobileCheck() == true) {
    scrollToIOS($("html, body"), $("#modal-nombre_comercial" + id));
  }

  $("#modal-nombre_comercial" + id).focus();
  $("#modal-nombre_comercial" + id).select();
}

function iOS() {
  return (
    [
      "iPad Simulator",
      "iPhone Simulator",
      "iPod Simulator",
      "iPad",
      "iPhone",
      "iPod",
    ].includes(navigator.platform) ||
    // iPad on iOS 13 detection
    (navigator.userAgent.includes("Mac") && "ontouchend" in document)
  );
}

function scrollToErrorHTML($sMetodo, $IdElemento) {
  $sMetodo.animate(
    {
      scrollTop: $IdElemento.offset().top + 450,
    },
    "slow"
  );
}

function scrollToIOS($sMetodo, $IdElemento) {
  $sMetodo.animate(
    {
      scrollTop: $IdElemento.offset().top,
    },
    "slow"
  );
}

function viewChatItem(id_item) {
  div_chat_item = "";
  $("#card-chat_item").html("");

  url = base_url + "AgenteCompra/PedidosGarantizados/viewChatItem/" + id_item;
  $.ajax({
    url: url,
    type: "GET",
    dataType: "JSON",
    success: function (response) {
      //console.log(response);

      if (response.status == "success") {
        var result = response.result;
        div_chat_item += '<div class="direct-chat-messages">';
        var sClassL = "",
          sClassR = "",
          sClassLR = "",
          sMessage = "",
          sName = "";
        for (i = 0; i < result.length; i++) {
          if (
            result[i].Txt_Usuario_Remitente != "" &&
            result[i].Txt_Usuario_Remitente != null
          ) {
            sClassLR = "";
            sClassL = "left";
            sClassR = "right";
            sMessage = result[i].Txt_Usuario_Remitente;
            sName = result[i].No_Nombres_Apellidos_Remitente;
          } else {
            sClassLR = "right";
            sClassL = "right";
            sClassR = "left";
            sMessage = result[i].Txt_Usuario_Destino;
            sName = result[i].No_Nombres_Apellidos_Destinatario;
          }

          div_chat_item += '<div class="direct-chat-msg ' + sClassLR + '">';
          div_chat_item += '<div class="direct-chat-infos clearfix">';
          div_chat_item +=
            '<span class="direct-chat-name float-' +
            sClassL +
            '">' +
            sName +
            "</span>";
          div_chat_item +=
            '<span class="direct-chat-timestamp float-' +
            sClassR +
            '">' +
            ParseDateString(result[i].Fe_Registro, "fecha_hora_bd", " ") +
            "</span>";
          div_chat_item += "</div>";
          div_chat_item +=
            '<img class="direct-chat-img" src="' +
            base_url +
            'dist_v2/img/user_all.png?ver=1.0" alt="a">';
          div_chat_item += '<div class="direct-chat-text">';
          div_chat_item += sMessage;
          div_chat_item += "</div><br>";
          div_chat_item += "</div>";
        }
        div_chat_item += "</div>";

        /*
        div_chat_item += '<div class="direct-chat-messages">';
          div_chat_item += '<div class="direct-chat-msg">';
            for (i = 0; i < result.length; i++) {
              if(result[i].Txt_Usuario_Remitente!='' && result[i].Txt_Usuario_Remitente!=null){
                div_chat_item += '<div class="direct-chat-infos clearfix">';
                  div_chat_item += '<span class="direct-chat-name float-left">' + result[i].No_Nombres_Apellidos_Remitente + '</span>';
                  div_chat_item += '<span class="direct-chat-timestamp float-right">' + ParseDateString(result[i].Fe_Registro, 'fecha_hora_bd', ' ') + '</span>';
                div_chat_item += '</div>';
                div_chat_item += '<img class="direct-chat-img" src="' + base_url + 'dist_v2/img/user_all.png?ver=1.0" alt="a">';
                div_chat_item += '<div class="direct-chat-text">';
                  div_chat_item += result[i].Txt_Usuario_Remitente;
                div_chat_item += '</div><br>';
              }
            }
          div_chat_item += '</div>';

          div_chat_item += '<div class="direct-chat-msg right">';
            for (i = 0; i < result.length; i++) {
              if(result[i].Txt_Usuario_Destino!='' && result[i].Txt_Usuario_Destino!=null){
                div_chat_item += '<div class="direct-chat-infos clearfix">';
                  div_chat_item += '<span class="direct-chat-name float-right">' + result[i].No_Nombres_Apellidos_Destinatario + '</span>';
                  div_chat_item += '<span class="direct-chat-timestamp float-left">' + ParseDateString(result[i].Fe_Registro, 'fecha_hora_bd', ' ') + '</span>';
                div_chat_item += '</div>';
                div_chat_item += '<img class="direct-chat-img" src="' + base_url + 'dist_v2/img/user_all.png?ver=1.0" alt="b">';
                div_chat_item += '<div class="direct-chat-text">';
                  div_chat_item += result[i].Txt_Usuario_Destino;
                div_chat_item += '</div><br>';
              }
            }
          div_chat_item += '</div>';
        div_chat_item += '</div>';
        */

        $("#card-chat_item").append(div_chat_item);
      }
    },
  });
}

//chat de novedades de producto
function asignarPedido(ID_Pedido_Cabecera, Nu_Estado) {
  // if(Nu_Estado!=3) {//3 - Enviado
  //   $('#moda-message-content').removeClass('bg-danger bg-warning bg-success');
  //   $('#modal-message').modal('show');

  //   $('#moda-message-content').addClass( 'bg-warning');
  //   $('.modal-title-message').html('Primero el estado debe ser <strong>ENVIADO</strong> para asignar.');

  //   setTimeout(function () { $('#modal-message').modal('hide'); }, 3100);
  // } else {
  $("#txt-guardar_personal_china-ID_Pedido_Cabecera").val(ID_Pedido_Cabecera);
  $(".modal-guardar_personal_china").modal("show");

  $("#cbo-guardar_personal_china-ID_Usuario").html(
    '<option value="0" selected="selected">Buscando...</option>'
  );
  url = base_url + "HelperImportacionController/getUsuarioChina";
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
}
//}

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
        "AgenteCompra/PedidosGarantizados/removerAsignarPedido/" +
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

function cambiarEstadoImpotacionIntegral(ID, Nu_Estado, sCorrelativo) {
  var $modal_delete = $("#modal-message-delete");
  $modal_delete.modal("show");

  $(".modal-message-delete").removeClass(
    "modal-danger modal-warning modal-success"
  );
  $(".modal-message-delete").addClass("modal-success");

  var sNombreEstado = "quitar";
  if (Nu_Estado == 1) sNombreEstado = "agregar";

  $("#modal-title").html(
    "¿Deseas <strong>" + sNombreEstado + "</strong> importación integral?"
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
        "AgenteCompra/PedidosGarantizados/cambiarEstadoImpotacionIntegral/" +
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
