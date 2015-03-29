<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// var_dump($this->form->getAttribute('created_by'));
// var_dump($this->item->get('created_by'));
// var_dump($this);

defined('_JEXEC') or die;

// $username = "poppy";

// $userRow = $userTable->load($this->item->get('created_by'));
// var_dump($userRow);

JHtml::_('behavior.keepalive');
JHtml::_('behavior.calendar');
JHtml::_('behavior.formvalidation');
JHtml::stylesheet(JUri::base().'templates/t3_bs3_blank/css/font-awesome.min.css', array(), true);


//Check if user is a tenant
$user = JFactory::getUser();

$isTenant = in_array("10", array_values(JFactory::getUser()->groups));

// Create shortcut to parameters.
$params = $this->state->get('params');
//$images = json_decode($this->item->images);
//$urls = json_decode($this->item->urls);

$catid = $this->form->getData()->get('catid');

/*Get the parent category ID, used for News and Company Profiles */
$categoriesModel = JCategories::getInstance('content');
$category = $categoriesModel->get($catid);
$parent = $category->getParent();
$parentid = $parent->id;
// var_dump($parentid);

// if ($parentid) {
// 	var_dump($parentid);
// }

/*BILLING*/
if ($catid == "10") {
	JHtml::script(JUri::base() . 'templates/t3_bs3_blank/js/dependsOn-1.0.1.min.js', true);

	if(version_compare(JVERSION, '3.0', 'ge')){
		JHtml::_('formbehavior.chosen', 'select');
		JHtml::_('behavior.modal', 'a.modal_jform_contenthistory');
	}

	$document = JFactory::getDocument();
	$document->addScriptDeclaration("
	   jQuery(function(){
	  		jQuery('#jform_attribs_billing_repeatcycle-lbl, #jform_attribs_billing_repeatcycle,       #jform_attribs_billing_repeatstart-lbl, #jform_attribs_billing_repeatstart, #jform_attribs_billing_repeatstart_img, #jform_attribs_billing_repeatend-lbl, #jform_attribs_billing_repeatend, #jform_attribs_billing_repeatend_img').dependsOn({
				'#jform_attribs_billing_repeat': {
					checked: true
				}
			});
		});
	");
}

if ($isTenant && $catid == 9) {
	JFactory::getApplication()->enqueueMessage("So that you don't lose your work, we advise you to use your favorite text processor to write your Company Profile then copy and paste into the box below when you are ready", "Notice");
}

// var_dump($isTenant);

// This checks if the editor config options have ever been saved. If they haven't they will fall back to the original settings.
$editoroptions = isset($params->show_publishing_options);
if (!$editoroptions)
{
	$params->show_urls_images_frontend = '0';
}

//T3: customize
$fieldsets   = $this->form->getFieldsets('attribs');
$extrafields = array();

foreach ($fieldsets as $fieldset) {
	if(isset($fieldset->group) && $fieldset->group == 'extrafields'){
		$extrafields[] = $fieldset;
	}
}

if(count($extrafields)){
	if(is_string($this->item->attribs)){
		$this->item->attribs = json_decode($this->item->attribs);
	}
	$tmp = new stdClass;
	$tmp->attribs = $this->item->attribs;
	$this->form->bind($tmp);
}
//T3: customize
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'article.cancel' || document.formvalidator.isValid(document.getElementById('adminForm')))
		{
			<?php echo $this->form->getField('articletext')->save(); ?>
			Joomla.submitform(task);
		}
	}
</script>



<!-- BILLING -->

<?php if ($catid == 10) : ?>
	<div class="edit item-page<?php echo $this->pageclass_sfx; ?>">
		<?php if ($params->get('show_page_heading', 1)) : ?>
		<div class="page-header">
			<h1>
				<?php echo $this->escape($params->get('page_heading')); ?>
			</h1>
		</div>
		<?php endif; ?>

		<form action="<?php echo JRoute::_('index.php?option=com_content&a_id='.(int) $this->item->id); ?>" role="form" method="post" name="adminForm" id="adminForm" class="form-validate">
			<fieldset>

				<ul class="nav nav-tabs">
					<li class="active"><a href="#editor" data-toggle="tab"><?php echo JText::_('TPL_EXTRAFIELDS_BILLING_SLIDER_LABEL') ?></a></li>
					<?php if(false) : ?>
					<li><a href="#extrafields" data-toggle="tab"><?php echo JText::_('T3_EXTRA_FIELDS_GROUP_LABEL') ?></a></li>
					<?php endif; ?>
					<?php if ($params->get('show_urls_images_frontend') ) : ?>
					<li><a href="#images" data-toggle="tab"><?php echo JText::_('COM_CONTENT_IMAGES_AND_URLS') ?></a></li>
					<?php endif; ?>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="editor">

						<div class="form-group">
							<?php echo str_replace("Title", JText::_('J3_EDITOR_BILLING_ITEM'), $this->form->getLabel('title')); ?>
							<?php echo $this->form->getInput('title'); ?>
						</div>

						<?php if (is_null($this->item->id)) : ?>
						<div class="form-group hidden">
							<?php echo $this->form->getLabel('alias'); ?>
							<?php echo $this->form->getInput('alias'); ?>
						</div>
						<?php endif; ?>
						<div class="form-group hidden">
							<?php echo $this->form->getInput('articletext'); ?>
						</div>


						<?php if(count($extrafields)) : ?>
							<?php foreach ($extrafields as $extraset) : ?>
								<?php foreach ($this->form->getFieldset($extraset->name) as $field) : ?>
									<div class="form-group">
										<div class="control-label">
											<?php echo $field->label; ?>
										</div>
										<div class="controls">
											<?php echo str_replace("icon-calendar", "glyphicon glyphicon-calendar", $field->input); ?>
										</div>
									</div>
								<?php endforeach ?>
							<?php endforeach ?>
						<?php endif; ?>

						<div class="form-group hidden">
							<?php echo $this->form->getLabel('catid'); ?>
							<?php
								$selectcat = $this->form->getInput('catid');

								$selectcat = str_replace('option value="8"',
								 'option value="8" disabled="disabled"',  $selectcat);
								$selectcat = str_replace('option value="9"',
								 'option value="9" disabled="disabled"', $selectcat );
								$selectcat = str_replace('option value="12"',
								 'option value="12" disabled="disabled"', $selectcat );
								echo $selectcat;
								$selectcat = str_replace('option value="13"',
								 'option value="9" disabled="disabled"', $selectcat );
							?>
						</div>

						<div class="form-group hidden">
							<?php echo $this->form->getLabel('state'); ?>
							<?php
								$statepublished = $this->form->getInput('state');

								$statepublished = str_replace('option value="0"',
									'option value="0" disabled="disabled"', $statepublished);
								$statepublished = str_replace('option value="2"',
									'option value="2" disabled="disabled"', $statepublished);
								$statepublished = str_replace('option value="-2"',
									'option value="-2" disabled="disabled"', $statepublished);
								echo $statepublished;
							?>
						</div>

					</div>

					<div class="tab-pane" id="publishing">

						<div class="form-group">
							<?php echo $this->form->getLabel('tags'); ?>
							<?php echo str_replace('span12', '', $this->form->getInput('tags')); ?>
						</div>

						<?php if ($params->get('save_history', 0)) : ?>
						<div class="form-group">
							<?php echo $this->form->getLabel('version_note'); ?>
							<?php echo $this->form->getInput('version_note'); ?>
						</div>
						<?php endif; ?>

						<div class="form-group">
							<?php echo $this->form->getLabel('created_by_alias'); ?>
							<?php echo $this->form->getInput('created_by_alias'); ?>
						</div>

						<?php if ($this->item->params->get('access-change')) : ?>

							<div class="form-group">
								<?php echo $this->form->getLabel('featured'); ?>
								<?php echo $this->form->getInput('featured'); ?>
							</div>

							<div class="form-group">
								<?php echo $this->form->getLabel('publish_up'); ?>
								<?php echo str_replace('class="btn"', 'class="btn btn-default"', $this->form->getInput('publish_up')); ?>
							</div>

							<div class="form-group">
								<?php echo $this->form->getLabel('publish_down'); ?>
								<?php echo str_replace('class="btn"', 'class="btn btn-default"', $this->form->getInput('publish_down')); ?>
							</div>
						<?php endif; ?>

						<div class="form-group">
							<?php echo $this->form->getLabel('access'); ?>
							<?php echo $this->form->getInput('access'); ?>
						</div>

						<?php if (is_null($this->item->id)):?>
							<div class="form-group">
								<?php echo JText::_('COM_CONTENT_ORDERING'); ?>
							</div>
						<?php endif; ?>
					</div>

					<div class="tab-pane" id="language">
						<div class="form-group">
							<?php echo $this->form->getLabel('language'); ?>
							<?php echo $this->form->getInput('language'); ?>
						</div>
					</div>

					<div class="tab-pane" id="metadata">
						<div class="form-group">
							<?php echo $this->form->getLabel('metadesc'); ?>
							<?php echo $this->form->getInput('metadesc'); ?>
						</div>

						<div class="form-group">
								<?php echo $this->form->getLabel('metakey'); ?>
								<?php echo $this->form->getInput('metakey'); ?>
						</div>

						<input type="hidden" name="task" value="" />
						<input type="hidden" name="return" value="<?php echo $this->return_page; ?>" />
						<?php if ($this->params->get('enable_category', 0) == 1) :?>
						<input type="hidden" name="jform[catid]" value="<?php echo $this->params->get('catid', 1); ?>" />
						<?php endif; ?>
					</div>
				</div>
				<div class="btn-toolbar">
					<div class="btn-group">
						<button type="button" class="btn btn-primary validate" onclick="Joomla.submitbutton('article.save')">
							<span class="fa fa-ok"></span>&#160;<?php echo JText::_('JSAVE') ?>
						</button>
					</div>
					<div class="btn-group">
						<button type="button" class="btn btn-default" onclick="Joomla.submitbutton('article.cancel')">
							<span class="fa fa-cancel"></span>&#160;<?php echo JText::_('JCANCEL') ?>
						</button>
					</div>
					<?php if ($params->get('save_history', 0)) : ?>
					<div class="btn-group">
						<?php echo $this->form->getInput('contenthistory'); ?>
					</div>
					<?php endif; ?>
				</div>
				<?php echo JHtml::_('form.token'); ?>
			</fieldset>
		</form>
	</div>

