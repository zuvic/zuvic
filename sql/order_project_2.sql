ALTER TABLE order_project DROP PRIMARY KEY, ADD PRIMARY KEY(order_project_id, order_project_site_id, order_project_idx, order_project_type);
ALTER TABLE order_project DROP COLUMN order_project_survey, DROP COLUMN order_project_planning, DROP COLUMN order_project_civil, DROP COLUMN order_project_transport, DROP COLUMN order_project_structural, DROP COLUMN order_project_bridges, DROP COLUMN order_project_utility, DROP COLUMN order_project_water, DROP COLUMN order_project_const, DROP COLUMN order_project_perm, DROP COLUMN order_project_esa, DROP COLUMN order_project_sgc, DROP COLUMN order_project_rap, DROP COLUMN order_project_design_remid, DROP COLUMN order_project_hazmat, DROP COLUMN order_project_exp_test, DROP COLUMN order_project_ast_ust;
ALTER TABLE order_project ADD COLUMN order_project_key varchar(10) not null;