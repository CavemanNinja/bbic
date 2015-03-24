<?php
/**
* @package 2JToolBox 2JTabs
* @Copyright (C) 2012 2Joomla.net
* @ All rights reserved
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version $Revision: 2.0.0 $
**/
defined('_JEXEC') or die;

class TwoJToolBoxTabs extends TwoJToolBoxPlugin{
	protected $css_list=array('tabs');
	protected $js_list=array('core', 'tabs');
	protected $delete_color_char = 0;
	protected $content_plugin = 1;
	
	protected $tabs_body_html = '';
	protected $tabs_header_html = '';
	
	protected $browser;
	protected $zindex;
	
	protected $tab_align = '';
	protected $tab_navigation = '';
	
	protected $tabs_coutn = 0;
	
	protected $titleintab = 0;
	protected $custom_label = '';
	protected $tab_template = '';
	protected $tab_title_length = 0;
	
	protected $block_class = '';
	
	protected $content_template = '';
	
	protected $element_div_style = array();
	protected $element_li_style = array();
	protected $element_ul_style = array();
	protected $element_all_style = array();
	protected $element_a_style = array();
	protected $element_span_style = array();
	
	protected $revers_tab = 1;
	protected $addsccript = '';
	
	protected $hideUnselectTabs = 0;
	
	
	public function includeLib(){
		jimport( 'joomla.environment.browser' );
		$this->browser = JBrowser::getInstance();
		if( $this->browser->getBrowser()=='msie' && $this->browser->getVersion()=='6.0' ) $this->css_list[] = 'tabs.ie6';
		
		$css_file = $this->getString('css_file', '2j.style1.tabs.css');
		if($css_file){
			$css_file = preg_replace ("/^2j\.(.*)\.css$/", "$1", $css_file);
			$this->css_list[] = $css_file;
			if( preg_match("/^style([0-9]{1,5})\.tabs$/", $css_file) ) $css_file = 'twoj_tabs_class'.preg_replace ("/^style([0-9]{1,5})\.tabs$/", "$1", $css_file);
			$this->block_class .= $css_file;
			
			if( $css_file=='twoj_tabs_class3' || $css_file=='twoj_tabs_class2' ) $this->revers_tab = 0; //temp
			
			parent::includeLib();
		}
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
		
		$this->uniqueid = $this->getuniqueid();
		
		$this->hideUnselectTabs = $this->getInt( 'hideUnselectTabs', 0 );
		
		$return_text = '';
		$this->insertString( 'event',	'click');
		$duration 		= $this->getInt( 'duration', 200 );
		if($duration) $duration = ", duration: ".$duration; else $duration = '';

		switch( $this->getInt( 'effect',	0) ){
			case '1': $this->addGenOption("fx: { height: 'toggle'".$duration."} ");
				break;
			case '2': $this->addGenOption("fx: { opacity:'toggle'".$duration."} ");
				break;
			case '3':  $this->addGenOption("fx: { height: 'toggle', opacity: 'toggle'".$duration."} ");
				break;
			
		}
		
		if( $this->getInt( 'timer', 0 ) ) $this->addsccript = '.tabs( "rotate" , '.$this->getInt( 'tdelay', 3000 ).' , 0 )';
		
		$height_content		= $this->getSize( 'height_content', '' );
		if($height_content && $height_content!='0px') {
			$this->addGenOption("'height_constant': 1");
			$this->element_div_style[] = ' height: 	'.$height_content.';';
		}
		
		$select_default	= $this->getInt( 'select_default');
		$this->tab_align = $this->getInt( 'tab_align' );
		if($select_default && $select_default<= $this->tabs_coutn ){
			 $this->addGenOption("'selected': ".($select_default-1));
		} elseif( $this->tab_align && $this->revers_tab ) $this->addGenOption("'selected': ".( $this->tabs_coutn - 1 ));
		
		if ($this->tab_align) {
			if($this->revers_tab){
				if( $this->list && count($this->list) ){
					$this->list = array_reverse($this->list);
					reset($this->list);
				}
			}
			$this->block_class .= ' twoj_tab_block_right';
		}
		
		if( !$this->getInt( 'enable_hover') ) $this->block_class .= ' twoj_tab_block_hover';
		
		$this->tab_navigation = $this->getInt( 'tab_navigation' );
		$this->block_class .= ( $this->tab_navigation ==1  ? ' twoj_tab_block_bottom':'');
		

		$this->element_all_style[] = 'width: '.$this->getSize( 'width' ).'; ';
		if( $this->getString( 'height', '' ) ) $this->element_all_style[] = 'height: '.$this->getSize( 'height' ).'; ';
	
		if( $bg_color =  $this->getColor( 'bg_color', '') ){
			$this->element_li_style[] = 'background-color: '.$bg_color.';';
			$this->element_ul_style[] = 'background-color: '.$bg_color.';';
			$this->element_all_style[] ='background-color: '.$bg_color.';';
			//$this->element_a_style[] = 	'background-color: '.$bg_color.';';
			//$this->element_span_style[]='background-color: '.$bg_color.';';
			$this->element_div_style[]='background-color: '.$bg_color.';';
		}
		

		if( $pending_ul_left = $this->getInt( 'pending_ul_left') )  	$this->element_ul_style[] ='padding-left: '.$pending_ul_left.'px;';
		if( $pending_ul_right = $this->getInt( 'pending_ul_right') )	$this->element_ul_style[] = 'padding-right: '.$pending_ul_right.'px;';
		if( $pending_li_left = $this->getInt( 'pending_li_left') )  	$this->element_li_style[] = 'padding-left: '.$pending_li_left.'px ;';
		if( $pending_li_right = $this->getInt( 'pending_li_right') )	$this->element_li_style[] = 'padding-right: '.$pending_li_right.'px;';
	
		if( $all_pending_top = $this->getInt( 'all_pending_top' ) )		$this->element_all_style[] = 'padding-top: '.$all_pending_top.'px;';
		if( $all_pending_left = $this->getInt( 'all_pending_left' ) )	$this->element_all_style[] = 'padding-left: '.$all_pending_left.'px;';
		if( $all_pending_right = $this->getInt( 'all_pending_right' ) )	$this->element_all_style[] = 'padding-right: '.$all_pending_right.'px;';
		if( $all_pending_bottom = $this->getInt( 'all_pending_bottom' ) )$this->element_all_style[] = 'padding-bottom: '.$all_pending_bottom.'px;';
		
		
		//$this->element_all_style[] = 'margin:0 auto;';
		
		if( $font_tab =  $this->getString( 'font_tab' ) ){
			$this->element_a_style[] 	= 'font-family: '.$font_tab.';';
			$this->element_span_style[] = 'font-family: '.$font_tab.';';
		}
		if( $font_size_tab	=  $this->getString( 'font_size_tab', '' ) ){
			$this->element_a_style[] =	  'font-size: '.$font_size_tab.';';
			$this->element_span_style[] = 'font-size: '.$font_size_tab.' ;';
		}
		

		$show_border = $this->getInt( 'type_border');
		if( $show_border ){
			if ($show_border==2) $this->element_div_style[] = 'border: '.$this->getString( 'border_w').' '.$this->getString( 'border_s').' '.$this->getColor( 'border_color').' ;';
			if ($show_border==1) $this->element_div_style[] = 'border: 0px none;';
		} 
		
		if(  $this->getInt( 'overflow')==0 ) $this->element_div_style[] = 'overflow: hidden;';
		
		
		//tabs title setup
		$this->titleintab	 	= $this->getInt( 'titleintab'); 
		$this->tab_template 	=  $this->getString( 'tab_template'); 
		$this->tab_title_length =  $this->getInt( 'tab_title_length' );
		$this->custom_label		=  $this->getString( 'custom_label');
		$this->custom_label 	= explode( ( strpos( $this->custom_label, "#;")!==false ? "#;" : "\n" ), $this->custom_label);
		
		$this->articles_diplay();
		
		if( !$this->getInt( 'show_comments' ) ) $return_text .= "\n".'<!-- 2JTabs (2JToolBox framework) Start tag http://www.2joomla.net -->'."\n";
		
		if( $this->tabs_body_html){
			$return_text .= $this->getString('pretext');
			$return_text .= '<div id="twoj_tabs_block_id'.$this->uniqueid.'"  class="'.$this->block_class.'" '.$this->getStyleString($this->element_all_style).'>';
			$return_text .= $this->tab_navigation == 0?$this->tabs_header_html:'';
			$return_text .= $this->tabs_body_html;
			$return_text .= $this->tab_navigation == 1?$this->tabs_header_html:'';
			$return_text .= '</div>';
			$return_text .= $this->getString('posttext');
			
			if(!$this->render_content){
				$return_text .= "\n".'<script language="JavaScript" type="text/javascript">';
				$return_text .= "\n".'<!--//<![CDATA[';
				$return_text .= "\n".'emsajax(document).ready(function(){ emsajax("#twoj_tabs_block_id'.$this->uniqueid.'").tabs({'.implode(' ,', $this->gen_option).'})'.$this->addsccript.';'; 
				$return_text .= 'var strippedUrl = document.location.toString().split("#");';
				$return_text .= 'if(strippedUrl.length > 1){';
				$return_text .= '	var anchorvalue = strippedUrl[1];';
				$return_text .= '	if ( /^2jtabs-'.$this->uniqueid.'-([0-9]{1,3})([-]{0,1})(.*)$/.test(anchorvalue) ){';
				$return_text .= '		var matches = /^2jtabs-'.$this->uniqueid.'-([0-9]{1,3})([-]{0,1})(.*)$/.exec(anchorvalue);';
				$return_text .= '		if(matches.length > 1) emsajax("#twoj_tabs_block_id'.$this->uniqueid.'").tabs("select", matches[1]);';
				$return_text .= '	}';
				$return_text .= '}';
				$return_text .= '});';
				$return_text .= "\n".'//]]>-->';
				$return_text .= "\n".'</script>';
			}			
		}
		if( !$this->getInt( 'show_comments' ) )  $return_text .= "\n".'<!-- 2JTabs (2JToolBox framework) End tag http://www.2joomla.net -->'."\n";
		if($return_text) return  $return_text; else return null;
	}
	
	
	function create_tab( $tab_count_link, $title, $content ){
			if( $this->titleintab==1 || ( $title=='' && $this->titleintab==0 ) ){
				if($this->tab_template){
					if ($this->tab_align && $this->revers_tab ) $title = str_replace('#',( $this->tabs_coutn - $tab_count_link),$this->tab_template);
						else $title = str_replace('#', $tab_count_link, $this->tab_template);
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
			$this->tabs_header_html .= '<div class="twoj_tab_block_li" '.$this->getStyleString($this->element_li_style).'>';
				$this->tabs_header_html .= '<div class="twoj_tab_block_a" '.$this->getStyleString($this->element_a_style).'>'; //
					$this->tabs_header_html .= '<span '.$this->getStyleString($this->element_span_style).'>'.$title.'</span>';
					$this->tabs_header_html .= ' <div class="twoj_tab_block_a_index" style="display:none;">#twoj_fragment'.$this->uniqueid.'-'.$tab_count_link.'</div>';
				$this->tabs_header_html .= '</div>';
			$this->tabs_header_html .= '</div>';
			
			$this->tabs_body_html .= '<div id="twoj_fragment'.$this->uniqueid.'-'.$tab_count_link.'" class="twoj_tab_content'.( ($tab_count_link>1 && $this->hideUnselectTabs) ?' twoj-tabs-hide':'').'" '.$this->getStyleString($this->element_div_style).'>';
			$this->tabs_body_html .= $content;
			$this->tabs_body_html .= '<div class="twoj_tab_clr"></div></div>';
		
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
					$this->create_tab( $tab_count, $item->title,  $this->article_diplay($item) );
				}
			}
		}
		$this->tabs_header_html = '<div class="twoj_tab_block_ul" '.$this->getStyleString($this->element_ul_style).'>'.$this->tabs_header_html.'</div>';
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
			if ($this->params->get('show_introtext')) $this->content_template .= '@TEXT@';
			if ($this->params->get('show_readmore') ) $this->content_template .= '@READMORE@';
		}
	}
}
