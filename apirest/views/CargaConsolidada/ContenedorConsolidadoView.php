<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-8">
          <h1>
            <i class="<?php echo $this->MenuModel->verificarAccesoMenuCRUD()->Txt_Css_Icons; ?>" aria-hidden="true"></i> <span id="section-title"><?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?></span>
            &nbsp;<span id="span-id_pedido" class="badge badge-secondary"></span>
          </h1>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>
   <section class="content">
    
    <div class="container-fluid table-almacen">
    <div class="row">
      <div class="col-6 col-sm-3">
                  <label>F. Inicio <span class="label-advertencia text-danger"> *</span></label>
                  <div class="form-group">
                    <input type="text" id="txt-Fe_Inicio" class="form-control input-report required" value="<?php echo dateNow('month_date_ini_report'); ?>">
                    <span class="help-block text-danger" id="error"></span>
                  </div>
                </div>
                <div class="col-6 col-sm-3">
                  <label>F. Fin <span class="label-advertencia text-danger"> *</span></label>
                  <div class="form-group">
                    <input type="text" id="txt-Fe_Fin" class="form-control input-report required" value="<?php echo dateNow('fecha_actual_dmy'); ?>">
                    <span class="help-block text-danger" id="error"></span>
                  </div>
                </div>
                <div class="col-6 col-sm-3">
                  <label>Estado</label>
                  <select id="txt-ID_Estado" name="ID_Estado" class="form-control input-estado">
                    <option value="0" selected>Todos</option>
                    <option value="PENDIENTE">PENDIENTE</option>
                    <option value="RECIBIENDO">RECIBIENDO</option>
                    <option value="COMPLETADO">COMPLETADO</option>
                  </select>
                </div>
                <div class="col-6 col-sm-3">
                  <label>&nbsp;</label>
                  <button type="button" id="btn-html_reporte" class="btn btn-primary btn-block btn-reporte" data-type="html"><i class="fa fa-search"></i> Buscar</button>
                </div>
      </div>
      <div class="table-responsive div-Listar">
        <table id="table-contenedor" class="table table-bordered table-hover table-striped">
          <thead class="thead-light">
            <tr>
              <th>Mes  </th>
              <th>Pais</th>
              <th>Carga</th>
              <th>F. Puerto</th>
              <th>F. Entrega</th>
              <th>Empresa</th>
              <th>Ver</th>
              <th>Estado</th>
             
            </tr>
          </thead>
        </table>
      </div>
    </div>
    </section>

 
</div>
<style >



</style>