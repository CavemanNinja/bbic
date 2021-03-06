<?php
/**
 * @package   T3 Blank
 * @copyright Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>

<?php if ($this->checkSpotlight('spotlight-2', 'position-5, position-6, position-7, position-8')) : ?>
	<!-- SPOTLIGHT 2 -->
	<?php $base = $this->base; ?>
	<?php if (strpos($base, 'tenants') !== FALSE) : ?>
		<div class="container t3-sl t3-sl-2 tenant-hide-module ">		
	<?php else : ?>
		<div class="container t3-sl t3-sl-2">
	<?php endif; ?>
		<?php $this->spotlight('spotlight-2', 'position-5, position-6, position-7, position-8') ?>
	</div>
	<!-- //SPOTLIGHT 2 -->
<?php endif ?>