<!-- NEWS -->

<?php elseif ($catid == "8" || $catid == "22" || $parentid == "8" || $parentid == "22") : ?>

<div class="edit item-page<?php echo $this->pageclass_sfx; ?>">
		<?php if ($params->get('show_page_heading', 1)) : ?>
		<div class="page-header">
			<h1>
				<?php echo $this->escape($params->get('page_heading')); ?>
			</h1>
		</div>
		<?php endif; ?>

		<form action="<?php echo JRoute::_('index.php?option=com_content&a_id='.(int) $this->item->id); ?>" role="form" method="post" name="adminForm" id="adminForm" class="form-validate">
			<fieldset>

				<ul class="nav nav-tabs">
					<li class="active"><a href="#editor" data-toggle="tab"><?php echo JText::_('JEDITOR') ?></a></li>
					<?php if(count($extrafields)) : ?>
					<li><a href="#extrafields" data-toggle="tab"><?php echo JText::_('T3_EXTRA_FIELDS_GROUP_LABEL') ?></a></li>
					<?php endif; ?>
					<?php if ($params->get('show_urls_images_frontend') ) : ?>
					<li><a href="#images" data-toggle="tab"><?php echo JText::_('COM_CONTENT_IMAGES_AND_URLS') ?></a></li>
					<?php endif; ?>

				</ul>

				 <input type="hidden" value="<?php echo $user->id ?>" id="usrid" />

				<div class="tab-content">
					<div class="tab-pane active" id="editor">

						<div class="form-group">
							<?php echo $this->form->getLabel('title'); ?>
							<?php echo $this->form->getInput('title'); ?>
						</div>

						<?php if (is_null($this->item->id)) : ?>
						<div class="form-group hidden">
							<?php echo $this->form->getLabel('alias'); ?>
							<?php echo $this->form->getInput('alias'); ?>
						</div>
						<?php endif; ?>

						<div class="form-group">
							<?php echo $this->form->getInput('articletext'); ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->form->getLabel('catid'); ?>
						<?php

							$lang = JFactory::getLanguage()->get('tag');
							// var_dump($lang);
							if ($lang == "en-GB") {
								$selectcat = $this->form->getInput('catid');
								// echo "START".$selectcat."END";

								$selectcat = str_replace('<option value="8" selected="selected">- - News</option>',
								 '', $selectcat );

								$selectcat = str_replace('<option value="8">- - News</option>',
								 '', $selectcat );

								$selectcat = str_replace('<option value="9">- - Company Profiles</option>',
								 '', $selectcat );

								$selectcat = str_replace('<option value="10">- Billing</option>',
								 '',  $selectcat);

								$selectcat = str_replace('<option value="12">- Service Requests</option>',
								 '', $selectcat );

								$selectcat = str_replace('<option value="13">- Downloads</option>',
								 '', $selectcat );

								$selectcat = str_replace('<option value="19">- Services</option>',
								 '', $selectcat );

								$selectcat = str_replace('<option value="28">- - Company Profiles</option>',
								 '', $selectcat );
								$selectcat = str_replace('<option value="22">- - الأخبار</option>',
								 '', $selectcat );
								$selectcat = str_replace('<option value="23">- - - أخبار ب ب أ سي </option>',
								 '', $selectcat );
								$selectcat = str_replace('<option value="24">- - - أخبار ب د ب</option>',
								 '', $selectcat );
								$selectcat = str_replace('<option value="25">- - - أخبار اضافية</option>',
								 '', $selectcat );

								$selectcat = str_replace('<option value="23">- - - أخبارالمركز </option>', '', $selectcat);
								$selectcat = str_replace('<option value="24">- - - أخبار بنك للتنمية</option>', '', $selectcat);
								$selectcat = str_replace('<option value="25">- - - أخبار المستأجرين</option>', '', $selectcat);

								$selectcat = str_replace('<option value="33">- - - Industrial</option>', '', $selectcat);
								$selectcat = str_replace('<option value="34">- - - Retail</option>', '', $selectcat);
								$selectcat = str_replace('<option value="50">- - - Food</option>', '', $selectcat);
								$selectcat = str_replace('<option value="49">- - - Consulting</option>', '', $selectcat);
								$selectcat = str_replace('<option value="51">- - - Society</option>', '', $selectcat);
								$selectcat = str_replace('<option value="52">- - - Trading</option>', '', $selectcat);
								$selectcat = str_replace('<option value="53">- - - Landscaping</option>', '', $selectcat);
								$selectcat = str_replace('<option value="54">- - - Contracting</option>', '', $selectcat);
								$selectcat = str_replace('<option value="55">- - - Recreational</option>', '', $selectcat);
								$selectcat = str_replace('<option value="56">- - - Advertising</option>', '', $selectcat);
								$selectcat = str_replace('<option value="57">- - - Fabricatiion</option>', '', $selectcat);
								$selectcat = str_replace('<option value="58">- - - Furniture</option>', '', $selectcat);
								$selectcat = str_replace('<option value="59">- - - Technology</option>', '', $selectcat);
								$selectcat = str_replace('<option value="60">- - - Transportation</option>', '', $selectcat);
								$selectcat = str_replace('<option value="61">- - - Marine</option>', '', $selectcat);
								$selectcat = str_replace('<option value="62">- - - Cleaning</option>', '', $selectcat);
								$selectcat = str_replace('<option value="63">- - - Event Management</option>', '', $selectcat);
								$selectcat = str_replace('<option value="64">- - - Media</option>', '', $selectcat);
								$selectcat = str_replace('<option value="65">- - - Design</option>', '', $selectcat);

								echo $selectcat;

							} else {
								$selectcat = $this->form->getInput('catid');

								$selectcat = str_replace('<option value="22" selected="selected">- - الأخبار</option>',
								 '', $selectcat );

								$selectcat = str_replace('<option value="8" selected="selected">- - News</option>',
								 '', $selectcat );

								$selectcat = str_replace('<option value="8">- - News</option>',
								 '', $selectcat );

								$selectcat = str_replace('<option value="9">- - Company Profiles</option>',
								 '', $selectcat );

								$selectcat = str_replace('<option value="10">- Billing</option>',
								 '',  $selectcat);

								$selectcat = str_replace('<option value="12">- Service Requests</option>',
								 '', $selectcat );

								$selectcat = str_replace('<option value="13">- Downloads</option>',
								 '', $selectcat );

								$selectcat = str_replace('<option value="19">- Services</option>',
								 '', $selectcat );

								$selectcat = str_replace('<option value="28">- - Company Profiles</option>',
								 '', $selectcat );
								$selectcat = str_replace('<option value="8">- - News</option>',
								 '', $selectcat );
								$selectcat = str_replace('<option value="14">- - - BBIC</option>',
								 '', $selectcat );
								$selectcat = str_replace('<option value="15">- - - BDB</option>',
								 '', $selectcat );
								$selectcat = str_replace('<option value="16">- - - Extra</option>',
								 '', $selectcat );
								$selectcat = str_replace('<option value="16">- - - Tenant</option>', '', $selectcat);
								$selectcat = str_replace('<option value="33">- - - Industrial</option>', '', $selectcat);
								$selectcat = str_replace('<option value="34">- - - Retail</option>', '', $selectcat);
								$selectcat = str_replace('<option value="50">- - - Food</option>', '', $selectcat);
								$selectcat = str_replace('<option value="49">- - - Consulting</option>', '', $selectcat);
								$selectcat = str_replace('<option value="51">- - - Society</option>', '', $selectcat);
								$selectcat = str_replace('<option value="52">- - - Trading</option>', '', $selectcat);
								$selectcat = str_replace('<option value="53">- - - Landscaping</option>', '', $selectcat);
								$selectcat = str_replace('<option value="54">- - - Contracting</option>', '', $selectcat);
								$selectcat = str_replace('<option value="55">- - - Recreational</option>', '', $selectcat);
								$selectcat = str_replace('<option value="56">- - - Advertising</option>', '', $selectcat);
								$selectcat = str_replace('<option value="57">- - - Fabricatiion</option>', '', $selectcat);
								$selectcat = str_replace('<option value="58">- - - Furniture</option>', '', $selectcat);
								$selectcat = str_replace('<option value="59">- - - Technology</option>', '', $selectcat);
								$selectcat = str_replace('<option value="60">- - - Transportation</option>', '', $selectcat);
								$selectcat = str_replace('<option value="61">- - - Marine</option>', '', $selectcat);
								$selectcat = str_replace('<option value="62">- - - Cleaning</option>', '', $selectcat);
								$selectcat = str_replace('<option value="63">- - - Event Management</option>', '', $selectcat);
								$selectcat = str_replace('<option value="64">- - - Media</option>', '', $selectcat);
								$selectcat = str_replace('<option value="65">- - - Design</option>', '', $selectcat);

								echo $selectcat;
							}

						?>
					</div>

					<!-- <div class="tab-pane hidden" id="language">
					</div> -->
					<div class="form-group">
						<?php echo $this->form->getLabel('language'); ?>
						<?php
							$lang = JFactory::getLanguage()->get('tag');
							if ($lang == "en-GB") {
								$selectlang = $this->form->getInput('language');
								$selectlang = str_replace(
									'<option value="en-GB">English</option>',
									'<option selected value="en-GB">English</option>',
									$selectlang);
								echo $selectlang;


							} else {
								$selectlang = $this->form->getInput('language');
								$selectlang = str_replace(
									'<option value="ar-AA">عربي</option>',
									'<option selected value="ar-AA">عربي</option>',
									$selectlang);
								echo $selectlang;
							}
						?>
					</div>

					<?php if(count($extrafields)) : ?>
					<div class="tab-pane" id="extrafields">
						<?php foreach ($extrafields as $extraset) : ?>
							<?php foreach ($this->form->getFieldset($extraset->name) as $field) : ?>
								<div class="form-group">
									<div class="control-label">
										<?php echo $field->label; ?>
									</div>
									<div class="controls">
										<?php echo $field->input; ?>
									</div>
								</div>
							<?php endforeach ?>
						<?php endforeach ?>
					</div>
					<?php endif; ?>

					<div class="form-group">
						<?php echo $this->form->getLabel('image_fulltext', 'images'); ?>
						<?php echo $this->form->getInput('image_fulltext', 'images'); ?>
					</div>

					<?php if ($params->get('show_urls_images_frontend')): ?>
					<div class="tab-pane" id="images">

						<div class="form-group">
							<?php echo $this->form->getLabel('image_intro', 'images'); ?>
							<?php echo $this->form->getInput('image_intro', 'images'); ?>
						</div>

						<div class="form-group">
							<?php echo $this->form->getLabel('image_intro_alt', 'images'); ?>
							<?php echo $this->form->getInput('image_intro_alt', 'images'); ?>
						</div>

						<div class="form-group">
							<?php echo $this->form->getLabel('image_intro_caption', 'images'); ?>
							<?php echo $this->form->getInput('image_intro_caption', 'images'); ?>
						</div>

						<div class="form-group">
							<?php echo $this->form->getLabel('float_intro', 'images'); ?>
							<?php echo $this->form->getInput('float_intro', 'images'); ?>
						</div>

						<div class="form-group">
							<?php echo $this->form->getLabel('image_fulltext', 'images'); ?>
							<?php echo $this->form->getInput('image_fulltext', 'images'); ?>
						</div>

						<div class="form-group">
							<?php echo $this->form->getLabel('image_fulltext_alt', 'images'); ?>
							<?php echo $this->form->getInput('image_fulltext_alt', 'images'); ?>
						</div>

						<div class="form-group">
							<?php echo $this->form->getLabel('image_fulltext_caption', 'images'); ?>
							<?php echo $this->form->getInput('image_fulltext_caption', 'images'); ?>
						</div>

						<div class="form-group">
							<?php echo $this->form->getLabel('float_fulltext', 'images'); ?>
							<?php echo $this->form->getInput('float_fulltext', 'images'); ?>
						</div>

						<div class="form-group">
							<?php echo $this->form->getLabel('urla', 'urls'); ?>
							<?php echo $this->form->getInput('urla', 'urls'); ?>
						</div>

						<div class="form-group">
							<?php echo $this->form->getLabel('urlatext', 'urls'); ?>
							<?php echo $this->form->getInput('urlatext', 'urls'); ?>
						</div>

						<div class="form-group">
							<?php echo $this->form->getInput('targeta', 'urls'); ?>
						</div>

						<div class="form-group">
							<?php echo $this->form->getLabel('urlb', 'urls'); ?>
							<?php echo $this->form->getInput('urlb', 'urls'); ?>
						</div>

						<div class="form-group">
							<?php echo $this->form->getLabel('urlbtext', 'urls'); ?>
							<?php echo $this->form->getInput('urlbtext', 'urls'); ?>
						</div>

						<div class="form-group">
							<?php echo $this->form->getInput('targetb', 'urls'); ?>
						</div>

						<div class="form-group">
							<?php echo $this->form->getLabel('urlc', 'urls'); ?>
							<?php echo $this->form->getInput('urlc', 'urls'); ?>
						</div>

						<div class="form-group">
							<?php echo $this->form->getLabel('urlctext', 'urls'); ?>
							<?php echo $this->form->getInput('urlctext', 'urls'); ?>
						</div>

						<div class="form-group">
							<?php echo $this->form->getInput('targetc', 'urls'); ?>
						</div>

					</div>
					<?php endif; ?>

					<div class="tab-pane" id="publishing">


						<div class="form-group">
							<?php echo $this->form->getLabel('tags'); ?>
							<?php echo str_replace('span12', '', $this->form->getInput('tags')); ?>
						</div>

						<?php if ($params->get('save_history', 0)) : ?>
						<div class="form-group hidden">
							<?php echo $this->form->getLabel('version_note'); ?>
							<?php echo $this->form->getInput('version_note'); ?>
						</div>
						<?php endif; ?>

						<div class="form-group hidden">
							<?php echo $this->form->getLabel('created_by_alias'); ?>
							<?php echo $this->form->getInput('created_by_alias'); ?>
						</div>

						<?php if ($this->item->params->get('access-change')) : ?>
							<div class="form-group">
								<?php echo $this->form->getLabel('state'); ?>
								<?php
									$statepublished = $this->form->getInput('state');

									$statepublished = str_replace('option value="0"',
										'option value="0" disabled="disabled"', $statepublished);
									$statepublished = str_replace('option value="2"',
										'option value="2" disabled="disabled"', $statepublished);
									$statepublished = str_replace('option value="-2"',
										'option value="-2" disabled="disabled"', $statepublished);
									echo $statepublished;
								?>
							</div>

							<div class="form-group">
								<?php echo $this->form->getLabel('featured'); ?>
								<?php echo $this->form->getInput('featured'); ?>
							</div>

							<div class="form-group hidden">
								<?php echo $this->form->getLabel('publish_up'); ?>
								<?php echo str_replace('class="btn"', 'class="btn btn-default"', $this->form->getInput('publish_up')); ?>
							</div>

							<div class="form-group hidden">
								<?php echo $this->form->getLabel('publish_down'); ?>
								<?php echo str_replace('class="btn"', 'class="btn btn-default"', $this->form->getInput('publish_down')); ?>
							</div>
						<?php endif; ?>

						<div class="form-group hidden">
							<?php echo $this->form->getLabel('access'); ?>
							<?php echo $this->form->getInput('access'); ?>
						</div>

						<?php if (is_null($this->item->id)):?>
							<div class="form-group">
								<?php echo JText::_('COM_CONTENT_ORDERING'); ?>
							</div>
						<?php endif; ?>
					</div>



					<div class="tab-pane" id="metadata">
						<div class="form-group">
							<?php echo $this->form->getLabel('metadesc'); ?>
							<?php echo $this->form->getInput('metadesc'); ?>
						</div>

						<div class="form-group">
								<?php echo $this->form->getLabel('metakey'); ?>
								<?php echo $this->form->getInput('metakey'); ?>
						</div>

						<input type="hidden" name="task" value="" />
						<input type="hidden" name="return" value="<?php echo $this->return_page; ?>" />
						<?php if ($this->params->get('enable_category', 0) == 1) :?>
						<input type="hidden" name="jform[catid]" value="<?php echo $this->params->get('catid', 1); ?>" />
						<?php endif; ?>
					</div>
				</div>
				<div class="btn-toolbar">
					<div class="btn-group">
						<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('article.save')">
							<span class="fa fa-ok"></span>&#160;<?php echo JText::_('JSAVE') ?>
						</button>
					</div>
					<div class="btn-group">
						<button type="button" class="btn btn-default" onclick="Joomla.submitbutton('article.cancel')">
							<span class="fa fa-cancel"></span>&#160;<?php echo JText::_('JCANCEL') ?>
						</button>
					</div>
					<?php if ($params->get('save_history', 0)) : ?>
					<div class="btn-group">
						<?php echo $this->form->getInput('contenthistory'); ?>
					</div>
					<?php endif; ?>
				</div>
				<?php echo JHtml::_('form.token'); ?>
			</fieldset>
		</form>
	</div>

