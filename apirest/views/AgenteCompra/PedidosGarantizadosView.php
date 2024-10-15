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
                <div class="col-4 col-sm-3">
                  <label>F. Inicio <span class="label-advertencia text-danger"> *</span></label>
                  <div class="form-group">
                    <input type="text" id="txt-Fe_Inicio" class="form-control input-report required" value="<?php echo dateNow('month_date_ini_report'); ?>">
                    <span class="help-block text-danger" id="error"></span>
                  </div>
                </div>
                <div class="col-12 col-md-3">
                  <label>F. Fin <span class="label-advertencia text-danger"> *</span></label>
                  <div class="form-group">
                    <input type="text" id="txt-Fe_Fin" class="form-control input-report required" value="<?php echo dateNow('fecha_actual_dmy'); ?>">
                    <span class="help-block text-danger" id="error"></span>
                  </div>
                </div>
                <div class="col-12 col-md-3">
                  <label>Estado<span class="label-advertencia text-danger"> </span></label>
                  <select id="cbo-Estado" name="cbo-Estado" class="form-control select2">
                    <option value="0" selected>Todos</option>
                    <option value="1">Pendiente</option>
                    <option value="2">En Proceso</option>
                    <option value="3">Cotizado</option>
                    <option value="4">Observado</option>
                  </select>
                </div>
                <div class="col-12 col-md-3">
                  <label class="d-none d-sm-block">&nbsp;</label>
                  <button type="button" id="btn-html_reporte" class="btn btn-primary btn-block btn-reporte" data-type="html"><i class="fa fa-search"></i> Buscar</button>
                </div>
                <div class="col-12 d-flex flex-row justify-content-between">
                  <button
                    
                  id="btn-cotizacion"
                  type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productoModal">
                    Nuevo Pedido
                  </button>
                  <div class="row align-items-end justify-content-end">
                    <div class="col-12 col-md-2">
                      <span>T.C GENERAL</span>
                      <input type="text" id="txt-Tc_General" class="form-control " 
                      <?php echo $this->user->Nu_Tipo_Privilegio_Acceso != 5 ? 'disabled ' : ''; ?>
                      onchange="updateTCambio(1, this.value)"/>
                    </div>
                    <div class="col-12 col-md-2">
                      <span>T.C CONS</span>
                      <input type="text" id="txt-Tc_Consolidado" class="form-control " 
                      <?php echo $this->user->Nu_Tipo_Privilegio_Acceso != 5 ? 'disabled ' : ''; ?>
                      onchange="updateTCambio(2, this.value)"/>
                    </div>
                    <div class="col-12 col-md-2">
                      <span>T.C TRADING</span>
                      <input type="text" id="txt-Tc_Trading" class="form-control " 
                      <?php echo $this->user->Nu_Tipo_Privilegio_Acceso != 5 ? 'disabled ' : ''; ?>
                      onchange="updateTCambio(3, this.value)"/>
                    </div>
                  </div>
                </div>
              </div>
              <div class="table-responsive div-Listar">
                <table id="table-Pedidos" class="table table-bordered table-hover table-striped">
                  <thead class="thead-light">
                    <tr>
                      <th>País</th>
                      <th>Cotización</th>
                      <th>Fecha</th>
                      <th>Cliente</th>
                      <th>Empresa</th>
                      <?php if ($this->user->Nu_Tipo_Privilegio_Acceso ==1 || $this->user->Nu_Tipo_Privilegio_Acceso ==5) {?>
                      <th>Pagos</th>
                      <?php }?>

                      <?php if ($this->user->Nu_Tipo_Privilegio_Acceso != 2) {?>
                      <th class="no-sort">Encargado</th>
                      <?php }?>
                      <th>Estado</th>
                      <th class="no-sort">Ver</th>

                      <th class="no-sort">Proforma</th>
                      <!-- <th class="no-sort">Vencimiento</th> -->
                      <!-- <th class="no-sort">Importación Integral</th> -->
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
                  <input type="hidden" id="txt-ECorrelativo" name="ECorrelativo" class="form-control">

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
                        <input type="text" name="No_Entidad" class="form-control " placeholder="Ingresar" maxlength="100" autocomplete="off">
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

                    
                    <div class="col-12 col-sm-2 col-md-2  d-flex flex-column">
                      <label> Agregar Cotización</label>
                      <div id="agregarCotizaciones" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-plus"></i>
                      </div>
                    </div>
                    <div class="col-12" >
                      <div id="cotizacionExcelContainer"  class="cotizacion-container">
                        
                      </div>

                      
                    </div>

                    <!-- <div class="col-12 col-sm-4 col-md-4">
                      <label>Cotizacion</label>
                      <div class="form-group" id="container-file_cotizacion">
                        <input type="file" id="file_cotizacion" name="file_cotizacion" class="" accept="application/pdf">
                      </div>
                    </div> -->
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
                    </div>

                    <div class="col-12 col-sm-12 col-md-12 mb-3 div-articulos">
                      <div id="div-arrItemsPedidos"></div>
                    </div>

                    <div class="col-xs-12 col-sm-12 col-md-12 div-items-garantizado">
                      <h3><span id="span-total_cantidad_items" class="badge badge-danger"></span> Productos 
                      <button type="button" id="btn-add_item" class="btn btn-danger shadow">Agregar</button>
                    </h3>

                      <div class="table-responsive div-Compuesto">
                        <table id="table-Producto_Enlace" class="table table-bordered table-hover table-striped">
                          <thead class="thead-light">
                            <tr>
                              <th  class="text-left">N°</th>
                              <th class="text-left" width="65%">Imagen Producto</th>
                              <!--<th class="text-left" width="20%">Nombre</th>-->
                              <th class="text-left" width="20%">Características Producto</th>
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
                  <input type="hidden" id="txt-Item_ECorrelativo" name="Item_ECorrelativo" class="form-control">
                  <input type="hidden" id="txt-Item_Ename_producto" name="Item_Ename_producto" class="form-control">

                  <div id="div-arrItems" class="div-agregar_proveedor"></div>

                  <div class="row div-agregar_proveedor">
                    <div class="col-12 col-sm-12 col-md-12 shadow p-0" id="div-button-add_item">
                      <div class="d-grid gap">
                        <button type="button" id="btn-add_item" class="btn btn-danger btn-lg col">Otra Opción</button>
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
                  <input type="hidden" id="txt-Item_ECorrelativo_Editar" name="Item_ECorrelativo_Editar" class="form-control">
                  <input type="hidden" id="txt-Item_Ename_producto_Editar" name="Item_Ename_producto_Editar" class="form-control">

                  <div id="div-arrItemsProveedor" class="col-xs-12 col-sm-12 col-md-12">
                    <h3>Elegir Proveedor</h3>

                    <div class="table-responsive">
                      <table id="table-elegir_productos_proveedor" class="table table-bordered table-hover table-striped">
                        <!-- <thead class="thead-light">
                          <tr>
                            <th style='display:none;' class="text-left">ID</th>
                            <th class="text-left" width="">Imagen Producto</th>
                            <th class="text-left" width="">Precio $</th>
                            <th class="text-left" width="">Precio ¥</th>
                            <th class="text-left" width="">__Moq__</th>
                            <th class="text-left" width="">Qtn Cajas</th>
                            <th class="text-left" width="">CBM</th>
                            <th class="text-left" width="">Delivery</th>
                            <th class="text-left" width="">Shipping Cost</th>
                            <th class="text-left" width="">Observaciones</th>
                            <th class="text-left" width="">Nombre Proveedor</th>
                            <th class="text-left" width="">Imagen Proveedor</th>
                          </tr>
                        </thead> -->
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
      <div  id="container-pagos">
              <div class="row w-100 d-flex justify-content-between" id="pagos-header">
                <div class="col-12 col-md-2 d-flex align-items-center">
                  <label>ORDEN TOTAL</label>
                </div>
                <div class="col-12 col-md-2">
                  <span class="pagos-header-input" id="orden_total">$0</span>
                </div>

                <div class="col-12 col-md-2 d-flex align-items-center">
                  <label>PAGO CLIENTE:</label>
                </div>
                <div class="col-12 col-md-2">
                  <span class="pagos-header-input" id="pago_cliente">$0</span>
                </div>
                <div class="col-12 col-md-2 d-flex align-items-center">
                  <label>PAGO RESTANTE:</label>
                </div>
                <div class="col-12 col-md-2">
                  <span class="pagos-header-input" id="pago_restante">$0</span>
                </div>
              </div>
              <div class="row separator-line"></div>
              <div class="w-100 row" id="pagos-body">
                <form class="col-12 w-100" id="pagos-form">
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
    </div>
    <!-- /.container-fluid -->
  </section>
  <!-- /.content -->
