<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>

<?
$token     = version_compare(JVERSION, '3.0', 'ge') ? JSession::getFormToken() : JUtility::getToken();
$show_download = isset($show_download) ? $show_download : $document->canPerform('download');
$show_delete   = isset($show_delete) ? $show_delete : $document->canPerform('delete');
$show_edit     = isset($show_edit) ? $show_edit : $document->canPerform('edit');

$button_size = 'btn-'.(isset($button_size) ? $button_size : 'small');
?>

<? if ($show_download || $show_edit || $show_delete): ?>
<div class="btn-toolbar">
    <? if ($show_download): ?>
	    <div class="btn-group docman-btn-group-download">
	        <a class="btn <?= $button_size ?> docman-download"
	        	data-title="<?= @escape($document->title); ?>" 
	        	data-id="<?= $document->id; ?>" 
	        	href="<?= $document->download_link; ?>"
                <?= $params->download_in_blank_page ? 'target="_blank"' : ''; ?>
            >
	        	<i class="icon-download"></i> 
	        	<?= @text('Download'); ?>
	        </a>
	    </div>
    <? endif ?>

    <? if (!($document->isLockable() && $document->locked())
            && ($show_edit || $show_delete)): ?>
        <div class="btn-group">
    
        <? if ($show_edit): ?>
        <a class="btn <?= $button_size ?>" href="<?= @route($document, 'layout=form');?>"><i class="icon-pencil"></i> <?= @text('Edit'); ?></a>
        <? endif ?>

        <? if ($show_delete):
            $data = sprintf("{method:'post', url:'%s', params:{_token:'%s', action:'delete'}}", @route($document), $token);
        ?>
        <!-- <script src="media://lib_koowa/js/koowa.js" />  -->
        <?= @helper('behavior.mootools'); ?>
        <?= @helper('behavior.deletable'); ?>
        
        <a class="btn <?= $button_size ?> btn-danger docman-deletable" href="#" rel="<?= $data ?>">
            <i class="icon-trash icon-white"></i> <?= @text('Delete') ?>
        </a>
        <? endif ?>
    </div>
    <? endif ?>
</div>
<? endif ?>
