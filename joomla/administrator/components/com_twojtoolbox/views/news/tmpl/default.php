<?php
/**
* @package     	2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   	Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.10 $
**/
defined('_JEXEC') or die('Restricted Access');
JHtml::_('behavior.tooltip');
?>
<form action="<?php echo JRoute::_('index.php?option=com_twojtoolbox&view=news');?>" method="post" name="adminForm" id="adminForm">
	<table  class="adminlist table table-striped" id="articleList">
		<thead>
			<tr>
				<th width="150"><?php echo JText::_('COM_TWOJTOOLBOX_HEADING_DATA'); ?></th>		
				<th><?php echo JText::_('COM_TWOJTOOLBOX_HEADING_TEXT'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="2"><?php echo $this->pagination->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
			<?php foreach($this->items as $i => $item): ?>
				<tr class="row<?php echo $i % 2; ?>">
					<td align="center"><div class="small"><?php echo date('D, d M Y H:i:s', $item->date_in); ?></div></td>
					<td align="left"><div class="twoj_news_message_<?php echo  $item->read==1?'read':'unread';?>"><?php echo JText::_($item->message);?></div></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHtml::_('form.token'); ?>		
</form>
<?php echo TwojToolboxHTMLHelper::getVersion(); ?>
	