<?php
/**
* @package     	2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   	Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.2 $
**/

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');
jimport('joomla.application.component.helper');

include_once( JPATH_ADMINISTRATOR.'/components/com_twojtoolbox/twojclass.php' );

JLoader::register('TwojToolboxHelper', JPATH_COMPONENT_ADMINISTRATOR.'/helpers/twojtoolbox.php');
JLoader::register('TwojToolBoxSiteHelper', JPATH_COMPONENT.'/helpers/twojtoolboxsite.php');

if (TJTB_JVERSION==0){
	JToolBarHelper::title(JText::_('COM_TWOJTOOLBOX_VERSION_ERROR'), 'twojtoolbox');
	echo '<h3>'.JText::_('COM_TWOJTOOLBOX_VERSION_ERROR_DESC').'</h3>';
	return '';
}

$controller = TwojController::getInstance('TwoJToolbox');
if(TJTB_JVERSION==2){
	$controller->execute(JRequest::getCmd('task'));
}else {
	$controller->execute(JFactory::getApplication()->input->get('task'));
}
$controller->redirect();
