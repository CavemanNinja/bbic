<?php
/**
 * Element: Modules
 * Displays an article id field with a button
 *
 * @package         NoNumber Framework
 * @version         15.3.4
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once JPATH_PLUGINS . '/system/nnframework/helpers/field.php';

class JFormFieldNN_Modules extends nnFormField
{
	public $type = 'Modules';

	protected function getInput()
	{
		$this->params = $this->element->attributes();

		JHtml::_('behavior.modal', 'a.modal');

		$size = ($this->get('size') ? 'style="width:' . $this->get('size') . 'px"' : '');

		$multiple = $this->get('multiple');
		$showtype = $this->get('showtype');
		$showid = $this->get('showid');
		$showinput = $this->get('showinput');

		// load the list of modules
		$query = $this->db->getQuery(true)
			->select('m.id, m.title, m.position, m.module, m.published, m.language')
			->from('#__modules AS m')
			->where('m.client_id = 0')
			->where('m.published > -2')
			->order('m.position, m.title, m.ordering, m.id');
		$this->db->setQuery($query);
		$modules = $this->db->loadObjectList();

		// assemble menu items to the array
		$options = array();

		$p = 0;
		foreach ($modules as $item)
		{
			if ($p !== $item->position)
			{
				$pos = $item->position;
				if ($pos == '')
				{
					$pos = ':: ' . JText::_('JNONE') . ' ::';
				}
				$options[] = JHtml::_('select.option', '-', '[ ' . $pos . ' ]', 'value', 'text', true);
			}
			$p = $item->position;

			$item->title = $item->title;
			if ($showtype)
			{
				$item->title .= ' [' . $item->module . ']';
			}
			if ($showinput || $showid)
			{
				$item->title .= ' [' . $item->id . ']';
			}
			if ($item->language && $item->language != '*')
			{
				$item->title .= ' (' . $item->language . ')';
			}
			$item->title = nnText::prepareSelectItem($item->title, $item->published);

			$options[] = JHtml::_('select.option', $item->id, $item->title);
		}

		if ($showinput)
		{
			array_unshift($options, JHtml::_('select.option', '-', '&nbsp;', 'value', 'text', true));
			array_unshift($options, JHtml::_('select.option', '-', '- ' . JText::_('Select Item') . ' -'));

			if ($multiple)
			{
				$onchange = 'if ( this.value ) { if ( ' . $this->id . '.value ) { ' . $this->id . '.value+=\',\'; } ' . $this->id . '.value+=this.value; } this.value=\'\';';
			}
			else
			{
				$onchange = 'if ( this.value ) { ' . $this->id . '.value=this.value;' . $this->id . '_text.value=this.options[this.selectedIndex].innerHTML.replace( /^((&|&amp;|&#160;)nbsp;|-)*/gm, \'\' ).trim(); } this.value=\'\';';
			}
			$attribs = 'class="inputbox" onchange="' . $onchange . '"';

			$html = '<table cellpadding="0" cellspacing="0"><tr><td style="padding: 0px;">' . "\n";
			if (!$multiple)
			{
				$val_name = $this->value;
				if ($this->value)
				{
					foreach ($modules as $item)
					{
						if ($item->id == $this->value)
						{
							$val_name = $item->title;
							if ($showtype)
							{
								$val_name .= ' [' . $item->module . ']';
							}
							$val_name .= ' [' . $this->value . ']';
							break;
						}
					}
				}
				$html .= '<input type="text" id="' . $this->id . '_text" value="' . $val_name . '" class="inputbox" ' . $size . ' disabled="disabled" />';
				$html .= '<input type="hidden" name="' . $this->name . '" id="' . $this->id . '" value="' . $this->value . '" />';
			}
			else
			{
				$html .= '<input type="text" name="' . $this->name . '" id="' . $this->id . '" value="' . $this->value . '" class="inputbox" ' . $size . ' />';
			}
			$html .= '</td><td style="padding: 0px;"padding-left: 5px;>' . "\n";
			$html .= JHtml::_('select.genericlist', $options, '', $attribs, 'value', 'text', '', '');
			$html .= '</td></tr></table>' . "\n";
		}
		else
		{
			$attr = $size;
			$attr .= $multiple ? ' multiple="multiple"' : '';
			$attr .= ' class="input-xxlarge"';

			$html = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
			$html = '<div class="input-maximize">' . $html . '</div>';
		}

		return preg_replace('#>\[\[\:(.*?)\:\]\]#si', ' style="\1">', $html);
	}
}
