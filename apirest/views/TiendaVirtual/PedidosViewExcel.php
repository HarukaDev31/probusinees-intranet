<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">  
  <!-- Main content -->
  <section class="content">
    <!-- New box-header -->
    <div class="row">
      <div class="col-xs-12">
        <div class="div-content-header">
          <h3>
            <button type="button" class="btn btn-default back-history"><i class="fa fa-chevron-left"></i>&nbsp;<span class="hidden-xs hidden-sm hidden-md">Regresar</span></button>
            <i class="fa fa-list" aria-hidden="true"></i> Pedidos Manuales - EXCEL
          </h3>
        </div>
      </div>
      <!-- ./New box-header -->
    </div>
    <div class="row">
      <div class="col-xs-12">
        <div class="box box-content">
          <div class="row">
            <div class="col-xs-12">
            <?php
            //array_debug($arrPedidoManualExcel);
            if(isset($arrPedidoManualExcel)) {
              $attributes = array('id' => 'form-PedidoExcel');
              echo form_open('', $attributes);
              ?>
              <div class="table-responsive">
                <table id="table-Pedido-Excel" class="table table-striped table-bordered">
                  <thead>
                    <tr>
                      <th class="text-left">ID Pedido</td>
                      <th class="text-left">Nombre Completo</td>
                      <th class="text-left">Telefono</td>
                      <th class="text-left">Email</td>
                      <th class="text-left">Ciudad</td>
                      <th class="text-left">Direccion</td>
                      <th class="text-left">Referencia</td>
                      <th class="text-left">F. Entrega</td>
                      <th class="text-left">Forma Pago</td>
                      <th class="text-left">Transportadora</td>
                      <th class="text-left">Observaciones</td>
                      <th class="text-left">Codigo</td>
                      <th class="text-left">Producto</td>
                      <th class="text-left">Cantidad</td>
                      <th class="text-left">Precio</td>
                      <th class="text-left">Total</td>
                      <th class="text-left">Transportadora</td>
                      <th class="text-center">Mensaje</td>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $iRegistroValido=0;
                    $iFila=0;
                    $iIdPedido=0;
                    $iVerificarEstadoRegistro = 1;
                    $sMessageError = '';
                    $ID_Producto = 0;
                    $ID_Empresa_Proveedor = 0;
                    $ID_Almacen_Proveeedor = 0;
                    $Ss_Precio_Proveedor_Dropshipping = 0;
                    $ID_Impuesto_Cruce_Documento = 0;
                    //array_debug($arrPedidoManualExcel);
                    foreach($arrPedidoManualExcel as $row) {
                      $iPedidoDiferente=0;//0=mo
                      $ID_Producto = 0;
                      $ID_Empresa_Proveedor = 0;
                      $ID_Almacen_Proveeedor = 0;
                      $Ss_Precio_Proveedor_Dropshipping = 0;
                      $ID_Impuesto_Cruce_Documento = 0;
                      if($iIdPedido != $row['iIdPedido']){
                        $iVerificarEstadoRegistro = 1;
                        $sMessageError = '';
                        $iIdPedido = $row['iIdPedido'];
                        $iPedidoDiferente=1;//1=si
                      }
                      ?>
                      <tr>
                        <td class="text-left">
                          <?php
                          if($iPedidoDiferente==1) {
                            if (!empty($row['iIdPedido'])) {
                              echo $row["iIdPedido"];
                            } else {
                              $iVerificarEstadoRegistro = 0;
                              echo $row["iIdPedido"];
                              $sMessageError .= '<br><span class="label label-danger">ID Pedido esta vacío</span>';
                            }
                          } else {
                            echo "";
                          }
                          ?>
                        </td>
                        <td class="text-left">
                          <?php
                          if($iPedidoDiferente==1) {
                              if (!empty($row['sNombreCompleto'])) {
                              echo $row["sNombreCompleto"];
                            } else {
                              $iVerificarEstadoRegistro = 0;
                              echo $row["sNombreCompleto"];
                              $sMessageError .= '<br><span class="label label-danger">Nombre Completo esta vacío</span>';
                            }
                          } else {
                            echo "";
                          }
                          ?>
                        </td>
                        <td class="text-left">
                          <?php
                          if($iPedidoDiferente==1) {
                            if (!empty($row['iTelefono'])) {
                              echo $row["iTelefono"];
                            } else {
                              $iVerificarEstadoRegistro = 0;
                              echo $row["iTelefono"];
                              $sMessageError .= '<br><span class="label label-danger">Teléfono esta vacío</span>';
                            }
                          } else {
                            echo "";
                          }
                          ?>
                        </td>
                        <td class="text-left"><?php echo $row['sEmail']; ?></td>
                        <td class="text-left">
                          <?php
                          if($iPedidoDiferente==1) {
                            if (!empty($row['sCiudad'])) {
                              echo $row["sCiudad"];
                            } else {
                              $iVerificarEstadoRegistro = 0;
                              echo $row["sCiudad"];
                              $sMessageError .= '<br><span class="label label-danger">Ciudad esta vacía</span>';
                            }
                          } else {
                            echo "";
                          }
                          ?>
                        </td>
                        <td class="text-left">
                          <?php
                          if($iPedidoDiferente==1) {
                              if (!empty($row['sDireccion'])) {
                              echo $row["sDireccion"];
                            } else {
                              $iVerificarEstadoRegistro = 0;
                              echo $row["sDireccion"];
                              $sMessageError .= '<br><span class="label label-danger">Dirección esta vacía</span>';
                            }
                          } else {
                            echo "";
                          }
                          ?>
                        </td>
                        <td class="text-left"><?php echo $row['sDireccionReferencia']; ?></td>
                        <td class="text-left">
                          <?php
                          if($iPedidoDiferente==1) {
                              if (!empty($row['dFechaEntrega'])) {
                              echo $row["dFechaEntrega"];
                            } else {
                              $iVerificarEstadoRegistro = 0;
                              echo $row["dFechaEntrega"];
                              $sMessageError .= '<br><span class="label label-danger">Fecha Entrega esta vacía</span>';
                            }
                          } else {
                            echo "";
                          }
                          ?>
                        </td>
                        <td class="text-left">
                          <?php
                          if($iPedidoDiferente==1) {
                            if ($row['iFormaPago'] == 1 || $row['iFormaPago'] == 2) {
                              echo ($row['iFormaPago'] == 1 ? 'Contra Entrega' : 'Dropshipping');
                            } else {
                              $iVerificarEstadoRegistro = 0;
                              echo $row["iFormaPago"];
                              $sMessageError .= '<span class="label label-danger">Codigo Forma de Pago: ' . $row["iFormaPago"] . ' - No existe</span>';
                            }
                          } else {
                            echo "";
                          }
                          ?>
                        </td>
                        <td class="text-left">
                          <?php
                          if($iPedidoDiferente==1) {
                            if ($row['iTransportadora'] == 1 || $row['iTransportadora'] == 2) {
                              echo ($row['iTransportadora'] == 1 ? 'Call Center' : 'Coordinado');
                            } else {
                              $iVerificarEstadoRegistro = 0;
                              echo $row["iTransportadora"];
                              $sMessageError .= '<br><span class="label label-danger">Codigo Transportadora: ' . $row["iFormaPago"] . ' - No existe</span>';
                            }
                          } else {
                            echo "";
                          }
                          ?>
                        </td>
                        <td class="text-left"><?php echo $row['sObservaciones']; ?></td>
                        <td class="text-left">
                          <?php
                          if (!empty($row['iIdStockProducto'])) {
                            //validar si ya genero stock por compra o venta
                            $objResponseStockItemxAlmacen = $this->PedidosModel->getStockItemxAlmacen( $row['iIdStockProducto'] );
                            if(is_object($objResponseStockItemxAlmacen)) {
                              //array_debug($objResponseStockItemxAlmacen);
                              $ID_Producto = $objResponseStockItemxAlmacen->ID_Producto;
                              $ID_Empresa_Proveedor = $objResponseStockItemxAlmacen->ID_Empresa_Proveedor;
                              $ID_Almacen_Proveeedor = $objResponseStockItemxAlmacen->ID_Almacen_Proveeedor;
                              $Ss_Precio_Proveedor_Dropshipping = $objResponseStockItemxAlmacen->Ss_Precio_Proveedor_Dropshipping;
                              $ID_Impuesto_Cruce_Documento = $objResponseStockItemxAlmacen->ID_Impuesto_Cruce_Documento;
                              echo $row["iIdStockProducto"];
                            } else {
                              $iVerificarEstadoRegistro = 0;
                              $sMessageError .= '<br><span class="label label-danger">No existe codigo de producto: ' . $row['iIdStockProducto'] . '</span>';
                            }
                          } else {
                            $iVerificarEstadoRegistro = 0;
                            echo $row["iIdStockProducto"];
                            $sMessageError .= '<br><span class="label label-danger">Codigo de producto esta vacío</span>';
                          }
                          ?>
                        </td>

                        <td class="text-left">
                          <?php if (!empty($row['sNombreProducto'])) {
                            echo $row["sNombreProducto"];
                          } else {
                            $iVerificarEstadoRegistro = 0;
                            echo $row["sNombreProducto"];
                            $sMessageError .= '<br><span class="label label-danger">Producto esta vacío</span>';
                          } ?>
                        </td>

                        <td class="text-right">
                          <?php if ($row['fCantidad'] > 0 && $row['fCantidad']!='' ) {
                            echo $row["fCantidad"];
                          } else {
                            $iVerificarEstadoRegistro = 0;
                            echo $row["fCantidad"];
                            if ($row['fCantidad']=='')
                              $sMessageError .= '<br><span class="label label-danger">' . "La cantidad esta vacia " . $row['fCantidad'] . ", esta debe ser igual o mayor 1</span>";
                            else if ($row['fCantidad'] < 0)
                              $sMessageError .= '<br><span class="label label-danger">' . "La cantidad es negativa " . $row['fCantidad'] . ", esta debe ser igual o mayor 1</span>";
                          } ?>
                        </td>

                        <td class="text-right">
                          <?php echo ($row['fTotal']>0 && $row['fCantidad']>0 ? ($row["fTotal"] / $row['fCantidad']) : ''); ?>
                        </td>

                        <td class="text-right">
                          <?php if ($row['fTotal'] > 0 && $row['fTotal']!='' ) {
                            echo $row["fTotal"];
                          } else {
                            $iVerificarEstadoRegistro = 0;
                            echo $row["fTotal"];
                            if ($row['fTotal']=='')
                              $sMessageError .= '<br><span class="label label-danger">' . "La cantidad esta vacia " . $row['fTotal'] . ", esta debe ser igual o mayor 1</span>";
                            else if ($row['fTotal'] < 0)
                              $sMessageError .= '<br><span class="label label-danger">' . "La cantidad es negativa " . $row['fTotal'] . ", esta debe ser igual o mayor 1</span>";
                          } ?>
                        </td>

                        <?php
                        if($row['iTipoTransportadora']==3){//1=ECXLAE
                          $sNombreTipoTransportadora='Ecxlae';//BD 3=ECXLAE
                        } else if($row['iTipoTransportadora']==1){//2=99 MINUTOS
                          $sNombreTipoTransportadora='99 Minutos';//BD 1=99 MINUTOS
                        } else if($row['iTipoTransportadora']==2){//3=QUIKEN
                          $sNombreTipoTransportadora='Quiken';//BD 2=QUIKEN
                        }
                        ?>
                        <td class="text-left"><?php echo $sNombreTipoTransportadora; ?></td>

                        <td class="text-center">
                          <?php if ($iVerificarEstadoRegistro == 1) {
                            ++$iRegistroValido;
                          ?>
                            <span class="label label-success">Aprobado</span>
                            <input type="hidden" name="arrPedidoManualExcel[<?php echo $iFila; ?>][iIdPedido]" value="<?php echo $row['iIdPedido']; ?>">
                            <input type="hidden" name="arrPedidoManualExcel[<?php echo $iFila; ?>][sNombreCompleto]" value="<?php echo $row['sNombreCompleto']; ?>">
                            <input type="hidden" name="arrPedidoManualExcel[<?php echo $iFila; ?>][iTelefono]" value="<?php echo $row['iTelefono']; ?>">
                            <input type="hidden" name="arrPedidoManualExcel[<?php echo $iFila; ?>][sCiudad]" value="<?php echo $row['sCiudad']; ?>">
                            <input type="hidden" name="arrPedidoManualExcel[<?php echo $iFila; ?>][sDireccion]" value="<?php echo $row['sDireccion']; ?>">
                            <input type="hidden" name="arrPedidoManualExcel[<?php echo $iFila; ?>][sDireccionReferencia]" value="<?php echo $row['sDireccionReferencia']; ?>">
                            <input type="hidden" name="arrPedidoManualExcel[<?php echo $iFila; ?>][dFechaEntrega]" value="<?php echo $row['dFechaEntrega']; ?>">
                            <input type="hidden" name="arrPedidoManualExcel[<?php echo $iFila; ?>][iFormaPago]" value="<?php echo $row['iFormaPago']; ?>">
                            <input type="hidden" name="arrPedidoManualExcel[<?php echo $iFila; ?>][iTransportadora]" value="<?php echo $row['iTransportadora']; ?>">
                            <input type="hidden" name="arrPedidoManualExcel[<?php echo $iFila; ?>][sObservaciones]" value="<?php echo $row['sObservaciones']; ?>">
                            <input type="hidden" name="arrPedidoManualExcel[<?php echo $iFila; ?>][ID_Producto]" value="<?php echo $ID_Producto; ?>">
                            <input type="hidden" name="arrPedidoManualExcel[<?php echo $iFila; ?>][ID_Empresa_Proveedor]" value="<?php echo $ID_Empresa_Proveedor; ?>">
                            <input type="hidden" name="arrPedidoManualExcel[<?php echo $iFila; ?>][ID_Almacen_Proveeedor]" value="<?php echo $ID_Almacen_Proveeedor; ?>">
                            <input type="hidden" name="arrPedidoManualExcel[<?php echo $iFila; ?>][Ss_Precio_Proveedor_Dropshipping]" value="<?php echo $Ss_Precio_Proveedor_Dropshipping; ?>">
                            <input type="hidden" name="arrPedidoManualExcel[<?php echo $iFila; ?>][ID_Impuesto_Cruce_Documento]" value="<?php echo $ID_Impuesto_Cruce_Documento; ?>">
                            <input type="hidden" name="arrPedidoManualExcel[<?php echo $iFila; ?>][sNombreProducto]" value="<?php echo $row['sNombreProducto']; ?>">
                            <input type="hidden" name="arrPedidoManualExcel[<?php echo $iFila; ?>][fCantidad]" value="<?php echo $row['fCantidad']; ?>">
                            <input type="hidden" name="arrPedidoManualExcel[<?php echo $iFila; ?>][fPrecio]" value="<?php echo ($row['fTotal'] / $row['fCantidad']); ?>">
                            <input type="hidden" name="arrPedidoManualExcel[<?php echo $iFila; ?>][fTotal]" value="<?php echo $row['fTotal']; ?>">
                            <input type="hidden" name="arrPedidoManualExcel[<?php echo $iFila; ?>][sEmail]" value="<?php echo $row['sEmail']; ?>">
                            <input type="hidden" name="arrPedidoManualExcel[<?php echo $iFila; ?>][iTipoTransportadora]" value="<?php echo $row['iTipoTransportadora']; ?>">
                          <?php } else {
                            echo $sMessageError;
                          } ?>
                        </td>
                      </tr>
                      <?php
                      ++$iFila;
                    }// ./ foreach
                    ?>
                  </tbody>
                </table>
                
                <!--para verificar registros validos-->
                <input type="hidden" class="form-control" id="hidden-registros_validos" value="<?php echo $iRegistroValido; ?>">

                <div class="col-xs-12 col-md-6"><br>
                  <div class="form-group">
                    <a href="<?php echo base_url(); ?>TiendaVirtual/PedidosTiendaVirtualController/listar" id="a-salir-excel-pedido_manual" class="btn btn-danger btn-lg btn-block">Cancelar</a>
                  </div>
                </div>

                <div class="col-xs-12 col-md-6"><br>
                  <div class="form-group">
                    <button type="button" id="btn-save-excel-pedido_manual" class="btn btn-success btn-lg btn-block">Guardar</button>
                  </div>
                </div>
              </div>
              <?php
              echo form_close();
            }
            ?>
          </div><!--row-->
          </div><!--col-->
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