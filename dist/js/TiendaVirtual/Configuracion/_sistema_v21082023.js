var url;
var table_sistema;

$(function () {
  $('#btn-agregar_integraciones').click(function () {
    if ($(this).data('agregar_integraciones') == 1) {
      //setter
      $('#btn-agregar_integraciones').data('agregar_integraciones', 0);
    } else {
      $('#btn-agregar_integraciones').data('agregar_integraciones', 1);
    }

    if ($(this).data('agregar_integraciones') == 1) {
      $('.div-agregar_integraciones').css("display", "");
    } else {
      $('.div-agregar_integraciones').css("display", "none");
    }
  })

  $('#btn-agregar_paginas').click(function () {
    if ($(this).data('agregar_paginas') == 1) {
      //setter
      $('#btn-agregar_paginas').data('agregar_paginas', 0);
    } else {
      $('#btn-agregar_paginas').data('agregar_paginas', 1);
    }

    if ($(this).data('agregar_paginas') == 1) {
      $('.div-agregar_paginas').css("display", "");
    } else {
      $('.div-agregar_paginas').css("display", "none");
    }
  })

  //PALETA DE COLORES
  jQuery.fn.pickify = function() {
    return this.each(function() {
      $picker = $(this);
      //console.log('picker this >');
      //console.log($picker);
      $icon = $picker.children('.icon');
      $input = $picker.children('input.No_Html_Color_Lae_Shop');
      $board = $picker.children('.board');
      $choice = $board.children();
      $rainbow = $picker.children('.rainbow');
      
      //SET DATA
      var colors = $picker.attr('data-hsv').split(',');
      $picker.data('hsv', {h: colors[0], s: colors[1], v: colors[2]});
      var hex = '#'+rgb2hex(hsv2rgb({h: colors[0], s: colors[1], v: colors[2]}));
      $input.val(hex);
      $icon.css('background-color', '#6350c7');
      $board.css('background-color', '#6350c7');
      //$board.css('background-color', 'rgb(99, 80, 199)');

      // making things happen
      $rainbow.slider({
        value: colors[0],
        min: 0,
        max: 360,
        slide: function(event, ui) { changeHue(ui.value) },
        stop: function() {changeColour($picker.data('hsv'), true)}
      });

      $choice.draggable({
        containment: '.board',
        cursor: 'crosshair',
        create: function() {$choice.css({'left': colors[1]*1.8, 'top': 180-colors[2]*1.8});},
        drag: function(event, ui) {changeSatVal(ui.position.left, ui.position.top)},
        stop: function() {changeColour($picker.data('hsv'), true)}
      });

      $board.on('click', function(e) {
        var left = e.pageX-$board.offset().left;
        var top = e.pageY-$board.offset().top;
        $choice.css({'left': left, 'top': top});
        changeSatVal(left, top);
        changeColour($picker.data('hsv'), true);
      });
      
      // drag var actions
      function changeHue(hue) {
        $board.css('background-color', 'hsl('+hue+',100%,50%)');
        var hsv = $picker.data('hsv');
        hsv.h = hue;
        changeColour(hsv);
      }

      function changeSatVal(sat,val) {
        sat = Math.floor(sat/1.8);
        val = Math.floor(100-val/1.8);
        var hsv = $picker.data('hsv');
        hsv.s = sat;
        hsv.v = val;
        changeColour(hsv);
      }
      
      // changing the colours
      function changeColour(hsv, restyle, hex) {
        $('[name="No_Html_Color_HSV_Lae_Shop"]').val(hsv.h+','+hsv.s+','+hsv.v);
        var rgb = hsv2rgb(hsv);
        if (!hex) {var hex = rgb2hex(rgb)}
        $picker.data('hsv', hsv).data('hex', hex).data('rgb', rgb);
        $icon.css('background-color', '#'+hex);
        $input.val('#'+hex);
        if (restyle) {
          changeStyle(rgb);
        }
      }

      function changeStyle(rgb) {
        var rgb = 'rgb('+rgb.r+','+rgb.g+','+rgb.b+')';
      }
      
      // input change
      $input.keyup(function(e) {
        if (e.which != (37||39)) {
          inputChange($input.val());
        }
      });

      function inputChange(val) {
        var hex = val.replace(/[^A-F0-9]/ig, '');
        if (hex.length > 6) {
          hex = hex.slice(0,6);
        }
        $input.val('#' + hex);
        if (hex.length == 6) {
          inputColour(hex);
        }
      }

      function inputColour(hex) {
        var hsv = hex2hsv(hex);
        $icon.css('background-color', '#'+hex);
        $choice.css({
          left: hsv.s*1.8,
          top: 180-hsv.v*1.8
        });
        $rainbow.children().css('left', hsv.h/3.6+'%');
        $board.css('background-color', 'hsl('+hue+',100%,50%)');
        changeColour(hsv, true, hex);
      }
      
      function hex2hsv(hex) {
        var r = parseInt(hex.substring(0,2),16)/255;
        var g = parseInt(hex.substring(2,4),16)/255;
        var b = parseInt(hex.substring(4,6),16)/255;
        var max = Math.max.apply(Math, [r,g,b]);
        var min = Math.min.apply(Math, [r,g,b]);
        var chr = max-min;
        hue = 0;
        val = max;
        sat = 0;
        if (val > 0) {
          sat = chr/val;
          if (sat > 0) {
            if (r == max) {
              hue = 60*(((g-min)-(b-min))/chr);
              if (hue < 0) {hue += 360;}
            } else if (g == max) { 
              hue = 120+60*(((b-min)-(r-min))/chr); 
            } else if (b == max) { 
              hue = 250+60*(((r-min)-(g-min))/chr); 
            }
          } 
        }
        return {h: hue, s: Math.round(sat*100), v: Math.round(val*100)}
      }

      function hsv2rgb(hsv) {
        h = hsv.h;
        s = hsv.s;
        v = hsv.v;
        var r, g, b;
        var i;
        var f, p, q, t;
        h = Math.max(0, Math.min(360, h));
        s = Math.max(0, Math.min(100, s));
        v = Math.max(0, Math.min(100, v));
        s /= 100;
        v /= 100;
        if(s == 0) {
          r = g = b = v;
          return {r: Math.round(r*255), g: Math.round(g*255), b: Math.round(b*255)};
        }
        h /= 60;
        i = Math.floor(h);
        f = h - i; // factorial part of h
        p = v * (1 - s);
        q = v * (1 - s * f);
        t = v * (1 - s * (1 - f));
        switch(i) {
          case 0: r = v; g = t; b = p; break;
          case 1: r = q; g = v; b = p; break; 
          case 2: r = p; g = v; b = t; break; 
          case 3: r = p; g = q; b = v; break; 
          case 4: r = t; g = p; b = v; break; 
          default: r = v; g = p; b = q;
          }
        return {r: Math.round(r*255), g: Math.round(g*255), b: Math.round(b*255)};
      }

      function rgb2hex(rgb) {
        function hex(x) {
          return ("0" + parseInt(x).toString(16)).slice(-2);
        }
        return hex(rgb.r) + hex(rgb.g) + hex(rgb.b);
      }
    });
  };
  
  $('.picker').pickify();
  //FIN DE PALETA DE COLORES

  $('.select2').select2();
  $('[data-mask]').inputmask();
  
  $('#textarea-descripcion_tienda').summernote({
    placeholder: 'Opcional',
    toolbar: [
      ['style', ['bold', 'italic', 'underline', 'clear']],
      ['fontname', ['fontname']],
      ['color', ['color']],
      ['view', ['fullscreen', 'codeview']],
    ],
    tabsize: 4,
    height: 110
  });

  $('#textarea-terminos_condiciones').summernote({
    placeholder: 'Opcional',
    toolbar: [
      ['style', ['style']],
      ['style', ['bold', 'italic', 'underline', 'clear']],
      ['fontname', ['fontname']],
      ['color', ['color']],
      ['view', ['fullscreen', 'codeview']],
    ],
    tabsize: 4,
    height: 70
  });
  
  $('#textarea-politica_privacidad').summernote({
    placeholder: 'Opcional',
    toolbar: [
      ['style', ['style']],
      ['style', ['bold', 'italic', 'underline', 'clear']],
      ['fontname', ['fontname']],
      ['color', ['color']],
      ['view', ['fullscreen', 'codeview']],
    ],
    tabsize: 4,
    height: 70
  });
  
  $('#textarea-devoluciones').summernote({
    placeholder: 'Opcional',
    toolbar: [
      ['style', ['style']],
      ['style', ['bold', 'italic', 'underline', 'clear']],
      ['fontname', ['fontname']],
      ['color', ['color']],
      ['view', ['fullscreen', 'codeview']],
    ],
    tabsize: 4,
    height: 70
  });

	$(".toggle-password").click(function() {
		$(this).toggleClass("fa-eye fa-eye-slash");
    var $pwd = $(".pwd");
    if ($pwd.attr('type') == 'password') {
      $pwd.attr('type', 'text');
    } else {
      $pwd.attr('type', 'password');
    }
  });

  /*url = base_url + 'TiendaVirtual/Configuracion/InformacionTiendaVirtualController/ajax_list';
  table_sistema = $('#table-Sistema').DataTable({
    'dom': '<"top">frt<"bottom"l><"clear">',
    'searching'   : false,
    'bStateSave'  : true,
    'processing'  : true,
    'serverSide'  : true,
    'info'        : true,
    'autoWidth'   : false,
    'pagingType'  : 'full_numbers',
    'oLanguage' : {
      'sInfo'                 : 'Mostrando (_START_ - _END_) total de registros _TOTAL_',
      'sLengthMenu'           : '_MENU_',
      'sSearch'               : 'Buscar por: ',
      'sSearchPlaceholder'    : 'UPC / Nombre',
      'sZeroRecords'          : 'No se encontraron registros',
      'sInfoEmpty'            : 'No hay registros',
      'sLoadingRecords'       : 'Cargando...',
      'sProcessing'           : 'Procesando...',
      'oPaginate'             : {
        'sFirst'    : '<<',
        'sLast'     : '>>',
        'sPrevious' : '<',
        'sNext'     : '>',
      },
    },
    'order': [],
    'ajax': {
      'url'       : url,
      'type'      : 'POST',
      'dataType'  : 'json',
      'data'      : function ( data ) {
        data.filtro_empresa = $( '#cbo-filtro_empresa' ).val(),
        data.filtro_organizacion = $( '#cbo-filtro_organizacion' ).val(),
        data.Filtros_Sistemas = $( '#cbo-Filtros_Sistemas' ).val(),
        data.Global_Filter = $( '#txt-Global_Filter' ).val();
      },
    },
    'columnDefs': [{
      'className' : 'text-center',
      'targets'   : 'no-sort',
      'orderable' : false,
    },],
    'lengthMenu': [[10, 100, 1000, -1], [10, 100, 1000, "Todos"]],
  });
  
  $( '.dataTables_length' ).addClass('col-md-3');
  $( '.dataTables_paginate' ).addClass('col-md-9');*/
  
  $( '#txt-Global_Filter' ).keyup(function() {
    table_sistema.search($(this).val()).draw();
  });
  
//   $.validator.addMethod("RemoteTienda",   function(value, element) {
//     var formData        = new FormData();
//     // let DominioNuevo    = value.toLowerCase()+".compramaz.com";
//     // let DominioActual   = $(element).data("DominioActual");

//     // if(DominioNuevo==DominioActual)
//     //   return this.optional(element) || false;
    
//     //  formData.append("No_Subdominio_Tienda_Virtual", value);

//     // const response = await fetch( base_url + 'TiendaVirtual/Configuracion/InformacionTiendaVirtualController/ValidarDominioTienda', {
//     //   method: 'POST', 
//     //   body: formData 
//     // });
//     // console.log(response);
//     return "pending";
// }, "Subominio ya esta en uso");


 /* $( '#form-Sistema' ).validate({
    rules: {
      No_Tienda_Lae_Shop: {
        required: true,
      },
      Nu_Celular_Whatsapp_Lae_Shop: {
        required: true,
      },
			Txt_Email_Lae_Shop:{
        validemail: true,
        required: true,
			}
		},
    messages: {
      No_Tienda_Lae_Shop:{
        required: "Ingresar Nombre",
      },
      Nu_Celular_Whatsapp_Lae_Shop: {
        required: "Ingresar WhatsApp",
      },
      Txt_Email_Lae_Shop: {
        validemail: "Ingresar correo válido",
        required: "Ingresar correo",
      },
		},
		errorPlacement : function(error, element) {
		  $(element).closest('.form-group').find('.help-block').html(error.html());
	  },
		highlight : function(element) {
	    $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
    },
    unhighlight: function(element, errorClass, validClass) {
			$(element).closest('.form-group').removeClass('has-error').addClass('has-success');
			$(element).closest('.form-group').find('.help-block').html('');
    },
		submitHandler: form_Sistema
    // submitHandler: function(){
    //   alert("ok");
    // }
  });*/
  
  /*$( '#cbo-filtro_empresa' ).html('<option value="0" selected="selected">- Todas -</option>');
  $( '#cbo-filtro_organizacion' ).html('<option value="0" selected="selected">- Todas -</option>');

  url = base_url + 'HelperController/getEmpresas';
  $.post( url , function( response ){
    $( '#cbo-filtro_empresa' ).html('<option value="0" selected="selected">- Todas -</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-filtro_empresa' ).append( '<option value="' + response[i].ID_Empresa + '">' + response[i].No_Empresa + '</option>' );
  }, 'JSON');
  
	$( '#cbo-filtro_empresa' ).change(function(){
    if ( $(this).val() > 0 ) {
      $( '#modal-loader' ).modal('show');
      url = base_url + 'HelperController/getOrganizaciones';
      var arrParams = {
        iIdEmpresa : $( this ).val(),
      };
      $.post( url, arrParams, function( response ){
        $( '#cbo-filtro_organizacion' ).html('<option value="0" selected="selected">- Todas -</option>');
        for (var i = 0; i < response.length; i++)
          $( '#cbo-filtro_organizacion' ).append( '<option value="' + response[i].ID_Organizacion + '">' + response[i].No_Organizacion + '</option>' );    
        $( '#modal-loader' ).modal('hide');
      }, 'JSON');
    }
    table_sistema.search($(this).val()).draw();
  });

	$( '#cbo-filtro_organizacion' ).change(function(){
    table_sistema.search($(this).val()).draw();
  });*/

  //CSS
  $('#cbo-color').change(function () {
    $(".background").css("background-color", "#" + $(this).val());
  })
//keypress keydown keyup
/*
  $('#txt-No_Subdominio_Tienda_Virtual').on('keypress',function(event){
    return event.charCode == 45 ||(event.charCode >= 48 && event.charCode <= 57) || (event.charCode >= 97 && event.charCode <= 122);
  });
  */
  
  verSistema();
})

