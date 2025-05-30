var tableConsolidadoCrons;
var url;
var cronId;
var createdAt;
$(document).ready(function () {
    url = base_url + "CargaConsolidada/ContenedorConsolidado/getConsolidadoCrons";
    tableConsolidadoCrons = $('#table-crons-consolidado').DataTable({
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
        },
    });

});
async function deleteCron(id) {
    Swal.fire({
        title: "¿Estás seguro?",
        text: "¡No podrás revertir esto!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, eliminarlo",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: base_url + "CargaConsolidada/ContenedorConsolidado/deleteCron/" + id,
                type: "POST",
                dataType: "JSON",
                success: function (response) {
                    if (response.status) {
                        Swal.fire("Eliminado", response.message, "success");
                        tableConsolidadoCrons.ajax.reload();
                    } else {
                        Swal.fire("Error", response.message, "error");
                    }
                },
                error: function () {
                    Swal.fire("Error", "Ocurrió un error al eliminar el cron.", "error");
                },
            });
        }
    });
}
async function editCron(id) {
    $.ajax({
        url: base_url + "CargaConsolidada/ContenedorConsolidado/getCron/" + id,
        type: "GET",
        dataType: "JSON",
        success: function (response) {
            if (response.status) {
                // Aquí puedes manejar la respuesta y mostrar los datos en un formulario de edición
                // Por ejemplo, puedes llenar un modal con los datos del cron
                $('#cronDetailsModal').modal('show');
                const data = response.data;
                cronId = data.id;
                let dataJson = JSON.parse(data.data_json);
                $('#cronMessage').val(dataJson.message);
                $("#cronPhoneNumber").val(dataJson.phoneNumberId);
                $('#cronDescription').val(data.description);
                $('#cronExecutionTime').val(data.time_between);
                $('#cronExecutionDate').val(data.execution_at);
                createdAt = data.created_at;
                // Otros campos...
            } else {
                Swal.fire("Error", response.message, "error");
            }
        },
        error: function () {
            Swal.fire("Error", "Ocurrió un error al obtener el cron.", "error");
        },
    });
}
$("#saveCronDetails").on("click", function () {
    const cronMessage = $('#cronMessage').val();
    const cronPhoneNumber = $('#cronPhoneNumber').val();
    const cronExecutionTime = $('#cronExecutionTime').val();

    if (!cronMessage || !cronPhoneNumber  || !cronExecutionTime) {
        Swal.fire("Error", "Por favor, completa todos los campos.", "error");
        return;
    }

    const dataJson = JSON.stringify({
        message: cronMessage,
        phoneNumberId: cronPhoneNumber
    });

    $.ajax({
        url: base_url + "CargaConsolidada/ContenedorConsolidado/updateCron",
        type: "POST",
        dataType: "JSON",
        data: {
            id: cronId,
            data_json: dataJson,
            time_between: cronExecutionTime,
            created_at: createdAt
        },
        success: function (response) {
            if (response.status) {
                Swal.fire("Actualizado", response.message, "success");
                tableConsolidadoCrons.ajax.reload();
                $('#cronDetailsModal').modal('hide');
            } else {
                Swal.fire("Error", response.message, "error");
            }
        },
        error: function () {
            Swal.fire("Error", "Ocurrió un error al actualizar el cron.", "error");
        },
    });
})
