<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$cparams = JComponentHelper::getParams('com_media');

jimport('joomla.html.html.bootstrap');
// var_dump($this);
?>

<?php if ($this->params->get("page_title") == "Contact Us") : ?>
	<div class="contact<?php echo $this->pageclass_sfx?>" itemscope itemtype="http://schema.org/Person">
		<?php if ($this->params->get('show_page_heading')) : ?>
			<h1>
				<?php echo $this->escape($this->params->get('page_heading')); ?>
			</h1>
		<?php endif; ?>
		<?php if ($this->contact->name && $this->params->get('show_name')) : ?>
			<div class="page-header">
				<h1>
					<?php if ($this->item->published == 0) : ?>
						<span class="label label-warning"><?php echo JText::_('JUNPUBLISHED'); ?></span>
					<?php endif; ?>
					<span class="contact-name" itemprop="name"><?php echo $this->contact->name; ?></span>
				</h1><br/>
				<p> If you have any inquires or would like to further your application process, please don’t hesitate to contact us in the given methods below.</p>
			</div>
		<?php endif;  ?>
		<?php if ($this->params->get('show_contact_category') == 'show_no_link') : ?>
			<h3>
				<span class="contact-category"><?php echo $this->contact->category_title; ?></span>
			</h3>
		<?php endif; ?>
		<?php if ($this->params->get('show_contact_category') == 'show_with_link') : ?>
			<?php $contactLink = ContactHelperRoute::getCategoryRoute($this->contact->catid); ?>
			<h3>
				<span class="contact-category"><a href="<?php echo $contactLink; ?>">
					<?php echo $this->escape($this->contact->category_title); ?></a>
				</span>
			</h3>
		<?php endif; ?>
		<?php if ($this->params->get('show_contact_list') && count($this->contacts) > 1) : ?>
			<form action="#" method="get" name="selectForm" id="selectForm">
				<?php echo JText::_('COM_CONTACT_SELECT_CONTACT'); ?>
				<?php echo JHtml::_('select.genericlist', $this->contacts, 'id', 'class="input" onchange="document.location.href = this.value"', 'link', 'name', $this->contact->link);?>
			</form>
		<?php endif; ?>

		<?php if ($this->params->get('show_tags', 1) && !empty($this->item->tags)) : ?>
			<?php $this->item->tagLayout = new JLayoutFile('joomla.content.tags'); ?>
			<?php echo $this->item->tagLayout->render($this->item->tags->itemTags); ?>
		<?php endif; ?>
		
		<?php if ($this->params->get('presentation_style') == 'sliders') : ?>
			<div class="panel-group" id="slide-contact">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a class="accordion-toggle" data-toggle="collapse" data-parent="#slide-contact" href="#basic-details">
							<?php echo JText::_('COM_CONTACT_DETAILS');?>
							</a>
						</h4>
					</div>
					<div id="basic-details" class="panel-collapse collapse in">
						<div class="panel-body">
		<?php endif; ?>
		<?php if ($this->params->get('presentation_style') == 'tabs'):?>
			<ul class="nav nav-tabs" id="myTab">
					<li class="active"><a data-toggle="tab" href="#basic-details"><?php echo JText::_('COM_CONTACT_DETAILS'); ?></a></li>
					<?php if ($this->params->get('show_email_form') && ($this->contact->email_to || $this->contact->user_id)) : ?><li><a data-toggle="tab" href="#display-form"><?php echo JText::_('COM_CONTACT_EMAIL_FORM'); ?></a></li><?php endif; ?>
					<?php if ($this->params->get('show_links')) : ?><li><a data-toggle="tab" href="#display-links"><?php echo JText::_('COM_CONTACT_LINKS'); ?></a></li><?php endif; ?>
					<?php if ($this->params->get('show_articles') && $this->contact->user_id && $this->contact->articles) : ?><li><a data-toggle="tab" href="#display-articles"><?php echo JText::_('JGLOBAL_ARTICLES'); ?></a></li><?php endif; ?>
					<?php if ($this->params->get('show_profile') && $this->contact->user_id && JPluginHelper::isEnabled('user', 'profile')) : ?><li><a data-toggle="tab" href="#display-profile"><?php echo JText::_('COM_CONTACT_PROFILE'); ?></a></li><?php endif; ?>
					<?php if ($this->contact->misc && $this->params->get('show_misc')) : ?><li><a data-toggle="tab" href="#display-misc"><?php echo JText::_('COM_CONTACT_OTHER_INFORMATION'); ?></a></li><?php endif; ?>
			</ul>
			<div class="tab-content" id="myTabContent">
				<div id="basic-details" class="tab-pane active">
		<?php endif; ?>
		<?php if ($this->params->get('presentation_style') == 'plain') : ?>
			<?php  echo '<h3>'. JText::_('COM_CONTACT_DETAILS').'</h3>';  ?>
		<?php endif; ?>
		
		<?php if ($this->contact->image && $this->params->get('show_image')) : ?>
			<div class="thumbnail pull-right">
				<?php echo JHtml::_('image', $this->contact->image, JText::_('COM_CONTACT_IMAGE_DETAILS'), array('align' => 'middle', 'itemprop' => 'image')); ?>
			</div>
		<?php endif; ?>

		<?php if ($this->contact->con_position && $this->params->get('show_position')) : ?>
			<dl class="contact-position dl-horizontal">
				<dd itemprop="jobTitle">
					<?php echo $this->contact->con_position; ?>
				</dd>
			</dl>
		<?php endif; ?>

		<?php echo $this->loadTemplate('address'); ?>

		<?php if ($this->params->get('allow_vcard')) :	?>
			<?php echo JText::_('COM_CONTACT_DOWNLOAD_INFORMATION_AS');?>
				<a href="<?php echo JRoute::_('index.php?option=com_contact&amp;view=contact&amp;id='.$this->contact->id . '&amp;format=vcf'); ?>">
				<?php echo JText::_('COM_CONTACT_VCARD');?></a>
		<?php endif; ?>
		<?php if ($this->params->get('presentation_style')=='sliders'):?>
						</div>
					</div>
				</div>
		<?php endif; ?>
		<?php if ($this->params->get('presentation_style') == 'tabs') : ?>
				</div>
		<?php endif; ?>
		<?php if ($this->params->get('show_email_form') && ($this->contact->email_to || $this->contact->user_id)) : ?>

			<?php if ($this->params->get('presentation_style')=='sliders'):?>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a class="accordion-toggle" data-toggle="collapse" data-parent="#slide-contact" href="#display-form">
							<?php echo JText::_('COM_CONTACT_EMAIL_FORM');?>
							</a>
						</h4>
					</div>
					<div id="display-form" class="panel-collapse collapse">
						<div class="panel-body">
			<?php endif; ?>
			<?php if ($this->params->get('presentation_style') == 'tabs') : ?>
				<div id="display-form" class="tab-pane">
			<?php endif; ?>
			<?php if ($this->params->get('presentation_style')=='plain'):?>
				<?php  echo '<h3>'. JText::_('COM_CONTACT_EMAIL_FORM').'</h3>';  ?>
			<?php endif; ?>
			<?php  echo $this->loadTemplate('form');  ?>
			<?php if ($this->params->get('presentation_style')=='sliders'):?>
						</div>
					</div>
				</div>
			<?php endif; ?>
			<?php if ($this->params->get('presentation_style') == 'tabs') : ?>
				</div>
			<?php endif; ?>
		<?php endif; ?>
		
		<?php if ($this->params->get('show_links')) : ?>
			<?php echo $this->loadTemplate('links'); ?>
		<?php endif; ?>
			
		<?php if ($this->params->get('show_articles') && $this->contact->user_id && $this->contact->articles) : ?>
			<?php if ($this->params->get('presentation_style')=='sliders'):?>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a class="accordion-toggle" data-toggle="collapse" data-parent="#slide-contact" href="#display-articles">
							<?php echo JText::_('JGLOBAL_ARTICLES');?>
							</a>
						</h4>
					</div>
					<div id="display-articles" class="panel-collapse collapse">
						<div class="panel-body">
			<?php endif; ?>
			<?php if ($this->params->get('presentation_style') == 'tabs') : ?>
				<div id="display-articles" class="tab-pane">
			<?php endif; ?>
			<?php if  ($this->params->get('presentation_style')=='plain'):?>
				<?php echo '<h3>'. JText::_('JGLOBAL_ARTICLES').'</h3>'; ?>
			<?php endif; ?>
				<?php echo $this->loadTemplate('articles'); ?>
			<?php if ($this->params->get('presentation_style')=='sliders'):?>
						</div>
					</div>
				</div>
			<?php endif; ?>
			<?php if ($this->params->get('presentation_style') == 'tabs') : ?>
				</div>
			<?php endif; ?>
		<?php endif; ?>
		<?php if ($this->params->get('show_profile') && $this->contact->user_id && JPluginHelper::isEnabled('user', 'profile')) : ?>
			<?php if ($this->params->get('presentation_style')=='sliders'):?>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a class="accordion-toggle" data-toggle="collapse" data-parent="#slide-contact" href="#display-profile">
							<?php echo JText::_('COM_CONTACT_PROFILE');?>
							</a>
						</h4>
					</div>
					<div id="display-profile" class="panel-collapse collapse">
						<div class="panel-body">
			<?php endif; ?>
			<?php if ($this->params->get('presentation_style') == 'tabs') : ?>
				<div id="display-profile" class="tab-pane">
			<?php endif; ?>
			<?php if ($this->params->get('presentation_style')=='plain'):?>
				<?php echo '<h3>'. JText::_('COM_CONTACT_PROFILE').'</h3>'; ?>
			<?php endif; ?>
			<?php echo $this->loadTemplate('profile'); ?>
			<?php if ($this->params->get('presentation_style')=='sliders'):?>
						</div>
					</div>
				</div>
			<?php endif; ?>
			<?php if ($this->params->get('presentation_style') == 'tabs') : ?>
				</div>
			<?php endif; ?>
		<?php endif; ?>
		<?php if ($this->contact->misc && $this->params->get('show_misc')) : ?>
			<?php if ($this->params->get('presentation_style')=='sliders'):?>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a class="accordion-toggle" data-toggle="collapse" data-parent="#slide-contact" href="#display-misc">
							<?php echo JText::_('COM_CONTACT_OTHER_INFORMATION');?>
							</a>
						</h4>
					</div>
					<div id="display-misc" class="panel-collapse collapse">
						<div class="panel-body">
			<?php endif; ?>
			<?php if ($this->params->get('presentation_style') == 'tabs') : ?>
				<div id="display-misc" class="tab-pane">
			<?php endif; ?>
			<?php if ($this->params->get('presentation_style')=='plain'):?>
				<?php echo '<h3>'. JText::_('COM_CONTACT_OTHER_INFORMATION').'</h3>'; ?>
			<?php endif; ?>
					<div class="contact-miscinfo">
						<dl class="dl-horizontal">
							<dt>
								<span class="<?php echo $this->params->get('marker_class'); ?>">
									<?php echo $this->params->get('marker_misc'); ?>
								</span>
							</dt>
							<dd>
								<span class="contact-misc">
									<?php echo $this->contact->misc; ?>
								</span>
							</dd>
						</dl>
					</div>
			<?php if ($this->params->get('presentation_style')=='sliders'):?>
						</div>
					</div>
				</div>
			<?php endif; ?>
			<?php if ($this->params->get('presentation_style') == 'tabs') : ?>
				</div>
			<?php endif; ?>
		<?php endif; ?>
		<?php if ($this->params->get('presentation_style')=='sliders'):?>
				<script type="text/javascript">
					(function($){
						$('#slide-contact').collapse({ parent: false, toggle: true, active: 'basic-details'});
					})(jQuery);
				</script>
			</div>
		<?php endif; ?>
		<?php if ($this->params->get('presentation_style') == 'tabs') : ?>
			</div>
		<?php endif; ?>
	</div>

