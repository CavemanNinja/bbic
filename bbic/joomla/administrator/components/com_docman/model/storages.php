<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanModelStorages extends KModelAbstract
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()
            ->insert('container'   , 'identifier', '')
            ->insert('storage_type', 'identifier', 'file')
            ->insert('storage_path', 'raw', '');
    }

    protected function _actionFetch(KModelContext $context)
    {
        $state = $this->getState();

        if ($state->storage_type == 'file')
        {
            // Can't use basename as it gets rid of UTF characters at the beginning of the file name
            $name = ltrim(basename(' '.strtr($state->storage_path, array('/' => '/ '))));

            $entity = $this->getObject('com://admin/docman.model.entity.file', array(
                'data' => array(
                    'container' => $state->container,
                    'folder' 	=> dirname($state->storage_path),
                    'name' 		=> $name
                )
            ));
        }
        else
        {
            $entity = $this->getObject('com://admin/docman.model.entity.remote', array(
                'data' => array(
                    'path' => $state->storage_path
                )
            ));
        }

        return $entity;
    }
}
