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
              <?php
              if ( $this->user->No_Usuario == 'root' ){ ?>
              <div class="col-md-12">
                <div class="form-group">
                  <select id="cbo-filtro_empresa" name="ID_Empresa" class="form-control"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              <?php } else { ?>
                <input type="hidden" id="cbo-filtro_empresa" name="ID_Empresa" class="form-control" value="<?php echo $this->user->ID_Empresa; ?>">
              <?php } ?>

              <div class="col-xs-12 col-sm-3 col-md-2">
                <div class="form-group">
                  <label>Moneda</label>
                  <select id="cbo-FiltroMonedas" name="ID_Moneda" class="form-control required" style="width: 100%;">
                  <option value="" selected="selected">- Todos -</option>
                  </select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-6 col-sm-3 col-md-2">
                <div class="form-group">
                  <label>F. Inicio</label>
                  <div class="input-group date">
                    <input type="text" id="txt-Filtro_Fe_Inicio" class="form-control date-picker-report_crud" value="<?php echo dateNow('month_date_report_crud'); ?>" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                  </div>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-3 col-md-2">
                <div class="form-group">
                  <label>F. Fin</label>
                  <div class="input-group date">
                    <input type="text" id="txt-Filtro_Fe_Fin" class="form-control date-picker-report_crud txt-Filtro_Fe_Fin" value="<?php echo dateNow('month_date_report_crud'); ?>" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                  </div>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-4 col-sm-3 col-md-2 text-center">
                <div class="form-group">
                  <label>Api</label>
                  <button type="button" id="btn-cloud-api_tasa_cambio" class="btn btn-success btn-block btn-md"><i class="fa fa-cloud-download fa-lg"></i></button>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-4 col-sm-6 col-md-2">
                <div class="form-group">
                  <label>&nbsp;</label>
                  <button type="button" id="btn-filter" class="btn btn-primary btn-block"><i class="fa fa-search"></i> Buscar</button>
                </div>
              </div>
              
              <div class="col-xs-4 col-sm-6 col-md-2">
                <div class="form-group">
                  <label>&nbsp;</label>
                  <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Agregar == 1) : ?>
                  <button type="button" class="btn btn-success btn-block" onclick="agregarTasaCambio()"><i class="fa fa-plus-circle"></i> Agregar</button>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="table-responsive">
            <table id="table-TasaCambio" class="table table-striped table-bordered">
              <thead>
              <tr>
                <?php if ( $this->user->No_Usuario == 'root' ){ ?>
                <th>Empresa</th>
                <?php } ?>
                <th>Nombre</th>
                <th>Signo</th>
                <th>F. Ingreso</th>
                <th>Compra</th>
                <th>Venta</th>
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
  $attributes = array('id' => 'form-TasaCambio');
  echo form_open('', $attributes);
  ?>
  <div class="modal fade" id="modal-TasaCambio" role="dialog">
  <div class="modal-dialog">
  	<div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title text-center"></h4>
      </div>
      
    	<div class="modal-body">
    	  <input type="hidden" name="EID_Empresa" class="form-control">
    	  <input type="hidden" name="EID_Tasa_Cambio" class="form-control">
    	  <input type="hidden" name="EID_Moneda" class="form-control">
    	  <input type="hidden" name="EFe_Ingreso" class="form-control">
    	  
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
          
          <div class="col-xs-6 col-sm-3 col-md-3">
            <div class="form-group">
              <label>Moneda <span class="label-advertencia">*</span></label>
              <select id="cbo-Monedas" name="ID_Moneda" class="form-control required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-6 col-sm-3 col-md-3">
            <div class="form-group">
              <label>F. Ingreso</label>
              <div class="input-group date">
                <input type="text" name="Fe_Ingreso" class="form-control date-picker-invoice required" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
              </div>
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-6 col-sm-3 col-md-3">
            <div class="form-group">
              <label>Compra <span class="label-advertencia">*</span></label>
              <input type="text" inputmode="decimal" name="Ss_Compra_Oficial" class="form-control required input-decimal">
              <span class="help-block" id="error"></span>
            </div>
          </div>
          
          <div class="col-xs-6 col-sm-3 col-md-3">
            <div class="form-group">
              <label>Venta <span class="label-advertencia">*</span></label>
              <input type="text" inputmode="decimal" name="Ss_Venta_Oficial" class="form-control required input-decimal">
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