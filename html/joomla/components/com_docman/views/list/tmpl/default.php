<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>

<?= @helper('behavior.bootstrap'); ?>
<?= @helper('behavior.mootools'); ?>

<!--
<script src="media://lib_koowa/js/koowa.js" />
-->

<? if ($params->get('show_page_heading')): ?>
    <h1>
        <?= $params->get('page_heading'); ?>
    </h1>
<? endif; ?>

<? if ($params->show_add_document_button):
    $route = '&view=document&layout=form&slug='.($category->slug ? '&category_slug='.$category->slug : '');
?>
<div class="btn-toolbar">
    <a class="btn" href="<?= @route($route); ?>">
        <i class="icon-plus"></i> <?= @text('Add new document')?>
    </a>
</div>
<? endif; ?>

<div id="docman-category" class="docman-heading category">
    <? if ($params->show_icon && !empty($category->params->icon)): ?>
        <img align="left" src="<?= @helper('icon.path', array('icon' => $category->params->icon)) ?>" class="icon" />
    <? endif ?>
    <div class="docman-heading-inner">
        <? if ($params->show_category_title): ?>
        <h3 class="page_title contentheading docman-title">
            <?= @escape($category->title); ?>
        </h3>
        <? endif; ?>

        <? if ($params->show_image && $category->image):
            $image_path = KRequest::root().'/joomlatools-files/docman-images/'.$category->image;
         ?>
            <?= @helper('behavior.modal', array('selector' => 'a.thumbnail')); ?>
            <a class="thumbnail docman-document-thumbnail" href="<?= $image_path ?>">
                <img src="<?= $image_path ?>" />
            </a>
        <? endif ?>

        <? if ($category->description_full && $params->show_description): ?>
        <div class="description"><?= @prepareText($category->description_full); ?></div>
        <? endif; ?>
    </div>
</div>
<? if ($params->show_subcategories && count($subcategories)): ?>
    <? if ($category->id): ?>
    <h3 class="docman-heading">
        <?= @text('Categories') ?>
    </h3>
    <? endif; ?>
    <?=@template('com://site/docman.view.list.categories', array(
        'categories' => $subcategories,
        'params' => $params,
        'config' => $config,
        'start_level' => $category->level
    ))?>
<? endif; ?>

<? if ($params->show_documents && count($documents)): ?>
<div class="docman-heading-wrap">
    <h3 class="docman-heading<? if ($params->show_document_sort_limit): ?> docman-heading-pagination<? endif; ?>">
        <?= @text('Documents')?>
    </h3>
    <? if ($params->show_document_sort_limit): ?>
        <div class="pagination-limit btn-group form-search">
            <label for="sort-documents" class="control-label"><?= @text('Order by') ?></label>
            <?= @helper('paginator.sort_documents', array(
                'attribs'   => array('class' => 'input-medium', 'id' => 'sort-documents'),
                'sort'      => $state->sort,
                'direction' => $state->direction,
            )); ?>
        </div>
    <? endif; ?>
</div>

<form action="<?= @route('slug='.$category->slug); ?>" method="get" class="-koowa-grid">
    <?= @template('com://site/docman.view.documents.list', array('documents' => $documents, 'params' => $params))?>

    <? if ($params->show_pagination !== '0' && count($documents)): ?>
        <?= @helper('paginator.pagination', array_merge(array(
            'total' => $total,
            'show_limit' => false
        ), $state->getData())) ?>
    <? endif; ?>
</form>
<? endif; ?>