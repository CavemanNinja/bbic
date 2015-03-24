<?php
/**
 * FW Gallery 3.1.0
 * @copyright (C) 2014 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined('_JEXEC') or die('Restricted access');
$comments = JPATH_SITE.'/components/com_komento/bootstrap.php';

if (is_file($comments)) {
	$src_path = JPATH_SITE.'/components/com_fwgallery/helpers/com_fwgallery.php';
	$dst_path = JPATH_SITE.'/components/com_komento/komento_plugins/com_fwgallery.php';
	jimport('joomla.filesystem.file');
	if (!file_exists($dst_path) and !JFile :: copy($src_path, $dst_path)) {
		echo JText :: _('FWG_CANT_COPY_KOMENTO_HELPER');
	}
	if (file_exists($dst_path)) {
		require_once($comments);

		$article = new stdclass;
		$article->id = $this->row->id;
		$article->catid = $this->row->project_id;
		$article->introtext = '';
		$article->text = $this->row->descr;

		echo Komento::commentify('com_fwgallery', $article, $options=array());
	}
} else echo JText :: _('FWG_KOMENTO_NOT_INSTALLED');
