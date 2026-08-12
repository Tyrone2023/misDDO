<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Reads the per-position scoring criteria maintained under
 * Page/positionCriteria and hands them to the screens that rate applicants.
 *
 * Before this, a rating screen took its point ceilings from one of six shared
 * rows in `hris_position_points` (picked by `hris_positions.bracket`) and its
 * "Applicants QS" pickers from `score_list`, keyed by `hris_positions.g_score`
 * - a column that is 0 on every row on file, so those pickers came up empty.
 *
 * A position with a criteria sheet now drives both. A position without one is
 * left exactly as it was, on the shared bracket, so nothing changes for the
 * titles that have not been set up yet.
 */
class Position_criteria_model extends CI_Model
{
    /**
     * Rating slot => name fragments that identify it, most specific first so
     * "Application of Education" is not swallowed by "Education". These are the
     * column names used by `hris_position_points` and by the rating views.
     */
    private $slot_patterns = array(
        'ae'        => array('application of education'),
        'ald'       => array('application of learning', 'learning and development'),
        'oa'        => array('outstanding'),
        'per'       => array('performance'),
        'educ'      => array('education'),
        'tr'        => array('training'),
        'exp'       => array('experience'),
        'potential' => array('potential', 'written exam', 'bei')
    );

    /**
     * The criteria of one position in sheet order, each with its increment
     * levels attached. Empty when the position has no sheet on file.
     */
    public function sheet($position_id)
    {
        $position_id = (int) $position_id;

        if ($position_id <= 0 || !$this->db->table_exists('hris_position_criteria')) {
            return array();
        }

        $this->db->where('position_id', $position_id);
        $this->db->order_by('criterion', 'ASC');
        $rows = $this->db->get('hris_position_criteria')->result();

        if (empty($rows)) {
            return array();
        }

        $criteria = array();
        $index    = array();

        foreach ($rows as $i => $row) {
            $criteria[$i] = array(
                'criterion'  => (int) $row->criterion,
                'label'      => (string) $row->label,
                'max_points' => (float) $row->max_points,
                'levels'     => array()
            );
            $index[(int) $row->criterion] = $i;
        }

        $this->db->where('position_id', $position_id);
        $this->db->order_by('criterion', 'ASC');
        $this->db->order_by('increment_level', 'ASC');
        $this->db->order_by('id', 'ASC');

        foreach ($this->db->get('hris_position_criteria_level')->result() as $row) {
            $code = (int) $row->criterion;
            if (isset($index[$code])) {
                $criteria[$index[$code]]['levels'][] = $row;
            }
        }

        return array_values($criteria);
    }

    /**
     * The same sheet keyed by the rating slot each criterion feeds, so a view
     * can ask for "the Education criterion" without knowing its position in
     * the sheet. Criteria whose name matches no slot are left out - they are
     * still part of the sheet, they just have no rating box to fill on these
     * screens. First match wins when two criteria claim one slot.
     */
    public function slots($position_id)
    {
        $slots = array();

        foreach ($this->sheet($position_id) as $criterion) {
            $slot = $this->slot_for($criterion['label']);

            if ($slot !== null && !isset($slots[$slot])) {
                $slots[$slot] = $criterion;
            }
        }

        return $slots;
    }

    /**
     * Which rating slot a criterion name belongs to, or null when it is one of
     * this position's own criteria with nowhere on the rating screen to go.
     */
    public function slot_for($label)
    {
        $label = strtolower(trim((string) $label));

        if ($label === '') {
            return null;
        }

        foreach ($this->slot_patterns as $slot => $patterns) {
            foreach ($patterns as $pattern) {
                if (strpos($label, $pattern) !== false) {
                    return $slot;
                }
            }
        }

        return null;
    }

    /**
     * A drop-in replacement for the `hris_position_points` row the rating views
     * read their maximums from. Every slot the position's sheet defines comes
     * from the sheet; the rest keep the value on $fallback, so a sheet that
     * leaves out Potential (scored later, from the written exam and the BEI)
     * does not knock out the ceiling the interview screens still need.
     */
    public function points($position_id, $fallback = null)
    {
        $slots = $this->slots($position_id);

        if (empty($slots)) {
            return $fallback;
        }

        $points = new stdClass();

        foreach (array_keys($this->slot_patterns) as $slot) {
            if (isset($slots[$slot])) {
                $points->$slot = $slots[$slot]['max_points'];
            } else {
                $points->$slot = isset($fallback->$slot) ? $fallback->$slot : 0;
            }
        }

        // carry anything else the legacy row exposed (id, position, ...)
        if (is_object($fallback)) {
            foreach (get_object_vars($fallback) as $key => $value) {
                if (!isset($points->$key)) {
                    $points->$key = $value;
                }
            }
        }

        return $points;
    }

    /**
     * The increment levels of one slot, ready for an "Applicants QS" picker.
     * Falls back to the legacy `score_list` rows when the position has no
     * sheet, so untouched positions keep whatever they had.
     */
    public function levels_for_slot($position_id, $slot, $legacy = array())
    {
        $slots = $this->slots($position_id);

        if (isset($slots[$slot])) {
            return $slots[$slot]['levels'];
        }

        return $legacy;
    }
}
