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
   <section class="content ">
    
    <div class="container-fluid table-almacen">
    <div class="row">
      <div class="col-6 col-sm-3">
                  <label>F. Inicio <span class="label-advertencia text-danger"> *</span></label>
                  <div class="form-group">
                    <input type="text" id="txt-Fe_Inicio" class="form-control input-report required" value="<?php echo dateNow('month_date_ini_report'); ?>">
                    <span class="help-block text-danger" id="error"></span>
                  </div>
                </div>
                <div class="col-6 col-sm-3">
                  <label>F. Fin <span class="label-advertencia text-danger"> *</span></label>
                  <div class="form-group">
                    <input type="text" id="txt-Fe_Fin" class="form-control input-report required" value="<?php echo dateNow('fecha_actual_dmy'); ?>">
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
                <div class="col-6 col-sm-3">
                  <label>&nbsp;</label>
                  <button type="button" id="btn-html_reporte" class="btn btn-primary btn-block btn-reporte" data-type="html"><i class="fa fa-search"></i> Buscar</button>
                </div>
      </div>
      <div class="table-responsive div-Listar">
        <table id="table-almacen" class="table table-bordered table-hover table-striped">
          <thead class="thead-light">
            <tr>
              <th>País </th>
              <th>Fecha</th>
              <th>Cliente</th>
              <th>Empresa</th>
              <th>N° Orden</th>
              <th>Ver</th>
              <th>Estado</th>
             
            </tr>
          </thead>
        </table>
      </div>
    </div>
    </section>
    <section class="content table-inspection">
      <div class="container-fluid">
        <div class="row mb-3">
          <div class="col-sm-12 col-md-3">
            <div class="input-group mb-3">
              <span class="input-group-text">Total Box</span>
              <input type="text" id="total-box"
              class="form-control" disabled aria-label="Dollar amount (with dot and two decimal places)">
            </div>
          </div>
          <div class="col-sm-12 col-md-3">
            <div class="input-group mb-3">
              <span class="input-group-text">Total CBM</span>
              <input type="text"
              id="total-cbm"
              class="form-control"  disabled aria-label="Dollar amount (with dot and two decimal places)">
            </div>
          </div>
          <div class="col-sm-12 col-md-3">
            <div class="input-group mb-3">
              <span class="input-group-text">Total KG</span>
              <input type="text" class="form-control" 
              id="total-kg"
              disabled
              aria-label="Dollar amount (with dot and two decimal places)">
            </div>
        </div>
        <div class="table-responsive div-Listar">
          <table id="table-inspection" class="table table-bordered table-hover table-striped">
            <thead class="thead-light">
              <tr>
                <th>Imagen </th>
                <th>Nombre Producto</th>
                <th>Cantidad Total</th>
                <th>Total Box</th>
                <th>Total CBM</th>
                <th>Total KG</th>
                <th>Fotos</th>
                <th>Estado</th>
                <th>Notas</th>
              
              </tr>
            </thead>
          </table>
          <div class="row mt-3 action-buttons">
            <div class="col-sm-12 col-md-6 d-flex flex-row align-items-center justify-content-center">
              <button class="btn btn-primary" id="btn-save-inspection">Guardar</button>
            </div>
            <div class="col-sm-12 col-md-6 d-flex flex-row align-items-center justify-content-center">
              <button class="btn btn-success" id="btn-back-inspection">Atras</button>
            </div>
        </div>
      </div>
    </section>
    <section class="min-h-screen bg-white main-container" id="drive-container">
      <header class="sticky top-0 z-10 bg-white border-b">
      <div class="container mx-auto px-4 py-2 w-100 flex items-center justify-between ">
          <!--subir archivo y retroceder-->
              <div class="flex items-center space-x-4">
                  <div class="p-2 btn btn-dark"
                  id="upload-btn"
                  >
                      <!-- icon to upload files -->
                      <i class="fas fa-cloud-upload-alt text-white-500"></i>
                      <span class="ml-2">Subir Archivo</span>
                  </div>
                  <div class="p-2 btn btn-dark"
                  id="back-btn"
                  >
                      <!-- icon to back to previous folder -->
                      <i class="fas fa-arrow-left text-white-500"></i>
                      <span class="ml-2">Atras</span>
                  </div>
                  </div>
          </div>
          <div class="container mx-auto px-4 py-2 flex items-center justify-between ">
          <input type="file" class="form-control-file d-none" id="file-input" accept="image/*,.mp4,
          .mp3,.avi,.mkv,.mov,.flv,.wmv,.wma,.wav,.ogg,.aac,.jpg,.jpeg,.png,.gif,.svg,.bmp,.tiff,.tif,.webp" multiple>
    
          <h1 class="text-xl font-semibold text-gray-800">Archivos</h1>
              <div class="flex items-center space-x-4">
                  <div class="relative">
                      <input type="text" placeholder="Buscar en Archivos" id="search-input"
                          class="pl-8 pr-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
                      <i class="fas fa-search absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                  </div>
                  <button class="p-2 rounded-full hover:bg-gray-100"
                  id="back-btn"
                  >
                      <!-- icon to back to previous folder -->
                      <i class="fas fa-arrow-left text-gray-500"></i>
                  </button>
              </div>
          </div>
      </header>

      <div class="container mx-auto px-4 py-8 file-section-container">
          <div id="drag-drop-container"
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

          <div id="file-grid" class="grid grid-cols-4 gap-4">

              <!-- Existing and uploaded files will appear here -->
          </div>
          <div id="pending-files" class="mb-4">
              <h2 class="text-lg font-semibold mb-2">Archivos Pendientes</h2>
              <div id="pending-file-list" class="space-y-2">
                  <!-- Los archivos pendientes aparecerán aquí -->
              </div>
          </div>
      </div>
    </section>
</div>
<style >
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
            height: 85vh;
            position: relative;

        }

        .file-list {
            position: sticky;
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
            color:#5dade2;
        }

        #pending-files {
            background-color: #fff8e1;
            border: 1px solid #ffe082;
            padding: 1em;
            border-radius: 0.5em;
            position: absolute;
            bottom: 1em;
            right: 1em;
            max-height: 80%;
            overflow-y: auto;
        }

        .progress-circle circle {
            transition: stroke-dashoffset 0.3s ease;
        }

        #pending-file-list .file-item {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        #pending-file-list .file-name {
            flex-grow: 1;
            padding-left: 10px;
            font-weight: 600;
        }

        #pending-file-list .text-sm {
            color: #666;
        }
        .file-section-container{
          width: 100%;
          height: 85vh;
          position: relative;
        }#file-grid{
          height: 100%;
          width: 100%;
          gap: 1rem;
          overflow-y: auto;
        }
        .group {
          height: 300px;
          width: 100%;
        } 


</style>