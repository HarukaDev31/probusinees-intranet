<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-8">
          <h1><i class="<?php echo $this->MenuModel->verificarAccesoMenuCRUD()->Txt_Css_Icons; ?>" aria-hidden="true"></i> <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?></h1>
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
                <div class="col-md-12">
                  <div class="form-group">
                    <h2>PC / Laptop / Tablet</h2>
                  </div>
                </div>
                <span class="d-none hidden-xs"><br></span>
                <div class="d-none col-md-3 hidden">
                  <div class="form-group">
                    <select id="cbo-Filtros_Inicios" name="Filtros_Inicios" class="form-control">
                      <option value="Inicio">Nombre Slider</option>
                    </select>
                  </div>
                </div>
                
                <div class="col-md-6 d-none ">
                  <div class="form-group">
                    <input type="text" id="txt-Global_Filter" name="Global_Filter" class="form-control" maxlength="100" placeholder="Buscar" value="" autocomplete="off">
                  </div>
                </div>
                
                <div class="col-md-12">
                  <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Agregar == 1) : ?>
                  <button type="button" class="btn btn-success btn-block" onclick="agregarInicio('1')">Agregar</button>
                  <?php endif; ?>
                </div>
              </div>
              
              <!-- /.box-header -->
              <div class="table-responsive">
                <table id="table-Inicio" class="table table-striped table-bordered">
                  <thead>
                    <tr>
                      <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Editar == 1) : ?>
                        <th class="no-sort">Editar</th>
                      <?php endif; ?>
                      <th>Orden</th>
                      <th class="no-sort">Imágen</th>
                      <th class="no-sort">Nombre</th>
                      <th class="no-sort">Tienda</th>
                      <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Eliminar == 1) : ?>
                        <th class="no-sort">Eliminar</th>
                      <?php endif; ?>
                    </tr>
                  </thead>
                </table>
              </div>
              
              <div class="row div-Filtros">
                <div class="col-md-12 mt-3">
                  <div class="form-group">
                    <h2>Celular</h2>
                  </div>
                </div>
                <span class="d-none hidden-xs"><br></span>
                <div class="d-none col-md-3 d-none ">
                  <div class="form-group">
                    <select id="cbo-Filtros_Inicios" name="Filtros_Inicios" class="form-control">
                      <option value="Inicio">Nombre Slider</option>
                    </select>
                  </div>
                </div>
                
                <div class="col-md-6 d-none ">
                  <div class="form-group">
                    <input type="text" id="txt-Global_Filter" name="Global_Filter" class="form-control" maxlength="100" placeholder="Buscar" value="" autocomplete="off">
                  </div>
                </div>
                
                <div class="col-md-12">
                  <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Agregar == 1) : ?>
                  <button type="button" class="btn btn-success btn-block" onclick="agregarInicio('3')">Agregar</button>
                  <?php endif; ?>
                </div>
              </div>
              
              <div class="table-responsive">
                <table id="table-Inicio-mobile" class="table table-striped table-bordered">
                  <thead>
                    <tr>
                      <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Editar == 1) : ?>
                        <th class="no-sort">Editar</th>
                      <?php endif; ?>
                      <th>Orden</th>
                      <th class="no-sort">Imágen</th>
                      <th class="no-sort">Nombre</th>
                      <th class="no-sort">Tienda</th>
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
      </div>
      <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php
$attributes = array('id' => 'form-Inicio');
echo form_open('', $attributes);
?>
<!-- Modal -->
<div class="modal fade" id="modal-Inicio" role="dialog">
  <div class="modal-dialog modal-lg">
  	<div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title text-center"></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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
          
          <div class="col-xs-8 col-sm-10 col-md-3">
            <label>Nombre</label>
            <div class="form-group">
              <input type="text" id="txt-No_Slider" name="No_Slider" placeholder="Opcional" class="form-control" maxlength="100" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
  				<div class="col-xs-4 col-sm-2 col-md-1">
            <label>Orden</label>
  					<div class="form-group">	
              <input type="text" inputmode="number" id="txt-Nu_Orden_Slider" name="Nu_Orden_Slider" class="form-control input-number" maxlength="4" autocomplete="off">
              <span class="help-block"></span>
            </div>
          </div>
          
          <div class="col-xs-12 col-sm-10 col-md-6">
            <label>Link / URL</label>
            <div class="form-group">
              <input type="text" id="txt-No_Url_Accion" name="No_Url_Accion" placeholder="Opcional" class="form-control" maxlength="255" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-12 col-sm-2 col-md-2">
            <div class="form-group">
              <label>Estado <span class="label-advertencia">*</span></label>
              <select id="cbo-Estado" name="Nu_Estado_Slider" class="form-control required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>
        
        <div class="row">
          <div class="col-sm-12 col-md-8 text-center divDropzone"></div>
          <div class="col-sm-12 col-md-4 hidden-xs">
            <div class="form-group">
              <div class="alert alert-warning" style="">
                <strong><i class="fa fa-warning"></i> Indicaciones:</strong><!--modificado-->
                <br>- Formato: <b>PNG / JPG</b>
                <br>- Tamaño Tablet / Laptop / PC</strong>:<span class="hidden-xs hidden-sm"></span> <strong>Alto: 350px y Ancho: 1200px</strong>
                <br>- Tamaño Celular:<span class="hidden-xs hidden-sm"></span> <strong>Alto: 700px y Ancho: 700px</strong>
                <br>- Peso: <b>1 MB</b>
                <br>- Reducir peso:
                <br><b><a href="https://compressor.io" target="_blank" rel="noopener noreferrer" class="d-block mb-3 ml-3">1. https://compressor.io</a></b>
                <b><a href="https://www.iloveimg.com/es/comprimir-imagen/comprimir-jpg" target="_blank" rel="noopener noreferrer" class="d-block mb-3 ml-3">2. https://www.iloveimg.com/es/comprimir-imagen/comprimir-jpg</a></b>
              </div>
            </div>
          </div>
        </div>

        <span class="hidden-sm hidden-md hidden-lg"><br></span>
        
			  <div class="row">
          <div class="col-xs-6 col-md-6">
            <div class="form-group">
              <button type="button" class="btn btn-danger btn-lg btn-block" data-dismiss="modal">Salir</button>
            </div>
          </div>
          <div class="col-xs-6 col-md-6">
            <div class="form-group">
              <button type="submit" id="btn-save" class="btn btn-success btn-lg btn-block btn-verificar">Guardar</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- /.Modal -->
<?php echo form_close(); ?>