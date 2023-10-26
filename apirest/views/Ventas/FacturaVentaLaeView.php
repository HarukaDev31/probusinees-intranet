<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">  <!-- Main content -->
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
              <div class="col-xs-12 hidden">
                <label>&nbsp;</label>
                <button type="button" id="btn-filter" class="btn btn-primary btn-block">Recargar</button>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="table-responsive">
            <table id="table-Pos" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th>F. Emisión</th>
                  <th>Tipo</th>
                  <th>Serie</th>
                  <th>Número</th>
                  <th class="sort-right">Total</th>
                  <th class="sort-right">Saldo</th>
                  <th class="no-sort">Pago</th>
                  <th class="no-sort">Estado</th>
                  <th class="no-sort">PDF</th>
                  <th class="no-sort">XML</th>
                  <th class="no-sort">CDR</th>
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
  $attributes = array('id' => 'form-Pos');
  echo form_open('', $attributes);
  ?>
  <div class="modal fade" id="modal-Pos" role="dialog">
  <div class="modal-dialog">
  	<div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title text-center"></h4>
      </div>
      
    	<div class="modal-body">
    	  <input type="hidden" name="EID_Empresa" class="form-control">
    	  <input type="hidden" name="EID_Organizacion" class="form-control">
    	  <input type="hidden" name="EID_Pos" class="form-control">
        <input type="hidden" name="ENu_Pos" class="form-control">
    	  
			  <div class="row">
          <?php
          if ( $this->user->No_Usuario == 'root' ){ ?>
          <div class="col-sm-6 col-md-12">
            <div class="form-group">
              <label>Empresa <span class="label-advertencia">*</span></label>
              <select id="cbo-Empresas" name="ID_Empresa" class="form-control select2 required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-sm-6 col-md-7">
            <div class="form-group">
              <label>Organización <span class="label-advertencia">*</span></label>
              <select id="cbo-Organizaciones" name="ID_Organizacion" class="form-control select2 required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          <?php } else { ?>
    	      <input type="hidden" id="cbo-Empresas" name="ID_Empresa" class="form-control" value="<?php echo $this->user->ID_Empresa; ?>">
            <input type="hidden" id="cbo-Organizaciones" name="ID_Organizacion" class="form-control" value="<?php echo $this->user->ID_Organizacion; ?>">
          <?php } ?>
          
          <div class="col-sm-3 col-xs-6 col-md-2">
            <div class="form-group">
              <label># Caja <span class="label-advertencia">*</span></label>
              <input type="tel" name="Nu_Pos" class="form-control required input-number" autocomplete="off" maxlength="3">
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-6 col-sm-4 col-md-3">
            <div class="form-group">
              <label>Estado <span class="label-advertencia">*</span></label>
		  				<select id="cbo-Estado" name="Nu_Estado" class="form-control required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-6 col-sm-12 col-md-12 hidden">
            <div class="form-group">
              <label>Ip (opcional)</label>
              <div class="input-group">
                <span class="input-group-addon"><i class="blue fa fa-key" aria-hidden="true"></i></span>
                <input type="password" name="Txt_Autorizacion_Venta_Serie_Disco_Duro" id="password-serie_hdd" placeholder="Ingresar Key serie HDD (opcional)" class="form-control pwd" autocomplete="off">
                <span toggle="#password-serie_hdd" class="fa fa-fw fa-eye field-icon toggle-password"></span>
              </div>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-6 col-sm-12 col-md-12 hidden">
            <div class="form-group">
              <label>Key Ip (opcional)</label>
              <div class="input-group">
                <span class="input-group-addon"><i class="blue fa fa-key" aria-hidden="true"></i></span>
                <input type="password" name="Key_Serie_Disco_Duro" id="password-key_serie_hdd" placeholder="Ingresar Key serie HDD (opcional)" class="form-control pwd_key_hdd" autocomplete="off">
                <span toggle="#password-key_serie_hdd" class="fa fa-fw fa-eye field-icon toggle-password_key_hdd"></span>
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