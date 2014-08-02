<?php
/**
 * fwgallerytmplvideo x.x.x
 * @copyright (C) 2012 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

class com_fwGalleryTmplVideoInstallerScript {
	function install($adaptor) {
		if (!file_exists(JPATH_SITE.'/components/com_fwgallery/helpers/helper.php')) {
			echo 'FW Gallery not installed';
			return false;
		}
		return true;
	}
	function postflight($type, $adaptor) {
		$db = JFactory :: getDBO();
		$db->setQuery('UPDATE #__extensions SET `enabled` = 0 WHERE `type` = \'component\' AND `element` = \'com_fwgallerytmplvideo\'');
		$db->query();
		return true;
	}
}

?>