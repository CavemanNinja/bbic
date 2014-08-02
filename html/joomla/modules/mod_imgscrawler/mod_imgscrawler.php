<?php
/*------------------------------------------------------------------------
# mod_imgscrawler - Images Crawler
# ------------------------------------------------------------------------
# author    Joomla!Vargas
# copyright Copyright (C) 2010 joomlahill.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomlahill.com
# Technical Support:  Forum - http://www.joomlahill.com/forum
-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

global $imgscrawler_id;

if ( !$imgscrawler_id ) : $imgscrawler_id =1; endif;

// Include the syndicate functions only once
//require_once __DIR__ . '/helper.php';
require_once dirname(__FILE__) . '/helper.php';

$folder	= modImgsCrawlerHelper::getFolder($params);
$images	= modImgsCrawlerHelper::getImages($params, $folder);

if (!count($images)) {
	echo JText::_( 'No images ');
	return;
}

require JModuleHelper::getLayoutPath('mod_imgscrawler', $params->get('layout', 'default'));

$imgscrawler_id++;
