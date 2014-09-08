<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanViewJson extends KViewJson
{
    protected static $_public_document_properties = array(
        'id',
        'uuid',
        'title',
        'slug',
        'alias',
        'docman_category_id',
        'description',
        'publish_date',
        'access_title',
        'created_by',
        'created_by_name',
        'image',
        'icon'
    );

    protected static $_public_category_properties = array(
        'id',
        'uuid',
        'title',
        'slug',
        'description',
        'access_title',
        'image',
        'icon'
    );

    /**
     * Returns an array representing the document row and with additional properties for download links and thumbnails
     *
     * @param $document KDatabaseRowInterface     Document row
     *
     * @return array
     */
    protected function _getDocument(KDatabaseRowInterface $document)
    {
        $download_link = $this->createRoute($document, 'view=download&format=html');
        $category_link = $this->createRoute($document->category);

        $result = array(
            'links' => array(
                'self' => array(
                    'href' => $this->createRoute($document, 'layout=default'),
                    'type' => 'application/json'
                )
            ),
            'data' => array(),
            'file' => array(
                'href' => $download_link,
            ),
            'category' => array(
                'href'  => $category_link,
                'type'  => 'application/json',
                'id'    => $document->docman_category_id,
                'title' => $document->category_title,
                'slug'  => $document->category_slug
            )
        );

        $data = $document->toArray();

        if ($document->image) {
            $data['image'] = array('href' => $document->image_path);
        }

        if ($document->icon) {
            $data['icon'] = array('href' => $document->icon_path);
        }

        $this->_filterArray($data, self::$_public_document_properties);

        $result['data'] = $data;

        if ($document->mimetype) {
            $result['file']['type'] = $document->mimetype;
        }

        if ($document->extension) {
            $result['file']['extension'] = $document->extension;
        }

        if ($document->size) {
            $result['file']['size'] = $document->size;
        }

        return $result;
    }

    /**
     * Returns an array representing the document row and with additional properties for download links and thumbnails
     *
     * @param $category KDatabaseRowInterface     Document row
     *
     * @return array
     */
    protected function _getCategory(KDatabaseRowInterface $category)
    {
        $result = array(
            'links' => array(
                'self' => array(
                    'href' => $this->createRoute($category)
                )
            ),
            'data' => array()
        );

        $data = $category->toArray();

        if ($category->image) {
            $data['image'] = array('href' => $category->image_path);
        }

        if ($category->icon) {
            $data['icon'] = array('href' => $category->icon_path);
        }

        $this->_filterArray($data, self::$_public_category_properties);

        $result['data'] = $data;

        return $result;
    }

    /**
     * Takes an array and removes unwanted properties based on the second argument
     *
     * @param array $array              Source array
     * @param array $allowed_properties A list of allowed keys
     */
    protected function _filterArray(array &$array, array $allowed_properties)
    {
        foreach(array_keys($array) as $key)
        {
            if (!in_array($key, $allowed_properties)) {
                unset($array[$key]);
            }
        }
    }

    /**
     * Applies the given callback to each row in the rowset and returns the results as an array
     *
     * @param KDatabaseRowsetInterface $rowset
     * @param array                    $callback
     *
     * @return array
     */
    protected function _filterRowset(KDatabaseRowsetInterface $rowset, $callback)
    {
        $result = array();

        foreach ($rowset as $row) {
            $result[] = call_user_func($callback, $row);
        }

        return $result;
    }

    /**
     * Returns the JSON output
     *
     * @return array
     */
    protected function _getData()
    {
        return array();
    }

    /**
     * Return the views output
     *
     * If the view 'output' variable is empty the output will be generated based on the
     * model data, if it set it will be returned instead.
     *
     * If the model contains a callback state, the callback value will be used to apply
     * padding to the JSON output.
     *
     *  @return string 	The output of the view
     */
    public function display()
    {
        if(empty($this->output))
        {
            $this->output = array_merge(array(
                'version' => '1.0'
            ), $this->_getData());
        }

        $this->_processLinks($this->output);

        return parent::display();
    }

    /**
     * Converts links in an array from relative to absolute
     *
     * @param array $array Source array
     */
    protected function _processLinks(array &$array)
    {
        $base = KRequest::url()->get(KHttpUrl::AUTHORITY);

        foreach ($array as $key => &$value)
        {
            if (is_array($value)) {
                $this->_processLinks($value);
            }
            elseif ($key === 'href') {
                if (substr($value, 0, 4) !== 'http') {
                    $array[$key] = trim($base, '/').$value;
                }
            }
            elseif ($key === 'description') {
                $array[$key] = $this->_processText($value);
            }
        }
    }

    /**
     * Convert links in a text from relative to absolute and runs them through JRoute
     *
     * @param string $text The text processed
     *
     * @return string Text with converted links
     *
     * @since   11.1
     */
    protected function _processText($text)
    {
        $base    = trim(JURI::base(), '/');
        $matches = array();

        preg_match_all("/(href|src)=\"(?!http|ftp|https|mailto|data)([^\"]*)\"/", $text, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $text = str_replace($match[0], $match[1].'="'.$base.JRoute::_($match[2]).'"', $text);
        }

        return $text;
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

        // Add the layout information to the route only if there is no layout information in the menu item
        // and the current layout is not default
        if (!isset($parts['layout']) && !isset($this->getActiveMenu()->query['layout'])
            && $this->getLayout() !== 'default')
        {
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

        return JRoute::_($result, false);
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
}
