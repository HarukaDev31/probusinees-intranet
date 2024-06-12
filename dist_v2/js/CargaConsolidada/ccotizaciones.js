var url;
const { jsPDF } = window.jspdf;
$(function () {
  $("#button-save").hide();
  $("#loading-spinner").hide();

  url = base_url + "CargaConsolidada/CCotizaciones/ajax_list";
  table_Entidad = $("#table-CCotizaciones").DataTable({
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
    order: [
      [1, "desc"],
      [10, "asc"],
    ],
    ajax: {
      url: url,
      type: "POST",
      dataType: "json",
      data: function (data) {
        console.log(data);
      },
    },
    columnDefs: [
      {
        targets: "no-hidden",
        orderable: false,
      },
      {
        targets: [1, 10], // Target the 10th (index 9) column
        visible: false, // Hide this column
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
});
function verCotizacion(ID) {
  CotizacionID = ID;
  $(".div-Listar").hide();
  $(".div-AgregarEditar").show();
  $("#button-save").show();
  urlCabecera =
    base_url + "CargaConsolidada/CCotizaciones/ajax_edit_header/" + ID;
  $.ajax({
    url: urlCabecera,
    type: "GET",
    dataType: "JSON",
    success: function (response) {
      //response have names,cbm_total, peso_total y empresa
      $("#Nombre").val(response[0].Nombre);
      $("#CBM_Total").val(response[0].CBM_Total);
      $("#Peso_Total").val(response[0].Peso_Total);
      $("#Empresa").val(response[0].Empresa);
      $("#ID_Cotizacion").val(ID);
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.log(jqXHR);
    },
  });

  urlBody = base_url + "CargaConsolidada/CCotizaciones/ajax_edit_body/" + ID;
  $.ajax({
    url: urlBody,
    type: "GET",
    dataType: "JSON",
    success: function (response) {
      const estado = $("#selectEstadoBody");
      const estadoCliente = response[0].ID_Tipo_Cliente;
      estado.val(estadoCliente);
      for (var i = 0; i < response.length; i++) {
        $("#div-CotizacionBody").append(
          getProvTemplate(i, response[i].ID_Proveedor)
        );
        $("#CBM_Total-" + i).val(response[i].CBM_Total);
        $("#Peso_Total-" + i).val(response[i].Peso_Total);
        //ADD CLICK LISTENER TO BUTTONS
        $(`#Archivos-${i}`).attr(
          "onclick",
          `downloadFile('${response[i].URL_Proforma}','${response[i].URL_Packing}')`
        );
        // $(`#Proforma-${i}`).attr("onclick",`downloadFile('${response[i].URL_Proforma}')`);
        // $(`#Packing-${i}`).attr("onclick",`downloadFile('${response[i].URL_Packing}')`);
        productosJSON = JSON.parse(response[i].productos);
        product = 0;

        for (const key in productosJSON) {
          var productoID = productosJSON[key].ID_Producto;
          //if tributos_pendiente int value is >0 then set the tributos button to red

          $(".proveedor-" + i + "-productos").append(
            getProductoTemplate(i, product, productoID)
          );
          $(`#URL_Link-${i}-${product}`).val(productosJSON[key].URL_Link);
          //add URL_redirect click listener to redirect to the URL and onhover change the cursor to pointer and svg color to blue
          if (
            productosJSON[key].URL_Link != "" ||
            productosJSON[key].URL_Link != null ||
            productosJSON[key].URL_Link != "null"
          ) {
            $(`#URL_redirect-${i}-${product}`).attr(
              "onclick",
              `window.open('${productosJSON[key].URL_Link}')`
            );
            $(`#URL_redirect-${i}-${product}`).hover(
              function () {
                $(this).css("cursor", "pointer");
                $(this).css("fill", "blue");
              },
              function () {
                $(this).css("fill", "black");
              }
            );
          }
          $(`#Nombre_Comercial-${i}-${product}`).val(
            productosJSON[key].Nombre_Comercial
          );
          $(`#Uso-${i}-${product}`).val(productosJSON[key].Uso);
          $(`#Cantidad-${i}-${product}`).val(productosJSON[key].Cantidad);
          $(`#Valor_Unitario-${i}-${product}`).val(
            productosJSON[key].Valor_unitario
          );
          $(`#img-${i}-${product}`).html(
            `<img src="${productosJSON[key].Url_Image}" class="img-fluid" alt="Responsive image">`
          );
          const tributos_pendiente = productosJSON[key].Tributos_Pendientes;
          console.log(tributos_pendiente);
          if (tributos_pendiente > 0) {
            console.log("Tributos Pendientes", tributos_pendiente, i, product);
            const button = $(`#button-tributo-${i}-${product}`);
            //ad circle to button with the number of tributos pendientes
            button.html(`<span>Revisar</span>`);
            //remove btn-primary class add btn-outline-secondary
            button.removeClass("btn-outline-secondary");
            button.addClass("btn-primary");
          }
          //add attributes data to button
          const button = $(`#button-tributo-${i}-${product}`);
          button.attr("data-nombre", productosJSON[key].Nombre_Comercial);
          button.attr("data-proveedorIndex", i);
          product++;
        }
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.log(jqXHR);
    },
  });
}
const downloadFile = (urlProforma, urlPacking) => {
  // Función para crear y activar un enlace de descarga
  const createAndClickDownloadLink = (url) => {
    if (!url) {
      console.error("No URL provided for download");
      return;
    }
    if (typeof url !== "string") {
      console.error("Invalid URL provided for download");
      return;
    }
    if (url.length === 0) {
      console.error("Empty URL provided for download");
      return;
    }
    if (url == "null") {
      console.error("Empty URL provided for download");
      return;
    }
    // Crea un enlace de descarga
    const link = document.createElement("a");
    link.href = url;
    link.target = "_blank";
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  };
  try {
    // Genera nombres de archivo para las descargas

    // Crea y activa enlaces de descarga
    createAndClickDownloadLink(urlProforma);
    createAndClickDownloadLink(urlPacking);
  } catch (error) {
    console.error("Error downloading files:", error);
  }
};

// Llamada a la función con las URLs de ejemplo

function getProvTemplate(index, ID_Proveedor = null) {
  template = `<div class="col-12 proveedor-${index}" > 
        <div class="row">
            <div class="col-12 col-md-6 d-flex flex-column justify-content-center">
                <div class="row">
                    <div class="col-12 col-md-5 d-flex flex-column justify-content-center">
                    <h4 class="font-weight-bold">PROVEEDOR ${index + 1}</h4>
                    </div>
                    <div class="col-12 col-md-7 d-flex align-items-center w-100 justify-content-center">
                    <div class="form-group d-flex flex-row align-items-center align-items-center w-100">
                    <button type="button" class="btn btn-outline-danger w-75 mx-1" onclick="borrarProveedor(${ID_Proveedor},${index})">Eliminar</button>
                    <button type="button" class="btn btn btn-outline-danger w-75 mx-1" onclick="agregarProducto(${ID_Proveedor},${index})">Agregar Producto</button>
                    </div>
                    </div>
                        
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="row d-flex proveedor flex-row">
                    <input class="proveedorID" value="${
                      ID_Proveedor ? ID_Proveedor : -1
                    }" type="hidden" >
                    <div class="col-12 col-md-3">
                        <div class="form-group ">
                        <label>CBM Total</label>
                        <input id="CBM_Total-${index}"  type="number"  class="form-control required" placeholder="Ingresar" maxlength="100" autocomplete="off">
                        <span class="help-block text-danger" id="error"></span>
                    </div>
                </div>
            
                <div class="col-12 col-md-3">
                    <div class="form-group">
                        <label>Peso Total</label>
                        <input  id="Peso_Total-${index}" type="text"  class="form-control required" placeholder="Ingresar" maxlength="100" autocomplete="off">
                        <span class="help-block text-danger" id="error"></span>
                    </div>   
                </div>
               

            </div>
        </div>
        </div>
        <div class="col-12 proveedor-${index}-productos">
            <div class="row rounded-5 mb-2 border border-secondary">
                <div class="col-12 col-md-6 d-flex justify-content-center">
                    <Label>Producto</Label>
                </div>
                <div class="col-12 col-md-4 d-flex justify-content-center">
                    <Label>Informacion del Producto</Label>
                </div>
                <div class="col-12 col-md-2 d-flex justify-content-center">
                    <Label>Tributos</Label>
                </div>
            </div>
        </div>
    </div>
    <div style="height: 1px; width:100%;background-color: #000000;" class="my-4"></div>`;
  return template;
}
function getProductoTemplate(proveedor, index, productoID) {
  template = `
    <div class="col-12 ">
        <div class="row producto-${proveedor} proveedor-${proveedor}">
        <input class="productID" value="${productoID}" type="hidden" >

            <div class="col-12 col-md-6 ">
            <Label id="img-${proveedor}-${index}"></Label>
            <div class="form-group d-flex flex-row align-items-center">
                    <input id="URL_Link-${proveedor}-${index}"   class="form-control required URL_Link mr-1" placeholder="Ingresar" maxlength="100" autocomplete="off">
                    <svg id="URL_redirect-${proveedor}-${index}"xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-up-right" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M8.636 3.5a.5.5 0 0 0-.5-.5H1.5A1.5 1.5 0 0 0 0 4.5v10A1.5 1.5 0 0 0 1.5 16h10a1.5 1.5 0 0 0 1.5-1.5V7.864a.5.5 0 0 0-1 0V14.5a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h6.636a.5.5 0 0 0 .5-.5"/>
                    <path fill-rule="evenodd" d="M16 .5a.5.5 0 0 0-.5-.5h-5a.5.5 0 0 0 0 1h3.793L6.146 9.146a.5.5 0 1 0 .708.708L15 1.707V5.5a.5.5 0 0 0 1 0z"/>
                  </svg>                    <span class="help-block text-danger" id="error"></span>
            </div>
            <button type="button" class="btn btn-outline-danger w-100" style="height:50px;" onclick="borrarProducto(${proveedor},${index},${
    productoID ? productoID : -1
  })">Quitar</button>
        </div>
        <div class="col-12 col-md-4">
        <div class="form-group">
                <label>Nombre Comercial</label>
                <input id="Nombre_Comercial-${proveedor}-${index}"  type="text"  class="form-control required Nombre_Comercial" placeholder="Ingresar" maxlength="100" autocomplete="off">
                <span class="help-block text-danger" id="error"></span>
        </div>
        <div class="form-group">
                <label>Uso</label>
                <input id="Uso-${proveedor}-${index}"  type="text"  class="form-control required Uso" placeholder="Ingresar" maxlength="100" autocomplete="off">
                <span class="help-block text-danger" id="error"></span>
        </div>
        <div class="form-group">
                <label>Cantidad</label>
                <input id="Cantidad-${proveedor}-${index}"  type="number"  class="form-control required Cantidad" placeholder="Ingresar" maxlength="100" autocomplete="off">
                <span class="help-block text-danger" id="error"></span>
        </div>
        <div class="form-group">
                <label>Valor Unitario</label>
                <input id="Valor_Unitario-${proveedor}-${index}" type="number"  class="form-control required Valor_Unitario" placeholder="Ingresar" maxlength="100" autocomplete="off">
                <span class="help-block text-danger" id="error"></span>
        </div>
        </div>
        <div class="col-12 col-md-2 d-flex justify-content-center align-items-center">
        <button id="button-tributo-${proveedor}-${index}" type="button" class="btn btn-outline-secondary w-100" style="height:50px;" data-toggle="modal" data-target="#exampleModal" data-productid="${productoID}" data-button="button-tributo-${proveedor}-${index}">Ver</button>
        </div>
    </div>
    </div>`;
  return template;
}
var productoID = null;
var newProveedores = [];
var newProveedor = null;
var CotizacionID = null;
var newProducto = null;
var deletedProveedores = [];
var deletedProductos = [];
var newProductos = [];
const borrarProveedor = (ID_Proveedor, index) => {
  if (ID_Proveedor == null) {
    newProveedores.splice(index, 1);
    $(`.proveedor-${index}`).remove();
  } else {
    deletedProveedores.push(ID_Proveedor);
    $(`.proveedor-${index}`).remove();
  }
};
const borrarProducto = (provedorIndex, index, ID_Producto) => {
  const productos = $(`.producto-${provedorIndex}.proveedor-${provedorIndex}`);
  //if products is only one alert to the user and tell him to delete the provider
  if (productos.length == 1) {
    alert(
      "No se puede eliminar el unico producto de un proveedor, elimine el proveedor"
    );
    return;
  }
  const producto = productos[index];
  if (ID_Producto == null) {
    newProductos.splice(index, 1);
    producto.remove();
  } else {
    deletedProductos.push(ID_Producto);
    producto.remove();
  }
  console.log(deletedProductos);
};
const agregarProducto = (ID_Proveedor, index) => {
  //productIndex if length of divs with both class producto-index and proveedor-index
  const productoIndex = $(`.producto-${index}.proveedor-${index}`).length;

  console.log(
    "Agregar Producto",
    "Proveedor",
    index,
    "Producto",
    productoIndex
  );
  $(`.proveedor-${index}-productos`).append(
    getProductoTemplate(index, productoIndex, -1)
  );
  const button = $(`#button-tributo-${index}-${productoIndex}`);
  button.attr("data-nombre", "nuevo");
  button.attr("data-proveedorIndex", index);
  button.attr("data-productid", productoIndex);
  button.attr("data-newProveedor", false);
  button.attr("data-productoIndex", productoIndex);

  newProductos.push({
    Prooveedor_Index: index,
    ID_Producto_temp: productoIndex,
    ID_Proveedor: ID_Proveedor,
    ID_Producto: -1,
    URL_Link: "",
    Nombre_Comercial: "",
    Uso: "",
    Cantidad: 0,
    Valor_Unitario: 0,
    created_for_new: false,
    tributos: {
      "ad-valorem": 0,
      igv: 16,
      ipm: 2,
      percepcion: 3.5,
      valoracion: 0,
      antidumping: 0,
    },
  });
  //add new product to proveedor
};

const guardarTributos = (button) => {
  if (newProveedor != null) {
    newProveedores[newProveedor].productos[productoID].tributos = {
      "ad-valorem": $("#ad-valorem").val(),
      igv: $("#igv").val(),
      ipm: $("#ipm").val(),
      percepcion: $("#percepcion").val(),
      valoracion: $("#valoracion").val(),
      antidumping: $("#antidumping").val(),
    };
    newProveedor = null;
    return;
  }
  if (newProducto != null) {
    newProductos[newProducto].tributos = {
      "ad-valorem": $("#ad-valorem").val(),
      igv: $("#igv").val(),
      ipm: $("#ipm").val(),
      percepcion: $("#percepcion").val(),
      valoracion: $("#valoracion").val(),
      antidumping: $("#antidumping").val(),
    };
    newProducto = null;
    return;
  }
  const tributos = {
    ID_Producto: productoID,
    "ad-valorem": $("#ad-valorem").val(),
    igv: $("#igv").val(),
    ipm: $("#ipm").val(),
    percepcion: $("#percepcion").val(),
    valoracion: $("#valoracion").val(),
    antidumping: $("#antidumping").val(),
  };

  url = base_url + "CargaConsolidada/CCotizaciones/guardarTributos";
  $.ajax({
    url: url,
    type: "post",
    dataType: "JSON",
    contentType: "application/json; charset=utf-8",
    traditional: true,
    data: JSON.stringify(tributos),
    success: function (response) {
      $("#exampleModal").modal("hide");
      currentButton.html(`<span>Ver</span>`);
      currentButton.removeClass("btn-primary");
      currentButton.addClass("btn-outline-secondary");
      currrentButton = null;
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.log(jqXHR);
    },
  });
};
let currentButton = null;

$("#modal-upload-excel").on("show.bs.modal", function (event) {
  url = base_url + "CargaConsolidada/CCotizaciones/getTarifas";

  $.ajax({
    url: url,
    type: "GET",
    dataType: "JSON",
    success: function (response) {
      let indexNuevo = 1;
      let indexAntiguo = 1;
      let indexSocio = 1;
      response.forEach((element) => {
        if (element.ID_Tipo_Cliente == 1) {
          $(`#tarifa-antiguo-${indexAntiguo}`).val(element.tarifa);
          indexAntiguo++;
        } else if (element.ID_Tipo_Cliente == 2) {
          $(`#tarifa-nuevo-${indexNuevo}`).val(element.tarifa);
          indexNuevo++;
        } else if (element.ID_Tipo_Cliente == 3) {
          const tarifaSocio = $(`#tarifa-socio-${indexSocio}`);
          tarifaSocio.val(element.tarifa);
          const tarifasSocio = $(`.tarifa-socio-fake-${indexSocio}`);

          //add event listener to change the value of fake inputs
          tarifaSocio.on("input change", (event) => {
            tarifasSocio.each((index, element) => {
              $(element).val($(event.target).val());
            });
          });

          //add event listener on change and all tarifa-socio-fake-indexSocio to change to the same value
          if (tarifasSocio.length > 0) {
            tarifasSocio.each((index, element) => {
              $(element).val($(`#tarifa-socio-${indexSocio}`).val());
            });
          }
          indexSocio++;
        }
      });
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.log(jqXHR);
    },
  });
});
$("#exampleModal").on("show.bs.modal", function (event) {
  var button = $(event.relatedTarget);
  currentButton = button;
  // Button that triggered the modal
  productoID = button.data("productid");
  const nombre = button.data("nombre");
  const proveedorIndex = button.data("proveedorindex");
  const newProv = button.data("newproveedor");
  const productoIndex = button.data("productoindex");

  $("#exampleModalLabel").text(
    "Tributos del producto " + nombre + " del Proveedor " + (proveedorIndex + 1)
  );

  if (newProv) {
    newProveedor = proveedorIndex;
    console.log("Modal Nuevo Proveedor", newProveedor);
  } else {
    newProveedor = null;
  }
  if (productoIndex) {
    //find Index of producto in newProductos
    newProducto = newProductos.findIndex(
      (producto) =>
        producto.ID_Producto_temp == productoIndex &&
        producto.Prooveedor_Index == proveedorIndex
    );
    console.log("Modal Nuevo Producto", productoIndex, proveedorIndex);
  } else {
    newProducto = null;
  }
  if (newProveedor != null) {
    //use the proveedor with the index equal to proveedorIndex from newProveedores
    console.log("newProveedor", newProveedores[proveedorIndex]);
    const proveedor = newProveedores[proveedorIndex];
    $("#ad-valorem").val(
      proveedor.productos[productoID].tributos["ad-valorem"]
    );
    $("#igv").val(proveedor.productos[productoID].tributos["igv"]);
    $("#ipm").val(proveedor.productos[productoID].tributos["ipm"]);
    $("#percepcion").val(
      proveedor.productos[productoID].tributos["percepcion"]
    );
    $("#valoracion").val(
      proveedor.productos[productoID].tributos["valoracion"]
    );
    $("#antidumping").val(
      proveedor.productos[productoID].tributos["antidumping"]
    );
    return;
  }
  if (productoIndex != null) {
    console.log("newProducto", newProductos, productoIndex);
    //use the producto with the index equal to productoIndex from newProductos
    const newProducto = newProductos.find(
      (producto) =>
        producto.ID_Producto_temp == productoIndex &&
        producto.Prooveedor_Index == proveedorIndex
    );
    $("#ad-valorem").val(newProducto.tributos["ad-valorem"]);
    $("#igv").val(newProducto.tributos["igv"]);
    $("#ipm").val(newProducto.tributos["ipm"]);
    $("#percepcion").val(newProducto.tributos["percepcion"]);
    $("#valoracion").val(newProducto.tributos["valoracion"]);
    $("#antidumping").val(newProducto.tributos["antidumping"]);
    return;
  }

  url =
    base_url +
    "CargaConsolidada/CCotizaciones/ajax_edit_tributos/" +
    productoID;

  $.ajax({
    url: url,
    type: "GET",
    dataType: "JSON",
    data: { ID_Producto: productoID },
    success: function (response) {
      $("#ad-valorem").val(response["ad-valorem"] ?? 0);
      $("#igv").val(response.igv ?? 16);
      $("#ipm").val(response.ipm ?? 2);
      $("#percepcion").val(response.percepcion ?? "3.50");
      $("#valoracion").val(response.valoracion ?? 0);
      $("#antidumping").val(response.antidumping ?? 0);
      newProveedor = null;
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.log(jqXHR);
      newProveedor = null;
    },
  });
});
const guardarCotizacion = () => {
  const cotizacion = [];

  const proveedores = $(".proveedor");
  for (let i = 0; i < proveedores.length; i++) {
    var CBM_Total = $(proveedores[i])
      .find("#CBM_Total-" + i)
      .val();
    var Peso_Total = $(proveedores[i])
      .find("#Peso_Total-" + i)
      .val();
    console.log(deletedProductos, deletedProveedores);
    cotizacion.push({
      ID_Proveedor: $(proveedores[i]).find(".proveedorID").val(),
      ID_Cotizacion: CotizacionID,
      CBM_Total: CBM_Total,
      Peso_Total: Peso_Total,
      productos: [],
      deletedProveedores: deletedProveedores,
      newProductos: [],
      deletedProductos: deletedProductos,
    });

    const productosHTML = $(`.producto-${i}`);
    for (let j = 0; j < productosHTML.length; j++) {
      var URL_Link = $(productosHTML[j]).find(`#URL_Link-${i}-${j}`).val();
      var Nombre_Comercial = $(productosHTML[j])
        .find(`#Nombre_Comercial-${i}-${j}`)
        .val();
      var Uso = $(productosHTML[j]).find(`#Uso-${i}-${j}`).val();
      var Cantidad = $(productosHTML[j]).find(`#Cantidad-${i}-${j}`).val();
      var Valor_Unitario = $(productosHTML[j])
        .find(`#Valor_Unitario-${i}-${j}`)
        .val();
      var ID_Producto = $(productosHTML[j]).find(".productID").val();

      cotizacion[i].productos.push({
        ID_Producto: ID_Producto,
        URL_Link: URL_Link,
        Nombre_Comercial: Nombre_Comercial,
        Uso: Uso,
        Cantidad: Cantidad,
        Valor_Unitario: Valor_Unitario,
        created_for_new: false,
      });
    }
  }
  for (let i = 0; i < newProductos.length; i++) {
    const searchString = `.producto-${newProductos[i].Prooveedor_Index}.proveedor-${newProductos[i].Prooveedor_Index}`;
    const producto = $(`${searchString}`)[newProductos[i].ID_Producto_temp];
    var URL_Link = $(producto)
      .find(
        `#URL_Link-${newProductos[i].Prooveedor_Index}-${newProductos[i].ID_Producto_temp}`
      )
      .val();
    var Nombre_Comercial = $(producto)
      .find(
        `#Nombre_Comercial-${newProductos[i].Prooveedor_Index}-${newProductos[i].ID_Producto_temp}`
      )
      .val();
    var Uso = $(producto)
      .find(
        `#Uso-${newProductos[i].Prooveedor_Index}-${newProductos[i].ID_Producto_temp}`
      )
      .val();
    var Cantidad = $(producto)
      .find(
        `#Cantidad-${newProductos[i].Prooveedor_Index}-${newProductos[i].ID_Producto_temp}`
      )
      .val();
    var Valor_Unitario = $(producto)
      .find(
        `#Valor_Unitario-${newProductos[i].Prooveedor_Index}-${newProductos[i].ID_Producto_temp}`
      )
      .val();
    cotizacion[newProductos[i].Prooveedor_Index].newProductos.push({
      ID_Producto: newProductos[i].ID_Producto,
      URL_Link: URL_Link,
      Nombre_Comercial: Nombre_Comercial,
      Uso: Uso,
      Cantidad: Cantidad,
      Valor_Unitario: Valor_Unitario,
      tributos: newProductos[i].tributos,
      created_for_new: newProductos[i].created_for_new,
    });
  }
  for (let j = 0; j < newProveedores.length; j++) {
    for (let k = 0; k < newProveedores[j].productos.length; k++) {
      console.log(newProveedores[j].productos[k].tributos);
      if (newProveedores[j].productos[k].ID_Producto == -1) {
        console.log(
          cotizacion[Math.abs(cotizacion.length - newProveedores.length)],
          "new to add"
        );
        cotizacion[
          Math.abs(cotizacion.length - newProveedores.length) + j
        ].productos[k].tributos = newProveedores[j].productos[k].tributos;
      }
    }
  }
  //add preloader
  url = base_url + "CargaConsolidada/CCotizaciones/guardarCotizacion";
  $.ajax({
    url: url,
    type: "post",
    dataType: "JSON",
    contentType: "application/json; charset=utf-8",
    traditional: true,
    data: JSON.stringify(cotizacion),
    success: function (response) {
      //remove preloader
      $(".preloader").remove();
      $(".div-Listar").show();
      $(".div-CotizacionBody").html("");
      $(".div-AgregarEditar").hide();
      table_Entidad.ajax.reload();
      newProveedor = null;
      newProducto = null;
      newProductos = [];
      newProveedores = [];
      deletedProveedores = [];
      deletedProductos = [];
      $("#button-save").hide();
      //set button revisar to ver
    },
    error: function (jqXHR, textStatus, errorThrown) {
      $(".preloader").remove();
      newProveedor = null;
      newProducto = null;
      newProductos = [];
      newProveedores = [];
      deletedProveedores = [];
      deletedProductos = [];
      $("#button-save").hide();
    },
  });
};
const getData = () => {
  const fileInput = $("#file-upload-excel")[0];

  // Check if a file is selected
  if (fileInput.files.length === 0) {
    alert("Please select a file before uploading.");
    return;
  }

  const file = fileInput.files[0];

  // Create form data
  const formData = new FormData();
  formData.append("file", file);
  //get tarifas

  //send file to server
  $("#modal-upload-excel").modal("hide");

  $("#loading-spinner").show();
  $.ajax({
    url: base_url + "CargaConsolidada/CCotizaciones/getExcelData",
    xhrFields: {
      responseType: "blob",
    },
    contentType: false, // Prevent jQuery from setting content type
    processData: false,
    data: formData,
    type: "POST",
    success: function (response) {
      console.log(response);
    },
    error: function (errorThrown) {
      console.error("Error al descargar el archivo Excel: " + errorThrown);
    },
  });
};
const uploadExcel = () => {
  const fileInput = $("#file-upload-excel")[0];

  // Check if a file is selected
  if (fileInput.files.length === 0) {
    alert("Please select a file before uploading.");
    return;
  }

  const file = fileInput.files[0];

  // Create form data
  const formData = new FormData();
  formData.append("file", file);
  const tarifas = [];
  const tarifasAntiguo = [];
  const tarifasNuevo = [];
  const tarifasSocio = [];
  const tarifasAntiguoInputs = $(".tarifa-antiguo");
  const tarifasNuevoInputs = $(".tarifa-nuevo");
  const tarifasSocioInputs = $(".tarifa-socio");
  let limitesAntiguo = [
    {
      inf: 0,
      sup: 0.59,
      id_tipo_tarifa: 1,
    },
    {
      inf: 0.6,
      sup: 1.09,
      id_tipo_tarifa: 1,
    },

    {
      inf: 1.1,
      sup: 2.09,
      id_tipo_tarifa: 2,
    },
    {
      inf: 2.1,
      sup: 3.09,
      id_tipo_tarifa: 2,
    },
    {
      inf: 3.1,
      sup: 4.09,
      id_tipo_tarifa: 2,
    },
    {
      inf: 4.1,
      sup: 999999,
      id_tipo_tarifa: 2,
    },
  ];
  let limitesNuevo = [
    {
      inf: 0,
      sup: 0.59,
      id_tipo_tarifa: 1,
    },
    {
      inf: 0.6,
      sup: 1.09,
      id_tipo_tarifa: 1,
    },

    {
      inf: 1.1,
      sup: 2.09,
      id_tipo_tarifa: 2,
    },
    {
      inf: 2.1,
      sup: 3.09,
      id_tipo_tarifa: 2,
    },
    {
      inf: 3.1,
      sup: 4.09,
      id_tipo_tarifa: 2,
    },
    {
      inf: 4.1,
      sup: 999999,
      id_tipo_tarifa: 2,
    },
  ];
  let limitesSocio = [
    {
      inf: 0,
      sup: 1.09,
      id_tipo_tarifa: 1,
    },
    {
      inf: 1.1,
      sup: 9999999,
      id_tipo_tarifa: 2,
    },
  ];
  let hasError = false;
  for (let i = 0; i < tarifasAntiguoInputs.length; i++) {
    //if not a number and not more than 0
    if (
      isNaN($(tarifasAntiguoInputs[i]).val()) ||
      $(tarifasAntiguoInputs[i]).val() < 0
    ) {
      hasError = true;
      const inputError = $(`#error-tarifa-antiguo-${i + 1}`);
      inputError.removeClass("d-none");
    } else {
      const inputError = $(`#error-tarifa-antiguo-${i + 1}`);
      inputError.addClass("d-none");
    }
    if (
      isNaN($(tarifasNuevoInputs[i]).val()) ||
      $(tarifasNuevoInputs[i]).val() < 0
    ) {
      hasError = true;
      const inputError = $(`#error-tarifa-nuevo-${i + 1}`);
      inputError.removeClass("d-none");
    } else {
      const inputError = $(`#error-tarifa-nuevo-${i + 1}`);
      inputError.addClass("d-none");
    }
    if (
      tarifasSocioInputs.length > i &&
      (isNaN($(tarifasSocioInputs[i]).val()) ||
        $(tarifasSocioInputs[i]).val() < 0)
    ) {
      console.log("Error");
      hasError = true;
      const inputError = $(`#error-tarifa-socio-${i + 1}`);
      inputError.removeClass("d-none");
    } else {
      const inputError = $(`#error-tarifa-socio-${i + 1}`);
      inputError.addClass("d-none");
    }

    tarifasAntiguo.push({
      id_tipo_cliente: 2,
      tarifa: $(tarifasAntiguoInputs[i]).val(),
      limite_inf: limitesAntiguo[i].inf,
      limite_sup: limitesAntiguo[i].sup,
      id_tipo_tarifa: limitesAntiguo[i].id_tipo_tarifa,
    });
    tarifasNuevo.push({
      id_tipo_cliente: 1,
      tarifa: $(tarifasNuevoInputs[i]).val(),
      limite_inf: limitesNuevo[i].inf,
      limite_sup: limitesNuevo[i].sup,
      id_tipo_tarifa: limitesNuevo[i].id_tipo_tarifa,
    });
    if (tarifasSocioInputs.length > i) {
      tarifasSocio.push({
        id_tipo_cliente: 3,
        tarifa: $(tarifasSocioInputs[i]).val(),
        limite_inf: limitesSocio[i].inf,
        limite_sup: limitesSocio[i].sup,
        id_tipo_tarifa: limitesSocio[i].id_tipo_tarifa,
      });
    }
  }
  if (hasError) {
    return;
  }
  tarifas.push(...tarifasAntiguo);
  tarifas.push(...tarifasNuevo);
  tarifas.push(...tarifasSocio);

  formData.append("tarifas", JSON.stringify(tarifas));
  //send file to server
  $("#modal-upload-excel").modal("hide");

  $("#loading-spinner").show();

  $.ajax({
    url: base_url + "CargaConsolidada/CCotizaciones/uploadExcelMassive",
    xhrFields: {
      responseType: "blob",
    },
    contentType: false, // Prevent jQuery from setting content type
    processData: false,
    data: formData,
    type: "POST",
    success: function (response) {
      console.log(response);
      //blob as zip file
      const blob = new Blob([response], {
        type: "application/zip",
      });
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement("a");
      a.href = url;
      a.download = "plantilla_cotizaciones.zip";
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
      $("#loading-spinner").hide();
      //hide modal-upload-excel
    },
    error: function (errorThrown) {
      console.error("Error al descargar el archivo Excel: " + errorThrown);
      $("#loading-spinner").hide();
    },
  });
};
const descargarReporte = (ID_Cotizacion, C_Cotizacion) => {
  $.ajax({
    url: base_url + "CargaConsolidada/CCotizaciones/descargarExcel",
    type: "POST",
    xhrFields: {
      responseType: "blob",
    },
    data: JSON.stringify({
      ID_Cotizacion: ID_Cotizacion,
      C_Cotizacion: C_Cotizacion,
    }),
    success: function (response) {
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
      link.download =
        "Cotizacion_" + C_Cotizacion + "_" + formattedDate + ".xlsx";
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    },
    error: function (errorThrown) {
      console.error("Error al descargar el archivo Excel: " + errorThrown);
    },
  });
};
const descargarBoletaPDF = (ID_Cotizacion) => {
  $.ajax({
    url: base_url + "CargaConsolidada/CCotizaciones/descargarBoleta",
    type: "POST",
    xhrFields: {
      responseType: "blob",
    },
    data: JSON.stringify({ ID_Cotizacion: ID_Cotizacion }),
    success: function (response) {
      var blob = new Blob([response], {
        type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
      });
      //return excel file converted to pdf
      convertirExcelAPDF(blob);
    },
    error: function (errorThrown) {
      console.error("Error al descargar el archivo Excel: " + errorThrown);
    },
  });
};
const convertirExcelAPDF = (excelBlob) => {
  const reader = new FileReader();
  reader.onload = function (event) {
    const data = new Uint8Array(event.target.result);
    const workbook = XLSX.read(data, { type: "array" });
    console.log(workbook);
    // Convertir el libro de trabajo (workbook) a PDF
    const pdfBlob = workbook2pdf(workbook);
    // Descargar el archivo PDF
  };
  reader.readAsArrayBuffer(excelBlob);
};

const workbook2pdf = (workbook) => {
  const pdf = new jsPDF();
  const sheetName = workbook.SheetNames[0];
  const worksheet = workbook.Sheets[sheetName];
  const html = XLSX.utils.sheet_to_html(worksheet);

  // Crear un contenedor temporal para el HTML
  const tempDiv = document.createElement("div");
  tempDiv.innerHTML = html;
  document.body.appendChild(tempDiv);

  html2canvas(tempDiv, {
    scale: 0.5,
  }).then((canvas) => {
    const imgData = canvas.toDataURL("image/png");
    pdf.addImage(imgData, "PNG", 10, 10, canvas.width / 4, canvas.height / 4);
    const pdfBlob = pdf.output("blob");

    // Eliminar el contenedor temporal
    document.body.removeChild(tempDiv);

    // Aquí puedes hacer lo que necesites con el pdfBlob, por ejemplo, descargarlo
    const url = URL.createObjectURL(pdfBlob);
    const a = document.createElement("a");
    a.href = url;
    a.download = "exported.pdf";
    a.click();

    URL.revokeObjectURL(url);
  });
};

const descargarPDF = (pdfBlob) => {
  console.log(pdfBlob);
  const url = window.URL.createObjectURL(pdfBlob);
  const a = document.createElement("a");
  a.href = url;
  a.download = "boleta.pdf";
  document.body.appendChild(a);
  a.click();
  window.URL.revokeObjectURL(url);
};
const agregarProveedor = () => {
  const index = $(".proveedor").length;
  $("#div-CotizacionBody").append(getProvTemplate(index));
  //add initial product
  $(".proveedor-" + index + "-productos").append(
    getProductoTemplate(index, 0, -1)
  );
  const button = $(`#button-tributo-${index}-${0}`);
  button.attr("data-nombre", "nuevo");
  button.attr("data-proveedorIndex", newProveedores.length);
  button.attr("data-productid", 0);
  button.attr("data-newProveedor", true);
  newProveedor = null;
  newProducto = null;
  newProveedores.push({
    indicators: {
      CBM_Total: 0,
      Peso_Total: 0,
    },
    productos: [
      {
        ID_Producto: -1,
        URL_Link: "",
        Nombre_Comercial: "",
        Uso: "",
        Cantidad: 0,
        Valor_Unitario: 0,
        tributos: {
          "ad-valorem": 0,
          igv: 16,
          ipm: 2,
          percepcion: 3.5,
          valoracion: 0,
          antidumping: 0,
        },
      },
    ],
  });
  newProductos.push({
    Prooveedor_Index: index,
    ID_Producto_temp: 0,
    ID_Producto: -1,
    URL_Link: "",
    Nombre_Comercial: "",
    Uso: "",
    Cantidad: 0,
    Valor_Unitario: 0,
    created_for_new: true,
    tributos: {
      "ad-valorem": 0,
      igv: 16,
      ipm: 2,
      percepcion: 3.5,
      valoracion: 0,
      antidumping: 0,
    },
  });
  //set disabled
};
const updateTipoCliente = (select, ID_Cotizacion) => {
  const tipoCliente = $(select).val();
  url = base_url + "CargaConsolidada/CCotizaciones/updateTipoCliente";
  $.ajax({
    url: url,
    type: "post",
    dataType: "JSON",
    contentType: "application/json; charset=utf-8",
    data: JSON.stringify({
      ID_Cotizacion: ID_Cotizacion ?? CotizacionID,
      Tipo_Cliente: tipoCliente,
    }),
    success: function (response) {
      console.log(response);
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.log(jqXHR);
    },
  });
};
const updateEstadoCotizacion = (select, ID_Cotizacion) => {
  const estado = $(select).val();
  url = base_url + "CargaConsolidada/CCotizaciones/updateEstadoCotizacion";
  $.ajax({
    url: url,
    type: "post",
    dataType: "JSON",
    contentType: "application/json; charset=utf-8",
    data: JSON.stringify({
      ID_Cotizacion: ID_Cotizacion,
      Estado: estado,
    }),
    success: function (response) {
      console.log(response);
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.log(jqXHR);
    },
  });
};
