<?php
/**
* @package     	2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   	Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.2 $
**/


defined('_JEXEC') or die('Restricted access');

jimport('joomla.database.table');

class TwojToolboxTableConfig extends JTable{

	function __construct(&$db){
		parent::__construct('#__twojtoolbox_config', 'id', $db);
	}
	
	protected function _getAssetName(){
		$k = $this->_tbl_key;
		return 'com_twojtoolbox.config.'.(int) $this->$k;
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
