var webroot = '/';
var cur_id = '';
var page_id = '';
var console;
var error;

var lang = 'en';

var siteCategories = new Array();
var formFields = new Array();

var formField;
var itemsPerPage;

var saveAndGo = 1;
var noSave = 0;

if(!console) {
    console = { log: function() {} } 
}

var statusPrefix = '';

Admin = {
	defaults: function() {
		this.webroot = '/';
		this.cur_id = 0;
		this.last_id;
		this.page_id;
		this.no_save = false;
		this.site_categories = new Array();
		this.invalid_slug = false;
		this.position = 'main';
	}
}

String.prototype.unescapeHtml = function () {
	try
	{
		var temp = document.createElement("div");
		temp.innerHTML = this;
		var result = temp.innerHTML;
		temp.removeChild(temp.firstChild)
		return result;
	}
	catch (e)
	{
		return "";
	}
}

function htmlentities(texto){
    //by Micox - elmicoxcodes.blogspot.com - www.ievolutionweb.com
    var i,carac,letra,novo='';
    for(i=0;i<texto.length;i++){
        carac = texto[i].charCodeAt(0);
        if( (carac > 47 && carac < 58) || (carac > 62 && carac < 127) ){
            //se for numero ou letra normal
            novo += texto[i];
        }else{
            novo += "&#" + carac + ";";
        }
    }
    return novo;
}

function html_entity_decode(str)
{
	document.getElementById("htmlconverter").innerHTML = '<textarea id="innerConverter">' + str + '</textarea>';
	var content = document.getElementById("innerConverter").value;
	document.getElementById("htmlconverter").innerHTML = "";
	return content;
}

function chsect( navID ) {
    Section.change( navID );
}

function replaceAll(text, strA, strB)
{
    while ( text.indexOf(strA) != -1)
    {
        text = text.replace(strA,strB);
    }
    return text;
}

Array.prototype.in_array = function( a) {
	
	for( var i = 0; i < this.length; i++ ) {

		if( this[i] == a )
			return true;
	}

	return false;
}

D = {
    img: function img(im, cl, alt) {
        var img = document.createElement('img');
        img.src = (webroot + 'img/' + im);
        img.alt = alt || im;
        img.className = cl;

        return img;
    },

    input: function ( name, value, type ) {
        var i = document.createElement('INPUT');
        i.type = type || 'hidden';
        i.name = name;
        i.value = value;
        i.id = name;

        return i;
    }

}

