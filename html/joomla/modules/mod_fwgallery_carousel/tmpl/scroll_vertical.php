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
	<div id="mod-fwg-carousel-up-button-<?php echo $id; ?>" class="mod-fwg-carousel-up-button mod-fwg-carousel-up-button-disabled"></div>
	<div id="mod-fwg-carousel-frame-<?php echo $id; ?>" class="mod-fwg-carousel-frame" style="height:<?php echo min($total_height, ($height + 6) * $params->get('qty', 3)); ?>px;width:<?php echo $width + 14; ?>px;">
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
	<div id="mod-fwg-carousel-down-button-<?php echo $id; ?>" class="mod-fwg-carousel-down-button<?php if ($total_qty <= $params->get('qty', 3)) { ?> mod-fwg-carousel-down-button-disabled<?php } ?>"></div>
</div>
<script type="text/javascript">
var mod_fwg_carousel_pos_<?php echo $id; ?> = 0;

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

window.addEvent('domready', function(){
	document.getElements('a.fwg-carousel-lightbox').cerabox({
		titleFormat: 'Image {number} / {total} {title}'
	});
	var mod_fwg_body = document.getElementById('mod-fwg-carousel-<?php echo $id; ?>');
	var mod_fwg_frame = document.getElementById('mod-fwg-carousel-frame-<?php echo $id; ?>');
	var mod_fwg_film = document.getElementById('mod-fwg-carousel-film-<?php echo $id; ?>');

	var mod_fwg_up = document.getElementById('mod-fwg-carousel-up-button-<?php echo $id; ?>');
	var mod_fwg_down = document.getElementById('mod-fwg-carousel-down-button-<?php echo $id; ?>');

	mod_fwg_down.addEvent('click', function(ev) {
		var film_height = parseInt(mod_fwg_film.style.height) * -1;
		if (mod_fwg_carousel_pos_<?php echo $id; ?> > film_height + <?php echo (int)min($total_height, ($height + 6) * $params->get('qty', 3)); ?>) {
			var class_name = mod_fwg_up.className;
			if (class_name.match(/disabled/)) mod_fwg_up.className = 'mod-fwg-carousel-up-button';
			var class_name = this.className;
			if (class_name.match(/disabled/)) this.className = 'mod-fwg-carousel-down-button';

			this.className.replace(' mod-fwg-carousel-down-button-disabled', '');
			mod_fwg_carousel_pos_<?php echo $id; ?> = Math.max(film_height, mod_fwg_carousel_pos_<?php echo $id; ?> - <?php echo $height + 4; ?>);
			(function fwg_go_up() {
				var top = mod_fwg_film.style.top.replace(/([\d.]+)(px|pt|em|%)/,'$1') * 1 - 20;
				mod_fwg_film.style.top = top+'px';
				if (top < (film_height + <?php echo (int)min($total_height, ($height + 6) * $params->get('qty', 3)); ?>)) {
					var class_name = mod_fwg_down.className;
					if (!class_name.match(/disabled/)) mod_fwg_down.className += ' mod-fwg-carousel-down-button-disabled';
				}
				if (top > mod_fwg_carousel_pos_<?php echo $id; ?>) setTimeout(fwg_go_up, 10);
			})();
		} else {
			var class_name = this.className;
			if (!class_name.match(/disabled/)) this.className += ' mod-fwg-carousel-down-button-disabled';
		}
	});

	mod_fwg_up.addEvent('click', function(ev) {
		if (mod_fwg_carousel_pos_<?php echo $id; ?> < 0) {
			var class_name = mod_fwg_down.className;
			if (class_name.match(/disabled/)) mod_fwg_down.className = 'mod-fwg-carousel-down-button';
			var class_name = this.className;
			if (class_name.match(/disabled/)) this.className = 'mod-fwg-carousel-up-button';
			mod_fwg_carousel_pos_<?php echo $id; ?> = Math.min(0, mod_fwg_carousel_pos_<?php echo $id; ?> + <?php echo $height + 4; ?>);
			(function fwg_go_down() {
				var top = mod_fwg_film.style.top.replace(/([\d.]+)(px|pt|em|%)/,'$1') * 1 + 20;
				mod_fwg_film.style.top = top+'px';
				if (mod_fwg_carousel_pos_<?php echo $id; ?> == 0) {
					var class_name = mod_fwg_up.className;
					if (!class_name.match(/disabled/)) mod_fwg_up.className += ' mod-fwg-carousel-up-button-disabled';
				}
				if (top < mod_fwg_carousel_pos_<?php echo $id; ?>) setTimeout(fwg_go_down, 10);
			})();
		}
	});

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