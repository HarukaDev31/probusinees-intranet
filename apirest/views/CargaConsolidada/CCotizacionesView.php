<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-8">
          <h1>
            <i class="<?php echo $this->MenuModel->verificarAccesoMenuCRUD()->Txt_Css_Icons; ?>" aria-hidden="true"></i> <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?>
            &nbsp;<span id="span-id_pedido" class="badge badge-primary"></span>
          </h1>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>
<?php //array_debug($this->user); ?>
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
          	  <input type="hidden" id="hidden-sMethod" name="sMethod" class="form-control" value="<?php echo $this->router->method; ?>">

              <div class="row mb-3 div-Listar">
                <div class="col-6 col-md-3">
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

                <div class="col-12 col-sm-3  ">
                  <label>&nbsp;</label>
                  <button type="button" id="btn-html_reporte" class="btn btn-primary btn-block btn-reporte" data-type="html"><i class="fa fa-search"></i> Buscar</button>
                </div>
                <div class="col-12 col-sm-3  ">
                  <label>&nbsp;</label>
                  <button type="button" id="btn-upload-excel" class="btn btn-primary btn-block btn-reporte" data-toggle="modal" data-target="#modal-upload-excel"><i class="fa fa-upload"></i> Subir Excel</button>

                </div>


                <!-- <div class="col-6 col-sm-2">
                  <label>&nbsp;</label>
                  <button type="button" class="btn btn-success btn-block" onclick="23+++0  ytfccxvbnm,.-()"><i class="fa fa-plus-circle"></i> Crear</button>
                </div> -->
              </div>

              <div class="table-responsive div-Listar">
                <table id="table-CCotizaciones" class="table table-bordered table-hover table-striped">
                  <thead class="thead-light">
                    <tr>
                      <th>N° Pedido</th>
                      <th>Fecha Unix</th>
                      <th>Fecha</th>
                      <th>Cliente</th>
                      <th>Telefono</th>
                      <th>Empresa</th>
                      <th>Tipo de Cliente</th>
                      <th>Descargar</th>
                      <th>Ver</th>
                      <th>Estado</th>
                      <th>Estado_ID</th>
                    </tr>
                  </thead>
                </table>
              </div>

              <div class="box-body div-AgregarEditar">
                <?php
                  $attributes = array('id' => 'form-pedido');
                  echo form_open('', $attributes);
                  ?>
                  <div class="row div-CotizacionHeader">
                    <div class="col-12 col-md-9">
                        <div class="row">
                        <div class="col-12 col-md-7">
                        <label>Cliente </label>
                          <div class="form-group">
                            <label>Nombre</label>
                            <input id="Nombre" disabled="true" type="text" name="No_Carga_Consolidada" class="form-control required" placeholder="Ingresar" maxlength="100" autocomplete="off">
                            <span class="help-block text-danger" id="error"></span>
                          </div>
                          <div class="form-group">
                          <label>CBM Total</label>

                            <input  id="CBM_Total" disabled="true" type="text" name="No_Carga_Consolidada" class="form-control required" placeholder="Ingresar" maxlength="100" autocomplete="off">
                            <span class="help-block text-danger" id="error"></span>
                          </div>
                        </div>

                        <div class="col-12 col-md-5">
                        <label>Empresa </label>
                          <div class="form-group">
                          <label>Nombre de Empresa</label>

                            <input id="Empresa" disabled="true" type="text" name="No_Carga_Consolidada" class="form-control required" placeholder="Ingresar" maxlength="100" autocomplete="off">
                            <span class="help-block text-danger" id="error"></span>
                          </div>
                          <div class="form-group">
                          <label>Peso Total</label>

                            <input id="Peso_Total" disabled="true" type="text" name="No_Carga_Consolidada" class="form-control required" placeholder="Ingresar" maxlength="100" autocomplete="off">
                            <span class="help-block text-danger" id="error"></span>
                          </div>
                        </div>
                        </div>

                    </div>
                    <div class="col-12 col-md-3 d-flex flex-column d-flex align-items-center justify-content-center">
                      <label>Estado</label>
                      <div class="form-group ">
                      <button type="button" class="btn btn-primary" onclick="agregarProveedor()">Agregar Proveedor </button>
                      </div>
                    <div class="form-group ">
                      <!--select with nuevo antiguo options with value 1 or 2-->
                      <select class="form-control" id="selectEstadoBody" name="selectEstado" onchange="updateTipoCliente(this)">
                        <option value="1">Nuevo</option>
                        <option value="2">Antiguo</option>
                        <option value="3">Socio</option>
                      </select>
                      </div>
                    </div>
                  </div>
                  <div class="row div-CotizacionBody" id="div-CotizacionBody">
                  </div>
                <?php echo form_close(); ?>
              </div>
              <div id="div-footer">
                <!-- <button type="button" class="btn btn-success" onclick="guardaryCambiarEstado()">Marcar Como Cotizado</button> -->
                <button id="button-save"  type="button" class="btn btn-primary"  onclick="guardarYSalir()">Guardar y Salir </button>
                <button id="button-save-excel"  type="button" class="btn btn-success"  onclick="guardarCotizacionYDescargar()">Guardar y Generar Excel </button>

              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
  </section>
  <div id="loading-spinner"class="spinner-backdrop">
  <div class="spinner">
    <div class="spinner-border text-primary" role="status">
      <span class="sr-only">Loading...</span>
    </div>
  </div>
