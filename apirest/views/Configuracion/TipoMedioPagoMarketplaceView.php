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
              <?php
              if ( $this->user->No_Usuario == 'root' ){ ?>
              <div class="col-md-12">
                <label>Empresa</label>
                <div class="form-group">
                  <select id="cbo-filtro_empresa" name="ID_Empresa" class="form-control select2 required" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              <?php } else { ?>
                <input type="hidden" id="cbo-filtro_empresa" name="ID_Empresa" class="form-control" value="<?php echo $this->user->ID_Empresa; ?>">
              <?php } ?>

              <div class="col-md-3">
                <div class="form-group">
    		  				<select id="cbo-Filtros_TipoMedioPagoMarketplace" name="Filtros_TipoMedioPagoMarketplace" class="form-control">
    		  				  <option value="TipoMedioPagoMarketplace">Nombre Tipo Medio Pago</option>
                    <option value="MedioPago">Nombre Medio Pago</option>
    		  				</select>
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="form-group">
                  <input type="text" id="txt-Global_Filter" name="Global_Filter" class="form-control" placeholder="Buscar" value="" autocomplete="off" maxlength="30">
                </div>
              </div>
              
              <div class="col-md-3">
                <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Agregar == 1) : ?>
                <button type="button" class="btn btn-success btn-block" onclick="agregarTipoMedioPagoMarketplace()"><i class="fa fa-plus-circle"></i> Agregar</button>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="table-responsive">
            <table id="table-TipoMedioPagoMarketplace" class="table table-striped table-bordered">
              <thead>
              <tr>
                <?php if ( $this->user->No_Usuario == 'root' ){ ?>
                <th>Empresa</th>
                <?php } ?>
                <th>Medio Pago</th>
                <th>Tipo Medio Pago</th>
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
  $attributes = array('id' => 'form-TipoMedioPagoMarketplace');
  echo form_open('', $attributes);
  ?>
  <div class="modal fade" id="modal-TipoMedioPagoMarketplace" role="dialog">
  <div class="modal-dialog">
  	<div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title text-center"></h4>
      </div>
      
    	<div class="modal-body">
        <input type="hidden" name="EID_Medio_Pago_Marketplace" class="form-control">
    	  <input type="hidden" name="EID_Tipo_Medio_Pago_Marketplace" class="form-control">
    	  <input type="hidden" name="ENo_Tipo_Medio_Pago_Marketplace" class="form-control">
        <input type="hidden" id="hidden-nombre_imagen_tipo_medio_pago_marketplace" name="No_Imagen_Tipo_Medio_Pago_Marketplace" class="form-control" value="">
        <input type="hidden" id="hidden-nombre_imagen_url_tipo_medio_pago_marketplace" name="No_Imagen_Url_Tipo_Medio_Pago_Marketplace" class="form-control" value="">
        
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
          
          <div class="col-xs-12 col-md-3">
            <div class="form-group">
              <label>Medio Pago <span class="label-advertencia">*</span></label>
		  				<select id="cbo-medio_pago" name="ID_Medio_Pago_Marketplace" class="form-control required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-4 col-md-5">
            <div class="form-group">
              <label>Nombre <span class="label-advertencia">*</span></label>
              <input type="text" id="txt-No_Tipo_Medio_Pago_Marketplace" name="No_Tipo_Medio_Pago_Marketplace" placeholder="Ingresar nombre" class="form-control required" autocomplete="off" maxlength="30">
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-12 col-md-3 div-Estado">
            <div class="form-group">
              <label>Estado <span class="label-advertencia">*</span></label>
		  				<select id="cbo-Estado" name="Nu_Estado" class="form-control required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>
      </div>
      
      <div class="row"><br>
        <div class="col-xs-6 col-md-6">
          <div class="well well-sm">
            <i class="fa fa-warning"></i> Indicaciones:
            <br>- Imagen: <b>opcional</b>
            <br>- Formato: <b>PNG</b>
            <br>- Fondo de imágen(es): <b>SIN FONDO / BLANCO / TRANSPARENTE</b>
            <br>- Peso Máximo: <b>1024 KB</b>
          </div>
        </div>
        
        <div class="col-xs-6 col-md-6 text-center divDropzone"></div>
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