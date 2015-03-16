<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanDispatcherHttp extends ComKoowaDispatcherHttp
{
	protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
        	'controller' => 'file',
            'behaviors'  => array(
                'com://admin/fileman.dispatcher.behavior.routable'
            )
        ));
        
        parent::_initialize($config);
    }
}