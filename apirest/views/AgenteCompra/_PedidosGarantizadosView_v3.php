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
                      <th>Cotización</th>
                      <th>Fecha</th>
                      <th>Cliente</th>
                      <th>Empresa</th>
                      <th class="no-sort">Excel</th>
                      <!--<th class="no-sort">PDF</th>-->
                      <th>Perú</th>
                      <th>China</th>
                      <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Editar == 1) : ?>
                        <th class="no-sort">Ver</th>
                      <?php endif; ?>
                      <th class="no-sort">Pay</th>
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
                //$attributes = array('id' => 'form-pedido');
                $attributes = array('id' => 'form-pedido', 'enctype' => 'multipart/form-data');
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

                    <div class="col-6 col-sm-3 col-md-3">
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
                    
                    <div class="col-6 col-sm-3 col-md-3">
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
                    
                    <div class="col-12 col-sm-4 col-md-2">
                      <label>T.C.</label>
                      <div class="form-group">
                        <input type="text" name="Ss_Tipo_Cambio" class="form-control required" placeholder="Ingresar" autocomplete="off">
                        <span class="help-block text-danger" id="error"></span>
                      </div>
                    </div>
                    
                    <div class="col-12 col-sm-4 col-md-4">
                      <label>Observaciones</label>
                      <div class="form-group">
                        <textarea name="Txt_Observaciones_Garantizado" class="form-control" rows="1" placeholder="Opcional" style="height: 38px !important;"></textarea>
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
                    
                    <div class="col-12 col-sm-12 col-md-12 mb-3" id="div-button-add_item">
                      <button type="button" id="btn-add_item" class="btn btn-danger btn-lg col shadow">Agregar producto</button>
                    </div>

                    <div class="col-12 col-sm-12 col-md-12 mb-3 div-articulos">
                      <div id="div-arrItemsPedidos"></div>
                    </div>

                    <div class="col-xs-12 col-sm-12 col-md-12">
                      <h3><span id="span-total_cantidad_items" class="badge badge-danger"></span> Productos</h3>

                      <div class="table-responsive div-Compuesto">
                        <table id="table-Producto_Enlace" class="table table-bordered table-hover table-striped">
                          <thead class="thead-light">
                            <tr>
                              <th style='display:none;' class="text-left">ID</th>
                              <th class="text-left" width="50%">producto_imagen</th>
                              <!--<th class="text-left" width="20%">Nombre</th>-->
                              <th class="text-left" width="20%">Características_producto</th>
                              <!--<th class="text-right">Qty</th>-->
                              <th class="text-left" width="10%">Link</th>
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
                    <div class="col-6 col-md-6">
                      <div class="form-group">
                        <button type="button" id="btn-cancelar" class="btn btn-danger btn-lg btn-block">Salir</button>
                      </div>
                    </div>
                    <div class="col-6 col-md-6">
                      <div class="form-group">
                        <button type="submit" id="btn-save" class="btn btn-success btn-lg btn-block btn-verificar">Guardar</button>
                      </div>
                    </div>
                  </div>
                <?php echo form_close(); ?>
              </div><!--div agregar-->
              <!-- div agregar productos de proveedor -->
              <div class="box-body" id="div-add_item_proveedor">
                <?php
                $attributes = array('id' => 'form-arrItems');
                echo form_open('', $attributes);
                ?>
                  <input type="hidden" id="txt-EID_Empresa_item" name="EID_Empresa_item" class="form-control">
                  <input type="hidden" id="txt-EID_Organizacion_item" name="EID_Organizacion_item" class="form-control">
                  <input type="hidden" id="txt-EID_Pedido_Cabecera_item" name="EID_Pedido_Cabecera_item" class="form-control">
                  <input type="hidden" id="txt-EID_Pedido_Detalle_item" name="EID_Pedido_Detalle_item" class="form-control">
                                    
                  <div id="div-arrItems" class="div-agregar_proveedor"></div>

                  <div class="row div-agregar_proveedor">
                    <div class="col-12 col-sm-12 col-md-12 shadow p-0" id="div-button-add_item">
                      <div class="d-grid gap">
                        <button type="button" id="btn-add_item" class="btn btn-danger btn-lg col">Nuevo proveedor</button>
                      </div>
                    </div>
                  </div>

                  <div class="row mt-4 div-agregar_proveedor">
                    <div class="col-6 col-md-6">
                      <div class="form-group">
                        <button type="button" id="btn-cancel_detalle_item_proveedor" class="btn btn-outline-secondary btn-lg btn-block">Regresar</button>
                      </div>
                    </div>
                    <div class="col-6 col-md-6">
                      <div class="form-group">
                        <button type="submit" id="btn-save_detalle_item_proveedor" class="btn btn-success btn-lg btn-block shadow">Guardar</button>
                      </div>
                    </div>
                  </div>
                <?php echo form_close(); ?>
              </div><!--div agregar productos de proveedor -->
              <div class="box-body" id="div-elegir_item_proveedor">
                <?php
                $attributes = array('id' => 'form-arrItemsProveedor');
                echo form_open('', $attributes);
                ?>
                  <input type="hidden" id="txt-EID_Empresa_item" name="EID_Empresa_item" class="form-control">
                  <input type="hidden" id="txt-EID_Organizacion_item" name="EID_Organizacion_item" class="form-control">
                  <input type="hidden" id="txt-EID_Pedido_Cabecera_item" name="EID_Pedido_Cabecera_item" class="form-control">
                  <input type="hidden" id="txt-EID_Pedido_Detalle_item" name="EID_Pedido_Detalle_item" class="form-control">

                  <div id="div-arrItemsProveedor" class="col-xs-12 col-sm-12 col-md-12">
                    <h3>Productos</h3>

                    <div class="table-responsive">
                      <table id="table-elegir_productos_proveedor" class="table table-bordered table-hover table-striped">
                        <thead class="thead-light">
                          <tr>
                            <th style='display:none;' class="text-left">ID</th>
                            <th class="text-left" width="">imagen_producto</th>
                            <th class="text-left" width="">P_Dólares</th>
                            <th class="text-left" width="">P_Yuanes</th>
                            <th class="text-left" width="">__Moq__</th>
                            <th class="text-left" width="">qty_caja</th>
                            <th class="text-left" width="">__cbm__</th>
                            <th class="text-left" width="">T. Producción</th>
                            <th class="text-left" width="">C. Delivery</th>
                            <th class="text-left" width="">Observaciones</th>
                            <th class="text-left" width="">Proveedor_nombre</th>
                            <th class="text-left" width="">Proveedor___Foto__</th>
                          </tr>
                        </thead>
                        <tbody>
                        </tbody>
                      </table>
                    </div>
                  </div>

                  <div class="row mt-4">
                    <div class="col-6 col-md-6">
                      <div class="form-group">
                        <button type="button" id="btn-cancel_detalle_elegir_proveedor" class="btn btn-outline-secondary btn-lg btn-block">Regresar</button>
                      </div>
                    </div>
                    <div class="col-6 col-md-6">
                      <div class="form-group">
                        <button type="submit" id="btn-save_detalle_elegir_proveedor" class="btn btn-success btn-lg btn-block shadow">Guardar</button>
                      </div>
                    </div>
                  </div>
                <?php echo form_close(); ?>
              </div><!--div elegir productos de proveedor -->
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
        <button type="button" class="col btn btn-outline-danger btn-lg btn-block pull-center" data-dismiss="modal">Salir</button>
        <a id="a-download_image" class="col btn btn-primary btn-lg btn-block" data-id_item="">Descargar</a>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal imagen del item -->

