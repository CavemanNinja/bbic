<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanViewSubmitHtml extends ComDocmanViewHtml
{
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'auto_assign' => false
        ));

        parent::_initialize($config);
    }

    public function display()
    {
        $this->assign('document', $this->getModel()->getItem());

        return parent::display();
    }
}
