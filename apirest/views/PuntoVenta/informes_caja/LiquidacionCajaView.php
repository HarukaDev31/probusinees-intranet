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
              <div class="col-xs-12 col-sm-6 col-md-2">
                <div class="form-group">
                  <label>Organización</label>
                  <select id="cbo-filtro_organizacion" name="ID_Organizacion" class="form-control select2" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-12 col-sm-6 col-md-2">
                <div class="form-group">
                  <label>Almacén</label>
                  <select id="cbo-filtro_almacen" name="ID_Almacen" class="form-control select2" style="width: 100%;"></select>
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

              <div class="col-xs-12 col-sm-6 col-md-4">
                <div class="form-group">
                  <label>Personal</label>
                  <input type="hidden" id="txt-AID" class="form-control">
                  <input type="text" id="txt-Filtro_Entidad" class="form-control autocompletar" data-global-class_method="AutocompleteController/getAllEmployee" data-global-table="entidad" placeholder="Ingresar Nombre / Nro. Documento de Identidad" value="" autocomplete="off">
                  <span class="help-block" id="error"></span>
                </div>
              </div>
            </div>
              
            <div class="row div-Filtros">
              <br>
              <div class="col-xs-4 col-md-4">
                <div class="form-group">
                  <button type="button" id="btn-html_liquidacion_caja" class="btn btn-primary btn-block btn-generar_liquidacion_caja" data-type="html"><i class="fa fa-search"></i> Buscar</button>
                </div>
              </div>
              
              <div class="col-xs-4 col-md-4">
                <div class="form-group">
                  <button type="button" id="btn-pdf_liquidacion_caja" class="btn btn-danger btn-block btn-generar_liquidacion_caja" data-type="pdf"><i class="fa fa-file-pdf-o color_icon_white"></i> PDF</button>
                </div>
              </div>
              
              <div class="col-xs-4 col-md-4">
                <div class="form-group">
                  <button type="button" id="btn-excel_liquidacion_caja" class="btn btn-success btn-block btn-generar_liquidacion_caja" data-type="excel"><i class="fa fa-file-excel-o color_icon_white"></i> Excel</button>
                </div>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div id="div-liquidacion_caja" class="table-responsive">
            <table id="table-liquidacion_caja" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th class="text-left">Organización</th>
                  <th class="text-left">Almacén</th>
                  <th class="text-left">Personal</th>
                  <th class="text-center">F. Apertura</th>
                  <th class="text-center">F. Cierre</th>
                  <th class="text-right">M</th>
                  <th class="text-right">Total a Liquidar</th>
                  <th class="text-right">Total Depositado</th>
                  <th class="text-right">Diferencia</th>
                  <th class="text-left">Nota</th>
                  <th class="text-center">Ver</th>
                  <th class="text-center">Imprimir</th>
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