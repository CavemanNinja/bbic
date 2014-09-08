<?php
/**
* @package     	2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   	Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.10 $
**/
defined('_JEXEC') or die('Restricted access');
if (!JFactory::getUser()->authorise('core.manage', 'com_twojtoolbox')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}
include_once( JPATH_ADMINISTRATOR.'/components/com_twojtoolbox/twojclass.php' );

if (TJTB_JVERSION==0){
	JToolBarHelper::title(JText::_('COM_TWOJTOOLBOX_VERSION_ERROR'), 'twojtoolbox');
	echo '<h3>'.JText::_('COM_TWOJTOOLBOX_VERSION_ERROR_DESC').'</h3>';
	return '';
}

$controller = TwojController::getInstance('TwojToolbox');
if(TJTB_JVERSION==2){
	$task = JRequest::getCmd('task');
}else {
	$task = JFactory::getApplication()->input->get('task');
}
$controller->execute($task);
$controller->redirect();