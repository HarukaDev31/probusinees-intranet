$(document).ready(function() {
    // Obtener el ID de la orden desde la URL
    var orderId = window.location.pathname.split('/').pop();
    $('#uploadOrderId').val(orderId);

    // Obtener y mostrar el nombre del cliente y la info de cotización
    $.ajax({
        url: base_url + 'OrdersController/getOrderInfo/' + orderId,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                var order = response.order;
                $('#customerName').text(order.customer_full_name);
                // Mostrar acciones de cotización
                if (!order.cotizacion_url) {
                    $('#cotizacionActions').html('<button class="btn btn-warning" id="btnShowUploadModal"><i class="fas fa-upload"></i> Subir Cotización</button>');
                } else {
                    var descargarBtn = '<button class="btn btn-success" id="btnDescargarCotizacion"><i class="fas fa-download"></i> Descargar</button>';
                    var eliminarBtn = '<button class="btn btn-danger ml-2" id="btnEliminarCotizacion"><i class="fas fa-trash"></i> Eliminar</button>';
                    $('#cotizacionActions').html(descargarBtn + ' ' + eliminarBtn);
                    // Guardar la url para descargar
                    $('#btnDescargarCotizacion').data('url', order.cotizacion_url);
                }
            } else {
                $('#customerName').text('No disponible');
            }
        },
        error: function() {
            $('#customerName').text('No disponible');
        }
    });

    // Delegar click para mostrar modal de subida
    $(document).on('click', '#btnShowUploadModal', function() {
        $('#uploadCotizacionModal').modal('show');
    });

    // Subir cotización
    $('#btnUploadCotizacion').on('click', function() {
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
                    location.reload();
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
    });

    // Descargar cotización
    $(document).on('click', '#btnDescargarCotizacion', function() {
        var url = $(this).data('url');
        if (url) {
            window.open(url, '_blank');
        }
    });

    // Eliminar cotización
    $(document).on('click', '#btnEliminarCotizacion', function() {
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
                            location.reload();
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
    });

    // Destruir si ya existe
    if ($.fn.DataTable.isDataTable('#productsTable')) {
        $('#productsTable').DataTable().destroy();
    }
    // Inicializar DataTable para la tabla de productos
    var productsTable = $('#productsTable').DataTable({
        "processing": true,
        "serverSide": false,
        "ajax": {
            "url": base_url + "OrdersController/getOrderProducts/" + orderId,
            "type": "GET"
        },
        "columns": [
            {"data": "product_image", "render": function(data, type, row) {
                if (data && data !== '') {
                    return '<img src="' + data + '" alt="Producto" style="max-width: 80px; max-height: 80px; object-fit: cover;">';
                } else {
                    return '<span class="text-muted">Sin imagen</span>';
                }
            }},
            {"data": "product_name"},
            {"data": "quantity"},
            {"data": "unit_price", "render": function(data) {
                return 'S/. ' + parseFloat(data).toFixed(2);
            }},
            {"data": "total_price", "render": function(data) {
                return 'S/. ' + parseFloat(data).toFixed(2);
            }},
            {"data": "store_link", "render": function(data) {
                if (data && data !== '') {
                    return '<a href="' + data + '" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-external-link-alt"></i> Ver</a>';
                } else {
                    return '<span class="text-muted">No disponible</span>';
                }
            }},
            {"data": "alibaba_link", "render": function(data) {
                if (data && data !== '') {
                    return '<a href="' + data + '" target="_blank" class="btn btn-sm btn-outline-info"><i class="fas fa-external-link-alt"></i> Ver</a>';
                } else {
                    return '<span class="text-muted">No disponible</span>';
                }
            }}
        ],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        },
        "responsive": true,
        "pageLength": 25,
        "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]]
    });

   
    // Función para formatear fecha
    function formatDate(dateString) {
        var date = new Date(dateString);
        return date.toLocaleDateString('es-ES', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    }

    // Función para obtener clase de estado
    function getEstadoClass(status) {
        switch (status) {
            case 'PENDIENTE':
                return 'bg-secondary';
            case 'PAGADO':
                return 'bg-success';
            case 'ADELANTO':
                return 'bg-warning';
            case 'OBSERVADO':
                return 'bg-danger';
            case 'CONFIRMADO':
                return 'bg-info';
            default:
                return 'bg-secondary';
        }
    }

    // Función para mostrar modal de subida de cotización
    window.showUploadModal = function() {
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
                    location.reload();
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

    // Función para descargar Excel
    window.downloadOrderExcel = function(orderId) {
        window.open(base_url + 'OrdersController/downloadOrderExcel/' + orderId, '_blank');
    };
}); 