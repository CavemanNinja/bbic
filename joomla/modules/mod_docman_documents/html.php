<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ModDocman_DocumentsHtml extends ModDefaultHtml
{
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        $this->getTemplate()->getFilter('alias')->append(array(
            'icon://' => $config->root_url.'/joomlatools-files/docman-icons/'
        ), KTemplateFilter::MODE_READ | KTemplateFilter::MODE_WRITE);
    }

    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'root_url' => KRequest::root()
        ));

        parent::_initialize($config);
    }

    public function display()
    {
        $model = $this->getService('com://site/docman.model.documents');
        $params = $this->module->params;

        $model
            ->access(JFactory::getUser()->getAuthorisedViewLevels())
            ->enabled(1)
            ->status('published')
            ->limit($params->limit)
            ->sort($params->sort)
            ->direction($params->direction)
            ->created_by(KConfig::unbox($params->created_by))
            ->category(KConfig::unbox($params->category))
            ->category_children($params->include_child_categories)
            ->page($params->page ? $params->page->toArray() : 'all')
            ->current_user(JFactory::getUser()->id)
            ;

        $documents = $model->getList();

        foreach ($documents as $document) {
            $document->link = JRoute::_(sprintf('index.php?option=com_docman&view=document&alias=%s&category_slug=%s&Itemid=%d', $document->alias, $document->category_slug, $document->itemid));
        }

        $this->assign('documents', $documents);
        $this->assign('total', $model->getTotal());
        $this->assign('params', $params);

        if (strpos($params->layout, ':')) {
            $layout = explode(':', $params->layout);
            $params->layout = array_pop($layout);
        }

        if ($params->layout) {
            $this->setLayout($params->layout);
        }

        return parent::display();
    }
}
