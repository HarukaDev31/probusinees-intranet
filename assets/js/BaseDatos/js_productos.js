

let productosTable;
let spinner;
$(document).ready(async function () {
    spinner = $(".backdrop");

    await getCampanas();
    initProductosTable();
    $('.dropdown-menu').on('click', function (event) {
        event.stopPropagation(); // Evita que el evento se propague
    });
    $("#aplicar-filtros").on("click", function () {
        productosTable.ajax.reload();
    });

    // Character counter for observations
    $('#observations').on('input', function () {
        const maxLength = 500;
        const currentLength = $(this).val().length;
        const remaining = maxLength - currentLength;

        $('#char-counter').text(`${currentLength}/${maxLength}`);

        if (remaining < 50) {
            $('#char-counter').removeClass('text-gray-500').addClass('text-amber-600');
        } else {
            $('#char-counter').removeClass('text-amber-600').addClass('text-gray-500');
        }
    });

    // Toggle observations section with switch
    $('#observations-toggle').on('change', function () {
        const isChecked = $(this).is(':checked');
        if (isChecked) {
            $('#observations-section').slideDown(400, 'swing');
            $('#toggle-icon').removeClass('text-gray-400').addClass('text-blue-600');
            // Change icon to eye-off when active
            $('#toggle-icon').html(`
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
          `);
        } else {
            $('#observations-section').slideUp(400, 'swing');
            $('#toggle-icon').removeClass('text-blue-600').addClass('text-gray-400');
            // Change icon back to eye
            $('#toggle-icon').html(`
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
          `);
        }
    });

    // Form validation with enhanced visual feedback
    function validateForm() {
        let isValid = true;
        const requiredFields = ['arancel-sunat', 'arancel-tlc', 'product-type', 'labeling'];

        requiredFields.forEach(field => {
            const element = $(`#${field}`);
            const value = element.val();

            if (!value || value.trim() === '') {
                element.removeClass('border-green-300 focus:border-green-500').addClass('border-red-300 bg-red-50 focus:border-red-500');
                element.parent().find('.error-message').remove();
                element.after('<p class="error-message text-red-500 text-xs mt-1 animate-pulse">Este campo es requerido</p>');
                isValid = false;
            } else {
                element.removeClass('border-red-300 bg-red-50 focus:border-red-500').addClass('border-green-300 focus:border-green-500');
                element.parent().find('.error-message').remove();
            }
        });

        return isValid;
    }
    function backToProducts() {
        $('#product-container').hide();
        $('#main-container').show();
    }
    // Enhanced save button with loading state
    $('#save-btn').on('click', function () {
        const $btn = $(this);

        if (validateForm()) {
            // Loading state
            $btn.prop('disabled', true);
            $btn.html(`
            <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Guardando...
          `);

            setTimeout(() => {
                $btn.removeClass('bg-blue-600 hover:bg-blue-700').addClass('bg-green-600 hover:bg-green-700');
                $btn.html(`
              <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
              </svg>
              ¡Guardado Exitosamente!
            `);
                showToast('Formulario guardado correctamente', 'success');

                setTimeout(() => {
                    $btn.prop('disabled', false);
                    $btn.removeClass('bg-green-600 hover:bg-green-700').addClass('bg-blue-600 hover:bg-blue-700');
                    $btn.html(`
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
                Guardar Cambios
              `);
                }, 2500);
            }, 1500);
        } else {
            showToast('Por favor complete todos los campos requeridos', 'error');
        }
    });

    // Enhanced toast notifications
    function showToast(message, type = 'info') {
        const icons = {
            success: `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
          </svg>`,
            error: `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
          </svg>`,
            info: `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
          </svg>`
        };

        const colors = {
            success: 'bg-gradient-to-r from-green-500 to-green-600',
            error: 'bg-gradient-to-r from-red-500 to-red-600',
            info: 'bg-gradient-to-r from-blue-500 to-blue-600'
        };

        const toast = $(`
          <div class="fixed top-4 right-4 ${colors[type]} text-white px-6 py-4 rounded-xl shadow-2xl z-50 transform translate-x-full transition-all duration-500 flex items-center space-x-3 max-w-sm">
            ${icons[type]}
            <span class="font-medium">${message}</span>
          </div>
        `);

        $('body').append(toast);
        setTimeout(() => toast.removeClass('translate-x-full'), 100);
        setTimeout(() => {
            toast.addClass('translate-x-full opacity-0');
            setTimeout(() => toast.remove(), 500);
        }, 4000);
    }

    // External link functionality with enhanced feedback
    $('#alibaba-link').on('click', function (e) {
        e.preventDefault();
        const $btn = $(this);
        $btn.addClass('scale-95 bg-orange-600');
        showToast('Abriendo Alibaba en nueva pestaña...', 'info');

        setTimeout(() => {
            $btn.removeClass('scale-95 bg-orange-600');
            window.open('https://www.alibaba.com/pe/', '_blank');
        }, 800);
    });

    // Add hover effects to form elements
    $('input, select, textarea').on('focus', function () {
        $(this).parent().addClass('transform scale-[1.02] transition-transform duration-200');
    }).on('blur', function () {
        $(this).parent().removeClass('transform scale-[1.02] transition-transform duration-200');
    });

    // Initialize character counter
    $('#observations').trigger('input');

    // Back button functionality
    $('#back-btn').on('click', function () {
        showToast('Regresando...', 'info');
        // Add your navigation logic here
    });

    // Product link functionality
    $('#product-link').on('click', function () {
        showToast('Abriendo enlace del producto...', 'info');
        // Add your product link logic here
    });

    // Cancel button functionality
    $('#cancel-btn').on('click', function () {
        if (confirm('¿Está seguro de que desea cancelar? Se perderán los cambios no guardados.')) {
            showToast('Operación cancelada', 'info');
            // Reset form or navigate away
        }
    });
    $('#back-btn-text').on('click', function () {
        backToProducts();
    });
});
/**
 * Inicializar DataTable de productos
 */
