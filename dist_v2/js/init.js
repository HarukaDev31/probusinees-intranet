$(function () {
  $( '.div-AgregarEditar' ).hide();
  $( '.div-Listar' ).show();

  //Div ocultar / mostrar
  $( '#btn-cancelar' ).click(function() {
    $( '.div-AgregarEditar' ).hide();
    $( '.div-Listar' ).show();
  })
})

function ParseDateString(fecha, tipo_fecha, caracter){
  if (tipo_fecha == 'fecha_bd') {// Caracter -> (-) y formato de fecha BD (YYY-MM-DD)
    var _FE = fecha.split(caracter);
    return _FE[2] + '/' + _FE[1] + '/' + _FE[0];
  }
}