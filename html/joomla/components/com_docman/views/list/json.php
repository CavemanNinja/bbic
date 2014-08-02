<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanViewListJson extends ComDocmanViewJson
{
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'model' => 'com://site/docman.model.categories'
        ));

        parent::_initialize($config);
    }

    /**
     * Returns the JSON output
     *
     * @return array
     */
    protected function _getData()
    {
        $category = $this->getModel()->getItem();
        $state    = $this->getModel()->getState();

        if ($category->isNew())
        {
            $data  = array(
                'links' => array(
                    'self' => array('href' => JRoute::_('index.php?format=json&Itemid='.$this->getActiveMenu()->id))
                )
            );
        }
        else {
            $data = $this->_getCategory($category);
        }

        $categories = $this->getService('com://site/docman.model.categories')
            ->level(1)
            ->parent_id($category->id)
            ->enabled($state->enabled)
            ->access($state->access)
            ->page($state->page)
            ->sort($this->getParameters()->sort_categories)
            ->getList();

        $data['categories'] = array(
            'offset'   => 0,
            'limit'    => 0,
            'total'	   => count($categories),
            'items'    => $this->_filterRowset($categories, array($this, '_getCategory'))
        );

        /*
         * Have to do this here because the default limit setting code is wrapped in _actionBrowse
         * and in a check for isDispatched and neither is true for this code
         */
        if (!$state->limit) {
            $state->limit = JFactory::getApplication()->getCfg('list_limit');
        }

        $model = $this->getService('com://site/docman.controller.document')
            ->enabled($state->enabled)
            ->status($state->status)
            ->access($state->access)
            ->page($state->page)
            ->category($category->id)
            ->limit($state->limit)
            ->offset($state->offset)
            ->sort($state->sort)
            ->direction($state->direction)
            ->getModel();

        $data['documents'] = array(
            'offset'   => (int) $model->offset,
            'limit'    => (int) $model->limit,
            'total'	   => $model->getTotal(),
            'items'    => $this->_filterRowset($model->getList(), array($this, '_getDocument'))
        );

        return $data;
    }
}
