<?php
/**
 * @package     EXTman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

defined('_JEXEC') or die;

if (!class_exists('Koowa')) 
{
	$link = version_compare(JVERSION, '1.6.0', '>=') ? '&view=plugins&filter_folder=system' : '&filter_type=system';
	return JFactory::getApplication()
		->redirect(JURI::base(), sprintf(JText::_('PLUGIN_ERROR'), JRoute::_('index.php?option=com_plugins'.$link)), 'error');
}

echo KService::get('com://admin/extman.dispatcher')->dispatch();