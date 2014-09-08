<?php
/**
 * @package     EXTman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('_JEXEC') or die;

jimport('joomla.installer.installer');

class ComExtmanInstaller extends JInstaller
{
	protected $_error = '';

	public function getError()
	{
		return $this->_error;
	}

	public function abort($msg=null, $type=null)
	{
		$this->_error = $msg;

		return parent::abort(null, $type);
	}

	public function getType()
	{
		$manifest = self::getVersion() === '1.5' ? $this->_manifest->document : $this->manifest;
		return self::getElementAttribute($manifest, 'type');
	}

	public static function getComponentDetails($manifest)
	{
		if (self::getVersion() === '1.5') {
			$name = $manifest->document->getElementByPath('name')->data();
		} else {
			$name = $manifest->name;
		}

		$name    = strtolower(JFilterInput::getInstance()->clean((string)$name, 'cmd'));
		$element = substr($name, 0, 4) == 'com_' ? $name : 'com_'.$name;

		return array(
			'type' => 'component',
			'element' => $element,
			'folder' => ''
		);
	}

	public static function getPluginDetails($manifest)
	{
		if (self::getVersion() === '1.5') {
			$manifest = $manifest->document;
		}

		$group = self::getElementAttribute($manifest, 'group');
		$files = self::getElementChildren($manifest, 'files');

		$element = null;
		if ($files)
		{
			foreach ($files as $file)
			{
				$plugin = self::getElementAttribute($file, 'plugin');
				if ($plugin) {
					$element = $plugin;
					break;
				}
			}
		}

		return array(
			'type' 		=> 'plugin',
			'folder' 	=> $group,
			'element' 	=> $element,
			'client_id' => 0
		);
	}

	public static function getModuleDetails($manifest)
	{
		if (self::getVersion() === '1.5') {
			$manifest = $manifest->document;
		}

		$group = self::getElementAttribute($manifest, 'group');
		$files = self::getElementChildren($manifest, 'files');

		$element = null;
		if ($files)
		{
			foreach ($files as $file)
			{
				$module = self::getElementAttribute($file, 'module');
				if ($module) {
					$element = $module;
					break;
				}
			}
		}

		$client_id = self::getElementAttribute($file, 'client') === 'site' ? 0 : 1;

		return array(
			'type' 		=> 'module',
			'folder' 	=> '',
			'element' 	=> $element,
			'client_id' => $client_id
		);
	}

	public static function getDetails($type, $manifest)
	{
		$method = 'get'.ucfirst($type).'Details';
		return call_user_func(array(__CLASS__, $method), $manifest);
	}

	public static function getElementAttribute($el, $attr)
	{
		if (self::getVersion() === '1.5') {
			$value = (string)$el->attributes($attr);
		} else {
			$value = (string)$el->attributes()->$attr;
		}
		return $value;
	}

	public static function getElementChildren($el, $path)
	{
		if (self::getVersion() === '1.5') {
			$value = $el->getElementByPath($path)->children();
		} else {
			$value = $el->$path->children();
		}
		return $value;
	}

	public static function getVersion()
	{
		return version_compare(JVERSION, '1.6', '<') ? '1.5' : '2.5';
	}


	public static function getExtensionId(array $extension)
	{
		$type = (string)$extension['type'];
		$element = (string)$extension['element'];
		$folder = isset($extension['folder']) ? (string)$extension['folder'] : '';
		$cid = isset($extension['client_id']) ? (int) $extension['client_id'] : 0;

		if ($type == 'component') {
			$cid = 1;
		}

		if ($type == 'component' && substr($element, 0, 4) !== 'com_') {
			$element = 'com_'.$element;
		} elseif ($type == 'module' && substr($element, 0, 4) !== 'mod_') {
			$element = 'mod_'.$element;
		}

		if (self::getVersion() === '2.5') {
			$query = "SELECT extension_id FROM #__extensions
				WHERE type = '$type' AND element = '$element' AND folder = '$folder' AND client_id = '$cid'
				LIMIT 1
			";
		}
		else {
			$query = "SELECT id FROM #__{$type}s";
			if ($type == 'component') {
				$query .= " WHERE `option` = '{$element}'";
			}
			else if ($type == 'module') {
				$query .= " WHERE module = '{$element}' AND client_id = '{$cid}'";
			}
			else if ($type == 'plugin') {
				$query .= " WHERE element = '{$element}' AND folder = '{$folder}'";
			}
			$query .= "LIMIT 1";
		}

		$db = JFactory::getDBO();
		$db->setQuery($query);

		return $db->loadResult();
	}
}