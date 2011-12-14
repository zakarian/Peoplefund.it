$( document ).ready( function() {

	$( '#fb-login' ).click( function() {
		FB.login( handleSessionResponseConnect, { perms: 'email' } );

		return false;
	});

	$( '#fb-add-connect' ).click( function() {
		FB.login( handleSessionResponseAddConnect, { perms: 'email' } );

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
	if (!response.session) {
		return;
	}

	FB.api( '/me', function( user ) {
		var data = {};

		data['uid'] = data['fb_uid'] = FB.getSession().uid;
		data['name'] = ( user.name ? user.name : '' );
		data['username'] = ( user.username ? user.username : '' );
		data['email'] = ( user.email ? user.email : '' );
		data['pic'] = 'http://graph.facebook.com/' + data['uid'] + '/picture';
		data['pic_big'] = 'http://graph.facebook.com/' + data['uid'] + '/picture?type=large';
		data['session_key'] = FB.getSession().session_key;

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
	if (!response.session) {
		return;
	}

	FB.api( '/me', function( user ) {
		var data = {};
		
		data['uid'] = FB.getSession().uid;
		data['name'] = ( user.name ? user.name : '' );
		data['username'] = ( user.username ? user.username : '' );
		data['email'] = ( user.email ? user.email : '' );
		data['pic'] = 'http://graph.facebook.com/' + user.username + '/picture';
		data['pic_big'] = 'http://graph.facebook.com/' + user.username + '/picture?type=large';
		data['session_key'] = FB.getSession().session_key;

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
	if (!response.session) {
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