function verSistema(){
  //console.log('entro');
  const ID = $('[name="ID"]').val();
  const No_Imagen_Logo_Empresa = $('[name="No_Imagen_Logo_Empresa"]').val();
  const Nu_Version_Imagen = $('[name="Nu_Version_Imagen"]').val();
  $( '#form-Sistema' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '.div-Listar' ).hide();
  $( '.div-AgregarEditar' ).show();
  
  $( '#modal-loader' ).modal('show');
   
  url = base_url + 'TiendaVirtual/Configuracion/InformacionTiendaVirtualController/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      //console.log('vamos');
      //console.log(response);
      $('[name="EID_Configuracion"]').val(response.ID_Configuracion);
      $('[name="EID_Configuracion_Dominio"]').val(response.ID_Configuracion);
      var selected;
      url = base_url + 'HelperController/getEmpresas';
      $.post( url , function( responseEmpresa ){
        $( '#cbo-Empresas' ).html('');
        for (var i = 0; i < responseEmpresa.length; i++){
          selected = '';
          if(response.ID_Empresa == responseEmpresa[i].ID_Empresa)
            selected = 'selected="selected"';
          $( '#cbo-Empresas' ).append( '<option value="' + responseEmpresa[i].ID_Empresa + '" ' + selected + '>' + responseEmpresa[i].No_Empresa + '</option>' );
        }
      }, 'JSON');
      
      $('[name="Txt_Url_Logo_Lae_Shop"]').val(response.Txt_Url_Logo_Lae_Shop);

      $('[name="No_Tienda_Lae_Shop"]').val(response.No_Tienda_Lae_Shop);
      $('[name="Nu_Celular_Lae_Shop"]').val(response.Nu_Celular_Lae_Shop);
      $('[name="Nu_Celular_Whatsapp_Lae_Shop"]').val(response.Nu_Celular_Whatsapp_Lae_Shop);
      $('[name="Txt_Email_Lae_Shop"]').val(response.Txt_Email_Lae_Shop);
      
      $("#textarea-descripcion_tienda").summernote("code", response.Txt_Descripcion_Lae_Shop);

      $('.div-agregar_integraciones').css("display", "none");
      $('#btn-agregar_integraciones').data('agregar_integraciones', 0);
      if(
        (response.Txt_Facebook_Pixel_Codigo_Dominio_Lae_Shop != '' && response.Txt_Facebook_Pixel_Codigo_Dominio_Lae_Shop != null) ||
        (response.Txt_Facebook_Pixel_Lae_Shop != '' && response.Txt_Facebook_Pixel_Lae_Shop != null) ||
        (response.Txt_Tiktok_Pixel_Lae_Shop != '' && response.Txt_Tiktok_Pixel_Lae_Shop != null) ||
        (response.Txt_Google_Analytics_Lae_Shop != '' && response.Txt_Google_Analytics_Lae_Shop != null) ||
        (response.Txt_Google_Shopping_Dominio_Lae_Shop != '' && response.Txt_Google_Shopping_Dominio_Lae_Shop != null)
      ){
        $('.div-agregar_integraciones').css("display", "block");
        $('#btn-agregar_integraciones').data('agregar_integraciones', 1);
      }

      $('[name="Txt_Facebook_Pixel_Codigo_Dominio_Lae_Shop"]').val(response.Txt_Facebook_Pixel_Codigo_Dominio_Lae_Shop);
      $('[name="Txt_Facebook_Pixel_Lae_Shop"]').val(response.Txt_Facebook_Pixel_Lae_Shop);
      getFacebookShopUrl();
      
      $('[name="Txt_Tiktok_Pixel_Lae_Shop"]').val(response.Txt_Tiktok_Pixel_Lae_Shop);

      $('[name="Txt_Google_Analytics_Lae_Shop"]').val(response.Txt_Google_Analytics_Lae_Shop);

      $('[name="Txt_Google_Shopping_Dominio_Lae_Shop"]').val(response.Txt_Google_Shopping_Dominio_Lae_Shop);
      getGoogleShoppingUrl();

      $(".background").css("background-color", "#" + response.No_Html_Color_Lae_Shop);

      $('[name="No_Html_Color_Lae_Shop"]').val('#' + response.No_Html_Color_Lae_Shop);
      $('[name="No_Html_Color_HSV_Lae_Shop"]').val(response.No_Html_Color_HSV_Lae_Shop);
      $('.icon').css('background-color', '#' + response.No_Html_Color_Lae_Shop);
      $('.picker').attr('data-hsv', response.No_Html_Color_HSV_Lae_Shop);
      var colors = $('.picker').attr('data-hsv').split(',');
      $('.picker').data('hsv', {h: colors[0], s: colors[1], v: colors[2]});
      $('.board').css('background-color', 'rgb(' + hexToRgb('#' + response.No_Html_Color_Lae_Shop).r + ', ' + hexToRgb('#' + response.No_Html_Color_Lae_Shop).g + ', ' + hexToRgb('#' + response.No_Html_Color_Lae_Shop).b + ')');

      selected = '';
      if (response.No_Html_Color_Lae_Shop == '766df4')
        selected = 'selected="selected"';
      $('#cbo-color').html('<option value="766df4" ' + selected + '>Púrpura</option>');

      selected = '';
      if (response.No_Html_Color_Lae_Shop == '7B39FF')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="7B39FF" ' + selected + '>Morado</option>');

      selected = '';
      if (response.No_Html_Color_Lae_Shop == '1A7FDC')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="1A7FDC" ' + selected + '>Celeste</option>');

      selected = '';
      if (response.No_Html_Color_Lae_Shop == '1B61A1')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="1B61A1" ' + selected + '>Azul</option>');

      selected = '';
      if (response.No_Html_Color_Lae_Shop == '227E52')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="227E52" ' + selected + '>Verde</option>');

      selected = '';
      if (response.No_Html_Color_Lae_Shop == '6D7A6A')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="6D7A6A" ' + selected + '>Verde grisáceo</option>');

      selected = '';
      if (response.No_Html_Color_Lae_Shop == 'ED5702')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="ED5702" ' + selected + '>Naranja</option>');

      selected = '';
      if (response.No_Html_Color_Lae_Shop == 'DE063A')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="DE063A" ' + selected + '>Rojo</option>');

      selected = '';
      if (response.No_Html_Color_Lae_Shop == '950919')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="950919" ' + selected + '>Guinda</option>');

      selected = '';
      if (response.No_Html_Color_Lae_Shop == 'DE287F')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="DE287F" ' + selected + '>Fucsia</option>');

      selected = '';
      if (response.No_Html_Color_Lae_Shop == 'FF3048')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="FF3048" ' + selected + '>Rosado</option>');

      selected = '';
      if (response.No_Html_Color_Lae_Shop == 'ffe930')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="ffe930" ' + selected + '>Amarillo</option>');

      selected = '';
      if (response.No_Html_Color_Lae_Shop == '2D2C2C')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="2D2C2C" ' + selected + '>Negro</option>');

      selected = '';
      if (response.No_Html_Color_Lae_Shop == '6C6B6C')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="6C6B6C" ' + selected + '>Gris</option>');

      selected = '';
      if (response.No_Html_Color_Lae_Shop == '4e342e')
        selected = 'selected="selected"';
      $('#cbo-color').append('<option value="4e342e" ' + selected + '>Marron</option>');

      // DATOS ADICIONALES
      $('[name="No_Subdominio_Tienda_Virtual"]').val(response.No_Subdominio_Tienda_Virtual)
      .data( "ID_Subdominio_Tienda_Virtual", response.ID_Subdominio_Tienda_Virtual )
      .data( "DominioActual", response.DominioActual );

      $('[name="Nu_Codigo_Pais_Celular_Lae_Shop"]').val(response.Nu_Codigo_Pais_Celular_Lae_Shop);

      $('[name="No_Dominio_Tienda_Virtual"]').val(response.No_Dominio_Tienda_Virtual);
      $('[name="Nu_Codigo_Pais_Celular_Whatsapp_Lae_Shop"]').val(response.Nu_Codigo_Pais_Celular_Whatsapp_Lae_Shop);
    
      $('#cbo-activar_stock').html('');
      for (var i = 0; i < 2; i++) {
        selected = '';
        if (response.Nu_Validar_Stock_Laeshop == i)
          selected = 'selected="selected"';
        $('#cbo-activar_stock').append('<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Desactivar' : 'Activar') + '</option>');
      }

      $('#cbo-precio_centralizado_laeshop').html('');
      for (var i = 0; i < 2; i++) {
        selected = '';
        if (response.Nu_Activar_Precio_Centralizado_Laeshop == i)
          selected = 'selected="selected"';
        $('#cbo-precio_centralizado_laeshop').append('<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Desactivar' : 'Activar') + '</option>');
      }

      $('#cbo-emitir_factura').html('');
      for (var i = 0; i < 2; i++) {
        selected = '';
        if (response.Nu_Activar_Emitir_Factura_Laeshop == i)
          selected = 'selected="selected"';
        $('#cbo-emitir_factura').append('<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Desactivar' : 'Activar') + '</option>');
      }

      $('#cbo-formulario_ver_item').html('');
      for (var i = 0; i < 2; i++) {
        selected = '';
        if (response.Nu_Activar_Formulario_Tienda_Virtual_Ver_Item == i)
          selected = 'selected="selected"';
        $('#cbo-formulario_ver_item').append('<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Desactivado' : 'Activado') + '</option>');
      }

      var sPrecioPaisCallCenter = 'S/ 4';
      if($('#hidden-ID_Pais_Usuario').val()=='2'){//2=Mexico
        var sPrecioPaisCallCenter = '$ 30';
      }

      $('#cbo-tipo_gestion_pedido_tienda_virtual').html('');
      for (var i = 1; i < 3; i++) {
        selected = '';
        if (response.Nu_Tipo_Gestion_Pedido_Tienda_Virtual == i)
          selected = 'selected="selected"';
        $('#cbo-tipo_gestion_pedido_tienda_virtual').append('<option value="' + i + '" ' + selected + '>' + (i == 1 ? 'CallCenter ' + sPrecioPaisCallCenter : 'Coordinado (GRATIS)') + '</option>');
      }
      
      $('.div-agregar_paginas').css("display", "none");
      $('#btn-agregar_paginas').data('agregar_integraciones', 0);
      if(
        (response.Txt_Page_Landing_Terminos != '' && response.Txt_Page_Landing_Terminos != null)
        || (response.Txt_Page_Landing_Politica != '' && response.Txt_Page_Landing_Politica != null)
        || (response.Txt_Page_Landing_Devolucion != '' && response.Txt_Page_Landing_Devolucion != null)
        || (response.Txt_Page_Landing_Envio != '' && response.Txt_Page_Landing_Envio != null)
      ){
        $('.div-agregar_paginas').css("display", "block");
        $('#btn-agregar_paginas').data('agregar_integraciones', 1);
      }

      $("#textarea-terminos_condiciones").summernote("block", response.Txt_Page_Landing_Terminos);
      $("#textarea-politica_privacidad").summernote("code", response.Txt_Page_Landing_Politica);
      $("#textarea-devoluciones").summernote("code", response.Txt_Page_Landing_Devolucion);
      $("#textarea-politica_envio").summernote("code", response.Txt_Page_Landing_Envio);
      
      $( '#modal-loader' ).modal('hide');
    },
    error: function (jqXHR, textStatus, errorThrown) {
      $( '#modal-loader' ).modal('hide');
	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
	    
  	  $( '#modal-message' ).modal('show');
	    $( '.modal-message' ).addClass( 'modal-danger' );
	    $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
	    setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
	    
	    //Message for developer
      console.log(jqXHR.responseText);
    }
  });
  
  /* obtener imagen guardada(s) */
  $( '.divDropzone' ).html(
  '<div id="id-divDropzone" class="dropzone div-dropzone">'
    +'<div class="dz-message">'
      +'Arrastrar o presionar click para subir imágen'
    +'</div>'
  +'</div>'
  );

  Dropzone.autoDiscover = false;
  Dropzone.prototype.defaultOptions.dictDefaultMessage = "Arrastrar o presionar click para subir imágen";
  Dropzone.prototype.defaultOptions.dictFallbackMessage = "Tu navegador no soporta la función arrastrar la imágen";
  Dropzone.prototype.defaultOptions.dictFileTooBig = "La imágen pesa ({{filesize}}MiB). El tamaño máximo es: {{maxFilesize}}MiB.";
  Dropzone.prototype.defaultOptions.dictInvalidFileType = "Solo se permite imágenes PNG";
  Dropzone.prototype.defaultOptions.dictCancelUpload = "Cancelar";
  Dropzone.prototype.defaultOptions.dictCancelUploadConfirmation = "¿Estás seguro de cancelar la subida?";
  Dropzone.prototype.defaultOptions.dictRemoveFile = "Eliminar";
  Dropzone.prototype.defaultOptions.dictMaxFilesExceeded = "Solo se puede subir 1 imágen";
    
  url = base_url + 'TiendaVirtual/Configuracion/InformacionTiendaVirtualController/uploadOnly/' + ID;
  var myDropzone = new Dropzone("#id-divDropzone", {
    url: url,
    params: {
      iVersionImage: (parseInt(Nu_Version_Imagen) + 1),
      iIdConfiguracion: ID,
    },
    acceptedFiles: ".jpeg,.jpg,.png",
    addRemoveLinks: true,
    uploadMultiple: false,
    maxFilesize: 1,//Peso en MB
    maxFiles: 1,
    thumbnailHeight: 200,
    thumbnailWidth: 200,
    parallelUploads: 1,
    thumbnail: function(file, dataUrl) {
      if (file.previewElement) {
        file.previewElement.classList.remove("dz-file-preview");
        var images = file.previewElement.querySelectorAll("[data-dz-thumbnail]");
        for (var i = 0; i < images.length; i++) {
          var thumbnailElement = images[i];
          thumbnailElement.alt = file.name;
          thumbnailElement.src = dataUrl;
        }
        setTimeout(function() { file.previewElement.classList.add("dz-image-preview"); }, 1);
      }
    },
    removedfile: function(file){
      url = base_url + 'TiendaVirtual/Configuracion/InformacionTiendaVirtualController/removeFileImage';
      $.ajax({
        url: url,
        type: "POST",
        dataType: "JSON",
        data: { iIdProducto: ID, nameFileImage: file.name },
        success: function (response) {
          $('.modal-message').removeClass('modal-danger modal-warning modal-success');
          $('#modal-message').modal('show');

          if (response.status == 'success') {
            $('.modal-message').addClass(response.style_modal);
            $('.modal-title-message').text(response.message);
            setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
            $('#hidden-nombre_imagen_logo_empresa').val('');
          } else {
            $('.modal-message').addClass(response.style_modal);
            $('.modal-title-message').text(response.message);
            setTimeout(function () { $('#modal-message').modal('hide'); }, 1200);
          }
        },
        error: function (jqXHR, textStatus, errorThrown) {
          $('.modal-message').removeClass('modal-danger modal-warning modal-success');
          $('#modal-message').modal('show');
          $('.modal-message').addClass('modal-danger');
          $('.modal-title-message').text(textStatus + ' [' + jqXHR.status + ']: ' + errorThrown);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 1700);

          //Message for developer
          console.log(jqXHR.responseText);
        }
      })
      var previewElement;
      return (previewElement = file.previewElement) != null ? (previewElement.parentNode.removeChild(file.previewElement)) : (void 0);
    },
    init: function () {
      //Verificar respuesta del servidor al subir archivo
      this.on("success", function (file, response) {
        var response = jQuery.parseJSON(response);

        $('.modal-message').removeClass('modal-danger modal-warning modal-success');
        $('#modal-message').modal('show');

        if (response.sStatus != 'error') {
          $('.modal-message').addClass(response.sClassModal);
          $('.modal-title-message').text(response.sMessage);

          $('#hidden-nombre_imagen_logo_empresa').val(response.sNombreImagenCategoriaUrl);

          setTimeout(function () { $('#modal-message').modal('hide'); }, 1100);
        } else {
          $('.modal-message').addClass(response.sClassModal);
          $('.modal-title-message').text(response.sMessage);
          setTimeout(function () { $('#modal-message').modal('hide'); }, 2100);
        }
      })

      if (No_Imagen_Logo_Empresa.length > 0 && No_Imagen_Logo_Empresa != '' && No_Imagen_Logo_Empresa !== undefined) {
        var me = this;
        url = base_url + 'TiendaVirtual/Configuracion/InformacionTiendaVirtualController/get_image';
        var arrPost = {
          'sUrlImage': No_Imagen_Logo_Empresa,
        }
        $.post(url, arrPost, function (response) {
          $.each(response, function (key, value) {
            var mockfile = value;
            me.emit("addedfile", mockfile);
            me.emit("thumbnail", mockfile, No_Imagen_Logo_Empresa);
            me.emit("complete", mockfile);
          })
        }, 'json');
      }
    }
  })
}

