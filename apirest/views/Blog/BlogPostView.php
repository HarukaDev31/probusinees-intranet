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
          <div class="box-header box-header-new div-Listar">
            <div class="row div-Filtros">
              <br>
              <div class="col-md-3">
                <div class="form-group">
    		  				<select id="cbo-Filtros_BlogPost" name="Filtros_BlogPost" class="form-control">
    		  				  <option value="Titulo">Título blog</option>
    		  				</select>
                </div>
              </div>
              
              <div class="col-md-7">
                <div class="form-group">
                  <input type="text" id="txt-Global_Filter" name="Global_Filter" class="form-control" maxlength="250" placeholder="Buscar" value="" autocomplete="off">
                </div>
              </div>
              
              <div class="col-xs-6 col-md-2">
                <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Agregar == 1) : ?>
                <button type="button" class="btn btn-success btn-block" onclick="agregarBlogPost()"><i class="fa fa-plus-circle"></i> Agregar</button>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <!-- ./box-header -->
          <div class="table-responsive div-Listar">
            <table id="table-Producto" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th>Tag</th>
                  <th>Título</th>
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
          
          <div class="box-body div-AgregarEditar">
            <?php
            $attributes = array('id' => 'form-BlogPost');
            echo form_open('', $attributes);
            ?>
          	  <input type="hidden" id="txt-EID_Post_Blog" name="EID_Post_Blog" class="form-control" value="">
          	  <input type="hidden" id="txt-EID_Gallery" name="EID_Gallery" class="form-control" value="">
          	  <input type="hidden" id="txt-ENo_Titulo_Blog" name="ENo_Titulo_Blog" class="form-control" value="">
          	  <input type="hidden" id="txt-ENo_Imagen_Gallery" name="ENo_Imagen_Gallery" class="form-control" value="">
          	  <input type="hidden" id="txt-ENo_Url_Imagen_Gallery" name="ENo_Url_Imagen_Gallery" class="form-control" value="">
    	  
              <div class="row">
                <div class="col-xs-12 col-sm-4 col-md-4">
                  <div class="form-group">
                    <label>Tag</label>
                    <select id="cbo-tag" name="ID_Tag_Blog" class="form-control" style="width: 100%;"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
  
                <div class="col-xs-12 col-sm-12 col-md-8">
                  <div class="form-group">
                    <label>Título <span class="label-advertencia">*</span></label>
                    <textarea name="No_Titulo_Blog" class="form-control required" placeholder="Ingresar nombre" maxlength="100"></textarea>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>

      			    <div class="col-xs-12 col-sm-12 col-md-12">
                  <div class="form-group">
                    <label>Contenido</label>
                    <textarea name="Txt_Contenido_Blog" class="form-control required" rows="10" cols="80"></textarea>
                  </div>
                </div>
                
                <div class="col-xs-12 col-sm-12 col-md-12">
                  <div class="form-group">
                    <label>Tipo media</label>
                    <select id="cbo-tipo_media" name="ID_Tipo_Media" class="form-control" style="width: 100%;"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12 div-video">
                  <label data-toggle="tooltip" data-placement="bottom" title="Ingresar URL, ejemplo: https://www.youtube.com/watch?v=O1T7EIeCxaA">Link <span class="label-advertencia">*</span></label>
                  <div class="form-group">
                    <input type="text" id="txt-No_Url_Video_Blog" name="No_Url_Video_Blog" class="form-control" placeholder="Ingresar link" maxlength="255" autocomplete="off" data-toggle="tooltip" data-placement="bottom" title="Ingresar URL, ejemplo: https://www.youtube.com/watch?v=O1T7EIeCxaA">
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
              </div>
              
    	        <div class="row div-imagen"><br>
                <div class="col-xs-12 col-sm-4 col-md-4">
                  <div class="well well-sm">
                    <i class="fa fa-warning"></i> Indicaciones:
                    <br>- Imagen: <b>opcional</b>
                    <br>- Formato: <b>PNG</b>
                    <br>- Fondo de imágen(es): <b>SIN FONDO / BLANCO / TRANSPARENTE</b>
                    <br>- Tamaños: <br>
                      &nbsp;&nbsp;* Pequeño: <b>Ancho: 199px</b> y <b>Alto: 199px</b><br>
                      &nbsp;&nbsp;* Mediano: <b>Ancho: 340px</b> y <b>Alto: 340px</b>
                    <br>- Peso Máximo: <b>200 KB</b>
                  </div>
                </div>
                
                <div class="col-xs-12 col-sm-8 col-md-8 text-center divDropzone"></div>
              </div>
              
      			  <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                  <div class="form-group">
                    <label>Estado <span class="label-advertencia">*</span></label>
                    <select id="cbo-estado" name="Nu_Estado" class="form-control required" style="width: 100%;"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
              </div>

      			  <div class="row">
      			    <br/>
                <div class="col-xs-6 col-sm-6 col-md-6">
                  <div class="form-group">
                    <button type="button" id="btn-cancelar" class="btn btn-danger btn-md btn-block"><span class="fa fa-close"></span> Cancelar (ESC)</button>
                  </div>
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6">
                  <div class="form-group">
                    <button type="submit" id="btn-save" class="btn btn-success btn-md btn-block btn-verificar"><i class="fa fa-save"></i> Guardar (ENTER)</button>
                  </div>
                </div>
              </div>
            <?php echo form_close(); ?>
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
</div>
<!-- /.content-wrapper -->