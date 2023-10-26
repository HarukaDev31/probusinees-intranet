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
          <h3>
            <i class="<?php echo $this->MenuModel->verificarAccesoMenuCRUD()->Txt_Css_Icons; ?>" aria-hidden="true"></i> <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?>
            &nbsp;<a href="<?php echo $this->MenuModel->verificarAccesoMenuCRUD()->Txt_Url_Video; ?>" target="_blank"><i class="fa fa-youtube-play red" aria-hidden="true" title="Video <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?>" alt="Video <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?>"></i> <span style="color: #7b7b7b; font-size: 14px;">ver video<span></a>
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
                  <select id="cbo-filtro_empresa" name="ID_Empresa" class="form-control select2 required" style="width: 100%;"></select>
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
                <input type="hidden" id="cbo-filtro_empresa" name="ID_Empresa" class="form-control" value="<?php echo $this->user->ID_Empresa; ?>">
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
          <div class="table-responsive div-Listar">
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
          
          <div class="box-body div-AgregarEditar">
            <?php
            $attributes = array('id' => 'form-Sistema');
            echo form_open('', $attributes);
            ?>
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
                          <?php if(!empty($arrUrlTiendaVirtual->No_Subdominio_Tienda_Virtual)) { ?>
                          <div class="col-xs-6 col-sm-4 col-md-4">
                            <div class="form-group">
                              <label>SubDominio <span class="label-advertencia">*</span></label>
                              <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="El dominio no puede contener mayúsculas ni caracteres especiales como '!%$&'. Máximo 20 caracteres.">
                                <i class="fa fa-info-circle"></i>
                              </span>
                              <div class="form-group">
                                <input type="text" id="txt-No_Subdominio_Tienda_Virtual" name="No_Subdominio_Tienda_Virtual" class="form-control input-subdominio required" placeholder="" maxlength="20" autocomplete="off">
                                <span class="help-block" id="error"></span>
                              </div>
                              <span class="hidden">
                                <b>¿Qué es SubDominio?</b><br>
                                El dominio es el nombre único con el que tus accederán a tu tienda virtual. Si el dominio ya está en uso, te pediremos que lo puedas cambiar.
                              </span>
                            </div>
                          </div>
                          <div class="col-xs-6 col-sm-4 col-md-4" style="padding-left: 0px;">
                            <label>&nbsp;</label>
                            <div class="form-group">
                              <div class="form-group">
                                <h4>.compramaz.com</h4>
                              </div>
                            </div>
                          </div>
                          <?php } ?>

                          <div class="col-xs-12 col-sm-4 col-md-4">
                            <label>Nombre <span class="label-advertencia">*</span></label>
                            <div class="form-group">
                              <input type="text" id="txt-No_Tienda_Lae_Shop" name="No_Tienda_Lae_Shop" class="form-control required" placeholder="" maxlength="100" autocomplete="off">
                              <span class="help-block" id="error"></span>
                            </div>
                          </div>
                          
                          <div class="col-xs-6 col-sm-4 col-md-4">
                            <label>Celular</label>
                            <div class="form-group">
                              <input type="text" inputmode="tel" name="Nu_Celular_Lae_Shop" class="form-control" data-inputmask="'mask': ['999 999 999']" data-mask autocomplete="off">
                              <span class="help-block" id="error"></span>
                            </div>
                          </div>
                          
                          <div class="col-xs-6 col-sm-4 col-md-4">
                            <label>WhatsApp <span class="label-advertencia">*</span></label>
                            <div class="form-group">
                              <input type="text" inputmode="tel" name="Nu_Celular_Whatsapp_Lae_Shop" class="form-control required" data-inputmask="'mask': ['999 999 999']" data-mask autocomplete="off">
                              <span class="help-block" id="error"></span>
                            </div>
                          </div>
                
                          <div class="col-xs-12 col-sm-12 col-md-12">
                            <label>Correo <span class="label-advertencia">*</span></label>
                            <div class="form-group">
                              <input type="text" inputmode="email" name="Txt_Email_Lae_Shop" placeholder="Ingresar correo" class="form-control required" maxlength="50" autocomplete="off">
                              <span class="help-block" id="error"></span>
                            </div>
                          </div>
                          
                          <div class="col-xs-12 col-sm-12 col-md-12">
                            <label>Descripción</label>
                            <div class="form-group">
                              <textarea name="Txt_Descripcion_Lae_Shop" rows="2" class="form-control"  placeholder="Opcional"></textarea>
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
                        </div>    
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- ./ Orden Compra -->
            
                				  
              <!-- Orden de Venta -->
      			  <div class="row">
                <div class="col-md-12">
        			    <div id="panel-DetalleProductosOrdenVenta" class="panel panel-default">
                    <div class="panel-heading"><strong>Datos Adicionales</strong></div>
                    <div class="panel-body">
                      <div class="row">
                        <div class="col-md-12">
                          <div class="col-xs-12 col-sm-4 col-md-3 col-lg-2">
                            <div class="form-group">
                              <label>Validar Stock</label>
                              <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Al elegir (SI), solo se podrá vender si su stock es > 0, si eligen (NO) entonces podrán vender sin stock">
                                <i class="fa fa-info-circle"></i>
                              </span>
                              <select id="cbo-activar_stock" name="Nu_Validar_Stock_Laeshop" class="form-control required"></select>
                              <span class="help-block" id="error"></span>
                            </div>
                          </div>
                          
                          <div class="col-xs-9 col-sm-4 col-md-2 col-lg-2">
                            <label>Color <span class="hidden-xs"></span></label>
                            <div class="form-group">
                              <select id="cbo-color" name="No_Html_Color_Lae_Shop" class="form-control required" style="width: 100%;"></select>
                              <span class="help-block" id="error"></span>
                            </div>
                          </div>

                          <div class="col-xs-3 col-sm-4 col-md-1 col-lg-2 text-center">
                            <label>&nbsp;</label>
                            <div class="form-group text-center">
                              <div class="background text-center" style="border-radius: 50%;width: 40px;height: 40px; background: #141619;"></div>
                            </div>
                          </div>

                          <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
                            <div class="form-group">
                              <label>Precio Centralizado</label>
                              <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Al elegir (SI) los precios de tienda y gestion serán iguales, si eligen (NO) el precio será independiente para tienda virtual y física">
                                <i class="fa fa-info-circle"></i>
                              </span>
                              <select id="cbo-precio_centralizado_laeshop" name="Nu_Activar_Precio_Centralizado_Laeshop" class="form-control required"></select>
                              <span class="help-block" id="error"></span>
                            </div>
                          </div>

                          <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
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

      			  <div class="row">
                <div class="col-xs-6 col-md-6">
                  <div class="form-group">
                    <button type="button" id="btn-cancelar" class="btn btn-danger btn-lg btn-block">Cancelar</button>
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