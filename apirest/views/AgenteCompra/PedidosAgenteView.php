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
                <table id="table-Pedidos" class="table table-bordered table-hover table-striped">
                  <thead class="thead-light">
                    <tr>
                      <th>País</th>
                      <th>Pedido</th>
                      <th>Fecha</th>
                      <th>Cliente</th>
                      <th>Empresa</th>
                      <th class="no-sort">Excel</th>
                      <!--<th class="no-sort">PDF</th>-->
                      <th>Estado</th>
                      <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Editar == 1) : ?>
                        <th class="no-sort">Ver</th>
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
                  <input type="hidden" id="txt-EID_Entidad" name="EID_Entidad" class="form-control">
                  <input type="hidden" id="txt-EID_Empresa" name="EID_Empresa" class="form-control">
                  <input type="hidden" id="txt-EID_Organizacion" name="EID_Organizacion" class="form-control">
                  
                  <div class="row">
                    <div class="col-12 col-sm-12 col-md-12">
                      <label>Estado</label>
                      <div class="form-group">
                        <div id="div-estado" style="font-size: 1.4rem;"></div>
                      </div>
                    </div>

                    <div class="col-12 col-sm-12 col-md-6">
                      <label>Cliente</label>
                      <div class="form-group">
                        <input type="text" name="No_Contacto" class="form-control required" placeholder="Ingresar" maxlength="100" autocomplete="off">
                        <span class="help-block text-danger" id="error"></span>
                      </div>
                    </div>

                    <div class="col-12 col-sm-4 col-md-4">
                      <label>Email</label>
                      <div class="form-group">
                        <input type="text" name="Txt_Email_Contacto" class="form-control required" placeholder="Ingresar" maxlength="100" autocomplete="off">
                        <span class="help-block text-danger" id="error"></span>
                      </div>
                    </div>

                    <div class="col-12 col-sm-4 col-md-2">
                      <label>Celular</label>
                      <div class="form-group">
                        <input type="text" inputmode="tel" name="Nu_Celular_Contacto" class="form-control required" placeholder="Ingresar" maxlength="11" autocomplete="off">
                        <span class="help-block text-danger" id="error"></span>
                      </div>
                    </div>
                    
                    <div class="col-12 col-sm-12 col-md-6">
                      <label>Empresa</label>
                      <div class="form-group">
                        <input type="text" name="No_Entidad" class="form-control required" placeholder="Ingresar" maxlength="100" autocomplete="off">
                        <span class="help-block text-danger" id="error"></span>
                      </div>
                    </div>

                    <div class="col-12 col-sm-4 col-md-4">
                      <label>RUC</label>
                      <div class="form-group">
                        <input type="text" name="Nu_Documento_Identidad" class="form-control required" placeholder="Ingresar" maxlength="100" autocomplete="off">
                        <span class="help-block text-danger" id="error"></span>
                      </div>
                    </div>
                  </div>
                    
                  <div class="row">
                    <div class="col-12 col-sm-8 col-md-8 d-none">
                      <label>Producto</label>
                      <div class="form-group">
                        <input type="hidden" id="txt-AID" name="AID" class="form-control">
                        <input type="hidden" id="txt-ID_Producto" name="ID_Producto" class="form-control">
                        <input type="hidden" id="txt-ID_Unidad_Medida" name="ID_Unidad_Medida" class="form-control">
                        <input type="hidden" id="txt-ID_Unidad_Medida_2" name="ID_Unidad_Medida_2" class="form-control">
                        <input type="hidden" id="txt-Precio_Producto" name="Precio_Producto" class="form-control">
                        <input type="hidden" id="txt-Cantidad_Configurada_Producto" name="Cantidad_Configurada_Producto" class="form-control">
                        <input type="hidden" id="txt-Nombre_Producto" name="Nombre_Producto" class="form-control">
                        <input type="hidden" id="txt-Nombre_Unidad_Medida" name="Nombre_Unidad_Medida" class="form-control">
                        <input type="text" id="txt-ANombre" name="ANombre" class="form-control autocompletar" placeholder="Buscar por Nombre" value="" autocomplete="off">
                        <span class="help-block text-danger" id="error"></span>
                      </div>
                    </div>
                      
                    <div class="col-12 col-sm-2 col-md-2 d-none">
                      <label>Cantidad</label>
                      <div class="form-group">
                        <input type="text" id="txt-Qt_Producto_Descargar" inputmode="decimal" name="Qt_Producto_Descargar" class="form-control input-decimal" placeholder="Ingresar cantidad" value="1" autocomplete="off">
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>
                    
                    <div class="col-12 col-sm-2 col-md-2 d-none">
                      <label class="hidden-xs">&nbsp;</label>
                      <div class="form-group">
                        <button type="button" id="btn-addProductosEnlaces" class="btn btn-success btn-block"> Agregar</button>
                      </div>
                    </div>
                    
                    <div class="col-xs-12 col-sm-12 col-md-12">
                      <div class="table-responsive div-Compuesto">
                        <table id="table-Producto_Enlace" class="table table-hover">
                          <thead>
                            <tr>
                              <th style='display:none;' class="text-left">ID</th>
                              <th class="text-left" width="30%">Imagen</th>
                              <th class="text-left">Nombre</th>
                              <th class="text-left">Características</th>
                              <th class="text-right">Cantidad</th>
                              <th class="text-left">Link</th>
                              <!--<th class="text-center"></th>-->
                            </tr>
                          </thead>
                          <tbody>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div><!-- ./table -->

                  <div class="row mt-3">
                    <div class="col-xs-6 col-md-6">
                      <div class="form-group">
                        <button type="button" id="btn-cancelar" class="btn btn-danger btn-lg btn-block">Salir</button>
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