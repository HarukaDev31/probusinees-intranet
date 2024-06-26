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
                <div class="col-6 col-sm-2">
                  <label>F. Inicio <span class="label-advertencia text-danger"> *</span></label>
                  <div class="form-group">
                    <input type="text" id="txt-Fe_Inicio" class="form-control input-report required" value="<?php echo dateNow('month_date_ini_report'); ?>">
                    <span class="help-block text-danger" id="error"></span>
                  </div>
                </div>

                <div class="col-6 col-sm-2">
                  <label>F. Fin <span class="label-advertencia text-danger"> *</span></label>
                  <div class="form-group">
                    <input type="text" id="txt-Fe_Fin" class="form-control input-report required" value="<?php echo dateNow('fecha_actual_dmy'); ?>">
                    <span class="help-block text-danger" id="error"></span>
                  </div>
                </div>
                
                <div class="col-6 col-sm-4">
                  <label>Consolidado</label>
                  <div class="form-group">
                    <select id="cbo-filtro-ID_Carga_Consolidada" name="filtro-ID_Carga_Consolidada" class="form-control select2" style="width: 100%;">
                      <option value="0">- Todos -</option>
                      <?php
                      //array_debug($arrResponseConsolidado);
                      if($arrResponseConsolidado['status']=='success'){
                        foreach ($arrResponseConsolidado['result'] as $row) {
                          $selected = ($row->id != $ID_Carga_Consolidada ? "" : "selected");
                          ?>
                          <option value="<?php echo $row->id; ?>" <?php echo $selected; ?>><?php echo $row->nombre; ?></option>
                          <?php
                        }
                      }
                      ?>
                    </select>
                  </div>
                </div>

                <div class="col-6 col-sm-2">
                  <label>&nbsp;</label>
                  <button type="button" id="btn-html_reporte" class="btn btn-primary btn-block btn-reporte" data-type="html"><i class="fa fa-search"></i> Buscar</button>
                </div>

                <div class="col-6 col-sm-2">
                  <label>&nbsp;</label>
                  <button type="button" class="btn btn-success btn-block" onclick="agregarPedido()"><i class="fa fa-plus-circle"></i> Crear</button>
                </div>
              </div>

              <div class="table-responsive div-Listar">
                <table id="table-Pedidos" class="table table-bordered table-hover table-striped">
                  <thead class="thead-light">
                    <tr>
                      <th>Nombre</th>
                      <th>Inicio</th>
                      <th>Termino</th>
                      <th>Carga</th>
                      <th>Zarpe</th>
                      <th>Llegada</th>
                      <th>Liberación</th>
                      <th>Canal</th>
                      <th>Entrega</th>
                      <!--<th>Categoría</th>-->
                      <th>Actividad</th>
                      <th>Tarea</th>
                      <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Editar == 1) : ?>
                      <th class="no-sort">Ver</th>
                      <?php endif; ?>
                      <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Eliminar == 1) : ?>
                      <th class="no-sort">Eliminar</th>
                      <?php endif; ?>
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
                  
                  <div class="row">
                    <div class="col-12 col-sm-12">
                      <label>Consolidado</label>
                      <div class="form-group">
                        <select id="cbo-ID_Carga_Consolidada" name="ID_Carga_Consolidada" class="form-control select2" style="width: 100%;"></select>
                      </div>
                    </div>

                    <div class="col-6 col-sm-2">
                      <label>F. Inicio <span class="label-advertencia text-danger"> *</span></label>
                      <div class="form-group">
                        <input type="text" id="modal-Fe_Inicio" name="Fe_Inicio" class="form-control input-datepicker-pay required" value="<?php echo dateNow('fecha_actual_dmy'); ?>">
                        <span class="help-block text-danger" id="error"></span>
                      </div>
                    </div>
                    
                    <div class="col-6 col-sm-2">
                      <label>F. Termino <span class="label-advertencia text-danger"> *</span></label>
                      <div class="form-group">
                        <input type="text" id="modal-Fe_Termino" name="Fe_Termino" class="form-control input-datepicker-pay required" value="<?php echo dateNow('fecha_actual_dmy'); ?>">
                        <span class="help-block text-danger" id="error"></span>
                      </div>
                    </div>
                    
                    <div class="col-6 col-sm-2">
                      <label>F. Carga <span class="label-advertencia text-danger"> *</span></label>
                      <div class="form-group">
                        <input type="text" id="modal-Fe_Carga" name="Fe_Carga" class="form-control input-datepicker-pay required" value="<?php echo dateNow('fecha_actual_dmy'); ?>">
                        <span class="help-block text-danger" id="error"></span>
                      </div>
                    </div>
                    
                    <div class="col-6 col-sm-2">
                      <label>F. Zarpe <span class="label-advertencia text-danger"> *</span></label>
                      <div class="form-group">
                        <input type="text" id="modal-Fe_Zarpe" name="Fe_Zarpe" class="form-control input-datepicker-pay required" value="<?php echo dateNow('fecha_actual_dmy'); ?>">
                        <span class="help-block text-danger" id="error"></span>
                      </div>
                    </div>
                    
                    <div class="col-6 col-sm-2">
                      <label>F. Llegada <span class="label-advertencia text-danger"> *</span></label>
                      <div class="form-group">
                        <input type="text" id="modal-Fe_Llegada" name="Fe_Llegada" class="form-control input-datepicker-pay required" value="<?php echo dateNow('fecha_actual_dmy'); ?>">
                        <span class="help-block text-danger" id="error"></span>
                      </div>
                    </div>
                    
                    <div class="col-12 col-sm-12 col-md-12">
                      <div class="form-group">
                        <label>Cliente<span class="label-advertencia text-danger"> *</span></label>
                        <input type="hidden" id="txt-ID_Entidad" name="" class="form-control">
                        <input type="text" id="txt-No_Entidad" class="form-control autocompletar clearable" data-global-class_method="AutocompleteController/getAllClientCargaConsolidada" data-global-table="entidad" placeholder="Buscar por Nombre / DNI / RUC / OTROS" value="" autocomplete="off">
                        <span class="text-danger help-block" id="error"></span>
                      </div>
                    </div>

                    <div class="table-responsive div-clientes">
                      <table id="table-clientes" class="table table-bordered table-hover table-striped">
                        <thead class="thead-light">
                          <tr>
                            <th style='display:none;' class="text-left">ID</th>
                            <th class="text-left">Cliente</th>
                            <!--<th class="text-left">Mensaje</th>-->
                            <th class="text-left">Mensaje</th>
                            <th class="text-right">Importe</th>
                            <th class="text-center">Tarea</th>
                            <th class="text-center">Eliminar</th><!-- que no se pueda eliminar si se envio mensaje -->
                          </tr>
                        </thead>
                        <tbody>
                        </tbody>
                      </table>
                    </div>
                  </div>
                    
                  <div class="row">
                    <br/>
                    <div class="col-xs-6 col-md-6">
                      <div class="form-group">
                        <button type="button" id="btn-cancelar" class="btn btn-danger btn-lg btn-block">Cancelar</button>
                      </div>
                    </div>
                    <div class="col-xs-6 col-md-6">
                      <div class="form-group">
                        <button type="submit" id="btn-save" class="btn btn-success btn-lg btn-block btn-verificar">Guardar</button>
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
  <?php $attributes = array('id' => 'form-enviar_mensaje'); echo form_open('', $attributes); ?>
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