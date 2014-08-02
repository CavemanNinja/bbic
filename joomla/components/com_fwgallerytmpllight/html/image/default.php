<?php
/**
 * FW Gallery x.x.x
 * @copyright (C) 2012 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined('_JEXEC') or die('Restricted access');
JHTML :: _('behavior.framework', true);

$next_link = $this->next_image?(JRoute :: _('index.php?option=com_fwgallery&view=image&id='.$this->next_image->id.':'.JFilterOutput :: stringURLSafe($this->next_image->name).'&Itemid='.JFHelper :: getItemid('gallery', $this->row->project_id, JRequest :: getInt('Itemid')).'#fwgallerytop')):'';
?>
<div id="fwgallery">
	<a name="fwgallerytop"></a>
<?php
if (!$this->params->get('hide_iphone_app_promo') and JFHelper :: detectIphone()) {
?>
	<div class="fwg-iphone-promo"><img src="<?php echo JURI :: root(true); ?>/components/com_fwgallery/assets/images/iPhoneAppStore_transp_mini.png" /></div>
<?php
}
if ($this->params->get('display_name_image')) {
?>
	<div class="fwgi-name"><?php echo $this->row->name; ?></div>
<?php
}
?>
    <div class="fwgi-header">
        <div class="fwgi-header-total row-fluid">
<?php
if ($this->params->get('display_date_image') and $date = JFHelper::encodeDate($this->row->created)) {
?>
            <div class="fwgi-stats-date span3">
				<span><?php echo $date; ?></span>
			</div>
<?php
}
if ($this->params->get('display_image_views')) {
?>
			<div class="fwgi-stats-views span2">
				<span><?php echo JText :: _('FWG_VIEWS') ?>: <?php echo $this->row->hits; ?></span>
			</div>
<?php
}
if ($this->params->get('use_voting')) {
?>
			<div class="fwgi-stats-vote span4" id="rating<?php echo $this->row->id; ?>">
				<?php include(dirname(__FILE__).'/../gallery/default_vote.php'); ?>
			</div>
<?php
}
if ($this->params->get('allow_image_download') or $this->params->get('allow_print_button')) {
?>
			<div class="span3">
<?php
	if ($this->params->get('allow_image_download')) {
?>
				<div class="fwgi-stats-download">
					<a href="<?php echo JRoute :: _('index.php?option=com_fwgallery&view=image&task=download&id='.$this->row->id); ?>">
					<img src="<?php echo JURI :: root(true); ?>/components/com_fwgallery/assets/images/download.png" /></a>
				</div>
<?php
	}
	if ($this->params->get('allow_print_button')) {
?>
				<div class="fwgi-stats-print">
					<a target="_blank" href="<?php echo JRoute :: _('index.php?option=com_fwgallery&tmpl=component&view=image&layout=print&id='.$this->row->id); ?>">
					<img src="<?php echo JURI :: root(true); ?>/components/com_fwgallery/assets/images/print.png" /></a>
				</div>
<?php
	}
?>
			</div>
<?php
}
?>
        </div>
        <div class="fwgi-header-return">
			<a href="<?php echo JRoute::_('index.php?option=com_fwgallery&view=gallery&id='.$this->row->project_id.':'.JFilterOutput :: stringURLSafe($this->row->_gallery_name).'&Itemid='.JFHelper :: getItemid('gallery', $this->row->project_id, JRequest :: getInt('Itemid')).'#fwgallerytop'); ?>">
				<?php echo JText :: _('FWG_RETURN_TO_THE_GALLERY'); ?>
			</a>
        </div>
        <div class="clr"></div>
    </div>

	<div class="fwgi-image">
		<div class="fwgi-image-picture fwgi-image-<?php echo $this->row->_plugin_name; ?>">
<?php
if ($this->row->type_id == JFHelper :: getTypeId('video')) {
	$media_is_file = (in_array($this->row->media, array('flv', 'mov', 'mp4', 'divx', 'avi')))?true:false;
	switch ($this->row->media) {
		case 'avi' :
		case 'divx' :
?>
<div class="fwgallery-video-wrapper">
	<object type="video/divx" width="500" height="400" data="<?php echo JURI::root(false).'images/com_fwgallery/files/'.$this->row->_user_id.'/'.$this->row->media_code; ?>">
		<param name="type" value="video/divx" />
		<param name="src" value="<?php echo JURI::root(false).'images/com_fwgallery/files/'.$this->row->_user_id.'/'.$this->row->media_code; ?>" />
		<param name="data" value="<?php echo JURI::root(false).'images/com_fwgallery/files/'.$this->row->_user_id.'/'.$this->row->media_code; ?>" />
		<param name="codebase" value="<?php echo JURI::root(false).'images/com_fwgallery/files/'.$this->row->_user_id.'/'.$this->row->media_code; ?>" />
		<param name="url" value="<?php echo JURI::root(false).'images/com_fwgallery/files/'.$this->row->_user_id.'/'.$this->row->media_code; ?>" />
		<param name="mode" value="full" />
		<param name="pluginspage" value="http://go.divx.com/plugin/download/" />
		<param name="allowContextMenu" value="true" />
<?php
		if (!empty($this->row->filename)) {
?>
		<param name="previewImage" value="<?php echo JURI::root(false).''.JFHelper::getFileFilename($file); ?>" />
<?php
}
?>
		<param name="autoPlay" value="false" />
		<param name="minVersion" value="1.0.0" />
		<param name="custommode" value="none" />
		<p>No video? Get the DivX browser plug-in for <a href="http://download.divx.com/player/DivXWebPlayerInstaller.exe">Windows</a> or <a href="http://download.divx.com/player/DivXWebPlayer.dmg">Mac</a></p>
	</object>
</div>
<?php
		break;
		case 'youtube' :
			JHTML :: script('components/com_fwgallery/assets/js/swfobject.js');
			$id = 'video-yt-'.rand();
?>
<div class="fwgallery-video-wrapper">
	<div id="<?php echo $id; ?>"></div>
	<script type="text/javascript">
	var params = { allowScriptAccess: "always", allowFullScreen: "true" };
	var atts = { id: "<?php echo $id; ?>" };
	swfobject.embedSWF(
		"http://www.youtube.com/v/<?php echo $this->row->media_code; ?>?enablejsapi=1&playerapiid=ytplayer&showsearch=0&fs=1",
	    "<?php echo $id; ?>",
	    "425",
	    "340",
	    "8",
	    null,
	    null,
	    params,
	    atts
	);
	</script>
</div>
<?php
		break;
		case 'vimeo' :
?>
<div class="fwgallery-video-wrapper">
	<iframe src="http://player.vimeo.com/video/<?php echo $this->row->media_code; ?>" width="425" height="340" frameborder="0"></iframe>
</div>
<?php
		break;
		case 'blip.tv' :
			$id = 'video-blip-'.rand();
?>
	<div class="fwgallery-video-wrapper" id="<?php echo $id; ?>">
		<iframe src="<?php echo $this->row->media_link; ?>" style="width:425px;height: 340px;border:none;" allowfullscreen="allowfullscreen"></iframe>
	</div>
<?php
		break;
		case 'mp4' :
		case 'mov' :
		case 'flv' :
			JHTML :: script('components/com_fwgallery/assets/js/flowplayer-3.2.13.min.js');
			JHTML :: script('components/com_fwgallery/assets/js/flowplayer.ipad-3.2.13.min.js');
			$id = 'video-flv-'.rand();
?>
	<div id="<?php echo $id; ?>" class="fwgallery-video-wrapper"></div>
	<script type="text/javascript">
	flowplayer("<?php echo $id; ?>", "<?php echo JURI :: root(true); ?>/components/com_fwgallery/assets/swf/flowplayer-3.2.18.swf", {
		playlist: [
<?php
				if (!empty($this->row->filename)) {
?>
			'<?php echo JURI::root(true).'/'.JFHelper::getFileFilename($file); ?>',
<?php
				}
?>
			{
				url: "<?php echo JURI::root(true).'/images/com_fwgallery/files/'.$this->row->_user_id.'/'.$this->row->media_code; ?>",
				autoPlay: false,
			}
		],
		clip: {
			autoBuffering: false
		}
	}).ipad();
	</script>
<?php
		break;
	}
} else {
	if ($next_link) {
?>
			<a href="<?php echo $next_link; ?>" title="<?php echo JText :: _('FWG_NEXT'); ?>">
<?php
	}
?>
	        <img src="<?php echo JURI::root(true).'/'.JFHelper::getFileFilename($this->row); ?>" alt="<?php echo JFHelper :: escape($this->row->name); ?>" />
<?php
	if ($next_link) {
?>
			</a>
<?php
	}
}
if ($new_days = (int)$this->params->get('new_days')) {
	$date_diff = floor((mktime() - strtotime($this->row->created))/86400);
	if ($date_diff <= $new_days) {
?>
			    <div class="fwgi-image-new"></div>
<?php
	}
}
?>
		</div>

<?php
if ($this->params->get('display_social_sharing')) {
	$uri = JURI :: getInstance();
?>
		<div class="fwg-social-sharing row-fluid">
			<div class="fwgi-stats-facebook">
				<a target="_blank" href="http://www.facebook.com/sharer.php?u=<?php echo urlencode($uri->toString()); ?>&t=<?php echo urldecode($this->row->name); ?>"><img src="<?php echo JURI :: root(true); ?>/components/com_fwgallery/assets/images/icon_facebook.png" /></a>
			</div>
			<div class="fwgi-stats-twitter">
			    <a target="_blank" href="https://twitter.com/share?url=<?php echo urlencode($uri->toString()); ?>&text=<?php echo urldecode($this->row->name); ?>"><img src="<?php echo JURI :: root(true); ?>/components/com_fwgallery/assets/images/icon_twitter.png" /></a>
			</div>
			<div class="fwgi-stats-pinterest">
			    <a target="_blank" href="http://pinterest.com/pin/create/button/?url=<?php echo urlencode($uri->toString()); ?>&media=<?php echo urlencode(JURI::root(false).JFHelper::getFileFilename($this->row)); ?>&description=<?php echo urldecode($this->row->name); ?>"><img src="<?php echo JURI :: root(true); ?>/components/com_fwgallery/assets/images/icon_pint.png" /></a>
			</div>
			<div class="clr"></div>
		</div>
<?php
}
if ($this->params->get('display_image_tags') and $this->row->_tags) {
?>
		<div class="fwg-image-tags row-fluid">
<?php
	foreach ($this->row->_tags as $i=>$tag) {
		if ($i) { ?>, <?php }
?>
			<a href="<?php echo JRoute :: _('index.php?option=com_fwgallery&view=tag&id='.urlencode($tag)); ?>"><?php echo $tag; ?></a>
<?php
	}
?>
		</div>
<?php
}
if ($this->params->get('hide_bottom_image')) {
	if ($this->row->_stock) echo $this->loadTemplate('stock');
	if ($this->row->copyright and $this->params->get('display_user_copyright')) {
?>
		<div class="fwgi-image-copyright"><?php echo $this->row->copyright; ?></div>
<?php
	}
	if ($this->row->descr) {
?>
		<?php echo JText::_('FWG_DESCRIPTION').': '.nl2br($this->row->descr); ?>
<?php
	}
	if ($this->comments_type) echo $this->loadTemplate('comments');
} else {
?>
		<div class="row-fluid">
			<div class="fwgi-image-prev span3">
<?php
	if ($this->previous_image) {
		$link = JRoute :: _('index.php?option=com_fwgallery&view=image&id='.$this->previous_image->id.':'.JFilterOutput :: stringURLSafe($this->previous_image->name).'&Itemid='.JFHelper :: getItemid('gallery', $this->row->project_id, JRequest :: getInt('Itemid')).'#fwgallerytop');
?>
			        <a href="<?php echo $link; ?>" title="<?php echo JText :: _('FWG_PREVIOUS_IMAGE'); ?>">
				        <img src="<?php echo JURI::root(true).'/'.JFHelper::getFileFilename($this->previous_image, 'th'); ?>"/>
				    </a>
<?php
	}
?>
			</div>
			<div class="fwgi-image-desc span6">
<?php
	if ($this->row->_stock) echo $this->loadTemplate('stock');
	if ($this->row->copyright and $this->params->get('display_user_copyright')) {
?>
					<div class="fwgi-image-copyright"><?php echo $this->row->copyright; ?></div>
<?php
	}
	if ($this->row->descr) {
?>
					<?php echo JText::_('FWG_DESCRIPTION').': '.nl2br($this->row->descr); ?>
<?php
	}
	if ($this->comments_type) echo $this->loadTemplate('comments');
?>
			</div>
			<div class="fwgi-image-next span3">
<?php
	if ($this->next_image) {
?>
			        <a href="<?php echo $next_link; ?>" title="<?php echo JText :: _('FWG_NEXT_IMAGE'); ?>">
				        <img src="<?php echo JURI::root(true).'/'.JFHelper::getFileFilename($this->next_image, 'th'); ?>"/>
			        </a>
<?php
	}
?>
			</div>
		</div>
<?php
}
?>
	</div>

</div>
<?php
$params = JComponentHelper :: getParams('com_fwgallery');
if (!$params->get('hide_fw_copyright')) {
?>
<div id="fwcopy" style="display:block;visibility:visible;text-align:center;font-size:10px;padding:20px 0;">
	<?php echo JText::_("FWG_POWERED_BY"); ?> <a href="http://fastw3b.net/fwgallery.html" target="_blank"><?php echo JText::_("FWG_FW_GALLERY"); ?></a>
</div>
<?php
}
?>
<script type="text/javascript">
document.addEvent('domready', function() {
	document.getElements('.fwg-star-rating-not-logged').each(function(el) {
		el.removeEvents('click');
		el.addEvent('click', function() {
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
						url: '<?php echo JRoute :: _('index.php?format=raw&option=com_fwgallery&view=gallery&task=vote', false); ?>',
						onSuccess: function(html) {
							var el = document.getElement('#rating'+ids[1]);
							if (el) el.innerHTML = html;
						}
					}).send('id='+ids[1]+'&value='+ids[2]);
				}
			});
		});
	});
});
</script>