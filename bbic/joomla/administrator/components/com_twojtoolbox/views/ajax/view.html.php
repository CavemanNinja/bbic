<?php
/**
* @package     	2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   	Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.2 $
**/

defined('_JEXEC') or die('Restricted access');

class TwoJToolboxViewAjax extends TwojJView{

	function display($tpl = null)	{
		if ($this->getLayout() == 'twojtoolboxbutton') {
			$document	= JFactory::getDocument();
			$document->setTitle(JText::_('COM_TWOJTOOLBOX_TWOJTOOLBOXBUTTON_TITLE'));
			$eName		= JRequest::getVar('e_name');
			$eName		= preg_replace( '#[^A-Z0-9\-\_\[\]]#i', '', $eName );
			JFactory::getDocument()->addScriptDeclaration(' 
				var  ename_in = "'.$eName.'";
				var  twojtoolboxbutton_error = "'.JText::_('TWOJTOOLBOXBUTTON_ERROR', 1).'";
			');
			$document->addScript("index.php?option=com_twojtoolbox&task=getjs&format=raw&need=init2jbrs2twojtoolboxbutton&name=2jscript.js");
			$document->addStyleSheet("index.php?option=com_twojtoolbox&task=getcss&format=raw&need=admin&name=2j.style.css");
			$this->items = $this->get('Items');
			parent::display($tpl);
			return;
		}
		parent::display($tpl);
	}
}
