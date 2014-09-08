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


class TwojToolboxModelElement extends JModelAdmin{

	public function getTable($type = 'Element', $prefix = 'TwojToolboxTable', $config = array()){
		return JTable::getInstance($type, $prefix, $config);
	}
	
	public function getForm($data = array(), $loadData = true){
		$form = new JForm('com_twojtoolbox.element', array('control' => 'jform', 'load_data' => $loadData));
		$item_id = TwojToolboxHelper::cgid( );
		$pl_info = TwojToolboxHelper::plugin_info($item_id);
		if( $pl_info){
			$xml_p = JPATH_COMPONENT_SITE.'/plugins/'.$pl_info->type.'/'.$pl_info->v_active.'/element_options.xml';
			if(JFile::exists($xml_p)){
				$form->loadFile($xml_p);
			} else {
				JFactory::getApplication()->redirect( 'index.php?option=com_twojtoolbox&view=elements&catid='.$item_id, JText::_( 'COM_TWOJTOOLBOX_ERROR_OPTIONLIST' ), 'error' );
				return false;
			}
		}
		if (empty($form)) return false;
		if (empty($data)) $data = $this->loadFormData();
		$form->bind($data);
		return $form;
	}
	
	public function getItemC(){
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->where('id = '.(int) TwojToolboxHelper::cgid( ));
		$query->from('#__twojtoolbox');
		$db->setQuery($query);
		return $db->loadObject();
	}
	
	protected function loadFormData(){		  
		$data = JFactory::getApplication()->getUserState('com_twojtoolbox.edit.element.data', array());
		if (empty($data)){
			$data = $this->getItem();
			if ($this->getState('element.id') == 0) {
				$app = JFactory::getApplication();
				$data->set('catid', TwojToolboxHelper::cgid( )); 
			}
		}
		return $data;
	}
	
}
