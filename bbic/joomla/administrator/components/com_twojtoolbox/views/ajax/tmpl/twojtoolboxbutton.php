<?php
/**
* @package     	2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   	Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.2 $
**/

defined('_JEXEC') or die;
?>
		<form>
		<div align="left"><strong><?php echo JText::_( 'COM_TWOJTOOLBOX_BUTTONS_HELP_BEFORE' ); ?></strong></div>
		<br />
		<table width="100%" align="center">
			<tr>
				<td class="key" align="left"  width="40%">
					<label for="twojtoolbox_select_tag">
						<?php echo JText::_( 'COM_TWOJTOOLBOX_BUTTONS_SELECTTAG' ); ?>
					</label>
				</td>
				<td>
					<select id="twojtoolbox_select_tag" name="twojtoolbox_select_tag">
						<option value="" selected="selected"><?php echo JText::_( 'COM_TWOJTOOLBOX_BUTTONS_SELECTTYPETAG' ); ?></option>
						<option value="0"><?php echo JText::_( 'COM_TWOJTOOLBOX_BUTTONS_SINGLETAG' ); ?></option>
						<option value="1"><?php echo JText::_( 'COM_TWOJTOOLBOX_BUTTONS_MULTITAG' ); ?></option>
					</select>
				</td>
			</tr>
			<tr id="twojtoolbox_tr_select_type" style="display: none;">
				<td class="key" align="left"  width="40%">
					<label for="twojtoolbox_select_type">
						<?php echo JText::_( 'COM_TWOJTOOLBOX_BUTTONS_SELECTTYPE' ); ?>
					</label>
				</td>
				<td>
					<div class="twojtoolbox_loading_small" style="float: left;"></div>
				</td>
			</tr>
			
			<tr id="twojtoolbox_tr_select_item" style="display: none;">
				<td class="key" align="left"  width="40%">
					<label for="twojtoolbox_select_item">
						<?php echo JText::_( 'COM_TWOJTOOLBOX_BUTTONS_SELECTITEM' ); ?>
					</label>
				</td>
				<td>
					<div class="twojtoolbox_loading_small" style="float: left;"></div>
				</td>
			</tr>

		</table>
		</form>
		<br />
		<button  style="display: none;" id="twojtoolbox_insertbutton"><?php echo JText::_( 'COM_TWOJTOOLBOX_TWOJTOOLBOXBUTTON_INSERT_BUTTON' ); ?></button>
		<br />	<br />	<br />
		<div align="center"><strong><?php echo JText::_( 'COM_TWOJTOOLBOX_BUTTONS_HELP' ); ?></strong></div>