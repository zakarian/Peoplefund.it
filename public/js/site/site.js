$(document).ready(function() {
	var clearAllValues = new Array('.clear-text');
	for(var i = 0; i < clearAllValues.length; i++) {
		var object = $(clearAllValues[i]);
		var z = 0; $(object).each(function(){
			var value = object.eq(z).val();
			clearValues(object.eq(z), value);
		z++; });
	}
	
	var focusAllStyle = new Array('.focus-style');
	for(var i = 0; i < focusAllStyle.length; i++) {
		var object = $(focusAllStyle[i]);
		var z = 0; $(object).each(function(){
			focusStyle(object.eq(z));
		z++; });
	}	
	
	$('#header li').hover(function() {
		$(this).children('ul').show();
		if($(this).children('ul').find('li').length > 0) 
			$(this).addClass('submenu');
		$('#header ul ul li').hover(function() {
			$(this).parent().parent().addClass('submenu');
		}, function() {
			$(this).parent().parent().addClass('submenu');
		});	
	}, function() {
		$(this).children('ul').hide();
		$(this).removeClass('submenu');
	});
	
});

function FitToContent(id, maxHeight) {
	var text = id && id.style ? id : document.getElementById(id);
	if ( !text )
		return;

	var adjustedHeight = text.clientHeight;
	if ( !maxHeight || maxHeight > adjustedHeight ) {
		adjustedHeight = Math.max(text.scrollHeight, adjustedHeight);
		if ( maxHeight )
			adjustedHeight = Math.min(maxHeight, adjustedHeight);
		if ( adjustedHeight > text.clientHeight )
			text.style.height = adjustedHeight + "px";
	}
}

function focusStyle(object) {
	object.focus(function() {
		$(this).addClass('focusStyle');
	});
	
	object.blur(function() {
		$(this).removeClass('focusStyle');
	});	
} 

function clearValues(object, value) {
	object.focus(function() {
		if ($(this).val() == value) {
			object.val('');
			if($(this).hasClass('type-password'))
				document.getElementById($(this).attr('id')).type = 'password';
		}
	});
	
	object.blur(function() {
		if ($(this).val() == '') {
			object.val(value);
			if($(this).hasClass('type-password'))
				document.getElementById($(this).attr('id')).type = 'text';
		}
	});	
} 

// Check projects search form - keywords
function checkSearchKeywords(idfield){
	if($("#"+idfield).val() == ""){
		$("#"+idfield).addClass("error");
		return false;
	} else {
		$("#"+idfield).removeClass("error");
		return true;
	}
}

// Check projects search form - category
function checkSearchCategory(idfield){
	if($("#"+idfield).val() == ""){
		$("#"+idfield).addClass("error");
		return false;
	} else {
		$("#"+idfield).removeClass("error");
		return true;
	}
}

// Check projects search form - string
function checkSearchString(idfield){
	if($("#"+idfield).val() == ""){
		$("#"+idfield).addClass("error");
		return false;
	} else {
		$("#"+idfield).removeClass("error");
		return true;
	}
}

// Will show user wall events
function showMorePosts(iduser){
	
	// Show loader
	$("#loading_events").show();
	
	// Sent params
	var from = $("#from").val();
	var count = 5;
	
	// Make request
	$.post('/user/get_events/', { from: from, count: count, iduser: iduser }, function(data) {

		// If no more events are found - remove older posts link
		if(data == ""){
			$(".older-posts").remove();
		}
	
		// Increment counter
		var newfrom = parseInt($("#from").val()) + 5;
		$("#from").val(newfrom);
		
		// Add data to the wall
		$("#activity_items").append(data);
		
		// Hide loader
		$("#loading_events").hide();
	});
}

// Project comments and updates - show comments
function showAllUpdateComments(idupdate){
	if($(".hidden_comments_"+idupdate).is(":visible")){
		$(".hidden_comments_"+idupdate).hide();
	} else {
		$(".hidden_comments_"+idupdate).show();
	}
}

var from = 0;
// Show more project updates and comments
function showMoreProjectUpdatesAndComments(slug, idproject){

	// Show loader
	$("#loading_events").show();
	
	// Sent params
	var count = 5;
	from = from + 5;
	// Make request
	$.post('/'+slug+'/comments/', { from: from, count: count, idproject: idproject }, function(data) {

		// If no more events are found - remove older posts link
		if(data.trim() == ""){
			$(".older-posts").remove();
		}
	
		//// Add data to the wall
		$("#items").append(data);
		
		// Hide loader
		$("#loading_events").hide();
	});
}

// Submit form on ENTER key press
function submitEnter(myfield, e){
	var keycode;
	if (window.event) 
		keycode = window.event.keyCode;
	else if (e) 
		keycode = e.which;
	else 
		return true;

	if (keycode == 13){
	   myfield.form.submit();
	   return false;
    } else {
	   return true;
	}
}

