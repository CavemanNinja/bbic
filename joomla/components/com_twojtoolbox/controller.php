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
	
	public function display($cachable = false, $urlparams = false){
		$cachable = true;
		$safeurlparams = array(	);
		if( JRequest::getInt('twoj_showPlugin', 0) ) $cachable = false; 
		return parent::display($cachable, $safeurlparams);
	}
	
}
