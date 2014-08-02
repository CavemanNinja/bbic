<?php
/**
 * @package   T3 Blank
 * @copyright Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>

<!-- MAIN NAVIGATION -->
<nav id="t3-mainnav" class="wrap navbar usernav navbar-default t3-mainnav">
	<div class="container">

		<!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
		
		<!-- USER MENU -->

		<?php if ($this->getParam('navigation_collapse_enable', 1) && $this->getParam('responsive', 1)) : ?>
				<?php $this->addScript(T3_URL.'/js/nav-collapse.js'); ?>
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".t3-navbar-usermenu">
					<i class="fa fa-bars"></i>
						<?php if (JFactory::getLanguage()->get('tag') == "en-GB") : ?>
					    	Toggle User Menu
		 				<?php else : ?>
		 					اظهار قائمة المستخدم
		 				<?php endif; ?>
				</button>
			<?php endif ?>

			<?php if ($this->getParam('addon_offcanvas_enable')) : ?>
				<?php $this->loadBlock ('off-canvas') ?>
			<?php endif ?>



		</div>

		<?php if ($this->getParam('navigation_collapse_enable')) : ?>
			<div class="t3-navbar-usermenu t3-navbar-collapse navbar-collapse collapse"></div>
		<?php endif ?>
		
		<div class="t3-navbar navbar-collapse collapse">
			<jdoc:include type="<?php echo $this->getParam('navigation_type', 'megamenu') ?>" name="usermenu" menutype="usermenu" />
		</div>

	</div>
</nav>
<!-- //MAIN NAVIGATION -->

