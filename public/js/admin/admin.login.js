$(document).ready(function(){   
	$('form#login-form').fadeIn("normal");
	
	$("#username").focus();
	
	$(".submitLogin").hover( function() {
		$(this).attr('src', '/img/buttonLoginOn.gif');
	}, function() {
		$(this).attr('src', '/img/buttonLoginOff.gif');
	});

	$('form#login-form').submit( function() {
		$('form#login-form').fadeOut("normal");
	});
});