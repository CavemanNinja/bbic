<?php
/**
 * FW Slide Gallery Module x.x.x
 * @copyright (C) 2013 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

?>
<div id="mod-fwsg-<?php echo $id; ?>" class="mod-fwsg">
	<div class="holder">
		<ul>
<?php
	foreach ($images as $image) {
?>
			<li>
				<img src="<?php echo JURI :: root(true).$path.$image; ?>" alt="" />
			</li>
<?php
	}
?>
		</ul>
		<div class="clr"></div>
	</div>
	<div class="container wrap_control">
		<div class="control">
			<a class="prev" href="javasrcipt:">#</a>
			<div class="counter">1/<?php echo count($images); ?></div>
			<a class="next" href="javasrcipt:">#</a>
		</div>
	</div>
	<div class="paging"></div>
</div>
<script type="text/javascript">
window.addEvent('domready', function() {
	var gallery = new fadeGallery(document.getElement("#mod-fwsg-<?php echo $id; ?>"), {
		steps: 5,
		paging: false,
		autoplay: true,
		duration: <?php echo $pause; ?>,
		onPlay: function(curr_number) {
			document.getElement('#mod-fwsg-<?php echo $id; ?> .counter').innerHTML = (curr_number + 1) + '/<?php echo count($images) ?>';
		}
	});
});
</script>
