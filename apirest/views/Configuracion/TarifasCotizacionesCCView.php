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
                <table id="table-tarifas" class="table table-bordered table-hover table-striped">
                  <thead class="thead-light">
                    <tr>
                      <th>Tipo de Tarifa</th>
                      <th>Tipo de Cliente</th>
                      <th>Limite Inferior</th>
                      <th>Limite Superior</th>
                      <th>Moneda</th>
                      <th>Tarifa</th>
                      <th>Estado</th>
                      <th>Fecha de Creacion</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                </table>
              </div>

        
              <div id="div-footer">
                <!-- <button type="button" class="btn btn-success" onclick="guardaryCambiarEstado()">Marcar Como Cotizado</button> -->
                <!-- <button id="button-save"  type="button" class="btn btn-primary"  onclick="guardarCotizacion()">Guardar </button> -->
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
<!---add modal with tarifa input and save button-->
<div class="modal fade" id="modal-tarifa" tabindex="-1" role="dialog" aria-labelledby="modal-tarifa" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Actualizar Tarifa</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="hidden-id_tarifa" name="id_tarifa" class="form-control">
        <div class="form-group">
          <label for="txt-tarifa">Tarifa</label>
          <input type="text" id="tarifa" name="tarifa" class="form-control" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="guardarTarifa()">Guardar</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
     </div>
    </div>
  </div>
</div>
</div>

