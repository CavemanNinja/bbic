<?php
/**
* @package     2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.2 $
**/


defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controllerform');

class TwojToolboxControllerPlitem extends JControllerForm{

	
	public function save ($key = null, $urlVar = null){
		$task		= $this->getTask();
		$data		= JRequest::getVar('jform', array(), 'post', 'array');
		
		$com_twojtoolbox_item_openTab = (int) JRequest::getVar('com_twojtoolbox_item_openTab', -1, 'post', 'int');
		if( $com_twojtoolbox_item_openTab != -1 ) JFactory::getApplication()->setUserState('com_twojtoolbox.edit.plitem.openTab', $com_twojtoolbox_item_openTab);

		if( $task=='save2copy' &&  count($data) && isset($data['title']) ){
			$data['title'] = 'Copy of '.$data['title'];
			JRequest::setVar('jform', $data, 'post', 'array');
		}
		if( $task=='save2new' &&  count($data) && isset($data['type']) ){
			JFactory::getApplication()->setUserState('com_twojtoolbox.edit.plitem.save2new.type', $data['type']);
		}
		return parent::save($key, $urlVar);
	}
	
	public function edit($key = null, $urlVar = null){
		if( JRequest::getVar('default', 0, 'post', 'integer') == 1 ){
			JFactory::getApplication()->setUserState('com_twojtoolbox.edit.plitem.default', 1);
		}
		if( $democode = JRequest::getVar('democode', '', 'post', 'string') ){
			JFactory::getApplication()->setUserState('com_twojtoolbox.edit.plitem.democode', $democode);
		}
		return parent::edit($key, $urlVar);
	}
	

}
