DROP TABLE IF EXISTS project_related;

CREATE TABLE project_site ( project_site_id INT(10) unsigned not null auto_increment, project_site_name varchar(50) not null, primary key (project_site_id) );
CREATE TABLE project_content ( project_content_id INT(10) unsigned not null, project_content_site_id INT(10) unsigned not null, project_content_type varchar(20) not null, project_content_value mediumtext not null, primary key (project_content_id) );