<?php elseif ($this->params->get("page_title") == "تواصل معنا") : ?>
	<div class="contact<?php echo $this->pageclass_sfx?>" itemscope itemtype="http://schema.org/Person">
		<?php if ($this->params->get('show_page_heading')) : ?>
			<h1>
				<?php echo $this->escape($this->params->get('page_heading')); ?>
			</h1>
		<?php endif; ?>
		<?php if ($this->contact->name && $this->params->get('show_name')) : ?>
			<div class="page-header">
				<h1>
					<?php if ($this->item->published == 0) : ?>
						<span class="label label-warning"><?php echo JText::_('JUNPUBLISHED'); ?></span>
					<?php endif; ?>
					<span class="contact-name" itemprop="name"><?php echo $this->contact->name; ?></span>
				</h1><br/>
				<p> اذا كانت لديك أي استفسارات أو تود أن تتم عملية تقديم طلبك، نرجوا منكم التواصل معنا بالطرق التالية</p>
			</div>
		<?php endif;  ?>
		<?php if ($this->params->get('show_contact_category') == 'show_no_link') : ?>
			<h3>
				<span class="contact-category"><?php echo $this->contact->category_title; ?></span>
			</h3>
		<?php endif; ?>
		<?php if ($this->params->get('show_contact_category') == 'show_with_link') : ?>
			<?php $contactLink = ContactHelperRoute::getCategoryRoute($this->contact->catid); ?>
			<h3>
				<span class="contact-category"><a href="<?php echo $contactLink; ?>">
					<?php echo $this->escape($this->contact->category_title); ?></a>
				</span>
			</h3>
		<?php endif; ?>
		<?php if ($this->params->get('show_contact_list') && count($this->contacts) > 1) : ?>
			<form action="#" method="get" name="selectForm" id="selectForm">
				<?php echo JText::_('COM_CONTACT_SELECT_CONTACT'); ?>
				<?php echo JHtml::_('select.genericlist', $this->contacts, 'id', 'class="input" onchange="document.location.href = this.value"', 'link', 'name', $this->contact->link);?>
			</form>
		<?php endif; ?>

		<?php if ($this->params->get('show_tags', 1) && !empty($this->item->tags)) : ?>
			<?php $this->item->tagLayout = new JLayoutFile('joomla.content.tags'); ?>
			<?php echo $this->item->tagLayout->render($this->item->tags->itemTags); ?>
		<?php endif; ?>
		
		<?php if ($this->params->get('presentation_style') == 'sliders') : ?>
			<div class="panel-group" id="slide-contact">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a class="accordion-toggle" data-toggle="collapse" data-parent="#slide-contact" href="#basic-details">
							<?php echo JText::_('COM_CONTACT_DETAILS');?>
							<!-- العنوان والهاتف -->
							</a>
						</h4>
					</div>
					<div id="basic-details" class="panel-collapse collapse in">
						<div class="panel-body">
		<?php endif; ?>
		<?php if ($this->params->get('presentation_style') == 'tabs'):?>
			<ul class="nav nav-tabs" id="myTab">
					<li class="active"><a data-toggle="tab" href="#basic-details"><?php echo JText::_('COM_CONTACT_DETAILS'); ?></a></li>
					<?php if ($this->params->get('show_email_form') && ($this->contact->email_to || $this->contact->user_id)) : ?><li><a data-toggle="tab" href="#display-form"><?php echo JText::_('COM_CONTACT_EMAIL_FORM'); ?></a></li><?php endif; ?>
					<?php if ($this->params->get('show_links')) : ?><li><a data-toggle="tab" href="#display-links"><?php echo JText::_('COM_CONTACT_LINKS'); ?></a></li><?php endif; ?>
					<?php if ($this->params->get('show_articles') && $this->contact->user_id && $this->contact->articles) : ?><li><a data-toggle="tab" href="#display-articles"><?php echo JText::_('JGLOBAL_ARTICLES'); ?></a></li><?php endif; ?>
					<?php if ($this->params->get('show_profile') && $this->contact->user_id && JPluginHelper::isEnabled('user', 'profile')) : ?><li><a data-toggle="tab" href="#display-profile"><?php echo JText::_('COM_CONTACT_PROFILE'); ?></a></li><?php endif; ?>
					<?php if ($this->contact->misc && $this->params->get('show_misc')) : ?><li><a data-toggle="tab" href="#display-misc"><?php echo JText::_('COM_CONTACT_OTHER_INFORMATION'); ?></a></li><?php endif; ?>
			</ul>
			<div class="tab-content" id="myTabContent">
				<div id="basic-details" class="tab-pane active">
		<?php endif; ?>
		<?php if ($this->params->get('presentation_style') == 'plain') : ?>
			<?php  echo '<h3>'. JText::_('COM_CONTACT_DETAILS').'</h3>';  ?>
		<?php endif; ?>
		
		<?php if ($this->contact->image && $this->params->get('show_image')) : ?>
			<div class="thumbnail pull-right">
				<?php echo JHtml::_('image', $this->contact->image, JText::_('COM_CONTACT_IMAGE_DETAILS'), array('align' => 'middle', 'itemprop' => 'image')); ?>
			</div>
		<?php endif; ?>

		<?php if ($this->contact->con_position && $this->params->get('show_position')) : ?>
			<dl class="contact-position dl-horizontal">
				<dd itemprop="jobTitle">
					<?php echo $this->contact->con_position; ?>
				</dd>
			</dl>
		<?php endif; ?>

		<?php echo $this->loadTemplate('address'); ?>

		<?php if ($this->params->get('allow_vcard')) :	?>
			<?php echo JText::_('COM_CONTACT_DOWNLOAD_INFORMATION_AS');?>
				<a href="<?php echo JRoute::_('index.php?option=com_contact&amp;view=contact&amp;id='.$this->contact->id . '&amp;format=vcf'); ?>">
				<?php echo JText::_('COM_CONTACT_VCARD');?></a>
		<?php endif; ?>
		<?php if ($this->params->get('presentation_style')=='sliders'):?>
						</div>
					</div>
				</div>
		<?php endif; ?>
		<?php if ($this->params->get('presentation_style') == 'tabs') : ?>
				</div>
		<?php endif; ?>
		<?php if ($this->params->get('show_email_form') && ($this->contact->email_to || $this->contact->user_id)) : ?>

			<?php if ($this->params->get('presentation_style')=='sliders'):?>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a class="accordion-toggle" data-toggle="collapse" data-parent="#slide-contact" href="#display-form">
							<?php echo JText::_('COM_CONTACT_EMAIL_FORM');?>
							<!-- التواصل لابريد الإلكتروني -->
							</a>
						</h4>
					</div>
					<div id="display-form" class="panel-collapse collapse">
						<div class="panel-body">
			<?php endif; ?>
			<?php if ($this->params->get('presentation_style') == 'tabs') : ?>
				<div id="display-form" class="tab-pane">
			<?php endif; ?>
			<?php if ($this->params->get('presentation_style')=='plain'):?>
				<?php  echo '<h3>'. JText::_('COM_CONTACT_EMAIL_FORM').'</h3>';  ?>
			<?php endif; ?>
			<?php  echo $this->loadTemplate('form');  ?>
			<?php if ($this->params->get('presentation_style')=='sliders'):?>
						</div>
					</div>
				</div>
			<?php endif; ?>
			<?php if ($this->params->get('presentation_style') == 'tabs') : ?>
				</div>
			<?php endif; ?>
		<?php endif; ?>
		
		<?php if ($this->params->get('show_links')) : ?>
			<?php echo $this->loadTemplate('links'); ?>
		<?php endif; ?>
			
		<?php if ($this->params->get('show_articles') && $this->contact->user_id && $this->contact->articles) : ?>
			<?php if ($this->params->get('presentation_style')=='sliders'):?>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a class="accordion-toggle" data-toggle="collapse" data-parent="#slide-contact" href="#display-articles">
							<?php echo JText::_('JGLOBAL_ARTICLES');?>
							</a>
						</h4>
					</div>
					<div id="display-articles" class="panel-collapse collapse">
						<div class="panel-body">
			<?php endif; ?>
			<?php if ($this->params->get('presentation_style') == 'tabs') : ?>
				<div id="display-articles" class="tab-pane">
			<?php endif; ?>
			<?php if  ($this->params->get('presentation_style')=='plain'):?>
				<?php echo '<h3>'. JText::_('JGLOBAL_ARTICLES').'</h3>'; ?>
			<?php endif; ?>
				<?php echo $this->loadTemplate('articles'); ?>
			<?php if ($this->params->get('presentation_style')=='sliders'):?>
						</div>
					</div>
				</div>
			<?php endif; ?>
			<?php if ($this->params->get('presentation_style') == 'tabs') : ?>
				</div>
			<?php endif; ?>
		<?php endif; ?>
		<?php if ($this->params->get('show_profile') && $this->contact->user_id && JPluginHelper::isEnabled('user', 'profile')) : ?>
			<?php if ($this->params->get('presentation_style')=='sliders'):?>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a class="accordion-toggle" data-toggle="collapse" data-parent="#slide-contact" href="#display-profile">
							<?php echo JText::_('COM_CONTACT_PROFILE');?>
							</a>
						</h4>
					</div>
					<div id="display-profile" class="panel-collapse collapse">
						<div class="panel-body">
			<?php endif; ?>
			<?php if ($this->params->get('presentation_style') == 'tabs') : ?>
				<div id="display-profile" class="tab-pane">
			<?php endif; ?>
			<?php if ($this->params->get('presentation_style')=='plain'):?>
				<?php echo '<h3>'. JText::_('COM_CONTACT_PROFILE').'</h3>'; ?>
			<?php endif; ?>
			<?php echo $this->loadTemplate('profile'); ?>
			<?php if ($this->params->get('presentation_style')=='sliders'):?>
						</div>
					</div>
				</div>
			<?php endif; ?>
			<?php if ($this->params->get('presentation_style') == 'tabs') : ?>
				</div>
			<?php endif; ?>
		<?php endif; ?>
		<?php if ($this->contact->misc && $this->params->get('show_misc')) : ?>
			<?php if ($this->params->get('presentation_style')=='sliders'):?>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a class="accordion-toggle" data-toggle="collapse" data-parent="#slide-contact" href="#display-misc">
							<?php echo JText::_('COM_CONTACT_OTHER_INFORMATION');?>
							</a>
						</h4>
					</div>
					<div id="display-misc" class="panel-collapse collapse">
						<div class="panel-body">
			<?php endif; ?>
			<?php if ($this->params->get('presentation_style') == 'tabs') : ?>
				<div id="display-misc" class="tab-pane">
			<?php endif; ?>
			<?php if ($this->params->get('presentation_style')=='plain'):?>
				<?php echo '<h3>'. JText::_('COM_CONTACT_OTHER_INFORMATION').'</h3>'; ?>
			<?php endif; ?>
					<div class="contact-miscinfo">
						<dl class="dl-horizontal">
							<dt>
								<span class="<?php echo $this->params->get('marker_class'); ?>">
									<?php echo $this->params->get('marker_misc'); ?>
								</span>
							</dt>
							<dd>
								<span class="contact-misc">
									<?php echo $this->contact->misc; ?>
								</span>
							</dd>
						</dl>
					</div>
			<?php if ($this->params->get('presentation_style')=='sliders'):?>
						</div>
					</div>
				</div>
			<?php endif; ?>
			<?php if ($this->params->get('presentation_style') == 'tabs') : ?>
				</div>
			<?php endif; ?>
		<?php endif; ?>
		<?php if ($this->params->get('presentation_style')=='sliders'):?>
				<script type="text/javascript">
					(function($){
						$('#slide-contact').collapse({ parent: false, toggle: true, active: 'basic-details'});
					})(jQuery);
				</script>
			</div>
		<?php endif; ?>
		<?php if ($this->params->get('presentation_style') == 'tabs') : ?>
			</div>
		<?php endif; ?>
	</div>


