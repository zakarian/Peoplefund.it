var cwd;
var vmode = 'list';

var win;
var wd;
var ifr;
var lang;
var root;
var section;

var console;

var itemsPerPage;

var gv = false;
var gal_id = null;

var input_id;
var section_id;

if(!console) {
    console = { log: function() {} } 
}

function insertdoc(url) {

}

function blankCreate() {
	var blankScreen = document.createElement('div');
	
	blankScreen.setAttribute('id', 'idBlankScreen');
	blankScreen.className = "blankScreen";
	
	document.body.appendChild(blankScreen);

}

function blankDestroy() {
	var blankScreen = document.getElementById('idBlankScreen');
	
	document.body.removeChild(blankScreen);
}

function getCWD () {
	return cwd;
}

function closeUpload() {
	$("div.edit_sect, div.add").fadeOut("normal");
    Dir.cd('.');
}

View = {
    row: function(i) {
        if(i.name == '..' && cwd == '/') return false;
        
        var acl; // <a> class

        acl = i['type'] == 'dir' ? 'dir' : 'file';
        
        if(!i['type']) i['type'] = 'misc';
        if(!i['title_en']) i['title_en'] = ' ';
        if(!i['size']) i['size'] = ' ';
		if(i['type'] == 'image') i['view_icon'] = $.A( { Class: 'lightbox', href: '/var' + i['dir'] + i['name'], rel: 'assets' }, 'View' );
		if(i['type'] != 'dir') i['icon'] = $.A( { Class: 'delete', href: '', title: 'Delete' }, 'Delete' );
		//var projRegex = new RegExp('project_\d');
		if(i['type'] == 'dir' && i['name'] != '..' && /* !projRegex.test(i['name'])*/ !i['name'].match(/project_\d/) ) i['icon'] = $.A( { Class: 'rmdir', href: '', title: 'Delete' }, 'Delete' );
		if(i['type'] == 'dir' && i['name'] != '..') i['view_gallery'] = $.A( { Class: 'gallery', href: '', title: 'Make gallery with images on this folder' }, 'Make gallery' );

        var ch;
        if(i['type'] != 'dir') {
            if(input_id) 
                ch = $.A( { Class: 'ch', href: '#' }, 
                        $.IMG( { src: '/share/images/icons/apply.gif' } )
                        );
        }

        var eTR = 
            $.TR( { Class: i['type'], id: i['id'], name: cwd+i['name'] },
                $.TD( { Class: 'name' }, $.A( { Class: 'dragbl ' + acl, href: "" }, i['name'] ) ),
                $.TD( { }, $.SPAN( { Class: 'tdpadding' }, i['title_en'] ) ),
                $.TD( { }, $.SPAN( { Class: 'tdpadding' }, i['size'] ) ),
				$.TD( { Class: 'icons' }, i['icon'] || ' ' )
            );
        
        $("td.name", eTR).append(ch);
		$("td.icons", eTR).append(i['view_icon']);
		$("td.icons", eTR).append(i['view_gallery']);

        return eTR;
    },

    list: function() {
        gv = false;
		
		cwd64 = Base64.encode(cwd);
		
        $.getJSON('/'+cmsSection+'/assets/dir/'+cwd64+'/'+Math.random(), { }, function(items) {
            $("#listing tbody").html('');
            for(var i = 0; i < items.length; i++) {
				if(items[i]['name'] == 'thumbs')
					continue;

                $("#listing tbody").append( View.row(items[i]) );
            }

			$('.lightbox').lightBox();

            $("#listing tbody tr:odd").addClass('zebra');

            $("a.dir").click( function() { 
				Dir.cd($(this).html());

                return false;
            });
            
            $("a.ch").click( function() {
                var f = $(this).prev().html();

                $.getJSON('/'+cmsSection+'/assets/properties/'+cwd64+'/'+f, function(d) {

					if(ifr.contents().find("#"+input_id).size() > 0)
						ifr.contents().find("#"+input_id).val(root + d['dir'] + d['name']);
					else
						if($("#"+input_id, wd).size() > 0)
							$("#"+input_id, wd).val(root + d['dir'] + d['name']);

					if(ifr.contents().find("#alt").size() > 0)
						ifr.contents().find("#alt").val(d['desc_en']);
					else 
						$("#i-title", wd).size() > 0
							$("#i-title", wd).val(d['desc_en']);
					
					if(ifr.contents().find("#title").size() > 0)
						ifr.contents().find("#title").val(d['title_en']);
					else
						if($("#i-desc", wd).size() > 0)
							$("#i-desc", wd).val(d['title_en']);

					$(".windowClose", wd).unbind("click");
					$(".assets", wd).fadeOut("normal");
                });
                
                return false;
            });

			$("a.file").click( function() {
			if(!input_id) {
                $("div.edit_sect, div.add").hide();

                $("#listing tbody tr").removeClass('selected');
                $(this).parents("tr:first").addClass('selected');
                
                var f = $(this).html();

                $.getJSON('/'+cmsSection+'/assets/properties/'+cwd64+'/'+f, { }, function(pr) {
                    $("input#id").get(0).value = pr['id'];

                    $("span#name").html(pr['name']);
                    $("span#title_en").html(pr['title_en']);
                    $("span#desc_en").html(pr['desc_en']);

                    $("div.noedit_sect").show();
                });
			}

                return false;
            });

            $("a.dir").Droppable( {
                accept: 'file',
                tolerance : 'pointer',
                hoverclass : 'dir-h',
                ondrop: function(d) {
                    var of = $(d).html();
                    var nf = $(this).html();

                    url = document.location.toString();
					$.post( url, { action: 'move', cwd: cwd, newfile: nf, oldfile: of }, function(r) {
                        console.log(r);
                        if(r.match(/OK/)) {
                            $(d).parents("tr:first").remove();
                        } else {
                            return false;
                        }
                    });
                }
            });

            $("a.dragbl").Draggable( { 
                revert : true
            });
            
            $("#quicksearch").remove();

            $("#listing tbody tr").quicksearch( {
                stripeRowClass: ['even', 'zebra'],
                position: 'before',
                attached: '#loc-ct',
                delay: 50
            });

            $("#listing").show();

			$("a.delete").click( function() {
				var row = $(this).parents("tr:first").get(0);

				jConfirm("Are you sure you want to delete this?", '', function(r) {
					if(!r) return false;

					url = document.location.toString();

					$.post( url, { action: 'delete_file', id: row.id }, function(r) {
						$(row).remove();
					});
				});

				return false;
			});
			
			$("a.rmdir").click( function() {
				var row = $(this).parents("tr:first").get(0);

				jConfirm("Are you sure you want to delete this directory?", '', function(r) {
					if(!r) return false;

					url = document.location.toString();

					$.post( url, { action: 'rmdir', name: row.name, cwd: cwd }, function(r) {
						if(r == 'ERROR')
							alert('Delete files on this folder first!');
						else
							$(row).remove();
					});
				});

				return false;
			});

			$('a.gallery').click ( function() {
				var row = $(this).parents("tr:first").get(0);

				$("#g-dir").val(row.name + '/');

				$("form#new-gallery").fadeIn("normal");

				return false;
			});
        });
    }	
}

