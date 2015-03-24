<?php
	
	$body_browser_class = '';
	if ( strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox') ) $body_browser_class = 'firefox';
	elseif ( stristr($_SERVER['HTTP_USER_AGENT'], 'Chrome') ) $body_browser_class = 'chrome';
	elseif ( stristr($_SERVER['HTTP_USER_AGENT'], 'Safari') ) $body_browser_class = 'safari';
	elseif ( stristr($_SERVER['HTTP_USER_AGENT'], 'Opera') ) $body_browser_class = 'opera';
	elseif ( stristr($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.0') ) $body_browser_class = 'ie6';
	elseif ( stristr($_SERVER['HTTP_USER_AGENT'], 'MSIE 7.0') ) $body_browser_class = 'ie7';
	elseif ( stristr($_SERVER['HTTP_USER_AGENT'], 'MSIE 8.0') ) $body_browser_class = 'ie8';
	elseif ( stristr($_SERVER['HTTP_USER_AGENT'], 'MSIE 9.0') ) $body_browser_class = 'ie9';
	elseif ( stristr($_SERVER['HTTP_USER_AGENT'], 'MSIE 10.0') ) $body_browser_class = 'ie10';
	elseif ( stristr($_SERVER['HTTP_USER_AGENT'], 'Trident/7') ) $body_browser_class = 'ie11';
?>

<?php

defined('_JEXEC') or die;

$app = JFactory::getApplication();
$doc = JFactory::getDocument();

// Detecting Active Variables
$option   = $app->input->getCmd('option', '');
$itemid   = $app->input->getCmd('Itemid', '');

// Добавить JavaScript Фреймворк Bootsrap
JHtml::_('bootstrap.framework');

// Добавить CSS файлы Фреймворка Bootsrap
JHtml::_( 'bootstrap.loadCss' );

// Подключение файла стилей шаблона
$doc->addStyleSheet('templates/'.$this->template.'/css/template.css');
$doc->addStyleSheet('templates/'.$this->template.'/css/meanmenu.css');
$doc->addStyleSheet('templates/'.$this->template.'/css/slidegallery.css');

// Подключение файла скриптов шаблона
$doc->addScript('templates/'.$this->template.'/js/jquery.meanmenu.js');
$doc->addScript('templates/'.$this->template.'/js/script.js');
?>

<!DOCTYPE html>
<!--[if IEMobile]>
	<html class="iemobile" lang="<?php echo $this->language; ?>">
<![endif]-->
<!--[if IE 7]>
	<html class="no-js ie7 oldie" lang="<?php echo $this->language; ?>">
<![endif]-->
<!--[if IE 8]>
	<html class="no-js ie8 oldie" lang="<?php echo $this->language; ?>">
<![endif]-->
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<jdoc:include type="head" />
	<link rel="apple-touch-icon-precomposed" href="<?php echo $tpath; ?>/57x.png">
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo $tpath; ?>/72x.png">
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo $tpath; ?>/114x.png">
	<!--[if lt IE 9]>
		<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<link href='http://fonts.googleapis.com/css?family=Advent+Pro:400,500,600,700,300,200' rel='stylesheet' type='text/css'>
</head>
<body class="<?= $body_browser_class ?>">
<div class="container wrapper" id="<?php echo ($itemid ? 'itemid-' . $itemid : '');?>">
	<header id="header">
		<div class="container">
			<div class="row-fluid">
				<div class="span6">
					<a href="<?php echo $this->baseurl;?>" class="logo">
						<img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/images/logo.png" alt="">
						<h1> 
							 <?php echo $app->getCfg('sitename');?> 
						</h1>
					</a>
				</div>
				<div class="span6">
					<div id="mean-nav"></div>
					<div id="main-menu">
						<div class="navbar">
							<nav>
								<jdoc:include type="modules" name="position-7" style="none" />
							</nav>
						</div>
					</div>
				</div>
			</div>
		</div>
	</header>
	<div class="content">
		<!-- contact page begin-->
		<div class="container contacts">
			<div class="row-fluid">
				<div class="span12">
					<div class="page-header">
						<h2>
							Contacts
						</h2>
					</div>
					<jdoc:include type="modules" name="map" style="none" />
				</div>
			</div>
		</div>
		<!-- contact page end-->

		<div class="row-fluid slider">
			<div class="span12">
				<!-- slider main begin-->
					<div class="wrap_slider">
						<jdoc:include type="modules" name="position-2" style="none" />
					</div>
				<!-- slider main end-->
			</div>
		</div>

		<!-- begin page about -->
		<div class="container about">
			<div class="row-fluid">
				<div class="span8">
					<jdoc:include type="component" />
				</div>
				<div class="span4">
					<jdoc:include type="modules" name="right" style="none" />
				</div>
			</div>
		</div>
		<!-- begin page end -->
	</div>
	<footer id="footer">
		<div class="container">
			<div class="row-fluid">
				<div class="span6">
					<div class="social">
						<jdoc:include type="modules" name="position-footer" style="none" />
					</div>
				</div>
				<div class="span6">
					<div class="copy">
						<jdoc:include type="modules" name="position-footer-copy" style="none" />
					</div>
				</div>
			</div>
		</div>
	</footer>
</div>
</body>
</html>
