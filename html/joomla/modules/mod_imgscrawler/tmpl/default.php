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
defined('_JEXEC') or die; ?>

<div class="ic_marquee" id="myimgscrawler-<?php echo $imgscrawler_id; ?>">
	<?php foreach ( $images as $image ) { echo $image; } ?>
</div>
<?php

$style = "'width':'".$params->get( 'width' )."',".
		 "'height':'".$params->get( 'height' )."'";
if ( $bgcolor = $params->get( 'bgcolor' ) )
	$style .= ",'background':'".$bgcolor."'";

$savedirection = '';	
switch ($params->get( 'savedirection' )) {
  case 2:
   $savedirection = "'reverse'";
   break;  
  case 0:
   $savedirection = "false";
   break;
  default:
   $savedirection = "true";
   break;    
}

$doc = JFactory::getDocument();
$doc->addScript(JURI::root(true).'/modules/mod_imgscrawler/crawler.js');

$doc->addScriptDeclaration("
window.addEventListener('load', function () {
    marqueeInit({
        uniqueid: 'myimgscrawler-".$imgscrawler_id."',
        style: {
            ".$style."
        },
        inc: ".$params->get( 'speed' ).",
        mouse: '".$params->get( 'mouse' )."',
        direction: '".$params->get( 'direction' )."',
        moveatleast: ".$params->get( 'moveatleast' ).",
        neutral: ".$params->get( 'neutral' ).",
        savedirection: ".$savedirection."
    });
},false);
");