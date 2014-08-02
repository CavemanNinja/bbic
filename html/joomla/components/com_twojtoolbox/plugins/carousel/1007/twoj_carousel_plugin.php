<?php
/**
* @package 2JToolBox 2JCarousel
* @Copyright (C) 2012 2Joomla.net
* @ All rights reserved
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version $Revision: 1.0.7 $
**/

JLoader::register('TwojToolboxHelper', JPATH_ADMINISTRATOR.'/components/com_twojtoolbox/helpers/twojtoolbox.php');

defined('_JEXEC') or die;

class TwoJToolBoxCarousel extends TwoJToolBoxPlugin{
	
	protected $css_list=array('carousel', 'carouselbox');
	protected $js_list=array( 'carouselbox', 'carousel');
	protected $delete_color_char = 1;
	
	public function getElement(){
		$db	= JFactory::getDBO();
		$app = JFactory::getApplication();
	
		$return_text = '';
		$uniqueid = $this->getuniqueid();
		
		$this->addGenOption('id: '.$uniqueid);
		$this->addGenOption('base_url: \''.JURI::root().'\'');
		
		$java_import	= $this->getInt('java_import');
		$delay_start	= $this->getInt( 'delay_start' );
		
		$thumb_width 	= $this->getInt( 'thumb_width' );
		$thumb_height 	= $this->getInt( 'thumb_height' );
		$this->addGenOption('th_width: '.$thumb_width);
		$this->addGenOption('th_height: '.$thumb_height);
		
		$big_width 		= $this->getInt( 'big_width' );
		$big_height 	= $this->getInt( 'big_height' );
		
		$this->insertInt( 'speed' );
		$max_speed 		= $this->getInt( 'max_speed' );
		$this->addGenOption('max_speed: '.str_replace(',', '.', ( $max_speed/1000 ))  );
		
		$items			= $this->getInt( 'items' );
		$textBox 		= $this->getInt( 'textBox' );
		$items			= $this->getInt( 'items' );
		$moveY			= $this->getInt( 'moveY' );
		$centerX		= $this->getInt( 'centerX' );
		$centreXYvariant= $this->getInt( 'centreXYvariant' );

		$show_name		= $this->getInt( 'show_name' );
		$show_desc		= $this->getInt( 'show_desc' );
		
		
		$pretext = 		$this->getString( 'pretext');
		$moduletext = 	$this->getString( 'moduletext');
		$posttext = 	$this->getString( 'posttext');
	
		
		$block_width	= $this->getSize('block_width' );
		$block_height	= $this->getSize('block_height' );
		
		$load_icon		=  $this->getUrl( 'load_icon' );
		$load_text		=  $this->getString( 'load_text' );
		
		$this->insertSize( 'arr_top' );
		$this->insertSize( 'arr_left' );
		
		$style			= $this->getString( 'style' );
		
		$big_type_resizing	= $this->getInt( 'big_type_resizing');
		
		$big_resize_position	= $this->getInt( 'big_resize_position' );
		$thumb_resize_position	= $this->getInt( 'thumb_resize_position' );
		
		$big_color		= $this->getColor( 'big_color' );
		$thumb_color	= $this->getColor( 'thumb_color' );
	
		$twoj_type_img = $this->getString('typeimg');
		if(!$twoj_type_img) $twoj_type_img = $this->globalparams->get('typeimg', 'png');
		
		$ie7_options	= $this->getInt( 'ie7_options' );
		$bg_color_ie7	= $this->getColor( 'bg_color_ie7');
		$ie7_options_check = $insert_style_filter = 0;
		if( $twoj_type_img=='png' && $thumb_color=='transparent'  ){
			jimport( 'joomla.environment.browser' );
			$browser = JBrowser::getInstance();
       		if( $browser->getBrowser()=='msie' && $browser->getMajor()=='7' ){
				switch( $ie7_options ){
					case 0: $thumb_color=$bg_color_ie7; ; break;
					case 1: $twoj_type_img='gif'; break;
					case 2: $ie7_options_check = 2; break;
				}
			}
			if( $browser->getBrowser()=='msie' && ( ($browser->getMajor()!='7' && $browser->getMajor()=='8' )  || $ie7_options_check == 2  ) ){
				$insert_style_filter = 1;
				$trans_gif = $this->plugin_url.'css/x.gif';
			}
		}
	
		$this->addGenOption("type_file: '".$twoj_type_img."'");
		$this->addGenOption("thumb_trans: ".($thumb_color=='transparent'?0:1));
		$this->addGenOption("big_trans: ".($big_color=='transparent'?0:1));
		
		$this->addGenOption('ie7_options: '.$ie7_options_check);
		
		$temlate 		= '@@big_image_tag@@<br />' . ($show_name==0?'<br /><h3>@@name@@</h3>':'') . ($show_desc==0?'@@desc@@':'');
		$textBox		=  $this->getInt( 'textBox' ) ;
		if( $textBox == 2 ){
			$this->addGenOption(' textBox: 1');
			$temlate  = $this->getString( 'template' );
		} else {
			$this->addGenOption(' textBox: '.$textBox);
		}
		
		$enable_link		= $this->getInt( 'enable_link' );
		if($enable_link){
			$this->addGenOption(' textBox: 3');
			$temlate  = '@@link@@';
		}
		
		$this->insertInt( 'direct_move' );
		
		$this->insertString('twoj_button_style');
		$this->insertInt( 'perspective' );
		$this->insertInt( 'fadeEffect' );
		$this->insertInt( 'radiusX' );
		$this->insertInt( 'radiusY' );
		$this->insertInt( 'control' );
		$this->insertColor('overley_color1', null, 0 );

		$this->insertInt('box_size_w', null, 2 );
		$this->insertInt('box_size_h', null, 2 );
		$this->insertInt('box_minsize_w', null, 2 );
		$this->insertInt('box_minsize_h', null, 2 );
		
		if ( $this->getInt( 'speed_koef' ) ){
			$this->insertInt( 'speed_koef' );
			$this->insertInt( 'speed_ie6' );
			$this->insertInt( 'speed_ie7');
			$this->insertInt( 'speed_ie8' );
			$this->insertInt( 'speed_mozilla' );
			$this->insertInt( 'speed_opera' );
			$this->insertInt( 'speed_safari' );
			$this->insertInt( 'speed_chrome' );
		}
		
		

		$query = $db->getQuery(true);
		$query->select('a.*');
		$query->from('#__twojtoolbox_elements AS a');
		$query->where('a.catid = '.$this->id.' AND a.state = 1');
		switch($this->getInt( 'orderby' )){
			case 6: $query->order('a.id DESC'); 		break;
			case 5: $query->order('a.id ASC');			break;
			case 4: $query->order('RAND()');			break;
			case 3: $query->order('a.title DESC');		break;
			case 2: $query->order('a.title ASC');		break;
			case 1: $query->order('a.ordering DESC');	break;
			case 0: 
			default:$query->order('a.ordering ASC');
		}
		$db->setQuery( (string) $query, 0, $items );
		$rows = $db->loadObjectList();
		
		$generet_big_img_url 	= $this->getUrlResize('big_');
		$generet_thumb_img_url 	= $this->getUrlResize('thumb_');
		
		$array_tag = array('@@big_image_tag@@', '@@big_image_url@@', '@@thumb_tag@@', '@@thumb_url@@', '@@name@@', '@@desc@@', '@@link@@');
		if ( count( $rows ) ){
			$image_array = array(); 
			$image_array_java = array(); 
			
			$descr_array = array(); 
			$descr_array_java = array();
			
			$fullimage_array = array(); 
			$fullimage_array_java = array();
			foreach ($rows as $row){
				$row->name = str_replace('&', '&amp;', $row->title);
				$name_enc = str_replace('"', '&quot;', $row->title);
				
				
				$big_img_url = $generet_big_img_url.'&ems_file='.TwojToolboxHelper::path_twojcode($row->img);
				if( $url_link = TwojToolBoxSiteHelper::imageResizeSave($big_img_url ) ) $big_img_url = $url_link;
				else  $big_img_url = str_replace( '&', '&amp;', $big_img_url );

				$thumb_img_url = $generet_thumb_img_url.'&ems_file='.TwojToolboxHelper::path_twojcode($row->img);
				if( $url_link = TwojToolBoxSiteHelper::imageResizeSave($thumb_img_url ) ) $thumb_img_url = $url_link;
				else  $thumb_img_url = str_replace( '&', '&amp;', $thumb_img_url );
				
				
				$array_tag_replace = array(
					'<img src="'.$big_img_url.'"   alt="'.$name_enc.'" title="'.$name_enc.'"  '.( !$big_type_resizing ? 'width="'.$big_width.'" height="'.$big_height.'"' : '' ).' class="nyroModalClose" />', 
					$big_img_url, 
					'<img src="'.$thumb_img_url.'" alt="'.$name_enc.'" title="'.$name_enc.'"  width="'.$thumb_width.'" height="'.$thumb_height.'" />', 
					$thumb_img_url, 
					$row->name,
					$row->desc, 
					$row->link
				);
				$text = str_replace( $array_tag, $array_tag_replace, $temlate) ;
				
				$width = '425';	$height  = '350';$matches = array();
				$regex		= '/{2JYouTube\s+(.*?)}/i';
				preg_match_all($regex, $text, $matches, PREG_SET_ORDER);
				if ($matches){
					foreach ($matches as $match) {
						$matchArray = explode(',', trim($match[1]));
						if(count($matchArray)>=1){
							$vid = $matchArray[0];
						}
						if(count($matchArray)>=2){
							$width = (int)$matchArray[1];
						}
						if(count($matchArray)>=3){
							$height = (int)$matchArray[2];
						}
						$output = "<object class=\"embed\" width=\"" . $width . "\" height=\"" . $height . "\" type=\"application/x-shockwave-flash\" data=\"http://www.youtube.com/v/".$vid."?fs=1\"><param name=\"movie\" value=\"http://www.youtube.com/v/".$vid."?fs=1\" /><param name=\"allowFullScreen\" value=\"true\"></param><param name=\"wmode\" value=\"transparent\"><em>You need to a flashplayer enabled browser to view this YouTube video</em></object>";
						$text = preg_replace("|$match[0]|", $output, $text, 1);
					}
				}
				
				if( $java_import && $this->render_content == 0 )  $image_array_java[] = $thumb_img_url.'||'.$name_enc.'||'.$thumb_width.'||'.$thumb_height;
					else $image_array[] 		= '<img '.( $this->render_content==0 ?'style="display: none;'.($insert_style_filter ? "filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='".$thumb_img_url."', sizingMethod='scale');" : '').'"':'').' class="twoj_thimage'.($enable_link && $row->link_blank ?' twoj_target_new' : '').'" src="'.( $insert_style_filter ? $trans_gif : $thumb_img_url ).'" title="'.$name_enc.'" alt="'.$name_enc.'" width="'.$thumb_width.'" height="'.$thumb_height.'" />';
				if($this->render_content == 0 ){ 
					if( $java_import) $descr_array_java[] = str_replace(array('"', "\r","\n") , array('\"', '', ''), $text);
						else	$descr_array[] 		= '<div style="display: none;" class="twoj_carouseltext">'.$text.'</div>';
					if( $java_import) $fullimage_array_java[] = $big_img_url.'||'.$name_enc.'||'.$big_width.'||'.$big_height;
						else 	$fullimage_array[] 	= '<img style="display: none;" class="twoj_bigimage" src="'.$big_img_url.'" title="'.$name_enc.'" alt="'.$name_enc.'" width="'.$big_width.'" height="'.$big_height.'" />';
				}
			}
			if( $this->render_content == 0){
				$return_text .= $pretext;
				$return_text .= '<div id="twoj_carousel_'.$uniqueid.'" class="twoj_carousel_all '.$style.'" style="'.($block_width?'width: '.$block_width.';':'').($block_height?'height: '.$block_height.';':'').'">'.($load_text || $load_icon?'<span class="twoj_loadingobject_'.$uniqueid.'">'.($load_icon?'<img src="'.$load_icon.'" alt="'.JText::_($load_text).'" border="0" /> ':'').$load_text.'</span>':'').$moduletext.'</div>';
				$return_text .= $posttext;
				$return_text .= "\n".'<div id="twoj_carousel_holder_images'.$uniqueid.'" class="'.$style.'">';
			}
			
			$return_text .= implode('', $image_array).' ';
			$return_text .= implode('', $fullimage_array).' ';
			$return_text .= implode('', $descr_array);
			
			if( $this->render_content == 0) {
				$return_text .= '</div>';
				$java_funct = '';
				
				if ( $java_import ){
					$java_funct .= ' var img_array'.$uniqueid.' 	= new Array( "'.implode('", "', $image_array_java).'"); ';
					$java_funct .= ' var big_img_array'.$uniqueid.' = new Array( "'.implode('", "', $fullimage_array_java).'"); ';
					$java_funct .= ' var descr_array'.$uniqueid.' 	= new Array( "'.implode('", "', $descr_array_java).'"); ';
					$this->addGenOption('array_small_images: img_array'.$uniqueid);
					$this->addGenOption('array_big_images: big_img_array'.$uniqueid);
					$this->addGenOption('array_descr_images: descr_array'.$uniqueid);
					$this->addGenOption('java_import: 1');
					
				}

               switch($centreXYvariant){
                case 2:
                        $java_funct .= ' var twoj_top 		  = '.$moveY.'; ';
        				$java_funct .= ' var twoj_center_x 	= '.$centerX.'; ';
                        break;
                case 1:
                        $java_funct .= ' var twoj_top 		  = Math.round(emsajax("#twoj_carousel_'.$uniqueid.'").offset().top-250 + '.$moveY.'); ';
        				$java_funct .= ' if( emsajax("#twoj_carousel_'.$uniqueid.'").offsetParent().css("position")== "relative"  ) var left_of_parent = emsajax("#twoj_carousel_'.$uniqueid.'").position().left; else var left_of_parent = emsajax("#twoj_carousel_'.$uniqueid.'").offset().left; ';
                        $java_funct .= ' var twoj_center_x 	= Math.round( left_of_parent + '.$centerX.'); ';
                        break;
                case 0:
                default:
                    $java_funct .= 'var twoj_top =  Math.round(emsajax("#twoj_carousel_'.$uniqueid.'").position().top+(emsajax("#twoj_carousel_'.$uniqueid.'").outerHeight(true)/2 )-'.($this->getInt( 'radiusY' )).'+'.$moveY.');';
					$java_funct .= 'var twoj_center_x 	= Math.round( emsajax("#twoj_carousel_'.$uniqueid.'").position().left + emsajax("#twoj_carousel_'.$uniqueid.'").outerWidth(true)/2  + '.$centerX.'); ';
                } //'.$centerX.'
				$java_funct .= ' var centerX_cyr_v = Math.round( emsajax("#twoj_carousel_'.$uniqueid.'").offset().left+ emsajax("#twoj_carousel_'.$uniqueid.'").outerWidth(true)/2 ) ; ';
                $java_funct .= ' emsajax("#twoj_carousel_'.$uniqueid.'").twoj_carousel({ centerX_user: '.$centerX.', centerXmove: centerX_cyr_v, centerX: twoj_center_x, moveY: twoj_top, '.implode(' ,', $this->gen_option).' });  ';

				$return_text .= '<script language="JavaScript" type="text/javascript"><!--//<![CDATA['."\n";
				if( $delay_start > 0 ){
					$return_text .= 'function twoj_start_carousel'.$uniqueid.'(){'.$java_funct.'} emsajax(document).ready(function(){	setTimeout( \'twoj_start_carousel'.$uniqueid.'()\', '.$delay_start.'); });';
				} else {
					$return_text .= 'emsajax(document).ready(function(){ '.$java_funct.' });';
				}
				$return_text .= "\n".'//]]>--></script>';
			}
		}
		if($return_text) return  $return_text; else return null;
	}
}
