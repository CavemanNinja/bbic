<?php
/**
* @package     	2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @�opyright   	Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.2 $
**/
defined('_JEXEC') or die;

class JFormFieldTwojtoolboxFieldRezerv extends JFormField{
	protected $type = 'twojtoolboxitemfieldrezerv';
	public $fullhide = 1;
	protected function getInput(){ return ''; }
	protected function getLabel(){ return ''; }
}
