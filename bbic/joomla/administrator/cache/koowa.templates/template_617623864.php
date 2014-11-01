<?php /**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>
<?php echo $this->helper('bootstrap.load', array(
    'package' => 'docman',
    'wrapper' => false
)); ?>
<?php // No documents message if the "Show only user's documents" parameter is enabled ?>
<?php if ($this->parameters()->total == 0): if ($params->own): ?>
    <p class="alert alert-info">
        <?php echo $this->translate('You do not have any documents yet.'); ?>
    </p>
<?php endif; else: ?>

<?php if ($params->track_downloads): ?>
    <?php echo $this->helper('com://admin/docman.behavior.download_tracker'); ?>
<?php endif; ?>

<div class="koowa mod_docman mod_docman--documents<?php echo $params->moduleclass_sfx; ?>">
    <ul<?php echo $params->show_icon ? ' class="mod_docman_icons"' :'' ?>>
    <?php foreach ($documents as $document): ?>
        <li class="module_document">

            <div class="koowa_header">
                <?php // Header icon/image ?>
                <?php if ($document->icon && $params->show_icon): ?>
                <span class="koowa_header__item koowa_header__item--image_container">
                    <a href="<?php echo $document->title_link; ?>"
                       class="koowa_header__image_link <?php echo $params->link_to_download ? 'docman_track_download' : ''; ?>"
                       data-title="<?php echo $this->escape($document->title); ?>"
                       data-id="<?php echo $document->id; ?>"
                        <?php echo $params->download_in_blank_page ? 'target="_blank"' : ''; ?>
                        >
                        <?php // Icon ?>
                        <?php echo $this->import('com://site/docman.document.icon.html', array('icon' => $document->icon)) ?>
                    </a>
                </span>
                <?php endif ?>

                <?php // Header title ?>
                <span class="koowa_header__item">
                    <span class="koowa_wrapped_content">
                        <span class="whitespace_preserver">
                            <a href="<?php echo $document->title_link; ?>"
                               class="koowa_header__title_link <?php echo $params->link_to_download === 'download' ? 'docman_track_download' : ''; ?>"
                               data-title="<?php echo $this->escape($document->title); ?>"
                               data-id="<?php echo $document->id; ?>"
                                <?php echo $params->download_in_blank_page ? 'target="_blank"' : ''; ?>
                                >
                                <?php echo $this->escape($document->title);?></a>

                            <?php // Label new ?>
                            <?php if ($params->show_recent && $this->isRecent($document)): ?>
                                <span class="label label-success"><?php echo $this->translate('New'); ?></span>
                            <?php endif; ?>

                            <?php // Label popular ?>
                            <?php if ($params->show_popular && ($document->hits >= $params->get('hits_for_popular', 100))): ?>
                                <span class="label label-important"><?php echo $this->translate('Popular') ?></span>
                            <?php endif ?>
                        </span>
                    </span>
                </span>
            </div>


            <div class="module_document__info">
                <?php // Category ?>
                <?php if ($document->category_link): ?>
                <div class="module_document__category">
                    <span class="koowa_wrapped_content">
                        <span class="whitespace_preserver">
                            <?php echo $this->translate('In {category}', array('category' => '<a href="'.$document->category_link.'">'.$this->escape($document->category_title).'</a>')); ?>
                        </span>
                    </span>
                </div>
                <?php endif; ?>

                <?php // Created ?>
                <?php if ($params->show_created): ?>
                <div class="module_document__date">
                    <?php echo $this->helper('date.format', array('date' => $document->publish_date)); ?>
                </div>
                <?php endif; ?>

                <?php // Size ?>
                <?php if ($params->show_size && $document->size): ?>
                <div class="module_document__size">
                    <?php echo $this->helper('com://admin/docman.string.humanize_filesize', array('size' => $document->size)); ?>
                </div>
                <?php endif; ?>

                <?php // Downloads ?>
                <?php if ($params->show_hits && $document->hits): ?>
                <div class="module_document__downloads">
                    <?php echo $this->object('translator')->choose(array('{number} download', '{number} downloads'), $document->hits, array('number' => $document->hits)) ?>
                </div>
                <?php endif; ?>
            </div>
        </li>
    <?php endforeach; ?>
    </ul>
</div>

<?php endif; ?>