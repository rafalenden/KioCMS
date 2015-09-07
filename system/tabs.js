jQuery.extend(
{
	loadTab: function(hash)
	{
		jQuery('#tabs li.current').removeClass();
		jQuery('a.tab[rev= ' + hash + ']').parent().addClass('current');
		jQuery('.m-content').load($('a[rev=' + hash + ']').attr('rel'));
	}
});

$(document).ready(function()
{
	var hash = window.location.hash.replace('#', '');

	if (hash && $('a.tab[rev= ' + hash + ']').length > 0)
		$.loadTab(hash);

	$('a.tab[rev]').click(function()
	{
		window.location = '#' + $(this).attr('rev');
		$.loadTab($(this).attr('rev'));
		return false;
	});
});
