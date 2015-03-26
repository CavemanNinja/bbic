<?php
/**
* @package 2JToolBox 2JSliderTabs
* @Copyright (C) 2013 2Joomla.net
* @ All rights reserved
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version $Revision: 1.0.0 $
**/
defined('_JEXEC') or die;

class TwoJToolBoxSliderTabs extends TwoJToolBoxPlugin{
	protected $css_list=array('slidertabs', '@@ROOT@@fa');
	protected $js_list=array( 'slidertabs.easing', 'slidertabs');
	protected $less_list=array();
	
	protected $content_plugin = 1;
	
	protected $tabs_body_html = '';
	protected $tabs_header_html = '';
	
	protected $tabs_coutn = 0;
	
	protected $titleintab = 0;
	protected $custom_label = '';
	protected $tab_template = '';
	protected $tab_title_length = 0;
	
	protected $select_default = 0;
	
	protected $block_class = '';
	
	protected $content_template = '';
	
	protected $wrapStyle = array();
	
	public function includeLib(){
		$this->uniqueid = $this->getuniqueid();
		jimport( 'joomla.environment.browser' );
		$browser = JBrowser::getInstance();
       	if( $browser->getBrowser()=='msie' && $browser->getMajor()=='7' ){
			$this->css_list[]='fa.ie7';
		}
		$this->less_list[] = $this->id.'*'.$this->cacheid.'*'.$this->uniqueid;
		if( $this->getInt( 'tabsScroll', 1 ) )		array_unshift( $this->js_list, 'slidertabs.mousewheel' );
		if( $this->getInt( 'touchSupport', 0 ) ) 	$this->js_list[]='slidertabs.touch' ;
		parent::includeLib();
	}
	
