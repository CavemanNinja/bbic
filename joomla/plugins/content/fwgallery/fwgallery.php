<?php
/**
 * FW Gallery content plugin 3.0
 * @copyright (C) 2014 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 * @example {fwgallery id:21} - it will show gallery image with ID 21
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

class plgContentFwGallery extends JPlugin {
	function onContentPrepare($context, &$row, &$params, $page = 0) {
		$this->onPrepareContent($row, $params, $page);
	}
	function onPrepareContent( &$article, &$params, $limitstart = 0 ) {
		$path = JPATH_SITE.'/components/com_fwgallery/';
		if (file_exists($path) and preg_match_all('/\{fwgallery([^\}]+)\}/i', $article->text, $matches, PREG_SET_ORDER)) {
			jimport('joomla.application.component.model');
			JTable :: addIncludePath(JPATH_SITE.'/administrator/components/com_fwgallery/tables');
			JModelLegacy :: addIncludePath($path.'models');
			if ($model = JModelLegacy :: getInstance('Image', 'fwGalleryModel')) {
				include_once($path.'helpers/helper.php');
				JHTML :: stylesheet('plugins/content/fwgallery/fwgallery/assets/css/style.css');

				if (!is_object($this->params)) {
					$plugin = JPluginHelper :: getPlugin('content', 'fwgallery');
					jimport('joomla.html.parameter');
					$this->params = new JParameter($plugin->params);
				}
				$is_image_loaded = false;
				$this->user = JFactory :: getUser();
				$component_params = JComponentHelper :: getParams('com_fwgallery');
				$this->new_days = $component_params->get('new_days');
				foreach ($matches as $match) {
					$link = '';
					$name = '';
					$html = '';
					$id = 0;
					if (preg_match('/category_id:\s*(\d+)/', $match[1], $id_match)) {
						$db = JFactory :: getDBO();
						$db->setQuery('
SELECT
	p.id,
	p.name,
	CASE WHEN (SELECT id FROM #__fwg_files AS f WHERE f.project_id = p.id AND selected = 1 LIMIT 1) IS NOT NULL THEN (SELECT id FROM #__fwg_files AS f WHERE f.project_id = p.id AND selected = 1 LIMIT 1) ELSE (SELECT id FROM #__fwg_files AS f WHERE f.project_id = p.id ORDER BY ordering LIMIT 1) END AS image_id
FROM
    #__fwg_projects AS p
WHERE
	p.id = '.(int)$id_match[1]);
						if ($obj = $db->loadObject() and $this->row = $model->getObj($obj->image_id)) {
							$link = JRoute::_('index.php?option=com_fwgallery&view=gallery&id='.$obj->id.':'.JFilterOutput :: stringURLSafe($obj->name).'&Itemid='.JFHelper :: getItemid('gallery', $obj->id, JRequest :: getInt('Itemid')).'#fwgallerytop');
							$name = $obj->name;
						}
					} elseif (preg_match('/id:\s*(\d+)/', $match[1], $id_match)) {
						if ($id = $id_match[1] and $this->row = $model->getObj($id)) {
							$link = JRoute::_('index.php?option=com_fwgallery&view=image&id='.$this->row->id.':'.JFilterOutput :: stringURLSafe($this->row->name).'&Itemid='.JFHelper :: getItemid('image', $this->row->id, JRequest :: getInt('Itemid')).'#fwgallerytop');
							$name = $this->row->name;
						}
					}
					if (!empty($this->row->id)) {
						$is_image_loaded = true;
						ob_start();
						include(dirname(__FILE__).'/fwgallery/tmpl/image.php');
						$html = ob_get_clean();
					}
					$article->text = str_replace($match[0], $html, $article->text);
				}
				if ($is_image_loaded and !defined('FWGALLERY_LIGHTBOX_LOADED')) {
					define('FWGALLERY_LIGHTBOX_LOADED', true);
					ob_start();
					include($path.'views/gallery/tmpl/default_scripts.php');
					$article->text .= ob_get_clean();
				}
			}
		}
	}
}
?>