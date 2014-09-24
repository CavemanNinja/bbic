<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_category
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('bootstrap.framework');

JHtml::script(JUri::base().'templates/t3_bs3_blank/js/jquery.bootpag.min.js', false, true);
JHtml::script(JUri::base().'templates/t3_bs3_blank/js/newsList.js', false, true);

?>
<!-- <div id="test">test</div> -->
<div id="content">
<ul id="list-ul" class="category-module<?php echo $moduleclass_sfx; ?> news-list-module">
	<?php foreach ($list as $item) : ?>
		<li>
			<?php
				$my_article = JTable::getInstance('content');
				$my_article->load($item->id);
				preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $my_article->introtext, $matches);
				$first_img = $matches[1][0];
			?>
			
			<?php if (JFactory::getLanguage()->get('tag') == "en-GB") : ?>
				<div id="news-list-content-id" class="news-list-content">
			<?php else : ?>
				<div id="news-list-content-id" class="news-list-content-ar">
			<?php endif; ?>
					<?php if (JFactory::getLanguage()->get('tag') == "en-GB") : ?>
						<div class="news-list-image">
					<?php else : ?>
						<div class="news-list-image-ar">
					<?php endif; ?>
						<?php if ($first_img !== null) :?>
							<div>
								<?php echo '<img class="img-thumbnail news-list-img" src="'.$first_img.'">'; ?>
							</div>
						<?php endif; ?>
					</div>

				<?php if (JFactory::getLanguage()->get('tag') == "en-GB") : ?>
					<div class="news-list-text">
				<?php else : ?>
					<div class="news-list-text-ar">
				<?php endif; ?>	
					<?php if ($params->get('link_titles') == 1) : ?>
						<a class="news-list-title mod-articles-category-title <?php echo $item->active; ?>" onclick="openArticle(<?php echo $item->id;?>)">
						<?php echo $item->title; ?><br/>
						</a>
					<?php else : ?>
						<?php echo $item->title; ?><br/>
					<?php endif; ?>

					<?php if ($params->get('show_introtext')) :?>
						<div id="introtext-<?php echo $item->id; ?>" class="news-list-introtext">
							<?php echo $item->displayIntrotext; ?><br/>
						</div>
						<div id="fulltext-<?php echo $item->id; ?>" class="news-list-introtext" style="display:none">
							<br/><?php echo $item->fulltext; ?>
						</div>
					<?php endif; ?>

					<br/>
					<!-- <button id="btn-expand-<?php echo $item->id; ?>" class="btn btn-default" onclick="openArticle(<?php echo $item->id;?>)">Read More</button>
					<button id="btn-collapse-<?php echo $item->id; ?>" class="btn btn-default" style="display:none" onclick="closeArticle(<?php echo $item->id;?>)">Show Less</button> -->
					<?php if (JFactory::getLanguage()->get('tag') == "en-GB") : ?>
						<button id="btn-expand-<?php echo $item->id; ?>" class="btn btn-default" >Read More</button>
						<button id="btn-collapse-<?php echo $item->id; ?>" class="btn btn-default" style="display:none" >Show Less</button>
					<?php else : ?>
						<button id="btn-expand-<?php echo $item->id; ?>" class="btn btn-default" >المزيد</button>
						<button id="btn-collapse-<?php echo $item->id; ?>" class="btn btn-default" style="display:none" >أقل</button>
					<?php endif; ?>

<!--
					<?php if ($params->get('show_readmore')) :?>
						<p class="mod-articles-category-readmore">
						
						<a class="mod-articles-category-title <?php echo $item->active; ?>" 
							onClick="openArticle(<?php echo $item->id;?>)" >
							<?php if ($item->params->get('access-view') == false) :
								echo JText::_('MOD_ARTICLES_CATEGORY_REGISTER_TO_READ_MORE');
							elseif ($readmore = $item->alternative_readmore) :
								echo $readmore;
								echo JHtml::_('string.truncate', $item->title, $params->get('readmore_limit'));
							elseif ($params->get('show_readmore_title', 0) == 0) :
								echo JText::sprintf('MOD_ARTICLES_CATEGORY_READ_MORE_TITLE');
							else :
								echo JText::_('MOD_ARTICLES_CATEGORY_READ_MORE');
								echo JHtml::_('string.truncate', $item->title, $params->get('readmore_limit'));
							endif; ?>
-->
						</a>
						</p>
					<?php endif; ?>
				</div>
			</div>
		</li>
	<?php endforeach; ?>
</ul>
</div>
<div id="page-selection"></div>