<?php
 // KioCMS - Kiofol Content Managment System
// includes/parser/bbcode/index.php

return array(
	// Horizontal rule
	'#\[hr\]#i' => '<hr />',
	// Logged user nickname
	'#\[you\]#ie' => 'NICKNAME',
	// Bold
	'#\[b\](.*?)\[/b\]#is' => '<strong>\1</strong>',
	// Underline
	'#\[u\](.*?)\[/u\]#is' => '<span style="text-decoration: underline;">\1</span>',
	// Italic
	'#\[i\](.*?)\[/i\]#is' => '<em>\1</em>',
	// Strike
	'#\[s\](.*?)\[/s\]#is' => '<span style="text-decoration: line-through;">\1</span>',
	// Image
	'#\[img\](.*?)\[/img\]#is' => '<img src="\1" alt="" />',
	// Hyperlink
	'#\[url\]([^<]+?)\[/url\]#is' => '<a href="\1" class="external" rel="nofollow">\1</a>',
	// Hyperlink with parameter
	'#\[url=([^<]+?)\](.*?)\[/url\]#is' => '<a href="\1" class="external" rel="nofollow">\2</a>',
	// Email
	'#\[email\]([^<]+?)\[/email\]#is' => '<a href="mailto:\1">\1</a>',
	// Email with parameter
	'#\[email=([^<]+?)\](.*?)\[/email\]#is' => '<a href="mailto:\1">\2</a>',
	// Text align
	'#\[(left|right|center|justify)\](.*?)\[/\1\]#is' => '<div style="text-align: \1;">\2</div>',
	// Preformated
	'#\[pre\](.*?)\[/pre\]#is' => '<pre>\1</pre>',
	// Code
	'#\[code\](\n?)(.*?)(\n?)\[/code\]#ise' => '\'<ol class="code"><li>\'.str_replace("\n", \'</li><li>\', \'\2\').\'</li></ul>\'',
	// Code with defined language
	'#\[code=(.*?)\](.*?)\[/code\]#ise' => '\'<pre><code class="\1">\2</code></pre>\'',
	// PHP Code Syntax Highlighting
	'#\[php\](.*?)\[/php\]#is' => '<pre class="code php">\1</pre>',
	// Quote
	'#\[(quote|q)\](.*?)\[/\1\][\r|\n]?#is' => '<blockquote class="quote">\2</blockquote>',
	// Quote with given author
	'#\[(quote|q)=(.*?)\](.*?)\[/\1\][\r|\n]?#si' => '<blockquote class="quote"><cite><strong>\2</strong>:</cite>\3</blockquote>',
	// Sup/Sub
	'#\[(sup|sub)\](.*?)\[/\1\]#is' => '<\1>\2</\1>',
	// Font size
	'#\[size=([1-9]{1,2}|1[1-9]{1,2}|200)](.*?)\[/size\]#si' => '<span style="font-size: \1%">\2</span>',
	// Font color
	'#\[color=([a-z]*|\#?[A-f\d]{3,6})](.*?)\[/color\]#si' => '<span style="color: \1">\2</span>',
	// List
	'#\[list\]([<br />]{1,})(.*?)([<br />]*)\[/list\]#si' => '[list]\2[/list]', //
	'#\[list\][\[\*\]]+(.*?)\[/list\]#si' => '<ul>\1</ul>',
	'#\[list=(.*?)\](.*?)\[/list\][\r|\n]?#si' => '<ol style="list-style: \1;"><li>\2</li></ol>',
	//'#{(?!disable1|disable2)[a-z0-9]+}#is', '\1'
	// Flash object
	'#\[flash\](.*?)\[/flash\]#si' => '<object type="application/x-shockwave-flash" width="100%" height="250" data="\1">Flash</object>',
	'#\[flash=([1-9]{1,4})/([1-9]{1,4})\](.*?)\[/flash\]#si' => '<object type="application/x-shockwave-flash" width="\1" height="\2" data="\3">\3</object>',

	'#\[\*\](.*?)(<br />)\s#si' => '<li>\1</li>',
	// Content visible only for logged in users
	'#\[hide\](.*?)\[/hide\]#is' => '<fieldset><legend>[hide]</legend><div>\1</div></fieldset>');
