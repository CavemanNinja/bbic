<?php
/**
* @package     	2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   	Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.10 $
**/
defined('_JEXEC') or die('Restricted Access');
?>
<div id="filter-bar" class="btn-toolbar">
	<div class="filter-search btn-group twoj_float_left">
		<label for="filter_search" class="element-invisible"><?php echo JText::_('COM_TWOJTOOLBOX_SEARCH_IN_TITLE');?></label>
		<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('COM_TWOJTOOLBOX_SEARCH_IN_TITLE'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_TWOJTOOLBOX_SEARCH_IN_TITLE'); ?>" />
	</div>
	<div class="btn-group twoj_float_left">
		<button type="submit" class="btn hasTooltip" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
		<button type="button" class="btn hasTooltip" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
	</div>
	<div class="btn-group twoj_float_right hidden-phone">
		<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
		<?php echo $this->pagination->getLimitBox(); ?>
	</div>
	<div class="btn-group twoj_float_right">
		<select name="filter_state" class="inputbox" onchange="this.form.submit()">
			<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
			<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array('archived'=>0, 'trash'=>0)), 'value', 'text', $this->state->get('filter.state'), true);?>
		</select>
		<select name="filter_language" class="inputbox" onchange="this.form.submit()">
			<option value=""><?php echo JText::_('JOPTION_SELECT_LANGUAGE');?></option>
			<?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'));?>
		</select>
	</div>
</div>