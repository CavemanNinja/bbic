<?php
/**
 * @package     EXTman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComExtmanControllerToolbarExtensions extends ComDefaultControllerToolbarDefault
{
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'title'  => 'EXTman'
        ));

        parent::_initialize($config);
    }

	protected function _commandDelete(KControllerToolbarCommand $command)
    {
        $command->icon  = 'icon-32-delete';
        $command->label = 'Uninstall';

        $command->append(array(
            'attribs' => array(
                'data-action' => 'delete'
            )
        ));
    }
}