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
              <div class="table-responsive div-Listar">
                <table id="table-Pedidos" class="table table-bordered table-hover">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Fecha</th>
                      <th>Cliente</th>
                      <th>M</th>
                      <th>F. Pago</th>
                      <th>Total 50%</th>
                      <th>Total</th>
                      <th>Cantidad</th>
                      <th class="no-sort">Estado</th>
                      <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Editar == 1) : ?>
                        <th class="no-sort">Editar</th>
                      <?php endif; ?>
                      <!--
                      <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Eliminar == 1) : ?>
                        <th class="no-sort">Eliminar</th>
                      <?php endif; ?>
                      -->
                    </tr>
                  </thead>
                </table>
              </div>
              
              <div class="box-body div-AgregarEditar">
                <?php
                $attributes = array('id' => 'form-pedido');
                echo form_open('', $attributes);
                ?>
                  <input type="hidden" id="txt-EID_Pedido_Cabecera" name="EID_Pedido_Cabecera" class="form-control">
                  
                  <div class="row">
                    <div class="col-xs-4 col-sm-4 col-md-2">
                      <div class="form-group">
                        <label>Moneda <span class="label-advertencia text-danger"> *</span></label>
                        <select id="cbo-Monedas" name="ID_Moneda" class="form-control required" style="width: 100%;"></select>
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>

                    <div class="col-xs-12 col-sm-12 col-md-6">
                      <label>Nombre</label>
                      <div class="form-group">
                        <input type="text" name="No_Entidad" class="form-control required" placeholder="Ingresar" maxlength="100" autocomplete="off">
                        <span class="help-block text-danger" id="error"></span>
                      </div>
                    </div>

                    <div class="col-xs-4 col-sm-3 col-md-2">
                      <label>F. Emisión</label><span class="label-advertencia text-danger"> *</span>
                      <div class="form-group">
                        <input type="text" id="fecha_emision" name="Fe_Emision" class="form-control" placeholder="Ingresar" autocomplete="off">
                        <!--
                        <div class="input-group date" id="fecha_emision" data-target-input="nearest">
                          <input type="text" name="Fe_Emision" value="" class="form-control datetimepicker-input date-picker-report_crud" data-target="#fecha_emision" data-toggle="datetimepicker" />
                          <div class="input-group-append" data-target="#fecha_emision" data-toggle="datetimepicker">
                              <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                          </div>
                        </div>
                        -->
                        <span class="help-block text-danger" id="error"></span>
                      </div>
                    </div>

                    <div class="col-xs-12 col-sm-12 col-md-2">
                      <label>Estado</label>
                      <div class="form-group">
                        <input type="text" name="No_Estado" class="form-control" placeholder="Ingresar" autocomplete="off">
                        <span class="help-block text-danger" id="error"></span>
                      </div>
                    </div>
                  </div>
                  
                  <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 d-none">
                      <label>Producto</label>
                      <div class="form-group">
                        <input type="hidden" id="txt-AID" name="AID" class="form-control">
                        <input type="hidden" id="txt-ACodigo" name="ACodigo" class="form-control">
                        <input type="text" id="txt-ANombre" name="ANombre" class="form-control autocompletar" placeholder="Buscar por Nombre / Código" value="" autocomplete="off">
                        <span class="help-block text-danger" id="error"></span>
                      </div>
                    </div>
                    
                    <div class="col-xs-12 col-sm-12 col-md-12">
                      <div class="table-responsive div-Compuesto">
                        <table id="table-Producto_Enlace" class="table table-hover">
                          <thead>
                            <tr>
                              <th style='display:none;' class="text-left">ID</th>
                              <th class="text-left">Nombre</th>
                              <th class="text-left">Unidad</th>
                              <th class="text-left">Cantidad</th>
                              <th class="text-left">Precio</th>
                              <th class="text-left">Total</th>
                              <!--<th class="text-center"></th>-->
                            </tr>
                          </thead>
                          <tbody>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div><!-- ./Compuesto -->

                  <div class="row mt-3">
                    <div class="col-xs-12 col-md-12">
                      <div class="form-group">
                        <button type="button" id="btn-cancelar" class="btn btn-danger btn-lg btn-block">Salir</button>
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