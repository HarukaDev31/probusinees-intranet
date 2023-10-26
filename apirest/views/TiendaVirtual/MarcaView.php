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
    		  				<select id="cbo-Filtros_Marcas" name="Filtros_Marcas" class="form-control">
    		  				  <option value="Marca">Nombre Marca</option>
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
                <button type="button" class="btn btn-success btn-block" onclick="agregarMarca()"><i class="fa fa-plus-circle"></i> Agregar</button>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="table-responsive">
            <table id="table-Marca" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th>Orden</th>
                  <th>Nombre</th>
                  <th class="no-sort">Imagen</th>
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
  <form id="form-Marca" enctype="multipart/form-data" method="post" role="form" autocomplete="off">
  <!-- Modal -->
  <div class="modal fade" id="modal-Marca" role="dialog">
  <div class="modal-dialog">
  	<div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title text-center"></h4>
      </div>
      
    	<div class="modal-body">
        <input type="hidden" name="EID_Empresa" class="form-control">
    	  <input type="hidden" name="EID_Marca" class="form-control">
    	  <input type="hidden" name="ENo_Marca" class="form-control">
        <input type="hidden" id="hidden-Txt_Url_Logo_Lae_Shop" name="Txt_Url_Logo_Lae_Shop" class="form-control">
        
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

          <div class="col-xs-6 col-sm-9 col-md-7">
            <label>Nombre <span class="label-advertencia">*</span></label>
            <div class="form-group">
              <input type="text" id="txt-No_Marca" name="No_Marca" placeholder="Ingresar Nombre" class="form-control required" maxlength="100" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-3 col-sm-1 col-md-2">
            <label data-toggle="tooltip" data-placement="bottom" title="Para indicar el orden en el menú de tu tienda ecommerce">Orden </label>
            <div class="form-group">
              <input type="tel" id="txt-Nu_Orden" name="Nu_Orden" placeholder="" class="form-control input-number" maxlength="3" autocomplete="off" data-toggle="tooltip" data-placement="bottom" title="Para indicar el orden en el menú de tu tienda ecommerce">
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-3 col-sm-3 col-md-3">
            <div class="form-group">
              <label>Estado <span class="label-advertencia">*</span></label>
		  				<select id="cbo-Estado" name="Nu_Estado" class="form-control required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-12 col-sm-12 col-md-12 text-center divDropzone"></div>

        </div>
      </div>
      
    	<div class="modal-footer">
			  <div class="row">
          <div class="col-xs-6">
            <button type="button" class="btn btn-danger btn-md btn-block" data-dismiss="modal">Salir</button>
          </div>
          <div class="col-xs-6">
            <button type="submit" id="btn-save" class="btn btn-success btn-md btn-block btn-verificar">Guardar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
  </form>
  <!-- /.Modal -->
  <?php //echo form_close(); ?>
</div>
<!-- /.content-wrapper -->