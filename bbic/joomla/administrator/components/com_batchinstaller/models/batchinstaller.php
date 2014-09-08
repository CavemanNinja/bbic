<?php
/**
 * FW Installer 1.0.0 - Joomla! Property Manager
 * @copyright (C) 2014 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined('_JEXEC') or die('Restricted access');

class batchinstallerModelbatchinstaller extends JModelLegacy {
	function getPackages() {
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		$data = array();
		$path = str_replace('\\', '/', JPATH_ADMINISTRATOR.'/components/com_batchinstaller/data');
		$files = JFolder :: files($path, '\.zip$', $recurse = true, $full = true);
		
		$info = file_get_contents(JPATH_SITE.'/administrator/components/com_batchinstaller/data/files.xml');
		$xmlcont = new SimpleXMLElement($info);

		foreach ($files as $i=>$file) {
			$row = (object)array(
				'filename'=>'',
				'version'=>'',
				'installed_version'=>'',
				'title'=>'',
				'link'=>'',
				'image'=>'',
				'description'=>''
			);
			
			$key = '';
			$filename = '';
			$file = str_replace($path.'/', '', str_replace('\\', '/', $file));
			$buff = explode('/', $file, 2);

			if (count($buff) == 1) {
				$key = 'Components';
				$filename = $file;
			} else {
				$key = ucfirst(strtolower($buff[0]));
				$filename = $buff[1];
			}

			if (!empty($xmlcont->file)) {
				foreach ($xmlcont->file as $info) {
					if (preg_match("#{$info->mask}(_v.*)#i", $filename, $matches)) {
						$row->version = trim(JFile :: stripExt($matches[1]), '_v');
						$xml_path = trim(str_replace('\\', '/', $info->xmlpath), '/');
						if ($xml_path and file_exists(JPATH_SITE.'/'.$xml_path)) {
							if ($xml_data = file_get_contents(JPATH_SITE.'/'.$xml_path)) {
								if (preg_match('#<version>(.*?)</version>#i', $xml_data, $matches)) {
									$row->installed_version = $matches[1];
								}
							}
						}
						$row->filename = $file;
						$row->title = (string)$info->title;
						$row->image = (string)$info->image;
						$row->link = (string)$info->link;
						$row->description = (string)$info->description;
						break;
					}
				}
			}
			if (!$row->title) $row->title = $filename;
			if (!$row->installed_version) $row->installed_version = JText :: _('Not installed');
			if (!isset($data[$key])) $data[$key] = array();
			$data[$key][] = $row;
		}

		return $data;
	}
	function install() {
		$success = $error = 0;
		if ($packages = (array)JRequest :: getVar('packages')) {
			jimport('joomla.filesystem.folder');
			jimport('joomla.filesystem.file');
			jimport('joomla.filesystem.archive');
			jimport('joomla.archive.archive');

			$info = file_get_contents(JPATH_SITE.'/administrator/components/com_batchinstaller/data/files.xml');
			$xmlcont = new SimpleXMLElement($info);

			$path = JPATH_ADMINISTRATOR.'/components/com_batchinstaller/data/';
			$app = JFactory :: getApplication();

			foreach ($packages as $package) if ($package and file_exists($path.$package)) {
				$installed = false;
				$title = '';
				if (!empty($xmlcont->file)) {
					foreach ($xmlcont->file as $info) {
						if (preg_match("#{$info->mask}_v#i", $package)) {
							$title = (string)$info->title;
							$xml_path = trim(str_replace('\\', '/', $info->xmlpath), '/');
							if ($xml_path and file_exists(JPATH_SITE.'/'.$xml_path)) $installed = true;
							break;
						}
					}
				}
				if (!$title) $title = basename($package);

				$extractdir = JPATH_SITE.'/tmp/'.rand();
				if (file_exists($extractdir)) JFolder :: delete($extractdir);
				JFolder :: create($extractdir);

				if (file_exists($extractdir) and JArchive::extract($path.$package, $extractdir)) {
					$installer = clone(JInstaller::getInstance());
					if ($installer->install($extractdir)) {
						if ($installed) {
							$app->enqueueMessage(JText :: sprintf('BI_SUCCESS_UPDATE', $title));
						} else {
							$app->enqueueMessage(JText :: sprintf('BI_SUCCESS_INSTALL', $title));
						}
					} else {
						if ($installed) {
							$app->enqueueMessage(JText :: sprintf('BI_ERROR_UPDATE', $title), 'error');
						} else {
							$app->enqueueMessage(JText :: sprintf('BI_ERROR_INSTALL', $title), 'error');
						}
					}
					JFolder :: delete($extractdir);
				} else $app->enqueueMessage(JText :: sprintf('BI_CANT_UNPACK', $title), 'error');
			}
		}
	}
}