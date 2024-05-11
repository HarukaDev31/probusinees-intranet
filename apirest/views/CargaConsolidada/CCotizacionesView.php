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
                  <input type="hidden" id="txt-EID_Pedido_Cabecera" name="EID_Pedido_Cabecera" class="form-control required">
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
                  <div class="row div-CotizacionBody">
                      <div class="col-12">
                        <div class="row"><div class="col-12 col-sm-3 col-md-6 col-lg-8"><label>Proveedor 1</label></div>
                          <div class="col-12 col-sm-9 col-md-6 col-lg-4">
                            <div class="row d-flex">
                              <div class="form-group">
                                  <input disabled="true" type="text" name="No_Carga_Consolidada" class="form-control required" placeholder="Ingresar" maxlength="100" autocomplete="off">
                                  <span class="help-block text-danger" id="error"></span>
                              </div>
                              <div class="form-group">
                                  <input disabled="true" type="text" name="No_Carga_Consolidada" class="form-control required" placeholder="Ingresar" maxlength="100" autocomplete="off">
                                  <span class="help-block text-danger" id="error"></span>
                              </div >
                          </div>
                        </div>
                      </div>
                      <div class="col-12">
                        <div class="row">
                          <div class="col-12 col-md-6"><label>Producto 1</label></div>
                          <div class="col-12 col-md-3"><label>Informacion de Productos</label></div>
                          <div class="col-12 col-md-3"><label>Tributos</label></div>
                        </div>
                          <div class="row">
                            <div class="col-12 col-md-6">
                              <label>Img</label>
                              <div class="form-group">
                                    <input disabled="true" type="text" name="No_Carga_Consolidada" class="form-control required" placeholder="Ingresar" maxlength="100" autocomplete="off">
                                    <span class="help-block text-danger" id="error"></span>
                              </div>
                            </div>
                            <div class="col-12 col-md-3">
                              <div class="form-group">
                                    <input disabled="true" type="text" name="No_Carga_Consolidada" class="form-control required" placeholder="Ingresar" maxlength="100" autocomplete="off">
                                    <span class="help-block text-danger" id="error"></span>
                              </div>
                              <div class="form-group">
                                    <input disabled="true" type="text" name="No_Carga_Consolidada" class="form-control required" placeholder="Ingresar" maxlength="100" autocomplete="off">
                                    <span class="help-block text-danger" id="error"></span>
                              </div>
                              <div class="form-group">
                                    <input disabled="true" type="text" name="No_Carga_Consolidada" class="form-control required" placeholder="Ingresar" maxlength="100" autocomplete="off">
                                    <span class="help-block text-danger" id="error"></span>
                              </div>
                              <div class="form-group">
                                    <input disabled="true" type="text" name="No_Carga_Consolidada" class="form-control required" placeholder="Ingresar" maxlength="100" autocomplete="off">
                                    <span class="help-block text-danger" id="error"></span>
                              </div>
                            </div>
                            <div class="col-12 col-md-3">
                            <button type="button" class="btn btn-primary">Ver Tributo</button>
                            </div>
                          </div>
                        </div>
                      </div>

                <?php echo form_close(); ?>
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
<div class="modal fade modal-enviar_mensaje" id="modal-enviar_mensaje">
  <?php $attributes = array('id' => 'form-enviar_mensaje');
echo form_open('', $attributes);?>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body" id="modal-body-enviar_mensaje">
        <input type="hidden" id="enviar_mensaje-id_pedido_cabecera" name="enviar_mensaje-id_pedido_cabecera" class="form-control" autocomplete="off">
        <input type="hidden" id="enviar_mensaje-id_entidad" name="enviar_mensaje-id_entidad" class="form-control" autocomplete="off">

        <div class="col-12 text-left">
          <label>Mensaje</label>
          <div class="form-group">
            <textarea class="form-control required" rows="3" name="enviar_mensaje-No_Seguimiento" placeholder="Escribir..."></textarea>
            <span class="help-block text-danger" id="error"></span>
          </div>
        </div>

        <div class="col-12 text-left">
          <label>Ajuste</label>
          <div class="form-group">
            <input type="text" id="enviar_mensaje-Ss_Total" inputmode="decimal" name="enviar_mensaje-Ss_Total" placeholder="Obligatorio" class="form-control required input-decimal" maxlength="20" autocomplete="off">
            <span class="help-block text-danger" id="error"></span>
          </div>
        </div>

        <div class="col-12 text-left">
          <label>Observaciones</label>
          <div class="form-group">
            <textarea class="form-control required" rows="3" name="enviar_mensaje-Txt_Nota" placeholder="Opcional"></textarea>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="col btn btn-danger btn-lg btn-block" data-dismiss="modal">Salir</button>
        <button type="submit" id="btn-enviar_mensaje" class="col btn btn-success btn-lg btn-block">Guardar</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div>
<!-- /.modal imagen del item -->