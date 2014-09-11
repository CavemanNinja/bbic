ALTER TABLE `#__twojtoolbox_data` 		ADD `plugintype` 	varchar(255) 	NOT NULL AFTER `json`;
ALTER TABLE `#__twojtoolbox_elements` 	ADD `width` 		smallint(6) 	NOT NULL AFTER `img`;
ALTER TABLE `#__twojtoolbox_elements` 	ADD `height` 		smallint(6) 	NOT NULL AFTER `img`;