<!-- COMPANY PROFILES -->
<?php elseif ($catid == "9" || $parentid == "9"): ?>



	<div class="edit item-page<?php echo $this->pageclass_sfx; ?>">
		<?php if ($params->get('show_page_heading', 1)) : ?>
		<div class="page-header">
			<h1>
				<?php echo $this->escape($params->get('page_heading')); ?>
			</h1>
		</div>
		<?php endif; ?>

		<form action="<?php echo JRoute::_('index.php?option=com_content&a_id='.(int) $this->item->id); ?>" role="form" method="post" name="adminForm" id="adminForm" class="form-validate">
			<fieldset>
			<div class="btn-toolbar">
					<div class="btn-group force-no-margin">
						<button type="button" class="btn btn-primary validate" onclick="Joomla.submitbutton('article.save')">
							<span class="fa fa-ok"></span>&#160;<?php echo JText::_('J3_EDITOR_SERVICEREQUEST_SUBMIT') ?>
						</button>
					</div>
					<div class="btn-group">
						<button type="button" class="btn btn-default" onclick="Joomla.submitbutton('article.cancel')">
							<span class="fa fa-cancel"></span>&#160;<?php echo JText::_('JCANCEL') ?>
						</button>
					</div>
					<?php if ($params->get('save_history', 0)) : ?>
					<div class="btn-group">
						<?php echo $this->form->getInput('contenthistory'); ?>
					</div>
					<?php endif; ?>
					<br/><br/>
				</div>

				 <input type="hidden" value="<?php echo $user->id ?>" id="usrid" />

				<ul class="nav nav-tabs">
					<li class="active"><a href="#editor" data-toggle="tab"><?php echo JText::_('J3_EDITOR_COMPANYPROFILES_TAB') ?></a></li>
					<?php if(false) : ?>
					<li><a href="#extrafields" data-toggle="tab"><?php echo JText::_('T3_EXTRA_FIELDS_GROUP_LABEL') ?></a></li>
					<?php endif; ?>
					<?php if ($params->get('show_urls_images_frontend') ) : ?>
					<li><a href="#images" data-toggle="tab"><?php echo JText::_('COM_CONTENT_IMAGES_AND_URLS') ?></a></li>
					<?php endif; ?>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="editor">

						<div class="form-group">
							<?php echo $this->form->getLabel('title'); ?>
							<?php echo $this->form->getInput('title'); ?>
						</div>

						<?php if (is_null($this->item->id)) : ?>
						<div class="form-group hidden">
							<?php echo $this->form->getLabel('alias'); ?>
							<?php echo $this->form->getInput('alias'); ?>
						</div>
						<?php endif; ?>

						<div class="form-group">
							<?php echo $this->form->getInput('articletext'); ?>
							<br/><br/><br/><br/>
						</div>

						<?php if(count($extrafields)) : ?>
						<div class="tab-pane" id="extrafields">
							<?php foreach ($extrafields as $extraset) : ?>
								<?php foreach ($this->form->getFieldset($extraset->name) as $field) : ?>

										<div class="form-group">
											<div class="control-label">
												<?php echo $field->label; ?>
											</div>
											<div class="controls">
												<?php if ($field->name == "jform[attribs][companyprofile_approval]" && $isTenant) : ?>
													<?php echo str_replace('<option value="1">Approved</option>', '<option value="1" disabled="disabled" >Approved</option>', $field->input); ?>

												<?php elseif ($field->name == "jform[attribs][companyprofile_category]" && JFactory::getLanguage()->get('tag') == "ar-AA") : ?>
													<?php
														$ar_cat_string = $field->input;
														$ar_cat_string = str_replace("Advertising", "الإعلان", $ar_cat_string);
														$ar_cat_string = str_replace("Cleaning", "التنظيف", $ar_cat_string);
														$ar_cat_string = str_replace("Consulting", "الاستشارات", $ar_cat_string);
														$ar_cat_string = str_replace("Contracting", "التعاقد", $ar_cat_string);
														$ar_cat_string = str_replace("Design", "تصميم", $ar_cat_string);
														$ar_cat_string = str_replace("Event Management", "إدارة الأحداث", $ar_cat_string);
														$ar_cat_string = str_replace("Fabrication", "التصنيع", $ar_cat_string);
														$ar_cat_string = str_replace("Food", "طعام", $ar_cat_string);
														$ar_cat_string = str_replace("Furniture", "الأثاث", $ar_cat_string);
														$ar_cat_string = str_replace("Industrial", "الصناعية", $ar_cat_string);
														$ar_cat_string = str_replace("Landscaping", "المناظر الطبيعية", $ar_cat_string);
														$ar_cat_string = str_replace("Marine", "البحرية", $ar_cat_string);
														$ar_cat_string = str_replace("Media", "الإعلام", $ar_cat_string);
														$ar_cat_string = str_replace("Recreational", "الترفيهية", $ar_cat_string);
														$ar_cat_string = str_replace("Retail", "التجارية‎", $ar_cat_string);
														$ar_cat_string = str_replace("Society", "المجتمع", $ar_cat_string);
														$ar_cat_string = str_replace("Technology", "التكنولوجيا", $ar_cat_string);
														$ar_cat_string = str_replace("Trading", "التداول", $ar_cat_string);
														$ar_cat_string = str_replace("Transportation", "النقل", $ar_cat_string);
														echo $ar_cat_string;
													?>
												<?php else : ?>
													<?php echo $field->input; ?>
												<?php endif; ?>
											</div>
										</div>

								<?php endforeach ?>
							<?php endforeach ?>
						</div>
						<?php endif; ?>

						<?php $created_by = $this->item->get('created_by'); ?>
						<?php if (!$isTenant && $created_by != NULL) : ?>
							
							<?php
								$db = JFactory::getDbo();
								$query = $db->getQuery(true);
								$query->select($db->quoteName('username'));
								$query->from($db->quoteName('#__users'));
								$query->where($db->quoteName('id') . " = " . $created_by);
								$db->setQuery($query);
								$username = $db->loadResult();
							?>

							<div class="form-group">
								<label class="control-label">Created By User</label>
								<p><?php echo $username; ?></p>
							</div>
						<?php endif; ?>

						<div class="form-group hidden">
							<?php echo $this->form->getLabel('catid'); ?>
							<?php echo $this->form->getInput('catid');
								// $selectcat =

								// $selectcat = str_replace('option value="10"',
								//  'option value="10" disabled="disabled"',  $selectcat);
								// $selectcat = str_replace('option value="8"',
								//  'option value="8" disabled="disabled"', $selectcat );
								// $selectcat = str_replace('option value="12"',
								//  'option value="12" disabled="disabled"', $selectcat );
								// echo $selectcat;
								// $selectcat = str_replace('option value="13"',
								//  'option value="9" disabled="disabled"', $selectcat );
							?>
						</div>

						<div class="form-group hidden">
							<?php echo $this->form->getLabel('state'); ?>
							<?php
								$statepublished = $this->form->getInput('state');

								$statepublished = str_replace('option value="0"',
									'option value="0" disabled="disabled"', $statepublished);
								$statepublished = str_replace('option value="2"',
									'option value="2" disabled="disabled"', $statepublished);
								$statepublished = str_replace('option value="-2"',
									'option value="-2" disabled="disabled"', $statepublished);
								echo $statepublished;
							?>
						</div>

						<div class="form-group hidden">
							<?php echo $this->form->getLabel('language'); ?>
							<?php echo $this->form->getInput('language');
							// $lang = JFactory::getLanguage()->get('tag');
							// if ($lang == "en-GB") {
							// 	$selectlang = $this->form->getInput('language');
							// 	$selectlang = str_replace(
							// 		'<option value="en-GB">English</option>',
							// 		'<option selected value="en-GB">English</option>',
							// 		$selectlang);
							// 	echo $selectlang;


							// } else {
							// 	$selectlang = $this->form->getInput('language');
							// 	$selectlang = str_replace(
							// 		'<option value="ar-AA">العربية</option>',
							// 		'<option selected value="ar-AA">العربية</option>',
							// 		$selectlang);
							// 	echo $selectlang;
							// }
						?>
						</div>
					</div>

					<div class="tab-pane" id="publishing">

						<div class="form-group">
							<?php echo $this->form->getLabel('tags'); ?>
							<?php echo str_replace('span12', '', $this->form->getInput('tags')); ?>
						</div>

						<?php if ($params->get('save_history', 0)) : ?>
						<div class="form-group">
							<?php echo $this->form->getLabel('version_note'); ?>
							<?php echo $this->form->getInput('version_note'); ?>
						</div>
						<?php endif; ?>

						<div class="form-group">
							<?php echo $this->form->getLabel('created_by_alias'); ?>
							<?php echo $this->form->getInput('created_by_alias'); ?>
						</div>

						<?php if ($this->item->params->get('access-change')) : ?>


							<div class="form-group">
								<?php echo $this->form->getLabel('featured'); ?>
								<?php echo $this->form->getInput('featured'); ?>
							</div>

							<div class="form-group">
								<?php echo $this->form->getLabel('publish_up'); ?>
								<?php echo str_replace('class="btn"', 'class="btn btn-default"', $this->form->getInput('publish_up')); ?>
							</div>

							<div class="form-group">
								<?php echo $this->form->getLabel('publish_down'); ?>
								<?php echo str_replace('class="btn"', 'class="btn btn-default"', $this->form->getInput('publish_down')); ?>
							</div>
						<?php endif; ?>

						<div class="form-group">
							<?php echo $this->form->getLabel('access'); ?>
							<?php echo $this->form->getInput('access'); ?>
						</div>

						<?php if (is_null($this->item->id)):?>
							<div class="form-group">
								<?php echo JText::_('COM_CONTENT_ORDERING'); ?>
							</div>
						<?php endif; ?>
					</div>

					<div class="tab-pane" id="language">

					</div>

					<div class="tab-pane" id="metadata">
						<div class="form-group">
							<?php echo $this->form->getLabel('metadesc'); ?>
							<?php echo $this->form->getInput('metadesc'); ?>
						</div>

						<div class="form-group">
								<?php echo $this->form->getLabel('metakey'); ?>
								<?php echo $this->form->getInput('metakey'); ?>
						</div>

						<input type="hidden" name="task" value="" />
						<input type="hidden" name="return" value="<?php echo $this->return_page; ?>" />
						<?php if ($this->params->get('enable_category', 0) == 1) :?>
						<input type="hidden" name="jform[catid]" value="<?php echo $this->params->get('catid', 1); ?>" />
						<?php endif; ?>
					</div>
				</div>
				<div class="btn-toolbar">
					<div class="btn-group">
						<button type="button" class="btn btn-primary validate" onclick="Joomla.submitbutton('article.save')">
							<span class="fa fa-ok"></span>&#160;<?php echo JText::_('JSAVE') ?>
						</button>
					</div>
					<div class="btn-group">
						<button type="button" class="btn btn-default" onclick="Joomla.submitbutton('article.cancel')">
							<span class="fa fa-cancel"></span>&#160;<?php echo JText::_('JCANCEL') ?>
						</button>
					</div>
					<?php if ($params->get('save_history', 0)) : ?>
					<div class="btn-group">
						<?php echo $this->form->getInput('contenthistory'); ?>
					</div>
					<?php endif; ?>
				</div>
				<?php echo JHtml::_('form.token'); ?>
			</fieldset>
		</form>
	</div>

