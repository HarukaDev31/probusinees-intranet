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
            &nbsp;<a href="<?php echo $this->MenuModel->verificarAccesoMenuCRUD()->Txt_Url_Video; ?>" target="_blank"><i class="fa fa-youtube-play red" aria-hidden="true" title="Video <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?>" alt="Video <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?>"></i></a>
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
              <div class="col-sm-5 col-md-3">
                <div class="form-group">
    		  				<select id="cbo-Filtros_Empleados" name="Filtros_Empleados" class="form-control">
    		  				  <option value="Empleado">Nombre</option>
    		  				  <option value="DNI">Número Documento Identidad</option>
    		  				</select>
                </div>
              </div>
              
              <div class="col-sm-7 col-md-6">
                <div class="form-group">
                  <input type="text" id="txt-Global_Filter" name="Global_Filter" class="form-control" maxlength="100" placeholder="Buscar" value="" autocomplete="off">
                </div>
              </div>
              
              <div class="col-sm-12 col-md-3">
                <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Agregar == 1) : ?>
                <button type="button" class="btn btn-success btn-block" onclick="agregarEmpleado()"><i class="fa fa-plus-circle"></i> Agregar</button>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="table-responsive">
            <table id="table-Empleado" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th>Tipo</th>
                  <th>Número</th>
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
  <!-- Modal -->
  <?php
  $attributes = array('id' => 'form-Empleado');
  echo form_open('', $attributes);
  ?>
  <div class="modal fade" id="modal-Empleado" role="dialog">
  <div class="modal-dialog">
  	<div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title text-center"></h4>
      </div>
    	<div class="modal-body">
    	  
    	  <input type="hidden" name="EID_Empresa" class="form-control required">
    	  <input type="hidden" name="EID_Entidad" class="form-control required">
    	  <input type="hidden" name="ENu_Documento_Identidad" class="form-control required">
    	  <input type="hidden" name="ENu_Pin_Caja" class="form-control required">
    	  
			  <div class="row">
          <div class="col-xs-12 col-sm-4 col-md-3">
            <label>Doc. Identidad <span class="label-advertencia">*</span></label>
            <div class="form-group">
              <input type="text" id="txt-Nu_Documento_Identidad" name="Nu_Documento_Identidad" class="form-control required input-codigo_barra" placeholder="Ingresar número" autocomplete="off" maxlength="16">
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-12 col-sm-2 col-md-2 text-center">
            <label>Api</label>
            <div class="form-group">
              <button type="button" id="btn-cloud-api_empleado" class="btn btn-success btn-block btn-md"><i class="fa fa-cloud-download fa-lg"></i></button>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-12 col-sm-6 col-md-5">
            <label>Nombre(s) y Apellidos <span class="label-advertencia">*</span></label>
            <div class="form-group">
              <input type="text" name="No_Entidad" placeholder="Ingresar Nombre" maxlength="100" class="form-control required" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-5 col-sm-3 col-md-2 hidden">
            <div class="form-group">
              <label data-toggle="tooltip" data-placement="bottom" title="Será tu clave para aperturar caja en la opción Punto de Venta">PIN</label>
              <input type="tel" name="Nu_Pin_Caja" class="form-control input-number" maxlength="4" autocomplete="off" data-toggle="tooltip" data-placement="bottom" title="Será tu clave para aperturar caja en la opción Punto de Venta">
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-12 col-sm-3 col-md-2 div-Estado">
            <div class="form-group">
              <label>Estado <span class="label-advertencia">*</span></label>
		  				<select id="cbo-Estado" name="Nu_Estado" class="form-control required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-5 col-sm-3 col-md-3" style="display: none">
            <div class="form-group">
              <label>F. Nacimiento</label>
              <input type="text" name="Fe_Nacimiento" class="form-control date-picker-employee" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-7 col-sm-3 col-md-3" style="display: none">
            <div class="form-group">
              <label>Sexo</label>
              <select id="cbo-Sexos" name="Nu_Tipo_Sexo" class="form-control select2 required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-5 col-sm-3 col-md-3">
            <div class="form-group">
              <label>Celular</label>
              <input type="tel" name="Nu_Celular_Entidad" class="form-control" data-inputmask="'mask': ['999 999 999']" data-mask autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-7 col-sm-4 col-md-4" style="display: none">
            <div class="form-group">
              <label>Distrito</label>
		  				<select id="cbo-Distritos" name="ID_Distrito" class="form-control select2 required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-12 col-sm-8 col-md-9">
            <label>Dirección</label>
            <div class="form-group">
              <input type="text" name="Txt_Direccion_Entidad" class="form-control" autocomplete="off" />
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>
      </div>
      
    	<div class="modal-footer">
			  <div class="row">
          <div class="col-xs-6 col-md-6">
            <div class="form-group">
              <button type="button" class="btn btn-danger btn-md btn-block" data-dismiss="modal"><span class="fa fa-sign-out"></span> Salir (ESC)</button>
            </div>
          </div>
          <div class="col-xs-6 col-md-6">
            <div class="form-group">
              <button type="submit" id="btn-save" class="btn btn-success btn-md btn-block btn-verificar"><i class="fa fa-save"></i> Guardar (ENTER)</button>
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