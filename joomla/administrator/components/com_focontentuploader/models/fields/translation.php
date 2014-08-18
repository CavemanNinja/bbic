<?php
/**
 * Copyright (C) 2014 freakedout (www.freakedout.de)
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
**/

// no direct access
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.language.helper');

class JElementTranslation extends JElement
{
    var	$_name = 'Language';

    function fetchElement($name, $value, &$node, $control_name)
    {

	$client = 'site';
	$paramsLang = JComponentHelper::getParams('com_languages');
	$current = $paramsLang->get($client, 'en-GB');
	$options = JLanguageHelper::createLanguageList('', constant('JPATH_'.strtoupper($client)), true);
	foreach($options as $k => $v)
	{
	    if ($v['value'] == $current) {
		unset($options[$k]);
	    }
	}
	// Base name of the HTML control.
	$ctrl  = $control_name .'['. $name .']';
	$attribs	    = 'onchange="addTranslationIds();"';
	if ($v = $node->attributes( 'size' )) {
	    $attribs       .= 'size="'.$v.'"';
	}
	if ($v = $node->attributes( 'class' )) {
	    $attribs       .= 'class="'.$v.'"';
	} else {
	    $attribs       .= 'class="inputbox"';
	}
	if ($m = $node->attributes( 'multiple' ))
	{
	    $attribs       .= ' multiple="multiple"';
	    $ctrl          .= '[]';
	}
	// Render the HTML SELECT list.
	return JHTML::_('select.genericlist', $options, $ctrl, $attribs, 'value', 'text', $value, $control_name.$name );
    }
}