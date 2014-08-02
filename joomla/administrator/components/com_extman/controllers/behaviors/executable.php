<?php
/**
 * @package     EXTman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComExtmanControllerBehaviorExecutable extends ComDefaultControllerBehaviorExecutable
{
	public function canGet()
	{
		$result = false;

		if(version_compare(JVERSION,'1.6.0','ge')) {
			$result = JFactory::getUser()->authorise('core.manage', 'com_extman') === true;
		} else {
			$result = JFactory::getUser()->get('gid') > 22;
		}
		return $result;
	}

	/**
	 * Generic authorize handler for controller delete actions
	 *
	 * @return  boolean     Can return both true or false.
	 */
	public function canDelete()
	{
		$result = $this->canGet();

		if ($result) {
			if(version_compare(JVERSION,'1.6.0','ge')) {
				$result = JFactory::getUser()->authorise('core.delete', 'com_extman') === true;
			} else {
				$result = JFactory::getUser()->get('gid') > 22;
			}
		}

		return $result;
	}
}
