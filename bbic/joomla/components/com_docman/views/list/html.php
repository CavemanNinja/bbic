<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanViewListHtml extends ComDocmanViewHtml
{
    /**
     * Passed to triggered events as context argument
     *
     * @var string
     */
    protected $_event_context = 'com_docman.list';

    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'model' => 'com://site/docman.model.categories'
        ));

        parent::_initialize($config);
    }

    public function display()
    {
        $category = $this->getModel()->getItem();
        $state    = $this->getModel()->getState();
        $params   = $this->getParameters();

        if ($state->isUnique() && $category->isNew()) {
            throw new KViewException($this->getService('translator')->translate('Category not found'), KHttpResponse::NOT_FOUND);
        }

        /*
         * Have to do this here because the default limit setting code is wrapped in _actionBrowse
         * and in a check for isDispatched and neither is true for this code
         */
        if (!$state->limit) {
            $state->limit = JFactory::getApplication()->getCfg('list_limit');
        }

        if ($params->show_subcategories)
        {
            $subcategories = $this->getService('com://site/docman.model.categories')
                ->level(1)
                ->parent_id($category->id)
                ->enabled($state->enabled)
                ->access($state->access)
                ->current_user(JFactory::getUser()->id)
                ->page($state->page)
                ->sort($this->getParameters()->sort_categories)
                ->limit(0)
                ->getList();

            $this->assign('subcategories', $subcategories);
        } else {
            $this->assign('subcategories', array());
        }

        if (substr($params->sort_documents, 0, 8) === 'reverse_') {
            $sort = substr($params->sort_documents, 8);
            $direction = 'desc';
        } else {
            $sort = $params->sort_documents;
            $direction = 'asc';
        }

        if ($params->show_document_sort_limit) {
            if ($state->document_sort) {
                $sort = $state->document_sort;
            }

            if ($state->document_direction) {
                $direction = $state->document_direction;
            }
        }

        if ($params->show_documents && $category->id)
        {
            $model = $this->getService('com://site/docman.controller.document')
                ->enabled($state->enabled)
                ->status($state->status)
                ->access($state->access)
                ->current_user(JFactory::getUser()->id)
                ->page($state->page)
                ->category($category->id)
                ->limit($state->limit)
                ->offset($state->offset)
                ->sort($sort)
                ->direction($direction)
                ->set($state->document_state)
                ->getModel();

            $total     = $model->getTotal();
            $documents = $model->getList();

            foreach ($documents as $document) {
                $this->_prepareDocument($document, $params);

                $this->callEvent('onDocmanContentPrepare', array($this->_event_context, &$document, &$params));
            }

            $this->assign('documents', $documents);
            $this->assign('total', $total);
        } else {
            $this->assign('documents', array());
            $this->assign('total', 0);
        }

        if (empty($category->id)) {
            $menu = $this->getActiveMenu();
            $category->title = $params->page_title ? $params->page_title : (isset($menu->name) ? $menu->name : $menu->title);
        }

        $this->assign('category', $category);
        $this->assign('event_context', $this->_event_context);
        $this->assign('login_to_download', $this->getService('com://admin/docman.model.configs')->getItem()->login_to_download);

        return parent::display();
    }

    protected function _generatePathway($category = null, $document = null)
    {
        $category = $this->getModel()->getItem();

        return parent::_generatePathway(($category->id ? $category : null));
    }

    /**
     * If the current page is not the menu category, use the current category title
     */
    protected function _setPageTitle()
    {
        if ($this->getName() === 'list' && $this->getName() === $this->getActiveMenu()->query['view'])
        {
            $category = $this->getModel()->getItem();

            if (isset($this->getActiveMenu()->query['slug']) && $category->slug !== $this->getActiveMenu()->query['slug']) {
                $this->getParameters()->set('page_title', $category->title);
            }
        }

        parent::_setPageTitle();
    }
}
