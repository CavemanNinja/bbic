<?php
/**
* @package     	2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   	Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.2 $
**/

defined('_JEXEC') or die;

jimport('joomla.form.formfield');
JFormHelper::loadFieldClass('spacer');

class JFormFieldTwoJSpacer extends JFormFieldSpacer{
	protected $type = 'TwoJSpacer';
	public $borderBottom = 0;
	
	protected function getLabel(){
	
		$html = array();
		$class = $this->element['class'] ? (string) $this->element['class'] : '';

		$html[] = '<span class="spacer">';
		$html[] = '<span class="'.$class.'">';

		$label = '';
		// Get the label text from the XML element, defaulting to the element name.
		$text = $this->element['label'] ? (string) $this->element['label'] : (string) $this->element['name'];
		$text = $this->translateLabel ? JText::_($text) : $text;

		// Build the class for the label.
		$class = !empty($this->description) ? 'hasTip' : '';
		$class = $this->required == true ? $class.' required' : $class;

		// Add the opening label tag and main attributes attributes.
		if( !isset($this->element['long']) ){
			$label .= '<label id="'.$this->id.'-lbl" class="'.$class.'"';
			
			
			// If a description is specified, use it to build a tooltip.
			if (!empty($this->description)) {
				$label .= ' title="'.htmlspecialchars(trim($text, ':').'::' .
							($this->translateDescription ? JText::_($this->description) : $this->description), ENT_COMPAT, 'UTF-8').'"';
			}

			// Add the label text and closing tag.
			$label .= '><strong>'.$text.'</strong></label>';
		} else {
			$this->borderBottom = 1;
			$html[] = '<strong>'.$text.'</strong>';
		}
		$html[] = $label;

		$html[] = '</span>';
		$html[] = '</span>';
		return implode('',$html);
	}

	/**
	 * Method to get the field title.
	 *
	 * @return  string  The field title.
	 * @since   11.1
	 */
	protected function getTitle()
	{
		return $this->getLabel();
	}
}