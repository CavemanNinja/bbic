<?php
/**
 * FW Gallery Picasa Import Plugin 3.0
 * @copyright (C) 2014 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/
defined( '_JEXEC' ) or die( 'Restricted access' );
JHTML :: _('behavior.framework');
?>
<legend><?php echo JText :: _('Picasa images import'); ?></legend>

<form action="index.php?option=com_fwgallery&amp;view=plugins" method="post" name="adminForm" id="fw-picasa-images-form-import" target="plg_fwg_picasa">
	<table class="admintable">
		<tr>
			<td class="key"><?php echo JText :: _('Gallery Username'); ?></td>
			<td>
				<input name="picasa_username" title="<?php echo htmlspecialchars(JText :: _('FWGPI_USERNAME_HINT'), ENT_NOQUOTES, 'UTF-8'); ?>" value="<?php echo htmlspecialchars($picasa_username, ENT_NOQUOTES, 'UTF-8'); ?>" />
				<input type="button" onclick="with(this.form) {step.value=0;submit();}" value="<?php echo JText :: _('Load galleries'); ?>" />
			</td>
		</tr>
		<tr>
			<td class="key"><?php echo JText :: _('Select a Picasa gallery'); ?></td>
			<td><?php echo JHTML :: _('select.genericlist', (array)$picasa_galleries, 'picasa_gallery_id', '', 'id', 'name', '', 'plg-fwg-picasa-gallery'); ?></td>
		</tr>
		<tr>
			<td class="key"><?php echo JText :: _('Select a local gallery'); ?></td>
			<td><?php echo JHTML :: _('fwGalleryCategory.getCategories', 'gallery_id', 0, '', false, JText :: _('Create a new one with the same name')); ?>
		</tr>
	</table>
	<div style="clear:all"></div>
    <div id="fwgallery-picasaimport-step-import-notice" style="display:none;"><?php echo JText :: _('Please wait while your photos are being uploaded.<br/>It can take some time depending on the size of your gallery'); ?>.</div>
	<input type="button" name="import_button" value="<?php echo JText :: _('Import'); ?>" />
	<span id="fwgallery-picasaimport-step-import-1"></span>
	<input type="button" name="stop_button" style="display:none;" value="<?php echo JText :: _('Stop'); ?>" />
	<input type="hidden" name="plugin" value="picasaimport" />
	<input type="hidden" name="step" value="1" />
	<input type="hidden" name="number" value="0" />
	<input type="hidden" name="stop" value="0" />
	<input type="hidden" name="tmpl" value="component" />
	<input type="hidden" name="task" value="edit" />
</form>
<iframe name="plg_fwg_picasa" style="display:none;"></iframe>

<script type="text/javascript">
window.addEvent('domready', function() {
	var form_import = $('fw-picasa-images-form-import');
	$(form_import.stop_button).addEvent('click', function() {
        form_import.stop.value = 1;
    });
	$(form_import.import_button).addEvent('click', function() {
        var msg = '<?php echo JText :: _('Are you sure you want to import', true); ?> ';
        msg += form_import.picasa_gallery_id.options[form_import.picasa_gallery_id.selectedIndex].text;
        msg += ' <?php echo JText ::_('album from Picasa to', true); ?> ';
        if (form_import.gallery_id.selectedIndex > 0) msg += form_import.gallery_id.options[form_import.gallery_id.selectedIndex].text;
        else msg += form_import.picasa_gallery_id.options[form_import.picasa_gallery_id.selectedIndex].text;
		if (confirm(msg+' <?php echo JText :: _('gallery'); ?>?')) {
            $('fwgallery-picasaimport-step-import-notice').setStyle('display', '');
			$('fwgallery-picasaimport-step-import-1').innerHTML = ' - <img src="<?php echo JURI :: root(true); ?>/plugins/fwgallery/picasaimport/picasaimport/assets/images/pleasewait.gif" alt="<?php echo JText :: _('Please wait', true); ?>" />';
            $(form_import.stop_button).setStyle('display', '');
			$(form_import.import_button).setAttribute('disabled', true);
			form_import.step.value = 1;
			form_import.stop.value = 0;
			form_import.set('send', {
				evalScripts: true
			}).send();
		}
	});
});
</script>