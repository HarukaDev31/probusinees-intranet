<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">  
  <!-- Main content -->
  <section class="content">
    <!-- New box-header -->
    <div class="row">
      <?php //array_debug($this->empresa); ?>
      <!-- Valores para la compra rápida -->
      <input type="hidden" id="hidden-ID_Serie_Documento-Registro" class="form-control" value="<?php echo dateNow('serie_ymd'); ?>">
      <input type="hidden" id="hidden-ID_Numero_Documento-Registro" class="form-control" value="<?php echo dateNow('numero_ymdhms'); ?>">
      
      <?php if($arrDataProveedor['status']=="success"){ ?>
        <input type="hidden" id="hidden-ID_Entidad-Registro" class="form-control" value="<?php echo $arrDataProveedor['data']->ID_Entidad; ?>">
        <input type="hidden" id="hidden-Nu_Documento_Identidad-Registro" class="form-control" value="<?php echo $arrDataProveedor['data']->Nu_Documento_Identidad; ?>">
        <input type="hidden" id="hidden-No_Entidad-Registro" class="form-control" value="<?php echo $arrDataProveedor['data']->No_Entidad; ?>">
      <?php } else { ?>
        <input type="hidden" id="hidden-ID_Entidad-Registro" class="form-control" value="">
        <input type="hidden" id="hidden-Nu_Documento_Identidad-Registro" class="form-control" value="">
        <input type="hidden" id="hidden-No_Entidad-Registro" class="form-control" value="">
      <?php } ?>

      <div class="col-xs-12">
        <div class="div-content-header">
          <h3>
            <i class="<?php echo $this->MenuModel->verificarAccesoMenuCRUD()->Txt_Css_Icons; ?>" aria-hidden="true"></i> <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?>
            &nbsp;<a href="<?php echo $this->MenuModel->verificarAccesoMenuCRUD()->Txt_Url_Video; ?>" target="_blank"><i class="fa fa-youtube-play red" aria-hidden="true" title="Video <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?>" alt="Video <?php echo $this->MenuModel->verificarAccesoMenuCRUD()->No_Menu; ?>"></i> <span style="color: #7b7b7b; font-size: 14px;">ver video<span></a>
          </h3>
        </div>
      </div>
    </div>
    <!-- ./New box-header -->
    
    <div class="row">
      <div class="col-xs-12">
        <div class="box box-content">
          <!-- box-header -->
          <div class="box-header box-header-new div-Listar cortar-padding">
            <div class="row div-Filtros cortar-padding">
          	  <input type="hidden" id="hidden-sMethod" name="sMethod" class="form-control" value="<?php echo $this->router->method; ?>">
              <br>
              <div class="col-xs-4 col-sm-4 col-md-2">
                <label>Almacén</label>
                <div class="form-group">
                  <select id="cbo-filtro_almacen" name="ID_Almacen" class="form-control select2" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-4 col-sm-4 col-md-2">
                <label>F. Inicio</label>
                <div class="form-group">
                  <div class="input-group date">
                    <input type="text" id="txt-Filtro_Fe_Inicio" class="form-control date-picker-report_crud" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                  </div>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-4 col-sm-4 col-md-2">
                <label>F. Fin</label>
                <div class="form-group">
                  <div class="input-group date">
                    <input type="text" id="txt-Filtro_Fe_Fin" class="form-control date-picker-report_end txt-Filtro_Fe_Fin" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                  </div>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-4 col-sm-4 col-md-2">
                <label>Tipo</label>
                <div class="form-group">
    		  				<select id="cbo-Filtro_TiposDocumento" class="form-control"></select>
                </div>
              </div>
              
              <div class="col-xs-4 col-sm-4 col-md-2">
                <label>Serie</label>
                <div class="form-group">
                  <input type="text" id="txt-Filtro_SerieDocumento" class="form-control input-Mayuscula input-codigo_barra" maxlength="20" placeholder="Ingresar serie" value="" autocomplete="off">
                </div>
              </div>
              
              <div class="col-xs-4 col-sm-4 col-md-2">
                <label>Número</label>
                <div class="form-group">
                  <input type="tel" id="txt-Filtro_NumeroDocumento" class="form-control input-number" maxlength="20" placeholder="Ingresar número" value="" autocomplete="off">
                </div>
              </div>
              
              <div class="col-xs-6 col-sm-3 col-md-2">
                <label>Estado</label>
                <div class="form-group">
    		  				<select id="cbo-Filtro_Estado" class="form-control">
    		  				  <option value="" selected>Todos</option>
        				    <option value="6">Completado</option>
        				    <option value="7">Anulado</option>
        				  </select>
                </div>
              </div>
              
              <div class="col-xs-6 col-sm-3 col-md-2">
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

              <div class="col-xs-12 col-sm-6 col-md-4">
                <label>Proveedor</label>
                <div class="form-group">
                  <input type="hidden" id="txt-AID_Doble" name="AID" class="form-control">
				          <span class="clearable">
                    <input type="text" id="txt-Filtro_Entidad" class="form-control autocompletar clearable" data-global-class_method="AutocompleteController/getAllProvider" data-global-table="entidad" placeholder="Buscar por Nombre / Nro. Doc. de Identidad" value="" autocomplete="off">
                    <i class="clearable__clear">&times;</i>
                  </span>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-6 col-md-2">
                <label class="hidden-xs hidden-sm">&nbsp;</label>
                <div class="form-group">
                  <button type="button" id="btn-filter" class="btn btn-primary btn-block"><i class="fa fa-search"></i> Buscar</button>
                </div>
              </div>
              
              <div class="col-xs-6 col-md-2">
                <label class="hidden-xs hidden-sm">&nbsp;</label>
                <div class="form-group">
                  <?php if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Agregar == 1) : ?>
                    <button type="button" class="btn btn-success btn-block" onclick="agregarCompra()"><i class="fa fa-plus-circle"></i> Agregar</button>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="table-responsive div-Listar">
            <table id="table-Compra" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th class="no-sort">Almacén</th>
                  <th class="no-sort_left">F. Emisión</th>
                  <th class="no-sort">Tipo</th>
                  <th class="no-sort">Serie</th>
                  <th class="no-sort_right">Número</th>
                  <th class="no-hidden no-sort_left">Nro. Doc. Ident.</th>
                  <th class="no-sort_left">Proveedor</th>
                  <th class="no-sort">F. Pago</th>
                  <th class="no-sort">M</th>
                  <th class="no-hidden no-sort_right">Percepción</th>
                  <th class="no-hidden no-sort_right">SubTotal</th>
                  <th class="no-hidden no-sort_right">Impuesto</th>
                  <th class="no-sort_right">Total</th>
                  <th class="no-sort_right">Saldo</th>
                  <th class="no-hidden no-sort">Stock</th>
                  <th class="no-hidden no-sort_right">Observaciones</th>
                  <th class="no-hidden no-sort_right">F. Detraccion</th>
                  <th class="no-hidden no-sort_right">Num. Detraccion</th>
                  <th class="no-hidden no-sort">Pago</th>
                  <th class="no-sort">Estado</th>
                  <th class="no-hidden no-sort">Enlace</th>
                  <th class="no-hidden no-sort">Guía</th>
                  <th class="no-sort">Pagar</th>
                  <?php //if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Editar == 1) : ?>
                    <th class="no-sort">Editar</th>
                  <?php //endif; ?>
                  <?php //if ($this->MenuModel->verificarAccesoMenuCRUD()->Nu_Eliminar == 1) : ?>
                    <th class="no-sort">Anular</th>
                    <th class="no-sort">Eliminar</th>
                  <?php //endif; ?>
                </tr>
              </thead>
            </table>
          </div>
          <!-- /.box-body -->
          
          <?php
          $attributes = array('id' => 'form-cobrar_proveedor');
          echo form_open('', $attributes);
          ?>
          <!-- modal cobrar proveedor -->
          <div class="modal fade modal-cobrar_proveedor" id="modal-default">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h4 class="text-center" id="modal-header-cobrar_proveedor-title"></h4>
                </div>

                <div class="modal-body">
                  <input type="hidden" name="iIdDocumentoCabecera" class="form-control">
                  <input type="hidden" name="iEstadoLavadoRecepcionProveedor" class="form-control" value="2">
                  <input type="hidden" id="hidden-cobrar_proveedor-fsaldo" name="fSaldoProveedor" class="form-control" value="0">
                  
                  <div class="row">
                    <div class="col-sm-12">
                      <label id="cobrar_proveedor-modal-body-proveedor"></label>
                    </div>
                    <div class="col-sm-12">
                      <label id="cobrar_proveedor-modal-body-saldo_proveedor"></label>
                    </div>
                  </div>

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
                        <select id="cbo-cobrar_proveedor-modal_tarjeta_credito" name="iTipoMedioPago" class="form-control" style="width: 100%;"></select>
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>
                    
                    <div class="col-xs-4 col-sm-3">
                      <label>Importe</label>
                      <div class="form-group">
                        <input type="tel" class="form-control input-decimal" id="modal-tel-cobrar_proveedor-fPagoProveedor" name="fPagoProveedor" value="" autocomplete="off">
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
                        <input type="tel" id="modal-tel-nu_ultimo_4_digitos_tarjeta" name="iNumeroTarjeta" class="form-control input-number" minlength="4" maxlength="4" value="" placeholder="últimos 4 dígitos" autocomplete="off">
                        <span class="help-block" id="error"></span>
                      </div>
                    </div>
                  </div><!-- ./ row tarjeta de crédito -->
                </div>
                <div class="modal-footer">
                  <div class="col-xs-6">
                    <button type="button" id="btn-salir" class="btn btn-danger btn-md btn-block pull-center" data-dismiss="modal">Salir</button>
                  </div>
                  <div class="col-xs-6">
                    <button type="button" id="btn-cobrar_proveedor" class="btn btn-primary btn-md btn-block pull-center">Pagar</button>
                  </div>
                </div>
              </div>
              <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
          </div>
          <!-- /. modal cobrar proveedor -->
          <?php echo form_close(); ?>

          <div class="box-body div-AgregarEditar">
            <?php
            $attributes = array('id' => 'form-Compra');
            echo form_open('', $attributes);
            ?>
          	  <input type="hidden" id="txt-EID_Empresa" name="EID_Empresa" class="form-control">
          	  <input type="hidden" id="txt-EID_Documento_Cabecera" name="EID_Documento_Cabecera" class="form-control">
          	  <input type="hidden" id="txt-ID_Documento_Cabecera_Orden" name="ID_Documento_Cabecera_Orden" class="form-control">
			  
      			  <div class="row">
                <div class="col-xs-6 col-md-6 div-tipo_compras">
                  <div class="form-group">
                    <button type="button" id="btn-configurar_compra_normal" class="btn btn-dark color-white btn-block" data-mostrar_configurar_compra_normal="1">Compra normal</button>
                  </div>
                </div>

                <div class="col-xs-6 col-md-6 div-tipo_compras">
                  <div class="form-group">
                    <button type="button" id="btn-configurar_compra_rapida" class="btn btn-default btn-block" data-mostrar_configurar_compra_rapida="0">Compra rápida</button>
                  </div>
                </div>

                <?php if($arrDataProveedor['status']=="error"){ ?>
                  <div class="col-xs-12 col-sm-12 col-md-12 div-compra_rapida">
                    <div class="alert alert-danger alert-dismissible">
                      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                      <?php echo $arrDataProveedor['message']; ?>
                    </div>
                  </div>
                <?php } ?>

                <div class="col-xs-12 col-sm-12 col-md-6 cortar-padding">
        			    <div class="panel panel-default">
                    <div class="panel-heading"><i class="fa fa-book"></i> <b>Documento</b></div>
                    <div class="panel-body">
                      <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
                        <div class="form-group">
                          <label>Tipo <span class="label-advertencia">*</span></label>
                          <select id="cbo-TiposDocumento" class="form-control required" style="width: 100%;"></select>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                    
                      <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
                        <div class="form-group">
                          <label>Serie <span class="label-advertencia">*</span></label>
            		  				<input type="text" id="txt-ID_Serie_Documento" name="ID_Serie_Documento" class="form-control required input-Mayuscula input-codigo_barra" maxlength="20" autocomplete="off">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
                        <div class="form-group">
                          <label>Número <span class="label-advertencia">*</span></label>
                          <input type="tel" id="txt-ID_Numero_Documento" name="ID_Numero_Documento" class="form-control required input-number" maxlength="20" autocomplete="off">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
                        <div class="form-group">
                          <label>F. Emisión <span class="label-advertencia">*</span></label>
                          <div class="input-group date">
                            <input type="text" id="txt-Fe_Emision" name="Fe_Emision" class="form-control date-picker-invoice required" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                          </div>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
                        <div class="form-group">
                          <label>Forma Pago <span class="label-advertencia">*</span></label>
                          <select id="cbo-MediosPago" class="form-control required" style="width: 100%;"></select>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4 div-MediosPago">
                        <div class="form-group">
                          <label>F. Vencimiento <span class="label-advertencia">*</span></label>
                          <div class="input-group date">
                            <input type="text" id="txt-Fe_Vencimiento" class="form-control required" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                          </div>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-6 col-sm-4 col-md-3 col-lg-4 div-modal_datos_tarjeta_credito">
                        <label>Tipo</label>
                        <div class="form-group">
                          <select id="cbo-tarjeta_credito" class="form-control" style="width: 100%;"></select>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-6 col-sm-4 col-md-3 col-lg-4 div-modal_datos_tarjeta_credito">
                        <label>Opcional</label>
                        <div class="form-group">
                          <input type="tel" id="tel-nu_referencia" class="form-control input-number" value="" maxlength="10" placeholder="No. Operación" autocomplete="off">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>

                      <div class="col-xs-6 col-sm-4 col-md-3 col-lg-4 div-modal_datos_tarjeta_credito">
                        <label>Opcional</label>
                        <div class="form-group">
                          <input type="tel" id="tel-nu_ultimo_4_digitos_tarjeta" class="form-control input-number" minlength="4" maxlength="4" value="" placeholder="últimos 4 dígitos" autocomplete="off">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-12 col-sm-4 col-md-3 col-lg-3" style="display:none">
                        <div class="form-group rubro">
                          <label>Rubro <span class="label-advertencia">*</span></label>
                          <select id="cbo-Rubros" class="form-control required" style="width: 100%;"></select>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
                        <div class="form-group">
                          <label>Moneda <span class="label-advertencia">*</span></label>
                          <select id="cbo-Monedas" class="form-control required" style="width: 100%;"></select>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>

                      <div class="col-xs-6 col-sm-4 col-md-5 col-lg-4">
                        <div class="form-group">
                          <label>Otro Periodo</label>
                          <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Contabilidad la compra en otro periodo posterior para PLE">
                            <i class="fa fa-info-circle"></i>
                          </span>
                          <select id="cbo-Periodo" class="form-control required" style="width: 100%;"></select>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-12 col-sm-4 col-md-3 col-lg-4 div-Periodo">
                        <div class="form-group">
                          <label>F. Periodo <span class="label-advertencia">*</span></label>
                          <div class="input-group date">
                            <input type="text" id="txt-Fe_Periodo" class="form-control required" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                          </div>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>

                      <div class="col-xs-6 col-sm-3 col-md-4 col-lg-4">
                        <div class="form-group">
                          <label>¿Stock?</label>
                          <select id="cbo-descargar_stock" class="form-control required" style="width: 100%;"></select>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="col-xs-12 col-sm-12 col-md-6 cortar-padding">
        			    <div class="panel panel-default">
                    <div class="panel-heading"><i class="fa fa-user"></i> <b>Proveedor</b></div>
                    <div class="panel-body">
                      <div class="col-xs-6 text-center">
                        <label style="cursor: pointer;"><input type="radio" name="addProveedor" id="radio-proveedor_existente" class="flat-red" value="0"> Existente</label>
                      </div>
                      
                      <div class="col-xs-6 text-center">
                        <label style="cursor: pointer;"><input type="radio" name="addProveedor" id="radio-proveedor_nuevo" class="flat-red" value="1"> Nuevo</label>
                      </div>
                      
                      <div class="col-xs-12 col-md-12 div-proveedor_existente">
                        <label>Proveedor</label>
                        <div class="form-group">
                          <input type="hidden" id="txt-AID" name="AID" class="form-control required">
				                  <span class="clearable">
                            <input type="text" id="txt-ANombre" name="ANombre" class="form-control autocompletar clearable" data-global-class_method="AutocompleteController/getAllProvider" data-global-table="entidad" placeholder="Buscar por Nombre / Nro. Doc. de Identidad" value="" autocomplete="off">
                            <i class="clearable__clear">&times;</i>
                          </span>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>

                      <div class="col-xs-12 col-md-7 hidden">
                        <div class="form-group">
                          <label>Número Documento Identidad</label>
                          <input type="text" id="txt-ACodigo" name="ACodigo" class="form-control required" disabled>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                            
                      <div class="col-xs-12 col-md-12" style="display:none">
                        <div class="form-group">
                          <label>Dirección <span class="label-advertencia">*</span></label>
                          <input type="text" id="txt-Txt_Direccion_Entidad" name="Txt_Direccion_Entidad" class="form-control" disabled>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <!-- Proveedor Nuevo -->
                      <div class="col-xs-5 col-sm-3 col-md-4 col-lg-4 div-proveedor_nuevo">
                        <div class="form-group">
                          <label>T.D.I.</label>
                          <span style="cursor: pointer;" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Tipo de Documento de Identidad">
                            <i class="fa fa-info-circle"></i>
                          </span>
            		  				<select id="cbo-TiposDocumentoIdentidadProveedor" name="ID_Tipo_Documento_Identidad" class="form-control required" style="width: 100%;"></select>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-7 col-sm-6 col-md-5 col-lg-5 div-proveedor_nuevo">
                        <div class="form-group">
                          <label id="label-Nombre_Documento_Identidad_Proveedor">DNI</label></span>
                          <input type="tel" id="txt-Nu_Documento_Identidad_Proveedor" name="Nu_Documento_Identidad_Proveedor" class="form-control input-Mayuscula input-codigo_barra" placeholder="Ingresar número" value="" autocomplete="off" maxlength="8">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-center div-proveedor_nuevo">
                        <label>Api</label>
                        <div class="form-group">
                          <button type="button" id="btn-cloud-api_compra_proveedor" class="btn btn-success btn-block btn-md"><i class="fa fa-cloud-download fa-lg"></i></button>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
              
                      <div class="col-xs-9 col-sm-9 col-md-12 col-lg-10 div-proveedor_nuevo">
                        <div class="form-group">
                          <label id="label-No_Entidad_Proveedor">Nombre(s) y Apellidos</label><span class="label-advertencia"> *</span>
                          <input type="text" id="txt-No_Entidad_Proveedor" name="No_Entidad_Proveedor" class="form-control required" placeholder="Ingresar nombre" value="" autocomplete="off" maxlength="100">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-12 col-sm-3 col-md-3 col-lg-2 div-proveedor_nuevo">
                        <div class="form-group estado">
                          <label>Estado</label>
            		  				<select id="cbo-Estado" name="Nu_Estado" class="form-control required">
            		  				  <option value="1">Activo</option>
            		  				  <option value="0">Inactivo</option>
            		  				</select>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>

                      <div class="col-xs-12 col-md-9 div-proveedor_nuevo">
                        <div class="form-group">
                          <label>Dirección</label>
                          <input type="text" id="txt-Txt_Direccion_Entidad_Proveedor" name="Txt_Direccion_Entidad_Proveedor" class="form-control" autocomplete="off">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-12 col-sm-3 col-md-4" style="display:none">
                        <div class="form-group">
                          <label>Telefono</label>
                          <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-phone" aria-hidden="true"></i></span>
                            <input type="tel" id="txt-Nu_Telefono_Entidad_Proveedor" name="Nu_Telefono_Entidad_Proveedor" class="form-control" data-inputmask="'mask': ['999 9999']" data-mask autocomplete="off">
                          </div>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-12 col-sm-4 col-md-5" style="display:none">
                        <div class="form-group">
                          <label>Celular</label>
                          <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-phone" aria-hidden="true"></i></span>
                            <input type="tel" id="txt-Nu_Celular_Entidad_Proveedor"  name="Nu_Celular_Entidad_Proveedor" class="form-control" data-inputmask="'mask': ['999 999 999']" data-mask autocomplete="off">
                          </div>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      <!-- /. Proveedor Nuevo -->
                    </div>
                  </div>
                </div>
                
                <div class="col-xs-12 col-sm-12 col-md-6 cortar-padding">
                  <div class="panel panel-default">
                    <div class="panel-heading">
                      <b>Agregar Detracción </b>&nbsp;
                        <label class="cursor_style_1" style="cursor: pointer;">
                          <input type="radio" style="cursor: pointer;" name="radio-addDetraccion" id="radio-InactiveDetraccion" onclick="addDetraccion(this.value);" value="0"> No
                        </label> 
                        <label class="cursor_style_1" style="cursor: pointer;">
                          <input type="radio" style="cursor: pointer;" name="radio-addDetraccion" id="radio-ActiveDetraccion" onclick="addDetraccion(this.value);" value="1"> Si
                        </label>
                    </div>
                    <div class="panel-body" id="div-addDetraccion">
                      <div class="col-xs-6 col-sm-4 col-md-4">
                        <div class="form-group">
                          <label>F. Detracción</label>
                          <div class="input-group date">
                            <input type="text" name="Fe_Detraccion" class="form-control date-picker-invoice required" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                          </div>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-6 col-sm-6 col-md-6">
                        <div class="form-group">
                          <label>Número</label>
                          <div class="input-group">
                            <input type="tel" id="txt-Nu_Detraccion" name="Nu_Detraccion" class="form-control required input-number" maxlength="10" autocomplete="off">
                          </div>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div><!-- ./Cabecera -->
              
      			  <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-6 cortar-padding">
        			    <div class="panel panel-default div-DocumentoModificar">
                    <div class="panel-heading"><i class="fa fa-book"></i> <b>Documento a Modificar</b></div>
                    <div class="panel-body">
                      <div class="col-xs-4 col-sm-4 col-md-4">
                        <div class="form-group">
                          <input type="hidden" id="txt-ID_Documento_Guardado">
                          <label>Tipo <span class="label-advertencia">*</span></label>
            		  				<select id="cbo-TiposDocumentoModificar" class="form-control" style="width: 100%;"></select>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                    
                      <div class="col-xs-4 col-sm-4 col-md-4">
                        <div class="form-group">
                          <label>Series <span class="label-advertencia">*</span></label>
            		  				<input type="text" id="txt-ID_Serie_Documento_Modificar" class="form-control input-Mayuscula input-codigo_barra" maxlength="20" autocomplete="off">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-4 col-sm-4 col-md-4">
                        <div class="form-group">
                          <label>Número <span class="label-advertencia">*</span></label>
                          <div class="input-group">
                            <input type="tel" id="txt-ID_Numero_Documento_Modificar" class="form-control input-number" maxlength="20" autocomplete="off">
                          </div>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                        <p class="div-mensaje_verificarExisteDocumento"></p>
                      </div>
                      
                      <div class="col-xs-12 col-md-12">
                        <div class="form-group">
                          <button type="button" id="btn-verificarExisteDocumento" class="btn btn-success btn-md btn-block">Verificar <i class="fa fa-check"></i></button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

      			  <div class="row">
                <div class="col-xs-12 col-md-12 cortar-padding">
        			    <div id="panel-DetalleProductos" class="panel panel-default">
                    <div class="panel-heading"><i class="fa fa-shopping-cart"></i> <b>Detalle</b></div>
                    <div class="panel-body" style="padding-top: 0px;">
                      <div class="sticky">
      			          <div class="row">
          	            <input type="hidden" name="Nu_Tipo_Lista_Precio" value="2" class="form-control"><!-- 2 = Compra -->
                        <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2">
                          <label>L. Precio</label>
                          <div class="form-group">
                            <select id="cbo-lista_precios" class="form-control required" style="width: 100%;"></select>
                            <span class="help-block" id="error"></span>
                          </div>
                        </div>
                        
                        <div class="col-xs-12 col-sm-7 col-md-7 col-lg-7">
                          <div class="form-group">
                            <label>Producto / Servicio <span class="label-advertencia">*</span></label>
                            <input type="hidden" id="txt-Nu_Tipo_Registro" class="form-control" value="0"><!-- Compra -->
                            <input type="hidden" id="txt-Nu_Compuesto" class="form-control" value="0">
                            <input type="hidden" id="txt-ID_Producto" class="form-control">
                            <input type="hidden" id="txt-Nu_Codigo_Barra" class="form-control">
                            <input type="hidden" id="txt-Ss_Precio" class="form-control">
                            <input type="hidden" id="txt-ID_Impuesto_Cruce_Documento" class="form-control">
                            <input type="hidden" id="txt-Nu_Tipo_Impuesto" class="form-control">
                            <input type="hidden" id="txt-Ss_Impuesto" class="form-control">
                            <input type="hidden" id="txt-No_Unidad_Medida" class="form-control">
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
                        
                        <div class="col-xs-12 col-sm-2 col-md-3 col-lg-3">
                          <div class="form-group">
                            <label class="hidden-xs">&nbsp;</label>
                            <button type="button" id="btn-addProductoCompra" class="btn btn-success btn-md btn-block">Agregar</button>
                          </div>
                        </div>
                      </div>
                      </div>
                      
      			          <div class="row">
                      <div class="col-md-12 delete-position">
                      <div class="table-responsive">
                        <table id="table-DetalleProductos" class="table table-striped table-bordered">
                          <thead>
                            <tr>
                              <th style="display:none;" class="text-left"></th>
                              <th class="text-center" style="width: 7%;">Cantidad</th>
                              <th class="text-center" style="width: 20%;">Item</th>
                              <th class="text-center" style="width: 8%;">Precio</th>
                              <th class="text-center">Impuesto</th>
                              <th class="text-center" style="display:none;">Sub Total</th>
                              <th class="text-center" style="width: 8%;">% Dscto</th>
                              <th class="text-center" style="width: 12%;">Total</th>
                              <th class="text-center">Nro. Lote</th>
                              <th class="text-center" style="width: 12%;">F. Vcto. Lote</th>
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
              
      			  <div class="row div-Glosa">
      			    <div class="col-xs-12 col-md-12 cortar-padding">
      			      <div class="panel panel-default">
                    <div class="panel-heading"><i class="fa fa-comment-o"></i> <b>Observaciones</b> (opcional) <button type="button" id="btn-adicionales_ov_garantia_glosa" class="btn btn-link"  data-ver_adicionales_ov_garantia_glosa="0">Agregar</button></div>
                    <div class="panel-body div-adicionales_ov_garantia_glosa">
                      <div class="col-xs-12 col-md-12">
                        <textarea name="Txt_Glosa" class="form-control"></textarea>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
                
      			  <div class="row"><!-- Totales -->
                <div class="col-xs-12 col-md-12 div-total cortar-padding">
      			      <div class="panel panel-default">
                  <div class="panel-heading text-right">
                    TOTAL <b class="hidden">CANTIDAD: </b> <span id="span-total_cantidad" class="hidden">0</span> IMPORTE: <span class="span-signo" style="font-size: 20px;font-weight: bold;"></span> <span id="span-total_importe" style="font-size: 20px;font-weight: bold;">0</span><button type="button" id="btn-ver_total_todo" class="btn btn-link" data-ver_total_todo="0">VER / DESCUENTO</button>
                  </div>
                  <div class="panel-body panel_body_total_todo">
                    <div class="table-responsive">
                    <table class="table" id="table-CompraTotal">
                      <tr>
                        <td class="text-center"><label>% Descuento</label></td>
                        <td class="text-right hidden-xs hidden-sm"><label>Inafectas</label></td>
                        <td class="text-right hidden-xs hidden-sm"><label>Exoneradas</label></td>
                        <td class="text-right hidden-xs hidden-sm"><label>Gratuitas</label></td>
                        <td class="text-right hidden-xs hidden-sm"><label>Gravadas</label></td>
                        <td class="text-right"><label>Dscto. Total (-)</label></td>
                        <td class="text-right hidden-xs hidden-sm"><label>I.G.V.</label></td>
                        <td class="text-right hidden-xs hidden-sm"><label>Percepción</label></td>
                        <td class="text-right"><label>Total</label></td>
                      </tr>

                      <tr>
                        <td class="text-right">
    	  					        <input type="text" class="form-control input-decimal" inputmode="decimal" id="txt-Ss_Descuento" name="Ss_Descuento" size="3" value="" autocomplete="off" />
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
                          <input type="hidden" class="form-control" id="txt-descuento" value="0.00"/>
                          <span class="span-signo"></span> <span id="span-descuento">0.00</span>
                        </td>
                        
                        <td class="text-right hidden-xs hidden-sm">
                            <input type="hidden" class="form-control" id="txt-impuesto" value="0.00"/>
                            <span class="span-signo"></span> <span id="span-impuesto">0.00</span>
                        </td>
                        
                        <td class="text-right hidden-xs hidden-sm">
    	  					        <input type="text" class="form-control input-decimal" inputmode="decimal" id="txt-Ss_Percepcion" name="Ss_Percepcion" size="3" value="" autocomplete="off" />
                        </td>
                        
                        <td class="text-right">
                          <input type="hidden" class="form-control" id="txt-total" value="0.00"/>
                          <span class="span-signo"></span> <span id="span-total">0.00</span>
                        </td>
                      </tr>
                    </table><!-- ./Totales -->
                    </div>
                  </div>
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
                    <button type="button" id="btn-save" class="btn btn-success btn-lg btn-block btn-verificar">Guardar</button>
                  </div>
                </div>
              </div>
            <?php echo form_close(); ?>
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