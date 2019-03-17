DROP TABLE IF EXISTS related_cats;

CREATE TABLE related_cats ( related_cats_id INT(10) unsigned not null auto_increment, related_cats_name varchar(50) not null, primary key (related_cats_id, related_cats_name) );