<?php
/**
 * FW Gallery Facebook Images Plugin 3.0
 * @copyright (C) 2014 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/
defined( '_JEXEC' ) or die( 'Restricted access' );
JHTML :: _('behavior.framework');
?>
<legend><?php echo JText :: _('FWGPF_FACEBOOK_IMAGES_IMPORT'); ?></legend>

<form action="<?php echo JRoute :: _('index.php?option=com_fwgallery&view=frontendmanager'); ?>" method="post" name="logoutForm" id="fw-facebook-logout">
	<p>
		<?php echo JText :: _('FWGPF_YOU_ARE_LOGGED_IN_AS'); ?> <strong><?php echo $user['name']; ?></strong>
		<button type="button" class="btn btn-danger" name="logout_button"><?php echo JText :: _('FWGPF_LOG_OUT'); ?></button>
	</p>
	<input type="hidden" name="plugin" value="facebookimages" />
	<input type="hidden" name="direction" value="logout" />
	<input type="hidden" name="tmpl" value="component" />
	<input type="hidden" name="task" value="processPlugin" />
</form>
<div class="tabbable">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#fw-facebook-images-import-panel" data-toggle="tab"><?php echo JText::_('FWGPF_IMPORT_IMAGES_FROM_FACEBOOK'); ?></a></li>
		<li><a href="#fw-facebook-images-export-panel" data-toggle="tab"><?php echo JText::_('FWGPF_EXPORT_IMAGES_TO_FACEBOOK'); ?></a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="fw-facebook-images-import-panel">
			<form action="<?php echo JRoute :: _('index.php?option=com_fwgallery&view=frontendmanager'); ?>" method="post" name="adminForm" id="fw-facebook-images-form-import">
				<table class="admintable">
					<tr>
						<td class="key"><?php echo JText :: _('FWGPF_SELECT_FACEBOOK_GALLERY'); ?></td>
						<td><?php echo JHTML :: _('select.genericlist', (array)$facebook_galleries, 'facebook_gallery_id', '', 'id', 'name'); ?></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText :: _('FWGPF_SELECT_LOCAL_GALLERY'); ?></td>
						<td><?php echo JHTML :: _('fwGalleryCategory.getCategories', 'gallery_id', 0, '', false, JText :: _('FWGPF_CREATE_NEW_WITH_THE_SAME_NAME')); ?>
					</tr>
				</table>
			    <div id="fwgallery-facebookimages-step-import-notice" style="display:none;"><?php echo JText :: _('FWGPF_PLEASE_WAIT_WHILE_YOUR_PHOTOS'); ?>.</div>
				<button type="button" class="btn btn-primary" name="import_button"><?php echo JText :: _('FWGPF_IMPORT'); ?></button>
				<span id="fwgallery-facebookimages-step-import-1"></span>
				<button type="button" class="btn btn-danger" name="stop_button" style="display:none;"><?php echo JText :: _('FWGPF_STOP'); ?></button>
				<input type="hidden" name="plugin" value="facebookimages" />
				<input type="hidden" name="step" value="1" />
				<input type="hidden" name="direction" value="import" />
				<input type="hidden" name="number" value="0" />
				<input type="hidden" name="stop" value="0" />
				<input type="hidden" name="tmpl" value="component" />
				<input type="hidden" name="task" value="processPlugin" />
				<div style="clear:both;"></div>
			</form>
		</div>
		<div class="tab-pane" id="fw-facebook-images-export-panel">
			<form action="<?php echo JRoute :: _('index.php?option=com_fwgallery&view=frontendmanager'); ?>" method="post" name="adminForm" id="fw-facebook-images-form-export">
				<table class="admintable">
					<tr>
						<td class="key"><?php echo JText :: _('FWGPF_SELECT_LOCAL_GALLERY'); ?></td>
						<td><?php echo JHTML :: _('fwGalleryCategory.getCategories', 'gallery_id', 0, '', false); ?>
					</tr>
					<tr>
						<td class="key"><?php echo JText :: _('FWGPF_SELECT_FACEBOOK_GALLERY'); ?></td>
						<td><?php echo JHTML :: _('select.genericlist', array_merge(array(
							JHTML :: _('select.option', '', JText :: _('FWGPF_CREATE_NEW_WITH_THE_SAME_NAME'), 'id', 'name')
						), (array)$facebook_galleries), 'facebook_gallery_id', '', 'id', 'name'); ?></td>
					</tr>
				</table>
			    <div id="fwgallery-facebookimages-step-export-notice" style="display:none;"><?php echo JText :: _('FWGPF_PLEASE_WAIT_WHILE_YOUR_PHOTOS'); ?>.</div>
				<button type="button" class="btn btn-primary" name="export_button"><?php echo JText :: _('FWGPF_EXPORT'); ?></button>
			    <span id="fwgallery-facebookimages-step-export-1"></span>
				<button type="button" class="btn btn-danger" name="stop_button" style="display:none;"><?php echo JText :: _('FWGPF_STOP'); ?></button>
				<input type="hidden" name="plugin" value="facebookimages" />
				<input type="hidden" name="step" value="1" />
				<input type="hidden" name="direction" value="export" />
				<input type="hidden" name="number" value="0" />
				<input type="hidden" name="stop" value="0" />
				<input type="hidden" name="tmpl" value="component" />
				<input type="hidden" name="task" value="processPlugin" />
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
window.addEvent('domready', function() {
	var form_logout = document.getElement('#fw-facebook-logout');
	if (form_logout) {
		form_logout.logout_button.addEvent('click', function() {
			form_logout.set('send', {
				evalScripts: true
			}).send();
		});
	}
	var form_import = document.getElement('#fw-facebook-images-form-import');
	$(form_import.stop_button).addEvent('click', function() {
        form_import.stop.value = 1;
    });
	$(form_import.import_button).addEvent('click', function() {
        var msg = '<?php echo JText :: _('FWGPF_ARE_YOU_SURE_YOU_WANT_TO_IMPORT', true); ?> ';
        msg += form_import.facebook_gallery_id.options[form_import.facebook_gallery_id.selectedIndex].text;
        msg += ' <?php echo JText ::_('FWGPF_ALBUM_FROM_FACEBOOK_TO', true); ?> ';
        if (form_import.gallery_id.selectedIndex > 0) msg += form_import.gallery_id.options[form_import.gallery_id.selectedIndex].text;
        else msg += form_import.facebook_gallery_id.options[form_import.facebook_gallery_id.selectedIndex].text;
		if (confirm(msg+' <?php echo JText :: _('FWGPF_GALLERY'); ?>?')) {
            document.getElement('#fwgallery-facebookimages-step-import-notice').setStyle('display', '');
			document.getElement('#fwgallery-facebookimages-step-import-1').innerHTML = ' - <img src="<?php echo JURI :: root(true); ?>/plugins/fwgallery/facebookimages/facebookimages/assets/images/pleasewait.gif" alt="<?php echo JText :: _('Please wait', true); ?>" />';
            $(form_import.stop_button).setStyle('display', '');
			$(form_import.import_button).setAttribute('disabled', true);
			form_import.step.value = 1;
			form_import.stop.value = 0;
			form_import.set('send', {
				evalScripts: true
			}).send();
		}
	});
	var form_export = document.getElement('#fw-facebook-images-form-export');
	$(form_export.stop_button).addEvent('click', function() {
        form_export.stop.value = 1;
    });
	$(form_export.export_button).addEvent('click', function() {
        var msg = '<?php echo JText :: _('FWGPF_ARE_YOU_SURE_YOU_WANT_TO_EXPORT', true); ?> ';
        msg += form_export.gallery_id.options[form_export.gallery_id.selectedIndex].text;
        msg += ' <?php echo JText ::_('FWGPF_GALLERY_TO_YOUR', true); ?> ';
        if (form_export.facebook_gallery_id.selectedIndex > 0) msg += form_export.facebook_gallery_id.options[form_export.facebook_gallery_id.selectedIndex].text;
        else msg += form_export.gallery_id.options[form_export.gallery_id.selectedIndex].text;
		if (confirm(msg+' <?php echo JText :: _('FWGPF_FACEBOOK_ALBUM'); ?>?')) {
            document.getElement('#fwgallery-facebookimages-step-export-notice').setStyle('display', '');
			document.getElement('#fwgallery-facebookimages-step-export-1').innerHTML = ' - <img src="<?php echo JURI :: root(true); ?>/plugins/fwgallery/facebookimages/facebookimages/assets/images/pleasewait.gif" alt="<?php echo JText :: _('Please wait', true); ?>" />';
            $(form_export.stop_button).setStyle('display', '');
			$(form_export.export_button).setAttribute('disabled', true);
			form_export.step.value = 1;
			form_export.stop.value = 0;
			form_export.set('send', {
				evalScripts: true
			}).send();
		}
	});
});
</script>