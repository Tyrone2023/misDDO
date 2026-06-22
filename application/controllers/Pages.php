<?php

class Pages extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
        $this->load->helper('rqa');

        $this->load->model('SettingsModel');

        $this->load->model('PersonnelModel');
        $this->load->model('Page_model');
         $this->load->model('Drrm_model', 'drrm');
        $this->load->model('Secretariat_model', 'secretariat');
        $this->load->model('ResearchModel');
    // load AssignRater model so we can check for active (non-Closed) assignments
    $this->load->model('AssignRater_model', 'assignRater');
    }

    private function get_rqa_sign($job, $stat = null, $useJobSign = true)
    {
        $sign = null;
        $fy = !empty($job) && isset($job->sy) ? $job->sy : null;
        $signId = !empty($job) && isset($job->sign) ? $job->sign : null;

        if (!empty($fy)) {
            if ($useJobSign && !empty($signId)) {
                if ($stat === null) {
                    $sign = $this->Common->two_cond_row('hris_rqa_sign', 'fy', $fy, 'id', $signId);
                } else {
                    $sign = $this->Common->three_cond_row('hris_rqa_sign', 'fy', $fy, 'stat', $stat, 'id', $signId);
                }
            }

            if (empty($sign) && $stat !== null) {
                $sign = $this->Common->two_cond_row('hris_rqa_sign', 'fy', $fy, 'stat', $stat);
            }

            if (empty($sign) && $useJobSign && !empty($signId)) {
                $sign = $this->Common->one_cond_row('hris_rqa_sign', 'id', $signId);
            }

            if (empty($sign)) {
                $sign = $this->Common->one_cond_row('hris_rqa_sign', 'fy', $fy);
            }
        } elseif ($useJobSign && !empty($signId)) {
            $sign = $this->Common->one_cond_row('hris_rqa_sign', 'id', $signId);
        }

        if (empty($sign)) {
            $jobID = !empty($job) && isset($job->jobID) ? $job->jobID : 'unknown';
            log_message('error', 'Missing RQA sign record for jobID '.$jobID);
        }

        return $this->rqa_sign_defaults($sign);
    }

    private function rqa_sign_defaults($sign = null)
    {
        $defaults = [
            'pdate' => '',
            'm1_sign' => 'blank',
            'm1n' => '',
            'm1p' => '',
            'm2_sign' => 'blank',
            'm2n' => '',
            'm2p' => '',
            'm3_sign' => 'blank',
            'm3n' => '',
            'm3p' => '',
            'm4_sign' => 'blank',
            'm4n' => '',
            'm4p' => '',
            'm5_sign' => 'blank',
            'm5n' => '',
            'm5p' => '',
            'm6_sign' => 'blank',
            'm6n' => '',
            'm6p' => '',
            'm7s' => 'blank',
            'm7n' => '',
            'm7p' => '',
            'm8s' => '',
            'm8n' => '',
            'm8p' => '',
            'sds_sign' => 'blank',
            'sdsn' => '',
            'sdsp' => '',
            'asds_sign' => 'blank',
            'asdsn' => '',
            'asdsp' => '',
        ];

        if (empty($sign)) {
            return (object) $defaults;
        }

        foreach ($defaults as $field => $value) {
            if (!isset($sign->$field)) {
                $sign->$field = $value;
            }
        }

        return $sign;
    }

    private function rqa_export_requested()
    {
        $export = strtolower((string) $this->input->get('export'));

        return in_array($export, ['excel', 'xls'], true) || $this->input->get('excel') === '1';
    }

    private function rqa_excel_export_url()
    {
        $params = $this->input->get();
        $params = is_array($params) ? $params : [];
        $params['export'] = 'excel';

        return current_url() . '?' . http_build_query($params, '', '&');
    }

    private function rqa_excel_filename($prefix, $jobID = null, $suffix = '')
    {
        $parts = [$prefix];

        if (!empty($jobID)) {
            $parts[] = 'job-' . $jobID;
        }

        if ($suffix !== '') {
            $parts[] = $suffix;
        }

        $filename = preg_replace('/[^A-Za-z0-9_-]+/', '-', implode('-', $parts));
        $filename = trim($filename, '-_');

        return ($filename !== '' ? $filename : 'rqa-report') . '.xls';
    }

    private function add_rqa_excel_export_button($html, $url)
    {
        $button = '<div class="no-print rqa-excel-action" style="position:fixed;top:12px;right:12px;z-index:9999;">'
            . '<a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '" '
            . 'style="display:inline-block;background:#107c41;color:#fff;padding:9px 14px;border-radius:4px;'
            . 'font-family:Arial,sans-serif;font-size:13px;font-weight:bold;text-decoration:none;'
            . 'box-shadow:0 2px 6px rgba(0,0,0,.18);">Export Excel</a>'
            . '</div>';

        if (stripos($html, '</head>') !== false) {
            $html = preg_replace(
                '/<\/head>/i',
                '<style type="text/css">@media print{.no-print{display:none!important;}}</style></head>',
                $html,
                1
            );
        }

        if (preg_match('/<body[^>]*>/i', $html, $match, PREG_OFFSET_CAPTURE)) {
            $insertAt = $match[0][1] + strlen($match[0][0]);

            return substr($html, 0, $insertAt) . $button . substr($html, $insertAt);
        }

        return $button . $html;
    }

    private function inline_rqa_excel_styles($html)
    {
        $cssPath = FCPATH . 'assets/css/renren_style.css';

        if (!is_file($cssPath)) {
            return $html;
        }

        $style = '<style type="text/css">' . file_get_contents($cssPath) . '</style>';
        $replacements = 0;
        $html = preg_replace(
            '#<link\s+[^>]*href=["\'][^"\']*assets/css/renren_style\.css[^>]*>\s*#i',
            $style,
            $html,
            1,
            $replacements
        );

        if (empty($replacements) && stripos($html, '</head>') !== false) {
            $html = preg_replace('/<\/head>/i', $style . '</head>', $html, 1);
        }

        return $html;
    }

    private function render_rqa_report($page, $data, $filename)
    {
        $data['is_excel_export'] = $this->rqa_export_requested();

        if (!empty($data['is_excel_export'])) {
            if (ob_get_level() > 0 && ob_get_length()) {
                ob_clean();
            }

            header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');

            $html = $this->load->view('pages/' . $page, $data, true);
            $html = $this->inline_rqa_excel_styles($html);

            echo "\xEF\xBB\xBF" . $html;
            exit;
        }

        $html = $this->load->view('pages/' . $page, $data, true);
        $this->output->set_output($this->add_rqa_excel_export_button($html, $this->rqa_excel_export_url()));
    }


    public function view()
    {


        if ($this->session->position == 'smme') {

            $page = "dashboard_smme";

            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }

            $data['title'] = "Company List";
            $data['pn'] = "Company";
            $data['link'] = "company_new";

            $fy = $this->session->cur_fy;


            //$result['data'] = $this->Page_model->count_all('hris_staff');
            $result['sub'] = $this->SGODModel->two_cond_count_rows('sgod_aip_submit', 'fy', $fy, 'status', 0);
            $result['ap'] = $this->SGODModel->two_cond_count_rows('sgod_aip_submit', 'fy', $fy, 'status', 1);
            $result['req'] = $this->SGODModel->two_cond_count_rows('sgod_aip_request', 'fy', $fy, 'stat', 0);



            $this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page, $result);
            $this->load->view('templates/modal');
            $this->load->view('templates/footer');
        } elseif ($this->session->position == 'Accountant') {


            $page = "dashboard_smme";

            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }

            $data['title'] = "Company List";
            $data['pn'] = "Company";
            $data['link'] = "company_new";


            $result['data'] = $this->Page_model->count_all('hris_staff');
            $result['sub'] = $this->SGODModel->two_cond_count_rows('sgod_aip_submit', 'fy', '2024', 'status', 0);
            $result['ap'] = $this->SGODModel->two_cond_count_rows('sgod_aip_submit', 'fy', '2024', 'status', 1);
            $result['req'] = $this->SGODModel->two_cond_count_rows('sgod_aip_request', 'fy', '2024', 'stat', 0);
            $result['last'] = $this->SGODModel->get_last_record('sgod_school_allocation');



            $this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page, $result);
            $this->load->view('templates/modal');
            $this->load->view('templates/footer');
        }elseif ($this->session->position == 'review') {

            $page = "dashboard_smmev2";

            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }

            $result['title'] = "Company List";


            // $result['data'] = $this->Page_model->count_all('hris_staff');
            // $result['sub'] = $this->SGODModel->two_cond_count_rows('sgod_aip_submit', 'fy', '2024', 'status', 0);
            // $result['ap'] = $this->SGODModel->two_cond_count_rows('sgod_aip_submit', 'fy', '2024', 'status', 1);
            // $result['req'] = $this->SGODModel->two_cond_count_rows('sgod_aip_request', 'fy', '2024', 'stat', 0);
            // $result['last'] = $this->SGODModel->get_last_record('sgod_school_allocation');



            $this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page, $result);
            $this->load->view('templates/modal');
            $this->load->view('templates/footer');
        }elseif ($this->session->position == 'funds') {

            $page = "dashboard_smmev2";

            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }

            $result['title'] = "Company List";


            // $result['data'] = $this->Page_model->count_all('hris_staff');
            // $result['sub'] = $this->SGODModel->two_cond_count_rows('sgod_aip_submit', 'fy', '2024', 'status', 0);
            // $result['ap'] = $this->SGODModel->two_cond_count_rows('sgod_aip_submit', 'fy', '2024', 'status', 1);
            // $result['req'] = $this->SGODModel->two_cond_count_rows('sgod_aip_request', 'fy', '2024', 'stat', 0);
            // $result['last'] = $this->SGODModel->get_last_record('sgod_school_allocation');



            $this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page, $result);
            $this->load->view('templates/modal');
            $this->load->view('templates/footer');
        }
        
       
        



        
        
        elseif ($this->session->position == 'School') {
            if ($this->session->userdata('sp') != 0) {
                $sp = $this->Common->one_cond_row('users_sp', 'id', $this->session->sp);
                if ($sp->position == "SBFP") {
                    redirect(base_url() . 'Page/sbfp_dashboard');
                }
            }

            $page = "dss";

            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }

            $data['title'] = "Company List";
            $data['pn'] = "Company";
            $data['link'] = "company_new";


            $id = $this->session->c_id;
            $result['data'] = $this->PersonnelModel->count_all_staff($id);
            $result['data1'] = $this->PersonnelModel->count_all_teaching($id);
            $result['data2'] = $this->PersonnelModel->count_all_nonteaching($id);
            $result['data3'] = $this->PersonnelModel->count_all_inactive($id);
            $result['data4'] = $this->PersonnelModel->announcements_post();
            $result['data5'] = $this->PersonnelModel->school_dashboard($id);



            $this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page, $result);
            $this->load->view('templates/modal');
            $this->load->view('templates/footer');
        }elseif ($this->session->position == 'sbfp') {

            redirect(base_url() . 'Page/sbfp_dashboard');

            $page = "dss";

            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }

            $data['title'] = "Company List";
            $data['pn'] = "Company";
            $data['link'] = "company_new";


            $id = $this->session->c_id;
            $result['data'] = $this->PersonnelModel->count_all_staff($id);
            $result['data1'] = $this->PersonnelModel->count_all_teaching($id);
            $result['data2'] = $this->PersonnelModel->count_all_nonteaching($id);
            $result['data3'] = $this->PersonnelModel->count_all_inactive($id);
            $result['data4'] = $this->PersonnelModel->announcements_post();
            $result['data5'] = $this->PersonnelModel->school_dashboard($id);



            $this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page, $result);
            $this->load->view('templates/modal');
            $this->load->view('templates/footer');
        } elseif ($this->session->position == 'doceval') {

            $page = "doceval";

            $cid = $this->session->c_id;

            if ($this->session->userdata('position') === 'Admin') {
            } elseif ($this->session->userdata('position') === 'Super Admin') {
            } elseif ($this->session->userdata('position') === 'Staff') {
            } elseif ($this->session->userdata('position') === 'smme') {
            } else {
                $this->Page_model->check_ownership($cid);
            }

            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }

            $data['title'] = "Company List";
            $data['pn'] = "Company";
            $data['link'] = "company_new";


            $result['data'] = $this->Page_model->count_all('hris_staff');
            $result['data1'] = $this->Page_model->count_inactive('hris_staff');
            $result['data2'] = $this->Page_model->count_teaching('hris_staff');
            $result['data3'] = $this->Page_model->count_nonteaching('hris_staff');
            $result['data4'] = $this->Page_model->count_for_approval_leave('hris_leave');



            $this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page, $result);
            $this->load->view('templates/modal');
            $this->load->view('templates/footer');
        } elseif ($this->session->position == 'District' || $this->session->position == 'Evaluator' || $this->session->position == 'rater') {

            // If evaluator/rater already has assignments (excluding Closed vacancies), send to the new dashboard
            if (in_array($this->session->position, ['Evaluator','rater','raters'])) {
                $rid = $this->session->id ?? $this->session->userdata('id');
                if ($rid) {
                    // use the AssignRater model which already filters out assignments for Closed job vacancies
                    $assignments = $this->assignRater->get_assigned_applicants((int)$rid);
                    $hasAssigned = (!empty($assignments['pending']) || !empty($assignments['scored']));
                    if ($hasAssigned) {
                        redirect(base_url('EvaluatorAssigned'));
                    }
                }
            }

            $page = "dashboard_district";

            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }

            $data['title'] = "DepEd Davao Oriental";
            $data['district'] = $this->Common->one_cond_row('district', 'id', $this->session->c_id);



            $this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page, $data);
            $this->load->view('templates/modal');
            $this->load->view('templates/footer');
        } elseif ($this->session->position == 'Validator') {

            $page = "dashboard_district";

            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }

            $data['title'] = "Company List";



            $this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page, $data);
            $this->load->view('templates/modal');
            $this->load->view('templates/footer');
        } elseif ($this->session->position == 'readmin') {

            $page = "dashboard_district";

            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }

            $data['title'] = "Company List";



            $this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page, $data);
            $this->load->view('templates/modal');
            $this->load->view('templates/footer');
        } elseif ($this->session->position == 'sgod') {

            $page = "dashboard_sgod";

            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }

            $data['title'] = "School Governance and Operations Division";

            $data['school'] = $this->Common->no_cond_count_row('schools');
            $data['accom'] = $this->Common->no_cond_count_row('sgod_accomplishments');
            $data['section'] = $this->Common->no_cond_count_row('sgod_sections');
            


            $this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page, $data);
            $this->load->view('templates/modal');
            $this->load->view('templates/footer');

        } elseif ($this->session->position == 'socmob') {

            $page = "dashboard_socmob";

            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }

            $data['title'] = "Social Mobilization and Networking Section";

            


            $this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page, $data);
            $this->load->view('templates/footer');
        } elseif ($this->session->position == 'Secretariat') {
            $page = "dashboard_secretariat";

            $userId = $this->session->id ?? $this->session->userdata('id');
            $jobTypes = $this->secretariat->user_job_types((int) $userId);
            $jobTypeLabels = $this->secretariat->job_types_map();
            $fy = (int) date('Y');

            $result['title'] = "Secretariat Dashboard";
            $result['jobTypes'] = $jobTypes;
            $result['jobTypeLabels'] = $jobTypeLabels;
            $result['counts'] = [
                'validated' => $this->secretariat->count_by_status($jobTypes, 'Validated'),
                'endorsed'  => $this->secretariat->count_by_status($jobTypes, 'Endorsed for Rating'),
                'rated'     => $this->secretariat->count_by_status($jobTypes, 'Rated'),
                'no_rater'  => $this->secretariat->count_endorsed_without_rater($jobTypes, $fy),
                'dq'        => $this->secretariat->count_dq_applicants($jobTypes, $fy),
            ];

            $this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page, $result);
            $this->load->view('templates/modal');
            $this->load->view('templates/footer');

       
        } elseif ($this->session->position == 'Human Resource Admin') {
            $page = "dashboard_hr";
            $cid = $this->session->c_id;
            $empEmail = $this->session->userdata('username'); // or 'email' if that's used in the `endorser` / `approver` field
            

            // View validation
            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }
            
            // Static page info
            $data['title'] = "Company List";
            $data['pn'] = "Company";
            $data['link'] = "company_new";
            
            // Common data
            $result['data'] = $this->Page_model->count_all('hris_staff');
            $result['data1'] = $this->Page_model->count_inactive('hris_staff');
            $result['data2'] = $this->Page_model->count_teaching('hris_staff');
            $result['data3'] = $this->Page_model->count_nonteaching('hris_staff');
            $result['data4'] = $this->Page_model->count_for_approval_leave('hris_leave');
                $result['data5'] = $this->Page_model->count_for_approval_leave1('hris_leave');
            
            
     
            // Load the views
            $this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page, $result);
            $this->load->view('templates/modal');
            $this->load->view('templates/footer');
            
        }
        elseif ($this->session->position == 'private') {
            $page = "dashboard_private";
            $cid = $this->session->c_id;
            $empEmail = $this->session->userdata('username'); // or 'email' if that's used in the `endorser` / `approver` field
            

            // View validation
            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }
            
            // Static page info
            $data['title'] = "Private School Dashboard";
            $data['pn'] = "Company";
            
     
            // Load the views
            $this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page, $data);
            $this->load->view('templates/modal');
            $this->load->view('templates/footer');
            
        }elseif ($this->session->position == 'psu') {
            $page = "dashboard_private";
            

            // View validation
            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }
            
            // Static page info
            $data['title'] = "Private School Dashboard";
            $data['pn'] = "Company";
            
     
            // Load the views
            $this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page, $data);
            $this->load->view('templates/modal');
            $this->load->view('templates/footer');
            
        }elseif ($this->session->position == 'hrtd') {
            $page = "dashboard_hrtd";
            

            // View validation
            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }
            
            // Static page info
            $data['title'] = "HRTD Dashboard";
            $data['pn'] = "Company";
            
     
            // Load the views
            $this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page, $data);
            $this->load->view('templates/modal');
            $this->load->view('templates/footer');
            
        }elseif ($this->session->position == 'del') {
            $page = "dashboard_hrtd";
            

            // View validation
            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }
            
            // Static page info
            $data['title'] = "Dashboard";
            $data['pn'] = "Company";
            
     
            // Load the views
            $this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page, $data);
            $this->load->view('templates/modal');
            $this->load->view('templates/footer');
            
        }elseif ($this->session->position == 'research') {
            $page = "dashboard_research";
            

            // View validation
            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }
            
            // Static page info
            $data['title'] = "Research Dashboard";
            $data['pn'] = "Company";
            $data['temp_permit_count'] = $this->ResearchModel->total_requests();
            $data['research_requests'] = $this->ResearchModel->all_requests();
            
     
            // Load the views
            $this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page, $data);
            $this->load->view('templates/modal');
            $this->load->view('templates/footer');
            
        }elseif ($this->session->position == 'legal') {
            $page = "dashboard_legal";
            

            // View validation
            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }
            
            // Static page info
            $data['title'] = "Legal Dashboard";
            $data['pn'] = "Company";
            
     
            // Load the views
            $this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page, $data);
            $this->load->view('templates/modal');
            $this->load->view('templates/footer');
            
        }elseif ($this->session->position == 'provident') {
            $page = "dashboard_provident";
            

            // View validation
            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }
            
            // Static page info
            $data['title'] = "Provident Dashboard";
            $data['pn'] = "Company";
            
     
            // Load the views
            $this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page, $data);
            $this->load->view('templates/modal');
            $this->load->view('templates/footer');
            
        }elseif ($this->session->position == 'mathcoor') {
            $page = "dashboard_mathcoor";
            

            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }
            
            
            $data['title'] = "Math Coordinator Dashboard";
            $data['pn'] = "Company";
            
     
            $this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page, $data);
            $this->load->view('templates/modal');
            $this->load->view('templates/footer');
            
        }
        
        else {
            $page = "dashboard";
            $cid = $this->session->c_id;
            $empEmail = $this->session->userdata('username'); // or 'email' if that's used in the `endorser` / `approver` field
            
            // Role access logic
            $position = $this->session->userdata('position');
            if (!in_array($position, ['Admin', 'Super Admin', 'Staff', 'smme','Human Resource Admin'])) {
                $this->Page_model->check_ownership($cid);
            }
            
            // Check if the user is an endorser or approver
            $is_endorser = $this->Page_model->is_user_endorser($empEmail);
            $is_approver = $this->Page_model->is_user_approver($empEmail);
            
            // View validation
            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }
            
            // Static page info
            $data['title'] = "Company List";
            $data['pn'] = "Company";
            $data['link'] = "company_new";
            
            // Common data
            $result['data'] = $this->Page_model->count_all('hris_staff');
            $result['data1'] = $this->Page_model->count_inactive('hris_staff');
            $result['data2'] = $this->Page_model->count_teaching('hris_staff');
            $result['data3'] = $this->Page_model->count_nonteaching('hris_staff');
            $result['data4'] = $this->Page_model->count_for_approval_leave('hris_leave');
            
            // Conditional logic for roles
            if ($is_approver) {
                $result['data5'] = $this->Page_model->count_for_approval_leave3('hris_leave', $empEmail);
                $result['data7'] = $this->Page_model->count_for_approval_leave4('hris_leave', $empEmail);
            } else {
                $result['data5'] = $this->Page_model->count_for_approval_leave1('hris_leave');
            }
            
            // Optional: pass role flags to the view
            $result['is_endorser'] = $is_endorser;
            $result['is_approver'] = $is_approver;
            
            // Load the views
            $this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page, $result);
            $this->load->view('templates/modal');
            $this->load->view('templates/footer');
            
        }
    }


    public function view_user()
    {
        $page = "dashboard_user";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $data['title'] = "Company List";
        $data['pn'] = "Company";
        $data['link'] = "company_new";

        $empEmail = $this->session->userdata('username');
        $result['data1'] = $this->Page_model->countvacancy('hris_jobvacancy');
        $result['data2'] = $this->Page_model->countapplications('hris_applications', $empEmail);
        $result['data5'] = $this->Page_model->count_for_approval_leave3('hris_leave', $empEmail);
        $result['data7'] = $this->Page_model->count_for_approval_leave4('hris_leave', $empEmail);
        $result['data6'] = $this->Page_model->count_for_approval_leave2('hris_leave');
    
        // ✅ Check if current user is listed as an endorser
        $result['is_endorser'] = $this->Page_model->is_user_endorser($empEmail);
        $result['is_approver'] = $this->Page_model->is_user_approver($empEmail);

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $result);
        $this->load->view('templates/modal');
        $this->load->view('templates/footer');
    }

    public function view_employee()
    {
        $page = "dashboard_employee";
    
        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
    
        $data['title'] = "Company List";
        $data['pn'] = "Company";
        $data['link'] = "company_new";
    
        $empEmail = $this->session->userdata('username');
    
        // Existing data
        $result['data1'] = $this->Page_model->countTrainingNeeds('hris_training_needs', $empEmail);
        $result['data2'] = $this->Page_model->countTrainingDevt('hris_ind_devt', $empEmail);
        $result['data3'] = $this->Page_model->vlCount($empEmail);
        $result['data4'] = $this->PersonnelModel->announcements_post();
        $result['data5'] = $this->Page_model->count_for_approval_leave3('hris_leave', $empEmail);
        $result['data7'] = $this->Page_model->count_for_approval_leave4('hris_leave', $empEmail);
        $result['data6'] = $this->Page_model->count_for_approval_leave2('hris_leave');
    
        // ✅ Check if current user is listed as an endorser
        $result['is_endorser'] = $this->Page_model->is_user_endorser($empEmail);
        $result['is_approver'] = $this->Page_model->is_user_approver($empEmail);
    
        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $result);
        $this->load->view('templates/modal');
        $this->load->view('templates/footer');
    }
    
    public function users()
    {
        $page = "user_list";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $data['title'] = "User's List";
        $data['pn'] = "Add New User";
        $data['link'] = "user_add";

        if ($this->session->position == 'Super Admin') {
            $data['users'] = $this->Common->no_cond_select('users','id,username,position,fname,fname,mname,lname');
            //$data['users'] = $this->Common->no_cond('users');
        } else {
            $data['users'] = $this->Common->no_cond_except_select('users','id,username,position,fname,fname,mname,lname');
            //$data['users'] = $this->Common->no_cond('users');
        }


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/modal_com');
        $this->load->view('templates/footer');
    }

    public function usersv2()
    {
        $page = "user_list";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $data['title'] = "User's List";
        $data['pn'] = "Add New User";
        $data['link'] = "user_add";

        $data['users'] = $this->Common->one_cond_select('users','id,username,position,fname,fname,mname,lname','position',$this->uri->segment('3'));
         


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/modal_com');
        $this->load->view('templates/footer');
    }

    public function hrusers()
    {
        $page = "user_listv2";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $data['title'] = "User's List";
        $data['pn'] = "Add New User";
        $data['link'] = "eval_user_add";


        $data['users'] = $this->Common->one_cond_or('users', 'position', 'user', 'position', 'reg');



        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/modal_com');
        $this->load->view('templates/footer');
    }

    public function users_school()
    {
        $page = "user_list_school";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $data['title'] = "User's List";
        $data['pn'] = "Add New User";
        $data['link'] = "user_add";
        $IDNumber = $this->session->userdata('username');
        $data['users'] = $this->Page_model->get_post_school('users', 'position', 'School', 'username', 'IDNumber');

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/modal_com');
        $this->load->view('templates/footer');
    }

    public function user_add()
    {

        $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        ', '</div>');
        $this->form_validation->set_rules('Username', 'Username', 'required');

        if ($this->form_validation->run() == FALSE) {

            $page = "user_new";

            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }

            $data['users'] = $this->Page_model->get_post_except_group_by('users', 'position', 'Super Admin');
            $data['district'] = $this->Common->no_cond('district');
            $data['title'] = "Add New User";

            $this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page, $data);
            $this->load->view('templates/footer');
        } else {

            $this->Page_model->insert_user();
            $this->Page_model->insert_at('Add New user', $this->db->insert_id());
            $this->session->set_flashdata('success', ' New User Added.');
            redirect(base_url() . 'users');
        }
    }

    public function eval_user_add()
    {

        $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        ', '</div>');
        $this->form_validation->set_rules('Username', 'Username', 'required');

        if ($this->form_validation->run() == FALSE) {

            $page = "user_eval_new";

            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }

            $data['users'] = $this->Page_model->get_post_except_group_by('users', 'position', 'Super Admin');
            $data['district'] = $this->Common->no_cond('district');
            $data['title'] = "Add New User";

            $this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page, $data);
            $this->load->view('templates/footer');
        } else {

            $this->Page_model->insert_user();
            $this->Page_model->insert_at('Add New user', $this->db->insert_id());
            $this->session->set_flashdata('success', ' New User Added.');
            redirect(base_url() . 'users');
        }
    }

    public function evaluators_account()
    {

        $page = "eval_new";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $data['title'] = "Evaluator Account";
        $data['district'] = $this->Common->no_cond('district');



        $this->load->view('templates/header_public');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer_public');
    }

    public function secretariat()
    {
        // $this->session->set_flashdata('failed', 'Temporary closure, please contact the system administrator');
        // redirect(base_url().'log_in');

        $page = "sec_new";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $data['title'] = "Secretariat Account";
        $data['district'] = $this->Common->no_cond('district');



        $this->load->view('templates/header_public');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer_public');
    }

    public function secretariat_endorsed()
    {
        if ($this->session->position !== 'Secretariat') {
            show_error('Forbidden', 403);
            return;
        }

        $page = "secretariat_endorsed";
        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $userId = $this->session->id ?? $this->session->userdata('id');
        $jobTypes = $this->secretariat->user_job_types((int) $userId);
        $fy = (int) date('Y');

        // Clean duplicates before loading lists
        $this->secretariat->merge_rating_duplicates();

        $data = [
            'title' => 'Secretariat Endorsed Applicants',
            'jobTypeLabels' => $this->secretariat->job_types_map(),
            'endorsed' => empty($jobTypes) ? [] : $this->secretariat->demo_trf_unscored($jobTypes, $fy),
            'scored' => empty($jobTypes) ? [] : $this->secretariat->demo_trf_scored($jobTypes, $fy),
            'hasAssignment' => !empty($jobTypes),
        ];

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function secretariat_dq_applicants()
    {
        if ($this->session->position !== 'Secretariat') {
            show_error('Forbidden', 403);
            return;
        }

        $page = "secretariat_dq";
        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $userId = $this->session->id ?? $this->session->userdata('id');
        $jobTypes = $this->secretariat->user_job_types((int) $userId);
        $fy = (int) date('Y');

        $data = [
            'title' => 'Disqualified Applicants (DQ=2)',
            'jobTypeLabels' => $this->secretariat->job_types_map(),
            'dq_applicants' => empty($jobTypes) ? [] : $this->secretariat->dq_applicants_list($jobTypes, $fy),
            'hasAssignment' => !empty($jobTypes),
        ];

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function secretariat_scores_report()
    {
        if ($this->session->position !== 'Secretariat') {
            show_error('Forbidden', 403);
            return;
        }

        $page = "secretariat_scores_report";
        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $userId = $this->session->id ?? $this->session->userdata('id');
        $jobTypes = $this->secretariat->user_job_types((int) $userId);
        $jobTypeLabels = $this->secretariat->job_types_map();
        $jobOptions = $this->secretariat->scores_report_job_options([]);
        $jobOptionMap = [];
        foreach ($jobOptions as $jobOption) {
            $jobOptionMap[(int) $jobOption->jobID] = $jobOption;
        }

        $districts = $this->secretariat->get_available_districts([]);
        $years = $this->secretariat->get_available_years([]);

        $fy = (int) date('Y');
        $filter = trim((string) $this->input->get('filter', true));
        $districtFilter = trim((string) $this->input->get('district', true));
        $yearFilter = trim((string) $this->input->get('year', true));
        $submitted = ($filter !== '' || $districtFilter !== '' || $yearFilter !== '');
        $selectedJobType = null;
        $selectedJobId = null;
        $filterError = '';
        $scores = [];

        if ($submitted) {
            if (preg_match('/^type:(\d+)$/', $filter, $matches)) {
                $selectedJobType = (int) $matches[1];
            } elseif (preg_match('/^job:(\d+)$/', $filter, $matches)) {
                $selectedJobId = (int) $matches[1];
                // Check if the selected job exists and is open
                if (!isset($jobOptionMap[$selectedJobId])) {
                    $selectedJobId = null;
                    $filterError = 'Selected job title is not available or is not open.';
                } else {
                    // Get the job type for data filtering purposes
                    $selectedJob = $jobOptionMap[$selectedJobId];
                    $selectedJobType = (int) $selectedJob->job_type;
                }
            } elseif ($filter !== '') {
                $filterError = 'Please select a valid job title or job type.';
            }

            if ($filterError === '') {
                $this->secretariat->merge_rating_duplicates();
                $yearFilterInt = !empty($yearFilter) ? (int) $yearFilter : null;
                $scores = $this->secretariat->scores_report_applicants([], $fy, $selectedJobType, $selectedJobId, $districtFilter, $yearFilterInt);
            }
        }

        $data = [
            'title' => 'Scores Report',
            'jobTypeLabels' => $jobTypeLabels,
            'assignedJobTypes' => $jobTypes,
            'jobOptions' => $jobOptions,
            'selectedFilter' => $filter,
            'selectedDistrict' => $districtFilter,
            'selectedYear' => $yearFilter,
            'districts' => $districts,
            'years' => $years,
            'submitted' => $submitted,
            'filterError' => $filterError,
            'scores' => $scores,
            'hasAssignment' => !empty($jobTypes),
        ];

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function secretariat_applicant_evaluation_report()
    {
        if ($this->session->position !== 'Secretariat') {
            show_error('Forbidden', 403);
            return;
        }

        $page = "secretariat_applicant_evaluation_report";
        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $userId = $this->session->id ?? $this->session->userdata('id');
        $jobTypes = $this->secretariat->user_job_types((int) $userId);
        $jobTypeLabels = $this->secretariat->job_types_map();
        $jobOptions = $this->secretariat->scores_report_job_options([]);
        $jobOptionMap = [];
        $availableJobTypes = [];
        foreach ($jobOptions as $jobOption) {
            $jobOptionMap[(int) $jobOption->jobID] = $jobOption;
            $availableJobTypes[(int) $jobOption->job_type] = true;
        }

        $years = $this->secretariat->get_available_years([]);
        $fy = (int) date('Y');
        $filter = trim((string) $this->input->get('filter', true));
        $yearFilter = trim((string) $this->input->get('year', true));
        $submitted = ($filter !== '' || $yearFilter !== '');
        $selectedJobType = null;
        $selectedJobId = null;
        $filterError = '';
        $applicants = [];

        if ($submitted) {
            if ($filter === '') {
                $filterError = 'Please select a job title or job type to generate the report.';
            } elseif (preg_match('/^type:(\d+)$/', $filter, $matches)) {
                $selectedJobType = (int) $matches[1];
                if (!isset($availableJobTypes[$selectedJobType])) {
                    $selectedJobType = null;
                    $filterError = 'Please select a valid job title or job type.';
                }
            } elseif (preg_match('/^job:(\d+)$/', $filter, $matches)) {
                $selectedJobId = (int) $matches[1];
                if (!isset($jobOptionMap[$selectedJobId])) {
                    $selectedJobId = null;
                    $filterError = 'Selected job title is not available or is not open.';
                } else {
                    $selectedJob = $jobOptionMap[$selectedJobId];
                    $selectedJobType = (int) $selectedJob->job_type;
                }
            } else {
                $filterError = 'Please select a valid job title or job type.';
            }

            if ($filterError === '') {
                $this->secretariat->merge_rating_duplicates();
                $yearFilterInt = !empty($yearFilter) ? (int) $yearFilter : null;
                $applicants = $this->secretariat->applicant_evaluation_report($fy, $selectedJobType, $selectedJobId, $yearFilterInt);
            }
        }

        $jobsByType = [];
        foreach (($jobOptions ?? []) as $job) {
            $jobType = (int) $job->job_type;
            if ($jobType === 0) {
                continue;
            }
            $jobsByType[$jobType][] = $job;
        }

        $data = [
            'title' => 'Applicant Evaluation Report',
            'jobTypeLabels' => $jobTypeLabels,
            'assignedJobTypes' => $jobTypes,
            'jobOptions' => $jobOptions,
            'jobsByType' => $jobsByType,
            'selectedFilter' => $filter,
            'selectedYear' => $yearFilter,
            'years' => $years,
            'submitted' => $submitted,
            'filterError' => $filterError,
            'applicants' => $applicants,
            'hasAssignment' => !empty($jobTypes),
            'hasOpenJobs' => !empty($jobsByType),
        ];

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function secretariat_inquiry_report()
    {
        if ($this->session->position !== 'Secretariat') {
            show_error('Forbidden', 403);
            return;
        }

        $page = "secretariat_inquiry_report";
        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $userId = $this->session->id ?? $this->session->userdata('id');
        $jobTypes = $this->secretariat->user_job_types((int) $userId);
        $jobTypeLabels = $this->secretariat->job_types_map();
        $jobOptions = $this->secretariat->scores_report_job_options([]);
        $jobOptionMap = [];
        foreach ($jobOptions as $jobOption) {
            $jobOptionMap[(int) $jobOption->jobID] = $jobOption;
        }

        $districts = $this->secretariat->get_available_districts([]);
        $years = $this->secretariat->get_available_years([]);

        $fy = (int) date('Y');
        $filter = trim((string) $this->input->get('filter', true));
        $districtFilter = trim((string) $this->input->get('district', true));
        $yearFilter = trim((string) $this->input->get('year', true));
        $submitted = ($filter !== '' || $districtFilter !== '' || $yearFilter !== '');
        $selectedJobType = null;
        $selectedJobId = null;
        $filterError = '';
        $inquiries = [];
        $statistics = [];

        if ($submitted) {
            // Require at least a job filter
            if ($filter === '') {
                $filterError = 'Please select a job title or job type to generate the report.';
            } elseif (preg_match('/^type:(\d+)$/', $filter, $matches)) {
                $selectedJobType = (int) $matches[1];
            } elseif (preg_match('/^job:(\d+)$/', $filter, $matches)) {
                $selectedJobId = (int) $matches[1];
                // Check if the selected job exists and is open
                if (!isset($jobOptionMap[$selectedJobId])) {
                    $selectedJobId = null;
                    $filterError = 'Selected job title is not available or is not open.';
                } else {
                    // Get the job type for data filtering purposes
                    $selectedJob = $jobOptionMap[$selectedJobId];
                    $selectedJobType = (int) $selectedJob->job_type;
                }
            } else {
                $filterError = 'Please select a valid job title or job type.';
            }

            if ($filterError === '') {
                $yearFilterInt = !empty($yearFilter) ? (int) $yearFilter : null;
                $inquiries = $this->secretariat->inquiry_report(
                    $fy,
                    $selectedJobType,
                    $selectedJobId,
                    $districtFilter,
                    $yearFilterInt,
                    null  // Status filter no longer used - always shows stat = 0
                );
                $statistics = $this->secretariat->inquiry_statistics(
                    $fy,
                    $selectedJobType,
                    $selectedJobId,
                    $districtFilter,
                    $yearFilterInt
                );
            }
        }

        $jobsByType = [];
        foreach (($jobOptions ?? []) as $job) {
            $jobType = (int) $job->job_type;
            if ($jobType === 0) {
                continue;
            }
            $jobsByType[$jobType][] = $job;
        }
        $hasOpenJobs = !empty($jobsByType);

        $data = [
            'title' => 'Inquiry Report',
            'jobTypeLabels' => $jobTypeLabels,
            'assignedJobTypes' => $jobTypes,
            'jobOptions' => $jobOptions,
            'selectedFilter' => $filter,
            'selectedDistrict' => $districtFilter,
            'selectedYear' => $yearFilter,
            'districts' => $districts,
            'years' => $years,
            'submitted' => $submitted,
            'filterError' => $filterError,
            'inquiries' => $inquiries,
            'statistics' => $statistics,
            'hasAssignment' => !empty($jobTypes),
            'hasOpenJobs' => $hasOpenJobs,
            'jobsByType' => $jobsByType,
        ];

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function secretariat_inquiry_report_print()
    {
        if ($this->session->position !== 'Secretariat') {
            show_error('Forbidden', 403);
            return;
        }

        $page = "secretariat_inquiry_report_print";
        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $userId = $this->session->id ?? $this->session->userdata('id');
        $jobTypes = $this->secretariat->user_job_types((int) $userId);
        $jobTypeLabels = $this->secretariat->job_types_map();
        $jobOptions = $this->secretariat->scores_report_job_options([]);
        $jobOptionMap = [];
        foreach ($jobOptions as $jobOption) {
            $jobOptionMap[(int) $jobOption->jobID] = $jobOption;
        }

        $districts = $this->secretariat->get_available_districts([]);
        $years = $this->secretariat->get_available_years([]);

        $fy = (int) date('Y');
        $filter = trim((string) $this->input->get('filter', true));
        $districtFilter = trim((string) $this->input->get('district', true));
        $yearFilter = trim((string) $this->input->get('year', true));
        $submitted = ($filter !== '' || $districtFilter !== '' || $yearFilter !== '');
        $selectedJobType = null;
        $selectedJobId = null;
        $filterError = '';
        $inquiries = [];
        $statistics = [];

        if ($submitted) {
            // Require at least a job filter
            if ($filter === '') {
                $filterError = 'Please select a job title or job type to generate the report.';
            } elseif (preg_match('/^type:(\d+)$/', $filter, $matches)) {
                $selectedJobType = (int) $matches[1];
            } elseif (preg_match('/^job:(\d+)$/', $filter, $matches)) {
                $selectedJobId = (int) $matches[1];
                // Check if the selected job exists and is open
                if (!isset($jobOptionMap[$selectedJobId])) {
                    $selectedJobId = null;
                    $filterError = 'Selected job title is not available or is not open.';
                } else {
                    // Get the job type for data filtering purposes
                    $selectedJob = $jobOptionMap[$selectedJobId];
                    $selectedJobType = (int) $selectedJob->job_type;
                }
            } else {
                $filterError = 'Please select a valid job title or job type.';
            }

            if ($filterError === '') {
                $yearFilterInt = !empty($yearFilter) ? (int) $yearFilter : null;
                $inquiries = $this->secretariat->inquiry_report(
                    $fy,
                    $selectedJobType,
                    $selectedJobId,
                    $districtFilter,
                    $yearFilterInt,
                    null  // Status filter no longer used - always shows stat = 0
                );
                $statistics = $this->secretariat->inquiry_statistics(
                    $fy,
                    $selectedJobType,
                    $selectedJobId,
                    $districtFilter,
                    $yearFilterInt
                );
            }
        }

        $jobsByType = [];
        foreach (($jobOptions ?? []) as $job) {
            $jobType = (int) $job->job_type;
            if ($jobType === 0) {
                continue;
            }
            $jobsByType[$jobType][] = $job;
        }
        $hasOpenJobs = !empty($jobsByType);

        // Get selected job/district/year names for display
        $selectedJobName = '';
        $selectedDistrictName = $districtFilter;
        $selectedYearName = $yearFilter ?: date('Y');
        
        if ($selectedJobId && isset($jobOptionMap[$selectedJobId])) {
            $selectedJob = $jobOptionMap[$selectedJobId];
            $jobTypeSuffixes = [
                1 => '- Elementary',
                2 => '- Secondary',
                3 => '- Junior High School',
                4 => '- Senior High School',
                5 => '- Kindergarten',
                6 => '- IPED Elementary',
                7 => '- IPED Secondary',
                8 => '- IPED Junior High School',
                9 => '- IPED Senior High School',
                10 => '- SNED',
            ];
            $typeSuffix = $jobTypeSuffixes[(int) $selectedJob->job_type] ?? ('- Job Type ' . (int) $selectedJob->job_type);
            $selectedJobName = ($selectedJob->jobTitle ?? '') . ' ' . $typeSuffix;
        }

        $data = [
            'title' => 'Inquiry Report - Printable Version',
            'jobTypeLabels' => $jobTypeLabels,
            'assignedJobTypes' => $jobTypes,
            'jobOptions' => $jobOptions,
            'selectedFilter' => $filter,
            'selectedDistrict' => $districtFilter,
            'selectedYear' => $yearFilter,
            'districts' => $districts,
            'years' => $years,
            'submitted' => $submitted,
            'filterError' => $filterError,
            'inquiries' => $inquiries,
            'statistics' => $statistics,
            'hasAssignment' => !empty($jobTypes),
            'hasOpenJobs' => $hasOpenJobs,
            'jobsByType' => $jobsByType,
            'selectedJobName' => $selectedJobName,
            'selectedDistrictName' => $selectedDistrictName,
            'selectedYearName' => $selectedYearName,
        ];

        $this->load->view('pages/' . $page, $data);
    }

    public function user_add2()
    {

        $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        ', '</div>');
        $this->form_validation->set_rules('Username', 'Username', 'required');

        if ($this->form_validation->run() == FALSE) {

            $page = "user_new";

            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }

            $data['users'] = $this->Page_model->get_post_except_group_by('users', 'position', 'Super Admin');
            $data['title'] = "Add New User";

            $this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page, $data);
            $this->load->view('templates/footer');
        } else {

            $check = $this->Common->one_cond_count_row('users', 'username', $this->input->post('Username'));

            if ($check->num_rows() >= 1) {
                $this->session->set_flashdata('danger', 'Duplicate username');
                $this->session->set_flashdata('failed', 'Duplicate username');
                if ($this->session->position == "Human Resource Admin" || $this->session->position == "HR Staff" || $this->session->position == "Admin" || $this->session->position == "Super Admin") {
                    redirect(base_url() . 'user_add');
                } else {
                    redirect(base_url() . 'log_in');
                }
            } else {

                $this->Page_model->insert_user_2();
                $this->Page_model->insert_at('Add New user', $this->db->insert_id());
                $this->session->set_flashdata('success', ' New User Added.');
                if ($this->session->position == "Human Resource Admin" || $this->session->position == "HR Staff") {
                    redirect(base_url() . 'hrusers');
                } elseif ($this->session->position == "Admin" || $this->session->position == "Super Admin") {
                    redirect(base_url() . 'users');
                } else {
                    redirect(base_url() . 'log_in');
                }
            }
        }
    }

    public function user_edit($param)
    {
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        $this->form_validation->set_rules('position', 'position', 'required');

        if ($this->form_validation->run() == FALSE) {

            $page = "user_update";

            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }

            $data['title'] = "Edit User";

            $data['users'] = $this->Page_model->get_post_except_group_by('users', 'position', 'Super Admin');
            $data['user'] = $this->Page_model->get_single_table_by_id('users', 'id', $param);
            $data['u'] = $this->Common->one_cond_row('users', 'id', $param);
            $data['district'] = $this->Common->no_cond('district');

            $data['id'] = $data['user']['id'];
            $data['username'] = $data['user']['username'];
            $data['position'] = $data['user']['position'];
            $data['fname'] = $data['user']['fname'];
            $data['mname'] = $data['user']['mname'];
            $data['lname'] = $data['user']['lname'];
            $data['se'] = $data['user']['sex'];
            $data['address'] = $data['user']['address'];

            $this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page, $data);
            $this->load->view('templates/footer');
        } else {

            $this->Page_model->update_user();
            $this->Page_model->insert_at('Updated User', $param);
            $this->session->set_flashdata('success', 'Selected user was updated');
            redirect(base_url() . 'users');
        }
    }

    public function eval_user_edit($param)
    {
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        $this->form_validation->set_rules('position', 'position', 'required');

        if ($this->form_validation->run() == FALSE) {

            $page = "user_eval_update";

            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }

            $data['title'] = "Edit User";

            $data['users'] = $this->Page_model->get_post_except_group_by('users', 'position', 'Super Admin');
            $data['user'] = $this->Page_model->get_single_table_by_id('users', 'id', $param);
            $data['u'] = $this->Common->one_cond_row('users', 'id', $param);
            $data['district'] = $this->Common->no_cond('district');

            $data['id'] = $data['user']['id'];
            $data['username'] = $data['user']['username'];
            $data['position'] = $data['user']['position'];
            $data['fname'] = $data['user']['fname'];
            $data['mname'] = $data['user']['mname'];
            $data['lname'] = $data['user']['lname'];
            $data['se'] = $data['user']['sex'];
            $data['address'] = $data['user']['address'];

            $this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page, $data);
            $this->load->view('templates/footer');
        } else {

            $this->Page_model->update_user();
            $this->Page_model->insert_at('Updated User', $param);
            $this->session->set_flashdata('success', 'Selected user was updated');
            redirect(base_url() . 'users');
        }
    }

    public function user_delete($param)
    {
        $this->Page_model->delete('2', 'id', 'users');
        $this->Page_model->insert_at('Deleted user', $param);
        $this->session->set_flashdata('danger', ' User account was deleted.');
        if ($this->session->position == "Human Resource Admin" || $this->session->position == "HR Staff") {
            redirect(base_url() . 'hrusers');
        } else {
            redirect(base_url() . 'users');
        }
    }

    public function personel_del()
    {
        $id = $this->input->get('id');
        $this->db->query("delete from hris_staff where IDNumber='" . $id . "'");
        $this->Page_model->insert_at('Deleted user id number ', $id);
        $this->session->set_flashdata('danger', 'Deleted successfully!');
        redirect(base_url() . 'personnel');
    }

    public function personnel()
    {

        $page = "personel_list";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $data['title'] = "Personnel List";
        $data['pn'] = "Add New";
        $data['link'] = "personel_add";
        $data['personel'] = $this->Page_model->get_limited_col('hris_staff', 'currentStatus,LastName,FirstName,MiddleName,IDNumber,empPosition,Department,csEligibility,tinNo,dateHired,lastAppointmentDate,retYear,serviceLenght,MaritalStatus,BirthDate,age');
        // $data['personel'] = $this->Page_model->get_limited_col('hris_staff', 'FirstName,MiddleName,LastName,IDNumber,empPosition,Department, currentStatus');


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }


    public function personnel_teaching()
    {

        $page = "personel_list_v2";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $data['title'] = "Personnel List";
        $data['pn'] = "Teaching Personnel";
        // $data['link'] = "personel_add";
        $data['personel'] = $this->Page_model->get_limited_col_con('hris_staff', 'FirstName,MiddleName,LastName,IDNumber,empPosition,Department', 'Teaching');


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function personnel_nonteaching()
    {

        $page = "personel_list_v2";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $data['title'] = "Personnel List";
        $data['pn'] = "Non-Teaching Personnel";
        // $data['link'] = "personel_add";
        $data['personel'] = $this->Page_model->get_limited_col_con('hris_staff', 'FirstName,MiddleName,LastName,IDNumber,empPosition,Department', 'Non-Teaching');


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function ntp()
    {

        $page = "personel_list_v2";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $data['title'] = "Personnel List";
        $data['pn'] = "Non-Teaching Personnel";
        // $data['link'] = "personel_add";
        $data['personel'] = $this->Page_model->empGroup('hris_staff', 'FirstName,MiddleName,LastName,IDNumber,empPosition,Department', 'Non-Teaching');


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function tp()
    {

        $page = "personel_list_v2";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $data['title'] = "Personnel List";
        $data['pn'] = "Teaching Personnel";
        // $data['link'] = "personel_add";
        $data['personel'] = $this->Page_model->empGroup('hris_staff', 'FirstName,MiddleName,LastName,IDNumber,empPosition,Department', 'Teaching');


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }


    public function personnel_inactive()
    {

        $page = "personel_list_v2";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $data['title'] = "Personnel List";
        $data['pn'] = "Inactive Personnel";
        // $data['link'] = "personel_add";
        $data['personel'] = $this->Page_model->get_limited_col_con2('hris_staff', 'FirstName,MiddleName,LastName,IDNumber,empPosition,Department', 'Active');


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function school_inactive()
    {
        $page = "personel_list_inactive";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $data['title'] = "Personnel List (INACTIVE)";
        // $data['pn'] = "Inactive Personnel";
        // $data['link'] = "personel_add";
        $data['personel'] = $this->Page_model->school_inactive('hris_staff', 'FirstName,MiddleName,LastName,IDNumber,empPosition,Department, currentStatus', 'Active');


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function school_tr()
    {
        $page = "personel_tr";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $data['title'] = "Personnel List (Teaching Related)";
        // $data['pn'] = "Inactive Personnel";
        // $data['link'] = "personel_add";
        $data['personel'] = $this->Common->two_cond('hris_staff', 'payCat', 'Teaching Related', 'schoolID', $this->session->username);


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function personel_add()
    {

        $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        ', '</div>');
        $this->form_validation->set_rules('IDNumber', 'IDNumber', 'required');

        if ($this->form_validation->run() == FALSE) {

            $page = "personel_new";

            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }

            $last = $this->Common->one_cond_row('count','id',3);
            $number = $last->number;
            $lastThreeDigits = substr($number, -3);
            $lastThreeDigits = (int)$lastThreeDigits + 1;

            if ($lastThreeDigits > 999) {
                $lastThreeDigits = 000;
            }

            $lastThreeDigits = str_pad($lastThreeDigits, 3, "0", STR_PAD_LEFT);

            $data['newIDNumber'] = date('Y').''.date('m').''. $lastThreeDigits;
            $data['ltd'] = $lastThreeDigits;

            $data['users'] = $this->Page_model->get_post_except_group_by('users', 'position', 'Super Admin');
            $data['title'] = "Add New Employee";

            $this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page, $data);
            $this->load->view('templates/footer');
        } else {

            $this->Page_model->insert_profile();
            $this->Page_model->insert_user();
            $this->Page_model->count_update(3,'1'.$this->input->post('ltd'));
            $this->Page_model->insert_at('Added new employee profile id number ', $this->db->insert_id());
            $this->session->set_flashdata('success', ' New employee added successfully.');
            redirect(base_url() . 'personnel');
        }
    }

    public function personnel_profile($param)
    {

        $page = "profile";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $data['title'] = "Profile";
        $data['user'] = $this->Page_model->get_single_table_by_id('hris_staff', 'IDNumber', $param);
        $data['c_user'] = $this->Page_model->get_single_table_by_id('users', 'user_id', $param);
        $data['awards'] = $this->Page_model->get_posts_by_col('hris_awards', 'IDNumber', $param);
        $data['files'] = $this->Page_model->get_posts_by_col('hris_files', 'IDNumber', $param);
        $data['trainings'] = $this->Common->one_cond('hris_trainings', 'IDNumber', $param);
        $data['educ'] = $this->Page_model->get_posts_by_col('hris_educ', 'IDNumber', $param);
        $data['family'] = $this->Page_model->get_posts_by_col('hris_family', 'IDNumber', $param);
        $data['employment'] = $this->Page_model->get_posts_by_col('hris_employment', 'IDNumber', $param);
        $data['ipcr'] = $this->Page_model->get_posts_by_col('hris_ipcr', 'IDNumber', $param);

        $data['training_sum'] = $this->Reg->gettotaltraining_staff('hris_trainings','noHours',$this->uri->segment(2));
        $data['ex_year_sum'] = $this->Reg->gettotaltraining('hris_experience','ny',$this->uri->segment(2));
        $data['ex_month_sum'] = $this->Reg->gettotaltraining('hris_experience','nm',$this->uri->segment(2));
        
        $data['experience'] = $this->Common->one_cond('hris_experience', 'id_number', $param);

        $data['users'] = $this->Page_model->get_row_data('users', 'username', $param);

        $data['c_id'] = $data['c_user']['user_id'];
        $data['image'] = $data['c_user']['image'];

        if ($this->session->userdata('position') === 'Admin') :
            $data['id'] = $data['user']['IDNumber'];
            $data['c_id'] = $data['c_user']['user_id'];

        elseif ($this->session->userdata('position') === 'School') :
            $data['id'] = $data['user']['IDNumber'];
            $data['c_id'] = $data['c_user']['user_id'];

        elseif ($this->session->userdata('position') === 'Super Admin' || $this->session->userdata('position') === 'Human Resource Admin' || $this->session->userdata('position') === 'HR Staff' || $this->session->userdata('position') === 'asds') :
            $data['id'] = $data['user']['IDNumber'];
            $data['c_id'] = $data['c_user']['user_id'];

        elseif ($this->session->userdata('position') === 'Staff') :
            $data['id'] = $data['user']['IDNumber'];
            $data['c_id'] = $data['c_user']['user_id'];
        elseif ($this->session->userdata('position') === 'user') :
            $data['id'] = $this->session->userdata('username');
            $data['c_id'] = $this->session->userdata('username');
        endif;

        $data['FirstName'] = $data['user']['FirstName'];
        $data['MiddleName'] = $data['user']['MiddleName'];
        $data['LastName'] = $data['user']['LastName'];
        $data['NameExtn'] = $data['user']['NameExtn'];
        $data['prefix'] = $data['user']['prefix'];
        $data['jobTitle'] = $data['user']['jobTitle'];
        $data['empPosition'] = $data['user']['empPosition'];
        $data['Department'] = $data['user']['Department'];
        $data['schoolID'] = $data['user']['schoolID'];
        $data['MaritalStatus'] = $data['user']['MaritalStatus'];
        $data['empStatus'] = $data['user']['empStatus'];
        $data['BirthDate'] = $data['user']['BirthDate'];
        $data['BirthPlace'] = $data['user']['BirthPlace'];
        $data['Sex'] = $data['user']['Sex'];
        $data['height'] = $data['user']['height'];
        $data['weight'] = $data['user']['weight'];
        $data['bloodType'] = $data['user']['bloodType'];
        $data['gsis'] = $data['user']['gsis'];
        $data['pagibig'] = $data['user']['pagibig'];
        $data['philHealth'] = $data['user']['philHealth'];
        $data['sssNo'] = $data['user']['sssNo'];
        $data['tinNo'] = $data['user']['tinNo'];
        $data['bnk_acct'] = $data['user']['bnk_acct'];
        $data['resHouseNo'] = $data['user']['resHouseNo'];
        $data['resStreet'] = $data['user']['resStreet'];
        $data['resVillage'] = $data['user']['resVillage'];
        $data['resBarangay'] = $data['user']['resBarangay'];
        $data['resCity'] = $data['user']['resCity'];
        $data['resProvince'] = $data['user']['resProvince'];
        $data['resZipCode'] = $data['user']['resZipCode'];
        $data['perHouseNo'] = $data['user']['perHouseNo'];
        $data['perStreet'] = $data['user']['perStreet'];
        $data['perVillage'] = $data['user']['perVillage'];
        $data['perBarangay'] = $data['user']['perBarangay'];
        $data['perCity'] = $data['user']['perCity'];
        $data['perProvince'] = $data['user']['perProvince'];
        $data['perZipCode'] = $data['user']['perZipCode'];
        $data['empTelNo'] = $data['user']['empTelNo'];
        $data['empMobile'] = $data['user']['empMobile'];
        $data['empEmail'] = $data['user']['empEmail'];
        $data['settingsID'] = $data['user']['settingsID'];
        $data['pronoun1'] = $data['user']['pronoun1'];
        $data['pronoun2'] = $data['user']['pronoun2'];
        $data['age'] = $data['user']['age'];
        $data['dateHired'] = $data['user']['dateHired'];
        $data['retirement'] = $data['user']['retirement'];
        $data['retYear'] = $data['user']['retYear'];
        $data['agencyCode'] = $data['user']['agencyCode'];
        $data['citizenship'] = $data['user']['citizenship'];
        $data['dualCitizenship'] = $data['user']['dualCitizenship'];
        $data['citizenshipType'] = $data['user']['citizenshipType'];
        $data['citizenshipCountry'] = $data['user']['citizenshipCountry'];
        $data['contactName'] = $data['user']['contactName'];
        $data['contactRel'] = $data['user']['contactRel'];
        $data['contactEmail'] = $data['user']['contactEmail'];
        $data['contactNo'] = $data['user']['contactNo'];
        $data['contactAddress'] = $data['user']['contactAddress'];
        $data['fb'] = $data['user']['fb'];
        $data['skype'] = $data['user']['skype'];
        $data['stationCode'] = $data['user']['staCode'];
        $data['umid'] = $data['user']['umid'];
        $data['csEligibility'] = $data['user']['csEligibility'];
        $data['csLevel'] = $data['user']['csLevel'];
        $data['currentStatus'] = $data['user']['currentStatus'];
        $data['YearsAsJO'] = $data['user']['YearsAsJO'];
        $data['workNature1'] = $data['user']['workNature1'];
        $data['workNature2'] = $data['user']['workNature2'];
        $data['employeeNo'] = $data['user']['employeeNo'];
        $data['itemNo'] = $data['user']['itemNo'];
        $data['sgNo'] = $data['user']['sgNo'];
        $data['authAnSalary'] = $data['user']['authAnSalary'];
        $data['actualSalary'] = $data['user']['actualSalary'];
        $data['stepNo'] = $data['user']['stepNo'];
        $data['origAppointmentDate'] = $data['user']['origAppointmentDate'];
        $data['lastAppointmentDate'] = $data['user']['lastAppointmentDate'];
        $data['serviceLenght'] = $data['user']['serviceLenght'];
        $data['payGroup'] = $data['user']['payGroup'];
        $data['payCat'] = $data['user']['payCat'];
        $data['workStat'] = $data['user']['workStat'];
        $data['staCode'] = $data['user']['staCode'];
        $data['lastUpdate'] = $data['user']['lastUpdate'];
        $data['updatedBy'] = $data['user']['updatedBy'];



        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/modal');
        $this->load->view('templates/footer');
    }

    public function del_ipcr($param)
    {
        $result['img'] = $this->Page_model->get_single_table_by_id("hris_ipcr", 'id', $param);
        $filename = $result['img']['fileName'];
        $id = $result['img']['IDNumber'];
        $this->Page_model->delete_group($param, $filename, 'ipcr', "hris_ipcr");
        $this->Page_model->insert_at('Delete uploaded IPCR. ', $param);
        $this->session->set_flashdata('danger', ' IPCR deleted successfully!');
        redirect(base_url() . 'personnel_profile/' . $id);
    }

    public function del_sip($param)
    {
        $result['img'] = $this->Page_model->get_single_table_by_id("school_imp_plans", 'id', $param);
        $filename = $result['img']['fileAttachment'];
        $id = $result['img']['id'];
        $this->Page_model->delete_group($param, $filename, 'sip_files', "school_imp_plans");
        // $this->Page_model->insert_at('Delete uploaded IPCR. ' . $param);
        $this->session->set_flashdata('danger', ' Deleted successfully!');
        redirect(base_url() . 'page/view_sip/');
    }


    public function del_201($param)
    {
        $result['img'] = $this->Page_model->get_single_table_by_id("hris_files", 'id', $param);
        $filename = $result['img']['fileName'];
        $id = $result['img']['IDNumber'];
        $this->Page_model->delete_group($param, $filename, '201files', "hris_files");
        $this->Page_model->insert_at('Deleted 201 Files. ', $param);
        $this->session->set_flashdata('danger', ' 201 File deleted successfully!');
        redirect(base_url() . 'personnel_profile/' . $id);
    }

    public function delete_employment()
    {
        $empID = $this->input->get('empID');
        $id = $this->input->get('id');
        $this->db->query("delete  from hris_employment where empID='" . $empID . "'");
        $this->session->set_flashdata('success', 'Deleted successfully!');
        redirect(base_url() . 'personnel_profile/' . $id);
    }

    public function delete_awards()
    {
        $awardsID = $this->input->get('awardsID');
        $id = $this->input->get('id');
        $this->db->query("delete  from hris_awards where id='" . $awardsID . "'");
        $this->session->set_flashdata('success', 'Deleted successfully!');
        redirect(base_url() . 'personnel_profile/' . $id);
    }

    public function delete_trainings()
    {
        $trainingID = $this->input->get('trainingID');
        $id = $this->input->get('id');
        $this->db->query("delete  from hris_trainings where trainingID='" . $trainingID . "'");
        $this->session->set_flashdata('success', 'Deleted successfully!');
        redirect(base_url() . 'personnel_profile/' . $id);
    }

    public function delete_education()
    {
        $educID = $this->input->get('educID');
        $id = $this->input->get('id');
        $this->db->query("delete  from hris_educ where educID='" . $educID . "'");
        $this->session->set_flashdata('success', 'Deleted successfully!');
        redirect(base_url() . 'personnel_profile/' . $id);
    }

    public function delete_family()
    {
        $famID = $this->input->get('famID');
        $id = $this->input->get('id');
        $this->db->query("delete  from hris_family where famID='" . $famID . "'");
        $this->session->set_flashdata('success', 'Deleted successfully!');
        redirect(base_url() . 'personnel_profile/' . $id);
    }

    public function twoOonefiles()
    {
        $config['allowed_types'] = 'jpg|pdf';
        $config['upload_path'] = './uploads/201files/';
        $this->load->library('upload', $config);

        if ($this->upload->do_upload('attachment')) {
            //$file = $this->upload->data();
            $id = $this->input->post('id');

            $this->Page_model->insert_201files();
            $this->Page_model->insert_at('Add new 201 file', $this->db->insert_id());
            $this->session->set_flashdata('success', '1 file uploaded successfully!');
            redirect(base_url() . 'personnel_profile/' . $id);
        } else {
            print_r($this->upload->display_errors());
        }
    }


    public function sip()
    {
        $config['allowed_types'] = 'pdf';
        $config['upload_path'] = './uploads/sip_files/';
        $this->load->library('upload', $config);

        if ($this->upload->do_upload('attachment')) {
            //$file = $this->upload->data();

            $this->Page_model->insert_sip();
            // $this->Page_model->insert_at('Add new 201 file,  id number ' . $this->db->insert_id());
            $this->session->set_flashdata('success', '1 file uploaded successfully!');
            redirect(base_url() . 'page/view_sip');
        } else {
            print_r($this->upload->display_errors());
        }
    }

    public function ipcr()
    {
        $config['allowed_types'] = 'pdf';
        $config['upload_path'] = './uploads/ipcr/';
        $this->load->library('upload', $config);

        if ($this->upload->do_upload('fileName')) {
            //$file = $this->upload->data();
            $id = $this->input->post('id');

            $this->Page_model->insert_ipcr();
            $this->Page_model->insert_at('Add new IPCR ', $this->db->insert_id());
            $this->session->set_flashdata('success', '1 file uploaded successfully!');
            redirect(base_url() . 'personnel_profile/' . $id);
        } else {
            print_r($this->upload->display_errors());
        }
    }

    public function awards()
    {
        $id = $this->input->post('id');

        $this->Page_model->insert_awards();
        $this->session->set_flashdata('success', 'One record added successfully!');
        redirect(base_url() . 'personnel_profile/' . $id);
    }

    public function family()
    {
        $id = $this->input->post('id');

        $this->Page_model->insert_family();
        $this->session->set_flashdata('success', 'One record added successfully!');
        redirect(base_url() . 'personnel_profile/' . $id);
    }

    public function trainings()
    {
        // $id = $this->input->post('id');

        // $this->Page_model->insert_trainings();
        // $this->session->set_flashdata('success', 'One record added successfully!');
        // redirect(base_url() . 'personnel_profile/' . $id);

        $config['allowed_types'] = 'pdf';
			$config['upload_path'] = './uploads/trainings_staff';
			$new_name = $this->input->post('id') . '-'. time() . $_FILES["file"]['name'];
			$config['file_name'] = $new_name;
			$this->load->library('upload', $config);

			if ($this->upload->do_upload('file')) {
				$this->Page_model->insert_trainings();
				$this->session->set_flashdata('success', 'Successfully Added.');
				redirect($_SERVER['HTTP_REFERER'] . '#trainings');
			} else {
				$this->session->set_flashdata('danger', $this->upload->display_errors());
				redirect($_SERVER['HTTP_REFERER'] . '#trainings');
			}
    }

    public function employment()
    {
        $id = $this->input->post('id');

        $this->Page_model->insert_employment();
        $this->session->set_flashdata('success', 'One record added successfully!');
        redirect(base_url() . 'personnel_profile/' . $id);
    }

    public function education()
    {
        $id = $this->input->post('id');

        $this->Page_model->insert_education();
        $this->session->set_flashdata('success', 'One record added successfully!');
        redirect(base_url() . 'personnel_profile/' . $id);
    }
    public function plantilla()
    {

        $page = "plantilla";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $data['title'] = "Plantilla Positions";
        $data['pn'] = "Add New";
        $data['link'] = "plantilla_add";
        $data['page'] = $this->Page_model->get_posts('hris_plantilla');

        $data['status'] = $this->Page_model->group_by_status('hris_plantilla');

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/modal_com');
        $this->load->view('templates/footer');
    }
    public function items($param)
    {

        $page = "plantilla_items";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $data['title'] = "Plantilla List";
        $data['pn'] = "Add New";
        $data['link'] = "plantilla_add";
        $data['p'] = $param;
        $data['page'] = $this->Page_model->get_posts_by_col('hris_plantilla', 'status', $param);



        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/modal_com');
        $this->load->view('templates/footer');
    }

    public function mis_logs()
    {

        $page = "mis_logs";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $data['title'] = "MIS Logs";
        $data['pn'] = "Add New";
        $data['link'] = "plantilla_add";
        // $data['p'] = $param;
        $year = date('Y');
        $data['page'] = $this->Common->one_cond_between('mis_logs', 'username', $this->input->post('username'), 'transDate', $year . '-01-01', $year . '-12-31');



        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/modal_com');
        $this->load->view('templates/footer');
    }

    public function plantilla_add()
    {

        $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        ', '</div>');
        $this->form_validation->set_rules('pGroup', 'Plantilla Group', 'required');
        //$this->form_validation->set_rules('termPeriodto', 'Term Period To', 'required');

        if ($this->form_validation->run() == FALSE) {

            $page = "plantilla_new";

            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }

            $data['pgroup'] = $this->Page_model->get_post_group_by('hris_plantilla', 'pGroup');
            $data['itemNo'] = $this->Page_model->get_post_group_by('hris_plantilla', 'itemNo');
            $data['itemPosition'] = $this->Page_model->get_post_group_by('hris_plantilla', 'itemPosition');
            $data['title'] = "Add New User";
            $data['title'] = "Add New User";

            $this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page, $data);
            $this->load->view('templates/footer');
        } else {
            $check = $this->Common->one_cond_count_row('hris_plantilla','itemNo',$this->input->post('itemNo'));
            if($check->num_rows() >= 1){
                $this->session->set_flashdata('danger', 'Item number already exists.');
                redirect(base_url() . 'plantilla');
            }else{
                $this->Page_model->insert_plantilla();
                $this->Page_model->insert_at('Add New user ', $this->db->insert_id());
                $this->session->set_flashdata('success', ' New User Save');
                redirect(base_url() . 'plantilla');
            }

        }
    }
    public function plantilla_update($param)
    {

        $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        ', '</div>');
        $this->form_validation->set_rules('pGroup', 'Plantilla Group', 'required');

        if ($this->form_validation->run() == FALSE) {

            $page = "plantilla_edit";


            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }

            $data['title'] = "Plantilla Update";
            $data['plantilla'] = $this->Page_model->get_single_table_by_id('hris_plantilla', 'id', $param);
            $data['id'] = $data['plantilla']['id'];
            $data['pGroup'] = $data['plantilla']['pGroup'];
            $data['itemNo'] = $data['plantilla']['itemNo'];
            $data['itemPosition'] = $data['plantilla']['itemPosition'];
            $data['sg'] = $data['plantilla']['sg'];
            $data['step'] = $data['plantilla']['step'];
            $data['authAnnualSalary'] = $data['plantilla']['authAnnualSalary'];

            $this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page, $data);
            $this->load->view('templates/footer');
        } else {
            $this->Page_model->update_plantilla();
            $last_id = $this->input->post('id');
            $this->Page_model->insert_at('Add New Plantilla', $last_id);
            $this->session->set_flashdata('success', 'Succesfully Updated.');
            redirect(base_url() . 'plantilla');
        }
    }
    public function plantilla_del($param)
    {
        $this->Page_model->delete('2', 'id', 'hris_plantilla');
        $this->Page_model->insert_at('Delete Plantilla', $param);
        $this->session->set_flashdata('danger', ' Plantilla deleted');
        redirect(base_url() . 'plantilla');
    }



    public function position()
    {

        $page = "ap";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $data['title'] = "Unfilled Items";
        if ($this->session->position != "Admin") {
            $data['page'] = $this->Page_model->get_table_by_col_group_by_param_by_two('hris_plantilla', 'status', 1, 'view_status', 1, 'itemPosition');
        } else {
            $data['page'] = $this->Page_model->get_table_by_col_group_by('hris_plantilla', 'status', 1, 'itemPosition');
        }
        $data['pos'] = $this->Page_model->position_count();

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/modal_apply');
        $this->load->view('templates/footer');
    }
    public function view_status_unview($param)
    {
        $data['page'] = $this->Page_model->get_single_table_by_id('hris_plantilla', 'id', $param);
        $item = $data['page']['itemPosition'];

        $this->Page_model->update_view_status_unview($param, $item);
        $this->Page_model->insert_at('Update Plantilla', $param);
        $this->session->set_flashdata('success', ' View Status Updated');
        redirect(base_url() . 'position');
    }
    public function view_status_view($param)
    {
        $data['page'] = $this->Page_model->get_single_table_by_id('hris_plantilla', 'id', $param);
        $item = $data['page']['itemPosition'];

        $this->Page_model->update_view_status_view($param, $item);
        $this->Page_model->insert_at('Update Plantilla', $param);
        $this->session->set_flashdata('success', ' View Status Updated');
        redirect(base_url() . 'position');
    }

    public function reg()
    {

        $page = "registered";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $data['title'] = "Registered Applicant List";
        $data['pn'] = "New Applicant";
        $data['link'] = "reg_add";
        $data['registered'] = $this->Page_model->get_post_except('hris_registration', 'status', 1);

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/modal_com');
        $this->load->view('templates/footer');
    }
    public function register()
    {

        $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        ', '</div>');
        $this->form_validation->set_rules('fname', 'First Name', 'required');
        //$this->form_validation->set_rules('termPeriodto', 'Term Period To', 'required');

        if ($this->form_validation->run() == FALSE) {

            $page = "registration_new";

            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }
            $data['title'] = "Register";
            $data['count'] = $this->Page_model->get_single_table_by_id('count', 'id', 1);
            $data['per'] = $data['count']['number'];
            $data['per_id'] = $data['count']['id'];

            $this->load->view('pages/' . $page, $data);
        } else {

            $this->Page_model->insert_reg();
            $this->Page_model->insert_at('Add New user', $this->db->insert_id());
            $this->Page_model->update_count();
            $this->Page_model->insert_reg_user();

            //Email Notification
            $email = $this->input->post('email');
            $fname = $this->input->post('fname');
            $this->load->config('email');
            $this->load->library('email');
            $mail_message = 'Dear ' . $fname . ',' . "\r\n";
            $mail_message .= '<br><br>Your account has been created successfully.' . "\r\n";
            // $mail_message .= '<br>Course: <b>' . $Course . '</b>' ."\r\n";
            // $mail_message .= '<br>Major: <b>' . $Major . '</b>'."\r\n";
            // $mail_message .= '<br>Year Level: <b>' . $YearLevel . '</b>'."\r\n";
            // $mail_message .= '<br>Sem/SY: <b>' . $Semester . ', '. $SY .'</b>'."\r\n";
            // $mail_message .= '<br>Status: <b>For Validation</b>'."\r\n";

            // $mail_message .= '<br><br>You will be notified once validated.' ."\r\n";
            $mail_message .= '<br><br>Thanks & Regards,';
            $mail_message .= '<br>HRIS - Online';

            $this->email->from('no-reply@lxeinfotechsolutions.com', 'HRIS')
                ->to($email)
                ->subject('Account Created')
                ->message($mail_message);
            $this->email->send();


            $this->session->set_flashdata('danger', 'fdasdf fadfas');
            redirect(base_url());
        }
    }

    public function school($param)
    {

        $id = $this->session->userdata('username');
        $result['data'] = $this->Page_model->schoolInfo('schools', $id);
        $this->load->view('dashboard_school', $result);
    }

    public function registered_profile($param)
    {

        $this->Page_model->check_ownership($param);

        $page = "profile_reg";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }


        $res['page'] = $this->Page_model->get_single_row_by_id('settings', 'id', 2);
        $data['title'] = "Profile";
        $data['a_user'] = $this->Common->one_cond_row('hris_applicant', 'id', $param);
        $data['user'] = $this->Common->one_cond_row('users', 'user_id', $param);

        $profileAppID = (int) $this->input->get('appID');
        $profileJobID = (int) $this->input->get('jobID');
        $profileApplication = null;

        if ($profileAppID > 0 || $profileJobID > 0) {
            $this->db->from('hris_applications');
            $this->db->where('applicant_id', $param);

            if ($profileAppID > 0) {
                $this->db->where('appID', $profileAppID);
            }

            if ($profileJobID > 0) {
                $this->db->where('jobID', $profileJobID);
            }

            $profileApplication = $this->db
                ->order_by('appID', 'DESC')
                ->limit(1)
                ->get()
                ->row();
        }

        $data['profile_application'] = $profileApplication;
        $data['profile_upload_allowed'] = !empty($profileApplication)
            ? (int) $profileApplication->stat === 0
            : (int) ($data['a_user']->stat ?? 0) === 0;
        $data['profile_lock_context_query'] = !empty($profileApplication)
            ? '?jobID=' . (int) $profileApplication->jobID . '&appID=' . (int) $profileApplication->appID
            : '';

        $data['awards'] = $this->Page_model->get_posts_by_col('hris_awards', 'IDNumber', $param);
        $data['files'] = $this->Page_model->get_posts_by_col('hris_files', 'IDNumber', $param);
        $data['trainings'] = $this->Page_model->get_posts_by_col('hris_trainings', 'IDNumber', $param);
        $data['educ'] = $this->Page_model->get_posts_by_col('hris_educ', 'IDNumber', $param);
        $data['family'] = $this->Page_model->get_posts_by_col('hris_family', 'IDNumber', $param);
        $data['employment'] = $this->Page_model->get_posts_by_col('hris_employment', 'IDNumber', $param);
        $data['ipcr'] = $this->Page_model->get_posts_by_col('hris_ipcr', 'IDNumber', $param);

        $data['training'] = $this->Common->one_cond('hris_trainings', 'IDNumber', $param);
        $data['experience'] = $this->Common->one_cond('hris_experience', 'id_number', $param);

        $data['training_sum'] = $this->Reg->gettotaltraining_staff('hris_trainings','noHours',$this->uri->segment(2));
        $data['ex_year_sum'] = $this->Reg->gettotaltraining('hris_experience','ny',$this->uri->segment(2));
        $data['ex_month_sum'] = $this->Reg->gettotaltraining('hris_experience','nm',$this->uri->segment(2));

        // Allow assigned evaluators to manage certain profile sections (e.g., trainings)
        $isAssignedEvaluator = false;
        $pos = $this->session->position ?? null;
        if (in_array($pos, ['Evaluator', 'rater', 'raters'], true)) {
            $raterId = (int) ($this->session->id ?? 0);

            $applicantKeys = [];
            if (!empty($data['a_user']->id)) {
                $applicantKeys[] = (string) $data['a_user']->id;
            }
            if (!empty($data['a_user']->record_no)) {
                $applicantKeys[] = (string) $data['a_user']->record_no;
            }

            if ($raterId && !empty($applicantKeys)) {
                $this->db->where('rater_user_id', $raterId);
                $this->db->where_in('applicant_id', $applicantKeys);
                $isAssignedEvaluator = $this->db->count_all_results('hris_rater_assignments') > 0;
            }
        }

        $data['isAssignedEvaluator'] = $isAssignedEvaluator;
        




        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }
    public function apply()
    {
        $this->Page_model->apply_insert();
        $this->Page_model->insert_at('Apply', $this->db->insert_id());
        $this->session->set_flashdata('success', ' Successfuly Applied');
        redirect(base_url() . 'registered_profile/' . $this->session->c_id);
    }
    public function applicant()
    {

        $page = "ap_application";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $data['title'] = "Applicant";
        $data['page'] = $this->Page_model->get_posts_by_col('applied', 'status', 0);

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/modal_com');
        $this->load->view('templates/footer');
    }
    public function ap_delete($param)
    {
        $this->Page_model->delete('2', 'id', 'applied');
        $this->Page_model->insert_at('Delete application', $this->db->insert_id());
        $this->session->set_flashdata('danger', ' Application was deleted');
        redirect(base_url() . 'applicant');
    }






    /* to delete */
    public function pass()
    {
        $this->Page_model->pass();
        redirect(base_url());
    }

    public function user_pass($param)
    {
        $this->Page_model->user_pass_change($param);
        $this->session->set_flashdata('success', 'Successfully Updated.');
        redirect(base_url() . "users");
    }

    public function log_in()
    {

        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        $this->form_validation->set_rules('username', 'username', 'required');
        $this->form_validation->set_rules('password', 'uassword', 'required');

        if ($this->form_validation->run() == FALSE) {

            $page = "login";

            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }

            $data['page'] = $this->Page_model->get_single_row_by_id('settings', 'id', 1);
            $this->load->view('pages/' . $page, $data);
        } else {

            $user_id = $this->Page_model->login();



            if ($user_id) {

                $user_data = array(
                    'username' => $user_id['username'],
                    'user' => $user_id['fname'] . ' ' . $user_id['mname'] . ' ' . $user_id['lname'],
                    'position' => $user_id['position'],
                    //'office' => $user_id['office'],
                    'sex' => $user_id['sex'],
                    'image' => $user_id['image'],
                    'id' => $user_id['id'],
                    'sp' => $user_id['sp'],
                    'c_id' => $user_id['user_id'],
                    'eg' => $user_id['egroup'],
                    'user_id' => $user_id['user_id'],
                    'id' => $user_id['id'],
                    'd_id' => $user_id['d_id'],
                    'logged_in' => true

                );

                $ren_guapo = $this->Common->one_cond_row_select('mis_settings','settingsID,fy','settingsID','1');

                $this->session->set_userdata($user_data);
                $this->session->set_userdata('cur_fy', date('Y'));
                $this->session->set_userdata('cur_month', date('m'));
                //$this->session->set_userdata('cur_fy', date('Y'));
                //$current_sy = date('Y').'-'.date('Y')+1;
                $this->session->set_userdata('cur_sy', $ren_guapo->fy);
                

                $logStat = 'success';
                $logType = 'login';
                $this->Page_model->insert_logs($logStat, $logType);

                if ($this->session->sp == 0) {
                    if ($this->session->position == "user") {
                        redirect(base_url() . 'Pages/view_employee');
                        $this->Page_model->insert_logs();
                    } elseif ($this->session->position === "reg") {
                        redirect(base_url() . 'registered_profile/' . $this->session->c_id);
                    } elseif ($this->session->position === "School") {
                        redirect(base_url() . 'Page/schoolDashboard/');
                    } elseif ($this->session->position === "SHNS") {
                        redirect(base_url() . 'Page/Health/');
                    } elseif ($this->session->position === "Staff") {
                        redirect(base_url());
                    } elseif ($this->session->position === "Accountant") {
                        redirect(base_url());
                    } else {
                        redirect(base_url());
                    }
                } else {
                    redirect(base_url());
                }
            } else {
                $this->session->set_flashdata('failed', 'Username/Password not match');
                $logStat = 'failed';
                $this->Page_model->insert_logs_failed($logStat);
                redirect(base_url() . 'log_in');
            }
        }
    }
    public function lock_user_screen()
    {

        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        $this->form_validation->set_rules('password', 'password', 'required');

        if ($this->form_validation->run() == FALSE) {

            $page = "lock_screen";

            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }

            $this->load->view('pages/' . $page);
        } else {

            $user_id = $this->Page_model->lock_screen();

            if ($user_id) {

                $user_data = array(
                    'username' => $user_id['username'],
                    'user' => $user_id['fname'] . ' ' . $user_id['mname'] . ' ' . $user_id['lname'],
                    'position' => $user_id['position'],
                    'office' => $user_id['office'],
                    'image' => $user_id['image'],
                    'id' => $user_id['id'],
                    'logged_in' => true
                );

                $this->session->set_userdata($user_data);
                $this->session->set_flashdata('user_log', 'You are now loged in as '
                    . $this->session->position);
                redirect(base_url());
            } else {
                $this->session->set_flashdata('failed', 'Password not match');
                redirect(base_url() . 'lock_user_screen');
            }
        }
    }

    public function logout()
    {

        $logStat = 'success';
        $logType = 'logout';
        $this->Page_model->insert_logs_out($logStat, $logType);

        $this->session->unset_userdata('id');
        $this->session->unset_userdata('username');
        $this->session->unset_userdata('position');
        $this->session->unset_userdata('office');
        $this->session->unset_userdata('logged_in');
        $this->session->unset_userdata('aip');

        $this->session->set_flashdata('failed', 'You are logged out.');



        redirect(base_url() . 'log_in');
    }
    public function lock()
    {
        $this->session->unset_userdata('id');
        $this->session->unset_userdata('position');
        $this->session->unset_userdata('office');
        $this->session->unset_userdata('logged_in');

        $this->session->set_flashdata('danger', 'You are now in Locked Screen Mode');
        redirect(base_url() . 'lock_user_screen');
    }

    public function new_applicant()
    {


        $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        ', '</div>');
        $this->form_validation->set_rules('fname', 'First Name', 'required');
        $this->form_validation->set_rules('contact', 'Contact', 'required');
        //$this->form_validation->set_rules('termPeriodto', 'Term Period To', 'required');
        $stat = $this->Common->one_cond_row('settings', 'id', 1);
        if($stat->status == 1){
            redirect(base_url());
        }

        if ($this->form_validation->run() == FALSE) {

            $page = "applicant_new";

            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }
            $data['title'] = "Register";
            $data['count'] = $this->Page_model->get_row_data('count', 'id', 1);
            $data['data1'] = $this->PersonnelModel->mis_settings(); // Additional settings data
            $data['provinces'] = $this->SettingsModel->get_provinces();
            $data['cities'] = $this->SettingsModel->get_cities();
            $data['brgy'] = $this->SettingsModel->get_barangays();

            $data['et'] = $this->Common->no_cond('settings_ethnicity');
            $data['re'] = $this->Common->no_cond('settings_religion');

            $data['mis_settings'] = $this->Common->one_cond_row('mis_settings','settingsID',1);


            $this->load->view('templates/header_public');
            $this->load->view('pages/' . $page, $data);
            $this->load->view('templates/footer_public');
        } else {

            $mis_settings = $this->Common->one_cond_row('mis_settings','settingsID',1);

            $recaptcha = $this->input->post('g-recaptcha-response');
            $secret = trim($mis_settings->secret_key);

            $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret}&response={$recaptcha}");
            $responseKeys = json_decode($response, true);

            if (!$responseKeys["success"]) {
                $this->session->set_flashdata('danger', 'reCAPTCHA verification failed. Please try again.');
                redirect('Pages/new_applicant'); 
            }

            $r_no = $this->input->post('record_no');
            $fname = $this->input->post('fname');
            $lname = $this->input->post('lname');
            $mname = $this->input->post('mname');
            $email = $this->input->post('email');
            $bd = $this->input->post('bd');
            $contact = $this->input->post('contact');
            $record_no_check = $this->Page_model->check_single_row_exist('hris_applicant', 'record_no', $r_no);
            $check = $this->Page_model->check_exist('hris_applicant', $fname, $lname,$mname);
            $email_check = $this->Page_model->check_single_row_exist('hris_applicant', 'empEmail', $email);
            $num_check = $this->Page_model->check_single_row_exist('hris_applicant', 'contactNo', $contact);

            $renren = $this->input->post('renren');
            $ivykate = $this->input->post('ivykate');
            $ivankyle = $this->input->post('ivankyle');
            $ic = $this->input->post('ic');

            if (!empty($renren) || !empty($ivykate) || !empty($ivankyle) || !empty($ic)) {
                $this->session->set_flashdata('danger', 'I Got you');
                redirect(base_url() . 'new_applicant');
            }

            if ($num_check->num_rows() >= 1) {
                $this->session->set_flashdata('danger', 'A Duplicate Contact Number was found.');
                redirect(base_url() . 'new_applicant');
            }

            if ($email_check->num_rows() >= 1) {
                $this->session->set_flashdata('danger', 'Duplicate Email found.');
                redirect(base_url() . 'new_applicant');
            } else {
                if ($check->num_rows() >= 1) {
                    $this->session->set_flashdata('danger', 'Duplicate records found.');
                    redirect(base_url() . 'new_applicant');
                } else {
                    $this->Page_model->insert_application();
                    $id = $this->db->insert_id();
                    $this->Page_model->update_app_count();
                    $this->Page_model->insert_reg_user($id);

                    //Email Notification
                    $this->load->config('email');
                    $this->load->library('email');
                    $mail_message = 'Dear ' . $fname . ',' . "\r\n";
                    $mail_message .= '<br><br>Thank you for signing up!' . "\r\n";
                    $mail_message .= '<br><br>You may now login to the system using <span style="color:red; font-weight:bold;">' . $email . '</span> as your username and <span style="color:red; font-weight:bold;">' . $bd . ' </span> as your password.' . "\r\n";
                    $mail_message .= '<br><br>Thanks & Regards,';
                    $mail_message .= '<br>HRIS - Online';

                    $this->email->from('no-reply@depeddavor.com', 'Human Resource Information System')
                        ->to($email)
                        ->subject('Account Created')
                        ->message($mail_message);
                    $this->email->send();


                    $this->session->set_flashdata('success', 'Your registration has been processed successfully.  Please check your email for the login credentials.');
                    redirect(base_url() . 'log_in');
                }
            }
        }
    }


    public function get_cities_by_province()
    {
        $province = $this->input->post('province');
        $this->db->select('City');
        $this->db->where('Province', $province);
        $this->db->distinct();
        $query = $this->db->get('settings_address');

        $output = '<option value="">Select City/Municipality</option>';
        foreach ($query->result() as $row) {
            $output .= '<option value="' . $row->City . '">' . $row->City . '</option>';
        }
        echo $output;
    }

    public function get_barangays_by_city()
    {
        $city = $this->input->post('city');
        $this->db->select('Brgy');
        $this->db->where('City', $city);
        $this->db->distinct();
        $query = $this->db->get('settings_address');

        $output = '<option value="">Select Barangay</option>';
        foreach ($query->result() as $row) {
            $output .= '<option value="' . $row->Brgy . '">' . $row->Brgy . '</option>';
        }
        echo $output;
    }


    public function profile_reg_delete($param)
    {
        $this->Page_model->delete('3', 'id', 'hris_applicant');
        $id = $this->db->insert_id();
        $this->Page_model->delete('3', 'user_id', 'users');
        $this->Page_model->insert_at('Deleted Applicant', $id);
        $this->session->set_flashdata('danger', ' Applicant account was deleted.');
        redirect(base_url() . 'Page/regApplicants');
    }

    public function del_application($param)
    {
        $this->Page_model->delete('3', 'appID', 'hris_applications');
        $this->Page_model->insert_at('Deleted Application', $this->db->insert_id());
        $this->session->set_flashdata('danger', ' Applications was deleted.');
        redirect(base_url() . 'Page/myApplications?id=' . $this->input->get('ee'));
    }

    public function del_application_school($param)
    {
        $this->Page_model->delete('3', 'appID', 'hris_applications');
        $this->Page_model->insert_at('Deleted Application', $this->db->insert_id());
        $this->session->set_flashdata('danger', ' Applications was deleted.');
        redirect(base_url() . 'Pages/school_applicant/' . $this->uri->segment(4));
    }

    public function pass_change_in_school()
    {
        $this->Page_model->change_pass_school_emp();
        $this->session->set_flashdata('success', 'Updated successfully.');
        redirect(base_url() . 'Page/employeelist');
    }

    public function pass_change_in_district()
    {
        $this->Page_model->change_pass_school_emp();
        $this->session->set_flashdata('success', 'Updated successfully.');
        redirect(base_url() . 'Page/employee_list/' . $this->uri->segment(4));
    }

    // public function pass_change()
    // {
    //     if ($this->session->position == "Admin" || $this->session->position == "Super Admin" || $this->session->position == "Human Resource Admin" || $this->session->position == "HR Staff" || $this->session->position == "doceval") {
    //         $this->Page_model->change_pass_admin();
    //     } elseif ($this->session->position == "School") {

    //         $this->Page_model->change_pass_admin();
    //     } else {
    //         $this->Page_model->change_pass();
    //         $this->session->set_flashdata('success', 'Updated successfully.');
    //         //redirect('Pages/users');
    //     }
    //     $this->Page_model->insert_at('Change Applicant ', $this->db->insert_id());
    //     $this->session->set_flashdata('success', 'Password successfully changed.');
    //     $id = $this->input->post('id');

    //     if ($this->session->userdata('position') === 'Admin' || $this->session->position == 'Super Admin') :
    //         redirect(base_url() . 'users');
    //     elseif ($this->session->userdata('position') === 'School') :
    //         redirect(base_url() . 'Users/users_sub');
    //     elseif ($this->session->userdata('position') === 'reg') :
    //         redirect(base_url() . 'registered_profile/' . $id);
    //     elseif ($this->session->userdata('position') === 'user') :
    //         redirect('Pages/view_employee');
    //     elseif ($this->session->position === 'Evaluator' || $this->session->position == "Human Resource Admin" || $this->session->position == "HR Staff" || $this->session->position == "District") :
    //         redirect();
    //     endif;
    // }





    public function pass_change()
{
    if ($this->session->position == "Admin" || 
        $this->session->position == "Super Admin" || 
        $this->session->position == "Human Resource Admin" || 
        $this->session->position == "HR Staff" || 
        $this->session->position == "asds" || 
        $this->session->position == "doceval") { 
        
        $this->Page_model->change_pass_admin();
        redirect(base_url());
    
    } elseif ($this->session->position == "SHNS") {
        // SHNS-specific password change logic
        $this->Page_model->change_pass_admin();
        $this->session->set_flashdata('success', 'Password successfully changed.');
    
    } elseif ($this->session->position == "School") {
        $this->Page_model->change_pass_admin();
    
    } else {
        $this->Page_model->change_pass();
        $this->session->set_flashdata('success', 'Updated successfully.');
    }

    $this->Page_model->insert_at('Change Applicant ', $this->db->insert_id());

    $id = $this->input->post('id');

    if ($this->session->userdata('position') === 'Admin' || $this->session->position == 'Super Admin') :
        $this->session->set_flashdata('success', 'Password successfully changed.');
        redirect(base_url() . 'users');

    elseif ($this->session->userdata('position') === 'School') :
        $this->session->set_flashdata('success', 'Password successfully changed.');
        redirect(base_url() . 'Users/users_sub');

    elseif ($this->session->userdata('position') === 'reg') :
        $this->session->set_flashdata('success', 'Password successfully changed.');
        redirect(base_url() . 'registered_profile/' . $id);

    elseif ($this->session->userdata('position') === 'user') :
        $this->session->set_flashdata('success', 'Password successfully changed.');
        redirect('Pages/view_employee');

    elseif ($this->session->position === "SHNS") :
        $this->session->set_flashdata('success', 'Password successfully changed.');
        redirect(base_url() . 'Page/Health'); // Change to the correct SHNS redirect page

    elseif ($this->session->position === 'Secretariat') :
        $this->session->set_flashdata('success', 'Password successfully changed.');
        redirect(base_url());

    elseif ($this->session->position === 'Evaluator' || 
            $this->session->position == "Human Resource Admin" || 
            $this->session->position == "HR Staff" || 
            $this->session->position == "District") :
        redirect();

    endif;
}

    public function other_settings()
    {

        $page = "settings_other";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $data['title'] = "Other Settings";
        $data['page'] = $this->Page_model->get_all_posts('settings');


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function update_settings()
    {
        $this->Page_model->close_open('3', 'id', 'settings', 'status', '4');
        $this->Page_model->insert_at('Update Other Settings', $this->db->insert_id());
        $this->session->set_flashdata('success', 'Successfully Updated');
        redirect(base_url() . 'Pages/other_settings');
    }

    public function profile_reg_edit($param)
    {

        $this->Page_model->check_ownership($param);

        $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        ', '</div>');
        $this->form_validation->set_rules('FirstName', 'First Name', 'required');
        //$this->form_validation->set_rules('termPeriodto', 'Term Period To', 'required');

        if ($this->form_validation->run() == FALSE) {

            $page = "profile_reg_update";

            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }

            $data['title'] = "Applicant";
            $data['app'] = $this->Page_model->get_single_row_by_id('hris_applicant', 'id', $param);
            $data['et'] = $this->Common->no_cond('settings_ethnicity');
            $data['re'] = $this->Common->no_cond('settings_religion');

            $this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page, $data);
            $this->load->view('templates/modal_com');
            $this->load->view('templates/footer');
        } else {
            $this->Page_model->update_profile_reg();
            $id = $this->db->insert_id();
            $this->Reg->ai_sex_update();
            $this->Page_model->insert_at('Profile registration successfully ', $id);

            $this->Reg->educ_jhss_update();
            $this->Reg->educ_shss_update();

            $this->session->set_flashdata('success', 'Updated successfully');
            redirect(base_url() . 'Pages/registered_profile/' . $param);
        }
    }

    public function employee_edit($param)
    {

        if ($this->session->userdata('position') === 'Admin') {
        } elseif ($this->session->userdata('position') === 'Super Admin') {
        } elseif ($this->session->userdata('position') === 'School') {
        } elseif ($this->session->userdata('position') === 'Staff') {
        } else {
            $this->Page_model->check_ownership($param);
        }

        $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        ', '</div>');
        $this->form_validation->set_rules('FirstName', 'First Name', 'required');
        //$this->form_validation->set_rules('termPeriodto', 'Term Period To', 'required');

        if ($this->form_validation->run() == FALSE) {

            $page = "employee_update";

            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }

            $data['title'] = "Employee Update";
            $data['app'] = $this->Page_model->get_single_row_by_id('hris_staff', 'IDNumber', $param);
            $data['position'] = $this->Common->no_cond_order_by('hris_positions','title','asc');

            $data['et'] = $this->Common->no_cond('settings_ethnicity');
            $data['re'] = $this->Common->no_cond('settings_religion');

            $this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page, $data);
            $this->load->view('templates/modal_com');
            $this->load->view('templates/footer');
        } else {
            if ($this->session->position == "user") {
                $this->Page_model->update_staff();
            } else {
                $this->Page_model->update_employee();
                $this->Page_model->update_user_account();
            }
            $this->Page_model->insert_at('Employee information updated successfully ', $this->input->post('id'));
            $this->session->set_flashdata('success', 'Updated successfully.');

            if ($this->session->position != 'user') {
                redirect(base_url() . 'Pages/personnel_profile/' . $this->input->post('empNo'));
            } else {
                redirect(base_url() . 'Pages/personnel_profile/' . $param);
            }
        }
    }


    public function ies()
    {
        $id = $this->uri->segment(3);
        $appId = $this->uri->segment(4);
        $jobId = $this->uri->segment(5);

        $job = $this->Page_model->get_single_row_by_id('hris_jobvacancy', 'jobID', $jobId);

        if ($job->position == 1) {
            $page = "ies_form";
        } else {
            $page = "ies_form_none";
        }

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }


        //$data['ap'] = $this->Page_model->get_single_row_by_id('hris_applicant', 'id', $id);
        
        if ($job->position == 1) {
            $data['rate'] = $this->Page_model->get_single_row_by_id('hris_applications_rating', 'appID', $appId);
        } else {
            $data['rate'] = $this->Page_model->get_single_row_by_id('hris_rating_none', 'appID', $appId);
        }
        $data['job'] = $this->Page_model->get_single_row_by_id('hris_jobvacancy', 'jobID', $jobId);
        $data['title'] = "INDIVIDUAL EVALUATION SHEET(IES)";

        $this->load->view('pages/' . $page, $data);
    }

    public function ies_promotion()
    {
        $id = $this->uri->segment(3);
        $appId = $this->uri->segment(4);
        $jobId = $this->uri->segment(5);

        $data['job'] = $this->Page_model->get_single_row_by_id('hris_jobvacancy', 'jobID', $jobId);

        $page = "ies_promotion";
       
        $data['ap'] = $this->Page_model->get_single_row_by_id('hris_staff', 'IDNumber', $id);

        $this->Reg->calculate_rating_promotion_ies($appId);

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }


        $data['rate'] = $this->Page_model->get_single_row_by_id('hris_rating_promotion', 'appID', $appId);
       

        
        $data['title'] = "INDIVIDUAL EVALUATION SHEET(IES)";

        $this->load->view('pages/' . $page, $data);
    }




   public function car_rqa()
{
    $page = "car_rqa_form";

    if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
        show_404();
    }

    $jobID = $this->uri->segment(3);
    $data['job'] = $this->Common->one_cond_row_select('hris_jobvacancy','jobID,jobTitle,job_type', 'jobID', $jobID);
    $data['car'] = $this->Page_model->rqa($jobID);
    $data['settings'] = $this->SettingsModel->get_mis_settings();

    $job = $this->Common->one_cond_row_select('hris_jobvacancy','jobID,sy,sign,ttype', 'jobID', $jobID);
    $data['sign'] = $this->get_rqa_sign($job);

    // Preload all applicants and staff into memory
    $applicants = $this->Page_model->get_all_applicants_indexed();
    $staff = $this->Page_model->get_all_staff_indexed();
    $data['applicants'] = $applicants;
    $data['staff'] = $staff;

    if($job->ttype == 1){
        $data['title'] = "COMPARATIVE ASSESSMENT RESULT FOR EXPANDED RECLASSIFICATION (CAReER)";
    }else{
        $data['title'] = "COMPARATIVE ASSESSMENT RESULT - REGISTRY OF QUALIFIED APPLICANTS (CAR - RQA)";
    }
    

    $this->render_rqa_report($page, $data, $this->rqa_excel_filename('car-rqa', $jobID));
}

public function car_rqa_promotion()
    {
        ob_start(); // Begin output buffering

        $page = "car_rqa_form_promotion";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $jobID = $this->uri->segment(3);
        $data['job'] = $this->Common->one_cond_row_select('hris_jobvacancy','jobID,jobTitle,job_type,sy,sign,ttype', 'jobID', $jobID);
        $data['car'] = $this->Page_model->rqa_promotion($jobID);
        $data['settings'] = $this->SettingsModel->get_mis_settings();

        //$job = $this->Common->one_cond_row_select('hris_jobvacancy','jobID,sy,sign,ttype', 'jobID', $jobID);
        $data['sign'] = $this->get_rqa_sign($data['job']);

        // Preload all applicants and staff into memory
        $applicants = $this->Page_model->get_all_applicants_indexed();
        $staff = $this->Page_model->get_all_staff_indexed();
        $data['applicants'] = $applicants;
        $data['staff'] = $staff;

        $data['title'] = "COMPARATIVE ASSESSMENT RESULT FOR EXPANDED RECLASSIFICATION (CAReER)";
       
        

        $this->load->view('pages/' . $page, $data);

        ob_end_flush(); // Output all content
    }






    public function car_rqa_complete()
{
    $page = "car_rqa_complete";

    if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
        show_404();
    }

    $jobID = $this->uri->segment(3);
    $data['job'] = $this->Common->one_cond_row_select('hris_jobvacancy','jobID,jobTitle,job_type', 'jobID', $jobID);
    $data['car'] = $this->Page_model->rqa($jobID);
    $data['settings'] = $this->SettingsModel->get_mis_settings();

    $job = $this->Common->one_cond_row_select('hris_jobvacancy','jobID,sy,sign', 'jobID', $jobID);
    $data['sign'] = $this->get_rqa_sign($job);

    $data['applicants'] = $this->Page_model->get_all_applicants_indexed();
    $data['staff'] = $this->Page_model->get_all_staff_indexed();

    $data['title'] = "COMPARATIVE ASSESSMENT RESULT - REGISTRY OF QUALIFIED APPLICANTS (CAR - RQA)";

    $this->render_rqa_report($page, $data, $this->rqa_excel_filename('car-rqa-complete', $jobID));
}

    /**
     * Make sure the table that stores RQA recommendations exists.
     * An Item Number may only be assigned to one ACTIVE applicant at a time,
     * but a waived post keeps its record (so the waiver can be undone) while
     * its Item Number becomes available again. Uniqueness among non-waived
     * records is therefore enforced in rqa_recommend_save(), not by the DB.
     */
    private function ensure_rqa_recommendation_table()
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `hris_rqa_recommendation` (
              `id` INT(11) NOT NULL AUTO_INCREMENT,
              `jobID` INT(11) NOT NULL,
              `appID` INT(11) DEFAULT NULL,
              `empEmail` VARCHAR(150) DEFAULT NULL,
              `record_no` VARCHAR(100) DEFAULT NULL,
              `applicant_name` VARCHAR(255) DEFAULT NULL,
              `item_number` VARCHAR(100) NOT NULL,
              `remarks` TEXT DEFAULT NULL,
              `total_points` DECIMAL(10,2) DEFAULT NULL,
              `school_id` INT(11) DEFAULT NULL,
              `school_name` VARCHAR(180) DEFAULT NULL,
              `status` VARCHAR(20) NOT NULL DEFAULT 'recommended',
              `recommended_by` INT(11) DEFAULT NULL,
              `created_at` DATETIME DEFAULT NULL,
              `approved_by` INT(11) DEFAULT NULL,
              `approved_at` DATETIME DEFAULT NULL,
              `date_hired` DATE DEFAULT NULL,
              `date_waived` DATE DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `idx_item_number` (`item_number`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
        ");

        // Backfill columns for installs created before they existed
        $columns = [
            'status' => "ADD COLUMN `status` VARCHAR(20) NOT NULL DEFAULT 'recommended'",
            'approved_by' => "ADD COLUMN `approved_by` INT(11) DEFAULT NULL",
            'approved_at' => "ADD COLUMN `approved_at` DATETIME DEFAULT NULL",
            'school_id' => "ADD COLUMN `school_id` INT(11) DEFAULT NULL",
            'school_name' => "ADD COLUMN `school_name` VARCHAR(180) DEFAULT NULL",
            'date_hired' => "ADD COLUMN `date_hired` DATE DEFAULT NULL",
            'date_waived' => "ADD COLUMN `date_waived` DATE DEFAULT NULL",
        ];
        foreach ($columns as $col => $ddl) {
            if (!$this->db->field_exists($col, 'hris_rqa_recommendation')) {
                $this->db->query("ALTER TABLE `hris_rqa_recommendation` $ddl");
            }
        }

        // Older installs created the table with a UNIQUE Item Number index,
        // which would stop a waived Item Number from ever being reused. Drop
        // it (if present) so reuse works; uniqueness is enforced in PHP now.
        $hasUnique = $this->db->query("SHOW INDEX FROM `hris_rqa_recommendation` WHERE Key_name = 'uniq_item_number'")->row();
        if (!empty($hasUnique)) {
            $this->db->query("ALTER TABLE `hris_rqa_recommendation` DROP INDEX `uniq_item_number`");
        }

        // Normalise rows waived before the 'waived' status existed (the old
        // flow only set date_waived). Give them status = 'waived' so their
        // Item Number frees up and the waiver can be undone. Idempotent.
        $this->db->where('status', 'approved')
            ->where('date_waived IS NOT NULL', null, false)
            ->update('hris_rqa_recommendation', ['status' => 'waived']);

        // Per-applicant ranking metadata for the RQA Recommendation report:
        //   tie_order      - manual ordering set by dragging tied applicants
        //                    (lower = higher up); only meaningful within a tie.
        //   is_corrigendum - applicant flagged as Corrigendum / Addendum.
        // Keyed by appID (globally unique) so the manual order/flag persists
        // for every user who opens the report.
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `hris_rqa_ranking_meta` (
              `id` INT(11) NOT NULL AUTO_INCREMENT,
              `jobID` INT(11) DEFAULT NULL,
              `appID` INT(11) NOT NULL,
              `tie_order` INT(11) DEFAULT NULL,
              `is_corrigendum` TINYINT(1) NOT NULL DEFAULT 0,
              `remarks` TEXT DEFAULT NULL,
              `updated_by` INT(11) DEFAULT NULL,
              `updated_at` DATETIME DEFAULT NULL,
              PRIMARY KEY (`id`),
              UNIQUE KEY `uniq_app` (`appID`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
        ");

        // Backfill the remarks column for installs created before it existed.
        // Remarks saved here (without a School + Item Number) keep the applicant
        // visible in the ranked list, unlike a full recommendation.
        if (!$this->db->field_exists('remarks', 'hris_rqa_ranking_meta')) {
            $this->db->query("ALTER TABLE `hris_rqa_ranking_meta` ADD COLUMN `remarks` TEXT DEFAULT NULL AFTER `is_corrigendum`");
        }

        // Identifies applicants whose RQA score was added/corrected through the
        // Corrigendum / Addendum page after a vacancy was closed. `type` is
        // 'corrigendum' (a correction) or 'addendum' (a late addition).
        // from_jobID / to_jobID record a position transfer made on that page:
        // the applicant was moved from one vacancy to another (hris_applications
        // is updated to to_jobID). When no transfer happens both stay equal to
        // the applicant's jobID.
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `hris_rqa_corrigendum` (
              `id` INT(11) NOT NULL AUTO_INCREMENT,
              `appID` INT(11) NOT NULL,
              `jobID` INT(11) DEFAULT NULL,
              `from_jobID` INT(11) DEFAULT NULL,
              `to_jobID` INT(11) DEFAULT NULL,
              `type` VARCHAR(20) NOT NULL DEFAULT 'corrigendum',
              `remarks` VARCHAR(255) DEFAULT NULL,
              `created_by` INT(11) DEFAULT NULL,
              `created_at` DATETIME DEFAULT NULL,
              `updated_by` INT(11) DEFAULT NULL,
              `updated_at` DATETIME DEFAULT NULL,
              PRIMARY KEY (`id`),
              UNIQUE KEY `uniq_app_job` (`appID`, `jobID`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
        ");

        // Older installs created hris_rqa_corrigendum without the transfer
        // columns; add them so the position transfer can be recorded.
        if (empty($this->db->query("SHOW COLUMNS FROM `hris_rqa_corrigendum` LIKE 'from_jobID'")->row())) {
            $this->db->query("ALTER TABLE `hris_rqa_corrigendum` ADD `from_jobID` INT(11) DEFAULT NULL AFTER `jobID`");
        }
        if (empty($this->db->query("SHOW COLUMNS FROM `hris_rqa_corrigendum` LIKE 'to_jobID'")->row())) {
            $this->db->query("ALTER TABLE `hris_rqa_corrigendum` ADD `to_jobID` INT(11) DEFAULT NULL AFTER `from_jobID`");
        }
    }

    /**
     * Insert/update a row in hris_rqa_ranking_meta for an applicant (keyed by
     * appID). Only the supplied $fields are written so a tie-order change does
     * not clobber the corrigendum flag and vice-versa.
     */
    private function rqa_meta_upsert($appID, $jobID, array $fields)
    {
        $appID = (int) $appID;
        if ($appID <= 0) {
            return;
        }

        $existing = $this->db->select('id')->where('appID', $appID)->get('hris_rqa_ranking_meta')->row();
        if (!empty($existing)) {
            $this->db->where('appID', $appID)->update('hris_rqa_ranking_meta', $fields);
        } else {
            $this->db->insert('hris_rqa_ranking_meta', array_merge(
                ['appID' => $appID, 'jobID' => $jobID > 0 ? (int) $jobID : null],
                $fields
            ));
        }
    }

    /**
     * Resolve the specialization value to show/filter for a row.
     * JHS job types use the `jhss` column, SHS job types use `shss`,
     * everything else falls back to the generic `specialization`.
     * Mirrors the logic already used in the car_rqa views.
     */
    private function rqa_row_specialization($row, $jobType)
    {
        $jobType = (int) $jobType;
        $specialization = $row->specialization ?? '';
        if (in_array($jobType, [3, 8, 16], true) && !empty($row->jhss)) {
            $specialization = $row->jhss;
        } elseif (in_array($jobType, [4, 9, 11, 12, 13, 14], true) && !empty($row->shss)) {
            $specialization = $row->shss;
        }
        return $specialization;
    }

    private function rqa_specialization_kind($jobType)
    {
        $jobType = (int) $jobType;
        if (in_array($jobType, [3, 8, 16], true)) {
            return 'jhs';
        }
        if (in_array($jobType, [4, 9, 11, 12, 13, 14], true)) {
            return 'shs';
        }
        return 'none';
    }

    private function rqa_jhs_specialization_group($specialization)
    {
        $specialization = trim((string) $specialization);
        $specialization = preg_replace('/\s+/', ' ', $specialization);
        $specialization = preg_replace('/\s*[\x{2010}-\x{2015}\x{2212}]\s*/u', ' - ', $specialization);

        $parts = array_map('trim', explode('-', $specialization));
        $group = trim((string) ($parts[0] ?? ''));

        return $group !== '' ? $group : $specialization;
    }

    private function rqa_recommendation_job_suffixes()
    {
        return [
            1 => '- Elementary',
            2 => '- Secondary',
            3 => '- Junior High School',
            4 => '- Senior High School',
            5 => '- Kindergarten',
            6 => '- IPED Elementary',
            7 => '- IPED Secondary',
            8 => '- IPED Junior High School',
            9 => '- IPED Senior High School',
            10 => '- SNED',
            16 => '- Junior High School - SPIMS',
        ];
    }

    // Hide the placeholder rating values (0.00001 / 0.0001) the system stores
    private function rqa_clean_score($v)
    {
        if ($v === null || $v === '' || (float) $v == 0.00001 || (float) $v == 0.0001) {
            return '';
        }
        return $v;
    }

    /**
     * RQA Recommendation report (shell only).
     * The Position drives everything: choosing a position loads the ranked
     * applicants via AJAX, then shows JHS or SHS specialization filters with
     * no page submit. See rqa_recommendation_data().
     */
    public function rqa_recommendation()
    {
        if ($this->session->logged_in == false) {
            redirect(base_url() . 'log_in');
            return;
        }

        $page = "rqa_recommendation";
        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $this->ensure_rqa_recommendation_table();

        // Years that have vacancies (newest first). The report is scoped by the
        // vacancy's school year rather than by Open/Closed status so it keeps
        // working on positions whose vacancy has already been closed.
        $years = $this->Page_model->rqa_available_years();

        $requestedYear = (int) $this->input->get('year', true);
        if ($requestedYear > 0 && in_array($requestedYear, $years, true)) {
            $selectedYear = $requestedYear;
        } else {
            $currentYear = (int) date('Y');
            $selectedYear = in_array($currentYear, $years, true)
                ? $currentYear
                : (int) ($years[0] ?? $currentYear);
        }

        $data = [
            'title' => 'RQA Recommendation',
            'jobOptions' => $this->Page_model->rqa_job_options(),
            'jobTypeSuffixes' => $this->rqa_recommendation_job_suffixes(),
            'years' => $years,
            'selectedYear' => $selectedYear,
            'selectedJobId' => (int) $this->input->get('job', true),
        ];

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    /**
     * Returns the ranked (highest -> lowest RQA) qualified applicants for a
     * single position as JSON. Already-recommended applicants are excluded.
     * The view builds the JHS/SHS specialization filters and the table from
     * this payload, so all remaining filtering happens client-side.
     */
    public function rqa_recommendation_data()
    {
        header('Content-Type: application/json');

        if ($this->session->logged_in == false) {
            echo json_encode(['status' => 'error', 'message' => 'Your session has expired. Please log in again.']);
            return;
        }

        $this->ensure_rqa_recommendation_table();

        // The position can be a merged group of vacancies sharing the same
        // title/type/year, so accept a comma-separated list of jobIDs.
        $jobIDs = array_values(array_unique(array_filter(
            array_map('intval', explode(',', (string) $this->input->get('job', true))),
            function ($v) {
                return $v > 0;
            }
        )));
        if (empty($jobIDs)) {
            echo json_encode(['status' => 'error', 'message' => 'No position selected.']);
            return;
        }

        $jobs = $this->db
            ->select('jobID, jobTitle, job_type')
            ->where_in('jobID', $jobIDs)
            ->get('hris_jobvacancy')
            ->result();
        if (empty($jobs)) {
            echo json_encode(['status' => 'error', 'message' => 'Position not found.']);
            return;
        }

        $jobType = (int) $jobs[0]->job_type;
        $jobTitle = $jobs[0]->jobTitle;
        $specializationKind = $this->rqa_specialization_kind($jobType);
        $specializationApplicable = $specializationKind !== 'none';

        $rows = [];
        foreach ($this->Page_model->rqa_ranked($jobIDs) as $row) {
            $rawSpecialization = trim((string) $this->rqa_row_specialization($row, $jobType));
            $strand = $specializationKind === 'shs' ? trim((string) ($row->shss ?? '')) : '';
            $major = $specializationKind === 'shs' ? trim((string) ($row->Major ?? '')) : '';
            $jhsGroup = $specializationKind === 'jhs' ? $this->rqa_jhs_specialization_group($rawSpecialization) : '';
            $displaySpecialization = $specializationKind === 'jhs' ? $jhsGroup : ($specializationKind === 'shs' ? $major : $rawSpecialization);

            $rows[] = [
                'appID' => (int) ($row->appID ?? 0),
                'jobID' => (int) ($row->jobID ?? $jobIDs[0]),
                'jobType' => $jobType,
                'specializationKind' => $specializationKind,
                'code' => (string) ($row->code ?? ''),
                'empEmail' => (string) ($row->empEmail ?? $row->renren ?? ''),
                'name' => rqa_applicant_name($row),
                'brgy' => trim((string) ($row->brgy ?? '')),
                'municipality' => trim((string) ($row->resCity ?? '')),
                'specialization' => $displaySpecialization,
                'rawSpecialization' => $rawSpecialization,
                'specializationGroup' => $jhsGroup,
                'strand' => $strand,
                'major' => $major,
                'education' => $this->rqa_clean_score($row->education ?? null),
                'training' => $this->rqa_clean_score($row->training ?? null),
                'experience' => $this->rqa_clean_score($row->experience ?? null),
                'let_rating' => $this->rqa_clean_score($row->let_rating ?? null),
                'demo_rating' => $this->rqa_clean_score($row->demo_rating ?? null),
                'tr_rating' => $this->rqa_clean_score($row->tr_rating ?? null),
                'total_points' => !empty($row->total_points) ? number_format((float) $row->total_points, 2, '.', '') : '',
                'tieOrder' => null,
                'isCorrigendum' => 0,
                'remarks' => '',
            ];
        }

        // Merge the saved manual tie-break order + corrigendum/addendum flag.
        $appIDs = array_values(array_filter(array_map(function ($r) {
            return (int) $r['appID'];
        }, $rows), function ($v) {
            return $v > 0;
        }));
        if (!empty($appIDs)) {
            $meta = [];
            foreach (
                $this->db->select('appID, tie_order, is_corrigendum, remarks')
                    ->where_in('appID', $appIDs)
                    ->get('hris_rqa_ranking_meta')
                    ->result() as $m
            ) {
                $meta[(int) $m->appID] = $m;
            }
            foreach ($rows as &$r) {
                $m = $meta[(int) $r['appID']] ?? null;
                if ($m) {
                    $r['tieOrder'] = ($m->tie_order === null) ? null : (int) $m->tie_order;
                    $r['isCorrigendum'] = ((int) $m->is_corrigendum === 1) ? 1 : 0;
                    $r['remarks'] = (string) ($m->remarks ?? '');
                }
            }
            unset($r);
        }

        echo json_encode([
            'status' => 'success',
            'specializationApplicable' => $specializationApplicable,
            'specializationKind' => $specializationKind,
            'jobTitle' => $jobTitle,
            'rows' => $rows,
        ]);
    }

    /**
     * Persist ranking metadata for the RQA Recommendation report (AJAX, JSON).
     *   action=order       -> save the manual tie-break order; expects `order`,
     *                         a JSON array of {appID, jobID} in the new order.
     *   action=corrigendum -> toggle the Corrigendum/Addendum flag for one
     *                         applicant; expects appID, jobID, value (0|1).
     */
    public function rqa_ranking_meta_save()
    {
        header('Content-Type: application/json');

        if ($this->session->logged_in == false) {
            echo json_encode(['status' => 'error', 'message' => 'Your session has expired. Please log in again.']);
            return;
        }

        $this->ensure_rqa_recommendation_table();

        $action = trim((string) $this->input->post('action'));
        $userId = $this->session->id ?? $this->session->userdata('id');
        $userId = $userId ? (int) $userId : null;
        $now = date('Y-m-d H:i:s');

        if ($action === 'corrigendum') {
            $appID = (int) $this->input->post('appID');
            $jobID = (int) $this->input->post('jobID');
            $value = (int) $this->input->post('value') === 1 ? 1 : 0;
            if ($appID <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid applicant.']);
                return;
            }
            $this->rqa_meta_upsert($appID, $jobID, [
                'is_corrigendum' => $value,
                'updated_by' => $userId,
                'updated_at' => $now,
            ]);
            echo json_encode(['status' => 'success', 'isCorrigendum' => $value]);
            return;
        }

        if ($action === 'remarks') {
            $appID = (int) $this->input->post('appID');
            $jobID = (int) $this->input->post('jobID');
            $remarks = trim((string) $this->input->post('remarks'));
            if ($appID <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid applicant.']);
                return;
            }
            $this->rqa_meta_upsert($appID, $jobID, [
                'remarks' => $remarks !== '' ? $remarks : null,
                'updated_by' => $userId,
                'updated_at' => $now,
            ]);
            echo json_encode(['status' => 'success', 'remarks' => $remarks]);
            return;
        }

        if ($action === 'order') {
            $payload = json_decode((string) $this->input->post('order'), true);
            if (!is_array($payload) || empty($payload)) {
                echo json_encode(['status' => 'error', 'message' => 'Nothing to reorder.']);
                return;
            }
            $i = 0;
            foreach ($payload as $entry) {
                $appID = (int) ($entry['appID'] ?? 0);
                $jobID = (int) ($entry['jobID'] ?? 0);
                if ($appID <= 0) {
                    continue;
                }
                $this->rqa_meta_upsert($appID, $jobID, [
                    'tie_order' => $i,
                    'updated_by' => $userId,
                    'updated_at' => $now,
                ]);
                $i++;
            }
            echo json_encode(['status' => 'success']);
            return;
        }

        echo json_encode(['status' => 'error', 'message' => 'Unknown action.']);
    }

    /**
     * Corrigendum / Addendum report (shell only). Mirrors the RQA Recommendation
     * position picker but lets the user add/correct an applicant's RQA score
     * after a vacancy has been closed. Data loads via rqa_corrigendum_data().
     */
    public function rqa_corrigendum()
    {
        if ($this->session->logged_in == false) {
            redirect(base_url() . 'log_in');
            return;
        }

        $page = "rqa_corrigendum";
        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $this->ensure_rqa_recommendation_table();

        $years = $this->Page_model->rqa_available_years();

        $requestedYear = (int) $this->input->get('year', true);
        if ($requestedYear > 0 && in_array($requestedYear, $years, true)) {
            $selectedYear = $requestedYear;
        } else {
            $currentYear = (int) date('Y');
            $selectedYear = in_array($currentYear, $years, true)
                ? $currentYear
                : (int) ($years[0] ?? $currentYear);
        }

        $data = [
            'title' => 'Corrigendum / Addendum',
            'jobOptions' => $this->Page_model->rqa_job_options(),
            'jobTypeSuffixes' => $this->rqa_recommendation_job_suffixes(),
            'years' => $years,
            'selectedYear' => $selectedYear,
            'selectedJobId' => (int) $this->input->get('job', true),
        ];

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    /**
     * Qualified applicants for the selected position with their current RQA
     * scores (editable on the Corrigendum / Addendum page), as JSON.
     */
    public function rqa_corrigendum_data()
    {
        header('Content-Type: application/json');

        if ($this->session->logged_in == false) {
            echo json_encode(['status' => 'error', 'message' => 'Your session has expired. Please log in again.']);
            return;
        }

        $this->ensure_rqa_recommendation_table();

        $jobIDs = array_values(array_unique(array_filter(
            array_map('intval', explode(',', (string) $this->input->get('job', true))),
            function ($v) {
                return $v > 0;
            }
        )));
        if (empty($jobIDs)) {
            echo json_encode(['status' => 'error', 'message' => 'No position selected.']);
            return;
        }

        $jobs = $this->db
            ->select('jobID, jobTitle, job_type')
            ->where_in('jobID', $jobIDs)
            ->get('hris_jobvacancy')
            ->result();
        if (empty($jobs)) {
            echo json_encode(['status' => 'error', 'message' => 'Position not found.']);
            return;
        }

        $jobType = (int) $jobs[0]->job_type;
        $jobTitle = $jobs[0]->jobTitle;
        $specializationKind = $this->rqa_specialization_kind($jobType);
        $specializationApplicable = $specializationKind !== 'none';

        $rows = [];
        foreach ($this->Page_model->rqa_corrigendum_applicants($jobIDs) as $row) {
            $rawSpecialization = trim((string) $this->rqa_row_specialization($row, $jobType));
            $strand = $specializationKind === 'shs' ? trim((string) ($row->shss ?? '')) : '';
            $major = $specializationKind === 'shs' ? trim((string) ($row->Major ?? '')) : '';
            $jhsGroup = $specializationKind === 'jhs' ? $this->rqa_jhs_specialization_group($rawSpecialization) : '';
            $displaySpecialization = $specializationKind === 'jhs' ? $jhsGroup : ($specializationKind === 'shs' ? $major : $rawSpecialization);

            $rows[] = [
                'appID' => (int) ($row->appID ?? 0),
                'jobID' => (int) ($row->jobID ?? $jobIDs[0]),
                'jobType' => $jobType,
                'specializationKind' => $specializationKind,
                'code' => (string) ($row->code ?? ''),
                'empEmail' => (string) ($row->empEmail ?? $row->renren ?? ''),
                'name' => rqa_applicant_name($row),
                'municipality' => trim((string) ($row->resCity ?? '')),
                'brgy' => trim((string) ($row->brgy ?? '')),
                'specialization' => $displaySpecialization,
                'specializationGroup' => $jhsGroup,
                'strand' => $strand,
                'major' => $major,
                'education' => $this->rqa_clean_score($row->education ?? null),
                'training' => $this->rqa_clean_score($row->training ?? null),
                'experience' => $this->rqa_clean_score($row->experience ?? null),
                'let_rating' => $this->rqa_clean_score($row->let_rating ?? null),
                'demo_rating' => $this->rqa_clean_score($row->demo_rating ?? null),
                'tr_rating' => $this->rqa_clean_score($row->tr_rating ?? null),
                'total_points' => ($row->total_points === null || (float) $row->total_points == 0)
                    ? ''
                    : number_format((float) $row->total_points, 2, '.', ''),
                'corrigendumType' => trim((string) ($row->corrigendum_type ?? '')),
            ];
        }

        echo json_encode([
            'status' => 'success',
            'specializationApplicable' => $specializationApplicable,
            'specializationKind' => $specializationKind,
            'jobTitle' => $jobTitle,
            'rows' => $rows,
        ]);
    }

    /**
     * Save a single Corrigendum / Addendum score (AJAX, JSON). Writes the RQA
     * score components to hris_applications_rating (insert if the applicant has
     * no rating row yet), optionally transfers the applicant to another position
     * (updating hris_applications.jobID), records the applicant in
     * hris_rqa_corrigendum with the chosen type and from/to position, and flags
     * them in hris_rqa_ranking_meta so they show as Corrigendum/Addendum (blue)
     * on the RQA Recommendation report.
     */
    public function rqa_corrigendum_save()
    {
        header('Content-Type: application/json');

        if ($this->session->logged_in == false) {
            echo json_encode(['status' => 'error', 'message' => 'Your session has expired. Please log in again.']);
            return;
        }

        $this->ensure_rqa_recommendation_table();

        $appID = (int) $this->input->post('appID');
        $jobID = (int) $this->input->post('jobID');
        // Position the applicant should end up in. Defaults to the current
        // position when not supplied (i.e. no transfer requested).
        $targetJobID = (int) $this->input->post('target_jobID');
        if ($targetJobID <= 0) {
            $targetJobID = $jobID;
        }
        $recordNo = trim((string) $this->input->post('record_no'));
        $type = strtolower(trim((string) $this->input->post('type')));
        if (!in_array($type, ['corrigendum', 'addendum'], true)) {
            $type = 'corrigendum';
        }

        if ($appID <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid applicant.']);
            return;
        }

        // When a transfer is requested, the target must be a real vacancy.
        $isTransfer = ($targetJobID > 0 && $targetJobID !== $jobID);
        $targetJob = null;
        if ($targetJobID > 0) {
            $targetJob = $this->db->select('jobID, jobTitle, job_type, sy')->where('jobID', $targetJobID)->get('hris_jobvacancy')->row();
            if ($isTransfer && empty($targetJob)) {
                echo json_encode(['status' => 'error', 'message' => 'The selected position could not be found.']);
                return;
            }
        }

        // Sanitise the six score components, then always derive the total from
        // those values instead of trusting a client-supplied total.
        $scoreFields = ['education', 'training', 'experience', 'let_rating', 'demo_rating', 'tr_rating'];
        $scores = [];
        foreach ($scoreFields as $f) {
            $raw = $this->input->post($f);
            $scores[$f] = ($raw === null || $raw === '') ? 0 : (float) $raw;
        }
        $scores['total_points'] = array_sum($scores);

        $userId = $this->session->id ?? $this->session->userdata('id');
        $userId = $userId ? (int) $userId : 0;
        $now = date('Y-m-d H:i:s');

        // Transfer: move the applicant to the target position in hris_applications.
        if ($isTransfer) {
            $this->db->where('appID', $appID)->update('hris_applications', ['jobID' => $targetJobID]);
        }

        // hris_applications_rating: update the existing row, else insert one.
        // The job_type / fy follow the target position so a transferred
        // applicant's rating reflects where they now sit.
        $ratingJob = $targetJob ?: $this->db->select('job_type, sy')->where('jobID', $jobID)->get('hris_jobvacancy')->row();
        $existing = $this->db->select('id')->where('appID', $appID)->get('hris_applications_rating')->row();
        if (!empty($existing)) {
            $update = $scores;
            $update['eval_id1'] = (string) $userId;
            if ($ratingJob) {
                $update['job_type'] = (int) $ratingJob->job_type;
                $update['fy'] = (string) $ratingJob->sy;
            }
            $this->db->where('appID', $appID)->update('hris_applications_rating', $update);
        } else {
            $insert = array_merge($scores, [
                'appID' => $appID,
                'record_no' => $recordNo !== '' ? $recordNo : null,
                'eval_id1' => (string) $userId,
                'eval_id2' => $userId,
                'eval_id3' => $userId,
                'job_type' => $ratingJob ? (int) $ratingJob->job_type : 0,
                'fy' => $ratingJob ? (string) $ratingJob->sy : (string) date('Y'),
            ]);
            $this->db->insert('hris_applications_rating', $insert);
        }

        // hris_rqa_corrigendum: identify the applicant + chosen type + transfer.
        // Look up by appID so a row created before a transfer is reused and its
        // jobID follows the applicant to the target position.
        $corValues = [
            'jobID' => $targetJobID > 0 ? $targetJobID : null,
            'from_jobID' => $jobID > 0 ? $jobID : null,
            'to_jobID' => $targetJobID > 0 ? $targetJobID : null,
            'type' => $type,
        ];
        $corExisting = $this->db->select('id')->where('appID', $appID)->order_by('id', 'DESC')->limit(1)->get('hris_rqa_corrigendum')->row();
        if (!empty($corExisting)) {
            $this->db->where('id', (int) $corExisting->id)->update('hris_rqa_corrigendum', array_merge($corValues, [
                'updated_by' => $userId ?: null,
                'updated_at' => $now,
            ]));
        } else {
            $this->db->insert('hris_rqa_corrigendum', array_merge($corValues, [
                'appID' => $appID,
                'created_by' => $userId ?: null,
                'created_at' => $now,
            ]));
        }

        // Flag for the RQA Recommendation blue highlight (keyed by appID, so it
        // follows the applicant to the target position).
        $this->rqa_meta_upsert($appID, $targetJobID, [
            'is_corrigendum' => 1,
            'updated_by' => $userId ?: null,
            'updated_at' => $now,
        ]);

        $message = 'Score saved and applicant marked as ' . ucfirst($type) . '.';
        if ($isTransfer && $targetJob) {
            $message = 'Score saved, applicant transferred to "' . trim((string) $targetJob->jobTitle) . '" and marked as ' . ucfirst($type) . '.';
        }

        echo json_encode([
            'status' => 'success',
            'message' => $message,
            'corrigendumType' => $type,
            'transferred' => $isTransfer,
            'targetJobID' => $targetJobID,
            'total_points' => $scores['total_points'] > 0 ? number_format($scores['total_points'], 2, '.', '') : '',
        ]);
    }

    /**
     * Cleanup report for JHS applicants with a blank Learning Area (jhss).
     * The UI mirrors the RQA Recommendation position picker but lists only
     * applicants that need profile correction.
     */
    public function rqa_missing_jhs_learning_area()
    {
        if ($this->session->logged_in == false) {
            redirect(base_url() . 'log_in');
            return;
        }

        $page = "rqa_missing_jhs_learning_area";
        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $jobOptions = array_values(array_filter($this->Page_model->rqa_job_options(), function ($j) {
            return in_array((int) $j->job_type, [3, 8, 16], true);
        }));

        $yearMap = [];
        foreach ($jobOptions as $j) {
            $sy = (int) ($j->sy ?? 0);
            if ($sy > 0) {
                $yearMap[$sy] = true;
            }
        }
        $years = array_keys($yearMap);
        rsort($years, SORT_NUMERIC);

        $requestedYear = (int) $this->input->get('year', true);
        if ($requestedYear > 0 && in_array($requestedYear, $years, true)) {
            $selectedYear = $requestedYear;
        } else {
            $currentYear = (int) date('Y');
            $selectedYear = in_array($currentYear, $years, true)
                ? $currentYear
                : (int) ($years[0] ?? $currentYear);
        }

        $data = [
            'title' => 'JHS Applicants Missing Learning Area',
            'jobOptions' => $jobOptions,
            'jobTypeSuffixes' => $this->rqa_recommendation_job_suffixes(),
            'years' => $years,
            'selectedYear' => $selectedYear,
            'selectedJobId' => (int) $this->input->get('job', true),
        ];

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    /**
     * JSON payload for the JHS missing Learning Area cleanup report.
     */
    public function rqa_missing_jhs_learning_area_data()
    {
        header('Content-Type: application/json');

        if ($this->session->logged_in == false) {
            echo json_encode(['status' => 'error', 'message' => 'Your session has expired. Please log in again.']);
            return;
        }

        $jobIDs = array_values(array_unique(array_filter(
            array_map('intval', explode(',', (string) $this->input->get('job', true))),
            function ($v) {
                return $v > 0;
            }
        )));
        if (empty($jobIDs)) {
            echo json_encode(['status' => 'error', 'message' => 'No position selected.']);
            return;
        }

        $jobs = $this->db
            ->select('jobID, jobTitle, job_type')
            ->where_in('jobID', $jobIDs)
            ->get('hris_jobvacancy')
            ->result();
        if (empty($jobs)) {
            echo json_encode(['status' => 'error', 'message' => 'Position not found.']);
            return;
        }

        $jhsJobIDs = [];
        $jobTitle = '';
        foreach ($jobs as $job) {
            if (in_array((int) $job->job_type, [3, 8, 16], true)) {
                $jhsJobIDs[] = (int) $job->jobID;
                if ($jobTitle === '') {
                    $jobTitle = (string) $job->jobTitle;
                }
            }
        }
        if (empty($jhsJobIDs)) {
            echo json_encode(['status' => 'error', 'message' => 'Please select a Junior High School position.']);
            return;
        }

        $rows = [];
        foreach ($this->Page_model->rqa_missing_jhs_learning_area($jhsJobIDs) as $row) {
            $profileRoute = (string) ($row->profile_route ?? '');
            $profileId = (string) ($row->profile_id ?? '');
            $profileUrl = '';

            if ($profileRoute === 'profile_reg_edit' && $profileId !== '') {
                $profileUrl = base_url('Pages/profile_reg_edit/' . rawurlencode($profileId));
            } elseif ($profileRoute === 'employee_edit' && $profileId !== '') {
                $profileUrl = base_url('Pages/employee_edit/' . rawurlencode($profileId));
            }

            $rows[] = [
                'appID' => (int) ($row->appID ?? 0),
                'jobID' => (int) ($row->jobID ?? $jhsJobIDs[0]),
                'code' => (string) ($row->code ?? ''),
                'empEmail' => (string) ($row->renren ?? ''),
                'name' => rqa_applicant_name($row),
                'contact' => (string) ($row->contactNo ?? ''),
                'municipality' => trim((string) ($row->resCity ?? '')),
                'brgy' => trim((string) ($row->brgy ?? '')),
                'applicationStatus' => (string) ($row->appStatus ?? ''),
                'dq' => (int) ($row->dq ?? 0),
                'learningArea' => trim((string) ($row->jhss ?? '')),
                'education' => $this->rqa_clean_score($row->education ?? null),
                'training' => $this->rqa_clean_score($row->training ?? null),
                'experience' => $this->rqa_clean_score($row->experience ?? null),
                'let_rating' => $this->rqa_clean_score($row->let_rating ?? null),
                'demo_rating' => $this->rqa_clean_score($row->demo_rating ?? null),
                'tr_rating' => $this->rqa_clean_score($row->tr_rating ?? null),
                'total_points' => !empty($row->total_points) ? number_format((float) $row->total_points, 2, '.', '') : '',
                'profileUrl' => $profileUrl,
            ];
        }

        echo json_encode([
            'status' => 'success',
            'jobTitle' => $jobTitle,
            'rows' => $rows,
        ]);
    }

    /**
     * Persist a single RQA recommendation (AJAX, JSON response).
     * Rejects a duplicate Item Number so the same Item Number can never
     * be assigned twice.
     */
    public function rqa_recommend_save()
    {
        header('Content-Type: application/json');

        if ($this->session->logged_in == false) {
            echo json_encode(['status' => 'error', 'message' => 'Your session has expired. Please log in again.']);
            return;
        }

        $this->ensure_rqa_recommendation_table();

        $jobID = (int) $this->input->post('jobID');
        $appID = (int) $this->input->post('appID');
        $empEmail = trim((string) $this->input->post('empEmail'));
        $record_no = trim((string) $this->input->post('record_no'));
        $applicant_name = trim((string) $this->input->post('applicant_name'));
        $item_number = trim((string) $this->input->post('item_number'));
        $remarks = trim((string) $this->input->post('remarks'));
        $total_points = $this->input->post('total_points');
        $school_id = (int) $this->input->post('school_id');
        $school_name = trim((string) $this->input->post('school_name'));

        if ($item_number === '') {
            echo json_encode(['status' => 'error', 'message' => 'Item Number is required.']);
            return;
        }
        if ($school_id <= 0 || $school_name === '') {
            echo json_encode(['status' => 'error', 'message' => 'Please select the School where the applicant will be assigned.']);
            return;
        }
        if ($jobID <= 0 || $appID <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Missing applicant information. Please reload the page and try again.']);
            return;
        }

        // Item Number must be unique among ACTIVE applicants. A waived post
        // frees its Item Number, so ignore waived records when checking.
        $existing = $this->db->where('item_number', $item_number)
            ->where('status !=', 'waived')
            ->get('hris_rqa_recommendation')->row();
        if (!empty($existing)) {
            echo json_encode(['status' => 'duplicate', 'message' => 'Item Number "' . $item_number . '" already exists. Please use a different Item Number.']);
            return;
        }

        $userId = $this->session->id ?? $this->session->userdata('id');

        $insert = $this->db->insert('hris_rqa_recommendation', [
            'jobID' => $jobID,
            'appID' => $appID,
            'empEmail' => $empEmail !== '' ? $empEmail : null,
            'record_no' => $record_no !== '' ? $record_no : null,
            'applicant_name' => $applicant_name !== '' ? $applicant_name : null,
            'item_number' => $item_number,
            'remarks' => $remarks !== '' ? $remarks : null,
            'total_points' => is_numeric($total_points) ? $total_points : null,
            'school_id' => $school_id,
            'school_name' => $school_name,
            'recommended_by' => $userId ? (int) $userId : null,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        if ($insert) {
            echo json_encode(['status' => 'success', 'message' => 'Applicant recommended successfully.']);
            return;
        }

        $error = $this->db->error();
        // Guard against a race on the unique Item Number index
        if (isset($error['code']) && (int) $error['code'] === 1062) {
            echo json_encode(['status' => 'duplicate', 'message' => 'Item Number "' . $item_number . '" already exists. Please use a different Item Number.']);
            return;
        }

        echo json_encode(['status' => 'error', 'message' => 'Failed to save recommendation: ' . ($error['message'] ?? 'Unknown error')]);
    }

    /**
     * Select2 (AJAX) search for schools used on the RQA Recommendation page.
     * Schools are NOT restricted by the applicant's municipality or barangay -
     * any school in the schools table can be selected.
     */
    public function rqa_school_search()
    {
        header('Content-Type: application/json');

        if ($this->session->logged_in == false) {
            echo json_encode(['results' => []]);
            return;
        }

        $q = trim((string) $this->input->get('q', true));

        $this->db->select('recID, schoolID, schoolName, district');
        $this->db->from('schools');
        if ($q !== '') {
            $this->db->group_start();
            $this->db->like('schoolName', $q);
            $this->db->or_like('schoolID', $q);
            $this->db->or_like('district', $q);
            $this->db->group_end();
        }
        $this->db->order_by('schoolName', 'ASC');
        $this->db->limit(30);

        $results = [];
        foreach ($this->db->get()->result() as $r) {
            $label = $r->schoolName;
            if (!empty($r->district)) {
                $label .= ' — ' . $r->district;
            }
            $results[] = [
                'id' => (int) $r->recID,
                'text' => $label,
                'name' => $r->schoolName,
            ];
        }

        echo json_encode(['results' => $results]);
    }

    public function rqa_shs_upload()
    {
        if ($this->session->logged_in == false) {
            redirect(base_url() . 'log_in');
            return;
        }

        $page = "rqa_shs_upload";
        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $data = [
            'title' => 'RQA SHS Strand and Major Upload',
            'result' => $this->session->flashdata('rqa_shs_import_result'),
            'error' => $this->session->flashdata('rqa_shs_import_error'),
        ];

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function rqa_shs_upload_import()
    {
        if ($this->session->logged_in == false) {
            redirect(base_url() . 'log_in');
            return;
        }

        $uploadPath = FCPATH . 'uploads/rqa/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $originalName = $_FILES['rqa_file']['name'] ?? '';
        $baseName = pathinfo($originalName, PATHINFO_FILENAME);
        $baseName = preg_replace('/[^A-Za-z0-9_-]+/', '-', $baseName);
        $baseName = trim($baseName, '-_');
        if ($baseName === '') {
            $baseName = 'rqa-shs';
        }

        $config = [
            'upload_path' => $uploadPath,
            'allowed_types' => 'xlsx',
            'file_ext_tolower' => true,
            'remove_spaces' => true,
            'overwrite' => false,
            'max_size' => 10240,
            'file_name' => 'rqa-shs-' . date('Ymd-His') . '-' . $baseName,
        ];

        $this->load->library('upload');
        $this->upload->initialize($config);

        if (!$this->upload->do_upload('rqa_file')) {
            $this->session->set_flashdata('rqa_shs_import_error', strip_tags($this->upload->display_errors('', '')));
            redirect(base_url('Pages/rqa_shs_upload'));
            return;
        }

        $uploadData = $this->upload->data();
        $filePath = $uploadData['full_path'];

        try {
            $result = $this->rqa_import_shs_xlsx($filePath);
            $result['file_name'] = $uploadData['file_name'];
            $this->session->set_flashdata('rqa_shs_import_result', $result);
        } catch (Exception $e) {
            $this->session->set_flashdata('rqa_shs_import_error', $e->getMessage());
        }

        redirect(base_url('Pages/rqa_shs_upload'));
    }

    public function rqa_jhs_upload()
    {
        if ($this->session->logged_in == false) {
            redirect(base_url() . 'log_in');
            return;
        }

        $page = "rqa_jhs_upload";
        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $data = [
            'title' => 'RQA JHS Specialization Upload',
            'result' => $this->session->flashdata('rqa_jhs_import_result'),
            'error' => $this->session->flashdata('rqa_jhs_import_error'),
        ];

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function rqa_jhs_upload_import()
    {
        if ($this->session->logged_in == false) {
            redirect(base_url() . 'log_in');
            return;
        }

        $uploadPath = FCPATH . 'uploads/rqa/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $originalName = $_FILES['rqa_file']['name'] ?? '';
        $baseName = pathinfo($originalName, PATHINFO_FILENAME);
        $baseName = preg_replace('/[^A-Za-z0-9_-]+/', '-', $baseName);
        $baseName = trim($baseName, '-_');
        if ($baseName === '') {
            $baseName = 'rqa-jhs';
        }

        $config = [
            'upload_path' => $uploadPath,
            'allowed_types' => 'xlsx',
            'file_ext_tolower' => true,
            'remove_spaces' => true,
            'overwrite' => false,
            'max_size' => 10240,
            'file_name' => 'rqa-jhs-' . date('Ymd-His') . '-' . $baseName,
        ];

        $this->load->library('upload');
        $this->upload->initialize($config);

        if (!$this->upload->do_upload('rqa_file')) {
            $this->session->set_flashdata('rqa_jhs_import_error', strip_tags($this->upload->display_errors('', '')));
            redirect(base_url('Pages/rqa_jhs_upload'));
            return;
        }

        $uploadData = $this->upload->data();
        $filePath = $uploadData['full_path'];

        try {
            $result = $this->rqa_import_jhs_xlsx($filePath);
            $result['file_name'] = $uploadData['file_name'];
            $this->session->set_flashdata('rqa_jhs_import_result', $result);
        } catch (Exception $e) {
            $this->session->set_flashdata('rqa_jhs_import_error', $e->getMessage());
        }

        redirect(base_url('Pages/rqa_jhs_upload'));
    }

    private function rqa_import_shs_xlsx($filePath)
    {
        if (!$this->db->field_exists('record_no', 'hris_applicant')
            || !$this->db->field_exists('shss', 'hris_applicant')) {
            throw new Exception('The hris_applicant table must have record_no and shss columns.');
        }
        $this->rqa_ensure_varchar_length('hris_applicant', 'Major', 200);

        $rows = $this->rqa_read_xlsx_rows($filePath);
        if (empty($rows)) {
            throw new Exception('No readable rows were found in the uploaded workbook.');
        }

        $columns = $this->rqa_detect_shs_import_columns($rows);
        $recordCol = $columns['record_col'];
        $trackCol = $columns['track_col'];
        $startRow = $columns['start_row'];

        $summary = [
            'total_rows' => 0,
            'matched' => 0,
            'updated' => 0,
            'unchanged' => 0,
            'not_found' => 0,
            'invalid' => 0,
            'duplicates' => 0,
            'not_found_rows' => [],
            'invalid_rows' => [],
            'duplicate_rows' => [],
        ];

        $seen = [];
        $this->db->trans_start();

        foreach ($rows as $row) {
            $rowNumber = (int) $row['row'];
            if ($rowNumber <= $startRow) {
                continue;
            }

            $values = $row['values'];
            $recordNo = trim((string) ($values[$recordCol] ?? ''));
            $trackMajor = $this->rqa_normalize_shs_track_value($values[$trackCol] ?? '');

            if ($recordNo === '' && $trackMajor === '') {
                continue;
            }

            if ($recordNo === '' || $trackMajor === '') {
                $summary['invalid']++;
                $this->rqa_push_limited($summary['invalid_rows'], [
                    'row' => $rowNumber,
                    'record_no' => $recordNo,
                    'value' => $trackMajor,
                    'reason' => 'Missing application code or track-strand-major value.',
                ]);
                continue;
            }

            $split = $this->rqa_split_shs_track_major($trackMajor);
            if ($split === false) {
                $summary['invalid']++;
                $this->rqa_push_limited($summary['invalid_rows'], [
                    'row' => $rowNumber,
                    'record_no' => $recordNo,
                    'value' => $trackMajor,
                    'reason' => 'Value must contain a hyphen before the major.',
                ]);
                continue;
            }

            $key = strtoupper($recordNo);
            if (isset($seen[$key])) {
                $summary['duplicates']++;
                $this->rqa_push_limited($summary['duplicate_rows'], [
                    'row' => $rowNumber,
                    'record_no' => $recordNo,
                    'first_row' => $seen[$key],
                ]);
                continue;
            }
            $seen[$key] = $rowNumber;

            $summary['total_rows']++;

            $applicant = $this->db
                ->select('id, record_no, shss, Major')
                ->where('record_no', $recordNo)
                ->get('hris_applicant')
                ->row();

            if (empty($applicant)) {
                $summary['not_found']++;
                $this->rqa_push_limited($summary['not_found_rows'], [
                    'row' => $rowNumber,
                    'record_no' => $recordNo,
                    'value' => $trackMajor,
                ]);
                continue;
            }

            $summary['matched']++;

            if ((string) $applicant->shss === $split['shss'] && (string) $applicant->Major === $split['major']) {
                $summary['unchanged']++;
                continue;
            }

            $this->db->where('id', (int) $applicant->id)->update('hris_applicant', [
                'shss' => $split['shss'],
                'Major' => $split['major'],
            ]);
            $summary['updated']++;
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            throw new Exception('The import failed while updating applicant records.');
        }

        return $summary;
    }

    private function rqa_import_jhs_xlsx($filePath)
    {
        if (!$this->db->field_exists('record_no', 'hris_applicant')
            || !$this->db->field_exists('jhss', 'hris_applicant')) {
            throw new Exception('The hris_applicant table must have record_no and jhss columns.');
        }
        $this->rqa_ensure_varchar_length('hris_applicant', 'jhss', 200);

        $rows = $this->rqa_read_xlsx_rows($filePath);
        if (empty($rows)) {
            throw new Exception('No readable rows were found in the uploaded workbook.');
        }

        $columns = $this->rqa_detect_jhs_import_columns($rows);
        $rankCol = $columns['rank_col'];
        $recordCol = $columns['record_col'];
        $specializationCol = $columns['specialization_col'];
        $startRow = $columns['start_row'];

        $summary = [
            'total_rows' => 0,
            'matched' => 0,
            'updated' => 0,
            'unchanged' => 0,
            'not_found' => 0,
            'invalid' => 0,
            'duplicates' => 0,
            'not_found_rows' => [],
            'invalid_rows' => [],
            'duplicate_rows' => [],
        ];

        $seen = [];
        $this->db->trans_start();

        foreach ($rows as $row) {
            $rowNumber = (int) $row['row'];
            if ($rowNumber <= $startRow) {
                continue;
            }

            $values = $row['values'];
            $rankValue = trim((string) ($values[$rankCol] ?? ''));
            $recordNo = trim((string) ($values[$recordCol] ?? ''));
            $jhss = $this->rqa_normalize_jhs_specialization_value($values[$specializationCol] ?? '');

            if ($recordNo === '' && $jhss === '') {
                continue;
            }
            if (!preg_match('/^\d+$/', $rankValue)) {
                continue;
            }

            if ($recordNo === '' || $jhss === '') {
                $summary['invalid']++;
                $this->rqa_push_limited($summary['invalid_rows'], [
                    'row' => $rowNumber,
                    'record_no' => $recordNo,
                    'value' => $jhss,
                    'reason' => 'Missing application code or specialization value.',
                ]);
                continue;
            }

            $key = strtoupper($recordNo);
            if (isset($seen[$key])) {
                $summary['duplicates']++;
                $this->rqa_push_limited($summary['duplicate_rows'], [
                    'row' => $rowNumber,
                    'record_no' => $recordNo,
                    'first_row' => $seen[$key],
                ]);
                continue;
            }
            $seen[$key] = $rowNumber;

            $summary['total_rows']++;

            $applicant = $this->db
                ->select('id, record_no, jhss')
                ->where('record_no', $recordNo)
                ->get('hris_applicant')
                ->row();

            if (empty($applicant)) {
                $summary['not_found']++;
                $this->rqa_push_limited($summary['not_found_rows'], [
                    'row' => $rowNumber,
                    'record_no' => $recordNo,
                    'value' => $jhss,
                ]);
                continue;
            }

            $summary['matched']++;

            if ((string) $applicant->jhss === $jhss) {
                $summary['unchanged']++;
                continue;
            }

            $this->db->where('id', (int) $applicant->id)->update('hris_applicant', [
                'jhss' => $jhss,
            ]);
            $summary['updated']++;
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            throw new Exception('The import failed while updating applicant records.');
        }

        return $summary;
    }

    private function rqa_detect_shs_import_columns($rows)
    {
        foreach ($rows as $row) {
            $recordCol = null;
            $trackCol = null;

            foreach ($row['values'] as $idx => $value) {
                $text = strtolower(trim(preg_replace('/\s+/', ' ', (string) $value)));
                if ($text === '') {
                    continue;
                }

                if (strpos($text, 'application code') !== false) {
                    $recordCol = $idx;
                }

                if (strpos($text, 'applied track') !== false || strpos($text, 'track - strand') !== false || strpos($text, 'track-strand') !== false) {
                    $trackCol = $idx;
                }
            }

            if ($recordCol !== null && $trackCol !== null) {
                return [
                    'record_col' => $recordCol,
                    'track_col' => $trackCol,
                    'start_row' => (int) $row['row'],
                ];
            }
        }

        return [
            'record_col' => 1,
            'track_col' => 2,
            'start_row' => 0,
        ];
    }

    private function rqa_detect_jhs_import_columns($rows)
    {
        foreach ($rows as $row) {
            $rankCol = null;
            $recordCol = null;
            $specializationCol = null;

            foreach ($row['values'] as $idx => $value) {
                $text = strtolower(trim(preg_replace('/\s+/', ' ', (string) $value)));
                if ($text === '') {
                    continue;
                }

                if ($text === 'no.' || $text === 'rank no.' || $text === 'rank') {
                    $rankCol = $idx;
                }

                if (strpos($text, 'application code') !== false) {
                    $recordCol = $idx;
                }

                if ($text === 'specialization' || strpos($text, 'specialization') !== false) {
                    $specializationCol = $idx;
                }
            }

            if ($recordCol !== null && $specializationCol !== null) {
                return [
                    'rank_col' => $rankCol !== null ? $rankCol : 0,
                    'record_col' => $recordCol,
                    'specialization_col' => $specializationCol,
                    'start_row' => (int) $row['row'],
                ];
            }
        }

        return [
            'rank_col' => 0,
            'record_col' => 2,
            'specialization_col' => 7,
            'start_row' => 0,
        ];
    }

    private function rqa_ensure_varchar_length($table, $column, $length)
    {
        if (!preg_match('/^[A-Za-z0-9_]+$/', $table) || !preg_match('/^[A-Za-z0-9_]+$/', $column)) {
            throw new Exception('Invalid database column identifier.');
        }

        $field = $this->db->query("SHOW COLUMNS FROM `$table` LIKE " . $this->db->escape($column))->row();
        if (empty($field)) {
            $this->db->query("ALTER TABLE `$table` ADD COLUMN `$column` VARCHAR(" . (int) $length . ") NULL");
            return;
        }

        if (!preg_match('/^varchar\((\d+)\)/i', (string) $field->Type, $matches)) {
            return;
        }

        if ((int) $matches[1] >= (int) $length) {
            return;
        }

        $nullable = strtoupper((string) $field->Null) === 'YES' ? 'NULL' : 'NOT NULL';
        $default = '';
        if ($field->Default !== null) {
            $default = ' DEFAULT ' . $this->db->escape($field->Default);
        }

        $this->db->query("ALTER TABLE `$table` MODIFY COLUMN `$column` VARCHAR(" . (int) $length . ") $nullable$default");
    }

    private function rqa_normalize_shs_track_value($value)
    {
        $value = trim((string) $value);
        $value = preg_replace('/\s+/', ' ', $value);
        $value = preg_replace('/\s*[\x{2010}-\x{2015}\x{2212}]\s*/u', '-', $value);
        $value = preg_replace('/\s*-\s*/', '-', $value);

        return trim($value);
    }

    private function rqa_normalize_jhs_specialization_value($value)
    {
        $value = trim((string) $value);
        $value = preg_replace('/\s+/', ' ', $value);
        $value = preg_replace('/\s*[\x{2010}-\x{2015}\x{2212}]\s*/u', ' - ', $value);
        $value = preg_replace('/\s*-\s*/', ' - ', $value);

        return trim($value);
    }

    private function rqa_split_shs_track_major($trackMajor)
    {
        $trackMajor = $this->rqa_normalize_shs_track_value($trackMajor);
        $parts = array_map('trim', explode('-', $trackMajor));
        $parts = array_values(array_filter($parts, function ($part) {
            return $part !== '';
        }));

        if (count($parts) < 2) {
            return false;
        }

        $major = array_pop($parts);
        $shss = implode('-', $parts);

        if ($shss === '' || $major === '') {
            return false;
        }

        return [
            'shss' => $shss,
            'major' => $major,
        ];
    }

    private function rqa_push_limited(&$list, $row, $limit = 15)
    {
        if (count($list) < $limit) {
            $list[] = $row;
        }
    }

    private function rqa_read_xlsx_rows($filePath)
    {
        if (!class_exists('ZipArchive')) {
            throw new Exception('The PHP Zip extension is required to read XLSX files.');
        }

        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            throw new Exception('Unable to open the uploaded XLSX file.');
        }

        $sharedStrings = $this->rqa_xlsx_shared_strings($zip);
        $sheetPath = $this->rqa_xlsx_first_sheet_path($zip);
        $sheetXml = $zip->getFromName($sheetPath);
        if ($sheetXml === false) {
            $zip->close();
            throw new Exception('Unable to locate the first worksheet in the uploaded file.');
        }

        $doc = $this->rqa_xlsx_dom($sheetXml);
        $xpath = new DOMXPath($doc);
        $xpath->registerNamespace('m', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

        $rows = [];
        foreach ($xpath->query('//m:sheetData/m:row') as $rowNode) {
            $rowNumber = (int) $rowNode->getAttribute('r');
            $values = [];
            $maxCol = -1;

            foreach ($xpath->query('m:c', $rowNode) as $cellNode) {
                $cellRef = $cellNode->getAttribute('r');
                $col = $this->rqa_xlsx_col_index($cellRef);
                if ($col < 0) {
                    continue;
                }

                $maxCol = max($maxCol, $col);
                $values[$col] = $this->rqa_xlsx_cell_value($xpath, $cellNode, $sharedStrings);
            }

            if ($maxCol < 0) {
                continue;
            }

            for ($i = 0; $i <= $maxCol; $i++) {
                if (!array_key_exists($i, $values)) {
                    $values[$i] = '';
                }
            }
            ksort($values);

            $rows[] = [
                'row' => $rowNumber > 0 ? $rowNumber : (count($rows) + 1),
                'values' => $values,
            ];
        }

        $zip->close();

        return $rows;
    }

    private function rqa_xlsx_shared_strings(ZipArchive $zip)
    {
        $xml = $zip->getFromName('xl/sharedStrings.xml');
        if ($xml === false) {
            return [];
        }

        $doc = $this->rqa_xlsx_dom($xml);
        $xpath = new DOMXPath($doc);
        $xpath->registerNamespace('m', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

        $strings = [];
        foreach ($xpath->query('//m:si') as $siNode) {
            $text = '';
            foreach ($xpath->query('.//m:t', $siNode) as $textNode) {
                $text .= $textNode->nodeValue;
            }
            $strings[] = $text;
        }

        return $strings;
    }

    private function rqa_xlsx_first_sheet_path(ZipArchive $zip)
    {
        $workbookXml = $zip->getFromName('xl/workbook.xml');
        $relsXml = $zip->getFromName('xl/_rels/workbook.xml.rels');

        if ($workbookXml !== false && $relsXml !== false) {
            $workbook = $this->rqa_xlsx_dom($workbookXml);
            $workbookXpath = new DOMXPath($workbook);
            $workbookXpath->registerNamespace('m', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
            $workbookXpath->registerNamespace('r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');

            $sheet = $workbookXpath->query('//m:sheets/m:sheet')->item(0);
            if ($sheet) {
                $relId = $sheet->getAttributeNS('http://schemas.openxmlformats.org/officeDocument/2006/relationships', 'id');
                if ($relId !== '') {
                    $rels = $this->rqa_xlsx_dom($relsXml);
                    $relsXpath = new DOMXPath($rels);
                    $relsXpath->registerNamespace('rel', 'http://schemas.openxmlformats.org/package/2006/relationships');

                    foreach ($relsXpath->query('//rel:Relationship') as $rel) {
                        if ($rel->getAttribute('Id') === $relId) {
                            $target = $rel->getAttribute('Target');
                            if ($target !== '') {
                                $target = ltrim($target, '/');
                                return strpos($target, 'xl/') === 0 ? $target : 'xl/' . $target;
                            }
                        }
                    }
                }
            }
        }

        if ($zip->locateName('xl/worksheets/sheet1.xml') !== false) {
            return 'xl/worksheets/sheet1.xml';
        }

        throw new Exception('No worksheet was found in the uploaded XLSX file.');
    }

    private function rqa_xlsx_dom($xml)
    {
        $doc = new DOMDocument();
        $previous = libxml_use_internal_errors(true);
        $loaded = $doc->loadXML($xml);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (!$loaded) {
            throw new Exception('The uploaded XLSX file contains invalid worksheet XML.');
        }

        return $doc;
    }

    private function rqa_xlsx_col_index($cellRef)
    {
        if (!preg_match('/^([A-Z]+)/i', (string) $cellRef, $matches)) {
            return -1;
        }

        $letters = strtoupper($matches[1]);
        $index = 0;
        for ($i = 0; $i < strlen($letters); $i++) {
            $index = ($index * 26) + (ord($letters[$i]) - 64);
        }

        return $index - 1;
    }

    private function rqa_xlsx_cell_value(DOMXPath $xpath, DOMElement $cellNode, $sharedStrings)
    {
        $type = $cellNode->getAttribute('t');

        if ($type === 'inlineStr') {
            $text = '';
            foreach ($xpath->query('.//m:is//m:t', $cellNode) as $textNode) {
                $text .= $textNode->nodeValue;
            }
            return trim($text);
        }

        $valueNode = $xpath->query('m:v', $cellNode)->item(0);
        if (!$valueNode) {
            return '';
        }

        $value = $valueNode->nodeValue;
        if ($type === 's') {
            $index = (int) $value;
            return trim((string) ($sharedStrings[$index] ?? ''));
        }

        return trim((string) $value);
    }

    /**
     * SDS approval screen (Approving Officer).
     * Lists the applicants that were recommended and are pending approval.
     * The SDS can Approve or Decline each one. Same look & feel as the
     * RQA Recommendation page. See rqa_approval_data() / rqa_approval_action().
     */
    public function rqa_approval()
    {
        if ($this->session->logged_in == false) {
            redirect(base_url() . 'log_in');
            return;
        }
        // Any logged-in position may view the List of Issuance page.

        $page = "rqa_approval";
        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $this->ensure_rqa_recommendation_table();

        $data = [
            'title' => 'RQA Recommended Applicants - For Approval',
            'jobTypeSuffixes' => $this->rqa_recommendation_job_suffixes(),
        ];

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    /**
     * Returns the pending (recommended) applicants as JSON for the SDS
     * approval screen. Filtering by Position/Municipality/Barangay happens
     * client-side from this payload.
     */
    public function rqa_approval_data()
    {
        header('Content-Type: application/json');

        if ($this->session->logged_in == false) {
            echo json_encode(['status' => 'error', 'message' => 'You are not authorised to view this list.']);
            return;
        }

        $this->ensure_rqa_recommendation_table();

        $suffixes = $this->rqa_recommendation_job_suffixes();
        $rows = [];

        foreach ($this->Page_model->recommended_for_approval('recommended') as $row) {
            $jobType = (int) ($row->job_type ?? 0);
            $specializationKind = $this->rqa_specialization_kind($jobType);
            $suffix = $suffixes[$jobType] ?? '';
            $name = trim((string) ($row->rec_name ?? ''));
            if ($name === '') {
                $name = rqa_applicant_name($row);
            }

            $rawSpecialization = trim((string) $this->rqa_row_specialization($row, $jobType));
            $strand = $specializationKind === 'shs' ? trim((string) ($row->shss ?? '')) : '';
            $major = $specializationKind === 'shs' ? trim((string) ($row->Major ?? '')) : '';
            $jhsGroup = $specializationKind === 'jhs' ? $this->rqa_jhs_specialization_group($rawSpecialization) : '';
            $displaySpecialization = $specializationKind === 'jhs' ? $jhsGroup : ($specializationKind === 'shs' ? $major : $rawSpecialization);

            $rows[] = [
                'recId' => (int) ($row->rec_id ?? 0),
                'appID' => (int) ($row->appID ?? 0),
                'jobID' => (int) ($row->jobID ?? 0),
                'jobType' => $jobType,
                'specializationKind' => $specializationKind,
                'position' => trim(($row->jobTitle ?? '') . ' ' . $suffix),
                'code' => (string) ($row->code ?? ''),
                'name' => $name,
                'itemNumber' => (string) ($row->item_number ?? ''),
                'remarks' => (string) ($row->remarks ?? ''),
                'school' => (string) ($row->school_name ?? ''),
                'municipality' => trim((string) ($row->resCity ?? '')),
                'brgy' => trim((string) ($row->brgy ?? '')),
                'specialization' => $displaySpecialization,
                'rawSpecialization' => $rawSpecialization,
                'specializationGroup' => $jhsGroup,
                'strand' => $strand,
                'major' => $major,
                'education' => $this->rqa_clean_score($row->education ?? null),
                'training' => $this->rqa_clean_score($row->training ?? null),
                'experience' => $this->rqa_clean_score($row->experience ?? null),
                'let_rating' => $this->rqa_clean_score($row->let_rating ?? null),
                'demo_rating' => $this->rqa_clean_score($row->demo_rating ?? null),
                'tr_rating' => $this->rqa_clean_score($row->tr_rating ?? null),
                'total_points' => !empty($row->total_points)
                    ? number_format((float) $row->total_points, 2, '.', '')
                    : (!empty($row->rec_total) ? number_format((float) $row->rec_total, 2, '.', '') : ''),
            ];
        }

        echo json_encode(['status' => 'success', 'rows' => $rows]);
    }

    /**
     * Approve or Decline a recommended applicant (AJAX, JSON).
     * Approve  -> marks the recommendation as approved (item number stays locked).
     * Decline  -> removes the recommendation, freeing the Item Number and
     *             returning the applicant to the recommender's list.
     */
    public function rqa_approval_action()
    {
        header('Content-Type: application/json');

        if ($this->session->logged_in == false || $this->session->position !== 'sds') {
            echo json_encode(['status' => 'error', 'message' => 'You are not authorised to perform this action.']);
            return;
        }

        $this->ensure_rqa_recommendation_table();

        $recId = (int) $this->input->post('rec_id');
        $action = trim((string) $this->input->post('action'));

        if ($recId <= 0 || !in_array($action, ['approve', 'decline'], true)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
            return;
        }

        $rec = $this->Common->one_cond_row('hris_rqa_recommendation', 'id', $recId);
        if (empty($rec)) {
            echo json_encode(['status' => 'error', 'message' => 'This recommendation no longer exists.']);
            return;
        }
        if (isset($rec->status) && $rec->status !== 'recommended') {
            echo json_encode(['status' => 'error', 'message' => 'This applicant has already been processed.']);
            return;
        }

        $userId = $this->session->id ?? $this->session->userdata('id');

        if ($action === 'approve') {
            $this->db->where('id', $recId)->update('hris_rqa_recommendation', [
                'status' => 'approved',
                'approved_by' => $userId ? (int) $userId : null,
                'approved_at' => date('Y-m-d H:i:s'),
            ]);
            echo json_encode(['status' => 'success', 'message' => 'Applicant approved.']);
            return;
        }

        // Decline -> remove so the Item Number is freed and the applicant returns to the pool
        $this->db->where('id', $recId)->delete('hris_rqa_recommendation');
        echo json_encode(['status' => 'success', 'message' => 'Applicant declined.']);
    }

    /**
     * List of Issuance screen (SDS). Standalone page that lists the approved
     * applicants for issuance. Data comes from rqa_issuance_data().
     */
    public function rqa_issuance()
    {
        if ($this->session->logged_in == false) {
            redirect(base_url() . 'log_in');
            return;
        }
        // Any logged-in position may view the printable List of Issuance report.

        $page = "rqa_issuance";
        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $this->ensure_rqa_recommendation_table();

        $data = [
            'title' => 'List of Issuance',
            'settings' => $this->SettingsModel->get_mis_settings(),
        ];

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    /**
     * List of Issuance (approved applicants) for the SDS screen, JSON.
     * Includes contact info and the assigned school; no scores. Each row can
     * have a Date Hired and (if the applicant waived the post) a Date Waived.
     */
    public function rqa_issuance_data()
    {
        header('Content-Type: application/json');

        if ($this->session->logged_in == false || $this->session->position !== 'sds') {
            echo json_encode(['status' => 'error', 'message' => 'You are not authorised to view this list.']);
            return;
        }

        $this->ensure_rqa_recommendation_table();

        $suffixes = $this->rqa_recommendation_job_suffixes();
        $rows = [];

        // Item Numbers currently held by an ACTIVE (non-waived) record. A
        // waived row whose Item Number is in this set has been reused, so its
        // waiver can no longer be undone (the button is hidden client-side).
        $activeItems = [];
        foreach ($this->db->select('item_number')->where('status !=', 'waived')->get('hris_rqa_recommendation')->result() as $ai) {
            $activeItems[(string) $ai->item_number] = true;
        }

        // Issued (approved) applicants plus any that have waived the post -
        // waived rows stay on the list so the waiver can be reviewed/undone.
        foreach ($this->Page_model->recommended_for_approval(['approved', 'waived']) as $row) {
            $jobType = (int) ($row->job_type ?? 0);
            $suffix = $suffixes[$jobType] ?? '';
            $name = trim((string) ($row->rec_name ?? ''));
            if ($name === '') {
                $name = rqa_applicant_name($row);
            }

            $dateHired = (!empty($row->date_hired) && $row->date_hired !== '0000-00-00') ? $row->date_hired : '';
            $dateWaived = (!empty($row->date_waived) && $row->date_waived !== '0000-00-00') ? $row->date_waived : '';

            $itemNumber = (string) ($row->item_number ?? '');
            $status = (string) ($row->status ?? '');
            // A waiver can only be undone while its Item Number is still free
            $canUndo = ($status === 'waived') && empty($activeItems[$itemNumber]);

            $rows[] = [
                'recId' => (int) ($row->rec_id ?? 0),
                'position' => trim(($row->jobTitle ?? '') . ' ' . $suffix),
                'code' => (string) ($row->code ?? ''),
                'name' => $name,
                'contact' => (string) ($row->contactNo ?? ''),
                'email' => (string) ($row->empEmail ?? ''),
                'municipality' => trim((string) ($row->resCity ?? '')),
                'brgy' => trim((string) ($row->brgy ?? '')),
                'itemNumber' => $itemNumber,
                'school' => (string) ($row->school_name ?? ''),
                'dateHired' => (string) $dateHired,
                'dateWaived' => (string) $dateWaived,
                'status' => $status,
                'canUndo' => $canUndo,
            ];
        }

        echo json_encode(['status' => 'success', 'rows' => $rows]);
    }

    /**
     * Update a Date Hired or Date Waived value on an approved (issued) record.
     */
    public function rqa_issuance_update()
    {
        header('Content-Type: application/json');

        if ($this->session->logged_in == false || $this->session->position !== 'sds') {
            echo json_encode(['status' => 'error', 'message' => 'You are not authorised to perform this action.']);
            return;
        }

        $this->ensure_rqa_recommendation_table();

        $recId = (int) $this->input->post('rec_id');
        $field = trim((string) $this->input->post('field'));
        $value = trim((string) $this->input->post('value'));

        $allowed = ['date_hired' => 'Date Hired', 'date_waived' => 'Date Waived'];
        if ($recId <= 0 || !isset($allowed[$field])) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
            return;
        }

        // Accept an empty value (clear the date) or a valid YYYY-MM-DD date
        if ($value !== '') {
            $d = DateTime::createFromFormat('Y-m-d', $value);
            if (!$d || $d->format('Y-m-d') !== $value) {
                echo json_encode(['status' => 'error', 'message' => 'Please enter a valid date.']);
                return;
            }
        }

        $rec = $this->Common->one_cond_row('hris_rqa_recommendation', 'id', $recId);
        if (empty($rec)) {
            echo json_encode(['status' => 'error', 'message' => 'This record is not available for issuance.']);
            return;
        }

        // Date Hired belongs to an issued (approved) record; Date Waived can
        // only be adjusted once the applicant has actually been marked waived.
        if ($field === 'date_hired' && $rec->status !== 'approved') {
            echo json_encode(['status' => 'error', 'message' => 'This record is not available for issuance.']);
            return;
        }
        if ($field === 'date_waived' && $rec->status !== 'waived') {
            echo json_encode(['status' => 'error', 'message' => 'Mark the applicant as Waived before setting a Date Waived.']);
            return;
        }

        $this->db->where('id', $recId)->update('hris_rqa_recommendation', [
            $field => ($value !== '' ? $value : null),
        ]);

        echo json_encode([
            'status' => 'success',
            'message' => $allowed[$field] . ' saved.',
            'value' => $value,
        ]);
    }

    /**
     * Mark an issued (approved) applicant as having WAIVED the post.
     * Sets status = 'waived' and records the Date Waived. The record is kept
     * (so the waiver can be undone) but its Item Number is now free to be
     * recommended to another applicant on the RQA Recommendation page.
     */
    public function rqa_issuance_waive()
    {
        header('Content-Type: application/json');

        if ($this->session->logged_in == false || $this->session->position !== 'sds') {
            echo json_encode(['status' => 'error', 'message' => 'You are not authorised to perform this action.']);
            return;
        }

        $this->ensure_rqa_recommendation_table();

        $recId = (int) $this->input->post('rec_id');
        $value = trim((string) $this->input->post('value'));
        if ($recId <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
            return;
        }

        // Default to today, otherwise accept a valid YYYY-MM-DD date
        if ($value === '') {
            $value = date('Y-m-d');
        } else {
            $d = DateTime::createFromFormat('Y-m-d', $value);
            if (!$d || $d->format('Y-m-d') !== $value) {
                echo json_encode(['status' => 'error', 'message' => 'Please enter a valid date.']);
                return;
            }
        }

        $rec = $this->Common->one_cond_row('hris_rqa_recommendation', 'id', $recId);
        if (empty($rec) || $rec->status !== 'approved') {
            echo json_encode(['status' => 'error', 'message' => 'This record is not available for issuance.']);
            return;
        }

        $this->db->where('id', $recId)->update('hris_rqa_recommendation', [
            'status' => 'waived',
            'date_waived' => $value,
        ]);

        echo json_encode([
            'status' => 'success',
            'message' => 'Applicant marked as waived. Item Number "' . $rec->item_number . '" is available again.',
            'value' => $value,
        ]);
    }

    /**
     * Undo a waiver (e.g. the Waived button was clicked by mistake).
     * Restores status = 'approved' and clears the Date Waived. This is blocked
     * if the freed Item Number has since been re-assigned to another active
     * applicant, otherwise two applicants would share the same Item Number.
     */
    public function rqa_issuance_unwaive()
    {
        header('Content-Type: application/json');

        if ($this->session->logged_in == false || $this->session->position !== 'sds') {
            echo json_encode(['status' => 'error', 'message' => 'You are not authorised to perform this action.']);
            return;
        }

        $this->ensure_rqa_recommendation_table();

        $recId = (int) $this->input->post('rec_id');
        if ($recId <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
            return;
        }

        $rec = $this->Common->one_cond_row('hris_rqa_recommendation', 'id', $recId);
        if (empty($rec) || $rec->status !== 'waived') {
            echo json_encode(['status' => 'error', 'message' => 'This record is not currently waived.']);
            return;
        }

        // Make sure the Item Number was not already reused by another applicant
        $reused = $this->db->where('item_number', $rec->item_number)
            ->where('status !=', 'waived')
            ->where('id !=', $recId)
            ->get('hris_rqa_recommendation')->row();
        if (!empty($reused)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Item Number "' . $rec->item_number . '" has already been re-assigned to another applicant, so this waiver can no longer be undone.',
            ]);
            return;
        }

        $this->db->where('id', $recId)->update('hris_rqa_recommendation', [
            'status' => 'approved',
            'date_waived' => null,
        ]);

        echo json_encode(['status' => 'success', 'message' => 'Waiver undone. The applicant is back on the List of Issuance.']);
    }

    /**
     * Printable List of Issuance report (separate page). Covers only issued
     * applicants with a Hired status (approved + Date Hired) and Waived ones,
     * grouped by school. The view provides Position and Status filters.
     */
    public function rqa_issuance_report()
    {
        if ($this->session->logged_in == false) {
            redirect(base_url() . 'log_in');
            return;
        }
        if ($this->session->position !== 'sds') {
            show_error('Forbidden', 403);
            return;
        }

        $page = "rqa_issuance_report";
        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $this->ensure_rqa_recommendation_table();

        $suffixes = $this->rqa_recommendation_job_suffixes();
        $rows = [];

        foreach ($this->Page_model->recommended_for_approval(['approved', 'waived']) as $row) {
            $jobType = (int) ($row->job_type ?? 0);
            $suffix = $suffixes[$jobType] ?? '';
            $name = trim((string) ($row->rec_name ?? ''));
            if ($name === '') {
                $name = rqa_applicant_name($row);
            }

            $dateHired = (!empty($row->date_hired) && $row->date_hired !== '0000-00-00') ? $row->date_hired : '';
            $dateWaived = (!empty($row->date_waived) && $row->date_waived !== '0000-00-00') ? $row->date_waived : '';
            $status = (string) ($row->status ?? '');

            // Only Hired (approved with a Date Hired) and Waived rows belong in
            // this report; skip approved rows still pending a Date Hired.
            if ($status === 'waived') {
                $reportStatus = 'waived';
            } elseif ($status === 'approved' && $dateHired !== '') {
                $reportStatus = 'hired';
            } else {
                continue;
            }

            $rows[] = [
                'position' => trim(($row->jobTitle ?? '') . ' ' . $suffix),
                'code' => (string) ($row->code ?? ''),
                'name' => $name,
                'contact' => (string) ($row->contactNo ?? ''),
                'email' => (string) ($row->empEmail ?? ''),
                'itemNumber' => (string) ($row->item_number ?? ''),
                'school' => (string) ($row->school_name ?? ''),
                'dateHired' => (string) $dateHired,
                'dateWaived' => (string) $dateWaived,
                'reportStatus' => $reportStatus,
            ];
        }

        $data = [
            'title' => 'List of Issuance Report',
            'settings' => $this->SettingsModel->get_mis_settings(),
            'rows' => $rows,
        ];

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function car_rqa_non()
    {
        $page = "car_rqa_form";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $jobID = $this->uri->segment(3);
        $data['job'] = $this->Page_model->get_single_row_by_id('hris_jobvacancy', 'jobID', $jobID);
        $data['car'] = $this->Common->rqa('hris_rating_none', $jobID);
        
        $job = $this->Common->one_cond_row_select('hris_jobvacancy','jobID,sy,sign', 'jobID', $jobID);
        $data['sign'] = $this->get_rqa_sign($job);

        $data['title'] = "COMPARATIVE ASSESSMENT RESULT - REGISTRY OF QUALIFIED APPLICANTS (CAR - RQA)";

        $this->load->view('pages/' . $page, $data);
    }



    public function car_rqa_administrative()
    {
        $page = "car_rqa_form_administrative";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $jobID = $this->uri->segment(3);
        $data['job'] = $this->Page_model->get_single_row_by_id('hris_jobvacancy', 'jobID', $jobID);
        $data['car'] = $this->Common->rqa_non($jobID);
        $job = $this->Common->one_cond_row_select('hris_jobvacancy','jobID,sy,sign,ttype', 'jobID', $jobID);
        $data['sign'] = $this->get_rqa_sign($job, 0);

        
        if($job->ttype == 1){
            $data['title'] = "COMPARATIVE ASSESSMENT RESULT FOR EXPANDED RECLASSIFICATION (CAReER)";
        }else{
            $data['title'] = "COMPARATIVE ASSESSMENT RESULT (CAR)";
        }


        $this->load->view('pages/' . $page, $data);
    }

    public function car_rqa_administrative_posting()
    {
        $page = "car_rqa_form_administrative_posting";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $jobID = $this->uri->segment(3);
        $data['job'] = $this->Page_model->get_single_row_by_id('hris_jobvacancy', 'jobID', $jobID);
        $data['car'] = $this->Common->rqa_non($jobID);
        $job = $this->Common->one_cond_row_select('hris_jobvacancy','jobID,sy,sign,ttype', 'jobID', $jobID);
        $data['sign'] = $this->get_rqa_sign($job, 0);


        if($job->ttype == 1){
            $data['title'] = "COMPARATIVE ASSESSMENT RESULT FOR EXPANDED RECLASSIFICATION (CAReER)";
        }else{
            $data['title'] = "COMPARATIVE ASSESSMENT RESULT (CAR)";
        }


        $this->load->view('pages/' . $page, $data);
    }

    public function car_rqa_administrative_region()
    {
        $page = "car_rqa_form_for_region";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $jobID = $this->uri->segment(3);
        $data['job'] = $this->Page_model->get_single_row_by_id('hris_jobvacancy', 'jobID', $jobID);
        $data['car'] = $this->Common->rqa_non($jobID);
        $job = $this->Common->one_cond_row_select('hris_jobvacancy','jobID,sy,sign,ttype', 'jobID', $jobID);
        $data['sign'] = $this->get_rqa_sign($job, 0);


        $data['title'] = "COMPARATIVE ASSESSMENT RESULT FOR EXPANDED RECLASSIFICATION (CAReER)";
       


        $this->load->view('pages/' . $page, $data);
    }


   

    public function car_rqa_related()
    {
        $page = "car_rqa_form_related";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $jobID = $this->uri->segment(3);
        $data['job'] = $this->Page_model->get_single_row_by_id('hris_jobvacancy', 'jobID', $jobID);
        $data['car'] = $this->Common->rqa_non($jobID);
        $job = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $jobID);
        $data['sign'] = $this->get_rqa_sign($job, 0);

        $data['title'] = "COMPARATIVE ASSESSMENT RESULT (CAR)";

        $this->load->view('pages/' . $page, $data);
    }

    public function car_rqa_related_posting()
    {
        $page = "car_rqa_form_related_posting";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $jobID = $this->uri->segment(3);
        $data['job'] = $this->Page_model->get_single_row_by_id('hris_jobvacancy', 'jobID', $jobID);
        $data['car'] = $this->Common->rqa_non($jobID);
        $job = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $jobID);
        $data['sign'] = $this->get_rqa_sign($job, 0);

        $data['title'] = "COMPARATIVE ASSESSMENT RESULT (CAR)";

        $this->load->view('pages/' . $page, $data);
    }

    public function car_rqa1_none()
    {
        $page = "car_rqa_form1_none";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }


        $jobID = $this->uri->segment(3);
        $data['job'] = $this->Page_model->get_single_row_by_id('hris_jobvacancy', 'jobID', $jobID);
        $data['car'] = $this->Common->rqa('hris_rating_none', $jobID);
        $job = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $jobID);
        $data['sign'] = $this->get_rqa_sign($job, 0, false);

        $data['title'] = "COMPARATIVE ASSESSMENT RESULT - REGISTRY OF QUALIFIED APPLICANTS (CAR - RQA)";

        $this->load->view('pages/' . $page, $data);
    }

    public function car_rqa1()
{
    $page = "car_rqa_form1";

    if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
        show_404();
    }

    $jobID = $this->uri->segment(3);
    $data['job'] = $this->Page_model->get_single_row_by_id('hris_jobvacancy', 'jobID', $jobID);
    $data['car'] = $this->Page_model->rqa($jobID);
    $data['settings'] = $this->SettingsModel->get_mis_settings();

    $job = $this->Common->one_cond_row_select('hris_jobvacancy','jobID,sy,sign,ttype', 'jobID', $jobID);
    $data['sign'] = $this->get_rqa_sign($job);

    // Preload all applicants and staff
    $data['all_applicants'] = $this->Page_model->get_all_applicants_indexed(); // record_no => object
    $data['all_staff'] = $this->Page_model->get_all_staff_indexed(); // IDNumber => object

    if($job->ttype == 1){
        $data['title'] = "COMPARATIVE ASSESSMENT RESULT FOR EXPANDED RECLASSIFICATION (CAReER)";
    }else{
        $data['title'] = "COMPARATIVE ASSESSMENT RESULT - REGISTRY OF QUALIFIED APPLICANTS (CAR - RQA)";
    }
    

    $this->render_rqa_report($page, $data, $this->rqa_excel_filename('car-rqa-posting', $jobID));
}

public function car_rqa1_promotion()
{
    ob_start(); // Start streaming to browser

    $page = "car_rqa_form1_promotion";

    if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
        show_404();
    }

    $jobID = $this->uri->segment(3);
    $data['job'] = $this->Page_model->get_single_row_by_id('hris_jobvacancy', 'jobID', $jobID);
    $data['car'] = $this->Page_model->rqa_promotion($jobID);
    $data['settings'] = $this->SettingsModel->get_mis_settings();

    $job = $this->Common->one_cond_row_select('hris_jobvacancy','jobID,sy,sign,ttype', 'jobID', $jobID);
    $data['sign'] = $this->get_rqa_sign($job);

    // Preload all applicants and staff
    $data['all_applicants'] = $this->Page_model->get_all_applicants_indexed(); // record_no => object
    $data['all_staff'] = $this->Page_model->get_all_staff_indexed(); // IDNumber => object

    $data['title'] = "COMPARATIVE ASSESSMENT RESULT FOR EXPANDED RECLASSIFICATION (CAReER)";
   
    

    $this->load->view('pages/' . $page, $data);

    ob_end_flush(); // Send output
}

public function car_rqa1_promotion_region()
{
    ob_start(); // Start streaming to browser

    $page = "car_rqa_form_promotion_for_region";

    if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
        show_404();
    }

    $jobID = $this->uri->segment(3);
    $data['job'] = $this->Page_model->get_single_row_by_id('hris_jobvacancy', 'jobID', $jobID);
    $data['car'] = $this->Page_model->rqa_promotion($jobID);
    $data['settings'] = $this->SettingsModel->get_mis_settings();

    $job = $this->Common->one_cond_row_select('hris_jobvacancy','jobID,sy,sign,ttype', 'jobID', $jobID);
    $data['sign'] = $this->get_rqa_sign($job);

    // Preload all applicants and staff
    $data['all_applicants'] = $this->Page_model->get_all_applicants_indexed(); // record_no => object
    $data['all_staff'] = $this->Page_model->get_all_staff_indexed(); // IDNumber => object

    $data['title'] = "COMPARATIVE ASSESSMENT RESULT FOR EXPANDED RECLASSIFICATION (CAReER)";
   
    

    $this->load->view('pages/' . $page, $data);

    ob_end_flush(); // Send output
}




    public function car()
    {
        $page = "car_rqa_form";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $jobID = $this->uri->segment(3);
        $data['job'] = $this->Page_model->get_single_row_by_id('hris_jobvacancy', 'jobID', $jobID);
        $data['car'] = $this->Page_model->car($jobID);
        $data['settings'] = $this->SettingsModel->get_mis_settings();
        $job = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $jobID);
        $data['sign'] = $this->get_rqa_sign($job, 0, false);

        $data['title'] = "COMPARATIVE ASSESSMENT RESULT (CAR)";

        $this->render_rqa_report($page, $data, $this->rqa_excel_filename('car', $jobID));
    }

    public function car_nlet()
    {
        $page = "car_rqa_form";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $jobID = $this->uri->segment(3);
        $data['job'] = $this->Page_model->get_single_row_by_id('hris_jobvacancy', 'jobID', $jobID);
        $data['car'] = $this->Page_model->car_nlet($jobID);
        $job = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $jobID);
        $data['sign'] = $this->get_rqa_sign($job, 0, false);
        $data['settings'] = $this->SettingsModel->get_mis_settings();

        $data['title'] = "COMPARATIVE ASSESSMENT RESULT - REGISTRY OF QUALIFIED APPLICANTS (CAR - RQA)";

        $this->render_rqa_report($page, $data, $this->rqa_excel_filename('car-nlet', $jobID));
    }

    public function car_nlet_pos()
    {
        $page = "car_rqa_form_nl_posting";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $jobID = $this->uri->segment(3);
        $data['job'] = $this->Page_model->get_single_row_by_id('hris_jobvacancy', 'jobID', $jobID);
        $data['car'] = $this->Page_model->car_nlet($jobID);
        $job = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $jobID);
        $data['sign'] = $this->get_rqa_sign($job, 0, false);
        $data['settings'] = $this->SettingsModel->get_mis_settings();

        $data['title'] = "COMPARATIVE ASSESSMENT RESULT - REGISTRY OF QUALIFIED APPLICANTS (CAR - RQA)";

        $this->render_rqa_report($page, $data, $this->rqa_excel_filename('car-nlet-posting', $jobID));
    }

    public function rqa_specialization_print()
    {
        $page = "car_rqa_form_special";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $jobID = $this->input->get('id');
        $spe = $this->input->get('spec');
        $data['job'] = $this->Page_model->get_single_row_by_id('hris_jobvacancy', 'jobID', $jobID);
        $data['car'] = $this->Page_model->rqa_specialization($jobID, $spe);

        $job = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $jobID);
        $data['sign'] = $this->get_rqa_sign($job, 0, false);

        $data['title'] = "COMPARATIVE ASSESSMENT RESULT - REGISTRY OF QUALIFIED APPLICANTS (CAR - RQA)";

        $this->load->view('pages/' . $page, $data);
    }

    public function rqa_specialization_print1()
    {
        $page = "car_rqa_form_special_posting1";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $jobID = $this->input->get('id');
        $spe = $this->input->get('spec');
        $data['job'] = $this->Page_model->get_single_row_by_id('hris_jobvacancy', 'jobID', $jobID);
        $data['car'] = $this->Page_model->rqa_specialization($jobID, $spe);

        $job = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $jobID);
        $data['sign'] = $this->get_rqa_sign($job, 0, false);

        $data['title'] = "COMPARATIVE ASSESSMENT RESULT - REGISTRY OF QUALIFIED APPLICANTS (CAR - RQA)";

        $this->load->view('pages/' . $page, $data);
    }

    public function rqa_cluster()
    {
        $page = "rqacluster";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $data['title'] = "COMPARATIVE ASSESSMENT RESULT-REGISTRY OF QUALIFIED APPLICANTS (CAR RQA)";

        $data['data'] = $this->Common->one_cond_group('hris_applications', 'jobID', $this->uri->segment(3), 'district');


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function rqa_clusterv2()
    {
        $page = "rqaclusterv2";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $data['title'] = "COMPARATIVE ASSESSMENT RESULT-REGISTRY OF QUALIFIED APPLICANTS (CAR RQA)";

        $data['data'] = $this->Common->one_cond_group('hris_applications', 'jobID', $this->uri->segment(3), 'district');


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }


    public function rqa_cluster_jhs()
    {
        $page = "rqacluster_jhs";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $data['title'] = "COMPARATIVE ASSESSMENT RESULT-REGISTRY OF QUALIFIED APPLICANTS (CAR RQA)";

        $data['data'] = $this->Page_model->cluster_jhs();


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }
    public function rqa_cluster_jhsv2()
    {
        $page = "rqacluster_jhsv2";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $data['title'] = "COMPARATIVE ASSESSMENT RESULT-REGISTRY OF QUALIFIED APPLICANTS (CAR RQA)";

        $data['data'] = $this->Page_model->cluster_jhs();


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function rqa_cluster_shs()
    {
        $page = "rqacluster_shs";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $data['title'] = "By Cluster";

        $data['data'] = $this->Page_model->cluster_shs();


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function rqa_cluster_shs_mun()
    {
        $page = "rqacluster_shs_mun";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $data['title'] = "By Cluster";

        $data['data'] = $this->Page_model->cluster_shs();


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }
    
    public function rqa_cluster_shsv2()
    {
        $page = "rqacluster_shsv2";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $data['title'] = "By Cluster";

        $data['data'] = $this->Page_model->cluster_shs();


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

   public function rqa_cluster_list()
{
    $data['title'] = "COMPARATIVE ASSESSMENT RESULT-REGISTRY OF QUALIFIED APPLICANTS (CAR RQA)";
    $data['st'] = $this->input->get('district');

    $jobID = $this->uri->segment(3);
    $data['car'] = $this->Page_model->rqa_cluster($jobID, $data['st']);
    $data['job'] = $this->Page_model->get_single_row_by_id('hris_jobvacancy', 'jobID', $jobID);
    $job = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $jobID);
    $data['sign'] = $this->get_rqa_sign($job, 0, false);
    $data['settings'] = $this->SettingsModel->get_mis_settings();

    $data['applicants'] = $this->Page_model->get_all_applicants_indexed();

    $this->render_rqa_report(
        'car_rqa_form_district',
        $data,
        $this->rqa_excel_filename('rqa-cluster-list', $jobID, $data['st'])
    );
}

public function rqa_cluster_list_np()
{
    $data['title'] = "COMPARATIVE ASSESSMENT RESULT-REGISTRY OF QUALIFIED APPLICANTS (CAR RQA)";
    $data['st'] = $this->input->get('district');

    $jobID = $this->uri->segment(3);
    $data['car'] = $this->Page_model->rqa_cluster($jobID, $data['st']);
    $data['job'] = $this->Page_model->get_single_row_by_id('hris_jobvacancy', 'jobID', $jobID);
    $job = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $jobID);
    $data['sign'] = $this->get_rqa_sign($job, 0, false);
    $data['settings'] = $this->SettingsModel->get_mis_settings();

    $data['applicants'] = $this->Page_model->get_all_applicants_indexed();

    $this->render_rqa_report(
        'car_rqa_form_np',
        $data,
        $this->rqa_excel_filename('rqa-cluster-list-np', $jobID, $data['st'])
    );
}

    public function job_vacancy_for_sds_only()
    {
        $data['title'] = "List of Job Vacancy";
        $data['data'] = $this->Common->one_cond('hris_jobvacancy', 'jvStatus', 'Open');
        $this->load->view('pages/job_list', $data);
    }

    public function rqa_clusterv3()
    {
        $page = "rqaclusterv3";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $data['title'] = "COMPARATIVE ASSESSMENT RESULT-REGISTRY OF QUALIFIED APPLICANTS (CAR RQA)";

        $data['data'] = $this->Common->one_cond_group('hris_applications', 'jobID', $this->uri->segment(3), 'district');


        $this->load->view('pages/' . $page, $data);
    }

    public function rqa_cluster_jhsv3()
    {
        $page = "rqacluster_jhsv3";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $data['title'] = "COMPARATIVE ASSESSMENT RESULT-REGISTRY OF QUALIFIED APPLICANTS (CAR RQA)";

        $data['data'] = $this->Page_model->cluster_jhs();


        $this->load->view('pages/' . $page, $data);
    }

    public function rqa_cluster_shsv3()
    {
        $page = "rqaclusterv4";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $data['title'] = "By Cluster";

        $data['data'] = $this->Page_model->cluster_shs();


        $this->load->view('pages/' . $page, $data);
    }




    public function rqa_cluster_list_jhs()
    {
        $data['title'] = "COMPARATIVE ASSESSMENT RESULT-REGISTRY OF QUALIFIED APPLICANTS (CAR RQA)";
        $data['st'] = $this->input->get('s');

        $data['car'] = $this->Page_model->rqa_cluster_jhs($this->uri->segment(3), $this->input->get('s'));
        $data['job'] = $this->Page_model->get_single_row_by_id('hris_jobvacancy', 'jobID', $this->uri->segment(3));
        $job = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $this->uri->segment(3));
        $data['sign'] = $this->get_rqa_sign($job, 0, false);
        $this->load->view('pages/car_rqa_form_jhs', $data);
    }

    public function rqa_cluster_list_jhsv2()
    {
        $data['title'] = "COMPARATIVE ASSESSMENT RESULT-REGISTRY OF QUALIFIED APPLICANTS (CAR RQA)";
        $data['st'] = $this->input->get('s');

        $data['car'] = $this->Page_model->rqa_cluster_jhs($this->uri->segment(3), $this->input->get('s'));
        $data['job'] = $this->Page_model->get_single_row_by_id('hris_jobvacancy', 'jobID', $this->uri->segment(3));
        $job = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $this->uri->segment(3));
        $data['sign'] = $this->get_rqa_sign($job, 0, false);
                $data['settings'] = $this->SettingsModel->get_mis_settings();
        $this->load->view('pages/car_rqa_form_jhsv2', $data);
    }

    public function rqa_cluster_list_shs()
    {
        $data['title'] = "COMPARATIVE ASSESSMENT RESULT-REGISTRY OF QUALIFIED APPLICANTS (CAR RQA)";
        $data['st'] = $this->input->get('s');

        $data['car'] = $this->Page_model->rqa_cluster_shs($this->uri->segment(3), $this->input->get('s'));
        $data['job'] = $this->Page_model->get_single_row_by_id('hris_jobvacancy', 'jobID', $this->uri->segment(3));
        $job = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $this->uri->segment(3));
        $data['sign'] = $this->get_rqa_sign($job, 0, false);
                $data['settings'] = $this->SettingsModel->get_mis_settings();
        $this->load->view('pages/car_rqa_form_jhs', $data);
    }

    public function rqa_cluster_list_shs_mun()
    {
        $data['title'] = "COMPARATIVE ASSESSMENT RESULT-REGISTRY OF QUALIFIED APPLICANTS (CAR RQA)";
        $data['st'] = $this->input->get('s');

        $data['car'] = $this->Page_model->rqa_cluster_shs_count_mun_print($this->uri->segment(3), $this->input->get('s'),$this->input->get('mun'));
        $data['job'] = $this->Page_model->get_single_row_by_id('hris_jobvacancy', 'jobID', $this->uri->segment(3));

        $job = $this->Common->one_cond_row_select('hris_jobvacancy','jobID,sy,sign', 'jobID', $this->uri->segment(3));
        $data['sign'] = $this->get_rqa_sign($job);
        
        $this->load->view('pages/car_rqa_form_jhs', $data);
    }

    public function rqa_cluster_list_shsv2()
    {
        $data['title'] = "COMPARATIVE ASSESSMENT RESULT-REGISTRY OF QUALIFIED APPLICANTS (CAR RQA)";
        $data['st'] = $this->input->get('s');

        $data['car'] = $this->Page_model->rqa_cluster_shs($this->uri->segment(3), $this->input->get('s'));
        $data['job'] = $this->Page_model->get_single_row_by_id('hris_jobvacancy', 'jobID', $this->uri->segment(3));
        $job = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $this->uri->segment(3));
        $data['sign'] = $this->get_rqa_sign($job, 0, false);
                $data['settings'] = $this->SettingsModel->get_mis_settings();
        $this->load->view('pages/car_rqa_form_jhsv2', $data);
    }


  public function rqa_municipality_print()
{
    $page = "car_rqa_form_special2";
    if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
        show_404();
    }

    $jobID = $this->input->get('id');
    $mun = $this->input->get('mun');

    $data['job'] = $this->Page_model->get_single_row_by_id('hris_jobvacancy', 'jobID', $jobID);
    $data['car'] = $this->Page_model->rqa_mun($jobID, $mun);
    $data['settings'] = $this->SettingsModel->get_mis_settings();

    $job = $this->Common->one_cond_row_select('hris_jobvacancy','jobID,sy,sign', 'jobID', $jobID);
    $data['sign'] = $this->get_rqa_sign($job);

    // Preload applicants and staff
    $data['applicants'] = $this->Page_model->get_all_applicants_indexed();
    $data['staff'] = $this->Page_model->get_all_staff_indexed();

    $data['title'] = "COMPARATIVE ASSESSMENT RESULT - REGISTRY OF QUALIFIED APPLICANTS (CAR - RQA)";

    $this->render_rqa_report($page, $data, $this->rqa_excel_filename('rqa-municipality-print', $jobID, $mun));
}

public function rqa_municipality_print_jhs()
{
    ob_start(); // Stream output to avoid timeout

    $page = "car_rqa_form_special_jhs";
    if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
        show_404();
    }

    $jobID = $this->input->get('id');
    $mun = $this->input->get('mun');

    $data['job'] = $this->Page_model->get_single_row_by_id('hris_jobvacancy', 'jobID', $jobID);
    $data['settings'] = $this->SettingsModel->get_mis_settings();

    $data['grouped_applicants'] = $this->Reg->get_grouped_applicants_by_job($jobID,$mun);

    $job = $this->Common->one_cond_row_select('hris_jobvacancy','jobID,sy,sign', 'jobID', $jobID);
    $data['sign'] = $this->get_rqa_sign($job);

    // Preload applicants and staff
    $data['applicants'] = $this->Page_model->get_all_applicants_indexed();
    $data['staff'] = $this->Page_model->get_all_staff_indexed();

    $data['title'] = "COMPARATIVE ASSESSMENT RESULT (CAR)";

    $this->load->view('pages/' . $page, $data);

    ob_end_flush();
}

public function rqa_municipality_print_jhsv2()
{
    ob_start(); // Stream output to avoid timeout

    $page = "car_rqa_form_special_jhsv2";
    if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
        show_404();
    }

    $jobID = $this->input->get('id');
    $mun = $this->input->get('mun');

    $data['job'] = $this->Page_model->get_single_row_by_id('hris_jobvacancy', 'jobID', $jobID);
    $data['settings'] = $this->SettingsModel->get_mis_settings();

    $data['grouped_applicants'] = $this->Reg->get_grouped_applicants_by_job($jobID,$mun);

    $job = $this->Common->one_cond_row_select('hris_jobvacancy','jobID,sy,sign', 'jobID', $jobID);
    $data['sign'] = $this->get_rqa_sign($job);

    // Preload applicants and staff
    $data['applicants'] = $this->Page_model->get_all_applicants_indexed();
    $data['staff'] = $this->Page_model->get_all_staff_indexed();

    $data['title'] = "COMPARATIVE ASSESSMENT RESULT (CAR)";

    $this->load->view('pages/' . $page, $data);

    ob_end_flush();
}

public function car_rqa_administrative_mun()
{
    ob_start(); // Stream output to avoid timeout

    $page = "car_rqa_form_administrative_mun";
    if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
        show_404();
    }

    $jobID = $this->uri->segment(3);
    //$mun = $this->input->get('mun');

    $data['job'] = $this->Page_model->get_single_row_by_id('hris_jobvacancy', 'jobID', $jobID);
    $data['settings'] = $this->SettingsModel->get_mis_settings();

    $data['grouped_applicants'] = $this->Reg->get_grouped_applicants_by_mun($jobID);

    $job = $this->Common->one_cond_row_select('hris_jobvacancy','jobID,sy,sign,ttype', 'jobID', $jobID);
    $data['sign'] = $this->get_rqa_sign($job);

    // Preload applicants and staff
    $data['applicants'] = $this->Page_model->get_all_applicants_indexed();
    $data['staff'] = $this->Page_model->get_all_staff_indexed();

    if($job->ttype == 1){
        $data['title'] = "COMPARATIVE ASSESSMENT RESULT FOR EXPANDED RECLASSIFICATION (CAReER)";
    }else{
       $data['title'] = "COMPARATIVE ASSESSMENT RESULT (CAR)";
    }

    $this->load->view('pages/' . $page, $data);

    ob_end_flush();
}

public function rqa_municipality_print_shs()
{
    ob_start(); // Stream output to avoid timeout

    $page = "car_rqa_form_special_shs";
    if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
        show_404();
    }

    $jobID = $this->input->get('id');
    $mun = $this->input->get('mun');

    $data['job'] = $this->Page_model->get_single_row_by_id('hris_jobvacancy', 'jobID', $jobID);
    $data['settings'] = $this->SettingsModel->get_mis_settings();

    $data['grouped_applicants'] = $this->Reg->get_grouped_applicants_by_shss($jobID,$mun);

    $job = $this->Common->one_cond_row_select('hris_jobvacancy','jobID,sy,sign', 'jobID', $jobID);
    $data['sign'] = $this->get_rqa_sign($job);

    // Preload applicants and staff
    $data['applicants'] = $this->Page_model->get_all_applicants_indexed();
    $data['staff'] = $this->Page_model->get_all_staff_indexed();

    $data['title'] = "COMPARATIVE ASSESSMENT RESULT - REGISTRY OF QUALIFIED APPLICANTS (CAR - RQA)";

    $this->load->view('pages/' . $page, $data);

    ob_end_flush();
}

public function rqa_municipality_print_shsv2()
{
    ob_start(); // Stream output to avoid timeout

    $page = "car_rqa_form_special_shsv2";
    if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
        show_404();
    }

    $jobID = $this->input->get('id');
    $mun = $this->input->get('mun');

    $data['job'] = $this->Page_model->get_single_row_by_id('hris_jobvacancy', 'jobID', $jobID);
    $data['settings'] = $this->SettingsModel->get_mis_settings();

    $data['grouped_applicants'] = $this->Reg->get_grouped_applicants_by_shss($jobID,$mun);

    $job = $this->Common->one_cond_row_select('hris_jobvacancy','jobID,sy,sign', 'jobID', $jobID);
    $data['sign'] = $this->get_rqa_sign($job);

    // Preload applicants and staff
    $data['applicants'] = $this->Page_model->get_all_applicants_indexed();
    $data['staff'] = $this->Page_model->get_all_staff_indexed();

    $data['title'] = "COMPARATIVE ASSESSMENT RESULT - REGISTRY OF QUALIFIED APPLICANTS (CAR - RQA)";

    $this->load->view('pages/' . $page, $data);

    ob_end_flush();
}


   public function rqa_municipality_post()
    {
        $page = "car_rqa_form_special2_posting";
        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $jobID = $this->input->get('id');
        $mun = $this->input->get('mun');

        $data['job'] = $this->Page_model->get_single_row_by_id('hris_jobvacancy', 'jobID', $jobID);
        $data['car'] = $this->Page_model->rqa_mun($jobID, $mun);

        $job = $this->Common->one_cond_row_select('hris_jobvacancy','jobID,sy,sign', 'jobID', $jobID);
        $data['sign'] = $this->get_rqa_sign($job);
        $data['settings'] = $this->SettingsModel->get_mis_settings();


        $data['applicants'] = $this->Page_model->get_all_applicants_indexed();
        $data['staff'] = $this->Page_model->get_all_staff_indexed();

        $data['title'] = "COMPARATIVE ASSESSMENT RESULT - REGISTRY OF QUALIFIED APPLICANTS (CAR - RQA)";

        $this->render_rqa_report($page, $data, $this->rqa_excel_filename('rqa-municipality-posting', $jobID, $mun));
    }

    public function rqa_municipality_postv2()
    {
        $page = "car_rqa_form_special2_postingv2";
        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $jobID = $this->input->get('id');
        $mun = $this->input->get('mun');

        $data['job'] = $this->Page_model->get_single_row_by_id('hris_jobvacancy', 'jobID', $jobID);
        $data['car'] = $this->Page_model->rqa_mun($jobID, $mun);

        $job = $this->Common->one_cond_row_select('hris_jobvacancy','jobID,sy,sign', 'jobID', $jobID);
        $data['sign'] = $this->get_rqa_sign($job);
        $data['settings'] = $this->SettingsModel->get_mis_settings();


        $data['applicants'] = $this->Page_model->get_all_applicants_indexed();
        $data['staff'] = $this->Page_model->get_all_staff_indexed();

        $data['title'] = "COMPARATIVE ASSESSMENT RESULT - REGISTRY OF QUALIFIED APPLICANTS (CAR - RQA)";

        $this->render_rqa_report($page, $data, $this->rqa_excel_filename('rqa-municipality-posting-v2', $jobID, $mun));
    }

    public function rqa_municipality_track()
    {
        $page = "car_rqa_form_special3";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $jobID = $this->input->get('id');
        $mun = $this->input->get('mun');
        $spec = $this->input->get('spec');
        $data['job'] = $this->Page_model->get_single_row_by_id('hris_jobvacancy', 'jobID', $jobID);
        $data['car'] = $this->Page_model->rqa_mun_track($jobID, $mun, $spec);

        $job = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $jobID);
        $data['sign'] = $this->get_rqa_sign($job, 0, false);

        $data['title'] = "COMPARATIVE ASSESSMENT RESULT - REGISTRY OF QUALIFIED APPLICANTS (CAR - RQA)";

        $this->load->view('pages/' . $page, $data);
    }

    public function rqa_municipality_spec()
    {
        $page = "car_rqa_form_special3";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $jobID = $this->input->get('id');
        $mun = $this->input->get('mun');
        $spec = $this->input->get('spec');
        $data['job'] = $this->Page_model->get_single_row_by_id('hris_jobvacancy', 'jobID', $jobID);
        $data['car'] = $this->Page_model->rqa_mun_spec($jobID, $mun, $spec);
        $job = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $jobID);
        $data['sign'] = $this->get_rqa_sign($job, 0, false);

        $data['title'] = "COMPARATIVE ASSESSMENT RESULT - REGISTRY OF QUALIFIED APPLICANTS (CAR - RQA)";

        $this->load->view('pages/' . $page, $data);
    }


    public function rqa_track_print()
    {
        $page = "car_rqa_form_special";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $jobID = $this->input->get('id');
        $spe = $this->input->get('spec');
        $data['job'] = $this->Page_model->get_single_row_by_id('hris_jobvacancy', 'jobID', $jobID);
        $data['car'] = $this->Page_model->rqa_track($jobID, $spe);

        $job = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $jobID);
        $data['sign'] = $this->get_rqa_sign($job, 0, false);

        $data['title'] = "COMPARATIVE ASSESSMENT RESULT - REGISTRY OF QUALIFIED APPLICANTS (CAR - RQA)";

        $this->load->view('pages/' . $page, $data);
    }

    public function rqa_track_print1()
    {
        $page = "car_rqa_form_special_posting";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $jobID = $this->input->get('id');
        $spe = $this->input->get('spec');
        $data['job'] = $this->Page_model->get_single_row_by_id('hris_jobvacancy', 'jobID', $jobID);
        $data['car'] = $this->Page_model->rqa_track($jobID, $spe);

        $job = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $jobID);
        $data['sign'] = $this->get_rqa_sign($job, 0, false);

        $data['title'] = "COMPARATIVE ASSESSMENT RESULT - REGISTRY OF QUALIFIED APPLICANTS (CAR - RQA)";

        $this->load->view('pages/' . $page, $data);
    }

    function schools()
    {
        $type = $this->input->get('type');
        $result['data'] = $this->Page_model->schools($type);
        $this->load->view('schools', $result);
    }

    private function online_demo_enabled()
    {
        return !empty($this->db
            ->where('settings_name', 'Online Demo')
            ->where('status', 1)
            ->limit(1)
            ->get('settings')
            ->row());
    }

    private function online_demo_allowed_for_job($job)
    {
        $allowedJobTypes = [15, 16, 17, 19];

        return !empty($job) && in_array((int)($job->job_type ?? 0), $allowedJobTypes, true);
    }

    // renren new code please don't touch

    public function ma($param)
    {
        $jobvacancy = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $this->uri->segment(4));

        if ($jobvacancy->position == 1) {
            if($jobvacancy->promotion == 0){
                $page = "rp";
            }else{
                $page = "rp_reg_promotion";
            }
        }elseif($jobvacancy->position == 5){
            $page = "rp_reg_promotion";
        } else {
            $page = "rp_reg_none";
        }


        if ($this->session->position == 'reg' || $this->session->position == 'user') {
            if ($this->session->c_id != $this->uri->segment(3)) {
                redirect(base_url());
            }
        }



        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        // if($this->session->position == 'reg'){
        //     $data['data'] = $this->Common->one_cond_row('hris_applicant', 'id', $param);
        // }else{

        //     $data['data'] = $this->Common->one_cond_row('hris_staff', 'IDNumber', $param);
        // }  


        $data['staff'] = $this->Common->one_cond_row('hris_applicant', 'id', $this->uri->segment(3));
        $data['user'] = $this->Common->one_cond_row('users', 'user_id', $param);
        $data['online_demo_enabled'] = $this->online_demo_enabled() && $this->online_demo_allowed_for_job($jobvacancy);

        // Consolidate duplicate ratings and auto-mark as Rated when complete
        $appIdForRating = $this->uri->segment(6);
        if (!empty($appIdForRating)) {
            $this->Reg->consolidate_rating_single($appIdForRating);
            $this->Reg->auto_mark_rated($appIdForRating);
        }


        // A Closed vacancy can no longer be scored from the rating page (any
        // role). The lock partial disables the form controls and points the
        // user to the Corrigendum / Addendum page for post-close corrections.
        $ratingLocked = isset($jobvacancy->jvStatus) && strcasecmp(trim((string) $jobvacancy->jvStatus), 'Closed') === 0;

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        if ($ratingLocked) {
            $this->load->view('pages/_rating_locked');
        }
        $this->load->view('templates/footer');
    }

    public function ma_staff($param)
    {
        $jobvacancy = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $this->uri->segment(4));

        if ($jobvacancy->position == 1) {
            if($jobvacancy->promotion == 0){
                $page = "rp_staff";
            }else{
                $page = "rp_staff_promotion";
            }
        }elseif($jobvacancy->position == 5){
            $page = "rp_staff_promotion";
        } else {
            $page = "rp_none";
        }

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }


        $data['staff'] = $this->Common->one_cond_row('hris_staff', 'IDNumber', $this->uri->segment(3));

        $data['data'] = $this->Common->one_cond_row('hris_applications', 'empEmail', $param);




        $data['user'] = $this->Common->one_cond_row('users', 'user_id', $param);

        $ratingLocked = isset($jobvacancy->jvStatus) && strcasecmp(trim((string) $jobvacancy->jvStatus), 'Closed') === 0;

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        if ($ratingLocked) {
            $this->load->view('pages/_rating_locked');
        }
        $this->load->view('templates/footer');
    }

    public function rate_applicant($param)
    {

        $page = "rp";


        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }


        $data['data'] = $this->Common->one_cond_row('hris_applicant', 'id', $param);
        $data['user'] = $this->Common->one_cond_row('users', 'user_id', $param);


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function update_educ()
    {
        $this->Reg->educ_update();
        $this->session->set_flashdata('success', 'Successfuly Saved');
        if ($this->session->position == 'asds') {
            redirect(base_url() . 'pages/ma/' . $this->input->post('id') . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#efile');
        } else {
            redirect(base_url() . 'pages/ma/' . $this->session->c_id . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#efile');
        }
    }

    public function update_educ_staff()
    {
        $this->Reg->educ_update_staff();
        $this->session->set_flashdata('success', 'Successfuly Saved');
        if ($this->session->position == 'asds') {
            redirect(base_url() . 'pages/ma_staff/' . $this->input->post('id') . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#efile');
        } else {
            redirect(base_url() . 'pages/ma_staff/' . $this->session->c_id . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#efile');
        }
    }

    public function update_ai()
    {
        $this->Reg->ai_update();
        $this->Reg->ai_sex_update();
        $this->session->set_flashdata('success', 'Successfuly Saved');
        if ($this->session->position == 'asds') {
            redirect(base_url() . 'pages/ma/' . $this->input->post('id') . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id'));
        } else {
            redirect(base_url() . 'pages/ma/' . $this->session->c_id . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id'));
        }
    }

    public function update_ai_staff()
    {
        $this->Reg->ai_update_staff();
        $this->Reg->ai_sex_update();
        $this->session->set_flashdata('success', 'Successfuly Saved');
        if ($this->session->position == 'asds') {
            redirect(base_url() . 'pages/ma_staff/' . $this->input->post('id') . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id'));
        } else {
            redirect(base_url() . 'pages/ma_staff/' . $this->session->c_id . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id'));
        }
    }

    public function update_lr()
    {
        $this->Reg->lr_update();
        $this->session->set_flashdata('success', 'Successfuly Saved');
        redirect(base_url() . 'pages/ma/' . $this->session->c_id . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#lr');
    }

    public function update_lr_staff()
    {
        $this->Reg->lr_update_staff();
        $this->session->set_flashdata('success', 'Successfuly Saved');
        redirect(base_url() . 'pages/ma_staff/' . $this->session->c_id . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#lr');
    }

    public function update_ept()
    {
        $this->Reg->ept_update();
        $this->session->set_flashdata('success', 'Successfuly Saved');
        redirect(base_url() . 'pages/ma/' . $this->session->c_id . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#ept');
    }

    public function update_tc()
    {
        $this->Reg->tc_update();
        $this->session->set_flashdata('success', 'Successfuly Saved');
        redirect(base_url() . 'pages/ma/' . $this->session->c_id . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#tsc');
    }

    public function update_tc_staff()
    {
        $this->Reg->tc_update_staff();
        $this->session->set_flashdata('success', 'Successfuly Saved');
        redirect(base_url() . 'pages/ma_staff/' . $this->session->c_id . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#tsc');
    }

    public function update_online_demo()
    {
        $appID = (int) $this->input->post('appID');
        $applicantId = trim((string) $this->input->post('id'));
        $jobID = trim((string) $this->input->post('jobID'));
        $schoolId = trim((string) $this->input->post('school_id'));
        $redirectApplicant = $applicantId !== '' ? $applicantId : $this->session->c_id;
        $redirect = base_url() . 'pages/ma/' . rawurlencode($redirectApplicant) . '/' . rawurlencode($jobID) . '/' . rawurlencode($schoolId);

        if ($appID > 0) {
            $redirect .= '/' . $appID;
        }

        $redirect .= '#online_demo';

        if (!$this->online_demo_enabled()) {
            $this->session->set_flashdata('danger', 'Online demo submission is currently disabled.');
            redirect($redirect);
        }

        if ($appID <= 0 || $jobID === '' || $schoolId === '') {
            $this->session->set_flashdata('danger', 'Unable to save the demonstration link. Invalid application details.');
            redirect($redirect);
        }

        $job = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $jobID);

        if (!$this->online_demo_allowed_for_job($job)) {
            $this->session->set_flashdata('danger', 'Online demo submission is not applicable for this position.');
            redirect($redirect);
        }

        $application = $this->Common->one_cond_row('hris_applications', 'appID', $appID);

        if (empty($application) || (string) $application->jobID !== $jobID || (string) $application->pre_school !== $schoolId) {
            $this->session->set_flashdata('danger', 'Unable to save the demonstration link. Application not found.');
            redirect($redirect);
        }

        if (!in_array((string) $this->session->position, ['reg', 'user'], true)) {
            $this->session->set_flashdata('danger', 'Only applicants can save the demonstration link.');
            redirect($redirect);
        }

        if ((string) $application->applicant_id !== (string) $this->session->c_id) {
            redirect(base_url());
        }

        if (!$this->db->field_exists('online_demo', 'hris_applications')) {
            $this->session->set_flashdata('danger', 'The online_demo column is missing from hris_applications.');
            redirect($redirect);
        }

        $demoLink = trim((string) $this->input->post('online_demo', false));

        if ($demoLink === '') {
            $this->session->set_flashdata('danger', 'Please enter a demonstration link.');
            redirect($redirect);
        }

        if (!preg_match('#^https?://#i', $demoLink)) {
            $demoLink = 'https://' . ltrim($demoLink, '/');
        }

        $scheme = strtolower((string) parse_url($demoLink, PHP_URL_SCHEME));

        if (!in_array($scheme, ['http', 'https'], true) || filter_var($demoLink, FILTER_VALIDATE_URL) === false) {
            $this->session->set_flashdata('danger', 'Please enter a valid demonstration link.');
            redirect($redirect);
        }

        if (strlen($demoLink) > 500) {
            $this->session->set_flashdata('danger', 'The demonstration link must not exceed 500 characters.');
            redirect($redirect);
        }

        if ($this->Reg->online_demo_update($appID, $demoLink)) {
            $this->session->set_flashdata('success', 'Demonstration link saved successfully.');
        } else {
            $this->session->set_flashdata('danger', 'Unable to save the demonstration link.');
        }

        redirect($redirect);
    }

    public function update_master_file() {
        $config['allowed_types'] = 'pdf';
        $config['upload_path'] = './uploads/regfile';
        $new_name = time().'efile'.$this->input->post('id').$_FILES["file"]['name'];
        $config['file_name'] = $new_name;
        $this->load->library('upload', $config);
    
        if ($this->upload->do_upload('file')) {
            $id = $this->input->post('id');
            $empEmail = $this->input->post('empEmail');
            $jobID = $this->input->post('jobID');
            $reg = $this->Common->one_cond_row('hris_applicant', 'empEmail', $empEmail);
            
            if (!empty($reg->master_file)) {
                unlink("uploads/regfile/" . $reg->master_file);
            }
            
            $this->Reg->master_file_update();
            $this->session->set_flashdata('success', 'Successfully updated.');
        } else {
            $this->session->set_flashdata('danger', $this->upload->display_errors());
        }
        
        redirect(base_url() . 'pages/ma/' . $this->session->userdata('c_id') . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#lr');
    }

    public function update_doctor_file() {
        $config['allowed_types'] = 'pdf';
        $config['upload_path'] = './uploads/regfile';
        $new_name = $this->input->post('id').'efile'.time().$_FILES["file"]['name'];
        $config['file_name'] = $new_name;
        $this->load->library('upload', $config);
    
        if ($this->upload->do_upload('file')) {
            $id = $this->input->post('id');
            $empEmail = $this->input->post('empEmail');
            $jobID = $this->input->post('jobID');
            $reg = $this->Common->one_cond_row('hris_applicant', 'empEmail', $empEmail);
            
            if (!empty($reg->doctor_file)) {
                unlink("uploads/regfile/" . $reg->doctor_file);
            }
            
            $this->Reg->doctor_file_update();
            $this->session->set_flashdata('success', 'Successfully updated.');
        } else {
            $this->session->set_flashdata('danger', $this->upload->display_errors());
        }
        
        redirect(base_url() . 'pages/ma/' . $this->session->userdata('c_id') . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#lr');
    }

    public function update_efile() {
        $config['allowed_types'] = 'pdf';
        $config['upload_path'] = './uploads/regfile';
        $new_name = time().'efile'.$this->input->post('id').$_FILES["file"]['name'];
        $config['file_name'] = $new_name;
        $this->load->library('upload', $config);
    
        if ($this->upload->do_upload('file')) {
            $id = $this->input->post('id');
            $empEmail = $this->input->post('empEmail');
            $jobID = $this->input->post('jobID');
            $reg = $this->Common->one_cond_row('hris_applicant', 'empEmail', $empEmail);
            
            if (!empty($reg->efile)) {
                unlink("uploads/regfile/" . $reg->efile);
            }
            
            $this->Reg->educfile_update();
            $this->session->set_flashdata('success', 'Successfully updated.');
        } else {
            $this->session->set_flashdata('danger', $this->upload->display_errors());
        }
        
        redirect(base_url() . 'pages/ma/' . $this->session->userdata('c_id') . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#lr');
    }

    public function update_master_staff() {
        $config['allowed_types'] = 'pdf';
        $config['upload_path'] = './uploads/regfile';
        $new_name = time().'efile'.$this->input->post('id').$_FILES["file"]['name'];
        $config['file_name'] = $new_name;
        $this->load->library('upload', $config);
    
        if ($this->upload->do_upload('file')) {
            $id = $this->input->post('id');
            $empEmail = $this->input->post('empEmail');
            $jobID = $this->input->post('jobID');
            $reg = $this->Common->one_cond_row('hris_staff', 'IDNumber', $id);
            
            if (!empty($reg->master_file)) {
                unlink("uploads/regfile/" . $reg->master_file);
            }
            
            $this->Reg->master_update_staff();
            $this->session->set_flashdata('success', 'Successfully updated.');
        } else {
            $this->session->set_flashdata('danger', $this->upload->display_errors());
        }
        
        redirect(base_url() . 'pages/ma_staff/' . $this->session->userdata('c_id') . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#lr');
    }

    public function update_doctor_staff() {
        $config['allowed_types'] = 'pdf';
        $config['upload_path'] = './uploads/regfile';
        $new_name = time().'efile'.$this->input->post('id').$_FILES["file"]['name'];
        $config['file_name'] = $new_name;
        $this->load->library('upload', $config);
    
        if ($this->upload->do_upload('file')) {
            $id = $this->input->post('id');
            $empEmail = $this->input->post('empEmail');
            $jobID = $this->input->post('jobID');
            $reg = $this->Common->one_cond_row('hris_staff', 'IDNumber', $id);
            
            if (!empty($reg->doctor_file)) {
                unlink("uploads/regfile/" . $reg->doctor_file);
            }
            
            $this->Reg->doctor_update_staff();
            $this->session->set_flashdata('success', 'Successfully updated.');
        } else {
            $this->session->set_flashdata('danger', $this->upload->display_errors());
        }
        
        redirect(base_url() . 'pages/ma_staff/' . $this->session->userdata('c_id') . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#lr');
    }
    
    
      public function update_efile_staff() {
        $config['allowed_types'] = 'pdf';
        $config['upload_path'] = './uploads/regfile';
        $new_name = time().'efile'.$this->input->post('id').$_FILES["file"]['name'];
        $config['file_name'] = $new_name;
        $this->load->library('upload', $config);
    
        if ($this->upload->do_upload('file')) {
            $id = $this->input->post('id');
            $empEmail = $this->input->post('empEmail');
            $jobID = $this->input->post('jobID');
            $reg = $this->Common->one_cond_row('hris_staff', 'IDNumber', $id);
            
            if (!empty($reg->efile)) {
                unlink("uploads/regfile/" . $reg->efile);
            }
            
            $this->Reg->educfile_update_staff();
            $this->session->set_flashdata('success', 'Successfully updated.');
        } else {
            $this->session->set_flashdata('danger', $this->upload->display_errors());
        }
        
        redirect(base_url() . 'pages/ma_staff/' . $this->session->userdata('c_id') . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#lr');
    }
    
    public function update_outfile_staff()
    {
        $config['allowed_types'] = 'pdf';
        $config['upload_path'] = './uploads/regfile';
        $new_name = $this->input->post('id') . 'outfile' . time() .  $_FILES["file"]['name'];
        $config['file_name'] = $new_name;
        $this->load->library('upload', $config);

        if ($this->upload->do_upload('file')) {
            $id = $this->input->post('id');
            $empEmail = $this->input->post('empEmail');
            $jobID = $this->input->post('jobID');
            $reg = $this->Common->one_cond_row('hris_staff', 'IDNumber', $id);
            if (!empty($reg->efile)) {
            unlink("uploads/regfile/" . $reg->efile);
            }
            $this->Reg->outfile_update_staff();
            $this->session->set_flashdata('success', 'Successfully updated.');
            redirect(base_url() . 'pages/ma_staff/' . $this->session->c_id . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#lr');
        } else {
            $this->session->set_flashdata('danger', $this->upload->display_errors());
            redirect(base_url() . 'pages/ma_staff/' . $this->session->c_id);
        }
    }

    public function update_aefile_staff()
    {
        $config['allowed_types'] = 'pdf';
        $config['upload_path'] = './uploads/regfile';
        $new_name = $this->input->post('id'). 'aefile' . time()  .  $_FILES["file"]['name'];
        $config['file_name'] = $new_name;
        $this->load->library('upload', $config);

        if ($this->upload->do_upload('file')) {
            $id = $this->input->post('id');
            $empEmail = $this->input->post('empEmail');
            $jobID = $this->input->post('jobID');
            $reg = $this->Common->one_cond_row('hris_staff', 'IDNumber', $id);

            if (!empty($reg->eafile)) {
            unlink("uploads/regfile/" . $reg->eafile);
            }
            
            $this->Reg->aefile_update_staff();
            $this->session->set_flashdata('success', 'Successfully updated.');
            redirect(base_url() . 'pages/ma_staff/' . $this->session->c_id . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#lr');
        } else {
            $this->session->set_flashdata('danger', $this->upload->display_errors());
            redirect(base_url() . 'pages/ma_staff/' . $this->session->c_id);
        }
    }

    public function update_aefile()
    {
        $config['allowed_types'] = 'pdf';
        $config['upload_path'] = './uploads/regfile';
        $new_name = $this->input->post('id') . 'aefile' . time() .  $_FILES["file"]['name'];
        $config['file_name'] = $new_name;
        $this->load->library('upload', $config);

        if ($this->upload->do_upload('file')) {
            $id = $this->input->post('id');
            $empEmail = $this->input->post('empEmail');
            $jobID = $this->input->post('jobID');
            $reg = $this->Common->one_cond_row('hris_applicant', 'empEmail', $empEmail);
            if (!empty($reg->ae)) {
            unlink("uploads/regfile/" . $reg->ae);
            }
            $this->Reg->aefile_update();
            $this->session->set_flashdata('success', 'Successfully updated.');
            redirect(base_url() . 'pages/ma/' . $this->session->c_id . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#lr');
        } else {
            $this->session->set_flashdata('danger', $this->upload->display_errors());
            redirect(base_url() . 'pages/ma/' . $this->session->c_id);
        }
    }

    public function update_aldfile_staff()
    {
        $config['allowed_types'] = 'pdf';
        $config['upload_path'] = './uploads/regfile';
        $new_name = $this->input->post('id') . 'aldfile' . time()   . $_FILES["file"]['name'];
        $config['file_name'] = $new_name;
        $this->load->library('upload', $config);

        if ($this->upload->do_upload('file')) {
            $id = $this->input->post('id');
            $empEmail = $this->input->post('empEmail');
            $jobID = $this->input->post('jobID');
            $reg = $this->Common->one_cond_row('hris_staff', 'IDNumber', $id);
            if (!empty($reg->aldfile)) {
            unlink("uploads/regfile/" . $reg->aldfile);
            }
            $this->Reg->aldfile_update_staff();
            $this->session->set_flashdata('success', 'Successfully updated.');
            redirect(base_url() . 'pages/ma_staff/' . $this->session->c_id . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#lr');
        } else {
            $this->session->set_flashdata('danger', $this->upload->display_errors());
            redirect(base_url() . 'pages/ma_staff/' . $this->session->c_id);
        }
    }

    public function update_aldfile()
    {
        $config['allowed_types'] = 'pdf';
        $config['upload_path'] = './uploads/regfile';
        $new_name = $this->input->post('id') . 'aldfile'. time() . $_FILES["file"]['name'];
        $config['file_name'] = $new_name;
        $this->load->library('upload', $config);

        if ($this->upload->do_upload('file')) {
            $id = $this->input->post('id');
            $empEmail = $this->input->post('empEmail');
            $jobID = $this->input->post('jobID');
            $reg = $this->Common->one_cond_row('hris_staff', 'IDNumber', $id);
            if (!empty($reg->efile)) {
            unlink("uploads/regfile/" . $reg->efile);
            }
            $this->Reg->aldfile_update();
            $this->session->set_flashdata('success', 'Successfully updated.');
            redirect(base_url() . 'pages/ma/' . $this->session->c_id . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#er');
        } else {
            $this->session->set_flashdata('danger', $this->upload->display_errors());
            redirect(base_url() . 'pages/ma/' . $this->session->c_id);
        }
    }

    public function update_efile_staff_none()
    {
        $config['allowed_types'] = 'pdf';
        $config['upload_path'] = './uploads/regfile';
        $new_name = time() . 'torcav' . $this->input->post('id') . $_FILES["file"]['name'];
        $config['file_name'] = $new_name;
        $this->load->library('upload', $config);

        if ($this->upload->do_upload('file')) {
            $id = $this->input->post('id');
            $empEmail = $this->input->post('empEmail');
            $jobID = $this->input->post('jobID');
            $reg = $this->Common->one_cond_row('hris_staff', 'IDNumber', $id);
            unlink("uploads/regfile/" . $reg->tor_cav);
            $this->Reg->educfile_update_staff_none();
            $this->session->set_flashdata('success', 'Successfully updated.');
            redirect(base_url() . 'pages/ma_staff/' . $this->session->c_id . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#lr');
        } else {
            $this->session->set_flashdata('danger', $this->upload->display_errors());
            redirect(base_url() . 'pages/ma_staff/' . $this->session->c_id);
        }
    }

    public function update_efile_none()
    {
        $config['allowed_types'] = 'pdf';
        $config['upload_path'] = './uploads/regfile';
        $new_name = time() . 'torcav' . $this->input->post('id') . $_FILES["file"]['name'];
        $config['file_name'] = $new_name;
        $this->load->library('upload', $config);

        if ($this->upload->do_upload('file')) {
            $id = $this->input->post('id');
            $empEmail = $this->input->post('empEmail');
            $jobID = $this->input->post('jobID');
            $reg = $this->Common->one_cond_row('hris_applicant', 'empEmail', $empEmail);
            if (!empty($reg->tor_cav)) {
            unlink("uploads/regfile/" . $reg->tor_cav);
            }
            $this->Reg->educfile_update_none();
            $this->session->set_flashdata('success', 'Successfully updated.');
            redirect(base_url() . 'pages/ma/' . $this->session->c_id . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#lr');
        } else {
            $this->session->set_flashdata('danger', $this->upload->display_errors());
            redirect(base_url() . 'pages/ma/' . $this->session->c_id);
        }
    }

    public function update_wefile() {
        $config['allowed_types'] = 'pdf';
        $config['upload_path'] = './uploads/regfile';
        $new_name = time().'wefile'.$this->input->post('id').$_FILES["file"]['name'];
        $config['file_name'] = $new_name;
        $this->load->library('upload', $config);
    
        if ($this->upload->do_upload('file')) {
            $id = $this->input->post('id');
            $empEmail = $this->input->post('empEmail');
            $jobID = $this->input->post('jobID');
            $reg = $this->Common->one_cond_row('hris_applicant', 'empEmail', $empEmail);
            
            if (!empty($reg->wefile)) {
                unlink("uploads/regfile/" . $reg->wefile);
            }
            
            $this->Reg->wefile_update();
            $this->session->set_flashdata('success', 'Successfully updated.');
        } else {
            $this->session->set_flashdata('danger', $this->upload->display_errors());
        }
        
        redirect(base_url() . 'pages/ma/' . $this->session->userdata('c_id') . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#lr');
    }
    
     public function update_wefile_staff() {
        $config['allowed_types'] = 'pdf';
        $config['upload_path'] = './uploads/regfile';
        $new_name = time().'wefile'.$this->input->post('id').$_FILES["file"]['name'];
        $config['file_name'] = $new_name;
        $this->load->library('upload', $config);
    
        if ($this->upload->do_upload('file')) {
            $id = $this->input->post('id');
            $empEmail = $this->input->post('empEmail');
            $jobID = $this->input->post('jobID');
            $reg = $this->Common->one_cond_row('hris_staff', 'IDNumber', $id);
            
            if (!empty($reg->wefile)) {
                unlink("uploads/regfile/" . $reg->wefile);
            }
            
            $this->Reg->wefile_update_staff();
            $this->session->set_flashdata('success', 'Successfully updated.');
        } else {
            $this->session->set_flashdata('danger', $this->upload->display_errors());
        }
        
        redirect(base_url() . 'pages/ma_staff/' . $this->session->userdata('c_id') . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#lr');
    }

    
    public function update_eligibility_staff()
    {
        $config['allowed_types'] = 'pdf';
        $config['upload_path'] = './uploads/regfile';
        $new_name = time() . 'eligibility' . $this->input->post('id') . $_FILES["file"]['name'];
        $config['file_name'] = $new_name;
        $this->load->library('upload', $config);

        if ($this->upload->do_upload('file')) {
            $id = $this->input->post('id');
            $empEmail = $this->input->post('empEmail');
            $jobID = $this->input->post('jobID');
            $reg = $this->Common->one_cond_row('hris_staff', 'IDNumber', $id);
            if (!empty($reg->eligibility)) {
            unlink("uploads/regfile/" . $reg->eligibility);
            }
            $this->Reg->eligibility_update_staff();
            $this->session->set_flashdata('success', 'Successfully updated.');
            redirect(base_url() . 'pages/ma_staff/' . $this->session->c_id . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#lr');
        } else {
            $this->session->set_flashdata('danger', $this->upload->display_errors());
            redirect(base_url() . 'pages/ma_staff/' . $this->session->c_id);
        }
    }

    public function update_eligibility()
    {
        $config['allowed_types'] = 'pdf';
        $config['upload_path'] = './uploads/regfile';
        
        $new_name = time() . 'eligibility' . $this->input->post('id') . $_FILES["file"]['name'];
        $config['file_name'] = $new_name;
        $this->load->library('upload', $config);

        if ($this->upload->do_upload('file')) {
            $id = $this->input->post('id');
            $empEmail = $this->input->post('empEmail');
            $jobID = $this->input->post('jobID');
            $reg = $this->Common->one_cond_row('hris_applicant', 'empEmail', $empEmail);
            if (!empty($reg->wefile)) {
            unlink("uploads/regfile/" . $reg->wefile);
            }
            $this->Reg->eligibility_update();
            $this->session->set_flashdata('success', 'Successfully updated.');
            redirect(base_url() . 'pages/ma/' . $this->session->c_id . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#lr');
        } else {
            $this->session->set_flashdata('danger', $this->upload->display_errors());
            redirect(base_url() . 'pages/ma/' . $this->session->c_id);
        }
    }

    
  public function update_letfile() {
    $config['allowed_types'] = 'pdf';
    $config['upload_path'] = './uploads/regfile';
    $new_name = time().'letfile'.$this->input->post('id').$_FILES["file"]['name'];
    $config['file_name'] = $new_name;
    $this->load->library('upload', $config);

    if ($this->upload->do_upload('file')) {
        $id = $this->input->post('id');
        $empEmail = $this->input->post('empEmail');
        $jobID = $this->input->post('jobID');
        $reg = $this->Common->one_cond_row('hris_applicant', 'empEmail', $empEmail);
        
        if (!empty($reg->letfile)) {
            unlink("uploads/regfile/" . $reg->letfile);
        }
        
        $this->Reg->letfile_update();
        $this->session->set_flashdata('success', 'Successfully updated.');
    } else {
        $this->session->set_flashdata('danger', $this->upload->display_errors());
    }
    
    redirect(base_url() . 'pages/ma/' . $this->session->userdata('c_id') . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#lr');
}
   public function update_letfile_staff() {
    $config['allowed_types'] = 'pdf';
    $config['upload_path'] = './uploads/regfile';
    $new_name = time().'letfile'.$this->input->post('id').$_FILES["file"]['name'];
    $config['file_name'] = $new_name;
    $this->load->library('upload', $config);

    if ($this->upload->do_upload('file')) {
        $id = $this->input->post('id');
        $empEmail = $this->input->post('empEmail');
        $jobID = $this->input->post('jobID');
        $reg = $this->Common->one_cond_row('hris_staff', 'IDNumber', $id);
        
        if (!empty($reg->letfile)) {
            unlink("uploads/regfile/" . $reg->letfile);
        }
        
        $this->Reg->letfile_update_staff();
        $this->session->set_flashdata('success', 'Successfully updated.');
    } else {
        $this->session->set_flashdata('danger', $this->upload->display_errors());
    }
    
    redirect(base_url() . 'pages/ma_staff/' . $this->session->userdata('c_id') . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#lr');
}


    public function update_tscfile() {
        $config['allowed_types'] = 'pdf';
        $config['upload_path'] = './uploads/regfile';
        $new_name = time().'tscfile'.$this->input->post('id').$_FILES["file"]['name'];
        $config['file_name'] = $new_name;
        $this->load->library('upload', $config);
    
        if ($this->upload->do_upload('file')) {
            $id = $this->input->post('id');
            $empEmail = $this->input->post('empEmail');
            $jobID = $this->input->post('jobID');
            $reg = $this->Common->one_cond_row('hris_applicant', 'empEmail', $empEmail);
            
            if (!empty($reg->tscfile)) {
                unlink("uploads/regfile/" . $reg->tscfile);
            }
            
            $this->Reg->tscfile_update();
            $this->session->set_flashdata('success', 'Successfully updated.');
        } else {
            $this->session->set_flashdata('danger', $this->upload->display_errors());
        }
        
        redirect(base_url() . 'pages/ma/' . $this->session->userdata('c_id') . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#tsc');
    }
    
       public function update_tscfile_staff() {
        $config['allowed_types'] = 'pdf';
        $config['upload_path'] = './uploads/regfile';
        $new_name = time().'tscfile'.$this->input->post('id').$_FILES["file"]['name'];
        $config['file_name'] = $new_name;
        $this->load->library('upload', $config);
    
        if ($this->upload->do_upload('file')) {
            $id = $this->input->post('id');
            $empEmail = $this->input->post('empEmail');
            $jobID = $this->input->post('jobID');
            $reg = $this->Common->one_cond_row('hris_staff', 'IDNumber', $id);
            
            if (!empty($reg->tscfile)) {
                unlink("uploads/regfile/" . $reg->tscfile);
            }
            
            $this->Reg->tscfile_update_staff();
            $this->session->set_flashdata('success', 'Successfully updated.');
        } else {
            $this->session->set_flashdata('danger', $this->upload->display_errors());
        }
        
        redirect(base_url() . 'pages/ma_staff/' . $this->session->userdata('c_id') . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#tsc');
    }

    public function update_tcfile() {
        $config['allowed_types'] = 'pdf';
        $config['upload_path'] = './uploads/regfile';
        $new_name = time().'tcfile'.$this->input->post('id').$_FILES["file"]['name'];
        $config['file_name'] = $new_name;
        $this->load->library('upload', $config);
    
        if ($this->upload->do_upload('file')) {
            $id = $this->input->post('id');
            $empEmail = $this->input->post('empEmail');
            $jobID = $this->input->post('jobID');
            $reg = $this->Common->one_cond_row('hris_applicant', 'empEmail', $empEmail);
            
            if (!empty($reg->tcfile)) {
                unlink("uploads/regfile/" . $reg->tcfile);
            }
            
            $this->Reg->tcfile_update();
            $this->session->set_flashdata('success', 'Successfully updated.');
        } else {
            $this->session->set_flashdata('danger', $this->upload->display_errors());
        }
        
        redirect(base_url() . 'pages/ma/' . $this->session->userdata('c_id') . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#tsc');
    }
    
      public function update_tcfile_staff() {
        $config['allowed_types'] = 'pdf';
        $config['upload_path'] = './uploads/regfile';
        $new_name = time().'tcfile'.$this->input->post('id').$_FILES["file"]['name'];
        $config['file_name'] = $new_name;
        $this->load->library('upload', $config);
    
        if ($this->upload->do_upload('file')) {
            $id = $this->input->post('id');
            $empEmail = $this->input->post('empEmail');
            $jobID = $this->input->post('jobID');
            $reg = $this->Common->one_cond_row('hris_staff', 'IDNumber', $id);
            
            if (!empty($reg->tcfile)) {
                unlink("uploads/regfile/" . $reg->tcfile);
            }
            
            $this->Reg->tcfile_update_staff();
            $this->session->set_flashdata('success', 'Successfully updated.');
        } else {
            $this->session->set_flashdata('danger', $this->upload->display_errors());
        }
        
        redirect(base_url() . 'pages/ma_staff/' . $this->session->userdata('c_id') . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#tsc');
    }
    

    public function update_omni(){
        $config['allowed_types'] = 'pdf';
        $config['upload_path'] = './uploads/regfile';
        $new_name = time().'omni'.$this->input->post('id').$_FILES["file"]['name'];
        $config['file_name'] = $new_name;
        $this->load->library('upload', $config);
    
        if ($this->upload->do_upload('file')) {
            $id = $this->input->post('id');
            $empEmail = $this->input->post('empEmail');
            $jobID = $this->input->post('jobID');
            $reg = $this->Common->one_cond_row('hris_applicant', 'empEmail', $empEmail);
    
            // Check if omnibus exists and is a file before unlinking
            if (!empty($reg->omnibus)) {
                $file_path = "uploads/regfile/" . $reg->omnibus;
                if (file_exists($file_path) && is_file($file_path)) {
                    unlink($file_path);
                }
            }
    
            $this->Reg->omni_update();
            $this->session->set_flashdata('success', 'Successfully updated.');
        } else {
            $this->session->set_flashdata('danger', $this->upload->display_errors());
        }
    
        redirect(base_url() . 'pages/ma/' . $this->session->c_id . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#appen');
    }
    // public function update_omni_staff()
    // {
    //     $config['allowed_types'] = 'pdf';
    //     $config['upload_path'] = './uploads/regfile';
    //     $new_name = time() . 'omni' . $this->input->post('id') . $_FILES["file_name"]['name'];
    //     $config['file_name'] = $new_name;
    //     $this->load->library('upload', $config);

    //     if ($this->upload->do_upload('file')) {
    //         $id = $this->input->post('id');
    //         $empEmail = $this->input->post('empEmail');
    //         $jobID = $this->input->post('jobID');
    //         $reg = $this->Common->one_cond_row('hris_staff', 'IDNumber', $id);
    //         unlink("uploads/regfile/" . $reg->omnibus);
    //         $this->Reg->omni_update_staff();
    //         $this->session->set_flashdata('success', 'Successfully updated.');
    //         redirect(base_url() . 'pages/ma_staff/' . $this->session->c_id . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#appen');
    //     } else {
    //         $this->session->set_flashdata('danger', $this->upload->display_errors());
    //         redirect(base_url() . 'pages/ma_staff/' . $this->session->c_id . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#appen');
    //     }
    // }

    public function update_apfile() {
        $config['allowed_types'] = 'pdf';
        $config['upload_path'] = './uploads/regfile';
        $new_name = time().'application'.$this->input->post('id').$_FILES["file"]['name'];
        $config['file_name'] = $new_name;
        $this->load->library('upload', $config);
    
        if ($this->upload->do_upload('file')) {
            $id = $this->input->post('id');
            $empEmail = $this->input->post('empEmail');
            $jobID = $this->input->post('jobID');
            $school_id = $this->input->post('school_id');
            $reg = $this->Common->three_cond_row('hris_applications', 'empEmail', $empEmail, 'jobID', $jobID, 'pre_school', $school_id);
            
            // Unlink old file if exists
            if (!empty($reg->Application)) {
                $file_path = "uploads/regfile/" . $reg->Application;
                if (file_exists($file_path) && is_file($file_path)) {
                    unlink($file_path);
                }
            }
            
            $this->Reg->apfile_update();
            $this->session->set_flashdata('success', 'Successfully updated.');
        } else {
            $this->session->set_flashdata('danger', $this->upload->display_errors());
        }
        
        redirect(base_url() . 'pages/ma/' . $this->session->c_id . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#appen');
    }

    public function update_apfile_rploi() {
        $config['allowed_types'] = 'pdf';
        $config['upload_path'] = './uploads/regfile';
        $new_name = time().'application'.$this->input->post('id').$_FILES["file"]['name'];
        $config['file_name'] = $new_name;
        $this->load->library('upload', $config);
    
        if ($this->upload->do_upload('file')) {
            $id = $this->input->post('id');
            $empEmail = $this->input->post('empEmail');
            $jobID = $this->input->post('jobID');
            $school_id = $this->input->post('school_id');
            $reg = $this->Common->three_cond_row('hris_applications', 'empEmail', $empEmail, 'jobID', $jobID, 'pre_school', $school_id);
            
            // Unlink old file if exists
            if (!empty($reg->rploi)) {
                $file_path = "uploads/regfile/" . $reg->rploi;
                if (file_exists($file_path) && is_file($file_path)) {
                    unlink($file_path);
                }
            }
            
            $this->Reg->apfile_rploi_update();
            $this->session->set_flashdata('success', 'Successfully updated.');
        } else {
            $this->session->set_flashdata('danger', $this->upload->display_errors());
        }
        
        redirect($_SERVER['HTTP_REFERER']);
    }
    
    
        public function update_apfile_staff() {
        $config['allowed_types'] = 'pdf';
        $config['upload_path'] = './uploads/regfile';
        $new_name = $this->input->post('id').'-'.time().'application'.$_FILES["file"]['name'];
        $config['file_name'] = $new_name;
        $this->load->library('upload', $config);
    
        if ($this->upload->do_upload('file')) {
            //$id = $this->input->post('id');
            //$empEmail = $this->input->post('empEmail');
            //$jobID = $this->input->post('jobID');
            //$school_id = $this->input->post('school_id');
            $appid = $this->input->post('appid');
            $reg = $this->Common->one_cond_row('hris_applications', 'appID', $appid);
            
            // Unlink old file if exists
            if (!empty($reg->Application)) {
                $file_path = "uploads/regfile/" . $reg->Application;
                if (file_exists($file_path) && is_file($file_path)) {
                    unlink($file_path);
                }
            }
            
            $this->Reg->apfile_update_staff();
            $this->session->set_flashdata('success', 'Successfully updated.');
        } else {
            $this->session->set_flashdata('danger', $this->upload->display_errors());
        }
        
        redirect(base_url() . 'pages/ma_staff/' . $this->session->c_id . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#appen');
    }

       public function update_voters() {
        $config['allowed_types'] = 'pdf';
        $config['upload_path'] = './uploads/regfile';
        $new_name = time().'voters'.$this->input->post('id').$_FILES["file"]['name'];
        $config['file_name'] = $new_name;
        $this->load->library('upload', $config);
    
        if ($this->upload->do_upload('file')) {
            $id = $this->input->post('id');
            $empEmail = $this->input->post('empEmail');
            $jobID = $this->input->post('jobID');
            $reg = $this->Common->one_cond_row('hris_applicant', 'empEmail', $empEmail);
            
            // Unlink old file if exists
            if (!empty($reg->voters)) {
                $file_path = "uploads/regfile/" . $reg->voters;
                if (file_exists($file_path) && is_file($file_path)) {
                    unlink($file_path);
                }
            }
            
            $this->Reg->voters_update();
            $this->session->set_flashdata('success', 'Successfully updated.');
        } else {
            $this->session->set_flashdata('danger', $this->upload->display_errors());
        }
        
        redirect(base_url() . 'pages/ma/' . $this->session->c_id . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#appen');
    }
    
    
    public function update_omni_staff() {
        $config['allowed_types'] = 'pdf';
        $config['upload_path'] = './uploads/regfile';
        $new_name = time().'omni'.$this->input->post('id').$_FILES["file"]['name'];
        $config['file_name'] = $new_name;
        $this->load->library('upload', $config);
    
        if ($this->upload->do_upload('file')) {
            $id = $this->input->post('id');
            $empEmail = $this->input->post('empEmail');
            $jobID = $this->input->post('jobID');
            $reg = $this->Common->one_cond_row('hris_staff', 'IDNumber', $id);
            
            // Unlink old file if exists
            if (!empty($reg->omnibus)) {
                $file_path = "uploads/regfile/" . $reg->omnibus;
                if (file_exists($file_path) && is_file($file_path)) {
                    unlink($file_path);
                }
            }
            
            $this->Reg->omni_update_staff();
            $this->session->set_flashdata('success', 'Successfully updated.');
        } else {
            $this->session->set_flashdata('danger', $this->upload->display_errors());
        }
        
        redirect(base_url() . 'pages/ma_staff/' . $this->session->c_id . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#appen');
    }
    
    
    
    public function update_voters_staff() {
        $config['allowed_types'] = 'pdf';
        $config['upload_path'] = './uploads/regfile';
        $new_name = time().'voters'.$this->input->post('id').$_FILES["file"]['name'];
        $config['file_name'] = $new_name;
        $this->load->library('upload', $config);
    
        if ($this->upload->do_upload('file')) {
            $id = $this->input->post('id');
            $empEmail = $this->input->post('empEmail');
            $jobID = $this->input->post('jobID');
            $reg = $this->Common->one_cond_row('hris_staff', 'empEmail', $id);
            
            // Unlink old file if exists
            if (!empty($reg->voters)) {
                $file_path = "uploads/regfile/" . $reg->voters;
                if (file_exists($file_path) && is_file($file_path)) {
                    unlink($file_path);
                }
            }
            
            $this->Reg->voters_update_staff();
            $this->session->set_flashdata('success', 'Successfully updated.');
        } else {
            $this->session->set_flashdata('danger', $this->upload->display_errors());
        }
        
        redirect(base_url() . 'pages/ma_staff/' . $this->session->c_id . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#appen');
    }
    
      public function update_pdsfile() {
        $config['allowed_types'] = 'pdf';
        $config['upload_path'] = './uploads/regfile';
        $new_name = time().'pdsfile'.$this->input->post('id').$_FILES["file"]['name'];
        $config['file_name'] = $new_name;
        $this->load->library('upload', $config);
    
        if ($this->upload->do_upload('file')) {
            $id = $this->input->post('id');
            $empEmail = $this->input->post('empEmail');
            $jobID = $this->input->post('jobID');
            $reg = $this->Common->one_cond_row('hris_applicant', 'empEmail', $empEmail);
            
            // Unlink old file if exists
            if (!empty($reg->pdsfile)) {
                unlink("uploads/regfile/" . $reg->pdsfile);
            }
            
            $this->Reg->pdsfile_update();
            $this->session->set_flashdata('success', 'Successfully updated.');
        } else {
            $this->session->set_flashdata('danger', $this->upload->display_errors());
        }
        
        redirect(base_url() . 'pages/ma/' . $this->session->c_id . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#appen');
    }
    
       public function update_pdsfile_staff() {
        $config['allowed_types'] = 'pdf';
        $config['upload_path'] = './uploads/regfile';
        $new_name = time().'pdsfile'.$this->input->post('id').$_FILES["file"]['name'];
        $config['file_name'] = $new_name;
        $this->load->library('upload', $config);
    
        if ($this->upload->do_upload('file')) {
            $id = $this->input->post('id');
            $empEmail = $this->input->post('empEmail');
            $jobID = $this->input->post('jobID');
            $reg = $this->Common->one_cond_row('hris_staff', 'empEmail', $id);
            
            // Unlink old file if exists
            if (!empty($reg->pdsfile)) {
                unlink("uploads/regfile/" . $reg->pdsfile);
            }
            
            $this->Reg->pdsfile_update_staff();
            $this->session->set_flashdata('success', 'Successfully updated.');
        } else {
            $this->session->set_flashdata('danger', $this->upload->display_errors());
        }
        
        redirect(base_url() . 'pages/ma_staff/' . $this->session->c_id . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#appen');
    }
    
    
      public function update_oafile() {
        $config['allowed_types'] = 'pdf';
        $config['upload_path'] = './uploads/regfile';
        $new_name = time().'oafile'.$this->input->post('id').$_FILES["file"]['name'];
        $config['file_name'] = $new_name;
        $this->load->library('upload', $config);
    
        if ($this->upload->do_upload('file')) {
            $id = $this->input->post('id');
            $empEmail = $this->input->post('empEmail');
            $jobID = $this->input->post('jobID');
            $reg = $this->Common->one_cond_row('hris_applicant', 'empEmail', $empEmail);
            
            if (!empty($reg->oafile)) {
                unlink("uploads/regfile/" . $reg->oafile);
            }
            
            $this->Reg->oafile_update();
            $this->session->set_flashdata('success', 'Successfully updated.');
        } else {
            $this->session->set_flashdata('danger', $this->upload->display_errors());
        }
        
        redirect(base_url() . 'pages/ma/' . $this->session->userdata('c_id') . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#appen');
    }
    
       public function update_oafile_staff() {
        $config['allowed_types'] = 'pdf';
        $config['upload_path'] = './uploads/regfile';
        $new_name = time().'oafile'.$this->input->post('id').$_FILES["file"]['name'];
        $config['file_name'] = $new_name;
        $this->load->library('upload', $config);
    
        if ($this->upload->do_upload('file')) {
            $id = $this->input->post('id');
            $empEmail = $this->input->post('empEmail');
            $jobID = $this->input->post('jobID');
            $reg = $this->Common->one_cond_row('hris_staff', 'empEmail', $id);
            
            if (!empty($reg->oafile)) {
                unlink("uploads/regfile/" . $reg->oafile);
            }
            
            $this->Reg->oafile_update_staff();
            $this->session->set_flashdata('success', 'Successfully updated.');
        } else {
            $this->session->set_flashdata('danger', $this->upload->display_errors());
        }
        
        redirect(base_url() . 'pages/ma_staff/' . $this->session->c_id . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#appen');
    }
    
    
       public function update_outfile() {
        $config['allowed_types'] = 'pdf';
        $config['upload_path'] = './uploads/regfile';
        $new_name = time().'outfile'.$this->input->post('id').$_FILES["file"]['name'];
        $config['file_name'] = $new_name;
        $this->load->library('upload', $config);
    
        if ($this->upload->do_upload('file')) {
            $id = $this->input->post('id');
            $empEmail = $this->input->post('empEmail');
            $jobID = $this->input->post('jobID');
            $reg = $this->Common->one_cond_row('hris_applicant', 'empEmail', $empEmail);
            
            if (!empty($reg->oa)) {
                unlink("uploads/regfile/" . $reg->oa);
            }
            
            $this->Reg->outfile_update();
            $this->session->set_flashdata('success', 'Successfully updated.');
        } else {
            $this->session->set_flashdata('danger', $this->upload->display_errors());
        }
        
        redirect(base_url() . 'pages/ma/' . $this->session->c_id . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#appen');
    }
    
    public function update_app()
    {
        $this->Reg->update_application_district();
        $this->session->set_flashdata('success', 'Successfully updated.');
        redirect(base_url() . 'Pages/request_to_rr/' . $this->input->post('applicant_id') . '/' . $this->input->post('jobID') . '/' . $this->input->post('appID'));
    }

    public function update_app_dis_blank()
    {
        $this->Reg->update_application_district_blank();
        $this->session->set_flashdata('success', 'Successfully updated.');
        redirect(base_url() . 'Pages/request_to_rr/' . $this->uri->segment(3) . '/' . $this->uri->segment(4) . '/' . $this->uri->segment(5));
    }

    public function update_ipcrffile() {
        $config['allowed_types'] = 'pdf';
        $config['upload_path'] = './uploads/regfile';
        $new_name = time().'ipcrffile'.$this->input->post('id').$_FILES["file"]['name'];
        $config['file_name'] = $new_name;
        $this->load->library('upload', $config);
    
        if ($this->upload->do_upload('file')) {
            $id = $this->input->post('id');
            $empEmail = $this->input->post('empEmail');
            $jobID = $this->input->post('jobID');
            $reg = $this->Common->one_cond_row('hris_applicant', 'empEmail', $empEmail);
            
            if (!empty($reg->ipcrffile)) {
                unlink("uploads/regfile/" . $reg->ipcrffile);
            }
            
            $this->Reg->ipcrffile_update();
            $this->session->set_flashdata('success', 'Successfully updated.');
        } else {
            $this->session->set_flashdata('danger', $this->upload->display_errors());
        }
        
        redirect(base_url() . 'pages/ma/' . $this->session->userdata('c_id') . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#appen');
    }
    
    
    public function update_ipcrffile_staff() {
        $config['allowed_types'] = 'pdf';
        $config['upload_path'] = './uploads/regfile';
        $new_name = time().'ipcrffile'.$this->input->post('id').$_FILES["file"]['name'];
        $config['file_name'] = $new_name;
        $this->load->library('upload', $config);
    
        if ($this->upload->do_upload('file')) {
            $id = $this->input->post('id');
            $empEmail = $this->input->post('empEmail');
            $jobID = $this->input->post('jobID');
            $reg = $this->Common->one_cond_row('hris_staff', 'empEmail', $id);
            
            if (!empty($reg->ipcrffile)) {
                unlink("uploads/regfile/" . $reg->ipcrffile);
            }
            
            $this->Reg->ipcrffile_update_staff();
            $this->session->set_flashdata('success', 'Successfully updated.');
        } else {
            $this->session->set_flashdata('danger', $this->upload->display_errors());
        }
        
        redirect(base_url() . 'pages/ma_staff/' . $this->session->userdata('c_id') . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#appen');
    }

    public function update_ppstco() {
        $config['allowed_types'] = 'pdf';
        $config['upload_path'] = './uploads/regfile';
        $new_name = time().'ppstco'.$this->input->post('id').$_FILES["file"]['name'];
        $config['file_name'] = $new_name;
        $this->load->library('upload', $config);
    
        if ($this->upload->do_upload('file')) {
            $id = $this->input->post('id');
            $empEmail = $this->input->post('empEmail');
            $jobID = $this->input->post('jobID');
            $reg = $this->Common->one_cond_row('hris_staff', 'empEmail', $id);
            
            if (!empty($reg->ppstco)) {
                unlink("uploads/regfile/" . $reg->ppstco);
            }
            
            $this->Reg->ppstco_update_staff();
            $this->session->set_flashdata('success', 'Successfully updated.');
        } else {
            $this->session->set_flashdata('danger', $this->upload->display_errors());
        }
        
        redirect(base_url() . 'pages/ma_staff/' . $this->session->userdata('c_id') . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#appen');
    }

    public function update_ppstpa() {
        $config['allowed_types'] = 'pdf';
        $config['upload_path'] = './uploads/regfile';
        $new_name = time().'ppstpa'.$this->input->post('id').$_FILES["file"]['name'];
        $config['file_name'] = $new_name;
        $this->load->library('upload', $config);
    
        if ($this->upload->do_upload('file')) {
            $id = $this->input->post('id');
            $empEmail = $this->input->post('empEmail');
            $jobID = $this->input->post('jobID');
            $reg = $this->Common->one_cond_row('hris_staff', 'empEmail', $id);
            
            if (!empty($reg->ppstco)) {
                unlink("uploads/regfile/" . $reg->ppstpa);
            }
            
            $this->Reg->ppstpa_update_staff();
            $this->session->set_flashdata('success', 'Successfully updated.');
        } else {
            $this->session->set_flashdata('danger', $this->upload->display_errors());
        }
        
        redirect(base_url() . 'pages/ma_staff/' . $this->session->userdata('c_id') . '/' . $this->input->post('jobID') . '/' . $this->input->post('school_id') . '#appen');
    }

    public function submit_application()
    {

        $applications = $this->Common->two_cond_count_row('hris_applications', 'jobID', $this->input->post('id'), 'applicant_id', $this->session->c_id);

        if ($applications->num_rows() >= 1) {
            $this->session->set_flashdata('danger', 'Please double-check the same job position you applied.');
            redirect(base_url() . 'pages/ja/' . $this->session->c_id);
        } else {
            $this->Reg->ap_submit();
        }
        $app_id = $this->db->insert_id();

        $this->Page_model->insert_at('Submit Application', $app_id);
        if ($this->session->position == 'user') {
            $this->Reg->ap_track_apply_user('Application Submitted', $app_id);
        } else {
            $this->Reg->ap_track_apply('Application Submitted', $app_id);
        }

        $this->session->set_flashdata('success', 'Successfully Applied');
        redirect(base_url() . 'pages/ja/' . $this->session->c_id);
    }

    public function edit_application()
    {
        $this->Reg->edit_submit();
        $this->Page_model->insert_at('Edited  Application', $this->db->insert_id());
        //$this->Reg->ap_track_apply('Edited Application',);
        $this->session->set_flashdata('success', 'Successfully Updated.');
        redirect(base_url() . 'pages/ja/' . $this->session->c_id);
    }
    public function ir()
    {
        $this->Reg->insert_rate($this->uri->segment(3), $this->uri->segment(4));
        redirect(base_url() . 'pages/ma/' . $this->uri->segment(9) . '/' . $this->uri->segment(8) . '/' . $this->uri->segment(7) . '/' . $this->uri->segment(10));
    }

    public function validated()
    {
        $this->Reg->ap_track('Validated');
        $this->Page_model->insert_at('Submit Application', $this->db->insert_id());
        $this->Reg->ap_change_stat('Validated');

        $rate = $this->Common->two_cond_count_row('hris_applications_rating', 'appID', $this->uri->segment(4), 'record_no', $this->uri->segment(5));
        if ($rate->num_rows() <= 0) {

            $job = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $this->uri->segment(4));

            $this->Reg->insert_rate($job->position, $job->sy);
            //$this->Reg->ap_track('The submitted documents has been Validated.');
        }

        $this->session->set_flashdata('success', 'Successfully Validated');
        redirect(base_url() . 'pages/school_applicant/' . $this->uri->segment(4));
    }

    public function undo_validated()
    {
        $this->Reg->ap_track('Cancelled Validation');
        $this->Page_model->insert_at('Validation Canceled', $this->db->insert_id());
        $this->Reg->ap_change_stat('Application Submitted');

        $this->session->set_flashdata('success', 'Successfully Updated');
        redirect(base_url() . 'pages/school_applicant/' . $this->uri->segment(4));
    }

    public function efr()
    {
        $this->Reg->ap_track('Endorse for Rating');
        $this->Reg->ap_change_stat('Endorsed for Rating');
        $this->session->set_flashdata('success', 'Successfully endorsed for rating.');
        redirect(base_url() . 'pages/sa_view_applicant/' . $this->uri->segment(4));
    }

    public function endorse_all_app()
    {
        //$this->Reg->ap_track('Endorse for Rating');
        $this->Reg->ap_change_stat_all_application('Endorsed for Rating');
        $this->session->set_flashdata('success', 'Successfully endorsed for rating.');
        redirect(base_url() . 'pages/' . $this->uri->segment(6) .'/'.$this->uri->segment(3) .'/'.$this->uri->segment(4) .'/'.$this->uri->segment(5));
    }

    public function efrv2()
    {
        //$this->Reg->ap_track('Endorse for Rating');
        $this->Reg->ap_change_stat_all('Endorsed for Rating');
        $this->session->set_flashdata('success', 'Successfully endorsed for rating.');
        redirect(base_url() . 'pages/for_endorsement');
    }

    public function efr_district()
    {
        //$this->Reg->ap_track('Endorse for Rating');
        //$this->Reg->insert_va();
        $this->Reg->ap_change_stat_district('Endorsed for Rating');
        $this->session->set_flashdata('success', 'Successfully Endorsed for Rating');
        redirect(base_url() . 'pages/sa_view_applicant/' . $this->uri->segment(4));
    }

    public function Rated()
    {
        $this->Reg->ap_track('Rated');
        $this->Reg->ap_change_stat('Rated');
        $this->session->set_flashdata('success', 'Successfully Rated');
        redirect(base_url() . 'pages/' . $this->uri->segment(7) . '/' . $this->uri->segment(3) . '/' . $this->uri->segment(4) . '/' . $this->uri->segment(5) . '/' . $this->uri->segment(6));
    }
    public function Ratednirenren()
    {
        $this->Reg->ap_track('Rated');
        $this->Reg->ap_change_stat('Rated');
        $this->session->set_flashdata('success', 'Successfully Rated');
        redirect(base_url() . 'pages/evaluator_applicant/' . $this->uri->segment(4));
    }
    public function confirmation()
    {
        $this->Reg->ap_trackv4('Confirmed');
        $this->Reg->ap_change_stat('Confirmed');
        $this->session->set_flashdata('success', 'Successfully Confirmed');
        redirect(base_url() . 'pages/' . $this->uri->segment(7) . '/' . $this->uri->segment(3) . '/' . $this->uri->segment(4) . '/' . $this->uri->segment(5) . '/' . $this->uri->segment(6));
    }

    public function ap_cancel($param)
    {
        $this->Common->del('hris_applications', 'appID', $param);
        $this->Common->del('hris_applications_track', 'app_id', $param);
        $this->Page_model->insert_at('Cancel Application', $param);
        $this->session->set_flashdata('success', 'The Application has been Successfully Canceled.');
        redirect(base_url() . 'pages/ja/' . $this->session->c_id);
    }

    public function ap_cancel_hr($param)
    {
        $this->Common->del('hris_applications', 'appID', $param);
        $this->Common->del('hris_applications_track', 'app_id', $param);
        $this->Page_model->insert_at('Cancel Application', $param);
        $this->session->set_flashdata('success', 'The Application has been Successfully Canceled.');
        redirect(base_url() . 'Page/jobVacancy');
    }

    public function close_job()
    {
        $this->Reg->close_jv();
        $this->Page_model->insert_at('Cancel Application', $this->uri->segment(3));
        $this->session->set_flashdata('success', 'The Job Item has been Successfully Closed.');
        redirect(base_url() . 'page/jobVacancy');
    }

    public function open_job()
    {
        $this->Reg->open_jv();
        $this->Page_model->insert_at('Cancel Application', $this->uri->segment(3));
        $this->session->set_flashdata('success', 'The Job Item has been Successfully opened.');
        redirect(base_url() . 'page/jobArchieved');
    }

    public function ja()
    {

        $page = "job_application";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $ee = $this->input->get('ee');

        if ($this->session->position == 'reg') {
            //$data['data'] = $this->Common->one_cond_loop_order_by('hris_applications', 'empEmail', $this->session->username,'appID','Desc');
            //$data['data'] = $this->Common->two_cond('hris_applications', 'empEmail', $this->session->username, 'app_year', date('Y'));
            $data['data'] = $this->Common->two_join_two_cond('hris_applications', 'hris_jobvacancy', 'a.jobID,a.empEmail,a.app_year,a.district,a.district,
            a.pre_school,a.applicant_id,a.appID,b.jobID,b.jobTitle,b.jvStatus', 'a.jobID = b.jobID', 'empEmail', $this->session->username,'jvStatus','Open','jobTitle', 'ASC');
        } elseif ($this->session->position == 'user') {
            $data['data'] = $this->Common->two_join_two_cond('hris_applications', 'hris_jobvacancy', 'a.jobID,a.empEmail,a.app_year,a.district,a.district,
            a.pre_school,a.applicant_id,a.appID,b.jobID,b.jobTitle,b.jvStatus', 'a.jobID = b.jobID', 'empEmail', $this->session->username,'jvStatus','Open','jobTitle', 'ASC');
        } else {
            $data['data'] = $this->Common->one_cond('hris_applications', 'empEmail', $ee);
        }


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
        $this->load->view('templates/ja_edit');
    }

    public function district_applicant()
    {

        $page = "list_applicant_district";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $data['d'] = $this->Common->one_cond_row('district', 'id', $this->session->c_id);

        if ($this->uri->segment(4) == 0) {
            $data['data'] = $this->Common->three_cond('hris_applications', 'jobID', $this->uri->segment(3), 'pre_school', $this->uri->segment(5), 'appStatus', 'Application Submitted');
        } else {
            $data['data'] = $this->Common->two_cond('hris_applications', 'jobID', $this->uri->segment(3), 'pre_school', $this->uri->segment(5));
        }



        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function request_to_rr()
    {

        $page = "rtrr";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $data['title'] = 'REQUEST TO RETAIN RATING';
        $data['applicant'] = $this->Common->one_cond_row('hris_applicant', 'id', $this->uri->segment(3));
        $data['job'] = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $this->uri->segment(4));
        $data['application'] = $this->Common->one_cond_row('hris_applications', 'appID', $this->uri->segment(5));
        $data['rating'] = $this->Common->one_cond_row('hris_applications_rating', 'appID', $this->uri->segment(5));

        $this->load->view('pages/' . $page, $data);
    }

    public function ssc_report()
    {
        $page = "ssc_vsr";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $data['settings'] = $this->SettingsModel->get_mis_settings();
        $data['title'] = 'REQUEST TO RETAIN RATING';

        $data['data'] = $this->Hiring_model->getApplications();
        
        $this->load->view('pages/' . $page, $data);
    }

    public function lva()
    {

        $page = "lva";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $data['settings'] = $this->SettingsModel->get_mis_settings();
        $data['title'] = 'REQUEST TO RETAIN RATING';
        $data['applicant'] = $this->Common->one_cond_row('hris_applicant', 'id', $this->uri->segment(3));
        $data['job'] = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $this->uri->segment(4));

        $data['data'] = $this->Common->two_join_one_cond('hris_applications', 'schools', 'a.pre_school,a.empEmail,b.schoolName,b.schoolID', 'b.schoolID=a.pre_school', 'appStatus', 'Endorsed for Rating', 'a.pre_school', 'b.schoolName', 'ASC');

        $this->load->view('pages/' . $page, $data);
    }

    public function abd_report()
    {

        $page = "abd";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $data['settings'] = $this->SettingsModel->get_mis_settings();
        $data['title'] = 'REQUEST TO RETAIN RATING';
        $data['applicant'] = $this->Common->one_cond_row('hris_applicant', 'id', $this->uri->segment(3));
        $data['job'] = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $this->uri->segment(4));

        $data['data'] = $this->Common->two_join_one_cond('hris_applications', 'schools', 'a.pre_school,a.district,a.empEmail,a.appStatus,b.schoolName,b.schoolID', 'b.schoolID=a.pre_school', 'a.appStatus', 'Endorsed for Rating', 'a.district', 'b.schoolName', 'ASC');

        $this->load->view('pages/' . $page, $data);
    }

    public function abd_report_multiple()
    {

        $page = "abd_multi";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $data['settings'] = $this->SettingsModel->get_mis_settings();
        //$data['applicant'] = $this->Common->one_cond_row('hris_applicant', 'id', $this->uri->segment(3));
        //$data['job'] = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $this->uri->segment(4));

        //$data['data'] = $this->Common->two_join_one_cond('hris_applications', 'schools', 'a.pre_school,a.district,a.empEmail,a.appStatus,b.schoolName,b.schoolID', 'b.schoolID=a.pre_school', 'a.appStatus', 'Endorsed for Rating', 'a.district', 'b.schoolName', 'ASC');

        $data['data'] = $this->Common->one_cond_and_two_cond_ne_ob_select('hris_applications','app_year,appStatus,empEmail,jobID,appID,pre_school','app_year', date('Y'),'appStatus','Application Submitted','appStatus','Validated','empEmail','ASC');

        $this->load->view('pages/' . $page, $data);
    }

    public function abd_report_multiple_district()
    {

        $page = "abd_multi";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $data['settings'] = $this->SettingsModel->get_mis_settings();
        $district = $this->input->get('d');
        //$data['applicant'] = $this->Common->one_cond_row('hris_applicant', 'id', $this->uri->segment(3));
        //$data['job'] = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $this->uri->segment(4));

        //$data['data'] = $this->Common->two_join_one_cond('hris_applications', 'schools', 'a.pre_school,a.district,a.empEmail,a.appStatus,b.schoolName,b.schoolID', 'b.schoolID=a.pre_school', 'a.appStatus', 'Endorsed for Rating', 'a.district', 'b.schoolName', 'ASC');

        $data['data'] = $this->Common->two_cond_and_two_cond_ne_ob_select('hris_applications','app_year,appStatus,empEmail,jobID,appID,pre_school,district','district',$district,'app_year', date('Y'),'appStatus','Application Submitted','appStatus','Validated','empEmail','ASC');

        $this->load->view('pages/' . $page, $data);
    }

    public function abd_report_multiple_for_endorsement()
    {

        $page = "abd_multiv2";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $data['settings'] = $this->SettingsModel->get_mis_settings();
        //$data['applicant'] = $this->Common->one_cond_row('hris_applicant', 'id', $this->uri->segment(3));
        //$data['job'] = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $this->uri->segment(4));

        //$data['data'] = $this->Common->two_join_one_cond('hris_applications', 'schools', 'a.pre_school,a.district,a.empEmail,a.appStatus,b.schoolName,b.schoolID', 'b.schoolID=a.pre_school', 'a.appStatus', 'Endorsed for Rating', 'a.district', 'b.schoolName', 'ASC');

        $data['data'] = $this->Common->one_cond_and_one_cond_ne_ob_select_gb('hris_applications','app_year,appStatus,empEmail,jobID,appID,pre_school','app_year', date('Y'),'appStatus','Application Submitted','empEmail','ASC','empEmail');

        $this->load->view('pages/' . $page, $data);
    }

    // public function abd_report_shs()
    // {

    //     $page = "abd_shs";

    //     if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
    //         show_404();
    //     }
    //     $data['settings'] = $this->SettingsModel->get_mis_settings();
    //     //$data['applicant'] = $this->Common->one_cond_row('hris_applicant', 'id', $this->uri->segment(3));
    //     //$data['job'] = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $this->uri->segment(4));

    //     //$data['data'] = $this->Common->two_join_one_cond('hris_applications', 'schools', 'a.pre_school,a.district,a.empEmail,a.appStatus,b.schoolName,b.schoolID', 'b.schoolID=a.pre_school', 'a.appStatus', 'Endorsed for Rating', 'a.district', 'b.schoolName', 'ASC');

    //     $data['data'] = $this->Common->one_cond_and_two_cond_ne_ob_select('hris_applications','app_year,appStatus,empEmail,jobID,appID,pre_school','app_year', date('Y'),'appStatus','Application Submitted','appStatus','Validated','empEmail','ASC');

    //     $this->load->view('pages/' . $page, $data);
    // }

    public function abd_report_shs()
    {

        $page = "abd_shsv2";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $data['settings'] = $this->SettingsModel->get_mis_settings();
       
        $data['data'] = $this->Common->one_cond_and_two_cond_ne_ob_select('hris_applications','app_year,appStatus,empEmail,jobID,appID,pre_school','app_year', date('Y'),'appStatus','Application Submitted','appStatus','Validated','empEmail','ASC');

        $this->load->view('pages/' . $page, $data);
    }

    public function abd_report_jhs()
    {

        $page = "abd_jhs";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $data['settings'] = $this->SettingsModel->get_mis_settings();
        //$data['applicant'] = $this->Common->one_cond_row('hris_applicant', 'id', $this->uri->segment(3));
        //$data['job'] = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $this->uri->segment(4));

        //$data['data'] = $this->Common->two_join_one_cond('hris_applications', 'schools', 'a.pre_school,a.district,a.empEmail,a.appStatus,b.schoolName,b.schoolID', 'b.schoolID=a.pre_school', 'a.appStatus', 'Endorsed for Rating', 'a.district', 'b.schoolName', 'ASC');

        $data['data'] = $this->Common->one_cond_and_two_cond_ne_ob_select('hris_applications','app_year,appStatus,empEmail,jobID,appID,pre_school','app_year', date('Y'),'appStatus','Application Submitted','appStatus','Validated','empEmail','ASC');

        $this->load->view('pages/' . $page, $data);
    }

    public function abd_report_rr()
    {

        $page = "abd_rr";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $data['settings'] = $this->SettingsModel->get_mis_settings();
        $data['title'] = 'APPLICANT WHO REQUEST TO RETAIN RATING';
        
        $data['applicant'] = $this->Common->one_cond_row('hris_applicant', 'id', $this->uri->segment(3));
        $data['job'] = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $this->uri->segment(4));

        $data['data'] = $this->Common->two_join_one_cond('hris_applications', 'schools', 'a.pre_school,a.district,a.empEmail,a.appStatus,b.schoolName,b.schoolID', 'b.schoolID=a.pre_school', 'a.appStatus', 'Endorsed for Rating', 'a.district', 'b.schoolName', 'ASC');
        

        $this->load->view('pages/' . $page, $data);
    }

    public function abd_report_validated()
    {

        $page = "abd_validated";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $data['settings'] = $this->SettingsModel->get_mis_settings();
        $data['title'] = 'APPLICANT WHO REQUEST TO RETAIN RATING';
        $data['applicant'] = $this->Common->one_cond_row('hris_applicant', 'id', $this->uri->segment(3));
        $data['job'] = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $this->uri->segment(4));

        $data['data'] = $this->Common->two_join_one_cond('hris_applications', 'schools', 'a.pre_school,a.district,a.empEmail,a.appStatus,b.schoolName,b.schoolID', 'b.schoolID=a.pre_school', 'a.appStatus', 'Validated', 'a.district', 'b.schoolName', 'ASC');

        $this->load->view('pages/' . $page, $data);
    }

    public function abd_report_district()
    {

        $page = "abd_applied";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $data['settings'] = $this->SettingsModel->get_mis_settings();
        $data['title'] = 'REQUEST TO RETAIN RATING';
        //$data['applicant'] = $this->Common->one_cond_row('hris_applicant', 'id', $this->uri->segment(3));
        $data['job'] = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $this->uri->segment(4));

        //$data['data'] = $this->Common->two_join_one_cond('hris_applications', 'schools', 'a.pre_school,a.district,a.empEmail,a.appStatus,b.schoolName,b.schoolID', 'b.schoolID=a.pre_school', 'a.appStatus', 'Endorsed for Rating', 'a.district', 'b.schoolName', 'ASC');
        $data['district'] = $this->Common->no_cond('district');

        $this->load->view('pages/' . $page, $data);
    }

    public function abd_reportv2()
    {

        $page = "abdv2";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $data['settings'] = $this->SettingsModel->get_mis_settings();
        $data['title'] = 'REQUEST TO RETAIN RATING';
        $data['applicant'] = $this->Common->one_cond_row('hris_applicant', 'id', $this->uri->segment(3));
        $data['job'] = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $this->uri->segment(4));

        $data['data'] = $this->Common->two_join_one_cond('hris_applications', 'schools', 'a.pre_school,a.district,a.empEmail,a.appStatus,b.schoolName,b.schoolID', 'b.schoolID=a.pre_school', 'a.appStatus', 'Endorsed for Rating', 'a.district', 'b.schoolName', 'ASC');

        $this->load->view('pages/' . $page, $data);
    }

    public function abd_reportv3()
    {

        $page = "abdv3";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $data['settings'] = $this->SettingsModel->get_mis_settings();

        $data['title'] = 'REQUEST TO RETAIN RATING';
        $data['applicant'] = $this->Common->one_cond_row('hris_applicant', 'id', $this->uri->segment(3));
        $data['job'] = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $this->uri->segment(4));

        $data['data'] = $this->Common->two_join_one_cond('hris_applications', 'schools', 'a.pre_school,a.district,a.empEmail,a.appStatus,b.schoolName,b.schoolID', 'b.schoolID=a.pre_school', 'a.dq', 1, 'a.district', 'b.schoolName', 'ASC');

        $this->load->view('pages/' . $page, $data);
    }

    public function abd_reportv4()
    {

        $page = "abdv4";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $data['settings'] = $this->SettingsModel->get_mis_settings();

        $data['title'] = 'REQUEST TO RETAIN RATING';
        $data['applicant'] = $this->Common->one_cond_row('hris_applicant', 'id', $this->uri->segment(3));
        $data['job'] = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $this->uri->segment(4));

        $data['data'] = $this->Common->two_join_one_cond('hris_applications', 'schools', 'a.pre_school,a.district,a.empEmail,a.appStatus,b.schoolName,b.schoolID', 'b.schoolID=a.pre_school', 'a.appStatus', 'Endorsed for Rating', 'a.district', 'b.schoolName', 'ASC');

        $this->load->view('pages/' . $page, $data);
    }

    public function abd_reportv5()
    {

        $page = "abdv5";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $data['settings'] = $this->SettingsModel->get_mis_settings();

        $data['title'] = 'REQUEST TO RETAIN RATING';
        $data['applicant'] = $this->Common->one_cond_row('hris_applicant', 'id', $this->uri->segment(3));
        $data['job'] = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $this->uri->segment(4));

        $data['data'] = $this->Common->two_join_one_cond('hris_applications', 'schools', 'a.pre_school,a.district,a.empEmail,a.appStatus,b.schoolName,b.schoolID', 'b.schoolID=a.pre_school', 'a.appStatus', 'Endorsed for Rating', 'a.district', 'b.schoolName', 'ASC');

        $this->load->view('pages/' . $page, $data);
    }
    public function hiring_non_all_applicant()
    {

        $page = "hqall";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $data['settings'] = $this->SettingsModel->get_mis_settings();

        $data['title'] = 'List of Qaulified Applicants';
        $data['job'] = $this->Common->two_cond_order_by('hris_jobvacancy', 'sy', date('Y'), 'jvStatus', 'Open', 'jobID', 'DESC');

        $this->load->view('pages/' . $page, $data);
    }

    public function hiring_non_qaulified()
    {

        $page = "hq";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $data['settings'] = $this->SettingsModel->get_mis_settings();

        $data['title'] = 'List of Qaulified Applicants';
        $data['job'] = $this->Common->one_cond_order_by('hris_jobvacancy', 'jvStatus', 'Open', 'jobID', 'DESC');

        $this->load->view('pages/' . $page, $data);
    }

    public function hiring_non_qaulified_rated()
    {

        $page = "hqd";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $data['settings'] = $this->SettingsModel->get_mis_settings();
        $data['title'] = 'List of Qaulified Applicants';
        //$data['job'] = $this->Common->two_cond_order_by('hris_jobvacancy', 'sy', date('Y'), 'jvStatus', 'Open', 'jobTitle', 'DESC');
        $data['job'] = $this->Common->two_cond_order_by('hris_jobvacancy', 'jvStatus', 'Open','promotion',0, 'jobTitle', 'DESC');

        $this->load->view('pages/' . $page, $data);
    }

    public function qaulified_promotion()
    {

        $page = "hqd_promotion";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $data['settings'] = $this->SettingsModel->get_mis_settings();
        $data['title'] = 'List of Qaulified Applicants';
        $data['job'] = $this->Common->two_cond_order_by_select('hris_jobvacancy','sy,jvStatus,jobID,promotion,jobTitle,job_type,assign','jvStatus', 'Open','promotion',1, 'jobTitle', 'DESC');


        $this->load->view('pages/' . $page, $data);
    }

    public function qaulified_promotion_job()
    {

        $jobID = $this->uri->segment(3);
        $page = "hqd_promotion";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $data['settings'] = $this->SettingsModel->get_mis_settings();
        $data['title'] = 'List of Qaulified Applicants';
        //$data['job'] = $this->Common->three_cond_order_by('hris_jobvacancy', 'sy', date('Y'), 'jvStatus', 'Open','jobID',$jobID, 'jobTitle', 'DESC');
        $data['job'] = $this->Common->three_cond_order_by_select('hris_jobvacancy','sy,jvStatus,jobID,promotion,jobTitle,job_type,assign','jobID',$jobID, 'sy', date('Y'),'jvStatus', 'Open','promotion',1, 'jobTitle', 'DESC');


        $this->load->view('pages/' . $page, $data);
    }


    public function hiring_non_not_qaulified()
    {

        $page = "hqn";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $data['settings'] = $this->SettingsModel->get_mis_settings();
        $data['title'] = 'List of Qaulified Applicants';
        $data['job'] = $this->Common->one_cond_order_by('hris_jobvacancy', 'jvStatus', 'Open', 'jobID', 'DESC');

        $this->load->view('pages/' . $page, $data);
    }

    public function not_qualify_promotion()
    {

        $page = "hqnv2";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $data['settings'] = $this->SettingsModel->get_mis_settings();
        $data['title'] = 'List of Qaulified Applicants';
        $data['job'] = $this->Common->two_cond_order_by('hris_jobvacancy', 'sy', date('Y'), 'jvStatus', 'Open', 'jobID', 'DESC');

        $this->load->view('pages/' . $page, $data);
    }

    public function abd_reportv6()
    {

        $page = "abdv6";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $data['settings'] = $this->SettingsModel->get_mis_settings();

        $data['title'] = 'REQUEST TO RETAIN RATING';
        $data['applicant'] = $this->Common->one_cond_row('hris_applicant', 'id', $this->uri->segment(3));
        $data['job'] = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $this->uri->segment(4));

        $data['a'] = $this->Common->two_cond_order_by('hris_applications', 'dq', 1, 'app_year', date('Y'), 'empEmail', 'ASC');


        $this->load->view('pages/' . $page, $data);
    }

    public function tr_special_update()
    {
        $this->Reg->trspecialupdate();
        redirect(base_url() . 'Pages/abd_reportv6');
    }


    public function dabd_report()
    {

        $page = "abdd";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $data['settings'] = $this->SettingsModel->get_mis_settings();
        $data['title'] = 'List of Disqualified Applicant/s';
        $data['dqval'] = 2;
        $data['applicant'] = $this->Common->one_cond_row('hris_applicant', 'id', $this->uri->segment(3));
        $data['job'] = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $this->uri->segment(4));

        $data['data'] = $this->Common->two_join_one_cond('hris_applications', 'schools', 'a.pre_school,a.district,a.empEmail,a.appStatus,a.dq,b.schoolName,b.schoolID', 'b.schoolID=a.pre_school', 'a.dq', 2, 'a.district', 'b.schoolName', 'ASC');

        $this->load->view('pages/' . $page, $data);
    }

    public function dabq_report()
    {

        $page = "abdd";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $data['settings'] = $this->SettingsModel->get_mis_settings();
        $data['title'] = 'Qualified Applicant/s';
        $data['dqval'] = 1;
        $data['applicant'] = $this->Common->one_cond_row('hris_applicant', 'id', $this->uri->segment(3));
        $data['job'] = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $this->uri->segment(4));

        $data['data'] = $this->Common->two_join_one_cond('hris_applications', 'schools', 'a.pre_school,a.district,a.empEmail,a.appStatus,a.dq,b.schoolName,b.schoolID', 'b.schoolID=a.pre_school', 'a.dq', 1, 'a.district', 'b.schoolName', 'ASC');

        $this->load->view('pages/' . $page, $data);
    }

    public function dabq_reportv2()
    {

        $page = "abddv2";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $data['settings'] = $this->SettingsModel->get_mis_settings();
        $data['title'] = 'District list';

        $this->load->view('pages/' . $page, $data);
    }

    public function dabq_reportv2_summary()
    {

        $page = "abd_summary";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $data['settings'] = $this->SettingsModel->get_mis_settings();
        $data['title'] = 'District list';

        $this->load->view('pages/' . $page, $data);
    }

    public function dabq_reportv2_list()
    {

        $page = "abddv2_list";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $data['settings'] = $this->SettingsModel->get_mis_settings();
        $data['title'] = 'Qualified Applicant/s';

        $this->load->view('pages/' . $page, $data);
    }

    public function school_applicant()
    {

        $page = "applicant_list";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        if ($this->uri->segment(4) == 0) {
            $data['data'] = $this->Common->three_cond('hris_applications', 'jobID', $this->uri->segment(3), 'pre_school', $this->session->c_id, 'appStatus', 'Application Submitted');
        } else {
            $data['data'] = $this->Common->two_cond('hris_applications', 'jobID', $this->uri->segment(3), 'pre_school', $this->session->c_id);
        }



        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function sa_view_applicant()
    {

        $job = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $this->uri->segment(3));
        if ($job->position == 1) {
            $page = "applicant_list_sa";
        } else {
            $page = "applicant_list_sa_none";
        }



        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }


        $data['data'] = $this->Common->two_cond('hris_applications', 'jobID', $this->uri->segment(3), 'appStatus', 'Validated');
        $data['district'] = $this->Common->no_cond('district');
        $data['job'] = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $this->uri->segment(3));

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function sa_view_applicant_endorsed()
    {

        $page = "applicant_list_sav2";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }


        $data['data'] = $this->Common->two_cond('hris_applications', 'jobID', $this->uri->segment(3), 'appStatus', 'Endorsed for Rating');
        $data['district'] = $this->Common->no_cond('district');
        $data['job'] = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $this->uri->segment(3));

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function evaluator_applicant()
    {

        $page = "applicant_list";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $data['dis'] = $this->Common->one_cond_row('district', 'id', $this->session->c_id);
        $district = $data['dis']->discription;

        $jobID = $this->uri->segment(3);
		$data['data'] = $this->Common->get_applicant_by_appstatus($jobID,'Endorsed for Rating','1',$district);

        // $d = $this->Common->one_cond_row('district', 'id', $this->session->c_id);


        // $job = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $this->uri->segment(3));
        // if ($job->position == 1) {
        //     $data['data'] = $this->Common->four_cond_or('hris_applications', 'jobID', $this->uri->segment(3),'district',$d->discription,'appStatus','Endorsed for Rating','dq',1,'appStatus','Rated','appStatus','Rated');
        //     //$data['data'] = $this->Common->four_cond('hris_applications', 'jobID', $this->uri->segment(3), 'appStatus', 'Endorsed for Rating', 'dq', 1, 'district', $d->discription);
        // } else {
        //     if ($this->session->eg == 1) {
        //         $data['data'] = $this->Common->three_cond('hris_applications', 'jobID', $this->uri->segment(3), 'appStatus', 'Endorsed for Rating', 'dq', 1);
        //     } else {
        //         $data['data'] = $this->Common->three_cond('hris_applications', 'jobID', $this->uri->segment(3), 'appStatus', 'Rated', 'dq', 1);
        //     }
        // }

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function Unqualified()
    {
        $dq = $this->input->post('remarks');
        $this->Reg->update_dq($dq);
        $this->Reg->insert_dq();

        $raterId = (int) $this->input->post('rater_id');
        $assignRater = ($raterId > 0);
        $assignedBy = $this->session->id ?? $this->session->userdata('id');
        $assignmentMsg = '';
        $assignmentDone = false;
        $assignmentSkipped = false;
        $assignmentFailed = false;

        if ($assignRater) {
            $validRater = $this->db
                ->where('id', $raterId)
                ->where('position', 'Evaluator')
                ->where('egroup', 1)
                ->count_all_results('users') > 0;
            if (!$validRater) {
                $assignRater = false;
                $assignmentMsg = ' Selected evaluator is not eligible; no assignment was made.';
            }
        }

        if ($dq == 1) {
            $this->Reg->ap_change_stat('Endorsed for Rating');
            $this->Reg->ap_trackv2('Endorsed for Rating.');
                $rate = $this->Common->two_cond_count_row('hris_applications_rating', 'appID', $this->input->post('appID'), 'record_no', $this->input->post('record_no'));
                if ($rate->num_rows() <= 0) {

                    $job = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $this->input->post('jobID'));

                    $this->Reg->insert_ndq_rate($job->position, $job->sy);
                    //$this->Reg->ap_track('The submitted documents has been Validated.');
                }

            // Optional: assign evaluator for this single application
            if ($assignRater) {
                $appRow = $this->Common->one_cond_row('hris_applications', 'appID', $this->input->post('appID'));
                if ($appRow) {
                    $jobRow = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $appRow->jobID);
                    $jobType = $jobRow->job_type ?? 0;

                    $applicant = $this->Common->one_cond_row('hris_applicant', 'id', $appRow->applicant_id);
                    if (empty($applicant)) {
                        $applicant = $this->Common->one_cond_row('hris_applicant', 'record_no', $appRow->applicant_id);
                    }
                    $spec = $applicant->specialization ?? '';

                    $existing = $this->db
                        ->where('fy', date('Y'))
                        ->where('app_id', $appRow->appID)
                        ->get('hris_rater_assignments')
                        ->row();

                    if ($existing) {
                        if ((int)$existing->rater_user_id !== $raterId) {
                            $this->db->where('id', $existing->id)
                                ->update('hris_rater_assignments', [
                                    'rater_user_id' => $raterId,
                                    'assigned_by' => $assignedBy,
                                    'assigned_at' => date('Y-m-d H:i:s'),
                                ]);
                            if ($this->db->affected_rows() > 0) {
                                $assignmentDone = true;
                            } else {
                                $assignmentFailed = true;
                            }
                        } else {
                            $assignmentSkipped = true;
                        }
                    } else {
                        $this->db->insert('hris_rater_assignments', [
                            'fy' => date('Y'),
                            'applicant_id' => $appRow->applicant_id,
                            'app_id' => $appRow->appID,
                            'job_id' => $appRow->jobID,
                            'job_type' => $jobType,
                            'specialization' => $spec,
                            'rater_user_id' => $raterId,
                            'assigned_by' => $assignedBy,
                            'assigned_at' => date('Y-m-d H:i:s'),
                        ]);

                        if ($this->db->affected_rows() > 0) {
                            $assignmentDone = true;
                        } else {
                            $assignmentFailed = true;
                        }
                    }
                } else {
                    $assignmentFailed = true;
                }
            }

        }
        if ($this->input->post('job_type') == 3) {
            $this->Reg->educ_jhss_update();
        }
        if ($this->input->post('job_type') == 4) {
            $this->Reg->educ_shss_update();
        }

        $msg = 'Successfuly Updated';
        if ($assignRater && $dq == 1) {
            if ($assignmentMsg !== '') {
                $msg .= $assignmentMsg;
            } else {
                if ($assignmentDone) {
                    $msg .= ' Evaluator assigned.';
                } elseif ($assignmentSkipped) {
                    $msg .= ' An evaluator is already assigned to this application.';
                } elseif ($assignmentFailed) {
                    $msg .= ' Unable to assign the evaluator.';
                }
            }
        }

        $this->session->set_flashdata('success', $msg);
        redirect(base_url() . 'pages/validated_list/' . $this->input->post('jobID') . '/?district=' . $this->input->post('dist'));
    }

    public function multi_remarks() {
        date_default_timezone_set('Asia/Manila');
        $date = date('Y-m-d');
        $fy = date('Y'); 
        $t = date('h:i:s a', time());
        $li = ($this->input->post('li') == '') ? 0 : 1;
        $da_pds = ($this->input->post('da_pds') == '') ? 0 : 1;
        $prc = ($this->input->post('prc') == '') ? 0 : 1;
        $trbd = ($this->input->post('trbd') == '') ? 0 : 1;
        $omni = ($this->input->post('omni') == '') ? 0 : 1;

        $educ = ($this->input->post('educ') == '') ? 0 : 1;
        $exp = ($this->input->post('exp') == '') ? 0 : 1;
        $tr = ($this->input->post('tr') == '') ? 0 : 1;
        $eli = ($this->input->post('eli') == '') ? 0 : 1;

        $dq = $this->input->post('remarks');

        $raterId = (int) $this->input->post('rater_id');
        $assignRater = ($raterId > 0);
        $assignedBy = $this->session->id ?? $this->session->userdata('id');
        $assignmentNote = '';
        $assignmentStats = ['added' => 0, 'exists' => 0, 'failed' => 0, 'reassigned' => 0];

        if ($assignRater) {
            $validRater = $this->db
                ->where('id', $raterId)
                ->where('position', 'Evaluator')
                ->where('egroup', 1)
                ->count_all_results('users') > 0;

            if (!$validRater) {
                $assignRater = false;
                $assignmentNote = ' Selected evaluator is not eligible; no assignment was made.';
            }
        }

        $selected = $this->input->post('app'); 
        $job_ids = $this->input->post('job_id'); 

        // If nothing was manually selected, default to all current-year applications (Validated or Endorsed for Rating)
        if (empty($selected)) {
            $empEmail = $this->input->post('empEmail');
            if (!empty($empEmail)) {
                $apps = $this->db
                    ->where('empEmail', $empEmail)
                    ->where('app_year', date('Y'))
                    ->where_in('appStatus', ['Validated', 'Endorsed for Rating'])
                    ->get('hris_applications')
                    ->result();

                foreach ($apps as $appRow) {
                    $selected[] = $appRow->appID;
                    $job_ids[] = $appRow->jobID;
                }
            }
        }
        if (!empty($selected)) {
            foreach ($selected as $appID => $val) {
                $jobID = $job_ids[$appID] ?? null;

                $data = [
                    'appID' => $val,
                    'jobID' => $jobID, 
                    'apID' => $this->input->post('id'), 
                    'li' => $li, 
                    'da_pds' => $da_pds, 
                    'prc' => $prc, 
                    'trbd' => $trbd, 
                    'omni' => $omni, 
                    'local' => $this->input->post('local'), 
                    'remarks' => $this->input->post('remarks'), 
                    'reason' => $this->input->post('reason'), 
                    'vdate' => $date,
                    'res' => $this->session->id,
                    'educ' => $educ,
                    'exp' => $exp,
                    'tr' => $tr,
                    'eli' => $eli,
                    'fy' =>  $fy
                ];
    
                $this->db->insert('hris_app_dq', $data);

                $ivyclaire = array(
                    'record_no' => $this->input->post('record_no'), 
                    'appID' => $val,
                    'education' => .00001, 
                    'training' => .00001, 
                    'experience' => .00001, 
                    'let_rating' => .00001, 
                    'demo_rating' => .00001, 
                    'tr_rating' => .00001,
                    'job_type' => $this->input->post('job_type'),
                    'fy' => $this->input->post('fy'),
                );

                $this->db->insert('hris_applications_rating', $ivyclaire);

                if($dq == 1){
                    $stat = 'Endorsed for Rating';
                }else{
                    $stat = 'Validated'; 
                }

                $update_data = [
                    'appStatus' => $stat,
                    'dq' => $dq,
                ];
                $this->db->where('appID', $val);
                $this->db->update('hris_applications', $update_data);

                // Optional: assign an evaluator when marking as Qualified
                if ($assignRater && $dq == 1) {
                    $appRow = $this->Common->one_cond_row('hris_applications', 'appID', $val);
                    if ($appRow) {
                        $jobRow = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $appRow->jobID);
                        $jobType = $jobRow->job_type ?? 0;

                        $applicant = $this->Common->one_cond_row('hris_applicant', 'id', $appRow->applicant_id);
                        if (empty($applicant)) {
                            $applicant = $this->Common->one_cond_row('hris_applicant', 'record_no', $appRow->applicant_id);
                        }
                        $spec = $applicant->specialization ?? '';

                        $existing = $this->db
                            ->where('fy', $fy)
                            ->where('app_id', $appRow->appID)
                            ->get('hris_rater_assignments')
                            ->row();

                        if ($existing) {
                            if ((int)$existing->rater_user_id !== $raterId) {
                                $this->db->where('id', $existing->id)
                                    ->update('hris_rater_assignments', [
                                        'rater_user_id' => $raterId,
                                        'assigned_by' => $assignedBy,
                                        'assigned_at' => date('Y-m-d H:i:s'),
                                    ]);
                                if ($this->db->affected_rows() > 0) {
                                    $assignmentStats['reassigned']++;
                                } else {
                                    $assignmentStats['failed']++;
                                }
                            } else {
                                $assignmentStats['exists']++;
                            }
                        } else {
                            $this->db->insert('hris_rater_assignments', [
                                'fy' => $fy,
                                'applicant_id' => $appRow->applicant_id,
                                'app_id' => $appRow->appID,
                                'job_id' => $appRow->jobID,
                                'job_type' => $jobType,
                                'specialization' => $spec,
                                'rater_user_id' => $raterId,
                                'assigned_by' => $assignedBy,
                                'assigned_at' => date('Y-m-d H:i:s'),
                            ]);

                            if ($this->db->affected_rows() > 0) {
                                $assignmentStats['added']++;
                            } else {
                                $assignmentStats['failed']++;
                            }
                        }
                    } else {
                        $assignmentStats['failed']++;
                    }
                }

            }

            $msg = 'Data successfully inserted!';
            if ($assignRater && $dq == 1) {
                if ($assignmentNote !== '') {
                    $msg .= $assignmentNote;
                } else {
                    if ($assignmentStats['added'] > 0) {
                        $msg .= ' Assigned '.$assignmentStats['added'].' application(s) to the selected evaluator.';
                    }
                    if ($assignmentStats['reassigned'] > 0) {
                        $msg .= ' Reassigned '.$assignmentStats['reassigned'].' application(s) to the selected evaluator.';
                    }
                    if ($assignmentStats['exists'] > 0) {
                        $msg .= ' '.$assignmentStats['exists'].' application(s) already had the same evaluator.';
                    }
                    if ($assignmentStats['failed'] > 0 && $assignmentStats['added'] == 0 && $assignmentStats['reassigned'] == 0) {
                        $msg .= ' Unable to assign '.$assignmentStats['failed'].' application(s).';
                    }
                }
            }

            $this->session->set_flashdata('success', $msg);
        } else {
            $this->session->set_flashdata('error', 'No job selected.');
        }
    
        if ($this->session->position === 'Secretariat') {
            redirect(base_url('Pages/endorsed_applicants'));
        }

        redirect(base_url() . 'pages/validated_list/' . $this->input->post('jobID') . '/?district=' . $this->input->post('dist'));
    }

    public function Unqualified_none()
    {
        $dq = $this->input->post('remarks');
        $this->Reg->update_dq($dq);
        $this->Reg->insert_dq();
        if ($dq == 1) {
            $this->Reg->ap_change_stat('Endorsed for Rating');
            $this->Reg->ap_trackv2('Endorsed for Rating.');
        }
        $this->Reg->insert_rate_none();


        $this->session->set_flashdata('success', 'Successfuly Updated');
        $redirect = $this->input->post('redirect');
        if (!empty($redirect)) {
            redirect($redirect);
        }
        redirect(base_url() . 'pages/validated_list/' . $this->input->post('jobID') . '/?district=' . $this->input->post('dist'));
    }

    public function Unqualified_promotion()
    {
        $dq = $this->input->post('remarks');
        $this->Reg->update_dq($dq);
        $this->Reg->insert_dq();
        if ($dq == 1) {
            $this->Reg->ap_change_stat('Endorsed for Rating');
            $this->Reg->ap_trackv2('Endorsed for Rating.');
        }
        $this->Reg->insert_rate_promotion();


        $this->session->set_flashdata('success', 'Successfuly Updated');
        $redirect = $this->input->post('redirect');
        if (!empty($redirect)) {
            redirect($redirect);
        }
        redirect(base_url() . 'pages/validated_list/' . $this->input->post('jobID') . '/?district=' . $this->input->post('dist'));
    }

    public function Unqualifiededit()
    {
        $dq = $this->input->post('remarks');
        $this->Reg->update_dq($dq);
        $this->Reg->update_dq2();

        $raterId = (int) $this->input->post('rater_id');
        $assignRater = ($raterId > 0);
        $assignedBy = $this->session->id ?? $this->session->userdata('id');
        $assignmentMsg = '';
        $assignmentDone = false;
        $assignmentSkipped = false;
        $assignmentFailed = false;

        if ($assignRater) {
            $validRater = $this->db
                ->where('id', $raterId)
                ->where('position', 'Evaluator')
                ->where('egroup', 1)
                ->count_all_results('users') > 0;
            if (!$validRater) {
                $assignRater = false;
                $assignmentMsg = ' Selected evaluator is not eligible; no assignment was made.';
            }
        }

        if ($dq == 2) {
            $this->Reg->ap_change_stat('Validated');
            $this->Common->tcd('hris_applications_track', 'app_id', $this->input->post('appID'), 'appStatus', 'Endorsed for Rating.');
        }
        if ($dq == 1) {
            $this->Reg->ap_change_stat('Endorsed for Rating');
            $this->Reg->ap_trackv2('Endorsed for Rating.');

            // Optional: assign evaluator for this single application
            if ($assignRater) {
                $appRow = $this->Common->one_cond_row('hris_applications', 'appID', $this->input->post('appID'));
                if ($appRow) {
                    $jobRow = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $appRow->jobID);
                    $jobType = $jobRow->job_type ?? 0;

                    $applicant = $this->Common->one_cond_row('hris_applicant', 'id', $appRow->applicant_id);
                    if (empty($applicant)) {
                        $applicant = $this->Common->one_cond_row('hris_applicant', 'record_no', $appRow->applicant_id);
                    }
                    $spec = $applicant->specialization ?? '';

                    $existing = $this->db
                        ->where('fy', date('Y'))
                        ->where('app_id', $appRow->appID)
                        ->get('hris_rater_assignments')
                        ->row();

                    if ($existing) {
                        if ((int)$existing->rater_user_id !== $raterId) {
                            $this->db->where('id', $existing->id)
                                ->update('hris_rater_assignments', [
                                    'rater_user_id' => $raterId,
                                    'assigned_by' => $assignedBy,
                                    'assigned_at' => date('Y-m-d H:i:s'),
                                ]);
                            if ($this->db->affected_rows() > 0) {
                                $assignmentDone = true;
                            } else {
                                $assignmentFailed = true;
                            }
                        } else {
                            $assignmentSkipped = true;
                        }
                    } else {
                        $this->db->insert('hris_rater_assignments', [
                            'fy' => date('Y'),
                            'applicant_id' => $appRow->applicant_id,
                            'app_id' => $appRow->appID,
                            'job_id' => $appRow->jobID,
                            'job_type' => $jobType,
                            'specialization' => $spec,
                            'rater_user_id' => $raterId,
                            'assigned_by' => $assignedBy,
                            'assigned_at' => date('Y-m-d H:i:s'),
                        ]);

                        if ($this->db->affected_rows() > 0) {
                            $assignmentDone = true;
                        } else {
                            $assignmentFailed = true;
                        }
                    }
                } else {
                    $assignmentFailed = true;
                }
            }
        }
        // if($this->input->post('job_type') == 2){
        //     $this->Reg-> educ_jhss_update();
        // }
        // if($this->input->post('job_type') == 3){
        //     $this->Reg-> educ_shss_update();
        // }

        $msg = 'Successfuly Updated';
        if ($assignRater && $dq == 1) {
            if ($assignmentMsg !== '') {
                $msg .= $assignmentMsg;
            } else {
                if ($assignmentDone) {
                    $msg .= ' Evaluator assigned.';
                } elseif ($assignmentSkipped) {
                    $msg .= ' An evaluator is already assigned to this application.';
                } elseif ($assignmentFailed) {
                    $msg .= ' Unable to assign the evaluator.';
                }
            }
        }

        $this->session->set_flashdata('success', $msg);
        $redirect = $this->input->post('redirect');
        if (!empty($redirect)) {
            redirect($redirect);
        }
        redirect(base_url() . 'pages/validated_list/' . $this->input->post('jobID') . '/?district=' . $this->input->post('dist'));
    }

    public function qualified()
    {
        $this->Reg->update_dq(0);
        $this->session->set_flashdata('success', 'Successfuly Updated');
        redirect(base_url() . 'pages/evaluator_applicant/' . $this->input->post('jobID'));
    }

    public function update_educ_rate()
    {

        $rn = $this->input->post('record_no');
        $appID = $this->input->post('app_id');
        $user = $this->input->post('page');
        if (empty($user)) {
            $page = 'ma';
        } else {
            $page = 'ma_staff';
        }

        $check = $this->Common->two_cond_row('hris_applications_rating', 'record_no', $rn, 'appId', $appID);
        if ($this->input->post('education') <= 10) {

            if ($check->eval_id1 == 0) {
                $this->Reg->update_eval('eval_id1');
            }

            $this->Reg->update_rate('education');
            $this->Reg->update_remarks('educ_remarks');

            $this->Reg->ap_track('The education rating has been encoded.');
            $this->session->set_flashdata('success', 'Successfuly Saved');
            redirect(base_url() . 'pages/' . $page . '/' . $this->uri->segment(3) . '/' . $this->uri->segment(4) . '/' . $this->input->post('school_id') . '/' . $this->input->post('app_id') . '/' . $this->input->post('record_no') . '#efile');
        } else {
            $this->session->set_flashdata('danger', 'The points exceeded the maximum allowed value.');
            redirect(base_url() . 'pages/' . $page . '/' . $this->uri->segment(3) . '/' . $this->uri->segment(4) . '/' . $this->input->post('school_id') . '/' . $this->input->post('app_id') . '/' . $this->input->post('record_no'));
        }
    }

    public function update_educ_rate_none()
    {

        $rn = $this->input->post('record_no');
        $appID = $this->input->post('app_id');

        $check = $this->Common->two_cond_row('hris_rating_none', 'record_no', $rn, 'appId', $appID);
        if ($this->input->post('educ') <= $this->input->post('max')) {

            if ($check->eval_id1 == 0) {
                $this->Reg->update_eval_none('eval_id1');
            }

            $this->Reg->update_rate_none('educ');
            $this->Reg->update_remarks('educ_remarks');
            
            $this->Reg->ap_track('The TOR & CAV - College / Graduate Studies rating has been encoded.');
            $this->session->set_flashdata('success', 'Successfuly Saved');
            redirect(base_url() . 'pages/' . $this->input->post('page') . '/' . $this->uri->segment(3) . '/' . $this->uri->segment(4) . '/' . $this->input->post('school_id') . '/' . $this->input->post('app_id') . '/' . $this->input->post('record_no') . '#efile');
        } else {
            $this->session->set_flashdata('danger', 'The points exceeded the maximum allowed value.');
            redirect(base_url() . 'pages/' . $this->input->post('page') . '/' . $this->uri->segment(3) . '/' . $this->uri->segment(4) . '/' . $this->input->post('school_id') . '/' . $this->input->post('app_id') . '/' . $this->input->post('record_no'));
        }
    }

    public function update_educ_rate_promotion()
    {

        $rn = $this->input->post('record_no');
        $appID = $this->input->post('app_id');

        $check = $this->Common->two_cond_row('hris_rating_promotion', 'record_no', $rn, 'appId', $appID);
        if ($this->input->post('educ') <= $this->input->post('max')) {

            if ($check->eval_id1 == 0) {
                $this->Reg->update_eval_promotion('eval_id1');
            }

            $this->Reg->update_rate_promotion('educ');
            $this->Reg->ap_track('The TOR & CAV - College / Graduate Studies rating has been encoded.');
            $this->session->set_flashdata('success', 'Successfuly Saved');
            redirect(base_url() . 'pages/' . $this->input->post('page') . '/' . $this->uri->segment(3) . '/' . $this->uri->segment(4) . '/' . $this->input->post('school_id') . '/' . $this->input->post('app_id') . '/' . $this->input->post('record_no') . '#efile');
        } else {
            $this->session->set_flashdata('danger', 'The points exceeded the maximum allowed value.');
            redirect(base_url() . 'pages/' . $this->input->post('page') . '/' . $this->uri->segment(3) . '/' . $this->uri->segment(4) . '/' . $this->input->post('school_id') . '/' . $this->input->post('app_id') . '/' . $this->input->post('record_no'));
        }
    }

    public function update_let_rate(){

        $rn = $this->input->post('record_no');
        $appID = $this->input->post('app_id');
        $user = $this->input->post('page');
        if (empty($user)) {
            $page = 'ma';
        } else {
            $page = 'ma_staff';
        }

        $check = $this->Common->two_cond_row('hris_applications_rating', 'record_no', $rn, 'appId', $appID);
        if ($this->input->post('let_rating') <= 10) {

            if ($check->eval_id1 == 0) {
                $this->Reg->update_eval('eval_id1');
            }

            $this->Reg->update_rate('let_rating');
            $this->Reg->ap_track('The LET rating has been encoded.');
            $this->session->set_flashdata('success', 'Successfuly Saved');
            redirect(base_url() . 'pages/' . $page . '/' . $this->uri->segment(3) . '/' . $this->uri->segment(4) . '/' . $this->input->post('school_id') . '/' . $this->input->post('app_id') . '/' . $this->input->post('record_no') . '#lr');
        } else {
            $this->session->set_flashdata('danger', 'The points exceeded the maximum allowed value.');
            redirect(base_url() . 'pages/' . $page . '/' . $this->uri->segment(3) . '/' . $this->uri->segment(4) . '/' . $this->input->post('school_id') . '/' . $this->input->post('app_id') . '/' . $this->input->post('record_no') . '#lr');
        }
    }

    public function update_tr_ic_rate()
    {

        $rn = $this->input->post('record_no');
        $appID = $this->input->post('app_id');
        $user = $this->input->post('page');
        if (empty($user)) {
            $page = 'ma';
        } else {
            $page = 'ma_staff';
        }

        $check = $this->Common->two_cond_row('hris_applications_rating', 'record_no', $rn, 'appId', $appID);
        if ($this->input->post('tr_rating') <= 25) {

            if ($check->eval_id1 == 0) {
                $this->Reg->update_eval('eval_id3');
            }

            $this->Reg->update_rate('tr_rating');
            $this->Reg->ap_track('Teachers Reflection rating has been encoded.');
            $this->session->set_flashdata('success', 'Successfuly Saved');
            redirect(base_url() . 'pages/' . $page . '/' . $this->uri->segment(3) . '/' . $this->uri->segment(4) . '/' . $this->input->post('school_id') . '/' . $this->input->post('app_id') . '/' . $this->input->post('record_no') . '#lr');
        } else {
            $this->session->set_flashdata('danger', 'The points exceeded the maximum allowed value.');
            redirect(base_url() . 'pages/' . $page . '/' . $this->uri->segment(3) . '/' . $this->uri->segment(4) . '/' . $this->input->post('school_id') . '/' . $this->input->post('app_id') . '/' . $this->input->post('record_no') . '#lr');
        }
    }

    public function update_demo_ic_rate()
    {

        $rn = $this->input->post('record_no');
        $appID = $this->input->post('app_id');
        $user = $this->input->post('page');
        if (empty($user)) {
            $page = 'ma';
        } else {
            $page = 'ma_staff';
        }

        $check = $this->Common->two_cond_row('hris_applications_rating', 'record_no', $rn, 'appId', $appID);
        if ($this->input->post('demo_rating') <= 35) {

            if ($check->eval_id1 == 0) {
                $this->Reg->update_eval('eval_id2');
            }

            $this->Reg->update_rate('demo_rating');
            $this->Reg->ap_track('Demo rating has been encoded.');
            $this->session->set_flashdata('success', 'Successfuly Saved');
            redirect(base_url() . 'pages/' . $this->uri->segment(7) . '/' . $this->uri->segment(3) . '/' . $this->uri->segment(4) . '/' . $this->input->post('school_id') . '/' . $this->input->post('app_id') . '/' . $this->input->post('record_no') . '#lr');
        } else {
            $this->session->set_flashdata('danger', 'The points exceeded the maximum allowed value.');
            redirect(base_url() . 'pages/' . $page . '/' . $this->uri->segment(3) . '/' . $this->uri->segment(4) . '/' . $this->input->post('school_id') . '/' . $this->input->post('app_id') . '/' . $this->input->post('record_no') . '#lr');
        }
    }

    public function update_training_rate(){
        $rn = $this->input->post('record_no');
        $appID = $this->input->post('app_id');
        $user = $this->input->post('page');
        if (empty($user)) {
            $page = 'ma';
        } else {
            $page = 'ma_staff';
        }
        $check = $this->Common->two_cond_row('hris_rating_none', 'record_no', $rn, 'appId', $appID);
        if($this->input->post('training') <= 10){
            // if($check->eval_id1 == 0){
            //     $this->Reg->update_eval('eval_id1');
            // }
            $this->Reg->update_rate('training');
            $this->Reg->update_remarks('training_remarks');

            $this->Reg->ap_track('The trainings and seminars rating has been encoded.');
            $this->session->set_flashdata('success', 'Successfuly Saved');
            redirect(base_url() . 'pages/' . $page . '/' . $this->uri->segment(3) . '/' . $this->uri->segment(4) . '/' . $this->input->post('school_id') . '/' . $this->input->post('app_id') . '/' . $this->input->post('record_no') . '#tsc');
        } else {
            $this->session->set_flashdata('danger', 'The points exceeded the maximum allowed value.');
            redirect(base_url() . 'pages/' . $page . '/' . $this->uri->segment(3) . '/' . $this->uri->segment(4) . '/' . $this->input->post('school_id') . '/' . $this->input->post('app_id') . '/' . $this->input->post('record_no') . '#tsc');
        }
    }

    public function update_rate_none()
    {

        $rn = $this->input->post('record_no');
        $appID = $this->input->post('app_id');
        $col = $this->input->post('col');
        $remarks = $this->input->post('remark_col');
        $message = $this->input->post('message');
        $maxpoint = $this->input->post('maxpoint');

        $check = $this->Common->two_cond_row('hris_rating_none', 'record_no', $rn, 'appId', $appID);
        if ($this->input->post($col) <= $maxpoint) {

            if ($check->eval_id1 == 0) { 
                $this->Reg->update_eval_none('eval_id1');
            }

            $this->Reg->update_rate_none($col);
            $this->Reg->update_remarks($remarks);
            $this->Reg->ap_track('The ' . $message . ' rating has been encoded.');
            $this->session->set_flashdata('success', 'Successfuly Saved');
            redirect(base_url() . 'pages/' . $this->input->post('page') . '/' . $this->uri->segment(3) . '/' . $this->uri->segment(4) . '/' . $this->input->post('school_id') . '/' . $this->input->post('app_id') . '/' . $this->input->post('record_no') . '#tsc');
        } else {
            $this->session->set_flashdata('danger', 'The points exceeded the maximum allowed value.');
            redirect(base_url() . 'pages/' . $this->input->post('page') . '/' . $this->uri->segment(3) . '/' . $this->uri->segment(4) . '/' . $this->input->post('school_id') . '/' . $this->input->post('app_id') . '/' . $this->input->post('record_no') . '#tsc');
        }
    }

    public function update_rate_nonev2()
    {

        $rn = $this->input->post('record_no');
        $appID = $this->input->post('app_id');
        $col = $this->input->post('col');
        $remarks = $this->input->post('remark_col');
        $message = $this->input->post('message');
        $maxpoint = $this->input->post('maxpoint');

        $check = $this->Common->two_cond_row('hris_rating_none', 'record_no', $rn, 'appId', $appID);
        if ($this->input->post($col) <= $maxpoint) {

            if ($check->eval_id1 == 0) { 
                $this->Reg->update_eval_none('eval_id1');
            }

            $this->Reg->update_rate_none($col);
            $this->Reg->ap_track('The ' . $message . ' rating has been encoded.');
            $this->session->set_flashdata('success', 'Successfuly Saved');
            redirect(base_url() . 'pages/' . $this->input->post('page') . '/' . $this->uri->segment(3) . '/' . $this->uri->segment(4) . '/' . $this->input->post('school_id') . '/' . $this->input->post('app_id') . '/' . $this->input->post('record_no') . '#tsc');
        } else {
            $this->session->set_flashdata('danger', 'The points exceeded the maximum allowed value.');
            redirect(base_url() . 'pages/' . $this->input->post('page') . '/' . $this->uri->segment(3) . '/' . $this->uri->segment(4) . '/' . $this->input->post('school_id') . '/' . $this->input->post('app_id') . '/' . $this->input->post('record_no') . '#tsc');
        }
    }

    public function update_rate_promotion()
    {

        $rn = $this->input->post('record_no');
        $appID = $this->input->post('app_id');
        $col = $this->input->post('col');
        $message = $this->input->post('message');
        $maxpoint = $this->input->post('maxpoint');

        $check = $this->Common->two_cond_row('hris_rating_promotion', 'record_no', $rn, 'appId', $appID);
        if ($this->input->post($col) <= $maxpoint) {

            if ($check->eval_id1 == 0) {
                $this->Reg->update_eval_none('eval_id1');
            }

            $this->Reg->update_rate_promotion($col);
            $this->Reg->ap_track('The ' . $message . ' rating has been encoded.');
            $this->session->set_flashdata('success', 'Successfuly Saved');
            redirect(base_url() . 'pages/' . $this->input->post('page') . '/' . $this->uri->segment(3) . '/' . $this->uri->segment(4) . '/' . $this->input->post('school_id') . '/' . $this->input->post('app_id') . '/' . $this->input->post('record_no') . '#tsc');
        } else {
            $this->session->set_flashdata('danger', 'The points exceeded the maximum allowed value.');
            redirect(base_url() . 'pages/' . $this->input->post('page') . '/' . $this->uri->segment(3) . '/' . $this->uri->segment(4) . '/' . $this->input->post('school_id') . '/' . $this->input->post('app_id') . '/' . $this->input->post('record_no') . '#tsc');
        }
    }

    public function update_rate_none_eval2()
    {

        $rn = $this->input->post('record_no');
        $appID = $this->input->post('app_id');
        $col = $this->input->post('col');
        $message = $this->input->post('message');
        $maxpoint = $this->input->post('maxpoint');

        $check = $this->Common->two_cond_row('hris_rating_none', 'record_no', $rn, 'appId', $appID);
        if ($this->input->post($col) <= $maxpoint) {

            if ($check->eval_id2 == 0) {
                $this->Reg->update_eval_none('eval_id2');
            }

            $this->Reg->update_rate_none($col);
            //$this->Reg->ap_track('The '. $message. ' rating has been encoded.');
            $this->session->set_flashdata('success', 'Successfuly Saved');
            redirect(base_url() . 'pages/' . $this->input->post('page') . '/' . $this->uri->segment(3));
        } else {
            $this->session->set_flashdata('danger', 'The points exceeded the maximum allowed value.');
            redirect(base_url() . 'pages/' . $this->input->post('page') . '/' . $this->uri->segment(3));
        }
    }

    public function update_rate_none_eval3()
    {

        $rn = $this->input->post('record_no');
        $appID = $this->input->post('app_id');
        $col = $this->input->post('col');
        $message = $this->input->post('message');
        $maxpoint = $this->input->post('maxpoint');

        $check = $this->Common->two_cond_row('hris_rating_none', 'record_no', $rn, 'appId', $appID);
        if ($this->input->post($col) <= $maxpoint) {

            if ($check->eval_id3 == 0) {
                $this->Reg->update_eval_none('eval_id3');
            }

            $this->Reg->update_rate_none($col);
            //$this->Reg->ap_track('The '. $message. ' rating has been encoded.');
            $this->session->set_flashdata('success', 'Successfuly Saved');
            redirect(base_url() . 'pages/' . $this->input->post('page') . '/' . $this->uri->segment(3));
        } else {
            $this->session->set_flashdata('danger', 'The points exceeded the maximum allowed value.');
            redirect(base_url() . 'pages/' . $this->input->post('page') . '/' . $this->uri->segment(3));
        }
    }

    public function update_we_rate()
    {

        $rn = $this->input->post('record_no');
        $appID = $this->input->post('app_id');
        $user = $this->input->post('page');
        if (empty($user)) {
            $page = 'ma';
        } else {
            $page = 'ma_staff';
        }

        $check = $this->Common->two_cond_row('hris_applications_rating', 'record_no', $rn, 'appId', $appID);
        if ($this->input->post('training') <= 10) {

            if ($check->eval_id1 == 0) {
                $this->Reg->update_eval('eval_id1');
            }

            $this->Reg->update_rate('experience');
            $this->Reg->update_remarks('experience_remarks');
            
            $this->Reg->ap_track('The experience rating has been encoded.');
            $this->session->set_flashdata('success', 'Successfuly Saved');
            redirect(base_url() . 'pages/' . $page . '/' . $this->uri->segment(3) . '/' . $this->uri->segment(4) . '/' . $this->input->post('school_id') . '/' . $this->input->post('app_id') . '/' . $this->input->post('record_no') . '#ept');
        } else {
            $this->session->set_flashdata('danger', 'The points exceeded the maximum allowed value.');
            redirect(base_url() . 'pages/' . $page . '/' . $this->uri->segment(3) . '/' . $this->uri->segment(4) . '/' . $this->input->post('school_id') . '/' . $this->input->post('app_id') . '/' . $this->input->post('record_no') . '#ept');
        }
    }

    public function update_dm_rate()
    {
        if ($this->input->post('demo_rating') <= 35) {

            $this->Reg->update_rate('demo_rating');
            $this->Reg->update_eval('eval_id2');
            $this->Reg->ap_track_apply('The demo rating has been encoded.', $this->input->post('app_id'));
            $this->session->set_flashdata('success', 'Successfuly Saved');
            if ($this->session->position == 'asds') {
                redirect(base_url() . 'pages/applicant_app/' . $this->input->post('record_no'));
            }
            redirect(base_url() . 'pages/evaluator_applicant/' . $this->uri->segment(4));
        } else {
            $this->session->set_flashdata('danger', 'The points exceeded the maximum allowed value.');
            redirect(base_url() . 'pages/evaluator_applicant/' . $this->uri->segment(4));
        }
    }

    public function update_cdm_rate()
    {
        if ($this->input->post('demo_rating') <= 35) {
            $this->Reg->update_rate('demo_rating');
            $this->Reg->ap_track_apply('The demo rating has been encoded.', $this->input->post('app_id'));
            $this->session->set_flashdata('success', 'Successfuly Saved');
            redirect(base_url() . 'pages/evaluator_applicant/' . $this->uri->segment(3));
        } else {
            $this->session->set_flashdata('danger', 'The points exceeded the maximum allowed value.');
            redirect(base_url() . 'pages/evaluator_applicant/' . $this->uri->segment(3));
        }
    }

    public function update_tr_rate()
    {
        if ($this->input->post('tr_rating') <= 25) {
            $this->Reg->update_rate('tr_rating');
            $this->Reg->update_eval('eval_id3');
            $this->Reg->ap_track_apply("The teacher's reflection rating has been encoded.", $this->input->post('app_id'));
            $this->session->set_flashdata('success', 'Successfuly Saved');
            if ($this->session->position == 'asds') {
                redirect(base_url() . 'pages/applicant_app/' . $this->input->post('record_no'));
            }
            redirect(base_url() . 'pages/evaluator_applicant/' . $this->uri->segment(4));
        } else {
            $this->session->set_flashdata('danger', 'The points exceeded the maximum allowed value.');
            redirect(base_url() . 'pages/evaluator_applicant/' . $this->uri->segment(4));
        }
    }

    public function update_trc_rate()
    {
        if ($this->input->post('tr_rating') <= 25) {
            $this->Reg->ap_track_apply("The teacher's reflection rating has been encoded.", $this->input->post('app_id'));
            $this->session->set_flashdata('success', 'Successfuly Saved');
            redirect(base_url() . 'pages/evaluator_applicant/' . $this->uri->segment(4));
        } else {
            $this->session->set_flashdata('danger', 'The points exceeded the maximum allowed value.');
            redirect(base_url() . 'pages/evaluator_applicant/' . $this->uri->segment(4));
        }
    }

    public function closed_vacancy()
    {
        $this->Reg->applicant_stat(1);
        $this->Reg->application_close_open(1);
        $this->Reg->lock_applicant_application('hris_staff', $this->uri->segment(3), 'IDNumber');
        $this->Reg->lock_applicant_application('hris_applicant', $this->uri->segment(3), 'empEmail ');
        $this->session->set_flashdata('success', 'Successfuly Saved');
        redirect(base_url() . 'page/jobVacancy');
    }
    public function open_vacancy()
    {
        $this->Reg->applicant_stat(0);
        $this->Reg->application_close_open(0);
        $this->session->set_flashdata('success', 'Successfuly Saved');
        redirect(base_url() . 'page/jobVacancy');
    }

    public function lock_applications()
    {
        $this->Reg->lock_application(1);
        $this->Reg->lock_applications(1);
        $this->Reg->applicant_stat(1);
        $this->session->set_flashdata('success', 'Successfuly Saved');
        redirect(base_url() . 'page/jobVacancy');
    }

    public function unlock_ads($jobID = null)
    {
        if (!empty($jobID)) {
            $this->Reg->lock_applicant_document_submission_by_job($jobID, 0);
        } else {
            $this->Reg->lock_applicant_document_submission('hris_applicant', 0);
            $this->Reg->lock_applicant_document_submission('hris_staff', 0);
            $this->Reg->lock_applications(0);
        }
        $this->session->set_flashdata('success', 'Successfuly Saved');
        redirect(base_url() . 'page/jobVacancy');
    }

    public function lock_ads($jobID = null)
    {
        if (!empty($jobID)) {
            $this->Reg->lock_applicant_document_submission_by_job($jobID, 1);
        } else {
            $this->Reg->lock_applicant_document_submission('hris_applicant', 1);
            $this->Reg->lock_applicant_document_submission('hris_staff', 1);
            $this->Reg->lock_applications(1);
        }
        $this->session->set_flashdata('success', 'Successfuly Saved');
        redirect(base_url() . 'page/jobVacancy');
    }

    public function unlock_applications()
    {
        $this->Reg->lock_application(0);
        $this->Reg->lock_applications(0);
        $this->Reg->applicant_stat(0);
        $this->session->set_flashdata('success', 'Successfuly Saved');
        redirect(base_url() . 'page/jobVacancy');
    }

    public function in_lock_applications()
    {
        $this->Reg->in_lock_application(1);

        // $ee = $this->input->get('ee');
        // if(strpos($ee, '@deped.gov.ph') !== false) { 
        //     $this->Reg->lock_staff(1);
        // }else{
        //     $this->Reg->lock_applicant(1);
        // }


        $this->session->set_flashdata('success', 'Successfuly Saved');
        redirect(base_url() . 'page/viewApplicants?jobID=' . $this->uri->segment(3) . '&jobTitle=' . $this->input->get('jt'));
    }



    public function unlock_applicant()
    {
        $this->Reg->applicant_status(0);
        $this->Reg->application_status(0);
        $this->session->set_flashdata('success', 'Successfuly Updated');
        redirect(base_url() . 'page/jobVacancy');
    }
    public function lock_applicant()
    {
        $this->Reg->application_status(1);
        $this->Reg->applicant_status(1);
        $this->session->set_flashdata('success', 'Successfuly Updated');
        redirect(base_url() . 'page/jobVacancy');
    }

    public function in_unlock_applications()
    {
        $this->Reg->in_lock_application(0);
        $this->Reg->unlock_applicant(0);
        $this->session->set_flashdata('success', 'Successfuly Saved');
        redirect(base_url() . 'page/viewApplicants?jobID=' . $this->uri->segment(3) . '&jobTitle=' . $this->input->get('jt'));
    }

    public function open_applications()
    {
        $id = $this->uri->segment(4);

        $this->Reg->in_lock_application(0);
        $this->Reg->unlock_applicant($id);
        $this->session->set_flashdata('success', 'Successfuly Saved');
        redirect(base_url() . 'page/viewApplicants?jobID=' . $this->uri->segment(3) . '&jobTitle=' . $this->input->get('jt'));
    }

    public function close_applications()
    {
        $id = $this->uri->segment(4);

        $this->Reg->in_lock_application(1);
        $this->Reg->lock_applicant($id);
        $this->session->set_flashdata('success', 'Successfuly Saved');
        redirect(base_url() . 'page/viewApplicants?jobID=' . $this->uri->segment(3) . '&jobTitle=' . $this->input->get('jt'));
    }

    public function edit_vacancy()
    {

        $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        ', '</div>');
        $this->form_validation->set_rules('empType', 'Position Type', 'required');

        if ($this->form_validation->run() == FALSE) {

            $page = "jobvacancy_edit";

            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }

            $data['data'] = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $this->uri->segment(3));
            $data['pos_title'] = $this->Common->no_cond_order_by('hris_positions', 'title', 'ASC');

            $this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page, $data);
            $this->load->view('templates/footer');
        } else {

            $this->Reg->edit_jobvacancy();
            $this->session->set_flashdata('success', 'Update Job Vacancy.');
            redirect(base_url() . 'Page/jobVacancy');
        }
    }

    public function jobVacancy_file_update()
    {
        $config['allowed_types'] = 'pdf';
        $config['upload_path'] = './uploads/regfile';
        $new_name = time() . 'documents' . $this->input->post('jobID') . $_FILES["file_name"]['name'];
        $config['file_name'] = $new_name;
        $this->load->library('upload', $config);

        if ($this->upload->do_upload('file')) {
            $jobID = $this->input->post('jobID');
            $reg = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $jobID);
            if (!empty($reg->file)) {
            unlink("uploads/regfile/" . $reg->file);
            }
            $this->Reg->doc_update();
            $this->session->set_flashdata('success', 'Successfully updated.');
            redirect(base_url() . 'Page/jobVacancy');
        } else {
            $this->session->set_flashdata('danger', $this->upload->display_errors());
            redirect(base_url() . 'Page/jobVacancy');
        }
    }

    public function application_history()
    {

        $page = "application_history";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $data['data'] = $this->Common->two_cond_order_by('hris_applications_track', 'applicant_id', $this->uri->segment(3), 'app_id', $this->uri->segment(5), 'trackID', 'DESC');
        $data['job'] = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $this->uri->segment(4));
        $data['app'] = $this->Common->two_cond_row('hris_applications', 'applicant_id', $this->uri->segment(3), 'jobID', $this->uri->segment(4));


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function inquiry()
    {

        $page = "application_inquiry";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $applicantId = $this->uri->segment(3);
        $jobId       = $this->uri->segment(4);
        $appId       = $this->uri->segment(6);

        $data['data'] = $this->Common->two_cond_order_by('hris_application_inquiry', 'applicant_id', $applicantId, 'application_id', $appId, 'id', 'DESC');
        $data['job']  = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $jobId);

        // primary fetch by applicant_id + jobID
        $data['app']  = $this->Common->two_cond_row('hris_applications', 'applicant_id', $applicantId, 'jobID', $jobId);
        if (!$data['app']) { // fallback by appID if applicant_id style doesn't match
            $data['app'] = $this->Common->one_cond_row('hris_applications', 'appID', $appId);
        }

        // Get evaluator's rating for this applicant
        $data['my_rating'] = $this->Common->two_cond_row('hris_applications_rating', 'appID', $appId, 'record_no', $this->uri->segment(7));

        // applicant profile: try numeric id, then record_no
        $data['a'] = $this->Common->one_cond_row('hris_applicant', 'id', $applicantId);
        if (!$data['a']) {
            $data['a'] = $this->Common->one_cond_row('hris_applicant', 'record_no', $applicantId);
        }


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function inquiry_non()
    {

        $page = "application_inquiry_non";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $applicant = $this->uri->segment(3);
        $appID = $this->uri->segment(6);
        $jobID = $this->uri->segment(4);

        $data['data'] = $this->Common->one_cond_order_by('hris_application_inquiry', 'application_id', $appID, 'id', 'DESC');
        $data['job'] = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $jobID);
        $data['app'] = $this->Common->one_cond_row('hris_applications', 'appID', $appID);
        $data['my_rating'] = $this->Common->two_cond_row('hris_applications_rating', 'appID', $appID, 'record_no', $this->uri->segment(7));
        //$data['a'] = $this->Common->one_cond_row('hris_applicant','id',$this->uri->segment(3));
        $this->Reg->update_query_stat($applicant,$jobID);


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function appinquiry()
    {
        $comment = trim((string) $this->input->post('comment'));
        $appId = $this->input->post('app_id');
        $backUrl = $_SERVER['HTTP_REFERER'] ?? base_url();

        if ($comment === '') {
            $this->session->set_flashdata('danger', 'Please enter your query first.');
            redirect($backUrl);
            return;
        }

        if (in_array((string) $this->session->position, ['reg', 'user'], true)) {
            $existingApplicantQuery = $this->db
                ->where('application_id', $appId)
                ->where('res', $this->session->username)
                ->count_all_results('hris_application_inquiry');

            if ($existingApplicantQuery > 0) {
                $this->session->set_flashdata('danger', 'You can only submit one query for this application.');
                redirect($backUrl);
                return;
            }
        }

        $this->Reg->app_inquiry();
        $this->session->set_flashdata('success', 'Successfuly Saved');
        redirect($backUrl); 
    }

    public function comment()
    {
        $comment = $this->input->post('comment');
        $this->Reg->ap_track_comment($comment);
        $this->session->set_flashdata('success', 'Successfuly Saved');
        redirect(base_url() . 'pages/application_history/' . $this->uri->segment(3) . '/' . $this->uri->segment(4) . '/' . $this->uri->segment(5) . '/' . $this->uri->segment(6));
    }


    public function remove_attachment()
    {
        $col = $this->uri->segment(6);
        $id = $this->uri->segment(3);
        $reg = $this->Common->one_cond_row('hris_applicant', 'id', $id);
        $this->Page_model->insert_at('Removed Attachment', $id);
        if (!empty($reg->$col)) {
        unlink("uploads/regfile/" . $reg->{$col});
        }
        $this->Reg->remove_attach($col);
        $this->session->set_flashdata('success', 'Successfuly Removed');
        redirect(base_url() . 'pages/ma/' . $this->uri->segment(3) . '/' . $this->uri->segment(4) . '/' . $this->uri->segment(5));
    }

    public function remove_attachment_staff()
    {
       $col = $this->uri->segment(6);
      $id = $this->uri->segment(3);
      $reg = $this->Common->one_cond_row('hris_staff', 'IDNumber', $id);
      $this->Page_model->insert_at('Removed Attachment', $id);

      $file = "uploads/regfile/" . $reg->{$col};
      if (is_file($file)) {
        unlink($file);
    }

    $this->Reg->remove_attach_staff($col);
    $this->session->set_flashdata('success', 'Successfully Removed');

    redirect(base_url() . 'pages/ma_staff/' . $this->uri->segment(3) . '/' . $this->uri->segment(4) . '/' . $this->uri->segment(5));
    }

    public function remove_attachment_app()
    {
        $col = $this->uri->segment(6);
        $id = $this->uri->segment(7);
        $reg = $this->Common->one_cond_row('hris_applications', 'appID', $id);
        if (!empty($reg->$col)) {
        unlink("uploads/regfile/" . $reg->{$col});
        }
        $this->Reg->remove_attach_app($col);
        $this->session->set_flashdata('success', 'Successfuly Removed');
        redirect(base_url() . 'pages/ma/' . $this->uri->segment(3) . '/' . $this->uri->segment(4) . '/' . $this->uri->segment(5));
    }

    public function remove_attachment_app_none()
    {
        $col = $this->uri->segment(6);
        $id = $this->uri->segment(7);
        $reg = $this->Common->one_cond_row('hris_applications', 'appID', $id);
        if (!empty($reg->$col)) {
        unlink("uploads/regfile/" . $reg->{$col});
        }
        $this->Reg->remove_attach_app($col);
        $this->session->set_flashdata('success', 'Successfuly Removed');
        redirect(base_url() . 'pages/ma_staff/' . $this->uri->segment(3) . '/' . $this->uri->segment(4) . '/' . $this->uri->segment(5));
    }

    public function nofitychangestat()
    {
        $this->Reg->notifychange();
        redirect(base_url() . 'Pages/application_history/' . $this->uri->segment(3) . '/' . $this->uri->segment(4) . '/0/' . $this->uri->segment(5));
    }

    public function nofitychangestatadmin()
    {
        $this->Reg->notifychangeadmin();
        redirect(base_url() . 'Pages/application_history/' . $this->uri->segment(3) . '/' . $this->uri->segment(4) . '/0/' . $this->uri->segment(5));
    }

    public function school_list_applicant()
    {

        $page = "list_school_applicant";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $dist = $this->Common->one_cond_row('district', 'id', $this->session->c_id);

        $data['data'] = $this->Common->two_cond_group('hris_applications', 'jobID', $this->uri->segment(3), 'district', $dist->discription, 'pre_school');




        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function school_list()
    {

        $page = "school_list";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $dist = $this->Common->one_cond_row('district', 'id', $this->session->c_id);

        $data['data'] = $this->Common->one_cond('schools', 'district', $dist->discription);


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function school_generate_report()
    {

        $page = "school_report";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $fy = date('Y');
        //$dist = $this->Common->one_cond_row('district', 'id',$this->session->c_id);
        //$data['data'] = $this->Common->one_cond('schools', 'district',$dist->discription);
        $data['title'] = 'Validated Applicant';
        //$data['data'] = $this->Common->three_cond_not_equal('hris_applications', 'pre_school',$this->session->c_id,'app_year',$fy,'appStatus','Application Submitted','applicant_id','ASC');
        $data['job'] = $this->Common->one_cond_loop_order_by('hris_jobvacancy', 'sy', $fy, 'jobID', 'Desc');

        $this->load->view('pages/' . $page, $data);
    }

    public function district_generate_report()
    {

        $page = "district_report";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $fy = $this->input->post('fy');
        $dist = $this->Common->one_cond_row('district', 'id', $this->session->c_id);
        $data['district'] = $this->Common->one_cond_row('district', 'id', $this->session->c_id);
        $data['title'] = 'Validated Applicant';
        $data['data'] = $this->Common->three_cond_not_equal('hris_applications', 'district', $dist->discription, 'app_year', $fy, 'appStatus', 'Application Submitted', 'applicant_id', 'ASC');


        $this->load->view('pages/' . $page, $data);
    }

    public function dgr()
    {

        $page = "district_transmittal";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $fy = date('Y');
        $dist = $this->Common->one_cond_row('district', 'id', $this->session->c_id);
        $data['district'] = $this->Common->one_cond_row('district', 'id', $this->session->c_id);
        $data['title'] = 'Validated Applicant';
        $data['data'] = $this->Common->three_cond_not_equal_gb('hris_applications', 'district', $dist->discription, 'app_year', $fy, 'appStatus', 'Application Submitted', 'applicant_id', 'ASC', 'pre_school');


        $this->load->view('pages/' . $page, $data);
    }

    public function validated_applicant()
    {

        $page = "validated";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $fy = date('Y');
        $dist = $this->Common->one_cond_row('district', 'id', $this->session->c_id);
        $data['district'] = $this->Common->one_cond_row('district', 'id', $this->session->c_id);
        $data['title'] = 'Validated Applicant';
        if ($this->session->position === 'Secretariat') {
            $jobTypes = $this->secretariat->user_job_types((int) ($this->session->id ?? $this->session->userdata('id')));
            if (empty($jobTypes)) {
                $this->session->set_flashdata('danger', 'No level assignment found for your account. Please contact a Super Admin.');
            }
            $data['assigned_job_types'] = $jobTypes;
            $data['data'] = empty($jobTypes) ? [] : $this->Common->get_applicant_staff_validated('Open', 'Validated', $jobTypes);
        } elseif ($this->session->position === 'HR Staff' || $this->session->position === 'Human Resource Admin' || $this->session->position === 'asds' || $this->session->position === 'Super Admin' || $this->session->position === 'Admin') {
            //$data['data'] = $this->Common->two_cond('hris_applications', 'app_year', $fy, 'appStatus', 'Validated');
            $data['data'] = $this->Common->get_applicant_staff_validated('Open','Validated');
        } else {
            $data['data'] = $this->Page_model->get_applicant_staff_validated_school_account('Open','Validated');
        }

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }






    public function applicant_list()
    {

        $page = "validated";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $fy = date('Y');
        $dist = $this->Common->one_cond_row('district', 'id', $this->session->c_id);
        $data['district'] = $this->Common->one_cond_row('district', 'id', $this->session->c_id);
        $data['title'] = 'Validated Applicant';
        $data['data'] = $this->Page_model->list_of_applicant();


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function validated_list()
    {
        $job = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $this->uri->segment(3));

        if ($job->position == 1) {
            $page = "validated";
        } else {
            $page = "validated_none";
        }


        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $fy = date('Y');
        $data['title'] = 'Validated Applicant';
        $jobID = $this->uri->segment(3);
        $district = $this->input->get('district');
        
        if ($job->position == 1) {
            //$data['data'] = $this->Common->five_cond('hris_applications', 'dq', 0, 'app_year', $job->sy, 'district', $this->input->get('district'), 'jobID', $this->uri->segment(3), 'appStatus', 'Validated');
            $data['data'] = $this->Common->get_endorsed_applicant_with_stat('Open',1,$district,$jobID,'Validated');
        } else {
            //$data['data'] = $this->Common->five_cond('hris_applications', 'dq', 0, 'app_year', $job->sy, 'district', $this->input->get('district'), 'jobID', $this->uri->segment(3), 'appStatus', 'Application Submitted');
            $data['data'] = $this->Common->get_endorsed_applicant_with_stat('Open',1,$district,$jobID,'Application Submitted');
        }


        $data['job'] = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $this->uri->segment(3));

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function endorsed_list()
    {
        $data['job'] = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $this->uri->segment(3));

        $page = "validated";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $fy = date('Y');
        $data['title'] = 'Validated Applicant';
        // if ($job->position == 1) {
        //     $data['data'] = $this->Common->five_cond('hris_applications', 'dq', 1, 'app_year', $fy, 'district', $this->input->get('district'), 'jobID', $this->uri->segment(3), 'appStatus', 'Endorsed for Rating');
        // } else {
        //     $data['data'] = $this->Common->five_cond('hris_applications', 'dq', 1, 'app_year', $fy, 'district', $this->input->get('district'), 'jobID', $this->uri->segment(3), 'appStatus', 'Endorsed for Rating');
        // }
        // $data['job'] = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $this->uri->segment(3));

        $district = $this->input->get('district');
        $jobID = $this->uri->segment(3);

        $data['data'] = $this->Common->get_endorsed_applicant('Open',1,$district,$jobID);


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }



    public function for_endorsement()
    {

        $page = "validated";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $fy = date('Y');
        $dist = $this->Common->one_cond_row('district', 'id', $this->session->c_id);
        $data['district'] = $this->Common->one_cond_row('district', 'id', $this->session->c_id);
        $data['title'] = 'Validated Applicant';

        if ($this->session->position === 'Secretariat') {
            $jobTypes = $this->secretariat->user_job_types((int) ($this->session->id ?? $this->session->userdata('id')));
            if (empty($jobTypes)) {
                $this->session->set_flashdata('danger', 'No level assignment found for your account. Please contact a Super Admin.');
            }
            $data['assigned_job_types'] = $jobTypes;
            $data['data'] = empty($jobTypes) ? [] : $this->Common->get_applicant_staff_validated('Open','Endorsed for Rating', $jobTypes);
        } else {
            $data['data'] = $this->Common->get_applicant_staff_validated('Open','Endorsed for Rating');
        }

        $data['efr'] = $this->Common->two_cond_count_row('hris_applications', 'appStatus', 'Validated', 'app_year', $fy);


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function endorsed_applicants()
    {

        $page = "validated";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $fy = date('Y');
        $dist = $this->Common->one_cond_row('district', 'id', $this->session->c_id);
        $data['district'] = $this->Common->one_cond_row('district', 'id', $this->session->c_id);
        $data['title'] = 'Validated Applicant';
        if ($this->session->position === 'Secretariat') {
            $jobTypes = $this->secretariat->user_job_types((int) ($this->session->id ?? $this->session->userdata('id')));
            if (empty($jobTypes)) {
                $this->session->set_flashdata('danger', 'No level assignment found for your account. Please contact a Super Admin.');
            }
            $data['assigned_job_types'] = $jobTypes;
            $data['data'] = empty($jobTypes) ? [] : $this->Common->get_applicant_staff_validated('Open','Endorsed for Rating', $jobTypes);
        } else {
            $data['data'] = $this->Common->get_applicant_staff_validated('Open','Endorsed for Rating');
        }


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function endorsed_applicants_unassigned()
    {
        $page = "endorsed_no_rater";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $fy = date('Y');

        $jobTypes = [];
        $enforceJobTypes = false;
        if ($this->session->position === 'Secretariat') {
            $jobTypes = $this->secretariat->user_job_types((int) ($this->session->id ?? $this->session->userdata('id')));
            $enforceJobTypes = true;
            if (empty($jobTypes)) {
                $this->session->set_flashdata('danger', 'No level assignment found for your account. Please contact a Super Admin.');
            }
        }

        // Get endorsed applicants without any rater assignment for current FY
        $rows = [];
        if (!$enforceJobTypes || !empty($jobTypes)) {
            $builder = $this->db
                ->select('app.appID, app.applicant_id, app.jobID, app.pre_school, app.appStatus, app.district, app.app_year,
                          jv.jobTitle, jv.job_type, jv.jvStatus,
                          COALESCE(ha.record_no, ha2.record_no) AS rec_no,
                          COALESCE(ha.FirstName, ha2.FirstName) AS FirstName,
                          COALESCE(ha.LastName, ha2.LastName) AS LastName,
                          COALESCE(ha.MiddleName, ha2.MiddleName) AS MiddleName')
                ->from('hris_applications app')
                ->join('hris_jobvacancy jv', 'jv.jobID = app.jobID', 'left')
                ->join('hris_applicant ha', 'ha.id = app.applicant_id', 'left')
                ->join('hris_applicant ha2', 'ha2.record_no = app.applicant_id', 'left')
                ->join('hris_rater_assignments ra', 'ra.app_id = app.appID AND ra.fy = '.$this->db->escape($fy), 'left', false)
                ->where('app.appStatus', 'Endorsed for Rating')
                ->where('app.app_year', $fy)
                ->where('jv.jvStatus', 'Open')
                ->where('ra.id IS NULL', null, false)
                ->order_by('jv.jvStatus', 'asc')
                ->order_by('jv.job_type', 'asc')
                ->order_by('jv.jobTitle', 'asc')
                ->order_by('app.appID', 'asc');

            if (!empty($jobTypes)) {
                $builder->where_in('jv.job_type', $jobTypes);
            }

            $rows = $builder->get()->result();
        }

        $grouped = [];
        foreach ($rows as $r) {
            $jt = (int)($r->job_type ?? 0);
            $grouped[$jt][] = $r;
        }

        $data = [
            'title'   => 'Endorsed Applicants (No Evaluator Assigned)',
            'grouped' => $grouped,
        ];

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function rated_applicants()
    {

        $page = "validated";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $fy = date('Y');
        $dist = $this->Common->one_cond_row('district', 'id', $this->session->c_id);
        $data['district'] = $this->Common->one_cond_row('district', 'id', $this->session->c_id);
        $data['title'] = 'Validated Applicant';
        //$data['data'] = $this->Common->two_cond('hris_applications', 'appStatus', 'Rated', 'app_year', $fy);
        if ($this->session->position === 'Secretariat') {
            $jobTypes = $this->secretariat->user_job_types((int) ($this->session->id ?? $this->session->userdata('id')));
            $data['assigned_job_types'] = $jobTypes;
            $data['data'] = empty($jobTypes) ? [] : $this->Common->get_applicant_staff_validated('Open','Rated', $jobTypes);
        } else {
            $data['data'] = $this->Common->get_applicant_staff_validated('Open','Rated');
        }


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function confirmed_applicants()
    {

        $page = "validated";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $fy = date('Y');
        $dist = $this->Common->one_cond_row('district', 'id', $this->session->c_id);
        $data['district'] = $this->Common->one_cond_row('district', 'id', $this->session->c_id);
        $data['title'] = 'Validated Applicant';
        //$data['data'] = $this->Common->two_cond('hris_applications', 'appStatus', 'Confirmed', 'app_year', $fy);
        if ($this->session->position === 'Secretariat') {
            $jobTypes = $this->secretariat->user_job_types((int) ($this->session->id ?? $this->session->userdata('id')));
            $data['assigned_job_types'] = $jobTypes;
            $data['data'] = empty($jobTypes) ? [] : $this->Common->get_applicant_staff_validated('Open','Confirmed', $jobTypes);
        } else {
            $data['data'] = $this->Common->get_applicant_staff_validated('Open','Confirmed');
        }


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function query_applicants()
    {

        $page = "applicant_query";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        //$data['data'] = $this->Common->one_cond_group('hris_application_inquiry', 'stat', 0, 'application_id');
        //$data['data'] = $this->Common->two_join_two_cond('hris_application_inquiry','hris_jobvacancy', 'a.*, b.*', 'a.job_id=b.jobID', 'stat', 0, 'jvStatus', 'Open', 'b.jobTitle', 'ASC');
        $data['data'] = $this->Common->get_applications_with_rating_status();

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function dq_applicants()
    {

        $page = "validated";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $fy = date('Y');
        $dist = $this->Common->one_cond_row('district', 'id', $this->session->c_id);
        $data['district'] = $this->Common->one_cond_row('district', 'id', $this->session->c_id);
        $data['title'] = 'Validated Applicant';
        //$data['data'] = $this->Common->three_cond('hris_applications', 'appStatus', 'Validated', 'app_year', $fy, 'dq', 2);
        $data['data'] = $this->Common->get_applicant_staff_dq('Open',2);


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function pdf()
    {

        $page = "pdf";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $data['data'] = $this->Common->one_cond_row('hris_applicant', 'id', $this->uri->segment(3));

        $data['title'] = 'title';
        $this->load->view('pages/' . $page, $data);
    }

    public function pdf_staff()
    {

        $page = "pdf"; 

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $data['data'] = $this->Common->one_cond_row('hris_staff', 'IDNumber', $this->uri->segment(3));

        $data['title'] = 'title';
        $this->load->view('pages/' . $page, $data);
    }

    public function pdfv2()
    {

        $page = "pdf";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $data['data'] = $this->Common->one_cond_row('hris_applications', 'appID', $this->uri->segment(3));


        $data['title'] = 'title';
        $this->load->view('pages/' . $page, $data);
    }



    public function applicant_applications()
    {

        $page = "aa";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $d = $this->Common->one_cond_row('district', 'id', $this->session->c_id);

        if ($this->session->position === 'Human Resource Admin' || $this->session->position === 'HR Staff' || $this->session->position === 'Super Admin' || $this->session->position === 'asds') {
            $data['data'] = $this->Common->one_cond('hris_applications_rating', 'record_no', $this->input->post('record_no'));
        } else {
            $data['data'] = $this->Common->two_cond('hris_applications_rating', 'record_no', $this->input->post('record_no'), 'fy', $this->input->post('fy'));
        }


        $data['applicant'] = $this->Common->one_cond_row('hris_applicant', 'record_no', $this->input->post('record_no'));

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function applicant_app()
    {

        $page = "aa";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $rn = $this->uri->segment('3');


        $d = $this->Common->one_cond_row('district', 'id', $this->session->c_id);



        if ($this->session->position === 'Human Resource Admin' || $this->session->position === 'HR Staff' || $this->session->position === 'Super Admin' || $this->session->position === 'asds') {
            $data['data'] = $this->Common->one_cond('hris_applications_rating', 'record_no', $rn);
        } else {
            $data['data'] = $this->Common->two_cond('hris_applications_rating', 'record_no', $rn, 'fy', date('Y'));
        }

        $data['applicant'] = $this->Common->one_cond_row('hris_applicant', 'record_no', $rn);

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function forgot_password()
    {

        $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        ', '</div>');
        $this->form_validation->set_rules('email', 'Email', 'required');

        if ($this->form_validation->run() == FALSE) {

            $page = "fp";

            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }

            $this->load->view('pages/' . $page);
        } else {
            if ($this->input->post('at') == 1) {
                $email_check = $this->Common->one_cond_count_row('users', 'username', $this->input->post('email'));
            } elseif ($this->input->post('at') == 2) {
                $email_check = $this->Common->one_cond_count_row('hris_staff', 'empEmail', $this->input->post('email'));
            } else {
                $email_check = $this->Common->one_cond_count_row('schools', 'schoolEmail', $this->input->post('email'));
            }



            if ($email_check->num_rows() == 0) {
                $this->session->set_flashdata('failed', 'We could not find your email address.');
                redirect(base_url() . 'Pages/forgot_password');
            } else {
                $this->Reg->update_request_password();
                $this->session->set_flashdata('success', 'The new password has been sent to your email.');

                redirect(base_url() . 'log_in');
            }
        }
    }


    public function rqa_list()
    {
        $page = "rqa";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }
        $jobID = $this->input->get('jobID');

        $data['job'] = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $jobID);

        $job = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $jobID);
        if ($job->promotion == 1) {
            $this->Reg->calculate_rating_promotion($this->uri->segment(3));
        }else{
            if ($job->position == 1) {
                $this->Reg->calculate_rating($this->uri->segment(3));
            } else {
                $this->Reg->calculate_rating_none($this->uri->segment(3));
        }
        }

        $data['title'] = 'Registry of Qualified Applicants';

        // $data['data'] = $this->Common->promotion_list($jobID);

        // $data['job'] = $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $this->input->get('jobID'));
        // $data['data1'] = $this->PersonnelModel->rqa_group($jobID);
        // $data['data2'] = $this->PersonnelModel->rqa_group_shs($jobID);
        // $data['data3'] = $this->PersonnelModel->rqa_group_jhs($jobID);



        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function aq()
    {
        $this->Reg->update_aq();
        $this->session->set_flashdata('success', 'Query has been finalized and hidden from your list.');

        $referrer = $this->input->server('HTTP_REFERER', true);
        if (!empty($referrer)) {
            redirect($referrer);
            return;
        }

        redirect(base_url() . 'Pages/query_applicants');
    }

    public function dcp_report()
    {

        $page = "dcp_report";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $data['title'] = 'DCP Recipient Reports';
        $data['school'] = $this->Common->no_cond_order_by('schools', 'schoolName', 'ASC');


        $this->load->view('pages/' . $page, $data);
    }

    public function district_report()
    {

        $page = "district_emp_list";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $data['title'] = 'Employee Report';
        $data['district'] = $this->Common->one_cond_row('district', 'id', $this->session->c_id);
        $dist = $this->Common->one_cond_row('district', 'id', $this->session->c_id);
        $data['school'] = $this->Common->one_cond('schools', 'district', $dist->discription);


        $this->load->view('pages/' . $page, $data);
    }

    // public function employee_report()
    // {

    //     $page = "employee_report";

    //     if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
    //         show_404();
    //     }

    //     $data['title'] = 'PLANTILLA OF PERSONNEL AS OF' . strtoupper(date('F-Y'));

    //     $data['plantilla'] = $this->Common->no_cond_group('hris_plantilla', 'pGroup');

    //     // if (!$this->input->post('pgroup')) {
    //     //     $data['plantilla'] = $this->Common->no_cond_group('hris_plantilla', 'pGroup');
    //     // } else {
    //     //     //$data['plantilla'] = $this->Common->one_cond_group('hris_plantilla', 'pGroup', $this->input->post('pgroup'), 'pGroup');
    //     //     $data['plantilla'] = $this->Common->one_cond_group('hris_plantilla', 'pGroup', $this->input->post('pgroup'), 'pGroup');
    //     // }
        

    //     $this->load->view('pages/' . $page, $data);
    // }

    public function employee_report()
    {

        $page = "employee_report";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $data['title'] = 'PLANTILLA OF PERSONNEL AS OF' . strtoupper(date('F-Y'));
        
        $data['plan'] = $this->Common->no_cond_group('hris_plantilla', 'pGroup');
 
                $data['grouped'] = [];

                if (!$this->input->post('pgroup')) {
                    $results = $this->Common->get_grouped_staff();
                }else{
                    $results = $this->Common->get_grouped_staff_limit($this->input->post('pgroup'));
                    
                }

                // Group staff by pGroup
                foreach ($results as $row) {
                    $group = $row->pGroup ?? 'Undefined';
                    $data['grouped'][$group][] = $row;
                }

            

        $this->load->view('pages/' . $page, $data);
    }

    public function employee_report_it()
    {

        $page = "employee_report_it";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $data['title'] = 'PLANTILLA OF PERSONNEL AS OF' . strtoupper(date('F-Y'));
        
        $data['plan'] = $this->Common->no_cond_group('hris_plantilla', 'pGroup');
 
                $data['grouped'] = [];

                if (!$this->input->post('pgroup')) {
                    $results = $this->Common->get_grouped_staff();
                }else{
                    $results = $this->Common->get_grouped_staff_limit($this->input->post('pgroup'));
                    
                }

                // Group staff by pGroup
                foreach ($results as $row) {
                    $group = $row->pGroup ?? 'Undefined';
                    $data['grouped'][$group][] = $row;
                }

            

        $this->load->view('pages/' . $page, $data);
    }

    public function ah()
    {

        $page = "job_history";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $ee = $this->input->get('ee');

        if ($this->session->position == 'reg') {
            //$data['data'] = $this->Common->one_cond_loop_order_by('hris_applications', 'empEmail', $this->session->username,'appID','Desc');
            $data['data'] = $this->Common->one_cond('hris_applications', 'empEmail', $this->session->username);
        } elseif ($this->session->position == 'user') {
            $data['data'] = $this->Common->one_cond('hris_applications', 'empEmail', $this->session->username);
        } else {
            $data['data'] = $this->Common->one_cond('hris_applications', 'empEmail', $ee);
        }


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function rr_all()
    {
        $check = $this->Common->three_cond_count_row('hris_rating_request', 'job_id', $this->uri->segment(4),'app_id',$this->uri->segment(5),'applicant_id',$this->uri->segment(3));
        if($check->num_rows() >= 1){
            $this->session->set_flashdata('danger', 'This is a duplicate request.');
        }else{
            $this->Reg->rrall();
            $this->session->set_flashdata('success', 'Succesfully saved.');
            
        }
        redirect(base_url() . 'Pages/'.$this->uri->segment(7).'/'.$this->uri->segment(3).'/'.$this->uri->segment(4).'/'.$this->uri->segment(8));
    }

    public function request_rating()
    {

        $page = "rating_request";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $fy = date('Y');
        $data['title'] = 'Request Rating';
        //$data['data'] = $this->Common->two_cond('hris_rating_request','fy', $fy, 'stat',0);
        $data['data'] = $this->Hiring_model->get_granted_rating_request($fy,0);
        $data['granted'] = $this->Hiring_model->get_granted_rating_request($fy,1);


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function rated_applicantion()
    {

        $page = "rated_applicant";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $fy = date('Y');
        $data['title'] = 'Rated Applicant';
        $data['data'] = $this->Hiring_model->rated_applicant();


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function rated_applicants_missing_scores()
    {

        $page = "rated_applicant";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $data['title'] = 'Rated Applicants With Missing Scores';
        $data['data'] = $this->Hiring_model->rated_applicants_with_missing_scores();


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function jshs_applicantion()
    {

        $page = "jshs_application";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $fy = date('Y');
        $data['title'] = 'Rated Applicant';
        $data['data'] = $this->Hiring_model->jshs_applicant($this->uri->segment(3));


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function request_rating_granted()
        {
    $this->form_validation->set_error_delimiters(
        '<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>',
        '</div>'
    );

    

    

    $this->form_validation->set_rules('application', 'application', 'required');

    if ($this->form_validation->run() == FALSE) {

        $page = "rating_request_granted";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $jobID = $this->uri->segment(4);

        $data['title'] = 'Request Rating';
        $data['data']  = $this->Common->one_cond('hris_rating_request', 'id', $this->uri->segment(3));
        $data['job']   = !empty($jobID)
            ? $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $jobID)
            : null;

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');

    } else {

        $sourceAppId = trim($this->input->post('application'));
        $targetAppId = trim($this->input->post('app_id'));
        $jobID       = trim($this->input->post('jobID'));
        $recordNo    = trim($this->input->post('record_no'));
        $rType       = trim($this->input->post('r_type'));

        if ($rType == 1) {
            $this->Reg->copy_rating($recordNo, $sourceAppId);
            $this->Reg->application_change_stat('Rated');
        } else {
            $this->Reg->copy_limited_rating($recordNo, $sourceAppId);
            $this->Reg->application_change_stat('Endorsed for Rating');
        }

        $this->Reg->copy_dq($sourceAppId, $targetAppId, $jobID);

        $this->Reg->update_request_stat();
        $this->session->set_flashdata('success', 'Saved successfully');
        redirect(base_url() . 'Pages/request_rating');
    }
}

public function request_rating_granted_none()
        {
    $this->form_validation->set_error_delimiters(
        '<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>',
        '</div>'
    );

    $this->form_validation->set_rules('application', 'application', 'required');

    if ($this->form_validation->run() == FALSE) {

        $page = "rating_request_granted_none";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $jobID = $this->uri->segment(4);

        $data['title'] = 'Request Rating';
        $data['data']  = $this->Common->one_cond('hris_rating_request', 'id', $this->uri->segment(3));
        $data['job']   = !empty($jobID)
            ? $this->Common->one_cond_row('hris_jobvacancy', 'jobID', $jobID)
            : null;

        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');

    } else {

        $sourceAppId = trim($this->input->post('application'));
        $targetAppId = trim($this->input->post('app_id'));
        $jobID       = trim($this->input->post('jobID'));
        $recordNo    = trim($this->input->post('record_no'));
        $rType       = trim($this->input->post('r_type'));

        
        $this->Hiring_model->copy_rating($recordNo, $sourceAppId);
        $this->Reg->application_change_stat('Rated');
        

        $this->Reg->copy_dq($sourceAppId, $targetAppId, $jobID);

        $this->Reg->update_request_stat();
        $this->session->set_flashdata('success', 'Saved successfully');
        redirect(base_url() . 'Pages/request_rating');
    }
}


    public function request_rating_applicant()
    {

        $page = "rating_request_applicant";

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $fy = date('Y');
        $data['title'] = 'Request Rating';
        $data['data'] = $this->Common->two_cond('hris_rating_request','fy', $fy,'applicant_id',$this->session->c_id);


        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }

    public function rr_delete()
    {
        $this->Page_model->delete('3', 'id', 'hris_rating_request');
        $this->Page_model->insert_at('Delete application', $this->db->insert_id());
        $this->session->set_flashdata('danger', 'Request was deleted');
        if($this->session->position == 'reg'){
            redirect(base_url() . 'Pages/request_rating_applicant');
        }else{
            redirect(base_url() . 'Pages/request_rating');
        }
    }

        public function validated_by_job_id() {
            $this->Reg->insert_rate_by_job();
            $this->session->set_flashdata('success', 'Successfully Validated');
            redirect(base_url() . 'Page/jobVacancy');
        }

        public function special_change_stat() {
            if($this->uri->segment(3) == 1){
                $this->Reg->special_change_stat('Rated');
            }else{
                $this->Reg->special_change_stat('Endorsed for Rating');
            }
            $this->session->set_flashdata('success', 'Saved successfully');
            redirect(base_url() . 'Pages/request_rating');
        }

        public function resetempno() {
            $this->Reg->resetemployeeno();
            $this->session->set_flashdata('success', 'Saved successfully');
            redirect(base_url() . 'personnel');
        }




    public function get_provinces()
    {
        $provinces = $this->SettingsModel->get_provinces();
        echo json_encode($provinces);
    }

    public function get_cities()
    {
        $province = $this->input->post('province');  // Ensure province is being received
        $cities = $this->SettingsModel->get_cities($province);
        echo json_encode($cities);
    }

    public function get_barangays()
    {
        $city = $this->input->post('city');  // Ensure city is being received
        $barangays = $this->SettingsModel->get_barangays($city);
        echo json_encode($barangays);
    }

    function regApplicantshire()
	{
		$result['data'] = $this->Reg->join_applicant_and_staff();
		$this->load->view('list_reg_applicants_hired', $result);
	}

    public function submit_application_non_teaching()
    {

        $applications = $this->Common->two_cond_count_row('hris_applications', 'jobID', $this->uri->segment(3), 'applicant_id', $this->session->c_id);

        if ($applications->num_rows() >= 1) {
            $this->session->set_flashdata('danger', 'Please double-check the same job position you applied.');
            redirect(base_url() . 'pages/ja/' . $this->session->c_id);
        } else {
            $this->Reg->ap_submit_non_teaching();
        }

        $this->Reg->unlock_hris_staff();

        $app_id = $this->db->insert_id();

        $this->Page_model->insert_at('Submit Application', $app_id);
        if ($this->session->position == 'user') {
            $this->Reg->ap_track_apply_user_none_teaching('Application Submitted', $app_id,$this->uri->segment(3));
        } else {
            $this->Reg->ap_track_apply_non_teaching('Application Submitted', $app_id);
        }

        $this->session->set_flashdata('success', 'Successfully Applied');
        redirect(base_url() . 'pages/ja/' . $this->session->c_id);
    }

    public function davor_confession()
    {


        $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        ', '</div>');
        $this->form_validation->set_rules('con', 'Confession', 'required');
       
        if ($this->form_validation->run() == FALSE) {

            $page = "confessions";

            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }
            $data['mis_settings'] = $this->Common->one_cond_row('mis_settings','settingsID',1);
            $data['title'] = "Register";
            $data['school'] = $this->Common->no_cond('schools');

            $this->load->view('templates/header_public');
            $this->load->view('pages/' . $page, $data);
            $this->load->view('templates/footer_public');
        } else {

            $mis_settings = $this->Common->one_cond_row('mis_settings','settingsID',1);

            $recaptcha = $this->input->post('g-recaptcha-response');
            $secret = trim($mis_settings->secret_key);

            $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret}&response={$recaptcha}");
            $responseKeys = json_decode($response, true);

            if (!$responseKeys["success"]) {
                $this->session->set_flashdata('danger', 'reCAPTCHA verification failed. Please try again.');
                redirect('davor_confession'); 
            }


            $renren = $this->input->post('renren');
            $ivykate = $this->input->post('ivykate');
            $ivankyle = $this->input->post('ivankyle');
            $ic = $this->input->post('ic');

            if (!empty($renren) || !empty($ivykate) || !empty($ivankyle) || !empty($ic)) {
                $this->session->set_flashdata('danger', 'I Got you');
                redirect(base_url() . 'davor_confession');
            }

            $this->Reg->new_confession();

            $this->session->set_flashdata('success', 'Your confession has been processed successfully.');
            redirect(base_url() . 'log_in');
                
            
        }
    }

    public function all_non_teaching_applicant()
    {

        $page = "ha_all_by_jp";
        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $data['title'] = "Initial Evaluation Result (IER)";
        $data['mis_settings'] = $this->SettingsModel->mis_settings();
        $data['data'] = $this->Common->get_submitted_applicant($this->uri->segment(3));

        

        $this->load->view('pages/' . $page, $data);
    }

    public function all_non_teaching_applicantv2()
    {

        $page = "ha_all_by_jp_v2";
        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $data['title'] = "Initial Evaluation Result (IER)";
        $data['mis_settings'] = $this->SettingsModel->mis_settings();
        $data['data'] = $this->Common->get_submitted_applicant($this->uri->segment(3));

        

        $this->load->view('pages/' . $page, $data);

    }

    public function all_non_teaching_applicantv3()
    {

        $page = "ha_all_by_jp_v2";
        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $data['title'] = "Initial Evaluation Result (IER)";
        $data['mis_settings'] = $this->SettingsModel->mis_settings();
        $data['data'] = $this->Hiring_model->get_submitted_applicant($this->uri->segment(3));

        

        $this->load->view('pages/' . $page, $data);

    }

    public function all_non_teaching_applicantv4()
    {

        $page = "ha_all_by_jp_v4";
        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
        }

        $data['title'] = "Initial Evaluation Result (IER)";
        $data['mis_settings'] = $this->SettingsModel->mis_settings();
        $data['data'] = $this->Hiring_model->get_submitted_applicant($this->uri->segment(3));

        

        $this->load->view('pages/' . $page, $data);

    }

public function ier_group_mun()
{
    ob_start(); // Stream output to avoid timeout

    $page = "ha_all_by_jp_mun";
    if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
        show_404();
    }

    $jobID = $this->uri->segment(3);
    //$mun = $this->input->get('mun');
    $data['mis_settings'] = $this->SettingsModel->mis_settings();

    $data['job'] = $this->Page_model->get_single_row_by_id('hris_jobvacancy', 'jobID', $jobID);
    $data['settings'] = $this->SettingsModel->get_mis_settings();

    $data['grouped_applicants'] = $this->Reg->get_grouped_applicants_by_mun_ier($jobID);

    $job = $this->Common->one_cond_row_select('hris_jobvacancy','jobID,sy,sign', 'jobID', $jobID);
    $data['sign'] = $this->get_rqa_sign($job);

    // Preload applicants and staff
    $data['applicants'] = $this->Page_model->get_all_applicants_indexed();
    $data['staff'] = $this->Page_model->get_all_staff_indexed();

    $data['title'] = "Initial Evaluation Result (IER)";

    $this->load->view('pages/' . $page, $data);

    ob_end_flush();
}

public function las()
{
    ob_start(); // Stream output to avoid timeout

    $page = "ha_all_las";
    if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
        show_404();
    }

    $jobID = $this->uri->segment(3);
    //$mun = $this->input->get('mun');
    $data['mis_settings'] = $this->SettingsModel->mis_settings();

    $data['job'] = $this->Page_model->get_single_row_by_id('hris_jobvacancy', 'jobID', $jobID);
    $data['settings'] = $this->SettingsModel->get_mis_settings();

    $data['grouped_applicants'] = $this->Hiring_model->get_grouped_applicants_by_mun_ierv2($jobID);

    $job = $this->Common->one_cond_row_select('hris_jobvacancy','jobID,sy,sign', 'jobID', $jobID);
    $data['sign'] = $this->get_rqa_sign($job);

    // Preload applicants and staff
    $data['applicants'] = $this->Page_model->get_all_applicants_indexed();
    $data['staff'] = $this->Page_model->get_all_staff_indexed();

    $data['title'] = "Initial Evaluation Result (IER)";

    $this->load->view('pages/' . $page, $data);

    ob_end_flush();
}

public function ier_group_munv2()
{
    ob_start(); // Stream output to avoid timeout

    $page = "ha_all_by_jp_munv2";
    if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
        show_404();
    }

    $jobID = $this->uri->segment(3);
    //$mun = $this->input->get('mun');
    $data['mis_settings'] = $this->SettingsModel->mis_settings();

    $data['job'] = $this->Page_model->get_single_row_by_id('hris_jobvacancy', 'jobID', $jobID);
    $data['settings'] = $this->SettingsModel->get_mis_settings();

    $data['grouped_applicants'] = $this->Reg->get_grouped_applicants_by_mun_ierv2($jobID);

    $job = $this->Common->one_cond_row_select('hris_jobvacancy','jobID,sy,sign', 'jobID', $jobID);
    $data['sign'] = $this->get_rqa_sign($job);

    // Preload applicants and staff
    $data['applicants'] = $this->Page_model->get_all_applicants_indexed();
    $data['staff'] = $this->Page_model->get_all_staff_indexed();

    $data['title'] = "Initial Evaluation Result (IER)";

    $this->load->view('pages/' . $page, $data);

    ob_end_flush();
}

    public function change_fy() {
        $new_fy = $this->input->post('new_fy');
        if (!empty($new_fy)) {
            $this->session->set_userdata('cur_fy', $new_fy);
        }
        redirect($_SERVER['HTTP_REFERER']); 
    }

    public function change_sy() {
        $new_fy = $this->input->post('new_fy');
        if (!empty($new_fy)) {
            $this->session->set_userdata('cur_sy', $new_fy);
        }
        redirect($_SERVER['HTTP_REFERER']); 
    }

    public function copy_application() {
            $id = $this->input->post('id');
            $jobid = $this->input->post('cur_id');
                
            if (!$id) {
                show_error('No application ID provided.');
                return;
            }

            $row = $this->db->get_where('hris_applications', ['appID' => $id])->row_array();

            if ($row) {
                unset($row['appID']);

                $row['dateSubmitted'] = date('Y-m-d H:i:s'); 
                $row['jobID'] = $jobid;  

                $this->db->insert('hris_applications', $row);
                $newAppId = $this->db->insert_id();
            }

            $rate = $this->db->get_where('hris_rating_none', ['appID' => $id])->row_array();

            if ($rate) {
                unset($rate['id']);

                //$rate['dateSubmitted'] = date('Y-m-d H:i:s'); 
                $rate['appID'] = $newAppId;  

                $this->db->insert('hris_rating_none', $rate);
            }

        redirect($_SERVER['HTTP_REFERER']);
    }

    public function copy_application_promotion() {
            $id = $this->input->post('id');
            $jobid = $this->input->post('cur_id');
            $applicant = $this->input->post('applicant_id');

            if ($jobid === 0 || $applicant === '') {
                $this->session->set_flashdata('danger', 'Missing applicant or job ID.');
                redirect($this->agent->referrer() ?: base_url());
                return; 
            }

            $exists = $this->db->where('empEmail', $applicant)
                            ->where('jobID',   $jobid)
                            ->limit(1)
                            ->count_all_results('hris_applications');

            if ($exists > 0) {
                $this->session->set_flashdata('danger', 'Application already exists for this job.');
                redirect($this->agent->referrer() ?: base_url());
                return; 
            }

                
            if (!$id) {
                show_error('No application ID provided.');
                return;
            }

            $row = $this->db->get_where('hris_applications', ['appID' => $id])->row_array();

            if ($row) {
                unset($row['appID']);

                $row['dateSubmitted'] = date('Y-m-d H:i:s'); 
                $row['jobID'] = $jobid;  

                $this->db->insert('hris_applications', $row);
                $newAppId = $this->db->insert_id();
            }

            $rate = $this->db->get_where('hris_rating_promotion', ['appID' => $id])->row_array();

            if ($rate) {
                unset($rate['id']);

                //$rate['dateSubmitted'] = date('Y-m-d H:i:s'); 
                $rate['appID'] = $newAppId;  

                $this->db->insert('hris_rating_promotion', $rate);
            }
        $this->session->set_flashdata('success', 'Copied successfully.');
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function application_delete()
    {
        $this->Common->delete('hris_applications', 'appID','3');
        $this->Common->delete('hris_rating_promotion', 'appID','3');
        //$this->session->set_flashdata('danger', 'Successfully deleted.');
        redirect(base_url() . 'Pages/qaulified_promotion');
    }

    public function application_delete_regular()
    {
        $this->Common->delete('hris_applications', 'appID','3');
        $this->Common->delete('hris_rating_none', 'appID','3');
        //$this->session->set_flashdata('danger', 'Successfully deleted.');
        redirect(base_url() . 'Pages/hiring_non_qaulified_rated');
    }

    function sbm_checklist_final()
    {
        $this->Page_model->sbm_cecklist_lock_unloc(1);
        $this->session->set_flashdata('success', 'Saved successfully.');
        redirect(base_url() . 'Page/sbm_checklist');
    }

    public function ier_insert_info()
    {

        $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        ', '</div>');
        $this->form_validation->set_rules('disability', 'Disability', 'required');
        //$this->form_validation->set_rules('termPeriodto', 'Term Period To', 'required');

        if ($this->form_validation->run() == FALSE) {

            $page = "eir";

            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }

            $data['data'] = $this->Common->find_by_idnumber_union($this->uri->segment(5));

            $data['title'] = "Initial Evaluation Result(IER) Info";

            $this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page, $data);
            $this->load->view('templates/footer');
        } else {
                $this->Page_model->insert_ier();
                $this->Page_model->update_ier_person($this->input->post('table'));
                $this->session->set_flashdata('success', ' New User Save');
                redirect(base_url() . 'Pages/all_non_teaching_applicant/'.$this->input->post('jobID'));

        }

    }

    public function ier_update_info()
    {

        $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        ', '</div>');
        $this->form_validation->set_rules('disability', 'Disability', 'required');
        //$this->form_validation->set_rules('termPeriodto', 'Term Period To', 'required');

        if ($this->form_validation->run() == FALSE) {

            $page = "eir_update";

            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }

            $data['data'] = $this->Common->find_by_idnumber_union($this->uri->segment(5));
            $data['ier'] = $this->Common->two_cond_row('ier_info','jobID',$this->uri->segment(3),'code',$this->uri->segment(5));

            $data['title'] = "Initial Evaluation Result(IER) Info";

            $this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page, $data);
            $this->load->view('templates/footer');
        } else {
                $this->Page_model->update_ier();
                $this->Page_model->update_ier_person($this->input->post('table'));
                $this->session->set_flashdata('success', ' New User Save');
                redirect(base_url() . 'Pages/all_non_teaching_applicant/'.$this->input->post('jobID'));

        }

    }

    public function validators_remarks(){
        $this->Page_model->validators_remarks();
        $this->session->set_flashdata('success', 'Successfully Saved.');
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function rftp()
    {

        $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        ', '</div>');
        $this->form_validation->set_rules('id', 'ID Number', 'required');
        //$this->form_validation->set_rules('termPeriodto', 'Term Period To', 'required');

        if ($this->form_validation->run() == FALSE) {

            $data['check'] = $this->Common->two_cond_row('hris_rftp','IDNumber',$this->session->username,'fy',$this->session->cur_fy);

            if($data['check']){
             $page = "rftp_update";
             $data['res'] = $this->Reg->count_ones_twos($this->session->username, $this->session->cur_fy);
             $data['coi'] = $this->Reg->count_coi($this->session->username, $this->session->cur_fy);
             $data['ncoi'] = $this->Reg->count_ncoi($this->session->username, $this->session->cur_fy);
            }else{
                $page = "rftp";
            }

            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }
            $data['rftp'] = $this->Common->no_cond('hris_rftp_domain');

            $data['data'] = $this->Common->find_by_idnumber_union($this->uri->segment(5));
            $data['ier'] = $this->Common->two_cond_row('ier_info','jobID',$this->uri->segment(3),'code',$this->uri->segment(5));

            $data['title'] = "RECLASIFICATION FORM FOR TEACHING POSITIONS (RFTP)";

            $this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page, $data);
            $this->load->view('templates/footer');
        } else {
                $this->Reg->insert_rftp();
                $this->session->set_flashdata('success', 'Successfully Saved.');
                redirect($_SERVER['HTTP_REFERER']);

        }

    }

    public function rftp_admin()
    {

        $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        ', '</div>');
        $this->form_validation->set_rules('id', 'ID Number', 'required');
        //$this->form_validation->set_rules('termPeriodto', 'Term Period To', 'required');

        if ($this->form_validation->run() == FALSE) {

            $data['check'] = $this->Common->two_cond_row('hris_rftp','IDNumber',$this->uri->segment(3),'fy',$this->session->cur_fy);

            if($data['check']){
             $page = "rftp_update";
             $data['res'] = $this->Reg->count_ones_twos($this->uri->segment(3), $this->session->cur_fy);
             $data['coi'] = $this->Reg->count_coi($this->uri->segment(3), $this->session->cur_fy);
             $data['ncoi'] = $this->Reg->count_ncoi($this->uri->segment(3), $this->session->cur_fy);
            }else{
                $page = "rftp";
            }

            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }
            $data['rftp'] = $this->Common->no_cond('hris_rftp_domain');

            $data['data'] = $this->Common->find_by_idnumber_union($this->uri->segment(5));
            $data['ier'] = $this->Common->two_cond_row('ier_info','jobID',$this->uri->segment(3),'code',$this->uri->segment(5));

            $data['title'] = "RECLASIFICATION FORM FOR TEACHING POSITIONS (RFTP)";

            $this->load->view('templates/head');
            $this->load->view('templates/header');
            $this->load->view('pages/' . $page, $data);
            $this->load->view('templates/footer');
        } else {
                $this->Reg->insert_rftp();
                $this->session->set_flashdata('success', 'Successfully Saved.');
                redirect($_SERVER['HTTP_REFERER']);

        }

    }

    public function rftp_update(){
        $this->Reg->update_rftp();
        $this->session->set_flashdata('success', 'Successfully Updated.');
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function rftp_print()
    {
            $page = "rftp_print";

            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }
            $data['rftp'] = $this->Common->no_cond('hris_rftp_domain');
            $data['id'] = $this->session->username;
            $data['fy'] = $this->session->cur_fy;

            $data['title'] = "RECLASIFICATION FORM FOR TEACHING POSITIONS (RFTP)";

            $this->load->view('pages/' . $page, $data);

    }

    public function rftp_print_admin()
    {
            $page = "rftp_print";

            if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
                show_404();
            }
            $data['rftp'] = $this->Common->no_cond('hris_rftp_domain');
            $data['id'] = $this->uri->segment(3);
            $data['fy'] = $this->session->cur_fy;

            $data['title'] = "RECLASIFICATION FORM FOR TEACHING POSITIONS (RFTP)";

            $this->load->view('pages/' . $page, $data);

    }

    public function shs_update(){
        $item = $this->input->post('item');
        if($item == 'ma'){
            $this->Hiring_model->shs_edit('hris_applicant','id');
        }else{
            $this->Hiring_model->shs_edit('hris_staff','IDNumber');   
        }
        $this->session->set_flashdata('success', 'Successfully Updated.');
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function jhs_update(){
        $item = $this->input->post('item');
        if($item == 'ma'){
            $this->Hiring_model->jhs_edit('hris_applicant','id');
        }else{
            $this->Hiring_model->jhs_edit('hris_staff','IDNumber');   
        }
        $this->session->set_flashdata('success', 'Successfully Updated.');
        redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * Insert Default Rating for an Application
     * Called via AJAX POST request
     */
    public function insert_default_rating()
    {
        // Get parameters from POST
        $record_no = $this->input->post('record_no');
        $appID = $this->input->post('appID');
        $jobID = $this->input->post('jobID');
        $fy = $this->input->post('fy');
        $job_type = $this->input->post('job_type');

        // Validate required parameters
        if (empty($record_no) || empty($appID) || empty($jobID) || empty($fy)) {
            $response = array(
                'status' => 'error',
                'message' => 'Missing required parameters'
            );
            echo json_encode($response);
            return;
        }

        // Check if rating already exists for THIS specific appID
        // appID is the unique identifier for each application
        $existing = $this->Common->one_cond_row('hris_applications_rating', 'appID', $appID);
        if (!empty($existing)) {
            $response = array(
                'status' => 'warning',
                'message' => 'Default rating already exists for this application'
            );
            echo json_encode($response);
            return;
        }

        // Prepare default rating data
        $ratingData = array(
            'record_no' => $record_no,
            'appID' => $appID,
            'education' => 0.00001,
            'training' => 0.00001,
            'experience' => 0.00001,
            'let_rating' => 0.00001,
            'demo_rating' => 0.00001,
            'tr_rating' => 0.00001,
            'job_type' => $job_type,
            'fy' => $fy
        );

        // Insert into database
        $insert_result = $this->db->insert('hris_applications_rating', $ratingData);

        if ($insert_result) {
            $response = array(
                'status' => 'success',
                'message' => 'Default rating inserted successfully'
            );
        } else {
            $error = $this->db->error();
            $response = array(
                'status' => 'error',
                'message' => 'Failed to insert default rating: ' . $error['message']
            );
        }

        echo json_encode($response);
    }

    public function hire_applicant(){
        $item = $this->input->post('item');

        if($item == 'ma'){
            $password = 'davor112';
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $last = $this->Common->one_cond_row('count', 'id', 3);
            $number = $last->number;
            $lastThreeDigits = substr($number, -3);
            $lastThreeDigits = (int)$lastThreeDigits + 1;

            if ($lastThreeDigits > 999) {
                $lastThreeDigits = 000;
            }

            $lastThreeDigits = str_pad($lastThreeDigits, 3, "0", STR_PAD_LEFT);

            $newIDNumber = date('Y') . '' . date('m') . '' . $lastThreeDigits;

            date_default_timezone_set('Asia/Manila'); # add your city to set local time zone
            $dateHired = date("Y-m-d");

            $id = $this->input->post('IDNumber');
            $username = $this->input->get('empEmail');

            $this->db->query("insert into hris_staff (IDNumber, FirstName, MiddleName, LastName, NameExtn, prefix, MaritalStatus, BirthDate, Sex, height, weight, bloodType, gsis, pagibig, philHealth, sssNo, tinNo, resHouseNo, resStreet, resVillage, resCity, resProvince, perStreet, perVillage, perBarangay, perCity, perProvince, empTelNo, empMobile, empEmail, age, citizenship, currentStatus, dateHired, citizenshipCountry) select '" . $newIDNumber . "', FirstName, MiddleName, LastName, NameExtn, prefix, MaritalStatus, BirthDate, Sex, height, weight, bloodType, gsis, pagibig, philHealth, sssNo, tinNo, resHouseNo, resStreet, resVillage, resCity, resProvince, perStreet, perVillage, perBarangay, perCity, perProvince, empTelNo, empMobile, empEmail, age, citizenship, 'Active','" . $dateHired . "','Philippines' from hris_applicant where record_no='" . $id . "'");
            $this->db->query("INSERT INTO users(username, password, position, fname, mname, lname, sex, user_id)SELECT '" . $newIDNumber . "', '$hash', 'user', FirstName, MiddleName, LastName, Sex, '" . $newIDNumber . "' FROM hris_applicant where record_no='" . $id . "'");
            $this->Page_model->count_update(3, '1' . $lastThreeDigits);
        }else{
            $newIDNumber = $this->input->post('IDNumber');
        }
            
        $this->Hiring_model->hris_hire_insert($newIDNumber);
        $this->session->set_flashdata('success', 'Successfully Saved.');
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function form_33b(){

        $data['title'] = 'ren';

        $data['data'] = $this->Hiring_model->get_single_joined_data($this->uri->segment(3));

		$this->load->view('pages/csform_33b', $data);
	}

    public function assumption_order(){

        $data['title'] = 'ren';

        $data['data'] = $this->Hiring_model->get_single_joined_data($this->uri->segment(3));

		$this->load->view('pages/csform_assumption', $data);
	}

    public function assignment_order(){

        $data['title'] = 'ren';

        $data['data'] = $this->Hiring_model->get_single_joined_data($this->uri->segment(3));

		$this->load->view('pages/csform_assignment_order', $data);
	}

    




}
