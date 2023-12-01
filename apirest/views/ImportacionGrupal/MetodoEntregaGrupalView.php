<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-8">
          <h1><i class="<?php echo $this->MenuModel->verificarAccesoMenuCRUD()->Txt_Css_Icons; ?>" aria-hidden="true"></i> <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?></h1>
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
                    <th>Metodo de entrega</th>
                    <th class="no-sort">Estado</th>
                    <th class="no-sort">Editar</th>
                  </tr>
                  </thead>
                </table>
              </div>

              <!-- DISTRITO DELIVERY TIENDA VIRTUAL -->
              <div class="box box-content">
              <!-- box-header -->
                <div class="box-header box-header-new  d-none">
                  <div class="row div-Filtros ">
                    <div class="col-md-12">
                      <div class="form-group">
                        <h3>Configuración de Delivery</h3>
                      </div>
                    </div>

                    <br>
                    <div class="col-md-4">
                      <div class="form-group">
                        <button type="button" id="btn-configurar_delivery_estandar" class="btn btn-primary btn-block" data-mostrar_configurar_delivery_estandar="0">Configuración Estándar</button>
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="form-group">
                        <button type="button" id="btn-configurar_delivery_manual" class="btn btn-primary btn-block" data-mostrar_configurar_delivery_manual="0">Configuración Manual</button>
                      </div>
                    </div>
                    
                    <div class="col-md-4">
                      <div class="form-group">
                        <button type="button" id="btn-configurar_promo_delivery" class="btn btn-primary btn-block" data-mostrar_configurar_delivery_estandar="0">Promoción de envío (opcional)</button>
                      </div>
                    </div>

                    <?php
                    if ( $this->user->No_Usuario == 'root' ){ ?>
                    <div class="col-md-12 div-configurar_delivery_estandar">
                      <div class="form-group">
                        <label>Empresa <span class="label-advertencia">*</span></label>
                        <select id="cbo-Empresas" name="ID_Empresa_Estandar_Delivery" class="form-control required" style="width: 100%;"></select>
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>
                    <?php } else { ?>
                    <div class="col-md-6 hidden">
                      <input type="text" id="cbo-Empresas" name="ID_Empresa_Estandar_Delivery" class="form-control" value="<?php echo $this->user->ID_Empresa; ?>">
                    </div>
                    <?php } ?>
                  
                    <!--PROMOCIONES DE ENVIOS-->
                    <?php 
                      $attributes = array('id' => 'form-ConfigurarPromoDelivery'); 
                      echo form_open('', $attributes);
                    ?>
                    <div class="col-xs-12 col-md-12 div-configurar_promo_delivery hidden">
                      <div class="alert alert-warning">
                        Crea una promoción de envío GRATIS en base al monto de compra.
                      </div>
                    </div>
                    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 div-configurar_promo_delivery hidden">
                      <div class="checkbox">
                        <label>
                          <input type="checkbox" id="chk-ID_Estatus_Promo" name="ID_Estatus_Promo">
                          <span class="check-title">Activar promoción</span>
                        </label>
                      </div>
                    </div>
                    <div class="col-xs-12 col-sm-8 col-md-4 col-lg-4 div-configurar_promo_delivery hidden">
                      <div class="form-group">
                        <label>A partir de este monto de compra<span class="label-advertencia">*</span></label>
                        <input type="text" disabled id="txt-Nu_Monto_Compra" name="Nu_Monto_Compra" min=".01" inputmode="number" step=".01" class="form-control input-configurar_promo_delivery required" autocomplete="off" value="0.00" placeholder="">
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>
                    <div class="col-xs-12 col-md-4 div-configurar_promo_delivery" style="display:none;">
                      <div class="form-group">
                        <label>El costo de envío será</label>
                        <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Si el campo esta vacío o con 0, el envío será GRATIS">
                          <i class="fa fa-info-circle"></i>
                        </span>
                        <input type="number" disabled id="txt-Nu_Costo_Envio" name="Nu_Costo_Envio" min="0" inputmode="number" step=".01" class="form-control input-configurar_promo_delivery" autocomplete="off" value="0.00" placeholder="">
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>

                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 div-configurar_promo_delivery hidden">
                      <div class="form-group">
                        <label>Términos y condiciones</label>
                        <textarea id="txt-Txt_Terminos" disabled name="Txt_Terminos" rows="3" class="form-control input-configurar_promo_delivery" placeholder="Opcional"></textarea>
                      </div>
                    </div>
                    <div class="col-xs-12 col-md-12 div-configurar_promo_delivery hidden">
                      <button type="submit" class="btn btn-success btn-lg btn-configurar_promo_delivery btn-block">Guardar</button>
                    </div>
                    <?php echo form_close(); ?>
                    <!--PROMOCIONES DE ENVIOS-->
                    <div class="col-xs-12 col-md-12 div-configurar_delivery_estandar hidden">
                      <div class="alert alert-warning">
                        <?php if($this->user->No_Pais=='PERU'){ ?>
                        <strong>IMPORTANTE:</strong> Al activar esta opción llegarás a todos los departamentos, provincias y distritos del Perú.
                        <?php } ?>
                        <?php if($this->user->No_Pais=='MÉXICO'){ ?>
                        <strong>IMPORTANTE:</strong> Al activar esta opción llegarás a todos los estados y ciudades de México.
                        <?php } ?>
                      </div>
                    </div>

                    <div class="col-md-6 hidden">
                      <div class="form-group">
                        <label>Precio <span class="label-advertencia">*</span></label>
                        <input type="text" id="txt-Ss_Precio_Estandar_Delivery" name="Ss_Precio_Estandar_Delivery" inputmode="decimal" class="form-control required input-decimal" maxlength="13" autocomplete="off" value="0" placeholder="Precio">
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>

                    <div class="col-md-12 div-configurar_delivery_estandar hidden">
                      <div class="form-group">
                        <label class="hidden">&nbsp;</label>
                        <button type="button" id="btn-modificar_precio_estandar_delivery" class="btn btn-lg btn-success btn-block">Activar</button>
                      </div>
                    </div>

                    <div class="col-md-4 div-configurar_delivery_manual hidden">
                      <div class="form-group">
                        <select id="cbo-Filtros_Distritos" name="Filtros_Distritos" class="form-control">
                          <option value="Distrito">Nombre Distrito</option>
                          <option value="Provincia">Nombre Provincia</option>
                          <option value="Departamento">Nombre Departamento</option>
                        </select>
                      </div>
                    </div>
                    
                    <div class="col-md-8 div-configurar_delivery_manual hidden">
                      <div class="form-group">
                        <input type="text" id="txt-Global_Filter_Distrito" name="Global_Filter_Distrito" class="form-control" maxlength="64" placeholder="Ingresar valor para buscar" value="" autocomplete="off">
                      </div>
                    </div>
                  </div>
                </div>
                <!-- /.box-header -->
                <div class="table-responsive div-configurar_delivery_manual hidden">
                  <table id="table-Distrito" class="table table-striped table-bordered">
                    <thead>
                      <tr>
                        <th>Departamento</th>
                        <th>Provincia</th>
                        <th>Distrito</th>
                        <!--<th>Precio</th>-->
                        <th>Tienda</th>
                        <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Editar == 1) : ?>
                          <th class="no-sort">Editar</th>
                        <?php endif; ?>
                      </tr>
                    </thead>
                  </table>
                </div>
                <!-- /.box-body -->
              </div>
              <!-- /.box -->
              <!-- /. DISTRITO DELIVERY TIENDA -->
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
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
    
    <div class="modal-body">
      <input type="hidden" name="EID_Empresa" class="form-control">
      <input type="hidden" name="EID_Metodo_Entrega_Tienda_Virtual" class="form-control">
      <input type="hidden" name="ENu_Tipo_Metodo_Entrega_Tienda_Virtual" class="form-control">
      <input type="hidden" name="ENo_Metodo_Entrega_Tienda_Virtual" class="form-control">
      
      <input type="hidden" name="EID_Almacen" class="form-control">

      <div class="row">
        <div class="col-xs-8 col-md-5">
          <div class="form-group">
            <label>Nombre <span class="label-advertencia">*</span></label>
            <input type="text" id="txt-No_Metodo_Entrega_Tienda_Virtual" name="No_Metodo_Entrega_Tienda_Virtual" placeholder="Ingresar nombre" class="form-control required" autocomplete="off" maxlength="50">
            <span class="help-block" id="error"></span>
          </div>
        </div>
        
        <div class="col-xs-4 col-md-3">
          <div class="form-group">
            <label>Estado <span class="label-advertencia">*</span></label>
            <select id="cbo-Estado_2" name="Nu_Estado_2" class="form-control required" style="width: 100%;"></select>
            <span class="help-block" id="error"></span>
          </div>
        </div>
      </div>

      <div class="row div-recojo_tienda">
        <h3 class="text-center">Dirección de Entrega</h3>

        <div class="col-xs-12 col-md-12 hidden">
          <label>Nombre <span class="label-advertencia">(Opcional)</span></label>
          <div class="form-group">
            <input type="text" id="txt-No_Almacen" name="No_Almacen" placeholder="Nombre (opcional)" class="form-control" autocomplete="off" maxlength="100">
            <span class="help-block" id="error"></span>
          </div>
        </div>

        <div class="col-xs-12 col-md-12">
          <label>Dirección <span class="label-advertencia">*</span></label>
          <div class="form-group">
            <input type="text" id="txt-direccion" name="Txt_Direccion_Almacen" placeholder="Ingresar dirección" class="form-control required" autocomplete="off">
            <span class="help-block" id="error"></span>
          </div>
        </div>

        <div class="col-xs-6 col-sm-4 col-md-4 hidden">
          <div class="form-group">
            <label>País <span class="label-advertencia">*</span></label>
            <select id="cbo-Paises-recojo_tienda" name="ID_Pais-recojo_tienda" class="form-control required" style="width: 100%;"></select>
            <span class="help-block" id="error"></span>
          </div>
        </div>

        <div class="col-xs-6 col-sm-4 col-md-4">
          <div class="form-group">
            <label>Departamento <span class="label-advertencia">*</span></label>
            <select id="cbo-Departamentos-recojo_tienda" name="ID_Departamento-recojo_tienda" class="form-control required" style="width: 100%;"></select>
            <span class="help-block" id="error"></span>
          </div>
        </div>
        
        <div class="col-xs-6 col-sm-4 col-md-4">
          <div class="form-group">
            <label>Provincia <span class="label-advertencia">*</span></label>
            <select id="cbo-Provincias-recojo_tienda" name="ID_Provincia-recojo_tienda" class="form-control required" style="width: 100%;"></select>
            <span class="help-block" id="error"></span>
          </div>
        </div>

        <div class="col-xs-12 col-sm-4 col-md-4">
          <div class="form-group">
            <label>Distrito <span class="label-advertencia">*</span></label>
            <select id="cbo-Distritos-recojo_tienda" name="ID_Distrito-recojo_tienda" class="form-control select2 required" style="width: 100%;"></select>
            <span class="help-block" id="error"></span>
          </div>
        </div>
      </div>
    </div>
    
    <div class="modal-footer justify-content-between">
      <button type="button" class="btn btn-danger btn-md btn-block col" data-dismiss="modal">Salir</button>
      <button type="submit" id="btn-save" class="btn btn-success btn-md btn-block col btn-verificar">Guardar</button>
    </div>
  </div>