<!-- SERVICE REQUESTS -->
<?php elseif ($catid == 12) : ?>
	<div class="edit item-page<?php echo $this->pageclass_sfx; ?>">
		<?php if ($params->get('show_page_heading', 1)) : ?>
		<div class="page-header">
			<h1>
				<?php echo $this->escape($params->get('page_heading')); ?>
			</h1>
		</div>
		<?php endif; ?>

		<form action="<?php echo JRoute::_('index.php?option=com_content&a_id='.(int) $this->item->id); ?>" role="form" method="post" name="adminForm" id="adminForm" class="form-validate">
			<fieldset>
			

				<ul class="nav nav-tabs">
					<li class="active"><a href="#editor" data-toggle="tab"><?php echo JText::_('J3_EDITOR_SERVICEREQUEST_TAB') ?></a></li>
					<?php if(false) : ?>
					<li><a href="#extrafields" data-toggle="tab"><?php echo JText::_('T3_EXTRA_FIELDS_GROUP_LABEL') ?></a></li>
					<?php endif; ?>
					<?php if ($params->get('show_urls_images_frontend') ) : ?>
					<?php endif; ?>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="editor">

						<div class="form-group">
							<?php echo str_replace("Title", JText::_('J3_EDITOR_SERVICEREQUEST_MESSAGE'), $this->form->getLabel('title')); ?>
							<?php echo $this->form->getInput('title'); ?>
						</div>

						<?php if (is_null($this->item->id)) : ?>
						<div class="form-group hidden">
							<?php echo $this->form->getLabel('alias'); ?>
							<?php echo $this->form->getInput('alias'); ?>
						</div>
						<?php endif; ?>

						<div class="form-group hidden">
							<?php echo $this->form->getInput('articletext'); ?>
							<br/><br/><br/><br/>
						</div>

						<?php if(count($extrafields)) : ?>
						<div class="tab-pane" id="extrafields">
							<?php foreach ($extrafields as $extraset) : ?>
								<?php foreach ($this->form->getFieldset($extraset->name) as $field) : ?>
									<div class="form-group">
										<div class="control-label">
											<?php echo $field->label; ?>
										</div>
										<div class="controls">
											<?php
												if ($isTenant) {
													$extrafields =  $field->input;
													$extrafields = 	str_replace("icon-calendar",
														"glyphicon glyphicon-calendar", $extrafields);
													$extrafields = str_replace('<option value="1">Approved',
													                           '<option value="1" disabled="disabled">Approved',
													                           $extrafields);
													$extrafields = str_replace('<option value="1" selected="selected">Approved',
													                           '<option value="1" selected="selected" disabled="disabled">Approved',
													                           $extrafields);
													$extrafields = str_replace('<option value="2">Denied',
													                           '<option value="2" disabled="disabled">Denied',
													                           $extrafields);
													echo $extrafields;
												} else {
													echo $field->input;
												}

											?>
										</div>
									</div>
								<?php endforeach ?>
							<?php endforeach ?>
						</div>
						<?php endif; ?>

						<div class="form-group hidden">
							<?php echo $this->form->getLabel('catid'); ?>
							<?php
								$selectcat = $this->form->getInput('catid');

								$selectcat = str_replace('option value="10"',
								 'option value="10" disabled="disabled"',  $selectcat);
								$selectcat = str_replace('option value="8"',
								 'option value="8" disabled="disabled"', $selectcat );
								$selectcat = str_replace('option value="9"',
								 'option value="9" disabled="disabled"', $selectcat );
								$selectcat = str_replace('option value="13"',
								 'option value="9" disabled="disabled"', $selectcat );
								echo $selectcat;
							?>
						</div>
						<?php if ($this->item->params->get('access-change')) : ?>
						<div class="form-group hidden">
							<?php echo $this->form->getLabel('state'); ?>
							<?php
								$statepublished = $this->form->getInput('state');

								$statepublished = str_replace('option value="0"',
									'option value="0" disabled="disabled"', $statepublished);
								$statepublished = str_replace('option value="2"',
									'option value="2" disabled="disabled"', $statepublished);
								$statepublished = str_replace('option value="-2"',
									'option value="-2" disabled="disabled"', $statepublished);
								echo $statepublished;
							?>
						<?php endif; ?>
						</div>

					</div><!--edit pane -->



					<div class="tab-pane" id="publishing">

						<div class="form-group hidden">
							<?php echo $this->form->getLabel('tags'); ?>
							<?php echo str_replace('span12', '', $this->form->getInput('tags')); ?>
						</div>

						<?php if ($params->get('save_history', 0)) : ?>
						<div class="form-group hidden">
							<?php echo $this->form->getLabel('version_note'); ?>
							<?php echo $this->form->getInput('version_note'); ?>
						</div>
						<?php endif; ?>

						<div class="form-group hidden">
							<?php echo $this->form->getLabel('created_by_alias'); ?>
							<?php echo $this->form->getInput('created_by_alias'); ?>
						</div>

						<?php if ($this->item->params->get('access-change')) : ?>
							<div class="form-group hidden">
								<?php echo $this->form->getLabel('featured'); ?>
								<?php echo $this->form->getInput('featured'); ?>
							</div>

							<div class="form-group hidden">
								<?php echo $this->form->getLabel('publish_up'); ?>
								<?php echo str_replace('class="btn"', 'class="btn btn-default"', $this->form->getInput('publish_up')); ?>
							</div>

							<div class="form-group hidden">
								<?php echo $this->form->getLabel('publish_down'); ?>
								<?php echo str_replace('class="btn"', 'class="btn btn-default"', $this->form->getInput('publish_down')); ?>
							</div>
						<?php endif; ?>

						<div class="form-group hidden">
							<?php echo $this->form->getLabel('access'); ?>
							<?php echo $this->form->getInput('access'); ?>
						</div>

						<?php if (is_null($this->item->id)):?>
							<div class="form-group hidden">
								<?php echo JText::_('COM_CONTENT_ORDERING'); ?>
							</div>
						<?php endif; ?>
					</div>

					<div class="tab-pane" id="language">
						<div class="form-group hidden">
							<?php echo $this->form->getLabel('language'); ?>
							<?php echo $this->form->getInput('language'); ?>
						</div>
					</div>

					<div class="tab-pane" id="metadata">
						<div class="form-group hidden">
							<?php echo $this->form->getLabel('metadesc'); ?>
							<?php echo $this->form->getInput('metadesc'); ?>
						</div>

						<div class="form-group hidden">
								<?php echo $this->form->getLabel('metakey'); ?>
								<?php echo $this->form->getInput('metakey'); ?>
						</div>

						<input type="hidden" name="task" value="" />
						<input type="hidden" name="return" value="<?php echo $this->return_page; ?>" />
						<?php if ($this->params->get('enable_category', 0) == 1) :?>
						<input type="hidden" name="jform[catid]" value="<?php echo $this->params->get('catid', 1); ?>" />
						<?php endif; ?>
					</div>
				</div>
				<div class="btn-toolbar">
					<div class="btn-group">
						<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('article.save')">
							<span class="fa fa-ok"></span>&#160;<?php echo JText::_('J3_EDITOR_SERVICEREQUEST_SUBMIT') ?>
						</button>
					</div>
					<div class="btn-group">
						<button type="button" class="btn btn-default" onclick="Joomla.submitbutton('article.cancel')">
							<span class="fa fa-cancel"></span>&#160;<?php echo JText::_('JCANCEL') ?>
						</button>
					</div>
					<?php if ($params->get('save_history', 0)) : ?>
					<div class="btn-group">
						<?php echo $this->form->getInput('contenthistory'); ?>
					</div>
					<?php endif; ?>
				</div>
				<?php echo JHtml::_('form.token'); ?>
			</fieldset>
		</form>
	</div>

