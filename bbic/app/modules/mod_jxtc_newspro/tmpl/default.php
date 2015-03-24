<?php
/*
	JoomlaXTC Wall Renderer

	version 1.8

	Copyright (C) 2010-2011  Monev Software LLC.	All Rights Reserved.

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

if (!defined( '_JEXEC' )) die( 'Direct Access to this location is not allowed.' );

// Render
$jxtc = uniqid('jxtc');

$cell_width = intval(100 / $columns);
$pageshtml = '';
$mainareahtml = '<div class="showcase'.$jxtc.'">';
$index=1;

	if (empty($items)) { continue; }
	$mainareahtml .= '<div class="sframe'.$jxtc.'"';
							
	$mainareahtml .= '><table class="table'.$jxtc.' singlepage page-1" width="100%" cellpadding="0" cellspacing="0">';

	for ($r=1;$r<=$rows;$r++) {
		if (empty($items)) { continue; }
		if ($rows == 1) { $rowclass = 'singlerow'; }	// Row class
		elseif ($r == 1) { $rowclass = 'firstrow'; }
		elseif ($r == $rows) { $rowclass = 'lastrow'; }
		else { $rowclass = 'centerrow'; }
		$mainareahtml .= '<tr class="'.$rowclass.' row-'.$r.'">';
		for ($c=1;$c<=$columns;$c++) {
			$item = array_shift($items);
			if (!empty($item)) {
				$itemhtml = $itemtemplate;
				require JModuleHelper::getLayoutPath($module->module, 'default_parse');
				if ($columns == 1) { $colclass = 'singlecol'; } 	// Col class
				elseif ($c == 1) { $colclass = 'firstcol'; }
				elseif ($c == $columns) { $colclass = 'lastcol'; }
				else { $colclass = 'centercol'; }
				$mainareahtml .= '<td class="'.$colclass.' col-'.$c.'" style="width:'.$cell_width.'%" >'.$itemhtml.'</td>';
				$index++;
			}
		}
		$mainareahtml .='</tr>';
	}
	$mainareahtml .= '</table></div>';
$mainareahtml .= '</div>';

// preps
	$css = $params->get('css');
	if ($css) { $doc->addStyleDeclaration($css); }

$doc->addStyleSheet($live_site.'modules/'.$moduleDir.'/css/wall.css','text/css');
$modulehtml = $moduletemplate;
$modulehtml = str_replace( '{mainarea}', $mainareahtml, $modulehtml );
