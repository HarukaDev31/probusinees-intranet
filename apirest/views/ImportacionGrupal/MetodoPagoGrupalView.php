<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-8">
          <h1><i class="<?php echo $this->MenuModel->verificarAccesoMenuCRUD()->Txt_Css_Icons; ?>" aria-d-none="true"></i> <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?></h1>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <div class="table-responsive div-Listar">
                <table id="table-MedioPago" class="table table-striped table-bordered">
                  <thead>
                  <tr>
                    <?php if ( $this->user->No_Usuario == 'root' ){ ?>
                    <th>Empresa</th>
                    <?php } ?>
                    <!--<th>Nombre Interno</th>-->
                    <th>Nombre</th>
                    <th class="sort_left">Acción</th>
                    <!--<th class="sort_left">Cierre de venta</th>-->
                    <th class="no-sort">Estado</th>
                    <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Editar == 1) : ?>
                      <th class="no-sort">Editar</th>
                    <?php endif; ?>
                    <th class="no-sort">Configurar</th>
                  </tr>
                  </thead>
                </table>
              </div>

              <!-- Nro. de Cuentas bancarias -->
              <div class="box box-content">
                <br><br>
                <!-- box-header -->
                <div class="box-header box-header-new">
                  <div class="row div-Filtros">
                    <div class="col-md-12">
                      <div class="form-group">
                        <h3>Configuración Cuentas Bancarias</h3>
                        <span>Solo se mostrarán los medios de pago con <b>Tipo de Pago</b> -> Pago por Transferencia</span>.
                      </div>
                    </div>
                    <?php
                    if ( $this->user->No_Usuario == 'root' ){ ?>
                    <div class="col-md-12">
                      <label>Empresa</label>
                      <div class="form-group">
                        <select id="cbo-filtro_empresa" name="ID_Empresa" class="form-control"  style="width: 100%;"></select>
                        <span class="help-block text-danger" id="error"></span>
                      </div>
                    </div>
                    <?php } else { ?>
                      <input type="hidden" id="cbo-filtro_empresa" name="ID_Empresa" class="form-control" value="<?php echo $this->user->ID_Empresa; ?>">
                    <?php } ?>
                  </div>
                </div>
                <!-- /.box-header -->
                <div class="table-responsive">
                  <table id="table-MedioPago-CuentasBancarias" class="table table-striped table-bordered">
                    <thead>
                    <tr>
                      <?php if ( $this->user->No_Usuario == 'root' ){ ?>
                      <th>Empresa</th>
                      <?php } ?>
                      <th>Nombre Pago Tienda</th>
                      <th>Banco</th>
                      <th>Tipo de cuenta</th>
                      <th>Moneda</th>
                      <th>Titular</th>
                      <th>Número de Cuenta</th>
                      <th>CCI</th>
                      <th class="no-sort">Editar</th>
                      <th class="no-sort">Eliminar</th>
                    </tr>
                    </thead>
                  </table>
                </div>
                <!-- /.box-body -->
              </div>
              <!-- /.box -->
              <!-- /.Nro. Cuentas Bancarias -->
            </div><!--card-body-->
          </div><!--card-->
        </div><!--col-12-->
      </div><!--row-->
    </div><!--container-->
  </section>
</div><!--div general-->

