<?php
/**
 * FW Real Estate Map Module 2.0.0
 * @copyright (C) 2013 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );
JHTML :: _('behavior.framework', true);
JHTML :: script('http://maps.google.com/maps/api/js?sensor=false');
$id = rand();
?>
<div id="mod-fwg-map-<?php echo $id; ?>" class="mod-fwg-map" style="width:100%;height:<?php echo $height; ?>px;"></div>
<script type="text/javascript">
var map_<?php echo $id; ?>;
var info_<?php echo $id; ?>;
window.addEvent('domready', function() {
	map_<?php echo $id; ?> = new google.maps.Map(document.getElementById('mod-fwg-map-<?php echo $id; ?>'), {mapTypeId: google.maps.MapTypeId.ROADMAP});
	var bounds = new google.maps.LatLngBounds();
<?php
foreach ($list as $row) {
?>
	var myLatLng = new google.maps.LatLng(<?php echo $row->latitude; ?>, <?php echo $row->longitude; ?>);
	bounds.extend(myLatLng);
	map_<?php echo $id; ?>.initialZoom = true;
	map_<?php echo $id; ?>.fitBounds(bounds);

	var marker = new google.maps.Marker({
	      position: myLatLng,
	      title:'<?php echo addcslashes($row->name, "'"); ?>',
	      map: map_<?php echo $id; ?>
	});
	google.maps.event.addListener(marker, 'click', function() {
		fwg_show_info(this, <?php echo $row->id; ?>);
	});
<?php
}
?>
	google.maps.event.addListener(map_<?php echo $id; ?>, 'zoom_changed', function() {
	    zoomChangeBoundsListener =
	        google.maps.event.addListener(map_<?php echo $id; ?>, 'bounds_changed', function(event) {
	            if (this.getZoom() > 15 && this.initialZoom == true) {
	                this.setZoom(15);
	                this.initialZoom = false;
	            }
		        google.maps.event.removeListener(zoomChangeBoundsListener);
		    });
	});
	google.maps.event.addDomListener(window, "resize", function() {
		var center = map_<?php echo $id; ?>.getCenter();
		google.maps.event.trigger(map_<?php echo $id; ?>, "resize");
		map_<?php echo $id; ?>.setCenter(center);
	});
});
function fwg_show_info(marker, id) {
	if (info_<?php echo $id; ?>) info_<?php echo $id; ?>.close();
	info_<?php echo $id; ?> = new google.maps.InfoWindow({
		maxWidth: 350,
	    content: '<?php echo JText :: _('FWGMAP_LOADING'); ?> <img src="<?php echo JURI :: root(true); ?>/modules/mod_fwgallery_map/assets/images/wait.gif" />'
	});
	info_<?php echo $id; ?>.open(map_<?php echo $id; ?>, marker);
	new Request({
		url: '<?php echo JRoute :: _('index.php?option=com_fwgallery&view=image', false); ?>',
		method: 'post',
		onSuccess: function(html) {
			var data = JSON.decode(html);
			if (data && data.link && data.com_link) {
				var buff = '<div><a href="'+data.com_link+'"><img src="'+data.th_link+'" /></a><div><a href="'+data.com_link+'">'+data.name+'</a></div></div>';
				info_<?php echo $id; ?>.setContent(buff);
			}
		}
	}).send('id='+id+'&format=json');
}
</script>