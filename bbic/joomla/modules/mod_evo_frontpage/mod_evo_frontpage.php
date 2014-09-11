<?php
 

/**
 * @extension       EVO frontpage module
 * @version         2.4.0
 *
 * @author          Pacificador <minifrontpageevolution@gmail.com>
 * @link            http://www.evofrontpage.com/
 * @copyright       Copyright © 2014 All Rights Reserved
 * @license         Released under GNU/GPL License
 */

defined('_JEXEC') or die;

require_once JPATH_SITE.'/components/com_content/helpers/route.php';
require_once dirname(__FILE__).'/helper.php';

$document		= JFactory::getDocument();
$modulebase		= ''.JURI::base(true).'/modules/mod_evo_frontpage/';

$theme = $params->get('theme', 'default');

//Create the temporary definition for TP_DS
if(!defined('TP_DS')) {
	define( 'TP_DS', DIRECTORY_SEPARATOR );
}

$evofpid= $module->id;
$loadJqueryOpt= (int) $params->get('loadJquery', 0);
	
$document = JFactory::getDocument();

// Then load all JS
if( $loadJqueryOpt == 1 ) {
	$document->addScript($modulebase.'library/jquery-1.8.3.min.js');
}
else if( $loadJqueryOpt == 2 ) {
	$document->addScript('http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js');
}

// Set the path definitions
if(!defined('MOD_EVO_FRONTPAGE_BASE')) {
	define('MOD_EVO_FRONTPAGE_BASE', JPATH_SITE.TP_DS.$params->get('image_path', 'images'));
}

if(!defined('MOD_EVO_FRONTPAGE_BASEURL')) {
	define('MOD_EVO_FRONTPAGE_BASEURL', JURI::base().$params->get('image_path', 'images'));
}

if(!defined('MOD_EVO_FRONTPAGE_THUMB_BASE')) {
	define('MOD_EVO_FRONTPAGE_THUMB_BASE', JPATH_SITE.TP_DS.$params->get('image_path', 'images'.TP_DS.'evofp'));
}

if(!defined('MOD_EVO_FRONTPAGE_THUMB_BASEURL')) {
	define('MOD_EVO_FRONTPAGE_THUMB_BASEURL', JURI::base().$params->get('image_path', 'images/evofp'));
}

if(!file_exists(MOD_EVO_FRONTPAGE_THUMB_BASE)) {
	if(mkdir(MOD_EVO_FRONTPAGE_THUMB_BASE)) {
		JPath::setPermissions(MOD_EVO_FRONTPAGE_THUMB_BASE, '0755');
	}
} else {
	if(!is_dir(MOD_EVO_FRONTPAGE_THUMB_BASE)) {
		if(mkdir(MOD_EVO_FRONTPAGE_THUMB_BASE)) {
			JPath::setPermissions(MOD_EVO_FRONTPAGE_THUMB_BASE, '0755');
		}
	}
}
		
// if there's no image in an article, give it a default one - change image name here if you have one
if(!defined('MOD_EVO_FRONTPAGE_DEFAULT_BASE')) {
	define('MOD_EVO_FRONTPAGE_DEFAULT_BASE', JPATH_SITE.TP_DS.$params->get('image_path', 'modules'.TP_DS.'mod_evo_frontpage'.TP_DS.'images'));
}

if(!defined('MOD_EVO_FRONTPAGE_DEFAULT_BASEURL')) {
	define('MOD_EVO_FRONTPAGE_DEFAULT_BASEURL', JURI::base().$params->get('image_path', 'modules/mod_evo_frontpage/images'));
}

if(!defined('MOD_EVO_FRONTPAGE_DEFAULT_IMAGE')) {
	define('MOD_EVO_FRONTPAGE_DEFAULT_IMAGE', 'default_image.png');
}

$thumbnail_position = intval( $params->get( 'thumbnail_position', 1 ) );
$limit_intro = intval( $params->get( 'limit_intro', 200 ) );
$intro_link = intval( $params->get( 'intro_link', 1 ) );
$show_others = intval( $params->get( 'show_others', 1 ) );

$cat_title = intval( $params->get( 'cat_title', 0 ) );
$cat_title_link = intval( $params->get( 'cat_title_link', 1 ) );
$trim_category_title = intval( $params->get( 'trim_category_title', 50 ) );
$trim_article_title = intval( $params->get( 'trim_article_title', 50 ) );
$show_title = intval( $params->get( 'show_title', 1 ) );
$title_link = intval( $params->get( 'title_link', 1 ) );
$show_author = intval( $params->get( 'show_author', 0 ) );
$show_author_type = intval( $params->get( 'show_author_type', 0 ) );
$show_date = intval( $params->get( 'show_date', 0 ) );
$fulllink = $params->get( 'fulllink','' );
$header_title_links = $params->get( 'header_title_links', "" );

$thumb_embed = intval( $params->get( 'thumb_embed', 0 ) );
$thumb_align = intval( $params->get( 'thumb_align', 0 ) );
$thumb_width = intval( $params->get( 'thumb_width', 32 ) );
$thumb_height = intval( $params->get( 'thumb_height', 32 ) );
$aspect = intval( $params->get( 'aspect', 0 ) );

