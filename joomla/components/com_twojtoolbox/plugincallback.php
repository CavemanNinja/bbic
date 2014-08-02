<?php 
/**
* @package     	2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   	Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 2.0.0 $
**/

defined('_JEXEC') or die;
class TwoJToolBoxPluginCallBack extends JObject{

	protected $params;
	protected $globalparams;
	protected $id;
	protected $item;
	protected $type;
	protected $plugin_path;
	protected $plugin_url;
	protected $version_plugin;
	
	public  $javascript_code = '';
	public 	$error_text;
	
	public function __construct( $plugin_info ){
		$this->error_text = '';

		$this->item = new JObject();
		$this->item->id = $plugin_info->item_id;
		$this->item->title = $plugin_info->item_title;
		$this->item->state = $plugin_info->state;
		$this->id = $this->item->id;
		$this->type = $plugin_info->type;
		
		
		$params = new JRegistry;
		$params->loadString($plugin_info->params);
		$this->params = $params;
		$this->version_plugin = $plugin_info->v_active;
		$this->globalparams = JComponentHelper::getParams('com_twojtoolbox');
		$this->plugin_path =  JPATH_SITE.'/components/com_twojtoolbox/plugins/'.$this->type.'/'.$this->version_plugin.'/';
		$this->plugin_url = JURI::root().'components/com_twojtoolbox/plugins/'.$this->type.'/'.$this->version_plugin.'/';
	
		
		$app = JFactory::getApplication();
		
		$this->def_params = $app->getUserState('com_twojtoolbox.plugin.workdata.'.$this->type.'.def_params', '');
		if( $this->def_params=='' ){
			$this->def_params = new JRegistry;
			$xml_option = $this->plugin_path.'item_options.xml';
			if( JFile::exists( $xml_option ) ){
				$xml =JFactory::getXML($xml_option);
				if(isset($xml->fields) && isset($xml->fields->fieldset) && count($xml->fields->fieldset))
						foreach ($xml->fields->fieldset as $fieldset)
							if( count($xml->fields->fieldset) )
							foreach ($fieldset as $field) $this->def_params->def( (string) $field['name'], (string) $field['default'] );
			} 
			$app->setUserState('com_twojtoolbox.plugin.workdata.'.$this->type.'.def_params', $this->def_params);
		}
		$document = JFactory::getDocument();
		$format =  $document->getType('raw');
		$tmpl =JRequest::getCmd('tmpl', '');
		$print =JRequest::getCmd('print', 0);
		$this->render_content = $app->isAdmin()  || $print || $tmpl=='component' || $format!='html' ;  //|| JDEBUG
	}
	
	
	public function outcontent(  ){
		echo 'Testing'.$this->id;
	}
}