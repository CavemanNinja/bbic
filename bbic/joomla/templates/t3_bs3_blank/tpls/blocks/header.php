<?php
/**
 * @package   T3 Blank
 * @copyright Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// get params
$sitename  = $this->params->get('sitename');
$slogan    = $this->params->get('slogan', '');
$logotype  = $this->params->get('logotype', 'text');
$logoimage = $logotype == 'image' ? $this->params->get('logoimage', 'templates/' . T3_TEMPLATE . '/images/logo.png') : '';
$logoimgsm = ($logotype == 'image' && $this->params->get('enable_logoimage_sm', 0)) ? $this->params->get('logoimage_sm', '') : false;

if (!$sitename) {
	$sitename = JFactory::getConfig()->get('sitename');
}

$logosize = 'col-sm-12';
if ($headright = $this->countModules('head-search or languageswitcherload')) {
	$logosize = 'col-sm-12 col-md-12 col-lg-5';
}

?>

<!-- HEADER -->
<div class="myheaderdiv">
<div class="row langsearch-row">
<div class="container langsearch-container">
		<!-- LANGUAGE SWITCHER & SEARCH -->
		<div class="col-xs-12 langsearch">
			<div class="breadcrumbs">
				<jdoc:include type="modules" name="<?php $this->_p('breadcrumbs') ?>" style="raw" />
			</div>
			<div class="languageswitcherload">
				<jdoc:include type="modules" name="<?php $this->_p('languageswitcherload') ?>" style="raw" />
			</div>
		</div>
		<!-- //LANGUAGE SWITCHER -->
	</div>
</div>

<header id="t3-header" class="container t3-header">
	
	<div class="row">
		<!-- LOGO -->
		<div class="col-xs-12 <?php echo $logosize ?> logo pull-down">
			<div class="logo-<?php echo $logotype, ($logoimgsm ? ' logo-control' : '') ?>">
				<a href="<?php echo JURI::base() ?>" title="<?php echo strip_tags($sitename) ?>">
					<?php if($logotype == 'image'): ?>
						<img class="logo-img pull-down img-responsive col-xs-12 col-sm-12" src="<?php echo JURI::base() . '/' . $logoimage ?>" alt="<?php echo strip_tags($sitename) ?>" />
					<?php endif ?>
					<?php if($logoimgsm) : ?>
						<img class="logo-img-sm pull-down" src="<?php echo JURI::base() . '/' . $logoimgsm ?>" alt="<?php echo strip_tags($sitename) ?>" />
					<?php endif ?>
					<span><?php echo $sitename ?></span>
				</a>
				<small class="site-slogan"><?php echo $slogan ?></small>
			</div>
		</div>
		<!-- //LOGO -->

		<?php if ($headright): ?>
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-7 carousel">
				<?php if ($this->countModules('head-search')) : ?>
					<!-- CAROUSEL-->
					<div class="head-search <?php $this->_c('head-search') ?>">
						<jdoc:include type="modules" name="<?php $this->_p('head-search') ?>" style="raw" />
					</div>
					<!-- //CAROUSEL -->
				<?php endif ?>

				
			</div>
		<?php endif ?>

	</div>
</header>
</div>
<!-- //HEADER -->
