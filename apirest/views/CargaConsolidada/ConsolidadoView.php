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
            <button type="button" class="btn btn-success btn-block" onclick="agregarCliente()"><i class="fa fa-plus-circle"></i> Crear</button>
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
                      <th>Nombre</th>
                      <th>Estado</th>
                      <th class="no-sort">Checklist</th>
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
                  <input type="hidden" id="txt-EID_Carga_Consolidada" name="EID_Carga_Consolidada" class="form-control">
                  <input type="hidden" id="txt-ENo_Carga_Consolidada" name="ENo_Carga_Consolidada" class="form-control">
                  
                  <div class="row">
                    <div class="col-8 col-sm-10 col-md-10">
                      <label>Nombre <span class="label-advertencia text-danger"> *</span></label>
                      <div class="form-group">
                        <input type="text" name="No_Carga_Consolidada" class="form-control required" placeholder="Ingresar" maxlength="100" autocomplete="off">
                        <span class="help-block text-danger" id="error"></span>
                      </div>
                    </div>

                    <div class="col-4 col-sm-2 col-md-2">
                      <label>Estado <span class="label-advertencia text-danger"> *</span></label>
                      <div class="form-group estado">
                        <select id="cbo-Estado" name="Nu_Estado" class="form-control required"></select>
                        <span class="help-block text-danger" id="error"></span>
                      </div>
                    </div>

                    <div class="col-12 col-sm-12 col-md-12">
                      <label>Descripción</label></span>
                      <div class="form-group">
                        <textarea name="Txt_Nota" class="form-control" rows="1" placeholder="Opcional" maxlength="255"></textarea>
                        <span class="help-block text-danger" id="error"></span>
                      </div>
                    </div>
                  </div>
                  
                  <div class="row mt-3">
                    <div class="col-6 col-md-6">
                      <div class="form-group">
                        <button type="button" id="btn-cancelar" class="btn btn-danger btn-lg btn-block">Cancelar</button>
                      </div>
                    </div>
                    <div class="col-6 col-md-6">
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