<!-- SERVICES -->
<?php elseif ($catid == 19) : ?>
	<div class="edit item-page<?php echo $this->pageclass_sfx; ?>">
		<?php if ($params->get('show_page_heading', 1)) : ?>
		<div class="page-header">
			<h1>
				<?php echo $this->escape($params->get('page_heading')); ?>
			</h1>
		</div>
		<?php endif; ?>

		<form action="<?php echo JRoute::_('index.php?option=com_content&a_id='.(int) $this->item->id); ?>" role="form" method="post" name="adminForm" id="adminForm" class="form-validate">
			<fieldset>


				<ul class="nav nav-tabs">
					<li class="active"><a href="#editor" data-toggle="tab"><?php echo JText::_('J3_EDITOR_SERVICE_TAB') ?></a></li>
					<?php if(false) : ?>
					<li><a href="#extrafields" data-toggle="tab"><?php echo JText::_('T3_EXTRA_FIELDS_GROUP_LABEL') ?></a></li>
					<?php endif; ?>
					<?php if ($params->get('show_urls_images_frontend') ) : ?>
					<?php endif; ?>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="editor">

						<div class="form-group" readonly>
							<?php echo $this->form->getLabel('title'); ?>
							<?php echo $this->form->getInput('title'); ?>
						</div>

						<?php if (is_null($this->item->id)) : ?>
						<div class="form-group hidden">
							<?php echo $this->form->getLabel('alias'); ?>
							<?php echo $this->form->getInput('alias'); ?>
						</div>
						<?php endif; ?>

						<div class="form-group hidden">
							<?php echo $this->form->getInput('articletext'); ?>
							<br/><br/><br/><br/>
						</div>

						<?php if(count($extrafields)) : ?>
						<div class="tab-pane" id="extrafields">
							<?php foreach ($extrafields as $extraset) : ?>
								<?php foreach ($this->form->getFieldset($extraset->name) as $field) : ?>
									<div class="form-group">
										<div class="control-label">
											<?php echo $field->label; ?>
										</div>
										<div class="controls">
											<?php echo $field->input; ?>
										</div>
									</div>
								<?php endforeach ?>
							<?php endforeach ?>
						</div>
						<?php endif; ?>

						<div class="form-group hidden">
							<?php echo $this->form->getLabel('catid'); ?>
							<?php
								$selectcat = $this->form->getInput('catid');

								$selectcat = str_replace('option value="10"',
								 'option value="10" disabled="disabled"',  $selectcat);
								$selectcat = str_replace('option value="8"',
								 'option value="8" disabled="disabled"', $selectcat );
								$selectcat = str_replace('option value="9"',
								 'option value="9" disabled="disabled"', $selectcat );
								$selectcat = str_replace('option value="13"',
								 'option value="9" disabled="disabled"', $selectcat );
								echo $selectcat;
							?>
						</div>
						<?php if ($this->item->params->get('access-change')) : ?>
						<div class="form-group hidden">
							<?php echo $this->form->getLabel('state'); ?>
							<?php
								$statepublished = $this->form->getInput('state');

								$statepublished = str_replace('option value="0"',
									'option value="0" disabled="disabled"', $statepublished);
								$statepublished = str_replace('option value="2"',
									'option value="2" disabled="disabled"', $statepublished);
								$statepublished = str_replace('option value="-2"',
									'option value="-2" disabled="disabled"', $statepublished);
								echo $statepublished;
							?>
						<?php endif; ?>
						</div>

					</div><!--edit pane -->



					<div class="tab-pane" id="publishing">

						<div class="form-group hidden">
							<?php echo $this->form->getLabel('tags'); ?>
							<?php echo str_replace('span12', '', $this->form->getInput('tags')); ?>
						</div>

						<?php if ($params->get('save_history', 0)) : ?>
						<div class="form-group hidden">
							<?php echo $this->form->getLabel('version_note'); ?>
							<?php echo $this->form->getInput('version_note'); ?>
						</div>
						<?php endif; ?>

						<div class="form-group hidden">
							<?php echo $this->form->getLabel('created_by_alias'); ?>
							<?php echo $this->form->getInput('created_by_alias'); ?>
						</div>

						<?php if ($this->item->params->get('access-change')) : ?>
							<div class="form-group hidden">
								<?php echo $this->form->getLabel('featured'); ?>
								<?php echo $this->form->getInput('featured'); ?>
							</div>

							<div class="form-group hidden">
								<?php echo $this->form->getLabel('publish_up'); ?>
								<?php echo str_replace('class="btn"', 'class="btn btn-default"', $this->form->getInput('publish_up')); ?>
							</div>

							<div class="form-group hidden">
								<?php echo $this->form->getLabel('publish_down'); ?>
								<?php echo str_replace('class="btn"', 'class="btn btn-default"', $this->form->getInput('publish_down')); ?>
							</div>
						<?php endif; ?>

						<div class="form-group hidden">
							<?php echo $this->form->getLabel('access'); ?>
							<?php echo $this->form->getInput('access'); ?>
						</div>

						<?php if (is_null($this->item->id)):?>
							<div class="form-group hidden">
								<?php echo JText::_('COM_CONTENT_ORDERING'); ?>
							</div>
						<?php endif; ?>
					</div>

					<div class="tab-pane" id="language">
						<div class="form-group hidden">
							<?php echo $this->form->getLabel('language'); ?>
							<?php echo $this->form->getInput('language'); ?>
						</div>
					</div>

					<div class="tab-pane" id="metadata">
						<div class="form-group hidden">
							<?php echo $this->form->getLabel('metadesc'); ?>
							<?php echo $this->form->getInput('metadesc'); ?>
						</div>

						<div class="form-group hidden">
								<?php echo $this->form->getLabel('metakey'); ?>
								<?php echo $this->form->getInput('metakey'); ?>
						</div>

						<input type="hidden" name="task" value="" />
						<input type="hidden" name="return" value="<?php echo $this->return_page; ?>" />
						<?php if ($this->params->get('enable_category', 0) == 1) :?>
						<input type="hidden" name="jform[catid]" value="<?php echo $this->params->get('catid', 1); ?>" />
						<?php endif; ?>
					</div>
				</div>
				<div class="btn-toolbar">
					<div class="btn-group">
						<button type="button" class="btn btn-primary validate" onclick="Joomla.submitbutton('article.save')">
							<span class="fa fa-ok"></span>&#160;<?php echo JText::_('J3_EDITOR_SERVICEREQUEST_SUBMIT') ?>
						</button>
					</div>
					<div class="btn-group">
						<button type="button" class="btn btn-default" onclick="Joomla.submitbutton('article.cancel')">
							<span class="fa fa-cancel"></span>&#160;<?php echo JText::_('JCANCEL') ?>
						</button>
					</div>
					<?php if ($params->get('save_history', 0)) : ?>
					<div class="btn-group">
						<?php echo $this->form->getInput('contenthistory'); ?>
					</div>
					<?php endif; ?>
				</div>
				<?php echo JHtml::_('form.token'); ?>
			</fieldset>
		</form>
	</div>

