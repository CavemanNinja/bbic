<?php
/**
* @package     	2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   	Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.11 $
**/

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modeladmin');

class TwojToolboxModelScan extends JModelAdmin{ 
	
	public function __construct($config = array()){
		parent::__construct($config);
	}
	
	public function getForm($data = array(), $loadData = true){
		$app = JFactory::getApplication();
		$form = $this->loadForm('com_twojtoolbox.scan', 'scan', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		return $form;
	}

	protected function loadFormData(){
		$data = JFactory::getApplication()->getUserState('com_twojtoolbox.display.scan.data', array());
		return $data;
	}
}
