<?php
/**
 * FW Gallery 3.1.0
 * @copyright (C) 2014 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );
JToolBarHelper :: title(JText::_('FWG_PLUGINS'), 'fwgallery-plugins.png' );

?>
<div class="row-fluid">
	<div class="span7">
		<h3><?php echo JText :: _('FWG_FW_GALLERY_PLUGINS'); ?></h3>
		<p><?php echo JText :: _('FWG_PLUGIN_SECTION'); ?></p>
<?php
if ($this->plugins) {
	foreach ($this->plugins as $plugin) if ($plugin) {
?>
		<fieldset class="adminform">
<?php
		echo $plugin;
?>
		</fieldset>
<?php
	}
} elseif ($this->fwgallery_plugins_installed) {
	echo JText :: _('FWG_PUBLISH_FW_GALLERY_PLUGIN_S__TO_USE_IT');
} else {
	echo JText :: _('FWG_NO_FW_GALLERY_PLUGINS_INSTALLED');
}
?>
	</div>
	<div class="span5">
		<h3><?php echo JText :: _('FWG_LIST_OF_INSTALLED_FW_GALLERY_PLUGINS'); ?></h3>
<?php
if ($this->installed_plugins) {
?>
		<table class="table table-striped">
			<thead>
				<th><?php echo JText :: _('FWG_PLUGIN_NAME'); ?></th>
				<th class="hidden-phone"><?php echo JText :: _('FWG_PLUGIN_VERSION'); ?></th>
				<th><?php echo JText :: _('FWG_PLUGIN_PUBLISH'); ?></th>
			</thead>
			<tbody>
<?php
	foreach ($this->installed_plugins as $i=>$plugin) {
?>
				<tr class="row<?php echo $i%2; ?>">
					<td><?php echo $plugin->element; ?> [<a href="index.php?option=com_plugins&amp;task=plugin.edit&amp;extension_id=<?php echo $plugin->id; ?>"><?php echo JText :: _('FWG_DETAILS'); ?></a>]</td>
					<td class="hidden-phone center"><?php echo $plugin->version; ?></td>
					<td class="center"><a class="btn<?php echo $plugin->published?' active':''; ?>" href="index.php?option=com_fwgallery&amp;view=plugins&task=<?php echo $plugin->published?'unpublish':'publish'; ?>&cid[]=<?php echo $plugin->id; ?>" alt="<?php echo JText :: _($plugin->published?'Click to unpuplish plugin':'Click to publish plugin'); ?>"><i class="icon-<?php echo $plugin->published?'':'un'; ?>publish"></i></a></td>
				</tr>
<?php
	}
?>
			</tbody>
		</table>
<?php
}
?>
	</div>
</div>
