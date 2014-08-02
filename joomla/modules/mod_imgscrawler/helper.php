<?php
/*------------------------------------------------------------------------
# mod_imgscrawler - Images Crawler
# ------------------------------------------------------------------------
# author    Joomla!Vargas
# copyright Copyright (C) 2010 joomlahill.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomlahill.com
# Technical Support:  Forum - http://www.joomlahill.com/forum
-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

jimport('joomla.filesystem.file');

abstract class modImgsCrawlerHelper
{

	public static function getImages(&$params, $folder)
	{
		$images	= array();
		
		$links     = $params->get( 'links' );
		$target    = $params->get( 'target', '_self' );
		$repeat    = $params->get( 'repeat', 0 );
		$margin    = intval($params->get( 'margin', 3 ));
		$direction = $params->get( 'direction', 'left' );
		$substoo   = $params->get( 'substoo', 0 );
		$random    = $params->get( 'random', 0 );
		$iwidth    = $params->get( 'iwidth' );
		$iheight   = $params->get( 'iheight' );
		$limit     = $params->get( 'limit' );
		
		if ($links)
			$links  = preg_split("/[\n\r]+/", $links);
			
		$style = 'vertical-align:'.$params->get('valign','top').';';
		
		if ($margin) :
			switch ( $direction ) {
				case 'left';
					$style .= 'margin-right:'.$margin.'px';
					break;
				case 'right';
					$style .= 'margin-left:'.$margin.'px';
					break;
			}
		endif;

		// check if directory exists
		if (is_dir(JPATH_BASE.DIRECTORY_SEPARATOR.$folder))
		{
			$files = modImgsCrawlerHelper::getFiles($folder,$substoo,$random);
			$i = 0;
			$repeated = 0;
			foreach ($files as $file)
			{
				if (modImgsCrawlerHelper::isImage($file)) {
					$file	    = str_replace( '\\', '/', $file );
					$attribs    = array();
					$attribs['style']  = $style;
					if ($iwidth) $attribs['width'] = intval($iwidth);
					if ($iheight) $attribs['height'] = intval($iheight);
					$images[$i] = JHTML::_('image', $file, substr($file,strrpos($file,'/')+1), $attribs);
					if ((isset($links[$i]) && $links[$i]) or $repeat) :
						if (isset($links[$i]) && $links[$i]) {
							$images[$i] = JHTML::_('link', trim($links[$i]), $images[$i], ($target ? array('target' => '_blank') : '' ) );
						} else {
							$repeated++;
							$images[$i] = JHTML::_('link', trim($links[$repeated-1]), $images[$i], ($target ? array('target' => '_blank') : '' ) );
							if ($repeated == count($links)) $repeated = 0 ;
						}
					endif;
					++$i;
					if ($limit == $i) break;
				}
			}
		}

		if ($random) :
                  shuffle($images);
		endif;

		return $images;
	}

	public static function getFolder(&$params)
	{
		$folder 	= $params->get( 'folder' );
		
		// Remove the trailing slash from the url (if any)
		if (substr($folder, -1) == '/') {
			$folder = substr($folder, 0 ,-1);
		}

		$LiveSite 	= JURI::base();

		// if folder includes livesite info, remove
		if ( JString::strpos($folder, $LiveSite) === 0 ) {
			$folder = str_replace( $LiveSite, '', $folder );
		}
		// if folder includes absolute path, remove
		if ( JString::strpos($folder, JPATH_SITE) === 0 ) {
			$folder= str_replace( JPATH_BASE, '', $folder );
		}
		$folder = str_replace('\\',DIRECTORY_SEPARATOR,$folder);
		$folder = str_replace('/',DIRECTORY_SEPARATOR,$folder);

		return $folder;
	}
	
	public static function getFiles($folder,$substoo,$random) {
	
		$dir      = JPATH_BASE.DIRECTORY_SEPARATOR.$folder;
			
		$files	  = array();
		$subfiles = array();
	
		if ($handle = opendir($dir)) {
			while (false !== ($file = readdir($handle))) {
				if ($file != '.' && $file != '..' && $file != 'CVS' && $file != 'index.html' ) {
					if (!is_dir($dir . DIRECTORY_SEPARATOR . $file))
					{
						$files[] = $folder . DIRECTORY_SEPARATOR . $file;
					} elseif ($substoo != 0) {
						$newfolder = $folder . DIRECTORY_SEPARATOR . $file;
						$subfiles[]  = modImgsCrawlerHelper::getFiles($newfolder,$substoo,$random);
					}
				}
			}
		}
		closedir($handle);
		
		sort($files);
		
		foreach ($subfiles as $subfile) :
			$files = array_merge($files,$subfile);
		endforeach;
		
		return $files;
	}
		
    public static function isImage($file) {
		$file  = JFile::getName($file);
		$file  = JFile::getExt($file);
		$file  = strtolower($file);
        $types = array("jpg", "jpeg", "gif", "png");
        if (in_array($file, $types)) return true;
        else return false;
    }
}
