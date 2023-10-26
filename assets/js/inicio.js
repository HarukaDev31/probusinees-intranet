$( function() {
	//$('.select2').select2();
	//$('[data-mask]').inputmask();

	$('#form-login')[0].reset();
	$('#form-login_empresa')[0].reset();
	$('#form-recuperar_cuenta')[0].reset();

	// valid email pattern
	var eregex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	$.validator.addMethod("validemail", function( value, element ) {
		return this.optional( element ) || eregex.test( value );
	});

	$('.input-username').on('input', function () {
		this.value = this.value.replace(/ /g, '');
	});

	$('.input-numeros_letras').on('input', function () {
		this.value = this.value.replace(/[^a-zA-Z0-9]/g, '');
	});

    $( ".form-control" ).focus(function() {
        $(this).prev('.input-group-addon').addClass('input-group-addon-focus');
        $(this).next('.input-group-addon').addClass('input-group-addon-focus');
    });
    
    $( ".form-control" ).focusout(function() {
        $(this).prev('.input-group-addon-focus').removeClass().addClass('input-group-addon');
        $(this).next('.input-group-addon-focus').removeClass().addClass('input-group-addon');
    });
	
	$(".toggle-password").click(function() {
		$(this).toggleClass("fa-eye fa-eye-slash");
	    var $pwd = $(".pwd");
	    if ($pwd.attr('type') == "password") {
	        $pwd.attr('type', 'text');
	    } else {
	        $pwd.attr('type', 'password');
	    }
	});
    
	$(".toggle-recuperar_password_1").click(function() {
		$(this).toggleClass("fa-eye fa-eye-slash");
	    var $pwd = $(".pwd_1");
	    if ($pwd.attr('type') == "password") {
	        $pwd.attr('type', 'text');
	    } else {
	        $pwd.attr('type', 'password');
	    }
	});
    
	$(".toggle-recuperar_password_2").click(function() {
		$(this).toggleClass("fa-eye fa-eye-slash");
	    var $pwd = $(".pwd_2");
	    if ($pwd.attr('type') == "password") {
	        $pwd.attr('type', 'text');
	    } else {
	        $pwd.attr('type', 'password');
	    }
	});

    $("#form-login").validate({
		rules:{
		    No_Usuario: {
				required: true
			},
			No_Password: {
				required: true
			},
		},
		messages:{
		    No_Usuario: {
				required : "Ingresar correo",
			},
			No_Password: {
				required : "Ingresar contraseña",
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
		submitHandler: formLogin
	});

	$( '#div-login' ).removeClass('div-ocultar');
	$( '#div-empresa' ).addClass('div-ocultar');
	$( '#div-recuperar_cuenta' ).addClass('div-ocultar');
	$( '#div-crear_cuenta' ).addClass('div-ocultar');
	$( '#div-crear_cuenta_cuentas_bancarias' ).addClass('div-ocultar');
  
    $("#form-login_empresa").validate({
		rules:{
		    ID_Empresa: {
				required: true
			},
		},
		messages:{
		    ID_Empresa: {
				required : "Seleccionar empresa",
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
		submitHandler: formLoginEmpresaxOrganizacion
	});
	
    $("#form-recuperar_cuenta").validate({
		rules:{
			Txt_Email_Recovery: {
				required: true,
				validemail: true
			},
		},
		messages:{
			Txt_Email_Recovery:{
				required: "Ingresar correo",
				validemail : "Ingresar un correo válido"
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
		submitHandler: form_recuperar_cuenta
	});

	
    $("#form-cambiar_clave").validate({
		rules:{
			No_Password: {
				required: true,
			},
			RNo_Password: {
				required: true,
				equalTo: '#No_Password'
			},
		},
		messages:{
			No_Password:{
				required: "Ingresar contraseña",
			},
			RNo_Password:{
				required: "Repetir contraseña",
				equalTo: "Las contraseñas no coinciden"
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
		submitHandler: form_cambiar_clave
	});
		
	$(".btn-login_return").click(function () {
		$('.panel-heading').removeClass('hidden-xs');
		
		$('#div-login').removeClass('div-ocultar');
		$('#div-empresa').addClass('div-ocultar');
		$('#div-recuperar_cuenta').addClass('div-ocultar');
		$('#div-crear_cuenta').addClass('div-ocultar');
		$('#div-crear_cuenta_cuentas_bancarias').addClass('div-ocultar');
		
		$( '.div-msg' ).html('');
		
		$( '#btn-login' ).text('Iniciar sesión');
	    $( '#btn-login' ).attr('disabled', false);
	});
	
	$("#btn-recuperar_cuenta").click(function () {
		$('.help-block').empty();
		$('.form-group').removeClass('has-error');
		$('#form-recuperar_cuenta')[0].reset();

		$('#div-login').addClass('div-ocultar');
		$('#div-empresa').addClass('div-ocultar');
		$('#div-recuperar_cuenta').removeClass('div-ocultar');
		$('#div-crear_cuenta').addClass('div-ocultar');
		$('#div-crear_cuenta_cuentas_bancarias').addClass('div-ocultar');
		
		$( '.div-msg' ).html('');
	})

	$('#cbo-tipo_proveedor_fe').change(function () {
		$('[name="Nu_Documento_Identidad"]').val('');
		$('#txt-Nu_Documento_Identidad').attr('placeholder', 'RUC');
		$('#txt-Nu_Documento_Identidad').attr('maxlength', 11);

		$('#modal-tipo_gestion').modal('show');

		$('.modal-header-title').html('<strong>INTERNO</strong>');
		if($(this).val()==1)
			$('.modal-header-title').html('<strong>PSE</strong>');
		else if($(this).val()==2)
			$('.modal-header-title').html('<strong>SUNAT</strong>');

		if ($(this).val() == 1 || $(this).val() == 2) {
			$('.modal-p-body-interno').hide();
			$('.modal-p-body-sunat_o_pse').show();

			$('#cbo-TiposDocumentoIdentidad').val('4');
			$('#txt-Nu_Documento_Identidad').attr('placeholder', 'RUC');
			$('#txt-Nu_Documento_Identidad').attr('maxlength', 11);
		} else {			
			$('.modal-p-body-interno').show();
			$('.modal-p-body-sunat_o_pse').hide();
		}
	});

	$(document).on('click', '#radio-sunat', function () {
		$('[name="Nu_Documento_Identidad"]').val('');

		$('#modal-tipo_gestion').modal('show');

		$('.modal-header-title').html('<strong>SUNAT</strong>');
		
		$('.modal-p-body-interno').hide();
		$('.modal-p-body-sunat_o_pse').show();

		$('#cbo-TiposDocumentoIdentidad').val('4');
		$('.label-valortdi').text('RUC');
		$('#txt-Nu_Documento_Identidad').attr('placeholder', 'Ingresar');
		$('#txt-Nu_Documento_Identidad').attr('maxlength', 11);		
	})

	$(document).on('click', '#radio-interno', function () {
		$('[name="Nu_Documento_Identidad"]').val('');

		$('#modal-tipo_gestion').modal('show');

		$('.modal-header-title').html('<strong>INTERNO</strong>');
		
		$('.modal-p-body-interno').show();
		$('.modal-p-body-sunat_o_pse').hide();

		$('#cbo-TiposDocumentoIdentidad').val('4');
		$('.label-valortdi').text('RUC');
		$('#txt-Nu_Documento_Identidad').attr('placeholder', 'Ingresar');
		$('#txt-Nu_Documento_Identidad').attr('maxlength', 11);		
	})

	/* Tipo Documento Identidad */
	$('#cbo-TiposDocumentoIdentidad').change(function () {
		$('[name="Nu_Documento_Identidad"]').val('');

		$('.label-valortdi').text('RUC');
		if ($(this).val() == 2) {//DNI
			$('.label-valortdi').text('DNI');
			$('#txt-Nu_Documento_Identidad').attr('placeholder', 'Ingresar');
			$('#txt-Nu_Documento_Identidad').attr('maxlength', $(this).find(':selected').data('nu_cantidad_caracteres'));
		} else if ($(this).val() == 4) {//RUC
			$('.label-valortdi').text('RUC');
			$('#txt-Nu_Documento_Identidad').attr('placeholder', 'Ingresar');
			$('#txt-Nu_Documento_Identidad').attr('maxlength', $(this).find(':selected').data('nu_cantidad_caracteres'));
		} else {
			$('.label-valortdi').text('OTROS');
			$('#txt-Nu_Documento_Identidad').attr('placeholder', 'Ingresar');
			$('#txt-Nu_Documento_Identidad').attr('maxlength', $(this).find(':selected').data('nu_cantidad_caracteres'));
		}
	})

	$('.panel-heading').removeClass('hidden-xs');	
	$( '#cbo-Empresas' ).change(function(){
		$( '#modal-loader' ).modal('show');
		url = base_url + 'HelperController/getOrganizaciones';
		var arrParams = {
		  iIdEmpresa : $(this).val(),
		};
		$.post( url, arrParams, function( response ){
			var iLength = response.length;
			$( '#cbo-organizacion' ).html('<option value="0" selected="selected">- Seleccionar -</option>');
			for (var i = 0; i < iLength; i++)
				$( '#cbo-organizacion' ).append( '<option value="' + response[i].ID_Organizacion + '">' + response[i].No_Organizacion + '</option>' );    
			$( '#modal-loader' ).modal('hide');
		}, 'JSON');
	});
	
	$( '#cbo-organizacion' ).change(function(){
		if ( $(this).val() > 0 ) {
			formLoginEmpresaxOrganizacion();
		}
	});
});

function formLogin() {
    $( '#btn-login' ).text('');
    $( '#btn-login' ).attr('disabled', true);
    $( '#btn-login' ).append( 'Verificando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
	
	$('#btn-crear_cuenta').attr('disabled', true);
	$('#btn-recuperar_cuenta').attr('disabled', true);
	
    var url = base_url + 'LoginController/post';
    $.ajax({
	    type : 'POST',
	    dataType : 'JSON',
		url : url,
		data : $('#form-login').serialize(),
		success : function( response ){
			$( '.div-msg' ).html('');
			if ( response.sStatus == 'success' ) {
				if ( response.iCantidadAcessoUsuario == 1 ) {// Usuario solo pertenece a 1 empresa y organizacion
					window.location = base_url + 'InicioController';
				} else {// Multiple empresa y organizacion
					$( '#div-login' ).addClass('div-ocultar');
					$( '#div-empresa' ).removeClass('div-ocultar');
					$( '#btn-login_empresa' ).hide();

					//GET Empresas
					url = base_url + 'HelperController/getEmpresasLogin';
					$.post(url, { iIdEmpresa: response.iIdEmpresa}, function( response ){
						$( '#cbo-Empresas' ).html('<option value="" selected="selected">- Seleccionar -</option>');
						for (var i = 0; i < response.length; i++){
							$( '#cbo-Empresas' ).append( '<option value="' + response[i].ID_Empresa + '">' + response[i].No_Empresa + '</option>' );
						}

						$( '#btn-login' ).text('Iniciar sesión');
						$( '#btn-login' ).attr('disabled', false);
					}, 'JSON');
		
					//Empresa
					$('#txt-usuario_empresa').val($('#txt-usuario').val());
					$('#txt-password_empresa').val($('#txt-password').val());
				}
			} else {
				$( '.div-msg' ).slideDown('fast', function(){
			      	$( '.div-msg' ).html( '<div class="alert alert-' + response.sStatus + '">' + response.sMessage +  '</div>' );
				});

	    		$( '#btn-login' ).text('Iniciar sesión');
			    $( '#btn-login' ).attr('disabled', false);
			}
			$('#btn-crear_cuenta').attr('disabled', false);
			$( '#btn-recuperar_cuenta' ).attr('disabled', false);
		}
	})
	.fail(function(jqXHR, textStatus, errorThrown) {
		$( '#btn-login' ).text('Iniciar sesión');
		$( '#btn-login' ).attr('disabled', false);

		$('#btn-crear_cuenta').attr('disabled', false);
		$( '#btn-recuperar_cuenta' ).attr('disabled', false);
		//Message for developer
		console.log(jqXHR.responseText);
	});
}

function formLoginEmpresaxOrganizacion() {
    $( '#btn-login_empresa' ).text('');
    $( '#btn-login_empresa' ).attr('disabled', true);
	$( '#btn-login_empresa' ).append( 'Verificando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
	
	$( '.btn-login_return' ).attr('disabled', true);

    var url = base_url + 'LoginController/post';
    $.ajax({
	    type		: 'POST',
	    dataType	: 'JSON',
		url		    : url,
		data		: $('#form-login_empresa').serialize(),
		success : function( response ){
			$( '.div-msg' ).html('');
			if ( response.sStatus == 'success' ) {
				window.location = base_url + 'InicioController';
			} else {
				$( '.div-msg' ).slideDown('fast', function(){
			      	$( '.div-msg' ).html( '<div class="alert alert-' + response.sStatus + '">' + response.sMessage +  '</div>' );
				});

	    		$( '#btn-login' ).text('Iniciar sesión');
			    $( '#btn-login' ).attr('disabled', false);
			}
			$( '.btn-login_return' ).attr('disabled', false);
		}		
	})
	.fail(function(jqXHR, textStatus, errorThrown) {
		$( '#btn-login' ).text('Iniciar sesión');
		$( '#btn-login' ).attr('disabled', false);

		$( '.btn-login_return' ).attr('disabled', false);

		//Message for developer
		console.log(jqXHR.responseText);
	});
}

function form_recuperar_cuenta() {
    $( '#btn-send_correo' ).text('');
    $( '#btn-send_correo' ).attr('disabled', true);
    $( '#btn-send_correo' ).append( 'Verificando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
    var url = base_url + 'LoginController/verificar_email';
    $.ajax({
	    type		: 'POST',
	    dataType	: 'JSON',
		url		    : url,
		data		: $('#form-recuperar_cuenta').serialize(),
		success : function( response ){
			if(response.status == "error") {
				$( '.div-msg' ).slideDown('fast', function(){
			      	$( '.div-msg' ).html(
			      	'<div class="alert alert-danger">'
			      		+'<i class="fa fa-exclamation-circle" aria-hidden="true"></i> '
			      			+ response.message + 
			      	'</div>'
			      	);
				});
			} else if(response.status == "warning") {
				$( '.div-msg' ).slideDown('fast', function(){
			      	$( '.div-msg' ).html(
			      	'<div class="alert alert-warning">'
			      		+'<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> '
			      			+ response.message + 
			      	'</div>'
			      	);
				});
	        } else {
				$( '.div-msg' ).slideDown('fast', function(){
			      	$( '.div-msg' ).html(
			      	'<div class="alert alert-success">'
			      		+'<i class="fa fa-check" aria-hidden="true"></i> '
			      			+ response.message + 
			      	'</div>'
			      	);
				});
	        }
    		$( '#btn-send_correo' ).text('Recuperar cuenta');
		    $( '#btn-send_correo' ).attr('disabled', false);
		}
	})
	.fail(function (jqXHR, textStatus, errorThrown) {
		$( '#btn-send_correo' ).text('Recuperar cuenta');
		$( '#btn-send_correo' ).attr('disabled', false);
	});
}

function form_cambiar_clave() {
    $( '#btn-cambiar_clave' ).text('');
    $( '#btn-cambiar_clave' ).attr('disabled', true);
    $( '#btn-cambiar_clave' ).append( 'Cambiando <i class="fa fa-refresh fa-spin fa-lg fa-fw"></i>' );
    var url = base_url + 'LoginController/cambiar_clave';
    $.ajax({
	    type		: 'POST',
	    dataType	: 'JSON',
		url		    : url,
		data		: $('#form-cambiar_clave').serialize(),
		success : function( response ){
			if(response.status == "error") {
				$( '.div-msg' ).slideDown('fast', function(){
			      	$( '.div-msg' ).html(
			      	'<div class="alert alert-danger">'
			      		+'<i class="fa fa-exclamation-circle" aria-hidden="true"></i> '
			      			+ response.message + 
			      	'</div>'
			      	);
				});
			} else if(response.status == "warning") {
				$( '.div-msg' ).slideDown('fast', function(){
			      	$( '.div-msg' ).html(
			      	'<div class="alert alert-warning">'
			      		+'<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> '
			      			+ response.message + 
			      	'</div>'
			      	);
				});
	        } else {
				$( '.div-msg' ).slideDown('fast', function(){
			      	$( '.div-msg' ).html(
			      	'<div class="alert alert-success">'
			      		+'<i class="fa fa-check" aria-hidden="true"></i> '
			      			+ response.message + 
			      	'</div>'
			      	);
				});
				
				setTimeout(function(){
					window.location.href = base_url;
				}, 1100);
	        }
    		$( '#btn-cambiar_clave' ).text('Cambiar contraseña');
		    $( '#btn-cambiar_clave' ).attr('disabled', false);
		}
	});
}
