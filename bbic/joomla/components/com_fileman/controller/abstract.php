<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

abstract class ComFilemanControllerAbstract extends ComKoowaControllerModel
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'model' => 'com:files.model.files'
        ));

        parent::_initialize($config);
    }

	public function getRequest()
	{
		$request = parent::getRequest();
        $query   = $request->query;

		$query->container = 'fileman-files';
		$query->folder = trim($query->folder, '/');

		return $request;
	}
}
