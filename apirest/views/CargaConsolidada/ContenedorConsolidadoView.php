<?php
$rolesChina = ["ContenedorAlmacen", "CatalogoChina"];
?>
<script>
var currentPrivilege = "<?php echo $this->user->No_Grupo; ?>";
localStorage.setItem("currentPrivilege", currentPrivilege);
</script>
<div class="content-wrapper">
    <div class="sessions-container d-flex pl-3 flex-row gap-2">
    </div>
    <!--set js variable = php variable-->

    <!-- Content Header (Page header) -->
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
                <?php if ($this->user->No_Grupo == "Coordinación"  || $this->user->No_Grupo == "Documentacion" || $this->user->No_Grupo == "Cotizador") {  ?>

                <!-- Buscador de la tabla -->
                <div class="col-9 col-xl-2 filter-contenedor">
                    <div class="dataTables_filter" style="display: flex;justify-content: flex-end;">
                        <input type="search"
                            class="form-control bg-white hover:bg-white-200 text-black-200 py-2 border border-transparent hover:border-orange-600 rounded search-table"
                            placeholder=" <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
Buscar por
                          <?php } else { ?>
Search for
                          <?php } ?> " aria-controls="table-contenedor"
                            style="width:100%;min-width:200px; padding-left: 40px; background: url('https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/svgs/solid/search.svg') no-repeat 15px center;background-size: 16px; font-size: 14px;">
                    </div>
                </div>
                <div class="col-1 col-xl-1 dropdown filter-contenedor px-0">
                    <button type="button" id="btn-exportar-carga"
                        class="bg-white hover:bg-white-200 text-black-200 py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block btn-reporte"
                        type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                            class="fa fa-upload"></i> <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                        Exportar
                        <?php } else { ?>
                        Export
                        <?php } ?></button>
                    <div class="dropdown-menu dropdown-menu-right px-3 py-3" aria-labelledby="btn-exportar-carga">
                        <button class="dropdown-item btn-block export-pdf-main-content"><i
                                class="fa fa-file-pdf color_icon_pdf"></i>
                            <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                            Exportar
                            <?php } else { ?>
                            Export
                            <?php } ?> PDF</button>
                        <button class="dropdown-item btn-block export-excel-main-content"><i
                                class="fa fa-file-excel color_icon_excel"></i>
                            <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                            Exportar
                            <?php } else { ?>
                            Export
                            <?php } ?> Excel</button>
                    </div>
                </div>

                <div class="col-1 col-xl-1 dropdown filter-contenedor px-0">

                    <button
                        class="bg-white py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block btn-reporte"
                        type="button" id="btn-filtrar-carga" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false">
                        <i class="fa fa-filter"></i> <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                        Filtros
                        <?php } else { ?>
                        Filters
                        <?php } ?>
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
                                    <select id="txt-ID_Estado" name="ID_Estado" class="form-control input-estado">
                                        <option value="0" selected>Todos</option>
                                        <option value="PENDIENTE">PENDIENTE</option>
                                        <option value="CONTACTADO">CONTACTADO</option>
                                        <option value="INTERESADO">INTERESADO</option>
                                        <option value="COMPLETADO">COMPLETADO</option>
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
                                id="aplicar-btn">Aplicar</button>
                        </div>
                    </div>

                </div>

                <div class="col-2 col-xl-1">
                    <?php if ($this->user->No_Grupo == "Coordinación") { ?>
                    <button type="button" id="btn-crear" class="btn btn-primary btn-block btn-reporte"
                        data-type="html"><i class="fa fa-plus"></i> Crear</button>
                    <?php } ?>
                </div>
                <?php } else { ?>

                <div class="col-12 col-xl-2"></div>

                <!-- Buscador de la tabla -->
                <div class="col-12 col-xl-3 flex justify-content-center gap-2 align-items-center filter-contenedor">
                    <div class="col-9 col-xl-8 filter-contenedor px-0">
                        <div class="dataTables_filter" style="display: flex;justify-content: flex-end;">
                            <input type="search"
                                class="form-control bg-white hover:bg-white-200 text-black-200 py-2 border border-transparent hover:border-orange-600 rounded search-table-carga"
                                id="search-input-filter" placeholder=" <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                Buscar por
                          <?php } else { ?>
                Search for
                          <?php } ?> " aria-controls="table-contenedor"
                                style="width:100%;min-width:200px; padding-left: 40px; background: url('https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/svgs/solid/search.svg') no-repeat 15px center;background-size: 16px; font-size: 14px;">
                        </div>
                    </div>

                    <div class="col-1 col-xl-4 col-lg-1 col-md-1 dropdown filter-contenedor px-0">
                        <button type="button" id="btn-exportar-carga"
                            class="bg-white hover:bg-white-200 text-black-200 py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block btn-reporte"
                            type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                class="fa fa-upload"></i> <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                            Exportar
                            <?php } else { ?>
                            Export
                            <?php } ?></button>
                        <div class="dropdown-menu dropdown-menu-right px-3 py-3" aria-labelledby="btn-exportar-carga">
                            <button class="dropdown-item btn-block export-pdf-main-content"><i
                                    class="fa fa-file-pdf color_icon_pdf"></i>
                                <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                                Exportar
                                <?php } else { ?>
                                Export
                                <?php } ?> PDF</button>
                            <button class="dropdown-item btn-block export-excel-main-content"><i
                                    class="fa fa-file-excel color_icon_excel"></i>
                                <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                                Exportar
                                <?php } else { ?>
                                Export
                                <?php } ?> Excel</button>
                        </div>
                    </div>

                    <div class="col-1 col-xl-4 col-lg-1 col-md-1 dropdown filter-contenedor px-0">

                        <button
                            class="bg-white py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block btn-reporte"
                            type="button" id="btn-filtrar-carga" data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                            <i class="fa fa-filter"></i> <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                            Filtros
                            <?php } else { ?>
                            Filters
                            <?php } ?>
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
                                        <select id="txt-ID_Estado" name="ID_Estado" class="form-control input-estado">
                                            <option value="0" selected>Todos</option>
                                            <option value="PENDIENTE">WAITING</option>
                                            <option value="COMPLETADO">FINISH</option>
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
                                    id="aplicar-btn">Aplicar</button>
                            </div>

                        </div>
                        <!-- Contenedor Principal de  <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                           Filtros
                          <?php } else { ?>
                           Filters
                          <?php } ?>-->
                    </div>
                </div>

                <?php } ?>
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
                || $this->user->No_Grupo == "Cotizador"
              ) {
              ?>
                            <th>Carga</th>
                            <th>Mes </th>
                            <th>Pais</th>

                            <th>F. Cierre</th>
                            <?php if ($this->user->No_Grupo != "Documentacion") { ?>
                            <th>F. Arribo</th>
                            <th>F. Entrega</th>
                            <?php } ?>
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
                            <th style="min-width: 8em;">Status</th>


                            <th>Check</th>

                            <?php } ?>
                        </tr>
                    </thead>
                </table>
                <table id="table-contenedor-completados" style="display:none;" class="table table-hover">
                    <thead class="thead-default">
                        <tr>
                            <!--th mes,pais,empresa,T.ctn,canal,desaduanaje,ajuste,multa,fob,flete,c.destino,observaciones,ver-->
                            <th>Mes</th>
                            <th style="min-width: 12em;">Empresa</th>
                            <th style="min-width: 12em;">Carga</th>
                            <th style="min-width: 4em;">T. Ctn</th>
                            <th>Canal</th>
                            <th>F. Cierre</th>
                            <th>F. Arribo</th>
                            <th>F. Declaración</th>
                            <th>F. Levante</th>
                            <th>Días de levante</th>
                            <th style="min-width: 8em;">N. Dua</th>
                            <th>Ajuste</th>
                            <th>Multa</th>
                            <th>FOB</th>
                            <th>Flete</th>
                            <th>C. Destino</th>
                            <th>Acciones</th>

                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </section>
    <section class="bg-gradient-to-br from-gray-50 to-gray-100 p-8" id="documentacion-container">
        <div class="row mb-2">
            <div class="col-10">
                &nbsp;
            </div>
            <div class="col-2" id="btn-back-documentacion-profile">

                <button type="button" class="btn btn-outline-primary btn-block btn-reporte" data-type="html"><i
                        class="fa fa-arrow-left"></i> </button>
            </div>
        </div>
        <div class="max-w-4xl mx-auto space-y-6">
            <!-- Providers Section -->
            <div class="providers flex gap-4 mb-8">
                <button
                    class="provider-btn px-6 py-3 rounded-lg font-medium transition-all duration-300 shadow-sm hover:shadow-md bg-orange-500 text-white">JS
                    - 1</button>
                <button
                    class="provider-btn px-6 py-3 rounded-lg font-medium transition-all duration-300 shadow-sm hover:shadow-md bg-gray-200 text-gray-700">JS
                    - 2</button>
                <button
                    class="provider-btn px-6 py-3 rounded-lg font-medium transition-all duration-300 shadow-sm hover:shadow-md bg-gray-200 text-gray-700">JS
                    - 3</button>
            </div>

            <!-- Peru Documentation -->
            <div class="bg-white rounded-xl shadow-lg p-6 transition-all duration-300 hover:shadow-xl">
                <h2
                    class="text-lg font-semibold mb-6 bg-gradient-to-r from-gray-200 to-gray-100 p-3 rounded-lg flex items-center">
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
                                <input type="text" id="txt-Vol_Doc"
                                    class="border rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none transition-all duration-300">
                            </div>
                            <div class="flex items-center gap-4">
                                <label class="font-medium flex items-center">
                                    <i class="bi bi-currency-dollar mr-2"></i>
                                    Valor Doc:
                                </label>
                                <input type="text" id="txt-Valor_Doc"
                                    class="border rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none transition-all duration-300">
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mt-4" id="documentacion-peru-documents">
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
                <h2
                    class="text-lg font-semibold mb-6 bg-gradient-to-r from-gray-200 to-gray-100 p-3 rounded-lg flex items-center">
                    <i class="bi bi-globe-asia-australia text-xl mr-2"></i>
                    DOCUMENTACIÓN CHINA
                </h2>
                <div class="grid grid-cols-2 gap-4 mt-4" id="documentacion-china">
                </div>
            </div>

            <!-- Inspection -->
            <div class="bg-white rounded-xl shadow-lg p-6 transition-all duration-300 hover:shadow-xl">
                <h2
                    class="text-lg font-semibold mb-6 bg-gradient-to-r from-gray-200 to-gray-100 p-3 rounded-lg flex items-center">
                    <i class="bi bi-clipboard-check text-xl mr-2"></i>
                    INSPECCIÓN
                </h2>
                <div class="grid grid-cols-2 gap-4" id="documentacion-inspeccion">

                </div>
            </div>
        </div>
    </section>
    <section class="m-5" id="documentacion-documentacion-container">
        <div class="row my-2 ">
            <button type="button" id="btn-back-documentacion-documentacion"
                class="py-1 px-2 bg-white hover:bg-white-200 text-black-200 py-2 px-2 border border-transparent hover:border-orange-600 rounded "
                data-type="html"><i class="fa fa-arrow-left"></i> Regresar</button>
        </div>
        <div class="row bg-white rounded-xl p-8 mb-5">
            <div class="col-10 mb-6 ">
                <span class="text-gray-800 border-b text-lg">
                    Documentación
                    <i class="bi bi-folder me-2"></i>

                </span>
            </div>
            <div class="col-1">
                <button type="button" id="btn-crear-documentacion-documentacion"
                    class="btn btn-primary btn-block btn-reporte" data-type="html"><i class="fa fa-plus"></i></button>
            </div>



            <!--button with plus icon to add new document-->

            <!-- Document Filter -->


            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 " id="documentacion-documentacion">
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
        </div>
    </section>
    <section class="" id="documentacion-aduana-container">
        <div class="row p-6">
            <button type="button" id="btn-back-documentacion-aduana"
                class="bg-white hover:bg-white-200 text-black-200 py-2 px-2 border border-transparent hover:border-orange-600 rounded"
                data-type="html"><i class="fa fa-arrow-left"></i> Regresar</button>
        </div>
        <div class="mx-6 p-6 bg-white">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-lg d-flex flex-row gap-3 align-items-center">Aduana <svg width="15" height="14"
                            viewBox="0 0 15 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M11.9232 8.46043V5.68313C11.9232 5.19881 11.6693 4.76745 11.2386 4.53286C11.2155 4.51772 11.1924 4.51772 11.1693 4.50259L8.23087 2.62583C7.78472 2.33826 7.2078 2.33826 6.76164 2.62583L3.81549 4.51016C3.81549 4.51016 3.76934 4.52529 3.75395 4.53286C3.32318 4.77502 3.06934 5.19881 3.06934 5.68313V8.46043C3.06934 8.92205 3.32318 9.36097 3.72318 9.59556L6.80011 11.4118C7.01549 11.5404 7.25395 11.601 7.49241 11.601C7.73087 11.601 7.96934 11.5404 8.18472 11.4118L11.2616 9.59556C11.6616 9.36097 11.9155 8.92205 11.9155 8.46043H11.9232ZM7.39241 3.57935C7.45395 3.54151 7.53857 3.54151 7.60011 3.57935L9.48472 4.78259L7.59241 5.89502C7.53087 5.93286 7.45395 5.93286 7.39241 5.89502L5.50011 4.78259L7.38472 3.57935H7.39241ZM4.32318 8.62691C4.26934 8.59664 4.23087 8.52853 4.23087 8.46799V5.6907C4.23087 5.59232 4.29241 5.54691 4.33087 5.52421C4.35395 5.50908 4.38472 5.50151 4.42318 5.50151C4.45395 5.50151 4.48472 5.50151 4.52318 5.53178L6.8078 6.87881C6.8078 6.87881 6.88472 6.90908 6.92318 6.92421V10.1631L4.32318 8.62691ZM10.7693 8.46799C10.7693 8.5361 10.7309 8.59664 10.677 8.62691L8.07703 10.1631V6.92421C8.07703 6.92421 8.15395 6.89394 8.19241 6.87881L10.477 5.53178C10.5616 5.48637 10.6386 5.50908 10.6693 5.53178C10.7078 5.55448 10.7693 5.59989 10.7693 5.69826V8.47556V8.46799Z"
                                fill="#272A30" />
                            <path
                                d="M12.1154 0H10.5769C10.2615 0 10 0.257297 10 0.567568C10 0.877838 10.2615 1.13514 10.5769 1.13514H12.1154C13.0692 1.13514 13.8462 1.89946 13.8462 2.83784V4.35135C13.8462 4.66162 14.1077 4.91892 14.4231 4.91892C14.7385 4.91892 15 4.66162 15 4.35135V2.83784C15 1.27135 13.7077 0 12.1154 0Z"
                                fill="#272A30" />
                            <path
                                d="M0.576923 4.91892C0.892308 4.91892 1.15385 4.66162 1.15385 4.35135V2.83784C1.15385 1.89946 1.93077 1.13514 2.88462 1.13514H4.42308C4.73846 1.13514 5 0.877838 5 0.567568C5 0.257297 4.73846 0 4.42308 0H2.88462C1.29231 0 0 1.27135 0 2.83784V4.35135C0 4.66162 0.261538 4.91892 0.576923 4.91892Z"
                                fill="#272A30" />
                            <path
                                d="M4.42308 12.8658H2.88462C1.93077 12.8658 1.15385 12.1015 1.15385 11.1631V9.6496C1.15385 9.33933 0.892308 9.08203 0.576923 9.08203C0.261538 9.08203 0 9.33933 0 9.6496V11.1631C0 12.7296 1.29231 14.001 2.88462 14.001H4.42308C4.73846 14.001 5 13.7437 5 13.4334C5 13.1231 4.73846 12.8658 4.42308 12.8658Z"
                                fill="#272A30" />
                            <path
                                d="M14.4231 9.08203C14.1077 9.08203 13.8462 9.33933 13.8462 9.6496V11.1631C13.8462 12.1015 13.0692 12.8658 12.1154 12.8658H10.5769C10.2615 12.8658 10 13.1231 10 13.4334C10 13.7437 10.2615 14.001 10.5769 14.001H12.1154C13.7077 14.001 15 12.7296 15 11.1631V9.6496C15 9.33933 14.7385 9.08203 14.4231 9.08203Z"
                                fill="#272A30" />
                        </svg>
                    </h1>
                </div>

            </div>

            <!-- Tabs -->


            <!-- Form -->
            <div class=" rounded-xl shadow-sm p-8">
                <form id="customsForm" class="space-y-6">
                    <!-- General Information Tab -->
                    <div class="tab-content active" id="general">
                        <div class="grid md:grid-cols-3 gap-x-12 gap-y-6">
                            <div class="form-group">
                                <label class="block text-gray-700 mb-2">Naviera</label>
                                <div class="relative">
                                    <select id="select-naviera" name="naviera"
                                        class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent input-aduana">

                                    </select>
                                    <div class="absolute right-4 top-1/2 -translate-y-1/2 w-4 h-4 rounded-full">
                                        <i class="fa fa-plus text-gray-500" id="navieraAdd"></i>

                                    </div>

                                </div>
                            </div>
                            <div class="form-group">
                                <label class="block text-gray-700 mb-2">Multa</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                                    <input name="multa" type="number" step="0.01"
                                        class="w-full pl-8 pr-4 py-2 border input-aduana border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>
                            <!--Div ocupes 3 rows-->
                            <div class="form-group row-span-3">
                                <label class="block text-gray-700 mb-2">OBSERVACIONES</label>
                                <textarea name="observaciones"
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 input-aduana focus:ring-blue-500 focus:border-transparent"
                                    rows="3"></textarea>
                            </div>

                            <div class="form-group">
                                <label class="block text-gray-700 mb-2">Toneladas contenedor</label>
                                <select name="tipo_contenedor"
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 input-aduana focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Seleccione tipo</option>
                                    <option value="LCL">LCL</option>
                                    <option value="20 GP">20 GP</option>
                                    <option value="40 NOR">40 NOR</option>
                                    <option value="40 GP">40 GP</option>
                                    <option value="40 HQ">40 HQ</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="block text-gray-700 mb-2">Fecha levante</label>
                                <input name="fecha_levante" type="date"
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 input-aduana focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <div class="form-group">
                                <label class="block text-gray-700 mb-2">Fecha zarpe</label>
                                <input name="fecha_zarpe" type="date"
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 input-aduana focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <div class="form-group">
                                <label class="block text-gray-700 mb-2">Número Dua</label>
                                <input name="numero_dua" type="text"
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 input-aduana focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <div class="form-group">
                                <label class="block text-gray-700 mb-2">Fecha arribo</label>
                                <input name="fecha_arribo" type="date"
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 input-aduana focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div class="form-group">
                                <label class="block text-gray-700 mb-2">Valor FOB</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                                    <input name="valor_fob" type="number" step="0.01"
                                        class="w-full pl-8 pr-4 py-2 border border-gray-200 rounded-lg input-aduana focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>
                            <div class="form-group row-span-2">
                                <div class="col-12 col-sm-12" id="multiple-file-upload-aduana">
                                    <div class="form-group"> DOC. TRIBUTOS Y AJUSTES
                                        <div
                                            class="file-upload-box <?php echo ($this->user->No_Grupo == "Cotizador") ? 'd-none' : ''; ?>">
                                            <input type="file" id="file-input-aduana" class="file-input" multiple
                                                accept=".pdf, .docx, .xlsx, .xls, .doc, .xlsm" />
                                            <label for="file-inpute" class="file-label d-flex">
                                                <i class="fas fa-upload"></i>
                                                <div class="file-group-text">
                                                    <span class="file-text">
                                                        <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                                                        Selecciona o arrastra tu archivo aquí
                                                        <?php } else { ?>
                                                        Select or drag your file here
                                                        <?php } ?></span>
                                                    <span class="file-format">
                                                        <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                                                        Formatos
                                                        <?php } else { ?>
                                                        Formats
                                                        <?php } ?>: .xlsx</span>
                                                </div>
                                                <button class="upload-button upload-button-aduana" type="button">
                                                    <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                                                    Subir archivo
                                                    <?php } else { ?>
                                                    Upload Files
                                                    <?php } ?></button>
                                            </label>
                                        </div>
                                        <!-- Cuadro de información del archivo subido (oculto inicialmente) -->
                                        <div class="file-lista hidden" id="file-lista-aduana">
                                        </div>
                                        <span class="invalid-feedback" id="error-volumen">La cotización es
                                            requerida</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="block text-gray-700 mb-2">Fecha declaración</label>
                                <input name="fecha_declaracion" type="date"
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg input-aduana focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div class="form-group">
                                <label class="block text-gray-700 mb-2">Valor flete</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                                    <input name="valor_flete" type="number" step="0.01"
                                        class="w-full pl-8 pr-4 py-2 border border-gray-200 input-aduana rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="block text-gray-700 mb-2">Canal de Control</label>
                                <div class="relative">
                                    <select name="canal_control"
                                        class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 input-aduana focus:ring-blue-500 focus:border-transparent"
                                        id="controlChannel">
                                        <option value="">Seleccione canal</option>
                                        <option value="Verde">Verde</option>
                                        <option value="Naranja">Naranja</option>
                                        <option value="Rojo">Rojo</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="block text-gray-700 mb-2">Costo destino</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                                    <input name="costo_destino" type="number" step="0.01"
                                        class="w-full pl-8 pr-4 py-2 border border-gray-200 input-aduana rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>
                            <div class="form-group row-span-2">
                                <div class="col-12 col-sm-12" id="multiple-file-upload-impuestos">
                                    <div class="form-group"> RESUMEN DE IMPUESTOS PAGADOS
                                        <div
                                            class="file-upload-box <?php echo ($this->user->No_Grupo == "Cotizador") ? 'd-none' : ''; ?>">
                                            <input type="file" id="file-input-impuestos" class="file-input" multiple
                                                accept=".pdf, .docx, .xlsx, .xls, .doc, .xlsm" />
                                            <label for="file-inpute" class="file-label d-flex">
                                                <i class="fas fa-upload"></i>
                                                <div class="file-group-text">
                                                    <span class="file-text">
                                                        <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                                                        Selecciona o arrastra tu archivo aquí
                                                        <?php } else { ?>
                                                        Select or drag your file here
                                                        <?php } ?></span>
                                                    <span class="file-format">
                                                        <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                                                        Formatos
                                                        <?php } else { ?>
                                                        Formats
                                                        <?php } ?>: .xlsx</span>
                                                </div>
                                                <button class="upload-button upload-button-impuestos" type="button">
                                                    <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                                                    Subir archivo
                                                    <?php } else { ?>
                                                    Upload Files
                                                    <?php } ?></button>
                                            </label>
                                        </div>
                                        <!-- Cuadro de información del archivo subido (oculto inicialmente) -->
                                        <div class="file-lista hidden" id="file-lista-impuestos">
                                        </div>
                                        <span class="invalid-feedback" id="error-volumen">La cotización es
                                            requerida</span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="block text-gray-700 mb-2">Ajuste de valor</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                                    <input name="ajuste_valor" type="number" step="0.01"
                                        class="w-full pl-8 pr-4 py-2 border border-gray-200  input-aduana rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>


                        </div>


                        <!-- Buttons -->
                        <div class="flex pt-6 border-t mt-6 row">
                            <div class="col-9"></div>
                            <div class="col-3 d-flex justify-content-end gap-2">
                                <button type="submit" class="btn btn-primary btn-block btn-reporte btn-guardar-aduana">
                                    <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                                    Guardar
                                    <?php } else { ?>
                                    Save
                                    <?php } ?>
                                </button>

                            </div>
                        </div>
                </form>
            </div>
        </div>

    </section>
    <section class="content" id="cotizacion-container">
        <div class="container-fluid ">
            <!-- Header de la tabla -->
            <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
            <div class="row mb-2 d-flex justify-content-between">
                <div class="col-3 col-xl-1 py-sm-3 py-xl-0 py-md-0">
                    <button type="button"
                        class="bg-white hover:bg-white-200 text-black-200 py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block btn-reporte btn-back-cotizacion"
                        data-type="html"><i class="fa fa-arrow-left"></i> Regresar</button>
                </div>
                <div class="col-12 col-md-8 d-flex gap-2 justify-content-end">
                    <!-- Buscador -->
                    <div class="dataTables_filter">
                        <input type="search"
                            class="form-control bg-white hover:bg-white-200 text-black-200 py-2 border border-transparent hover:border-orange-600 rounded search-table"
                            placeholder=" <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                Buscar por
                          <?php } else { ?>
                  Search for
                          <?php } ?> " aria-controls="table-contenedor"
                            style="width:100%;min-width:200px; padding-left: 40px; background: url('https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/svgs/solid/search.svg') no-repeat 15px center;background-size: 16px; font-size: 14px;">
                    </div>


                    <!-- Contenedor Principal de  <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                            Exportar
                          <?php } else { ?>
                            Export
                          <?php } ?>-->
                    <div class="col-6 col-sm-2 dropdown">
                        <button type="button" id="btn-exportar-carga"
                            class="bg-white hover:bg-white-200 text-black-200 py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block btn-reporte"
                            type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                class="fa fa-upload"></i> <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                            Exportar
                            <?php } else { ?>
                            Export
                            <?php } ?></button>
                        <div class="dropdown-menu dropdown-menu-right px-3 py-3" aria-labelledby="btn-exportar-carga">
                            <button class="dropdown-item btn-block export-pdf-main-content"><i
                                    class="fa fa-file-pdf color_icon_pdf"></i>
                                <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                                Exportar
                                <?php } else { ?>
                                Export
                                <?php } ?> PDF</button>
                            <button class="dropdown-item btn-block export-excel-main-content"><i
                                    class="fa fa-file-excel color_icon_excel"></i>
                                <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                                Exportar
                                <?php } else { ?>
                                Export
                                <?php } ?> Excel</button>
                        </div>
                    </div>
                    <!-- Contenedor Principal de  <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                           Filtros
                          <?php } else { ?>
              Filters
                          <?php } ?>-->
                    <div class=" col-6 col-sm-2 dropdown">
                        <!-- Botón de  <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                           Filtros
                          <?php } else { ?>
                          Filters
                          <?php } ?> -->
                        <button
                            class="bg-white py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block btn-reporte"
                            type="button" id="btn-filtrar-carga" data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                            <i class="fa fa-filter"></i> <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                            Filtros
                            <?php } else { ?>
                            Filters
                            <?php } ?>
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
                                <?php if ($this->user->No_Grupo == "Cotizador") {  ?>
                                <div class="d-flex align-items-center p-2" style="width:300px;">
                                    <div class="d-flex" style="width:60%">Estado</div>
                                    <div style="width: 200px;">
                                        <select id="txt-ID_Estado_Cotizacion" name="ID_Estado"
                                            class="form-control input-estado">
                                            <option value="0" selected>Todos</option>
                                            <option value="PENDIENTE">PENDIENTE</option>
                                            <option value="CONTACTADO">CONTACTADO</option>
                                            <option value="INTERESADO">INTERESADO</option>
                                            <option value="CONFIRMADO">CONFIRMADO</option>
                                        </select>
                                    </div>
                                </div>
                                <?php } ?>
                                <?php if ($this->user->No_Grupo == "Coordinación") {  ?>
                                <div class="d-flex align-items-center p-2" style="width:300px;">
                                    <div class="d-flex" style="width:60%">Estado</div>
                                    <div style="width: 200px;">
                                        <select id="txt-ID_States_Cliente" name="ID_States_Cliente"
                                            class="form-control input-estado">
                                            <option value="0" selected>Todos</option>
                                            <option value="ROTULADO">ROTULADO</option>
                                            <option value="COBRANDO">COBRANDO</option>
                                            <option value="DATOS PROVEEDOR">DATOS PROVEEDOR</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center p-2" style="width:300px;">
                                    <div class="d-flex" style="width:60%">Status</div>
                                    <div style="width: 200px;">
                                        <select id="txt-ID_Estatus_Cotizacion" name="ID_Status"
                                            class="form-control input-estado">
                                            <option value="0" selected>Todos</option>
                                            <option value="NC">NC</option>
                                            <option value="C">C</option>
                                            <option value="R">R</option>
                                            <option value="NS">NS</option>
                                            <option value="INSPECTION">INSPECTION</option>
                                            <option value="LOADED">LOADED</option>
                                            <option value="NO LOADED">NO LOADED</option>
                                        </select>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                            <div class="dropdown-divider"></div>
                            <!-- Botones -->
                            <div class="d-flex justify-content-around">
                                <button
                                    class="bg-white py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block"
                                    style="margin-top: .5rem;" id="cancelar-btn">Cancelar</button>
                                <button
                                    class="bg-orange py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block"
                                    id="aplicar-btn-cotizacion">Aplicar</button>
                            </div>
                        </div>
                        <!-- Contenedor Principal de  <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                            Exportar
                          <?php } else { ?>
                            Export
                          <?php } ?>-->
                    </div>
                    <div class="col-12 col-md-3">
                        <button type="button" id="btn-crear-cotizacion"
                            class="bg-orange text-black-200 py-2 px-3 border border-transparent rounded btn-block btn-reporte"
                            data-type="html"><i class="fa fa-plus"></i> Crear Prospecto</button>
                    </div>
                </div>

            </div>
            <?php } else { ?>
            <div class="row mb-2 gap-3 gap-xl-0 pt-3 pt-xl-0 pt-lg-0 pt-md-0">
                <div class="col-sm-4 col-md-4 col-xl-1 col-4 py-3 py-lg-0 col-lg-2 py-xl-0 py-md-0">
                    <button type="button"
                        class="bg-white hover:bg-white-200 text-black-200 py-2 px-3 border border-transparent hover:border-orange-600 rounded btn-block btn-reporte btn-back-cotizacion"
                        data-type="html"><i class="fa fa-arrow-left"></i> Regresar</button>
                </div>
                <div class="col-xl-6 col-0"></div>
                <div
                    class="col-12 col-xl-4 flex justify-content-center gap-2 align-items-center filter-contenedor px-0">
                    <div class="col-8 col-xl-6 filter-contenedor px-0">
                        <div class="dataTables_filter" style="display: flex;justify-content: flex-end;">
                            <input type="search"
                                class="form-control bg-white hover:bg-white-200 text-black-200 py-2 border border-transparent hover:border-orange-600 rounded search-table"
                                placeholder=" <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                  Buscar por
                <?php } else { ?>
                  Search for
                <?php } ?> " aria-controls="table-contenedor"
                                style="width:100%;min-width:200px; padding-left: 40px; background: url('https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/svgs/solid/search.svg') no-repeat 15px center;background-size: 16px; font-size: 14px;">
                        </div>
                    </div>
                    <!-- Contenedor Principal de Cargar-->
                    <div class="col-1 col-xl-3 col-lg-1 col-md-1 dropdown filter-contenedor px-0">
                        <button type="button" id="btn-cargar-carga"
                            class="bg-white hover:bg-white-200 text-black-200 py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block btn-reporte"
                            type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                class="fa fa-upload"></i>
                            <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                            Cargar
                            <?php } else { ?>
                            Upload
                            <?php } ?>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right px-3 py-3" aria-labelledby="btn-cargar-carga">
                            <div class="dropdown-item btn-block" id="packing-list-container"></div>
                            <div class="dropdown-item btn-block" id="bl-file-container"></div>
                        </div>
                    </div>
                    <!-- Contenedor Principal de  <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                              Exportar
                            <?php } else { ?>
                              Export
                            <?php } ?>-->
                    <div class="col-1 col-xl-3 col-lg-1 col-md-1 dropdown filter-contenedor px-0">
                        <button type="button" id="btn-exportar-carga"
                            class="bg-white hover:bg-white-200 text-black-200 py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block btn-reporte"
                            type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                class="fa fa-download"></i>
                            <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                            Exportar
                            <?php } else { ?>
                            Export
                            <?php } ?></button>
                        <div class="dropdown-menu dropdown-menu-right px-3 py-3" aria-labelledby="btn-exportar-carga">
                            <button class="dropdown-item btn-block" id="export-excel"><i
                                    class="fa fa-file-excel color_icon_excel"></i>
                                <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                                Exportar
                                <?php } else { ?>
                                Export
                                <?php } ?> Excel</button>
                        </div>
                    </div>
                    <div class="col-xl-3 col-1 px-0 col-sm-2 dropdown">
                        <button
                            class="bg-white py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block btn-reporte"
                            type="button" id="btn-filtrar-carga" data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                            <i class="fa fa-filter"></i>
                            Filters
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
                                    <div class="d-flex" style="width:60%">Status</div>
                                    <div style="width: 200px;">
                                        <select id="txt-ID_Estatus_Cotizacion" name="ID_Status"
                                            class="form-control input-estado">
                                            <option value="0" selected>Todos</option>
                                            <option value="NC">NC</option>
                                            <option value="C">C</option>
                                            <option value="R">R</option>
                                            <option value="NS">NS</option>
                                            <option value="INSPECTION">INSPECTION</option>
                                            <option value="LOADED">LOADED</option>
                                            <option value="NO LOADED">NO LOADED</option>
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
                                    id="aplicar-btn-cotizacion">Aplicar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>


                <div class="col-xl-12 list-cmb row mb-2 gap-xs-3 gap-md-0 mx-100 py-3"
                    style="border-bottom: #DFDFDF solid 2px">
                    <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                    <div class="d-flex align-items-center col-sm-2"
                        style="border-right: #DFDFDF solid 2px; width:10%; padding:15px 10px">
                        <span>Consolidado </span>
                        <div class="col-4 col-md-4 px-0">
                            <input class="col-12 px-1" id="cotizacion_name" disabled>
                        </div>
                    </div>
                    <?php } ?>

                    <div
                        class="col-5 col-sm-12 col-md-4 col-xl-2 d-flex align-items-center justify-content-center justify-content-xl-start">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/c/cf/Flag_of_Peru.svg"
                            class="country-icons" alt="Perú">
                        <span>CBM Total:</span>
                        <div class="">
                            <strong><span type="number" id="txt-CBM_Total_Peru" class="cbm_score"
                                    disabled></span></strong>
                        </div>
                    </div>
                    <?php if ($this->user->No_Grupo == 'Cotizador') { ?>
                    <div
                        class="col-5 col-sm-4  col-md-6  col-xl-2 d-flex align-items-center justify-content-center justify-content-xl-start">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/c/cf/Flag_of_Peru.svg"
                            class="country-icons" alt="Perú">
                        <span>CBM Pendiente:</span>
                        <div class="">
                            <strong><span type="number" id="txt-CBM_Total_Pendiente" class="cbm_score"
                                    disabled></span></strong>
                        </div>
                    </div>
                    <?php } ?>
                    <div
                        class="col-5 col-sm-12  col-md-4  col-xl-2 d-flex align-items-center justify-content-center justify-content-xl-start">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/f/fa/Flag_of_the_People%27s_Republic_of_China.svg"
                            alt="China" class="country-icons">
                        <span>CBM Total:</span>
                        <div class="">
                            <strong><span type="number" id="txt-CBM_Total_China" class="cbm_score"
                                    disabled></span></strong>
                        </div>
                    </div>
                    <?php if ($this->user->No_Grupo=="Coordinación" ) { ?>

                    <div
                        class="col-5 col-sm-12  col-md-4  col-xl-2 d-flex align-items-center justify-content-center gap-1 justify-content-xl-start">
                        <!-- icon with dollar icon-->
                        <i class="fas fa-dollar-sign"></i>
                        <span>Total Logistica:</span>
                        <div class="">
                            <strong><span type="number" id="txt-CBM_Total_Logistica" class="cbm_score"
                                    disabled></span></strong>
                        </div>
                    </div>

                    <?php } ?>
                    <?php if ($this->user->No_Grupo == "Coordinación") {  ?>
                    <div
                        class="col-5 col-sm-12  col-md-4  col-xl-2 d-flex align-items-center justify-content-center gap-1 justify-content-xl-start">
                        <!-- icon with dollar icon-->
                        <i class="fas fa-dollar-sign"></i>
                        <span>Total Pagado:</span>
                        <div class="">
                            <strong><span type="number" id="txt-CBM_Total_Pagado" class="cbm_score"
                                    disabled></span></strong>
                        </div>
                    </div>
                    <?php } ?>
                    <?php if ($this->user->No_Grupo == "Coordinación" || $this->user->No_Grupo == "Cotizador") {  ?>
                    <div
                        class="col-5 col-sm-12  col-md-4  col-xl-2 d-flex align-items-center justify-content-center gap-1 justify-content-xl-start">
                        <!-- icon with boxes icon-->
                        <i class="nav-icon fas fa-boxes"></i>
                        <span>Total Qty Items:</span>
                        <div class="">
                            <strong><span type="number" id="txt-CBM_Total_Qty_Items" class="cbm_score"
                                    disabled></span></strong>
                        </div>
                    </div>
                    <?php } ?>    
                </div>
                <div class="row tabs">
                    <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                    <div data-table="prospectos"
                        class="col-12 col-md-4 col-xl-1 d-flex align-items-center justify-content-center btn tab tab-cotizacion">
                        Prospectos
                    </div>
                    <div data-table="embarque"
                        class="col-12 col-md-4 col-xl-1 d-flex align-items-center justify-content-center btn tab tab-cotizacion">
                        Por Embarcar
                    </div>
                    <?php } ?>
                    <?php if ($this->user->No_Grupo =="Coordinación"){  ?>
                    <div data-table="pagos"
                        class="col-12 col-md-3 col-xl-1 d-flex xl:max-w-fit align-items-center justify-content-center btn tab tab-cotizacion">
                        Pagos
                    </div>
                    <?php } ?>
                </div>
                <div class="table-responsive">
                    <table id="table-cotizacion-prospectos" class="table table-hover dataTable no-footer hidden">
                        <thead class="thead-default">
                            <tr>
                                <th>N°</th>
                                <th>Carga</th>
                                <th>F. Cierre</th>
                                <th>Asesor</th>
                                <th>COD</th>
                                <th>Fecha</th>
                                <th>Fecha de modificación</th>
                                <th>Nombre</th>
                                <th>DNI/RUC</th>
                                <th>Correo</th>
                                <th>Whatsapp</th>
                                <th>T. Cliente</th>
                                <th>Volumen</th>
                                <th>Volumen China</th>
                                <?php if ($this->user->No_Grupo == "Coordinación" || $this->user->No_Grupo == "Cotizador") {  ?>
                                <th>Qty Item</th>
                                <?php } ?>
                                <th>Fob</th>
                                <th>Logistica</th>
                                <th>Impuesto</th>
                                <th>Tarifa</th>
                                <th>Cotizacion</th>
                                <?php if ($this->user->No_Grupo == "Cotizador") {  ?>
                                <th style="min-width: 8em;">Estado</th>
                                <?php } ?>
                                <?php if ($this->user->No_Grupo == "Coordinación") {  ?>
                                <th>Acciones
                                </th>
                                <?php } ?>
                            </tr>
                        </thead>
                    </table>
                    <table id="table-cotizacion-embarque" class="table table-hover dataTable no-footer hidden">
                        <thead class="thead-default">
                            <tr>
                                <?php if (
                        !in_array($this->user->No_Grupo, $rolesChina)
                        && $this->user->No_Grupo != "Documentacion"
                      ) {  ?>
                                <th>Asesor</th>
                                <?php } ?>
                                <th style="min-width: 8em;" class="no-sort">Status</th>
                                <th class="orderable">N.</th>
                                <th class="buyer" style="min-width: 10em;">Buyer</th>
                                <?php if (!in_array($this->user->No_Grupo, $rolesChina) && $this->user->No_Grupo != "Documentacion") {  ?>
                                <th style="min-width: 8em;">Whatsapp</th>
                                <th style="min-width: 9em;">Estado</th>
                                <?php } ?>

                                <th style="min-width: 10em;">
                                    <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                                    Productos
                                    <?php } else { ?>
                                    Products
                                    <?php } ?> </th>
                                <th style="min-width: 4em;">Qty Box</th>
                                <th style="min-width: 4em;">CBM t.</th>
                                <th style="min-width: 4em;">Weight</th>
                                <th style="min-width: 6em;">Supplier</th>
                                <th style="min-width: 8em;">C. Supplier</th>
                                <th class="number" style="min-width: 8em;">P. Number</th>
                                <th style="min-width: 4em;">Qty Box.</th>
                                <th style="min-width: 4em;">CBM Ch.</th>
                                <th style="min-width: 8em;">Arrive Date </th>
                                <th>
                                    <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                                    Acciones
                                    <?php } else { ?>
                                    Actions
                                    <?php } ?>
                                </th>


                            </tr>
                        </thead>
                    </table>
                    <table id="table-cotizacion-pagos" class="table table-hover dataTable no-footer hidden">
                        <thead class="thead-default">
                            <tr>
                                <th>N.</th>
                                <th>Nombre</th>
                                <th>DNI/RUC</th>
                                <th>Whatsapp</th>
                                <th>T. Cliente</th>
                                <th>Estado</th>
                                <th>Conceptop</th>
                                <th>Importe</th>
                                <th>Pagado</th>
                                <th>Adelantos</th>

                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
    </section>


    <!-- Clientes View -->

    <section class="content px-3" id="clientes-container">
        <!-- header de la tabla -->
        <div class="row mb-2 d-flex justify-content-between">
            <div class="col-3 col-xl-1 py-sm-3 py-xl-0 py-md-0">
                <button type="button"
                    class="bg-white hover:bg-white-200 text-black-200 py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block btn-reporte btn-back-cotizacion"
                    data-type="html"><i class="fa fa-arrow-left"></i> Regresar</button>
            </div>
            <div class="col-xl-3 col-md-2"></div>
            <div class="col-6 col-md-0 col-xl-1">
            </div>
            <div class="col-12 col-md-2 col-xl-1">
            </div>
            <div class="col-12 col-md-0 col-xl-3">
                <label>&nbsp;</label>
            </div>
            <div class="col-6 col-sm-11 col-md-11 col-xl-2">
                <div class="dataTables_filter" style="display: flex;justify-content: flex-end;">
                    <input type="search"
                        class="form-control bg-white hover:bg-white-200 text-black-200 py-2 border border-transparent hover:border-orange-600 rounded search-table"
                        placeholder=" <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?> Buscar por
                          <?php } else { ?> Search for <?php } ?> " aria-controls="table-contenedor"
                        style="width:100%;min-width:200px; padding-left: 40px; background: url('https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/svgs/solid/search.svg') no-repeat 15px center;background-size: 14px; font-size: 14px;">
                </div>
            </div>
            <!-- Contenedor Principal de  <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                            Exportar
                          <?php } else { ?>
                            Export
                          <?php } ?>-->
            <div class="col-6 col-sm-1 dropdown">
                <button type="button" id="btn-exportar-carga"
                    class="bg-white hover:bg-white-200 text-black-200 py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block btn-reporte"
                    type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                        class="fa fa-download"></i> <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                    Exportar
                    <?php } else { ?>
                    Export
                    <?php } ?></button>
                <div class="dropdown-menu dropdown-menu-right px-3 py-3" aria-labelledby="btn-exportar-carga">
                    <button class="dropdown-item btn-block export-pdf-main-content"><i
                            class="fa fa-file-pdf color_icon_pdf"></i>
                        <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                        Exportar
                        <?php } else { ?>
                        Export
                        <?php } ?> PDF</button>
                    <button class="dropdown-item btn-block export-excel-main-content"><i
                            class="fa fa-file-excel color_icon_excel"></i>
                        <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                        Exportar
                        <?php } else { ?>
                        Export
                        <?php } ?> Excel</button>
                </div>
            </div>
        </div>

        <div class="table-responsive" class="table table-bordered table-hover table-striped">
            <div class="col-xl-12 list-cmb row mb-2 gap-xs-3 gap-md-0 mx-100 py-3  "
                style="border-bottom: #DFDFDF solid 2px">

                <div class="row pl-3">
                    <div class="d-flex align-items-center" style="width:10%; padding:15px 10px">
                        <span>Clientes</span>
                        <div class="col-4 col-md-4 px-0">
                            <span class="px-1" id="cotizacion_Cliente_name"></span>
                        </div>
                    </div>
                </div>

                <?php if($this->user->No_Grupo =="Coordinación"){?>

                <div
                    class="col-5 col-sm-12 col-md-4 col-xl-2 d-flex align-items-center justify-content-center justify-content-xl-start">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/c/cf/Flag_of_Peru.svg"
                        class="country-icons" alt="Perú">
                    <span>CBM Total:</span>
                    <div class="">
                        <strong><span id="txt-CBM_Cliente_Total_Peru" class="cbm_score"></span></strong>
                    </div>
                </div>
                <div
                    class="col-5 col-sm-12  col-md-4  col-xl-2 d-flex align-items-center justify-content-center justify-content-xl-start">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/f/fa/Flag_of_the_People%27s_Republic_of_China.svg"
                        alt="China" class="country-icons">
                    <span>CBM Total:</span>
                    <div class="">
                        <strong><span id="txt-CBM_Cliente_Total_China" class="cbm_score"></span></strong>
                    </div>
                </div>
                <?php } ?>
                <div
                    class="col-5 col-sm-12  col-md-4  col-xl-2 d-flex align-items-center justify-content-center gap-1 justify-content-xl-start">
                    <!-- icon with dollar icon-->
                    <i class="fas fa-dollar-sign"></i>
                    <span>Total Logistica:</span>
                    <div class="">
                        <strong><span id="txt-CBM_Cliente_Total_Logistica" class="cbm_score"></span></strong>
                    </div>
                </div>
                <div
                    class="col-5 col-sm-12  col-md-4  col-xl-2 d-flex align-items-center justify-content-center gap-1 justify-content-xl-start">
                    <!-- icon with dollar icon-->
                    <i class="fas fa-dollar-sign"></i>
                    <span>Total Pagado:</span>
                    <div class="">
                        <strong><span id="txt-CBM_Cliente_Total_Logistica_Pagado" class="cbm_score"></span></strong>
                    </div>
                </div>
                <?php if ($this->user->No_Grupo == "Coordinación" || $this->user->No_Grupo == "Cotizador") {  ?>
                <div
                    class="col-5 col-sm-12  col-md-4  col-xl-2 d-flex align-items-center justify-content-center gap-1 justify-content-xl-start">
                    <!-- icon with boxes icon-->
                    <i class="nav-icon fas fa-boxes"></i>
                    <span>Total Qty Items:</span>
                    <div class="">
                        <strong><span type="number" id="txt-CBM_Cliente_Total_Qty_Items" class="cbm_score"
                                disabled></span></strong>
                    </div>
                </div>
                <?php } ?> 
            </div>
            <div class="row tabs">
                <div data-table="general"
                    class="col-12 col-md-4 col-xl-1 d-flex align-items-center justify-content-center btn tab tab-clientes">
                    General
                </div>
                <?php if ($this->user->No_Grupo != "Documentacion") {  ?>
                <div data-table="variacion"
                    class="col-12 col-md-4 col-xl-1 d-flex align-items-center justify-content-center btn tab tab-clientes">
                    Variación
                </div>
                <div data-table="pagos"
                    class="col-12 col-md-4 col-xl-1 d-flex max-w-fit align-items-center justify-content-center btn tab tab-clientes">
                    Pagos
                </div>
                <?php } ?>
            </div>
            <table id="table-clientes-general" class="table table-hover dataTable no-footer">
                <thead class="thead-default">
                    <tr>
                        <th>N°</th>
                        <th style="min-width: 10em;">Nombre</th>
                        <th>DNI/RUC</th>
                        <th>Correo</th>
                        <th>Whatsapp</th>
                        <!-- <?php if ($this->user->No_Grupo != "Documentacion") {  ?>
            <th>Asesor</th>
          <?php } ?> -->
                        <th>T. Cliente</th>
                        <?php if ($this->user->No_Grupo != "Documentacion") {  ?>
                        <th>Volumen</th>
                        <?php if ($this->user->No_Grupo == "Coordinación" || $this->user->No_Grupo == "Cotizador") {  ?>
                        <th>Qty Item</th>
                        <?php } ?>
                        <th>Fob</th>
                        <th>Logistica</th>
                        <th>Impuesto</th>
                        <th>Tarifa</th>
                        <?php } ?>
                        <?php if ($this->user->No_Grupo == "Coordinación") {  ?>
                        <th style="min-width: 8em;">Estados</th>
                        <?php } ?>
                        <?php if ($this->user->No_Grupo == "Documentacion" || $this->user->No_Grupo == "Coordinación") {  ?>
                        <th style="min-width: 8em;">Status</th>
                        <?php } ?>
                        <th>Acciones</th>
                        
                    </tr>
                </thead>
            </table>
            <table id="table-clientes-variacion" class="table table-hover dataTable no-footer">
                <thead class="thead-default">
                    <tr>
                        <th>N°</th>
                        <th>Asesor</th>
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
            <table id="table-clientes-pagos" class="table table-hover dataTable no-footer hidden">
                <thead class="thead-default">
                    <tr>
                        <th>N.</th>
                        <th>Nombre</th>
                        <th>DNI/RUC</th>
                        <th>Whatsapp</th>
                        <th>T. Cliente</th>
                        <th>Estado</th>
                        <th>Conceptop</th>
                        <th>Importe</th>
                        <th>Pagado</th>
                        <th>Adelantos</th>

                    </tr>
                </thead>
            </table>
        </div>
    </section>
    <section class="content  px-4 py-3" id="clientes-documentation-container">
        <div class="row mb-2">
            <div class="col-12 col-md-2 col-xl-1">
                <button type="button"
                    class="bg-white hover:bg-white-200 text-black-200 py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block btn-reporte btn-back-cotizacion-documentacion"
                    data-type="html"><i class="fa fa-arrow-left"></i> Regresar</button>
            </div>
            <div class="col-xl-9 col-md-8"></div>

            <div class="col-12 col-md-2">
                <?php if ($this->user->No_Grupo != "Documentacion") { ?>
                <button type="button" id="btn-guardar-documentacion"
                    class="bg-orange text-black-200 py-2 px-2 border border-transparent rounded btn-block btn-reporte"
                    data-type="html"><i class="fa fa-save"></i>
                    <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                    Guardar
                    <?php } else { ?>
                    Save
                    <?php } ?> </button>
                <?php } ?>
            </div>

            <div class="name_cliente col-12 p-6" style="border-bottom: #DFDFDF solid 2px;">

            </div>

        </div>
        <div class="documentos-clientes-tabs pt-6">
        </div>
        <div class="container documentos-clientes-content mx-auto px-4 py-8 max-w-75 flex justify-content-center">


        </div>

        <!-- <div class="container ">

    </div> -->
        <!-- <div class="col col-12 my-2" id="clientes-documentacion">
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

              class="btn btn-primary btn-block btn-reporte col-6" data-type="html"><i class="fa fa-save"></i> <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
              Guardar
            <?php } else { ?>
              Save
            <?php } ?>  </div>
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
    <div class="col col-12 my-2" id="clientes-inpection"> -->
        <!-- <h3
        data-toggle="collapse"
        href="#collapse-inspection"
        role="button"
        aria-expanded="false"
        aria-controls="collapse-inspection"
        class="documentation-title">Inpection</h3> -->
        <!-- <div class="collapse show row row-cols-4 " id="collapse-inspection">
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
            </div>
          </div>
        </div>
      </div> -->

    </section>
    <section class="content" id="cotizacion-almacen">
        <!--row with button back and search-->
        <div class="row mb-2 gap-2 px-xl-3 px-0 pt-2 pt-sm-2 pt-md-2 pt-lg-0 pt-xl-0">
            <div class="col-sm-3 col-md-3 col-4 col-xl-1 py-3 py-sm-3 py-xl-0 py-lg-0 py-md-0">
                <button type="button"
                    class="bg-white hover:bg-white-200 text-black-200 py-2 px-2 mx-2 border border-transparent hover:border-orange-600 rounded btn-block btn-reporte"
                    id="btn-back-cotizacion-almacen"><i class="fa fa-arrow-left"></i> Regresar</button>
            </div>
            <div class="col-xl-5 col-lg-5 col-md-5 col-sm-5 col-0"></div>
            <div class="col-0 col-lg-4 col-md-2 col-xl-4 d-sm-none d-xl-block">
            </div>
            <div id="btn-grd-doc-not"
                class="col-12 col-md-3 col-xl-1 px-xl-1 px-lg-1 px-md-1 px-sm-5 px-5 <?php echo ($this->user->No_Grupo == "Cotizador") ? 'd-none' : ''; ?>">
                <button type="button" id="btn-guardar-doc-not"
                    class="py-sm-3 py-3 py-xl-2 py-md-2 py-lg-2 bg-orange hover:bg-orange-200 text-black-200 border border-transparent hover:border-orange-600 rounded btn-block btn-reporte"
                    data-type="html"><?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                    Guardar
                    <?php } else { ?>
                    Save
                    <?php } ?> <i class="fas fa-save"></i></button>
            </div>
        </div>



        <div class="row m-0 m-sm-2 m-md-2 m-lg-2 m-xl-2" style="border-bottom: #DFDFDF solid 2px;">
            <div class="d-flex col-4 col-md-3 col-xl-2 flex-column flex-md-row consolid-name"
                style="border-right: #DFDFDF solid 2px;">
                <span>Consolidado</span>
                <div class="">
                    <label id="cotizacion_name" disabled=""></label>
                </div>
            </div>
            <div class="col-8 col-xl-4 col-lg-6 col-md-6 d-flex align-items-center"
                style="border-right: #DFDFDF solid 2px;">
                <span id="client-title"></span>
            </div>
            <div class="col-3 col-xl-1  d-flex align-items-center">
                <span id="client-supplier-code"></span>
            </div>
        </div>

        <div class="row mb-2 justify-around">
            <div class="px-4 py-8 py-sm-4 file-section-container col-12 col-xl-4 col-lg-12 col-md-12 ">
                <div>
                    <div
                        class="text-lg font-semibold bg-white  shadow documentation-title d-flex justify-content-between rounded-top <?php echo ($this->user->No_Grupo == "Cotizador") ? 'pt-5 pl-5' : 'p-xl-5 p-lg-5 p-md-5 pt-sm-3 pt-3'; ?> title-inspection">
                        <h2 class="d-flex w-100 justify-content-between align-items-center title-inspection"><label
                                class="text-lg">
                                <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                                Documentación
                                <?php } else { ?>
                                Documentation
                                <?php } ?>
                                <i class="far fa-folder-open"></i></label>

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
                                    <div
                                        class="file-upload-box <?php echo ($this->user->No_Grupo == "Cotizador") ? 'd-none' : ''; ?>">
                                        <input type="file" id="file-input-documentacion" class="file-input" multiple
                                            accept=".pdf, .docx, .xlsx, .xls, .doc, .xlsm" />
                                        <label for="file-inpute" class="file-label d-flex">
                                            <i class="fas fa-upload"></i>
                                            <div class="file-group-text">
                                                <span class="file-text">
                                                    <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                                                    Selecciona o arrastra tu archivo aquí
                                                    <?php } else { ?>
                                                    Select or drag your file here
                                                    <?php } ?></span>
                                                <span class="file-format">
                                                    <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                                                    Formatos
                                                    <?php } else { ?>
                                                    Formats
                                                    <?php } ?>: .xlsx</span>
                                            </div>
                                            <button class="upload-button upload-button-documentacion" type="button">
                                                <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                                                Subir archivo
                                                <?php } else { ?>
                                                Upload Files
                                                <?php } ?></button>
                                        </label>
                                    </div>
                                    <!-- Cuadro de información del archivo subido (oculto inicialmente) -->
                                    <div class="file-lista hidden" id="file-lista-documentacion">
                                    </div>
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
            <!-- <div class="col-12 col-xl-5 col-md-12 px-4 py-8 py-sm-4"> -->
            <div class="px-4 py-8 py-sm-4 file-section-container col-12 col-xl-5 col-lg-12 col-md-12">
                <h2
                    class="text-lg font-semibold bg-white  shadow documentation-title d-flex justify-content-between rounded-top <?php echo ($this->user->No_Grupo == "Cotizador") ? 'pt-5 pl-5' : 'p-5'; ?> title-inspection">
                    <label class="text-lg">Inspection <i class="fas fa-images"></i></label>
                    <div id="btn-guardar-inspection" onclick="saveInspection()"
                        class="hidden bg-orange py-2 px-5 border border-transparent rounded text-sm" data-type="html">
                        <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                        Guardar
                        <?php } else { ?>
                        Save
                        <?php } ?> &nbsp; <i class="fa fa-save"></i>
                    </div>

                    <?php if ($this->user->No_Grupo == "GERENCIA" || $this->user->No_Grupo == "Coordinacion") {   ?>

                    <div id="btn-send-inspection">
                        <i class="fas fa-save"></i>
                    </div>

                    <?php } ?>
                </h2>
                <!--Button para guardar-->

                <div id="file-grid-inspection" class="grid gap-4 text-lg font-semibold bg-white shadow p-3 rounded-bot">
                    <form class="form-horizontal" method="post">
                        <div class="row">
                            <div class="col-12 col-sm-12" id="multiple-file-upload-image">
                                <div class="form-group">
                                    <div
                                        class="file-upload-box <?php echo ($this->user->No_Grupo == "Cotizador") ? 'd-none' : ''; ?>">
                                        <input type="file" id="file-input-inspeccion" class="file-input" multiple
                                            accept=".jpeg, .jpg, .png, .mp4" />
                                        <label for="file-inpute" class="file-label d-flex">
                                            <i class="fas fa-upload"></i>
                                            <div class="file-group-text">
                                                <span class="file-text">
                                                    <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                                                    Selecciona o arrastra tu archivo aquí
                                                    <?php } else { ?>
                                                    Select or drag your file here
                                                    <?php } ?></span>
                                                <span class="file-format">
                                                    <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                                                    Formatos
                                                    <?php } else { ?>
                                                    Formats
                                                    <?php } ?>: .jpeg .png .mp4</span>
                                            </div>
                                            <button class="upload-button upload-button-inspeccion" type="button">
                                                <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                                                Subir archivo
                                                <?php } else { ?>
                                                Upload Files
                                                <?php } ?></button>
                                        </label>
                                    </div>
                                    <!-- Cuadro de información del archivo subido (oculto inicialmente) -->
                                    <div class="file-lista hidden" id="file-lista-inspection">
                                    </div>
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
            <!-- </div> -->
            <div class="col-12 col-xl-3 col-md-12 px-4 py-8 pt-sm-4 note-container-container">
                <h2
                    class="text-lg font-semibold bg-white shadow documentation-title d-flex justify-content-between rounded-top p-5 title-inspection">
                    <label class="text-lg"><?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                        Notas
                        <?php } else { ?>
                        Notes
                        <?php } ?></label>
                    <button onclick="addNote()"
                        class="hidden new-doc-btn hover-effect flex items-center space-x-2 px-4 py-2 text-white rounded-lg hover:bg-orange-700 transition-colors bg-orange border border-transparent rounded">
                        <i class="fas fa-save  float-right"></i>

                        <span><?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                            Guardar
                            <?php } else { ?>
                            Save
                            <?php } ?> </span>
                    </button>
                </h2>
                <div id="note-container" class="bg-white shadow p-4 rounded-lg" style="height: 250px;">
                    <textarea id="txt-Id_Carga_Consolidada" class="form-control" style="height: 200px;"></textarea>
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
                <button type="button"
                    class="bg-white text-black-200 py-2 px-2 border border-transparent rounded btn-block btn-reporte btn-back-documentacion"
                    data-type="html"><i class="fa fa-arrow-left"></i> Regresar</button>
            </div>
            <div class="col-sm-3"></div>
            <div class="col-6 col-sm-2"></div>
            <div class="col-6 col-sm-2">
                <button type="button" id="btn-documentacion-factura"
                    class="bg-white text-black-200 py-2 px-2 border border-transparent rounded btn-block btn-reporte"
                    data-type="html"><i class="fas fa-file-invoice"></i>Factura General</button>
            </div>
            <div class="col-6 col-sm-2">
                <button type="button" id="btn-documentacion-zip"
                    class="bg-white text-black-200 py-2 px-2 border border-transparent rounded btn-block btn-reporte"
                    data-type="html"><i class="fa fa-download"></i>Descargar todo</button>
            </div>
            <div class="col-12 col-md-2">
                <button type="button" id="btn-documentacion-new"
                    class="bg-orange text-black-200 py-2 px-2 border border-transparent rounded btn-block btn-reporte"
                    data-type="html">Nuevo documento<i class="fa fa-plus"></i></button>
            </div>
            <div class="mx-10" style="border-bottom: #DFDFDF solid 2px; width:100%">
                <label>&nbsp;</label>
            </div>
        </div>

        <!-- body -->
        <div
            class="row m-20 documentation-files-container grid grid-cols-2   gap-4 text-lg font-semibold bg-white shadow p-3 rounded-lg">
        </div>

    </section>
    <section class="content" id="cotizacion-final-container">
        <div class="container-fluid">
            <!-- Header de la tabla -->
            <div class="col-3 col-xl-1 py-sm-3 py-xl-0 py-md-0">
                <button type="button" class="bg-white hover:bg-white-200 text-black-200 py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block btn-reporte"  id="btn-back-cotizacion-final"><i class="fa fa-arrow-left"></i> Regresar</button>
            </div>
            <div class="col-xl-12 list-cmb row mb-2 gap-xs-3 gap-md-0 mx-100 pt-3" style="border-bottom: #DFDFDF solid 2px">
                <div class="d-flex align-items-center col-sm-3 xl:max-w-[200px]" style="border-right: #DFDFDF solid 2px; padding:15px 10px">
                    <span id="cotizacion-final-title"></span>
                </div>
                
                <div class="col-5 col-sm-12 col-md-4 col-xl-5 d-flex align-items-center justify-content-center justify-content-xl-start">
                    <span>Cotitación Final</span>
                </div>
                <div class="flex xl:w-[40%]">
                    <div class="col-5 col-sm-12  col-md-4  col-xl-4 d-flex align-items-center justify-content-center justify-content-xl-start">
                        <button id="uploadGeneral" class="tab">
                            <div class="fa fa-upload"></div>
                            Subir Factura
                        </button>
                    </div>
                    <div class="col-5 col-sm-12  col-md-4  col-xl-4 d-flex align-items-center justify-content-center justify-content-xl-start">
                        <button id="downloadTemplate" class="tab">
                            <div class="fa fa-download"></div>
                            Plantilla General
                        </button>
                    </div>
                    <div class="col-5 col-sm-12  col-md-4  col-xl-4 d-flex align-items-center justify-content-center gap-1 justify-content-xl-start">
                        <button id="uploadFinal" class="tab">
                            <div class="fa fa-upload"></div>
                            Plantilla Final
                        </button>
                    </div>
                </div>
            </div>
            <div class="row tabs">
                <div data-table="general"
                    class="col-12 col-md-4 col-xl-1 d-flex max-w-fit align-items-center justify-content-center btn tab tab-clientes-final">
                    General
                </div>
                <?php if ($this->user->No_Grupo != "Coordinacion") {  ?>
                
                <div data-table="pagos"
                    class="col-12 col-md-4 col-xl-1 d-flex max-w-fit align-items-center justify-content-center btn tab tab-clientes-final">
                    Pagos
                </div>
                <?php } ?>
            </div>
            <div class="table-responsive" class="table table-hover">
                <table id="table-cotizacion-final" class="table table-hover dataTable no-footer">
                    <thead class="thead-default">
                        <tr>
                            <th>N°</th>
                            <th>Nombre</th>
                            <th>DNI/RUC</th>
                            <th>Correo</th>
                            <th>Whatsapp</th>
                            <th>T. Cliente</th>
                            <th>Volumen </th>
                            <th>Fob</th>
                            <th>Logística</th>
                            <th>Impuesto</th>
                            <th>Tarifa </th>
                            <th>Estados</th>
                            <th>C Final</th>
                        </tr>
                    </thead>
                </table>
                <table id="table-cotizacion-final-pagos" class="table table-hover hidden">
                    <thead class="thead-default">
                        <tr>
                            <th>N.</th>
                            <th>Nombre</th>
                            <th>DNI/RUC</th>
                            <th>Whatsapp</th>
                            <th>T. Cliente</th>
                            <th>Importe</th>
                            <th>Pagado</th>
                            <th>Adelantos</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>   
    </section>
    <section class="content" id="factura-guia-container">
        <div class="container-fluid">
            <!-- Header de la tabla -->
            <div class="col-3 col-xl-1 py-sm-3 py-xl-0 py-md-0">
                <button type="button" class="bg-white hover:bg-white-200 text-black-200 py-2 px-2 border border-transparent hover:border-orange-600 rounded btn-block btn-reporte" id="btn-back-factura-guia"><i class="fa fa-arrow-left"></i> Regresar</button>
            </div>
            <div class="col-xl-12 row mb-2 gap-xs-3 gap-md-0 mx-100 pt-3" style="border-bottom: #DFDFDF solid 2px">
                <div class="d-flex align-items-center col-sm-3 xl:max-w-[200px]" style="border-right: #DFDFDF solid 2px; padding:15px 10px">
                    <span id="factura-guia-title"></span>
                </div>
                
                <div class="col-5 col-sm-12 col-md-4 col-xl-5 d-flex align-items-center justify-content-center justify-content-xl-start">
                    <span>Factura y Guia Remisión</span>
                </div>
            </div>
            <div class="table-responsive" class="table table-hover">
                <table id="table-factura-guia" class="table table-hover dataTable no-footer">
                    <thead class="thead-default">
                        <tr>
                            <th>N°</th>
                            <th>Nombre</th>
                            <th>DNI/RUC</th>
                            <th>Correo</th>
                            <th>Whatsapp</th>
                            <th>T. Cliente</th>
                            <th>Ajuste</th>
                            <th>C.Final</th>
                            <th>Factura C.</th>
                            <th>Guia R.</th>
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

    <!-- modal with id modalClientePagoCoordination -->
    <div class="modal fade" id="modalClientePagoCoordination" tabindex="-1" role="dialog" aria-labelledby="modalClientePagoCoordinationLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle del Adelanto</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <!-- Aquí se cargan los datos dinámicamente -->
            </div>
            </div>
        </div>
    </div>

    <!-- modal with table and id modalClientePagosCoordination -->
    <div class="modal fade" id="modalClientePagosCoordination" tabindex="-1" role="dialog"
        aria-labelledby="modalClientePagosCoordinationLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalClientePagosCoordinationLabel">Pagos del Cliente</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table id="table-pagos-tracking-coordinacion" class="table table-hover dataTable no-footer">
                        <thead class="thead-default">
                            <tr>
                                <th>N°</th>
                                <th>Fecha</th>
                                <th>Banco</th>
                                <th>Monto</th>
                                <th>Voucher</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="uploadModalInspection" tabindex="-1" aria-labelledby="uploadModalLabel"
        aria-hidden="true">
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

    <div class="modal fade" id="modal-crear-cotizacion" tabindex="-1" role="dialog" aria-labelledby="modal-cotizacion"
        aria-hidden="true">
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
                                        <input type="file" id="file-input-prospecto" class="file-input"
                                            name="cotizacion"
                                            accept=".xlsx,.xls,.csv,.xlsb,.xlsm,.xltx,.xltm,.xls,.xlt" />
                                        <label for="file-inpute" class="file-label d-flex">
                                            <i class="fas fa-upload"></i>
                                            <div class="file-group-text">
                                                <span class="file-text">
                                                    <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                                                    Selecciona o arrastra tu archivo aquí
                                                    <?php } else { ?>
                                                    Select or drag your file here
                                                    <?php } ?></span>
                                                <span class="file-format">
                                                    <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                                                    Formatos
                                                    <?php } else { ?>
                                                    Formats
                                                    <?php } ?>: .xlsx</span>
                                            </div>
                                            <button class="upload-button" type="button">
                                                <?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                                                Subir archivo
                                                <?php } else { ?>
                                                Upload Files
                                                <?php } ?></button>
                                        </label>

                                        <!-- Cuadro de información del archivo subido (oculto inicialmente) -->
                                        <div class="file-info-box hidden">
                                            <div class="file-info">
                                                <div class="file-iconic">
                                                </div>
                                                <span class="file-name"></span>
                                                <span class="file-size"></span>
                                                <div class="remove-file-button">
                                                    <i class="fas fa-trash"></i>
                                                </div>
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
                        <button type="button"
                            class="bg-white hover:bg-white-200 text-black-200 py-2 px-5 border border-transparent hover:border-orange-600 rounded"
                            data-dismiss="modal">Cancelar</button>
                        <button type="button" id="btn-actualizar-cotizacion"
                            class="bg-orange py-2 px-5 border border-transparent hover:border-orange-600 rounded">Actualizar</button>
                        <button type="button" id="btn-guardar-cotizacion"
                            class="bg-orange py-2 px-5 border border-transparent hover:border-orange-600 rounded"><?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                            Guardar
                            <?php } else { ?>
                            Save
                            <?php } ?> </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-crear" tabindex="-1" role="dialog" aria-labelledby="modal-crear"
        aria-hidden="true">
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
                            <div class="col-6 col-sm-6">
                                <!--carga -->
                                <div class="form-group">
                                    <label>Carga <span class="label-advertencia text-danger"> *</span></label>
                                    <select type="text" id="txt-No_Carga" required name="carga"
                                        class="form-control input-report required">
                                    </select>
                                    <span class="invalid-feedback" id="error-carga">La carga es requerida</span>
                                </div>
                            </div>

                            <div class="col-6 col-sm-6">
                                <!--fecha cierre -->
                                <div class="form-group">

                                    <label>Fecha Cierre <span class="label-advertencia text-danger"> *</span></label>
                                    <input type="text" name="f_cierre" required id="txt-Fe_Cierre"
                                        placeholder="00/00/0000"
                                        class=" w-100 form-control input-report required input-date">
                                    <span class="invalid-feedback" id="error-f-cierre">La fecha de Cierre es
                                        requerida</span>
                                </div>
                            </div>
                            <div class="col-6 col-sm-6">
                                <!--mes -->
                                <div class="form-group">
                                    <label>Mes <span class="label-advertencia text-danger"> *</span></label>

                                    <select id="txt-Mes" required name="mes"
                                        class=" w-100 form-control input-report required">
                                    </select>
                                    <span class="invalid-feedback" id="error-mes">El mes es requerido</span>
                                </div>
                            </div>
                            <div class="col-6 col-sm-6">
                                <!--fecha arribo -->
                                <div class="form-group">

                                    <label>Fecha Arribo <span class="label-advertencia text-danger"> *</span></label>
                                    <input type="text" name="f_puerto" required id="txt-Fe_Puerto"
                                        placeholder="00/00/0000"
                                        class="form-control input-report required input-date w-100">
                                    <span class="invalid-feedback" id="error-f-puerto">La fecha Arribo es
                                        requerida</span>
                                </div>
                            </div>
                            <div class="col-6 col-sm-6">
                                <!--pais -->
                                <div class="form-group">
                                    <label>Pais <span class="label-advertencia text-danger"> *</span></label>
                                    <select id="txt-ID_Pais" required name="id_pais"
                                        class="form-control input-report required">

                                    </select>
                                    <!-- <span class="help-block text-danger" id="error"></span>
                    <input type="hidden" id="txt-ID_Carga_Consolidada" name="id" value="0"> -->
                                </div>
                            </div>



                            <div class="col-6 col-sm-6">
                                <div class="form-group" id="div-Fe_Entrega">

                                    <label>Fecha Entrega <span class="label-advertencia text-danger"> *</span></label>
                                    <input type="text" required name="f_entrega" id="txt-Fe_Entrega"
                                        placeholder="00/00/0000"
                                        class="w-100 form-control input-report required input-date">
                                    <span class="invalid-feedback" id="error-f-entrega">La fecha entrega es
                                        requerida</span>
                                </div>
                            </div>
                            <div class="col-6 col-sm-6">
                                <!--empresa -->
                                <div class="form-group ">
                                    <label>Empresa <span class="label-advertencia text-danger"> *</span></label>
                                    <input type="text" required name="empresa" id="txt-Empresa"
                                        placeholder="Ingresa el nombre de la empresa"
                                        class="form-control input-report required text">
                                    <span class="invalid-feedback" id="error-empresa">La empresa es requerida</span>
                                </div>
                            </div>
                        </div>
                </div>
                </form>
                <div class="modal-footer">
                    <button type="button"
                        class="bg-white hover:bg-white-200 text-black-200 py-2 px-5 border border-transparent hover:border-orange-600 rounded"
                        data-dismiss="modal">Cancelar</button>
                    <button type="button" id="btn-actualizar"
                        class="bg-orange py-2 px-5 border border-transparent hover:border-orange-600 rounded">Actualizar</button>
                    <button type="button" id="btn-guardar"
                        class="bg-orange py-2 px-5 border border-transparent hover:border-orange-600 rounded"><?php if (!in_array($this->user->No_Grupo, $rolesChina)) {  ?>
                        Guardar
                        <?php } else { ?>
                        Save
                        <?php } ?> </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para mover cotización a otro consolidado -->
    <div class="modal fade" id="modal-move-cotizacion" tabindex="-1" role="dialog" aria-labelledby="modalMoveCotizacionLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalMoveCotizacionLabel">Mover cotización a otro consolidado</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <label for="selectConsolidado">Selecciona el consolidado destino:</label>
                <select id="selectConsolidado" class="form-control"></select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn-confirm-move">Mover cotización</button>
            </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal para registrar pago de curso -->
