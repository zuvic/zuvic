DROP TABLE IF EXISTS project_related;

CREATE TABLE `project_related` (
  `project_related_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `project_related_site_id` int(10) unsigned NOT NULL,
  `project_related_survey` int(2) DEFAULT NULL,
  `project_related_planning` int(2) DEFAULT NULL,
  `project_related_civil` int(2) DEFAULT NULL,
  `project_related_transport` int(2) DEFAULT NULL,
  `project_related_structural` int(2) DEFAULT NULL,
  `project_related_bridges` int(2) DEFAULT NULL,
  `project_related_utility` int(2) DEFAULT NULL,
  `project_related_water` int(2) DEFAULT NULL,
  `project_related_const` int(2) DEFAULT NULL,
  `project_related_perm` int(2) DEFAULT NULL,
  `project_related_enviro` int(2) DEFAULT NULL,
  PRIMARY KEY (`project_related_id`,`project_related_site_id`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8;
CREATE TABLE project_site ( project_site_id INT(10) unsigned not null auto_increment, project_site_name varchar(50) not null, primary key (project_site_id) );
CREATE TABLE project_content ( project_content_id INT(10) unsigned not null, project_content_site_id INT(10) unsigned not null, project_content_type varchar(20) not null, project_content_value mediumtext not null, primary key (project_content_id) );
