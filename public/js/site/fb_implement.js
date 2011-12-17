$( document ).ready( function() {

	$( '#fb-login' ).click( function() {
		FB.login( handleSessionResponseConnect, { scope: 'email' } );

		return false;
	});

	$( '#fb-add-connect' ).click( function() {
		FB.login( handleSessionResponseAddConnect, { scope: 'email' } );

		return false;
	});

	$( '#fb-disconnect' ).click( function() {
		FB.login( handleSessionResponseRemoveConnect );
	
		return false;
	});
});

// Login/Sign-Up
var ajax = false;

function handleSessionResponseConnect(response) {
	if (!response.authResponse) {
		return;
	}

	FB.api( '/me', function( user ) {
		var data = {};

		data['uid'] = data['fb_uid'] = (user.id ? user.id : '');
		data['name'] = ( user.name ? user.name : '' );
		data['username'] = ( user.username ? user.username : '' );
		data['email'] = ( user.email ? user.email : '' );
		data['pic'] = 'http://graph.facebook.com/' + data['uid'] + '/picture';
		data['pic_big'] = 'http://graph.facebook.com/' + data['uid'] + '/picture?type=large';

	    $.ajax({
			url: '/user/fb_connect/',
			type: 'POST',
			data: data,
			dataType: 'json',
			success: function(data){
				if( data.logged ) {
					parent.window.location.href = '/';
				} else if( data.error ){
					window.location = "/user/fb_error/";
				} else {
					window.location = "/user/fb_sign_up/"; 
				}
			}
		});
	});
}

// Add connection
function handleSessionResponseAddConnect(response) {
	if (!response.authResponse) {
		return;
	}

	FB.api( '/me', function( user ) {
		var data = {};
		
		data['uid'] = (user.id ? user.id : '');
		data['name'] = ( user.name ? user.name : '' );
		data['username'] = ( user.username ? user.username : '' );
		data['email'] = ( user.email ? user.email : '' );
		data['pic'] = 'http://graph.facebook.com/' + user.username + '/picture';
		data['pic_big'] = 'http://graph.facebook.com/' + user.username + '/picture?type=large';

		$.ajax({
			url: '/user/add_facebook/',
			type: 'POST',
			data: data,
			dataType: 'json',
			success: function( data ) {
				document.location = '/user/profile/';
			}
		});
	});
}

// Remove connection
function handleSessionResponseRemoveConnect(response) {
	if (!response.authResponse) {
		return;
	}

	FB.api( '/me', function( user ) {
		var data = {};

		$.ajax({
			url: '/user/remove_facebook/',
			type: 'POST',
			data: data,
			dataType: 'json',
			success: function( data ) {
				document.location = '/user/profile/';
			}
		});
		
		return false;
	});
}