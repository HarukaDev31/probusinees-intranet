<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-8">
          <h1><i class="<?php echo $this->MenuModel->verificarAccesoMenuCRUD()->Txt_Css_Icons; ?>" aria-hidden="true"></i> <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?></h1>
        </div>
        
        <div class="col-sm-4 div-Listar">
          <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Agregar == 1) : ?>
            <button type="button" class="btn btn-primary btn-block" onclick="agregarCategoria()"><i class="fa fa-plus-circle"></i> Agregar</button>
          <?php endif; ?>
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
$attributes = array('id' => 'form-Categoria');
echo form_open('', $attributes);
?>
<!-- Modal -->
<div class="modal fade" id="modal-Categoria" role="dialog">
<div class="modal-dialog modal-lg">
  <div class="modal-content">
    <div class="modal-header">
      <h4 class="modal-title text-center"></h4>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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
          <label>Nombre <span class="label-advertencia text-danger">*</span></label>
          <div class="form-group">
            <input type="text" id="txt-No_Familia" name="No_Familia" placeholder="Ingresar Nombre" class="form-control required" maxlength="100" autocomplete="off">
            <span class="help-block text-danger" id="error"></span>
          </div>
        </div>
        
        <div class="col-xs-3 col-sm-2 col-md-1 col-lg-1">
          <label data-toggle="tooltip" data-placement="bottom" title="Para indicar el orden en el menú de tu tienda ecommerce">Orden </label>
          <div class="form-group">
            <input type="tel" id="txt-Nu_Orden" name="Nu_Orden" placeholder="" class="form-control input-number" maxlength="3" autocomplete="off" data-toggle="tooltip" data-placement="bottom" title="Para indicar el orden en el menú de tu tienda ecommerce">
            <span class="help-block text-danger" id="error"></span>
          </div>
        </div>
        
        <div class="col-sm-4 col-md-3  d-none">
          <label>Nombre Breve</label>
          <div class="form-group">
            <input type="text" id="txt-No_Familia_Breve" name="No_Familia_Breve" placeholder="Opcional" class="form-control" maxlength="100" autocomplete="off">
            <span class="help-block" id="error"></span>
          </div>
        </div>

        <div class="col-sm-4 col-md-2 d-none">
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
            <span class="help-block text-danger" id="error"></span>
          </div>
        </div>
      </div>
      
      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-8">
          <div class="text-center divDropzone"></div>
        </div>
        
        <div class="col-xs-12 col-sm-12 col-md-4 hidden-xs">
          <div class="form-group">
            <div class="alert alert-waring" style="color: #664d03 !important;background-color: #fff3cd !important;border-color: #ffecb5 !important;">
              <strong><i class="fa fa-warning"></i> Indicaciones:</strong>
              <br>- Formatos: <b>.jpeg | .jpg | .png | .webp</b>
              <br>- Peso: <b>400 KB</b>
              <br>- Tamaño: <b>Ancho 200 x 200 px</b></b>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="modal-footer justify-content-between">
      <button type="button" class="col btn btn-danger btn-md btn-block" data-dismiss="modal">Salir</button>
      <button type="submit" id="btn-save" class="col btn btn-success btn-md btn-block btn-verificar">Guardar</button>
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
            <img class="img-responsive" style="display: block; margin-left: auto; margin-right: auto;" src="">
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