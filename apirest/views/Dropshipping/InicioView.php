<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">  
  <!-- Section Main content -->
  <section class="content">
    <?php
    if ($arrTourTiendaVirtual['sStatus']=='success') {
      $iCantidadLineaCarga = 0;
      foreach ($arrTourTiendaVirtual['arrData'] as $row)
        $iCantidadLineaCarga += ($row->Nu_Estado_Proceso==1 ? 50 : 0);//completado
      if ($iCantidadLineaCarga<100) {
    ?>
      <!-- row TOUR TIENDA VIRTUAL -->
      
      <!-- row TOUR TIENDA VIRTUAL -->
      <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="container-tour" style="background: #acaaf7; margin: 3% 0;border-radius: 12px;">
          <div class="title-tour">
            <h2 style="font-weight: 900;">Tienda Virtual</h2>
          </div>
          <div class="title-tour">
            <h4>Completa los siguientes pasos para configurar tu tienda</h4>
          </div>
          <div class="container-carga">
            <div class="box-linea">
              <!-- aquí se debe de validar segun respuesta aumentar el largo del div-->
              <div class="linea-carga" style="width: <?php echo $iCantidadLineaCarga; ?>%;"></div>
            </div>
            <div class="texto-porcentaje" style="margin-left: 3%; width: 13%;">
              <!--El  lago que le pones al div de linea de carga debe de ir aquí-->
              <span><?php echo $iCantidadLineaCarga; ?> %</span>
            </div>
          </div>
          <div class="tour-item">
            <?php foreach ($arrTourTiendaVirtual['arrData'] as $row) { ?>
              <a href="<?php echo base_url() . $row->Txt_Url_Menu; ?>">
                <i class="fa fa-check-circle <?php echo ($row->Nu_Estado_Proceso == 1 ? 'active' : ''); ?>"></i>
                <div>
                  <?php if (!empty($row->No_Subtitulo)) { ?>
                    <p><?php echo $row->No_Subtitulo; ?></p>
                  <?php } ?>
                  <label><?php echo $row->No_Titulo; ?></label>
                </div>
              </a>
            <?php } ?>
          </div>
        </div>
      </div><!-- col -->
    <?php
      }//if total
    } ?>

    <!-- row-reporte-grafico-inicio-->
    <div class="row">
        
      <div class="col-md-12">
        <br>
        <div class="callout callout-success" style="background-color: #acaaf7 !important;border-color: #7c40ff;">
          
          <?php if($this->empresa->Nu_Lae_Shop==1 && isset($arrUrlDropshipping) && (!empty($arrUrlDropshipping->No_Dominio_Dropshipping) || !empty($arrUrlDropshipping->No_Subdominio_Dropshipping))){
            $sUrlTiendaVirtual=(!empty($arrUrlDropshipping->No_Subdominio_Dropshipping) ? $arrUrlDropshipping->No_Subdominio_Dropshipping . '.' . $arrUrlDropshipping->No_Dominio_Dropshipping : $arrUrlDropshipping->No_Dominio_Dropshipping);
          ?>
          <span style="font-size: 1.9rem;">URL de tienda: </span> <span id="span-url_tienda" style="font-size: 1.9rem; font-weight:bold"><?php echo $sUrlTiendaVirtual . '  '; ?></span>&nbsp;&nbsp;<span id="span-compartir_url_tienda" style="font-size: 1.75rem; cursor:pointer" onclick="copyText()"><i class="fa fa-share-alt" aria-hidden="true"></i> Compartir</span>
          <?php } ?>
        </div>
      </div>

      <div class="col-md-12">
        <div class="box">
          <div class="box-header ui-sortable-handle" style="margin-bottom: -20px !important;padding-top: 0px;">
            <div class="row">
              <div class="col-xs-12 col-sm-12 col-md-4">
                <div class="div-content-header">
                  <h3 class="box-title" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-bar-chart-o fa-fw"></i> Venta</h3>
                </div>
              </div>
              <div class="col-xs-6 col-sm-3 col-md-2">
                <label>Moneda</label>
                <div class="form-group">
                  <select id="cbo-filtro_moneda" class="form-control" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              <div class="col-xs-6 col-sm-3 col-md-2">
                <label>Impuesto</label>
                <div class="form-group">
                  <select id="cbo-filtro_impuesto" class="form-control" style="width: 100%;">
    		  				  <option value="0">No</option>
                    <option value="1">Si</option>
                  </select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              <div class="col-xs-12 col-sm-6 col-md-4">
                <label>Fecha</label>
                <div class="input-group" style="width: 100%;">
                  <button type="button" class="btn btn-success pull-right" id="daterange-btn" data-toggle="tooltip" data-placement="bottom" style="width: 100%;">
                    <span><?php echo $sToday; ?></span>
                    <i class="fa fa-caret-down"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <div class="row">
              <div id="div-inicio-reporte-grafico" class="col-md-12">
                
                <br>

                <div class="row">
                  <?php
                  //TOTAL DE CUADRO
                  $sSignoMoneda = 'S/';
                  if (isset($reporte['Tabla'][0]->No_Signo))
                    $sSignoMoneda = $reporte['Tabla'][0]->No_Signo;
                  $fTotal = 0.00;
                  foreach ($reporte['Tabla'] as $row){
                    $fTotal += $row->venta_neta;
                  }
                  $fTotal = round($fTotal, 2);

                  //FIN TOTAL DE CUADRO

                  // TOTAL DE TRANS
                  $fCantTransPendiente = 0;
                  $fCantTransConfirmado = 0;
                  $fCantTransEntregado = 0;
                  
                  $fTotalPendiente = 0.00;
                  $fTotalConfirmado = 0.00;
                  $fTotalEntregado = 0.00;
                  foreach ($reporte['arrPedidosEstados'] as $row){
                    if ($row->Nu_Estado==1){
                      $fCantTransPendiente += $row->Qt_Cantidad_Trans;
                      $fTotalPendiente += $row->Ss_Total;
                    } else if ($row->Nu_Estado==2){
                      $fCantTransConfirmado += $row->Qt_Cantidad_Trans;
                      $fTotalConfirmado += $row->Ss_Total;
                    } else {
                      $fCantTransEntregado += $row->Qt_Cantidad_Trans;
                      $fTotalEntregado += $row->Ss_Total;
                    }
                  }
                  ?>
                  <div class="col-md-4 col-sm-4 col-xs-12">
                    <div class="info-box">
                      <span class="info-box-icon bg-aqua"><i class="ion ion-ios-cart-outline"></i></span>
                      <div class="info-box-content">
                        <span class="info-box-number"><?php echo $fCantTransPendiente; ?></span>
                        <span class="info-box-text"><span class="hidden-sm">Pedidos</span> Pendientes</span>
                        <span class="info-box-number"><?php echo $sSignoMoneda . ' ' . numberFormat($fTotalPendiente, 2, '.', ','); ?></span>
                      </div>
                    </div>
                  </div>
                  
                  <div class="col-md-4 col-sm-4 col-xs-12">
                    <div class="info-box">
                      <span class="info-box-icon bg-blue"><i class="ion ion-ios-heart-outline"></i></span>
                      <div class="info-box-content">
                        <span class="info-box-number"><?php echo $fCantTransConfirmado; ?></span>
                        <span class="info-box-text"><span class="hidden-sm">Pedidos</span> Confirmados</span>
                        <span class="info-box-number"><?php echo $sSignoMoneda . ' ' . numberFormat($fTotalConfirmado, 2, '.', ','); ?></span>
                      </div>
                    </div>
                  </div>
                  
                  <div class="col-md-4 col-sm-4 col-xs-12">
                    <div class="info-box">
                      <span class="info-box-icon bg-green"><i class="ion ion-ios-pricetag-outline"></i></span>
                      <div class="info-box-content">
                        <span class="info-box-number"><?php echo $fCantTransEntregado; ?></span>
                        <span class="info-box-text"><span class="hidden-sm">Pedidos</span> Entregados</span>
                        <span class="info-box-number"><?php echo $sSignoMoneda . ' ' . numberFormat($fTotalEntregado, 2, '.', ','); ?></span>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- /.box footer TOTAL TRANS -->

                <div id="div-inicio-filtro-reporte-grafico" class="col-xs-12 col-sm-12 col-md-10 tab-pane" style="padding: 0px;margin: 0px;">
                  <canvas id="canvas-graficaBar" class="wrapper-home"></canvas>
                </div>
                
                <div class="box-footer">
                  <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-2">
                      <div class="description-block border-right">
                        <span class="description-percentage text-green"><i class="fa fa-caret-up"></i></span>
                        <h5 class="description-header"><?php echo $sSignoMoneda . ' ' . numberFormat($fTotal, 2, '.', ','); ?></h5>
                        <span class="description-text" data-toggle="tooltip" data-placement="bottom">VENTA ENTREGADOS</span>
                      </div>
                      <!-- /.description-block -->
                    </div>
                    <!-- /.col-xs-12 col-sm-4-->
                  </div>
                  <!-- /.row -->
                </div>
                <!-- /.box footer TOTAL VENTA ENTREGADOS -->                

                <br>
                
                <div class="row">
                  <div class="col-xs-12 col-sm-12 col-md-4">
                    <div class="box box-primary">
                      <div class="box-header with-border">
                        <h3 class="box-title" data-toggle="tooltip" data-placement="bottom" title="Solo muestra los últimos 10 ítems más vendido, según el rango de fecha colocado en la parte superior"><i class="fa fa-shopping-cart"></i> Productos más vendidos</h3>
                      </div>
                      <!-- /.box-header -->
                      <div class="box-body">
                        <ul class="products-list product-list-in-box">
                          <?php
                          if ( count($reporte['arrProductosVendidos']) == 0 ) { ?>
                            <li class="item">
                              <div class="product-info">
                                <span class="product-description">No hay registros</span>
                              </div>
                            </li>
                          <?php
                          } else {
                            foreach ($reporte['arrProductosVendidos'] as $row){ ?>
                            <li class="item">
                              <div class="product-img">
                                <img src="<?php echo (!empty($row->No_Imagen_Item) ? $row->No_Imagen_Item : 'dist/img/default-50x50.gif'); ?>" alt="<?php echo $row->No_Producto; ?>">
                              </div>
                              <div class="product-info">
                                <a href="javascript:void(0)" class="product-title" data-toggle="tooltip" data-placement="bottom" title="Marca">
                                  <?php echo (!empty($row->No_Marca) ? $row->No_Marca : 'Sin marca'); ?>
                                  <span class="label label-dark pull-right" data-toggle="tooltip" data-placement="bottom" title="Cantidad">Cant.: <?php echo numberFormat($row->Qt_Producto, 2, '.', ','); ?></span>
                                  <span class="label label-dark pull-right" data-toggle="tooltip" data-placement="bottom" title="Total"><?php echo $row->No_Signo . ' ' . numberFormat($row->Ss_Producto, 2, '.', ','); ?></span>
                                </a>
                                <span class="product-description" title="<?php echo $row->No_Marca . ' ' . $row->No_Producto; ?>"><?php echo strlen($row->No_Producto) > 80 ? $row->No_Producto . '..' : $row->No_Producto; ?></span>
                              </div>
                            </li>
                            <?php
                            }
                          } ?>
                        </ul>
                        <!-- /.ul-items -->
                      </div>
                      <!-- /.box-body -->
                      <div class="box-footer text-center"><a class="btn btn-success btn-block small-box-footer" href="<?php echo base_url() . 'Logistica/ReglasLogistica/ProductoController/listarProductos'; ?>">Ver  &nbsp; <i class="fa fa-arrow-circle-right"></i></a></div>
                    </div>
                    <!-- /.box-principal productos mas vendidos -->
                  </div>
                  <!-- /.col-xs-12 col-sm-12 col-md-4 productos mas vendidos -->
                  
                  <div class="col-xs-12 col-sm-12 col-md-4">
                    <div class="box box-primary">
                      <div class="box-header with-border">
                        <h3 class="box-title" data-toggle="tooltip" data-placement="bottom" title="Solo muestra los últimos 10 ítems más vendido, según el rango de fecha colocado en la parte superior"><i class="fa fa-shopping-cart"></i> Categorías más vendidas</h3>
                      </div>
                      <!-- /.box-header -->
                      <div class="box-body">
                        <ul class="products-list product-list-in-box">
                          <?php
                          if ( count($reporte['arrCategoriasVendidos']) == 0 ) { ?>
                            <li class="item">
                              <div class="product-info">
                                <span class="product-description">No hay registros</span>
                              </div>
                            </li>
                          <?php
                          } else {
                            foreach ($reporte['arrCategoriasVendidos'] as $row){ ?>
                            <li class="item">
                              <div class="product-img">
                                <img src="<?php echo (!empty($row->No_Imagen_Url_Categoria) ? $row->No_Imagen_Url_Categoria : 'dist/img/default-50x50.gif'); ?>" alt="<?php echo $row->No_Familia; ?>">
                              </div>
                              <div class="product-info">
                                <a href="javascript:void(0)" class="product-title" data-toggle="tooltip" data-placement="bottom" title="Marca">
                                  -
                                  <span class="label label-dark pull-right" data-toggle="tooltip" data-placement="bottom" title="Cantidad">Cant.: <?php echo numberFormat($row->Qt_Producto, 2, '.', ','); ?></span>
                                  <span class="label label-dark pull-right" data-toggle="tooltip" data-placement="bottom" title="Total"><?php echo $row->No_Signo . ' ' . numberFormat($row->Ss_Producto, 2, '.', ','); ?></span>
                                </a>
                                <span class="product-description" title="<?php echo $row->No_Familia; ?>"><?php echo strlen($row->No_Familia) > 80 ? $row->No_Familia . '..' : $row->No_Familia; ?></span>
                              </div>
                            </li>
                            <?php
                            }
                          } ?>
                        </ul>
                        <!-- /.ul-items -->
                      </div>
                      <!-- /.box-body -->
                    </div>
                    <!-- /.box-principal categorias mas vendidos -->
                  </div>
                  <!-- /.col-xs-12 col-sm-12 col-md-4 categorias mas vendidos -->
                
                  <div class="col-xs-12 col-sm-12 col-md-4">
                    <div class="box box-info">
                      <div class="box-header with-border">
                        <h3 class="box-title" data-toggle="tooltip" data-placement="bottom" title="Solo muestra los últimos 10 clientes con más venta y no incluye impuestos, según el rango de fecha colocado en la parte superior"><i class="fa fa-users"></i> Mejores Clientes</h3>
                      </div>
                      <!-- /.box-header -->
                      <div class="box-body table-responsive">
                        <table class="table table-striped table-bordered">
                          <thead>
                            <tr>
                              <th class="left">Nombre</th>
                              <th class="right">Cantidad</th>
                              <th class="right">M</th>
                              <th class="right" data-toggle="tooltip" data-placement="bottom">Total</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                            if ( count($reporte['arrMejoresClientes']) == 0 ) { ?>
                              <tr>
                                <td colspan="4" class="text-center">No hay registros</td>
                              </tr>
                            <?php
                            } else {
                              foreach ($reporte['arrMejoresClientes'] as $row){
                                $sNombreCliente = $row->No_Razsocial;
                                $sNombreCliente = strtolower($sNombreCliente);
                                
                                $sBuscarPalabra = 'vario';
                                $bStatus = strpos($sNombreCliente, $sBuscarPalabra);
                                
                                $sBuscarPalabra = 'cliente';
                                $bStatus2 = strpos($sNombreCliente, $sBuscarPalabra);
                                if ($bStatus === false && $bStatus2 === false) { ?>
                                  <tr>
                                    <td class="left"><?php echo $row->No_Razsocial; ?></td>
                                    <td class="right"><?php echo numberFormat($row->Qt_Producto, 2, '.', ','); ?></td>
                                    <td class="right"><?php echo $row->No_Signo; ?></td>
                                    <td class="right"><?php echo numberFormat($row->venta_neta, 2, '.', ','); ?></td>
                                  </tr>
                                <?php
                                }
                              }
                            } ?>
                          </tbody>
                          <tfoot>
                            <tr>
                              <td colspan="4" class="text-center"><a class="btn btn-success btn-block small-box-footer" href="<?php echo base_url() . 'Ventas/ReglasVenta/ClienteController/listarClientes'; ?>">Ver clientes &nbsp; <i class="fa fa-arrow-circle-right"></i></a></td>
                            </tr>
                          </tfoot>
                        </table>
                        <!-- /.table-responsive -->
                      </div>
                      <!-- /.box-body -->
                    </div>
                    <!-- /.box-principal ordenes de venta -->
                  </div>
                  <!-- /.col-xs-12 col-sm-12 col-md-4 ordenes de venta -->
                </div>
                <!-- /.row producto mas vendidos y mejores clientes -->                
              </div>
            </div>
          </div>
          <!-- /.box-body -->
        </div><!--box-->
      </div>
    </div>
    <!-- /. row-reporte-grafico-inicio-->
  </section>
  <!-- /. Section content -->
</div>
<!-- /.content-wrapper -->