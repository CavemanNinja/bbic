<?php
/**
 * FW Gallery 3.1.0
 * @copyright (C) 2014 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

class fwGalleryViewConfig extends JViewLegacy {
    function display($tmpl=null) {
        $model = $this->getModel();
		$this->getMenu();
        $this->assign('obj', $model->loadObj());
        parent::display($tmpl);
    }
    function edit($tmpl=null) {
        $model = $this->getModel();
		$this->getMenu();
        $this->assign('obj', $model->loadObj());
        $this->columns = array(
        	JHTML :: _('select.option', '1', '1', 'id', 'name'),
        	JHTML :: _('select.option', '2', '2', 'id', 'name'),
        	JHTML :: _('select.option', '3', '3', 'id', 'name'),
        	JHTML :: _('select.option', '4', '4', 'id', 'name'),
        	JHTML :: _('select.option', '6', '6', 'id', 'name'),
        	JHTML :: _('select.option', '12', '12', 'id', 'name')
        );
        parent::display($tmpl);
    }
    function getMenu() {
		JSubMenuHelper::addEntry(
			JText::_('FWG_GALLERIES'),
			'index.php?option=com_fwgallery&view=fwgallery',
			false
		);
		JSubMenuHelper::addEntry(
			JText::_('FWG_IMAGES'),
			'index.php?option=com_fwgallery&view=files',
			false
		);
		JSubMenuHelper::addEntry(
			JText::_('FWG_PLUGINS'),
			'index.php?option=com_fwgallery&view=plugins',
			false
		);
		JSubMenuHelper::addEntry(
			JText::_('FWG_TEMPLATES'),
			'index.php?option=com_fwgallery&view=templates',
			false
		);
		JSubMenuHelper::addEntry(
			JText::_('FWG_LANGUAGE'),
			'index.php?option=com_fwgallery&view=language',
			false
		);
		JSubMenuHelper::addEntry(
			JText::_('FWG_CONFIGURATION'),
			'index.php?option=com_fwgallery&view=config',
			true
		);
		JSubMenuHelper::addEntry(
			JText::_('FWG_IPHONE_APP'),
			'index.php?option=com_fwgallery&view=iphone',
			false
		);
    }
}
