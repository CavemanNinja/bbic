<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class JFormFieldDocmancategories extends JFormField
{
    protected $type = 'Docmancategories';

    protected function getInput()
    {
        if (!class_exists('Koowa')) {
            return '';
        }

        $value = $this->value;
        $el_name = $this->name;

        $key_field = (string) $this->element['key_field'];
        $multiple = (string) $this->element['multiple'] == 'true';
        $deselect =  (string) $this->element['deselect'] === 'true';
        $id =  isset($this->element['id']) ? (string) $this->element['element_id'] : 'docman_categories_select2';

        KService::get('translator')->loadLanguageFiles('com_docman');

        $view = KService::get('com://admin/docman.view.default');
        $template = $view->getTemplate();

        $attribs = array();
        if ($multiple) {
            $attribs['multiple'] = true;
            $attribs['size'] = $this->element['size'] ? $this->element['size'] : 5;
        }

        $value_field = $key_field ? $key_field : 'slug';
        $string = "
        <?= @helper('behavior.bootstrap'); ?>
        <?= @helper('com://admin/docman.template.helper.listbox.categories', array(
            'autocomplete' => false,
            'name' => \$el_name,
            'value' => \$value_field,
            'deselect' => \$deselect,
            'selected' => \$value,
            'attribs'  => array_merge(\$attribs, array(
                'id' => \$id,
                'data-placeholder' => @text('All Categories')))
        )); ?>";

        //@TODO submit fix on Joomla CMS github for allowing Chosen opt-out on form elements in menu item custom forms
        if(version_compare(JVERSION, '3.0', 'ge'))
        {
            $string .= "
            <script>
                jQuery(function($){
                    $('#<?= \$id ?>_chzn').remove();
                });
            </script>
            ";
        }

        $template->loadString($string, array(
            'el_name'  => $el_name,
            'value'    => $value,
            'value_field'    => $value_field,
            'deselect' => $deselect,
            'attribs'  => $attribs,
            'id' => $id
        ));

        return $template->render();
    }
}
