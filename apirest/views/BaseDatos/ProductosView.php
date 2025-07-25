<div class="content-wrapper">
<main id="main-container">
    <section class="content-header" id="content-header">
        <div class="container-fluid">
            <div class="row mb-2 gap-2 px-xl-3 px-0">
                <div class="col-sm-12 col-xl-6 p-3 p-xl-0">
                    <h1>
                        <i class="<?php echo $this->MenuModel->verificarAccesoMenuCRUD()->Txt_Css_Icons; ?>"
                            aria-hidden="true"></i> <span
                            id="section-title"><?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?></span>
                        &nbsp;<span id="span-id_pedido" class="badge badge-secondary"></span>
                    </h1>
                </div>

                <!-- Buscador de la tabla -->
                <div class="col-9 col-xl-2 filter-contenedor">
                    <div class="dataTables_filter" style="display: flex;justify-content: flex-end;">
                        <input type="search"
                            class="form-control bg-white hover:bg-white-200 text-black-200 py-2 border border-transparent hover:border-orange-600 rounded search-table"
                            placeholder="Buscar por " aria-controls="table-contenedor"
                            style="width:100%;min-width:200px; padding-left: 40px; background: url('https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/svgs/solid/search.svg') no-repeat 15px center;background-size: 16px; font-size: 14px;">
                    </div>
                </div>
                <div class="col-1 col-xl-1 dropdown filter-contenedor px-0">
                    <button type="button" id="btn-exportar-carga"
                        class="bg-white hover:bg-white-200 text-black-200 py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block btn-reporte"
                        type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                            class="fa fa-upload"></i> Exportar</button>
                    <div class="dropdown-menu dropdown-menu-right px-3 py-3" aria-labelledby="btn-exportar-carga">
                        <button class="dropdown-item btn-block export-pdf-main-content"><i
                                class="fa fa-file-pdf color_icon_pdf"></i>
                            Exportar PDF</button>
                        <button class="dropdown-item btn-block export-excel-main-content"><i
                                class="fa fa-file-excel color_icon_excel"></i>
                            Exportar Excel</button>
                    </div>
                </div>

                <div class="col-1 col-xl-1 dropdown filter-contenedor px-0">

                    <button
                        class="bg-white py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block btn-reporte"
                        type="button" id="btn-filtrar-carga" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false">
                        <i class="fa fa-filter"></i> Filtros
                    </button>
                    <!-- Menú Desplegable -->
                    <div class="dropdown-menu dropdown-menu-right px-3 py-3" aria-labelledby="btn-filtrar-carga">
                        <div class="form-group">
                            <div class="d-flex align-items-center p-2">
                                <div class="d-flex" style="width:60%">Fecha Inicio</div>
                                <div style="width: 200px;">
                                    <input type="text" id="txt-Fe_Inicio_Carga"
                                        class="form-control text-center input-date input-report required"
                                        value="<?php echo dateNow('month_date_ini_report'); ?>">
                                    <span class="help-block text-danger" id="error"></span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center p-2">
                                <div class="d-flex" style="width:60%">Fecha Fin</div>
                                <div style="width: 200px;">
                                    <input type="text" id="txt-Fe_Fin_Carga"
                                        class="form-control input-date input-report required">
                                    <span class="help-block text-danger" id="error"></span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center p-2" style="width:300px;">
                                <div class="d-flex" style="width:60%">Estado</div>
                                <div style="width: 200px;">
                                    <select id="filtroCampana" class="form-select w-full border-gray-300 rounded">
                                        <option value="0">Todas</option>
                                    </select>
                                </div>

                            </div>
                            <div class="d-flex align-items-center p-2" style="width:300px;">
                                <div class="d-flex" style="width:60%">Estado</div>
                                <div style="width: 200px;">
                                    <select id="filtroTipo" class="form-select w-full border-gray-300 rounded">
                                        <option value="0">Todos</option>
                                        <option value="LIBRE">Libre</option>
                                        <option value="RESTRINGIDO">Restringido</option>
                                    </select>
                                </div>

                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <!-- Botones -->
                        <div class="d-flex justify-content-around">
                            <button
                                class="bg-white py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block"
                                style="margin-top: .5rem;" id="cancelar-btn">Cancelar</button>
                            <button
                                class="bg-orange py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block"
                                id="aplicar-filtros">Aplicar</button>
                        </div>
                    </div>

                </div>
            </div>
    </section>
    <section id="productos-container">
        <div class="mx-5">
            <!-- Tabla -->
            <div class="table-responsive" class="table table-bordered table-hover table-striped">
                <table id="productosTable" class="stripe hover w-full text-sm">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>Nombre comercial</th>
                            <th>Foto</th>
                            <th>Características</th>
                            <th>Rubro</th>
                            <th>T. Producto</th>
                            <th>Unidad Com.</th>
                            <th>Precio Exw</th>
                            <th>Subpartida</th>
                            <th>Campaña</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </section>
