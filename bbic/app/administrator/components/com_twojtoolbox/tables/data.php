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

class TwojToolboxTableData extends JTable{

	function __construct(&$db){
		parent::__construct('#__twojtoolbox_data', 'id', $db);
	}
	
	public function loadPluginType( $pluginType ){
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select( '`id`' );
		$query->from('#__twojtoolbox_data');
		$query->where('	plugintype = '. $db->quote($pluginType) );
		$db->setQuery( (string) $query );
		$realId = $db->loadResult();
		$this->load($realId);
		$this->plugintype = $pluginType;
	}
	
	public function loadPluginId( $pluginId ){
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select( '`id`' );
		$query->from('#__twojtoolbox_data');
		$query->where('pluginid = '.(int) $pluginId );
		$db->setQuery( (string) $query );
		$realId = $db->loadResult();
		$this->load($realId);
		$this->pluginid = (int) $pluginId;
	}
	
	public function bind($array, $ignore = ''){
		return parent::bind($array, $ignore);
	}
	
	
	public function store($updateNulls = false){
		return parent::store($updateNulls);
	}

	
	protected function _getAssetName(){
		$k = $this->_tbl_key;
		return 'com_twojtoolbox.data.'.(int) $this->$k;
	}

	/* protected function _getAssetParentId( $table = null, $id = null ){
		$asset = JTable::getInstance('Asset');
		$asset->loadByName('com_twojtoolbox');
		return $asset->id;
	} */
}
