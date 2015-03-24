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


class JFormFieldTwojtoolboxtype extends JFormFieldList{

	protected $type = 'twojtoolboxtype';

	protected function getInput(){
		include_once( JPATH_ADMINISTRATOR.'/components/com_twojtoolbox/twojclass.php');
		JLoader::register('TwojToolboxHTMLHelper', JPATH_SITE.'/administrator/components/com_twojtoolbox/helpers/twojtoolboxHTML.php');
		
		$document = JFactory::getDocument();
		$application = JFactory::getApplication();
		$document->addScriptDeclaration(
			TwojToolboxHTMLHelper::baseDialogOptions().'
			var  name_syff = "'.($application->scope == 'com_modules' ? 'params' :'request').'";
		');
		$document->addScript("index.php?option=com_twojtoolbox&task=getjs&need=init2jbrs2menuelement&format=raw&name=2jscript.js");
		$document->addStyleSheet("index.php?option=com_twojtoolbox&task=getcss&need=admin&format=raw&name=2j.style.css");
		return parent::getInput();
	}
	
	protected function getOptions(){
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('id,type,title');
		$query->from('#__twojtoolbox_plugins');
		$query->order('install DESC, ordering DESC, adddate DESC, title');
		$query->where('install = 1');
		$db->setQuery((string)$query);
		$plugins = $db->loadObjectList();
		$options = array();
		if ($plugins) foreach($plugins as $plugin) $options[] = JHtml::_('select.option', $plugin->type, $plugin->title );
		$options = array_merge(parent::getOptions(), $options);
		array_unshift($options, JHtml::_('select.option', '0', JText::_('COM_TWOJTOOLBOX_SELECT')));
		return $options;
	}
}
