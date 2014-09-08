<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanViewDoclinkHtml extends ComDocmanViewHtml
{
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'layout' => 'default',
            'auto_assign' => false
        ));

        parent::_initialize($config);
    }

    public function display()
    {
        $categories = $this->getService('com://admin/docman.controller.category')->limit(0)->browse();
        $this->assign('categories', $categories);

        $pages = $this->getService('com://site/docman.model.pages')
            ->view(array('list', 'document', 'filteredlist', 'submit'))->getList();

        foreach ($pages as $page) {
            if ($page->query['view'] === 'list') {
                $page->categories = $this->getService('com://admin/docman.model.categories')->page($page->id)->getList();
            } else {
                $page->categories = array();
            }
        }

        $this->assign('pages', $pages);

        return parent::display();
    }
}
