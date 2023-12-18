$(function () {
  $( '.div-AgregarEditar' ).hide();
  $( '.div-Listar' ).show();

  //Div ocultar / mostrar
  $( '#btn-cancelar' ).click(function() {
    $('#span-id_pedido').html('');
    $( '.div-AgregarEditar' ).hide();
    $( '.div-Listar' ).show();
  })
  
  $( '.input-number' ).on('input', function () {
    this.value = this.value.replace(/[^0-9]/g,'');
  });
  
  $( '.input-decimal' ).on('input', function () {
    var numero = parseFloat(this.value);
    if(!isNaN(numero)){
      this.value = this.value.replace(/[^0-9\.]/g,'');
      if (numero < 0)
        this.value = '';
    } else
      this.value = this.value.replace(/[^0-9\.]/g,'');
  });
})

function ParseDateString(fecha, tipo_fecha, caracter){
  if (tipo_fecha == 'fecha_bd') {// Caracter -> (-) y formato de fecha BD (YYY-MM-DD)
    var _FE = fecha.split(caracter);
    return _FE[2] + '/' + _FE[1] + '/' + _FE[0];
  }
}

(function() {
  /**
   * Ajuste decimal de un número.
   *
   * @param {String}  tipo  El tipo de ajuste.
   * @param {Number}  valor El numero.
   * @param {Integer} exp   El exponente (el logaritmo 10 del ajuste base).
   * @returns {Number} El valor ajustado.
   */
  function decimalAdjust(type, value, exp) {
    // Si el exp no está definido o es cero...
    if (typeof exp === 'undefined' || +exp === 0) {
      return Math[type](value);
    }
    value = +value;
    exp = +exp;
    // Si el valor no es un número o el exp no es un entero...
    if (isNaN(value) || !(typeof exp === 'number' && exp % 1 === 0)) {
      return NaN;
    }
    // Shift
    value = value.toString().split('e');
    value = Math[type](+(value[0] + 'e' + (value[1] ? (+value[1] - exp) : -exp)));
    // Shift back
    value = value.toString().split('e');
    return +(value[0] + 'e' + (value[1] ? (+value[1] + exp) : exp));
  }

  // Decimal round
  if (!Math.round10) {
    Math.round10 = function(value, exp) {
      return decimalAdjust('round', value, exp);
    };
  }
  // Decimal floor
  if (!Math.floor10) {
    Math.floor10 = function(value, exp) {
      return decimalAdjust('floor', value, exp);
    };
  }
  // Decimal ceil
  if (!Math.ceil10) {
    Math.ceil10 = function(value, exp) {
      return decimalAdjust('ceil', value, exp);
    };
  }
})();

