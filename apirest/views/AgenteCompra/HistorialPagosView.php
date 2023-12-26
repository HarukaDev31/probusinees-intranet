<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-8">
          <h1><i class="<?php echo $this->MenuModel->verificarAccesoMenuCRUD()->Txt_Css_Icons; ?>" aria-hidden="true"></i> <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?></h1>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <div class="row mb-3">
                <div class="col-6 col-sm-4">
                  <label>F. Inicio <span class="label-advertencia text-danger"> *</span></label>
                  <div class="form-group">
                    <input type="text" id="txt-Fe_Inicio" class="form-control input-report required" value="<?php echo dateNow('month_date_ini_report'); ?>">
                    <span class="help-block text-danger" id="error"></span>
                  </div>
                </div>
                <div class="col-6 col-sm-4">
                  <label>F. Fin <span class="label-advertencia text-danger"> *</span></label>
                  <div class="form-group">
                    <input type="text" id="txt-Fe_Fin" class="form-control input-report required" value="<?php echo dateNow('fecha_actual_dmy'); ?>">
                    <span class="help-block text-danger" id="error"></span>
                  </div>
                </div>
                <div class="col-6 col-sm-2">
                  <label>&nbsp;</label>
                  <button type="button" id="btn-html_reporte" class="btn btn-primary btn-block btn-reporte" data-type="html"><i class="fa fa-search"></i> Buscar</button>
                </div>
                <div class="col-6 col-sm-2">
                  <label>&nbsp;</label>
                  <button type="button" id="btn-excel_reporte" class="btn btn-success btn-block btn-reporte" data-type="excel"><i class="fa fa-file-excel-o text-white"></i> Excel</button>
                </div>
              </div>

              <div class="table-responsive">
                <table id="table-Cliente" class="table table-bordered table-hover table-striped">
                  <thead class="thead-light">
                    <tr>
                      <th>País</th>
                      <th>ID</th>
                      <th>F. Emisión</th>

                      <th>Garantizado</th>

                      <th>Pais 30%</th>
                      <th>F. Pago 30%</th>
                      <th>Importe 30%</th>
                      <th>Operacion 30%</th>
                      <th>Voucher</th>
                      
                      <th>Pais 70%</th>
                      <th>F. Pago 70%</th>
                      <th>Importe 70%</th>
                      <th>Operacion 70%</th>
                      <th>Voucher</th>
                      
                      <th>Pais Servicio</th>
                      <th>F. Pago Servicio</th>
                      <th>Importe Servicio</th>
                      <th>Operacion Servicio</th>
                      <th>Voucher</th>
                      
                      <th class="no-sort">Perú</th>
                      <th class="no-sort">China</th>
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

<!-- modal ver imagen del item -->
<div class="modal fade modal-ver_item" id="modal-default">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body" id="modal-body-ver_item">
        <div class="col-xs-12 text-center">
          <img class="img-responsive img-fluid" style=" display: block; margin-left: auto; margin-right: auto;" src="">
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="col btn btn-outline-danger btn-lg btn-block pull-center" data-dismiss="modal">Salir</button>
        <a id="a-download_image" class="col btn btn-primary btn-lg btn-block" data-id_item="">Descargar</a>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal imagen del item -->