<div class="modal fade" id="modal-pago-curso" tabindex="-1" aria-labelledby="modalPagoCursoLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form id="form-pago-curso" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header justify-content-center">
          <h2 class="modal-title" id="modalPagoCursoLabel">Registrar Pago de Curso</h2>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body row">
          <input type="hidden" id="id-pedido-curso" name="idPedido">
          <div class="col-md-6 mb-3">
            <label for="monto" class="form-label">Monto</label>
            <div class="input-soles-wrapper">
                <span class="soles-symbol">S/</span>
                <input type="number" id="monto" name="monto" class="form-control" step="0.01" required>
            </div>
          </div>
          <div class="col-md-6 mb-3">
            <label for="banco" class="form-label">Banco</label>
            <div class="d-flex gap-3 align-items-center" id="banco-group">
                <div class="form-check form-check-inline text-center">
                <input class="form-check-input" type="radio" name="banco" id="banco-bcp" value="BCP" required>
                <label class="form-check-label flex" for="banco-bcp">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/c/ca/Logo_credito.gif" alt="BCP" style="height:32px;"><br>
                </label>
                </div>
                <div class="form-check form-check-inline text-center">
                <input class="form-check-input" type="radio" name="banco" id="banco-interbank" value="INTERBANK" required>
                <label class="form-check-label flex" for="banco-interbank">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/c/ca/Interbank_logo.svg" alt="INTERBANK" style="height:32px;"><br>
                </label>
                </div>
                <div class="form-check form-check-inline text-center">
                <input class="form-check-input" type="radio" name="banco" id="banco-yape" value="YAPE" required>
                <label class="form-check-label flex" for="banco-yape">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/0/08/Icono_de_la_aplicaci%C3%B3n_Yape.png" alt="YAPE" style="height:32px;"><br>
                </label>
                </div>
            </div>
          </div>
          <div class="col-md-6 mb-3">
            <label for="fecha" class="form-label">Fecha</label>
            <input type="date" id="fecha_pago" name="fecha" class="form-control" required>
          </div>
          <div class="col-md-12 mb-3">
            <label class="form-label">Voucher</label>
            <div id="file-upload-pagos-container">
              <div class="file-upload-box" id="single-file-upload-pagos">
                <input type="file" id="file-input-pagos" class="file-input" name="voucher"
                  accept=".pdf, .docx, .xlsx, .xls, .xlsm, .csv, .xlsb, .xltx, .xlt, .png, .jpg, .jpeg">
                <label for="file-input-pagos" class="file-label d-flex">
                  <i class="fas fa-upload"></i>
                  <div class="file-group-text">
                    <span class="file-text">Selecciona o arrastra tu archivo aquí</span><br>
                    <span class="file-format">Formatos: .pdf, .docx, .xlsx, .xls, .xlsm, .csv, .xlsb, .xltx, .xlt, .png, .jpg, .jpeg</span>
                  </div>
                  <button class="upload-button upload-button-pagos" type="button">Subir archivo</button>
                </label>
                <div class="file-info-box hidden">
                  <div class="file-info">
                    <div class="file-iconic"></div>
                    <span class="file-name"></span>
                    <span class="file-size"></span>
                    <button class="remove-file-button"><i class="fas fa-trash"></i></button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn bg-orange text-white">Guardar</button>
        </div>
      </div>
    </form>
  </div>
