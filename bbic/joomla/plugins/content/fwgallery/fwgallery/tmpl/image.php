<?php
/**
 * FW Gallery content plugin 3.0
 * @copyright (C) 2014 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

$color_displayed = false;
$this->image_height = (int)$this->params->get('image_box_height', 220);
?>
<div class="fwgcp-content-plugin" style="margin:0;padding-bottom:0;border:none;float:<?php if ($this->params->get('align') == 'right') { ?>right<?php } else { ?>left<?php } ?>;">
	<div class="fwgcp-item" style="margin: 10px;">
		<div id="fwgallery-image-<?php echo $this->row->id; ?>">
			<div class="fwgcp-image"<?php if ($this->image_height) { ?> style="height:<?php echo $this->image_height; ?>px;"<?php } ?>>
				<a href="<?php echo $link; ?>"><img src="<?php echo JURI::base(false).JFHelper::getFileFilename($this->row, 'mid'); ?>" alt="<?php echo JFHelper :: escape($this->row->name); ?>" <?php echo JFHelper :: getCenteringStyle($this->row, 'mid', 0, $this->image_height); ?>/></a>
<?php
if (!$this->params->get('hide_mignifier')) {
?>
		    	<div class="fwgcp-zoom">
		    		<a class="fwg-lightbox" href="<?php echo JURI::base(false).JFHelper::getFileFilename($this->row); ?>" rel="fwg-lightbox-gallery" class="effectable">
		    			<img src="<?php echo JURI :: root(true); ?>/components/com_fwgallery/assets/images/zoom.png" />
		    		</a>
		    	</div>
<?php
}
?>
		    </div>
<?php
if ($this->params->get('display_name_image')) {
?>
		    <div class="fwgcp-name" style="background-color:#<?php echo JFHelper :: getGalleryColor($this->row->project_id); ?>;">
				<a href="<?php echo $link; ?>">
		        	<?php echo ($name) ? $name : JText::_('View image'); ?>
				</a>
		    </div>
<?php
	$color_displayed = true;
}
if ($this->params->get('display_date_image') and $date = JFHelper::encodeDate($this->row->created)) {
?>
		    <div class="fwgcp-date"<?php if (!$color_displayed) { ?> style="background-color:#<?php echo JFHelper :: getGalleryColor($this->row->project_id); ?>;"<?php $color_displayed = true; } ?>>
		    	<?php echo $date; ?>
		    </div>
<?php
}
if ($this->params->get('display_owner_image') and $this->row->_user_name) {
?>
		    <div class="fwgcp-author"<?php if (!$color_displayed) { ?> style="background-color:#<?php echo JFHelper :: getGalleryColor($this->row->project_id); ?>;"<?php $color_displayed = true; } ?>>
		        <?php echo JText::_('by')." ".$this->row->_user_name; ?>
		    </div>
<?php
}
if ($this->params->get('use_voting')) {
?>
			<div class="fwgcp-vote" id="rating<?php echo $this->row->id ?>"<?php if (!$color_displayed) { ?> style="background-color:#<?php echo JFHelper :: getGalleryColor($this->row->project_id); ?>;"<?php $color_displayed = true; } ?>>
			<ul class="fwg-star-rating<?php if (!$component_params->get('public_voting') and !$this->user->id) { ?> fwg-star-rating-not-logged<?php } ?>">
				<li class="current-rating" style="width:<?php echo $this->row->_rating_count?(number_format(intval($this->row->_rating_sum) / intval( $this->row->_rating_count ),2)*20):'0'; ?>%;"></li>
<?php
			if (!$this->row->_is_voted and (!$this->row->user_id or ($this->row->user_id and $this->user->id != $this->row->user_id))) {
?>
				<li><a href="javascript:" rel="<?php echo $this->row->id ?>_1" title="1 <?php echo JText :: _('Star of'); ?> 5" class="fwgallery-stars one-star">1</a></li>
				<li><a href="javascript:" rel="<?php echo $this->row->id ?>_2" title="2 <?php echo JText :: _('Stars of'); ?> 5" class="fwgallery-stars two-stars">2</a></li>
				<li><a href="javascript:" rel="<?php echo $this->row->id ?>_3" title="3 <?php echo JText :: _('Stars of'); ?> 5" class="fwgallery-stars three-stars">3</a></li>
				<li><a href="javascript:" rel="<?php echo $this->row->id ?>_4" title="4 <?php echo JText :: _('Stars of'); ?> 5" class="fwgallery-stars four-stars">4</a></li>
				<li><a href="javascript:" rel="<?php echo $this->row->id ?>_5" title="5 <?php echo JText :: _('Stars of'); ?> 5" class="fwgallery-stars five-stars">5</a></li>
<?php
			}
?>
			</ul>
			<div class="fwg-vote-box">
				(<?php echo (int)$this->row->_rating_count; ?>) <?php echo JText :: _($this->row->_rating_count!=1?'Votes':'Vote'); ?>
			</div>
	        <div class="clr"></div>
	    	</div>
<?php
}
if (!empty($this->new_days)) {
	$date_diff = floor((time() - strtotime($this->row->created))/86400);
	if ($date_diff <= $this->new_days) {
?>
    		<div class="fwgcp-new"></div>
<?php
	}
}
?>
		</div>
	</div>
</div>
