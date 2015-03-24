<?
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('_JEXEC') or die;
?>

<?= helper('bootstrap.load'); ?>
<?= helper('behavior.jquery'); ?>

<ktml:script src="media://com_fileman/js/fileman.js" />

<? if ($params->track_downloads): ?>
<script>
kQuery(function($) {
    $('.fileman-view').click(function() {
        Fileman.trackEvent({action: 'Download', label: $(this).attr('data-path')});
    });
});
</script>
<? endif; ?>

<div class="fileman_table_layout">

    <? if ($params->show_page_heading): ?>
        <h2 class="fileman_header">
            <?= escape($params->page_heading); ?>
        </h2>
    <? endif; ?>

    <? if ($params->allow_uploads): ?>
        <?= import('upload.html', array('folder' => $folder)); ?>
    <? endif; ?>

    <form action="" method="get" class="koowa_form -koowa-grid koowa_table_list">
        <? // Table ?>
        <table class="table table-striped koowa_table koowa_table--files">
            <? if ($params->limit != 0): ?>
            <tfoot>
                <tr>
                    <td colspan="2">
                        <?= helper('paginator.pagination', array(
                            'total' 	 => parameters()->total,
                            'limit' 	 => parameters()->limit,
                            'offset' 	 => parameters()->offset,
                            'attribs'    => array(
                                'onchange' => 'this.form.submit();'
                            )
                        )) ?>
                    </td>
                </tr>
            </tfoot>
            <? endif; ?>
            <tbody>
                <? if ($folders): foreach($folders as $folder): ?>
                <tr class="fileman_folder">
                    <td colspan="2">
                        <span class="koowa_header">
                            <? if ($params->show_icon): ?>
                            <span class="koowa_header__item koowa_header__item--image_container">
                                <a class="iconImage" href="<?= route('layout=table&folder='.$folder->path);?>">
                                    <span class="koowa_icon--folder koowa_icon--24"><i>Folder icon</i></span>
                                </a>
                            </span>
                            <? endif ?>
                            <span class="koowa_header__item">
                                <span class="koowa_wrapped_content">
                                    <span class="whitespace_preserver">
                                        <a href="<?= route('layout=table&folder='.$folder->path);?>">
                                            <?=escape($folder->display_name)?>
                                        </a>
                                    </span>
                                </span>
                            </span>
                        </span>
                    </td>
                </tr>
                <? endforeach; endif; ?>
                <? if ($files): foreach($files as $file): ?>
                <tr class="fileman_file" itemscope itemtype="http://schema.org/CreativeWork">
                    <td>
                        <span class="koowa_header">
                            <? if ($params->show_icon): ?>
                            <span class="koowa_header__item koowa_header__item--image_container">
                                <a class="iconImage" data-path="<?= escape($file->path); ?>"
                                   href="<?= route('view=file&folder='.parameters()->folder.'&name='.$file->name);?>">
                                    <span class="koowa_icon--<?= helper('com:files.icon.icon', array(
                                        'extension' => $file->extension
                                    )) ?> koowa_icon--24"></span>
                                </a>
                            </span>
                            <? endif ?>
                            <span class="koowa_header__item">
                                <span class="koowa_wrapped_content">
                                    <span class="whitespace_preserver">
                                        <a data-path="<?= escape($file->path); ?>"
                                            href="<?= route('view=file&folder='.parameters()->folder.'&name='.$file->name);?>">
                                            <span itemprop="name"><?=escape($file->display_name)?></span>
                                        </a>
                                    </span>
                                </span>
                            </span>
                        </span>
                    </td>
                    <td width="10" style="white-space: nowrap">
                        <small>
                            <?= helper('com:files.filesize.humanize', array('size' => $file->size));?>
                        </small>
                    </td>
                </tr>
                <? endforeach; endif; ?>
            </tbody>
        </table>
    </form>
</div>