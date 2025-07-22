$(document).ready(function() {
    // Inicializar DataTable
    initRegulacionesTable();
    
    // Cargar datos iniciales
    cargarEstadisticas();
    cargarPaises();
    
    // Event listeners para filtros
    $('#filtroTipo, #filtroEstado, #filtroPais').on('change', function() {
        $('#regulacionesTable').DataTable().ajax.reload();
    });
});

let regulacionesTable;
let currentRegulacionId = null;

/**
 * Inicializar DataTable de regulaciones
 */
function initRegulacionesTable() {
    // Destruir tabla existente si existe
    if ($.fn.DataTable.isDataTable('#regulacionesTable')) {
        $('#regulacionesTable').DataTable().destroy();
    }
    
    regulacionesTable = $('#regulacionesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: base_url + 'BaseDatos/RegulacionesController/getRegulaciones',
            type: 'POST',
            data: function(d) {
                d.tipo = $('#filtroTipo').val();
                d.estado = $('#filtroEstado').val();
                d.pais = $('#filtroPais').val();
            }
        },
        columns: [
            { data: 'id', width: '5%' },
            { data: 'codigo', width: '10%' },
            { data: 'titulo', width: '25%' },
            { data: 'tipo', width: '10%' },
            { data: 'pais', width: '10%' },
            { data: 'entidad_emisora', width: '15%' },
            { data: 'fecha_vigencia', width: '10%' },
            { data: 'estado', width: '10%' },
            { data: 'fecha_creacion', width: '10%' },
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
        url: base_url + 'BaseDatos/RegulacionesController/getEstadisticas',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#totalRegulaciones').text(response.data.total);
                $('#regulacionesVigentes').text(response.data.vigentes);
                $('#enRevision').text(response.data.en_revision);
                $('#proximasVencer').text(response.data.proximas_vencer);
            }
        },
        error: function() {
            console.error('Error al cargar estadísticas');
        }
    });
}

/**
 * Cargar países para el filtro
 */
function cargarPaises() {
    $.ajax({
        url: base_url + 'BaseDatos/RegulacionesController/getPaises',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                let options = '<option value="">Todos los países</option>';
                response.data.forEach(function(pais) {
                    options += '<option value="' + pais.pais + '">' + pais.pais + '</option>';
                });
                $('#filtroPais').html(options);
            }
        },
        error: function() {
            console.error('Error al cargar países');
        }
    });
}

/**
 * Limpiar filtros
 */
function limpiarFiltros() {
    $('#filtroTipo').val('');
    $('#filtroEstado').val('');
    $('#filtroPais').val('');
    $('#regulacionesTable').DataTable().ajax.reload();
}

/**
 * Nueva regulación
 */
function nuevaRegulacion() {
    window.location.href = base_url + 'BaseDatos/RegulacionesController/form';
}

/**
 * Ver regulación
 */
function viewRegulacion(id) {
    $.ajax({
        url: base_url + 'BaseDatos/RegulacionesController/getRegulacion/' + id,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const regulacion = response.data;
                currentRegulacionId = id;
                
                $('#verCodigo').text(regulacion.codigo);
                $('#verTitulo').text(regulacion.titulo);
                $('#verTipo').html(getTipoBadge(regulacion.tipo));
                $('#verEstado').html(getEstadoBadge(regulacion.estado));
                $('#verPais').text(regulacion.pais || 'No especificado');
                $('#verEntidadEmisora').text(regulacion.entidad_emisora || 'No especificado');
                $('#verFechaVigencia').text(formatDate(regulacion.fecha_vigencia) || 'No especificada');
                $('#verFechaVencimiento').text(formatDate(regulacion.fecha_vencimiento) || 'No especificada');
                $('#verDescripcion').text(regulacion.descripcion || 'Sin descripción');
                $('#verObservaciones').text(regulacion.observaciones || 'Sin observaciones');
                
                // Mostrar enlace al documento si existe
                if (regulacion.url_documento) {
                    $('#linkDocumento').attr('href', regulacion.url_documento);
                    $('#documentoSection').show();
                } else {
                    $('#documentoSection').hide();
                }
                
                $('#modalVerRegulacion').modal('show');
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function() {
            Swal.fire('Error', 'Error al cargar los datos de la regulación', 'error');
        }
    });
}

/**
 * Editar regulación
 */
function editRegulacion(id) {
    window.location.href = base_url + 'BaseDatos/RegulacionesController/form/' + id;
}

/**
 * Editar desde modal
 */
function editarDesdeModal() {
    if (currentRegulacionId) {
        $('#modalVerRegulacion').modal('hide');
        editRegulacion(currentRegulacionId);
    }
}

/**
 * Eliminar regulación
 */
function deleteRegulacion(id) {
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
                url: base_url + 'BaseDatos/RegulacionesController/delete',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Eliminado', response.message, 'success');
                        $('#regulacionesTable').DataTable().ajax.reload();
                        cargarEstadisticas();
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Error al eliminar la regulación', 'error');
                }
            });
        }
    });
}

