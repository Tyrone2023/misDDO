<?php
// Merge these methods into your Leave_template_model.php

private function insertSignatureIfSigned($sheet, $signatory, $imageCell, $nameCell, $positionCell)
{
    if (empty($signatory)) {
        return;
    }

    $sheet->setCellValue($nameCell, isset($signatory['full_name']) ? $signatory['full_name'] : '');
    $sheet->setCellValue($positionCell, isset($signatory['position_title']) ? $signatory['position_title'] : '');

    if (empty($signatory['signed_at']) || empty($signatory['signature_path'])) {
        return;
    }

    $absolute = FCPATH . ltrim($signatory['signature_path'], '/');
    if (!file_exists($absolute)) {
        return;
    }

    $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
    $drawing->setName('Signature');
    $drawing->setDescription('Leave Signatory Signature');
    $drawing->setPath($absolute);
    $drawing->setCoordinates($imageCell);
    $drawing->setOffsetX(10);
    $drawing->setOffsetY(5);
    $drawing->setHeight(36);
    $drawing->setWorksheet($sheet);
}

private function applyPersonnelSheetSignatories($sheet, $leave_application_id)
{
    $this->load->model('Leave_signatory_model');
    $signatories = $this->Leave_signatory_model->get_export_signatory_map($leave_application_id);

    // Adjust these cells based on your final workbook mapping.
    $this->insertSignatureIfSigned($sheet, isset($signatories['APPLICANT']) ? $signatories['APPLICANT'] : null, 'B37', 'B38', 'B39');
    $this->insertSignatureIfSigned($sheet, isset($signatories['RECOMMENDING_AUTHORITY']) ? $signatories['RECOMMENDING_AUTHORITY'] : null, 'B55', 'B56', 'B57');
    $this->insertSignatureIfSigned($sheet, isset($signatories['APPROVING_AUTHORITY']) ? $signatories['APPROVING_AUTHORITY'] : null, 'F55', 'F56', 'F57');
    $this->insertSignatureIfSigned($sheet, isset($signatories['HR_CERTIFICATION']) ? $signatories['HR_CERTIFICATION'] : null, 'B48', 'B49', 'B50');
}
