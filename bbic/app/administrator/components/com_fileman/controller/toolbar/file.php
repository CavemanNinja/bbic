<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanControllerToolbarFile extends ComKoowaControllerToolbarActionbar
{
    public function getCommands()
    {
        $controller = $this->getController();

        if ($controller->canAdd())
        {
            $this->addUpload(array(
                'label' => 'Upload',
                'attribs' => array(
                    'class' => array('btn-success')
                )
            ));

            $this->addNewfolder(array(
                'label' => 'New Folder',
                'icon' => 'icon-32-new'
            ));

            /*$this->addSeparator();

            if ($controller->canCopy()) {
                $this->addCopy();
            }

            if ($controller->canMove()) {
                $this->addMove();
            }*/
        }

        if ($controller->canDelete()) {
            $this->addDelete();
        }

        $this->addSeparator();

        $this->addRefresh();

        if ($controller->canAdmin()) {
            $this->addSeparator()->addOptions();
        }

        return parent::getCommands();
    }
}