</div>
<style>
  .spinner-backdrop {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1050;
    display: flex;
    justify-content: center;
    align-items: center;
  }
</style>
</div>
  <!-- /.content -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Tributos del Producto {
        Nombre del producto} del Proveedor {Nombre del proveedor}
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form>
          <div class="form-group">
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <label class="input-group-text" id="ad-valorem-label">AD VALOREM (%)</label>
              </div>
              <input type="number" value="0" id="ad-valorem"class="form-control" placeholder="Ad-Valorem" aria-label="ad-valorem" aria-describedby="ad-valorem-label">
            </div>
          </div>
          <div class="form-group">
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <label class="input-group-text" id="igv-label">IGV %</label>
              </div>
              <input type="number" value="16" id="igv"class="form-control" placeholder="IGV" aria-label="igv" aria-describedby="igv-label">
            </div>
          </div>
          <div class="form-group">
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <label class="input-group-text" id="ipm-label">IPM %</label>
              </div>
              <input type="number" value="2" id="ipm"class="form-control" placeholder="ipm" aria-label="ipm" aria-describedby="ipm-label">
            </div>
          </div>
          <div class="form-group">
            <div class="input-group mb-3">
            <div class="input-group-prepend">
              <label class="input-group-text" for="Percepcion">PERCEPCION %</label>
            </div>
            <select class="custom-select" id="percepcion">
              <option value="0.00">0</option>
              <option value="3.50" selected>3.5</option>
              <option value="5.00">5</option>
            </select>
            </div>
          </div>
          <div class="form-group">
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <label class="input-group-text" id="valoracion-label">VALORACION</label>
              </div>
              <input type="number"  value="0"   id="valoracion" class="form-control" placeholder="valoracion" aria-label="valoracion" aria-describedby="valoracion-label">
            </div>
          </div>
          <div class="form-group">
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <label class="input-group-text" id="antidumping-label">ANTIDUMPING</label>
              </div>
              <input type="number" value="0" id="antidumping"class="form-control" placeholder="antidumping" aria-label="antidumping" aria-describedby="antidumping-label">
            </div>
          </div>
          <!--Percepcion select-->


        </form>
      </div>
      <div class="modal-footer">
        <button type="button" onclick="guardarTributos(this)" class="btn btn-primary">Guardar</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal  with input file and button upload-->
