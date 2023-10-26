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
    		  				<select id="cbo-Filtros_Transporte_Deliverys" name="Filtros_Transporte_Deliverys" class="form-control">
    		  				  <option value="Transporte_Delivery">Nombre Transporte Delivery</option>
    		  				  <option value="DNI">DNI</option>
    		  				</select>
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="form-group">
                  <input type="text" id="txt-Global_Filter" name="Global_Filter" class="form-control" maxlength="100" placeholder="Buscar" value="" autocomplete="off">
                </div>
              </div>
              
              <div class="col-md-3">
                <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Agregar == 1) : ?>
                <button type="button" class="btn btn-success btn-block" onclick="agregarTransporte_Delivery()"><i class="fa fa-plus-circle"></i> Agregar</button>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="table-responsive">
            <table id="table-Transporte_Delivery" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th>Tipo Doc.</th>
                  <th>Num. Doc.</th>
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
  $attributes = array('id' => 'form-Transporte_Delivery');
  echo form_open('', $attributes);
  ?>
  <div class="modal fade" id="modal-Transporte_Delivery" role="dialog">
  <div class="modal-dialog">
  	<div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title text-center"></h4>
      </div>
    	<div class="modal-body">
    	  
    	  <input type="hidden" name="EID_Empresa" class="form-control required">
    	  <input type="hidden" name="EID_Transporte_Delivery" class="form-control required">
    	  <input type="hidden" name="ENu_Documento_Identidad" class="form-control required">
    	  
			  <div class="row">
          <div class="col-md-3">
            <label>DNI <span class="label-advertencia">*</span></label>
            <div class="form-group">
              <input type="tel" id="txt-Nu_Documento_Identidad" name="Nu_Documento_Identidad" class="form-control required input-number" maxlength="8" placeholder="Ingresar número" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-md-2 text-center">
            <label>Api</label>
            <div class="form-group">
              <button type="button" id="btn-cloud-api_transporte_delivery" class="btn btn-success btn-block btn-md"><i class="fa fa-cloud-download fa-lg"></i></button>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-md-7">
            <label>Nombre(s) y Apellidos <span class="label-advertencia">*</span></label>
            <div class="form-group">
              <input type="text" name="No_Transportista" placeholder="Ingresar Nombre" maxlength="100" class="form-control required" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>
        
			  <div class="row">
          <div class="col-xs-6 col-md-3">
            <label>Celular</label>
            <div class="form-group">
              <input type="tel" name="Nu_Celular" class="form-control" data-inputmask="'mask': ['999 999 999']" data-mask autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-6 col-md-4">
            <div class="form-group">
              <label>Estado <span class="label-advertencia">*</span></label>
		  				<select id="cbo-Estado" name="Nu_Estado" class="form-control required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>
          
			  <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label>Dirección</label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-map-marker" aria-hidden="true"></i></span>
                <input type="text" name="Txt_Direccion" class="form-control" autocomplete="off" />
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
  <?php echo form_close(); ?>
  <!-- /.Modal -->
</div>
<!-- /.content-wrapper -->