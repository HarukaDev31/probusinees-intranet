<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">  
  <?php
  //array_debug($this->empresa);
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
            &nbsp;
            <a href="<?php echo $this->MenuModel->verificarAccesoMenuCRUD()->Txt_Url_Video; ?>" target="_blank" rel="noopener noreferrer" title="Ver video tutorial de <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?>" alt="Ver video tutorial de <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?>">
              <span style="font-size: 12px; background-color: #FF0000 !important; padding: 10px; border-radius: 50px;">
                <i class="fa fa-youtube-play red" style="color:  #FFF !important" aria-hidden="true"></i>
                &nbsp;&nbsp;<span style="color: #FFF; font-size: 14px;">Video Turorial<span>
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
            <div class="row div-Filtros hidden">
              <br>              
              <div class="col-xs-12 col-md-12">
                <label>&nbsp;</label>
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
                  <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Editar == 1) : ?>
                    <th class="no-sort">Editar</th>
                  <?php endif; ?>
                  <th>Sistema</th>
                  <th>Tipo</th>
                  <th>Número</th>
                  <th>Denominación</th>
                  <th>Nombre Comercial</th>
                  <th class="no-sort_left">Dirección</th>
                  <!--<th class="no-sort">Multi Logo</th>-->
                  <th class="no-sort">Estado</th>
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
  $attributes = array('id' => 'form-Empresa', 'enctype' => 'multipart/form-data');
  echo form_open('', $attributes);
  ?>
  <div class="modal fade" id="modal-Empresa" role="dialog">
  <div class="modal-dialog modal-lg">
  	<div class="modal-content">      
    	<div class="modal-body">
    	  <input type="hidden" id="txt-EID_Empresa" name="EID_Empresa" value="">
    	  <input type="hidden" id="txt-ENu_Documento_Identidad" name="ENu_Documento_Identidad" value="">
    	  <input type="hidden" id="txt-ETxt_Direccion_Empresa" name="ETxt_Direccion_Empresa" value="">
  
        <div class="row">
          <div class="col-xs-12 col-md-12">
            <h4 class="modal-title text-center"></h4><br>
          </div>

          <?php if ( $this->user->No_Usuario == 'root' ){ ?>
          <div class="col-xs-12 col-md-2">
            <div class="form-group">
              <label>Tipo de Sistema <span class="label-advertencia">*</span></label>
              <select id="cbo-tipo_proveedor_fe" name="Nu_Tipo_Proveedor_FE" class="form-control required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          <?php } else { ?>
            <input type="hidden" id="cbo-tipo_proveedor_fe" name="Nu_Tipo_Proveedor_FE" class="form-control" value="<?php echo $this->empresa->Nu_Tipo_Proveedor_FE; ?>">
          <?php } ?>

          <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
            <div class="form-group">
              <label>T.D.I </label>
              <span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Tipo de documento identidad: RUC / DNI / OTROS">
                <i class="fa fa-info-circle"></i>
              </span>
              <select id="cbo-TiposDocumentoIdentidad" name="ID_Tipo_Documento_Identidad" class="form-control required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-5 col-sm-4 col-md-2 col-lg-2">
            <div class="form-group">
              <label id="label-Nombre_Documento_Identidad">RUC <span class="label-advertencia">*</span></label>
              <input type="tel" id="txt-Nu_Documento_Identidad" name="Nu_Documento_Identidad" class="form-control required input-number" maxlength="11" placeholder="Ingresar número" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-3 col-sm-2 col-md-2 col-lg-1 text-center">
            <div class="form-group">
              <label>Api</label>
              <button type="button" id="btn-cloud-api_empresa" class="btn btn-success btn-block btn-md"><i class="fa fa-cloud-download fa-lg"></i></button>
            </div>
          </div>

          <?php if ( $this->user->No_Usuario == 'root' ){ ?>
          <div class="col-xs-3 col-sm-2 col-md-2">
            <div class="form-group">
              <label title="Activar guía electrónica">Guía</label>
              <span data-toggle="tooltip" data-trigger="hover" data-placement="right" title="¿Activar Guía Electrónica?">
                <i class="fa fa-info-circle"></i>
              </span>
              <select id="cbo-Activar_Guia_Electronica" name="Nu_Activar_Guia_Electronica" class="form-control required"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          <?php } else { ?>
            <input type="hidden" id="cbo-Activar_Guia_Electronica" name="Nu_Activar_Guia_Electronica" class="form-control" value="<?php echo $this->empresa->Nu_Activar_Guia_Electronica; ?>">
          <?php } ?>

          <div class="col-xs-5 col-md-2 hidden">
            <div class="form-group">
              <label>¿Ecommerce? <span class="label-advertencia">*</span></label>
  	  				<select id="cbo-tipo_ecommerce_empresa" name="Nu_Tipo_Ecommerce_Empresa" class="form-control required"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-12 col-sm-3 col-md-2"  <?php echo $sCssDisplayRoot; ?>>
            <div class="form-group">
              <label>Estado <span class="label-advertencia">*</span></label>
              <span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Si el cliente sale INACTIVO cambiar ACTIVO">
                <i class="fa fa-info-circle"></i>
              </span>
  	  				<select id="cbo-Estado" name="Nu_Estado" class="form-control required"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-6 col-sm-7 col-md-3 col-lg-4">
            <label id="label-No_Entidad">Razón Social <span class="label-advertencia"> *</span></label>
            <div class="form-group">
              <input type="text" name="No_Empresa" class="form-control required" placeholder="Ingresar nombre" maxlength="100" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-6 col-sm-5 col-md-3 col-lg-3">
            <label>Nombre Comercial</label>
            <div class="form-group">
              <input type="text" name="No_Empresa_Comercial" class="form-control" placeholder="Opcional" maxlength="100" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-12 col-sm-7 col-md-6 col-lg-6">
            <label>Domicilio Fiscal <span class="label-advertencia">*</span></label>
            <div class="form-group">
              <input type="text" name="Txt_Direccion_Empresa" placeholder="Ingresar dirección" class="form-control required" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-6 col-md-2 hidden">
            <div class="form-group">
              <label>Multi Logo</label>
              <span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Se activa, si se desea manejar diferentes logo por cada sucursal / almacén.">
                <i class="fa fa-info-circle"></i>
              </span>
  	  				<select id="cbo-multi_almacen" name="Nu_MultiAlmacen" class="form-control required"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-6 col-sm-5 col-md-6 col-lg-6 div-row-nubefact">
            <div class="form-group">
              <label>Ubigeo INEI <span class="label-advertencia">*</span></label>
		  				<select id="cbo-ubigeo_inei" name="ID_Ubigeo_Inei" title="Se encuentra en la ficha RUC" class="form-control select2" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
            <div class="form-group">
              <label>Departamento <span class="label-advertencia">*</span></label>
		  				<select id="cbo-Departamentos" name="ID_Departamento" class="form-control select2" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
            <div class="form-group">
              <label>Provincia <span class="label-advertencia">*</span></label>
		  				<select id="cbo-Provincias" name="ID_Provincia" class="form-control select2" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
            <div class="form-group">
              <label>Distrito <span class="label-advertencia">*</span></label>
		  				<select id="cbo-Distritos" name="ID_Distrito" class="form-control select2" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>
          
        <div class="row div-row-empresas-marketplace">
          <div class="col-xs-12 col-md-12">
            <div class="form-group">
              <label>Empresas Marketplace <span class="label-advertencia">*</span></label>
              <select id="cbo-empresa-marketplace" name="ID_Empresa_Marketplace" class="form-control required"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>
        
  		  <div class="row">
        </div>
        
        <?php if($this->empresa->Nu_Estado_Sistema == 0 && $this->empresa->Nu_Tipo_Proveedor_FE == 1) { ?>
  		  <div class="row div-nubefact_reseller">
          <div class="col-xs-12 col-md-12">
            <div class="form-group">
              <a href="https://drive.google.com/file/d/1v1ujS7YSygYLfR4SxIBRwMK5fnfvpTIR/view?usp=sharing" target="_blank" style="font-weight: bold; display: block; width: 100%;" title="RECORDAR: Alta de SUNAT es al día siguiente o mayor a la fecha de HOY" alt="RECORDAR: Alta de SUNAT es al día siguiente o mayor a la fecha de HOY">Dar de alta SUNAT PSE <label style="color: #484848; font-weight: normal;">(RECORDAR: El alta en SUNAT es al día siguiente o mayor a la fecha de HOY)</label></a>
            </div>
          </div>
        </div>
        <?php } ?>

        <div class="row">
          <div class="col-xs-6 col-md-3 hidden">
            <div class="form-group">
              <label>País <span class="label-advertencia">*</span></label>
		  				<select id="cbo-Paises" name="ID_Pais" class="form-control select2" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>

  		  <div class="row div-row-nubefact">
          <div class="col-xs-12 col-sm-12 col-md-12">
		        <div class="well well-sm text-center">Conexión con <strong>SUNAT</strong></div>
          </div>
          
          <div class="col-xs-6 col-sm-3">
            <label>Archivo Certificado <span class="label-advertencia">*</span></label>
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-cloud-upload" aria-hidden="true"></i></span>
                <label class="btn btn-default" for="file-certificado_digital">
                  <input type="file" id="file-certificado_digital" name="certificado_digital" multiple=false accept=".pfx" required style="display:none" onchange="$('#upload-file-certificado_digital').html(this.files[0].name)">Buscar...
                </label>
                <?php if($this->empresa->Nu_Estado_Sistema == 1 && $this->empresa->Nu_Tipo_Proveedor_FE == 2) { ?>
                  <a href="<?php echo '../../../librerias/download.php/sunat_facturador/certificado_digital/FIRMA/' . $this->empresa->Nu_Documento_Identidad . '.pfx'; ?>" download="Certificado_Digital_SUNAT">Descargar</a>
                  <!--<a href="<?php echo '../../../librerias.laesystems.com/download.php/sunat_facturador/certificado_digital/FIRMA/' . $this->empresa->Nu_Documento_Identidad . '.pfx'; ?>" download="Certificado_Digital_SUNAT">Descargar</a>--><!-- localhost-->
                <?php } ?>
                <span class='label label-info' id="upload-file-certificado_digital"></span>
              </div>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-6 col-sm-3">
            <div class="form-group">
              <label>Clave Certificado <span class="label-advertencia">*</span></label>
              <input type="password" name="Txt_Password_Firma_Digital" placeholder="Ingresar dirección" class="form-control required pwd" autocomplete="off" value="123456">
              <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-12 col-sm-3">
            <div class="form-group">
              <label>Usuario Secundario<span class="label-advertencia">*</span></label>
              <input type="text" name="Txt_Usuario_Sunat_Sol" placeholder="Ingresar dirección" class="form-control required input-Mayuscula" autocomplete="off" value="MODDATOS">
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-12 col-sm-3">
            <div class="form-group">
              <label>Contraseña Secundario<span class="label-advertencia">*</span></label>
              <input type="password" name="Txt_Password_Sunat_Sol" placeholder="Ingresar dirección" class="form-control required pwd" autocomplete="off" value="moddatos">
              <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <?php if($this->empresa->Nu_Tipo_Proveedor_FE == 2 && ($this->empresa->Txt_Usuario_Sunat_Sol=='MODDATOS' || $this->empresa->Txt_Password_Sunat_Sol=='moddatos' || $this->empresa->Txt_Password_Firma_Digital=='123456')) { ?>
          <div class="col-xs-12 col-sm-6">
            <div class="form-group">
              <a href="https://www.youtube.com/watch?v=8hByHdhIh1w" target="_blank" style="color: #484848; font-weight: bold;" title="Video para crear certificado digital" alt="Video para crear certificado digital"><i class="fa fa-youtube-play red" aria-hidden="true" title="Video para crear certificado digital" alt="Video para crear certificado digital"></i> Video Certificado Digital</a>
            </div>
          </div>

          <div class="col-xs-12 col-sm-6">
            <div class="form-group">
              <a href="https://youtu.be/wUOcQjpojHw" target="_blank" style="color: #484848; font-weight: bold;" title="Video para crear usuario secundario" alt="Video para crear usuario secundario"><i class="fa fa-youtube-play red" aria-hidden="true" title="Video para crear usuario secundario" alt="Video para crear usuario secundario"></i> Video Usuario Secundario</a>
            </div>
          </div>
          <?php } ?>
        </div>
      </div>
      
    	<div class="modal-footer">
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
  <!-- /.Modal -->
  <?php echo form_close(); ?>
</div>
<!-- /.content-wrapper -->