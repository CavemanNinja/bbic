<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
JHtml::stylesheet(JUri::base().'templates/t3_bs3_blank/css/font-awesome.min.css', array(), true);

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

//TableSorter JQuery Plugin
JHtml::script(JUri::base().'templates/t3_bs3_blank/js/tableSorter/jquery.tablesorter.js', false, true);
JHtml::script(JUri::base().'templates/t3_bs3_blank/js/SimpleTableFilter.js', false, true);

// JHtml::script(JUri::base().'templates/t3_bs3_blank/js/jquery.watable.js', false, true);
// JHtml::stylesheet(JUri::base().'templates/t3_bs3_blank/css/watable.css', array(), true);

$document = JFactory::getDocument();
$document->addScriptDeclaration("
    jQuery(function(){
          jQuery('#mytable').tablesorter();
    });

    jQuery(function(){
        jQuery('#mytable').filterTable();
    });

");


// phpinfo();
// $this->state->set('filter.published', [0, 1, 2]);
// var_dump($this);
// var_dump($this->pagination->total);
// $this->state->set('list_limit', "0");

$myPageItems = $this->pagination->total;
$myPagesTotal = 0;

// Create some shortcuts.
$params = &$this->item->params;
$n = count($this->items);
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));

//Check for Tenant
$isTenant = in_array(10, array_values(JFactory::getUser()->groups));
$isStaff = in_array(11, array_values(JFactory::getUser()->groups));

$restrictView = false;

if ($this->items[0]) {
    $catid = $this->items[0]->catid;
    /*Get the parent category ID, used for News and Company Profiles */
    $categoriesModel = JCategories::getInstance('content');
    $category = $categoriesModel->get($catid);
    $parent = $category->getParent();
    $parentid = $parent->id;
    $restrictView = (($catid == 9) || $parentid == 9 || ($catid == 10) || ($catid == 12))  && $isTenant;


    //$noSubmit = $isTenant && ($catid == 9 || $parentid == 9);
    $noSubmit = $isStaff && ($catid == 12);
}


//Table header settings
$list_show_servicerequest_item = false;
$list_show_servicerequest_approval = false;
$list_show_billing_name = false;
$list_show_billing_status = false;
$list_show_billing_price = false;
$list_show_billing_type = false;
$list_show_companyprofile_approval = false;
$list_show_billing_repeating = false;
$list_show_companyprofile_language = false;
$list_show_companyprofile_title = false;
$list_show_servicerequest_tenant = false;
$list_show_news_heading = false;
$list_show_service_arabic_name = false;
$list_show_servicerequest_arabic_name = false;
$list_show_companyprofile_arabic = false;

/*Must list news parent and all subcategories !use parent cat instead*/

if ($catid == "8" ||  $catid == "14" ||  $catid == "15" ||
     $catid == "16" ||  $catid == "22" ||  $catid == "23" ||
      $catid == "24" || $catid == "25") {
    $list_show_news_heading = true;
    $list_show_category_title = true;
    // var_dump($this);
}

//IF COMPANY PROFILE
if ($catid == 9 || $parentid == 9) {
    $list_show_companyprofile_approval = true;
    $list_show_companyprofile_language = true;
    $list_show_companyprofile_title = true;
    if (JFactory::getLanguage()->get('tag') == "ar-AA") {
        $list_show_companyprofile_arabic = true;
    }
}

//IF CATEGORY IS SERVICE REQUEST OR BILL
if ($catid == 12 || 10) {
    $this->params->set("list_show_date", "created");
    $this->params->set("list_show_author", "0");
    $this->params->set("list_show_hits", "0");
}

//Service Requests
if ($catid == 12) {
    $list_show_servicerequest_item = true;
    $list_show_servicerequest_approval = true;
    $list_show_servicerequest_tenant = true;

    if (JFactory::getLanguage()->get('tag') == "ar-AA") {
        $list_show_servicerequest_arabic_name = true;
    }
}

//Billing
if ($catid == 10) {
    $list_show_billing_name = !$restrictView;
    $list_show_billing_status = true;
    $list_show_billing_price = true;
    $list_show_billing_type = true;
    $list_show_billing_repeating = true;
}