	public function outLess($uniqueId=0){
		$this->lessFiles[] = 'less/slidertabs';
		$this->lessContent .= '.setBackgroundColor(){';
		if( $this->getJSONValue('background') =='bgColor'){
			$this->lessContent .= 'background: '.$this->getJSONValue( 'background', 'bgColor', 'bgColorValue', 'color' ).';';
		} elseif( $this->getJSONValue('background') =='bgGradient'){
				$colorStart = $this->getJSONValue( 'background', 'bgGradient', 'bgGradientStart', 'color' );
				$colorStop = $this->getJSONValue( 'background', 'bgGradient', 'bgGradientStop', 'color' );
				$bgGradientOrientation = $this->getJSONValue( 'background', 'bgGradient', 'bgGradientOrientation', 1 );
				$mixGradient = $bgGradientOrientation.','.$colorStart.','.$colorStop;
				$this->lessContent .= 'background: '.$colorStart.';';
				$this->lessContent .= 'background: -moz-linear-gradient('.$mixGradient.');';
				$this->lessContent .= 'background: -webkit-gradient(linear, '.$mixGradient.');';
				$this->lessContent .= 'background: -webkit-linear-gradient('.$mixGradient.');';
				$this->lessContent .= 'background: -o-linear-gradient('.$mixGradient.');';
				$this->lessContent .= 'background: -ms-linear-gradient('.$mixGradient.');';
				$this->lessContent .= 'background: linear-gradient('.$mixGradient.');';
				$this->lessContent .= "filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='".$colorStart."', endColorstr='".$colorStop."',GradientType=1 );";
		}
		$this->lessContent .= '}';

		$this->lessContent .=  '@tabBgColor:  '.		$this->getJSONValue( 'tabBgColorSet', 'tabBgColorOn', 'tabBgColor', 		'color' ).';';
		$this->lessContent .=  '@tabBgColorHover:  '.	$this->getJSONValue( 'tabBgColorSet', 'tabBgColorOn', 'tabBgColorHover', 	'color' ).';';
		$this->lessContent .=  '@tabBgColorActive:  '.	$this->getJSONValue( 'tabBgColorSet', 'tabBgColorOn', 'tabBgColorActive', 	'color' ).';';
		
		$this->lessContent .= '@tabBorderWidth: '.$this->getString( 'border_w').';';
		$this->lessContent .= '@borderStyle: @tabBorderWidth '.$this->getString( 'border_s').' '.$this->getColor( 'border_color').';';
		
		$navButWidth = $this->getInt( 'navButWidth');
		$this->lessContent .= '@navButWidth:  '.(!$navButWidth?$navButWidth:43).'px;';
		
		$tabHeight = $this->getInt( 'tabHeight');
		$this->lessContent .= '@tabHeight: '.($tabHeight?$tabHeight:43).'px;';
		
		
		$this->lessContent .=  '@tabFontSize:'.(int)$this->getJSONValue( 'fontTabs', 'customFont', 'sizeFont' ).'px;';
		$this->lessContent .=  '@tabFontName:'.		$this->getJSONValue( 'fontTabs', 'customFont', 'familyFont', 1).';';
		$this->lessContent .=  '@tabFontWidth:'.	$this->getJSONValue( 'fontTabs', 'customFont', 'weightFont', 1).';';
	
		
		/* $this->lessContent .=  '@tabSpanFontSize:'.(int)$this->getJSONValue( 'fontSpanTabs', 'customSpanFont', 'sizeSpanFont' ).'px;';
		$this->lessContent .=  '@tabSpanFontName:'.		$this->getJSONValue( 'fontSpanTabs', 'customSpanFont', 'familySpanFont', 1).';';
		$this->lessContent .=  '@tabSpanFontWidth:'.	$this->getJSONValue( 'fontSpanTabs', 'customSpanFont', 'weightSpanFont', 1).';';
	 */
		
		if( $this->getJSONValue('tabRadius') =='tabRadiusOn'){
			$this->lessContent .=  '@tabRadius:  '.(int) $this->getJSONValue( 'tabRadius', 'tabRadiusOn', 'tabRadiusValue' ).'px;';
			$this->lessFiles[] = 'less/round';
		}
		
		if( $this->getJSONValue('tabOut') =='tabOutSet' || $this->getJSONValue('tabOut') =='tabOutOn'){
			if($this->getJSONValue('tabOut') =='tabOutSet') $this->lessContent .=  '@tabOutPx:  '.(int) $this->getJSONValue( 'tabOut', 'tabOutSet', 'tabOutValue' ).'px;';
				else { $this->lessContent .=  '@tabOutPx: -1 px;'; }
			$this->lessFiles[] = 'less/out';
			if( $this->getJSONValue('tabRadius') =='tabRadiusOn' ) $this->lessFiles[] = 'less/outround';
		}
		
		
		$this->lessContent .=  '@navButColor:  '.		$this->getJSONValue( 'navButColorSet', 'navButColorOn', 'navButColor', 			'color' ).';';
		$this->lessContent .=  '@navButColorHover:'.	$this->getJSONValue( 'navButColorSet', 'navButColorOn', 'navButColorHover', 	'color' ).';';
		$this->lessContent .=  '@navButColorDisabled:'.	$this->getJSONValue( 'navButColorSet', 'navButColorOn', 'navButColorDisabled', 	'color' ).';';
		
		$this->lessContent .=  '@navBgColor:  '.		$this->getJSONValue( 'navBgColorSet', 'navBgColorOn', 'navBgColor', 		'color' ).';';
		$this->lessContent .=  '@navBgColorHover:  '.	$this->getJSONValue( 'navBgColorSet', 'navBgColorOn', 'navBgColorHover', 	'color' ).';';
		$this->lessContent .=  '@navBgColorDisabled:  '.$this->getJSONValue( 'navBgColorSet', 'navBgColorOn', 'navBgColorDisabled', 'color' ).';';
		
		
		$this->lessContent .=  '@tabColor:  '.			$this->getJSONValue( 'tabColorSet', 'tabColorOn', 'tabColor', 		'color' ).';';
		$this->lessContent .=  '@tabColorHover:  '.		$this->getJSONValue( 'tabColorSet', 'tabColorOn', 'tabColorHover', 	'color' ).';';
		$this->lessContent .=  '@tabColorActive:  '.	$this->getJSONValue( 'tabColorSet', 'tabColorOn', 'tabColorActive', 'color' ).';';
		
		
		$this->lessContent .= '@CSS_URL: "'.$this->plugin_url.'css";';
		$this->lessContent .= '@twoJObjectId: twoj_slidertabs_block_id'.$uniqueId.';';
		
		// for vertical
		$this->lessContent .= '@tabWidth:  '.abs($this->getInt( 'tabWidth')).'px;';
		$this->lessContent .= '@verticalHeight:  '.$this->getInt( 'verticalHeight').'px;';
		
		
		$this->lessContent .= '@zIndex = 0;'; 
		
		if( $this->getJSONValue('pendingInner') =='pendingInnerOn'){
			$this->lessContent .= '@contentPadding:'
				.(int) $this->getJSONValue( 'pendingInner', 'pendingInnerOn', 'pendingInnerTop').'px '
				.(int) $this->getJSONValue( 'pendingInner', 'pendingInnerOn', 'pendingInnerRight').'px '
				.(int) $this->getJSONValue( 'pendingInner', 'pendingInnerOn', 'pendingInnerBottom').'px '
				.(int) $this->getJSONValue( 'pendingInner', 'pendingInnerOn', 'pendingInnerLeft').'px;';	
		} else $this->lessContent .= '@contentPadding: 0;';
		
		$this->lessContent .= '@pendingButtonTop:' 		.abs((int) $this->getJSONValue( 'pendingButton', 'pendingButtonOn', 'pendingButtonTop')).'px; ';
		$this->lessContent .= '@pendingButtonRight:' 	.abs((int) $this->getJSONValue( 'pendingButton', 'pendingButtonOn', 'pendingButtonRight')).'px; ';
		$this->lessContent .= '@pendingButtonBottom:' 	.abs((int) $this->getJSONValue( 'pendingButton', 'pendingButtonOn', 'pendingButtonBottom')).'px; ';
		$this->lessContent .= '@pendingButtonLeft:' 	.abs((int) $this->getJSONValue( 'pendingButton', 'pendingButtonOn', 'pendingButtonLeft')).'px; ';
		
		return parent::outLess();
	}
	
	
	public function getStyleString($style, $add_style=''){
		if($add_style) $style[] = $add_style;
		if( count($style) ) return ' style="'.implode(' ', $style).'"';
		return '';
	}
	
	
	
	
	public function getElement(){
		$db	= JFactory::getDBO();
		$app = JFactory::getApplication();
		
		if( $this->params->get('content_source', 'articles')=='modules' ){
			$this->multitag=1;
			$module_list = $this->params->get('module_list', '');
			if($module_list){
				if( strpos($module_list, ';') ){
					$module_list_array = explode(";", $module_list);
				}else if( strpos($module_list, "\n") ){
					$module_list_array = explode("\n", $module_list);
				}else{
					$module_list_array = array($module_list);
				}
				for($i=0; $i<count($module_list_array);$i++){
					$module = $module_list_array[$i];
					$module = str_replace(';', '', $module);
					$module = trim($module);
					$temp_obj = new JObject;
					$temp_obj->title = '';
					if( is_numeric($module) ){
						$temp_obj->conten = $this->loadModuleId($module, $this->params->get('module_type_out', '') );
					} else {
						$temp_obj->conten = $this->loadModulePosition($module, $this->params->get('module_type_out', '') );
					}
					if( !$temp_obj->conten )  $temp_obj->conten = JText::_("You don't have such module at your website. Try to check whether you are write down module ID or position of the module. [Incorrect value: ".$module." ]");;
						
					$this->page_array[] = $temp_obj;
				}
			}
		}
		
		if($this->multitag==0){
			$this->list = $this->getList();
			$this->grouped = false;
			$article_grouping = $this->params->get('article_grouping', 'none');
			$article_grouping_direction = $this->params->get('article_grouping_direction', 'ksort');
			$item_heading = $this->params->get('item_heading');
			if ($article_grouping !== 'none'){
				$this->grouped = true;
				switch($article_grouping){
					case 'year':
					case 'month_year':
						$this->list = TwojToolBoxSiteHelper::contentGroupByDate($this->list, $article_grouping, $article_grouping_direction, $this->params->get('month_year_format', 'F Y'));
						break;
					case 'author':
					case 'category_title':
						$this->list = TwojToolBoxSiteHelper::contentGroupBy($this->list, $article_grouping, $article_grouping_direction);
						break;
					default:
						break;
				}
			}
			$this->tabs_coutn = count($this->list);
		} else {
			$this->tabs_coutn = count($this->page_array);
		}
		$return_text = '';
		
		$this->initOptions();
		$this->articles_diplay();
		
		//if( !$this->getInt( 'show_comments' ) ) $return_text .= "\n".'<!-- 2JSliderTabs (2JToolBox framework) Start tag -->'."\n";
		
		if( $this->tabs_body_html){
			$return_text .= $this->getString('pretext');
			$return_text .= '<div class="twoj_slidertabs_wrap" '.$this->getStyleString($this->wrapStyle).'>';
				$return_text .= '<div id="twoj_slidertabs_block_id'.$this->uniqueid.'"  class="'.$this->classBlock.' twoj_slidertabs twoj_slidertabs_'.$this->orientation.'">'; 
					$return_text .= $this->tabs_header_html;
					$return_text .= '<div class="st_views">'.$this->tabs_body_html.'</div>';
				$return_text .= '</div>';
			$return_text .= '</div>';
			$return_text .= '<div class="twoj_clear"></div>';
			$return_text .= $this->getString('posttext');
			
			if(!$this->render_content){
				$return_text .= "\n".'<script language="JavaScript" type="text/javascript">';
				$return_text .= "\n".'<!--//<![CDATA[';
				$return_text .= "\n".'emsajax('.($this->getInt( 'workScript', 0 )?'window).load':'document).ready').'(function() { emsajax("#twoj_slidertabs_block_id'.$this->uniqueid.'").slidetabs({'.implode(' ,', $this->gen_option).'});})';
				$return_text .= "\n".'//]]>-->';
				$return_text .= "\n".'</script>';
			}			
		}
		//if( !$this->getInt( 'show_comments' ) )  $return_text .= "\n".'<!-- 2JSliderTabs (2JToolBox framework) End tag  -->'."\n";
		if($return_text) return  $return_text; else return null;
	}
	
	
	function create_tab( $tab_count_link, $title, $content, $idArticle= 0 ){
			if( $this->titleintab==1 || ( $title=='' && $this->titleintab==0 ) ){
				if($this->tab_template){
					$title = str_replace('#', $tab_count_link, $this->tab_template);
				} else $title = $tab_count_link;
			}
			if($this->titleintab==2){
				if( count($this->custom_label) && isset($this->custom_label[$tab_count_link-1]) && $cus_text=trim($this->custom_label[$tab_count_link-1]) )
					$title = $cus_text;
				 else 
					$title = $tab_count_link;
			}
			if($this->tab_title_length && strlen($title) > $this->tab_title_length ){
				$title = substr( $title, 0, $this->tab_title_length );
			}
			$this->tabs_header_html .= '<li class="twoj_tab_block_li">'; //#tab-'.$tab_count_link.'
				$this->tabs_header_html .= 
					' <a href="#tab'.$this->uniqueid.'-'.$tab_count_link.'" '
						.( $this->select_default && $this->select_default==$tab_count_link ? 'class="st_tab_active"' : '')
						.($this->getInt( 'urlLinking', 0) ? ' rel="tab'.$this->uniqueid.'-'.$tab_count_link.'"':'')
					.'>'
						.$title; //twoj_slidertabs_'.$this->uniqueid.'  /site/j6/index.php?option=com_content&view=article&id='.$idArticle.'&Itemid=0&tmpl=component
					//class="st_ext"  rel="fghfghfg-'.$tab_count_link.'" data-target="#tab-'.$tab_count_link.'"  
					//$this->tabs_header_html .= '<span>'.$title.'</span>';
				$this->tabs_header_html .= '</a>';
			$this->tabs_header_html .= '</li>';
			//$this->tabs_body_html = ' ';
			$this->tabs_body_html .= '<div class="tab'.$this->uniqueid.'-'.$tab_count_link.' st_view">';//twoj_slidertabs_'.$this->uniqueid.'
			$this->tabs_body_html .= '<div class="st_view_inner">'.$content.'</div>';
			$this->tabs_body_html .= '</div>';
		
	}
	function getIcon($iconName){
		$iconString = $this->getString($iconName);
		if( !$iconString ) $iconString = $this->def_params->get($iconName);
		if( !$iconString ) $iconString = '{}';
		$jsonObj = json_decode( $iconString );
		if($jsonObj === NULL){ //json_last_error()
			$iconString = $this->def_params->get($iconName);
			if( !$iconString ) $iconString = '{}';
			$jsonObj = json_decode($iconString);
		}
		$iconClass = '';
		if(isset($jsonObj->icon)) $iconClass.=$jsonObj->icon;
		if(isset($jsonObj->rotate)) $iconClass.=' tjicon-'.$jsonObj->rotate;
		$iconStyle='';
		if(isset($jsonObj->color)) $iconStyle.='color:'.$jsonObj->color.';';
		if(isset($jsonObj->size)) $iconStyle.='font-size:'.$jsonObj->size.';';
		if($iconStyle) $iconStyle = 'style="'.$iconStyle.'"';
		return '<i class="'.$iconClass.'" '.$iconStyle.'></i>';
	}
	
	
	function initOptions(){
		
		$this->addGenOption("'nextIcon': '". JText::_($this->getIcon('nextIcon'))."'");
		$this->addGenOption("'prevIcon': '". JText::_($this->getIcon('prevIcon'))."'");
		
		$this->orientation = $this->getString( 'orientation', 'horizontal');
		$this->addGenOption("orientation:  '".$this->orientation."'");
		$this->classBlock = $this->orientation; //$this->styleName.
		if( $this->getInt( 'orientationAlign', 0 ) ){
			$this->classBlock .= ' '.($this->orientation=='horizontal'?'align_bottom':'align_right');
		}
		$this->insertInt( 'tabsLoop');
		$this->insertInt( 'tabsScroll', 1);
		
		$this->contentAnim = $this->getString( 'contentAnim', 'slideH');
		if( $this->contentAnim=='-1' ){
			$this->contentAnim = '';
		} else {
			$this->insertInt( 'contentAnimSpeed', 600);
			$this->insertString( 'contentEasing', "easeInOutExpo");
		}
		$this->addGenOption("contentAnim:  '".$this->contentAnim."'");
		
		$this->insertInt( 'autoplay', 0);
		$this->insertInt( 'autoplayInterval', 3000);
		$this->insertInt( 'autoplayClickStop', 0);
		
		$this->insertInt( 'tabsShowHash', 0);
		$this->insertInt( 'urlLinking', 0);
		
		
		
		$this->insertString( 'buttonsFunction', 'slide');
		
		if( $this->getJSONValue('height') =='autoHeight'){
			$this->addGenOption("autoHeight:  true");
			$this->addGenOption("autoHeightSpeed:  ".(int)$this->getJSONValue( 'height', 'autoHeight', 'autoHeightSpeed' ));
		} else {
			$this->addGenOption("autoHeight:  false");
			$this->addGenOption("totalHeight:  '".(int)$this->getJSONValue( 'height', 'customHeight', 'totalHeight' )."'");
		}
		
		if( $this->getJSONValue('width') =='autoWidth'){
			$this->addGenOption("Width:  ''");
		} else {
			$this->addGenOption("totalWidth:  '".(int)$this->getJSONValue( 'width', 'customWidth', 'totalWidth' )."'");
		}
		
		if( $this->getInt( 'touchSupport', 0 ) ) $this->addGenOption("touchSupport:  true");
		
		
		if( $this->getJSONValue('pendingWrap') =='pendingWrapOn'){
			$pendingTop = 		(int) $this->getJSONValue( 'pendingWrap', 'pendingWrapOn', 'pendingWrapTop');
			$pendingLeft = 		(int) $this->getJSONValue( 'pendingWrap', 'pendingWrapOn', 'pendingWrapLeft');
			$pendingBottom = 	(int) $this->getJSONValue( 'pendingWrap', 'pendingWrapOn', 'pendingWrapBottom');
			$pendingRight = 	(int) $this->getJSONValue( 'pendingWrap', 'pendingWrapOn', 'pendingWrapRight');
			if( $pendingTop || $pendingLeft || $pendingBottom || $pendingRight){
				$this->wrapStyle[] = 'padding:'.$pendingTop.'px '.$pendingRight.'px '.$pendingBottom.'px '.$pendingLeft.'px;';	
			}
		}
		
		
		//=========================
		$this->select_default	= $this->getInt( 'select_default');


		//tabs title setup
		$this->titleintab	 	= $this->getInt( 'titleintab'); 
		$this->tab_template 	=  $this->getString( 'tab_template'); 
		$this->tab_title_length =  $this->getInt( 'tab_title_length' );
		$this->custom_label		=  $this->getString( 'custom_label');
		$this->custom_label 	= explode( ( strpos( $this->custom_label, "#;")!==false ? "#;" : "\n" ), $this->custom_label);
		
		if( $this->getJSONValue('navAnimation') =='navAnimationOn'){
			$animE = $this->getJSONValue( 'navAnimation', 'navAnimationOn', 'navAnimationEffect' );
			$this->addGenOption("tabsAnimSpeed:  ".(int)$this->getJSONValue( 'navAnimation', 'navAnimationOn', 'navAnimationSpeed' ));
			if($animE=='_empty_') $animE = '';
			if($animE) $this->addGenOption("tabsEasing:  '".$animE."'");
		}
	}
	
