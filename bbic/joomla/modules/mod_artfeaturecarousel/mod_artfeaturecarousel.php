<?php
/**
* @module   Art Feature Carousel
* @copyright  Copyright (C) 2010 artetics.com
* @license    GPL
*/

defined('_JEXEC') or die('Restricted access');
error_reporting(E_ERROR);
define ("DS", DIRECTORY_SEPARATOR);

$moduleId = $module->id;

// $loadJQuery = $params->get('loadJQuery', 1);
$loadJQuery = false;
$autoPlay = $params->get('autoPlay', 0);
$width = $params->get('width', 960);
$height = $params->get('height', 280);
$largeFeatureWidth = $params->get('largeFeatureWidth', 0);
$largeFeatureHeight = $params->get('largeFeatureHeight', 0);
$smallFeatureWidth = $params->get('smallFeatureWidth', 0);
$smallFeatureHeight = $params->get('smallFeatureHeight', 0);
$linksNewWindow = $params->get('linksNewWindow', 0);
$smallFeatureOffset = $params->get('smallFeatureOffset', 50);
$topPadding = $params->get('topPadding', 20);
$runCarousel = $params->get('runCarousel', 0);
$lightbox = $params->get('lightbox', 0);  
$document = &JFactory::getDocument();
?>
<style type="text/css">
.featureCarousel {
  width:<?php echo $width;?>px;
  height: <?php echo $height;?>px;
}
</style>
<?php
if ($loadJQuery) {
  $document->addScript( JURI::root() . 'modules/mod_artfeaturecarousel/js/jquery.js' );
}
switch ($lightbox) {
  case 'artsexylightbox':
    $document->addScript(JURI::root() . 'plugins/content/artsexylightbox/artsexylightbox/js/jquery.easing.1.3.js');
    $document->addScript(JURI::root() . 'plugins/content/artsexylightbox/artsexylightbox/js/script.v2.3.4.js');
    $document->addStyleSheet( JURI::root() . 'plugins/content/artsexylightbox/artsexylightbox/css/sexylightbox.css' );
    $document->addScript(JURI::root() . 'plugins/content/artsexylightbox/artsexylightbox/js/jquery.nc.js');
    ?>
    <script type="text/javascript" charset="utf-8">asljQuery(function(){asljQuery(document).ready(function(){SexyLightbox.initialize({"imagesdir": "<?php echo JURI::BASE(); ?>plugins/content/artsexylightbox/artsexylightbox/images","find": "featureimage"});})});</script>
    <?php
  break;
  case 'artcolorbox':
    $document->addScript( JURI::root() . 'plugins/content/artcolorbox/artcolorbox/js/jquery.colorbox-min.js' );
    $document->addScript( JURI::root() . 'plugins/content/artcolorbox/artcolorbox/js/jquery.nc.js' );
    $document->addStyleSheet( JURI::root() . 'plugins/content/artcolorbox/artcolorbox/css/themes/1/colorbox.css' );
    ?>
    <script type="text/javascript" charset="utf-8">acbjQuery(document).ready(function(){acbjQuery("a[rel^='featureimage']").colorbox({});});</script>
    <?php
  break;
  case 'artprettyphoto':
    $document->addScript(JURI::root() . 'plugins/content/artprettyphoto/artprettyphoto/js/jquery.prettyPhoto.js');
    $document->addStyleSheet( JURI::root() . 'plugins/content/artprettyphoto/artprettyphoto/css/prettyPhoto.css');
    ?>
    <script type="text/javascript" charset="utf-8">jQuery(document).ready(function(){jQuery("a[rel^='featureimage']").prettyPhoto({theme:"facebook"});});</script>
    <?php
  break;
  default:
  break;
}
$document->addScript( JURI::root() . 'modules/mod_artfeaturecarousel/js/jquery.featureCarousel.js' );
$document->addScript( JURI::root() . 'modules/mod_artfeaturecarousel/js/jquery.nc.js' );
$document->addStyleSheet( JURI::root() . 'modules/mod_artfeaturecarousel/css/featureCarousel.css' );
echo '<div id="featureCarousel' . $moduleId . '" class="featureCarousel">';
for ($i = 1; $i <=25; $i++) {
  $image = $params->get('image' . $i, '');
  if ($image) {
    $link = $params->get('link' . $i, '');
    $description = $params->get('description' . $i, '');
    $imgsz = getimagesize($image);
    //$size_string = $imgsz[0];
    $w = $imgsz[0];
    $h = $imgsz[1];
    
    $ratio = $w/$h;
    $largeFeatureHeight = $height;
    $largeFeatureWidth = $largeFeatureHeight*$ratio;
    // $largeFeatureWidth = $largeFeatureHeight*$ratio;
    $smallFeatureWidth = $largeFeatureWidth*0.75;
    $smallFeatureHeight = $largeFeatureHeight*0.75;
    $smallFeatureOffset = $largeFeatureHeight*0.1;
    echo '<div class="feature">';
          if ($link) {
            if ($lightbox) {
              echo '<a rel="featureimage" href="' . $image . '"';
            } else {
              echo '<a href="' . $link . '"';
            }
            if ($linksNewWindow) {
              echo ' target="_blank"';
            }
            echo ' ><img alt="Image Caption" src="' . $image . '" /></a>';
          } else {
            if ($lightbox) {
              echo '<a rel="featureimage" href="' . $image . '">';
              echo '<img alt="Image Caption" src="' . $image . '" /></a>';
            } else {
              echo '<img alt="Image Caption" src="' . $image . '" />';
            }
          }
      echo '<div>';
          if ($description) {
      echo '<p>' . $description . '</p>';
          }
          
    echo '</div>
    </div>';
  }
}
echo '</div>';
if ($runCarousel) {
?>
<script type="text/javascript">
fcjQuery(window).load(function() {
  fcjQuery("#featureCarousel<?php echo $moduleId; ?>").featureCarousel({
  <?php if ($autoPlay) {
    echo 'autoPlay:' . $autoPlay . ',';
  }
  ?>
  <?php echo 'largeFeatureWidth:' . $largeFeatureWidth; ?>,
  <?php echo 'largeFeatureHeight:' . $largeFeatureHeight; ?>,
  <?php echo 'smallFeatureWidth:' . $smallFeatureWidth; ?>,
  <?php echo 'smallFeatureHeight:' . $smallFeatureHeight; ?>,
  <?php echo 'smallFeatureOffset:' . $smallFeatureOffset; ?>,
  <?php echo 'topPadding:' . $topPadding; ?>
  });
});
</script>
<?php } else {
?>
<script type="text/javascript">
fcjQuery(document).ready(function() {
  fcjQuery("#featureCarousel<?php echo $moduleId; ?>").featureCarousel({
  <?php if ($autoPlay) {
    echo 'autoPlay:' . $autoPlay . ',';
  }
  ?>
  <?php echo 'largeFeatureWidth:' . $largeFeatureWidth; ?>,
  <?php echo 'largeFeatureHeight:' . $largeFeatureHeight; ?>,
  <?php echo 'smallFeatureWidth:' . $smallFeatureWidth; ?>,
  <?php echo 'smallFeatureHeight:' . $smallFeatureHeight; ?>,
  <?php echo 'smallFeatureOffset:' . $smallFeatureOffset; ?>,
  <?php echo 'topPadding:' . $topPadding; ?>
  });
});
</script>
<?php
}
?>