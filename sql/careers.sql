DROP TABLE IF EXISTS careers;

CREATE TABLE `careers` (
  `careers_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `careers_title` varchar(50) DEFAULT NULL,
  `careers_location` varchar(50) DEFAULT NULL,
  `careers_duration` int(2) DEFAULT NULL,
  `careers_active` boolean DEFAULT NULL,
  `careers_description` mediumtext DEFAULT NULL,
  `careers_desc_extra` mediumtext DEFAULT NULL,
  `careers_order` int(2) DEFAULT NULL,
  PRIMARY KEY (`careers_id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
