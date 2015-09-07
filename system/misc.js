var IE = navigator.appName == 'Microsoft Internet Explorer';

$(function()
{
	var hash = location.hash.replace('#', '');
	
	$('.resizable:not(.processed)').TextAreaResizer();

	$('table.floating').floatHeader({floatClass: 'floating-header'});

	$('#navigation li ul').parent().addClass('parent');
	if ($('textarea.limit').length != 0) limit($('textarea.limit'));
	$('textarea.limit').keyup(function() {limit($(this));}).focus(function() {limit($(this));});
	$('html').addClass('js');

	if (hash)
	{
		$('fieldset#' + hash + '.collapsed *').show();
		$('fieldset#' + hash).removeClass('collapsed');
	}
	
	$('fieldset.collapsed *:not(legend, legend *)').hide();

	/* Hashing e-mail address
	$('.mailto').attr('href', 'mailto:'); */

	
	$('.sort').click(function()
	{
		if ($(this).attr('href').substr(0, 1) == '#')
		{
			window.location.href = $(this).attr('href').substr(1);
			return false;
		}
	});

	$('a.delete').click(function()
	{
		if (confirm(lang[0]))
		{
			$.post(document.href, {delete_id: $(this).attr('accesskey'), auth: AUTH});
			$($(this).attr('href')).fadeOut('slow');
		}
		return false;

	});
	$('.confirm').click(function()
	{
		if (!confirm(lang['confirm_delete']))
		{
			return false;
		}
	});
	/*$('form').submit(function()
	{
		// $(this).find('input, textarea, select').attr('readonly', 'readonly');
		// $(this).find('input:submit, input:reset, input:button').attr('disabled', 'disabled');
		$(this).find('input:submit').attr('readonly', 'readonly');
	});*/
	$('img.resize').click(function()
	{
		var textarea = document.getElementById($(this).attr('longdesc'));
		this.alt == '+' ? textarea.rows += 5 : textarea.rows -= 5;
		textarea.focus();
	});
	$('a.external, a.out').click(function()
	{
		window.open(this.href);
		return false;
	});
	$('.check_all').click(function()
	{
		 $(this).is(':checked')
		 	? $('#' + $(this).attr('value') + ' input').attr('checked', 'checked')
			:  $('#' + $(this).attr('value') + ' input').removeAttr('checked');
		// $('input.check_all[checked]').lenght > 0 ? $('#check input').removeAttr('checked') : $('#check input').attr('checked', 'checked');
	});
	$('.get_host').click(function()
	{
		$.post(LOCAL + 'includes/get_host.php', {ip: $(this).text()}, function(host)
		{
			alert(host);
		});
	});
	$('form.ajax').submit(function()
	{
		$.post(LOCAL + 'ajax.php', {action: $(this).attr('action')}, function(host)
		{
			alert(host);
		});
	});

	$('.is_available').click(function()
	{
		$.post(LOCAL + 'modules/registration/check.php', {logname: $('#form-logname').val()}, function(data)
		{
			$('#logname_availability').html(data);
		})
	});

	$('fieldset.collapsible > legend > a').click(function()
	{
		var fieldset = '#' + $(this).attr('href').substr(1);

		
		// Show
		if ($(fieldset).is('.collapsed'))
		{
			$(fieldset).removeClass('collapsed');
			$(fieldset + ' *').show();
		}
		// Hide
		else
		{
			$(fieldset).addClass('collapsed');
			$(fieldset + ' *:not(legend, legend *)').hide();
		}
		return false;
	});

	
	$('textarea.bbcode').each(function()
	{
		$(this).before(
			'<div id="preview-' + $(this).attr('id') + '"></div>' +
			'<div class="panel" id="panel-' + $(this).attr('id') + '">' +
			'<img alt="b" class="tag" title="${t:Bold}" src="' + LOCAL + 'plugins/panel/images/b.png" /> ' +
			'<img alt="u" class="tag" title="${t:Underline}" src="' + LOCAL + 'plugins/panel/images/u.png" /> ' +
			'<img alt="i" class="tag" title="${t:Italic}" src="' + LOCAL + 'plugins/panel/images/i.png" /> ' +
			'<img alt="left" class="tag" title="${t:Align left}" src="' + LOCAL + 'plugins/panel/images/left.png" /> ' +
			'<img alt="center" class="tag" title="${t:Center}" src="' + LOCAL + 'plugins/panel/images/center.png" /> ' +
			'<img alt="right" class="tag" title="${t:Align right}" src="' + LOCAL + 'plugins/panel/images/right.png" /> ' +
			'<img alt="justify" class="tag" title="${t:Justify}" src="' + LOCAL + 'plugins/panel/images/justify.png" /> ' +
			'<img alt="pre" class="tag bb-double" title="${t:Preformatted}" src="' + LOCAL + 'plugins/panel/images/pre.png" /> ' +
			'<img alt="hr" class="tag bb-single" title="${t:Horizontal rule}" src="' + LOCAL + 'plugins/panel/images/hr.png" /> ' +
			'<img alt="[list][*]~[*][/list]" class="tag" title="${t:List}" src="' + LOCAL + 'plugins/panel/images/list.png" /> ' +
			'<img alt="code" class="tag" title="${t:Code}" src="' + LOCAL + 'plugins/panel/images/code.png" /> ' +
			'<br />' +
			'<img alt="quote" class="tag" title="${t:Quote}" src="' + LOCAL + 'plugins/panel/images/quote.png" /> ' +
			'<img alt="sup" class="tag" title="${t:Superscript}" src="' + LOCAL + 'plugins/panel/images/sup.png" /> ' +
			'<img alt="sub" class="tag" title="${t:Subscript}" src="' + LOCAL + 'plugins/panel/images/sub.png" /> ' +
			'<img alt="img" class="tag" title="${t:Image}" src="' + LOCAL + 'plugins/panel/images/img.png" /> ' +
			'<img alt="url" class="tag" title="${t:Hyperlink}" src="' + LOCAL + 'plugins/panel/images/url.png" /> ' +
			'<img alt="email" class="tag" title="${t:E-mail}" src="' + LOCAL + 'plugins/panel/images/email.png" /> ' +
			'<img alt="size" class="tag" title="${t:Font size}" src="' + LOCAL + 'plugins/panel/images/size.png" /> ' +
			'<img alt="color" class="tag" title="${t:Font color}" src="' + LOCAL + 'plugins/panel/images/color.png" /> ' +
			'<img alt="font" class="tag" title="${t:Font face}" src="' + LOCAL + 'plugins/panel/images/font.png" /> ' +
			'<img alt="emoticons" class="tag" title="${t:Emoticons}" src="' + LOCAL + 'plugins/panel/images/emoticons.png" /> ' +
			'<img alt="preview" class="preview" title="${t:Preview}" src="' + LOCAL + 'plugins/panel/images/preview.png" /> ' +
			'<!--  <img class="resize decrease" src="' + LOCAL + 'plugins/panel/images/decrease.png" longdesc="${element_name}" alt="-" title="${t:Decrease textarea size}" />' +
			'<img class="resize increase" src="' + LOCAL + 'plugins/panel/images/increase.png" longdesc="${element_name}" alt="+" title="${t:Increase textarea size}" />-->' +
			'</div>'
		);
	});

	$('img.tag').click(function()
	{
		insert($(this).parent().attr('id').substr(6), '[' + this.alt + ']', '[/' + this.alt + ']');
	});

	$('.preview').click(function()
	{
		var textarea_id = $(this).parent().attr('id').substr(6);

		$('#' + textarea_id).val() && $.ajax(
		{
			type: 'POST',
			url: LOCAL + 'plugins/panel/preview.php',
			data: 'text=' + $('#' + textarea_id).val(),
			success: function(html)
			{
				//$('<div id="preview_' + textarea_id + '"></div>').prependTo($(this).parent())
				$('#preview-' + textarea_id).addClass('preview');
				$('#preview-' + textarea_id).html(html);
			}
		});
	});
});


