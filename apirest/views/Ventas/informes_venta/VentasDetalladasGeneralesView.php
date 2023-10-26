<script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.13.4/underscore-min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/backbone.js/1.4.1/backbone-min.js"></script>

<script type=text/template id="TemplateReporte">
  <% _.each( arrData, function( row ){
  var estado = "";
  var disable = "";
  var label = "";
  var button = "";

  if(row.ID_Estatus==0 || row.ID_Estatus==1){
    estado = "Procesando";
    disable = "disabled";
    label= "warning";
  } else if(row.ID_Estatus==2){
    estado = "Finalizado";
    label= "success";
  }

  if(row.Nu_Tipo_Formato==1){
    button= "success";
  }else if(row.Nu_Tipo_Formato==2){
    button= "danger";
  }else if(row.Nu_Tipo_Formato==3){
    button= "default";
  }
  %>
  <tr>
    <td class="text-center"><%= row.Fe_Creacion %></td>
    <td class="text-left"><%= row.Txt_Nombre_Archivo %></td>
    <td class="text-center">
      <span class="label label-<%=label%>"><%= estado %></span>
      <% if(row.ID_Estatus==0 || row.ID_Estatus==1){ %>
        <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>
      <% } %>
    </td>
    <td class="text-center">
      <button type="button" data-valor=<%= row.ID_Reporte %> <%= disable %> class="btn btn-<%= button %> btn-xs <%= disable %> pull-center btn-download">
      <% if(row.Nu_Tipo_Formato==1){ %>
        <i class="fa fa-file-excel-o"></i> Descargar EXCEL
      <% }else if(row.Nu_Tipo_Formato==2){ %>
        <i class="fa fa-file-pdf-o"></i> Descargar PDF
      <% }else if(row.Nu_Tipo_Formato==3){ %>
        <i class="fa fa-file-text-o"></i> Descargar TXT
      <% } %>
      </button>
    </td>

    <td class="text-center">
      <% if(row.ID_Estatus==0 || row.ID_Estatus==1){ %>
      <button type="button" data-valor=<%= row.ID_Reporte %> class="btn btn-primary btn-xs btn-danger pull-center btn-cancelar">
        Cancelar
      </button>
      <%} else if(row.ID_Estatus==2){ %>
        <button type="button" data-valor=<%= row.ID_Reporte %> class="btn btn-primary btn-xs btn-danger pull-center btn-cancelar">
        Eliminar
      </button>
      <% } %>
    </td>
  </tr>
<% }); %>
</script>
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
              <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2">
                <div class="form-group">
                  <label>Almacén</label>
    		  				<select id="cbo-Almacenes_VentasDetalladasGenerales" class="form-control select2" style="width: 100%;"></select>
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

              <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2 div-mas_opciones">
                <div class="form-group">
                  <label>Tipo</label>
    		  				<select id="cbo-filtros_tipos_documento" class="form-control" style="width: 100%;"></select>
                </div>
              </div>
              
              <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2 div-mas_opciones">
                <div class="form-group">
                  <label>Serie</label>
    		  				<select id="cbo-filtros_series_documento" class="form-control" style="width: 100%;"></select>
                </div>
              </div>
              
              <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2 div-mas_opciones">
                <div class="form-group">
                  <label>Número</label>
                  <input type="tel" id="txt-Filtro_NumeroDocumento" class="form-control input-number" maxlength="20" placeholder="Buscar" value="" autocomplete="off">
                </div>
              </div>
              
              <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2 div-mas_opciones">
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

              <div class="col-xs-6 col-sm-4 col-md-3 col-lg-3 div-mas_opciones">
                <div class="form-group">
                  <label>Categoría</label>
    		  				<select id="cbo-familia" class="form-control select2" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-4 col-md-3 col-lg-3 div-mas_opciones">
                <div class="form-group">
                  <label>Sub Categoría</label>
                  <select id="cbo-sub_categoria" class="form-control select2" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-4 col-md-4 col-lg-2 div-mas_opciones">
                <div class="form-group">
                  <label>Marca</label>
                  <select id="cbo-marca" class="form-control select2" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-6 col-md-3 col-lg-2 div-mas_opciones">
                <div class="form-group">
                  <label>L. Precio</label>
                  <select id="cbo-lista_precios" class="form-control select2" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2 div-mas_opciones">
                <div class="form-group">
                  <label>Tipo Venta</label>
    		  				<select id="cbo-tipo_venta" class="form-control" style="width: 100%;">
    		  				  <option value="0" selected="selected">Todos</option>
    		  				  <option value="1">Factura de Venta (Oficina)</option>
    		  				  <option value="2">Punto de Venta (Caja)</option>
    		  				</select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-12 col-sm-6 col-md-3 col-lg-5 div-mas_opciones">
                <div class="form-group">
                  <label>Cliente</label>
                  <input type="hidden" id="txt-AID" class="form-control">
                  <input type="text" id="txt-Filtro_Entidad" class="form-control autocompletar" data-global-class_method="AutocompleteController/getAllClient" data-global-table="entidad" placeholder="Buscar por Nombre / Nro. Documento de Identidad" value="" autocomplete="off">
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-12 col-sm-6 col-md-4 col-lg-5 div-mas_opciones">
                <div class="form-group">
                  <label>Producto</label>
                  <input type="hidden" id="txt-Nu_Tipo_Registro" class="form-control" value="1"><!-- Venta -->
                  <input type="hidden" id="txt-ID_Producto" class="form-control">
                  <input type="text" id="txt-No_Producto" class="form-control autocompletar_detalle" data-global-class_method="AutocompleteController/globalAutocompleteReport" data-global-table="producto" placeholder="Buscar por Nombre / Código / SKU" value="" autocomplete="off">
                  <span class="help-block" id="error"></span>
                </div>
              </div>
            </div>

            <div class="row div-Filtros">
              <div class="col-xs-6 col-sm-4 col-md-2 col-lg-2 div-mas_opciones">
                <label>Recepción</label>
                <div class="form-group">
    		  				<select id="cbo-tipo_recepcion_cliente" class="form-control">
    		  				  <option value="0" selected="selected">Todos</option>
                    <option value="5">Tienda</option>
                    <option value="6">Delivery</option>
                    <option value="7">Recojo en Tienda</option>
        				  </select>
                </div>
              </div>
              
              <div class="col-xs-6 col-sm-4 col-md-2 col-lg-2 div-mas_opciones">
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
              
              <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4 div-mas_opciones">
                <label>Transporte</label>
                <div class="form-group">
    		  				<select id="cbo-transporte" class="form-control select2" style="width: 100%;"></select>
                </div>
              </div>
              
              <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 div-mas_opciones">
                <label>Canal Venta</label>
                <div class="form-group">
    		  				<select id="cbo-canal_venta" class="form-control" style="width: 100%;"></select>
                </div>
              </div>
            </div>
            
           <!--  <div class="row div-Filtros">
              <br>
              <div class="col-xs-4 col-md-4">
                <div class="form-group">
                  <button type="button" id="btn-html_ventas_detalladas_generales" class="btn btn-primary btn-block btn-generar_ventas_detalladas_generales" data-type="html"><i class="fa fa-search"></i> Buscar</button>
                </div>
              </div>
              
              <div class="col-xs-4 col-md-4">
                <div class="form-group">
                  <button type="button" id="btn-pdf_ventas_detalladas_generales" class="btn btn-danger btn-block btn-generar_ventas_detalladas_generales" data-type="pdf"><i class="fa fa-file-pdf-o color_white"></i> PDF</button>
                </div>
              </div>
              
              <div class="col-xs-4 col-md-4">
                <div class="form-group">
                  <button type="button" id="btn-excel_ventas_detalladas_generales" class="btn btn-success btn-block btn-generar_ventas_detalladas_generales" data-type="excel"><i class="fa fa-file-excel-o color_white"></i> Excel</button>
                </div>
              </div>
            </div> -->

            <div class="row div-Filtros">
              <br>
               <div class="col-xs-4 col-md-2">
                 <div class="form-group">
                  <button type="button" id="btn-html_ventas_detalladas_generales" class="btn btn-primary btn-block btn-generar_ventas_detalladas_generales" data-type="html"><i class="fa fa-search color_white"></i> Buscar</button>
                </div>
              </div>

              <div class="col-xs-4 col-md-2">
                <div class="form-group">
                  <label style="cursor: pointer; font-weight: normal;">
                    <!-- <div class="iradio_flat-green checked" style="position: relative;" aria-checked="false" aria-disabled="false"><input type="radio" name="Nu_Tipo_Formato" value="2" class="flat-red" checked="" style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px none; opacity: 0;"></ins></div>
                    &nbsp;<i class="fa fa-file-pdf-o color_icon_pdf"></i> PDF -->
                  </label>
                </div>
              </div>
              
              <div class="col-xs-4 col-md-2">
                <div class="form-group">
                  <label style="cursor: pointer; font-weight: normal;">
                    <div class="iradio_flat-green" style="position: relative;" aria-checked="false" aria-disabled="false"><input type="radio" name="Nu_Tipo_Formato" value="1" class="flat-red" checked style="position: absolute; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px none; opacity: 0;"></ins></div>
                    &nbsp;<i class="fa fa-file-excel-o color_icon_excel"></i> Excel
                  </label>
                </div>
              </div>
              
              <div class="col-xs-12 col-md-3">
                <div class="form-group">
                  <button type="button" id="btn-generar" class="btn btn-default btn-block">Generar Reporte</button>
                </div>
              </div>

              <div class="col-xs-12 col-md-3">
                <div class="form-group">
                    <button type="button" id="btn-reload" class="btn btn-default btn-block">Actualizar Estado Reporte</button>
                </div>
              </div>
              
              <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="alert alert-success alert-dismissible">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                  <strong>Nota:</strong> Luego de <b>"Generar Reporte"</b> se estará procesando y con el botón <b>"Actualizar Estado Reporte"</b> verificaremos en la pestaña <b>"Reportes Generados"</b> si ya terminó.
                </div>
              </div>
            </div>

             <ul class="nav nav-tabs">
                  <li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true">Listado Html</a></li>
                  <li class=""><a href="#tab_2" data-toggle="tab" aria-expanded="true">Reportes Generados</a></li>
            </ul>

          </div>

          <div class="tab-content">
            <div class="tab-pane active" id="tab_1">
          <!-- /.box-header -->
                <div id="div-ventas_detalladas_generales" class="table-responsive">
                  <table id="table-ventas_detalladas_generales" class="table table-striped table-bordered">
                    <thead>
                      <tr>
                        <th class="text-center" rowspan="2">Fecha Emisión</th>
                        <th class="text-center" rowspan="2">Hora Emisión</th>
                        <th class="text-center" rowspan="2">Personal / Cajero</th>
                        <th class="text-center" colspan="3">Documento</th>
                        <th class="text-center" colspan="3">Cliente</th>
                        <th class="text-center" colspan="2">Moneda</th>
                        <th class="text-center" colspan="13">Producto</th>
                        <th class="text-center" colspan="4">Recepción</th>
                        <th class="text-center" colspan="3">Guía</th>
                        <th class="text-center" rowspan="2">Nota Global</th>
                        <th class="text-center" rowspan="2">L. Precio</th>
                        <th class="text-center" rowspan="2">Canal Venta</th>
                        <th class="text-center" rowspan="2">O/C</th>
                        <th class="text-center" rowspan="2">Placa</th>
                        <th class="text-center" colspan="6">Cliente Adicionales</th><!-- -->
                        <th class="text-center" rowspan="2">Descuento ITEM</th>
                        <th class="text-center" rowspan="2">Descuento TOTAL</th>
                        <th class="text-center" rowspan="2">Estado</th>
                      </tr>
                      <tr>
                        <th class="text-center">Tipo</th>
                        <th class="text-center">Serie</th>
                        <th class="text-center">Número</th>
                        <th class="text-center">Tipo</th>
                        <th class="text-center"># Documento</th>
                        <th class="text-center">Nombre</th>
                        <th class="text-center">Tipo</th>
                        <th class="text-center">T.C.</th>
                        <th class="text-center">Marca</th>
                        <th class="text-center">Categoría</th>
                        <th class="text-center">Sub Categoría</th>
                        <th class="text-center">Unidad Medida</th>
                        <th class="text-center">Código Barra</th>
                        <th class="text-center">Nombre</th>
                        <th class="text-center">Nota</th>
                        <th class="text-center">Cantidad</th>
                        <th class="text-center">CO2</th>
                        <th class="text-center">Precio</th>
                        <th class="text-center">Subtotal</th>
                        <th class="text-center">Impuesto</th>
                        <th class="text-center">Total</th>
                        <th class="text-center">Tipo</th>
                        <th class="text-center">Transporte</th>
                        <th class="text-center">F. Entrega</th>
                        <th class="text-center">Estado Pedido</th>
                        <th class="text-center">Tipo</th>
                        <th class="text-center">Serie</th>
                        <th class="text-center">Número</th>
                        <th class="text-center">Dirección</th>
                        <th class="text-center">Distrito</th>
                        <th class="text-center">Provincia</th>
                        <th class="text-center">Departamento</th>
                        <th class="text-center">Celular</th>
                        <th class="text-center">Correo</th>
                      </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                </div>
             </div>

              <div class="modal modal-default fade" id="modal-venta">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-body">
                      <h4>
                        <p>El reporte se esta procesando puede seguir usando el sistema y para ver el avance del estado clic en el botón "<b>Actualizar Estado Reporte</b>".</p>
                      </h4>
                    </div>
                    <div class="modal-footer">
                      <button type="button" id="btn-salir" class="btn btn-danger btn-block pull-center" data-dismiss="modal">Cerrar</button>
                    </div>
                  </div>
                </div>
              </div>

            <div class="tab-pane" id="tab_2">
              <div class="table-responsive">
                <table class="table table-striped table-bordered">
                  <thead>
                    <tr>
                      <th class="text-center">F. Creacion</th>
                      <th class="text-center">Nombre Archivo</th>
                      <th class="text-center">Estado</th>
                      <th class="text-center">Archivo</th>
                      <th class="text-center"></th>
                    </tr>
                  </thead>
                  <tbody id="CuerpoReporte"></tbody>
                </table>
              </div>
           </div>


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