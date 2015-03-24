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

<div class="fileman_submit_layout">

    <? if ($params->show_page_heading): ?>
        <h2 class="fileman_header"><?= escape($params->page_heading) ?></h2>
    <? endif ?>

    <ktml:toolbar type="actionbar" title="false">

    <form action="" method="post" class="koowa_form -koowa-form" enctype="multipart/form-data">
        <fieldset class="form-horizontal">
            <legend><?= translate('Select a file')?></legend>
            <div class="control-group">
                <input required type="file" name="file" />
            </div>
        </fieldset>
    </form>

</div>