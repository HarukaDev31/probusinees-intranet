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
      <!-- ./New box-header -->
    </div>
    
    <div class="row">
      <div class="col-xs-12">
        <div class="box box-content">
          <!-- box-header -->
          <div class="box-header box-header-new">
            <div class="row div-Filtros">
              <br>
              
              <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="alert alert-info">
                  <strong>Nota:</strong> Esta opción se configura para alimentar la información que usuará el módulo de <b>Punto de Venta</b>.
                </div>
              </div>

              <?php
              if ( $this->user->No_Usuario == 'root' ){ ?>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Empresa</label>
                  <select id="cbo-filtro_empresa" name="ID_Empresa" class="form-control select2" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Organización</label>
                  <select id="cbo-filtro_organizacion" name="ID_Organizacion" class="form-control select2" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              <?php } else { ?>
                <input type="hidden" id="cbo-filtro_empresa" name="ID_Empresa" class="form-control" value="<?php echo $this->user->ID_Empresa; ?>">
                <input type="hidden" id="cbo-filtro_organizacion" name="ID_Organizacion" class="form-control" value="<?php echo $this->user->ID_Organizacion; ?>">
              <?php } ?>

              <div class="col-md-3">
                <div class="form-group">
                  <label>Almacén</label>
                  <select id="cbo-filtro_almacen" name="ID_Almacen" class="form-control select2" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-md-3">
                <div class="form-group">
                  <label>Filtro</label>
    		  				<select id="cbo-Filtros_TipoOperacionCaja" name="Filtros_TipoOperacionCaja" class="form-control">
    		  				  <option value="TipoOperacionCaja">Nombre Tipo Operacion Caja</option>
    		  				</select>
                </div>
              </div>
              
              <div class="col-md-3">
                <div class="form-group">
                  <label>Nombre</label>
                  <input type="text" id="txt-Global_Filter" name="Global_Filter" class="form-control" placeholder="Buscar" value="" autocomplete="off" maxlength="30">
                </div>
              </div>
              
              <div class="col-md-3">
                <label>&nbsp;</label>
                <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Agregar == 1) : ?>
                <button type="button" class="btn btn-success btn-block" onclick="agregarTipoOperacionCaja()"><i class="fa fa-plus-circle"></i> Agregar</button>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="table-responsive">
            <table id="table-TipoOperacionCaja" class="table table-striped table-bordered">
              <thead>
              <tr>
                <?php if ( $this->user->No_Usuario == 'root' ){ ?>
                <th>Empresa</th>
                <th>Organizacion</th>
                <?php } ?>
                <th>Almacen</th>
                <th>Grupo</th>
                <th>Nombre</th>
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
  <?php
  $attributes = array('id' => 'form-TipoOperacionCaja');
  echo form_open('', $attributes);
  ?>
  <div class="modal fade" id="modal-TipoOperacionCaja" role="dialog">
  <div class="modal-dialog">
  	<div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title text-center"></h4>
      </div>
      
    	<div class="modal-body">
        <input type="hidden" name="EID_Empresa" class="form-control">
        <input type="hidden" name="EID_Organizacion" class="form-control">
        <input type="hidden" name="EID_Almacen" class="form-control">
    	  <input type="hidden" name="EID_Tipo_Operacion_Caja" class="form-control">
    	  <input type="hidden" name="ENo_Tipo_Operacion_Caja" class="form-control">
    	  
			  <div class="row">
          <?php
          if ( $this->user->No_Usuario == 'root' ){ ?>
          <div class="col-xs-12 col-md-6">
            <div class="form-group">
              <label>Empresa <span class="label-advertencia">*</span></label>
              <select id="cbo-Empresas" name="ID_Empresa" class="form-control select2 required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          <div class="col-xs-12 col-md-6">
            <div class="form-group">
              <label>Organizacion <span class="label-advertencia">*</span></label>
              <select id="cbo-organizacion" name="ID_Organizacion" class="form-control select2 required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          <?php } else { ?>
    	      <input type="hidden" id="cbo-Empresas" name="ID_Empresa" class="form-control" value="<?php echo $this->user->ID_Empresa; ?>">
            <input type="hidden" id="cbo-organizacion" name="ID_Organizacion" class="form-control" value="<?php echo $this->user->ID_Organizacion; ?>">
          <?php } ?>
          
          <div class="col-xs-12 col-md-4">
            <div class="form-group">
              <label>Almacen <span class="label-advertencia">*</span></label>
              <select id="cbo-almacen-add" name="ID_Almacen" class="form-control select2 required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-4 col-md-4">
            <div class="form-group">
              <label>Grupo <span class="label-advertencia">*</span></label>
              <select id="cbo-grupo_tipo_operacion_caja" name="Nu_Tipo" class="form-control required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-4 col-md-4">
            <div class="form-group">
              <label>Nombre <span class="label-advertencia">*</span></label>
              <input type="text" id="txt-No_Tipo_Operacion_Caja" name="No_Tipo_Operacion_Caja" placeholder="Ingresar nombre" class="form-control required" autocomplete="off" maxlength="30">
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-12 col-md-3 div-Estado">
            <div class="form-group">
              <label>Estado <span class="label-advertencia">*</span></label>
		  				<select id="cbo-Estado" name="Nu_Estado" class="form-control required" style="width: 100%;"></select>
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
  <?php echo form_close(); ?>
  <!-- /.Modal -->
</div>
<!-- /.content-wrapper -->