<!-- Downloads (Not even using this) -->
<?php elseif($catid == 13) : ?>
<div class="edit item-page<?php echo $this->pageclass_sfx; ?>">
		<?php if ($params->get('show_page_heading', 1)) : ?>
		<div class="page-header">
			<h1>
				<?php echo $this->escape($params->get('page_heading')); ?>
			</h1>
		</div>
		<?php endif; ?>

		<form action="<?php echo JRoute::_('index.php?option=com_content&a_id='.(int) $this->item->id); ?>" role="form" enctype="multipart/form-data" method="post" name="adminForm" id="adminForm" class="form-validate">
			<fieldset>
			<div class="btn-toolbar">
					<div class="btn-group force-no-margin">
						<button type="button" class="btn btn-primary validate" onclick="Joomla.submitbutton('article.save')">
							<span class="fa fa-ok"></span>&#160;<?php echo JText::_('J3_EDITOR_SERVICEREQUEST_SUBMIT') ?>
						</button>
					</div>
					<div class="btn-group">
						<button type="button" class="btn btn-default" onclick="Joomla.submitbutton('article.cancel')">
							<span class="fa fa-cancel"></span>&#160;<?php echo JText::_('JCANCEL') ?>
						</button>
					</div>
					<?php if ($params->get('save_history', 0)) : ?>
					<div class="btn-group">
						<?php echo $this->form->getInput('contenthistory'); ?>
					</div>
					<?php endif; ?>
					<br/><br/>
				</div>

				<ul class="nav nav-tabs">
					<li class="active"><a href="#editor" data-toggle="tab"><?php echo JText::_('J3_EDITOR_DOWNLOAD_TAB') ?></a></li>
					<?php if(false) : ?>
					<li><a href="#extrafields" data-toggle="tab"><?php echo JText::_('T3_EXTRA_FIELDS_GROUP_LABEL') ?></a></li>
					<?php endif; ?>
					<?php if ($params->get('show_urls_images_frontend') ) : ?>
					<li><a href="#images" data-toggle="tab"><?php echo JText::_('COM_CONTENT_IMAGES_AND_URLS') ?></a></li>
					<?php endif; ?>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="editor">

						<div class="form-group">
							<?php echo $this->form->getLabel('title'); ?>
							<?php echo $this->form->getInput('title'); ?>
						</div>

						<?php if (is_null($this->item->id)) : ?>
						<div class="form-group hidden">
							<?php echo $this->form->getLabel('alias'); ?>
							<?php echo $this->form->getInput('alias'); ?>
						</div>
						<?php endif; ?>

						<div class="form-group">
							<?php echo '<strong>'.JText::_('TPL_EXTRAFILEDS_DOWNLOAD_DESC_LABEL').'</strong>'; ?>
							<br/><br/>
							<?php echo $this->form->getInput('articletext'); ?>
							<br/><br/><br/>						</div>


					<?php if(count($extrafields)) : ?>
					<div class="tab-pane" id="extrafields">
						<?php foreach ($extrafields as $extraset) : ?>
							<?php foreach ($this->form->getFieldset($extraset->name) as $field) : ?>
								<div class="form-group">
									<div class="control-label">
										<?php echo $field->label; ?>
									</div>
									<div class="controls">
										<?php echo $field->input; ?>
									</div>
								</div>
							<?php endforeach ?>
						<?php endforeach ?>
					</div>
					<?php endif; ?>

					<div class="form-group hidden">
						<?php echo $this->form->getLabel('catid'); ?>
						<?php
							$selectcat = $this->form->getInput('catid');

							$selectcat = str_replace('option value="10"',
							 'option value="10" disabled="disabled"',  $selectcat);
							$selectcat = str_replace('option value="8"',
							 'option value="8" disabled="disabled"', $selectcat );
							$selectcat = str_replace('option value="9"',
							 'option value="9" disabled="disabled"', $selectcat );
							$selectcat = str_replace('option value="12"',
							 'option value="9" disabled="disabled"', $selectcat );
							echo $selectcat;
						?>
					</div>

					<div class="form-group hidden">
						<?php echo $this->form->getLabel('state'); ?>
						<?php
								$statepublished = $this->form->getInput('state');

								$statepublished = str_replace('option value="0"',
									'option value="0" disabled="disabled"', $statepublished);
								$statepublished = str_replace('option value="2"',
									'option value="2" disabled="disabled"', $statepublished);
								$statepublished = str_replace('option value="-2"',
									'option value="-2" disabled="disabled"', $statepublished);
								echo $statepublished;
							?>
					</div>



					</div><!--EDIT TAB -->

					<div class="tab-pane" id="publishing">

						<div class="form-group">
							<?php echo $this->form->getLabel('tags'); ?>
							<?php echo str_replace('span12', '', $this->form->getInput('tags')); ?>
						</div>

						<?php if ($params->get('save_history', 0)) : ?>
						<div class="form-group">
							<?php echo $this->form->getLabel('version_note'); ?>
							<?php echo $this->form->getInput('version_note'); ?>
						</div>
						<?php endif; ?>

						<div class="form-group">
							<?php echo $this->form->getLabel('created_by_alias'); ?>
							<?php echo $this->form->getInput('created_by_alias'); ?>
						</div>

						<?php if ($this->item->params->get('access-change')) : ?>

							<div class="form-group">
								<?php echo $this->form->getLabel('featured'); ?>
								<?php echo $this->form->getInput('featured'); ?>
							</div>

							<div class="form-group">
								<?php echo $this->form->getLabel('publish_up'); ?>
								<?php echo str_replace('class="btn"', 'class="btn btn-default"', $this->form->getInput('publish_up')); ?>
							</div>

							<div class="form-group">
								<?php echo $this->form->getLabel('publish_down'); ?>
								<?php echo str_replace('class="btn"', 'class="btn btn-default"', $this->form->getInput('publish_down')); ?>
							</div>
						<?php endif; ?>

						<div class="form-group">
							<?php echo $this->form->getLabel('access'); ?>
							<?php echo $this->form->getInput('access'); ?>
						</div>

						<?php if (is_null($this->item->id)):?>
							<div class="form-group">
								<?php echo JText::_('COM_CONTENT_ORDERING'); ?>
							</div>
						<?php endif; ?>
					</div>

					<div class="tab-pane" id="language">
						<div class="form-group">
							<?php echo $this->form->getLabel('language'); ?>
							<?php echo $this->form->getInput('language'); ?>
						</div>
					</div>

					<div class="tab-pane" id="metadata">
						<div class="form-group">
							<?php echo $this->form->getLabel('metadesc'); ?>
							<?php echo $this->form->getInput('metadesc'); ?>
						</div>

						<div class="form-group">
								<?php echo $this->form->getLabel('metakey'); ?>
								<?php echo $this->form->getInput('metakey'); ?>
						</div>

						<input type="hidden" name="task" value="" />
						<input type="hidden" name="return" value="<?php echo $this->return_page; ?>" />
						<?php if ($this->params->get('enable_category', 0) == 1) :?>
						<input type="hidden" name="jform[catid]" value="<?php echo $this->params->get('catid', 1); ?>" />
						<?php endif; ?>
					</div>
				</div>
				<div class="btn-toolbar">
					<div class="btn-group">
						<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('article.save')">
							<span class="fa fa-ok"></span>&#160;<?php echo JText::_('JSAVE') ?>
						</button>
					</div>
					<div class="btn-group">
						<button type="button" class="btn btn-default" onclick="Joomla.submitbutton('article.cancel')">
							<span class="fa fa-cancel"></span>&#160;<?php echo JText::_('JCANCEL') ?>
						</button>
					</div>
					<?php if ($params->get('save_history', 0)) : ?>
					<div class="btn-group">
						<?php echo $this->form->getInput('contenthistory'); ?>
					</div>
					<?php endif; ?>
				</div>
				<?php echo JHtml::_('form.token'); ?>
			</fieldset>
		</form>
	</div>


