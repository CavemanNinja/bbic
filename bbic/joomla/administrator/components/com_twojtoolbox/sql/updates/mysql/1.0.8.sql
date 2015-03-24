CREATE TABLE IF NOT EXISTS `#__twojtoolbox_news` ( 
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_news` int(11) NOT NULL,
  `date_in` int(11) NOT NULL,
  `message` text NOT NULL,
  `read` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;