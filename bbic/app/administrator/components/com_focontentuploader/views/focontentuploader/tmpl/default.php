<?php
/**
 * Copyright (C) 2014 freakedout (www.freakedout.de)
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 * */

// no direct access
defined('_JEXEC') or die('Restricted access');?>

<script language="javascript">
    var configValue;
    window.addEvent('domready', function(){
        enableField('featured', 'featured_col');
        addHiddenInput();
    });

    function enableField(id1, id2) {
        if(document.getElementById(id1) && document.getElementById(id1).checked == true){
            document.getElementById(id2).disabled = false;
        } else if (document.getElementById(id2)) {
            document.getElementById(id2).disabled = true;
        }
    }

    function addHiddenInput(){
        var name = document.getElementById('config_name').value;
        document.getElementById('configname').value = name;
        var id = document.getElementById('configid').value;
    }

    function saveconfig() {
        if (document.configForm.config_name.value == "") {
            alert('<?php echo JText::_('CU_ERRORCONFIGNAME', true);?>');
        } else if ( document.settingsForm.firstdatarow.value == "" ) {
            alert('<?php echo JText::_('CU_ERRORFIRSTDATAROW', true);?>');
        } else {
            document.settingsForm.submit();
        }
    }

    Joomla.submitbutton = function(pressbutton) {
        if (pressbutton == 'csv') {
            document.configForm.task.value = 'csv';
            document.configForm.submit();
        } else if (pressbutton == 'deleteconfig') {
            if( confirm('<?php echo JText::_('CU_ARE_YOU_SURE_TO_DELETE_CONFIG', true); ?>') ){
            document.configForm.task.value = 'deleteconfig';
            document.configForm.submit();
            }
        } else if (document.adminForm.file.value == "") {
            alert('<?php echo JText::_('CU_ERRORNOFILE', true);?>');
        } else {
            document.adminForm.submit();
        }
    }

    !function($){
        $(document).ready(function(){
            if ($('#contentEditor').length > 0) {
                $('.tab-pane').width($('#contentEditor').width());
            }
        });
    }(window.jQuery);
</script>

