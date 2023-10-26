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
      <!-- ./New box-header -->
    </div>
    <div class="row">
      <div class="col-xs-12">
        <div class="box box-content">
          <!-- box-header -->
          <div class="box-header box-header-new div-Listar">
            <div class="row div-Filtros">
              <br>
              <h2 class="text-center" style="margin-top: 5px;"><label>Almacén: <?php echo $this->session->userdata['almacen']->No_Almacen; ?></label></h2>

              <div class="col-xs-6 col-sm-3 col-md-2 col-lg-2">
                <div class="form-group">
                  <label>F. Inicio</label>
                  <div class="input-group date" style="width: 100%;">
                    <input type="text" id="txt-Filtro_Fe_Inicio" class="form-control date-picker-report_crud" value="<?php echo dateNow('month_date_report_crud'); ?>" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                  </div>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-3 col-md-2 col-lg-2">
                <div class="form-group">
                  <label>F. Fin</label>
                  <div class="input-group date" style="width: 100%;">
                    <input type="text" id="txt-Filtro_Fe_Fin" class="form-control date-picker-report_crud txt-Filtro_Fe_Fin" value="<?php echo dateNow('month_date_report_crud'); ?>" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                  </div>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-6 col-sm-3 col-md-2 col-lg-2">
                <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Consultar == 1) : ?>
                <div class="form-group">
                  <label>&nbsp;</label>
                  <button type="button" id="btn-filter" class="btn btn-primary btn-block"><i class="fa fa-search"></i> Buscar</button>
                </div>
                <?php endif; ?>
              </div>
              
              <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Agregar == 1) : ?>
                <div class="col-xs-6 col-sm-3 col-md-2 col-lg-2">
                  <label>&nbsp;</label>
                  <button type="button" class="btn btn-success btn-block" onclick="agregarAjusteInventario()"><i class="fa fa-plus-circle"></i> Agregar</button>
                </div>
                
                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                  <label class="hidden-xs hidden-sm">&nbsp;</label>
                  <button type="button" title="Importar Ajuste de Inventario por EXCEL" alt="Importar Ajuste de Inventario por EXCEL" class="btn btn-success btn-block" onclick="importarExcelAjusteInventario()"><i class="fa fa-file-excel-o color-white"></i> Importar Excel<span class="hidden-xs"> Ajuste</span></button>
                </div>
              <?php endif; ?>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="table-responsive div-Listar">
            <table id="table-AjusteInventario" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th>Almacén</th>
                  <th class="text-center">F. Ajuste</th>
                  <th>Cant. Item(s)</th>
                  <th class="no-sort">Ver ajuste</th>
                </tr>
              </thead>
            </table>
          </div>
          <!-- /.box-body -->
          
          <div class="box-body div-AgregarEditar">
            <h2 class="text-center" style="margin-top: 5px;"><label>Almacén: <?php echo $this->session->userdata['almacen']->No_Almacen; ?></label></h2>
            <?php
            $attributes = array('id' => 'form-AjusteInventario');
            echo form_open('', $attributes);
            ?>
              <div class="box-header box-header-new">
                <div class="row">
                  <br>
                  <div class="col-md-12">
                    <div class="callout callout-warning">
                      <p>
                        Se mostrarán solo los registros que generaron una COMPRA / VENTA / GUIA / STOCK INICIAL.<br>
                        Se guardarán si la columna <b>Stock Físico</b> tiene el número <b>0</b> o <b>mayor a cero, si se encuentran vacíos, estás no se procesarán.</b>
                      </p>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-12">
                    <label>Producto / Servicio</label>
                    <div class="form-group">
                      <input type="text" id="txt-Global_Filter_Producto" class="form-control" placeholder="Buscar por Nombre / Código / SKU" value="" autocomplete="off">
                    </div>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="table-responsive">
                <table id="table-AjusteInventarioAgregar" class="table table-striped table-bordered">
                  <thead>
                    <tr>
                      <th class="text-center">Codigo</th>
                      <th class="text-center">Nombre</th>
                      <th class="text-center">Stock Actual</th>
                      <th class="text-center">Stock Físico</th>
                      <th class="text-center">Diferencia</th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
                </div>
              </div>
          	  <div class="row">
          	    <br>
                      
                <div class="col-xs-12 col-sm-12 col-md-12">
                  <label>Movimiento Inventario</label>
                  <div class="form-group">
                    <label style="font-weight:normal; cursor:pointer;"><input type="radio" style="cursor:pointer;" id="radio-ajuste" class="flat-red" name="radio-tipo_movimiento_inventario" value="19" checked>&nbsp; AJUSTE POR DIFERENCIA DE INVENTARIO</label>
                    &nbsp;&nbsp;<label style="font-weight:normal; cursor:pointer;"><input type="radio" style="cursor:pointer;" id="radio-ajuste_error" class="flat-red" name="radio-tipo_movimiento_inventario" value="21">&nbsp; ERROR DE SISTEMA AJUSTE POR DIFERENCIA DE INVENTARIO</label>
                  </div>
                </div>
                
                <div class="col-xs-6 col-md-6">
                  <div class="form-group">
                    <button type="button" id="btn-cancelar" class="btn btn-danger btn-md btn-block"><span class="fa fa-close"></span> Cancelar (ESC)</button>
                  </div>
                </div>
                <div class="col-xs-6 col-md-6">
                  <div class="form-group">
                    <button type="button" id="btn-procesar_ajuste" class="btn btn-success btn-block" onclick="guardarAjusteInventario()"><i class="fa fa-save"></i> Procesar Ajuste</button>
                  </div>
                </div>
              </div>
            <?php echo form_close(); ?>
          </div>
          <!-- /.box-body -->
          
          <div class="box-body div-Ver">
            <div class="row">
              <div class="col-xs-12 col-md-12">
                <h4 id="h4-title-ver_ajuste_inventario" class="text-center"></h4>
              </div>
              <table id="table-AjusteInventarioVer" class="table table-striped table-bordered">
                <thead>
                  <tr>
                    <th class="text-center">Codigo</th>
                    <th class="text-center">Nombre</th>
                    <th class="text-center">Diferencia</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
            <div class="row">
              <br>
              <div class="col-xs-12 col-md-12">
                <div class="form-group">
                  <button type="button" id="btn-cancelar_ver_ajuste_inventario" class="btn btn-danger btn-md btn-block"><span class="fa fa-close"></span> Cancelar (ESC)</button>
                </div>
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

