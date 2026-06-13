<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class Leave_template_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('Leave_signatory_model');
    }

    private function insert_signature($sheet, $cell, $path)
    {
        if (empty($path)) {
            return;
        }

        $file = FCPATH . 'uploads/signatures/' . $path;

        if (!file_exists($file)) {
            return;
        }

        $drawing = new Drawing();
        $drawing->setPath($file);
        $drawing->setHeight(45);
        $drawing->setCoordinates($cell);
        $drawing->setWorksheet($sheet);
    }

    public function apply_signatories_to_personnel_sheet($sheet, $leave_id)
    {
        $signatories = $this->Leave_signatory_model->get_signatories($leave_id);

        foreach ($signatories as $s) {
            if ($s['action_status'] !== 'SIGNED') {
                continue;
            }

            switch (strtoupper($s['signatory_role'])) {
                case 'CERTIFY':
                    $this->insert_signature($sheet, 'D48', $s['signature_path']);
                    $sheet->setCellValue('D50', $s['full_name']);
                    $sheet->setCellValue('D51', $s['position_title']);
                    break;

                case 'RECOMMEND':
                    $this->insert_signature($sheet, 'D55', $s['signature_path']);
                    $sheet->setCellValue('D57', $s['full_name']);
                    $sheet->setCellValue('D58', $s['position_title']);
                    break;

                case 'APPROVE':
                    $this->insert_signature($sheet, 'D62', $s['signature_path']);
                    $sheet->setCellValue('D64', $s['full_name']);
                    $sheet->setCellValue('D65', $s['position_title']);
                    break;
            }
        }
    }
}