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
              <div class="col-xs-12 col-sm-6 col-md-6">
                <div class="form-group">
                  <label>Libro</label>
                  <select id="cbo-TiposLibroSunatCompra" class="form-control"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-12 col-sm-6 col-md-6">
                <div class="form-group">
                  <label>Organización</label>
    		  				<select id="cbo-organizaciones" class="form-control" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-4 col-sm-4 col-md-2">
                <div class="form-group">
                  <label>Vista Boleta</label>
                  <select id="cbo-tipo_vista_venta" class="form-control">
        				    <option value="0" selected>Agrupado</option>
        				    <option value="1">Detallado</option>
                  </select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-4 col-sm-4 col-md-2">
                <div class="form-group">
                  <label>Año</label>
			            <?php echo Select('cbo-year', 'year', 'year', YearsYMD($this->empresa->Fe_Inicio_Sistema), date('Y'), true, ''); ?>
                </div>
              </div>
              
              <div class="col-xs-4 col-sm-4 col-md-2">
                <div class="form-group">
                  <label>Mes</label>
			            <?php echo Select('cbo-mes', 'valor', 'mes', Months(), date('m'), false, ''); ?>
                </div>
              </div>
              
              <div class="col-xs-6 col-sm-4 col-md-3">
                <div class="form-group">
                  <label>Ordenar por</label>
                  <select id="cbo-ordenar" class="form-control">
                    <option value="0" selected>Fecha Emision</option>
                    <option value="1">Fecha Sistema</option>
                    <option value="2">Fecha Periodo</option>
                    <option value="3">Series</option>
                  </select>
                </div>
              </div>

              <div class="col-xs-6 col-sm-8 col-md-3">
                <div class="form-group">
                  <label></label>
                  <button type="button" id="btn-modificar" class="btn btn-default btn-block"><i class="fa fa-table"></i> Modificar Correlativo</button>
                </div>
              </div>
            </div>
            
            <div class="row div-Filtros">
              <br>
              <div class="col-xs-4 col-md-2">
                <div class="form-group">
                  <label style="cursor: pointer; font-weight: normal;">
                    <input type="radio" name="Nu_Tipo_Formato" value="2" class="flat-red" checked style="position: absolute; opacity: 0;">
                    &nbsp;<i class="fa fa-file-pdf-o color_icon_pdf"></i> PDF
                  </label>
                </div>
              </div>
              
              <div class="col-xs-4 col-md-2">
                <div class="form-group">
                  <label style="cursor: pointer; font-weight: normal;">
                    <input type="radio" name="Nu_Tipo_Formato" value="1" class="flat-red" style="position: absolute; opacity: 0;">
                    &nbsp;<i class="fa fa-file-excel-o color_icon_excel"></i> Excel
                  </label>
                </div>
              </div>
              
              <div class="col-xs-4 col-md-2">
                <div class="form-group">
                  <label style="cursor: pointer; font-weight: normal;">
                    <input type="radio" name="Nu_Tipo_Formato" value="3" class="flat-red" style="position: absolute; opacity: 0;">
                    &nbsp;<i class="fa fa-files-o"></i> Libro <span class="hidden-xs">Electrónico</span>
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
                  <strong>Nota:</strong> Luego de <b>"Generar Reporte"</b> se estará procesando y con el botón <b>"Actualizar Estado Reporte"</b> verificaremos si ya terminó.</b>
                </div>
              </div>
            </div>
          </div>

          <div class="modal modal-default fade" id="modal-compra">
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

          <!-- /.box-header -->
          <div id="div-RegistroCompra" class="table-responsive">
            <table id="table-RegistroCompra" class="table table-striped table-bordered">
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