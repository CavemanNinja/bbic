<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

abstract class ComFilemanControllerPermissionAbstract extends ComKoowaControllerPermissionAbstract
{
    /**
     * Common method to run on both GET and POST
     *
     * @return boolean
     */
    public function canDoAnything()
    {
        return JFactory::getApplication()->getMenu()->getActive();
    }

    public function canAdd()
    {
        return $this->canDoAnything();
    }

    public function canRender()
    {
        if ($this->canDoAnything() === false) {
            return false;
        }

        $menu   = JFactory::getApplication()->getMenu()->getActive();
        $folder = isset($menu->query['folder']) ? $menu->query['folder'] : null;

        if (!empty($folder) && strpos($this->getRequest()->query->folder, $folder) !== 0) {
            return false;
        }

        return true;
    }

}