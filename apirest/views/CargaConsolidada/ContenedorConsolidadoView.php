<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header" id="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-7">
          <h1>
            <i class="<?php echo $this->MenuModel->verificarAccesoMenuCRUD()->Txt_Css_Icons; ?>" aria-hidden="true"></i> <span id="section-title"><?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?></span>
            &nbsp;<span id="span-id_pedido" class="badge badge-secondary"></span>
          </h1>
        </div>
        <?php if ($this->user->No_Grupo == "Coordinación") {  ?>

        <!-- Buscador de la tabla -->
        <div class="col-6 col-sm-2">
          <div class="dataTables_filter" style="display: flex;justify-content: flex-end;">
            <input type="search" class="form-control bg-white hover:bg-white-200 text-black-200 py-2 border border-transparent hover:border-orange-600 rounded search-table" placeholder="Buscar por: " aria-controls="table-contenedor" style="width:250px; padding-left: 40px; background: url('https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/svgs/solid/search.svg') no-repeat 15px center;background-size: 16px; font-size: 14px;">
          </div>
        </div>
        <!-- Contenedor Principal de Exportar-->
        <div class="col-6 col-sm-1 dropdown">
          <button type="button" id="btn-exportar-carga" class="bg-white hover:bg-white-200 text-black-200 py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block btn-reporte" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-upload"></i> Exportar</button>
          <div class="dropdown-menu dropdown-menu-right px-3 py-3" aria-labelledby="btn-exportar-carga">
            <button class="dropdown-item btn-block export-pdf-main-content" ><i class="fa fa-file-pdf color_icon_pdf"></i>Exportar PDF</button>
            <button class="dropdown-item btn-block export-excel-main-content" ><i class="fa fa-file-excel color_icon_excel"></i>Exportar Excel</button>
          </div>
        </div>
        <!-- Contenedor Principal de Filtros-->
        <div class=" col-6 col-sm-1 dropdown">
          <!-- Botón de Filtros -->
          <button class="bg-white py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block btn-reporte" type="button" id="btn-filtrar-carga" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-filter"></i>Filtros
          </button>
          <!-- Menú Desplegable -->
          <div class="dropdown-menu dropdown-menu-right px-3 py-3" aria-labelledby="btn-filtrar-carga">
            <div class="form-group">
              <div class="d-flex align-items-center p-2">
                <div class="d-flex" style="width:60%">Fecha Inicio</div>
                <div style="width: 200px;">
                  <input type="text" id="txt-Fe_Inicio_Carga" class="form-control text-center input-date input-report required" value="<?php echo dateNow('month_date_ini_report'); ?>">
                  <span class="help-block text-danger" id="error"></span>
                </div>
              </div>
              <div class="d-flex align-items-center p-2">
                <div class="d-flex" style="width:60%">Fecha Fin</div>
                <div style="width: 200px;">
                  <input type="text" id="txt-Fe_Fin_Carga" class="form-control input-date input-report required">
                  <span class="help-block text-danger" id="error"></span>
                </div>
              </div>
              <div class="d-flex align-items-center p-2" style="width:300px;">
                <div class="d-flex" style="width:60%">Estado</div>
                <div style="width: 200px;">
                  <select id="txt-ID_Estado" name="ID_Estado" class="form-control input-estado">
                    <option value="0" selected>Todos</option>
                    <option value="PENDIENTE">PENDIENTE</option>
                    <option value="RECIBIENDO">RECIBIENDO</option>
                    <option value="COMPLETADO">COMPLETADO</option>
                  </select>
                </div>

              </div>
          </div>         
          <div class="dropdown-divider"></div>
          <!-- Botones -->
          <div class="d-flex justify-content-around">
                <button class="bg-white py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block" style="margin-top: .5rem;" id="cancelar-btn">Cancelar</button>
                <button class="bg-orange py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block" id="aplicar-btn">Aplicar</button>
            </div>
          </div>
        </div>
          <!-- Botón crear consolidados -->
          <div class="col-12 col-sm-1">
            <button type="button" id="btn-crear" class="bg-orange py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block btn-reporte" data-type="html">Crear<i class="fa fa-plus"></i> </button>
          </div>
        <?php }else{ ?>

        <div class="col-12 col-sm-1"></div>

        <!-- Buscador de la tabla -->
        <div class="col-6 col-sm-2">
          <div class="dataTables_filter" style="display: flex;justify-content: flex-end;">
            <input type="search" class="form-control bg-white hover:bg-white-200 text-black-200 py-2 border border-transparent hover:border-orange-600 rounded search-table" id="search-input-filter" placeholder="Buscar por: " aria-controls="table-contenedor" style="width:250px; padding-left: 40px; background: url('https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/svgs/solid/search.svg') no-repeat 15px center;background-size: 16px; font-size: 14px;">
          </div>
        </div>
        <!-- Contenedor Principal de Exportar-->
        <div class="col-6 col-sm-1 dropdown">
          <button type="button" id="btn-exportar-carga" class="bg-white hover:bg-white-200 text-black-200 py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block btn-reporte" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-upload"></i> Exportar</button>
          <div class="dropdown-menu dropdown-menu-right px-3 py-3" aria-labelledby="btn-exportar-carga">
            <button class="dropdown-item btn-block export-pdf-main-content"><i class="fa fa-file-pdf color_icon_pdf"></i>Exportar PDF</button>
            <button class="dropdown-item btn-block export-excel-main-content"><i class="fa fa-file-excel color_icon_excel"></i>Exportar Excel</button>
          </div>
        </div>
        <!-- Contenedor Principal de Filtros-->
        <div class=" col-6 col-sm-1 dropdown">
          <!-- Botón de Filtros -->
          <button class="bg-white py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block btn-reporte" type="button" id="btn-filtrar-carga" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-filter"></i>Filtros
          </button>
          <!-- Menú Desplegable -->
          <div class="dropdown-menu dropdown-menu-right px-3 py-3" aria-labelledby="btn-filtrar-carga">
            <div class="form-group">
              <div class="d-flex align-items-center p-2">
                <div class="d-flex" style="width:60%">Fecha Inicio</div>
                <div style="width: 200px;">
                  <input type="text" id="txt-Fe_Inicio_Carga" class="form-control text-center input-date input-report required" value="<?php echo dateNow('month_date_ini_report'); ?>">
                  <span class="help-block text-danger" id="error"></span>
                </div>
              </div>
              <div class="d-flex align-items-center p-2">
                <div class="d-flex" style="width:60%">Fecha Fin</div>
                <div style="width: 200px;">
                  <input type="text" id="txt-Fe_Fin_Carga" class="form-control input-date input-report required">
                  <span class="help-block text-danger" id="error"></span>
                </div>
              </div>
              <div class="d-flex align-items-center p-2" style="width:300px;">
                <div class="d-flex" style="width:60%">Estado</div>
                <div style="width: 200px;">
                  <select id="txt-ID_Estado" name="ID_Estado" class="form-control input-estado" >
                    <option value="0" selected>Todos</option>
                    <option value="PENDIENTE">PENDIENTE</option>
                    <option value="RECIBIENDO">RECIBIENDO</option>
                    <option value="COMPLETADO">COMPLETADO</option>
                  </select>
                </div>
                
              </div>
          </div>         
          <div class="dropdown-divider"></div>
          <!-- Botones -->
          <div class="d-flex justify-content-around">
                <button class="bg-white py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block" style="margin-top: .5rem;" id="cancelar-btn">Cancelar</button>
                <button class="bg-orange py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block" id="aplicar-btn">Aplicar</button>
            </div>
          </div>
        </div>
        <?php }?>
      </div>
    </div><!-- /.container-fluid desactivate for a moment-->
  </section>
  <section class="content" id="main-container">
    <div class="container-fluid">

      <div class="table-responsive div-Listar">
        <table id="table-contenedor" class="table table-hover">
          <thead class="thead-default">
            <tr>
              <?php if (
                $this->user->No_Grupo == "Coordinación"
                || $this->user->No_Grupo == "Documentacion"
              ) {
              ?>
                <th>Carga</th>
                <th>Mes </th>
                <th>Pais</th>

                <th>F. Cierre</th>
                <th>F. Arribo</th>
                <th>F. Entrega</th>

                <th>Empresa</th>
                <th>Estado</th>
                <th>Acciones
                </th>

              <?php } else { ?>
                <th>Cargo.</th>
                <th>Month</th>
                <th>Country</th>
                <th>Cut off</th>
                <th>Company</th>
                <th>Status</th>

                <th>Check</th>

              <?php } ?>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </section>
  <section class="bg-gradient-to-br from-gray-50 to-gray-100 p-8"
    id="documentacion-container">
    <div class="row mb-2">
      <div class="col-10">
        &nbsp;
      </div>
      <div class="col-2"
        id="btn-back-documentacion-profile">

        <button type="button" class="btn btn-outline-primary btn-block btn-reporte" data-type="html"><i class="fa fa-arrow-left"></i> </button>
      </div>
    </div>
    <div class="max-w-4xl mx-auto space-y-6">
      <!-- Providers Section -->
      <div class="providers flex gap-4 mb-8">
        <button class="provider-btn px-6 py-3 rounded-lg font-medium transition-all duration-300 shadow-sm hover:shadow-md bg-orange-500 text-white">JS - 1</button>
        <button class="provider-btn px-6 py-3 rounded-lg font-medium transition-all duration-300 shadow-sm hover:shadow-md bg-gray-200 text-gray-700">JS - 2</button>
        <button class="provider-btn px-6 py-3 rounded-lg font-medium transition-all duration-300 shadow-sm hover:shadow-md bg-gray-200 text-gray-700">JS - 3</button>
      </div>

      <!-- Peru Documentation -->
      <div class="bg-white rounded-xl shadow-lg p-6 transition-all duration-300 hover:shadow-xl">
        <h2 class="text-lg font-semibold mb-6 bg-gradient-to-r from-gray-200 to-gray-100 p-3 rounded-lg flex items-center">
          <i class="bi bi-flag text-xl mr-2"></i>
          DOCUMENTACIÓN PERÚ
        </h2>
        <div class="space-y-6">
          <div class="grid grid-cols-2 gap-6">
            <div class="space-y-4">
              <div class="flex items-center gap-4">
                <label class="font-medium flex items-center">
                  <i class="bi bi-file-text mr-2"></i>
                  Vol. Doc:
                </label>
                <input type="text"
                  id="txt-Vol_Doc"
                  class="border rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none transition-all duration-300">
              </div>
              <div class="flex items-center gap-4">
                <label class="font-medium flex items-center">
                  <i class="bi bi-currency-dollar mr-2"></i>
                  Valor Doc:
                </label>
                <input type="text"
                  id="txt-Valor_Doc"
                  class="border rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none transition-all duration-300">
              </div>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4 mt-4"
            id="documentacion-peru-documents">
            <!-- <button class="download-btn comercial-download flex items-center gap-3 p-4 bg-gray-50 rounded-lg transition-all duration-300 hover:bg-gray-100 group">
                        <i class="bi bi-file-earmark-text text-2xl text-gray-600 transition-colors duration-300"></i>
                        <div class="text-left">
                            <div class="text-title font-medium text-gray-700 transition-colors duration-300">F. Comercial</div>
                            <div class="text-sm text-gray-500">Descargar documento</div>
                        </div>
                    </button> -->
            <!-- <button class="download-btn excel-download flex items-center gap-3 p-4 bg-gray-50 rounded-lg transition-all duration-300 hover:bg-gray-100 group">
                        <i class="bi bi-file-earmark-excel text-2xl text-gray-600 transition-colors duration-300"></i>
                        <div class="text-left">
                            <div class="text-title font-medium text-gray-700 transition-colors duration-300">Excel confirmación</div>
                            <div class="text-sm text-gray-500">Descargar archivo</div>
                        </div>
                    </button> -->
          </div>
        </div>
      </div>

      <!-- China Documentation -->
      <div class="bg-white rounded-xl shadow-lg p-6 transition-all duration-300 hover:shadow-xl">
        <h2 class="text-lg font-semibold mb-6 bg-gradient-to-r from-gray-200 to-gray-100 p-3 rounded-lg flex items-center">
          <i class="bi bi-globe-asia-australia text-xl mr-2"></i>
          DOCUMENTACIÓN CHINA
        </h2>
        <div class="grid grid-cols-2 gap-4 mt-4"
          id="documentacion-china">
        </div>
      </div>

      <!-- Inspection -->
      <div class="bg-white rounded-xl shadow-lg p-6 transition-all duration-300 hover:shadow-xl">
        <h2 class="text-lg font-semibold mb-6 bg-gradient-to-r from-gray-200 to-gray-100 p-3 rounded-lg flex items-center">
          <i class="bi bi-clipboard-check text-xl mr-2"></i>
          INSPECCIÓN
        </h2>
        <div class="grid grid-cols-2 gap-4"
          id="documentacion-inspeccion">

        </div>
      </div>
    </div>
  </section>
  <section class="bg-white rounded-xl shadow-lg p-8"
    id="documentacion-documentacion-container">
    <div class="row">
      <div class="col-10">
        <h2 class="text-xl font-bold mb-6 text-gray-800 border-b pb-4">
          <i class="bi bi-folder me-2"></i>
          DOCUMENTACIÓN
        </h2>
      </div>
      <div class="col-1">
        <button type="button" id="btn-crear-documentacion-documentacion" class="btn btn-primary btn-block btn-reporte" data-type="html"><i class="fa fa-plus"></i></button>
      </div>
      <div class="col-1">
        <button type="button" id="btn-back-documentacion-documentacion" class="btn btn-primary btn-block btn-reporte" data-type="html"><i class="fa fa-arrow-left"></i></button>
      </div>
    </div>

    <!--button with plus icon to add new document-->

    <!-- Document Filter -->
    <div class="mb-8">
      <div class="flex space-x-4 bg-gray-100 p-2 rounded-lg">
        <button class="doc-filter active px-4 py-2 rounded-md transition-all duration-300" data-filter="todos">Todos</button>
        <button class="doc-filter px-4 py-2 rounded-md transition-all duration-300" data-filter="ENVIO">Envío</button>
        <button class="doc-filter px-4 py-2 rounded-md transition-all duration-300" data-filter="COMERCIAL">Comercial</button>
        <button class="doc-filter px-4 py-2 rounded-md transition-all duration-300" data-filter="LEGAL">Legal</button>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6" id="documentacion-documentacion">
      <!-- Document Cards -->
      <!-- <div class="doc-card opacity-0 bg-blue-50 p-4 rounded-lg transition-all duration-300" data-type="envio">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <i class="bi bi-file-text text-blue-500 text-xl"></i>
            <div>
              <h3 class="font-medium text-gray-800">Packing China</h3>
              <p class="text-sm text-gray-500">Ver documento</p>
            </div>
          </div>
          <button class="download-btn text-blue-500 hover:text-blue-700">
            <i class="bi bi-download"></i>
          </button>
        </div>
      </div>

      <div class="doc-card opacity-0 bg-blue-50 p-4 rounded-lg transition-all duration-300" data-type="envio">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <i class="bi bi-check-circle text-blue-500 text-xl"></i>
            <div>
              <h3 class="font-medium text-gray-800">Pre confirmación</h3>
              <p class="text-sm text-gray-500">Ver documento</p>
            </div>
          </div>
          <button class="download-btn text-blue-500 hover:text-blue-700">
            <i class="bi bi-download"></i>
          </button>
        </div>
      </div>

      <div class="doc-card opacity-0 bg-green-50 p-4 rounded-lg transition-all duration-300" data-type="comercial">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <i class="bi bi-receipt text-green-500 text-xl"></i>
            <div>
              <h3 class="font-medium text-gray-800">Factura comercial</h3>
              <p class="text-sm text-gray-500">Ver documento</p>
            </div>
          </div>
          <button class="download-btn text-green-500 hover:text-green-700">
            <i class="bi bi-download"></i>
          </button>
        </div>
      </div>

      <div class="doc-card opacity-0 bg-yellow-50 p-4 rounded-lg transition-all duration-300" data-type="legal">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <i class="bi bi-file-earmark-text text-yellow-500 text-xl"></i>
            <div>
              <h3 class="font-medium text-gray-800">Declaración Jurada</h3>
              <p class="text-sm text-gray-500">Ver documento</p>
            </div>
          </div>
          <button class="download-btn text-yellow-500 hover:text-yellow-700">
            <i class="bi bi-download"></i>
          </button>
        </div>
      </div>

      <div class="doc-card opacity-0 bg-green-50 p-4 rounded-lg transition-all duration-300" data-type="comercial">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <i class="bi bi-list-check text-green-500 text-xl"></i>
            <div>
              <h3 class="font-medium text-gray-800">Packing list</h3>
              <p class="text-sm text-gray-500">Ver documento</p>
            </div>
          </div>
          <button class="download-btn text-green-500 hover:text-green-700">
            <i class="bi bi-download"></i>
          </button>
        </div>
      </div>

      <div class="doc-card opacity-0 bg-yellow-50 p-4 rounded-lg transition-all duration-300" data-type="legal">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <i class="bi bi-envelope text-yellow-500 text-xl"></i>
            <div>
              <h3 class="font-medium text-gray-800">Carta Aclaratoria</h3>
              <p class="text-sm text-gray-500">Ver documento</p>
            </div>
          </div>
          <button class="download-btn text-yellow-500 hover:text-yellow-700">
            <i class="bi bi-download"></i>
          </button>
        </div>
      </div> -->
    </div>
  </section>
  <section class="bg-gray-50"
    id="documentacion-aduana-container">
    <div class="max-w-7xl mx-auto p-6">
      <!-- Header -->
      <div class="flex justify-between items-center mb-6">
        <div>
          <h1 class="text-2xl font-semibold text-gray-800">Formulario de Aduana</h1>
          <p class="text-gray-500 mt-1">Complete la información para el trámite aduanero</p>
        </div>
        <div class="text-gray-600 bg-gray-100 px-4 py-2 rounded-lg">
          <button type="button" id="btn-back-documentacion-aduana" class="btn btn-outline-primary btn-block btn-reporte" data-type="html"><i class="fa fa-arrow-left"></i> </button>
        </div>
      </div>

      <!-- Tabs -->
      <div class="flex space-x-4 mb-8">
        <button class="tab-btn active flex items-center space-x-2 px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-100" data-tab="general">
          <i class="bi bi-box-seam"></i>
          <span>Información General</span>
        </button>
        <button class="tab-btn flex items-center space-x-2 px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-100" data-tab="dates">
          <i class="bi bi-calendar"></i>
          <span>Fechas y Plazos</span>
        </button>
        <button class="tab-btn flex items-center space-x-2 px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-100" data-tab="values">
          <i class="bi bi-currency-dollar"></i>
          <span>Valores y Costos</span>
        </button>
      </div>

      <!-- Form -->
      <div class="bg-white rounded-xl shadow-sm p-8">
        <form id="customsForm" class="space-y-6">
          <!-- General Information Tab -->
          <div class="tab-content active" id="general">
            <div class="grid md:grid-cols-2 gap-x-12 gap-y-6">
              <div class="form-group">
                <label class="block text-gray-700 mb-2">Naviera</label>
                <select name="naviera" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                  <option value="">Seleccione una naviera</option>
                  <option value="MAERSK">MAERSK</option>
                  <option value="ONE">ONE</option>
                  <option value="COSCO">COSCO</option>
                  <option value="EVERGREEN">EVERGREEN</option>
                  <option value="MSC">MSC</option>
                  <option value="HAPAG LLOYD">HAPAG LLOYD</option>
                  <option value="CMA CGM">CMA CGM</option>
                  <option value="YANG MING">YANG MING</option>
                  <option value="ZIM">ZIM</option>
                </select>
              </div>

              <div class="form-group">
                <label class="block text-gray-700 mb-2">Tipo de Contenedor</label>
                <select name="tipo_contenedor" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                  <option value="">Seleccione tipo</option>
                  <option value="LCL">LCL</option>
                  <option value="20 GP">20 GP</option>
                  <option value="40 NOR">40 NOR</option>
                  <option value="40 GP">40 GP</option>
                  <option value="40 HQ">40 HQ</option>
                </select>
              </div>

              <div class="form-group">
                <label class="block text-gray-700 mb-2">Canal de Control</label>
                <div class="relative">
                  <select name="canal_control" class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="controlChannel">
                    <option value="">Seleccione canal</option>
                    <option value="Verde">Verde</option>
                    <option value="Naranja">Naranja</option>
                    <option value="Rojo">Rojo</option>
                  </select>
                  <div class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 rounded-full" id="channelIndicator"></div>
                </div>
              </div>

              <div class="form-group">
                <label class="block text-gray-700 mb-2">Número DUA</label>
                <input name="numero_dua" type="text" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
              </div>
            </div>
          </div>

          <!-- Dates Tab -->
          <div class="tab-content hidden" id="dates">
            <div class="grid md:grid-cols-2 gap-x-12 gap-y-6">
              <div class="form-group">
                <label class="block text-gray-700 mb-2">F. ZARPE</label>
                <input name="fecha_zarpe" type="date" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
              </div>

              <div class="form-group">
                <label class="block text-gray-700 mb-2">F. ARRIBO</label>
                <input name="fecha_arribo" type="date" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
              </div>

              <div class="form-group">
                <label class="block text-gray-700 mb-2">F. DECLARACIÓN</label>
                <input name="fecha_declaracion" type="date" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
              </div>

              <div class="form-group">
                <label class="block text-gray-700 mb-2">F. LEVANTE</label>
                <input name="fecha_levante" type="date" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
              </div>
            </div>
          </div>

          <!-- Values Tab -->
          <div class="tab-content hidden" id="values">
            <div class="grid md:grid-cols-2 gap-x-12 gap-y-6">
              <div class="form-group">
                <label class="block text-gray-700 mb-2">VALOR FOB</label>
                <div class="relative">
                  <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                  <input name="valor_fob" type="number" step="0.01" class="w-full pl-8 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
              </div>

              <div class="form-group">
                <label class="block text-gray-700 mb-2">VALOR FLETE</label>
                <div class="relative">
                  <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                  <input name="valor_flete" type="number" step="0.01" class="w-full pl-8 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
              </div>

              <div class="form-group">
                <label class="block text-gray-700 mb-2">COSTO DESTINO</label>
                <div class="relative">
                  <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                  <input name="costo_destino" type="number" step="0.01" class="w-full pl-8 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
              </div>

              <div class="form-group">
                <label class="block text-gray-700 mb-2">AJUSTE DE VALOR</label>
                <div class="relative">
                  <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                  <input name="ajuste_valor" type="number" step="0.01" class="w-full pl-8 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
              </div>

              <div class="form-group">
                <label class="block text-gray-700 mb-2">MULTA</label>
                <div class="relative">
                  <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                  <input name="multa" type="number" step="0.01" class="w-full pl-8 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
              </div>

              <div class="form-group">
                <label class="block text-gray-700 mb-2">OBSERVACIONES</label>
                <textarea name="observaciones" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" rows="3"></textarea>
              </div>
            </div>
          </div>

          <!-- Buttons -->
          <div class="flex justify-between pt-6 border-t mt-6">

            <button type="submit" class="px-6 py-2 text-white bg-black rounded-lg hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900">
              Guardar
            </button>
          </div>
        </form>
      </div>
    </div>

  </section>
  <section class="content" id="cotizacion-container">
    <div class="container-fluid ">
      <!-- Header de la tabla -->
      <?php if ($this->user->No_Grupo != "ContenedorAlmacen") {  ?>
      <div class="row mb-2">
        <div class="col-12 col-md-1">
          <button type="button" class="bg-white hover:bg-white-200 text-black-200 py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block btn-reporte btn-back-cotizacion" data-type="html"><i class="fa fa-arrow-left"></i> Regresar</button>
        </div>
        <div class="col-sm-5"></div>
        <!-- Buscador de la tabla -->
        <div class="col-6 col-sm-2">
          <div class="dataTables_filter" style="display: flex;justify-content: flex-end;">
            <input type="search" class="form-control bg-white hover:bg-white-200 text-black-200 py-2 border border-transparent hover:border-orange-600 rounded search-table" placeholder="Buscar por: " aria-controls="table-contenedor" style="width:250px; padding-left: 40px; background: url('https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/svgs/solid/search.svg') no-repeat 15px center;background-size: 14px; font-size: 14px;">
          </div>
        </div>
        <!-- Contenedor Principal de Exportar-->
        <div class="col-6 col-sm-1 dropdown">
          <button type="button" id="btn-exportar-carga" class="bg-white hover:bg-white-200 text-black-200 py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block btn-reporte" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-upload"></i> Exportar</button>
          <div class="dropdown-menu dropdown-menu-right px-3 py-3" aria-labelledby="btn-exportar-carga">
            <button class="dropdown-item btn-block export-pdf-main-content"><i class="fa fa-file-pdf color_icon_pdf"></i>Exportar PDF</button>
            <button class="dropdown-item btn-block export-excel-main-content"><i class="fa fa-file-excel color_icon_excel"></i>Exportar Excel</button>
          </div>
        </div>
        <!-- Contenedor Principal de Filtros-->
        <div class=" col-6 col-sm-1 dropdown">
          <!-- Botón de Filtros -->
          <button class="bg-white py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block btn-reporte" type="button" id="btn-filtrar-carga" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-filter"></i>Filtros
          </button>
          <!-- Menú Desplegable -->
          <div class="dropdown-menu dropdown-menu-right px-3 py-3" aria-labelledby="btn-filtrar-carga">
            <div class="form-group">
              <div class="d-flex align-items-center p-2">
                <div class="d-flex" style="width:60%">Fecha Inicio</div>
                <div style="width: 200px;">
                  <input type="text" id="txt-Fe_Inicio_Carga" class="form-control text-center input-date input-report required" value="<?php echo dateNow('month_date_ini_report'); ?>">
                  <span class="help-block text-danger" id="error"></span>
                </div>
              </div>
              <div class="d-flex align-items-center p-2">
                <div class="d-flex" style="width:60%">Fecha Fin</div>
                <div style="width: 200px;">
                  <input type="text" id="txt-Fe_Fin_Carga" class="form-control input-date input-report required">
                  <span class="help-block text-danger" id="error"></span>
                </div>
              </div>
              <div class="d-flex align-items-center p-2" style="width:300px;">
                <div class="d-flex" style="width:60%">Estado</div>
                <div style="width: 200px;">
                  <select id="txt-ID_Estado" name="ID_Estado" class="form-control input-estado" >
                    <option value="0" selected>Todos</option>
                    <option value="PENDIENTE">PENDIENTE</option>
                    <option value="RECIBIENDO">RECIBIENDO</option>
                    <option value="COMPLETADO">COMPLETADO</option>
                  </select>
                </div>
                
              </div>
          </div>         
          <div class="dropdown-divider"></div>
          <!-- Botones -->
          <div class="d-flex justify-content-around">
                <button class="bg-white py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block" style="margin-top: .5rem;" id="cancelar-btn">Cancelar</button>
                <button class="bg-orange py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block" id="aplicar-btn">Aplicar</button>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-2">     
            <button type="button" id="btn-crear-cotizacion" class="bg-orange text-black-200 py-2 px-2 border border-transparent rounded btn-block btn-reporte" data-type="html"><i class="fa fa-plus"></i> Crear Prospecto</button>
        </div>
        <div class="col-12 col-md-4">
          <label>&nbsp;</label>
        </div>
      </div>
      <?php } else{ ?>
      <div class="row mb-2">
        <div class="col-12 col-md-1">
          <button type="button" class="bg-white hover:bg-white-200 text-black-200 py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block btn-reporte btn-back-cotizacion" data-type="html"><i class="fa fa-arrow-left"></i> Regresar</button>
        </div>
        <div class="col-sm-5"></div>
        <div class="col-12 col-md-2"></div>
        <div class="col-6 col-sm-2">
          <div class="dataTables_filter" style="display: flex;justify-content: flex-end;">
            <input type="search" class="form-control bg-white hover:bg-white-200 text-black-200 py-2 border border-transparent hover:border-orange-600 rounded search-table" placeholder="Buscar por: " aria-controls="table-contenedor" style="width:250px; padding-left: 40px; background: url('https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/svgs/solid/search.svg') no-repeat 15px center;background-size: 14px; font-size: 14px;">
          </div>
        </div>
        <!-- Contenedor Principal de Cargar-->
        <div class="col-6 col-sm-1 dropdown">
          <button type="button" id="btn-cargar-carga" class="bg-white hover:bg-white-200 text-black-200 py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block btn-reporte" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-upload"></i> Cargar</button>
          <div class="dropdown-menu dropdown-menu-right px-3 py-3" aria-labelledby="btn-cargar-carga">
            <div class="dropdown-item btn-block" id ="packing-list-container"></div>
            <div class="dropdown-item btn-block" id="bl-file-container"></div>
          </div>
        </div>
        <!-- Contenedor Principal de Exportar-->
        <div class="col-6 col-sm-1 dropdown">
          <button type="button" id="btn-exportar-carga" class="bg-white hover:bg-white-200 text-black-200 py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block btn-reporte" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-download"></i> Exportar</button>
          <div class="dropdown-menu dropdown-menu-right px-3 py-3" aria-labelledby="btn-exportar-carga">
            <button class="dropdown-item btn-block" id="export-excel"><i class="fa fa-file-excel color_icon_excel"></i>Exportar Excel</button>
          </div>
        </div>
        <div class="col-12 col-md-4">
          <label>&nbsp;</label>
        </div>
      </div>
      <?php } ?>


      <div class="row mb-2" style="border-bottom: #DFDFDF solid 2px;max-width: 100%;">
        <?php if ($this->user->No_Grupo != "ContenedorAlmacen") {  ?>
          <div class="d-flex align-items-center" style="border-right: #DFDFDF solid 2px; width:10%; padding:15px 10px">
            <span>Consolidado #</span>
            <div class="col-md-1">
              <input id="cotizacion_name" disabled>
            </div>
          </div>
        <?php } ?>

        <div class="col-12 col-md-2 d-flex align-items-center">
          <i class="fas fa-flag px-2"></i>
          <span>CBM Total Peru:</span>
          <div class="col-md-1">
            <strong><input type="number" id="txt-CBM_Total_Peru" class="cbm_score" disabled></strong>
          </div>
        </div>
        <div class="col-12 col-md-2 d-flex align-items-center">
          <i class="fas fa-flag px-2"></i>
          <span>CBM Total China:</span>
          <div class="col-md-1">
            <strong><input type="number" id="txt-CBM_Total_China" class="cbm_score" disabled></strong>
          </div>
        </div>        
      </div>
      <!-- Body de la tabla -->
      <div class="table-responsive">
        <table id="table-cotizacion-prospectos" class="table table-hover dataTable no-footer">
          <thead class="thead-default">
            <tr>
              <th>N°</th>
              <th>Fecha</th>
              <th>Nombre</th>
              <th>DNI/RUC</th>
              <th>Correo</th>
              <th>Whatsapp</th>
              <th>T. Cliente</th>
              <th>Volumen</th>
              <th>Precio Cbm</th>
              <th>Tarifa</th>
              <th>Cotizacion</th>
              <?php if ($this->user->No_Grupo == "Cotizador") {  ?>
                <th>Estado</th>
              <?php } ?>
              <?php if ($this->user->No_Grupo == "Coordinación" || $this->user->No_Grupo == "Cotizador") {  ?>

                <th>Acciones
                </th>
              <?php } ?>
            </tr>
          </thead>
        </table>
        <table id="table-cotizacion-embarque" class="table table-hover dataTable no-footer embarque">
          <thead class="thead-default">
            <tr>
              <?php if (
                $this->user->No_Grupo != "ContenedorAlmacen"
                && $this->user->No_Grupo != "Documentacion"
              ) {  ?>
                <th>Asesor</th>
              <?php } ?>
              <th style="min-width: 8em;" class="no-sort">Status</th>
              <th class="orderable">N.</th>
              <th style="min-width: 14em;">Buyer</th>
              <?php if ($this->user->No_Grupo != "ContenedorAlmacen" && $this->user->No_Grupo != "Documentacion") {  ?>
                <th style="min-width: 8em;">Whatsapp</th>
                <th
                  style="min-width: 10em;">Estado</th>
              <?php } ?>

              <th
                style="min-width: 8em;">Productos</th>
              <th
                style="min-width: 4em;">Qty Box</th>
              <th
                style="min-width: 4em;">CBM t.</th>
              <th
                style="min-width: 4em;">Weight</th>
              <th
                style="min-width: 6em;">Supplier</th>
              <th
                style="min-width: 6em;">C. Supplier</th>
              <th
                style="min-width: 7em;">P. Number</th>
              <th
                style="min-width: 4em;">Qty Box.</th>
              <th
                style="min-width: 6em;">CBM China </th>
              <th
                style="min-width: 6em;">Arrive Date </th>
              <th> Acciones </th>


            </tr>
          </thead>
        </table>
      </div>
    </div>
  </section>


  <!-- Clientes View -->

  <section class="content px-3" id="clientes-container">
    <!-- header de la tabla -->
    <div class="row mb-2">
      <div class="col-12 col-md-1">
        <button type="button" class="bg-white hover:bg-white-200 text-black-200 py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block btn-reporte btn-back-cotizacion" data-type="html"><i class="fa fa-arrow-left"></i> Regresar</button>
      </div>
      <div class="col-sm-3"></div>
      <div class="col-6 col-sm-1">
      </div>
      <div class="col-12 col-md-1">
      </div>
      <div class="col-12 col-md-3">
        <label>&nbsp;</label>
      </div>
      <div class="col-6 col-sm-2">
        <div class="dataTables_filter" style="display: flex;justify-content: flex-end;">
          <input type="search" class="form-control bg-white hover:bg-white-200 text-black-200 py-2 border border-transparent hover:border-orange-600 rounded search-table" placeholder="Buscar por: " aria-controls="table-contenedor" style="width:250px; padding-left: 40px; background: url('https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/svgs/solid/search.svg') no-repeat 15px center;background-size: 14px; font-size: 14px;">
        </div>
      </div>
        <!-- Contenedor Principal de Exportar-->
      <div class="col-6 col-sm-1 dropdown">
          <button type="button" id="btn-exportar-carga" class="bg-white hover:bg-white-200 text-black-200 py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block btn-reporte" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-download"></i> Exportar</button>
          <div class="dropdown-menu dropdown-menu-right px-3 py-3" aria-labelledby="btn-exportar-carga">
            <button class="dropdown-item btn-block export-pdf-main-content" ><i class="fa fa-file-pdf color_icon_pdf"></i>Exportar PDF</button>
            <button class="dropdown-item btn-block export-excel-main-content"><i class="fa fa-file-excel color_icon_excel"></i>Exportar Excel</button>
          </div>
      </div>
    </div>

    <div class="table-responsive" class="table table-bordered table-hover table-striped">
      <div class="row pl-3 mb-4" style="border-bottom: #DFDFDF solid 2px;max-width: 100%;">
        <div class="d-flex align-items-center" style="width:10%; padding:15px 10px">
          <span>Clientes</span>
        </div>
      </div>
      <table id="table-clientes-general" class="table table-hover dataTable no-footer">
        <thead class="thead-default">
          <tr>
            <th>N°</th>
            <th>Nombre</th>
            <th>DNI/RUC</th>
            <th>Correo</th>
            <th>Whatsapp</th>
            <th>T. Cliente</th>
            <th>Volumen</th>
            <th>Monto</th>
            <th>Tarifa</th>

            <th>Estados</th>
            <th>Acciones</th>
          </tr>
        </thead>
      </table>
      <table id="table-clientes-variacion" class="table table-hover dataTable no-footer">
        <thead class="thead-default">
          <tr>
            <th>N°</th>
            <th>Nombre</th>
            <th>DNI/RUC</th>
            <th>T. Cliente</th>
            <th>Tarifa</th>
            <th>Vol. Cot</th>
            <th>Vol. China</th>
            <th>Vol. Doc</th>
            <th>Valor Cot</th>
            <th>Valor Doc</th>
            <th>Variación</th>
          </tr>
        </thead>
      </table>
    </div>
  </section>
  <section class="content card px-4 py-3" id="clientes-documentation-container">

    <div class="col col-12 my-2" id="clientes-documentacion">
      <h3
        class="d-flex w-100 row documentation-title">
        <span

          data-toggle="collapse"
          href="#collapse-documentacion"
          role="button"
          aria-expanded="false"
          aria-controls="collapse-documentacion"
          class="col-9">
          Documentación
        </span>
        <div
          class="col-3 d-flex justify-content-end">
          <button type="button" id="btn-crear-documentacion" class="btn btn-outline-primary" data-type="html"><i class="fa fa-upload"></i>Nuevo documento</button>
          <button type="button" id="btn-back-cliente-documentacion" class="btn btn-outline-primary" data-type="html"><i class="fa fa-arrow-left"></i></button>
        </div>
      </h3>
      <div class="collapse show row row-cols-1" id="collapse-documentacion">
        <form id="form-documentacion" class="form-horizontal  mb-2 row">
          <div class="col-6">
            <label>Vol. Doc</label>
            <input type="text" id="txt-Vol_Doc"
              name="volumen_doc"
              class="form-control input-report required">
            <span class="invalid-feedback" id="error-vol-doc">El volumen es requerido</span>
          </div>
          <div class="col-6">
            <label class="form-label">Valor Doc</label>
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <span class="input-group-text">$</span>
              </div>
              <input type="number"

                step="0.01"
                name="valor_doc"
                id="txt-Valor_Doc" class="form-control input-report required">
              <span class="invalid-feedback" id="error-valor-doc">El valor es requerido</span>
            </div>
          </div>
          <div class="col-3">
            <label>F. Comercial</label>
            <div id="factura-comercial">
            </div>
          </div>
          <div class="col-3">
            <label>Excel Confirmacion</label>
            <div id="excel-confirmacion">
            </div>
          </div>
          <div class="col-12 col-guardar-documentacion m-2 d-flex justify-content-center align-items-center">
            <div id="btn-guardar-documentacion"

              class="btn btn-primary btn-block btn-reporte col-6" data-type="html"><i class="fa fa-save"></i> Guardar</div>
          </div>
        </form>

      </div>
    </div>
    <div class="col col-12 my-2" id="clientes-cotizacion">
      <h3
        data-toggle="collapse"
        href="#collapse-cotizacion"
        role="button"
        aria-expanded="false"
        aria-controls="collapse-cotizacion"
        class="documentation-title">Cotizaciones</h3>
      <div class="collapse show row row-cols-4 " id="collapse-cotizacion">
        <div class="col">
          <a id="btn-descargar-cotizacion-inicial"
            class="btn btn-outline-success btn-block " target="_blank" href="#"><i class="fa fa-download"></i> Descargar Cotización Inicial</a>
        </div>
        <div class="col">
          <button type="button" id="btn-descargar-cotizacion-final" class="btn btn-outline-success btn-block btn-reporte" data-type="html"><i class="fa fa-download"></i> Descargar Cotización Final</button>
        </div>
      </div>
    </div>
    <div class="col col-12 my-2" id="clientes-inpection">
      <h3
        data-toggle="collapse"
        href="#collapse-inspection"
        role="button"
        aria-expanded="false"
        aria-controls="collapse-inspection"
        class="documentation-title">Inpection</h3>
      <div class="collapse show row row-cols-4 " id="collapse-inspection">
        <div class="mx-auto px-4 py-8 file-section-container col-12 col-md-12">
          <h2 class="text-lg font-semibold bg-white  shadow documentation-title d-flex justify-content-between p-5 rounded-top">Inspection

          </h2>
          <div id="drag-drop-container-inspection-coordinacion"
            class="drag-drop-area border-2 border-dashed border-gray-300 rounded-lg p-4 text-center mb-4 hidden">
            <div id="drop-message">
              <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4 block"></i>
              <p class="text-gray-600">
                Arrastra y suelta archivos aquí
              </p>
              <p class="text-xs text-gray-500 mt-2">
                Soporta: PDF, JPG, PNG, DOCX (Máximo 10MB)
              </p>
            </div>
          </div>

          <div id="file-grid-inspection-coordinacion" class="grid grid-cols-4 gap-4 text-lg font-semibold bg-white shadow p-3 rounded-bot">
          </div>
          <div id="pending-files-inspection-coordinacion hidden" class="mb-4">
            <h2 class="text-lg font-semibold mb-2">Archivos Pendientes</h2>
            <div id="pending-file-list-inspection-coordinacion" class="space-y-2">
              <!-- Los archivos pendientes aparecerán aquí -->
            </div>
          </div>
        </div>
      </div>

  </section>
  <section class="content" id="cotizacion-almacen">
    <!--row with button back and search-->
    <div class="row mb-2">
      <div class="col-12 col-md-1">
        <button type="button" class="bg-white hover:bg-white-200 text-black-200 py-2 px-2 mx-2 border border-transparent hover:border-orange-600 rounded btn-block btn-reporte" id="btn-back-cotizacion-almacen"><i class="fa fa-arrow-left"></i> Regresar</button>
      </div>
      <div class="col-sm-5"></div>
      <div class="col-6 col-sm-4">
      </div>
      <div class="col-12 col-md-1">
        <button type="button" id="btn-crear-cotizacion" class="bg-orange hover:bg-orange-200 text-black-200 py-2 px-20 border border-transparent hover:border-orange-600 rounded btn-block btn-reporte" data-type="html">Guardar <i class="fas fa-save"></i></button>
      </div>
      <div class="col-12 col-md-4">
        <label>&nbsp;</label>
      </div>
    </div>



    <div class="row mb-2 ml-2" style="border-bottom: #DFDFDF solid 2px;">
      <div class="d-flex col-12 col-md-1 align-items-center" style="border-right: #DFDFDF solid 2px; max-width:12%; padding:15px 10px">
        <span>Consolidado #</span>
        <div class="col-md-1">
          <input id="cotizacion_name" disabled="">
        </div>
      </div>
      <div class="col-12 col-md-1 pl-4 d-flex align-items-center" style="border-right: #DFDFDF solid 2px;">
        <span id="client-title"></span>
      </div>
      <div class="col-12 col-md-1 pl-4 d-flex align-items-center">
        <span id="client-supplier-code"></span>
      </div>
    </div>

    <div class="row mb-2">
      <div class="px-4 py-8 file-section-container col-12 col-md-5  ">
        <div>
          <div class="text-lg font-semibold bg-white  shadow documentation-title d-flex justify-content-between p-5 rounded-top">
            <h2
            class="d-flex w-100 justify-content-between align-items-center"><label>Documentación <i class="far fa-folder-open"></i></label>
            <div id="btn-guardar-documentation" onclick="saveDocumentation()" class="new-doc-btn hover-effect flex items-center space-x-2 px-4 py-2 text-white rounded-lg hover:bg-orange-700 transition-colors bg-orange border border-transparent rounded" data-type="html"><i class="fa fa-save"></i> Guardar
            </div>
          </h2>
          </div>
        </div>
        <div id="drag-drop-container"
          class="drag-drop-area border-2 border-dashed border-gray-300 rounded-lg p-4 text-center mb-4 hidden bg-white rounded-lg shadow">
          <div id="drop-message">
            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4 block"></i>
            <p class="text-gray-600">
              
              Arrastra y suelta archivos aquí
            </p>
            <p class="text-xs text-gray-500 mt-2">
              Soporta: PDF, JPG, PNG, DOCX (Máximo 10MB)
            </p>
          </div>
        </div>
        <div id="file-grid" class="grid gap-4 text-lg font-semibold bg-white shadow p-3 rounded-bot">

          <form class="form-horizontal" method="post">
            <div class="row">
              <div class="col-12 col-sm-12" id="multiple-file-upload">
                <div class="form-group">
                  <div class="file-upload-box">
                    <input type="file" id="file-input-documentacion" class="file-input"  multiple accept="*/*" />
                    <label for="file-inpute" class="file-label d-flex">
                      <i class="fas fa-upload"></i>
                      <div class="file-group-text">
                        <span class="file-text">Selecciona o arrastra tu archivo aquí</span>
                        <span class="file-format">Formatos: .xlsx</span>
                      </div>
                      <button class="upload-button" type="button">Subir archivo</button>
                    </label>
                  </div>
                  <!-- Cuadro de información del archivo subido (oculto inicialmente) -->
                  <div class="file-lista hidden" id="file-lista-documentacion">
                  </div>
                  <!-- <input type="file" id="txt-Cotizacion" name="cotizacion" required class="form-control input-report required"> -->
                  <span class="invalid-feedback" id="error-volumen">La cotización es requerida</span>
                </div>
              </div>
            </div>
          </form>
          <!-- Existing and uploaded files will appear here -->
        </div>
        <div id="pending-files" class="hidden d-none">
          <h2 class="text-lg font-semibold mb-2">Archivos Pendientes</h2>
          <div id="pending-file-list" class="space-y-2">
            <!-- Los archivos pendientes aparecerán aquí -->
          </div>
        </div>
      </div>
      <div class="col-12 col-md-4">
        <div class="container px-4 py-8 file-section-container col-12 col-md-12">
          <h2 class="
          
          text-lg font-semibold bg-white  shadow documentation-title d-flex justify-content-between p-5 rounded-top">Inspection
            <div id="btn-guardar-inspection"
              onclick="saveInspection()"
              class="new-doc-btn hover-effect flex items-center space-x-2 px-4 py-2 text-white rounded-lg hover:bg-orange-700 transition-colors bg-orange border border-transparent rounded" data-type="html"><i class="fa fa-save"></i> Guardar
            </div>
          </h2>
          <!--Button para guardar-->

          <div id="file-grid-inspection" class="grid gap-4 text-lg font-semibold bg-white shadow p-3 rounded-bot">
            <form class="form-horizontal" method="post">
              <div class="row">
                <div class="col-12 col-sm-12" id="multiple-file-upload-image">
                  <div class="form-group">
                    <div class="file-upload-box">
                      <input type="file" id="file-input-inspeccion" class="file-input" multiple accept=".jpeg, .jpg, .png, .mp4" />
                      <label for="file-inpute" class="file-label d-flex">
                        <i class="fas fa-upload"></i>
                        <div class="file-group-text">
                          <span class="file-text">Selecciona o arrastra tu archivo aquí</span>
                          <span class="file-format">Formatos: .jpeg .png .mp4</span>
                        </div>
                        <button class="upload-button" type="button">Subir archivo</button>
                      </label>
                    </div>
                    <!-- Cuadro de información del archivo subido (oculto inicialmente) -->
                    <div class="file-lista hidden" id="file-lista-inspection">
                    </div>
                    <!-- <input type="file" id="txt-Cotizacion" name="cotizacion" required class="form-control input-report required"> -->
                    <span class="invalid-feedback" id="error-volumen">La cotización es requerida</span>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div id="pending-files-inspection hidden" class="mb-4 hidden">
            <h2 class="text-lg font-semibold mb-2">Archivos Pendientes</h2>
            <div id="pending-file-list-inspection" class="space-y-2">
              <!-- Los archivos pendientes aparecerán aquí -->
            </div>
          </div>
        </div>
      </div>
      <div class="col-12 col-md-3 px-4 py-8 note-container-container">
        <h2 class="text-lg font-semibold  documentation-title  bg-white d-flex justify-content-between">Notas
          <button onclick="addNote()" class="new-doc-btn hover-effect flex items-center space-x-2 px-4 py-2 text-white rounded-lg hover:bg-orange-700 transition-colors bg-orange border border-transparent rounded">
            <i class="fas fa-save  float-right"></i>

            <span>Guardar</span>
          </button>
        </h2>
        <div id="note-container" class="bg-white shadow p-4 rounded-lg">
          <textarea id="txt-Id_Carga_Consolidada"
            class="form-control"></textarea>
        </div>
      </div>
    </div>


  </section>
  <section id="steps" class="content">
    <div id="steps-container">
    </div>
    <div class="steps-buttons">
    </div>
  </section>
  <section class="content px-3" id="documentation-container">
    <!-- header -->
    <div class="row mb-2">
      <div class="col-12 col-md-1">
        <button type="button" class="bg-white text-black-200 py-2 px-2 border border-transparent rounded btn-block btn-reporte btn-back-documentacion" data-type="html"><i class="fa fa-arrow-left"></i> Regresar</button>
      </div>
      <div class="col-sm-3"></div>
      <div class="col-6 col-sm-2"></div>
      <div class="col-6 col-sm-2">
        <button type="button" id="btn-documentacion-factura" class="bg-white text-black-200 py-2 px-2 border border-transparent rounded btn-block btn-reporte" data-type="html"><i class="fas fa-file-invoice"></i>Factura General</button>
      </div>
      <div class="col-6 col-sm-2">
        <button type="button" id="btn-documentacion-zip" class="bg-white text-black-200 py-2 px-2 border border-transparent rounded btn-block btn-reporte" data-type="html"><i class="fa fa-download"></i>Descargar todo</button>
      </div>
      <div class="col-12 col-md-2">
        <button type="button" id="btn-documentacion-new" class="bg-orange text-black-200 py-2 px-2 border border-transparent rounded btn-block btn-reporte" data-type="html">Nuevo documento<i class="fa fa-plus"></i></button>
      </div>
      <div class="mx-10" style="border-bottom: #DFDFDF solid 2px; width:100%">
        <label>&nbsp;</label>
      </div>
    </div>

    <!-- body -->
    <div class="row m-20 documentation-files-container
      grid grid-cols-2   gap-4 text-lg font-semibold bg-white shadow p-3 rounded-lg">
    </div>

  </section>
  <section class="content card" id="cotizacion-final-container">
    <div class="min-h-screen bg-gray-50 p-8">
      <h1 class="text-3xl font-bold text-gray-800 mb-6">CONSOLIDADO #1: COTIZACIÓN FINAL</h1>

      <div class="flex gap-4 mb-8">

        <button
          id="uploadGeneral"
          class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors flex items-center gap-2">
          <div class="fa fa-upload"></div>
          Subir Factura
        </button>
        <button
          id="downloadTemplate"
          class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors flex items-center gap-2">
          <div class="fa fa-download"></div>
          Plantilla General
        </button>
        <button
          id="uploadFinal"
          class="px-6 py-2 bg-white border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors flex items-center gap-2">
          <div class="fa fa-upload"></div>
          Plantilla Final
        </button>
        <!--button back-->
        <button
          id="btn-back-cotizacion-final"
          class="px-6 py-2 bg-white border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors flex items-center gap-2">
          <div class="fa fa-arrow-left"></div>
        </button>
      </div>
      <div class="table-responsive" class="table table-bordered table-hover table-striped">
        <table id="table-cotizacion-final" class="table table-bordered table-hover table-striped">
          <thead class="thead-light">
            <tr>
              <th>N°</th>
              <th>Nombre</th>
              <th>DNI/RUC</th>
              <th>Correo</th>
              <th>Whatsapp</th>
              <th>T. Cliente</th>
              <th>Volumen F</th>
              <th>Precio Cbm</th>
              <th>Tarifa F</th>
              <th>Estados</th>
              <th>C Final</th>
            </tr>
          </thead>
        </table>
      </div>
  </section>
  <section class="content card" id="factura-guia-container">
    <div class="min-h-screen bg-gray-50 p-8">
      <h1 class="text-3xl font-bold text-gray-800 mb-6">CONSOLIDADO #1: FACTURA Y GUIA</h1>
      <div class="flex 
      w-full gap-4 mb-8
      justify-end
      
      ">
        <!--3 empty divs-->

        <!--button back-->
        <button
          id="btn-back-factura-guia"
          class="px-6 py-2 bg-white border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors flex items-center gap-2">
          <div class="fa fa-arrow-left"></div>
        </button>
      </div>
      <div class="table-responsive" class="table table-bordered table-hover table-striped">
        <table id="table-factura-guia" class="table table-bordered table-hover table-striped">
          <thead class="thead-light">
            <tr>
              <th>N°</th>
              <th>Nombre</th>
              <th>DNI/RUC</th>
              <th>Correo</th>
              <th>Whatsapp</th>
              <th>T. Cliente</th>
              <th>Ajuste</th>
              <th>C.Final</th>
              <th>Factura</th>
              <th>Guia R</th>
            </tr>
          </thead>
        </table>
      </div>
  </section>
  <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="uploadModalLabel">Subir Documentos</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="upload-body">
          <input type="file" class="form-control-file" id="file-input-modal" accept="*/*" multiple>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary upload-btn">Upload</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="uploadModalInspection" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="uploadModalLabel">Subir Documentos</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="upload-body">
          <input type="file" class="form-control-file" id="file-input-modal-inspection" accept="*/*" multiple>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary upload-btn-inspection">Upload</button>
        </div>
      </div>
    </div>
  </div>
  <!-- Modal para imágenes -->
  <div class="modal fade" id="image-modal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-body">
          <img id="image-preview" src="" style="width: 100%;">
        </div>
      </div>
    </div>
  </div>

  <!-- Modal para videos -->
  <div class="modal fade" id="video-modal" tabindex="-1" role="dialog" aria-labelledby="videoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-body">
          <video id="video-preview" controls style="width: 100%;">
            <source src="" type="video/mp4">
            Tu navegador no soporta la reproducción de videos.
          </video>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal para archivos (PDF, Word, Excel) -->
  <div class="modal fade" id="file-modal" tabindex="-1" role="dialog" aria-labelledby="fileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-body">
          <iframe id="file-preview" src="" style="width: 100%; height: 500px;"></iframe>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modal-crear-cotizacion" tabindex="-1" role="dialog" aria-labelledby="modal-cotizacion" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Crear Prospecto</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="form-crear-cotizacion" class="form-horizontal" method="post">
            <div class="row">

              <div class="col-12 col-sm-12" id="single-file-upload">
                <div class="form-group">
                  <div class="file-upload-box">
                    <input type="file" id="file-input-prospecto" class="file-input" name="cotizacion"
                      accept=".xlsx,.xls,.csv,.xlsb,.xlsm,.xltx,.xltm,.xls,.xlt" />
                    <label for="file-inpute" class="file-label d-flex">
                      <i class="fas fa-upload"></i>
                      <div class="file-group-text">
                        <span class="file-text">Selecciona o arrastra tu archivo aquí</span>
                        <span class="file-format">Formatos: .xlsx</span>
                      </div>
                      <button class="upload-button" type="button">Subir archivo</button>
                    </label>

                    <!-- Cuadro de información del archivo subido (oculto inicialmente) -->
                    <div class="file-info-box hidden">
                      <div class="file-info">
                        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="50" height="50" viewBox="0 0 48 48">
                          <rect width="16" height="9" x="28" y="15" fill="#21a366"></rect>
                          <path fill="#185c37" d="M44,24H12v16c0,1.105,0.895,2,2,2h28c1.105,0,2-0.895,2-2V24z"></path>
                          <rect width="16" height="9" x="28" y="24" fill="#107c42"></rect>
                          <rect width="16" height="9" x="12" y="15" fill="#3fa071"></rect>
                          <path fill="#33c481" d="M42,6H28v9h16V8C44,6.895,43.105,6,42,6z"></path>
                          <path fill="#21a366" d="M14,6h14v9H12V8C12,6.895,12.895,6,14,6z"></path>
                          <path d="M22.319,13H12v24h10.319C24.352,37,26,35.352,26,33.319V16.681C26,14.648,24.352,13,22.319,13z" opacity=".05"></path>
                          <path d="M22.213,36H12V13.333h10.213c1.724,0,3.121,1.397,3.121,3.121v16.425	C25.333,34.603,23.936,36,22.213,36z" opacity=".07"></path>
                          <path d="M22.106,35H12V13.667h10.106c1.414,0,2.56,1.146,2.56,2.56V32.44C24.667,33.854,23.52,35,22.106,35z" opacity=".09"></path>
                          <linearGradient id="flEJnwg7q~uKUdkX0KCyBa_UECmBSgBOvPT_gr1" x1="4.725" x2="23.055" y1="14.725" y2="33.055" gradientUnits="userSpaceOnUse">
                            <stop offset="0" stop-color="#18884f"></stop>
                            <stop offset="1" stop-color="#0b6731"></stop>
                          </linearGradient>
                          <path fill="url(#flEJnwg7q~uKUdkX0KCyBa_UECmBSgBOvPT_gr1)" d="M22,34H6c-1.105,0-2-0.895-2-2V16c0-1.105,0.895-2,2-2h16c1.105,0,2,0.895,2,2v16	C24,33.105,23.105,34,22,34z"></path>
                          <path fill="#fff" d="M9.807,19h2.386l1.936,3.754L16.175,19h2.229l-3.071,5l3.141,5h-2.351l-2.11-3.93L11.912,29H9.526	l3.193-5.018L9.807,19z"></path>
                        </svg>
                        <span class="file-name"></span>
                        <span class="file-size"></span>
                        <button class="remove-file-button">
                          <i class="fas fa-trash"></i>
                        </button>
                      </div>
                    </div>

                  </div>
                  <!-- <input type="file" id="txt-Cotizacion" name="cotizacion" required class="form-control input-report required"> -->
                  <span class="invalid-feedback" id="error-volumen">La cotización es requerida</span>
                </div>
              </div>
            </div>
          </form>
          <div class="modal-footer">
            <button type="button" class="bg-white hover:bg-white-200 text-black-200 py-2 px-5 border border-transparent hover:border-orange-600 rounded" data-dismiss="modal">Cancelar</button>
            <button type="button" id="btn-actualizar-cotizacion" class="bg-orange py-2 px-5 border border-transparent hover:border-orange-600 rounded">Actualizar</button>
            <button type="button" id="btn-guardar-cotizacion" class="bg-orange py-2 px-5 border border-transparent hover:border-orange-600 rounded">Guardar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modal-crear" tabindex="-1" role="dialog" aria-labelledby="modal-crear" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Crear Carga Consolidada</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="form-crear" class="form-horizontal" method="post">
            <div class="row">
              <div class="col-6 col-sm-6"> <!--carga -->
                <div class="form-group">
                  <label>Carga <span class="label-advertencia text-danger"> *</span></label>
                  <select type="text" id="txt-No_Carga" required name="carga" class="form-control input-report required">
                  </select>
                  <span class="invalid-feedback" id="error-carga">La carga es requerida</span>
                </div>
              </div>
              <div class="col-6 col-sm-6"> <!--fecha arribo -->
                <div class="form-group">

                  <label>Fecha Arribo <span class="label-advertencia text-danger"> *</span></label>
                  <input type="text" name="f_puerto" required id="txt-Fe_Puerto" placeholder="00/00/0000" class="form-control input-report required input-date w-100">
                  <span class="invalid-feedback" id="error-f-puerto">La fecha Arribo es requerida</span>
                </div>
              </div>

              <div class="col-6 col-sm-6"> <!--mes -->
                <div class="form-group">
                  <label>Mes <span class="label-advertencia text-danger"> *</span></label>

                  <select id="txt-Mes" required name="mes" class=" w-100 form-control input-report required">
                  </select>
                  <span class="invalid-feedback" id="error-mes">El mes es requerido</span>
                </div>
              </div>
              <div class="col-6 col-sm-6"> <!--fecha cierre -->
                <div class="form-group">

                  <label>Fecha Cierre <span class="label-advertencia text-danger"> *</span></label>
                  <input type="text" name="f_cierre" required id="txt-Fe_Cierre" placeholder="00/00/0000" class=" w-100 form-control input-report required input-date">
                  <span class="invalid-feedback" id="error-f-cierre">La fecha de Cierre es requerida</span>
                </div>
              </div>
              <div class="col-6 col-sm-6"> <!--pais -->
                <div class="form-group">
                  <label>Pais <span class="label-advertencia text-danger"> *</span></label>
                  <select id="txt-ID_Pais" required name="id_pais" class="form-control input-report required">

                  </select>
                  <!-- <span class="help-block text-danger" id="error"></span>
                    <input type="hidden" id="txt-ID_Carga_Consolidada" name="id" value="0"> -->
                </div>
              </div>



              <div class="col-6 col-sm-6">
                <div class="form-group" id="div-Fe_Entrega">

                  <label>Fecha Entrega <span class="label-advertencia text-danger"> *</span></label>
                  <input type="text" required name="f_entrega" id="txt-Fe_Entrega" placeholder="00/00/0000" class="w-100 form-control input-report required input-date">
                  <span class="invalid-feedback" id="error-f-entrega">La fecha entrega es requerida</span>
                </div>
              </div>
              <div class="col-6 col-sm-6"> <!--empresa -->
                <div class="form-group ">
                  <label>Empresa <span class="label-advertencia text-danger"> *</span></label>
                  <input type="text" required name="empresa" id="txt-Empresa" placeholder="Ingresa el nombre de la empresa" class="form-control input-report required text">
                  <span class="invalid-feedback" id="error-empresa">La empresa es requerida</span>
                </div>
              </div>
            </div>
        </div>
        </form>
        <div class="modal-footer">
          <button type="button" class="bg-white hover:bg-white-200 text-black-200 py-2 px-5 border border-transparent hover:border-orange-600 rounded" data-dismiss="modal">Cancelar</button>
          <button type="button" id="btn-actualizar" class="bg-orange py-2 px-5 border border-transparent hover:border-orange-600 rounded">Actualizar</button>
          <button type="button" id="btn-guardar" class="bg-orange py-2 px-5 border border-transparent hover:border-orange-600 rounded">Guardar</button>
        </div>
      </div>
    </div>
  </div>
