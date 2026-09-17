<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Selective (second round) selection batches.
 *
 * A vacancy is sometimes deliberated more than once - the later round must
 * leave out the applicants already acted on in the first one. Each batch keeps
 * the manually picked hris_applications.appID rows of one round; the existing
 * IER and RQA reports print only those rows when opened with ?batch={id}.
 *
 * Nothing here writes to hris_applications - the batch is only a filter.
 */
class Reselection_model extends CI_Model
{
    protected $batch_table  = 'hris_reselection_batch';
    protected $member_table = 'hris_reselection_member';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->ensure_table();
    }

    /**
     * Idempotent schema guard - creates the two tables on first use only and
     * never touches them again once they exist.
     */
    public function ensure_table(): void
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `hris_reselection_batch` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `jobID` INT UNSIGNED NOT NULL,
                `batch_name` VARCHAR(200) NOT NULL,
                `round_no` INT UNSIGNED NOT NULL DEFAULT 2,
                `remarks` TEXT NULL DEFAULT NULL,
                `created_by` INT UNSIGNED NULL DEFAULT NULL,
                `created_by_name` VARCHAR(150) NULL DEFAULT NULL,
                `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                KEY `idx_job` (`jobID`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        $this->db->query("
            CREATE TABLE IF NOT EXISTS `hris_reselection_member` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `batch_id` INT UNSIGNED NOT NULL,
                `appID` INT UNSIGNED NOT NULL,
                `added_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY `uniq_batch_app` (`batch_id`, `appID`),
                KEY `idx_batch` (`batch_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
    }

    /* ------------------------------------------------------------------ */
    /* Batches                                                             */
    /* ------------------------------------------------------------------ */

    public function get_batches($jobID): array
    {
        return $this->db->query(
            "select b.*, (select count(*) from `{$this->member_table}` m where m.batch_id = b.id) as total
               from `{$this->batch_table}` b
              where b.jobID = ?
              order by b.round_no asc, b.id asc",
            array((int) $jobID)
        )->result();
    }

    public function get_batch($id)
    {
        return $this->db->where('id', (int) $id)->get($this->batch_table)->row();
    }

    /** Next round number for a vacancy - first batch created is round 2. */
    public function next_round($jobID): int
    {
        $row = $this->db->select_max('round_no', 'mx')
            ->where('jobID', (int) $jobID)
            ->get($this->batch_table)
            ->row();

        return max(2, (int) ($row->mx ?? 1) + 1);
    }

    public function create_batch(array $data)
    {
        $this->db->insert($this->batch_table, $data);
        return $this->db->insert_id();
    }

    public function update_batch($id, array $data): void
    {
        $this->db->where('id', (int) $id)->update($this->batch_table, $data);
    }

    /** Removes the batch and its picked applicants - never the applications. */
    public function delete_batch($id): void
    {
        $id = (int) $id;
        $this->db->where('batch_id', $id)->delete($this->member_table);
        $this->db->where('id', $id)->delete($this->batch_table);
    }

    /** Group types as posted on Page/jobVacancy (hris_jobvacancy.job_type). */
    public function job_types(): array
    {
        return array(
            1  => "Elementary",
            2  => "Secondary",
            3  => "Junior High School",
            4  => "Senior High School",
            5  => "Kindergarten",
            6  => "IPED Elementary",
            7  => "IPED Secondary",
            8  => "IPED Junior High School",
            9  => "IPED Senior High School",
            10 => "SNED",
            11 => "SHS Academic and Core Subjects",
            12 => "SHS Arts and Design Track",
            13 => "SHS Sports Track",
            14 => "SHS Technical-Vocational(TVL) Track",
            15 => "Elementary - SPIMS",
            16 => "Junior High School - SPIMS",
            17 => "DOST - (RA 7687)",
            18 => "DOST - (RA 10612)",
            19 => "(SST I)",
            20 => "FOR TESTING PURPOSES (DO NOT APPLY)"
        );
    }

    /**
     * Vacancy cards on the Selective IER / RQA landing page: the ones still
     * posted, plus any archived vacancy that already carries a batch so an
     * unfinished second round stays reachable. $all drops both conditions.
     */
    public function dashboard_vacancies($all = false): array
    {
        $where = $all
            ? '1 = 1'
            : "v.jvStatus = 'Open' or exists (select 1 from `{$this->batch_table}` b2 where b2.jobID = v.jobID)";

        $rows = $this->db->query(
            "select v.jobID, v.jobTitle, v.sy, v.job_type, v.position, v.promotion, v.jvStatus, v.itemNo,
                    (select count(*) from hris_applications a where a.jobID = v.jobID) as applicant_total,
                    (select count(*) from hris_applications a2 where a2.jobID = v.jobID and a2.dq = 1) as qualified_total,
                    (select count(*) from `{$this->batch_table}` b where b.jobID = v.jobID) as batch_total,
                    (select count(*)
                       from `{$this->member_table}` m
                       join `{$this->batch_table}` b3 on b3.id = m.batch_id
                      where b3.jobID = v.jobID) as picked_total
               from hris_jobvacancy v
              where {$where}
              order by v.sy desc, v.jobID desc"
        )->result();

        $types  = $this->job_types();
        $groups = $this->position_groups();

        foreach ($rows as $row) {
            $row->rs_type  = $types[(int) $row->job_type] ?? '';
            $row->rs_group = (int) $row->promotion === 1
                ? 'Promotion'
                : ($groups[(int) $row->position] ?? 'Vacancy');
        }

        return $rows;
    }

    /** Position groups as labelled on Page/jobVacancy (hris_jobvacancy.position). */
    public function position_groups(): array
    {
        return array(
            1 => 'Teaching',
            2 => 'School Administration',
            3 => 'Related Teaching',
            4 => 'Non-Teaching'
        );
    }
    /* ------------------------------------------------------------------ */
    /* Members                                                             */
    /* ------------------------------------------------------------------ */

    /** appIDs picked for one batch. */
    public function member_ids($batch_id): array
    {
        $rows = $this->db->select('appID')
            ->where('batch_id', (int) $batch_id)
            ->get($this->member_table)
            ->result();

        $ids = array();
        foreach ($rows as $row) {
            $ids[] = (int) $row->appID;
        }

        return $ids;
    }

    /**
     * appIDs already taken by the other batches of the same vacancy - the
     * "already done" applicants the next round is meant to skip.
     */
    public function used_ids($jobID, $exclude_batch_id = 0): array
    {
        $rows = $this->db->query(
            "select m.appID, b.id as batch_id, b.batch_name, b.round_no
               from `{$this->member_table}` m
               join `{$this->batch_table}` b on b.id = m.batch_id
              where b.jobID = ? and b.id != ?",
            array((int) $jobID, (int) $exclude_batch_id)
        )->result();

        $used = array();
        foreach ($rows as $row) {
            $used[(int) $row->appID] = $row;
        }

        return $used;
    }

    /**
     * Replaces the whole picked list of a batch in one pass. Rows already
     * saved are kept as they are so added_at stays meaningful.
     */
    public function set_members($batch_id, array $appIDs): int
    {
        $batch_id = (int) $batch_id;

        $clean = array();
        foreach ($appIDs as $appID) {
            $appID = (int) $appID;
            if ($appID > 0) {
                $clean[$appID] = $appID;
            }
        }

        $current = $this->member_ids($batch_id);

        $remove = array_diff($current, $clean);
        if (!empty($remove)) {
            $this->db->where('batch_id', $batch_id)
                ->where_in('appID', $remove)
                ->delete($this->member_table);
        }

        $add = array_diff($clean, $current);
        if (!empty($add)) {
            $rows = array();
            foreach ($add as $appID) {
                $rows[] = array('batch_id' => $batch_id, 'appID' => $appID);
            }
            $this->db->insert_batch($this->member_table, $rows);
        }

        return count($clean);
    }

    /* ------------------------------------------------------------------ */
    /* Applicants of the vacancy                                           */
    /* ------------------------------------------------------------------ */

    /**
     * Rating table a vacancy scores into - the same one its RQA report reads.
     */
    public function rating_table($job): string
    {
        if ((int) ($job->promotion ?? 0) === 1) {
            return 'hris_rating_promotion';
        }

        return ((int) ($job->position ?? 0) === 1)
            ? 'hris_applications_rating'
            : 'hris_rating_none';
    }

    /**
     * Every application of the vacancy with the identity fields the picker
     * shows. Mirrors Hiring_model::get_submitted_applicant() (same IER source)
     * plus the score and the hired flag so HR can tell who is already done.
     */
    public function applicants($job): array
    {
        $jobID = (int) $job->jobID;
        $table = $this->rating_table($job);

        $sql = "
            select a.appID, a.jobID, a.dq, a.appStatus, a.district, a.dateSubmitted,
                   coalesce(app.record_no, staff.IDNumber) as code,
                   coalesce(app.FirstName, staff.FirstName) as FirstName,
                   coalesce(app.MiddleName, staff.MiddleName) as MiddleName,
                   coalesce(app.LastName, staff.LastName) as LastName,
                   coalesce(app.NameExtn, staff.NameExtn) as NameExtn,
                   coalesce(app.resCity, staff.resCity) as resCity,
                   coalesce(app.empEmail, staff.IDNumber) as email,
                   r.total_points,
                   case when h.appID is not null then 1 else 0 end as hired
              from hris_applications a
              left join (
                    select x.*
                      from hris_applicant x
                      inner join (
                            select empEmail, max(id) as max_id
                              from hris_applicant
                             group by empEmail
                      ) m on m.empEmail = x.empEmail and m.max_id = x.id
              ) app on a.empEmail = app.empEmail
              left join hris_staff staff on app.record_no is null and a.empEmail = staff.IDNumber
              left join (
                    select t.appID, max(t.total_points) as total_points
                      from `{$table}` t
                     group by t.appID
              ) r on r.appID = a.appID
              left join (select distinct appID from hris_hire) h on h.appID = a.appID
             where a.jobID = ?
             order by r.total_points desc, a.dq asc, LastName asc
        ";

        return $this->db->query($sql, array($jobID))->result();
    }
}
