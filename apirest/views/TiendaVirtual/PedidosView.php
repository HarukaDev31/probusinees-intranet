<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Main content -->
  <section class="content">
    <!-- New box-header -->
    <div class="row">
      <div class="col-xs-12">
        <div class="div-content-header">
          <h3>
            <i class="<?php echo $this->MenuModel->verificarAccesoMenuCRUD()->Txt_Css_Icons; ?>" aria-hidden="true"></i> <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?>
            <?php
            //array_debug($this->user);
            ?>
          </h3>
        </div>
      </div>
    </div>
    <!-- ./New box-header -->
    <div class="row">
      <div class="col-xs-12">
        <div class="box box-content">
          <!-- box-header -->
          <div class="box-header box-header-new div-Listar">
            <div class="row div-Filtros">
              <br>
              <?php
              if ( $this->user->ID_Usuario == 1 || $this->user->ID_Grupo == 1502 || $this->user->ID_Grupo == 1503 ){ ?>
              <div class="col-xs-12 col-sm-12 col-md-12">
                <label>Empresa</label>
                <div class="form-group">
                  <select id="cbo-filtro_empresa" name="ID_Filtro_Empresa" class="form-control select2 required" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              <?php } else { ?>
              <div class="col-xs-12 col-sm-12 col-md-12 hidden">
                <input type="text" id="cbo-filtro_empresa" name="ID_Filtro_Empresa" class="form-control" value="<?php echo $this->user->ID_Empresa; ?>">
              </div>
              <?php } ?>

              <div class="col-xs-6 col-sm-4 col-md-2">
                <div class="form-group">
                  <label>F. Inicio</label>
                  <div class="input-group date" style="width: 100%;">
                    <input type="text" id="txt-Filtro_Fe_Inicio" class="form-control date-picker-report" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                  </div>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-4 col-md-2">
                <div class="form-group">
                  <label>F. Fin</label>
                  <div class="input-group date" style="width: 100%;">
                    <input type="text" id="txt-Filtro_Fe_Fin" class="form-control date-picker-invoice txt-Filtro_Fe_Fin" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                  </div>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-12 col-sm-4 col-md-2 hidden">
                <div class="form-group">
                  <label>Documento</label>
    		  				<select id="cbo-filtros_tipos_documento" class="form-control" style="width: 100%;">
                    <option value="0">Todos</option>
                    <option value="4">Boleta</option>
                    <option value="3">Factura</option>
                  </select>
                </div>
              </div>
              
              <div class="col-xs-6 col-sm-4 col-md-2">
                <div class="form-group">
                  <label>ID Pedido</label>
                  <input type="text" inputmode="numeric" id="txt-Filtro_NumeroDocumento" class="form-control input-number" maxlength="20" placeholder="" value="" autocomplete="off">
                </div>
              </div>
              
              <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3">
                <div class="form-group">
                  <label>Estado Pedido</label>
    		  				<select id="cbo-estado_pedido" class="form-control" style="width: 100%;">
    		  				  <option value="0" selected="selected">Todos</option>
                    <?php if($this->user->ID_Pais == 1) { ?>
                      <option value="1">Pendiente</option>
                      <option value="2">Confirmado</option>
                      <option value="3">Preparando</option>
                      <option value="4">En Camino</option>
                      <option value="5">Entregado</option>
                      <option value="6">Rechazado</option>
                    <?php } ?>
                    <?php if($this->user->ID_Pais == 2) { ?>
                      <option value="1">Pendiente</option>
                      <option value="2">Confirmado</option>
                      <option value="3">Preparando</option>
                      <option value="4">En Camino</option>
                      <option value="5">Entregado</option>
                      <option value="6">Rechazado</option>
                      <option value="8">Guía Generada</option>
                      <option value="9">Recolectado</option>
                      <option value="10">Transito</option>
                      <option value="11">1 intento</option>
                      <option value="12">2 intento</option>
                      <option value="13">Devolución Pendiente</option>
                      <option value="14">Devuelto</option>
                    <?php } ?>
    		  				</select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
                <div class="form-group">
                  <label>Estado Empresa</label>
    		  				<select id="cbo-estado_pedido_empresa" class="form-control" style="width: 100%;">
    		  				  <option value="999" selected="selected">Todos</option>
    		  				  <option value="0">Pendiente</option>
    		  				  <option value="1">Completado</option>
    		  				  <option value="2">Pago realizado</option>
    		  				  <option value="3">Falsa Parada</option>
    		  				</select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-4 col-sm-4 col-md-3 hidden">
                <div class="form-group">
                  <label>Recepción</label>
    		  				<select id="cbo-estado_recepcion" class="form-control" style="width: 100%;">
    		  				  <option value="0" selected="selected">Todos</option>
    		  				  <option value="6">Delivery</option>
    		  				  <option value="7">Recojo en tienda</option>
    		  				</select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-12 col-sm-8 col-md-8">
                <label>Cliente</label>
                <div class="form-group">
                  <input type="hidden" id="txt-AID" class="form-control">
				          <span class="clearable">
                    <input type="text" id="txt-Filtro_Entidad" class="form-control autocompletar clearable" data-global-class_method="AutocompleteController/getAllClient" data-global-table="entidad" placeholder="Buscar por Nombre" value="" autocomplete="off">
                    <i class="clearable__clear">&times;</i>
                  </span>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-12 col-sm-4 col-md-4">
                <label>Ciudad</label>
                <div class="form-group">
                  <input type="text" id="txt-Filtro_Ciudad_Manual" class="form-control" placeholder="Buscar.." value="" autocomplete="on">
                  <span class="help-block" id="error"></span>
                </div>
              </div>
            </div>
              
            <div class="row div-Filtros">
              <div class="col-xs-6 col-sm-3 col-md-3">
                <div class="form-group">
                  <button type="button" id="btn-html_ventas_detalladas_generales" class="btn btn-primary btn-block btn-generar_ventas_detalladas_generales" data-type="html"><i class="fa fa-search"></i> Buscar</button>
                </div>
              </div>
              
              <div class="col-xs-6 col-sm-3 col-md-3">
                <div class="form-group">
                  <button type="button" id="btn-excel_ventas_detalladas_generales" class="btn btn-default btn-block btn-generar_ventas_detalladas_generales" data-type="excel"><i class="fa fa-file-excel-o color_icon_excel"></i> Reporte Excel</button>
                </div>
              </div>

              <div class="col-xs-6 col-sm-3 col-md-3">
                <div class="form-group">
                  <button type="button" id="btn-agregar_pedido" class="btn btn-success btn-block" onclick="agregarPedido()"><i class="fa fa-plus-circle"></i> Crear Pedido Manual</button>
                </div>
              </div>
                
              <div class="col-xs-6 col-sm-3 col-md-3">
                <button type="button" title="Importar pedidos manuales por EXCEL" alt="Importar pedidos manuales por EXCEL" class="btn btn-success btn-block" onclick="importarExcelPedidos()"><i class="fa fa-file-excel-o color-white"></i> Importar Pedidos Excel</button>
              </div>
              
              <div class="col-xs-4 col-md-4 hidden">
                <div class="form-group">
                  <button type="button" id="btn-pdf_ventas_detalladas_generales" class="btn btn-default btn-block btn-generar_ventas_detalladas_generales" data-type="pdf"><i class="fa fa-file-pdf-o color_icon_pdf"></i> PDF</button>
                </div>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div id="div-ventas_detalladas_generales" class="table-responsive div-Listar">
            <table id="table-ventas_detalladas_generales" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th class="text-center">Canal</th>
                  <th class="text-center">F. Pedido</th>
                  <th class="text-center">Pedido</th>
                  <th class="text-left">Cliente</th>
                  <th class="text-left">CIudad</th>
                  <th class="text-right">Total</th>
                  <!--<th class="text-center">F. Entrega</th>-->
                  <th class="text-center">Pago</th>
                  <th class="text-center">Transportadora</th>
                  <!--<th class="text-center">Estado Empresa</th>-->
                  <!--<th class="text-center">Recepción</th>-->
                  <th class="text-center">Estado Cliente</th>
                  <!--<th class="text-center">Ver</th>-->
                  <th class="text-center">Completar</th>
                  <th class="text-center">Novedades</th>
                  <th class="text-center">Delivery</th>
                  <th class="text-center">Call Center</th>
                  <th class="text-center">Dropshipping</th>
                  <th class="text-center">Ticket</th>
                  <th class="text-center">Transportadora</th>
                  <th class="text-center">Ganancia</th>
                  <th class="text-center">Eliminar</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
          <!-- /.box-body -->

          
          <div class="box-body div-AgregarEditar">
            <?php $attributes = array('id' => 'form-agregar_pedido'); echo form_open('', $attributes); ?>
          	  <input type="hidden" id="txt-EID_Empresa_Pedido" name="EID_Empresa_Pedido" class="form-control required">
          	  <input type="hidden" id="txt-EID_Pedido_Cabecera" name="EID_Pedido_Cabecera" class="form-control required">
          	  <input type="hidden" id="txt-ENu_Estado_Pedido_Empresa" name="ENu_Estado_Pedido_Empresa" class="form-control required">
              
              <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12"><h3 style="margin-top: 0px;"><strong>Detalles del cliente <span id="span-pedido_pendiente" class="status-pedido label label-warning">Pedido Pendiente</span><span id="span-pedido_completado" class="status-pedido label label-success">Pedido completado</span></strong></h3></div>

                <div class="col-xs-12 col-sm-12 col-md-6">
                  <label>Nombres Completo</label><span class="label-advertencia"> *</span>
                  <div class="form-group">
                    <input type="text" name="No_Entidad_Order_Address_Entry" class="form-control required" placeholder="Obligatorio" maxlength="100" autocomplete="off">
                    <span class="help-block" id="error"></span>
                  </div>
                </div>

                <div class="col-xs-12 col-sm-5 col-md-3">
                  <label>Celular</label><span class="label-advertencia"> *</span>
                  <div class="form-group">
                    <input type="text" inputmode="tel" name="Nu_Celular_Entidad_Order_Address_Entry" class="form-control required input-number" placeholder="Obligatorio" maxlength="15" autocomplete="off">
                    <span class="help-block" id="error"></span>
                  </div>
                </div>

                <div class="col-xs-12 col-sm-7 col-md-3">
                  <label>Email</label>
                  <div class="form-group">
                    <input type="text" id="txt-Txt_Email_Dropshipping" name="Txt_Email_Dropshipping" inputmode="email" placeholder="Ingresar email" class="form-control" autocomplete="off" maxlength="100">
                    <span class="help-block" id="error"></span>
                  </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-3">
                  <label>Ciudad, Estado</label><span class="label-advertencia"> *</span>
                  <div class="form-group">
                    <input type="text" name="No_Ciudad_Dropshipping" class="form-control required" placeholder="Ciudad, Estado" maxlength="100" autocomplete="on">
                    <span class="help-block" id="error"></span>
                  </div>
                </div>

                <div class="col-xs-12 col-sm-6 col-md-6">
                  <label>Dirección</label><span class="label-advertencia"> *</span>
                  <div class="form-group">
                    <input type="text" name="Txt_Direccion_Entidad_Order_Address_Entry" class="form-control required" placeholder="Calle Numero, Colonia, CP" maxlength="255" autocomplete="off">
                    <span class="help-block" id="error"></span>
                  </div>
                </div>

                <div class="col-xs-12 col-sm-6 col-md-3">
                  <label>Referencia</label>
                  <div class="form-group">
                    <input type="text" name="Txt_Direccion_Referencia_Entidad_Order_Address_Entry" class="form-control" placeholder="Opcional" maxlength="100" autocomplete="off">
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
              </div>
              
              <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12"><h3><strong>Detalles del Pedido</strong></h3></div>

                <?php
                  $sClassHiddenFechaEntregaMexico = '';
                  if($this->user->ID_Pais == 2) {
                    $sClassHiddenFechaEntregaMexico = 'hidden';
                  } ?>

                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 <?php echo $sClassHiddenFechaEntregaMexico; ?>">
                  <label>F. Entrega</label>
                  <div class="form-group">
                    <div class="input-group date" style="width:100%">
                      <input type="text" id="txt-fe_entrega" name="Fe_Entrega" class="form-control input-datepicker-today-to-more required" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                    </div>
                  </div>
                </div>

                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                  <label>Forma de Pago</label>
                  <div class="form-group">
                    <label style="cursor: pointer;">
                      <?php if($this->user->ID_Pais == 1) { ?>
                      <input type="radio" name="radio-forma_pago" class="flat-red" id="radio-forma_pago-contraentrega" value="1" checked> Contra Entrega <a href="https://docs.google.com/spreadsheets/d/1EJvCetHm-vY_QWhCX0fn_z_ofaFkkPUaHhNbDMCkVf8/edit?usp=sharing" style="" class="btn btn-danger" target="_blank"><i class="fa fa-map-marker"></i> Cobertura Perú <img src="<?php echo base_url() . 'assets/img/peru.png'; ?>" class="rounded-circle" alt="País Perú" width="16"></a>´
                      <?php } ?>
                      <?php if($this->user->ID_Pais == 2) { ?>
                      <input type="radio" name="radio-forma_pago" class="flat-red" id="radio-forma_pago-contraentrega" value="1" checked> Contra Entrega <a href="https://docs.google.com/spreadsheets/d/1Mji3l8Y-FhliWg2w6TlVHWj4ywPFucHtTHyTSrMy_cc/edit?usp=sharing" style="" class="btn btn-default" target="_blank"><i class="fa fa-map-marker"></i> Cobertura México <img src="<?php echo base_url() . 'assets/img/mexico.png'; ?>" class="rounded-circle" alt="País México" width="16"></a>´
                      <?php } ?>
                    </label>&nbsp;
                    <label style="cursor: pointer;">
                      <input type="radio" name="radio-forma_pago" class="flat-red" id="radio-forma_pago-dropshipping" value="2"> Dropshipping 
                      <?php if($this->user->ID_Pais == 1) { ?>
                        <span style="font-weight: normal;">(<?php echo $this->user->No_Signo; ?> 8.00)</span>
                        <button type="button" id="btn-cuenta_bancaria_dropshipping" class="btn btn-default" onclick="verCuentasBancariasDropshipping()"><i class="fa fa-bank"></i> Cuentas Bancarias</button>
                      <?php } ?>
                    </label>
                  </div>
                </div>
                

                <?php
                $sClassPaqueteriaMexico = '';
                if($this->user->ID_Pais == 1) {
                  $sClassPaqueteriaMexico = 'hidden';
                }
                ?>
                <div class="div-transportadora col-xs-12 col-sm-6 col-md-6 col-lg-6 <?php echo $sClassPaqueteriaMexico; ?> ">
                  <label style="font-size: 18px;">Transportadora</label>
                  <div class="form-group">
                    <label style="cursor: pointer;">
                      <input type="radio" name="radio-paqueteria" class="flat-red" id="radio-paqueteria-ecxlae" value="3" checked> Ecxlae
                    </label>&nbsp;
                    <label style="cursor: pointer;">
                      <input type="radio" name="radio-paqueteria" class="flat-red" id="radio-paqueteria-99" value="1"> 99 Minutos 
                    </label>
                    <label style="cursor: pointer;">
                      <input type="radio" name="radio-paqueteria" class="flat-red" id="radio-paqueteria-quiken" value="2"> Quiken 
                    </label>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12"><h3><strong>Transportadora</strong></h3></div>

                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                  <label>Servicio</label>
                  <div class="form-group">
                    <label style="cursor: pointer;">
                      <?php if($this->user->ID_Pais == 1) { ?>
                      <input type="radio" name="radio-servicio_transportadora" class="flat-red" id="radio-servicio_callcenter" value="1" checked> Call Center <span style="font-weight: normal;">(S/ 5.00)</span>
                      <?php } ?>
                      <?php if($this->user->ID_Pais == 2) { ?>
                      <input type="radio" name="radio-servicio_transportadora" class="flat-red" id="radio-servicio_callcenter" value="1" checked> Call Center <span style="font-weight: normal;">($ 30.00)</span>
                      <?php } ?>
                    </label>&nbsp;
                    <label style="cursor: pointer;">
                      <input type="radio" name="radio-servicio_transportadora" class="flat-red" id="radio-servicio_coordinado" value="2"> Coordinado <span style="font-weight: normal;">(GRATIS)</span>
                    </label>
                  </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 hidden">
                  <div>
                    <div class="form-group">
                      <label style="cursor: pointer;">WhastApp Call Center
                        <a href="https://api.whatsapp.com/send?phone=51954702963&text=Necesito%20ayuda%20con%20CallCenter" alt="EcxpressLae Call Center" title="EcxpressLae Call Center" target="_blank">
                          <i class="fa fa-fw fa-whatsapp fa-2x" style="color: #25d366;"></i><span>954 702 963</span>
                        </a>
                      </label>
                    </div>
                  </div>

                  <div>
                    <div class="form-group">
                      <label style="cursor: pointer;">WhastApp Coordinado
                        <a href="https://api.whatsapp.com/send?phone=51986224023&text=Necesito%20ayuda%20con%20Coordinado" alt="EcxpressLae Coordinado" title="EcxpressLae Coordinado" target="_blank">
                          <i class="fa fa-fw fa-whatsapp fa-2x" style="color: #25d366;"></i><span>986 224 023</span>
                        </a>
                      </label>
                    </div>
                  </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                  <label>Observaciones</label>
                  <div class="form-group">  
                    <!--<textarea name="Txt_Glosa" placeholder="Opcional" class="form-control"></textarea>-->
                    <div id="textarea-descripcion_item"></div>
                  </div>
                </div>
              </div>
              
              <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12"><h3><strong>Productos</strong></h3></div>

                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                  <label>Producto</label>
                  <div class="form-group">
                    <input type="hidden" id="hidden-precio_empresa_proveedor" name="AID_Empresa" class="form-control">
                    <input type="hidden" id="hidden-id_empresa_proveedor" name="AID_Empresa" class="form-control">
                    <input type="hidden" id="hidden-id_almacen_proveedor" name="AID_Almacen" class="form-control">
                    <input type="hidden" id="hidden-name_empresa_proveedor" name="" class="form-control">
                    <input type="hidden" id="hidden-name_almacen_proveedor" name="" class="form-control">
                    <input type="hidden" id="txt-AID_Producto" name="AID_Producto" class="form-control">
                    <input type="hidden" id="txt-ACodigo" name="ACodigo" class="form-control">
                    <input type="hidden" id="txt-AID_Impuesto_Cruce_Documento" name="AID_Impuesto_Cruce_Documento" class="form-control">
                    <input type="text" id="txt-ANombre" name="ANombre" class="form-control autocompletar_dropshipping" data-global-class_method="HelperDropshippingController/globalAutocomplete" placeholder="Ingresar" value="" autocomplete="off">
                    <label style="color: #7d7d7d; margin-top: .7rem !important; font-weight: normal;">Para buscar ingresar al menos 3 caracteres</label>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
                
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                  <label>Cantidad</label>
                  <div class="form-group">
                    <input type="text" id="txt-Qt_Producto_Descargar" inputmode="numeric" name="Qt_Producto_Descargar" class="form-control input-number" placeholder="Ingresar Cantidad" value="1" autocomplete="off">
                    <span class="help-block" id="error"></span>
                  </div>
                </div>

                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 hidden">
                  <label>Precio</label>
                  <div class="form-group">
                    <input type="text" id="txt-Precio_Pedido" inputmode="decimal" name="Precio_Pedido" class="form-control input-decimal" placeholder="Ingresar Precio" value="" autocomplete="off">
                    <span class="help-block" id="error"></span>
                  </div>
                </div>

                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                  <label>Total</label>
                  <div class="form-group">
                    <input type="text" id="txt-Total_Pedido" inputmode="decimal" name="Total_Pedido" class="form-control input-decimal" placeholder="Ingresar Total" value="" autocomplete="off">
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
                
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                  <label class="hidden-sm hidden-md hidden-lg">&nbsp;</label>
                  <div class="form-group">
                    <button type="button" id="btn-addProductosEnlaces" class="btn btn-primary btn-md btn-block"> Agregar producto</button>
                  </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                  <div id="div-pedido_productos" class="table-responsive">
                    <table id="table-pedido_productos" class="table table-striped table-bordered">
                      <thead>
                        <tr>
                          <th style='display:none;' class="text-left">ID</th>
                          <th class="text-left">Proveedor</th>
                          <th class="text-left">Almacén</th>
                          <th class="text-left">Nombre</th>
                          <th class="text-right">Cantidad</th>
                          <th class="text-right">Precio</th>
                          <th class="text-right">Total</th>
                          <th class="text-right">Precio Proveedor</th>
                          <th class="text-right">Total Proveedor</th>
                          <th class="text-center">Quitar</th>
                        </tr>
                      </thead>
                      <tbody></tbody>
                    </table>
                  </div>
                </div>
              </div>

      			  <div class="row">
      			    <br/>
                <div class="col-xs-6 col-md-6">
                  <div class="form-group">
                    <button type="button" id="btn-cancelar_pedido" class="btn btn-danger btn-lg btn-block">Salir</button>
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
        <!-- /.box -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </section>
  <!-- /.content -->

  <?php
  $attributes = array('id' => 'form-generar_venta');
  echo form_open('', $attributes);
  ?>
  <div class="modal modal-default fade modal-generar_venta" id="modal-generar_venta">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-header-title-generar_venta text-center"></h4>
        </div>

        <div class="modal-body">
          <input type="hidden" name="ID_Pedido_Cabecera" class="form-control">
          <input type="hidden" name="ID_Almacen" class="form-control">
          <input type="hidden" name="fTotalDocumento" class="form-control">
          <input type="hidden" name="ID_Tipo_Documento_Identidad" class="form-control">
          <input type="hidden" name="No_Entidad" class="form-control" placeholder="nombre">
          <input type="hidden" name="Nu_Documento_Identidad" class="form-control" placeholder="nro. doc. iden">
          <input type="hidden" name="Nu_Celular_Entidad_Order_Address_Entry" class="form-control" placeholder="celular">
          <input type="hidden" name="Txt_Direccion_Entidad_Order_Address_Entry" class="form-control" placeholder="direccion">
          <input type="hidden" name="Txt_Direccion_Referencia_Entidad_Order_Address_Entry" class="form-control" placeholder="direccion">
          <input type="hidden" name="Nu_Tipo_Recepcion" class="form-control" placeholder="tipo recepcion">
          <input type="hidden" name="ID_Moneda" class="form-control" placeholder="moneda">
          <input type="hidden" name="ID_Medio_Pago" class="form-control" placeholder="medio pago">
          <input type="hidden" name="ID_Entidad" class="form-control" placeholder="id entidad">
          <input type="hidden" name="ID_Distrito_Delivery" class="form-control" placeholder="id distrito delivery">

          <div class="row">
            <div class="col-xs-12 div-tipo_documento">
              <label>Tipo Documento</label>
              <div class="form-group">
                <select id="cbo-tipo_documento" name="ID_Tipo_Documento" class="form-control" style="width: 100%;">
                  <?php if($this->empresa->Nu_Tipo_Proveedor_FE != 3) { ?>
                  <option value="4">B/Venta</option>
                  <option value="3">Factura</option>
                  <?php } ?>
                  <option value="2">Nota de Venta</option>
                </select>
              </div>
            </div>

            <div class="col-xs-12">
              <p id="info-generar_venta"></p>
              <div class="hidden">
              (*) Para ver las ventas la opción es <strong>Ventas > Factura de Venta</strong>
              <br>(*) Al procesar venta se descargará stock.
                  </div>
            </div>
          </div>
        </div><!-- div modal body -->

        <div class="modal-footer">
          <div class="col-xs-6">
            <button type="button" id="btn-modal-generar_venta-cancel" class="btn btn-danger btn-md btn-block pull-left" data-dismiss="modal">Cancelar</button>
          </div>
          <div class="col-xs-6">
            <button type="button" id="btn-modal-generar_venta-send" class="btn btn-success btn-md btn-block">Generar Venta</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /.modal -->
  <?php echo form_close(); ?>

