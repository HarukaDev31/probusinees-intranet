<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-8">
          <h1>
            <i class="<?php echo $this->MenuModel->verificarAccesoMenuCRUD()->Txt_Css_Icons; ?>" aria-hidden="true"></i> <span id="section-title"><?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?></span>
            &nbsp;<span id="span-id_pedido" class="badge badge-secondary"></span>
          </h1>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>
  <?php //array_debug($this->user);
  ?>
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body" id="container-listar">
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
                      <th>País </th>
                      <th>N° Orden</th>
                      <th>Fecha</th>
                      <!--  -->

                      <th>Servicio</th>
                      <th>Incoterms</th>
                      <?php if ($this->user->Nu_Tipo_Privilegio_Acceso == 1) { ?>
                        <th>Pagos</th><!--peru-->
                      <?php } ?>
                      <?php if ( $this->user->Nu_Tipo_Privilegio_Acceso==5) { ?>
                        <th>Peru</th><!--peru-->
                      <?php } ?>

                      <?php if ($this->user->Nu_Tipo_Privilegio_Acceso == 1) { ?>
                        <th>China</th>

                      <?php } ?>
                      <?php if ($this->user->Nu_Tipo_Privilegio_Acceso != 1) { ?>
                        <th class="no-sort">Estado Orden </th>

                      <?php } ?>

                      <th>Ver</th>

                        <th class="no-sort">Descarga</th>

                      <th>Avance</th>
                      <!-- <th>Status</th> -->
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

                  <?php
                  $sClassOcultar = '';
                  if ($this->user->Nu_Tipo_Privilegio_Acceso == 2 || $this->user->Nu_Tipo_Privilegio_Acceso == 5) {
                    $sClassOcultar = 'd-none';
                  }

                  ?>
                  <div class="col-6 col-sm-6 col-md-6 <?php echo $sClassOcultar; ?>">
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

                  <div class="col-6 col-sm-6 col-md-6 <?php echo $sClassOcultar; ?>">
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

                  <div class="col-6 col-sm-12 col-md-6 text-left <?php echo $sClassOcultar; ?>">
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

                  <div class="col-6 col-sm-12 col-md-6 text-left <?php echo $sClassOcultar; ?>">
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

                  <div class="col-xs-12 col-sm-12 col-md-12 mt-3 <?php echo $this->user->Nu_Tipo_Privilegio_Acceso == 1 ? '' : 'd-none'; ?>">
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

                  <div class="col-xs-12 col-sm-12 col-md-12 mt-3 <?php echo $this->user->Nu_Tipo_Privilegio_Acceso == 1 ? 'd-none' : ''; ?>">
                    <h3><span id="span-total_cantidad_items" class="badge badge-danger"></span> Productos <button type="button" id="btn-excel_order_tracking" class="btn btn-default" alt="Orden Tracking" title="Orden Tracking" href="javascript:void(0)" onclick="generarExcelOrderTracking(1)" data-id_pedido="">Descargar &nbsp;<i class="fa fa-file-excel text-success"></i></button></h3>

                    <div class="table-responsive div-Compuesto tableFixHeadV2">
                      <table id="table-Producto_Enlace" class="table table-bordered table-hover">
                        <thead class="thead-light">
                          <tr>
                            <th style='display:none;' class="text-left">ID</th>
                            <th class="text-left" width="10%">Product_Photo</th>
                            <th class="text-left">Product_Name</th>
                            <th class="text-right">Qty</th>
                            <th class="text-right">Price</th>
                            <th class="text-right">Amount</th>
                            <!--<th class="text-right">Deposit_#1</th>-->
                            <!--<th class="text-right">Balance</th>-->
                            <!--<th class="text-right">Deposit_#2</th>-->
                            <th class="text-right">T. Producción</th>
                            <!--<th class="text-right">C. Delivery</th>--><!-- cabecera costo de delivery por proveedor no es por item-->
                            <th class="text-center">fecha_Entrega</th>
                            <th class="text-center">Change Supplier</th>
                            <th class="text-center">Eliminar Item</th>
                            <!--<th class="text-right">Supplier</th>--><!--proveedor-->
                            <!--<th class="text-right">Phone_Image_Supplier</th>--><!--celular imagen de tarjeta de presentación-->
                            <!--<th class="text-right"></th>-->
                          </tr>
                        </thead>
                        <tbody>
                        </tbody>
                      </table>
                    </div>

                    <div class="table-responsive div-Producto_Recepcion_Carga tableFixHeadV2">
                      <table id="table-Producto_Recepcion_Carga" class="table table-bordered table-hover">
                        <thead class="thead-light">
                          <tr>
                            <th style='display:none;' class="text-left">ID</th>
                            <th class="text-center" width="50px">Product_Photo</th>
                            <th class="text-center">Product_Name</th>
                            <th class="text-center" width="150px">Qty Total</th>
                            <th class="text-center">Nro. Cajas</th>
                            <th class="text-center">Estado</th>
                          </tr>
                        </thead>
                        <tbody>
                        </tbody>
                      </table>
                    </div>

                    <div class="table-responsive div-Invoice_Proveedor tableFixHeadV2">
                      <table id="table-Invoice_Proveedor" class="table table-bordered table-hover">
                        <thead class="thead-light">
                          <tr>
                            <th style='display:none;' class="text-left">ID</th>
                            <th class="text-center" width="50px">Supplier</th>
                            <th class="text-center">Invoice y PL</th>
                            <th class="text-center"></th>
                          </tr>
                        </thead>
                        <tbody>
                        </tbody>
                      </table>
                    </div>

                    <div class="table-responsive div-Pago_Proveedor tableFixHeadV2">
                      <table id="table-Pago_Proveedor" class="table table-bordered table-hover">
                        <thead class="thead-light">
                          <tr>
                            <th style='display:none;' class="text-left">ID</th>
                            <th class="text-center">Supplier</th>
                            <!--<th class="text-center">Cuenta Bancaria</th>-->
                            <th class="text-center">Importe</th>
                            <th class="text-center">voucher</th>
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
            <div class="card-body" id="container-ver">
              <h1 class="m-0 p-0">Avances</h1>
              <div class="separator-line"></div>
              <?php
              if ($this->user->Nu_Tipo_Privilegio_Acceso == 1) {
              ?>
                <div class="" id="cotizacionOrdenContainer">
                  <span>Consolidado:#</span>
                  <input class="ml-1" style="width: 100px" id="consolidadoOrden" type="number" class="form-control" value="0" />
                </div>
                <?php
                }
                ?>  
              <div id="steps" class="w-100 pt-2" style="height: 100%;">
                <div class="spinner-border text-primary" id="loading-steps" role="status">
                </div>
              </div>
              <div class="steps-buttons row"></div>
            </div>
            <div class="card-body" id="container_orden-compra">
              <div class="orden-compra_header_china"></div>
              <div class="orden-compra_header row">
                <div class="col-12 col-md-3">
                  Imagen
                </div>
                <div class="col-12 col-md-2">
                  Nombre
                </div>
                <div class="col-12 col-md-2">
                  Cantidad
                </div>
                <div class="col-12 col-md-3">
                  Caracteristicas
                </div>
                <div class="col-12 col-md-2">
                  Link
                </div>
              </div>
            </div>
            <div class="card-body" id="container-rotulado">
            </div>
            <div class="card-body" id="container-pagos">
              <div class="row w-100 d-flex justify-content-between" id="pagos-header">
                <div class="col-12 col-md-3 d-flex align-items-center">
                  <label>ORDEN TOTAL</label>
                </div>
                <div class="col-12 col-md-3">
                  <span class="pagos-header-input" id="orden_total">$0</span>
                </div>

                <div class="col-12 col-md-3 d-flex align-items-center">
                  <label>PAGO CLIENTE:</label>
                </div>
                <div class="col-12 col-md-3">
                  <span class="pagos-header-input" id="pago_cliente">$0</span>
                </div>
              </div>
              <div class="row separator-line"></div>
              <div class="w-100 row" id="pagos-body">
                <form class="row w-100" id="pagos-form">
                  <!-- <div class="first-column col-12 col-md-6">
                    <div class="pago row" id="pago-garantia-container">
                      <div class="col-12 col-md-2 d-flex align-items-center justify-content-center ">
                        <label>PAGO GARANTIA</label>

                        <input type="hidden" name="pago-garantia_URL" id="pago-garantia_URL" />
                      </div>
                      <div class="col-12 col-md-10 d-flex flex-row align-items-center" id="pago-garantia-div">
                        <input type="file" name="pago-garantia" id="pago-garantia" class="" />
                        <input type="number" name="pago-garantia-value" id="pago-garantia-value" class="form-control" />
                      </div>
                    </div>
                    <div class="pago row" id="pago-1-container">
                      <div class="col-12 col-md-2 d-flex align-items-center justify-content-center ">
                        <label>PAGO 1:</label>
                        <input type="hidden" name="pago-1_URL" id="pago-1_URL" />
                      </div>
                      <div class="col-12 col-md-10 d-flex flex-row align-items-center" id="pago-1-div">
                        <input type="file" name="pago-1" id="pago-1" class="" />
                        <input type="number" name="pago-1-value" id="pago-1-value" class="form-control" />
                      </div>
                    </div>
                    <div class="pago row" id="pago-2-container">
                      <div class="col-12 col-md-2 d-flex align-items-center justify-content-center ">
                        <label>PAGO 2:</label>
                        <input type="hidden" name="pago-2_URL" id="pago-2_URL" >

                      </div>
                      <div class="col-12 col-md-10 d-flex flex-row align-items-center " id="pago-2-div">
                        <input type="file" name="pago-2" id="pago-2" class="" />
                        <input type="number" name="pago-2-value" id="pago-2-value" class="form-control"/ />
                      </div>
                    </div>
                    <div class="pago row  form-group col-12 col-md-12 d-flex flex-row align-items-center" id="pago-3-div">
                      <div class="conditional-field">
                        <label>PAGO 3:</label>
                        <label class="switch">
                          <input type="checkbox" id="pago3_URL_switch">
                          <span class="slider"></span>
                        </label>
                        </div>
                      </div>
                    <div class="pago row  form-group col-12 col-md-12 d-flex flex-row align-items-center" id="pago-4-div">
                      <div class="conditional-field">
                        <label>PAGO 4:</label>
                        <label class="switch">
                          <input type="checkbox" id="pago4_URL_switch">
                          <span class="slider"></span>
                        </label>
                      </div>
                    </div>
                  </div>
                  <div class="col-12 col-md-6">
                    <div class="form-group" id="liquidacion-container">
                      <label>LIQUIDACION:</label>
                      <input type="hidden" name="liquidacion_URL" id="liquidacion_URL" />
                      <input type="file" name="liquidacion" id="liquidacion" />
                    </div>
                    <div class="form-group">
                      <label>NOTAS:</label>
                      <textarea class="form-control" name="notas-pagos" id="notas-pagos"></textarea>
                    </div>
                  </div> -->

                </form>

              </div>
              <div class="row" id="pagos-buttons"></div>

            </div>
            <div class="card-body" id="container-coordination">

            </div>
            <form class="card-body" id="table-elegir_productos_proveedor">
            </form>
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
  <?php $attributes = array('id' => 'form-agregar_pago_proveedor');
  echo form_open('', $attributes); ?>
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

          <div class="col-12 col-sm-6 text-center d-none">
            <label>Amount</label>
            <div class="form-group">
              <input type="text" inputmode="decimal" id="amount_proveedor" name="amount_proveedor" class="form-control input-decimal required" placeholder="" maxlength="16" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <!--<div class="col-12 col-sm-6 position-relative text-center ps-4 pe-3 pe-sm-0">-->
          <div class="col-12 col-sm-12">
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
  <?php $attributes = array('id' => 'form-agregar_inspeccion');
  echo form_open('', $attributes); ?>
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
            <label><strong>Inspección</strong></label>
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
  <?php $attributes = array('id' => 'form-documento_entrega');
  echo form_open('', $attributes); ?>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body" id="modal-body-documento_entrega">
        <div class="row">
          <input type="hidden" id="documento-id_cabecera" name="documento-id_cabecera" class="form-control">
          <input type="hidden" id="documento-correlativo" name="documento-correlativo" class="form-control">
          <div class="col-sm-12">
            <label>Invoice and PL</label>
            <div class="form-group">
              <input class="form-control" id="image_documento" name="image_documento" type="file" accept="application/msword, application/vnd.ms-excel, application/pdf, .csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"></input>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-sm-12">
            <label>Invoice Detail</label>
            <div class="form-group">
              <input class="form-control" id="image_documento_detalle" name="image_documento_detalle" type="file" accept="application/msword, application/vnd.ms-excel, application/pdf, .csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"></input>
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
  <?php $attributes = array('id' => 'form-pago_cliente_30');
  echo form_open('', $attributes); ?>
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
  <?php $attributes = array('id' => 'form-pago_cliente_100');
  echo form_open('', $attributes); ?>
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
  <?php $attributes = array('id' => 'form-pago_cliente_servicio');
  echo form_open('', $attributes); ?>
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
  <?php $attributes = array('id' => 'form-pago_flete');
  echo form_open('', $attributes); ?>
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
  <?php $attributes = array('id' => 'form-costos_origen');
  echo form_open('', $attributes); ?>
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
  <?php $attributes = array('id' => 'form-pago_fta');
  echo form_open('', $attributes); ?>
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
  <?php $attributes = array('id' => 'form-otros_cuadrilla');
  echo form_open('', $attributes); ?>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body" id="modal-body-otros_cuadrilla">
        <div class="row">
          <input type="hidden" id="otros_cuadrilla-id_cabecera" name="otros_cuadrilla-id_cabecera" class="form-control">

          <div class="col-6 col-sm-3 div-modal_datos_tarjeta_credito">
            <label>Nombre Gasto <span class="label-advertencia text-danger"> *</span></label>
            <div class="form-group">
              <input type="text" inputmode="text" id="otros_cuadrilla-No_Concepto_Pago_Cuadrilla" name="otros_cuadrilla-No_Concepto_Pago_Cuadrilla" class="form-control" value="" maxlength="50" placeholder="" autocomplete="off">
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
  <?php $attributes = array('id' => 'form-otros_costos');
  echo form_open('', $attributes); ?>
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

