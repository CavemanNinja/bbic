<?php
/**
 * @version		$Id: k2item.php 303 2010-01-07 02:56:33Z joomlaworks $
 * @package		K2
 * @author    JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2010 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class Tablefocontentuploader extends JTable {

	var $id = null;
	var $name = null;
	var $params = null;
	var $active = null;

	function __construct(&$db) {

		parent::__construct('#__focontentuploader', 'id', $db);
	}

	function check() {

		if (trim($this->name) == '') {
			$this->setError(JText::_('Item must have a name'));
			return false;
		}

        /*
		if(JPluginHelper::isEnabled('system', 'unicodeslug'))
			$this->alias = JFilterOutput::stringURLSafe($this->alias);
		else {
			mb_internal_encoding("UTF-8");
			mb_regex_encoding("UTF-8");
			$this->alias = trim(mb_strtolower($this->alias));
			$this->alias = str_replace('-', ' ', $this->alias);
			$this->alias = mb_ereg_replace('[[:space:]]+', ' ', $this->alias);
			$this->alias = trim(str_replace(' ', '-', $this->alias));
			$this->alias = str_replace('.', '', $this->alias);
			$this->alias = str_replace('"', '', $this->alias);
			$this->alias = str_replace("'", '', $this->alias);

			$stripthese = ',|~|!|@|%|^|(|)|<|>|:|;|{|}|[|]|&|`|„|‹|’|‘|“|�?|•|›|«|´|»|°|�|�|�';
			$strips = explode('|', $stripthese);
			foreach ($strips as $strip) {
				$this->alias = str_replace($strip, '', $this->alias);
			}


			$params = &JComponentHelper::getParams('com_k2');
			$SEFReplacements = array();
			$items = explode(',', $params->get('SEFReplacements'));
			foreach ($items as $item) {
				if (! empty($item)) {
					@list($src, $dst) = explode('|', trim($item));
					$SEFReplacements[trim($src)] = trim($dst);
				}
			}


			foreach ($SEFReplacements as $key=>$value) {
				$this->alias = str_replace($key, $value, $this->alias);
			}

			$this->alias = trim($this->alias, '-.');

			if (trim(str_replace('-', '', $this->alias)) == '') {
				$datenow = &JFactory::getDate();
				$this->alias = $datenow->toFormat("%Y-%m-%d-%H-%M-%S");
			}


		}
        */

		return true;

	}

	function bind($array, $ignore = '') {

		if (key_exists('params', $array) && is_array($array['params'])) {
			$registry = new JRegistry();
			$registry->loadArray($array['params']);
			$array['params'] = $registry->toString();
		}

		return parent::bind($array, $ignore);
	}

}
