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
              <div class="col-md-3">
                <div class="form-group">
    		  				<select id="cbo-Filtros" name="Filtros" class="form-control">
    		  				  <option value="Nombre">Nombre</option>
    		  				</select>
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="form-group">
                  <input type="text" id="txt-Global_Filter" name="Global_Filter" class="form-control" maxlength="100" placeholder="Buscar" value="" autocomplete="off">
                </div>
              </div>

              <div class="col-xs-6 col-sm-3 col-md-2">
                <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Agregar == 1) : ?>
                  <button type="button" class="btn btn-success btn-block" onclick="agregarPos()"><i class="fa fa-plus-circle"></i> Agregar</button>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="table-responsive">
            <table id="table-Pos" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th>Empresa</th>
                  <th>Sede</th>
                  <th>Día</th>
                  <th>Hora Inicio</th>
                  <th>Minuto Inicio</th>
                  <th>Hora Fin</th>
                  <th>Minuto Fin</th>
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
    	  <input type="hidden" name="EID_Horario_Clase" class="form-control">
    	  <input type="hidden" name="EID_Sede_Musica" class="form-control">
    	  <input type="hidden" name="EID_Dia_Semana" class="form-control">
    	  
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
          <?php } else { ?>
    	      <input type="hidden" id="cbo-Empresas" name="ID_Empresa" class="form-control" value="<?php echo $this->user->ID_Empresa; ?>">
          <?php } ?>
          
          <div class="col-sm-3 col-xs-6 col-md-6">
            <div class="form-group">
              <label>Sede</label>
              <select id="cbo-sede_musica" name="ID_Sede_Musica" class="form-control select2 required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-sm-3 col-xs-6 col-md-6">
            <div class="form-group">
              <label>Día</label>
              <select id="cbo-dia_semana" name="ID_Dia_Semana" class="form-control select2 required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-3 col-sm-2 col-md-3">
            <div class="form-group">
              <label>Hora Ini<span class="label-advertencia">*</span></label>
		  				<select id="cbo-hora_ini" name="Nu_Hora_Desde" class="form-control required select2" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-3 col-sm-2 col-md-3">
            <div class="form-group">
              <label>Minuto Ini<span class="label-advertencia">*</span></label>
		  				<select id="cbo-minuto_ini" name="Nu_Minuto_Desde" class="form-control required select2" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-3 col-sm-2 col-md-3">
            <div class="form-group">
              <label>Hora Fin<span class="label-advertencia">*</span></label>
		  				<select id="cbo-hora_fin" name="Nu_Hora_Hasta" class="form-control required select2" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-3 col-sm-2 col-md-3">
            <div class="form-group">
              <label>Minuto Fin<span class="label-advertencia">*</span></label>
		  				<select id="cbo-minuto_fin" name="Nu_Minuto_Hasta" class="form-control required select2" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-6 col-sm-4 col-md-12">
            <div class="form-group">
              <label>Estado <span class="label-advertencia">*</span></label>
		  				<select id="cbo-Estado" name="Nu_Estado" class="form-control required" style="width: 100%;"></select>
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