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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
**/

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_COMPONENT_ADMINISTRATOR . '/lib/phpexcel/Classes/PHPExcel/IOFactory.php');

class foContentUploaderControllerfoContentUploader extends foContentUploaderController {

    private $db;
    private $app;

    public function __construct() {
        parent::__construct();

        $this->db = JFactory::getDBO();
        $this->app = JFactory::getApplication();

        $this->registerTask('startupload', 'upload');
        $this->registerTask('savesettings', 'save');
    }

    public function upload() {
        set_time_limit(0);
        jimport('joomla.database.table.category');
        JPluginHelper::importPlugin('content');
        $user = JFactory::getUser();
		$dispatcher = JDispatcher::getInstance();
		$helper = new foContentUploaderHelper();
		$id = $helper->getCurrentConfigID();
		$config = $helper->getConfig($id);
		$timezone = date('e');
		$tzoffset = JFactory::getConfig()->get('offset');

		$uploadfile = $_FILES['file']['tmp_name'];

		if (!$uploadfile) {
			$this->app->enqueueMessage(JText::_('CU_ERRORNOFILE'), 'error');
			$this->app->redirect('index.php?option=com_focontentuploader');
		}

		if ($_FILES['file']['type'] == 'application/zip') {
			$uploadfile = $helper->zipfile($uploadfile);
		}

        $objPHPExcel = PHPExcel_IOFactory::load($uploadfile);
		$worksheet = $objPHPExcel->getActiveSheet();
		$calendar = PHPExcel_Shared_Date::getExcelCalendar();

        if (isset($objPHPExcel->fileType)) {
			$filetype = $objPHPExcel->fileType;
		} else {
			$filetype = '';
		}

        $first_row = (int)$config['firstdatarow'];
		$last_col = $helper->chr2col($worksheet->getHighestColumn());

        include(JPATH_COMPONENT . '/lib/phpexcel/Classes/PHPExcel/Reader/ExcelProcessor.php');

		$rec = array();

        $query = $this->db->getQuery(true)
            ->select('MAX(ordering)')
            ->from('#__content')
            ->where('state > -1');
		$this->db->setQuery($query);
		$a_order = $this->db->loadResult();

        $query = $this->db->getQuery(true)
            ->select('MAX(ordering)')
            ->from('#__content_frontpage');
		$this->db->setQuery($query);
		$f_order = $this->db->loadResult();

		for ($i = $first_row; $i <= $last_row; $i++) {

			$row = JTable::getInstance('content');

			$a_order++;
			$row->ordering = (int)($a_order);

            $attribs = array(
                'show_title'            => $config['show_title'],
                'link_titles'           => $config['link_titles'],
                'show_tags'             => $config['show_tags'],
                'show_intro'            => $config['show_intro'],
                'info_block_position'   => $config['info_block_position'],
                'show_category'         => $config['show_category'],
                'link_category'         => $config['link_category'],
                'show_parent_category'  => $config['show_parent_category'],
                'link_parent_category'  => $config['link_parent_category'],
                'show_author'           => $config['show_author'],
                'link_author'           => $config['link_author'],
                'show_create_date'      => $config['show_create_date'],
                'show_modify_date'      => $config['show_modify_date'],
                'show_publish_date'     => $config['show_publish_date'],
                'show_item_navigation'  => $config['show_item_navigation'],
                'show_icons'            => $config['show_icons'],
                'show_print_icon'       => $config['show_print_icon'],
                'show_email_icon'       => $config['show_email_icon'],
                'show_vote'             => $config['show_vote'],
                'show_hits'             => $config['show_hits'],
                'show_noauth'           => $config['show_noauth'],
                'alternative_readmore'  => $config['alternative_readmore'],
                'article_layout'        => $config['article_layout']
            );
            $row->attribs = json_encode($attribs);

            $row->language = ($config['language']) ? $config['language'] : '*';

			$row->created_by = (isset($config['created_by']) && is_numeric($config['created_by']))
                ? $config['created_by'] : $user->get('id');

			$created_by_alias = str_replace(array('{', '}'), '', $config['created_by_alias']);
			if ($helper->placeholders($created_by_alias) && $helper->check_col($created_by_alias)) {
				$col = $helper->chr2col($created_by_alias);
				$row->created_by_alias = $worksheet->getCellByColumnAndRow($col, $i)->getValue();
			}

            // date fields
            $fields = array('created', 'publish_up', 'publish_down');
            foreach ($fields as $field) {
                ${$field} = str_replace(array('{', '}'), '', $config[$field]);

                if (${$field}) {
                    // Check to see if the created var is in format YYYY-MM-DD HH:MM:SS or YYYY-MM-DD
                    if ($helper->check_iso_date(${$field})) {
                        ${$field} = $helper->dateTimeToGMT($helper->check_iso_date(${$field}));
                        $row->{$field} = $helper->check_iso_date(${$field});
                    // check to see if col ref A-IV
                    } else if ($helper->check_col(${$field})) {
                        $col = $helper->chr2col(${$field});
                        $datetime = $worksheet->getCellByColumnAndRow($col, $i)->getValue();
                        if ($datetime != '0000-00-00 00:00:00') {
                            $datetime = $helper->time_format($datetime, $tzoffset, $timezone, $calendar, $filetype);
                            $row->{$field} = $helper->dateTimeToGMT($helper->check_iso_date($datetime));
                        } else {
                            $row->{$field} = ($field == 'publish_down') ? $this->db->getNullDate() : JFactory::getDate('now', $tzoffset)->toSql();
                        }
                    }
                } else {
                    $row->{$field} = ($field == 'publish_down') ? $this->db->getNullDate() : JFactory::getDate('now', $tzoffset)->toSql();
                }
            }

			$cmetadesc = $cmetakey = $cmetarobots = $cmetaauthor = $cmetarights = $cmetaxreference = 0;

			$metadesc = $config['description'];
			if ($helper->placeholders($metadesc)) {
				$cmetadesc = 1;
			} else if ($helper->check_col($metadesc)) {
				$col = $helper->chr2col($metadesc);
				$metadesc = $helper->clean_data($worksheet->getCellByColumnAndRow($col,$i)->getValue());
				$jfarray['metadesc'] = $col;
			}

			$metakey = $config['keywords'];
			if ($helper->placeholders($metakey)) {
				$cmetakey = 1;
			} else if ($helper->check_col($metakey)) {
				$col = $helper->chr2col($metakey);
				$metakey = $helper->clean_data($worksheet->getCellByColumnAndRow($col,$i)->getValue());
				$jfarray['metakey'] = $col;
			}

			$metarobotsSelect = $config['robots_select'];
			$metarobots = $config['robots'];

			if( $metarobotsSelect ){
				$metarobots = $metarobotsSelect;
			} else {
				if ($helper->placeholders($metarobots)) {
					$cmetarobots = 1;
				} else if ($helper->check_col($metarobots)) {
					$metarobots = $helper->chr2col($metarobots);
					$metarobots = $helper->clean_data($worksheet->getCellByColumnAndRow($metarobots,$i)->getValue());
				}
			}

			$metaauthor = $config['author'];
			if ($helper->placeholders($metaauthor)) {
				$cmetaauthor = 1;
			} elseif ($helper->check_col($metaauthor)) {
				$metaauthor = $helper->chr2col($metaauthor);
				$metaauthor = $helper->clean_data($worksheet->getCellByColumnAndRow($metaauthor,$i)->getValue());
			}

			$metarights = $config['rights'];
			if ($helper->placeholders($metarights)) {
				$cmetarights = 1;
			} elseif ($helper->check_col($metarights)) {
				$metarights = $helper->chr2col($metarights);
				$metarights = $helper->clean_data($worksheet->getCellByColumnAndRow($metarights,$i)->getValue());
			}

			$metaxreference = $config['xreference'];
			if ($helper->placeholders($metaxreference)) {
				$cmetaxreference = 1;
			} else if ($helper->check_col($metaxreference)) {
				$metaxreference = $helper->chr2col($metaxreference);
				$metaxreference = $helper->clean_data($worksheet->getCellByColumnAndRow($metaxreference,$i)->getValue());
			}

			$introtext_format = str_replace('&#61;', '=', $config['introtext']);
			$content_format   = str_replace('&#61;', '=', $config['fulltext']);

            if (!$content_format && !$introtext_format) {
                $this->app->enqueueMessage(JText::_('CU_ERRORNOCONTENT'), 'error');
				$this->app->redirect('index.php?option=com_focontentuploader');
			}

			$createdsum = $publish_upsum = $publish_downsum = 0;

            for ($j = 0; $j <= $last_col; $j++) {
				$rec[$i][$j] = $helper->clean_data($worksheet->getCellByColumnAndRow($j,$i)->getValue());
				$rec[$i][$j] = str_replace(array("`", "‘"), "'", $rec[$i][$j]);
				$current_rec = $rec[$i][$j];
				$jchr = "{" . $helper->col2chr($j+1) . "}";
				$introtext_format = str_replace($jchr, $current_rec, $introtext_format);
				$content_format   = str_replace($jchr, $current_rec, $content_format);

				if ($cmetadesc) {
					$metadesc = str_replace($jchr, $current_rec, $metadesc);
				}
				if ($cmetakey) {
					$metakey = str_replace($jchr, $current_rec, $metakey);
				}
				if ($cmetarobots) {
					$metarobots = str_replace($jchr, $current_rec, $metarobots);
				}
				if ($cmetaauthor) {
					$metaauthor = str_replace($jchr, $current_rec, $metaauthor);
				}
				if ($cmetarights) {
					$metarights = str_replace($jchr, $current_rec, $metarights);
				}
				if ($cmetaxreference) {
					$metaxreference = str_replace($jchr, $current_rec, $metaxreference);
				}
			}

			if (preg_match_all('%\{cuif ([a-z]{1,2})\}%i', $introtext_format, $colums)) {
				foreach ($colums[1] as $col) {
					$colNr = $helper->chr2col($col);
					$hasContent = $worksheet->getCellByColumnAndRow($colNr, $i)->getValue();
					if (!$hasContent) {
						$introtext_format = preg_replace('%\{cuif ' . $col . '\}.*?\{/cuif\}%si', '', $introtext_format);
					}
				}
				$introtext_format = preg_replace('%\{cuif [a-z]{1,2}\}|\{/cuif\}%i', '', $introtext_format);
			}

			if (preg_match_all('%\{cuif ([a-z]{1,2})\}%i', $content_format, $colums)) {
				foreach ($colums[1] as $col) {
					$colNr = $helper->chr2col($col);
					$hasContent = $worksheet->getCellByColumnAndRow($colNr, $i)->getValue();
					if (!$hasContent) {
						$content_format = preg_replace('%\{cuif ' . $col . '\}.*?\{/cuif\}%si', '', $content_format);
					}
				}
				$content_format = preg_replace('%\{cuif [a-z]{1,2}\}|\{/cuif\}%i', '', $content_format);
			}

			if (!$metarobots) {
				$metarobots = '';
			}
			if (!$metaauthor) {
				$metaauthor = '';
			}

			$row->metadesc   = $metadesc;
			$row->metakey    = $metakey;
			$row->xreference = $metaxreference;

            $meta = array();
			$meta['robots']    = $metarobots;
			$meta['author']    = $metaauthor;
			$meta['rights']    = $metarights;
			$meta['xreference'] = $metaxreference;
			$row->metadata  = json_encode($meta);

			$title = $config['title'];
			if ($helper->check_col($title)) {
				$col = $helper->chr2col($title);
				$articletitle = $helper->clean_data($worksheet->getCellByColumnAndRow($col, $i)->getValue());
			} else {
				$articletitle = $helper->clean_data($worksheet->getCellByColumnAndRow(1, $i)->getValue());
			}

			if (!$articletitle) {
				$this->app->enqueueMessage(JText::_('CU_ERRORNOTITLES'), 'error');
				$this->app->redirect('index.php?option=com_focontentuploader');
			}

			$row->title = $articletitle;
			$title_alias = $config['alias'];

			if ($helper->check_col($title_alias)) {
				$col = $helper->chr2col($title_alias);
				$alias = $helper->clean_data($worksheet->getCellByColumnAndRow($col,$i)->getValue());
				$alias = $helper->slugify($alias);
				$row->alias = $alias;
			} else {
			    $row->alias = $helper->slugify($articletitle);
			}

            $row->introtext = mb_convert_encoding($introtext_format, 'UTF-8', mb_detect_encoding($introtext_format));
			$row->introtext = str_replace("\r\n", '<br />', $row->introtext);
			$row->introtext = str_replace("\n", '<br />', $row->introtext);
			if ($content_format) {
				$row->fulltext = mb_convert_encoding($content_format, 'UTF-8', mb_detect_encoding($content_format));
				$row->fulltext = str_replace("\r\n", '<br />', $row->fulltext);
				$row->fulltext = str_replace("\n", '<br />', $row->fulltext);
			}

			$published = $config['state'];

			if (isset($config['state_col'])) {
				$published_col = $config['state_col'];
			}

            if (!is_numeric($published)) {
				if ($helper->check_col($published_col)) {
					$col = $helper->chr2col($published_col);
					$val = $worksheet->getCellByColumnAndRow($col, $i)->getValue();
					if ($val == 1) {
						$row->state=1;
					} else {
						$row->state=0;
					}
				}
			} else if ($published == 0 || $published == 1) {
				$row->state = $published;
			} else {
				$row->state = 0;
			}

			$catid  = $config['catid'];
			$catcol = $config['catcol'];
			$parentcatcol = $config['parentcatcol'];

            if (version_compare(JVERSION, '3.0') < 0) {
			    $row->sectionid = 0;
            }

			if ($parentcatcol) {
				if (is_numeric($parentcatcol)) {
                    $query = $this->db->getQuery(true)
                        ->select('level')
                        ->from('#__categories')
                        ->where('id = "' . $parentcatcol . '"');
					$this->db->setQuery($query);
					$result = $this->db->loadResult();

					if ($result) {
						$parent = $parentcatcol;
						$level  = $result + 1;
					} else {
						$category = JTable::getInstance('Category');
						$category->title = $parent_name;
						$category->parent_id = 1;
						$category->level = 1;
						$category->extension = 'com_content';
						$category->published = $config['catstate'];
						$category->params = json_encode(array('category_layout' => '', 'image' => ''));
                        $category->language = '*';
                        $category->metadata = json_encode(array('author' => '', 'robots' => ''));
						$category->access = 1;

						$category->setLocation($category->parent_id, 'last-child');

						if (!$category->check()) {
                            $this->app->enqueueMessage($category->getError(), 'error');
							$this->app->redirect('index.php?option=com_focontentuploader');
						}

						if (!$category->store()) {
							$msg = $category->getError() . ' (Affected row: ' . $i . ')';
							$this->app->enqueueMessage($msg, 'error');
                            $this->app->redirect('index.php?option=com_focontentuploader');
						}

						if (!$category->rebuildPath($category->id)) {
                            $this->app->enqueueMessage($category->getError(), 'error');
                            $this->app->redirect('index.php?option=com_focontentuploader');
						}

						if (!$category->rebuild($category->id, $category->lft, $category->level, $category->path)) {
                            $this->app->enqueueMessage($category->getError(), 'error');
                            $this->app->redirect('index.php?option=com_focontentuploader');
						}

						$parent = $category->id;
						$level = $category->level + 1;
					}
				} else {
					if ($helper->check_col($parentcatcol)) {
						$col = $helper->chr2col($parentcatcol);
						$parent_col = $worksheet->getCellByColumnAndRow($col, $i)->getValue();
						if ($parent_col) {
                            $query = $this->db->getQuery(true)
                                ->select('id, level, title')
                                ->from('#__categories');
							if (is_numeric($parent_col)) {
                                $query->where('id = "' . $parent_col . '"');
							} else {
                                $query->where('title = "' . $this->db->escape($parent_col) . '" AND extension = "com_content"');
							}
							$this->db->setQuery($query);
							$result = $this->db->loadObject();

							if ($result) {
								$parent = $result->id;
								$level  = $result->level + 1;
							} else {
								$category = JTable::getInstance('Category');
								$category->title = $parent_col;
								$category->parent_id = 1;
								$category->level = 1;
								$category->extension = 'com_content';
								$category->published = $config['catstate'];
								$category->params = json_encode(array('category_layout' => '', 'image' => ''));
                                $category->language = '*';
                                $category->metadata = json_encode(array('author' => '', 'robots' => ''));
								$category->access = 1;

								$category->setLocation($category->parent_id, 'last-child');

								if (!$category->check()) {
                                    $this->app->enqueueMessage($category->getError(), 'error');
                                    $this->app->redirect('index.php?option=com_focontentuploader');
								}

								if (!$category->store()) {
									$msg = $category->getError() . ' (Affected row: ' . $i . ')';
									$this->app->enqueueMessage($msg, 'error');
                                    $this->app->redirect('index.php?option=com_focontentuploader');
								}

								if (!$category->rebuildPath($category->id)) {
									$this->app->enqueueMessage($category->getError(), 'error');
                                    $this->app->redirect('index.php?option=com_focontentuploader');
								}

								if (!$category->rebuild($category->id, $category->lft, $category->level, $category->path)) {
									$this->app->enqueueMessage($category->getError(), 'error');
                                    $this->app->redirect('index.php?option=com_focontentuploader');
								}

								$parent = $category->id;
								$level = $category->level + 1;
							}
						}
					}
				}
			} else {
				$parent = $level = 1;
			}

			if ($catid != -1) {
				$row->catid = $catid;
			} else {
				if ($helper->check_col($catcol)) {
					$col = $helper->chr2col($catcol);
					$category_name = $worksheet->getCellByColumnAndRow($col,$i)->getValue();

					if ($category_name) {
						if (is_numeric($category_name)) {
							$category_id = $category_name;
						} else {
                            $query = $this->db->getQuery(true)
                                ->select('id')
                                ->from('#__categories')
                                ->where('title= "' . $this->db->escape($category_name) . '" AND extension = "com_content"');
							$this->db->setQuery($query);
							$category_id = $this->db->loadResult();
						}

						if($category_id) {
							$row->catid = $category_id;
						} else {
							$category = JTable::getInstance('Category');
							$category->title = $category_name;
							$category->parent_id = $parent;
							$category->level = $level;
							$category->extension = 'com_content';
							$category->published = $config['catstate'];
							$category->params = json_encode(array('category_layout' => '', 'image' => ''));
							$category->language = '*';
							$category->metadata = json_encode(array('author' => '', 'robots' => ''));
							$category->access = 1;

							$category->setLocation($category->parent_id, 'last-child');

							if (!$category->check()) {
								$this->app->enqueueMessage($category->getError(), 'error');
                                $this->app->redirect('index.php?option=com_focontentuploader');
							}

							if (!$category->store()) {
								$msg = $category->getError() . ' (Affected row: ' . $i . ')';
								$this->app->enqueueMessage($msg, 'error');
                                $this->app->redirect('index.php?option=com_focontentuploader');
							}

							if (!$category->rebuildPath($category->id)) {
								$this->app->enqueueMessage($category->getError(), 'error');
                                $this->app->redirect('index.php?option=com_focontentuploader');
							}

							if (!$category->rebuild($category->id, $category->lft, $category->level, $category->path)) {
								$this->app->enqueueMessage($category->getError(), 'error');
                                $this->app->redirect('index.php?option=com_focontentuploader');
							}

							$row->catid = $category->id;
						}
					} else {
						$row->catid = 0;
					}
				}
			}

			if ($helper->check_col($config['articleid'])) {
				$col = $helper->chr2col($config['articleid']);
				$id  = $worksheet->getCellByColumnAndRow($col, $i)->getValue();
				if (is_numeric($id)) {
					$isNew = false;
					$row->id = $id;
				}
			} else {
				$isNew = true;
			}

			$table = JTable::getInstance('Content', 'JTable');
			if ($table->load(array('alias' => $row->alias, 'catid' => $row->catid)) && ($table->id != $row->id || $row->id == 0)) {
				$sql = 'SHOW TABLE STATUS LIKE "' . $this->db->getPrefix() . 'content"';
				$this->db->setQuery($sql);
				$tableStatus = $this->db->loadObject();
				$row->alias .= '-' . $tableStatus->Auto_increment;
			}

			if (isset($config['access_column'])) {
			    if ($helper->check_col($config['access_column'])) {
                    $col = $helper->chr2col($config['access_column']);
                    $level = $worksheet->getCellByColumnAndRow($col,$i)->getValue();
                    if (is_numeric($level)) {
                        $row->access = $level;
                    } else {
                        $level = $helper->getLevelByName($level);
                        if ($level) {
                            $row->access = $level;
                        } else {
                            $row->access = $config['access'];
                        }
                    }
			    } else {
    				$row->access = $config['access'];
			    }
			} else {
			    $row->access = $config['access'];
			}

			$featured = $config['featured'];
			if (isset($config['featured_col'])) {
				$featured_col = $config['featured_col'];
			}
			if (!is_numeric($featured)) {
				if ($helper->check_col($featured_col)) {
					$col = $helper->chr2col($featured_col);
					$row->featured = $worksheet->getCellByColumnAndRow($col,$i)->getValue();
				}
			} else {
				$row->featured = $featured;
			}

			$helper->saveContentPrep($row);

            if (!$row->check()) {
                $this->app->enqueueMessage($row->getError(), 'error');
                $this->app->redirect('index.php?option=com_focontentuploader');
			}

			$result = $dispatcher->trigger('onBeforeContentSave', array(&$row, $isNew));

			if (in_array(false, $result, true)) {
				$this->app->enqueueMessage($row->getError(), 'error');
                $this->app->redirect('index.php?option=com_focontentuploader');
			}

			if (!$row->store()) {
				$msg = $row->getError() . ' (Affected row: ' . $i . ')';
                $this->app->enqueueMessage($msg, 'error');
                $this->app->redirect('index.php?option=com_focontentuploader');
			} else {
				$row->introtext = str_ireplace('{articleid}', $row->id, $row->introtext);
				$row->fulltext = str_ireplace('{articleid}', $row->id, $row->fulltext);
				$row->store();
			}

			$row->checkin();

            if ($row->featured) {
                $query = $this->db->getQuery(true)
                    ->insert('#__content_frontpage')
                    ->set('content_id = ' . $row->id);
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

        // Flush cache to include new content
        $cache = JFactory::getCache('com_content');
		$cache->clean();

		$msg = JText::sprintf('CU_ITEMSUPLOADED', $i - $first_row);
		$this->app->enqueueMessage($msg);
        $this->app->redirect('index.php?option=com_focontentuploader');
    }

    public function save () {
        $helper = new foContentUploaderHelper();
        $post = JRequest::get('post', JREQUEST_ALLOWRAW);

        foreach ($post as $k => $v) {
            $config[$k] = $v;
        }

        unset($config['global']);

        foreach ($config as $k => $v) {
            if (is_array($v) && $k != 'tags') {
                foreach ($v as $kk => $vv) {
                    $config[$kk] = $vv;
                }
                unset($config[$k]);
            }
        }

        $config['configid'] = 1;
        $config['configname'] = $this->db->escape($post['configname']);

        $pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';
        if (preg_match($pattern, $config['fulltext'])) {
            list($config['introtext'], $config['fulltext']) = preg_split($pattern, $config['fulltext'], 2);
        } else {
            $config['introtext'] = $config['fulltext'];
            unset($config['fulltext']);
        }

        $c = chr(9).chr(13).chr(10);
        $config['introtext']   = str_replace('\n', '', $config['introtext'] ) ;
        $config['introtext']   = str_replace('\r\n', '', $config['introtext'] ) ;
        $config['introtext']   = str_replace($c, '', $config['introtext']);
        $config['introtext']   = preg_replace('/[\x0A|\x0D]/', '', $config['introtext']);
        $config['introtext']   = str_replace('=', '&#61;', $config['introtext']);
        $config['introtext']   = nl2br($config['introtext']);
        $config['introtext']   = addslashes($config['introtext']);

        if (isset($config['fulltext'])) {
            $config['fulltext']    = str_replace('\n', '', $config['fulltext'] ) ;
            $config['fulltext']    = str_replace('\r\n', '', $config['fulltext'] ) ;
            $config['fulltext']    = str_replace($c, '', $config['fulltext']);
            $config['fulltext']    = preg_replace('/[\x0A|\x0D]/', '', $config['fulltext']);
            $config['fulltext']    = str_replace('=', '&#61;', $config['fulltext']);
            $config['fulltext']    = nl2br($config['fulltext']);
            $config['fulltext']    = addslashes($config['fulltext']);
        }

        $params_to_save = $helper->params_to_save($config);

        $query = $this->db->getQuery(true)
            ->update('#__focontentuploader')
            ->set('params = ' . $this->db->quote($params_to_save))
            ->set('name = "'. $config['configname'] . '"')
            ->set('active = 1')
            ->where('id = 1');
        $this->db->setQuery($query);

        try {
            $this->db->query();
            $msgType = 'message';
            $msg = JText::_('CU_SETTINGS_SAVED');
        } catch (Exception $e) {
            $msgType = 'error';
            $msg = 'Datbase Error: ' . str_replace("\n", '<br />', $e->getMessage());
        }

       	$this->app->enqueueMessage($msg, $msgType);
        $this->app->redirect('index.php?option=com_focontentuploader');
    }
}
