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
              <div class="col-md-3">
                <div class="form-group">
    		  				<select id="cbo-Filtros_Distritos" name="Filtros_Distritos" class="form-control">
    		  				  <option value="Distrito">Nombre Distrito</option>
    		  				  <option value="Provincia">Nombre Provincia</option>
    		  				  <option value="Departamento">Nombre Departamento</option>
    		  				  <option value="Pais">Nombre País</option>
    		  				</select>
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="form-group">
                  <input type="text" id="txt-Global_Filter" name="Global_Filter" class="form-control" maxlength="64" placeholder="Buscar" value="" autocomplete="off">
                </div>
              </div>
              
              <div class="col-md-3">
                <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Agregar == 1) : ?>
                <button type="button" class="btn btn-success btn-block" onclick="agregarDistrito()"><i class="fa fa-plus-circle"></i> Agregar</button>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="table-responsive">
            <table id="table-Distrito" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th>País</th>
                  <th>Departamento</th>
                  <th>Provincia</th>
                  <th>Distrito</th>
                  <!--<th>Precio</th>-->
                  <!--<th>Ecommerce</th>-->
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
  $attributes = array('id' => 'form-Distrito');
  echo form_open('', $attributes);
  ?>
  <div class="modal fade" id="modal-Distrito" role="dialog">
  <div class="modal-dialog">
  	<div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title text-center"></h4>
      </div>
      
    	<div class="modal-body">
    	  <input type="hidden" name="EID_Provincia" class="form-control required">
    	  <input type="hidden" name="EID_Distrito" class="form-control required">
    	  <input type="hidden" name="ENo_Distrito" class="form-control required">
    	  
			  <div class="row">
          <div class="col-xs-12 col-sm-6 col-md-6">
            <div class="form-group">
              <label>País <span class="label-advertencia">*</span></label>
  	  				<select id="cbo-Paises" name="ID_Pais" class="form-control select2 required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-12 col-sm-6 col-md-6">
            <div class="form-group">
              <label>Departamento <span class="label-advertencia">*</span></label>
  	  				<select id="cbo-Departamentos" name="ID_Departamento" class="form-control select2 required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>

			  <div class="row">
          <div class="col-xs-12 col-sm-6 col-md-6">
            <div class="form-group">
              <label>Provincia <span class="label-advertencia">*</span></label>
  	  				<select id="cbo-Provincias" name="ID_Provincia" class="form-control select2 required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-12 col-sm-6 col-md-6">
            <label>Distrito <span class="label-advertencia">*</span></label>
            <div class="form-group">
              <input type="text" id="txt-No_Distrito" name="No_Distrito" class="form-control required" placeholder="Ingresar nombre" autocomplete="off" maxlength="64">
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>
          
			  <div class="row">
          <div class="col-xs-6 col-sm-3 col-md-2">
            <label>Siglas</label>
            <div class="form-group">
              <input type="text" name="No_Distrito_Breve" class="form-control" autocomplete="off" maxlength="2">
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-6 col-sm-3 col-md-3 hidden">
            <label>Precio Delivery</label>
            <div class="form-group">
              <input type="tel" name="Ss_Delivery" class="form-control input-number" autocomplete="off" maxlength="5">
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-6 col-sm-9 col-md-4 hidden">
            <label>¿Habilitar Ecommerce?</label>
            <div class="form-group">
		  				<select id="cbo-habilitar_ecommerce" name="Nu_Habilitar_Ecommerce" class="form-control required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-6 col-sm-9 col-md-3 div-Estado">
            <label>Estado <span class="label-advertencia">*</span></label>
            <div class="form-group">
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