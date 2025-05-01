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
const EXCHANGE_RATE = 3.8;
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
        if (window.location.href.includes("listarCompletados")){
            isInCompleted = true;
        }else{
            isInCompleted = false;
        }

        url = base_url + 'CatalogoController/getCatalogo';
        if (isInCompleted) {
            url = base_url + 'CatalogoController/getCatalogoCompletados';
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
        function renderProducts(products) {
            const $grid = $('#productGrid');
            const $template = $('#productTemplate');
            // Clear grid
            $grid.empty();

            // Add products with staggered animation
            products.forEach((product, index) => {
                const $product = $($template.html());
               
                    //add badge in tienda 
                $product.find('.badge').text(product.status);
                switch (product.status) {
                    case "COTIZADO":
                        $product.find('.badge').addClass('bg-blue-500');
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
                $product.find('.precioPeru').text(`Precio Peru: S/. ${product.precio_peru}`);
                $product.find('.precioUSD').text(`Precio USD: $ ${product.precio_usd}`);
                if (product.status == "PENDIENTE") {
                    $product.find('.precioUSD').hide();
                    $product.find('.precioPeru').hide();
                }else{
                    $product.find('.precioUSD').show();
                    $product.find('.precioPeru').show();
                }
                if(product.status != "COTIZADO") {
                    $product.find('.btnTienda').hide();
                }else{
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
                setTimeout(() => {
                    $product.animate({ opacity: 1 }, 300);
                }, index * 100);
                
            });
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
                acceptedTypes: "video/mp4,video/webm,video/ogg",// Tipos de archivos aceptados
                maxSize: 20 * 1024 * 1024
            });

            contactCardContainer = new FileUploader({
                containerId: 'contactCardContainer',
                acceptedTypes: "image/jpeg,image/png,image/gif,image/webp",// Tipos de archivos aceptados
                maxSize: 5 * 1024 * 1024
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
                $('#servicioImpo').val(product.servicio_impo??350);
                $('#arancel').val(product.arancel??6.00);
                $('#igv').val(product.igv??0.18);
                $('#antidumping').val(product.antidumping??0.00);
                $('#percepcion').val(product.percepcion??3.50);
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

            // Filter button handler
            $('#filterBtn').on('click', function () {
                // Implement filter modal/dropdown
                console.log('Show filters');
            });

            // Edit button handler
            $(document).on('click', '.edit-btn', async function () {
                const $card = $(this).closest('.card');
                const productId = $card.data('product-id');
                // Redirect to edit page
                await loadProductDetails(productId);
            });

            // Delete button handler
            $(document).on('click', '.delete-btn', async function () {
                const $card = $(this).closest('.card');
                const productId = $card.data('product-id');
                console.log('Delete product with ID:', productId);
                await deleteProduct(productId);
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
        $("#btnAddProduct").on("click", function (e) {
            e.preventDefault();
            productoFormSection.show();
            productListSection.hide();
            currentProductId = null;
            const $form = $('#productForm');
            $form[0].reset();
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
                acceptedTypes: "video/mp4,video/webm,video/ogg",// Tipos de archivos aceptados
                maxSize: 20 * 1024 * 1024
            });
            contactCardContainer = new FileUploader({
                containerId: 'contactCardContainer',
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
                formData.append('precio_peru', $('#costoUnitarioPEN').val());
                formData.append('precio_usd', $('#costoUnitarioUSD').val());
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
                    if (!/^\d{9}$/.test(field.value.trim()) && field.value.trim() !== '') {
                        showError(field, errorElement, 'Ingrese un número de teléfono válido (9 dígitos).');
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
            return currency + ' ' + parseFloat(number).toFixed(2);
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

            // 8. Actualizamos la UI con todos los valores calculados
            updateUI(
                derivedValues,
                baseImponibleValues,
                impuestosValues,
                percepcionValue,
                totalValues,
                unitCosts
            );
        }

        /**
         * Obtiene todos los valores de entrada del formulario
         */
        function getInputValues() {
            return {
                precioYuanes: parseFloat($('#precio').val()) || 0,
                moq: parseInt($('#moq').val()) || 0,
                arancelRate: parseFloat($('#arancel').val()) / 100,
                igvRate: parseFloat($('#igv').val()) / 100,
                antidumpingValue: parseFloat($('#antidumping').val()) || 0,
                percepcionRate: parseFloat($('#percepcion').val()) / 100,
                servicioImpoValue: parseFloat($('#servicioImpo').val()),
                qtyXbox: parseInt($('#qtyXbox').val()) || 0,
                cbmXbox: parseFloat($('#cbmXbox').val()) || 0
            };
        }

        /**
         * Calcula valores derivados de los inputs
         */
        function calculateDerivedValues(inputs) {
            // Calcula precio en USD según fórmula (precio en yuanes + 7) / 7
            const precioUSD = (inputs.precioYuanes + 7) / 7;

            // Calcula total USD como MOQ * precio USD
            const totalUSDValue = inputs.moq * precioUSD;

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
            const fleteValue = servicioImpoValue * 0.6; // Servicio de impo * 0.6
            const seguroValue = derived.totalUSDValue >= 5000 ? 100 : 50;
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
            const igvTotal = 0.16 * baseImponible.valorCIFValue;
            const ipmTotal = (inputs.igvRate - 0.16) * baseImponible.valorCIFValue;
            const antidumpingTotal = inputs.antidumpingValue * inputs.moq;

            const subtotal = arancelValue + igvTotal + ipmTotal + antidumpingTotal;

            return {
                arancelValue,
                baseIGV,
                igvValue,
                igvTotal,
                ipmTotal,
                antidumpingTotal,
                subtotal
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
                impuestos.igvValue +
                (inputs.igvRate - 0.16) * baseImponible.valorCIFValue +
                (inputs.antidumpingValue * inputs.moq) +
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
        function updateUI(derived, baseImponible, impuestos, percepcionValue, totals, unitCosts) {
            // Actualizar valores derivados
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
            $('#servicioImportacion').text(formatCurrency(250));
            $('#impuestos').text(formatCurrency(totals.impuestosTotal));
            $('#montoTotal').text(formatCurrency(totals.montoTotalValue));

            // Actualizar costos unitarios
            $('#costoUnitarioUSD').val(unitCosts.costoUnitarioUSDValue.toFixed(2));
            $('#costoUnitarioPEN').val(unitCosts.costoUnitarioPENValue.toFixed(2));
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