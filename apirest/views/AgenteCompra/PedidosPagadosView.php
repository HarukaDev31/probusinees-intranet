<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-8">
          <h1>
            <i class="<?php echo $this->MenuModel->verificarAccesoMenuCRUD()->Txt_Css_Icons; ?>" aria-hidden="true"></i> <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?>
            &nbsp;<span id="span-id_pedido" class="badge badge-secondary"></span>
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
              <div class="row mb-3 div-Listar">
                <input type="hidden" id="hidden-sCorrelativoCotizacion" name="sCorrelativoCotizacion" class="form-control" value="<?php echo $sCorrelativoCotizacion; ?>">
                <input type="hidden" id="hidden-ID_Pedido_Cabecera" name="ID_Pedido_Cabecera" class="form-control" value="<?php echo $ID_Pedido_Cabecera; ?>">
                <div class="col-6 col-sm-4">
                  <label>F. Inicio <span class="label-advertencia text-danger"> *</span></label>
                  <div class="form-group">
                    <input type="text" id="txt-Fe_Inicio" class="form-control input-report required" value="<?php echo dateNow('month_date_ini_report'); ?>">
                    <span class="help-block text-danger" id="error"></span>
                  </div>
                </div>
                <div class="col-6 col-sm-4">
                  <label>F. Fin <span class="label-advertencia text-danger"> *</span></label>
                  <div class="form-group">
                    <input type="text" id="txt-Fe_Fin" class="form-control input-report required" value="<?php echo dateNow('fecha_actual_dmy'); ?>">
                    <span class="help-block text-danger" id="error"></span>
                  </div>
                </div>
                <div class="col-6 col-sm-4">
                  <label>&nbsp;</label>
                  <button type="button" id="btn-html_reporte" class="btn btn-primary btn-block btn-reporte" data-type="html"><i class="fa fa-search"></i> Buscar</button>
                </div>
              </div>
              
              <div class="table-responsive div-Listar">
                <table id="table-Pedidos" class="table table-bordered table-hover table-striped">
                  <thead class="thead-light">
                    <tr>
                      <th>País</th>
                      <th>Pedido</th>
                      <th>Fecha</th>
                      <th>Cliente</th>
                      <!--<th>Empresa</th>-->
                      <!--<th class="no-sort">Excel</th>-->
                      <th>Servicio</th>
                      <th>Incoterms</th>
                      <th>Envío</th>
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

                    <div class="col-6 col-sm-12 col-md-6 text-left">
                      <label>Pagos</label>
                      <div class="form-group">
                        <button type="button" class="btn btn-secondary" alt="Subir pago 30%" title="Subir pago 30%" onclick="subirPago30()">Pagar 30%</button>
                        <button type="button" id="btn-descargar_pago_30" class="btn btn-secondary d-none" alt="Descargar pago 30%" title="Descargar pago 30%" onclick="descargarPago30()"><span id="span-pago_30"></span> <i class="fas fa-download" aria-hidden="true"></i></button>
                        
                        <button type="button" class="btn btn-secondary" alt="Subir pago 100%" title="Subir pago 100%" onclick="subirPago100()">Pagar 70%</button>
                        <button type="button" id="btn-descargar_pago_100" class="btn btn-secondary d-none" alt="Descargar pago 100%" title="Descargar pago 100%" onclick="descargarPago100()"><span id="span-pago_100"></span> <i class="fas fa-download" aria-hidden="true"></i></button>
                        
                        <button type="button" class="btn btn-secondary" alt="Subir pago servicio" title="Subir pago servicio" onclick="subirPagoServicio()">Pagar servicio</button>
                        <button type="button" id="btn-descargar_pago_servicio" class="btn btn-secondary d-none" alt="Descargar pago servicio" title="Descargar pago servicio" onclick="descargarPagoServicio()"><span id="span-pago_servicio"></span> <i class="fas fa-download" aria-hidden="true"></i></button>
                      </div>
                    </div>

                    <div class="col-6 col-sm-12 col-md-6 text-left">
                      <label>Otros Pagos</label>
                      <div class="form-group">
                        <button type="button" class="btn btn-secondary" alt="Subir Flete" title="Subir Flete" onclick="subirPagoFlete()">Pagar Flete</button>
                        <button type="button" id="btn-descargar_flete" class="btn btn-secondary d-none" alt="Descargar Flete" title="Descargar Flete" onclick="descargarPagoFlete()"><span id="span-flete"></span> <i class="fas fa-download" aria-hidden="true"></i></button>
                        
                        <button type="button" class="btn btn-secondary" alt="Subir Costo Origen" title="Subir Costo Origen" onclick="subirPagoCostoOrigen()">Costo Origen</button>
                        <button type="button" id="btn-descargar_costo_origen" class="btn btn-secondary d-none" alt="Descargar Costo Origen" title="Descargar Costo Origen" onclick="descargarPagoCostosOrigen()"><span id="span-costo_origen"></span> <i class="fas fa-download" aria-hidden="true"></i></button>
                        
                        <button type="button" class="btn btn-secondary" alt="Subir Costo FTA" title="Subir Costo FTA" onclick="subirPagoFTA()">Costo FTA</button>
                        <button type="button" id="btn-descargar_fta" class="btn btn-secondary d-none" alt="Descargar Costo FTA" title="Descargar Costo FTA" onclick="descargarPagoFTA()"><span id="span-fta"></span> <i class="fas fa-download" aria-hidden="true"></i></button>
                        
                        <button type="button" class="btn btn-secondary" alt="Subir Costo Gastos" title="Subir Costo Gastos" onclick="subirPagoCuadrilla()">Gastos</button>
                        <button type="button" id="btn-descargar_pago_cuadrilla" class="btn btn-secondary d-none" alt="Descargar Costo Gastos" title="Descargar Costo Gastos" onclick="descargarPagoCuadrilla()"><span id="span-cuadrilla"></span> <i class="fas fa-download" aria-hidden="true"></i></button>

                        <!-- ahora lo utilizaremos para otros gastos y se agrego campo de texto libre para concepto
                        <button type="button" class="btn btn-secondary" alt="Subir Costo Cuadrilla" title="Subir Costo Cuadrilla" onclick="subirPagoCuadrilla()">Cuadrilla</button>
                        <button type="button" id="btn-descargar_pago_cuadrilla" class="btn btn-secondary d-none" alt="Descargar Costo Cuadrilla" title="Descargar Costo Cuadrilla" onclick="descargarPagoCuadrilla()"><span id="span-cuadrilla"></span> <i class="fas fa-download" aria-hidden="true"></i></button>
                        -->
                        
                        <button type="button" class="btn btn-secondary" alt="Subir Otros Costos" title="Subir Otros Costo" onclick="subirPagoOtrosCostos()">Otros Costo</button>
                        <button type="button" id="btn-descargar_otros_costos" class="btn btn-secondary d-none" alt="Descargar Otros Costo" title="Descargar Otros Costo" onclick="descargarPagoOtrosCostos()"><span id="span-otros_costo"></span> <i class="fas fa-download" aria-hidden="true"></i></button>
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
                    
                    <div class="col-xs-12 col-sm-12 col-md-12 mt-3 <?php echo $this->user->Nu_Tipo_Privilegio_Acceso==1 ? '' : 'd-none'; ?>">
                      <div class="col-12 col-sm-2 col-md-2">
                        <label>Total Cliente</label>
                        <div class="form-group">
                          <span id="span-total_cliente"></span>
                        </div>
                      </div>
                      <div class="col-12 col-sm-2 col-md-2">
                        <label>Saldo Cliente</label>
                        <div class="form-group">
                          <span id="span-saldo_cliente"></span>
                        </div>
                      </div>
                    </div>

                    <div class="col-xs-12 col-sm-12 col-md-12 mt-3 <?php echo $this->user->Nu_Tipo_Privilegio_Acceso==1 ? 'd-none' : ''; ?>">
                      <h3><span id="span-total_cantidad_items" class="badge badge-danger"></span> Productos <button type="button" id="btn-excel_order_tracking" class="btn btn-default" alt="Orden Tracking" title="Orden Tracking" href="javascript:void(0)" onclick="generarExcelOrderTracking(1)" data-id_pedido="">Descargar &nbsp;<i class="fa fa-file-excel text-success"></i></button></h3>

                      <div class="table-responsive div-Compuesto">
                        <table id="table-Producto_Enlace" class="table table-bordered table-hover">
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
                              <th class="text-right">T. Producción</th>
                              <th class="text-right">C. Delivery</th>
                              <th class="text-right">fecha_Entrega</th>
                              <th class="text-right">Supplier</th><!--proveedor-->
                              <th class="text-right">Phone_Image_Supplier</th><!--celular imagen de tarjeta de presentación-->
                              <th class="text-right"></th>
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
          <input type="hidden" id="proveedor-correlativo" name="proveedor-correlativo" class="form-control">

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
          <input type="hidden" id="proveedor-correlativo" name="proveedor-correlativo" class="form-control">

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
          <input type="hidden" id="documento-correlativo" name="documento-correlativo" class="form-control">
          <div class="col-sm-12">
            <label>Documento</label>
            <div class="form-group">
              <input class="form-control" id="image_documento" name="image_documento" type="file" accept="application/msword, application/vnd.ms-excel, application/pdf, .csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"></input>
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

