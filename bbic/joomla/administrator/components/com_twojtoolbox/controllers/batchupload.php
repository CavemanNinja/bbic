<?php
/**
* @package     2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @�opyright   Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.2 $
**/


defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controllerform');

class TwojToolboxControllerBatchUpload extends TwojController{
	public function send(){
		if (!JFactory::getUser()->authorise('core.create', 'com_twojtoolbox')) {
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
		JRequest::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));
		$model = $this->getModel('BatchUpload');
		$msg = $type = '';
		if ( !$model->send() ) {
			$type = 'error';
			$msg = $model->getError();
		}
		
		$this->setredirect('index.php?option=com_twojtoolbox&view=elements', $msg, $type);
	}
}