<!-- Modal proveedor -->
<div class="modal fade modal-proveedor" id="modal-default">
  <?php $attributes = array('id' => 'form-proveedor');
  echo form_open('', $attributes); ?>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="text-center"><strong>Proveedor</strong></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <input type="hidden" name="proveedor-ID_Entidad" class="form-control" autocomplete="off">
          <input type="hidden" name="proveedor-ID_Pedido_Detalle_Producto_Proveedor" class="form-control" autocomplete="off">

          <div class="col-6 col-lg-8">
            <label>Vendedor</label>
            <div class="form-group">
              <input type="text" name="proveedor-No_Contacto" class="form-control required" placeholder="Ingresar" maxlength="100" autocomplete="off">
              <!--<input type="text" name="proveedor-No_Wechat" class="form-control required" placeholder="Ingresar" maxlength="100" autocomplete="off">-->
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-4">
            <label>Rubro</label>
            <div class="form-group">
              <input type="text" name="proveedor-No_Rubro" class="form-control" placeholder="Opcional" maxlength="50" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-8">
            <label>Titular</label>
            <div class="form-group">
              <input type="text" name="proveedor-No_Titular_Cuenta_Bancaria" placeholder="Ingresar" class="form-control" maxlength="100" autocomplete="off">
              <!--<input type="text" name="proveedor-No_Cuenta_Bancaria" placeholder="Ingresar" class="form-control" maxlength="100" autocomplete="off">-->
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-4">
            <label>Importe 1</label>
            <div class="form-group">
              <input type="text" name="proveedor-Ss_Pago_Importe_1" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-6">
            <label>Payment</label>
            <div class="form-group">
              <select id="cbo-proveedor-Nu_Tipo_Pay_Proveedor_China" name="proveedor-Nu_Tipo_Pay_Proveedor_China" class="form-control" style="width: 100%;"></select>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-6">
            <label>Cuenta / ID</label>
            <div class="form-group">
              <input type="text" name="proveedor-No_Cuenta_Bancaria" placeholder="Ingresar" class="form-control" maxlength="100" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-12">
            <label>QR</label>
            <div class="form-group">
              <input class="form-control" id="proveedor-foto_proveedor_pay_qr" name="proveedor-foto_proveedor_pay_qr" type="file" accept="image/*">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-12 div-banco_china">
            <label>Banco</label>
            <div class="form-group">
              <input type="text" name="proveedor-No_Banco_China" placeholder="Ingresar" class="form-control" maxlength="100" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" id="btn-modal-salir" class="btn btn-danger btn-lg btn-block pull-center col" data-dismiss="modal">Salir</button>
        <button type="button" id="btn-save_proveedor" class="btn btn-success btn-lg btn-block pull-center col">Guardar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div><!-- /. Modal proveedor -->