$(document).ready( function() {
	Admin.defaults();

	$('input.datepicker').datepicker({ dateFormat: 'yy-mm-dd', constrainInput: false });

	$("#loading").ajaxStart(function() { 
		blankCreate();
		$(this).show();
	});
    $("#loading").ajaxStop(function() { 
		blankDestroy();
		$(this).hide();
	});

	var dateFormat = /^\d{2}.\d{2}.\d{4}$/;
	var timeFormat = /^\d{2}:\d{2}$/;

    $("#browse").click( function() {
        popup();

        return false;
    });

    $(".resultsTable tbody tr:odd").addClass('zebra');
    $("#resultsDiv dd:odd").addClass('zebra');
    
	// VALIDATE FIELDS
	$('.number').keypress( function(int) {
		return Validate.numbers(int, false);
	});
	$('form').submit( function() {
		error = 0;

		$('form .validate').each( function() {
			if($(this).val() == '') {
				$(this).addClass('error');
				error = 1;
			} else {
				$(this).removeClass('error');
			}
		});

		if(error > 0) {
			jAlert('Please fill all fields marked in red.');

			return false;
		}
	});
	// END VALIDATE FIELDS
	
	// ADD CORNERS
	if($('#formsEditScreen').size() > 0)
		$('#formsEditScreen fieldset').corner();
	if($('#edit-section-div').size() > 0)
		$('#edit-section-div fieldset').corner();
	// END ADD CORNERS

	// HOVER BUTTONS
	$("#formsEditScreen input.save").hover( function() {
		$(this).attr('src', '/img/buttons/button-save-hover.gif');
	}, function() {
		$(this).attr('src', '/img/buttons/button-save.gif');
	});

	$("#formsEditScreen img.close").hover( function() {
		$(this).attr('src', '/img/buttons/button-close-hover.gif');
	}, function() {
		$(this).attr('src', '/img/buttons/button-close.gif');
	});
	// END HOVER BUTTONS

	// FAST SWITCH
    $(".featured_on").click( function() {
        var row = $(this).parents("tr:first").get(0);

        url = document.location.toString();
		$.post( url, { action: 'featured_off', id: row.id }, function() {
			$("#"+row.id + " .featured_on").addClass('hidden');
			$("#"+row.id + " .featured_off").removeClass('hidden');
		});

		return false;
    });

    $(".featured_off").click( function() {
        var row = $(this).parents("tr:first").get(0);

        url = document.location.toString();
		$.post( url, { action: 'featured_on', id: row.id }, function() {
			$("#"+row.id + " .featured_on").removeClass('hidden');
			$("#"+row.id + " .featured_off").addClass('hidden');
		});

		return false;
    });	
	// END FAST SWITCH
	
	// DATE/TIME CHECK
	$("#date").blur( function() {
		if(!dateFormat.test(document.getElementById('date').value)) {
			$("#date").addClass('error');

			jAlert('Please enter correct date format.');
		} else {
			$("#date").removeClass('error');
		}
	});

	$("#time").blur( function() {
		if(document.getElementById('time').value != '' && !timeFormat.test(document.getElementById('time').value)) {
			$("#time").addClass('error');

			jAlert('Please enter correct time format.');
		} else {
			$("#time").removeClass('error');
		}
	});	
	// END DATE CHECK
	
	// CLOSE BUTTON
	$(".windowClose").click( function() {
		$("#formsEditScreen").fadeOut();
	});

	$(".formsCancel").click( function() {
		$("#formsEditScreen").fadeOut();
	});
	// END CLOSE BUTTON

	// SORTING
    // UP
    $("a.sorting_up_js").live( 'click', function() {
        var ct = $(this).parents("tr:first");
		var navID = ct.get(0).id.replace(/n/, '');

        var url = document.location.toString() + "sort/";
        $.post(url, { id: navID.replace(/\D*/, ''), dir: 'sorting_up' }, function(response) {
			switch (response) {
				case 'ok':
			        ct.after(ct.prev());
			        $("#listing tbody tr").removeClass('zebra');
			        $("#listing tbody tr:odd").addClass('zebra');
					break;
				case 'nochange':
					break;
				default:
					jAlert('Could not sort list');
					break;
			}
        });
    });

	// DOWN
    $("a.sorting_down_js").live( 'click', function() {
    	var ct = $(this).parents("tr:first");
		var navID = ct.get(0).id.replace(/n/, '');

    	var url = document.location.toString() + "sort/";
        $.post(url, { id: navID.replace(/\D*/, ''), dir: 'sorting_down' }, function(response) {
			switch (response) {
				case 'ok':
			        ct.before(ct.next()); 
					$("#listing tbody tr").removeClass('zebra');
					$("#listing tbody tr:odd").addClass('zebra');
					break;
				case 'nochange':
					break;
				default:
					jAlert('Could not sort list');
					break;
			}
        });
    });
	// END SORTING
	
    $("div.edit").hide();
    $("div#edit-section-div").hide();
    $("span.en").hide();
    $("span." + lang).show();
	
/*     // QUICK SEARCH
	$("#quicksearch").remove();
    $("#listing tbody tr").quicksearch( {
        stripeRowClass: ['even', 'odd'],
        position: 'after',
        attached: '#show_quicksearch_here',
                delay: 50
    });
	// END QUICK SEARCH */

    // GENERATE & VALIDATE SLUGS
	$("#title").blur( function() {
        if( this.value ) {
            if($("#slug").size() > 0) {
				var slug = $("#slug").get(0);

				if(!slug.value || slug.value == '') {
					slug.value = slugify(this.value);
					slug.blur();
				}
			}
        }
    });
	
	$("#s_title").blur( function() {
        if( this.value ) {
            if($("#s_slug").size() > 0) {
				var slug = $("#s_slug").get(0);

				if(!slug.value || slug.value == '') {
					slug.value = slugify(this.value);
					slug.blur();
				}
			}
        }
    });
	
	$('#slug, #s_slug').blur( function() {
		if( this.value )
			$(this).val( slugify( this.value ) );
	});
	// END GENERATE & VALIDATE SLUGS
    
    // CHECK FOR PAGES SECTION
	var sch = document.location.toString();
	
	// if( sch.match( /administration\/pages[^a-z0-9_]/ ) ) {
	// 	Section.change('nav_0');
	// }

	if($('#items-ct').size() > 0) {

		$("a.new-page").click( function() {
			var eFORM = $("#page-form").get(0);
			
			Page.fillForm( {}, 'insert_page');

			$("#listing").hide();
			$("#page").show();
		});

		$("form#page-form .cancel").click( function() {
			$("#listing, #filter").show();
			$("#page").hide();
			
		});
		
		$("form#page-form .save-edit").click( function() {
			if(noSave > 0)
				return false;
		
			// add or edit page
			saveAndGo = 0;

			$("form#page-form").submit();

			return false;
		});

		$("form#page-form .submit").click( function() {
			saveAndGo = 1;
		});

		$("form#page-form").submit( function() {
			console.log('invalidSlug = ' + this.invalidSlug);
			if(this.invalidSlug)
				return false;
			// add or edit page
			return jform.submit(this, {
				onComplete: function(data) {
					console.log(data);

					if(saveAndGo > 0) {
						url = webroot + 'administration/subpages/' + cur_id + '/' + Admin.position;
						$.getJSON(url, function(items) {
							List.build(items);
						
							$("#page").hide();
							$("#listing, #filter").show();
						});
					} else {
						jAlert('Съхраняване... ОК!');
					}
				}
			});
		});


		$("a.new-section").click( function() {
			var arrPageSizes = ___getPageSize();
			var arrPageScroll = ___getPageScroll();

			$('input#s_id').val('');
			$('input.long').val('');

			$('#edit-section-div .navigations').each( function() {
				$(this).get(0).checked = false;
			});
			$('#edit-section-div #s_active').get(0).checked = true;
			
			$('#edit-section-div').css({ top: arrPageScroll[1] + (arrPageSizes[3] / 5) }).fadeIn();

			return false;
		});

		$('form#section-form').submit( function() {

			if($('#s_id').val() > 0)
				this['action'].value = 'edit_section';
			else
				this['action'].value = 'insert_section';

			this['parent_id'].value = cur_id;
			this['is_section'].value = 1;

			return jform.submit(this, {
				onComplete: function(d) {

					Section.change('nav_' + cur_id);

					$('#edit-section-div').fadeOut();
				}
			});


			return false;
		});
		
		$("#choose-header").click( function() {
			popup();
			
			return false;
		});
	}
	// END CHECK FOR PAGES SECTION

	// FILTER
	$("#filter_by").change( function() {
		var sel = $("#filter_by").get(0);
		var items_from = 0;

		$(".a8cf04a9734132302f96da8e113e80ce5").hide();

		if (sel.value == 'a8cf04a9734132302f96da8e113e80ce5') {
			var total = $(".a8cf04a9734132302f96da8e113e80ce5").size();

			for(i = items_from;i < (itemsPerPage + items_from); i++)
				$(".a8cf04a9734132302f96da8e113e80ce5:eq("+i+")").show();

		} else {
			for (var n = 0; n < filter_by.length; n++) {

				if (sel.value == filter_by[n]) {

					var total = $("." +  filter_by[n]).size();

					for(i = items_from;i < (itemsPerPage + items_from); i++) {
						$("." +  filter_by[n] + ":eq("+i+")").show();
					}
				}
			}
		}

		var totalPages = Math.ceil(total / itemsPerPage);

        var t = $("#pager"); t.html(''); // clear previous content

		if(totalPages > 1)
			for(i = 0; i < totalPages; i++)
				t.append( Links.build( i, sel.value ) );
	});
	// END FILTER
	
	// IMAGE PREVIEW
	$('.lightbox').lightBox();
	// END IMAGE PREVIEW
	
	if(itemsPerPage > 0)
		Links.filter( itemsPerPage ); // Start frst filter

	$(".add_image").click( function() { 
		var where = document.getElementById('images');
		var id = Math.floor(Math.random() * Math.random() * 100252);

		what = 
			$.TR( { id: 'im' + id },
				$.TD( {},
					$.LABEL( { Class: 'imgs' },
						'',
						$.INPUT( { Class: 'long', type: 'text', id: 'imgs' + id, name: 'images['+id+']', value: '' } ),
						$.SPAN( { Class: 'icons' },
							$.A( { Class: 'browse', name: 'imgs' + id, title: 'browse' }, 'browse' ),
							$.A( { Class: 'remove', name: 'im' + id, title: 'remove' }, 'remove' )
						)
					)
				)
			);

		$(".browse", what).click( function() {
			popup($(this).get(0).name, 'images');

			return false;
		});

		$(".remove", what).click( function() {
			r = confirm("Are you sure you want to delete this picture?");

			if(!r) return false;
			var row = $(this).get(0).name;

			$("#" + row).remove();

			return false;
		});
		
		where.appendChild(what);
	});

	$(".remove").click( function() {
		r = confirm("Are you sure you want to delete this picture?");

		if(!r) return false;
		var row = $(this).get(0).name;

		$("#" + row).remove();

		return false;
	});
	
	$("#filter_pages").change(function() {
		Admin.position = $(this).val();
		Section.list(0);
	});
});

