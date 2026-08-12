<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Stand-in for a rating row that does not exist yet.
 *
 * The rating views read $rating->educ, $rating->trainings and ~120 other
 * properties directly. A row is only created when the Secretariat endorses the
 * applicant (Pages::Unqualified_none -> Reg::insert_rate_none), so anyone who
 * opens the rating page before that - or an application produced by
 * Pages::copy_application from a source that had no rating - hits a NULL
 * $rating and every read emits "Attempt to read property on null". The live site
 * runs as ENVIRONMENT=development (warnings are rendered, not hidden), so those
 * warnings land in the user's page.
 *
 * Rather than guarding every read, hand the view an object shaped like a genuine
 * unrated row: real column list, score columns carrying the same 0.00001
 * "not rated yet" sentinel that the insert_rate_* methods use, everything else
 * NULL. The page then renders exactly as it does for a freshly endorsed
 * applicant - blank score boxes, no evaluator - instead of a wall of warnings.
 *
 * This deliberately does not write anything. A row is created when a score is
 * actually saved (see Reg::ensure_rate_row).
 *
 * @param  string $table One of the hris rating tables.
 * @return stdClass
 */
if (!function_exists('blank_rating_row')) {
    function blank_rating_row($table)
    {
        static $cache = array();

        if (isset($cache[$table])) {
            return clone $cache[$table];
        }

        $CI =& get_instance();

        $row = new stdClass();

        // Every column the table actually has, so no read can miss.
        foreach ($CI->db->list_fields($table) as $field) {
            $row->$field = NULL;
        }

        foreach (rating_score_fields($table) as $field) {
            $row->$field = 0.00001;
        }

        $cache[$table] = $row;

        return clone $row;
    }
}

/**
 * Values for columns that are NOT NULL with no database default, so a fresh
 * rating row can be inserted on a server running in STRICT mode.
 *
 * hris_rating_none.total_points and .skills (and the equivalents on the other
 * two tables) are declared NOT NULL without a default, which is why
 * Reg::insert_rate_none gets away with omitting them only on a non-strict
 * server. Derive the list from the schema rather than hardcoding it, so a new
 * NOT NULL column does not quietly start breaking inserts.
 *
 * @param  string $table
 * @return array column => zero value of the right type
 */
if (!function_exists('rating_required_defaults')) {
    function rating_required_defaults($table)
    {
        static $cache = array();

        if (isset($cache[$table])) {
            return $cache[$table];
        }

        $CI =& get_instance();

        // Raw query on purpose: the query builder would try to escape
        // "information_schema.COLUMNS" as an identifier of this database.
        $sql = 'SELECT COLUMN_NAME, DATA_TYPE
                  FROM information_schema.COLUMNS
                 WHERE TABLE_SCHEMA = ?
                   AND TABLE_NAME = ?
                   AND IS_NULLABLE = "NO"
                   AND COLUMN_DEFAULT IS NULL
                   AND EXTRA NOT LIKE "%auto_increment%"';

        $rows = $CI->db->query($sql, array($CI->db->database, $table))->result();

        $numeric = array('int', 'bigint', 'smallint', 'tinyint', 'mediumint',
                         'decimal', 'double', 'float', 'numeric');

        $defaults = array();
        foreach ($rows as $row) {
            $defaults[$row->COLUMN_NAME] = in_array(strtolower($row->DATA_TYPE), $numeric, TRUE) ? 0 : '';
        }

        $cache[$table] = $defaults;

        return $defaults;
    }
}

/**
 * Score columns per rating table - the ones the views compare against the
 * 0.00001 sentinel. Kept in step with Reg::insert_rate, insert_rate_none and
 * insert_rate_promotion.
 *
 * @param  string $table
 * @return array
 */
if (!function_exists('rating_score_fields')) {
    function rating_score_fields($table)
    {
        $fields = array(
            'hris_applications_rating' => array(
                'education', 'training', 'experience',
                'let_rating', 'demo_rating', 'tr_rating',
            ),
            'hris_rating_none' => array(
                'educ', 'trainings', 'experience', 'performance',
                'oa', 'ae', 'ald', 'interview', 'written',
            ),
            'hris_rating_promotion' => array(
                'educ', 'trainings', 'experience', 'performance',
                'ppstco', 'ppstpa',
            ),
        );

        return isset($fields[$table]) ? $fields[$table] : array();
    }
}
