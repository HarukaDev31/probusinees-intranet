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
    </div>
    <!-- ./New box-header -->
    <div class="row">
      <div class="col-xs-12">
        <div class="box box-content">
          <!-- box-header -->
          <div class="box-header box-header-new">
            <div class="row div-Filtros">
              <br>
              <div class="col-xs-12 col-sm-4 col-md-4 col-lg-2">
                <div class="form-group">
                  <label>Almacén</label>
    		  				<select id="cbo-Almacenes_VentasTiposDocumentoSunat" class="form-control select2" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-3 col-md-2 col-lg-2">
                <div class="form-group">
                  <label>F. Inicio</label>
                  <div class="input-group date">
                    <input type="text" id="txt-Filtro_Fe_Inicio" class="form-control date-picker-report" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                  </div>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-3 col-md-2 col-lg-2">
                <div class="form-group">
                  <label>F. Fin</label>
                  <div class="input-group date">
                    <input type="text" id="txt-Filtro_Fe_Fin" class="form-control date-picker-invoice txt-Filtro_Fe_Fin" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                  </div>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
                <div class="form-group">
                  <label>Estado</label>
    		  				<select id="cbo-Filtro_Estado" class="form-control">
    		  				  <option value="0" selected>Todos</option>
    		  				  <option value="6">Completado</option>
    		  				  <option value="8">Completado Enviado</option>
    		  				  <option value="7">Anulado</option>
    		  				  <option value="10">Anulado Enviado</option>
        				  </select>
                </div>
              </div>

              <div class="col-xs-4 col-sm-12 col-md-4 col-lg-3">
                <div class="form-group">
                  <label>Regalo</label>
                  <select id="cbo-regalo" class="form-control" style="width: 100%;">
                    <option value="-" selected="selected">Todos</option>
                    <option value="1">Si</option>
                    <option value="2">No</option>
                  </select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
                            
              <div class="col-xs-4 col-sm-4 col-md-4">
                <label>&nbsp;</label>
                <div class="form-group">
                  <button type="button" id="btn-html_venta_sunat" class="btn btn-primary btn-block btn-generar_venta_sunat" data-type="html"><i class="fa fa-search color_white"></i> Buscar</button>
                </div>
              </div>
              
              <div class="col-xs-4 col-sm-4 col-md-4">
                <label>&nbsp;</label>
                <div class="form-group">
                  <button type="button" id="btn-pdf_venta_sunat" class="btn btn-danger btn-block btn-generar_venta_sunat" data-type="pdf"><i class="fa fa-file-pdf-o color_white"></i> PDF</button>
                </div>
              </div>
              
              <div class="col-xs-4 col-sm-4 col-md-4">
                <label>&nbsp;</label>
                <div class="form-group">
                  <button type="button" id="btn-excel_venta_sunat" class="btn btn-success btn-block btn-generar_venta_sunat" data-type="excel"><i class="fa fa-file-excel-o color_white"></i> Excel</button>
                </div>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div id="div-venta_x_tipo_documento_sunat" class="table-responsive">
            <table id="table-venta_x_tipo_documento_sunat" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th class="text-center">Fecha</th>
                  <th class="text-center" colspan="2">Boleta</th>
                  <th class="text-center" colspan="2">Factura</th>
                  <th class="text-center" colspan="2">N/Crédito</th>
                  <th class="text-center" colspan="2">N/Débito</th>
                  <th class="text-center" colspan="2">Total</th>
                </tr>
                <tr>
                  <th class="text-center">Emisión</th>
                  <th class="text-center">Trans.</th>
                  <th class="text-center">Importe</th>
                  <th class="text-center">Trans.</th>
                  <th class="text-center">Importe</th>
                  <th class="text-center">Trans.</th>
                  <th class="text-center">Importe</th>
                  <th class="text-center">Trans.</th>
                  <th class="text-center">Importe</th>
                  <th class="text-center">Trans.</th>
                  <th class="text-center">Importe</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
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