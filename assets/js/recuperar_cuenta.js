$( function() {
    
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
});

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
