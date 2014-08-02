<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanViewJson extends KViewJson
{
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
                    'href' => $this->createRoute($document),
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

        $result['data'] = $data;

        return $result;
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
        return KInflector::isPlural($this->getName()) ? $this->_getList() : $this->_getItem();
    }

    /**
     * Returns the JSON output for items
     *
     * @return array
     */
    protected function _getItem()
    {
        $type   = KInflector::singularize($this->getName());
        $method = '_get'.ucfirst($type);
        $data   = $this->$method($this->getModel()->getItem());

        return $data;
    }

    /**
     * Returns the JSON output for lists
     *
     * @return array
     */
    protected function _getList()
    {
        $name   = $this->getName();
        $type   = KInflector::singularize($name);
        $filter = array($this, '_get'.ucfirst($type));

        $model = $this->getModel();
        $data  = array(
            'links' => array(
                'self' => array('href' => (string) KRequest::url())
            ),
            $name => array(
                'offset'   => (int) $model->offset,
                'limit'    => (int) $model->limit,
                'total'	   => $model->getTotal(),
                'items'    => $this->_filterRowset($this->getModel()->getList(), $filter)
            )
        );

        return $data;
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

        // $parameters elements always take precedence
        parse_str($route, $tmp);
        $parts = array_merge($tmp, $parts);

        if (!isset($parts['option'])) {
            $parts['option'] = 'com_'.$this->getIdentifier()->package;
        }

        if (!isset($parts['view'])) {
            $parts['view'] = $this->getName();
        }

        if (!isset($parts['layout']) && $this->getLayout() !== 'default') {
            $parts['layout'] = $this->getLayout();
        }

        // Add the format information to the URL only if it's not 'html'
        if (!isset($parts['format']) && $this->getIdentifier()->name !== 'html') {
            $parts['format'] = $this->getIdentifier()->name;
        }

        $result = 'index.php?'.http_build_query($parts, '', '&');

        return JRoute::_($result, false);
    }
}
