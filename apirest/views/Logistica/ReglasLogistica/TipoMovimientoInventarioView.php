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
    		  				<select id="cbo-Filtros_TiposMovimiento" name="Filtros_TiposMovimiento" class="form-control">
    		  				  <option value="Tipo_Movimiento">Tipo de movimiento</option>
    		  				</select>
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="form-group">
                  <input type="text" id="txt-Global_Filter" name="Global_Filter" class="form-control" maxlength="50" placeholder="Buscar" value="" autocomplete="off">
                </div>
              </div>
              
              <div class="col-md-3">
                <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Agregar == 1) : ?>
                <button type="button" class="btn btn-success btn-block" onclick="agregarTipo_Movimiento()"><i class="fa fa-plus-circle"></i> Agregar</button>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="table-responsive">
            <table id="table-Tipo_Movimiento" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th>Nombre</th>
                  <th>Cod. Sunat</th>
                  <th>Tipo Movimiento</th>
                  <th>Costear</th>
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
  $attributes = array('id' => 'form-Tipo_Movimiento');
  echo form_open('', $attributes);
  ?>
  <!-- Modal -->
  <div class="modal fade" id="modal-Tipo_Movimiento" role="dialog">
  <div class="modal-dialog">
  	<div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title text-center"></h4>
      </div>
      
    	<div class="modal-body">
    	  <input type="hidden" name="EID_Tipo_Movimiento" class="form-control required">
    	  <input type="hidden" name="ENo_Tipo_Movimiento" class="form-control required">
        
			  <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label>Nombre <span class="label-advertencia">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-book" aria-hidden="true"></i></span>
                <input type="text" id="txt-No_Tipo_Movimiento" name="No_Tipo_Movimiento" placeholder="Ingresar Nombre" class="form-control required" maxlength="50" autocomplete="off">
              </div>
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>
          
			  <div class="row">
          <div class="col-md-3">
            <div class="form-group">
              <label>Codigo Sunat <span class="label-advertencia">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-book" aria-hidden="true"></i></span>
                <input type="tel" id="txt-Nu_Sunat_Codigo" name="Nu_Sunat_Codigo" class="form-control required input-number" maxlength="2" autocomplete="off">
              </div>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-6 col-md-3">
            <div class="form-group">
              <label>Tipo Operación <span class="label-advertencia">*</span></label>
		  				<select id="cbo-TipoOperacion" name="Nu_Tipo_Movimiento" class="form-control required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-6 col-md-3">
            <div class="form-group">
              <label>Promediar <span class="label-advertencia">*</span></label>
		  				<select id="cbo-Costear" name="Nu_Costear" class="form-control required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
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