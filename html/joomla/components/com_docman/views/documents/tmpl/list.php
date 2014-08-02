<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>

<? if ($params->track_downloads): ?>
    <?= @helper('behavior.download_tracker'); ?>
<? endif; ?>

<? foreach ($documents as $document): ?>
<div class="docman-row">
    <div class="docman-group">
    	<? if ($params->show_document_created 
    		|| ($params->show_document_modified && $document->modified_by)
    		|| ($params->show_document_size && $document->size)): ?>
        <div class="docman-document-details">
            <table class="table table-bordered">
                <tbody>
                    <? if ($params->show_document_created): ?>
                    <tr>
                        <td class="detail-label"><?= @text('Created date'); ?></td>
                        <td><?= @date(array('date' => $document->publish_date)); ?></td>
                    </tr>
                    <? endif; ?>

                    <? if ($params->show_document_modified && $document->modified_by): ?>
                    <tr>
                        <td class="detail-label"><?= @text('Modified date'); ?></td>
                        <td><?= @date(array('date' => $document->modified_on)); ?></td>
                    </tr>
                    <? endif; ?>

                    <? if ($params->show_document_size && $document->size): ?>
                    <tr>
                        <td class="detail-label"><?= @text('Filesize'); ?></td>
                        <td>
                            <?= $document->size
                                ? @helper('com://admin/docman.template.helper.string.bytes2text', array('bytes' => $document->size))
                                : ''; ?>
                        </td>
                    </tr>
                    <? endif; ?>
                </tbody>
            </table>
        </div>
        <? endif; ?>
        <h4 class="docman-document-header">
        
            <? if ($params->document_title_link): ?>
            	<a href="<?= ($document->title_link) ?>"
                   data-title="<?= @escape($document->title); ?>"
                   data-id="<?= $document->id; ?>"
                   <?= $params->document_title_link === 'download' ? 'class="docman-download"' : ''; ?>
                   <?= $params->download_in_blank_page && $params->document_title_link === 'download' ? 'target="_blank"' : ''; ?>
                >
            <? endif; ?>
            
            <? if (!empty($document->params->icon) && $params->show_document_icon): ?>
                <img align="left" src="<?= @helper('icon.path', array('icon' => $document->params->icon)) ?>" class="icon" />
            <? endif ?>
                <?= @escape($document->title);?>
            <? if ($params->document_title_link): ?>
            	</a>
            <? endif; ?>
            
            <?= @event('onContentAfterTitle', array($event_context, &$document, &$params)); ?>

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
        </h4>

        <?= @event('onContentBeforeDisplay', array($event_context, &$document, &$params)); ?>

        <div class="docman-document-description">
            <? if ($params->show_document_image && $document->image): ?>
                <?= @helper('behavior.modal', array('selector' => 'a.thumbnail', 'handler' => 'image')); ?>
                <a style="max-width: 192px;" class="thumbnail docman-document-thumbnail" href="<?= $document->image_path ?>">
                    <img src="<?= $document->thumbnail_path ?>" />
                </a>
            <? endif ?>

            <? if ($params->show_document_description): ?>
                <?= @prepareText($document->description_summary); ?>
            <? endif; ?>
        </div>

        <? if ($params->show_document_description): ?>

        <? endif ?>

        <?= @template('com://site/docman.view.document.manage', array(
        	'document' => $document,
        	'show_download' => $params->document_title_link !== 'download'
        )) ?>
        
        <?= @event('onContentAfterDisplay', array($event_context, &$document, &$params)); ?>
	</div>
</div>
<? endforeach ?>