<!-- MAP -->
<?php elseif($catid == 29) : ?>

	<div class="edit item-page<?php echo $this->pageclass_sfx; ?>">
		<?php if ($params->get('show_page_heading', 1)) : ?>
		<div class="page-header">
			<h1>
				<?php echo $this->escape($params->get('page_heading')); ?>
			</h1>
		</div>
		<?php endif; ?>

		<form action="<?php echo JRoute::_('index.php?option=com_content&a_id='.(int) $this->item->id); ?>" role="form" method="post" name="adminForm" id="adminForm" class="form-validate">
			<fieldset>
			<div class="btn-toolbar">
					<div class="btn-group force-no-margin">
						<button type="button" class="btn btn-primary validate" onclick="Joomla.submitbutton('article.save')">
							<span class="fa fa-ok"></span>&#160;<?php echo JText::_('J3_EDITOR_SERVICEREQUEST_SUBMIT') ?>
						</button>
					</div>
					<div class="btn-group">
						<button type="button" class="btn btn-default" onclick="Joomla.submitbutton('article.cancel')">
							<span class="fa fa-cancel"></span>&#160;<?php echo JText::_('JCANCEL') ?>
						</button>
					</div>
					<?php if ($params->get('save_history', 0)) : ?>
					<div class="btn-group">
						<?php echo $this->form->getInput('contenthistory'); ?>
					</div>
					<?php endif; ?>
					<br/><br/>
				</div>

				<ul class="nav nav-tabs">
					<li class="active"><a href="#editor" data-toggle="tab"><?php echo "Edit Map" ?></a></li>
					<?php if(false) : ?>
					<li><a href="#extrafields" data-toggle="tab"><?php echo JText::_('T3_EXTRA_FIELDS_GROUP_LABEL') ?></a></li>
					<?php endif; ?>
					<?php if ($params->get('show_urls_images_frontend') ) : ?>
					<?php endif; ?>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="editor">

						<div class="form-group" readonly>
							<?php echo $this->form->getLabel('title'); ?>
							<?php
								$title_input = $this->form->getInput('title');
								// var_dump($title_input);
								// echo str_replace("<input", "<input default='Map' disabled='disabled'", $title_input);
								echo $title_input;
							?>
						</div>

						<?php if (is_null($this->item->id)) : ?>
						<div class="form-group hidden">
							<?php echo $this->form->getLabel('alias'); ?>
							<?php echo $this->form->getInput('alias'); ?>
						</div>
						<?php endif; ?>

						<div class="form-group hidden">
							<?php echo $this->form->getInput('articletext'); ?>
							<br/><br/><br/><br/>
						</div>

						<input type="hidden" value="<?php echo $user->id; ?>" id="userid" />

						<?php if(count($extrafields)) : ?>
						<div class="tab-pane" id="extrafields">
							<?php foreach ($extrafields as $extraset) : ?>
								<?php foreach ($this->form->getFieldset($extraset->name) as $field) : ?>
									<div class="form-group">
										<div class="control-label">
											<?php echo $field->label; ?>
										</div>
										<div class="controls">
											<?php echo $field->input; ?>
										</div>
									</div>
								<?php endforeach ?>
							<?php endforeach ?>
						</div>
						<?php endif; ?>

						<div class="form-group hidden">
							<?php echo $this->form->getLabel('catid'); ?>
							<?php
								$selectcat = $this->form->getInput('catid');

								$selectcat = str_replace('option value="10"',
								 'option value="10" disabled="disabled"',  $selectcat);
								$selectcat = str_replace('option value="8"',
								 'option value="8" disabled="disabled"', $selectcat );
								$selectcat = str_replace('option value="9"',
								 'option value="9" disabled="disabled"', $selectcat );
								$selectcat = str_replace('option value="13"',
								 'option value="9" disabled="disabled"', $selectcat );
								echo $selectcat;
							?>
						</div>
						<?php if ($this->item->params->get('access-change')) : ?>
						<div class="form-group hidden">
							<?php echo $this->form->getLabel('state'); ?>
							<?php
								$statepublished = $this->form->getInput('state');

								$statepublished = str_replace('option value="0"',
									'option value="0" disabled="disabled"', $statepublished);
								$statepublished = str_replace('option value="2"',
									'option value="2" disabled="disabled"', $statepublished);
								$statepublished = str_replace('option value="-2"',
									'option value="-2" disabled="disabled"', $statepublished);
								echo $statepublished;
							?>
						<?php endif; ?>
						</div>

					</div><!--edit pane -->



					<div class="tab-pane" id="publishing">

						<div class="form-group">
							<?php echo $this->form->getLabel('tags'); ?>
							<?php echo str_replace('span12', '', $this->form->getInput('tags')); ?>
						</div>

						<?php if ($params->get('save_history', 0)) : ?>
						<div class="form-group">
							<?php echo $this->form->getLabel('version_note'); ?>
							<?php echo $this->form->getInput('version_note'); ?>
						</div>
						<?php endif; ?>

						<div class="form-group">
							<?php echo $this->form->getLabel('created_by_alias'); ?>
							<?php echo $this->form->getInput('created_by_alias'); ?>
						</div>

						<?php if ($this->item->params->get('access-change')) : ?>
							<div class="form-group">
								<?php echo $this->form->getLabel('featured'); ?>
								<?php echo $this->form->getInput('featured'); ?>
							</div>

							<div class="form-group">
								<?php echo $this->form->getLabel('publish_up'); ?>
								<?php echo str_replace('class="btn"', 'class="btn btn-default"', $this->form->getInput('publish_up')); ?>
							</div>

							<div class="form-group">
								<?php echo $this->form->getLabel('publish_down'); ?>
								<?php echo str_replace('class="btn"', 'class="btn btn-default"', $this->form->getInput('publish_down')); ?>
							</div>
						<?php endif; ?>

						<div class="form-group">
							<?php echo $this->form->getLabel('access'); ?>
							<?php echo $this->form->getInput('access'); ?>
						</div>

						<?php if (is_null($this->item->id)):?>
							<div class="form-group">
								<?php echo JText::_('COM_CONTENT_ORDERING'); ?>
							</div>
						<?php endif; ?>
					</div>

					<div class="tab-pane" id="language">
						<div class="form-group">
							<?php echo $this->form->getLabel('language'); ?>
							<?php echo $this->form->getInput('language'); ?>
						</div>
					</div>

					<div class="tab-pane" id="metadata">
						<div class="form-group">
							<?php echo $this->form->getLabel('metadesc'); ?>
							<?php echo $this->form->getInput('metadesc'); ?>
						</div>

						<div class="form-group">
								<?php echo $this->form->getLabel('metakey'); ?>
								<?php echo $this->form->getInput('metakey'); ?>
						</div>

						<input type="hidden" name="task" value="" />
						<input type="hidden" name="return" value="<?php echo $this->return_page; ?>" />
						<?php if ($this->params->get('enable_category', 0) == 1) :?>
						<input type="hidden" name="jform[catid]" value="<?php echo $this->params->get('catid', 1); ?>" />
						<?php endif; ?>
					</div>
				</div>
				<div class="btn-toolbar">
					<div class="btn-group">
						<button type="button" class="btn btn-primary validate" onclick="Joomla.submitbutton('article.save')">
							<span class="fa fa-ok"></span>&#160;<?php echo JText::_('J3_EDITOR_SERVICEREQUEST_SUBMIT') ?>
						</button>
					</div>
					<div class="btn-group">
						<button type="button" class="btn btn-default" onclick="Joomla.submitbutton('article.cancel')">
							<span class="fa fa-cancel"></span>&#160;<?php echo JText::_('JCANCEL') ?>
						</button>
					</div>
					<?php if ($params->get('save_history', 0)) : ?>
					<div class="btn-group">
						<?php echo $this->form->getInput('contenthistory'); ?>
					</div>
					<?php endif; ?>
				</div>
				<?php echo JHtml::_('form.token'); ?>
			</fieldset>
		</form>
	</div>

