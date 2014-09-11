<?php
/**
 * Plugin Helper File
 *
 * @package         Components Anywhere
 * @version         1.3.1
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2014 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';
require_once JPATH_PLUGINS . '/system/nnframework/helpers/text.php';
require_once JPATH_PLUGINS . '/system/nnframework/helpers/tags.php';
require_once JPATH_PLUGINS . '/system/nnframework/helpers/protect.php';

NNFrameworkFunctions::loadLanguage('plg_system_componentsanywhere');

/**
 * Plugin that places components
 */
class plgSystemComponentsAnywhereHelper
{
	public function __construct(&$params)
	{
		$this->option = JFactory::getApplication()->input->get('option');

		$this->params = $params;
		$this->params->comment_start = '<!-- START: Components Anywhere -->';
		$this->params->comment_end = '<!-- END: Components Anywhere -->';
		$this->params->message_start = '<!--  Components Anywhere Message: ';
		$this->params->message_end = ' -->';
		$this->params->protect_start = '<!-- START: CA_PROTECT -->';
		$this->params->protect_end = '<!-- END: CA_PROTECT -->';

		$this->params->tag = preg_quote($this->params->component_tag, '#');

		$bts = '((?:<p(?: [^>]*)?>)?)((?:\s*<br ?/?>)?\s*)';
		$bte = '(\s*(?:<br ?/?>\s*)?)((?:</p>)?)';
		$regex = '((?:\{div(?: [^\}]*)\})?)(\s*)'
			. '\{' . $this->params->tag . '(?: |&nbsp;|&\#160;)([^\{\}\[][^\}]*?)\}'
			. '(\s*)((?:\{/div\})?)';
		$this->params->regex = '#' . $bts . $regex . $bte . '#s';
		$this->params->regex2 = '#' . $regex . '#s';

		$this->params->protected_tags = array(
			$this->params->component_tag
		);

		$this->params->message = '';

		$this->aid = JFactory::getUser()->getAuthorisedViewLevels();

		$this->cache = JFactory::getCache('plugin_componentsanywhere', 'output');

		$this->params->disabled_components = array('com_acymailing');
	}

	public function onContentPrepare(&$article, &$context)
	{
		$area = isset($article->created_by) ? 'articles' : 'other';


		NNFrameworkHelper::processArticle($article, $context, $this, 'processComponents', array($area));
	}

	public function onAfterDispatch()
	{
		// only in html
		if (JFactory::getDocument()->getType() !== 'html' && JFactory::getDocument()->getType() !== 'feed')
		{
			return;
		}

		$buffer = JFactory::getDocument()->getBuffer('component');

		if (empty($buffer) || is_array($buffer))
		{
			return;
		}

		if (JFactory::getApplication()->input->get('rendercomponent'))
		{
			$doc = JFactory::getDocument();
			$ret = new stdClass;
			$ret->script = $doc->_script;
			$ret->scripts = $doc->_scripts;
			$ret->style = $doc->_style;
			$ret->styles = $doc->_styleSheets;
			$ret->html = $buffer;
			$ret->token = JFactory::getSession()->getFormToken();
			echo json_encode($ret);
			die();
		}

		$this->replaceTags($buffer, 'component');

		JFactory::getDocument()->setBuffer($buffer, 'component');
	}

	public function onAfterRender()
	{
		// only in html and feeds
		if (JFactory::getDocument()->getType() !== 'html' && JFactory::getDocument()->getType() !== 'feed')
		{
			return;
		}

		$html = JResponse::getBody();
		if ($html == '')
		{
			return;
		}

		if (JFactory::getDocument()->getType() != 'html')
		{
			$this->replaceTags($html, 'body');
		}
		else
		{
			// only do stuff in body
			list($pre, $body, $post) = nnText::getBody($html);
			$this->replaceTags($body, 'body');

			if (strpos($pre, '</head>') !== false || strpos($body, '<!-- CA HEAD START') !== false)
			{
				if (preg_match_all('#<!-- CA HEAD START STYLES -->(.*?)<!-- CA HEAD END STYLES -->#s', $body, $matches, PREG_SET_ORDER) > 0)
				{
					$styles = '';
					foreach ($matches as $match)
					{
						$styles .= $match['1'];
						$body = str_replace($match['0'], '', $body);
					}
					preg_match('#<link [^>]+templates/#', $body, $add_before) || $add_before = '</head>';
					$pre = str_replace($add_before, $styles . $add_before, $pre);
				}

				if (preg_match_all('#<!-- CA HEAD START SCRIPTS -->(.*?)<!-- CA HEAD END SCRIPTS -->#s', $body, $matches, PREG_SET_ORDER) > 0)
				{
					$scripts = '';
					foreach ($matches as $match)
					{
						$scripts .= $match['1'];
						$body = str_replace($match['0'], '', $body);
					}
					preg_match('#<script [^>]+templates/#', $body, $add_before) || $add_before = '</head>';
					$pre = str_replace($add_before, $scripts . $add_before, $pre);
				}
			}
			$html = $pre . $body . $post;
		}

		$this->cleanLeftoverJunk($html);

		JResponse::setBody($html);
	}

