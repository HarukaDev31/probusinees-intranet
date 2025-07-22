$(document).ready(function() {
    // Inicializar DataTable
    initProductosTable();
    
    // Cargar datos iniciales
    cargarEstadisticas();
    cargarCategorias();
    
    // Event listeners para filtros
    $('#filtroCategoria, #filtroEstado').on('change', function() {
        $('#productosTable').DataTable().ajax.reload();
    });
    
    // Form submit para actualizar stock
    $('#formActualizarStock').on('submit', function(e) {
        e.preventDefault();
        actualizarStock();
    });
});

let productosTable;

/**
 * Inicializar DataTable de productos
 */
function initProductosTable() {
    // Destruir tabla existente si existe
    if ($.fn.DataTable.isDataTable('#productosTable')) {
        $('#productosTable').DataTable().destroy();
    }
    
    productosTable = $('#productosTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + 'BaseDatos/ProductosController/getProductos',
            type: 'POST',
            data: function(d) {
                d.categoria = $('#filtroCategoria').val();
                d.estado = $('#filtroEstado').val();
            }
        },
        columns: [
            { data: 'id', width: '5%' },
            { data: 'codigo', width: '10%' },
            { data: 'nombre', width: '20%' },
            { data: 'categoria', width: '15%' },
            { 
                data: 'precio', 
                width: '10%',
                render: function(data, type, row) {
                    return 'S/ ' + data;
                }
            },
            { 
                data: 'stock', 
                width: '8%',
                render: function(data, type, row) {
                    let clase = '';
                    if (parseInt(data) === 0) {
                        clase = 'text-danger font-weight-bold';
                    } else if (parseInt(data) <= 10) {
                        clase = 'text-warning font-weight-bold';
                    }
                    return '<span class="' + clase + '">' + data + '</span>';
                }
            },
            { data: 'estado', width: '10%' },
            { data: 'fecha_creacion', width: '12%' },
            { 
                data: 'acciones', 
                width: '10%',
                orderable: false,
                searchable: false
            }
        ],
        order: [[0, 'desc']],
        language: {
            url: base_url + 'assets/js/dataTables_spanish.json'
        },
        responsive: true,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-success btn-sm'
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-danger btn-sm'
            }
        ]
    });
}

/**
 * Cargar estadísticas
 */
function cargarEstadisticas() {
    $.ajax({
        url: base_url + 'BaseDatos/ProductosController/getEstadisticas',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#totalProductos').text(response.data.total);
                $('#productosActivos').text(response.data.activos);
                $('#stockBajo').text(response.data.stock_bajo);
                $('#productosAgotados').text(response.data.agotados);
            }
        },
        error: function() {
            console.error('Error al cargar estadísticas');
        }
    });
}

/**
 * Cargar categorías para el filtro
 */
function cargarCategorias() {
    $.ajax({
        url: base_url + 'BaseDatos/ProductosController/getCategorias',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                let options = '<option value="">Todas las categorías</option>';
                response.data.forEach(function(categoria) {
                    options += '<option value="' + categoria.id + '">' + categoria.nombre + '</option>';
                });
                $('#filtroCategoria').html(options);
            }
        },
        error: function() {
            console.error('Error al cargar categorías');
        }
    });
}

/**
 * Limpiar filtros
 */
function limpiarFiltros() {
    $('#filtroCategoria').val('');
    $('#filtroEstado').val('');
    $('#productosTable').DataTable().ajax.reload();
}

/**
 * Nuevo producto
 */
function nuevoProducto() {
    window.location.href = base_url + 'BaseDatos/ProductosController/form';
}

/**
 * Ver producto
 */
function viewProducto(id) {
    $.ajax({
        url: base_url + 'BaseDatos/ProductosController/getProducto/' + id,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const producto = response.data;
                $('#verCodigo').text(producto.codigo);
                $('#verNombre').text(producto.nombre);
                $('#verCategoria').text(producto.categoria_nombre || 'Sin categoría');
                $('#verEstado').html(getEstadoBadge(producto.estado));
                $('#verPrecio').text('S/ ' + parseFloat(producto.precio).toFixed(2));
                $('#verStock').text(producto.stock);
                $('#verStockMinimo').text(producto.stock_minimo);
                $('#verFechaCreacion').text(formatDate(producto.fecha_creacion));
                $('#verDescripcion').text(producto.descripcion || 'Sin descripción');
                
                // Guardar ID para posible edición
                $('#modalVerProducto').data('producto-id', id);
                $('#modalVerProducto').modal('show');
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function() {
            Swal.fire('Error', 'Error al cargar los datos del producto', 'error');
        }
    });
}

/**
 * Editar producto
 */
function editProducto(id) {
    window.location.href = base_url + 'BaseDatos/ProductosController/form/' + id;
}

/**
 * Editar desde modal
 */
function editarDesdeModal() {
    const id = $('#modalVerProducto').data('producto-id');
    if (id) {
        $('#modalVerProducto').modal('hide');
        editProducto(id);
    }
}

/**
 * Eliminar producto
 */
function deleteProducto(id) {
    Swal.fire({
        title: '¿Está seguro?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: base_url + 'BaseDatos/ProductosController/delete',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Eliminado', response.message, 'success');
                        $('#productosTable').DataTable().ajax.reload();
                        cargarEstadisticas();
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Error al eliminar el producto', 'error');
                }
            });
        }
    });
}

/**
 * Mostrar modal para actualizar stock
 */
function actualizarStockModal(id) {
    $.ajax({
        url: base_url + 'BaseDatos/ProductosController/getProducto/' + id,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const producto = response.data;
                $('#stockProductoId').val(id);
                $('#stockProductoNombre').text(producto.nombre);
                $('#stockActual').text(producto.stock);
                $('#tipoMovimiento').val('');
                $('#cantidadMovimiento').val('');
                $('#modalActualizarStock').modal('show');
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function() {
            Swal.fire('Error', 'Error al cargar los datos del producto', 'error');
        }
    });
}

/**
 * Actualizar stock
 */
function actualizarStock() {
    const id = $('#stockProductoId').val();
    const tipo = $('#tipoMovimiento').val();
    const cantidad = $('#cantidadMovimiento').val();
    
    $.ajax({
        url: base_url + 'BaseDatos/ProductosController/updateStock',
        type: 'POST',
        data: {
            id: id,
            tipo: tipo,
            stock: cantidad
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire('Éxito', response.message, 'success');
                $('#modalActualizarStock').modal('hide');
                $('#productosTable').DataTable().ajax.reload();
                cargarEstadisticas();
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function() {
            Swal.fire('Error', 'Error al actualizar el stock', 'error');
        }
    });
}

/**
 * Exportar productos
 */
function exportarProductos() {
    $.ajax({
        url: base_url + 'BaseDatos/ProductosController/exportar',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire('Éxito', response.message, 'success');
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function() {
            Swal.fire('Error', 'Error al exportar productos', 'error');
        }
    });
}

/**
 * Generar badge de estado
 */
function getEstadoBadge(estado) {
    switch (estado) {
        case 'activo':
            return '<span class="badge badge-success">Activo</span>';
        case 'inactivo':
            return '<span class="badge badge-secondary">Inactivo</span>';
        case 'agotado':
            return '<span class="badge badge-danger">Agotado</span>';
        default:
            return '<span class="badge badge-secondary">' + estado.charAt(0).toUpperCase() + estado.slice(1) + '</span>';
    }
}

/**
 * Formatear fecha
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES') + ' ' + date.toLocaleTimeString('es-ES');
} 