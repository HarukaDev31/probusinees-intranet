$(document).ready(function() {
    // Obtener el ID de la orden desde la URL
    var orderId = window.location.pathname.split('/').pop();
    $('#uploadOrderId').val(orderId);

    // Obtener y mostrar los datos de la orden y la info de cotización/orden
    $.ajax({
        url: base_url + 'OrdersController/getOrderInfo/' + orderId,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                var order = response.order;
                $('#customerName').text(order.customer_full_name || 'No disponible');
                // Puedes agregar aquí más campos si quieres mostrar más datos de la orden
                // Ejemplo: $('#orderNumber').text(order.order_number);

                // Cotización
                if (order.cotizacion_url) {
                    $('#btnDescargarCotizacion').show().data('url', order.cotizacion_url);
                    $('#cotizacionInfo').show();
                } else {
                    $('#btnDescargarCotizacion').hide();
                    $('#cotizacionInfo').hide();
                }
                // Estado confirmado
                if (order.estado_confirmado) {
                    $('#selectEstadoConfirmado').val(order.estado_confirmado);
                }
                // Orden - lógica corregida para mostrar/ocultar botones
                if (order.orden_url) {
                    // Si existe orden: mostrar descargar y borrar, ocultar subir
                    $('#btnDescargarOrden').show().data('url', order.orden_url);
                    $('#btnBorrarOrden').show();
                    $('#btnSubirOrden').hide();
                } else {
                    // Si NO existe orden: mostrar subir, ocultar descargar y borrar
                    $('#btnDescargarOrden').hide();
                    $('#btnBorrarOrden').hide();
                    $('#btnSubirOrden').show();
                }
            } else {
                $('#customerName').text('No disponible');
                $('#btnDescargarCotizacion').hide();
                $('#cotizacionInfo').hide();
                $('#btnDescargarOrden').hide();
                $('#btnBorrarOrden').hide();
                $('#btnSubirOrden').show(); // Mostrar subir si no hay datos
            }
        },
        error: function() {
            $('#customerName').text('No disponible');
            $('#btnDescargarCotizacion').hide();
            $('#cotizacionInfo').hide();
            $('#btnDescargarOrden').hide();
            $('#btnBorrarOrden').hide();
            $('#btnSubirOrden').show(); // Mostrar subir si hay error
        }
    });

    // Descargar cotización
    $('#btnDescargarCotizacion').off('click').on('click', function() {
        var url = $(this).data('url');
        if (url) {
            window.open(url, '_blank');
        }
    });

    // Subir orden
    $('#btnSubirOrden').off('click').on('click', function() {
        $('#uploadOrdenModal').modal('show');
    });
    $('#btnUploadOrden').off('click').on('click', function() {
        var formData = new FormData(document.getElementById('uploadOrdenForm'));
        $.ajax({
            url: base_url + 'OrdersController/uploadOrden',
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
                        text: 'Orden subida correctamente'
                    });
                    $('#uploadOrdenModal').modal('hide');
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

    // Descargar orden
    $('#btnDescargarOrden').off('click').on('click', function() {
        var url = $(this).data('url');
        if (url) {
            window.open(url, '_blank');
        }
    });

    // Borrar orden
    $('#btnBorrarOrden').off('click').on('click', function() {
        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Esta acción eliminará la orden subida',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: base_url + 'OrdersController/deleteOrden',
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
                        Swal.fire('Error', 'Error al eliminar la orden', 'error');
                    }
                });
            }
        });
    });

    // Cambiar estado confirmado
    function updateEstadoConfirmado(orderId, estado) {
       
    }   

    // Inicializar DataTable para la tabla de productos
    if ($.fn.DataTable.isDataTable('#productsTable')) {
        $('#productsTable').DataTable().destroy();
    }
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
            {"data": "store_link", "render": function(data, type, row) {
                var link = data;
                // Si no hay store_link, usar base_url + 'producto/' + product_id como fallback
                if (!data || data === '') {
                    link = base_url + 'producto/' + row.product_id;
                }
                return '<a href="' + link + '" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-external-link-alt"></i> Ver</a>';
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
}); 