	function replaceTags(&$str, $area = 'article')
	{
		if (!is_string($str) || $str == '')
		{
			return;
		}

		if ($area == 'component')
		{
			// allow in component?
			if (in_array(JFactory::getApplication()->input->get('option'), $this->params->disabled_components))
			{
				$this->protectTags($str);

				return;
			}
		}

		if (strpos($str, '{' . $this->params->component_tag) === false)
		{
			return;
		}

		$this->protect($str);

		$this->params->message = '';

		// COMPONENT
		if (JFactory::getDocument()->getType() == 'feed')
		{
			$s = '#(<item[^>]*>)#s';
			$str = preg_replace($s, '\1<!-- START: COMA_COMPONENT -->', $str);
			$str = str_replace('</item>', '<!-- END: COMA_COMPONENT --></item>', $str);
		}
		if (strpos($str, '<!-- START: COMA_COMPONENT -->') === false)
		{
			$this->tagArea($str, 'COMA', 'component');
		}

		$this->params->message = '';

		$components = $this->getTagArea($str, 'COMA', 'component');

		foreach ($components as $component)
		{
			$this->processComponents($component['1'], 'components');
			$str = str_replace($component['0'], $component['1'], $str);
		}

		// EVERYWHERE
		$this->processComponents($str, 'other');

		NNProtect::unprotect($str);
	}

	function tagArea(&$str, $ext = 'EXT', $area = '')
	{
		if ($str && $area)
		{
			$str = '<!-- START: ' . strtoupper($ext) . '_' . strtoupper($area) . ' -->' . $str . '<!-- END: ' . strtoupper($ext) . '_' . strtoupper($area) . ' -->';
			if ($area == 'article_text')
			{
				$str = preg_replace('#(<hr class="system-pagebreak".*?/>)#si', '<!-- END: ' . strtoupper($ext) . '_' . strtoupper($area) . ' -->\1<!-- START: ' . strtoupper($ext) . '_' . strtoupper($area) . ' -->', $str);
			}
		}
	}

	function getTagArea(&$str, $ext = 'EXT', $area = '')
	{
		$matches = array();
		if ($str && $area)
		{
			$start = '<!-- START: ' . strtoupper($ext) . '_' . strtoupper($area) . ' -->';
			$end = '<!-- END: ' . strtoupper($ext) . '_' . strtoupper($area) . ' -->';
			$matches = explode($start, $str);
			array_shift($matches);
			foreach ($matches as $i => $match)
			{
				list($text) = explode($end, $match, 2);
				$matches[$i] = array(
					$start . $text . $end,
					$text
				);
			}
		}

		return $matches;
	}

	function processComponents(&$string, $area = 'articles')
	{

		if (preg_match('#\{' . $this->params->tag . '#', $string))
		{
			self::replace($string, $this->params->regex, $area);
			self::replace($string, $this->params->regex2, $area);
		}
	}

	function replace(&$string, $regex, $area = 'articles')
	{
		if (@preg_match($regex . 'u', $string))
		{
			$regex .= 'u';
		}

		$matches = array();
		$count = 0;

		$protects = array();
		while ($count++ < 10 && preg_match('#\{' . $this->params->tag . '#', $string) && preg_match_all($regex, $string, $matches, PREG_SET_ORDER) > 0)
		{
			foreach ($matches as $match)
			{
				if (!$this->processMatch($string, $match, $area))
				{
					$protected = $this->params->protect_start . base64_encode($match['0']) . $this->params->protect_end;
					$string = str_replace($match['0'], $protected, $string);
					$protects[] = array($match['0'], $protected);
				}
			}
			$matches = array();
		}
		foreach ($protects as $protect)
		{
			$string = str_replace($protect['1'], $protect['0'], $string);
		}
	}