<!-- modal pago 30% cliente -->
<div class="modal fade modal-pago_cliente_30" id="modal-pago_cliente_30">
  <?php $attributes = array('id' => 'form-pago_cliente_30'); echo form_open('', $attributes); ?>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body" id="modal-body-pago_cliente_30">
        <div class="row">
          <input type="hidden" id="pago_cliente_30-id_cabecera" name="pago_cliente_30-id_cabecera" class="form-control">

          <div class="col-12 col-sm-12">
            <label>Voucher pago 30%</label>
            <div class="form-group">
              <input class="form-control" id="pago_cliente_30" name="pago_cliente_30" type="file" accept="image/*"></input>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-sm-3">
            <label class="fw-bold mb-2">Empresa <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <select name="ID_Pais_30_Cliente" id="cbo-ID_Pais_30_Cliente" class="form-control">
                <option value="0" selected="selected">- Seleccionar -</option>
                <option value="1">Perú</option>
                <option value="55">China</option>
              </select>
            </div>
            <span class="help-block text-danger" id="error"></span>
          </div>

          <div class="col-6 col-sm-3">
            <label>F. Pago <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" id="modal-Fe_Pago_30_Cliente" name="Fe_Pago_30_Cliente" class="form-control input-datepicker-pay required" value="<?php echo dateNow('fecha_actual_dmy'); ?>">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
          
          <div class="col-6 col-sm-3">
            <label>Importe <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" inputmode="decimal" class="form-control input-decimal" id="modal-Ss_Pago_30_Cliente" name="Ss_Pago_30_Cliente" value="" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-sm-3 div-modal_datos_tarjeta_credito">
            <label>Nro. Operación <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" inputmode="numeric" id="modal-Nu_Operacion_Pago_30_Cliente" name="Nu_Operacion_Pago_30_Cliente" class="form-control input-number" value="" maxlength="10" placeholder="No. Operación" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-outline-danger btn-lg col" data-dismiss="modal">Cancelar</button>
        <button type="submit" id="btn-guardar_pago_cliente_30" class="col btn btn-success btn-lg btn-block">Guardar</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div>
