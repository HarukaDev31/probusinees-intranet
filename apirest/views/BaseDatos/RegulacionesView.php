<div class="content-wrapper">
    <main id="main-container">
        <div class="container mx-auto p-6 max-w-6xl container-fluid">
            <!-- Header principal -->
            <div class="bg-white rounded-lg shadow-sm mb-6 p-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-shield-alt text-blue-600 mr-3 text-2xl"></i>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800">Gestión de Regulaciones</h1>
                            <p class="text-gray-600 text-sm">Administra las regulaciones por tipo de producto</p>
                        </div>
                    </div>

                    <!-- Botón para mostrar formulario -->
                    <div class="flex items-center space-x-4">
                        <button id="btn-nueva-regulacion" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-all duration-300 font-medium flex items-center">
                            <i class="fas fa-plus mr-2"></i>
                            Nueva Regulación
                        </button>
                    </div>
                </div>

                <!-- Navigation Tabs para tipos de regulaciones -->
                <div class="flex flex-wrap gap-2">
                    <button class="regulacion-tab-btn active bg-blue-600 text-white px-4 py-2 rounded-lg font-medium transition-all hover:bg-blue-700" data-tab="antidumping">
                        <i class="fas fa-shield-alt mr-2"></i>Antidumping
                    </button>
                    <button class="regulacion-tab-btn bg-gray-100 text-gray-700 px-4 py-2 rounded-lg font-medium transition-all hover:bg-gray-200" data-tab="permiso">
                        <i class="fas fa-file-contract mr-2"></i>Permisos
                    </button>
                    <button class="regulacion-tab-btn bg-gray-100 text-gray-700 px-4 py-2 rounded-lg font-medium transition-all hover:bg-gray-200" data-tab="etiquetado">
                        <i class="fas fa-tags mr-2"></i>Etiquetado
                    </button>
                    <button class="regulacion-tab-btn bg-gray-100 text-gray-700 px-4 py-2 rounded-lg font-medium transition-all hover:bg-gray-200" data-tab="documentos">
                        <i class="fas fa-file-alt mr-2"></i>Doc. Especiales
                    </button>
                </div>
            </div>

            <!-- Contenedor para las datatables -->
            <div id="datatables-container">
                <!-- Tab Antidumping -->
                <div class="regulacion-content active" id="antidumping-table">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-bold text-blue-700 flex items-center gap-2">
                                <i class="fas fa-shield-alt"></i> Regulaciones Antidumping
                            </h2>
                            <div class="flex items-center space-x-2">
                                <button class="btn-refresh-table bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg transition-all" data-table="antidumping">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="antidumpingTable" class="stripe hover w-full text-sm">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Producto</th>

                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Tab Permisos -->
                <div class="regulacion-content hidden" id="permiso-table">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-bold text-blue-700 flex items-center gap-2">
                                <i class="fas fa-file-contract"></i> Entidades Reguladoras
                            </h2>
                            <div class="flex items-center space-x-2">
                                <button class="btn-refresh-table bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg transition-all" data-table="permiso">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="permisoTable" class="stripe hover w-full text-sm">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Entidad Reguladora</th>
                                        <th>Total Permisos</th>
                                        <th>Última Actualización</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Tab Etiquetado -->
                <div class="regulacion-content hidden" id="etiquetado-table">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-bold text-blue-700 flex items-center gap-2">
                                <i class="fas fa-tags"></i> Regulaciones de Etiquetado
                            </h2>
                            <div class="flex items-center space-x-2">
                                <button class="btn-refresh-table bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg transition-all" data-table="etiquetado">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="etiquetadoTable" class="stripe hover w-full text-sm">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Producto</th>
                                        <th>Tipo Etiquetado</th>
                                        <th>Requisitos</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Tab Documentos Especiales -->
                <div class="regulacion-content hidden" id="documentos-table">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-bold text-blue-700 flex items-center gap-2">
                                <i class="fas fa-file-alt"></i> Documentos Especiales
                            </h2>
                            <div class="flex items-center space-x-2">
                                <button class="btn-refresh-table bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg transition-all" data-table="documentos">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="documentosTable" class="stripe hover w-full text-sm">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Producto</th>
                                        <th>Tipo Documento</th>
                                        <th>Requisitos</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>


        </div>
        <div class="formulario-container hidden container-fluid">
            <!-- Formulario de Nueva Regulación (Oculto por defecto) -->
            <div id="formulario-regulacion">
                <!-- Barra de progreso tipo stepper -->
                <div id="progressStepper" class="flex justify-center items-center mb-8">
                    <div class="flex items-center gap-8">
                        <div class="stepper-item flex flex-col items-center">
                            <div class="stepper-circle w-8 h-8 rounded-full flex items-center justify-center bg-gray-300 text-white font-bold mb-1" data-step="antidumping">1</div>
                            <span class="text-xs font-medium text-gray-700">Antidumping</span>
                        </div>
                        <div class="stepper-line w-12 h-1 bg-gray-300"></div>
                        <div class="stepper-item flex flex-col items-center">
                            <div class="stepper-circle w-8 h-8 rounded-full flex items-center justify-center bg-gray-300 text-white font-bold mb-1" data-step="permiso">2</div>
                            <span class="text-xs font-medium text-gray-700">Permiso</span>
                        </div>
                        <div class="stepper-line w-12 h-1 bg-gray-300"></div>
                        <div class="stepper-item flex flex-col items-center">
                            <div class="stepper-circle w-8 h-8 rounded-full flex items-center justify-center bg-gray-300 text-white font-bold mb-1" data-step="etiquetado">3</div>
                            <span class="text-xs font-medium text-gray-700">Etiquetado</span>
                        </div>
                        <div class="stepper-line w-12 h-1 bg-gray-300"></div>
                        <div class="stepper-item flex flex-col items-center">
                            <div class="stepper-circle w-8 h-8 rounded-full flex items-center justify-center bg-gray-300 text-white font-bold mb-1" data-step="documentos">4</div>
                            <span class="text-xs font-medium text-gray-700">Doc. Especiales</span>
                        </div>
                    </div>
                </div>

                <!-- Header del formulario -->
                <div class="bg-white rounded-lg shadow-sm mb-6 p-6">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                        <div class="flex items-center">
                            <button id="btn-volver-tablas" class="flex items-center text-gray-600 hover:text-gray-800 transition-all duration-300 hover:scale-105 group mr-6">
                                <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i>
                                <span class="font-medium">Regresar</span>
                            </button>
                            <div>
                                <h1 class="text-2xl font-bold text-gray-800 flex items-center">
                                    <i class="fas fa-plus-circle text-blue-600 mr-3 text-2xl"></i>
                                    Nueva Regulación
                                </h1>
                                <p class="text-gray-600 text-sm">Completa la información de la nueva regulación</p>
                            </div>
                        </div>

                        <!-- Rubro Selector -->
                        <div class="lg:w-80">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-tag mr-1"></i>Rubro
                            </label>
                            <select id="rubroSelector" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all bg-white shadow-sm">
                                <option value="">Selecciona un rubro</option>
                            </select>
                        </div>
                    </div>

                    <!-- Navigation Tabs del formulario -->
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

                <!-- Contenido del formulario -->
                <div class="tab-content" id="antidumping">
                    <div class="bg-white rounded-lg shadow-sm p-8 mb-6">
                        <!-- Título y ayuda -->
                        <div class="mb-6 flex justify-between items-center">
                            <h2 class="text-2xl font-bold text-blue-700 flex items-center gap-2">
                                <i class="fas fa-shield-alt"></i> Antidumping
                            </h2>

                            <div class="flex justify-end mt-4 mb-6">
                                <button class="guardar-tab-btn bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg shadow transition-all text-lg flex items-center gap-2" data-tab="antidumping">
                                    <i class="fas fa-save"></i> Guardar
                                </button>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-4 col-span-2">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción del Producto</label>
                                    <textarea id="antidumping-description" name="antidumping-description" class="product-description w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 h-20 resize-none" placeholder="Ingrese la descripción del producto..."></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Partida</label>
                                    <input id="antidumping-partida" name="antidumping-partida" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Ej: 6402999000">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">P. declarado</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-2 text-gray-500">$</span>
                                            <input id="antidumping-p-declarado" name="antidumping-p-declarado" type="number" step="0.01" class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Ej: 7.5">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Antidumping</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-2 text-gray-500">$</span>
                                            <input id="antidumping-antidumping" name="antidumping-antidumping" type="number" step="0.01" class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Ej: 0.63">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-span-2">
                                <div class="flex justify-between items-center">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Imágenes del producto</label>
                                    <button id="addImageSlotBtn" type="button" class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-600 hover:bg-blue-700 text-white text-2xl mb-4 shadow transition-all" title="Agregar otra imagen">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                <div id="imageUploadSlots" class="flex gap-3 overflow-x-auto" style="scroll-snap-type: x mandatory;"></div>

                            </div>
                            <div class="mt-8 col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                                <textarea id="antidumping-observaciones" name="antidumping-observaciones" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 h-20 resize-none" placeholder="Agregar observaciones sobre el antidumping..."></textarea>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Permiso tab content -->
                <div class="tab-content hidden" id="permiso">
                    <div class="bg-white rounded-lg shadow-sm p-8 mb-6">
                        <div class="mb-6 flex justify-between items-center">
                            <h2 class="text-2xl font-bold text-blue-700 flex items-center gap-2">
                                <i class="fas fa-file-contract"></i> Permiso
                            </h2>
                            <div class="flex justify-end mt-4 mb-6">
                                <button class="guardar-tab-btn bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg shadow transition-all text-lg flex items-center gap-2" data-tab="permiso">
                                    <i class="fas fa-save"></i> Guardar
                                </button>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-4 col-span-2">
                                <!-- Inputs principales -->
                                <div class="grid md:grid-cols-2 gap-4 mb-4">
                                    <div class="form-group">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-building mr-1"></i>Entidad
                                        </label>
                                        <div class="flex gap-2">
                                            <select id="entidadSelector" name="permiso-entidad" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                                <option value="">Cargando entidades...</option>
                                            </select>
                                            <button type="button" id="btn-create-entidad" class="px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors flex items-center gap-2" title="Crear nueva entidad">
                                                <i class="fas fa-plus"></i>
                                                <span class="hidden sm:inline">Crear</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-tag mr-1"></i>Nombre del permiso
                                    </label>
                                    <input id="permiso-nombre" name="permiso-nombre" type="text" class="permit-name w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" placeholder="Nombre del permiso para el producto seleccionado">
                                </div>
                                <div class="grid md:grid-cols-3 gap-4">
                                    <div class="form-group">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-barcode mr-1"></i>C. Permiso
                                        </label>
                                        <input id="permiso-codigo" name="permiso-codigo" type="text" value="PRM-2024-001" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                    </div>
                                    <div class="form-group">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-dollar-sign mr-1"></i>Costo Base
                                        </label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-2 text-gray-500">S/.</span>
                                            <input id="permiso-costo-base" name="permiso-costo-base" type="number" value="90" step="0.01" class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-user-tie mr-1"></i>C. Tramitador
                                        </label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-2 text-gray-500">S/.</span>
                                            <input id="permiso-costo-tramitador" name="permiso-costo-tramitador" type="number" value="50" step="0.01" class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-span-2">
                                <div class="flex justify-between items-center">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Documentos</label>
                                    <button class="add-document-btn w-8 h-8 flex items-center justify-center rounded-full bg-blue-600 hover:bg-blue-700 text-white text-2xl mb-4 shadow transition-all" title="Agregar otro documento">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                <div id="documentUploadSlots" class="flex gap-3 overflow-x-auto" style="scroll-snap-type: x mandatory;"></div>
                            </div>
                            <div class="mt-8 col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                                <textarea id="permiso-observaciones" name="permiso-observaciones" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 h-20 resize-none" placeholder="Agregar observaciones sobre el permiso..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Etiquetado tab content -->
                <div class="tab-content hidden" id="etiquetado">
                    <div class="bg-white rounded-lg shadow-sm p-8 mb-6">
                        <div class="mb-6 flex justify-between items-center">
                            <h2 class="text-2xl font-bold text-blue-700 flex items-center gap-2">
                                <i class="fas fa-tags"></i> Etiquetado
                            </h2>
                            <div class="flex justify-end mt-4 mb-6">
                                <button class="guardar-tab-btn bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg shadow transition-all text-lg flex items-center gap-2" data-tab="etiquetado">
                                    <i class="fas fa-save"></i> Guardar
                                </button>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                            <div class="col-span-2">
                                <div class="flex justify-between items-center">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Imágenes del producto</label>
                                    <button class="add-label-image-btn w-8 h-8 flex items-center justify-center rounded-full bg-blue-600 hover:bg-blue-700 text-white text-2xl mb-4 shadow transition-all" title="Agregar otra imagen">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                <div id="labelImageUploadSlots" class="flex gap-3 overflow-x-auto" style="scroll-snap-type: x mandatory;"></div>
                            </div>
                            <div class="mt-8 col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                                <textarea id="etiquetado-observaciones" name="etiquetado-observaciones" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 h-20 resize-none" placeholder="Agregar observaciones sobre el etiquetado..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Documentos Especiales tab content -->
                <div class="tab-content hidden" id="documentos">
                    <div class="bg-white rounded-lg shadow-sm p-8 mb-6">
                        <div class="mb-6 flex justify-between items-center">
                            <h2 class="text-2xl font-bold text-blue-700 flex items-center gap-2">
                                <i class="fas fa-file-alt"></i> Documentos Especiales
                            </h2>
                            <div class="flex justify-end mt-4 mb-6">
                                <button class="guardar-tab-btn bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg shadow transition-all text-lg flex items-center gap-2" data-tab="documentos">
                                    <i class="fas fa-save"></i> Guardar
                                </button>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-4 col-span-2">
                                <!-- Inputs principales -->

                            </div>
                            <div class="col-span-2">
                                <div class="flex justify-between items-center">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Documentos</label>
                                    <button class="add-special-document-btn w-8 h-8 flex items-center justify-center rounded-full bg-blue-600 hover:bg-blue-700 text-white text-2xl mb-4 shadow transition-all" title="Agregar otro documento">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                <div id="specialDocumentUploadSlots" class="flex gap-3 overflow-x-auto" style="scroll-snap-type: x mandatory;"></div>
                            </div>
                            <div class="mt-8 col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                                <textarea id="documentos-observaciones" name="documentos-observaciones" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 h-20 resize-none" placeholder="Agregar observaciones sobre los documentos especiales..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Botón global de guardar -->
            <div class="fixed bottom-6 right-6 z-50">
                <button id="guardar-global-btn" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-10 rounded-full shadow-lg text-xl flex items-center gap-3 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    <i class="fas fa-cloud-upload-alt"></i> Guardar Todo
                </button>
            </div>

        </div>
    </main>
</div>

<!-- Scripts -->