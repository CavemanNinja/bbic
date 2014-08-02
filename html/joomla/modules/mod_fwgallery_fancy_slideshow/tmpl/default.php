<?php
/**
 * FW Gallery Fancy Slideshow 3.0
 * @copyright (C) 2014 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );
JHTML :: _('behavior.framework', true);

?>
<div class="mod-fwgallery" id="mod-fwgallery-<?php echo $id; ?>" style="width: <?php echo $width; ?>px; height: <?php echo $height; ?>px; background-color: <?php echo $params->get('background_color', '#202834'); ?>;">
<?php
if ($list) foreach ($list as $i=>$row) {
	if (!$row->filename) continue;
	$path = FWG_STORAGE.'files/'.$row->_user_id.'/'.$prefix.$row->filename;
	if (!file_exists(JPATH_SITE.'/'.$path)) continue;
?>
	<img src="<?php echo JURI :: root(true); ?>/<?php echo $path; ?>" title="<?php echo $row->name; ?>"<?php if ($i) { ?> style="display:none;"<?php } ?> />
<?php
}
?>
</div>
<script type="text/javascript">
/* initing global variables */
var position<?php echo $id; ?> = 0;
var elements<?php echo $id; ?> = <?php echo (int)max(count($list) - 1, 0); ?>;
var timer<?php echo $id; ?> = 0;
var slices<?php echo $id; ?> = <?php echo (int)$params->get('slices', 20); ?>;
window.addEvent('domready', function() {
	var slider = $('mod-fwgallery-<?php echo $id; ?>');
<?php
if ($params->get('display_image_name')) {
?>
	var el = new Element('div');
	el.addClass('mod-fwgallery-caption');
	var img = $('mod-fwgallery-<?php echo $id; ?>').getElement('img');
	if (img) el.innerHTML = img.title;
	el.inject(slider);
<?php
}
if ($params->get('display_navigation')) {
?>
	var el = new Element('div');
	el.addClass('mod-fwgallery-directionNav');
	el.innerHTML = '<a class="mod-fwgallery-prevNav"><?php echo JText :: _('Prev', true); ?></a><a class="mod-fwgallery-nextNav"><?php echo JText :: _('Next', true); ?></a>';
	el.inject(slider);
	$('mod-fwgallery-<?php echo $id; ?>').getElement('.mod-fwgallery-directionNav').setStyle('display', 'none');
	slider.addEvent('mouseenter', function() {
		$('mod-fwgallery-<?php echo $id; ?>').getElement('.mod-fwgallery-directionNav').setStyle('display', '');
<?php
if ($params->get('run_slideshow')) {
?>
		if (timer<?php echo $id; ?>) clearTimeout(timer<?php echo $id; ?>);
<?php
}
?>
	});
	slider.addEvent('mouseleave', function() {
		$('mod-fwgallery-<?php echo $id; ?>').getElement('.mod-fwgallery-directionNav').setStyle('display', 'none');
<?php
if ($params->get('run_slideshow')) {
?>
		if (timer<?php echo $id; ?>) clearTimeout(timer<?php echo $id; ?>);
		timer<?php echo $id; ?> = setTimeout(function() {
			show_mod_fwgallery(1, <?php echo $id; ?>);
		}, <?php echo $pause; ?>);
<?php
}
?>
	});
	$('mod-fwgallery-<?php echo $id; ?>').getElement('.mod-fwgallery-nextNav').addEvent('click', function() {
<?php
if ($params->get('run_slideshow')) {
?>
		if (timer<?php echo $id; ?>) clearTimeout(timer<?php echo $id; ?>);
<?php
}
?>
		show_mod_fwgallery(1, <?php echo $id; ?>);
	});
	$('mod-fwgallery-<?php echo $id; ?>').getElement('.mod-fwgallery-prevNav').addEvent('click', function() {
<?php
if ($params->get('run_slideshow')) {
?>
		if (timer<?php echo $id; ?>) clearTimeout(timer<?php echo $id; ?>);
<?php
}
?>
		show_mod_fwgallery(-1, <?php echo $id; ?>);
	});
<?php
}
?>
	var sliderSize = slider.getSize();
	var sliceWidth = Math.round(sliderSize.x/slices<?php echo $id; ?>);
	var image = $('mod-fwgallery-<?php echo $id; ?>').getElement('img');
	for(var i = 0; i < slices<?php echo $id; ?>; i++) {
		var el = new Element('div');
		el.addClass('mod-fwgallery-slice');
		el.setStyles({
			'top':'0',
			'height':sliderSize.y,
			'left': (sliceWidth*i)+'px',
			'background-repeat': 'no-repeat',
			'opacity': '0',
			'width':((i == slices<?php echo $id; ?>-1)?((sliderSize.x - sliceWidth*i)+'px'):(sliceWidth+'px')),
			'background-color':'<?php echo $params->get('background_color', '#202834'); ?>'
		});
		if (image) el.setStyle('background-image', 'url('+image.src+')');
		el.inject(slider);
	}
	$$('#mod-fwgallery-<?php echo $id; ?> img').each(function (el, index) {
		center_mod_fwgallery(sliderSize, el);
	});
<?php
if ($params->get('run_slideshow')) {
?>
	if (timer<?php echo $id; ?>) clearTimeout(timer<?php echo $id; ?>);
	timer<?php echo $id; ?> = setTimeout(function() {
		show_mod_fwgallery(1, <?php echo $id; ?>);
	}, <?php echo $pause; ?>);
<?php
}
?>
});
<?php
/* load once module transition engine */
if (!defined('FW_MOD_FWGALLERY_LOADED')) {
	define('FW_MOD_FWGALLERY_LOADED', true);
?>
function center_mod_fwgallery(wrapper_size, el) {
	var imageSize = el.getSize();
	if (imageSize.x > 0) {
		el.setStyles({
			'margin-top':Math.round((wrapper_size.y - imageSize.y)/2),
			'margin-left':Math.round((wrapper_size.x - imageSize.x)/2)
		});
	} else if (el.getStyle('display') == 'none') {
		el.setStyles({
			'left':-10000,
			'display':'',
			'opacity':1
		});
		var imageSize = this.getSize();
		el.setStyles({
			'margin-top':Math.round((wrapper_size.y - imageSize.y)/2),
			'margin-left':Math.round((wrapper_size.x - imageSize.x)/2),
			'display':'none',
			'left':0
		});
	} else {
		el.addEvent('load', function() {
			this.setStyles({
				'left':-10000,
				'display':'',
				'opacity':1
			});
    		var imageSize = this.getSize();
    		this.setStyles({
    			'margin-top':Math.round((wrapper_size.y - imageSize.y)/2),
				'margin-left':Math.round((wrapper_size.x - imageSize.x)/2),
				'display':'none',
				'left':0
			});
		});
	}
}
function show_mod_fwgallery(direction, id) {
	/* if a transition is not displayed now */
	if (!$('mod-fwgallery-'+id).hasClass('active-transition')) {
		/* calculate next element position */
		if(direction == 1) {
			var eval_str = 'var elements = elements'+id+'; var prev_pos = position'+id+'; if (position'+id+' < elements'+id+') position'+id+'++; else position'+id+' = 0; var next_pos = position'+id+';';
			try {
				eval(eval_str);
			} catch(e) {
				alert(e)
			};
		} else {
			var eval_str = 'var elements = elements'+id+'; var prev_pos = position'+id+'; if (position'+id+' > 0) position'+id+'--; else position'+id+' = elements'+id+'; var next_pos = position'+id+';';
			try {
				eval(eval_str);
			} catch(e) {
				alert(e)
			};
		}
		var slider = $('mod-fwgallery-<?php echo $id; ?>');
		var sliderSize = slider.getSize();
		var sliceWidth = Math.round(sliderSize.x/slices<?php echo $id; ?>);

		/* copy next image into the slices and set start values */
		$$('#mod-fwgallery-'+id+' img').each(function (el, index) {
			if (index == prev_pos) {
				/* find and set active image */
				el.setStyles({'display':'', 'opacity':'1'});
				center_mod_fwgallery(sliderSize, el);
			} else if (index == next_pos) {
				/* copy next image into the slices */
				el.setStyles({
					'left':-10000,
					'display':'',
					'opacity':1
				});
				var imageSize = el.getSize();
				el.setStyles({
					'display':'none',
					'left':0
				});

				x_shift = Math.round((sliderSize.x - imageSize.x)/2);
				y_shift = Math.round((sliderSize.y - imageSize.y)/2);
				$$('#mod-fwgallery-'+id+' div.mod-fwgallery-slice').each(function(sl, slide_index) {
					sl.setStyles({
						'background-position': ((sliceWidth*slide_index*-1)+x_shift)*1+'px '+y_shift+'px',
						'opacity':'0',
						'background-image':'url('+el.src+')'
					});
				});
<?php
if ($params->get('display_image_name')) {
?>
				$('mod-fwgallery-<?php echo $id; ?>').getElement('.mod-fwgallery-caption').innerHTML = el.getAttribute('title');
<?php
}
?>
			} else {
				el.setStyle('display', 'none');
			}
		});

		var transition = '<?php echo $params->get('transition'); ?>';
		if (transition == 'random') transition = Math.floor(Math.random() * 4);
		else if (transition != 'none') transition *= 1;
		$('mod-fwgallery-'+id).addClass('active-transition');
		switch (transition) {
			case 0: mod_fwgallery_sliceDownRight(id, prev_pos, next_pos);
			break;
			case 1: mod_fwgallery_sliceDownLeft(id, prev_pos, next_pos);
			break;
			case 2: mod_fwgallery_fold(id, prev_pos, next_pos);
			break;
			case 3: mod_fwgallery_fade(id, prev_pos, next_pos);
			break;
			default: mod_fwgallery_none(id, prev_pos, next_pos);
		}
		setTimeout(function() {
			$('mod-fwgallery-'+id).removeClass('active-transition');
		}, 100 + slices<?php echo $id; ?> * 50);
	}
<?php
if ($params->get('run_slideshow')) {
?>
	if (timer<?php echo $id; ?>) clearTimeout(timer<?php echo $id; ?>);
	timer<?php echo $id; ?> = setTimeout(function() {
		show_mod_fwgallery(1, <?php echo $id; ?>);
	}, <?php echo $pause; ?>);
<?php
}
?>
}
function mod_fwgallery_sliceDownRight(id, prev_pos, next_pos) {
	var time_buffer = 0;
	$$('#mod-fwgallery-'+id+' div.mod-fwgallery-slice').each(function (el) {
		var slice = el;
		var height = el.getStyle('height');
		slice.setStyles({'height':0, 'opacity':1, 'background-color':'<?php echo $params->get('background_color', '#202834'); ?>'});
		setTimeout(function() {
			new Fx.Morph(slice).start({'height':height});
		}, 100 + time_buffer);
		time_buffer += 50;
	});
}
function mod_fwgallery_sliceDownLeft(id, prev_pos, next_pos) {
	var time_buffer = 50 * <?php echo (int)$params->get('slices', 20); ?>;
	$$('#mod-fwgallery-'+id+' div.mod-fwgallery-slice').each(function (el) {
		var slice = el;
		var height = el.getStyle('height');
		slice.setStyles({'height':0, 'opacity':1});
		setTimeout(function() {
			new Fx.Morph(slice).start({'height':height});
		}, 100 + time_buffer);
		time_buffer -= 50;
	});
}
function mod_fwgallery_fold(id, prev_pos, next_pos) {
	var time_buffer = 0;
	$$('#mod-fwgallery-'+id+' div.mod-fwgallery-slice').each(function (el) {
		var slice = el;
		var width = el.getStyle('width');
		slice.setStyles({'width':0, 'opacity':1});
		setTimeout(function() {
			new Fx.Morph(slice).start({'width':width});
		}, 100 + time_buffer);
		time_buffer += 50;
	});
}
function mod_fwgallery_fade(id, prev_pos, next_pos) {
	$$('#mod-fwgallery-'+id+' img').each(function (el, index) {
		if (index == prev_pos) {
			/* start fade off for being displayed image */
			var fadeoff = new Fx.Morph(el, {duration:1000, wait:false}).start({'opacity':0});
		}
	});
	/* start fade on for the next image */
	$$('#mod-fwgallery-'+id+' div.mod-fwgallery-slice').each(function (el) {
		el.setStyles({'opacity':'0'});
		var fadeon = new Fx.Morph(el, {duration:1000, wait:false}).start({'opacity':1});
	});
}
function mod_fwgallery_none(id, prev_pos, next_pos) {
	$$('#mod-fwgallery-'+id+' div.mod-fwgallery-slice').each(function (el) {
		el.setStyles({'opacity':'1'});
	});
}
<?php
}
?>
</script>