//////////////

function limit(textarea)
{
	var id = textarea.attr('id');
	var limit = $('var.limit.' + id).attr('title');

	if (textarea.val().length > limit)
		textarea.val(textarea.val().substring(0, limit));

	$('var.limit.' + id).text(limit - textarea.val().length);
}

/* Hashing e-mail address */
function mailto(user, domain)
{
	window.location = 'mailto:' + user + '@' + domain;
}

/* Function inserts tags to form element */
function insert(target_id, before, after)
{
	var element = document.getElementById(target_id);
	if (IE)
	{
		element.focus();
		var between = document.selection.createRange();
		var between2 = between.text.length;
		document.selection.createRange().text = before + between.text + after;
		between.moveStart('character', before.length);
		between.moveEnd('character', between2);
		between.select();
	}
	else
	{
		var start = element.selectionStart;
		var end = element.selectionEnd;
		var between = element.value.substring(start, end);
		element.value = element.value.substr(0, start) + before + between + after + element.value.substr(end);
		var pos = (between.length == 0) ? start + before.length : start + before.length + between.length + after.length;
		element.selectionStart = between.length == 0 ? start + before.length : start + before.length;
		element.selectionEnd = between.length == 0 ? start + before.length : start + before.length + between.length;
		element.focus();
	}
}

