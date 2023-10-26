//Clear HTML textarea
function clearHTMLTextArea(str){
  str=str.replace(/<br>/gi, "");
  str=str.replace(/<br\s\/>/gi, "");
  str=str.replace(/<br\/>/gi, "");
  str=str.replace(/<\/button>/gi, "");
  str=str.replace(/<br >/gi, "");
  return str;
}

// valid email pattern
var eregex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
$.validator.addMethod("validemail", function( value, element ) {
	return this.optional( element ) || eregex.test( value );
});

$(document).keyup(function(event){
  if (event.which == 27) {//ESC
    var $modal_delete = $('.modal-message-delete');
    $modal_delete.modal('show');

    $('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
    $('.modal-message-delete').addClass('modal-danger');

    $('.modal-title-message-delete').text('¿Estas seguro que deseas salir?');

    $('#btn-save-delete').off('click').click(function () {
      $modal_delete.modal('hide');

      $( '.div-AgregarEditar' ).hide();
      $( '.div-Ver' ).hide();
      $( '.div-AgregarEditarPrecio' ).hide();
      $( '.div-Listar' ).show();
    });

    $('#btn-cancel-delete').off('click').click(function () {
      $modal_delete.modal('hide');
    });
  }
});

//Variables globales
var url,
src_root_sitio_web_js = '../../../../',
src_root_sitio_subweb_js = '../../../',
sTokenGlobal = '',
iIdTipoRubroEmpresaGlobal = 0,
iValidarStockGlobal = 0,
iMostrarLogoTicketGlobal = 0,
iFormatoTicketLiquidacionCajaGlobal = 0,
iHeightLogoTicketGlobal = 0,
iWidthLogoTicketGlobal = 0,
iVerificarAutorizacionVentaGlobal = 0,
sTerminosCondicionesTicket = '',
iActivarDescuentoPuntoVenta = 0,
iPrecioPuntoVenta = 0,
iActivarUnaLineaDetalleTicket = 0,
Nu_ID_Tipo_Documento_Venta_Predeterminado = 0,
Nu_Cliente_Varios_Venta_Predeterminado = 0,
ID_Entidad_Clientes_Varios_Venta_Predeterminado = 0,
Nu_Tipo_Lenguaje_Impresion_Pos = 0,
nu_enlace = '',
fYearIni,
fYearFin,
fInicioSistema,
fToday = new Date(), // Date today
fYear = fToday.getFullYear(),
fMonth = fToday.getMonth() + 1,//hoy es 0!
fDay = fToday.getDate();

//AUTOCOMPLETE
var caractes_no_validos_global_autocomplete = "\"'~!@%^\|";
// Se puede crear un arreglo a partir de la cadena
let search_global_autocomplete = caractes_no_validos_global_autocomplete.split('');
// Solo tomé algunos caracteres, completa el arreglo
let replace_global_autocomplete = ['', '', '', '', '', '', '', '', ''];
//28 caracteres
// FIN AUTOCOMPLETE

//WHATSAPP
var caractes_no_validos_whatsapp = "\"'~!@#$%^&*()_+:;{}[]\|<>,/?";
// Se puede crear un arreglo a partir de la cadena
let search_whatsapp = caractes_no_validos_whatsapp.split('');
// Solo tomé algunos caracteres, completa el arreglo
let replace_whatsapp = ['', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''];
//28 caracteres
// FIN WHATSAPP

var arrDiasMes =
'{' +
'"dias_mes":[' +
  '{"value":"1"},' +
  '{"value":"2"},' +
  '{"value":"3"},' +
  '{"value":"4"},' +
  '{"value":"5"},' +
  '{"value":"6"},' +
  '{"value":"7"},' +
  '{"value":"8"},' +
  '{"value":"9"},' +
  '{"value":"10"},' +
  '{"value":"11"},' +
  '{"value":"12"},' +
  '{"value":"13"},' +
  '{"value":"14"},' +
  '{"value":"15"},' +
  '{"value":"16"},' +
  '{"value":"17"},' +
  '{"value":"18"},' +
  '{"value":"19"},' +
  '{"value":"20"},' +
  '{"value":"21"},' +
  '{"value":"22"},' +
  '{"value":"23"},' +
  '{"value":"24"},' +
  '{"value":"25"},' +
  '{"value":"26"},' +
  '{"value":"27"},' +
  '{"value":"28"},' +
  '{"value":"0"}' +
  ']' +
'}';
arrDiasMes = JSON.parse(arrDiasMes);
// /. Variables Globales

$(function () {
  $('.select2').select2();

  $(".clearable").each(function () {
    var $inp = $(this).find("input:text"),
    $cle = $(this).find(".clearable__clear");
    
    $inp.on("input", function () {
      $cle.toggle(!!this.value);

      $('.autocomplete-suggestion').html('');
    });

    $cle.on("touchstart click", function (e) {
      e.preventDefault();
      $inp.val("").trigger("input");

      $('.autocomplete-suggestion').html('');
    });
  });

  $('#cbo-almacen').change(function () {
    if ($(this).val() > 0) {
      var arrParams = {
        'iIdEmpresa': $('#header-a-id_empresa').val(),
        'iIdOrganizacion': $('#header-a-id_organizacion').val(),
        'iIdAlmacen': $(this).val(),
      };
      getAlmacenesSession(arrParams);
    }
  })

  // Div formulario para agregar / editar
  $( '.div-AgregarEditar' ).hide();
  $( '.div-Ver' ).hide();
  
  //Flat red color scheme for iCheck
  $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
    checkboxClass: 'icheckbox_flat-green',
    radioClass   : 'iradio_flat-green'
  })
  
	$.fn.datepicker.dates['en'] = {
    days: ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"],
    daysShort: ["Dom", "Lun", "Mar", "Mié", "Juv", "Vie", "Sáb"],
    daysMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sá"],
    months: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Setiembre", "Octubre", "Noviembre", "Diciembre"],
    monthsShort: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Set", "Oct", "Nov", "Dic"],
    today: "Hoy",
    clear: "Limpiar",
    format: "dd/mm/yyyy",
    titleFormat: "MM yyyy",
    weekStart: 0
  };
  
  //Datemask dd/mm/yyyy
  $('#datemask').inputmask('dd/mm/yyyy', { 'placeholder': 'dd/mm/yyyy' });
  
  $( '.input-datepicker' ).inputmask('dd/mm/yyyy', { 'placeholder': 'dd/mm/yyyy' });
  $( '.input-datepicker' ).val(fDay + '/' + fMonth + '/' + fYear);
	$( '.input-datepicker' ).datepicker({
		autoclose : true,
		startDate : new Date(fYear + '-' + fMonth + '-' + (parseInt(fDay) + 1)),
		todayHighlight : true
  });
  
  $( '.date-picker-invoice' ).inputmask('dd/mm/yyyy', { 'placeholder': 'dd/mm/yyyy' });
  $( '.date-picker-invoice' ).val(fDay + '/' + fMonth + '/' + fYear);
	
  $( '.input-datepicker-today-to-more' ).inputmask('dd/mm/yyyy', { 'placeholder': 'dd/mm/yyyy' });
  $( '.input-datepicker-today-to-more' ).val(fDay + '/' + fMonth + '/' + fYear);
  
  //Date picker employee
	fYearIni = fYear - 65;//Edad para empleo 35 significa años
	fYearFin = fYear - 17;//Edad para empleo 17 significa años
  $( '.date-picker-employee' ).datepicker({
    autoclose : true,
    startDate : new Date(fYearIni + '-' + fMonth + '-' + fDay),
    endDate: new Date(fYearFin + '-' + fMonth + '-' + fDay),
    todayHighlight: true
  });

  //Date picker employee
  fYearIni = fYear - 65;//Edad para empleo 35 significa años
  fYearFin = fYear;//Edad para empleo 17 significa años
  $('.date-picker-chid').datepicker({
    autoclose: true,
    startDate: new Date(fYearIni + '-' + fMonth + '-' + fDay),
    endDate: new Date(fYearFin + '-' + fMonth + '-' + fDay),
    todayHighlight: true
  });
  
  //Validation input's
  $('.input-guias_remision').on('input', function () {
    this.value = this.value.replace(/[^tTa-zA-Z0-9\,\-]/g, '');
  });

  $('.textarea-caracter_especial').bind('input propertychange', function () {
    if (/[%|<>#]/.test(this.value)) {
      alert('No se acepta caracter %')
      this.value = this.value.replace('%', '');
    } else {
      this.value = this.value;
    }
    //this.value = this.value.replace(/[^%\%]/g, '');
  });

  //$('.input-subdominio').bind('input', function () {
  $('.input-subdominio').on('input', function () {
    this.value = this.value.replace(/[^a-z0-9\-_]/g,'');
    /*
    if (/[!%$&]/.test(this.value)) {
      alert('No se acepta caracter "!%$&"')
      this.value = this.value.replace('%', '');
    } else {
      this.value = this.value;
    }
    */
  });

  //$('.input-subdominio').bind('input', function () {
  $('.input-dominio').on('input', function () {
    this.value = this.value.replace(/[^a-z0-9\-_.]/g,'');
    /*
    if (/[!%$&]/.test(this.value)) {
      alert('No se acepta caracter "!%$&"')
      this.value = this.value.replace('%', '');
    } else {
      this.value = this.value;
    }
    */
  });

  $( '.input-codigo_barra' ).on('input', function () {
    this.value = this.value.replace(/[^a-zA-Z0-9\-#]/g,'');
  });
  
  $( '.input-number' ).on('input', function () {
    this.value = this.value.replace(/[^0-9]/g,'');
  });
  
  $( '.input-porcentaje' ).on('input', function () {
    var numero = parseFloat(this.value);
    if(!isNaN(numero)){
      this.value = this.value.replace(/[^0-9\.]/g,'');
      if (numero < 0)
        this.value = '';
    } else
      this.value = this.value.replace(/[^0-9\.]/g,'');
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
  
  $( '.input-number_operacion' ).on('input', function () {
    var numero = parseFloat(this.value);
    if(!isNaN(numero)){
      this.value = this.value.replace(/[^0-9]/g,'');
      if (numero < 0)
        this.value = '';
    } else
      this.value = this.value.replace(/[^0-9]/g,'');
  });
  
  //Div ocultar / mostrar
  $( '#btn-cancelar' ).click(function() {
    $( '.div-AgregarEditar' ).hide();
    $( '.div-Ver' ).hide();
    $( '.div-Listar' ).show();
  })
  
  $( '#btn-cancelar_precio' ).click(function() {
    $( '.div-AgregarEditarPrecio' ).hide();
    $( '.div-Listar' ).show();
  })
  
  $( '#btn-cerrar_modal_excel' ).click(function() {
    $('#modal-message_excel').modal('toggle');
  })
  
  //Global Autocomplete
  $( '.autocompletar' ).autoComplete({
    minChars: 0,
    source: function (term, response) {
      term = term.trim();
      if (term.length > 2) {
        var term                = term.toLowerCase();
        var global_class_method = $( '.autocompletar' ).data('global-class_method');
        var global_table        = $( '.autocompletar' ).data('global-table');
        
        var filter_id_codigo = '';
        if ($( '#txt-EID_Producto' ).val() !== undefined)
          filter_id_codigo = $( '#txt-EID_Producto' ).val();
        
        $.post( base_url + global_class_method, { global_table: global_table, global_search : term, filter_id_codigo : filter_id_codigo }, function( arrData ){
          response(arrData);
        }, 'JSON');
      }
    },
    renderItem: function (item, search){
      search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
      var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");
      var data_direccion = '';
      if ($( '#txt-Txt_Direccion_Entidad' ).val() != undefined)
        data_direccion = 'data-direccion_cliente="' + item.Txt_Direccion_Entidad + '"';
      var data_telefono = '';
      if ($( '#txt-Nu_Telefono_Entidad_Cliente' ).val() != undefined)
        data_telefono = 'data-telefono="' + item.Nu_Telefono_Entidad + '"';
      var data_celular = '';
      if ($( '#txt-Nu_Celular_Entidad_Cliente' ).val() != undefined)
        data_celular = 'data-celular="' + item.Nu_Celular_Entidad + '"';
      var data_email = '';
      if ($( '#txt-Txt_Email_Entidad_Cliente' ).val() != undefined)
        data_email = 'data-email="' + item.Txt_Email_Entidad + '"';
      var data_id_tipo_documento_identidad = '';
      if ($('#hidden-ID_Tipo_Documento_Identidad_Existente').val() != undefined)
        data_id_tipo_documento_identidad = 'data-id_tipo_documento_identidad="' + item.ID_Tipo_Documento_Identidad + '"';
      var data_dias_credito = '';
      //if ( $( '#txt-Fe_Vencimiento' ).val() != undefined && ($( '#cbo-MediosPago' ).val() != undefined && $( '#cbo-MediosPago' ).find(':selected').data('nu_tipo') == 1) )
        data_dias_credito = 'data-dias_credito="' + item.Nu_Dias_Credito + '"';
      return '<div title="' + caracteresValidosAutocomplete(item.Nombre) + '" class="autocomplete-suggestion" data-id="' + item.ID + '" data-codigo="' + item.Codigo + '" data-nombre="' + caracteresValidosAutocomplete(item.Nombre) + '" data-estado="' + item.Nu_Estado + '" data-id_departamento="' + item.ID_Departamento + '" data-id_provincia="' + item.ID_Provincia + '" data-id_distrito="' + item.ID_Distrito + '" data-val="' + search + '" ' + data_direccion + ' ' + data_telefono + ' ' + data_celular + ' ' + data_email + ' ' + data_dias_credito + ' ' + data_id_tipo_documento_identidad + '>' + caracteresValidosAutocomplete(item.Nombre).replace(re, "<b>$1</b>") + '</div>';
    },
    onSelect: function(e, term, item){
      $( '#txt-AID' ).val(item.data('id'));
      if ($('#txt-AID_Doble').val() !== undefined)
        $('#txt-AID_Doble').val(item.data('id'));
      $( '#txt-ACodigo' ).val(item.data('codigo'));
      $('#txt-ANombre').val(item.data('nombre'));
      if ($('#hidden-ID_Tipo_Documento_Identidad_Existente').val() !== undefined)
        $('#hidden-ID_Tipo_Documento_Identidad_Existente').val(item.data('id_tipo_documento_identidad'));
      if ( $( '#txt-Filtro_Entidad' ).val() !== undefined )
        $( '#txt-Filtro_Entidad' ).val(item.data('nombre'));
      if ( $( '#txt-Txt_Direccion_Entidad' ).val() !== undefined )
        $('#txt-Txt_Direccion_Entidad').val(item.data('direccion_cliente'));
      if ($('#txt-Txt_Direccion_Entidad-modal').val() !== undefined)
        $('#txt-Txt_Direccion_Entidad-modal').val(item.data('direccion_cliente'));
      if ( $( '#txt-Nu_Telefono_Entidad_Cliente' ).val() !== undefined )
        $( '#txt-Nu_Telefono_Entidad_Cliente' ).val(item.data('telefono'));
      if ( $( '#txt-Nu_Celular_Entidad_Cliente' ).val() !== undefined )
        $( '#txt-Nu_Celular_Entidad_Cliente' ).val(item.data('celular'));
      if ( $( '#txt-Txt_Email_Entidad_Cliente' ).val() !== undefined )
        $('#txt-Txt_Email_Entidad_Cliente').val(item.data('email'));
      if ($('#txt-Nu_Celular_Entidad').val() !== undefined)
        $('#txt-Nu_Celular_Entidad').val(item.data('celular'));
      if ($('#txt-Txt_Email_Entidad').val() !== undefined)
        $('#txt-Txt_Email_Entidad').val(item.data('email'));
      if ( $( '#label-no_nombres' ).val() !== undefined )
        $('#label-no_nombres').text(item.data('nombre'));
      if ( $( '#hidden-nu_numero_documento_identidad' ).val() !== undefined )
        $('#hidden-nu_numero_documento_identidad').val(item.data('codigo'));
      if ( $( '#hidden-estado_entidad' ).val() !== undefined )
        $('#hidden-estado_entidad').val(item.data('estado'));
        
      var iDiasCredito = item.data('dias_credito');
      if (item.data('dias_credito') == null || item.data('dias_credito') == 0)
        iDiasCredito = 1;
      if ( $( '#txt-Fe_Vencimiento' ).val() != undefined && ($( '#cbo-MediosPago' ).val() != undefined && $( '#cbo-MediosPago' ).find(':selected').data('nu_tipo') == 1) ) {
        dNuevaFechaVencimiento = sumaFecha(iDiasCredito, $( '#txt-Fe_Emision' ).val());
        $( '#txt-Fe_Vencimiento' ).val( dNuevaFechaVencimiento );
      }
      //dias de credito
      if ( $( '#txt-Nu_Dias_Credito' ).val() !== undefined )
        $('#txt-Nu_Dias_Credito').val(iDiasCredito);

      $( '#txt-ACodigo' ).closest('.form-group').find('.help-block').html( '' );
      $('#txt-ACodigo').closest('.form-group').removeClass('has-error');
      if ($('[name="Txt_Direccion_Delivery"]').val() !== undefined)
        $('[name="Txt_Direccion_Delivery"]').val(item.data('direccion_cliente'));
      if ($('[name="AID"]').val() !== undefined)
        $('[name="AID"]').val(item.data('id'));

      //Proceso para verificar lista de precio si tiene el cliente o no
      if ($('#txt-Obtener_Lista_Precio').val() !== undefined) {
        $.post(base_url + 'HelperController/getListaPrecioxCliente', { Nu_Tipo_Lista_Precio: 1, ID_Entidad: item.data('id') }, function (responseListaxCliente) {
          if (responseListaxCliente.sStatus == 'success') {
            $('#cbo-lista_precios').val(responseListaxCliente.arrData[0].ID_Lista_Precio_Cabecera);
            var arrParams = {
              sUrl: 'HelperController/getItems',
              ID_Almacen: $('#cbo-almacen').val(),
              iIdListaPrecio: responseListaxCliente.arrData[0].ID_Lista_Precio_Cabecera,
              ID_Linea: 'favorito',
            };
            getItems(arrParams);
          } else {
            $('#cbo-lista_precios').val(0);
            var arrParams = {
              sUrl: 'HelperController/getItems',
              ID_Almacen: $('#cbo-almacen').val(),
              iIdListaPrecio: 0,
              ID_Linea: 'favorito',
            };
            getItems(arrParams);
          }
        }, 'JSON');
      }
    }
  });
  
  $( '.autocompletar_detalle_version2' ).autoComplete({
    minChars: 0,
    source: function (term, response) {
      term = term.trim();
      if (term.length > 2) {
        var global_class_method = $( '.autocompletar_detalle_version2' ).data('global-class_method');
        var global_table = $( '.autocompletar_detalle_version2' ).data('global-table');

        var filter_id_almacen = '';
        if ( $( '#cbo-DescargarInventario' ).val() == '1' && $( '#txt-Nu_Tipo_Registro' ).val() == '1')
          filter_id_almacen = $( '#cbo-Almacenes' ).val();

        var filter_nu_compuesto = '';
        if ($( '#txt-Nu_Compuesto' ).val() !== undefined)
          filter_nu_compuesto = $( '#txt-Nu_Compuesto' ).val();

        var filter_nu_tipo_producto = '';
        if ($( '#txt-Nu_Tipo_Producto' ).val() !== undefined)
          filter_nu_tipo_producto = $( '#txt-Nu_Tipo_Producto' ).val();
        
        var filter_lista = 0;
        if ($( '#cbo-lista_precios' ).val() !== undefined)
          filter_lista = $( '#cbo-lista_precios' ).val();
        
        var send_post = {
          global_table : global_table,
          global_search : term,
          filter_id_almacen : filter_id_almacen,
          filter_nu_compuesto : filter_nu_compuesto,
          filter_nu_tipo_producto : filter_nu_tipo_producto,
          filter_lista : filter_lista,
        }
        
        $.post( base_url + global_class_method, send_post, function( arrData ){
          response(arrData);
        }, 'JSON');
      }
    },
    renderItem: function (item, search){
      search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
      var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");
      var Ss_Precio = 0;
      if ( (item.Ss_Precio === null || item.Ss_Precio == 0.000000) && (item.Ss_Precio_Item !== null || item.Ss_Precio_Item != 0.000000) && $( '#txt-Nu_Tipo_Registro' ).val() == '1')
        Ss_Precio = item.Ss_Precio_Item;
      if ( (item.Ss_Precio === null || item.Ss_Precio == 0.000000) && (item.Ss_Costo_Item !== null || item.Ss_Costo_Item != 0.000000) && $( '#txt-Nu_Tipo_Registro' ).val() == '0')
        Ss_Precio = item.Ss_Costo_Item;
      if ( item.Ss_Precio !== null )
        Ss_Precio = item.Ss_Precio;
      return '<div title="' + caracteresValidosAutocomplete(item.Nombre) + '" class="autocomplete-suggestion" data-id="' + item.ID + '" data-nu_tipo_item="' + item.Nu_Tipo_Producto + '" data-codigo="' + item.Codigo + '" data-nombre="' + caracteresValidosAutocomplete(item.Nombre) + '" data-precio="' + Ss_Precio + '" data-nu_tipo_impuesto="' + item.Nu_Tipo_Impuesto + '" data-id_impuesto_cruce_documento="' + item.ID_Impuesto_Cruce_Documento + '" data-ss_impuesto="' + item.Ss_Impuesto + '" data-qt_producto="' + item.Qt_Producto + '" data-val="' + search + '">' + caracteresValidosAutocomplete(item.Nombre).replace(re, "<b>$1</b>") + ' / <strong>P:</strong> ' + Ss_Precio + ' / <strong>S:</strong> ' + (isNaN(parseFloat(item.Qt_Producto)) ? 0 : parseFloat(item.Qt_Producto) ) + '</div>';
    },
    onSelect: function(e, term, item){
      $( '#txt-Nu_Codigo_Barra' ).val(item.data('codigo'));
      $( '#txt-Qt_Producto' ).val(item.data('qt_producto'));
      if ( $( '#txt-Ss_Precio' ).val() !== undefined )
        $( '#txt-Ss_Precio' ).val(item.data('precio'));
      if ( $( '#txt-ID_Impuesto_Cruce_Documento' ).val() !== undefined )
        $( '#txt-ID_Impuesto_Cruce_Documento' ).val(item.data('id_impuesto_cruce_documento'));
      if ( $( '#txt-Nu_Tipo_Impuesto' ).val() !== undefined )
        $( '#txt-Nu_Tipo_Impuesto' ).val(item.data('nu_tipo_impuesto'));
      if ( $( '#txt-Ss_Impuesto' ).val() !== undefined )
        $( '#txt-Ss_Impuesto' ).val(item.data('ss_impuesto'));
      if ( $( '#txt-nu_tipo_item' ).val() !== undefined )
        $( '#txt-nu_tipo_item' ).val(item.data('nu_tipo_item'));
      if ( $( '#txt-id_item' ).val() !== undefined )
        $( '#txt-id_item' ).val(item.data('id'));
      if ( $( '#txt-item' ).val() !== undefined )
        $( '#txt-item' ).val(item.data('nombre'));
    }
  });

  //Global Autocomplete Contacto
  $( '.autocompletar_contacto' ).autoComplete({
    minChars: 0,
    source: function (term, response) {
      term = term.trim();
      if (term.length > 2) {
        var filter_tipo_asiento = '';
        if ($( '#txt-ID_Tipo_Asiento' ).val() !== undefined)
          filter_tipo_asiento = $( '#txt-ID_Tipo_Asiento' ).val();

        $.post( base_url + 'AutocompleteController/getAllContact', { global_search : term.toLowerCase(), filter_tipo_asiento : filter_tipo_asiento }, function( arrData ){
          if (arrData.length === 0){
            if ( $( '#txt-Nu_Documento_Identidad_existe' ).val() !== undefined )
              $( '#txt-Nu_Documento_Identidad_existe' ).val( '' );
            if ( $( '#txt-No_Contacto_existe' ).val() !== undefined )
              $( '#txt-No_Contacto_existe' ).val( '' );
            if ( $( '#txt-Txt_Email_Contacto_existe' ).val() !== undefined )
              $( '#txt-Txt_Email_Contacto_existe' ).val( '' );
            if ( $( '#txt-Nu_Telefono_Contacto_existe' ).val() !== undefined )
              $( '#txt-Nu_Telefono_Contacto_existe' ).val( '' );
            if ( $( '#txt-Nu_Celular_Contacto_existe' ).val() !== undefined )
              $( '#txt-Nu_Celular_Contacto_existe' ).val( '' );
          }
          response(arrData);
        }, 'JSON');
      }
    },
    renderItem: function (item, search){
      search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
      var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");
      return '<div class="autocomplete-suggestion" data-id="'+item.ID+'" data-tipo="'+item.ID_Tipo_Documento_Identidad+'" data-codigo="'+item.Nu_Documento_Identidad+'" data-nombre="'+item.No_Contacto+'" data-correo="'+item.Txt_Email_Contacto+'" data-telefono="'+item.Nu_Telefono_Contacto+'" data-celular="'+item.Nu_Celular_Contacto+'" data-val="'+search+'">'+item.No_Contacto.replace(re, "<b>$1</b>")+'</div>';
    },
    onSelect: function(e, term, item){
      $("div.id_tipo_documento_identidad select").val(item.data('tipo'));
      $( '#txt-Nu_Documento_Identidad_existe' ).val(item.data('codigo'));
      $( '#txt-AID_Contacto' ).val(item.data('id'));
      $( '#txt-No_Contacto_existe' ).val(item.data('nombre'));
      $( '#txt-Txt_Email_Contacto_existe' ).val(item.data('correo'));
      $( '#txt-Nu_Telefono_Contacto_existe' ).val(item.data('telefono'));
      $( '#txt-Nu_Celular_Contacto_existe' ).val(item.data('celular'));
      if ( $( '#txt-Filtro_Contacto' ).val() !== undefined )
        $( '#txt-Filtro_Contacto' ).val(item.data('nombre'));
    }
  });
  
  //Global Autocomplete Orden
  $( '.autocompletar_orden' ).autoComplete({
    minChars: 0,
    source: function (term, response) {
      term = term.trim();
      if (term.length > 0) {
        $.post( base_url + 'AutocompleteController/getAllOrden', { global_search : term.toLowerCase() }, function( arrData ){
          if (arrData.length === 0){
            if ( $( '#txt-No_Contacto_existe' ).val() !== undefined )
              $( '#txt-No_Contacto_existe' ).val( '' );
          }
          response(arrData);
        }, 'JSON');
      }
    },
    renderItem: function (item, search){
      search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
      var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");
      return '<div class="autocomplete-suggestion" data-id_orden="'+item.ID_Documento_Cabecera+'" data-tipo="'+item.ID_Tipo_Documento_Identidad+'" data-codigo="'+item.Nu_Documento_Identidad+'" data-nombre="'+item.No_Contacto+'" data-correo="'+item.Txt_Email_Contacto+'" data-telefono="'+item.Nu_Telefono_Contacto+'" data-celular="'+item.Nu_Celular_Contacto+'" data-val="'+search+'">'+item.ID_Documento_Cabecera.replace(re, "<b>$1</b>")+'</div>';
    },
    onSelect: function(e, term, item){
      $( '#txt-ID_Documento_Cabecera' ).val(item.data('id_orden'));
      $( '#txt-No_Contacto_existe' ).val(item.data('nombre'));
    }
  });
  
  if (fMonth < 10) {
    fMonth = '0' + fMonth;
  }

	//Cargar datos iniciales
	$.post( base_url + 'HelperController/getToken', function( response ){
	  sTokenGlobal = response.Txt_Token;
		fInicioSistema = response.Fe_Inicio_Sistema;
    iIdTipoRubroEmpresaGlobal = response.Nu_Tipo_Rubro_Empresa;
    iValidarStockGlobal = response.Nu_Validar_Stock;
    iMostrarLogoTicketGlobal = response.Nu_Logo_Empresa_Ticket;
	  iFormatoTicketLiquidacionCajaGlobal = response.Nu_Imprimir_Liquidacion_Caja;
	  iHeightLogoTicketGlobal = response.Nu_Height_Logo_Ticket;
    iWidthLogoTicketGlobal = response.Nu_Width_Logo_Ticket;
    iVerificarAutorizacionVentaGlobal = response.Nu_Verificar_Autorizacion_Venta;
    sTerminosCondicionesTicket = response.Txt_Terminos_Condiciones_Ticket;
    iActivarDescuentoPuntoVenta = response.Nu_Activar_Descuento_Punto_Venta;
    iPrecioPuntoVenta = response.Nu_Precio_Punto_Venta;
    iActivarUnaLineaDetalleTicket = response.Nu_Activar_Detalle_Una_Linea_Ticket;
    Nu_ID_Tipo_Documento_Venta_Predeterminado = response.Nu_ID_Tipo_Documento_Venta_Predeterminado;
    Nu_Cliente_Varios_Venta_Predeterminado = response.Nu_Cliente_Varios_Venta_Predeterminado;
    ID_Entidad_Clientes_Varios_Venta_Predeterminado = response.ID_Entidad_Clientes_Varios_Venta_Predeterminado;
    Nu_Tipo_Lenguaje_Impresion_Pos = response.Nu_Tipo_Lenguaje_Impresion_Pos;
    
    //Date picker report
    $( '.date-picker-report_crud' ).val(fDay + '/' + fMonth + '/' + fYear);

    $( '.date-picker-report_crud' ).inputmask('dd/mm/yyyy', { 'placeholder': 'dd/mm/yyyy' });
    $( '.date-picker-report_crud' ).datepicker({
      autoclose       : true,
      startDate : new Date(fInicioSistema),
      endDate         : new Date(fYear, fMonth, fDay),
      todayHighlight  : true
    });

    $( '.date-picker-report_crud' ).datepicker({}).on('changeDate', function (selected) {
      var minDate = new Date(selected.date.valueOf());
      $( '.txt-Filtro_Fe_Fin' ).datepicker('setStartDate', minDate);
    });

    $( '.date-picker-report' ).val('01/' + fMonth + '/' + fYear);

    $( '.date-picker-report' ).inputmask('dd/mm/yyyy', { 'placeholder': 'dd/mm/yyyy' });
    $( '.date-picker-report' ).datepicker({
      autoclose       : true,
      startDate : new Date(fInicioSistema),
      endDate         : new Date(fYear, fMonth, fDay),
      todayHighlight  : true
    });

    $( '.date-picker-report' ).datepicker({}).on('changeDate', function (selected) {
      var minDate = new Date(selected.date.valueOf());
      $( '.txt-Filtro_Fe_Fin' ).datepicker('setStartDate', minDate);
    });

    //Date picker report
    $( '.date-picker-report_end' ).val(fDay + '/' + fMonth + '/' + fYear);
    $( '.date-picker-report_end' ).inputmask('dd/mm/yyyy', { 'placeholder': 'dd/mm/yyyy' });
    $( '.date-picker-report_end' ).datepicker({
      autoclose       : true,
      startDate : new Date(fInicioSistema),
      endDate         : new Date(fYear, fMonth, fDay),
      todayHighlight  : true
    });

    $( '.date-picker-report_end' ).datepicker({}).on('changeDate', function (selected) {
      var minDate = new Date(selected.date.valueOf());
      $( '.txt-Filtro_Fe_Fin' ).datepicker('setStartDate', minDate);
    });

    $( '.date-picker-invoice' ).datepicker({
  		autoclose : true,
      startDate : new Date(fInicioSistema),
      endDate   : new Date(fYear, fToday.getMonth(), fDay),
  		todayHighlight  : true
    });

    //Date picker invoice
    $( '.input-datepicker-today-to-more' ).datepicker({
  		autoclose : true,
      startDate : new Date(fYear, fToday.getMonth(), fDay),
  		todayHighlight  : true
    });
	}, 'JSON');
  // /. Cargar token dni y ruc, fecha de inicio de sistema

  //Global autocomplete Producto
  $('.autocompletar_detalle').autoComplete({
    minChars: 0,
    source: function (term, response) {
      term = term.trim();
      if (term.length > 2) {
        var global_class_method = $('.autocompletar_detalle').data('global-class_method');
        var global_table = $('.autocompletar_detalle').data('global-table');

        var filter_id_almacen = '';
        if ($('#cbo-DescargarInventario').val() == '1' && $('#txt-Nu_Tipo_Registro').val() == '1')
          filter_id_almacen = $('#cbo-Almacenes').val();

        var filter_id_tipo_movimiento = '';
        if ($('#txt-Nu_Tipo_Registro').val() !== undefined)
          filter_id_tipo_movimiento = $('#txt-Nu_Tipo_Registro').val();

        var filter_nu_compuesto = '';
        if ($('#txt-Nu_Compuesto').val() !== undefined)
          filter_nu_compuesto = $('#txt-Nu_Compuesto').val();

        var filter_nu_tipo_producto = '';
        if ($('#txt-Nu_Tipo_Producto').val() !== undefined)
          filter_nu_tipo_producto = $('#txt-Nu_Tipo_Producto').val();

        var filter_lista = 0;
        if ($('#cbo-lista_precios').val() !== undefined)
          filter_lista = $('#cbo-lista_precios').val();

        var send_post = {
          global_table: global_table,
          global_search: term,
          filter_id_almacen: filter_id_almacen,
          filter_nu_compuesto: filter_nu_compuesto,
          filter_nu_tipo_producto: filter_nu_tipo_producto,
          filter_lista: filter_lista,
          filter_id_tipo_movimiento: filter_id_tipo_movimiento,
        }

        $.post(base_url + global_class_method, send_post, function (arrData) {
          response(arrData);
        }, 'JSON');
      }
    },
    renderItem: function (item, search) {
      search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
      var Ss_Precio = 0;
      if ((item.Ss_Precio === null || item.Ss_Precio == 0.000000) && (item.Ss_Precio_Item !== null || item.Ss_Precio_Item != 0.000000) && $('#txt-Nu_Tipo_Registro').val() == '1')
        Ss_Precio = item.Ss_Precio_Item;
      if ((item.Ss_Precio === null || item.Ss_Precio == 0.000000) && (item.Ss_Costo_Item !== null || item.Ss_Costo_Item != 0.000000) && $('#txt-Nu_Tipo_Registro').val() == '0')
        Ss_Precio = item.Ss_Costo_Item;
      if (item.Ss_Precio !== null)
        Ss_Precio = item.Ss_Precio;
      //return '<div title="' + caracteresValidosAutocomplete(item.Nombre) + '" class="autocomplete-suggestion" data-id="' + item.ID + '" data-no_unidad_medida="' + item.No_Unidad_Medida + '" data-ss_icbper="' + item.Ss_Icbper + '" data-id_impuesto_icbper="' + item.ID_Impuesto_Icbper + '" data-no_codigo_interno="' + item.No_Codigo_Interno + '" data-nu_tipo_item="' + item.Nu_Tipo_Producto + '" data-codigo="' + item.Codigo + '" data-nombre="' + caracteresValidosAutocomplete(item.Nombre) + '" data-precio="' + Ss_Precio + '" data-nu_tipo_impuesto="' + item.Nu_Tipo_Impuesto + '" data-id_impuesto_cruce_documento="' + item.ID_Impuesto_Cruce_Documento + '" data-ss_impuesto="' + item.Ss_Impuesto + '" data-qt_producto="' + item.Qt_Producto + '" data-val="' + search + '">' + (iIdTipoRubroEmpresaGlobal == 2 ? '[' + (item.No_Marca !== null ? item.No_Marca : 'Sin marca') + '] ' : '') + (item.No_Codigo_Interno !== null && item.No_Codigo_Interno != '' ? '[' + item.No_Codigo_Interno + '] ' : '') + caracteresValidosAutocomplete(item.Nombre).replace(re, "<b>$1</b>") + ' / <strong>P:</strong> ' + Ss_Precio + ' / <strong>S:</strong> ' + (isNaN(parseFloat(item.Qt_Producto)) ? 0 : parseFloat(item.Qt_Producto)) + '</div>';
      var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");	
			return '<div style="cursor:pointer;" title="' + caracteresValidosAutocomplete(item.Nombre) + ' / Precio: ' + parseFloat(Ss_Precio).toFixed(2) + ' / Stock: ' + (isNaN(parseFloat(item.Qt_Producto)) ? 0 : parseFloat(item.Qt_Producto)) + '" class="autocomplete-suggestion" data-id="' + item.ID + '" data-nu_activar_precio_x_mayor="' + item.Nu_Activar_Precio_x_Mayor + '" data-no_variante_1="' + item.No_Variante_1 + '" data-no_valor_variante_1="' + item.No_Valor_Variante_1 + '" data-no_variante_2="' + item.No_Variante_2 + '" data-no_valor_variante_2="' + item.No_Valor_Variante_2 + '" data-no_variante_3="' + item.No_Variante_3 + '" data-no_valor_variante_3="' + item.No_Valor_Variante_3 + '" data-no_unidad_medida="' + item.No_Unidad_Medida + '" data-ss_icbper="' + item.Ss_Icbper + '" data-id_impuesto_icbper="' + item.ID_Impuesto_Icbper + '" data-no_codigo_interno="' + item.No_Codigo_Interno + '" data-nu_compuesto="' + item.Nu_Compuesto + '" data-nu_tipo_item="' + item.Nu_Tipo_Producto + '" data-codigo="' + item.Codigo + '" data-nombre="' + caracteresValidosAutocomplete(item.Nombre.replace('"', "''")) + '" data-precio="' + Ss_Precio + '" data-precio_interno="' + item.Ss_Precio_Interno + '" data-nu_tipo_impuesto="' + item.Nu_Tipo_Impuesto + '" data-id_impuesto_cruce_documento="' + item.ID_Impuesto_Cruce_Documento + '" data-ss_impuesto="' + item.Ss_Impuesto + '" data-qt_producto="' + item.Qt_Producto + '" data-txt_composicion="' + item.Txt_Composicion + '" data-val="' + search + '">' + (iIdTipoRubroEmpresaGlobal == 2 ? '[' + (item.No_Marca !== null ? item.No_Marca : 'Sin marca') + '] ' : '') + (item.No_Codigo_Interno !== null && item.No_Codigo_Interno != '' ? '[' + item.No_Codigo_Interno + '] ' : '') + caracteresValidosAutocomplete(item.Nombre).replace(re, "<b>$1</b>") + ($('#hidden-iTipoRubroEmpresa').val() == 6 ? (item.No_Valor_Variante_1 !== null && item.No_Variante_1 !== null ? ' / <strong>' + item.No_Variante_1 + '</strong>: ' + item.No_Valor_Variante_1 : '') + (item.No_Valor_Variante_2 !== null && item.No_Variante_2 !== null ? ' / <strong>' + item.No_Variante_2 + '</strong>: ' + item.No_Valor_Variante_2 : '') + (item.No_Valor_Variante_3 !== null && item.No_Variante_3 !== null ? ' / <strong>' + item.No_Variante_3 + '</strong>: ' + item.No_Valor_Variante_3 : '') + ' ' : '') + ' / <strong>P:</strong> ' + parseFloat(Ss_Precio).toFixed(2) + ' / <strong>S:</strong> ' + (isNaN(parseFloat(item.Qt_Producto)) ? 0 : parseFloat(item.Qt_Producto)) + '</div>';
		},
    onSelect: function (e, term, item) {
      $('#txt-ID_Producto').val(item.data('id'));
      $('#txt-Nu_Codigo_Barra').val(item.data('codigo'));
      $('#txt-No_Producto').val(item.data('nombre'));
      $('#txt-Qt_Producto').val(item.data('qt_producto'));
      if ($('#txt-No_Unidad_Medida').val() !== undefined)
        $('#txt-No_Unidad_Medida').val(item.data('no_unidad_medida'));
      if ($('#txt-Ss_Precio').val() !== undefined)
        $('#txt-Ss_Precio').val(item.data('precio'));
      if ($('#txt-ID_Impuesto_Cruce_Documento').val() !== undefined)
        $('#txt-ID_Impuesto_Cruce_Documento').val(item.data('id_impuesto_cruce_documento'));
      if ($('#txt-Nu_Tipo_Impuesto').val() !== undefined)
        $('#txt-Nu_Tipo_Impuesto').val(item.data('nu_tipo_impuesto'));
      if ($('#txt-Ss_Impuesto').val() !== undefined)
        $('#txt-Ss_Impuesto').val(item.data('ss_impuesto'));
      if ($('#txt-nu_tipo_item').val() !== undefined)
        $('#txt-nu_tipo_item').val(item.data('nu_tipo_item'));
      if ($('#txt-No_Codigo_Interno').val() !== undefined)
        $('#txt-No_Codigo_Interno').val(item.data('no_codigo_interno'));
      if ($('#txt-ID_Impuesto_Icbper').val() !== undefined)
        $('#txt-ID_Impuesto_Icbper').val(item.data('id_impuesto_icbper'));
      if ($('#txt-Ss_Icbper').val() !== undefined)
        $('#txt-Ss_Icbper').val(item.data('ss_icbper'));
      if ($('#txt-no_variante_1').val() !== undefined)
        $('#txt-no_variante_1').val(item.data('no_variante_1'));
      if ($('#txt-no_valor_variante_1').val() !== undefined)
        $('#txt-no_valor_variante_1').val(item.data('no_valor_variante_1'));
      if ($('#txt-no_variante_2').val() !== undefined)
        $('#txt-no_variante_2').val(item.data('no_variante_2'));
      if ($('#txt-no_valor_variante_2').val() !== undefined)
        $('#txt-no_valor_variante_2').val(item.data('no_valor_variante_2'));
      if ($('#txt-no_variante_3').val() !== undefined)
        $('#txt-no_variante_3').val(item.data('no_variante_3'));
      if ($('#txt-no_valor_variante_3').val() !== undefined)
        $('#txt-no_valor_variante_3').val(item.data('no_valor_variante_3'));
      if ($('#txt-nu_activar_precio_x_mayor').val() !== undefined)
        $('#txt-nu_activar_precio_x_mayor').val(item.data('nu_activar_precio_x_mayor'));
    }
  });

  // Inicio / Escritorio para cargar modal con el cambio de version
  $(".aVersionSistema").click(function () {
    $('#modal-header-actualizacion_sistema').text('Nueva versión del sistema ' + $(this).data('numero_version_sistema'));
    $('.modal-actualizacion_sistema').modal('show');
  })

  // Ver como pagar a laesystems
  $(".btn-lae_pagos").click(function () {
    $('.modal-lae_pagos').modal('show');
  })

  // Ver como pagar a laesystems
  $(".btn-ocultar_menu_izquierdo").click(function () {
    /*
    alert($('#hidden-sDirectory').val());
    alert($('#hidden-sClass').val());
    alert($('#hidden-sMethod').val());
    */
    var arrParams = {
      'Nu_Setting_Panel_Menu_Izquierdo': $(this).data('nu_setting_panel_menu_izquierdo'),
    };
    $.post(base_url + 'HelperController/reloadUpdateUsuario', arrParams, function (response) {
      if (response.status=='success')
        window.location.href = base_url + $('#hidden-sDirectory').val() + $('#hidden-sClass').val() + '/' + $('#hidden-sMethod').val();
      else
        alert(response.message);
    }, 'JSON');

    //$('.modal-lae_pagos').modal('show');
  })

  // Ver como pagar a laesystems
  $(".btn-ver_facturas_pagos_lae").click(function () {
    var arrParams = {
      'ID_Empresa': $('#header-a-id_empresa').val(),
      'ID_Menu': 123,
    };
    //console.log(arrParams);
    $.post(base_url + 'HelperController/verificarAccesoMenuXGrupo', arrParams, function (response) {
      if (response.status == 'success')
        window.location.href = base_url + 'Ventas/FacturaVentaLaeController/listar';
      else
        alert(response.message);
    }, 'JSON');    
  })
})// /. document ready

function scrollToError( $sMetodo, $IdElemento ){
  $sMetodo.animate({
    scrollTop: $IdElemento.offset().top
  }, 'slow');
}


function validateNumber(){
  $( '.input-number' ).unbind();
  $( '.input-number' ).on('input', function () {
    this.value = this.value.replace(/[^0-9]/g,'');
  });
}

function validateDecimal(){
  $( '.input-decimal' ).unbind();
  $( '.input-decimal' ).on('input', function () {
    numero = parseFloat(this.value);
    if(!isNaN(numero)){
      this.value = this.value.replace(/[^0-9\.]/g,'');
      if (numero < 0)
        this.value = '';
    } else
      this.value = this.value.replace(/[^0-9\.]/g,'');
  });
}

function validateNumberOperation(){
  $( '.input-number_operacion' ).unbind();
  $( '.input-number_operacion' ).on('input', function () {
    numero = parseFloat(this.value);
    if(!isNaN(numero)){
      this.value = this.value.replace(/[^0-9]/g,'');
      if (numero < 0)
        this.value = '';
    } else
      this.value = this.value.replace(/[^0-9]/g,'');
  });
}

function validateCodigoBarra(){
  $( '.input-codigo_barra' ).on('input', function () {
    this.value = this.value.replace(/[^a-zA-Z0-9\-]/g,'');
  });
}

function ParseDate(fecha){
  var _FE = fecha.split('-');
  return _FE[2] + '/' + _FE[1] + '/' + _FE[0];
}

function ParseDateHour(fecha){
  var _FE = fecha.split('-');
  var _FEH = _FE[2].split(' ');
  return _FEH[0] + '/' + _FE[1] + '/' + _FE[0] + ' ' + _FEH[1];
}

function ParseDateString(fecha, tipo, caracter){
  if (tipo == 1) {// Caracter -> /
    var _FE = fecha.split(caracter);
    return _FE[2] + '-' + _FE[1] + '-' + _FE[0];
  } else if (tipo == 2) {// Caracter -> -
    var _FE = fecha.split(caracter);
    return _FE[2] + '/' + _FE[1] + '/' + _FE[0];
  } else if (tipo == 3) {// Caracter -> - y fecha y hora BD pero solo obtiene fecha
    var _FE = fecha.split(caracter);
    var _FEH = _FE[2].split(' ');
    return _FEH[0] + '/' + _FE[1] + '/' + _FE[0];
  } else if (tipo == 4) {// Caracter -> - y fecha y hora BD pero solo obtiene hora
    var _FE = fecha.split(caracter);
    var _FEH = _FE[2].split(' ');
    var _H = _FEH[1].split(':');
    return _H[0];
  } else if (tipo == 5) {// Caracter -> - y fecha y hora BD pero solo obtiene minuto
    var _FE = fecha.split(caracter);
    var _FEH = _FE[2].split(' ');
    var _M = _FEH[1].split(':');
    return _M[1];
  } else if (tipo == 6) {// Caracter -> (-) y formato de fecha BD (YYY-MM-DD)
    var _FE = fecha.split('-');
    return _FE[2] + '/' + _FE[1] + '/' + _FE[0];
  } else if (tipo == 7) {// Caracter -> (N tipos / - ) y fecha y hora BD pero solo obtiene hora:minuto:segundo
    var _FE = fecha.split(caracter);
    var _FEH = _FE[2].split(' ');
    var _H = _FEH[1].split(':');
    return _H[0] + ':' + _H[1] + ':' + _H[2];
  } else if (tipo == 8) {// Caracter -> - y DD/MM/YYYY HH:MM:SS
    var _FE = fecha.split(caracter);
    return _FE[0];
  }
}

function number_format(amount, decimals) {
  amount += ''; // por si pasan un numero en vez de un string
  amount = parseFloat(amount.replace(/[^0-9\.]/g, '')); // elimino cualquier cosa que no sea numero o punto

  decimals = decimals || 0; // por si la variable no fue fue pasada

  // si no es un numero o es igual a cero retorno el mismo cero
  if (isNaN(amount) || amount === 0) 
    return parseFloat(0).toFixed(decimals);

  // si es mayor o menor que cero retorno el valor formateado como numero
  amount = '' + amount.toFixed(decimals);

  var amount_parts = amount.split('.'), regexp = /(\d+)(\d{3})/;

  while (regexp.test(amount_parts[0]))
    amount_parts[0] = amount_parts[0].replace(regexp, '$1' + ',' + '$2');

  return amount_parts.join('.');
}

function existeUrl(url) {
  var http = new XMLHttpRequest();
  http.open('HEAD', url, false);
  http.send();
  return http.status!=404;
}

function formatoImpresionComandaCocina(Accion, ID_Pedido_Cabecera, url_print) {
  window.open(base_url + "Ventas/VentaController/generarComandaCocinaPDF/" + ID_Pedido_Cabecera, "_blank", "location=yes,top=80,left=800,width=760,height=550,scrollbars=yes,status=yes");
}

function formatoImpresionTicketPreCuenta(Accion, ID_Pedido_Cabecera, url_print) {
  if ($('#hidden-Nu_Tipo_Lenguaje_Impresion_Pos').val() == 2) {//PDF
    window.open(base_url + "Ventas/VentaController/generarPreCuentaPDF/" + ID_Pedido_Cabecera, "_blank", "location=yes,top=80,left=800,width=760,height=550,scrollbars=yes,status=yes");
  } else {
    if (Accion != 'imprimir') {
      $('.modal_ticket').modal('show');
      $('#modal-loader').modal('show');
    }

    url = base_url + 'ImprimirTicketController/formatoImpresionTicketPreCuenta';
    $.post(url, { ID_Pedido_Cabecera: ID_Pedido_Cabecera }, function (response) {
      $('#table-modal_ticket thead').empty();
      $('#table-modal_ticket tbody').empty();
      $('#table-modal_ticket tfoot').empty();

      var iCantidadRegistros = response['arrTicket'].length;
      var sTdOriginal3 = '4';
      var sTdOriginal2 = '3';

      $('#img-logo_punto_venta').hide();
      $('#img-logo_punto_venta_click').hide();

      if (iMostrarLogoTicketGlobal == 1) {
        $('#img-logo_punto_venta').show();
      }

      var p_title = '';
      p_title += '<strong>' + (response['arrTicket'][0].Nu_Documento_Identidad_Empresa.substr(0, 2) == '20' ? response['arrTicket'][0].No_Empresa : response['arrTicket'][0].No_Empresa_Comercial) + '</strong><br>'
      + response['arrTicket'][0].Txt_Direccion_Empresa + '<br>';

      if (response['arrTicket'][0].Txt_Direccion_Empresa != response['arrTicket'][0].Txt_Direccion_Almacen)
        p_title += response['arrTicket'][0].Txt_Direccion_Almacen + '<br>';

      if (response['arrTicket'][0].No_Dominio_Empresa != '') {
        p_title += response['arrTicket'][0].No_Dominio_Empresa + '<br>';
      }

      if (response['arrTicket'][0].Nu_Celular_Empresa != '' && response['arrTicket'][0].Nu_Telefono_Empresa != '') {
        p_title += response['arrTicket'][0].Nu_Celular_Empresa + ' - ' + response['arrTicket'][0].Nu_Telefono_Empresa + '<br>';
      } else if (response['arrTicket'][0].Nu_Celular_Empresa != '' && response['arrTicket'][0].Nu_Telefono_Empresa.length == 0) {
        p_title += response['arrTicket'][0].Nu_Celular_Empresa + '<br>';
      } else {
        p_title += response['arrTicket'][0].Nu_Telefono_Empresa + '<br>';
      }

      if (response['arrTicket'][0].Txt_Email_Empresa != '')
        p_title += response['arrTicket'][0].Txt_Email_Empresa + '<br>';

      p_title += '<br><strong> RUC: ' + response['arrTicket'][0].Nu_Documento_Identidad_Empresa + '</strong><br>'
      +'<strong>PRE CUENTA - ' + response['arrTicket'][0].ID_Pedido_Cabecera + '</strong><br><br>'
      +'<strong>' + response['arrTicket'][0].No_Mesa_Restaurante + '</strong><br><br>';

      $('#modal-body-p-title').html(p_title);

      var sCssFontSize = 'font-size: 12px; font-family: Arial, Helvetica, sans-serif;';
      var sFontFamiliy = 'Arial, Helvetica, sans-serif;';
      var iFontSize = '12px;';
      var sFontFamiliyDetailItem = 'Arial, Helvetica, sans-serif;';
      //var iFontSizeDetailItem = '15px;';
      var iFontSizeDetailItem = '12px;';

      var table_ticket = '';
      table_ticket +=
        "<thead>"
        + "<tr>"
          + "<td class='text-left' style='" + sCssFontSize + "' colspan='" + sTdOriginal2 + "'></td>"
        + "</tr>"
        + "<tr>"
          + "<td class='text-left' style='" + sCssFontSize + "'>" + response['arrTicket'][0].No_Tipo_Documento_Identidad_Breve + "</td>"
          + "<td class='text-left' style='" + sCssFontSize + "' colspan='" + sTdOriginal2 + "'>" + response['arrTicket'][0].Nu_Documento_Identidad + "</td>"
        + "</tr>"
        + "<tr>"
          + "<td class='text-left' style='" + sCssFontSize + "'>CLIENTE: </td>"
          + "<td class='text-left' style='" + sCssFontSize + "' colspan='" + sTdOriginal2 + "'>" + response['arrTicket'][0].No_Entidad + "</td>"
        + "</tr>"
        + "<tr>"
          + "<td class='text-left' style='" + sCssFontSize + "'>F. EMISIÓN: </td>"
          + "<td class='text-left' style='" + sCssFontSize + "' colspan='" + sTdOriginal2 + "'>" + ParseDateHour(response['arrTicket'][0].Fe_Emision_Hora) + "</td>"
        + "</tr>"
        +"<tr>"
          + "<td class='text-left' style='" + sCssFontSize + "'>CAJERO: </td>"
          + "<td class='text-left' style='" + sCssFontSize + "' colspan='" + sTdOriginal2 + "'>" + response['arrTicket'][0].No_Empleado + "</td>"
        + "</tr>"
        + "<tr>"
          + "<td class='text-left' style='" + sCssFontSize + "'>MOZO: </td>"
          + "<td class='text-left' style='" + sCssFontSize + "' colspan='" + sTdOriginal2 + "'>" + response['arrTicket'][0].No_Mesero + "</td>"
        + "</tr>"
      "</thead>";

      table_ticket +=
      "<tbody>"
        + "<tr>"
      + "<td colspan='" + sTdOriginal3 + "' class='text-left' style='font-size:" + iFontSize + "; font-family: " + sFontFamiliy + "; width: 20%; padding: 2px;'>DESCRIPCION</td>"
        + "</tr>"
        + "<tr>"
          + "<td class='text-left' style='font-size: " + iFontSize + " font-family: " + sFontFamiliy + " width: 3%; padding: 2px; border-top: 1px solid transparent !important; border-bottom: 1px solid black !important;'>CANT.</td>"
          + "<td class='text-left' style='font-size: " + iFontSize + " font-family: " + sFontFamiliy + " width: 3%; padding: 2px; border-top: 1px solid transparent !important; border-bottom: 1px solid black !important;'>DSCTO.</td>"
          + "<td class='text-center' style='font-size: " + iFontSize + " font-family: " + sFontFamiliy + " width: 5%; padding: 2px; border-top: 1px solid transparent !important; border-bottom: 1px solid black !important;'>PRECIO</td>"
          + "<td class='text-right' style='font-size: " + iFontSize + " font-family: " + sFontFamiliy + " width: 13%; padding: 2px; border-top: 1px solid transparent !important; border-bottom: 1px solid black !important;'>TOTAL</td>"
        + "</tr>";

        var Ss_SubTotal_Producto = 0.00;
        var sNombreBreveLaboratorio = '';
        var fCantidadItem = 0.00;
        var Ss_Total_Producto = 0.00;
        var fOperacionesGravadas = 0.00;
        var fIGV = 0;
        var fOperacionesExoneradas = 0.00;
        var fOperacionesInafectas = 0.00;

        var fTotalCO2 = 0.00;
        var fTotalGeneralCO2 = 0.00;
        var fTotalIcbper = 0.00;

        for (var i = 0; i < iCantidadRegistros; i++) {
          if (response['arrTicket'][i].ID_Impuesto_Icbper == "1")
            fTotalIcbper += parseFloat(response['arrTicket'][i].Ss_Total_Producto);
          fCantidadItem = parseFloat(response['arrTicket'][i].Qt_Producto);
          table_ticket +=
          "<tr>"
            + "<td colspan='" + sTdOriginal3 + "' class='text-left' style='font-size:" + iFontSizeDetailItem + " font-family: " + sFontFamiliyDetailItem + " padding: 0px;'>" + response['arrTicket'][i].No_Producto + (response['arrTicket'][i].Txt_Nota_Item != null ? ' ' + response['arrTicket'][i].Txt_Nota_Item : '') + "</td>"
          + "</tr>"
          + "<tr>"
            + "<td class='text-left' style='font-size:" + iFontSizeDetailItem + " font-family: " + sFontFamiliyDetailItem + " padding: 0px; border-top: 1px solid transparent;'>" + fCantidadItem + " </td>"
            + "<td class='text-right' style='font-size:" + iFontSizeDetailItem + " font-family: " + sFontFamiliyDetailItem + " padding: 0px; border-top: 1px solid transparent;'>" + (parseFloat(response['arrTicket'][i].Ss_Descuento_Producto) > 0.00 ? response['arrTicket'][i].Ss_Descuento_Producto : '') + "</td>"
            + "<td class='text-right' style='font-size:" + iFontSizeDetailItem + " font-family: " + sFontFamiliyDetailItem + " padding: 0px; border-top: 1px solid transparent;'>" + number_format(response['arrTicket'][i].ss_precio_unitario, 3, '.') + "</td>"
            + "<td class='text-right' style='font-size:" + iFontSizeDetailItem + " font-family: " + sFontFamiliyDetailItem + " padding: 0px; border-top: 1px solid transparent;'>" + number_format(response['arrTicket'][i].Ss_Total_Producto, 2, '.', ',') + "</td>"
          + "</tr>";
        }

        table_ticket +=
        +"</tbody>"
        + "<tfoot>"
          + "<tr>"
          + "<td style='" + sCssFontSize + "' class='text-left' colspan='" + sTdOriginal2 + "'>TOTAL</td>"
          + "<td style='" + sCssFontSize + "' class='text-right'>" + response['arrTicket'][0].No_Signo + " " + number_format(parseFloat(response['arrTicket'][0].Ss_Total) + fTotalIcbper, 2, '.', ',') + "</td>"
          + "</tr>";

          table_ticket +=
          + "<tr>"
          + "<td style='" + sCssFontSize + "' class='text-left' colspan='" + sTdOriginal3 + "'>SON: " + response['totalEnLetras'] + "</td>"
          + "</tr>";

          if (response['arrTicket'][0].Txt_Glosa_Global != '') {
            table_ticket +=
            +"<tr>"
            + "<td style='" + sCssFontSize + "' class='text-left' colspan='" + sTdOriginal3 + "'>NOTA: " + response['arrTicket'][0].Txt_Glosa_Global + "</td>"
            + "</tr>";
          }

          table_ticket +=
          + "<tr>"
          + "<td style='" + sCssFontSize + "' class='text-center' colspan='" + sTdOriginal3 + "'>&nbsp;</td>"
          + "</tr>"
          + "<tr>"
          + "<td style='" + sCssFontSize + "' class='text-center' colspan='" + sTdOriginal3 + "'>Emitido desde <strong>laesystems.com</strong></td>"
          + "</tr>"
        + "</tfoot>";

      $('#table-modal_ticket').html(table_ticket);

      $('#div-codigo_qr').hide();
      if (sTerminosCondicionesTicket != '')
        $('#modal-body-p-terminos_condiciones_ticket').html(sTerminosCondicionesTicket);

      if (Accion != 'imprimir')
        $('#modal-loader').modal('hide');

      if (Accion == 'imprimir')
        generarFormatoImpresion('div-ticket', '');
    }, 'JSON')
    .fail(function () {
      $('#modal-loader').modal('hide');
    });
  }
}

function formatoImpresionTicket(Accion, ID_Documento_Cabecera, url_print, url_api_pdf = ''){
  window.open(base_url + "Ventas/VentaController/generarRepresentacionInternaTicketPDF/" + ID_Documento_Cabecera, "_blank", "location=yes,top=80,left=800,width=760,height=550,scrollbars=yes,status=yes");
}

function formatoImpresionTicketGuia(Accion, ID_Documento_Cabecera, url_print){
  if ( Accion != 'imprimir' ) {
    $( '.modal_ticket' ).modal('show');
    $( '#modal-loader' ).modal('show');
  }
  
  url = base_url + 'ImprimirTicketController/formatoImpresionTicketGuia';
  $.post( url, {ID_Documento_Cabecera : ID_Documento_Cabecera}, function( response ) {
    console.log(response);

    $( '#table-modal_ticket thead' ).empty();
	  $( '#table-modal_ticket tbody' ).empty();
    $( '#table-modal_ticket tfoot' ).empty();
	  
    var iCantidadRegistros = response['arrTicket'].length;

    var sCssFontSize = 'font-size: 12px; font-family: Arial, Helvetica, sans-serif;';
    var iFontSize = '14px;';
    var sFontFamiliy = 'Arial, Helvetica, sans-serif;';
    var sTdOriginal3 = 4;
    var sTdOriginal2 = 3;

    $('#img-logo_punto_venta').hide();
    $('#img-logo_punto_venta_click').hide();
    
    if (response['arrTicket'][0].Nu_Logo_Empresa_Ticket == 1 ) {
      $('#img-logo_punto_venta').show();
    }

    var p_title = '';
    p_title += 
      '<strong>' + (response['arrTicket'][0].Nu_Documento_Identidad_Empresa.substr(0, 2) == '20' ? response['arrTicket'][0].No_Empresa : response['arrTicket'][0].No_Empresa_Comercial) + '</strong><br>'
    + response['arrTicket'][0].Txt_Direccion_Empresa + '<br>';

    if ( response['arrTicket'][0].Txt_Direccion_Empresa != response['arrTicket'][0].Txt_Direccion_Almacen ) {
      p_title += response['arrTicket'][0].Txt_Direccion_Almacen + '<br>';
    }

    if ( response['arrTicket'][0].Nu_Celular_Empresa != '' && response['arrTicket'][0].Nu_Telefono_Empresa != '' ) {
      p_title += response['arrTicket'][0].Nu_Celular_Empresa + ' - ' + response['arrTicket'][0].Nu_Telefono_Empresa + '<br>';
    } else if ( response['arrTicket'][0].Nu_Celular_Empresa != '' && response['arrTicket'][0].Nu_Telefono_Empresa.length == 0 ) {
      p_title += response['arrTicket'][0].Nu_Celular_Empresa + '<br>';
    } else {
      p_title += response['arrTicket'][0].Nu_Telefono_Empresa + '<br>';
    }

    if ( response['arrTicket'][0].Txt_Email_Empresa != '' ) {
      p_title += response['arrTicket'][0].Txt_Email_Empresa + '<br>';
    }
    
    var sDocumentoElectronico = response['arrTicket'][0].No_Tipo_Documento.toUpperCase() + (response['arrTicket'][0].ID_Serie_Documento.substr(0,1) == 'T' ? " ELECTRÓNICA" : "");
    p_title += 
    '<br><strong> RUC: ' + response['arrTicket'][0].Nu_Documento_Identidad_Empresa + '</strong><br>'
    + '<strong>' + sDocumentoElectronico + '</strong><br>'
    + '<strong>' + response['arrTicket'][0].ID_Serie_Documento + '-' + ('000000' + response['arrTicket'][0].ID_Numero_Documento).slice(-6) + '</strong><br>'
    + '<br>';
    $( '#modal-body-p-title' ).html(p_title);
      
    var table_ticket = '';
    table_ticket += 
    "<thead>"
      +"<tr>"
        +"<td class='text-left' style='" + sCssFontSize + "' colspan='" + sTdOriginal2 + "'></td>"
      +"</tr>"
      +"<tr>"
        +"<td class='text-left' style='" + sCssFontSize + "'>" + response['arrTicket'][0].No_Tipo_Documento_Identidad_Breve + "</td>"
        +"<td class='text-left' style='" + sCssFontSize + "' colspan='" + sTdOriginal2 + "'>" + response['arrTicket'][0].Nu_Documento_Identidad + "</td>"
      +"</tr>"
      +"<tr>"
        +"<td class='text-left' style='" + sCssFontSize + "'>CLIENTE: </td>"
        +"<td class='text-left' style='" + sCssFontSize + "' colspan='" + sTdOriginal2 + "'>" + response['arrTicket'][0].No_Entidad + "</td>"
      +"</tr>"
      +"<tr>"
        +"<td class='text-left' style='" + sCssFontSize + "'>F. EMISIÓN: </td>"
        +"<td class='text-left' style='" + sCssFontSize + "' colspan='" + sTdOriginal2 + "'>" + ParseDateHour(response['arrTicket'][0].Fe_Emision_Hora) + "</td>"
      +"</tr>";

      if (response['arrTicket'][0].Nu_Tipo_Recepcion == '6' && (response['arrTicket'][0].Nu_Celular_Entidad != '' || response['arrTicket'][0].Txt_Direccion_Entidad != '')) {// Negocio Delivery
        table_ticket +=
        +"<tr>"
          +"<td style='" + sCssFontSize + "' class='text-left' colspan='" + sTdOriginal3 + "'><b>DELIVERY</b></td>"
        +"</tr>";
        if (response['arrTicket'][0].Nu_Celular_Entidad != '') {
          table_ticket +=
          +"<tr>"
            +"<td style='" + sCssFontSize + "' class='text-left' colspan='" + sTdOriginal3 + "'>CELULAR: " + response['arrTicket'][0].Nu_Celular_Entidad + "</td>"
          +"</tr>";
        }
        
        table_ticket +=
        +"<tr>"
          +"<td style='" + sCssFontSize + "' class='text-left' colspan='" + sTdOriginal3 + "'>DIRECCIÓN: " + response['arrTicket'][0].Txt_Direccion_Entidad + "</td>"
        +"</tr>"
        +"<tr>"
          +"<td style='" + sCssFontSize + "' class='text-left' colspan='" + sTdOriginal3 + "'>TRANSPORTISTA: " + response['arrTicket'][0].No_Tipo_Documento_Identidad_Breve_Transporte + ": " + response['arrTicket'][0].Nu_Documento_Identidad_Transportista + " " + response['arrTicket'][0].No_Entidad_Transportista + "</td>"
        +"</tr>";

        if (response['arrTicket'][0].No_Placa != '') {
          table_ticket +=
          +"<tr>"
            +"<td style='" + sCssFontSize + "' class='text-left' colspan='" + sTdOriginal3 + "'>PLACA: " + response['arrTicket'][0].No_Placa + "</td>"
          +"</tr>";
        }
      }

      if (response['arrTicket'][0].Nu_Tipo_Recepcion == '7' && response['arrTicket'][0].Nu_Celular_Entidad != '') {// Recojo en tienda
        table_ticket +=
        +"<tr>"
          +"<td style='" + sCssFontSize + "' class='text-left' colspan='" + sTdOriginal3 + "'><b>RECOJO EN TIENDA</b></td>"
        +"</tr>"
        +"<tr>"
          +"<td style='" + sCssFontSize + "' class='text-left' colspan='" + sTdOriginal3 + "'>CELULAR: " + response['arrTicket'][0].Nu_Celular_Entidad + "</td>"
        +"</tr>";
      }
    table_ticket += 
    "</thead>";

    table_ticket +=
    "<tbody>"
      +"<tr>"
        +"<td class='text-left' colspan='" + sTdOriginal2 + "' style='font-size:" + iFontSize + " font-family: " + sFontFamiliy + " padding: 2px; border-top: 1px solid transparent !important; border-bottom: 1px solid black !important;'>ITEM</td>"
        +"<td class='text-right' style='font-size:" + iFontSize + " font-family: " + sFontFamiliy + " padding: 2px; border-top: 1px solid transparent !important; border-bottom: 1px solid black !important;'>CANT.</td>"
      +"</tr>";
    
    for (var i = 0; i < iCantidadRegistros; i++) {
      fCantidadItem = parseFloat(response['arrTicket'][i].Qt_Producto);
      table_ticket +=
      +"<tr>"
        +"<td style='" + sCssFontSize + " padding: 0px;' class='text-left' colspan='" + sTdOriginal2 + "'>" + response['arrTicket'][i].No_Producto + (response['arrTicket'][i].Txt_Nota_Item != null ? ' ' + response['arrTicket'][i].Txt_Nota_Item : '') + "</td>"
        +"<td style='" + sCssFontSize + " padding: 0px; border-top: 1px solid transparent;' class='text-right'>" + fCantidadItem + " " + response['arrTicket'][i].nu_codigo_unidad_medida_sunat + " </td>"
      +"</tr>";
    }

    table_ticket +=
    "</tbody>"
    +"<tfoot>"
      +"<tr>"
        +"<td style='" + sCssFontSize + "' class='text-left' colspan='" + sTdOriginal2 + "'>TOTAL</td>"
        +"<td style='" + sCssFontSize + "' class='text-right'>" + response['arrTicket'][0].No_Signo + " " + number_format( parseFloat(response['arrTicket'][0].Ss_Total), 2, '.', ',') + "</td>"
      +"</tr>";

    if (response['arrTicket'][0].No_Codigo_Sunat_FE_MP != '2' && parseFloat(response['arrTicket'][0].Ss_Vuelto) > 0.00) {
      table_ticket +=
      +"<tr>"
        +"<td style='" + sCssFontSize + "' class='text-left' colspan='" + sTdOriginal2 + "'>VUELTO</td>"
        +"<td style='" + sCssFontSize + "' class='text-right'>" + response['arrTicket'][0].No_Signo + " " + number_format(parseFloat(response['arrTicket'][0].Ss_Vuelto), 2, '.', ',') + "</td>"
      +"</tr>"
      +"<tr>"
        +"<td style='" + sCssFontSize + "' class='text-left' colspan='" + sTdOriginal2 + "'>PAGO DEL CLIENTE</td>"
        +"<td style='" + sCssFontSize + "' class='text-right'>" + response['arrTicket'][0].No_Signo + " " + number_format(parseFloat(response['arrTicket'][0].Ss_Total) + parseFloat(response['arrTicket'][0].Ss_Vuelto), 2, '.', ',') + "</td>"
      + "</tr>";
    }

    table_ticket +=
    +"<tr>"
      +"<td style='" + sCssFontSize + "' class='text-left' colspan='" + sTdOriginal3 + "'>MEDIO DE PAGO: " + response['arrTicket'][0].No_Medio_Pago + "</td>"
    +"</tr>";

    if ( response['arrTicket'][0].Nu_Tipo_Proveedor_FE != 3 ) {
      if ( response['arrTicket'][0].ID_Tipo_Documento!='2' && response['arrTicket'][0].Nu_Tipo_Proveedor_FE == 1 ) {
        table_ticket +=
        "<tr>"
        + "<td style='" + sCssFontSize + "' class='text-center' colspan='" + sTdOriginal3 + "'>Consulte su documento en <strong>laesystems.pse.pe/" + response['arrTicket'][0].Nu_Documento_Identidad + "</strong></td>"
        +"</tr>"
        +"<tr>"
          +"<td style='" + sCssFontSize + "' class='text-center' colspan='" + sTdOriginal3 + "'>Resumen: " + response['arrTicket'][0].Txt_Hash + "</td>"
        +"</tr>";
      } else if ( response['arrTicket'][0].ID_Tipo_Documento!='2' && response['arrTicket'][0].Nu_Tipo_Proveedor_FE == 2 ) {
        table_ticket +=
        "<tr>"
          +"<td style='" + sCssFontSize + "' class='text-center' colspan='" + sTdOriginal3 + "'>Representación impresa de la " + sDocumentoElectronico + ", visita <strong>laesystems.com</strong></td>"
        +"</tr>"
        +"<tr>"
          +"<td style='" + sCssFontSize + "' class='text-center' colspan='" + sTdOriginal3 + "'>Resumen: " + response['arrTicket'][0].Txt_Hash + "</td>"
        +"</tr>";
        if ( response['arrTicket'][0].Nu_Estado_Sistema == 0 ) {
          table_ticket +=
          "<tr>"
            +"<td style='" + sCssFontSize + "' class='text-center' colspan='" + sTdOriginal3 + "'>Representación Impresa de Documento Electrónico Generado En Una Versión de Pruebas. No tiene Validez!</td>"
          +"</tr>";
        }
      } // if - else formato ticket
    }

    table_ticket +=
      +"<tr>"
        +"<td style='" + sCssFontSize + "' class='text-center' colspan='" + sTdOriginal3 + "'>Emitido desde <strong>laesystems.com</strong></td>"
      + "</tr>";
    +"</tfoot>";

    $( '#table-modal_ticket' ).html(table_ticket);

    if ( sTerminosCondicionesTicket != '' )
      $('#modal-body-p-terminos_condiciones_ticket').html(sTerminosCondicionesTicket);

    $( '#div-codigo_qr' ).hide();
    $( '#div-codigo_qr' ).html('');
    if ( response['arrTicket'][0].ID_Tipo_Documento!="2" && response['arrTicket'][0].Txt_QR != null ) {
      var miCodigoQR = new QRCode("div-codigo_qr", {
        text: response['arrTicket'][0].Txt_QR,
        width: 80,
        height: 80
      });
      $( '#div-codigo_qr' ).show();
    }
   
    if ( Accion != 'imprimir' )
      $( '#modal-loader' ).modal('hide');

    if (Accion == 'imprimir')
      generarFormatoImpresion('div-ticket', '');
  }, 'JSON')
  .fail(function() {
    $( '#modal-loader' ).modal('hide');
  });
}

function formatoImpresionLiquidacionCaja(arrPost) {
  if (arrPost.sAccion == undefined) {
    arrPost = JSON.parse(arrPost);
  }

  if ($('#hidden-Nu_Tipo_Lenguaje_Impresion_Pos').val() == 2) {//PDF
    window.open(base_url + "Ventas/VentaController/generarArqueoPOSPDF/" + arrPost.iIdMatriculaEmpleado + '/' + arrPost.iIdEnlaceAperturaCaja + '/' + arrPost.iIdEnlaceCierreCaja, "_blank", "location=yes,top=80,left=800,width=760,height=550,scrollbars=yes,status=yes");
    if (arrPost.sUrlAperturaCaja !== undefined) {
      window.location = arrPost.sUrlAperturaCaja
    }    
  } else {
    if (arrPost.sAccion != 'imprimir') {
      $('.modal-liquidacion_caja').modal('show');

      $('#modal-loader').modal('show');
    }

    url = base_url + 'ImprimirLiquidacionCajaController/formatoImpresionLiquidacionCaja';
    $.post( url, arrPost, function( response ) {
      $( '#modal-table-ventas_x_familia' ).empty();
      $( '#modal-table-movimientos_caja' ).empty();
      $( '#modal-table-ventas_generales' ).empty();
      $('#modal-table-ventas_x_descuento').empty();
      $('#modal-table-ventas_x_gratuita_regalo').empty();
      $( '#modal-table-ventas_totales' ).empty();

      if ( response.sStatus == 'success' ) {
        var fTotal=0.00;
        var fCantidadTotal=0.00;

        var iCantidadRegistrosVentasxFamilia = response.arrData.VentasxFamilia.length;
        var arrDataVentasxFamilia = response.arrData.VentasxFamilia;
        var sHtmlTableVentasxFamilia = '';
        sHtmlTableVentasxFamilia +=
        '<thead>'+
          '<tr>'+
            '<td class="text-left">&nbsp;</td>'+
          '</tr>'+
          '<tr>'+
            '<td class="text-left">F. Apertura: </td>'+
            '<td class="text-left" colspan="3">' + ParseDateHour(response.arrData.TotalesLiquidacionCaja[0].Fe_Apertura) + '</td>'+
          '</tr>'+
          '<tr>'+
            '<td class="text-left">F. Cierre: </td>'+
            '<td class="text-left" colspan="3">' + ParseDateHour(response.arrData.TotalesLiquidacionCaja[0].Fe_Cierre) + '</td>'+
          '</tr>'+
          '<tr>'+
            '<td class="text-left">Caja: </td>'+
            '<td class="text-left" colspan="3">' + response.arrData.TotalesLiquidacionCaja[0].ID_POS + '</td>'+
          '</tr>'+
          '<tr>'+
            '<td class="text-left">Cajero: </td>'+
            '<td class="text-left" colspan="3">' + response.arrData.TotalesLiquidacionCaja[0].No_Entidad + '</td>'+
          '</tr>'+
          '<tr>'+
            '<td class="text-left">&nbsp;</td>'+
          '</tr>'+
          '</thead>'+
          '<thead>'+
            '<tr>'+
              '<th class="text-center" colspan="4">Ventas por ' + (iFormatoTicketLiquidacionCajaGlobal == 1 ? 'Familia' : 'Producto') + '</th>'+
            '</tr>'+
            '<tr>'+
              '<th class="text-left">' + (iFormatoTicketLiquidacionCajaGlobal == 1 ? 'Categoría' : 'Producto' ) + '</th>'+
              '<th class="text-right">Cantidad</th>'+
              '<th class="text-right">M</th>'+
              '<th class="text-right">Total</th>'+
            '</tr>'+
          '</thead>';
        if ( iCantidadRegistrosVentasxFamilia>0 ) {
          fCantidadTotal = 0.00;
          fTotal=0.00;
          sHtmlTableVentasxFamilia +=
          '<tbody>';
          for (var i = 0; i < iCantidadRegistrosVentasxFamilia; i++) {//Ventas x Familia
            sHtmlTableVentasxFamilia +=
            '<tr>'+
              '<td class="text-left">' + arrDataVentasxFamilia[i].No_Familia_Item + '</td>'+
              '<td class="text-right">' + arrDataVentasxFamilia[i].Qt_Producto + '</td>'+
              '<td class="text-right">' + arrDataVentasxFamilia[i].No_Signo + '</td>'+
              '<td class="text-right">' + number_format(arrDataVentasxFamilia[i].Ss_Total, 2, '.', ',') + '</td>'+
            '</tr>';
            fCantidadTotal+=parseFloat(arrDataVentasxFamilia[i].Qt_Producto);
            fTotal+=parseFloat(arrDataVentasxFamilia[i].Ss_Total);
          }// ./ for
          sHtmlTableVentasxFamilia +=
          '</tbody>'+
          '<tfoot>'+
            '<tr>'+
              '<th class="text-right">Total</th>'+
              '<th class="text-right">' + number_format(fCantidadTotal, 3, '.', ',') + '</th>' +
              '<th class="text-right">S/</th>' +
              '<th class="text-right">' + number_format(fTotal, 2, '.', ',') + '</th>'+
            '</tr>'+
          '</tfoot>';
        } else {
          sHtmlTableVentasxFamilia += 
          '<tr>'+
            '<td class="text-center" colspan="4">No hay registros</td>'+
          '</tr>';
        }// ./ if - else table ventas por familia

        $( '#modal-table-ventas_x_familia' ).append( sHtmlTableVentasxFamilia );

        var iCantidadRegistrosMovimientosCaja = response.arrData.MovimientosCaja.length;
        var arrDataMovimientosCaja = response.arrData.MovimientosCaja;
        var sHtmlTableMovimientosCaja = '';

        sHtmlTableMovimientosCaja +=
        '<table id="modal-table-movimientos_caja" class="table table-hover">'+
          '<thead>'+
            '<tr>'+
              '<th class="text-center" colspan="4">Movimientos de Caja</th>'+
            '</tr>'+
            '<tr>'+
              '<th class="text-left" colspan="2">Movimiento</th>'+
              '<th class="text-right">M</th>'+
              '<th class="text-right">Total</th>'+
            '</tr>'+
          '</thead>';        
        if ( iCantidadRegistrosMovimientosCaja>0 ) {
          fTotal=0.00;
          sHtmlTableMovimientosCaja +=
          '<tbody>';
          for (var i = 0; i < iCantidadRegistrosMovimientosCaja; i++) {//Movimientos de Caja
            fTotal+= (arrDataMovimientosCaja[i].Nu_Tipo != '6' ? parseFloat(arrDataMovimientosCaja[i].Ss_Total) : -parseFloat(arrDataMovimientosCaja[i].Ss_Total));
            sHtmlTableMovimientosCaja +=
            '<tr>'+
              '<td class="text-left" colspan="2">' + arrDataMovimientosCaja[i].No_Tipo_Operacion_Caja + '</td>'+
              '<td class="text-right">' + arrDataMovimientosCaja[i].No_Signo + '</td>'+
              '<td class="text-right">' + number_format(arrDataMovimientosCaja[i].Ss_Total, 2, '.', ',') + '(' + (arrDataMovimientosCaja[i].Nu_Tipo != '6' ? '+' : '-') + ')</td>'+
            '</tr>';
          }// ./ for
          sHtmlTableMovimientosCaja +=
          '</tbody>'+
          '<tfoot>'+
            '<tr>'+
              '<th class="text-right" colspan="2">Total</th>' +
              '<th class="text-right">S/</th>' +
              '<th class="text-right">' + number_format(fTotal, 2, '.', ',') + '</th>' +
            '</tr>'+
          '</tfoot>';
        } else {
          sHtmlTableMovimientosCaja += 
          '<tr>'+
            '<td class="text-center" colspan="4">No hay registros</td>'+
          '</tr>';
        }// ./ if - else table Movimientos de Caja

        sHtmlTableMovimientosCaja += '</table>';
        $( '#modal-table-movimientos_caja' ).append( sHtmlTableMovimientosCaja );

        var iCantidadRegistrosVentasGenerales = response.arrData.VentasGenerales.length;
        var arrDataVentasGenerales = response.arrData.VentasGenerales;
        var iCantidadRegistrosVentasCreditoGenerales = response.arrData.VentasGeneralesCreditoCreditoNoSuma.length;
        var arrDataVentasCreditoGenerales = response.arrData.VentasGeneralesCreditoCreditoNoSuma;
        var sHtmlTableVentasGenerales = '';

        sHtmlTableVentasGenerales +=
        '<table id="modal-table-ventas_generales" class="table table-hover">'+
          '<thead>'+
            '<tr>'+
              '<th class="text-center" colspan="4">Ventas Generales</th>'+
              '</tr>'+
              '<tr>'+
              '<th class="text-center" colspan="2">Tipo</th>'+
              '<th class="text-center">M</th>'+
              '<th class="text-right">Total</th>'+
            '</tr>'+
          '</thead>';
        if ( iCantidadRegistrosVentasGenerales>0 ) {
          fTotal=0.00;
          sHtmlTableVentasGenerales +=
          '<tbody>';
          for (var i = 0; i < iCantidadRegistrosVentasGenerales; i++) {//Ventas Generales
            if (arrDataVentasGenerales[i].No_Medio_Pago !== undefined ) {
              sHtmlTableVentasGenerales +=
              '<tr>'+
                '<td class="text-left" colspan="2">' + arrDataVentasGenerales[i].No_Medio_Pago + '</td>'+
                '<td class="text-center">' + arrDataVentasGenerales[i].No_Signo + '</td>'+
                '<td class="text-right">' + number_format(arrDataVentasGenerales[i].Ss_Total, 2, '.', ',') + ' ' + (arrDataVentasGenerales[i].Nu_Tipo_Caja == '0' ? '(+)' : '') + '</td>'+
              '</tr>';
              fTotal+=parseFloat(arrDataVentasGenerales[i].Ss_Total);
            }
          }// ./ for
          sHtmlTableVentasGenerales +=
            '<tr>'+
              '<th class="text-right" colspan="2">Total</th>' +
              '<th class="text-right">S/</th>' +
              '<th class="text-right">' + number_format(fTotal, 2, '.', ',') + '</th>' +
            '</tr>'+
          '</tbody>';
          //if (iIdTipoRubroEmpresaGlobal != 3) {//Lavandería VAPI
            sHtmlTableVentasGenerales +=
              '<thead>' +
              '<tr>' +
              '<th class="text-center" colspan="4">Ventas al Crédito</th>' +
              '</tr>' +
              '<tr>' +
                '<th class="text-left" colspan="2">Tipo</th>' +
                '<th class="text-center">M</th>' +
                '<th class="text-right">Total</th>' +
              '</tr>' +
              '</thead>';
              if (iCantidadRegistrosVentasCreditoGenerales > 0) {
                fTotalCredito = 0.00;
                sHtmlTableVentasGenerales +=
                '<tbody>';
                for (var i = 0; i < iCantidadRegistrosVentasCreditoGenerales; i++) {//Ventas Generales
                  sHtmlTableVentasGenerales +=
                  '<tr>' +
                    '<td class="text-left" colspan="2">' + arrDataVentasCreditoGenerales[i].No_Medio_Pago + '</td>' +
                    '<td class="text-center">' + arrDataVentasCreditoGenerales[i].No_Signo + '</td>' +
                    '<td class="text-right">' + number_format(arrDataVentasCreditoGenerales[i].Ss_Total, 2, '.', ',') + ' ' + (arrDataVentasCreditoGenerales[i].Nu_Tipo_Caja == '0' ? '(+)' : '') + '</td>' +
                  '</tr>';
                  fTotalCredito += parseFloat(arrDataVentasCreditoGenerales[i].Ss_Total);
                }// ./ for
                sHtmlTableVentasGenerales +=
                  '<tr>' +
                    '<th class="text-right" colspan="2">Total</th>' +
                    '<th class="text-right">S/</th>' +
                    '<th class="text-right">' + number_format(fTotalCredito, 2, '.', ',') + '</th>' +
                  '</tr>' +
                '</tbody>';
              } else {
                sHtmlTableVentasGenerales +=
                '<tr>' +
                  '<td class="text-center" colspan="4">No hay registros</td>' +
                '</tr>';
              }
          //} // ./ if rubro
        } else {
          sHtmlTableVentasGenerales += 
          '<tr>'+
            '<td class="text-center" colspan="4">No hay registros</td>'+
          '</tr>';
        }// ./ if - else table Ventas Generales

        sHtmlTableVentasGenerales += '</table>';
        $( '#modal-table-ventas_generales' ).append( sHtmlTableVentasGenerales );

        var iCantidadRegistrosVentasxDescuento = response.arrData.VentasxDescuento.length;
        var arrDataVentasxDescuento = response.arrData.VentasxDescuento;
        var sHtmlTableVentasxDescuento = '';
        sHtmlTableVentasxDescuento +=
        '<table id="modal-table-ventas_generales" class="table table-hover">' +
          '<thead>' +
          '<tr>' +
          '<th class="text-center" colspan="3">Ventas por Descuento</th>' +
          '</tr>' +
          '<tr>' +
            '<th class="text-right">Nombre</th>' +
            '<th class="text-right">M</th>' +
            '<th class="text-right">Total</th>' +
          '</tr>' +
          '</thead>';
        if (iCantidadRegistrosVentasxDescuento > 0) {
          fTotal = 0.00;
          sHtmlTableVentasxDescuento +=
            '<tbody>';
          for (var i = 0; i < iCantidadRegistrosVentasxDescuento; i++) {//Ventas x descuento
            sHtmlTableVentasxDescuento +=
              '<tr>' +
                '<td class="text-right">Descuento</td>' +
                '<td class="text-right">' + arrDataVentasxDescuento[i].No_Signo + '</td>' +
                '<td class="text-right">' + number_format(arrDataVentasxDescuento[i].Ss_Total, 2, '.', ',') + '</td>' +
              '</tr>';
            fTotal += parseFloat(arrDataVentasxDescuento[i].Ss_Total);
          }// ./ for
          sHtmlTableVentasxDescuento +=
            '</tbody>' +
            '<tfoot>' +
            '<tr>' +
              '<th class="text-right">Total</th>' +
              '<th class="text-right">S/</th>' +
              '<th class="text-right">' + number_format(fTotal, 2, '.', ',') + '</th>' +
            '</tr>' +
            '</tfoot>';
        } else {
          sHtmlTableVentasxDescuento +=
            '<tr>' +
            '<td class="text-center" colspan="3">No hay registros</td>' +
            '</tr>';
        }// ./ if - else table ventas por descuento
        
        sHtmlTableVentasxDescuento += '</table>';
        $('#modal-table-ventas_x_descuento').append(sHtmlTableVentasxDescuento);

        var iCantidadRegistrosVentasxRegaloGratuita = response.arrData.VentasxRegaloGratuita.length;
        var arrDataVentasxRegaloGratuita = response.arrData.VentasxRegaloGratuita;
        var sHtmlTableVentasxRegaloGratuita = '';
        sHtmlTableVentasxRegaloGratuita +=
        '<table id="modal-table-ventas_generales" class="table table-hover">' +
          '<thead>' +
          '<tr>' +
          '<th class="text-center" colspan="3">Ventas por GRATUITAS o REGALOS</th>' +
          '</tr>' +
          '<tr>' +
            '<th class="text-right">Nombre</th>' +
            '<th class="text-right">M</th>' +
            '<th class="text-right">Total</th>' +
          '</tr>' +
          '</thead>';
        if (iCantidadRegistrosVentasxRegaloGratuita > 0) {
          fTotal = 0.00;
          sHtmlTableVentasxRegaloGratuita +=
            '<tbody>';
          for (var i = 0; i < iCantidadRegistrosVentasxRegaloGratuita; i++) {//Ventas x GRATUITAS o REGALOS
            sHtmlTableVentasxRegaloGratuita +=
              '<tr>' +
                '<td class="text-right">Gratuitas</td>' +
                '<td class="text-right">' + arrDataVentasxRegaloGratuita[i].No_Signo + '</td>' +
                '<td class="text-right">' + number_format(arrDataVentasxRegaloGratuita[i].Ss_Total, 2, '.', ',') + '</td>' +
              '</tr>';
            fTotal += parseFloat(arrDataVentasxRegaloGratuita[i].Ss_Total);
          }// ./ for
          sHtmlTableVentasxRegaloGratuita +=
            '</tbody>' +
            '<tfoot>' +
            '<tr>' +
              '<th class="text-right">Total</th>' +
              '<th class="text-right">S/</th>' +
              '<th class="text-right">' + number_format(fTotal, 2, '.', ',') + '</th>' +
            '</tr>' +
            '</tfoot>';
        } else {
          sHtmlTableVentasxRegaloGratuita +=
            '<tr>' +
            '<td class="text-center" colspan="3">No hay registros</td>' +
            '</tr>';
        }// ./ if - else table ventas por GRATUITAS o REGALOS

        sHtmlTableVentasxRegaloGratuita += '</table>';
        $('#modal-table-ventas_x_gratuita_regalo').append(sHtmlTableVentasxRegaloGratuita);

        var sHtmlTotales = '';
        // Totales a liquidar, depositar y diferencia
        var fDiferencia = (  parseFloat(response.arrData.TotalesLiquidacionCaja[0].Ss_Total) - parseFloat(response.arrData.TotalesLiquidacionCaja[0].Ss_Expectativa) );
        sHtmlTotales +=
        '<tbody>'+
          '<tr>'+
            '<td class="text-left">&nbsp;</td>'+
          '</tr>'+
          '<tr>'+
            '<th class="text-right" colspan="2">Total a Liquidar</th>'+
            '<th class="text-right" colspan="2">S/ ' + number_format(response.arrData.TotalesLiquidacionCaja[0].Ss_Expectativa, 2, '.', ',') + '</td>'+
          '</tr>'+
          '<tr>'+
            '<th class="text-right" colspan="2">Total a Depositado</th>'+
            '<th class="text-right" colspan="2">S/ ' + number_format(response.arrData.TotalesLiquidacionCaja[0].Ss_Total, 2, '.', ',') + '</td>'+
          '</tr>'+
          '<tr>'+
            '<th class="text-right" colspan="2">Diferencia</th>'+
            '<th class="text-right" colspan="2">S/ ' + number_format(fDiferencia, 2, '.', ',') + '</td>'+
          '</tr>'+
        '</tbody>';

        $( '#modal-table-ventas_totales' ).append( sHtmlTotales );

        // Totales a liquidar, depositar y diferencia
        var fDiferencia = (  parseFloat(response.arrData.TotalesLiquidacionCaja[0].Ss_Total) - parseFloat(response.arrData.TotalesLiquidacionCaja[0].Ss_Expectativa) );
        $('#label-ss_liquidado').text('S/ ' + number_format(response.arrData.TotalesLiquidacionCaja[0].Ss_Expectativa, 2, '.', ',') );
        $('#label-ss_depositado').text('S/ ' + number_format(response.arrData.TotalesLiquidacionCaja[0].Ss_Total, 2, '.', ',') );
        $('#label-ss_diferencia').text('S/ ' + number_format(fDiferencia, 2, '.', ',') );
      } else {
        $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
        $( '#modal-message' ).modal('show');
        $( '.modal-message' ).addClass( 'modal-' + response.sStatus );
        $( '.modal-title-message' ).text( response.sMessage );
        setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
      }
      $( '#modal-loader' ).modal('hide');
    
      if (arrPost.sAccion == 'imprimir') {
        generarFormatoImpresion('modal-body-div-liquidacion_caja', arrPost);
      }
    }, 'JSON')
    .fail(function() {
      $( '#modal-loader' ).modal('hide');
    });
  }// if else html o pdf
}

function generarFormatoImpresion(sIdFormatoImpresion, arrPost){
  winPrintSunat = window.open("", "MsgWindow", "top=80,left=800,width=550,height=550");
  //winPrintSunat.document.open();
	printContentsSunat = document.getElementById(sIdFormatoImpresion).innerHTML;
  winPrintSunat.document.write(printContentsSunat);
	winPrintSunat.document.close();
	winPrintSunat.focus();
	winPrintSunat.print();
  winPrintSunat.close();
  
  if (arrPost.sUrlAperturaCaja !== undefined ){
    window.location = arrPost.sUrlAperturaCaja
  }
}

function AjaxPopupModal(id, title, url, params){
	$("#" + id).remove();
  $("body").append(
  '<div id="' + id + '" class="modal fade" role="dialog">'
    + '<div class="modal-dialog">' 
      + '<div class="modal-content">'
        + '<div class="modal-header">'
          + '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span class="close_new" aria-hidden="true">&times;</span></button>'
          + '<h4 class="panel-heading text-center"><b>' + title + '</b></h4>'
        + '</div>'
        + '<div class="modal-body">'
        + '</div>'
      + '</div>'
    + '</div>'
  + '</div>'
  );
  
  $("#" + id).modal();
  // Cargando
  $( "#modal-loader" ).modal( "show" );
  $.post(base_url + url, params, function(r){
    $("#" + id).find('.modal-body').html(r);
    $( "#modal-loader" ).modal( "hide" );
  });
}

function validateStockNow(event){
  if ( iValidarStockGlobal == 1 ) {// 1 = Validar stock
    var iIdItem = event.target.dataset.id_item, fCantidadActualCliente = event.target.value, fCantidadActualEmpresa = 0.00;
    var arrParams = {
      iIdAlmacen : document.getElementById("cbo-almacen").value,
      iIdItem : iIdItem,
    };
    $.post( base_url + 'HelperController/validateStockNow', arrParams, function( response ){
      fCantidadActualCliente = parseFloat(fCantidadActualCliente);
      fCantidadActualEmpresa = parseFloat(response.Qt_Producto);
      if ( fCantidadActualCliente > fCantidadActualEmpresa ) {
        alert( 'La cantidad ingresada: ' + fCantidadActualCliente + ' es mayor al stock actual: ' + fCantidadActualEmpresa );
      }
    }, 'JSON')
  }
}

function validarNumeroCelular(celular, div){
  var caract = new RegExp(/^([0-9\s]{11})+$/);
  /*
  if (caract.test(celular) == false){
    $(div).hide().removeClass('hide').slideDown('fast');
    return false;
  } else{
    $(div).hide().addClass('hide').slideDown('slow');
    return true;
  }
  */
}

function caracteresCorreoValido(email, div){
  var caract = new RegExp(/^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/);
  if (caract.test(email) == false){
    $(div).hide().removeClass('hide').slideDown('fast');
    return false;
  }else{
    $(div).hide().addClass('hide').slideDown('slow');
    return true;
  }
}

function caracteresDNIRUCValido(sValue) {
  var caract = new RegExp(/^([0-9])+$/);
  if (caract.test(sValue) == false) {
    return false;
  } else {
    return true;
  }
}

function sumaFecha(d, fecha){
  var Fecha = new Date();
  var sFecha = fecha || (Fecha.getDate() + "/" + (Fecha.getMonth() +1) + "/" + Fecha.getFullYear());
  var sep = sFecha.indexOf('/') != -1 ? '/' : '-';
  var aFecha = sFecha.split(sep);
  var fecha = aFecha[2]+'/'+aFecha[1]+'/'+aFecha[0];
  fecha= new Date(fecha);
  fecha.setDate(fecha.getDate()+parseInt(d));
  var anno=fecha.getFullYear();
  var mes= fecha.getMonth()+1;
  var dia= fecha.getDate();
  mes = (mes < 10) ? ("0" + mes) : mes;
  dia = (dia < 10) ? ("0" + dia) : dia;
  var fechaFinal = dia+sep+mes+sep+anno;
  return (fechaFinal);
}

function imageExists(image_url) {
  var http = new XMLHttpRequest();

  http.open('HEAD', image_url, false);
  http.send();
  return http.status != 404;
}

function getAlmacenesSession(arrParams){
  $.post(base_url + 'HelperController/getAlmacenesSession', arrParams, function (responseAlmacenSession) {
    if (responseAlmacenSession.status=='success'){
      window.location.href = base_url + $('#hidden-sDirectory').val() + $('#hidden-sClass').val() + '/' + $('#hidden-sMethod').val();
    } else {
      alert(responseAlmacenSession.message);
    }
  }, 'JSON');
}

function caracteresValidosAutocomplete(msg) {
  // Recorrer todos los caracteres
  search_global_autocomplete.forEach((char, index) => {
    // Remplazar cada caracter en la cadena
    msg = msg.replaceAll(char, replace_global_autocomplete[index]);
  });
  return msg;
}

function caracteresValidosWhatsApp(msg) {
  // Recorrer todos los caracteres
  search_whatsapp.forEach((char, index) => {
    // Remplazar cada caracter en la cadena
    msg = msg.replaceAll(char, replace_whatsapp[index]);
  });
  return msg;
}

function copyText() {
  // Get the text field
  var copyText = document.getElementById("span-url_tienda");
  var sUrlTiendaCompartir = 'https://' + copyText.textContent;
  sUrlTiendaCompartir = sUrlTiendaCompartir.trim();

  navigator.clipboard.writeText(sUrlTiendaCompartir).then(function() {
    console.log('Async: Copying to clipboard was successful!' + sUrlTiendaCompartir);
    //alert( 'Link copiado ' + sUrlTiendaCompartir );
    alert( 'Link copiado' );
  }, function(err) {
    console.error('Async: Could not copy text: ', err);
  });
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