<!-- Modal booking -->
<div class="modal fade modal-booking" id="modal-default">
  <?php $attributes = array('id' => 'form-booking');
  echo form_open('', $attributes); ?>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="text-center">Reserva de Booking</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <input type="hidden" name="booking-ID_Pedido_Cabecera" class="form-control" autocomplete="off">

          <div class="col-12 col-lg-4">
            <label>Cajas Total</label>
            <div class="form-group">
              <input type="text" name="booking-Qt_Caja_Total_Booking" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-12 col-lg-4">
            <label>CBM Total</label>
            <div class="form-group">
              <input type="text" name="booking-Qt_Cbm_Total_Booking" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-12 col-lg-4">
            <label>Peso Total</label>
            <div class="form-group">
              <input type="text" name="booking-Qt_Peso_Total_Booking" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" id="btn-modal-salir" class="btn btn-danger btn-lg btn-block pull-center col" data-dismiss="modal">Salir</button>
        <button type="button" id="btn-save_booking" class="btn btn-success btn-lg btn-block pull-center col">Guardar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div><!-- /. Modal booking -->

<!-- modal fecha_entrega_shipper -->
<div class="modal fade modal-fecha_entrega_shipper" id="modal-fecha_entrega_shipper">
  <?php $attributes = array('id' => 'form-fecha_entrega_shipper');
  echo form_open('', $attributes); ?>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body" id="modal-body-fecha_entrega_shipper">
        <div class="row">
          <input type="hidden" id="despacho-id_cabecera" name="despacho-id_cabecera" class="form-control">
          <input type="hidden" id="despacho-correlativo" name="despacho-correlativo" class="form-control">
          <div class="col-sm-12">
            <label>F. Entrega</label>
            <div class="form-group">
              <input type="text" name="despacho-Fe_Entrega_Shipper_Forwarder" class="form-control input-report required" value="<?php echo dateNow('fecha_actual_dmy'); ?>">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-outline-danger btn-lg col" data-dismiss="modal">Cancelar</button>
        <button type="submit" id="btn-guardar_fecha_entrega_shipper" class="col btn btn-success btn-lg btn-block">Guardar</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div>
<!-- /.modal agregar fecha_entrega_shipper -->

<!-- modal cambio_item_proveedor -->
<div class="modal fade modal-cambio_item_proveedor" id="modal-cambio_item_proveedor">
  <?php $attributes = array('id' => 'form-cambio_item_proveedor');
  echo form_open('', $attributes); ?>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body" id="modal-body-cambio_item_proveedor">
        <div class="row">
          <input type="hidden" id="cambio_item_proveedor-id_item" name="cambio_item_proveedor-id_item" class="form-control">
          <input type="hidden" id="cambio_item_proveedor-id_cabecera" name="cambio_item_proveedor-id_cabecera" class="form-control">
          <div id="card1" class="card border-0 rounded shadow-sm mt-3">
            <div class="row">
              <div class="col-sm-12">
                <div class="card-body pt-3">
                  <div class="row">
                    <div class="col-11 col-sm-11 col-md-11 col-lg-11 mb-0 mb-sm-0">
                      <h6 class="text-left card-title mb-2 pt-0" style="text-align: left;"><span class="fw-bold" style="font-weight: bold;">Imagen<span class="label-advertencia text-danger"> *</span></span></h6>
                      <div class="form-group">
                        <input class="form-control" name="voucher[1][]" type="file" accept="image/*" multiple="">
                        <span class="help-block text-danger" id="error"></span>
                      </div>
                    </div>

                    <div class="col-1 col-sm-1 col-md-1 col-lg-1">
                      <span class="fw-bold" style="font-weight: bold;">&nbsp;</span>
                      <div class="d-grid gap">
                        <button type="button" id="btn-quitar_item_1" class="btn btn-outline-danger btn-quitar_item col" data-id="1">X</button>
                      </div>
                    </div>
                    <div class="col-6 col-sm-3 col-md-3 col-lg-2 mb-0 mb-sm-0">
                      <h6 class="card-title mb-2" style="font-weight:bold"><span class="fw-bold">Precio<span class="label-advertencia text-danger"> *</span></span></h6>
                      <div class="form-group">
                        <input type="text" id="modal-precio1" data-correlativo="1" inputmode="decimal" name="addProducto[1][precio]" class="arrProducto form-control required precio input-decimal" placeholder="" value="" autocomplete="off">
                        <span class="help-block text-danger" id="error"></span>
                      </div>
                    </div>
                    <div class="col-6 col-sm-3 col-md-3 col-lg-2 mb-0 mb-sm-0">
                      <h6 class="card-title mb-2" style="font-weight:bold"><span class="fw-bold">moq<span class="label-advertencia text-danger"> *</span></span></h6>
                      <div class="form-group">
                        <input type="text" id="modal-moq1" data-correlativo="1" inputmode="decimal" name="addProducto[1][moq]" class="arrProducto form-control required moq input-decimal" placeholder="" value="" autocomplete="off">
                        <span class="help-block text-danger" id="error"></span>
                      </div>
                    </div>
                    <div class="col-6 col-sm-3 col-md-3 col-lg-2 mb-0 mb-sm-0">
                      <h6 class="card-title mb-2" style="font-weight:bold"><span class="fw-bold">Pcs/Caja<span class="label-advertencia text-danger"> *</span></span></h6>
                      <div class="form-group">
                        <input type="text" id="modal-qty_caja1" data-correlativo="1" inputmode="decimal" name="addProducto[1][qty_caja]" class="arrProducto form-control required qty_caja input-decimal" placeholder="" value="" autocomplete="off">
                        <span class="help-block text-danger" id="error"></span>
                      </div>
                    </div>
                    <div class="col-6 col-sm-3 col-md-3 col-lg-2 mb-0 mb-sm-0">
                      <h6 class="card-title mb-2" style="font-weight:bold"><span class="fw-bold">cbm<span class="label-advertencia text-danger"> *</span></span></h6>
                      <div class="form-group">
                        <input type="text" id="modal-cbm1" data-correlativo="1" inputmode="decimal" name="addProducto[1][cbm]" class="arrProducto form-control required input-decimal" cbm="" placeholder="" value="" autocomplete="off">
                        <span class="help-block text-danger" id="error"></span>
                      </div>
                    </div>
                    <div class="col-12 col-sm-3 col-md-3 col-lg-2 mb-3 mb-sm-3">
                      <h6 class="card-title mb-2" style="font-weight:bold"><span class="fw-bold">T. Producción</span></h6>
                      <input type="text" inputmode="numeric" id="modal-delivery1" name="addProducto[1][delivery]" class="arrProducto form-control input-number" placeholder="" minlength="1" maxlength="90" autocomplete="off">
                    </div>
                    <div class="col-12 col-sm-3 col-md-3 col-lg-2 mb-3 mb-sm-3">
                      <h6 class="card-title mb-2" style="font-weight:bold"><span class="fw-bold">C. Delivery</span></h6>
                      <input type="text" inputmode="decimal" id="modal-costo_delivery1" name="addProducto[1][costo_delivery]" class="arrProducto form-control input-decimal" placeholder="" minlength="1" maxlength="90" autocomplete="off">
                    </div>
                    <div class="col-sm-12 mb-1">
                      <h6 class="card-title mb-2" style="font-weight:bold"><span class="fw-bold">Observaciones</span></h6>
                      <div class="form-group">
                        <textarea class="arrProducto form-control required nota" rows="1" placeholder="Opcional" id="modal-nota1" name="addProducto[1][nota]" style="height: 50px;"></textarea>
                        <span class="help-block text-danger" id="error"></span>
                      </div>
                    </div>
                    <div class="col-12 col-sm-6 mb-1">
                      <h6 class="card-title mb-2" style="font-weight:bold">
                        <span class="fw-bold">Nombre Proveedor</span>
                      </h6>
                      <div class="form-group">
                        <input type="text" inputmode="text" id="modal-contacto_proveedor1" name="addProducto[1][contacto_proveedor]" class="arrProducto form-control" placeholder="" maxlength="255" autocomplete="off">
                        <span class="help-block text-danger" id="error"></span>
                      </div>
                    </div>
                    <div class="col-12 col-sm-6 mb-0">
                      <h6 class="card-title mb-2" style="font-weight:bold"><span class="fw-bold">Foto Proveedor</span></h6>
                      <div class="form-group">
                        <input class="form-control" id="modal-foto_proveedor1" name="proveedor[1]" type="file" accept="image/*">
                        <span class="help-block text-danger" id="error"></span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-outline-danger btn-lg col" data-dismiss="modal">Cancelar</button>
        <button type="submit" id="btn-guardar_cambio_item_proveedor" class="col btn btn-success btn-lg btn-block">Guardar</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div>
