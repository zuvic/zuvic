ALTER TABLE project_related DROP COLUMN project_related_enviro;
ALTER TABLE project_related ADD COLUMN project_related_esa int(2) DEFAULT NULL;
ALTER TABLE project_related ADD COLUMN project_related_sgc int(2) DEFAULT NULL;
ALTER TABLE project_related ADD COLUMN project_related_rap int(2) DEFAULT NULL;
ALTER TABLE project_related ADD COLUMN project_related_design_remid int(2) DEFAULT NULL;
ALTER TABLE project_related ADD COLUMN project_related_hazmat int(2) DEFAULT NULL;
ALTER TABLE project_related ADD COLUMN project_related_exp_test int(2) DEFAULT NULL;
ALTER TABLE project_related ADD COLUMN project_related_ast_ust int(2) DEFAULT NULL;