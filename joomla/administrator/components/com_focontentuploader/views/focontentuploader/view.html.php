<?php
/**
* Copyright (C) 2014 freakedout (www.freakedout.de)
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
**/

// no direct access
defined('_JEXEC') or die('Restricted access');

class foContentUploaderViewfoContentUploader extends cuView {

    protected $form;

    public function display ($tpl = NULL) {
        $lang = JFactory::getLanguage();
        $lang->load('com_content', JPATH_ADMINISTRATOR);
        $lang->load('com_config', JPATH_ADMINISTRATOR);

        JToolBarHelper::title(JText::_('CU_UPLOAD_ARTICLES'), 'generic.png');
        JToolBarHelper::custom('startupload', 'apply', 'apply', 'CU_START_UPLOAD', false, false);
        JToolBarHelper::custom('saveconfig', 'save', 'save', 'CU_SAVE_CONFIGURATION', false);
        JToolBarHelper::spacer();

        $db = JFactory::getDBO();
        $helper = new foContentUploaderHelper();
        $lists = array();

        $query = $db->getQuery(true);
        $query->select($db->qn('params'))
            ->from('#__focontentuploader')
            ->where($db->qn('component') . ' = ' . $db->q('content'))
            ->where($db->qn('active') . ' = ' . $db->q(1));
        $db->setQuery($query);
        $row = $db->loadResult();
        $params = new JRegistry();
        $params->loadString($row);

        $this->form = JForm::getInstance('cuCoreParams', JPATH_COMPONENT_ADMINISTRATOR . '/models/forms/article.xml');
        $this->form->addFieldPath(JPATH_COMPONENT_ADMINISTRATOR . '/models/fields/');
        $this->form->bind($params);

        $id = $helper->getCurrentConfigID();
        $config = $helper->getConfig($id);
        $lists['configid'] = $helper->getConfigs($id);

        if (isset($config['fulltext']) && JString::strlen($config['fulltext']) > 1) {
            $config['introtext'] = $config['introtext'] . '<hr id="system-readmore" />' . $config['fulltext'];
        }

        $lists['removetags'] = JHTML::_('select.booleanlist', 'removetags', 'class="inputbox"', $config['removetags']);

        $state[] = JHTML::_('select.option', '0', JText::_('JNO'));
        $state[] = JHTML::_('select.option', '1', JText::_('JYES'));
        $lists['catstate'] = JHTML::_('select.radiolist', $state, 'catstate', 'class="inputbox"', 'value', 'text', $config['catstate'] );
        $lists['state'] = JHTML::_('select.radiolist', $state, 'state', 'class="inputbox" onchange="enableField(\'state\',\'state_col\');" ', 'value', 'text', @$config['state'] );
        $lists['featured'] = JHTML::_('select.radiolist', $state, 'featured', 'class="inputbox" onchange="enableField(\'frontpage\',\'frontpage_col\');" ', 'value', 'text', @$config['featured'] );

        if (version_compare(JVERSION, '3.1') >= 0) {
            $languages = JLanguageHelper::getLanguages('lang_code');
            $this->assignRef('languages', $languages);
        }

        $this->assignRef('config', $config);
        $this->assignRef('params', $params);
        $this->assignRef('storedconfigs', $storedconfigs);
        $this->assignRef('lists', $lists);

        parent::display($tpl);
    }
}