<!-- modal pago 30% cliente -->

<!-- modal pago 100% cliente -->
<div class="modal fade modal-pago_cliente_100" id="modal-pago_cliente_100">
  <?php $attributes = array('id' => 'form-pago_cliente_100'); echo form_open('', $attributes); ?>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body" id="modal-body-pago_cliente_100">
        <div class="row">
          <input type="hidden" id="pago_cliente_100-id_cabecera" name="pago_cliente_100-id_cabecera" class="form-control">

          <div class="col-sm-12">
            <label>Voucher pago 70%</label>
            <div class="form-group">
              <input class="form-control" id="pago_cliente_100" name="pago_cliente_100" type="file" accept="image/*"></input>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-sm-3">
            <label class="fw-bold mb-2">País <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <select name="ID_Pais_100_Cliente" id="cbo-ID_Pais_100_Cliente" class="form-control">
                <option value="0" selected="selected">- Seleccionar -</option>
                <option value="1">Perú</option>
                <option value="55">China</option>
              </select>
            </div>
            <span class="help-block text-danger" id="error"></span>
          </div>

          <div class="col-6 col-sm-3">
            <label>F. Pago <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" id="modal-Fe_Pago_100_Cliente" name="Fe_Pago_100_Cliente" class="form-control input-datepicker-pay required" value="<?php echo dateNow('fecha_actual_dmy'); ?>">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-sm-3">
            <label>Importe <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" inputmode="decimal" class="form-control input-decimal" id="modal-Ss_Pago_100_Cliente" name="Ss_Pago_100_Cliente" value="" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-sm-3 div-modal_datos_tarjeta_credito">
            <label>Opcional <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" inputmode="numeric" id="modal-Nu_Operacion_Pago_100_Cliente" name="Nu_Operacion_Pago_100_Cliente" class="form-control input-number" value="" maxlength="10" placeholder="No. Operación" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-outline-danger btn-lg col" data-dismiss="modal">Cancelar</button>
        <button type="submit" id="btn-pago_cliente_100" class="col btn btn-success btn-lg btn-block">Guardar</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div>
