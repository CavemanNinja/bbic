<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

/**
 * Routes requests marked with routed=1 through com_files
 *
 */
class ComFilemanDispatcherBehaviorRoutable extends KControllerBehaviorAbstract
{
    protected function _setContainer(KControllerContextInterface $context)
    {
        $context->request->query->container = 'fileman-files';
    }

    protected function _attachBehaviors(KControllerContextInterface $context)
    {
        $this->getObject('manager')->getClassLoader()->load('ComFilemanControllerPermissionAbstract');

        $behaviors = array(
            'com:files.controller.behavior.cacheable' => array(
                'group' => 'com_fileman.files'
            ),
            'permissible' => array(
                'permission' => 'com://admin/fileman.controller.permission.file'
            )
        );

        // Use our own ACL and cache the hell out of JSON requests
        foreach (array('file', 'folder', 'node', 'thumbnail') as $name)
        {
            $this->getIdentifier('com:files.controller.'.$name)->getConfig()->append(array(
                'behaviors' => $behaviors
            ));
        }

        if ($this->getMixer()->getIdentifier()->domain === 'site')
        {
            $this->getIdentifier('com:files.controller.file')->getConfig()->append(array(
                'behaviors' => array('com://site/fileman.controller.behavior.notifiable')
            ));
        }
    }

    protected function _beforeDispatch(KControllerContextInterface $context)
    {
        $query = $context->request->query;

        if ($query->routed || $query->view === 'filelink' || ($query->view === 'files'
                && (!$query->has('layout')) || in_array($query->layout, array('default', 'select'))))
        {
            $layout = $query->layout;
            $view   = $query->view;
            $tmpl   = $query->tmpl;

            $this->_setContainer($context);
            $this->_attachBehaviors($context);


            $config = array(
                'can_upload' => $context->getUser()->authorise('core.create', 'com_fileman'),
                'grid' => array(
                    'layout' => 'compact'
                )
            );

            if ($layout === 'select' || $view === 'filelink')
            {
                $query->tmpl = 'joomla';

                $query->layout = 'compact';
                $query->types = array('file', 'image');

                if ($view === 'filelink') {
                    $query->editor = $query->e_name;
                    $query->view   = 'files';
                }
            }
            else
            {
                $config['grid']['layout'] = 'icons';
                $query->layout = 'default';
            }

            if ($layout === 'upload') {
                $query->layout = 'compact_upload';

                $config['multi_selection'] = true;
                $config['tree'] = false;
                $config['initial_response'] = false;

                $menu = JFactory::getApplication()->getMenu()->getActive();

                $base_path = $context->request->getUrl()->toString(KHttpUrl::AUTHORITY);
                $menu_path = JRoute::_('index.php?option=com_fileman&Itemid='.$menu->id, false);
                $menu_path = $this->getObject('filter.factory')->createChain('url')->sanitize($menu_path);

                $config['base_url'] = $base_path.$menu_path;

                if ($menu->query['layout'] === 'gallery') {
                    $query->types = array('image');
                }
            }

            $query->config = $config;

            $context->param = 'com:files.dispatcher.http';
            $this->getMixer()->execute('forward', $context);

            $query->layout = $layout;
            $query->view   = $view;
            $query->tmpl   = $tmpl;

            unset($context->param);

            if ($query->routed)
            {
                // Work-around the bug here: http://joomlacode.org/gf/project/joomla/tracker/?action=TrackerItemEdit&tracker_item_id=28249
                JFactory::getSession()->set('com.files.fix.the.session.bug', microtime(true));

                return false;
            }
        }
    }
}
