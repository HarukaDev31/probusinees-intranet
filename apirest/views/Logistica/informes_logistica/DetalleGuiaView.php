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
              <div class="col-sm-2">
                <div class="form-group">
                  <label>Almacén Origen</label>
    		  				<select id="cbo-Almacenes_Detalle_Guia" class="form-control select2" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
                      
              <div class="col-sm-2">
                <div class="form-group">
                  <label>Almacén Destino</label>
    		  				<select id="cbo-Almacenes_Externos_Detalle_Guia" class="form-control select2" style="width: 100%;"></select>
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

              <div class="col-sm-2">
                <div class="form-group">
                  <label>Tipo Movimiento</label>
    		  				<select id="cbo-tipo_movimiento" class="form-control" style="width: 100%;">
    		  				  <option value="-" selected="selected">Entrada y Salida</option>
    		  				  <option value="0">Entrada</option>
    		  				  <option value="1">Salida</option>
    		  				</select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-sm-2">
                <div class="form-group">
                  <label>Estado</label>
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
            </div>
            
            <div class="row div-Filtros">  
              <div class="col-xs-12 col-sm-4 col-md-2">
                <div class="form-group">
                  <label>Tipo Documento</label>
    		  				<select id="cbo-filtros_tipos_documento" class="form-control" style="width: 100%;"></select>
                </div>
              </div>

              <div class="col-xs-12 col-sm-4 col-md-2">
                <div class="form-group">
                  <label>Serie</label>
                  <input type="text" id="txt-Filtro_SerieDocumento" class="form-control input-Mayuscula input-codigo_barra" maxlength="20" placeholder="Buscar" value="" autocomplete="off">
                </div>
              </div>
              
              <div class="col-xs-12 col-sm-4 col-md-3">
                <div class="form-group">
                  <label>Número</label>
                  <input type="tel" id="txt-Filtro_NumeroDocumento" class="form-control input-number" maxlength="20" placeholder="Buscar" value="" autocomplete="off">
                </div>
              </div>
            </div>
              
            <div class="row div-Filtros">
              <div class="col-xs-12 col-md-2">
                <label>Todos los proveedores</label>
                <div class="form-group">
        				  <select id="cbo-FiltrosProveedorGuias" class="form-control">
        				    <option value="0" selected>No</option>
        				    <option value="1">Si</option>
        				  </select>
                </div>
              </div>
              
              <div class="col-md-10 div-proveedores">
                <div class="form-group">
                  <label>Proveedor</label>
                  <input type="hidden" id="txt-AID" class="form-control">
                  <input type="text" id="txt-Filtro_Entidad" class="form-control autocompletar" data-global-class_method="AutocompleteController/getAllProvider" data-global-table="entidad" placeholder="Buscar por Nombre / Nro. Documento de Identidad" value="" autocomplete="off">
                  <span class="help-block" id="error"></span>
                </div>
              </div>
            </div>
              
            <div class="row div-Filtros">
              <div class="col-xs-12 col-md-2">
                <label>Todos los clientes</label>
                <div class="form-group">
                  <select id="cbo-FiltrosClientesGuias" class="form-control">
                    <option value="0" selected>No</option>
                    <option value="1">Si</option>
                  </select>
                </div>
              </div>
              
              <div class="col-md-10 div-clientes">
                <div class="form-group">
                  <label>Cliente</label>
                  <input type="hidden" id="txt-AID_Doble" class="form-control">
                  <input type="text" id="txt-ANombre" class="form-control autocompletar_2" data-global-class_method="AutocompleteController/getAllClient" data-global-table="entidad" placeholder="Buscar por Nombre / Nro. Documento de Identidad" value="" autocomplete="off">
                  <span class="help-block" id="error"></span>
                </div>
              </div>
            </div>
              
            <div class="row div-Filtros">
              <div class="col-xs-12 col-md-2">
                <label>Todos los productos</label>
                <div class="form-group">
        				  <select id="cbo-FiltrosProductoGuias" class="form-control">
        				    <option value="0" selected>Si</option>
        				    <option value="1">No</option>
        				  </select>
                </div>
              </div>
              
              <div class="col-xs-12 col-md-10 div-productos">
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
              <br>
              <div class="col-xs-4 col-md-4">
                <div class="form-group">
                  <button type="button" id="btn-html_detalle_guia" class="btn btn-primary btn-block btn-generar_detalle_guia" data-type="html"><i class="fa fa-search color_white"></i> Buscar</button>
                </div>
              </div>
              
              <div class="col-xs-4 col-md-4">
                <div class="form-group">
                  <button type="button" id="btn-pdf_detalle_guia" class="btn btn-danger btn-block btn-generar_detalle_guia" data-type="pdf"><i class="fa fa-file-pdf-o color_white"></i> PDF</button>
                </div>
              </div>
              
              <div class="col-xs-4 col-md-4">
                <div class="form-group">
                  <button type="button" id="btn-excel_detalle_guia" class="btn btn-success btn-block btn-generar_detalle_guia" data-type="excel"><i class="fa fa-file-excel-o color_white"></i> Excel</button>
                </div>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div id="div-detalle_guia" class="table-responsive">
            <table id="table-detalle_guia" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th class="text-center" colspan="2" style="width: 8%">Guía</th>
                  <th class="text-center" style="width: 10%">Fecha</th>
                  <th class="text-center" colspan="2">Proveedor</th>
                  <th class="text-center" style="width: 8%" colspan="2">Factura</th>
                  <th class="text-center" rowspan="2">Moneda</th>
                  <th class="text-center">Tipo</th>
                  <th class="text-center" colspan="8">Producto</th>
                  <th class="text-center" rowspan="2">Glosa</th>
                  <th class="text-center" rowspan="2">Estado</th>
                  <th class="text-center" rowspan="2">Tipo</th>
                  <th class="text-center" rowspan="2">Movimiento</th>
                </tr>
                <tr>
                  <th class="text-center">Serie</th>
                  <th class="text-center">Número</th>
                  <th class="text-center" style="width: 10%">Emisión</th>
                  <th class="text-center">RUC</th>
                  <th class="text-center">Razón Social</th>
                  <th class="text-center">Serie</th>
                  <th class="text-center">Número</th>
                  <th class="text-center">Cambio</th>
                  <th class="text-center">Código Barra</th>
                  <th class="text-center">Descripción</th>
                  <th class="text-center">Cantidad</th>
                  <th class="text-center">Precio</th>
                  <th class="text-center">SubTotal S/</th>
                  <th class="text-center">Impuesto S/</th>
                  <th class="text-center">Total S/</th>
                  <th class="text-center">Total M. Ext.</th>
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