//Services 
if ($catid == 19 && JFactory::getLanguage()->get('tag') == "ar-AA") {
    $list_show_service_arabic_name = true;
}

// var_dump($this);

// Check for at least one editable article
$isEditable = false;

if (!empty($this->items))
{
    foreach ($this->items as $article)
    {
        if ($article->params->get('access-edit'))
        {
            $isEditable = true;
            break;
        }
    }
}


/*
    Make isEditable false for tenants when viewing Service Requests or Bills.
 */
if (($catid == 12 || $catid == 10) && $isTenant) {
    $isEditable = false;
}

/*
    Access Rules to manage whether a user can see show_no_articles
    based on groups and viewing access levels.
*/

$authorized = false;
$access = $this->menu->access;
$user_groups = JFactory::getUser()->groups;


// News
if ($access == 7 && !in_array(12, $user_groups)) {
    $this->params->set('show_no_articles', '0');
    $this->params->set('show_page_heading', 0);
}
// Company Profiles
if ($access == 8 && !in_array(13, $user_groups)) {
    $this->params->set('show_no_articles', '0');
    $this->params->set('show_page_heading', 0);
}
// Billing
if ($access == 9 && !in_array(14, $user_groups)) {
    $this->params->set('show_no_articles', '0');
    $this->params->set('show_page_heading', 0);
}
// Service Requests
if ($access == 11 && !in_array(15, $user_groups)) {
    $this->params->set('show_no_articles', '0');
    $this->params->set('show_page_heading', 0);
}
// Services
if ($access == 15 && !in_array(19, $user_groups)) {
    $this->params->set('show_no_articles', '0');
    $this->params->set('show_page_heading', '0');
}





// $this->params->set('show_page_heading', '0');
// var_dump($this);

?>

