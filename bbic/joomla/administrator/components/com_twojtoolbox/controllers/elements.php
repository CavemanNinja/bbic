<?php
/**
* @package     2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.2 $
**/


defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controlleradmin');
class TwojToolboxControllerElements extends JControllerAdmin
{
	public function getModel($name = 'Element', $prefix = 'TwojToolboxModel', $config = array('ignore_request' => true)) 
	{	
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	public function saveOrderAjax(){
		// Get the input
		$pks = $this->input->post->get('cid', array(), 'array');
		$order = $this->input->post->get('order', array(), 'array');

		// Sanitize the input
		JArrayHelper::toInteger($pks);
		JArrayHelper::toInteger($order);

		// Get the model
		$model = $this->getModel();

		// Save the ordering
		$return = $model->saveorder($pks, $order);

		if ($return)
		{
			echo "1";
		}

		// Close the application
		JFactory::getApplication()->close();
	}
}
