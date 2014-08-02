<?php
/**
* @package 2JToolBox 2J Simple SlideShow
* @Copyright (C) 2012 2Joomla.net
* @ All rights reserved
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version $Revision: 1.0.2 $
**/

defined('_JEXEC') or die;

class TwoJToolBoxSimpleSlideshow extends TwoJToolBoxPlugin{
	
	protected $javaScriptStyles = array();
	protected $uniqueId = 0;

	
	protected $css_list=array( 'simpleslideshow' );
	protected $js_list=array( 'simpleslideshow');
	

	public function getElement(){
		$db			= JFactory::getDBO();
		$app 		= JFactory::getApplication();
		
		$return_text = '';
		
		$this->uniqueId = $this->getuniqueid();
		
		$ulStyle = '';
		
		
		$generet_big_img_url 	= $this->getUrlResize('big_');
		$generet_thumb_img_url 	= $this->getUrlResize('thumb_');
		
		if( $this->id == -1 ){
			$rows = $this->loadDemo();
			$generet_big_img_url .= '&ems_root=1';
			$generet_thumb_img_url .= '&ems_root=1';
		} else {
			$query= $db->getQuery(true);
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
			$db->setQuery( (string) $query );
			$rows = $db->loadObjectList();
		}
		
		 
		 $caption =  $this->getInt('caption');

		if ( count( $rows ) ){
			$image_listing = '';			
			foreach ($rows as $row){

				$row->title = str_replace('&', '&amp;', $row->title);
				
				$imageEl = '';
				
				if( $this->getInt('big_type_resizing')==3 ){				
					$big_img_url = JURI::root().($this->id == -1 ? '' : 'media/com_twojtoolbox/' ).$row->img;
				} else {
					$big_img_url = $generet_big_img_url.'&ems_file='.TwojToolboxHelper::path_twojcode($row->img);
					if( $url_link = TwojToolBoxSiteHelper::imageResizeSave($big_img_url ) ) $big_img_url = $url_link;
						else  $big_img_url = str_replace( '&', '&amp;', $big_img_url );
				}
	
				
				$thumb_img_url = $generet_thumb_img_url.'&ems_file='.TwojToolboxHelper::path_twojcode($row->img);
				if( $url_link = TwojToolBoxSiteHelper::imageResizeSave($thumb_img_url ) ) $thumb_img_url = $url_link;
					else  $thumb_img_url = str_replace( '&', '&amp;', $thumb_img_url );
				
				$cleatTitle = htmlspecialchars($row->title);
				$captionText = $caption ? 'data-caption="'.$cleatTitle.'" ' :'';
				
				if( $row->link_blank==3 || $row->link_blank==4 ){
					$imageEl = 	'<div '
									.$captionText
									.'data-thumb="'.$thumb_img_url.'" '
									.'data-thumbratio="'.$this->getInt('thumb_width').'/'.$this->getInt('thumb_height').'" '
									.($row->link_blank==4?' data-img="'.$big_img_url.'"':'')
									.'>'.$row->desc
								.'</div>';
				}else if( $row->link && ( $row->link_blank==1 || $row->link_blank==2) ){
					$imageEl = '<a href="'.$row->link.'" ' .$captionText.' data-thumbratio="'.$this->getInt('thumb_width').'/'.$this->getInt('thumb_height').'" ';
						//if( $row->link_blank==1 ) $imageEl .= 'data-thumb="'.$thumb_img_url.'" ';
						if( $row->link_blank==2 ) $imageEl .= 'data-img="'.$big_img_url.'" ';
						$imageEl .= '>';
						if( $row->link_blank==2 ) $imageEl .= '<img src="'.$thumb_img_url.'" alt="'.$cleatTitle.'">';
					$imageEl .='</a>';
				} else {
					$imageEl = 	
					'<a href="'.$big_img_url.'" '.$captionText.'>'
						.($thumb_img_url?'<img src="'.$thumb_img_url.'"  alt="'.$cleatTitle.'" width="'.$this->getInt('thumb_width').'" height="'.$this->getInt('thumb_height').'">':'')
					.'</a>';
				}
				
				$image_listing .= $imageEl; 
			}
			$ulStyle .= ' data-nav="'.$this->getString('navType').'"';
			
			if( $this->getInt('navigationPosition') ) $ulStyle .= ' data-navposition="top"';
			
			if( $customWidthVal = $this->getJSONValue( 'size', 'sizeSet', 'customWidthVal' ) ){
				$ulStyle .= ' data-width="'.(int) $customWidthVal.($this->getJSONValue( 'size', 'sizeSet', 'customWidthType' )=='%'?'%':'').'"';
			}
			
			if( $customHeightVal = $this->getJSONValue( 'size', 'sizeSet', 'customHeightVal' ) ){
				$ulStyle .= ' data-height="'.(int) $customHeightVal.($this->getJSONValue( 'size', 'sizeSet', 'customHeightType' )=='%'?'%':'').'"';
			}
			
			if( $minSizeWidth = $this->getJSONValue( 'minSize', 'minSizeSet', 'minSizeWidth' ) ){
				$ulStyle .= ' data-minwidth="'.(int) $minSizeWidth.($this->getJSONValue( 'minSize', 'minSizeSet', 'minSizeWidthType' )=='%'?'%':'').'"';
			}
			
			if( $minSizeHeight = $this->getJSONValue( 'minSize', 'minSizeSet', 'minSizeHeight' ) ){
				$ulStyle .= ' data-minheight="'.(int) $minSizeHeight.($this->getJSONValue( 'minSize', 'minSizeSet', 'minSizeHeightType' )=='%'?'%':'').'"';
			}
			
			if( $maxSizeWidth = $this->getJSONValue( 'maxSize', 'maxSizeSet', 'maxSizeWidth' ) ){
				$ulStyle .= ' data-maxwidth="'.(int) $maxSizeWidth.($this->getJSONValue( 'maxSize', 'maxSizeSet', 'maxSizeWidthType' )=='%'?'%':'').'"';
			}
			
			if( $maxSizeHeight = $this->getJSONValue( 'maxSize', 'maxSizeSet', 'maxSizeHeight' ) ){
				$ulStyle .= ' data-maxheight="'.(int) $maxSizeHeight.($this->getJSONValue( 'maxSize', 'maxSizeSet', 'maxSizeHeightType' )=='%'?'%':'').'"';
			}
			
			if( $galleryRatio = $this->getString('galleryRatio') ) $ulStyle .= ' data-ratio="'.$galleryRatio.'"';
			
			$ulStyle .= ' data-thumbwidth="'.$this->getInt('thumb_width').'"';
			$ulStyle .= ' data-thumbheight ="'.$this->getInt('thumb_height').'"';
			
	
			$ulStyle .= ' data-allowfullscreen="'.$this->getString('fullScreen').'"';
			$ulStyle .= ' data-fit="'.$this->getString('fit').'"';
			$ulStyle .= ' data-transition="'.$this->getString('transition').'"';
			$ulStyle .= ' data-transitionduration="'.$this->getInt('transitionduration').'"';
			
			
			$ulStyle .= ' data-loop="'.$this->getString('loop').'"';
			
			if( $this->getJSONValue( 'autoplay' )=='enabledAutoplay'){
				$ulStyle .= ' data-autoplay="'.$this->getJSONValue( 'autoplay', 'enabledAutoplay', 'valAutoplay' ).'"';
				if( $this->getJSONValue( 'autoplay', 'enabledAutoplay', 'stopAutoplay' ) ) $ulStyle .= ' data-stopautoplayontouch="true"';
			}
	
			$ulStyle .= ' data-margin="'.$this->getInt('margin').'"';
			$ulStyle .= ' data-thumbmargin="'.$this->getInt('thumbmargin').'"';
			
			if( $this->getInt('keyboard') ) $ulStyle .= ' data-keyboard="true"';
			
			if( $this->getJSONValue( 'control', 'controlSet', 'controlArrows' )=='true') $ulStyle .= ' data-arrows="true"';
			if( $this->getJSONValue( 'control', 'controlSet', 'controlClick' )=='true') $ulStyle .= ' data-click="true"';
			if( $this->getJSONValue( 'control', 'controlSet', 'controlSwipe' )=='true') $ulStyle .= ' data-swipe="true" data-trackpad="true"';
			
			if( $this->getInt('rtl') ) $ulStyle .= ' data-direction="rtl"';
			
			if( $startindex = $this->getInt('startindex') ) $ulStyle .= ' data-startindex="'.($startindex-1).'"';
			
			if( $border = $this->getStyleFromJSON('thumbborder') ){
				$ulStyle .= ' data-thumbborderwidth="'.$border['width'].'"';
			} else  $ulStyle .= ' data-thumbborderwidth="0"';
			
			$return_text .= '<div id="twoj_simpleslideshow'.$this->uniqueId.'" class="twoj_simpleslideshow" data-auto="false" ' .$ulStyle .'>'.$image_listing.'</div><div style="clear: both;"></div>';
			
			$this->compileJavaScriptStyles();
		
			$this->javascript_code .= 'emsajax("head").append("<style '.($this->id==-1?'id=\'dynamic_css\'':'').' type=\'text/css\'>'.implode('\n ', $this->javaScriptStyles).'</style>");';
			$this->javascript_code .= 'emsajax(function(){';
			$this->javascript_code .= 'emsajax( "#twoj_simpleslideshow'.$this->uniqueId.'" ).twoj_simpleslideshow({ });';
			$this->javascript_code .= ' });'; 

			
			if( $this->render_content == 0) {
				$return_text .= '<script type="text/javascript">'."\n".'<!--//<![CDATA['."\n".$this->javascript_code."\n".'//]]>-->'."\n".'</script>';
			}
		}
		if($return_text) return  $return_text; else return null;
	}
	
	
	protected function compileJavaScriptStyles(){
		if( $this->getJSONValue('galleryBgColor') =='enabledBgColor'){
				$this->javaScriptStyles[] = '#twoj_simpleslideshow'.$this->uniqueId.' .twoj_simpleslideshow__stage{ '
					.'background-color:'.$this->getJSONValue( 'galleryBgColor',		'enabledBgColor', 'bgImages' ).'; '
				.'}';
				$this->javaScriptStyles[] = '#twoj_simpleslideshow'.$this->uniqueId.' .twoj_simpleslideshow__nav__shaft{ '
					.'background-color: '.$this->getJSONValue( 'galleryBgColor', 		'enabledBgColor', 'bgNav' ).'; '
				.'}';
		}
		
		$galleryPaddingStyle = '';
		if($this->getJSONValue('galleryPadding') =='enabledAlignPadding'){
			switch( $this->getJSONValue( 'galleryPadding', 'enabledAlignPadding', 'alignPaddingValue' ) ){
				case 'left':  	$galleryPaddingStyle = 'float: left;'; 		break;
				case 'right':  	$galleryPaddingStyle = 'float: right;'; 	break;
				case 'center':  $galleryPaddingStyle = 'margin: 0 auto;'; 	break;
			}
			if( $galleryPaddingStyle ) $this->javaScriptStyles[] = '#twoj_simpleslideshow'.$this->uniqueId.'>.twoj_simpleslideshow__wrap{'.$galleryPaddingStyle.'}';
		}
		
		$galleryPaddingStyle = '';
		if($this->getJSONValue('galleryPadding') =='enabledCustomPadding'){
			if( $valPadding = $this->getJSONValue( 'galleryPadding', 'enabledCustomPadding', 'alignPaddingLeft' ) ) 	$galleryPaddingStyle .= 'margin-left:'	.$valPadding.'px;';
			if( $valPadding = $this->getJSONValue( 'galleryPadding', 'enabledCustomPadding', 'alignPaddingRight' ) ) 	$galleryPaddingStyle .= 'margin-right:'	.$valPadding.'px;';
			if( $valPadding = $this->getJSONValue( 'galleryPadding', 'enabledCustomPadding', 'alignPaddingTop' ) ) 		$galleryPaddingStyle .= 'margin-top:'	.$valPadding.'px;';
			if( $valPadding = $this->getJSONValue( 'galleryPadding', 'enabledCustomPadding', 'alignPaddingBottom' ) ) 	$galleryPaddingStyle .= 'margin-bottom:'.$valPadding.'px;';			
		}
		if( $galleryPaddingStyle ) $this->javaScriptStyles[] = '#twoj_simpleslideshow'.$this->uniqueId.'{'.$galleryPaddingStyle.'}';
		
		if( $border = $this->getStyleFromJSON('thumbborder') ){
			$this->javaScriptStyles[] = '#twoj_simpleslideshow'.$this->uniqueId.' .twoj_simpleslideshow__thumb-border{ border-color: '.$border['color'].';}';
		}
	}
	
