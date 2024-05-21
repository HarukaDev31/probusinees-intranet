$(function () {
    url = base_url + "Configuracion/TarifasCotizacionesCCController/getTarifas";
    table_Entidad = $("#table-tarifas").DataTable({
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
        [6, "asc"],
        [0, "asc"],
      ],
      ajax: {
        url: url,
        type: "POST",
        dataType: "json",
        data: function (data) {},
      },
      columnDefs: [
        {
          targets: "no-hidden",
          orderable: false,
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
  let id_tarifa = null;
  let tarifa = null;
  const modificarTarifa = (id,tarifa) => {
    //add modal with a input text with tarifa value
    $("#modal-tarifa").modal("show");
    $("#tarifa").val(tarifa);
    id_tarifa = id;
    tarifa = tarifa;
  }
  const guardarTarifa = () => {
    tarifa = $("#tarifa").val();
    if (tarifa == "") {
      alert("Debe ingresar una tarifa");
      return;
    }
    $.ajax({
      url: base_url + "Configuracion/TarifasCotizacionesCCController/modificarTarifa",
      type: "POST",
      dataType: "json",
      data: {
        id_tarifa: id_tarifa,
        tarifa: tarifa
      },
    }).done(function (data) {
        console.log(data);

      if (data.status == 200) {
        $("#modal-tarifa").modal("hide");
        table_Entidad.ajax.reload();
        id_tarifa = null;
        tarifa = null;
      } else {
        alert("Error al modificar la tarifa");
      }
    });
  }