<!-- modal documento -->
<div class="modal fade modal-documento_pago_garantizado" id="modal-documento_pago_garantizado">
  <?php $attributes = array('id' => 'form-documento_pago_garantizado'); echo form_open('', $attributes); ?>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body" id="modal-body-documento_pago_garantizado">
        <div class="row">
          <input type="hidden" id="documento_pago_garantizado-id_cabecera" name="documento_pago_garantizado-id_cabecera" class="form-control">

          <div class="col-sm-12">
            <label>Voucher</label>
            <div class="form-group">
              <input class="form-control" id="image_documento" name="image_documento" type="file" accept="image/*"></input>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-outline-danger btn-lg col" data-dismiss="modal">Cancelar</button>
        <button type="submit" id="btn-guardar_documento_pago_garantizado" class="col btn btn-success btn-lg btn-block">Guardar</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div>
<!-- /.modal agregar pagos -->

<!-- modal ver pago garantizado -->
<div class="modal fade modal-ver_pago_garantizado" id="modal-default">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body" id="modal-body-ver_pago_garantizado">
        <div class="col-xs-12 text-center">
          <img class="img-responsive img-pago_garantizado img-fluid" style=" display: block; margin-left: auto; margin-right: auto;" src="">
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="col btn btn-outline-danger btn-lg btn-block pull-center" data-dismiss="modal">Salir</button>
        <a id="a-download_image_pago_garantizado" target="_blank" rel="noopener noreferrer" class="col btn btn-primary btn-lg btn-block" data-id_pago="">Descargar</a>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal imagen del item -->

