$(document).ready(function() {
    // Destruir si ya existe
    if ($.fn.DataTable.isDataTable('#ordersTable')) {
        $('#ordersTable').DataTable().destroy();
    }
    // Ajustar el filtro de estado según la vista
    var isConfirmados = window.location.pathname.includes('listarConfirmados');
    var estadoFiltroSelector = isConfirmados ? '#estado_confirmado' : '#estado';
    
    // Mostrar/ocultar filtros según la vista
    if (isConfirmados) {
        $('#estado_confirmado_group').show();
        $('#estado').parent().hide();
    } else {
        $('#estado_confirmado_group').hide();
        $('#estado').parent().show();
    }
    // Si no existe el select, lo creamos dinámicamente (opcional)
    // ...
    var ordersTable = $('#ordersTable').DataTable({
        "processing": true,
        "serverSide": false,
        "ajax": {
            "url": base_url + "OrdersController/getOrders",
            "type": "POST",
            "data": function(d) {
                var data = {
                    fecha_inicio: $('#fecha_inicio').val(),
                    fecha_fin: $('#fecha_fin').val(),
                    estado_pago: $('#estado_pago').val()
                };
                if (isConfirmados) {
                    data.confirmados = true;
                    data.estado_confirmado = $(estadoFiltroSelector).val();
                } else {
                    data.estado = $(estadoFiltroSelector).val();
                }
                return data;
            }
        },
        "columns": [
            {"data": 0}, // N.
            {"data": 1}, // N. Orden
            {"data": 2}, // Fecha
            {"data": 3}, // Cliente
            {"data": 4}, // DNI
            {"data": 5}, // WhatsApp
            {"data": 6}, // Correo
            {"data": 7}, // Estado
            {"data": 8}, // Cotización
            {"data": 9}  // Acciones
        ],
        "order": [[2, "desc"]], // Ordenar por fecha descendente
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        },
        "responsive": true,
        "pageLength": 25,
        "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]]
    });

    // Búsqueda en tiempo real (ya no es necesario, DataTables tiene su propio buscador)
    // $('#searchInput').off('keyup').on('keyup', function() {
    //     ordersTable.search(this.value).draw();
    // });

    // Filtros
    window.showFilters = function() {
        $('#filtersModal').modal('show');
    };
    window.applyFilters = function() {
        $('#filtersModal').modal('hide');
        ordersTable.ajax.reload();
    };
    // Cambiar filtro de estado
    $(estadoFiltroSelector).off('change').on('change', function() {
        ordersTable.ajax.reload();
    });

    // Cambiar filtro de estado de pago
    $('#estado_pago').off('change').on('change', function() {
        ordersTable.ajax.reload();
    });

    // Subida de cotización
    $('#btnUploadCotizacion').off('click').on('click', function() {
        window.uploadCotizacion();
    });

    // Función para ver detalles de la orden
    window.viewOrderDetails = function(orderId) {
        // Obtener el nombre del cliente de la fila seleccionada
        var rowData = ordersTable.row($('#ordersTable button[data-id="' + orderId + '"]').parents('tr')).data();
        var customerName = rowData ? rowData[3] : '';
        viewOrderDetails(orderId, customerName);
    };

    // Función para descargar Excel de la orden
    window.downloadOrderExcel = function(orderId) {
        window.open(base_url + 'OrdersController/downloadOrderExcel/' + orderId, '_blank');
    };

    // Función para eliminar orden
    window.deleteOrder = function(orderId) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: base_url + 'OrdersController/deleteOrder',
                    type: 'POST',
                    data: {
                        orderId: orderId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire(
                                '¡Eliminado!',
                                response.message,
                                'success'
                            );
                            ordersTable.ajax.reload();
                        } else {
                            Swal.fire(
                                'Error',
                                response.message,
                                'error'
                            );
                        }
                    },
                    error: function() {
                        Swal.fire(
                            'Error',
                            'Error al eliminar la orden',
                            'error'
                        );
                    }
                });
            }
        });
    };

    // Función para formatear fecha
    function formatDate(dateString) {
        var date = new Date(dateString);
        return date.toLocaleDateString('es-ES', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    }

    // Limpiar filtros
    $('#filtersForm').on('reset', function() {
        setTimeout(function() {
            ordersTable.ajax.reload();
        }, 100);
    });

    // Evento para cerrar modales
    $('.modal').on('hidden.bs.modal', function() {
        // Limpiar contenido de modales si es necesario
        if ($(this).attr('id') === 'orderDetailsModal') {
            $('#orderItemsTable').html('');
        }
    });

    // Función para mostrar modal de subida de cotización
    window.showUploadModal = function(orderId) {
        $('#uploadOrderId').val(orderId);
        $('#uploadCotizacionModal').modal('show');
    };

    // Función para subir cotización
    window.uploadCotizacion = function() {
        var formData = new FormData(document.getElementById('uploadCotizacionForm'));
        
        $.ajax({
            url: base_url + 'OrdersController/uploadCotizacion',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                var data = JSON.parse(response);
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'Cotización subida correctamente'
                    });
                    $('#uploadCotizacionModal').modal('hide');
                    // Recargar la tabla
                    ordersTable.ajax.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al subir el archivo'
                });
            }
        });
    };

    // Función para ver cotización
    window.viewCotizacion = function(orderId) {
        window.open(base_url + 'OrdersController/cotizacion/' + orderId, '_blank');
    };

    // Nueva función para eliminar cotización
    window.deleteCotizacion = function(orderId) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Esta acción eliminará la cotización de la orden',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: base_url + 'OrdersController/deleteCotizacion',
                    type: 'POST',
                    data: { orderId: orderId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire('¡Eliminado!', response.message, 'success');
                            $('#ordersTable').DataTable().ajax.reload();
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Error al eliminar la cotización', 'error');
                    }
                });
            }
        });
    };

    window.updateStatusCotizacionJS = function(orderId, status) {
        $.ajax({
            url: base_url + 'OrdersController/updateStatusCotizacion',
            type: 'POST',
            data: { orderId: orderId, status: status },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire('¡Actualizado!', response.message, 'success');
                    $('#ordersTable').DataTable().ajax.reload(null, false);
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Error al actualizar el estado', 'error');
            }
        });
    };

    // Cambiar filtro de estado confirmado
    $('#estado_confirmado').off('change').on('change', function() {
        ordersTable.ajax.reload();
    });

    window.updateEstadoConfirmadoJS = function(orderId, estado) {
        $.ajax({
            url: base_url + 'OrdersController/updateEstadoConfirmado',
            type: 'POST',
            data: { orderId: orderId, estado: estado },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire('¡Actualizado!', response.message, 'success');
                    $('#ordersTable').DataTable().ajax.reload(null, false);
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Error al actualizar el estado', 'error');
            }
        });
    };
}); 