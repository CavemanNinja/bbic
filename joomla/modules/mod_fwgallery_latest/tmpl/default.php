<?php
/**
 * FW Gallery Latest 3.0
 * @copyright (C) 2014 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );
JHTML :: _('behavior.framework');

if ($list) {
	$module_id = rand();
	$height = (int)$params->get('height');
?>
<div id="mod-fwgallery-latest-<?php echo $module_id; ?>"<?php if ($params->get('layout') == 'horizontal') { ?> class="mod-fwgallery-latest-horizontal"<?php } ?>>
<?php
	foreach ($list as $row) {
		if (!$row->filename) continue;
		$path = FWG_STORAGE.'files/'.$row->_user_id.'/'.$prefix.$row->filename;
		if (!file_exists(JPATH_SITE.'/'.$path)) continue;
?>
	<div class="mod-fwgallery-latest-image"<?php if ($height) { ?> style="height:<?php echo $height; ?>px;"<?php } ?>>
		<a href="<?php echo JRoute::_('index.php?option=com_fwgallery&view=image&id='.$row->id.':'.JFilterOutput :: stringURLSafe($row->name).'&Itemid='.JFHelper :: getItemid('image', $row->id, JRequest :: getInt('Itemid')).'#fwgallerytop'); ?>">
			<div class="fwgs-image">
				<img src="<?php echo JURI :: root(true); ?>/<?php echo $path; ?>" title="<?php echo $row->name; ?>" />
			</div>
<?php
		if ($params->get('display_name') or $params->get('display_data')) {
?>
			<div class="mod-fwgallery-latest-image-info" style="display:none;">
<?php
			if ($params->get('display_name')) { ?><p><?php echo $row->name; ?></p><?php }
			if ($params->get('display_data')) { ?><p><?php echo JHTML :: date($row->created); ?></p><?php }
?>
			</div>
<?php
		}
?>
		</a>
	</div>
<?php
	}
?>
	<div style="clear:both;"></div>
</div>
<script type="text/javascript">
window.addEvent('domready', function() {
	$$('#mod-fwgallery-latest-<?php echo $module_id; ?> .mod-fwgallery-latest-image').each(function(el) {
		var info = el.getElements('div.mod-fwgallery-latest-image-info');
		if (info) {
			el.addEvent('mouseenter', function() {
				info.setStyle('display', '');
			});
			el.addEvent('mouseleave', function() {
				info.setStyle('display', 'none');
			});
		}
	});
});
</script>
<?php
}
?>