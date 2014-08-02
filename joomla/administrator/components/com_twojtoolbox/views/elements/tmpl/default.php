<?php
/**
* @package     	2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   	Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.2 $
**/


defined('_JEXEC') or die('Restricted Access');

JHtml::_('behavior.tooltip');
JHtml::_('script','system/multiselect.js',false,true);

if(TJTB_JVERSION==3){
	JHtml::_('behavior.multiselect');
	JHtml::_('dropdown.init');
	JHtml::_('formbehavior.chosen', 'select');
}
$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$canOrder	= 1;
$saveOrder	= $listOrder=='ordering';
if( TJTB_JVERSION==3){
	if ($saveOrder){
		$saveOrderingUrl = 'index.php?option=com_twojtoolbox&task=elements.saveOrderAjax&tmpl=component';
		JHtml::_('sortablelist.sortable', 'articleList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
	}
	$sortFields = $this->getSortFields();
}
?><form action="<?php echo JRoute::_('index.php?option=com_twojtoolbox&view=elements'); ?>"method="post" name="adminForm" id="adminForm">
	<div id="j-main-container">
	
		<?php  if(TJTB_JVERSION==2) echo $this->loadTemplate('topbar_v2'); else  echo $this->loadTemplate('topbar_v3');  ?>

		<div class="twoj_clear"> </div>

		<table class="table table-striped adminlist" id="articleList">
			<thead>
				<tr>
					<?php if(TJTB_JVERSION==3){?>
						<th width="15"  class="twoj_nowrap twoj_center  hidden-phone">
							<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
						</th>
					<?php } else { ?>
						<th width="10%">
							<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ORDERING', 'ordering', $listDirn, $listOrder); ?>
							<?php if ($canOrder && $saveOrder): ?>
								<?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'elements.saveorder'); ?>
							<?php endif;?>
						</th>
					<?php } ?>
						
					<th width="15"  class="twoj_center twoj_nowrap">
						<?php if(TJTB_JVERSION==2){?>
							<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
						<?php } else { ?>
							<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
						<?php } ?>
					</th>
					<th class="twoj_nowrap">
						<?php echo JHtml::_('grid.sort',  'COM_TWOJTOOLBOX_HEADING_NAME', 'title', $listDirn, $listOrder); ?>
					</th>
					<th width="5%"  class="twoj_nowrap twoj_center">
						<?php echo JHtml::_('grid.sort', 'JSTATUS', 'state', $listDirn, $listOrder); ?>
					</th>
					<th width="5%"  class="twoj_nowrap twoj_center">
						<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'a.language', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
					</th>
					<th width="20"  class="twoj_center twoj_nowrap hidden-phone">
						<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($this->items as $i => $item): 
				$ordering	= ($listOrder == 'ordering');
				$canCreate	= $user->authorise('core.create',		'com_twojtoolbox');
				$canEdit	= $user->authorise('core.edit',			'com_twojtoolbox');
				$canCheckin	= $user->authorise('core.manage',		'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
				$canChange	= $user->authorise('core.edit.state',	'com_twojtoolbox') && $canCheckin;
				?>
				<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->catid?>" >
					
					<?php if(TJTB_JVERSION==3){?>
						<td class="order nowrap center hidden-phone">
						<?php if ($canChange) :
							$disableClassName = '';
							$disabledLabel	  = '';
							if (!$saveOrder) :
								$disabledLabel    = JText::_('JORDERINGDISABLED');
								$disableClassName = 'inactive tip-top';
							endif; ?>
							<span class="sortable-handler hasTooltip <?php echo $disableClassName?>" title="<?php echo $disabledLabel?>">
								<i class="icon-menu"></i>
							</span>
							<input type="text" style="display:none" name="order[]" size="5"
								value="<?php echo $item->ordering;?>" class="width-20 text-area-order " />
						<?php else : ?>
							<span class="sortable-handler inactive" >
								<i class="icon-menu"></i>
							</span>
						<?php endif; ?>
						</td>
					<?php } else { ?>
						<td class="order">
							<?php if ($canChange) : ?>
								<?php if ($saveOrder) : ?>
									<?php if ($listDirn == 'asc') : ?>
										<span><?php echo $this->pagination->orderUpIcon($i, (@$this->items[$i-1]->catid == $item->catid), 'elements.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
										<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, (@$this->items[$i+1]->catid == $item->catid), 'elements.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
									<?php elseif ($listDirn == 'desc') : ?>
										<span><?php echo $this->pagination->orderUpIcon($i, (@$this->items[$i-1]->catid == $item->catid), 'elements.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
										<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, (@$this->items[$i+1]->catid == $item->catid), 'elements.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
									<?php endif; ?>
								<?php endif; ?>
								<?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
								<input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled;?> class="text-area-order" />
							<?php else : ?>
								<?php echo $item->ordering; ?>
							<?php endif; ?>
						</td>
					<?php } ?>
					
					<td class="twoj_center">
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>
					<td class="twojtoolbox_showpreview">
						<?php if ($item->checked_out) : ?>
							<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'elements.', $canCheckin); ?>
						<?php endif; ?>
						<?php if ($canEdit) : ?>
							<a href="<?php echo JRoute::_('index.php?option=com_twojtoolbox&task=element.edit&id='.(int) $item->id); ?>">
								<?php echo $this->escape($item->title); ?></a>
						<?php else : ?>
							<?php echo $this->escape($item->title); ?>
						<?php endif; ?>
						<div class="twojtoolbox_field_id" style="display:none;"><?php echo $item->id; ?></div>
					</td>
					<td class="twoj_center">
						<?php echo JHtml::_('jgrid.published', $item->state, $i, 'elements.', $canChange, 'cb'); ?>
					</td>
					<td class="twoj_center twoj_nowrap">
						<?php if ($item->language=='*'):?>
							<?php echo JText::alt('JALL','language'); ?>
						<?php else:?>
							<?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
						<?php endif;?>
					</td>
					<td class="twoj_center hidden-phone"><?php echo $item->id; ?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
			<?php if(TJTB_JVERSION==2){ ?>
			<tfoot>
				<tr>
					<td colspan="6"><?php echo $this->pagination->getListFooter(); ?></td>
				</tr>
			</tfoot>
			<?php } ?>
		</table>
		<div class="twoj_clear"> </div>
		<?php if(TJTB_JVERSION==3) echo $this->pagination->getListFooter(); ?>
		<div class="twoj_clear"> </div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_category_id" value="<?php echo TwojToolboxHelper::cgid(); ?>" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
<?php echo TwojToolboxHTMLHelper::getVersion(); ?>
<div id="twojtoolbox_previewbox_divbox" class="twojtoolbox_hiddenblock">
	<div id="twojtoolbox_previewbox_divbox_inner"></div>
</div>