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
              <div class="table-responsive div-Listar">
                <table id="table-Pedidos" class="table table-bordered table-hover table-striped">
                  <thead class="thead-light">
                    <tr>
                      <th>País</th>
                      <th>Pedido</th>
                      <th>Fecha</th>
                      <th>Cliente</th>
                      <!--<th>Empresa</th>-->
                      <th class="no-sort">Excel</th>
                      <th>Perú</th>
                      <th>China</th>
                      <th class="no-sort">Pay</th>
                      <th class="no-sort">Insp.</th>
                      <th class="no-sort">Invoice</th>
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
                    <div class="col-12 col-sm-12 col-md-12 d-none">
                      <label>Estado</label>
                      <div class="form-group">
                        <div id="div-estado" style="font-size: 1.4rem;"></div>
                      </div>
                    </div>

                    <div class="col-6 col-sm-6 col-md-6">
                      <label>Cliente</label>
                      <div class="form-group">
                        <input type="text" name="No_Contacto" class="form-control required" placeholder="Ingresar" maxlength="100" autocomplete="off">
                        <span class="help-block text-danger" id="error"></span>
                      </div>
                    </div>

                    <div class="col-12 col-sm-4 col-md-4 d-none">
                      <label>Email</label>
                      <div class="form-group">
                        <input type="text" name="Txt_Email_Contacto" class="form-control required" placeholder="Ingresar" maxlength="100" autocomplete="off">
                        <span class="help-block text-danger" id="error"></span>
                      </div>
                    </div>

                    <div class="col-12 col-sm-4 col-md-2 d-none">
                      <label>Celular</label>
                      <div class="form-group">
                        <input type="text" inputmode="tel" name="Nu_Celular_Contacto" class="form-control required" placeholder="Ingresar" maxlength="11" autocomplete="off">
                        <span class="help-block text-danger" id="error"></span>
                      </div>
                    </div>
                    
                    <div class="col-6 col-sm-6 col-md-6">
                      <label>Empresa</label>
                      <div class="form-group">
                        <input type="text" name="No_Entidad" class="form-control required" placeholder="Ingresar" maxlength="100" autocomplete="off">
                        <span class="help-block text-danger" id="error"></span>
                      </div>
                    </div>

                    <div class="col-12 col-sm-4 col-md-4 d-none">
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
                      <h3><span id="span-total_cantidad_items" class="badge badge-danger"></span> Productos</h3>

                      <div class="table-responsive div-Compuesto">
                        <table id="table-Producto_Enlace" class="table table-bordered table-hover table-striped">
                          <thead class="thead-light">
                            <tr>
                              <th style='display:none;' class="text-left">ID</th>
                              <th class="text-left" width="50%">Product_Photo</th>
                              <th class="text-left">Product_Name</th>
                              <th class="text-right">Qty</th>
                              <th class="text-right">Price</th>
                              <th class="text-right">Amount</th>
                              <th class="text-right">Deposit_#1</th>
                              <th class="text-right">Balance</th>
                              <th class="text-right">Deposit_#2</th>
                              <th class="text-right">Delivery</th>
                              <th class="text-right">Supplier</th><!--proveedor-->
                              <th class="text-right">Phone</th><!--celular-->
                            </tr>
                          </thead>
                          <tbody>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div><!-- ./table -->

                  <div class="row mt-3">
                    <div class="col-12 col-md-12">
                      <div class="form-group">
                        <button type="button" id="btn-cancelar" class="btn btn-danger btn-lg btn-block">Salir</button>
                      </div>
                    </div>
                    <div class="col-6 col-md-6 d-none">
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
<div class="modal fade modal-ver_item" id="modal-default">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body" id="modal-body-ver_item">
        <div class="col-xs-12 text-center">
          <img class="img-responsive img-fluid" style=" display: block; margin-left: auto; margin-right: auto;" src="">
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="col btn btn-danger btn-lg btn-block pull-center" data-dismiss="modal">Salir</button>
        <a id="a-download_image" class="col btn btn-primary btn-lg btn-block" data-id_item="">Descargar</a>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal imagen del item -->