<!-- /.modal agregar cambio_item_proveedor -->

<!-- asignar pedido personal de china -->
<?php
$attributes = array('id' => 'form-guardar_personal_china');
echo form_open('', $attributes);
?>
<div class="modal fade modal-guardar_personal_china" id="modal-guardar_personal_china">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body" id="modal-body-guardar_personal_china">
        <input type="hidden" id="txt-guardar_personal_china-ID_Pedido_Cabecera" name="guardar_personal_china-ID_Pedido_Cabecera" class="form-control form-control-lg">
        <div class="col-xs-12">
          <label>Usuario</label>
          <div class="form-group">
            <select id="cbo-guardar_personal_china-ID_Usuario" name="cbo-guardar_personal_china-ID_Usuario" class="form-control select2" style="width: 100%;">
              <option selected="selected" value="0"></option>
            </select>
            <span class="help-block text-danger" id="error"></span>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-outline-danger btn-lg col" data-dismiss="modal">Cancelar</button>
        <button type="button" id="btn-guardar_personal_china" class="col btn btn-success btn-lg btn-block">Guardar</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- ./ asignar pedido personal de china -->
<?php echo form_close(); ?>

<!-- Modal booking_consolidado -->
<div class="modal fade modal-booking_consolidado" id="modal-default">
  <?php $attributes = array('id' => 'form-booking_consolidado');
  echo form_open('', $attributes); ?>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="text-center">Reserva de Booking</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <input type="hidden" name="booking_consolidado-ID_Pedido_Cabecera" class="form-control" autocomplete="off">

          <div class="col-12 col-lg-4">
            <label># Consolidado</label>
            <div class="form-group">
              <input type="text" name="booking_consolidado-No_Numero_Consolidado" class="form-control" placeholder="Ingresar" maxlength="50" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-12 col-lg-4">
            <label>CBM Total</label>
            <div class="form-group">
              <label id="booking_consolidado-Qt_Cbm_Total_Booking">CBM Total</label>
              <!--<input type="text" name="booking_consolidado-Qt_Cbm_Total_Booking" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">-->
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" id="btn-modal-salir" class="btn btn-danger btn-lg btn-block pull-center col" data-dismiss="modal">Salir</button>
        <button type="button" id="btn-save_booking_consolidado" class="btn btn-success btn-lg btn-block pull-center col">Guardar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div><!-- /. Modal booking_consolidado -->

<!-- Modal inspeccion -->
<div class="modal fade modal-booking_inspeccion" id="modal-default">
  <?php $attributes = array('id' => 'form-booking_inspeccion');
  echo form_open('', $attributes); ?>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="text-center"><strong>Inspección</strong></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <input type="hidden" name="booking_inspeccion-ID_Pedido_Cabecera" class="form-control" autocomplete="off">
          <input type="hidden" name="booking_inspeccion-Nu_ID_Interno" class="form-control" autocomplete="off">
          <input type="hidden" name="booking_inspeccion-ID_Usuario_Interno_China" class="form-control" autocomplete="off">
          <input type="hidden" name="booking_inspeccion-sCorrelativoCotizacion" class="form-control" autocomplete="off">
          <input type="hidden" name="booking_inspeccion-Qt_Caja_Total_Booking-Actual" class="form-control" autocomplete="off">
          <input type="hidden" name="booking_inspeccion-Qt_Cbm_Total_Booking-Actual" class="form-control" autocomplete="off">
          <input type="hidden" name="booking_inspeccion-Qt_Peso_Total_Booking-Actual" class="form-control" autocomplete="off">

          <div class="col-12 col-lg-4">
            <label>Cajas Total</label>
            <div class="form-group">
              <!--<label id="booking_inspeccion-Qt_Caja_Total_Booking"></label>-->
              <input type="text" name="booking_inspeccion-Qt_Caja_Total_Booking" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-12 col-lg-4">
            <label>CBM Total</label>
            <div class="form-group">
              <!--<label id="booking_inspeccion-Qt_Cbm_Total_Booking"></label>-->
              <input type="text" name="booking_inspeccion-Qt_Cbm_Total_Booking" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-12 col-lg-4">
            <label>Peso Total</label>
            <div class="form-group">
              <!--<label id="booking_inspeccion-Qt_Peso_Total_Booking"></label>-->
              <input type="text" name="booking_inspeccion-Qt_Peso_Total_Booking" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-12 col-lg-12">
            <label>Observación</label>
            <div class="form-group">
              <input type="text" name="booking_inspeccion-No_Observacion_Inspeccion" class="form-control" placeholder="Opcional" maxlength="255" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" id="btn-modal-salir" class="btn btn-danger btn-lg btn-block pull-center col" data-dismiss="modal">Salir</button>
        <button type="button" id="btn-save_booking_inspeccion" class="btn btn-success btn-lg btn-block pull-center col">Guardar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div><!-- /. Modal booking_inspeccion -->

<!-- modal supervisar_llenado_contenedor -->
<div class="modal fade modal-supervisar_llenado_contenedor" id="modal-supervisar_llenado_contenedor">
  <?php $attributes = array('id' => 'form-supervisar_llenado_contenedor');
  echo form_open('', $attributes); ?>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body" id="modal-body-supervisar_llenado_contenedor">
        <div class="row">
          <input type="hidden" id="supervisar_llenado_contenedor-id_cabecera" name="supervisar_llenado_contenedor-id_cabecera" class="form-control">
          <input type="hidden" id="supervisar_llenado_contenedor-correlativo" name="supervisar_llenado_contenedor-correlativo" class="form-control">

          <div class="col-12 col-lg-12">
            <label>F. Entrega</label>
            <div class="form-group">
              <input type="text" name="supervisar_llenado_contenedor-Fe_Llenado_Contenedor" class="form-control input-report required" value="<?php echo dateNow('fecha_actual_dmy'); ?>">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-12 col-lg-12">
            <label>Observación</label>
            <div class="form-group">
              <input type="text" name="supervisar_llenado_contenedor-Txt_Llenado_Contenedor" class="form-control" placeholder="Opcional" maxlength="255" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-outline-danger btn-lg col" data-dismiss="modal">Cancelar</button>
        <button type="submit" id="btn-guardar_supervisar_llenado_contenedor" class="col btn btn-success btn-lg btn-block">Guardar</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div>
