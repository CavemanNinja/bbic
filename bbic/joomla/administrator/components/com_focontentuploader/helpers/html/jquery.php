<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Utility class for jQuery JavaScript behaviors
 *
 * @package     Joomla.Libraries
 * @subpackage  HTML
 * @since       3.0
 */
abstract class JHtmlJquery {
	/**
	 * @var    array  Array containing information for loaded files
	 * @since  3.0
	 */
	protected static $loaded = array();

	/**
	 * Method to load the jQuery JavaScript framework into the document head
	 *
	 * If debugging mode is on an uncompressed version of jQuery is included for easier debugging.
	 *
	 * @param   boolean  $noConflict  True to load jQuery in noConflict mode [optional]
	 * @param   mixed    $debug       Is debugging mode on? [optional]
	 * @param   boolean  $migrate     True to enable the jQuery Migrate plugin
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public static function framework($noConflict = true, $debug = null, $migrate = true) {
		return;
	}

	/**
	 * Method to load the jQuery UI JavaScript framework into the document head
	 *
	 * If debugging mode is on an uncompressed version of jQuery UI is included for easier debugging.
	 *
	 * @param   array  $components  The jQuery UI components to load [optional]
	 * @param   mixed  $debug       Is debugging mode on? [optional]
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public static function ui(array $components = array('core'), $debug = null)	{
		return;
	}
}
