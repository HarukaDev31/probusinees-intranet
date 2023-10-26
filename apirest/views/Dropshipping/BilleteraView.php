<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">  
<?php
$sCssDisplayRoot='style="display:none"';
if ( $this->user->No_Usuario == 'root' ){
  $sCssDisplayRoot='';
}
?>
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
          <div class="box-header box-header-new"><!-- div-Listar -->
            <div class="row div-Filtros">
              <br>
              <?php
              if ( $this->user->No_Usuario == 'root' ){ ?>
              <div class="col-md-6">
                <label>Empresa</label>
                <div class="form-group">
                  <select id="cbo-filtro_empresa" name="ID_Empresa" class="form-control select2 required" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              <div class="col-md-6">
                <label>Organización</label>
                <div class="form-group">
                  <select id="cbo-filtro_organizacion" name="ID_Organizacion" class="form-control select2 required" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              <?php } else { ?>
                <input type="hidden" id="cbo-filtro_empresa" name="ID_Empresa" class="form-control" value="<?php echo $this->user->ID_Empresa; ?>">
                <input type="hidden" id="cbo-filtro_organizacion" name="ID_Organizacion" class="form-control" value="<?php echo $this->user->ID_Organizacion; ?>">
              <?php } ?>
              
              <div class="col-md-6">
                <div class="alert alert-default alert-dismissible" style="background-color: #ccccccab;">
                  <div class="row">
                    <div class="col-md-12">
                      <span style="font-size: 2rem; font-weight:bold"><i class="icon fa fa-money"></i>Mi saldo </span>es <span style="font-size: 2rem; font-weight:bold; color: #00a65a">S/ 1, 000.00</span>
                      <br><span>Dinero para solicitar desembolso</span>
                      <button type="button" id="btn-desembolsar" class="btn btn-success"onclick="solicitarDesembolso()">desembolsar</button>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="alert alert-default alert-dismissible" style="background-color: #ccccccab;">
                  <div class="row">
                    <div class="col-md-12">
                      <span style="font-size: 2rem; font-weight:bold"><i class="icon fa fa-up"></i>Mis Ingresos </span>son <span style="font-size: 2rem; font-weight:bold; color: #00a65a">S/ 2, 000.00</span>
                      <br>
                      <span style="font-size: 2rem; font-weight:bold"><i class="icon fa fa-up"></i>Mis Egresos </span>son <span style="font-size: 2rem; font-weight:bold; color: #c40000">S/ 1, 000.00</span>
                      <br>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-3 hidden">
                <div class="form-group">
    		  				<select id="cbo-Filtros_Sistemas" name="Filtros_Sistemas" class="form-control">
    		  				  <option value="Sistema">Nombre Dominio</option>
    		  				</select>
                </div>
              </div>
              
              <div class="col-md-6 hidden">
                <div class="form-group">
                  <input type="text" id="txt-Global_Filter" name="Global_Filter" class="form-control" placeholder="Buscar" value="" autocomplete="off">
                </div>
              </div>
              
              <div class="col-md-12">
                <button type="button" class="btn btn-success btn-block" onclick="agregarCuentaBancaria()">Crear cuenta bancaria</button>
              </div>
            </div>
          </div>

          <!-- /.box-header -->
          <div class="table-responsive div-Listar">
            <h4 class="text-left"><strong>Lista de Cuenta Bancarias</strong></h4>
            <table id="table-Sistema" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <?php if ( $this->user->No_Usuario == 'root' ){ ?>
                  <th>Empresa</th>
                  <?php } ?>
                  <th>Banco</th>
                  <th>Tipo</th>
                  <th>Moneda</th>
                  <th>Cuenta Bancaria</th>
                  <th>CCI</th>
                  <th>Titular</th>
                  <th>Estado</th>
                  <th>Editar</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
        <!-- /.box -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->

    <div class="row">
      <div class="col-xs-12 col-sm-6">
        <div class="box box-content">
          <!-- /.box-header -->
          <div class="table-responsive div-Listar">
            <h4 class="text-left"><strong>Lista de Desembolsos Pendientes</strong></h4>
            <table id="table-desembolsos" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <?php if ( $this->user->No_Usuario == 'root' ){ ?>
                  <th>Empresa</th>
                  <?php } ?>
                  <th>ID</th>
                  <th>Cuenta</th>
                  <th>Importe</th>
                  <th>Fecha</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
        <!-- /.box -->
      </div>
      <!-- /.col -->

      <div class="col-xs-12 col-sm-6">
        <div class="box box-content">
          <!-- /.box-header -->
          <div class="table-responsive div-Listar">
            <h4 class="text-left"><strong>Lista de Desembolsos Completados</strong></h4>
            <table id="table-pagos" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <?php if ( $this->user->No_Usuario == 'root' ){ ?>
                  <th>Empresa</th>
                  <?php } ?>
                  <th>ID</th>
                  <th>Cuenta</th>
                  <th>Importe</th>
                  <th>Fecha</th>
                </tr>
              </thead>
            </table>
          </div>
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

