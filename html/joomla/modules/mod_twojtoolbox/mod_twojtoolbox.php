<?php
/**
* @package     2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.2 $
**/


defined('_JEXEC') or die;
include_once( JPATH_ADMINISTRATOR.'/components/com_twojtoolbox/twojclass.php' );
JLoader::register('TwojToolBoxSiteHelper', JPATH_SITE.'/components/com_twojtoolbox/helpers/twojtoolboxsite.php');
$module_html = TwojToolBoxSiteHelper::getPluginContent($params->get('id', 0));
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
require JModuleHelper::getLayoutPath('mod_twojtoolbox', $params->get('layout', 'default'));