</main>
<main class="bg-gray-50 min-h-screen hidden" id="product-container">
    <div class="container mx-auto p-6 max-w-6xl">
        <!-- Header con navegación -->
        <div class="bg-white rounded-lg shadow-sm mb-6 p-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                <div class="flex items-center">
                    <button id="back-btn" class="flex items-center text-gray-600 hover:text-gray-800 transition-all duration-300 hover:scale-105 group mr-6">
                        <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i>
              <span class="font-medium" id="back-btn-text">Regresar</span>
            </button>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-box-open text-blue-600 mr-3 text-2xl"></i>
                            <span id="product-name-text">Detalle del Producto</span>
                        </h1>
                        <p class="text-gray-600 text-sm">Información completa del producto seleccionado</p>
                    </div>
          </div>

                <!-- Botones de acción -->
          <div class="flex items-center space-x-4">
                    <button id="product-link" class="flex items-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-all duration-300 font-medium">
                        <i class="fas fa-external-link-alt mr-2"></i>
              Link Producto
            </button>
                    <button id="alibaba-link" class="flex items-center bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg transition-all duration-300 font-medium">
                        <i class="fas fa-link mr-2"></i>
                        <span id="alibaba-link-text">Alibaba</span>
            </button>
          </div>
            </div>
          </div>

        <!-- Contenido principal -->
        <div class="bg-white rounded-lg shadow-sm p-8 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Tributos Aduaneros -->
                <div class="space-y-6">
                    <div class="mb-6 flex justify-between items-center">
                        <h2 class="text-xl font-bold text-blue-700 flex items-center gap-2">
                            <i class="fas fa-calculator"></i> Tributos Aduaneros
                        </h2>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Arancel Sunat</label>
                                <input type="text" id="arancel-sunat" value="0%" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" placeholder="Ingrese porcentaje">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Disponibilidad</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        <option value="OK">✅ OK</option>
                        <option value="Pendiente">⏳ Pendiente</option>
                        <option value="No disponible">❌ No disponible</option>
                      </select>
                    </div>
                  </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Arancel TLC</label>
                                <input type="text" id="arancel-tlc" value="0%" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" placeholder="Ingrese porcentaje">
                    </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        <option value="OK">✅ OK</option>
                        <option value="Pendiente">⏳ Pendiente</option>
                        <option value="Rechazado">❌ Rechazado</option>
                      </select>
                    </div>
                  </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Correlativo</label>
                                <input type="text" id="correlativo" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Antidumping</label>
                                <input type="number" id="antidumping" value="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                      </div>
                      </div>
                    </div>
                </div>
                
                <!-- Requisitos Aduaneros -->
                <div class="space-y-6">
                    <div class="mb-6 flex justify-between items-center">
                        <h2 class="text-xl font-bold text-blue-700 flex items-center gap-2">
                            <i class="fas fa-clipboard-check"></i> Requisitos Aduaneros
                        </h2>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de producto</label>
                                <select id="product-type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        <option value="LIBRE">🟢 Libre</option>
                        <option value="RESTRINGIDO">🟡 Restringido</option>
                        <option value="PROHIBIDO">🔴 Prohibido</option>
                      </select>
                    </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Etiquetado</label>
                                <select id="labeling" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        <option value="NORMAL">📋 Normal</option>
                        <option value="ESPECIAL">⭐ Especial</option>
                        <option value="NO_REQUERIDO">❌ No requerido</option>
                      </select>
                    </div>
                  </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Documento especial</label>
                            <select id="special-document" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        <option value="NO">No</option>
                        <option value="SI">Si</option>
                      </select>
                  </div>
                </div>
              </div>
            </div>

            <!-- Observaciones -->
            <div class="mt-8 border-t border-gray-200 pt-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-blue-700 flex items-center gap-2">
                        <i class="fas fa-comment-alt"></i> Observaciones de Aduana
                    </h2>
                <div class="flex items-center space-x-4">
                        <span id="observations-toggle-text" class="text-sm font-medium text-gray-700">No</span>
                  <label class="toggle-switch">
                    <input type="checkbox" id="observations-toggle">
                    <span class="slider"></span>
                  </label>
                  <span class="text-sm font-medium text-gray-700">Si</span>
                </div>
              </div>
              
                <div id="observations-section" class="space-y-4" style="display: none;">
                    <div class="relative">
                        <textarea id="observations" rows="5" maxlength="500" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none" placeholder="Ingrese las observaciones de aduana detalladas...">El vista de aduanas observa la medida del producto</textarea>
                        <div class="absolute bottom-2 right-2">
                            <span id="char-counter" class="text-sm font-medium text-gray-500 bg-white px-2 py-1 rounded">50/500</span>
                  </div>
                </div>
                
                    <div class="flex items-start space-x-3 text-sm text-gray-700 bg-blue-50 p-4 rounded-lg border border-blue-100">
                        <i class="fas fa-info-circle text-blue-600 flex-shrink-0 mt-0.5"></i>
                  <div>
                    <p class="font-semibold text-blue-800 mb-1">Información importante</p>
                    <p>Las observaciones serán revisadas por el departamento de aduanas antes de la aprobación final. Asegúrese de proporcionar información precisa y detallada.</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Botones de acción -->
            <div class="flex flex-col sm:flex-row justify-end space-y-4 sm:space-y-0 sm:space-x-4 mt-8 pt-6 border-t border-gray-200">
                <button id="cancel-btn" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-300 font-medium">
                Cancelar
              </button>
                <button id="save-btn" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all duration-300 font-medium flex items-center justify-center">
                    <i class="fas fa-save mr-2"></i>
                Guardar Cambios
              </button>
            </div>
          </div>
        </div>
