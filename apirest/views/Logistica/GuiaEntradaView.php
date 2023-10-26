<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header"></section>
  
  <!-- Main content -->
  <section class="content">
    <!-- New box-header -->
    <div class="row">
      <div class="col-xs-12">
        <div class="div-content-header">
          <h3>
            <i class="<?php echo $this->MenuModel->verificarAccesoMenuCRUD()->Txt_Css_Icons; ?>" aria-hidden="true"></i> <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?>
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
              <div class="col-xs-6 col-md-2">
                <div class="form-group">
                  <label>F. Inicio</label>
                  <div class="input-group date">
                    <input type="text" id="txt-Filtro_Fe_Inicio" class="form-control date-picker-report" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                  </div>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-md-2">
                <div class="form-group">
                  <label>F. Fin</label>
                  <div class="input-group date">
                    <input type="text" id="txt-Filtro_Fe_Fin" class="form-control date-picker-invoice txt-Filtro_Fe_Fin" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                  </div>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
  
              <div class="col-xs-5 col-md-2">
                <div class="form-group">
                  <label>Serie</label>
                  <input type="tel" id="txt-Filtro_SerieDocumento" class="form-control input-number" maxlength="4" placeholder="Buscar" value="" autocomplete="off">
                </div>
              </div>
              
              <div class="col-xs-7 col-md-2">
                <div class="form-group">
                  <label>Número</label>
                  <input type="tel" id="txt-Filtro_NumeroDocumento" class="form-control input-number" maxlength="8" placeholder="Buscar" value="" autocomplete="off">
                </div>
              </div>
              
              <div class="col-md-2">
                <div class="form-group">
                  <label>Estado</label>
    		  				<select id="cbo-Filtro_Estado" class="form-control">
    		  				  <option value="" selected>Todos</option>
        				    <option value="6">Completado</option>
        				    <option value="7">Anulado</option>
        				  </select>
                </div>
              </div>
            </div>

            <div class="row div-Filtros">
              <div class="col-md-8">
                <div class="form-group">
                  <label>Nombre Proveedor</label>
                  <input type="text" id="txt-Filtro_Entidad" class="form-control autocompletar" data-global-class_method="AutocompleteController/getAllProvider" data-global-table="entidad" placeholder="Ingresar nombre" value="" autocomplete="off" maxlength="100">
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-6 col-md-2">
                <div class="form-group">
                  <label>&nbsp;</label>
                  <button type="button" id="btn-filter" class="btn btn-primary btn-block"><i class="fa fa-search"></i> Buscar</button>
                </div>
              </div>
              
              <div class="col-xs-6 col-md-2">
                <div class="form-group">
                  <label>&nbsp;</label>
                  <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Agregar == 1) : ?>
                    <button type="button" class="btn btn-success btn-block" onclick="agregarGuiaEntrada()"><i class="fa fa-plus-circle"></i> Agregar</button>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="table-responsive div-Listar">
            <table id="table-GuiaEntrada" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th>F. Emisión</th>
                  <th>Tipo</th>
                  <th>Serie</th>
                  <th>Número</th>
                  <th>Tipo Doc.</th>
                  <th>Proveedor</th>
                  <th class="sort_center">M</th>
                  <th class="no-sort_right">Total</th>
                  <th class="no-sort">Estado</th>
                  <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Editar == 1) : ?>
                    <th class="no-sort"></th>
                  <?php endif; ?>
                  <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Eliminar == 1) : ?>
                    <th class="no-sort"></th>
                  <?php endif; ?>
                </tr>
              </thead>
            </table>
          </div>
          <!-- /.box-body -->
          
          <div class="box-body div-AgregarEditar">
            <?php
            $attributes = array('id' => 'form-GuiaEntrada');
            echo form_open('', $attributes);
            ?>
          	  <input type="hidden" id="txt-EID_Empresa" name="EID_Empresa" class="form-control">
          	  <input type="hidden" id="txt-EID_Guia_Cabecera" name="EID_Guia_Cabecera" class="form-control">
          	  <input type="hidden" id="txt-EID_Tipo_Documento_Guia" name="EID_Tipo_Documento_Guia" class="form-control">
          	  <input type="hidden" id="txt-EID_Serie_Documento_Guia" name="EID_Serie_Documento_Guia" class="form-control">
          	  <input type="hidden" id="txt-EID_Numero_Documento_Guia" name="EID_Numero_Documento_Guia" class="form-control">
          	  <input type="hidden" id="txt-EID_Documento_Cabecera" name="EID_Documento_Cabecera" class="form-control">
          	  <input type="hidden" id="txt-EID_Tipo_Documento_Factura" name="EID_Tipo_Documento_Factura" class="form-control">
          	  <input type="hidden" id="txt-EID_Serie_Documento_Factura" name="EID_Serie_Documento_Factura" class="form-control">
          	  <input type="hidden" id="txt-EID_Numero_Documento_Factura" name="EID_Numero_Documento_Factura" class="form-control">
              
          	  <input type="hidden" id="txt-ENu_Descargar_Inventario" name="ENu_Descargar_Inventario" class="form-control">
          	  <input type="hidden" id="txt-ENu_Descargar_Inventario_Guia" name="ENu_Descargar_Inventario_Guia" class="form-control">
              
              <div class="row">
              	<div class="col-sm-12 col-md-12">
              		<div class="page-header">
              			<div class="pull-right">
              				<div class="btn-group" title="Proveedores">
              				  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
              				    <span class="fa fa-user"></span> Proveedores <span class="caret"></span>
              				  </button>
              				  <ul class="dropdown-menu" role="menu">
              				    <li><a href="<?php echo base_url('Logistica/ReglasLogistica/ProveedorController/listarProveedores'); ?>" target="_blank">Mis Proveedores</a></li>
              				  </ul>
              				</div>
                			<div class="btn-group" title="Productos">
                			  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                			    <span class="fa fa-shopping-cart"></span> Productos <span class="caret"></span>
                			  </button>
                			  <ul class="dropdown-menu" role="menu">
                			    <li><a href="<?php echo base_url('Logistica/ReglasLogistica/ProductoController/listarProductos'); ?>" target="_blank">Mis Productos</a></li>
                			  </ul>
                			</div>
              			</div>
              		</div>
              		<br/>
              	</div>
              </div>
			  
      			  <div class="row">
                <div class="col-sm-12 col-md-6">
        			    <div class="panel panel-default">
                    <div class="panel-heading"><i class="fa fa-book"></i> <b>Datos Documento</b></div>
                    <div class="panel-body">
                      <div class="col-sm-12 col-md-5 div-TipoDocumento">
                          <input type="radio" id="radio-guia" name="radioTipoDocumento" value="7" checked onclick="verDocumentoEntrada(this.value);"/> <b onclick="verDocumentoEntrada(7);">GuÍa de Remisión</b>
                          <span class="help-block" id="error"></span>
                      </div>
                      
                      <div class="col-sm-12 col-md-1 div-TipoDocumento"><label> ó </label></div>
                        
                      <div class="col-sm-12 col-md-6 div-TipoDocumento">
                          <input type="radio" id="radio-ambos" name="radioTipoDocumento" value="0" onclick="verDocumentoEntrada(this.value);"/> <b onclick="verDocumentoEntrada(0);">Guía de Remisión y Factura</b>
                          <span class="help-block" id="error"></span>
                      </div>
                      
                      <div class="col-sm-12 text-center"><span class="label-advertencia" id="error-msgTipoDocumento"></span></div>
                      
                      <div class="col-sm-12 col-md-4 div-Factura">
                        <div class="form-group">
                          <label>Documento <span class="label-advertencia">*</span></label>
                          <input type="hidden" id="txt-ID_Tipo_Asiento_Factura" name="ID_Tipo_Asiento_Factura" class="form-control" value="2">
                          <input type="hidden" id="txt-ID_Tipo_Documento_Factura" name="ID_Tipo_Documento_Factura" class="form-control" value="3">
                          <input type="text" value="Factura" class="form-control" disabled>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-sm-12 col-md-4 div-Factura">
                        <div class="form-group">
                          <label>Series <span class="label-advertencia">*</span></label>
            		  				<input type="text" id="txt-ID_Serie_Documento_Factura" name="ID_Serie_Documento_Factura" class="form-control input-Mayuscula input-codigo_barra" maxlength="4" autocomplete="off">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-sm-12 col-md-4 div-Factura">
                        <div class="form-group">
                          <label>Número <span class="label-advertencia">*</span></label>
                          <input type="tel" id="txt-ID_Numero_Documento_Factura" name="ID_Numero_Documento_Factura" class="form-control input-number" maxlength="8" autocomplete="off">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-sm-12 col-md-4 div-Guia">
                        <div class="form-group">
                          <label>Documento <span class="label-advertencia">*</span></label>
                          <input type="hidden" id="txt-ID_Tipo_Asiento_Guia" name="ID_Tipo_Asiento_Guia" class="form-control" value="3">
                          <input type="hidden" id="txt-ID_Tipo_Documento_Guia" name="ID_Tipo_Documento_Guia" class="form-control" value="7">
                          <input type="text" value="Guia Remisión" class="form-control" disabled>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-sm-12 col-md-4 div-Guia">
                        <div class="form-group">
                          <label>Series <span class="label-advertencia">*</span></label>
            		  				<input type="tel" id="txt-ID_Serie_Documento_Guia" name="ID_Serie_Documento_Guia" class="form-control input-number" maxlength="4" autocomplete="off">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-sm-12 col-md-4 div-Guia">
                        <div class="form-group">
                          <label>Número <span class="label-advertencia">*</span></label>
                          <input type="tel" id="txt-ID_Numero_Documento_Guia" name="ID_Numero_Documento_Guia" class="form-control input-number" maxlength="8" autocomplete="off">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-sm-12 col-md-8">
                        <div class="form-group">
                          <label>Movimiento <span class="label-advertencia">*</span></label>
                          <select id="cbo-TiposMovimientoEntrada" class="form-control required" style="width: 100%;"></select>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-sm-12 col-md-4">
                        <div class="form-group">
                          <label>F. Emisión <span class="label-advertencia">*</span></label>
                          <div class="input-group date">
                            <input type="text" id="txt-Fe_Emision" name="Fe_Emision" class="form-control date-picker-invoice required" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                          </div>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                
                      <div class="col-sm-12 col-md-5">
                        <div class="form-group">
                          <label>Moneda <span class="label-advertencia">*</span></label>
                          <select id="cbo-Monedas" class="form-control required" style="width: 100%;"></select>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-sm-12 col-md-4 div-DescargarInventario">
                        <div class="form-group">
                          <label>Descargar Stock</label>
            		  				<select id="cbo-DescargarInventario" name="Nu_Descargar_Inventario" class="form-control required"></select>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-sm-12 div-Almacen">
                        <div class="form-group">
                          <label>Almacen <span class="label-advertencia">*</span></label>
            		  				<select id="cbo-Almacenes" class="form-control required" style="width: 100%;"></select>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="col-sm-12 col-md-6">
        			    <div class="panel panel-default">
                    <div class="panel-heading"><i class="fa fa-user"></i> <b>Datos Proveedor</b></div>
                    <div class="panel-body">
                      <div class="col-md-12">
                        <div class="form-group">
                          <label>Nombre Proveedor <span class="label-advertencia">*</span></label>
                          <input type="hidden" id="txt-AID" name="AID" class="form-control required">
                          <input type="text" id="txt-ANombre" name="ANombre" class="form-control autocompletar" data-global-class_method="AutocompleteController/getAllProvider" data-global-table="entidad" placeholder="Ingresar nombre" value="" autocomplete="off">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>

                      <div class="col-md-7">
                        <div class="form-group">
                          <label>Número Documento Identidad</label>
                          <input type="text" id="txt-ACodigo" name="ACodigo" class="form-control required" disabled>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                            
                      <div class="col-md-12">
                        <div class="form-group">
                          <label>Dirección</label>
                          <input type="text" id="txt-Txt_Direccion_Entidad" name="Txt_Direccion_Entidad" class="form-control" disabled>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div><!-- ./Cabecera -->

      			  <div class="row">
                <div class="col-md-12">
        			    <div id="panel-DetalleProductos" class="panel panel-default">
                    <div class="panel-heading"><i class="fa fa-shopping-cart"></i> <b>Detalle</b></div>
                    <div class="panel-body">
      			          <div class="row">
          	            <input type="hidden" name="Nu_Tipo_Lista_Precio" value="2" class="form-control"><!-- 2 = Compra -->
                        <div class="col-xs-4">
                          <label>Lista de Precio <span class="label-advertencia">*</span></label>
                          <div class="form-group">
                            <select id="cbo-lista_precios" class="form-control required" style="width: 100%;"></select>
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>
                        
                        <div class="col-md-9">
                          <div class="form-group">
                            <label>Producto <span class="label-advertencia">*</span></label>
                            <input type="hidden" id="txt-Nu_Tipo_Producto" class="form-control" value="2">
                            <input type="hidden" id="txt-ID_Producto" class="form-control">
                            <input type="hidden" id="txt-Nu_Codigo_Barra" class="form-control">
                            <input type="hidden" id="txt-Ss_Precio" class="form-control">
                            <input type="hidden" id="txt-Nu_Compuesto" class="form-control" value="0">
                            <input type="hidden" id="txt-ID_Impuesto_Cruce_Documento" class="form-control">
                            <input type="hidden" id="txt-Nu_Tipo_Impuesto" class="form-control">
                            <input type="hidden" id="txt-Ss_Impuesto" class="form-control">
                            <input type="text" id="txt-No_Producto" class="form-control autocompletar_detalle" data-global-class_method="AutocompleteController/getAllProduct" data-global-table="producto" placeholder="Ingresar nombre o código barra" value="" autocomplete="off">
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>
                        
                        <div class="col-md-3">
                          <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="button" id="btn-addProductoGuiaEntrada" class="btn btn-success btn-md btn-block"><i class="fa fa-plus-circle"></i> Agregar Item</button>
                          </div>
                        </div>
                      </div>
                      
      			          <div class="row">
                      <div class="col-md-12">
                      <div class="table-responsive">
                        <table id="table-DetalleGuiasEntradaProductos" class="table table-striped table-bordered">
                          <thead>
                            <tr>
                              <th style="display:none;" class="text-left"></th>
                              <th class="text-center" style="width: 10%;">Cantidad</th>
                              <th class="text-center" style="width: 25%;">UPC y Descripción</th>
                              <th class="text-center" style="width: 10%;">Costo Unitario</th>
                              <th class="text-center" style="width: 15%;">Impuesto Tributario</th>
                              <th class="text-center">Sub Total</th>
                              <th class="text-center" style="width: 10%;">% DSCTO</th>
                              <th class="text-center">Valor Total</th>
                              <th class="text-center"></th>
                            </tr>
                          </thead>
                          <tbody>
                          </tbody>
                        </table>
                      </div>
                      </div>
                      </div>
                    </div>
                  </div>
                </div><!-- ./Detalle -->
              </div>
              
      			  <div class="row">
      			    <div class="col-md-12">
      			      <div class="panel panel-default">
                    <div class="panel-heading"><i class="fa fa-comment-o"></i> <b>Observaciones</b></div>
                    <div class="panel-body">
                      <div class="col-md-12">
                        <textarea name="Txt_Glosa" class="form-control"></textarea>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
                
      			  <div class="row"><!-- Totales -->
      			    <div class="col-md-8"></div>
                <div class="col-md-4">
        			    <div class="panel panel-default">
                    <div class="panel-heading"><i class="fa fa-money"></i> <b>Totales</b></div>
                    <div class="panel-body">
                      <table class="table" id="table-GuiaEntradaTotal">
                        <tr>
                          <td><label>% Descuento</label></td>
                          <td class="text-right">
      	  					        <input type="tel" class="form-control input-decimal" id="txt-Ss_Descuento" name="Ss_Descuento" size="3" value="" autocomplete="off" />
                          </td>
                        </tr>
                        
                        <tr>
                          <td><label>OP. Gravadas</label></td>
                          <td class="text-right">
      	  					        <input type="hidden" class="form-control" id="txt-subTotal" value="0.00"/>
                            <span class="span-signo"></span> <span id="span-subTotal">0.00</span>
                          </td>
                        </tr>
                        
                        <tr>
                          <td><label>OP. Inafectas</label></td>
                          <td class="text-right">
                            <input type="hidden" class="form-control" id="txt-inafecto" value="0.00"/>
                            <span class="span-signo"></span> <span id="span-inafecto">0.00</span>
                          </td>
                        </tr>
                        
                        <tr>
                          <td><label>OP. Exoneradas</label></td>
                          <td class="text-right">
                            <input type="hidden" class="form-control" id="txt-exonerada" value="0.00"/>
                            <span class="span-signo"></span> <span id="span-exonerada">0.00</span>
                          </td>
                        </tr>
                        
                        <tr>
                          <td><label>Descuento Total (-)</label></td>
                          <td class="text-right">
                            <input type="hidden" class="form-control" id="txt-descuento" value="0.00"/>
                            <span class="span-signo"></span> <span id="span-descuento">0.00</span>
                          </td>
                        </tr>
                        
                        <tr>
                          <td><label>I.G.V. %</label></td>
                          <td class="text-right">
                              <input type="hidden" class="form-control" id="txt-impuesto" value="0.00"/>
                              <span class="span-signo"></span> <span id="span-impuesto">0.00</span>
                          </td>
                        </tr>
                        
                        <tr>
                          <td><label>Total</label></td>
                          <td class="text-right">
                            <input type="hidden" class="form-control" id="txt-total" value="0.00"/>
                            <span class="span-signo"></span> <span id="span-total">0.00</span>
                          </td>
                        </tr>
                      </table><!-- ./Totales -->
                    </div>
                  </div>
                </div>
              </div>
              
      			  <div class="row">
                <div class="col-xs-6 col-md-6">
                  <div class="form-group">
                    <button type="button" id="btn-cancelar" class="btn btn-danger btn-md btn-block"><span class="fa fa-close"></span> Cancelar (ESC)</button>
                  </div>
                </div>
                <div class="col-xs-6 col-md-6">
                  <div class="form-group">
                    <button type="submit" id="btn-save" class="btn btn-success btn-md btn-block btn-verificar"><i class="fa fa-save"></i> Guardar (ENTER)</button>
                  </div>
                </div>
              </div>
            <?php echo form_close(); ?>
          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->