<?php elseif ($this->params->get("page_title") == "Suggestions") : ?>
	<?php if ($this->contact->name && $this->params->get('show_name')) : ?>
		<div class="page-header">
			<h1>
				<?php if ($this->item->published == 0) : ?>
					<span class="label label-warning"><?php echo JText::_('JUNPUBLISHED'); ?></span>
				<?php endif; ?>
				<span class="contact-name" itemprop="name"><?php echo $this->contact->name; ?></span>
			</h1>
		</div>
	<?php endif;  ?>
	
	<?php if ($this->params->get('show_email_form') && ($this->contact->email_to || $this->contact->user_id)) : ?>

		<?php if ($this->params->get('presentation_style')=='sliders'):?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#slide-contact" href="#display-form">
						<?php echo JText::_('COM_CONTACT_EMAIL_FORM');?>
						</a>
					</h4>
				</div>
				<div id="display-form" class="panel-collapse collapse">
					<div class="panel-body">
		<?php endif; ?>
		<?php if ($this->params->get('presentation_style') == 'tabs') : ?>
			<div id="display-form" class="tab-pane">
		<?php endif; ?>
		<?php if ($this->params->get('presentation_style')=='plain'):?>
			<?php  echo '<h3>'. JText::_('COM_CONTACT_EMAIL_FORM').'</h3>';  ?>
		<?php endif; ?>
		<?php  echo $this->loadTemplate('form');  ?>
		<?php if ($this->params->get('presentation_style')=='sliders'):?>
					</div>
				</div>
			</div>
		<?php endif; ?>
		<?php if ($this->params->get('presentation_style') == 'tabs') : ?>
			</div>
		<?php endif; ?>
	<?php endif; ?>

	<div class="panel-group collapse in" id="slide-contact" style="height: auto;">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a class="accordion-toggle" data-toggle="collapse" data-parent="#slide-contact" href="#basic-details">
						Contact							</a>
					</h4>
				</div>
				<div id="basic-details" class="panel-collapse collapse in">
					<div class="panel-body">
						
						
						
						<dl class="contact-address dl-horizontal" itemprop="address" itemscope="" itemtype="http://schema.org/PostalAddress">
							<dt>
								<span class="jicons-text">
									Address: 				</span>
								</dt>
								
								<dd>
									<span class="contact-street" itemprop="streetAddress">
										Building 8, Road No. 17
										Block No.117, Hidd Industrial Area
										<br>				</span>
									</dd>
									
									<dd>
										<span class="contact-country" itemprop="addressCountry">
											Kingdom of Bahrain<br>			</span>
										</dd>
										

										<dt>
											<span class="jicons-text">
												Phone: 		</span>
											</dt>
											<dd>
												<span class="contact-telephone" itemprop="telephone">
													17358888		</span>
												</dd>
												<dt>
													<span class="jicons-text">
														Fax: 		</span>
													</dt>
													<dd>
														<span class="contact-fax" itemprop="faxNumber">
															17465484		</span>
														</dd>
													</dl>

												</div>
											</div>
										</div>
										
										<div class="panel panel-default">
											<div class="panel-heading">
												<h4 class="panel-title">
													<a class="accordion-toggle" data-toggle="collapse" data-parent="#slide-contact" href="#display-form">
														Contact Form							</a>
													</h4>
												</div>
												<div id="display-form" class="panel-collapse collapse">
													<div class="panel-body">
														
														<div class="contact-form">
															<form id="contact-form" action="/bbic/joomla/index.php/en/contact-us" method="post" class="form-validate form-horizontal">
																<fieldset>
																	<legend>Send an email. All fields with an * are required.</legend>
																	<div class="form-group">
																		<label id="jform_contact_name-lbl" for="jform_contact_name" class="hasTooltip required col-sm-2 control-label" title="" data-original-title="<strong>Name</strong><br />Your name">Name<span class="star">&nbsp;*</span></label>				<div class="col-sm-10">
																		<input type="text" name="jform[contact_name]" id="jform_contact_name" value="" size="30" required="" aria-required="true">				</div>
																	</div>
																	<div class="form-group">
																		<label id="jform_contact_email-lbl" for="jform_contact_email" class="hasTooltip required col-sm-2 control-label" title="" data-original-title="<strong>Email</strong><br />Email for contact">Email<span class="star">&nbsp;*</span></label>				<div class="col-sm-10">
																		<input type="email" name="jform[contact_email]" class="validate-email form-control" id="jform_contact_email" value="" size="30" required="" aria-required="true">				</div>
																	</div>
																	<div class="form-group">
																		<label id="jform_contact_emailmsg-lbl" for="jform_contact_emailmsg" class="hasTooltip required col-sm-2 control-label" title="" data-original-title="<strong>Subject</strong><br />Enter the subject of your message here .">Subject<span class="star">&nbsp;*</span></label>				<div class="col-sm-10">
																		<input type="text" name="jform[contact_subject]" id="jform_contact_emailmsg" value="" size="60" required="" aria-required="true">				</div>
																	</div>
																	<div class="form-group">
																		<label id="jform_contact_message-lbl" for="jform_contact_message" class="hasTooltip required col-sm-2 control-label" title="" data-original-title="<strong>Message</strong><br />Enter your message here.">Message<span class="star">&nbsp;*</span></label>				<div class="col-sm-10">
																		<textarea name="jform[contact_message]" id="jform_contact_message" cols="50" rows="10" required="" aria-required="true"></textarea>				</div>
																	</div>
																	<div class="form-group">
																		<div class="col-sm-offset-2 col-sm-10">
																			<div class="checkbox">
																				<input type="checkbox" name="jform[contact_email_copy]" id="jform_contact_email_copy" value="1">							<label id="jform_contact_email_copy-lbl" for="jform_contact_email_copy" class="hasTooltip" title="" data-original-title="<strong>Send copy to yourself</strong><br />Sends a copy of the message to the address you have supplied.">Send copy to yourself</label>						</div>
																			</div>
																		</div>
																		<div class="form-group">
																		</div>
																		<div class="form-group">
																			<div class="col-sm-offset-2 col-sm-10">
																				<button class="btn btn-primary validate" type="submit">Send Email</button>
																			</div>
																			
																			<input type="hidden" name="option" value="com_contact">
																			<input type="hidden" name="task" value="contact.submit">
																			<input type="hidden" name="return" value="">
																			<input type="hidden" name="id" value="1:enquiries">
																			<input type="hidden" name="6687849e18a250703ce669392bd4fdce" value="1">			</div>
																		</fieldset>
																	</form>
																</div>
															</div>
														</div>
													</div>
													
													
													<script type="text/javascript">
														(function($){
															$('#slide-contact').collapse({ parent: false, toggle: true, active: 'basic-details'});
														})(jQuery);
													</script>
												</div>