<!-- Modal -->
<?php
$attributes = array('id' => 'form-MedioPago');
echo form_open('', $attributes);
?>
<div class="modal fade" id="modal-MedioPago" role="dialog">
<div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header">
      <h4 class="modal-title text-center"></h4>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-d-none="true">&times;</span></button>
    </div>
    
    <div class="modal-body">
      <input type="hidden" name="EID_Empresa" class="form-control">
      <input type="hidden" name="EID_Medio_Pago" class="form-control">
      <input type="hidden" name="ENo_Medio_Pago_Tienda_Virtual" class="form-control">
      
      <div class="row">
        <?php
        if ( $this->user->No_Usuario == 'root' ){ ?>
        <div class="col-xs-12 col-md-12">
          <div class="form-group">
            <label>Empresa <span class="label-advertencia">*</span></label>
            <select id="cbo-Empresas" name="ID_Empresa" class="form-control required" style="width: 100%;"></select>
            <span class="help-block text-danger" id="error"></span>
          </div>
        </div>
        <?php } else { ?>
          <input type="hidden" id="cbo-Empresas" name="ID_Empresa" class="form-control" value="<?php echo $this->user->ID_Empresa; ?>">
        <?php } ?>
        
        <div class="col-xs-4 col-md-6 d-none">
          <div class="form-group">
            <label>Nombre Interno<span class="label-advertencia">*</span></label>
            <input type="text" id="txt-No_Medio_Pago" name="No_Medio_Pago" placeholder="Ingresar nombre" class="form-control required" autocomplete="off" maxlength="50">
            <span class="help-block text-danger" id="error"></span>
          </div>
        </div>

        <div class="col-xs-6 col-md-6">
          <div class="form-group">
            <label>Nombre Pago<span class="label-advertencia">*</span></label>
            <input type="text" id="txt-No_Medio_Pago_Tienda_Virtual" name="No_Medio_Pago_Tienda_Virtual" placeholder="Ingresar nombre" class="form-control required" autocomplete="off" maxlength="50">
            <span class="help-block text-danger" id="error"></span>
          </div>
        </div>

        <div class="col-xs-4 col-md-5 d-none">
          <div class="form-group">
            <label>Descripción <span class="label-advertencia">*</span></label>
            <input type="text" id="txt-Txt_Medio_Pago" name="Txt_Medio_Pago" placeholder="Ingresar nombre" class="form-control required" autocomplete="off" maxlength="250">
            <span class="help-block text-danger" id="error"></span>
          </div>
        </div>
        
        <div class="col-xs-5 col-md-3 d-none">
          <div class="form-group">
            <label>SUNAT Codigo <span class="label-advertencia">*</span></label>
            <input type="text" name="No_Codigo_Sunat_PLE" placeholder="Ingresar codigo" class="form-control required" autocomplete="off" maxlength="3">
            <span class="help-block text-danger" id="error"></span>
          </div>
        </div>

        <div class="col-xs-6 col-md-4 d-none">
          <div class="form-group">
            <label>Proveedor FE Codigo <span class="label-advertencia"> *</span></label>
            <input type="text" name="No_Codigo_Sunat_FE" placeholder="Ingresar número" class="form-control required" autocomplete="off" maxlength="3">
            <span class="help-block text-danger" id="error"></span>
          </div>
        </div>
        
        <div class="col-xs-5 col-md-3 d-none">
          <div class="form-group">
            <label>Tipo Vista <span class="label-advertencia">*</span></label>
            <input type="tel" name="Nu_Tipo" placeholder="Ingresar codigo" class="form-control required" autocomplete="off" maxlength="3">
            <span class="help-block text-danger" id="error"></span>
          </div>
        </div>
        
        <div class="col-xs-5 col-md-3 d-none">
          <div class="form-group">
            <label>Dinero Caja PV <span class="label-advertencia">*</span></label>
            <select id="cbo-dinero_caja_pv" name="Nu_Tipo_Caja" class="form-control required" style="width: 100%;"></select>
            <span class="help-block text-danger" id="error"></span>
          </div>
        </div>
        
        <div class="col-xs-5 col-md-2 d-none">
          <div class="form-group">
            <label>Orden</label>
            <input type="tel" name="Nu_Orden" placeholder="Ingresar" class="form-control" autocomplete="off" maxlength="3">
            <span class="help-block text-danger" id="error"></span>
          </div>
        </div>
        
        <div class="col-xs-6 col-md-6">
          <div class="form-group">
            <label>Tipo de Pago</label>
            <select id="cbo-tipo_forma_pago" name="Nu_Tipo_Forma_Pago_Lae_Shop" class="form-control required" style="width: 100%;"></select>
            <span class="help-block text-danger" id="error"></span>
          </div>
        </div>
        
        <div class="col-xs-6 col-md-6">
          <div class="form-group">
            <label>Cierre de Venta</label>
            <select id="cbo-cierre_venta_pago" name="Nu_Cierre_Venta_Pago_Lae_Shop" class="form-control required" style="width: 100%;"></select>
            <span class="help-block text-danger" id="error"></span>
          </div>
        </div>

        <div class="col-xs-6 col-md-6">
          <div class="form-group">
            <label>Estado</label>
            <select id="cbo-Estado" name="Nu_Estado" class="form-control required" style="width: 100%;"></select>
            <span class="help-block text-danger" id="error"></span>
          </div>
        </div>
        
        <div class="col-xs-12 col-md-12 div-mercado_pago">
          <div class="form-group">
            <label>Public Key</label>
            <input type="password" id="txt-Txt_Pasarela_Pago_Key" name="Txt_Pasarela_Pago_Key" placeholder="" class="form-control pwd" autocomplete="off">
            <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
            <span class="help-block text-danger" id="error"></span>
          </div>
        </div>
        
        <div class="col-xs-12 col-md-12 div-mercado_pago">
          <div class="form-group">
            <label>Access Token</label>
            <input type="password" id="txt-Txt_Pasarela_Pago_Token" name="Txt_Pasarela_Pago_Token" placeholder="" class="form-control pwd" autocomplete="off">
            <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
            <span class="help-block text-danger" id="error"></span>
          </div>
        </div>
      </div>
    </div>
    
    <div class="modal-footer">
      <div class="row">
        <div class="col-xs-6 col-md-6">
          <div class="form-group">
            <button type="button" class="btn btn-danger btn-md btn-block" data-dismiss="modal">Salir</button>
          </div>
        </div>
        <div class="col-xs-6 col-md-6">
          <div class="form-group">
            <button type="submit" id="btn-save" class="btn btn-success btn-md btn-block btn-verificar">Guardar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
