<?php
/**
 * FW Gallery Frontend Manager 3.0
 * @copyright (C) 2014 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/
defined( '_JEXEC' ) or die( 'Restricted access' );

JHTML :: _('behavior.framework', true);
?>
<div id="fwg-fm-wrapper" class="tabbable">
	<ul class="nav nav-tabs">
<?php
if ($this->params->get('allow_frontend_galleries_management')) {
?>
		<li id="fwg-fm-galleries-tab"<?php if ($this->type != 'image') { ?> class="active"<?php } ?>><a href="<?php echo JRoute :: _('index.php?option=com_fwgallery&view=frontendmanager') ?>"><?php echo JText :: _('Galleries') ?></a></li>
<?php
}
?>
		<li id="fwg-fm-images-tab"<?php if ($this->type == 'image') { ?> class="active"<?php } ?>><a href="<?php echo JRoute :: _('index.php?option=com_fwgallery&view=frontendmanager&layout=default_image') ?>"><?php echo JText :: _('Images') ?></a></li>
	</ul>
	<div id="fwg-fm-body-wrapper">
		<div id="fwg-fm-body">
<?php
$this->_layout = $this->layout.'_'.$this->type;
echo $this->loadTemplate();
?>
		</div>
	</div>
</div>