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
    
    <!-- Row Lista de POS -->
    <div class="row">
      <div class="col-xs-12">
        <div class="box box-content">
          <!-- box-header -->
          <div class="box-header">
            <div class="row">
              <?php
              $fTotalaLiquidar = 0.00;
              $sClassOcultarCajaCiega = ((($this->empresa->ID_Empresa==73 && $this->MenuModel->verificarAccesoMenuCRUD()->Nu_Editar == 1) || $this->empresa->ID_Empresa!=73) ? '' : 'hidden');
              if ( (empty($arrValidacionCajaCerrada) || (isset($arrValidacionCajaCerrada['result']) && $arrValidacionCajaCerrada['result']->Nu_Tipo == '3')) && isset($this->session->userdata['arrDataPersonal']) && $this->session->userdata['arrDataPersonal']['sStatus']=='success' ) { ?>
                <div class="col-xs-12 col-sm-7 <?php echo $sClassOcultarCajaCiega; ?>">
                  <!-- PANEL Liquidacion de caja -->
                  <div class="panel panel-default">
                    <div class="panel-heading"><i class="fa fa-balance-scale"></i> <b>Liquidación de Caja</b></div>
                    <!-- Panel body -->
                    <div class="panel-body">
                      <!-- div ventas por categoria -->
                      <div class="col-sm-12">
                        <div class="table-responsive">
                          <table id="table-ventas_por_categoria" class="table table-striped">
                            <thead>
                              <tr>
                                <th class="text-center" colspan="4">Ventas por <?php echo $this->empresa->Nu_Imprimir_Liquidacion_Caja == 1 ? 'Categoría' : 'Producto'; ?></th>
                              </tr>
                              <tr>
                                <th class="text-center"><?php echo $this->empresa->Nu_Imprimir_Liquidacion_Caja == 1 ? 'Categoría' : 'Producto'; ?></th>
                                <th class="text-right">Cantidad</th>
                                <th class="text-right">M</th>
                                <th class="text-right">Total</th>
                              </tr>
                            </thead>
                            <tbody>
                            <?php
                              if ( $arrModalVentasMultiples['sStatus']=='success' ) {
                                $fCantidadTotal = 0.00;
                                $fTotal = 0.00;                                
                                foreach( $arrModalVentasMultiples['arrData']['VentasxFamilia'] as $row ){
                            ?>
                              <tr>
                                <td class="text-left"><?php echo $row->No_Familia_Item; ?></td>
                                <td class="text-right"><?php echo numberFormat($row->Qt_Producto, 2, '.', ','); ?></td>
                                <td class="text-right"><?php echo $row->No_Signo; ?></td>
                                <td class="text-right"><?php echo numberFormat($row->Ss_Total, 2, '.', ','); ?></td>
                              </tr>
                            <?php
                                  $fCantidadTotal += $row->Qt_Producto;
                                  $fTotal += $row->Ss_Total;
                                } // ./ foreach
                            ?>
                            </tbody>
                            <tfoot>
                              <tr>
                                <th class="text-right">Total</th>
                                <th class="text-right"><?php echo numberFormat($fCantidadTotal, 2, '.', ','); ?></th>                                
                                <th class="text-right" colspan="2"><?php echo numberFormat($fTotal, 2, '.', ','); ?></th>
                              </tr>
                            </tfoot>
                            <?php
                              } else {
                            ?>
                              <tr>
                                <td class="text-center" colspan="4"><?php $arrModalVentasMultiples['sMessage']; ?></td>
                              </tr>
                            <?php
                              }// ./ if - else
                            ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                      <!-- ./ div ventas por categoria -->
                      
                      <!-- div ventas por categoria NOTA DE CREDITO -->
                      <div class="col-sm-12">
                        <div class="table-responsive">
                          <table id="table-ventas_por_categoria" class="table table-striped">
                            <thead>
                              <tr>
                                <th class="text-center" colspan="4">Ventas por <?php echo $this->empresa->Nu_Imprimir_Liquidacion_Caja == 1 ? 'Categoría' : 'Producto'; ?> - Nota Crédito</th>
                              </tr>
                              <tr>
                                <th class="text-center"><?php echo $this->empresa->Nu_Imprimir_Liquidacion_Caja == 1 ? 'Categoría' : 'Producto'; ?></th>
                                <th class="text-right">Cantidad</th>
                                <th class="text-right">M</th>
                                <th class="text-right">Total</th>
                              </tr>
                            </thead>
                            <tbody>
                            <?php
                              if ( $arrModalVentasMultiples['sStatus']=='success' ) {
                                $fCantidadTotal = 0.00;
                                $fTotal = 0.00;                                
                                foreach( $arrModalVentasMultiples['arrData']['VentasxFamiliaNotaCredito'] as $row ){
                            ?>
                              <tr>
                                <td class="text-left"><?php echo $row->No_Familia_Item; ?></td>
                                <td class="text-right"><?php echo numberFormat($row->Qt_Producto, 2, '.', ','); ?></td>
                                <td class="text-right"><?php echo $row->No_Signo; ?></td>
                                <td class="text-right"><?php echo numberFormat($row->Ss_Total, 2, '.', ','); ?></td>
                              </tr>
                            <?php
                                  $fCantidadTotal += $row->Qt_Producto;
                                  $fTotal += $row->Ss_Total;
                                } // ./ foreach
                            ?>
                            </tbody>
                            <tfoot>
                              <tr>
                                <th class="text-right">Total</th>
                                <th class="text-right"><?php echo numberFormat($fCantidadTotal, 2, '.', ','); ?></th>                                
                                <th class="text-right" colspan="2"><?php echo numberFormat($fTotal, 2, '.', ','); ?></th>
                              </tr>
                            </tfoot>
                            <?php
                              } else {
                            ?>
                              <tr>
                                <td class="text-center" colspan="4"><?php $arrModalVentasMultiples['sMessage']; ?></td>
                              </tr>
                            <?php
                              }// ./ if - else
                            ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                      <!-- ./ div ventas por categoria NOTA DE CREDITO -->
                      
                      <!-- div Movimientos de caja -->
                      <div class="col-sm-12">
                        <div class="table-responsive">
                          <table id="table-movimientos_caja" class="table table-striped">
                            <thead>
                              <tr>
                                <th class="text-center" colspan="3">Movimientos de Caja</th>
                              </tr>
                              <tr>
                                <th class="text-center">Movimiento</th>
                                <th class="text-center">M</th>
                                <th class="text-right">Total</th>
                              </tr>
                            </thead>
                            <tbody>
                            <?php
                              if ( $arrModalVentasMultiples['sStatus']=='success' ) {
                                $fTotal = 0.00;
                                foreach( $arrModalVentasMultiples['arrData']['MovimientosCaja'] as $row ){
                                  $fTotalaLiquidar += $row->Nu_Tipo != '6' ? $row->Ss_Total : -$row->Ss_Total;
                            ?>
                              <tr>
                                <td class="text-left"><?php echo $row->No_Tipo_Operacion_Caja; ?></td>
                                <td class="text-center"><?php echo $row->No_Signo; ?></td>
                                <td class="text-right"><?php echo numberFormat($row->Ss_Total, 2, '.', ','); ?> (<?php echo $row->Nu_Tipo != '6' ? '+' : '-' ; ?>)</td>
                              </tr>
                            <?php
                                  $fTotal += $row->Nu_Tipo != '6' ? $row->Ss_Total : -$row->Ss_Total;
                                } // ./ foreach
                            ?>
                            </tbody>
                            <tfoot>
                              <tr>
                                <th class="text-right" colspan="2">Total</th>
                                <th class="text-right"><?php echo numberFormat($fTotal, 2, '.', ','); ?></th>
                              </tr>
                            </tfoot>
                            <?php
                              } else {
                            ?>
                              <tr>
                                <td class="text-center" colspan="3"><?php $arrModalVentasMultiples['sMessage']; ?></td>
                              </tr>
                            <?php
                              }// ./ if - else
                            ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                      <!-- ./ div Movimientos de caja -->
                      
                      <!-- div Ventas generales -->
                      <div class="col-sm-12">
                        <div class="table-responsive">
                          <table id="table-ventas_generales" class="table table-striped">
                            <thead>
                              <tr>
                                <th class="text-center" colspan="3">Ventas x Formas de Pago</th>
                              </tr>
                              <tr>
                                <th class="text-center">Tipo</th>
                                <th class="text-center">M</th>
                                <th class="text-right">Total</th>
                              </tr>
                            </thead>
                            <tbody>
                            <?php
                            if ( $arrModalVentasMultiples['sStatus']=='success' ) {
                              $fTotal = 0.00;
                              foreach( $arrModalVentasMultiples['arrData']['VentasGenerales'] as $row ){
                                if ( $row->Ss_Total > 0.00 ) {//Agregado 20/11/2020
                                  $fTotalaLiquidar += $row->Nu_Tipo_Caja == '0' ? $row->Ss_Total : 0.00; ?>
                                  <tr>
                                    <td class="text-left"><?php echo $row->No_Medio_Pago; ?></td>
                                    <td class="text-center"><?php echo $row->No_Signo; ?></td>
                                    <td class="text-right"><?php echo numberFormat($row->Ss_Total, 2, '.', ','); ?> <?php echo $row->Nu_Tipo_Caja == '0' ? '(+)' : '' ; ?></td>
                                  </tr>
                                  <?php
                                  $fTotal += $row->Ss_Total;
                                }
                              } // ./ foreach
                            ?>
                              <tr>
                                <th class="text-right" colspan="2">Total</th>
                                <th class="text-right"><?php echo numberFormat($fTotal, 2, '.', ','); ?></th>
                              </tr>
                            </tbody>
                            <!-- Solo ventas al crédito -->
                            <?php
                            //if ( $this->empresa->Nu_Tipo_Rubro_Empresa != 3 ) { ?>
                              <thead>
                                <tr>
                                  <th class="text-center" colspan="3">Ventas al Crédito</th>
                                </tr>
                                <tr>
                                  <th class="text-center">Tipo</th>
                                  <th class="text-center">M</th>
                                  <th class="text-right">Total</th>
                                </tr>
                              </thead>
                                <?php
                                if ( !empty($arrModalVentasMultiples['arrData']['VentasGeneralesCreditoCreditoNoSuma']) ) {?>
                                  <tbody>
                                  <?php
                                  $fTotalCredito = 0.00;
                                  foreach( $arrModalVentasMultiples['arrData']['VentasGeneralesCreditoCreditoNoSuma'] as $row ){ ?>
                                    <tr>
                                      <td class="text-left"><?php echo $row->No_Medio_Pago; ?></td>
                                      <td class="text-center"><?php echo $row->No_Signo; ?></td>
                                      <td class="text-right"><?php echo numberFormat($row->Ss_Total, 2, '.', ','); ?></td>
                                    </tr>
                                    <?php
                                    $fTotalCredito += $row->Ss_Total;
                                  } // ./ foreach ?>
                                    <tr>
                                      <th class="text-right" colspan="2">Total</th>
                                      <th class="text-right"><?php echo numberFormat($fTotalCredito, 2, '.', ','); ?></th>
                                    </tr>
                                  </tbody>
                                <?php
                                } else {
                                ?>
                                  <tr>
                                    <td class="text-center" colspan="3">No hay datos</td>
                                  </tr>
                                <?php
                                }
                             //} // ./ if rubro ?>
                            <!-- ./ Solo ventas al crédito -->
                            <?php
                              } else {
                            ?>
                              <tr>
                                <td class="text-center" colspan="3"><?php $arrModalVentasMultiples['sMessage']; ?></td>
                              </tr>
                            <?php
                              }// ./ if - else
                            ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                      <!-- ./ div Ventas generales -->
                      
                      <!-- div ventas por descuento -->
                      <div class="col-sm-12">
                        <div class="table-responsive">
                          <table id="table-ventas_por_categoria" class="table table-striped">
                            <thead>
                              <tr>
                                <th class="text-center" colspan="3">Ventas por Descuento</th>
                              </tr>
                              <tr>
                                <th class="text-left">Nombre</th>
                                <th class="text-right">M</th>
                                <th class="text-right">Total</th>
                              </tr>
                            </thead>
                            <tbody>
                            <?php
                              if ( $arrModalVentasMultiples['sStatus']=='success' ) {
                                $fCantidadTotal = 0.00;
                                $fTotal = 0.00;                                
                                foreach( $arrModalVentasMultiples['arrData']['VentasxDescuento'] as $row ){
                            ?>
                              <tr>
                                <td class="text-left">Descuento</td>
                                <td class="text-right"><?php echo $row->No_Signo; ?></td>
                                <td class="text-right"><?php echo numberFormat($row->Ss_Total, 2, '.', ','); ?></td>
                              </tr>
                            <?php
                                  $fTotal += $row->Ss_Total;
                                } // ./ foreach
                            ?>
                            </tbody>
                            <tfoot>
                              <tr>
                                <th class="text-right" colspan="2">Total</th>                                
                                <th class="text-right"><?php echo numberFormat($fTotal, 2, '.', ','); ?></th>
                              </tr>
                            </tfoot>
                            <?php
                              } else {
                            ?>
                              <tr>
                                <td class="text-center" colspan="3"><?php $arrModalVentasMultiples['sMessage']; ?></td>
                              </tr>
                            <?php
                              }// ./ if - else
                            ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                      <!-- ./ div ventas por descuento -->
                      
                      <!-- div ventas por GRATUITAS -->
                      <div class="col-sm-12">
                        <div class="table-responsive">
                          <table id="table-ventas_por_categoria" class="table table-striped">
                            <thead>
                              <tr>
                                <th class="text-center" colspan="3">Ventas por GRATUITAS o REGALOS</th>
                              </tr>
                              <tr>
                                <th class="text-left">Nombre</th>
                                <th class="text-right">M</th>
                                <th class="text-right">Total</th>
                              </tr>
                            </thead>
                            <tbody>
                            <?php
                              if ( $arrModalVentasMultiples['sStatus']=='success' ) {
                                $fCantidadTotal = 0.00;
                                $fTotal = 0.00;                                
                                foreach( $arrModalVentasMultiples['arrData']['VentasxRegaloGratuita'] as $row ){
                            ?>
                              <tr>
                                <td class="text-left">Gratuita</td>
                                <td class="text-right"><?php echo $row->No_Signo; ?></td>
                                <td class="text-right"><?php echo numberFormat($row->Ss_Total, 2, '.', ','); ?></td>
                              </tr>
                            <?php
                                  $fTotal += $row->Ss_Total;
                                } // ./ foreach
                            ?>
                            </tbody>
                            <tfoot>
                              <tr>
                                <th class="text-right" colspan="2">Total</th>                                
                                <th class="text-right"><?php echo numberFormat($fTotal, 2, '.', ','); ?></th>
                              </tr>
                            </tfoot>
                            <?php
                              } else {
                            ?>
                              <tr>
                                <td class="text-center" colspan="3"><?php $arrModalVentasMultiples['sMessage']; ?></td>
                              </tr>
                            <?php
                              }// ./ if - else
                            ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                      <!-- ./ div ventas por GRATUITAS -->
                    </div>
                  <!-- /. Panel body -->
                  </div>
                  <!-- /. PANEL Liquidacion de caja -->
                </div>

                <!-- DIV col-sm-6 -->
                <div class="col-xs-12 col-sm-5">
                  <!-- PANEL Cierre de caja -->
                  <div class="panel panel-default">
                    <div class="panel-heading"><i class="fa fa-keyboard-o"></i> <b>Cierre de Caja - Dinero Efectivo</b></div>
                    <!-- Panel body -->
                    <div class="panel-body">
                      <input type="hidden" id="hidden-id_tipo_operacion_caja_apertura" value=""><!-- Apertura de caja -->
                      <input type="hidden" id="hidden-id_tipo_operacion_caja" value=""><!-- Cierre de caja -->
                      <!-- Fila -->
                      <div class="col-xs-6" style="display: none">
                        <label>Cajero(a)</label>
                        <div class="form-group">
                          <input type="hidden" id="hidden-id_matricula_personal" value="<?php echo $this->session->userdata['arrDataPersonal']['arrData'][0]->ID_Matricula_Empleado; ?>">
                          <label style="font-weight: normal"><?php echo $this->session->userdata['arrDataPersonal']['arrData'][0]->No_Entidad; ?></label>
                        </div>
                      </div>

                      <div class="col-xs-12">
                        <div class="form-group">
                          <label>Moneda</label>
                          <div class="form-group">
                            <select id="cbo-moneda" class="form-control required">
                              <option value="1" data-no_signo="<?php echo $this->session->userdata['arrDataPersonal']['arrData']['No_Signo']; ?>">Soles</option>
                            </select>
                          </div>
                        </div>
                      </div>
                      <!-- /.Fila -->                      
                      <!-- Fila 2 -->
                      <div class="col-xs-6 col-sm-6 col-md-4 <?php echo $sClassOcultarCajaCiega; ?>">
                        <label>Liquidar</label>
                        <div class="form-group">
                          <input type="tel" id="txt-ss_total_liquidar_referencial" class="form-control required input-decimal" maxlength="15" autocomplete="off" disabled="" value="<?php echo $fTotalaLiquidar; ?>">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-6 col-sm-6 col-md-4">
                        <label>Depositar</label>
                        <div class="form-group">
                          <input type="text" inputmode="decimal" id="txt-ss_total_depositado" class="form-control required input-decimal hotkey-save_cierre_caja" maxlength="15" autocomplete="off" value="<?php echo (empty($sClassOcultarCajaCiega) ? $fTotalaLiquidar : ''); ?>">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      
                      <div class="col-xs-12 col-sm-12 col-md-4 <?php echo $sClassOcultarCajaCiega; ?>">
                        <label>Diferencia <span class="span-signo"><?php echo $this->session->userdata['arrDataPersonal']['arrData']['No_Signo']; ?></span></label>
                        <div class="form-group">
                          <input type="tel" id="txt-ss_total_diferencia" class="form-control required input-decimal" maxlength="15" autocomplete="off" disabled="">
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      <!-- /. Fila 2 -->
                      <!-- Fila 3 -->
                      <div class="col-xs-12 col-sm-12">
                        <label>Nota</label>
                        <div class="form-group">
                          <textarea name="area-txt_cierre_caja" class="form-control" placeholder="opcional"></textarea>
                          <span class="help-block" id="error"></span>
                        </div>
                      </div>
                      <!-- /.Fila 3 -->
                      <!-- Fila 4 -->
                      <div class="col-xs-12 col-sm-12">
                        <div class="form-group">
                          <button type="button" id="btn-save_cierre_caja" class="btn btn-danger btn-lg btn-block">Cerrar Caja</button>
                        </div>
                      </div>
                      <!-- /.Fila 4 -->
                    </div>
                    <!-- /.Panel body -->
                  </div>
                  <input type="hidden" id="hidden-ss_total_liquidar" class="form-control required input-decimal" maxlength="15" autocomplete="off" disabled="" value="<?php echo $fTotalaLiquidar; ?>">
                  <!-- PANEL Cierre de caja -->
                </div>
                <!-- DIV col-sm-6 -->
              <?php } else { ?>
                <div class="col-xs-12">
                  <h3 style="margin-top: 0px;"><span class="label label-danger">La caja esta cerrada</span><br>Para abrir nuestra caja ir a la opción:<br>Punto de venta > Apertura de Caja</h3>
                </div>
              <?php } ?>
            </div>
            <!-- DIV row -->
          </div>
        </div>
      </div>
    </div>
    <!-- /. Row -->    
  </section>
  <!-- /. Main content -->
</div>
<!-- /.content-wrapper -->