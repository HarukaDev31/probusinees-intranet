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
      <!-- ./New box-header -->
    </div>
    
    <div class="row">
      <div class="col-xs-12">
        <div class="box box-content">
          <!-- box-header -->
          <div class="box-header box-header-new">
            <div class="col-md-12">
          		<div class="page-header">
          			<h1>
          				Copias de seguridad <small>estas permiten respaldar su información.</small>
          				<button id="btn-generar_backup" class="pull-right btn btn-default">
          					<i class="red bigger glyphicon glyphicon-compressed"></i> Respaldar
          				</button>
          			</h1>
          		</div>
            </div>
          </div>
          <!-- /.box-header -->
    			<?php if($copias['response'] != "success"): ?>
    				<div class="alert alert-danger text-center">
    					<?php echo $copias['message']; ?>
    				</div>
    			<?php endif; ?>
    			<?php if($copias['response']== "success"): ?>
    				<div class="alert alert-info text-center">
    					Se <b>recomienda</b> que haga este proceso los <b>fines de semana/mes</b> siempre y cuando no se este usando el sistema.
    					<br />El sistema <b>creara una copia de seguridad</b> para que pueda respaldar su información y mantenerla a salvo.
    				</div>
            <div class="table-responsive">
      				<table id="table-respaldar" class="table table-striped table-bordered">
      					<thead>
      						<tr>
      							<th>Respaldo</th>
      							<th style="width:200px;">Fecha</th>
      						</tr>
      					</thead>
      					<tbody>
      						<?php if(count($copias['result']) == 0): ?>
      							<tr>
      								<td colspan="2" class="text-center">
      									No se han encontrado copias de seguridad.
      								</td>
      							<tr>
      						<?php endif; ?>
      						<?php if(count($copias['result']) > 0): ?>
      							<?php foreach($copias['result'] as $r): ?>
      								<tr>
      									<td>
      										<a href="<?php echo base_url('respaldos/' .$r->Archivo); ?> ">
      											<?php echo $r->Archivo; ?>
      										</a>
      									</td>
      									<td><?php echo $r->Fecha; ?></td>
      								</tr>
      							<?php endforeach; ?>
      						<?php endif; ?>
      					</tbody>
      				</table>
      			</div>
		      <?php endif; ?>
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