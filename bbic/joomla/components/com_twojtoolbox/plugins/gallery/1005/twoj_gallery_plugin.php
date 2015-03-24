<?php
/**
* @package 2JToolBox 2JGallery
* @Copyright (C) 2012 2Joomla.net
* @ All rights reserved
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version $Revision: 1.0.2 $
**/

defined('_JEXEC') or die;

class TwoJToolBoxGallery extends TwoJToolBoxPlugin{
	
	protected $lightboxStyle = 0;
	protected $thumbHoverEffect = 0;
	protected $preload = 0;
	protected $showPagination = 0;
	protected $javaScriptStyles = array();
	protected $lbOptions = array();
	protected $uniqueId = 0;
	
	protected $totalImages = 0;
	protected $countPagination = 0;
	
	protected $paginationPosition = 0;
	
	protected $css_list=array( 'gallery.base' );
	protected $js_list=array( 'gallery');
	
	
	public function includeLib(){
		$this->preload = $this->getInt( 'preload', 0);
		if( $this->preload ) $this->js_list[] = 'preload';
		
		if( $this->showPagination = $this->getInt('showPagination', 0) ){
			$this->css_list[] = 'pagination';
			$this->js_list[] = 'pages';
		}
		
		$this->thumbHoverEffect = $this->getInt('thumbHoverEffect', 0);
		$this->css_list[] = 'gallery.style'.$this->thumbHoverEffect;
		if($this->thumbHoverEffect!=-1) $this->css_list[] = 'gallery.innerstyle'.$this->getInt('thumbStyle', 0);
		
		$this->lightboxStyle = $this->getInt('lightboxStyle', 0);
		$this->css_list[] = 'lb'.$this->lightboxStyle;
		$this->js_list[] = 'lb'.$this->lightboxStyle;

		parent::includeLib();
	}
	

