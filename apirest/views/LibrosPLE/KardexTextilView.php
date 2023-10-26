<?php
$sCssDisplayRopa='style="display:none"';
if ( $this->empresa->Nu_Tipo_Rubro_Empresa == '6' ){//La cava del baco
  $sCssDisplayRopa='';
}
?>
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
            <div class="row div-Filtros hidden">
              <div class="col-xs-12 col-md-12">
                <label style="margin-top: 1%;">Libro<span class="label-advertencia">*</span></label>
                <div class="form-group">
                  <select id="cbo-TiposLibroSunatKardex" class="form-control"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
            </div>
            <br>
            <div class="row div-Filtros">
              <div class="col-xs-12 col-sm-4 col-md-4">
                <div class="form-group">
                  <label>Almacen</label>
    		  				<select id="cbo-Almacenes_filtro_kardex" class="form-control select2" multiple="multiple" style="width: 100%;" placeholder="- Todos -"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
          
              <div class="col-xs-6 col-sm-4 col-md-2">
                <div class="form-group">
                  <label>F. Inicio</label>
                  <div class="input-group date" style="width: 100%;">
                    <input type="text" id="txt-Filtro_Fe_Inicio" class="form-control date-picker-report" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                  </div>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-4 col-md-2">
                <div class="form-group">
                  <label>F. Fin</label>
                  <div class="input-group date" style="width: 100%;">
                    <input type="text" id="txt-Filtro_Fe_Fin" class="form-control date-picker-invoice txt-Filtro_Fe_Fin" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                  </div>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-12 col-md-4">
                <label>Todos los productos</label>
                <div class="form-group">
        				  <select id="cbo-FiltrosProducto" class="form-control" style="width: 100%;">
        				    <option value="0">Si</option>
        				    <option value="1">No</option>
        				  </select>
                </div>
              </div>

              <div class="col-xs-12 col-md-12 div-productos">
                <div class="form-group">
                  <label>Producto</label>
                  <input type="hidden" id="txt-Nu_Tipo_Registro" class="form-control" value="3"><!-- Venta -->
                  <input type="hidden" id="txt-ID_Producto" class="form-control">
                  <input type="text" id="txt-No_Producto" class="form-control autocompletar_kardex" data-global-class_method="AutocompleteController/globalAutocompleteKardex" data-global-table="producto" placeholder="Ingresar nombre / código de barra / código sku" value="" autocomplete="off">
                  <span class="help-block" id="error"></span>
                </div>
                
                <label class="" style="cursor:pointer;">
                  <div class="icheckbox_flat-green">
                    <input type="checkbox" id="checkbox-busqueda_producto" name="filtro-busqueda_producto" class="flat-red">
                  </div>
                  Búsqueda por Nombre
                  <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Al activar buscará todos los productos que tengan coincidencia con el mismo NOMBRE">
                    <i class="fa fa-info-circle"></i>
                  </span>
                </label>
                <div><br></div>
              </div>
              
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <label class="" style="cursor:pointer;">
                  <div class="icheckbox_flat-green">
                    <input type="checkbox" id="checkbox-mas_filtros" name="filtro-mas_filtros" class="flat-red">
                  </div>
                  Más filtros
                  <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Al activar tendrán mas opciones para filtrar">
                    <i class="fa fa-info-circle"></i>
                  </span>
                  <div><br></div>
                </label>
                <!--<label class="hidden-md hidden-lg">&nbsp;</label>-->
                <!--
                <div class="form-group">
                  <button type="button" id="btn-mostrar_campos_adicionales" class="btn btn-default btn-block" data-mostrar_campos_adicionales="0">Ver más filtros</button>
                </div>