<!-- modal pago 100% cliente -->

<!-- modal pago servicio cliente -->
<div class="modal fade modal-pago_cliente_servicio" id="modal-pago_cliente_servicio">
  <?php $attributes = array('id' => 'form-pago_cliente_servicio'); echo form_open('', $attributes); ?>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body" id="modal-body-pago_cliente_servicio">
        <div class="row">
          <input type="hidden" id="pago_cliente_servicio-id_cabecera" name="pago_cliente_servicio-id_cabecera" class="form-control">

          <div class="col-sm-12">
            <label>Voucher pago servicio</label>
            <div class="form-group">
              <input class="form-control" id="pago_cliente_servicio" name="pago_cliente_servicio" type="file" accept="image/*"></input>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-sm-3">
            <label class="fw-bold mb-2">País <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <select name="ID_Pais_Servicio_Cliente" id="cbo-ID_Pais_Servicio_Cliente" class="form-control">
                <option value="0" selected="selected">- Seleccionar -</option>
                <option value="1">Perú</option>
                <option value="55">China</option>
              </select>
            </div>
            <span class="help-block text-danger" id="error"></span>
          </div>

          <div class="col-6 col-sm-3">
            <label>F. Pago <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" id="modal-Fe_Pago_Servicio_Cliente" name="Fe_Pago_Servicio_Cliente" class="form-control input-datepicker-pay required" value="<?php echo dateNow('fecha_actual_dmy'); ?>">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-sm-3">
            <label>Importe <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" inputmode="decimal" class="form-control input-decimal" id="modal-Ss_Pago_Servicio_Cliente" name="Ss_Pago_Servicio_Cliente" value="" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-sm-3 div-modal_datos_tarjeta_credito">
            <label>Opcional <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" inputmode="numeric" id="modal-Nu_Operacion_Pago_Servicio_Cliente" name="Nu_Operacion_Pago_Servicio_Cliente" class="form-control input-number" value="" maxlength="10" placeholder="No. Operación" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-outline-danger btn-lg col" data-dismiss="modal">Cancelar</button>
        <button type="submit" id="btn-pago_cliente_servicio" class="col btn btn-success btn-lg btn-block">Guardar</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div>
<!-- modal pago servicio cliente -->