<?php else : ?>

	<div class="contact<?php echo $this->pageclass_sfx?>" itemscope itemtype="http://schema.org/Person">
		<?php if ($this->params->get('show_page_heading')) : ?>
			<h1>
				<?php echo $this->escape($this->params->get('page_heading')); ?>
			</h1>
		<?php endif; ?>
		<?php if ($this->contact->name && $this->params->get('show_name')) : ?>
			<div class="page-header">
				<h2>
					<?php if ($this->item->published == 0) : ?>
						<span class="label label-warning"><?php echo JText::_('JUNPUBLISHED'); ?></span>
					<?php endif; ?>
					<span class="contact-name" itemprop="name"><?php echo $this->contact->name; ?></span>
				</h2>
			</div>
		<?php endif;  ?>
		<?php if ($this->params->get('show_contact_category') == 'show_no_link') : ?>
			<h3>
				<span class="contact-category"><?php echo $this->contact->category_title; ?></span>
			</h3>
		<?php endif; ?>
		<?php if ($this->params->get('show_contact_category') == 'show_with_link') : ?>
			<?php $contactLink = ContactHelperRoute::getCategoryRoute($this->contact->catid); ?>
			<h3>
				<span class="contact-category"><a href="<?php echo $contactLink; ?>">
					<?php echo $this->escape($this->contact->category_title); ?></a>
				</span>
			</h3>
		<?php endif; ?>
		<?php if ($this->params->get('show_contact_list') && count($this->contacts) > 1) : ?>
			<form action="#" method="get" name="selectForm" id="selectForm">
				<?php echo JText::_('COM_CONTACT_SELECT_CONTACT'); ?>
				<?php echo JHtml::_('select.genericlist', $this->contacts, 'id', 'class="input" onchange="document.location.href = this.value"', 'link', 'name', $this->contact->link);?>
			</form>
		<?php endif; ?>

		<?php if ($this->params->get('show_tags', 1) && !empty($this->item->tags)) : ?>
			<?php $this->item->tagLayout = new JLayoutFile('joomla.content.tags'); ?>
			<?php echo $this->item->tagLayout->render($this->item->tags->itemTags); ?>
		<?php endif; ?>
		
		<?php if ($this->params->get('presentation_style') == 'sliders') : ?>
			<div class="panel-group" id="slide-contact">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a class="accordion-toggle" data-toggle="collapse" data-parent="#slide-contact" href="#basic-details">
							<?php echo JText::_('COM_CONTACT_DETAILS');?>
							</a>
						</h4>
					</div>
					<div id="basic-details" class="panel-collapse collapse in">
						<div class="panel-body">
		<?php endif; ?>
		<?php if ($this->params->get('presentation_style') == 'tabs'):?>
			<ul class="nav nav-tabs" id="myTab">
					<li class="active"><a data-toggle="tab" href="#basic-details"><?php echo JText::_('COM_CONTACT_DETAILS'); ?></a></li>
					<?php if ($this->params->get('show_email_form') && ($this->contact->email_to || $this->contact->user_id)) : ?><li><a data-toggle="tab" href="#display-form"><?php echo JText::_('COM_CONTACT_EMAIL_FORM'); ?></a></li><?php endif; ?>
					<?php if ($this->params->get('show_links')) : ?><li><a data-toggle="tab" href="#display-links"><?php echo JText::_('COM_CONTACT_LINKS'); ?></a></li><?php endif; ?>
					<?php if ($this->params->get('show_articles') && $this->contact->user_id && $this->contact->articles) : ?><li><a data-toggle="tab" href="#display-articles"><?php echo JText::_('JGLOBAL_ARTICLES'); ?></a></li><?php endif; ?>
					<?php if ($this->params->get('show_profile') && $this->contact->user_id && JPluginHelper::isEnabled('user', 'profile')) : ?><li><a data-toggle="tab" href="#display-profile"><?php echo JText::_('COM_CONTACT_PROFILE'); ?></a></li><?php endif; ?>
					<?php if ($this->contact->misc && $this->params->get('show_misc')) : ?><li><a data-toggle="tab" href="#display-misc"><?php echo JText::_('COM_CONTACT_OTHER_INFORMATION'); ?></a></li><?php endif; ?>
			</ul>
			<div class="tab-content" id="myTabContent">
				<div id="basic-details" class="tab-pane active">
		<?php endif; ?>
		<?php if ($this->params->get('presentation_style') == 'plain') : ?>
			<?php  echo '<h3>'. JText::_('COM_CONTACT_DETAILS').'</h3>';  ?>
		<?php endif; ?>
		
		<?php if ($this->contact->image && $this->params->get('show_image')) : ?>
			<div class="thumbnail pull-right">
				<?php echo JHtml::_('image', $this->contact->image, JText::_('COM_CONTACT_IMAGE_DETAILS'), array('align' => 'middle', 'itemprop' => 'image')); ?>
			</div>
		<?php endif; ?>

		<?php if ($this->contact->con_position && $this->params->get('show_position')) : ?>
			<dl class="contact-position dl-horizontal">
				<dd itemprop="jobTitle">
					<?php echo $this->contact->con_position; ?>
				</dd>
			</dl>
		<?php endif; ?>

		<?php echo $this->loadTemplate('address'); ?>

		<?php if ($this->params->get('allow_vcard')) :	?>
			<?php echo JText::_('COM_CONTACT_DOWNLOAD_INFORMATION_AS');?>
				<a href="<?php echo JRoute::_('index.php?option=com_contact&amp;view=contact&amp;id='.$this->contact->id . '&amp;format=vcf'); ?>">
				<?php echo JText::_('COM_CONTACT_VCARD');?></a>
		<?php endif; ?>
		<?php if ($this->params->get('presentation_style')=='sliders'):?>
						</div>
					</div>
				</div>
		<?php endif; ?>
		<?php if ($this->params->get('presentation_style') == 'tabs') : ?>
				</div>
		<?php endif; ?>
		<?php if ($this->params->get('show_email_form') && ($this->contact->email_to || $this->contact->user_id)) : ?>

			<?php if ($this->params->get('presentation_style')=='sliders'):?>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a class="accordion-toggle" data-toggle="collapse" data-parent="#slide-contact" href="#display-form">
							<?php echo JText::_('COM_CONTACT_EMAIL_FORM');?>
							</a>
						</h4>
					</div>
					<div id="display-form" class="panel-collapse collapse">
						<div class="panel-body">
			<?php endif; ?>
			<?php if ($this->params->get('presentation_style') == 'tabs') : ?>
				<div id="display-form" class="tab-pane">
			<?php endif; ?>
			<?php if ($this->params->get('presentation_style')=='plain'):?>
				<?php  echo '<h3>'. JText::_('COM_CONTACT_EMAIL_FORM').'</h3>';  ?>
			<?php endif; ?>
			<?php  echo $this->loadTemplate('form');  ?>
			<?php if ($this->params->get('presentation_style')=='sliders'):?>
						</div>
					</div>
				</div>
			<?php endif; ?>
			<?php if ($this->params->get('presentation_style') == 'tabs') : ?>
				</div>
			<?php endif; ?>
		<?php endif; ?>
		
		<?php if ($this->params->get('show_links')) : ?>
			<?php echo $this->loadTemplate('links'); ?>
		<?php endif; ?>
			
		<?php if ($this->params->get('show_articles') && $this->contact->user_id && $this->contact->articles) : ?>
			<?php if ($this->params->get('presentation_style')=='sliders'):?>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a class="accordion-toggle" data-toggle="collapse" data-parent="#slide-contact" href="#display-articles">
							<?php echo JText::_('JGLOBAL_ARTICLES');?>
							</a>
						</h4>
					</div>
					<div id="display-articles" class="panel-collapse collapse">
						<div class="panel-body">
			<?php endif; ?>
			<?php if ($this->params->get('presentation_style') == 'tabs') : ?>
				<div id="display-articles" class="tab-pane">
			<?php endif; ?>
			<?php if  ($this->params->get('presentation_style')=='plain'):?>
				<?php echo '<h3>'. JText::_('JGLOBAL_ARTICLES').'</h3>'; ?>
			<?php endif; ?>
				<?php echo $this->loadTemplate('articles'); ?>
			<?php if ($this->params->get('presentation_style')=='sliders'):?>
						</div>
					</div>
				</div>
			<?php endif; ?>
			<?php if ($this->params->get('presentation_style') == 'tabs') : ?>
				</div>
			<?php endif; ?>
		<?php endif; ?>
		<?php if ($this->params->get('show_profile') && $this->contact->user_id && JPluginHelper::isEnabled('user', 'profile')) : ?>
			<?php if ($this->params->get('presentation_style')=='sliders'):?>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a class="accordion-toggle" data-toggle="collapse" data-parent="#slide-contact" href="#display-profile">
							<?php echo JText::_('COM_CONTACT_PROFILE');?>
							</a>
						</h4>
					</div>
					<div id="display-profile" class="panel-collapse collapse">
						<div class="panel-body">
			<?php endif; ?>
			<?php if ($this->params->get('presentation_style') == 'tabs') : ?>
				<div id="display-profile" class="tab-pane">
			<?php endif; ?>
			<?php if ($this->params->get('presentation_style')=='plain'):?>
				<?php echo '<h3>'. JText::_('COM_CONTACT_PROFILE').'</h3>'; ?>
			<?php endif; ?>
			<?php echo $this->loadTemplate('profile'); ?>
			<?php if ($this->params->get('presentation_style')=='sliders'):?>
						</div>
					</div>
				</div>
			<?php endif; ?>
			<?php if ($this->params->get('presentation_style') == 'tabs') : ?>
				</div>
			<?php endif; ?>
		<?php endif; ?>
		<?php if ($this->contact->misc && $this->params->get('show_misc')) : ?>
			<?php if ($this->params->get('presentation_style')=='sliders'):?>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a class="accordion-toggle" data-toggle="collapse" data-parent="#slide-contact" href="#display-misc">
							<?php echo JText::_('COM_CONTACT_OTHER_INFORMATION');?>
							</a>
						</h4>
					</div>
					<div id="display-misc" class="panel-collapse collapse">
						<div class="panel-body">
			<?php endif; ?>
			<?php if ($this->params->get('presentation_style') == 'tabs') : ?>
				<div id="display-misc" class="tab-pane">
			<?php endif; ?>
			<?php if ($this->params->get('presentation_style')=='plain'):?>
				<?php echo '<h3>'. JText::_('COM_CONTACT_OTHER_INFORMATION').'</h3>'; ?>
			<?php endif; ?>
					<div class="contact-miscinfo">
						<dl class="dl-horizontal">
							<dt>
								<span class="<?php echo $this->params->get('marker_class'); ?>">
									<?php echo $this->params->get('marker_misc'); ?>
								</span>
							</dt>
							<dd>
								<span class="contact-misc">
									<?php echo $this->contact->misc; ?>
								</span>
							</dd>
						</dl>
					</div>
			<?php if ($this->params->get('presentation_style')=='sliders'):?>
						</div>
					</div>
				</div>
			<?php endif; ?>
			<?php if ($this->params->get('presentation_style') == 'tabs') : ?>
				</div>
			<?php endif; ?>
		<?php endif; ?>
		<?php if ($this->params->get('presentation_style')=='sliders'):?>
				<script type="text/javascript">
					(function($){
						$('#slide-contact').collapse({ parent: false, toggle: true, active: 'basic-details'});
					})(jQuery);
				</script>
			</div>
		<?php endif; ?>
		<?php if ($this->params->get('presentation_style') == 'tabs') : ?>
			</div>
		<?php endif; ?>
	</div>
<?php endif; ?>