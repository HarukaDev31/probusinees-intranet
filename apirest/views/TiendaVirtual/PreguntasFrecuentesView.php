<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Main content -->
  <section class="content">
    <!-- New box-header -->
    <div class="row">
      <div class="col-xs-12">
        <div class="div-content-header">
          <h2><i class="fa fa-comment"></i> Preguntas Frecuentes</h2>
        </div>
      </div>
    </div>
    <!-- ./New box-header -->
    
    <?php if($this->user->ID_Pais == 2) {//2=MEXICO ?>
    <div class="row">
      <div class="col-xs-12">
        <div class="box box-content">
          <!-- box-header -->
          <div class="box-header box-header-new">
            <div class="row div-Filtros">
              <div class="col-xs-12">
                <h3>¿Cuanto tarda en llegar un pedido a mi cliente?</h3>
                <p>
                  Depende de la paquetería, hay desde 2 hasta 6 días hábiles. Con la nueva paquetería Delivery Ecxpress llegan el mismo día en CDMX.
                </p>
              </div>
              
              <div class="col-xs-12">
                <h3>¿A que se refiere el % en el precio de los envíos?</h3>
                <p>
                  Es un % que cobra la paquetería por el servicio de COD
                </p>
              </div>
              
              <div class="col-xs-12">
                <h3>¿Que es el precio de las devoluciones?</h3>
                <p>
                  Cuando tu cliente no recibe tu pedido y fue enviado a otro estado con esta paquetería.
                </p>
              </div>
              
              <div class="col-xs-12">
                <h3>¿Todo es Pago contra entrega?</h3>
                <p>
                  No, tenemos COD en un 90% del país, la cobertura la puedes encontrar arriba a la derecha en tu plataforma y te podemos brindar una lista de segmentación para que llegues a los Estados con mayor Efectividad.
                </p>
              </div>
              
              <div class="col-xs-12">
                <h3>¿En cuánto tiempo recibo el dinero de mis ventas con pago contra entrega?</h3>
                <p>
                  En tu billetera digital se verá reflejado automáticamente una vez entregado el pedido.
                  Y los pagos se realizan todos los lunes.
                </p>
              </div>
            </div>
          </div><!-- box header-->
        </div><!-- box -->
      </div>
    </div>
    <!-- /.row -->
    <?php } ?>
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->