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
<div class="tx-tenants-header">
    <?php $page_lang = JFactory::getLanguage()->get('tag'); ?>
    <?php if ($page_lang == "en-GB") : ?>
        <h1>Tenants</h1>
    <?php else : ?>
        <h1>الشركات</h1>
    <?php endif; ?>
</div>
<!--ThemeXpert: Xpert Gallery module 2.2 Start here-->
<div class="tx-gallery <?php echo $module_id;?> overview-<?php echo $params->get('overview_position');?> clearfix">
    
    <div class="tx-gallery-header">
        <ul class="tx-gallery-filters">
            <li data-filter="*" class="active">
                <?php 
                    if ($page_lang == "ar-AA")
                        echo "جميع";
                    else
                        echo JText::_('ALL');
                ?>
            </li>
            <?php
                $cat_tags = XEFXpertGalleryHelper::getCatFilterList( $items, $params );
                // var_dump($cat_tags);
                if ($page_lang == "ar-AA") {
                    $cat_tags = str_replace("Advertising", "دعاية والإعلان", $cat_tags);
                    $cat_tags = str_replace("Cleaning", "التنظيف", $cat_tags);
                    $cat_tags = str_replace("Consulting", "إستشارة", $cat_tags);
                    $cat_tags = str_replace("Contracting", "مقاولات", $cat_tags);
                    $cat_tags = str_replace("Delivery", "خدمات التوصيل", $cat_tags);
                    $cat_tags = str_replace("Design", "تصميم", $cat_tags);
                    $cat_tags = str_replace("Event Management", "تنظيم المناسبات", $cat_tags);
                    $cat_tags = str_replace("Fabrication and Manufacturing", "إنتاج و تصنيع", $cat_tags);
                    $cat_tags = str_replace("Food and Drink", "مواد غذائية و مشروبات", $cat_tags);
                    $cat_tags = str_replace("Furniture", "أثاث", $cat_tags);
                    $cat_tags = str_replace("Industrial", "الصناعية", $cat_tags);
                    $cat_tags = str_replace("Landscaping", "المناظر الطبيعية", $cat_tags);
                    $cat_tags = str_replace("Maintenance and Repairs", "تصليح و صيانة", $cat_tags);
                    $cat_tags = str_replace("Marine Services", "خدمات بحرية", $cat_tags);
                    $cat_tags = str_replace("Media", "إعلام", $cat_tags);
                    $cat_tags = str_replace("Recreational", "الترفيهية", $cat_tags);
                    $cat_tags = str_replace("Retail", "التجارية‎", $cat_tags);
                    $cat_tags = str_replace("Societies", "جمعيات", $cat_tags);
                    $cat_tags = str_replace("Technology", "تكنولوجيا", $cat_tags);
                    $cat_tags = str_replace("Trading", "البيع و الشراء", $cat_tags);
                    $cat_tags = str_replace("Transport", "خدمات النقل", $cat_tags);
                }
                
                /*Remove li for empty categories*/
                // var_dump($cat_tags);
                $cat_tags = str_replace('<li data-filter="."></li>', "", $cat_tags);

                echo $cat_tags;
            ?>
        </ul>

        <?php if($params->get('sort_enabled', 1)): ?>
        <ul class="tx-gallery-sort" >
            <?php foreach($params->get('sort_elements') as $sitem):?>
                <?php $selected = ($sitem == 'original-order') ? 'active' : ''; ?>
                <li data-sort="<?php echo $sitem; ?>" class="<?php echo $selected?>">
                    <?php 
                        $english_text = JText::_( strtoupper($sitem));

                        if ($english_text == 'Title' && $page_lang == "ar-AA") {
                            echo "اسم";
                        } else {
                            echo $english_text;
                        }
                    ?>
                </li>
            <?php endforeach;?>
        </ul>
        <?php endif;?>
    </div>

    <ul class="tx-gallery-container tx-gallery-columns-<?php echo $params->get('column',3);?>">
        <?php foreach($items as $item):?>
            <?php foreach($item as $i):?>
                <?php 
                    $extrafields_attribs_json = $i->attribs;
                    $extrafields_attribs = json_decode($extrafields_attribs_json, true);
                    $item_lang = $extrafields_attribs["companyprofile_language"];
                    $graduated = $extrafields_attribs["companyprofile_graduated"];
                    
                    if (($extrafields_attribs["companyprofile_approval"] == "1") &&
                        ( $item_lang == '0' || ($page_lang == "en-GB" && $item_lang == '1') || ($page_lang == "ar-AA" && $item_lang == '2') ))
                        :
                ?>
                <li class="<?php echo XEFXpertGalleryHelper::getCatNameAsClass( $i, $params ) ; ?>">
                    <div class="tx-gallery-item">
                        <div class="tx-gallery-item-in">
                            
                            <?php if ($graduated == 1) : ?>
                                <div class="tx-gallery-grad"><img src="images/grad.png"></div>
                            <?php endif; ?>
                            
                            <div class="tx-gallery-image">
                                <!-- <a href="<?php echo $i->image; ?>"> -->
                                    <img class="img-thumbnail" src="<?php echo $extrafields_attribs['companyprofile_logo'];?>" alt="<?php echo $i->title?>">
                                    <!-- <img class="img-thumbnail" src="<?php echo $i->image;?>" alt="<?php echo $i->title?>"> -->
                                    <!-- <span class="tx-gallery-image-overlay"></span> -->
                                <!-- </a> -->
                                <!-- <a class="tx-gallery-image-preview" href="<?php echo $i->image;?>"></a> -->
                                <!-- <a class="tx-gallery-image-link" href="<?php echo $i->link . '/?tmpl=component'; ?>"></a> -->
                            </div>

                            <?php if( $params->get('overview', 1) ):?>
                                <div class="tx-gallery-info">
                                    <?php if( in_array('title', $overview_elements) ): ?>
                                        <h2 class="tx-gallery-title">
                                            <!-- <a class="tx-gallery-image-link" href="<?php echo $i->link . '/?tmpl=component'; ?>"> <?php echo $i->title; ?> </a> -->
                                            <?php 
                                                $ilink = $i->link;
                                                if (JFactory::getLanguage()->get('tag') == "ar-AA") {
                                                    /***ADD ANY ARABIC COMPANY SUBCATEGORIES HERE ***/
                                                    $arabic_subcategories = ['35', '36'];
                                                    foreach ($arabic_subcategories as $ar_sub) {
                                                        $ilink = str_replace("/2014-09-24-14-12-37/".$ar_sub."-arabic", "", $ilink);    
                                                    }
                                                }
                                            ?>
                                            <a class="" href="<?php echo $ilink; ?>"> <?php echo $i->title; ?> </a>
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
            <?php endif; ?>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </ul>
</div>
<!--ThemeXpert: Xpert Gallery module 2.2 End Here-->