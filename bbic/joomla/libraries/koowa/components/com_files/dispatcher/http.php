<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-files for the canonical source repository
 */

/**
 * Dispatcher
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesDispatcherHttp extends ComKoowaDispatcherHttp
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'limit' => array(
                'max' => 2000 // Used in tree view
            )
        ));

        parent::_initialize($config);
    }

    /**
     * Overloaded execute function to handle exceptions in JSON requests
     */
    public function execute($action, KControllerContextInterface $context)
    {
        try {
            return parent::execute($action, $context);
        } catch (Exception $e) {
            return $this->_handleException($e);
        }
    }

    protected function _handleException(Exception $e) 
    {
    	if ($this->getRequest()->getFormat() == 'json')
        {
    		$obj = new stdClass;
    		$obj->status = false;
    		$obj->error  = $e->getMessage();
    		$obj->code   = $e->getCode();

    		// Plupload do not pass the error to our application if the status code is not 200
    		$code = $this->getRequest()->query->plupload ? 200 : ($e->getCode() ? $e->getCode() : 500);

    		header($code.' '.str_replace("\n", ' ', $e->getMessage()), true, $code);

    		echo json_encode($obj);
    		JFactory::getApplication()->close();
    	}
    	else throw $e;

        return false;
    }

    // FIXME: this is here because forwarded dispatchers still render results
    protected function _actionSend(KDispatcherContextInterface $context)
    {
        if (!$context->getRequest()->isGet() || $context->getResponse()->getContentType() !== 'text/html') {
            return parent::_actionSend($context);
        }
    }
}
