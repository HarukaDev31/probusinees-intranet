<script>
  var currentPrivilege = "<?php echo $this->user->No_Grupo; ?>";
  localStorage.setItem("currentPrivilege", currentPrivilege);
</script>
<div class="content-wrapper">
    <!--Main Content-->
    <section class="content" id="productListSection">
        <div class="container-fluid">
            <div class="flex items-center justify-between mb-8">
                <h1 class="text-2xl font-bold text-gray-800">Productos</h1>

                <button id="btnAddProduct" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-6 rounded-md transition-colors flex items-center justify-center shadow-sm">
                    <span class="i-lucide-plus mr-2"></span>
                    <span>Agregar Producto</span>
                </button>
            </div>

            <!-- Search and Filter Bar -->
            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 mb-6">
                <div class="flex flex-col md:flex-row gap-4">
                    <!-- Search -->
                    <div class="flex-1">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                                <span class="i-lucide-search w-5 h-5"></span>
                            </span>
                            <input type="text" id="searchInput"
                                placeholder="Buscar productos..."
                                class="pl-10 pr-4 py-2 w-full border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="flex gap-4">
                        <select id="sortSelect" class="border border-gray-300 rounded-md px-4 py-2 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <option value="">Ordenar por</option>
                            <option value="name">Nombre</option>
                            <option value="price">Precio</option>
                            <option value="date">Fecha</option>
                        </select>

                        <button id="filterBtn" class="border border-gray-300 rounded-md px-4 py-2 flex items-center gap-2 hover:bg-gray-50 transition-colors">
                            <span class="i-lucide-filter w-5 h-5"></span>
                            <span>Filtros</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Product Grid -->
            <div id="productGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <!-- Skeleton Template -->


                <!-- Product Card Template -->

            </div>
        </div>
    </section>
    <!-- Add Product Form -->
    <section class="content hidden" id="productoFormSection">
        <div class="container-fluid">
            <div class="flex items-center justify-between mb-6">
                <button 
                id="btnBack"
                type="button" id="btn-back-documentacion-documentacion" class="py-1 px-2 bg-white hover:bg-white-200 text-black-200 py-2 px-2 border border-transparent hover:border-orange-600 rounded " data-type="html"><i class="fa fa-arrow-left"></i> Regresar</button>


                <button id="btnSave" class="bg-orange-500 hover:bg-orange-600 text-white py-2 px-6 rounded-md transition-colors flex items-center justify-center shadow-sm">
                    <span>Guardar</span>
                    <span class="i-lucide-save ml-2"></span>
                </button>
            </div>

        </div>
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
            <!-- Left Column - Images -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-4 border-b border-gray-100">
                        <h2 class="text-lg font-medium text-gray-800">Imágenes</h2>
                    </div>

                    <div class="p-4">
                        <!-- Main Image Upload -->
                        <div id="mainImageContainer" class="relative bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg mb-4 transition-all hover:bg-gray-100 hover:border-blue-300">

                        </div>

                        <!-- Additional Images -->
                        <div class="grid grid-cols-3 gap-3">
                            <div id="additionalImage1Container" class="additional-image-container relative bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg transition-all hover:bg-gray-100 hover:border-blue-300">

                            </div>

                            <div id="additionalImage2Container" class="additional-image-container relative bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg transition-all hover:bg-gray-100 hover:border-blue-300">

                            </div>

                            <div id="additionalVideo1Container" class="video-container relative bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg transition-all hover:bg-gray-100 hover:border-blue-300">

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Product Details Form -->
            <div class="lg:col-span-3">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
                    <div class="p-4 border-b border-gray-100 flex items-center">
                        <h2 class="text-lg font-medium text-gray-800">Detalles del Producto</h2>
                        <div class="ml-2 tooltip" data-tooltip="Información obligatoria para guardar el producto">
                            <span class="i-lucide-info text-blue-500 w-5 h-5"></span>
                        </div>
                    </div>

                    <div class="p-4">
                        <form id="productForm">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <!-- Nombre -->
                                <div class="form-group col-span-2">
                                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">
                                        Nombre <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="nombre" name="nombre"
                                        class="form-input block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors"
                                        required>
                                    <p class="form-error text-red-500 text-xs mt-1 hidden">Este campo es obligatorio.</p>
                                </div>

                                <!-- Precio -->
                                <div class="form-group">
                                    <label for="precio" class="block text-sm font-medium text-gray-700 mb-1">
                                        Precio <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">¥</span>
                                        <input type="text" id="precio" name="precio"
                                            class="form-input block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 pl-8 transition-colors"
                                            placeholder="0.00" required>
                                    </div>
                                    <p class="form-error text-red-500 text-xs mt-1 hidden">Ingrese solo valores numéricos.</p>
                                </div>
                                <div class="form-group">
                                    <label for="profit" class="block text-sm font-medium text-gray-700 mb-1">
                                        Profit <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">¥</span>
                                        <input type="text" id="profit" 
                                            class="form-input block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 pl-8 transition-colors"
                                            placeholder="0.00" disabled
                                            value="3.00">
                                    </div>
                                </div>
                                <!-- MOQ -->
                                <div class="form-group">
                                    <label for="moq" class="block text-sm font-medium text-gray-700 mb-1">
                                        MOQ <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" id="moq" name="moq" min="1"
                                        class="form-input block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors"
                                        required>
                                    <p class="form-error text-red-500 text-xs mt-1 hidden">Ingrese un valor numérico válido.</p>
                                </div>

                                <!-- Qty x box -->
                                <div class="form-group">
                                    <label for="qtyXbox" class="block text-sm font-medium text-gray-700 mb-1">
                                        Qty x box <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" id="qtyXbox" name="qtyXbox" min="1"
                                        class="form-input block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors"
                                        required>
                                    <p class="form-error text-red-500 text-xs mt-1 hidden">Ingrese un valor numérico válido.</p>
                                </div>

                                <!-- Cbm x box -->
                                <div class="form-group">
                                    <label for="cbmXbox" class="block text-sm font-medium text-gray-700 mb-1">
                                        Cbm x box <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" id="cbmXbox" name="cbmXbox" min="0" step="0.001"
                                        class="form-input block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors"
                                        required>
                                    <p class="form-error text-red-500 text-xs mt-1 hidden">Ingrese un valor numérico válido.</p>
                                </div>

                                <!-- Días Entrega -->
                                <div class="form-group">
                                    <label for="diasEntrega" class="block text-sm font-medium text-gray-700 mb-1">
                                        Días Entrega <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" id="diasEntrega" name="diasEntrega" min="1"
                                        class="form-input block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors"
                                        required>
                                    <p class="form-error text-red-500 text-xs mt-1 hidden">Ingrese un valor numérico válido.</p>
                                </div>

                                <!-- Delivery -->
                                <div class="form-group">
                                    <label for="delivery" class="block text-sm font-medium text-gray-700 mb-1">
                                        Delivery <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">¥</span>
                                        <input type="text" id="delivery" name="delivery"
                                            class="form-input block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 pl-8 transition-colors"
                                            placeholder="0.00" required>
                                    </div>
                                    <p class="form-error text-red-500 text-xs mt-1 hidden">Ingrese solo valores numéricos.</p>
                                </div>

                                <!-- Colores -->
                                <div class="form-group col-span-2">
                                    <label for="colores" class="block text-sm font-medium text-gray-700 mb-1">
                                        Colores
                                    </label>
                                    <input type="text" id="colores" name="colores"
                                        class="form-input block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                                </div>

                                <!-- Notas -->
                                <div class="form-group col-span-2">
                                    <label for="notas" class="block text-sm font-medium text-gray-700 mb-1">
                                        Notas
                                    </label>
                                    <textarea id="notas" name="notas" rows="4"
                                        class="form-input block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors"
                                        placeholder="Información adicional..."></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Provider Data -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden ">
                    <div class="p-4 border-b border-gray-100">
                        <h2 class="text-lg font-medium text-gray-800">Datos del Proveedor</h2>
                    </div>

                    <div class="p-4">
                        <form id="providerForm">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- WeChat / Phone -->
                                <div class="form-group">
                                    <label for="wechatPhone" class="block text-sm font-medium text-gray-700 mb-1">
                                        WeChat / Phone
                                    </label>
                                    <input type="text" id="wechatPhone" name="wechatPhone" required
                                        class="form-input block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                                        <p class="form-error text-red-500 text-xs mt-1 hidden">Este campo es obligatorio.</p>

                                    </div>

                                <!-- Contact Card -->
                                <div class="form-group">
                                    <label for="contactCard" class="block text-sm font-medium text-gray-700 mb-1">
                                        Tarjeta de contacto
                                    </label>
                                    <div id="contactCardContainer" class="relative bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg transition-all hover:bg-gray-100 hover:border-blue-300">

                                    </div>
                                    <p class="text-xs text-blue-600 mt-1 cursor-pointer hover:underline">
                                        <span class="i-lucide-info mr-1"></span>
                                        Puede darle clic y ver
                                    </p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- both columns -->
            <div class="lg:col-span-5">

            <div class=" bg-white rounded-lg shadow-lg p-6">
                <h1 class="text-2xl font-bold mb-6">COSTOS DE IMPORTACIÓN:</h1>
                
                <!-- Input Fields Section -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                    <div class="input-group">
                        <label class="block text-sm font-medium text-gray-700">Precio USD:</label>
                        <input 
                        disabled
                        type="number" id="precioUSD" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" step="0.01">
                    </div>
        
                    
                    <div class="input-group">
                        <label class="block text-sm font-medium text-gray-700">Total USD:</label>
                        <input type="number" id="totalUSD" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" readonly>
                    </div>
                    
                    <div class="input-group">
                        <label class="block text-sm font-medium text-gray-700">Total CBM:</label>
                        <input type="number" id="totalCBM" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" step="0.01">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                    <div class="input-group">
                        <label class="block text-sm font-medium text-gray-700">Servicio Impo:</label>
                        <input type="number" id="servicioImpo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" value="350.00" readonly>
                    </div>
                    
                    <div class="input-group">
                        <!-- <label class="block text-sm font-medium text-gray-700">*Arancel:</label>
                        <select id="arancel" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="0">0%</option>
                            <option value="6">6%</option>
                            <option value="11">11%</option>
                        </select> -->
                    </div>
                    
                    <div class="input-group">
                        <label class="block text-sm font-medium text-gray-700">IGV:</label>
                        <input type="number" id="igv" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" value="18" readonly>
                    </div>
                    
                    <div class="input-group">
                        <label class="block text-sm font-medium text-gray-700">*Antidumping:</label>
                        <input type="number" id="antidumping" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" value="0">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                    <div class="input-group">
                        <label class="block text-sm font-medium text-gray-700">Percepción:</label>
                        <input type="number" id="percepcion" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500" value="3.5" readonly>
                    </div>
                </div>

                <!-- Calculation Tables -->
                <div class="grid grid-cols-1 md:grid-cols-2">
                    <!-- Base Imponible Table -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h2 class="text-lg font-semibold mb-4 bg-teal-600 text-white p-2">CÁLCULO DE BASE IMPONIBLE</h2>
                        <table class="w-full">
                            <tr>
                                <td class="py-2">Valor de carga</td>
                                <td class="py-2 text-right" id="valorCarga">$ 0.00</td>
                            </tr>
                            <tr>
                                <td class="py-2">Flete</td>
                                <td class="py-2 text-right" id="flete">$ 0.00</td>
                            </tr>
                            <tr>
                                <td class="py-2">Seguro</td>
                                <td class="py-2 text-right" id="seguro">$ 0.00</td>
                            </tr>
                            <tr class="font-bold">
                                <td class="py-2">VALOR CIF</td>
                                <td class="py-2 text-right" id="valorCIF">$ 0.00</td>
                            </tr>
                        </table>
                    </div>

                    <!-- Resumen Table -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h2 class="text-lg font-semibold mb-4 bg-orange-500 text-white p-2">RESUMEN DE COTIZACIÓN</h2>
                        <table class="w-full">
                            <tr>
                                <td class="py-2">Valor de carga (pago China)</td>
                                <td class="py-2 text-right" id="valorCargaResumen">$ 0.00</td>
                            </tr>
                            <tr>
                                <td class="py-2">Servicio trading (pago China)</td>
                                <td class="py-2 text-right" id="servicioTrading">$ 0.00</td>
                            </tr>
                            <tr>
                                <td class="py-2">Servicio importación</td>
                                <td class="py-2 text-right" id="servicioImportacion">$ 0.00</td>
                            </tr>
                            <tr>
                                <td class="py-2">Impuestos</td>
                                <td class="py-2 text-right" id="impuestos">$ 0.00</td>
                            </tr>
                            <tr class="font-bold">
                                <td class="py-2">MONTO TOTAL</td>
                                <td class="py-2 text-right" id="montoTotal">$ 0.00</td>
                            </tr>
                        </table>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h2 class="text-lg font-semibold mb-4 bg-teal-600 text-white p-2">CÁLCULOS DE TRIBUTOS</h2>
                        <table class="w-full">
                            <!-- ADD FOR AD VALOREM IGV IPM ANTIDUMPING SUBTOTAL IN BOLD, PERCEPCION AND TOTAL INPUTS-->
                            <tr>
                                <td class="py-2">Ad Valorem</td>
                                <td class="py-2 text-right" id="adValorem">$ 0.00</td>
                            </tr>
                            <tr>
                                <td class="py-2">IGV</td>
                                <td class="py-2 text-right" id="igvTotal">$ 0.00</td>
                            </tr>
                            <tr>
                                <td class="py-2">IPM</td>
                                <td class="py-2 text-right" id="ipmTotal">$ 0.00</td>
                            </tr>
                            <tr>
                                <td class="py-2">Antidumping</td>
                                <td class="py-2 text-right" id="antidumpingTotal">$ 0.00</td>
                            </tr>
                            <tr class="font-bold">
                                <td class="py-2">Subtotal</td>
                                <td class="py-2 text-right" id="subtotal">$ 0.00</td>
                            </tr>
                            <tr>
                                <td class="py-2">Percepción</td>
                                <td class="py-2 text-right" id="percepcionTotal">$ 0.00</td>
                            </tr>
                            <tr class="font-bold">
                                <td class="py-2">TOTAL</td>
                                <td class="py-2 text-right" id="total">$ 0.00</td>
                            </tr>
                            <!-- costo en destiono -->
                            <tr class="font-bold">
                                <td class="py-2">Costo en destino</td>
                                <td class="py-2 text-right" id="costoDestino">$ 0.00</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Cost Results -->
                <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-orange-500 text-white p-4 rounded-lg">
                        <div class="flex justify-between items-center">
                            <span>COSTO UNITARIO DE IMPORTACIÓN</span>
                            <span id="costoUnitarioUSD">$ 0.00</span>
                        </div>
                    </div>
                    <div class="bg-orange-500 text-white p-4 rounded-lg">
                        <div class="flex justify-between items-center">
                            <span>COSTO UNITARIO DE IMPORTACIÓN</span>
                            <span id="costoUnitarioPEN">S/ 0.00</span>
                        </div>
                    </div>
                </div>

                <!-- Help Text -->
                <div class="mt-8 text-sm text-gray-600">
                    <h3 class="font-bold text-red-600 mb-2">COSTOS DE IMPORTACIÓN:</h3>
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Cuando quieran sacar los costos tienen que llenar obligatoriamente lo siguiente:</li>
                        <li>Arancel: Tenga la opción de seleccionar o escribir *0% - 6% - 11%</li>
                        <li>*Puede escribir en porcentaje</li>
                        <li>Antidumping: Le permita colocar valor y el signo en dólares</li>
                        <li>Precio USD: Es la suma de precio en yuanes + profit y divido entre 7</li>
                        <li>Total USD: Es la multiplicación del MOQ x el PRECIO USD.</li>
                        <li>Total CBM: Es la división de MOQ / QTY box, luego el resultado lo multiplica por el CBM x box</li>
                        <li>Servicio de impo: por defecto sale $350</li>
                        <li>IGV: sale por defecto 18%</li>
                        <li>Percepción: Sale por defecto 3.5%</li>
                        <li>Costo unitario de importación en soles: Siempre será divido entre 3.8</li>
                    </ul>
                </div>
            </div>
            </div>
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
        <div class="bg-white card rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow group">
            <div class="relative aspect-square">
                <img src="" alt="" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-opacity"></div>
            </div>

            <div class="p-4">
                <h3 class="font-medium text-gray-800 mb-2"></h3>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 font-bold text-lg"></span>
                    <span class="text-gray-500 font-bold text-lg"></span>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="flex items-center justify-center gap-2">
                        <button class="edit-btn flex-1 bg-blue-50 hover:bg-blue-100 text-blue-600 py-2 rounded-md transition-colors flex items-center justify-center gap-1">
                            <span class="i-lucide-edit w-4 h-4"></span>
                            <span>Editar</span>
                        </button>
                        <button class="delete-btn flex-1 bg-red-50 hover:bg-red-100 text-red-600 py-2 rounded-md transition-colors flex items-center justify-center gap-1">
                            <span class="i-lucide-trash-2 w-4 h-4"></span>
                            <span>Eliminar</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
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

    #productGrid {
        height: auto;

    }
    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

</style>