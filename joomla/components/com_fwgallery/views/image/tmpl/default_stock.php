<?php
/**
 * FW Gallery 3.1.0
 * @copyright (C) 2014 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined('_JEXEC') or die('Restricted access');

$id = 'fwg-stock-'.rand();
JHTML :: script('components/com_fwgallery/assets/js/lightbox_plus_min.js', false);
$select = array();
foreach ($this->row->_stock as $s) {
	$row = new stdclass;
	$row->id = $s->id;
	$row->name = $s->width.'x'.$s->height.' '.JFHelper :: formatPrice($s->price);
	$select[] = $row;
}
?>
				<div>
					<form action="<?php echo JRoute :: _('index.php?option=com_fwgallery&view=image&id='.$this->row->id); ?>" style="text-align: center;">
						<?php echo JHTML :: _('select.genericlist', $select, 'file_stock_id', '', 'id', 'name'); ?>
						<button type="button" id="<?php echo $id; ?>"><?php echo JText :: _('FWG_BUY'); ?></button>
						<input type="hidden" name="format" value="json" />
						<input type="hidden" name="layout" value="buy" />
					</form>

					<script type="text/javascript">
					var httpStRequest = false;
					window.addEvent('domready', function() {
						var button = document.getElement('#<?php echo $id ?>');
						button.addEvent('click', function(ev) {
							new Request.JSON({
								url: '<?php echo JRoute :: _('index.php?option=com_fwgallery&view=image&id='.$this->row->id, false); ?>',
								data: {
									'format': 'json',
									'layout': 'buy',
									'file_stock_id': this.form.file_stock_id.value
								},
								method: 'post',
								onSuccess: function(result) {
									if (result.msg) alert(result.msg);
									if (result.link) location = result.link;
								}
							}).send();
						});
					});
					</script>
				</div>
