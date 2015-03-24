<?php
/**
* @package     	2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   	Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.2 $
**/


defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modeladmin');

class TwojToolboxModelPlugin extends JModelAdmin{

	public function getTable($type = 'Plugin', $prefix = 'TwojToolboxTable', $config = array()) 
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	public function save($data){	
		//$data['params'] =  JRequest::getVar( 'params', '', 'post', 'ARRAY', JREQUEST_ALLOWRAW );
		return parent::save($data);
	}
	
	public function getForm($data = array(), $loadData = true) {
		// Get the form.
		$form = $this->loadForm('com_twojtoolbox.plugin', 'plugin', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) 
		{
			return false;
		}
		return $form;
	}
	
	
	public function getInstall() 
	{
		return 'ggg';
		//return 'administrator/components/com_twojtoolbox/models/forms/plitem.js';
	}

	protected function loadFormData(){		 
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_twojtoolbox.edit.plugin.data', array());
		if (empty($data)) 
		{
			$data = $this->getItem();
			//print_r($data);
			$selecttype = JRequest::getCmd('selecttype', '');
			if($selecttype) $data->type =  $selecttype;
		} else {
			TwojToolboxHelper::cgid( $data->id ); 
		}
		return $data;
	}
	
}
