<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

defined( '_JEXEC' ) or die;

jimport('joomla.plugin.plugin');

class plgButtonFilelink extends JPlugin
{
    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);
        $this->loadLanguage();
    }

    public function onDisplay($name)
    {
        if (JFactory::getApplication()->isAdmin() && version_compare(JVERSION, '3.0', '<')) {
            $style = ".button2-left .picture {
                background:transparent url(".JURI::root(true)."/media/com_fileman/images/j_button2_filelink.png) no-repeat scroll 100% 0pt;
            }";
            JFactory::getDocument()->addStyleDeclaration($style);
        }

        $button = new JObject();
        if (version_compare(JVERSION, '3.0', '>')) {
            $button->class = 'btn';
        }

        $button->set('modal', true);
        $button->set('link', 'index.php?option=com_fileman&amp;view=filelink&amp;e_name='.$name.'&amp;tmpl=koowa');
        $button->set('text', JText::_('PLG_FILELINK_BUTTON_FILE'));
        $button->set('name', 'picture');
        $button->set('options', "{handler: 'iframe', size: {x: 800, y: 500}}");

        return $button;
    }
}