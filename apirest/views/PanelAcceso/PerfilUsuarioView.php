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
            <button type="button" class="btn btn-primary btn-block" onclick="agregarPerfilUsuario()"><i class="fa fa-plus-circle"></i> Agregar</button>
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
                if ( $this->user->No_Usuario == 'root' ){ ?>
                <div class="col-md-6">
                  <label>Empresa</label>
                  <div class="form-group">
                    <select id="cbo-filtro_empresa" name="ID_Empresa" class="form-control select2" style="width: 100%;"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
                <div class="col-md-6">
                  <label>Organización</label>
                  <div class="form-group">
                    <select id="cbo-filtro_organizacion" name="ID_Organizacion" class="form-control select2" style="width: 100%;"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
                <?php } else { ?>
                  <input type="hidden" id="cbo-filtro_empresa" name="ID_Empresa" class="form-control" value="<?php echo $this->user->ID_Empresa; ?>">
                  <input type="hidden" id="cbo-filtro_organizacion" name="ID_Organizacion" class="form-control" value="<?php echo $this->user->ID_Organizacion; ?>">
                <?php } ?>

                <div class="col-md-3 d-none">
                  <div class="form-group">
                    <label></label>
                    <select id="cbo-Filtros_Perfil_Usuario" name="Filtros_Perfil_Usuario" class="form-control">
                      <option value="Perfil_Usuario">Nombre Cargo</option>
                    </select>
                  </div>
                </div>
                
                <div class="col-md-6 d-none">
                  <label class="hidden-xs hidden-sm">&nbsp;</label>
                  <div class="form-group">
                    <input type="text" id="txt-Global_Filter" name="Global_Filter" class="form-control" maxlength="11" placeholder="Buscar" value="">
                  </div>
                </div>
                
                <div class="col-md-3 d-none">
                  <label class="hidden-xs hidden-sm">&nbsp;</label>
                  <div class="form-group">
                    <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Agregar == 1) : ?>
                      <!--<button type="button" class="btn btn-success btn-block" onclick="agregarPerfilUsuario()"><i class="fa fa-plus-circle"></i> Agregar</button>-->
                    <?php endif; ?>
                    </div>
                </div>
              </div>
              <div class="table-responsive">
                <table id="table-Perfil_Usuario" class="table table-bordered table-hover table-striped">
                  <thead class="thead-light">
                    <tr>
                      <?php if ( $this->user->No_Usuario == 'root' ){ ?>
                      <th>Empresa</th>
                      <th>Organizacion</th>
                      <?php } ?>
                      <th>Privilegio</th>
                      <th>Cargo</th>
                      <th>Descripción</th>
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
<form id="form-Perfil_Usuario" enctype="multipart/form-data" method="post" role="form" autocomplete="off">
  <div class="modal fade" id="modal-Perfil_Usuario" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title text-center"></h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="EID_Empresa" class="form-control">
          <input type="hidden" name="EID_Organizacion" class="form-control">
          <input type="hidden" name="EID_Grupo" class="form-control">
          <input type="hidden" name="ENo_Grupo" class="form-control">
          
          <div class="row">
            <?php
            if ( $this->user->No_Usuario == 'root' ){ ?>
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
              <input type="hidden" id="hidden-id_empresa" name="ID_Empresa" class="form-control" value="<?php echo $this->user->ID_Empresa; ?>">
              <input type="hidden" id="hidden-id_organizacion" name="ID_Organizacion" class="form-control" value="<?php echo $this->user->ID_Organizacion; ?>">
            <?php } ?>
            
            <div class="col-12 col-sm-12 col-md-12">
              <div class="form-group">
                <label>Privilegio <span class="label-advertencia text-danger">*</span></label>
                <select id="cbo-privilegio" name="Nu_Tipo_Privilegio_Acceso" class="form-control required" style="width: 100%;"></select>
                <span class="help-block text-danger" id="error"></span>
              </div>
            </div>

            <div class="col-12 col-sm-6 col-md-4">
              <div class="form-group">
                <label>Cargo <span class="label-advertencia text-danger">*</span></label>
                <input type="text" id="txt-No_Grupo" name="No_Grupo" placeholder="" class="form-control required" maxlength="30" autocomplete="off">
                <span class="help-block text-danger" id="error"></span>
              </div>
            </div>
            
            <div class="col-12 col-sm-8 col-md-5">
              <div class="form-group">
                <label>Descripción Cargo</label>
                <input type="text" name="No_Grupo_Descripcion" placeholder="Opcional" class="form-control" maxlength="100" autocomplete="off">
              </div>
            </div>
            
            <div class="col-12 col-sm-4 col-md-3">
              <div class="form-group">
                <label>Estado <span class="label-advertencia text-danger">*</span></label>
                <select id="cbo-Estado" name="Nu_Estado" class="form-control required" style="width: 100%;"></select>
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