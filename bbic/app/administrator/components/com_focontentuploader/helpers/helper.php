<?php
/**
 * Copyright (C) 2014 freakedout (www.freakedout.de)
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
**/

// no direct access
defined('_JEXEC') or die( 'Restricted access' );

class foContentUploaderHelper {

    private $removeTags = false;

	public function saveContentPrep (&$row) {

		// Filter settings
		jimport('joomla.application.component.helper');
		$config	= JComponentHelper::getParams('com_content');
		$user	= &JFactory::getUser();
		$gid	= $user->get('gid');

		$filterGroups	= (array)$config->get('filter_groups');
		if (in_array($gid, $filterGroups)) {
			$filterType	 = $config->get('filter_type');
			$filterTags	 = preg_split('#[,\s]+#', trim($config->get('filter_tags')));
			$filterAttrs = preg_split('#[,\s]+#', trim($config->get('filter_attributes')));
			switch ($filterType) {
				case 'NH':
					$filter	= new JFilterInput();
					break;
				case 'WL':
					$filter	= new JFilterInput($filterTags, $filterAttrs, 0, 0);
					break;
				case 'BL':
				default:
					$filter	= new JFilterInput($filterTags, $filterAttrs, 1, 1);
					break;
			}
			$row->introtext	= $filter->clean( $row->introtext );
			$row->fulltext	= $filter->clean( $row->fulltext );
		}

		return true;
	}

	public function getCategories ($active = NULL) {
		$db	= JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('id as value, title as text');
        $query->from('#__categories');
        $query->where('extension = "com_content"');
        $db->setQuery($query);

		$categories[] = JHTML::_('select.option', '0', '- '.JText::_('Select Category').' -');
		$categories = array_merge($categories, $db->loadObjectList());

		$category = JHTML::_('select.genericlist',  $categories, 'catid', 'class="inputbox" size="1" onchange="document.settingsForm.submit();"', 'value', 'text', (int)$active);

		return $category;
	}

	public function clean_data ($string) {
		if ($this->removeTags){
			return strip_tags($string);
		}
		return $string;
	}

	public function slugify ($string) {
        return JApplication::stringURLSafe($string);
	}

	public function check_iso_date($datetime){
		if (!preg_match('/^[0-9]{4}-(((0[13578]|(10|12))-(0[1-9]|[1-2][0-9]|3[0-1]))|(02-(0[1-9]|[1-2][0-9]))|((0[469]|11)-(0[1-9]|[1-2][0-9]|30)))\s(([0-1]?[0-9])|([2][0-3])):([0-5]?[0-9])(:([0-5]?[0-9]))?$/', $datetime)) {
			if (!preg_match('/^[0-9]{4}-(((0[13578]|(10|12))-(0[1-9]|[1-2][0-9]|3[0-1]))|(02-(0[1-9]|[1-2][0-9]))|((0[469]|11)-(0[1-9]|[1-2][0-9]|30)))$/', $datetime)) {
				return false;
			}
		}
		if(strlen($datetime) == 10) {
			$datetime .= ' 00:00:00';
		}

		return $datetime;
	}

    function dateTimeToGMT($datetime) {
		$Jconfig = JFactory::getConfig();
		$tzOffset = $Jconfig->get('config.offset');

		$dt = JFactory::getDate($datetime);
		$offset = $dt->getOffsetFromGMT();
		$datetime = date('Y-m-d H:i:s', strtotime($datetime) - $offset + (date('I') * 60 * 60));

		return $datetime;
	}

	public function check_col ($col){
		$col = strtoupper($col);
		if (!preg_match('/^([A-Z]|[A-H][A-Z]|[I][A-V])$/', $col)) {
			return false;
		}

		return $col;
	}

	public function placeholders($col){
		if (!preg_match('/(\{([A-Z]|[A-H][A-Z]*|[I][A-V]*)\}.*?)*/', $col, $match)) {
			return false;
		}

		return $col;
	}

	public function time_format($datetime, $tzoffset, $timezone, $calendar, $filetype){
		date_default_timezone_set('Europe/London');
		if (!$filetype) {
			$yrs = $calendar - 1900;
			$nineteenseventy = 25569 - ($yrs * 365 + $yrs * 0.5);
			if (is_numeric($datetime)) {
				$datetime = (-$nineteenseventy - ($tzoffset + date('I')) / 24 + $datetime) * 24 * 60 * 60;
			} else {
				$datetime = strtotime($datetime);
			}
		} else {
			$csvtime = strtotime($datetime);
			$datetime = $csvtime - ($tzoffset) * 60 * 60;
		}

		$datetime = date('Y-m-d H:i:s', $datetime);
		$datetime = JFactory::getDate($datetime);
		$datetime = $datetime->toSql();
		date_default_timezone_set($timezone);

		return $datetime;
	}

