<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Per-vacancy signatories.
 *
 * Every job vacancy (Teaching, School Administration, Related Teaching or
 * Non-Teaching alike) keeps its own ordered list of signatories, each with an
 * optional e-signature image stored in uploads/esig. The list is what the RQA
 * reports print at the bottom of the sheet, in signatory_order.
 */
class Vacancy_signatory_model extends CI_Model
{
    protected $table = 'hris_vacancy_signatories';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->ensure_table();
    }

    /**
     * Idempotent schema guard - creates the table on first use only, never
     * touches it again once it exists.
     */
    public function ensure_table(): void
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `hris_vacancy_signatories` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `job_id` INT UNSIGNED NOT NULL,
                `name` VARCHAR(200) NOT NULL,
                `designation` VARCHAR(200) NULL DEFAULT NULL,
                `sign_role` VARCHAR(100) NULL DEFAULT NULL,
                `esig` VARCHAR(255) NULL DEFAULT NULL,
                `signatory_order` INT NOT NULL DEFAULT 0,
                `created_by` INT UNSIGNED NULL DEFAULT NULL,
                `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                KEY `idx_job_order` (`job_id`, `signatory_order`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
    }

    /**
     * Signatories of one vacancy, first to print first.
     */
    public function get_by_job($job_id): array
    {
        return $this->db
            ->where('job_id', (int) $job_id)
            ->order_by('signatory_order', 'ASC')
            ->order_by('id', 'ASC')
            ->get($this->table)
            ->result();
    }

    public function get_by_id($id)
    {
        return $this->db
            ->where('id', (int) $id)
            ->get($this->table)
            ->row();
    }

    public function insert(array $data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, array $data)
    {
        $this->db->where('id', (int) $id);
        return $this->db->update($this->table, $data);
    }

    public function delete($id)
    {
        $this->db->where('id', (int) $id);
        return $this->db->delete($this->table);
    }

    /**
     * Highest order used by this vacancy, so a new row lands at the bottom.
     */
    public function max_order($job_id): int
    {
        $row = $this->db
            ->select_max('signatory_order', 'max_order')
            ->where('job_id', (int) $job_id)
            ->get($this->table)
            ->row();

        return ($row && $row->max_order !== null) ? (int) $row->max_order : 0;
    }

    public function count_by_job($job_id): int
    {
        return (int) $this->db
            ->where('job_id', (int) $job_id)
            ->count_all_results($this->table);
    }

    /**
     * Swap a row with its neighbour so the print order can be nudged without
     * retyping order numbers. Orders are renumbered first, which also repairs
     * any duplicate/zero order left by earlier edits.
     */
    public function move($id, $direction): bool
    {
        $row = $this->get_by_id($id);
        if (empty($row)) {
            return false;
        }

        $this->normalize_orders($row->job_id);

        $row  = $this->get_by_id($id);
        $rows = $this->get_by_job($row->job_id);

        $index = null;
        foreach ($rows as $i => $r) {
            if ((int) $r->id === (int) $row->id) {
                $index = $i;
                break;
            }
        }

        if ($index === null) {
            return false;
        }

        $target = ($direction === 'up') ? $index - 1 : $index + 1;
        if ($target < 0 || !isset($rows[$target])) {
            return false;
        }

        $a = $rows[$index];
        $b = $rows[$target];

        $this->update($a->id, array('signatory_order' => (int) $b->signatory_order));
        $this->update($b->id, array('signatory_order' => (int) $a->signatory_order));

        return true;
    }

    /**
     * Renumber a vacancy's signatories 1..n keeping their current sequence.
     */
    public function normalize_orders($job_id): void
    {
        $order = 1;
        foreach ($this->get_by_job($job_id) as $row) {
            if ((int) $row->signatory_order !== $order) {
                $this->update($row->id, array('signatory_order' => $order));
            }
            $order++;
        }
    }

    /**
     * Signatories of several vacancies at once, keyed by job_id - used by the
     * job vacancy list so it can show a count without a query per row.
     */
    public function counts_by_job(array $job_ids): array
    {
        $job_ids = array_values(array_filter(array_map('intval', $job_ids)));
        if (empty($job_ids)) {
            return array();
        }

        $rows = $this->db
            ->select('job_id, COUNT(*) AS total', false)
            ->where_in('job_id', $job_ids)
            ->group_by('job_id')
            ->get($this->table)
            ->result();

        $counts = array();
        foreach ($rows as $row) {
            $counts[(int) $row->job_id] = (int) $row->total;
        }

        return $counts;
    }
}
