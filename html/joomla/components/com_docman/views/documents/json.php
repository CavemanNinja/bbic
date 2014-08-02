<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanViewDocumentsJson extends ComDocmanViewJson
{
    /**
     * Returns the JSON output
     *
     * @return array
     */
    protected function _getData()
    {
        $model = $this->getModel();
        $data  = array(
            'links' => array(
                'self' => array('href' => (string) KRequest::url())
            ),
            'documents' => array(
                'offset'   => (int) $model->offset,
                'limit'    => (int) $model->limit,
                'total'	   => $model->getTotal(),
                'items'    => $this->_filterRowset($this->getModel()->getList(), array($this, '_getDocument'))
            )
        );

        return $data;
    }
}
