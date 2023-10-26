<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

<?php //array_debug($this->empresa);
$sClassHiddenTienda='hidden';
if ( $this->empresa->Nu_Lae_Shop == '1' ){//La cava del baco
  $sClassHiddenTienda='';
}
$sCssDisplayRoot='style="display:none"';
if ( $this->user->ID_Usuario == 1 ){
  $sCssDisplayRoot='';
}

$sCssDisplayView='style="display:none"';
if ( $this->user->ID_Usuario == 1 ){
  $sCssDisplayView='';
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
      <!-- ./New box-header -->
    </div>
    
    <div class="row">
      <div class="col-xs-12">
        <div class="box box-content">
          <!-- box-header -->
          <div class="box-header box-header-new">
            <div class="row div-Filtros">
              <br>
              <?php
              if ( $this->user->ID_Usuario == 1 ){ ?>
              <div class="col-xs-12 col-sm-4 col-md-6 col-lg-6">
                <div class="form-group">
                  <label>Empresa</label>
                  <select id="cbo-filtro_empresa" class="form-control select2" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              <div class="col-xs-12 col-sm-4 col-md-6 col-lg-6">
                <div class="form-group">
                  <label>Organización</label>
                  <select id="cbo-filtro_organizacion" class="form-control select2" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-4 col-md-2 col-lg-3 hidden">
                <div class="form-group">
                  <label>Estado Sistema</label>
                  <select id="cbo-filtro-estado_laegestion" class="form-control" style="width: 100%;">
                    <option value="-" selected="selected">Todos</option>
                    <option value="1">Activo</option>
                    <option value="0">Inactivo</option>
                  </select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-4 col-md-2 col-lg-3 hidden">
                <div class="form-group">
                  <label>Estado Tienda</label>
                  <select id="cbo-filtro-estado_laeshop" class="form-control" style="width: 100%;">
                    <option value="-" selected="selected">Todos</option>
                    <option value="1">Activo</option>
                    <option value="0">Inactivo</option>
                  </select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-4 col-md-2 col-lg-3 hidden">
                <div class="form-group">
                  <label>Pago Sistema</label>
                  <select id="cbo-filtro-estado_pago" class="form-control" style="width: 100%;">
    		  				  <option value="-" selected="selected">Todos</option>
    		  				  <option value="0">Pendiente</option>
                    <option value="1">Cancelado</option>
                  </select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-4 col-md-2 hidden">
                <div class="form-group">
                  <label>Tipo Sistema</label>
                  <select id="cbo-filtro-tipo_sistema" class="form-control" style="width: 100%;">
    		  				  <option value="" selected="selected">Todos</option>
    		  				  <option value="2">SUNAT</option>
                    <option value="1">PSE</option>
                    <option value="3">INTERNO</option>
                  </select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-6 col-sm-4 col-md-2 hidden">
                <div class="form-group">
                  <label>Estado Sistema</label>
                  <select id="cbo-filtro-estado_sistema" class="form-control" style="width: 100%;">
    		  				  <option value="-" selected="selected">Todos</option>
    		  				  <option value="0">Demostración</option>
                    <option value="1">Producción</option>
                  </select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-4 col-md-2 col-lg-3 hidden">
                <div class="form-group">
                  <label>Pago Tienda</label>
                  <select id="cbo-filtro-estado_pago_laeshop" class="form-control" style="width: 100%;">
                    <option value="-" selected="selected">Todos</option>
                    <option value="0">Pendiente</option>
                    <option value="1">Cancelado</option>
                  </select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              <?php } else { ?>
                <input type="hidden" id="cbo-filtro_empresa" class="form-control" value="<?php echo $this->user->ID_Empresa; ?>">
                <input type="hidden" id="cbo-filtro_organizacion" class="form-control" value="<?php echo $this->user->ID_Organizacion; ?>">
                <input type="hidden" id="cbo-filtro-estado_laegestion" class="form-control" value="<?php echo $this->empresa->Nu_Lae_Gestion; ?>">
                <input type="hidden" id="cbo-filtro-estado_laeshop" class="form-control" value="<?php echo $this->empresa->Nu_Lae_Shop; ?>">

                <input type="hidden" id="cbo-filtro-tipo_sistema" class="form-control" value="<?php echo $this->empresa->Nu_Tipo_Proveedor_FE; ?>">
                <input type="hidden" id="cbo-filtro-estado_sistema" class="form-control" value="<?php echo $this->empresa->Nu_Estado_Sistema; ?>">
                <input type="hidden" id="cbo-filtro-estado_pago" class="form-control" value="<?php echo $this->empresa->Nu_Estado_Pago_Sistema; ?>">
                <input type="hidden" id="cbo-filtro-estado_pago_laeshop" class="form-control" value="<?php echo $this->empresa->Nu_Estado_Pago_Sistema_Laeshop; ?>">
              <?php } ?>

              <div class="col-xs-6 col-sm-4 col-md-3 col-lg-3">
                <label>Filtro</label>
                <div class="form-group">
    		  				<select id="cbo-Filtros_Almacenes" class="form-control">
    		  				  <option value="Almacen">Nombre Almacén</option>
    		  				  <option value="Organizacion">Nombre Organización</option>
    		  				</select>
                </div>
              </div>
              
              <div class="col-xs-6 col-sm-4 col-md-4 col-lg-6">
                <label>&nbsp;</label>
                <div class="form-group">
                  <input type="text" id="txt-Global_Filter" class="form-control" maxlength="100" placeholder="Buscar" value="" autocomplete="off">
                </div>
              </div>
              
              <?php if ( $this->user->ID_Usuario == 1 || $this->empresa->Nu_Proveedor_Dropshipping == 1) : ?>
              <div class="col-xs-12 col-sm-4 col-md-3 col-lg-3">
                <label class="hidden-xs">&nbsp;</label>
                <button type="button" class="btn btn-success btn-block" onclick="agregarAlmacen()"><i class="fa fa-plus-circle"></i> Agregar</button>
              </div>
              <?php endif; ?>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="table-responsive">
            <table id="table-Almacen" class="table table-striped table-bordered">
              <thead>
              <tr>
                <!--<th>Modo</th>-->
                <!--<th>Tipo</th>-->
                <th>Empresa</th>
                <th>Organización</th>
                <th>Almacén</th>
                <th>Departamento</th>
                <th>Provincia</th>
                <th>Distrito</th>
                <th>Dirección</th>
                <th class="no-sort">Estado</th>
			          <?php if ( $this->user->ID_Usuario == 1 ) : ?>
                <!--
                  <th>Pago Sistema</th>
                  <th>F. Vcto. Sistema</th>
                -->
                  <th>Pago Tienda</th>
                  <th>F. Vencimiento</th>
                <?php endif; ?>
                <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Editar == 1) : ?>
                  <th class="no-sort">Editar</th>
                <?php endif; ?>
                <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Eliminar == 1) : ?>
                  <th class="no-sort">Eliminar</th>
                <?php endif; ?>
              </tr>
              </thead>
            </table>
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
  <!-- Modal -->
  <?php
  $attributes = array('id' => 'form-Almacen');
  echo form_open('', $attributes);
  ?>
  <div class="modal fade" id="modal-Almacen" role="dialog">
  <div class="modal-dialog modal-lg">
  	<div class="modal-content">      
    	<div class="modal-body">
    	  <input type="hidden" name="EID_Organizacion" class="form-control required">
    	  <input type="hidden" name="EID_Almacen" class="form-control required">
    	  <input type="hidden" name="ENo_Almacen" class="form-control required">
        <input type="hidden" id="hidden-No_Logo_Almacen" name="No_Logo_Almacen" class="form-control" value="">
        <input type="hidden" id="hidden-No_Logo_Url_Almacen" name="No_Logo_Url_Almacen" class="form-control" value="">
    	  
			  <div class="row">
          <div class="col-xs-12 col-md-12">
            <h4 class="modal-title text-center"></h4>
          </div>
          
          <?php if ( $this->user->ID_Usuario == 1 ){ ?>
          <div class="col-xs-12 col-sm-6 col-md-6">
            <label>Empresa <span class="label-advertencia">*</span></label>
            <div class="form-group">
              <select id="cbo-Empresas" name="ID_Empresa" class="form-control select2 required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <?php } else { ?>
            <input type="hidden" id="cbo-Empresas" name="ID_Empresa" class="form-control" value="<?php echo $this->user->ID_Empresa; ?>">
          <?php } ?>
          
          <div class="col-xs-6 col-sm-3 col-md-2  hidden" <?php echo $sCssDisplayView; ?>>
            <div class="form-group">
              <label>Pago Sistema</label>
		  				<select id="cbo-Estado_Pago_Sistema" name="Nu_Estado_Pago_Sistema" class="form-control required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-6 col-sm-3 col-md-2  hidden" <?php echo $sCssDisplayView; ?>>
            <label>F. Vcto. Sistema</label>
            <div class="form-group">
              <div class="input-group date" style="width:100%">
                <input type="text" id="txt-Fe_Vencimiento_LaeGestion" name="Fe_Vencimiento_LaeGestion" class="form-control input-datepicker_todo" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
              </div>                
            </div>
          </div>

          <div class="col-xs-6 col-sm-3 col-md-2 " <?php echo $sCssDisplayView; ?>>
            <div class="form-group">
              <label>Pago Tienda</label>
              <select id="cbo-Estado_Pago_Sistema_Laeshop" name="Nu_Estado_Pago_Sistema_Laeshop" class="form-control required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-6 col-sm-3 col-md-2 " <?php echo $sCssDisplayView; ?>>
            <label>F. Vcto. Tienda</label>
            <div class="form-group">
              <div class="input-group date" style="width:100%">
                <input type="text" id="txt-Fe_Vencimiento_Laeshop" name="Fe_Vencimiento_Laeshop" class="form-control input-datepicker_todo" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
              </div>                
            </div>
          </div>

          <div class="col-xs-6 col-sm-4 col-md-3 hidden">
            <label>Cod. Establecimiento<span class="label-advertencia">*</span></label>
            <div class="form-group">
              <input type="text" id="txt-Nu_Codigo_Establecimiento_Sunat" name="Nu_Codigo_Establecimiento_Sunat" placeholder="Ingresar código" class="form-control input-Mayuscula input-codigo_barra" value="0000" maxlength="30" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>
        
        <div class="row">
          <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <div class="form-group">
              <label>Organización <span class="label-advertencia">*</span></label>
              <select id="cbo-Organizaciones" name="ID_Oganizacion" class="form-control select2 required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <label>Almacén <span class="label-advertencia">*</span></label>
            <div class="form-group">
              <input type="text" id="txt-No_Almacen" name="No_Almacen" placeholder="Ingresar descripción" class="form-control required" autocomplete="off" maxlength="100">
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-6 col-md-3">
            <div class="form-group">
              <label>País</label>
		  				<select id="cbo-Paises" name="ID_Pais" class="form-control select2" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-6 col-sm-4 col-md-3 col-lg-3">
            <div class="form-group">
              <label>Departamento <span class="label-advertencia">*</span></label>
		  				<select id="cbo-Departamentos" name="ID_Departamento" class="form-control select2" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-6 col-sm-4 col-md-3 col-lg-3">
            <div class="form-group">
              <label>Provincia <span class="label-advertencia">*</span></label>
		  				<select id="cbo-Provincias" name="ID_Provincia" class="form-control select2" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-6 col-sm-4 col-md-3 col-lg-3">
            <div class="form-group">
              <label>Distrito <span class="label-advertencia">*</span></label>
		  				<select id="cbo-Distritos" name="ID_Distrito" class="form-control select2" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-6 col-sm-12 col-md-3 hidden">
            <div class="form-group">
              <label>Ubigeo INEI <span class="label-advertencia">*</span></label>
		  				<select id="cbo-ubigeo_inei" name="ID_Ubigeo_Inei_Partida" class="form-control select2" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>
        
			  <div class="row">
          <div class="col-xs-12 col-md-12">
            <label>Dirección <span class="label-advertencia">*</span></label>
            <div class="form-group">
              <input type="hidden" id="txt-direccion-lat" name="Nu_Latitud_Maps" class="form-control" />
              <input type="hidden" id="txt-direccion-lng" name="Nu_Longitud_Maps" class="form-control" />
              <!--<input type="text" id="txt-direccion" name="Txt_Direccion_Almacen" placeholder="Ingresar descripción breve" class="form-control" autocomplete="off">-->
              <input type="text" id="txt-direccion" name="Txt_Direccion_Almacen" placeholder="Ingresar dirección" class="form-control required" autocomplete="off">
              <span class="help-block" id="error"></span>
              <!--<div id="map" class="hidden"></div>-->
            </div>
          </div>
        </div>
        
			  <div class="row hidden">
          <div class="col-md-6">
            <label>URL</label>
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="blue fa fa-key" aria-hidden="true"></i></span>
                <input type="password" name="Txt_FE_Ruta" placeholder="Ingresar URL" class="form-control pwd" autocomplete="off" />
                <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
              </div>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-md-6">
            <label>Token</label>
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="blue fa fa-key" aria-hidden="true"></i></span>
                <input type="password" name="Txt_FE_Token" placeholder="Ingresar Token" class="form-control pwd" autocomplete="off" />
                <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
              </div>
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>

			  <div class="row">
          <div class="col-md-6" <?php echo $sCssDisplayView; ?>>
            <label>URL Tienda</label>
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="blue fa fa-key" aria-hidden="true"></i></span>
                <input type="password" name="Txt_Ruta_Lae_Shop" placeholder="Ingresar URL" class="form-control pwd-laeshop" autocomplete="off" />
                <span toggle="#password-field-laeshop" class="fa fa-fw fa-eye field-icon toggle-password-laeshop"></span>
              </div>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-md-6" <?php echo $sCssDisplayView; ?>>
            <label>Token Tienda</label>
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="blue fa fa-key" aria-hidden="true"></i></span>
                <input type="password" name="Txt_Token_Lae_Shop" placeholder="Ingresar Token" class="form-control pwd-laeshop" autocomplete="off" />
                <span toggle="#password-field-laeshop" class="fa fa-fw fa-eye field-icon toggle-password-laeshop"></span>
              </div>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          

          <div class="col-xs-12 col-sm-12 col-md-12" <?php echo $sCssDisplayView; ?>>
            <div class="form-group">
              <label>Estado</label>
		  				<select id="cbo-Estado" name="Nu_Estado" class="form-control required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>

			  <div class="row div-generar-token_lae_fe">
          <?php if ( $this->user->ID_Usuario == 1 ){ ?>
          <div class="col-md-12">
            <label>Acceso lae FE</label>
            <div class="form-group">
              <a style="background-color: #4f4f4f !important; border-color: #4f4f4f;" href="https://laesystems.com/librerias/RegisterController" target="_blank" class="btn btn-primary btn-block" role="button">Generar token</a>
            </div>
          </div>
          <?php } ?>
        </div>
        
        <div class="row <?php echo ($this->empresa->Nu_MultiAlmacen == 0 ? 'hidden' : '') ?>">
          <div class="col-md-4">
            <div class="form-group">
              <div class="well well-sm">
                <i class="fa fa-warning"></i> Indicaciones:
                <br>- Formato: <b>JPG / JPEG / PNG</b>
                <br>- Tamaño de imagen referencial: <br><b>Alto: 150px y Ancho: 150px</b> ó <br><b>Alto: 80px y Ancho: 320px</b>
                <br>- Peso Máximo <b>1024 KB</b>
              </div>
            </div>
          </div>
          
          <div class="col-md-8 text-center divDropzone"></div>
        </div>
        
			  <div class="row">
          <div class="col-xs-6 col-md-6">
            <div class="form-group">
              <button type="button" class="btn btn-danger btn-md btn-block" data-dismiss="modal"><span class="fa fa-sign-out"></span> Salir</button>
            </div>
          </div>
          <div class="col-xs-6 col-md-6">
            <div class="form-group">
              <button type="submit" id="btn-save" class="btn btn-success btn-md btn-block btn-verificar"><i class="fa fa-save"></i> Guardar </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
  <?php echo form_close(); ?>
  <!-- /.Modal -->
</div>
<!-- /.content-wrapper -->