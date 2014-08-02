<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>

<?= @helper('behavior.bootstrap'); ?>

<?= $app; ?>

<script>
window.addEvent('domready', function() {
	var selected = null;

	document.id('insert-document').addEvent('click', function(e) {
		e.stop();
		window.parent.jQuery('#storage_path_file').val(selected).trigger('change');
		window.parent.SqueezeBox.close();
	});

	document.id('details').adopt(document.id('document-insert-form'));

	var evt = function(e) {
		var target = document.id(e.target).getParent('.files-node');
		var row = target.retrieve('row');

		selected = row.path;
		document.id('insert-document').set('disabled', false);
	};
	Files.app.grid.addEvent('clickFile', evt);
	Files.app.grid.addEvent('clickImage', evt);
});
</script>

<div id="document-insert-form" style="text-align: center; ">
	<button class="btn btn-primary" type="button" id="insert-document" disabled><?= @text('Insert') ?></button>
</div>