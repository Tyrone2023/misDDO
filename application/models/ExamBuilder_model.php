<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Exam Builder storage, ported from the SRMS College Assessment Suite.
 *
 * The college build binds an assessment to a class offering (subject + section).
 * Recruitment has no classes, so an exam here belongs to a *vacancy*: the
 * Secretariat builds one exam per position they are assigned to, and every
 * screen renders that vacancy instead of a subject.
 *
 * jobTitle / position / sy are snapshotted onto the exam row on top of job_id.
 * A vacancy that is later archived or renamed would otherwise leave the exam
 * list with blank headings, and an exam is a record of what was administered at
 * the time - not of the vacancy's current wording. Live values are still joined
 * for the vacancies that are present.
 *
 * ensure_tables() runs from the constructor so the tables appear on first use
 * without a migration step, the same way Secretariat_model bootstraps its own.
 */
class ExamBuilder_model extends CI_Model
{
    private $tableExams = 'hris_exams';
    private $tableQuestions = 'hris_exam_questions';

    /** Question types the builder can store, mirroring the college editor. */
    private $questionTypes = [
        'single_choice' => 'Single Choice',
        'multiple_choice' => 'Multiple Choice',
        'true_false' => 'True / False',
        'short_answer' => 'Short Answer',
        'matching' => 'Matching',
        'essay' => 'Essay',
    ];

