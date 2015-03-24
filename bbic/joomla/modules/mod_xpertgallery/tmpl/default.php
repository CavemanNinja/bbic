<?php
/**
 * @package Xpert Gallery
 * @version 2.2
 * @author ThemeXpert http://www.themexpert.com
 * @copyright Copyright (C) 2009 - 2011 ThemeXpert
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted accessd');

?>

<!--ThemeXpert: Xpert Gallery module 2.2 Start here-->
<div class="tx-gallery <?php echo $module_id;?> overview-<?php echo $params->get('overview_position');?> clearfix">
    
    <div class="tx-gallery-header">
        <ul class="tx-gallery-filters">
            <li data-filter="*" class="active"><?php echo JText::_('ALL')?></li>
            <?php echo XEFXpertGalleryHelper::getCatFilterList( $items, $params ) ; ?>
        </ul>

        <?php if($params->get('sort_enabled', 1)): ?>
        <ul class="tx-gallery-sort" >
            <?php foreach($params->get('sort_elements') as $sitem):?>
                <?php $selected = ($sitem == 'original-order') ? 'active' : ''; ?>
                <li data-sort="<?php echo $sitem; ?>" class="<?php echo $selected?>">
                    <?php echo JText::_( strtoupper($sitem) );?>
                </li>
            <?php endforeach;?>
        </ul>
        <?php endif;?>
    </div>

    <ul class="tx-gallery-container tx-gallery-columns-<?php echo $params->get('column',3);?>">
        <?php foreach($items as $item):?>
            <?php foreach($item as $i):?>
                <li class="<?php echo XEFXpertGalleryHelper::getCatNameAsClass( $i, $params ) ; ?>">
                    <div class="tx-gallery-item">
                        <div class="tx-gallery-item-in">

                            <div class="tx-gallery-image">
                                <a href="<?php echo $i->image; ?>">
                                    <img src="<?php echo $i->image;?>" alt="<?php echo $i->title?>">
                                    <span class="tx-gallery-image-overlay"></span>
                                </a>
                                <a class="tx-gallery-image-preview" href="<?php echo $i->image;?>"></a>
                                <a class="tx-gallery-image-link" href="<?php echo $i->link . '/?tmpl=component'; ?>"></a>
                            </div>

                            <?php if( $params->get('overview', 1) ):?>
                                <div class="tx-gallery-info">
                                    <?php if( in_array('title', $overview_elements) ): ?>
                                        <h2 class="tx-gallery-title">
                                            <a href="<?php echo $i->link; ?>"> <?php echo $i->title; ?> </a>
                                        </h2>
                                    <?php endif;?>

                                    <?php if( in_array('date', $overview_elements)
                                       OR in_array('intro', $overview_elements)
                                       OR in_array('readmore', $overview_elements) ): ?>
                                    <div class="tx-gallery-content clearfix">

                                        <?php if( in_array('date', $overview_elements) ) :?>
                                            <div class="tx-gallery-date">
                                                <span><?php echo $i->date;?></span>
                                            </div>
                                        <?php endif; ?>

                                        <?php if( in_array('intro', $overview_elements) ) :?>
                                            <div class="tx-gallery-intro">
                                                <?php echo $i->introtext; ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if( in_array('readmore', $overview_elements) ) :?>
                                            <div class="tx-gallery-readmore">
                                                <a class="btn" href="<?php echo $i->link; ?>">Read More</a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php endif; ?>

                                </div>
                            <?php endif;?>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </ul>
</div>
<!--ThemeXpert: Xpert Gallery module 2.2 End Here-->