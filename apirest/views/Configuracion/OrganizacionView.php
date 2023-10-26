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
    		  				<select id="cbo-Filtros_Organizaciones" name="Filtros_Organizaciones" class="form-control">
                    <option value="Organizacion">Nombre Organización</option>
                    <option value="Empresa">Nombre Empresa</option>
    		  				</select>
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="form-group">
                  <input type="text" id="txt-Global_Filter" name="Global_Filter" class="form-control" placeholder="Buscar ..." value="" autocomplete="off">
                </div>
              </div>
              
              <div class="col-md-3">
                <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Agregar == 1) : ?>
                <button type="button" class="btn btn-success btn-block" onclick="agregarOrganizacion()"><i class="fa fa-plus-circle"></i> Agregar</button>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="table-responsive">
            <table id="table-Organizacion" class="table table-striped table-bordered">
              <thead>
              <tr>
                <th>Empresa</th>
                <th>Organización</th>
                <!--
                  <th class="no-sort">Estado Sistema</th>
                  <th class="no-sort">Limpiar</th>
                  <th class="no-sort">Pasar Producción</th>
                  <th class="no-sort">Pasar Producción SIN BORRAR</th>
                -->
                <th class="no-sort">Estado Org.</th>
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
  $attributes = array('id' => 'form-Organizacion');
  echo form_open('', $attributes);
  ?>
  <div class="modal fade" id="modal-Organizacion" role="dialog">
  <div class="modal-dialog">
  	<div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title text-center"></h4>
      </div>
      
    	<div class="modal-body">
    	  <input type="hidden" name="EID_Organizacion" class="form-control required">
    	  <input type="hidden" name="ENo_Organizacion" class="form-control required">
    	  
			  <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>Empresa <span class="label-advertencia">*</span></label>
              <select id="cbo-Empresas" name="ID_Empresa" class="form-control select2 required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label>Nombre <span class="label-advertencia">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="blue fa fa-bank" aria-hidden="true"></i></span>
                <input type="text" id="txt-No_Organizacion" name="No_Organizacion" placeholder="Ingresar descripción" class="form-control required" autocomplete="off" maxlength="100">
              </div>
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>
          
			  <div class="row">
          <div class="col-md-8">
            <div class="form-group">
              <label>Descripción</label>
              <div class="input-group">
                <span class="input-group-addon"><i class="blue fa fa-bank" aria-hidden="true"></i></span>
                <input type="text" name="Txt_Organizacion" placeholder="Ingresar descripción breve" class="form-control" autocomplete="off">
              </div>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <?php
          $sCssDisplayView='style="display:none"';
          if ( $this->user->No_Usuario == 'root' ){
            $sCssDisplayView='';
          } ?>
          <div class="col-md-4" <?php echo $sCssDisplayView; ?>>
            <div class="form-group">
              <label>Estado <span class="label-advertencia">*</span></label>
		  				<select id="cbo-Estado" name="Nu_Estado" class="form-control required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-md-12" style="display:none">
            <div class="form-group">
              <label>Proveedor FE <span class="label-advertencia">*</span></label>
              <select id="cbo-tipo_proveedor_fe" name="Nu_Tipo_Proveedor_FE" class="form-control required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>
          
			  <div class="row" style="display:none">
          <div class="col-md-12">
            <div class="form-group">
              <label>Ruta <span class="label-advertencia">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="blue fa fa-key" aria-hidden="true"></i></span>
                <input type="text" name="Txt_FE_Ruta" placeholder="Ingresar Ruta" class="form-control" autocomplete="off">
              </div>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-md-12">
            <div class="form-group">
              <label>Token <span class="label-advertencia">*</span></label>
              <div class="input-group">
                <span class="input-group-addon"><i class="blue fa fa-key" aria-hidden="true"></i></span>
                <input type="text" name="Txt_FE_Token" placeholder="Ingresar Token" class="form-control" autocomplete="off">
              </div>
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>
        
			  <div class="row" style="display:none">
          <div class="col-md-6">
            <div class="form-group">
              <label>Localhost Hostname</label>
              <div class="input-group">
                <span class="input-group-addon"><i class="blue fa fa-key" aria-hidden="true"></i></span>
                <input type="text" name="Txt_Autorizacion_Venta_Localhost_Hostname" placeholder="Ingresar Hostname" class="form-control" autocomplete="off">
              </div>
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label>Localhost Username</label>
              <div class="input-group">
                <span class="input-group-addon"><i class="blue fa fa-key" aria-hidden="true"></i></span>
                <input type="text" name="Txt_Autorizacion_Venta_Localhost_User" placeholder="Ingresar Username" class="form-control" autocomplete="off">
              </div>
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label>Localhost Password</label>
              <div class="input-group">
                <span class="input-group-addon"><i class="blue fa fa-key" aria-hidden="true"></i></span>
                <input type="text" name="Txt_Autorizacion_Venta_Localhost_Password" placeholder="Ingresar Password" class="form-control" autocomplete="off">
              </div>
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label>Localhost Database</label>
              <div class="input-group">
                <span class="input-group-addon"><i class="blue fa fa-key" aria-hidden="true"></i></span>
                <input type="text" name="Txt_Autorizacion_Venta_Localhost_Database" placeholder="Ingresar Database" class="form-control" autocomplete="off">
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