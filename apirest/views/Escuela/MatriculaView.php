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

              <div class="col-xs-6 col-sm-6 col-md-2">
                <label>&nbsp;</label>
                <div class="form-group">
                  <button type="button" id="btn-filter" class="btn btn-primary btn-block"><i class="fa fa-search"></i> Buscar</button>
                </div>
              </div>

              <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Agregar == 1) : ?>
              <div class="col-xs-6 col-sm-3 col-md-2 hidden">
                <label>&nbsp;</label>
                <div class="form-group">
                  <button type="button" class="btn btn-success btn-block" onclick="agregarPos()"><i class="fa fa-plus-circle"></i> Agregar</button>
                </div>
              </div>
              <div class="col-xs-6 col-sm-3 col-md-2">
                <label>&nbsp;</label>
                <div class="form-group">
                  <button type="button" class="btn btn-success btn-block" onclick="agregarMultiplePos()"><i class="fa fa-plus-circle"></i> Agregar</button>
                </div>
              </div>
              <?php endif; ?>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="table-responsive div-Listar">
            <table id="table-Pos" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th>Empresa</th>
                  <th>F. Matricula</th>
                  <th>Sede</th>
                  <th>Salon</th>
                  <th>Día</th>
                  <th>Horario</th>
                  <th>Profesor</th>
                  <th>Alumno</th>
                  <th>Categoría</th>
                  <th>Grupo</th>
                  <th>Tipo</th>
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
          
          <!-- FORMULARIO -->
          <div class="box-body div-AgregarEditar">
          <?php
          $attributes = array('id' => 'form-Pos_Multiple');
          echo form_open('', $attributes);
          ?>
            <input type="hidden" name="EID_Matricula_Alumno" class="form-control">
            
            <div class="row">
              <?php
              if ( $this->user->No_Usuario == 'root' ){ ?>
              <div class="col-sm-6 col-md-12">
                <div class="form-group">
                  <label>Empresa <span class="label-advertencia">*</span></label>
                  <select id="cbo-Empresas2" name="ID_Empresa2" class="form-control select2 required" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              <?php } else { ?>
                <input type="hidden" id="cbo-Empresas2" name="ID_Empresa2" class="form-control" value="<?php echo $this->user->ID_Empresa; ?>">
              <?php } ?>

              <div class="col-sm-3 col-xs-6 col-md-3">
                <label>Sede</label>
                <div class="form-group">
                  <select id="cbo-sede_musica2" name="ID_Sede_Musica2" class="form-control select2 required" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-sm-3 col-xs-6 col-md-3">
                <label>Salon</label>
                <div class="form-group">
                  <select id="cbo-salon2" name="ID_Salon2" class="form-control select2 required" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-sm-3 col-xs-6 col-md-4">
                <label>Profesor</label>
                <div class="form-group">
                  <select id="cbo-profesor2" name="ID_Entidad_Profesor2" class="form-control select2 required" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-sm-3 col-xs-6 col-md-4">
                <label>Categoria</label>
                <div class="form-group">
                  <select id="cbo-categoria2" name="ID_Familia2" class="form-control select2 required" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-sm-3 col-xs-6 col-md-2">
                <label>Grupo</label>
                <div class="form-group">
                  <select id="cbo-tipos_grupo_clase2" name="ID_Grupo_Clase2" class="form-control select2 required" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-sm-3 col-xs-6 col-md-2">
                <label>Clase</label>
                <div class="form-group">
                  <select id="cbo-tipos_clase2" name="ID_Tipo_Clase2" class="form-control select2 required" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-sm-3 col-xs-6 col-md-2">
                <label>F. Matrícula</label>
                <div class="form-group">
                  <div class="input-group date">
                    <input type="text" id="txt-Fe_Matricula2" name="Fe_Matricula2" class="form-control date-picker-invoice required" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                  </div>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-4 col-md-2">
                <label>Estado</label>
                <div class="form-group">
                  <select id="cbo-Estado2" name="Nu_Estado2" class="form-control required" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-6 col-sm-5 col-md-6">
                <label>Horario Clase</label>
                <div class="form-group">
                  <select id="cbo-horario_clase2" name="ID_Horario_Clase2" class="form-control select2" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-6 col-sm-4 col-md-4">
                <label>Alumno</label>
                <div class="form-group">
                  <select id="cbo-alumno2" name="ID_Entidad_Alumno2" class="form-control select2" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-12 col-sm-3 col-md-2">
                <label>&nbsp;</label>
                <div class="form-group">
                  <button type="button" id="btn-agregarAlumnoHorario" class="btn btn-success btn-block">Agregar</button>
                </div>
              </div>
              
              <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="table-responsive div-alumno_horarios">
                  <table id="table-alumno_horarios" class="table table-striped table-bordered">
                    <thead>
                      <tr>
                        <th style='display:none;' class="text-left">ID</th>
                        <th style='display:none;' class="text-left">ID Horario</th>
                        <th style='display:none;' class="text-left">ID Alumno</th>
                        <th class="text-left">Horario</th>
                        <th class="text-left">Alumno</th>
                        <th class="text-center">Eliminar</th>
                      </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                </div>
              </div>


              <div class="col-xs-12 col-sm-12 col-md-12">
                <label>Glosa</label>
                <div class="form-group">
                  <textarea name="Txt_Glosa2" rows="1" placeholder="Opcional" class="form-control"></textarea>
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
                  <button type="submit" id="btn-save2" class="btn btn-success btn-md btn-block btn-verificar"><i class="fa fa-save"></i> Guardar (ENTER)</button>
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
  <!-- Modal -->
  <?php
  $attributes = array('id' => 'form-Pos');
  echo form_open('', $attributes);
  ?>
  <div class="modal fade" id="modal-Pos" role="dialog">
  <div class="modal-dialog modal-lg">
  	<div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title text-center"></h4>
      </div>
      
    	<div class="modal-body">
    	  <input type="hidden" name="EID_Matricula_Alumno" class="form-control">
    	  <input type="hidden" name="EID_Horario_Clase" class="form-control">
    	  <input type="hidden" name="EID_Entidad_Alumno" class="form-control">
    	  
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
          
          <div class="col-sm-3 col-xs-6 col-md-6">
            <label>Horario Clase</label>
            <div class="form-group">
              <select id="cbo-horario_clase" name="ID_Horario_Clase" class="form-control select2 required" style="width: 100%;"></select>
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
          
          <div class="col-sm-3 col-xs-6 col-md-4">
            <label>Alumno</label>
            <div class="form-group">
              <select id="cbo-alumno" name="ID_Entidad_Alumno" class="form-control select2 required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-sm-3 col-xs-6 col-md-4">
            <label>Categoria</label>
            <div class="form-group">
              <select id="cbo-categoria" name="ID_Familia" class="form-control select2 required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-sm-3 col-xs-6 col-md-2">
            <label>Grupo</label>
            <div class="form-group">
              <select id="cbo-tipos_grupo_clase" name="ID_Grupo_Clase" class="form-control select2 required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-sm-3 col-xs-6 col-md-2">
            <label>Clase</label>
            <div class="form-group">
              <select id="cbo-tipos_clase" name="ID_Tipo_Clase" class="form-control select2 required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-sm-3 col-xs-6 col-md-2">
            <label>F. Matrícula</label>
            <div class="form-group">
              <div class="input-group date">
                <input type="text" id="txt-Fe_Matricula" name="Fe_Matricula" class="form-control date-picker-invoice required" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
              </div>
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-6 col-sm-4 col-md-2">
            <label>Estado</label>
            <div class="form-group">
		  				<select id="cbo-Estado" name="Nu_Estado" class="form-control required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-12 col-sm-12 col-md-12">
            <label>Glosa</label>
            <div class="form-group">
              <textarea name="Txt_Glosa" rows="1" placeholder="Opcional" class="form-control"></textarea>
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
              <button type="submit" id="btn-save2" class="btn btn-success btn-md btn-block btn-verificar"><i class="fa fa-save"></i> Guardar </button>
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