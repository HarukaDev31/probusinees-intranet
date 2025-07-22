$(document).ready(function() {
    // Inicializar DataTable
    initClientesTable();
    
    // Cargar datos iniciales
    cargarEstadisticas();
    
    // Event listeners para filtros
    $('#filtroTipoCliente, #filtroEstado, #filtroPais').on('change', function() {
        $('#clientesTable').DataTable().ajax.reload();
    });
    
    // Form submit para importar clientes
    $('#formImportarClientes').on('submit', function(e) {
        e.preventDefault();
        procesarImportacion();
    });
});

let clientesTable;
let currentClienteId = null;

/**
 * Inicializar DataTable de clientes
 */
function initClientesTable() {
    // Destruir tabla existente si existe
    if ($.fn.DataTable.isDataTable('#clientesTable')) {
        $('#clientesTable').DataTable().destroy();
    }
    
    clientesTable = $('#clientesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + 'BaseDatos/ClientesController/getClientes',
            type: 'POST',
            data: function(d) {
                d.tipo_cliente = $('#filtroTipoCliente').val();
                d.estado = $('#filtroEstado').val();
                d.pais = $('#filtroPais').val();
            }
        },
        columns: [
            { data: 'id', width: '4%' },
            { data: 'codigo_cliente', width: '8%' },
            { data: 'razon_social', width: '18%' },
            { data: 'nombre_comercial', width: '15%' },
            { data: 'tipo_cliente', width: '10%' },
            { data: 'documento', width: '10%' },
            { data: 'email', width: '12%' },
            { data: 'telefono', width: '8%' },
            { data: 'pais', width: '6%' },
            { data: 'estado', width: '6%' },
            { data: 'fecha_registro', width: '8%' },
            { 
                data: 'acciones', 
                width: '15%',
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
        url: base_url + 'BaseDatos/ClientesController/getEstadisticas',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#totalClientes').text(response.data.total);
                $('#clientesActivos').text(response.data.activos);
                $('#prospectos').text(response.data.prospectos);
                $('#favoritos').text(response.data.favoritos);
            }
        },
        error: function() {
            console.error('Error al cargar estadísticas');
        }
    });
}

/**
 * Limpiar filtros
 */
function limpiarFiltros() {
    $('#filtroTipoCliente').val('');
    $('#filtroEstado').val('');
    $('#filtroPais').val('');
    $('#clientesTable').DataTable().ajax.reload();
}

/**
 * Nuevo cliente
 */
function nuevoCliente() {
    window.location.href = base_url + 'BaseDatos/ClientesController/form';
}

/**
 * Ver cliente
 */
function viewCliente(id) {
    $.ajax({
        url: base_url + 'BaseDatos/ClientesController/getCliente/' + id,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const cliente = response.data;
                currentClienteId = id;
                
                $('#verCodigo').text(cliente.codigo_cliente);
                $('#verRazonSocial').text(cliente.razon_social);
                $('#verNombreComercial').text(cliente.nombre_comercial || 'No especificado');
                $('#verTipoCliente').html(getTipoClienteBadge(cliente.tipo_cliente));
                $('#verDocumento').text((cliente.tipo_documento || '') + ' ' + (cliente.numero_documento || ''));
                $('#verEstado').html(getEstadoBadge(cliente.estado));
                $('#verEmail').text(cliente.email || 'No especificado');
                $('#verTelefono').text(cliente.telefono || 'No especificado');
                $('#verDireccion').text(cliente.direccion || 'No especificada');
                $('#verCiudad').text(cliente.ciudad || 'No especificada');
                $('#verPais').text(cliente.pais || 'No especificado');
                $('#verSitioWeb').text(cliente.sitio_web || 'No especificado');
                $('#verPersonaContacto').text(cliente.persona_contacto || 'No especificado');
                $('#verTelefonoContacto').text(cliente.telefono_contacto || 'No especificado');
                $('#verEmailContacto').text(cliente.email_contacto || 'No especificado');
                $('#verFechaRegistro').text(formatDate(cliente.fecha_registro));
                $('#verFavorito').html(cliente.favorito == 1 ? '<i class="fas fa-star text-warning"></i> Sí' : '<i class="far fa-star"></i> No');
                $('#verNotas').text(cliente.notas || 'Sin notas adicionales');
                
                $('#modalVerCliente').modal('show');
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function() {
            Swal.fire('Error', 'Error al cargar los datos del cliente', 'error');
        }
    });
}

/**
 * Editar cliente
 */
function editCliente(id) {
    window.location.href = base_url + 'BaseDatos/ClientesController/form/' + id;
}

/**
 * Editar desde modal
 */
function editarDesdeModal() {
    if (currentClienteId) {
        $('#modalVerCliente').modal('hide');
        editCliente(currentClienteId);
    }
}

/**
 * Eliminar cliente
 */
