<?php
/**
 * FW Gallery Search plugin 3.0
 * @copyright (C) 2014 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

class plgSearchFwGallery extends JPlugin {
	function onContentSearchAreas() {
		return $this->onSearchAreas();
	}
	function onSearchAreas() {
		static $areas = array(
			'fwgallery' => 'FW Gallery'
		);
		return $areas;
	}
	function onContentSearch($text, $phrase='', $ordering='', $areas=null) {
		return $this->onSearch($text, $phrase, $ordering, $areas);
	}
	function onSearch($text, $phrase='', $ordering='', $areas=null) {
		if ($text == '') {
			return array();
		}
		$limit = is_object($this->params)?$this->params->get('search_limit', 50):50;
		if (!$limit) return array();
		if (is_array($areas)) {
			if (!array_intersect($areas, array_keys(plgSearchFwGallery :: onSearchAreas()))) {
				return array();
			}
		}
		$db = JFactory :: getDBO();

		$wh_p = $wh_i = '';
		switch ($phrase) {
			case 'exact':
				$text = $db->Quote('%'.$db->escape($text, true).'%', false);
				$wheres2 = array();
				$wheres2[] = 'f.name LIKE '.$text;
				$wheres2[] = 'f.descr LIKE '.$text;
				$wh_i = '(' . implode( ') OR (', $wheres2 ) . ')';

				$wheres2 = array();
				$wheres2[] 	= 'p.name LIKE '.$text;
				$wheres2[] 	= 'p.descr LIKE '.$text;
				$wh_p = '(' . implode( ') OR (', $wheres2 ) . ')';
				break;

			case 'all':
			case 'any':
			default:
				$words = explode(' ', $text);
				$whs_i = $whs_p = array();
				foreach ($words as $word) {
					$word = $db->Quote('%'.$db->escape($word, true).'%', false);
					$wheres2 = array();
					$wheres2[] = 'f.name LIKE '.$word;
					$wheres2[] = 'f.descr LIKE '.$word;
					$whs_i[] = implode(' OR ', $wheres2);

					$wheres2 = array();
					$wheres2[] 	= 'p.name LIKE '.$word;
					$wheres2[] 	= 'p.descr LIKE '.$word;
					$whs_p[] = implode(' OR ', $wheres2);
				}
				$wh_i = '(' . implode((($phrase == 'all') ? ') AND (' : ') OR ('), $whs_i ) . ')';
				$wh_p = '(' . implode((($phrase == 'all') ? ') AND (' : ') OR ('), $whs_p ) . ')';
				break;
		}

		switch ( $ordering ) {
			case 'alpha':
				$order = 'name ASC';
				break;
			case 'category':
				$order = 'category_name ASC';
				break;
			case 'popular':
				$order = 'hits DESC';
				break;
			case 'newest':
				$order = 'created DESC';
				break;
			case 'oldest':
				$order = 'created ASC';
				break;
			default:
				$order = 'name DESC';
		}

		$db->setQuery('
SELECT
	f.id,
	f.name,
	f.name AS title,
	f.descr AS `text`,
	f.created,
	f.hits,
	"2" AS browsernav,
	\'image\' AS `type`
FROM
	#__fwg_files AS f
WHERE
	('.$wh_i.')
	AND
	f.published = 1

UNION

SELECT
	p.id,
	p.name,
	p.name AS title,
	p.descr AS `text`,
	p.created,
	(SELECT SUM(ff.hits) FROM #__fwg_files AS ff WHERE ff.project_id = p.id AND ff.published = 1) AS hits,
	"2" AS browsernav,
	\'gallery\' AS `type`
FROM
	#__fwg_projects AS p
WHERE
	('.$wh_p.')
	AND
	p.published = 1

ORDER BY '.$order,
			0,
			$limit
		);
		$rows = (array)$db->loadObjectList();

		foreach ($rows as $i => $row) {
			$rows[$i]->href = JRoute :: _('index.php?option=com_fwgallery&view='.$row->type.'&id='.$row->id.':'.JFilterOutput :: stringURLSafe($row->name));
			$rows[$i]->section = 'FW Gallery';
		}
		return $rows;
	}
}
?>