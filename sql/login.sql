DROP TABLE IF EXISTS login;

CREATE TABLE login ( login_id INT(10) unsigned not null auto_increment, login_user char(20) not null, login_first char(20) not null, login_last char(20) not null, login_pass char(60) not null, login_email varchar(320) not null, login_type char(20) not null, primary key (login_id, login_user) );