	function articles_diplay(){
		$ret_html = '';

		if($this->multitag){
			for($i=0;$i<count($this->page_array);$i++){
				$page = $this->page_array[$i];
				$this->create_tab( ($i+1), $page->title, $page->conten );				
			}
		} else {
			$this->prepare_template();
			$tab_count = 0;
			if ($this->grouped){
				foreach ($this->list as $group_name => $group){
					++$tab_count;
					$article_list = '';
					foreach ($group as $item) $article_list .= $this->article_diplay($item);
					$this->create_tab( $tab_count, $group_name, $article_list );
				}
			} else {
				foreach ($this->list as $item){
					++$tab_count;
					$this->create_tab( $tab_count, $item->title,  $this->article_diplay($item), $item->id );
				}
			}
		}
		$this->tabs_header_html = '<div class="st_tabs"><div class="st_tabs_wrap"><ul class="st_tabs_ul">'.$this->tabs_header_html.'</ul></div></div>';
	}
	
	function article_diplay($item){
		$return_html='';

		$text_title = $item->title;
		$category_url = '<a href="'.JRoute::_(ContentHelperRoute::getCategoryRoute($item->catslug)).'">'.$item->category_title.'</a>';
		$cat_text = JText::sprintf('COM_CONTENT_CATEGORY', $item->category_title); 
		if ($item->catslug) $cat_text_url = JText::sprintf('COM_CONTENT_CATEGORY', $category_url); 
			else  $cat_text_url = JText::sprintf('COM_CONTENT_CATEGORY', $item->category_title); 
		$date_create = JText::sprintf('COM_CONTENT_CREATED_DATE_ON', JHtml::_('date',$item->created, JText::_('DATE_FORMAT_LC2')));
		$date_modified = JText::sprintf('COM_CONTENT_LAST_UPDATED', JHtml::_('date',$item->modified, JText::_('DATE_FORMAT_LC2')));
		$date_publish_up = JText::sprintf('COM_CONTENT_PUBLISHED_DATE', JHtml::_('date',$item->publish_up, JText::_('DATE_FORMAT_LC2')));
		
		$text_author = JText::sprintf('COM_CONTENT_WRITTEN_BY', ($item->created_by_alias ? $item->created_by_alias : $item->author) );
		$text_hits = JText::sprintf('COM_CONTENT_ARTICLE_HITS', $item->hits);
		
		if (JFactory::getLanguage()->get('tag') == "en-GB") {
			$item->link = 'index.php?lang=en&Itemid=227'
		} else {
			$item->link = 'index.php?lang=ar&Itemid=229'
		}


		$text_link = $item->link;
	
		if($item->readmore){
			$text_readmore='<p class="readmore"><a href="'.$item->link.'">';
			if ($item->params->get('access-view')== FALSE){
				$text_readmore.=JText::_('COM_CONTENT_REGISTER_TO_READ_MORE');
			} elseif ($readmore = $item->alternative_readmore){
				$text_readmore.=$readmore;
				$text_readmore.=JHtml::_('string.truncate', $item->title, $this->params->get('readmore_limit'));
			} elseif ($this->params->get('show_readmore_title', 0) == 0){
				$text_readmore.=JText::sprintf('COM_CONTENT_READ_MORE_TITLE');
			} else {
				$text_readmore.=JText::_('COM_CONTENT_READ_MORE');
				$text_readmore.=JHtml::_('string.truncate', $item->title, $this->params->get('readmore_limit'));
			}
			$text_readmore.='</a></p>';
		} else $text_readmore = '';
		$text_text =  $item->displayIntrotext;
		$copy_template = $this->content_template;
		$copy_template = str_replace( 
			array('@TITLE@', 	'@TEXT@', 	'@READMORE@', 	'@READMORELINK@', 	'@CATEGORY@', 	'@CATEGORYLINK@', 	'@CREATEDATE@', '@MODIFIEDDATE@', 	'@PUBLISHDATE@', 	'@AUTHOR@', 	'@HITS@'),
			array($text_title,	$text_text,	$text_readmore,	$text_link,			$cat_text,		$cat_text_url,		$date_create,	$date_modified,		$date_publish_up,	$text_author,	$text_hits),
			$copy_template
		);
		
		$return_html.= $copy_template;
		//$return_html.="\n";
		return $return_html;
	}
	