<?php else : ?>
	<div class="edit item-page<?php echo $this->pageclass_sfx; ?>">
		<?php if ($params->get('show_page_heading', 1)) : ?>
		<div class="page-header">
			<h1>
				<?php echo $this->escape($params->get('page_heading')); ?>
			</h1>
		</div>
		<?php endif; ?>

		<form action="<?php echo JRoute::_('index.php?option=com_content&a_id='.(int) $this->item->id); ?>" role="form" method="post" name="adminForm" id="adminForm" class="form-validate">
			<fieldset>
				<div class="btn-toolbar">
					<div class="btn-group force-no-margin">
						<button type="button" class="btn btn-primary validate" onclick="Joomla.submitbutton('article.save')">
							<span class="fa fa-ok"></span>&#160;<?php echo JText::_('J3_EDITOR_SERVICEREQUEST_SUBMIT') ?>
						</button>
					</div>
					<div class="btn-group">
						<button type="button" class="btn btn-default" onclick="Joomla.submitbutton('article.cancel')">
							<span class="fa fa-cancel"></span>&#160;<?php echo JText::_('JCANCEL') ?>
						</button>
					</div>
					<?php if ($params->get('save_history', 0)) : ?>
					<div class="btn-group">
						<?php echo $this->form->getInput('contenthistory'); ?>
					</div>
					<?php endif; ?>
					<br/><br/>
				</div>

				<ul class="nav nav-tabs">
					<li class="active"><a href="#editor" data-toggle="tab"><?php echo JText::_('JEDITOR') ?></a></li>
					<?php if(count($extrafields)) : ?>
					<li><a href="#extrafields" data-toggle="tab"><?php echo JText::_('T3_EXTRA_FIELDS_GROUP_LABEL') ?></a></li>
					<?php endif; ?>
					<?php if ($params->get('show_urls_images_frontend') ) : ?>
					<li><a href="#images" data-toggle="tab"><?php echo JText::_('COM_CONTENT_IMAGES_AND_URLS') ?></a></li>
					<?php endif; ?>
					<li><a href="#publishing" data-toggle="tab"><?php echo JText::_('COM_CONTENT_PUBLISHING') ?></a></li>
					<li><a href="#language" data-toggle="tab"><?php echo JText::_('JFIELD_LANGUAGE_LABEL') ?></a></li>
					<li><a href="#metadata" data-toggle="tab"><?php echo JText::_('COM_CONTENT_METADATA') ?></a></li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="editor">

						<div class="form-group">
							<?php echo $this->form->getLabel('title'); ?>
							<?php echo $this->form->getInput('title'); ?>
						</div>

						<?php if (is_null($this->item->id)) : ?>
						<div class="form-group">
							<?php echo $this->form->getLabel('alias'); ?>
							<?php echo $this->form->getInput('alias'); ?>
						</div>
						<?php endif; ?>

						<div class="form-group">
							<?php echo $this->form->getInput('articletext'); ?>
						</div>
					</div>

					<?php if(count($extrafields)) : ?>
					<div class="tab-pane" id="extrafields">
						<?php foreach ($extrafields as $extraset) : ?>
							<?php foreach ($this->form->getFieldset($extraset->name) as $field) : ?>
								<div class="form-group">
									<div class="control-label">
										<?php echo $field->label; ?>
									</div>
									<div class="controls">
										<?php echo $field->input; ?>
									</div>
								</div>
							<?php endforeach ?>
						<?php endforeach ?>
					</div>
					<?php endif; ?>

					<?php if ($params->get('show_urls_images_frontend')): ?>
					<div class="tab-pane" id="images">

						<div class="form-group">
							<?php echo $this->form->getLabel('image_intro', 'images'); ?>
							<?php echo $this->form->getInput('image_intro', 'images'); ?>
						</div>

						<div class="form-group">
							<?php echo $this->form->getLabel('image_intro_alt', 'images'); ?>
							<?php echo $this->form->getInput('image_intro_alt', 'images'); ?>
						</div>

						<div class="form-group">
							<?php echo $this->form->getLabel('image_intro_caption', 'images'); ?>
							<?php echo $this->form->getInput('image_intro_caption', 'images'); ?>
						</div>

						<div class="form-group">
							<?php echo $this->form->getLabel('float_intro', 'images'); ?>
							<?php echo $this->form->getInput('float_intro', 'images'); ?>
						</div>

						<div class="form-group">
							<?php echo $this->form->getLabel('image_fulltext', 'images'); ?>
							<?php echo $this->form->getInput('image_fulltext', 'images'); ?>
						</div>

						<div class="form-group">
							<?php echo $this->form->getLabel('image_fulltext_alt', 'images'); ?>
							<?php echo $this->form->getInput('image_fulltext_alt', 'images'); ?>
						</div>

						<div class="form-group">
							<?php echo $this->form->getLabel('image_fulltext_caption', 'images'); ?>
							<?php echo $this->form->getInput('image_fulltext_caption', 'images'); ?>
						</div>

						<div class="form-group">
							<?php echo $this->form->getLabel('float_fulltext', 'images'); ?>
							<?php echo $this->form->getInput('float_fulltext', 'images'); ?>
						</div>

						<div class="form-group">
							<?php echo $this->form->getLabel('urla', 'urls'); ?>
							<?php echo $this->form->getInput('urla', 'urls'); ?>
						</div>

						<div class="form-group">
							<?php echo $this->form->getLabel('urlatext', 'urls'); ?>
							<?php echo $this->form->getInput('urlatext', 'urls'); ?>
						</div>

						<div class="form-group">
							<?php echo $this->form->getInput('targeta', 'urls'); ?>
						</div>

						<div class="form-group">
							<?php echo $this->form->getLabel('urlb', 'urls'); ?>
							<?php echo $this->form->getInput('urlb', 'urls'); ?>
						</div>

						<div class="form-group">
							<?php echo $this->form->getLabel('urlbtext', 'urls'); ?>
							<?php echo $this->form->getInput('urlbtext', 'urls'); ?>
						</div>

						<div class="form-group">
							<?php echo $this->form->getInput('targetb', 'urls'); ?>
						</div>

						<div class="form-group">
							<?php echo $this->form->getLabel('urlc', 'urls'); ?>
							<?php echo $this->form->getInput('urlc', 'urls'); ?>
						</div>

						<div class="form-group">
							<?php echo $this->form->getLabel('urlctext', 'urls'); ?>
							<?php echo $this->form->getInput('urlctext', 'urls'); ?>
						</div>

						<div class="form-group">
							<?php echo $this->form->getInput('targetc', 'urls'); ?>
						</div>

					</div>
					<?php endif; ?>

					<div class="tab-pane" id="publishing">
						<div class="form-group">
							<?php echo $this->form->getLabel('catid'); ?>
							<?php echo $this->form->getInput('catid'); ?>
						</div>

						<div class="form-group">
							<?php echo $this->form->getLabel('tags'); ?>
							<?php echo str_replace('span12', '', $this->form->getInput('tags')); ?>
						</div>

						<?php if ($params->get('save_history', 0)) : ?>
						<div class="form-group">
							<?php echo $this->form->getLabel('version_note'); ?>
							<?php echo $this->form->getInput('version_note'); ?>
						</div>
						<?php endif; ?>

						<div class="form-group">
							<?php echo $this->form->getLabel('created_by_alias'); ?>
							<?php echo $this->form->getInput('created_by_alias'); ?>
						</div>

						<?php if ($this->item->params->get('access-change')) : ?>
							<div class="form-group">
								<?php echo $this->form->getLabel('state'); ?>
								<?php echo $this->form->getInput('state'); ?>
							</div>

							<div class="form-group">
								<?php echo $this->form->getLabel('featured'); ?>
								<?php echo $this->form->getInput('featured'); ?>
							</div>

							<div class="form-group">
								<?php echo $this->form->getLabel('publish_up'); ?>
								<?php echo str_replace('class="btn"', 'class="btn btn-default"', $this->form->getInput('publish_up')); ?>
							</div>

							<div class="form-group">
								<?php echo $this->form->getLabel('publish_down'); ?>
								<?php echo str_replace('class="btn"', 'class="btn btn-default"', $this->form->getInput('publish_down')); ?>
							</div>
						<?php endif; ?>

						<div class="form-group">
							<?php echo $this->form->getLabel('access'); ?>
							<?php echo $this->form->getInput('access'); ?>
						</div>

						<?php if (is_null($this->item->id)):?>
							<div class="form-group">
								<?php echo JText::_('COM_CONTENT_ORDERING'); ?>
							</div>
						<?php endif; ?>
					</div>

					<div class="tab-pane" id="language">
						<div class="form-group">
							<?php echo $this->form->getLabel('language'); ?>
							<?php echo $this->form->getInput('language'); ?>
						</div>
					</div>

					<div class="tab-pane" id="metadata">
						<div class="form-group">
							<?php echo $this->form->getLabel('metadesc'); ?>
							<?php echo $this->form->getInput('metadesc'); ?>
						</div>

						<div class="form-group">
								<?php echo $this->form->getLabel('metakey'); ?>
								<?php echo $this->form->getInput('metakey'); ?>
						</div>

						<input type="hidden" name="task" value="" />
						<input type="hidden" name="return" value="<?php echo $this->return_page; ?>" />
						<?php if ($this->params->get('enable_category', 0) == 1) :?>
						<input type="hidden" name="jform[catid]" value="<?php echo $this->params->get('catid', 1); ?>" />
						<?php endif; ?>
					</div>
				</div>
				<div class="btn-toolbar">
					<div class="btn-group">
						<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('article.save')">
							<span class="fa fa-ok"></span>&#160;<?php echo JText::_('JSAVE') ?>
						</button>
					</div>
					<div class="btn-group">
						<button type="button" class="btn btn-default" onclick="Joomla.submitbutton('article.cancel')">
							<span class="fa fa-cancel"></span>&#160;<?php echo JText::_('JCANCEL') ?>
						</button>
					</div>
					<?php if ($params->get('save_history', 0)) : ?>
					<div class="btn-group">
						<?php echo $this->form->getInput('contenthistory'); ?>
					</div>
					<?php endif; ?>
				</div>
				<?php echo JHtml::_('form.token'); ?>
			</fieldset>
		</form>
	</div>
<?php endif; ?>
