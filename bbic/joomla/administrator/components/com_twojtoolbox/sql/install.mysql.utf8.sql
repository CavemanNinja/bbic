CREATE TABLE IF NOT EXISTS `#__twojtoolbox` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `type` varchar(20) NOT NULL,
  `params` text NOT NULL DEFAULT '',
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL,
  `state` tinyint(4) NOT NULL,
  `itemid` int(11) NOT NULL DEFAULT '0',
  `cacheid` int(11) NOT NULL DEFAULT '0',
   PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__twojtoolbox_config` (
  `id` int(11) NOT NULL,
  `update` bigint(20) NOT NULL,
  `t` int(11) NOT NULL,
  `version` int(11) NOT NULL,
  `version_available` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__twojtoolbox_elements`(
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `catid` int(11) NOT NULL,
  `params` text NOT NULL DEFAULT '',
  `img` varchar(255) NOT NULL DEFAULT '',
  `width` smallint(6) NOT NULL,
  `height` smallint(6) NOT NULL,
  `desc` text NOT NULL DEFAULT '',
  `language` varchar(7) NOT NULL,
  `ordering` int(11) NOT NULL,
  `state` tinyint(4) NOT NULL,
  `link` text NOT NULL DEFAULT '',
  `link_blank` tinyint(4) NOT NULL,
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__twojtoolbox_plugins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `type` varchar(20) NOT NULL,
  `desc` text NOT NULL,
  `install` int(1) NOT NULL,
  `v_install` int(11) NOT NULL,
  `v_server` int(11) NOT NULL,
  `v_active` varchar(255) NOT NULL,
  `adddate` bigint(20) NOT NULL,
  `multi` int(1) NOT NULL,
  `images` int(1) NOT NULL,
  `multitag` int(11) NOT NULL,
  `daemon` int(11) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL,
  `desc_small` varchar(255) NOT NULL,
  `price` varchar(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__twojtoolbox_menu` (
  `id` int(11) NOT NULL,
  `itemid` int(11) NOT NULL,
  PRIMARY KEY (`id`,`itemid`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__twojtoolbox_news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_news` int(11) NOT NULL,
  `date_in` int(11) NOT NULL,
  `message` text NOT NULL,
  `read` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__twojtoolbox_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pluginid` int(11) NOT NULL,
  `json` mediumtext NOT NULL,
  `plugintype` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;