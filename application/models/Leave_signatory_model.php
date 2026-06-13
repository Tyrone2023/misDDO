<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Leave_signatory_model extends CI_Model
{
    private $fixed_signatory_ids = array(
        'HR_CERTIFICATION'       => '7312910',
        'RECOMMENDING_AUTHORITY' => '7312300',
        'APPROVING_AUTHORITY'    => '8301540',
        'SDS'                    => '9130437'
    );

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_staff($staff_idnumber)
    {
        return $this->db->where('IDNumber', $staff_idnumber)->get('hris_staff')->row_array();
    }

    public function get_staff_by_name($name)
    {
        $name = trim((string)$name);
        if ($name === '') {
            return null;
        }

        return $this->db
            ->group_start()
                ->where("CONCAT(FirstName, ' ', LastName) =", $name)
                ->or_where("CONCAT(LastName, ', ', FirstName) =", $name)
            ->group_end()
            ->get('hris_staff')
            ->row_array();
    }

    private function format_full_name($staff)
    {
        $parts = array();
        if (!empty($staff['FirstName'])) $parts[] = $staff['FirstName'];
        if (!empty($staff['MiddleName'])) $parts[] = $staff['MiddleName'];
        if (!empty($staff['LastName'])) $parts[] = $staff['LastName'];
        return trim(implode(' ', $parts));
    }

    private function get_exact_position($position, $department = null)
    {
        $this->db->from('hris_staff');
        $this->db->where('empPosition', $position);
        if (!empty($department)) {
            $this->db->where('Department', $department);
        }
        return $this->db->get()->row_array();
    }

    private function get_like_position($keyword, $department = null, $exclude_keyword = null)
    {
        $this->db->from('hris_staff');
        $this->db->like('empPosition', $keyword);
        if (!empty($department)) {
            $this->db->like('Department', $department);
        }
        if (!empty($exclude_keyword)) {
            $this->db->not_like('empPosition', $exclude_keyword);
        }
        return $this->db->get()->row_array();
    }

    private function get_fixed_staff($role)
    {
        if (empty($this->fixed_signatory_ids[$role])) {
            return null;
        }
        return $this->get_staff($this->fixed_signatory_ids[$role]);
    }

    private function resolve_direct_head($staff)
    {
        if (empty($staff['directHead'])) {
            return null;
        }
        $head = $this->get_staff($staff['directHead']);
        if ($head) {
            return $head;
        }
        return $this->get_staff_by_name($staff['directHead']);
    }

    private function resolve_cid_or_sgod_chief($department)
    {
        if (stripos($department, 'Curriculum') !== false) {
            return $this->get_like_position('Chief', 'Curriculum');
        }
        if (stripos($department, 'School Governance') !== false) {
            return $this->get_like_position('Chief', 'School Governance');
        }
        return $this->get_like_position('Chief');
    }

    public function resolve_signatories($staff, $working_days)
    {
        $department  = isset($staff['Department']) ? trim((string)$staff['Department']) : '';
        $empPosition = isset($staff['empPosition']) ? trim((string)$staff['empPosition']) : '';

        $isSchoolBased = !empty($staff['schoolID']);
        $isSchoolHead = (
            stripos($empPosition, 'Principal') !== false ||
            stripos($empPosition, 'School Head') !== false ||
            stripos($empPosition, 'Head Teacher') !== false
        );

        $isOSDS = stripos($department, 'Office of the Schools Division Superintendent') !== false;
        $isCID_SGOD = (
            stripos($department, 'Curriculum') !== false ||
            stripos($department, 'School Governance') !== false
        );

        $HR_AO_IV = $this->get_fixed_staff('HR_CERTIFICATION');
        $HR_AO_V  = $this->get_fixed_staff('RECOMMENDING_AUTHORITY');
        $ASDS     = $this->get_fixed_staff('APPROVING_AUTHORITY');
        $SDS      = $this->get_fixed_staff('SDS');

        $school_head = $this->resolve_direct_head($staff);
        $CHIEF = $this->resolve_cid_or_sgod_chief($department);

        if ($isSchoolHead) {
            return array(
                'HR_CERTIFICATION'       => $HR_AO_IV,
                'RECOMMENDING_AUTHORITY' => $ASDS,
                'APPROVING_AUTHORITY'    => $SDS
            );
        }

        if ($isSchoolBased) {
            if ((float)$working_days >= 61) {
                return array(
                    'HR_CERTIFICATION'       => $HR_AO_IV,
                    'RECOMMENDING_AUTHORITY' => $ASDS,
                    'APPROVING_AUTHORITY'    => $SDS
                );
            }
            return array(
                'HR_CERTIFICATION'       => $HR_AO_IV,
                'RECOMMENDING_AUTHORITY' => $school_head,
                'APPROVING_AUTHORITY'    => $ASDS
            );
        }

        if ($isOSDS) {
            return array(
                'HR_CERTIFICATION'       => $HR_AO_IV,
                'RECOMMENDING_AUTHORITY' => $HR_AO_V,
                'APPROVING_AUTHORITY'    => $ASDS
            );
        }

        if ($isCID_SGOD) {
            return array(
                'HR_CERTIFICATION'       => $HR_AO_IV,
                'RECOMMENDING_AUTHORITY' => $CHIEF,
                'APPROVING_AUTHORITY'    => $ASDS
            );
        }

        return array(
            'HR_CERTIFICATION'       => $HR_AO_IV,
            'RECOMMENDING_AUTHORITY' => $HR_AO_V,
            'APPROVING_AUTHORITY'    => $ASDS
        );
    }

    public function snapshot_signatories($leave_id, $signatories)
    {
        foreach ($signatories as $role => $s) {
            if (!is_array($s) || empty($s['IDNumber'])) {
                continue;
            }

            $exists = $this->db->where('leave_application_id', $leave_id)
                ->where('signatory_role', $role)
                ->get('leave_application_signatories')
                ->row();

            $payload = array(
                'leave_application_id' => $leave_id,
                'signatory_role'       => $role,
                'staff_idnumber'       => $s['IDNumber'],
                'full_name'            => strtoupper($this->format_full_name($s)),
                'position_title'       => isset($s['empPosition']) ? strtoupper($s['empPosition']) : '',
                'signature_path'       => isset($s['esig']) ? $s['esig'] : '',
                'office_name'          => isset($s['Department']) ? strtoupper($s['Department']) : '',
            );

            if ($exists) {
                if ((empty($exists->staff_idnumber) || empty($exists->full_name) || empty($exists->signature_path)) && $exists->action_status === 'PENDING') {
                    $this->db->where('id', $exists->id)->update('leave_application_signatories', $payload);
                }
                continue;
            }

            $payload['action_status'] = 'PENDING';
            $this->db->insert('leave_application_signatories', $payload);
        }
    }

    public function get_assigned_signatory($leave_id, $role)
    {
        return $this->db->where('leave_application_id', $leave_id)
            ->where('signatory_role', $role)
            ->get('leave_application_signatories')
            ->row_array();
    }

    public function mark_signed($leave_id, $role, $staff_id, $signed_at = null)
    {
        $staff = $this->get_staff($staff_id);
        if (!$staff) {
            return false;
        }

        $status_map = array(
            'HR_CERTIFICATION'       => 'CERTIFIED',
            'RECOMMENDING_AUTHORITY' => 'RECOMMENDED',
            'APPROVING_AUTHORITY'    => 'APPROVED'
        );
        $status = isset($status_map[$role]) ? $status_map[$role] : 'SIGNED';

        $this->db->where('leave_application_id', $leave_id)
            ->where('signatory_role', $role)
            ->update('leave_application_signatories', array(
                'staff_idnumber' => $staff['IDNumber'],
                'full_name'      => strtoupper($this->format_full_name($staff)),
                'position_title' => isset($staff['empPosition']) ? strtoupper($staff['empPosition']) : '',
                'signature_path' => isset($staff['esig']) ? $staff['esig'] : '',
                'office_name'    => isset($staff['Department']) ? strtoupper($staff['Department']) : '',
                'signed_at'      => $signed_at ?: date('Y-m-d H:i:s'),
                'action_status'  => $status
            ));

        return true;
    }

    public function get_signatories($leave_id)
    {
        return $this->db->where('leave_application_id', $leave_id)
            ->get('leave_application_signatories')
            ->result_array();
    }
}
