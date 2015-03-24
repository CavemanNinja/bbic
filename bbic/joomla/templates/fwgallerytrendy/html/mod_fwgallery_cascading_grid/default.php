<?php
/**
 * FW Gallery Cascading Grid Module 1.0.0
 * @copyright (C) 2014 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

JHTML :: _('behavior.framework', true);
JHTML :: script('components/com_fwgallery/assets/js/cerabox.min.js');
JHTML :: script('modules/mod_fwgallery_cascading_grid/assets/js/masonry.pkgd.min.js');
$com_params = JComponentHelper :: getParams('com_fwgallery');
$lightbox_size = $com_params->get('lightbox_size');
if (!$lightbox_size) $lightbox_size = 90;
?>

<?php
if ($subcategories) {
?>
	<div id="fwg-subcategories">
		<button type="button" class="btn" id="fwg-subcat-<?php echo $category->id?$category->id:0; ?>"><?php echo $category->id?$category->name:JText :: _('FWG_ALL'); ?></button>
<?php
	foreach ($subcategories as $subcategory) {
?>
		<button type="button" class="btn" id="fwg-subcat-<?php echo $subcategory->id; ?>"><?php echo $subcategory->name; ?></button>
<?php
	}
?>
	</div>
<?php
}

$fwcgm = new modFwCascadingGrid;
$fwcgm->display($list, $com_params, $params);

class modFwCascadingGrid {
	function display($list, $com_params, $params) {
		$this->params = $com_params;
		$this->mod_params = $params;
?>
	<div id="fwg-grid-wrapper">
	<div class="container gallery">
	<div class="row-fluid">
				<div class="span12">
<?php
		if ($list) {
			$this->counter = 0;
			foreach ($list as $row) {
				$this->row = $row;
?>
		<div class="fwg-grid-item<?php if ($this->counter%8==0) { ?> w2<?php } ?>">
<?php
				include(JPATH_SITE.'/components/com_fwgallery/views/grid/tmpl/default_item.php');
?>
		</div>
<?php
				$this->counter++;
			}
		}
?>
		<div id="fwg-grid-load-more" style="display:none;"><a href="javascript:fwg_grid_loading();"><?php echo JText :: _('FWG_LOAD_MORE'); ?></a></div>
	</div>
	</div>
	</div>
	</div>
<?php
	}
}
?>

<script type="text/javascript">
var fwg_cache = {
	id: <?php echo (int)$category_id; ?>,
	module_id: <?php echo (int)$module->id; ?>,
	items_qty: <?php echo (int)count($list) + 1; ?>,
	request_in_progress: false,
	items_total: <?php echo (int)$total; ?>
};
var msnry;
var crbx;
window.addEvent('domready', function() {
	var container = document.getElement('#fwg-grid-wrapper');
	if (fwg_cache.items_qty < fwg_cache.items_total) {
		document.getElement('#fwg-grid-load-more').setStyle('display', '');
	}
	document.getElements('#fwg-subcategories button').each(function(el) {
		el.addEvent('click', function() {
			if (!fwg_cache.request_in_progress) {
				var id = this.id.toString().replace('fwg-subcat-', '');
				container.innerHTML = '<div id="fwg-grid-load-more" style="display:none;"><a href="javascript:fwg_grid_loading();"><?php echo JText :: _('FWG_LOAD_MORE', true); ?></a></div>';
				fwg_cache.items_qty = 0;
				fwg_cache.items_total = 10;
				fwg_cache.id = id;
				fwg_grid_loading(true);
			} else {
				alert('<?php echo JText :: _('FWG_CANT_CONTINUE_LOADING_NOW', true); ?>');
			} 
		});
	});
	crbx = new CeraBox(document.getElements('a.fwg-lightbox'), {
		titleFormat: 'Image {number} / {total} {title}',
		width: '<?php echo $lightbox_size; ?>%',
		constrainProportions: true
	});
	document.getElements('#fwg-grid-wrapper .fwg-image').each(function(el) {
		el.removeEvents('mouseover').addEvent('mouseover', function() {
			var panel = this.getElement('.fwg-panel');
			panel.tween('top', 0);
		}).removeEvents('mouseout').addEvent('mouseout', function() {
			var panel = this.getElement('.fwg-panel');
			panel.tween('top', panel.getSize().y);
		}).fireEvent('mouseout');
	});
<?php
if ($com_params->get('voting_type')) {
?>
	document.getElements('.fwg-thumb-vote').each(function(el) {
		el.removeEvents('click').addEvent('click', function() {
<?php
	if (!$com_params->get('public_voting') and !$this->user->id) {
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
			star.removeEvents('click').addEvent('click', function() {
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
	msnry = new Masonry(container, {
		columnWidth: 170,
		itemSelector: '.fwg-grid-item'
	});
});

function fwg_grid_loading(fire_scroll) {
	fwg_cache.request_in_progress = true;
	var wrapper = document.getElement('#fwg-grid-wrapper');
	wrapper.getElement('#fwg-grid-load-more').setStyle('display', 'none');

	var img = new Element('img', {
		'src': '<?php echo JURI :: root(true); ?>/components/com_fwgallery/assets/images/loading.gif',
		'id': 'fwg-grid-wait'
	});
	img.inject(wrapper);

	new Request.JSON({
		url: '<?php echo JRoute :: _('index.php?option=com_fwgallery&view=grid'); ?>',
		data: {
			'id': fwg_cache.id,
			'module_id': fwg_cache.module_id,
			'format': 'json',
			'load_subgalleries': <?php echo $load_subgalleries; ?>,
			'limitstart': fwg_cache.items_qty
		},
		onSuccess: function(data) {
			img.dispose();
			var wrapper = document.getElement('#fwg-grid-wrapper');
			fwg_cache.items_total = data.items_total;
			if (data.images.length > 0) {
				for (var i = 0; i < data.images.length; i++) {
					var div = new Element('div', {
						'class': 'fwg-grid-item'+((i%8==0)?' w2':'')
					});
					div.inject(wrapper);
					div.innerHTML = data.images[i];
					
					var buff = new Array(div);
					msnry.addItems(buff);
					msnry.layout();
					fwg_cache.items_qty++;
				}
			} else if (data.items_total == 0) {
				wrapper.innerHTML = '<?php echo JText :: _('FWG_NO_IMAGES_IN_THIS_GALLERY_', true); ?>';
			}
			fwg_cache.request_in_progress = false;

			document.getElement('#fwg-grid-load-more').setStyle('display', (fwg_cache.items_qty < fwg_cache.items_total)?'':'none');

			document.getElements('a.fwg-lightbox').each(function(link) {
				link.removeEvents('click');
			});

			crbx = new CeraBox(document.getElements('a.fwg-lightbox'), {
				titleFormat: 'Image {number} / {total} {title}',
				width: '<?php echo $lightbox_size; ?>%',
				constrainProportions: true
			});

			document.getElements('#fwg-grid-wrapper .fwg-image').each(function(el) {
				el.removeEvents('mouseover').addEvent('mouseover', function() {
					var panel = this.getElement('.fwg-panel');
					if (panel) panel.tween('top', 0);
				}).removeEvents('mouseout').addEvent('mouseout', function() {
					var panel = this.getElement('.fwg-panel');
					if (panel) panel.tween('top', panel.getSize().y);
				}).fireEvent('mouseout');
			});

<?php
if ($com_params->get('voting_type')) {
?>
			document.getElements('.fwg-thumb-vote').each(function(el) {
				el.removeEvents('click').addEvent('click', function() {
<?php
	if (!$com_params->get('public_voting') and !$this->user->id) {
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
					star.removeEvents('click').addEvent('click', function() {
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

			if (fire_scroll) window.fireEvent('scroll');
		}
	}).send();
}
</script>
