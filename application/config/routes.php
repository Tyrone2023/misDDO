<?php
defined('BASEPATH') OR exit('No direct script access allowed');


$route['default_controller'] = 'pages/view';

$route['personnel'] = 'pages/personnel';
$route['dept_summary'] = 'pages/dept_summary';
$route['personel_add'] = 'pages/personel_add';
$route['personnel_profile/(:any)'] = 'pages/personnel_profile/$1';
$route['twoOonefiles'] = 'pages/twoOonefiles';

$route['plantilla'] = 'pages/plantilla';
$route['plantilla_add'] = 'pages/plantilla_add';
$route['plantilla_update/(:any)'] = 'pages/plantilla_update/$1';
$route['plantilla_del/(:any)'] = 'pages/plantilla_del/$1';

$route['items/(:any)'] = 'pages/items/$1';
$route['position'] = 'pages/position';
$route['requirment'] = 'pages/requirment';
$route['view_status_unview/(:any)'] = 'pages/view_status_unview/$1';
$route['view_status_view/(:any)'] = 'pages/view_status_view/$1';

$route['reg'] = 'pages/reg';
$route['register'] = 'pages/register';
$route['registered_profile/(:any)'] = 'pages/registered_profile/$1';

$route['profile_ap/(:any)'] = 'pages/profile_ap/$1';
$route['profile_staff/(:any)'] = 'pages/profile_staff/$1';

$route['pass'] = 'pages/pass';
$route['apply'] = 'pages/apply';
$route['applicant'] = 'pages/applicant';
$route['ap_delete/(:any)'] = 'pages/ap_delete/$1';

$route['users'] = 'pages/users';
$route['user_add'] = 'pages/user_add';
$route['user_edit/(:any)'] = 'pages/user_edit/$1';
$route['user_delete/(:any)'] = 'pages/user_delete/$1';

$route['hrusers'] = 'pages/hrusers';
$route['eval_user_add'] = 'pages/eval_user_add';
$route['hruser_edit/(:any)'] = 'pages/user_edit/$1';

$route['log_in'] = 'pages/log_in';
$route['logout'] = 'pages/logout';
$route['lock'] = 'pages/lock';
$route['lock_user_screen'] = 'pages/lock_user_screen';

$route['profile/(:any)'] = 'pages/profile/$1';
$route['new_applicant'] = 'pages/new_applicant';
$route['secretariat'] = 'pages/secretariat';

$route['davor_confession'] = 'pages/davor_confession';
$route['private'] = 'Ps/private';

$route['coor'] = 'Coor/coor';
$route['mycoor'] = 'Coor/mycoor';

$route['trainings'] = 'Hrtd/hrtd_trainings';
$route['available_trainings'] = 'Hrtd/hrtd_trainings_school';
$route['owned_program'] = 'Hrtd/hrtd_trainings_program_owned';

$route['complaints'] = 'Legal/complaints';
$route['provident'] = 'Provident/provident';
$route['loans'] = 'Provident/loan_list';
$route['paid_loans'] = 'Provident/paid_loan_list';
$route['implementing'] = 'Provident/implementing_loans';
$route['payment/(:any)/(:any)'] = 'Provident/implementing_payment/$1';
$route['provident_school'] = 'Provident/provident_school';

$route['evaluators_account/(:any)'] = 'pages/evaluators_account/$1';

$route['register_profile/(:any)'] = 'pages/register_profile/$1';

$route['user_pass/(:any)'] = 'pages/user_pass/$1';

