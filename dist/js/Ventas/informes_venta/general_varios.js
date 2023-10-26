var url;

$(document).ready(function(){
  Reporte(fYear, fMonth, '', '', 1, 0);
  $( "#sltReporte" ).change(function(){
    Reporte(fYear, fMonth, '', '', 1, 0);
  })
})

function Reporte(anio, mes, ID_Moneda, Nu_Tipo_Producto, iOrder, iImpuesto){
  $( '#modal-loader' ).modal('show');
  $( "#dvReporte" ).load(base_url + 'Ventas/informes_venta/GeneralVariosController/Ajax/Reporte', {
    tipo : $( "#sltReporte" ).val(),
    y : anio,
    m : mes,
    ID_Moneda : ID_Moneda,
    Nu_Tipo_Producto : Nu_Tipo_Producto,
    iOrder : iOrder,
    iImpuesto: iImpuesto,
  },
  function(response, status, xhr) {
    if(status=='success'){
      $( '#modal-loader' ).modal('hide');
    } else {
      $( '#modal-loader' ).modal('hide');
      alert('Problemas al carga información')
    }
  });  
}

function topProductosxMarca() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("id-marca");
  filter = input.value.toUpperCase();
  table = document.getElementById("top-venta");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[0];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }
  }
}