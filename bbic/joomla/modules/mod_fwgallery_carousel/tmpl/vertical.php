<?php
/**
 * FW Gallery Carousel Module 3.0
 * @copyright (C) 2014 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

?>
<div id="mod-fwg-carousel-<?php echo $id; ?>" class="mod-fwg-carousel-vertical<?php if ($class_name = $params->get('moduleclass_sfx')) { ?> mod-fwg-carousel-<?php echo $class_name; } ?>">
	<div id="mod-fwg-carousel-frame-<?php echo $id; ?>" class="mod-fwg-carousel-frame" style="height:<?php echo ($height + 6) * $params->get('qty', 3); ?>px;width:<?php echo $width + 14; ?>px;">
		<div id="mod-fwg-carousel-film-<?php echo $id; ?>" class="mod-fwg-carousel-film" style="height:<?php echo $total_height; ?>px;">
<?php
foreach ($list as $i=>$row) {
	$path = '/images/com_fwgallery/files/'.$row->_user_id.'/';
	if (file_exists(JPATH_SITE.$path.$prefix.$row->filename)) {
		$descr = '';
		if ($params->get('display_galleryname')) $descr = JText :: _('Gallery').': <a href="'.JRoute :: _('index.php?option=com_fwgallery&view=gallery&id='.$row->project_id.':'.JFilterOutput :: stringURLSafe($row->_gallery_name)).'">'.$row->_gallery_name.'</a>';
		if ($params->get('display_username') and $row->_user_name) $descr .= ($descr?'<br/>':'').JText :: _('Author').': '.$row->_user_name;
		if ($params->get('display_imagename')) $descr .= ($descr?'<br/>':'').JText :: _('Image').': '.$row->name;
		if ($params->get('display_imagedate')) $descr .= ($descr?'<br/>':'').JText :: _('Date').': '.JHTML :: date($row->created);
		if ($params->get('display_imageviews')) $descr .= ($descr?'<br/>':'').JText :: _('Hits').': '.(int)$row->hits;
?>
			<div><?php if ($display_iconnew and $row->_is_new) { ?><span class="fwg-icon-new"></span><?php } ?><a href="<?php echo JURI :: root(true).$path.$row->filename; ?>" class="fwg-carousel-lightbox" rel="fwg-carousel-lightbox"><img src="<?php echo JURI :: root(true).$path.$prefix.$row->filename; ?>" alt="<?php echo htmlspecialchars($descr); ?>"/></a></div>
<?php
	}
}
?>
		</div>
	</div>
</div>
<script type="text/javascript">
function fwg_fade(elem, start, end, time) {
	elem.style.opacity = start;
	(function fwg_go() {
		if (start < end) {
			var opacity = elem.style.opacity * 1 + 0.1;
			elem.style.opacity = opacity;
			elem.style.filter = 'alpha(opacity=' + elem.style.opacity * 100 + ')';
			if (elem.style.opacity < end) setTimeout(fwg_go, 50);
		} else {
			var opacity = elem.style.opacity * 1 - 0.1;
			elem.style.opacity = opacity;
			elem.style.filter = 'alpha(opacity=' + elem.style.opacity * 100 + ')';
			if (elem.style.opacity > end) setTimeout(fwg_go, 50);
		}
	})();
}

var fwg_film_eff_<?php echo $id; ?> = {
	stop: function() {
		if (this.timer.length) for (var i = 0; i < this.timer.length; i++) if (this.timer[i]) clearTimeout(this.timer[i]);
		this.timer = new Array;
	},
	start: function(to) {
		var curr_pos = fwg_film_eff_<?php echo $id; ?>.el.style.top?parseInt(fwg_film_eff_<?php echo $id; ?>.el.style.top):0;
		var step = (curr_pos - to) / 10;
		for (var i = 0; i < 10; i++) {
			this.timer.push(
				setTimeout(function() {
					fwg_film_eff_<?php echo $id; ?>.el.style.top = (curr_pos + step * i * -1) + 'px';
				}, 50 * i)
			);
		}
	}
}

window.addEvent('domready', function() {
	document.getElements('a.fwg-carousel-lightbox').cerabox({
		titleFormat: 'Image {number} / {total} {title}'
	});
	var mod_fwg_body = document.getElementById('mod-fwg-carousel-<?php echo $id; ?>');
	var mod_fwg_frame = document.getElementById('mod-fwg-carousel-frame-<?php echo $id; ?>');
	var mod_fwg_film = document.getElementById('mod-fwg-carousel-film-<?php echo $id; ?>');
<?php
if ($total_height > $height * $params->get('qty', 3)) {
?>
	var el = mod_fwg_film, mod_fwg_top = 0;
	do {
		mod_fwg_top += el.offsetTop || 0;
		el = el.offsetParent;
	} while (el);

	var padding = 0;
	var totalContent = <?php echo $total_width; ?>;
	var sliderHeight = <?php echo ($height + 6) * $params->get('qty', 3); ?>;

	fwg_film_eff_<?php echo $id; ?>.el = mod_fwg_film;
	fwg_film_eff_<?php echo $id; ?>.timer = new Array;

	mod_fwg_frame.addEvent('mousemove', function(ev) {
		var mouseCoords = ev.page.y - mod_fwg_top;

		var mousePercentX = mouseCoords/sliderHeight;

		var destY =- (((totalContent-(sliderHeight))-sliderHeight)*(mousePercentX));

		var thePosA = mouseCoords - destY;
		var thePosB = destY - mouseCoords;

		fwg_film_eff_<?php echo $id; ?>.stop();
		if(mouseCoords > destY) {
			fwg_film_eff_<?php echo $id; ?>.start(-thePosA);
		}
		else if(mouseCoords < destY) {
			fwg_film_eff_<?php echo $id; ?>.start(thePosB);
		}
	});
<?php
}
?>
	var fwg_divs = mod_fwg_film.getElementsByTagName('div');
	for (var i = 0; i < fwg_divs.length; i++) {
		var src_images = fwg_divs[i].getElementsByTagName('img');
		if (src_images.length > 0) {
			fwg_divs[i].style.width = '<?php echo $width; ?>px';
			fwg_divs[i].style.height = '<?php echo $height; ?>px';
			src_images[0].style.position = 'absolute';
			src_images[0].style.top = '0';
			src_images[0].style.left = '0';
		}

		fwg_divs[i].addEvent('mouseover', function(ev) {
			var src_images = this.getElementsByTagName('img');
			if (src_images.length > 0) {
				src_images[0].style.zIndex = '10';
				src_images[0].style.left = '-4px';
				src_images[0].style.top = '-4px';
				src_images[0].style.width = '<?php echo $width + 8; ?>px';
			}
			fwg_fade(this, 0.6, 1, 500);
		});
		fwg_divs[i].addEvent('mouseout', function(ev) {
			var src_images = this.getElementsByTagName('img');
			if (src_images.length > 0) {
				src_images[0].style.zIndex = '0';
				src_images[0].style.left = '0';
				src_images[0].style.top = '0';
				src_images[0].style.width = '<?php echo $width; ?>px';
			}
			fwg_fade(this, 1, 0.6, 500);
		});
	}
});

</script>