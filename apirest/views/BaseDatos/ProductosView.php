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
<main class="bg-gradient-to-br from-indigo-50 via-white to-cyan-50 min-h-screen font-inter hidden" id="product-container">
    <!-- Animated Background Elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
      <div class="floating-bg absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-br from-blue-400/20 to-purple-400/20 rounded-full blur-3xl"></div>
      <div class="floating-bg absolute -bottom-40 -left-40 w-80 h-80 bg-gradient-to-br from-cyan-400/20 to-blue-400/20 rounded-full blur-3xl"></div>
      <div class="floating-bg absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-60 h-60 bg-gradient-to-br from-purple-400/10 to-pink-400/10 rounded-full blur-3xl"></div>
    </div>

    <div class="min-h-screen p-4 sm:p-6 lg:p-8">
      <!-- Header -->
      <div class="max-w-7xl mx-auto mb-8 relative z-10">
        <div class="flex items-center justify-between mb-8">
          <div class="flex items-center space-x-4">
            <button id="back-btn" class="flex items-center text-gray-600 hover:text-gray-800 transition-all duration-300 hover:scale-105 group">
              <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
              </svg>
              <span class="font-medium" id="back-btn-text">Regresar</span>
            </button>
          </div>
          <div class="flex items-center space-x-4">
            <button id="product-link" class="flex items-center bg-white/80 backdrop-blur-sm border border-gray-200 text-gray-700 px-6 py-3 rounded-xl hover:bg-white hover:shadow-lg transition-all duration-300 hover:scale-105 font-medium">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
              </svg>
              Link Producto
            </button>
            <button id="alibaba-link" class="flex items-center bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-3 rounded-xl hover:shadow-lg transition-all duration-300 hover:scale-105 font-medium">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
              </svg>
              <span id="alibaba-link-text">www.alibaba.com/pe/</span>
            </button>
          </div>
        </div>

        <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-2xl border border-gray-100 overflow-hidden">
          <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 px-8 py-8 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-600/50 to-transparent"></div>
            <div class="relative z-10">
              <h1 class="text-3xl font-bold text-white flex items-center">
                <svg class="w-8 h-8 mr-4 drop-shadow-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span id="product-name-text">CAJA DE MANTENIMIENTO PARA IMPRESO</span>
              </h1>
            </div>
          </div>

          <div class="p-8 lg:p-12">
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-12 mb-12">
              <!-- Tributos Aduanero Section -->
              <div class="space-y-8">
                <div class="flex items-center space-x-3 border-b border-gray-200 pb-4">
                  <div class="w-2 h-8 bg-gradient-to-b from-red-500 to-red-600 rounded-full"></div>
                  <h2 class="text-xl font-bold text-gray-800">Tributos Aduanero</h2>
                  <span class="text-red-500 text-sm font-medium bg-red-50 px-2 py-1 rounded-full">Requerido</span>
                </div>
                
                <div class="space-y-6">
                  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="group">
                      <label class="block text-sm font-semibold text-gray-700 mb-3">Arancel Sunat</label>
                      <input type="text" id="arancel-sunat" value="0%" class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 bg-gray-50/50 hover:bg-white font-medium" placeholder="Ingrese porcentaje">
                    </div>
                    <div class="group">
                      <label class="block text-sm font-semibold text-gray-700 mb-3">Disponibilidad</label>
                      <select class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 bg-gray-50/50 hover:bg-white font-medium">
                        <option value="OK">✅ OK</option>
                        <option value="Pendiente">⏳ Pendiente</option>
                        <option value="No disponible">❌ No disponible</option>
                      </select>
                    </div>
                  </div>

                  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="group">
                      <label class="block text-sm font-semibold text-gray-700 mb-3">Arancel TLC</label>
                      <input type="text" id="arancel-tlc" value="0%" class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 bg-gray-50/50 hover:bg-white font-medium" placeholder="Ingrese porcentaje">
                    </div>
                    <div class="group">
                      <label class="block text-sm font-semibold text-gray-700 mb-3">Estado</label>
                      <select class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 bg-gray-50/50 hover:bg-white font-medium">
                        <option value="OK">✅ OK</option>
                        <option value="Pendiente">⏳ Pendiente</option>
                        <option value="Rechazado">❌ Rechazado</option>
                      </select>
                    </div>
                  </div>

                  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="group">
                      <label class="block text-sm font-semibold text-gray-700 mb-3">Correlativo</label>
                      <div class="flex items-center space-x-4">
                        <input type="text" id="correlativo" class="flex-1 px-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 bg-gray-50/50 hover:bg-white font-medium">
                        
                      </div>
                    </div>
                    <div class="group">
                      <label class="block text-sm font-semibold text-gray-700 mb-3">Antidumping</label>
                      <div class="flex items-center space-x-4">
                        <input type="number" id="antidumping" value="0" class="flex-1 px-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 bg-gray-50/50 hover:bg-white font-medium">
                        
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Requisito Aduanero Section -->
              <div class="space-y-8">
                <div class="flex items-center space-x-3 border-b border-gray-200 pb-4">
                  <div class="w-2 h-8 bg-gradient-to-b from-blue-500 to-blue-600 rounded-full"></div>
                  <h2 class="text-xl font-bold text-gray-800">Requisito Aduanero</h2>
                  <span class="text-red-500 text-sm font-medium bg-red-50 px-2 py-1 rounded-full">Requerido</span>
                </div>
                
                <div class="space-y-6">
                  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="group">
                      <label class="block text-sm font-semibold text-gray-700 mb-3">Tipo de producto</label>
                      <select id="product-type" class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 bg-gray-50/50 hover:bg-white font-medium">
                        <option value="LIBRE">🟢 Libre</option>
                        <option value="RESTRINGIDO">🟡 Restringido</option>
                        <option value="PROHIBIDO">🔴 Prohibido</option>
                      </select>
                    </div>
                    <div class="group">
                      <label class="block text-sm font-semibold text-gray-700 mb-3">Etiquetado</label>
                      <select id="labeling" class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 bg-gray-50/50 hover:bg-white font-medium">
                        <option value="NORMAL">📋 Normal</option>
                        <option value="ESPECIAL">⭐ Especial</option>
                        <option value="NO_REQUERIDO">❌ No requerido</option>
                      </select>
                    </div>
                  </div>

                  <div class="group">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Documento especial</label>
                    <div class="flex items-center space-x-4">
                      <select id="special-document" class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 bg-gray-50/50 hover:bg-white font-medium">
                        <option value="NO">No</option>
                        <option value="SI">Si</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Observations Section with Toggle Switch -->
            <div class="border-t-2 border-gray-100 pt-12">
              <div class="flex items-center justify-between mb-8 p-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl border border-blue-100">
                <div class="flex items-center space-x-4">
                  <div class="w-2 h-8 bg-gradient-to-b from-purple-500 to-purple-600 rounded-full"></div>
                  <div>
                    <h2 class="text-xl font-bold text-gray-800 flex items-center">
                      Observaciones de Aduana
                      <span class="text-red-500 text-sm font-medium bg-red-50 px-2 py-1 rounded-full ml-3">Requerido</span>
                    </h2>
                    <p class="text-gray-600 text-sm mt-1">Activar para agregar observaciones detalladas</p>
                  </div>
                </div>
                
                <!-- Modern Toggle Switch -->
                <div class="flex items-center space-x-4">
                  <span id="observations-toggle-text">No</span>
                  <label class="toggle-switch">
                    <input type="checkbox" id="observations-toggle">
                    <span class="slider"></span>
                  </label>
                  <span class="text-sm font-medium text-gray-700">Si</span>
                </div>
              </div>
              
              <div id="observations-section" class="space-y-6" style="display: none;">
                <div class="relative group">
                  <textarea id="observations" rows="5" maxlength="500" class="w-full px-6 py-5 border-2 border-gray-200 rounded-2xl focus:ring-4 focus:ring-purple-500/20 focus:border-purple-500 transition-all duration-300 resize-none bg-gradient-to-br from-gray-50/50 to-white hover:shadow-lg font-medium text-gray-700" placeholder="Ingrese las observaciones de aduana detalladas...">El vista de aduanas observa la medida del producto</textarea>
                  <div class="absolute bottom-4 right-4 flex items-center space-x-3">
                    <span id="char-counter" class="text-sm font-medium text-gray-500 bg-white/80 backdrop-blur-sm px-3 py-1 rounded-full shadow-sm">50/500</span>
                  </div>
                </div>
                
                <div class="flex items-start space-x-4 text-sm text-gray-700 bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-2xl border border-blue-100">
                  <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                  </svg>
                  <div>
                    <p class="font-semibold text-blue-800 mb-1">Información importante</p>
                    <p>Las observaciones serán revisadas por el departamento de aduanas antes de la aprobación final. Asegúrese de proporcionar información precisa y detallada.</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Enhanced Action Buttons -->
            <div class="flex flex-col sm:flex-row justify-end space-y-4 sm:space-y-0 sm:space-x-6 mt-12 pt-8 border-t-2 border-gray-100">
              <button id="cancel-btn" class="px-8 py-4 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all duration-300 font-semibold hover:scale-105 hover:shadow-lg">
                Cancelar
              </button>
              <button id="save-btn" class="px-8 py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-300 font-semibold flex items-center justify-center hover:scale-105 hover:shadow-xl shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
                Guardar Cambios
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</div>
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