<!-- /.modal agregar supervisar_llenado_contenedor -->

<!-- Modal reserva_booking_trading -->
<div class="modal fade modal-reserva_booking_trading" id="modal-default">
  <?php $attributes = array('id' => 'form-reserva_booking_trading');
  echo form_open('', $attributes); ?>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="text-center"><strong>Reserva de Booking</strong></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <input type="hidden" name="reserva_booking_trading-ID_Pedido_Cabecera" class="form-control" autocomplete="off">

          <div class="col-12 col-lg-4">
            <label>CBM Total</label>
            <div class="form-group">
              <label id="reserva_booking_trading-Qt_Cbm_Total_Booking"></label>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-12 col-lg-4">
            <label>Tipo de Envío</label>
            <div class="form-group">
              <label id="reserva_booking_trading-Nu_Tipo_Transporte_Maritimo"></label>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-12 col-lg-4">
            <label>Shipper</label>
            <div class="form-group">
              <select id="cbo-shipper" name="reserva_booking_trading-ID_Shipper" class="form-control select2" style="width: 100%;">
                <option selected="selected" value="0"></option>
              </select>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-12 col-lg-6 div-tipo_contenedor">
            <label>Tipo Contenedor</label>
            <div class="form-group">
              <input type="text" name="reserva_booking_trading-No_Tipo_Contenedor" class="form-control" placeholder="Opcional" maxlength="255" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-12 col-lg-6">
            <label>Naviera</label>
            <div class="form-group">
              <input type="text" name="reserva_booking_trading-No_Naviera" class="form-control" placeholder="Opcional" maxlength="255" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-12 col-lg-4">
            <label>C. Días de tránsito</label>
            <div class="form-group">
              <input type="text" name="reserva_booking_trading-No_Dias_Transito" class="form-control input-number" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-12 col-lg-4">
            <label>D. Días Libres</label>
            <div class="form-group">
              <input type="text" name="reserva_booking_trading-No_Dias_Libres" class="form-control input-number" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" id="btn-modal-salir" class="btn btn-danger btn-lg btn-block pull-center col" data-dismiss="modal">Salir</button>
        <button type="button" id="btn-save_reserva_booking_trading" class="btn btn-success btn-lg btn-block pull-center col">Guardar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div><!-- /. Modal reserva_booking_trading -->

<!-- Modal costos_origen_china -->
<div class="modal fade modal-costos_origen_china" id="modal-default">
  <?php $attributes = array('id' => 'form-costos_origen_china');
  echo form_open('', $attributes); ?>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="text-center"><strong>Costos de Origen</strong></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <input type="hidden" name="costos_origen_china-ID_Pedido_Cabecera" class="form-control" autocomplete="off">

          <div class="col-6 col-lg-6">
            <label>Flete ¥</label>
            <div class="form-group">
              <input type="text" inputmode="decimal" name="costos_origen_china-Ss_Pago_Otros_Flete_China_Yuan" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-6">
            <label>Flete $</label>
            <div class="form-group">
              <input type="text" inputmode="decimal" name="costos_origen_china-Ss_Pago_Otros_Flete_China_Dolar" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-6">
            <label>Costos de Origen ¥</label>
            <div class="form-group">
              <input type="text" inputmode="decimal" name="costos_origen_china-Ss_Pago_Otros_Costo_Origen_China_Yuan" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-6">
            <label>Costos de Origen $</label>
            <div class="form-group">
              <input type="text" inputmode="decimal" name="costos_origen_china-Ss_Pago_Otros_Costo_Origen_China_Dolar" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-6">
            <label>Costos de FTA ¥</label>
            <div class="form-group">
              <input type="text" inputmode="decimal" name="costos_origen_china-Ss_Pago_Otros_Costo_Fta_China_Yuan" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-6">
            <label>Costos de FTA $</label>
            <div class="form-group">
              <input type="text" inputmode="decimal" name="costos_origen_china-Ss_Pago_Otros_Costo_Fta_China_Dolar" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-6">
            <label>
              <input type="text" inputmode="text" id="costos_origen_china-No_Concepto_Pago_Cuadrilla" name="costos_origen_china-No_Concepto_Pago_Cuadrilla" class="form-control" value="Cuadrilla ¥" maxlength="50" placeholder="" autocomplete="off">
            </label>
            <div class="form-group">
              <input type="text" inputmode="decimal" name="costos_origen_china-Ss_Pago_Otros_Cuadrilla_China_Yuan" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-6">
            <label>$</label>
            <div class="form-group">
              <input type="text" inputmode="decimal" name="costos_origen_china-Ss_Pago_Otros_Cuadrilla_China_Dolar" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-6">
            <label>Otros Costos ¥</label>
            <div class="form-group">
              <input type="text" inputmode="decimal" name="costos_origen_china-Ss_Pago_Otros_Costos_China_Yuan" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-6">
            <label>Otros Costos $</label>
            <div class="form-group">
              <input type="text" inputmode="decimal" name="costos_origen_china-Ss_Pago_Otros_Costos_China_Dolar" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" id="btn-modal-salir" class="btn btn-danger btn-lg btn-block pull-center col" data-dismiss="modal">Salir</button>
        <button type="button" id="btn-save_costos_origen_china" class="btn btn-success btn-lg btn-block pull-center col">Guardar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div><!-- /. Modal costos_origen_china -->

<!-- modal docs_exportacion -->
<div class="modal fade modal-docs_exportacion" id="modal-docs_exportacion">
  <?php $attributes = array('id' => 'form-docs_exportacion');
  echo form_open('', $attributes); ?>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body" id="modal-body-docs_exportacion">
        <div class="row">
          <input type="hidden" id="docs_exportacion-ID_Pedido_Cabecera" name="docs_exportacion-ID_Pedido_Cabecera" class="form-control">
          <input type="hidden" id="docs_exportacion-iIdTareaPedido" name="docs_exportacion-iIdTareaPedido" class="form-control">

          <div class="col-sm-12 div-docs_shipper">
            <label>Docs Shipper</label>
            <div class="form-group">
              <input class="form-control" id="docs_exportacion-Txt_Url_Archivo_Exportacion_Docs_Shipper" name="docs_exportacion-Txt_Url_Archivo_Exportacion_Docs_Shipper" type="file" accept="application/msword, application/vnd.ms-excel, application/pdf, .csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"></input>
              <a class="btn btn-link" id="docs_exportacion-Txt_Url_Archivo_Exportacion_Docs_Shipper-a" href="#" role="button">Descargar</a>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-sm-12">
            <label>Commercial Invoice</label>
            <div class="form-group">
              <input class="form-control" id="docs_exportacion-Txt_Url_Archivo_Exportacion_Commercial_Invoice" name="docs_exportacion-Txt_Url_Archivo_Exportacion_Commercial_Invoice" type="file" accept="application/msword, application/vnd.ms-excel, application/pdf, .csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"></input>
              <a class="btn btn-link" id="docs_exportacion-Txt_Url_Archivo_Exportacion_Commercial_Invoice-a" href="#" role="button">Descargar</a>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-sm-12">
            <label>Packing List</label>
            <div class="form-group">
              <input class="form-control" id="docs_exportacion-Txt_Url_Archivo_Exportacion_Packing_List" name="docs_exportacion-Txt_Url_Archivo_Exportacion_Packing_List" type="file" accept="application/msword, application/vnd.ms-excel, application/pdf, .csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"></input>
              <a class="btn btn-link" id="docs_exportacion-Txt_Url_Archivo_Exportacion_Packing_List-a" href="#" role="button">Descargar</a>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-sm-12 div-bl">
            <label>BL</label>
            <div class="form-group">
              <input class="form-control" id="docs_exportacion-Txt_Url_Archivo_Exportacion_Bl" name="docs_exportacion-Txt_Url_Archivo_Exportacion_Bl" type="file" accept="application/msword, application/vnd.ms-excel, application/pdf, .csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"></input>
              <a class="btn btn-link" id="docs_exportacion-Txt_Url_Archivo_Exportacion_Bl-a" href="#" role="button">Descargar</a>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-sm-12">
            <label>FTA</label>
            <div class="form-group">
              <input class="form-control" id="docs_exportacion-Txt_Url_Archivo_Exportacion_Fta" name="docs_exportacion-Txt_Url_Archivo_Exportacion_Fta" type="file" accept="application/msword, application/vnd.ms-excel, application/pdf, .csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"></input>
              <a class="btn btn-link" id="docs_exportacion-Txt_Url_Archivo_Exportacion_Fta-a" href="#" role="button">Descargar</a>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-outline-danger btn-lg col" data-dismiss="modal">Cancelar</button>
        <button type="submit" id="btn-guardar_docs_exportacion" class="col btn btn-success btn-lg btn-block">Guardar</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div>