Links = {
	build : function( total, filter ) {
		var eLink;

		eLink = $.A( { Class: 'pager', name: filter }, (total + 1) );

		$(eLink).click( function() { 
		
			var items_from = ((total + 1) * itemsPerPage - itemsPerPage);

			$(".a8cf04a9734132302f96da8e113e80ce5").hide();

			if (filter == 'a8cf04a9734132302f96da8e113e80ce5')
				for(i = items_from;i < (itemsPerPage + items_from); i++)
					$(".a8cf04a9734132302f96da8e113e80ce5:eq("+i+")").show();

			else
				for (var n = 0; n < filter_by.length; n++)
					if (filter == filter_by[n])
						for(i = items_from;i < (itemsPerPage + items_from); i++)
							$("." +  filter_by[n] + ":eq("+i+")").show();

			return false;
		});

		return eLink;
	},
	
	filter: function( itemsPerPage) {
		var items_from = 0;

		var total = $(".a8cf04a9734132302f96da8e113e80ce5").size();

		if(total > itemsPerPage) {
			$(".a8cf04a9734132302f96da8e113e80ce5").hide();

			for(i = items_from;i < (itemsPerPage + items_from); i++)
				$(".a8cf04a9734132302f96da8e113e80ce5:eq("+i+")").show();

			var totalPages = Math.ceil(total / itemsPerPage);

			var t = $("#pager");
			t.html(''); // clear previous content

			for(i = 0; i < totalPages; i++) {
				t.append( Links.build( i, 'a8cf04a9734132302f96da8e113e80ce5' ) );
			}
		}
	}
}