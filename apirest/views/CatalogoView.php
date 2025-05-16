<script>
    var currentPrivilege = "<?php echo $this->user->No_Grupo; ?>";
    localStorage.setItem("currentPrivilege", currentPrivilege);
</script>
<div class="content-wrapper">
    <section class="content" id="productListSection">
        <div class="container-fluid">
            <div class="flex items-center
            flex-col flex-md-row flex-lg-row flex-xl-row
            justify-between mb-8 pt-3  border-b-2 border-gray-200 pb-2">
                <h1 class="text-2xl font-bold text-gray-800">Listado de Cotizados</h1>

                <div class="flex flex-col gap-2 flex-md-row flex-xs-row flex-xl-row">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                            <span class="i-lucide-search w-5 h-5"></span>
                        </span>
                        <input type="text" id="searchInput"
                            placeholder="Buscar por"
                            class="pl-10 pr-4 py-2 w-full border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <!-- button enviar productos -->

                    </div>

                    <?php if ($this->user->No_Grupo == "CatalogoChina") { ?>
                        <button id="btnAddProduct" class="gap-2 bg-orange-600 hover:bg-orange-700 text-white py-2 px-10 rounded-md transition-colors flex items-center justify-center shadow-sm">
                            <span>Agregar Producto</span>
                            <span class="i-lucide-plus text-lg"></span>
                        </button>
                    <?php } ?>
                    <?php if ($this->user->No_Grupo == "CatalogoPeru") { ?>
                        <button id="btnEnviarProductos" class=" btn text-white text-sm bg-orange-600 hover:bg-orange-700inset-y-0 right-0 flex items-center pr-3 text-gray-500">
                            Enviar Productos
                        </button>
                        <button id="btnCancelarEnvio" class="hidden btn text-white text-sm bg-white hover:bg-white-200 border border-orange-600 hover:border-orange-600 inset-y-0 right-0 flex items-center pr-3 text-gray-500">
                            Cancelar
                        </button>
                        <button id="btnConfirmarEnvio" class="hidden btn text-white text-sm   bg-orange-600 hover:bg-orange-700 inset-y-0 right-0 flex items-center pr-3 text-gray-500">

                            <span class="ml-2"> Confirmar Envio</span>
                            <span class="i-lucide-send w-5 h-5">

                            </span>
                        </button>
                    <?php } ?>
                </div>
            </div>

            <!-- Search and Filter Bar -->
            <div class=" mb-6">
                <div class="flex flex-col md:flex-row gap-4">
                    <!-- Search -->
                    <div class="flex-1">

                    </div>

                    <!-- Filters -->
                    <div class="flex flex-col flex-sm-row align-items-center justify-end gap-2">
                        <span value=""
                            class="text-sm font-medium text-gray-400 mb-1">Ordenar por:</span>
                        <div class="flex gap-2 items-center justify-center mr-4">

                            <select id="sortSelect" class="border border-gray-300 rounded-md px-4 py-2 bg-white focus:ring-2  text-gray-300  focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <option value="name" class="text-gray-300">Agregados recientemente</option>
                                <option value="price">Precio</option>
                                <option value="date">Fecha</option>
                            </select>
                        </div>
                        <!-- ver vista cuadricula ,lista -->
                        <div class="flex gap-1 items-center justify-center">
                            Ver
                            <button id="gridViewBtn" class="rounded-full w-8 h-8 flex items-center justify-center shadow-sm focus:outline-none">
                                <svg width="9" height="9" viewBox="0 0 9 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="4.09091" height="4.09091" rx="0.818182" fill="#7E7E7E" />
                                    <rect y="4.9082" width="4.09091" height="4.09091" rx="0.818182" fill="#7E7E7E" />
                                    <rect x="4.90918" width="4.09091" height="4.09091" rx="0.818182" fill="#7E7E7E" />
                                    <rect x="4.90918" y="4.9082" width="4.09091" height="4.09091" rx="0.818182" fill="#7E7E7E" />
                                </svg>

                            </button>
                            <button id="listViewBtn" class="rounded-full w-8 h-8 flex items-center justify-center shadow-sm focus:outline-none">
                                <svg width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="3" width="9" height="2" rx="1" fill="#7E7E7E" />
                                    <rect width="2" height="2" rx="1" fill="#7E7E7E" />
                                    <rect y="3" width="2" height="2" rx="1" fill="#7E7E7E" />
                                    <rect y="6" width="2" height="2" rx="1" fill="#7E7E7E" />
                                    <rect x="3" y="3" width="9" height="2" rx="1" fill="#7E7E7E" />
                                    <rect x="3" y="6" width="9" height="2" rx="1" fill="#7E7E7E" />
                                </svg>

                            </button>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Product Grid -->
            <div id="productGrid" class="grid grid-cols-1 grid-rows-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3  gap-5  p-3 h-full">

            </div>
        </div>
    </section>
    <!-- Add Product Form -->
    <section class="content hidden" id="productoFormSection">
        <div class="container-fluid">

            <div class="flex items-center justify-between flex-col md:flex-row pt-4">
                <button
                    id="btnBack"
                    type="button" id="btn-back-documentacion-documentacion" class="py-2 px-4 bg-white hover:bg-white-200 text-black-200 py-2 px-2 border border-transparent hover:border-orange-600 rounded " data-type="html"><i class="fa fa-arrow-left mr-2"></i> Regresar</button>
                <div class="gap-2 d-flex flex-row  mt-2 md:mt-0" id="actionButtons">
                    <?php if ($this->user->No_Grupo == "CatalogoPeru") { ?>
                        <button id="btnCotizar" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-6 rounded-sm transition-colors flex items-center justify-center shadow-sm">
                            <span>Pasar a cotizados</span>
                            <span class="i-lucide-check-circle ml-2"></span>
                        </button>
                    <?php } ?>
                    <button id="btnDelete" class="border-2 border-red-500 gap-2 text-red py-2 px-8 rounded-sm transition-colors flex items-center justify-center shadow-sm">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 3.5H2.55556H15" stroke="#FF3636" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M13.4446 3.5V14C13.4446 14.3978 13.2807 14.7794 12.9889 15.0607C12.6972 15.342 12.3016 15.5 11.889 15.5H4.11122C3.69866 15.5 3.303 15.342 3.01128 15.0607C2.71955 14.7794 2.55566 14.3978 2.55566 14V3.5M4.889 3.5V2C4.889 1.60218 5.05289 1.22064 5.34461 0.93934C5.63633 0.658035 6.03199 0.5 6.44455 0.5H9.55566C9.96822 0.5 10.3639 0.658035 10.6556 0.93934C10.9473 1.22064 11.1112 1.60218 11.1112 2V3.5" stroke="#FF3636" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M6.44434 7.25V11.75" stroke="#FF3636" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M9.55566 7.25V11.75" stroke="#FF3636" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span>Eliminar Producto</span>


                    </button>
                    <button id="btnSave" class="bg-orange-500 hover:bg-orange-600 text-white py-2 px-8 rounded-sm transition-colors flex items-center justify-center shadow-sm">
                        <span>Guardar</span>
                        <span class="i-lucide-save ml-2"></span>
                    </button>
                </div>
            </div>

        </div>
        <div class="grid grid-cols-1 lg:grid-cols-8 gap-6 mt-6">
            <!-- Left Column - Images -->
            <div class="lg:col-span-3">
                <div class="">
                    <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <!-- Main Image Upload -->
                        <div id="mainImageContainer" class="relative bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg mb-4 transition-all hover:bg-gray-100 hover:border-blue-300 w-75 mx-auto">
                        </div>
                    </div>
                    <div class=" mt-2 shadow-sm border border-gray-200 overflow-hidden">
                        <!-- Main Image Upload -->
                        <div class="flex md:grid md:grid-cols-3 gap-3 overflow-x-auto md:overflow-x-visible snap-x snap-mandatory">
                            <div id="additionalImage1Container"
                                class="min-w-[70vw] md:min-w-0 snap-center bg-white p-2 rounded-lg additional-image-container relative bg-gray-50 border-2 border-dashed border-gray-300 transition-all hover:bg-gray-100 hover:border-blue-300 h-full flex items-center justify-center"></div>
                            <div id="additionalImage2Container"
                                class="min-w-[70vw] md:min-w-0 snap-center bg-white p-2 rounded-lg additional-image-container relative bg-gray-50 border-2 border-dashed border-gray-300 transition-all hover:bg-gray-100 hover:border-blue-300 h-full flex items-center justify-center"></div>
                            <div id="additionalVideo1Container"
                                class="min-w-[70vw] md:min-w-0 snap-center bg-white p-2 rounded-lg video-container relative bg-gray-50 border-2 border-dashed border-gray-300 transition-all hover:bg-gray-100 hover:border-blue-300 h-full flex items-center justify-center"></div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Right Column - Product Details Form -->
            <div class="lg:col-span-3">

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden " <?php if ($this->user->No_Grupo != "CatalogoChina") echo 'style="pointer-events:none"'; ?>>
                    <div class="p-4 border-b border-gray-100 flex items-center">
                        <h2 class="text-lg font-medium text-gray-800">Detalles del Producto</h2>
                        <div class="ml-2 tooltip" data-tooltip="Información obligatoria para guardar el producto">
                            <span class="i-lucide-info text-blue-500 w-5 h-5"></span>
                        </div>
                    </div>

                    <div class="px-4 py-3">
                        <form id="productForm">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <!-- Nombre -->
                                <div class="form-group col-span-1 md:col-span-2">
                                    <div class="flex flex-col sm:flex-row">
                                        <label for="nombre" class="mb-1 sm:mb-0 sm:w-1/6 text-sm font-medium text-gray-400 flex items-center">
                                            Nombre <span class="text-red-500">*</span>
                                        </label>
                                        <div class="w-full sm:w-5/6">
                                            <input type="text" id="nombre" name="nombre"
                                                class="form-input block w-full border-2 text-center rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                                required>
                                            <p class="form-error text-red-500 text-xs mt-1 hidden">Este campo es obligatorio.</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Precio -->
                                <div class="form-group col-span-1">
                                    <div class="flex flex-col sm:flex-row">
                                        <label for="precio" class="mb-1 sm:mb-0 sm:w-1/3 text-sm font-medium text-gray-400 flex items-center">
                                            Precio <span class="text-red-500">*</span>
                                        </label>
                                        <div class="w-full sm:w-2/3">
                                            <div class="relative">
                                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">¥</span>
                                                <input type="text" id="precio" name="precio"
                                                    class="form-input block w-full border-2 text-center rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 pl-8"
                                                    placeholder="0.00" required>
                                            </div>
                                            <p class="form-error text-red-500 text-xs mt-1 hidden">Ingrese solo valores numéricos.</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Profit (conditionally shown) -->
                                <?php if ($this->user->No_Grupo == "CatalogoPeru") { ?>
                                    <div class="form-group col-span-1">
                                        <div class="flex flex-col sm:flex-row">
                                            <label for="profit" class="mb-1 sm:mb-0 sm:w-1/3 text-sm font-medium text-gray-400 flex items-center">
                                                Profit <span class="text-red-500">*</span>
                                            </label>
                                            <div class="w-full sm:w-2/3">
                                                <div class="relative">
                                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">¥</span>
                                                    <input type="text" id="profit"
                                                        class="form-input block w-full border-2 text-center rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 pl-8"
                                                        placeholder="0.00" disabled value="3.00">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>

                                <!-- MOQ -->
                                <div class="form-group col-span-1">
                                    <div class="flex flex-col sm:flex-row">
                                        <label for="moq" class="mb-1 sm:mb-0 sm:w-1/3 text-sm font-medium text-gray-400 flex items-center">
                                            MOQ <span class="text-red-500">*</span>
                                        </label>
                                        <div class="w-full sm:w-2/3">
                                            <input type="number" id="moq" name="moq" min="1"
                                                class="form-input block w-full border-2 text-center rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                                required>
                                            <p class="form-error text-red-500 text-xs mt-1 hidden">Ingrese un valor numérico válido.</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Qty x box -->
                                <div class="form-group col-span-1">
                                    <div class="flex flex-col sm:flex-row">
                                        <label for="qtyXbox" class="mb-1 sm:mb-0 sm:w-1/3 text-sm font-medium text-gray-400 flex items-center">
                                            Qty x box <span class="text-red-500">*</span>
                                        </label>
                                        <div class="w-full sm:w-2/3">
                                            <input type="number" id="qtyXbox" name="qtyXbox" min="1"
                                                class="form-input block w-full border-2 text-center rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                                required>
                                            <p class="form-error text-red-500 text-xs mt-1 hidden">Ingrese un valor numérico válido.</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Cbm x box -->
                                <div class="form-group col-span-1">
                                    <div class="flex flex-col sm:flex-row">
                                        <label for="cbmXbox" class="mb-1 sm:mb-0 sm:w-1/3 text-sm font-medium text-gray-400 flex items-center">
                                            Cbm x box <span class="text-red-500">*</span>
                                        </label>
                                        <div class="w-full sm:w-2/3">
                                            <input type="number" id="cbmXbox" name="cbmXbox" min="0" step="0.001"
                                                class="form-input block w-full border-2 text-center rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                                required>
                                            <p class="form-error text-red-500 text-xs mt-1 hidden">Ingrese un valor numérico válido.</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Días Entrega -->
                                <div class="form-group col-span-1">
                                    <div class="flex flex-col sm:flex-row">
                                        <label for="diasEntrega" class="mb-1 sm:mb-0 sm:w-1/3 text-sm font-medium text-gray-400 flex items-center">
                                            Días Entrega <span class="text-red-500">*</span>
                                        </label>
                                        <div class="w-full sm:w-2/3">
                                            <input type="number" id="diasEntrega" name="diasEntrega" min="1"
                                                class="form-input block w-full border-2 text-center rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                                required>
                                            <p class="form-error text-red-500 text-xs mt-1 hidden">Ingrese un valor numérico válido.</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Delivery -->
                                <div class="form-group col-span-1">
                                    <div class="flex flex-col sm:flex-row">
                                        <label for="delivery" class="mb-1 sm:mb-0 sm:w-1/3 text-sm font-medium text-gray-400 flex items-center">
                                            Delivery <span class="text-red-500">*</span>
                                        </label>
                                        <div class="w-full sm:w-2/3">
                                            <div class="relative">
                                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">¥</span>
                                                <input type="text" id="delivery" name="delivery"
                                                    class="form-input block w-full border-2 text-center rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 pl-8"
                                                    placeholder="0.00" required>
                                            </div>
                                            <p class="form-error text-red-500 text-xs mt-1 hidden">Ingrese solo valores numéricos.</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Colores -->
                                <div class="form-group col-span-1 md:col-span-2">
                                    <div class="flex flex-col sm:flex-row">
                                        <label for="colores" class="mb-1 sm:mb-0 sm:w-1/6 text-sm font-medium text-gray-400 flex items-center">
                                            Colores
                                        </label>
                                        <div class="w-full sm:w-5/6">
                                            <input type="text" id="colores" name="colores"
                                                class="form-input block w-full border-2 text-center rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                        </div>
                                    </div>
                                </div>

                                <!-- Notas -->
                                <div class="form-group col-span-1 md:col-span-2">
                                    <div class="flex flex-col sm:flex-row">
                                        <label for="notas" class="mb-1 sm:mb-0 sm:w-1/6 text-sm font-medium text-gray-400 flex items-center">
                                            Notas
                                        </label>
                                        <div class="w-full sm:w-5/6">
                                            <textarea id="notas" name="notas" rows="4"
                                                class="form-input block w-full border-2 text-center rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Provider Data -->

            </div>
            <div class="lg:col-span-2">



                <!-- Provider Data -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden ">
                    <div class="p-4 border-b border-gray-100">
                        <h2 class="text-lg font-medium text-gray-800">Datos del Proveedor</h2>
                    </div>

                    <div class="p-4">
                        <form id="providerForm">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- WeChat / Phone -->
                                <div class="form-group col-span-2 flex flex-row gap-1">
                                    <label for="wechatPhone" class="block text-sm font-medium text-gray-400 mb-1 w-1/2">
                                        WeChat / Phone
                                    </label>
                                    <input type="text" id="wechatPhone" name="wechatPhone" required
                                        class=" w-1/2 form-input block w-full border-2 text-center rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                                    <p class="form-error text-red-500 text-xs mt-1 hidden">Este campo es obligatorio.</p>

                                </div>

                                <!-- Contact Card -->
                                <div class="form-group col-span-2">
                                    <label for="contactCard" class="block text-sm font-medium text-gray-400 mb-1">
                                        Tarjeta de contacto
                                    </label>
                                    <div id="contactCardContainer" class="relative bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg transition-all hover:bg-gray-100 hover:border-blue-300">

                                    </div>

                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- both columns -->
            <?php if ($this->user->No_Grupo == "CatalogoPeru") { ?>
                <div class="lg:col-span-3"></div>
                <div class="lg:col-span-5">
                    <div class=" bg-white rounded-lg shadow-lg ">
                        <h1 class="text-2xl font-bold mb-6 p-6  border-b-2 border-gray-300">Costos de importación</h1>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8 p-6">
                            <div class="md:grid-cols-1 grid grid-cols-1 ">
                                <div class="form-group flex md:flex-row flex-col gap-3">
                                    <label class="block text-sm font-medium text-gray-700 w-full md:w-2/3">Precio USD:</label>
                                    <div class="relative w-full md:w-2/3">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">$</span>
                                        <input type="number" id="precioUSD" class="block w-full text-end rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" disabled>
                                    </div>
                                </div>
                                <div class="form-group flex md:flex-row flex-col gap-3">
                                    <label class="block text-sm font-medium text-gray-700 w-full md:w-2/3">Total USD:</label>
                                    <div class="relative w-full md:w-2/3">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">$</span>
                                        <input type="number" id="totalUSD" class="block w-full  text-end rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" disabled>
                                    </div>
                                </div>

                                <div class="form-group flex md:flex-row flex-col gap-3">
                                    <label class="block text-sm font-medium text-gray-700 w-full md:w-2/3">Total CBM:</label>
                                    <input type="number" id="totalCBM" class="block w-full md:w-1/3 text-end rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" step="0.01" disabled>
                                </div>
                                <div class="form-group flex md:flex-row flex-col gap-3">
                                    <label class="block text-sm font-medium text-gray-700 w-full md:w-2/3">Servicio Impo:</label>
                                    <div class="relative w-full md:w-2/3">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">$</span>
                                        <input type="number" id="servicioImpo" class="border-gray-300 block w-full  text-end rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" value="350.00" readonly>
                                    </div>
                                </div>

                                <div class="form-group flex md:flex-row flex-col gap-3">
                                    <label class="block text-sm font-medium text-gray-700 w-full md:w-1/3">Arancel:</label>
                                    <select id="arancel" class="block w-full md:w-2/3 text-end rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                        <option value="0.00">0%</option>
                                        <option value="6.00">6%</option>
                                        <option value="11.00">11%</option>
                                    </select>
                                </div>

                                <div class="form-group flex md:flex-row flex-col gap-3">
                                    <label class="block text-sm font-medium text-gray-700 w-full md:w-2/3">IGV:</label>
                                    <div class="relative w-full md:w-2/3">
                                        <input type="number" id="igv" class="border-gray-300 block w-full  text-end rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" value="18" readonly>
                                        <span class="absolute bottom-2 left-0 flex items-center pl-3 text-gray-500">%</span>
                                    </div>
                                </div>

                                <div class="form-group flex md:flex-row flex-col gap-3">
                                    <label class="block text-sm font-medium text-gray-700 w-full md:w-2/3">Antidumping:</label>
                                    <div class="relative w-full md:w-2/3">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">$</span>
                                        <input type="number" id="antidumping" class="border-gray-300 block w-full  text-end rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" value="0">
                                    </div>
                                </div>


                                <div class="form-group flex md:flex-row flex-col gap-1">
                                    <label class="block text-sm font-medium text-gray-700 w-full md:w-2/3">Percepción:</label>
                                    <input type="number" id="percepcion" class="block w-full md:w-1/3 text-end rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" value="3.5">
                                </div>
                            </div>
                            <div class="md:grid-cols-1 grid grid-cols-1 text-start ">
                                <table class="w-full">
                                    <thead>
                                        <tr>
                                            <th class="border border-1 bg-gray-300 border-gray-200 text-center">Calculo de base imponible</th>
                                            <th class="border border-1 bg-gray-300 border-gray-200 text-center">Monto</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="border border-1 border-gray-300">Valor de carga</td>
                                            <td class="border border-1 border-gray-300 text-center" id="valorCarga">$ 0.00</td>
                                        </tr>
                                        <tr>
                                            <td class="border border-1 border-gray-300">Flete</td>
                                            <td class="border border-1 border-gray-300 text-center" id="flete">$ 0.00</td>
                                        </tr>
                                        <tr>
                                            <td class="border border-1 border-gray-300">Seguro</td>
                                            <td class="border border-1 border-gray-300 text-center" id="seguro">$ 0.00</td>
                                        </tr>
                                        <tr>
                                            <td class="border border-1 border-gray-300 font-bold">Valor cif</td>
                                            <td class="border border-1 border-gray-300 text-center font-bold" id="valorCIF">$ 0.00</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table class="w-full mt-2">
                                    <thead>
                                        <tr>
                                            <th class="border border-1 bg-gray-300 border-gray-200 text-center">Calculo de tributos</th>
                                            <td class="border border-1 bg-gray-300 border-gray-200 text-center">Monto</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="border border-1 border-gray-300 ">Ad valorem </td>
                                            <td class="border border-1 border-gray-30 text-center" id="adValorem">$ 0.00</td>

                                        </tr>
                                        <tr>
                                            <td class="border border-1 border-gray-300">IGV</td>
                                            <td class="border border-1 border-gray-300 text-center" id="igvTotal">$ 0.00</td>
                                        </tr>
                                        <tr>
                                            <td class="border border-1 border-gray-300">IPM</td>
                                            <td class="border border-1 border-gray-300 text-center" id="ipmTotal">$ 0.00</td>
                                        </tr>
                                        <tr>
                                            <td class="border border-1 border-gray-300 ">Antidumping</td>
                                            <td class="border border-1 border-gray-300 text-center" id="antidumpingTotal">$ 0.00</td>
                                        </tr>
                                        <tr>
                                            <td class="border border-1 border-gray-300 font-bold">Sub Total</td>
                                            <td class="border border-1 border-gray-300 text-center font-bold" id="subtotal">$ 0.00</td>
                                        </tr>

                                        <tr>
                                            <td class="border border-1 border-gray-300 ">Percepción</td>
                                            <td class="border border-1 border-gray-300 text-center" id="percepcionTotal">$ 0.00</td>
                                        </tr>
                                        <tr>
                                            <td class="border border-1 border-gray-300 font-bold">Total</td>
                                            <td class="border border-1 border-gray-300 text-center font-bold" id="total">$ 0.00</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <!--table with only head for costo en destino-->
                                <table class="w-full mt-2">
                                    <thead>
                                        <tr>
                                            <th class="border bg-gray-300 text-center">Costo en destino</th>
                                            <td class="border  bg-gray-300" id="costoDestino">$ 0.00</td>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <div class="md:grid-cols-1 grid grid-cols-1 text-start ">
                                <!--table base imponible-->
                                <table class="w-full">
                                    <thead>
                                        <tr>
                                            <th class="border border-1 bg-gray-500 text-white border-gray-200 text-center">Calculo de base imponible</th>
                                            <td class="border border-1 bg-gray-500 text-white border-gray-200 text-center">Monto</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="border border-1 border-gray-300 ">Valor carga(pago China)</td>
                                            <td class="border border-1 border-gray-300 text-center" id="valorCargaResumen">$ 0.00</td>

                                        </tr>
                                        <tr>
                                            <td class="border border-1 border-gray-300">Servicio trading(pago China)</td>
                                            <td class="border border-1 border-gray-300 text-center" id="servicioTrading">$ 0.00</td>
                                        </tr>
                                        <tr>
                                            <td class="border border-1 border-gray-300">Servicio importación</td>
                                            <td class="border border-1 border-gray-300 text-center" id="servicioImportacion">$ 0.00</td>
                                        </tr>
                                        <tr>
                                            <td class="border border-1 border-gray-300 ">Impuestos</td>
                                            <td class="border border-1 border-gray-300 text-center" id="impuestos">$ 0.00</td>
                                        </tr>
                                        <tr>
                                            <td class="border border-1 border-gray-300 font-bold">Sub Total</td>
                                            <td class="border border-1 border-gray-300 text-center font-bold" id="montoTotal">$ 0.00</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table class="w-full mt-2">
                                    <thead>
                                        <tr>
                                            <th class="border bg-gray-500 text-white text-center">Costo unitario de importación </th>
                                            <td class="border  bg-gray-500 text-white" id="costoUnitarioUSD">$ 0.00</td>
                                        </tr>
                                        <tr>
                                            <th class="border bg-gray-500 text-white text-center">Costo unitario de importación </th>
                                            <td class="border  bg-gray-500 text-white" id="costoUnitarioPEN">S/ 0.00</td>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8 p-6">

                    </div>
                </div>
        </div>
</div>
<?php } ?>
</div>

</section>
<template id="skeletonTemplate">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden animate-pulse">
        <div class="aspect-square bg-gray-200"></div>
        <div class="p-4">
            <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
            <div class="flex items-center justify-between mb-4">
                <div class="h-3 bg-gray-200 rounded w-1/4"></div>
                <div class="h-3 bg-gray-200 rounded w-1/4"></div>
            </div>
            <div class="border-t border-gray-100 pt-4 mt-4">
                <div class="h-8 bg-gray-200 rounded"></div>
            </div>
        </div>
    </div>
</template>
<template id="productTemplate">
    <div class="card edit-btn rounded-lg h-40 cursor-pointer flex flex-row w-full my-0 shadow-sm border border-gray-200 overflow-hidden hover:shadow-md hover:bg-gray-100 transition-shadow group relative"> <!--status badge-->
        <!--checkbox-->
        <input type="checkbox" class="checkbox hidden h-6 w-6 absolute top-2 left-2 
        accent-orange-500
        cursor-pointer checkbox" style="z-index: 100;" />
        <div class="absolute bottom-2 right-2 text-white text-xs badge font-semibold px-2 py-1 rounded-full" style="z-index: 200;"></div>

        <!--dropdown menu trigger-->
        <div class="absolute top-0 right-2 z-30">
            <button class="dropdown-trigger bg-white hover:bg-gray-100 rounded-full w-8 h-8 flex items-center justify-center shadow-sm focus:outline-none">
                <svg width="20" height="5" viewBox="0 0 11 3" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="9.76901" cy="1.15385" r="1.15385" transform="rotate(90 9.76901 1.15385)" fill="#D9D9D9" />
                    <circle cx="5.46139" cy="1.15385" r="1.15385" transform="rotate(90 5.46139 1.15385)" fill="#D9D9D9" />
                    <circle cx="1.15377" cy="1.15385" r="1.15385" transform="rotate(90 1.15377 1.15385)" fill="#D9D9D9" />
                </svg>

            </button>

            <!--dropdown menu content-->
            <div class="dropdown-menu2 absolute right-0 mt-1 w-80 bg-white rounded-md shadow-lg overflow-hidden z-40 hidden">
                <div class="py-1">
                    <button class="edit-btn w-full text-left px-4 py-2 text-sm text-gray-700 w-full hover:bg-blue-50 hover:text-blue-600 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                        Editar
                    </button>
                    <button class="delete-btn w-full text-left px-4 py-2 text-sm text-gray-700 w-full hover:bg-red-50 text-red-600 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            <line x1="10" y1="11" x2="10" y2="17"></line>
                            <line x1="14" y1="11" x2="14" y2="17"></line>
                        </svg>
                        Eliminar producto
                    </button>
                </div>
            </div>
        </div>
        <div class="flex flex-row gap-2 w-full ">
            <div class="relative aspect-square w-2/5 p-2 px-1">
                <img src="" alt="" class="w-full h-full object-cover">
                <div class="absolute inset-0 "></div>
            </div>

            <div class="px-2 pb-4 pt-2 w-3/5">
                <span class="text-gray-400 text-sm"></span>
                <!--badge category_name-->
                <span class=" badge-category text-gray-800 text-xs  font-semibold "></span>
                <h3 class="mb-0 font-bold text-md"></h3>
                <div class="flex-col flex items-start justify-between text-sm py-1">
                    <span class="text-gray-600 text-md"></span>
                    <span class="text-black-600 text-md precioPeru"></span>
                    <span class="text-black-500 text-md precioUSD"></span>
                    <span class="text-gray-500 text-md"></span>
                </div>
                <button class="btn btn-primary btnTienda mx-auto mt-4 bg-blue-50 text-blue-600 py-2 rounded-md transition-colors flex items-center justify-center gap-1 w-full">
                    <span>PASAR A TIENDA</span>
                </button>
            </div>
        </div>
    </div>
</template>
<div id="noResults" class="hidden col-span-2 lg:col-span-4 h-full">
    <div class=" rounded-lg  overflow-hidden flex flex-col items-center gap-10 justify-center h-full">
        <svg width="140" height="217" viewBox="0 0 74 107" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="4" y="40" width="66" height="66" rx="9" stroke="#D9D9D9" stroke-width="2" />
            <mask id="path-2-inside-1_3747_3546" fill="white">
                <path d="M71 97C71 102.523 66.5228 107 61 107H13C7.47715 107 3 102.523 3 97V75H27V87.1377H48V75H71V97Z" />
            </mask>
            <path d="M61 107V109V107ZM13 107V109V107ZM3 75V73H1V75H3ZM27 75H29V73H27V75ZM27 87.1377H25V89.1377H27V87.1377ZM48 87.1377V89.1377H50V87.1377H48ZM48 75V73H46V75H48ZM71 75H73V73H71V75ZM71 97H69C69 101.418 65.4183 105 61 105V107V109C67.6274 109 73 103.627 73 97H71ZM61 107V105H13V107V109H61V107ZM13 107V105C8.58172 105 5 101.418 5 97H3H1C1 103.627 6.37258 109 13 109V107ZM3 97H5V75H3H1V97H3ZM3 75V77H27V75V73H3V75ZM27 75H25V87.1377H27H29V75H27ZM27 87.1377V89.1377H48V87.1377V85.1377H27V87.1377ZM48 87.1377H50V75H48H46V87.1377H48ZM48 75V77H71V75V73H48V75ZM71 75H69V97H71H73V75H71Z" fill="#D9D9D9" mask="url(#path-2-inside-1_3747_3546)" />
            <path d="M37.0195 2V21.8129" stroke="#DFDFDE" stroke-width="3.30215" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M2 16.5117L16.0176 30.5294" stroke="#DFDFDE" stroke-width="3.30215" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M58.0212 30.5294L72.0389 16.5117" stroke="#DFDFDE" stroke-width="3.30215" stroke-linecap="round" stroke-linejoin="round" />
        </svg>

        <p class="text-gray-500">Aún no hay productos ingresados</p>
    </div>
</div>
<div class="modal fade" id="modalConfirmacion" tabindex="-1" aria-labelledby="modalConfirmacion" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfirmacionLabel">Confirmación de envio</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="p-5">Antes de enviar el o los productos a la web, selecciona la categoría a la que pertenecen</p>
                <!-- Modal body content tailwind input dropdowns seleccionar la categoria label-->
                <div class="row px-5 mb-5">
                    <div class="col-10">
                        <label for="categoriaProductos" class="block text-sm font-medium text-gray-700">Selecciona la categoría</label>
                        <select id="categoriaProductos" name="categoria" class="form-select block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">

                        </select>
                    </div>
                    <!--button to add new category-->
                    <div class="col-1">
                        <button type="button" class="btn btn-primary bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600" data-toggle="modal" data-target="#modalNuevaCategoria">
                            +
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer row">
                <button type="button" class="btn bg-white col-5
                border border-gray-300 text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-md mr-2" data-dismiss="modal">
                    <span class="">Cancelar</span>
                </button>
                <button type="button" class="btn bg-green-700 text-white col-6" id="btnGuardarCategoriaProductos">
                    <span class="ml-2">Si, Enviar</span>
                </button>

            </div>
        </div>
    </div>
</div>
<!--modal create new category-->
<div class="modal fade" id="modalNuevaCategoria" tabindex="-1" aria-labelledby="modalNuevaCategoria" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalNuevaCategoriaLabel">Crear nueva categoría</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="p-5">Ingresa el nombre de la nueva categoría</p>
                <!-- Modal body content tailwind input dropdowns seleccionar la categoria label-->
                <div class="row px-5">
                    <div class="col-12">
                        <label for="nuevaCategoria" class="block text-sm font-medium text-gray-700">Nombre de la nueva categoría</label>
                        <input type="text" id="nuevaCategoria" name="nuevaCategoria" class="form-input block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">

                    </div>
                </div>
            </div>
            <div class="modal-footer row">
                <button type="button" class="btn bg-white col-5
                border border-gray-300 text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-md mr-2" data-dismiss="modal">
                    <span class="">Cancelar
                    </span>
                </button>
                <button type="button" class="btn bg-green-700 text-white col-6" id="btnCrearCategoria" data-dismiss="modal">
                    <span class="ml-2">Crear</span>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom animations */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes shake {
        0% {
            transform: translateX(0);
        }

        25% {
            transform: translateX(-5px);
        }

        50% {
            transform: translateX(5px);
        }

        75% {
            transform: translateX(-5px);
        }

        100% {
            transform: translateX(0);
        }
    }

    /* Custom utilities */
    .animate-fadeIn {
        animation: fadeIn 0.3s ease-out;
    }

    .shake {
        animation: shake 0.5s ease-in-out;
    }

    /* Smooth transitions */
    .transition-all {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 300ms;
    }

    /* CSS for Lucide icons */
    [class^="i-lucide-"] {
        display: inline-block;
        width: 1em;
        height: 1em;
        background-color: currentColor;
        mask-repeat: no-repeat;
        mask-size: contain;
        mask-position: center;
    }

    .i-lucide-plus {
        mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M5 12h14'%3E%3C/path%3E%3Cpath d='M12 5v14'%3E%3C/path%3E%3C/svg%3E");
    }

    .i-lucide-search {
        mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='11' cy='11' r='8'%3E%3C/circle%3E%3Cpath d='m21 21-4.3-4.3'%3E%3C/path%3E%3C/svg%3E");
    }

    .i-lucide-filter {
        mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M22 3H2l8 9.46V19l4 2v-8.54L22 3z'%3E%3C/path%3E%3C/svg%3E");
    }

    .i-lucide-edit {
        mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7'%3E%3C/path%3E%3Cpath d='M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z'%3E%3C/path%3E%3C/svg%3E");
    }

    .i-lucide-trash-2 {
        mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M3 6h18'%3E%3C/path%3E%3Cpath d='M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6'%3E%3C/path%3E%3Cpath d='M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2'%3E%3C/path%3E%3Cline x1='10' x2='10' y1='11' y2='17'%3E%3C/line%3E%3Cline x1='14' x2='14' y1='11' y2='17'%3E%3C/line%3E%3C/svg%3E");
    }

    .i-lucide-check-circle {
        mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M22 11.08V12a10 10 0 1 1-5.93-9.14'%3E%3C/path%3E%3Cpolyline points='22 4 12 14.01 9 11.01'%3E%3C/polyline%3E%3C/svg%3E");
    }

    .i-lucide-alert-circle {
        mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='12' cy='12' r='10'%3E%3C/circle%3E%3Cline x1='12' x2='12' y1='8' y2='12'%3E%3C/line%3E%3Cline x1='12' x2='12.01' y1='16' y2='16'%3E%3C/line%3E%3C/svg%3E");
    }

    .i-lucide-arrow-left {
        mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M15 18l-6-6 6-6'%3E%3C/path%3E%3C/svg%3E");
    }

    .i-lucide-save {
        mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M19 21H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h12l4 4v10a2 2 0 0 1-2 2z'%3E%3C/path%3E%3Cpolyline points='17 21 17 13 7 13 7 21'%3E%3C/polyline%3E%3Cpolyline points='7 3 7 8 15.5 8'%3E%3C/polyline%3E%3C/svg%3E");
    }

    .i-lucide-x {
        mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cline x1='18' x2='6' y1='6' y2='18'%3E%3C/line%3E%3Cline x1='6' x2='18' y1='6' y2='18'%3E%3C/line%3E%3C/svg%3E");
    }

    .i-lucide-file {
        mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z'%3E%3C/path%3E%3C/svg%3E");
    }

    .i-lucide-send {
        mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M22 2 11 13'%3E%3C/path%3E%3Cpath d='M22 2l-4 20-2-8-8-2L2 2l20 20'%3E%3C/path%3E%3C/svg%3E");
    }

    #productGrid {
        height: auto;
        min-height: 40vh;
        gap: 10px;
    }

    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    body {
        font-family: 'Epilogue', sans-serif;
    }

    .dropdown-trigger {
        cursor: pointer;
        -webkit-tap-highlight-color: transparent;
        /* Elimina el resaltado azul en iOS */
    }

    .dropdown-menu {
        -webkit-overflow-scrolling: touch;
        /* Mejor scrolling en iOS */
    }
</style>