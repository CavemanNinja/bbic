<?php
/**
* @package     	2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   	Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.10 $
**/



defined('_JEXEC') or die('Restricted access');

jimport('joomla.database.table');

class TwojToolboxTablePlugin extends JTable{

	
	function __construct(&$db) 
	{
		parent::__construct('#__twojtoolbox_plugins', 'id', $db);
	}

	public function bind($array, $ignore = ''){
		return parent::bind($array, $ignore);
	}
	
	
	public function store($updateNulls = false){
		return parent::store($updateNulls);
	}

	
	protected function _getAssetName(){
		$k = $this->_tbl_key;
		return 'com_twojtoolbox.plugin.'.(int) $this->$k;
	}

	protected function _getAssetTitle(){
		return $this->Title;
	}


	/* protected function _getAssetParentId( $table = null, $id = null ){
		$asset = JTable::getInstance('Asset');
		$asset->loadByName('com_twojtoolbox');
		return $asset->id;
	} */
}
