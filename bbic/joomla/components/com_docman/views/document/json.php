<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanViewDocumentJson extends ComDocmanViewJson
{
    /**
     * Returns the JSON output
     *
     * @return array
     */
    protected function _getData()
    {
        $data = $this->_getDocument($this->getModel()->getItem());

        return $data;
    }
}