</div>
</div>
<?php echo form_close(); ?>
<!-- /.Modal -->

<!-- Modal -->
<?php
$attributes = array('id' => 'form-Distrito');
echo form_open('', $attributes);
?>
<div class="modal fade" id="modal-Distrito" role="dialog">
<div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header">
      <h4 class="modal-title text-center"></h4>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
    
    <div class="modal-body">
      <input type="hidden" name="EID_Distrito" class="form-control required">
      
      <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6">
          <div class="form-group">
            <label>País <span class="label-advertencia">*</span></label>
            <select id="cbo-Paises" name="ID_Pais" class="form-control required" style="width: 100%;"></select>
            <span class="help-block" id="error"></span>
          </div>
        </div>
        
        <div class="col-xs-6 col-sm-6 col-md-6">
          <div class="form-group">
            <label>Departamento <span class="label-advertencia">*</span></label>
            <select id="cbo-Departamentos" name="ID_Departamento" class="form-control required" style="width: 100%;"></select>
            <span class="help-block" id="error"></span>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6">
          <div class="form-group">
            <label>Provincia <span class="label-advertencia">*</span></label>
            <select id="cbo-Provincias" name="ID_Provincia" class="form-control required" style="width: 100%;"></select>
            <span class="help-block" id="error"></span>
          </div>
        </div>
        
        <div class="col-xs-6 col-sm-6 col-md-6">
          <label>Distrito <span class="label-advertencia">*</span></label>
          <div class="form-group">
            <input type="text" id="txt-No_Distrito" name="No_Distrito" class="form-control required" placeholder="Ingresar nombre" autocomplete="off" maxlength="64">
            <span class="help-block" id="error"></span>
          </div>
        </div>
        
        <div class="col-xs-6 col-sm-3 col-md-2 d-none">
          <label>Siglas</label>
          <div class="form-group">
            <input type="text" name="No_Distrito_Breve" class="form-control" autocomplete="off" maxlength="2">
            <span class="help-block" id="error"></span>
          </div>
        </div>

        <div class="col-xs-6 col-sm-3 col-md-6 d-none">
          <label>Precio Delivery</label>
          <div class="form-group">
            <input type="text" inputmode="numeric" name="Ss_Delivery" class="form-control input-decimal" autocomplete="off" maxlength="5">
            <span class="help-block" id="error"></span>
          </div>
        </div>
        
        <div class="col-xs-12 col-sm-12 col-md-12">
          <label>¿Habilitar Tienda?</label>
          <div class="form-group">
            <select id="cbo-habilitar_ecommerce" name="Nu_Habilitar_Ecommerce" class="form-control required" style="width: 100%;"></select>
            <span class="help-block" id="error"></span>
          </div>
        </div>

        <div class="col-xs-6 col-sm-9 col-md-3 d-none">
          <label>Estado <span class="label-advertencia">*</span></label>
          <div class="form-group">
            <select id="cbo-Estado" name="Nu_Estado" class="form-control required" style="width: 100%;"></select>
            <span class="help-block" id="error"></span>
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