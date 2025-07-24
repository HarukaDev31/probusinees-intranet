<div class="content-wrapper">
    <main id="main-container">
        <!-- apirest/views/BaseDatos/RegulacionesView.php -->
        <div class="p-6">
            <div class="flex justify-between">
                <h2 class="text-2xl font-bold mb-4">Regulaciones aduanera</h2>
                <!--button crear producto-->
                <button class="px-4 py-2 border rounded" id="btn-crear-producto">Crear Producto</button>
            </div>
            <div class="flex gap-2 mb-4">
                <button class="px-4 py-2 border rounded">Antidumping</button>
                <button class="px-4 py-2 border rounded">Permiso</button>
                <button class="px-4 py-2 border rounded">Etiquetado</button>
                <button class="px-4 py-2 border rounded">Doc. Especiales</button>
            </div>
            <!-- Tabla principal -->
            <div class="table-responsive">

                <table id="tabla-productos" class="table table-hover dataTable no-footer ">
                    <thead class="thead-default">
                        <tr class="bg-gray-100">
                            <th class="p-2 border">N.</th>
                            <th class="p-2 border">Producto</th>
                            <th class="p-2 border">Acción</th>
                        </tr>
                    </thead>

                </table>
            </div>

            <!-- Tabla detalle (oculta por defecto) -->
            <div id="detalle-producto-1" class="hidden">
                <table id="tabla-detalle-1" class="display w-full mb-4 border rounded">
                    <thead class="thead-default">
                        <tr class="bg-gray-100">
                            <th class="p-2 border">N.</th>
                            <th class="p-2 border">Descripción</th>
                            <th class="p-2 border">Partida</th>
                            <th class="p-2 border">P. Declaracion</th>
                            <th class="p-2 border">Antidumping</th>
                            <th class="p-2 border">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="p-2 border">1</td>
                            <td class="p-2 border">CALZADO CON SUELA Y PARTE SUPERIOR DE CAUCHO O PLASTICO</td>
                            <td class="p-2 border">6402999000</td>
                            <td class="p-2 border">$7.5</td>
                            <td class="p-2 border">$0.63</td>
                            <td class="p-2 border text-center">
                                <button class="text-blue-500 mr-2">
                                    <svg class="inline w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                                <button class="text-yellow-500">
                                    <svg class="inline w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h2M12 7v10m-7 4h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td class="p-2 border">2</td>
                            <td class="p-2 border">CALZADO CON SUELA Y PARTE SUPERIOR DE CAUCHO O PLASTICO</td>
                            <td class="p-2 border">6402999000</td>
                            <td class="p-2 border">$7.5</td>
                            <td class="p-2 border">$0.63</td>
                            <td class="p-2 border text-center">
                                <button class="text-blue-500 mr-2">
                                    <svg class="inline w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                                <button class="text-yellow-500">
                                    <svg class="inline w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h2M12 7v10m-7 4h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td class="p-2 border">3</td>
                            <td class="p-2 border">CALZADO CON SUELA Y PARTE SUPERIOR DE CAUCHO O PLASTICO</td>
                            <td class="p-2 border">6402999000</td>
                            <td class="p-2 border">$7.5</td>
                            <td class="p-2 border">$0.63</td>
                            <td class="p-2 border text-center">
                                <button class="text-blue-500 mr-2">
                                    <svg class="inline w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                                <button class="text-yellow-500">
                                    <svg class="inline w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h2M12 7v10m-7 4h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <!-- Imágenes y comentarios -->
                <div class="flex gap-4">
                    <div>
                        <h3 class="font-semibold mb-2">Imágenes de productos</h3>
                        <div class="flex gap-2">
                            <img src="ruta1.jpg" class="w-24 h-24 object-cover cursor-pointer imagen-producto" alt="Producto 1">
                            <img src="ruta2.jpg" class="w-24 h-24 object-cover cursor-pointer imagen-producto" alt="Producto 2">
                            <img src="ruta3.jpg" class="w-24 h-24 object-cover cursor-pointer imagen-producto" alt="Producto 3">
                        </div>
                    </div>
                    <div>
                        <h3 class="font-semibold mb-2">Comentarios:</h3>
                        <div class="border rounded p-2 w-80">
                            Si el valor de las zapatillas está entre $7 a $13.84 le van a aplicar antidumping de $0.63.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para imagen grande -->
        <div id="modal-imagen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
            <img id="imagen-grande" src="" class="max-w-xl max-h-[80vh] rounded shadow-lg" alt="Imagen grande">
        </div>
    </main>
    <main id="create-product-container" class="hidden">
        <div class="p-6">
            <!--boton regresar-->

            <div class="bg-gray-50 min-h-screen">
                <div class="container mx-auto p-6 max-w-6xl">
                    <!-- Header -->
                    <div class="bg-white rounded-lg shadow-sm mb-6 p-6">
                        <h1 class="text-2xl font-bold text-gray-800 mb-4">
                            <i class="fas fa-box-open text-blue-600 mr-2"></i>
                            Gestión de Productos
                        </h1>

                        <!-- Navigation Tabs -->
                        <div class="flex flex-wrap gap-2 mb-4">
                            <button class="tab-btn active bg-blue-600 text-white px-4 py-2 rounded-lg font-medium transition-all hover:bg-blue-700" data-tab="antidumping">
                                <i class="fas fa-shield-alt mr-2"></i>Antidumping
                            </button>
                            <button class="tab-btn bg-gray-100 text-gray-700 px-4 py-2 rounded-lg font-medium transition-all hover:bg-gray-200" data-tab="permiso">
                                <i class="fas fa-file-contract mr-2"></i>Permiso
                            </button>
                            <button class="tab-btn bg-gray-100 text-gray-700 px-4 py-2 rounded-lg font-medium transition-all hover:bg-gray-200" data-tab="etiquetado">
                                <i class="fas fa-tags mr-2"></i>Etiquetado
                            </button>
                            <button class="tab-btn bg-gray-100 text-gray-700 px-4 py-2 rounded-lg font-medium transition-all hover:bg-gray-200" data-tab="documentos">
                                <i class="fas fa-file-alt mr-2"></i>Doc. Especiales
                            </button>
                        </div>
                    </div>

                    <!-- Main Content -->
                    <div class="tab-content" id="antidumping">
                        <div class="grid lg:grid-cols-3 gap-6">
                            <!-- Left Column - Product Details -->
                            <div class="lg:col-span-2 space-y-6">
                                <!-- Product Information Card -->
                                <div class="bg-white rounded-lg shadow-sm p-6">
                                    <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                                        Información del Producto
                                    </h2>

                                    <div class="grid md:grid-cols-2 gap-4">
                                        <div class="form-group">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                <i class="fas fa-cog mr-1"></i>T. Regulación
                                            </label>
                                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                                <option>Antidumping</option>
                                                <option>Salvaguardia</option>
                                                <option>Compensatorio</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                <i class="fas fa-box mr-1"></i>Producto
                                            </label>
                                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                                <option>Calzados</option>
                                                <option>Textiles</option>
                                                <option>Electrónicos</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-align-left mr-1"></i>Descripción
                                        </label>
                                        <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all h-20 resize-none"
                                            placeholder="Ingrese la descripción del producto...">CALZADO CON SUELA Y PARTE SUPERIOR DE CAUCHO O PLÁSTICO</textarea>
                                    </div>
                                </div>

                                <!-- Pricing Information Card -->
                                <div class="bg-white rounded-lg shadow-sm p-6">
                                    <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                        <i class="fas fa-dollar-sign text-green-600 mr-2"></i>
                                        Información de Precios
                                    </h2>

                                    <div class="grid md:grid-cols-3 gap-4">
                                        <div class="form-group">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Partida</label>
                                            <input type="text" value="6402999000" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                        </div>

                                        <div class="form-group">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">P. declarado</label>
                                            <div class="relative">
                                                <span class="absolute left-3 top-2 text-gray-500">$</span>
                                                <input type="number" value="7.5" step="0.01" class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Antidumping</label>
                                            <div class="relative">
                                                <span class="absolute left-3 top-2 text-gray-500">$</span>
                                                <input type="number" value="0.63" step="0.01" class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Product Images Card -->
                                <div class="bg-white rounded-lg shadow-sm p-6">
                                    <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                        <i class="fas fa-images text-purple-600 mr-2"></i>
                                        Imágenes de productos
                                    </h2>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <!-- Main Image -->
                                        <div class="image-upload-container relative group">
                                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-400 transition-colors cursor-pointer h-32 flex items-center justify-center bg-gray-50">
                                                <img src="/placeholder.svg?height=100&width=100" alt="Producto principal" class="max-h-full max-w-full object-contain">
                                            </div>
                                            <button class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                                <i class="fas fa-times text-xs"></i>
                                            </button>
                                        </div>

                                        <!-- Upload Slots -->
                                        <div class="image-upload-container">
                                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-400 transition-colors cursor-pointer h-32 flex flex-col items-center justify-center bg-gray-50">
                                                <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 mb-2"></i>
                                                <span class="text-sm text-gray-500">Subir imagen</span>
                                            </div>
                                        </div>

                                        <div class="image-upload-container">
                                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-400 transition-colors cursor-pointer h-32 flex flex-col items-center justify-center bg-gray-50">
                                                <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 mb-2"></i>
                                                <span class="text-sm text-gray-500">Subir imagen</span>
                                            </div>
                                        </div>
                                    </div>

                                    <input type="file" id="imageUpload" class="hidden" accept="image/*" multiple>
                                </div>
                            </div>

                            <!-- Right Column - Comments & Actions -->
                            <div class="space-y-6">
                                <!-- Comments Card -->
                                <div class="bg-white rounded-lg shadow-sm p-6">
                                    <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                        <i class="fas fa-comments text-orange-600 mr-2"></i>
                                        Comentarios
                                    </h2>

                                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg mb-4">
                                        <div class="flex items-start">
                                            <i class="fas fa-exclamation-triangle text-yellow-600 mt-1 mr-2"></i>
                                            <div>
                                                <p class="text-sm text-yellow-800">
                                                    Si el valor de las zapatillas está entre $7 a $13.84 le van aplicar antidumping de $0.63.
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-y-3">
                                        <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all h-24 resize-none"
                                            placeholder="Agregar comentario..."></textarea>
                                        <button class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                                            <i class="fas fa-plus mr-2"></i>Agregar Comentario
                                        </button>
                                    </div>
                                </div>

                                <!-- Quick Actions Card -->
                                <div class="bg-white rounded-lg shadow-sm p-6">
                                    <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                        <i class="fas fa-bolt text-yellow-600 mr-2"></i>
                                        Acciones Rápidas
                                    </h2>

                                    <div class="space-y-3">
                                        <button class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-colors">
                                            <i class="fas fa-save mr-2"></i>Guardar Producto
                                        </button>
                                        <button class="w-full bg-gray-600 text-white py-2 px-4 rounded-lg hover:bg-gray-700 transition-colors">
                                            <i class="fas fa-eye mr-2"></i>Vista Previa
                                        </button>
                                        <button class="w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition-colors">
                                            <i class="fas fa-trash mr-2"></i>Eliminar
                                        </button>
                                    </div>
                                </div>

                                <!-- Summary Card -->
                                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg shadow-sm p-6 border border-blue-200">
                                    <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                        <i class="fas fa-chart-line text-blue-600 mr-2"></i>
                                        Resumen
                                    </h2>

                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Precio declarado:</span>
                                            <span class="font-semibold">$7.50</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Antidumping:</span>
                                            <span class="font-semibold text-red-600">$0.63</span>
                                        </div>
                                        <hr class="my-2">
                                        <div class="flex justify-between text-base">
                                            <span class="font-semibold">Total:</span>
                                            <span class="font-bold text-green-600">$8.13</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Other tab contents (hidden by default) -->
                    <div class="tab-content hidden" id="permiso">
                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <h2 class="text-xl font-semibold text-gray-800 mb-4">Gestión de Permisos</h2>
                            <p class="text-gray-600">Contenido de permisos en desarrollo...</p>
                        </div>
                    </div>

                    <div class="tab-content hidden" id="etiquetado">
                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <h2 class="text-xl font-semibold text-gray-800 mb-4">Gestión de Etiquetado</h2>
                            <p class="text-gray-600">Contenido de etiquetado en desarrollo...</p>
                        </div>
                    </div>

                    <div class="tab-content hidden" id="documentos">
                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <h2 class="text-xl font-semibold text-gray-800 mb-4">Documentos Especiales</h2>
                            <p class="text-gray-600">Contenido de documentos especiales en desarrollo...</p>
                        </div>
                    </div>
                </div>

                <script src="script.js"></script>
            </div>


        </div>
    </main>
</div>
<style>
    .table.table-hover.dataTable.no-footer tbody {
        background-color: white;
    }

    tr.odd>td,
    tr.even>td {
        border-top: 4px solid #f4f6f9;
        border-bottom: 4px solid #f4f6f9;
        vertical-align: middle !important;
        height: 3vh;
    }

    th.sorting_disabled {
        font-weight: normal !important;
    }

    .table thead th {
        /* vertical-align: bottom !important; */
        border-bottom: 0px solid #dee2e6 !important;
        border-top: 0px solid #dee2e6;
    }


    div.dataTables_wrapper div.dataTables_paginate ul.pagination {
        margin: 2px 0;
        white-space: nowrap;
        justify-content: flex-start;
    }

    div#table-contenedor_length {
        display: none;
    }
</style>