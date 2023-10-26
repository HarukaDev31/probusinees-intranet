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
            &nbsp;<a href="<?php echo $this->MenuModel->verificarAccesoMenuCRUD()->Txt_Url_Video; ?>" target="_blank"><i class="fa fa-youtube-play red" aria-hidden="true" title="Video <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?>" alt="Video <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?>"></i> <span style="color: #7b7b7b; font-size: 14px;">ver video<span></a>
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
              <div class="col-xs-12 col-sm-4 col-md-6">
                <div class="form-group">
                  <label>Empresa</label>
                  <select id="cbo-filtro_empresa" name="ID_Empresa" class="form-control select2" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              <div class="col-xs-12 col-sm-4 col-md-6">
                <div class="form-group">
                  <label>Organización</label>
                  <select id="cbo-filtro_organizacion" name="ID_Organizacion" class="form-control select2" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              <?php } else { ?>
                <input type="hidden" id="cbo-filtro_empresa" name="ID_Empresa" class="form-control" value="<?php echo $this->user->ID_Empresa; ?>">
                <input type="hidden" id="cbo-filtro_organizacion" name="ID_Organizacion" class="form-control" value="<?php echo $this->user->ID_Organizacion; ?>">
              <?php } ?>
              
              <div class="col-xs-12 col-sm-4 col-md-4">
                <div class="form-group">
                  <label>Almacén</label>
                  <select id="cbo-filtro_almacen" name="ID_Almacen" class="form-control select2" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-3 col-md-2">
                <div class="form-group">
                  <label>Documento</label>
    		  				<select id="cbo-Filtro_TiposDocumento" class="form-control"></select>
                </div>
              </div>
              
              <div class="col-xs-6 col-sm-3 col-md-2">
                <div class="form-group">
                  <label>Serie</label>
                  <select id="cbo-Filtro_SeriesDocumento" class="form-control"></select>
                </div>
              </div>

              <div class="col-xs-6 col-sm-3 col-md-2">
                <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Consultar == 1) : ?>
                  <label>&nbsp;</label>
                  <button type="button" id="btn-filter" class="btn btn-primary btn-block"><i class="fa fa-search"></i> Buscar</button>
                <?php endif; ?>
                </h3>
              </div>

              <div class="col-xs-6 col-sm-3 col-md-2">
                <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Agregar == 1) : ?>
                  <label>&nbsp;</label>
                  <button type="button" class="btn btn-success btn-block" onclick="agregarSerie()"><i class="fa fa-plus-circle"></i> Agregar</button>
                <?php endif; ?>
                </h3>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="table-responsive">
            <table id="table-Serie" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <?php if ( $this->user->No_Usuario == 'root' ){ ?>
                  <th>Empresa</th>
                  <th>Organización</th>
                  <?php } ?>
                  <th>Almacén</th>
                  <th>Tipo</th>
                  <th>Serie</th>
                  <th>Número Actual</th>
                  <th>Nro. Caja</th>
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
  <!-- Modal -->
  <?php
  $attributes = array('id' => 'form-Serie');
  echo form_open('', $attributes);
  ?>
  <div class="modal fade" id="modal-Serie" role="dialog">
  <div class="modal-dialog">
  	<div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title text-center"></h4>
      </div>
      
    	<div class="modal-body">
    	  <input type="hidden" name="EID_Empresa" class="form-control required">
    	  <input type="hidden" name="EID_Organizacion" class="form-control required">
    	  <input type="hidden" name="EID_Almacen" class="form-control required">
    	  <input type="hidden" name="EID_Tipo_Documento" class="form-control required">
    	  <input type="hidden" name="EID_Serie_Documento" class="form-control required">
    	  <input type="hidden" name="EID_Serie_Documento_PK" class="form-control required">
    	  
			  <div class="row">
          <?php
          if ( $this->user->No_Usuario == 'root' ){ ?>
          <div class="col-sm-6 col-md-6">
            <div class="form-group">
              <label>Empresa <span class="label-advertencia">*</span></label>
              <select id="cbo-Empresas" name="ID_Empresa" class="form-control select2 required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-sm-6 col-md-6">
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
          
          <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="form-group">
              <label>Almacén <span class="label-advertencia">*</span></label>
              <select id="cbo-almacen_serie" name="ID_Almacen" class="form-control select2 required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
              
          <div class="col-xs-4 col-sm-3 col-md-3">
            <div class="form-group">
              <label>Tipo <span class="label-advertencia">*</span></label>
		  				<select id="cbo-TiposDocumento" name="ID_Tipo_Documento" class="form-control required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-4 col-xs-6 col-sm-3 col-md-3">
            <div class="form-group">
              <label>Serie <span class="label-advertencia">*</span></label>
              <input type="text" id="txt-ID_Serie_Documento" name="ID_Serie_Documento" placeholder="" class="form-control input-codigo_barra input-Mayuscula required" autocomplete="off" maxlength="4" minlength="4">
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-4 col-sm-3 col-xs-6 col-md-3">
            <div class="form-group">
              <label>Correlativo <span class="label-advertencia">*</span></label>
              <input type="tel" name="Nu_Numero_Documento" placeholder="" class="form-control required input-number" autocomplete="off" minlength="1" maxlength="8" value="1">
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-4 col-sm-3 col-xs-6 col-md-3 hidden">
            <div class="form-group">
              <label># Caracteres <span class="label-advertencia">*</span></label>
              <input type="tel" name="Nu_Cantidad_Caracteres" class="form-control required input-number" autocomplete="off" minlength="1" minlength="8" value="6">
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-6 col-sm-3 col-xs-6 col-md-3">
            <div class="form-group">
              <label>Caja </label> (opcional)
              <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Si usará Punto de Venta es obligatorio">
                <i class="fa fa-info-circle"></i>
              </span>
		  				<select id="cbo-pos" name="ID_POS" class="form-control" style="width: 100%;" data-placement="bottom" title=""></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-6 col-xs-6 col-sm-4 col-md-3 div-Estado">
            <div class="form-group">
              <label>Estado <span class="label-advertencia">*</span></label>
		  				<select id="cbo-Estado" name="Nu_Estado" class="form-control required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <label>¿Utilizar misma serie?</label>
            <div class="form-group">
              <label style="cursor: pointer;">
                <input type="radio" name="radio-addSerieIgual" class="flat-red" id="radio-InactiveSerieIgual" value="0" checked> No
              </label>
              <label style="cursor: pointer;">
                &nbsp;<input type="radio" name="radio-addSerieIgual" class="flat-red" id="radio-ActiveSerieIgual" value="1"> Si
              </label>
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