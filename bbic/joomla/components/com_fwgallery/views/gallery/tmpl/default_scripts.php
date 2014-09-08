<?php
/**
 * FW Gallery 3.1.0
 * @copyright (C) 2014 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

JHTML :: _('behavior.framework', true);
JHTML :: script('components/com_fwgallery/assets/js/cerabox.min.js');
$lightbox_size = $this->params->get('lightbox_size');
if (!$lightbox_size) $lightbox_size = 90;
?>
<script type="text/javascript">
document.addEvent('domready', function() {
	var size = window.getSize();
	document.getElements('a.fwg-lightbox').cerabox({
		titleFormat: 'Image {number} / {total} {title}',
		width: '<?php echo $lightbox_size; ?>%',
		constrainProportions: true
	});
<?php
if ($this->params->get('voting_type')) {
?>
	document.getElements('.fwg-thumb-vote').each(function(el) {
		el.removeEvents('click').addEvent('click', function() {
<?php
	if (!$this->params->get('public_voting') and !$this->user->id) {
?>
			alert('<?php echo JText :: _('FWG_VOTING_IS_AVAILABLE_FOR_REGISTERED_USERS_ONLY__PLEASE_REGISTER_', true); ?>');
<?php
	} else {
?>
			var id = this.getParent().getParent().id.replace('rating', '');
			var val = this.hasClass('fwg-thumb-up')?1:-1;
			new Request({
				url: '<?php echo JRoute :: _('index.php', false); ?>',
				onSuccess: function(html) {
					var el = document.getElement('#rating'+id);
					if (el) el.innerHTML = html;
				}
			}).send('format=raw&option=com_fwgallery&view=gallery&task=vote&id='+id+'&value='+val);
<?php
	}
?>
		});
	});
<?php
} else {
?>
	document.getElements('.fwg-star-rating-not-logged').each(function(el) {
		el.removeEvents('click').addEvent('click', function() {
			alert('<?php echo JText :: _('FWG_VOTING_IS_AVAILABLE_FOR_REGISTERED_USERS_ONLY__PLEASE_REGISTER_', true); ?>');
		});
	});
	document.getElements('.fwg-star-rating').each(function(rating) {
		rating.getElements('.fwgallery-stars').each(function(star) {
			star.removeEvents('click');
			star.addEvent('click', function() {
				var ids = this.getProperty('rel').match(/^(\d+)_(\d+)$/);
				if (ids.length == 3) {
					new Request({
						url: '<?php echo JRoute :: _('index.php', false); ?>',
						onSuccess: function(html) {
							var el = document.getElement('#rating'+ids[1]);
							if (el) el.innerHTML = html;
						}
					}).send('format=raw&option=com_fwgallery&view=gallery&task=vote&id='+ids[1]+'&value='+ids[2]);
				}
			});
		});
	});
<?php
}
?>
});
</script>