	function processMatch(&$string, &$match, $area = 'articles')
	{
		$html = '';
		if ($this->params->message != '')
		{
			if ($this->params->place_comments)
			{
				$html = $this->params->message_start . $this->params->message . $this->params->message_end;
			}
		}
		else
		{
			if (count($match) < 9)
			{
				array_unshift($match, $match['0'], '');
				$match['2'] = '';
				array_push($match, '', '');
			}

			$p_start = $match['1'];
			$br1a = $match['2'];
			$div_start = $match['3'];
			$br2a = $match['4'];
			$id = trim($match['5']);
			$br2a = $match['6'];
			$div_end = $match['7'];
			$br2b = $match['8'];
			$p_end = $match['9'];

			$id = trim($id);

			// Handle multiple attribute syntaxes
			$id = str_replace(
				array('|cache', '|keepurl', '|keepurlss'),
				array('|caching', '|keepurls', '|keepurls'),
				$id
			);
			$tag = NNTags::getTagValues($id, array('url'));

			foreach ($tag->params as $param)
			{
				$tag->{$param} = 1;
			}
			unset($tag->params);

			$tag->keepurls = isset($tag->keepurls) ? $tag->keepurls : $this->params->keepurls;
			$tag->caching = isset($tag->caching) ? $tag->caching : $this->params->caching;

			$html = $this->processComponent($tag, $area);

			if ($p_start && $p_end)
			{
				$p_start = '';
				$p_end = '';
			}

			$html = $br1a . $br2a . $html . $br2a . $br2b;

			if ($div_start)
			{
				$extra = trim(preg_replace('#\{div(.*)\}#si', '\1', $div_start));
				$div = '';
				if ($extra)
				{
					$extra = explode('|', $extra);
					$extras = new stdClass;
					foreach ($extra as $e)
					{
						if (strpos($e, ':') !== false)
						{
							list($key, $val) = explode(':', $e, 2);
							$extras->$key = $val;
						}
					}
					if (isset($extras->class))
					{
						$div .= 'class="' . $extras->class . '"';
					}

					$style = array();
					if (isset($extras->width))
					{
						if (is_numeric($extras->width))
						{
							$extras->width .= 'px';
						}
						$style[] = 'width:' . $extras->width;
					}
					if (isset($extras->height))
					{
						if (is_numeric($extras->height))
						{
							$extras->height .= 'px';
						}
						$style[] = 'height:' . $extras->height;
					}
					if (isset($extras->align))
					{
						$style[] = 'float:' . $extras->align;
					}
					else if (isset($extras->float))
					{
						$style[] = 'float:' . $extras->float;
					}

					if (!empty($style))
					{
						$div .= ' style="' . implode(';', $style) . ';"';
					}
				}
				$html = trim('<div ' . trim($div)) . '>' . $html . '</div>';

				$html = $p_end . $html . $p_start;
			}
			else
			{
				$html = $p_start . $html . $p_end;
			}

			$html = preg_replace('#((?:<p(?: [^>]*)?>\s*)?)((?:<br ?/?>)?\s*<div(?: [^>]*)?>.*?</div>\s*(?:<br ?/?>)?)((?:\s*</p>)?)#', '\3\2\1', $html);
			$html = preg_replace('#(<p(?: [^>]*)?>\s*)<p(?: [^>]*)?>#', '\1', $html);
			$html = preg_replace('#(</p>\s*)</p>#', '\1', $html);
		}

		if ($this->params->place_comments)
		{
			$html = $this->params->comment_start . $html . $this->params->comment_end;
		}

		$string = str_replace($match['0'], $html, $string);
		unset($match);

		return 1;
	}

