<?php
/**
* @package     2JToolBox
* @author      2JoomlaNet http://www.2joomla.net
* @ñopyright   Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license     released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version     $Revision: 1.0.2 $
**/


defined('_JEXEC') or die;
jimport('joomla.plugin.plugin');

class plgSystemTwojToolbox extends JPlugin{

	var $_twojtoolbox = null;
	protected $regex = '/\{2jtoolbox_content\s+([a-z0-9]*)\s+id:([0-9]*)\s+begin(\s+title:([^}]*)){0,1}\}.*\{2jtoolbox_content\s+\\1\s+id:\\2\s+end\}/isU';
	protected $regex_easy = '/\{2jtoolbox_content\s+([a-z0-9]*)\s+id:([0-9]*)\s+begin(\s+title:([^}]*)){0,1}\}.*\{2jtoolbox_content\s+\\1\s+id:\\2\s+end\}/is';
	
	protected $regex_single = '/{2jtoolbox\s+([a-z0-9]*)\s+id:([0-9]*)}/i';
	
	function __construct(& $subject, $config){
		parent::__construct($subject, $config);
	}

	public function onContentPrepare($context, &$article, &$params, $page = 0){

		if (strpos($article->text, '{2jtoolbox') === false) return true;
		JLoader::register('TwojToolBoxSiteHelper', JPATH_SITE.'/components/com_twojtoolbox/helpers/twojtoolboxsite.php');
		if( !class_exists('TwojToolBoxSiteHelper') ) return '';
		if( ($new_article_text = preg_replace_callback( ( strlen($article->text)<100000 ? $this->regex : $this->regex_easy ) , 'TwojToolBoxSiteHelper::parseMultiTagCalback', $article->text) )!==NULL ){
			$article->text = $new_article_text;
		}
		$matches	= array();
		preg_match_all($this->regex_single, $article->text, $matches, PREG_SET_ORDER);
		if( count($matches) ){
			foreach ($matches as $match) {
				if(count($match)==3 && (int)$match[2] ){
					$parent_acticle_id = '';
					if(isset($article->id) && $article->id > 0 ){
						$parent_acticle_id = $article->id;
					}

					if( JRequest::getVar('com_twojtoolbox_item_'.(int)$match[2], 0, '', 'int') > 3  ){
						$output  = 'cicle';
					}  else $output  = TwojToolBoxSiteHelper::getPluginContent((int)$match[2], $parent_acticle_id);
					$article->text = preg_replace("|$match[0]|", $output, $article->text, 1);
				}
			}
		}
	}
	
	function onAfterRoute(){
		$app = JFactory::getApplication();
		$document = JFactory::getDocument();
		$format =  $document->getType('raw');
		$tmpl =JRequest::getCmd('tmpl', '');
		$print =JRequest::getCmd('print', 0);
		if ($app->isAdmin()  || $print || $tmpl=='component' || $format!='html' ){
			return;
		}
		$menu = $app->getMenu()->getActive();
		if( !isset($menu->id)  ) return;
		$itemid = $menu->id;
		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('id');
		$query->where('itemid = '. (int) $itemid, 'OR' );
		$query->where('itemid = -1', 'OR');
		$query->from('#__twojtoolbox_menu');
		$db->setQuery($query);
		//echo (string) $query;
		if( $plugins_for_page = $db->loadColumn() ){
			for($i=0;$i<count($plugins_for_page);$i++){
				$query->clear();
				$query->select('plu.*, ite.id AS item_id, ite.title AS item_title, ite.params, ite.state, ite.cacheid');
				$query->where('ite.`id` = '. (int) $plugins_for_page[$i]);
				$query->where('plu.`type` = ite.`type`');
				$query->where('plu.`daemon` = 1 ');
				$query->where('ite.`state` = 1');
				$query->from('#__twojtoolbox AS ite, #__twojtoolbox_plugins AS plu');
				$db->setQuery($query);
				if( $plugin_info = $db->loadObject() ){
					require_once (JPATH_SITE.'/components/com_twojtoolbox/pluginclass.php');
					jimport('joomla.filesystem.file');
					$plugin_classfile = JPATH_SITE.'/'.
									'components/'.
									'com_twojtoolbox/'.
									'plugins/'.
									$plugin_info->type.'/'.
									$plugin_info->v_active.'/'.
									'twoj_'.$plugin_info->type.'_plugin.php';
					if( !JFile::exists($plugin_classfile) ) return JText::_('COM_TWOJTOOLBOX_ERROR_FILE_ERROR');
					require_once ($plugin_classfile);
					$class = 'TwoJToolBox'.ucfirst($plugin_info->type);
					if (class_exists($class)) $instance = new $class($plugin_info);
						else return JText::sprintf('JLIB_APPLICATION_ERROR_INVALID_CONTROLLER_CLASS', $class);
					if( $instance->error_text  ) return  $instance->error_text;
					$instance->getDaemon(); 
				}
			}
		}
	}
	