function deleteCliente(id) {
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
                url: base_url + 'BaseDatos/ClientesController/delete',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Eliminado', response.message, 'success');
                        $('#clientesTable').DataTable().ajax.reload();
                        cargarEstadisticas();
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Error al eliminar el cliente', 'error');
                }
            });
        }
    });
}

/**
 * Toggle favorito
 */
function toggleFavorito(id, favorito) {
    $.ajax({
        url: base_url + 'BaseDatos/ClientesController/toggleFavorito',
        type: 'POST',
        data: { 
            id: id,
            favorito: favorito
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    title: 'Éxito',
                    text: response.message,
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
                $('#clientesTable').DataTable().ajax.reload();
                cargarEstadisticas();
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function() {
            Swal.fire('Error', 'Error al actualizar el favorito', 'error');
        }
    });
}

/**
 * Ver historial del cliente
 */
function verHistorial(id) {
    $.ajax({
        url: base_url + 'BaseDatos/ClientesController/getHistorial/' + id,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                let html = '';
                
                if (response.data.length === 0) {
                    html = '<div class="alert alert-info">No hay actividades registradas para este cliente.</div>';
                } else {
                    html = '<div class="timeline">';
                    
                    response.data.forEach(function(actividad) {
                        html += '<div class="timeline-item">';
                        html += '<div class="timeline-marker"></div>';
                        html += '<div class="timeline-content">';
                        html += '<h6 class="timeline-title">' + actividad.actividad + '</h6>';
                        html += '<p class="timeline-text">' + actividad.detalles + '</p>';
                        html += '<span class="timeline-date">' + formatDateTime(actividad.fecha) + ' - ' + actividad.usuario + '</span>';
                        html += '</div>';
                        html += '</div>';
                    });
                    
                    html += '</div>';
                }
                
                $('#historialContent').html(html);
                $('#modalHistorialCliente').modal('show');
            }
        },
        error: function() {
            $('#historialContent').html('<div class="alert alert-danger">Error al cargar el historial.</div>');
            $('#modalHistorialCliente').modal('show');
        }
    });
}

/**
 * Ver historial desde modal principal
 */
function verHistorialModal() {
    if (currentClienteId) {
        $('#modalVerCliente').modal('hide');
        verHistorial(currentClienteId);
    }
}

/**
 * Mostrar clientes favoritos
 */
function mostrarFavoritos() {
    // Aplicar filtro de favoritos (esto requeriría modificar el backend)
    Swal.fire('Info', 'Función en desarrollo', 'info');
}

/**
 * Exportar clientes
 */
function exportarClientes() {
    $.ajax({
        url: base_url + 'BaseDatos/ClientesController/exportar',
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
            Swal.fire('Error', 'Error al exportar clientes', 'error');
        }
    });
}

/**
 * Mostrar modal de importación
 */
function importarClientes() {
    $('#modalImportarClientes').modal('show');
}

/**
 * Procesar importación de clientes
 */
function procesarImportacion() {
    const formData = new FormData($('#formImportarClientes')[0]);
    
    $.ajax({
        url: base_url + 'BaseDatos/ClientesController/importar',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        beforeSend: function() {
            Swal.fire({
                title: 'Procesando...',
                text: 'Importando clientes, por favor espere',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        },
        success: function(response) {
            Swal.close();
            
            if (response.success) {
                Swal.fire('Éxito', response.message, 'success');
                $('#modalImportarClientes').modal('hide');
                $('#formImportarClientes')[0].reset();
                $('#clientesTable').DataTable().ajax.reload();
                cargarEstadisticas();
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function() {
            Swal.close();
            Swal.fire('Error', 'Error al procesar la importación', 'error');
        }
    });
}

/**
 * Generar badge de tipo de cliente
 */
function getTipoClienteBadge(tipo) {
    switch (tipo) {
        case 'persona_natural':
            return '<span class="badge badge-primary">Persona Natural</span>';
        case 'empresa':
            return '<span class="badge badge-info">Empresa</span>';
        case 'gobierno':
            return '<span class="badge badge-warning">Gobierno</span>';
        case 'ong':
            return '<span class="badge badge-secondary">ONG</span>';
        default:
            return '<span class="badge badge-light">' + tipo.charAt(0).toUpperCase() + tipo.slice(1) + '</span>';
    }
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
        case 'suspendido':
            return '<span class="badge badge-danger">Suspendido</span>';
        case 'prospecto':
            return '<span class="badge badge-info">Prospecto</span>';
        default:
            return '<span class="badge badge-secondary">' + estado.charAt(0).toUpperCase() + estado.slice(1) + '</span>';
    }
}

/**
 * Formatear fecha
 */
function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES');
}

/**
 * Formatear fecha y hora
 */
function formatDateTime(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES') + ' ' + date.toLocaleTimeString('es-ES');
} 