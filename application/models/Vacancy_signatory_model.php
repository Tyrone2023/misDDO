<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Per-vacancy signatories.
 *
 * Every job vacancy (Teaching, School Administration, Related Teaching or
 * Non-Teaching alike) keeps its own ordered list of signatories, each with an
 * optional e-signature image stored in uploads/esig. The list is what the RQA
 * reports print at the bottom of the sheet, positioned by print_slot in a
 * five-column grid.
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
                `print_slot` INT UNSIGNED NULL DEFAULT NULL,
                `print_label` VARCHAR(200) NULL DEFAULT NULL,
                `created_by` INT UNSIGNED NULL DEFAULT NULL,
                `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                KEY `idx_job_order` (`job_id`, `signatory_order`),
                KEY `idx_job_slot` (`job_id`, `print_slot`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        // Existing installations may already have the original signatory
        // table. Add the layout fields without requiring a separate migration.
        if (!$this->db->field_exists('print_slot', $this->table)) {
            $this->db->query("ALTER TABLE `{$this->table}` ADD COLUMN `print_slot` INT UNSIGNED NULL DEFAULT NULL AFTER `signatory_order`");
            $this->db->query("ALTER TABLE `{$this->table}` ADD KEY `idx_job_slot` (`job_id`, `print_slot`)");
        }
        if (!$this->db->field_exists('print_label', $this->table)) {
            $this->db->query("ALTER TABLE `{$this->table}` ADD COLUMN `print_label` VARCHAR(200) NULL DEFAULT NULL AFTER `print_slot`");
        }

        // Preserve the old printed sequence as the initial five-column layout.
        $this->db->query(
            "UPDATE `{$this->table}`
                SET `print_slot` = CASE WHEN `signatory_order` > 0 THEN `signatory_order` ELSE `id` END
              WHERE `print_slot` IS NULL OR `print_slot` < 1"
        );
    }

    /**
     * Signatories of one vacancy, first to print first.
     */
    public function get_by_job($job_id): array
    {
        return $this->db
            ->where('job_id', (int) $job_id)
            ->order_by('print_slot', 'ASC')
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

    /** Highest occupied position in the five-column printed layout. */
    public function max_slot($job_id): int
    {
        $row = $this->db
            ->select_max('print_slot', 'max_slot')
            ->where('job_id', (int) $job_id)
            ->get($this->table)
            ->row();

        return ($row && $row->max_slot !== null) ? (int) $row->max_slot : 0;
    }

    /**
     * Find the first unused layout position at or after the requested slot.
     * This keeps inserts deterministic and prevents two cards occupying a cell.
     */
    public function next_available_slot($job_id, $start = 1): int
    {
        $slot = max(1, min(50, (int) $start));
        $used = array();

        foreach ($this->get_by_job($job_id) as $row) {
            $used[(int) $row->print_slot] = true;
        }

        while (isset($used[$slot]) && $slot < 50) {
            $slot++;
        }

        return $slot;
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

        return $this->move_to_slot($row->id, (int) $rows[$target]->print_slot);
    }

    /**
     * Place one signatory in an exact cell. If the cell is occupied, the two
     * signatories swap places so no entry is silently displaced.
     */
    public function move_to_slot($id, $target_slot): bool
    {
        $row = $this->get_by_id($id);
        $target_slot = (int) $target_slot;

        if (empty($row) || $target_slot < 1 || $target_slot > 50) {
            return false;
        }

        $current_slot = max(1, (int) $row->print_slot);
        if ($current_slot === $target_slot) {
            return true;
        }

        $occupant = $this->db
            ->where('job_id', (int) $row->job_id)
            ->where('print_slot', $target_slot)
            ->where('id !=', (int) $row->id)
            ->get($this->table)
            ->row();

        $this->db->trans_start();
        if (!empty($occupant)) {
            $this->update($occupant->id, array('print_slot' => $current_slot));
        }
        $this->update($row->id, array('print_slot' => $target_slot));
        $this->db->trans_complete();

        $this->normalize_orders($row->job_id);
        return $this->db->trans_status();
    }

    /** Move through the five-column grid using the arrow controls. */
    public function move_in_layout($id, $direction): bool
    {
        $row = $this->get_by_id($id);
        if (empty($row)) {
            return false;
        }

        $slot = max(1, (int) $row->print_slot);
        $column = (($slot - 1) % 5) + 1;

        switch ($direction) {
            case 'left':
                if ($column === 1) { return false; }
                $target = $slot - 1;
                break;
            case 'right':
                if ($column === 5) { return false; }
                $target = $slot + 1;
                break;
            case 'up':
                $target = $slot - 5;
                if ($target < 1) { return false; }
                break;
            case 'down':
                $target = $slot + 5;
                if ($target > 50) { return false; }
                break;
            default:
                return false;
        }

        return $this->move_to_slot($id, $target);
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