// Add update comment
function submitUpdateComment(myfield, e, idupdate, user_slug, username, iduser, ext){
	var keycode;
	if (window.event) 
		keycode = window.event.keyCode;
	else if (e) 
		keycode = e.which;
	else 
		return true;

	if (keycode == 13){
		$.post('/projects/add_update_comment/', { idupdate: idupdate, text: myfield.value }, function(data) {
			
			var html = '<div class="item">';
				html += '<div class="picture">';
					html += '<a href="/user/'+user_slug+'/" title="'+username+'">';
						if(ext){
							html += '<img src="/uploads/users/'+iduser+'.'+ext+'" alt="" />';
						} else {
							html += '<img src="/img/site/delete/PF_homepage_v05_19.png" alt="" />';
						}
					html += '</a>';
				html += '</div>';
				html += '<div class="details">';
					html += '<a href="/user/'+user_slug+'/" title="'+username+'">'+username+'</a>&nbsp;';
					html += '<p>'+myfield.value+'</p>';
					html += '<br /><span class="date">BEFORE 1 SECOND</span>';
				html += '</div>';
				html += '<div class="clear"></div>';
			html += '</div>';
								
			$("."+idupdate+"_comments").append(html);
			myfield.value = "Post a comment..."
		});

    } else {
	   return true;
	}
}

// Make a slug from string
function slugify(t) {
	/*t = t.replace(/�/g, "a");	t = t.replace(/�/g, "b");	t = t.replace(/�/g, "v");	t = t.replace(/�/g, "g");	t = t.replace(/�/g, "d");	t = t.replace(/�/g, "e");	t = t.replace(/�/g, "j");	t = t.replace(/�/g, "z");	t = t.replace(/�/g, "i");	t = t.replace(/�/g, "i");	t = t.replace(/�/g, "k");	t = t.replace(/�/g, "l");	t = t.replace(/�/g, "m");	t = t.replace(/�/g, "n");	t = t.replace(/�/g, "o");	t = t.replace(/�/g, "p");	t = t.replace(/�/g, "r");	t = t.replace(/�/g, "s");	t = t.replace(/�/g, "t");	t = t.replace(/�/g, "u");	t = t.replace(/�/g, "f");	t = t.replace(/�/g, "h");	t = t.replace(/�/g, "c");	t = t.replace(/�/g, "ch");	t = t.replace(/�/g, "sh");	t = t.replace(/�/g, "sht");	t = t.replace(/�/g, "i");	t = t.replace(/�/g, "y");	t = t.replace(/�/g, "yu");	t = t.replace(/�/g, "ya");
	t = t.replace(/�/g, "a");	t = t.replace(/�/g, "b");	t = t.replace(/�/g, "v");	t = t.replace(/�/g, "g");	t = t.replace(/�/g, "d");	t = t.replace(/�/g, "e");	t = t.replace(/�/g, "j");	t = t.replace(/�/g, "z");	t = t.replace(/�/g, "i");	t = t.replace(/�/g, "i");	t = t.replace(/�/g, "k");	t = t.replace(/�/g, "l");	t = t.replace(/�/g, "m");	t = t.replace(/�/g, "n");	t = t.replace(/�/g, "o");	t = t.replace(/�/g, "p");	t = t.replace(/�/g, "r");	t = t.replace(/�/g, "s");	t = t.replace(/�/g, "t");	t = t.replace(/�/g, "u");	t = t.replace(/�/g, "f");	t = t.replace(/�/g, "h");	t = t.replace(/�/g, "c");	t = t.replace(/�/g, "ch");	t = t.replace(/�/g, "sh");	t = t.replace(/�/g, "sht");	t = t.replace(/�/g, "i");	t = t.replace(/�/g, "y");	t = t.replace(/�/g, "yu");	t = t.replace(/�/g, "ya");*/
    t = t.replace(/[^a-zA-Z0-9\ _-]/g, "");
    t = t.replace(/\s/g, "-");
    t = t.replace(/-+/g, "-");
    t = t.replace(/_+/g, "_");
    t = t.replace(/[_-]*$/, "");
    t = t.toLowerCase();
    return t;
}

// Make slug
function fillSlug(from, to){
	var from_value = $("#"+from).val();
	var to_value = $("#"+to).val();

	if(to_value == ""){
		$("#"+to).val(slugify(from_value));
	}
}

// Limit text area size
function limitTextArea(idtextarea, idcounter, chars){
	if ($("#"+idtextarea).val().length > chars)
		$("#"+idtextarea).val($("#"+idtextarea).val().substring(0, chars));
	else 
		$("#"+idcounter).html(chars - $("#"+idtextarea).val().length);
}

// Limit text area size
function limitTextAreaWords(idtextarea, idcounter, words){
	if ($("#"+idtextarea).val().split(" ").length > words)
		$("#"+idtextarea).val($("#"+idtextarea).val().substring(0, $("#"+idtextarea).val().split(" ", words).toString().length));
	else 
		$("#"+idcounter).html(words - $("#"+idtextarea).val().split(" ").length);
}

// URL encode JS function
function js_urlencode (str) {
	str = escape(str);
	return str.replace(/[*+\/@]|%20/g,
		function (s) {
			switch (s) {
				case "*": s = "%2A"; break;
				case "+": s = "%2B"; break;
				case "/": s = "%2F"; break;
				case "@": s = "%40"; break;
				case "%20": s = "+"; break;
			}
			return s;
		}
	);
}