<?php if ($this->params->get('show_no_articles', 0)) : ?>

    <h1><?php echo $this->params->get('page_title'); ?></h1>

    <?php if (empty($this->items)) : ?>

        <?php if ($this->params->get('show_no_articles', 1)) : ?>
            <p><?php echo JText::_('COM_CONTENT_NO_ARTICLES'); ?></p>
        <?php endif; ?>

    <?php else : ?>

        <form action="<?php echo htmlspecialchars(JUri::getInstance()->toString()); ?>" method="post" name="adminForm" id="adminForm" class="form-inline">
            <?php if ($this->params->get('show_headings') || $this->params->get('filter_field') != 'hide' || $this->params->get('show_pagination_limit')) :?>
                <fieldset class="filters btn-toolbar clearfix">
                    <?php if ($this->params->get('filter_field') != 'hide') :?>
                        <div class="btn-group">
                            <label class="filter-search-lbl element-invisible" for="filter-search">
                                <?php echo JText::_('COM_CONTENT_'.$this->params->get('filter_field').'_FILTER_LABEL').'&#160;'; ?>
                            </label>
                            <input type="text" name="filter-search" id="filter-search" value="<?php echo $this->escape($this->state->get('list.filter')); ?>" class="inputbox" onchange="document.adminForm.submit();" title="<?php echo JText::_('COM_CONTENT_FILTER_SEARCH_DESC'); ?>" placeholder="<?php echo JText::_('COM_CONTENT_'.$this->params->get('filter_field').'_FILTER_LABEL'); ?>" />
                        </div>
                    <?php endif; ?>
                    <?php if ($this->params->get('show_pagination_limit')) : ?>
                        <div class="btn-group pull-right">
                            <label for="limit" class="element-invisible">
                                <?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>
                            </label>
                            <!-- <?php echo $this->pagination->getLimitBox(); ?> -->
                        </div>
                    <?php endif; ?>

                    <input type="hidden" name="filter_order" value="" />
                    <input type="hidden" name="filter_order_Dir" value="" />
                    <input type="hidden" name="limitstart" value="" />
                    <input type="hidden" name="task" value="" />
                </fieldset>
            <?php endif; ?>

            <!-- SET UP THE TABLE -->

            <table class="category table table-striped table-bordered table-hover tablesorter" id="mytable">
                <?php if ($this->params->get('show_headings')) : ?>
                    <thead>
                        <tr>
                            <th id="categorylist_header_title" data-sorter="false">
                                <?php if ($list_show_companyprofile_title == true) : ?>
                                    <?php if (JFactory::getLanguage()->get('tag') == "en-GB") : ?>
                                        <?php echo JHtml::_('grid.sort', 'Company Name', 'a.title', $listDirn, $listOrder); ?>
                                    <?php else : ?>
                                        <?php echo JHtml::_('grid.sort', 'اسم الشركة', 'a.title', $listDirn, $listOrder); ?>
                                    <?php endif; ?>
                                
                                <?php elseif ($list_show_news_heading == true) :?>
                                    <?php if (JFactory::getLanguage()->get('tag') == "en-GB") : ?>
                                        <?php echo JHtml::_('grid.sort', 'Title', 'a.title', $listDirn, $listOrder); ?>
                                    <?php else : ?>
                                        <?php echo JHtml::_('grid.sort', 'عنوان المقال', 'a.title', $listDirn, $listOrder); ?>
                                    <?php endif; ?>
                                <?php else : ?>
                                    <?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
                                <?php endif; ?>
                            </th>
                            <?php if ($date = $this->params->get('list_show_date')) : ?>
                                <th id="categorylist_header_date" data-sorter="false">
                                    <?php if ($date == "created") : ?>
                                        <?php echo JHtml::_('grid.sort', 'COM_CONTENT_'.$date.'_DATE', 'a.created', $listDirn, $listOrder); ?>
                                    <?php elseif ($date == "modified") : ?>
                                        <?php echo JHtml::_('grid.sort', 'COM_CONTENT_'.$date.'_DATE', 'a.modified', $listDirn, $listOrder); ?>
                                    <?php elseif ($date == "published") : ?>
                                        <?php echo JHtml::_('grid.sort', 'COM_CONTENT_'.$date.'_DATE', 'a.publish_up', $listDirn, $listOrder); ?>
                                    <?php endif; ?>
                                </th>
                            <?php endif; ?>
                            <?php if ($this->params->get('list_show_author')) : ?>
                                <th id="categorylist_header_author">
                                    <?php echo JHtml::_('grid.sort', 'JAUTHOR', 'author', $listDirn, $listOrder); ?>
                                </th>
                            <?php endif; ?>
                            <?php if ($this->params->get('list_show_hits')) : ?>
                                <th id="categorylist_header_hits">
                                    <?php echo JHtml::_('grid.sort', 'JGLOBAL_HITS', 'a.hits', $listDirn, $listOrder); ?>
                                </th>
                            <?php endif; ?>

                            <!--SERVICE REQUEST HEADINGS -->
                            <?php if ($list_show_servicerequest_tenant) : ?>
                                <th id="categorylist_header_servicerequest_tenant" class="applyfilter">
                                    <a href="#" onclick="return false;" class="hasTooltip" title="" data-original-title="<strong><?php echo JText::_('TPL_EXTRAFIELDS_SERVICEREQUEST_TENANT'); ?></strong><br />Click to sort by this column"><?php echo JText::_('TPL_EXTRAFIELDS_SERVICEREQUEST_TENANT'); ?></a>

                                </th>
                            <?php endif; ?>

                            <?php if ($list_show_servicerequest_item) : ?>
                                <th id="categorylist_header_servicerequest_item" class="applyfilter">
                                    <a href="#" onclick="return false;" class="hasTooltip" title="" data-original-title="<strong><?php echo JText::_('TPL_EXTRAFIELDS_SERVICEREQUEST_ITEM'); ?></strong><br />Click to sort by this column"><?php echo JText::_('TPL_EXTRAFIELDS_SERVICEREQUEST_ITEM'); ?></a>

                                </th>
                            <?php endif; ?>

                            <?php if ($list_show_servicerequest_approval) : ?>
                                <th id="categorylist_header_servicerequest_approval" class="applyfilter">
                                <a href="#" onclick="return false;" class="hasTooltip" title="" data-original-title="<strong><?php echo JText::_('TPL_EXTRAFIELDS_SERVICEREQUEST_APPROVAL'); ?></strong><br />Click to sort by this column"><?php echo JText::_('TPL_EXTRAFIELDS_SERVICEREQUEST_APPROVAL'); ?></a>

                                </th>
                            <?php endif; ?>

                            <!-- BILLING HEADINGS -->
                            <?php if ($list_show_billing_name) : ?>
                                <th id="categorylist_header_billing_name" class="applyfilter">
                                    <a href="#" onclick="return false;" class="hasTooltip" title="" data-original-title="<strong><?php echo JText::_('TPL_EXTRAFIELDS_BILLING_INVOICEE'); ?></strong><br />Click to sort by this column"><?php echo JText::_('TPL_EXTRAFIELDS_BILLING_INVOICEE'); ?></a>
                                </th>
                            <?php endif; ?>

                            <?php if ($list_show_billing_price) : ?>
                                <th id="categorylist_header_billing_price">
                                    <a href="#" onclick="return false;" class="hasTooltip" title="" data-original-title="<strong><?php echo JText::_('TPL_EXTRAFIELDS_BILLING_AMOUNT'); ?></strong><br />Click to sort by this column"><?php echo JText::_('TPL_EXTRAFIELDS_BILLING_AMOUNT'); ?></a>
                                </th>
                            <?php endif; ?>

                            <?php if ($list_show_billing_type) : ?>
                                <th id="categorylist_header_billing_type" class="applyfilter">
                                    <a href="#" onclick="return false;" class="hasTooltip" title="" data-original-title="<strong><?php echo JText::_('TPL_EXTRAFIELDS_BILLING_TYPE'); ?></strong><br />Click to sort by this column"><?php echo JText::_('TPL_EXTRAFIELDS_BILLING_TYPE'); ?></a>
                                </th>
                            <?php endif; ?>

                            <?php if ($list_show_billing_status) : ?>
                                <th id="categorylist_header_billing_status" class="applyfilter">
                                    <a href="#" onclick="return false;" class="hasTooltip" title="" data-original-title="<strong><?php echo JText::_('TPL_EXTRAFIELDS_BILLING_REPEAT_SHORT'); ?></strong><br />Click to sort by this column"><?php echo JText::_('TPL_EXTRAFIELDS_BILLING_REPEAT_SHORT'); ?></a>
                                </th>
                            <?php endif; ?>


                            <?php if ($list_show_billing_repeating) : ?>
                                <th id="categorylist_header_billing_repeating" class="applyfilter">
                                    <a href="#" onclick="return false;" class="hasTooltip" title="" data-original-title="<strong><?php echo JText::_('TPL_EXTRAFIELDS_BILLING_STATUS'); ?></strong><br />Click to sort by this column"><?php echo JText::_('TPL_EXTRAFIELDS_BILLING_STATUS'); ?></a>
                                </th>
                            <?php endif; ?>

                            <!-- COMPANY PROFILE HEADING -->
                            <?php if ($list_show_companyprofile_approval) : ?>
                                <th id="categorylist_header_companyprofile_approval" class="applyfilter">
                                    <a href="#" onclick="return false;" class="hasTooltip" title="" data-original-title="<strong><?php echo JText::_('TPL_EXTRAFIELDS_COMPANYPROFILE_APPROVAL_SHORT'); ?></strong><br />Click to sort by this column"><?php echo JText::_('TPL_EXTRAFIELDS_COMPANYPROFILE_APPROVAL_SHORT'); ?></a>
                                </th>
                            <?php endif; ?>

                            <?php if ($list_show_companyprofile_language) : ?>
                                <th id="categorylist_header_companyprofile_language" class="applyfilter">
                                    <a href="#" onclick="return false;" class="hasTooltip" title="" data-original-title="<strong><?php echo JText::_('TPL_EXTRAFIELDS_COMPANYPROFILE_LANGUAGE_SHORT'); ?></strong><br />Click to sort by this column"><?php echo JText::_('TPL_EXTRAFIELDS_COMPANYPROFILE_LANGUAGE_SHORT'); ?></a>
                                </th>
                            <?php endif; ?>

                            <!-- NEWS HEADINGS -->
                            
                            <?php if ($list_show_category_title) :?>
                                <th id="categorylist_header_category_title">
                                <a href="#" onclick="return false;" class="hasTooltip" title="" data-original-title="<strong><?php echo JText::_('J3_LISTHEADING_CATEGORY'); ?></strong><br />Click to sort by this column"><?php echo JText::_('J3_LISTHEADING_CATEGORY'); ?></a>

                                </th>
                            <?php endif; ?>

                            <?php if ($isEditable) : ?>
                                <th id="categorylist_header_edit" data-sorter="false"><?php echo JText::_('J3_EDITOR_EDIT_ITEM'); ?></th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                <?php endif; ?>

                <tbody>
                <!-- FILL IN THE TABLE ROWS -->
                <?php foreach ($this->items as $i => $article) : ?>

                    <?php
                        $attribs = new JRegistry($article->attribs);
                         $showarticle = false;

                        //CHECK IF IS A TENANT
                         if ($restrictView) {
                            $username = JFactory::getUser()->name;
                            $userid = JFactory::getUser()->id;

                            if ($catid == 10) { //Billing, check if billed to current user
                                if ($attribs->get('billing_tenant_name') == $username || $attribs->get('billing_tenant_id') == $userid)
                                    $showarticle = true;
                            } else { //otherwise check current user is the author
                                if ($this->items[$i]->created_by == $userid)

                                    $showarticle = true;
                            }
                        } else {
                            $showarticle = true;
                        }
                    ?>

                    <!-- ONLY PRINT IF ARTICLE IS OWNED BY THE CURRENT USER -->
                    <?php if ($showarticle) : ?>

                        <?php if ($this->items[$i]->state == 0) : ?> <!--remove this to print unpublished items -->
                            <tr class="system-unpublished cat-list-row<?php echo $i % 2; ?>">
                        <?php else: ?>
                            <tr class="cat-list-row<?php echo $i % 2; ?>" >
                        <?php endif; ?>
                                <td headers="categorylist_header_title" class="list-title">
                                    <?php if (in_array($article->access, $this->user->getAuthorisedViewLevels())) : ?>
                                        <a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($article->slug, $article->catid)); ?>">
                                            <?php if ($list_show_service_arabic_name): ?>
                                                <?php echo $attribs->get('service_arabic_name'); ?>
                                            <?php else : ?>
                                                <?php echo $this->escape($article->title); ?>
                                            <?php endif; ?>
                                        </a>
                                    <?php else: ?>
                                        <?php
                                            echo $this->escape($article->title).' : ';
                                            $menu        = JFactory::getApplication()->getMenu();
                                            $active        = $menu->getActive();
                                            $itemId        = $active->id;
                                            $link = JRoute::_('index.php?option=com_users&view=login&Itemid='.$itemId);
                                            $returnURL = JRoute::_(ContentHelperRoute::getArticleRoute($article->slug));
                                            $fullURL = new JUri($link);
                                            $fullURL->setVar('return', base64_encode($returnURL));
                                        ?>
                                        <a href="<?php echo $fullURL; ?>" class="register">
                                            <?php echo JText::_('COM_CONTENT_REGISTER_TO_READ_MORE'); ?>
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($article->state == 0) : ?>
                                        <span class="list-published label label-warning">
                                            <?php echo JText::_('JUNPUBLISHED'); ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if (strtotime($article->publish_up) > strtotime(JFactory::getDate())) : ?>
                                        <span class="list-published label label-warning">
                                            <?php echo JText::_('JNOTPUBLISHEDYET'); ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if ((strtotime($article->publish_down) < strtotime(JFactory::getDate())) && $article->publish_down != '0000-00-00 00:00:00') : ?>
                                        <span class="list-published label label-warning">
                                            <?php echo JText::_('JEXPIRED'); ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <?php if ($this->params->get('list_show_date')) : ?>
                                    <td headers="categorylist_header_date" class="list-date small">
                                        <?php
                                            echo JHtml::_('date', $article->displayDate, $this->escape($this->params->get('date_format', JText::_('DATE_FORMAT_LC3'))));
                                        ?>
                                     </td>
                                <?php endif; ?>
                                <?php if ($this->params->get('list_show_author', 1)) : ?>
                                    <td headers="categorylist_header_author" class="list-author">
                                          <?php if (!empty($article->author) || !empty($article->created_by_alias)) : ?>
                                              <?php $author = $article->author ?>
                                              <?php $author = ($article->created_by_alias ? $article->created_by_alias : $author);?>
                                              <?php if (!empty($article->contact_link) && $this->params->get('link_author') == true) : ?>
                                                  <?php echo JText::sprintf('COM_CONTENT_WRITTEN_BY', JHtml::_('link', $article->contact_link, $author)); ?>
                                              <?php else: ?>
                                                  <?php echo JText::sprintf('COM_CONTENT_WRITTEN_BY', $author); ?>
                                              <?php endif; ?>
                                          <?php endif; ?>
                                      </td>
                                <?php endif; ?>
                                <?php if ($this->params->get('list_show_hits', 1)) : ?>
                                      <td headers="categorylist_header_hits" class="list-hits">
                                          <span class="badge badge-info">
                                              <?php echo JText::sprintf('JGLOBAL_HITS_COUNT', $article->hits); ?>
                                          </span>
                                      </td>
                                  <?php endif; ?>
                                  <?php if ($list_show_servicerequest_tenant) :?>
                                      <td headers="categorylist_header_servicerequest_tenant" class="list-servicerequest-tenant">
                                        <?php echo $article->author; ?>
                                      </td>
                                  <?php endif; ?>
                                  
                                  <?php if ($list_show_servicerequest_item) :?>
                                      <td headers="categorylist_header_servicerequest_item" class="list-servicerequest-item">
                                  
                                            <?php if ($list_show_servicerequest_arabic_name) : ?>
                                                <?php echo $attribs->get('service_arabic_name'); ?>
                                            <?php else : ?>
                                                <?php echo $attribs->get('service_name'); ?>
                                            <?php endif; ?>
                                  
                                      </td>
                                  <?php endif; ?>
                                  
                                  <?php if ($list_show_servicerequest_approval) :?>
                                  <td headers="categorylist_header_servicerequest_approval" class="list-servicerequest-approval">
                                      <?php
                                          $servicerequest_approval = $attribs->get('servicerequest_approval');
                                          switch ($servicerequest_approval) {
                                              case '0':
                                                  if ($list_show_servicerequest_arabic_name) echo "بإنتظر الموفقة";
                                                  else echo "Pending";
                                                  break;
                                              case '1':
                                                  if ($list_show_servicerequest_arabic_name) echo "موافق";
                                                  else echo "Approved";
                                                  break;
                                              case '2':
                                                  if ($list_show_servicerequest_arabic_name) echo "رفض";
                                                  else echo "Denied";
                                                  break;
                                              default:
                                                  break;
                                          };
                                      ?>
                                  </td>
                                  <?php endif; ?>
                                  <?php if ($list_show_companyprofile_approval) :?>
                                  <td headers="categorylist_header_compnayprofile_approval" class="list-companyprofile-approval">
                                      <?php
                                          $companyprofile_approval = $attribs->get('companyprofile_approval');
                                          switch ($companyprofile_approval) {
                                              case '0':
                                                  if ($list_show_companyprofile_arabic) echo "بإنتظر الموفقة";
                                                  else echo "Pending";
                                                  break;
                                              case '1':
                                                  if ($list_show_companyprofile_arabic) echo "موافق";
                                                  else echo "Approved";
                                                  break;
                                              default:
                                                  break;
                                          };
                                      ?>
                                  </td>
                                  <?php endif; ?>
                                  <?php if ($list_show_companyprofile_language) :?>
                                  <td headers="categorylist_header_companyprofile_language" class="list-companyprofile-language">
                                      <?php
                                          $companyprofile_language = $attribs->get('companyprofile_language');
                                          switch ($companyprofile_language) {
                                              case '0':
                                                  echo "Both";
                                                  break;
                                              case '1':
                                                  echo "English Only";
                                                  break;
                                              case '2':
                                                  echo "Arabic Only";
                                                  break;
                                              default:
                                                  break;
                                          };
                                      ?>
                                  </td>
                                  <?php endif; ?>
                                  <?php if ($list_show_billing_name) : ?>
                                    <td headers="categorylist_header_billing_name" class="list-billing-name">
                                          <?php echo $attribs->get('billing_tenant_name'); ?>
                                      </td>
                                <?php endif; ?>
                                <?php if ($list_show_billing_price) : ?>
                                    <td headers="categorylist_header_billing_price" class="list-billing-price">
                                          <?php echo $attribs->get('billing_amount'); ?>
                                      </td>
                                <?php endif; ?>
                                <?php if ($list_show_billing_type) : ?>
                                    <td headers="categorylist_header_billing_type" class="list-billing-type">
                                          <?php echo $attribs->get('billing_type'); ?>
                                      </td>
                                <?php endif; ?>
                                  <?php if ($list_show_billing_status) : ?>
                                    <td headers="categorylist_header_billing_repeat" class="list-billing-repeat">
                                          <?php
                                              $billing_repeat = $attribs->get('billing_repeat');
                                              switch ($billing_repeat) {
                                                  case '0':
                                                      echo "No";
                                                      break;
                                                  case '1':
                                                      echo "Yes";
                                                      break;
                                                  default:
                                                      echo "No";
                                                      break;
                                              };
                                          ?>
                                      </td>
                                  <?php endif; ?>

                                <?php if ($list_show_billing_status) : ?>
                                    <td headers="categorylist_header_billing_status" class="list-billing-status">
                                          <?php
                                              $billing_status = $attribs->get('billing_status');
                                              switch ($billing_status) {
                                                  case '0':
                                                      echo "Unpaid";
                                                      break;
                                                  case '1':
                                                      echo "Paid";
                                                      break;
                                                  case '2':
                                                      echo "Paid";
                                                      break;
                                                  case '3':
                                                      echo "Paid";
                                                      break;
                                                  case '4':
                                                      echo "Paid";
                                                      break;

                                                  default:
                                                      break;
                                              };
                                          ?>
                                      </td>
                                  <?php endif; ?>

                                   <?php if ($list_show_category_title) : ?>
                                    <td headers="categorylist_header_category_title" class="list-category-title">
                                        <?php echo $article->category_title; ?>
                                    </td>
                                  <?php endif; ?>

                                <?php if ($isEditable) : ?>
                                    <td headers="categorylist_header_edit" class="list-edit">
                                        <?php if ($article->params->get('access-edit')) : ?>
                                            <?php echo JHtml::_('icon.edit', $article, $params); ?>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                        </tr>
                    <?php else: ?>
                        <?php $myPageItems--; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <?php // Code to add a link to submit an article. ?>
        <?php if ($this->category->getParams()->get('access-create') && !$noSubmit) : ?>
            <?php echo JHtml::_('icon.create', $this->category, $this->category->params); ?>
        <?php  endif; ?>

        <?php // Add pagination links ?>
        <?php if (!empty($this->items)) : ?>
            <?php
                if ($restrictView) {
                    $myPageItems = $myPageItems - ($this->pagination->total - $this->pagination->limit);
                    $myPagesTotal = ceil($myPageItems/$this->pagination->limit);
                    $this->pagination->pagesTotal = $myPagesTotal;
                }
                // print_r(" myPageItems:".$myPageItems);
                // print_r(" myPagesTotal: ".$myPagesTotal);
                // print_r(" this->pagination->limit: ".$this->pagination->limit);
            ?>
            <?php if (($this->params->def('show_pagination', 2) == 1  || ($this->params->get('show_pagination') == 2)) && ($this->pagination->pagesTotal > 1)) : ?>
                <br/><div class="pagination">

                    <?php if ($this->params->def('show_pagination_results', 1)) : ?>
                        <p class="counter pull-right">
                            <?php echo $this->pagination->getPagesCounter(); ?>
                        </p>
                    <?php endif; ?>

                    <?php echo $this->pagination->getPagesLinks(); ?>
                </div>
            <?php endif; ?>
        </form>
    <?php  endif; ?>
<?php endif; ?>
