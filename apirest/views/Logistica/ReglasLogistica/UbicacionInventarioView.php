<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header"></section>
  
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
              <div class="col-md-3">
                <div class="form-group">
    		  				<select id="cbo-Filtros_UnidadesMedida" name="Filtros_UnidadesMedida" class="form-control">
    		  				  <option value="UbicacionInventario">Nombre Ubicación Inventario</option>
    		  				  <option value="Almacen">Nombre Almacén</option>
    		  				</select>
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="form-group">
                  <input type="text" id="txt-Global_Filter" name="Global_Filter" class="form-control" maxlength="11" placeholder="Buscar" value="">
                </div>
              </div>
              
              <div class="col-md-3">
                <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Agregar == 1) : ?>
                <button type="button" class="btn btn-success btn-block" onclick="agregarUbicacionInventario()"><i class="fa fa-plus-circle"></i> Agregar</button>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="table-responsive">
            <table id="table-UbicacionInventario" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th>Almacén</th>
                  <th>Nombre</th>
                  <th class="no-sort">Estado</th>
                  <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Editar == 1) : ?>
                    <th class="no-sort"></th>
                  <?php endif; ?>
                  <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Eliminar == 1) : ?>
                    <th class="no-sort"></th>
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
  <?php
  $attributes = array('id' => 'form-UbicacionInventario');
  echo form_open('', $attributes);
  ?>
  <!-- Modal -->
  <div class="modal fade" id="modal-UbicacionInventario" role="dialog">
  <div class="modal-dialog">
  	<div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title text-center"></h4>
      </div>
      
    	<div class="modal-body">
    	  <input type="hidden" name="EID_Almacen" class="form-control required">
    	  <input type="hidden" name="EID_Ubicacion_Inventario" class="form-control required">
    	  <input type="hidden" name="ENo_Ubicacion_Inventario" class="form-control required">
        
			  <div class="row">
          <div class="col-md-5">
            <div class="form-group">
              <label>Almacén <span class="label-advertencia">*</span></label>
		  				<select id="cbo-Almacenes" name="ID_Almacen" class="form-control select2 required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-md-7">
            <div class="form-group">
              <label>Nombre <span class="label-advertencia">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
                <input type="text" id="txt-No_Ubicacion_Inventario" name="No_Ubicacion_Inventario" placeholder="Ingresar Nombre" class="form-control required" maxlength="100" autocomplete="off">
              </div>
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>

			  <div class="row">
          <div class="col-md-3 div-Estado">
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
  <!-- /.Modal -->
  <?php echo form_close(); ?>
</div>
<!-- /.content-wrapper -->