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

class foContentUploaderController extends cuController {

    public static $cuVersion;

    public function __construct() {
        parent::__construct();

        if (!self::$cuVersion) {
            $xml = file_get_contents(JPATH_COMPONENT_ADMINISTRATOR . '/focontentuploader.xml');
            preg_match('#<version>(\d+(\.\d+(\.\d+)?)?)</version>#', $xml, $cuVersion);
            self::$cuVersion = $cuVersion[1];
        }
    }
}