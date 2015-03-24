<?php
/**
 * fwgallery y x.x.x
 * @copyright (C) 2012 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

function com_install() {
	if (!file_exists(JPATH_SITE.'/components/com_fwgallery/helpers/helper.php')) {
		echo 'FW Gallery not installed';
		return false;
	}
	require_once(JPATH_SITE.'/components/com_fwgallery/helpers/helper.php');

	$db = JFactory :: getDBO();
	$db->setQuery('UPDATE #__extensions SET enabled = 0 WHERE `type` = \'component\' AND element = \'com_fwgallerytmplphotographer\'');
	$db->query();
}
?>