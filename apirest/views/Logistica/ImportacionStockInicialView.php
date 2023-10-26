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
    <?php
    if ( !empty($sStatus) ){
      $sClassModal = 'success';
      $sMessage = 'Datos cargados satisfactoriamente';
      if ( (int)$iCantidadNoProcesados > 0 ){
        $sMessage .= '. Pero tiene ' . $iCantidadNoProcesados . ' registro(s) no procesados';
      }
      if ( $sStatus == 'error-sindatos' ) {
        $sMessage = 'Llenar los campos obligatorios o los valores no son iguales a las columna del excel';
        $sClassModal = 'danger';  
      } else if ( $sStatus == 'error-bd' ) {
        $sMessage = quitarCaracteresEspeciales($sMessageErrorBD);
        $sClassModal = 'danger';  
      } else if ( $sStatus == 'error-archivo_no_existe' ) {
        $sMessage = 'El archivo no existe';
        $sClassModal = 'danger';  
      } else if ( $sStatus == 'error-copiar_archivo' ) {
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
    <div class="row">
      <div class="col-xs-12">
        <div class="box box-content">
          <!-- box-header -->
          <div class="box-header box-header-new div-Listar">
            <div class="row div-Filtros">
              <br>
              <h2 class="text-center" style="margin-top: 5px;"><label>Almacén: <?php echo $this->session->userdata['almacen']->No_Almacen; ?></label></h2>
              
              <div class="col-md-3">
                <div class="form-group">
    		  				<select id="cbo-Filtros_Productos" name="Filtros_Productos" class="form-control">
    		  				  <option value="Producto">Nombre Producto</option>
    		  				  <option value="CodigoBarra">Código</option>
    		  				</select>
                </div>
              </div>
              
              <div class="col-md-5">
                <div class="form-group">
                  <input type="text" id="txt-Global_Filter" name="Global_Filter" class="form-control" maxlength="250" placeholder="Buscar" value="" autocomplete="off">
                </div>
              </div>

              <div class="col-md-4">
                <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Agregar == 1) : ?>
                  <button type="button" class="btn btn-success btn-block" onclick="importarExcelStockInicialProductos()"><i class="fa fa-file-excel-o color_white"></i> Importar stock</button>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="table-responsive div-Listar">
            <table id="table-ImportacionStockInicial" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th class="no-sort">Almacén</th>
                  <th class="no-sort">F. Emisión</th>
                  <!--<th class="no-sort">Operación</th>-->
                  <!--<th>Movimiento</th>-->
                  <!--<th>Proveedor</th>-->
                  <th class="no-sort">Código</th>
                  <th class="no-sort_left">Producto</th>
                  <!--<th class="sort_right">Precio</th>-->
                  <th class="no-sort">Stock</th>
                </tr>
              </thead>
            </table>
          </div>
          
          <!-- Importar Productos -->
          <div class="modal fade modal_importar_stock_inicial_productos" id="modal-default">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-body">
                  <form id="form-importar_stock_inicial_productos" name="importar_stock_inicial_productos" method="post" action="<?php echo base_url(); ?>Logistica/ImportacionStockInicialController/importarExcelStockInicialProductos" enctype="multipart/form-data">
                    <div class="row">
                      <div class="col-sm-12 text-center">
                        <h3>Importación de Stock Inicial Productos</h3>
                      </div>
                      
                      <div class="col-md-12"><br>
                        <div class="well well-sm">
                          <i class="fa fa-warning"></i> Indicaciones:
                          <br>&nbsp;
                          <br>- El formato requerido es <b>.xlsx</b>
                          <br>- El archivo <b>.xlsx</b> no debe contener estilos, gráficos o fórmulas
                          <br>- La plantilla que se debe utilizar es la siguiente, dar clic en el siguiente botón
                          <br>&nbsp;
                          <a id="a-download-product" href="<?php echo base_url(); ?>DownloadController/download/Ecxpresslae_Plantilla_Stock_Inicial_Productos.xlsx" class="btn btn-success btn-md btn-block"><span class="fa fa-cloud-download"></span> Descargar plantilla</a>
                        </div>
                      </div>
                        
                      <div class="col-sm-12">
                        <label>Archivo</label>
                        <div class="form-group">
                          <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-cloud-upload" aria-hidden="true"></i></span>
                            <label class="btn btn-default" for="my-file-selector" style="width: 100%;">
                              <input type="file" id="my-file-selector" name="excel-archivo_stock_inicial_productos" multiple=false accept=".xlsx" required style="display:none" onchange="$('#upload-file-info').html(this.files[0].name)">Buscar...
                            </label>
                            <span class='label label-info' id="upload-file-info"></span>
                          </div>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-6 col-md-6">
                        <div class="form-group">
                          <button type="button" id="btn-cancel-product" class="btn btn-danger btn-md btn-block" data-dismiss="modal"><span class="fa fa-sign-out"></span> Cancelar</button>
                        </div>
                      </div>
                      
                      <div class="col-xs-6 col-md-6">
                        <div class="form-group">
                          <button type="submit" id="btn-excel-importar_stock_inicial_productos" class="btn btn-success btn-md btn-block" onclick="submit();"><span class="fa fa-cloud-upload"></span> Subir excel</button>
                        </div>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <!-- /.modal Importar stock_inicial_productos -->

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