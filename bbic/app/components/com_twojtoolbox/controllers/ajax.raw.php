<?php
/**
* @package 2JToolBox
* @Copyright (C) 2011 2Joomla.net
* @ All rights reserved
* @ Joomla! is Free Software
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version $Revision: 1.0.10 $
**/


defined('_JEXEC') or die('Restricted access');


class TwojToolboxControllerAjax extends TwojController{
	
	public function display($cachable = false, $urlparams = false){	
		JFactory::getApplication()->close('');
	}
	
	public function callback(){
		$document = JFactory::getDocument();
		$document->setType('raw');
		if( $plugin_id = JRequest::getInt('plugin_id', 0) ){
			echo TwojToolBoxSiteHelper::PluginÑallback( $plugin_id );
		} else {
			echo JText::_('2jToolBox::Plugin callback error!');
		}
		JFactory::getApplication()->close('');
	}
	
	public function getcss(){
		header('Content-type: text/css');
		$document = JFactory::getDocument();
		$document->setType('raw');
		$document->setMimeEncoding('text/css');
		$need_files = JRequest::getString('need', '');
		TwojToolBoxSiteHelper::scriptSave( $need_files, 'css');
		die();
	}
	
	public function getjs(){
		header('Content-type: text/javascript');
		$document = JFactory::getDocument();
		$document->setType('raw');
		$document->setMimeEncoding('text/javascript');
		$need_files = JRequest::getString('need', '');
		TwojToolBoxSiteHelper::scriptSave( $need_files, 'js');
		die();
	}
	