</main>
<style>
     .toggle-switch {
        position: relative;
        display: inline-block;
        width: 64px;
        height: 32px;
      }
      
      .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
      }
      
      .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, #e5e7eb, #d1d5db);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 34px;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
      }
      
      .slider:before {
        position: absolute;
        content: "";
        height: 28px;
        width: 28px;
        left: 2px;
        bottom: 2px;
        background: linear-gradient(135deg, #ffffff, #f8fafc);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 50%;
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
      }
      
      input:checked + .slider {
        background: linear-gradient(135deg,rgb(184, 28, 0),rgb(150, 0, 0));
        box-shadow: 0 0 20px rgba(246, 65, 59, 0.3);
      }
      
      input:checked + .slider:before {
        transform: translateX(32px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
      }
      
      /* Floating animation */
      @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        33% { transform: translateY(-10px) rotate(1deg); }
        66% { transform: translateY(5px) rotate(-1deg); }
      }
      
      .floating-bg {
        animation: float 6s ease-in-out infinite;
      }
      
      .floating-bg:nth-child(2) {
        animation-delay: -2s;
      }
      
      .floating-bg:nth-child(3) {
        animation-delay: -4s;
      }
      
      /* Pulse animation */
      @keyframes pulse-glow {
        0%, 100% { box-shadow: 0 0 20px rgba(59, 130, 246, 0.3); }
        50% { box-shadow: 0 0 40px rgba(59, 130, 246, 0.6); }
      }
      
      .pulse-glow {
        animation: pulse-glow 3s ease-in-out infinite;
      }
</style>