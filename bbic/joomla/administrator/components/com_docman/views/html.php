<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanViewHtml extends ComDefaultViewHtml
{
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        $this->getTemplate()->getFilter('alias')->append(array(
            'icon://' => $config->root_url.'/joomlatools-files/docman-icons/'
        ), KTemplateFilter::MODE_READ | KTemplateFilter::MODE_WRITE);
    }

    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'root_url' => KRequest::root()
        ));

        parent::_initialize($config);
    }
}
