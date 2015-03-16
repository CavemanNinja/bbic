<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanModelEntityExtension extends ComExtmanModelEntityExtension
{
    protected function _createContainer()
    {
        $entity = $this->getObject('com:files.model.containers')->slug('fileman-files')->fetch();

        if ($entity->isNew())
        {
            $thumbnails = true;
            if (!extension_loaded('gd'))
            {
                $thumbnails = false;
                $translator = $this->getObject('translator');
                JFactory::getApplication()->enqueueMessage($translator->translate('Your server does not have the necessary GD image library for thumbnails.'));
            }

            $entity->create(array(
                'slug' => 'fileman-files',
                'path' => 'images',
                'title' => 'FILEman',
                'parameters' => array(
                    'thumbnails' => $thumbnails
                )
            ));
            $entity->save();
        }
    }

	public function save()
	{
		$result = parent::save();

		if ($result)
		{
            $this->_createContainer();
            // Managers should be able to access the component by default just like com_media
            if ($this->event === 'install')
            {
                $rule = '{"core.admin":[],"core.manage":{"6":1},"core.create":[],"core.delete":[],"core.edit":[]}';
                JFactory::getDbo()
                    ->setQuery(sprintf("UPDATE #__assets SET rules = '%s' WHERE name = '%s'", $rule, 'com_fileman'))
                    ->query();
            }

			if ($this->old_version && version_compare($this->old_version, '1.0.0RC4', '<='))
			{
				// Path encoding got removed in 1.0.0RC5
				$entity = KObjectManager::getInstance()->getObject('com:files.model.containers')->slug('fileman-files')->fetch();
				$path = $entity->fullpath;
				$rename = array();
				$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::CHILD_FIRST);
				foreach ($iterator as $f)
				{
					$name = $f->getFilename();
					if ($name === rawurldecode($name)) {
						continue;
					}
				
					$rename[$f->getPathname()] = $f->getPath().'/'.rawurldecode($name);
				}
					
				foreach ($rename as $from => $to) {
					rename($from, $to);
				}
			}
			
			if ($this->old_version && version_compare($this->old_version, '1.0.0RC4', '<='))
			{
				// format=raw was removed from URLs in RC4
				$id = JComponentHelper::getComponent('com_fileman')->id;
				if ($id)
				{
					$table = KObjectManager::getInstance()->getObject('com://admin/docman.database.table.menus', array('name' => 'menu'));
					$items = $table->select(array('component_id' => $id));
						
					foreach ($items as $item) {
						parse_str(str_replace('index.php?', '', $item->link), $query);
							
						if (!isset($query['view']) || $query['view'] !== 'file') {
							continue;
						}
						
						$item->link = str_replace('&format=raw', '', $item->link);
							
						$item->save();
					}
				}
			}

            if ($this->old_version && version_compare($this->old_version, '1.0.0RC5', '<='))
            {
                // cache structure is changed. clean old cache folders
                jimport('joomla.filesystem.folder');

                $folders = JFolder::folders(JPATH_ROOT.'/cache', '^com_fileman');
                foreach ($folders as $folder) {
                    JFolder::delete(JPATH_ROOT.'/cache/'.$folder);
                }

                // thumbnail column is now a mediumtext in com_files
                $query = "ALTER TABLE `#__files_thumbnails` MODIFY `thumbnail` MEDIUMTEXT";
                JFactory::getDBO()->setQuery($query);
                JFactory::getDBO()->query();
            }
		}

		return $result;
	}

	public function delete()
	{
		$result = parent::delete();

		if ($result)
		{
			//Sometimes installer messes up and leaves stuff behind. Remove them too when uninstalling
            $query = sprintf("DELETE FROM #__menu WHERE link = 'index.php?option=com_%s' AND component_id = 0 LIMIT 1", $this->component);
            $db = JFactory::getDbo();
            $db->setQuery($query);
            $db->query();

			$db = JFactory::getDBO();
			$db->setQuery('SHOW TABLES LIKE '.$db->quote($db->replacePrefix('#__files_containers')));
			if ($db->loadResult()) {
				$db->setQuery("DELETE FROM `#__files_containers` WHERE `slug` = 'fileman-files'");
				$db->query();
			}

            JFactory::getCache()->clean('com_fileman');
		}

		return $result;
	}
}