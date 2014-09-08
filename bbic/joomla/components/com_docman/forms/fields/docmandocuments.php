<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class JFormFieldDocmandocuments extends JFormField
{
    protected $type = 'Docmandocuments';

    protected function getInput()
    {
        if (!class_exists('Koowa')) {
            return '';
        }

        $value = $this->value;
        $el_name = $this->name;

        KService::get('translator')->loadLanguageFiles('com_docman');

        $view = KService::get('com://admin/docman.view.default');
        $template = $view->getTemplate();

        $string = "
        <?= @helper('com://admin/docman.template.helper.listbox.documents_ajax', array(
            'name' => \$el_name,
            'id'   => 'docman_document_select',
            'selected' => \$value
        ));";

        $template->loadString($string, array(
            'el_name' => $el_name,
            'value'   => $value
        ));

        return $template->render();
    }
}
