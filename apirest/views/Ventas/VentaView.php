<!-- Content Wrapper. Contains page content -->
<?php
  $sCssDisplay17='style="display:none"';
  if ( $this->empresa->Nu_Tipo_Rubro_Empresa == '17' ){//La cava del baco
    $sCssDisplay17='';
  }
?>
<div class="content-wrapper">
  <!-- Main content -->
  <section class="content">
    <!-- New box-header -->
    <div class="row">
      <div class="col-xs-12">
        <div class="div-content-header">
          <h3 class="h3-titulo">
            <?php
            //echo $iNumeroDocumento;
            //echo base_url() . 'Ventas/VentaController/generarRepresentacionInternaPDF/';
            ?>
            <i class="<?php echo $this->MenuModel->verificarAccesoMenuCRUD()->Txt_Css_Icons; ?>" aria-hidden="true"></i> <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?>
            &nbsp;<a href="<?php echo $this->MenuModel->verificarAccesoMenuCRUD()->Txt_Url_Video; ?>" target="_blank"><i class="fa fa-youtube-play red" aria-hidden="true" title="Video <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?>" alt="Video <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?>"></i> <span style="color: #7b7b7b; font-size: 14px;">ver video<span></a>
          </h3>
        </div>
      </div>
    </div>
    <!-- ./New box-header -->
    
    <div class="row">
      <div class="col-xs-12">
        <div class="box box-content" style="padding-top: 0px">
          <!-- box-header -->
          <div class="box-header box-header-new div-Listar">
            <div class="row div-Filtros">
          	  <input type="hidden" id="hidden-sMethod" name="sMethod" class="form-control" value="<?php echo $this->router->method; ?>">
              <?php if ( $this->empresa->Nu_Estado_Pago_Sistema == 0 ) { ?>
                <div class="alert alert-danger alert-dismissible">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                  <strong>Advertencia:</strong> No se guardará información porque ya se venció el pago. <button type="button" class="btn btn-success" style="padding: 5px 25px; font-size: 16px;" data-toggle="modal" data-target="#modal-pago_cuenta_bancarias_laesystems">Pagar aquí</button>
                </div>
              <?php }
              if ( $this->user->No_Usuario == 'root' ){ ?>
              <div class="col-xs-12 col-sm-4 col-md-4">
                <label>Empresa</label>
                <div class="form-group">
                  <select id="cbo-filtro_empresa" name="ID_Empresa" class="form-control select2" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              <div class="col-xs-12 col-sm-4 col-md-4">
                <label>Organización</label>
                <div class="form-group">
                  <select id="cbo-filtro_organizacion" name="ID_Organizacion" class="form-control select2" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-12 col-sm-4 col-md-2">
                <label>Tipo Sistema</label>
                <div class="form-group">
                  <select id="cbo-filtro-tipo_sistema" name="Nu_Tipo_Sistema" class="form-control" style="width: 100%;">
    		  				  <option value="" selected="selected">Todos</option>
    		  				  <option value="2">SUNAT</option>
                    <option value="1">PSE N</option>
                    <option value="3">INTERNO</option>
                  </select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-12 col-sm-4 col-md-2">
                <label>Estado Sistema</label>
                <div class="form-group">
                  <select id="cbo-filtro-estado_sistema" name="Nu_Estado_Sistema" class="form-control" style="width: 100%;">
    		  				  <option value="-" selected="selected">Todos</option>
    		  				  <option value="0">Demostración</option>
                    <option value="1">Producción</option>
                  </select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              <?php } else { ?>
                <input type="hidden" id="cbo-filtro_empresa" name="ID_Empresa" class="form-control" value="<?php echo $this->user->ID_Empresa; ?>">
                <input type="hidden" id="cbo-filtro_organizacion" name="ID_Organizacion" class="form-control" value="<?php echo $this->user->ID_Organizacion; ?>">
                <input type="hidden" id="cbo-filtro-tipo_sistema" name="Nu_Tipo_Sistema" class="form-control" value="<?php echo $this->empresa->Nu_Tipo_Proveedor_FE; ?>">
                <input type="hidden" id="cbo-filtro-estado_sistema" name="Nu_Estado_Sistema" class="form-control" value="<?php echo $this->empresa->Nu_Estado_Sistema; ?>">
              <?php } ?>
              
              <div class="col-xs-4 col-sm-4 col-md-2">
                <label>Almacén</label>
                <div class="form-group">
                  <select id="cbo-filtro_almacen" name="ID_Almacen" class="form-control select2" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-4 col-sm-3 col-md-2">
                <label>F. Inicio</label>
                <div class="form-group">
                  <div class="input-group date" style="width:100%">
                    <!--<input type="text" id="txt-Filtro_Fe_Inicio" class="form-control date-picker-report_crud" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>-->
                    <input type="text" id="txt-Filtro_Fe_Inicio" class="form-control date-picker-report_crud" value="<?php echo dateNow('month_date_report_crud'); ?>" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                  </div>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-4 col-sm-3 col-md-2">
                <label>F. Fin</label>
                <div class="form-group">
                  <div class="input-group date" style="width:100%">
                    <!--<input type="text" id="txt-Filtro_Fe_Fin" class="form-control date-picker-report_end txt-Filtro_Fe_Fin" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>-->
                    <input type="text" id="txt-Filtro_Fe_Fin" class="form-control date-picker-report_crud txt-Filtro_Fe_Fin" value="<?php echo dateNow('month_date_report_crud'); ?>" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                  </div>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-4 col-sm-4 col-md-2 col-lg-2">
                <label>Tipo</label>
                <div class="form-group">
    		  				<select id="cbo-Filtro_TiposDocumento" class="form-control"></select>
                </div>
              </div>
              
              <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
                <label>Serie</label>
                <div class="form-group">
                  <select id="cbo-Filtro_SeriesDocumento" class="form-control"></select>
                </div>
              </div>
              
              <div class="col-xs-4 col-sm-5 col-md-2 col-lg-2">
                <label>Número</label>
                <div class="form-group">
                  <input type="tel" id="txt-Filtro_NumeroDocumento" class="form-control input-number" maxlength="8" placeholder="Opcional" value="<?php echo $iNumeroDocumento; ?>" autocomplete="off">
                </div>
              </div>
              
              <div class="col-xs-6 col-sm-3 col-md-2 col-lg-2">
                <label>Estado</label>
                <div class="form-group">
    		  				<select id="cbo-Filtro_Estado" class="form-control">
    		  				  <option value="" selected="selected">Todos</option>
    		  				  <option value="6">Completado</option>
    		  				  <option value="8">Completado Enviado</option>
    		  				  <option value="9">Completado Error</option>
    		  				  <option value="7">Anulado</option>
    		  				  <option value="10">Anulado Enviado</option>
    		  				  <option value="11">Anulado Error</option>
        				  </select>
                </div>
              </div>

              <div class="col-xs-6 col-sm-3 col-md-2 col-lg-2">
                <label>Estado Pago</label>
                <div class="form-group">
    		  				<select id="cbo-Filtro_Estado_Pago" class="form-control">
    		  				  <option value="0" selected="selected">Todos</option>
    		  				  <option value="1">Pendiente</option>
                    <option value="2">Cancelado</option>
    		  				</select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                <label>Cliente</label>
                <div class="form-group">
                  <input type="hidden" id="txt-AID_Doble" name="AID" class="form-control">
				          <span class="clearable">
                    <input type="text" id="txt-Filtro_Entidad" class="form-control autocompletar clearable" data-global-class_method="AutocompleteController/getAllClient" data-global-table="entidad" placeholder="Buscar por Nombre / Número de Documento de Identidad" value="" autocomplete="off">
                    <i class="clearable__clear">&times;</i>
                  </span>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-6 col-sm-6 col-md-2">
                <label class="hidden-xs hidden-sm">&nbsp;</label>
                <div class="form-group">
                  <button type="button" id="btn-filter" class="btn btn-primary btn-block"><i class="fa fa-search"></i> Buscar</button>
                </div>
              </div>
              
              <div class="col-xs-6 col-sm-6 col-md-2">
                <label class="hidden-xs hidden-sm">&nbsp;</label>
                <div class="form-group">
                  <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Agregar == 1 && $this->empresa->Nu_Lae_Gestion==1) : ?>
                    <button type="button" class="btn btn-success btn-block" onclick="agregarVenta()"><i class="fa fa-plus-circle"></i> Agregar</button>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="table-responsive div-Listar">
            <table id="table-Venta" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <?php if ( $this->user->No_Usuario == 'root' ){ ?>
                    <th class="no-sort">Empresa</th>
                  <?php } ?>
                  <th class="no-sort">Almacén</th>
                  <th class="no-sort_left">F. Emisión</th>
                  <th class="no-sort">Tipo</th>
                  <th class="no-sort">Serie</th>
                  <th class="no-sort_right">Número</th>
                  <th class="no-hidden no-sort_left">Nro. Doc. Ident.</th>
                  <th class="no-sort_left">Cliente</th>
                  <th class="no-sort">F. Pago</th>
                  <th class="no-sort">M</th>
                  <th class="no-sort_right">Total</th>
                  <th class="no-sort_right">Saldo</th>
                  <th class="no-hidden no-sort">Stock</th>
                  <th class="no-hidden no-sort_right">Observaciones</th>
                  <th class="no-hidden no-sort_right">Guia Escrita</th>
                  <th class="no-hidden no-sort_right">O/C</th>
                  <th class="no-hidden no-sort_right">Placa</th>
                  <!--<th class="no-sort">Pago</th>
                  <th class="no-sort">Estado</th>-->
                  <!--<th class="no-sort"></th>--><!-- send SUNAT -->
                  <?php //if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Editar == 1) : ?>
                    <th class="no-sort">Editar</th>
                  <?php //endif; ?>
                  <?php //if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Eliminar == 1) : ?>
                    <th class="no-sort">Anular</th>
                  <?php //endif; ?>
                  <th class="no-sort">PDF</th><!-- PDF -->
                  <!--<th class="no-sort">XML</th>--><!-- XML -->
                  <th class="no-sort">SUNAT</th><!-- CDR -->
                  <th class="no-sort">Opciones</th><!-- dropdown -->
                  <th class="no-sort"></th><!-- Representación interna -->
                  <th class="no-sort">Enlace</th>
                  <th class="no-sort">Guía</th>
                  <th class="no-sort">Repetir</th><!-- Repetir -->
                </tr>
              </thead>
            </table>
          </div>
          <!-- /.box-body -->
          
          <?php
          $attributes = array('id' => 'form-cobrar_cliente');
          echo form_open('', $attributes);
          ?>
          <!-- modal cobrar cliente -->
          <div class="modal fade modal-cobrar_cliente" id="modal-default">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h4 class="text-center" id="modal-header-cobrar_cliente-title"></h4>

                  <div class="row">
                    <div class="col-sm-12">
                      <label id="cobrar_cliente-modal-body-cliente"></label>
                    </div>
                    <div class="col-sm-12">
                      <label id="cobrar_cliente-modal-body-saldo_cliente"></label>
                    </div>
                  </div>
                </div>

                <div class="modal-body">
                  <input type="hidden" name="iIdDocumentoCabecera" class="form-control">
                  <input type="hidden" name="iEstadoLavadoRecepcionCliente" class="form-control" value="2">
                  <input type="hidden" id="hidden-cobrar_cliente-fsaldo" name="fSaldoCliente" class="form-control" value="0">
                  <input type="hidden" id="hidden-cobrar_cliente-detraccion"name="iCobrarModalDetraccion" class="form-control" value="0">
                  
                  <div class="row div-forma_pago">
                    <div class="col-xs-4 col-sm-3">
                      <div class="form-group">
                        <label>F. Pago</label>
                        <div class="input-group date">
                          <input type="text" id="txt-Fe_Emision_Hora_Pago" name="Fe_Emision_Hora_Pago" class="form-control date-picker-invoice required" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                        </div>
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>

                    <div class="col-xs-4 col-sm-3">
                      <label>Forma Pago</label>
                      <div class="form-group">
                        <select id="cbo-modal_forma_pago" name="iFormaPago" class="form-control" style="width: 100%;"></select>
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>
                    
                    <div class="col-xs-4 col-sm-3 div-modal_datos_tarjeta_credito">
                      <label>Tarjeta</label>
                      <div class="form-group">
                        <select id="cbo-cobrar_cliente-modal_tarjeta_credito" name="iTipoMedioPago" class="form-control" style="width: 100%;"></select>
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>
                    
                    <div class="col-xs-4 col-sm-3">
                      <label>Pago cliente</label>
                      <div class="form-group">
                        <input type="tel" class="form-control input-decimal" id="modal-tel-cobrar_cliente-fPagoCliente" name="fPagoCliente" value="" autocomplete="off">
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>

                    <div class="col-xs-4 col-sm-3 div-modal_datos_tarjeta_credito">
                      <label>Opcional</label>
                      <div class="form-group">
                        <input type="tel" id="modal-tel-nu_referencia" name="iNumeroTransaccion" class="form-control input-number" value="" maxlength="10" placeholder="No. Operación" autocomplete="off">
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>
                    <div class="col-xs-4 col-sm-3 div-modal_datos_tarjeta_credito">
                      <label>Opcional</label>
                      <div class="form-group">
                        <input type="tel" id="modal-tel-nu_ultimo_4_digitos_tarjeta" name="iNumeroTarjeta" class="form-control input-number" maxlength="4" value="" placeholder="últimos 4 dígitos" autocomplete="off">
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>
                  </div><!-- ./ row importes -->
                </div>
                <div class="modal-footer">
                  <div class="col-xs-6">
                    <button type="button" id="btn-salir" class="btn btn-danger btn-md btn-block pull-center" data-dismiss="modal">Salir</button>
                  </div>
                  <div class="col-xs-6">
                    <button type="button" id="btn-cobrar_cliente" class="btn btn-primary btn-md btn-block pull-center">Cobrar</button>
                  </div>
                </div>
              </div>
              <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
          </div>
          <!-- /. modal cobrar cliente -->
          <?php echo form_close(); ?>
          
          <div class="box-body div-AgregarEditar">
            <?php
            $attributes = array('id' => 'form-Venta');
            echo form_open('', $attributes);
            ?>
          	  <input type="hidden" id="txt-EID_Empresa" name="EID_Empresa" class="form-control">
          	  <input type="hidden" id="txt-EID_Documento_Cabecera" name="EID_Documento_Cabecera" class="form-control">
          	  <input type="hidden" id="txt-ID_Documento_Cabecera_Orden" name="ID_Documento_Cabecera_Orden" class="form-control">
              
      			  <div class="row">
                <div class="col-sm-12 col-md-6 cortar-padding">
        			    <div class="panel panel-default">
                    <div class="panel-heading"><i class="fa fa-book" style="font-size: 1.6rem;"></i> <b style="font-size: 1.6rem;">Documento</b></div>
                    <div class="panel-body">
                      <div class="row">
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-3">
                          <label>Tipo <span class="label-advertencia">*</span></label>
                          <div class="form-group">
                            <select id="cbo-TiposDocumento" name="ID_Tipo_Documento" class="form-control required" style="width: 100%;"></select>
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>
                      
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-3">
                          <label>Serie <span class="label-advertencia">*</span></label>
                          <div class="form-group">
                            <select id="cbo-SeriesDocumento" name="ID_Serie_Documento" class="form-control required" style="width: 100%;"></select>
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>

                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-3">
                          <label>F. Emisión</label>
                          <div class="form-group">
                            <div class="input-group date">
                              <input type="text" id="txt-Fe_Emision" name="Fe_Emision" class="form-control date-picker-invoice required" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                            </div>
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>
                        
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-3">
                          <div class="form-group">
                            <label>Moneda</label>
                            <select id="cbo-Monedas" class="form-control required" style="width: 100%;"></select>
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>
                        
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-3">
                          <div class="form-group">
                            <label>F. Pago</label>
                            <select id="cbo-MediosPago" class="form-control required" style="width: 100%;"></select>
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>
                        
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-3 div-MediosPago">
                          <div class="form-group">
                            <label>F. Vcto</label>
                            <div class="input-group date" style="width: 100%;">
                              <input type="text" id="txt-Fe_Vencimiento" class="form-control required" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                            </div>
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>
                        
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-3 div-modal_datos_tarjeta_credito">
                          <label>Tipo</label>
                          <div class="form-group">
                            <select id="cbo-tarjeta_credito" class="form-control" style="width: 100%;"></select>
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>
                        
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-3 div-modal_datos_tarjeta_credito">
                          <label>Opcional</label>
                          <div class="form-group">
                            <input type="tel" id="tel-nu_referencia" class="form-control input-number" value="" maxlength="10" placeholder="No. Operación" autocomplete="off">
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>

                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-3 div-modal_datos_tarjeta_credito">
                          <label>Opcional</label>
                          <div class="form-group">
                            <input type="tel" id="tel-nu_ultimo_4_digitos_tarjeta" class="form-control input-number" minlength="4" maxlength="4" value="" placeholder="últimos 4 dígitos" autocomplete="off">
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>
                        
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-3">
                          <label>L. Precio</label>
                          <div class="form-group">
                            <select id="cbo-lista_precios" class="form-control required" style="width: 100%;"></select>
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>

                        <div class="col-xs-8 col-sm-8 col-md-4 col-lg-3">
                          <div class="css_tooltip">
                              <label>&nbsp;</label>
                            <button type="button" id="btn-adicionales_fv" class="btn btn-link btn-lg btn-block">Adicionales</button>
                            <span class="css_tooltiptext">Agregar vendedor, observación, guía y más.</span>
                          </div>
                        </div>
                        
                        <div class="row hidden">
                          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-3">
                              <label>&nbsp;</label>
                              <button type="button" id="btn-adicionales_fv" class="btn btn-link btn-lg btn-block">Adicionales</button>
                            </div>

                            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-3 hidden">
                              <label>&nbsp;</label>
                              <button type="button" id="btn-guias_remision_fv" class="btn btn-link btn-lg btn-block">Guía</button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div><!-- /. Datos del documento -->

                <div class="col-sm-12 col-md-6 cortar-padding" style="margin-left: 0px;padding-left: 0px;">
        			    <div class="panel panel-default">
                    <div class="panel-heading"><i class="fa fa-user" style="font-size: 1.6rem;"></i> <b style="font-size: 1.6rem;">Cliente</b></div>
                    <div class="panel-body">
                      <div class="row">
                      <div id="div-cliente_rapido" class="col-xs-4 text-center" title="Solo para boletas y ventas menores < 700.00">
                        <label style="cursor: pointer;" title="Solo para boletas y ventas menores < 700.00"><input type="radio" name="addCliente" id="radio-cliente_varios" class="flat-red" value="3" title="Solo para boletas y ventas menores < 700.00"> Varios</label>
                      </div>
                      
                      <div class="col-xs-4 text-center">
                        <label style="cursor: pointer;"><input type="radio" name="addCliente" id="radio-cliente_existente" class="flat-red" value="0"> Existe</label>
                      </div>
                      
                      <div class="col-xs-4 text-center">
                        <label style="cursor: pointer;"><input type="radio" name="addCliente" id="radio-cliente_nuevo" class="flat-red" value="1"> Nuevo</label>
                      </div>
                      
                      <div class="col-xs-12 div-cliente_existente">
                        <label>Cliente <span class="label-advertencia">*</span></label>
                        <div class="form-group text-right">
                          <input type="hidden" id="hidden-ID_Tipo_Documento_Identidad_Existente" name="ID_Tipo_Documento_Identidad_Existente" class="form-control required">
                          <input type="hidden" id="txt-AID" name="AID" class="form-control required">
				                  <span class="clearable">
                            <input type="text" id="txt-ANombre" name="ANombre" class="form-control autocompletar clearable" data-global-class_method="AutocompleteController/getAllClient" data-global-table="entidad" placeholder="Buscar por Nombre / Número de Documento de Identidad" value="" autocomplete="off">
                            <i class="clearable__clear">&times;</i>
                          </span>
                          <span class="help-block" id="error"></span>
                          <button type="button" id="btn-actualizar_datos_cliente" data-display_data_cliente="0" class="btn btn-md btn-link">Actualizar datos</button>
                        </div>
                      </div>
                            
                      <div class="col-xs-12 col-md-7 hidden">
                        <div class="form-group">
                          <label>Número Documento Identidad</label>
                          <input type="text" id="txt-ACodigo" name="ACodigo" class="form-control required" disabled>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>

                      <div class="col-xs-7 col-sm-6 col-md-6 col-lg-4 div-actualizar_datos_cliente">
                        <div class="form-group">
                          <label>Email</label>
                          <input type="text" id="txt-Txt_Email_Entidad" name="Txt_Email_Entidad" class="form-control" placeholder="" value="" autocomplete="off" maxlength="100">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>

                      <div class="col-xs-5 col-sm-6 col-md-6 col-lg-4 div-actualizar_datos_cliente">
                        <div class="form-group">
                          <label>Celular</label>
                          <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Se usará para enviar por whatsApp el comprobante">
                            <i class="fa fa-info-circle"></i>
                          </span>
                          <input type="tel" id="txt-Nu_Celular_Entidad" name="Nu_Celular_Entidad" class="form-control" data-inputmask="'mask': ['999 999 999']" data-mask autocomplete="off" placeholder="">
                          <span class="hide" id="span-celular" style="color: #dd4b39;">Ingresa un celular válido</span>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                            
                      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-4 div-actualizar_datos_cliente">
                        <div class="form-group">
                          <label>Dirección</label>
                          <input type="text" id="txt-Txt_Direccion_Entidad" name="Txt_Direccion_Entidad" class="form-control" autocomplete="off">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      <!-- /. Cliente Existente -->
                      
                      <!-- Cliente Nuevo -->
                      <div class="row">
                        <div class="col-xs-12 div-cliente_nuevo">
                          <div class="col-xs-4 col-sm-4 col-md-4 col-lg-3 div-cliente_nuevo">
                            <div class="form-group">
                              <label>T.D.I</label>
                              <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Tipo de Documento Identidad">
                                <i class="fa fa-info-circle"></i>
                              </span>
                              <select id="cbo-TiposDocumentoIdentidadCliente" name="ID_Tipo_Documento_Identidad" class="form-control required" style="width: 100%;"></select>
                              <span class="help-block" id="error"></span>
                            </div>
                          </div>
                          
                          <div class="col-xs-5 col-sm-5 col-md-5 col-lg-7 div-cliente_nuevo">
                            <div class="form-group">
                              <label id="label-Nombre_Documento_Identidad_Cliente">DNI</label></span>
                              <input type="text" id="txt-Nu_Documento_Identidad_Cliente" name="Nu_Documento_Identidad_Cliente" class="form-control input-Mayuscula input-codigo_barra" placeholder="Ingresar número" value="" maxlength="8" autocomplete="off">
                              <span class="help-block" id="error"></span>
                            </div>
                          </div>
                          
                          <div class="col-xs-3 col-sm-3 col-md-3 col-lg-2 text-center div-cliente_nuevo">
                            <label>Api</label>
                            <div class="form-group">
                              <button type="button" id="btn-cloud-api_venta_cliente" class="btn btn-success btn-block btn-md"><i class="fa fa-cloud-download fa-lg"></i></button>
                              <span class="help-block" id="error"></span>
                            </div>
                          </div>
                        </div>
                      </div>
              
                      <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9 div-cliente_nuevo">
                        <label id="label-No_Entidad_Cliente">Nombre(s) y Apellidos</label><span class="label-advertencia"> *</span>
                        <div class="form-group">
                          <input type="text" id="txt-No_Entidad_Cliente" name="No_Entidad_Cliente" class="form-control" placeholder="Ingresar nombre" value="" autocomplete="off" maxlength="100">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 div-cliente_nuevo">
                        <div class="form-group estado">
                          <label>Estado</label>
            		  				<select id="cbo-Estado" name="Nu_Estado" class="form-control required">
            		  				  <option value="1">Activo</option>
            		  				  <option value="0">Inactivo</option>
            		  				</select>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 div-cliente_nuevo div-tipo_cliente_1">
                        <div class="form-group">
                          <label>Tipo</label>
                          <select id="cbo-tipo_cliente_1-nuevo" name="ID_Tipo_Cliente_1" class="form-control" style="width: 100%;">
                            <option value="0" selected="selected">- Seleccionar -</option>
                            <option value="2088">Licoreria La Cava de Baco</option>
                            <option value="2089">Corporativo</option>
                            <option value="2090">Mayorista</option>
                          </select>
                        </div>
                      </div>

                      <div class="col-xs-7 col-sm-6 col-md-6 col-lg-4 div-cliente_nuevo">
                        <div class="form-group">
                          <label>Email</label>
                          <input type="text" id="txt-Txt_Email_Entidad_Cliente" name="Txt_Email_Entidad_Cliente" class="form-control" placeholder="opcional" value="" autocomplete="off" maxlength="100">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>

                      <div class="col-xs-5 col-sm-6 col-md-6 col-lg-4 div-cliente_nuevo">
                        <div class="form-group">
                          <label>Celular</label>
                          <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Se usará para enviar por whatsApp el comprobante">
                            <i class="fa fa-info-circle"></i>
                          </span>
                          <input type="tel" id="txt-Nu_Celular_Entidad_Cliente" name="Nu_Celular_Entidad" class="form-control" data-inputmask="'mask': ['999 999 999']" data-mask autocomplete="on" placeholder="opcional">
                          <span class="hide" id="span-celular" style="color: #dd4b39;">Ingresa un celular válido</span>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>

                      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-4 div-cliente_nuevo">
                        <div class="form-group">
                          <label>Dirección</label>
                          <input type="text" id="txt-Txt_Direccion_Entidad_Cliente" name="Txt_Direccion_Entidad_Cliente" placeholder="opcional" class="form-control" autocomplete="off">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-12 col-sm-3 col-md-12 col-lg-12" style="display:none">
                        <div class="form-group">
                          <label>Telefono</label>
                          <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-phone" aria-hidden="true"></i></span>
                            <input type="tel" id="txt-Nu_Telefono_Entidad_Cliente" name="Nu_Telefono_Entidad_Cliente" class="form-control" data-inputmask="'mask': ['999 9999']" data-mask autocomplete="off">
                          </div>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      <!-- /. Cliente Nuevo -->
                      </div><!-- /. row -->
                    </div><!-- /. body -->
                  </div>
                </div><!-- panel-cliente -->
              </div><!-- ./Cabecera -->
              
      			  <div class="row">
                <div class="col-sm-12 col-md-12 cortar-padding">
        			    <div class="panel panel-default div-DocumentoModificar"><!-- div-DocumentoModificar !-->
                    <div class="panel-heading"><i class="fa fa-book"></i> <b>Documento a Modificar</b></div>
                    <div class="panel-body">
                      <div class="row">
                        <div class="col-xs-4 col-sm-4 col-md-2">
                          <div class="form-group">
                            <input type="hidden" id="txt-ID_Documento_Guardado">
                            <label>Tipo <span class="label-advertencia">*</span></label>
                            <select id="cbo-TiposDocumentoModificar" class="form-control" style="width: 100%;"></select>
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>
                      
                        <div class="col-xs-4 col-sm-4 col-md-2">
                          <div class="form-group">
                            <label>Series <span class="label-advertencia">*</span></label>
                            <select id="cbo-SeriesDocumentoModificar" class="form-control" style="width: 100%;"></select>
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>
                        
                        <div class="col-xs-4 col-sm-4 col-md-2">
                          <div class="form-group">
                            <label>Número</label><span class="label-advertencia"> *</span>
                            <div class="input-group">
                              <input type="tel" id="txt-ID_Numero_Documento_Modificar" class="form-control input-number" maxlength="8" autocomplete="off">
                            </div>
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>
                        
                        <div class="col-xs-12 col-sm-12 col-md-6">
                          <div class="form-group">
                            <input type="hidden" id="txt-ID_Documento_Guardado">
                            <label>Motivo Modificar <span class="label-advertencia">*</span></label>
                            <select id="cbo-MotivoReferenciaModificar" class="form-control" style="width: 100%;"></select>
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>
                        
                        <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                          <p class="div-mensaje_verificarExisteDocumento"></p>
                        </div>
                        
                        <div class="col-xs-12 col-sm-12 col-md-12">
                          <div class="form-group">
                            <button type="button" id="btn-verificarExisteDocumento" class="btn btn-success btn-md btn-block">Verificar <i class="fa fa-check"></i></button>
                          </div>
                        </div>
                      </div><!-- row -->
                    </div><!-- panel.body -->
                  </div>
                </div>
              </div>

      			  <div class="row">
                <div class="col-md-12 cortar-padding">
        			    <div id="panel-DetalleProductosVenta" class="panel panel-default">
                    <div class="panel-heading"><i class="fa fa-shopping-cart"></i> <b style="font-size: 1.6rem;">Producto / Servicio</b></div>
                    <div class="panel-body" style="padding-top: 0px;">
                      <div class="sticky">
      			          <div class="row">
          	            <input type="hidden" name="Nu_Tipo_Lista_Precio" value="1" class="form-control"><!-- 1 = Venta -->                        
                        <div class="col-xs-12 col-sm-12 col-md-6">
                          <div class="form-group">
                            <input type="hidden" id="txt-Nu_Tipo_Registro" class="form-control" value="1"><!-- Venta -->
                            <input type="hidden" id="txt-Nu_Tipo_Producto" class="form-control" value="2"><!-- No muestra los productos de tipo interno -->
                            <input type="hidden" id="txt-Nu_Compuesto" class="form-control" value="">
                            <input type="hidden" id="txt-ID_Producto" class="form-control">
                            <input type="hidden" id="txt-Nu_Codigo_Barra" class="form-control">
                            <input type="hidden" id="txt-Ss_Precio" class="form-control">
                            <input type="hidden" id="txt-ID_Impuesto_Cruce_Documento" class="form-control">
                            <input type="hidden" id="txt-Nu_Tipo_Impuesto" class="form-control">
                            <input type="hidden" id="txt-Ss_Impuesto" class="form-control">
                            <input type="hidden" id="txt-Qt_Producto" class="form-control">
                            <input type="hidden" id="txt-nu_tipo_item" class="form-control">
                            <input type="hidden" id="txt-ID_Impuesto_Icbper" class="form-control">
                            <input type="hidden" id="txt-Ss_Icbper" class="form-control">
                            <input type="hidden" id="txt-No_Unidad_Medida" class="form-control">
                            <input type="hidden" id="txt-nu_activar_precio_x_mayor" class="form-control">
                            <input type="hidden" id="txt-no_variante_1" class="form-control">
                            <input type="hidden" id="txt-no_valor_variante_1" class="form-control">
                            <input type="hidden" id="txt-no_variante_2" class="form-control">
                            <input type="hidden" id="txt-no_valor_variante_2" class="form-control">
                            <input type="hidden" id="txt-no_variante_3" class="form-control">
                            <input type="hidden" id="txt-no_valor_variante_3" class="form-control">
                            <input type="text" id="txt-No_Producto" class="form-control autocompletar_detalle" data-global-class_method="AutocompleteController/getAllProduct" data-global-table="producto" placeholder="Buscar por Nombre / Código / SKU" value="" autocomplete="off">
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>
                        
                        <div class="col-xs-6 col-sm-6 col-md-3">
                          <div class="form-group">
                            <button type="button" id="btn-addProducto" class="btn btn-success btn-md btn-block"> Agregar Item</button>
                          </div>
                        </div>
                        
                        <div class="col-xs-6 col-sm-6 col-md-3">
                          <div class="form-group">
                            <button type="button" id="btn-crearItem" class="btn btn-default btn-md btn-block"> Crear Item</button>
                          </div>
                        </div>
                      </div>
                      </div>
                      
      			          <div class="row">
                      <div class="col-md-12 delete-position">
                      <div class="table-responsive">
                        <table id="table-DetalleProductosVenta" class="table table-striped table-bordered">
                          <thead>
                            <tr>
                              <th style="display:none;" class="text-left"></th>
                              <th class="text-center" style="width: 7%;">Cantidad</th>
                              <th class="text-left" style="width: 33%;">Item</th>
                              <th class="text-center" style="width: 5%;"></th>
                              <th class="text-center" style="width: 8%;">V. U.</th>
                              <th class="text-center" style="width: 8%;">Precio</th>
                              <th class="text-center" style="width: 19%;">Impuesto</th>
                              <th class="text-center" style="display:none;">Sub Total</th>
                              <th class="text-center" style="width: 8%;">% Dscto</th>
                              <th class="text-center" style="width: 12%;">Total</th>
                              <th class="text-center" style="display:none;">Nro. Lote</th>
                              <th class="text-center" style="display:none; width: 12%;">F. Vcto. Lote</th>
                              <th class="text-center"></th>
                            </tr>
                          </thead>
                          <tbody>
                          </tbody>
                        </table>
                      </div>
                      </div>
                      </div>
                    </div>
                  </div>
                </div><!-- ./Detalle -->
              </div>
                              
      			  <div class="row"><!-- Totales -->
                <div class="col-md-12 cortar-padding">
      			    <div class="panel panel-default">
                  <div class="panel-heading text-right">
                    TOTAL <b class="hidden">CANTIDAD: </b> <span id="span-total_cantidad" class="hidden">0</span> <span class="span-signo" style="font-size: 20px;font-weight: bold;"></span> <span id="span-total_importe" style="font-size: 20px;font-weight: bold;">0</span><button type="button" id="btn-ver_total_todo" class="btn btn-link" data-ver_total_todo="0">VER / DESCUENTO</button>
                  </div>
                  <div class="panel-body panel_body_total_todo">
                    <div class="row">
                    <div class="table-responsive">
                    <table class="table" id="table-VentaTotal">
                      <tr>
                        <td class="text-center"><label class="input-size_otros">% Descuento</label></td>
                        <td class="text-right hidden-xs hidden-sm"><label>Inafectas</label></td>
                        <td class="text-right hidden-xs hidden-sm"><label>Exoneradas</label></td>
                        <td class="text-right hidden-xs hidden-sm"><label>Gratuitas</label></td>
                        <td class="text-right hidden-xs hidden-sm"><label>Gravadas</label></td>
                        <td class="text-right"><label>Dscto. Total (-)</label></td>
                        <td class="text-right hidden-xs hidden-sm"><label>I.G.V.</label></td>
                        <td class="text-right hidden-xs hidden-sm"><label>ICBPER</label></td>
                        <td class="text-right"><label>Total</label></td>
                      </tr>
                      <tr>
                        <td class="text-center">
    	  					        <input type="text" class="form-control input-decimal input-size_otros" inputmode="decimal" id="txt-Ss_Descuento" name="Ss_Descuento" size="3" value="" autocomplete="off" />
                        </td>

                        <td class="text-right hidden-xs hidden-sm">
                          <input type="hidden" class="form-control" id="txt-inafecto" value="0.00"/>
                          <span class="span-signo"></span> <span id="span-inafecto">0.00</span>
                        </td>

                        <td class="text-right hidden-xs hidden-sm">
                          <input type="hidden" class="form-control" id="txt-exonerada" value="0.00"/>
                          <span class="span-signo"></span> <span id="span-exonerada">0.00</span>
                        </td>
                        
                        <td class="text-right hidden-xs hidden-sm">
                          <input type="hidden" class="form-control" id="txt-gratuita" value="0.00"/>
                          <span class="span-signo"></span> <span id="span-gratuita">0.00</span>
                        </td>

                        <td class="text-right hidden-xs hidden-sm">
    	  					        <input type="hidden" class="form-control" id="txt-subTotal" value="0.00"/>
                          <span class="span-signo"></span> <span id="span-subTotal">0.00</span>
                        </td>

                        <td class="text-right">
                          <input type="hidden" class="form-control" id="txt-descuento_igv" value="0.00"/>
                          <input type="hidden" class="form-control" id="txt-descuento" value="0.00"/>
                          <span class="span-signo"></span> <span id="span-descuento">0.00</span>
                        </td>

                        <td class="text-right hidden-xs hidden-sm">
                          <input type="hidden" class="form-control" id="txt-impuesto" value="0.00"/>
                          <span class="span-signo"></span> <span id="span-impuesto">0.00</span>
                        </td>

                        <td class="text-right hidden-xs hidden-sm">
                          <input type="hidden" class="form-control" id="txt-total_icbper" value="0.00"/>
                          <span class="span-signo"></span> <span id="span-total_icbper">0.00</span>
                        </td>

                        <td class="text-right">
                          <input type="hidden" class="form-control" id="txt-total" value="0.00"/>
                          <span class="span-signo"></span> <span id="span-total">0.00</span>
                        </td>
                      </tr>
                    </table><!-- ./Totales -->
                    </div><!-- responsive -->
                    </div><!-- row -->
                  </div><!-- panel-body -->
                </div>
              </div>
              
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

              <!-- Modal Adicionales -->
              <div class="modal fade modal-adicionales" id="modal-default">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-body">
                      <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                          <h4 class="text-center">Adicionales</h4>
                        </div>

                        <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
                          <div class="form-group">
                            <label>Tipo Operación</label>
                            <select id="cbo-sunat_tipo_transaction" name="ID_Sunat_Tipo_Transaction" class="form-control select2" style="width: 100%;"></select>
                          </div>
                        </div>

                        <div class="col-xs-3 col-sm-2 col-md-2 col-lg-2">
                          <div class="form-group">
                            <label>¿Stock?</label>
                            <select id="cbo-descargar_stock" class="form-control required" style="width: 100%;"></select>
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>

                        <div class="col-xs-3 col-sm-2 col-md-2 col-lg-2">
                          <label>PDF</label>
                          <div class="form-group">
                            <select id="cbo-formato_pdf" class="form-control required" style="width: 100%;">
                              <option value="A4" selected="selected">A4</option>
                              <option value="A5">A5</option>
                              <option value="TICKET">Ticket</option>
                            </select>
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>
                        
                        <div class="col-xs-6 col-sm-2 col-md-2 col-lg-2">
                          <label>Detracción</label>
                          <div class="form-group" style="height: 0px;">
                            <label style="cursor: pointer;">
                              <input type="radio" style="cursor: pointer;" name="radio-addDetraccion" class="flat-red" id="radio-InactiveDetraccion" onclick="addDetraccion(this.value);" value="0" checked> No
                            </label>
                            <label style="cursor: pointer;">
                              &nbsp;<input type="radio" style="cursor: pointer;" name="radio-addDetraccion" class="flat-red" id="radio-ActiveDetraccion" onclick="addDetraccion(this.value);" value="1"> Si
                            </label>
                          </div>
                        </div>
                        
                        <div class="col-xs-6 col-sm-2 col-md-2 col-lg-2 div-detraccion">
                          <label>% Detraccion</label>
                          <div class="form-group">
                            <input type="text" class="form-control input-decimal" inputmode="decimal" id="txt-Po_Detraccion" name="Po_Detraccion" value="12" autocomplete="off" />
                          </div>
                        </div>

                        <div class="col-xs-6 col-sm-2 col-md-2 col-lg-2 <?php //echo ($this->empresa->Nu_Tipo_Proveedor_FE == 2 ? '' : 'hidden'); ?>">
                          <label>Retención</label>
                          <div class="form-group">
                            <label style="cursor: pointer;">
                              <input type="radio" style="cursor: pointer;" name="radio-addRetencion" class="flat-red" id="radio-InactiveRetencion" onclick="addRetencion(this.value);" value="0" checked> No
                            </label>
                            <label style="cursor: pointer;">
                              &nbsp;<input type="radio" style="cursor: pointer;" name="radio-addRetencion" class="flat-red" id="radio-ActiveRetencion" onclick="addRetencion(this.value);" value="1"> Si
                            </label>
                          </div>
                        </div>

                        <div class="col-xs-6 col-sm-6 col-md-7 col-lg-6">
                          <div class="form-group">
                            <label>Vendedor</label>
                            <select id="cbo-vendedor" name="ID_Mesero" class="form-control select2" style="width: 100%;"></select>
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>

                        <div class="col-xs-6 col-sm-3 col-md-3 col-lg-2">
                          <label>Recepción</label>
                          <div class="form-group">
                            <select id="cbo-recepcion" class="form-control" name="Nu_Tipo_Recepcion"></select>
                          </div>
                        </div>
                        
                        <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2 div-fecha_entrega">
                          <label>F. Entrega</label>
                          <div class="form-group">
                            <div class="input-group date" style="width:100%">
                              <input type="text" id="txt-fe_entrega" name="Fe_Entrega" class="form-control input-datepicker-today-to-more required" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                            </div>                
                          </div>
                        </div>

                        <div class="col-xs-12 col-sm-6 col-md-6">
                          <label>Observaciones</label>
                          <div class="form-group">
                            <textarea name="Txt_Glosa" class="form-control"  placeholder="Opcional" rows="1"></textarea>
                          </div>
                        </div>

                        <div class="col-xs-12 col-sm-6 col-md-6">
                          <label>Guia: Serie(4)-Número(8)</label>
                          <div class="form-group">
                            <textarea name="Txt_Garantia" class="form-control input-Mayuscula input-guias_remision" placeholder="Opcional" rows="1"></textarea>
                          </div>
                        </div>

                        <div class="col-xs-6 col-sm-4 col-md-4 hidden">
                          <label>Canal Venta</label>
                          <span style="cursor: pointer;" class="" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Canal de Venta">
                            <i class="fa fa-info-circle"></i>
                          </span>
                          <div class="form-group">
                            <select id="cbo-canal_venta" class="form-control" style="width: 100%;"></select>
                          </div>
                        </div>

                        <div class="col-xs-6 col-sm-4 col-md-3">
                          <label>O/C / Servicio</label>
                          <div class="form-group">
                            <input type="text" name="No_Orden_Compra_FE" class="form-control" maxlength="20" placeholder="Opcional" value="" autocomplete="off">
                          </div>
                        </div>
                        
                        <div class="col-xs-6 col-sm-4 col-md-3">
                          <label>Placa</label>
                          <div class="form-group">
                            <input type="text" name="No_Placa_FE" class="form-control" maxlength="255" placeholder="Opcional" value="" autocomplete="off">
                          </div>
                        </div>
                        
                        <div class="col-xs-6 col-sm-4 col-md-3">
                          <label>Nro. Expediente</label>
                          <div class="form-group">
                            <input type="text" name="Nu_Expediente_FE" class="form-control" maxlength="50" placeholder="Opcional" value="" autocomplete="off">
                          </div>
                        </div>
                        
                        <div class="col-xs-6 col-sm-4 col-md-3">
                          <label>Unidad Ejecutora</label>
                          <div class="form-group">
                            <input type="text" name="Nu_Codigo_Unidad_Ejecutora_FE" class="form-control" maxlength="50" placeholder="Opcional" value="" autocomplete="off">
                          </div>
                        </div>

                        <div class="col-xs-4 col-sm-4 col-md-4 hidden">
                          <div class="form-group">
                            <label>Porcentaje</label>
                            <select id="cbo-porcentaje" name="Po_Comision" class="form-control" style="width: 100%;"></select>
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <div class="col-xs-12 col-sm-12">
                        <div class="form-group">
                          <button type="button" id="btn-salir_adicionales" class="btn btn-primary btn-lg btn-block pull-center" data-dismiss="modal">Aceptar</button>
                        </div>
                      </div>
                    </div>
                  </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
              </div><!-- /. Modal Adicionales -->
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
            <?php echo form_close(); ?>
          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
        
        <?php
        $attributes = array('id' => 'form-generar_guia');
        echo form_open('', $attributes);
        ?>
        <!-- modal generar_guia -->
        <div class="modal fade modal-generar_guia" id="modal-default">
          <div class="modal-dialog modal-md">
            <div class="modal-content">
              <div class="modal-body">
                <input type="hidden" name="Hidden_ID_Empresa" class="form-control" value="">
                <input type="hidden" name="Hidden_ID_Organizacion" class="form-control" value="">
                <input type="hidden" name="Hidden_ID_Almacen" class="form-control" value="">
                <input type="hidden" name="Hidden_ID_Moneda" class="form-control" value="">
                <input type="hidden" name="Hidden_ID_Documento_Cabecera" class="form-control" value="">
                <input type="hidden" name="Hidden_ID_Entidad" class="form-control" value="">
                <input type="hidden" name="Hidden_ID_Lista_Precio_Cabecera" class="form-control" value="">
                <input type="hidden" name="Hidden_Fe_Emision" class="form-control" value="">
                <input type="hidden" name="Hidden_Fe_Emision_Hora" class="form-control" value="">
                <input type="hidden" name="Hidden_Ss_Total" class="form-control" value="">
                
                <div class="row">
                  <div class="col-xs-12 col-sm-12 col-md-12">
                    <h4 class="text-center" id="modal-header-generar_guia-title"></h4>
                    <h4 class="text-center" id="">Generar Guía / Salida de Inventario</h4>
                  </div>

                  <div class="col-xs-12 col-sm-12 col-md-12 hidden">
                    <label id="generar_guia-modal-body-cliente"></label>
                  </div>
                </div>

                <div class="row div-tipoguia">
                  <br>
                  <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 text-left hidden"><b>Documento:</b></div>

                  <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 text-center div-tipoguia" data-estado="14">
                    <label style="cursor: pointer;" class="div-tipoguia"><input type="radio" name="radio-TipoDocumento" id="radio-guia_i" class="flat-red div-tipoguia" value="14"> Interna</label>
                  </div>
                
                  <div class="col-xs-3 col-sm-4 col-md-4 col-lg-4 text-center div-tipoguia" data-estado="7" style="padding-left: 0px;padding-right: 0px;">
                    <label style="cursor: pointer;"><input type="radio" name="radio-TipoDocumento" id="radio-guia_f" class="flat-red" value="7"> Física</label>
                  </div>
                
                  <div class="col-xs-5 col-sm-4 col-md-4 col-lg-4 text-center div-tipoguia" data-estado="8" id="div-tipoguia_electronica">
                    <label style="cursor: pointer;"><input type="radio" name="radio-TipoDocumento" id="radio-guia_e" class="flat-red" value="8"> Electrónica</label>
                  </div>
                </div>
                
                <br>
                
                <div class="row hidden"><!-- Flete -->
                  <div class="col-xs-4 col-sm-4 col-md-2 col-lg-2 text-left">
                    <b>Flete</b>
                  </div>

                  <div class="col-xs-3 col-sm-4 col-md-2 col-lg-2 text-left div-flete" data-estado="1">
                    <label style="cursor: pointer;"><input type="radio" name="radio-addFlete" id="radio-flete_si" class="flat-red" value="1"> Si</label>
                  </div>
                
                  <div class="col-xs-5 col-sm-4 col-md-2 col-lg-2 text-left div-flete" data-estado="0">
                    <label style="cursor: pointer;"><input type="radio" name="radio-addFlete" id="radio-flete_no" class="flat-red" value="0"> No</label>
                  </div>
                </div>

                <div class="row" id="div-addFlete"><!-- Flete -->
                  <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 div-electronico">
                    <label>Transporte</label>
                    <div class="form-group">
                      <label style="cursor: pointer;"><input type="radio" name="radio-TipoTransporte" id="radio-tipo_transporte_publico" class="flat-red" value="01" checked> Público &nbsp;&nbsp;</label>
                      <label style="cursor: pointer;"><input type="radio" name="radio-TipoTransporte" id="radio-tipo_transporte_privado" class="flat-red" value="02"> Privado</label>
                    </div>
                  </div>

                  <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                    <label>Transportista</label><!--si transporte es privado es obligatorio-->
                    <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Para crear la opción es Personal > Maestro Delivery.">
                      <i class="fa fa-info-circle"></i>
                    </span>
                    <div class="form-group">
                      <select id="cbo-transporte" name="AID_Transportista" class="form-control select2" style="width: 100%;"></select>                        
                      <span class="help-block" id="error"></span>
                    </div>
                  </div>

                  <div class="col-xs-6 col-sm-3 col-md-3 col-lg-3">
                    <div class="form-group">
                      <label>F. Emisión</label>
                      <div class="input-group date" style="width: 100%;">
                        <input type="text" id="txt-Fe_Traslado" name="Fe_Traslado" class="form-control date-picker-invoice required" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                      </div>
                      <span class="help-block" id="error"></span>
                    </div>
                  </div>

                  <div class="col-xs-6 col-sm-3 col-md-3 col-lg-3">
                    <label>Placa</label><!--si transporte es privado es obligatorio-->
                    <span data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Obligatorio si es GUÍA ELECTRÓNICA y Transporte Privado">
                    <i class="fa fa-info-circle"></i>
                    </span>
                    <div class="form-group">
                      <input type="text" id="txt-No_Placa" name="No_Placa" placeholder="Opcional" class="form-control required input-Mayuscula input-codigo_barra" maxlength="6" autocomplete="off">
                      <span class="help-block" id="error"></span>
                    </div>
                  </div>

                  <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 div-electronico">
                    <label>Licencia</label><!--si transporte es privado es obligatorio-->
                    <div class="form-group">
                      <input type="text" id="txt-No_Licencia" inputmode="text" name="No_Licencia" placeholder="Opcional" class="form-control input-codigo_barra input-Mayuscula" minlength="9" maxlength="10" autocomplete="off">
                      <span class="help-block" id="error"></span>
                    </div>
                  </div>

                  <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 div-electronico">
                    <div class="form-group">
                      <label>Ubigeo</label>
                      <select id="cbo-ubigeo_inei-modal" name="ID_Ubigeo_Inei_Llegada" class="form-control select2" style="width: 100%;"></select>
                      <span class="help-block" id="error"></span>
                    </div>
                  </div>
                  
                  <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                    <label>Dirección</label>
                    <div class="form-group">
                      <input type="text" id="txt-Txt_Direccion_Entidad-modal" name="Txt_Direccion_Entidad-modal" placeholder="Obligatorio" class="form-control" autocomplete="off">
                      <span class="help-block" id="error"></span>
                    </div>
                  </div>

                  <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 div-datos_guia_electronica div-electronico">
                    <label>Peso Bruto</label>
                    <div class="form-group">
                      <input type="text" id="txt-Ss_Peso_Bruto" inputmode="decimal" name="Ss_Peso_Bruto" placeholder="Obligatorio" class="form-control input-decimal" maxlength="20" autocomplete="off">
                      <span class="help-block" id="error"></span>
                    </div>
                  </div>

                  <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 div-datos_guia_electronica div-electronico">
                    <label>Cantidad Bultos</label>
                    <div class="form-group">
                      <input type="text" id="txt-Nu_Bulto" inputmode="number" name="Nu_Bulto" placeholder="Opcional" class="form-control input-number" maxlength="12" autocomplete="off">
                      <span class="help-block" id="error"></span>
                    </div>
                  </div>
          
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="form-group">
                      <label>¿Descargar Stock?</label>
                      <select id="cbo-descargar_stock-modal" name="Nu_Descargar_Stock-modal" class="form-control required" style="width: 100%;">
                        <option value="1">Si</option>
                        <option value="0">No</option>
                      </select>
                      <span class="help-block" id="error"></span>
                      <span class="help-block" id="span-stock"></span>
                    </div>
                  </div>
                </div><!-- ./ Flete -->
              </div>

              <div class="modal-footer">
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                  <button type="button" id="btn-salir" class="btn btn-danger btn-md btn-block pull-center" data-dismiss="modal">Salir</button>
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                  <button type="button" id="btn-generar_guia" class="btn btn-primary btn-md btn-block pull-center">Generar Guía</button>
                </div>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>
        <!-- /. modal generar_guia -->
        <?php echo form_close(); ?>
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </section>
  <!-- /.content -->
  
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
              <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Para agregar Transporte o Delivery, ingresar a la opción Personal > Maestro Delivery">
                <i class="fa fa-info-circle"></i>
              </span>
              <div class="form-group">
                <select id="modal-cbo-transporte" name="ID_Transporte_Delivery" class="form-control select2" style="width: 100%;"></select>
              </div>
            </div>
            <div class="col-xs-12">
              <label>Dirección</label> (Opcional)
              <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Si ingresa la dirección, se mostrará impreso en el ticket. También se guardará y se mostrará para la próxima venta">
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
</div>
<!-- /.content-wrapper -->