</div>

    <!-- Modal para imágenes -->
    <div class="modal fade" id="image-modal" tabindex="-2" role="dialog" aria-labelledby="imageModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <img id="image-preview" src="" style="width: 100%;">
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para videos -->
    <div class="modal fade" id="video-modal" tabindex="-1" role="dialog" aria-labelledby="videoModalLabel"
        aria-hidden="true">
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
    <div class="modal fade" id="file-modal" tabindex="-1" role="dialog" aria-labelledby="fileModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <iframe id="file-preview" src="" style="width: 100%; height: 500px;"></iframe>
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

.country-icons {
    width: 24px;
    height: 16px;
    margin-right: 5px;
    margin-top: -4px;
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
    height: auto;
    min-height: 150px;
}

button.swal2-confirm.swal2-styled.swal2-default-outline {
    color: #585858;
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
    width: 60%;
    padding: 0.75rem .5rem;
    background-color: #F0F4F9;
    color: #272A30;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: .75rem;
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

div#table-cotizacion-embarque_filter,
div#table-contenedor_filter,
div#table-clientes-general_filter,
div#table-cotizacion-inspection_filter,
div#table-cotizacion-inspection-coordinacion_filter,
div#table-clientes-variacion_filter,
div#table-cotizacion-prospectos_filter,
div#table-cotizacion-pagos_filter,
div#table-clientes-pagos_filter {
    display: none;
}



