<?php
/**
* @package     	2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   	Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.10 $
**/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('joomla.application.component.view');
jimport('joomla.application.component.model');

JLoader::register('TwojToolboxHelper', 		JPATH_SITE.'/administrator/components/com_twojtoolbox/helpers/twojtoolbox.php');
JLoader::register('TwojToolboxHTMLHelper', 	JPATH_SITE.'/administrator/components/com_twojtoolbox/helpers/twojtoolboxHTML.php');
JLoader::register('TwojToolBoxSiteHelper', 	JPATH_SITE.'/components/com_twojtoolbox/helpers/twojtoolboxsite.php');

/* define( "TJTB_JVERSION", 0); return ; */
if( version_compare(JVERSION,'3.0.0','ge') ) {
	define( "TJTB_JVERSION", 3);
} else {
	define( "TJTB_JVERSION", 2);
}
if( version_compare(JVERSION,'2','<') ) define( "TJTB_JVERSION_FULL", '1.7');
	elseif(version_compare(JVERSION,'3','<') )  define( "TJTB_JVERSION_FULL", '2');
		elseif(version_compare(JVERSION,'3.1','<') ) define( "TJTB_JVERSION_FULL", '3');
			elseif(version_compare(JVERSION,'3.2','<') ) define( "TJTB_JVERSION_FULL", '3.1');
				elseif(version_compare(JVERSION,'3.2','>=') ) define( "TJTB_JVERSION_FULL", '3.2');
//echo TJTB_JVERSION_FULL;

if(TJTB_JVERSION==2){
	if( !class_exists( 'TwojController') ){ class TwojController extends JController{} 			}
	if( !class_exists( 'TwojJView') ){ 		class TwojJView extends JView{} 					}
	if( !class_exists( 'TwojJModel') ){ 	class TwojJModel extends JModel{}					}
} else {
	if( !class_exists( 'TwojController') ){ class TwojController extends JControllerLegacy{}	}
	if( !class_exists( 'TwojJView') ){ 		class TwojJView extends JViewLegacy{}  				}
	if( !class_exists( 'TwojJModel') ){  	class TwojJModel extends JModelLegacy{} 			}
}
?>