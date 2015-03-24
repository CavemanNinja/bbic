<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

abstract class ComFilemanControllerPermissionAbstract extends ComKoowaControllerPermissionAbstract
{
    public function canMove()
    {
        return $this->canDelete() && $this->canAdd();
    }

    public function canCopy()
    {
        return $this->canAdd();
    }

    /**
     * {@inheritdoc}
     */
    public function canAdd()
    {
        $component = $this->getIdentifier()->package;

        return $this->getObject('user')->authorise('core.create', 'com_'.$component) === true;
    }

    /**
     * {@inheritdoc}
     */
    public function canEdit()
    {
        $component = $this->getIdentifier()->package;

        return $this->getObject('user')->authorise('core.edit', 'com_'.$component) === true;
    }

    /**
     * {@inheritdoc}
     */
    public function canDelete()
    {
        $component = $this->getIdentifier()->package;

        return $this->getObject('user')->authorise('core.delete', 'com_'.$component) === true;
    }
}