<!-- /.modal agregar docs_exportacion -->

<!-- modal despacho_shipper -->
<div class="modal fade modal-despacho_shipper" id="modal-despacho_shipper">
  <?php $attributes = array('id' => 'form-despacho_shipper');
  echo form_open('', $attributes); ?>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="text-center"><strong>Despacho al Shipper / Forwarder</strong></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body" id="modal-body-despacho_shipper">
        <div class="row">
          <input type="hidden" id="despacho_shipper-ID_Pedido_Cabecera" name="despacho_shipper-ID_Pedido_Cabecera" class="form-control">

          <div class="col-12 col-lg-12">
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" id="inlineCheckbox1" value="option1">
              <label class="form-check-label" for="inlineCheckbox1">Entrega de Carga</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" id="inlineCheckbox2" value="option2">
              <label class="form-check-label" for="inlineCheckbox2">Entrega de Documentos</label>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-outline-danger btn-lg col" data-dismiss="modal">Cancelar</button>
        <button type="submit" id="btn-guardar_despacho_shipper" class="col btn btn-success btn-lg btn-block">Guardar</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div>
<!-- /.modal agregar despacho_shipper -->

<!-- Modal cliente -->
<div class="modal fade modal-revision_bl" id="modal-default">
  <?php $attributes = array('id' => 'form-revision_bl');
  echo form_open('', $attributes); ?>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="text-center"><strong>Revisión de BL</strong></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <input type="hidden" id="revision_bl-ID_Pedido_Cabecera" name="revision_bl-ID_Pedido_Cabecera" class="form-control" autocomplete="off">
          <input type="hidden" id="revision_bl-iIdTareaPedido" name="revision_bl-iIdTareaPedido" class="form-control" autocomplete="off">
          <input type="hidden" id="revision_bl-ID_Entidad" name="revision_bl-ID_Entidad" class="form-control" autocomplete="off">
          <input type="hidden" id="revision_bl-ENo_Entidad" name="revision_bl-ENo_Entidad" class="form-control" autocomplete="off">

          <div class="col-6 col-lg-12">
            <label style="font-size: 1.3rem;">Consignatario</label>
          </div>

          <div class="col-6 col-lg-4">
            <label>Empresa</label>
            <div class="form-group">
              <input type="text" name="revision_bl-No_Entidad" class="form-control required" placeholder="Ingresar" maxlength="100" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-2">
            <label>RUC</label>
            <div class="form-group">
              <input type="text" name="revision_bl-Nu_Documento_Identidad" class="form-control input-Mayuscula input-codigo_barra" placeholder="Ingresar" maxlength="11" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-6">
            <label>Dirección</label>
            <div class="form-group">
              <input type="text" name="revision_bl-Txt_Direccion_Entidad" class="form-control" placeholder="Ingresar" maxlength="100" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-12">
            <label style="font-size: 1.3rem;">Exportador</label>
          </div>

          <div class="col-12 col-lg-4">
            <label>Razón Social</label>
            <div class="form-group">
              <span id="revision_bl-exportador"></span>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-12 col-lg-4">
            <label>Dirección</label>
            <div class="form-group">
              <span id="revision_bl-exportador_direccion"></span>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-12 col-lg-4">
            <label>Shipper</label>
            <div class="form-group">
              <span id="revision_bl-shipper"></span>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-12">
            <label style="font-size: 1.3rem;">Datos de Carga</label>
          </div>

          <div class="col-12 col-lg-3">
            <label>Cajas Total</label>
            <div class="form-group">
              <span id="revision_bl-Qt_Caja_Total_Booking"></span>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-12 col-lg-3">
            <label>CBM Total</label>
            <div class="form-group">
              <span id="revision_bl-Qt_Cbm_Total_Booking"></span>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-12 col-lg-3">
            <label>Peso Total</label>
            <div class="form-group">
              <span id="revision_bl-Qt_Peso_Total_Booking"></span>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-12 col-lg-3">
            <label>Tipo de Envío</label>
            <div class="form-group">
              <span id="revision_bl-Nu_Tipo_Transporte_Maritimo"></span>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-12 col-lg-12">
            <label>Descripción BL</label>
            <div class="form-group">
              <textarea class="form-control" rows="5" placeholder="Obligatorio" name="revision_bl-Txt_Descripcion_BL_China"></textarea>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" id="btn-modal-salir" class="btn btn-danger btn-lg btn-block pull-center col" data-dismiss="modal">Salir</button>
        <button type="button" id="btn-save_revision_bl" class="btn btn-success btn-lg btn-block pull-center col">Guardar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div><!-- /. Modal revision_bl -->

<!-- modal entrega_docs_cliente -->
<div class="modal fade modal-entrega_docs_cliente" id="modal-entrega_docs_cliente">
  <?php $attributes = array('id' => 'form-entrega_docs_cliente');
  echo form_open('', $attributes); ?>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="text-center"><strong>Entrega de Docs - Cliente</strong></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body" id="modal-body-entrega_docs_cliente">
        <div class="row">
          <input type="hidden" id="entrega_docs_cliente-ID_Pedido_Cabecera" name="entrega_docs_cliente-ID_Pedido_Cabecera" class="form-control">
          <input type="hidden" id="entrega_docs_cliente-Nu_Tipo_Incoterms" name="entrega_docs_cliente-Nu_Tipo_Incoterms" class="form-control">

          <div class="col-12 col-lg-12">
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" id="entrega_docs_cliente-inlineCheckbox1" value="option1">
              <label class="form-check-label" for="entrega_docs_cliente-inlineCheckbox1">Commercial Invoice</label>
            </div>

            <div class="form-check form-check-inline div-bl-entrega_docs"><!-- SOLO SI ES CIF O DDP-->
              <input class="form-check-input" type="checkbox" id="entrega_docs_cliente-inlineCheckbox2" value="option2">
              <label class="form-check-label" for="entrega_docs_cliente-inlineCheckbox2">BL</label>
            </div>

            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" id="entrega_docs_cliente-inlineCheckbox3" value="option3">
              <label class="form-check-label" for="entrega_docs_cliente-inlineCheckbox3">FTA Detalle</label>
            </div>

            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" id="entrega_docs_cliente-inlineCheckbox4" value="option4">
              <label class="form-check-label" for="entrega_docs_cliente-inlineCheckbox4">Packing List</label>
            </div>

            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" id="entrega_docs_cliente-inlineCheckbox5" value="option5">
              <label class="form-check-label" for="entrega_docs_cliente-inlineCheckbox5">FTA</label>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-outline-danger btn-lg col" data-dismiss="modal">Cancelar</button>
        <button type="submit" id="btn-guardar_entrega_docs_cliente" class="col btn btn-success btn-lg btn-block">Guardar</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
  <?php echo form_close(); ?>
</div>
<!-- /.modal agregar despacho_shipper -->

