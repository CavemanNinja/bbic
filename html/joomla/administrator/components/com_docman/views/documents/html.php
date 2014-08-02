<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanViewDocumentsHtml extends ComDocmanViewHtml
{
    public function display()
    {
        $categories = $this->getService('com://admin/docman.controller.category')->limit(0)->sort('title')->browse();

        $this->assign('categories', $categories);
        $this->assign('user', JFactory::getUser());

        return parent::display();
    }
}
