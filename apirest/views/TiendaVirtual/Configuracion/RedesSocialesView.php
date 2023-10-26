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
              <?php
              if ( $this->user->No_Usuario == 'root' ){ ?>
              <div class="col-md-12">
                <label>Empresa</label>
                <div class="form-group">
                  <select id="cbo-filtro_empresa" name="ID_Empresa" class="form-control select2"  style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              <?php } else { ?>
                <input type="hidden" id="cbo-filtro_empresa" name="ID_Empresa" class="form-control" value="<?php echo $this->user->ID_Empresa; ?>">
              <?php } ?>

              <div class="col-md-3 hidden">
                <div class="form-group">
    		  				<select id="cbo-Filtros_MedioPago" name="Filtros_MedioPago" class="form-control">
    		  				  <option value="MedioPago">Nombre Medio Pago</option>
    		  				</select>
                </div>
              </div>
              
              <div class="col-md-6 hidden">
                <div class="form-group">
                  <input type="text" id="txt-Global_Filter" name="Global_Filter" class="form-control" placeholder="Ingresar..." value="" autocomplete="off" maxlength="30">
                </div>
              </div>
              
              <div class="col-md-3 hidden">
                <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Agregar == 1) : ?>
                <button type="button" class="btn btn-success btn-block">Agregar</button>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="table-responsive">
            <table id="table-MedioPago" class="table table-striped table-bordered">
              <thead>
              <tr>
                <?php if ( $this->user->No_Usuario == 'root' ){ ?>
                <th>Empresa</th>
                <?php } ?>
                <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Editar == 1) : ?>
                  <th class="no-sort">Editar</th>
                <?php endif; ?>
                <th>Facebook</th>
                <th>Instagram</th>
                <th>TikTok</th>
                <th>YouTube</th>
                <th>LinkedIn</th>
                <th>Twitter</th>
                <th>Pinterest</th>
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
  $attributes = array('id' => 'form-MedioPago');
  echo form_open('', $attributes);
  ?>
  <div class="modal fade" id="modal-MedioPago" role="dialog">
  <div class="modal-dialog">
  	<div class="modal-content">
    	<div class="modal-body">
        <input type="hidden" name="EID_Empresa" class="form-control">
    	  <input type="hidden" name="EID_Configuracion" class="form-control">
    	  
			  <div class="row">
          <?php
          if ( $this->user->No_Usuario == 'root' ){ ?>
          <div class="col-xs-12 col-md-12">
            <div class="form-group">
              <label>Empresa <span class="label-advertencia">*</span></label>
              <select id="cbo-Empresas" name="ID_Empresa" class="form-control select2 required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          <?php } else { ?>
    	      <input type="hidden" id="cbo-Empresas" name="ID_Empresa" class="form-control" value="<?php echo $this->user->ID_Empresa; ?>">
          <?php } ?>
          
          <div class="col-xs-12 col-md-12">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title text-center"></h4>
          </div>

          <div class="col-xs-12 col-md-12">
            <div class="form-group">
              <label>Facebook</label>
              <div class="input-group">
                <span class="input-group-addon"><i class="blue fa fa-facebook" aria-hidden="true"></i></span>
                <input type="text" id="No_Red_Social_Facebook" name="No_Red_Social_Facebook" placeholder="" class="form-control input-replace_red_social" data-id_url="a-facebook" data-url="https://www.facebook.com/" autocomplete="off" value="">
              </div>
              <span class="help-block" id="error"></span>
              <span style="color: #737373;">
                <a id="a-facebook" href="https://www.facebook.com/laesystemsperu" target="_blank" rel="noopener noreferrer"></a>
              </span>
            </div>
          </div>
          
          <div class="col-xs-12 col-md-12">
            <div class="form-group">
              <label>Instagram</label>
              <div class="input-group">
                <span class="input-group-addon"><i class="blue fa fa-instagram" aria-hidden="true"></i></span>
                <input type="text" id="No_Red_Social_Instagram" name="No_Red_Social_Instagram" placeholder="" class="form-control input-replace_red_social" data-id_url="a-instagram" data-url="https://www.instagram.com/" autocomplete="off" value="">
              </div>
              <span class="help-block" id="error"></span>
              <span style="color: #737373;">
                <a id="a-instagram" href="https://www.instagram.com/laesystems" target="_blank" rel="noopener noreferrer"></a>
              </span>
            </div>
          </div>
          
          <div class="col-xs-12 col-md-12">
            <div class="form-group">
              <label>TikTok</label>
              <div class="input-group">
                <span class="input-group-addon"><i class="blue fa fa-music" aria-hidden="true"></i></span>
                <input type="text" id="No_Red_Social_Tiktok" name="No_Red_Social_Tiktok" placeholder="" class="form-control input-replace_red_social" data-id_url="a-tiktok" data-url="https://www.tiktok.com/" autocomplete="off" value="">
              </div>
              <span class="help-block" id="error"></span>
              <span style="color: #737373;">
                <a id="a-tiktok" href="https://www.tiktok.com/@laesystems" target="_blank" rel="noopener noreferrer"></a>
              </span>
            </div>
          </div>
          
          <div class="col-xs-12 col-md-12">
            <div class="form-group">
              <label>YouTube</label>
              <div class="input-group">
                <span class="input-group-addon"><i class="blue fa fa-youtube" aria-hidden="true"></i></span>
                <input type="text" id="No_Red_Social_Youtube" name="No_Red_Social_Youtube" placeholder="" class="form-control input-replace_red_social" data-id_url="a-youtube" data-url="https://www.youtube.com/channel/" autocomplete="off" value="">
              </div>
              <span class="help-block" id="error"></span>
              <span style="color: #737373;">
                <a id="a-youtube" href="https://www.youtube.com/channel/UClrHLQVilNeqG40rleqzvTw" target="_blank" rel="noopener noreferrer"></a>
              </span>
            </div>
          </div>
          
          <div class="col-xs-12 col-md-12">
            <div class="form-group">
              <label>LinkedIn</label>
              <div class="input-group">
                <span class="input-group-addon"><i class="blue fa fa-linkedin" aria-hidden="true"></i></span>
                <input type="text" id="No_Red_Social_Linkedin" name="No_Red_Social_Linkedin" placeholder="" class="form-control input-replace_red_social" data-id_url="a-linkedin" data-url="https://www.linkedin.com/company/" autocomplete="off" value="">
              </div>
              <span class="help-block" id="error"></span>
              <span style="color: #737373;">
                <a id="a-linkedin" href="https://www.linkedin.com/company/laesystems" target="_blank" rel="noopener noreferrer"></a>
              </span>
            </div>
          </div>
          
          <div class="col-xs-12 col-md-12">
            <div class="form-group">
              <label>Twitter</label>
              <div class="input-group">
                <span class="input-group-addon"><i class="blue fa fa-twitter" aria-hidden="true"></i></span>
                <input type="text" id="No_Red_Social_Twitter" name="No_Red_Social_Twitter" placeholder="" class="form-control input-replace_red_social" data-id_url="a-twitter" data-url="https://www.twitter.com/" autocomplete="off" value="">
              </div>
              <span class="help-block" id="error"></span>
              <span style="color: #737373;">
                <a id="a-twitter" href="https://twitter.com/laesystems" target="_blank" rel="noopener noreferrer"></a>
              </span>
            </div>
          </div>
          
          <div class="col-xs-12 col-md-12">
            <div class="form-group">
              <label>Pinterest</label>
              <div class="input-group">
                <span class="input-group-addon"><i class="blue fa fa-pinterest" aria-hidden="true"></i></span>
                <input type="text" id="No_Red_Social_Pinterest" name="No_Red_Social_Pinterest" placeholder="" class="form-control input-replace_red_social" data-id_url="a-pinterest" data-url="https://www.pinterest.com/" autocomplete="off" value="">
              </div>
              <span class="help-block" id="error"></span>
              <span style="color: #737373;">
                <a id="a-pinterest" href="https://www.pinterest.com/laesystems" target="_blank" rel="noopener noreferrer"></a>
              </span>
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
  <?php echo form_close(); ?>
  <!-- /.Modal -->
</div>
<!-- /.content-wrapper -->