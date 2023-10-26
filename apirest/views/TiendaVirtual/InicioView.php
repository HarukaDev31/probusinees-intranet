<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">  
  <!-- Section Main content -->
  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        <h3>Bienvenid@ a EcXlae <b><?php echo $this->user->No_Usuario; ?></b></h3>
      </div>
      
      <div class="col-md-6">
        <div class="">
          <h3><strong><i class="fa fa-graduation-cap"></i> Academia X</strong></h3>
          <div class="form-group">
            <a href="https://t.me/+UxHhahd2B8RhMjMx" target="_blank" rel="noopener noreferrer" style="" class="btn btn-lg btn-info btn-block"><span class="fa fa-telegram"></span> Unirme Grupo Telegram</a>
          </div>
        </div>
      </div>
      
      <div class="col-md-6">
        <div class="">
          <h3><strong><i class="fa fa-link"></i> Link de Referidos</strong> <span style="font-size: 1.5rem;font-weight: normal;"><span class="hidden-md hidden-lg"><br></span> (Genera un ingreso extra compartiendo tu link de referido de EcXlae)</span></h3>
          <div class="form-group">
            <a href="<?php echo base_url() . 'PanelAcceso/LinkReferidoController/listarUsuarios'; ?>" style="color: #ffffff !important;background-color: #0d6efd !important;border-color: #0d6efd;" class="btn btn-lg btn-primary btn-primary-v2 btn-block"><span class="fa fa-link"></span> Link Referido</a>
            </div>
        </div>
      </div>
    </div>
    
    <?php
    if ($arrTourTiendaVirtual['sStatus']=='success') {
      $iCantidadLineaCarga = 0;
      foreach ($arrTourTiendaVirtual['arrData'] as $row)
        $iCantidadLineaCarga += ($row->Nu_Estado_Proceso==1 ? 25 : 0);//completado
      if ($iCantidadLineaCarga<100) {
    ?>
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="container-tour" style="background: #d9d9d9; margin: 3% 0;border-radius: 12px;">
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
              <a href="<?php echo base_url() . (($row->Txt_Url_Menu == 'TiendaVirtual/ItemsTiendaVirtualController/listar' && $this->empresa->Nu_Proveedor_Dropshipping==0) ? 'Proveedores/ProductosProveedoresDropshippingController/listar' : $row->Txt_Url_Menu); ?>">
                <i class="fa fa-check-circle <?php echo ($row->Nu_Estado_Proceso == 1 ? 'active' : ''); ?>"></i>
                <div>
                  <?php if (!empty($row->No_Subtitulo)) { ?>
                    <p><?php echo (($row->Txt_Url_Menu == 'TiendaVirtual/ItemsTiendaVirtualController/listar' && $this->empresa->Nu_Proveedor_Dropshipping==0) ? '' : $row->No_Subtitulo); ?></p>
                  <?php } ?>
                  <label><?php echo (($row->Txt_Url_Menu == 'TiendaVirtual/ItemsTiendaVirtualController/listar' && $this->empresa->Nu_Proveedor_Dropshipping==0) ? 'Super Bodegas' : $row->No_Titulo); ?></label>
                </div>
              </a>
            <?php } ?>
          </div>
        </div>
      </div><!-- col -->
    </div><!-- row -->
    <?php
      }//if total
    } ?>

    <!-- row-reporte-grafico-inicio-->
    <div class="row">
      <div class="col-md-12">
        <h3><strong><i class="fa fa-link"></i> Link de Tienda</strong></h3>
        <div class="callout callout-success" style="background-color: #3c3c3c !important;border-color: #b7b7b7; margin-top: 1.5rem;">
          <?php if($this->empresa->Nu_Lae_Shop==1 && isset($arrUrlTiendaVirtual) && (!empty($arrUrlTiendaVirtual->No_Dominio_Tienda_Virtual) || !empty($arrUrlTiendaVirtual->No_Subdominio_Tienda_Virtual))){
            $sUrlTiendaVirtual=(!empty($arrUrlTiendaVirtual->No_Subdominio_Tienda_Virtual) ? $arrUrlTiendaVirtual->No_Subdominio_Tienda_Virtual . '.' . $arrUrlTiendaVirtual->No_Dominio_Tienda_Virtual : $arrUrlTiendaVirtual->No_Dominio_Tienda_Virtual);
          ?>
          <span id="span-url_tienda" style="font-size: 1.9rem; font-weight:bold"><?php echo $sUrlTiendaVirtual . '  '; ?></span>&nbsp;&nbsp;<span id="span-compartir_url_tienda" style="font-size: 1.75rem; cursor:pointer" onclick="copyText()"><span class="hidden-sm hidden-md hidden-lg"><br></span><i class="fa fa-share-alt" aria-hidden="true"></i> Compartir</span>
          <?php } ?>
        </div>
      </div>

      <div class="col-md-12">
        <div class="box">
          <?php
          $sNombreGrupo = strtoupper($this->user->No_Grupo);
          if ( $sNombreGrupo != 'CAJEROS' && $sNombreGrupo != 'CAJERO' && $sNombreGrupo != 'CAJA' && $sNombreGrupo != 'CAJAS' ) { ?>
          <div class="box-header ui-sortable-handle" style="margin-bottom: -20px !important;padding-top: 0px;">
            <div class="row">
              <div class="col-xs-12 col-sm-12 col-md-4">
                <div class="div-content-header">
                  <h3 class="box-title" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-bar-chart-o fa-fw"></i> Venta</h3>
                </div>
              </div>
              <div class="col-xs-6 col-sm-3 col-md-2 hidden">
                <label>Moneda</label>
                <div class="form-group">
                  <select id="cbo-filtro_moneda" class="form-control" style="width: 100%;"></select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              <div class="col-xs-6 col-sm-3 col-md-2 hidden">
                <label>Impuesto</label>
                <div class="form-group">
                  <select id="cbo-filtro_impuesto" class="form-control" style="width: 100%;">
    		  				  <option value="0">No</option>
                    <option value="1">Si</option>
                  </select>
                  <span class="help-block" id="error"></span>
                </div>
              </div>
              <div class="col-xs-12 col-sm-12 col-md-12">
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
                  <div class="col-md-4 offset-md-4 col-sm-3 offset-sm-3">
                  </div>
                  <div class="col-md-4 col-sm-6 col-xs-12">
                    <div class="info-box">
                      <span class="info-box-icon bg-green"><i class="ion ion-bag"></i></span>
                      <div class="info-box-content">
                        <span class="info-box-text">Ventas totales</span>
                        <span class="info-box-number" style="font-size: 2.5rem;"><?php echo $sSignoMoneda . ' ' . numberFormat($fTotalEntregado, 2, '.', ','); ?></span>
                        <span class="info-box-number" style="font-weight: normal;"><?php echo $fCantTransEntregado; ?> pedidos</span>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- /.box footer TOTAL TRANS -->

                <div id="div-inicio-filtro-reporte-grafico" class="col-xs-12 col-sm-12 col-md-12 tab-pane" style="padding: 0px;margin: 0px;">
                  <canvas id="canvas-graficaBar" class="wrapper-home"></canvas>
                </div>
              </div>
            </div>
          </div>
          <!-- /.box-body -->  
          <?php
          }
          ?>
        </div><!--box-->
      </div>
    </div>
  </section>
  <!-- /. Section content -->
</div>
<!-- /.content-wrapper -->