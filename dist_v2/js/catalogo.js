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
// Function to format number as currency

$(document).ready(async function () {
    productoFormSection = $('#productoFormSection')
    productListSection = $('#productListSection')
    spinner = $(".backdrop")

    showSkeletons();

    // Simulate loading data (replace with actual API call)
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

    async function loadProducts() {
        if (window.location.href.includes("listarCompletados")) {
            isInCompleted = true;
        } else if (window.location.href.includes("listarSeleccionados")) {
            isInTienda = true;
        } else {
            isInCompleted = false;
            isInTienda = false;
        }

        url = base_url + 'CatalogoController/getCatalogo';
        if (isInCompleted) {
            url = base_url + 'CatalogoController/getCatalogoCompletados';
        } else if (isInTienda) {
            url = base_url + 'CatalogoController/getCatalogoTienda';
        }
        const response = await fetch(url);
        if (response.ok) {
            const data = await response.json();
            if (data.status) {
                const products = data.data;
                renderProducts(products);
            } else {
                console.error('Error loading products:', data.message);
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
            $product.find('h3').text(product.nombre);
            $product.find('.text-gray-600').text(`RMB: ¥${product.precio}`);
            $product.find('.text-gray-500').text(`MOQ: ${product.moq}`);
            $product.find('.text-gray-400').text(`${product.cod_producto}`);
            $product.find('.precioPeru').text(`Precio Peru: S/. ${parseFloat(product.precio_peru).toFixed(2)}`);
            $product.find('.precioUSD').text(`Precio USD: $ ${parseFloat(product.precio_usd).toFixed(2)}`);
            //ifproducts has category_name key set text-gray-800 
            if (product.category_name) {
                $product.find('.text-gray-800').text(`${product.category_name}`);
            }
            if (product.status == "PENDIENTE") {
                $product.find('.precioUSD').hide();
                $product.find('.precioPeru').hide();
            } else {
                $product.find('.precioUSD').show();
                $product.find('.precioPeru').show();
            }
            if (product.status != "COTIZADO") {
                $product.find('.btnTienda').hide();
            } else {
                $product.find('.btnTienda').show();
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
            $checkbox.on('click', function (e) {
                e.stopPropagation();
                const productId = $(this).data('product-id');
                if ($(this).is(':checked')) {
                    checkedProducts.push(productId);
                } else {
                    checkedProducts = checkedProducts.filter(id => id !== productId);
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
            const cbm = Number(product.cbm_box)*Number(product.moq)/ Number(product.qty_box);
            $('#servicioImpo').val(product.servicio_impo ?? getServicioPerCbm(cbm));
            $('#arancel').val(product.arancel ?? "6.00");
            $('#igv').val(product.igv ?? 16);
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

            const query = $("#searchInput").val()?.toLowerCase() || '';
            filterProducts(query);
        }, 300));

        // Sort select handler
        $('#sortSelect').on('change', function () {
            const value = $(this).val();
            sortProducts(value);
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
                    checkedProducts = [];
                    $('#btnEnviarProductos').show();
                    $('#btnCancelarEnvio').hide();
                    $('#btnConfirmarEnvio').hide();
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
        $('#filterBtn').on('click', function () {
            // Implement filter modal/dropdown
            console.log('Show filters');
        });

        // Edit button handler
        $(document).on('click', '.edit-btn', async function (e) {
            e.preventDefault();
            e.stopPropagation();
            $(".checkbox").hide();
            $('#btnEnviarProductos').show();
            $('#btnCancelarEnvio').hide();
            $('#btnConfirmarEnvio').hide();
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
        const $grid = $('#productGrid');
        const $cards = $grid.find('.card');
        console.log($cards)
        // Filter cards based on query
        $cards.each(function () {
            const $card = $(this);
            const name = $card.find('h3').text().toLowerCase();
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
    }

    function sortProducts(criteria) {
        const $grid = $('#productGrid');
        const $cards = $grid.find('.card');

        // Sort cards based on criteria
        const sortedCards = $cards.sort((a, b) => {
            const aValue = $(a).find('.text-gray-600').text().replace('RMB: ¥', '');
            const bValue = $(b).find('.text-gray-600').text().replace('RMB: ¥', '');
            if (criteria === 'priceAsc') {
                return parseFloat(aValue) - parseFloat(bValue);
            } else if (criteria === 'priceDesc') {
                return parseFloat(bValue) - parseFloat(aValue);
            }
            return 0;
        });

        // Clear grid and append sorted cards
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


    $('#btnEnviarProductos').on('click', function (e) {
        e.preventDefault();
        $('#btnEnviarProductos').hide();
        $('#btnCancelarEnvio').show();
        $('#btnConfirmarEnvio').show();
        $(".checkbox").show();
    })
    $('#btnCancelarEnvio').on('click', function (e) {
        e.preventDefault();
        $('#btnEnviarProductos').show();
        $('#btnCancelarEnvio').hide();
        $('#btnConfirmarEnvio').hide();
        $(".checkbox").hide();
        checkedProducts = [];
    })
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
    $("#btnBack").on("click", function (e) {
        e.preventDefault();
        productoFormSection.hide();
        productListSection.show();
    });
    $("#listViewBtn").on("click", function (e) {
        e.preventDefault();
        //change productsGrid to flex column
        const $grid = $('#productGrid');
        $grid.removeClass('grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-5');
        $grid.addClass('flex flex-col gap-5');

    })
    $("#gridViewBtn").on("click", function (e) {
        e.preventDefault();
        //change productsGrid to grid
        const $grid = $('#productGrid');
        $grid.removeClass('flex flex-col gap-5 ');
        $grid.addClass('grid  gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3');
    })
    $("#btnDelete").on("click", function (e) {
        e.preventDefault();
        //show swall confirmation
        Swal.fire({
            title: '¿Está seguro de eliminar el producto?',
            text: "Una vez eliminado, no podrá deshacer esta acción.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                deleteProduct(currentProductId);
            }
        })
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
        const precioUSD = ((inputs.precioYuanes + inputs.profit + (inputs.delivery / inputs.moq))/ YUAN_TO_USD)
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
        const igvValue = baseIGV * inputs.igvRate;
        const igvTotal = 0.16 * (baseImponible.valorCIFValue + arancelValue);
        const ipmTotal = 0.02 * (arancelValue + baseImponible.valorCIFValue);
        const antidumpingTotal = inputs.antidumpingValue * inputs.moq;
        const percepcionValue = (baseImponible.valorCIFValue + arancelValue + igvTotal+ipmTotal) * inputs.percepcionRate;
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
        const costoDestino = baseImponible.valorCIFValue * 0.4;

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
    function updateUI(derived, baseImponible, impuestos, percepcionValue, totals, unitCosts,inputs) {
        console.log(derived,"derived")
        $("#precioUSD").val(derived.precioUSD.toFixed(2));
        $('#totalUSD').val(derived.totalUSDValue.toFixed(2));
        $('#totalCBM').val(derived.totalCBMValue.toFixed(2));

        // Actualizar base imponible
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
        $("#costoDestino").text(formatCurrency(totals.costoDestino));

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
        if (cbmParsed >= 0.1 && cbmParsed <= 0.59) {
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

});