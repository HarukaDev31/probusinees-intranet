<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Main content -->
  <section class="section-escenario_mesas">
    <?php
    if ( isset($this->session->userdata['arrDataPersonal']) && $this->session->userdata['arrDataPersonal']['sStatus']=='success' ) { ?>
    <input type="hidden" id="hidden-iIdEscenarioRestaurante" value="<?php echo $iIdEscenarioRestaurante; ?>">
    <div class="row">
      <div class="col-xs-12">
        <div class="col-xs-12">
          <ul class="list-group row div-lista_escenario_mesas">
            <li class="li-item_mesa li-add-item_mesa list-group-item col-xs-6 col-sm-4 col-md-3 col-lg-3 text-center" value="0"><br><br><h2><span class="fa fa-plus"></span> Agregar Mesa</h2></b><br></li>
          </ul>
        </div>
      </div>
    </div>

    <div class="div-footer-escenario_mesas" style="z-index: 1001;">
      <div class="btn-group div-crud-escenario_mesas">
      </div>
    </div>
    
    <!-- Modal -->
    <div class="modal fade" id="modal-crud-escenario_mesas" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
        
          <input type="hidden" name="EID_Escenario_Restaurante" value="">
          <input type="hidden" name="ENo_Escenario_Restaurante" value="">

          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title text-center">Nuevo escenario</h4>
          </div>
          <div class="modal-body">
            <div class="col-md-12">
              <label>Nombre <span class="label-advertencia"> *</span></label>
              <div class="form-group">
                <input type="text" id="txt-No_Escenario_Restaurante" name="No_Escenario_Restaurante" placeholder="Ingresar nombre de escenario" class="form-control" autocomplete="off" maxlength="100">
                <span class="help-block" id="error"></span>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <div class="col-xs-6">
              <button type="button" id="btn-modal-footer-cancel-escenario_mesas" class="btn btn-danger btn-md btn-block pull-left" data-dismiss="modal">Salir</button>
            </div>
            <div class="col-xs-6">
              <button type="button" id="btn-modal-footer-add-escenario_mesas" class="btn btn-success btn-md btn-block">Guardar</button>
            </div>
          </div>
        </div>
      </div>
    </div><!-- Modal add escenario restaurante -->
    
    <!-- Modal all escenarios restaurante -->
    <div class="modal fade" id="modal-all-escenario_mesas" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title text-center">Administrar Escenarios</h4>
          </div>
          <div class="modal-body">
            <div class="table-responsive">
              <table id="table-administrar_escenarios" class="table table-striped table-bordered">
                <thead>
                  <tr>
                    <th class="text-center">Nombre</th>
                    <th></th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>
          <div class="modal-footer">
            <div class="col-xs-12">
              <button type="button" id="btn-modal-footer-cancel-escenario_mesas" class="btn btn-danger btn-md btn-block pull-left" data-dismiss="modal">Salir</button>
            </div>
          </div>
        </div>
      </div>
    </div><!-- Modal all escenarios restaurante -->

    <!-- Modal Crud Mesa -->
    <div class="modal fade" id="modal-add-mesas" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <input type="hidden" name="EID_Mesa_Restaurante" value="">
          <input type="hidden" name="ENo_Mesa_Restaurante" value="">

          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title text-center">Nueva Mesa</h4>
          </div>

          <div class="modal-body">
            <div class="col-xs-12 div-escenario">
              <label title="Escenario para mesas">Escenario <span class="label-advertencia">*</span></label>
              <div class="form-group">
                <select id="cbo-escenario" name="ID_Escenario_Restaurante" title="Escenario para mesas" class="form-control select2 required" style="width: 100%;"></select>
                <span class="help-block" id="error"></span>
              </div>
            </div>
            
            <div class="col-xs-12 div-escenario-no_creado">
              <label data-toggle="tooltip" data-placement="bottom" title="También lo puedes realizar en la parte inferior derecha y clic en agregar">Primero debes crear al menos 1 escenario <span class="label-advertencia">*</span></label>
              <div class="form-group">
                <button type="button" class="btn btn-success btn-block btn-add-escenario_mesas" data-toggle="modal" data-target="#modal-add-escenario_mesas"  data-toggle="tooltip" data-placement="bottom" title="También lo puedes realizar en la parte inferior derecha y clic en agregar">Agregar escenario</button>
              </div>
            </div>

            <div class="col-xs-12">
              <label>Nombre <span class="label-advertencia"> *</span></label>
              <div class="form-group">
                <input type="text" id="txt-No_Mesa_Restaurante" name="No_Mesa_Restaurante" placeholder="Ingresar nombre de escenario" class="form-control" autocomplete="off" maxlength="100">
                <span class="help-block" id="error"></span>
              </div>
            </div>
            
            <div class="col-xs-12">
              <div class="form-group">
                <label>Estado <span class="label-advertencia">*</span></label>
                <select id="cbo-estado" name="Nu_Estado" class="form-control required" style="width: 100%;"></select>
                <span class="help-block" id="error"></span>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <div class="col-xs-6">
              <button type="button" id="btn-modal-footer-cancel-mesas" class="btn btn-danger btn-md btn-block pull-left" data-dismiss="modal">Salir</button>
            </div>
            <div class="col-xs-6">
              <button type="button" id="btn-modal-footer-add-mesas" class="btn btn-success btn-md btn-block">Guardar</button>
            </div>
          </div>
        </div>
      </div>
    </div><!-- Modal add mesa en escenario restaurante -->
    <?php } else { ?>
      <div class="col-xs-12">
        <h3><span class="label label-danger">La caja esta cerrada</span><br>Para abrir nuestra caja ir a la opción:<br>Punto de venta > Apertura de Caja</h3>
      </div>
    <?php } ?>
  </section>
</div>
<!-- /.content-wrapper -->