</div>
<!-- Estructura del Modal -->
<div class="modal fade" id="modal-cotizacion" tabindex="-1" aria-labelledby="productoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="productModalLabel">Nueva Cotización</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Datos del Cliente -->
        <div class="container">
          <h5 class="text-center">Datos de Cliente</h5>
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="clientName" class="form-label">Nombres y Apellidos *</label>
                <input type="text" class="form-control" id="clientName" name="clientName">
              </div>
              <div class="mb-3">
                <label for="clientWhatsapp" class="form-label">WhatsApp *</label>
                <input type="text" class="form-control" id="clientWhatsapp" name="clientWhatsapp">
              </div>
              <div class="mb-3">
                <label for="clientEmail" class="form-label">Email *</label>
                <input type="email" class="form-control" id="clientEmail" name="clientEmail">
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="clientCountry" class="form-label">País *</label>
                <select class="custom-select" id="clientCountry" name="clientCountry">
                </select>
              </div>
              <div class="mb-3">
                <label for="clientRUC" class="form-label">RUC</label>
                <input type="text" class="form-control" id="clientRUC" name="clientRUC">
              </div>
              <div class="mb-3">
                <label for="clientCompany" class="form-label">Empresa</label>
                <input type="text" class="form-control" id="clientCompany" name="clientCompany">
              </div>
            </div>
          </div>
        </div>

        <div class="product-container position-relative">
  <div id="productsContainer">
    <!-- Productos -->
  </div>
  <button type="button" class="btn btn-outline-secondary col-12 col-md-12 my-2 position-absolute bottom-0" id="addProductBtn">Agregar Más Productos</button>
