<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Main content -->
  <section class="content">
    <!-- New box-header -->
    <div class="row">
      <div class="col-xs-12">
        <div class="div-content-header">
          <h3>
            <i class="<?php echo $this->MenuModel->verificarAccesoMenuCRUD()->Txt_Css_Icons; ?>" aria-hidden="true"></i> Monitoreo de Cuentas
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
              <div class="col-xs-6 col-sm-6 col-md-3 hidden">
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

              <div class="col-xs-6 col-sm-6 col-md-3 hidden">
                <label>País</label>
                <div class="form-group">
                  <select id="cbo-filtro-pais" name="Nu_Estado" class="form-control required" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-6 col-md-3 hidden">
                <label>Estado Cuenta</label>
                <div class="form-group">
                  <select id="cbo-filtro-estado" name="Nu_Estado" class="form-control required" style="width: 100%;">
    		  				  <option value="" selected="selected">Todos</option>
    		  				  <option value="1">Activo</option>
    		  				  <option value="0">Inactivo</option>
                  </select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-6 col-md-3 hidden">
                <label>Estado Proveedor</label>
                <div class="form-group">
                  <select id="cbo-filtro-estado_proveedor" name="Nu_Estado" class="form-control required" style="width: 100%;">
                    <option value="" selected="selected">Todos</option>
                    <option value="1">Activo</option>
                    <option value="0">Inactivo</option>
                  </select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-4 col-md-3 hidden">
                <div class="form-group">
                  <label>Estado Sistema</label>
                  <select id="cbo-filtro-estado_laegestion" name="Nu_Lae_Gestion" class="form-control" style="width: 100%;">
                    <option value="-" selected="selected">Todos</option>
                    <option value="1">Activo</option>
                    <option value="0">Inactivo</option>
                  </select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-4 col-md-3 hidden">
                <div class="form-group">
                  <label>Estado Tienda</label>
                  <select id="cbo-filtro-estado_laeshop" name="Nu_Lae_Shop" class="form-control" style="width: 100%;">
                    <option value="-" selected="selected">Todos</option>
                    <option value="1">Activo</option>
                    <option value="0">Inactivo</option>
                  </select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-4 col-md-2 hidden">
                <div class="form-group">
                  <label>Guía</label>
                  <select id="cbo-filtro-guia" name="Nu_Filtro_Guia" class="form-control" style="width: 100%;">
                    <option value="-" selected="selected">Todos</option>
                    <option value="1">Activo</option>
                    <option value="0">Inactivo</option>
                  </select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-6 col-md-3 hidden">
                <label>Filtro</label>
                <div class="form-group">
    		  				<select id="cbo-Filtros_Empresas" name="Filtros_Empresas" class="form-control">
    		  				  <option value="Empresa">Nombre Empresa</option>
    		  				  <option value="RUC">DNI / OTROS / RUC</option>
    		  				</select>
                </div>
              </div>
              
              <div class="col-xs-12 col-sm-6 col-md-3 hidden">
                <label>Búsqueda por Filtro</label>
                <div class="form-group">
                  <input type="text" id="txt-Global_Filter" name="Global_Filter" class="form-control" maxlength="100" placeholder="Buscar" value="" autocomplete="off" autocomplete="off">
                </div>
              </div>
              
              <div class="col-xs-12 col-sm-12 col-md-2 hidden">
                <label class="hidden-xs hidden-sm">&nbsp;</label>
                <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Agregar == 1) : ?>
                <button type="button" class="btn btn-success btn-block" onclick="agregarEmpresa()"><i class="fa fa-plus-circle"></i> Agregar</button>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="table-responsive div-Listar">
            <table id="table-Empresa" class="table table-striped table-bordered" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th>F. Registro</th>
                  <th>País</th>
                  <th>Empresa</th>
                  <th>Estado</th>
                  <th class="no-sort">Editar</th>
                  <!--<th class="no-sort">Eliminar</th>-->
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
  $attributes = array('id' => 'form-Empresa', 'enctype' => 'multipart/form-data');
  echo form_open('', $attributes);
  ?>
  <div class="modal fade" id="modal-Empresa" role="dialog">
  <div class="modal-dialog modal-lg">
  	<div class="modal-content">      
    	<div class="modal-body">
    	  <input type="hidden" id="txt-EID_Empresa" name="EID_Empresa" value="">
    	  <input type="hidden" id="txt-ENu_Documento_Identidad" name="ENu_Documento_Identidad" value="">
  
        <div class="row">
          <div class="col-xs-12 col-md-12">
            <h4 class="modal-title text-center"></h4><br>
          </div>

          <div class="col-xs-3 col-sm-3 col-md-2 hidden">
            <div class="form-group">
              <label>Sistema <span class="label-advertencia">*</span></label>
              <select id="cbo-tipo_proveedor_fe" name="Nu_Tipo_Proveedor_FE" class="form-control required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
            <div class="form-group">
              <label>T.D.I <span class="label-advertencia">*</span></label>
              <span data-toggle="tooltip" data-trigger="hover" data-placement="right" title="RUC / DNI / OTROS">
                <i class="fa fa-info-circle"></i>
              </span>
              <select id="cbo-TiposDocumentoIdentidad" name="ID_Tipo_Documento_Identidad" class="form-control required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-5 col-sm-3 col-md-2 col-lg-2">
            <div class="form-group">
              <label id="label-Nombre_Documento_Identidad">RUC <span class="label-advertencia">*</span></label>
              <input type="tel" id="txt-Nu_Documento_Identidad" name="Nu_Documento_Identidad" class="form-control required input-number" maxlength="11" placeholder="Ingresar número" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-3 col-sm-3 col-md-2 col-lg-2 text-center">
            <div class="form-group">
              <label>Api</label>
              <button type="button" id="btn-cloud-api_empresa" class="btn btn-success btn-block btn-md"><i class="fa fa-cloud-download fa-lg"></i></button>
            </div>
          </div>

          <div class="col-xs-3 col-sm-2 col-md-4 col-lg-2 hidden">
            <div class="form-group">
              <label>Guía</label>
              <span data-toggle="tooltip" data-trigger="hover" data-placement="right" title="¿Activar Guía Electrónica?">
                <i class="fa fa-info-circle"></i>
              </span>
              <select id="cbo-Activar_Guia_Electronica" name="Nu_Activar_Guia_Electronica" class="form-control required"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-6 col-sm-4 col-md-4 col-lg-3 hidden">
            <div class="form-group">
              <label>Multiple Almacén</label>
              <span data-toggle="tooltip" data-trigger="hover" data-placement="right" title="¿Activar multiples almacenes virtuales y no generará documentos electrónicos?">
                <i class="fa fa-info-circle"></i>
              </span>
              <select id="cbo-Agregar_Almacen_Virtual" name="Nu_Agregar_Almacen_Virtual" class="form-control required"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <label id="label-No_Entidad">Razón Social <span class="label-advertencia"> *</span></label>
            <div class="form-group">
              <input type="text" name="No_Empresa" class="form-control required" placeholder="Ingresar nombre" maxlength="100" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-6 col-sm-6 col-md-5 col-lg-4 hidden">
            <label>Nombre Comercial</label>
            <div class="form-group">
              <input type="text" name="No_Empresa_Comercial" class="form-control" placeholder="Ingresar nombre" maxlength="100" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-6 col-sm-3 col-md-4 hidden">
            <div class="form-group">
              <label>¿Ecommerce?</label>
  	  				<select id="cbo-tipo_ecommerce_empresa" name="Nu_Tipo_Ecommerce_Empresa" class="form-control required"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>
          
        <div class="row div-row-empresas-marketplace hidden">
          <div class="col-xs-12 col-sm-3 col-md-12">
            <div class="form-group">
              <label>Empresas Marketplace <span class="label-advertencia">*</span></label>
              <select id="cbo-empresa-marketplace" name="ID_Empresa_Marketplace" class="form-control required"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>
        
  		  <div class="row">
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <label>Domicilio Fiscal <span class="label-advertencia">*</span></label>
            <div class="form-group">
              <input type="text" name="Txt_Direccion_Empresa" placeholder="Ingresar dirección" class="form-control required" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="form-group">
              <label>Estado</label>
              <select id="cbo-Estado" name="Nu_Estado" class="form-control required"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-4 col-sm-3 col-md-2 hidden">
            <div class="form-group">
              <label>Multi Logo</label>
              <span data-toggle="tooltip" data-trigger="hover" data-placement="right" title="Se activa, si manejará diferentes logo por cada sucursal / almacén.">
                <i class="fa fa-info-circle"></i>
              </span>
  	  				<select id="cbo-multi_almacen" name="Nu_MultiAlmacen" class="form-control required"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-8 col-sm-9 col-md-4 div-row-nubefact">
            <div class="form-group">
              <label>Ubigeo INEI <span class="label-advertencia">*</span></label>
		  				<select id="cbo-ubigeo_inei" name="ID_Ubigeo_Inei" title="Se encuentra en la ficha RUC" class="form-control select2" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>
        
        <?php if($this->empresa->Nu_Tipo_Sistema == 0 && $this->empresa->Nu_Tipo_Proveedor_FE == 1) { ?>
  		  <div class="row div-nubefact_reseller">
          <div class="col-xs-12 col-md-12">
            <div class="form-group">
              <a href="https://drive.google.com/file/d/1v1ujS7YSygYLfR4SxIBRwMK5fnfvpTIR/view?usp=sharing" target="_blank" style="font-weight: bold; display: block; width: 100%;" title="RECORDAR: Alta de SUNAT es al día siguiente o mayor a la fecha de HOY" alt="RECORDAR: Alta de SUNAT es al día siguiente o mayor a la fecha de HOY">Dar de alta SUNAT PSE <label style="color: #484848; font-weight: normal;">(RECORDAR: El alta en SUNAT es al día siguiente o mayor a la fecha de HOY)</label></a>
            </div>
          </div>
        </div>
        <?php } ?>

        <div class="row div-row-nubefact hidden">
          <div class="col-xs-6 col-md-3">
            <div class="form-group">
              <label>País <span class="label-advertencia">*</span></label>
		  				<select id="cbo-Paises" name="ID_Pais" class="form-control select2" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-6 col-md-3">
            <div class="form-group">
              <label>Departamento <span class="label-advertencia">*</span></label>
		  				<select id="cbo-Departamentos" name="ID_Departamento" class="form-control select2" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-6 col-md-3">
            <div class="form-group">
              <label>Provincia <span class="label-advertencia">*</span></label>
		  				<select id="cbo-Provincias" name="ID_Provincia" class="form-control select2" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-6 col-md-3">
            <div class="form-group">
              <label>Distrito <span class="label-advertencia">*</span></label>
		  				<select id="cbo-Distritos" name="ID_Distrito" class="form-control select2" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>

  		  <div class="row div-row-nubefact">
          <div class="col-xs-6 col-sm-3">
            <div class="form-group">
              <label>Usuario Secundario SOL<span class="label-advertencia">*</span></label>
              <input type="text" name="Txt_Usuario_Sunat_Sol" placeholder="Ingresar dirección" class="form-control required input-Mayuscula" autocomplete="off" value="MODDATOS">
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-6 col-sm-3">
            <div class="form-group">
              <label>Password Secundario SOL<span class="label-advertencia">*</span></label>
              <input type="password" name="Txt_Password_Sunat_Sol" placeholder="Ingresar dirección" class="form-control required pwd" autocomplete="off" value="moddatos">
              <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-6 col-sm-3">
            <label>Archivo CDT <span class="label-advertencia">*</span></label>
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-cloud-upload" aria-hidden="true"></i></span>
                <label class="btn btn-default" for="file-certificado_digital">
                  <input type="file" id="file-certificado_digital" name="certificado_digital" multiple=false accept=".pfx" required style="display:none" onchange="$('#upload-file-certificado_digital').html(this.files[0].name)">Buscar...
                </label>
                <span class='label label-info' id="upload-file-certificado_digital"></span>
              </div>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-6 col-sm-3">
            <div class="form-group">
              <label>Contraseña CDT <span class="label-advertencia">*</span></label>
              <input type="password" name="Txt_Password_Firma_Digital" placeholder="Ingresar dirección" class="form-control required pwd" autocomplete="off" value="123456">
              <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <?php if($this->empresa->Nu_Tipo_Sistema == 0 && $this->empresa->Nu_Tipo_Proveedor_FE == 2) { ?>
          <div class="col-xs-12 col-sm-6">
            <div class="form-group">
              <a href="https://www.youtube.com/watch?v=yEUcHSW95wY" target="_blank" style="color: #484848; font-weight: bold;" title="Video para crear usuario secundario" alt="Video para crear usuario secundario"><i class="fa fa-youtube-play red" aria-hidden="true" title="Video para crear usuario secundario" alt="Video para crear usuario secundario"></i> Crear Usuario Secundario</a>
            </div>
          </div>
          
          <div class="col-xs-12 col-sm-6">
            <div class="form-group">
              <a href="https://www.youtube.com/watch?v=8hByHdhIh1w" target="_blank" style="color: #484848; font-weight: bold;" title="Video para crear certificado digital" alt="Video para crear certificado digital"><i class="fa fa-youtube-play red" aria-hidden="true" title="Video para crear certificado digital" alt="Video para crear certificado digital"></i> Crear Certificado Digital</a>
            </div>
          </div>
          <?php } ?>
        </div>
      
        <div class="row div-guia_sunat hidden">
          <div class="col-xs-6 col-sm-6">
            <div class="form-group">
              <label>Guia Client ID<span class="label-advertencia">*</span></label>
              <input type="text" name="Txt_Sunat_Token_Guia_Client_ID" placeholder="Obligatorio" class="form-control required pwd" autocomplete="off" value="">
              <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-6 col-sm-6">
            <div class="form-group">
              <label>Guia Client Secret<span class="label-advertencia">*</span></label>
              <input type="password" name="Txt_Sunat_Token_Guia_Client_Secret" placeholder="Obligatorio" class="form-control required pwd" autocomplete="off" value="">
              <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>
      </div>
      
    	<div class="modal-footer">
          <div class="col-xs-6 col-md-6">
            <div class="form-group">
              <button type="button" class="btn btn-danger btn-lg btn-block" data-dismiss="modal"><span class="fa fa-sign-out"></span> Salir</button>
            </div>
          </div>
          <div class="col-xs-6 col-md-6">
            <div class="form-group">
              <button type="submit" id="btn-save" class="btn btn-success btn-lg btn-block btn-verificar"><i class="fa fa-save"></i> Guardar </button>
            </div>
          </div>
      </div>
    </div>
  </div>
  </div>
  <!-- /.Modal -->
  <?php echo form_close(); ?>

	<div class="modal fade" id="modal-laegestion_credenciales_usuario" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title text-center modal-header-title"><strong>Usuario - Sistema</strong></h4>
					<h5 class="modal-title text-center modal-header-title_empresa"><strong></strong></h5>
				</div>
				<div class="modal-body">
          <div class="row">
            <div class="col-xs-6 col-md-6">
              <label>Celular</label>
              <div class="form-group">
    	          <input type="text" id="txt-Nu_Celular_Usuario" inputmode="tel" name="Nu_Celular_Usuario" value="" class="form-control required">
              </div>
            </div>

            <div class="col-xs-12 col-md-12">
              <label>Mensaje</label>
              <div class="form-group">
                <textarea name="Mensaje_WhatsApp_Usuario" class="form-control required" rows="6" placeholder="Obligatorio" maxlength="250"></textarea>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-xs-12 col-md-12">
					    <p class="modal-p-body-laegestion_credenciales_usuario"></p>
            </div>
          </div>
				</div>
				<div class="modal-footer">
          <div class="col-xs-6 col-md-6">
					  <button type="button" class="btn btn-danger btn-block" data-dismiss="modal">Salir</button>
          </div>
          <div class="col-xs-6 col-md-6">
					  <button type="button" id="btn-whatsapp-laegestion_usuario" class="btn btn-success btn-block">Enviar WhatsApp</button>
          </div>
				</div>
			</div>			
		</div>
	</div>
