<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2026-10-12 01:53:20 --> 404 Page Not Found: Uploads/profile
ERROR - 2026-10-12 01:53:25 --> 404 Page Not Found: Uploads/profile
ERROR - 2026-10-12 01:53:56 --> 404 Page Not Found: Uploads/profile
ERROR - 2026-10-12 08:05:02 --> Query error: Table 'depedddomis_db.users' doesn't exist - Invalid query: 
            insert ignore into hris_field_encoder_access (encoder_user_id, job_id, encode_mode, created_by)
            select u.id, sv.job_id, 'both', sv.secretariat_user_id
            from users u
            join hris_secretariat_vacancies sv on sv.secretariat_user_id = CAST(u.user_id AS UNSIGNED)
            join hris_jobvacancy j on j.jobID = sv.job_id
            where u.position = 'Field Encoder'
              and j.jvStatus != 'Closed'
              and j.position not in (1, 5)
        
ERROR - 2026-10-12 08:05:04 --> Query error: Table 'depedddomis_db.sgod_school_allocation' doesn't exist - Invalid query: alter table `sgod_school_allocation` add column `alloc_program` varchar(45) NOT NULL DEFAULT ''
ERROR - 2026-10-12 08:05:04 --> Query error: Table 'depedddomis_db.sgod_school_allocation' doesn't exist - Invalid query: UPDATE sgod_school_allocation
			SET alloc_amount = CAST(REPLACE(REPLACE(TRIM(alloc_amount), ',', ''), ' ', '') AS DECIMAL(15,2))
			WHERE alloc_amount REGEXP '[, ]' AND REPLACE(REPLACE(TRIM(alloc_amount), ',', ''), ' ', '') REGEXP '^[0-9]+(\\.[0-9]+)?$'
ERROR - 2026-10-12 08:05:09 --> Query error: Table 'depedddomis_db.sgod_school_allocation' doesn't exist - Invalid query: alter table `sgod_school_allocation` add column `alloc_program` varchar(45) NOT NULL DEFAULT ''
ERROR - 2026-10-12 08:05:09 --> Query error: Table 'depedddomis_db.sgod_school_allocation' doesn't exist - Invalid query: UPDATE sgod_school_allocation
			SET alloc_amount = CAST(REPLACE(REPLACE(TRIM(alloc_amount), ',', ''), ' ', '') AS DECIMAL(15,2))
			WHERE alloc_amount REGEXP '[, ]' AND REPLACE(REPLACE(TRIM(alloc_amount), ',', ''), ' ', '') REGEXP '^[0-9]+(\\.[0-9]+)?$'
ERROR - 2026-10-12 08:05:10 --> Query error: Table 'depedddomis_db.hris_staff' doesn't exist - Invalid query: SELECT count(IDNumber) as Counts FROM hris_staff where schoolID='128440' and currentStatus='Active'
ERROR - 2026-10-12 08:05:11 --> Query error: Table 'depedddomis_db.hris_rater_assignments' doesn't exist - Invalid query: SHOW COLUMNS FROM `hris_rater_assignments`
ERROR - 2026-10-12 08:05:11 --> Query error: Table 'depedddomis_db.hris_rater_assignments' doesn't exist - Invalid query: SHOW COLUMNS FROM `hris_rater_assignments`
ERROR - 2026-10-12 08:05:15 --> Query error: Table 'depedddomis_db.hris_rater_assignments' doesn't exist - Invalid query: SHOW COLUMNS FROM `hris_rater_assignments`
ERROR - 2026-10-12 08:05:40 --> Query error: Table 'depedddomis_db.hris_rater_assignments' doesn't exist - Invalid query: SHOW COLUMNS FROM `hris_rater_assignments`
ERROR - 2026-10-12 02:17:02 --> 404 Page Not Found: Uploads/profile
ERROR - 2026-10-12 02:17:10 --> 404 Page Not Found: Uploads/profile
ERROR - 2026-10-12 02:17:13 --> 404 Page Not Found: Uploads/profile