	public function getless(){
		header('Content-type: text/css');
		$document = JFactory::getDocument();
		$document->setType('raw');
		$document->setMimeEncoding('text/css');
		$need_files = JRequest::getString('need', '');
		TwojToolBoxSiteHelper::scriptSave( $need_files, 'less');
		die();
	}
	function twojtoolbox_image_resize( ){
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		
		$id	= 	JRequest::getInt('id', 0);
		$ems_cache	= 	JRequest::getInt('ems_cache', 1);
		$max_width 	= 	JRequest::getInt('ems_max_width', 120);
		$max_height = 	JRequest::getInt('ems_max_height', 120);
		$bg			= 	JRequest::getString('ems_bg', 'transparent');

		$type_img	= 	JRequest::getString('ems_type_img', 'png');
		$type_res	= 	JRequest::getInt('ems_type_res', 0);
		$position	= 	JRequest::getInt('ems_position', 0);
	
		$ems_root	= 	JRequest::getInt('ems_root', 0);
		$debug 		= JRequest::getInt('ems_debug', 0);
		
		$crop = 0;
		if($type_res==2){
			$crop = 1;
			$type_res = 0;
		}
		if($type_res) $position = 0;
		
		if( $type_img!='png' && $type_img!='gif' ) $type_img = 'jpg';
		if($max_width==0) 	$max_width = 120;
		if($max_height==0) 	$max_height = 108;

		if( $max_width  < 2000) $max_width  = $max_width; 
		if( $max_height < 1000) $max_height = $max_height; 
	
		if( !$id ) {
			$image_filename_in	= JRequest::getString('ems_file', '');
			$image_filename = TwojToolboxHelper::path_twojcode($image_filename_in, 1);
		} else {
			$database =  JFactory::getDbo();
			$database->setQuery( "SELECT img FROM #__twojtoolbox_elements WHERE id = ".$id );
			$image_filename = $database->loadResult();
		}
	
		if( !$image_filename ){
			JText::_('input parametr error');
			return ;
		}
		$image_filename = str_replace('\\', '/', $image_filename);
		$image_filename = str_replace('/', '/', $image_filename);
	
		if($ems_root) $image = JPATH_SITE.'/'.$image_filename;
			else $image = JPATH_SITE.'/media/com_twojtoolbox/'.$image_filename;
	
		if( !JFile::exists( $image ) ){
			JText::_('2JToolBox::file '.$image_filename.' read error');
			return;
		}
	
		$file_size = ( function_exists('md5_file') ? md5_file($image) : filesize($image) );
		
		$cacheFodler = JPATH_CACHE.'/twojtoolbox/';
		if(JComponentHelper::getParams('com_twojtoolbox')->get('twojcachefolder', 0)){
			$cacheFodler = JPATH_ROOT.'/media/com_twojtoolbox/cache/';
		}
		
		if( !JFolder::exists($cacheFodler)) JFolder::create($cacheFodler);
		
		$resized = $cacheFodler
		.($id==0?str_replace(array('.', '(', ')'), '', $image_filename_in):$id)
		.'_size'.$max_width.'x'.$max_height
		.'_bg'.$bg
		.'_fs'.$file_size 
		.'_tr'.($crop?2:$type_res)
		.'_p'.$position
		.'.'
		.$type_img;
		
	
		$imageModified = @filemtime($image);
		$thumbModified = @filemtime($resized);
	
		$document = JFactory::getDocument();
		$document->setType('raw');
		
		if(!$debug){
			switch ($type_img) { 
				case 'gif': $document->setMimeEncoding('image/gif');header("Content-type: image/gif"); break;
				case 'png': $document->setMimeEncoding('image/png');header("Content-type: image/png"); break;
				case 'jpg': 
				default: 	$document->setMimeEncoding('image/jpeg');header("Content-type: image/jpeg");
			}
		}
		
		if($imageModified<$thumbModified && $ems_cache ) {
			header("Last-Modified: ".gmdate("D, d M Y H:i:s",$thumbModified)." GMT");
			readfile($resized);
			return ;
		}
		$ext = strtolower(substr(strrchr($image, '.'), 1));
		switch ($ext) { 
			case 'jpg':     // jpg
				$src = imagecreatefromjpeg($image) or notfound();
				break;
			case 'png':     // png
				$src = imagecreatefrompng($image) or notfound();
				break;
			case 'gif':     // gif
				$src = imagecreatefromgif($image) or notfound();
				break;
			default:
				JText::_('2JToolBox::Error - check GD version');
				return ;
		}
		
		$size = getimagesize($image);
	
		$width = $size[0];
		$height = $size[1];
	
		$x_ratio = $max_width / $width;
		$y_ratio = $max_height / $height;
		$xy_ratio = $width / $height;
		
		if( ($width <= $max_width) && ($height <= $max_height) ) {
			$tn_width = $width;
			$tn_height = $height;
		} else if( ($x_ratio * $height) < $max_height ) {
			$tn_height = ceil($x_ratio * $height);
			$tn_width = $max_width;
		} else {
			$tn_width = ceil($y_ratio * $width);
			$tn_height = $max_height;
		}

		
		if( $type_res )
			$dst = imagecreatetruecolor($tn_width,$tn_height);
		 else 
			$dst = imagecreatetruecolor($max_width,$max_height);
		
		if($crop){
			if( $tn_width < $max_width ){
				$k_uv = $max_width - $tn_width;
				$tn_width +=  $k_uv;
				$tn_height =  $tn_width / $xy_ratio;
			}
			if( $tn_height < $max_height ){
				$k_uv = $max_height - $tn_height;
				$tn_height += $k_uv;
				$tn_width = $xy_ratio * $tn_height;
			}
		}
		
		if($debug){
			echo '$xy_ratio'.$xy_ratio."<br />";
			echo '$tn_width'.$tn_width."<br />";
			echo '$tn_height'.$tn_height."<br />";
			echo '$max_width'.$max_width."<br />";
			echo '$max_height'.$max_height."<br />";
			echo '$width'.$width."<br />";
			echo '$height'.$height."<br />";
			die();
		}
		
		if (function_exists('imageantialias')) imageantialias ($dst, true);
		if( $bg!='transparent' ) {
			$bg = $this->html2rgb($bg);
			$color =  imagecolorallocate ($dst, $bg[0] , $bg[1], $bg[2]);
		} else {
			if($type_img!='png'){
				imagealphablending($dst, false);
				imagesavealpha($dst,true);
				$color = imagecolortransparent($src);
				if($color >= 0) {
					$transcol = imagecolorsforindex($src, $color);
					$color = imagecolorallocatealpha($dst, $transcol['red'], $transcol['green'], $transcol['blue'], 127);
				} else $color = imagecolorallocatealpha($dst, 0, 0, 0, 127);
			} else {
				imagealphablending($dst, false);
				imagesavealpha($dst, true);
				$color = imagecolorallocatealpha($dst, 0, 0, 0, 127);
			}
		}

		imagefill($dst, 0, 0, $color);
		
		if(!$crop){
			if( $position!=1 && $type_res!=1 ) $copy_x =  ( ($max_width - $tn_width) / ($position==2?1:2) ); else $copy_x = 0;
			if( $position!=3 && $type_res!=1 ) $copy_y =  ( ($max_height - $tn_height) / ($position==4?1:2)); else $copy_y = 0;
		} else {
			if( $max_width <  $tn_width  && !$type_res && $position!=1 ) $copy_x =  ( ($max_width - $tn_width) / ($position==2?1:2) ); else $copy_x = 0;
			if( $max_height <  $tn_height && !$type_res && $position!=3 ) $copy_y =  ( ($max_height - $tn_height) / ($position==4?1:2)); else $copy_y = 0;
		}
		
		ImageCopyResampled ($dst, $src, $copy_x, $copy_y, 0, 0, $tn_width, $tn_height, $width, $height);
		
		if( $bg=='transparent' && $type_img!='png'){
			if($color >= 0) {
				imagecolortransparent($dst, $color);
				for($y=0; $y<($type_res?$tn_height:$max_height); ++$y)
					for($x=0; $x<($type_res?$tn_width:$max_width); ++$x)
						if(((imagecolorat($dst, $x, $y)>>24) & 0x7F) >= 100) imagesetpixel($dst, $x, $y, $color);
			}
		}
		//ob_start();
		if ($type_img=='png'){
			imagepng($dst, null, 9);
			if($ems_cache) imagepng($dst, $resized, 9);
		}else  if ($type_img=='gif'){
			imagetruecolortopalette($dst, true, 256);
			imagesavealpha($dst, false);
			imagegif($dst);
			if($ems_cache) imagegif($dst, $resized);
		}else {
			imagejpeg($dst, null, 90);
			if($ems_cache)imagejpeg($dst, $resized, 90);
		}
/* 		$output = ob_get_contents();
		ob_end_clean();
		echo $output;
		if($ems_cache) JFile::write( $resized , $output );
		 */ 
		imagedestroy($src);
		imagedestroy($dst);
		die();
}

	function html2rgb($color){
		if (strlen($color) == 6)
			list($r, $g, $b) = array($color[0].$color[1], $color[2].$color[3], $color[4].$color[5]);
		else
			return array(255, 255, 255);
		$r = hexdec($r); $g = hexdec($g); $b = hexdec($b);
		return array($r, $g, $b);
	}
	
}