	function processComponent($tag, $area = '')
	{
		$url = ltrim(html_entity_decode($tag->url), '/');

		$pagination_stuff = array('p', 'page', 'limitstart', 'start', 'filter', 'filter-search');
		$full_url = $url;
		foreach ($pagination_stuff as $key)
		{
			if (!isset($_GET[$key]))
			{
				continue;
			}

			$full_url .= (strpos($url, '?') === false) ? '?' : '&';
			$full_url .= $key . '=' . $_GET[$key];
		}

		$data = $this->getByURL($full_url, $tag->caching);

		if (!$data || $data == '{}')
		{
			if ($this->params->place_comments)
			{
				return $this->params->message_start . JText::_('CA_OUTPUT_REMOVED_INVALID') . $this->params->message_end;
			}

			return '';
		}

		// remove possible leading encoding  characters
		$data = preg_replace('#^.*?\{#', '{', $data);

		$data = json_decode($data);

		if (!isset($data->html))
		{
			if ($this->params->place_comments)
			{
				return $this->params->message_start . JText::_('CA_OUTPUT_REMOVED_INVALID') . $this->params->message_end;
			}

			return '';
		}

		$this->addScriptsAndStyles($data, $area);

		$uri = JURI::getInstance();
		$path = $uri->getPath();
		$path .= (strpos($path, '?') === false) ? '?' : '&';

		// Remove tmpl and rendercomponent parameters that have possibly been added to urls by the component
		$data->html = preg_replace('#((?:\?|&(?:amp;)?)tmpl=component)?&(?:amp;)?rendercomponent=1#si', '', $data->html);
		if ($this->params->force_remove_tmpl)
		{
			$data->html = preg_replace('#(?:\?|&(?:amp;)?)tmpl=component#si', '', $data->html);
		}
		$data->html = preg_replace('#\?&(?:amp;)#si', '?', $data->html);

		// Replace the form token with the current correct one
		$data->html = str_replace($data->token, JFactory::getSession()->getFormToken(), $data->html);

		// Replace the return values in urls
		$data->html = preg_replace('#(\?|&(amp;)?)return=([a-z0-9=]+)#i', '\1return=' . base64_encode($uri->toString()), $data->html);

		if(!$tag->keepurls)
		{
			// Replace urls
			$data->html = str_replace(
				array(
					$url,
					JRoute::_($url),
					JRoute::_($url, 0)
				),
				$path,
				$data->html
			);
			// Also get non-sef matches
			$url_regex = str_replace('&', '&(?:amp;)?', str_replace('index.php', '', $url));
			$data->html = preg_replace('#"[^"]+' . $url_regex . '("|&)#si', '"' . $path . '\1', $data->html);
		}

		return $data->html;
	}

	function addScriptsAndStyles(&$data, $area = '')
	{
		// add set scripts and styles to current jdoc
		$doc = JFactory::getDocument();
		$this->removeDuplicatesFromObject($data->styles, $doc->_styleSheets);
		$this->removeDuplicatesFromObject($data->style, $doc->_style, 1);
		$this->removeDuplicatesFromObject($data->scripts, $doc->_scripts);
		$this->removeDuplicatesFromObject($data->script, $doc->_script, 1);

		if ($area == 'articles')
		{
			foreach ($data->styles as $style => $attr)
			{
				$doc->addStyleSheet($style, $attr->mime, $attr->media, $attr->attribs);
			}
			foreach ($data->style as $type => $content)
			{
				$doc->addStyleDeclaration($content, $type);
			}
			foreach ($data->scripts as $script => $attr)
			{
				$doc->addScript($script, $attr->mime, $attr->defer, $attr->async);
			}
			foreach ($data->script as $type => $content)
			{
				$doc->addScriptDeclaration($content, $type);
			}

			return;
		}

		$inline_head_styles = array();
		$inline_head_scripts = array();

		// Generate stylesheet links
		foreach ($data->styles as $style => $attr)
		{
			$inline_head_styles[] = $this->styleToString($style, $attr) . "\n";
		}

		// Generate stylesheet declarations
		foreach ($data->style as $type => $content)
		{
			$inline_head_styles[] = '<style type="' . $type . '">' . "\n"
				. $content . "\n"
				. $inline_head[] = '</style>' . "\n";
		}

		// Generate script file links
		foreach ($data->scripts as $script => $attr)
		{
			$inline_head_scripts[] = $this->scriptToString($script, $attr) . "\n";
		}

		// Generate script declarations
		foreach ($data->script as $type => $content)
		{
			$inline_head_scripts[] = '<script type="' . $type . '">' . "\n"
				. $content . "\n"
				. '</script>' . "\n";
		}

		if (!empty($inline_head_styles))
		{
			$data->html = '<!-- CA HEAD START STYLES -->' . implode('', $inline_head_styles) . '<!-- CA HEAD END STYLES -->' . $data->html;
		}

		if (!empty($inline_head_scripts))
		{
			$data->html = '<!-- CA HEAD START SCRIPTS -->' . implode('', $inline_head_scripts) . '<!-- CA HEAD END SCRIPTS -->' . $data->html;
		}
	}

	function styleToString($style, $attr)
	{
		$string = '<link rel="stylesheet" href="' . $style . '" type="' . $attr->mime . '"';

		$string .= !is_null($attr->media) ? ' media="' . $attr->media . '"' : '';
		$string = trim($string . ' ' . JArrayHelper::toString($attr->attribs));

		$string .= ' />';

		return $string;
	}

	function scriptToString($script, $attr)
	{
		$string = '<script src="' . $script . '"';

		$string .= !is_null($attr->mime) ? ' type="' . $attr->mime . '"' : '';
		$string .= $attr->defer ? ' defer="defer"' : '';
		$string .= $attr->async ? ' async="async"' : '';

		$string .= '></script>';

		return $string;
	}