$link_newtab = intval( $params->get( 'link_newtab', 0 ) );
$show_fulllink = intval( $params->get( 'show_fulllink', 0 ) );
$thumb_link = intval( $params->get( 'thumb_link', 0 ) );

//Get the config
$config = JFactory::getConfig();
$tzoffset = $config->get('offset');

$allowed_tags = "<i><b><strong><br><a>"; 
$image_path = $params->get( 'image_path', 'images' );

$list = modEVO_frontpageHelper::getList( $params );

if ($thumb_align == 0) { 
	$alignment = "left"; 
}	
if ($thumb_align == 1) { 
	$alignment = "right"; 
}
if ($thumb_align == 2) { 
	$alignment = "centered"; 
}

$date_type = $params->get( 'date_type' );
$date_format = $params->get( 'date_format' );
$user = JFactory::getUser();

foreach($list as $index => $item){
	if ($thumb_embed) {	
		/* Regex tool for finding image path on img tag - thx to Jerson Figueiredo */	
		//preg_match_all("/<img[^>]*>/Ui", modEVO_frontpageHelper::unhtmlentities(html_entity_decode(htmlentities($item->introtext))), $txtimg);
		preg_match_all("/<img[^>]*>/Ui", $item->introtext, $txtimg);
		if (!empty($txtimg[0])) 
		{
			foreach ($txtimg[0] as $txtimgel) 
			{	
				$item->introtext = str_replace($txtimgel,"",$item->introtext);
				if (preg_match_all("#http#",$txtimgel,$txtimgelsr,PREG_PATTERN_ORDER) > 0) {
					preg_match_all("#src=\"([\-\/\_A-Za-z0-9\.\:]+)\"#",$txtimgel,$txtimgelsr);
					if (!empty($item->thumb_images)) {
						$item->thumb_images = $txtimgelsr[1][0] . "\n" . $item->thumb_images;
					}
					else {
						$item->thumb_images = $txtimgelsr[1][0];
					}
				}
				elseif ( strstr($txtimgel, $image_path) ) {
					if (strstr($txtimgel, 'src="/')) {
						preg_match_all("#src=\"\/" . addslashes($image_path) . "\/([\:\-\/\_A-Za-z0-9\.]+)\"#",$txtimgel,$txtimgelsr);
					}
					else {
						preg_match_all("#src=\"" . addslashes($image_path) . "\/([\:\-\/\_A-Za-z0-9\.]+)\"#",$txtimgel,$txtimgelsr);
					}
					
					if (!empty($item->thumb_images)) {
						$item->thumb_images = $txtimgelsr[1][0] . "\n" . $item->thumb_images;
					}
					else {
						$item->thumb_images = $txtimgelsr[1][0];
					}
				}
			}
		}
	}
	if($limit_intro){
		$item->introtext = preg_replace("#{(.*?)}(.*?){/(.*?)}#s", '', $item->introtext);
		//$item->introtext= preg_replace("/{[^}]*}/","",$item->introtext);
		$item->introtext = fptn_limittext($item->introtext,$allowed_tags,$limit_intro);
	}else{
		$item->introtext = null;
	}

	if ($thumb_embed == 1){
		if (!empty($item->thumb_images)) { 
			$img = strtok($item->thumb_images,"|\r\n"); 
			$class=""; 
			$extra = ' class="evofp-img-'.$alignment.'" alt="'.$item->title.'" title="'.$item->title.'"';  
			fptn_thumb_size($img, $thumb_width, $thumb_height, $image, $extra, $class, $aspect); 
			$item->thumb = $image;
		}
	}else{
		$item->thumb = null;
	}

	switch($date_type) {
		case 1:
			$item->date = ($item->modified != "0000-00-00 00:00") ? $item->modified : $item->created;
			break;
		case 2:
			$item->date = $item->publish_up;
			break;
		case 0:
		default:
			$item->date = $item->created;
			break;
	}

	$item->date = JHTML::_('date', $item->date, $date_format, $tzoffset);

	if($show_author_type) {
		$item->author = htmlspecialchars( $item->author );
	} else {
		if ( !empty( $item->created_by_alias ) ) {
			$item->author = $item->created_by_alias;
		}
	}
	
	if($trim_category_title){
		if(strlen($item->categtitle) > $trim_category_title){
// If your server doesn't support "mb_" (multibyte) functions, replace "mb_substr" with "substr"
			$item->categtitle = mb_substr( $item->categtitle, 0, $trim_category_title ) . '...';
		}
	}

	if($trim_article_title){
		if(strlen($item->title) > $trim_article_title){
// If your server doesn't support "mb_" (multibyte) functions, replace "mb_substr" with "substr"
			$item->title = mb_substr( $item->title, 0, $trim_article_title ) . '...';
		}
	}

	$list[$index] = $item;
}

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
require dirname(dirname(JModuleHelper::getLayoutPath('mod_evo_frontpage'))).TP_DS.'themes'.TP_DS.$theme.TP_DS.'default.php';

?>