<div class="modal fade" id="modal-cuenta_bancaria" role="dialog">
  <?php $attributes = array('id' => 'form-cuenta_bancaria'); echo form_open('', $attributes); ?>
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title text-center">Cuenta Bancaria</h4>
        </div>
        
        <div class="modal-body">
          <input type="hidden" name="EID_Cuenta_Bancaria_Billetera" class="form-control">
          
          <!--<div class="row" <?php echo $sCssDisplayRoot; ?>>-->
          <div class="row">
            <div class="col-xs-12 col-md-12">
              <label>Empresa <span class="label-advertencia">*</span></label>
              <div class="form-group">
                <select id="cbo-Empresas" name="ID_Empresa" class="form-control select2 required" style="width: 100%;"></select>
                <span class="help-block" id="error"></span>
              </div>
            </div>

            <div class="col-xs-12 col-md-4">
              <label>Banco <span class="label-advertencia">*</span></label>
              <div class="form-group">
                <select id="cbo-banco" name="ID_Banco" class="form-control required" style="width: 100%;"></select>
                <span class="help-block" id="error"></span>
              </div>
            </div>

            <div class="col-xs-12 col-md-4">
              <label>Tipo <span class="label-advertencia">*</span></label>
              <div class="form-group">
                <select id="cbo-tipo_cuenta" name="Nu_Tipo_Cuenta" class="form-control required" style="width: 100%;"></select>
                <span class="help-block" id="error"></span>
              </div>
            </div>

            <div class="col-xs-12 col-md-4">
              <label>Moneda <span class="label-advertencia">*</span></label>
              <div class="form-group">
                <select id="cbo-moneda" name="ID_Moneda" class="form-control required" style="width: 100%;"></select>
                <span class="help-block" id="error"></span>
              </div>
            </div>

            <div class="col-xs-12 col-md-6">
              <label id="label-nro_cuenta">Nro. de Cuenta <span class="label-advertencia">*</span></label>
              <div class="form-group">
                <input type="text" id="txt-No_Cuenta_Bancaria" name="No_Cuenta_Bancaria" placeholder="Obligatorio" class="form-control required" autocomplete="off" maxlength="30">
                <span class="help-block" id="error"></span>
              </div>
            </div>

            <div class="col-xs-12 col-md-6 div-cci">
              <label>CCI <span class="label-advertencia">*</span></label>
              <div class="form-group">
                <input type="text" id="txt-No_Cuenta_Interbancario" name="No_Cuenta_Interbancario" placeholder="Obligatorio" class="form-control" autocomplete="off" maxlength="50">
                <span class="help-block" id="error"></span>
              </div>
            </div>

            <div class="col-xs-12 col-md-12">
              <label>Titular <span class="label-advertencia">*</span></label>
              <div class="form-group">
                <input type="text" id="txt-No_Titular_Cuenta" name="No_Titular_Cuenta" placeholder="Obligatorio" class="form-control required" autocomplete="off" maxlength="100">
                <span class="help-block" id="error"></span>
              </div>
            </div>
          </div>
          
          <br>

          <div class="row">
            <div class="col-xs-6 col-md-6">
              <div class="form-group">
                <button type="button" id="btn-cancelar" class="btn btn-danger btn-lg btn-block">Cancelar</button>
              </div>
            </div>
            <div class="col-xs-6 col-md-6">
              <div class="form-group">
                <button type="submit" id="btn-save" class="btn btn-success btn-lg btn-block btn-verificar">Guardar</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php echo form_close(); ?>
</div>