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

	<div class="myspacer">
	</div>
	<section class="t3-copyright">
		<div class="container">
			<div class="row mypartners myfooter copyright mysitemap">
			<?php if (JFactory::getLanguage()->get('tag') == "en-GB") : ?>
				<div class="col-md-12">
					<p class="mypartners-heading">Our Partners</p>
					<div class="row">
						<div class="col-md-6">
							<a href="http://www.bdb-bh.com/"><img class="img-responsive partners-images" src="images/partners/bdb-logo-clear.png" alt=""/></a>
							<p>BDB commenced its operations on January 20, 1992 as the country's leading Development Financial Institution. The Government has entrusted the Bank with a crucial task of promoting investments in the Kingdom of Bahrain.</p>
						</div>
						<div class="col-md-6">
							<a href="http://www.lf.bh/en/"><img class="img-responsive partners-images" src="images/partners/tamkeen-logo-clear.png" alt="" /></a>
							<p>Tamkeen was established in August 2006 as part of Bahrain’s national reform initiatives and Bahrain Economic Vision 2030, and is tasked with supporting Bahrain’s private sector and positioning it as the key driver of economic development.</p>
						</div>
					</div>
				</div>
			<?php else: ?>
				<div class="col-md-12">
					<p class="mypartners-heading">شركاؤنا</p>
					<div class="row">
						<div class="col-md-6">
							<a href="http://www.bdb-bh.com/"><img class="img-responsive partners-images" src="images/partners/bdb-logo-clear.png" alt=""/></a>
							<p>يعتبر بنك البحرين للتنمية مؤسسة مالية وتنموية متخصصة في تمويل وتنمية المؤسسات الصغيرة والمتوسطة في مملكة البحرين. وقد بدأ البنك عملياته في شهر يناير 1992، حيث عهدت إليه الحكومة الرشيدة عملية تمويل المؤسسات الصغيرة والمتوسطة وتشجيع الاستثمار في هذا القطاع الحيوي، وذلك بهدف المساهمة في عملية التنمية الاقتصادية والاجتماعية وتوفير فرص عمل جديدة للبحرينيين وزيادة نسبة الصادرات البحرينية وغيرها.</p>
						</div>
						<div class="col-md-6">
							<a href="http://www.lf.bh/en/"><img class="img-responsive partners-images" src="images/partners/tamkeen-logo-clear.png" alt="" /></a>
							<p>تعتبر تمكين جهة شبه مستقلة تتمتع بقدر من الاستقلال تقوم بوضع وصياغة الخطط الإستراتيجية وخطط العمل لاستغلال الرسوم التي تقوم بجمعها هيئة تنظيم سوق العمل من أجل تحقيق الرفاهية الشاملة للبحرين عن طريق الاستثمار في تحسين قدرات التوظيف للمواطنين البحرينيين وخلق وتوفير الوظائف وتقديم الدعم الاجتماعي.</p>
						</div>
					</div>
				</div>
			<?php endif; ?>
			</div>
			<div class="row myfooter copyright">
				<div>
					<div class="col-md-9 mysitemap"/>
						<?php if (JFactory::getLanguage()->get('tag') == "en-GB") : ?>
							<p class="mypartners-heading">Site Map</p>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/en/">Home</a>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/en/about-us">About Us</a>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/en/applications">Incubator Application</a>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/en/applications">Business Application</a>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/en/newstabs">News (All)</a>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/en/tenants">Tenants</a>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/en/gallery">Image Gallery</a>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/en/downloads">Download Centre</a>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/en/map">Map</a>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/en/contact-us">Contact Us</a>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/en/component/users/?view=login">User Login</a>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/en/component/search/">Search</a>
					 	<?php else : ?>
					 		<p class="mypartners-heading">خارطة الموقع</p>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/ar/">الصفحة الرئيسية</a>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/ar/2014-07-07-21-54-17">عن المركز</a>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/ar/arapplications">تسجيل لمشروع الحاضنات</a>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/ar/arapplications">تسجيل في مركز الاعمال</a>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/ar/2014-09-13-21-21-58">الأخبار</a>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/ar/artenants">شركات</a>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/ar/argallery">المعرض</a>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/ar/ardownloads">التنزيلات</a>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/ar/map-ar">خريطة</a>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/ar/2014-07-07-21-42-36">للإتصال بنا</a>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/ar/component/users/?view=login">تسجيل دخول المستأجر</a>
							<a class="mysitemap-entry" href="<?php echo JURI::root();?>index.php/ar/component/search/">البحث</a>
					 	<?php endif; ?>

					</div>
					<div class="col-md-3 mycopyright">
						
			            	© BBIC All rights reserved
          				
					</div>
				</div>	
			</div>
		</div>
	</section>

</footer>
<!-- //FOOTER -->