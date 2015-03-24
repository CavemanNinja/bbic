<?php
/**
* @package 2JToolBox 2JGallery
* @Copyright (C) 2012 2Joomla.net
* @ All rights reserved
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version $Revision: 2.0.0 $
**/

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

defined('_JEXEC') or die('Restricted access');

if (!JFactory::getUser()->authorise('core.manage', 'com_twojtoolbox')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}
if(!JFolder::exists(JPATH_ADMINISTRATOR.'/components/com_twojtoolbox/')){
	echo '<h3>'.JText::_("This component requires installation of the 2JToolBox - Framework component. Please make sure that you installed 2JToolBox at your website. You can download it from <a href='http://www.2joomla.net/products' target='_blank'>2JoomlaNet Memberplace Section</a> If you have any questions contact <a href='http://www.2joomla.net' target='_blank'>2JoomlaNet Team</a>").'</h3>';
	return ;
}
$lang = JFactory::getLanguage();
$extension = 'com_twojtoolbox';
$base_dir = JPATH_ADMINISTRATOR;
$lang->load($extension, $base_dir, null, true);
$document = JFactory::getDocument();

include_once(JPATH_ADMINISTRATOR.'/components/com_twojtoolbox/twojclass.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_twojtoolbox/helpers/twojtoolbox.php');

JToolBarHelper::title('2J Gallery'); 
$document->setTitle( $document->getTitle().' - '."2JToolBox");
JHtml::_('behavior.tooltip');

echo '<div id="about_content"  style="width: 500px; margin: 0 auto;" >'.TwojToolboxHelper::about( 'gallery', !(bool) JFactory::getApplication()->getUserState('com_twojtoolbox.options.after_install', '0') ).'</div>';
$document->addScriptDeclaration("
	emsajax(document).ready(function(){
		emsajax('#toolbar-box').remove();
		emsajax('div.subhead-collapse').remove();
		emsajax('.twojbutton').button();
	});
");
TwojToolboxHelper::adminAddScript( array('init','ui.core','ui.position','ui.widget','ui.dialog','ui.button'), 'js');
TwojToolboxHelper::adminAddScript( array('admin.helper','admin.color','admin','ui'));

