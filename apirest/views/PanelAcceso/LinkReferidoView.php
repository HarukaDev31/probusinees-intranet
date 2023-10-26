<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">  
  <!-- Main content -->
  <section class="content">
    <!-- New box-header -->
    <div class="row">
      <div class="col-xs-12">
        <div class="div-content-header">
          <h3>
            <i class="fa fa-link" aria-hidden="true"></i> Link Referido
            <?php if($this->user->ID_Pais == 1) { ?>
            <span style="font-size: 1.5rem;font-weight: normal;"> (Gana S/ 1 por cada pedido entregado de tus referidos cuando generen más de 25 pedidos)</span>
            <?php } ?>
            <?php if($this->user->ID_Pais == 2) { ?>
            <span style="font-size: 1.5rem;font-weight: normal;"> (Gana 1% por cada pedido entregado después de tús 50 referidos)</span>
            <?php } ?>
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
            <div class="row">
              <div class="col-md-12">
                <div class="callout callout-success" style="color: #ffffff !important;background-color: #0d6efd !important;border-color: #0d6efd;margin-bottom: 0px;" class="btn btn-lg btn-primary btn-primary-v2 btn-block">
                  <span id="span-url_tienda" style="font-size: 1.9rem; font-weight:bold"><?php echo $link_referido . '  '; ?></span>
                  &nbsp;&nbsp;
                  <button type="button" class="btn btn-default">
                    <span id="span-compartir_url_tienda" style="font-size: 1.75rem; cursor:pointer" onclick="copyText()"><i class="fa fa-share-alt" aria-hidden="true"></i> Compartir</span>
                  </button>
                </div>
              </div>
            </div>

            <div class="row div-Filtros">
              <?php
              if ( $this->user->No_Usuario == 'root' ){ ?>
              <div class="col-md-6">
                <label>Empresa</label>
                <div class="form-group">
                  <select id="cbo-filtro_empresa" class="form-control select2" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              <div class="col-md-6">
                <label>Organización</label>
                <div class="form-group">
                  <select id="cbo-filtro_organizacion" class="form-control select2" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              <?php } else { ?>
                <input type="hidden" id="cbo-filtro_empresa" class="form-control" value="<?php echo $this->user->ID_Empresa; ?>">
                <input type="hidden" id="cbo-filtro_organizacion" class="form-control" value="<?php echo $this->user->ID_Organizacion; ?>">
              <?php } ?>

              <div class="col-md-3">
                <label class="hidden-xs hidden-sm">&nbsp;</label>
                <div class="form-group">
                  <select id="cbo-Filtros_Usuario" name="Filtros_Usuario" class="form-control">
                    <option value="Usuario">Referido</option>
                  </select>
                </div>
              </div>
              
              <div class="col-md-6">
                <label class="hidden-xs hidden-sm">&nbsp;</label>
                <div class="form-group">
                  <input type="text" id="txt-Global_Filter" name="Global_Filter" class="form-control" maxlength="100" placeholder="Buscar" value="">
                </div>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="table-responsive">
            <table id="table-Usuario" class="table table-striped table-bordered">
              <thead>
              <tr>
                <th>Empresa</th>
                <th>Referido</th>
                <th>F. Registro</th>
              </tr>
              </thead>
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