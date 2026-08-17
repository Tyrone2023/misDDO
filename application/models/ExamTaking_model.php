<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * The applicant side of the Exam Builder: attempts, answers and grading.
 *
 * Kept apart from ExamBuilder_model, which owns authoring. That model is read by
 * the Secretariat screens; this one is read by the applicant screens, and the two
 * only meet through hris_exams / hris_exam_questions.
 *
 * Grading mirrors the SRMS College Assessment Suite exactly - all-or-nothing per
 * question, including matching - so a score means the same thing in both systems.
 * Essays are never auto-graded; they are stored with a null verdict and counted as
 * pending review.
 */
class ExamTaking_model extends CI_Model
{
    private $tableAttempts = 'hris_exam_attempts';
    private $tableAnswers = 'hris_exam_attempt_answers';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->ensure_tables();
    }

    public function ensure_tables(): void
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS {$this->tableAttempts} (
                attempt_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                exam_id INT UNSIGNED NOT NULL,
                job_id INT UNSIGNED NOT NULL,
                app_id INT UNSIGNED NOT NULL,
                applicant_id INT UNSIGNED NOT NULL,
                applicant_email VARCHAR(150) NULL,
                attempt_no INT UNSIGNED NOT NULL DEFAULT 1,
                status VARCHAR(20) NOT NULL DEFAULT 'in_progress',
                score DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                total_points DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                percentage DECIMAL(7,2) NOT NULL DEFAULT 0.00,
                answered_count INT UNSIGNED NOT NULL DEFAULT 0,
                auto_graded_count INT UNSIGNED NOT NULL DEFAULT 0,
                pending_review_count INT UNSIGNED NOT NULL DEFAULT 0,
                started_at DATETIME NOT NULL,
                expires_at DATETIME NULL,
                submitted_at DATETIME NULL,
                timed_out TINYINT(1) NOT NULL DEFAULT 0,
                ip_address VARCHAR(64) NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                UNIQUE KEY uniq_attempt (exam_id, app_id, attempt_no),
                KEY idx_exam_status (exam_id, status),
                KEY idx_app (app_id),
                KEY idx_applicant (applicant_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $this->db->query("
            CREATE TABLE IF NOT EXISTS {$this->tableAnswers} (
                answer_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                attempt_id INT UNSIGNED NOT NULL,
                question_id INT UNSIGNED NOT NULL,
                response_json LONGTEXT NULL,
                is_correct TINYINT(1) NULL,
                points_awarded DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                max_points DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                UNIQUE KEY uniq_attempt_question (attempt_id, question_id),
                KEY idx_attempt (attempt_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    }

    /** Every attempt this application has made on one exam, oldest first. */
    public function attempts_for(int $examId, int $appId): array
    {
        return $this->db
            ->from($this->tableAttempts)
            ->where('exam_id', $examId)
            ->where('app_id', $appId)
            ->order_by('attempt_no', 'asc')
            ->get()
            ->result();
    }

    /**
     * Attempt tallies for a set of exams, keyed by exam id, for one application.
     * Lets the gate render "1 of 2 used" without a query per exam.
     */
    public function attempt_summary(array $examIds, int $appId): array
    {
        $examIds = array_values(array_unique(array_filter(array_map('intval', $examIds))));
        if (empty($examIds) || $appId <= 0) {
            return [];
        }

        $rows = $this->db
            ->select("exam_id,
                COUNT(*) AS total,
                SUM(CASE WHEN status = 'submitted' THEN 1 ELSE 0 END) AS submitted,
                MAX(CASE WHEN status = 'submitted' THEN score END) AS best_score,
                MAX(attempt_id) AS last_attempt_id", false)
            ->from($this->tableAttempts)
            ->where_in('exam_id', $examIds)
            ->where('app_id', $appId)
            ->group_by('exam_id')
            ->get()
            ->result();

        $summary = [];
        foreach ($rows as $row) {
            $summary[(int) $row->exam_id] = [
                'total' => (int) $row->total,
                'submitted' => (int) $row->submitted,
                'best_score' => $row->best_score === null ? null : (float) $row->best_score,
                'last_attempt_id' => (int) $row->last_attempt_id,
            ];
        }

        return $summary;
    }

    /** The attempt still in progress for this exam/application, if any. */
    public function open_attempt(int $examId, int $appId): ?object
    {
        $attempt = $this->db
            ->from($this->tableAttempts)
            ->where('exam_id', $examId)
            ->where('app_id', $appId)
            ->where('status', 'in_progress')
            ->order_by('attempt_no', 'desc')
            ->limit(1)
            ->get()
            ->row();

        return $attempt ?: null;
    }

    public function get_attempt(int $attemptId): ?object
    {
        $attempt = $this->db
            ->select('a.*, e.title AS exam_title, e.instructions, e.time_limit_minutes, e.passing_score,
                e.attempt_limit, e.exam_code, e.status AS exam_status, e.close_at, e.open_at,
                e.job_title, e.position_group, j.jobTitle AS vacancy_title', false)
            ->from($this->tableAttempts . ' a')
            ->join('hris_exams e', 'e.exam_id = a.exam_id', 'left')
            ->join('hris_jobvacancy j', 'j.jobID = a.job_id', 'left')
            ->where('a.attempt_id', $attemptId)
            ->get()
            ->row();

        return $attempt ?: null;
    }

    /**
     * Open a new attempt.
     *
     * expires_at is stamped once, at start, from the exam's time limit. Deriving it
     * on each request instead would hand the applicant a fresh clock every reload.
     */
    public function start_attempt(array $meta, ?int $timeLimitMinutes): ?object
    {
        $now = date('Y-m-d H:i:s');
        $examId = (int) $meta['exam_id'];
        $appId = (int) $meta['app_id'];

        $last = $this->db
            ->select_max('attempt_no', 'max_no')
            ->from($this->tableAttempts)
            ->where('exam_id', $examId)
            ->where('app_id', $appId)
            ->get()
            ->row();

        $attemptNo = ((int) ($last->max_no ?? 0)) + 1;

        $this->db->insert($this->tableAttempts, [
            'exam_id' => $examId,
            'job_id' => (int) $meta['job_id'],
            'app_id' => $appId,
            'applicant_id' => (int) $meta['applicant_id'],
            'applicant_email' => trim((string) ($meta['applicant_email'] ?? '')) ?: null,
            'attempt_no' => $attemptNo,
            'status' => 'in_progress',
            'started_at' => $now,
            'expires_at' => $timeLimitMinutes > 0
                ? date('Y-m-d H:i:s', strtotime($now . ' +' . (int) $timeLimitMinutes . ' minutes'))
                : null,
            'ip_address' => substr((string) ($meta['ip_address'] ?? ''), 0, 64) ?: null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $attemptId = (int) $this->db->insert_id();

        return $attemptId > 0 ? $this->get_attempt($attemptId) : null;
    }

    public function has_expired(object $attempt): bool
    {
        return !empty($attempt->expires_at) && time() > strtotime((string) $attempt->expires_at);
    }

    public function seconds_remaining(object $attempt): ?int
    {
        if (empty($attempt->expires_at)) {
            return null;
        }

        return max(0, strtotime((string) $attempt->expires_at) - time());
    }

    /**
     * Grade the posted responses and close the attempt.
     *
     * A late POST is still graded - the countdown fires the submit and the request
     * takes time to arrive - but it is flagged timed_out. What stops an applicant
     * buying extra time is the controller refusing to re-serve the exam once the
     * clock has run out, not this method rejecting the answers.
     */
    public function submit_attempt(object $attempt, array $questions, array $responses): array
    {
        $now = date('Y-m-d H:i:s');
        $attemptId = (int) $attempt->attempt_id;

        $score = 0.0;
        $totalPoints = 0.0;
        $answered = 0;
        $autoGraded = 0;
        $pending = 0;

        $this->db->trans_begin();
        $this->db->where('attempt_id', $attemptId)->delete($this->tableAnswers);

        foreach ($questions as $question) {
            $questionId = (int) $question->question_id;
            $raw = $responses[$questionId] ?? null;
            $graded = $this->grade_question($question, $raw);

            $totalPoints += $graded['max_points'];
            $score += $graded['points_awarded'];

            if ($graded['answered']) {
                $answered++;
            }
            if ($graded['is_correct'] === null) {
                $pending++;
            } else {
                $autoGraded++;
            }

            $this->db->insert($this->tableAnswers, [
                'attempt_id' => $attemptId,
                'question_id' => $questionId,
                'response_json' => json_encode($graded['response'], JSON_UNESCAPED_UNICODE),
                'is_correct' => $graded['is_correct'],
                'points_awarded' => $graded['points_awarded'],
                'max_points' => $graded['max_points'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $totals = [
            'status' => 'submitted',
            'score' => round($score, 2),
            'total_points' => round($totalPoints, 2),
            'percentage' => $totalPoints > 0 ? round(($score / $totalPoints) * 100, 2) : 0.00,
            'answered_count' => $answered,
            'auto_graded_count' => $autoGraded,
            'pending_review_count' => $pending,
            'submitted_at' => $now,
            'timed_out' => $this->has_expired($attempt) ? 1 : 0,
            'updated_at' => $now,
        ];

        $this->db->where('attempt_id', $attemptId)->update($this->tableAttempts, $totals);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return [];
        }

        $this->db->trans_commit();

        return $totals;
    }

    /** Close an attempt the applicant never submitted, so it cannot sit open forever. */
    public function abandon_attempt(object $attempt, array $questions): array
    {
        return $this->submit_attempt($attempt, $questions, []);
    }

    public function answers_for(int $attemptId): array
    {
        $rows = $this->db
            ->from($this->tableAnswers)
            ->where('attempt_id', $attemptId)
            ->get()
            ->result();

        $byQuestion = [];
        foreach ($rows as $row) {
            $row->response = json_decode((string) $row->response_json, true);
            $byQuestion[(int) $row->question_id] = $row;
        }

        return $byQuestion;
    }

    /**
     * All-or-nothing per question, matching the college build.
     *
     * @return array{response:mixed,is_correct:?int,points_awarded:float,max_points:float,answered:bool}
     */
    public function grade_question(object $question, $raw): array
    {
        $type = (string) $question->question_type;
        $points = round((float) $question->points, 2);
        $key = (array) $question->answer_key;

        $result = static function ($response, ?int $correct, float $awarded, bool $answered) use ($points) {
            return [
                'response' => $response,
                'is_correct' => $correct,
                'points_awarded' => round($awarded, 2),
                'max_points' => $points,
                'answered' => $answered,
            ];
        };

        if ($type === 'single_choice' || $type === 'true_false') {
            $answer = trim((string) (is_array($raw) ? reset($raw) : $raw));
            if ($type === 'true_false') {
                $answer = strtolower($answer);
            }
            $correct = in_array($answer, array_map('strval', $key), true);

            return $result($answer, $correct ? 1 : 0, $correct ? $points : 0.0, $answer !== '');
        }

        if ($type === 'multiple_choice') {
            $selected = is_array($raw) ? array_values(array_unique(array_map('strval', $raw))) : [];
            $expected = array_values(array_map('strval', $key));
            sort($selected);
            sort($expected);
            $correct = !empty($selected) && $selected === $expected;

            return $result($selected, $correct ? 1 : 0, $correct ? $points : 0.0, !empty($selected));
        }

        if ($type === 'short_answer') {
            $answer = trim((string) (is_array($raw) ? reset($raw) : $raw));
            $accepted = array_map([$this, 'normalize_text'], $key);
            $correct = $answer !== '' && in_array($this->normalize_text($answer), $accepted, true);

            return $result($answer, $correct ? 1 : 0, $correct ? $points : 0.0, $answer !== '');
        }

        if ($type === 'matching') {
            $answer = is_array($raw) ? $raw : [];
            $pairs = 0;
            $correctPairs = 0;
            $index = 0;

            // The form posts by pair index; the stored key is left-prompt => right.
            foreach ($key as $left => $right) {
                $given = $answer[$index] ?? $answer[$left] ?? '';
                if ($this->normalize_text((string) $given) === $this->normalize_text((string) $right)) {
                    $correctPairs++;
                }
                $pairs++;
                $index++;
            }

            $correct = $pairs > 0 && $correctPairs === $pairs;

            return $result($answer, $correct ? 1 : 0, $correct ? $points : 0.0, !empty(array_filter($answer)));
        }

        // Essay: stored for the Secretariat to read, never machine-scored, and it
        // contributes 0 until somebody reviews it.
        $answer = trim((string) (is_array($raw) ? reset($raw) : $raw));

        return $result($answer, null, 0.0, $answer !== '');
    }

    private function normalize_text(string $value): string
    {
        return strtolower(trim(preg_replace('/\s+/u', ' ', $value) ?? $value));
    }

    /**
     * Stable per-attempt ordering.
     *
     * Choices are shuffled so two applicants side by side do not see the same
     * letters, but the seed is the attempt, so a reload shows the same order the
     * applicant was already looking at.
     */
    public function shuffle_for_attempt(array $items, int $seed): array
    {
        $keys = array_keys($items);
        $ordered = [];

        foreach ($keys as $i => $key) {
            $ordered[] = [
                'key' => $key,
                'rank' => crc32($seed . ':' . $i . ':' . (is_scalar($items[$key]) ? $items[$key] : $i)),
            ];
        }

        usort($ordered, static function ($a, $b) {
            return $a['rank'] <=> $b['rank'];
        });

        $out = [];
        foreach ($ordered as $entry) {
            $out[] = $items[$entry['key']];
        }

        return $out;
    }
}
