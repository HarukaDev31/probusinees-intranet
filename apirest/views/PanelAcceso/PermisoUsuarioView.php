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
          </h3>
        </div>
      </div>
    </div>
    <!-- ./New box-header -->
    
    <div class="row">
      <div class="col-xs-12">
        <div class="box box-content">
          <!-- box-header -->
          <div class="box-header text-center">
            <h3 class="box-title">Menú del Sistema</h3>
          </div>
          
          <div class="box-header box-header-new div-Listar">
            <div class="row div-Filtros">
              <br>
              <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="alert alert-success">
                  <strong>Nota:</strong> Solo los usuarios con <b>Cargo = Gerencia</b>. Tendrán acceso al módulo <b>Configuración</b>.
                </div>
              </div>

              <?php
              if ( $this->user->No_Usuario == 'root' ){ ?>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Empresa <span class="label-advertencia">*</span></label>
                    <select id="cbo-Empresas" name="ID_Empresa" class="form-control select2 required" style="width: 100%;"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>

                <div class="col-md-6">
                  <label>Organizacion</label>
                  <div class="form-group">
                    <select id="cbo-organizacion" class="form-control select2" style="width: 100%;"></select>
                  </div>
                </div>
              <?php } else { ?>
                <input type="hidden" id="cbo-Empresas" name="ID_Empresa" class="form-control" value="<?php echo $this->user->ID_Empresa; ?>">
                <input type="hidden" id="cbo-organizacion" name="ID_Organizacion" class="form-control" value="<?php echo $this->user->ID_Organizacion; ?>">
              <?php } ?>

              <div class="col-md-12">
                <label>Cargo</label>
                <div class="form-group">
                  <select id="cbo-Grupos" class="form-control select2" style="width: 100%;"></select>
        				</div>
        			</div>
        		</div>
          </div>
          <!-- /.box-header -->
          <div class="table-responsive div-table-Permiso_Usuario tableFixHead">
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