<div class="modal fade" id="modal-upload-excel" tabindex="-1" role="dialog" aria-labelledby="modal-upload-excel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title mr-1" id="modal-upload-excel">Subir Cotizacion Final</h5>
          <a href="<?php echo base_url('assets/downloads/Massive_Payroll.xlsx'); ?>" class="btn btn-primary" aria-hidden="true" title="Descargar Plantilla" download>
          <!--i with download icon and hover text-->
          <i class="fas fa-download" "></i>
      </a>

        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="form-upload-excel" enctype="multipart/form-data">
          <div class="form-group">
            <label for="file-upload-excel">Subir Archivo Excel</label>
            <!--download template button-->
            <input type="file" class="form-control-file" id="file-upload-excel" name="file-upload-excel" accept=".xls,.xlsx">
          </div>
          <div  class="form-group">
            <!--date picker for date of the cotizacion-->
            <label for="date-cotizacion">Fecha de Vencimiento</label>
            <input type="date" class="form-control" id="date-cotizacion" name="date-cotizacion" value="">
          </div>
          <div class="form-group " id="tarifas-container">
            <div class="row mx-auto w-100" id="headers">
              <div class="col-3 col-lg-1">
                <span>Rangos</span>
              </div>
              <div class="col-3 text-center">
                <span >Nuevo</span>
              </div>
              <div class="col-3 text-center">
                <span >Antiguo</span>
              </div>
              <div class="col-3 text-center">
                <span >Socio</span>
              </div>
            </div>
            <div class="row" id="tarifas-1">
              <div class="col-3 col-lg-1">
                <span>0-0.59</span>
              </div>
              <div class="col-3">
                <input type="number" min="0" class="form-control tarifa-nuevo" id="tarifa-nuevo-1" name="nuevo-1" value="0">
                <div class="text-danger d-none" id="error-tarifa-nuevo-1" >
                  Debe ser mayor a 0
                </div>
              </div>
              <div class="col-3">
                <input type="number" min="0" class="form-control tarifa-antiguo" id="tarifa-antiguo-1" name="antiguo-1" value="0">
                <div class="text-danger d-none" id="error-tarifa-antiguo-1" >
                  Debe ser mayor a 0
                </div>
              </div>
              <div class="col-3">
                <input type="number" min="0" class="form-control tarifa-socio" id="tarifa-socio-1" name="socio-1" value="0">
                <div class="text-danger d-none" id="error-tarifa-socio-1" >
                  Debe ser mayor a 0
                </div>
              </div>
            </div>
            <div class="row" id="tarifas-2">
              <div class="col-3 col-lg-1">
                <span>0.6-1.09</span>
              </div>
              <div class="col-3">
                <input type="number" min="0" class="form-control tarifa-nuevo" id="tarifa-nuevo-2" name="nuevo-2" value="0">
                <div class="text-danger d-none" id="error-tarifa-nuevo-2" >
                  Debe ser mayor a 0
                </div>
              </div>
              <div class="col-3">
                <input type="number" min="0" class="form-control tarifa-antiguo" id="tarifa-antiguo-2" name="antiguo-2" value="0">
                <div class="text-danger d-none" id="error-tarifa-antiguo-2" >
                  Debe ser mayor a 0
                </div>
              </div>
              <div class="col-3">
                <input type="number" min="0" class="form-control tarifa-socio-fake-1" name="socio-1" value="0" disabled>

              </div>
            </div>

            <div class="row" id="tarifas-3">
              <div class="col-3 col-lg-1">
                <span>1.10-2.09</span>
              </div>
              <div class="col-3">
                <input type="number" min="0" class="form-control tarifa-nuevo" id="tarifa-nuevo-3" name="nuevo-3" value="0">
                <div class="text-danger d-none" id="error-tarifa-nuevo-3" >
                  Debe ser mayor a 0
                </div>
              </div>
              <div class="col-3">
                <input type="number" min="0" class="form-control tarifa-antiguo" id="tarifa-antiguo-3" name="antiguo-3" value="0">
                <div class="text-danger d-none" id="error-tarifa-antiguo-3" >
                  Debe ser mayor a 0
                </div>
              </div>
              <div class="col-3">
                <input type="number" min="0" class="form-control tarifa-socio" id="tarifa-socio-2" name="socio-3" value="0" >
                <div class="text-danger d-none" id="error-tarifa-socio-2" >
                  Debe ser mayor a 0
                </div>
              </div>
            </div>
            <div class="row" id="tarifas-4">
              <div class="col-3 col-lg-1">
                <span>2.1-3.09</span>
              </div>
              <div class="col-3">
                <input type="number" min="0" class="form-control tarifa-nuevo" id="tarifa-nuevo-4" name="nuevo-4" value="0">
                <div class="text-danger d-none" id="error-tarifa-nuevo-4" >
                  Debe ser mayor a 0
                </div>
              </div>
              <div class="col-3">
                <input type="number" min="0" class="form-control tarifa-antiguo" id="tarifa-antiguo-4" name="antiguo-4" value="0">
                <div class="text-danger d-none" id="error-tarifa-antiguo-4" >
                  Debe ser mayor a 0
                </div>
              </div>
              <div class="col-3">
                <input type="number" min="0" class="form-control tarifa-socio-fake-2" name="socio-3" value="0" disabled>
          
              </div>
            </div>
            <div class="row" id="tarifas-5">
              <div class="col-3 col-lg-1">
                <span>3.1-4.09</span>
              </div>
              <div class="col-3">
                <input type="number" min="0" class="form-control tarifa-nuevo" id="tarifa-nuevo-5" name="nuevo-5" value="0">
                <div class="text-danger d-none" id="error-tarifa-nuevo-5" >
                  Debe ser mayor a 0
                </div>
              </div>
              <div class="col-3">
                <input type="number" min="0" class="form-control tarifa-antiguo" id="tarifa-antiguo-5" name="antiguo-5" value="0">
                <div class="text-danger d-none" id="error-tarifa-antiguo-5" >
                  Debe ser mayor a 0
                </div>
              </div>
              <div class="col-3">
                <input type="number" min="0" class="form-control tarifa-socio-fake-2"  name="socio-3" value="0" disabled>

              </div>
            </div>
            <div class="row" id="tarifas-5">
              <div class="col-3 col-lg-1">
                <span>+4.1</span>
              </div>
              <div class="col-3">
                <input type="number" min="0" class="form-control tarifa-nuevo" id="tarifa-nuevo-6" name="nuevo-6" value="0">
                <div class="text-danger d-none" id="error-tarifa-nuevo-6" >
                  Debe ser mayor a 0
                </div>
              </div>
              <div class="col-3">
                <input type="number" min="0" class="form-control tarifa-antiguo" id="tarifa-antiguo-6" name="antiguo-6" value="0">
                <div class="text-danger d-none" id="error-tarifa-antiguo-6" >
                  Debe ser mayor a 0
                </div>
              </div>
              <div class="col-3">
                <input type="number" min="0" class="form-control tarifa-socio-fake-2"  name="socio-3" value="0" disabled>
              
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" onclick="uploadExcel()" class="btn btn-primary">Subir</button>
      </div>
    </div>
  </div>
</div>

