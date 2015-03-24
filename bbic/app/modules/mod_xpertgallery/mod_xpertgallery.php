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

// Check for the framework, if not found eject
include_once JPATH_LIBRARIES . '/xef/bootstrap.php';

if( !defined('XEF_INCLUDED'))
{
    echo 'Your Module installation is broken; please re-install. Alternatively, extract the installation archive and copy the xef directory inside your site\'s libraries directory.';
    return ;
}

// Include the syndicate functions only once
require_once (dirname(__FILE__). '/helper.php');

//set module id
$module_id = XEFUtility::getModuleId($module, $params);

// Content source
$content_source = $params->get('content_source','joomla');

// Import source and get the class name
$class_name = importSource($content_source);

// Create instance of the class
$instance = new $class_name($module, $params);

// Lets set some variables
$cat_ids = array();
$cat_field = '';
$items = array();
// assign ids based on contetn_source
switch ( $content_source ) {
	case 'joomla':
		$cat_ids = $params->get('jom_catid');
		$cat_field = 'jom_catid';
		// Set cat filter alwasy 1
		$instance->set( 'jomcatfilter', 1 );
		break;
	case 'k2':
		$cat_ids = $params->get('k2_catid');
		$cat_field = 'k2_catid';
		// Set cat_filter alwasy 1
		$instance->set( 'k2_catfilter', 1 );
		break;
	case 'easyblog':
		$cat_ids = $params->get('eb_catid');
		$cat_field = 'eb_catid';
		break;
	default:
		$cat_ids = $params->get('jom_catid');
		$cat_field = 'eb_catid';
		break;
}
// Populate items
foreach ($cat_ids as $id) {
	// Set cat id
	$instance->set( $cat_field, array($id) );
	$items[] = $instance->getItems();
}

// Load Stylesheet file
XEFUtility::loadStyleSheet($module, $params);

// Load Module specific script
XEFXpertGalleryHelper::loadScript($module, $params);

// Load Module specific style defination
//XEFXpertScrollerHelper::load_style($module, $params);

// Assign some variable to use inside views
$overview_elements = $params->get('overview_elements');

require JModuleHelper::getLayoutPath($module->module, $params->get('layout', 'default'));
