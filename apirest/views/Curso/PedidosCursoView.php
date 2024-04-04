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
              
              <div class="row mb-3">
                <div class="col-6 col-sm-3">
                  <label>F. Inicio</label>
                  <div class="form-group">
                    <input type="text" id="txt-Fe_Inicio" class="form-control input-report required" value="<?php echo dateNow('month_date_ini_report'); ?>">
                    <span class="help-block text-danger" id="error"></span>
                  </div>
                </div>
                <div class="col-6 col-sm-3">
                  <label>F. Fin</label>
                  <div class="form-group">
                    <input type="text" id="txt-Fe_Fin" class="form-control input-report required" value="<?php echo dateNow('fecha_actual_dmy'); ?>">
                    <span class="help-block text-danger" id="error"></span>
                  </div>
                </div>
                
                <div class="col-6 col-sm-3">
                  <label>Pago</label>
                  <div class="form-group">
                    <select id="cbo-filtro-estado_pago" name="" class="form-control required" style="width: 100%;">
                      <option value="0" selected="selected">Todos</option>
                      <option value="1">Pendiente</option>
                      <option value="2">Confirmado</option>
                      <option value="3">Finalizado</option>
                      <option value="4">Rechazado</option>
                    </select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>

                <div class="col-6 col-sm-3">
                  <label>&nbsp;</label>
                  <button type="button" id="btn-html_reporte" class="btn btn-primary btn-block btn-reporte" data-type="html"><i class="fa fa-search"></i> Buscar</button>
                </div>
              </div>

              <div class="table-responsive div-Listar">
                <table id="table-Pedidos" class="table table-bordered table-hover table-striped">
                  <thead class="thead-light">
                    <tr>
                      <th>Pedido</th>
                      <th>Fecha</th>
                      <th>Cliente</th>
                      <th>Total</th>
                      <th>Estado Pago</th>
                      <th>Estado Curso</th>
                      <th>Moodle</th>
                      <th>Ref. Pago</th>
                      <th>Compartir</th>
                      <th>Celular</th>
                      <th>T.D.I</th>
                      <th>Nro. Doc. Ident.</th>
                      <th>Nombres y Apellidos</th>
                      <th>F. Nacimiento</th>
                      <th>Sexo</th>
                      <th>Red Social</th>
                      <th>País</th>
                      <th>Departamento</th>
                      <th>Provincia</th>
                      <th>Distrito</th>
                    </tr>
                  </thead>
                </table>
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
</div>
<!-- /.content-wrapper -->