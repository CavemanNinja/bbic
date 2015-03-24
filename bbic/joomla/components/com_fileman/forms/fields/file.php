<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

defined('_JEXEC') or die;

class JFormFieldFile extends JFormField
{
	protected function getInput()
	{
		$value = $this->value;
		$name = $this->name;

        JHtml::_('behavior.modal');

        $html = KObjectManager::getInstance()->getObject('com://admin/fileman.template.helper.modal')->select(array(
            'name'  => $name,
            'id'    => 'fileman-file-link-name',
            'value' => $value,
            'link'  => JRoute::_('index.php?option=com_fileman&view=files&layout=select&tmpl=koowa')
        ));

        return $html;
	}
}