DROP TABLE IF EXISTS services_content;

CREATE TABLE services_content ( services_content_id INT(10) unsigned not null auto_increment, services_content_type varchar(50) not null, services_content_page varchar(50) not null, services_content_value mediumtext not null, primary key (services_content_id) );