	public function getElement(){
		$db			= JFactory::getDBO();
		$app 		= JFactory::getApplication();
		
		$return_text = '';
		
		if( JRequest::getInt('uniqueIdSet', 0) ){
			$this->uniqueId = JRequest::getInt('uniqueIdSet', 0);
		} else {
			$this->uniqueId = $this->getuniqueid();
		}
		
		$liStyle = '';
		$ulStyle = '';
		$hidden_block = '';
		
		
		$generet_big_img_url 	= $this->getUrlResize('big_');
		$generet_thumb_img_url 	= $this->getUrlResize('thumb_');
		
		$items		= $this->getInt('items'); 
		$fromItem = 0;
	
		$onlyImagesLoad = JRequest::getInt('twoj_onlyImagesLoad', 0);
		if($onlyImagesLoad) $this->render_content = 1;
		$pageNumber = 0;
		
		if($this->showPagination){
			$pageNumber 				= JRequest::getInt('twoj_pageNumber', 0);
			$this->countPagination		= $this->getInt('countPagination');
			$this->paginationPosition 	= $this->getInt('paginationPosition');
			
			$items = $this->countPagination;
			if( --$pageNumber < 0 ) $pageNumber = 0;
			$fromItem = $this->countPagination * $pageNumber;
			if(!$onlyImagesLoad){
				$query= $db->getQuery(true);
				$query->select('count(a.id)');
				$query->from('#__twojtoolbox_elements AS a');
				$query->where('a.catid = '.$this->id.' AND a.state = 1');
				$db->setQuery( (string) $query, 0, 0);
				$this->totalImages = $db->loadResult();
			}
			if( $this->totalImages<=$this->countPagination ) $this->showPagination = 0;
		}
		
		if( $this->id == -1 ){
			$this->showPagination = 0;
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
			$db->setQuery( (string) $query, $fromItem, $items );
			$rows = $db->loadObjectList();
		}
		

		$hoverStyleSize 	= ($this->thumbHoverEffect==-1 ? 0 : 1);
		
		$showTitle = $this->getInt('showTitle');
		
		if( !$onlyImagesLoad ) $this->compileJavaScriptStyles();
		
		//galleryAlign
		$galleryAlign = $this->getString('galleryAlign');
		if($galleryAlign!='centre') $liStyle .= 'float: '.$galleryAlign.'; ';
		
		//thumbPadding
		$liStyle .= 'margin: '.$this->getPend('thumbPadding').';';
		
		//gallery border
		if( $shadow = $this->getStyleFromJSON('galleryShadow') ) $ulStyle .=  'box-shadow: 1px 1px '.$shadow['width'].'px rgba( '.$shadow['color_rgb'][0].', '.$shadow['color_rgb'][1].', '.$shadow['color_rgb'][2].', '.$shadow['opacity'].');';
		if( $border = $this->getStyleFromJSON('galleryBorder', 2) ) $ulStyle .=  'border: '.$border['width'].'px '.$border['style'].' '.$border['color'].';';
		
		//galleryPadding	
		if( $pad_top = $this->getInt( 'galleryPadding_top') )  		$ulStyle .= 'padding-top: '.$pad_top.'px;';
		if( $pad_bot = $this->getInt( 'galleryPadding_bottom') )  	$ulStyle .= 'padding-bottom: '.$pad_bot.'px;';
		
		//galleryBgColor
		if( $galleryBgColor = $this->getColor('galleryBgColor') ) 	$ulStyle .= 'background-color: '.$galleryBgColor.'; ';
		
		//galleryWidth
		if( $galleryWidth = $this->getString('galleryWidth') ) $ulStyle .= 'width: '.(int) $galleryWidth.(strpos($galleryWidth, '%')!==false  ? '%' : 'px').'; ';
		
		//preload option
		$stylePreload = '';
		if($this->preload){
			$stylePreload = "background: url('".str_replace( '%%CSS_URL%%', $this->plugin_url.'css', $this->getString('preloadImage') )."') no-repeat center;";
		}
		
		//iframe flag init
		$needIframeCode 	= 0;
		$needInlineCode 	= 0;
		$image_count 		= 0;
		
		if ( count( $rows ) ){
			$image_listing = ''; 
			foreach ($rows as $row){
				++$image_count;
				$row->title = str_replace('&', '&amp;', $row->title);
				$row->iframe = 0;
				$row->inline = 0;
				if( $row->link || $row->link_blank==6 ){
					$big_img_url = str_replace( '&', '&amp;', $row->link );
					if($this->lightboxStyle!=6 && $row->link_blank>2 ){
						switch($row->link_blank){
							case 6:
								$big_img_url = '#twoj_gallery_'.$this->uniqueId.'_image_desc_id'.$image_count;
								$needInlineCode = 1;
								$row->inline = 1;
								break;
							case 4:
								$match = array();
								if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $big_img_url, $match) ) {
									$big_img_url = 'http://www.youtube.com/embed/'.$match[1].'?rel=0&hd=1';
								}
							default:
							$needIframeCode = 1;
							$row->iframe = 1;
							$big_img_url = str_replace( '&', '&amp;', $big_img_url );
						}
					}
				} else {
					if( $this->getInt('big_type_resizing')==3 ){				
						$big_img_url = JURI::root().($this->id == -1 ? '' : 'media/com_twojtoolbox/' ).$row->img;
					} else {
						$big_img_url = $generet_big_img_url.'&ems_file='.TwojToolboxHelper::path_twojcode($row->img);
						if( $url_link = TwojToolBoxSiteHelper::imageResizeSave($big_img_url ) ) $big_img_url = $url_link;
							else  $big_img_url = str_replace( '&', '&amp;', $big_img_url );
					}
				}
				
				$thumb_img_url = $generet_thumb_img_url.'&ems_file='.TwojToolboxHelper::path_twojcode($row->img);
				if( $url_link = TwojToolBoxSiteHelper::imageResizeSave($thumb_img_url ) ) $thumb_img_url = $url_link;
					else  $thumb_img_url = str_replace( '&', '&amp;', $thumb_img_url );
				
				$hover_html = '';
				if($this->thumbHoverEffect>=0 ){
					$hover_html = '<span class="twoj_gallery_hover" '.($hoverStyleSize?'style="width:'.$this->getInt('thumb_width').'px; height:'.$this->getInt('thumb_height').'px"':'').'>'
										.($this->getInt('showTitleHover')?	'<span class="twoj_gallery_hover_info_title">'.JText::_($row->title).		'</span>':'')
										.($this->getInt('showDescHover')?	'<span class="twoj_gallery_hover_info_desc">'.JText::_( strip_tags($row->desc, '<span><em><strong>')).	'</span>':'')
										.($this->getInt('showButtonHover')?	'<span class="twoj_gallery_hover_info_text">'.$this->getString('clickText').'</span>':'')
									.'</span>';
				}
				
				if( $row->link_blank==6 ) $hidden_block .= '<div id="twoj_gallery_'.$this->uniqueId.'_image_desc_id'.$image_count.'">'.$row->desc.'</div>';
				$titleText = '';
				switch( $this->getInt('lightboxDesc') ){
					case 2:  $titleText = htmlspecialchars( strip_tags($row->desc));
							break;
					case 1:  $titleText =  htmlspecialchars($row->title).' '.htmlspecialchars( strip_tags($row->desc));
							break;
					case 0:  $titleText = htmlspecialchars($row->title);
							break;
				}
				$image_listing .= '<div class="twoj_gallery_class_li twoj_gallery_'.$this->uniqueId.'_pageContent'.$pageNumber.'" '.($image_count==1?'id="twoj_gallery_'.$this->uniqueId.'_page'.$pageNumber.'"':'').' style="'.$liStyle.'">'
					.'<a href="'.$big_img_url.'"  '
						.'title="'.$titleText.'" '
						.($row->link && $row->link_blank==1?'target="_blank"':'data-rel="twoj_gallery_lb_group'.$this->uniqueId.'"').' '
						.'class="'
							.( $this->thumbHoverEffect>=0 ? ' twoj_gallery_hover_innerstyle'.$this->getInt('thumbStyle').' twoj_gallery_style'.$this->thumbHoverEffect : '')
							.(!$row->link?' twoj_gallery_lb_enable':'')
							.($row->iframe && $row->link_blank!==1?' twoj_gallery_lb_enable_iframe':'')
							.($row->inline ?' twoj_gallery_lb_enable_inline':'')
						.'" '
						.'style="width:'.$this->getInt('thumb_width').'px; height:'.$this->getInt('thumb_height').'px;"'
						.($this->getInt('lightboxDesc')==2? 'data-decs="twoj_gallery_'.$this->uniqueId.'_image_desc_id'.$image_count.'"':'')
					.'>'
						.'<img '
							.( $this->preload ? 
								'data-original="'.$thumb_img_url.'" '.
								'src="'.$this->plugin_url.'css/images/clear.gif" ' : 
								'src="'.$thumb_img_url.'" '  
							)
							.'style="width:'.$this->getInt('thumb_width').'px; height:'.$this->getInt('thumb_height').'px; margin: 0; padding: 0;'.$stylePreload.'" '
							.'width="'.$this->getInt('thumb_width').'" height="'.$this->getInt('thumb_height').'" '
							.'alt="'.JText::_($row->title).'" '
						.'/>'
						.$hover_html
					.'</a>'
					.($showTitle?'<div class="twoj_gallery_div_title"  style="width:'.$this->getInt('thumb_width').'px;">'.JText::_($row->title).'</div>':'')
				.'</div>';
			}
			if( !$onlyImagesLoad && $this->showPagination && $this->paginationPosition!=1 ) $return_text .= '<div id="twoj_gallery_pagination'.$this->uniqueId.'_top" class="twoj_gallery_pagination_block" ></div>';
			if( !$onlyImagesLoad ) $return_text .=  '<div id="twoj_gallery_holder_images'.$this->uniqueId.'" class="twoj_gallery_class_ul" '.($ulStyle?'style="'.$ulStyle.'"':'').'><div id="twoj_gallery_wrapper'.$this->uniqueId.'" class="twoj_gallery_class_wrapper">';
			$return_text .= $image_listing;
			if( !$onlyImagesLoad ) $return_text .=  '</div></div>';
			if( !$onlyImagesLoad ) $return_text .= '<div class="twojtoolbox_clear"></div>';
			if( !$onlyImagesLoad  && $this->showPagination && $this->paginationPosition!=0 ) $return_text .= '<div id="twoj_gallery_pagination'.$this->uniqueId.'_bottom" class="twoj_gallery_pagination_block" ></div>';
			$return_text .= ($hidden_block ? '<div style="display: none;">'.$hidden_block.'</div>' : ''); //desc
			
			
			$this->javascript_code .= ' var  twoj_gallery'.$this->uniqueId.'_helper = function( num ){';
			if( $this->preload ) $this->javascript_code .= 'emsajax("#twoj_gallery_holder_images'.$this->uniqueId.' .twoj_gallery_'.$this->uniqueId.'_pageContent"+num+".twoj_gallery_class_li a img").twoj_preload({'.($this->getInt( 'preloadEffect')?'effect: "fadeIn"':'').'});';
			if($this->lightboxStyle!=6) $this->lightboxText();
			$this->javascript_code .= ' emsajax("#twoj_gallery_holder_images'.$this->uniqueId.' .twoj_gallery_'.$this->uniqueId.'_pageContent"+num+" a.twoj_gallery_lb_enable").twoj_gallery_lb'.$this->lightboxStyle.'({'.implode(',', $this->lbOptions).'});';
			if($needIframeCode) $this->javascript_code .= "\n".$this->lightboxWidthHeight('Iframe');
			if($needInlineCode) $this->javascript_code .= "\n".$this->lightboxWidthHeight('Inline');
			if($showTitle) $this->javascript_code .= "\n".'emsajax("#twoj_gallery_holder_images'.$this->uniqueId.' .twoj_gallery_'.$this->uniqueId.'_pageContent"+num+" .twoj_gallery_div_title").click( function(){ emsajax(this).prev("a").click(); } );';
			$this->javascript_code .= ' }'."\n";
			
