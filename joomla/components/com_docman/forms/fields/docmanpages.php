<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

JFormHelper::loadFieldClass('groupedlist');

class JFormFieldDocmanpages extends JFormFieldGroupedList
{
    protected $type = 'Docmanpages';

    protected function getGroups()
    {
        $allowed_views = array('document', 'list', 'filteredlist');
        $component_id  = JComponentHelper::getComponent('com_docman')->id;

        $db = JFactory::getDbo();
        $db->setQuery(sprintf('
            SELECT id AS value, title AS text, menutype, link
            FROM #__menu
            WHERE component_id = %d AND published = 1 AND menutype <> "main"', $component_id));

        $items = $db->loadObjectList();

        $groups = array();
        foreach ($items as $item) {
            parse_str(str_replace('index.php?', '', $item->link), $query);
            if (!isset($query['view']) || !in_array($query['view'], $allowed_views)) {
                continue;
            }

            if (!isset($groups[$item->menutype])) {
                $groups[$item->menutype] = array();
            }

            $groups[$item->menutype][] = JHtml::_('select.option', $item->value, $item->text);
        }

        return $groups;
    }

    /**
     * Wraps the output in com_docman class for Bootstrap and removes Chosen
     */
    protected function getInput()
    {
        $html = parent::getInput();

        $html = '<div class="com_docman">'.$html.'</div>';

        if(version_compare(JVERSION, '3.0', 'ge'))
        {
            $html .= "
            <script type=\"text/javascript\">
                jQuery(function($){
                    $('#{$this->id}_chzn').remove();
                });
            </script>
            ";
        }

        return $html;
    }
}