</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" id="saveBtn">Guardar</button>
      </div>
    </div>
  </div>
</div>
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
  <?php $attributes = array('id' => 'form-documento_pago_garantizado');
echo form_open('', $attributes);?>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body" id="modal-body-documento_pago_garantizado">
        <div class="row">
          <input type="hidden" id="documento_pago_garantizado-id_cabecera" name="documento_pago_garantizado-id_cabecera" class="form-control">
          <input type="hidden" id="documento_pago_garantizado-correlativo" name="documento_pago_garantizado-correlativo" class="form-control">

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
<!--- modal confirmar borrar item -->
<div class="modal fade modal-eliminar-item-pedido" id="modal-default">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Eliminar Item</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="modal-body-eliminar-item-pedido">

      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="col btn btn-outline-danger btn-lg btn-block pull-center" data-dismiss="modal">Salir</button>
        <button type="button" id="btn-eliminar-item-pedido" class="col btn btn-outline-success btn-lg btn-block pull-center" >Aceptar</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
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
  <div class="modal-dialog"><!-- modal-dialog-scrollable -->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title font-weight-bold" id="title-chat_producto">Producto</h4>
        <button type="button" id="close-modal-chat" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
      </div>
      <div class="modal-body" id="modal-body-chat_producto">
        <div class="row">
          <div class="col-md-12">
            <!-- DIRECT CHAT PRIMARY -->
            <div class="card card-success card-outline direct-chat direct-chat-primary">
              <div class="card-header">
                <h3 class="card-title">Chat</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body" id="card-chat_item">
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                <?php
$attributes = array('id' => 'form-chat_producto');
echo form_open('', $attributes);
?>
                  <input type="hidden" name="chat_producto-ID_Empresa_item" id="txt-chat_producto-ID_Empresa_item" class="form-control form-control-lg">
                  <input type="hidden" name="chat_producto-ID_Organizacion_item" id="txt-chat_producto-ID_Organizacion_item" class="form-control form-control-lg">
                  <input type="hidden" name="chat_producto-ID_Pedido_Cabecera_item" id="txt-chat_producto-ID_Pedido_Cabecera_item" class="form-control form-control-lg">
                  <input type="hidden" name="chat_producto-ID_Pedido_Detalle_item" id="txt-chat_producto-ID_Pedido_Detalle_item" class="form-control form-control-lg">

                  <div class="form-group">
                    <div class="input-group">
                      <!--<input type="text" name="message_chat" id="message_chat" placeholder="Escribir mensaje ..." class="form-control form-control-lg">-->

                      <textarea name="message_chat" id="message_chat" class="form-control" rows="1" placeholder="Opcional" style="height: auto;"></textarea>
                      <span class="input-group-append">
                        <button type="button" id="btn-enviar_mensaje" class="btn btn-success btn-lg">Enviar</button>
                      </span>
                    </div>
                    <span class="help-block text-danger" id="error"></span>
                  </div>
                <?php echo form_close(); ?>
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
<div class="modal fade" id="uploadCotizacionModal" tabindex="-1" aria-labelledby="uploadCotizacionModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="uploadCotizacionModalLabel">Subir Cotización</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="uploadCotizacionForm">
          <div class="mb-3">
            <label for="cotizacionFile" class="form-label">Selecciona un archivo de cotización (Excel)</label>
            <input type="file" class="form-control" id="cotizacionFile" accept=".xlsx, .xls" required>
          </div>
          <div class="mb-3">
            <label for="cotizacionDescription" class="form-label">Descripción (opcional)</label>
            <input type="text" class="form-control" id="cotizacionDescription" placeholder="Descripción del archivo">
          </div>
          <button type="submit" class="btn btn-primary">Subir</button>
        </form>
      </div>
    </div>
  </div>
