<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
<?php
$sCssDisplayRoot='style="display:none"';
if ( $this->user->ID_Usuario == 1 ){
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
          </h3>
        </div>
      </div>
    </div>
    <!-- ./New box-header -->
    
    <div class="row">
      <div class="col-xs-12">
        <div class="box box-content">
          <!-- box-header -->
          <div class="box-header box-header-new">
            <div class="row div-Filtros">
              <br class="hidden-xs hidden-sm">
              <?php
              if ( $this->user->ID_Usuario == 1 ){ ?>
              <div class="col-md-6">
                <label>Empresa</label>
                <div class="form-group">
                  <select id="cbo-filtro_empresa" class="form-control select2" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              <div class="col-md-6">
                <label>Organización</label>
                <div class="form-group">
                  <select id="cbo-filtro_organizacion" class="form-control select2" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              <?php } else { ?>
                <input type="hidden" id="cbo-filtro_empresa" class="form-control" value="<?php echo $this->user->ID_Empresa; ?>">
                <input type="hidden" id="cbo-filtro_organizacion" class="form-control" value="<?php echo $this->user->ID_Organizacion; ?>">
              <?php } ?>

              <div class="col-md-3">
                <label class="hidden-xs hidden-sm">&nbsp;</label>
                <div class="form-group">
                  <select id="cbo-Filtros_Usuario" name="Filtros_Usuario" class="form-control">
                    <option value="Usuario">Usuario</option>
                  </select>
                </div>
              </div>
              
              <div class="col-md-6">
                <label class="hidden-xs hidden-sm">&nbsp;</label>
                <div class="form-group">
                  <input type="text" id="txt-Global_Filter" name="Global_Filter" class="form-control" maxlength="100" placeholder="Buscar" value="<?php echo $sUsuario; ?>">
                </div>
              </div>
              
              <div class="col-md-3">
                <label class="hidden-xs hidden-sm">&nbsp;</label>
                <div class="form-group">
                  <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Agregar == 1) : ?>
                    <button type="button" class="btn btn-success btn-block" onclick="agregarUsuario()"><i class="fa fa-plus-circle"></i> Agregar</button>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="table-responsive">
            <table id="table-Usuario" class="table table-striped table-bordered">
              <thead>
              <tr>
                <?php if ( $this->user->ID_Usuario == 1 ){ ?>
                <th>Empresa</th>
                <th>Organizacion</th>
                <?php } ?>
                <th>Cargo</th>
                <th>Usuario</th>
                <th>Nombres y Apellidos</th>
                <th class="no-sort">Estado</th>
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
  <form id="form-Usuario" enctype="multipart/form-data" method="post" role="form" autocomplete="off">
  <div class="modal fade" id="modal-Usuario" role="dialog">
  <div class="modal-dialog">
  	<div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title text-center"></h4>
      </div>
    	<div class="modal-body">
        <input type="hidden" id="hidden-sCorreUsuarioLink" name="hidden-sCorreUsuarioLink" class="form-control" value="<?php echo $sUsuario; ?>">

        <input type="hidden" name="EID_Empresa" class="form-control">
    	  <input type="hidden" name="EID_Organizacion" class="form-control">
    	  <input type="hidden" name="EID_Grupo" class="form-control">
    	  <input type="hidden" name="EID_Usuario" class="form-control">
    	  <input type="hidden" name="ENo_Usuario" class="form-control">
    	  <input type="hidden" name="ENu_Celular" class="form-control">
    	  <input type="hidden" name="ETxt_Email" class="form-control">
        <input type="hidden" name="ENu_Estado" class="form-control">
    	  
			  <div class="row">
          <?php
          if ( $this->user->ID_Usuario == 1 ){ ?>
          <div class="col-md-12">
            <div class="form-group">
              <label>Empresa <span class="label-advertencia">*</span></label>
              <select id="cbo-Empresas" name="ID_Empresa" class="form-control select2 required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-12 col-sm-6 col-md-6">
            <div class="form-group">
              <label>Organizacion <span class="label-advertencia">*</span></label>
		  				<select id="cbo-organizacion" name="ID_Organizacion" class="form-control required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          <?php } else { ?>
    	      <input type="hidden" id="cbo-Empresas" name="ID_Empresa" class="form-control" value="<?php echo $this->user->ID_Empresa; ?>">
            <input type="hidden" id="cbo-organizacion" name="ID_Organizacion" class="form-control" value="<?php echo $this->user->ID_Organizacion; ?>">
          <?php } ?>

          <div class="col-xs-12 col-sm-6 col-md-6">
            <div class="form-group">
              <label>Grupo / Cargo <span class="label-advertencia">*</span></label>
		  				<select id="cbo-Grupos" name="ID_Grupo" class="form-control required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>
          
			  <div class="row">
          <div class="col-xs-12 col-sm-6 col-md-6">
            <div class="form-group">
              <label>Email Usuario <span class="label-advertencia">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="blue fa fa-user" aria-hidden="true"></i></span>
                <input type="text" id="txt-No_Usuario" name="No_Usuario" inputmode="email" placeholder="Ingresar email" class="form-control required" autocomplete="off" maxlength="100">
              </div>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-12 col-sm-6 col-md-6">
            <div class="form-group">
              <label>Nombre(s) y Apellidos</label>
              <div class="input-group">
                <span class="input-group-addon"><i class="blue fa fa-user" aria-hidden="true"></i></span>
                <input type="text" name="No_Nombres_Apellidos" placeholder="Ingresar nombre(s) y apellidos" class="form-control" autocomplete="off" maxlength="100">
              </div>
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>
        
        <div class="row">
          <div class="col-xs-12 col-sm-6 col-md-6">
            <div class="form-group">
              <label>Contraseña <span class="label-advertencia">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="blue fa fa-lock" aria-hidden="true"></i></span>
                <input type="password" id="No_Password" name="No_Password" placeholder="Ingresar contraseña" class="form-control required pwd" autocomplete="off">
                <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
              </div>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-12 col-sm-6 col-md-6">
            <div class="form-group">
              <label>Repetir Contraseña <span class="label-advertencia">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="blue fa fa-lock" aria-hidden="true"></i></span>
                <input type="password" name="RNo_Password" placeholder="Repetir contraseña" class="form-control required pwd" autocomplete="off">
                <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
              </div>
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>
        
        <div class="row">
          <div class="col-xs-6 col-sm-6 col-md-6">
            <div class="form-group">
              <label>Celular</label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-phone" aria-hidden="true"></i></span>
                <input type="text" name="Nu_Celular" inputmode="tel" class="form-control" style="width: 100%;" data-inputmask="'mask': ['999 999 999']" data-mask autocomplete="off">
              </div>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-6 col-sm-6 col-md-6" <?php echo $sCssDisplayRoot; ?>>
            <div class="form-group">
              <label>Estado <span class="label-advertencia">*</span></label>
		  				<select id="cbo-Estado" name="Nu_Estado" class="form-control required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-12 col-sm-8 col-md-8" style="display:none">
            <div class="form-group">
              <label>Correo <span class="label-advertencia">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="blue fa fa-envelope" aria-hidden="true"></i></span>
                <input type="text" name="Txt_Email" inputmode="email" placeholder="Ingresar correo" class="form-control" autocomplete="off">
              </div>
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>
	    </div>
    	<div class="modal-footer">
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
  </form>
  <!-- /.Modal -->
</div>
<!-- /.content-wrapper -->