<?php
/**
 * @package     EXTman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComExtmanInstallerHelper
{
	public function getVersion()
	{
		return version_compare(JVERSION, '1.6', '<') ? '1.5' : '2.5';
	}

	public function getServerErrors()
	{
		$errors = array();

		if(!class_exists('mysqli')) {
		    $errors[] = JText::_("We're sorry but your server isn't configured with the MySQLi database driver. Please contact your host and ask them to enable MySQLi for your server.");
		}

		if(version_compare(phpversion(), '5.2', '<')) {
		    $errors[] = sprintf(JText::_("EXTman requires PHP 5.2 or later. Your server is running PHP %s."), phpversion());
		}

		if(version_compare(JFactory::getDBO()->getVersion(), '5.0.41', '<')) {
		    $errors[] = sprintf(JText::_("EXTman requires MySQL 5.0.41 or later. Your server is running MySQL %s."), JFactory::getDBO()->getVersion());
		}

		if (class_exists('Koowa') && (!method_exists('Koowa', 'getInstance') || version_compare(Koowa::getInstance()->getVersion(), '12', '<'))) {
			$errors[] = sprintf(JText::_("Your site has an older version of our library already installed. Installation is aborting to prevent creating conflicts with other extensions."));
		}

		//Some hosts that specialize on Joomla are known to lock permissions to the libraries folder
		if(!is_writable(JPATH_LIBRARIES)) {
		    $errors[] = sprintf(JText::_("The <em title=\"%s\">libraries</em> folder needs to be writable in order for EXTman to install correctly."), JPATH_LIBRARIES);
		}

		if (count($errors) === 0 && $this->checkDatabaseType() === false)
		{
			$string   = $this->getVersion() === '1.5' ? 'mysqli' : 'MySQLi';
			$link     = JRoute::_('index.php?option=com_config');
			$errors[] = "In order to use Joomlatools extensions, your database type in Global Configuration should be set to <strong>$string</strong>. Please go to <a href=\"$link\">Global Configuration</a> and in the 'Server' tab change your Database Type to <strong>$string</strong>.";
		}

		return $errors;
	}

	public function checkDatabaseType()
	{
		$result = true;
		if(JFactory::getApplication()->getCfg('dbtype') === 'mysql')
		{
			$result = $this->setDatabaseType();
			if ($result) {
				JFactory::getApplication()->enqueueMessage("Your database type has been converted to 'mysqli'.");
			}
		}

		return $result;
	}

	public function setDatabaseType()
	{
	    $path = JPATH_CONFIGURATION.'/configuration.php';
	    $result = false;

		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		$ftp = JClientHelper::getCredentials('ftp');

	    jimport('joomla.filesystem.path');
		if ($ftp['enabled'] || (JPath::isOwner($path) && JPath::setPermissions($path, '0644'))) {
	    	$search     = JFile::read($path);
	        $replaced   = str_replace('$dbtype = \'mysql\';', '$dbtype = \'mysqli\';', $search);
	        $result 	= JFile::write($path, $replaced);

	        if (!$ftp['enabled'] && JPath::isOwner($path)) {
	        	JPath::setPermissions($path, '0444');
	        }
	    }

	    return $result;
	}

	public function installFramework()
	{
		$source    = $this->installer->getPath('source').'/framework';
		$framework = $this->manifest->framework;

		foreach ($framework->folder as $folder)
		{
		    $from = isset($folder['src']) ? $folder['src'] : $folder;
		    $folder = str_replace('site/', '', (string)$folder);
		    JFolder::copy($source.$from, JPATH_ROOT.$folder, false, true);
		}

		foreach ($framework->file as $file)
		{
		    $folder = JPATH_ROOT.dirname($file);
		    if(!JFolder::exists($folder)) {
		    	JFolder::create($folder);
		    }

		    JFile::copy($source.$file, JPATH_ROOT.$file);
		}

		if ($this->getVersion() === '1.5')
		{
			$id = $this->getExtensionId(array(
				'type' => 'plugin',
				'element' => 'mtupgrade',
				'folder' => 'system',
			));

			$db = JFactory::getDBO();
			$db->setQuery('SELECT published FROM #__plugins WHERE id = '.(int) $id);
			if (!$db->loadResult())
			{
				$db->setQuery('UPDATE #__plugins SET published = 1 WHERE id = '.(int) $id);
				$db->query();
				JFactory::getApplication()->enqueueMessage("Mootools Upgrade plugin has been enabled.");
			}
		}
	}

	/**
	 * Can't use JPluginHelper here since there is no way
	 * of clearing the cached list of plugins.
	 *
	 * @return PlgSystemKoowa Instantiated plugin object
	 */
	public function bootFramework()
	{
		if (class_exists('Koowa')) {
			return true;
		}

		$db = JFactory::getDbo();
		$table = $this->getVersion() === '2.5' ? '#__extensions' : '#__plugins';
		$db->setQuery("SELECT folder AS type, element AS name, params
			 FROM $table
			 WHERE folder = 'system' AND element = 'koowa'"
		);
		$plugin = $db->loadObject();

		$legacypath = JPATH_PLUGINS.'/'.$plugin->type.'/'.$plugin->name.'.php';
		$path       = JPATH_PLUGINS.'/'.$plugin->type.'/'.$plugin->name.'/'.$plugin->name.'.php';
		$loaded     = false;

		foreach (array($legacypath, $path) as $p)
		{
			if (file_exists($p))
			{
				$loaded = true;
				require_once $p;
				break;
			}
		}

		if ($loaded === false) {
			return false;
		}

		$dispatcher = JDispatcher::getInstance();
		$className  = 'plg' . $plugin->type . $plugin->name;
		if (class_exists($className))
		{
			// Constructor does all the work in the plugin
			$class = new $className($dispatcher, (array) ($plugin));
		}

		return class_exists('Koowa');
	}

    public function storeUUID($uuid = false)
    {
        if($uuid === false) {
            return false;
        }

        if($this->_isLocal()) {
            return false;
        }

        $db     = JFactory::getDbo();

        $params = JComponentHelper::getParams('com_extman');
        $params->set('joomlatools_user_id', $uuid);

        if($this->getVersion() === '1.5')
        {
            $db->setQuery("SELECT `id` FROM #__components WHERE `link`='option=com_extman' AND `option`='com_extman'");
            $componentId = (int) $db->loadResult();

            $registry = new JRegistry();
            $registry->loadArray($params->toArray());
            $value = $registry->toString();

            $query = "UPDATE #__components SET params = ".$db->Quote($value)." WHERE `id` = ". $componentId;
        }
        else
        {
            $componentId = JComponentHelper::getComponent('com_extman')->id;

            $query = "UPDATE #__extensions SET params = ".$db->quote($params->toString())." WHERE `extension_id` = ". (int) $componentId;
        }

        $db->setQuery($query);
        $db->query();

        return true;
    }

    public function getUUID()
    {
        $file   = $this->installer->getPath('source').'/install/.subscription';

        if(JFile::exists($file))
        {
            $uuid = trim(JFile::read($file));

            return $uuid;
        }

        return false;
    }

	public function installExtensions()
	{
		$source = $this->installer->getPath('source');

		foreach ($this->manifest->extensions->extension as $extension)
		{
			$path = $source.'/'.(string)$extension;
			$this->installExtension($extension, $path);
		}
	}

	public function installExtension($extension, $path)
	{
		$installer = new JInstaller();
		$installer->install($path);

		if ((string)$extension['type'] === 'plugin')
		{
			$db = JFactory::getDBO();
			$id = $this->getExtensionId($extension);
			if ($this->getVersion() === '2.5') {
				$query = 'UPDATE #__extensions SET enabled = 1 WHERE extension_id = '.$id;
			}
			else {
				$query = 'UPDATE #__plugins SET published = 1 WHERE id = '.$id;
			}
			$db->setQuery($query);
			$db->query();
		}

		if ((string)$extension['protected'] === 'true') {
			$this->setCoreExtension($extension, true);
		}
	}

	public function setCoreExtension($extension, $value = true)
	{
		$value = (int) $value;
		$type  = (string)$extension['type'];
		$id    = (int) $this->getExtensionId($extension);
		$db    = JFactory::getDBO();

		if ($this->getVersion() === '2.5') {
			$query = "UPDATE #__extensions SET protected = {$value}
					WHERE extension_id = {$id}
					LIMIT 1";
		}
		else
		{
			$query = "UPDATE #__{$type}s SET iscore = {$value}
					WHERE id = {$id}
					LIMIT 1";
		}

		$db->setQuery($query);

		return $db->query();
	}

	public function uninstallExtensions()
	{
		foreach ($this->manifest->extensions->extension as $extension)
		{
			if ((string)$extension['protected'] === 'true') {
				$this->setCoreExtension($extension, false);
			}

			$this->uninstallExtension($extension);
		}
	}

	public function uninstallExtension($extension)
	{
		$type = (string)$extension['type'];
		$cid  = (int) $extension['client_id'];
		$id   = $this->getExtensionId($extension);

		$installer = new JInstaller();
		$result    = $installer->uninstall($type, $id, $cid);
	}

	public function getExtensionId($extension)
	{
		$type    = (string)$extension['type'];
		$element = (string)$extension['element'];
		$folder  = isset($extension['folder']) ? (string) $extension['folder'] : '';
		$cid     = isset($extension['client_id']) ? (int) $extension['client_id'] : 0;

		if ($type == 'component') {
			$cid = 1;
		}

		if ($type == 'component' && substr($element, 0, 4) !== 'com_') {
			$element = 'com_'.$element;
		} elseif ($type == 'module' && substr($element, 0, 4) !== 'mod_') {
			$element = 'mod_'.$element;
		}

		$db = JFactory::getDBO();
		if ($this->getVersion() === '2.5')
		{
			$query = "SELECT extension_id FROM #__extensions
				WHERE type = '$type' AND element = '$element' AND folder = '$folder' AND client_id = '$cid'
				LIMIT 1
			";
		}
		else
		{
			$query = "SELECT id FROM #__{$type}s";
			if ($type == 'component') {
				$query .= " WHERE `option` = '{$element}'";
			}
			else if ($type == 'module') {
				$query .= " WHERE module = '{$element}' AND client_id = '{$cid}'";
			}
			else if ($type == 'plugin') {
				$query .= " WHERE element = '{$element}' AND folder = '{$folder}'";
			}

			$query .= "LIMIT 1";
		}

		$db->setQuery($query);

		return $db->loadResult();
	}

	public function uninstallFramework()
	{
		$framework = $this->manifest->framework;
		foreach ($framework->folder as $folder) {
		    if(JFolder::exists(JPATH_ROOT.$folder)) JFolder::delete(JPATH_ROOT.$folder);
		}

		// Delete framework files, like the koowa plugin, and the default plugin
		foreach ($framework->file as $file) {
		    if(JFile::exists(JPATH_ROOT.$file)) JFile::delete(JPATH_ROOT.$file);
		}
	}

    /**
     * Tests a list of DB privileges against the current application DB connection.
     *
     * @param array $privileges An array containing the privileges to be checked.
     *
     * @return array True An array containing the privileges that didn't pass the test, i.e. not granted.
     */
    public function checkDatabasePrivileges($privileges)
    {
        $privileges = (array) $privileges;

        $db = JFactory::getDBO();

        $query = 'SELECT @@SQL_MODE';
        $db->setQuery($query);
        $sql_mode = $db->loadResult($query);

        $db_name = JFactory::getApplication()->getCfg('db');

        // Quote and escape DB name.
        if (strtolower($sql_mode) == 'ansi_quotes') {
            // Double quotes as delimiters.
            $db_name = '"' . str_replace('"', '""', $db_name) . '"';
        } else {
            $db_name = '`' . str_replace('`', '``', $db_name) . '`';
        }

        // Properly escape DB name.
        $db_name = str_replace('_', '\_', $db_name);

        $query = 'SHOW GRANTS';
        $db->setQuery($query);

        if (version_compare(JVERSION, '1.6', '<')) {
            $grants = $db->loadResultArray();
        } else {
            $grants = $db->loadColumn();
        }

        $granted = array();

        foreach ($privileges as $privilege) {
            foreach ($grants as $grant) {
                $regex = '/(grant\s+|,\s*)' . $privilege . '(\s*,|\s+on)/i';

                if (stripos($grant, 'ALL PRIVILEGES') || preg_match($regex, $grant)) {
                    // Check tables
                    $tables = substr($grant, stripos($grant, ' ON ') + 4);
                    $tables = substr($tables, 0, stripos($tables, ' TO'));
                    $tables = trim($tables);

                    if (in_array($tables, array('*.*', $db_name . '.*'))) {
                        $granted[] = $privilege;
                    }
                }
            }
        }

        return array_diff($privileges, $granted);
    }

    protected function _isLocal()
    {
        $isLocal = false;
        $ip      = @$_SERVER['REMOTE_ADDR'];

        if(!empty($ip))
        {
            $isLoopback = preg_match('/^localhost$|^127(?:\.[0-9]+){0,2}\.[0-9]+$|^(?:0*\:)*?:?0*1$/', $ip) ? true : false;

            if(!$isLoopback)
            {
                $result  = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE);
                $isLocal = $result === false ? true : false;
            }
            else $isLocal = true;
        }

        return $isLocal;
    }
}