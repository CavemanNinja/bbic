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
class TwojToolboxTablePlitem extends JTable{

	function __construct(&$db){
		parent::__construct('#__twojtoolbox', 'id', $db);
	}

	public function bind($array, $ignore = ''){
		if (isset($array['params']) && is_array($array['params'] )  ){
			$parameter = new JRegistry;
			$parameter->loadArray($array['params']);
			$array['params'] = (string)$parameter;
		}
		/* if(isset( $array['id'] ) && ( (int)$array['id'] ) ){
			$db	= $this->getDbo();
			$db->setQuery('SELECT itemid FROM #__twojtoolbox_menu WHERE id = '.$array['id'] );
			$array['itemid'] = $db->loadColumn();
		} else $array['itemid'] = -1; */
		return parent::bind($array, $ignore);
	}
	
	
	
	public function store($updateNulls = false){
		if (is_array($this->params)) {
			$registry = new JRegistry();
			$registry->loadArray($this->params);
			$this->params = (string)$registry;
		}
		$this->cacheid = rand(100000, 999999);
		return parent::store($updateNulls);
	}
	
	
	public function publish($pks = null, $state = 1, $userId = 0)
	{
		// Initialise variables.
		$k = $this->_tbl_key;

		// Sanitize input.
		JArrayHelper::toInteger($pks);
		$userId = (int) $userId;
		$state  = (int) $state;

		// If there are no primary keys set check to see if the instance key is set.
		if (empty($pks))
		{
			if ($this->$k) {
				$pks = array($this->$k);
			}
			// Nothing to set publishing state on, return false.
			else {
				$this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
				return false;
			}
		}

		// Get an instance of the table
		$table = JTable::getInstance('Plitem','TwojToolboxTable');

		// For all keys
		foreach ($pks as $pk)
		{
			// Load the banner
			if(!$table->load($pk))
			{
				$this->setError($table->getError());
			}

			// Verify checkout
			if($table->checked_out==0 || $table->checked_out==$userId)
			{
				// Change the state
				$table->state = $state;
				$table->checked_out=0;
				$table->checked_out_time=0;

				// Check the row
				$table->check();

				// Store the row
				if (!$table->store())
				{
					$this->setError($table->getError());
				}
			}
		}
		return count($this->getErrors())==0;
	}
	
	
	
	protected function _getAssetName(){
		$k = $this->_tbl_key;
		return 'com_twojtoolbox.plitem.'.(int) $this->$k;
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
