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
							<a href="http://www.bdb-bh.com/" target="_blank"><img class="img-responsive partners-images" src="images/partners/bdb-logo-clear.png" alt=""/></a>
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
							<a class="mysitemap-entry" href="index.php?lang=en">Home</a>
							<a class="mysitemap-entry" href="index.php?option=com_content&view=category&layout=blog&id=30&Itemid=179&lang=en">About Us</a>
							<a class="mysitemap-entry" href="https://afternoon-falls-9745.herokuapp.com/bbic/joomla/index.php?option=com_content&view=category&layout=blog&id=43&Itemid=161&lang=en">Office Application</a>
							<a class="mysitemap-entry" href="https://afternoon-falls-9745.herokuapp.com/bbic/joomla/index.php?option=com_content&view=category&layout=blog&id=44&Itemid=160&lang=en">Workshop Application</a>
							<a class="mysitemap-entry" href="index.php?option=com_twojtoolbox&view=twojtoolbox&type=tabs&id=15&Itemid=227&lang=en">News</a>
							<a class="mysitemap-entry" href="index.php?option=com_content&view=category&layout=blog&id=9&Itemid=182&lang=en">Tenants</a>
							<a class="mysitemap-entry" href="index.php?option=com_twojtoolbox&view=twojtoolbox&type=gallery&id=1&Itemid=183&lang=en">Image Gallery</a>
							<a class="mysitemap-entry" href="index.php?option=com_docman&view=filteredlist&layout=table&Itemid=248&lang=en">Download Centre</a>
							<a class="mysitemap-entry" href="index.php?option=com_content&view=article&id=145&Itemid=211&lang=en">Map</a>
							<a class="mysitemap-entry" href="index.php?option=com_contact&view=category&id=18&Itemid=242&lang=en">Contact Us</a>
							<a class="mysitemap-entry" href="index.php?option=com_users&view=login&lang=en">User Login</a>
							<a class="mysitemap-entry" href="index.php?Itemid=167&option=com_search&lang=en">Search</a>
					 	<?php else : ?>
					 		<p class="mypartners-heading">خارطة الموقع</p>
							<a class="mysitemap-entry" href="index.php?lang=ar&Itemid=170">الصفحة الرئيسية</a>
							<a class="mysitemap-entry" href="index.php?option=com_content&view=category&layout=blog&id=31&Itemid=177&lang=ar">عن المركز</a>
							<a class="mysitemap-entry" href="index.php?option=com_content&view=article&id=140&Itemid=187&lang=ar">استمارة طلب مكتب</a>
							<a class="mysitemap-entry" href="index.php?option=com_content&view=article&id=139&Itemid=186&lang=ar">استمارة طلب ورشة</a>
							<a class="mysitemap-entry" href="index.php?option=com_twojtoolbox&view=twojtoolbox&type=tabs&id=16&Itemid=229&lang=ar">الأخبار</a>
							<a class="mysitemap-entry" href="index.php?option=com_content&view=category&layout=blog&id=9&Itemid=173&lang=ar">شركات</a>
							<a class="mysitemap-entry" href="index.php?option=com_twojtoolbox&view=twojtoolbox&type=gallery&id=1&Itemid=174&lang=ar">المعرض</a>
							<a class="mysitemap-entry" href="index.php?option=com_docman&view=filteredlist&layout=table&Itemid=249&lang=ar">التنزيلات</a>
							<a class="mysitemap-entry" href="index.php?option=com_content&view=article&id=217&Itemid=236&lang=ar">خريطة</a>
							<a class="mysitemap-entry" href="index.php?option=com_contact&view=contact&id=4&Itemid=176&lang=ar">للإتصال بنا</a>
							<a class="mysitemap-entry" href="index.php?option=com_users&view=login&lang=ar">تسجيل دخول المستأجر</a>
							<a class="mysitemap-entry" href="index.php?Itemid=167&option=com_search&lang=ar">البحث</a>
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