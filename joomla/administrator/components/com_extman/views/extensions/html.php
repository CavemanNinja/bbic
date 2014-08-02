<?php
/**
 * @package     EXTman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
class ComExtmanViewExtensionsHtml extends ComDefaultViewHtml
{
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        $this->getTemplate()->getFilter('alias')->append(array(
            '@translateComponentName(' => '$this->getView()->translateComponentName('
        ), KTemplateFilter::MODE_READ);
    }

    public function translateComponentName($component)
    {
        $language = JFactory::getLanguage();

        $language->load($component.'.sys', JPATH_BASE, null, false, false)
            ||	$language->load($component.'.sys', JPATH_ADMINISTRATOR.'/components/'.$component, null, false, false)
            ||	$language->load($component.'.sys', JPATH_BASE, $language->getDefault(), false, false)
            ||	$language->load($component.'.sys', JPATH_ADMINISTRATOR.'/components/'.$component, $language->getDefault(), false, false);

        return $language->hasKey($component) ? JText::_($component) : $component;
    }
}