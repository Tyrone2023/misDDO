<?php
$appointmentTemplateManagers = ['sds', 'asst_sds', 'HRMO', 'Human Resource Admin', 'asds', 'Secretariat'];
$canManageAppointmentTemplates = in_array((string) $this->session->position, $appointmentTemplateManagers, true);
?>
<li>
    <a href="javascript: void(0);" class="waves-effect">
        <i class="mdi mdi-file-document-multiple-outline"></i>
        <span>Appointment Documents</span>
        <span class="menu-arrow"></span>
    </a>
    <ul class="nav-second-level" aria-expanded="false">
        <li><a href="<?= base_url(); ?>Pages/appointment_reports">Applicant Reports</a></li>
        <?php if ($canManageAppointmentTemplates) : ?>
            <li><a href="<?= base_url(); ?>Pages/appointment_template_setup">Template Setup</a></li>
        <?php endif; ?>
    </ul>
</li>
