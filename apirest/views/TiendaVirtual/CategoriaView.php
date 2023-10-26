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
    		  				<select id="cbo-Filtros_Categorias" name="Filtros_Categorias" class="form-control">
    		  				  <option value="Categoria">Nombre Categoría</option>
    		  				</select>
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="form-group">
                  <input type="text" id="txt-Global_Filter" name="Global_Filter" class="form-control" maxlength="100" placeholder="Buscar..." value="" autocomplete="off">
                </div>
              </div>
              
              <div class="col-md-3">
                <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Agregar == 1) : ?>
                <button type="button" class="btn btn-success btn-block" onclick="agregarCategoria()"><i class="fa fa-plus-circle"></i> Agregar</button>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="table-responsive">
            <table id="table-Categoria" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Editar == 1) : ?>
                    <th class="no-sort">Editar</th>
                  <?php endif; ?>
                  <th>Orden</th>
                  <th>Nombre</th>
                  <th class="no-sort">Imagen</th>
                  <th class="no-sort">Tienda</th>
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
  <?php
  $attributes = array('id' => 'form-Categoria');
  echo form_open('', $attributes);
  ?>
  <!-- Modal -->
  <div class="modal fade" id="modal-Categoria" role="dialog">
  <div class="modal-dialog modal-lg">
  	<div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title text-center"></h4>
      </div>
      
    	<div class="modal-body">
    	  <input type="hidden" name="EID_Empresa" class="form-control">
    	  <input type="hidden" name="EID_Familia" class="form-control">
    	  <input type="hidden" name="ENo_Familia" class="form-control">
    	  <input type="hidden" name="ENu_Orden" class="form-control">
        <input type="hidden" id="hidden-nombre_imagen_categoria" name="No_Imagen_Categoria" class="form-control" value="">
        <input type="hidden" id="hidden-nombre_imagen_url_categoria" name="No_Imagen_Url_Categoria" class="form-control" value="">
        
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

          <div class="col-xs-6 col-sm-5 col-md-4 col-lg-4">
            <label>Nombre <span class="label-advertencia">*</span></label>
            <div class="form-group">
              <input type="text" id="txt-No_Familia" name="No_Familia" placeholder="Ingresar Nombre" class="form-control required" maxlength="100" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-3 col-sm-2 col-md-1 col-lg-1">
            <label data-toggle="tooltip" data-placement="bottom" title="Para indicar el orden en el menú de tu tienda ecommerce">Orden </label>
            <div class="form-group">
              <input type="tel" id="txt-Nu_Orden" name="Nu_Orden" placeholder="" class="form-control input-number" maxlength="3" autocomplete="off" data-toggle="tooltip" data-placement="bottom" title="Para indicar el orden en el menú de tu tienda ecommerce">
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-sm-4 col-md-3 hidden">
            <label>Nombre Breve</label>
            <div class="form-group">
              <input type="text" id="txt-No_Familia_Breve" name="No_Familia_Breve" placeholder="Opcional" class="form-control" maxlength="100" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-sm-4 col-md-2 hidden">
            <label>Color</label>
            <div class="form-group">
		  				<select id="cbo-color" name="No_Html_Color" class="form-control required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>          
          
          <div class="col-xs-3 col-sm-3 col-md-2 col-lg-2">
            <div class="form-group">
              <label>Estado</label>
		  				<select id="cbo-Estado" name="Nu_Estado" class="form-control required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>
        
        <div class="row">
          <div class="col-xs-12 col-sm-12 col-md-8">
            <div class="text-center divDropzone"></div>
          </div>
          
          <div class="col-xs-12 col-sm-12 col-md-4 hidden-xs">
            <div class="form-group"><span class="hidden-md hidden-lg"><br></span>
              <div class="well well-sm" style="color: #664d03 !important;background-color: #fff3cd !important;border-color: #ffecb5 !important;">
                <strong><i class="fa fa-warning"></i> Indicaciones:</strong>
                <br>- Formatos: <b>.jpeg | .jpg | .png | .webp</b>
                <br>- Peso: <b>400 KB</b>
                <br>- Tamaño: <b>Ancho 200 x 200 px</b></b>
              </div>
            </div>
          </div>
        </div>
      </div>
      
    	<div class="modal-footer">
			  <div class="row">
          <div class="col-xs-6 col-md-6">
            <div class="form-group">
              <button type="button" class="btn btn-danger btn-md btn-block" data-dismiss="modal">Salir</button>
            </div>
          </div>
          <div class="col-xs-6 col-md-6">
            <div class="form-group">
              <button type="submit" id="btn-save" class="btn btn-success btn-md btn-block btn-verificar">Guardar</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
  <!-- /.Modal -->
  <?php echo form_close(); ?>
  
  <!-- modal informacion del item -->
  <div class="modal fade modal-info_item" id="modal-default">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="text-center" id="modal-header-info_item-title"></h4>
        </div>
        <div class="modal-body" id="modal-body-info_item">
            <div class="col-xs-12 text-center">
              <img class="img-responsive" style="
display: block;
margin-left: auto;
margin-right: auto;" src="">
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger btn-lg btn-block pull-center" data-dismiss="modal">Salir</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
  <!-- /.modal informacion del item -->
</div>
<!-- /.content-wrapper -->