	function onAfterInitialise(){
		$app = JFactory::getApplication();
		$user	= JFactory::getUser();
		
		//$document = JFactory::getDocument();
		//$format =  $document->getType('raw');
		$format =JRequest::getCmd('format', 'html');
		// EVENTRAW incorrect detection of the RAW output 2 cases (select one from this line)
		
		$tmpl =JRequest::getCmd('tmpl', '');
		$print =JRequest::getCmd('print', 0);
		if ($app->isAdmin()  || $print || $tmpl=='component' || $format!='html' ) {
			return;
		}
		JRequest::setVar('com_twojtoolbox_filelist_css', array());
		JRequest::setVar('com_twojtoolbox_filelist_js', array());
		JRequest::setVar('com_twojtoolbox_filelist_less', array());
	}
	
	function getFileList( $type = 'css', $cache_enable=0, $cache=NULL, $get_id = ''  ){ 
		$document = JFactory::getDocument();
		$twojtoolbox_cache_enable = JComponentHelper::getParams('com_twojtoolbox')->get('twojcache', 1);
		
		$fileList = JRequest::getVar('com_twojtoolbox_filelist_'.$type, array(), '', 'array');
		if( count($fileList) ){
			$in_file = array();
			if(  $type == 'js'  ) $in_file[] = 'init';
			for($i=0;$i<count($fileList);++$i){
				if( !in_array($fileList[$i], $in_file) ) $in_file[] = $fileList[$i]; 
			}
			$urlFile = TwojToolBoxSiteHelper::scriptCompile( implode( '2jbrs2', $in_file), $type );
			if(!$urlFile || !$twojtoolbox_cache_enable ) $urlFile = JURI::root(true)."/index.php?option=com_twojtoolbox&amp;format=raw&amp;task=ajax.get".$type."&amp;need=".implode( '2jbrs2', $in_file)."&amp;name=2jscript.".$type;
			if( $type == 'js' ) $document->addScript($urlFile);
			if( $type == 'css' || $type == 'less' ) $document->addStyleSheet($urlFile);
			if($cache_enable) $cache->store( $urlFile, $get_id.$type);
		}
	}
	
	function onBeforeCompileHead(){
		$app = JFactory::getApplication();
		$document = JFactory::getDocument();
		$format =  $document->getType('raw');
		$tmpl =JRequest::getCmd('tmpl', '');
		$print =JRequest::getCmd('print', 0);
		if ($app->isAdmin() || $print || $tmpl=='component' || $format!='html' ) { 
			return;
		}
		$get_id = '';
		JLoader::register('TwojToolBoxSiteHelper', JPATH_SITE.'/components/com_twojtoolbox/helpers/twojtoolboxsite.php');
		$cache = JFactory::getCache('twojtoolboxplugin', 'output');
		$cache_enable = $cache->getCaching();
		if($cache_enable) $get_id = JCache::makeId();
		
		$list_cache = '';

		if ($cache_enable && $list_cache = $cache->get($get_id.'js')) {
			$document->addScript($list_cache);
		} else {
			$this->getFileList('js', $cache_enable, $cache, $get_id);
		}
		$list_cache = '';
		if ($cache_enable && $list_cache = $cache->get($get_id.'css')) {
			$document->addStyleSheet($list_cache);
		} else {
			$this->getFileList('css', $cache_enable, $cache, $get_id);
		}
		
		$list_cache = '';
		if ($cache_enable && $list_cache = $cache->get($get_id.'less')) {
			$document->addStyleSheet($list_cache);
		} else {
			$this->getFileList('less', $cache_enable, $cache, $get_id);
		}
		
	}
}
