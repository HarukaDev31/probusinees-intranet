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
              <div class="col-xs-12 col-md-12">
                <label style="margin-top: 1%;">Libro <span class="label-advertencia">*</span></label>
                <div class="form-group">
                  <select id="cbo-TiposLibroSunatKardex" class="form-control"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
            </div>
            
            <div class="row div-Filtros">              
              <div class="col-xs-12 col-md-4">
                <div class="form-group">
                  <label>Almacen</label>
    		  				<select id="cbo-Almacenes_filtro_kardex" class="form-control select2 required" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
          
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
              
              <div class="col-xs-12 col-md-4">
                <label>Todos los productos</label>
                <div class="form-group">
        				  <select id="cbo-FiltrosProducto" class="form-control">
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
                  <input type="text" id="txt-No_Producto" class="form-control autocompletar_detalle" data-global-class_method="AutocompleteController/globalAutocompleteReport" data-global-table="producto" placeholder="Ingresar nombre / código de barra / código sku" value="" autocomplete="off">
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="form-group">
                  <label>Tipo Movimiento</label>
    		  				<select id="cbo-filtro_tipo_movimiento" class="form-control select2 required" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
            </div>
            
<!--             <div class="row div-Filtros">
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
            </div> -->
            <div class="row div-Filtros">
              <br>
               <div class="col-xs-4 col-md-2">
                 <div class="form-group">
                  <button type="button" id="btn-html_kardex" class="btn btn-primary btn-block btn-generar_kardex " data-type="html"><i class="fa fa-search color_white"></i> Buscar</button>
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
          <!-- /.box-header -->
          <!-- <div id="div-Kardex" class="table-responsive">
            <table id="table-Kardex" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th class="text-center" rowspan="2">F. Emisión</th>
                  <th class="text-center" rowspan="2">Cod. Tipo</th>
                  <th class="text-center" rowspan="2">Tipo</th>
                  <th class="text-center" rowspan="3">Serie</th>
                  <th class="text-center" rowspan="3">Número</th>
                  <th class="text-center" rowspan="2">Operación</th>
                  <th class="text-center" rowspan="2">Movimiento</th>
                  <th class="text-center" rowspan="2">Nro. Doc. Identidad</th>
                  <th class="text-center" rowspan="2">Cliente / Proveedor</th>
                  <th class="text-center" colspan="3">Entrada</th>
                  <th class="text-center" colspan="3">Salida</th>
                  <th class="text-center" colspan="3">Saldo Final</th>
                  <th class="text-center" rowspan="2">Estado</th>
                </tr>
                <tr>
                  <th class="text-center">Cantidad</th>
                  <th class="text-center">C/Unitario</th>
                  <th class="text-center">Total</th>
                  <th class="text-center">Cantidad</th>
                  <th class="text-center">C/Unitario</th>
                  <th class="text-center">Total</th>
                  <th class="text-center">Cantidad</th>
                  <th class="text-center">C/Promedio</th>
                  <th class="text-center">Total</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div> -->
                    <!-- /.box-header -->
          <div class="tab-content">
            <div class="tab-pane active" id="tab_1">
          <!-- /.box-header -->
                <div id="div-Kardex" class="table-responsive">
                  <table id="table-Kardex" class="table table-striped table-bordered">
                  <thead>
                      <tr>
                        <th class="text-center" rowspan="2">F. Emisión</th>
                        <th class="text-center" rowspan="2">Cod. Tipo</th>
                        <th class="text-center" rowspan="2">Tipo</th>
                        <th class="text-center" rowspan="3">Serie</th>
                        <th class="text-center" rowspan="3">Número</th>
                        <th class="text-center" rowspan="2">Operación</th>
                        <th class="text-center" rowspan="2">Movimiento</th>
                        <th class="text-center" rowspan="2">Nro. Doc. Identidad</th>
                        <th class="text-center" rowspan="2">Cliente / Proveedor</th>
                        <th class="text-center" colspan="3">Entrada</th>
                        <th class="text-center" colspan="3">Salida</th>
                        <th class="text-center" colspan="3">Saldo Final</th>
                        <th class="text-center" rowspan="2">Estado</th>
                      </tr>
                      <tr>
                        <th class="text-center">Cantidad</th>
                        <th class="text-center">C/Unitario</th>
                        <th class="text-center">Total</th>
                        <th class="text-center">Cantidad</th>
                        <th class="text-center">C/Unitario</th>
                        <th class="text-center">Total</th>
                        <th class="text-center">Cantidad</th>
                        <th class="text-center">C/Promedio</th>
                        <th class="text-center">Total</th>
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
<!-- /.content-wrapper