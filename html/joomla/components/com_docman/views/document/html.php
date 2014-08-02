<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanViewDocumentHtml extends ComDocmanViewHtml
{
    public function display()
    {
        $document = $this->getModel()->getItem();

        if ($this->getLayout() !== 'form')
        {
            $params        = $this->getParameters();
            $event_context = 'com_docman.document';

            $this->_prepareDocument($document, $params);

            $this->callEvent('onDocmanContentPrepare', array($event_context, &$document, &$params));

            $this->assign('event_context', $event_context);
            $this->assign('login_to_download', $this->getService('com://admin/docman.model.configs')->getItem()->login_to_download);

            $query = $this->getActiveMenu()->query;
            if ($query['view'] === 'document' && $query['slug'] === $document->slug) {
                $this->assign('show_delete', false);
            }
        }
        else
        {
            $this->assign('user', JFactory::getUser());
        }

        return parent::display();
    }

    protected function _generatePathway($category = null, $document = null)
    {
        $document = $this->getModel()->getItem();

        if ($this->getLayout() === 'form') {
            $text = $document->isNew()
                ? $this->getTemplate()->getHelper('translator')->translate('Add new document')
                : $this->getTemplate()->getHelper('translator')->translate('Edit document %title%', array('%title%' => $document->title));

            $this->getPathway()->addItem($text, '');
        } else {
            $category = $this->getService('com://site/docman.model.categories')
                ->id($document->docman_category_id)->getItem();

            return parent::_generatePathway($category, $document);
        }
    }

    /**
     * If this is not a single document menu item, set the page title to the document title
     */
    protected function _setPageTitle()
    {
        if ($this->getName() === 'document' && $this->getActiveMenu()->query['view'] !== 'document') {
            $this->getParameters()->set('page_title', $this->getModel()->getItem()->title);
        }

        parent::_setPageTitle();
    }
}