// new rout for biometric
$route['System_settings/upload_biometric_logs'] = 'System_settings/upload_biometric_logs';
$route['System_settings/upload_biometric_logs_submit'] = 'System_settings/upload_biometric_logs_submit';
$route['System_settings/biometric_employee_mapping'] = 'System_settings/biometric_employee_mapping';
$route['System_settings/save_biometric_employee_mapping'] = 'System_settings/save_biometric_employee_mapping';
$route['System_settings/process_biometric_dtr'] = 'System_settings/process_biometric_dtr';
$route['System_settings/run_biometric_dtr_process'] = 'System_settings/run_biometric_dtr_process';
$route['System_settings/biometric_dtr_viewer'] = 'System_settings/biometric_dtr_viewer';
$route['System_settings/biometric_dtr_viewer/(:any)/(:num)/(:num)'] = 'System_settings/biometric_dtr_viewer/$1/$2/$3';
$route['System_settings/my_dtr'] = 'System_settings/my_dtr';
$route['System_settings/my_dtr/(:num)/(:num)'] = 'System_settings/my_dtr/$1/$2';
$route['System_settings/print_my_dtr/(:num)/(:num)'] = 'System_settings/print_my_dtr/$1/$2';
$route['System_settings/verify_dtr'] = 'System_settings/verify_dtr';
$route['System_settings/verify_dtr/(:any)'] = 'System_settings/verify_dtr/$1';
$route['System_settings/manual_timelog'] = 'System_settings/manual_timelog';
$route['System_settings/save_manual_timelog'] = 'System_settings/save_manual_timelog';
$route['System_settings/manual_timelog_list'] = 'System_settings/manual_timelog_list';
$route['System_settings/generate_missing_dtr_logs'] = 'System_settings/generate_missing_dtr_logs';
$route['System_settings/add_specific_dtr_log'] = 'System_settings/add_specific_dtr_log';
$route['System_settings/delete_specific_dtr_log'] = 'System_settings/delete_specific_dtr_log';
$route['system_settings/add_specific_dtr_log'] = 'System_settings/add_specific_dtr_log';
$route['system_settings/delete_specific_dtr_log'] = 'System_settings/delete_specific_dtr_log';
$route['System_settings/process_logs'] = 'System_settings/process_logs';
$route['late_undertime_summary'] = 'System_settings/late_undertime_summary';
$route['leave'] = 'leave/index';
$route['leave/my_applications'] = 'leave/my_applications';
$route['leave/balance'] = 'leave/balance';
$route['leave_settings/upload_leave_balances'] = 'leave_settings/upload_leave_balances';
$route['leave_settings/upload_leave_balances_submit'] = 'leave_settings/upload_leave_balances_submit';
$route['leave/store'] = 'leave/store';
$route['leave_admin'] = 'leave_admin/index';
$route['leave_admin/my_queue'] = 'leave_admin/my_queue';
$route['leave_admin/certify/(:num)'] = 'leave_admin/certify/$1';
$route['leave_admin/recommend/(:num)'] = 'leave_admin/recommend/$1';
$route['leave_admin/approve/(:num)'] = 'leave_admin/approve/$1';
$route['leave_admin/reject/(:num)'] = 'leave_admin/reject/$1';
$route['leave/view_application/(:num)'] = 'leave/view_application/$1';
$route['leave/print_preview/(:num)'] = 'leave/print_preview/$1';
$route['leave_admin/view_application/(:num)'] = 'leave_admin/view_application/$1';
$route['leave_admin/print_preview/(:num)'] = 'leave_admin/print_preview/$1';
$route['leave_balance_admin'] = 'Leave_balance_admin/index';
$route['leave_balance_admin/upload_balances'] = 'Leave_balance_admin/upload_balances';
$route['leave_balance_admin/privileges'] = 'Leave_balance_admin/privileges';
$route['leave_balance_admin/save_privilege'] = 'Leave_balance_admin/save_privilege';
$route['leave_balance_admin'] = 'Leave_balance_admin/index';
$route['leave_balance_admin/upload_balances'] = 'Leave_balance_admin/upload_balances';
$route['leave_balance_admin/privileges'] = 'Leave_balance_admin/privileges';
$route['leave_balance_admin/save_privilege'] = 'Leave_balance_admin/save_privilege';
$route['leave_balance_admin/upload_history'] = 'Leave_balance_admin/upload_history';
$route['leave_balance_admin/privilege_history'] = 'Leave_balance_admin/privilege_history';
$route['leave_balance_admin/upload'] = 'leave_balance_admin/upload';
$route['leave_balance_admin/process_upload'] = 'leave_balance_admin/process_upload';
$route['leave_balance_admin/entitlements'] = 'leave_balance_admin/entitlements';
$route['leave_balance_admin/approve_entitlement/(:num)'] = 'leave_balance_admin/approve_entitlement/$1';
$route['leave_balance_admin/deny_entitlement/(:num)'] = 'leave_balance_admin/deny_entitlement/$1';
$route['leave/request_entitlement'] = 'leave/request_entitlement';
$route['leave_balance_admin/run_monthly_credits'] = 'leave_balance_admin/run_monthly_credits';
$route['leave_balance_admin/process_entitlement_action'] = 'leave_balance_admin/process_entitlement_action';
$route['leave_admin/cancel_approved/(:num)'] = 'leave_admin/cancel_approved/$1';
$route['leave_admin/recall_approved/(:num)'] = 'leave_admin/recall_approved/$1';
$route['leave_balance_admin/adjust_balance'] = 'leave_balance_admin/adjust_balance';



$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
$route['Drrm'] = 'Drrm/index';
$route['drrm'] = 'Drrm/index';