-->
              </div>

              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 div-mas_opciones">
                <div class="form-group">
                  <label>Tipo Movimiento</label>
                  <select id="cbo-filtro_tipo_movimiento" class="form-control select2 required" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 hidden">
                <label>Ver productos solo</label>
                <div class="form-group">
        				  <label style="cursor:pointer; font-weight:normal"><input type="radio" id="radio-filtro_item_con_movimiento" class="flat-red" name="radio-filtro_item_movimiento" value="2">&nbsp; Con movimientos 
                  <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Productos que tengan ventas / compras / guías dentro del rango de fecha seleccionado">
                    <i class="fa fa-info-circle"></i>
                  </span></label>
        				  &nbsp;&nbsp;<label style="cursor:pointer; font-weight:normal"><input type="radio" id="radio-filtro_item_ambos_movimiento" class="flat-red" name="radio-filtro_item_movimiento" value="1" checked>&nbsp; ambos movimientos
                  <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Productos que tengan o NO ventas / compras / guías dentro del rango de fecha seleccionado">
                    <i class="fa fa-info-circle"></i>
                  </span></label>
                </div>
              </div>

              <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 div-mas_opciones">
                <div class="form-group">
                  <label>Categoría</label>
    		  				<select id="cbo-filtro_categoria" class="form-control select2 required" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 div-mas_opciones">
                <div class="form-group">
                  <label>SubCategoría</label>
                  <select id="cbo-filtro_sub_categoria" class="form-control select2 required" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 div-mas_opciones">
                <div class="form-group">
                  <label>Marca</label>
                  <select id="cbo-filtro_marca" class="form-control select2 required" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
      			  <div class="div-mas_opciones-variantes" <?php echo $sCssDisplayRopa; ?>>
              <div class="col-xs-6 col-sm-6 col-md-2">
                <label>Variante 1</label>
                <div class="form-group">                    
                  <select id="cbo-filtro_variante_1" name="ID_Variante_Item_1" class="form-control select2" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-6 col-sm-6 col-md-2">
                <label>Valor 1</label>
                <div class="form-group">                    
                  <select id="cbo-filtro_valor_1" name="ID_Variante_Item_Detalle_1" class="form-control select2" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-6 col-md-2">
                <label>Variante 2</label>
                <div class="form-group">                    
                  <select id="cbo-filtro_variante_2" name="ID_Variante_Item_2" class="form-control select2" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-6 col-sm-6 col-md-2">
                <label>Valor 2</label>
                <div class="form-group">                    
                  <select id="cbo-filtro_valor_2" name="ID_Variante_Item_Detalle_2" class="form-control select2" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-6 col-md-2">
                <label>Variante 3</label>
                <div class="form-group">                    
                  <select id="cbo-filtro_variante_3" name="ID_Variante_Item_3" class="form-control select2" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-6 col-sm-6 col-md-2">
                <label>Valor 3</label>
                <div class="form-group">                    
                  <select id="cbo-filtro_valor_3" name="ID_Variante_Item_Detalle_3" class="form-control select2" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              </div>
            </div>

            <div class="row div-Filtros">
              <br>
              <div class="col-xs-4 col-md-3">
                <div class="form-group">
                  <button type="button" id="btn-html_kardex" class="btn btn-primary btn-block btn-generar_kardex" data-type="html"><i class="fa fa-table color_white"></i> HTML</button>
                </div>
              </div>
              
              <div class="col-xs-4 col-md-3">
                <div class="form-group">
                  <button type="button" id="btn-pdf_kardex" class="btn btn-danger btn-block btn-generar_kardex" data-type="pdf"><i class="fa fa-file-pdf-o color_white"></i> PDF</button>
                </div>
              </div>
              
              <div class="col-xs-4 col-md-3">
                <div class="form-group">
                  <button type="button" id="btn-excel_kardex" class="btn btn-success btn-block btn-generar_kardex" data-type="excel"><i class="fa fa-file-excel-o color_white"></i> Excel</button>
                </div>
              </div>
              
              <div class="col-xs-12 col-md-3">
                <div class="form-group">
                  <button type="button" id="btn-txt_kardex" class="btn btn-default btn-block btn-generar_kardex" data-type="txt"><i class="fa fa-files-o"></i> Libro Electrónico</button>
                </div>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div id="div-Kardex" class="table-responsive">
            <table id="table-Kardex" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th class="text-center">F. Emisión</th>
                  <th class="text-center">Cod. Tipo</th>
                  <th class="text-center">Tipo</th>
                  <th class="text-center">Serie</th>
                  <th class="text-center">Número</th>
                  <th class="text-center">Operación</th>
                  <th class="text-center">Movimiento</th>
                  <th class="text-center">Nro. Doc. Identidad</th>
                  <th class="text-center">Cliente / Proveedor</th>
                  <th class="text-center">Entrada</th>
                  <th class="text-center">Salida</th>
                  <th class="text-center">Saldo Final</th>
                  <th class="text-center">Estado</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
            
            <table id="table-Kardex_sin_movimientos" class="table table-striped table-bordered">
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