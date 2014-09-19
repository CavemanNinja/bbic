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
				<option value="0" disabled selected> -- Select an business name -- </option>
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
				?>
			</select>
			<br/>
			<button id="back-button" class="btn btn-primary" style="display:none"><i class="glyphicon glyphicon-arrow-left" ></i>  Back</button>

			<div class="bbic-map">
				<div class="campus">
					<i class="campus_building_building8"></i>
					<i class="campus_building_1w"></i>
					<i class="campus_building_2w"></i>
					<i class="campus_building_3w"></i>
					<i class="campus_building_4w"></i>
					<i class="campus_building_a1"></i>
					<i class="campus_building_a2"></i>
					<i class="campus_building_a3"></i>
					<i class="campus_building_b1"></i>
					<i class="campus_building_b2"></i>
					<i class="campus_building_b3"></i>
					<i class="campus_building_c1"></i>
					<i class="campus_building_c2"></i>
					<i class="campus_building_c3"></i>
					<i class="campus_building_d2"></i>
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
					<i class="campus_building_4e"></i>
					<i class="campus_building_unnamed"></i>
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
				</div>

				<div class="building-map building8">
					<!-- <i class="main-building-1"><i></i></i> -->
					<i class="building8_outline"></i>
					<i class="building8_reception"></i>
					<div class="map-popover building8_1" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_1'); ?>"></div>
					<i class="map-popover building8_2" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_2'); ?>"></i>
					<i class="map-popover building8_3" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_3'); ?>"></i>
					<i class="map-popover building8_4" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_4'); ?>"></i>
					<i class="map-popover building8_5" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_1'); ?>"></i>
					<i class="map-popover building8_6" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_1'); ?>"></i>
					<i class="map-popover building8_7" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_1'); ?>"></i>
					<i class="map-popover building8_8" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_1'); ?>"></i>
					<i class="map-popover building8_9" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_1'); ?>"></i>
					<i class="map-popover building8_10" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_1'); ?>"></i>
					<i class="map-popover building8_11" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_1'); ?>"></i>
					<i class="map-popover building8_12" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_1'); ?>"></i>
					<i class="map-popover building8_13" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_1'); ?>"></i>
					<i class="map-popover building8_14" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_1'); ?>"></i>
					<i class="map-popover building8_15" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_1'); ?>"></i>
					<i class="map-popover building8_16" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_1'); ?>"></i>
					<i class="map-popover building8_17" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_1'); ?>"></i>
					<i class="map-popover building8_18" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_1'); ?>"></i>
					<i class="map-popover building8_19" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_1'); ?>"></i>
					<i class="map-popover building8_20" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_1'); ?>"></i>
					<i class="map-popover building8_21" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_1'); ?>"></i>
					<i class="map-popover building8_22" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_1'); ?>"></i>
					<i class="map-popover building8_23" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_1'); ?>"></i>
					<i class="map-popover building8_24" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_1'); ?>"></i>
					<i class="map-popover building8_25" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_1'); ?>"></i>
					<i class="map-popover building8_26" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_1'); ?>"></i>
					<i class="map-popover building8_27" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_1'); ?>"></i>
					<i class="map-popover building8_28" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_1'); ?>"></i>
					<i class="map-popover building8_29" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_1'); ?>"></i>
					<i class="map-popover building8_30" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_1'); ?>"></i>
					<i class="map-popover building8_31" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_1'); ?>"></i>
					<i class="map-popover building8_32" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_1'); ?>"></i>
					<i class="map-popover building8_33" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_1'); ?>"></i>
					<i class="map-popover building8_34" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_1'); ?>"></i>
					<i class="map-popover building8_35" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_building8_1'); ?>"></i>
				</div>
				<div class="building-map lota a1 a2 a3">
					<i class="map-popover lota_1" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_lota_1'); ?>"></i>
					<i class="map-popover lota_2" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_lota_2'); ?>"></i>
					<i class="map-popover lota_3" data-container="body" data-toggle="popover" data-placement="top" 
						data-content="<?php echo $attribs->get('map_lota_3'); ?>"></i>
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