			$this->javascript_code .= 'emsajax(function(){';
			$this->javascript_code .= 'emsajax("head").append("<style '.($this->id==-1?'id=\'dynamic_css\'':'').' type=\'text/css\'>'.implode('\n ', $this->javaScriptStyles).'</style>");';
			
			if($galleryAlign=='centre') 
				$this->javascript_code .= 'twojGalleryInit( "#twoj_gallery_holder_images'.$this->uniqueId.'", '.$this->getInt( 'galleryPadding_left').', '.$this->getInt( 'galleryPadding_right').' );';
			else 
				$this->javascript_code .= 'twojGalleryHeight( "#twoj_gallery_holder_images'.$this->uniqueId.'" );';
				
			$this->javascript_code .= ' twoj_gallery'.$this->uniqueId.'_helper(0); ';
			
			if($this->showPagination){
				
				$this->javascript_code .= $this->pagination('top');
				$this->javascript_code .= $this->pagination('bottom');
				$this->javascript_code .= $this->parceAnchor();
			}
			$this->javascript_code .= ' });';

			
			if( $this->render_content == 0) {
				//$document = JFactory::getDocument();
				//$document->addScriptDeclaration($this->javascript_code);
				$return_text .= '<script language="JavaScript" type="text/javascript">'."\n".'<!--//<![CDATA['."\n".$this->javascript_code."\n".'//]]>-->'."\n".'</script>';
			}
		}
		if($return_text) return  $return_text; else return null;
	}
	
	
	protected function pagination( $position ){
		if( ($this->paginationPosition =='0' && $position=='bottom') || ( $this->paginationPosition =='1' && $position=='top') ) return ;
		$returnJs = '';
		if($position=='top') $returnJs .= 'var twoj_gallery'.$this->uniqueId.'_pagination = 0;';
		$returnJs .= 'emsajax("#twoj_gallery_pagination'.$this->uniqueId.'_'.$position.'").pagination({ 
			items: '.$this->totalImages.', 
			itemsOnPage: '.$this->countPagination.', 
			displayedPages: '.$this->getInt('showPaginationPagesInBar').',
			cssStyle:"twoj_gallery_pages_'.$this->getString('paginationTheme').'",
			hrefTextPrefix: "#gallery'.$this->uniqueId.'-page-", 
			nextText: "'.str_replace('"', "'", $this->getString('nextTextPagination')).'",
			prevText: "'.str_replace('"', "'", $this->getString('prevTextPagination')).'",
			onPageClick: function( pageNumber ){';
		if( $position=='top' ) $returnJs .= 'twoj_gallery'.$this->uniqueId.'_pagination = 1;';
		if($this->paginationPosition==2 && $position=='bottom'){
			$returnJs .= 'if(twoj_gallery'.$this->uniqueId.'_pagination==0){ emsajax("#twoj_gallery_pagination'.$this->uniqueId.'_top").pagination("selectPage", pageNumber);'; 
			if($this->getInt('goToTopPagination')){
				$returnJs .= 'var topValue = Math.round(emsajax("#twoj_gallery_wrapper'.$this->uniqueId.'").offset().top);emsajax("body,html").clearQueue().animate({scrollTop: topValue}, 1000);';
			}
			$returnJs .= '}';
		} else {
			$returnJs .= 'twoj_gallery_pagination_toPage( "'.JURI::base().'", '.$this->id.', '.$this->uniqueId.', twoj_gallery'.$this->uniqueId.'_helper , pageNumber );';
			if($position=='top')  $returnJs .= 'emsajax("#twoj_gallery_pagination'.$this->uniqueId.'_bottom").pagination("selectPage", pageNumber);';
		}
		if($position=='top') $returnJs .= 'twoj_gallery'.$this->uniqueId.'_pagination = 0;';
		$returnJs .= ' return false }});';
		return $returnJs;
	}
	
	
	protected function parceAnchor(){
		return  'var strippedUrl = document.location.toString().split("#");'
			.'if(strippedUrl.length > 1){'
				.'var anchorvalue = strippedUrl[1];'
				.'if ( /^gallery'.$this->uniqueId.'-page-([0-9]{1,3})([-]{0,1})(.*)$/.test(anchorvalue) ){'
					.'var matches = /^gallery'.$this->uniqueId.'-page-([0-9]{1,3})([-]{0,1})(.*)$/.exec(anchorvalue);'
					.'if(matches.length > 1){'
							.'emsajax("#twoj_gallery_pagination'.$this->uniqueId.'_'.($this->paginationPosition=='0'?'top':'bottom').'").pagination("selectPage", matches[1]);'
					.'}'
				.'}'
			.'}';
	}
	
	protected function lengthFormat( $valInput){ return  (int)$valInput.(strpos($valInput, '%')!==false?'%':''); }
	
	protected function lightboxText( ){
		$this->lbOptions[] = 'current: "'.	JText::_($this->getString( 'lightboxTextCurrent'), 1).'"';
		$this->lbOptions[] = 'previous: "'.	JText::_($this->getString( 'lightboxTextPrevious'), 1).'"';
		$this->lbOptions[] = 'next: "'.		JText::_($this->getString( 'lightboxTextNext'), 1).'"';
		$this->lbOptions[] = 'close: "'.	JText::_($this->getString( 'lightboxTextClose'), 1).'"';
		
	}
	
	protected function lightboxWidthHeight( $type = 'Iframe' ){
		$lightboxWidth 	= $this->lengthFormat( $this->getString( 'lightbox'.$type.'Width') );
		$lightboxHeight = $this->lengthFormat(  $this->getString( 'lightbox'.$type.'Height') );
		return ' emsajax("#twoj_gallery_holder_images'.$this->uniqueId.' .twoj_gallery_'.$this->uniqueId.'_pageContent"+num+"  a.twoj_gallery_lb_enable_'.strtolower($type).':visible").twoj_gallery_lb'.$this->lightboxStyle.'({'
			.strtolower($type).':true,'
			.'innerWidth: "'.$this->lengthFormat( $this->getString( 'lightbox'.$type.'Width') ).'",'
			.'innerHeight: "'.$this->lengthFormat( $this->getString( 'lightbox'.$type.'Height') ).'"'.(count($this->lbOptions)?',':'')
			.implode(',', $this->lbOptions)
		.'});';
	}
	
	
	protected function compileJavaScriptStyles(){
		//Def hover border style
		$borderStylesHover = '';
		if( $hovershadow = $this->getStyleFromJSON('hovershadow') ){
			$borderStylesHover .= 'box-shadow: 1px 1px '.$hovershadow['width'].'px rgba( '.$hovershadow['color_rgb'][0].', '.$hovershadow['color_rgb'][1].', '.$hovershadow['color_rgb'][2].', '.$hovershadow['opacity'].');';
		}
		if( $hoverborder = $this->getStyleFromJSON('hoverborder', 2) ){
			$borderStylesHover .= 'border: '.$hoverborder['width'].'px '.$hoverborder['style'].' '.$hoverborder['color'].';';
		}
		if( $hoverinnerborder = $this->getStyleFromJSON('hoverinnerborder') ){
			$borderStylesHover .= 'background-color: '.$hoverinnerborder['color'].'; padding: '.$hoverinnerborder['width'].'px;';
		}
		if($borderStylesHover) $this->javaScriptStyles[] = '#twoj_gallery_holder_images'.$this->uniqueId.' .twoj_gallery_class_li:hover {'.$borderStylesHover.'}';
		
		//Def border style
		$borderStyles = '';
		if( $shadow = $this->getStyleFromJSON('shadow') ){
			$borderStyles .= 'box-shadow: 1px 1px '.$shadow['width'].'px rgba( '.$shadow['color_rgb'][0].', '.$shadow['color_rgb'][1].', '.$shadow['color_rgb'][2].', '.$shadow['opacity'].');';
		}
		if( $border = $this->getStyleFromJSON('border', 2) ){
			$borderStyles .= 'border: '.$border['width'].'px '.$border['style'].' '.$border['color'].';';
		}
		if( $innerborder = $this->getStyleFromJSON('innerborder') ){
			$borderStyles .= 'background-color: '.$innerborder['color'].'; padding: '.$innerborder['width'].'px;';
		}
		if($borderStyles) $this->javaScriptStyles[] = '#twoj_gallery_holder_images'.$this->uniqueId.' .twoj_gallery_class_li{'.$borderStyles.'}';
		
		
		if($this->thumbHoverEffect==1){
			$thumb_width = $this->getInt('thumb_width');
			$this->javaScriptStyles[] = '#twoj_gallery_holder_images'.$this->uniqueId.'.twoj_gallery_style'.$this->thumbHoverEffect.':hover img{'
				.'-webkit-transform: translateX('.$thumb_width .'px);'
				.'-moz-transform: translateX('.$thumb_width .'px);'
				.'-o-transform: translateX('.$thumb_width .'px);'
				.'-ms-transform: translateX('.$thumb_width .'px);'
				.'transform: translateX('.$thumb_width .'px);}';
				
			$this->javaScriptStyles[] = '#twoj_gallery_holder_images'.$this->uniqueId.'.twoj_gallery_style'.$this->thumbHoverEffect.' .twoj_gallery_hover{'
				.'-webkit-transform: translateX(-'.$thumb_width .'px);'
				.'-moz-transform: translateX(-'.$thumb_width .'px);'
				.'-o-transform: translateX(-'.$thumb_width .'px);'
				.'-ms-transform: translateX(-'.$thumb_width .'px);'
				.'transform: translateX(-'.$thumb_width .'px);'
				.'}';
		}
		
		//BG lightbox
		if( $lightboxBg = $this->getStyleFromJSON('lightboxBg') ){
			if( $this->lightboxStyle > 5 ){
				$this->javaScriptStyles[] = '#twoj_gallery_lb'.$this->lightboxStyle.'_Overlay.visible{ background-color: rgba( '.$lightboxBg['color_rgb'][0].','.$lightboxBg['color_rgb'][1].','.$lightboxBg['color_rgb'][2].','.$lightboxBg['opacity'].');}';
			} else {
				$this->javaScriptStyles[] = '#twoj_gallery_lb'.$this->lightboxStyle.'_Overlay{background: '.$lightboxBg['color'].' none no-repeat;}';
				$this->lbOptions[]= 'opacity:'."'".$lightboxBg['opacity']."'";
			}
		}
		if( $this->lightboxStyle <= 5 ){ $this->lbOptions[] = 'rel:"twoj_gallery_lb_group'.$this->uniqueId.'"'; }
		
		$thumbStyleBgOpacity = $this->getInt('thumbStyleBgOpacity')/100;
		
		$thumbStyleBgColor = TwoJToolBoxGallery::html2rgb($this->getColor('thumbStyleBgColor'));
		if(count($thumbStyleBgColor) ) $this->javaScriptStyles[] = '#twoj_gallery_holder_images'.$this->uniqueId.' .twoj_gallery_hover,'
			.'#twoj_gallery_holder_images'.$this->uniqueId.' .twoj_gallery_hover:hover'
			.'{'
				.'background-color:rgb('.$thumbStyleBgColor[0].', '.$thumbStyleBgColor[1].', '.$thumbStyleBgColor[2].');'
				.'background-color:rgba('.$thumbStyleBgColor[0].', '.$thumbStyleBgColor[1].', '.$thumbStyleBgColor[2].', '.$thumbStyleBgOpacity.');'
			.'}';
		
		
		//template fix
		$this->javaScriptStyles[] = '#twoj_gallery_holder_images'.$this->uniqueId.' .twoj_gallery_class_li > a,'
			.'#twoj_gallery_holder_images'.$this->uniqueId.' .twoj_gallery_class_li > a:hover,'
			.'#twoj_gallery_holder_images'.$this->uniqueId.' .twoj_gallery_class_li > a:link,'
			.'#twoj_gallery_holder_images'.$this->uniqueId.' .twoj_gallery_class_li > a:visited,'
			.'#twoj_gallery_holder_images'.$this->uniqueId.' .twoj_gallery_class_li > a:focus'
			.'{background-color: transparent; outline: 0;}';
		
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
	
	function getPend( $name ){
		$html_ret = ' ';
		$html_ret .=  $this->getInt( $name.'_top', 10)	.'px ';
		$html_ret .=  $this->getInt( $name.'_right', 10 ).'px ';
		$html_ret .=  $this->getInt( $name.'_bottom', 10).'px ';			
		$html_ret .=  $this->getInt( $name.'_left', 10 ).'px';
		return str_replace( ' 0px', ' 0', $html_ret );
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
			
			$json_temp['color_rgb'] = TwoJToolBoxGallery::html2rgb($json_temp['color']); 
			
			if($add_check){
				if($add_check==2  && (!isset($json_temp['style']) || $json_temp['style']=='none;' ) ) return false;
			}
			return $json_temp;
		} return false;
	}
}