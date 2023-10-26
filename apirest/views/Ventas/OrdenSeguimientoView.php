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
              <div class="col-xs-12 col-sm-4 col-md-2">
                <div class="form-group">
                  <label>Almacén</label>
                  <select id="cbo-filtro_almacen" name="ID_Almacen" class="form-control select2" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-md-2">
                <div class="form-group">
                  <label>F. Inicio</label>
                  <div class="input-group date">
                    <input type="text" id="txt-Filtro_Fe_Inicio" class="form-control date-picker-report" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                  </div>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-md-2">
                <div class="form-group">
                  <label>F. Fin</label>
                  <div class="input-group date">
                    <input type="text" id="txt-Filtro_Fe_Fin" class="form-control date-picker-invoice txt-Filtro_Fe_Fin" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                  </div>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-md-4">
                <div class="form-group">
                  <label>Nombre Contacto</label>
                  <input type="text" id="txt-Filtro_Contacto" class="form-control autocompletar_contacto" placeholder="Ingresar nombre" maxlength="50" autocomplete="off">
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-6 col-md-2">
                <div class="form-group">
                  <label>Número</label>
                  <input type="tel" id="txt-Filtro_NumeroDocumento" class="form-control input-number" maxlength="20" placeholder="Buscar" value="" autocomplete="off">
                </div>
              </div>
            </div>
              
            <div class="row div-Filtros">
              <div class="col-md-8">
                <div class="form-group">
                  <label>Nombre Cliente</label>
                  <input type="text" id="txt-Filtro_Entidad" class="form-control autocompletar" data-global-class_method="AutocompleteController/getAllClient" data-global-table="entidad" placeholder="Ingresar nombre" value="" autocomplete="off">
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-6 col-md-2">
                <h3>
                <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Agregar == 1) : ?>
                  <button type="button" id="btn-filter" class="btn btn-primary btn-block"><i class="fa fa-search"></i> Buscar</button>
                <?php endif; ?>
                </h3>
              </div>

              <div class="col-xs-6 col-md-2">
                <h3>
                <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Agregar == 1) : ?>
                  <button type="button" class="btn btn-success btn-block" onclick="agregarOrdenSeguimiento()"><i class="fa fa-plus-circle"></i> Agregar</button>
                <?php endif; ?>
                </h3>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="table-responsive">
            <table id="table-OrdenSeguimiento" class="table table-striped table-bordered">
              <thead>
              <tr>
                <th>Almacén</th>
                <th>F. Registro</th>
                <th class="no-sort">Tipo</th>
                <th>Orden</th>
                <th class="no-sort_left">Cliente</th>
                <th class="no-sort_left">Contacto</th>
                <th class="no-sort_left">Observación</th>
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
  $attributes = array('id' => 'form-OrdenSeguimiento');
  echo form_open('', $attributes);
  ?>
  <div class="modal fade" id="modal-OrdenSeguimiento" role="dialog">
  <div class="modal-dialog">
  	<div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title text-center"></h4>
      </div>
      
    	<div class="modal-body">
    	  <input type="hidden" name="EID_Orden_Seguimiento" class="form-control">
    	  
			  <div class="row">
          <div class="col-xs-7 col-sm-4 col-md-4">
            <div class="form-group">
              <label>Tipo <span class="label-advertencia">*</span></label>
		  				<select id="cbo-tipos_orden_seguimiento" name="ID_Tipo_Orden_Seguimiento" class="form-control required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-5 col-sm-3 col-sm-12 col-md-3">
            <div class="form-group">
              <label>F. Registro <span class="label-advertencia">*</span></label>
              <div class="input-group date">
                <input type="text" id="txt-Fe_Registro" name="Fe_Registro" class="form-control date-picker-invoice required" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
              </div>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-3 col-sm-2 col-md-2">
            <div class="form-group">
              <label>Hora <span class="label-advertencia">*</span></label>
		  				<select id="cbo-hora" name="ID_Hora" class="form-control required select2" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-3 col-sm-2 col-md-2">
            <div class="form-group">
              <label>Minuto <span class="label-advertencia">*</span></label>
		  				<select id="cbo-minuto" name="ID_Minuto" class="form-control required select2" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-6 col-sm-4 col-md-4">
            <label># Orden <span class="label-advertencia">*</span></label>
            <div class="form-group">
              <input type="tel" id="txt-ID_Documento_Cabecera" name="ID_Documento_Cabecera" placeholder="Ingresar Num. de orden" class="form-control autocompletar_orden required input-number" autocomplete="off" maxlength="20">
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <!-- Contacto Existente -->
          <div class="col-xs-6 col-sm-4 col-md-4 text-center">
            <label>Contacto <span class="label-advertencia">*</span></label>
            <div class="form-group">
              <label><input type="radio" id="radio-Nu_Tipo_Contacto_Existente" name="Nu_Tipo_Contacto" id="radio-contacto_existente" onclick="addContacto(0);" value="0"> Existente</label>
            </div>
          </div>
          
          <div class="col-xs-6 col-sm-4 col-md-4 text-center">
            <label>Contacto <span class="label-advertencia">*</span></label>
            <div class="form-group">
              <label><input type="radio" id="radio-Nu_Tipo_Contacto_Nuevo" name="Nu_Tipo_Contacto" id="radio-contacto_nuevo" onclick="addContacto(1);" value="1"> Nuevo</label>
            </div>
          </div>
        </div>
          
			  <div class="row">
          <div class="col-xs-12 div-contacto_existente">
            <label id="label-No_Contacto">Nombre(s) y Apellidos</label><span class="label-advertencia"> *</span>
            <div class="form-group">
              <input type="text" id="txt-No_Contacto_existe" class="form-control" placeholder="Ingresar nombre" maxlength="50" autocomplete="off" disabled>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-6 col-sm-5 col-md-5 div-contacto_nuevo">
            <div class="form-group">
              <label>Tipo Doc. Identidad</label>
		  				<select id="cbo-tipos_documento_identidad_contacto" name="ID_Tipo_Documento_Identidad" class="form-control" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-6 col-sm-4 col-md-4 div-contacto_nuevo">
            <div class="form-group">
              <label id="label-Nombre_Documento_Identidad">DNI</label>
              <input type="tel" id="txt-Nu_Documento_Identidad" name="Nu_Documento_Identidad" class="form-control input-number" placeholder="Ingresar número" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-12 col-sm-3 col-md-2 text-center div-contacto_nuevo">
            <label>Api</label>
            <div class="form-group">
              <button type="button" id="btn-cloud-api_orden_seguimiento_contacto" class="btn btn-success btn-block btn-md"><i class="fa fa-cloud-download fa-lg"></i></button>
            </div>
          </div>
        </div>
      
			  <div class="row">
          <div class="col-xs-12 col-sm-6 col-md-6 div-contacto_nuevo">
            <div class="form-group">
              <label id="label-No_Contacto">Nombre(s) y Apellidos</label><span class="label-advertencia"> *</span>
              <input type="text" id="txt-No_Contacto" name="No_Contacto" class="form-control required" placeholder="Ingresar nombre" maxlength="50" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-12 col-sm-6 col-md-6 div-contacto_nuevo">
            <label>Correo <span class="label-advertencia">*</span></label>
            <div class="form-group">
              <input type="text" id="txt-Txt_Email_Contacto" name="Txt_Email_Contacto" placeholder="Ingresar correo" class="form-control" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>
    
			  <div class="row">
          <div class="col-xs-5 col-sm-3 col-md-3 div-contacto_nuevo">
            <label>Celular <span class="label-advertencia">*</span></label>
            <div class="form-group">
              <input type="tel" id="txt-Nu_Celular_Contacto" name="Nu_Celular_Contacto" class="form-control" data-inputmask="'mask': ['999 999 999']" data-mask autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>
    
          <div class="col-xs-4 col-sm-3 col-md-3 div-contacto_nuevo">
          <label>Teléfono</label>
            <div class="form-group">
              <input type="tel" id="txt-Nu_Telefono_Contacto" name="Nu_Telefono_Contacto" class="form-control" data-inputmask="'mask': ['999 9999']" data-mask autocomplete="off">
            </div>
            <span class="help-block" id="error"></span>
          </div>
        </div>
        
			  <div class="row">
          <div class="col-md-12">
            <label>Observación <span class="label-advertencia">*</span></label>
            <div class="form-group">
              <textarea name="Txt_Observacion" class="form-control" placeholder="Ingresar observación" value="" autocomplete="off"></textarea>
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div>
      </div>
      
    	<div class="modal-footer">
			  <div class="row">
          <div class="col-xs-6 col-md-6">
            <div class="form-group">
              <button type="button" class="btn btn-danger btn-md btn-block" data-dismiss="modal"><span class="fa fa-sign-out"></span> Salir (ESC)</button>
            </div>
          </div>
          <div class="col-xs-6 col-md-6">
            <div class="form-group">
              <button type="submit" id="btn-save" class="btn btn-success btn-md btn-block btn-verificar"><i class="fa fa-save"></i> Guardar (ENTER)</button>
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