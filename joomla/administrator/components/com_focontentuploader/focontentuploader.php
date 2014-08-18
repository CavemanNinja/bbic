<?php
/**
 * Copyright (C) 2014 freakedout (www.freakedout.de)
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
**/

// no direct access
defined('_JEXEC') or die( 'Restricted access' );

if (JRequest::getVar('format') != "raw") {
    $document = JFactory::getDocument();

    if (version_compare(JVERSION, '3.0.0') >= 0){
        JHtml::_('bootstrap.framework');
        JHtml::_('behavior.tabstate');
    } else {
        $document->addScript(JURI::root(true) . '/media/com_focontentuploader/js/jquery.1.8.3.min.js');
        $document->addScript(JURI::root(true) . '/media/com_focontentuploader/js/jquery-noconflict.js');
        $document->addScript(JURI::root(true) . '/media/com_focontentuploader/js/bootstrap.min.js');
        $document->addStyleSheet(JURI::root(true) . '/media/com_focontentuploader/css/bootstrap.min.css');
        $document->addScript(JURI::root(true) . '/media/com_focontentuploader/js/tabs-state.js');
        JHtml::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/html');
    }

    $document->addStyleSheet(JURI::root(true) . '/media/com_focontentuploader/css/style.css');
    $document->addStyleSheet(JURI::root(true) . '/media/com_focontentuploader/css/bootstrap-fileupload.min.css');
    $document->addStyleSheet(JURI::root(true) . '/media/com_focontentuploader/css/bootstrap-select.min.css');
    $document->addScript(JURI::root(true) . '/media/com_focontentuploader/js/bootstrap-fileupload.min.js');
    $document->addScript(JURI::root(true) . '/media/com_focontentuploader/js/bootstrap-select.min.js');
    $document->addScript(JURI::root(true) . '/media/com_focontentuploader/js/date.js');
    $document->addScript(JURI::root(true) . '/media/com_focontentuploader/js/jquery.datePicker.js');
    $document->addStyleSheet(JURI::root(true) . '/media/com_focontentuploader/css/jquery.datepicker.css');
}

JHtml::_('behavior.tooltip');
JHtml::_('behavior.calendar');

JLoader::register('cuModel', JPATH_COMPONENT . '/models/cuModel.php');
JLoader::register('cuView', JPATH_COMPONENT . '/views/cuView.php');
JLoader::register('cuController', JPATH_COMPONENT . '/controllers/cuController.php');

require_once(JPATH_COMPONENT . '/controller.php');
$controller = JRequest::getVar('controller');

if ($controller) {
    $path = JPATH_COMPONENT . '/controllers/' . $controller . '.php';
    if (file_exists($path)) {
        require_once($path);
    } else {
        $controller = '';
    }
}

require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/helper.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/helper.php');

$activeView = JRequest::getVar('view', 'focontentuploader');
$submenu = array();
$submenu['CU_ARTICLES']   = 'focontentuploader';
$submenu['K2']            = 'k2';
$submenu['CU_WEB_LINKS']  = 'weblinks';
$submenu['CU_CONTACTS']   = 'contact';
$submenu['CU_JOOMFISH']  = 'joomfish';
$submenu['CU_ADMINTOOLS'] = 'admintools';

foreach ($submenu as $name => $view) {
	JSubMenuHelper::addEntry(JText::_($name), 'index.php?option=com_focontentuploader&view=' . $view, $view == $activeView);
}

JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_focontentuploader/tables');

$classname  = 'foContentUploaderController' . $controller;
$controller = new $classname();
$controller->execute(JRequest::getVar('task'));
$controller->redirect();
