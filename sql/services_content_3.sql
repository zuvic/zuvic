ALTER TABLE services_content DROP PRIMARY KEY, ADD PRIMARY KEY(services_content_id, services_content_type, services_content_idx);
ALTER TABLE services_content DROP COLUMN services_content_page;
ALTER TABLE services_content ADD COLUMN services_content_site_id int(10) not null;
ALTER TABLE services_content DROP PRIMARY KEY, ADD PRIMARY KEY(services_content_id, services_content_site_id, services_content_type, services_content_idx);