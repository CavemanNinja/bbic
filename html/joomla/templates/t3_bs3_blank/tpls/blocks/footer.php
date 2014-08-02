<?php
/**
 * @package   T3 Blank
 * @copyright Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>

<!-- FOOTER -->
<footer id="t3-footer" class="wrap t3-footer">

	<?php if ($this->checkSpotlight('footnav', 'footer-1, footer-2, footer-3, footer-4, footer-5, footer-6')) : ?>
		<!-- FOOT NAVIGATION -->
		<div class="container">
			<?php $this->spotlight('footnav', 'footer-1, footer-2, footer-3, footer-4, footer-5, footer-6') ?>
		</div>
		<!-- //FOOT NAVIGATION -->
	<?php endif ?>

	<div class="row-fluid partners-div">
		<?php if ($this->title == "Home" || $this->title == "الرئيسية") : ?>
		 	<div class="partners-heading">
			 	<?php if (JFactory::getLanguage()->get('tag') == "en-GB") : ?>
			 		<h3>Our Partners</h3>
			 	<?php else : ?>
			 		<h3>شركاؤنا</h3>
			 	<?php endif; ?>
		 	</div>
		 	<div class="partners-images-div">
		 		<p>
		 			<img src="images/partners/bdb-logo.png" class="img-responsive partners-images">

		 			<img src="images/partners/tamkeen-logo.png" class="img-responsive partners-images">
		 		</p>
		 	</div>
		<?php endif; ?>
	</div>
	

	<section class="t3-copyright">
		<div class="container">
			<div class="row myfooter copyright">
				<div>
					<div class="col-md-9 mysitemap"/>
						<?php if (JFactory::getLanguage()->get('tag') == "en-GB") : ?>
							<p>Site Map</p>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/en/">Home</a>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/en/about-us">About Us</a>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/en/applicationincubator">Incubator Application</a>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/en/applicationbusiness">Business Application</a>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/en/news">News (All)</a>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/en/14-bbic">BBIC News</a>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/en/15-bdb">BDB News</a>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/en/16-extra">Extra News</a>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/en/company-profiles">Company Profiles</a>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/en/gallery2j">Image Gallery</a>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/en/download">Download Centre</a>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/en/contact-us">Contact Us</a>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/en/component/users/?view=login">User Login</a>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/en/component/search/">Search</a>
					 	<?php else : ?>
					 		<p>خارطة الموقع</p>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/en/">الصفحة الرئيسية</a>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/en/about-us">عن المركز</a>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/en/applicationincubator">تسجيل لمشروع الحاضنات</a>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/en/applicationbusiness">تسجيل في مركز الاعمال</a>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/en/news">الأخبار</a>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/en/14-bbic">أخبار ب ب أ سي</a>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/en/15-bdb">أخبار ب د ب</a>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/en/16-extra">أخبار اضافية</a>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/en/company-profiles">شركات</a>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/en/gallery2j">المعرض</a>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/en/download">التنزيلات</a>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/en/contact-us">للإتصال بنا</a>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/en/component/users/?view=login">تسجيل دخول المستأجر</a>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/en/component/search/">البحث</a>
					 	<?php endif; ?>

					</div>
					<div class="col-md-3 mycopyright">
						<small>
			            	© BBIC All rights reserved
          				</small>
					</div>
				</div>	
			</div>
		</div>
	</section>

</footer>
<!-- //FOOTER -->