<?php
/**
 * FW Gallery Carousel Module 3.0
 * @copyright (C) 2014 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<div id="mod-fwg-carousel-<?php echo $id; ?>" class="mod-fwg-carousel-horizontal<?php if ($class_name = $params->get('moduleclass_sfx')) { ?> mod-fwg-carousel-<?php echo $class_name; } ?>">
	<table>
		<tr>
			<td>
				<div id="mod-fwg-carousel-left-button-<?php echo $id; ?>" class="mod-fwg-carousel-left-button mod-fwg-carousel-left-button-disabled"></div>
			</td>
			<td>
				<div id="mod-fwg-carousel-frame-<?php echo $id; ?>" class="mod-fwg-carousel-frame" style="height:<?php echo $height + 14; ?>px;width:<?php echo ($width + 6) * $params->get('qty', 3); ?>px;">
					<div id="mod-fwg-carousel-film-<?php echo $id; ?>" class="mod-fwg-carousel-film" style="width:<?php echo $total_width; ?>px;">
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
			</td>
			<td>
				<div id="mod-fwg-carousel-right-button-<?php echo $id; ?>" class="mod-fwg-carousel-right-button<?php if ($total_qty <= $params->get('qty', 3)) { ?> mod-fwg-carousel-right-button-disabled<?php } ?>"></div>
			</td>
		</tr>
	</table>
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

var fwg_film_eff_<?php echo $id; ?> = {
	stop: function() {
		if (this.timer.length) for (var i = 0; i < this.timer.length; i++) if (this.timer[i]) clearTimeout(this.timer[i]);
		this.timer = new Array;
	},
	start: function(to) {
		var curr_pos = fwg_film_eff_<?php echo $id; ?>.el.style.left?parseInt(fwg_film_eff_<?php echo $id; ?>.el.style.left):0;
		var step = (curr_pos - to) / 10;
		for (var i = 0; i < 10; i++) {
			this.timer.push(
				setTimeout(function() {
					fwg_film_eff_<?php echo $id; ?>.el.style.left = (curr_pos + step * i * -1) + 'px';
				}, 50 * i)
			);
		}
	}
}

window.addEvent('domready', function() {
	document.getElements('a.fwg-carousel-lightbox').cerabox({
		titleFormat: 'Image {number} / {total} {title}'
	});
	var mod_fwg_body = document.getElement('#mod-fwg-carousel-<?php echo $id; ?>');
	var mod_fwg_frame = document.getElement('#mod-fwg-carousel-frame-<?php echo $id; ?>');
	var mod_fwg_film = document.getElement('#mod-fwg-carousel-film-<?php echo $id; ?>');

	var mod_fwg_left = document.getElement('#mod-fwg-carousel-left-button-<?php echo $id; ?>');
	var mod_fwg_right = document.getElement('#mod-fwg-carousel-right-button-<?php echo $id; ?>');

	window.addEvent('resize', function() {
		var width = mod_fwg_body.getSize().x * 1;
		mod_fwg_frame.setStyle('width', width - 40);

		var film_width = parseInt(mod_fwg_film.style.width) * -1;
		var frame_width = parseInt(mod_fwg_frame.style.width);

		var left = mod_fwg_film.style.left.replace(/([\d.]+)(px|pt|em|%)/,'$1') * 1 - 20;
		var class_name = mod_fwg_right.className;
		if (left <= film_width + frame_width) {
			if (!class_name.match(/disabled/)) mod_fwg_right.className += ' mod-fwg-carousel-right-button-disabled';
		} else {
			mod_fwg_right.className = 'mod-fwg-carousel-right-button';
		}

	}).fireEvent('resize');

	mod_fwg_right.addEvent('click', function(ev) {
		var film_width = parseInt(mod_fwg_film.style.width) * -1;
		var frame_width = parseInt(mod_fwg_frame.style.width);
		if (mod_fwg_carousel_pos_<?php echo $id; ?> > film_width + frame_width) {
			var class_name = mod_fwg_left.className;
			if (class_name.match(/disabled/)) mod_fwg_left.className = 'mod-fwg-carousel-left-button';
			var class_name = this.className;
			if (class_name.match(/disabled/)) this.className = 'mod-fwg-carousel-right-button';

			mod_fwg_carousel_pos_<?php echo $id; ?> = Math.max(film_width, mod_fwg_carousel_pos_<?php echo $id; ?> - <?php echo $width + 6; ?>);
			(function fwg_go_left() {
				var left = mod_fwg_film.style.left.replace(/([\d.]+)(px|pt|em|%)/,'$1') * 1 - 20;
				mod_fwg_film.style.left = left+'px';
				if (left <= film_width + frame_width) {
					var class_name = mod_fwg_right.className;
					if (!class_name.match(/disabled/)) mod_fwg_right.className += ' mod-fwg-carousel-right-button-disabled';
				}
				if (left > mod_fwg_carousel_pos_<?php echo $id; ?>) setTimeout(fwg_go_left, 10);
			})();
		}
	});

	mod_fwg_left.addEvent('click', function(ev) {
		var film_width = parseInt(mod_fwg_film.style.width) * -1;
		var frame_width = parseInt(mod_fwg_frame.style.width);
		if (mod_fwg_carousel_pos_<?php echo $id; ?> < 0) {
			var left = mod_fwg_film.style.left.replace(/([\d.]+)(px|pt|em|%)/,'$1') * 1 + 20;
			var class_name = mod_fwg_right.className;
			if (left > film_width + frame_width) {
				if (class_name.match(/disabled/)) mod_fwg_right.className = 'mod-fwg-carousel-right-button';
			} else {
				if (!class_name.match(/disabled/)) mod_fwg_right.className += ' mod-fwg-carousel-right-button-disabled';
			}
			var class_name = this.className;
			if (class_name.match(/disabled/)) this.className = 'mod-fwg-carousel-left-button';

			mod_fwg_carousel_pos_<?php echo $id; ?> = Math.min(0, mod_fwg_carousel_pos_<?php echo $id; ?> + <?php echo $width + 4; ?>);
			(function fwg_go_right() {
				var left = mod_fwg_film.style.left.replace(/([\d.]+)(px|pt|em|%)/,'$1') * 1 + 20;
				mod_fwg_film.style.left = left+'px';
				if (mod_fwg_carousel_pos_<?php echo $id; ?> == 0) {
					var class_name = mod_fwg_left.className;
					if (!class_name.match(/disabled/)) mod_fwg_left.className += ' mod-fwg-carousel-left-button-disabled';
				}
				if (left < mod_fwg_carousel_pos_<?php echo $id; ?>) setTimeout(fwg_go_right, 10);
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
				src_images[0].style.height = '<?php echo $height + 8; ?>px';
			}
			fwg_fade(this, 0.6, 1, 500);
		});
		fwg_divs[i].addEvent('mouseout', function(ev) {
			var src_images = this.getElementsByTagName('img');
			if (src_images.length > 0) {
				src_images[0].style.zIndex = '0';
				src_images[0].style.left = '0';
				src_images[0].style.top = '0';
				src_images[0].style.height = '<?php echo $height; ?>px';
			}
			fwg_fade(this, 1, 0.6, 500);
		});
	}
});

</script>