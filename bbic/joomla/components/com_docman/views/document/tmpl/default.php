<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>

<?= @helper('behavior.jquery'); ?>
<?= @helper('behavior.bootstrap'); ?>
<?= @helper('behavior.modal', array('selector' => 'a.thumbnail', 'handler' => 'image')); ?>

<? if ($params->track_downloads): ?>
    <?= @helper('behavior.download_tracker'); ?>
<? endif; ?>

<script>
    jQuery(function($){
        var $buttons = $('.docman-btn-group-download'),
            width = $buttons.css('display', 'inline-block').width(),
            $well = $('.docman-document-details > .well'),
            widthFix = function(){
                if(width > $well.outerWidth()) {
                    $buttons.addClass('btn-group-vertical').removeClass('btn-group-split');
                } else {
                    $buttons.removeClass('btn-group-vertical').addClass('btn-group-split');
                }
            };
        $buttons.css('display', '').addClass('btn-block')
                .find('.btn').addClass('btn-block');

        widthFix();
        $(window).resize(widthFix);
    });
</script>

<? if ($params->get('show_page_heading')): ?>
    <h1>
        <?= $params->get('page_heading'); ?>
    </h1>
<? endif; ?>

<div class="docman-document">
	<div class="container-fluid">
        <? if ($params->show_document_title): ?>
        <div class="row-fluid">
            <h1 class="docman-document-header <?= $document->params->icon && $params->show_document_icon ? 'icon' :'' ?>">
                <? if ($document->params->icon && $params->show_document_icon): ?>
                    <img align="left" src="<?= @helper('icon.path', array('icon' => $document->params->icon)) ?>" class="icon" />
                <? endif ?>

                <?= $document->title; ?>

                <? if ($params->show_document_recent && @isRecent($document)): ?>
                    <span class="label label-success"><?= @text('New'); ?></span>
                <? endif; ?>

                <? if ($document->canPerform('edit') && $document->isLockable() && $document->locked()): ?>
                    <span class="label label-warning"><?= $document->lockMessage(); ?></span>
                <? endif; ?>

                <? if (!$document->isPublished() || !$document->enabled): ?>
                    <? $status = $document->enabled ? @text($document->status) : @text('draft'); ?>
                    <span class="label label-<?= $status ?>"><?= @text(ucfirst($status)); ?></span>
                <? endif; ?>

                <?= @template('manage', array('document' => $document, 'show_download' => false)); ?>
            </h1>
            
            <?= @event('onContentAfterTitle', array($event_context, &$document, &$params)); ?>
        </div>
        <? endif; ?>
        <div class="row-fluid">
            <div class="span8">
                <?= @event('onContentBeforeDisplay', array($event_context, &$document, &$params)); ?>

                <? if ($params->show_document_image && $document->image): ?>
                    <?= @helper('behavior.modal', array('selector' => 'a.thumbnail', 'handler' => 'image')); ?>
                    <a class="thumbnail docman-document-thumbnail" href="<?= $document->image_path ?>">
                        <img src="<?= $document->thumbnail_path ?>" />
                    </a>
                <? endif ?>

                <? if ($document->description_full && $params->show_document_description): ?>
                    <div class="docman-document-description">

                        <?= @prepareText($document->description_full); ?>
                    </div>
                <? endif; ?>
            </div>

            <div class="span4 docman-document-details">
                <? if ($document->canPerform('download') || $login_to_download):
                ?>
	            <a class="btn btn-large btn-primary docman-download docman-btn-download btn-block" href="<?= $document->download_link; ?>"
                   data-title="<?= @escape($document->title); ?>"
                   data-id="<?= $document->id; ?>"
                    <?= $params->download_in_blank_page ? 'target="_blank"' : ''; ?>
                >
	                <i class="icon-white icon-download"></i>
                    <?= @text('Download'); ?>
                </a>
                <? endif ?>
                
                <? if (($document->storage->name && $params->show_document_filename)
                	|| ($document->size && $params->show_document_size)
                	|| ($document->storage_type == 'file' && $params->show_document_extension)
                	|| ($params->show_document_created) || ($params->show_document_created_by)
                	|| ($document->modified_by && $params->show_document_modified)
                ): ?>
                <div class="well">
                    <? if ($document->storage->name && $params->show_document_filename): ?>
                        <span class="detail-label"><?= @text('File name'); ?></span>
                        <span class="detail-name" title="<?= @escape($document->storage->name); ?>"><?= $document->storage->name; ?></span>
                    <? endif; ?>
                    <? if ($document->size && $params->show_document_size): ?>
                        <span class="detail-label"><?= @text('File Size'); ?></span>
                        <?= @helper('com://admin/docman.template.helper.string.bytes2text', array('bytes' => $document->size)); ?>
                    <? endif; ?>
                    <? if ($document->storage_type == 'file' && $params->show_document_extension): ?>
                        <span class="detail-label"><?= @text('File Type'); ?></span>
                        <?= $document->extension; ?> <?= $document->mimetype ? '('.$document->mimetype.')' : ''; ?>
                    <? endif; ?>
                    <? if ($params->show_document_created): ?>
                        <span class="detail-label"><?= @text('Created date'); ?></span>
                        <?= @date(array('date' => $document->publish_date)); ?>
                    <? endif; ?>
                    <? if ($params->show_document_created_by): ?>
                        <span class="detail-label"><?= @text('Owner'); ?></span>
                        <?= $document->created_by_name; ?>
                    <? endif; ?>
                    <? if ($document->modified_by && $params->show_document_modified): ?>
                        <span class="detail-label"><?= @text('Modified date'); ?></span>
                        <?= @date(array('date' => $document->modified_on)); ?>
                    <? endif; ?>
                </div>
                <? endif; ?>
            </div>
        </div>
        <?= @event('onContentAfterDisplay', array($event_context, &$document, &$params)); ?>
    </div>
</div>

