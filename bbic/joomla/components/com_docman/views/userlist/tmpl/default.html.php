<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>

<?= helper('bootstrap.load'); ?>
<?= helper('behavior.koowa');?>
<?= helper('behavior.modal');?>

<div class="docman_list_layout docman_list_layout--user_list">

    <? // Page Heading ?>
    <? if ($params->get('show_page_heading')): ?>
    <h1 class="docman_page_heading">
        <?= escape($params->get('page_heading')); ?>
    </h1>
    <? endif; ?>

    <? // Toolbar ?>
    <ktml:toolbar type="actionbar" title="false">

    <? // Category ?>
    <? if (($params->show_category_title && $category->title)
        || ($params->show_image && $category->image)
        || ($category->description_full && $params->show_description)
    ): ?>
    <div class="docman_category">

        <? // Header ?>
        <? if ($params->show_category_title && $category->title): ?>
            <h3 class="koowa_header">
                <? // Header image ?>
                <? if ($params->show_icon && $category->icon): ?>
                    <span class="koowa_header__item koowa_header__item--image_container">
            <?= import('com://site/docman.document.icon.html', array('icon' => $category->icon)) ?>
        </span>
                <? endif ?>

                <? // Header title ?>
                <? if ($params->show_category_title): ?>
                    <span class="koowa_header__item">
                        <span class="koowa_wrapped_content">
                            <span class="whitespace_preserver">
                                <?= escape($category->title); ?>
                            </span>
                        </span>
                    </span>
                <? endif; ?>
            </h3>
        <? endif; ?>

        <? // Category image ?>
        <? if ($params->show_image && $category->image): ?>
            <?= helper('behavior.thumbnail_modal'); ?>
            <a class="docman_thumbnail thumbnail" href="<?= $category->image_path ?>">
                <img src="<?= $category->image_path ?>" />
            </a>
        <? endif ?>

        <? // Category description full ?>
        <? if ($category->description_full && $params->show_description): ?>
            <div class="docman_description">
                <?= prepareText($category->description_full); ?>
            </div>
        <? endif; ?>
    </div>
    <? endif; ?>


    <? // Sub categories ?>
    <? if ($params->show_subcategories && count($subcategories)): ?>
        <? if ($category->id && $params->show_categories_header): ?>
            <div class="docman_block docman_block--top_margin">
                <? // Header ?>
                <h3 class="koowa_header koowa_header--bottom_margin">
                    <?= translate('Categories') ?>
                </h3>
            </div>
        <? endif; ?>

        <? // Categories list ?>
        <?=import('com://site/docman.list.categories.html', array(
            'categories' => $subcategories,
            'params' => $params,
            'config' => $config
        ))?>
    <? endif; ?>


    <? // Documents header & sorting ?>
    <? if (parameters()->total): ?>
    <div class="docman_block">
        <? if ($params->show_documents_header): ?>
            <h3 class="koowa_header">
                <?= translate('Documents')?>
            </h3>
        <? endif; ?>
        <? if ($params->show_document_sort_limit): ?>
            <div class="docman_sorting btn-group form-search">
                <label for="sort-documents" class="control-label"><?= translate('Order by') ?></label>
                <?= helper('paginator.sort_documents', array(
                    'sort'      => 'document_sort',
                    'direction' => 'document_direction',
                    'attribs'   => array('class' => 'input-medium', 'id' => 'sort-documents')
                )); ?>
            </div>
        <? endif; ?>
    </div>


    <? // Documents & pagination  ?>
    <form action="" method="get" class="-koowa-grid">

        <? // Document list | Import child template from documents view ?>
        <?= import('com://site/docman.documents.list.html',array(
            'documents' => $documents,
            'params' => $params
        ))?>

        <? // Pagination  ?>
        <?= helper('paginator.pagination', array_merge(array(
            'total'      => parameters()->total,
            'show_limit' => (bool) $params->show_document_sort_limit
        ), parameters()->toArray())) ?>

    </form>
    <? elseif ($category->id): ?>
    <p class="alert alert-info">
        <?= translate('You do not have any documents in this category.'); ?>
    </p>
    <? endif; ?>
</div>