#table-contenedor_wrapper.dt-buttons.btn-group.flex-wrap {
    display: none;
}

.tabs{
    gap:10px;
    margin-right: 0;
    margin-left: 0;
    padding-top: 10px;
    padding-bottom: 10px;
}

.tab {
    padding: 0.5em;
    border-radius: 0.5em;
    width: 100%;
    border-width: 2px;
    border-color: #CDCDCD;
    color: #7E7E7E;
    text-align: center;
    cursor: pointer;
    background-color: transparent;

}

.tab.active {
    background-color: #FFFFFF;
    color: black;
}

.tab:hover {
    background-color: #FFFFFF;
    color: black;
    transition: background-color 0.5s ease;
}

.documentos-clientes-tabs {
    display: grid;
    grid-template-columns: repeat(8, 1fr);
    gap: 1em;
    margin: 1em 2em;
}

.file-icon-container {
    display: flex;
    justify-content: space-between;
    width: -webkit-fill-available;
}

label {
    font-family: Epilogue;
    font-size: 14px;
    font-weight: 400;
    color: #272A30;
}

label>i {
    font-size: 20px;
}

/* Ocultar el texto del botón en pantallas pequeñas */
@media (max-width: 768px) {

    #table-contenedor.table.table-hover.dataTable.no-footer tbody {
        background-color: transparent;
    }

    .fa-bars {
        font-size: 20px;
    }

    h1,
    .content-header h1 {
        font-size: 1.2rem;
    }

    body {
        font-size: 12px;
    }

    button>.fa {
        font-size: 15px !important;
        width: 20px;
        height: 20px;
        margin-top: 1px;
    }

    #table-cotizacion-embarque tbody {
        padding: 10px 20px;
    }

    .consolid-name {
        display: none !important;
    }

    .documentation-title {
        border-bottom: 1px #DFDFDF solid;
    }

    .documentation-title>h2>label,
    .title-inspection>label {
        font-size: 1rem !important;
    }

    .file-upload-box {
        padding: 0;
        height: 200px;
    }

    .form-horizontal {
        max-width: 100%;
        overflow-x: hidden;
        box-sizing: border-box;
    }

    .file-preview>p {
        width: 70%;
        overflow-x: hidden;
        font-weight: 400;
        font-size: 12px;
    }

    .file-preview {
        width: 80%;
    }

    .file-format {
        font-size: 10px !important;
        margin-top: -5%;
        padding-bottom: 5%;
    }

    .file-item {
        padding: 8px;
        background: transparent;
    }

    #table-contenedor thead {
        display: none;
        /* Ocultar encabezados de la tabla */
    }

    #table-contenedor tbody tr:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.18);
        background: #f7f7f7;
    }

    .view-eye {
        display: none;
    }

    .buyer {
        min-width: 20em !important;
    }

    .number {
        min-width: 10em !important;
    }

    #table-contenedor tbody tr {
        font-size: 11px;
        cursor: pointer;
        transition: box-shadow 0.2s;
        display: grid;
        grid-template-columns: 1fr 1fr;
        /* Dos columnas iguales */
        margin-bottom: 16px;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 10px 16px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        background-color: white;
    }

    #table-contenedor tbody td {
        align-content: center;
        gap: 0px;
        /* Espaciado entre   el select y el botón */
    }

    .form-horizontal>.row>div>.form-group {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .form-group>.file-upload-box {
        width: 80%;
    }

    #table-contenedor tr.odd>td,
    #table-contenedor tr.even>td {
        border: 0px solid transparent;
        padding: 0rem;
    }

    td>.form-control {
        height: auto;
        width: auto;
        border-radius: .40rem;
        border: 0px solid #ddd;
        font-size: 13px;
        padding: 9px 20px;
        margin: 1px;
    }

    .form-control:disabled {
        background-color: transparent;
        border: 0px solid #ddd;
        opacity: 1;
        font-size: 12px;
    }

    #btn-exportar-carga,
    #btn-filtrar-carga,
    #btn-cargar-carga {
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 0;
        /* Oculta el texto del botón */
    }

    #btn-exportar-carga i {
        margin-right: 0;
        /* Asegúrate de que el ícono esté centrado */
    }

    #btn-exportar-carga>.fa-upload,
    #btn-exportar-carga>.fa-download,
    #btn-filtrar-carga>.fa-filter,
    #btn-cargar-carga>.fa-upload {
        font-size: 20px;
        margin-right: -12px;
    }

    .note-container-container {
        min-height: 10%;
        padding-bottom: 25%;
        margin-top: 7%;
    }

    #btn-grd-doc-not {
        position: absolute;
        bottom: 0%;
        left: 0%;
        order: 99;
    }

    #txt-Id_Carga_Consolidada {
        height: 20vh;
    }

    .file-label>i {
        font-size: 2.5rem;
    }

    .file-label {
        flex-direction: column;
        justify-content: center;
        gap: 0px;
        height: 100%;
        font-size: 11px;
    }

    .file-group-text {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .file-group-text>span {
        font-size: 12px;
    }

    .file-format {
        margin-bottom: 0;
    }

    .title-inspection {
        justify-content: center !important;
    }

}

