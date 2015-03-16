<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanViewFolderHtml extends ComKoowaViewHtml
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->auto_fetch = false;

        parent::_initialize($config);
    }

    protected function _fetchData(KViewContext $context)
	{
        $state  = $this->getModel()->getState();

        $parts  = explode('/', $state->folder);
        $state->name   = array_pop($parts);
        $state->folder = implode('/', $parts);

        $folder = $this->getModel()->reset()->fetch();

		$menu   = JFactory::getApplication()->getMenu()->getActive();
        $params = new ComKoowaDecoratorParameter(new KObjectConfig(array('delegate' => $menu->params)));

        $state->setProperty('sort', 'default', $params->sort);
        $state->setProperty('direction', 'default', $params->direction);
        $state->setProperty('container', 'internal', true);

        $state->folder = ($state->folder ? $state->folder.'/' : '').$state->name;
        $state->name   = null;

		$query = $state->getValues();
		$query['folder'] = isset($query['folder']) ? rawurldecode($query['folder']) : '';
        $query['thumbnails'] = (bool) $params->show_thumbnails;

		if ($this->getLayout() === 'gallery') {
			$query['types'] = array('image');
		}

		$folders = array();
		if ($params->show_folders)
        {
            $folder_controller = $this->getObject('com:files.controller.folder');
            $folder_controller->getRequest()->setQuery($query);

            $folders = $folder_controller->browse();
		}

		$file_controller = $this->getObject('com:files.controller.file');
        $file_controller->getRequest()->setQuery($query);

		$files = $file_controller->browse();
		$total = $file_controller->getModel()->count();

        $humanize = $params->humanize_filenames;
		foreach ($folders as $f) {
            $f->display_name = $humanize ? ucfirst(preg_replace('#[-_\s\.]+#i', ' ', $f->name)) : $f->name;
		}
		
		foreach ($files as $f) {
            $f->display_name = $humanize ? ucfirst(preg_replace('#[-_\s\.]+#i', ' ', $f->filename)) : $f->name;
		}
		
		$parent = null;
		if ($menu->query['folder'] !== $folder->path)
		{
			$path   = explode('/', $folder->path);
			$parent = count($path) > 1 ? implode('/', array_slice($path, 0, count($path)-1)) : '';

            $params->page_heading = ucfirst($folder->name);
		}

        if (!$params->page_heading) {
            $params->page_heading = $menu->title;
        }
		
		if (!$this->getObject('user')->isAuthentic()) {
		    $params->allow_uploads = false;
		}

		$context->data->folder = $folder;
		$context->data->files  = $files;
		$context->data->total  = $total;
		$context->data->folders = $folders;
		$context->data->parent  = $parent;
		$context->data->params  = $params;
		$context->data->menu    = $menu;
		$context->data->thumbnail_size = array('x' => 200, 'y' => 150);

        $this->_setPathway($context);

		parent::_fetchData($context);

        $context->parameters->total = $total;
	}

	protected function _setPathway(KViewContext $context)
	{
		if ($context->data->parent !== null)
		{
			$pathway = JFactory::getApplication()->getPathway();

			$path   = $context->data->folder->path;
            $menu   = $context->data->menu;
            $append = '';

			if (!empty($menu->query['folder']) && strpos($path, $menu->query['folder']) === 0)
            {
				$path = substr($path, strlen($menu->query['folder'])+1, strlen($path));
			    $append = $menu->query['folder'];
			}

			$parts = explode('/', $path);

			foreach ($parts as $i => $part)
			{
				if ($part !== $context->data->folder->name)
				{
					$path = $append.'/'.implode('/', array_slice($parts, 0, $i+1));
					$link = JRoute::_('index.php?option=com_fileman&layout='.$this->getLayout().'&view=folder&folder='.$path);
				}
				else $link = '';

				$pathway->addItem(ucfirst($part), $link);
			}
		}
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