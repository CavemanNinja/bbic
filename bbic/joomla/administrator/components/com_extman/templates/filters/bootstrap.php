<?php
/**
 * @package     EXTman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComExtmanTemplateFilterBootstrap extends KTemplateFilterAbstract implements KTemplateFilterWrite
{
    /**
     * Id of the div to wrap the view in. Pass false to turn the wrapping off.
     */
    protected $_namespace;

    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        if (empty($config->namespace) && $config->namespace !== false) {
            $identifier = $this->getIdentifier();
            $config->namespace = sprintf('%s_%s', $identifier->type, $identifier->package);
        }

        $this->setNamespace($config->namespace);
    }

    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'priority'   => KCommand::PRIORITY_LOWEST,
            'namespace'  => null
        ));

        parent::_initialize($config);
    }

    public function getNamespace()
    {
        return $this->_namespace;
    }

    public function setNamespace($namespace)
    {
        $this->_namespace = $namespace;

        return $this;
    }

    public function write(&$text)
    {
        if ($this->_namespace !== false) {
            $text = sprintf('<div class="%s">%s</div>', $this->_namespace, $text);
        }

        return $this;
    }
}
