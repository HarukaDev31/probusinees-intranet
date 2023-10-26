<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">  
  <?php
  $sCssDisplayRoot='style="display:none"';
  if ( $this->user->No_Usuario == 'root' ){
    $sCssDisplayRoot='';
  }
  ?>

  <!-- Main content -->
  <section class="content">
    <!-- New box-header -->
    <div class="row">
      <div class="col-xs-12">
        <div class="div-content-header">
          <h3 class="title-opcion">
            <i class="<?php echo $this->MenuModel->verificarAccesoMenuCRUD()->Txt_Css_Icons; ?>" aria-hidden="true"></i> <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?>
            &nbsp;
            <a href="<?php echo $this->MenuModel->verificarAccesoMenuCRUD()->Txt_Url_Video; ?>" target="_blank" target="_blank" rel="noopener noreferrer" title="Ver video tutorial de <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?>" alt="Ver video tutorial de <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?>">
            <span class="icon-opcion" style="background-color: #FF0000 !important; padding: 10px; border-radius: 50px;">
                <i class="fa fa-youtube-play red hidden-xs" style="color:  #FFF !important" aria-hidden="true"></i>
                &nbsp;&nbsp;<span class="icon-opcion" style="color: #FFF;">Video Turorial<span>
              </span>
            </a>
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
              if ( $this->user->No_Usuario == 'root' ){ ?>
              <div class="col-xs-12 col-sm-12 col-md-12">
                <label>Empresa</label>
                <div class="form-group">
                  <select id="cbo-filtro_empresa" name="ID_Empresa" class="form-control select2 required" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-6 col-sm-6 col-md-6">
                <label>Tipo Sistema</label>
                <div class="form-group">
                  <select id="cbo-filtro-tipo_sistema" name="Nu_Tipo_Proveedor_FE" class="form-control required" style="width: 100%;">
    		  				  <option value="0" selected="selected">Todos</option>
    		  				  <option value="3">Control INTERNO</option>
    		  				  <option value="2">SUNAT</option>
                    <option value="1">PSE N</option>
                  </select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-6 col-md-6">
                <label>Estado Empresa</label>
                <div class="form-group">
                  <select id="cbo-filtro-estado" name="Nu_Estado" class="form-control required" style="width: 100%;">
                    <option value="">Todos</option>
                    <option value="1" selected="selected">Activo</option>
                    <option value="0">Inactivo</option>
                  </select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-6 col-md-6 hidden">
                <label>Organización</label>
                <div class="form-group">
                  <select id="cbo-filtro_organizacion" name="ID_Organizacion" class="form-control select2 required" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              <?php } else { ?>
                <input type="hidden" id="cbo-filtro_empresa" name="ID_Empresa" class="form-control" value="<?php echo $this->user->ID_Empresa; ?>">
                <input type="hidden" id="cbo-filtro_organizacion" name="ID_Organizacion" class="form-control" value="<?php echo $this->user->ID_Organizacion; ?>">
                
                <div class="col-xs-6 col-sm-6 col-md-6 hidden">
                  <label>Tipo Sistema</label>
                  <div class="form-group">
                    <select id="cbo-filtro-tipo_sistema" name="Nu_Tipo_Proveedor_FE" class="form-control required" style="width: 100%;">
                      <option value="0" selected="selected">Todos</option>
                      <option value="3">Control INTERNO</option>
                      <option value="2">SUNAT</option>
                      <option value="1">PSE N</option>
                    </select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>

                <div class="col-xs-6 col-sm-6 col-md-6 hidden">
                  <label>Estado Empresa</label>
                  <div class="form-group">
                    <select id="cbo-filtro-estado" name="Nu_Estado" class="form-control required" style="width: 100%;">
                      <option value="">Todos</option>
                      <option value="1" selected="selected">Activo</option>
                      <option value="0">Inactivo</option>
                    </select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
              <?php } ?>

              <div class="col-xs-12 col-md-3" <?php echo $sCssDisplayRoot; ?>>
                <div class="form-group">
    		  				<select id="cbo-Filtros_Sistemas" name="Filtros_Sistemas" class="form-control">
    		  				  <option value="Sistema">Nombre Dominio</option>
    		  				</select>
                </div>
              </div>
              
              <div class="col-xs-12 col-md-6" <?php echo $sCssDisplayRoot; ?>>
                <div class="form-group">
                  <input type="text" id="txt-Global_Filter" name="Global_Filter" class="form-control" placeholder="Buscar" value="" autocomplete="off">
                </div>
              </div>

              <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Agregar == 1) : ?>
              <div class="col-xs-12 col-md-3">
                <button type="button" class="btn btn-success btn-block" onclick="agregarSistema()"><i class="fa fa-plus-circle"></i> Agregar</button>
              </div>
              <?php endif; ?>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="table-responsive div-Listar">
            <table id="table-Sistema" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <?php if ( $this->user->No_Usuario == 'root' ){ ?>
                  <th>Empresa</th>
                  <th>Importe</th>
                  <th>F. Inicio</th>
                  <?php } ?>
                  <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Editar == 1) : ?>
                    <th class="no-sort">Editar</th>
                  <?php endif; ?>
                  <th class="no-sort">Logo</th>
                  <th>Web / Redes Sociales</th>
                  <th>Celular</th>
                  <th>Correo</th>
                  <th class="no-sort">Stock</th>
                  <th class="no-sort">Estado</th>
                  <?php //if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Eliminar == 1) : ?>
                    <!--<th class="no-sort">Eliminar</th>-->
                  <?php //endif; ?>
                </tr>
              </thead>
            </table>
          </div>
          <!-- /.box-body -->
          
          <div class="box-body div-AgregarEditar">
            <?php
            $attributes = array('id' => 'form-Sistema');
            echo form_open('', $attributes);
            ?>
          	  <input type="hidden" name="EID_Empresa" class="form-control">
          	  <input type="hidden" name="EID_Configuracion" class="form-control">
          	  <input type="hidden" name="ENo_Dominio_Empresa" class="form-control">
          	  <input type="hidden" name="ENo_Foto_Boleta" class="form-control">
          	  <input type="hidden" name="ENo_Foto_Factura" class="form-control">
          	  <input type="hidden" name="ENo_Foto_NCredito" class="form-control">
          	  <input type="hidden" name="ENo_Foto_Guia" class="form-control">
              <input type="hidden" name="hidden-nombre_logo" class="form-control" value="">
          	  <input type="hidden" id="hidden-nombre_imagen_logo_empresa" name="No_Imagen_Logo_Empresa" class="form-control" value="">
              
              <div class="row" <?php echo $sCssDisplayRoot; ?>>
                <div class="col-xs-12">
                  <div class="form-group">
                    <label>Empresa <span class="label-advertencia">*</span></label>
        	  				<select id="cbo-Empresas" name="ID_Empresa" class="form-control select2 required" style="width: 100%;"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
              </div>
              
              <div class="row">
                <div class="col-xs-6 col-sm-4 col-md-2 col-lg-2" <?php echo $sCssDisplayRoot; ?>>
                  <div class="form-group">
                    <label data-toggle="tooltip" data-placement="bottom" title="Fecha de inicio de sistema">F. Inicio <span class="label-advertencia">*</span></label>
                    <div class="input-group date">
                      <input type="text" name="Fe_Inicio_Sistema" class="form-control date-picker-invoice required" data-toggle="tooltip" data-placement="bottom" title="Fecha de inicio de sistema" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                    </div>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>

                <?php
                if ( $this->user->No_Usuario == 'root' ){ ?>
                <div class="col-xs-6 col-sm-2 col-md-3 col-lg-3">
                  <div class="form-group">
                    <label title="Rubro empresa">Rubro <span class="label-advertencia">*</span></label>
                    <select id="cbo-tipo_rubro_empresa" name="Nu_Tipo_Rubro_Empresa" title="Rubro Empresa" class="form-control select2 required" style="width: 100%;"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
                <?php } else { ?>
                  <input type="hidden" id="cbo-tipo_rubro_empresa" name="Nu_Tipo_Rubro_Empresa" class="form-control" value="<?php echo $this->empresa->Nu_Tipo_Rubro_Empresa; ?>">
                <?php } ?>

                <div class="col-xs-6 col-sm-3 col-md-3 col-lg-3" <?php echo $sCssDisplayRoot; ?>>
                  <div class="form-group">
                    <label>Estado <span class="label-advertencia">*</span></label>
                    <select id="cbo-Estado" name="Nu_Estado" class="form-control required"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>

                <?php
                if ( $this->user->No_Usuario == 'root' ){ ?>
                <div class="col-xs-6 col-sm-4 col-md-2 col-lg-2">
                  <div class="form-group">
                    <label data-toggle="tooltip" data-placement="bottom" title="Importe de pago de nuestro servicio para el cliente">Pago Cliente <span class="label-advertencia">*</span></label>
                    <input type="text" name="Ss_Total_Pago_Cliente_Servicio" data-toggle="tooltip" data-placement="bottom" title="Importe de pago de nuestro servicio para el cliente" class="form-control input-decimal required" maxlength="20" placeholder="" value="" autocomplete="off">
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
                <?php } else { ?>
                  <input type="hidden" name="Ss_Total_Pago_Cliente_Servicio" class="form-control" value="<?php echo $this->empresa->Ss_Total_Pago_Cliente_Servicio; ?>">
                <?php } ?>

                <div class="col-xs-6 col-sm-4 col-md-2 col-lg-2" <?php echo $sCssDisplayRoot; ?>>
                  <div class="form-group">
                    <label data-toggle="tooltip" data-placement="bottom" title="Verificar que la opción Punto de Venta se pueda vender desde puntos autorizados (Si / No)">Autorización Venta <span class="label-advertencia">*</span></label>
                    <select id="cbo-autorizacion_punto_venta" name="Nu_Verificar_Autorizacion_Venta" class="form-control required" data-toggle="tooltip" data-placement="bottom" title="Verificar que la opción Punto de Venta se pueda vender desde puntos autorizados (Si / No)"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
                
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" <?php echo $sCssDisplayRoot; ?>>
                  <div class="form-group">
                    <label>Lenguaje Impresión
                      <span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Para generar ticket en formato PDF / HTML">
                      <i class="fa fa-info-circle"></i>
                      </span>
                    </label>
                    <select id="cbo-tipo_lenguaje_impresion_pos" name="Nu_Tipo_Lenguaje_Impresion_Pos" class="form-control required"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
              </div><!-- fin row -->

      			  <div class="row" <?php echo $sCssDisplayRoot; ?>>
                <div class="col-md-12">
        			    <div id="panel-DetalleProductosOrdenVenta" class="panel panel-default">
                    <div class="panel-heading"><i class="fa fa-cloud-download"></i> <b>Token para acceder (RENIEC, RUC y Tasa de cambio SUNAT)</b></div>
                    <div class="panel-body">
                      <div class="row">
                				<div class="col-sm-12">
                          <label>Token</label>
                          <div class="form-group">
                            <input type="text" id="Txt_Token" name="Txt_Token" placeholder="Ingresar token" class="form-control required pwd_sistema" autocomplete="off" value="<?php echo $this->empresa->Txt_Token; ?>">
                            <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password-sistema"></span>
                            <span class="help-block" id="error"></span>
                          </div>
                				</div>
                			</div>
                		</div>
                	</div>
                </div>
              </div><!-- fin row -->

              <div class="row">
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                  <div class="form-group">
                    <label>Sunat</label>
                    <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Enviar registros de la opción Ventas y clientes > Vender automáticamente a Sunat (Si / No)">
                      <i class="fa fa-info-circle"></i>
                    </span>
        	  				<select id="cbo-enviar_sunat_automatic" name="Nu_Enviar_Sunat_Automatic" class="form-control required"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>

                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                  <div class="form-group">
                    <label>Validar Stock</label>
                    <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Si esta ACTIVADO, solo se podrá vender cuando el stock sea mayor a 0 y si esta DESACTIVADO podrá vender sin stock">
                      <i class="fa fa-info-circle"></i>
                    </span>
        	  				<select id="cbo-activar_stock" name="Nu_Validar_Stock" class="form-control required"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>

                <div class="col-xs-6 col-sm-2 col-md-2 col-lg-2 hidden">
                  <div class="form-group">
                    <label>Día(s)</label>
                    <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Se usuará cuando se necesite alertar al sistema de los productos con fecha de vencimiento actual o antes según el número de días configurados">
                      <i class="fa fa-info-circle"></i>
                    </span>
                    <input type="tel" name="Nu_Dia_Limite_Fecha_Vencimiento" class="form-control input-number" maxlength="1" placeholder="Lote vencimiento" value="0" autocomplete="off">
                  </div>
                </div>
              </div>
                
              <div class="row">
                <div class="col-md-12">
                  <div id="" class="panel panel-default">
                    <div class="panel-heading">
                      <b><i class="fa fa-fax"></i> Punto de Venta</b>
                    </div>
                    <div class="panel-body">
                      <div class="col-xs-6 col-sm-4 col-md-2 col-lg-2">
                        <div class="form-group">
                          <label>Redondeo</label>
                          <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Si esta ACTIVADO, en el POS si el monto es > 0.05 será a favor de la empresa y si es < 0.05 será a favor del cliente">
                            <i class="fa fa-info-circle"></i>
                          </span>
                          <select id="cbo-activar_redondeo" name="Nu_Activar_Redondeo" class="form-control required"></select>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>

                      <div class="col-xs-6 col-sm-4 col-md-2 col-lg-2">
                        <label>Arqueo</label>
                        <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Vista del formato del cierre de caja en el Punto de Venta por categoría / producto">
                          <i class="fa fa-info-circle"></i>
                        </span>
                        <div class="form-group">
                          <select id="cbo-arqueo_punto_venta" name="Nu_Imprimir_Liquidacion_Caja" class="form-control required"></select>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-6 col-sm-4 col-md-2 col-lg-2">
                        <div class="form-group">
                          <label>Precio</label>
                          <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Si ACTIVAMOS, en la opción Punto de Venta podremos modificar el precio de venta del producto">
                            <i class="fa fa-info-circle"></i>
                          </span>
                          <select id="cbo-precio_punto_venta" name="Nu_Precio_Punto_Venta" class="form-control required"></select>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>

                      <div class="col-xs-6 col-sm-4 col-md-2 col-lg-2">
                        <div class="form-group">
                          <label>Descuento</label>
                          <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Si ACTIVAMOS, en la opción Punto de Venta podremos brindar descuento por producto">
                            <i class="fa fa-info-circle"></i>
                          </span>
                          <select id="cbo-activar_descuento_punto_venta" name="Nu_Activar_Descuento_Punto_Venta" class="form-control required"></select>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>

                      <div class="col-xs-6 col-sm-4 col-md-6 col-lg-2 hidden">
                        <div class="form-group">
                          <label>Ticket Línea Detalle
                            <span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Si se activa, la información del detalle se mostrará en cada línea acortando el nombre de ítem para que la información se pueda mostrar en una fila de cada producto vendido en el ticket">
                            <i class="fa fa-info-circle"></i>
                            </span>
                          </label>
                          <select id="cbo-activar_ticket_linea_detalle" name="Nu_Activar_Detalle_Una_Linea_Ticket" class="form-control required"></select>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>

                      <div class="col-xs-12 col-sm-8 col-md-4 col-lg-4">
                        <div class="form-group">
                          <label>Pre-seleccion Tipo Documento
                            <span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Si seleccionamos un tipo de documento estará por defecto en primer lugar al momento de vender (Punto de Venta y Factura de Venta)">
                            <i class="fa fa-info-circle"></i>
                            </span>
                          </label>
                          <select id="cbo-predeterminar_tipo_documento_venta" name="Nu_ID_Tipo_Documento_Venta_Predeterminado" class="form-control required"></select>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>

                      <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4" id="div-predeterminar_cliente_varios_venta">
                        <div class="form-group">
                          <label>Pre-seleccion Cliente Varios
                            <span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Si activamos clientes varios, estará preseleccionado al momento de vender (Punto de Venta y Factura de Venta)">
                            <i class="fa fa-info-circle"></i>
                            </span>
                          </label>
                          <select id="cbo-predeterminar_cliente_varios_venta" name="Nu_Cliente_Varios_Venta_Predeterminado" class="form-control required"></select>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>

                      <div class="col-xs-12 col-sm-4 col-md-4">
                        <div class="form-group">
                          <label>Pre-seleccion Formato PDF
                            <span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Tipo de formato impresión para PDF de POS (TICKET / A4)">
                            <i class="fa fa-info-circle"></i>
                            </span>
                          </label>
                          <select id="cbo-formato_impresion_pdf" name="No_Predeterminado_Formato_PDF_POS" class="form-control required"></select>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>

                      <div class="col-xs-12 col-sm-4 col-md-4">
                        <div class="form-group">
                          <label>Ticket Detalle
                            <span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Puedes mostrar / ocultar columnas al ticket detalle de productos">
                            <i class="fa fa-info-circle"></i>
                            </span>
                          </label>
                          <select id="cbo-imprimir_columna_ticket_detalle" name="Nu_Imprimir_Columna_Ticket_Detalle" class="form-control required"></select>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>

                      <div class="col-xs-12 col-sm-4 col-md-4">
                        <div class="form-group">
                          <label>Vender Con
                            <span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Puedes vender solo con Cajero o puedes tener ventas por cajero + vendedores">
                            <i class="fa fa-info-circle"></i>
                            </span>
                          </label>
                          <select id="cbo-Nu_Tipo_Vender_Usuario_POS" name="Nu_Tipo_Vender_Usuario_POS" class="form-control required"></select>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div><!-- fin row -->
                				  
      			  <div class="row" style="display:none">
                <div class="col-md-12">
        			    <div id="panel-DetalleProductosOrdenVenta" class="panel panel-default">
                    <div class="panel-heading"><i class="fa fa-book"></i> <b>Formatos de impresión</b></div>
                    <div class="panel-body">
                      <div class="row">
                				<div class="col-xs-3 col-sm-6 col-md-3">
                          <label>Boleta</label>
                				  <div class="form-group">
                            <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-cloud-upload" aria-hidden="true"></i></span>
                              <label class="btn btn-default" for="my-file-selector_boleta">
                                <input type="file" id="my-file-selector_boleta" name="No_Foto_Boleta" multiple=false accept=".png,.jpeg,.jpg" required style="display:none" onchange="$('#upload-file-info_boleta').html(this.files[0].name)">Subir archivo
                              </label>
                              <span class='label label-info' id="upload-file-info_boleta"></span>
                            </div>
                          </div>
                        </div>
                        
                				<div class="col-xs-3 col-sm-6 col-md-3">
                          <label>Factura</label>
                				  <div class="form-group">
                            <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-cloud-upload" aria-hidden="true"></i></span>
                              <label class="btn btn-default" for="my-file-selector_factura">
                                <input type="file" id="my-file-selector_factura" name="No_Foto_Factura" multiple=false accept=".png,.jpeg,.jpg" required style="display:none" onchange="$('#upload-file-info_factura').html(this.files[0].name)">Subir archivo
                              </label>
                              <span class='label label-info' id="upload-file-info_factura"></span>
                            </div>
                          </div>
                        </div>
                        
                				<div class="col-xs-3 col-sm-6 col-md-3">
                          <label>Nota de Crédito</label>
                				  <div class="form-group">
                            <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-cloud-upload" aria-hidden="true"></i></span>
                              <label class="btn btn-default" for="my-file-selector_ncredito">
                                <input type="file" id="my-file-selector_ncredito" name="No_Foto_NCredito" multiple=false accept=".png,.jpeg,.jpg" required style="display:none" onchange="$('#upload-file-info_ncredito').html(this.files[0].name)">Subir archivo
                              </label>
                              <span class='label label-info' id="upload-file-info_ncredito"></span>
                            </div>
                          </div>
                        </div>
                        
                				<div class="col-xs-3 col-sm-6 col-md-3">
                          <label>Guía de Remisión</label>
                				  <div class="form-group">
                            <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-cloud-upload" aria-hidden="true"></i></span>
                              <label class="btn btn-default" for="my-file-selector_guia">
                                <input type="file" id="my-file-selector_guia" name="No_Foto_Guia" multiple=false accept=".png,.jpeg,.jpg" required style="display:none" onchange="$('#upload-file-info_guia').html(this.files[0].name)">Subir archivo
                              </label>
                              <span class='label label-info' id="upload-file-info_guia"></span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
                        
              <!-- Orden de Venta -->
      			  <div class="row">
                <div class="col-md-12">
        			    <div id="panel-DetalleProductosOrdenVenta" class="panel panel-default">
                    <div class="panel-heading"><i class="fa fa-book"></i> <b>Formato de TICKET y A4</b> (Opcional)
                      <span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="(Nota de Venta - Facturación Electrónica - Representación Interna - Cotización - Orden de Compra)">
                      <i class="fa fa-info-circle"></i>
                      </span>
                    </div>
                    <div class="panel-body">
                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group">
                            <label>Web / Facebook / Instagram y más</label> (100 carateres)
                            <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-building" aria-hidden="true"></i></span>
                              <input type="text" id="txt-No_Dominio_Empresa" name="No_Dominio_Empresa" class="form-control" placeholder="Opcional" maxlength="100" autocomplete="off">
                            </div>
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>
              
                        <div class="col-md-4">
                          <div class="form-group">
                            <label>Correo</label>
                            <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-envelope" aria-hidden="true"></i></span>
                              <input type="text" name="Txt_Email_Empresa" placeholder="Opcional" class="form-control" maxlength="50" autocomplete="off">
                            </div>
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>
                        
                        <div class="col-sm-6 col-md-4">
                          <label>Celular / WhatsApp</label>
                          <div class="form-group">
                            <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-phone" aria-hidden="true"></i></span>
                              <input type="text" name="Nu_Celular_Empresa" class="form-control" placeholder="Opcional" maxlength="30" autocomplete="off">
                            </div>
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>
                        
                        <div class="col-sm-6 col-md-4">
                          <div class="form-group">
                            <label>Teléfono</label>
                            <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-phone" aria-hidden="true"></i></span>
                              <input type="text" name="Nu_Telefono_Empresa" class="form-control" placeholder="Opcional" maxlength="30" autocomplete="off">
                            </div>
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>
                      </div>
                        
                      <div class="row">
                        <div class="col-md-12">
                          <label>Slogan</label>
                          <div class="form-group">
                            <input type="text" name="Txt_Slogan_Empresa" class="form-control" placeholder="Opcional" autocomplete="off">
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>
                      </div>

            	        <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                          <div class="form-group">
                            <div class="well well-sm">
                              <i class="fa fa-warning"></i> Logo <b>(PNG | JPG)</b>
                              <span class="hidden-sm"><br></span>- Tamaño: <span class="hidden-sm"><br></span><b>Alto: 320px y Ancho: 150px</b>
                              <span class="hidden-sm"><br></span>- Peso: <b>1 MB</b>
                            </div>
                          </div>
                        </div>

                        <div class="col-xs-12 col-sm-12 col-md-4">
                          <div class="col-xs-12 col-sm-4 col-md-12">
                            <div class="form-group">
                              <label>Mostrar Logo</label>
                              <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Mostrar logo impreso (si / no)">
                                <i class="fa fa-info-circle"></i>
                              </span>
                              <select id="cbo-logo_ticket" name="Nu_Logo_Empresa_Ticket" class="form-control required"></select>
                              <span class="help-block" id="error"></span>
                            </div>
                          </div>
                          
                          <div class="col-xs-6 col-sm-4 col-md-6">
                            <div class="form-group">
                              <label>Ancho</label>
                              <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="El ancho se va a considerar para los todos los formatos (PDF y Ticket)">
                                <i class="fa fa-info-circle"></i>
                              </span>
                              <input type="tel" name="Nu_Width_Logo_Ticket" class="form-control input-number required" maxlength="3" placeholder="" value="" autocomplete="off">
                              <span class="help-block" id="error"></span>
                            </div>
                          </div>
                            
                          <div class="col-xs-6 col-sm-4 col-md-6">
                            <div class="form-group">
                              <label>Alto</label>
                              <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="El alto se va a considerar para los todos los formatos (PDF y Ticket)">
                                <i class="fa fa-info-circle"></i>
                              </span>
                              <div class="form-group">
                                <input type="tel" name="Nu_Height_Logo_Ticket" class="form-control input-number required" maxlength="3" placeholder="" value="" autocomplete="off">
                                <span class="help-block" id="error"></span>
                              </div>
                            </div>
                          </div>
                        </div>

            	          <div class="col-xs-12 col-sm-12 col-md-5 text-center divDropzone"></div>
                        <br>
                      </div>
                    
            	        <div class="row">                        
                        <div class="col-md-6">
                          <label>Términos y condiciones (Ticket)</label>
                          <div class="form-group">
                            <textarea name="Txt_Terminos_Condiciones_Ticket" class="form-control textarea-caracter_especial" placeholder="Opcional" value="" autocomplete="off"></textarea>
                          </div>
                        </div>

                        <div class="col-md-6">
                          <label>Términos y condiciones (Cotización)</label>
                          <div class="form-group">
                            <textarea name="Txt_Terminos_Condiciones" class="form-control" placeholder="Opcional" value="" autocomplete="off"></textarea>
                          </div>
                        </div>
                      </div>
                      
            	        <div class="row">
                        <div class="col-md-6">
                          <label>Cuentas Bancarias (Todo)</label>
                          <div class="form-group">
                            <textarea name="Txt_Cuentas_Bancarias" class="form-control" placeholder="Opcional" value="" autocomplete="off"></textarea>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <label>Nota (Cotización / Orden Compra)</label>
                          <div class="form-group">
                            <textarea name="Txt_Nota" class="form-control" placeholder="Opcional" value="" autocomplete="off"></textarea>
                          </div>
                        </div>
                      </div>
                      
            	        <div class="row">
                        <div class="col-md-12">
                          <label>Cuenta Bancaria Detracción</label>
                          <div class="form-group">
                            <textarea name="Txt_Cuenta_Banco_Detraccion" class="form-control" placeholder="Opcional" value="" autocomplete="off"></textarea>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- ./ Orden Compra -->
            
      			  <div class="row">
                <div class="col-xs-6 col-md-6">
                  <div class="form-group">
                    <button type="button" id="btn-cancelar" class="btn btn-danger btn-lg btn-block"><span class="fa fa-close"></span> Cancelar</button>
                  </div>
                </div>
                <div class="col-xs-6 col-md-6">
                  <div class="form-group">
                    <button type="submit" id="btn-save" class="btn btn-success btn-lg btn-block btn-verificar"><i class="fa fa-save"></i> Guardar</button>
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