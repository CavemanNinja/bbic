<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

defined('_JEXEC') or die;

KService::get('koowa:loader')->loadIdentifier('com://site/docman.parameter.default');

class ComDocmanViewHtml extends ComDefaultViewHtml
{
    protected $_toolbar;

    /**
     * A reference to the component and menu parameters
     */
    protected $_parameters = array();

    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        $this->getTemplate()->addFilter('icon');

        $this->getTemplate()->getFilter('alias')->append(array(
                '@prepareText(' => '$this->getView()->prepareText(',
                '@isRecent('    => '$this->getView()->isRecent(',
                '@event(' => '$this->getView()->callEvent(',
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

    /**
     * Runs a text through content plugins
     *
     * @param $text
     *
     * @return string
     */
    public function prepareText($text)
    {
        $result = JHtml::_('content.prepare', $text);

        // Make sure our script filter does not screw up email cloaking
        if (strpos($result, '<script') !== false) {
            $result = str_replace('<script', '<script inline', $result);
        }

        return $result;
    }

    /**
     * Calls an event and returns the output from plugins
     *
     * @param string     $event        event name
     * @param array|null $arguments    arguments to pass
     * @param string     $import_group if supplied, this group of plugins will be imported
     *
     * @return string
     */
    public function callEvent($event, $arguments, $import_group = 'content')
    {
        if ($import_group) {
            JPluginHelper::importPlugin($import_group);
        }

        $results = JDispatcher::getInstance()->trigger($event, $arguments);

        return trim(implode("\n", $results));
    }

    public function setToolbar($toolbar)
    {
        $this->_toolbar = $toolbar;

        return $this;
    }

    public function getToolbar()
    {
        return $this->_toolbar;
    }

    /**
     * Get menu or component parameters
     *
     * @param string $type component or menu
     */
    public function getParameters($type = 'menu')
    {
        if (!isset($this->_parameters[$type])) {
            $params = false;

            if ($type == 'menu') {
                $params = $this->getActiveMenu()->params;
            } elseif ($type == 'component') {
                $params = JFactory::getApplication()->getParams();
            }

            $this->_parameters[$type] = $params ? $this->getService('com://site/docman.parameter.default', array('object' =>$params)) : false;
        }

        return $this->_parameters[$type];
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
     * Returns pathway object
     *
     * @return JPathway
     */
    public function getPathway()
    {
        return JFactory::getApplication()->getPathway();
    }

    public function display()
    {
        $this->assign('menu', $this->getActiveMenu());
        $this->assign('params', $this->getParameters());
        $this->assign('config', $this->getService('com://admin/docman.database.row.config'));

        $this->_preparePage();
        $this->_generatePathway();

        return parent::display();
    }

    /**
     * Returns true if the document shoul have a badge marking it as new
     *
     * @param KDatabaseRowInterface $document
     *
     * @return bool
     */
    public function isRecent(KDatabaseRowInterface $document)
    {
        $result = false;

        $days_for_new = $this->getParameters()->get('days_for_new');

        if (!empty($days_for_new))
        {
            $post = strtotime($document->created_on);
            $new = time() - ($days_for_new*24*3600);
            if ($post >= $new) {
                $result = true;
            }
        }

        return $result;
    }

    /**
     * Converts a row object into a query string
     *
     * @param KDatabaseRowInterface $row
     *
     * @return string
     */
    protected function _createRouteFromRow(KDatabaseRowInterface $row)
    {
        $name  = $row->getIdentifier()->name;
        $query = array(
            'view' => $name,
            'slug' => $row->slug
        );

        if ($row instanceof ComDocmanDatabaseRowCategory) {
            $query['view'] = 'list';
        }
        elseif ($row instanceof ComDocmanDatabaseRowDocument)
        {
            // Documents use aliases (id-slug) in URLs
            $query['alias'] = $row->alias;
            unset($query['slug']);

            if ($row->category_slug) {
                $query['category_slug'] = $row->category_slug;
            }
        }

        $route = http_build_query($query, '', '&');

        return $route;
    }

    /**
     * Create a route based on a query string or a row.
     *
     * If you pass a row object it is passed to _createRouteFromRow() and converted to a string
     *
     * option, view and layout will automatically be added if they are omitted.
     *
     * In templates, use @route()
     *
     * @param string|KDatabaseRowInterface $route      The query string or a row
     * @param string                       $parameters A query string for extra parameters.
     *
     * @return string The route
     */
    public function createRoute($route = '', $parameters = '')
    {
        $parts = array();

        // Convert parameters to an array
        if (!empty($parameters)) {
            parse_str($parameters, $parts);
        }

        // Convert row object to query string first
        if ($route instanceof KDatabaseRowInterface) {
            $route = $this->_createRouteFromRow($route);
        }

        // $parameters elements always take precendence
        parse_str($route, $tmp);
        $parts = array_merge($tmp, $parts);

        if (!isset($parts['option'])) {
            $parts['option'] = 'com_'.$this->getIdentifier()->package;
        }

        if (!isset($parts['view'])) {
            $parts['view'] = $this->getName();
        }

        // Add the layout information to the route only if there is no layout information
        // in the menu item and the current layout is not default
        if (!isset($parts['layout']) && $this->getLayout() !== 'default') {
            $parts['layout'] = $this->getLayout();
        }

        // Add the format information to the URL only if it's not 'html'
        if (!isset($parts['format']) && $this->getIdentifier()->name !== 'html') {
            $parts['format'] = $this->getIdentifier()->name;
        }

        if (!isset($parts['Itemid'])) {
            $parts['Itemid'] = $this->getActiveMenu()->id;
        }

        // We don't add the category path to the documents view URLs
        if ($this->getActiveMenu()->query['view'] === 'filteredlist') {
            unset($parts['category_slug']);
        }

        $result = 'index.php?'.http_build_query($parts, '', '&');

        return JRoute::_($result);
    }

    protected function _generatePathway($category = null, $document = null)
    {
        $view    = $this->getName();
        $query   = $this->getActiveMenu()->query;
        $pathway = $this->getPathway();
        $link    = null;
        $query_slug = isset($query['slug']) ? $query['slug'] : '';

        // Joomla handles it all when the view is document or submit
        // or when we are on a menu item set to the current category or document
        if (!in_array($view, array('document', 'list', 'submit'))
            || ($query['view'] === 'document' && $view === 'document' && $query['slug'] === $document->slug)
            || ($query['view'] === 'list' && $view === 'list' && !empty($category)
                && $query_slug === (string) $category->slug)
        ) {
            return;
        }

        if ($category && $query_slug !== $category->slug)
        {
            $vars = array(
                'option' => 'com_docman',
                'view'   => $query['view'],
            );

            if (isset($query['layout'])) {
                $vars['layout'] = $query['layout'];
            }

            $pass = !empty($query_slug);
            foreach ($category->getAncestors() as $ancestor)
            {
                if ($query['view'] === 'list' && $query_slug === $ancestor->slug)
                {
                    /*
                     * We reached to the point of menu category
                     * From this point on all categories should be displayed
                     */
                    $pass = false;
                    continue;
                }

                if ($pass) {
                    continue;
                }

                $vars['slug'] = $ancestor->slug;
                $link = 'index.php?'.http_build_query($vars, null, '&');

                $pathway->addItem($ancestor->title, JRoute::_($link));
            }

            // Link the category if the view is category instead of document
            $vars['slug'] = $category->slug;
            $link = $view === 'list' ? '' : JRoute::_('index.php?'.http_build_query($vars, null, '&'));
            $pathway->addItem($category->title, $link);
        }

        if ($document) {
            $pathway->addItem($document->title, $link);
        }
    }

    /**
     * Set page title, add metadata
     */
    protected function _preparePage()
    {
        $document = JFactory::getDocument();
        $params   = $this->getParameters();

        // Set robots
        if (KRequest::get('get.print', 'boolean')) {
            $params->robots = 'noindex, nofollow';
        }

        if ($params->robots) {
            $document->setMetadata('robots', $params->robots);
        }

        // Set keywords
        if ($params->{'menu-meta_keywords'}) {
            $document->setMetadata('keywords', $params->{'menu-meta_keywords'});
        }

        // Set description
        if ($params->{'menu-meta_description'}) {
            $document->setDescription($params->{'menu-meta_description'});
        }

        // Add page class
        if ($params->pageclass_sfx)
        {
            if (!$this->getTemplate()->hasFilter('bootstrap')) {
                $this->getTemplate()->addFilter('bootstrap');
            }

            $filter = $this->getTemplate()->getFilter('bootstrap');

            // Escape and append the page class to the namespace
            $namespace = $filter->getNamespace().htmlspecialchars($params->pageclass_sfx);

            $filter->setNamespace($namespace);
        }

        // Set page title
        $this->_setPageTitle();
    }

    /*
     * Sets the page title
     */
    protected function _setPageTitle()
    {
        $app      = JFactory::getApplication();
        $document = JFactory::getDocument();
        $menu     = $this->getActiveMenu();
        $params   = $this->getParameters();
        $title    = null;

        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $params->def('page_heading', $params->get('page_title', ''));

        $title = $params->get('page_title', $menu->title);

        if (empty($title)) {
            $title = $app->getCfg('sitename');
        }
        elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
            $title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
        }
        elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
            $title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
        }

        $document->setTitle($title);
    }

    /**
     * Adds some information to the document row like download links and thumbnails
     *
     * @param $document KDatabaseRowInterface     Document row
     * @param $params   ComDocmanParameterDefault Page parameters
     */
    protected function _prepareDocument(&$document, $params)
    {
        // Always supply an icon
        if (empty($document->params->icon)) {
            $document->params->icon = 'default.png';
        }

        if (JFactory::getUser()->guest)
        {
            // Chrome caches the page so without this download links won't work after user hits the back button
            $login_to_download = $this->getService('com://admin/docman.model.configs')->getItem()->login_to_download;
            if ($login_to_download && !$document->canPerform('download'))
            {
                JResponse::setHeader('Cache-Control', 'no-store', false);
                JResponse::setHeader('Expires', '-1', true);
            }
        }


        $document->document_link = $this->createRoute($document, 'layout=default');
        $document->download_link = $this->createRoute($document, 'view=download');

        $link_to = $params->document_title_link;
        $link = '';
        if ($link_to == 'download') {
            $link = $document->download_link;
        } elseif ($link_to == 'details') {
            $link = $document->document_link;
        }

        $document->title_link = $link;

        $document->thumbnail_path = null;
        $document->image_path = null;
        if ($document->image)
        {
            $document->thumbnail_path = KRequest::root().'/joomlatools-files/docman-images/'.$document->image;

            if ($document->isImage() && $document->canPerform('download')) {
                $document->image_path = $document->download_link;
            } else {
                $document->image_path = $document->thumbnail_path;
            }
        }
    }
}
