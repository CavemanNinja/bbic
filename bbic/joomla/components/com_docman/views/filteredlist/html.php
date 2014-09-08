<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanViewFilteredlistHtml extends ComDocmanViewHtml
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
        $states     = array('category', 'created_by', 'limit');
        $parameters = $this->getParameters();
        $model 	    = $this->getModel();

        foreach ($states as $state) {
            if ($parameters->$state) {
                $model->set($state, $parameters->$state);
            }
        }

        if (substr($parameters->sort, 0, 8) === 'reverse_') {
            $sort = substr($parameters->sort, 8);
            $direction = 'desc';
        } else {
            $sort = $parameters->sort;
            $direction = 'asc';
        }

        $model->sort($sort)->direction($direction)
            ->page($this->getActiveMenu()->id);

        $documents = $model->getList();

        $params        = $this->getParameters();
        $event_context = 'com_docman.documents';

        foreach ($documents as $document) {
            $this->_prepareDocument($document, $params);

            $this->callEvent('onDocmanContentPrepare', array($event_context, &$document, &$params));
        }

        $this->assign('category', $this->getModel()->getState()->category);
        $this->assign('documents', $documents);
        $this->assign('total', $this->getModel()->getTotal());
        $this->assign('event_context', $event_context);
        $this->assign('login_to_download', $this->getService('com://admin/docman.model.configs')->getItem()->login_to_download);

        return parent::display();
    }
}
