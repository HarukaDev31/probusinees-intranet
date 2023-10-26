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
              <div class="col-md-12">
                <div class="form-group">
                  <h2>Slider Blog</h2>
                </div>
              </div>
              <br>              
              <div class="col-md-3">
                <div class="form-group">
    		  				<select id="cbo-Filtros_Inicios" name="Filtros_Inicios" class="form-control">
    		  				  <option value="Inicio">Nombre Slider</option>
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
                <button type="button" class="btn btn-success btn-block" onclick="agregarInicio('4')"><i class="fa fa-plus-circle"></i> Agregar</button>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="table-responsive">
            <table id="table-Inicio" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th>Nombre</th>
                  <th>Imágen</th>
                  <th>Orden</th>
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
        
        <div class="box box-content">
          <!-- box-header -->
          <div class="box-header box-header-new">
            <div class="row div-Filtros">
              <div class="col-md-12">
                <div class="form-group">
                  <h2>Slider Blog (Mobile)</h2>
                </div>
              </div>
              <br>              
              <div class="col-md-3">
                <div class="form-group">
    		  				<select id="cbo-Filtros_Inicios" name="Filtros_Inicios" class="form-control">
    		  				  <option value="Inicio">Nombre Slider</option>
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
                <button type="button" class="btn btn-success btn-block" onclick="agregarInicio('5')"><i class="fa fa-plus-circle"></i> Agregar</button>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="table-responsive">
            <table id="table-Inicio-mobile" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th>Nombre</th>
                  <th>Imágen</th>
                  <th>Orden</th>
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
  $attributes = array('id' => 'form-Inicio');
  echo form_open('', $attributes);
  ?>
  <!-- Modal -->
  <div class="modal fade" id="modal-Inicio" role="dialog">
  <div class="modal-dialog">
  	<div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title text-center"></h4>
      </div>
      
    	<div class="modal-body">
    	  <input type="hidden" name="EID_Empresa" class="form-control">
    	  <input type="hidden" name="EID_Ecommerce_Inicio" class="form-control">
    	  <input type="hidden" name="ENo_Slider" class="form-control">
        <input type="hidden" id="hidden-tipo_inicio" name="Nu_Tipo_Inicio" class="form-control" value="1">
        <input type="hidden" id="hidden-nombre_imagen_categoria" name="No_Imagen_Inicio_Slider" class="form-control" value="">
        <input type="hidden" id="hidden-nombre_imagen_url_categoria" name="No_Imagen_Url_Inicio_Slider" class="form-control" value="">
        
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
          <?php } else { ?>
    	      <input type="hidden" id="cbo-Empresas" name="ID_Empresa" class="form-control" value="<?php echo $this->user->ID_Empresa; ?>">
          <?php } ?>
          
          <div class="col-sm-9 col-md-9">
            <label>Nombre <span class="label-advertencia">*</span></label>
            <div class="form-group">
              <input type="text" id="txt-No_Slider" name="No_Slider" placeholder="Ingresar Nombre" class="form-control required" maxlength="100" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
  				<div class="col-sm-3 col-md-3">
            <label>Orden</label>
  					<div class="form-group">	
              <input type="tel" id="txt-Nu_Orden_Slider" name="Nu_Orden_Slider" class="form-control input-number" maxlength="4" autocomplete="off">
              <span class="help-block"></span>
            </div>
          </div>
          
          <div class="col-sm-9 col-md-9">
            <label>Link Acción <span class="label-advertencia">*</span></label>
            <div class="form-group">
              <input type="text" id="txt-No_Url_Accion" name="No_Url_Accion" placeholder="Ingresar link" class="form-control required" maxlength="255" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-sm-3 col-md-3 div-Estado">
            <div class="form-group">
              <label>Estado <span class="label-advertencia">*</span></label>
		  				<select id="cbo-Estado" name="Nu_Estado_Slider" class="form-control required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>
        
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <div class="well well-sm">
                <i class="fa fa-warning"></i> Indicaciones:
                <br>- Formato: <b>PNG / JPG / JPEG</b>
                <br>- Tamaño imagén <b>Ancho: 1440px y Alto: 382px</b>
                <br>- Peso Máximo <b>1 MB</b>
              </div>
            </div>
          </div>
          
          <div class="col-md-8 text-center divDropzone"></div>
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
  <!-- /.Modal -->
  <?php echo form_close(); ?>
</div>
<!-- /.content-wrapper -->