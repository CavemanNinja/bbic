<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanViewSubmitHtml extends ComKoowaViewHtml
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'auto_fetch' => false
        ));

        parent::_initialize($config);
    }

    protected function _fetchData(KViewContext $context)
    {
        $state  = $this->getModel()->getState();
        $menu   = JFactory::getApplication()->getMenu()->getActive();
        $params = new ComKoowaDecoratorParameter(new KObjectConfig(array('delegate' => $menu->params)));

        if (!$params->page_heading) {
            $params->page_heading = $menu->title;
        }

        $menu_folder = $params->get('folder');

        if (!$state->folder || ($menu_folder && strpos($state->folder, $menu_folder) !== 0)) {
            $state->folder = $menu_folder;
        }
        
        $context->data->menu   = $menu;
        $context->data->params = $params;
        
        parent::_fetchData($context);
    }


    /**
     * Returns currently active menu item
     *
     * Default menu item for the site will be returned if there is no active menu items
     *
     * @return object
     */
    public function getActiveMenu()
    {
        $menu = JFactory::getApplication()->getMenu()->getActive();
        if (is_null($menu)) {
            $menu = JFactory::getApplication()->getMenu()->getDefault();
        }

        return $menu;
    }

    /**
     * Create a route based on a query string.
     *
     * Automatically adds the menu item ID to links
     *
     * {@inheritdoc}
     */
    public function getRoute($route = '', $fqr = false, $escape = true)
    {
        if (is_string($route)) {
            parse_str(trim($route), $parts);
        } else {
            $parts = $route;
        }

        if (!isset($parts['Itemid'])) {
            $parts['Itemid'] = $this->getActiveMenu()->id;
        }

        return parent::getRoute($parts, $fqr, $escape);
    }
}
