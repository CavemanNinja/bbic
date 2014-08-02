<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Application
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.helper');

abstract class TwojToolBoxModuleHelper{
	
	public static function &getModuleId($id){
		$result = null;
		$modules =& TwojToolBoxModuleHelper::_load();
		$total = count($modules);
		for ($i = 0; $i < $total; $i++)	{
			if ($modules[$i]->id == $id){
				$result = &$modules[$i];
				break; 
			}
		}
		if (is_null($result) && !$id ){
			$result            = new stdClass;
			$result->id        = 0;
			$result->title     = '';
			$result->module    = '';
			$result->position  = '';
			$result->content   = '';
			$result->showtitle = 0;
			$result->control   = '';
			$result->params    = '';
			$result->user      = 0;
		}
		return $result;
	}
	
	public static function &getModule($name, $title = null){
		$result = null;
		$modules =& TwojToolBoxModuleHelper::_load();
		$total = count($modules);
		for ($i = 0; $i < $total; $i++)	{
			if ($modules[$i]->name == $name || $modules[$i]->module == $name){
				if (!$title || $modules[$i]->title == $title){
					$result = &$modules[$i];
					break; 
				}
			}
		}
		if (is_null($result) && substr($name, 0, 4) == 'mod_'){
			$result            = new stdClass;
			$result->id        = 0;
			$result->title     = '';
			$result->module    = $name;
			$result->position  = '';
			$result->content   = '';
			$result->showtitle = 0;
			$result->control   = '';
			$result->params    = '';
			$result->user      = 0;
		}
		return $result;
	}

	public static function &getModules($position){
		$position = strtolower($position);
		$result = array();
		$modules =& TwojToolBoxModuleHelper::_load();
		$total = count($modules);
		for ($i = 0; $i < $total; $i++){
			if ($modules[$i]->position == $position){
				$result[] = &$modules[$i];
			}
		}
		if (count($result) == 0){
			if (JRequest::getBool('tp') && JComponentHelper::getParams('com_templates')->get('template_positions_display')){
				$result[0] = TwojToolBoxModuleHelper::getModule('mod_' . $position);
				$result[0]->title = $position;
				$result[0]->content = $position;
				$result[0]->position = $position;
			}
		}
		return $result;
	}

	

	/**
	 * Load published modules.
	 *
	 * @return  array
	 *
	 * @since   11.1
	 */
	protected static function &_load()
	{
		static $clean;

		if (isset($clean))
		{
			return $clean;
		}

		$Itemid = JRequest::getInt('Itemid');
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$groups = implode(',', $user->getAuthorisedViewLevels());
		$lang = JFactory::getLanguage()->getTag();
		$clientId = (int) $app->getClientId();

		$cache = JFactory::getCache('com_modules', '');
		$cacheid = md5(serialize(array($Itemid, $groups, $clientId, $lang)));

		if (!($clean = $cache->get($cacheid)))
		{
			$db = JFactory::getDbo();

			$query = $db->getQuery(true);
			$query->select('m.id, m.title, m.module, m.position, m.content, m.showtitle, m.params, mm.menuid');
			$query->from('#__modules AS m');
			$query->join('LEFT', '#__modules_menu AS mm ON mm.moduleid = m.id');
			$query->where('m.published = 1');

			$query->join('LEFT', '#__extensions AS e ON e.element = m.module AND e.client_id = m.client_id');
			$query->where('e.enabled = 1');

			$date = JFactory::getDate();
			$now = $date->toSql();
			$nullDate = $db->getNullDate();
			$query->where('(m.publish_up = ' . $db->Quote($nullDate) . ' OR m.publish_up <= ' . $db->Quote($now) . ')');
			$query->where('(m.publish_down = ' . $db->Quote($nullDate) . ' OR m.publish_down >= ' . $db->Quote($now) . ')');

			$query->where('m.access IN (' . $groups . ')');
			$query->where('m.client_id = ' . $clientId);
			$query->where('(mm.menuid = ' . (int) $Itemid . ' OR mm.menuid <= 0)');

			// Filter by language
			if ($app->isSite() && $app->getLanguageFilter())
			{
				$query->where('m.language IN (' . $db->Quote($lang) . ',' . $db->Quote('*') . ')');
			}

			$query->order('m.position, m.ordering');

			// Set the query
			$db->setQuery($query);
			$modules = $db->loadObjectList();
			$clean = array();

			if ($db->getErrorNum())
			{
				JError::raiseWarning(500, JText::sprintf('JLIB_APPLICATION_ERROR_MODULE_LOAD', $db->getErrorMsg()));
				return $clean;
			}

			// Apply negative selections and eliminate duplicates
			$negId = $Itemid ? -(int) $Itemid : false;
			$dupes = array();
			for ($i = 0, $n = count($modules); $i < $n; $i++)
			{
				$module = &$modules[$i];

				// The module is excluded if there is an explicit prohibition or if
				// the Itemid is missing or zero and the module is in exclude mode.
				$negHit = ($negId === (int) $module->menuid) || (!$negId && (int) $module->menuid < 0);

				if (isset($dupes[$module->id]))
				{
					// If this item has been excluded, keep the duplicate flag set,
					// but remove any item from the cleaned array.
					if ($negHit)
					{
						unset($clean[$module->id]);
					}
					continue;
				}

				$dupes[$module->id] = true;

				// Only accept modules without explicit exclusions.
				if (!$negHit)
				{
					// Determine if this is a 1.0 style custom module (no mod_ prefix)
					// This should be eliminated when the class is refactored.
					// $module->user is deprecated.
					$file = $module->module;
					$custom = substr($file, 0, 4) == 'mod_' ?  0 : 1;
					$module->user = $custom;
					// 1.0 style custom module name is given by the title field, otherwise strip off "mod_"
					$module->name = $custom ? $module->module : substr($file, 4);
					$module->style = null;
					$module->position = strtolower($module->position);
					$clean[$module->id] = $module;
				}
			}

			unset($dupes);

			// Return to simple indexing that matches the query order.
			$clean = array_values($clean);

			$cache->store($clean, $cacheid);
		}

		return $clean;
	}

}
