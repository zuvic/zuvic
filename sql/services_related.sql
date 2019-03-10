CREATE TABLE `services_related` (
  `services_related_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `services_related_site_id` int(10) unsigned NOT NULL,
  `services_related_survey` int(2) DEFAULT 0,
  `services_related_planning` int(2) DEFAULT 0,
  `services_related_civil` int(2) DEFAULT 0,
  `services_related_transport` int(2) DEFAULT 0,
  `services_related_structural` int(2) DEFAULT 0,
  `services_related_bridges` int(2) DEFAULT 0,
  `services_related_utility` int(2) DEFAULT 0,
  `services_related_water` int(2) DEFAULT 0,
  `services_related_const` int(2) DEFAULT 0,
  `services_related_perm` int(2) DEFAULT 0,
  `services_related_esa` int(2) DEFAULT 0,
  `services_related_sgc` int(2) DEFAULT 0,
  `services_related_rap` int(2) DEFAULT 0,
  `services_related_design_remid` int(2) DEFAULT 0,
  `services_related_hazmat` int(2) DEFAULT 0,
  `services_related_exp_test` int(2) DEFAULT 0,
  `services_related_ast_ust` int(2) DEFAULT 0,
  PRIMARY KEY (`services_related_id`,`services_related_site_id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;