<?php
/**
* @package     2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.10 $
**/

defined('_JEXEC') or die;

abstract class TwojToolboxHTMLHelper{	
	
	public static function getVersion(){
		$version = TwojToolboxHelper::getTBVersion();
		$returnHTML = (TJTB_JVERSION==2?'<br />':'');
		$returnHTML .= '<div class="twojtoolbox_toolbox_version">2JToolBox <span>v'.TwojToolboxHelper::perseVersion($version->version).'</span>';
		$returnHTML .= ( isset($version->version_available) && $version->version_available > $version->version ? 
				'<a href="http://www.2joomla.net/products_info/goto.php?content=update&type=toolbox" class="" target="_blank">
					<span class="twojtoolbox_needupdate" style="color:red">['. JText::_( 'COM_TWOJTOOLBOX_PLUGINS_TOOLBOX_VER_AV' ).' v'.
						TwojToolboxHelper::perseVersion($version->version_available).
					']</span>
				  </a>&nbsp;&nbsp;' : '&nbsp;&nbsp;|&nbsp;&nbsp;' );
		$returnHTML .= 'Powered by <a href="http://www.2joomla.net">2Joomla.net</a></div>';
		return $returnHTML;				
	}
	
	public static function baseDialogOptions(){
		$returnHTML = '';
		$returnHTML .=" var twoj_baseDialogOptions = {
			'draggable': false, 'modal': true, 'resizable': false, 'width': 500, 'maxHeight': 600, 'autoOpen': false, 'zIndex': 11111,
			'position': { my: 'center top', at: 'center top+35'},
			'buttons': [{ text: '".JText::_('COM_TWOJTOOLBOX_CLOSEDIALOG', 1)."', click: function() { emsajax(this).dialog('close'); } }]
		};
			var twojtoolbox_rooturl = '".JURI::root()."';
			var TJTB_JVERSION	= ".TJTB_JVERSION.";
		;";
		return $returnHTML;
	}
}
