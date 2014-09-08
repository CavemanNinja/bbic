<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanViewFilesHtml extends ComDocmanViewHtml
{
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'auto_assign' => false
        ));

        return parent::_initialize($config);
    }

    public function display()
    {
        if (in_array($this->getLayout(), array('default', 'select', 'select_icon')))
        {
            $state = $this->getModel()->getState();
            $container = is_object($state->container) ? $state->container->slug : $state->container;

            $controller = $this->getService('com://admin/files.controller.file');

            $view = $controller->getView();
            $view->getTemplate()->addFilter('com://admin/docman.template.filter.overrider');

            $container = $this->getService('com://admin/files.controller.container')->slug($container)->read();

            if (!is_dir($container->path)) {
                $translator = $this->getService('translator')->getTranslator($this->getIdentifier());
                throw new KViewException($translator->translate('Document path is missing. Please make sure there is a folder named %folder% on your site root.', array(
                    '%folder%' => $container->path_value
                )));
            }

            $controller->container($container);

            $config = array(
                'router' => array(
                    'defaults' => (object) array(
                        'option' => 'com_docman',
                        'routed' => '1'
                    )
                ),
                'grid' => array(
                    'layout' => 'details'
                )
            );

            if ($menu = JFactory::getApplication()->getMenu()->getActive())
            {
                $config['router']['defaults']->Itemid = $menu->id;
                // Disable persistency if an upload folder is set for the menu item
                if ($menu->params->get('upload_folder')) {
                    $config['persistent'] = false;
                }
            }

            // Note: PHP converts dots to underscores in cookie names
            $cookie = json_decode(KRequest::get('cookie.com_files_container_docman-files_state', 'raw', ''), true);

            // Check if the folder exists.
            if (is_array($cookie) && isset($cookie['folder']))
            {
                $adapter = clone $container->getAdapter('folder');
                $adapter->setPath($container->path . '/' . $cookie['folder']);
                // Unset folder cookie if path does not exists.
                if (!$adapter->exists())
                {
                    unset($cookie['folder']);
                    //KRequest::set('cookie.com_files_container_docman-files_state', json_encode($cookie));
                    setcookie('com.files.container.docman-files.state', json_encode($cookie), 0, $this->baseurl);
                }
            }

            if ($this->getLayout() === 'default') {
                $view->setLayout('com://admin/docman.view.files.tmpl.app.default');

                $state  = $this->getModel()->getState();

                if ($cookie) {
                    $state->setData($cookie);
                }

                if (!$state->limit) {
                    $state->limit = JFactory::getApplication()->getCfg('list_limit');
                }

                $node_controller = $this->getService('com://admin/files.controller.node')
                    ->setRequest($state->toArray())->format('json');

                $node_controller->thumbnails = $container->parameters->thumbnails;

                $config['initial_response'] = $node_controller->display();

                $html = $controller->limit($state->limit)
                    ->offset($state->offset)
                    ->config($config)
                    ->display();
            }
            else
            {
                $view->setLayout('com://admin/docman.view.files.tmpl.app.compact');
                $config['grid']['layout'] = 'compact';

                $types = empty($state->types) ? array('file', 'image') : $state->types;

                $html = $controller->types($types)->config($config)->display();
            }

            $this->assign('app', $html);
        }

        return parent::display();
    }
}
