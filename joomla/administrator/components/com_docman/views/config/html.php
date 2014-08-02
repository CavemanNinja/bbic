<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanViewConfigHtml extends ComDocmanViewHtml
{
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        // Add a new alias for cutting the long texts
        $this->getTemplate()->getFilter('alias')->append(array(
            '@bytes2text(' => '$this->loadHelper(\'string.bytes2text\', ')
        );
    }

    public function display()
    {
        $this->assign('upload_max_filesize', ComDocmanDatabaseRowExtension::getMaximumUploadSize());

        $filetypes = array(
            'archive' => array('7z', 'ace', 'bz2', 'dmg', 'gz', 'rar', 'tgz', 'zip'),
            'document' => array('csv', 'doc', 'docx', 'html', 'key', 'keynote', 'odp', 'ods', 'odt', 'pages', 'pdf', 'pps', 'ppt', 'pptx', 'rtf', 'tex', 'txt', 'xls', 'xlsx', 'xml'),
            'image' => array('bmp', 'exif', 'gif', 'ico', 'jpeg', 'jpg', 'png', 'psd', 'tif', 'tiff'),
            'audio' => array('aac', 'aif', 'aiff', 'alac', 'amr', 'au', 'cdda', 'flac', 'm3u', 'm3u', 'm4a', 'm4a', 'm4p', 'mid', 'mp3', 'mp4', 'mpa', 'ogg', 'pac', 'ra', 'wav', 'wma'),
            'video' => array('3gp', 'asf', 'avi', 'flv', 'm4v', 'mkv', 'mov', 'mp4', 'mpeg', 'mpg', 'ogg', 'rm', 'swf', 'vob', 'wmv')
        );
        $this->assign('filetypes', $filetypes);

        return parent::display();
    }
}
