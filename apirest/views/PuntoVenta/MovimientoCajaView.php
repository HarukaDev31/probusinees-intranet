<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">  
  <!-- Main content -->
  <section class="content">
    <!-- New box-header -->
    <div class="row">
      <div class="col-xs-12">
        <div class="div-content-header">
          <h3>
            <i class="<?php echo $this->MenuModel->verificarAccesoMenuCRUD()->Txt_Css_Icons; ?>" aria-hidden="true"></i> <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?>
            &nbsp;<a href="<?php echo $this->MenuModel->verificarAccesoMenuCRUD()->Txt_Url_Video; ?>" target="_blank"><i class="fa fa-youtube-play red" aria-hidden="true" title="Video <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?>" alt="Video <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?>"></i></a>
          </h3>
        </div>
      </div>
    </div>
    <!-- ./New box-header -->
    <div class="row">
      <div class="col-xs-12">
        <div class="box box-content">
          <!-- box-header -->
          <div class="box-header box-header-new">
            <div class="row div-Filtros">
              <br>
              <div class="col-xs-6 col-sm-6 col-md-6">
                <div class="form-group">
                  <button type="button" id="btn-salida_movimiento_caja" class="btn btn-danger btn-block"><i class="fa fa-upload"></i> Salida de dinero</button>
                </div>
              </div>

              <div class="col-xs-6 col-sm-6 col-md-6">
                <div class="form-group">
                  <button type="button" id="btn-ingreso_movimiento_caja" class="btn btn-success btn-block"><i class="fa fa-download"></i> Entrada de dinero</button>
                </div>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div id="div-movimiento_caja" class="table-responsive">
            <table id="table-movimiento_caja" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th class="text-center">Tipo</th>
                  <th class="text-center">F. Movimiento</th>
                  <th class="text-center">M</th>
                  <th class="text-right">Total</th>
                  <th class="text-left">Nota</th>
                  <th class="text-center">Imprimir</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
    <?php
    $attributes = array('id' => 'form-movimiento_caja');
    echo form_open('', $attributes);
    ?>
    <!-- Modal ingreso / salida de dinero -->
    <div class="modal fade modal-movimiento_caja" id="modal-default">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h3 id="h3-title" class="text-center"></h3>
          </div>
          <div class="modal-body">
            <div class="row">
              <input type="hidden" id="hidden-id_tipo_operacion_caja" name="ID_Tipo_Operacion_Caja" value="">

              <div class="col-xs-6 col-sm-3 col-md-3">
                <div class="form-group">
                  <label>Moneda</label>
    		  				<select id="cbo-moneda" name="ID_Moneda" class="form-control select2" style="width: 100%;"></select>
                </div>
              </div>

              <div class="col-xs-6 col-sm-3 col-md-3">
                <div class="form-group">
                  <label>Importe</label>
                  <input type="text" inputmode="decimal" id="txt-ss_monto_caja" name="Ss_Total" class="form-control input-decimal hotkey-btn-add_movimiento_caja" maxlength="13" autocomplete="off" placeholder="Ingresar monto">
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-12 col-sm-6 col-md-6">
                <div class="form-group">
                  <label>Nota</label>
                  <textarea name="Txt_Nota" class="form-control hotkey-btn-add_movimiento_caja" rows="1" placeholder="Opcional" maxlength="250"></textarea>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-xs-6 col-sm-6">
                <div class="form-group">
                  <label>&nbsp;</label>
                  <button type="button" id="btn-salir" class="btn btn-danger btn-md btn-block pull-center" data-dismiss="modal">Salir (ESC)</button>
                </div>
              </div>
              <div class="col-xs-6 col-sm-6">
                <div class="form-group">
                  <label>&nbsp;</label>
                  <button type="button" id="btn-guardar_movimiento_caja" class="btn btn-primary btn-md btn-block pull-center">Guardar</button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>
    <!-- /. Modal ingreso o salida de dinero -->
    <?php echo form_close(); ?>
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->