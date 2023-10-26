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
          <div class="box-header box-header-new div-Listar">
            <div class="row div-Filtros">
              <br>
              <div class="col-xs-4 col-sm-3 col-md-2">
                <label>F. Inicio</label>
                <div class="form-group">
                  <div class="input-group date" style="width:100%">
                    <input type="text" id="txt-Filtro_Fe_Inicio" class="form-control date-picker-report" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                  </div>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-4 col-sm-3 col-md-2">
                <label>F. Fin</label>
                <div class="form-group">
                  <div class="input-group date" style="width:100%">
                    <input type="text" id="txt-Filtro_Fe_Fin" class="form-control date-picker-invoice txt-Filtro_Fe_Fin" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                  </div>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
          
              <div class="col-sm-3 col-xs-6 col-md-4">
                <label>Alumno</label>
                <div class="form-group">
                  <select id="cbo-alumno" name="ID_Entidad_Alumno" class="form-control select2 required" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-6 col-md-2">
                <label class="hidden-xs">&nbsp;</label>
                <div class="form-group">
                  <button type="button" id="btn-filter" class="btn btn-primary btn-block"><i class="fa fa-search"></i> Buscar</button>
                </div>
              </div>

              <div class="col-xs-6 col-sm-3 col-md-2">
                <label class="hidden-xs">&nbsp;</label>
                <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Agregar == 1) : ?>
                  <button type="button" class="btn btn-success btn-block" onclick="agregarPos()">Agregar</button>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="table-responsive div-Listar">
            <table id="table-Pos" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th>Empresa</th>
                  <th>Escuela</th>
                  <th>Salon</th>
                  <th>Profesor</th>
                  <th>F. Asistencia</th>
                  <th>Alumno</th>
                  <th class="no-sort">Asistio</th>
                  <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Eliminar == 1) : ?>
                    <th class="no-sort">Eliminar</th>
                  <?php endif; ?>
                </tr>
              </thead>
            </table>
          </div>

          <!-- Modal -->
          <div class="box-body div-AgregarEditar">
          <?php
          $attributes = array('id' => 'form-Pos');
          echo form_open('', $attributes);
          ?>
            <input type="hidden" name="EID_Control_Asistencia_Alumno" class="form-control">
            
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
              
              <div class="col-sm-3 col-xs-6 col-md-3">
                <label>Sede</label>
                <div class="form-group">
                  <select id="cbo-sede_musica" name="ID_Sede_Musica" class="form-control select2 required" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-sm-3 col-xs-6 col-md-3">
                <label>Salon</label>
                <div class="form-group">
                  <select id="cbo-salon" name="ID_Salon" class="form-control select2 required" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
                  
              <div class="col-sm-3 col-xs-6 col-md-4">
                <label>Profesor</label>
                <div class="form-group">
                  <select id="cbo-profesor" name="ID_Entidad_Profesor" class="form-control select2 required" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-sm-3 col-xs-6 col-md-2">
                <label>F. Asistencia</label>
                <div class="form-group">
                  <div class="input-group date">
                    <input type="text" id="txt-Fe_Asistencia" name="Fe_Asistencia" class="form-control date-picker-invoice required" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                  </div>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-12">
                <div class="table-responsive div-alumno_matriculados">
                  <table id="table-alumno_matriculados" class="table table-striped table-bordered">
                    <thead>
                      <tr>
                        <th style='display:none;' class="text-left">ID</th>
                        <th class="text-left">Nombre</th>
                        <th class="text-center">Asistio</th>
                        <th class="text-left">Nota</th>
                      </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                </div>
              </div>

            </div>
            
            <div class="row">
              <br/>
              <div class="col-xs-6 col-md-6">
                <div class="form-group">
                  <button type="button" id="btn-cancelar" class="btn btn-danger btn-md btn-block"><span class="fa fa-close"></span> Cancelar (ESC)</button>
                </div>
              </div>
              <div class="col-xs-6 col-md-6">
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