function initProductosTable() {
    // Destruir tabla existente si existe
    if ($.fn.DataTable.isDataTable('#productosTable')) {
        //reload table and return
        productosTable.ajax.reload();
        return;
    }

    productosTable = $('#productosTable').DataTable({
     
        ajax: {
            url: base_url + 'BaseDatos/ProductosController/getProductos',
            type: 'POST',
            data: function (d) {
                d.idContenedor = $('#filtroCampana').val();
                d.tipo = $('#filtroTipo').val();
            }
        },

        order: [[0, 'desc']],
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
        buttons: [

        ]
    });
}


/**
 * Limpiar filtros
 */
function limpiarFiltros() {
    $('#filtroCampana').val('');
    $('#filtroTipo').val('');
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
    spinner.show();
    $.ajax({
        url: base_url + 'BaseDatos/ProductosController/getProducto/' + id,
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                const producto = response.data;
                spinner.hide();
                $('#product-container').show();
                $('#main-container').hide();
                $("#alibaba-link-text").text(producto.link.length > 10 ? producto.link.substring(0, 10) + '...' : producto.link);
                $("#product-name-text").text(producto.nombre_comercial.length > 10 ? producto.nombre_comercial.substring(0, 10) + '...' : producto.nombre_comercial);
                $("#arancel-sunat").val(producto.arancel_sunat??0);
                $("#arancel-tlc").val(producto.arancel_tlc??0);
                $("#product-type").val(producto.tipo??'LIBRE');
                $("#labeling").val(producto.etiquetado??'NORMAL');
                $("#special-document").val(producto.doc_especial??'NO');
                $("#correlativo").val(producto.correlativo??'NO');
                $("#antidumping").val(producto.antidumping??0);
                $("#observations").val(producto.observaciones);
               
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function () {
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
                success: function (response) {
                    if (response.success) {
                        Swal.fire('Eliminado', response.message, 'success');
                        $('#productosTable').DataTable().ajax.reload();
                        cargarEstadisticas();
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function () {
                    Swal.fire('Error', 'Error al eliminar el producto', 'error');
                }
            });
        }
    });
}


/**
 * Formatear fecha
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES') + ' ' + date.toLocaleTimeString('es-ES');
}

const getCampanas = async () => {
    await $.ajax({
        url: base_url + 'BaseDatos/ProductosController/getCampanas',
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            if (response.status == "success") {
                const campanas = response.data;
                $('#filtroCampana').empty();
                $('#filtroCampana').append(`<option value="0">Todas</option>`);
                $('#filtroCampana').append(campanas.map(function (c) {
                    return `<option value="${c.id}">${c.carga}</option>`;
                }));
            }
        }
    });
}