	public function replaceData($col, $data, $param) {
		return str_replace("{" . $col . "}", $data, $param);
	}

	public function col2chr ($a){
		$b = 0;
		if ($a < 27) {
			return strtoupper(chr($a+96));
		} else {
			while($a > 26){
                $a = $a - 26;
				$b++;
			}
            $a = strtoupper(chr($a+96));
			$b = strtoupper(chr($b+96));

			return $b . $a;
		}
	}

	public function chr2col ($c) {
		if (strlen($c) > 1){
			$c = strtolower($c);
			$a = (ord($c[0]) - 96) * 26 + ord($c[1]) - 96;
		} else {
			$a = ord(strtolower($c)) - 96;
		}
		return $a-1;
	}

	public function getConfigs ($active = NULL) {
		$db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('id as value, name as text');
        $query->from('#__focontentuploader');
        $query->where('component = "content"');
        $query->order('name ASC');
		$db->setQuery($query);
        $options = array(
            JHTML::_('select.option', '-1', '- '.JText::_('CU_NEW_CONFIG').' -')
        );
        $options = array_merge($options, $db->loadObjectList());
		$configs = JHTML::_('select.genericlist', $options, 'configid', 'class="inputbox" size="1" onclick="configValue=this.value" onchange="this.value=configValue;alert(\'' . JText::_('CU_PRO_FEATURE') . '\')";', 'value', 'text', $active);

		return $configs;
	}

	public function getConfig ($id = false) {
		if ($id) {
			$db = JFactory::getDBO();
            $query = $db->getQuery(true);
            $query->select('params');
            $query->from('#__focontentuploader');
            $query->where('id = ' . $id);
            $query->where('component = "content"');
			$db->setQuery($query);
			$params = $db->loadResult();
		}

		if (isset($params) && $params) {
			$params = explode("\n", $params);
			foreach ($params as $param) {
				$param = explode('=', $param);
				if (isset($param[1])) {
					$config[$param[0]] = trim($param[1]);
				} else {
					$config[$param[0]] = '';
				}
			}
			$config['introtext'] = stripslashes($config['introtext']);
            if(isset($config['fulltext'])) {
                $config['fulltext']  = stripslashes($config['fulltext']);
                $config['fulltext']  = str_replace('&#61;', '=', $config['fulltext']);
                $config['introtext'] = str_replace('&#61;', '=', $config['introtext']);
                $config['introtext'] = str_replace('%7B', '{', $config['introtext']);
                $config['fulltext']  = str_replace('%7B', '{', $config['fulltext']);
                $config['introtext'] = str_replace('%7D', '}', $config['introtext']);
                $config['fulltext']  = str_replace('%7D', '}', $config['fulltext']);
            }

            $this->removeTags = $config['removetags'];
		} else {
            $config = 0;
        }

		return $config;
	}

	public function getCurrentConfigID () {
		$db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('id');
        $query->from('#__focontentuploader');
        $query->where('component = "content"');
        $query->where('active = 1');
		$db->setQuery($query);

		return $db->loadResult();
	}

	public function params_to_save($params) {
		if (is_array($params)) {
			$txt = array();
			foreach ($params as $k => $v) {
				if(is_array($v)) {
					$v=implode("|",$v);
				}
				$txt[] = "$k=$v";
			}
			$params_to_save = implode("\n", $txt);

			return $params_to_save;
		}
	}

    public function zipfile ($file) {
		$zip = zip_open($file);
		if ($zip) {
			while ($zip_entry = zip_read($zip))	{
				$filename = zip_entry_name($zip_entry);
			}
			zip_close($zip);
		}

		$zip = new ZipArchive;
		if ($zip->open($file) === TRUE) {
			$zip->extractTo(JPATH_ROOT . '/media/com_focontentuploader/');
			$zip->close();
			$uploadfile = JPATH_ROOT . '/media/com_focontentuploader/' . $filename;
		}

		return $uploadfile;
	}

    public function getLevelByName($name) {
	    $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('id');
        $query->from('#__viewlevels');
        $query->where('title = "' . trim($name) . '"');
	    $db->setQuery($query);
	    return $db->loadResult();
	}
}