</div>

<style scoped>
  * {
    font-family: Epilogue;
  }

  p {
    font-size: 14px;
  }

  #steps {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 1em;
    margin-top: 1em;
    flex-direction: column;
  }

  #steps-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 1em;
    margin-top: 1em;
  }

  .step-container {
    display: flex;
    flex-direction: column;
    border: 1px solid #ccc;
    border-radius: 1em;
    align-items: center;
    margin-bottom: 10px;
    width: 200px;
    padding: 1em 2em;
    animation: fadeIn 1s forwards;
    min-width: 200px;
    /* Inicialmente ocultar los elementos */
    opacity: 0;
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
    }

    to {
      opacity: 1;
    }
  }

  /* Aplicar un retraso basado en el índice del hijo */
  .step-container:nth-child(1) {
    animation-delay: 0s;
  }

  .step-container:nth-child(2) {
    animation-delay: 0.2s;
  }

  .step-container:nth-child(3) {
    animation-delay: 0.4s;
  }

  .step-container:nth-child(4) {
    animation-delay: 0.6s;
  }

  .step-container:nth-child(4) {
    animation-delay: 0.8s;
  }

  .step-container:nth-child(5) {
    animation-delay: 1s;
  }

  .step-container:nth-child(6) {
    animation-delay: 1.2s;
  }

  .step-container-completed {
    display: flex;
    flex-direction: column;
    border: 1px solid #ccc;
    border-radius: 1em;
    align-items: center;
    margin-bottom: 10px;
    width: 200px;
    padding: 1em 2em;
    animation: fadeInBounce 1s forwards;
    min-width: 200px;
    /* Inicialmente ocultar los elementos */
    opacity: 0;
    background-color: #85C1E9;
    color: white;
  }

  .step-container-progress {
    display: flex;
    flex-direction: column;
    border: 1px solid #ccc;
    border-radius: 1em;
    align-items: center;
    margin-bottom: 10px;
    width: 200px;
    padding: 1em 2em;
    animation: fadeInBounce 1s forwards;
    min-width: 200px;
    /* Inicialmente ocultar los elementos */
    opacity: 0;
    background-color: #f4d03f;
    text-align: center;
    color: white;
  }

  @keyframes fadeInBounce {
    from {
      opacity: 0;
      transform: scale(0.5);
    }

    to {
      opacity: 1;
      transform: scale(1);
    }
  }

  .step-container-completed span {
    text-align: center;
  }

  .step-container span {
    text-align: center;
  }

  .step-container:hover {
    background-color: #f9f9f9;
  }

  .collapse.show {

    visibility: visible;
  }

  input[type="number"]::-webkit-inner-spin-button,
  input[type="number"]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
  }

  .documentation-title {
    cursor: pointer;
    padding: 0 1em;
    height: 3em;
    background-color: white;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
  }

  .documentation-title:hover {
    background-color: #d5dbdb;
  }

  .delete-folder-button:hover {
    cursor: pointer;
  }

  i:hover {
    cursor: pointer;
    color: #007bff;
  }

  .drag-drop-area {
    transition: all 0.3s ease;
  }

  .drag-over {
    background-color: rgba(59, 130, 246, 0.1);
    border-color: #3b82f6;
  }

  .context-menu {
    position: absolute;
    z-index: 50;
    min-width: 150px;
    background-color: white;
    border: 1px solid #e5e7eb;
    border-radius: 0.25rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    top: 2rem;
  }

  .fade-in {
    animation: fadeIn 0.3s ease-out;
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: scale(0.95);
    }

    to {
      opacity: 1;
      transform: scale(1);
    }
  }

  .main-container {
    height: 100%;
    position: relative;
  }

  .file-list {
    position: absolute;
    bottom: 0;
    max-height: 100px;
    height: 100px;
    width: 500px;
    right: 1em;
    border-top-left-radius: 1em;
    border-top-right-radius: 1em;
    overflow-y: auto;
  }

  .file-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5em 1em;
    background-color: #f9f9f9;
    border-top-left-radius: 1em;
    border-top-right-radius: 1em;
  }

  .file-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    margin-bottom: 10px;
    background: #f9f9f9;
  }

  .progress-bar {
    height: 10px;
    background: #76c7c0;
    width: 0%;
    border-radius: 5px;
    transition: width 0.3s ease;
  }

  i {
    align-self: center;
    align-items: center;

  }

  i:hover {
    cursor: pointer;
    color: #5dade2;
  }

  #pending-files,
  #pending-files-inspection,
  #pending-files-inspection-coordinacion {
    background-color: #fff8e1;
    border: 1px solid #ffe082;
    padding: 1em;
    border-radius: 0.5em;
    position: absolute;
    bottom: 0;
    right: 1em;
    max-height: 80%;
    overflow-y: auto;
  }

  .progress-circle circle {
    transition: stroke-dashoffset 0.3s ease;
  }

  #pending-file-list .file-item,
  #pending-file-list-inspection .file-item,
  #pending-file-list-inspection-coordinacion .file-item {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
  }

  #pending-file-list .file-name,
  #pending-file-list-inspection .file-name,
  #pending-file-list-inspection-coordinacion .file-name {
    flex-grow: 1;
    padding-left: 10px;
    font-weight: 600;
  }

  #pending-file-list .text-sm,
  #pending-file-list-inspection .text-sm,
  #pending-file-list-coordinacion .text-xs {
    color: #666;
  }

  .file-section-container {
    width: 100%;
    height: 50vh;
    min-height: 600px;
    position: relative;
  }

  #file-grid,
  #file-grid-inspection,
  #file-grid-inspection-coordinacion {
    height: auto;
    width: 100%;
    gap: 1rem;
    max-height: 500px;
    min-height: 400px;
    overflow-y: auto;
  }

  h1 {
    font-weight: 600;
    font-size: 1.5em;
  }

  @keyframes blink {
    0% {
      opacity: 1;
    }

    50% {
      opacity: 0;
    }

    100% {
      opacity: 1;
    }
  }

  .btn-block {
    font-size: 14px;
    display: flex;
    justify-content: center;
    gap: 12px;
  }

  .bg-orange {
    background-color: #FF500B !important;
    color: #fff !important;
  }

  .table.table-hover.dataTable.no-footer tbody {
    background-color: white;
  }

  tr.odd>td,
  tr.even>td {
    border-top: 4px solid #f4f6f9;
    border-bottom: 4px solid #f4f6f9;
    vertical-align: middle !important;
    padding-bottom: 20px;
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

  .page-item.active .page-link {
    background-color: #FF500B;
    border-color: #FF500B;
  }

  .page-link {
    color: #585858;
  }

  .step-icon {
    padding-top: 10px;
    display: flex;
    width: 70px;
    height: 50px;
    align-items: center;
    justify-content: center;

  }

  div:where(.swal2-container) h2:where(.swal2-title) {
    font-weight: 400;
    font-size: 28px !important;
    line-height: 30px;
    color: #272A30;
  }

  button.swal2-confirm.swal2-styled.swal2-default-outline {
    color: #585858;
  }

  div:where(.swal2-container) h2:where(.swal2-title) {
    padding-bottom: 3em;
  }

  button.swal2-confirm.swal2-styled.swal2-default-outline,
  button.swal2-cancel.swal2-styled.swal2-default-outline {
    margin-top: -4em;
    padding: .8em 2.8em;
  }

  .btn-primary {
    background-color: #FF500B;
    border-color: #FF500B;
  }

  .btn-primary:hover {
    background-color: #FF500B;
    border-color: #FF500B;
  }

  h5.modal-title {
    text-align: center;
    width: 100%;
    font-weight: 500;
    font-size: 25px !important;
  }

  label:not(.form-check-label):not(.custom-file-label) {
    font-weight: normal;
  }

  @media (min-width: 576px) {
    .modal-content {
      padding: 20px 70px;
    }
  }

  input#txt-Fe_Puerto,
  input#txt-Fe_Cierre,
  input#txt-Fe_Entrega {
    width: 115px;
  }

  .col-6.col-sm-6.fech {
    padding-left: 5%;
    padding-top: 5%;
  }

  .cont-fech {
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .form-control.input-report.required.input-date::placeholder {
    text-align: center;
    color: #495057;
  }

  #txt-Empresa::placeholder {
    color: #495057;
  }

  .file-upload-box {
    border: 2px dashed #cccccc;
    padding: 1.5rem;
    border-radius: 10px;
    cursor: pointer;
    transition: border-color 0.3s ease;
  }

  .file-upload-box:hover {
    border-color: #cccccc;
  }

  .file-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
  }

  .file-input {
    display: none;
    /* Oculta el input de archivo por defecto */
  }

  .file-label {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 0.5rem;
  }

  .file-text {
    font-size: 1rem;
    color: #333333;
  }

  .file-format {
    font-size: 0.9rem;
    color: #666666;
    margin-bottom: 1rem;
  }

  .upload-button {
    width: 55%;
    padding: 0.75rem;
    background-color: #F0F4F9;
    color: #272A30;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1rem;
    transition: background-color 0.3s ease;
  }

  .upload-button:hover {
    background-color: #F0F4F9;
  }

  .table.embarque th {
    padding: .55rem;
  }

  /* Estilos para el cuadro de información del archivo subido */
  .file-info-box {
    border: 1px solid #cccccc;
    padding: 1rem;
    border-radius: 10px;
    background-color: #f9f9f9;
    margin-top: 1rem;
    text-align: left;
  }

  .file-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
  }

  .file-name {
    font-size: 1rem;
    color: #333333;
  }

  .file-size {
    font-size: 0.9rem;
    color: #666666;
  }

  /* Lista de archivos seleccionados */
  .file-lista {
    margin-top: 20px;
    text-align: left;
  }

  .file-lista.hidden {
    display: none;
  }

  .file-lista-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 5px;
    margin-bottom: 10px;
    background-color: #f9f9f9;
  }

  .file-lista-item svg {
    width: 30px;
    height: 30px;
    margin-right: 10px;
  }

  .file-lista-item span {
    font-size: 14px;
    color: #333;
    flex-grow: 1;
  }


  .remove-button {
    background: none;
    border: none;
    cursor: pointer;
    color: #ff4d4d;
    font-size: 1rem;
    transition: color 0.3s ease;
  }

  .remove-button:hover {
    color: #cc0000;
  }

  .file-list {
    margin-top: 20px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    background-color: #fff;
  }

  .file-list-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px;
    border-bottom: 1px solid #eee;
  }

  .file-list-item:last-child {
    border-bottom: none;
  }

