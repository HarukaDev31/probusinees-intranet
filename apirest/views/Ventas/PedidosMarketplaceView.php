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
    </div>
    <!-- ./New box-header -->
    <div class="row">
      <div class="col-xs-12">
        <div class="box box-content">
          <!-- box-header -->
          <div class="box-header box-header-new">
            <div class="row div-Filtros">
              <br>
              <div class="col-xs-6 col-sm-3 col-md-2">
                <div class="form-group">
                  <label>F. Inicio</label>
                  <div class="input-group date">
                    <input type="text" id="txt-Filtro_Fe_Inicio" class="form-control date-picker-report" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                  </div>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-3 col-md-2">
                <div class="form-group">
                  <label>F. Fin</label>
                  <div class="input-group date">
                    <input type="text" id="txt-Filtro_Fe_Fin" class="form-control date-picker-invoice txt-Filtro_Fe_Fin" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                  </div>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-12 col-sm-4 col-md-2">
                <div class="form-group">
                  <label>Tipo</label>
    		  				<select id="cbo-filtros_tipos_documento" class="form-control" style="width: 100%;">
                    <option value="0">Todos</option>
                    <option value="4">Boleta</option>
                    <option value="3">Factura</option>
                  </select>
                </div>
              </div>
              
              <div class="col-xs-6 col-sm-4 col-md-2">
                <div class="form-group">
                  <label>Número</label>
                  <input type="tel" id="txt-Filtro_NumeroDocumento" class="form-control input-number" maxlength="20" placeholder="Buscar" value="" autocomplete="off">
                </div>
              </div>
              
              <div class="col-xs-12 col-sm-12 col-md-2">
                <div class="form-group">
                  <label>Estado</label>
    		  				<select id="cbo-estado_pedido" class="form-control" style="width: 100%;">
    		  				  <option value="1" selected="selected">Pendiente</option>
    		  				  <option value="2">En Camino</option>
    		  				  <option value="3">Entregado</option>
    		  				  <option value="0">Todos</option>
    		  				</select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                  <label>Cliente</label>
                  <input type="hidden" id="txt-AID" class="form-control">
                  <input type="text" id="txt-Filtro_Entidad" class="form-control autocompletar" data-global-class_method="AutocompleteController/getAllClientMarketSeller" data-global-table="entidad" placeholder="Ingresar nombre / # documento identidad" value="" autocomplete="off">
                  <span class="help-block" id="error"></span>
                </div>
              </div>
            </div>
              
            <div class="row div-Filtros">
              <br>
              <div class="col-xs-4 col-md-4">
                <div class="form-group">
                  <button type="button" id="btn-html_ventas_detalladas_generales" class="btn btn-default btn-block btn-generar_ventas_detalladas_generales" data-type="html"><i class="fa fa-search"></i> Buscar</button>
                </div>
              </div>
              
              <div class="col-xs-4 col-md-4">
                <div class="form-group">
                  <button type="button" id="btn-pdf_ventas_detalladas_generales" class="btn btn-default btn-block btn-generar_ventas_detalladas_generales" data-type="pdf"><i class="fa fa-file-pdf-o color_icon_pdf"></i> PDF</button>
                </div>
              </div>
              
              <div class="col-xs-4 col-md-4">
                <div class="form-group">
                  <button type="button" id="btn-excel_ventas_detalladas_generales" class="btn btn-default btn-block btn-generar_ventas_detalladas_generales" data-type="excel"><i class="fa fa-file-excel-o color_icon_excel"></i> Excel</button>
                </div>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div id="div-ventas_detalladas_generales" class="table-responsive">
            <table id="table-ventas_detalladas_generales" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th class="text-center" rowspan="2">Fecha</th>
                  <th class="text-center" colspan="2">Documento</th>
                  <th class="text-center" colspan="3">Cliente</th>
                  <th class="text-center" rowspan="2">Total</th>
                  <th class="text-center" rowspan="2">Recepción</th>
                  <th class="text-center" rowspan="2">Estado</th>
                  <th class="text-center" rowspan="2"></th>
                </tr>
                <tr>
                  <th class="text-center">Tipo</th>
                  <th class="text-center">Número</th>
                  <th class="text-center">Tipo</th>
                  <th class="text-center"># Documento</th>
                  <th class="text-center">Nombre</th>
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