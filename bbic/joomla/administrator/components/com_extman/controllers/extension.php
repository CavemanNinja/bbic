<?php
/**
 * @package     EXTman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComExtmanControllerExtension extends ComDefaultControllerDefault
{
	public function __construct(KConfig $config)
	{
		parent::__construct($config);

		$this->registerCallback('before.get', array($this, 'beforeGet'));
		$this->registerCallback('before.add', array($this, 'beforeAdd'));
	}

	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'behaviors' => array('trackable')
		));

		parent::_initialize($config);
	}

	public function beforeGet()
	{
		if ($this->isDispatched()) {
			$this->getRequest()->top_level = true;
		}
	}

	/**
	 * Load the correct row object for installs
	 *
	 * @param KCommandContext $context
	 */
	public function beforeAdd(KCommandContext $context)
	{
		if (is_object($context->data->manifest) && (string)$context->data->manifest->identifier)
		{
			$id = new KServiceIdentifier((string)$context->data->manifest->identifier);
        	if ($id->type === 'com') {
        		$this->getModel()->getTable()->setRowPackage($id->package);
        	}
		}
	}
}