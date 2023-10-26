<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">  
<?php
$sCssDisplayRoot='style="display:none"';
if ( $this->user->No_Usuario == 'root' ){
  $sCssDisplayRoot='';
}

//array_debug($this->empresa);

$sCssDisplayProveedorDropshipping='';
$sCssDisplayProveedorDropshipping_propertie='';
if ( $this->empresa->Nu_Proveedor_Dropshipping == '1' && $this->empresa->Nu_Vendedor_Dropshipping == 0 && $this->empresa->Nu_Tienda_Virtual_Propia == 0 ){//
  $sCssDisplayProveedorDropshipping='style="display:none"';
  $sCssDisplayProveedorDropshipping_propertie='display:none;';
}

?>
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
          <div class="box-header box-header-new hidden"><!-- div-Listar -->
            <div class="row div-Filtros">
              <br>
              <?php
              if ( $this->user->No_Usuario == 'root' ){ ?>
              <div class="col-md-6">
                <label>Empresa</label>
                <div class="form-group">
                  <select id="cbo-filtro_empresa" name="Filtro_ID_Empresa" class="form-control select2 required" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              <div class="col-md-6">
                <label>Organización</label>
                <div class="form-group">
                  <select id="cbo-filtro_organizacion" name="ID_Organizacion" class="form-control select2 required" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              <?php } else { ?>
                <input type="hidden" id="cbo-filtro_empresa" name="Filtro_ID_Empresa" class="form-control" value="<?php echo $this->user->ID_Empresa; ?>">
                <input type="hidden" id="cbo-filtro_organizacion" name="ID_Organizacion" class="form-control" value="<?php echo $this->user->ID_Organizacion; ?>">
              <?php } ?>

              <div class="col-md-3">
                <div class="form-group">
    		  				<select id="cbo-Filtros_Sistemas" name="Filtros_Sistemas" class="form-control">
    		  				  <option value="Sistema">Nombre Dominio</option>
    		  				</select>
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="form-group">
                  <input type="text" id="txt-Global_Filter" name="Global_Filter" class="form-control" placeholder="Buscar" value="" autocomplete="off">
                </div>
              </div>
              
              <div class="col-md-3">
                <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Agregar == 1) : ?>
                <button type="button" class="btn btn-success btn-block" onclick="agregarSistema()">Agregar</button>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="table-responsive div-Listar hidden">
            <table id="table-Sistema" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <?php if ( $this->user->No_Usuario == 'root' ){ ?>
                  <th>Empresa</th>
                  <?php } ?>
                  <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Editar == 1) : ?>
                    <th class="no-sort">Editar</th>
                  <?php endif; ?>
                  <th>Logo</th>
                  <th>Nombre</th>
                  <th>Celular</th>
                  <th>WhatsApp</th>
                  <th>Email</th>
                  <th>Descripción</th>
                </tr>
              </thead>
            </table>
          </div>
          <!-- /.box-body -->
          
          <div class="box-body div-AgregarEditar show">
            <?php

            $attributes = array('id' => 'form-Sistema-Dominio');
            echo form_open('', $attributes);
            ?>
            <div class="row" <?php echo $sCssDisplayProveedorDropshipping; ?>>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 hidden-xs">
                <div class="form-group">
                  <h2 style="margin-top: 0px;"><strong>Dominio</strong></h2>
                </div>
              </div>

              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 hidden-xs hidden-sm hidden-md hidden-lg">
                <div class="form-group">
                  <h4 style="margin-top: 0px;"><strong>Dominio</strong></h4>
                </div>
              </div>
            </div>
            <div class="row" <?php echo $sCssDisplayProveedorDropshipping; ?>>
              <div class="col-md-12">
                <div id="panel-DetalleProductosOrdenVenta" class="panel panel-default">
                  <div class="panel-heading"><strong>Datos de Dominio</strong></div>
                  <div class="panel-body">
                    <div class="row">
                      <input type="hidden" name="EID_Configuracion_Dominio" class="form-control">
                      <input type="hidden" id="INT_Dominio_Asociado" value="<?php echo ($arrUrlTiendaVirtual->Nu_Tipo_Tienda == 1) ? "0" : "1"; ?>" />
                      <input type="hidden" name="ID_Subdominio_Tienda_Virtual" value="<?php echo $arrUrlTiendaVirtual->ID_Subdominio_Tienda_Virtual; ?>" />
                      <input type="hidden" name="ID_Empresa" value="<?php echo $arrUrlTiendaVirtual->ID_Empresa; ?>" />
                      <input type="hidden" name="hidden-tipo_dominio" value="<?php echo $arrUrlTiendaVirtual->Nu_Tipo_Tienda; ?>" />
                      <input type="hidden" name="hidden-nombre_subdominio" value="<?php echo $arrUrlTiendaVirtual->No_Subdominio_Tienda_Virtual; ?>" />
                      <input type="hidden" name="hidden-nombre_dominio" value="<?php echo $arrUrlTiendaVirtual->No_Dominio_Tienda_Virtual; ?>" />
                      
                      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="form-group">
                          <label style="cursor: pointer;">
                            <input type="radio" style="cursor: pointer;" name="Nu_Tipo_Tienda" id="Tipo_Tienda_Subdominio" value="1" <?php if($arrUrlTiendaVirtual->Nu_Tipo_Tienda == 1) { echo "checked"; } ?>> Subdominio <span style="font-weight: normal">(GRATIS)</span>
                          </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                          <label style="cursor: pointer;">
                            <input type="radio" style="cursor: pointer;" name="Nu_Tipo_Tienda" id="Tipo_Tienda_Dominio" value="3" <?php if($arrUrlTiendaVirtual->Nu_Tipo_Tienda == 3) { echo "checked"; }?>> Dominio
                          </label>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>

                      <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 div-Tipo-Tienda">
                        <div class="form-group hidden">
                          <label><?php echo ($arrUrlTiendaVirtual->Nu_Tipo_Tienda == 1) ? "SubDominio" : "Dominio"; ?> <span class="label-advertencia">*</span></label>
                          <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="El dominio no puede contener mayúsculas ni caracteres especiales como '!%$&'. Máximo 20 caracteres.">
                              <i class="fa fa-info-circle"></i>
                            </span>
                        </div>
                        <div class="form-group div-Subdominio <?php echo  $arrUrlTiendaVirtual->Nu_Tipo_Tienda != 1 ? "hidden" : ""; ?> ">
                          <div class="input-group">
                              <input type="text" inputmode="text" autocorrect="off" autocapitalize="none" id="txt-No_Subdominio_Tienda_Virtual" name="No_Subdominio_Tienda_Virtual" class="form-control input-subdominio input-Minuscula" placeholder="" maxlength="20" autocomplete="off" aria-describedby="basic-addon2">
                              <span class="input-group-addon" id="basic-addon2">.compramaz.com</span>
                          </div>
                          <span class="help-block" id="error"></span>
                        </div>
                        <div class="form-group div-Dominio <?php echo  $arrUrlTiendaVirtual->Nu_Tipo_Tienda != 3 ? "hidden" : ""; ?> ">
                          <div class="input-group">
                              <input type="text" inputmode="text" autocorrect="off" autocapitalize="none" id="txt-No_Dominio_Tienda_Virtual" name="No_Dominio_Tienda_Virtual" class="form-control input-dominio input-Minuscula" placeholder="" autocomplete="off" aria-describedby="basic-addon4">
                              <span class="input-group-btn"  id="basic-addon4"><button class="btn btn-primary" id="btn-verificar-dominio" type="button">Verificar</button></span>
                          </div>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                    
                      <div class="col-xs-12 col-md-12 hidden">
                        <div class="form-group">
                          <button type="button" class="btn btn-danger btn-lg btn-block btn-cancelar">Cancelar</button>
                        </div>
                      </div>
                      <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                        <div class="form-group">
                          <button type="submit" id="btn-save-Dominio" class="btn btn-success btn-md btn-block">Guardar</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <?php echo form_close(); ?>
            <?php
            $attributes = array('id' => 'form-Sistema');
            echo form_open('', $attributes);
            ?>
              <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 hidden-xs">
                  <div class="form-group">
                    <h2 style="margin-top: 0px;"><strong>Información de Tienda</strong></h2>
                  </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 hidden-xs hidden-sm hidden-md hidden-lg">
                  <div class="form-group">
                    <h4 style="margin-top: 0px;"><strong>Información de Tienda</strong></h4>
                  </div>
                </div>
              </div>

              <input type="hidden" name="ID" value="<?php echo $ID; ?>">
              <input type="hidden" name="No_Imagen_Logo_Empresa" value="<?php echo $No_Imagen_Logo_Empresa; ?>">
              <input type="hidden" name="Nu_Version_Imagen" value="<?php echo $Nu_Version_Imagen; ?>">
          	  <input type="hidden" name="EID_Configuracion" class="form-control">
          	  <input type="hidden" id="hidden-nombre_imagen_logo_empresa" name="Txt_Url_Logo_Lae_Shop" class="form-control" value="">
              
              <div class="row" <?php echo $sCssDisplayRoot; ?>>
                <div class="col-xs-12">
                  <div class="form-group">
                    <label>Empresa <span class="label-advertencia">*</span></label>
        	  				<select id="cbo-Empresas" name="ID_Empresa" class="form-control select2 required" style="width: 100%;"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
              </div>
                				  
              <!-- Orden de Venta -->
      			  <div class="row">
                <div class="col-md-12">
        			    <div id="panel-DetalleProductosOrdenVenta" class="panel panel-default">
                    <div class="panel-heading"><strong>Datos de Tienda</strong></div>
                    <div class="panel-body">
                      <div class="row">
                        <div class="col-md-8">
                          

                          <div class="col-xs-12 col-sm-4 col-md-4">
                            <label>Nombre <span class="label-advertencia">*</span></label>
                            <div class="form-group">
                              <input type="text" id="txt-No_Tienda_Lae_Shop" name="No_Tienda_Lae_Shop" class="form-control required" placeholder="" maxlength="100" autocomplete="off">
                              <span class="help-block" id="error"></span>
                            </div>
                          </div>
                
                          <div class="col-xs-12 col-sm-8 col-md-8">
                            <label>Correo <span class="label-advertencia">*</span></label>
                            <div class="form-group">
                              <input type="text" inputmode="email" name="Txt_Email_Lae_Shop" placeholder="Ingresar correo" class="form-control required" maxlength="50" autocomplete="off">
                              <span class="help-block" id="error"></span>
                            </div>
                          </div>
                          
                          <div class="col-xs-6 col-sm-6 col-md-3">
                            <label>Codigo País</label>
                            <div class="form-group">
                              <input type="text" inputmode="tel" name="Nu_Codigo_Pais_Celular_Lae_Shop" class="form-control input-number" maxlength="4">
                              <span class="help-block" id="error"></span>
                            </div>
                          </div>
                          
                          <div class="col-xs-6 col-sm-6 col-md-3">
                            <label>Celular</label>
                            <div class="form-group">
                              <input type="text" inputmode="tel" name="Nu_Celular_Lae_Shop" class="form-control input-number" maxlength="15">
                              <span class="help-block" id="error"></span>
                            </div>
                          </div>
                          
                          <div class="col-xs-6 col-sm-6 col-md-3">
                            <label>Codigo País</label>
                            <div class="form-group">
                              <input type="text" inputmode="tel" name="Nu_Codigo_Pais_Celular_Whatsapp_Lae_Shop" class="form-control input-number" maxlength="4">
                              <span class="help-block" id="error"></span>
                            </div>
                          </div>
                          
                          <div class="col-xs-6 col-sm-6 col-md-3">
                            <label>WhatsApp <span class="label-advertencia">*</span></label>
                            <div class="form-group">
                              <input type="text" inputmode="tel" name="Nu_Celular_Whatsapp_Lae_Shop" class="form-control input-number required" maxlength="15">
                              <span class="help-block" id="error"></span>
                            </div>
                          </div>
                          
                          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-8">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="padding-left: 0px;">
                              <label>Descripción</label>
                              <div class="form-group">
                                <!--<textarea name="Txt_Descripcion_Lae_Shop" rows="5" class="form-control"  placeholder="Opcional"></textarea>-->
                                <input type="hidden" name="Txt_Descripcion_Lae_Shop" class="form-control" autocomplete="off" />
                                <div id="textarea-descripcion_tienda"></div>
                              </div>
                            </div>

                            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6" style="padding-left: 0px;<?php echo $sCssDisplayProveedorDropshipping_propertie; ?>">
                              <div class="form-group">
                                <label>Formulario Item</label>
                                <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Al ACTIVAR se visualizará el formulario en la sección VER PRODUCTO de la TIENDA VIRTUAL">
                                  <i class="fa fa-info-circle"></i>
                                </span>
                                <select id="cbo-formulario_ver_item" name="Nu_Activar_Formulario_Tienda_Virtual_Ver_Item" class="form-control required"></select>
                                <span class="help-block" id="error"></span>
                              </div>
                            </div>

                            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6" style="padding-left: 0px;<?php echo $sCssDisplayProveedorDropshipping_propertie; ?>">
                              <div class="form-group">
                                <label>Gestion Pedidos</label>
                                <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Si es Callcenter S/ 4 por pedido entregado y si es coordinado es GRATIS">
                                  <i class="fa fa-info-circle"></i>
                                </span>
                                <select id="cbo-tipo_gestion_pedido_tienda_virtual" name="Nu_Tipo_Gestion_Pedido_Tienda_Virtual" class="form-control required"></select>
                                <span class="help-block" id="error"></span>
                              </div>
                            </div>

                            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6" style="padding-left: 0px;<?php echo $sCssDisplayProveedorDropshipping_propertie; ?>">
                              <div class="form-group">
                                <label>WhatsApp Ver Producto</label>
                                <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Si esta visibles, estará en ver item en la parte inferior derecha">
                                  <i class="fa fa-info-circle"></i>
                                </span>
                                <select id="cbo-estado_tienda_whatsapp_ver_producto" name="Nu_Estado_Tienda_Whatsapp_Ver_Producto" class="form-control required"></select>
                                <span class="help-block" id="error"></span>
                              </div>
                            </div>

                            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6" style="padding-left: 0px;<?php echo $sCssDisplayProveedorDropshipping_propertie; ?>">
                              <div class="form-group">
                                <label>Contador de Item</label>
                                <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Al ACTIVAR se visualizará el formulario en la sección VER PRODUCTO de la TIENDA VIRTUAL">
                                  <i class="fa fa-info-circle"></i>
                                </span>
                                <select id="cbo-contador_item" name="Nu_Estado_Contador_Item_Tienda" class="form-control required"></select>
                                <span class="help-block" id="error"></span>
                              </div>
                            </div>
                          </div>

                          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-4" style="margin-bottom: 2rem; <?php echo $sCssDisplayProveedorDropshipping_propertie; ?>">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-left">
                              <label>Color de Tienda</label>
                              <div class="pickshell">
                                <div class="picker" data-hsv="250,60,78">
                                  <a href="#change" class="icon change"></a>
                                  <input type="hidden" class="No_Html_Color_HSV_Lae_Shop form-control" name="No_Html_Color_HSV_Lae_Shop" value="" />
                                  <input type="text" class="No_Html_Color_Lae_Shop form-control" name="No_Html_Color_Lae_Shop" value="" />
                                  <div class="board">
                                    <div class="choice"></div>
                                  </div>
                                  <div class="rainbow"></div>
                                </div>
                              </div>
                            </div>
                          </div>
                          
                          <div class="col-xs-9 col-sm-4 col-md-5 col-lg-2 hidden">
                            <label>Color de Tienda<span class="hidden-xs"></span></label>
                            <div class="form-group">
                              <select id="cbo-color" name="No_Html_Color_Lae_Shop_" class="form-control required" style="width: 100%;"></select>
                              <span class="help-block" id="error"></span>
                            </div>
                          </div>

                          <div class="col-xs-3 col-sm-3 col-md-2 col-lg-2 text-center hidden">
                            <label>&nbsp;</label>
                            <div class="form-group text-center">
                              <div class="background text-center" style="border-radius: 50%;width: 40px;height: 40px; background: #141619;"></div>
                            </div>
                          </div>
                        </div>
                        
                        <div class="col-xs-12 col-sm-12 col-md-4">
            	            <div class="col-md-12">
                            <label>Logo</label>
                            <div class="form-group">
                              <div class="text-center divDropzone"></div>
                            </div>
                          </div>
                          
            	            <div class="col-md-12 hidden-xs">
                            <div class="form-group"><br>
                              <div class="well well-sm">
                                <strong><i class="fa fa-warning"></i> Indicaciones:</strong>
                                <br>- Formatos: <b>.jpeg | .jpg | .png</b>
                                <br>- Peso: <b>1 MB</b>
                                <br>- Tamaños:
                                <br><b>Formato cuadrado: Ancho 512 x 512 px</b>
                                <br><b>Formato rectangular: Ancho 140 x 70 px</b>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <!-- row -->
                    </div>
                  </div>
                </div>
              </div>
              <!-- ./ Orden Compra -->
            
                				  
              <!-- Orden de Venta -->
      			  <div class="row hidden">
                <div class="col-md-12">
        			    <div id="panel-DetalleProductosOrdenVenta" class="panel panel-default">
                    <div class="panel-heading"><strong>Datos Adicionales</strong></div>
                    <div class="panel-body">
                      <div class="row">
                        <div class="col-md-12">
                          <div class="col-xs-12 col-sm-4 col-md-3 col-lg-2 hidden">
                            <div class="form-group">
                              <label>Validar Stock</label>
                              <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Al elegir (SI), solo se podrá vender si su stock es > 0, si eligen (NO) entonces podrán vender sin stock">
                                <i class="fa fa-info-circle"></i>
                              </span>
                              <select id="cbo-activar_stock" name="Nu_Validar_Stock_Laeshop" class="form-control required"></select>
                              <span class="help-block" id="error"></span>
                            </div>
                          </div>

                          <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3 hidden">
                            <div class="form-group">
                              <label>Precio Centralizado</label>
                              <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Al elegir (SI) los precios de tienda y gestion serán iguales, si eligen (NO) el precio será independiente para tienda virtual y física">
                                <i class="fa fa-info-circle"></i>
                              </span>
                              <select id="cbo-precio_centralizado_laeshop" name="Nu_Activar_Precio_Centralizado_Laeshop" class="form-control required"></select>
                              <span class="help-block" id="error"></span>
                            </div>
                          </div>

                          <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3 hidden">
                            <div class="form-group">
                              <label>Emitir Factura</label>
                              <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Al elegir (SI) se mostrará la opción FACTURAR, si eligen (NO) se ocultará la opción FACTURAR para la tienda">
                                <i class="fa fa-info-circle"></i>
                              </span>
                              <select id="cbo-emitir_factura" name="Nu_Activar_Emitir_Factura_Laeshop" class="form-control required"></select>
                              <span class="help-block" id="error"></span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- ./ Orden Compra -->

              <!-- Paginas -->
              <div class="row" <?php echo $sCssDisplayProveedorDropshipping; ?>>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                  <div class="form-group">
                    <h2 style="margin-top: 0px;"><strong>Páginas</strong> <button type="button" id="btn-agregar_paginas" class="btn btn-link btn-lg" data-agregar_paginas="0">agregar</button></h2>
                  </div>
                </div>
              </div>

      			  <div class="row div-agregar_paginas">
                <div class="col-md-12">
        			    <div id="panel-DetalleProductosOrdenVenta" class="panel panel-default">
                    <div class="panel-heading"><strong>Términos y Condiciones </strong> <button type="button" id="btn-terminos" class="btn btn-link" onclick="importarPaginas('terminos')">Importar</button></div>
                    <div class="panel-body">
                      <div class="row">
                        <div class="col-xs-12">
                          <div class="form-group">
                            <input type="hidden" name="Txt_Page_Landing_Terminos" class="form-control" autocomplete="off" />
                            <div id="textarea-terminos_condiciones"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="col-md-12">
        			    <div id="panel-DetalleProductosOrdenVenta" class="panel panel-default">
                    <div class="panel-heading"><strong>Política de Privacidad <button type="button" id="btn-privacidad" class="btn btn-link" onclick="importarPaginas('privacidad')">Importar</button></strong></div>
                    <div class="panel-body">
                      <div class="row">
                        <div class="col-xs-12">
                          <div class="form-group">
                            <input type="hidden" name="Txt_Page_Landing_Politica" class="form-control" autocomplete="off" />
                            <div id="textarea-politica_privacidad"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="col-md-12">
        			    <div id="panel-DetalleProductosOrdenVenta" class="panel panel-default">
                    <div class="panel-heading"><strong>Devoluciones </strong> <button type="button" id="btn-devoluciones" class="btn btn-link" onclick="importarPaginas('devoluciones')">Importar</button></strong></div>
                    <div class="panel-body">
                      <div class="row">
                        <div class="col-xs-12">
                          <div class="form-group">
                            <input type="hidden" name="Txt_Page_Landing_Devolucion" class="form-control" autocomplete="off" />
                            <div id="textarea-devoluciones"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="col-md-12">
        			    <div id="panel-DetalleProductosOrdenVenta" class="panel panel-default">
                    <div class="panel-heading"><strong>Política de Envío <button type="button" id="btn-politica_envio" class="btn btn-link" onclick="importarPaginas('politica_envio')">Importar</button></strong></div>
                    <div class="panel-body">
                      <div class="row">
                        <div class="col-xs-12">
                          <div class="form-group">
                            <input type="hidden" name="Txt_Page_Landing_Envio" class="form-control" autocomplete="off" />
                            <div id="textarea-politica_envio"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- ./ Paginas -->

              <!-- Integraciones -->
              <div class="row" <?php echo $sCssDisplayProveedorDropshipping; ?>>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                  <div class="form-group">
                    <h2 style="margin-top: 0px;"><strong>Intregaciones</strong> <button type="button" id="btn-agregar_integraciones" class="btn btn-link btn-lg" data-agregar_integraciones="0">agregar</button></h2>
                  </div>
                </div>
              </div>

      			  <div class="row div-agregar_integraciones">
                <div class="col-md-12">
        			    <div id="panel-DetalleProductosOrdenVenta" class="panel panel-default">
                    <div class="panel-heading"><strong>Integraciones</strong></div>
                    <div class="panel-body">
                      <div class="row">
                        <div class="col-xs-12">
                          <label>Facebook Verificación de Dominio</label>
                          <div class="form-group">
                            <input type="text" name="Txt_Facebook_Pixel_Codigo_Dominio_Lae_Shop" class="form-control"  placeholder="Opcional" autocomplete="off" />
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-xs-12">
                            <label>Facebook Pixel ID</label>
                            <div class="form-group">
                              <input type="text" name="Txt_Facebook_Pixel_Lae_Shop" class="form-control"  placeholder="Opcional" autocomplete="off" />
                            </div>
                          </div>
                      </div>
                      <div class="row Div_Facebook_Url_Lae_Shop hidden">
                        <div class="col-xs-12">
                            <label>Facebook Shop Url</label>
                            <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Copia el enlace de abajo para que puedas sincronizar tus productos en Facebook Shop e Instagram Shop">
                              <i class="fa fa-info-circle"></i>
                            </span>
                            <input type="hidden" id="Txt_Facebook_Url_Lae_Shop" value="" />
                            <div class="form-group">
                            </div>
                          </div>
                      </div>
                      <div class="row">
                        <div class="col-xs-12">
                            <label>Tiktok Pixel ID</label>
                            <div class="form-group">
                              <input type="text" name="Txt_Tiktok_Pixel_Lae_Shop" class="form-control"  placeholder="Opcional" autocomplete="off" />
                            </div>
                          </div>
                      </div>
                      <div class="row">
                        <div class="col-xs-12">
                            <label>Google Analytics</label>
                            <div class="form-group">
                              <input type="text" name="Txt_Google_Analytics_Lae_Shop" class="form-control"  placeholder="Opcional" autocomplete="off" />
                            </div>
                          </div>
                      </div>
                      <div class="row">
                        <div class="col-xs-12">
                            <label>Google Shopping Verificación Dominio</label>
                            <div class="form-group">
                              <input type="text" name="Txt_Google_Shopping_Dominio_Lae_Shop" class="form-control"  placeholder="Opcional" autocomplete="off" />
                            </div>
                          </div>
                      </div>
                      <div class="row">
                        <div class="col-xs-12" style="padding: 0px;">
                          <div class="col-sm-6">
                            <label>Dominio Shopify</label>
                            <div class="form-group">
                              <input type="text" name="No_Dominio_Externo" class="form-control"  placeholder="Opcional" autocomplete="off" />
                            </div>
                          </div>
                          <div class="col-sm-6">
                            <label>Webhook Shopify</label>
                            <div class="form-group">
                              <input type="text" name="Txt_Llave_Externa" class="form-control"  placeholder="Opcional" autocomplete="off" />
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row Div_Google_Shopping_Url_Lae_Shop hidden">
                        <div class="col-xs-12">
                            <label>Google Shopping Url</label>
                            <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Copia el enlace de abajo para que puedas sincronizar tus productos en Google Shopping">
                              <i class="fa fa-info-circle"></i>
                            </span>
                            <input type="hidden" id="Txt_Google_Shopping_Url_Lae_Shop" value="" />
                            <div class="form-group">
                            </div>
                          </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- ./ Integraciones -->

              <!-- Modal fomulario_item_tienda -->
              <div class="modal fade modal-fomulario_item_tienda" id="modal-default">
                <div class="modal-dialog modal-md">
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      <h4 class="text-center">Formulario Item</h4>
                    </div>
                    <div class="modal-body">
                      <div class="row">
                        <div class="col-xs-12">
                          <label>Tipo Formulario</label>
                          <div class="form-group">
                            <label style="cursor: pointer;">
                              <input type="radio" style="cursor: pointer;" name="radio-tipoFormularioItem" class="flat-red" id="radio-formulario_item_fijo" value="1" checked> Fijo
                            </label>
                            <label style="cursor: pointer;">
                              &nbsp;<input type="radio" style="cursor: pointer;" name="radio-tipoFormularioItem" class="flat-red" id="radio-formulario_item_modal" value="2"> Flotante
                            </label>
                          </div>
                        </div>

                        <div class="col-xs-12">
                          <label>Título de Cabecera</label>
                          <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Título del formulario, se puede agregar emojis">
                            <i class="fa fa-info-circle"></i>
                          </span>
                          <div class="form-group">
                            <input type="text" inputmode="text" id="txt-modal-Txt_Titulo_Cabecera_Fomulario_Item_Tienda" name="Txt_Titulo_Cabecera_Fomulario_Item_Tienda" class="form-control required" maxlength="250" autocomplete="off">
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>
                        <div class="col-xs-12">
                          <label>Botón</label>
                          <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Nombre de botón, se puede agregar emojis">
                            <i class="fa fa-info-circle"></i>
                          </span>
                          <div class="form-group">
                            <input type="text" inputmode="text" id="txt-modal-Txt_Boton_Fomulario_Item_Tienda" name="Txt_Boton_Fomulario_Item_Tienda" class="form-control required" maxlength="50" autocomplete="off">
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>
                        <div class="col-xs-12">
                          <label>Pie de Página</label>
                          <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Pie de página del formulario, se puede agregar emojis">
                            <i class="fa fa-info-circle"></i>
                          </span>
                          <div class="form-group">
                            <input type="text" inputmode="text" id="txt-modal-Txt_Titulo_Pie_Pagina_Fomulario_Item_Tienda" name="Txt_Titulo_Pie_Pagina_Fomulario_Item_Tienda" class="form-control required" maxlength="250" autocomplete="off">
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <div class="col-xs-6 col-md-6">
                        <div class="form-group">
                          <button type="button" class="btn btn-danger btn-lg btn-block pull-center" data-dismiss="modal">Salir</button>
                        </div>
                      </div>
                      <div class="col-xs-6 col-sm-6">
                        <div class="form-group">
                          <button type="button" class="btn btn-primary btn-lg btn-block pull-center" data-dismiss="modal">Aceptar</button>
                        </div>
                      </div>
                    </div>
                  </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
              </div><!-- /. Modal fomulario_item_tienda -->
              
              <!-- Modal formulario_contador_tiempo_item -->
              <div class="modal fade modal-formulario_contador_tiempo_item" id="modal-default">
                <div class="modal-dialog modal-sm">
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      <h4 class="text-center">Contador de Tiempo en Item</h4>
                    </div>
                    <div class="modal-body">
                      <div class="row">
                        <div class="col-xs-12">
                          <label>Tiempo en minutos</label>
                          <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Si colocas 60 minutos es 1 hora y si colocas 1440 minutos es 1 día">
                            <i class="fa fa-info-circle"></i>
                          </span>
                          <div class="form-group">
                            <input type="text" inputmode="text" id="txt-modal-Nu_Tiempo_Minutos_Contador_Item_Tienda" name="Nu_Tiempo_Minutos_Contador_Item_Tienda" class="form-control required" maxlength="11" autocomplete="off">
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <div class="col-xs-6 col-md-6">
                        <div class="form-group">
                          <button type="button" class="btn btn-danger btn-lg btn-block pull-center" data-dismiss="modal">Salir</button>
                        </div>
                      </div>
                      <div class="col-xs-6 col-sm-6">
                        <div class="form-group">
                          <button type="button" class="btn btn-primary btn-lg btn-block pull-center" data-dismiss="modal">Aceptar</button>
                        </div>
                      </div>
                    </div>
                  </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
              </div><!-- /. Modal formulario_contador_tiempo_item -->

      			  <div class="row">
                <div class="col-xs-6 col-md-6 hidden">
                  <div class="form-group">
                    <button type="button" class="btn btn-danger btn-lg btn-block btn-cancelar">Cancelar</button>
                  </div>
                </div>
                <div class="col-xs-12 col-md-12">
                  <div class="form-group">
                    <button type="submit" id="btn-save" class="btn btn-success btn-lg btn-block btn-verificar">Guardar</button>
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
