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
          	  <input type="hidden" id="hidden-sMethod" name="sMethod" class="form-control" value="<?php echo $this->router->method; ?>">
              <br>
              <?php
              if ( $this->user->No_Usuario == 'root' ){ ?>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-2">
                <div class="form-group">
                  <label>Empresa</label>
                  <select id="cbo-filtro_empresa" name="ID_Empresa" class="form-control select2" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              <div class="col-xs-12 col-sm-4 col-md-6 col-lg-6 hidden">
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
                            
              <div class="col-xs-12 col-sm-4 col-md-3 col-lg-2">
                <div class="form-group">
                  <label>Sistema</label>
                  <select id="cbo-filtro-tipo_sistema" name="Nu_Tipo_Sistema" class="form-control" style="width: 100%;">
    		  				  <option value="" selected="selected">Todos</option>
    		  				  <option value="2">SUNAT</option>
                    <option value="1">PSE</option>
                  </select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-12 col-sm-4 col-md-3 col-lg-2">
                <label>Documento</label>
                <div class="form-group">
                  <select id="cbo-filtro-tipo_documento" name="tipo_documento" class="form-control hotkey-cobrar_cliente">
                    <option value="0">- Todos -</option>
                    <option value="4">Boleta</option>
                    <option value="3">Factura</option>
                  </select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-12 col-sm-4 col-md-3 col-lg-2 hidden">
                <div class="form-group">
                  <label>Estado Sistema</label>
                  <select id="cbo-filtro-estado_sistema" name="Nu_Estado_Sistema" class="form-control" style="width: 100%;">
    		  				  <option value="-" selected="selected">Todos</option>
    		  				  <option value="0">Demostración</option>
                    <option value="1">Producción</option>
                  </select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-4 col-md-3 col-lg-2">
                <div class="form-group">
                  <label>F. Inicio</label>
                  <div class="input-group date">
                    <!--<input type="text" id="txt-Filtro_Fe_Inicio" class="form-control date-picker-report" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>-->
                    <input type="text" id="txt-Filtro_Fe_Inicio" class="form-control date-picker-report_crud" value="<?php echo dateNow('month_date_report_crud'); ?>" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                  </div>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-4 col-md-3 col-lg-2">
                <div class="form-group">
                  <label>F. Fin</label>
                  <div class="input-group date">
                    <!--<input type="text" id="txt-Filtro_Fe_Fin" class="form-control date-picker-invoice txt-Filtro_Fe_Fin" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>-->
                    <input type="text" id="txt-Filtro_Fe_Fin" class="form-control date-picker-report_crud txt-Filtro_Fe_Fin" value="<?php echo dateNow('month_date_report_crud'); ?>" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                  </div>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-12 col-sm-12 col-md-3 col-lg-2">
                <label class="hidden-xs hidden-sm">&nbsp;</label>
                <button type="button" id="btn-filter" class="btn btn-primary btn-block"><i class="fa fa-search"></i> Buscar</button>
              </div>
              
              <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="alert alert-warning">
                  <strong>Advertencia:</strong><br>
                  - Boletas / N/C / N/D: Si permanecen más de 5 días, SUNAT rechazará el documento.<br>
                  - Facturas: Si permanecen más de 3 días, SUNAT rechazará el documento.<br>
                  - Anulacion / Baja: Solo pueden enviar hasta 5 días y se podrá usar también para ANULAR venta por RECHAZO de SUNAT.<br>
                </div>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="table-responsive">
            <table id="table-MonitoreoDocumentosElectronicos" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th class="no-sort">Sistema</th>
                  <th class="no-sort_left">Empresa</th>
                  <th class="no-sort_left">Organización</th>
                  <th class="no-sort">F. Emisión</th>
                  <th class="no-sort">Tipo</th>
                  <th class="no-sort">Serie</th>
                  <th class="no-sort">Número</th>
                  <th class="no-sort">Estado</th>
                  <th class="no-sort">Vence</th>
                  <th class="no-sort">Mensaje SUNAT</th>
                  <th class="no-sort">Editar</th>
                  <th class="no-sort">Anular</th>
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
</div>
<!-- /.content-wrapper -->


<div class="modal fade modal-modificar_venta" id="modal-default">
  <?php
  $attributes = array('id' => 'form-modificar_venta');
  echo form_open('', $attributes);
  ?>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="text-center">Modificar Venta</h4>
        <h5 class="text-center" id="title-venta"></h5>
      </div>

      <div class="modal-body">
        <div class="row">
          <input type="hidden" id="hidden-ID_Venta_Modificar" name="ID_Venta_Modificar" class="form-control" value="">
          <input type="hidden" id="hidden-Fe_Hora_Modificar" name="Fe_Hora_Modificar" class="form-control" value="">

          <div class="col-xs-12 col-sm-6">
            <label>Organización</label>
            <div class="form-group">
              <select id="cbo-modificar-organizacion" name="ID_Organizacion_Modificar" class="form-control select2" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-12 col-sm-6">
            <label>Almacén</label>
            <div class="form-group">
              <select id="cbo-modificar-almacen" name="ID_Almacen_Modificar" class="form-control select2" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-12 col-sm-6">
            <label>F. Emisión</label>
            <div class="form-group">
              <div class="input-group date" style="width: 100%;">
                <input type="text" id="txt-modificar-Fe_Emision" name="Fe_Emision_Modificar" class="form-control date-picker-invoice required" style="width: 100%;" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
              </div>
            </div>
          </div>
          
          <div class="col-xs-12 col-sm-6">
            <label>F. Vencimiento</label>
            <div class="form-group">
              <div class="input-group date" style="width: 100%;">
                <input type="text" id="txt-modificar-Fe_Vencimiento" name="Fe_Vencimiento_Modificar" class="form-control required" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
              </div>
              <span class="help-block" id="error"></span>
            </div>
          </div>
        </div><!--row data-->
      </div><!--modal-body-->
        
      <div class="modal-footer">
        <div class="row">
          <div class="col-xs-6 col-sm-6">
            <button type="button" id="btn-modal-salir" class="btn btn-danger btn-lg btn-block pull-center" data-dismiss="modal">Salir</button>
          </div>
          <div class="col-xs-6 col-sm-6">
            <button type="button" id="btn-modificar_venta" class="btn btn-success btn-lg btn-block pull-center" data-dismiss="modal">Modificar</button>
          </div>
        </div>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <?php echo form_close(); ?>
  <!-- /.modal-dialog -->
</div><!-- /.modal datos_adicionales_entidad -->