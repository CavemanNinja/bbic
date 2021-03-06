<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>

<? // Categories ?>
<div class="docman_categories">
    <? foreach ($categories as $category): ?>

    <? // Category ?>
    <div class="docman_category docman_category--style">

        <? // Header ?>
        <h4 class="koowa_header">

            <? // Header image ?>
            <? if ($params->show_icon && $category->icon): ?>
                <span class="koowa_header__item koowa_header__item--image_container">
                    <? // Link ?>
                    <a class="koowa_header__link" href="<?= helper('route.category', array('entity' => $category)) ?>">
                        <?= import('com://site/docman.document.icon.html', array('icon' => $category->icon, 'class' => ' koowa_icon--24')) ?>
                    </a>
                </span>
            <? endif ?>

            <? // Header title ?>
            <span class="koowa_header__item">
                <span class="koowa_wrapped_content">
                    <span class="whitespace_preserver">
                        <a class="koowa_header__link" href="<?= helper('route.category', array('entity' => $category)) ?>">
                            <?= escape($category->title) ?>
                        </a>
                    </span>
                </span>
            </span>
        </h4>

        <? if ($params->show_image && $category->image): ?>
            <?= helper('behavior.thumbnail_modal'); ?>
            <a class="docman_thumbnail thumbnail" href="<?= $category->image_path ?>">
                <img src="<?= $category->image_path ?>" />
            </a>
        <? endif ?>

        <? // Category description summary ?>
        <? if ($params->show_description && $category->description_summary): ?>
        <div class="docman_description">
            <?= prepareText($category->description_summary); ?>
        </div>
        <? endif ?>

	</div>
    <? endforeach; ?>
</div>