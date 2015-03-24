<?php
/**
* @package     FILEman
* @copyright   Copyright (C) 2011 - 2014 Timble CVBA. (http://www.timble.net)
* @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link        http://www.joomlatools.com
*/

class ComFilemanControllerBehaviorNotifiable extends KControllerBehaviorAbstract
{
    protected function _afterAdd(KControllerContextInterface $context)
    {
        if ($context->getResponse()->getStatusCode() !== 201) {
            return;
        }

        $page = JFactory::getApplication()->getMenu()->getItem($this->getRequest()->query->Itemid);

        if (!$page) {
            return;
        }

        $emails = $page->params->get('notification_emails');
        if (empty($emails)) {
            return;
        }

        $translator = $this->getObject('translator');
        $emails     = explode("\n", $emails);
        $config  	= JFactory::getConfig();
        $from_name  = $config->get('fromname');
        $mail_from  = $config->get('mailfrom');
        $sitename   = $config->get('sitename');
        $subject    = $translator->translate('A new file was submitted for you to review on {sitename}', array(
            'sitename' => $sitename));

        $admin_link = JURI::root().'administrator/index.php?option=com_fileman&view=files';
        $name = $context->result->name;
        $admin_title = $translator->translate('File Manager');

        foreach ($emails as $email)
        {
            $body = $translator->translate('Submit notification mail body', array(
                'name'     => $name,
                'sitename' => $sitename,
                'url'      => $admin_link,
                'url_text' => $admin_title
            ));
            JFactory::getMailer()->sendMail($mail_from, $from_name, $email, $subject, $body, true);
        }
    }
}