/**
 * Ver documento
 */
function verDocumento(url) {
    window.open(url, '_blank');
}

/**
 * Mostrar regulaciones próximas a vencer
 */
function mostrarProximasVencer() {
    cargarProximasVencer();
    $('#modalProximasVencer').modal('show');
}

/**
 * Cargar regulaciones próximas a vencer
 */
function cargarProximasVencer() {
    const dias = $('#diasVencimiento').val();
    
    $.ajax({
        url: base_url + 'BaseDatos/RegulacionesController/proximasVencer',
        type: 'GET',
        data: { dias: dias },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                let html = '';
                
                if (response.data.length === 0) {
                    html = '<div class="alert alert-info">No hay regulaciones próximas a vencer en este período.</div>';
                } else {
                    html = '<div class="table-responsive">';
                    html += '<table class="table table-sm table-striped">';
                    html += '<thead><tr><th>Código</th><th>Título</th><th>Fecha Vencimiento</th><th>Días Restantes</th><th>Acciones</th></tr></thead>';
                    html += '<tbody>';
                    
                    response.data.forEach(function(regulacion) {
                        const diasRestantes = calcularDiasRestantes(regulacion.fecha_vencimiento);
                        const claseDias = diasRestantes <= 7 ? 'text-danger' : (diasRestantes <= 15 ? 'text-warning' : 'text-info');
                        
                        html += '<tr>';
                        html += '<td>' + regulacion.codigo + '</td>';
                        html += '<td>' + regulacion.titulo + '</td>';
                        html += '<td>' + formatDate(regulacion.fecha_vencimiento) + '</td>';
                        html += '<td class="' + claseDias + ' font-weight-bold">' + diasRestantes + ' días</td>';
                        html += '<td>';
                        html += '<button class="btn btn-sm btn-info" onclick="viewRegulacion(' + regulacion.id + ')" title="Ver"><i class="fas fa-eye"></i></button> ';
                        html += '<button class="btn btn-sm btn-warning" onclick="editRegulacion(' + regulacion.id + ')" title="Editar"><i class="fas fa-edit"></i></button>';
                        html += '</td>';
                        html += '</tr>';
                    });
                    
                    html += '</tbody></table></div>';
                }
                
                $('#listaProximasVencer').html(html);
            }
        },
        error: function() {
            $('#listaProximasVencer').html('<div class="alert alert-danger">Error al cargar las regulaciones.</div>');
        }
    });
}

/**
 * Marcar regulación como revisada
 */
function marcarRevisada() {
    if (!currentRegulacionId) return;
    
    Swal.fire({
        title: 'Marcar como Revisada',
        input: 'textarea',
        inputLabel: 'Observaciones de la revisión (opcional):',
        inputPlaceholder: 'Ingrese las observaciones...',
        showCancelButton: true,
        confirmButtonText: 'Marcar como Revisada',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: base_url + 'BaseDatos/RegulacionesController/marcarRevisada',
                type: 'POST',
                data: {
                    id: currentRegulacionId,
                    observaciones: result.value || ''
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Éxito', response.message, 'success');
                        $('#modalVerRegulacion').modal('hide');
                        $('#regulacionesTable').DataTable().ajax.reload();
                        cargarEstadisticas();
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Error al marcar la regulación como revisada', 'error');
                }
            });
        }
    });
}

/**
 * Exportar regulaciones
 */
function exportarRegulaciones() {
    $.ajax({
        url: base_url + 'BaseDatos/RegulacionesController/exportar',
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
            Swal.fire('Error', 'Error al exportar regulaciones', 'error');
        }
    });
}

/**
 * Generar badge de tipo
 */
function getTipoBadge(tipo) {
    switch (tipo) {
        case 'ley':
            return '<span class="badge badge-primary">Ley</span>';
        case 'decreto':
            return '<span class="badge badge-info">Decreto</span>';
        case 'resolucion':
            return '<span class="badge badge-warning">Resolución</span>';
        case 'norma_tecnica':
            return '<span class="badge badge-secondary">Norma Técnica</span>';
        default:
            return '<span class="badge badge-light">' + tipo.charAt(0).toUpperCase() + tipo.slice(1) + '</span>';
    }
}

/**
 * Generar badge de estado
 */
function getEstadoBadge(estado) {
    switch (estado) {
        case 'vigente':
            return '<span class="badge badge-success">Vigente</span>';
        case 'derogada':
            return '<span class="badge badge-danger">Derogada</span>';
        case 'en_revision':
            return '<span class="badge badge-warning">En Revisión</span>';
        case 'proyecto':
            return '<span class="badge badge-info">Proyecto</span>';
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
 * Calcular días restantes hasta una fecha
 */
function calcularDiasRestantes(fechaVencimiento) {
    const hoy = new Date();
    const fechaVenc = new Date(fechaVencimiento);
    const diffTime = fechaVenc - hoy;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays;
} 