<?php
/**
 * Copyright (C) 2014  freakedout (www.freakedout.de)
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

class com_focontentuploaderInstallerScript {

    protected $app;
    protected $db;

    public function __construct() {
        $this->app = JFactory::getApplication();
        $this->db = JFactory::getDbo();
    }

    private $obsoleteFolders = array(
        'administrator/compontents/com_focontentuploader',
        'compontents/com_focontentuploader',
        'media/com_focontentuploader'
    );

    public function preflight($type, $parent) {
        if (!version_compare(JVERSION, '2.5.6', 'ge')) {
            $msg = "<p>You need Joomla! 2.5.6 or later to install this component</p>";
            JError::raiseWarning(100, $msg);
            return false;
        }

        // Workarounds for JInstaller bugs
        if (in_array($type, array('install', 'discover_install'))) {
            //$this->bugfixDBFunctionReturnedNoError();

            $query = 'SHOW TABLES LIKE "' . $this->db->getPrefix() . 'focontentuploader";';
            $this->db->setQuery($query);
            $tableExists = $this->db->loadResult();

            if (!$tableExists) {
                $queries = array(
                    "CREATE TABLE IF NOT EXISTS #__focontentuploader (`id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(255) NOT NULL, `params` text NOT NULL, `active` int(1) NOT NULL, `component` VARCHAR(10) NOT NULL, PRIMARY KEY (`id`));",
                    'INSERT INTO `#__focontentuploader` (`id`, `name`, `params`, `active`, `component`) VALUES
                        (1, \'Sailing Yachts\', \'state=1\ncatid=-1\nfeatured=0\nremovetags=0\ntags=G|L\ncatstate=1\nimage_intro=M\nfloat_intro=right\nimage_intro_alt=O\nimage_intro_caption=Q\nimage_fulltext=N\nfloat_fulltext=left\nimage_fulltext_alt=P\nimage_fulltext_caption=R\nurla=S\nurlatext=T\ntargeta=\nurlb=U\nurlbtext=V\ntargetb=1\nurlc=W\nurlctext=X\ntargetc=2\ncreated_by=0\ncreated_by_alias=\naccess=5\naccess_column=\ncreated=\npublish_up=\npublish_down=\nshow_title=\nlink_titles=\nshow_tags=\nshow_intro=\ninfo_block_position=\nshow_category=\nlink_category=\nshow_parent_category=\nlink_parent_category=\nshow_author=\nlink_author=\nshow_create_date=\nshow_modify_date=\nshow_publish_date=\nshow_item_navigation=\nshow_icons=\nshow_print_icon=\nshow_email_icon=\nshow_vote=\nshow_hits=\nshow_noauth=\nurls_position=\nalternative_readmore=\narticle_layout=\nlanguage=*\nlanguage_column=\ndescription={A}, {B}, {C}\nkeywords={A}, {B}, {C}\nxreference=\nrobots_select=index, follow\nrobots=\nauthor=\nrights=\ncsvDelimiter=,\ncsvTextQualifier="\ndateFormat=d/m/Y H:i:s\ndecimalDigits=2\ndecimalSeparator=.\nthousandsSeparator=,\ndecimalColumns=\nconfigname=Sailing Yachts\nconfigid=1\narticleid=\nfirstdatarow=2\ntitle=A\nalias=\nmax_articles=50\ncatcol=B\nparentcatcol=C\nfulltext=<table><tbody><tr><td>Boat</td><td>{B}</td></tr><tr><td>Length</td><td>{D}</td></tr><tr><td>Year</td><td>{E}</td></tr><tr><td>Hull material</td><td>{F}</td></tr><tr><td>Engine type</td><td>{G}</td></tr><tr><td>Location</td><td>{J}</td></tr><tr><td>Price</td><td>{I} {H}</td></tr></tbody></table>\nintrotext=<p><img title&#61;\\"\\" src&#61;\\"{K}\\" alt&#61;\\"\\" /></p>\', 1, \'content\')'
                );

                foreach ($queries as $query) {
                    $this->db->setQuery($query);
                    try {
                        $this->db->query();
                    } catch (Exception $e) {
                        $msg = 'Database error: ' . $e->getMessage();
                        $this->app->enqueueMessage($msg, 'error');
                        $this->app->redirect('index.php');
                    }
                }
            } else {
                // fix configurations with blank config id
                $query = "SELECT id, params FROM `#__focontentuploader`;";
                $this->db->setQuery($query);
                $configs = $this->db->loadObjectList();

                foreach ($configs as $config) {
                    $config->params = str_replace("configid=\n", "configid={$config->id}\n", $config->params);
                    $query = 'UPDATE #__focontentuploader SET params = "' . $config->params . '" WHERE id = ' . $config->id;
                    $this->db->setQuery($query);
                    $this->db->query();
                }
            }
        } else {
            $this->bugfixCantBuildAdminMenus();
            $this->fixSchemaVersion();
        }

        $this->deleteObsoleteFolders();
    }

    public function postflight(){}

    public function uninstall() {
        $query = 'DROP TABLE IF EXISTS #__focontentuploader';
        $this->db->setQuery($query);
        if (!$this->db->query()) {
            echo $this->db->getErrorMsg();
            return false;
        } else {
            echo 'You have successfully uninstalled the Content Uploader.';
        }
    }

    /**
     * Joomla! 1.6+ bugfix for "DB function returned no error"
     */
    private function bugfixDBFunctionReturnedNoError() {
        // Fix broken #__assets records
        $query = $this->db->getQuery(true);
        $query->select('id')
            ->from('#__assets')
            ->where($this->db->qn('name') . ' = ' . $this->db->q('com_focontentuploader'));
        $this->db->setQuery($query);
        if (version_compare(JVERSION, '3.0', 'ge')) {
            $ids = $this->db->loadColumn();
        } else {
            $ids = $this->db->loadResultArray();
        }
        if (!empty($ids)) {
            foreach($ids as $id) {
                $query = $this->db->getQuery(true);
                $query->delete('#__assets')
                    ->where($this->db->qn('id') . ' = ' . $this->db->q($id));
                $this->db->setQuery($query);
                $this->db->execute();
            }
        }

        // Fix broken #__extensions records
        $query = $this->db->getQuery(true);
        $query->select('extension_id')
            ->from('#__extensions')
            ->where($this->db->qn('element') . ' = ' . $this->db->q('com_focontentuploader'));
        $this->db->setQuery($query);
        if (version_compare(JVERSION, '3.0', 'ge')) {
            $ids = $this->db->loadColumn();
        } else {
            $ids = $this->db->loadResultArray();
        }
        if (!empty($ids)) {
            foreach($ids as $id) {
                $query = $this->db->getQuery(true);
                $query->delete('#__extensions')
                    ->where($this->db->qn('extension_id') . ' = ' . $this->db->q($id));
                $this->db->setQuery($query);
                $this->db->execute();
            }
        }

        // Fix broken #__menu records
        $query = $this->db->getQuery(true);
        $query->select('id')
            ->from('#__menu')
            ->where($this->db->qn('type') . ' = ' . $this->db->q('component'))
            ->where($this->db->qn('menutype') . ' = ' . $this->db->q('main'))
            ->where($this->db->qn('link') . ' LIKE ' . $this->db->q('index.php?option=com_focontentuploader'));
        $this->db->setQuery($query);

        if (version_compare(JVERSION, '3.0', 'ge')) {
            $ids = $this->db->loadColumn();
        } else {
            $ids = $this->db->loadResultArray();
        }
        if (!empty($ids)) {
            foreach($ids as $id) {
                $query = $this->db->getQuery(true);
                $query->delete('#__menu')
                    ->where($this->db->qn('id').' = '.$this->db->q($id));
                $this->db->setQuery($query);
                $this->db->execute();
            }
        }
    }

    /**
     * Joomla! 1.6+ bugfix for "Can not build admin menus"
     */
    private function bugfixCantBuildAdminMenus() {
        // If there are multiple #__extensions record, keep one of them
        $query = $this->db->getQuery(true);
        $query->select('extension_id')
            ->from('#__extensions')
            ->where($this->db->qn('element') . ' = ' . $this->db->q('com_focontentuploader'));
        $this->db->setQuery($query);
        if (version_compare(JVERSION, '3.0', 'ge')) {
            $ids = $this->db->loadColumn();
        } else {
            $ids = $this->db->loadResultArray();
        }
        if (count($ids) > 1) {
            asort($ids);
            $extension_id = array_shift($ids); // Keep the oldest id

            foreach ($ids as $id) {
                $query = $this->db->getQuery(true);
                $query->delete('#__extensions')
                    ->where($this->db->qn('extension_id') . ' = ' . $this->db->q($id));
                $this->db->setQuery($query);
                $this->db->execute();
            }
        }

        // If there are multiple assets records, delete all except the oldest one
        $query = $this->db->getQuery(true);
        $query->select('id')
            ->from('#__assets')
            ->where($this->db->qn('name') . ' = ' . $this->db->q('com_focontentuploader'));
        $this->db->setQuery($query);
        $ids = $this->db->loadObjectList();
        if (count($ids) > 1) {
            asort($ids);
            $asset_id = array_shift($ids); // Keep the oldest id

            foreach ($ids as $id) {
                $query = $this->db->getQuery(true);
                $query->delete('#__assets')
                    ->where($this->db->qn('id') . ' = ' . $this->db->q($id));
                $this->db->setQuery($query);
                $this->db->execute();
            }
        }

        // Remove #__menu records for good measure!
        $query = $this->db->getQuery(true);
        $query->select('id')
            ->from('#__menu')
            ->where($this->db->qn('type') . ' = ' . $this->db->q('component'))
            ->where($this->db->qn('menutype') . ' = ' . $this->db->q('main'))
            ->where($this->db->qn('link') . ' LIKE ' . $this->db->q('index.php?option=com_focontentuploader'));
        $this->db->setQuery($query);
        if (version_compare(JVERSION, '3.0', 'ge')) {
            $ids1 = $this->db->loadColumn();
        } else {
            $ids1 = $this->db->loadResultArray();
        }
        if (empty($ids1)) {
            $ids1 = array();
        }
        $query = $this->db->getQuery(true);
        $query->select('id')
            ->from('#__menu')
            ->where($this->db->qn('type') . ' = ' . $this->db->q('component'))
            ->where($this->db->qn('menutype') . ' = ' . $this->db->q('main'))
            ->where($this->db->qn('link') . ' LIKE ' . $this->db->q('index.php?option=com_focontentuploader&%'));
        $this->db->setQuery($query);
        if (version_compare(JVERSION, '3.0', 'ge')) {
            $ids2 = $this->db->loadColumn();
        } else {
            $ids2 = $this->db->loadResultArray();
        }
        if (empty($ids2)) {
            $ids2 = array();
        }
        $ids = array_merge($ids1, $ids2);
        if (!empty($ids)) {
            foreach($ids as $id) {
                $query = $this->db->getQuery(true);
                $query->delete('#__menu')
                    ->where($this->db->qn('id') . ' = ' . $this->db->q($id));
                $this->db->setQuery($query);
                $this->db->execute();
            }
        }
    }

    /**
     * When you are upgrading from an old version of the component or when your
     * site is upgraded from Joomla! 1.5 there is no "schema version" for our
     * component's tables. As a result Joomla! doesn't run the database queries
     * and you get a broken installation.
     *
     * This method detects this situation, forces a fake schema version "0.0.1"
     * and lets the crufty mess Joomla!'s extensions installer is to bloody work
     * as anyone would have expected it to do!
     */
    private function fixSchemaVersion() {
        $query = $this->db->getQuery(true);
        $query->select('extension_id')
            ->from('#__extensions')
            ->where($this->db->qn('element').' = '.$this->db->q('com_focontentuploader'));
        $this->db->setQuery($query);
        $eid = $this->db->loadResult();

        if (!$eid) {
            return;
        }

        $query = $this->db->getQuery(true);
        $query->select('version_id')
            ->from('#__schemas')
            ->where('extension_id = ' . $eid);
        $this->db->setQuery($query);
        $version = $this->db->loadResult();

        if (!$version) {
            // No schema version found. Fix it.
            $o = (object)array(
                'version_id'   => '0.0.1-2007-08-15',
                'extension_id' => $eid,
            );
            $this->db->insertObject('#__schemas', $o);
        }
    }

    private function deleteObsoleteFolders() {
        foreach ($this->obsoleteFolders as $folder) {
            $f = JPATH_ROOT . '/' . $folder;
            if (JFolder::exists($f)) {
                JFolder::delete($f);
            }
        }
    }
}