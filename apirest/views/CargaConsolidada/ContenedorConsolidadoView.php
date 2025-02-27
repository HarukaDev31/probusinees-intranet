<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-8">
          <h1>
            <i class="<?php echo $this->MenuModel->verificarAccesoMenuCRUD()->Txt_Css_Icons; ?>" aria-hidden="true"></i> <span id="section-title"><?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?></span>
            &nbsp;<span id="span-id_pedido" class="badge badge-secondary"></span>
          </h1>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>
  <section class="content" id="main-container">
    <div class="container-fluid">
      <div class="row">
        <div class="col-6 col-sm-2">
        <i class="flag flag-andorra"></i>

          <label>F. Inicio <span class="label-advertencia text-danger"> *</span></label>
          <div class="form-group">
            <input type="text" id="txt-Fe_Inicio_Carga" class="form-control  input-date input-report required" value="<?php echo dateNow('month_date_ini_report'); ?>">
            <span class="help-block text-danger" id="error"></span>
          </div>
        </div>
        <div class="col-6 col-sm-2">
          <label>F. Fin <span class="label-advertencia text-danger"> *</span></label>
          <div class="form-group">
            <input type="text" id="txt-Fe_Fin_Carga" class="form-control input-date input-report required">
            <span class="help-block text-danger" id="error"></span>
          </div>
        </div>
        <div class="col-6 col-sm-3">
          <label>Estado</label>
          <select id="txt-ID_Estado" name="ID_Estado" class="form-control input-estado">
            <option value="0" selected>Todos</option>
            <option value="PENDIENTE">PENDIENTE</option>
            <option value="RECIBIENDO">RECIBIENDO</option>
            <option value="COMPLETADO">COMPLETADO</option>
          </select>
        </div>
        <?php if ($this->user->No_Grupo == "Coordinación") {  ?>
          <div class="col-6 col-sm-2">
            <label>&nbsp;</label>
            <button type="button" id="btn-crear" class="btn btn-primary btn-block btn-reporte" data-type="html"><i class="fa fa-plus"></i> Crear</button>
          </div>
        <?php } ?>

        <div class="col-6 col-sm-2">
          <label>&nbsp;</label>
          <button type="button" id="btn-buscar-carga" class="btn btn-primary btn-block btn-reporte" data-type="html"><i class="fa fa-search"></i> Buscar</button>
        </div>
      </div>
      <div class="table-responsive div-Listar">
        <table id="table-contenedor" class="table table-bordered table-hover table-striped">
          <thead class="thead-light">
            <tr>
              <?php if ($this->user->No_Grupo == "Coordinación") {
              ?>
                <th>Carga</th>
                <th>Mes </th>
                <th>Pais</th>

                <th>F. Cierre</th>
                <th>F. Arribo</th>
                <th>F. Entrega</th>

                <th>Empresa</th>
                <th>Ver</th>
                <th>Estado</th>
                <th>Acciones
                </th>
              <?php } else { ?>
                <th>Month</th>
                <th>Country</th>
                <th>Cargo.</th>
                <th>Cut off</th>
                <th>Company</th>
                <th>Check</th>
                <th>Status</th>

              <?php } ?>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </section>
  <section class="content" id="cotizacion-container">
    <div class="container-fluid ">
      <div class="row mb-2">

        <div class="col-12 col-md-4">
          <label>&nbsp;</label>
          <?php if ($this->user->No_Grupo != "ContenedorAlmacen") {  ?>
            <button type="button" id="btn-crear-cotizacion" class="btn btn-primary btn-block btn-reporte" data-type="html"><i class="fa fa-plus"></i> Crear Prospecto</button>
          <?php } ?>
        </div>
        <div class="col-12 col-md-4">
          <label>&nbsp;</label>


        </div>
        <div class="col-12 col-md-3">
        </div>
        <div class="col-12 col-md-1">
          <label>&nbsp;</label>
          <button type="button" class="btn btn-outline-primary btn-block btn-reporte btn-back-cotizacion" data-type="html"><i class="fa fa-arrow-left"></i> </button>
        </div>
      </div>
      <div class="row mb-2">

        <div class="col-12 col-md-3">
          <label>CBM Total Peru</label>
          <div class="input-group mb-3">
            <input type="number" id="txt-CBM_Total_Peru" class="form-control input-report" disabled>
          </div>
        </div>
        <div class="col-12 col-md-3">
          <label>CBM Total China</label>
          <div class="input-group mb-3">
            <input type="number" id="txt-CBM_Total_China" class="form-control input-report" disabled>
          </div>
        </div>
        <?php if ($this->user->No_Grupo == "ContenedorAlmacen") {  ?>

          <div class="col-12 col-md-3">
            <label>Packing List</label>
            <div class="input-group mb-3" id="packing-list-container">
            </div>
          </div>
          <div class="col-12 col-md-3">
            <label>BL File</label>
            <div class="input-group mb-3" id="bl-file-container">
            </div>
          </div>
        <?php } ?>
      </div>

      <div class="table-responsive">
        <table id="table-cotizacion-prospectos" class="table table-bordered table-hover table-striped">
          <thead class="thead-light">
            <tr>
              <th>N°</th>
              <th>Fecha</th>
              <th>Nombre</th>
              <th>DNI/RUC</th>
              <th>Correo</th>
              <th>Whatsapp</th>
              <th>T. Cliente</th>
              <th>Volumen</th>
              <th>Monto</th>
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
        <table id="table-cotizacion-embarque" class="table table-bordered table-hover table-striped">
          <thead class="thead-light">
            <tr>
              <?php if ($this->user->No_Grupo != "ContenedorAlmacen") {  ?>
                <th>Asesor</th>
              <?php } ?>
              <th style="min-width: 8em;" class="no-sort">Status</th>
              <th class="orderable">N.</th>
              <th>Buyer</th>
              <?php if ($this->user->No_Grupo != "ContenedorAlmacen") {  ?>
                <th>Whatsapp</th>
                <th
                  style="min-width: 10em;">Estado</th>
              <?php } ?>

              <th
                style="min-width: 10em;">Productos</th>
              <th
                style="min-width: 3em;">Qty Box.</th>
              <th
                style="min-width: 4em;">CBM Total</th>
              <th
                style="min-width: 4em;">Weight</th>
              <th
                style="min-width: 5em;">Supplier</th>
              <th
                style="min-width: 5em;">Code Supplier</th>
              <th
                style="min-width: 7em;">Phone Number</th>
              <th
                style="min-width: 3em;">Qty Box.</th>
              <th
                style="min-width: 3em;">CBM China </th>
              <th
                style="min-width: 5em;">Arrive Date </th>
              <th> Ver </th>

              <th> Acciones </th>


            </tr>
          </thead>
        </table>
      </div>
    </div>
  </section>
  <section class="content" id="clientes-container">
    <div class="row mb-2">
      <!--select estado  with TODOS PENDIENTE,COTIZADO,PAGADO Y ENTREGADO OPTIOONS-->
      <div class="col-12 col-md-4">
        <label>Estado</label>
        <select id="txt-ID_Estado_Cliente" name="ID_Estado" class="form-control input-estado">
          <option value="0" selected>Todos</option>
          <option value="RESERVADO">RESERVADO</option>
          <option value="NO RESERVADO">NO RESERVADO</option>
          <option value="DOCUMENTACION">DOCUMENTACION</option>
          <option value="C FINAL">C FINAL</option>
          <option value="FACTURADO">FACTURADO</option>
        </select>
      </div>

      <div class="col-12 col-md-4">
        <label>&nbsp;</label>
        <button type="button" id="btn-buscar-clientes-general" class="btn btn-primary btn-block btn-reporte" data-type="html"><i class="fa fa-search"></i> Buscar</button>
      </div>
      <div class="col-12 col-md-3">
      </div>
      <div class="col-12 col-md-1">
        <label>&nbsp;</label>
        <button type="button" class="btn-back-cotizacion btn btn-outline-primary btn-block btn-reporte" data-type="html"><i class="fa fa-arrow-left"></i> </button>
      </div>
    </div>
    <div class="row mb-2">
      <!-- monto total, cbm total_china-->
      <div class="col-12 col-md-3">
        <label>Monto Total</label>
        <div class="input-group mb-3">
          <input type="number" id="txt-Monto_Total" class="form-control input-report" disabled>
        </div>
      </div>
      <div class="col-12 col-md-3">
        <label>CBM Total China</label>
        <div class="input-group mb-3">
          <input type="number" id="txt-CBM_Total_China_Clientes" class="form-control input-report" disabled>
        </div>
      </div>
      <div class="table-responsive" class="table table-bordered table-hover table-striped">
        <table id="table-clientes-general" class="table table-bordered table-hover table-striped">
          <thead class="thead-light">
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

              <th>Ver</th>
              <th>Estados</th>
              <th>Acciones</th>
            </tr>
          </thead>
        </table>
        <table id="table-clientes-variacion" class="table table-bordered table-hover table-striped">
          <thead class="thead-light">
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
    <div class="row mb-2 bg-white shadow-sm px-4 py-4 mx-3">
      <div class="col-12 col-md-8">
        <div class="d-flex flex-row">
          <h1 id="client-title" style="margin-right: 2em;"></h1>
          <h1 id="client-supplier-code"></h1>
        </div>
      </div>
      <div class="col-12 col-md-4 d-flex justify-content-end  ">
        <div class="btn btn-outline-primary " data-type="html" id="btn-back-cotizacion-almacen"><i class="fa fa-arrow-left"></i> </div>
      </div>
    </div>
    <div class="row mb-2">
      <div class="container mx-auto px-4 py-8 file-section-container col-12 col-md-8">
        <div>
          <h2 class="text-lg font-semibold bg-white  shadow documentation-title d-flex justify-content-between p-5 rounded-top">Documents
            <button data-toggle="modal" data-target="#uploadModal" class="new-doc-btn hover-effect flex items-center space-x-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
              <i class="fas fa-plus"></i>
              <span>Nuevo</span>
            </button>
          </h2>

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

        <div id="file-grid" class="grid grid-cols-4 gap-4 text-lg font-semibold bg-white shadow p-3 rounded-bot">

          <!-- Existing and uploaded files will appear here -->
        </div>
        <div id="pending-files" class="hidden d-none">
          <h2 class="text-lg font-semibold mb-2">Archivos Pendientes</h2>
          <div id="pending-file-list" class="space-y-2">
            <!-- Los archivos pendientes aparecerán aquí -->
          </div>
        </div>
      </div>
      <div class="col-12 col-md-4 px-4 py-8 note-container-container">
        <h2 class="text-lg font-semibold  documentation-title  bg-white d-flex justify-content-between">Notas
          <button onclick="addNote()" class="new-doc-btn hover-effect flex items-center space-x-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
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
    <div class="row my-2">
      <div class="mx-auto px-4 py-8 file-section-container col-12 col-md-12">
        <h2 class="text-lg font-semibold bg-white  shadow documentation-title d-flex justify-content-between p-5 rounded-top">Inspection
          <button data-toggle="modal" data-target="#uploadModalInspection" class="new-doc-btn hover-effect flex items-center space-x-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            <i class="fas fa-plus"></i>
            <span>Nuevo</span>
          </button>
        </h2>

        <div id="drag-drop-container-inspection"
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

        <div id="file-grid-inspection" class="grid grid-cols-4 gap-4 text-lg font-semibold bg-white shadow p-3 rounded-bot">
        </div>
        <div id="pending-files-inspection hidden" class="mb-4">
          <h2 class="text-lg font-semibold mb-2">Archivos Pendientes</h2>
          <div id="pending-file-list-inspection" class="space-y-2">
            <!-- Los archivos pendientes aparecerán aquí -->
          </div>
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
  <section class="content card px-4 py-3" id="documentation-container">
    <div class="row">
      <div class="col-12 col-md-2 ">
        <button type="button" id="btn-documentacion-factura" class="btn btn-outline-primary btn-block btn-reporte" data-type="html"><i class="fa fa-download"></i>Factura General</button>
      </div>
      <div class="col-12 col-md-2">
        <button type="button" id="btn-documentacion-zip" class="btn btn-outline-primary btn-block btn-reporte" data-type="html"><i class="fa fa-download"></i>Zip</button>
      </div>
      <div class="col-12 col-md-3">
        <button type="button" id="btn-documentacion-new" class="btn btn-outline-primary btn-block btn-reporte" data-type="html"><i class="fa fa-upload"></i> Nuevo documento</button>
      </div>
      <div class="col-12 col-md-3">
      </div>
      <div class="col-12 col-md-2">
        <button type="button" class="btn btn-outline-primary btn-block btn-reporte btn-back-documentacion" data-type="html"><i class="fa fa-arrow-left"></i> </button>
      </div>
    </div>
    <div class="row documentation-files-container
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
              <th>Monto F</th>
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

  <div class="modal fade" id="modal-crear-cotizacion" tabindex="-1" role="dialog" aria-labelledby="modal-cotizacion" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Subir Prospecto</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="form-crear-cotizacion" class="form-horizontal" method="post">
            <div class="row">

              <div class="col-12 col-sm-12">
                <div class="form-group
                  ">
                  <label>Cotizacion <span class="label-advertencia text-danger"> *</span></label>
                  <input type="file" id="txt-Cotizacion" name="cotizacion" required class="form-control input-report required">
                  <span class="invalid-feedback" id="error-volumen">La cotización es requerida</span>

                </div>
              </div>
            </div>
          </form>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            <button type="button" id="btn-actualizar-cotizacion" class="btn btn-primary">Actualizar</button>
            <button type="button" id="btn-guardar-cotizacion" class="btn btn-primary">Guardar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modal-crear" tabindex="-1" role="dialog" aria-labelledby="modal-crear" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
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
                <label>Mes <span class="label-advertencia text-danger"> *</span></label>
                <div class="form-group">
                  <select id="txt-Mes" required name="mes" class="form-control input-report required">
                  </select>
                  <span class="invalid-feedback" id="error-mes">El mes es requerido</span>
                </div>
              </div>
              <div class="col-6 col-sm-6">
                <div class="form-group
                  ">
                  <label>F. Cierre <span class="label-advertencia text-danger"> *</span></label>
                  <input type="text" name="f_cierre" required id="txt-Fe_Cierre" class="form-control input-report required input-date">
                  <span class="invalid-feedback" id="error-f-cierre">La fecha de Cierre es requerida</span>
                </div>
              </div>
              <div class="col-6 col-sm-6">
                <div class="form-group">
                  <label>Pais <span class="label-advertencia text-danger"> *</span></label>
                  <select id="txt-ID_Pais" required name="id_pais" class="form-control input-report required">

                  </select>
                  <!-- <span class="help-block text-danger" id="error"></span>
                    <input type="hidden" id="txt-ID_Carga_Consolidada" name="id" value="0"> -->
                </div>
              </div>
              <div class="col-6 col-sm-6">
                <div class="form-group
                  ">
                  <label>F. Arribo <span class="label-advertencia text-danger"> *</span></label>
                  <input type="text" name="f_puerto" required id="txt-Fe_Puerto" class="form-control input-report required input-date">
                  <span class="invalid-feedback" id="error-f-puerto">La fecha Arribo es requerida</span>
                </div>
              </div>
              <div class="col-6 col-sm-6">
                <div class="form-group">
                  <label>Carga <span class="label-advertencia text-danger"> *</span></label>
                  <select type="text" id="txt-No_Carga" required name="carga" class="form-control input-report required">
                  </select>
                  <span class="invalid-feedback" id="error-carga">La carga es requerida</span>
                </div>
              </div>


              <div class="col-6 col-sm-6">
                <div class="form-group" id="div-Fe_Entrega">
                  <label>F. Entrega <span class="label-advertencia text-danger"> *</span></label>
                  <input type="text" required name="f_entrega" id="txt-Fe_Entrega" class="form-control input-report required input-date">
                  <span class="invalid-feedback" id="error-f-entrega">La fecha entrega es requerida</span>
                </div>
              </div>
              <div class="col-6 col-sm-6">
                <div class="form-group ">
                  <label>Empresa <span class="label-advertencia text-danger"> *</span></label>
                  <input type="text" required name="empresa" id="txt-Empresa" class="form-control input-report required">
                  <span class="invalid-feedback" id="error-empresa">La empresa es requerida</span>
                </div>
              </div>
            </div>
        </div>
        </form>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          <button type="button" id="btn-actualizar" class="btn btn-primary">Actualizar</button>
          <button type="button" id="btn-guardar" class="btn btn-primary">Guardar</button>
        </div>
      </div>
    </div>
  </div>
</div>

<style scoped>
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

  .table-condesed {
    border-spacing: 1em;
  }

  
</style>