<?php
/*
	JoomlaXTC Deluxe News

	version 2.0.0

	Copyright (C) 2008-2012 Monev Software LLC.	All Rights Reserved.

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

	THIS LICENSE IS NOT EXTENSIVE TO ACCOMPANYING FILES UNLESS NOTED.

	See COPYRIGHT.php for more information.
	See LICENSE.php for more information.

	Monev Software LLC
	www.joomlaxtc.com
*/

defined( '_JEXEC' ) or die;

jimport( 'joomla.html.parameter' );


if (!function_exists('npMakeLink')) {
	function npMakeLink($link,$label,$target) {
		$label = ($label) ? $label : $link;
		switch ($target) {
			case 1: // open in a new window
				$html = '<a href="'.htmlspecialchars($link).'" target="_blank" rel="nofollow">'.htmlspecialchars($label).'</a>';
				break;
			case 2: // open in a popup window
				$attribs = 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=600,height=600';
				$html = "<a href=\"".htmlspecialchars($link)."\" onclick=\"window.open(this.href,'targetWindow','".$attribs."');return false;\">".htmlspecialchars($label).'</a>';
				break;
			case 3: // open in a modal window
				JHtml::_('behavior.modal', 'a.modal');
				$html = '<a class="modal" href="'.htmlspecialchars($link).'" rel="{handler:\'iframe\',size:{x:600,y:600}}">'.htmlspecialchars($label).'</a>';
				break;
			default: // open in parent window
				$html = '<a href="'.htmlspecialchars($link).'" rel="nofollow">'.htmlspecialchars($label).'</a>';
				break;
		}
		return $html;
	}
}

//Core calls
$live_site = JURI::base();
$doc = JFactory::getDocument();
$db = JFactory::getDBO();
$moduleDir = 'mod_jxtc_newspro';

$user = JFactory::getUser();
$contentconfig = JComponentHelper::getParams('com_content');

require_once (JPATH_SITE.'/components/com_content/helpers/route.php');

//Core Vars

$userid = $user->get('id');
$nullDate = $db->getNullDate();
$date = JFactory::getDate();
$now = $date->toSQL();

//Parameters
$artid = trim($params->get('artid', ''));
$avatarw = $params->get('avatarw');
$avatarh = $params->get('avatarh');
$compat = $params->get('compat','none');
$comcompat = $params->get('comcompat','none');
$catid = $params->get('catid',0);

$group = $params->get('group', 0);
$sortorder = $params->get('sortorder', 3);
$order = $params->get('order', 3);
$rows = $params->get('rows', 1);
$columns = $params->get('columns', 1);
$template = $params->get('template', '');
$moduletemplate = trim($params->get('modulehtml', '{mainarea}'));
$itemtemplate = trim($params->get('html', '{intro}'));
$mainmaxtitle = $params->get('maxtitle', '');
$mainmaxtitlesuf = $params->get('maxtitlesuf', '...');
$mainmaxintro = $params->get('maxintro', '');
$mainmaxintrosuf = $params->get('maxintrosuf', '...');
$mainmaxtext = $params->get('maxtext', '');
$mainmaxtextsuf = $params->get('maxtextsuf', '...');
$maintextbrk = $params->get('textbrk', '');
$dateformat = trim($params->get('dateformat', 'Y-m-d'));
$enablerl = $params->get('enablerl', 0);

if ($template && $template != -1) {
    $moduletemplate = file_get_contents(JPATH_ROOT.'/modules/'.$moduleDir.'/templates/'.$template.'/module.html');
    $itemtemplate = file_get_contents(JPATH_ROOT.'/modules/'.$moduleDir.'/templates/'.$template.'/element.html');
    if (file_exists(JPATH_ROOT.'/modules/'.$moduleDir.'/templates/'.$template.'/template.css')) {
        $doc->addStyleSheet($live_site . 'modules/'.$moduleDir.'/templates/' . $template . '/template.css', 'text/css');
    }
}

// Build Query
$query = 'SELECT a.id, a.access,a.introtext,a.fulltext, a.title,UNIX_TIMESTAMP(a.created) as created,UNIX_TIMESTAMP(a.modified) as modified, a.catid, a.created_by, a.created_by_alias, a.hits, a.alias, a.images, a.urls,
	cc.title as cat_title, cc.params as cat_params, cc.description as cat_description, cc.alias as cat_alias, cc.id as cat_id,
	u.name as author, u.username as authorid,
	CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug,
	CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug
	FROM #__content AS a';
$query .= ' INNER JOIN #__categories AS cc ON cc.id = a.catid
	LEFT JOIN #__users AS u ON u.id = a.created_by';
$query .= ' WHERE a.state = 1 ';
$query .= 'AND ( a.publish_up = ' . $db->Quote($nullDate) . ' OR a.publish_up <= ' . $db->Quote($now) . ' )
	AND ( a.publish_down = ' . $db->Quote($nullDate) . ' OR a.publish_down >= ' . $db->Quote($now) . ' )
	AND (cc.published = 1 OR cc.published IS NULL)';

if ($artid) {
    $articles = explode(',', $artid);
    JArrayHelper::toInteger($articles);
    $query .= ' AND a.id in (' . join(',', $articles) . ') ';
} else {
  if ($catid) {
    if (is_array($catid)) {
      if ($catid[0] != 0) {
				$query .= ' AND (cc.id='.join(' OR cc.id=', $catid).')';
      }
    }
    else {
			$query .= ' AND (cc.id = ' . $catid . ')';
    }
  }
}

if ($group == 1) {
    $query .= ' GROUP BY a.created_by';
}
$query .= ' ORDER BY ';

$aux = ($order == '0') ? ' ASC ' : ' DESC ';

switch ($sortorder) {
    case 0: // creation
        $query .= 'a.created'.$aux;
        break;
    case 1: // modified
        $query .= 'a.modified'.$aux;
        break;
    case 2: // hits
        $query .= 'a.hits'.$aux;
        break;
    case 3: // joomla order
        $query .= 'a.ordering'.$aux;
        break;
    case 5: // Category Title
        $query .= 'cc.title'.$aux;
        break;
    case 6: // Article Title
        $query .= 'a.title'.$aux;
        break;
    case 7:
        $query .= 'RAND()';
        break;
}

// echo nl2br(str_replace('#__','jos_',$query));

$mainqty = $columns * $rows;
$db->setQuery($query, 0, $mainqty);
$items = $db->loadObjectList();
$cloneditems = $items;
if (count($items) == 0) return; // Return if empty

$rowmaxintro = $mainmaxintro;
$rowmaxintrosuf = $mainmaxintrosuf;
$rowmaxtitle = $mainmaxtitle;
$rowmaxtitlesuf = $mainmaxtitlesuf;
$rowmaxtext = $mainmaxtext;
$rowmaxtextsuf = $mainmaxtextsuf;
$rowtextbrk = $maintextbrk;

// Check for RL support
if ($enablerl && stripos($mainareahtml,'{readinglist}')!==false) {
	jimport( 'joomla.plugin.helper' );
	if (JpluginHelper::isEnabled('content','jxtcreadinglist')) {
		echo 'IS ENABLED';
	}
}
else $enablerl = false;

// Check for RL support
$enablerl = false;
if (stripos($itemtemplate,'{readinglist}')!==false) {
	jimport( 'joomla.plugin.helper' );
	$enablerl = JpluginHelper::isEnabled('content','jxtcreadinglist');
}

require JModuleHelper::getLayoutPath($module->module, $params->get('layout', 'default'));

echo '<div id="' . $jxtc . '">' . $modulehtml . '</div>';