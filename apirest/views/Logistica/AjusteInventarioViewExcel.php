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
            <i class="fa fa-balance-scale" aria-hidden="true"></i> Ajuste de Inventario - EXCEL
          </h3>
        </div>
      </div>
      <!-- ./New box-header -->
    </div>
    <div class="row">
      <div class="col-xs-12">
        <div class="box box-content">
          <div class="row">
          <h2 class="text-center" style="margin-top: 5px;"><label>Almacén: <?php echo $this->session->userdata['almacen']->No_Almacen; ?></label></h2>
          <div class="col-xs-12 col-sm-12 col-md-12" style="border-left-color: #bd2130;border-radius: .25rem;box-shadow: 0 1px 3px rgba(0,0,0,.12),0 1px 2px rgba(0,0,0,.24);background-color: #fff;border-left: 5px solid #e9ecef;border-left-color: rgb(233, 236, 239);margin-bottom: 1rem;padding: 1rem;">
            <div class="alert alert-default alert-dismissible">
              <?php
              $iFilaExcel = 0;
              $arrValidateDataDuplicada = array();
              foreach($arrDataExcel as $sub){
                $sub['Nu_Codigo_Barra'] = trim($sub['Nu_Codigo_Barra']);
                $new[$sub['Nu_Codigo_Barra']][$iFilaExcel] = $iFilaExcel;
                ++$iFilaExcel;
              }

              foreach($new as $key => $sub){
                if(count($sub) == 1){ 
                  continue;
                }
                $arrValidateDataDuplicada[$key] = array_keys(array_diff($sub, [min($sub)]));
              }

              $iVerificarEstadoRegistroItem = 0;
              $iVerificarEstadoRegistroStock = 0;
              $iFila=0;
              $iRegistroValido = 0;
              $iRegistroInvalido = 0;
              $iCodigoDuplicado=0;
              foreach($arrDataExcel as $row) {
                $row['Nu_Codigo_Barra'] = trim($row['Nu_Codigo_Barra']);
                $row['Qt_Producto'] = trim($row['Qt_Producto']);
                $iCodigoDuplicado=0;
                if (!isset($arrValidateDataDuplicada[$row['Nu_Codigo_Barra']]))
                  $iCodigoDuplicado = 0;
                else {
                  $iCodigoDuplicado = 0;
                  for ($x = 0; $x <= count($arrValidateDataDuplicada); $x++) {
                    if(isset($arrValidateDataDuplicada[$row['Nu_Codigo_Barra']][$x])) {
                      $iCodigoDuplicado = 0;
                      if($iFila == $arrValidateDataDuplicada[$row['Nu_Codigo_Barra']][$x]) {
                        $iCodigoDuplicado = 1;
                        break;
                      }
                    }
                  }
                }
                
                if ($iCodigoDuplicado == 0) {
                  if(!empty($row['Nu_Codigo_Barra'])) {
                    $objResponseItem = $this->AjusteInventarioModel->getItem( $row['Nu_Codigo_Barra'] );
                    if(is_object($objResponseItem)) {
                      if($objResponseItem->Nu_Tipo_Producto!=0) {                        
                        if($objResponseItem->Nu_Compuesto==0) {
                          //validar si ya genero stock por compra o venta
                          $objResponseStockItemxAlmacen = $this->AjusteInventarioModel->getStockItemxAlmacen( $objResponseItem->ID_Producto );
                          if(is_object($objResponseStockItemxAlmacen)) {
                            $iVerificarEstadoRegistroItem = 1;
                          } else {
                            $iVerificarEstadoRegistroItem = 0;
                          }
                        } else {
                          $iVerificarEstadoRegistroItem = 0;
                        }
                      } else {
                        $iVerificarEstadoRegistroItem = 0;
                      }
                    } else {
                      $iVerificarEstadoRegistroItem = 0;
                    }
                  } else {
                    $iVerificarEstadoRegistroItem = 0;
                  }
                } else {
                  $iVerificarEstadoRegistroItem = 0;
                }
              
                if ($row['Qt_Producto'] >= 0 && $row['Qt_Producto']!='' ) {
                  $iVerificarEstadoRegistroStock = 1;
                } else {
                  $iVerificarEstadoRegistroStock = 0;
                }
                
                if ($iVerificarEstadoRegistroItem == 1 && $iVerificarEstadoRegistroStock == 1) {
                  ++$iRegistroValido;
                } else {
                  ++$iRegistroInvalido;
                }
                ++$iFila;
              }// ./ foreach
              ?>

              <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                <h4>Total Registros</h4>
                <div class="form-group">
                  <h5 style="font-size: 17px;"><?php echo count($arrDataExcel); ?></h5>
                </div>
              </div>
              <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                <h4>Registros válidos</h4>
                <div class="form-group">
                  <h5 id="h5-excel-registro_valido_excel" style="font-size: 17px; color: #198754"><?php echo $iRegistroValido; ?></h5>
                </div>
              </div>
              <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                <h4>Registros inválidos</h4>
                <div class="form-group">
                  <h5 style="font-size: 17px; color: #dc3545"><?php echo $iRegistroInvalido; ?></h5>
                </div>
              </div>
            </div>
          </div>
          </div>
          <!-- /.box-body -->
          <div class="row">
            <?php
            if(isset($arrDataExcel)) {
              $attributes = array('id' => 'form-AjusteInventarioExcel');
              echo form_open('', $attributes);
              ?>
              <div class="table-responsive">
                <table id="table-AjusteInventario-Excel" class="table table-striped table-bordered">
                  <thead>
                    <tr>
                      <th class="text-left">Codigo</td>
                      <th class="text-left">Producto</td>
                      <th class="text-right">Stock Fisico</td>
                      <th class="text-center">Estado</td>
                      <th class="text-center">Mensaje</td>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $iFilaExcel = 0;
                    $arrValidateDataDuplicada = array();
                    foreach($arrDataExcel as $sub){
                      $sub['Nu_Codigo_Barra'] = trim($sub['Nu_Codigo_Barra']);
                      $new[$sub['Nu_Codigo_Barra']][$iFilaExcel] = $iFilaExcel;
                      ++$iFilaExcel;
                    }

                    foreach($new as $key => $sub){
                      if(count($sub) == 1){ 
                        continue;
                      }
                      $arrValidateDataDuplicada[$key] = array_keys(array_diff($sub, [min($sub)]));
                    }
                      
                    $iVerificarEstadoRegistroItem = 0;
                    $iVerificarEstadoRegistroStock = 0;

                    $sMessageErrorItem = '';
                    $sMessageErrorStock = '';

                    $iFila = 0;
                    $iCodigoDuplicado = 0;//0 = bien y 1 = duplicado
                    $iIdItem = 0;
                    $fStockItemActualxAlmacen = 0;
                    foreach($arrDataExcel as $row) {
                      $iIdItem = 0;
                      $fStockItemActualxAlmacen = 0;
                      $row['Nu_Codigo_Barra'] = trim($row['Nu_Codigo_Barra']);
                      $row['Qt_Producto'] = trim($row['Qt_Producto']);
                      ?>
                      <tr>
                        <td class="text-left"><?php echo $row['Nu_Codigo_Barra']; ?></td>

                        <td class="text-left">
                          <?php
                          $iCodigoDuplicado=0;
                          if (!isset($arrValidateDataDuplicada[$row['Nu_Codigo_Barra']]))
                            $iCodigoDuplicado = 0;
                          else {
                            $iCodigoDuplicado = 0;
                            for ($x = 0; $x <= count($arrValidateDataDuplicada); $x++) {
                              if(isset($arrValidateDataDuplicada[$row['Nu_Codigo_Barra']][$x])) {
                                $iCodigoDuplicado = 0;
                                if($iFila == $arrValidateDataDuplicada[$row['Nu_Codigo_Barra']][$x]) {
                                  $iCodigoDuplicado = 1;
                                  break;
                                }
                              }
                            }
                          }
                          
                          if ($iCodigoDuplicado == 0) {
                            if(!empty($row['Nu_Codigo_Barra'])) {
                              $objResponseItem = $this->AjusteInventarioModel->getItem( $row['Nu_Codigo_Barra'] );
                              if(is_object($objResponseItem)) {
                                if($objResponseItem->Nu_Tipo_Producto!=0) {
                                  if($objResponseItem->Nu_Compuesto==0) {
                                    //validar si ya genero stock por compra o venta
                                    $objResponseStockItemxAlmacen = $this->AjusteInventarioModel->getStockItemxAlmacen( $objResponseItem->ID_Producto );
                                    if(is_object($objResponseStockItemxAlmacen)) {
                                      echo $objResponseItem->No_Producto;
                                      
                                      $iIdItem = $objResponseItem->ID_Producto;
                                      $fStockItemActualxAlmacen =  $objResponseStockItemxAlmacen->Qt_Producto;
                                      $iVerificarEstadoRegistroItem = 1;
                                      $sMessageErrorItem = '';
                                    } else {
                                      echo $objResponseItem->No_Producto;
                                      $iVerificarEstadoRegistroItem = 0;
                                      $sMessageErrorItem = '<span class="label label-danger">El producto no tiene ningun movimiento de inventario</span>';
                                    }
                                  } else {
                                    echo $objResponseItem->No_Producto;

                                    $iVerificarEstadoRegistroItem = 0;
                                    $sMessageErrorItem = '<span class="label label-danger">Producto compuesto, debes de ajustar a los items que componen PACK o PROMOCION o ENLACE</span>';
                                  }
                                } else {
                                  echo $objResponseItem->No_Producto;

                                  $iVerificarEstadoRegistroItem = 0;
                                  $sMessageErrorItem = '<span class="label label-danger">Es un SERVICIO este tipo no GENERA STOCK</span>';
                                }
                              } else {
                                $iVerificarEstadoRegistroItem = 0;
                                $sMessageErrorItem = '<span class="label label-danger">No existe codigo: ' . $row['Nu_Codigo_Barra'] . ' registrado en el sistema</span>';
                              }
                            } else {
                              $iVerificarEstadoRegistroItem = 0;
                              $sMessageErrorItem = '<span class="label label-danger">Codigo vacio ' . $row['Nu_Codigo_Barra'] . '</span>';
                            }
                          } else {
                            $iVerificarEstadoRegistroItem = 0;
                            $sMessageErrorItem = '<span class="label label-danger">Codigo DUPLICADO: ' . $row['Nu_Codigo_Barra'] . ' no se registrar</span>';
                          }
                          ?>
                        </td>
                        
                        <td class="text-right">
                          <?php if ($row['Qt_Producto'] >= 0 && $row['Qt_Producto']!='' ) {
                            $iVerificarEstadoRegistroStock = 1;
                            $sMessageErrorStock = '';
                            echo $row["Qt_Producto"]; ?>
                          <?php } else {
                            $iVerificarEstadoRegistroStock = 0;
                            echo $row["Qt_Producto"];
                            if ($row['Qt_Producto']=='')
                              $sMessageErrorStock = '<br><span class="label label-danger">' . "La cantidad esta vacia " . $row['Qt_Producto'] . ", esta debe ser igual a 0 o mayor a 0</span>";
                            else if ($row['Qt_Producto'] < 0)
                              $sMessageErrorStock = '<br><span class="label label-danger">' . "La cantidad es negativa " . $row['Qt_Producto'] . ", esta debe ser igual a 0 o mayor a 0</span>";
                          } ?>
                        </td>
                        
                        <td class="text-center">
                          <?php if ($iVerificarEstadoRegistroItem == 1 && $iVerificarEstadoRegistroStock == 1) { ?>
                            <span class="label label-success">Verificado</span>
                            <input type="hidden" name="arrAjusteInventario[<?php echo $iFila; ?>][iIdItem]" value="<?php echo $iIdItem; ?>">
                            <input type="hidden" name="arrAjusteInventario[<?php echo $iFila; ?>][fStockFisico]" value="<?php echo ($row["Qt_Producto"] - $fStockItemActualxAlmacen); ?>">
                          <?php } else { ?>
                            <span class="label label-danger">Error</span>
                          <?php } ?>
                        </td>

                        <td class="text-center">
                          <?php if ($iVerificarEstadoRegistroItem == 1 && $iVerificarEstadoRegistroStock == 1) { ?>
                            <span class="label label-success"></span>
                          <?php } else { ?>
                            <?php echo $sMessageErrorItem . $sMessageErrorStock; ?>
                          <?php } ?>
                        </td>
                      </tr>
                      <?php
                      ++$iFila;
                    }// ./ foreach
                    ?>
                  </tbody>
                </table>

                <div class="col-xs-12 col-sm-12 col-md-12">
                  <label>Movimiento Inventario</label>
                  <div class="form-group">
                    <label style="font-weight:normal; cursor:pointer;"><input type="radio" style="cursor:pointer;" id="radio-ajuste" class="flat-red" name="iTipoMovimientoInventario" value="19" checked>&nbsp; AJUSTE POR DIFERENCIA DE INVENTARIO</label>
                    &nbsp;&nbsp;<label style="font-weight:normal; cursor:pointer;"><input type="radio" style="cursor:pointer;" id="radio-ajuste_error" class="flat-red" name="iTipoMovimientoInventario" value="21">&nbsp; ERROR DE SISTEMA AJUSTE POR DIFERENCIA DE INVENTARIO</label>
                  </div>
                </div>
                
                <div class="col-xs-12 col-md-6"><br>
                  <div class="form-group">
                    <a href="<?php echo base_url(); ?>Logistica/AjusteInventarioController/listar" id="a-salir-excel-ajuste_inventario" class="btn btn-danger btn-lg btn-block">Cancelar</a>
                  </div>
                </div>

                <div class="col-xs-12 col-md-6"><br>
                  <div class="form-group">
                    <button type="button" id="btn-save-excel-ajuste_inventario" class="btn btn-success btn-lg btn-block">Guardar</button>
                  </div>
                </div>
              </div>
              <?php
              echo form_close();
            }
            ?>
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