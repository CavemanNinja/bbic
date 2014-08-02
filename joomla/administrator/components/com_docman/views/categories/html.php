<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanViewCategoriesHtml extends ComDocmanViewHtml
{
    public function display()
    {
        $categories = $this->getModel()->getList();

        $document_map = $categories->getDocumentMap();
        foreach ($categories as $category) {
            $category->document_count = isset($document_map[$category->id]) ? count($document_map[$category->id]) : 0;
        }

        $breadcrumbs = array();
        if ($category = $categories->top()) {
            $breadcrumbs = $category->getAncestors()->getColumn('title');
        }

        $this->assign('breadcrumbs', $breadcrumbs);

        return parent::display();
    }
}
