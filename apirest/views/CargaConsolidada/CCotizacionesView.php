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
                <div class="col-2 col-md-3">
                  <label>F. Inicio <span class="label-advertencia text-danger"> *</span></label>
                  <div class="form-group">
                    <input type="text" id="txt-Fe_Inicio" class="form-control input-report required" value="<?php echo dateNow('month_date_ini_report'); ?>">
                    <span class="help-block text-danger" id="error"></span>
                  </div>
                </div>

                <div class="col-2 col-sm-3">
                  <label>F. Fin <span class="label-advertencia text-danger"> *</span></label>
                  <div class="form-group">
                    <input type="text" id="txt-Fe_Fin" class="form-control input-report required" value="<?php echo dateNow('fecha_actual_dmy'); ?>">
                    <span class="help-block text-danger" id="error"></span>
                  </div>
                </div>

                <div class="col-2 col-sm-3  ">
                  <label>&nbsp;</label>
                  <button type="button" id="btn-html_reporte" class="btn btn-primary btn-block btn-reporte" data-type="html"><i class="fa fa-search"></i> Buscar</button>
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
                      <th>Pedido</th>
                      <th>Fecha</th>
                      <th>Cliente</th>
                      <th>Empresa</th>
                      <th>Cotizacion</th>
                      <th>Tipo de Cliente</th>
                      <th>Ver</th>
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
                            <input disabled="true" type="text" name="No_Carga_Consolidada" class="form-control required" placeholder="Ingresar" maxlength="100" autocomplete="off">
                            <span class="help-block text-danger" id="error"></span>
                          </div>
                          <div class="form-group">
                            <input disabled="true" type="text" name="No_Carga_Consolidada" class="form-control required" placeholder="Ingresar" maxlength="100" autocomplete="off">
                            <span class="help-block text-danger" id="error"></span>
                          </div>
                        </div>

                        <div class="col-12 col-md-5">
                        <label>Empresa </label>
                          <div class="form-group">
                            <input disabled="true" type="text" name="No_Carga_Consolidada" class="form-control required" placeholder="Ingresar" maxlength="100" autocomplete="off">
                            <span class="help-block text-danger" id="error"></span>
                          </div>
                          <div class="form-group">
                            <input disabled="true" type="text" name="No_Carga_Consolidada" class="form-control required" placeholder="Ingresar" maxlength="100" autocomplete="off">
                            <span class="help-block text-danger" id="error"></span>
                          </div>
                        </div>
                        </div>

                    </div>
                  </div>
                  <div class="row div-CotizacionBody" id="div-CotizacionBody">
                <?php echo form_close(); ?>
              </div>
              <div id="div-footer">
                <button type="button" class="btn btn-success" onclick="guardaryCambiarEstado()">Marcar Como Cotizado</button>
                <button type="button" class="btn btn-primary" onclick="guardarCotizacion()">Guardar </button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
  </section>
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
              <input type="text" value="0" id="ad-valorem"class="form-control" placeholder="Ad-Valorem" aria-label="ad-valorem" aria-describedby="ad-valorem-label">
            </div>
          </div>
          <div class="form-group">
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <label class="input-group-text" id="igv-label">IGV %</label>
              </div>
              <input type="text" value="16" id="igv"class="form-control" placeholder="IGV" aria-label="igv" aria-describedby="igv-label">
            </div>
          </div>
          <div class="form-group">
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <label class="input-group-text" id="ipm-label">IPM %</label>
              </div>
              <input type="text" value="2" id="ipm"class="form-control" placeholder="ipm" aria-label="ipm" aria-describedby="ipm-label">
            </div>
          </div>
          <div class="form-group">
            <div class="input-group mb-3">
            <div class="input-group-prepend">
              <label class="input-group-text" for="Percepcion">PERCEPCION %</label>
            </div>
            <select class="custom-select" id="percepcion">
              <option value="0">0</option>
              <option value="3.5" selected>3.5</option>
              <option value="5">5</option>
            </select>
            </div>
          </div>
          <div class="form-group">
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <label class="input-group-text" id="valoracion-label">VALORACION</label>
              </div>
              <input type="text"  value="0"   id="valoracion" class="form-control" placeholder="valoracion" aria-label="valoracion" aria-describedby="valoracion-label">
            </div>
          </div>
          <div class="form-group">
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <label class="input-group-text" id="antidumping-label">ANTIDUMPING</label>
              </div>
              <input type="text" value="0" id="antidumping"class="form-control" placeholder="antidumping" aria-label="antidumping" aria-describedby="antidumping-label">
            </div>
          </div>
          <!--Percepcion select-->
          
          
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" onclick="guardarTributos()" class="btn btn-primary">Guardar</button>
      </div>
    </div>
  </div>
</div>
</div>

