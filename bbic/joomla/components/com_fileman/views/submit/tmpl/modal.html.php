<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
        
defined('KOOWA') or die; ?>

<?= helper('bootstrap.load'); ?>
<?= helper('behavior.keepalive'); ?>
<?= helper('behavior.validator'); ?>
<?= helper('behavior.koowa');?>

<ktml:script src="media://koowa/com_files/js/uploader.min.js" />
<ktml:script src="media://com_fileman/js/site/submit.modal.js" />

<div class="koowa" style="visibility: hidden">
    <div id="files-upload" style="clear: both" class="uploader-files-empty well">
        <div style="text-align: center;">
            <h3 style=" float: none">
                <?= translate('Upload files to {folder}', array(
                    'folder' => $folder
                )) ?>
            </h3>
        </div>
        <div id="files-upload-controls" class="clearfix">
            <ul class="upload-buttons">
                <li id="upload-max">
                    <?= translate('Each file should be smaller than {size}', array(
                        'size' => $size
                    )); ?>
                </li>
            </ul>
        </div>
        <div id="files-uploader-computer" class="upload-form">

            <div style="clear: both"></div>
            <div class="dropzone">
                <h2><?= translate('Drag files here') ?></h2>
            </div>
            <h3 class="nodropzone"><?= translate('Or select a file to upload:') ?></h3>
            <div id="files-upload-multi"></div>

        </div>
    </div>
</div>
