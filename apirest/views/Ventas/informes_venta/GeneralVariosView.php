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
              <br class="hidde-xs">
            	<div class="col-md-12">
                <div class="row div-Filtros">
        					<div class="col-md-4 hidden-print">
        						<div class="form-group">
        							<label>Reporte actual</label>
        							<select id="sltReporte" class="form-control">
        								<optgroup label="Reportes por Periodo">
        									<option value="1">Reporte Venta Diario</option>
        									<option value="2">Reporte Venta Mensual</option>
        									<option value="3">Reporte Venta Anual</option>
        								</optgroup>
        								<optgroup label="Movimiento de su Negocio">
        									<option value="5">Top de Clientes</option>
        									<option value="4">Top de Productos</option>
        								</optgroup>
        								<optgroup label="Análisis de Negocio">
        									<option value="6">Rentabilidad de Producto Trimestral</option>
        								</optgroup>
        							</select>
        						</div>					
        					</div>
        				</div>
        				<hr class="hidden-xs hidden-sm">
            	</div>
            </div>
          </div>
          <!-- /.box-header-new -->
          <div class="row div-Filtros">
  					<div class="col-md-12">
  						<div id="dvReporte"></div>
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