$( '#form-Sistema' ).validate({
  rules: {
    No_Tienda_Lae_Shop: {
      required: true,
    },
    Nu_Celular_Whatsapp_Lae_Shop: {
      required: true,
    },
    Txt_Email_Lae_Shop:{
      validemail: true,
      required: true,
    }
  },
  messages: {
    No_Tienda_Lae_Shop:{
      required: "Ingresar Nombre",
    },
    Nu_Celular_Whatsapp_Lae_Shop: {
      required: "Ingresar WhatsApp",
    },
    Txt_Email_Lae_Shop: {
      validemail: "Ingresar correo válido",
      required: "Ingresar correo",
    },
  },
  errorPlacement : function(error, element) {
    $(element).closest('.form-group').find('.help-block').html(error.html());
  },
  highlight : function(element) {
    $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
  },
  unhighlight: function(element, errorClass, validClass) {
    $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
    $(element).closest('.form-group').find('.help-block').html('');
  },
  submitHandler: form_Sistema
  // submitHandler: function(){
  //   alert("ok");
  // }
});

function form_Sistema(){
  if ( $( '#cbo-Empresas' ).val() == 0){
    $( '#cbo-Empresas' ).closest('.form-group').find('.help-block').html('Seleccionar empresa');
    $( '#cbo-Empresas' ).closest('.form-group').removeClass('has-success').addClass('has-error');
  } else {
    $( '#btn-save' ).text('');
    $( '#btn-save' ).attr('disabled', true);
    $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
  
    $( '#modal-loader' ).modal('show');
    
    var formData = new FormData($('#form-Sistema')[0]);

    formData.set('Txt_Descripcion_Lae_Shop',$( '#textarea-descripcion_tienda' ).summernote('code'));

    formData.set('Txt_Page_Landing_Terminos',$( '#textarea-terminos_condiciones' ).summernote('code'));
    formData.set('Txt_Page_Landing_Politica',$( '#textarea-politica_privacidad' ).summernote('code'));
    formData.set('Txt_Page_Landing_Devolucion',$( '#textarea-devoluciones' ).summernote('code'));
    formData.set('Txt_Page_Landing_Envio',$( '#textarea-politica_envio' ).summernote('code'));
    
    //console.log(formData);

    url = base_url + 'TiendaVirtual/Configuracion/InformacionTiendaVirtualController/crudSistema';
  	$.ajax({
      type		    : 'POST',
      dataType	  : 'JSON',
  		url		      : url,
  		data		    : formData,
      mimeType    : "multipart/form-data",
      contentType : false,
      cache       : false,
      processData : false,
  		success : function( response ){
  		  $( '#modal-loader' ).modal('hide');        
  	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
    	  $( '#modal-message' ).modal('show');
  		  
  		  if (response.status == 'success'){
          //$( '#form-Sistema' )[0].reset();
          //$( '.div-AgregarEditar' ).hide();
          //$( '.div-Listar' ).show();
          $( '#btn-save' ).text('');
          $( '#btn-save' ).attr('disabled', false);
          $( '#btn-save' ).append( 'Guardar' );
          getFacebookShopUrl();
          getGoogleShoppingUrl();
    	    $( '.modal-message' ).addClass(response.style_modal);
    	    $( '.modal-title-message' ).text(response.message);
    	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
    	    //reload_table_sistema();
  		  } else {
    	    $( '.modal-message' ).addClass(response.style_modal);
    	    $( '.modal-title-message' ).text(response.message);
    	    setTimeout(function() {$('#modal-message').modal('hide');}, 1200);
  	    }
  	    
        $( '#btn-save' ).text('');
        $( '#btn-save' ).append( 'Guardar' );
        $( '#btn-save' ).attr('disabled', false);
  		},
      error: function (jqXHR, textStatus, errorThrown) {
        $( '#modal-loader' ).modal('hide');
  	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
  	    
    	  $( '#modal-message' ).modal('show');
  	    $( '.modal-message' ).addClass( 'modal-danger' );
  	    $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
  	    
  	    //Message for developer
        console.log(jqXHR.responseText);
        
        $( '#btn-save' ).text('');
        $( '#btn-save' ).append( 'Guardar' );
        $( '#btn-save' ).attr('disabled', false);
      }
	  });
  }
}