</div>
<!-- /.content-wrapper -->

<!-- Importar Pedidos Excel -->
<div class="modal fade modal_importar_excel_pedidos" id="modal-default">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
        <form name="importa" method="post" action="<?php echo base_url(); ?>TiendaVirtual/PedidosTiendaVirtualController/importarExcelPedidos" enctype="multipart/form-data">
          <div class="row">
    				<div class="col-sm-12 text-center">
              <h3>Importación de Pedidos por Excel</h3>
            </div>
            
            <div class="col-md-12"><br>
              <div class="well well-sm">
                <i class="fa fa-warning"></i> Indicaciones:
                <br>&nbsp;
                <br>- El formato requerido es <b>.xlsx</b>
                <br>- El archivo <b>.xlsx</b> no debe contener estilos, gráficos o fórmulas
                <br>- No guardar la plantilla porque el formato puede ser actualizado sin previo aviso
                <br>&nbsp;
                <a id="a-download-pedidos" href="<?php echo base_url(); ?>DownloadController/download/Ecxpresslae_pedidos_manuales.xlsx" class="btn btn-success btn-md btn-block"><span class="fa fa-cloud-download"></span> Descargar plantilla</a>
              </div>
            </div>
              
    				<div class="col-sm-12">
              <label>Archivo</label>
    				  <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-cloud-upload" aria-hidden="true"></i></span>
                  <label class="btn btn-default" for="my-file-selector-pedidos" style="width: 100%;">
                    <input type="file" id="my-file-selector-pedidos" name="excel-archivo-pedidos" multiple=false accept=".xlsx" required style="display:none" onchange="$('#upload-file-info').html(this.files[0].name)">Buscar archivo ...
                  </label>
                  <span class='label label-info' id="upload-file-info"></span>
                </div>
                <span class="help-block" id="error"></span>
              </div>
            </div>
            
            <div class="col-xs-6 col-md-6"><br>
              <div class="form-group">
                <button type="button" id="btn-cancel-pedidos" class="btn btn-danger btn-md btn-block" data-dismiss="modal">Salir</button>
              </div>
            </div>
            
            <div class="col-xs-6 col-md-6"><br>
              <div class="form-group">
                <button type="submit" id="btn-excel-importar_excel_pedidos" class="btn btn-success btn-md btn-block" onclick="submit();"><span class="fa fa-cloud-upload"></span> Procesar Excel</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php
  $i=0;
  if ( !empty($sStatusExcel) ){
    $i=1;
    $sClassModal = 'success';
    $sMessage = 'Datos cargados satisfactoriamente';
    if ( $sStatusExcel == 'error-archivo_no_existe' ) {
      $sMessage = 'El archivo no existe';
      $sClassModal = 'danger';  
    } else if ( $sStatusExcel == 'error-copiar_archivo' ) {
      $sMessage = 'Error al copiar archivo al servidor';
      $sClassModal = 'danger';  
    }
  ?>
  <div class="modal fade in modal-<?php echo $sClassModal; ?>" id="modal-message_excel" role="dialog" style="display: block;">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"><?php echo $sMessage; ?></h4>
        </div>
        <div class="modal-footer">
          <button type="button" id="btn-cerrar_modal_excel" class="btn btn-outline pull-right" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>
<?php } ?>