<div class="cuContainer">
    <form action="index.php?option=com_focontentuploader" enctype="multipart/form-data" method="post" name="adminForm" id="adminForm">
        <div id="getstarted">
            <div id="getstartedtxt">
                <h3><?php echo JText::_('CU_WELCOME_TO_THE_FREAKEDOUT');?> <span class="red"><?php echo JText::_('CU_CONTENT_UPLOADER_FREE');?>!</span></h3>
                <p><?php echo JText::_('CU_SELECT_THE_CATEGORY');?><a id="paramslink" class="red pointer" onclick="window.scroll(0,270)"> <?php echo JText::_('CU_CONFIGURATION');?></a>.  <?php echo JText::_('CU_INSTRUCTIONS');?> <a class="red" target="_blank" href="https://www.freakedout.de/support/documentation"><?php echo JText::_('CU_DOCUMENTATION');?></a>.</p>
            </div>
        </div>
        <div id="uploadFileContainer" class="whiteBox clearfix">
            <label for="file" class="inline left" style="padding: 7px 0 0 0;font-weight: bold;">
                <?php echo JText::_('CU_UPLOAD_FILE');?>:
            </label>
            <div class="fileupload fileupload-new inline left" data-provides="fileupload">
                <div class="input-append">
                    <div class="uneditable-input span3">
                        <i class="icon-file fileupload-exists"></i>
                        <span class="fileupload-preview"></span>
                    </div>
                    <span class="btn btn-file">
                        <span class="fileupload-new"><?php echo JText::_('CU_SELECT_FILE');?></span>
                        <span class="fileupload-exists"><?php echo JText::_('CU_CHANGE');?></span>
                        <input type="file" name="file" id="uploadFile" class="hasTip" title="<?php echo JText::_('CU_TIPUPLOADFILE');?>" />
                    </span><a href="#" class="btn fileupload-exists" data-dismiss="fileupload"><?php echo JText::_('CU_REMOVE');?></a>
                </div>
            </div>
            <input type="button" class="btn btn-primary inline left" value="<?php echo JText::_('CU_START_UPLOAD');?>" onclick="Joomla.submitbutton('startupload')" style="margin-top: 3px;" />
        </div>
        <input type="hidden" name="option" value="com_focontentuploader" />
        <input type="hidden" name="task" value="startupload" />
        <input type="hidden" name="controller" value="focontentuploader" />
        <input type="hidden" name="component" value="content" />
        <input type="hidden" name="configdelete" value="<?php echo $this->config['configid']; ?>" />
    </form>

    <div style="margin: 1em 10px;"><?php echo JText::_('CU_CHOOSECONFIG'); ?></div>

    <table id="configTable">
        <tr>
            <td>
                <div id="configTableBox" class="whiteBox">
                    <form action="index.php?option=com_focontentuploader" method="post" name="configForm" enctype="multipart/form-data" id="configForm">
                        <div>
                            <strong><?php echo JText::_('CU_MANAGE_CONFIGURATIONS');?></strong>
                        </div>
                        <br />
                        <div class="input-prepend inline left">
                            <span class="add-on hasTip" title="<?php echo JText::_('CU_TIPCONFIGNAME');?>">
                                <?php echo JText::_('CU_CONFIG_NAME');?>
                            </span>
                            <input type="text" name="config[config_name]" id="config_name" size="20" value="<?php echo htmlspecialchars(stripslashes($this->config['configname']));?>" onchange="addHiddenInput()" />
                        </div>
                        <div class="inline left" style="margin: 0 10px;">
                            <?php echo $this->lists['configid'];?>
                        </div>
                        <div title="<?php echo JText::_('CU_TIPSAVECONFIG');?>" class="hasTip inline left">
                            <input type="button" name="save" class="btn btn-primary" onclick="saveconfig();" value="<?php echo JText::_('CU_SAVE');?>" />
                        </div>
                        <div title="<?php echo JText::_('CU_TIPDELETE');?>" class="hasTip inline right">
                            <input type="button" name="deleteconfig" class="btn" value="<?php echo JText::_('CU_DELETE');?>" onclick="alert('<?php echo JText::_('CU_PRO_FEATURE');?>')" />
                        </div>
                        <div title="<?php echo JText::_('CU_TIPCSV');?>" class="hasTip inline right" style="margin-right: 10px;">
                            <input type="button" name="csv" class="btn" value="<?php echo JText::_('CU_CSV');?>" onclick="alert('<?php echo JText::_('CU_PRO_FEATURE');?>')" />
                        </div>
                        <input type="hidden" name="option" value="com_focontentuploader" />
                        <input type="hidden" name="controller" value="focontentuploader" />
                        <input type="hidden" name="task" value="changeconfig" />
                        <input type="hidden" name="component" value="content" />
                        <input type="hidden" name="configdelete" value="<?php echo $this->config['configid']; ?>" />
                    </form>
                </div>
            </td>
            <td width="322">
                <div id="uploadConfigTableBox" class="whiteBox">
                    <form action="index.php?option=com_focontentuploader" enctype="multipart/form-data" method="post" name="uploadconfigForm">
                        <div>
                            <strong><?php echo JText::_('CU_UPLOAD_CONFIGURATION');?></strong>
                        </div>
                        <br />
                        <div class="fileupload fileupload-new inline left" data-provides="fileupload">
                            <div class="input-append">
                                <div class="uneditable-input" style="width: 50px;">
                                    <i class="icon-file fileupload-exists"></i>
                                    <span class="fileupload-preview"></span>
                                </div>
                                <span class="btn btn-file">
                                    <span class="fileupload-new"><?php echo JText::_('CU_SELECT_FILE');?></span>
                                    <span class="fileupload-exists"><?php echo JText::_('CU_CHANGE');?></span>
                                    <input type="file" name="configfile" id="configfile" class="hasTip" title="<?php echo JText::_('CU_TIPCONFIGFILE');?>" />
                                </span><a href="#" class="btn fileupload-exists" data-dismiss="fileupload"><?php echo JText::_('CU_REMOVE');?></a>
                            </div>
                        </div>
                        <input type="submit" value="<?php echo JText::_('CU_SUBMIT');?>" onclick="alert('<?php echo JText::_('CU_PRO_FEATURE');?>')" title="<?php echo JText::_('CU_TIPSUBMITCONFIG');?>" class="hasTip btn inline left" style="margin-top: 3px;" />
                        <input type="hidden" name="option" value="com_focontentuploader" />
                        <input type="hidden" name="controller" value="focontentuploader" />
                        <input type="hidden" name="task" value="uploadconfig" />
                        <input type="hidden" name="component" value="content" />
                    </form>
                </div>
            </td>
        </tr>
    </table>

    <form action="index.php?option=com_focontentuploader" method="post" name="settingsForm" enctype="multipart/form-data">
        <table>
            <tr>
                <td valign="top">
                    <div class="whiteBox">
                        <table class="paramsTable" width="100%">
                            <colgroup width="120" />
                            <colgroup width="80" />
                            <colgroup width="150" />
                            <colgroup width="80" />
                            <colgroup width="100" />
                            <colgroup width="" />
                            <tr>
                                <td>
                                    <label for="firstdatarow" title="<?php echo JText::_('CU_TIPFIRSTDATAROW'); ?>" class="hasTip"><?php echo JText::_('CU_FIRST_DATA_ROW'); ?></label>
                                </td>
                                <td>
                                    <input class="columnRef" type="text" name="config[firstdatarow]" id="firstdatarow" size="4" maxlength="5" value="<?php echo $this->config['firstdatarow']; ?>" />
                                </td>
                                <td nowrap="nowrap">
                                    <label for="max_articles" title="<?php echo JText::_('CU_TIPMAXARTICLES'); ?>" class="hasTip"><?php echo JText::_('CU_MAX_ARTICLES'); ?></label>
                                </td>
                                <td colspan="3">
                                    <input class="columnRef" type="text" name="config[max_articles]" id="max_articles" size="4" maxlength="5"	value="50" readonly="readonly" />
                                    <span style="position: relative;top: -4px;left: 10px;">
                                        (<?php echo JText::_('CU_MAX_ARTICLES_PRO');?>)
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="title" title="<?php echo JText::_('CU_TIPTITLECOL'); ?>" class="hasTip"><?php echo JText::_('JGLOBAL_TITLE'); ?></label>
                                </td>
                                <td>
                                    <input class="columnRef" type="text" name="config[title]" id="title" size="4" maxlength="2" value="<?php echo $this->config['title']; ?>" />
                                </td>
                                <td>
                                    <label for="alias" title="<?php echo JText::_('CU_TIPALIAS'); ?>" class="hasTip"><?php echo JText::_('JFIELD_ALIAS_LABEL'); ?></label>
                                </td>
                                <td>
                                    <input class="columnRef" type="text" name="config[alias]"	id="alias" size="4" maxlength="2" value="<?php echo $this->config['alias']; ?>" />
                                </td>
                                <td>
                                    <label for="state_col" title="<?php echo JText::_('CU_TIPSTATECOL'); ?>" class="hasTip"><?php echo JText::_('JPUBLISHED'); ?></label>
                                </td>
                                <td>
                                    <div class="radio btn-group inline left">
                                        <?php echo $this->lists['state']; ?>
                                    </div>
                                    <span style="position:relative;top: 4px;">
                                        (<?php echo JText::_('CU_PRO_FEATURE_INDIVIDUALLY');?>)
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="catid" title="<?php echo JText::_('CU_TIPCATID'); ?>" class="hasTip"><?php echo JText::_('JCATEGORY'); ?></label>
                                </td>
                                <td colspan="3">
                                    <select name="catid" id="catid" style="width: 88%">
                                        <option value="-1"><?php echo JText::_('JOPTION_SELECT_CATEGORY'); ?></option>
                                        <?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_content'), 'value', 'text', $this->config['catid']); ?>
                                    </select>
                                </td>
                                <td>
                                    <label for="featured" title="<?php echo JText::_('CU_TIPFEATUREDCOL'); ?>" class="hasTip"><?php echo JText::_('JFEATURED'); ?></label>
                                </td>
                                <td>
                                    <div class="radio btn-group inline left">
                                        <?php echo $this->lists['featured'];?>
                                    </div>
                                    <span style="position:relative;top: 4px;">
                                        (<?php echo JText::_('CU_PRO_FEATURE_INDIVIDUALLY');?>)
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="catcol" title="<?php echo JText::_('CU_TIPCATCOL'); ?>" class="hasTip"><?php echo JText::_('CU_CATEGORY_COLUMN'); ?></label>
                                </td>
                                <td>
                                    <input type="text" name="config[catcol]" id="catcol" class="columnRef" size="4" maxlength="2" value="<?php echo $this->config['catcol']; ?>" />
                                </td>
                                <td>
                                    <label for="catcolRef" title="<?php echo JText::_('CU_TIPPARENTCATCOLK2'); ?>" class="hasTip"><?php echo JText::_('CU_PARENT_CATEGORY_COLUMN'); ?></label>
                                </td>
                                <td>
                                    <input type="text" name="config[parentcatcol]" id="catcolRef" class="columnRef" size="4" maxlength="2" value="<?php echo @$this->config['parentcatcol']; ?>" />
                                </td>
                                <td>
                                    <label for="removetags" title="<?php echo JText::_('CU_TIPREMOVETAGS'); ?>" class="hasTip"><?php echo JText::_('CU_REMOVE_HTML'); ?></label>
                                </td>
                                <td>
                                    <div class="radio btn-group inline left">
                                        <?php echo $this->lists['removetags']; ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="catstate" title="<?php echo JText::_('CU_TIPCATSTATE'); ?>" class="hasTip"><?php echo JText::_('CU_CATSTATE'); ?></label>
                                </td>
                                <td>
                                    <div class="radio btn-group inline left">
                                        <?php echo $this->lists['catstate']; ?>
                                    </div>
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr style="height:auto">
                                <td colspan="99"><?php echo JText::_('CU_IF_CATEGORYDD'); ?></td>
                            </tr>
                        </table>
                    </div>
                    <div id="content-data-tabs" class="whiteBox" style="margin-top: 3px;">
                        <?php echo JHtml::_('bootstrap.startTabSet', 'contentData', array('active' => 'contentEditor')); ?>
                        <?php echo JHtml::_('bootstrap.addTab', 'contentData', 'contentEditor', JText::_('CU_CONTENT_ARTICLE_CONTENT', true)); ?>
                            <?php echo JText::_('CU_EDITORINFO');?>
                            <br />
                            <br />
                            <?php echo JFactory::getEditor()->display('config[fulltext]', $this->config['introtext'], '100%', '550', '75', '20'); ?>
                            <?php //echo $this->form->getInput('config[fulltext]'); ?>
                        <?php echo JHtml::_('bootstrap.endTab'); ?>
                        <?php echo JHtml::_('bootstrap.addTab', 'contentData', 'contentImages', JText::_('CU_URLS_AND_IMAGES', true)); ?>
                            <h3 class="red"><?php echo JText::_('CU_PRO_FEATURE'); ?></h3>
                            <div class="span6">
                                <h4><?php echo JText::_('CU_IMAGE_OPTIONS'); ?></h4>
                                <?php foreach ($this->form->getFieldset('images') as $field) : ?>
                                    <div class="control-group">
                                        <?php echo $field->label; ?>
                                        <div class="controls">
                                            <?php echo $field->input; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="span6">
                                <h4><?php echo JText::_('CU_URL_OPTIONS'); ?></h4>
                                <?php foreach ($this->form->getFieldset('urls') as $field) : ?>
                                    <div class="control-group">
                                        <?php echo $field->label; ?>
                                        <div class="controls">
                                            <?php echo $field->input; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div style="clear: both;"></div>
                        <?php echo JHtml::_('bootstrap.endTab'); ?>
                        <?php if (version_compare(JVERSION, '3.1') >= 0) : ?>
                            <?php echo JHtml::_('bootstrap.addTab', 'contentData', 'contentAssoc', JText::_('JGLOBAL_FIELDSET_ASSOCIATIONS', true)); ?>
                                <h3 class="red"><?php echo JText::_('CU_PRO_FEATURE'); ?></h3>
                                <?php echo '<p>' . JText::_('CU_ASSOCIATIONS_DESC') . '</p><br />';
                                foreach ($this->languages as $languageTag => $lang) : ?>
                                    <div class="control-group">
                                        <label for="assoc_<?php echo $lang->lang_code;?>"><?php echo $lang->title;?></label>
                                        <div class="controls">
                                            <input type="text" value="" class="columnRef" disabled="disabled" />
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php echo JHtml::_('bootstrap.endTab'); ?>
                        <?php endif; ?>
                        <?php echo JHtml::_('bootstrap.endTabSet'); ?>
                    </div>
                </td>
                <td valign="top" width="322">
                    <?php
                    $panels = array(
                        array(
                            'title' => 'JGLOBAL_FIELDSET_PUBLISHING',
                            'fieldset' => 'publishing'
                        ),
                        array(
                            'title' => 'COM_CONTENT_ATTRIBS_ARTICLE_SETTINGS_LABEL',
                            'fieldset' => 'article'
                        ),
                        array(
                            'title' => 'JGLOBAL_FIELDSET_METADATA_OPTIONS',
                            'fieldset' => 'metadata'
                        )
                    );
                    if (version_compare(JVERSION, '3.0.0') >= 0) {
                        ?><div class="whiteBox params"><?php
                        $options = array(
                            'onActive' => 'function(title, description){
                                description.setStyle("display", "block");
                                title.addClass("open").removeClass("closed");
                            }',
                            'onBackground' => 'function(title, description){
                                description.setStyle("display", "none");
                                title.addClass("closed").removeClass("open");
                            }',
                            'startOffset' => 0,
                            'useCookie' => true,
                            'allowAllClose' => true
                        );
                        echo JHtml::_('tabs.start', 'params', $options);

                        foreach ($panels as $panel) {
                            echo JHtml::_('tabs.panel', JText::_($panel['title']), 'params');
                            foreach ($this->form->getFieldset($panel['fieldset']) as $field) : ?>
                                <div class="control-group">
                                    <?php echo $field->label; ?>
                                    <div class="controls">
                                        <?php echo $field->input; ?>
                                    </div>
                                </div>
                            <?php endforeach;
                        }
                        echo JHtml::_('tabs.end');
                        ?></div><?php
                    } else {
                        jimport('joomla.html.pane');
                        $pane = JPane::getInstance('sliders', array('allowAllClose' => true));
                        echo $pane->startPane('menu-pane');
                        foreach ($panels as $panel) {
                            echo $pane->startPanel(JText :: _($panel['title']), 'param-page');
                            foreach ($this->form->getFieldset($panel['fieldset']) as $field) { ?>
                                <div class="control-group">
                                    <?php echo $field->label; ?>
                                    <div class="controls">
                                        <?php echo $field->input; ?>
                                    </div>
                                </div><?php
                                if ($field->name == 'created_by') {
                                    echo '<br style="clear:left" />';
                                }
                            }
                            echo $pane->endPanel();
                        }
                        echo $pane->endPane();
                    } ?>
                    <fieldset id="upgradeInfo" class="panelform whiteBox">
                        <h3>
                            <?php echo JText::_('CU_UPGRADE_TO_PRO');?>
                        </h3>
                        <a href="https://www.freakedout.de/joomla-extensions/content-uploader/?utm_source=CU%2BFree&utm_medium=backend&utm_campaign=CU%2Bgo%2Bpro%2Blink" target="_blank">
                            <img src="<?php echo Juri::root();?>media/com_focontentuploader/images/become-professional.png" alt="<?php echo JText::_('CU_GET_PRO');?>" />
                        </a>
                        <?php echo JText::_('CU_UPGRADE_TO_PRO_DESC');?>
                        <div style="width: 100%;text-align: center;">
                            <a class="upgradeToProButton" href="https://www.freakedout.de/joomla-extensions/content-uploader/?utm_source=CU%2BFree&utm_medium=backend&utm_campaign=CU%2Bgo%2Bpro%2Blink" target="_blank">
                                www.freakedout.de
                            </a>
                            <br />
                            <a class="red" href="https://www.freakedout.de/joomla-extensions/content-uploader/?utm_source=CU%2BFree&utm_medium=backend&utm_campaign=CU%2Bgo%2Bpro%2Blink" target="_blank">
                                www.freakedout.de
                            </a>
                        </div>
                    </fieldset>
                </td>
            </tr>
        </table>
        <input type="hidden" name="configname" id="configname" value="<?php echo $this->config['configname']; ?>" />
        <input type="hidden" name="configid" id="configid" value="<?php echo $this->config['configid']; ?>" />
        <input type="hidden" name="option" value="com_focontentuploader" />
        <input type="hidden" name="controller" value="focontentuploader" />
        <input type="hidden" name="task" value="savesettings" />
        <input type="hidden" name="component" value="content" />
    </form>
    <div class="clr"></div>

    <div style="width: 100%;text-align: center;">
        <?php echo JText::_('CU_CONTENT_UPLOADER_FREE') . ' | Version: ' . foContentUploaderController::$cuVersion . ' | <a href="http://www.freakedout.de" target="_blank">www.freakedout.de</a>';?>
    </div>
</div>
