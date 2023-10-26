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
              <div class="col-sm-6 col-xs-6 col-md-6">
                <label>Sede</label>
                <div class="form-group">
                  <select id="cbo-sede_musica" name="ID_Sede_Musica" class="form-control select2 required" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-sm-6 col-xs-6 col-md-6">
                <label>Salon</label>
                <div class="form-group">
                  <select id="cbo-salon" name="ID_Salon" class="form-control select2 required" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
            </div>
              
            <div class="row div-Filtros">
              <div class="col-xs-4 col-sm-4 col-md-4">
                <div style="padding: 2%; background-color: #69a1f7;"></div><label>Online</label>
              </div>
            </div>

            <div class="row div-Filtros">
              <div class="col-xs-4 col-sm-4 col-md-4">
                <div style="padding: 2%; background-color: #6de6af;"></div><label>Presencial</label>
              </div>
            </div>
              
            <div class="row div-Filtros">
              <div class="col-xs-4 col-sm-4 col-md-4">
                <div style="padding: 2%; background-color: #ff6c7a;"></div><label>Ambos</label>
              </div>
            </div>
              
            <div class="row div-Filtros">
              <br>
              <div class="col-xs-12 col-md-12">
                <div class="form-group">
                  <button type="button" id="btn-html_ventas_x_familia" class="btn btn-default btn-block btn-generar_ventas_x_familia" data-type="html"><i class="fa fa-search"></i> Buscar</button>
                </div>
              </div>
              
              <div class="col-xs-4 col-md-4 hidden">
                <div class="form-group">
                  <button type="button" id="btn-pdf_ventas_x_familia" class="btn btn-default btn-block btn-generar_ventas_x_familia" data-type="pdf"><i class="fa fa-file-pdf-o color_icon_pdf"></i> PDF</button>
                </div>
              </div>
              
              <div class="col-xs-4 col-md-4 hidden">
                <div class="form-group">
                  <button type="button" id="btn-excel_ventas_x_familia" class="btn btn-default btn-block btn-generar_ventas_x_familia" data-type="excel"><i class="fa fa-file-excel-o color_icon_excel"></i> Excel</button>
                </div>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div id="div-ventas_x_familia" class="table-responsive">
            <table id="table-ventas_x_familia" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th class="text-center"></th>
                  <th class="text-center">Lunes</th>
                  <th class="text-center">Martes</th>
                  <th class="text-center">Miercoles</th>
                  <th class="text-center">Jueves</th>
                  <th class="text-center">Viernes</th>
                  <th class="text-center">Sabado</th>
                  <th class="text-center">Domingo</th>
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