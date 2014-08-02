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

<? if ($params->track_downloads): ?>
    <?= @helper('behavior.download_tracker'); ?>
<? endif; ?>

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
    
<form action="<?= @route('slug='.$category->slug); ?>" method="get" class="-koowa-grid docman-table-list">
<? if ($params->show_subcategories && count($subcategories)): ?>
    <table class="table table-striped docman-table-categories">
        <? if ($category->id): ?>
        <thead>
            <tr>
                <th colspan="2">
                    <?= @text('Categories') ?>
                </th>
            </tr>
        </thead>
        <? endif; ?>
        <tbody>
            <? foreach ($subcategories as $subcategory): ?>
            <tr>
                <td colspan="2">
                    <? if ($params->show_icon && !empty($subcategory->params->icon)): ?>
                    <a href="<?= @route($subcategory) ?>">
                        <img src="<?= @helper('icon.path', array('icon' => $subcategory->params->icon)) ?>" class="icon autosize" /></a>
                    <? endif ?>

                    <a href="<?= @route($subcategory) ?>">
                        <?= @escape($subcategory->title) ?>
                    </a>
                </td>
            </tr>
            <? endforeach; ?>
        </tbody>
    </table>
<? endif; ?>
    
<? if ($params->show_documents && count($documents)): ?>
<table class="table table-striped docman-table-documents">
    <thead>
        <tr>
            <th colspan="4">
                <?= @text('Documents')?>

                <? if ($params->show_document_sort_limit): ?>
                <div class="pagination-limit btn-group form-search pull-right">
                    <label for="sort-documents" class="control-label"><?= @text('Order by') ?></label>
                    <?= @helper('paginator.sort_documents', array(
                    'attribs'   => array('class' => 'input-medium', 'id' => 'sort-documents'),
                    'sort'      => $state->sort,
                    'direction' => $state->direction,
                )); ?>
                </div>
                <? endif; ?>
            </th>
        </tr>
    </thead>
    <tbody>
        <? foreach ($documents as $document): ?>
        <tr>
            <td width="100%">
                <? if (!empty($document->params->icon) && $params->show_document_icon): ?>
                    <? if ($params->document_title_link): ?>
                        <a href="<?= ($document->title_link) ?>"
                            data-title="<?= @escape($document->title); ?>"
                            data-id="<?= $document->id; ?>"
                            <?= $params->document_title_link === 'download' ? 'class="docman-download"' : ''; ?>
                            <?= $params->download_in_blank_page && $params->document_title_link === 'download'  ? 'target="_blank"' : ''; ?>
                        >
                    <? endif; ?>
                    <img src="<?= @helper('icon.path', array('icon' => $document->params->icon)) ?>" class="icon autosize" /><? if ($params->document_title_link): ?></a><? endif; ?>
                <? endif ?>

                <? if ($params->document_title_link): ?>
                    <a href="<?= ($document->title_link) ?>"
                        data-title="<?= @escape($document->title); ?>"
                        data-id="<?= $document->id; ?>"
                        <?= $params->document_title_link === 'download' ? 'class="docman-download"' : ''; ?>
                        <?= $params->download_in_blank_page && $params->document_title_link === 'download'  ? 'target="_blank"' : ''; ?>
                    ><?= @escape($document->title);?></a>
                <? else: ?>
                    <?= @escape($document->title);?>
                <? endif; ?>

                <? if ($params->show_document_recent && @isRecent($document)): ?>
                    <span class="label label-success"><?= @text('New'); ?></span>
                <? endif; ?>

                <? if ($document->canPerform('edit') && $document->isLockable() && $document->locked()): ?>
                    <span class="label label-warning"><?= @text('Locked'); ?></span>
                <? endif; ?>

                <? if (!$document->isPublished() || !$document->enabled): ?>
                    <? $status = $document->enabled ? @text($document->status) : @text('draft'); ?>
                    <span class="label label-docman-<?= $status ?>"><?= @text(ucfirst($status)); ?></span>
                <? endif; ?>
            </td>
            <td width="10" style="white-space: nowrap">
            <? if ($params->show_document_size && $document->size): ?>
                <small>
                    <?= @helper('com://admin/docman.template.helper.string.bytes2text', array('bytes' => $document->size)) ?>
                </small>
            <? endif; ?>
            </td>
            <td width="5" style="white-space: nowrap">
            <? if ($params->show_document_created): ?>
                <small>
                    <?= @date(array('date' => $document->publish_date)); ?>
                </small>
            </td>
            <? endif ?>
            <td width="10" style="white-space: nowrap">
            <?= @template('com://site/docman.view.document.manage', array(
                'document' => $document,
                'show_download' => $params->document_title_link !== 'download',
                'button_size' => 'mini'
            )) ?>
            </td>
        </tr>
        <? endforeach ?>
    </tbody>
</table>
<? endif; ?>
<? if ($params->show_pagination !== '0' && !empty($total)): ?>
<table class="table table-striped docman-table-pagination">
    <tfoot>
        <tr>
            <td colspan="2">
                <?= @helper('paginator.pagination', array_merge(array(
                    'total' => $total,
                    'show_limit' => false
                ), $state->getData())) ?>
            </td>
        </tr>
    </tfoot>
</table>
<? endif; ?>
</form>