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
              <br>
              <div class="col-xs-4 col-sm-3 col-md-3 col-lg-2">
                <div class="form-group">
                  <button type="button" id="btn-html_venta_punto_venta_actual" class="btn btn-success  btn-block btn-generar_venta_punto_venta btn-generar_venta_punto_venta-fecha" data-type="html" data-fecha="actual" data-tipo_recepcion="0" data-tipo_recepcion_actual="-"> Actual</button>
                </div>
              </div>
              <div class="col-xs-4 col-sm-3 col-md-3 col-lg-2">
                <div class="form-group">
                  <button type="button" id="btn-html_venta_punto_venta_hoy" class="btn btn-default  btn-block btn-generar_venta_punto_venta btn-generar_venta_punto_venta-fecha" data-type="html" data-fecha="hoy" data-tipo_recepcion="0" data-tipo_recepcion_actual="-"> Hoy</button>
                </div>
              </div>
              <div class="col-xs-4 col-sm-3 col-md-3 col-lg-2">
                <div class="form-group">
                  <button type="button" id="btn-html_venta_punto_venta_semana" class="btn btn-default  btn-block btn-generar_venta_punto_venta btn-generar_venta_punto_venta-fecha" data-type="html" data-fecha="semana" data-tipo_recepcion="0" data-tipo_recepcion_actual="-"> Semana</button>
                </div>
              </div>
              <div class="col-xs-12 col-sm-3 col-md-3 col-lg-2">
                <div class="form-group">
                  <button type="button" id="btn-html_venta_punto_venta_mes" class="btn btn-default  btn-block btn-generar_venta_punto_venta btn-generar_venta_punto_venta-fecha" data-type="html" data-fecha="mes" data-tipo_recepcion="0" data-tipo_recepcion_actual="-"> Mes</button>
                </div>
              </div>
            </div>

            <div class="row div-Filtros">              
              <div class="col-xs-6 col-sm-3 col-md-3 col-lg-2">
                <label style="font-size: 1.8rem;font-weight: normal;">Tipo Recepción</label>
              </div>
              <div class="col-xs-6 col-sm-9 col-md-2 col-lg-2">
                <select id="cbo-filtro_tipo_recepcion" class="form-control" style="width: 100%;">
                  <option value="0" selected="selected">Todos</option>
                  <option value="6">Delivery</option>
                  <option value="7">Recojo en Tienda</option>
                </select>
              </div>
            </div>

              <br>
            <div class="row div-Filtros div-delivery">
              <div class="col-xs-12 col-sm-3 col-md-3 col-lg-2">
                <label style="font-size: 1.8rem;font-weight: normal;">Delivery</label>
              </div>

              <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
                <div class="form-group">
                  <button type="button" id="btn-html_venta_punto_venta_dp" style="border-radius: 2rem;" class="btn btn-warning btn-xs btn-block btn-generar_venta_punto_venta btn-generar_venta_punto_venta_recepcion" data-type="html" data-fecha="actual" data-tipo_recepcion="-" data-tipo_recepcion_actual="0">Pendiente</button>
                </div>
              </div>
              
              <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
                <div class="form-group">
                  <button type="button" id="btn-html_venta_punto_venta_dpr" style="border-radius: 2rem;" class="btn btn-default btn-xs btn-block btn-generar_venta_punto_venta btn-generar_venta_punto_venta_recepcion" data-type="html" data-fecha="actual" data-tipo_recepcion="-" data-tipo_recepcion_actual="1">Preparando</button>
                </div>
              </div>
              
              <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
                <div class="form-group">
                  <button type="button" id="btn-html_venta_punto_venta_de" style="border-radius: 2rem;" class="btn btn-primary btn-xs btn-block btn-generar_venta_punto_venta btn-generar_venta_punto_venta_recepcion" data-type="html" data-fecha="actual" data-tipo_recepcion="-" data-tipo_recepcion_actual="2">Enviado</button>
                </div>
              </div>

              <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
                <div class="form-group">
                  <button type="button" id="btn-html_venta_punto_venta_en" style="border-radius: 2rem;" class="btn btn-success btn-success2 btn-xs btn-block btn-generar_venta_punto_venta btn-generar_venta_punto_venta_recepcion" data-type="html" data-fecha="actual" data-tipo_recepcion="-" data-tipo_recepcion_actual="3">Entregado</button>
                </div>
              </div>

              <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
                <div class="form-group">
                  <button type="button" id="btn-html_venta_punto_venta_dr" style="border-radius: 2rem;" class="btn btn-danger btn-xs btn-block btn-generar_venta_punto_venta btn-generar_venta_punto_venta_recepcion" data-type="html" data-fecha="actual" data-tipo_recepcion="-" data-tipo_recepcion_actual="4">Rechazado</button>
                </div>
              </div>
            </div>
            
            <div class="row div-Filtros div-recepcion">
              <div class="col-xs-12 col-sm-3 col-md-3 col-lg-2">
                <label style="font-size: 1.8rem;font-weight: normal;">Recojo en Tienda</label>
              </div>
              <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
                <div class="form-group">
                  <button type="button" id="btn-html_venta_punto_venta_rp" style="border-radius: 2rem;" class="btn btn-warning btn-xs btn-block btn-generar_venta_punto_venta btn-generar_venta_punto_venta_recepcion" data-type="html" data-fecha="actual" data-tipo_recepcion="-" data-tipo_recepcion_actual="0">Pendiente</button>
                </div>
              </div>
              <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
                <div class="form-group">
                  <button type="button" id="btn-html_venta_punto_venta_rpr" style="border-radius: 2rem;" class="btn btn-default btn-xs btn-block btn-generar_venta_punto_venta btn-generar_venta_punto_venta_recepcion" data-type="html" data-fecha="actual" data-tipo_recepcion="-" data-tipo_recepcion_actual="1">Preparando</button>
                </div>
              </div>
              <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
                <div class="form-group">
                  <button type="button" id="btn-html_venta_punto_venta_re" style="border-radius: 2rem;" class="btn btn-primary btn-xs btn-block btn-generar_venta_punto_venta btn-generar_venta_punto_venta_recepcion" data-type="html" data-fecha="actual" data-tipo_recepcion="-" data-tipo_recepcion_actual="2">Enviado</button>
                </div>
              </div>
              <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
                <div class="form-group">
                  <button type="button" id="btn-html_venta_punto_venta_ren" style="border-radius: 2rem;" class="btn btn-success btn-success2 btn-xs btn-block btn-generar_venta_punto_venta btn-generar_venta_punto_venta_recepcion" data-type="html" data-fecha="actual" data-tipo_recepcion="-" data-tipo_recepcion_actual="3">Entregado</button>
                </div>
              </div>
              <div class="col-xs-4 col-sm-3 col-md-2 col-lg-2">
                <div class="form-group">
                  <button type="button" id="btn-html_venta_punto_venta_rr" style="border-radius: 2rem;" class="btn btn-danger btn-xs btn-block btn-generar_venta_punto_venta btn-generar_venta_punto_venta_recepcion" data-type="html" data-fecha="actual" data-tipo_recepcion="-" data-tipo_recepcion_actual="4">Rechazado</button>
                </div>
              </div>
            </div>

            <div class="row div-Filtros hide">
              <br>

              <div class="col-xs-12 col-sm-12 col-md-2 hide">
                <div class="form-group">
                  <label>Fecha</label>
    		  				<select id="cbo-tipo_consulta_fecha" class="form-control" style="width: 100%;">
    		  				  <option value="0" selected="selected">Actual</option>
    		  				  <option value="1">Histórico</option>
    		  				</select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-3 col-md-2 div-fecha_historica hide">
                <div class="form-group">
                  <label>F. Inicio</label>
                  <div class="input-group date">
                    <input type="text" id="txt-Filtro_Fe_Inicio" class="form-control date-picker-report" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                  </div>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-6 col-sm-3 col-md-2 div-fecha_historica hide">
                <div class="form-group">
                  <label>F. Fin</label>
                  <div class="input-group date">
                    <input type="text" id="txt-Filtro_Fe_Fin" class="form-control date-picker-invoice txt-Filtro_Fe_Fin" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                  </div>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-12 col-sm-4 col-md-2 hide">
                <div class="form-group">
                  <label>Tipo</label>
                  <select id="cbo-filtros_tipos_documento" class="form-control" style="width: 100%;">
                    <option value="0" selected="selected">Todos</option>
                    <option value="4">B/Venta</option>
                    <option value="3">Factura</option>
                    <option value="2">Nota de Venta</option
                  ></select>
                </div>
              </div>
              
              <div class="col-xs-6 col-sm-4 col-md-2 hide">
                <div class="form-group">
                  <label>Serie</label>
    		  				<select id="cbo-filtros_series_documento" class="form-control" style="width: 100%;"></select>
                </div>
              </div>
              
              <div class="col-xs-6 col-sm-4 col-md-2 hide">
                <div class="form-group">
                  <label>Número</label>
                  <input type="tel" id="txt-Filtro_NumeroDocumento" class="form-control input-number" maxlength="20" placeholder="Buscar" value="" autocomplete="off">
                </div>
              </div>
              
              <div class="col-xs-12 col-sm-12 col-md-2 hide">
                <div class="form-group">
                  <label>Estado Documento</label>
    		  				<select id="cbo-estado_documento" class="form-control" style="width: 100%;">
    		  				  <option value="0" selected="selected">Todos</option>
    		  				  <option value="6">Completado</option>
    		  				  <option value="8">Completado Enviado</option>
    		  				  <option value="9">Completado Error</option>
    		  				  <option value="7">Anulado</option>
    		  				  <option value="10">Anulado Enviado</option>
    		  				  <option value="11">Anulado Error</option>
    		  				</select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-12 col-sm-12 col-md-2 hide">
                <div class="form-group">
                  <label>Estado Pago</label>
    		  				<select id="cbo-estado_pago" class="form-control" style="width: 100%;">
    		  				  <option value="0" selected="selected">Todos</option>
    		  				  <option value="1">Pendiente</option>
                    <option value="2">Cancelado</option>
    		  				</select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>

              <div class="col-xs-12 col-sm-12 col-md-6 hide">
                <div class="form-group">
                  <label>Cliente</label>
                  <input type="hidden" id="txt-AID" class="form-control">
                  <input type="text" id="txt-Filtro_Entidad" class="form-control autocompletar" data-global-class_method="AutocompleteController/getAllClient" data-global-table="entidad" placeholder="Ingresar Nombre / Nro. Documento de identidad" value="" autocomplete="off">
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              
              <div class="col-xs-12 col-sm-12 col-md-2 hide">
                <div class="form-group">
                  <label>Recepción</label>
    		  				<select id="cbo-tipo_recepcion_cliente" class="form-control">
    		  				  <option value="0" selected="selected">Todos</option>
                    <option value="5">Tienda</option>
                    <option value="6">Delivery</option>
                    <option value="7">Recojo en Tienda</option>
        				  </select>
                </div>
              </div>
            </div>
              
            <div class="row div-Filtros hide">
              <br>
              <div class="col-xs-4 col-md-3">
                <div class="form-group">
                  <button type="button" id="btn-html_venta_punto_venta" class="btn btn-default btn-block btn-generar_venta_punto_venta" data-type="html"><i class="fa fa-search"></i> Buscar</button>
                </div>
              </div>
              
              <div class="col-xs-4 col-md-3">
                <div class="form-group">
                  <button type="button" id="btn-pdf_venta_punto_venta" class="btn btn-default btn-block btn-generar_venta_punto_venta" data-type="pdf"><i class="fa fa-file-pdf-o color_icon_pdf"></i> PDF</button>
                </div>
              </div>
              
              <div class="col-xs-4 col-md-3">
                <div class="form-group">
                  <button type="button" id="btn-excel_venta_punto_venta" class="btn btn-default btn-block btn-generar_venta_punto_venta" data-type="excel"><i class="fa fa-file-excel-o color_icon_excel"></i> Excel</button>
                </div>
              </div>
              
              <div class="col-xs-12 col-md-3">
                <div class="form-group">
                  <button type="button" class="btn btn-success btn-block btn-save" onclick="cobroMasivoVenta();"><span class="fa fa-money"></span> Cobrar Masivo</button>
                </div>
              </div>
            </div>
          </div>

          <!-- /.box-header -->
          <div id="div-venta_punto_venta" class="table-responsive">
            <form id="form-cobro_masivo_venta" enctype="multipart/form-data" method="post" role="form" autocomplete="off">
              <table id="table-venta_punto_venta" class="table table-striped table-bordered">
                <thead>
                  <tr>
                    <th class="text-center">Recepción</th>
                    <th class="text-left">Despachador</th>
                    <!--<th class="text-center">F. Emisión</th>-->
                    <th class="text-center">F. Entrega</th>
                    <th class="text-center">Tiempo</th>
                    <!--<th class="text-center">Transcurrido</th> Aquí debería el delivery entrar y marcar su pedido completado o rechazado, con eso guardo el tiempo que transcurrio, que debería de contabilizarse desde que se envió el pedido-->
                    <th class="text-left">Documento</th>
                    <!--<th class="text-center">Tipo</th>
                    <th class="text-center">Serie</th>
                    <th class="text-center">Número</th>-->
                    <th class="text-left">Cliente</th>
                    <th class="text-center">Celular</th>
                    <th class="text-left">Dirección</th>
                    <th class="text-center">M</th>
                    <th class="text-center">Total</th>
                    <th class="text-center">Estado</th>
                    <!--<th class="text-center">Estado Documento</th>-->
                    <th class="text-center" title="Ver guias en Logistica > Guia / Salida de Inventario">Generar</th>
                    <th class="text-center"></th><!-- imprimir -->
                    <th class="text-center"></th><!-- ver -->
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </form>
          </div><!-- /. div-venta_punto_venta -->
          
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