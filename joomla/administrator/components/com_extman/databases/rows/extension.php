<?php
/**
 * @package     EXTman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class ComExtmanDatabaseRowExtension extends KDatabaseRowTable implements KServiceInstantiatable
{
   	public static function getInstance(KConfigInterface $config, KServiceInterface $container)
    {
        $identifier = clone $config->service_identifier;
        if ($config->data && $config->data->identifier) {
        	$id = new KServiceIdentifier($config->data->identifier);
        	if ($id->type === 'com') {
        		$identifier->package = $id->package;
        	}
        }

        $identifier = $container->getIdentifier($identifier);

        if($identifier->classname != 'KDatabaseRowDefault' && class_exists($identifier->classname)) {
            $classname = $identifier->classname;
        } else {
            $classname = 'ComExtmanDatabaseRowExtension';
        }

        $instance = new $classname($config);
        return $instance;
    }

	public function setData($data, $modified = true)
	{
		$result = parent::setData($data, $modified);

		if (isset($data['package']) || isset($data['manifest'])) {
			$this->setup();
		}

		return $result;
	}

	public function __set($column, $value)
	{
		parent::__set($column, $value);

		if (in_array($column, array('package', 'manifest'))) {
			$this->setup();
		}
	}

	public function __get($column)
	{
		if ($column == 'dependents') {
			return $this->getService('com://admin/extman.model.dependencies')->extman_extension_id($this->id)->getList();
		}

		if ($column == 'children') {
			return $this->getService('com://admin/extman.model.extensions')->parent_id($this->id)->getList();
		}

        if ($column == 'joomlatools_user_id' && !isset($this->_data['joomlatools_user_id']))
        {
            if ($this->type == 'component' && $this->parent_id == 0)
            {
                $component = $this->name;
                if (substr($component, 0, 4) !== 'com_') {
                    $component  = 'com_'.$component;
                }
                $params     = JComponentHelper::getParams($component);

                $this->_data['joomlatools_user_id'] = $params->get('joomlatools_user_id');
            }
        }

		return parent::__get($column);
	}
	
	public function setupManifest($path)
	{
		$xmlfiles = JFolder::files($path, '.xml$', 1, true);
		if (empty($xmlfiles)) {
			return;
		}
		
		foreach ($xmlfiles as $file)
		{
			$chunk = file_get_contents($file, null, null, -1, 120);
			
			if (strpos($chunk, '<install') !== false 
				&& version_compare(JVERSION, '3.0', 'ge')) 
			{
				$contents = file_get_contents($file);
				
				// <install to <extension
				$pattern  = '#<install type="([a-z0-9\-]+)" version="[0-9\.]+"#i';
				$contents = preg_replace($pattern, '<extension type="\\1" version="3.0.0"', $contents);
				
				// </install to </extension
				$pattern  = '#</install>\s*$#i';
				$contents = preg_replace($pattern, '</extension>', $contents);
				
				// <name>component</name> to com_component
				// 3.0 requires that component names start with com_ in the manifest
				if (strpos($chunk, 'type="component"') !== false) {
					$pattern = '#<name>(?<!com_)([a-z0-9\-\_]+)</name>#i';
					if (preg_match($pattern, $contents, $matches)) {
						$contents = str_replace($matches[0], '<name>com_'.strtolower($matches[1]).'</name>', $contents);
					}
				}
				
				JFile::write($file, $contents);
			}
		}
	}

	public function setup()
	{
		if (!$this->installer) {
			$this->installer = new ComExtmanInstaller();
		}

		if ($this->package)
		{
			$this->setupManifest($this->package);
			
			$this->installer->setPath('source', $this->package);
			$this->installer->getManifest();
			$this->manifest_path = $this->installer->getPath('manifest');

			if (!$this->manifest_path || !file_exists($this->manifest_path)) {
				$this->setStatusMessage('Manifest not found');
				return false;
			}
			$this->manifest = simplexml_load_file($this->manifest_path);
		}

		if ($this->manifest && !is_string($this->manifest))
		{
			/* can't use $this->identifier here because __set does string comparison
			 * and doesn't convert the object to string
			 */
			try {
				$this->_data['identifier'] = new KServiceIdentifier((string)$this->manifest->identifier);
			} catch (KServiceIdentifierException $e) {
				$this->setStatusMessage('Invalid identifier in the manifest');
				return false;
			}

			$existing = $this->getTable()->select(array('identifier' => (string)$this->identifier), KDatabase::FETCH_ROW);

			if ($this->old_version === null)
            {
				$this->_new    = !($existing && $existing->id);

                if (is_object($existing))
                {
                    $this->id      = $existing->id;
                    $this->old_version = $existing->version;
                    $this->joomlatools_user_id = $existing->joomlatools_user_id;
                }

			}

			$this->type    = $this->detectType();
			$this->name    = (string) $this->manifest->name;
			$this->version = (string) $this->manifest->version;
		}
	}

	public function detectType()
	{
		static $type_map = array(
			'com' => 'component',
			'mod' => 'module',
			'plg' => 'plugin'
		);

		$type = isset($type_map[(string)$this->identifier->type]) ? $type_map[(string)$this->identifier->type] : 'component';

		return $type;
	}

    /**
     * Gets the subscriber's UUID from the package and removes it afterwards
     *
     * @param $package string Package path
     * @return null|string
     */
    public function getUUID($package)
    {
        $uuid = null;
        $file = $package.'/install/.subscription';

        if(JFile::exists($file))
        {
            $uuid = trim(JFile::read($file));

            JFile::delete($file);
        }

        return $uuid;
    }

    /**
     * Store's the package's UUID into the extensions table's params column
     *
     * @param $package string Package path
     * @return null|string
     */
    protected function _storeUUID($uuid = false)
    {
        if ($uuid === false) {
            return false;
        }

        if ($this->_isLocal()) {
            return false;
        }

        $db     = JFactory::getDbo();


        $component = $this->name;
        if (substr($component, 0, 4) !== 'com_') {
            $component  = 'com_'.$component;
        }

        $params = JComponentHelper::getParams($component);
        $params->set('joomlatools_user_id', $uuid);

        if (version_compare(JVERSION, '1.6', '<'))
        {
            $db->setQuery("SELECT `id` FROM #__components WHERE `link`='option=".$component."' AND `option`='".$component."'");
            $componentId = (int) $db->loadResult();

            $registry = new JRegistry();
            $registry->loadArray($params->toArray());
            $value = $registry->toString();

            $query = "UPDATE #__components SET params = ".$db->Quote($value)." WHERE `id` = ". $componentId;
        }
        else
        {
            $componentId = JComponentHelper::getComponent($component)->id;

            $query = "UPDATE #__extensions SET params = ".$db->quote($params->toString())." WHERE `extension_id` = ". (int) $componentId;
        }

        $db->setQuery($query);
        $db->query();

        $this->_data['joomlatools_user_id'] = $uuid;

        return true;
    }

	public function save()
	{
		$result = true;

        if ($this->source)
        {
            $uuid = $this->getUUID($this->source);

            if ($uuid && $this->joomlatools_user_id != $uuid) {
                $this->user_id_saved = $this->_storeUUID($uuid);
            }
        }

		if ($this->package)
		{
			$result = $this->installer->install($this->package);

			if ($result)
			{
				if (is_string($this->identifier)) {
					$this->_data['identifier'] = new KServiceIdentifier($this->identifier);
				}

				$this->joomla_extension_id = $this->getExtensionId(array(
					'type' => $this->type,
					'element' => $this->type == 'plugin' ? $this->identifier->name : $this->identifier->package,
					'folder' => $this->type == 'plugin' ? $this->identifier->package : '',
					'client_id' => $this->identifier->application === 'admin' || $this->type == 'component' ? 1 : 0
				));
			}
			else
			{
				$this->setStatusMessage($this->installer->getError());
				$this->setStatus(KDatabase::STATUS_FAILED);
			}
		}

		if ($result && (isset($uuid) ||array_intersect($this->getModified(), array_keys($this->getTable()->getColumns()))))
		{

			if ($this->joomla_extension_id)
			{
				$this->setCoreExtension(true);
				if ($this->type === 'plugin')
				{
					if (version_compare(JVERSION, '1.6.0', 'ge')) {
						$query = 'UPDATE #__extensions SET enabled = 1 WHERE extension_id = '.(int)$this->joomla_extension_id;
					} else {
						$query = 'UPDATE #__plugins SET published = 1 WHERE id = '.(int)$this->joomla_extension_id;
					}

					$db = JFactory::getDBO();
					$db->setQuery($query);
					$db->query();
				}
			}
			parent::save();
		}

		if ($result && $this->parent_id)
		{
			$data = array(
				'extman_extension_id' => $this->parent_id,
				'dependent_id' => $this->id
			);

			$row = $this->getService('com://admin/extman.model.dependencies')->set($data)->getItem();
			if ($row->isNew()) {
				$row->setData($data)->save();
			}
		}

		if ($result && $this->manifest)
		{
			if (is_string($this->manifest)) {
				$this->manifest = simplexml_load_string($this->manifest);
			}

			if ($this->manifest->dependencies)
			{
				foreach ($this->manifest->dependencies->dependency as $dependency)
				{
					$row = $this->getService('com://admin/extman.database.row.extension');
					$row->setData(array(
						'package' => $this->source.'/'.(string)$dependency,
						'parent_id' => $this->id
					));
					$row->save();
				}
			}

            // Delete old files to keep the installation clean
            if ($this->manifest->deleted)
            {
                foreach ($this->manifest->deleted->file as $file)
                {
                    $path = JPATH_ROOT.'/'.(string)$file;

                    if (file_exists($path)) {
                        JFile::delete($path);
                    }
                }

                foreach ($this->manifest->deleted->folder as $folder)
                {
                    $path = JPATH_ROOT.'/'.(string)$folder;

                    if (file_exists($path)) {
                        JFolder::delete($path);
                    }
                }
            }
		}

		return $result;
	}

	public function delete()
	{
		$result = true;

        // Make sure to fetch the joomlatools_user_id now.
        // Once the component has been uninstalled, this value will be lost.
        if (!isset($this->_data['joomlatools_user_id'])) {
            $uuid = $this->joomlatools_user_id;
        }

		if ($this->joomla_extension_id)
		{
			$this->setCoreExtension(false);
			if (is_string($this->identifier)) {
				$this->_data['identifier'] = new KServiceIdentifier($this->identifier);
			}
			$client_id = $this->identifier->application === 'admin' || $this->type == 'component' ? 1 : 0;
			$result = $this->installer->uninstall($this->type, $this->joomla_extension_id, $client_id);
		}

		if ($result) {
			$result = parent::delete();
		}

		if ($result)
		{
			// Uninstall dependencies (if they are dependent to this extension only)
			foreach ($this->dependents as $dependency)
			{
				$count = count($this->getService('com://admin/extman.model.dependencies')->dependent_id($dependency->dependent_id)->getList());

				if ($count === 1) {
					$extension = $this->getService('com://admin/extman.model.extensions')->id($dependency->dependent_id)->getItem();
					$extension->delete();
				}

				$dependency->delete();
			}
		}

		return $result;
	}

	public function getExtensionId($extension)
	{
		return ComExtmanInstaller::getExtensionId($extension);
	}

	public function setCoreExtension($value = true)
	{
		$value = (int) $value;
		$db    = JFactory::getDBO();

		if (version_compare(JVERSION, '1.6.0', 'ge'))
		{
			$query = "UPDATE #__extensions SET protected = {$value}"
				. " WHERE extension_id = ".(int) $this->joomla_extension_id
				. " LIMIT 1";
		}
		else
		{
			$query = "UPDATE #__{$this->type}s SET iscore = {$value}"
				. " WHERE id = ".(int) $this->joomla_extension_id
				. " LIMIT 1";
		}
		$db->setQuery($query);

		return $db->query();
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
