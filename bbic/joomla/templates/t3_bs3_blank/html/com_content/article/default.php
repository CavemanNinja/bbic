<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$attribs = new JRegistry($this->item->attribs);
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
// JHtml::addIncludePath(T3_PATH . '/html/com_content');
JHtml::addIncludePath(dirname(dirname(__FILE__)));
JHtml::stylesheet(JUri::base().'templates/t3_bs3_blank/css/font-awesome.min.css', array(), true);
JHtml::stylesheet(JUri::base().'templates/t3_bs3_blank/css/map.css', array(), true);

JHtml::_('bootstrap.framework');
JHtml::script(JUri::base().'templates/t3_bs3_blank/js/map.js', false, true);

// <script type="text/javascript">
// 	(function($){
// 		$('#slide-contact').collapse({ parent: false, toggle: true, active: 'basic-details'});
// 	})(jQuery);
// </script>

$document = JFactory::getDocument();
$document->addScriptDeclaration("
	// jQuery(function(){
	// 	jQuery('#slide-contact').collapse({ parent: false, toggle: true, active: 'basic-details'});
	// });
");

// Create shortcuts to some parameters.
$params   = $this->item->params;
$images   = json_decode($this->item->images);
$urls     = json_decode($this->item->urls);
$canEdit  = $params->get('access-edit');
$user     = JFactory::getUser();
$info    = $params->get('info_block_position', 2);
$aInfo1 = ($params->get('show_publish_date') || $params->get('show_category') || $params->get('show_parent_category') || $params->get('show_author'));
$aInfo2 = ($params->get('show_create_date') || $params->get('show_modify_date') || $params->get('show_hits'));
$topInfo = ($aInfo1 && $info != 1) || ($aInfo2 && $info == 0);
$botInfo = ($aInfo1 && $info == 1) || ($aInfo2 && $info != 0);
$icons = !empty($this->print) || $canEdit || $params->get('show_print_icon') || $params->get('show_email_icon');
$articleid = $this->item->id;
$catid = $this->item->catid;
// var_dump($params);
// var_dump($catid);

// override link_titles option if set in template
$app = JFactory::getApplication();
$tmpl = $app->getTemplate(true);
if ($tmpl->params->get('link_titles') !== NULL) {
	$params->set('link_titles', $tmpl->params->get('link_titles'));
}

/* UNLINK TITLE IF HOMEPAGE ARTICLE */
if ($articleid == "7") {
	$params->set('link_titles', "0");
}

/* DO NOT SHOW IF BILL OR SERVICE REQUEST */
if ($catid == "12" || $catid == "10") {
	$params->set("show_create_date", "0");
	$params->set("show_author", "0");
	$params->set("show_hits", "0");
	$params->set("show_category", "0");
	$params->set("show_publish_date", "0");
}

/* APPLICATIONS */
if ($catid == "11" || $catid == "17" || $catid == "29") {
	$params->set("show_author", "0");
	$params->set("show_category", "0");
	$params->set("show_hits", "0");	
	$params->set("show_publish_date", "0");	
}

JHtml::_('behavior.caption');
JHtml::_('bootstrap.tooltip');
?>

<?php if ($this->params->get('show_page_heading', 1)) : ?>
	<div class="page-header clearfix">
		<h1 class="page-title"><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	</div>
<?php endif; ?>

<div class="item-page<?php echo $this->pageclass_sfx ?> clearfix">

<?php if (!empty($this->item->pagination) && $this->item->pagination && !$this->item->paginationposition && $this->item->paginationrelative) : ?>
	<?php echo $this->item->pagination; ?>
<?php endif; ?>

	<!-- Article -->
<!-- BILLING -->

<?php if ($catid == "10") : ?>

	<article itemscope itemtype="http://schema.org/Article">
		<meta itemprop="inLanguage" content="<?php echo ($this->item->language === '*') ? JFactory::getConfig()->get('language') : $this->item->language; ?>" />

	<?php if ($params->get('show_title')) : ?>
		<?php echo JLayoutHelper::render('joomla.content.item_title', array('item' => $this->item, 'params' => $params, 'title-tag'=>'h1')); ?>
	<?php endif; ?>

	<!-- Aside -->
	<?php if ($topInfo || $icons) : ?>
	<aside class="article-aside clearfix">
	  <?php if ($topInfo): ?>
	  <?php echo JLayoutHelper::render('joomla.content.info_block.block', array('item' => $this->item, 'params' => $params, 'position' => 'above')); ?>
	  <?php endif; ?>
	  
	  <?php if ($icons): ?>
	  <?php echo JLayoutHelper::render('joomla.content.icons', array('item' => $this->item, 'params' => $params, 'print' => $this->print)); ?>
	  <?php endif; ?>
	</aside>  
	<?php endif; ?>
	<!-- //Aside -->

	<?php if (isset ($this->item->toc)) : ?>
		<?php echo $this->item->toc; ?>
	<?php endif; ?>

	<?php if ($params->get('show_tags', 1) && !empty($this->item->tags)) : ?>
		<?php echo JLayoutHelper::render('joomla.content.tags', $this->item->tags->itemTags); ?>
	<?php endif; ?>

	<?php if (!$params->get('show_intro')) : ?>
		<?php echo $this->item->event->afterDisplayTitle; ?>
	<?php endif; ?>

	<?php echo $this->item->event->beforeDisplayContent; ?>

	<?php if (isset($urls) && ((!empty($urls->urls_position) && ($urls->urls_position == '0')) || ($params->get('urls_position') == '0' && empty($urls->urls_position))) || (empty($urls->urls_position) && (!$params->get('urls_position')))): ?>
		<?php echo $this->loadTemplate('links'); ?>
	<?php endif; ?>

	<?php if ($params->get('access-view')): ?>

		<?php echo JLayoutHelper::render('joomla.content.fulltext_image', array('item' => $this->item, 'params' => $params)); ?>

		<?php	if (!empty($this->item->pagination) AND $this->item->pagination AND !$this->item->paginationposition AND !$this->item->paginationrelative):
			echo $this->item->pagination;
		endif; ?>

		<section class="article-content clearfix" itemprop="articleBody">
			<?php echo $this->item->text; ?>
		</section>

		<!-- EXTRA FIELDS CONTENT -->
		<h2>Invoice Details</h2>

		<?php if ($attribs->get('billing_invoice_date')) : ?>
 			<?php echo "<div class='ef_billing_date'><span class='ef-label'>".JText::_('TPL_EXTRAFIELDS_BILLING_INVOICE_DATE').": </span>" ?>
 			<span class="item-state state-<?php echo $attribs->get('billing_invoice_date') ?>">
 				<?php echo $attribs->get('billing_invoice_date') ?>
 			</span>
 			<?php echo "</div>" ?>
 		<?php endif ?>

 		<?php if ($attribs->get('billing_tenant_name')) : ?>
 			<?php echo "<div class='ef_billing_name'><span class='ef-label'>".JText::_('TPL_EXTRAFIELDS_BILLING_INVOICEE').": </span>" ?>
 			<span class="item-state state-<?php echo $attribs->get('name') ?>">
 				<?php echo $attribs->get('billing_tenant_name') ?>
 			</span>
 			<?php echo "</div>" ?>
 		<?php endif ?>
		
		<?php if ($attribs->get('billing_amount')) : ?>
 			<?php echo "<div class='ef_billing_amount'><span class='ef-label'>".JText::_('TPL_EXTRAFIELDS_BILLING_AMOUNT').": </span>" ?>
	 			<span class="item-state state-<?php echo $attribs->get('billing_amount') ?>">
	 				<?php echo $attribs->get('billing_amount') ?>
	 			</span>
 			<?php echo "</div>" ?>
 		<?php endif ?>

		<?php if ($attribs->get('billing_due_date')) : ?>
 			<?php echo "<div class='ef_billing_date'><span class='ef-label'>".JText::_('TPL_EXTRAFIELDS_BILLING_DUE_DATE').": </span>" ?>
 			<span class="item-state state-<?php echo $attribs->get('billing_due_date') ?>">
 				<?php echo $attribs->get('billing_due_date') ?>
 			</span>
 			<?php echo "</div>" ?>
 		<?php endif ?>

 		
		<?php echo "<div class='ef_billing_date'><span class='ef-label'>".JText::_('TPL_EXTRAFIELDS_BILLING_STATUS').": </span>" ?>
		<?php if ($attribs->get('billing_status') == "1") : ?>
			<?php echo "Paid" ?>
		<?php else : ?>
			<?php echo "Unpaid" ?>
		<?php endif; ?>
		<?php echo "</div>" ?>

	  <!-- footer -->
	  <?php if ($botInfo) : ?>
	  <footer class="article-footer clearfix">
	    <?php echo JLayoutHelper::render('joomla.content.info_block.block', array('item' => $this->item, 'params' => $params, 'position' => 'below')); ?>
	  </footer>
	  <?php endif; ?>
	  <!-- //footer -->

		<?php
		if (false): ?>
			<?php
			echo '<hr class="divider-vertical" />';
			echo $this->item->pagination;
			?>
		<?php endif; ?>

		<?php if (isset($urls) && ((!empty($urls->urls_position) && ($urls->urls_position == '1')) || ($params->get('urls_position') == '1'))): ?>
			<?php echo $this->loadTemplate('links'); ?>
		<?php endif; ?>

		<?php //optional teaser intro text for guests ?>
	<?php elseif ($params->get('show_noauth') == true and  $user->get('guest')) : ?>

		<?php echo $this->item->introtext; ?>
		<?php //Optional link to let them register to see the whole article. ?>
		<?php if ($params->get('show_readmore') && $this->item->fulltext != null) :
			$link1 = JRoute::_('index.php?option=com_users&view=login');
			$link = new JURI($link1);
			?>
			<section class="readmore">
				<a href="<?php echo $link; ?>" itemprop="url">
							<span>
							<?php $attribs = json_decode($this->item->attribs); ?>
							<?php
							if ($attribs->alternative_readmore == null) :
								echo JText::_('COM_CONTENT_REGISTER_TO_READ_MORE');
							elseif ($readmore = $this->item->alternative_readmore) :
								echo $readmore;
								if ($params->get('show_readmore_title', 0) != 0) :
									echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
								endif;
							elseif ($params->get('show_readmore_title', 0) == 0) :
								echo JText::sprintf('COM_CONTENT_READ_MORE_TITLE');
							else :
								echo JText::_('COM_CONTENT_READ_MORE');
								echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
							endif; ?>
							</span>
				</a>
			</section>
		<?php endif; ?>
	<?php endif; ?>

	</article>
	<!-- //Article -->

	<?php if (!empty($this->item->pagination) && $this->item->pagination && $this->item->paginationposition && $this->item->paginationrelative): ?>
		<?php echo $this->item->pagination; ?>
	<?php endif; ?>

	<?php echo $this->item->event->afterDisplayContent; ?>
	</div>

<!-- SERVICE REQUEST -->


<?php elseif ($catid == "12") : ?>

<article itemscope itemtype="http://schema.org/Article">
	<meta itemprop="inLanguage" content="<?php echo ($this->item->language === '*') ? JFactory::getConfig()->get('language') : $this->item->language; ?>" />

	<?php if ($params->get('show_title')) : ?>
		<?php echo JLayoutHelper::render('joomla.content.item_title', array('item' => $this->item, 'params' => $params, 'title-tag'=>'h1')); ?>
	<?php endif; ?>

	<!-- Aside -->
	<?php if ($topInfo || $icons) : ?>
	<aside class="article-aside clearfix">
	  <?php if ($topInfo): ?>
	  <?php echo JLayoutHelper::render('joomla.content.info_block.block', array('item' => $this->item, 'params' => $params, 'position' => 'above')); ?>
	  <?php endif; ?>
	  
	  <?php if ($icons): ?>
	  <?php echo JLayoutHelper::render('joomla.content.icons', array('item' => $this->item, 'params' => $params, 'print' => $this->print)); ?>
	  <?php endif; ?>
	</aside>  
	<?php endif; ?>
	<!-- //Aside -->


	<?php if (isset ($this->item->toc)) : ?>
		<?php echo $this->item->toc; ?>
	<?php endif; ?>

	<?php if ($params->get('show_tags', 1) && !empty($this->item->tags)) : ?>
		<?php echo JLayoutHelper::render('joomla.content.tags', $this->item->tags->itemTags); ?>
	<?php endif; ?>

	<?php if (!$params->get('show_intro')) : ?>
		<?php echo $this->item->event->afterDisplayTitle; ?>
	<?php endif; ?>

	<?php echo $this->item->event->beforeDisplayContent; ?>

	<?php if (isset($urls) && ((!empty($urls->urls_position) && ($urls->urls_position == '0')) || ($params->get('urls_position') == '0' && empty($urls->urls_position))) || (empty($urls->urls_position) && (!$params->get('urls_position')))): ?>
		<?php echo $this->loadTemplate('links'); ?>
	<?php endif; ?>

	<?php	if ($params->get('access-view')): ?>

		<?php echo JLayoutHelper::render('joomla.content.fulltext_image', array('item' => $this->item, 'params' => $params)); ?>

		<?php	if (!empty($this->item->pagination) AND $this->item->pagination AND !$this->item->paginationposition AND !$this->item->paginationrelative):
			echo $this->item->pagination;
		endif; ?>

		<section class="article-content clearfix" itemprop="articleBody">
			<?php echo $this->item->text; ?>
		</section>

		<!-- EXTRA FIELDS CONTENT -->

		<h2>Service Request Details</h2>

		<?php if ($attribs->get('servicerequest_date')) : ?>
 			<?php echo "<div class='ef_servicerequest_date'><span class='ef-label'>".JText::_('TPL_EXTRAFIELDS_SERVICEREQUEST_DATE').": </span>" ?>
 			<span class="item-state state-<?php echo $attribs->get('servicerequest_date') ?>">
 				<?php echo $attribs->get('servicerequest_date') ?>
 			</span>
 			<?php echo "</div>" ?>
 		<?php endif ?>
		
		<?php if ($attribs->get('service_name')) : ?>
			<?php echo "<div class='ef_servicerequest_item'><span class='ef-label'>".JText::_('TPL_EXTRAFIELDS_SERVICE_NAME').": </span>" ?>
			<?php echo $attribs->get('service_name'); ?>
			<?php echo "</div>" ?>
		<?php endif; ?>

		<?php if ($attribs->get('service_price')) : ?>
			<?php echo "<div class='ef_servicerequest_item'><span class='ef-label'>".JText::_('TPL_EXTRAFIELDS_SERVICE_PRICE').": </span>" ?>
			<?php echo $attribs->get('service_price'); ?>
			<?php echo "</div>" ?>
		<?php endif; ?>
		
		<?php echo "<div class='ef_servicerequest_approval'><span class='ef-label'>". JText::_('TPL_EXTRAFIELDS_SERVICEREQUEST_APPROVAL') .": </span>" ?>
		<?php if ($attribs->get('servicerequest_approval') == "1") : ?>
			<?php echo "Approved" ?>
		<?php elseif ($attribs->get('servicerequest_approval') == "2") : ?>
			<?php echo "Denied" ?>
		<?php else : ?>
			<?php echo "Pending" ?>
		<?php endif; ?>
		<?php echo "</div>" ?>

	  <!-- footer -->
	  <?php if ($botInfo) : ?>
	  <footer class="article-footer clearfix">
	    <?php echo JLayoutHelper::render('joomla.content.info_block.block', array('item' => $this->item, 'params' => $params, 'position' => 'below')); ?>
	  </footer>
	  <?php endif; ?>
	  <!-- //footer -->

		<?php
		if (false): ?>
			<?php
			echo '<hr class="divider-vertical" />';
			echo $this->item->pagination;
			?>
		<?php endif; ?>

		<?php if (isset($urls) && ((!empty($urls->urls_position) && ($urls->urls_position == '1')) || ($params->get('urls_position') == '1'))): ?>
			<?php echo $this->loadTemplate('links'); ?>
		<?php endif; ?>

		<?php //optional teaser intro text for guests ?>
	<?php elseif ($params->get('show_noauth') == true and  $user->get('guest')) : ?>

		<?php echo $this->item->introtext; ?>
		<?php //Optional link to let them register to see the whole article. ?>
		<?php if ($params->get('show_readmore') && $this->item->fulltext != null) :
			$link1 = JRoute::_('index.php?option=com_users&view=login');
			$link = new JURI($link1);
			?>
			<section class="readmore">
				<a href="<?php echo $link; ?>" itemprop="url">
							<span>
							<?php $attribs = json_decode($this->item->attribs); ?>
							<?php
							if ($attribs->alternative_readmore == null) :
								echo JText::_('COM_CONTENT_REGISTER_TO_READ_MORE');
							elseif ($readmore = $this->item->alternative_readmore) :
								echo $readmore;
								if ($params->get('show_readmore_title', 0) != 0) :
									echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
								endif;
							elseif ($params->get('show_readmore_title', 0) == 0) :
								echo JText::sprintf('COM_CONTENT_READ_MORE_TITLE');
							else :
								echo JText::_('COM_CONTENT_READ_MORE');
								echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
							endif; ?>
							</span>
				</a>
			</section>
		<?php endif; ?>
	<?php endif; ?>

	</article>
	<!-- //Article -->

	<?php if (!empty($this->item->pagination) && $this->item->pagination && $this->item->paginationposition && $this->item->paginationrelative): ?>
		<?php echo $this->item->pagination; ?>
	<?php endif; ?>

	<?php echo $this->item->event->afterDisplayContent; ?>
	</div>

<!-- PUBLIC PAGES -->
<?php elseif ($catid == "11" || $catid == "17") : ?>
<article itemscope itemtype="http://schema.org/Article">
		<meta itemprop="inLanguage" content="<?php echo ($this->item->language === '*') ? JFactory::getConfig()->get('language') : $this->item->language; ?>" />

	<?php if ($params->get('show_title')) : ?>
		<?php echo JLayoutHelper::render('joomla.content.item_title', array('item' => $this->item, 'params' => $params, 'title-tag'=>'h1')); ?>
	<?php endif; ?>

	<!-- Aside -->
	<?php if ($topInfo || $icons) : ?>
	<aside class="article-aside clearfix">
	  <?php if ($topInfo): ?>
	  <?php echo JLayoutHelper::render('joomla.content.info_block.block', array('item' => $this->item, 'params' => $params, 'position' => 'above')); ?>
	  <?php endif; ?>
	  
	  <?php if ($icons): ?>
	  <?php echo JLayoutHelper::render('joomla.content.icons', array('item' => $this->item, 'params' => $params, 'print' => $this->print)); ?>
	  <?php endif; ?>
	</aside>  
	<?php endif; ?>
	<!-- //Aside -->


	<?php if (isset ($this->item->toc)) : ?>
		<?php echo $this->item->toc; ?>
	<?php endif; ?>

	<?php if ($params->get('show_tags', 1) && !empty($this->item->tags)) : ?>
		<?php echo JLayoutHelper::render('joomla.content.tags', $this->item->tags->itemTags); ?>
	<?php endif; ?>

	<?php if (!$params->get('show_intro')) : ?>
		<?php echo $this->item->event->afterDisplayTitle; ?>
	<?php endif; ?>

	<?php echo $this->item->event->beforeDisplayContent; ?>

	<?php if (isset($urls) && ((!empty($urls->urls_position) && ($urls->urls_position == '0')) || ($params->get('urls_position') == '0' && empty($urls->urls_position))) || (empty($urls->urls_position) && (!$params->get('urls_position')))): ?>
		<?php echo $this->loadTemplate('links'); ?>
	<?php endif; ?>

	<?php	if ($params->get('access-view')): ?>

		<?php echo JLayoutHelper::render('joomla.content.fulltext_image', array('item' => $this->item, 'params' => $params)); ?>

		<?php	if (!empty($this->item->pagination) AND $this->item->pagination AND !$this->item->paginationposition AND !$this->item->paginationrelative):
			echo $this->item->pagination;
		endif; ?>

		<section class="article-content clearfix" itemprop="articleBody">
			<?php echo $this->item->text; ?>
		</section>

	  <!-- footer -->
	  <?php if ($botInfo) : ?>
	  <footer class="article-footer clearfix">
	    <?php echo JLayoutHelper::render('joomla.content.info_block.block', array('item' => $this->item, 'params' => $params, 'position' => 'below')); ?>
	  </footer>
	  <?php endif; ?>
	  <!-- //footer -->

		<?php
		if (!empty($this->item->pagination) && $this->item->pagination && $this->item->paginationposition && !$this->item->paginationrelative): ?>
			<?php
			echo '<hr class="divider-vertical" />';
			echo $this->item->pagination;
			?>
		<?php endif; ?>

		<?php if (isset($urls) && ((!empty($urls->urls_position) && ($urls->urls_position == '1')) || ($params->get('urls_position') == '1'))): ?>
			<?php echo $this->loadTemplate('links'); ?>
		<?php endif; ?>

		<?php //optional teaser intro text for guests ?>
	<?php elseif ($params->get('show_noauth') == true and  $user->get('guest')) : ?>

		<?php echo $this->item->introtext; ?>
		<?php //Optional link to let them register to see the whole article. ?>
		<?php if ($params->get('show_readmore') && $this->item->fulltext != null) :
			$link1 = JRoute::_('index.php?option=com_users&view=login');
			$link = new JURI($link1);
			?>
			<section class="readmore">
				<a href="<?php echo $link; ?>" itemprop="url">
							<span>
							<?php $attribs = json_decode($this->item->attribs); ?>
							<?php
							if ($attribs->alternative_readmore == null) :
								echo JText::_('COM_CONTENT_REGISTER_TO_READ_MORE');
							elseif ($readmore = $this->item->alternative_readmore) :
								echo $readmore;
								if ($params->get('show_readmore_title', 0) != 0) :
									echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
								endif;
							elseif ($params->get('show_readmore_title', 0) == 0) :
								echo JText::sprintf('COM_CONTENT_READ_MORE_TITLE');
							else :
								echo JText::_('COM_CONTENT_READ_MORE');
								echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
							endif; ?>
							</span>
				</a>
			</section>
		<?php endif; ?>
	<?php endif; ?>

	</article>
	<!-- //Article -->

	<?php if (!empty($this->item->pagination) && $this->item->pagination && $this->item->paginationposition && $this->item->paginationrelative): ?>
		<?php echo $this->item->pagination; ?>
	<?php endif; ?>

	<?php echo $this->item->event->afterDisplayContent; ?>
	</div>

<!-- COMPANY PROFILE -->
<?php elseif ($catid == "9") : ?>
	<article itemscope itemtype="http://schema.org/Article">
		<meta itemprop="inLanguage" content="<?php echo ($this->item->language === '*') ? JFactory::getConfig()->get('language') : $this->item->language; ?>" />

	<?php if ($params->get('show_title')) : ?>
		<?php echo JLayoutHelper::render('joomla.content.item_title', array('item' => $this->item, 'params' => $params, 'title-tag'=>'h1')); ?>
	<?php endif; ?>

	<!-- Aside -->
	<?php if ($topInfo || $icons) : ?>
	<aside class="article-aside clearfix">
	  <?php if (false): ?>
	  <?php echo JLayoutHelper::render('joomla.content.info_block.block', array('item' => $this->item, 'params' => $params, 'position' => 'above')); ?>
	  <?php endif; ?>
	  
	  <?php if ($icons): ?>
	  <?php echo JLayoutHelper::render('joomla.content.icons', array('item' => $this->item, 'params' => $params, 'print' => $this->print)); ?>
	  <?php endif; ?>
	</aside>  
	<?php endif; ?>
	<!-- //Aside -->


	<?php if (isset ($this->item->toc)) : ?>
		<?php echo $this->item->toc; ?>
	<?php endif; ?>

	<?php if ($params->get('show_tags', 1) && !empty($this->item->tags)) : ?>
		<?php echo JLayoutHelper::render('joomla.content.tags', $this->item->tags->itemTags); ?>
	<?php endif; ?>

	<?php if (!$params->get('show_intro')) : ?>
		<?php echo $this->item->event->afterDisplayTitle; ?>
	<?php endif; ?>

	<?php echo $this->item->event->beforeDisplayContent; ?>

	<?php if (isset($urls) && ((!empty($urls->urls_position) && ($urls->urls_position == '0')) || ($params->get('urls_position') == '0' && empty($urls->urls_position))) || (empty($urls->urls_position) && (!$params->get('urls_position')))): ?>
		<?php echo $this->loadTemplate('links'); ?>
	<?php endif; ?>

	<?php	if ($params->get('access-view')): ?>

		<?php echo JLayoutHelper::render('joomla.content.fulltext_image', array('item' => $this->item, 'params' => $params)); ?>

		<?php	if (!empty($this->item->pagination) AND $this->item->pagination AND !$this->item->paginationposition AND !$this->item->paginationrelative):
			echo $this->item->pagination;
		endif; ?>

		<section class="article-content clearfix" itemprop="articleBody">
			<?php echo $this->item->text; ?>
		</section>

		<!--EXTRA FIELDS -->
		<?php if ($attribs->get('companyprofile_address1')) : ?>
			<h2>Contact Details</h2>
 			<?php echo "<div class='ef_companyprofile_address1'><span class='ef-label'>".JText::_('TPL_EXTRAFIELDS_COMPANYPROFILE_ADDRESS').":  </span>"?>
 			<span class="item-state state-<?php echo $attribs->get('companyprofile_address1') ?>">
 				<?php 
 					$address = $attribs->get('companyprofile_address1');
 					if ($attribs->get('companyprofile_address2'))
 						$address .= ", ".$attribs->get('companyprofile_address2');

 					if ($attribs->get('companyprofile_block'))
 						$address .= ", Block No. ".$attribs->get('companyprofile_block');
					
					if ($attribs->get('companyprofile_area'))
 						$address .= ", ".$attribs->get('companyprofile_area');

 					if ($attribs->get('companyprofile_pobox'))
 						$address .= ", PO Box: ".$attribs->get('companyprofile_pobox'); 					
 					echo $address;
 				?>
 			</span>
 			<?php echo "</div>" ?>
 		<?php endif ?>

 		<?php if ($attribs->get('companyprofile_tel')) : ?>
 			<?php echo "<div class='ef_companyprofile_block'><span class='ef-label'>".JText::_('TPL_EXTRAFIELDS_COMPANYPROFILE_TEL').": </span>" ?>
 			<span class="item-state state-<?php echo $attribs->get('companyprofile_tel') ?>">
 				<?php echo $attribs->get('companyprofile_tel') ?>
 			</span>
 			<?php echo "</div>" ?>
 		<?php endif ?>
 		
 		<?php if ($attribs->get('companyprofile_website')) : ?>
 			<?php echo "<div class='ef_companyprofile_block'><span class='ef-label'>".JText::_('TPL_EXTRAFIELDS_COMPANYPROFILE_WEBSITE').": </span>" ?>
 			<span class="item-state state-<?php echo $attribs->get('companyprofile_website') ?>">
 				<a href="<?php echo $attribs->get('companyprofile_website') ?>">
 					<?php echo $attribs->get('companyprofile_website') ?>
 				</a>
 			</span>
 			<?php echo "</div>" ?>
 		<?php endif ?>

	  <!-- footer -->
	  <?php if ($botInfo) : ?>
	  <footer class="article-footer clearfix">
	    <?php echo JLayoutHelper::render('joomla.content.info_block.block', array('item' => $this->item, 'params' => $params, 'position' => 'below')); ?>
	  </footer>
	  <?php endif; ?>
	  <!-- //footer -->

		<?php
		if (!empty($this->item->pagination) && $this->item->pagination && $this->item->paginationposition && !$this->item->paginationrelative): ?>
			<?php
			echo '<hr class="divider-vertical" />';
			echo $this->item->pagination;
			?>
		<?php endif; ?>

		<?php if (isset($urls) && ((!empty($urls->urls_position) && ($urls->urls_position == '1')) || ($params->get('urls_position') == '1'))): ?>
			<?php echo $this->loadTemplate('links'); ?>
		<?php endif; ?>

		<?php //optional teaser intro text for guests ?>
	<?php elseif ($params->get('show_noauth') == true and  $user->get('guest')) : ?>

		<?php echo $this->item->introtext; ?>
		<?php //Optional link to let them register to see the whole article. ?>
		<?php if ($params->get('show_readmore') && $this->item->fulltext != null) :
			$link1 = JRoute::_('index.php?option=com_users&view=login');
			$link = new JURI($link1);
			?>
			<section class="readmore">
				<a href="<?php echo $link; ?>" itemprop="url">
							<span>
							<?php $attribs = json_decode($this->item->attribs); ?>
							<?php
							if ($attribs->alternative_readmore == null) :
								echo JText::_('COM_CONTENT_REGISTER_TO_READ_MORE');
							elseif ($readmore = $this->item->alternative_readmore) :
								echo $readmore;
								if ($params->get('show_readmore_title', 0) != 0) :
									echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
								endif;
							elseif ($params->get('show_readmore_title', 0) == 0) :
								echo JText::sprintf('COM_CONTENT_READ_MORE_TITLE');
							else :
								echo JText::_('COM_CONTENT_READ_MORE');
								echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
							endif; ?>
							</span>
				</a>
			</section>
		<?php endif; ?>
	<?php endif; ?>

	</article>
	<!-- //Article -->

	<?php if (!empty($this->item->pagination) && $this->item->pagination && $this->item->paginationposition && $this->item->paginationrelative): ?>
		<?php echo $this->item->pagination; ?>
	<?php endif; ?>

	<?php echo $this->item->event->afterDisplayContent; ?>
	</div>

<!--MAP-->
<?php elseif ($catid == "29") : ?>

	<article itemscope itemtype="http://schema.org/Article">
		<meta itemprop="inLanguage" content="<?php echo ($this->item->language === '*') ? JFactory::getConfig()->get('language') : $this->item->language; ?>" />

	<?php if ($params->get('show_title')) : ?>
		<?php echo JLayoutHelper::render('joomla.content.item_title', array('item' => $this->item, 'params' => $params, 'title-tag'=>'h1')); ?>
	<?php endif; ?>

	<!-- Aside -->
	<?php if ($topInfo || $icons) : ?>
	<aside class="article-aside clearfix">
	  <?php if ($topInfo): ?>
	  <?php echo JLayoutHelper::render('joomla.content.info_block.block', array('item' => $this->item, 'params' => $params, 'position' => 'above')); ?>
	  <?php endif; ?>
	  
	  <?php if ($icons): ?>
	  <?php echo JLayoutHelper::render('joomla.content.icons', array('item' => $this->item, 'params' => $params, 'print' => $this->print)); ?>
	  <?php endif; ?>
	</aside>  
	<?php endif; ?>
	<!-- //Aside -->
	

	<!-- <div class="panel-group collapse in" id="slide-contact" style="height: auto;">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a class="accordion-toggle" data-toggle="collapse" data-parent="#slide-contact" href="#basic-details">
					Level 1 
				</a>
			</h4>
		</div>
		<div id="basic-details" class="panel-collapse collapse in">
			<div class="panel-body">
				<div class="map_wrapper">
					<div class="map location1">
						<?php echo $attribs->get('map_location_1_company_name'); ?>
					</div>
					<div class="map location2">
						<?php echo $attribs->get('map_location_2_company_name'); ?>
					</div>
					<div class="map location3">
						<?php echo $attribs->get('map_location_3_company_name'); ?>
					</div>
					<div class="map location4">
						<?php echo $attribs->get('map_location_4_company_name'); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a class="accordion-toggle" data-toggle="collapse" data-parent="#slide-contact" href="#display-form">
					Level 2
				</a>
			</h4>
		</div>
		<div id="display-form" class="panel-collapse collapse">
			<div class="panel-body">
				Panel Body Level 2
			</div>
		</div>
	</div> -->

<!-- </div> -->


	<?php if (isset ($this->item->toc)) : ?>
		<?php echo $this->item->toc; ?>
	<?php endif; ?>

	<?php if ($params->get('show_tags', 1) && !empty($this->item->tags)) : ?>
		<?php echo JLayoutHelper::render('joomla.content.tags', $this->item->tags->itemTags); ?>
	<?php endif; ?>

	<?php if (!$params->get('show_intro')) : ?>
		<?php echo $this->item->event->afterDisplayTitle; ?>
	<?php endif; ?>

	<?php echo $this->item->event->beforeDisplayContent; ?>

	<?php if (isset($urls) && ((!empty($urls->urls_position) && ($urls->urls_position == '0')) || ($params->get('urls_position') == '0' && empty($urls->urls_position))) || (empty($urls->urls_position) && (!$params->get('urls_position')))): ?>
		<?php echo $this->loadTemplate('links'); ?>
	<?php endif; ?>

	<?php	if ($params->get('access-view')): ?>

		<?php echo JLayoutHelper::render('joomla.content.fulltext_image', array('item' => $this->item, 'params' => $params)); ?>

		<?php	if (!empty($this->item->pagination) AND $this->item->pagination AND !$this->item->paginationposition AND !$this->item->paginationrelative):
			echo $this->item->pagination;
		endif; ?>
		
		<section class="article-content clearfix" itemprop="articleBody">
			<!-- <?php var_dump($attribs->get('map_building8_1')); ?> -->
			<?php echo $this->item->text; ?>

			<select id="map-select">
				<option value="0" disabled selected> -- Select a business name -- </option>
				<?php 
					if ($attribs->get('map_building8_1') != "") {
						echo "<option value='building8_1'>".$attribs->get('map_building8_1')."</option>";
					}
					if ($attribs->get('map_building8_2') != "") {
						echo "<option value='building8_2'>".$attribs->get('map_building8_2')."</option>";
					}
					if ($attribs->get('map_building8_3') != "") {
						echo "<option value='building8_3'>".$attribs->get('map_building8_3')."</option>";
					}
					if ($attribs->get('map_building8_4') != "") {
						echo "<option value='building8_4'>".$attribs->get('map_building8_4')."</option>";
					}
					if ($attribs->get('map_building8_5') != "") {
						echo "<option value='building8_5'>".$attribs->get('map_building8_5')."</option>";
					}
					if ($attribs->get('map_building8_6') != "") {
						echo "<option value='building8_6'>".$attribs->get('map_building8_6')."</option>";
					}
					if ($attribs->get('map_building8_7') != "") {
						echo "<option value='building8_7'>".$attribs->get('map_building8_7')."</option>";
					}
					if ($attribs->get('map_building8_8') != "") {
						echo "<option value='building8_8'>".$attribs->get('map_building8_8')."</option>";
					}
					if ($attribs->get('map_building8_9') != "") {
						echo "<option value='building8_9'>".$attribs->get('map_building8_9')."</option>";
					}
					if ($attribs->get('map_building8_10') != "") {
						echo "<option value='building8_10'>".$attribs->get('map_building8_10')."</option>";
					}
					if ($attribs->get('map_building8_11') != "") {
						echo "<option value='building8_11'>".$attribs->get('map_building8_11')."</option>";
					}
					if ($attribs->get('map_building8_12') != "") {
						echo "<option value='building8_12'>".$attribs->get('map_building8_12')."</option>";
					}
					if ($attribs->get('map_building8_13') != "") {
						echo "<option value='building8_13'>".$attribs->get('map_building8_13')."</option>";
					}
					if ($attribs->get('map_building8_14') != "") {
						echo "<option value='building8_14'>".$attribs->get('map_building8_14')."</option>";
					}
					if ($attribs->get('map_building8_15') != "") {
						echo "<option value='building8_15'>".$attribs->get('map_building8_15')."</option>";
					}
					if ($attribs->get('map_building8_16') != "") {
						echo "<option value='building8_16'>".$attribs->get('map_building8_16')."</option>";
					}
					if ($attribs->get('map_building8_17') != "") {
						echo "<option value='building8_17'>".$attribs->get('map_building8_17')."</option>";
					}
					if ($attribs->get('map_building8_18') != "") {
						echo "<option value='building8_18'>".$attribs->get('map_building8_18')."</option>";
					}
					if ($attribs->get('map_building8_19') != "") {
						echo "<option value='building8_19'>".$attribs->get('map_building8_19')."</option>";
					}
					if ($attribs->get('map_building8_20') != "") {
						echo "<option value='building8_20'>".$attribs->get('map_building8_20')."</option>";
					}
					if ($attribs->get('map_building8_21') != "") {
						echo "<option value='building8_21'>".$attribs->get('map_building8_21')."</option>";
					}
					if ($attribs->get('map_building8_22') != "") {
						echo "<option value='building8_22'>".$attribs->get('map_building8_22')."</option>";
					}
					if ($attribs->get('map_building8_23') != "") {
						echo "<option value='building8_23'>".$attribs->get('map_building8_23')."</option>";
					}
					if ($attribs->get('map_building8_24') != "") {
						echo "<option value='building8_24'>".$attribs->get('map_building8_24')."</option>";
					}
					if ($attribs->get('map_building8_25') != "") {
						echo "<option value='building8_25'>".$attribs->get('map_building8_25')."</option>";
					}
					if ($attribs->get('map_building8_26') != "") {
						echo "<option value='building8_26'>".$attribs->get('map_building8_26')."</option>";
					}
					if ($attribs->get('map_building8_27') != "") {
						echo "<option value='building8_27'>".$attribs->get('map_building8_27')."</option>";
					}
					if ($attribs->get('map_building8_28') != "") {
						echo "<option value='building8_28'>".$attribs->get('map_building8_28')."</option>";
					}
					if ($attribs->get('map_building8_29') != "") {
						echo "<option value='building8_29'>".$attribs->get('map_building8_29')."</option>";
					}
					if ($attribs->get('map_building8_30') != "") {
						echo "<option value='building8_30'>".$attribs->get('map_building8_30')."</option>";
					}
					if ($attribs->get('map_building8_31') != "") {
						echo "<option value='building8_31'>".$attribs->get('map_building8_31')."</option>";
					}
					if ($attribs->get('map_building8_32') != "") {
						echo "<option value='building8_32'>".$attribs->get('map_building8_32')."</option>";
					}
					if ($attribs->get('map_building8_33') != "") {
						echo "<option value='building8_33'>".$attribs->get('map_building8_33')."</option>";
					}
					if ($attribs->get('map_building8_34') != "") {
						echo "<option value='building8_34'>".$attribs->get('map_building8_34')."</option>";
					}
					if ($attribs->get('map_building8_35') != "") {
						echo "<option value='building8_35'>".$attribs->get('map_building8_35')."</option>";
					}
					if ($attribs->get('map_building8_36') != "") {
						echo "<option value='building8_36'>".$attribs->get('map_building8_36')."</option>";
					}
					if ($attribs->get('map_building8_37') != "") {
						echo "<option value='building8_37'>".$attribs->get('map_building8_37')."</option>";
					}
					if ($attribs->get('map_building8_38') != "") {
						echo "<option value='building8_38'>".$attribs->get('map_building8_38')."</option>";
					}
					if ($attribs->get('map_building8_39') != "") {
						echo "<option value='building8_39'>".$attribs->get('map_building8_39')."</option>";
					}
					if ($attribs->get('map_building8_40') != "") {
						echo "<option value='building8_40'>".$attribs->get('map_building8_40')."</option>";
					}
					if ($attribs->get('map_building8_41') != "") {
						echo "<option value='building8_41'>".$attribs->get('map_building8_41')."</option>";
					}
					if ($attribs->get('map_building8_42') != "") {
						echo "<option value='building8_42'>".$attribs->get('map_building8_42')."</option>";
					}
					if ($attribs->get('map_building8_43') != "") {
						echo "<option value='building8_43'>".$attribs->get('map_building8_43')."</option>";
					}
					if ($attribs->get('map_building8_44') != "") {
						echo "<option value='building8_44'>".$attribs->get('map_building8_44')."</option>";
					}
					if ($attribs->get('map_building8_45') != "") {
						echo "<option value='building8_45'>".$attribs->get('map_building8_45')."</option>";
					}
					if ($attribs->get('map_building8_46') != "") {
						echo "<option value='building8_46'>".$attribs->get('map_building8_46')."</option>";
					}
					if ($attribs->get('map_building8_47') != "") {
						echo "<option value='building8_47'>".$attribs->get('map_building8_47')."</option>";
					}
					/*LOTA*/
					if ($attribs->get('map_lota_1') != "") {
						echo "<option value='lota_1'>".$attribs->get('map_lota_1')."</option>";
					}
					if ($attribs->get('map_lota_2') != "") {
						echo "<option value='lota_2'>".$attribs->get('map_lota_2')."</option>";
					}
					if ($attribs->get('map_lota_3') != "") {
						echo "<option value='lota_3'>".$attribs->get('map_lota_3')."</option>";
					}
					if ($attribs->get('map_lota_4') != "") {
						echo "<option value='lota_4'>".$attribs->get('map_lota_4')."</option>";
					}
					if ($attribs->get('map_lota_5') != "") {
						echo "<option value='lota_5'>".$attribs->get('map_lota_5')."</option>";
					}
					if ($attribs->get('map_lota_6') != "") {
						echo "<option value='lota_6'>".$attribs->get('map_lota_6')."</option>";
					}
					if ($attribs->get('map_lota_7') != "") {
						echo "<option value='lota_7'>".$attribs->get('map_lota_7')."</option>";
					}
					if ($attribs->get('map_lota_8') != "") {
						echo "<option value='lota_8'>".$attribs->get('map_lota_8')."</option>";
					}
					if ($attribs->get('map_lota_9') != "") {
						echo "<option value='lota_9'>".$attribs->get('map_lota_9')."</option>";
					}
					if ($attribs->get('map_lota_10') != "") {
						echo "<option value='lota_10'>".$attribs->get('map_lota_10')."</option>";
					}
					/*LOTB*/
					if ($attribs->get('map_lotb_1') != "") {
						echo "<option value='lotb_1'>".$attribs->get('map_lotb_1')."</option>";
					}
					if ($attribs->get('map_lotb_2') != "") {
						echo "<option value='lotb_2'>".$attribs->get('map_lotb_2')."</option>";
					}
					if ($attribs->get('map_lotb_3') != "") {
						echo "<option value='lotb_3'>".$attribs->get('map_lotb_3')."</option>";
					}
					if ($attribs->get('map_lotb_4') != "") {
						echo "<option value='lotb_4'>".$attribs->get('map_lotb_4')."</option>";
					}
					if ($attribs->get('map_lotb_5') != "") {
						echo "<option value='lotb_5'>".$attribs->get('map_lotb_5')."</option>";
					}
					if ($attribs->get('map_lotb_6') != "") {
						echo "<option value='lotb_6'>".$attribs->get('map_lotb_6')."</option>";
					}
					if ($attribs->get('map_lotb_7') != "") {
						echo "<option value='lotb_7'>".$attribs->get('map_lotb_7')."</option>";
					}
					if ($attribs->get('map_lotb_8') != "") {
						echo "<option value='lotb_8'>".$attribs->get('map_lotb_8')."</option>";
					}
					if ($attribs->get('map_lotb_9') != "") {
						echo "<option value='lotb_9'>".$attribs->get('map_lotb_9')."</option>";
					}
					if ($attribs->get('map_lotb_10') != "") {
						echo "<option value='lotb_10'>".$attribs->get('map_lotb_10')."</option>";
					}
					/*LOTC*/
					if ($attribs->get('map_lotc_1') != "") {
						echo "<option value='lotc_1'>".$attribs->get('map_lotc_1')."</option>";
					}
					if ($attribs->get('map_lotc_2') != "") {
						echo "<option value='lotc_2'>".$attribs->get('map_lotc_2')."</option>";
					}
					if ($attribs->get('map_lotc_3') != "") {
						echo "<option value='lotc_3'>".$attribs->get('map_lotc_3')."</option>";
					}
					if ($attribs->get('map_lotc_4') != "") {
						echo "<option value='lotc_4'>".$attribs->get('map_lotc_4')."</option>";
					}
					if ($attribs->get('map_lotc_5') != "") {
						echo "<option value='lotc_5'>".$attribs->get('map_lotc_5')."</option>";
					}
					if ($attribs->get('map_lotc_6') != "") {
						echo "<option value='lotc_6'>".$attribs->get('map_lotc_6')."</option>";
					}
					if ($attribs->get('map_lotc_7') != "") {
						echo "<option value='lotc_7'>".$attribs->get('map_lotc_7')."</option>";
					}
					if ($attribs->get('map_lotc_8') != "") {
						echo "<option value='lotc_8'>".$attribs->get('map_lotc_8')."</option>";
					}
					if ($attribs->get('map_lotc_9') != "") {
						echo "<option value='lotc_9'>".$attribs->get('map_lotc_9')."</option>";
					}
					if ($attribs->get('map_lotc_10') != "") {
						echo "<option value='lotc_10'>".$attribs->get('map_lotc_10')."</option>";
					}		
					if ($attribs->get('map_w4_1') != "") {
						echo "<option value='w4_1'>".$attribs->get('map_w4_1')."</option>";
					}
					if ($attribs->get('map_w4_2') != "") {
						echo "<option value='w4_2'>".$attribs->get('map_w4_2')."</option>";
					}
					if ($attribs->get('map_w4_3') != "") {
						echo "<option value='w4_3'>".$attribs->get('map_w4_3')."</option>";
					}
					if ($attribs->get('map_w4_4') != "") {
						echo "<option value='w4_4'>".$attribs->get('map_w4_4')."</option>";
					}
					if ($attribs->get('map_w4_5') != "") {
						echo "<option value='w4_5'>".$attribs->get('map_w4_5')."</option>";
					}
					if ($attribs->get('map_w4_6') != "") {
						echo "<option value='w4_6'>".$attribs->get('map_w4_6')."</option>";
					}
					if ($attribs->get('map_wh_1') != "") {
						echo "<option value='wh_1'>".$attribs->get('map_wh_1')."</option>";
					}
					if ($attribs->get('map_wh_2') != "") {
						echo "<option value='wh_2'>".$attribs->get('map_wh_2')."</option>";
					}
					if ($attribs->get('map_wh_3') != "") {
						echo "<option value='wh_3'>".$attribs->get('map_wh_3')."</option>";
					}
					if ($attribs->get('map_wh_4') != "") {
						echo "<option value='wh_4'>".$attribs->get('map_wh_4')."</option>";
					}
					if ($attribs->get('map_wh_5') != "") {
						echo "<option value='wh_5'>".$attribs->get('map_wh_5')."</option>";
					}
					if ($attribs->get('map_wh_6') != "") {
						echo "<option value='wh_6'>".$attribs->get('map_wh_6')."</option>";
					}
					if ($attribs->get('map_wh_7') != "") {
						echo "<option value='wh_7'>".$attribs->get('map_wh_7')."</option>";
					}
					if ($attribs->get('map_wh_8') != "") {
						echo "<option value='wh_8'>".$attribs->get('map_wh_8')."</option>";
					}
					if ($attribs->get('map_wh_9') != "") {
						echo "<option value='wh_9'>".$attribs->get('map_wh_9')."</option>";
					}
					if ($attribs->get('map_wh_10') != "") {
						echo "<option value='wh_10'>".$attribs->get('map_wh_10')."</option>";
					}
					if ($attribs->get('map_wh_11') != "") {
						echo "<option value='wh_11'>".$attribs->get('map_wh_11')."</option>";
					}
					if ($attribs->get('map_wh_12') != "") {
						echo "<option value='wh_12'>".$attribs->get('map_wh_12')."</option>";
					}
					if ($attribs->get('map_wh_13') != "") {
						echo "<option value='wh_13'>".$attribs->get('map_wh_13')."</option>";
					}
					if ($attribs->get('map_wh_14') != "") {
						echo "<option value='wh_14'>".$attribs->get('map_wh_14')."</option>";
					}
					if ($attribs->get('map_w1_1_g') != "") {
						echo "<option value='w1_1_g'>".$attribs->get('map_w1_1_g')."</option>";
					}
					if ($attribs->get('map_w1_2_g') != "") {
						echo "<option value='w1_2_g'>".$attribs->get('map_w1_2_g')."</option>";
					}
					if ($attribs->get('map_w1_3_g') != "") {
						echo "<option value='w1_3_g'>".$attribs->get('map_w1_3_g')."</option>";
					}
					if ($attribs->get('map_w1_4_g') != "") {
						echo "<option value='w1_4_g'>".$attribs->get('map_w1_4_g')."</option>";
					}
					if ($attribs->get('map_w1_5_g') != "") {
						echo "<option value='w1_5_g'>".$attribs->get('map_w1_5_g')."</option>";
					}
					if ($attribs->get('map_w1_6_g') != "") {
						echo "<option value='w1_6_g'>".$attribs->get('map_w1_6_g')."</option>";
					}
					if ($attribs->get('map_w1_7_g') != "") {
						echo "<option value='w1_7_g'>".$attribs->get('map_w1_7_g')."</option>";
					}
					if ($attribs->get('map_w1_8_g') != "") {
						echo "<option value='w1_8_g'>".$attribs->get('map_w1_8_g')."</option>";
					}
					if ($attribs->get('map_w1_1_1st') != "") {
						echo "<option value='w1_1_1st'>".$attribs->get('map_w1_1_1st')."</option>";
					}
					if ($attribs->get('map_w1_2_1st') != "") {
						echo "<option value='w1_2_1st'>".$attribs->get('map_w1_2_1st')."</option>";
					}
					if ($attribs->get('map_w1_3_1st') != "") {
						echo "<option value='w1_3_1st'>".$attribs->get('map_w1_3_1st')."</option>";
					}
					if ($attribs->get('map_w1_4_1st') != "") {
						echo "<option value='w1_4_1st'>".$attribs->get('map_w1_4_1st')."</option>";
					}
					if ($attribs->get('map_w1_5_1st') != "") {
						echo "<option value='w1_5_1st'>".$attribs->get('map_w1_5_1st')."</option>";
					}
					if ($attribs->get('map_w1_6_1st') != "") {
						echo "<option value='w1_6_1st'>".$attribs->get('map_w1_6_1st')."</option>";
					}
					if ($attribs->get('map_w1_7_1st') != "") {
						echo "<option value='w1_7_1st'>".$attribs->get('map_w1_7_1st')."</option>";
					}
					if ($attribs->get('map_w1_8_1st') != "") {
						echo "<option value='w1_8_1st'>".$attribs->get('map_w1_8_1st')."</option>";
					}
					if ($attribs->get('map_w1_9_1st') != "") {
						echo "<option value='w1_9_1st'>".$attribs->get('map_w1_9_1st')."</option>";
					}
					if ($attribs->get('map_w1_10_1st') != "") {
						echo "<option value='w1_10_1st'>".$attribs->get('map_w1_10_1st')."</option>";
					}
					if ($attribs->get('map_w1_11_1st') != "") {
						echo "<option value='w1_11_1st'>".$attribs->get('map_w1_11_1st')."</option>";
					}
					if ($attribs->get('map_w3_1_g') != "") {
						echo "<option value='w3_1_g'>".$attribs->get('map_w3_1_g')."</option>";
					}
					if ($attribs->get('map_w3_2_g') != "") {
						echo "<option value='w3_2_g'>".$attribs->get('map_w3_2_g')."</option>";
					}
					if ($attribs->get('map_w3_3_g') != "") {
						echo "<option value='w3_3_g'>".$attribs->get('map_w3_3_g')."</option>";
					}
					if ($attribs->get('map_w3_4_g') != "") {
						echo "<option value='w3_4_g'>".$attribs->get('map_w3_4_g')."</option>";
					}
					if ($attribs->get('map_w3_5_g') != "") {
						echo "<option value='w3_5_g'>".$attribs->get('map_w3_5_g')."</option>";
					}
					if ($attribs->get('map_w3_6_g') != "") {
						echo "<option value='w3_6_g'>".$attribs->get('map_w3_6_g')."</option>";
					}
					if ($attribs->get('map_w3_7_g') != "") {
						echo "<option value='w3_7_g'>".$attribs->get('map_w3_7_g')."</option>";
					}
					if ($attribs->get('map_w3_8_g') != "") {
						echo "<option value='w3_8_g'>".$attribs->get('map_w3_8_g')."</option>";
					}
					if ($attribs->get('map_w3_1_1st') != "") {
						echo "<option value='w3_1_1st'>".$attribs->get('map_w3_1_1st')."</option>";
					}
					if ($attribs->get('map_w3_2_1st') != "") {
						echo "<option value='w3_2_1st'>".$attribs->get('map_w3_2_1st')."</option>";
					}
					if ($attribs->get('map_w3_3_1st') != "") {
						echo "<option value='w3_3_1st'>".$attribs->get('map_w3_3_1st')."</option>";
					}
					if ($attribs->get('map_w3_4_1st') != "") {
						echo "<option value='w3_4_1st'>".$attribs->get('map_w3_4_1st')."</option>";
					}
					if ($attribs->get('map_w3_5_1st') != "") {
						echo "<option value='w3_5_1st'>".$attribs->get('map_w3_5_1st')."</option>";
					}
					if ($attribs->get('map_w3_6_1st') != "") {
						echo "<option value='w3_6_1st'>".$attribs->get('map_w3_6_1st')."</option>";
					}
					if ($attribs->get('map_w3_7_1st') != "") {
						echo "<option value='w3_7_1st'>".$attribs->get('map_w3_7_1st')."</option>";
					}
					if ($attribs->get('map_w3_8_1st') != "") {
						echo "<option value='w3_8_1st'>".$attribs->get('map_w3_8_1st')."</option>";
					}
					if ($attribs->get('map_w3_9_1st') != "") {
						echo "<option value='w3_9_1st'>".$attribs->get('map_w3_9_1st')."</option>";
					}
					if ($attribs->get('map_w3_10_1st') != "") {
						echo "<option value='w3_10_1st'>".$attribs->get('map_w3_10_1st')."</option>";
					}
					if ($attribs->get('map_w3_11_1st') != "") {
						echo "<option value='w3_11_1st'>".$attribs->get('map_w3_11_1st')."</option>";
					}
					if ($attribs->get('map_bdb_1_g') != "") {
						echo "<option value='bdb_1_g'>".$attribs->get('map_bdb_1_g')."</option>";
					}
					if ($attribs->get('map_bdb_2_g') != "") {
						echo "<option value='bdb_2_g'>".$attribs->get('map_bdb_2_g')."</option>";
					}
					if ($attribs->get('map_bdb_3_g') != "") {
						echo "<option value='bdb_3_g'>".$attribs->get('map_bdb_3_g')."</option>";
					}
					if ($attribs->get('map_bdb_4_g') != "") {
						echo "<option value='bdb_4_g'>".$attribs->get('map_bdb_4_g')."</option>";
					}
					if ($attribs->get('map_bdb_5_g') != "") {
						echo "<option value='bdb_5_g'>".$attribs->get('map_bdb_5_g')."</option>";
					}
					if ($attribs->get('map_bdb_6_g') != "") {
						echo "<option value='bdb_6_g'>".$attribs->get('map_bdb_6_g')."</option>";
					}
					if ($attribs->get('map_bdb_7_g') != "") {
						echo "<option value='bdb_7_g'>".$attribs->get('map_bdb_7_g')."</option>";
					}
					if ($attribs->get('map_bdb_8_g') != "") {
						echo "<option value='bdb_8_g'>".$attribs->get('map_bdb_8_g')."</option>";
					}
					if ($attribs->get('map_bdb_9_g') != "") {
						echo "<option value='bdb_9_g'>".$attribs->get('map_bdb_9_g')."</option>";
					}
					if ($attribs->get('map_bdb_10_g') != "") {
						echo "<option value='bdb_10_g'>".$attribs->get('map_bdb_10_g')."</option>";
					}
					if ($attribs->get('map_bdb_11_g') != "") {
						echo "<option value='bdb_11_g'>".$attribs->get('map_bdb_11_g')."</option>";
					}
					if ($attribs->get('map_bdb_12_g') != "") {
						echo "<option value='bdb_12_g'>".$attribs->get('map_bdb_12_g')."</option>";
					}
					if ($attribs->get('map_bdb_13_g') != "") {
						echo "<option value='bdb_13_g'>".$attribs->get('map_bdb_13_g')."</option>";
					}
					if ($attribs->get('map_bdb_14_g') != "") {
						echo "<option value='bdb_14_g'>".$attribs->get('map_bdb_14_g')."</option>";
					}
					if ($attribs->get('map_bdb_15_g') != "") {
						echo "<option value='bdb_15_g'>".$attribs->get('map_bdb_15_g')."</option>";
					}
					if ($attribs->get('map_bdb_1_1st') != "") {
						echo "<option value='bdb_1_1st'>".$attribs->get('map_bdb_1_1st')."</option>";
					}
					if ($attribs->get('map_bdb_2_1st') != "") {
						echo "<option value='bdb_2_1st'>".$attribs->get('map_bdb_2_1st')."</option>";
					}
					if ($attribs->get('map_bdb_3_1st') != "") {
						echo "<option value='bdb_3_1st'>".$attribs->get('map_bdb_3_1st')."</option>";
					}
					if ($attribs->get('map_bdb_4_1st') != "") {
						echo "<option value='bdb_4_1st'>".$attribs->get('map_bdb_4_1st')."</option>";
					}
					if ($attribs->get('map_bdb_5_1st') != "") {
						echo "<option value='bdb_5_1st'>".$attribs->get('map_bdb_5_1st')."</option>";
					}
					if ($attribs->get('map_bdb_6_1st') != "") {
						echo "<option value='bdb_6_1st'>".$attribs->get('map_bdb_6_1st')."</option>";
					}
					if ($attribs->get('map_bdb_7_1st') != "") {
						echo "<option value='bdb_7_1st'>".$attribs->get('map_bdb_7_1st')."</option>";
					}
					if ($attribs->get('map_bdb_8_1st') != "") {
						echo "<option value='bdb_8_1st'>".$attribs->get('map_bdb_8_1st')."</option>";
					}
					if ($attribs->get('map_bdb_9_1st') != "") {
						echo "<option value='bdb_9_1st'>".$attribs->get('map_bdb_9_1st')."</option>";
					}
					if ($attribs->get('map_bdb_10_1st') != "") {
						echo "<option value='bdb_10_1st'>".$attribs->get('map_bdb_10_1st')."</option>";
					}
					if ($attribs->get('map_bdb_11_1st') != "") {
						echo "<option value='bdb_11_1st'>".$attribs->get('map_bdb_11_1st')."</option>";
					}
					if ($attribs->get('map_bdb_12_1st') != "") {
						echo "<option value='bdb_12_1st'>".$attribs->get('map_bdb_12_1st')."</option>";
					}
					if ($attribs->get('map_bdb_13_1st') != "") {
						echo "<option value='bdb_13_1st'>".$attribs->get('map_bdb_13_1st')."</option>";
					}
					if ($attribs->get('map_bdb_14_1st') != "") {
						echo "<option value='bdb_14_1st'>".$attribs->get('map_bdb_14_1st')."</option>";
					}
					if ($attribs->get('map_bdb_15_1st') != "") {
						echo "<option value='bdb_15_1st'>".$attribs->get('map_bdb_15_1st')."</option>";
					}
					if ($attribs->get('map_bdb_16_1st') != "") {
						echo "<option value='bdb_16_1st'>".$attribs->get('map_bdb_16_1st')."</option>";
					}
				?>
			</select>
			<br/>
			<button id="back-button" class="btn btn-primary" style="display:none"><i class="glyphicon glyphicon-arrow-left" ></i>  Back</button>

			<div class="bbic-map">
				<div class="campus">
					<i class="campus_building_building8 map-popover cursor-pointer" data-content="Building 8" data-trigger="hover" data-placement="top" data-container="body" data-toggle="popover"></i>
					<i class="campus_building_w1 cursor-pointer map-popover" data-content="W1" data-trigger="hover" data-placement="top" data-container="body" data-toggle="popover"></i>
					<i class="campus_building_bdb cursor-pointer map-popover" data-content="BDB Building" data-trigger="hover" data-placement="top" data-container="body" data-toggle="popover"></i>
					<i class="campus_building_w3 cursor-pointer map-popover" data-content="W3" data-trigger="hover" data-placement="top" data-container="body" data-toggle="popover"></i>
					<i class="campus_building_w4 cursor-pointer map-popover" data-content="W4" data-trigger="hover" data-placement="top" data-container="body" data-toggle="popover"></i>
					<i class="campus_building_a1 cursor-pointer map-popover" data-content="A1" data-trigger="hover" data-placement="top" data-container="body" data-toggle="popover"></i>
					<i class="campus_building_a2 cursor-pointer map-popover" data-content="A2" data-trigger="hover" data-placement="top" data-container="body" data-toggle="popover"></i>
					<i class="campus_building_a3 cursor-pointer map-popover" data-content="A3" data-trigger="hover" data-placement="top" data-container="body" data-toggle="popover"></i>
					<i class="campus_building_b1 cursor-pointer map-popover" data-content="B1" data-trigger="hover" data-placement="top" data-container="body" data-toggle="popover"></i>
					<i class="campus_building_b2 cursor-pointer map-popover" data-content="B2" data-trigger="hover" data-placement="top" data-container="body" data-toggle="popover"></i>
					<i class="campus_building_b3 cursor-pointer map-popover" data-content="B3" data-trigger="hover" data-placement="top" data-container="body" data-toggle="popover"></i>
					<i class="campus_building_c1 cursor-pointer map-popover" data-content="C1" data-trigger="hover" data-placement="top" data-container="body" data-toggle="popover"></i>
					<i class="campus_building_c2 cursor-pointer map-popover" data-content="C2" data-trigger="hover" data-placement="top" data-container="body" data-toggle="popover"></i>
					<i class="campus_building_c3 cursor-pointer map-popover" data-content="C3" data-trigger="hover" data-placement="top" data-container="body" data-toggle="popover"></i>
					<i class="campus_building_wh cursor-pointer map-popover" data-content="Warehouses" data-trigger="hover" data-placement="top" data-container="body" data-toggle="popover"></i>
					<!-- <i class="campus_building_d2"></i>
					<i class="campus_building_d1"></i>
					<i class="campus_building_d2"></i>
					<i class="campus_building_d3"></i>
					<i class="campus_building_e1"></i>
					<i class="campus_building_e2"></i>
					<i class="campus_building_e3"></i>
					<i class="campus_building_e5"></i>
					<i class="campus_building_e6"></i>
					<i class="campus_building_e7"></i>
					<i class="campus_building_1e"></i>
					<i class="campus_building_2e"></i>
					<i class="campus_building_3e"></i>
					<i class="campus_building_4e"></i> -->
					<i class="campus_lot8"></i>
					<i class="campus_lotw"></i>
					<i class="campus_lota"></i>
					<i class="campus_lotb"></i>
					<i class="campus_lotc"></i>
					<i class="campus_lotd"></i>
					<i class="campus_lote1"></i>
					<i class="campus_lote2"></i>
					<i class="campus_lote3"></i>
					<i class="campus_border"></i>
					<i class="campus_trees"></i>
					<i class="campus_roads"></i>
					<i class="campus_carspaces"></i>

				</div>

				<div class="building-map building8">
					<!-- <i class="main-building-1"><i></i></i> -->
					<i class="building8_outline"></i>
					<i class="building8_reception"></i>
					<div class="map-popover building8 building8_1" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_1'); ?>">16</div>
					<i class="map-popover building8 building8_2" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_2'); ?>">18</i>
					<i class="map-popover building8 building8_3" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_3'); ?>">20</i>
					<i class="map-popover building8 building8_4" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_4'); ?>">22</i>
					<i class="map-popover building8 building8_5" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_5'); ?>">24</i>
					<i class="map-popover building8 building8_6" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_6'); ?>">26</i>
					<i class="map-popover building8 building8_7" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_7'); ?>">28</i>
					<i class="map-popover building8 building8_8" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_8'); ?>">30</i>
					<i class="map-popover building8 building8_9" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_9'); ?>"><span class="text-bottom">32</span></i>
					<i class="map-popover building8 building8_10" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_10'); ?>">34</i>
					<i class="map-popover building8 building8_11" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_11'); ?>">36</i>
					<i class="map-popover building8 building8_12" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_12'); ?>">38</i>
					<i class="map-popover building8 building8_13" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_13'); ?>">40</i>
					<i class="map-popover building8 building8_14" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_14'); ?>">42</i>
					<i class="map-popover building8 building8_15" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_15'); ?>"><span class="text-bottom">44</span></i>
					<i class="map-popover building8 building8_16" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_16'); ?>">14</i>
					<i class="map-popover building8 building8_17" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_17'); ?>">12</i>
					<i class="map-popover building8 building8_18" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_18'); ?>">10</i>
					<!-- <i class="map-popover building8 building8_19" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_19'); ?>">10</i> -->
					<i class="map-popover building8 building8_20" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_20'); ?>">6</i>
					<i class="map-popover building8 building8_21" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_21'); ?>">4</i>
					<i class="map-popover building8 building8_22" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_22'); ?>">2</i>
					<i class="map-popover building8 building8_23 text-small" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_23'); ?>">77</i>
					<i class="map-popover building8 building8_24 text-small" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_24'); ?>">39</i>
					<i class="map-popover building8 building8_25 text-small" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_25'); ?>">41</i>
					<i class="map-popover building8 building8_26" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_26'); ?>">78</i>
					<i class="map-popover building8 building8_27" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_27'); ?>">11</i>
					<i class="map-popover building8 building8_28" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_28'); ?>">55</i>
					<i class="map-popover building8 building8_29" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_29'); ?>">9</i>
					<i class="map-popover building8 building8_30" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_30'); ?>">57</i>
					<i class="map-popover building8 building8_31" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_31'); ?>"></i>
					<i class="map-popover building8 building8_32 text-small" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_32'); ?>">BBIC Admin</i>
					<i class="map-popover building8 building8_33" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_33'); ?>"></i>
					<i class="map-popover building8 building8_34" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_34'); ?>"></i>
					<i class="map-popover building8 building8_35" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_35'); ?>">33</i>
					<i class="map-popover building8 building8_36" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_36'); ?>">35</i>
					<i class="map-popover building8 building8_37" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_37'); ?>">37</i>
					<i class="map-popover building8 building8_38 text-small" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_38'); ?>">75</i>
					<i class="map-popover building8 building8_39 text-small" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_39'); ?>">76</i>
					<i class="map-popover building8 building8_40 text-small" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_40'); ?>">71</i>
					<i class="map-popover building8 building8_41 text-small" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_41'); ?>">69</i>
					<i class="map-popover building8 building8_42 text-small" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_42'); ?>">74</i>
					<i class="map-popover building8 building8_43 text-small" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_43'); ?>">72</i>
					<i class="map-popover building8 building8_44 text-small" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_44'); ?>">67</i>
					<i class="map-popover building8 building8_45 text-small" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_45'); ?>">73</i>
					<i class="map-popover building8 building8_46 text-small" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_46'); ?>">43</i>
					<i class="map-popover building8 building8_47 text-small" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_47'); ?>"><span class="text-bottom">46</span></i>
				</div>
				<div class="building-map lota a1 a2 a3">
					<i class="map-popover lota_1" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_lota_1'); ?>">135</i>
					<i class="map-popover lota_2" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_lota_2'); ?>">136</i>
					<i class="map-popover lota_3" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_lota_3'); ?>">137</i>
					<i class="map-popover lota_4" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_lota_4'); ?>">138</i>
					<i class="map-popover lota_5" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_lota_5'); ?>"><span class="text-bottom text-center">134</span></i>
					<i class="map-popover lota_6" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_lota_6'); ?>">132</i>
					<i class="map-popover lota_7" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_lota_7'); ?>">128</i>
					<i class="map-popover lota_8" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_lota_8'); ?>">129</i>
					<i class="map-popover lota_9" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_lota_9'); ?>">130</i>
					<i class="map-popover lota_10" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_lota_10'); ?>">131</i>
					<i class="lota_outline"></i>
				</div>
				<div class="building-map lotb b1 b2 b3">
					<i class="map-popover lotb_1" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_lotb_1'); ?>">145</i>
					<i class="map-popover lotb_2" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_lotb_2'); ?>">146</i>
					<i class="map-popover lotb_3" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_lotb_3'); ?>">147</i>
					<i class="map-popover lotb_4" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_lotb_4'); ?>">148</i>
					<i class="map-popover lotb_5" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_lotb_5'); ?>"><span class="text-bottom text-center">144</span></i>
					<i class="map-popover lotb_6" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_lotb_6'); ?>">143</i>
					<i class="map-popover lotb_7" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_lotb_7'); ?>">139</i>
					<i class="map-popover lotb_8" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_lotb_8'); ?>">140</i>
					<i class="map-popover lotb_9" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_lotb_9'); ?>">141</i>
					<i class="map-popover lotb_10" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_lotb_10'); ?>">142</i>
					<i class="lotb_outline"></i>
				</div>
				<div class="building-map lotc c1 c2 c3">
					<i class="map-popover lotc_1" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_lotc_1'); ?>">154</i>
					<i class="map-popover lotc_2" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_lotc_2'); ?>">155</i>
					<i class="map-popover lotc_3" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_lotc_3'); ?>">156</i>
					<i class="map-popover lotc_4" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_lotc_4'); ?>">157</i>
					<i class="map-popover lotc_5" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_lotc_5'); ?>">153</i>
					<i class="map-popover lotc_6" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_lotc_6'); ?>">133</i>
					<i class="map-popover lotc_7" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_lotc_7'); ?>">149</i>
					<i class="map-popover lotc_8" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_lotc_8'); ?>">150</i>
					<i class="map-popover lotc_9" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_lotc_9'); ?>">151</i>
					<i class="map-popover lotc_10" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_lotc_10'); ?>">152</i>
					<i class="lotc_outline"></i>
				</div>
				<div class="building-map w1">
					<i class="map-popover w1_1_g" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_w1_1_g'); ?>">12</i>
					<i class="map-popover w1_2_g" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_w1_2_g'); ?>">14</i>
					<i class="map-popover w1_3_g" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_w1_3_g'); ?>">16</i>
					<i class="map-popover w1_4_g" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_w1_4_g'); ?>">18</i>
					<i class="map-popover w1_5_g" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_w1_5_g'); ?>">20</i>
					<i class="map-popover w1_6_g" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_w1_6_g'); ?>">22</i>
					<i class="map-popover w1_7_g" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_w1_7_g'); ?>">27</i>
					<i class="map-popover w1_8_g" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_w1_8_g'); ?>">24</i>
					<i class="map-popover w1_1_1st" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_w1_1_1st'); ?>">118</i>
					<i class="map-popover w1_2_1st" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_w1_2_1st'); ?>">120</i>
					<i class="map-popover w1_3_1st" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_w1_3_1st'); ?>">121</i>
					<i class="map-popover w1_4_1st" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_w1_4_1st'); ?>">122</i>
					<i class="map-popover w1_5_1st" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_w1_5_1st'); ?>">123</i>
					<i class="map-popover w1_6_1st" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_w1_6_1st'); ?>">124</i>
					<i class="map-popover w1_7_1st" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_w1_7_1st'); ?>">116</i>
					<i class="map-popover w1_8_1st" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_w1_8_1st'); ?>">114</i>
					<i class="map-popover w1_9_1st" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_w1_9_1st'); ?>">112</i>
					<i class="map-popover w1_10_1st" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_w1_10_1st'); ?>">110</i>
					<i class="map-popover w1_11_1st" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_w1_11_1st'); ?>">109</i>
					<i class="w1_outline_1st"></i>
					<i class="w1_outline_g"></i>
				</div>
				<div class="building-map w3">
					<i class="map-popover w3_1_g" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_w3_1_g'); ?>">29</i>
					<i class="map-popover w3_2_g" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_w3_2_g'); ?>">30</i>
					<i class="map-popover w3_3_g" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_w3_3_g'); ?>">31</i>
					<i class="map-popover w3_4_g" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_w3_4_g'); ?>">32</i>
					<i class="map-popover w3_5_g" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_w3_5_g'); ?>">33</i>
					<i class="map-popover w3_6_g" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_w3_6_g'); ?>">34</i>
					<i class="map-popover w3_7_g" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_w3_7_g'); ?>">35</i>
					<i class="map-popover w3_8_g" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_w3_8_g'); ?>">37</i>
					<i class="map-popover w3_1_1st" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_w3_1_1st'); ?>">5</i>
					<i class="map-popover w3_2_1st" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_w3_2_1st'); ?>">6</i>
					<i class="map-popover w3_3_1st" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_w3_3_1st'); ?>">7</i>
					<i class="map-popover w3_4_1st" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_w3_4_1st'); ?>">8</i>
					<i class="map-popover w3_5_1st" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_w3_5_1st'); ?>">4</i>
					<i class="map-popover w3_6_1st" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_w3_6_1st'); ?>">3</i>
					<i class="map-popover w3_7_1st" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_w3_7_1st'); ?>">2</i>
					<i class="map-popover w3_8_1st" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_w3_8_1st'); ?>">1</i>
					<i class="w3_outline"></i>
					<i class="w3_outline_1st"></i>
				</div>

				<div class="building-map bdb">
					<i class="map-popover bdb_1_g" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_bdb_1_g'); ?>">3</i>
					<i class="map-popover bdb_2_g" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_bdb_2_g'); ?>">4</i>
					<i class="map-popover bdb_3_g" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_bdb_3_g'); ?>">6</i>
					<i class="map-popover bdb_4_g" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_bdb_4_g'); ?>">8</i>
					<i class="map-popover bdb_5_g" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_bdb_5_g'); ?>"></i>
					<i class="map-popover bdb_6_g" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_bdb_6_g'); ?>"></i>
					<i class="map-popover bdb_7_g" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_bdb_7_g'); ?>"></i>
					<i class="map-popover bdb_8_g" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_bdb_8_g'); ?>"></i>
					<i class="map-popover bdb_9_g" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_bdb_9_g'); ?>"></i>
					<i class="map-popover bdb_10_g" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_bdb_10_g'); ?>"></i>
					<i class="map-popover bdb_11_g" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_bdb_11_g'); ?>"></i>
					<i class="map-popover bdb_12_g" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_bdb_12_g'); ?>"></i>
					<i class="map-popover bdb_13_g" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_bdb_13_g'); ?>"></i>
					<i class="map-popover bdb_14_g" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_bdb_14_g'); ?>"></i>
					<i class="map-popover bdb_15_g" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_bdb_15_g'); ?>">Bank</i>
					<i class="map-popover bdb_1_1st" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_bdb_1_1st'); ?>">103</i>
					<i class="map-popover bdb_2_1st" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_bdb_2_1st'); ?>">104</i>
					<i class="map-popover bdb_3_1st" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_bdb_3_1st'); ?>">105</i>
					<i class="map-popover bdb_4_1st" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_bdb_4_1st'); ?>">106</i>
					<i class="map-popover bdb_5_1st" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_bdb_5_1st'); ?>">108</i>
					<i class="map-popover bdb_6_1st" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_bdb_6_1st'); ?>"><span class="text-small">115</span></i>
					<i class="map-popover bdb_7_1st" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_bdb_7_1st'); ?>"><span class="text-small">117</span></i>
					<i class="map-popover bdb_8_1st" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_bdb_8_1st'); ?>">113</i>
					<i class="map-popover bdb_9_1st" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_bdb_9_1st'); ?>">111</i>
					<i class="map-popover bdb_10_1st" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_bdb_10_1st'); ?>">107</i>
					<i class="map-popover bdb_11_1st" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_bdb_11_1st'); ?>">1</i>
					<i class="map-popover bdb_12_1st" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_bdb_12_1st'); ?>">2</i>
					<i class="map-popover bdb_13_1st" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_bdb_13_1st'); ?>">3</i>
					<i class="map-popover bdb_14_1st" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_bdb_14_1st'); ?>">4</i>
					<i class="map-popover bdb_15_1st" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_bdb_15_1st'); ?>">5</i>
					<i class="map-popover bdb_16_1st" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_bdb_16_1st'); ?>">6</i>
					<i class="bdb_outline_g"></i>
					<i class="bdb_outline_1st"></i>
				</div>
				<div class="building-map w4">
					<i class="map-popover w4_1" data-container="body" data-toggle="popover" data-placement="top" 
							data-content="<?php echo $attribs->get('map_w4_1'); ?>">42</i>
					<i class="map-popover w4_2" data-container="body" data-toggle="popover" data-placement="top" 
							data-content="<?php echo $attribs->get('map_w4_2'); ?>">44</i>
					<i class="map-popover w4_3" data-container="body" data-toggle="popover" data-placement="top" 
							data-content="<?php echo $attribs->get('map_w4_3'); ?>">46</i>
					<i class="map-popover w4_4" data-container="body" data-toggle="popover" data-placement="top" 
							data-content="<?php echo $attribs->get('map_w4_4'); ?>">48</i>
					<i class="map-popover w4_5" data-container="body" data-toggle="popover" data-placement="top" 
							data-content="<?php echo $attribs->get('map_w4_5'); ?>">101</i>
					<i class="map-popover w4_6" data-container="body" data-toggle="popover" data-placement="top" 
							data-content="<?php echo $attribs->get('map_w4_6'); ?>">102</i>
					<i class="w4_outline"></i>
				</div>
				<div class="building-map wh">
					<i class="map-popover wh_1" data-container="body" data-toggle="popover" data-placement="top" 
							data-content="<?php echo $attribs->get('map_wh_1'); ?>">87</i>
					<i class="map-popover wh_2" data-container="body" data-toggle="popover" data-placement="top" 
							data-content="<?php echo $attribs->get('map_wh_2'); ?>">89</i>
					<i class="map-popover wh_3" data-container="body" data-toggle="popover" data-placement="top" 
							data-content="<?php echo $attribs->get('map_wh_3'); ?>">95</i>
					<i class="map-popover wh_4" data-container="body" data-toggle="popover" data-placement="top" 
							data-content="<?php echo $attribs->get('map_wh_4'); ?>">97</i>
					<i class="map-popover wh_5" data-container="body" data-toggle="popover" data-placement="top" 
							data-content="<?php echo $attribs->get('map_wh_5'); ?>">106</i>
					<i class="map-popover wh_6" data-container="body" data-toggle="popover" data-placement="top" 
							data-content="<?php echo $attribs->get('map_wh_6'); ?>">108</i>
					<i class="map-popover wh_7" data-container="body" data-toggle="popover" data-placement="top" 
							data-content="<?php echo $attribs->get('map_wh_7'); ?>">114</i>
					<i class="map-popover wh_8" data-container="body" data-toggle="popover" data-placement="top" 
							data-content="<?php echo $attribs->get('map_wh_8'); ?>">102</i>
					<i class="map-popover wh_9" data-container="body" data-toggle="popover" data-placement="top" 
							data-content="<?php echo $attribs->get('map_wh_9'); ?>">104</i>
					<i class="map-popover wh_10" data-container="body" data-toggle="popover" data-placement="top" 
							data-content="<?php echo $attribs->get('map_wh_10'); ?>">110</i>
					<i class="map-popover wh_11" data-container="body" data-toggle="popover" data-placement="top" 
							data-content="<?php echo $attribs->get('map_wh_11'); ?>">112</i>
					<i class="map-popover wh_12" data-container="body" data-toggle="popover" data-placement="top" 
							data-content="<?php echo $attribs->get('map_wh_12'); ?>">85</i>
					<i class="map-popover wh_13" data-container="body" data-toggle="popover" data-placement="top" 
							data-content="<?php echo $attribs->get('map_wh_13'); ?>">91</i>
					<i class="map-popover wh_14" data-container="body" data-toggle="popover" data-placement="top" 
							data-content="<?php echo $attribs->get('map_wh_14'); ?>">93</i>
					<i class="wh_outline"></i>
				</div>
			</div>
		</section>

	  <!-- footer -->
	  <?php if ($botInfo) : ?>
	  <footer class="article-footer clearfix">
	    <?php echo JLayoutHelper::render('joomla.content.info_block.block', array('item' => $this->item, 'params' => $params, 'position' => 'below')); ?>
	  </footer>
	  <?php endif; ?>
	  <!-- //footer -->

		<?php
		if (!empty($this->item->pagination) && $this->item->pagination && $this->item->paginationposition && !$this->item->paginationrelative): ?>
			<?php
			echo '<hr class="divider-vertical" />';
			echo $this->item->pagination;
			?>
		<?php endif; ?>

		<?php if (isset($urls) && ((!empty($urls->urls_position) && ($urls->urls_position == '1')) || ($params->get('urls_position') == '1'))): ?>
			<?php echo $this->loadTemplate('links'); ?>
		<?php endif; ?>

		<?php //optional teaser intro text for guests ?>
	<?php elseif ($params->get('show_noauth') == true and  $user->get('guest')) : ?>

		<?php echo $this->item->introtext; ?>
		<?php //Optional link to let them register to see the whole article. ?>
		<?php if ($params->get('show_readmore') && $this->item->fulltext != null) :
			$link1 = JRoute::_('index.php?option=com_users&view=login');
			$link = new JURI($link1);
			?>
			<section class="readmore">
				<a href="<?php echo $link; ?>" itemprop="url">
							<span>
							<?php $attribs = json_decode($this->item->attribs); ?>
							<?php
							if ($attribs->alternative_readmore == null) :
								echo JText::_('COM_CONTENT_REGISTER_TO_READ_MORE');
							elseif ($readmore = $this->item->alternative_readmore) :
								echo $readmore;
								if ($params->get('show_readmore_title', 0) != 0) :
									echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
								endif;
							elseif ($params->get('show_readmore_title', 0) == 0) :
								echo JText::sprintf('COM_CONTENT_READ_MORE_TITLE');
							else :
								echo JText::_('COM_CONTENT_READ_MORE');
								echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
							endif; ?>
							</span>
				</a>
			</section>
		<?php endif; ?>
	<?php endif; ?>

	</article>
	<!-- //Article -->

	<?php if (!empty($this->item->pagination) && $this->item->pagination && $this->item->paginationposition && $this->item->paginationrelative): ?>
		<?php echo $this->item->pagination; ?>
	<?php endif; ?>

	<?php echo $this->item->event->afterDisplayContent; ?>
	</div>




<?php else : ?>
	<article itemscope itemtype="http://schema.org/Article">
		<meta itemprop="inLanguage" content="<?php echo ($this->item->language === '*') ? JFactory::getConfig()->get('language') : $this->item->language; ?>" />

	<?php if ($params->get('show_title')) : ?>
		<?php echo JLayoutHelper::render('joomla.content.item_title', array('item' => $this->item, 'params' => $params, 'title-tag'=>'h1')); ?>
	<?php endif; ?>

	<!-- Aside -->
	<?php if ($topInfo || $icons) : ?>
	<aside class="article-aside clearfix">
	  <?php if ($topInfo): ?>
	  <?php echo JLayoutHelper::render('joomla.content.info_block.block', array('item' => $this->item, 'params' => $params, 'position' => 'above')); ?>
	  <?php endif; ?>
	  
	  <?php if ($icons): ?>
	  <?php echo JLayoutHelper::render('joomla.content.icons', array('item' => $this->item, 'params' => $params, 'print' => $this->print)); ?>
	  <?php endif; ?>
	</aside>  
	<?php endif; ?>
	<!-- //Aside -->


	<?php if (isset ($this->item->toc)) : ?>
		<?php echo $this->item->toc; ?>
	<?php endif; ?>

	<?php if ($params->get('show_tags', 1) && !empty($this->item->tags)) : ?>
		<?php echo JLayoutHelper::render('joomla.content.tags', $this->item->tags->itemTags); ?>
	<?php endif; ?>

	<?php if (!$params->get('show_intro')) : ?>
		<?php echo $this->item->event->afterDisplayTitle; ?>
	<?php endif; ?>

	<?php echo $this->item->event->beforeDisplayContent; ?>

	<?php if (isset($urls) && ((!empty($urls->urls_position) && ($urls->urls_position == '0')) || ($params->get('urls_position') == '0' && empty($urls->urls_position))) || (empty($urls->urls_position) && (!$params->get('urls_position')))): ?>
		<?php echo $this->loadTemplate('links'); ?>
	<?php endif; ?>

	<?php	if ($params->get('access-view')): ?>

		<?php echo JLayoutHelper::render('joomla.content.fulltext_image', array('item' => $this->item, 'params' => $params)); ?>

		<?php	if (!empty($this->item->pagination) AND $this->item->pagination AND !$this->item->paginationposition AND !$this->item->paginationrelative):
			echo $this->item->pagination;
		endif; ?>

		<section class="article-content clearfix" itemprop="articleBody">
			<?php echo $this->item->text; ?>
		</section>

	  <!-- footer -->
	  <?php if ($botInfo) : ?>
	  <footer class="article-footer clearfix">
	    <?php echo JLayoutHelper::render('joomla.content.info_block.block', array('item' => $this->item, 'params' => $params, 'position' => 'below')); ?>
	  </footer>
	  <?php endif; ?>
	  <!-- //footer -->

		<?php
		if (!empty($this->item->pagination) && $this->item->pagination && $this->item->paginationposition && !$this->item->paginationrelative): ?>
			<?php
			echo '<hr class="divider-vertical" />';
			echo $this->item->pagination;
			?>
		<?php endif; ?>

		<?php if (isset($urls) && ((!empty($urls->urls_position) && ($urls->urls_position == '1')) || ($params->get('urls_position') == '1'))): ?>
			<?php echo $this->loadTemplate('links'); ?>
		<?php endif; ?>

		<?php //optional teaser intro text for guests ?>
	<?php elseif ($params->get('show_noauth') == true and  $user->get('guest')) : ?>

		<?php echo $this->item->introtext; ?>
		<?php //Optional link to let them register to see the whole article. ?>
		<?php if ($params->get('show_readmore') && $this->item->fulltext != null) :
			$link1 = JRoute::_('index.php?option=com_users&view=login');
			$link = new JURI($link1);
			?>
			<section class="readmore">
				<a href="<?php echo $link; ?>" itemprop="url">
							<span>
							<?php $attribs = json_decode($this->item->attribs); ?>
							<?php
							if ($attribs->alternative_readmore == null) :
								echo JText::_('COM_CONTENT_REGISTER_TO_READ_MORE');
							elseif ($readmore = $this->item->alternative_readmore) :
								echo $readmore;
								if ($params->get('show_readmore_title', 0) != 0) :
									echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
								endif;
							elseif ($params->get('show_readmore_title', 0) == 0) :
								echo JText::sprintf('COM_CONTENT_READ_MORE_TITLE');
							else :
								echo JText::_('COM_CONTENT_READ_MORE');
								echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
							endif; ?>
							</span>
				</a>
			</section>
		<?php endif; ?>
	<?php endif; ?>

	</article>
	<!-- //Article -->

	<?php if (!empty($this->item->pagination) && $this->item->pagination && $this->item->paginationposition && $this->item->paginationrelative): ?>
		<?php echo $this->item->pagination; ?>
	<?php endif; ?>

	<?php echo $this->item->event->afterDisplayContent; ?>
	</div>
<?php endif; ?>