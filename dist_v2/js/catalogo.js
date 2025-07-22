
var spinner = null;
var productoFormSection = null
var productListSection = null
var currentProductId = null
var mainImage = null
var additionalImage1 = null
var additionalImage2 = null
var additionalVideo1 = null
var contactCardContainer = null
var currentPrivilege = localStorage.getItem("currentPrivilege") == null ? "" : localStorage.getItem("currentPrivilege");
var isInCompleted = false;
var isInTienda = false;
var checkedProducts = [];
var pricePEN = 0;
var priceUSD = 0;
const EXCHANGE_RATE = 3.8;
const YUAN_TO_USD = 6.8;
const ROLE_PERU = "CatalogoPeru";
const ROLE_CHINA = "CatalogoChina";

// Variables de paginación
var currentPage = 1;
var totalPages = 1;
var perPage = 50;
var totalRecords = 0;

    // Variables de filtros
    var currentFilters = {};
    
    // Variables de estado de selección
    var isSelectionMode = false;
// Function to format number as currency

$(document).ready(async function () {

    // Al inicio del $(document).ready
    $('#productGrid').off('change.selectProducts').on('change.selectProducts', '.checkbox', function () {
        // Actualiza checkedProducts con TODOS los checkboxes marcados (visibles o no)
        checkedProducts = $('#productGrid .card .checkbox:checked').map(function () {
            return $(this).data('product-id');
        }).get();
        updateSelectAllBtnText();
    });

    function mostrarBotones() {
        isSelectionMode = false; // Desactivar modo de selección
        $('#btnEnviarProductos').show();
        $('#btnCancelarEnvio').hide();
        $('#btnDeleteProducts').hide();
        $('#dynamicCategorizeBtnContainer').hide();
        $('#selectAll').hide();
    }

    $.ajax({
        url: base_url + 'CatalogoController/getCategorias', // Ajusta la ruta si es necesario
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            // response es un array de categorías
            var $select = $('#txt-ID_Categoria');
            $select.empty();
            $select.append('<option value="0" selected>Todos</option>');
            response.forEach(function(cat) {
                $select.append('<option value="' + cat.id + '">' + cat.name + '</option>');
            });
        }
    });

    // Cambiar imagen principal al hacer click en miniatura o video
    $('#detalleMiniatura1, #detalleMiniatura2, #detalleMiniatura3').on('click', function () {
        const src = $(this).attr('src');
        if (src) {
            // Si el principal es video, lo ocultamos
            $('#detalleVideoPrincipal').hide();
            $('#detalleImagenPrincipal').attr('src', src).show();
            // Resalta la miniatura activa
            $('#detalleMiniatura1, #detalleMiniatura2, #detalleMiniatura3, #detalleMiniaturaVideo1').removeClass('border-orange-400');
            $(this).addClass('border-orange-400');
        }
    });

    // Para el video
    $('#detalleMiniaturaVideo1').on('click', function () {
        const src = $(this).attr('src');
        if (src) {
            $('#detalleImagenPrincipal').hide();
            // Si no existe el video principal, lo creamos
            if ($('#detalleVideoPrincipal').length === 0) {
                $('<video id="detalleVideoPrincipal" class="w-80 h-80 object-contain rounded shadow" controls autoplay></video>')
                    .insertAfter('#detalleImagenPrincipal');
            }
            const $video = $('#detalleVideoPrincipal');
            $video.attr('src', src).show()[0].play();

            // Si hay error al cargar el video, ocultarlo
            $video.off('error').on('error', function () {
                $(this).hide();
                $('#detalleImagenPrincipal').show();
            });

            // Resalta la miniatura activa
            $('#detalleMiniatura1, #detalleMiniatura2, #detalleMiniatura3, #detalleMiniatura4, #detalleMiniaturaVideo1').removeClass('border-orange-400');
            $(this).addClass('border-orange-400');
        }
    });

    // Evita que el dropdown se cierre al hacer clic en inputs, selects o botones dentro del menú
    $('.dropdown-menu').on('click', function(e) {
        e.stopPropagation();
    });

    // Botón Categorizar/Enviar Producto dinámico
    let $categorizeBtn = $("#btnCategorizarDynamic");
    if ($categorizeBtn.length === 0) {
        $categorizeBtn = $('<button id="btnCategorizarDynamic" class="bg-orange-500 hover:bg-orange-600 text-white py-2 px-8 rounded-sm transition-colors flex items-center justify-center shadow-sm"></button>');
        $("#dynamicCategorizeBtnContainer").append($categorizeBtn);
    }
    function updateCategorizeButton() {
        if (isInTienda) {
            $categorizeBtn.show();
            $categorizeBtn.text("Categorizar");
        } else {
            $categorizeBtn.show();
            $categorizeBtn.text("Enviar Producto");
        }
    }
    productoFormSection = $('#productoFormSection')
    productListSection = $('#productListSection')
    spinner = $(".backdrop")

    showSkeletons();

    // Cargar estado de selección si existe
    loadSelectionState();
    
    // Simulate loading data (replace with actual API call)
    currentFilters.sort = $('#sortSelect').val();
    await loadProducts();

    // Setup event handlers
    setupEventHandlers();

    function showSkeletons() {
        const $grid = $('#productGrid');
        const $template = $('#skeletonTemplate');

        // Clear grid and add skeletons
        $grid.empty();
        for (let i = 0; i < 8; i++) {
            const $skeleton = $($template.html());
            $grid.append($skeleton);
        }
    }

    async function loadProducts(filters = null) {
        // Si no se pasan filtros, usar los filtros actuales
        if (!filters) {
            filters = currentFilters;
        } else {
            // Si se pasan filtros nuevos, actualizar currentFilters
            currentFilters = { ...filters };
        }
        
        // Resetear a página 1 si son filtros nuevos
        if (filters && !filters.page) {
            currentPage = 1;
        }

        if (window.location.href.includes("listarCompletados")) {
            isInCompleted = true;
            isInTienda = false;
            $("#section-title").text("Listado de Cotizados");
        } else if (window.location.href.includes("listarSeleccionados")) {
            isInTienda = true;
            isInCompleted = false;
            $("#section-title").text("Listado de Seleccionados").append('<span id="select-count" class="text-green-600 font-bold"></span>');
        } else {
            isInCompleted = false;
            isInTienda = false;
            $("#section-title").html('Listado de Nuevos <span id="nuevos-count" class="text-blue-600 font-bold"></span>');
        }
        updateCategorizeButton();

        url = base_url + 'CatalogoController/getCatalogo';
        if (isInCompleted) {
            url = base_url + 'CatalogoController/getCatalogoCompletados';
        } else if (isInTienda) {
            url = base_url + 'CatalogoController/getCatalogoTienda';
        }

        // Agregar parámetros de paginación
        filters.page = currentPage;
        filters.per_page = perPage;

        let response;
        response = await fetch(url, {
            method: 'POST',
            body: JSON.stringify(filters),
            headers: {
                'Content-Type': 'application/json'
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            if (data.status) {
                const products = data.data;
                renderProducts(products);
                updateSelectAllBtnText();
                
                // Actualizar variables de paginación
                totalRecords = data.total;
                totalPages = data.total_pages;
                currentPage = data.current_page;
                
                // Actualizar contadores
                if (!isInCompleted && !isInTienda) {
                    $('#nuevos-count').text(`(${data.total})`);
                    $('#select-count').text('');
                } else if (isInTienda) {
                    $('#select-count').text(`(${data.total})`);
                    $('#nuevos-count').text('');
                } else {
                    $('#nuevos-count').text('');
                    $('#select-count').text('');
                }
                
                // Actualizar controles de paginación
                updatePaginationControls();
                
                // Sincronizar controles con filtros actuales
                syncControlsWithFilters();
                
                // Actualizar apariencia del botón de limpiar
                updateClearButtonAppearance();
                
                // Verificar si necesitamos restaurar el modo de selección
                if (isSelectionMode) {
                    console.log('Verificando restauración de modo de selección después de cargar productos');
                    setTimeout(() => {
                        restoreSelectionMode();
                    }, 100);
                }
            } else {
                console.error('Error loading products:', data.message);
            }
            
            // Solo mostrar botones si no estamos en modo selección
            if (!isSelectionMode) {
                mostrarBotones();
            }
        } else {
            console.error('Network error:', response.statusText);
        }
    }

    // Función para alternar la visibilidad del dropdown
    function initializeProductDropdowns() {
        // Buscar todos los productos con el dropdown
        document.querySelectorAll('.dropdown-trigger').forEach(trigger => {
            // Limpiar handlers anteriores si existieran
            trigger.removeEventListener('click', toggleDropdown);
            trigger.removeEventListener('touchstart', handleTouchStart); // Nuevo para Safari móvil

            // Añadir nuevos event listeners
            trigger.addEventListener('click', toggleDropdown);
            trigger.addEventListener('touchstart', handleTouchStart); // Para Safari en iOS
        });

        // Cerrar todos los dropdowns cuando se hace clic/touch en cualquier parte
        document.addEventListener('click', function (event) {
            closeAllDropdowns(event);
        });

        // Manejar toques en iOS
        document.addEventListener('touchstart', function (event) {
            closeAllDropdowns(event);
        });

        document.querySelectorAll('.dropdown-trigger').forEach(trigger => {
            trigger.addEventListener('focusout', handleFocusOut);
            trigger.addEventListener('blur', handleFocusOut);
        });
    }

    // Nuevo: Manejar el primer toque en iOS
    function handleTouchStart(event) {
        // Prevenir el comportamiento por defecto para evitar problemas de doble toque
        event.preventDefault();
        toggleDropdown.call(this, event);
    }

    // Función para alternar la visibilidad del dropdown
    function toggleDropdown(event) {
        event.stopPropagation();
        const dropdownMenu = this.nextElementSibling;

        // Cerrar todos los otros dropdowns
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            if (menu !== dropdownMenu) {
                menu.classList.add('hidden');
            }
        });

        // Alternar el actual
        dropdownMenu.classList.toggle('hidden');
    }

    // Función para cerrar todos los dropdowns excepto el actual
    function closeAllDropdowns(event) {
        const isDropdownTrigger = event.target.closest('.dropdown-trigger');
        const isDropdownMenu = event.target.closest('.dropdown-menu');

        if (!isDropdownTrigger && !isDropdownMenu) {
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.classList.add('hidden');
            });
        }
    }
    // Función para manejar cuando el trigger pierde el foco
    function handleFocusOut(event) {
        const dropdownTrigger = event.target;
        const dropdownMenu = dropdownTrigger.nextElementSibling;

        // Pequeño retraso para permitir que el `click` se procese primero
        setTimeout(() => {
            // Verificar si el nuevo elemento con foco está dentro del dropdown
            const focusedElement = document.activeElement;
            const isFocusInsideDropdown = dropdownMenu.contains(focusedElement);

            if (!isFocusInsideDropdown) {
                dropdownMenu.classList.add('hidden');
            }
        }, 100);
    }


    function renderProducts(products) {
        const $grid = $('#productGrid');
        const $template = $('#productTemplate');
        const $noResults = $('#noResults');
        // Clear grid
        $grid.empty();

        // Add products with staggered animation
        products.forEach((product, index) => {
            const $product = $($template.html());
            const $checkbox = $product.find('.checkbox');
            //add badge in tienda 
            $product.attr('data-fecha', product.created_at);
            $product.find('.badge').text(product.status);
            switch (product.status) {
                case "COTIZADO":
                    $product.find('.badge').addClass('bg-blue-500 hidden    ');
                    break;
                case "PENDIENTE":
                    $product.find('.badge').addClass('bg-yellow-500');
                    break;
                case "EN TIENDA":
                    $product.find('.badge').addClass('bg-green-500');
                    break;
                default:
                    $product.find('.badge').addClass('bg-gray-500');
                    break;
            }
            // Set product data
            $product.find('img').attr({
                src: product.main_image_url,
                alt: product.name
            });
            $product.find('.nameProducto').text(product.nombre);
            $product.find('.precioChina').text(`RMB: ¥${product.precio}`);
            $product.find('.MOQ').text(`MOQ: ${product.moq}`);
            $product.find('.codProducto').text(`${product.cod_producto}`);
            let precioUSD = parseFloat(product.precio_usd);
            if (isNaN(precioUSD)) precioUSD = 0;
            $product.find('.precioUSD').text(`Precio USD: $ ${parseFloat(precioUSD).toFixed(2)}`);
            //ifproducts has category_name key set text-gray-800 
            // Obtener el precio mínimo de prices_range
            let minPrecio = 0;
            if (product.prices_range) {
                try {
                    const priceRangeObj = JSON.parse(product.prices_range);
                    if (Array.isArray(priceRangeObj) && priceRangeObj.length > 0) {
                        // Tomar el primer precio del array (cantidad mínima)
                        const cleanPrice = (priceRangeObj[0].price + '').replace(/,/g, '');
                        minPrecio = parseFloat(cleanPrice) || 0;
                    }
                } catch (e) {
                    minPrecio = 0;
                }
            }
            $product.attr('data-precio', minPrecio);
            $product.find('.precioPeru').text(`Precio: S/. ${minPrecio.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`);
            if (product.category_name) {
                $product.find('.text-gray-800').text(`${product.category_name}`);
            }
            if (currentPrivilege == ROLE_PERU) {
                $product.find('.MOQ').text(`Cantidad mínima: ${product.moq} uni.`);
                $product.find('.precioUSD').hide();
                $product.find('.precioChina').hide();
            }
            $product.find('.btnTienda').off('click');
            $product.find(".btnTienda").on("click", function (e) {
                e.preventDefault();
                const $card = $(this).closest('.card');
                const productId = $card.data('product-id');
                url = base_url + 'CatalogoController/pasarTienda';
                const formData = new FormData();
                formData.append('productId', productId);
                fetch(url, {
                    method: 'post',
                    body: formData,
                }).then(async response => {
                    if (response.ok) {
                        const data = await response.json();
                        if (data.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Éxito',
                                text: 'Producto pasado a tienda correctamente.',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            await loadProducts();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message,
                            });
                        }
                    } else {
                        console.error('Network error:', response.statusText);
                    }
                });
            });
            // Add to grid with staggered fade in animation
            $product.css('opacity', 0);
            //add product id to the card
            $product.attr('data-product-id', product.id);

            $grid.append($product);
            $checkbox.attr('data-product-id', product.id);
            if (checkedProducts.includes(product.id)) {
                $checkbox.prop('checked', true);
            } else {
                $checkbox.prop('checked', false);
            }
            $checkbox.on('change', function () {
                // Actualiza checkedProducts con TODOS los checkboxes marcados (visibles o no)
                checkedProducts = $('#productGrid .card .checkbox:checked').map(function () {
                    return $(this).data('product-id');
                }).get();
                console.log('Checked products:', checkedProducts);
                updateSelectAllBtnText();
                
                // Guardar estado en localStorage si estamos en modo selección
                if (isSelectionMode) {
                    saveSelectionState();
                }
            });
            initializeProductDropdowns();

            setTimeout(() => {
                $product.animate({ opacity: 1 }, 300);
            }, index * 100);

        });
        if (products.length === 0) {
            $grid.append($noResults);
            $noResults.show();
        }
        // Ordenar productos según el valor actual del select
        const sortValue = $('#sortSelect').val();
        sortProducts(sortValue);

        updateSelectAllBtnText();
        
        // Mantener el estado de selección si estamos en modo selección
        if (isSelectionMode) {
            // Usar setTimeout para asegurar que los elementos estén renderizados
            setTimeout(() => {
                restoreSelectionMode();
            }, 50);
        }
    }
    function renderProductDetails(product) {
        productoFormSection.show();
        productListSection.hide();
        currentProductId = product.id;

        const $form = $('#productForm');
        $form.find('#nombre').val(product.nombre);
        $form.find('#precio').val(product.precio);
        $form.find('#moq').val(product.moq);
        $form.find('#delivery').val(product.delivery);
        $form.find('#diasEntrega').val(product.dias_entrega);
        $form.find('#qtyXbox').val(product.qty_box);
        $form.find('#cbmXbox').val(product.cbm_box);
        $('#wechatPhone').val(product.whechat_phone);
        $form.find('#colores').val(product.colores);
        $form.find('#notas').val(product.notas);
        $form.find('#mainImageContainer').attr('data-file', product.main_image_url);
        $form.find('#additionalImage1Container').attr('data-file', product.additional_image_1_url);
        $form.find('#additionalImage2Container').attr('data-file', product.additional_image_2_url);
        $form.find('#additionalVideo1Container').attr('data-file', product.additional_video_1_url);
        $form.find('#contactCardContainer').attr('data-file', product.contact_card_url);
        mainImage = new FileUploader({
            containerId: 'mainImageContainer',
            acceptedTypes: "image/jpeg,image/png,image/gif,image/webp",// Tipos de archivos aceptados
            maxSize: 5 * 1024 * 1024,
            showAsModal: currentPrivilege == ROLE_PERU ? true : false
        });


        additionalImage1 = new FileUploader({
            containerId: 'additionalImage1Container',
            acceptedTypes: "image/jpeg,image/png,image/gif,image/webp",// Tipos de archivos aceptados
            maxSize: 5 * 1024 * 1024,
            showAsModal: currentPrivilege == ROLE_PERU ? true : false

        });
        additionalImage2 = new FileUploader({
            containerId: 'additionalImage2Container',
            acceptedTypes: "image/jpeg,image/png,image/gif,image/webp",// Tipos de archivos aceptados
            maxSize: 5 * 1024 * 1024,
            showAsModal: currentPrivilege == ROLE_PERU ? true : false

        });

        additionalVideo1 = new FileUploader({
            containerId: 'additionalVideo1Container',
            acceptedTypes: "video/mp4,video/webm,video/ogg,video/quicktime,.mov,.mp4",
            maxSize: 20 * 1024 * 1024,
            showAsModal: currentPrivilege == ROLE_PERU ? true : false

        });

        contactCardContainer = new FileUploader({
            containerId: 'contactCardContainer',
            acceptedTypes: "image/jpeg,image/png,image/gif,image/webp",// Tipos de archivos aceptados
            maxSize: 5 * 1024 * 1024,
            showPreview: false
        });
        if (product.main_image_url) {
            mainImage.loadFromURL(product.main_image_url);
        }
        if (product.aditional_image1_url) {
            additionalImage1.loadFromURL(product.aditional_image1_url);
        }
        if (product.aditional_image2_url) {
            additionalImage2.loadFromURL(product.aditional_image2_url);
        }
        if (product.aditional_video1_url) {
            additionalVideo1.loadFromURL(product.aditional_video1_url);
        }
        if (product.contact_card_url) {
            contactCardContainer.loadFromURL(product.contact_card_url);
        }
        if (currentPrivilege == ROLE_PERU) {
            const cbm = Number(product.cbm_box) * Number(product.moq) / Number(product.qty_box);
            $('#servicioImpo').val(product.servicio_impo ?? getServicioPerCbm(cbm));
            $('#arancel').val(product.arancel ?? "6.00");
            $('#igv').val(product.igv ?? 18);
            $('#antidumping').val(product.antidumping ?? 0.00);
            $('#percepcion').val(product.percepcion ?? 3.50);
        }
        if (product.status != "PENDIENTE") {
            $("#actionButtons").addClass("hidden");
            $("#actionButtons").removeClass("d-flex");
        }
        else {
            $("#actionButtons").removeClass("hidden");
            $("#actionButtons").addClass("d-flex");
        }

    }

    function setupEventHandlers() {
        // Search input handler
        $('#searchInput').on('input', debounce(function () {
            const query = String($("#searchInput").val() || '').toLowerCase();
            currentFilters.search = query;
            currentPage = 1; // Resetear a página 1 en búsqueda
            showSkeletons();
            loadProducts();
        }, 300));

        // Sort select handler
        $('#sortSelect').on('change',async function () {
            const sortValue = $(this).val();
            currentFilters.sort = sortValue;
            currentPage = 1; // Resetear a página 1 en ordenamiento
            showSkeletons();
            loadProducts();
        });

        // Botón Categorizar/Enviar Producto
        $(document).on('click', '#btnCategorizarDynamic', async function (e) {
            e.preventDefault();
            if (isInTienda) {
                // Categorizar productos seleccionados
                if (checkedProducts.length == 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Seleccione al menos un producto.',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    return;
                }
                // Mostrar modal para seleccionar categoría
                $('#modalConfirmacion').modal('show');
                await fillDropdownCategorias();
            } else {
                // Enviar productos (cambiar status a EN TIENDA)
                if (checkedProducts.length == 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Seleccione al menos un producto.',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    return;
                }
                // Confirmación antes de enviar
                Swal.fire({
                    title: '¿Está seguro de enviar los productos seleccionados?',
                    text: "Una vez enviados, no podrá deshacer esta acción.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Enviar',
                    cancelButtonText: 'Cancelar'
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        // Llamar endpoint para cambiar status
                        const formData = new FormData();
                        formData.append('productIds', JSON.stringify(checkedProducts));
                        const url = base_url + 'CatalogoController/pasarTienda';
                        const response = await fetch(url, {
                            method: 'POST',
                            body: formData,
                        });
                        if (response.ok) {
                            const data = await response.json();
                            mostrarBotones();
                            if (data.status) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Éxito',
                                    text: 'Productos enviados a tienda correctamente.',
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                                clearSelectionState();
                                await loadProducts();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: data.message || 'Error al enviar productos.',
                                });
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error de red al enviar productos.',
                            });
                        }
                    }
                });
            }
        });

        $("#btnGuardarCategoriaProductos").on("click", function (e) {
            e.preventDefault();
            const categoriaId = $("#categoriaProductos").val();
            if (categoriaId == "") {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Seleccione una categoría válida.',
                    showConfirmButton: false,
                    timer: 1500
                });
                return;
            }
            const formData = new FormData();
            formData.append('categoriaId', categoriaId);
            formData.append('productIds', JSON.stringify(checkedProducts));
            const url = base_url + 'CatalogoController/guardarCategoriaProductos';
            fetch(url, {
                method: 'POST',
                body: formData,
            }).then(async response => {
                if (response.ok) {
                    const data = await response.json();
                    clearSelectionState();
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: 'Categoría guardada correctamente.',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    $('#modalConfirmacion').modal('hide');
                    await loadProducts();

                } else {
                    console.error('Network error:', response.statusText);
                }
            });
        })
        // Filter button handler
        $('#filterBtn').on('click', async function (e) {
            e.preventDefault();
            // Obtener valores de los filtros
            let fechaInicio = $('#txt-Fe_Inicio_Carga').val();
            let fechaFin = $('#txt-Fe_Fin_Carga').val();
            let categoria = $('#txt-ID_Categoria').val();

            // Normalizar fechas: si están vacías, enviar null
            if (!fechaInicio || fechaInicio.trim() === '') fechaInicio = null;
            if (!fechaFin || fechaFin.trim() === '') fechaFin = null;
            if (!categoria || categoria === '0') categoria = null;

            // Normalizar fechas: si están vacías, enviar null
            currentFilters.fechaInicio = fechaInicio && fechaInicio.trim() !== '' ? fechaInicio : null;
            currentFilters.fechaFin = fechaFin && fechaFin.trim() !== '' ? fechaFin : null;
            currentFilters.categoria = categoria && categoria !== '0' ? categoria : null;

            currentPage = 1; // Resetear a página 1 en filtros
            showSkeletons();
            loadProducts();
        });

        // Botón cancelar filtros
        $('#cancelar-btn').on('click', function (e) {
            e.preventDefault();
            clearAllFilters();
        });

        // Botón limpiar filtros (nuevo botón)
        $('#clearFiltersBtn').on('click', function (e) {
            e.preventDefault();
            clearAllFilters();
        });

        // View toggle handlers
        $(document).on('click', '.view-btn', async function (e) {
            e.preventDefault();
            e.stopPropagation();
            $('#viewProductSection').show();
            $('#dynamicCategorizeBtnContainerView').show();
            $('#productListSection').hide();
            await showProductDetail($(this).data('product-id'));
            
        });
        

        // Edit button handler
        $(document).on('click', '.edit-btn', async function (e) {
            e.preventDefault();
            e.stopPropagation();
            $(".checkbox").hide();
            mostrarBotones();
            const $card = $(this).closest('.card');
            const productId = $card.data('product-id');
            // Redirect to edit page
            await loadProductDetails(productId);
        });

        // Delete button handler
        $(document).on('click', '.delete-btn', async function (e) {
            e.preventDefault();
            e.stopPropagation();
            const $card = $(this).closest('.card');
            const productId = $card.data('product-id');
            Swal.fire({
                title: '¿Está seguro de eliminar el producto?',
                text: "Una vez eliminado, no podrá deshacer esta acción.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Eliminar',
                cancelButtonText: 'Cancelar'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    // Delete product
                    await deleteProduct(productId);
                }
            });
        });
    }
    // Handler para Categorizar en vista detalle
    $(document).on('click', '#btnCategorizarDynamicView', async function (e) {
        e.preventDefault();
        $('#modalConfirmacion').modal('show');
        await fillDropdownCategorias();

        // Cuando se confirme la categoría, envía solo el producto actual
        $("#btnGuardarCategoriaProductos").off('click').on('click', async function (e) {
            e.preventDefault();
            const categoriaId = $("#categoriaProductos").val();
            if (categoriaId == "") {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Seleccione una categoría válida.',
                    showConfirmButton: false,
                    timer: 1500
                });
                return;
            }
            const formData = new FormData();
            formData.append('categoriaId', categoriaId);
            formData.append('productIds', JSON.stringify([currentProductId]));
            const url = base_url + 'CatalogoController/guardarCategoriaProductos';
            const response = await fetch(url, {
                method: 'POST',
                body: formData,
            });
            if (response.ok) {
                const data = await response.json();
                Swal.fire({
                    icon: data.status ? 'success' : 'error',
                    title: data.status ? 'Éxito' : 'Error',
                    text: data.message || (data.status ? 'Categoría guardada correctamente.' : 'Error al guardar categoría.'),
                    showConfirmButton: false,
                    timer: 1500
                });
                if (data.status) {
                    $('#modalConfirmacion').modal('hide');
                    await loadProducts();
                    $('#viewProductSection').hide();
                    $('#productListSection').show();
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error de red al guardar categoría.',
                });
            }
        });
    });

    function resetDetallePrincipal() {
        $('#detalleVideoPrincipal').hide().attr('src', '');
        $('#detalleImagenPrincipal').show();
    }

    // Handler para Enviar Producto en vista detalle
    $(document).on('click', '#btnEnviarDynamicView', async function (e) {
        e.preventDefault();
        // Confirmación
        Swal.fire({
            title: '¿Está seguro de enviar el producto?',
            text: "Una vez enviado, no podrá deshacer esta acción.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Enviar',
            cancelButtonText: 'Cancelar'
        }).then(async (result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('productIds', JSON.stringify([currentProductId]));
                const url = base_url + 'CatalogoController/pasarTienda';
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData,
                });
                if (response.ok) {
                    const data = await response.json();
                    if (data.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: 'Producto enviado a tienda correctamente.',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        await loadProducts();
                        $('#viewProductSection').hide();
                        $('#productListSection').show();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Error al enviar producto.',
                        });
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error de red al enviar producto.',
                    });
                }
            }
        });
    });

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    function filterProducts(query) {
        // Esta función ahora solo se usa para filtrado local
        // El filtrado principal se hace en el servidor con paginación
        const $grid = $('#productGrid');
        const $cards = $grid.find('.card');
        
        // Filter cards based on query
        $cards.each(function () {
            const $card = $(this);
            const name = $card.find('.nameProducto').text().toLowerCase();
            if (name.includes(query)) {
                $card.show();
            } else {
                $card.hide();
            }
        });
        
        // Show or hide "No results" message
        const $noResults = $('#noResults');
        if ($grid.find('.card:visible').length === 0) {
            $noResults.show();
        } else {
            $noResults.hide();
        }
        
        // Reset scroll position
        $grid.scrollTop(0);

        // Actualizar contador de productos visibles
        const visibles = $grid.find('.card:visible').length;
        if (!isInCompleted && !isInTienda) {
            $('#nuevos-count').text(`(${visibles})`);
            $('#select-count').text('');
        } else if (isInTienda) {
            $('#select-count').text(`(${visibles})`);
            $('#nuevos-count').text('');
        } else {
            $('#nuevos-count').text('');
            $('#select-count').text('');
        }
    }

    function sortProducts(criteria) {
        const $grid = $('#productGrid');
        const $cards = $grid.find('.card');

        const sortedCards = $cards.sort((a, b) => {
            // Por nombre
            if (criteria === 'nameAZ' || criteria === 'nameZA') {
                const aName = $(a).find('.nameProducto').text().toLowerCase();
                const bName = $(b).find('.nameProducto').text().toLowerCase();
                if (criteria === 'nameAZ') {
                    return aName.localeCompare(bName);
                } else {
                    return bName.localeCompare(aName);
                }
            }
            // Por precio
            if (criteria === 'pricemin' || criteria === 'pricemax') {
                const aPrice = parseFloat($(a).data('precio')) || 0;
                const bPrice = parseFloat($(b).data('precio')) || 0;
                if (criteria === 'pricemin') {
                    return aPrice - bPrice;
                } else {
                    return bPrice - aPrice;
                }
            }
            // Por fecha (más reciente primero)
            if (criteria === 'recent') {
                // Asegúrate de que cada .card tenga data-fecha con la fecha en formato ISO o timestamp
                const aDate = new Date($(a).data('fecha') || 0);
                const bDate = new Date($(b).data('fecha') || 0);
                return bDate - aDate;
            }
            return 0;
        });

        $grid.empty().append(sortedCards);
    }
    async function loadProductDetails(productId) {
        url = base_url + 'CatalogoController/getProductDetails/' + productId;
        const response = await fetch(url);
        if (response.ok) {
            const data = await response.json();
            if (data.status) {
                const product = data.data;

                renderProductDetails(product);

                calculateValues();
            } else {
                console.error('Error loading product details:', data.message);
            }
        } else {
            console.error('Network error:', response.statusText);
        }
    }
    async function deleteProduct(productId) {
        url = base_url + 'CatalogoController/deleteProduct';
        const formData = new FormData();
        formData.append('productId', productId);
        const response = await fetch(url, {
            method: 'post',
            body: formData,
        });
        if (response.ok) {
            const data = await response.json();
            if (data.status) {
                // Reload products after deletion
                await loadProducts();
            } else {
                console.error('Error deleting product:', data.message);
            }
        } else {
            console.error('Network error:', response.statusText);
        }
    }
    async function sendCotizacion() {
        //show swall confirmation
        Swal.fire({
            title: '¿Está seguro de enviar la cotización?',
            text: "Una vez enviada, no podrá deshacer esta acción.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Enviar',
            cancelButtonText: 'Cancelar'
        }).then(async (result) => {
            if (result.isConfirmed) {
                //send cotizacion
                validateForm();
                const formData = new FormData();
                formData.append('productId', currentProductId);
                const response = await fetch(base_url + 'CatalogoController/sendCotizacion', {
                    method: 'POST',
                    body: formData,
                });
                if (response.ok) {
                    productoFormSection.hide();
                    productListSection.show();
                    await loadProducts();
                } else {
                    Swal.fire(
                        'Error!',
                        'Error al enviar la cotización.',
                        'error'
                    )
                }
            }
        })
    }
    $('#selectAll').on('click', function (e) {
        e.preventDefault();
        const $checkboxes = $('#productGrid .card:visible .checkbox');
        const checkedCount = $checkboxes.filter(':checked').length;

        if (checkedCount > 0) {
            // Deseleccionar todos los visibles
            $checkboxes.prop('checked', false);
            checkedProducts = checkedProducts.filter(id =>
                $checkboxes.filter(`[data-product-id="${id}"]`).length === 0
            );
        } else {
            // Seleccionar todos los visibles
            $checkboxes.prop('checked', true);
            $checkboxes.each(function () {
                const id = $(this).data('product-id');
                if (!checkedProducts.includes(id)) {
                    checkedProducts.push(id);
                }
            });
        }
        updateSelectAllBtnText(); // <-- Asegura que el texto se actualice siempre
    });

    // Cambia el texto del botón al cargar productos o filtrar
    function updateSelectAllBtnText() {
        const $checkboxes = $('#productGrid .card:visible .checkbox');
        const checkedCount = $checkboxes.filter(':checked').length;
        if (checkedCount === 0) {
            $('#selectAll').text('Seleccionar a todos');
        } else {
            $('#selectAll').text('Deseleccionar a todos');
        }
    }

    $('#btnEnviarProductos').on('click', function (e) {
        e.preventDefault();
        activateSelectionMode();
    });
    $('#btnCancelarEnvio').on('click', function (e) {
        e.preventDefault();
        deactivateSelectionMode();
    });
    // on show modalNuevaCategoria 
    $('#modalNuevaCategoria').on('show.bs.modal', function (e) {

        $("#nuevaCategoria").val("");
    });
    $("#btnCrearCategoria").on("click", async function (e) {
        e.preventDefault();
        const categoria = $("#nuevaCategoria").val();
        if (categoria.trim() == "") {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ingrese una categoría válida.',
                showConfirmButton: false,
                timer: 1500
            });
            return;
        }
        const formData = new FormData();
        formData.append('categoria', categoria);
        const response = await fetch(base_url + 'CatalogoController/createCategoria', {
            method: 'POST',
            body: formData,
        });
        if (response.ok) {
            const data = await response.json();
            if (data.status) {
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: 'Categoría creada correctamente.',
                    showConfirmButton: false,
                    timer: 1500
                });
                $('#modalNuevaCategoria').modal('hide');
                await fillDropdownCategorias();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        } else {
            console.error('Network error:', response.statusText);
        }
    })

    $('#btnConfirmarEnvio').on('click', async function (e) {
        e.preventDefault();
        if (checkedProducts.length == 0) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Seleccione al menos un producto.',
                showConfirmButton: false,
                timer: 1500
            });
            return;
        }
        //show modalConfirmacion
        $('#modalConfirmacion').modal('show');
        await fillDropdownCategorias();
    })

    $("#btnSave").on("click", function (e) {
        e.preventDefault();
        validateForm();
    });
    $("#btnCotizar").on("click", function (e) {
        e.preventDefault();
        sendCotizacion();
    });
    $('#btnBackView').on('click', function (e) {
        e.preventDefault();
        resetDetallePrincipal();
        $('#viewProductSection').hide();
        $('#productListSection').show();
    });

    $('#btnBackForm').on('click', function (e) {
        e.preventDefault();
        resetDetallePrincipal();
        productoFormSection.hide();
        console.log('hide productoFormSection');
        $('#productListSection').show();
    });
    $("#listViewBtn").on("click", function (e) {
        e.preventDefault();
        //change productsGrid to flex column
        const $grid = $('#productGrid');
        $grid.removeClass('grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-4 gap-5 w-full');
        $grid.addClass('flex flex-col gap-5 items-center grid-cols-2');
        $grid.find('.card').removeClass('w-full').addClass('w-50');
        $grid.find('.card-img').removeClass('w-full').addClass('w-[15rem] h-[15rem]');
        $grid.find('.card-content').removeClass('flex-col justify-between').addClass('flex-row justify-center');

    })
    $("#gridViewBtn").on("click", function (e) {
        e.preventDefault();
        //change productsGrid to grid
        const $grid = $('#productGrid');
        $grid.removeClass('flex flex-col gap-5 items-center');
        $grid.addClass('grid  gap-5 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-4 w-full');
        $grid.find('.card').removeClass('w-50').addClass('w-full');
        $grid.find('.card-img').removeClass('w-[15rem] h-[15rem]').addClass('w-full');
        $grid.find('.card-content').removeClass('flex-row justify-center').addClass('flex-col justify-between');
    })
    $("#btnDelete").on("click", function (e) {
        e.preventDefault();
        Swal.fire({
            title: '¿Está seguro de eliminar el producto?',
            text: "Una vez eliminado, no podrá deshacer esta acción.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Eliminar',
            cancelButtonText: 'Cancelar'
        }).then(async (result) => {
            if (result.isConfirmed) {
                await deleteProduct(currentProductId);
                Swal.fire({
                    icon: 'success',
                    title: 'Eliminado',
                    text: 'Producto eliminado correctamente.',
                    showConfirmButton: false,
                    timer: 1500
                });
                $('#viewProductSection').hide();
                $('#productListSection').show();
                await loadProducts();
            }
        });
    });

    $("#btnDeleteProducts").on("click", async function (e) {
        e.preventDefault();
        if (checkedProducts.length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Seleccione al menos un producto para eliminar.',
                showConfirmButton: false,
                timer: 1500
            });
            return;
        }
        Swal.fire({
            title: '¿Está seguro de eliminar los productos seleccionados?',
            text: "Una vez eliminados, no podrá deshacer esta acción.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Eliminar',
            cancelButtonText: 'Cancelar'
        }).then(async (result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('productIds', JSON.stringify(checkedProducts));
                const url = base_url + 'CatalogoController/deleteMultipleProducts';
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData,
                });
                if (response.ok) {
                    const data = await response.json();
                    mostrarBotones();
                    $(".checkbox").hide();
                    if (data.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: 'Productos eliminados correctamente.',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        clearSelectionState();
                        await loadProducts();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Error al eliminar productos.',
                        });
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error de red al eliminar productos.',
                    });
                }
            }
        });
    });
    $("#btnAddProduct").on("click", function (e) {
        e.preventDefault();
        productoFormSection.show();
        productListSection.hide();
        currentProductId = null;
        const $form = $('#productForm');
        $form[0].reset();
        $('#wechatPhone').val('');
        $form.find('.form-error').addClass('hidden');
        $form.find('input, textarea').removeClass('border-red-500');
        $form.find('#mainImageContainer').attr('data-file', '');
        $form.find('#additionalImage1Container').attr('data-file', '');
        $form.find('#additionalImage2Container').attr('data-file', '');
        $form.find('#additionalVideo1Container').attr('data-file', '');
        $form.find('#contactCardContainer').attr('data-file', '');
        mainImage = new FileUploader({
            containerId: 'mainImageContainer',
            acceptedTypes: "image/jpeg,image/png,image/gif,image/webp",// Tipos de archivos aceptados
            maxSize: 5 * 1024 * 1024
        });
        additionalImage1 = new FileUploader({
            containerId: 'additionalImage1Container',
            acceptedTypes: "image/jpeg,image/png,image/gif,image/webp",// Tipos de archivos aceptados
            maxSize: 5 * 1024 * 1024
        });
        additionalImage2 = new FileUploader({
            containerId: 'additionalImage2Container',
            acceptedTypes: "image/jpeg,image/png,image/gif,image/webp",// Tipos de archivos aceptados
            maxSize: 5 * 1024 * 1024
        });
        additionalVideo1 = new FileUploader({
            containerId: 'additionalVideo1Container',
            acceptedTypes: "video/mp4,video/webm,video/ogg,video/quicktime,.mov,.mp4",
            maxSize: 20 * 1024 * 1024
        });
        contactCardContainer = new FileUploader({
            containerId: 'contactCardContainer',
            showPreview: false,
            acceptedTypes: "image/jpeg,image/png,image/gif,image/webp",// Tipos de archivos aceptados
            maxSize: 5 * 1024 * 1024
        });
        ;
    })

    // Validar campos al perder el foco
    const formInputs = document.querySelectorAll('#productForm input, #productForm textarea');
    formInputs.forEach(input => {
        input.addEventListener('blur', function () {
            validateField(this);
        });
    });

    // Formatear campos de precio al perder el foco
    const priceFields = document.querySelectorAll('#precio, #delivery');
    priceFields.forEach(field => {
        field.addEventListener('blur', function () {
            if (this.value && !isNaN(parseFloat(this.value))) {
                this.value = parseFloat(this.value).toFixed(2);
            }
        });
    });

    async function showProductDetail(productId) {

        mostrarSkeletonProducto();
        const response = await fetch(`${base_url}CatalogoController/getProductDetails/${productId}`);

        if (!response.ok) {
            alert('No se pudo cargar el detalle del producto');
            return;
        }
        const data = await response.json();
        if (!data.status) {
            alert('No se encontró el producto');
            return;
        }
        // Botón dinámico según pestaña
        let btnHtml = '';
        if (isInTienda) {
            btnHtml = `<button id="btnCategorizarDynamicView" class="bg-orange-500 hover:bg-orange-600 text-white py-2 px-8 rounded-sm transition-colors flex items-center justify-center shadow-sm">Categorizar</button>`;
        } else {
            btnHtml = `<button id="btnEnviarDynamicView" class="bg-orange-500 hover:bg-orange-600 text-white py-2 px-8 rounded-sm transition-colors flex items-center justify-center shadow-sm">Enviar Producto</button>`;
        }
        $('#dynamicCategorizeBtnContainerView').html(btnHtml);

        ocultarSkeletonProducto();
        const producto = data.data;
        currentProductId = producto.id;

        // Imágenes
        if (producto.aditional_image1_url) {
            $('#detalleMiniatura1').attr('src', producto.aditional_image1_url).show();
        } else {
            $('#detalleMiniatura1').hide();
        }
        if (producto.aditional_image2_url) {
            $('#detalleMiniatura2').attr('src', producto.aditional_image2_url).show();
        } else {
            $('#detalleMiniatura2').hide();
        }
        if (producto.main_image_url) {
            $('#detalleMiniatura3').attr('src', producto.main_image_url).show();
            $('#detalleImagenPrincipal').attr('src', producto.main_image_url).show();
        } else {
            $('#detalleMiniatura3').hide();
            $('#detalleImagenPrincipal').hide();
        }
        if (producto.aditional_video1_url) {
            $('#detalleMiniaturaVideo1').attr('src', producto.aditional_video1_url).show();
        } else {
            $('#detalleMiniaturaVideo1').hide();
        }

        // Nombre
        $('.font-bold.text-lg.mb-1').text(producto.nombre);

        // Renderizar price_range (cantidades y precios)
        let priceRangeHtml = '';
        if (producto.prices_range) {
            try {
                const priceRangeObj = JSON.parse(producto.prices_range);
                if (Array.isArray(priceRangeObj)) {
                    priceRangeHtml = '<div class="mb-2 font-semibold">Cantidades</div>';
                    priceRangeHtml += '<div class="flex flex-row gap-8">';
                    priceRangeObj.forEach(obj => {
                        // Si el objeto tiene 'quantity' y 'price'
                        const cantidad = obj.quantity || '';
                        const precio = obj.price || '';
                        priceRangeHtml += `
                            <div class="text-center">
                                <div class="text-xs text-gray-500 mb-1">${cantidad}</div>
                                <div class="font-bold text-lg">s/${precio}</div>
                            </div>
                        `;
                    });
                    priceRangeHtml += '</div>';
                } else {
                    priceRangeHtml = '<table class="min-w-full text-sm"><tbody>';
                    for (const [key, value] of Object.entries(priceRangeObj)) {
                        priceRangeHtml += `<tr>
                            <td class="font-semibold pr-2 py-1 text-gray-600">${key}</td>
                            <td class="py-1">${value}</td>
                        </tr>`;
                    }
                    priceRangeHtml += '</tbody></table>';
                }
            } catch (e) {
                priceRangeHtml = '<div class="text-red-500">Error al mostrar los rangos de precio</div>';
            }
        }
        $('#contenedorPriceRange').html(priceRangeHtml);
        

        // Botones de links
        let linksHtml = '';
        if (producto.url_tienda && producto.url_tienda.trim() !== '') {
            linksHtml += `<a href="${producto.url_tienda}" target="_blank" class="border px-4 py-2 rounded text-sm hover:bg-gray-50 mr-2">Link del producto</a>`;
        }
        if (producto.url_alibaba && producto.url_alibaba.trim() !== '') {
            linksHtml += `<a href="${producto.url_alibaba}" target="_blank" class="border px-4 py-2 rounded text-sm hover:bg-gray-50">Link Alibaba</a>`;
        }
        // Inserta los links en el contenedor correspondiente
        $('#viewProductSection .flex.gap-2.mt-2').html(linksHtml);

        // Renderizar attributes (atributos clave)
        let attributesHtml = '';
        if (producto.attributes) {
            try {
                const attributesObj = JSON.parse(producto.attributes);
                const entries = Object.entries(attributesObj);
                const columns = 2; // Cambia este valor para más o menos columnas
                attributesHtml = '<div class="mb-2 font-semibold">Atributos clave</div>';
                attributesHtml += '<table class="min-w-full text-sm bg-gray-50 rounded"><tbody>';
                for (let i = 0; i < entries.length; i += columns) {
                    attributesHtml += '<tr>';
                    for (let j = 0; j < columns; j++) {
                        const entry = entries[i + j];
                        if (entry) {
                            attributesHtml += `
                                <td class="border px-3 py-2 font-semibold text-gray-700 bg-white">${entry[0]}</td>
                                <td class="border px-3 py-2 bg-white">${entry[1]}</td>
                            `;
                        } else {
                            // Si faltan celdas para completar la fila
                            attributesHtml += '<td class="border px-3 py-2 bg-white"></td><td class="border px-3 py-2 bg-white"></td>';
                        }
                    }
                    attributesHtml += '</tr>';
                }
                attributesHtml += '</tbody></table>';
            } catch (e) {
                attributesHtml = '<div class="text-red-500">Error al mostrar los atributos</div>';
            }
        }
        $('#contenedorAttributes').html(attributesHtml);

        // Renderizar product_details (detalle extendido)
        let productDetailsHtml = '';
        if (producto.product_details) {
            try {
                // Quitar comillas iniciales/finales si existen
                let html = producto.product_details;
                if (html.startsWith('"') && html.endsWith('"')) {
                    html = html.slice(1, -1);
                }
                // Reemplazar barras invertidas dobles por una sola
                html = html.replace(/\\([\s\S])/g, '$1');
                // Decodificar unicode tipo \u00f3 y también u00f3
                html = html.replace(/\\?u([\dA-Fa-f]{4})/g, function (match, grp) {
                    return String.fromCharCode(parseInt(grp, 16));
                });
                productDetailsHtml = html;
            } catch (e) {
                productDetailsHtml = '<div class="text-red-500">Error al mostrar el detalle</div>';
            }
        } else {
            productDetailsHtml = 'No hay detalles';
        }
        $('#contenedorProductDetails').html(productDetailsHtml);
        $('#contenedorProductDetails img').addClass('mx-auto my-8 max-w-xl w-full rounded-xl bg-white shadow');
        $('#contenedorProductDetails').addClass('bg-[#f4f8fc] p-8 rounded-xl');



    }
    async function fillDropdownCategorias() {
        const url = base_url + 'CatalogoController/getCategorias';
        const response = await fetch(url);
        if (response.ok) {
            const data = await response.json();
            console.log(data, "data")
            const categorias = data;
            const $dropdown = $('#categoriaProductos');
            $dropdown.empty();
            categorias.forEach(categoria => {
                $dropdown.append(new Option(categoria.name, categoria.id));
            });

        } else {
            console.error('Network error:', response.statusText);
        }
    }
    async function validateForm() {
        let isValid = true;
        const requiredFields = document.querySelectorAll('#productForm [required]');
        const requiredFieldsProvider = document.querySelectorAll('#providerForm [required]');
        //union of both arrays
        const allRequiredFields = [...requiredFields, ...requiredFieldsProvider];
        // Validar cada campo requerido
        allRequiredFields.forEach(field => {
            console.log(field)
            if (!validateField(field)) {
                isValid = false;
            }
        });

        // Si el formulario es válido, proceder con el envío o procesamiento
        if (isValid) {
            const formData = new FormData($('#productForm')[0]);
            //append wechatPhone.trim() if wechatPhone.trim() !== '
            const wechatPhone = $('#wechatPhone').val();
            formData.append('wechatPhone', wechatPhone.trim());
            formData.append('contactCard', contactCardContainer.getFile());
            formData.append('mainImage', mainImage.getFile());
            formData.append('additionalImage1', additionalImage1.getFile());
            formData.append('additionalImage2', additionalImage2.getFile());
            formData.append('additionalVideo1', additionalVideo1.getFile());
            formData.append('servicioImpo', $('#servicioImpo').val());
            formData.append('arancel', $('#arancel').val());
            formData.append('igv', $('#igv').val());
            formData.append('antidumping', $('#antidumping').val());
            formData.append('percepcion', $('#percepcion').val());
            //append precio_peru and precio_usd
            formData.append('precio_peru', pricePEN);
            formData.append('precio_usd', priceUSD);
            if (currentProductId) {
                formData.append('productId', currentProductId);
            }
            // Enviar el formulario usando fetch
            spinner.show();
            url = base_url + 'CatalogoController/saveProduct';
            await fetch(url, {
                method: 'POST',
                body: formData,
            })
                .then(async response => {
                    console.log(response)
                    if (response.ok) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: 'Producto guardado correctamente.',
                            showConfirmButton: false,
                            timer: 1500
                        });


                        spinner.hide();
                        return response.json();

                    } else {
                        throw new Error('Error en la respuesta del servidor');
                    }
                    spinner.hide();
                })
                .then(async data => {
                    console.log(data)
                    if (data.status) {
                        // Mostrar mensaje de éxito
                        // Volver a cargar la lista de productos
                        await loadProducts();
                        // Volver a la sección de lista de productos
                        productoFormSection.hide();
                        productListSection.show();
                    } else {
                    }
                    spinner.hide();
                })
                .catch(error => {
                    console.error('Error:', error);
                });

        } else {
            console.log('Formulario inválido, corrige los errores');
        }

        return isValid;
    }

    // Función para validar un campo específico
    function validateField(field) {
        // Obtener el mensaje de error relacionado con este campo
        const errorElement = field.parentElement.querySelector('.form-error') || field.parentElement.parentElement.querySelector('.form-error');

        // Validación básica de campo requerido vacío
        if (field.hasAttribute('required')
            && field.value.trim() == ""
        ) {
            showError(field, errorElement, 'Este campo es obligatorio.');
            return false;
        }
        // Validaciones específicas por tipo o ID del campo
        switch (field.id) {
            case 'wechatPhone':
                // Validar formato de número de teléfono (ejemplo: solo dígitos y longitud de 10-15)
                if (field.value.trim() === '') {
                    showError(field, errorElement, 'Ingrese un número de teléfono válido');
                    return false;
                }
                break;

            case 'precio':
                if (!/^\d+(\.\d{1,2})?$/.test(field.value.trim()) && field.value.trim() !== '') {
                    showError(field, errorElement, 'Ingrese solo valores numéricos con hasta 2 decimales.');
                    return false;
                }
                break;

            case 'delivery':
                // Validar formato de precio
                if (!/^\d+(\.\d{1,2})?$/.test(field.value.trim()) && field.value.trim() !== '') {
                    showError(field, errorElement, 'Ingrese solo valores numéricos con hasta 2 decimales.');
                    return false;
                }
                break;

            case 'moq':
            case 'qtyXbox':
            case 'diasEntrega':
                // Validar que sea un número entero positivo
                if (parseInt(field.value) <= 0 || isNaN(parseInt(field.value))) {
                    showError(field, errorElement, 'Ingrese un valor numérico entero positivo.');
                    return false;
                }
                break;

            case 'cbmXbox':
                // Validar que sea un número positivo (puede tener decimales)
                if (parseFloat(field.value) < 0 || isNaN(parseFloat(field.value))) {
                    showError(field, errorElement, 'Ingrese un valor numérico positivo.');
                    return false;
                }
                break;
        }

        // Si llegamos aquí, el campo es válido
        hideError(field, errorElement);
        return true;
    }

    // Función para mostrar un mensaje de error
    function showError(field, errorElement, message) {
        console.log(field, errorElement, message)
        // Añadir clase de error al campo
        field.classList.add('border-red-500');
        field.classList.remove('border-gray-300');
        field.classList.add('border-2');

        // Mostrar mensaje de error si existe el elemento
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.classList.remove('hidden');
        }
    }

    // Función para ocultar un mensaje de error
    function hideError(field, errorElement) {
        // Quitar clase de error del campo
        field.classList.remove('border-red-500');

        // Ocultar mensaje de error si existe el elemento
        if (errorElement) {
            errorElement.classList.add('hidden');
        }
    }
    function formatCurrency(number, currency = '$') {
        return currency + '' + parseFloat(number).toFixed(2);
    }

    function calculateValues() {
        // 1. Obtenemos todos los valores de entrada
        const inputValues = getInputValues();

        // 2. Calculamos valores derivados básicos
        const derivedValues = calculateDerivedValues(inputValues);

        // 3. Calculamos valores base imponible
        const baseImponibleValues = calculateBaseImponible(derivedValues);

        // 4. Calculamos impuestos
        const impuestosValues = calculateImpuestos(baseImponibleValues, inputValues);

        // 5. Calculamos percepción
        const percepcionValue = calculatePercepcion(baseImponibleValues, impuestosValues, inputValues);

        // 6. Calculamos totales
        const totalValues = calculateTotals(
            derivedValues,
            baseImponibleValues,
            impuestosValues,
            percepcionValue,
            inputValues
        );

        // 7. Calculamos costos unitarios
        const unitCosts = calculateUnitCosts(totalValues, inputValues.moq);
        priceUSD = unitCosts.costoUnitarioUSDValue;
        pricePEN = unitCosts.costoUnitarioPENValue;
        // 8. Actualizamos la UI con todos los valores calculados
        updateUI(
            derivedValues,
            baseImponibleValues,
            impuestosValues,
            percepcionValue,
            totalValues,
            unitCosts,
            inputValues
        );
    }

    /**
     * Obtiene todos los valores de entrada del formulario
     */
    function getInputValues() {
        const precioYuanes = parseFloat($('#precio').val()) || 0;
        const moq = parseInt($('#moq').val()) || 0;
        const arancelRate = parseFloat($('#arancel').val()) / 100;
        const igvRate = parseFloat($('#igv').val());
        const antidumpingValue = parseFloat($('#antidumping').val()) || 0;
        const percepcionRate = parseFloat($('#percepcion').val()) / 100;
        const servicioImpoValue = parseFloat($('#servicioImpo').val()) || 0;
        const qtyXbox = parseInt($('#qtyXbox').val()) || 0;
        const cbmXbox = parseFloat($('#cbmXbox').val()) || 0;
        const profit = parseFloat($('#profit').val()) || 0;
        const delivery = parseInt($('#delivery').val()) || 0;
        return {
            precioYuanes,
            moq,
            arancelRate,
            igvRate,
            antidumpingValue,
            percepcionRate,
            servicioImpoValue,
            qtyXbox,
            cbmXbox,
            profit,
            delivery,

        };
    }

    /**
     * Calcula valores derivados de los inputs
     */
    function calculateDerivedValues(inputs) {
        const precioUSD = ((inputs.precioYuanes + inputs.profit + (inputs.delivery / inputs.moq)) / YUAN_TO_USD)
        // Calcula total USD como MOQ * precio USD
        const totalUSDValue = inputs.moq * Number(precioUSD.toFixed(2));

        // Calcula CBM total según fórmula MOQ / cantidad por caja * CBM por caja
        const totalCBMValue = inputs.moq / inputs.qtyXbox * inputs.cbmXbox;

        return {
            precioUSD,
            totalUSDValue,
            totalCBMValue
        };
    }

    /**
     * Calcula valores de la base imponible
     */
    function calculateBaseImponible(derived) {
        const valorCargaValue = derived.totalUSDValue;
        const servicioImpoValue = $('#servicioImpo').val() || 0;
        const fleteValue = servicioImpoValue * 0.7; // Servicio de impo * 0.6
        const seguroValue = 100;
        const valorCIFValue = valorCargaValue + fleteValue + seguroValue;

        return {
            valorCargaValue,
            fleteValue,
            seguroValue,
            valorCIFValue
        };
    }

    /**
     * Calcula los diferentes impuestos
     */
    function calculateImpuestos(baseImponible, inputs) {
        const arancelValue = baseImponible.valorCIFValue * inputs.arancelRate;
        const baseIGV = baseImponible.valorCIFValue + arancelValue;
        const igvValue = baseIGV * inputs.igvRate / 100;
        const igvTotal = 0.16 * (baseImponible.valorCIFValue + arancelValue);
        const ipmTotal = 0.02 * (arancelValue + baseImponible.valorCIFValue);
        const antidumpingTotal = inputs.antidumpingValue * inputs.moq;
        const percepcionValue = (baseImponible.valorCIFValue + arancelValue + igvTotal + ipmTotal) * inputs.percepcionRate;
        const subtotal = arancelValue + igvTotal + ipmTotal + antidumpingTotal;
        const total = percepcionValue + subtotal;
        return {
            arancelValue,
            baseIGV,
            igvValue,
            igvTotal,
            ipmTotal,
            antidumpingTotal,
            subtotal,
            percepcionValue,
            total
        };
    }

    /**
     * Calcula la percepción
     */
    function calculatePercepcion(baseImponible, impuestos, inputs) {
        return (baseImponible.valorCIFValue + impuestos.arancelValue + impuestos.igvValue) * inputs.percepcionRate;
    }

    /**
     * Calcula los totales
     */
    function calculateTotals(derived, baseImponible, impuestos, percepcionValue, inputs) {
        const costoDestino = baseImponible.valorCIFValue * 0.3;

        const impuestosTotal = impuestos.arancelValue +
            impuestos.igvTotal + impuestos.ipmTotal +
            impuestos.antidumpingTotal +
            percepcionValue;

        const servicioTrading = 250;

        const montoTotalValue = servicioTrading +
            inputs.servicioImpoValue +
            impuestosTotal +
            baseImponible.valorCargaValue;

        return {
            impuestosTotal,
            costoDestino,
            servicioTrading,
            montoTotalValue
        };
    }

    /**
     * Calcula los costos unitarios
     */
    function calculateUnitCosts(totals, moq) {
        let costoUnitarioUSDValue = 0;
        if (moq > 0) {
            costoUnitarioUSDValue = totals.montoTotalValue / moq;
        }
        const costoUnitarioPENValue = costoUnitarioUSDValue * EXCHANGE_RATE;

        return {
            costoUnitarioUSDValue,
            costoUnitarioPENValue
        };
    }

    /**
     * Actualiza la interfaz de usuario con todos los valores calculados
     */
    function updateUI(derived, baseImponible, impuestos, percepcionValue, totals, unitCosts, inputs) {
        console.log(derived, "derived")
        $("#precioUSD").val(derived.precioUSD.toFixed(2));
        $('#totalUSD').val(derived.totalUSDValue.toFixed(2));
        $('#totalCBM').val(derived.totalCBMValue.toFixed(2));

        // Actualizar base imponiblelis
        $('#valorCarga').text(formatCurrency(baseImponible.valorCargaValue));
        $('#flete').text(formatCurrency(baseImponible.fleteValue));
        $('#seguro').text(formatCurrency(baseImponible.seguroValue));
        $('#valorCIF').text(formatCurrency(baseImponible.valorCIFValue));

        // Actualizar impuestos
        $("#adValorem").text(formatCurrency(impuestos.arancelValue));
        $("#igvTotal").text(formatCurrency(impuestos.igvTotal));
        $("#ipmTotal").text(formatCurrency(impuestos.ipmTotal));
        $("#antidumpingTotal").text(formatCurrency(impuestos.antidumpingTotal));
        $("#subtotal").text(formatCurrency(impuestos.subtotal));

        // Actualizar percepción
        $("#percepcionTotal").text(formatCurrency(percepcionValue));

        // Actualizar totales de impuestos
        $('#total').text(formatCurrency(totals.impuestosTotal));
        // $("#costoDestino").text(formatCurrency(totals.costoDestino));

        // Actualizar resumen
        $('#valorCargaResumen').text(formatCurrency(baseImponible.valorCargaValue));
        $('#servicioTrading').text(formatCurrency(totals.servicioTrading));
        $('#servicioImportacion').text(formatCurrency(inputs.servicioImpoValue));
        $('#impuestos').text(formatCurrency(totals.impuestosTotal));
        $('#montoTotal').text(formatCurrency(totals.montoTotalValue));

        // Actualizar costos unitarios
        $('#costoUnitarioUSD').text(formatCurrency(unitCosts.costoUnitarioUSDValue.toFixed(2), '$'));
        $('#costoUnitarioPEN').text(formatCurrency(unitCosts.costoUnitarioPENValue.toFixed(2), 'S/'));
    }
    function getServicioPerCbm(cbm) {
        const cbmParsed = Number(cbm.toFixed(2));
        console.log(cbmParsed)
        if (cbmParsed >= 0.00 && cbmParsed <= 0.59) {
            return 280;
        } else if (cbmParsed >= 0.60 && cbmParsed <= 1.00) {
            return 375;
        }
        else if (cbmParsed > 1.00 && cbmParsed <= 2.00) {
            return 375 * cbmParsed;
        } else if (cbmParsed > 2.10 && cbmParsed <= 3.00) {
            return 350 * cbmParsed;
        } else if (cbmParsed > 3.10 && cbmParsed <= 4.00) {
            return 325 * cbmParsed;
        } else if (cbmParsed > 4.10) {
            return 300 * cbmParsed;
        }
        return 0;
    }
    

    function mostrarSkeletonProducto() {
        // Oculta solo el detalle, muestra el skeleton
        $('#ViewProduct').fadeOut(150, function() {
            $('#viewProductSkeleton').removeClass('hidden').css('display', 'flex').hide().fadeIn(200);
        });
    }

    function ocultarSkeletonProducto() {
        // Oculta el skeleton y muestra el detalle
        $('#viewProductSkeleton').fadeOut(150, function() {
            $('#viewProductSkeleton').addClass('hidden');
            $('#ViewProduct').fadeIn(200);
        });
    }

    // Event listeners for input changes
    $('#precioUSD, #moq, #totalCBM, #arancel, #antidumping').on('input', calculateValues);

    // Also allow manually entering percentage in arancel dropdown
    $('#arancel').on('change', function () {
        calculateValues();
    });

    // Allow custom arancel percentage input
    $('#arancel').on('keyup', function () {
        let value = $(this).val().replace('%', '');
        $(this).val(value);
        calculateValues();
    });
    $("#servicioImpo").on("keyup", function () {
        calculateValues();
    });
    $("#igv").on("keyup", function () {
        calculateValues();
    });
    $("#antidumping").on("keyup", function () {
        calculateValues();
    });
    $("#percepcion").on("keyup", function () {
        calculateValues();
    });

    $('.input-date').datepicker({
        format: 'dd/mm/yyyy',
        language: 'es',
        autoclose: true
    });

    // Event handlers para paginación
    $('#prevPageBtn').on('click', function() {
        if (currentPage > 1) {
            currentPage--;
            loadProducts();
        }
    });

    $('#nextPageBtn').on('click', function() {
        if (currentPage < totalPages) {
            currentPage++;
            loadProducts();
        }
    });

    // Función para actualizar controles de paginación
    function updatePaginationControls() {
        const startRecord = (currentPage - 1) * perPage + 1;
        const endRecord = Math.min(currentPage * perPage, totalRecords);
        
        // Actualizar información de paginación
        $('#paginationInfo').text(`Mostrando ${startRecord}-${endRecord} de ${totalRecords} productos`);
        
        // Actualizar botones anterior/siguiente
        $('#prevPageBtn').prop('disabled', currentPage <= 1);
        $('#nextPageBtn').prop('disabled', currentPage >= totalPages);
        
        // Generar números de página
        generatePageNumbers();
    }

    // Función para generar números de página
    function generatePageNumbers() {
        const $pageNumbers = $('#pageNumbers');
        $pageNumbers.empty();
        
        const maxVisiblePages = 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
        let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
        
        // Ajustar si no hay suficientes páginas
        if (endPage - startPage + 1 < maxVisiblePages) {
            startPage = Math.max(1, endPage - maxVisiblePages + 1);
        }
        
        // Agregar botón "..." al inicio si es necesario
        if (startPage > 1) {
            $pageNumbers.append('<span class="px-3 py-2 text-sm text-gray-500">...</span>');
        }
        
        // Generar botones de página
        for (let i = startPage; i <= endPage; i++) {
            const isActive = i === currentPage;
            const pageBtn = $(`
                <button class="page-number px-3 py-2 text-sm font-medium rounded-md ${
                    isActive 
                        ? 'bg-orange-600 text-white' 
                        : 'text-gray-500 bg-white border border-gray-300 hover:bg-gray-50'
                }" data-page="${i}">
                    ${i}
                </button>
            `);
            
            pageBtn.on('click', function() {
                const page = parseInt($(this).data('page'));
                if (page !== currentPage) {
                    currentPage = page;
                    loadProducts();
                }
            });
            
            $pageNumbers.append(pageBtn);
        }
        
        // Agregar botón "..." al final si es necesario
        if (endPage < totalPages) {
            $pageNumbers.append('<span class="px-3 py-2 text-sm text-gray-500">...</span>');
        }
    }

    // Función para sincronizar el estado de los controles con los filtros actuales
    function syncControlsWithFilters() {
        // Sincronizar select de ordenamiento
        if (currentFilters.sort) {
            $('#sortSelect').val(currentFilters.sort);
        } else {
            $('#sortSelect').val('recent'); // Valor por defecto
        }
        
        // Sincronizar campo de búsqueda
        if (currentFilters.search) {
            $('#searchInput').val(currentFilters.search);
        } else {
            $('#searchInput').val('');
        }
        
        // Sincronizar filtros de fecha y categoría
        if (currentFilters.fechaInicio) {
            $('#txt-Fe_Inicio_Carga').val(currentFilters.fechaInicio);
        } else {
            $('#txt-Fe_Inicio_Carga').val('');
        }
        if (currentFilters.fechaFin) {
            $('#txt-Fe_Fin_Carga').val(currentFilters.fechaFin);
        } else {
            $('#txt-Fe_Fin_Carga').val('');
        }
        if (currentFilters.categoria) {
            $('#txt-ID_Categoria').val(currentFilters.categoria);
        } else {
            $('#txt-ID_Categoria').val('0');
        }
    }

    // Función para limpiar todos los filtros
    function clearAllFilters() {
        currentFilters = {};
        currentPage = 1;
        
        // Limpiar controles
        $('#searchInput').val('');
        $('#sortSelect').val('recent');
        $('#txt-Fe_Inicio_Carga').val('');
        $('#txt-Fe_Fin_Carga').val('');
        $('#txt-ID_Categoria').val('0');
        
        loadProducts();
    }

    // Función para verificar si hay filtros activos
    function hasActiveFilters() {
        return !!(currentFilters.search || 
                 currentFilters.fechaInicio || 
                 currentFilters.fechaFin || 
                 currentFilters.categoria || 
                 (currentFilters.sort && currentFilters.sort !== 'recent'));
    }

    // Función para actualizar la apariencia del botón de limpiar
    function updateClearButtonAppearance() {
        const $clearBtn = $('#clearFiltersBtn');
        if (hasActiveFilters()) {
            $clearBtn.removeClass('text-gray-600').addClass('text-orange-600');
            $clearBtn.find('i').removeClass('fa-times').addClass('fa-filter');
        } else {
            $clearBtn.removeClass('text-orange-600').addClass('text-gray-600');
            $clearBtn.find('i').removeClass('fa-filter').addClass('fa-times');
        }
    }

    // Función para activar el modo de selección
    function activateSelectionMode() {
        isSelectionMode = true;
        $('#btnEnviarProductos').hide();
        $('#btnCancelarEnvio').show();
        $('#btnDeleteProducts').show().css('display', 'flex');
        $('#dynamicCategorizeBtnContainer').show();
        $(".checkbox").show();
        $('#selectAll').show();

        // Quitar clases view-btn y edit-btn de los productos
        $('#productGrid .view-btn, #productGrid .edit-btn').removeClass('view-btn edit-btn');

        // Hacer que al hacer click en la tarjeta, se active su checkbox
        $('#productGrid .card').off('click.selectProduct').on('click.selectProduct', function (e) {
            if ($(e.target).is('.checkbox') || $(e.target).is('button') || $(e.target).closest('button').length) return;
            const $checkbox = $(this).find('.checkbox');
            $checkbox.trigger('click');
        });
        
        // Guardar estado en localStorage
        saveSelectionState();
    }

    // Función para restaurar el modo de selección después de cambiar de página
    function restoreSelectionMode() {
        if (!isSelectionMode) return;
        
        console.log('Restaurando modo de selección, productos seleccionados:', checkedProducts);
        
        // Mostrar checkboxes y botones de selección
        $(".checkbox").show();
        $('#btnEnviarProductos').hide();
        $('#btnCancelarEnvio').show();
        $('#btnDeleteProducts').show().css('display', 'flex');
        $('#dynamicCategorizeBtnContainer').show();
        $('#selectAll').show();

        // Quitar clases view-btn y edit-btn de los productos
        $('#productGrid .view-btn, #productGrid .edit-btn').removeClass('view-btn edit-btn');

        // Restaurar el comportamiento de click en tarjetas
        $('#productGrid .card').off('click.selectProduct').on('click.selectProduct', function (e) {
            if ($(e.target).is('.checkbox') || $(e.target).is('button') || $(e.target).closest('button').length) return;
            const $checkbox = $(this).find('.checkbox');
            $checkbox.trigger('click');
        });

        // Restaurar checkboxes marcados
        $('#productGrid .card .checkbox').each(function() {
            const productId = $(this).data('product-id');
            if (checkedProducts.includes(productId)) {
                $(this).prop('checked', true);
            }
        });

        updateSelectAllBtnText();
        
        console.log('Modo de selección restaurado correctamente');
    }

    // Función para desactivar el modo de selección
    function deactivateSelectionMode() {
        isSelectionMode = false;
        mostrarBotones();
        checkedProducts = [];
        // Desmarcar todos los checkboxes visibles
        $('#productGrid .card:visible .checkbox').prop('checked', false);
        $(".checkbox").hide();
        updateSelectAllBtnText();

        // Regresar las clases según el perfil
        if (currentPrivilege === ROLE_PERU) {
            $('#productGrid .card').addClass('view-btn').removeClass('edit-btn');
        } else if (currentPrivilege === ROLE_CHINA) {
            $('#productGrid .card').addClass('edit-btn').removeClass('view-btn');
        }
        
        // Limpiar localStorage
        localStorage.removeItem('catalogoSelectionMode');
        localStorage.removeItem('catalogoCheckedProducts');
    }

    // Función para guardar el estado de selección en localStorage
    function saveSelectionState() {
        if (isSelectionMode) {
            localStorage.setItem('catalogoSelectionMode', 'true');
            localStorage.setItem('catalogoCheckedProducts', JSON.stringify(checkedProducts));
        }
    }

    // Función para cargar el estado de selección desde localStorage
    function loadSelectionState() {
        const savedMode = localStorage.getItem('catalogoSelectionMode');
        const savedProducts = localStorage.getItem('catalogoCheckedProducts');
        
        console.log('Cargando estado de selección:', { savedMode, savedProducts });
        
        if (savedMode === 'true') {
            isSelectionMode = true;
            if (savedProducts) {
                checkedProducts = JSON.parse(savedProducts);
            }
            
            console.log('Estado de selección cargado:', { isSelectionMode, checkedProducts });
            
            // Restaurar la UI inmediatamente si estamos en modo selección
            setTimeout(() => {
                if (isSelectionMode) {
                    restoreSelectionMode();
                }
            }, 100);
            
            return true;
        }
        return false;
    }

    // Función para limpiar el estado de selección después de acciones exitosas
    function clearSelectionState() {
        checkedProducts = [];
        isSelectionMode = false;
        localStorage.removeItem('catalogoSelectionMode');
        localStorage.removeItem('catalogoCheckedProducts');
        deactivateSelectionMode();
    }

});