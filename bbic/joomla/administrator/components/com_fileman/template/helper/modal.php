<?php
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
class ComFilemanTemplateHelperModal extends ComFilesTemplateHelperModal
{
	public function select($config = array())
	{
		$config = new KObjectConfigJson($config);
		$config->append(array(
				'name'      => '',
				'id'        => '',
				'visible'   => true,
				'link'      => '',
                'link_text' => $this->getObject('translator')->translate('Select'),
				'link_selector' => 'modal'
		))->append(array(
				'value' => $config->name,
				'id' => $config->name
		));

		$input = '<input name="%1$s" id="%4$s" value="%2$s" %3$s size="40" />';

        if (version_compare(JVERSION, '3.0', '<'))
        {
            $link = '<div class="button2-left"><div class="blank"><a class="%s"
                rel="{\'ajaxOptions\': {\'method\': \'get\'}, \'handler\': \'iframe\', \'size\': {\'x\': 700}}"
                href="%s">%s</a></div></div>';
        }
        else
        {
            $link = '<a class="btn btn-primary %s"
                rel="{\'ajaxOptions\': {\'method\': \'get\'}, \'handler\': \'iframe\', \'size\': {\'x\': 700}}"
                href="%s"><i class="icon-list icon-white"></i> %s</a>';
        }

		$html  = sprintf($input, $config->name, $this->getTemplate()->escape($config->value), $config->visible ? 'type="text" readonly' : 'type="hidden"', $config->id);
		$html .= sprintf($link, $config->link_selector, $config->link, $config->link_text);

        if (version_compare(JVERSION, '3.0', '>=')) {
            $html = '<span class="input-append">'.$html.'</span>';
        }

		return $html;
	}
}