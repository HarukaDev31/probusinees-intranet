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
          	<input type="hidden" id="hidden-iActionEditar" name="iActionEditar" class="form-control" value="<?php echo $this->MenuModel->verificarAccesoMenuCRUD()->Nu_Editar; ?>">
            <div class="row div-Filtros">
              <br>
              <div class="col-xs-6 col-sm-4 col-md-3">
                <div class="form-group">
                  <label>Categoría</label>
    		  				<select id="cbo-Categorias_Stock_Valorizado" class="form-control select2" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-4 col-md-3">
                <div class="form-group">
                  <label>Sub Categoría</label>
                  <select id="cbo-sub_categoria" class="form-control select2" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-6 col-sm-4 col-md-2">
                <label>Todos productos</label>
                <div class="form-group">
        				  <select id="cbo-FiltrosProducto" class="form-control">
        				    <option value="0" selected>Si</option>
        				    <option value="1">No</option>
        				  </select>
                </div>
              </div>
              
              <div class="col-xs-12 col-sm-8 col-md-4 div-productos">
                <div class="form-group">
                  <label>Producto <span class="label-advertencia">*</span></label>
                  <input type="hidden" id="txt-Nu_Tipo_Producto" class="form-control" value="2">
                  <input type="hidden" id="txt-ID_Producto" class="form-control">
                  <input type="text" id="txt-No_Producto" class="form-control autocompletar_detalle" data-global-class_method="AutocompleteController/getAllProduct" data-global-table="producto" placeholder="Ingresar nombre o código barra" value="" autocomplete="off">
                  <span class="help-block" id="error"></span>
                </div>
              </div>
            </div>
              
            <div class="row div-Filtros">
              <div class="col-xs-12 col-sm-6 col-md-3">
                <label>Filtrar por</label>
                <div class="form-group">
        				  <label><input type="radio" id="radio-fe_actual" name="radio-fecha" value="0" class="flat-red" onclick="verFecha(this.value);" checked>&nbsp; Stock Actual</label>
        				  &nbsp;&nbsp;<label><input type="radio" id="radio-fe_seleccionada" name="radio-fecha" value="1" class="flat-red" onclick="verFecha(this.value);">&nbsp; Por Fecha</label>
                </div>
              </div>
              
              <div class="col-xs-6 col-sm-3 col-md-2 div-fecha_stock_valorizado">
                <div class="form-group">
                  <label>F. Inicio</label>
                  <div class="input-group date">
                    <input type="text" id="txt-Filtro_Fe_Inicio" class="form-control date-picker-report" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                  </div>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-3 col-md-2 div-fecha_stock_valorizado">
                <div class="form-group">
                  <label>F. Fin</label>
                  <div class="input-group date">
                    <input type="text" id="txt-Filtro_Fe_Fin" class="form-control date-picker-invoice txt-Filtro_Fe_Fin" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                  </div>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-12 col-sm-12 col-md-5">
                <label>Visualización de Stock</label>
                <div class="form-group">
                  <label><input type="radio" id="radio-stock_todos" class="flat-red" name="radio-stock" value="3" checked>&nbsp; Todos</label>
        				  &nbsp;&nbsp;<label><input type="radio" id="radio-stock_no" class="flat-red" name="radio-stock" value="0">&nbsp; Mayor a Cero</label>
        				  &nbsp;&nbsp;<label><input type="radio" id="radio-stock_negativo" class="flat-red" name="radio-stock" value="2">&nbsp; En cero</label>
        				  &nbsp;&nbsp;<label><input type="radio" id="radio-stock_si" class="flat-red" name="radio-stock" value="1">&nbsp; Negativo</label>
                </div>
              </div>
            </div>
              
            <div class="row div-Filtros">
              <br>
              <div class="col-xs-4 col-md-4">
                <div class="form-group">
                  <button type="button" id="btn-html_stock_valorizado" class="btn btn-primary btn-block btn-generar_stock_valorizado" data-type="html"><i class="fa fa-search color_white"></i> Buscar</button>
                </div>
              </div>
              
              <div class="col-xs-4 col-md-4">
                <div class="form-group">
                  <button type="button" id="btn-pdf_stock_valorizado" class="btn btn-danger btn-block btn-generar_stock_valorizado" data-type="pdf"><i class="fa fa-file-pdf-o color_white"></i> PDF</button>
                </div>
              </div>
              
              <div class="col-xs-4 col-md-4">
                <div class="form-group">
                  <button type="button" id="btn-excel_stock_valorizado" class="btn btn-success btn-block btn-generar_stock_valorizado" data-type="excel"><i class="fa fa-file-excel-o color_white"></i> Excel</button>
                </div>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div id="div-stock_valorizado" class="table-responsive">
            <table id="table-stock_valorizado" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th class="text-left">Código</th>
                  <th class="text-left">Nombre</th>
                  <th class="text-center">Unidad</th>
                  <th class="text-right">Stock</th>
                  <?php if (($this->empresa->ID_Empresa==73 && $this->MenuModel->verificarAccesoMenuCRUD()->Nu_Editar == 1) || $this->empresa->ID_Empresa!=73) : ?>
                    <th class="text-right">Precio Venta</th>
                    <th class="text-right">Precio Compra</th>
                    <th class="text-right">Costo Promedio</th>
                    <th class="text-right">Total</th>
                  <?php endif; ?>
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