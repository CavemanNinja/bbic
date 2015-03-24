<?php
/**
* @package     	2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   	Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.2 $
**/


defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.filesystem.folder');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldTwoJToolBoxFolderList extends JFormFieldList{
	public $type = 'TwoJToolBoxFolderList';
	protected function getOptions(){
		$options = array();
		$filter			= (string) $this->element['filter'];
		$path = JPATH_ROOT.'/'."media".'/'."com_twojtoolbox";
		$folders = JFolder::folders($path, $filter, true, true);
		$path_ch = str_replace('\\', '/', $path);
		$path_ch = str_replace('/', '/', $path_ch);
		$options[] = JHtml::_('select.option', '', '/');
		if (is_array($folders)) {
			foreach($folders as $folder) {
				$folder = str_replace('\\', '/', $folder);
				$folder_name = str_replace($path_ch, '', $folder);
				$folder_id = str_replace($path_ch.'/', '', $folder);
				
				$options[] = JHtml::_('select.option', $folder_id, $folder_name);
			}
		}
		$options = array_merge(parent::getOptions(), $options);
		return $options;
	}
}
