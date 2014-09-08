<?php
/**
 * @package     EXTman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

defined('_JEXEC') or die;

// Need to do this as Joomla 2.5 "protects" parent and manifest properties of the installer
global $installer_manifest, $installer_source, $installer_instance;
$installer_manifest = simplexml_load_file($this->parent->getPath('manifest'));
$installer_source   = $this->parent->getPath('source');
$installer_instance = $this->parent;

require_once dirname(__FILE__).'/helper.php';

class com_extmanInstallerScript
{
    protected static $_files_to_delete = array(
        'administrator/components/com_extman/controllers/dashboard.php',
        'administrator/components/com_extman/controllers/toolbars/dashboard.php',
        'libraries/koowa/filter/tidy.php',
        'libraries/koowa/template/stack.php',
        'libraries/koowa/template/stream.php',
        'media/lib_koowa/js/autocomplete.js',
        'administrator/components/com_extman/install/.subscription'
    );

    protected static $_folders_to_delete = array(
        'administrator/components/com_extman/views/dashboard',
        'media/com_extman/css'
    );

	/**
	 * Name of the component
	 */
	public $component;

	public function __construct($installer)
	{
		global $installer_manifest, $installer_source, $installer_instance;
		// Need to do this as Joomla 2.5 "protects" parent and manifest properties of the installer
		$source   = $installer_source;
		$manifest = $installer_manifest;

		$class = get_class($this);
		preg_match('#^com_([a-z0-9_]+)#', get_class($this), $matches);
		$this->component = $matches[1];

		$this->helper = new ComExtmanInstallerHelper();

		$this->helper->installer = $installer_instance;
		$this->helper->manifest = $manifest;
	}
	
	protected function _fixJoomlaInstallBugs()
	{
	    $db = JFactory::getDbo();
	    
	    // Delete leftover entries from #__assets, #__extensions and #__menu
	    $queries = array();
	    $queries[] = "DELETE FROM #__assets WHERE name = '%s'";
	    $queries[] = "DELETE FROM #__extensions WHERE element = '%s'";
	    $queries[] = "DELETE FROM #__menu 
	        WHERE type = 'component' AND menutype = 'main'
	        AND link LIKE 'index.php?option=%s%%'";
	    
	    foreach ($queries as $query) {
	        $db->setQuery(sprintf($query, 'com_'.$this->component))
	           ->query();
	    }
	}
	
	protected function _fixJoomlaUpdateBugs()
	{
	    $db = JFactory::getDbo();
	    $component = 'com_'.$this->component;
	    
	    // Delete excess entries from #__extensions
	    $query = "SELECT extension_id FROM #__extensions WHERE element = '%s' ORDER BY extension_id ASC";
	    $ids = $db->setQuery(sprintf($query, $component))->loadColumn();

	    if (count($ids) > 1) {
	        $query = sprintf("DELETE FROM #__extensions WHERE element = '%s' AND extension_id <> %d", $component, $ids[0]);
	        $db->setQuery($query)->query();
	    }
	    
	    // Delete excess entries from #__assets
	    $query = "SELECT id FROM #__assets WHERE name = '%s' ORDER BY id ASC LIMIT 1";
	    $ids = $db->setQuery(sprintf($query, $component))->loadColumn();
	    
	    if (count($ids) > 1) {
	        $query = sprintf("DELETE FROM #__assets WHERE name = '%s' AND id <> %d", $component, $ids[0]);
	        $db->setQuery($query)->query();
	    }
	    
	    // Delete entries from #__menu to be sure
	    $query = "DELETE FROM #__menu 
	        WHERE type = 'component' AND menutype = 'main'
	        AND link LIKE 'index.php?option=%s%%'";
	    $query = sprintf($query, $component);

	    $db->setQuery($query)->query();
	}

	public function preflight($type, $installer)
	{
	    if (version_compare(JVERSION, '1.6', '>='))
	    {
	        if (in_array($type, array('install', 'discover_install'))) {
	            $this->_fixJoomlaInstallBugs();
	        } else {
	            $this->_fixJoomlaUpdateBugs();
	        }
	    }

		if ($errors = $this->helper->getServerErrors())
		{
			ob_start();
			echo JText::_("The installation can't proceed until you resolve the following: ");
			echo implode(',', $errors);

			$error = ob_get_clean();
			JFactory::getApplication()->enqueueMessage($error, 'error');

			// J1.5 does not remove menu items on unsuccessful installs
			if (version_compare(JVERSION, '1.6', '<'))
			{
				$db = JFactory::getDBO();
				$db->setQuery("DELETE FROM #__components WHERE `option` = 'com_extman'");
				$db->query();
			}

			return false;
		}
	}

	public function postflight($type, $installer)
	{
		/*
		 * Temporary fix: When upgrading from RC3 to RC4 we added a constant in KHttpUrl 
		 * and use it in KFilterInternalurl. The problem is we load KHttpUrl on upgrade before 
		 * overwriting the files. So KFilterInternalurl tries to reach a non-existent constant
		 * which is a fatal error. Hence this:
		 */
		if (class_exists('Koowa')) {
			KService::get('koowa:filter.internalurl');
		}

        $params     = JComponentHelper::getParams('com_extman');
        $old_uuid   = $params->get('joomlatools_user_id');
        $uuid       = $this->helper->getUUID();
        $user_id_saved = false;

        if ($uuid !== false && $old_uuid != $uuid)
        {
            $user_id_saved = $this->helper->storeUUID($uuid);

            // If we didn't store the user, we don't want to keep track of him either in this run
            if(!$user_id_saved) {
                $uuid = null;
            }
        }

		$this->helper->installFramework();
		$this->helper->installExtensions();
		
		// Rename manifest.xml to koowa.xml if possible
		$folder = JPATH_ROOT.'/plugins/system/koowa';
		if (file_exists($folder.'/manifest.xml')) {
            jimport('joomla.filesystem.file');

			JFile::move($folder.'/manifest.xml', $folder.'/koowa.xml');
		}

		if (version_compare(JVERSION, '1.6', '<'))
		{
            // Hide component in the menu manager in Joomla 1.5
			$db = JFactory::getDBO();
			$db->setQuery("UPDATE #__components SET link = '' WHERE link = 'option=com_extman'");
			$db->query();

            // Rename System - EXTman to System - Joomlatools Framework if possible
            $db->setQuery("
                UPDATE #__plugins SET name = 'System - Joomlatools Framework'
                WHERE element = 'koowa' AND folder = 'system'
            ");

            $db->query();
		}

		if ($this->helper->bootFramework())
		{
		    KService::setAlias('translator', 'com:default.translator');

            $controller = KService::get('com://admin/extman.controller.extension', array(
                'request' => array(
                    'view'   => 'extension',
                    'layout' => 'success',
                    'event'  => $type === 'update' ? 'update' : 'install'
                )
            ));

			$extension = $controller->read();
			$extension->name = 'EXTman';
            $extension->joomlatools_user_id = $uuid;
            $extension->user_id_saved = $user_id_saved;
			$extension->version = (string)$this->helper->manifest->version;

			echo $controller->display();
		}

        jimport('joomla.filesystem.file');
        jimport('joomla.filesystem.folder');

        foreach (self::$_files_to_delete as $file)
        {
            $path = JPATH_ROOT.'/'.(string)$file;

            if (file_exists($path)) {
                JFile::delete($path);
            }
        }

        foreach (self::$_folders_to_delete as $folder)
        {
            $path = JPATH_ROOT.'/'.(string)$folder;

            if (file_exists($path)) {
                JFolder::delete($path);
            }
        }

        /*
         * When releasing RC5 we removed the wrong column accidentally
         * which broke uninstall functionality
         *
         * This is here to make sure we fix those install on the fly by adding the missing column
         */
        $db = JFactory::getDbo();
        $db->setQuery('SHOW TABLES LIKE '.$db->quote($db->replacePrefix('#__extman_extensions')));
        if ($db->loadResult())
        {
            $db->setQuery("SHOW COLUMNS FROM " . $db->replacePrefix('#__extman_extensions'));
            $columns = $db->loadObjectList();

            $fields = array();
            foreach($columns as $column)  {
                $fields[$column->Field] = $column;
            }

            if (!isset($fields['joomla_extension_id']))
            {
                $query = "ALTER TABLE `#__extman_extensions` ADD COLUMN `joomla_extension_id` int(11) unsigned NOT NULL DEFAULT '0' AFTER `identifier`";
                $db->setQuery($query);
                $db->query();
            }

            if (isset($fields['joomlatools_user_id']))
            {
                $query = "ALTER TABLE `#__extman_extensions` DROP COLUMN `joomlatools_user_id`";
                $db->setQuery($query);
                $db->query();
            }
        }
	}

	public function uninstall($installer)
	{
		// Pre-cache uninstall tracking code since we are gonna get rid of the framework
		$track = '';
		if (class_exists('Koowa'))
		{
            $params = JComponentHelper::getParams('com_extman');
            $uuid   = $params->get('joomlatools_user_id');

            $controller = KService::get('com://admin/extman.controller.extension', array(
                'request' => array(
                    'view'   => 'extension',
                    'layout' => 'uninstall',
                    'event'  => 'uninstall'
                )
            ));

			$extension = $controller->read();
			$extension->name = 'EXTman';
            $extension->joomlatools_user_id = $uuid;
			$extension->version = (string)$this->helper->manifest->version;

			$track = $controller->display();
		}

		$db = JFactory::getDBO();
		$db->setQuery("SELECT name FROM #__extman_extensions WHERE parent_id = 0 AND identifier <> 'com:extman'");
		$results = version_compare(JVERSION, '3.0', 'ge') ? $db->loadColumn() : $db->loadResultArray();

		if (count($results))
		{
			$extension = count($results)  == 1 ? sprintf('the <strong>%s</strong> extension by Joomlatools installed', $results[0]) : sprintf('%d Joomlatools extensions installed', count($results));
			JFactory::getApplication()->enqueueMessage(sprintf(
				"You have $extension. EXTman is needed for Joomlatools extensions to work properly. These extensions will not work until you re-install EXTman. EXTman database tables are not deleted to make sure your site still works if you install it again.",
				JRoute::_('index.php?option=com_extman')), 'error');
		} 
		else 
		{
			$tables = array('#__extman_extensions', '#__extman_dependencies');
			foreach ($tables as $table)
			{
				$db->setQuery('DROP TABLE IF EXISTS '.$db->replacePrefix($table));
				$db->query();
			}
		}

		$this->helper->uninstallExtensions();
		$this->helper->uninstallFramework();

		echo $track;
	}
}