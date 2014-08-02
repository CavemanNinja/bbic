<?php
/**
 * FW Gallery 3.1.0
 * @copyright (C) 2014 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined('_JEXEC') or die('Restricted access');

$comments = JPATH_SITE . '/components/com_jcomments/jcomments.php';

if (file_exists($comments)) {
	require_once($comments);
	echo JComments::show($this->row->id, 'com_fwgallery', $this->row->name);
} else {
	echo JText :: _('FWG_JCOMMENTS_NOT_INSTALLED');
}
