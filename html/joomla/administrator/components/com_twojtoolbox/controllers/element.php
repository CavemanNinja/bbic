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
class TwojToolboxControllerElement extends JControllerForm{
	
	public function save ($key = null, $urlVar = null){
		$task		= $this->getTask();
		$data		= JRequest::getVar('jform', array(), 'post', 'array');
		if( $task=='save2copy' &&  count($data) && isset($data['title']) ){
			$data['title'] = 'Copy of '.$data['title'];
			JRequest::setVar('jform', $data, 'post', 'array');
		}
		return parent::save($key, $urlVar);
	}
}
