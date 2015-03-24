<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComFilemanControllerFile extends ComKoowaControllerView
{
	public function __construct(KObjectConfig $config)
	{
		parent::__construct($config);

        $this->addCommandCallback('before.render', 'syncSettings');
	}

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'model' => 'com:files.model.files',
            'view' => 'files'
        ));

        parent::_initialize($config);
    }

	public function syncSettings(KControllerContextInterface $context)
	{
		$container = $this->getObject('com:files.controller.container')
						->slug('fileman-files')
						->read();

		$params = JComponentHelper::getParams('com_media');

		$config = new stdclass;

		$extensions = array_map('strtolower', explode(',', $params->get('upload_extensions')));
		$config->allowed_extensions = array_values(array_unique($extensions));

		if ($params->get('check_mime'))
		{
			$mimes = array_map('strtolower', explode(',', $params->get('upload_mime')));
			$config->allowed_mimetypes = array_unique($mimes);
		}
		else $config->allowed_mimetypes = false;

		$config->maximum_size = $params->get('upload_maxsize')*1024*1024;

		// Do not overwrite thumbnail configuration
		$config->thumbnails = $container->getParameters()->thumbnails;

		$container->path = $params->get('file_path');

		$container->getParameters()->merge((array) $config);

		$container->save();
	}
}