	static public function html2rgb($color){
		$color = trim($color);
		if ( strpos($color, '#') !==false  ) $color = str_replace('#', '', $color);
		if(!preg_match('/[0-9a-fA-F]{3,6}/', $color)) return array( '0', '0', '0');
		if( strlen($color) == 6 ){
			list($r, $g, $b) = array($color[0].$color[1], $color[2].$color[3], $color[4].$color[5]);
		} elseif (strlen($color) == 3){
			list($r, $g, $b) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
		} else return  array( '0', '0', '0');
		$r = hexdec($r); $g = hexdec($g); $b = hexdec($b);
		return array($r, $g, $b);
	}
	
	function getStyleFromJSON( $name, $add_check = 0 ){
		$json_temp = $this->getString($name);

		if($json_temp){
			$json_temp = json_decode($json_temp, 1 );
			
			if( $json_temp==null || !isset($json_temp['enabled']) || !$json_temp['enabled'] ) return false;
			
			$json_temp['enabled'] = 1;
			
			if( !isset($json_temp['width'])  ) $json_temp['width'] = 1;
			$json_temp['width'] = (int) $json_temp['width'];
			
			if( !isset($json_temp['opacity'])  ) $json_temp['opacity'] = 1;
			$json_temp['opacity'] = ( (int) $json_temp['opacity'] / 100 ); 
			
			if( !isset($json_temp['color']) )  return false; 
			
			$json_temp['color_rgb'] = TwoJToolBoxSimpleSlideshow::html2rgb($json_temp['color']); 
			
			if($add_check){
				if($add_check==2  && (!isset($json_temp['style']) || $json_temp['style']=='none;' ) ) return false;
			}
			return $json_temp;
		} return false;
	}
	
	
}