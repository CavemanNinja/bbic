<?php
/**
* @package     	2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   	Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.2 $
**/


defined('_JEXEC') or die;

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');


class JFormFieldTwojtoolboxdisplaytype extends JFormField{

	protected $type = 'twojtoolboxdisplaytype';
	
	protected function getInput()
	{
		return ' ';
	}
	
	protected function getLabel(){
		$html = array();
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('id,title,v_active');
		$query->from('#__twojtoolbox_plugins');
		$query->where('type='.$db->quote($this->value) );
		$db->setQuery((string)$query);
		$plugin_inf = $db->loadObject();
		$sad = (int) JFactory::getApplication()->getUserState('com_twojtoolbox.options.sad', 0); 
			$text = $this->element['label'] ? (string) $this->element['label'] : (string) $this->element['name'];
			$text = $this->translateLabel ? JText::_($text) : $text;
			$class = !empty($this->description) ? 'hasTip' : '';
			if (!empty($this->description)) {
				$title = ' title="'.htmlspecialchars(trim($text, ':').'::' .
							($this->translateDescription ? JText::_($this->description) : $this->description), ENT_COMPAT, 'UTF-8').'"';
			}
			if($sad!=-2) $html[] = ' <a href="'.JURI::root().'administrator/index.php?option=com_twojtoolbox&view=plugins&opendialog='.$this->value.'" title="'.$plugin_inf->title.'" target="_blank" id="twojtoolbox_typelabel_text_title">';
			$html[] = '<img border="0" style="height:63px;"  height="63" class="'.$class.'" '.$title.' src="'.JURI::root().'components/com_twojtoolbox/plugins/'.$this->value.'/'.$plugin_inf->v_active.'/mid_logo.png" />';
			if($sad!=-2) $html[] = '</a>';
		$html[] = '<input type="hidden" name="'.$this->name.'" value="'.$this->value.'" />';
		return implode('',$html);
	}
}