function eliminarSistema(ID_Empresa, ID){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });
  
  $( '#btn-save-delete' ).off('click').click(function () {
    $( '#modal-loader' ).modal('show');
    
    url = base_url + 'TiendaVirtual/Configuracion/InformacionTiendaVirtualController/eliminarSistema/' + ID_Empresa + '/' + ID;
    $.ajax({
      url       : url,
      type      : "GET",
      dataType  : "JSON",
      success: function( response ){
        $( '#modal-loader' ).modal('hide');
        
        $modal_delete.modal('hide');
  	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
    	  $( '#modal-message' ).modal('show');
  		  
  		  if (response.status == 'success'){
    	    $( '.modal-message' ).addClass(response.style_modal);
    	    $( '.modal-title-message' ).text(response.message);
    	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
    	    //reload_table_sistema();
  		  } else {
    	    $( '.modal-message' ).addClass(response.style_modal);
    	    $( '.modal-title-message' ).text(response.message);
    	    setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
  		  }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        $( '#modal-loader' ).modal('hide');
        $modal_delete.modal('hide');
  	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
  	    
    	  $( '#modal-message' ).modal('show');
  	    $( '.modal-message' ).addClass( 'modal-danger' );
  	    $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
  	    
  	    //Message for developer
        console.log(jqXHR.responseText);
      },
    });
  });
}