<?php echo form_close(); ?>
<!-- /.Modal -->

<!-- Modal Cuentas Bancarias -->
<?php
$attributes = array('id' => 'form-MedioPago-CuentasBancarias');
echo form_open('', $attributes);
?>
<div class="modal fade" id="modal-MedioPago-CuentasBancarias" role="dialog">
<div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header">
      <h4 class="modal-title text-center"></h4>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-d-none="true">&times;</span></button>
    </div>
    
    <div class="modal-body">
      <input type="hidden" name="EID_Cuenta_Bancaria" class="form-control">
      <input type="hidden" name="EID_Medio_Pago" class="form-control"><!-- para agregar y editar-->
      
      <div class="row">
        <?php
        if ( $this->user->No_Usuario == 'root' ){ ?>
        <div class="col-xs-12 col-md-12">
          <div class="form-group">
            <label>Empresa <span class="label-advertencia">*</span></label>
            <select id="cbo-Empresas" name="ID_Empresa" class="form-control required" style="width: 100%;"></select>
            <span class="help-block text-danger" id="error"></span>
          </div>
        </div>
        <?php } else { ?>
          <input type="hidden" id="cbo-Empresas" name="ID_Empresa" class="form-control" value="<?php echo $this->user->ID_Empresa; ?>">
        <?php } ?>
        
        <div class="col-xs-4 col-md-5">
          <div class="form-group">
            <label>Banco</label>
            <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Si no muestra BANCOS es porque la opción Configuración > Tipo Medio de Pago, no tiene configurado los BANCOS">
              <i class="fa fa-info-circle"></i>
            </span>
            <select id="cbo-banco_tipo_medio_pago" name="ID_Tipo_Medio_Pago" class="form-control required" style="width: 100%;"></select>
            <span class="help-block text-danger" id="error"></span>
          </div>
        </div>

        <div class="col-xs-4 col-md-5 d-none">
          <div class="form-group">
            <label>Banco BD</label>
            <select id="cbo-banco" name="ID_Banco" class="form-control required" style="width: 100%;"></select>
            <span class="help-block text-danger" id="error"></span>
          </div>
        </div>

        <div class="col-xs-4 col-md-4 div-tipo_cuenta">
          <div class="form-group">
            <label>Tipo de cuenta</label>
            <select id="cbo-tipo_cuenta" name="Nu_Tipo_Cuenta" class="form-control required" style="width: 100%;"></select>
            <span class="help-block text-danger" id="error"></span>
          </div>
        </div>

        <div class="col-xs-4 col-md-3 div-moneda">
          <div class="form-group">
            <label>Moneda</label>
            <select id="cbo-moneda" name="ID_Moneda" class="form-control required" style="width: 100%;"></select>
            <span class="help-block text-danger" id="error"></span>
          </div>
        </div>

        <div class="col-xs-12 col-md-12">
          <div class="form-group">
            <label>Titular de Cuenta <span class="label-advertencia">*</span></label>
            <input type="text" id="txt-No_Titular_Cuenta" name="No_Titular_Cuenta" placeholder="Obligatorio" class="form-control required" autocomplete="off" maxlength="100">
            <span class="help-block text-danger" id="error"></span>
          </div>
        </div>

        <div class="col-xs-12 col-md-6">
          <div class="form-group">
            <label id="label-nro_cuenta">Nro. de Cuenta <span class="label-advertencia">*</span></label>
            <input type="text" id="txt-No_Cuenta_Bancaria" name="No_Cuenta_Bancaria" placeholder="Obligatorio" class="form-control required" autocomplete="off" maxlength="30">
            <span class="help-block text-danger" id="error"></span>
          </div>
        </div>

        <div class="col-xs-12 col-md-6 div-cci">
          <div class="form-group">
            <label>CCI</label>
            <input type="text" id="txt-No_Cuenta_Interbancario" name="No_Cuenta_Interbancario" placeholder="Opcional" class="form-control" autocomplete="off" maxlength="50">
            <span class="help-block text-danger" id="error"></span>
          </div>
        </div>
      </div>
    </div>
    
    <div class="modal-footer">
      <div class="row mt-3">
        <div class="col-xs-6 col-md-6">
          <div class="form-group">
            <button type="button" id="btn-cancelar" class="btn btn-danger btn-lg btn-block"  data-dismiss="modal">Salir</button>
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
</div>
<?php echo form_close(); ?>
<!-- /.Modal Cuentas Bancarias-->