<?php
/**
 * FW Gallery 3.1.0
 * @copyright (C) 2014 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );
$qty_images = count($this->images);
?>
<script type="text/javascript">
with (parent) {
	document.getElement('#fwgallery-manualresize-step-1').innerHTML = ' - <?php echo JText :: sprintf('FWG_FOUND_IMAGES', $qty_images); ?>';
<?php
if ($qty_images) {
?>
	document.getElement('#fwgallery-manualresize-step-2').innerHTML = ' - <img src="<?php echo JURI :: root(true); ?>/administrator/components/com_fwgallery/assets/images/pleasewait.gif" alt="<?php echo JText :: _('FWG_PLEASE_WAIT', true); ?>" />';
	document.getElement('#fw-manualresize-form').last_id.value = '<?php echo $this->images[0]->id; ?>';
	document.getElement('#fw-manualresize-form').step.value = 2;
	document.getElement('#fw-manualresize-form').set('send', {
		evalScripts: true
	}).send();
<?php
}
?>
}
</script>
