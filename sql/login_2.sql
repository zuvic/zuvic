ALTER TABLE login MODIFY COLUMN login_pass char(60) DEFAULT NULL;
ALTER TABLE login ADD COLUMN login_token char(60) DEFAULT NULL;
ALTER TABLE login ADD COLUMN login_token_expiry char(60) DEFAULT NULL;