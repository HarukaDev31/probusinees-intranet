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
    		  				<select id="cbo-Almacenes_VentasxFamilia" class="form-control select2" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-4 col-md-2 col-lg-2">
                <div class="form-group">
                  <label>F. Inicio</label>
                  <div class="input-group date">
                    <input type="text" id="txt-Filtro_Fe_Inicio" class="form-control date-picker-report" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                  </div>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-4 col-md-2 col-lg-2">
                <div class="form-group">
                  <label>F. Fin</label>
                  <div class="input-group date">
                    <input type="text" id="txt-Filtro_Fe_Fin" class="form-control date-picker-invoice txt-Filtro_Fe_Fin" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                  </div>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-6 col-sm-4 col-md-2 col-lg-2">
                <div class="form-group">
                  <label>Moneda</label>
                  <select id="cbo-filtro_monedas" name="ID_Moneda" class="form-control" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-5 col-md-4 col-lg-2">
                <div class="form-group">
                  <label>Categoría</label>
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

              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <label class="" style="cursor:pointer;">
                  <div class="icheckbox_flat-green">
                    <input type="checkbox" id="checkbox-mas_filtros" name="filtro-mas_filtros" class="flat-red">
                  </div>
                  Más filtros
                  <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Al activar podrán visualizar mas campos para filtrar">
                    <i class="fa fa-info-circle"></i>
                  </span>
                  <div><br></div>
                </label>
              </div>
              
              <div class="col-xs-6 col-sm-4 col-md-2 col-lg-2 div-mas_opciones">
                <div class="form-group">
                  <label>Sub Categoría</label>
                  <select id="cbo-sub_categoria" class="form-control select2" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-8 col-md-6 col-lg-6 div-mas_opciones">
                <div class="form-group">
                  <label>Producto</label>
                  <input type="hidden" id="txt-Nu_Tipo_Registro" class="form-control" value="1"><!-- Venta -->
                  <input type="hidden" id="txt-ID_Producto" class="form-control">
                  <input type="text" id="txt-No_Producto" class="form-control autocompletar_detalle" data-global-class_method="AutocompleteController/globalAutocompleteReport" data-global-table="producto" placeholder="Ingresar nombre / código de barra / código sku" value="" autocomplete="off">
                  <span class="help-block" id="error"></span>
                </div>
                
                <label class="div-mas_opciones" style="cursor:pointer;">
                  <div class="icheckbox_flat-green">
                    <input type="checkbox" id="checkbox-busqueda_producto" name="filtro-busqueda_producto" class="flat-red">
                  </div>
                  Búsqueda por Nombre
                  <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Al activar buscará todos los productos que tengan coincidencia con el mismo NOMBRE">
                    <i class="fa fa-info-circle"></i>
                  </span>
                </label>
              </div>

              <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 div-mas_opciones">
                <div class="form-group">
                  <label>Marca</label>
                  <select id="cbo-filtro_marca" class="form-control select2 required" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
      			  <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12 div-mas_opciones">
                <div class="col-xs-6 col-sm-6 col-md-2">
                  <label>Variante 1</label>
                  <div class="form-group">                    
                    <select id="cbo-filtro_variante_1" name="ID_Variante_Item_1" class="form-control select2" style="width: 100%;"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
                
                <div class="col-xs-6 col-sm-6 col-md-2 div-mas_opciones">
                  <label>Valor 1</label>
                  <div class="form-group">                    
                    <select id="cbo-filtro_valor_1" name="ID_Variante_Item_Detalle_1" class="form-control select2" style="width: 100%;"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>

                <div class="col-xs-6 col-sm-6 col-md-2 div-mas_opciones">
                  <label>Variante 2</label>
                  <div class="form-group">                    
                    <select id="cbo-filtro_variante_2" name="ID_Variante_Item_2" class="form-control select2" style="width: 100%;"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
                
                <div class="col-xs-6 col-sm-6 col-md-2 div-mas_opciones">
                  <label>Valor 2</label>
                  <div class="form-group">                    
                    <select id="cbo-filtro_valor_2" name="ID_Variante_Item_Detalle_2" class="form-control select2" style="width: 100%;"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>

                <div class="col-xs-6 col-sm-6 col-md-2 div-mas_opciones">
                  <label>Variante 3</label>
                  <div class="form-group">                    
                    <select id="cbo-filtro_variante_3" name="ID_Variante_Item_3" class="form-control select2" style="width: 100%;"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
                
                <div class="col-xs-6 col-sm-6 col-md-2 div-mas_opciones">
                  <label>Valor 3</label>
                  <div class="form-group">                    
                    <select id="cbo-filtro_valor_3" name="ID_Variante_Item_Detalle_3" class="form-control select2" style="width: 100%;"></select>
                    <span class="help-block" id="error"></span>
                  </div>
                </div>
              </div>

              <div class="col-xs-12 col-sm-4 col-md-3 hidden">
                <label>Agrupar por Empresa</label>
                <div class="form-group">
                  <label style="font-weight:normal"><input type="radio" id="radio-agrupar_x_empresa_no" class="flat-red" name="radio-agrupar_x_empresa" value="0" checked>&nbsp; No</label>
                  &nbsp;&nbsp;<label style="font-weight:normal"><input type="radio" id="radio-agrupar_x_empresa_si" class="flat-red" name="radio-agrupar_x_empresa" value="1">&nbsp; Si</label>
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