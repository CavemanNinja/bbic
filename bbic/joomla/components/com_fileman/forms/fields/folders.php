<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

defined('_JEXEC') or die;

class JFormFieldFolders extends JFormField
{
	protected $type = 'Folders';

	protected function getInput()
	{
        $manager = KObjectManager::getInstance();
		$value   = $this->value;
		$name    = $this->name;

        $show_root  = (bool) $this->element['show_root'];
        $url_encode = (bool) $this->element['url_encode'];

        $tree = $manager->getObject('com:files.controller.folder')
            ->container('fileman-files')
            ->tree(1)
            ->limit(0)
            ->browse();

        $options = array();

        if ($show_root) {
            $options[] = array('label' => JText::_('ROOT_FOLDER'), 'value' => '');
        }

        foreach ($tree as $folder) {
            $this->_addFolder($folder, $options, $url_encode);
        }

        $selected = htmlspecialchars(($url_encode ? urlencode($value) : $value), ENT_QUOTES);

        return $manager->getObject('com://admin/fileman.template.helper.select')->optionlist(array(
            'name' => $name,
            'options' => $options,
            'showroot' => false,
            'selected' => $selected
        ));
	}

    protected function _addFolder($folder, &$options, $url_encode = false)
    {
        $padded    = str_repeat('&nbsp;', 2*(count(explode('/', $folder->path)))).$folder->name;
        $path      = htmlspecialchars($url_encode ? urlencode($folder->path) : $folder->path, ENT_QUOTES);
        $options[] = array('label' => $padded, 'value' => $path);

        if ($folder->hasChildren())
        {
            foreach ($folder->getChildren() as $child) {
                $this->_addFolder($child, $options, $url_encode);
            }
        }

    }
}