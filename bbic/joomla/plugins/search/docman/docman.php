<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class plgSearchDocman extends JPlugin
{
    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);
        $this->loadLanguage();
    }

    /**
     * Overridden to only run if we have Nooku framework installed
     */
    public function update(&$args)
    {
        $return = null;

        if (class_exists('Koowa')) {
            $return = parent::update($args);
        }

        return $return;
    }

    /**
     * @return array An array of search areas
     */
    public function onContentSearchAreas()
    {
        static $areas = array(
            'docman' => 'Documents'
        );

        return $areas;
    }

    /**
     * Weblink Search method
     *
     * The sql must return the following fields that are used in a common display
     * routine: href, title, section, created, text, browsernav
     * @param string Target search string
     * @param string mathcing option, exact|any|all
     * @param string ordering option, newest|oldest|popular|alpha|category
     * @param mixed An array if the search it to be restricted to areas, null if search all
     */
    public function onContentSearch($keyword, $type='', $order='', $areas=null)
    {
        KService::get('koowa:loader')->loadIdentifier('com://admin/docman.init');
        KService::get('com://site/docman.aliases')->setAliases();

        if (is_array($areas)) {
            if (!array_intersect($areas, array_keys($this->onContentSearchAreas()))) {
                return array();
            }
        }

        $keyword = trim($keyword);
        if (empty($keyword)) {
            return array();
        }

        $limit = $this->params->def('search_limit', 50);

        $order_map = array(
            'default' => array('tbl.title', 'ASC'),
            'oldest' => array('tbl.created_on', 'ASC'),
            'newest' => array('tbl.created_on', 'DESC'),
            'category' => array('category_title', 'ASC')
        );

        if (!array_key_exists($order, $order_map)) {
            $order = 'default';
        }
        list($sort, $direction) = $order_map[$order];

        $model = KService::get('com://site/docman.model.documents');
        $model->enabled(1)
            ->status('published')
            ->access(JFactory::getUser()->getAuthorisedViewLevels())
            ->page('all')
            ->search($keyword)
            ->search_by($type)
            ->limit($limit)
            ->sort($sort)
            ->direction($direction);

        $list = $model->getList();

        if (!count($list)) {
            return array();
        }

        $return = array();
        foreach ($list as $item) {
            if (!$item->itemid || !searchHelper::checkNoHTML($item, $keyword, array('title', 'description'))) {
                continue;
            }

            $row = new stdClass();
            $row->created = $item->created_on;

            $row->href = JRoute::_(sprintf('index.php?option=com_docman&view=document&alias=%s&category_slug=%s&Itemid=%d',
                $item->alias, $item->category->slug, $item->itemid));
            $row->browsernav = '';
            $row->title = $item->title;
            $row->section = '';
            $row->text = $item->description;

            $return[] = $row;
        }

        return $return;
    }
}