<!-- modal pago flete -->
<div class="modal fade modal-pago_flete" id="modal-pago_flete">
  <?php $attributes = array('id' => 'form-pago_flete'); echo form_open('', $attributes); ?>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body" id="modal-body-pago_flete">
        <div class="row">
          <input type="hidden" id="pago_flete-id_cabecera" name="pago_flete-id_cabecera" class="form-control">

          <div class="col-sm-12">
            <label>Voucher</label>
            <div class="form-group">
              <input class="form-control" id="pago_flete" name="pago_flete" type="file" accept="image/*"></input>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-sm-3">
            <label class="fw-bold mb-2">País <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <select name="pago_flete-ID_Pais_Otros_Flete" id="pago_flete-ID_Pais_Otros_Flete" class="form-control">
                <option value="0" selected="selected">- Seleccionar -</option>
                <option value="1">Perú</option>
                <option value="55">China</option>
              </select>
            </div>
            <span class="help-block text-danger" id="error"></span>
          </div>

          <div class="col-6 col-sm-3">
            <label>F. Pago <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" id="pago_flete-Fe_Pago" name="pago_flete-Fe_Pago" class="form-control input-datepicker-pay required" value="<?php echo dateNow('fecha_actual_dmy'); ?>">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-sm-3">
            <label>Importe <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" inputmode="decimal" class="form-control input-decimal" id="pago_flete-Ss_Pago_Otros_Flete" name="pago_flete-Ss_Pago_Otros_Flete" value="" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-sm-3 div-modal_datos_tarjeta_credito">
            <label>Opcional <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" inputmode="numeric" id="pago_flete-Nu_Operacion_Pago_Otros_Flete" name="pago_flete-Nu_Operacion_Pago_Otros_Flete" class="form-control input-number" value="" maxlength="10" placeholder="No. Operación" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-outline-danger btn-lg col" data-dismiss="modal">Cancelar</button>
        <button type="submit" id="btn-pago_flete" class="col btn btn-success btn-lg btn-block">Guardar</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div>
<!-- modal pago flete -->

<!-- modal pago costos_origen -->
<div class="modal fade modal-costos_origen" id="modal-costos_origen">
  <?php $attributes = array('id' => 'form-costos_origen'); echo form_open('', $attributes); ?>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body" id="modal-body-costos_origen">
        <div class="row">
          <input type="hidden" id="costos_origen-id_cabecera" name="costos_origen-id_cabecera" class="form-control">

          <div class="col-sm-12">
            <label>Voucher</label>
            <div class="form-group">
              <input class="form-control" id="costos_origen" name="costos_origen" type="file" accept="image/*"></input>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-sm-3">
            <label class="fw-bold mb-2">País <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <select name="costos_origen-ID_Pais_Otros_Costo_Origen" id="costos_origen-ID_Pais_Otros_Costo_Origen" class="form-control">
                <option value="0" selected="selected">- Seleccionar -</option>
                <option value="1">Perú</option>
                <option value="55">China</option>
              </select>
            </div>
            <span class="help-block text-danger" id="error"></span>
          </div>

          <div class="col-6 col-sm-3">
            <label>F. Pago <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" id="costos_origen-Fe_Pago_Otros_Costo_Origen" name="costos_origen-Fe_Pago_Otros_Costo_Origen" class="form-control input-datepicker-pay required" value="<?php echo dateNow('fecha_actual_dmy'); ?>">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-sm-3">
            <label>Importe <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" inputmode="decimal" class="form-control input-decimal" id="costos_origen-Ss_Pago_Otros_Costo_Origen" name="costos_origen-Ss_Pago_Otros_Costo_Origen" value="" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-sm-3 div-modal_datos_tarjeta_credito">
            <label>Nro. Operación <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" inputmode="numeric" id="costos_origen-Nu_Operacion_Pago_Otros_Costo_Origen" name="costos_origen-Nu_Operacion_Pago_Otros_Costo_Origen" class="form-control input-number" value="" maxlength="10" placeholder="No. Operación" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-outline-danger btn-lg col" data-dismiss="modal">Cancelar</button>
        <button type="submit" id="btn-costos_origen" class="col btn btn-success btn-lg btn-block">Guardar</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div>
