<?php
/**
* @package     2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.2 $
**/


defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldOrdering extends JFormField{

	protected $type = 'Ordering';

	protected function getInput(){
		$html = array();
		$attr = '';
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		$attr .= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		$attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';
		$elementId	= (int) $this->form->getValue('id');
		$categoryId	= (int) $this->form->getValue('catid');
		$query = 'SELECT ordering AS value, title AS text' .
				' FROM #__twojtoolbox_elements' .
				' WHERE catid = ' . (int) $categoryId .
				' ORDER BY ordering';
		if ((string) $this->element['readonly'] == 'true') {
			$html[] = JHtml::_('list.ordering', '', $query, trim($attr), $this->value, $elementId ? 0 : 1);
			$html[] = '<input type="hidden" name="'.$this->name.'" value="'.$this->value.'"/>';
		}
		else {
			$html[] = JHtml::_('list.ordering', $this->name, $query, trim($attr), $this->value, $elementId ? 0 : 1);
		}
		return implode($html);
	}
}
