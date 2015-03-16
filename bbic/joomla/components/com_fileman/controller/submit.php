<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanControllerSubmit extends ComKoowaControllerModel
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'behaviors' => array('notifiable'),
            'model' => 'com:files.model.files'
        ));

        parent::_initialize($config);
    }

    /**
     * Add the toolbar for non-authentic users too
     *
     * @param KControllerContextInterface $context
     */
    protected function _addToolbars(KControllerContextInterface $context)
    {
        if($this->getView() instanceof KViewHtml)
        {
            if($this->isDispatched())
            {
                foreach($context->toolbars as $toolbar) {
                    $this->addToolbar($toolbar);
                }

                if($toolbars = $this->getToolbars())
                {
                    $this->getView()
                        ->getTemplate()
                        ->addFilter('toolbar', array('toolbars' => $toolbars));
                };
            }
        }
    }

    public function getRequest()
    {
        $request = parent::getRequest();
    
        $request->query->container = 'fileman-files';
        $request->query->folder = trim($request->query->folder, '/');
    
        return $request;
    }

    protected function _actionAdd(KControllerContextInterface $context)
    {
        $translator = $this->getObject('translator');
        $data       = $context->request->data;

        if (empty($data->file) && $context->request->files->has('file'))
        {
            $data->file = $context->request->files->file['tmp_name'];
            if (empty($data->name)) {
                $data->name = $context->request->files->file['name'];
            }
        }
        
        try
        {
            $menu = JFactory::getApplication()->getMenu()->getActive();

            $data->folder = $menu->params->get('folder');

            if (empty($data->file)) {
                throw new KControllerExceptionRequestInvalid($translator->translate('Please select a file to upload'));
            }
            
            if ($data->folder) {
                $this->getRequest()->query->folder = $data->folder;
            }

            $config =  array(
                'request' => $this->getRequest(),
                'behaviors' => array(
                    'permissible' => array(
                        'permission' => 'com://site/fileman.controller.permission.submit'
                    )
                )
            );

            $controller = $this->getObject('com:files.controller.file', $config);

            $container  = $controller->getModel()->getContainer();
            $data->name = $this->_getUniqueName($container, $data->folder, $data->name);

            $result = $controller->add($context);

            return $result;

        }
        catch (Exception $e)
        {
            $context->response->setRedirect($this->getObject('request')->getReferrer(), $e->getMessage(), 'error');
            return $this->getModel()->create();
        }
    }

    /**
     * Find a unique name for the given container and folder by adding (1) (2) etc to the end of file name
     *
     * @param $container
     * @param $folder
     * @param $file
     * @return string
     */
    protected function _getUniqueName($container, $folder, $file)
    {
        $adapter  = $container->getAdapter('file');
        $folder   = $container->fullpath.(!empty($folder) ? '/'.$folder : '');
        $fileinfo  = pathinfo(' '.strtr($file, array('/' => '/ ')));
        $filename  = ltrim($fileinfo['filename']);
        $extension = $fileinfo['extension'];

        $adapter->setPath($folder.'/'.$file);

        $i = 1;
        while ($adapter->exists())
        {
            $file = sprintf('%s (%d).%s', $filename, $i, $extension);

            $adapter->setPath($folder.'/'.$file);
            $i++;
        }

        return $file;
    }

    protected function _actionSave(KControllerContextInterface $context)
    {
        $result = $this->execute('add', $context);

        if ($context->getResponse()->getStatusCode() === KHttpResponse::CREATED)
        {
            $url = 'index.php?Itemid='.$this->getRequest()->query->Itemid;

            $route   = JRoute::_($url, false);
            $message = $this->getObject('translator')->translate('File is uploaded successfully');

            $context->response->setRedirect($route, $message);
        }

        return $result;
    }
}
