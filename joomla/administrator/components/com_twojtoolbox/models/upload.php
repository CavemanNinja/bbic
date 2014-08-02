<?php
/**
* @package     	2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   	Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.2 $
**/


defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modeladmin');

class TwojToolboxModelUpload extends JModelAdmin
{

	public function getForm($data = array(), $loadData = true){
		$app = JFactory::getApplication();
		$form = $this->loadForm('com_twojtoolbox.upload', 'upload', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		return $form;
	}

	protected function loadFormData(){
		$data = JFactory::getApplication()->getUserState('com_twojtoolbox.display.upload.data', array());
		return $data;
	}


	public function send(){
		$catid 	= TwojToolboxHelper::cgid( );
		$col = 0;
		$app	= JFactory::getApplication();
		$filename_up = JRequest::getVar('filename_up', null, 'files', 'array');
		$jform = JRequest::getVar('jform', array(), 'POST', 'array');
		$folderlist = $jform['folderlist'];
		$state = (int) $jform['state'];
		$language = $jform['language'];
		$folderlist = str_replace('\\', '/', $folderlist);
		$folderlist = str_replace('/', '/', $folderlist);
		$dir_upload = JPATH_ROOT.'/'."media".'/'."com_twojtoolbox".'/';
		if(JFolder::exists($dir_upload.$folderlist)) $dir_upload .= $folderlist.'/';
			else $folderlist = '';
		
		if (!(bool) ini_get('file_uploads') ) {
			$app->enqueueMessage(JText::_('COM_TWOJTOOLBOX_MSG_INSTALL_WARNINSTALLFILE'));
			return false;
		}
		if (!is_array($filename_up)) {
			$app->enqueueMessage(JText::_('COM_TWOJTOOLBOX_MSG_INSTALL_NO_FILE_SELECTED'));
			return false;
		}
		if ($filename_up['size'] < 1  ) {
			$app->enqueueMessage(JText::_('COM_TWOJTOOLBOX_MSG_INSTALL_WARNINSTALLUPLOADERROR'));
			return false;
		}
		
		if( isset($jform['foldernew']) && $foldernew = $jform['foldernew'] ){
			$foldernew = TwojToolboxHelper::pathCheck(JFolder::makeSafe($foldernew));
			if( JFolder::exists($dir_upload.$foldernew) || JFolder::create($dir_upload.$foldernew) ) {
				$folderlist.= ($folderlist?'/':'').$foldernew;
				$dir_upload.=$foldernew.'/'; 
			} else {
				$app->enqueueMessage(JText::_('COM_TWOJTOOLBOX_ERROR_CREATEFOLDER'), 'error');
			}
		}
		
		$filename = JRequest::getVar( 'filename', array(), 'post', 'array' );
		$desc = JRequest::getVar( 'filedesc', array(), 'post', 'array' );
		for($i=0;$i<count($filename_up['name']);$i++){
			if ( $filename_up['error'][$i]==0 && $filename_up['name'][$i] && in_array( strtolower(JFile::getExt($filename_up['name'][$i])), array('png', 'jpeg', 'jpg', 'gif') )  ){
				$uploadname = JFile::makeSafe($filename_up['name'][$i]) ;
				$image_name = $filename[$i]!='' ?  $filename[$i] :  JFile::makeSafe($filename_up['name'][$i]) ;
				if (JFile::exists( $dir_upload.$uploadname)){ 
					$k=1;
					$ext_only = JFile::getExt($uploadname);
					$name_only = JFile::stripExt($uploadname);
					$version_temp = $name_only.'('.$k.').'.$ext_only;
					while( JFile::exists( $dir_upload.$version_temp ) ){
						$version_temp = $name_only.'('.++$k.').'.$ext_only;
					}
					$uploadname = $version_temp;
				}
   				if( JFile::upload( $filename_up['tmp_name'][$i], $dir_upload.$uploadname) ){
					++$col;
					$row	= JTable::getInstance('Element', 'TwojToolboxTable');
					$row->load(0);
					$row->title = $image_name;
					$row->desc = isset($desc[$i])?$desc[$i]:'';
					$row->img = TwojToolboxHelper::pathCheck($folderlist.'/'.$uploadname);
					$row->language = $language;
					$row->state = $state;
					$row->catid = $catid;
					$row->check();
					$row->store();
				}
			}
		}
		$app->setUserState('com_twojtoolbox.display.elements.data', array());
		$app->enqueueMessage( $col.' '.JText::_('COM_TWOJTOOLBOX_UPLOAD_UPLOADIMAGES', count($col)),'notice');
		return true;
	}
	
}