</div>
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
<div class="modal fade modal-confirmation" id="modal-confirmation">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body" id="modal-body-confirmation">
        <div class="col-xs-12">
          <label id="modal-message-confirmation-title">¿Está seguro de asignar el pedido al personal de China?</label>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" id="btn-cancel-confirmation" class="btn btn-outline-danger btn-lg col" data-dismiss="modal">Cancelar</button>
        <button type="button" id="btn-confirmation" class="col btn btn-success btn-lg btn-block">Guardar</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>

<style scoped>
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
.separator-line {
      width: 100%;
      height: 0.1em;
      background-color: black;
      margin: 1em 0;
    }
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/* Firefox */
input[type=number] {
  -moz-appearance: textfield;
}
.supplier-list{
  background: white;
    padding: 0;
    border: 1px solid #BFC9CA;
    width: 100%;
}
.supplier-list option{
  list-style: none;
  padding: 10px;
  border-bottom: 1px solid #BFC9CA;
  cursor: pointer;
}
.supplier-list option:hover{
  background: #BFC9CA;
}
.card-cuz{
  border: 1px solid #BFC9CA;
  border-radius: 1em;
  padding: 1em;
  margin-bottom: 10px;
  background: #F2F3F4;
}.ql-editor{
  background: white!important;
  min-height: 100px;
}
.ql-container{
  width: 300px;
}
.ql-container.ql-snow {
    width: 100%!important
  }
  .preview img, .preview video {
    max-width: 100%;
    max-height: 150px;
    border: 1px solid #ddd;
    margin-top: 10px;
}
    .cotizacion-container {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .cotizacion-header {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .cotizacion-button {
            margin-top: 10px;
        }
        .empty-message {
            text-align: center;
            margin-top: 20px;
            color: #888;
        }
        .upload-payment{
    width: 180px;
    height: 180px!important;
    border-radius: 50%;
    padding: 0.5em;
    display: flex;
    flex-direction:column;
    justify-content:center;
    align-items: center;
    position: relative;
  }.not-filled:hover{
    background-color: #3498DB;
    cursor:pointer;
  }

  .not-filled:hover svg path{
    stroke: white;
  }
  @keyframes pulse {
    0% {
      transform: scale(1);
    }
    50% {
      transform: scale(1.1);
    }
    100% {
      transform: scale(1);
    }
  }
  .upload-payment:hover{
    cursor: pointer;
  }
  .filled{
    background-color: #3498DB;
    color:white;
  }
  .filled svg path{
    stroke: white;
  }.add-payments-container{
    font-weight: bold;
    font-family: Arial, sans-serif;
    display: flex;
    flex-direction: row;
    width: 100%;
    align-items: center;
  }.add-payments-button{
    display: flex;
    align-items: center;
    justify-content: center;
    width: 50px;
    height: 50px;
    border-radius: 1em;
    -webkit-box-shadow: 5px 5px 3px 0px rgba(209, 209, 209, 1);
    -moz-box-shadow: 5px 5px 3px 0px rgba(209, 209, 209, 1);
    box-shadow: 5px 5px 3px 0px rgba(209, 209, 209, 1);
  }.add-payments-button:hover{
    background-color: #3498DB;
    color:white;
    cursor: pointer;
  }

  .payment-name{
    font-size: 1.2em;
    font-weight: bold;
    padding:1em 0.5em;
    -webkit-box-shadow: 5px 5px 3px 0px rgba(209, 209, 209, 1);
    -moz-box-shadow: 5px 5px 3px 0px rgba(209, 209, 209, 1);
    box-shadow: 5px 5px 3px 0px rgba(209, 209, 209, 1);
    border-radius: 3em;
    width: 250px;
    height: 70px!important;
    text-align: center;
    word-wrap: break-word;
    word-break: break-all;
    align-self: center;
  }

  .upload-payment svg{
    width: 120px;
    height: 120px;
  }.container-div{
    -webkit-box-shadow: 5px 5px 3px 0px rgba(209, 209, 209, 1);
    -moz-box-shadow: 5px 5px 3px 0px rgba(209, 209, 209, 1);
    box-shadow: 5px 5px 3px 0px rgba(209, 209, 209, 1);
  }
  
  .remove-item{
    position: absolute;
    right: -2em;
    width: 30px;
    height: 30px;
    top:0;
    display: flex;
    border-radius: 1em;
    background: #F1948A;
    align-items: center;
    justify-content: center;
    color: white;
  }.remove-item:hover{
    cursor: pointer;
    background-color: #E74C3C;
  }.payment-container{
    display: flex;
    flex-direction: column;
    align-items: center;
    row-gap: 1em;
  }
</style>
<!-- ./ asignar pedido personal de china -->
<?php echo form_close(); ?>