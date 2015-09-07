<?php
 // KioCMS - Kiofol Content Managment System
// themes/Kiofol/index.php

defined('KioCMS') || exit;

Block::loadBlocks($module->blocks);

if (!Block::sectorEmpty('left'))
{
	if (!Block::sectorEmpty('right'))
	{
		$columns = 3;
	}
	else
	{
		$columns = 2;
	}
}

// TODO: Zapamiętywanie w ciachach zamykanych/otwieranych boxów

?>
<!DOCTYPE html>
<html lang="<?php echo LANG; ?>">
<head>
<title><?php echo Kio::getTitle(); ?></title>
<meta charset="utf-8" />
<meta name="Author" content="Kiofol Software (www.kiofol.com)" />
<meta name="Generator" content="KioCMS (www.kiocms.com) © <?php echo date('Y'); ?>" />
<meta name="Description" content="<?php echo Kio::getDescription(); ?>" />
<meta name="Keywords" content="<?php echo Kio::getKeywords(); ?>" />
<meta name="Robots" content="index,follow" />

<?php
echo Kio::getCssFiles();
// echo $kio->getRss();
?>

<link rel="stylesheet" type="text/css" href="<?php echo LOCAL.'themes/Kiofol/system.css?'.filemtime(ROOT.'themes/Kiofol/system.css'); ?>" />
<!--[if lt IE 9]>
<script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>
<![endif]-->

<script type="text/javascript" src="<?php echo LOCAL.'system/jquery.js'; ?>"></script>
<script type="text/javascript" src="<?php echo LOCAL.'system/jquery.textarearesizer.js'; ?>"></script>
<script type="text/javascript" src="<?php echo LOCAL.'system/jquery.floatheader.js'; ?>"></script>
<?php echo Kio::getJsFiles(); ?>

<?php echo Kio::getJsCode(); ?>

<script type="text/javascript">
// <![CDATA[
$('#check_logname').click(function()
{
	alert();
});
	var lang = new Array(
		'Czy na pewno chcesz usunąć wybrany element?',
		'sunąć?');
	var confirm_delete = 'Czy na pewno chcesz usunąć wybrany element?';
	var LOCAL = '<?php echo LOCAL; ?>';
	var new_window = 1;
	var AUTH = '<?php echo AUTH; ?>';

// ]]>
</script>


<script type="text/javascript" src="<?php echo LOCAL.'system/misc.js?'.filemtime(ROOT.'system/misc.js'); ?>"></script>
<?php echo Kio::getHead(); ?>
<link rel="alternate" type="application/rss+xml" href="<?php echo LOCAL; ?>rss.php" />
<!--[if lte IE 7]><link rel="stylesheet" type="text/css" href="<?php echo LOCAL; ?>themes/Kiofol/ie.css" /><![endif]-->
</head>

<body id="columns-<?php echo $columns; ?>" class="<?php echo MODULE; ?>">

<div id="header"><!-- HEADER -->


<?php if (Kio::getConfig('multilang')) echo '<div class="langs">'.Kio::langSwitcher().'</div>' ?>

<h1 style="font-size: 50px;">KioCMS</h1>
<h2><?php echo Kio::getConfig('description') ?></h2>
<div style="margin: 0 auto; text-align: center; width: 82%; max-width: 1100px; min-width: 950px;"><div id="navigation"><?php
$navigation = new Navigation();
echo $navigation->content;
// echo Navigation::as_html();
?></div></div>
</div>
<!-- /HEADER -->

<div id="body"><!-- BODY -->

<div id="container">

<div id="inner">

<?php
// Left side blocks
// $kio->isBlocks('left');
//if ($module->sectorEnabled(1) && $module->blocks // $module->blocks->exists(1)
if (!Block::sectorEmpty('left'))
{
	echo '<div id="left"><!-- LEFT -->';

	foreach (Block::getSector('left') as $block)
	{
		echo "\n";
		echo '<div id="'.$block->codename.'" class="block'.($block->subcodename ? ' '.$block->codename.'-'.$block->subcodename : '').'">';
		if ($block->headerVisible && Kio::getConfig('blocks_headers'))
		{
			echo '<div class="block-header"><h4>'.$block->name.'</h4></div>';
		}
		echo '<div class="block-content'.($block->isLast() ? ' last' : '').'">';
		echo $block->content;
		echo '</div></div>';
	}

	echo "\n".'</div><!-- /LEFT -->';
}
?>



<?php
// Right side blocks
if (!Block::sectorEmpty('right'))
{
	echo '<div id="right"><!-- RIGHT -->';

	foreach (Block::getSector('right') as $block)
	{
		echo "\n";
		echo '<div id="'.$block->codename.'" class="block'.($block->subcodename ? ' '.$block->codename.'-'.$block->subcodename : '').'">';
		if ($block->headerVisible && Kio::getConfig('blocks_headers'))
		{
			echo '<div class="block-header"><h4>'.$block->name.'</h4></div>';
		}
		echo '<div class="block-content'.($block->isLast() ? ' last' : '').'">';
		echo $block->content;
		echo '</div></div>';
	}

	echo "\n".'</div><!-- /RIGHT -->';
}
?>
<div id="content"><!-- CONTENT -->
<?php
// Path bar
if (Kio::breadcrumbsExists())
{
	echo '
		<div id="path">
			<a href="'.LOCAL.'"><img style="margin-right: 5px;" src="'.LOCAL.'themes/'.THEME.'/images/home.png" alt="Strona główna" />'.t('Home').'</a>
			'.Kio::getBreadcrumbs().'
		</div>';
}
?>
<?php
// Site main content
echo '<div'.($module->codename ? ' id="'.$module->codename.'"' : '').' class="module'.($module->subcodename ? ' '.$module->codename.'-'.$module->subcodename : '').'">


<div class="module-content">
	<div class="module-header"><h2>'.$module->name.'</h2>'.Kio::getTabs(0).'</div>
	'.$module->content.'
</div></div>';

?>
</div><!-- /CONTENT -->


</div>
</div>
</div><!-- /BODY -->

<div id="footer"><!-- FOOTER -->
<strong>Kio</strong>fol <strong>C</strong>ontent <strong>M</strong>anagment <strong>S</strong>ystem<br /><a href="http://www.kjofol.pl" onclick="target='new'"><b>KioCMS</b></a>
<div style="position: absolute; top: 5px; left: 5px;"><?php echo Kio::getTimer().' / '.$sql->getCounter().' / '.memory_get_usage(); ?></div>
</div><!-- /FOOTER -->

</body>
</html>