function reload_table_sistema(){
  table_sistema.ajax.reload(null,false);
}

//RADIO TIPO DE DOMINIO
$( '[name=Nu_Tipo_Tienda' ).on("click", function() {
  if($(this).val() == 1) {
    $('.div-Tipo-Tienda label').text('');
    $('.div-Tipo-Tienda label').append('Subdominio');
    $('.div-Subdominio').removeClass('hidden');
    $('.div-Dominio').addClass('hidden');
  }else if ($(this).val() == 3) {
    $('.div-Tipo-Tienda label').text('');
    $('.div-Tipo-Tienda label').append('Dominio');
    $('.div-Subdominio').addClass('hidden');
    $('.div-Dominio').removeClass('hidden');
  }
});

//CAMPO SUBDOMINIO TIENDA VIRTUAL
$( '#txt-No_Subdominio_Tienda_Virtual' ).on('keypress', function(){
  if($( this ).val()) {
    $( '#txt-No_Subdominio_Tienda_Virtual' ).closest('.form-group').find('.help-block').html('');
    $( '#txt-No_Subdominio_Tienda_Virtual' ).closest('.form-group').removeClass('has-error');
  }
});

//CAMPO DOMINIO TIENDA VIRTUAL
$( '#txt-No_Dominio_Tienda_Virtual' ).on('keypress', function(){
  if($( this ).val()) {
    $( '#txt-No_Dominio_Tienda_Virtual' ).closest('.form-group').find('.help-block').html('');
    $( '#txt-No_Dominio_Tienda_Virtual' ).closest('.form-group').removeClass('has-error');
  }
});

