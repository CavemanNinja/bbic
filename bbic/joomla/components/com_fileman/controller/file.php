<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanControllerFile extends KControllerAbstract
{
    public function getRequest()
    {
        $request = parent::getRequest();
        $query   = $request->query;

        $query->container = 'fileman-files';
        $query->folder = trim($query->folder, '/');

        return $request;
    }

    protected function _actionRender(KControllerContextInterface $context)
    {
        $file = $this->getObject('com:files.controller.file')->setRequest($this->getRequest())->read();

        try
        {
            $this->getResponse()
                ->attachTransport('stream')
                ->setContent($file->fullpath, $file->mimetype);
        }
        catch (InvalidArgumentException $e) {
            throw new KControllerExceptionResourceNotFound('File not found');
        }
    }
}