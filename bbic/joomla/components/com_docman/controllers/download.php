<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanControllerDownload extends ComDocmanControllerDocument
{
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'model' => 'documents',
            'view'  => 'download'
        ));

        parent::_initialize($config);
    }

    /**
     * Use browser redirection for http/https links or if the path does not have a whitelisted stream wrapper
     *
     * @param KCommandContext $context Context
     * @return mixed Action result
     */
    protected function _actionGet(KCommandContext $context)
    {
        $row = $this->getModel()->getItem();

        if ($this->_request->view == 'download' && $row->storage_type == 'remote')
        {
            if (substr($row->storage_path, 0, 4) === 'http' || !$row->hasStreamWrapper()) {
                $this->_redirect($row->storage_path);
                return false;
            }
        }

        return parent::_actionGet($context);
    }

    /**
     * Redirects user to a given URL
     *
     * Uses JavaScript redirection if headers are already sent. Otherwise sends a 303 header.
     *
     * @param $url string A fully qualified URL
     */
    protected function _redirect($url)
    {
        // Strip out any line breaks.
        $url = preg_split("/[\r\n]/", $url);
        $url = $url[0];

        // If the headers have been sent, then we cannot send an additional location header
        // so we will output a javascript redirect statement.
        if (headers_sent())
        {
            echo "<script>document.location.href='" . htmlspecialchars($url) . "';</script>\n";
        }
        else
        {
            jimport('phputf8.utils.ascii');

            $document   = JFactory::getDocument();
            $user_agent = null;

            if (isset($_SERVER['HTTP_USER_AGENT'])) {
                $user_agent = $_SERVER['HTTP_USER_AGENT'];
            }

            if ((stripos($user_agent, 'MSIE') !== false || stripos($user_agent, 'Trident') !== false)
                && !utf8_is_ascii($url))
            {
                // MSIE type browser and/or server cause issues when url contains utf8 character,so use a javascript redirect method
                echo '<html><head><meta http-equiv="content-type" content="text/html; charset=' . $document->getCharset() . '" />'
                    . '<script>document.location.href=\'' . htmlspecialchars($url) . '\';</script></head></html>';
            }
            else
            {
                // All other browsers, use the more efficient HTTP header method
                header('HTTP/1.1 303 See other');
                header('Location: ' . $url);
                header('Content-Type: text/html; charset=' . $document->getCharset());
            }
        }

        JFactory::getApplication()->close();
    }

}
