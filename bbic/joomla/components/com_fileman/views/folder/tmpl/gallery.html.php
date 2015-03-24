<?
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2012 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('_JEXEC') or die; ?>

<?= helper('bootstrap.load', array('javascript' => true)); ?>
<?= helper('behavior.jquery'); ?>
<?= helper('behavior.modal'); ?>

<ktml:script src="media://com_fileman/js/fileman.js" />

<script>
kQuery(function() {
    new Fileman.Gallery({
        <? if ($params->track_views): ?>
        modal: {
            trackViews: true
        }
        <? endif; ?>
    });
});
</script>

<div class="com_fileman fileman_gallery_layout" itemprop="mainContentOfPage" itemscope itemtype="http://schema.org/ImageGallery">

    <? if ($params->show_page_heading): ?>
    	<h2 class="fileman_header" itemprop="name">
    		<?= escape($params->page_heading) ?>
    	</h2>
    <? endif ?>

    <? if ($params->allow_uploads): ?>
        <?= import('upload.html', array('folder' => $folder)); ?>
    <? endif; ?>

    <? if (count($files) || count($folders)): ?>
    <ul class="gallery-thumbnails"><!--
        <? foreach($folders as $folder): ?>
        --><li class="gallery-folder">
            <a class="gallery-link" href="<?= route('layout=gallery&folder='.$folder->path) ?>">
                <span class="gallery-content-box">
                    <span class="gallery-content-box__content">
                        <span class="gallery-content-box__content__inner">
                            <span class="gallery-content-box__content__inner__alignment">
                                <span class="koowa_icon--folder koowa_icon--48"></span>
                            </span>
                        </span>
                    </span>
                </span>
                <span class="gallery-label"><?= escape($folder->display_name) ?></span>
            </a>
        </li><!--
        <? endforeach ?>
        
        <? foreach($files as $file): ?>
    	<? if ($params->show_thumbnails && !empty($file->thumbnail)): ?>
        --><li class="gallery-file" itemscope itemtype="http://schema.org/ImageObject">
            <? if ($file->width): ?>
            <meta itemprop="width" content="<?= $file->width; ?>">
            <? endif; ?>
            <? if ($file->height): ?>
                <meta itemprop="height" content="<?= $file->height; ?>">
            <? endif; ?>
            <meta itemprop="contentUrl" content="<?= route('view=file&folder='.parameters()->folder.'&name='.$file->name) ?>">
    		<a class="gallery-link gallery-modal" data-path="<?= escape($file->path); ?>"
    			href="<?= route('view=file&folder='.parameters()->folder.'&name='.$file->name) ?>"
    		    title="<?= escape($file->display_name) ?>"
            >
    		    <span class="gallery-thumbnail">
        		    <img itemprop="thumbnail" class="gallery-polaroid" src="<?= $file->thumbnail ?>" alt="<?= escape($file->display_name) ?>" />
        		</span>

        		<? if ($params->show_filenames): ?>
        		<span class="gallery-label" itemprop="caption"><?= escape($file->display_name) ?></span>
        		<? endif; ?>
        	</a>
        </li><!--
    	<? else: ?>
        --><li class="gallery-document">
    		<a class="gallery-link gallery-modal" data-path="<?= escape($file->path); ?>"
    			href="<?= route('view=file&folder='.parameters()->folder.'&name='.$file->name) ?>"
    		    title="<?= escape($file->display_name) ?>"
            >
                <span class="gallery-content-box">
                    <span class="gallery-content-box__content">
                        <span class="gallery-content-box__content__inner">
                            <span class="gallery-content-box__content__inner__alignment">
                                <span class="koowa_icon--image koowa_icon--48"></span>
                            </span>
                        </span>
                    </span>
    			</span>
                <? if ($params->show_filenames): ?>
                <span class="gallery-label"><?= escape($file->display_name) ?></span>
                <? endif; ?>
    		</a>
        </li><!--
    	<? endif ?>
        <? endforeach ?>
    --></ul>

    <? if ($params->limit != 0): ?>
    <form action="" method="get" class="-koowa-form">
    <?= helper('paginator.pagination', array(
    	  'total'  => parameters()->total,
    	  'limit'  => parameters()->limit,
    	  'offset' => parameters()->offset,
        'attribs'    => array(
            'onchange' => 'this.form.submit();'
        )
    )) ?>
    </form>
    <? endif ?>

    <? else : ?>

    <h2><?= translate('Folder is empty.') ?></h2>

    <? endif ?>
</div>