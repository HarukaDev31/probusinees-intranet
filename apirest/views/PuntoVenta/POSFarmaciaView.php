<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Main content -->
  <section class="content" style="background: #eeeeee; margin-left: 0px; margin-right: 0px;">
    <div class="row">
      <?php      
      if ( $this->empresa->Nu_Estado_Pago_Sistema == 0 ) { ?>
      <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <strong>Advertencia:</strong> No se guardará información porque ya se venció el pago. <button type="button" class="btn btn-success" style="padding: 5px 25px; font-size: 16px;" data-toggle="modal" data-target="#modal-pago_cuenta_bancarias_laesystems">Pagar aquí</button>
      </div>

      <?php
      }
      
      if ( isset($this->session->userdata['arrDataPersonal']) && $this->session->userdata['arrDataPersonal']['sStatus']=='success' ) { ?>
      <!-- BUSCADOR DE PRODUCTOS -->
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
      <div class="row" style="background: #transparent !important; padding: 0px; margin-bottom: 0px; margin-right: 0px; border-radius: 0.3em;">
        <div class="col-xs-12 col-sm-9 col-md-9 col-lg-9">
          <div class="form-group">
            <label style="">ítem</label>
            <span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Busca tus ítems por Nombre / Código de Barra (UPC) / Código SKU. Para agregar nuevos ítems usar el botón (+) o Compras y Productos > Reglas de Productos >  Productos">
              <i class="fa fa-info-circle"></i>
            </span>
            &nbsp;&nbsp;&nbsp;
            <span>
                <label style="cursor: pointer; font-weight: normal; font-size: 11px">
                  <input type="radio" name="radio-addLector" class="flat-red" id="radio-InactiveLector" value="0" checked> Manualmente
                </label> 
                <label style="cursor: pointer; font-weight: normal; font-size: 11px">
                  <input type="radio" name="radio-addLector" class="flat-red" id="radio-ActiveLector" value="1"> Lector Código Barra
                </label>
            </span>

            <input type="hidden" id="txt-Nu_Compuesto" class="form-control" value="">
            <input type="hidden" id="txt-ID_Producto" class="form-control">
            <input type="hidden" id="txt-Nu_Codigo_Barra" class="form-control">
            <input type="hidden" id="txt-Ss_Precio" class="form-control">
            <input type="hidden" id="txt-Ss_Precio_Interno" class="form-control">
            <input type="hidden" id="txt-ID_Impuesto_Cruce_Documento" class="form-control">
            <input type="hidden" id="txt-Nu_Tipo_Impuesto" class="form-control">
            <input type="hidden" id="txt-Ss_Impuesto" class="form-control">
            <input type="hidden" id="txt-Qt_Producto" class="form-control">
            <input type="hidden" id="txt-nu_tipo_item" class="form-control">
            <?php if($bStatusOpcionProductoAdd==1) { ?>
            <div class="input-group">
            <?php } ?>
              <input type="text" id="txt-No_Producto" class="form-control autocompletar_lector_codigo_barra hotkey-limpiar_item hotkey-cancelar_venta" data-global-class_method="AutocompleteController/getAllProduct" data-global-table="producto" onkeyup="autocompleteItemsAlternativos(this.value);" placeholder="Buscar por Nombre / Código Barra / Código SKU" value="" autocomplete="off">
              <?php if($bStatusOpcionProductoAdd==1) { ?>
                <div class="input-group-btn">
                  <button type="button" class="btn btn-primary" id="btn-crear_item" title="Crear Producto o Servicio" alt="Crear Producto o Servicio" data-toggle="tooltip" data-trigger="hover" data-placement="bottom">Nuevo</button>
                </div>
              <?php } ?>
            <?php if($bStatusOpcionProductoAdd==1) { ?>
            </div>
            <?php } ?>
            <span class="help-block" id="error"></span>
          </div>
        </div>

        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
          <label data-toggle="tooltip" data-placement="bottom" title="">L. Precio</label>
          <span class="hidden-sm hidden-md" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Para configurar múltiples lista de precios, debemos de ingresar a la opción Ventas -> Reglas de Venta -> Lista de Precios">
            <i class="fa fa-info-circle"></i>
          </span>
          <div class="form-group">
            <select id="cbo-lista_precios" class="form-control" style="width: 100%;" data-toggle="tooltip" data-placement="bottom" title=""></select>
          </div>
        </div>
      </div>
      </div>

      <!-- Lateral Izquierda -->
      <div class="col-xs-12 col-sm-5">
        <div class="row" style="display:none; background: #fcfcfc !important; padding: 10px; margin-bottom: 15px; margin-right: 0px; border-radius: 0.3em;">
          <?php
           if ( isset($this->session->userdata['arrDataPersonal']) && $this->session->userdata['arrDataPersonal']['sStatus']=='success' ) {
            $arrDataPersonal = $this->session->userdata['arrDataPersonal']['arrData'][0];
          ?>
          <div class="col-xs-4">
            <label>Cajero(a)</label>
            <div class="form-group">
              <input type="hidden" id="hidden-id_matricula_personal" value="<?php echo $arrDataPersonal->ID_Matricula_Empleado; ?>">
              <label style="font-weight: normal"><?php echo '(' . $arrDataPersonal->Nu_Caja . ') ' . $arrDataPersonal->No_Entidad; ?></label>
            </div>
          </div>

          <div class="col-xs-4">
            <label>F. Apertura Caja</label>
            <div class="form-group">
              <label style="font-weight: normal"><?php echo $arrDataPersonal->Fe_Matricula; ?></label>
            </div>
          </div>

          <div class="col-xs-4">
            <div class="form-group">
              <input type="hidden" id="hidden-id_moneda_caja_pos" value="<?php echo $this->session->userdata['arrDataPersonal']['arrData']['ID_Moneda']; ?>">
              <input type="hidden" id="hidden-no_signo_caja_pos" value="<?php echo $this->session->userdata['arrDataPersonal']['arrData']['No_Signo']; ?>">
            </div>
          </div>
          <?php } ?>
        </div>
        
        <!-- listar productos alternativos -->
        <?php
        $sCssDisplay='style="display:none"';
        if ( $this->empresa->Nu_Tipo_Rubro_Empresa==1 ){//1 = Farmacia
          $sCssDisplay='';
        }?>
        <div class="row" <?php echo $sCssDisplay; ?>>
          <div class="col-xs-12 div-items_alternativos" style="height: 220px; overflow-y: auto;"><!-- 175 -->
            <table id="table-items_alternativos" class="table table-striped">
              <thead>
                <tr>
                  <th colspan="4" style="padding-top: 5px; padding-bottom: 5px;">Productos Alternativos</th>
                </tr>
                <tr>
                  <th class="text-left" style="padding-top: 5px; padding-bottom: 5px;"></th>
                  <th class="text-left" style="padding-top: 5px; padding-bottom: 5px;">Nombre</th>
                  <th class="text-right" style="padding-top: 5px; padding-bottom: 5px;">Precio</th>
                  <th class="text-right" style="padding-top: 5px; padding-bottom: 5px;">Stock</th>
                  <th class="text-right" style="padding-top: 5px; padding-bottom: 5px;">U.M.</th>
                  <th class="text-right" style="padding-top: 5px; padding-bottom: 5px;">Marca</th>
                  <th class="text-right" style="padding-top: 5px; padding-bottom: 5px;">Laboratorio</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
        <!-- ./ listar productos alternativos -->

        <!-- div row alertas -->
        <div class="row" id="div-row-alertas"></div>
        
        <!-- Centered Tabs -->
        <div class="col-xs-12">
          <div class="tabbable table-responsive">
            <ul class="nav nav-tabs nav-tabs-lista_categorias"></ul>
          </div>
        </div>
        
        <div class="col-xs-12" style="height: 510px; overflow-y: auto; padding: 0;">
          <div class="col-xs-12">
            <ul class="list-group row div-lista_cuadro_items"></ul>
          </div>
        </div>
      </div>
      <!-- ./ Lateral Izquierda -->

      <!-- Lateral Derecha -->
      <div class="col-xs-12 col-sm-7 sidenav" style="background: #fcfcfc !important; padding: 10px; margin-bottom: 15px; border-radius: 0.3em;">
        <div class="row">
          <div class="col-xs-6 col-sm-4 col-md-3" style="display:none">
            <label>Cliente</label>
            <div class="form-group">
              <select id="cbo-tipo_cliente" name="tipo_cliente" class="form-control hotkey-cobrar_cliente" style="width: 100%;">
                <option value="3">Nuevo / Existe</option>
                <option value="1" title="Solo para boletas y ventas menores < 700.00">Rápido (Boleta)</option>
              </select>
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-6 col-sm-4 col-md-2 div-nuevo_cliente" style="padding-right: 0px;">
            <label>Comprobante</label>
            <div class="form-group">
              <select id="cbo-tipo_documento" name="tipo_documento" class="form-control hotkey-cobrar_cliente" style="width: 100%;padding: 3px;">
                <?php if($this->empresa->Nu_Tipo_Proveedor_FE != 3) { ?>
                  <option value="4" data-nu_cantidad_caracteres="8" title="Puedes registrar boleta ingresando solo Nombres del Cliente">Boleta</option>
                  <option value="3" data-nu_cantidad_caracteres="11">Factura</option>
                <?php } ?>
                <option value="2" data-nu_cantidad_caracteres="15" title="Son notas de venta interna y estas no son enviadas a SUNAT. Puedes registrar nota de venta ingresando solo Nombres del Cliente">Nota Venta</option>
              </select>
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-6 col-sm-3 col-md-2">
            <div class="form-group">
              <label>T.D.I</label>
              <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Tipo de Documento de Identidad">
                <i class="fa fa-info-circle"></i>
              </span>
              <select id="cbo-TiposDocumentoIdentidad" name="ID_Tipo_Documento_Identidad" class="form-control required" style="width: 100%;"></select>
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-6 col-sm-5 col-md-3 div-nuevo_cliente">
            <label id="label-tipo_documento_identidad" title="Si existe cliente, buscar por Nombre / Num. Doc. Ident.">DNI</label>
            <span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Si el cliente ya existe, puedes buscar por Nombre o Número de Documento de Identidad, si no existe el sistema lo guardará y en la siguiente venta lo podrán buscar">
              <i class="fa fa-info-circle"></i>
            </span>
            <div class="form-group">
              <input type="hidden" id="txt-ID_Tipo_Documento_Identidad" class="form-control">
              <input type="hidden" id="txt-AID" name="AID" class="form-control">
              <input type="hidden" id="txt-Txt_Direccion_Entidad" name="Txt_Direccion_Entidad" class="form-control">
              <input type="hidden" id="txt-Nu_Dias_Credito" name="Nu_Dias_Credito" class="form-control">
              <input type="text" id="txt-ACodigo" name="Nu_Documento_Identidad" class="form-control autocompletar input-Mayuscula input-codigo_barra hotkey-cancelar_venta hotkey-cobrar_cliente hotkey-focus_item" onkeyup="api_sunat_reniec(this.value);" data-global-class_method="AutocompleteController/getAllClient" data-global-table="entidad" placeholder="" title="Si existe cliente ingresar Nombre / # Doc. Ident." maxlength="8" autocomplete="off">
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-6 col-sm-6 col-md-5 div-nuevo_cliente">
            <label id="label-No_Entidad">Nombre(s) <span class="hidden-xs">y Apellidos</span></label><span id="span-no_nombres_cargando"></span>
            <span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Para buscar un cliente registrado, deben de escribir en el campo T.D.I.">
              <i class="fa fa-info-circle"></i>
            </span>
            <div class="form-group">
              <div class="input-group">
                <input type="hidden" id="hidden-nu_numero_documento_identidad" class="form-control" value="">
                <input type="hidden" id="hidden-estado_entidad" class="form-control" value="0">
                <input type="text" id="txt-ANombre" name="No_Entidad" class="form-control hotkey-cobrar_cliente hotkey-cancelar_venta hotkey-limpiar_item hotkey-focus_item" autocomplete="off">
                <span class="help-block" id="error"></span>
                <div class="input-group-btn">
                  <button type="button" id="btn-datosAdicionalesEntidad" class="btn btn-primary btn-block btn-md">ver</button>
                </div>
              </div>
            </div>
          </div>

          <div class="col-xs-4 col-sm-6 col-md-6 col-lg-7"><!--div-nuevo_cliente-->
            <div class="form-group">
              <label id="label_correo">Correo</label>
              <span id="span_correo" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Si ingresas el email del cliente, se enviará el PDF y XML del comprobante electrónico. También quedará registrado para la próxima venta">
                <i class="fa fa-info-circle"></i>
              </span>
              <input type="email" id="txt-Txt_Email_Entidad_Cliente" name="Txt_Email_Entidad" placeholder="opcional" class="form-control hotkey-cobrar_cliente hotkey-cancelar_venta hotkey-limpiar_item hotkey-focus_item" autocomplete="on">
              <span class="hide" id="span-email" style="color: #dd4b39;">Ingresa un email válido</span>
            </div>
          </div>

          <div class="col-xs-4 col-sm-7 col-md-3 col-lg-3 div-nuevo_cliente">
            <div class="form-group">
              <label>Celular</label>
              <span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Se mostrará impreso en el ticket cuando la Recepción sea Delivery">
                <i class="fa fa-info-circle"></i>
              </span>
              <input type="tel" id="txt-Nu_Celular_Entidad_Cliente" name="Nu_Celular_Entidad" class="form-control hotkey-cobrar_cliente hotkey-cancelar_venta hotkey-limpiar_item hotkey-focus_item" data-inputmask="'mask': ['999 999 999']" data-mask autocomplete="on" placeholder="opcional">
              <span class="hide" id="span-celular" style="color: #dd4b39;">Ingresa un celular válido</span>
              <span class="help-block" id="error"></span>
            </div>
          </div>

          <div class="col-xs-4 col-sm-5 col-md-3 col-lg-2 div-nuevo_cliente">
            <label>WhatsApp</label>
            <div class="form-group">
              <label style="cursor: pointer;">
                <input type="radio" name="radio-addWhatsapp" class="flat-red" id="radio-InactiveWhatsapp" value="0" checked>No
              </label>&nbsp;
              <label style="cursor: pointer;">
                <input type="radio" name="radio-addWhatsapp" class="flat-red" id="radio-ActiveWhatsapp" value="1">Si
              </label>
            </div>
          </div>
          
          <div class="col-xs-12 hidden">
            <div class="alert alert-success alert-dismissible" style="background-color: #4f4f4f !important; border-color: #4f4f4f; margin: 1px 10px;padding: 0.3% 5%;padding-left: 1%;background-color: #FFFFF !important;font-weight: normal;font-size: 12px;">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
              Si los campos están vacíos mostrará clientes varios
            </div>
          </div>

          <div class="col-xs-5 col-sm-5 col-md-5" style="display:none">
            <label>Dirección</label>
            <div class="form-group">
              <label id="label-txt_direccion" style="font-weight: normal"></label>
            </div>
          </div>

          <div class="col-xs-2 col-sm-5 col-md-5" style="display:none">
            <label>Estado</label>
            <div class="form-group">
              <label id="label-txt_estado_cliente" style="font-weight: normal"></label>
            </div>
          </div>
        </div>
        
        <!-- listar productos agregados 275px -->
        <div class="row">
          <div class="col-xs-12 div-lista_compra_items" style="height: 180px; overflow-y: auto;"><!-- 240px -->
            <table id="table-detalle_productos_pos" class="table table-striped">
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
        <!-- ./ listar productos agregados -->

        <div class="row" style="margin-top: 2%;">
          <div class="col-xs-6 col-md-3">
            <label>Recepción</label>
            <div class="form-group">
              <select id="cbo-recepcion" class="form-control">
                <option value="5" selected>Tienda</option>
                <option value="6">Delivery</option>
                <option value="7">Recojo en Tienda</option>
              </select>
            </div>
          </div>
          <div class="col-xs-6 col-md-3">
            <label>F. Entrega</label>
            <div class="form-group">
              <div class="input-group date" style="width:100%">
                <input type="text" id="txt-fe_entrega" class="form-control input-datepicker-today-to-more required" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
              </div>                
            </div>
          </div>
          <div class="col-xs-6 col-md-3">
            <label>Descuento</label>
            <span data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Para desactivar / activar descuento ir a Configuración > Configurar empresa y sistema">
              <i class="fa fa-info-circle"></i>
            </span>
            <div class="form-group">
              <select id="cbo-descuento" class="form-control hotkey-cobrar_cliente">
                <option value="1" selected>Importe</option>
                <option value="2">Porcentaje</option>
              </select>
            </div>
          </div>
          <div class="col-xs-6 col-md-3">
            <label>Descuento Total</label>
            <span data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Para habilitar descuento ir a Configuración > Configurar empresa y sistema">
              <i class="fa fa-info-circle"></i>
            </span>
            <div class="form-group">
              <button type="button" id="btn-add_descuento_total" class="btn btn-link btn-md btn-block">Agregar</button>
            </div>
          </div>
        </div>
        <!-- Totales -->
        <div class="row">
          <div class="col-xs-12">
            <div class="form-group">
              <button type="button" id="btn-pagar" class="btn btn-primary btn-lg btn-block">
                <div class="col-xs-7 text-left">
                  <h4 style="font-size: 26px;">COBRAR</h4>
                </div>
                <div class="col-xs-5 text-right">
                  <input type="hidden" class="hidden-gravada" value="0.00">
                  <input type="hidden" class="hidden-exonerada" value="0.00">
                  <input type="hidden" class="hidden-inafecta" value="0.00">
                  <input type="hidden" class="hidden-grautita" value="0.00">
                  <input type="hidden" class="hidden-total_icbper" value="0.00">
                  <input type="hidden" class="input-total_detalle_productos_pos" value="0.00">
                  <h4 style="font-size: 26px;" class="label-total_detalle_productos_pos">S/ 0.00</h4>
                </div>
              </button>
            </div>
          </div>
        </div>
        <!-- ./ Totales -->

        <!-- Atajos de teclado -->
        <div class="row">
          <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3 hidden-xs">
            <button type="button" id="btn-atajos_teclado" class="btn btn-link btn-lg btn-block">Atajos de teclado</button>
          </div>
          <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
            <button type="button" id="btn-cancelar_venta" class="btn btn-link btn-lg btn-block">Cancelar Venta</button>
          </div>
          <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
            <button type="button" id="btn-add_nota_global" class="btn btn-link btn-lg btn-block">Adicionales</button>
          </div>
          <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
            <button type="button" id="btn-guias_remision_pos" class="btn btn-link btn-lg btn-block">Guía(s) Remisión</button>
          </div>
        </div>
        <!-- ./ Atajos de teclado -->
      </div>
      <!-- /. Lateral Derecha -->
    </div>
    <!-- /.row -->
    <!-- modal atajos de teclado -->    
    <div class="modal fade modal-atajos_teclado" id="modal-default">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="text-center">Atajos de teclado</h4>
            <ul>
              <li>[ESC] => Cancelar Venta</li>
              <li>[F2] => Limpiar caja producto</li>
              <li>[F4] => Establecer el cursor en la búsqueda de productos</li>
              <li>[Intro / Enter] => Cobrar / Agregar forma pago del cliente / Generar comprobante</li>
            </ul>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-lg btn-block pull-center" data-dismiss="modal">Salir</button>
          </div>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div><!-- /.modal atajos de teclado -->

    <!-- modal Adicionales -->
    <div class="modal fade modal-add_nota_global" id="modal-default">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="text-center">Adicionales</h4>
          </div>
          <div class="modal-body">
            <div class="col-xs-12 col-sm-10">
              <label>Observaciones</label> (opcional)
              <div class="form-group">
                <textarea name="Txt_Glosa" class="form-control"></textarea>
              </div>
            </div>

            <div class="col-xs-12 col-sm-2">
              <label>Detracción</label>
              <span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Solo se usará si el servicio esta afecto a detracción">
                <i class="fa fa-info-circle"></i>
              </span>
              <div class="form-group">
                <label style="cursor: pointer;">
                  <input type="radio" name="radio-addDetraccion" class="flat-red" id="radio-InactiveDetraccion" value="0" checked> No
                </label>
                <label style="cursor: pointer;">
                  <input type="radio" name="radio-addDetraccion" class="flat-red" id="radio-ActiveDetraccion" value="1"> Si
                </label>
              </div>
            </div>

            <div class="col-xs-12 col-sm-6">
              <label>Orden de Compra / Servicio</label> (opcional)
              <div class="form-group">
                <input type="text" inputmode="numeric" name="No_Orden_Compra_FE" class="form-control" maxlength="20" placeholder="Ingresar" value="" autocomplete="off">
              </div>
            </div>

            <div class="col-xs-12 col-sm-6">
              <label>Placa de vehículo</label> (opcional)
              <div class="form-group">
                <input type="text" name="No_Placa_FE" class="form-control" maxlength="255" placeholder="Ingresar" value="" autocomplete="off">
              </div>
            </div>
            
            <div class="col-xs-12 col-sm-6">
              <label>Nro. Lote</label> (opcional)
              <div class="form-group">
                <input type="text" inputmode="numeric" name="Nu_Lote_Vencimiento" class="form-control" maxlength="20" placeholder="Ingresar" value="" autocomplete="off">
              </div>
            </div>

            <div class="col-xs-12 col-sm-6">
              <label>F. Lote</label> (opcional)
              <div class="input-group date">
                <input type="text" id="txt-Fe_Lote_Vencimiento" name="Fe_Lote_Vencimiento" class="form-control input-datepicker-today-to-more required" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary btn-lg btn-block pull-center" data-dismiss="modal">Aceptar</button>
          </div>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div><!-- /.modal Adicionales -->
    
    <!-- modal Descuento Global -->
    <div class="modal fade modal-add_descuento_total" id="modal-default">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="text-center">Descuento Total</h4>
          </div>
          <div class="modal-body">            
            <div class="col-xs-12">
              <label>Descuento x <span class="span-descuento_total_tipo">importe</span></label> (opcional)
              <div class="form-group">
                <input type="text" inputmode="decimal" class="form-control input-decimal" id="txt-Ss_Descuento" name="Ss_Descuento" size="3" value="" autocomplete="off" />
              </div>
            </div>
            <div class="col-xs-6">
              <div class="form-group text-left">
                <h5 class="">Descuento (-)</h5>
              </div>
            </div>
            <div class="col-xs-6">
              <div class="form-group text-right">
                <input type="hidden" class="input-total_descuento" value="0.00">
                <h5 class="label-total_descuento">S/ 0.00</h5>
              </div>
            </div>
            <div class="col-xs-6">
              <div class="form-group text-left">
                <h5 class="">Descuento IGV (-)</h5>
              </div>
            </div>
            <div class="col-xs-6">
              <div class="form-group text-right">
                <input type="hidden" class="input-total_descuento_sin_impuestos" value="0.00">
                <h5 class="label-total_descuento_sin_impuestos">S/ 0.00</h5>
              </div>
            </div>
            <div class="col-xs-6">
              <div class="form-group text-left">
                <h5 class="">Total</h5>
              </div>
            </div>
            <div class="col-xs-6">
              <div class="form-group text-right">
                <h5 class="label-total_detalle_productos_pos">S/ 0.00</h5>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary btn-lg btn-block pull-center" data-dismiss="modal">Aceptar</button>
          </div>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div><!-- /.modal descuento global -->

    <!-- Modal guias_remision -->
    <div class="modal fade modal-guias_remision" id="modal-default">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="text-center">Guía(s) de Remisión</h4>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-xs-12">
                <label>Serie(4)-Número(8) </label> (0XXX-XXXXXXXX / TXXX-XXXXXXXX) - Ejemplo: 0001-1 y múltiple: T001-1,T001-23,T001-212
                <div class="form-group">
                  <textarea name="Txt_Garantia" class="form-control input-Mayuscula input-guias_remision" placeholder="Ejemplo: 0001-1 y múltiple: 0001-3, 0001-44, 0001-555" title="Formato: serie-número separado por signo, si es más de uno coma(,)"></textarea>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <div class="col-xs-12 col-sm-12">
              <div class="form-group">
                <button type="button" id="btn-salir_guias_remision" class="btn btn-primary btn-lg btn-block pull-center" data-dismiss="modal">Aceptar</button>
              </div>
            </div>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /. Modal guias_remision -->

    <div class="modal fade modal-datos_adicionales_entidad" id="modal-default">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="text-center">Datos Adicionales Cliente</h4>
            <h4 class="text-center" id="h4-label_cliente" style="font-weight: bold !important;"></h4>
          </div>

          <div class="modal-body">
            <div class="row">
              <div class="col-xs-12 col-sm-12 col-md-12">
                <label>Dirección</label>
                <div class="form-group">
                  <input type="text" id="txt-Txt_Direccion_Entidad-modal" name="Txt_Direccion_Entidad" placeholder="Ingresar dirección" class="form-control" autocomplete="off">
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-12 col-sm-8 col-md-9">
                <label>Correo</label>
                <div class="form-group">
                  <input type="text" id="txt-Txt_Email_Entidad_Cliente-modal" name="Txt_Email_Entidad" placeholder="Ingresar correo" class="form-control" autocomplete="off">
                  <span class="hide" id="span-email-modal" style="color: #dd4b39;">Ingresa un email válido</span>
                </div>
              </div>
              
              <div class="col-xs-6 col-sm-4 col-md-3">
                <label>Celular</label>
                <div class="form-group">
                  <input type="tel" id="txt-Nu_Celular_Entidad_Cliente-modal" name="Nu_Celular_Entidad" class="form-control" data-inputmask="'mask': ['999 999 999']" data-mask autocomplete="off">
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-6 col-sm-12 col-md-12">
                <label>Estado</label>
                <div class="form-group estado">
                  <select id="cbo-Estado-modal"  name="Nu_Estado" class="form-control required"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
            </div><!--row-->
          </div><!--modal-body-->
            
          <div class="modal-footer">
            <div class="row">
              <div class="col-xs-12">
                <button type="button" id="btn-modal-salir" class="btn btn-danger btn-lg btn-block pull-center" data-dismiss="modal">Salir</button>
              </div>
            </div>
          </div>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div><!-- /.modal datos_adicionales_entidad -->

    <?php
    $attributes = array('id' => 'form-crear_item');
    echo form_open('', $attributes);
    ?>
    <!-- formulario crear item -->
    <div class="modal fade modal-crear_item" id="modal-default">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="text-center">Crear Item <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Para ver tus ítems ir a Compras y Productos > Reglas de Productos >  Productos">
              <i class="fa fa-info-circle"></i>
            </span></h4>
            
          </div>

          <div class="modal-body">
            <div class="row">
              <div class="col-xs-12 col-sm-3">
                <label>Grupo <span class="label-advertencia">*</span></label>
                <div class="form-group div-modal-grupoItem">
                  <select id="cbo-modal-grupoItem" class="form-control">
                    <option value="1">Producto</option>
                    <option value="0">Servicio</option>
                  </select>
                </div>
              </div>
              
              <div class="col-xs-6 col-sm-4 hidden">
                <div class="form-group">
                  <label>Tipo Producto</label>
                  <select id="cbo-modal-tipoItem" class="form-control"></select>
                </div>
              </div>
              
              <div class="col-xs-6 col-sm-5">
                <label>Código barra <span class="label-advertencia">*</span></label>
                <div class="form-group">
                  <input type="text" id="txt-modal-upcItem" class="form-control input-codigo_barra input-Mayuscula" placeholder="Ingresar código" maxlength="20" autocomplete="off">
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-3 col-sm-2">
                <label>Precio <span class="label-advertencia">*</span></label>
                <div class="form-group">
                  <input type="text" id="txt-modal-precioItem" inputmode="decimal" class="form-control required input-decimal" maxlength="13" autocomplete="off">
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-3 col-sm-2">
                <label>Costo</label>
                <div class="form-group">
                  <input type="text" id="txt-modal-costoItem" inputmode="decimal" class="form-control required input-decimal" maxlength="13" autocomplete="off">
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-12 col-sm-8" style="display:none">
                <label>Producto SUNAT <span class="label-advertencia">*</span></label>
                <div class="form-group">
                  <input type="hidden" id="hidden-ID_Tabla_Dato" name="ID_Tabla_Dato" class="form-control">
                  <input type="text" id="txt-No_Descripcion" name="No_Descripcion" class="form-control autocompletar_producto_sunat" placeholder="Ingresar nombre" value="" autocomplete="off">
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-12 col-sm-12">
                <label>Nombre <span class="label-advertencia">*</span></label>
                <div class="form-group">
                  <textarea name="textarea-modal-nombreItem" class="form-control required" rows="1" placeholder="Ingresar nombre" maxlength="250" autocomplete="off" aria-required="true"></textarea>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-6">
                <label>Impuesto <span class="label-advertencia">*</span></label>
                <div class="form-group">
                  <select id="cbo-modal-impuestoItem" class="form-control"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-6">
                <label>Unidad Medida <span class="label-advertencia">*</span></label>
                <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Para crear ir a Compras y Productos > Reglas de Productos >  Unidad de Medida">
                  <i class="fa fa-info-circle"></i>
                </span><a class="btn btn-link" target="_blank" style="color: #7b7b7b" href="<?php echo base_url() . 'Logistica/ReglasLogistica/CategoriaController/listarUnidadesMedida'; ?>">[Crear]</a>
                <div class="form-group">
                  <select id="cbo-modal-unidad_medidaItem" class="form-control"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
          
              <div class="col-xs-6">
                <label>Categoría <span class="label-advertencia">*</span></label>
                <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Para crear ir a Compras y Productos > Reglas de Productos >  Categoría">
                  <i class="fa fa-info-circle"></i>
                </span><a class="btn btn-link" target="_blank" style="color: #7b7b7b" href="<?php echo base_url() . 'Logistica/ReglasLogistica/CategoriaController/listarCategorias'; ?>">[Crear]</a>
                <div class="form-group">
                  <select id="cbo-modal-categoria" class="form-control select2" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
          
              <div class="col-xs-6">
                <label>Favorito</label>
                <div class="form-group">
                  <select id="cbo-modal-favorito" class="form-control" style="width: 100%;">
                    <option value="0">No</option>
                    <option value="1">Si</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
            
          <div class="modal-footer">
            <div class="col-xs-6">
              <button type="button" id="btn-modal-salir" class="btn btn-danger btn-lg btn-block pull-center" data-dismiss="modal"><span class="fa fa-close"></span> Cancelar</button>
            </div>
            <div class="col-xs-6">
              <button type="button" id="btn-modal-crear_item" class="btn btn-success btn-lg btn-block pull-center"><i class="fa fa-save"></i> Guardar</button>
            </div>
          </div>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div><!-- /.modal crear item -->
    <!-- /. formulario crear item -->
    <?php echo form_close(); ?>

    <!-- Modal delivery -->
    <div class="modal fade modal-delivery" id="modal-default">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="text-center">Delivery</h4>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-xs-12">
                <label>Transporte</label> (Opcional)
                <span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Para agregar Transporte o Delivery, ingresar a la opción Personal > Maestro Delivery">
                  <i class="fa fa-info-circle"></i>
                </span>
                <div class="form-group">
                  <select id="cbo-transporte" class="form-control select2" style="width: 100%;"></select>
                </div>
              </div>
              <div class="col-xs-12">
                <label>Dirección</label> (Opcional)
                <span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Si ingresa la dirección, se mostrará impreso en el ticket. También se guardará y se mostrará para la próxima venta">
                  <i class="fa fa-info-circle"></i>
                </span>
                <div class="form-group">
                  <textarea name="Txt_Direccion_Delivery" class="form-control"></textarea>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <div class="col-xs-12">
              <div class="form-group">
                <button type="button" id="btn-salir_delivery" class="btn btn-primary btn-lg btn-block pull-center" data-dismiss="modal">Aceptar</button>
              </div>
            </div>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /. Modal delivery -->    
    <!-- modal informacion del item -->
    <div class="modal fade modal-info_item" id="modal-default">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="text-center" id="modal-header-info_item-title"></h4>
          </div>
          <div class="modal-body" id="modal-body-info_item"></div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-lg btn-block pull-center" data-dismiss="modal">Salir</button>
          </div>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>
    <!-- /.modal informacion del item -->
    <?php
    $attributes = array('id' => 'form-modal_venta_pos_forma_pago');
    echo form_open('', $attributes);
    ?>
    <!-- Forma Pago POS Modal -->
    <div class="modal fade modal_forma_pago" id="modal-default">
      <div class="modal-dialog modal-md">
        <div class="modal-content">
          <div class="modal-body">
            <div class="row">
              <div class="col-xs-6 col-sm-3">
                <label>Forma Pago</label>
                <div class="form-group">
                  <select id="cbo-modal_forma_pago" class="form-control" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-6 col-sm-3 div-modal_datos_tarjeta_credito">
                <label>Tarjeta Crédito</label>
                <div class="form-group">
                  <select id="cbo-modal_tarjeta_credito" class="form-control" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-6 col-sm-3">
                <label>¿Con cuánto paga?</label>
                <div class="form-group">
                  <input type="text" inputmode="decimal" class="form-control input-decimal send-forma_pago_pos input-modal_monto hotkey-cancelar_venta" value="" autocomplete="off">
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-12 col-sm-3">
                <label>&nbsp;</label>
                <div class="form-group">
                  <button type="button" id="btn-add_forma_pago" class="btn btn-primary btn-md btn-block">Agregar pago</button>
                </div>
              </div>
            </div><!-- ./ row -->
            
            <div class="row div-modal_credito">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="form-group">
                  <label>F. Vencimiento</label>
                  <div class="input-group date" style="width: 100%;">
                    <input type="text" id="txt-Fe_Vencimiento" name="Fe_Vencimiento" class="form-control required" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                  </div>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
            </div>
            
            <div class="row div-modal_datos_tarjeta_credito">
              <div class="col-xs-6 col-sm-3">
                <label>Opcional</label>
                <div class="form-group">
                  <input type="text" inputmode="numeric" id="tel-nu_referencia" class="form-control input-number" value="" maxlength="10" placeholder="No. Operación" autocomplete="off">
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              <div class="col-xs-6 col-sm-3">
                <label>Opcional</label>
                <div class="form-group">
                  <input type="text" inputmode="numeric" id="tel-nu_ultimo_4_digitos_tarjeta" class="form-control input-number" minlength="4" maxlength="4" value="" placeholder="últimos 4 dígitos" autocomplete="off">
                  <span class="help-block" id="error"></span>
                </div>
              </div>
            </div>

            <div class="row div-billete_soles">
              <div class="col-xs-3 col-sm-3">
                <div class="form-group">
                  <button type="button" class="btn btn-outline-secondary btn-block billete-soles" value="10">S/ 10.00</button>
                </div>
              </div>
              <div class="col-xs-3 col-sm-3">
                <div class="form-group">
                  <button type="button" class="btn btn-outline-secondary btn-block billete-soles" value="20">S/ 20.00</button>
                </div>
              </div>
                
              <div class="col-xs-3 col-sm-3">
                <div class="form-group">
                  <button type="button" class="btn btn-outline-secondary btn-block billete-soles" value="50">S/ 50.00</button>
                </div>
              </div>
                
              <div class="col-xs-3 col-sm-3">
                <div class="form-group">
                  <button type="button" class="btn btn-outline-secondary btn-block billete-soles" value="100">S/ 100.00</button>
                </div>
              </div>
            </div><!-- ./ row -->
            
            <div class="row">
              <div class="col-xs-12 col-sm-12">
                <div id="div-modal_forma_pago" class="table-responsive">
                  <table id="table-modal_forma_pago" class="table table-striped table-bordered">
                    <thead>
                      <tr>
                        <th style="display:none; padding: 10px" class="text-left"></th>
                        <th style="display:none; padding: 10px" class="text-left"></th>
                        <th style="display:none; padding: 10px" class="text-left"></th>
                        <th style="padding: 10px" class="text-left">F. PAGO</th>
                        <th style="padding: 10px" class="text-left">TARJETA</th>
                        <th style="padding: 10px" class="text-right">MONTO</th>
                        <th style="width: 20px; text-align:center; padding: 10px"><i class="fa fa-trash-o fa-lg icon-clear_all_forma_pago_pos" style="cursor: pointer;"></i></th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                      <tr>
                        <th class="text-right" colspan="2">PAGO DEL CLIENTE</th>
                        <th class="text-right">
                          <input type="hidden" class="input-modal_forma_pago_monto_total" value="0.00" autocomplete="off">
                          <label class="label-modal_forma_pago_monto_total">0.00</label>
                        </th>
                      </tr>
                      <tr>
                        <th class="text-right" colspan="2">TOTAL A COBRAR</th>
                        <th class="text-right">
                          <input type="hidden" class="input-total_detalle_productos_pos" value="0.00" autocomplete="off">
                          <label class="label-total_detalle_productos_pos">0.00</label>
                        </th>
                      </tr>
                      <tr>
                        <th class="text-right th-label-vuelto" colspan="2">VUELTO</th>
                        <th class="text-right th-label-vuelto">
                          <input type="hidden" class="input-vuelto_pos" value="0.00" autocomplete="off">
                          <label class="label-vuelto_pos">0.00</label>
                        </th>
                      </tr>
                      <tr>
                        <th class="text-right th-label-saldo" colspan="2">SALDO</th>
                        <th class="text-right th-label-saldo">
                          <input type="hidden" class="input-saldo_pos_cliente" value="0.00" autocomplete="off">
                          <label class="label-saldo_pos_cliente">0.00</label>
                        </th>
                      </tr>
                    </tfoot>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <div class="col-xs-6">
              <button type="button" id="btn-salir" class="btn btn-danger btn-lg btn-block pull-center" data-dismiss="modal">Cancelar Pago</button>
            </div>
            <div class="col-xs-6">
              <button type="button" id="btn-ticket" class="btn btn-success btn-lg btn-block pull-center btn-generar_pedido" data-type="generar_ticket">Generar venta</button>
            </div>
          </div>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>
    <!-- /. Forma Pago POS modal -->
    <?php echo form_close(); ?>

    <?php } else { ?>
      <div class="col-xs-12">
        <h3><span class="label label-danger">La caja esta cerrada</span><br>Para abrir nuestra caja ir a la opción:<br>Punto de venta > Apertura de Caja</h3>
      </div>
    <?php } ?>
  </section>
  <!-- /. Main content -->
</div>
<!-- /.content-wrapper -->