//FUNCION CON AJAX PARA VALIDAR SI EL DOMINIO ESTA ASOCIADO
function validarDominioAsociado(){
  if(!$( '#txt-No_Dominio_Tienda_Virtual' ).val()) {
    $( '#txt-No_Dominio_Tienda_Virtual' ).closest('.form-group').find('.help-block').html('Debe llenar el campo');
    $( '#txt-No_Dominio_Tienda_Virtual' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    return;
  }
  $( '#modal-loader' ).modal('show');
    
  var formData = new FormData();
  formData.append('No_Dominio_Tienda_Virtual', $( '#txt-No_Dominio_Tienda_Virtual' ).val());
  
  url = base_url + 'TiendaVirtual/Configuracion/InformacionTiendaVirtualController/validarDominioAsociado';
  $.ajax({
    type		    : 'POST',
    dataType	  : 'JSON',
    url		      : url,
    data		    : formData,
    mimeType    : "multipart/form-data",
    contentType : false,
    cache       : false,
    processData : false,
    success : function( response ){
      $( '#modal-loader' ).modal('hide');        
      $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
      $( '#modal-message' ).modal('show');
      
      if (response.status == 'success'){
        $( '#INT_Dominio_Asociado').val(1);
        $( '.modal-message' ).addClass(response.style_modal);
        $( '.modal-title-message' ).text(response.message);
        setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
        //reload_table_sistema();
      } else {
        $( '.modal-message' ).addClass(response.style_modal);
        $( '.modal-title-message' ).text(response.message);
        setTimeout(function() {$('#modal-message').modal('hide');}, 1200);
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      $( '#modal-loader' ).modal('hide');
      $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
      
      $( '#modal-message' ).modal('show');
      $( '.modal-message' ).addClass( 'modal-danger' );
      $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
      setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
      
      //Message for developer
      console.log(jqXHR.responseText);
    }
  });  
}

//BOTON VERIFICAR DOMINIO ASOCIADO
$( '#btn-verificar-dominio' ).on('click', validarDominioAsociado);

//VALIDACION FORMULARIO DE DOMINIO
$( '#form-Sistema-Dominio' ).validate({
  rules: {
    Nu_Tipo_Tienda: {
      required: true,
    },
  },
  messages: {
    Nu_Tipo_Tienda:{
      required: "Debe elegir un tipo de dominio",
    },
  },
  errorPlacement : function(error, element) {
    $(element).closest('.form-group').find('.help-block').html(error.html());
  },
  highlight : function(element) {
    $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
  },
  unhighlight: function(element, errorClass, validClass) {
    $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
    $(element).closest('.form-group').find('.help-block').html('');
  },
  submitHandler: form_Sistema_Dominio
});

//SUBMIT DE FORMULARIO DE DOMINIO
function form_Sistema_Dominio(){
  
  if ( $( '#cbo-Empresas' ).val() == 0){
    $( '#cbo-Empresas' ).closest('.form-group').find('.help-block').html('Seleccionar empresa');
    $( '#cbo-Empresas' ).closest('.form-group').removeClass('has-success').addClass('has-error');
  } 
  else {
            $( '#btn-save' ).text('');
            $( '#btn-save' ).attr('disabled', true);
            $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
          
            if($( '#Tipo_Tienda_Subdominio' ).is(':checked') && !$( '#txt-No_Subdominio_Tienda_Virtual' ).val()) {
              $( '#txt-No_Subdominio_Tienda_Virtual' ).closest('.form-group').find('.help-block').html('Debe llenar el campo');
              $( '#txt-No_Subdominio_Tienda_Virtual' ).closest('.form-group').removeClass('has-success').addClass('has-error');
              $( '#btn-save' ).text('');
              $( '#btn-save' ).attr('disabled', false);
              $( '#btn-save' ).append( 'Guardar' );
              return;
            }

            if($( '#Tipo_Tienda_Dominio' ).is(':checked')){
              if(!$( '#txt-No_Dominio_Tienda_Virtual' ).val()) {
                $( '#txt-No_Dominio_Tienda_Virtual' ).closest('.form-group').find('.help-block').html('Debe llenar el campo');
                $( '#txt-No_Dominio_Tienda_Virtual' ).closest('.form-group').removeClass('has-success').addClass('has-error');
                $( '#btn-save' ).text('');
                $( '#btn-save' ).attr('disabled', false);
                $( '#btn-save' ).append( 'Guardar' );
                return;
              }
              if( $( '#INT_Dominio_Asociado') .val() != 1){
                $( '#txt-No_Dominio_Tienda_Virtual' ).closest('.form-group').find('.help-block').html('Debe pulsar el botón de Verificar Dominio para comprobar de que el dominio ya esté asociado');
                $( '#txt-No_Dominio_Tienda_Virtual' ).closest('.form-group').removeClass('has-success').addClass('has-error');
                $( '#btn-save' ).text('');
                $( '#btn-save' ).attr('disabled', false);
                $( '#btn-save' ).append( 'Guardar' );
                return;
              }
            }

            $( '#modal-loader' ).modal('show');
            
            var formData = new FormData($('#form-Sistema-Dominio')[0]);
            
            url = base_url + 'TiendaVirtual/Configuracion/InformacionTiendaVirtualController/crudSistemaDominio';
          	$.ajax({
              type		    : 'POST',
              dataType	  : 'JSON',
          		url		      : url,
          		data		    : formData,
              mimeType    : "multipart/form-data",
              contentType : false,
              cache       : false,
              processData : false,
          		success : function( response ){
          		  $( '#modal-loader' ).modal('hide');        
          	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
            	  $( '#modal-message' ).modal('show');
          		  
          		  if (response.status == 'success'){
                  $( '#btn-save' ).text('');
                  $( '#btn-save' ).attr('disabled', false);
                  $( '#btn-save' ).append( 'Guardar' );
            	    $( '.modal-message' ).addClass(response.style_modal);
            	    $( '.modal-title-message' ).text(response.message);
            	    setTimeout(function() {
                    $('.modal-message').modal('hide');
                    window.location = base_url + 'TiendaVirtual/Configuracion/InformacionTiendaVirtualController/listar';
                  }, 2100);
          		  } else {
            	    $( '.modal-message' ).addClass(response.style_modal);
            	    $( '.modal-title-message' ).text(response.message);
            	    setTimeout(function() {$('#modal-message').modal('hide');}, 6100);
          	    }
          	    
                $( '#btn-save' ).text('');
                $( '#btn-save' ).append( 'Guardar' );
                $( '#btn-save' ).attr('disabled', false);
          		},
              error: function (jqXHR, textStatus, errorThrown) {
                $( '#modal-loader' ).modal('hide');
          	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
          	    
            	  $( '#modal-message' ).modal('show');
          	    $( '.modal-message' ).addClass( 'modal-danger' );
          	    $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
          	    setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
          	    
          	    //Message for developer
                console.log(jqXHR.responseText);
                
                $( '#btn-save' ).text('');
                $( '#btn-save' ).append( 'Guardar' );
                $( '#btn-save' ).attr('disabled', false);
              }
        	  });
   }

}

//BOTON CANCELAR DE AMBOS FORMULARIOS 
$( '.btn-cancelar').on('click', function() {
  verSistema()
});

function hexToRgb(hex) {
  // Expand shorthand form (e.g. "03F") to full form (e.g. "0033FF")
  var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
  hex = hex.replace(shorthandRegex, function(m, r, g, b) {
    return r + r + g + g + b + b;
  });

  var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
  return result ? {
    r: parseInt(result[1], 16),
    g: parseInt(result[2], 16),
    b: parseInt(result[3], 16)
  } : null;
}

function importarPaginas(sTipo) {
  var ID = $('[name="EID_Configuracion"]').val();

  var $modal_delete = $('.modal-message-delete');
  $modal_delete.modal('show');

  $('.modal-message-delete').removeClass('modal-danger modal-warning modal-success');
  $('.modal-message-delete').addClass('modal-success');

  $('.modal-title-message-delete').text('¿Deseas importar términos y condiciones?');

  $('#btn-cancel-delete').off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $('#btn-save-delete').off('click').click(function () {
    _importarPaginas($modal_delete, ID, sTipo);
  });
}

function _importarPaginas($modal_delete, ID, sTipo){
  $( '#modal-loader' ).modal('show');
    
  url = base_url + 'TiendaVirtual/Configuracion/InformacionTiendaVirtualController/importarPaginas/' + ID + '/' + sTipo;
  $.ajax({
    url       : url,
    type      : "GET",
    dataType  : "JSON",
    success: function( response ){
      $( '#modal-loader' ).modal('hide');
      
      $modal_delete.modal('hide');
	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
  	  $( '#modal-message' ).modal('show');
		  
		  if (response.status == 'success'){
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);

        if(sTipo=='terminos'){
          $('[name="Txt_Page_Landing_Terminos"]').val(response.data);
          $("#textarea-terminos_condiciones").summernote("code", response.data);
        } else if(sTipo=='privacidad'){
          $('[name="Txt_Page_Landing_Politica"]').val(response.data);
          $("#textarea-politica_privacidad").summernote("code", response.data);
        } else if(sTipo=='devoluciones'){
          $('[name="Txt_Page_Landing_Devolucion"]').val(response.data);
          $("#textarea-devoluciones").summernote("code", response.data);
        } else if(sTipo=='politica_envio'){
          $('[name="Txt_Page_Landing_Envio"]').val(response.data);
          $("#textarea-politica_envio").summernote("code", response.data);
        }
		  } else {
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 3100);
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      accion = '';
      $( '#modal-loader' ).modal('hide');
      $modal_delete.modal('hide');
	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
	    
  	  $( '#modal-message' ).modal('show');
	    $( '.modal-message' ).addClass( 'modal-danger' );
	    $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
	    setTimeout(function() {$('#modal-message').modal('hide');}, 1700);
	    
	    //Message for developer
      console.log(jqXHR.responseText);
    },
  });
}

function getFacebookShopUrl() {
  $( '.Div_Facebook_Url_Lae_Shop' ).addClass('hidden');
  $( '.Div_Facebook_Url_Lae_Shop .form-group' ).text('');
  $( '#Txt_Facebook_Url_Lae_Shop' ).val('');
  if($( '[name="Txt_Facebook_Pixel_Lae_Shop"]' ).val() && $( '[name="Txt_Facebook_Pixel_Codigo_Dominio_Lae_Shop"]' ).val()) {
    const Txt_Facebook_Url_Lae_Shop = window.location.protocol + '//' + window.location.host + '/principal/assets/downloads/' + $( '[name="Txt_Facebook_Pixel_Lae_Shop"]' ).val()+'.csv';
    $( '.Div_Facebook_Url_Lae_Shop' ).removeClass('hidden');
    $( '.Div_Facebook_Url_Lae_Shop .form-group' ).html(`<span onclick="copyFacebookUrlToClipboard('${Txt_Facebook_Url_Lae_Shop}')" title="Haz click aqui" class="btn btn-info btn-block" style="background-color: #0080FB; border-color: #0080FB; text-align: left;"><i class="fa fa-facebook"></i> ${Txt_Facebook_Url_Lae_Shop}</span>`);
    $( '#Txt_Facebook_Url_Lae_Shop' ).val(Txt_Facebook_Url_Lae_Shop);
  }
}

function getGoogleShoppingUrl() {
  $( '.Div_Google_Shopping_Url_Lae_Shop' ).addClass('hidden');
  $( '.Div_Google_Shopping_Url_Lae_Shop .form-group' ).text('');
  $( '#Txt_Google_Shopping_Url_Lae_Shop' ).val('');
  if($( '[name="Txt_Google_Shopping_Dominio_Lae_Shop"]' ).val()) {
    const Txt_Google_Shopping_Url_Lae_Shop = window.location.protocol + '//' + window.location.host + '/principal/assets/downloads/' + $( '[name="Txt_Google_Shopping_Dominio_Lae_Shop"]' ).val()+'.txt';
    $( '.Div_Google_Shopping_Url_Lae_Shop' ).removeClass('hidden');
    $( '.Div_Google_Shopping_Url_Lae_Shop .form-group' ).html(`<span onclick="copyFacebookUrlToClipboard('${Txt_Google_Shopping_Url_Lae_Shop}')" title="Haz click aqui" class="btn btn-default btn-block" style="text-align: left;"><i style="color: #EA4335;" class="fa fa-google"></i> ${Txt_Google_Shopping_Url_Lae_Shop}</span>`);
    $( '#Txt_Google_Shopping_Url_Lae_Shop' ).val(Txt_Google_Shopping_Url_Lae_Shop);
  }
}

function copyFacebookUrlToClipboard(texto) {
  $( '#modal-loader' ).modal('show');
  url = base_url + 'TiendaVirtual/Configuracion/InformacionTiendaVirtualController/getAlmacenPrincipal/';
  $.ajax({
    url       : url,
    type      : "GET",
    dataType  : "JSON",
    success: function( response ){
      $( '#modal-loader' ).modal('hide');
      $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
      $( '#modal-message' ).modal('show');

      if (response.status == 'success'){
        navigator.clipboard.writeText(texto);
        $( '.modal-message' ).addClass(response.style_modal);
        $( '.modal-title-message' ).text("Enlace Copiado");
        setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
        //reload_table_sistema();
      } else {
        $( '.modal-message' ).addClass(response.style_modal);
        $( '.modal-title-message' ).text(response.message);
        setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      $( '#modal-loader' ).modal('hide');
      $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');

      $( '#modal-message' ).modal('show');
      $( '.modal-message' ).addClass( 'modal-danger' );
      $( '.modal-title-message' ).text( textStatus + ' [' + jqXHR.status + ']: ' + errorThrown );
      setTimeout(function() {$('#modal-message').modal('hide');}, 1700);

      //Message for developer
      console.log(jqXHR.responseText);
    },
  });
} 