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
JHtml::_('script','system/multiselect.js',false,true);

if(TJTB_JVERSION==3){
	JHtml::_('behavior.multiselect');
	JHtml::_('dropdown.init');
	JHtml::_('formbehavior.chosen', 'select');
}

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering', 'title'));
$listDirn	= $this->escape($this->state->get('list.direction', 'asc'));
$sad = JFactory::getApplication()->getUserState('com_twojtoolbox.options.sad', 0); 
?>
<form action="<?php echo JRoute::_('index.php?option=com_twojtoolbox&view=plitems'); ?>" method="post" name="adminForm" id="adminForm">
<div id="j-main-container">

	<?php  if(TJTB_JVERSION==2) echo $this->loadTemplate('topbar_v2'); else  echo $this->loadTemplate('topbar_v3');  ?>
		
	<div class="twoj_clear"> </div>
	<table class="table table-striped adminlist" id="articleList">
		<thead>
			<tr>
				<th width="5"><?php echo JText::_('COM_TWOJTOOLBOX_HEADING_ID'); ?></th>
				<th width="20">
					<?php if(TJTB_JVERSION==2){?>
						<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
					<?php } else { ?>
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
					<?php } ?>
				</th>			
				<th><?php echo JHtml::_('grid.sort',  'COM_TWOJTOOLBOX_HEADING_NAME', 'title', $listDirn, $listOrder); ?></th>
				<th width="5%"><?php echo JHtml::_('grid.sort', 'JSTATUS', 'state', $listDirn, $listOrder); ?></th>
				<th width="150"><?php echo JHtml::_('grid.sort', 'COM_TWOJTOOLBOX_ITEMS_HEADING_TYPE', 'category_type', $listDirn, $listOrder); ?></th>
				<th  width="500"><?php echo JText::_('COM_TWOJTOOLBOX_ITEMS_HEADING_ACTION'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($this->items as $i => $item): 
				$ordering	= ($listOrder == 'ordering');
				$item->cat_link = JRoute::_('index.php?option=com_twojtoolbox&view=plugins&opendialog='. $item->type);
				$canEdit	= $user->authorise('core.edit',			'com_twojtoolbox');
				$canCheckin	= $user->authorise('core.manage',		'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
				$canChange	= $user->authorise('core.edit.state',	'com_twojtoolbox') && $canCheckin;
			?>
				<tr class="row<?php echo $i % 2; ?> buttonset_helper_class">
					<td><?php echo $item->id; ?></td>
					<td><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
					<td>
					<?php if ($item->checked_out) : ?>
						<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'plitems.', $canCheckin); ?>
					<?php endif; ?>
					<?php if ($canEdit) : ?>
						<a href="<?php echo JRoute::_('index.php?option=com_twojtoolbox&task=plitem.edit&id='.(int) $item->id); ?>">
							<?php echo $this->escape($item->title); ?></a>
					<?php else : ?>
						<?php echo $this->escape($item->title); ?>
					<?php endif; ?>
					</td>
					<td class="twoj_center">
					<?php echo JHtml::_('jgrid.published', $item->state, $i, 'plitems.', $canChange, 'cb'); ?>
				</td>
					<td >
						<div class="twojtoolbox_typeinfo">
							<?php if( $sad!=-2 ){ ?>
								<a href="<?php echo $item->cat_link;?>">
									<?php echo $item->category_type; ?>
								</a>
							<?php }  else echo $item->category_type; ?>
						</div>
					</td>
					<td>
						<div class="twojtoolbox_status_actionbutton">
							<a href="<?php echo JRoute::_('index.php?option=com_twojtoolbox&task=plitem.edit&id=' . $item->id); ?>">
								<span class="twojtoolbox_buttonset_button_options"></span>
							</a>
						<?php
							if($item->multi){?>
								<a href="<?php echo JRoute::_('index.php?option=com_twojtoolbox&view=elements&catid=' . $item->id); ?>">
									<span class="twojtoolbox_buttonset_button_elements"></span>
								</a>
							<?php 
								if($item->images){?>
									<a href="<?php echo JRoute::_('index.php?option=com_twojtoolbox&view=batchupload&catid=' . $item->id); ?>">
										<span class="twojtoolbox_buttonset_button_upload"></span>
									</a>
									<a href="<?php echo JRoute::_('index.php?option=com_twojtoolbox&view=scan&catid=' . $item->id); ?>">
										<span class="twojtoolbox_buttonset_button_scan"></span>
									</a>
								<?php
								}
							}
						?></span>
					</td>
					
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
	<?php if(TJTB_JVERSION==3) echo $this->pagination->getListFooter(); ?>
	<div class="twoj_clear"></div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</div>
</form>

<div id="twojtoolbox_typedialog"  class="twojtoolbox_hiddenblock">
	<legend><?php echo JText::_( 'COM_TWOJTOOLBOX_PLUGINS_INSTALL_LIST' ); ?></legend>
	<div  id="twojtoolbox_install">
	<?php foreach($this->inst_plugins as $i => $plugin): ?>
		<div class="twojtoolbox_product" id="<?php echo $plugin->type;?>">
			<img src="<?php echo JURI::root().'components/com_twojtoolbox/plugins/'.$plugin->type.'/'.$plugin->v_active;?>/mid_logo.png" alt="<?php echo $plugin->title;?>" title="<?php echo $plugin->title;?>"/>
		</div>
	<?php endforeach; ?>
	</div>
	<div class="twoj_clear"></div>
	<br />
	<?php if($this->need_update) {?><div id="twojtoolbox_newproduct"><?php echo JText::_( 'COM_TWOJTOOLBOX_UPDATEPRODUCTS_TEXT' ); ?></div><?php } ?>
	<?php if( count($this->new_plugins) && $this->optionSad ){ ?>
		<legend><?php echo JText::_( 'COM_TWOJTOOLBOX_PLUGINS_ALL' ); ?></legend>
		<div id="twojtoolbox_demo">
		<?php foreach($this->new_plugins as $i => $plugin): ?>
			<div class="twojtoolbox_product" id="<?php echo $plugin->type;?>">
				<img src="http://www.2joomla.net/products_info/<?php echo $plugin->type;?>/mid_logo.png" alt="<?php echo $plugin->title;?>" title="<?php echo $plugin->title;?>"/>
			</div>
		<?php endforeach; ?>
		</div>
	<?php } ?> 
	<div class="twojtoolbox_hiddenblock">
		<input type="hidden" name="twojtoolbox_typefield" id="twojtoolbox_typefield" value="" />
	</div>
</div>
<div class="twoj_clear"> </div>
<?php echo TwojToolboxHTMLHelper::getVersion(); ?>