function Switch(id, content)
{
	var element = document.getElementById(id);
	element.value = content;
	element.focus;
} 

/* Show text in password input */
function Unmask(id)
{
	var element = document.getElementById(id);
	var checkbox = document.getElementById(id + '_unmask');
	if (IE)
	{
		var element2 = document.createElement('input');
		checkbox.checked ? element2.setAttribute('type', 'text') : element2.setAttribute('type', 'password')
		element2.id = element.id;
		element2.name = element.name;
		element2.className = element.className;
		element2.value = element.value;
		element.parentNode.replaceChild(element2, element);
	}
	else
	{
		checkbox.checked ? element.setAttribute('type', 'text') : element.setAttribute('type', 'password');
	}
}
// Autocheck
function checkAll(form)
{
	var form = document.getElementById(form);
	for (var x = 0; x < form.elements.length; x++)
	{
		if (form.elements[x].name != 'all')
		{
			form.elements[x].checked = form.all.checked;
		}
	}
}

function show_hide(msg_id){
       msg_id.style.display=msg_id.style.display=='none' ? '' : 'none'
} 


/*
///* Switching body id
function Template(e)
{
	var element = document.getElementById(e);
	var body = document.getElementsByTagName('body')[0].id;
	if (body == 'a')
	{
		document.getElementById('a').id = 'b';
		element.style.display = 'none';
		///*var ImgSrc = document.getElementById('i' + e);
		//ImgSrc.src = 'plus.gif';
	}
	else if (body == 'b')
	{
		if (element.style.display == '')
		{
			document.getElementById('b').id = 'c';
			element.style.display = 'none';
		}
		else
		{
			element.style.display = '';
			document.getElementById('b').id = 'a';
			
		}
		///*var ImgSrc = document.getElementById('i' + e);
		//ImgSrc.src = 'minus.gif';
	}
	else if (body == 'c')
	{
		document.getElementById('c').id = 'b';
		element.style.display = '';
		//var ImgSrc = document.getElementById('i' + e);
		//ImgSrc.src = 'minus.gif';
	}
}
function Users(id, box_id, letter)
{
	document.getElementById(box_id).innerHTML += '<form action="' + (action ? action : '') + '" id="delete-' + id + '" method="post"><input type="hidden" name="delete" value="' + id + '" /></form>';
	document.getElementById('delete-' + id).submit();
}

function Resize(target, action)
{
	var textarea = document.getElementById(target);
	action == '+' ? textarea.rows += 4 : textarea.rows -= 4;
	textarea.focus();
}
function Delete2(id)
{
	document.getElementById('delete-' + id).innerHTML += '<input type="hidden" name="delete" value="' + id + '" />';
	document.getElementById('delete-' + id).submit();
}

function Preview2()
{
  if(document.pressed == 'Podglad')
  {
   document.form.action ="#form";
  }
  else
  if(document.pressed == 'Update')
  {
    document.form.action ="update.html";
  }
  return true;
}


function Resze(id)
{
	var textarea = document.getElementById(id);


	if (textarea.className == 'big')
	{
		textarea.className = 'bigger';

	}
	else
	{
		textarea.className = 'big';

	}
	textarea.focus();
}
function Unmask2(id)
{
	var element = document.getElementById(id);
	var checkbox = document.getElementById(id + '_unmask');
	if (checkbox.checked)
	{
		element.setAttribute('type', 'text');
	}
	else
	{
		element.setAttribute('type', 'password');
	}
	element.focus();
}

function strlen(name, limit)
{
	textarea = document.getElementById(name);
	if (textarea.value.length <= limit)
	{
		a = textarea.value.length;
		c = limit - a;
		document.getElementById('strlen_' + name).innerHTML = c;
	}
	else
	{
		textarea.value = textarea.value.substring(0, limit);
	}
}
function strlen2(name, limit)
{ 
	if (my_form.krotkatresc.value.length<=300)
	{ 
		a=my_form.krotkatresc.value.length;
		b=300; 
		c=b-a; 
		document.getElementById('ile').innerHTML= c;
	} 
	else 
	{ 
		my_form.krotkatresc.value = my_form.krotkatresc.value.substring(0, 300);
	}
} 
	var textarea = document.getElementsByTagName('textarea');
	for (i = 0; i < textarea.length; i++)
	{
		if (textarea[i].className.match('big'))
		{
			textarea[i].onkeydown = textarea[i].onkeyup = textarea[i].onfocus = function()
			{
				var name = this.getAttribute('name');
				var limit = this.getAttribute('tabindex');
				strlen(name, limit);
			}
		}
	}
	
var tgs = new Array('div','body','td');
var szs = new Array('10px','11px','12px','13px','14px');
var startSz = 1;

function text_resize(trgt, inc) {

        if (!document.getElementById) return
        var d = document,cEl = null,sz = startSz,i,j,cTags;

        sz += inc;
        if ( sz < 0 ) sz = 0;
        if ( sz > 4 ) sz = 4;
        startSz = sz;

        if ( !( cEl = d.getElementById( trgt ) ) ) cEl = d.getElementsByTagName( trgt )[ 0 ];

        cEl.style.fontSize = szs[ sz ];

        for ( i = 0 ; i < tgs.length ; i++ ) {

                cTags = cEl.getElementsByTagName( tgs[ i ] );
                for ( j = 0 ; j < cTags.length ; j++ ) cTags[ j ].style.fontSize = szs[ sz ];
        }
}
function submi()
{
	var ttt = document.forms['form']
	document.ttt.elements['podglad'].submit();
}
function show_pagina(e)
{
	var sTop = document.body.scrollTop;
	var sLeft = document.body.scrollLeft;
	document.getElementById('s_pagina').style.display='block';
	document.getElementById('s_pagina').style.left=e.clientX-35+sLeft;
	document.getElementById('s_pagina').style.top=e.clientY+sTop-20;
	return;
}
function BBCode0(name, before, after)
{
	textarea = document.forms['form'].elements[name];
	if ((typeof textarea.selectionStart) != 'undefined')
	{
		ss = textarea.selectionStart;
		se = textarea.selectionEnd;
		st = textarea.scrollTop;
		v1 = (textarea.value).substring(0, ss);
		v2 = (textarea.value).substring(ss, se);
		v3 = (textarea.value).substring(se, textarea.textLength);
		textarea.value = v1 + before + v2 + after + v3;
		if (!!window.opera)
		{
			textarea.selectionStart = (v1 + before + v2).length;
		}
		else
		{
			textarea.selectionEnd = (v1 + before + v2).length;
		}
		textarea.scrollTop = st;
		textarea.focus();
	}
	else
	{
		if (document.selection)
		{
			str = document.selection.createRange().text;
			textarea.focus();
			sel = document.selection.createRange();
			sel.text = before + str + after;
		}
		else
		{
			textarea.value += before + after;
		}
	}
}

function chkFormular(name)
{
	if (document.form.author.value == "")
	{
		document.form.author.className = "text-big error";
		document.form.message.id = "";
		document.form.author.focus();
		return false;
	}
	if (document.form.message.value == "")
	{
		document.form.message.id = "error";
		document.form.author.className = "text-big";
		document.getElementById('resize_' + name).className = "textarea_size error";
		document.getElementById('strlen_' + name).className = "strlen error";
		document.form.message.focus();
   		return false;
	}
}
function BBCode2(elname, wrap1, wrap2) {
	if (document.selection) { // for IE 
		str = document.selection.createRange().text;
		//document.forms['inputform'].elements[elname].focus();
		sel = document.selection.createRange();
		sel.text = wrap1 + str + wrap2;
		return;
	} else if ((typeof document.forms['dodaj'].elements[elname].selectionStart) != 'undefined') { // for Mozilla
		txtarea = document.forms['dodaj'].elements[elname];
		selLength = txtarea.textLength;
		selStart = txtarea.selectionStart;
		selEnd = txtarea.selectionEnd;
		oldScrollTop = txtarea.scrollTop;
		//if (selEnd == 1 || selEnd == 2)
		//selEnd = selLength;
		s1 = (txtarea.value).substring(0,selStart);
		s2 = (txtarea.value).substring(selStart, selEnd)
		s3 = (txtarea.value).substring(selEnd, selLength);
		txtarea.value = s1 + wrap1 + s2 + wrap2 + s3;
		txtarea.selectionStart = s1.length;
		txtarea.selectionEnd = s1.length + s2.length + wrap1.length + wrap2.length;
		txtarea.scrollTop = oldScrollTop;
		txtarea.focus();
		return;
	}
}

function BBC(t,x,y) {
 f=document.getElementById(t);
 if((typeof f.selectionStart)!='undefined') {
  s=f.selectionStart;
  k=f.selectionEnd;
  ost=f.scrollTop;
  a1=(f.value).substring(0,s);
  a2=(f.value).substring(s,k);
  a3=(f.value).substring(k,f.textLength);
  f.value=a1+x+a2+y+a3;
  f.selectionEnd=(a1+x+a2).length;
  f.scrollTop=ost;
  f.focus(); }
 else { f.value+=x+y; }
}

function maxLength2(e,o,v,m){
  if(!o.all&&e.keyCode!=0)return!0;
  return(document.getElementById(v).innerHTML=o.value.length)<m
}

function maxLength3(o,v,m){
  if(o.value.length>m)
    document.getElementById(v).innerHTML=
      ((o.value=o.value.substr(0,m)).length);
}


function s(e) {

        var elementId = document.getElementById(e);
        if (elementId == null) return;
        if (elementId.style.display == '') {

                elementId.style.display = 'none';
                var ImgSrc = document.getElementById("i" + e);
                ImgSrc.src = "plus.gif";
        } else {

                elementId.style.display = '';
                var ImgSrc = document.getElementById("i" + e);
                ImgSrc.src = "minus.gif";
        }
}



function BBCode(id, before, after)
{
	textarea = document.getElementById(id);
*/
/*
function BBCode(before, after)
{

	textarea = document.forms['dodaj'].komentarz;
}


var linki = document.getElementsByTagName('a');
for(i=0; i < linki.length; i++)
{
if (linki[i].className=='costam')
linki[i].onclick = costam; // nazwa funkcji bez ()!
 mozna tez uzyc: linki[i].onlick = function() {return costam(para,metry);}
}



function BBCode(text)
{
	document.forms['dodaj'].komentarz.value  += text;
	document.forms['dodaj'].komentarz.focus();
}
function ubmit()
{
	document.location.href = 'guestbook.php?a=preview';
	document.dodaj.submit();
	
}


function activateBBCode(name)
{
	linki = document.getElementsByTagName('img');
	for(i=0; i < linki.length; i++)
	{
		if (linki[i].className=='bbcode')
		{
			names = linki[i].getAttribute('alt').split(' ')
			linki[i].onclick = function() {return BBCode(name, names[0], names[1]);}
		}
	}
}






function chkFormular()
{
	var box = document.getElementById(\'box\');
	if (document.form.author.value != "")
	{
		document.form.author.id = \'\';
		box.innerHTML = \'sfgkhi;.\';
		box.id = \'\';
	}
	if (document.form.message.value != "")
	{
		document.form.message.id = \'\';
		box.innerHTML = \'sfgkhi;.\';
		box.id = \'\';
	}
	
	if (document.form.author.value == "")
	{
		document.form.author.id = \'negative\';
		document.form.author.focus();
		box.innerHTML = \'Pole <span class="negatywny">autor</span> nie moze zostac puste.\';
		box.id = \'negative\';
		return false;
	}
	if (document.form.message.value == "")
	{
		document.form.message.id = \'negative\';
		document.form.message.focus();
		box.innerHTML = \'Pole <span class="negatywny">wiadomosc</span> nie moze zostac puste.\';
		box.id = \'negative\';
   		return false;
	}

	
}

ZMIANA KLASY!
function chkFormular()
{
var lolek = document.getElementsByTagName(\'div\');
	if (document.form.author.value == "")
	{
		document.form.author.id = \'negative\';
		document.form.message.id = \'\';
		for (i = 0; i < lolek.length; i++)
		{
			if (lolek[i].className==\'neutralny\')
			{
				lolek[i].setAttribute("class","negatywny");
			}
		}

		document.form.author.focus();
		return false;
	}
	if (document.form.message.value == "")
	{
		document.form.message.id = \'negative\';
		document.form.author.id = \'\';
		document.form.message.focus();
   		return false;
	}
}


function chkmailaddr(src) {

var regex=/^[\w]{1,}[\w\-\.]{1,}@([A-Za-z0-9\-]+\.)+[A-Za-z0-9]{2,3}$/i;

return regex.test(src);

}
*/