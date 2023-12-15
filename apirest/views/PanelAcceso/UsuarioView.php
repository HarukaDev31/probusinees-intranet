<?php
$sCssDisplayRoot='style="display:none"';
if ( $this->user->ID_Usuario == 1 ){
  $sCssDisplayRoot='';
}
?>
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-8">
          <h1><i class="<?php echo $this->MenuModel->verificarAccesoMenuCRUD()->Txt_Css_Icons; ?>" aria-hidden="true"></i> <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?></h1>
        </div>
        
        <div class="col-sm-4">
          <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Agregar == 1) : ?>
            <button type="button" class="btn btn-primary btn-block" onclick="agregarUsuario()"><i class="fa fa-plus-circle"></i> Agregar</button>
          <?php endif; ?>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
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

                <div class="col-md-3 d-none">
                  <label class="hidden-xs hidden-sm">&nbsp;</label>
                  <div class="form-group">
                    <select id="cbo-Filtros_Usuario" name="Filtros_Usuario" class="form-control">
                      <option value="Usuario">Usuario</option>
                    </select>
                  </div>
                </div>
                
                <div class="col-md-6 d-none">
                  <label class="hidden-xs hidden-sm">&nbsp;</label>
                  <div class="form-group">
                    <input type="text" id="txt-Global_Filter" name="Global_Filter" class="form-control" maxlength="100" placeholder="Buscar" value="<?php echo $sUsuario; ?>">
                  </div>
                </div>
              </div>
              <div class="table-responsive">
                <table id="table-Usuario" class="table table-bordered table-hover table-striped">
                  <thead class="thead-light">
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
            </div>
          </div>
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

<!-- Modal -->
<!--<form id="form-Usuario" enctype="multipart/form-data" method="post" role="form" autocomplete="off">-->
<?php
$attributes = array(
  'id' => 'form-Usuario',
  'enctype' => 'multipart/form-data',
  'autocomplete' => 'off',
);
echo form_open('', $attributes);
?>
  <div class="modal fade" id="modal-Usuario" role="dialog">
  <div class="modal-dialog">
  	<div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title text-center"></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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
            <label>Grupo / Cargo <span class="label-advertencia text-danger">*</span></label>
            <div class="form-group">
		  				<select id="cbo-Grupos" name="ID_Grupo" class="form-control required" style="width: 100%;"></select>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
        </div>
          
			  <div class="row">
          <div class="col-xs-12 col-sm-6 col-md-6">
            <label>Email <span class="label-advertencia text-danger">*</span></label>
            <div class="form-group">
              <input type="text" id="txt-No_Usuario" name="No_Usuario" inputmode="email" placeholder="Ingresar email" class="form-control required" autocomplete="off" maxlength="100">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-12 col-sm-6 col-md-6">
            <label>Nombre(s) y Apellidos</label>
            <div class="form-group">
              <input type="text" name="No_Nombres_Apellidos" placeholder="Ingresar nombre(s) y apellidos" class="form-control" autocomplete="off" maxlength="100">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
        </div>
        
        <div class="row">
          <div class="col-xs-12 col-sm-6 col-md-6">
            <label>Contraseña <span class="label-advertencia text-danger">*</span></label>
            <div class="form-group">
              <input type="password" id="No_Password" name="No_Password" placeholder="Ingresar contraseña" class="form-control required pwd" autocomplete="off">
              <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-12 col-sm-6 col-md-6">
            <label>Repetir Contraseña <span class="label-advertencia text-danger">*</span></label>
            <div class="form-group">
              <input type="password" name="RNo_Password" placeholder="Repetir contraseña" class="form-control required pwd" autocomplete="off">
              <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
        </div>
        
        <div class="row">
          <div class="col-xs-6 col-sm-6 col-md-6">
            <label>Celular</label>
            <div class="form-group">
              <input type="text" name="Nu_Celular" inputmode="tel" class="form-control" style="width: 100%;" data-inputmask="'mask': ['999 999 999']" data-mask autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-6 col-sm-6 col-md-6" <?php echo $sCssDisplayRoot; ?>>
            <label>Estado <span class="label-advertencia text-danger">*</span></label>
            <div class="form-group">
		  				<select id="cbo-Estado" name="Nu_Estado" class="form-control required" style="width: 100%;"></select>
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-12 col-sm-8 col-md-8" style="display:none">
            <label>Correo <span class="label-advertencia text-danger">*</span></label>
            <div class="form-group">
              <input type="text" name="Txt_Email" inputmode="email" placeholder="Ingresar correo" class="form-control" autocomplete="off">
              <span class="help-block text-danger" id="error"></span>
            </div>
          </div>
        </div>
	    </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="col btn btn-danger btn-md btn-block" data-dismiss="modal"><span class="fa fa-sign-out"></span> Salir</button>
        <button type="submit" id="btn-save" class="col btn btn-success btn-md btn-block btn-verificar"><i class="fa fa-save"></i> Guardar </button>
      </div>
    </div>
  </div>
  </div>
</form>
  <!-- /.Modal -->