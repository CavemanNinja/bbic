<?php
/**
 * @package     EXTman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComExtmanFilterManifest extends KFilterAbstract
{
	protected $_walk = false;

    protected function _validate($value)
    {
        return true;
    }

	protected function _sanitize($value)
	{
		return $value instanceof SimpleXMLElement ? $value->asXML() : (string)$value;
	}
}