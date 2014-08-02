<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanViewDownloadHtml extends KViewFile
{
    protected static $_gdocs_extensions = array(
        'webm', 'mpeg4', '3gpp', 'mov', 'avi', 'mpegps', 'wmv', 'flv', 'ogg',
        'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'pages', 'ai',
        'psd', 'tiff', 'dxf', 'svg', 'eps', 'ps', 'ttf', 'xps'
    );

    public function display()
    {
        $document = $this->getModel()->getItem();

        if (!$document->id) {
            throw new KViewException('Document not found');
        }

        if (!KRequest::get('get.force_download', 'int')
            && $document->storage_type === 'file'
            && $this->canPreview($document) && $this->canUseGoogleViewer($document)) {
            $document_url = clone KRequest::url();
            $document_url->setQuery(array_merge(array('force_download' => '1'), $document_url->getQuery(true)));

            $url = sprintf('http://docs.google.com/viewer?embedded=true&url=%s', urlencode($document_url));

            return JFactory::getApplication()->redirect($url);
        }

        if (!$document->hasAvailableStreamWrapper()) {
            throw new KViewException('Stream wrapper is missing');
        }

        $params = JFactory::getApplication()->getMenu()->getActive()->params;
        if (!$params->get('force_download') && !KRequest::get('get.force_download', 'int')) {
            $this->disposition = 'inline';
        }

        $file     = $document->storage;
        $mimetype = $file->mimetype;

        if (empty($mimetype))
        {
            $row = $this->getService('com://admin/docman.model.mimetypes')
                ->extension($file->extension)
                ->getList()->top();
            $mimetype = $row ? $row->mimetype : 'application/octet-stream';
        }

        $this->filename = $file->name;
        $this->path = $file->fullpath;

        $this->mimetype = $mimetype;

        if (!file_exists($this->path)) {
            throw new KViewException('File not found');
        }

        return parent::display();
    }

    /**
     * Returns true if the browser can preview the document
     *
     * @param KDatabaseRowInterface $document
     *
     * @return bool
     */
    public function canPreview(KDatabaseRowInterface $document)
    {
        return ($this->canUseGoogleViewer($document)
            || in_array($document->storage->extension, array('txt', 'pdf'))
            || $document->storage->isImage());
    }

    /**
     * Returns true if Google viewer is enabled and works for the current document type
     *
     * @param KDatabaseRowInterface $document
     *
     * @return bool
     */
    public function canUseGoogleViewer(KDatabaseRowInterface $document)
    {
        $params = JFactory::getApplication()->getMenu()->getActive()->params;

        return ($params->get('preview_with_gdocs')
            && in_array($document->storage->extension, self::$_gdocs_extensions));
    }

}