div#table-cotizacion-embarque_filter,div#table-contenedor_filter,div#table-clientes-general_filter,div#table-cotizacion-inspection_filter,div#table-cotizacion-inspection-coordinacion_filter,div#table-clientes-variacion_filter {
    display: none;
}



</style>
<script>
  // script.js
  function setupSingleFileUpload(containerId,inputId) {
    const container = document.getElementById(containerId);
    const fileInput = $(`#${inputId}`)[0];
    const fileLabel = container.querySelector('.file-label');
    const fileInfoBox = container.querySelector('.file-info-box');
    const fileNameElement = container.querySelector('.file-name');
    const fileSizeElement = container.querySelector('.file-size');
    const removeFileButton = container.querySelector('.remove-file-button');
    const selectFileButton = container.querySelector('.upload-button');

    // Abrir el diálogo de selección de archivos al hacer clic en el botón
    selectFileButton.addEventListener('click', (e) => {
      e.preventDefault();
      fileInput.click();
    });

    // Mostrar la información del archivo seleccionado
    fileInput.addEventListener('change', (e) => {
      if (fileInput.files.length > 0) {
        const file = fileInput.files[0];
        if (file.name.endsWith('.xlsx') || file.name.endsWith('.xls') || file.name.endsWith('.csv') || file.name.endsWith('.xlsb') || file.name.endsWith('.xlsm') || file.name.endsWith('.xltx') || file.name.endsWith('.xlt')) {
          // Mostrar el cuadro de información del archivo
          fileInfoBox.classList.remove('hidden');

          // Mostrar el nombre y el tamaño del archivo
          fileNameElement.textContent = file.name;
          fileSizeElement.textContent = `${(file.size / 1024).toFixed(2)} KB`;
        } else {
          alert("Solo se permiten archivos .xlsx");
          fileInput.value = ""; // Limpia el input
        }
      } else {
        fileInfoBox.classList.add('hidden'); // Ocultar el cuadro de información
      }
    });

    // Manejar el botón de tacho de basura para quitar el archivo
    removeFileButton.addEventListener('click', (e) => {
      e.preventDefault();
      fileInput.value = ""; // Limpia el input
      fileInfoBox.classList.add('hidden'); // Oculta el cuadro de información
    });

    // Manejar el arrastre de archivos
    fileLabel.addEventListener('dragover', (e) => {
      e.preventDefault();
      fileLabel.style.borderColor = '#007bff';
    });

    fileLabel.addEventListener('dragleave', (e) => {
      e.preventDefault();
      fileLabel.style.borderColor = '#cccccc';
    });

    fileLabel.addEventListener('drop', (e) => {
      e.preventDefault();
      fileLabel.style.borderColor = '#cccccc';
      if (e.dataTransfer.files.length > 0) {
        const file = e.dataTransfer.files[0];
        if (file.name.endsWith('.xlsx')) {
          fileInput.files = e.dataTransfer.files; // Asigna el archivo arrastrado al input

          // Mostrar la información del archivo
          fileInput.dispatchEvent(new Event('change'));
        } else {
          alert("Solo se permiten archivos .xlsx");
        }
      }
    });
  }

  // Funcion para subir archivos multiples

  function setupMultiFileUpload(containerId,inputId) {
    const container = document.getElementById(containerId);
    const fileInput = $(`#${inputId}`)[0];
    const fileLabel = container.querySelector('.file-label');
    const fileList = container.querySelector('.file-lista');
    const uploadButton = container.querySelector('.upload-button');

    // Abrir el diálogo de selección de archivos al hacer clic en el botón
    uploadButton.addEventListener('click', (e) => {
      e.preventDefault();
      fileInput.click();
    });

    // Mostrar la lista de archivos seleccionados
    fileInput.addEventListener('change', (e) => {
      if (fileInput.files.length > 0) {
        fileList.classList.remove('hidden');

        // Recorrer los archivos seleccionados
        Array.from(fileInput.files).forEach((file, index) => {
          // Crear un elemento de lista para cada archivo
          const fileItem = document.createElement('div');
          fileItem.classList.add('file-list-item');

          // Definir el ícono según el tipo de archivo
          let icon = '';
          if (file.name.endsWith('.jpeg') || file.name.endsWith('.jpg')) {
            icon = `
                        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="50" height="50" viewBox="0 0 48 48">
                          <path fill="#90caf9" d="M40,42H8c-1.105,0-2-0.895-2-2V8c0-1.105,0.895-2,2-2h32c1.105,0,2,0.895,2,2v32C42,41.105,41.105,42,40,42z"></path>
                          <path fill="#1565c0" d="M40,42H8c-1.105,0-2-0.895-2-2V8c0-1.105,0.895-2,2-2h32c1.105,0,2,0.895,2,2v32C42,41.105,41.105,42,40,42z"></path>
                          <path fill="#fff" d="M24,14c-5.523,0-10,4.477-10,10s4.477,10,10,10s10-4.477,10-10S29.523,14,24,14z M24,30c-3.314,0-6-2.686-6-6	s2.686-6,6-6s6,2.686,6,6S27.314,30,24,30z"></path>
                        </svg>
                    `;
          } else if (file.name.endsWith('.png')) {
            icon = `
                        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="50" height="50" viewBox="0 0 48 48">
                          <path fill="#90caf9" d="M40,42H8c-1.105,0-2-0.895-2-2V8c0-1.105,0.895-2,2-2h32c1.105,0,2,0.895,2,2v32C42,41.105,41.105,42,40,42z"></path>
                          <path fill="#1565c0" d="M40,42H8c-1.105,0-2-0.895-2-2V8c0-1.105,0.895-2,2-2h32c1.105,0,2,0.895,2,2v32C42,41.105,41.105,42,40,42z"></path>
                          <path fill="#fff" d="M24,14c-5.523,0-10,4.477-10,10s4.477,10,10,10s10-4.477,10-10S29.523,14,24,14z M24,30c-3.314,0-6-2.686-6-6	s2.686-6,6-6s6,2.686,6,6S27.314,30,24,30z"></path>
                        </svg>
                    `;
          } else if (file.name.endsWith('.xlsx')) {
            icon = `
                            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="50" height="50" viewBox="0 0 48 48">
                              <rect width="16" height="9" x="28" y="15" fill="#21a366"></rect>
                              <path fill="#185c37" d="M44,24H12v16c0,1.105,0.895,2,2,2h28c1.105,0,2-0.895,2-2V24z"></path>
                              <rect width="16" height="9" x="28" y="24" fill="#107c42"></rect>
                              <rect width="16" height="9" x="12" y="15" fill="#3fa071"></rect>
                              <path fill="#33c481" d="M42,6H28v9h16V8C44,6.895,43.105,6,42,6z"></path>
                              <path fill="#21a366" d="M14,6h14v9H12V8C12,6.895,12.895,6,14,6z"></path>
                              <path d="M22.319,13H12v24h10.319C24.352,37,26,35.352,26,33.319V16.681C26,14.648,24.352,13,22.319,13z" opacity=".05"></path>
                              <path d="M22.213,36H12V13.333h10.213c1.724,0,3.121,1.397,3.121,3.121v16.425	C25.333,34.603,23.936,36,22.213,36z" opacity=".07"></path>
                              <path d="M22.106,35H12V13.667h10.106c1.414,0,2.56,1.146,2.56,2.56V32.44C24.667,33.854,23.52,35,22.106,35z" opacity=".09"></path>
                              <linearGradient id="flEJnwg7q~uKUdkX0KCyBa_UECmBSgBOvPT_gr1" x1="4.725" x2="23.055" y1="14.725" y2="33.055" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#18884f"></stop>
                                <stop offset="1" stop-color="#0b6731"></stop>
                              </linearGradient>
                              <path fill="url(#flEJnwg7q~uKUdkX0KCyBa_UECmBSgBOvPT_gr1)" d="M22,34H6c-1.105,0-2-0.895-2-2V16c0-1.105,0.895-2,2-2h16c1.105,0,2,0.895,2,2v16	C24,33.105,23.105,34,22,34z"></path>
                              <path fill="#fff" d="M9.807,19h2.386l1.936,3.754L16.175,19h2.229l-3.071,5l3.141,5h-2.351l-2.11-3.93L11.912,29H9.526	l3.193-5.018L9.807,19z"></path>
                            </svg>
                        `;
          } else if (file.name.endsWith('.mp4')) {
            icon = `
                        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="50" height="50" viewBox="0 0 48 48">
                          <path fill="#ff7043" d="M40,42H8c-1.105,0-2-0.895-2-2V8c0-1.105,0.895-2,2-2h32c1.105,0,2,0.895,2,2v32C42,41.105,41.105,42,40,42z"></path>
                          <path fill="#bf360c" d="M40,42H8c-1.105,0-2-0.895-2-2V8c0-1.105,0.895-2,2-2h32c1.105,0,2,0.895,2,2v32C42,41.105,41.105,42,40,42z"></path>
                          <path fill="#fff" d="M19,32V16l12,8L19,32z"></path>
                        </svg>
                    `;
          } else {
            alert(`El archivo "${file.name}" no es un archivo válido`);
            return; // Salir si el archivo no es válido
          }

          // Mostrar el nombre y el tamaño del archivo
          fileItem.innerHTML = `
                    ${icon}
                    <span>${file.name} (${(file.size / 1024).toFixed(2)} KB)</span>
                    <button class="remove-file-button" data-index="${index}">
                        <i class="fas fa-trash"></i>
                    </button>
                `;

          // Agregar el elemento a la lista
          fileList.appendChild(fileItem);
        });
      } else {
        fileList.classList.add('hidden'); // Ocultar la lista si no hay archivos seleccionados
      }
    });

    // Manejar la eliminación de archivos individuales
    fileList.addEventListener('click', (e) => {
      if (e.target.classList.contains('remove-file-button') || e.target.closest('.remove-file-button')) {
        const index = e.target.dataset.index || e.target.closest('.remove-file-button').dataset.index;

        // Convertir FileList a un array para poder eliminar el archivo
        const files = Array.from(fileInput.files);
        files.splice(index, 1); // Eliminar el archivo del array

        // Crear un nuevo FileList (no es mutable, así que usamos DataTransfer)
        const dataTransfer = new DataTransfer();
        files.forEach(file => dataTransfer.items.add(file));
        fileInput.files = dataTransfer.files;

        // Volver a mostrar la lista de archivos actualizada
        fileInput.dispatchEvent(new Event('change'));
      }
    });

    // Manejar el arrastre de archivos
    fileLabel.addEventListener('dragover', (e) => {
      e.preventDefault();
      fileLabel.style.borderColor = '#007bff';
    });

    fileLabel.addEventListener('dragleave', (e) => {
      e.preventDefault();
      fileLabel.style.borderColor = '#cccccc';
    });

    fileLabel.addEventListener('drop', (e) => {
      e.preventDefault();
      fileLabel.style.borderColor = '#cccccc';
      if (e.dataTransfer.files.length > 0) {
        // Asignar los archivos arrastrados al input
        fileInput.files = e.dataTransfer.files;

        // Mostrar la lista de archivos
        fileInput.dispatchEvent(new Event('change'));
      }
    });
  }

  $(document).ready(function() {
    // Evita que el menú se cierre al hacer clic fuera de él
    $('.dropdown-menu').on('click', function(event) {
      event.stopPropagation(); // Evita que el evento se propague
    });

    // Cierra el menú al hacer clic en "Cancelar" o "Aplicar"
    $('#cancelar-btn, #aplicar-btn').on('click', function() {
      $('#filtros-btn').dropdown('hide'); // Cierra el menú
    });

    // Cierra el menú al hacer clic en el botón "Filtros" si ya está abierto
    $('#filtros-btn').on('click', function(event) {
      if ($(this).attr('aria-expanded') === 'true') {
        $(this).dropdown('hide'); // Cierra el menú si ya está abierto
      }
    });
  });
</script>


