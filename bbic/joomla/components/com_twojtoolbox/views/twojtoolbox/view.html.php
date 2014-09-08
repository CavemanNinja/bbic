<?php
/**
* @package     	2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   	Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.2 $
**/


defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
class TwojtoolboxViewTwojtoolbox extends TwojJView{

	protected $item;
	protected $params;
	protected $pageclass_sfx;
	protected $return_page;
	
	
	function display($tpl = null){
		
		
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu();
		$pathway	= $app->getPathway();
		$title 		= null;
		
		$active	= $menus->getActive();
		
		if (count($errors = $this->get('Errors'))) {
			JError::raiseWarning(500, implode("\n", $errors));
			return false;
		}
		
		if( isset($active->params) ) {
			$this->params = $active->params;
		}
		
		if(  !isset($this->params) ){
			echo JText::_('COM_TWOJTOOLBOX_ERROR_NOID');
			return '';
		}
		
		
		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));
		

		if ($active) {
			$this->params->def('page_heading',  $active->title);
		}
		else {
			$this->params->def('page_heading', JText::_('COM_TWOJTTOOLBOX_DEFAULT_PAGE_TITLE'));
		}
		
		$title = $this->params->get('page_title', '');
		
		$id = (int) @$active->query['id'];
		
		$this->item = TwojToolBoxSiteHelper::getPluginContent($id);
	
		if($title==null) $title = $app->getCfg('sitename');

		if ($app->getCfg('sitename_pagetitles', 0)) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}

		$this->document->setTitle($title);
		
		

		if ( $this->params->get('menu-meta_description')){
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords')){
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots')){
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}

		if ($app->getCfg('MetaTitle') == '1') {
			$this->document->setMetaData('title', $this->escape($this->params->get('page_heading')));
		}

		

		parent::display($tpl);
	}
	
}
