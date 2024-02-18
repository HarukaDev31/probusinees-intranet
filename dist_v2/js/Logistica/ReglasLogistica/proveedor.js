var url;
var table_proveedor;
var accion_proveedor = '';

function importarExcelProveedor(){
  $( ".modal_importar_proveedor" ).modal( "show" );
}

$(function () {
  $('.div-mas_opciones').hide();
  $('#checkbox-mas_filtros').on('ifChanged', function(){
    $('.div-mas_opciones').hide();
    var _this = jQuery(this);
    if(_this.is(':checked')){
      $('.div-mas_opciones').show();
    }
  });

  // Validate exist file excel product
	$( document ).on('click', '#btn-excel-importar_proveedor', function(event) {
	  if ( $( "#my-file-selector_proveedor" ).val().length === 0 ) {
      $( '#my-file-selector_proveedor' ).closest('.form-group').find('.help-block').html('Seleccionar archivo');
		  $( '#my-file-selector_proveedor' ).closest('.form-group').removeClass('has-success').addClass('has-error');
	  } else {
      $( '#btn-cancel-provider' ).attr('disabled', true);
      $( '#a-download-provider' ).attr('disabled', true);
	    
      $( '#btn-excel-importar_proveedor' ).text('');
      $( '#btn-excel-importar_proveedor' ).attr('disabled', true);
      $( '#btn-excel-importar_proveedor' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
  
      //$( '#modal-loader' ).modal('show');
	  }
  })
  
  $('.select2').select2();
  
  //LAE API SUNAT / RENIEC
  $( '#btn-cloud-api_proveedor' ).click(function(){
    if ( $( '#cbo-TiposDocumentoIdentidad' ).val().length === 0){
      $( '#cbo-TiposDocumentoIdentidad' ).closest('.form-group').find('.help-block').html('Seleccionar tipo doc. identidad');
  	  $( '#cbo-TiposDocumentoIdentidad' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ($( '#cbo-TiposDocumentoIdentidad' ).find(':selected').data('nu_cantidad_caracteres') != $( '#txt-Nu_Documento_Identidad').val().length ) {
      $( '#txt-Nu_Documento_Identidad' ).closest('.form-group').find('.help-block').html('Debe ingresar ' + $( '#cbo-TiposDocumentoIdentidad' ).find(':selected').data('nu_cantidad_caracteres') + ' dígitos' );
  	  $( '#txt-Nu_Documento_Identidad' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else if ( 
        (
          $( '#cbo-TiposDocumentoIdentidad' ).val() == 1 ||
          $( '#cbo-TiposDocumentoIdentidad' ).val() == 3 ||
          $( '#cbo-TiposDocumentoIdentidad' ).val() == 5 ||
          $( '#cbo-TiposDocumentoIdentidad' ).val() == 6
        )
        ) {
      $( '#cbo-TiposDocumentoIdentidad' ).closest('.form-group').find('.help-block').html('Disponible solo para DNI / RUC');
  	  $( '#cbo-TiposDocumentoIdentidad' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      $( '#btn-cloud-api_proveedor' ).text('');
      $( '#btn-cloud-api_proveedor' ).attr('disabled', true);
      $( '#btn-cloud-api_proveedor' ).append( '<i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
      
      // Obtener datos de SUNAT y RENIEC
      var url_api = 'https://www.laesystems.com/librerias/sunat/partner/format/json/x-api-key/';
			if ( $( '#cbo-TiposDocumentoIdentidad' ).val() == 2 )//2=RENIEC, API SUNAT
				url_api = 'https://www.laesystems.com/librerias/reniec/partner/format/json/x-api-key/';
			url_api = url_api + sTokenGlobal;
			
      var data = {
        ID_Tipo_Documento_Identidad : $( '#cbo-TiposDocumentoIdentidad' ).val(),
        Nu_Documento_Identidad : $( '#txt-Nu_Documento_Identidad' ).val(),
      };
      
      $.ajax({
        url: url_api,
        type:'POST',
        data : data,
        success: function(response){
          $( '#btn-cloud-api_proveedor' ).closest('.form-group').find('.help-block').html('');
      	  $( '#btn-cloud-api_proveedor' ).closest('.form-group').removeClass('has-success').addClass('has-error');
      	  
          if (response.success == true){
            $('[name="No_Entidad"]').val( response.data.No_Names );
            if ( $( '#cbo-TiposDocumentoIdentidad' ).val() == 4) {//RUC
              $('[name="Txt_Direccion_Entidad"]').val( response.data.Txt_Address );
              $('[name="Nu_Telefono_Entidad"]').val( response.data.Nu_Phone );
              $('[name="Nu_Celular_Entidad"]').val( response.data.Nu_Cellphone );
              if ( response.data.Nu_Status == 1)
                $("div.estado select").val("1");
              else {
                $("div.estado select").val("0");

                $( '#modal-message' ).modal('show');
                $( '.modal-message' ).addClass('modal-danger');
                $( '.modal-title-message' ).text( 'El proveedor se encuentra con BAJA DE OFICIO / NO HABIDO' );
                setTimeout(function() {$('#modal-message').modal('hide');}, 2500);
              }
            }
          } else {
            $('[name="No_Entidad"]').val( '' );
            if ( $( '#cbo-TiposDocumentoIdentidad' ).val() == 4) {//RUC
              $('[name="Txt_Direccion_Entidad"]').val( '' );
              $('[name="Nu_Telefono_Entidad"]').val( '' );
              $('[name="Nu_Celular_Entidad"]').val( '' );
            }
            $( '#txt-Nu_Documento_Identidad' ).closest('.form-group').find('.help-block').html(response.msg);
        	  $( '#txt-Nu_Documento_Identidad' ).closest('.form-group').removeClass('has-success').addClass('has-error');
        	  
  		  	  $( '#txt-Nu_Documento_Identidad' ).focus();
  		  	  $( '#txt-Nu_Documento_Identidad' ).select();
          }
  		    
          $( '#btn-cloud-api_proveedor' ).text('');
          $( '#btn-cloud-api_proveedor' ).attr('disabled', false);
          $( '#btn-cloud-api_proveedor' ).append( '<i class="fa fa-cloud-download fa-lg"></i>' );
        },
        error: function(response){
          $( '#btn-cloud-api_proveedor' ).closest('.form-group').find('.help-block').html('Sin acceso');
      	  $( '#btn-cloud-api_proveedor' ).closest('.form-group').removeClass('has-success').addClass('has-error');
      	  
          $( '[name="No_Entidad"]' ).val( '' );
          $( '[name="Txt_Direccion_Entidad"]' ).val( '' );
          $( '[name="Nu_Telefono_Entidad"]' ).val( '' );
          $( '[name="Nu_Celular_Entidad"]' ).val( '' );
              
          $( '#btn-cloud-api_proveedor' ).text('');
          $( '#btn-cloud-api_proveedor' ).attr('disabled', false);
          $( '#btn-cloud-api_proveedor' ).append( '<i class="fa fa-cloud-download fa-lg"></i>' );
        }
      });// /. SUNAT y RENIEC
    }
  })
  
  url = base_url + 'Logistica/ReglasLogistica/ProveedorController/ajax_list';
  table_proveedor = $( '#table-Proveedor' ).DataTable({
    dom: "<'row'<'col-sm-12 col-md-4'B><'col-sm-12 col-md-7'f><'col-sm-12 col-md-1'>>" +
    "<'row'<'col-sm-12'tr>>" +
    "<'row'<'col-sm-12 col-md-2'l><'col-sm-12 col-md-5'i><'col-sm-12 col-md-5'p>>",
    buttons     : [{
    extend    : 'excel',
    text      : '<i class="fa fa-file-excel color_icon_excel"></i> Excel',
    titleAttr : 'Excel',
    exportOptions: {
        columns: ':visible'
    }
    },
    {
    extend    : 'pdf',
    text      : '<i class="fa fa-file-pdf color_icon_pdf"></i> PDF',
    titleAttr : 'PDF',
    exportOptions: {
        columns: ':visible'
    }
    },
    {
    extend    : 'colvis',
    text      : '<i class="fa fa-ellipsis-v"></i> Columnas',
    titleAttr : 'Columnas',
    exportOptions: {
        columns: ':visible'
    }
    }],
    "paging": true,
    "lengthChange": true,
    "searching": true,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "responsive": false,
    'pagingType'  : 'full_numbers',
    'oLanguage' : {
    'sInfo'              : 'Mostrando (_START_ - _END_) total de registros _TOTAL_',
    'sLengthMenu'        : '_MENU_',
    'sSearch'            : 'Buscar por: ',
    'sSearchPlaceholder' : '',
    'sZeroRecords'       : 'No se encontraron registros',
    'sInfoEmpty'         : 'No hay registros',
    'sLoadingRecords'    : 'Cargando...',
    'sProcessing'        : 'Procesando...',
    'oPaginate'          : {
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
      'dataType'  : 'JSON',
      'data'      : function ( data ) {
      },
    },
    'columnDefs': [
    {
        'targets': 'no-hidden',
        "visible": false, 
    },{
    'className' : 'text-center',
    'targets'   : 'no-sort',
    'orderable' : false,
    },],
    'lengthMenu': [[10, 100, 1000, -1], [10, 100, 1000, "Todos"]],
  });

  $('#table-Proveedor_filter input').removeClass('form-control-sm');
  $('#table-Proveedor_filter input').addClass('form-control-md');
  $('#table-Proveedor_filter input').addClass("width_full");
  
  $( '#form-Proveedor' ).validate({
		rules:{
			ID_Tipo_Documento_Identidad: {
				required: true,
			},
			No_Entidad: {
				required: true,
				maxlength: 100
			},
			Nu_Telefono_Entidad: {
				minlength: 8,
				maxlength: 8
			},
			Nu_Celular_Entidad: {
				minlength: 11,
				maxlength: 11
			},
			Txt_Email_Entidad:{
				validemail: true,
			},
		},
		messages:{
			ID_Tipo_Documento_Identidad:{
				required: "Seleccionar tipo doc.",
			},
			No_Entidad:{
				required: "Ingresar Nombre",
				maxlength: "Máximo 100 dígitos"
			},
			Nu_Telefono_Entidad:{
				minlength: "Debe ingresar 7 dígitos",
				maxlength: "Debe ingresar 7 dígitos"
			},
			Nu_Celular_Entidad:{
				minlength: "Debe ingresar 9 dígitos",
				maxlength: "Debe ingresar 9 dígitos"
			},
			Txt_Email_Entidad:{
				validemail: "Ingresar correo válido",
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
		submitHandler: form_Proveedor
	});
  
	/* Tipo Documento Identidad */
	$( '#cbo-TiposDocumentoIdentidad' ).change(function(){
    $('.div-api').show();
	  if ( $(this).val() == 2 ) {//DNI
		  $( '#label-Nombre_Documento_Identidad' ).text('DNI');
		  $( '#label-No_Entidad' ).text('Nombre(s) y Apellidos');
			$( '#txt-Nu_Documento_Identidad' ).attr('maxlength', $(this).find(':selected').data('nu_cantidad_caracteres'));
	  } else if ( $(this).val() == 4 ) {//RUC
		  $( '#label-Nombre_Documento_Identidad' ).text('RUC');
		  $( '#label-No_Entidad' ).text('Razón Social');
			$( '#txt-Nu_Documento_Identidad' ).attr('maxlength', $(this).find(':selected').data('nu_cantidad_caracteres'));
	  } else {
      $('.div-api').hide();
	    $( '#label-Nombre_Documento_Identidad' ).text('DOCUMENTO');
		  $( '#label-No_Entidad' ).text('Nombre(s) y Apellidos');
			$( '#txt-Nu_Documento_Identidad' ).attr('maxlength', $(this).find(':selected').data('nu_cantidad_caracteres'));
	  }
	})
	
	$( '#cbo-Paises' ).change(function(){
	  $( '#cbo-Departamentos' ).html('');
	  if ( $(this).val() > 0 ) {
      url = base_url + 'HelperController/getDepartamentos';
      $.post( url, {ID_Pais : $(this).val()}, function( response ){
        $( '#cbo-Departamentos' ).html('<option value="" selected="selected">- Seleccionar -</option>');
        for (var i = 0; i < response.length; i++)
          $( '#cbo-Departamentos' ).append( '<option value="' + response[i].ID_Departamento + '">' + response[i].No_Departamento + '</option>' );
      }, 'JSON');
	  }
	})
	
	$( '#cbo-Departamentos' ).change(function(){
	  $( '#cbo-Provincias' ).html('');
	  if ( $(this).val() > 0 ) {
      url = base_url + 'HelperController/getProvincias';
      $.post( url, {ID_Departamento : $(this).val()}, function( response ){
        $( '#cbo-Provincias' ).html('<option value="" selected="selected">- Seleccionar -</option>');
        for (var i = 0; i < response.length; i++)
          $( '#cbo-Provincias' ).append( '<option value="' + response[i].ID_Provincia + '">' + response[i].No_Provincia + '</option>' );
      }, 'JSON');
	  }
	})
	
	$( '#cbo-Provincias' ).change(function(){
	  $( '#cbo-Distritos' ).html('');
	  if ( $(this).val() > 0 ) {
      url = base_url + 'HelperController/getDistritos';
      $.post( url, {ID_Provincia : $(this).val()}, function( response ){
        $( '#cbo-Distritos' ).html('<option value="" selected="selected">- Seleccionar -</option>');
        for (var i = 0; i < response.length; i++)
          $( '#cbo-Distritos' ).append( '<option value="' + response[i].ID_Distrito + '">' + response[i].No_Distrito + '</option>' );
      }, 'JSON');
	  }
	})
})

function agregarProveedor(){
  accion_proveedor = 'add_proveedor';
  $( '[name="EID_Empresa"]' ).focus();
  
  $( '#form-Proveedor' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '.div-Listar' ).hide();
  $( '.div-AgregarEditar' ).show();
  
  $( '[name="EID_Empresa"]' ).val('');
  $( '[name="EID_Entidad"]' ).val('');
  $( '[name="ENu_Documento_Identidad"]' ).val('');
  
  $( '#cbo-Departamentos' ).html('');
  $( '#cbo-Provincias' ).html('');
  $( '#cbo-Distritos' ).html('');

  $('.div-mas_opciones').hide();
  //$('#checkbox-mas_filtros').prop('checked', false).iCheck('update');
  $('.div-api').show();

  //$( '#modal-loader' ).modal('show');
  
  url = base_url + 'HelperController/getTiposDocumentoIdentidad';
  $.post( url , function( response ){
    $( '#cbo-TiposDocumentoIdentidad' ).html('');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-TiposDocumentoIdentidad' ).append( '<option value="' + response[i].ID_Tipo_Documento_Identidad + '" data-nu_cantidad_caracteres="' + response[i].Nu_Cantidad_Caracteres + '">' + response[i].No_Tipo_Documento_Identidad_Breve + '</option>' );
  }, 'JSON');
  
  url = base_url + 'HelperController/getPaises';
  $.post( url , function( response ){
    //$( '#modal-loader' ).modal('hide');
    $( '#cbo-Paises' ).html('<option value="" selected="selected">- Seleccionar -</option>');
    for (var i = 0; i < response.length; i++)
      $( '#cbo-Paises' ).append( '<option value="' + response[i].ID_Pais + '">' + response[i].No_Pais + '</option>' );
  }, 'JSON');
  
  $( '#cbo-Estado' ).html( '<option value="1">Activo</option>' );
  $( '#cbo-Estado' ).append( '<option value="0">Inactivo</option>' );
}

function verProveedor(ID){
  accion_proveedor = 'upd_proveedor';
  $( '[name="EID_Empresa"]' ).focus();
  
  $( '#form-Proveedor' )[0].reset();
  $( '.form-group' ).removeClass('has-error');
  $( '.form-group' ).removeClass('has-success');
  $( '.help-block' ).empty();
  
  $( '.div-Listar' ).hide();
  $( '.div-AgregarEditar' ).show();
  
  //$( '#modal-loader' ).modal('show');

  $('.div-mas_opciones').hide();
  //$('#checkbox-mas_filtros').prop('checked', false).iCheck('update');
 
  url = base_url + 'Logistica/ReglasLogistica/ProveedorController/ajax_edit/' + ID;
  $.ajax({
    url : url,
    type: "GET",
    dataType: "JSON",
    success: function(response){
      $( '#modal-Proveedor' ).modal('show');
      $( '.modal-title' ).text('Modificar Proveedor');
      
      $('[name="EID_Empresa"]').val(response.ID_Empresa);
      $('[name="EID_Entidad"]').val(response.ID_Entidad);
      $('[name="ENu_Documento_Identidad"]').val(response.Nu_Documento_Identidad);
      
      var selected;
      url = base_url + 'HelperController/getTiposDocumentoIdentidad';
      $.post( url , function( responseTiposDocumentoIdentidad ){
        $( '#cbo-TiposDocumentoIdentidad' ).html( '' );
        for (var i = 0; i < responseTiposDocumentoIdentidad.length; i++){
          selected = '';
          if(response.ID_Tipo_Documento_Identidad == responseTiposDocumentoIdentidad[i].ID_Tipo_Documento_Identidad)
            selected = 'selected="selected"';
          $( '#cbo-TiposDocumentoIdentidad' ).append( '<option value="' + responseTiposDocumentoIdentidad[i].ID_Tipo_Documento_Identidad + '" data-nu_cantidad_caracteres="' + responseTiposDocumentoIdentidad[i].Nu_Cantidad_Caracteres + '" ' + selected + '>' + responseTiposDocumentoIdentidad[i].No_Tipo_Documento_Identidad_Breve + '</option>' );
        }
        
        if ( response.ID_Tipo_Documento_Identidad == 2 ) {//DNI
          $( '#label-Nombre_Documento_Identidad' ).text('DNI');
          $( '#label-No_Entidad' ).text('Nombre(s) y Apellidos');
          $( '#txt-Nu_Documento_Identidad' ).attr('maxlength', $( '#cbo-TiposDocumentoIdentidad' ).find(':selected').data('nu_cantidad_caracteres'));
        } else if ( response.ID_Tipo_Documento_Identidad == 4 ) {//RUC
          $( '#label-Nombre_Documento_Identidad' ).text('RUC');
          $( '#label-No_Entidad' ).text('Razón Social');
          $( '#txt-Nu_Documento_Identidad' ).attr('maxlength', $( '#cbo-TiposDocumentoIdentidad' ).find(':selected').data('nu_cantidad_caracteres'));
        } else {
          $('.div-api').hide();
          $( '#label-Nombre_Documento_Identidad' ).text('# Documento Identidad');
          $( '#label-No_Entidad' ).text('Nombre(s) y Apellidos');
          $( '#txt-Nu_Documento_Identidad' ).attr('maxlength', $( '#cbo-TiposDocumentoIdentidad' ).find(':selected').data('nu_cantidad_caracteres'));
        }
      }, 'JSON');

      $('.div-api').show();
      
      $('[name="Nu_Documento_Identidad"]').val(response.Nu_Documento_Identidad);
      $('[name="No_Entidad"]').val(response.No_Entidad);
      $('[name="Txt_Direccion_Entidad"]').val(response.Txt_Direccion_Entidad);
      
      url = base_url + 'HelperController/getPaises';
      $.post( url , function( responsePais ){
        $( '#cbo-Paises' ).html('');
        for (var i = 0; i < responsePais.length; i++){
          selected = '';
          if(response.ID_Pais == responsePais[i].ID_Pais)
            selected = 'selected="selected"';
          $( '#cbo-Paises' ).append( '<option value="' + responsePais[i].ID_Pais + '" ' + selected + '>' + responsePais[i].No_Pais + '</option>' );
        }
      }, 'JSON');
      
      url = base_url + 'HelperController/getDepartamentos';
      $.post( url, {ID_Pais : response.ID_Pais}, function( responseDepartamentos ){
        $( '#cbo-Departamentos' ).html('');
        for (var i = 0; i < responseDepartamentos.length; i++){
          selected = '';
          if(response.ID_Departamento == responseDepartamentos[i].ID_Departamento)
            selected = 'selected="selected"';
          $( '#cbo-Departamentos' ).append( '<option value="' + responseDepartamentos[i].ID_Departamento + '" ' + selected + '>' + responseDepartamentos[i].No_Departamento + '</option>' );
        }
      }, 'JSON');
      
      url = base_url + 'HelperController/getProvincias';
      $.post( url, {ID_Departamento : response.ID_Departamento}, function( responseProvincia ){
        $( '#cbo-Provincias' ).html('');
        for (var i = 0; i < responseProvincia.length; i++){
          selected = '';
          if(response.ID_Provincia == responseProvincia[i].ID_Provincia)
            selected = 'selected="selected"';
          $( '#cbo-Provincias' ).append( '<option value="' + responseProvincia[i].ID_Provincia + '" ' + selected + '>' + responseProvincia[i].No_Provincia + '</option>' );
        }
      }, 'JSON');
      
      url = base_url + 'HelperController/getDistritos';
      $.post( url, {ID_Provincia : response.ID_Provincia}, function( responseDistrito ){
        //$( '#modal-loader' ).modal('hide');
        $( '#cbo-Distritos' ).html('');
        for (var i = 0; i < responseDistrito.length; i++){
          selected = '';
          if(response.ID_Distrito == responseDistrito[i].ID_Distrito)
            selected = 'selected="selected"';
          $( '#cbo-Distritos' ).append( '<option value="' + responseDistrito[i].ID_Distrito + '" ' + selected + '>' + responseDistrito[i].No_Distrito + '</option>' );
        }
      }, 'JSON');

      $('[name="Nu_Dias_Credito"]').val(response.Nu_Dias_Credito);
      $('[name="Nu_Telefono_Entidad"]').val(response.Nu_Telefono_Entidad);
      $('[name="Nu_Celular_Entidad"]').val(response.Nu_Celular_Entidad);
      $('[name="Txt_Email_Entidad"]').val(response.Txt_Email_Entidad);
      
      $( '#cbo-Estado' ).html( '' );
      for (var i = 0; i < 2; i++){
        selected = '';
        if(response.Nu_Estado == i)
          selected = 'selected="selected"';
        $( '#cbo-Estado' ).append( '<option value="' + i + '" ' + selected + '>' + (i == 0 ? 'Inactivo' : 'Activo') + '</option>' );
      }
      
      $('[name="Nu_Celular_Entidad"]').val(response.Nu_Celular);
    },
    error: function (jqXHR, textStatus, errorThrown) {
      //$( '#modal-loader' ).modal('hide');
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

function form_Proveedor(){
  if (accion_proveedor == 'add_proveedor' || accion_proveedor == 'upd_proveedor'){ 
    if ( $( '#cbo-TiposDocumentoIdentidad' ).val() != 2 && $( '#txt-Nu_Documento_Identidad' ).val().length == 0){
  		$( '#txt-Nu_Documento_Identidad' ).closest('.form-group').find('.help-block').html('Ingresar datos');
  		$( '#txt-Nu_Documento_Identidad' ).closest('.form-group').removeClass('has-success').addClass('has-error');
  	} else if ( $( '#cbo-TiposDocumentoIdentidad' ).val() != 2 && $('[name="No_Entidad"]').val().length == 0 ){
  		$( '#txt-Nu_Documento_Identidad' ).closest('.form-group').find('.help-block').html('Ingresar nombre(s)');
  		$( '#txt-Nu_Documento_Identidad' ).closest('.form-group').removeClass('has-success').addClass('has-error');
  	} else if ( $( '#cbo-TiposDocumentoIdentidad' ).val() != 1 && $( '#cbo-TiposDocumentoIdentidad' ).val() != 2 && ($( '#cbo-TiposDocumentoIdentidad' ).find(':selected').data('nu_cantidad_caracteres') != $( '#txt-Nu_Documento_Identidad').val().length) ) {
      $( '#txt-Nu_Documento_Identidad' ).closest('.form-group').find('.help-block').html('Debe ingresar ' + $( '#cbo-TiposDocumentoIdentidad' ).find(':selected').data('nu_cantidad_caracteres') + ' dígitos' );
  	  $( '#txt-Nu_Documento_Identidad' ).closest('.form-group').removeClass('has-success').addClass('has-error');
    } else {
      $( '#btn-save' ).text('');
      $( '#btn-save' ).attr('disabled', true);
      $( '#btn-save' ).append( 'Guardando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
  
      //$( '#modal-loader' ).modal('show');
      
      url = base_url + 'Logistica/ReglasLogistica/ProveedorController/crudProveedor';
    	$.ajax({
        type		  : 'POST',
        dataType	: 'JSON',
    		url		    : url,
    		data		  : $('#form-Proveedor').serialize(),
    		success : function( response ){
    		  //$( '#modal-loader' ).modal('hide');
    		  
    	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
      	  $( '#modal-message' ).modal('show');
    		  
    		  if (response.status == 'success'){
		        accion_proveedor = '';
		    
    		    $( '#form-Proveedor' )[0].reset();
            $( '.div-AgregarEditar' ).hide();
            $( '.div-Listar' ).show();
      	    $( '.modal-message' ).addClass(response.style_modal);
      	    $( '.modal-title-message' ).text(response.message);
      	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
      	    reload_table_proveedor();
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
          //$( '#modal-loader' ).modal('hide');
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
}

function eliminarProveedor(ID, accion_proveedor){
  var $modal_delete = $( '#modal-message-delete' );
  $modal_delete.modal('show');
  
  $( '#btn-cancel-delete' ).off('click').click(function () {
    $modal_delete.modal('hide');
  });

  $( '#btn-save-delete' ).off('click').click(function () {
    eliminarData_Proveedor($modal_delete, ID);
  });
}

function reload_table_proveedor(){
  table_proveedor.ajax.reload(null,false);
}

function eliminarData_Proveedor($modal_delete, ID){
  //$( '#modal-loader' ).modal('show');
  
  url = base_url + 'Logistica/ReglasLogistica/ProveedorController/eliminarProveedor/' + ID;
  $.ajax({
    url       : url,
    type      : "GET",
    dataType  : "JSON",
    success: function( response ){
      //$( '#modal-loader' ).modal('hide');
      
      $modal_delete.modal('hide');
	    $( '.modal-message' ).removeClass('modal-danger modal-warning modal-success');
  	  $( '#modal-message' ).modal('show');
		  
		  if (response.status == 'success'){
		    accion_proveedor='';
		    
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1100);
  	    reload_table_proveedor();
		  } else {
		    accion_proveedor='';
  	    $( '.modal-message' ).addClass(response.style_modal);
  	    $( '.modal-title-message' ).text(response.message);
  	    setTimeout(function() {$('#modal-message').modal('hide');}, 1500);
		  }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      accion_proveedor='';
      //$( '#modal-loader' ).modal('hide');
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