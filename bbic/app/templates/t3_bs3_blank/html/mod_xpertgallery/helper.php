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
defined('_JEXEC') or die;

abstract class XEFXpertGalleryHelper
{

    public static function getCatFilterList($items, $params)
    {
        $html = '';

        // Set the var name based on content source
        switch ( $params->get('content_source', 'joomla') ) {
            case 'k2':
                $cat_alias = 'categoryalias';
                $cat_name = 'categoryname';
                break;
            // Default Joomla
            default:
                $cat_alias = 'category_route';
                $cat_name = 'category_title';
                break;
        }

        foreach( $items as $item )
        {
            $extrafields_attribs_json = $item[0]->attribs;
            $extrafields_attribs = json_decode($extrafields_attribs_json, true);

            if ($extrafields_attribs["companyprofile_approval"] == "1")
                $html .= '<li data-filter=".'. str_replace('/', '-', $item[0]->$cat_alias) .'">' . $item[0]->$cat_name . '</li>' . "\n";
        }

        return $html ;
    }

    public function getCatNameAsClass( $item, $params )
    {
        switch ( $params->get('content_source', 'joomla') )
        {
            case 'k2':
                $class = $item->categoryalias;
                break;
            
            default:
                $class = str_replace('/', '-', $item->category_route);
                break;
        }
        return $class;
    }

    public static function loadScript($module, $params)
    {
        if( !defined('XPERT_GALLERY') )
        {
            $doc = JFactory::getDocument();
            $js_path = JURI::root(true).'/modules/mod_xpertgallery/assets/js';
            $css_path = JURI::root(true).'/modules/mod_xpertgallery/assets/css';

            $doc->addScript($js_path . '/jquery.isotope.min.js');

            if( !defined('XPERT_POPUP') )
            {
                $doc->addScript($js_path . '/magnific.min.js');
                $doc->addStyleSheet($css_path . '/magnific.css');
                define('XPERT_POPUP',1);
            }

            // Load module script last
            $doc->addScript($js_path . '/script.min.js');

            define('XPERT_GALLERY',1);
        }
    }
}