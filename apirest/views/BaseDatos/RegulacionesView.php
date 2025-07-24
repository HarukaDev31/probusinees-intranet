<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos - Mejorado</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto p-6 max-w-6xl">
        <!-- Header with Product Selection -->
        <div class="bg-white rounded-lg shadow-sm mb-6 p-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                <div class="flex items-center">
                    <i class="fas fa-box-open text-blue-600 mr-3 text-2xl"></i>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Gestión de Productos</h1>
                        <p class="text-gray-600 text-sm">Administra la información completa del producto seleccionado</p>
                    </div>
                </div>

                <!-- Product Selector -->
                <div class="lg:w-80">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-search mr-1"></i>Producto Seleccionado
                    </label>
                    <select id="productSelector" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all bg-white shadow-sm">
                        <option value="calzados">Calzados</option>
                        <option value="motos-electricas">Motos Eléctricas</option>
                        <option value="textiles">Textiles</option>
                        <option value="electronicos">Electrónicos</option>
                        <option value="juguetes">Juguetes</option>
                    </select>
                </div>
            </div>

            <!-- Product Status Bar -->
            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                    <i class="fas fa-chart-line mr-2"></i>Estado de Completitud por Sección
                </h3>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                    <div class="status-indicator" data-tab="antidumping">
                        <div class="flex items-center justify-between p-3 bg-white rounded-lg border-l-4 border-blue-500 shadow-sm">
                            <div class="flex items-center">
                                <i class="fas fa-shield-alt text-blue-600 mr-2"></i>
                                <span class="text-sm font-medium">Antidumping</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                                <span class="text-xs text-gray-600">85%</span>
                            </div>
                        </div>
                    </div>

                    <div class="status-indicator" data-tab="permiso">
                        <div class="flex items-center justify-between p-3 bg-white rounded-lg border-l-4 border-green-500 shadow-sm">
                            <div class="flex items-center">
                                <i class="fas fa-file-contract text-green-600 mr-2"></i>
                                <span class="text-sm font-medium">Permiso</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></div>
                                <span class="text-xs text-gray-600">60%</span>
                            </div>
                        </div>
                    </div>

                    <div class="status-indicator" data-tab="etiquetado">
                        <div class="flex items-center justify-between p-3 bg-white rounded-lg border-l-4 border-purple-500 shadow-sm">
                            <div class="flex items-center">
                                <i class="fas fa-tags text-purple-600 mr-2"></i>
                                <span class="text-sm font-medium">Etiquetado</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-red-500 rounded-full mr-2"></div>
                                <span class="text-xs text-gray-600">25%</span>
                            </div>
                        </div>
                    </div>

                    <div class="status-indicator" data-tab="documentos">
                        <div class="flex items-center justify-between p-3 bg-white rounded-lg border-l-4 border-orange-500 shadow-sm">
                            <div class="flex items-center">
                                <i class="fas fa-file-alt text-orange-600 mr-2"></i>
                                <span class="text-sm font-medium">Doc. Especiales</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-gray-400 rounded-full mr-2"></div>
                                <span class="text-xs text-gray-600">0%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Tabs -->
            <div class="flex flex-wrap gap-2">
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
                <div class="lg:col-span-3 space-y-6">
                    <!-- Product Information Card -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                            Información de Antidumping
                        </h2>


                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-align-left mr-1"></i>Descripción del Producto
                            </label>
                            <textarea class="product-description w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all h-20 resize-none"
                                placeholder="Ingrese la descripción del producto...">CALZADO CON SUELA Y PARTE SUPERIOR DE CAUCHO O PLÁSTICO</textarea>
                        </div>
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
                    <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-comments text-orange-600 mr-2"></i>
                        Comentarios
                    </h2>

                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg mb-4">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-600 mt-1 mr-2"></i>
                            <div>
                                <p class="text-sm text-blue-800">
                                    Si el valor de las zapatillas está entre $7 a $13.84 le van aplicar antidumping de $0.63.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all h-24 resize-none"
                            placeholder="Agregar observaciones sobre el permiso..."></textarea>
                        <button class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>Agregar Comentario
                        </button>
                    </div>
                </div>

                </div>
              
            </div>
        </div>

        <!-- Permiso tab content -->
        <div class="tab-content hidden" id="permiso">
            <div class="grid lg:grid-cols-3 gap-6">
                <!-- Left Column - Permit Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Permit Information Card -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-file-contract text-blue-600 mr-2"></i>
                            Información del Permiso
                        </h2>

                        <div class="grid md:grid-cols-2 gap-4 mb-4">
                           

                            <div class="form-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-building mr-1"></i>Entidad
                                </label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                    <option>MTC</option>
                                    <option>MINSA</option>
                                    <option>PRODUCE</option>
                                    <option>MINCETUR</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-tag mr-1"></i>Nombre del permiso
                            </label>
                            <input type="text" class="permit-name w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" placeholder="Nombre del permiso para el producto seleccionado">
                        </div>

                        <div class="grid md:grid-cols-3 gap-4">
                            <div class="form-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-barcode mr-1"></i>C. Permiso
                                </label>
                                <input type="text" value="PRM-2024-001" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            </div>

                            <div class="form-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-dollar-sign mr-1"></i>Costo Base
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-gray-500">S/.</span>
                                    <input type="number" value="90" step="0.01" class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-user-tie mr-1"></i>C. Tramitador
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-gray-500">S/.</span>
                                    <input type="number" value="50" step="0.01" class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Documents Card -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-folder-open text-purple-600 mr-2"></i>
                            Documentos
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- Document Upload Area -->
                            <div class="document-upload-container relative group col-span-3">
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors cursor-pointer h-32 flex flex-col items-center justify-center bg-gray-50">
                                    <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                    <span class="text-sm text-gray-500">Arrastrar documentos aquí o hacer clic para seleccionar</span>
                                    <span class="text-xs text-gray-400 mt-1">PDF, DOC, DOCX, JPG, PNG (Max. 10MB)</span>
                                </div>
                            </div>

                            <!-- Add Document Button -->
                            <div class="flex items-center justify-center">
                                <button class="add-document-btn w-16 h-16 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center">
                                    <i class="fas fa-plus text-xl"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Document List -->
                        <div class="mt-4 space-y-2" id="documentList">
                            <!-- Documents will be added here dynamically -->
                        </div>

                        <input type="file" id="documentUpload" class="hidden" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" multiple>
                    </div>

                    <!-- Comments Card -->

                </div>

                <!-- Right Column - Help & Actions -->
                <div class="space-y-6">
                    <!-- Help Information Card -->
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg shadow-sm p-6 border border-green-200">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-question-circle text-green-600 mr-2"></i>
                            Información de Ayuda
                        </h2>

                        <div class="space-y-4 text-sm">
                            <div>
                                <h3 class="font-semibold text-gray-700 mb-2">T. Producto:</h3>
                                <p class="text-gray-600">Le salga un historial de productos ya agregados para que los una</p>
                            </div>

                            <div>
                                <h3 class="font-semibold text-gray-700 mb-2">T. Regulación:</h3>
                                <p class="text-gray-600 mb-2">Le sale las siguientes opciones:</p>
                                <ul class="list-disc list-inside text-gray-600 space-y-1 ml-2">
                                    <li>Antidumping</li>
                                    <li>Permiso</li>
                                    <li>Etiquetado</li>
                                    <li>Doc. Especiales</li>
                                </ul>
                            </div>

                            <div>
                                <h3 class="font-semibold text-gray-700 mb-2">Documentos:</h3>
                                <p class="text-gray-600">Puede agregar documentos relacionados al permiso</p>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions Card -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-bolt text-yellow-600 mr-2"></i>
                            Acciones del Permiso
                        </h2>

                        <div class="space-y-3">
                            <button class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-save mr-2"></i>Guardar Permiso
                            </button>
                            <button class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-file-pdf mr-2"></i>Generar PDF
                            </button>
                            <button class="w-full bg-purple-600 text-white py-2 px-4 rounded-lg hover:bg-purple-700 transition-colors">
                                <i class="fas fa-paper-plane mr-2"></i>Enviar Solicitud
                            </button>
                            <button class="w-full bg-gray-600 text-white py-2 px-4 rounded-lg hover:bg-gray-700 transition-colors">
                                <i class="fas fa-eye mr-2"></i>Vista Previa
                            </button>
                        </div>
                    </div>

                    <!-- Cost Summary Card -->
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg shadow-sm p-6 border border-blue-200">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-calculator text-blue-600 mr-2"></i>
                            Resumen de Costos
                        </h2>

                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Costo base:</span>
                                <span class="font-semibold">S/. 90.00</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tramitador:</span>
                                <span class="font-semibold">S/. 50.00</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">IGV (18%):</span>
                                <span class="font-semibold">S/. 25.20</span>
                            </div>
                            <hr class="my-2">
                            <div class="flex justify-between text-base">
                                <span class="font-semibold">Total:</span>
                                <span class="font-bold text-green-600">S/. 165.20</span>
                            </div>
                        </div>
                    </div>

                    <!-- Status Card -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-tasks text-indigo-600 mr-2"></i>
                            Estado del Permiso
                        </h2>

                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Estado actual:</span>
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">
                                    En Proceso
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Progreso:</span>
                                <span class="text-sm font-semibold">60%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: 60%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Etiquetado tab content -->
        <div class="tab-content hidden" id="etiquetado">
            <div class="grid lg:grid-cols-3 gap-6">
                <!-- Left Column - Labeling Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Labeling Information Card -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-tags text-blue-600 mr-2"></i>
                            Información de Etiquetado
                        </h2>

                        
                    </div>

                    <!-- Product Images Card -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-images text-purple-600 mr-2"></i>
                            Imágenes de productos
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- Main Upload Area -->
                            <div class="label-image-upload-container relative group col-span-3">
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors cursor-pointer h-32 flex flex-col items-center justify-center bg-gray-50">
                                    <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                    <span class="text-sm text-gray-500">Subir imágenes del producto</span>
                                    <span class="text-xs text-gray-400 mt-1">JPG, PNG, GIF (Max. 5MB)</span>
                                </div>
                            </div>

                            <!-- Add Image Button -->
                            <div class="flex items-center justify-center">
                                <button class="add-label-image-btn w-16 h-16 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center justify-center">
                                    <i class="fas fa-plus text-xl"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Image List -->
                        <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-3" id="labelImageList">
                            <!-- Images will be added here dynamically -->
                        </div>

                        <input type="file" id="labelImageUpload" class="hidden" accept="image/*" multiple>
                    </div>

                    <!-- Minimum Descriptions Card -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-align-left text-green-600 mr-2"></i>
                            Descripciones mínimas
                        </h2>

                        <div class="space-y-4">
                            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg">
                                <div class="flex items-start">
                                    <i class="fas fa-info-circle text-blue-600 mt-1 mr-2"></i>
                                    <div>
                                        <p class="text-sm text-blue-800">
                                            Si el valor de las zapatillas está entre $7 a $13.84 le van aplicar antidumping de $0.63.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <label class="block text-sm font-medium text-gray-700">
                                    Descripción adicional del etiquetado:
                                </label>
                                <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all h-32 resize-none"
                                    placeholder="Ingrese descripciones adicionales para el etiquetado del producto..."></textarea>
                                <button class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-colors">
                                    <i class="fas fa-save mr-2"></i>Guardar Descripción
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Help & Actions -->
                <div class="space-y-6">
                    <!-- Labeling Requirements Card -->
                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg shadow-sm p-6 border border-purple-200">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-clipboard-list text-purple-600 mr-2"></i>
                            Requisitos de Etiquetado
                        </h2>

                        <div class="space-y-3 text-sm">
                            <div class="flex items-start">
                                <i class="fas fa-check-circle text-green-600 mt-1 mr-2"></i>
                                <span class="text-gray-700">Nombre del producto</span>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-check-circle text-green-600 mt-1 mr-2"></i>
                                <span class="text-gray-700">País de origen</span>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-check-circle text-green-600 mt-1 mr-2"></i>
                                <span class="text-gray-700">Composición del material</span>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-check-circle text-green-600 mt-1 mr-2"></i>
                                <span class="text-gray-700">Instrucciones de cuidado</span>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-check-circle text-green-600 mt-1 mr-2"></i>
                                <span class="text-gray-700">Tallas disponibles</span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions Card -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-bolt text-yellow-600 mr-2"></i>
                            Acciones de Etiquetado
                        </h2>

                        <div class="space-y-3">
                            <button class="w-full bg-purple-600 text-white py-2 px-4 rounded-lg hover:bg-purple-700 transition-colors">
                                <i class="fas fa-tags mr-2"></i>Generar Etiquetas
                            </button>
                            <button class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-file-pdf mr-2"></i>Exportar PDF
                            </button>
                            <button class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-save mr-2"></i>Guardar Etiquetado
                            </button>
                            <button class="w-full bg-gray-600 text-white py-2 px-4 rounded-lg hover:bg-gray-700 transition-colors">
                                <i class="fas fa-eye mr-2"></i>Vista Previa
                            </button>
                        </div>
                    </div>

                    <!-- Label Templates Card -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-layer-group text-indigo-600 mr-2"></i>
                            Plantillas de Etiquetas
                        </h2>

                        <div class="space-y-3">
                            <div class="p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium">Calzado Deportivo</span>
                                    <i class="fas fa-download text-blue-600"></i>
                                </div>
                            </div>
                            <div class="p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium">Calzado Formal</span>
                                    <i class="fas fa-download text-blue-600"></i>
                                </div>
                            </div>
                            <div class="p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium">Calzado Infantil</span>
                                    <i class="fas fa-download text-blue-600"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Compliance Status Card -->
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg shadow-sm p-6 border border-green-200">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-shield-check text-green-600 mr-2"></i>
                            Estado de Cumplimiento
                        </h2>

                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Etiquetado:</span>
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                    Completo
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Imágenes:</span>
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">
                                    Pendiente
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Descripciones:</span>
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                    Completo
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Documentos Especiales tab content -->
        <div class="tab-content hidden" id="documentos">
            <div class="grid lg:grid-cols-3 gap-6">
                <!-- Left Column - Special Documents Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Special Documents Information Card -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-file-alt text-blue-600 mr-2"></i>
                            Información de Documentos Especiales
                        </h2>

                       
                    </div>

                    <!-- Special Documents Upload Card -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-folder-open text-orange-600 mr-2"></i>
                            Documentos
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- Document Upload Area -->
                            <div class="special-document-upload-container relative group col-span-3">
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors cursor-pointer h-32 flex flex-col items-center justify-center bg-gray-50">
                                    <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                    <span class="text-sm text-gray-500">Subir documentos especiales</span>
                                    <span class="text-xs text-gray-400 mt-1">PDF, DOC, DOCX, XLS, XLSX (Max. 15MB)</span>
                                </div>
                            </div>

                            <!-- Add Document Button -->
                            <div class="flex items-center justify-center">
                                <button class="add-special-document-btn w-16 h-16 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors flex items-center justify-center">
                                    <i class="fas fa-plus text-xl"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Document List -->
                        <div class="mt-4 space-y-2" id="specialDocumentList">
                            <!-- Documents will be added here dynamically -->
                        </div>

                        <input type="file" id="specialDocumentUpload" class="hidden" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" multiple>
                    </div>

                    <!-- Comments Card -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-comments text-green-600 mr-2"></i>
                            Comentarios
                        </h2>

                        <div class="space-y-4">
                            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg">
                                <div class="flex items-start">
                                    <i class="fas fa-info-circle text-blue-600 mt-1 mr-2"></i>
                                    <div>
                                        <p class="text-sm text-blue-800">
                                            Si el valor de las zapatillas está entre $7 a $13.84 le van aplicar antidumping de $0.63.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <label class="block text-sm font-medium text-gray-700">
                                    Observaciones sobre documentos especiales:
                                </label>
                                <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all h-32 resize-none"
                                    placeholder="Agregar comentarios sobre los documentos especiales requeridos..."></textarea>
                                <button class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-plus mr-2"></i>Agregar Comentario
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Help & Actions -->
                <div class="space-y-6">
                    <!-- Document Types Card -->
                    <div class="bg-gradient-to-r from-orange-50 to-red-50 rounded-lg shadow-sm p-6 border border-orange-200">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-list-check text-orange-600 mr-2"></i>
                            Tipos de Documentos
                        </h2>

                        <div class="space-y-3 text-sm">
                            <div class="flex items-start">
                                <i class="fas fa-certificate text-blue-600 mt-1 mr-2"></i>
                                <span class="text-gray-700">Certificados de calidad</span>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-file-medical text-green-600 mt-1 mr-2"></i>
                                <span class="text-gray-700">Registros sanitarios</span>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-shield-check text-purple-600 mt-1 mr-2"></i>
                                <span class="text-gray-700">Certificados de seguridad</span>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-leaf text-green-600 mt-1 mr-2"></i>
                                <span class="text-gray-700">Certificados ambientales</span>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-globe text-blue-600 mt-1 mr-2"></i>
                                <span class="text-gray-700">Certificados de origen</span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions Card -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-bolt text-yellow-600 mr-2"></i>
                            Acciones de Documentos
                        </h2>

                        <div class="space-y-3">
                            <button class="w-full bg-orange-600 text-white py-2 px-4 rounded-lg hover:bg-orange-700 transition-colors">
                                <i class="fas fa-file-upload mr-2"></i>Subir Lote
                            </button>
                            <button class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-compress-arrows-alt mr-2"></i>Comprimir Todo
                            </button>
                            <button class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-save mr-2"></i>Guardar Documentos
                            </button>
                            <button class="w-full bg-purple-600 text-white py-2 px-4 rounded-lg hover:bg-purple-700 transition-colors">
                                <i class="fas fa-paper-plane mr-2"></i>Enviar para Revisión
                            </button>
                        </div>
                    </div>

                    <!-- Document Status Card -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-tasks text-indigo-600 mr-2"></i>
                            Estado de Documentos
                        </h2>

                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Certificados:</span>
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">
                                    Pendiente
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Registros:</span>
                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">
                                    Faltante
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Validación:</span>
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium">
                                    No iniciado
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-3">
                                <div class="bg-orange-600 h-2 rounded-full" style="width: 25%"></div>
                            </div>
                            <div class="text-center text-sm text-gray-600">25% Completado</div>
                        </div>
                    </div>

                    <!-- Required Documents Checklist -->
                    <div class="bg-gradient-to-r from-red-50 to-pink-50 rounded-lg shadow-sm p-6 border border-red-200">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-clipboard-check text-red-600 mr-2"></i>
                            Documentos Requeridos
                        </h2>

                        <div class="space-y-2 text-sm">
                            <div class="flex items-center">
                                <input type="checkbox" class="mr-2 text-blue-600">
                                <span class="text-gray-700">Certificado CE</span>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" class="mr-2 text-blue-600">
                                <span class="text-gray-700">Manual de usuario</span>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" class="mr-2 text-blue-600" checked>
                                <span class="text-gray-700">Ficha técnica</span>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" class="mr-2 text-blue-600">
                                <span class="text-gray-700">Certificado de batería</span>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" class="mr-2 text-blue-600">
                                <span class="text-gray-700">Registro SUNAT</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>

</html>