<!-- Modal nota -->
<div class="modal fade modal-nota_pedido" id="modal-default">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="text-center">Novedades Pedido</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" id="hidden-modal-id_pedido_cabecera_nota" class="form-control" autocomplete="off">
        <div class="row">
          <div class="col-xs-12">
            <div class="form-group">
              <div id="textarea-ver_novedades"></div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <div class="col-xs-12 col-md-12">
          <div class="form-group">
            <button type="button" id="btn-modal-salir" class="btn btn-danger btn-lg btn-block pull-center" data-dismiss="modal">Salir</button>
          </div>
        </div>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /. Modal nota -->

<!-- Modal cuentas_bancarias_dropshipping -->
<div class="modal fade modal-cuentas_bancarias_dropshipping" id="modal-default">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
        <div class="col-xs-12 col-md-12">
          <img class="img-responsive col-xs-12" src="<?php echo base_url() . 'assets/img/cuentas_bancarias_dropshipping.jpeg?ver=1.0.0'; ?>" alt="Cuentas Bancarias Dropshipping" title="Cuentas Bancarias Dropshipping">
        </div>
      </div>
      <div class="modal-footer">
        <div class="col-xs-12 col-md-12">
          <div class="form-group"><br>
            <button type="button" id="btn-modal-salir" class="btn btn-danger btn-lg btn-block pull-center" data-dismiss="modal">Salir</button>
          </div>
        </div>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /. Modal delivery_importe -->