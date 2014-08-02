<?php
/**
 * FW Gallery 3.1.0
 * @copyright (C) 2014 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

$row = $this->row;
$color_displayed = false;
$link = $this->params->get('hide_single_image_view')?(JURI::root(true).'/'.JFHelper::getFileFilename($row, '')):JRoute::_('index.php?option=com_fwgallery&view=image&id='.$row->id.':'.JFilterOutput :: stringURLSafe($row->name).'&Itemid='.JFHelper :: getItemid('image', $row->id, JRequest :: getInt('Itemid')).'#fwgallerytop');
$image = JFHelper::getFileFilename($row, 'mid');
$sizes = array();

if (file_exists(JPATH_SITE.'/'.$image)) {
	$sizes = getimagesize(JPATH_SITE.'/'.$image);
}
$display_name = $this->mod_params->get('display_name');
$display_date = $this->mod_params->get('display_date');
$display_author = $this->mod_params->get('display_author') and $row->_user_name;
$display_voting = $this->mod_params->get('display_voting');
$display_tags = $this->mod_params->get('display_tags') and $row->_tags;
?>
			<div class="fwg-image">
				<img src="<?php echo JRoute :: _('index.php?option=com_fwgallery&view=image&layout=image&format=raw&id='.$row->id.'&w='.$this->width.'&h='.$this->height); ?>" alt="<?php echo JFHelper :: escape($row->name); ?>" />
<?php 
if (!$this->params->get('hide_mignifier')) { 
?>
				<div class="fwg-zoom">
					<a class="fwg-lightbox" href="<?php echo JURI::base(false).JFHelper::getFileFilename($row); ?>" rel="fwg-lightbox-gallery" title="<?php echo htmlspecialchars($row->name); ?>">
						<img src="<?php echo JURI :: root(true); ?>/components/com_fwgallery/assets/images/zoom.png" />
					</a>
				</div>
<?php
}

if ($display_name or $display_date or $display_author or $display_voting or $display_tags) {
?>
				<div class="fwg-panel">
<?php
	if ($display_name) {
?>
					<div class="fwg-name">
						<?php echo $row->name; ?>
					</div>
<?php
	}
	if ($display_date and $date = JFHelper::encodeDate($row->created)) {
?>
					<div class="fwg-date">
						<?php echo $date; ?>
					</div>
<?php
	}
	if ($display_author) {
?>
					<div class="fwg-author">
						<?php echo JText::_('FWG_BY')." ".$row->_user_name; ?>
					</div>
<?php
	}
	if ($display_voting) {
?>
					<div class="fwg-vote" id="rating<?php echo $row->id ?>">
<?php
		include(dirname(__FILE__).'/../../gallery/tmpl/default_vote.php');
?>
					</div>
<?php
	}
	if (!empty($this->new_days)) {
		$date_diff = floor((time() - strtotime($row->created))/86400);
		if ($date_diff <= $this->new_days) {
?>
					<div class="fwg-new"></div>
<?php
		}
	}
	if ($display_tags) {
?>
					<div class="fwg-image-tags">
<?php
		foreach ($row->_tags as $i=>$tag) {
			if ($i) { ?>, <?php }
?>
						<a href="<?php echo JRoute :: _('index.php?option=com_fwgallery&view=tag&id='.urlencode($tag)); ?>"><?php echo $tag; ?></a>
<?php
		}
?>
					</div>
<?php
	}
?>
				</div>
<?php
}
?>
			</div>