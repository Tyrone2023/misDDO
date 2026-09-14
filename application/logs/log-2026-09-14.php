<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2026-09-14 05:05:09 --> 404 Page Not Found: Uploads/profile
ERROR - 2026-09-14 05:05:15 --> 404 Page Not Found: Uploads/profile
ERROR - 2026-09-14 05:08:22 --> 404 Page Not Found: Uploads/profile
ERROR - 2026-09-14 05:08:32 --> 404 Page Not Found: Uploads/profile
ERROR - 2026-09-14 11:28:24 --> Severity: error --> Exception: Unable to locate the model you have specified: Login_model /Applications/XAMPP/xamppfiles/htdocs/misDDO/system/core/Loader.php 350
ERROR - 2026-09-14 05:29:47 --> 404 Page Not Found: Uploads/profile
ERROR - 2026-09-14 05:29:52 --> 404 Page Not Found: Uploads/profile
ERROR - 2026-09-14 11:31:08 --> Query error: Illegal mix of collations (utf8mb3_unicode_ci,IMPLICIT) and (utf8mb3_general_ci,IMPLICIT) for operation '=' - Invalid query: SELECT r.id, r.fy, r.b_code, r.school_id, r.tdate, r.ttime, r.remarks, r.stat, r.s_id, r.deny_remarks, r.deny_by, r.deny_date, r.deny_time, sc.schoolName, sc.district, sc.course, alloc.alloc_group, alloc.alloc_amount, alloc.alloc_type, sub.id AS submit_id, sub.status AS submit_status, sub.remarks AS submit_remarks, sub.date AS submit_date, (SELECT MIN(app.id) FROM sgod_app_percentage app
				WHERE app.b_code = r.b_code AND app.fy = '2026') AS app_id
FROM `sgod_aip_request` `r`
LEFT JOIN schools sc ON sc.schoolID = CAST(r.school_id AS CHAR)
LEFT JOIN sgod_school_allocation alloc ON alloc.schoolID = CAST(r.school_id AS CHAR)
				AND alloc.alloc_batch = CAST(r.b_code AS CHAR)
LEFT JOIN `sgod_aip_submit` `sub` ON `sub`.`id` = `r`.`s_id`
WHERE `r`.`fy` = '2026'
AND `r`.`stat` = 0
ORDER BY `r`.`id` DESC
ERROR - 2026-09-14 11:41:02 --> Severity: error --> Exception: Unable to locate the model you have specified: Login_model /Applications/XAMPP/xamppfiles/htdocs/misDDO/system/core/Loader.php 350
ERROR - 2026-09-14 11:41:39 --> Severity: error --> Exception: Unable to locate the model you have specified: Login_model /Applications/XAMPP/xamppfiles/htdocs/misDDO/system/core/Loader.php 350
ERROR - 2026-09-14 05:50:55 --> 404 Page Not Found: Uploads/profile
ERROR - 2026-09-14 05:50:59 --> 404 Page Not Found: Uploads/profile
ERROR - 2026-09-14 05:52:01 --> 404 Page Not Found: Uploads/profile
ERROR - 2026-09-14 05:52:17 --> 404 Page Not Found: Uploads/profile
ERROR - 2026-09-14 11:53:41 --> Severity: Warning --> Attempt to read property "alloc_group" on null /Applications/XAMPP/xamppfiles/htdocs/misDDO/application/views/aip_action_view_sgod_chief.php 87
ERROR - 2026-09-14 05:55:03 --> 404 Page Not Found: Uploads/profile
ERROR - 2026-09-14 05:55:08 --> 404 Page Not Found: Uploads/profile
ERROR - 2026-09-14 11:56:16 --> Severity: error --> Exception: Unable to locate the model you have specified: Login_model /Applications/XAMPP/xamppfiles/htdocs/misDDO/system/core/Loader.php 350
ERROR - 2026-09-14 05:58:16 --> 404 Page Not Found: Uploads/profile
ERROR - 2026-09-14 05:58:25 --> 404 Page Not Found: Uploads/profile
ERROR - 2026-09-14 05:58:27 --> 404 Page Not Found: Uploads/profile
ERROR - 2026-09-14 12:04:37 --> Severity: error --> Exception: Unable to locate the model you have specified: Login_model /Applications/XAMPP/xamppfiles/htdocs/misDDO/system/core/Loader.php 350
ERROR - 2026-09-14 12:04:38 --> Severity: error --> Exception: Unable to locate the model you have specified: Login_model /Applications/XAMPP/xamppfiles/htdocs/misDDO/system/core/Loader.php 350
ERROR - 2026-09-14 12:11:39 --> Severity: error --> Exception: Unable to locate the model you have specified: Login_model /Applications/XAMPP/xamppfiles/htdocs/misDDO/system/core/Loader.php 350
ERROR - 2026-09-14 12:11:40 --> Severity: error --> Exception: Unable to locate the model you have specified: Login_model /Applications/XAMPP/xamppfiles/htdocs/misDDO/system/core/Loader.php 350
ERROR - 2026-09-14 12:33:50 --> Severity: error --> Exception: Unable to locate the model you have specified: Login_model /Applications/XAMPP/xamppfiles/htdocs/misDDO/system/core/Loader.php 350
ERROR - 2026-09-14 12:33:51 --> Severity: error --> Exception: Unable to locate the model you have specified: Login_model /Applications/XAMPP/xamppfiles/htdocs/misDDO/system/core/Loader.php 350
