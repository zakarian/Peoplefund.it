$(document).ready(function() {

// clear session messages
setTimeout(function() {
	$('div.session-messages').slideUp('fast', function() { $(this).remove(); });
}, 3000);

function ajax(url, success, failure) {
	$.ajax({
		url: url,
		type: 'post',
		dataType: 'json',
		success: function(data) {
			if (data.alert) {
				jAlert(data.alert);
			}
			if ((data == true) || data.success) {
				if (data.redirect) {
					window.location = data.redirect;
					return;
				}
				return success(data);
			}
			if (!data.alert) {
				jAlert('AJAX request failed');
			}
			if (failure) {
				failure(data);
			}
		},
		error: function() {
			jAlert('AJAX request failed');
		}
	});
}

$('.switch a').click(function() {
	var row = $(this).parents("tr:first").get(0);
	
	var url = "/administration/"+fromPage+"/active/";
	if($(this).attr('class') == "turn_on"){
		$.post( url, { active: '0', id: row.id }, function() {
			$("#"+row.id + " .turn_on").addClass('hidden');
			$("#"+row.id + " .turn_off").removeClass('hidden');
		});
	} else {
		$.post( url, { active: '1', id: row.id }, function() {
			$("#"+row.id + " .turn_off").addClass('hidden');
			$("#"+row.id + " .turn_on").removeClass('hidden');
		});
	}

});

$('.switch_picks a').click(function() {
	var row = $(this).parents("tr:first").get(0);
	
	var url = "/administration/"+fromPage+"/picks/";
	if($(this).attr('class') == "turn_on_pick"){
		$.post( url, { active: '0', id: row.id }, function() {
			$("#"+row.id + " .turn_on_pick").addClass('hidden');
			$("#"+row.id + " .turn_off_pick").removeClass('hidden');
		});
	} else {
		$.post( url, { active: '1', id: row.id }, function() {
			$("#"+row.id + " .turn_off_pick").addClass('hidden');
			$("#"+row.id + " .turn_on_pick").removeClass('hidden');
		});
	}

});

$('.switch_section a').click(function() {
	var row = $(this).parents("tr:first").get(0);
	
	var url = "/administration/pages/active_section/";
	if($(this).attr('class') == "turn_on"){
		$.post( url, { active: '0', id: row.id }, function() {
			$("#"+row.id + " .turn_on").addClass('hidden');
			$("#"+row.id + " .turn_off").removeClass('hidden');
		});
	} else {
		$.post( url, { active: '1', id: row.id }, function() {
			$("#"+row.id + " .turn_off").addClass('hidden');
			$("#"+row.id + " .turn_on").removeClass('hidden');
		});
	}

});

$('a[data-ajax-handler].delete').click(function() {
	var item = $(this).closest('.item');
	var url = this.getAttribute('data-ajax-handler');
	var confirm_prompt = this.getAttribute('data-confirm-prompt');
	var do_ajax = function() {
		ajax(url, function(data) { item.remove() })
	};
	if (confirm_prompt) {
		jConfirm(confirm_prompt, '', do_ajax);
	} else {
		do_ajax();
	}
});

$('input[data-autocomplete-source]').each(function() {
	$(this).autocomplete(this.getAttribute('data-autocomplete-source'));
});

$("a.delete").not('[data-ajax-handler]').click(function() {
    var row = $(this).parents("tr:first").get(0);
	var client_id = null, id = row.id.split('-');
	if (id.length == 1) {
		id = id[0].replace(/\D*/, '');
	} else {
		client_id = id[0].replace(/\D*/, '');
		id = id[1].replace(/\D*/, '');
	}
	console.log('id: ' + id + '; client: ' + client_id);

	jConfirm("Are you sure you want to delete this?", '', function(r) {
		if(!r) return false;

		if(!('fromPage' in window))
			url = document.location.toString();
		else
			url = '/administration/' + fromPage + '/delete/'+ id +'/';

		var params = { action: 'delete', id: id };
		if (client_id)
			params['client_id'] = client_id;

		$.post( url, params, function(r) {
			r = r.split(':');
			switch (r[0]) {
				case 'ok':
					$(row).remove();
					break;
				case 'redirect':
					window.location = r[1];
					break;
				case 'alert':
					jAlert(r[1]);
					break;
				case 'failed':
				default:
					jAlert('Could not delete record');
					break;
			}
		});
	});

	return false;
});


$("a.delete_amount").not('[data-ajax-handler]').click(function() {
    var row = $(this).parents("tr:first").get(0);
	var client_id = null, id = row.id.split('-');
	if (id.length == 1) {
		id = id[0].replace(/\D*/, '');
	} else {
		client_id = id[0].replace(/\D*/, '');
		id = id[1].replace(/\D*/, '');
	}
	console.log('id: ' + id + '; client: ' + client_id);

	jConfirm("Are you sure you want to delete this?", '', function(r) {
		if(!r) return false;

		url = '/administration/projects/remove_amount/'+id;

		$.post( url, function(r) {
			r = r.split(':');
			switch (r[0]) {
				case 'ok':
					window.location.reload();
					break;
				default:
					jAlert('Could not delete record');
					break;
			}
		});
	});

	return false;
});

$("a.delete_menu").not('[data-ajax-handler]').click(function() {
    var row = $(this).parents("tr:first").get(0);
	var client_id = null, id = row.id.split('-');
	if (id.length == 1) {
		id = id[0].replace(/\D*/, '');
	} else {
		client_id = id[0].replace(/\D*/, '');
		id = id[1].replace(/\D*/, '');
	}
	console.log('id: ' + id + '; client: ' + client_id);

	jConfirm("Are you sure you want to delete this?", '', function(r) {
		if(!r) return false;

		if(!('fromPage' in window))
			url = document.location.toString();
		else
			url = '/administration/' + fromPage + '/delete/'+ id +'/';

		var params = { action: 'delete', id: id };
		if (client_id)
			params['client_id'] = client_id;

		$.post( url, params, function(r) {
			r = r.split(':');
			switch (r[0]) {
				case 'ok':
					window.location.reload();
					break;
				case 'redirect':
					window.location = r[1];
					break;
				case 'alert':
					jAlert(r[1]);
					break;
				case 'failed':
				default:
					jAlert('Could not delete record');
					break;
			}
		});
	});

	return false;
});

$("a.delete_section").not('[data-ajax-handler]').click(function() {
    var row = $(this).parents("tr:first").get(0);
	var client_id = null, id = row.id.split('-');
	if (id.length == 1) {
		id = id[0].replace(/\D*/, '');
	} else {
		client_id = id[0].replace(/\D*/, '');
		id = id[1].replace(/\D*/, '');
	}
	console.log('id: ' + id + '; client: ' + client_id);

	jConfirm("Are you sure you want to delete this?", '', function(r) {
		if(!r) return false;

		if(!('fromPage' in window))
			url = document.location.toString();
		else
			url = '/administration/pages/delete_section/'+ id +'/';

		var params = { action: 'delete', id: id };
		if (client_id)
			params['client_id'] = client_id;

		$.post( url, params, function(r) {
			r = r.split(':');
			switch (r[0]) {
				case 'ok':
					$(row).remove();
					break;
				case 'redirect':
					window.location = r[1];
					break;
				case 'alert':
					jAlert(r[1]);
					break;
				case 'failed':
				default:
					jAlert('Could not delete record');
					break;
			}
		});
	});

	return false;
});

});