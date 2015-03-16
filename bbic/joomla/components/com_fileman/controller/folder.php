<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanControllerFolder extends ComFilemanControllerAbstract
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'model' => 'com:files.model.folders'
        ));

        parent::_initialize($config);
    }

    public function getRequest()
    {
        $request = parent::getRequest();
        $query   = $request->query;

        $menu   = JFactory::getApplication()->getMenu()->getActive();
        $params = new ComKoowaDecoratorParameter(new KObjectConfig(array('delegate' => $menu->params)));

        if ($params->limit != -1) {
            $query->limit = (int) $params->limit;
        } elseif (!$query->limit) {
            $query->limit = (int) JFactory::getApplication()->getCfg('list_limit');
        }

        $query->sort      = $params->sort;
        $query->direction = $params->direction;

        return $request;
    }
}