<!-- modal pago costos_origen -->

<!-- modal pago pago_fta -->
<div class="modal fade modal-pago_fta" id="modal-pago_fta">
  <?php $attributes = array('id' => 'form-pago_fta'); echo form_open('', $attributes); ?>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body" id="modal-body-pago_fta">
        <div class="row">
          <input type="hidden" id="pago_fta-id_cabecera" name="pago_fta-id_cabecera" class="form-control">

          <div class="col-sm-12">
            <label>Voucher</label>
            <div class="form-group">
              <input class="form-control" id="pago_fta" name="pago_fta" type="file" accept="image/*"></input>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-sm-3">
            <label class="fw-bold mb-2">País <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <select name="pago_fta-ID_Pais_Otros_Costo_Fta" id="pago_fta-ID_Pais_Otros_Costo_Fta" class="form-control">
                <option value="0" selected="selected">- Seleccionar -</option>
                <option value="1">Perú</option>
                <option value="55">China</option>
              </select>
            </div>
            <span class="help-block text-danger" id="error"></span>
          </div>

          <div class="col-6 col-sm-3">
            <label>F. Pago <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" id="pago_fta-Fe_Pago_Otros_Costo_Fta" name="pago_fta-Fe_Pago_Otros_Costo_Fta" class="form-control input-datepicker-pay required" value="<?php echo dateNow('fecha_actual_dmy'); ?>">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-sm-3">
            <label>Importe <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" inputmode="decimal" class="form-control input-decimal" id="pago_fta-Ss_Pago_Otros_Costo_Fta" name="pago_fta-Ss_Pago_Otros_Costo_Fta" value="" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-sm-3 div-modal_datos_tarjeta_credito">
            <label>Nro. Operación <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" inputmode="numeric" id="pago_fta-Nu_Operacion_Pago_Otros_Costo_Fta" name="pago_fta-Nu_Operacion_Pago_Otros_Costo_Fta" class="form-control input-number" value="" maxlength="10" placeholder="No. Operación" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-outline-danger btn-lg col" data-dismiss="modal">Cancelar</button>
        <button type="submit" id="btn-pago_fta" class="col btn btn-success btn-lg btn-block">Guardar</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div>
<!-- modal pago pago_fta -->

<!-- modal pago otros_cuadrilla -->
<div class="modal fade modal-otros_cuadrilla" id="modal-otros_cuadrilla">
  <?php $attributes = array('id' => 'form-otros_cuadrilla'); echo form_open('', $attributes); ?>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body" id="modal-body-otros_cuadrilla">
        <div class="row">
          <input type="hidden" id="otros_cuadrilla-id_cabecera" name="otros_cuadrilla-id_cabecera" class="form-control">

          <div class="col-6 col-sm-3 div-modal_datos_tarjeta_credito">
            <label>Nombre Gasto <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" inputmode="numeric" id="otros_cuadrilla-No_Concepto_Pago_Cuadrilla" name="otros_cuadrilla-No_Concepto_Pago_Cuadrilla" class="form-control" value="" maxlength="50" placeholder="" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-sm-12">
            <label>Voucher</label>
            <div class="form-group">
              <input class="form-control" id="otros_cuadrilla" name="otros_cuadrilla" type="file" accept="image/*"></input>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-sm-3">
            <label class="fw-bold mb-2">País <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <select name="otros_cuadrilla-ID_Pais_Otros_Cuadrilla" id="otros_cuadrilla-ID_Pais_Otros_Cuadrilla" class="form-control">
                <option value="0" selected="selected">- Seleccionar -</option>
                <option value="1">Perú</option>
                <option value="55">China</option>
              </select>
            </div>
            <span class="help-block text-danger" id="error"></span>
          </div>

          <div class="col-6 col-sm-3">
            <label>F. Pago <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" id="otros_cuadrilla-Fe_Pago_Otros_Cuadrilla" name="otros_cuadrilla-Fe_Pago_Otros_Cuadrilla" class="form-control input-datepicker-pay required" value="<?php echo dateNow('fecha_actual_dmy'); ?>">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-sm-3">
            <label>Importe <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" inputmode="decimal" class="form-control input-decimal" id="otros_cuadrilla-Ss_Pago_Otros_Cuadrilla" name="otros_cuadrilla-Ss_Pago_Otros_Cuadrilla" value="" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-sm-3 div-modal_datos_tarjeta_credito">
            <label>Nro. Operación <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" inputmode="numeric" id="otros_cuadrilla-Nu_Operacion_Pago_Otros_Cuadrilla" name="otros_cuadrilla-Nu_Operacion_Pago_Otros_Cuadrilla" class="form-control input-number" value="" maxlength="10" placeholder="No. Operación" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-outline-danger btn-lg col" data-dismiss="modal">Cancelar</button>
        <button type="submit" id="btn-otros_cuadrilla" class="col btn btn-success btn-lg btn-block">Guardar</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div>
