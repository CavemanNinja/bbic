<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanControllerPermissionSubmit extends ComFilemanControllerPermissionAbstract
{
    /**
     * Common method to run on both GET and POST
     *
     * @return boolean
     */
    public function canDoAnything()
    {
        $result = parent::canDoAnything();

        if ($result) {
            $menu = JFactory::getApplication()->getMenu()->getActive();
            $result = $menu->query['view'] === 'submit' || $menu->params->get('allow_uploads');
        }

        return $result;
    }

    public function canAdd()
    {
        return $this->canDoAnything();
    }

    public function canRender()
    {
        return $this->canDoAnything();
    }

}