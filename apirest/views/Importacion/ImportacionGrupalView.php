<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-8">
          <h1><i class="<?php echo $this->MenuModel->verificarAccesoMenuCRUD()->Txt_Css_Icons; ?>" aria-hidden="true"></i> <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?></h1>
        </div>
        
        <div class="col-sm-4 div-Listar">
          <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Agregar == 1) : ?>
            <button type="button" class="btn btn-primary btn-block" onclick="agregarCliente()"><i class="fa fa-plus-circle"></i> Agregar</button>
          <?php endif; ?>
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
              <div class="table-responsive div-Listar">
                <table id="table-Cliente" class="table table-bordered table-hover">
                  <thead>
                    <tr>
                      <th>Moneda</th>
                      <th>Nombre</th>
                      <th>F. Inicio</th>
                      <th>F. Fin</th>
                      <th class="no-sort">Estado</th>
                      <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Editar == 1) : ?>
                        <th class="no-sort">Editar</th>
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
                $attributes = array('id' => 'form-Cliente');
                echo form_open('', $attributes);
                ?>
                  <input type="hidden" id="txt-EID_Importacion_Grupal" name="EID_Importacion_Grupal" class="form-control">
                  
                  <div class="row">
                    <div class="col-xs-4 col-sm-4 col-md-2">
                      <div class="form-group">
                        <label>Moneda <span class="label-advertencia text-danger"> *</span></label>
                        <select id="cbo-Monedas" name="ID_Moneda" class="form-control required" style="width: 100%;"></select>
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>

                    <div class="col-xs-12 col-sm-12 col-md-10">
                      <label>Nombre <span class="label-advertencia text-danger"> *</span></label>
                      <div class="form-group">
                        <input type="text" name="No_Importacion_Grupal" class="form-control required" placeholder="Ingresar" maxlength="100" autocomplete="off">
                        <span class="help-block text-danger" id="error"></span>
                      </div>
                    </div>

                    <div class="col-xs-4 col-sm-3 col-md-4">
                      <label>F. Inicio</label><span class="label-advertencia text-danger"> *</span>
                      <div class="form-group">
                        <div class="input-group date" id="fecha_inicio" data-target-input="nearest">
                          <input type="text" name="Fe_Inicio" value="<?php echo dateNow('fecha_actual_dmy'); ?>" class="form-control datetimepicker-input date-picker-report_crud" data-target="#fecha_inicio" data-toggle="datetimepicker"/>
                          <div class="input-group-append" data-target="#fecha_inicio" data-toggle="datetimepicker">
                              <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                          </div>
                        </div>
                        <span class="help-block text-danger" id="error"></span>
                      </div>
                    </div>

                    <div class="col-xs-4 col-sm-3 col-md-4">
                      <label>F. Fin</label><span class="label-advertencia text-danger"> *</span>
                      <div class="form-group">
                        <div class="input-group date" id="fecha_fin" data-target-input="nearest">
                          <input type="text" name="Fe_Fin" value="<?php echo dateNow('fecha_actual_dmy'); ?>" class="form-control datetimepicker-input date-picker-report_crud" data-target="#fecha_fin" data-toggle="datetimepicker"/>
                          <div class="input-group-append" data-target="#fecha_fin" data-toggle="datetimepicker">
                              <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                          </div>
                        </div>
                        <span class="help-block text-danger" id="error"></span>
                      </div>
                    </div>

                    <div class="col-xs-6 col-sm-4 col-md-4">
                      <label>Estado <span class="label-advertencia text-danger"> *</span></label>
                      <div class="form-group estado">
                        <select id="cbo-Estado" name="Nu_Estado" class="form-control required"></select>
                        <span class="help-block text-danger" id="error"></span>
                      </div>
                    </div>

                    <div class="col-xs-12 col-sm-12 col-md-12">
                      <label>Descripción</label></span>
                      <div class="form-group">
                        <textarea name="Txt_Importacion_Grupal" class="form-control" rows="1" placeholder="Opcional" maxlength="255"></textarea>
                        <span class="help-block text-danger" id="error"></span>
                      </div>
                    </div>
                  </div>
                  
                  <div class="row">
                    <div class="col-xs-12 col-sm-9 col-md-6">
                      <label>Producto</label>
                      <div class="form-group">
                        <input type="hidden" id="txt-AID" name="AID" class="form-control">
                        <input type="hidden" id="txt-ACodigo" name="ACodigo" class="form-control">
                        <input type="text" id="txt-ANombre" name="ANombre" class="form-control autocompletar" data-global-class_method="AutocompleteController/globalAutocomplete" data-global-table="producto" placeholder="Buscar por Nombre / Código" value="" autocomplete="off">
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>
                    
                    <div class="col-xs-12 col-sm-3 col-md-3">
                      <label>Cantidad</label>
                      <div class="form-group">
                        <input type="tel" id="txt-Qt_Producto_Descargar" inputmode="decimal" name="Qt_Producto_Descargar" class="form-control input-decimal" placeholder="Ingresar cantidad" value="1" autocomplete="off">
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>
                    
                    <div class="col-xs-12 col-sm-12 col-md-3">
                      <label class="hidden-xs">&nbsp;</label>
                      <div class="form-group">
                        <button type="button" id="btn-addProductosEnlaces" class="btn btn-success btn-block"> Agregar</button>
                      </div>
                    </div>
                  </div><!-- ./Compuesto -->

                  <div class="row mt-3">
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