Dir = {
    cd: function(d) {
        if(d == '..' && cwd == '/') return;

        if(d == undefined || d == '/') {
            cwd = '/'; 
        } else if(d == '..') { // previous
            cwd = cwd.replace(/[^\/]+\/$/, ""); 
        } else if(d.match(/^\//) ) { // absolute paths
            cwd = d;
        } else if(d != '.') { // current 
            cwd += d + '/';
        }

        if(cwd.match(/images/)) {
            $("#vmode").show();
        } else {
            $("#vmode").hide();
            vmode = 'list'; 
        }
     
        View[vmode](); // nice
        
        // TODO:update #nav sections accordingly
        $("#location span").html(cwd);

        $("div.noedit_sect, div.edit_sect").hide();
    }
}

$(document).ready( function() {
	if(input_id) {
        root = '/var';

		section = section_id || 'images';

        wd = parent.document;

		$('iframe', wd).each( function() {
			ifr = $(this);
		});
    }

    $("#loading").hide();
    $('.assets').hide();

    $("#loading").ajaxStart(function() { 
		blankCreate();
		$(this).show();
	});
    $("#loading").ajaxStop(function() { 
		blankDestroy();
		$(this).hide();
	});
    
    if(section) {
        Dir.cd( section + '/')
    } else {
        Dir.cd('/');
    }
	
    $("#vmode a").click( function() {
        vmode = this.id;
        Dir.cd('.');

        return false;
    });
    $(".new-page").click( function() {
        $("div.noedit_sect, div.edit_sect").hide();

        var f = $("form#new").get(0);
        
        f['file'].value     = '';
        f['title_en'].value = '';
        f['desc_en'].value  = '';
        
        var arrPageScroll = ___getPageScroll();

        $("div.add").css({ top: arrPageScroll[1] + 250 });

        $("div.add").fadeIn("normal");

		return false;
    });

    $("div.noedit_sect img.edit_file").click( function() { 
        var f = $("form#properties").get(0);

        f['file'].value = $("span#name").html();
        f['title_en'].value = $("span#title_en").html();
        f['desc_en'].value  = $("span#desc_en").html();

        $("div.edit_sect").fadeIn("normal");
        $("div.noedit_sect").hide();

        return false;
    });

    $(".new-section").click( function() {
        jPrompt('Please enter new folder name:', '', 'Prompt', function(d) {
			if(!d) { return false; }

			url = document.location.toString();
			$.post( url, { action: 'mkdir', dir: d, cwd: cwd }, function(r) {
				//alert( r );
				if(r == 'OK') 
					Dir.cd('.');
			});
		});

		return false;
    });

	// ADD CORNERS
	$(".add fieldset").corner(); 
	$(".edit_sect fieldset").corner();
	$("#new-gallery fieldset").corner();
	// END ADD CORNERS

	$("img.close").click( function() {
		$('div.edit_sect').fadeOut('normal');
		$("div.add").fadeOut("normal");
		$("form#new-gallery").fadeOut("normal");

		return false;
	});
	
	$("input.save").hover( function() {
		$(this).attr('src', '/share/images/buttons/button-save-hover.gif');
	}, function() {
		$(this).attr('src', '/share/images/buttons/button-save.gif');
	});

	$("img.close").hover( function() {
		$(this).attr('src', '/share/images/buttons/button-close-hover.gif');
	}, function() {
		$(this).attr('src', '/share/images/buttons/button-close.gif');
	});

    $("form.jform").submit( function() {
        console.log(cwd);

        this['cwd'].value = cwd;
        return jform.submit(this, { 
            onComplete: function(r) {
//                console.log(r);
                if(r.match(/Error: /)) {
                    jAlert(r);
                    return false;
                }
                $("div.edit_sect, div.add").fadeOut("normal");
                Dir.cd('.');
            }
        });
    });
	
    $("form#new-gallery").submit( function() {
	
		return jform.submit(this, { 
            onComplete: function(r) {
//                console.log(r);
                if(r.match(/ERROR: /)) {
                    jAlert(r);
                    return false;
                }
                $("form#new-gallery").fadeOut("normal");

				// jump to gallery
				document.location = r;
            }
        });

    });

	$("a.popup").click( function() {
        window.open($(this).attr('href'),'browser',"scrollbars=yes,width=700,height=400,screenX=150,screenY=150,top=150,left=150");
        return false;
    });

	$("a#flash").click( function() {
		$('#fileUpload').show();
		$('#new').hide();

		return false;
	});

	$("a#form").click( function() {
		$('#fileUpload').hide();
		$('#new').show();

		return false;
	});
});