<div class="modal fade modal-chat_producto" id="modal-default">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Producto</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
      </div>
      <div class="modal-body" id="modal-body-chat_producto">
        <div class="row">
          <div class="col-md-12">
            <!-- DIRECT CHAT PRIMARY -->
            <div class="card card-primary card-outline direct-chat direct-chat-primary">
              <div class="card-header">
                <h3 class="card-title">Chat</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <!-- Conversations are loaded here -->
                <div class="direct-chat-messages">
                  <!-- Message. Default to the left -->
                  <div class="direct-chat-msg">
                    <div class="direct-chat-infos clearfix">
                      <span class="direct-chat-name float-left">Alexander Pierce</span>
                      <span class="direct-chat-timestamp float-right">23 Jan 2:00 pm</span>
                    </div>
                    <!-- /.direct-chat-infos -->
                    <img class="direct-chat-img" src="<?php echo base_url() . 'dist_v2/img/user_all.png?ver=1.0.0'; ?>" alt="a">
                    <!-- /.direct-chat-img -->
                    <div class="direct-chat-text">
                      Is this template really for free? That's unbelievable!
                    </div>
                    <!-- /.direct-chat-text -->
                  </div>
                  <!-- /.direct-chat-msg -->

                  <!-- Message to the right -->
                  <div class="direct-chat-msg right">
                    <div class="direct-chat-infos clearfix">
                      <span class="direct-chat-name float-right">Sarah Bullock</span>
                      <span class="direct-chat-timestamp float-left">23 Jan 2:05 pm</span>
                    </div>
                    <!-- /.direct-chat-infos -->
                    <img class="direct-chat-img" src="<?php echo base_url() . 'dist_v2/img/user_all.png?ver=1.0.0'; ?>" alt="b">
                    <!-- /.direct-chat-img -->
                    <div class="direct-chat-text">
                      You better believe it!
                    </div>
                    <!-- /.direct-chat-text -->
                  </div>
                  <!-- /.direct-chat-msg -->
                </div>
                <!--/.direct-chat-messages-->
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                <form action="#" method="post">
                  <div class="input-group">
                    <input type="text" name="message" placeholder="Escribir mensaje ..." class="form-control form-control-lg">
                    <span class="input-group-append">
                      <button type="button" class="btn btn-primary btn-lg">Enviar</button>
                    </span>
                  </div>
                </form>
              </div>
              <!-- /.card-footer-->
            </div>
            <!--/.direct-chat -->
          </div>
          <!-- /.col -->
        </div>
      </div>
    </div>
  </div>
</div>