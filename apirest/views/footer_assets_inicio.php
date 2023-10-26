<script src="<?php echo base_url() . 'assets/js/Chart.min.js'; ?>"></script>
<script type="text/javascript">
var ctx = document.getElementById('canvas-graficaBar');
var myChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: [<?php echo $reporte['Grafica']['Categoria'];?>, ],
    datasets: [{
      label: 'VENTA',
      data: [<?php echo $reporte['Grafica']['Vendido']; ?>, ],
      backgroundColor: 'rgba(0, 255, 82, 0.6)',
      borderColor: 'rgba(0, 255, 82, 0.6)',
      borderWidth: 1
    }]
  },
  options: {
    responsive: true,
    legend: {
      display: true,
      position: 'top',
    },
    title: {
      display: true,
      text: ''//Titulo
    },
    scales: {
      yAxes: [{
        ticks: {
          beginAtZero:true
        }
      }]
    },
    tooltips: {
      callbacks: {
        label: function(tooltipItem, data) {
          var sGanancia = data.datasets[tooltipItem.datasetIndex].label;
          var arrMoneda = '<?php echo $reporte['Grafica']['Moneda']; ?>';
          arrMoneda = arrMoneda.split(',');
          var sSignoMoneda = '';
          for (var i = 1; i < arrMoneda.length; i++){
            if(i == data.labels[tooltipItem.index])//Comparo con el dia que selecciono vs lo que tengo en BD de moneda
              sSignoMoneda = arrMoneda[i - 1];//Le resto uno porque el array empieza en 0
          }
          var fTotalNeto = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
          return "Día " + data.labels[tooltipItem.index] + ': ' + sSignoMoneda + ' ' + number_format(parseFloat(fTotalNeto),2);
        },
      },
    },
  }
});
</script>