<?php
/**
* @package     	2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   	Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.10 $
**/
defined('_JEXEC') or die('Restricted Access');

if( JRequest::getInt('update_complete', 0) ){
	JFactory::getApplication()->enqueueMessage( JText::_('COM_TWOJTOOLBOX_UPDATEPRODUCTSCOMPLETE_TEXT'), 'notice' );
}
JHtml::_('behavior.tooltip');
?>
<form action="<?php echo JRoute::_('index.php?option=com_twojtoolbox&view=plugins');?>" method="post" name="adminForm" id="adminForm">
	<table  class="adminlist table table-striped" id="articleList">
		<thead>
			<tr>
				<th width="10"><?php echo JText::_('COM_TWOJTOOLBOX_HEADING_ID'); ?></th>
				<th width="20" class="center">
					<?php if(TJTB_JVERSION==2){?>
						<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
					<?php } else { ?>
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
					<?php } ?>
				</th>	
				<th><?php echo JText::_('COM_TWOJTOOLBOX_PLUGINS_HEADING_TITLE'); ?></th>	
				<th class="center" width="160"><?php echo JText::_('COM_TWOJTOOLBOX_PLUGINS_HEADING_VERSION_INSTALL'); ?></th>
				<th class="center" width="90"><?php echo JText::_('COM_TWOJTOOLBOX_PLUGINS_HEADING_VERSION_SERVER'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="5"><?php echo $this->pagination->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
			<?php foreach($this->items as $i => $item): ?>
				<tr class="row<?php echo $i % 2; ?>" id="<?php echo $item->type; ?>">
					<td class="twojtoolbox_td_active center">
						<?php echo $item->id; ?>
					</td>
					<?php if($item->install){?>
					<td class="center">
						<?php echo $item->install ? JHtml::_('grid.id', $i, $item->id) : '';?>
					</td>
					<?php } ?>
					<td class="twojtoolbox_td_active has-context" <?php echo (!$item->install?'colspan="2"':''); ?>>
						<?php if( !$item->install ){?>
							<img src="http://2joomla.net/products_info/<?php echo $item->type;?>/small_logo.png"  class="twojtoolbox_pluginslist_image hidden-phone" border="0" title="<?php echo $item->title;; ?>" alt="<?php echo $item->title;; ?>"/>
						<?php } ?>
						<div class="twojtoolbox_pluginslist_title"><?php echo $item->title; ?></div>
						<div class="small hidden-phone"><?php echo $item->desc_small; ?></div>

						<div id="dialog_<?php echo $item->type; ?>" class="twoj_hiddenblock">
								<div class="twojtoolbox_infoplugin_leftpanel">
									
									<img src="http://2joomla.net/products_info/<?php echo $item->type;?>/logo.png" style="width: 100px;" title="<?php echo $item->title; ?>" alt="<?php echo $item->title; ?>"/><br />
									<?php if(!$item->install && $item->price){?>
									<br />
									<div class="twojtoolbox_infoplugin_title"><?php echo JText::_('COM_TWOJTOOLBOX_PRICE'); ?>:</div>
									
									<div align="center">
										<a href="http://www.2joomla.net/products_info/goto.php?content=price&type=<?php echo $item->type ;?>" target="_blank"  title="<?php echo JText::_('COM_TWOJTOOLBOX_PLUGINS_PLUGININFO_PRICETEXT'); ?>" alt="<?php echo JText::_('COM_TWOJTOOLBOX_PLUGINS_PLUGININFO_PRICETEXT'); ?>" >
										<button class="twojtoolbox_plugins_button"><?php echo JText::_( $item->price.' $'); ?></button>
										</a>
									</div>
									<?php } ?>
									<br />
									<div class="twojtoolbox_infoplugin_title"><?php echo JText::_('COM_TWOJTOOLBOX_VERSIONS'); ?>:</div>
									<div align="center" style="margin-top:4px;"></div>
										
										<div class="twojtoolbox_infoplugin_versiontext">
											<div><?php echo JText::_('COM_TWOJTOOLBOX_AVAILABLE'); ?>:</div>
											<?php echo TwojToolboxHelper::perseVersion( $item->v_server);?>
										</div>
									<?php  if ($item->install){ ?>
										<div class="twojtoolbox_infoplugin_versiontext">
											<div><?php echo JText::_('COM_TWOJTOOLBOX_INSTALLED'); ?>:</div>
											<?php echo TwojToolboxHelper::perseVersion( $item->v_install);?>
										</div>
									<?php } ?>
									<?php if($item->status=='update'){?>
										<div align="center">
										<a href="http://www.2joomla.net/products_info/goto.php?content=update&type=<?php echo $item->type ;?>" target="_blank"  title="<?php echo JText::_('COM_TWOJTOOLBOX_PLUGINS_PLUGININFO_UPDATETEXT'); ?>" alt="<?php echo JText::_('COM_TWOJTOOLBOX_PLUGINS_PLUGININFO_UPDATETEXT'); ?>" >
											<button class="btn btn-small" id="twojtoolbox_infoplugin_updatebutton"><?php echo JText::_('COM_TWOJTOOLBOX_UPDATE'); ?></button>
										</a>
										</div>
									<?php } ?>
									<br />
									<div class="twojtoolbox_infoplugin_title"><?php echo JText::_('COM_TWOJTOOLBOX_COMPATIBLE'); ?>:</div>
									<div align="center" style="margin-top:4px;">
										<img src="<?php echo JURI::root();?>components/com_twojtoolbox/css/admin/joomla_30.png" title="<?php echo JText::_('Joomla 3.0'); ?>" alt="<?php echo JText::_('Joomla 3.0'); ?>"/><br />
										<img src="<?php echo JURI::root();?>components/com_twojtoolbox/css/admin/joomla_25.png" title="<?php echo JText::_('Joomla 2.5'); ?>" alt="<?php echo JText::_('Joomla 2.5'); ?>"/><br />
										<img src="<?php echo JURI::root();?>components/com_twojtoolbox/css/admin/joomla_17.png" title="<?php echo JText::_('Joomla 1.7'); ?>" alt="<?php echo JText::_('Joomla 1.7'); ?>"/><br />
										<!--<img src="<?php echo JURI::root();?>components/com_twojtoolbox/css/admin/joomla_16.png" title="<?php echo JText::_('Joomla 1.6'); ?>" alt="<?php echo JText::_('Joomla 1.6'); ?>"/><br />-->
									</div>
								</div>
								<div class="twojtoolbox_infoplugin_rightpanel">
									<div id="twojtoolbox_infoplugin_pluginname_<?php echo $item->type; ?>" class="twojtoolbox_hiddenblock"><?php echo $item->title.' '.JText::_('COM_TWOJTOOLBOX_DETAILS'); ?> </div>
									<br />
									<div class="twojtoolbox_infoplugin_title"><?php echo JText::_('COM_TWOJTOOLBOX_DESC'); ?>:</div>
									<div class="twojtoolbox_infoplugin_desc"><?php echo JText::_($item->desc); ?></div>
									<br />
									<div class="twojtoolbox_infoplugin_title"><?php echo JText::_('COM_TWOJTOOLBOX_SCREENSHOT'); ?>:</div>
									<div align="center">
										<a href="http://www.2joomla.net/products_info/goto.php?content=demo&type=<?php echo $item->type ;?>" target="_blank"  title="<?php echo JText::_('COM_TWOJTOOLBOX_PLUGINS_INFOPLUGIN_DEMOTEXT'); ?>" alt="<?php echo JText::_('COM_TWOJTOOLBOX_PLUGINS_INFOPLUGIN_DEMOTEXT'); ?>" >
											<img src="http://2joomla.net/products_info/<?php echo $item->type ;?>/screenshots/<?php echo $item->type ;?>1.png" border="0" title="<?php echo JText::_('COM_TWOJTOOLBOX_PLUGINS_INFOPLUGIN_DEMOTEXT'); ?>" alt="<?php echo JText::_('COM_TWOJTOOLBOX_PLUGINS_INFOPLUGIN_DEMOTEXT'); ?>"/>
											<img src="http://2joomla.net/products_info/<?php echo $item->type ;?>/screenshots/<?php echo $item->type ;?>2.png" border="0" title="<?php echo JText::_('COM_TWOJTOOLBOX_PLUGINS_INFOPLUGIN_DEMOTEXT'); ?>" alt="<?php echo JText::_('COM_TWOJTOOLBOX_PLUGINS_INFOPLUGIN_DEMOTEXT'); ?>"/>
											<img src="http://2joomla.net/products_info/<?php echo $item->type ;?>/screenshots/<?php echo $item->type ;?>3.png" border="0" title="<?php echo JText::_('COM_TWOJTOOLBOX_PLUGINS_INFOPLUGIN_DEMOTEXT'); ?>" alt="<?php echo JText::_('COM_TWOJTOOLBOX_PLUGINS_INFOPLUGIN_DEMOTEXT'); ?>"/>
											<img src="http://2joomla.net/products_info/<?php echo $item->type ;?>/screenshots/<?php echo $item->type ;?>4.png" border="0" title="<?php echo JText::_('COM_TWOJTOOLBOX_PLUGINS_INFOPLUGIN_DEMOTEXT'); ?>" alt="<?php echo JText::_('COM_TWOJTOOLBOX_PLUGINS_INFOPLUGIN_DEMOTEXT'); ?>"/>
										</a>
									</div>
								</div>
						</div>
					</td>
					<td class="left">
						<?php echo  $item->install ? $item->version_list : '';?>
					</td>
					<td  align="center" class="twojtoolbox_td_active center">
						<span class="<?php echo $item->status=='update'?'version_new':'';?>"><?php echo $item->v_server_p;?></span>
					</td>
					
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHtml::_('form.token'); ?>
</form>

<div id="twojtoolbox_typedialog" class="twojtoolbox_hiddenblock" >
	<form enctype="multipart/form-data"  action="<?php echo JRoute::_('index.php?option=com_twojtoolbox&view=plugins');?>" method="post" name="adminFormPluginUpload" id="adminFormPluginUpload">
		<div id="twojtoolbox_selectfilepanel">
			<?php echo JText::_('COM_TWOJTOOLBOX_PLUGINS_UPLOAD_LABEL'); ?>
			<input type="file" id="twojtoolbox_uploadplugin" name="plugin_file" size="40" class="inputbox" />
		</div>
		<div id="twojtoolbox_loadingpanel"></div>
		<input type="hidden" name="task" value="install" />
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>

<div id="twojtoolbox_typedialog_plugininfo" class="twojtoolbox_hiddenblock" >
	<div id="twojtoolbox_plugininfo_panel"></div>
</div>
	
<div id="twojtoolbox_typedialog_plugindelete" class="twojtoolbox_hiddenblock" >
	<div id="twojtoolbox_plugindelete_panel">
		<br />
		<div class="twojtoolbox_plugindelete_text"><strong><?php echo JText::_('COM_TWOJTOOLBOX_PLUGINDELETE_TEXTBEFORE'); ?></strong></div>
		<br />
		<fieldset id="twojtoolbox_plugindelete_group" class="radio">
			<input type="radio" id="delete_option_1" name="delete_option" value="1" checked />
			<label for="delete_option_1"><?php echo JText::_('COM_TWOJTOOLBOX_PLUGINDELETE_OPTION1'); ?></label>
			<br />
			<input type="radio" id="delete_option_2" name="delete_option" value="2" />
			<label for="delete_option_2"><?php echo JText::_('COM_TWOJTOOLBOX_PLUGINDELETE_OPTION2'); ?></label>
			<br />
			<input type="radio" id="delete_option_3" name="delete_option" value="3" />
			<label for="delete_option_3"><?php echo JText::_('COM_TWOJTOOLBOX_PLUGINDELETE_OPTION3'); ?></label>
		</fieldset>
		<br /><br />
		<div id="twojtoolbox_plugindelete_log">
		</div>
	</div>
</div>

<div id="twojtoolbox_divaupdate" class="twoj_hiddenblock">
	<div id="twojtoolbox_newproduct" class="twojtoolbox_updatecorect"><?php echo JText::_('COM_TWOJTOOLBOX_UPDATEPRODUCTS_TEXT' ); ?></div>
</div>

<div id="twojtoolbox_uploadplugin_loading" class="twoj_hiddenblock">
	<div class="twojtoolbox_uplodinglog"><?php echo JText::_( 'COM_TWOJTOOLBOX_PLUGINS_LOADINGTEXT' ); ?></div> <div class="twojtoolbox_uplodinglog_loadimage"></div>
</div>	

<?php echo TwojToolboxHTMLHelper::getVersion(); ?>