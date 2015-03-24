<?php
/**
 * FW Gallery 3.2
 * @copyright (C) 2014 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined('_JEXEC') or die('Restricted access');

class com_fwGalleryInstallerScript {
	function postflight($type, $adaptor) {
		$db = JFactory :: getDBO();

		$tables = $db->getTableList();
		if (!in_array($db->getPrefix().'fwg_files_thumbs_vote', $tables)) {
			$db->setQuery('
CREATE TABLE `#__fwg_files_thumbs_vote` (
	`user_id` INT(10) UNSIGNED NOT NULL,
	`file_id` INT(10) UNSIGNED NOT NULL,
	`value` TINYINT(4) NOT NULL DEFAULT 0,
	`ipaddr` BIGINT(20) NULL DEFAULT NULL,
	INDEX `user_id` (`user_id`),
	INDEX `file_id` (`file_id`),
	INDEX `ipaddr` (`ipaddr`)
)');
			if (!$db->query()) echo $db->getError();
		}
		if (!in_array($db->getPrefix().'fwg_file_prices', $tables)) {
			$db->setQuery('
CREATE TABLE `#__fwg_file_prices` (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	file_id INT UNSIGNED,
	width INT UNSIGNED,
	height INT UNSIGNED,
	price DECIMAL(10, 2),
	PRIMARY KEY(id),
	KEY(file_id),
	KEY(price)
)');
			if (!$db->query()) echo $db->getError();
		}
		if (!in_array($db->getPrefix().'fwg_files_tags', $tables)) {
			$db->setQuery('
CREATE TABLE `#__fwg_files_tags` (
	`tag_id` INT(10) UNSIGNED NOT NULL,
	`file_id` INT(10) UNSIGNED NOT NULL,
	INDEX `tag_id` (`tag_id`),
	INDEX `file_id` (`file_id`)
)');
			if (!$db->query()) echo $db->getError();
		}
		if (!in_array($db->getPrefix().'fwg_tags', $tables)) {
			$db->setQuery('
CREATE TABLE `#__fwg_tags` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(100) NULL DEFAULT NULL,
	PRIMARY KEY (`id`),
	INDEX `name` (`name`)
)');
			if (!$db->query()) echo $db->getError();
		}

		$db->setQuery('SHOW FIELDS FROM #__fwg_files');
		$fields = method_exists($db, 'loadColumn')?$db->loadColumn():$db->loadResultArray();
		$list = array(
			'media'=>"ALTER TABLE `#__fwg_files` ADD COLUMN `media` VARCHAR(20)",
			'media_code'=>"ALTER TABLE `#__fwg_files` ADD COLUMN `media_code` VARCHAR(200)",
			'media_link'=>"ALTER TABLE `#__fwg_files` ADD COLUMN `media_link` VARCHAR(250)",
			'original_filename'=>'ALTER TABLE `#__fwg_files` ADD COLUMN `original_filename` VARCHAR(40) AFTER `filename`;',
			'hits'=>'ALTER TABLE `#__fwg_files` ADD COLUMN `hits` INT(10) UNSIGNED NOT NULL DEFAULT \'0\' AFTER `ordering`;',
			'type_id'=>'ALTER TABLE `#__fwg_files` ADD COLUMN `type_id` INT(10) UNSIGNED NOT NULL DEFAULT \'0\' AFTER `project_id`,  ADD INDEX `type_id` (`type_id`);',
			'user_id'=>'ALTER TABLE `#__fwg_files` ADD COLUMN `user_id` INT(10) UNSIGNED NOT NULL DEFAULT \'0\' AFTER `project_id`,  ADD INDEX `user_id` (`user_id`);',
			'latitude'=>'ALTER TABLE `#__fwg_files` ADD COLUMN `latitude` FLOAT NOT NULL DEFAULT \'0\';',
			'longitude'=>'ALTER TABLE `#__fwg_files` ADD COLUMN `longitude` FLOAT NOT NULL DEFAULT \'0\';',
			'copyright'=>'ALTER TABLE `#__fwg_files` ADD COLUMN `copyright` VARCHAR(100);'
		);

		foreach ($list as $name=>$query) {
			if (!in_array($name, $fields)) {
				$db->setQuery($query);
				if (!$db->query()) echo 'Can\'t execute a query: '.$name.' - do it manually: '.$db->getQuery().'<br/>';
				else {
					if ($name == 'original_filename') {
						jimport('joomla.filesystem.file');
						$path = JPATH_SITE.'/images/com_fwgallery/files/';
						$db->setQuery('
SELECT
	id,
	(SELECT p.user_id FROM #__fwg_projects AS p WHERE p.id = f.project_id) AS user_id,
	filename
FROM
	#__fwg_files AS f');
						if ($list = $db->loadObjectList()) foreach ($list as $row) if ($row->filename and $row->user_id) {
							$files_path = $path.$row->user_id.'/';
							if (is_file($files_path.$row->filename)) {
								$original_filename = '';
								$ext = strtolower(JFile :: getExt($row->filename));
								do {
									$original_filename = md5($row->filename.rand().microtime()).'.'.$ext;
								} while (file_exists($files_path.$original_filename));
								if (JFile :: copy($files_path.$row->filename, $files_path.$original_filename)) {
									$db->setQuery('
UPDATE
	#__fwg_files
SET
	original_filename = '.$db->quote($original_filename).'
WHERE
	id = '.$row->id
									);
									$db->query();
								}
							}
						}
					} elseif ($name == 'media') {
						$db->setQuery('ALTER TABLE `#__fwg_files` CHANGE COLUMN `media` `media` VARCHAR(20)');
						if (!$db->query()) echo 'Can\'t execute a query: '.$name.' - do it manually: '.$db->getQuery().'<br/>';
					} elseif ($name == 'media_code') {
						$db->setQuery('ALTER TABLE `#__fwg_files` CHANGE COLUMN `media_code` `media_code` VARCHAR(200)');
						if (!$db->query()) echo 'Can\'t execute a query: '.$name.' - do it manually: '.$db->getQuery().'<br/>';
					}
				}
			}
		}

		$db->setQuery('SELECT * FROM #__fwg_types WHERE plugin = \'video\'', 0, 1);
		if ($obj = $db->loadObject()) {
			if (!$obj->published) {
				$db->setQuery('UPDATE #__fwg_types SET published = 1 WHERE id = '.$obj->id);
				$db->query();
			}
		} else {
			$db->setQuery('INSERT INTO #__fwg_types SET published = 1, name = \'Video\', plugin = \'video\'');
			$db->query();
		}

		$db->setQuery('SHOW FIELDS FROM #__fwg_files_vote');
		$fields = method_exists($db, 'loadColumn')?$db->loadColumn():$db->loadResultArray();
		$list = array(
			'ipaddr'=>'ALTER TABLE `#__fwg_files_vote` ADD COLUMN `ipaddr` BIGINT NULL, ADD INDEX `ipaddr` (`ipaddr`), DROP PRIMARY KEY, ADD INDEX `user_id` (`user_id`), ADD INDEX `file_id` (`file_id`)',
		);

		foreach ($list as $name=>$query) {
			if (!in_array($name, $fields)) {
				$db->setQuery($query);
				if (!$db->query()) echo 'Can\'t execute a query: '.$name.' - do it manually: '.$db->getQuery().'<br/>';
			}
		}
		$db->setQuery('ALTER TABLE `#__fwg_files_vote`  CHANGE COLUMN `ipaddr` `ipaddr` BIGINT NULL DEFAULT NULL');
		$db->query();

		$db->setQuery('SHOW FIELDS FROM #__fwg_projects');
		$fields = method_exists($db, 'loadColumn')?$db->loadColumn():$db->loadResultArray();
		$list = array(
			'is_public'=>'ALTER TABLE `#__fwg_projects` ADD COLUMN `is_public` TINYINT UNSIGNED NOT NULL DEFAULT 0',
			'parent'=>'ALTER TABLE `#__fwg_projects` ADD COLUMN `parent` INT(10) UNSIGNED NOT NULL DEFAULT 0, ADD INDEX `parent` (`parent`)',
			'gid'=>'ALTER TABLE `#__fwg_projects` ADD COLUMN `gid` TINYINT UNSIGNED NOT NULL DEFAULT 0, ADD INDEX `gid` (`gid`)',
			'color'=>'ALTER TABLE `#__fwg_projects` ADD COLUMN `color` CHAR(6)'
		);

		foreach ($list as $name=>$query) {
			if (!in_array($name, $fields)) {
				$db->setQuery($query);
				if (!$db->query()) echo 'Can\'t execute a query: '.$name.' - do it manually: '.$db->getQuery().'<br/>';
			}
		}
		/* install simple template */
        $installer = method_exists($adaptor, 'getParent') ? $adaptor->getParent() : $adaptor->parent;
		$source_path = $installer->getPath('source').'/';
		if (file_exists($source_path.'com_fwgallerytmplsimple.zip') and JArchive::extract($source_path.'com_fwgallerytmplsimple.zip', $source_path.'com_fwgallerytmplsimple')) {
			$installer->install($source_path.'com_fwgallerytmplsimple');
			JFolder :: delete($source_path.'com_fwgallerytmplsimple');
		}
	}
}