</div>
<!-- /.content-wrapper -->

<div class="modal fade" id="modal-ver_progreso_global_cliente" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title text-center modal-header-ver_progreso_global_cliente"></h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-xs-12 col-md-12">
            <p class="modal-p-body-ver_progreso_global_cliente"></p>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <div class="col-xs-12 col-md-12">
          <button type="button" class="btn btn-danger btn-block" data-dismiss="modal">Salir</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal deposito_billetera -->
<div class="modal fade modal-deposito_billetera" id="modal-default">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="text-center">Agregar Deposito</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" id="hidden-modal_deposito-id_empresa" class="form-control" autocomplete="off">
        <div class="row">
          <div class="col-xs-12">
            <label>Importe</label>
            <div class="form-group">
              <input type="text" inputmode="decimal" id="txt-modal_deposito-importe_deposito" class="form-control required input-decimal" maxlength="13" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <div class="col-xs-6 col-md-6">
          <div class="form-group">
            <button type="button" id="btn-modal-salir" class="btn btn-danger btn-lg btn-block pull-center" data-dismiss="modal">Salir</button>
          </div>
        </div>
        <div class="col-xs-6 col-sm-6">
          <div class="form-group">
            <button type="button" id="btn-save_deposito_billetera" class="btn btn-success btn-lg btn-block pull-center">Guardar</button>
          </div>
        </div>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /. Modal deposito_billetera -->

<!-- Modal actualizar_saldo_billetera -->
<div class="modal fade modal-actualizar_saldo_billetera" id="modal-default">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="text-center">Agregar Deposito</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" id="hidden-modal_saldo-id_empresa" class="form-control" autocomplete="off">
        <div class="row">
          <div class="col-xs-12">
            <label>Importe</label>
            <div class="form-group">
              <input type="text" inputmode="decimal" id="txt-modal_saldo-importe_deposito" class="form-control required input-decimal" maxlength="13" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <div class="col-xs-6 col-md-6">
          <div class="form-group">
            <button type="button" id="btn-modal-salir" class="btn btn-danger btn-lg btn-block pull-center" data-dismiss="modal">Salir</button>
          </div>
        </div>
        <div class="col-xs-6 col-sm-6">
          <div class="form-group">
            <button type="button" id="btn-save_actualizar_saldo_billetera" class="btn btn-success btn-lg btn-block pull-center">Guardar</button>
          </div>
        </div>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /. Modal actualizar_saldo_billetera -->