    /**
     * Recruitment administers one kind of exam, so the column is not a choice the
     * Secretariat makes - it is stamped. Kept as a column rather than dropped so a
     * second kind can be introduced without a migration.
     */
    const EXAM_TYPE = 'written_exam';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->ensure_tables();
    }

    public function ensure_tables(): void
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS {$this->tableExams} (
                exam_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                job_id INT UNSIGNED NOT NULL,
                job_title VARCHAR(255) NOT NULL,
                position_group TINYINT UNSIGNED NOT NULL DEFAULT 0,
                sy VARCHAR(20) NULL,
                created_by INT UNSIGNED NULL,
                created_by_username VARCHAR(100) NULL,
                title VARCHAR(255) NOT NULL,
                exam_type VARCHAR(30) NOT NULL DEFAULT 'written_exam',
                delivery_mode VARCHAR(20) NOT NULL DEFAULT 'online',
                instructions TEXT NULL,
                total_points DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                question_count INT UNSIGNED NOT NULL DEFAULT 0,
                attempt_limit INT NOT NULL DEFAULT 1,
                passing_score DECIMAL(8,2) NULL,
                time_limit_minutes INT NULL,
                open_at DATETIME NULL,
                close_at DATETIME NULL,
                password_hash VARCHAR(255) NULL,
                password_plain VARCHAR(255) NULL,
                exam_code VARCHAR(40) NOT NULL,
                status VARCHAR(20) NOT NULL DEFAULT 'published',
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                UNIQUE KEY uniq_exam_code (exam_code),
                KEY idx_job (job_id),
                KEY idx_status (status),
                KEY idx_creator (created_by)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Existing installations pre-date printable OMR delivery. Keep the
        // bootstrap migration additive so every saved exam remains online by
        // default and no manual SQL step is required during deployment.
        if (!$this->db->field_exists('delivery_mode', $this->tableExams)) {
            $this->db->query("ALTER TABLE {$this->tableExams}
                ADD COLUMN delivery_mode VARCHAR(20) NOT NULL DEFAULT 'online' AFTER exam_type");
        }

        $this->db->query("
            CREATE TABLE IF NOT EXISTS {$this->tableQuestions} (
                question_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                exam_id INT UNSIGNED NOT NULL,
                question_order INT NOT NULL DEFAULT 0,
                question_name VARCHAR(255) NULL,
                question_type VARCHAR(30) NOT NULL,
                prompt LONGTEXT NOT NULL,
                points DECIMAL(10,2) NOT NULL DEFAULT 1.00,
                choices_json LONGTEXT NULL,
                answer_key_json LONGTEXT NULL,
                metadata_json LONGTEXT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                KEY idx_exam_order (exam_id, question_order)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    }

    public function question_types(): array
    {
        return $this->questionTypes;
    }

    public function question_type_label(string $type): string
    {
        return $this->questionTypes[$type] ?? ucwords(str_replace('_', ' ', $type));
    }

    /**
     * Exams for the vacancies handed in, newest first, grouped by vacancy.
     *
     * Grouping is built off the caller's vacancy list rather than off the exam
     * rows so an assigned vacancy with no exam yet still gets a heading - that
     * empty group is the affordance for building its first exam.
     *
     * @param array $vacancies rows from Secretariat_model::tagging_vacancies()
     */
    public function grouped_for_vacancies(array $vacancies): array
    {
        $jobIds = [];
        foreach ($vacancies as $vacancy) {
            $jobIds[] = (int) $vacancy->jobID;
        }

        $exams = $this->list_for_jobs($jobIds);
        $byJob = [];
        foreach ($exams as $exam) {
            $byJob[(int) $exam->job_id][] = $exam;
        }

        $grouped = [];
        foreach ($vacancies as $vacancy) {
            $jobId = (int) $vacancy->jobID;
            $grouped[] = [
                'vacancy' => $vacancy,
                'exams' => $byJob[$jobId] ?? [],
            ];
        }

        return $grouped;
    }

    public function list_for_jobs(array $jobIds): array
    {
        $jobIds = array_values(array_unique(array_filter(array_map('intval', $jobIds))));
        if (empty($jobIds)) {
            return [];
        }

        return $this->db
            ->select('e.*, j.jobTitle AS vacancy_title, j.position AS vacancy_position,
                j.sy AS vacancy_sy, j.itemNo AS vacancy_item_no, j.jvStatus AS vacancy_status', false)
            ->from($this->tableExams . ' e')
            ->join('hris_jobvacancy j', 'j.jobID = e.job_id', 'left')
            ->where_in('e.job_id', $jobIds)
            ->order_by('e.created_at', 'desc')
            ->order_by('e.exam_id', 'desc')
            ->get()
            ->result();
    }

    /**
     * Exam / question / point tallies per vacancy, for the dashboard badges.
     *
     * @return array keyed by job_id
     */
    public function counts_for_jobs(array $jobIds): array
    {
        $jobIds = array_values(array_unique(array_filter(array_map('intval', $jobIds))));
        if (empty($jobIds)) {
            return [];
        }

        $rows = $this->db
            ->select("job_id,
                COUNT(*) AS total,
                SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) AS published,
                SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) AS draft,
                SUM(question_count) AS questions,
                SUM(total_points) AS points", false)
            ->from($this->tableExams)
            ->where_in('job_id', $jobIds)
            ->group_by('job_id')
            ->get()
            ->result();

        $counts = [];
        foreach ($rows as $row) {
            $counts[(int) $row->job_id] = [
                'total' => (int) $row->total,
                'published' => (int) $row->published,
                'draft' => (int) $row->draft,
                'questions' => (int) $row->questions,
                'points' => (float) $row->points,
            ];
        }

        return $counts;
    }

    /**
     * Published exams for a vacancy, for the applicant side.
     *
     * Draft exams are excluded outright. An exam whose window has not opened or has
     * already closed IS returned, so the applicant is told when it opens or that it
     * closed, rather than being shown nothing and left guessing.
     */
    public function published_exams_for_job(int $jobId): array
    {
        if ($jobId <= 0) {
            return [];
        }

        return $this->db
            ->from($this->tableExams)
            ->where('job_id', $jobId)
            ->where('status', 'published')
            ->where('delivery_mode', 'online')
            ->order_by('created_at', 'asc')
            ->order_by('exam_id', 'asc')
            ->get()
            ->result();
    }

    /**
     * How many published exams each of these vacancies has, keyed by job_id, so the
     * applicant's application list can show a button only where one exists.
     */
    public function published_counts_for_jobs(array $jobIds): array
    {
        $jobIds = array_values(array_unique(array_filter(array_map('intval', $jobIds))));
        if (empty($jobIds)) {
            return [];
        }

        $rows = $this->db
            ->select('job_id, COUNT(*) AS total', false)
            ->from($this->tableExams)
            ->where_in('job_id', $jobIds)
            ->where('status', 'published')
            ->where('delivery_mode', 'online')
            ->group_by('job_id')
            ->get()
            ->result();

        $counts = [];
        foreach ($rows as $row) {
            $counts[(int) $row->job_id] = (int) $row->total;
        }

        return $counts;
    }

    /**
     * Whether an exam is open for taking right now, and why not when it is not.
     *
     * @return array{open:bool,reason:string,message:string}
     */
    public function availability(object $exam): array
    {
        if ((string) $exam->status !== 'published') {
            return ['open' => false, 'reason' => 'draft', 'message' => 'This exam has not been released yet.'];
        }

        $now = time();

        if (!empty($exam->open_at) && $now < strtotime((string) $exam->open_at)) {
            return [
                'open' => false,
                'reason' => 'scheduled',
                'message' => 'This exam opens on ' . date('M j, Y \a\t g:i A', strtotime((string) $exam->open_at)) . '.',
            ];
        }

        if (!empty($exam->close_at) && $now > strtotime((string) $exam->close_at)) {
            return [
                'open' => false,
                'reason' => 'closed',
                'message' => 'This exam closed on ' . date('M j, Y \a\t g:i A', strtotime((string) $exam->close_at)) . '.',
            ];
        }

        if ((int) $exam->question_count < 1) {
            return ['open' => false, 'reason' => 'empty', 'message' => 'This exam has no questions yet.'];
        }

        return ['open' => true, 'reason' => '', 'message' => ''];
    }

    public function get_exam(int $examId): ?object
    {
        $exam = $this->db
            ->select('e.*, j.jobTitle AS vacancy_title, j.position AS vacancy_position,
                j.sy AS vacancy_sy, j.itemNo AS vacancy_item_no, j.department AS vacancy_department,
                j.jvStatus AS vacancy_status', false)
            ->from($this->tableExams . ' e')
            ->join('hris_jobvacancy j', 'j.jobID = e.job_id', 'left')
            ->where('e.exam_id', $examId)
            ->get()
            ->row();

        return $exam ?: null;
    }

    /**
     * Questions with their JSON columns decoded, ready to render or to seed the
     * builder on edit.
     */
    public function get_questions(int $examId): array
    {
        $rows = $this->db
            ->from($this->tableQuestions)
            ->where('exam_id', $examId)
            ->order_by('question_order', 'asc')
            ->order_by('question_id', 'asc')
            ->get()
            ->result();

        foreach ($rows as $row) {
            $row->choices = $this->decode_json($row->choices_json, []);
            $row->answer_key = $this->decode_json($row->answer_key_json, []);
            $row->metadata = $this->decode_json($row->metadata_json, []);
        }

        return $rows;
    }

    /**
     * Same decoded questions, shaped the way the JavaScript builder posts them,
     * so an edit reopens with exactly what was saved.
     */
    public function questions_for_builder(int $examId): array
    {
        $out = [];

        foreach ($this->get_questions($examId) as $question) {
            $out[] = [
                'question_name' => (string) $question->question_name,
                'question_type' => (string) $question->question_type,
                'prompt' => (string) $question->prompt,
                'points' => (float) $question->points,
                'choices' => $question->choices,
                'answer_key' => $question->answer_key,
            ];
        }

        return $out;
    }

    public function create_exam(array $payload, array $questions): int
    {
        $now = date('Y-m-d H:i:s');

        $this->db->trans_begin();

        $row = $this->exam_row($payload, $questions);
        $row['exam_code'] = $this->generate_exam_code();
        $row['created_by'] = isset($payload['created_by']) ? (int) $payload['created_by'] : null;
        $row['created_by_username'] = trim((string) ($payload['created_by_username'] ?? ''));
        $row['created_at'] = $now;
        $row['updated_at'] = $now;

        $this->db->insert($this->tableExams, $row);
        $examId = (int) $this->db->insert_id();

        if ($examId > 0) {
            $this->insert_questions($examId, $questions, $now);
        }

        if ($this->db->trans_status() === false || $examId <= 0) {
            $this->db->trans_rollback();
            return 0;
        }

        $this->db->trans_commit();

        return $examId;
    }

    /**
     * Settings and the whole question bank are saved together.
     *
     * The college build keeps question ids stable because attempt answers point
     * at them. Nothing here records answers yet, so the bank is replaced wholesale
     * - one code path for reordering, editing and removing, instead of three.
     */
    public function update_exam(int $examId, array $payload, array $questions): bool
    {
        $now = date('Y-m-d H:i:s');

        $this->db->trans_begin();

        $row = $this->exam_row($payload, $questions);
        $row['updated_at'] = $now;

        $this->db->where('exam_id', $examId)->update($this->tableExams, $row);
        $this->db->where('exam_id', $examId)->delete($this->tableQuestions);
        $this->insert_questions($examId, $questions, $now);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        }

        $this->db->trans_commit();

        return true;
    }

    public function delete_exam(int $examId): bool
    {
        $this->db->trans_begin();

        $this->db->where('exam_id', $examId)->delete($this->tableQuestions);
        $this->db->where('exam_id', $examId)->delete($this->tableExams);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        }

        $this->db->trans_commit();

        return true;
    }

    /**
     * Columns shared by insert and update. total_points and question_count are
     * derived from the posted bank, never from the form, so the list totals
     * cannot drift away from the questions actually stored.
     */
    private function exam_row(array $payload, array $questions): array
    {
        $totalPoints = 0.0;
        foreach ($questions as $question) {
            $totalPoints += (float) ($question['points'] ?? 0);
        }

        $status = trim((string) ($payload['status'] ?? 'published'));
        $password = trim((string) ($payload['password'] ?? ''));

        return [
            'job_id' => (int) $payload['job_id'],
            'job_title' => trim((string) ($payload['job_title'] ?? '')),
            'position_group' => (int) ($payload['position_group'] ?? 0),
            'sy' => trim((string) ($payload['sy'] ?? '')) ?: null,
            'title' => trim((string) $payload['title']),
            'exam_type' => self::EXAM_TYPE,
            'delivery_mode' => ($payload['delivery_mode'] ?? 'online') === 'omr' ? 'omr' : 'online',
            'instructions' => trim((string) ($payload['instructions'] ?? '')),
            'total_points' => round($totalPoints, 2),
            'question_count' => count($questions),
            'attempt_limit' => max(0, (int) ($payload['attempt_limit'] ?? 1)),
            'passing_score' => isset($payload['passing_score']) && $payload['passing_score'] !== null
                ? round((float) $payload['passing_score'], 2)
                : null,
            'time_limit_minutes' => !empty($payload['time_limit_minutes']) ? (int) $payload['time_limit_minutes'] : null,
            'open_at' => !empty($payload['open_at']) ? (string) $payload['open_at'] : null,
            'close_at' => !empty($payload['close_at']) ? (string) $payload['close_at'] : null,
            'password_hash' => $password !== '' ? sha1($password) : null,
            'password_plain' => $password !== '' ? $password : null,
            'status' => in_array($status, ['draft', 'published'], true) ? $status : 'published',
        ];
    }

    private function insert_questions(int $examId, array $questions, string $now): void
    {
        foreach (array_values($questions) as $offset => $question) {
            $this->db->insert($this->tableQuestions, [
                'exam_id' => $examId,
                'question_order' => $offset + 1,
                'question_name' => trim((string) ($question['question_name'] ?? '')),
                'question_type' => trim((string) $question['question_type']),
                'prompt' => trim((string) $question['prompt']),
                'points' => round((float) ($question['points'] ?? 1), 2),
                'choices_json' => $this->encode_json($question['choices'] ?? []),
                'answer_key_json' => $this->encode_json($question['answer_key'] ?? []),
                'metadata_json' => $this->encode_json($question['metadata'] ?? []),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    private function generate_exam_code(): string
    {
        do {
            $code = strtoupper(substr(bin2hex(random_bytes(6)), 0, 8));
            $exists = $this->db->get_where($this->tableExams, ['exam_code' => $code], 1)->row();
        } while ($exists);

        return $code;
    }

    private function encode_json($value): string
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '[]';
    }

    private function decode_json(?string $value, $default)
    {
        if ($value === null || trim($value) === '') {
            return $default;
        }

        $decoded = json_decode($value, true);

        return $decoded === null ? $default : $decoded;
    }
}