<!-- modal pago otros_cuadrilla -->

<!-- modal pago pago_fta -->
<div class="modal fade modal-otros_costos" id="modal-otros_costos">
  <?php $attributes = array('id' => 'form-otros_costos'); echo form_open('', $attributes); ?>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body" id="modal-body-otros_costos">
        <div class="row">
          <input type="hidden" id="otros_costos-id_cabecera" name="otros_costos-id_cabecera" class="form-control">

          <div class="col-sm-12">
            <label>Voucher</label>
            <div class="form-group">
              <input class="form-control" id="otros_costos" name="otros_costos" type="file" accept="image/*"></input>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-sm-3">
            <label class="fw-bold mb-2">País <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <select name="otros_costos-ID_Pais_Otros_Costos" id="otros_costos-ID_Pais_Otros_Costos" class="form-control">
                <option value="0" selected="selected">- Seleccionar -</option>
                <option value="1">Perú</option>
                <option value="55">China</option>
              </select>
            </div>
            <span class="help-block text-danger" id="error"></span>
          </div>

          <div class="col-6 col-sm-3">
            <label>F. Pago <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" id="otros_costos-Fe_Pago_Otros_Costos" name="otros_costos-Fe_Pago_Otros_Costos" class="form-control input-datepicker-pay required" value="<?php echo dateNow('fecha_actual_dmy'); ?>">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-sm-3">
            <label>Importe <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" inputmode="decimal" class="form-control input-decimal" id="otros_costos-Ss_Pago_Otros_Costos" name="otros_costos-Ss_Pago_Otros_Costos" value="" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-sm-3 div-modal_datos_tarjeta_credito">
            <label>Nro. Operación <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" inputmode="numeric" id="otros_costos-Nu_Operacion_Pago_Otros_Costos" name="otros_costos-Nu_Operacion_Pago_Otros_Costos" class="form-control input-number" value="" maxlength="10" placeholder="No. Operación" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-outline-danger btn-lg col" data-dismiss="modal">Cancelar</button>
        <button type="submit" id="btn-otros_costos" class="col btn btn-success btn-lg btn-block">Guardar</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div>
<!-- modal pago otros_costos -->

<!-- Modal comision_trading -->
<div class="modal fade modal-comision_trading" id="modal-default">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="text-center">Comisión</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="hidden-modal-id_pedido_cabecera_comision_trading" class="form-control" autocomplete="off">
        <div class="col-xs-12">
          <label>Importe</label>
          <div class="form-group">
            <input type="text" inputmode="decimal" id="txt-modal-precio_comision_trading" class="form-control required input-decimal" maxlength="13" autocomplete="off">
            <span class="help-block text-danger" id="error"></span>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" id="btn-modal-salir" class="btn btn-danger btn-lg btn-block pull-center col" data-dismiss="modal">Salir</button>
        <button type="button" id="btn-save_comision_trading" class="btn btn-success btn-lg btn-block pull-center col">Guardar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /. Modal comision_trading -->