	function prepare_template(){
		if($this->getInt('enable_template') && $this->getString('content_template') ){
			$this->content_template = $this->getString('content_template');
		} else {
			$show_hits 		= $this->getInt('show_hits', 0);
			$show_author 	= $this->getInt('show_author', 0);
			$show_category 	= $this->getInt('show_category', 0);
			$link_titles 	= $this->getInt('link_titles', 0);
			$show_date_create 		= $this->getInt('show_date_create', 0);
			$show_date_modified 	= $this->getInt('show_date_modified', 0);
			$show_date_publish_up 	= $this->getInt('show_date_publish_up', 0);
			$item_heading = str_replace( array('/', '<', '>', ' '), '', $this->params->get('item_heading', 'h3'));
			if($item_heading=='') $item_heading = 'h3';
			$this->content_template .= '<'.$item_heading.'>';
			if($link_titles) $this->content_template .= '<a href="@READMORELINK@">';
			$this->content_template .= '@TITLE@';
			if($link_titles) $this->content_template .= '</a>';
			$this->content_template .='</'.$item_heading.'>';
			$cat_tag = ($this->getInt('link_category',0)?'@CATEGORYLINK@':'@CATEGORY@');
			if($show_hits || $show_author || $show_category || $show_date_create || $show_date_modified || $show_date_publish_up){
				$this->content_template .= '<dl class="article-info" style="clear: both;">';
				if($show_category) 			$this->content_template .= '<dd class="category-name">'.$cat_tag.'</dd>';
				if($show_date_create) 		$this->content_template .= '<dd class="create">@CREATEDATE@</dd>';
				if($show_date_modified) 	$this->content_template .= '<dd class="modified">@MODIFIEDDATE@</dd>';
				if($show_date_publish_up) 	$this->content_template .= '<dd class="published">@PUBLISHDATE@</dd>';
				if($show_author)			$this->content_template .= '<dd class="createdby">@AUTHOR@</dd>';
				if($show_hits ) 			$this->content_template .= '<dd class="hits">@HITS@</dd>';
				$this->content_template .= '</dl>';
			}
			// $mytext = '@TEXT@';
			// $mytext = str_replace("<img", "<div class='myimgclass'><img", $mytext);
			



			if ($this->params->get('show_introtext')) $this->content_template .= '@TEXT@';
			if ($this->params->get('show_readmore') ) $this->content_template .= '@READMORE@';
		}
	}
}