<!-- Importar Productos -->
<div class="modal fade modal_importar_excel_ajuste_inventario" id="modal-default">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
        <form name="importa" method="post" action="<?php echo base_url(); ?>Logistica/AjusteInventarioController/importarExcelAjusteInventario" enctype="multipart/form-data">
          <div class="row">
    				<div class="col-sm-12 text-center">
              <h3>Importación de Excel Ajuste de Inventario</h3>
            </div>
            
            <div class="col-md-12"><br>
              <div class="well well-sm">
                <i class="fa fa-warning"></i> Indicaciones:
                <br>&nbsp;
                <br>- El formato requerido es <b>.xlsx</b>
                <br>- El archivo <b>.xlsx</b> no debe contener estilos, gráficos o fórmulas
                <br>- No guardar la plantilla porque el formato puede ser actualizado sin previo aviso
                <br>&nbsp;
                <a id="a-download-ajuste_inventario" href="<?php echo base_url(); ?>DownloadController/download/Laesystems_Plantilla_Ajuste_Inventario.xlsx" class="btn btn-success btn-md btn-block"><span class="fa fa-cloud-download"></span> Descargar plantilla</a>
              </div>
            </div>
              
    				<div class="col-sm-12">
              <label>Archivo</label>
    				  <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-cloud-upload" aria-hidden="true"></i></span>
                  <label class="btn btn-default" for="my-file-selector-ajuste_inventario" style="width: 100%;">
                    <input type="file" id="my-file-selector-ajuste_inventario" name="excel-archivo-ajuste_inventario" multiple=false accept=".xlsx" required style="display:none" onchange="$('#upload-file-info').html(this.files[0].name)">Buscar archivo ...
                  </label>
                  <span class='label label-info' id="upload-file-info"></span>
                </div>
                <span class="help-block" id="error"></span>
              </div>
            </div>
            
            <div class="col-xs-6 col-md-6"><br>
              <div class="form-group">
                <button type="button" id="btn-cancel-ajuste_inventario" class="btn btn-danger btn-md btn-block" data-dismiss="modal">Salir</button>
              </div>
            </div>
            
            <div class="col-xs-6 col-md-6"><br>
              <div class="form-group">
                <button type="submit" id="btn-excel-importar_excel_ajuste_inventario" class="btn btn-success btn-md btn-block" onclick="submit();"><span class="fa fa-cloud-upload"></span> Procesar Excel</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- /.modal Importar productos -->

<?php
  $i=0;
  if ( !empty($sStatusExcel) ){
    $i=1;
    $sClassModal = 'success';
    $sMessage = 'Datos cargados satisfactoriamente';
    if ( $sStatusExcel == 'error-archivo_no_existe' ) {
      $sMessage = 'El archivo no existe';
      $sClassModal = 'danger';  
    } else if ( $sStatusExcel == 'error-copiar_archivo' ) {
      $sMessage = 'Error al copiar archivo al servidor';
      $sClassModal = 'danger';  
    }
  ?>
  <div class="modal fade in modal-<?php echo $sClassModal; ?>" id="modal-message_excel" role="dialog" style="display: block;">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"><?php echo $sMessage; ?></h4>
        </div>
        <div class="modal-footer">
          <button type="button" id="btn-cerrar_modal_excel" class="btn btn-outline pull-right" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>
<?php } ?>