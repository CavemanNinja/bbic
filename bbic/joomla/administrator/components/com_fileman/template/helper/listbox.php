<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanTemplateHelperListbox extends ComKoowaTemplateHelperListbox
{
    public function folders($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'showroot' => true,
            'folder' => null
        ));
    
        $tree = KObjectManager::getInstance()->getObject('com:files.controller.folder')
            ->container('fileman-files')
            ->tree(1)
            ->limit(0)
            ->browse();
        
        $options = array();
        if (!$config->folder && $config->showroot) {
            $options[] = array('label' => $this->getObject('translator')->translate('Root folder'), 'value' => '');
        }
        
        foreach ($tree as $folder) {
            $this->_addFolder($folder, $options, $config);
        }
        
        $config->options = $options;
    
        return $this->optionlist($config);
    }
    
    protected function _addFolder($folder, &$options, $config)
    {
        if (!$config->folder || strpos($folder->path, $config->folder) === 0)
        {
            $padded = str_repeat('&nbsp;', 2*(count(explode('/', $folder->path)))).$folder->name;
            $options[] = array('label' => $padded, 'value' => $folder->path);
        }
        
        if ($folder->hasChildren())
        {
            foreach ($folder->getChildren() as $child) {
                $this->_addFolder($child, $options, $config);
            }
        }
    }
}