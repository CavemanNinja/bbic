ALTER TABLE `#__twojtoolbox_plugins` ADD `daemon` int(11) NULL  DEFAULT '0' AFTER `multitag`;
ALTER TABLE `#__twojtoolbox` ADD `itemid` int(11) NULL  DEFAULT '0' AFTER `state`;
CREATE TABLE IF NOT EXISTS `#__twojtoolbox_menu` ( `id` int(11) NOT NULL,   `itemid` int(11) NOT NULL,   PRIMARY KEY (`id`,`itemid`) ) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;