<!-- modal agregar pagos -->
<div class="modal fade modal-agregar_pago" id="modal-agregar_pago">
  <?php $attributes = array('id' => 'form-agregar_pago_proveedor'); echo form_open('', $attributes); ?>
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-body" id="modal-body-agregar_pago">
        <div class="row">
          <input type="hidden" id="proveedor-id_empresa" name="proveedor-id_empresa" class="form-control">
          <input type="hidden" id="proveedor-id_organizacion" name="proveedor-id_organizacion" class="form-control">
          <input type="hidden" id="proveedor-id_cabecera" name="proveedor-id_cabecera" class="form-control">
          <input type="hidden" id="proveedor-id_detalle" name="proveedor-id_detalle" class="form-control">
          <input type="hidden" id="proveedor-id" name="proveedor-id" class="form-control">
          <input type="hidden" id="proveedor-tipo_pago" name="proveedor-tipo_pago" class="form-control">

          <div class="col-12 col-sm-6 text-center">
            <label>Amount</label>
            <div class="form-group">
              <input type="text" inputmode="decimal" id="amount_proveedor" name="amount_proveedor" class="form-control input-decimal required" placeholder="" maxlength="16" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-12 col-sm-6 position-relative text-center ps-4 pe-3 pe-sm-0">
            <div class="col-sm-12">
              <label>Voucher</label>
              <div class="form-group">
                <label class="btn btn btn-outline-secondary" for="voucher_proveedor" style="width: 100%;">
                  <input class="arrProducto form-control voucher_proveedor" id="voucher_proveedor" type="file" style="display:none" name="voucher_proveedor" data-id="1" onchange="loadFile(event, 1)" placeholder="sin archivo" accept="image/*">Subir archivo
                </label>
                <span class="help-block text-danger" id="error"></span>
              </div>
            </div>
            <img id="img_producto-preview1" src="" class="arrProducto img-thumbnail border-0 rounded" alt="">
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-outline-danger btn-lg col" data-dismiss="modal">Cancelar</button>
        <button type="submit" id="btn-guardar_pago_proveedor" class="col btn btn-success btn-lg btn-block">Guardar</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div>
<!-- /.modal agregar pagos -->

<!-- modal agregar pagos -->
<div class="modal fade modal-agregar_inspeccion" id="modal-agregar_inspeccion">
  <?php $attributes = array('id' => 'form-agregar_inspeccion'); echo form_open('', $attributes); ?>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body" id="modal-body-agregar_inspeccion">
        <div class="row">
          <input type="hidden" id="proveedor-id_empresa" name="proveedor-id_empresa" class="form-control">
          <input type="hidden" id="proveedor-id_organizacion" name="proveedor-id_organizacion" class="form-control">
          <input type="hidden" id="proveedor-id_cabecera" name="proveedor-id_cabecera" class="form-control">
          <input type="hidden" id="proveedor-id_detalle" name="proveedor-id_detalle" class="form-control">
          <input type="hidden" id="proveedor-id" name="proveedor-id" class="form-control">
          <input type="hidden" id="proveedor-tipo_pago" name="proveedor-tipo_pago" class="form-control">

          <div class="col-sm-12">
            <label>Inspección</label>
            <div class="form-group">
              <input class="form-control" id="image_inspeccion" name="image_inspeccion[]" type="file" accept="image/*" multiple></input>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-outline-danger btn-lg col" data-dismiss="modal">Cancelar</button>
        <button type="submit" id="btn-guardar_agregar_inspeccion" class="col btn btn-success btn-lg btn-block">Guardar</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div>
<!-- /.modal agregar pagos -->

<!-- modal ver imagen del item -->
<div class="modal fade modal-ver_inspeccion_item" id="modal-ver_inspeccion_item">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body" id="modal-body-ver_inspeccion_item">
        <div class="col-xs-12 text-center">
          <div id="div-img_inspeccion_item"></div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="col btn btn-danger btn-lg btn-block pull-center" data-dismiss="modal">Salir</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal imagen del item -->

<!-- modal documento -->
<div class="modal fade modal-documento_entrega" id="modal-documento_entrega">
  <?php $attributes = array('id' => 'form-documento_entrega'); echo form_open('', $attributes); ?>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body" id="modal-body-documento_entrega">
        <div class="row">
          <input type="hidden" id="documento-id_cabecera" name="documento-id_cabecera" class="form-control">

          <div class="col-sm-12">
            <label>Documento</label>
            <div class="form-group">
              <input class="form-control" id="image_documento" name="image_documento" type="file" accept="application/msword, application/vnd.ms-excel, application/pdf"></input>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-outline-danger btn-lg col" data-dismiss="modal">Cancelar</button>
        <button type="submit" id="btn-guardar_documento_entrega" class="col btn btn-success btn-lg btn-block">Guardar</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div>
<!-- /.modal agregar pagos -->