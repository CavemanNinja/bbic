<?php
/**
 * FW Installer 1.0.0 - Joomla! Property Manager
 * @copyright (C) 2014 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined('_JEXEC') or die('Restricted access');

JHTML :: _('behavior.framework');
JToolBarHelper::title(JText :: _('FW Installer'));
JToolBarHelper::custom('install', 'copy', 'copy', JText :: _('Install'), false);
?>
<form action="index.php?option=com_batchinstaller&amp;view=batchinstaller" method="post" name="adminForm" id="adminForm">
	<div>
		<table class="table table-striped">
		<a href="http://extensions.joomla.org/extensions/photos-a-images/galleries/photo-gallery/13185" target="_blank"><img style="float:right" src="<?php echo JURI :: root(true); ?>/administrator/components/com_batchinstaller/assets/images/fw_gallery_support.png" /></a>
		<p><?php echo JText :: _('Installer Description'); ?></p>
<?php
if ($this->packages) {
?>
			<tr>
				<th colspan="2">
					<input type="checkbox" id="batchinstaller-checkall" value="1" /> <?php echo JText :: _('Check all'); ?>
				</th>
				<th><?php echo JText :: _('Extension Name'); ?></th>
				<th><?php echo JText :: _('Version'); ?></th>
				<th><?php echo JText :: _('Description'); ?></th>
				<th><?php echo JText :: _('Installed Version'); ?></th>
			</tr>
<?php
	if (!empty($this->packages['Components'])) {
?>
			<tr>
				<td colspan="6">
					<h3><?php echo JText :: _('Components'); ?></h3>
				</td>
			</tr>
<?php
		foreach ($this->packages['Components'] as $package) {
?>
			<tr>
				<td><input type="checkbox" name="packages[]" value="<?php echo $package->filename; ?>" /></td>
<?php
			if ($package->link) {
?>
				<td>
					<a target="_blank" href="<?php echo $package->link; ?>">
						<img src="<?php echo JURI :: root(true); ?>/administrator/components/com_batchinstaller/assets/images/<?php echo $package->image; ?>" />
					</a>
				</td>
				<td>
					<a target="_blank" href="<?php echo $package->link; ?>">
						<?php echo $package->title; ?>
					</a>
				</td>
				<td><?php echo $package->version; ?></td>
				<td><?php echo $package->description; ?></td>
				<td><?php echo $package->installed_version; ?></td>
<?php
			} else {
?>
				<td colspan="4"><?php echo basename($package->title); ?></td>
				<td><?php echo $package->installed_version; ?></td>
<?php
			}
?>
			</tr>
<?php
		}
?>
<?php
	}
?>
<?php
	foreach ($this->packages as $key=>$data) if ($key != 'Components') {
?>
			<tr>
				<td colspan="6">
					<h3><?php echo JText :: _($key); ?></h3>
				</td>
			</tr>
<?php
		foreach ($data as $package) {
?>
			<tr>
				<td><input type="checkbox" name="packages[]" value="<?php echo $package->filename; ?>" /></td>
<?php
			if ($package->link) {
?>
				<td>
					<a target="_blank" href="<?php echo $package->link; ?>">
						<img src="<?php echo JURI :: root(true); ?>/administrator/components/com_batchinstaller/assets/images/<?php echo $package->image; ?>" />
					</a>
				</td>
				<td>
					<a target="_blank" href="<?php echo $package->link; ?>">
						<?php echo $package->title; ?>
					</a>
				</td>
				<td><?php echo $package->version; ?></td>
				<td><?php echo $package->description; ?></td>
				<td><?php echo $package->installed_version; ?></td>
<?php
			} else {
?>
				<td colspan="4"><?php echo basename($package->title); ?></td>
				<td><?php echo $package->installed_version; ?></td>
<?php
			}
?>
			</tr>
<?php
		}
?>
<?php
	}
?>
<?php
}
?>
		</table>
	</div>
	<input type="hidden" name="task" value="" />
</form>
<script type="text/javascript">
window.addEvent('domready', function() {
	document.getElement('#batchinstaller-checkall').addEvent('click', function() {
		var button = this;
		document.getElements('input[type=checkbox]').each(function(el) {
			if (el.name == 'packages[]') el.checked = button.checked;
		});
	});
});
Joomla.submitbutton = function(pressbutton) {
	var has_job = false;
	document.getElements('input[type=checkbox]').each(function(el) {
		if (el.name == 'packages[]' && el.checked) has_job = true;
	});
	if (has_job) {
		var button = document.getElement('#toolbar-copy button');
		if (button) {
			button.setProperty('disabled', 'disabled');
			var img = new Element('img', {
				'src': 'data:image/gif;base64,R0lGODlhEAALAPQAAP///wAAANra2tDQ0Orq6gYGBgAAAC4uLoKCgmBgYLq6uiIiIkpKSoqKimRkZL6+viYmJgQEBE5OTubm5tjY2PT09Dg4ONzc3PLy8ra2tqCgoMrKyu7u7gAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh/hpDcmVhdGVkIHdpdGggYWpheGxvYWQuaW5mbwAh+QQJCwAAACwAAAAAEAALAAAFLSAgjmRpnqSgCuLKAq5AEIM4zDVw03ve27ifDgfkEYe04kDIDC5zrtYKRa2WQgAh+QQJCwAAACwAAAAAEAALAAAFJGBhGAVgnqhpHIeRvsDawqns0qeN5+y967tYLyicBYE7EYkYAgAh+QQJCwAAACwAAAAAEAALAAAFNiAgjothLOOIJAkiGgxjpGKiKMkbz7SN6zIawJcDwIK9W/HISxGBzdHTuBNOmcJVCyoUlk7CEAAh+QQJCwAAACwAAAAAEAALAAAFNSAgjqQIRRFUAo3jNGIkSdHqPI8Tz3V55zuaDacDyIQ+YrBH+hWPzJFzOQQaeavWi7oqnVIhACH5BAkLAAAALAAAAAAQAAsAAAUyICCOZGme1rJY5kRRk7hI0mJSVUXJtF3iOl7tltsBZsNfUegjAY3I5sgFY55KqdX1GgIAIfkECQsAAAAsAAAAABAACwAABTcgII5kaZ4kcV2EqLJipmnZhWGXaOOitm2aXQ4g7P2Ct2ER4AMul00kj5g0Al8tADY2y6C+4FIIACH5BAkLAAAALAAAAAAQAAsAAAUvICCOZGme5ERRk6iy7qpyHCVStA3gNa/7txxwlwv2isSacYUc+l4tADQGQ1mvpBAAIfkECQsAAAAsAAAAABAACwAABS8gII5kaZ7kRFGTqLLuqnIcJVK0DeA1r/u3HHCXC/aKxJpxhRz6Xi0ANAZDWa+kEAA7AAAAAAAAAAAA',
				'style': 'margin-left:4px;'
			});
			img.inject(button, 'after');
		}
		document.adminForm.task.value = pressbutton;
		document.adminForm.submit();
	} else {
		alert('<?php echo JText :: _('Nothing selected', true); ?>');
	}
}
</script>