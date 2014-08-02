<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanViewCategoryHtml extends ComDocmanViewHtml
{
    public function display()
    {
        $this->assign('parent', $this->getModel()->getItem()->getParent());

        $default_access = (int) (JFactory::getConfig()->get('access') || 1);
        $default_access = $this->getService('com://admin/docman.model.viewlevels')
            ->id($default_access)->getItem();

        $this->assign('default_access', $default_access);

        return parent::display();
    }
}
