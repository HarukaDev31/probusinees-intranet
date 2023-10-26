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

              <div class="col-xs-8 col-sm-6 col-md-4 col-lg-6">
                <label>Transporte</label>
                <div class="form-group">
                  <select id="cbo-transporte" class="form-control select2" style="width: 100%;"></select>
                </div>
              </div>

              <div class="col-xs-4 col-sm-12 col-md-4 col-lg-2">
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

              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <label class="" style="cursor:pointer;">
                  <div class="icheckbox_flat-green">
                    <input type="checkbox" id="checkbox-mas_filtros" name="filtro-mas_filtros" class="flat-red">
                  </div>
                  Más filtros
                  <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Al activar podrán visualizar mas campos para filtrar">
                    <i class="fa fa-info-circle"></i>
                  </span>
                </label>
              </div>
              
              <div class="col-xs-12 col-sm-4 col-md-2 div-mas_opciones">
                <div class="form-group">
                  <label>Tipo</label>
    		  				<select id="cbo-filtros_tipos_documento" class="form-control" style="width: 100%;"></select>
                </div>
              </div>
              
              <div class="col-xs-6 col-sm-4 col-md-2 div-mas_opciones">
                <div class="form-group">
                  <label>Serie</label>
    		  				<select id="cbo-filtros_series_documento" class="form-control" style="width: 100%;"></select>
                </div>
              </div>
              
              <div class="col-xs-6 col-sm-4 col-md-2 div-mas_opciones">
                <div class="form-group">
                  <label>Número</label>
                  <input type="tel" id="txt-Filtro_NumeroDocumento" class="form-control input-number" maxlength="20" placeholder="Buscar" value="" autocomplete="off">
                </div>
              </div>
              
              <div class="col-xs-12 col-sm-12 col-md-2 hidden">
                <div class="form-group">
                  <label>Estado Documento</label>
    		  				<select id="cbo-estado_documento" class="form-control" style="width: 100%;">
    		  				  <option value="0" selected="selected">Todos</option>
    		  				  <option value="6">Completado</option>
    		  				  <option value="8">Completado Enviado</option>
    		  				  <option value="9">Completado Error</option>
    		  				  <option value="7">Anulado</option>
    		  				  <option value="10">Anulado Enviado</option>
    		  				  <option value="11">Anulado Error</option>
    		  				</select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-12 col-sm-12 col-md-2 div-mas_opciones">
                <label>Estado Pedido</label>
                <div class="form-group">
    		  				<select id="cbo-tipo_estado_pedido" class="form-control">
    		  				  <option value="-" selected="selected">Todos</option>
                    <option value="0">Pendiente</option>
                    <option value="1">Preparando</option>
                    <option value="2">Enviado</option>
                    <option value="3">Entregado</option>
                    <option value="4">Rechazado</option>
        				  </select>
                </div>
              </div>

              <div class="col-xs-12 col-sm-6 col-md-6 div-mas_opciones">
                <div class="form-group">
                  <label>Producto</label>
                  <input type="hidden" id="txt-Nu_Tipo_Registro" class="form-control" value="1"><!-- Venta -->
                  <input type="hidden" id="txt-ID_Producto" class="form-control">
                  <input type="text" id="txt-No_Producto" class="form-control autocompletar_detalle" data-global-class_method="AutocompleteController/globalAutocompleteReport" data-global-table="producto" placeholder="Ingresar nombre / código de barra / código sku" value="" autocomplete="off">
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-12">
                <label></label>
                <div class="form-group">
                  <label><input type="radio" name="radio-tipo-reporte-ventas_x_trabajador" class="flat-red" value="0" checked> Detallado</label>
                  &nbsp;&nbsp;<label><input type="radio" name="radio-tipo-reporte-ventas_x_trabajador" class="flat-red" value="1"> Resumido</label>
                </div>                          
              </div>
            </div>
              
            <div class="row div-Filtros">
              <br>
              <div class="col-xs-4 col-md-4">
                <div class="form-group">
                  <button type="button" id="btn-html_ventas_x_trabajador" class="btn btn-primary btn-block btn-generar_ventas_x_trabajador" data-type="html"><i class="fa fa-search color_white"></i> Buscar</button>
                </div>
              </div>
              
              <div class="col-xs-4 col-md-4">
                <div class="form-group">
                  <button type="button" id="btn-pdf_ventas_x_trabajador" class="btn btn-danger btn-block btn-generar_ventas_x_trabajador" data-type="pdf"><i class="fa fa-file-pdf-o color_white"></i> PDF</button>
                </div>
              </div>
              
              <div class="col-xs-4 col-md-4">
                <div class="form-group">
                  <button type="button" id="btn-excel_ventas_x_trabajador" class="btn btn-success btn-block btn-generar_ventas_x_trabajador" data-type="excel"><i class="fa fa-file-excel-o color_white"></i> Excel</button>
                </div>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div id="div-ventas_x_trabajador" class="table-responsive">
            <table id="table-ventas_x_trabajador" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th class="text-center" style="width: 10%">Fecha</th>
                  <th class="text-center" style="width: 8%" colspan="3">Documento</th>
                  <th class="text-center" rowspan="2">Moneda</th>
                  <th class="text-center">Tipo</th>
                  <th class="text-center" colspan="8">Producto</th>
                  <th class="text-center" rowspan="2">Estado</th>
                  <th class="text-center" rowspan="2">Estado Delivery</th>
                </tr>
                <tr>
                  <th class="text-center" style="width: 10%">Emisión</th>
                  <th class="text-center">Tipo</th>
                  <th class="text-center">Serie</th>
                  <th class="text-center">Número</th>
                  <th class="text-center">Cambio</th>
                  <th class="text-center">Descripción</th>
                  <th class="text-center">Cantidad</th>
                  <th class="text-center">Precio</th>
                  <th class="text-center">Subtotal S/</th>
                  <th class="text-center">I.G.V. S/</th>
                  <th class="text-center">Dscto. S/</th>
                  <th class="text-center">Total S/</th>
                  <th class="text-center">Total M. Ex.</th>
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