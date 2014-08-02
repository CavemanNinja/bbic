<?php
/**
* @package     2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.8 $
**/

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controlleradmin');
class TwojToolboxControllerPlitems extends JControllerAdmin{
	public function getModel($name = 'Plitem', $prefix = 'TwojToolboxModel', $config = array('ignore_request' => true)){	
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
}