	function removeDuplicatesFromObject(&$obj, $doc, $match_value = 0)
	{
		foreach ($obj as $key => $val)
		{
			if (isset($doc[$key]) && (!$match_value || $doc[$key] == $val))
			{
				unset($obj->{$key});
			}
		}
	}

	function protect(&$str)
	{
		NNProtect::protectFields($str);
		NNProtect::protectSourcerer($str);
	}

	function protectTags(&$str)
	{
		NNProtect::protectTags($str, $this->params->protected_tags);
	}

	function unprotectTags(&$str)
	{
		NNProtect::unprotectTags($str, $this->params->protected_tags);
	}

	/**
	 * Just in case you can't figure the method name out: this cleans the left-over junk
	 */
	function cleanLeftoverJunk(&$str)
	{
		$this->unprotectTags($str);

		$str = preg_replace('#<\!-- (START|END): COMA_[^>]* -->#', '', $str);
		if (!$this->params->place_comments)
		{
			$str = str_replace(
				array(
					$this->params->comment_start, $this->params->comment_end,
					htmlentities($this->params->comment_start), htmlentities($this->params->comment_end),
					urlencode($this->params->comment_start), urlencode($this->params->comment_end)
				), '', $str
			);
			$str = preg_replace('#' . preg_quote($this->params->message_start, '#') . '.*?' . preg_quote($this->params->message_end, '#') . '#', '', $str);
		}
	}

	function getByURL($url, $cache)
	{
		$cacheid = $url;
		$url = JURI::base() . $url . (strpos($url, '?') === false ? '?' : '&') . 'tmpl=component&rendercomponent=1';

		if ($cache)
		{
			$this->cache->setCaching(1);
			$html = $this->cache->get($cacheid);
			if ($html)
			{
				$this->cache->store($html, $cacheid);

				return $html;
			}
		}

		$html = $this->getHtmlByURL($url);

		if (!$html)
		{
			$html = '{}';
		}

		if ($cache)
		{
			$this->cache->store($html, $cacheid);
		}

		return $html;
	}

	function getHtmlByURL($url)
	{
		// Grab cookies
		$cookies = array();
		foreach ($_COOKIE as $k => $v)
		{
			// Only include hexadecimal keys
			if (!preg_match('#^[a-f0-9]+$#si', $k))
			{
				continue;
			}

			$cookies[] = $k . '=' . $v;
		}

		if (!($html = $this->getURLByGetContents($url, $cookies))
			&& !($html = $this->getURLByFopen($url, $cookies))
			&& !($html = $this->getURLByCurl($url, $cookies))
		)
		{
			return false;
		}

		return $html;
	}

	function getURLByGetContents($url, $cookies)
	{
		return @file_get_contents($url . '&' . implode('&', $cookies));
	}

	function getURLByFopen($url, $cookies)
	{

		if (!ini_get('allow_url_fopen'))
		{
			return false;
		}

		$file = @fopen($url . '&' . implode('&', $cookies), 'r');
		if (!$file)
		{
			return false;
		}

		$html = array();

		// using do/while to prevent infinite loop
		do
		{
			$html[] = fgets($file, 4096);
		}
		while (!feof($file));

		if (empty($html))
		{
			return false;
		}

		return implode('', $html);
	}

	function getURLByCurl($url, $cookies)
	{
		if (!function_exists('curl_init'))
		{
			return false;
		}

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, 'NoNumber/0.0');
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->params->timeout);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->params->timeout);

		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 10); // stop after 10 redirects

		if (!empty($cookies))
		{
			curl_setopt($ch, CURLOPT_COOKIESESSION, false); // False to keep all cookies of previous session
			curl_setopt($ch, CURLOPT_COOKIE, implode(';', $cookies));
		}

		if (!empty($_POST))
		{
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST);
		}

		// Send authentication
		$username = "";
		$password = "";
		if (isset($_SERVER['PHP_AUTH_USER']))
		{
			$username = $_SERVER['PHP_AUTH_USER'];
			$password = $_SERVER['PHP_AUTH_PW'];
		}
		elseif (isset($_SERVER['HTTP_AUTHENTICATION']))
		{
			if (strpos(strtolower($_SERVER['HTTP_AUTHENTICATION']), 'basic') === 0)
			{
				list($username, $password) = explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
			}
		}
		if ($username != '' && $password != '')
		{
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
			curl_setopt($ch, CURLOPT_USERPWD, $username . ':' . $password); // set referer on redirect
		}

		$html = curl_exec($ch);
		curl_close($ch);

		return $html;
	}
}