.scroll-arrow {
    position: absolute;
    top: 400px;
    transform: translateY(-50%);
    background-color: rgba(0, 0, 0, 0.5);
    color: white;
    border: none;
    padding: 10px;
    cursor: none;
    z-index: 10;
    border-radius: 50%;
    font-size: 16px;
    display: none;
    /* Ocultar inicialmente */
    justify-content: center;
    align-items: center;
}

.scroll-arrow.left {
    left: 10px;
}

.scroll-arrow.right {
    right: 10px;
}

.scroll-arrow:hover {
    background-color: rgba(0, 0, 0, 0.8);
}

.swal2-input {
    width: 80%;
    height: 40px;
    padding: 10px;
    font-size: 16px;
    border-radius: 5px;
    border: 1px solid #ccc;
    box-shadow: none;
    transition: border-color 0.3s ease;
}
.table-responsive{
    overflow-x: hidden;
}
.input-soles-wrapper {
  position: relative;
}
.input-soles-wrapper .soles-symbol {
  position: absolute;
  left: 20px;
  top: 51%;
  transform: translateY(-50%);
  pointer-events: none;
  font-size: 1rem;
}
.input-soles-wrapper input {
  padding-left: 2.2em;
}
#modalClientePagosCoordination >.modal-dialog>.modal-content {
 background-color: #F0F4F9;
} 
</style>