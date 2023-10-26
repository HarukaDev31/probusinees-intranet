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
            &nbsp;<a href="<?php echo $this->MenuModel->verificarAccesoMenuCRUD()->Txt_Url_Video; ?>" target="_blank"><i class="fa fa-youtube-play red" aria-hidden="true" title="Video <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?>" alt="Video <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?>"></i> <span style="color: #7b7b7b; font-size: 14px;">ver video<span></a>
          </h3>
        </div>
      </div>
    </div>
    <!-- ./New box-header -->

    <!-- Row Lista de POS -->
    <div class="row">
      <div class="col-xs-12">
        <h4 class="hidden" id="h4-verificar_autorizacion_venta"></h4>
        <?php if ($this->empresa->Nu_Lae_Gestion==1) : ?>
        <ul class="list-group row ul-lista_pos"></ul>
        <?php endif; ?>
        <h4 id="h4-msg_punto_venta"></h4>
      </div>
    </div>
    <!-- /. Row -->
    <!-- Modal Personal -->
    <?php
    $attributes = array('id' => 'form-matricula_personal_apertura_caja', 'autocomplete' => 'off');
    echo form_open('', $attributes);
    ?>
    <input id="password" style="display:none" type="password" name="fakepasswordremembered">
    <div class="modal fade modal-personal" id="modal-default">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-body">
            <input type="hidden" id="hidden-id_pos" value="">
            <input type="hidden" id="hidden-id_personal" value="">
            <input type="hidden" id="hidden-id_tipo_operacion_caja" value=""><!-- Apertura de caja -->

            <div class="row">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
                  <label>PIN</label>
                  <span class="" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Clave PIN de la opción Personal > Maestro Personal">
                    <i class="fa fa-info-circle"></i>
                  </span>
                  <div class="form-group">
                    <input type="text" id="tel-nu_documento_identidad_personal" pattern="[0-9]*" inputmode="numeric" class="form-control pwd input-decimal hotkey-btn-add_matricular_personal_apertua_caja" value="" placeholder="Ingresar PIN" maxlength="4" autocomplete="new-password">
                    <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>

                <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
                  <label>Moneda</label>
                  <div class="form-group">
                    <select id="cbo-moneda" class="form-control select2" style="width: 100%;"></select>
                  </div>
                </div>

                <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                  <label>Importe Apertura</label>
                  <span class="hidden-sm hidden-md" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Si la caja inicia sin saldo, digitar 0">
                    <i class="fa fa-info-circle"></i>
                  </span>
                  <div class="form-group">
                    <input type="text" id="txt-ss_apertura_caja" inputmode="decimal" class="form-control input-decimal hotkey-btn-add_matricular_personal_apertua_caja" maxlength="13" autocomplete="off" placeholder="Opcional">
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
              </div>

              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                  <div class="form-group">
                    <label>Nota</label>
                    <textarea name="area-txt_nota_caja" rows="1" class="form-control hotkey-btn-add_matricular_personal_apertua_caja" placeholder="Opcional"></textarea>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-xs-6 col-sm-6">
                <div class="form-group">
                  <label>&nbsp;</label>
                  <button type="button" id="btn-salir" class="btn btn-danger btn-lg btn-block pull-center" data-dismiss="modal">Cancelar</button>
                </div>
              </div>
              <div class="col-xs-6 col-sm-6">
                <div class="form-group">
                  <label>&nbsp;</label>
                  <button type="button" id="btn-save_personal" class="btn btn-primary btn-lg btn-block pull-center">Vender</button>
                </div>
              </div>
            </div>
            <!-- /. Row Input Personal -->
          </div>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>
    <!-- /. Modal Personal -->
    <?php echo form_close(); ?>
    <?php
    $attributes = array('id' => 'form-caja_aperturada', 'autocomplete' => 'off');
    echo form_open('', $attributes);
    ?>
    <!-- Modal Verificar Personal de inicio de caja por PIN -->
    <input id="password2" style="display:none" type="password" name="fakepasswordremembered2">
    <div class="modal fade modal-inicio_sesion_caja_x_personal" id="modal-default">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-body">
            <input type="hidden" id="hidden-id_matricula_empleado" value="">
            <input type="hidden" id="hidden-id_moneda_caja_pos" value="">
            <div class="row">
              <div class="col-xs-4"></div>
              <div class="col-xs-4">
                <label>PIN</label>
                <span class="" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Clave PIN de la opción Personal > Maestro Personal">
                  <i class="fa fa-info-circle"></i>
                </span>
                <div class="form-group">
                  <input type="text" id="tel-Nu_Pin_Caja" pattern="[0-9]*" inputmode="numeric" class="form-control pwd input-number hotkey-btn-add_matricular_personal_apertua_caja" value="" autocomplete="off" placeholder="Ingresar número" maxlength="4">
                  <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              <div class="col-xs-4"></div>

              <div class="col-xs-12">
                <div class="form-group">
                  <button type="button" id="btn-ingresar_punto_venta" class="btn btn-primary btn-lg btn-block pull-center">Vender</button>
                </div>
              </div>
              <div class="col-xs-12">
                <div class="form-group">
                  <button type="button" id="btn-salir" class="btn btn-danger btn-lg btn-block pull-center" data-dismiss="modal">Cancelar</button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>
    <?php echo form_close(); ?>
    <!-- /. Modal Verificar Personal de inicio de caja por PIN -->
  </section>
  <!-- /. Main content -->
</div>
<!-- /.content-wrapper -->