<!-- Modal pagos_logisticos -->
<div class="modal fade modal-pagos_logisticos" id="modal-default">
  <?php $attributes = array('id' => 'form-pagos_logisticos');
  echo form_open('', $attributes); ?>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="text-center"><strong>Pagos Logísticos</strong></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <input type="hidden" name="pagos_logisticos-ID_Pedido_Cabecera" class="form-control" autocomplete="off">

          <div class="col-12 col-lg-12">
            <span>Shipper: <label id="pagos_logisticos-shipper"></label></span>
          </div>

          <div class="col-6 col-lg-3 div-pagos_logisticos-cif_ddp">
            <label>Flete ¥</label>
            <div class="form-group">
              <input type="text" inputmode="decimal" name="pagos_logisticos-Ss_Pago_Otros_Flete_China_Yuan" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-3 div-pagos_logisticos-cif_ddp">
            <label>Flete $</label>
            <div class="form-group">
              <input type="text" inputmode="decimal" name="pagos_logisticos-Ss_Pago_Otros_Flete_China_Dolar" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-4 div-pagos_logisticos-cif_ddp">
            <label>Voucher</label>
            <div class="form-group">
              <input class="form-control" id="pagos_logisticos-Txt_Url_Pago_Otros_Flete_China" name="pagos_logisticos-Txt_Url_Pago_Otros_Flete_China" type="file" accept="application/msword, application/vnd.ms-excel, application/pdf, .csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"></input>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-2 div-pagos_logisticos-cif_ddp">
            <label></label>
            <div class="form-group">
              <a class="btn btn-link" id="pagos_logisticos-Txt_Url_Pago_Otros_Flete_China-a" href="#" role="button">Descargar</a>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-3">
            <label>Costos de Origen ¥</label>
            <div class="form-group">
              <input type="text" inputmode="decimal" name="pagos_logisticos-Ss_Pago_Otros_Costo_Origen_China_Yuan" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-3">
            <label>Costos de Origen $</label>
            <div class="form-group">
              <input type="text" inputmode="decimal" name="pagos_logisticos-Ss_Pago_Otros_Costo_Origen_China_Dolar" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-4">
            <label>Voucher</label>
            <div class="form-group">
              <input class="form-control" id="pagos_logisticos-Txt_Url_Pago_Otros_Costo_Origen_China" name="pagos_logisticos-Txt_Url_Pago_Otros_Costo_Origen_China" type="file" accept="application/msword, application/vnd.ms-excel, application/pdf, .csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"></input>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-2">
            <label></label>
            <div class="form-group">
              <a class="btn btn-link" id="pagos_logisticos-Txt_Url_Pago_Otros_Costo_Origen_China-a" href="#" role="button">Descargar</a>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-3">
            <label>Costos de FTA ¥</label>
            <div class="form-group">
              <input type="text" inputmode="decimal" name="pagos_logisticos-Ss_Pago_Otros_Costo_Fta_China_Yuan" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-3">
            <label>Costos de FTA $</label>
            <div class="form-group">
              <input type="text" inputmode="decimal" name="pagos_logisticos-Ss_Pago_Otros_Costo_Fta_China_Dolar" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-4">
            <label>Voucher</label>
            <div class="form-group">
              <input class="form-control" id="pagos_logisticos-Txt_Url_Pago_Otros_Costo_Fta_China" name="pagos_logisticos-Txt_Url_Pago_Otros_Costo_Fta_China" type="file" accept="application/msword, application/vnd.ms-excel, application/pdf, .csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"></input>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-2">
            <label></label>
            <div class="form-group">
              <a class="btn btn-link" id="pagos_logisticos-Txt_Url_Pago_Otros_Costo_Fta_China-a" href="#" role="button">Descargar</a>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-3 div-pagos_logisticos-cif_ddp">
            <div class="form-group">
              <strong>
                <h6>SubTotal ¥: <label id="pagos_logisticos-subtotal-yuan"></label></h6>
              </strong>
            </div>
          </div>

          <div class="col-6 col-lg-9 div-pagos_logisticos-cif_ddp">
            <div class="form-group">
              <strong>
                <h6>SubTotal $: <label id="pagos_logisticos-subtotal-dolar"></label></h6>
              </strong>
            </div>
          </div>

          <div class="col-6 col-lg-3 div-pagos_logisticos-cif_ddp">
            <label>Cuadrilla ¥</label>
            <div class="form-group">
              <input type="text" inputmode="decimal" name="pagos_logisticos-Ss_Pago_Otros_Cuadrilla_China_Yuan" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-3 div-pagos_logisticos-cif_ddp">
            <label>Cuadrilla $</label>
            <div class="form-group">
              <input type="text" inputmode="decimal" name="pagos_logisticos-Ss_Pago_Otros_Cuadrilla_China_Dolar" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-4 div-pagos_logisticos-cif_ddp">
            <label>Voucher</label>
            <div class="form-group">
              <input class="form-control" id="pagos_logisticos-Txt_Url_Pago_Otros_Cuadrilla_China" name="pagos_logisticos-Txt_Url_Pago_Otros_Cuadrilla_China" type="file" accept="application/msword, application/vnd.ms-excel, application/pdf, .csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"></input>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-2 div-pagos_logisticos-cif_ddp">
            <label></label>
            <div class="form-group">
              <a class="btn btn-link" id="pagos_logisticos-Txt_Url_Pago_Otros_Cuadrilla_China-a" href="#" role="button">Descargar</a>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-3 div-pagos_logisticos-cif_ddp">
            <label>Otros Costos ¥</label>
            <div class="form-group">
              <input type="text" inputmode="decimal" name="pagos_logisticos-Ss_Pago_Otros_Costos_China_Yuan" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-3 div-pagos_logisticos-cif_ddp">
            <label>Otros Costos $</label>
            <div class="form-group">
              <input type="text" inputmode="decimal" name="pagos_logisticos-Ss_Pago_Otros_Costos_China_Dolar" class="form-control input-decimal" placeholder="Ingresar" maxlength="20" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-4 div-pagos_logisticos-cif_ddp">
            <label>Voucher</label>
            <div class="form-group">
              <input class="form-control" id="pagos_logisticos-Txt_Url_Pago_Otros_Costos_China" name="pagos_logisticos-Txt_Url_Pago_Otros_Costos_China" type="file" accept="application/msword, application/vnd.ms-excel, application/pdf, .csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"></input>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-2 div-pagos_logisticos-cif_ddp">
            <label></label>
            <div class="form-group">
              <a class="btn btn-link" id="pagos_logisticos-Txt_Url_Pago_Otros_Costos_China-a" href="#" role="button">Descargar</a>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>

          <div class="col-6 col-lg-3 div-pagos_logisticos-cif_ddp">
            <div class="form-group">
              <strong>
                <h6>Total ¥: <label id="pagos_logisticos-total-yuan"></label></h6>
              </strong>
            </div>
          </div>

          <div class="col-6 col-lg-9 div-pagos_logisticos-cif_ddp">
            <div class="form-group">
              <strong>
                <h6>Total $: <label id="pagos_logisticos-total-dolar"></label></h6>
              </strong>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" id="btn-modal-salir" class="btn btn-danger btn-lg btn-block pull-center col" data-dismiss="modal">Salir</button>
        <button type="button" id="btn-save_pagos_logisticos" class="btn btn-success btn-lg btn-block pull-center col">Guardar</button>
      </div>
    </div><!-- /.modal-content -->

  </div><!-- /.modal-dialog -->
  <style scoped>
    .coordination-header {
      display: flex;
      flex-direction: row;
      justify-content: space-between;
      border: 1px solid black;
      padding: 1em 2em;
    }

    .step-container {
      display: flex;
      flex-direction: column;
      border: 1px solid #ccc;
      border-radius: 1em;
      align-items: center;
      margin-bottom: 10px;
      width: 200px;
      padding: 1em 2em;
      animation: fadeIn 1s forwards;
      min-width: 200px;
      /* Inicialmente ocultar los elementos */
      opacity: 0;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
      }

      to {
        opacity: 1;
      }
    }

    /* Aplicar un retraso basado en el índice del hijo */
    .step-container:nth-child(1) {
      animation-delay: 0s;
    }

    .step-container:nth-child(2) {
      animation-delay: 0.5s;
    }

    .step-container:nth-child(3) {
      animation-delay: 1s;
    }

    .step-container:nth-child(4) {
      animation-delay: 1.5s;
    }

    .step-container:nth-child(4) {
      animation-delay: 2s;
    }

    .step-container:nth-child(5) {
      animation-delay: 2.5s;
    }

    .step-container:nth-child(6) {
      animation-delay: 3s;
    }

    .img-cuz {
      width: 100px;
      height: 100px;
    }

    .orden-compra_header div {
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .step-container-completed {
      display: flex;
      flex-direction: column;
      border: 1px solid #ccc;
      border-radius: 1em;
      align-items: center;
      margin-bottom: 10px;
      width: 200px;
      padding: 1em 2em;
      animation: fadeInBounce 1s forwards;
      min-width: 200px;
      /* Inicialmente ocultar los elementos */
      opacity: 0;
      background-color: #85C1E9;
      color: white;
    }

    @keyframes fadeInBounce {
      from {
        opacity: 0;
        transform: scale(0.5);
      }

      to {
        opacity: 1;
        transform: scale(1);
      }
    }

    #steps {
      height: 100%;
      display: flex;
      flex-direction: row;
      column-gap: 1em;
      overflow-x: auto;
    }

    .step-container-completed span {

      text-align: center;
    }

    .step-container span {
      text-align: center;
    }

    .step-container:hover {
      background-color: #f9f9f9;
    }

    .separator-line {
      width: 100%;
      height: 0.1em;
      background-color: black;
      margin: 1em 0;
    }

    /* The switch - the box around the slider */
    .switch {
      position: relative;
      display: inline-block;
      width: 60px;
      height: 34px;
    }

    /* Hide default HTML checkbox */
    .switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }

    /* The slider */
    .slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: #E74C3C;
      -webkit-transition: .4s;
      transition: .4s;
      border-radius: 1em;
      -webkit-box-shadow: 2px 2px 3px 0px rgba(209, 209, 209, 1);
      -moz-box-shadow: 2px 2px 3px 0px rgba(209, 209, 209, 1);
      box-shadow: 2px 2px 3px 0px rgba(209, 209, 209, 1);

    }

    .slider:before {
      position: absolute;
      content: "";
      height: 26px;
      width: 26px;
      left: 4px;
      bottom: 4px;
      background-color: white;
      -webkit-transition: .4s;
      transition: .4s;
      border-radius: 50%;
    }

    input:checked+.slider {
      background-color: #28B463;
    }

    input:focus+.slider {
      box-shadow: 0 0 1px #2196F3;
    }

    input:checked+.slider:before {
      -webkit-transform: translateX(26px);
      -ms-transform: translateX(26px);
      transform: translateX(26px);
    }

    input[type="file"] {
      position: relative;
    }

    input[type="file"]::file-selector-button {
      width: 136px;
      color: transparent;
    }

    /* Faked label styles and icon */
    input[type="file"]::before {
      position: absolute;
      pointer-events: none;
      top: 10px;
      left: 16px;
      height: 20px;
      width: 20px;
      content: "";
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%230964B0'%3E%3Cpath d='M18 15v3H6v-3H4v3c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2v-3h-2zM7 9l1.41 1.41L11 7.83V16h2V7.83l2.59 2.58L17 9l-5-5-5 5z'/%3E%3C/svg%3E");
    }

    input[type="file"]::after {
      position: absolute;
      pointer-events: none;
      top: 11px;
      left: 40px;
      color: #0964b0;
      content: "Upload File";
    }

    /* ------- From Step 1 ------- */

    /* file upload button */
    input[type="file"]::file-selector-button {
      border-radius: 4px;
      padding: 0 16px;
      height: 40px;
      cursor: pointer;
      background-color: white;
      border: 1px solid rgba(0, 0, 0, 0.16);
      box-shadow: 0px 1px 0px rgba(0, 0, 0, 0.05);
      margin-right: 16px;
      transition: background-color 200ms;
    }

    /* file upload button hover state */
    input[type="file"]::file-selector-button:hover {
      background-color: #f3f4f6;
    }

    /* file upload button active state */
    input[type="file"]::file-selector-button:active {
      background-color: #e5e7eb;
    }

    .btn-ver-pago {
      width: 120px;
      margin-left: 2em;
      height: 75px;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .pago {
      margin-bottom: 1em;
    }

    .pagos-header-input {
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 0.5em 2em;
      border: 1px solid #ccc;
    }
    .card-body{
      padding: 2em 1em!important;
    }
    .orden-compra_header {
      -webkit-box-shadow: 0px 5px 3px 0px rgba(209, 209, 209, 1);
      -moz-box-shadow: 0px 5px 3px 0px rgba(209, 209, 209, 1);
      box-shadow: 0px 5px 3px 0px rgba(209, 209, 209, 1);
      padding: 1em 0;
      margin-bottom: 1em;
    }

    .producto div {
      display: flex;
      justify-content: center;
      align-items: center;

    }

    .producto {
      border: 1px solid #ccc;
      border-radius: 1em;
      padding: 2em 1em;
    }

    body {
      font-family: Arial, sans-serif;
    }

    .supplier-table {
      width: 100%;
      border-collapse: collapse;
      border-spacing: 0px 0.4em;
    }

    .supplier-table th,
    .supplier-table td {
      border: none;
      padding: 10px;
      text-align: left;
    }

    .supplier-table th {
      background-color: #f2f2f2;
      -webkit-box-shadow: 0px 5px 5px 0px rgba(163, 163, 163, 1);
      -moz-box-shadow: 0px 5px 5px 0px rgba(163, 163, 163, 1);
      box-shadow: 0px 5px 5px 0px rgba(163, 163, 163, 1);
    }

    .supplier-table tr td{
      box-shadow: -1px 10px 5px -5px rgba(201,194,194,0.75);
      -webkit-box-shadow: -1px 10px 5px -5px rgba(201,194,194,0.75);
      -moz-box-shadow: -1px 10px 5px -5px rgba(201,194,194,0.75);
      border-left: none;
      border-right: none;
    }

    .supplier-info {
      background-color: #d9edf7;
      padding: 10px;
    }

    .detail {
      background-color: #f9f9f9;
    }

    .button {
      padding: 10px 20px;
      color: #fff;
      text-align: center;
      cursor: pointer;
      margin-top: 10px;
    }

    .red {
      background-color: red;
    }

    .blue {
      background-color: blue;
    }

    .green {
      background-color: green;
    }

    @media (max-width: 768px) {
      #steps {
        flex-direction: column;
        row-gap: 1em;
        align-items: center;
      }

      .container-steps {
        display: flex;
        flex-direction: column;
        border: 1px solid #ccc;
        border-radius: 1em;
        align-items: center;
        margin-bottom: 10px;
        width: 40%;
        padding: 1em 2em;
        animation: fadeIn 1s forwards;
        min-width: 250px;
        opacity: 0;
      }

      .img-cuz {
        width: 200px;
        height: 200px;
      }
    }

    .row .producto {
      -webkit-box-shadow: 5px 5px 3px 0px rgba(209, 209, 209, 1);
      -moz-box-shadow: 5px 5px 3px 0px rgba(209, 209, 209, 1);
      box-shadow: 5px 5px 3px 0px rgba(209, 209, 209, 1);
      margin-bottom: 1em;
    }

    /* }.detail{
      display: flex;
      flex-direction: row;
    }.c-imagen-column{
      width: 200px;
    }.c-nombre-column{
      width: 200px;
    }.c-qty-column{
      width: 100px;
    }.c-precio-column{
      width: 100px;
    }.c-total-column{
      width: 100px;
    }.c-tproducto-column{
      width: 100px;
    }.c-tentrega-column{
      width: 100px;
    }.c-pago1-column{
      width: 100px;
    }
    .c-pago2-column{
      width: 100px;
    }
    .c-estado-column{
      width: 100px;
    }.c-supplier-column{
      width: 300px;
    } */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
      -webkit-appearance: none;
      margin: 0;
    }

    /* Firefox */
    input[type=number] {
      -moz-appearance: textfield;
    }
  </style>
  <?php echo form_close(); ?>