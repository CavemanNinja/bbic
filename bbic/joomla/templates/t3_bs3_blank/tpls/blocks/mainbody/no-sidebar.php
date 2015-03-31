<?php
/**
 * @package   T3 Blank
 * @copyright Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Mainbody 1 columns, content only
 */
?>

<div id="t3-mainbody" class="container t3-mainbody">
	<?php $id = JRequest::getVar('id'); ?>
	<div class="row">

		<!-- MAIN CONTENT -->

		<!-- <?php if ($id == "162" || $id == "167" || $id == "173" || $id == "9") : ?> -->
			<!-- <div id="t3-content" class="tenant-no-component t3-content col-xs-12"> -->
		<!-- <?php else: ?> -->
			<div id="t3-content" class="t3-content col-xs-12">
		<!-- <?php endif; ?> -->

				<jdoc:include type="component" />
			</div>
			<!-- //MAIN CONTENT -->

	</div>
</div> 