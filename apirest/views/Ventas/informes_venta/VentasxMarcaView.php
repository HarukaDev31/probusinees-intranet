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
              <div class="col-xs-12 col-sm-4 col-md-2 col-lg-2">
                <div class="form-group">
                  <label>Almacén</label>
    		  				<select id="cbo-Almacenes_VentasxMarca" class="form-control select2" style="width: 100%;"></select>
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
              
              <div class="col-xs-6 col-sm-3 col-md-2 col-lg-2">
                <div class="form-group">
                  <label>Moneda</label>
                  <select id="cbo-filtro_monedas" name="ID_Moneda" class="form-control" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                <div class="form-group">
                  <label>Marca</label>
    		  				<select id="cbo-familia" class="form-control select2" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2">
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

              <div class="col-xs-12 col-sm-6 col-md-12">
                <div class="form-group">
                  <label>Producto</label>
                  <input type="hidden" id="txt-Nu_Tipo_Registro" class="form-control" value="1"><!-- Venta -->
                  <input type="hidden" id="txt-ID_Producto" class="form-control">
                  <input type="text" id="txt-No_Producto" class="form-control autocompletar_detalle" data-global-class_method="AutocompleteController/globalAutocompleteReport" data-global-table="producto" placeholder="Buscar por Nombre / Código/ SKU" value="" autocomplete="off">
                  <span class="help-block" id="error"></span>
                </div>
              </div>
            </div>
              
            <div class="row div-Filtros">
              <br>
              <div class="col-xs-4 col-md-4">
                <div class="form-group">
                  <button type="button" id="btn-html_ventas_x_familia" class="btn btn-primary btn-block btn-generar_ventas_x_familia" data-type="html"><i class="fa fa-search color_white"></i> Buscar</button>
                </div>
              </div>
              
              <div class="col-xs-4 col-md-4">
                <div class="form-group">
                  <button type="button" id="btn-pdf_ventas_x_familia" class="btn btn-danger btn-block btn-generar_ventas_x_familia" data-type="pdf"><i class="fa fa-file-pdf-o color_white"></i> PDF</button>
                </div>
              </div>
              
              <div class="col-xs-4 col-md-4">
                <div class="form-group">
                  <button type="button" id="btn-excel_ventas_x_familia" class="btn btn-success btn-block btn-generar_ventas_x_familia" data-type="excel"><i class="fa fa-file-excel-o color_white"></i> Excel</button>
                </div>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div id="div-ventas_x_familia" class="table-responsive">
            <table id="table-ventas_x_familia" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th class="text-center">F. Emisión</th>
                  <th class="text-center">Tipo</th>
                  <th class="text-center">Serie</th>
                  <th class="text-center">Número</th>
                  <th class="text-center">Cliente</th>
                  <th class="text-center">M</th>
                  <th class="text-center">T.C.</th>
                  <th class="text-center">U.M.</th>
                  <th class="text-center">Item</th>
                  <th class="text-center">Cantidad</th>
                  <th class="text-center">Precio</th>
                  <th class="text-center">SubTotal</th>
                  <th class="text-center">Impuesto</th>
                  <th class="text-center">Total</th>
                  <th class="text-center">Estado</th>
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