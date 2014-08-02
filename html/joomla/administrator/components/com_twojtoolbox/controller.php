<?php
/**
* @package     	2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   	Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.2 $
**/

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

class TwojToolboxController extends TwojController{
	protected $default_view = 'plitems';
	
	function getjs(){
		JLoader::register('TwojToolBoxSiteHelper', JPATH_SITE.'/components/com_twojtoolbox/helpers/twojtoolboxsite.php');
		header('Content-type: text/javascript');
		$document = JFactory::getDocument();
		$document->setType('raw');
		$document->setMimeEncoding('text/javascript');
		$need_files = JRequest::getString('need', '');
		TwojToolBoxSiteHelper::scriptSave( $need_files, 'js', 1);
		die();
	}
	
	function getcss(){
		JLoader::register('TwojToolBoxSiteHelper', JPATH_SITE.'/components/com_twojtoolbox/helpers/twojtoolboxsite.php');
		header('Content-type: text/css');
		$document = JFactory::getDocument();
		$document->setType('raw');
		$document->setMimeEncoding('text/css');
		$need_files = JRequest::getString('need', '');
		TwojToolBoxSiteHelper::scriptSave( $need_files, 'css', 1);
		die();
	}
	
	public function display($cachable = false, $urlparams = false) {
		$view	= JRequest::getCmd('view', 'plitems');
		$layout = JRequest::getCmd('layout', 'default');
		$id		= JRequest::getInt('id');
		
		//JFactory::getApplication()->setUserState('com_twojtoolbox.options.sad', 0);
		$sad = JComponentHelper::getParams('com_twojtoolbox')->get('sad', 0);
		JFactory::getApplication()->setUserState('com_twojtoolbox.options.sad', $sad);
		
		$twojcache = JComponentHelper::getParams('com_twojtoolbox')->get('twojcache', 1);
		JFactory::getApplication()->setUserState('com_twojtoolbox.options.twojcache', $twojcache);
		
		//JFactory::getApplication()->setUserState('com_twojtoolbox.options.twojnews', 0);
		$twojnews = JComponentHelper::getParams('com_twojtoolbox')->get('twojnews', 0);
		JFactory::getApplication()->setUserState('com_twojtoolbox.options.twojnews', $twojnews);
		
		if( ($view=='plitems' || $view =='plugins'|| $view=='news' || $view=='') && $sad!=-2 ) TwojToolboxHelper::addSubmenu($view, $twojnews);
		
		if( !class_exists('plgSystemTwojToolbox', false) && !JFactory::getApplication()->getUserState('com_twojtoolbox.options.after_install', '0') )JRequest::setVar('show_about', 1);
		
		if ($view == 'plitem' && $layout == 'edit' && !$this->checkEditId('com_twojtoolbox.edit.plitem', $id)) {
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_twojtoolbox&view=plitems', false));
			return false;
		} else  if ($view == 'element' && $layout == 'edit' && !$this->checkEditId('com_twojtoolbox.edit.element', $id)) {
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_twojtoolbox&view=elements', false));
			return false;
		}
		parent::display();
		return $this;
	}
	
}
