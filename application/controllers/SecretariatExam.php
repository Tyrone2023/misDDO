<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Exam Builder for the Secretariat, ported from the SRMS College
 * Assessment Suite (AssessmentSuite/create).
 *
 * The college version binds an assessment to a class offering. Recruitment has
 * no classes, so an exam here belongs to a vacancy: every screen picks from - and
 * renders - the vacancies assigned to the signed-in Secretariat account, and
 * nothing outside that assignment can be read or written.
 *
 * The question bank, its six question types and the JSON the builder posts are
 * the college shapes unchanged, so a bank exported from one reads in the other.
 */
class SecretariatExam extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'form']);
        $this->load->model('Secretariat_model', 'secretariat');
        $this->load->model('ExamBuilder_model', 'exams');
        $this->load->model('ExamTaking_model', 'taking');
        $this->load->model('Audit_model', 'Audit');
        $this->load->library('GiftAssessmentParser');
    }

    private function guard(): void
    {
        if ($this->session->userdata('position') !== 'Secretariat') {
            // AJAX requests (fetch with X-Requested-With) get JSON so the caller
            // can show a clean error instead of receiving an HTML error page.
            // json() exits on its own; the show_error path still needs exit.
            if (strtolower((string) $this->input->get_request_header('X-Requested-With')) === 'xmlhttprequest') {
                $this->json(false, 'Your session has expired. Reload the page and sign in again.', 403);
            }
            show_error('Only Secretariat users can build exams.', 403, 'Forbidden');
            exit;
        }
    }

    private function user_id(): int
    {
        return (int) ($this->session->id ?? $this->session->userdata('id'));
    }

    private function require_post(): void
    {
        if (strtoupper((string) $this->input->method()) !== 'POST') {
            show_error('Method Not Allowed', 405);
            exit;
        }
    }

    /**
     * The exam plus its vacancy, but only when that vacancy is assigned to the
     * signed-in account. Every id-taking action funnels through here, so a
     * guessed exam_id from another Secretariat's vacancy reads as not found.
     */
    private function owned_exam(int $examId): ?object
    {
        $exam = $this->exams->get_exam($examId);

        if (!$exam || !$this->secretariat->secretariat_has_vacancy($this->user_id(), (int) $exam->job_id)) {
            return null;
        }

        return $exam;
    }

    public function index(): void
    {
        $this->guard();

        $userId = $this->user_id();
        $jobId = (int) $this->input->get('job_id');
        $vacancies = $this->secretariat->tagging_vacancies($userId);

        $selectedVacancy = null;
        foreach ($vacancies as $vacancy) {
            if ((int) $vacancy->jobID === $jobId) {
                $selectedVacancy = $vacancy;
                break;
            }
        }

        if ($jobId > 0 && !$selectedVacancy) {
            $this->session->set_flashdata('danger', 'That vacancy is not assigned to your Secretariat account.');
            redirect(base_url('secretariat/exams'));
            return;
        }

        // A chosen vacancy narrows the page to that one group; the heading is
        // kept so the empty state still says which vacancy it is empty for.
        $scope = $selectedVacancy ? [$selectedVacancy] : $vacancies;
        $grouped = $this->exams->grouped_for_vacancies($scope);

        $totals = ['exams' => 0, 'published' => 0, 'draft' => 0, 'questions' => 0, 'points' => 0.0];
        foreach ($grouped as $group) {
            foreach ($group['exams'] as $exam) {
                $totals['exams']++;
                $totals[(string) $exam->status === 'draft' ? 'draft' : 'published']++;
                $totals['questions'] += (int) $exam->question_count;
                $totals['points'] += (float) $exam->total_points;
            }
        }

        $this->render('secretariat_exam_list', [
            'title' => 'Exam Builder',
            'vacancies' => $vacancies,
            'grouped' => $grouped,
            'selectedVacancy' => $selectedVacancy,
            'selectedJobId' => $jobId,
            'totals' => $totals,
        ]);
    }

    public function create(): void
    {
        $this->guard();

        $vacancies = $this->secretariat->tagging_vacancies($this->user_id());

        if (empty($vacancies)) {
            $this->session->set_flashdata('danger', 'No open vacancy is assigned to your account, so there is nothing to build an exam for.');
            redirect(base_url('secretariat/exams'));
            return;
        }

        $this->render('secretariat_exam_form', [
            'title' => 'New Exam',
            'mode' => 'create',
            'vacancies' => $vacancies,
            'exam' => null,
            'builderQuestions' => [],
            'selectedJobId' => (int) $this->input->get('job_id'),
            'old' => (array) $this->session->flashdata('exam_form_old'),
        ]);
    }

    /**
     * Preview a GIFT / XML question bank before saving.
     *
     * Ported from AssessmentSuite::preview_import() so both builders accept the
     * same files. Returns the parsed questions as JSON for the builder to render.
     */
    public function preview_import(): void
    {
        $this->guard();

        if (strtoupper((string) $this->input->method()) !== 'POST') {
            $this->json(false, 'Question bank preview requires a POST request.', 405);
            return;
        }

        $sourceName = 'pasted_import';
        $source = trim((string) $this->input->post('gift_text'));
        $upload = $_FILES['gift_file'] ?? null;

        if (is_array($upload) && (int) ($upload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            if ((int) ($upload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                $this->json(false, 'The question bank file could not be uploaded for preview.', 422);
                return;
            }

            $sourceName = basename((string) ($upload['name'] ?? 'uploaded_import'));
            $extension = strtolower((string) pathinfo($sourceName, PATHINFO_EXTENSION));
            if (!in_array($extension, ['txt', 'gift', 'xml'], true)) {
                $this->json(false, 'Upload a .txt, .gift, or .xml question bank file.', 422);
                return;
            }

            if ((int) ($upload['size'] ?? 0) > 5 * 1024 * 1024) {
                $this->json(false, 'The question bank file must not exceed 5 MB.', 422);
                return;
            }

            $tmpName = (string) ($upload['tmp_name'] ?? '');
            if ($tmpName === '' || !is_uploaded_file($tmpName)) {
                $this->json(false, 'The uploaded question bank file is invalid.', 422);
                return;
            }

            $source = (string) file_get_contents($tmpName);
        }

        if (trim($source) === '') {
            $this->json(false, 'Choose a question bank file or paste GIFT / XML content first.', 422);
            return;
        }

        // Suppress warnings during parsing so development-mode display_errors
        // cannot inject PHP notices into the JSON response.
        $prevDisplay = ini_set('display_errors', '0');
        $parsed = $this->giftassessmentparser->parse($source, $sourceName);
        if ($prevDisplay !== false) {
            ini_set('display_errors', $prevDisplay);
        }
        if (empty($parsed['questions'])) {
            $this->json(false, 'No valid questions were found in this import.', 422, [
                'warnings' => array_values((array) ($parsed['warnings'] ?? [])),
            ]);
            return;
        }

        $this->json(true, '', 200, [
            'source_name' => $sourceName,
            'source' => $source,
            'question_count' => count($parsed['questions']),
            'questions' => array_values($parsed['questions']),
            'warnings' => array_values((array) ($parsed['warnings'] ?? [])),
        ]);
    }

    private function json(bool $ok, string $message, int $status, array $extra = []): void
    {
        // Discard anything already written (PHP warnings/notices from the
        // development environment would otherwise corrupt the JSON body).
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        $payload = array_merge(['ok' => $ok, 'message' => $message], $extra);
        $this->output
            ->set_status_header($status)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

    public function store(): void
    {
        $this->guard();
        $this->require_post();

        $vacancies = $this->secretariat->tagging_vacancies($this->user_id());
        $jobId = (int) $this->input->post('job_id');

        $vacancy = null;
        foreach ($vacancies as $candidate) {
            if ((int) $candidate->jobID === $jobId) {
                $vacancy = $candidate;
                break;
            }
        }

        if (!$vacancy) {
            $this->fail('Choose one of the vacancies assigned to your account.', base_url('secretariat/exams/create'));
            return;
        }

        try {
            $payload = $this->collect_payload($vacancy);
            $questions = $this->collect_questions();
        } catch (RuntimeException $e) {
            $this->fail($e->getMessage(), base_url('secretariat/exams/create'));
            return;
        }

        $payload['created_by'] = $this->user_id();
        $payload['created_by_username'] = (string) $this->session->userdata('username');

        $examId = $this->exams->create_exam($payload, $questions);

        if ($examId <= 0) {
            $this->fail('The exam could not be saved. Please try again.', base_url('secretariat/exams/create'));
            return;
        }

        $this->audit($jobId, $examId, 'Created exam "' . $payload['title'] . '" with '
            . count($questions) . ' question' . (count($questions) === 1 ? '' : 's')
            . ' (' . number_format($payload['total_points_preview'], 2) . ' points) for '
            . $vacancy->jobTitle . '.', 'exam_create');

        $this->session->set_flashdata('success', 'Exam created with ' . count($questions)
            . ' question' . (count($questions) === 1 ? '' : 's') . '.');
        redirect(base_url('secretariat/exams/' . $examId));
    }

    public function show(int $examId = 0): void
    {
        $this->guard();

        $exam = $this->owned_exam($examId);

        if (!$exam) {
            $this->session->set_flashdata('danger', 'That exam is not available under your assigned vacancies.');
            redirect(base_url('secretariat/exams'));
            return;
        }

        $this->render('secretariat_exam_show', [
            'title' => $exam->title,
            'exam' => $exam,
            'questions' => $this->exams->get_questions($examId),
            'omrAttempts' => (string) ($exam->delivery_mode ?? 'online') === 'omr'
                ? $this->taking->omr_attempts_for_exam($examId)
                : [],
        ]);
    }

    public function edit(int $examId = 0): void
    {
        $this->guard();

        $exam = $this->owned_exam($examId);

        if (!$exam) {
            $this->session->set_flashdata('danger', 'That exam is not available under your assigned vacancies.');
            redirect(base_url('secretariat/exams'));
            return;
        }

        $this->render('secretariat_exam_form', [
            'title' => 'Edit Exam',
            'mode' => 'edit',
            'vacancies' => $this->secretariat->tagging_vacancies($this->user_id()),
            'exam' => $exam,
            'builderQuestions' => $this->exams->questions_for_builder($examId),
            'selectedJobId' => (int) $exam->job_id,
            'old' => (array) $this->session->flashdata('exam_form_old'),
        ]);
    }

    public function update(int $examId = 0): void
    {
        $this->guard();
        $this->require_post();

        $exam = $this->owned_exam($examId);

        if (!$exam) {
            $this->session->set_flashdata('danger', 'That exam is not available under your assigned vacancies.');
            redirect(base_url('secretariat/exams'));
            return;
        }

        $editUrl = base_url('secretariat/exams/' . $examId . '/edit');

        // The vacancy is fixed once an exam exists: moving it would silently
        // re-scope a saved bank to a position it was never written for.
        $vacancy = null;
        foreach ($this->secretariat->tagging_vacancies($this->user_id()) as $candidate) {
            if ((int) $candidate->jobID === (int) $exam->job_id) {
                $vacancy = $candidate;
                break;
            }
        }

        if (!$vacancy) {
            $this->fail('That vacancy is no longer assigned to your account.', base_url('secretariat/exams'));
            return;
        }

        try {
            $payload = $this->collect_payload($vacancy);
            $questions = $this->collect_questions();
        } catch (RuntimeException $e) {
            $this->fail($e->getMessage(), $editUrl);
            return;
        }

        if (!$this->exams->update_exam($examId, $payload, $questions)) {
            $this->fail('The exam could not be saved. Please try again.', $editUrl);
            return;
        }

        $this->audit((int) $exam->job_id, $examId, 'Updated exam "' . $payload['title'] . '" - '
            . count($questions) . ' question' . (count($questions) === 1 ? '' : 's') . ', '
            . number_format($payload['total_points_preview'], 2) . ' points.', 'exam_update');

        $this->session->set_flashdata('success', 'Exam saved.');
        redirect(base_url('secretariat/exams/' . $examId));
    }

    /** Printable question paper and generic, batch-ready OMR answer sheets. */
    public function omr_print(int $examId = 0): void
    {
        $this->guard();
        $exam = $this->owned_exam($examId);

        if (!$exam || (string) ($exam->delivery_mode ?? 'online') !== 'omr') {
            $this->session->set_flashdata('danger', 'That exam is not configured for OMR paper delivery.');
            redirect(base_url('secretariat/exams/' . $examId));
            return;
        }

        $this->load->view('pages/secretariat_exam_omr_print', [
            'title' => 'Print OMR Exam',
            'exam' => $exam,
            'questions' => $this->exams->get_questions($examId),
        ]);
    }

    /** Mobile-friendly capture and review screen for a completed OMR sheet. */
    public function omr_scan(int $examId = 0): void
    {
        $this->guard();
        $exam = $this->owned_exam($examId);

        if (!$exam || (string) ($exam->delivery_mode ?? 'online') !== 'omr') {
            $this->session->set_flashdata('danger', 'That exam is not configured for OMR scanning.');
            redirect(base_url('secretariat/exams/' . $examId));
            return;
        }

        $applicants = array_values(array_filter(
            $this->secretariat->applicants_for_tagging($this->user_id(), (int) $exam->job_id),
            static function ($row) { return (int) $row->dq !== 2; }
        ));
        $result = null;
        $resultId = (int) $this->input->get('result');
        if ($resultId > 0) {
            $candidate = $this->taking->get_attempt($resultId);
            if ($candidate && (int) $candidate->exam_id === $examId && (string) ($candidate->submission_source ?? '') === 'omr') {
                $result = $candidate;
            }
        }

        $this->render('secretariat_exam_omr_scan', [
            'title' => 'Scan OMR Answer Sheet',
            'exam' => $exam,
            'questions' => $this->exams->get_questions($examId),
            'applicants' => $applicants,
            'selectedAppId' => (int) $this->input->get('app_id'),
            'result' => $result,
            'omrAttempts' => $this->taking->omr_attempts_for_exam($examId),
        ]);
    }

    /** Validate reviewed bubbles, grade them, and store an ordinary exam result. */
    public function omr_submit(int $examId = 0): void
    {
        $this->guard();
        $this->require_post();
        $exam = $this->owned_exam($examId);

        if (!$exam || (string) ($exam->delivery_mode ?? 'online') !== 'omr') {
            show_error('That exam is not available for OMR submission.', 404, 'Not found');
            return;
        }

        $applicants = $this->secretariat->applicants_for_tagging($this->user_id(), (int) $exam->job_id);
        $appId = (int) $this->input->post('app_id');
        $application = $this->find_application($applicants, $appId);
        $scanUrl = base_url('secretariat/exams/' . $examId . '/omr/scan?app_id=' . $appId);

        if (!$application || (int) $application->dq === 2) {
            $this->session->set_flashdata('danger', 'Choose an eligible applicant for this vacancy.');
            redirect($scanUrl);
            return;
        }

        $questions = $this->exams->get_questions($examId);
        $decoded = json_decode((string) $this->input->post('answers_json'), true);
        if (!is_array($decoded)) {
            $this->session->set_flashdata('danger', 'The reviewed bubble answers could not be read. Scan the sheet again.');
            redirect($scanUrl);
            return;
        }

        $responses = [];
        foreach ($questions as $question) {
            $qid = (int) $question->question_id;
            $posted = $decoded[$qid] ?? $decoded[(string) $qid] ?? [];
            $valid = [];
            foreach ((array) $question->choices as $choice) {
                $valid[] = (string) (is_array($choice) ? ($choice['id'] ?? '') : $choice);
            }
            $selected = array_values(array_intersect(array_map('strval', (array) $posted), $valid));
            if ((string) $question->question_type === 'multiple_choice') {
                $responses[$qid] = array_values(array_unique($selected));
            } else {
                $responses[$qid] = $selected[0] ?? '';
            }
        }

        $application->ip_address = (string) $this->input->ip_address();
        $totals = $this->taking->record_omr_attempt($exam, $application, $questions, $responses, $this->user_id());
        if (empty($totals)) {
            $this->session->set_flashdata('danger', 'The scanned result could not be saved. Please try again.');
            redirect($scanUrl);
            return;
        }

        $this->audit((int) $exam->job_id, $examId, 'Recorded OMR result for application #'
            . $appId . ': ' . number_format((float) $totals['score'], 2) . '/'
            . number_format((float) $totals['total_points'], 2) . ' points.', 'exam_omr_scan');
        $this->session->set_flashdata('success', 'OMR result saved and graded.');
        redirect(base_url('secretariat/exams/' . $examId . '/omr/scan?app_id=' . $appId
            . '&result=' . (int) $totals['attempt_id']));
    }

    public function delete(int $examId = 0): void
    {
        $this->guard();
        $this->require_post();

        $exam = $this->owned_exam($examId);

        if (!$exam) {
            $this->session->set_flashdata('danger', 'That exam is not available under your assigned vacancies.');
            redirect(base_url('secretariat/exams'));
            return;
        }

        if (!$this->exams->delete_exam($examId)) {
            $this->session->set_flashdata('danger', 'The exam could not be deleted. Please try again.');
            redirect(base_url('secretariat/exams/' . $examId));
            return;
        }

        $this->audit((int) $exam->job_id, $examId, 'Deleted exam "' . $exam->title . '" and its question bank.', 'exam_delete');

        $this->session->set_flashdata('success', 'Exam deleted.');
        redirect(base_url('secretariat/exams?job_id=' . (int) $exam->job_id));
    }

    /**
     * Exam settings off the POST, validated.
     *
     * total_points_preview is carried alongside for the audit line only - the
     * stored total is recomputed from the question bank by the model.
     *
     * @throws RuntimeException on the first field that cannot be accepted
     */
    private function collect_payload(object $vacancy): array
    {
        $title = trim((string) $this->input->post('title', true));
        if ($title === '') {
            throw new RuntimeException('Exam title is required.');
        }

        $deliveryMode = strtolower(trim((string) $this->input->post('delivery_mode')));
        $deliveryMode = $deliveryMode === 'omr' ? 'omr' : 'online';

        $openAt = $this->normalize_datetime($this->input->post('open_at'), 'Open At');
        $closeAt = $this->normalize_datetime($this->input->post('close_at'), 'Closes At');

        if ($openAt !== null && $closeAt !== null && strtotime($closeAt) <= strtotime($openAt)) {
            throw new RuntimeException('Closes At must come after Open At.');
        }

        // An exam gated by an Open At window does not need a password: the window
        // itself is the gate, so applicants can enter directly once it opens. Only
        // an exam with no schedule requires a password as the entry point.
        $password = trim((string) $this->input->post('exam_password'));

        if ($deliveryMode === 'online' && $password === '' && $openAt === null) {
            throw new RuntimeException('A password is required - applicants key it in to enter the exam.');
        }

        return [
            'job_id' => (int) $vacancy->jobID,
            'job_title' => (string) $vacancy->jobTitle,
            'position_group' => (int) $vacancy->position,
            'sy' => (string) $vacancy->sy,
            'title' => $title,
            'delivery_mode' => $deliveryMode,
            // Recruitment runs one kind of exam, so no type is posted; the model
            // stamps the column with its single value.
            'instructions' => trim((string) $this->input->post('instructions')),
            'attempt_limit' => $this->normalize_attempt_limit($this->input->post('attempt_limit')),
            'passing_score' => $this->normalize_passing_score($this->input->post('passing_score')),
            'time_limit_minutes' => $this->normalize_time_limit(
                $this->input->post('time_limit_minutes'),
                $this->input->post('time_limit_minutes_custom')
            ),
            'open_at' => $openAt,
            'close_at' => $closeAt,
            'password' => $password,
            'status' => trim((string) $this->input->post('status', true)),
            'total_points_preview' => $this->posted_points_total(),
        ];
    }

    /**
     * The posted question bank, decoded and sanitized.
     *
     * Ported from AssessmentSuite::sanitize_manual_questions() so both builders
     * accept and reject the same banks.
     *
     * @throws RuntimeException on the first question that cannot be accepted
     */
    private function collect_questions(): array
    {
        $raw = (string) $this->input->post('questions_json');
        $decoded = json_decode($raw, true);

        if (!is_array($decoded) || empty($decoded)) {
            throw new RuntimeException('Add at least one question before saving this exam.');
        }

        $allowed = array_keys($this->exams->question_types());
        $out = [];

        foreach ($decoded as $idx => $row) {
            if (!is_array($row)) {
                continue;
            }

            $type = trim((string) ($row['question_type'] ?? ''));
            if (!in_array($type, $allowed, true)) {
                throw new RuntimeException('Unsupported question type at item ' . ($idx + 1) . '.');
            }

            $prompt = trim((string) ($row['prompt'] ?? ''));
            if ($prompt === '') {
                throw new RuntimeException('Question ' . ($idx + 1) . ' is missing its prompt.');
            }

            $question = [
                'question_name' => trim((string) ($row['question_name'] ?? '')),
                'question_type' => $type,
                'prompt' => $prompt,
                'points' => max(0, (float) ($row['points'] ?? 1)),
                'choices' => [],
                'answer_key' => [],
                'metadata' => [],
            ];

            if ($type === 'single_choice' || $type === 'multiple_choice') {
                $choices = [];
                foreach ((array) ($row['choices'] ?? []) as $i => $choice) {
                    $text = trim((string) (is_array($choice) ? ($choice['text'] ?? '') : $choice));
                    if ($text === '') {
                        continue;
                    }
                    $id = trim((string) (is_array($choice) ? ($choice['id'] ?? '') : ''));
                    if ($id === '') {
                        $id = 'choice_' . ($i + 1);
                    }
                    $choices[] = ['id' => $id, 'text' => $text];
                }

                if (count($choices) < 2) {
                    throw new RuntimeException('Question ' . ($idx + 1) . ' needs at least two choices.');
                }

                $validIds = array_column($choices, 'id');
                $answerKey = array_values(array_filter(
                    (array) ($row['answer_key'] ?? []),
                    static function ($choiceId) use ($validIds) {
                        return in_array((string) $choiceId, $validIds, true);
                    }
                ));

                if ($type === 'single_choice' && count($answerKey) !== 1) {
                    throw new RuntimeException('Question ' . ($idx + 1) . ' needs exactly one correct choice.');
                }
                if ($type === 'multiple_choice' && empty($answerKey)) {
                    throw new RuntimeException('Question ' . ($idx + 1) . ' needs at least one correct choice.');
                }

                $question['choices'] = $choices;
                $question['answer_key'] = $answerKey;
                $question['metadata'] = ['correct_count' => count($answerKey)];
            } elseif ($type === 'true_false') {
                $answer = strtolower(trim((string) ($row['answer_key'][0] ?? '')));
                if (!in_array($answer, ['true', 'false'], true)) {
                    throw new RuntimeException('Question ' . ($idx + 1) . ' must mark True or False as the answer.');
                }
                $question['choices'] = [
                    ['id' => 'true', 'text' => 'True'],
                    ['id' => 'false', 'text' => 'False'],
                ];
                $question['answer_key'] = [$answer];
            } elseif ($type === 'short_answer') {
                $answers = [];
                foreach ((array) ($row['answer_key'] ?? []) as $answer) {
                    $answer = trim((string) $answer);
                    if ($answer !== '') {
                        $answers[] = $answer;
                    }
                }
                if (empty($answers)) {
                    throw new RuntimeException('Question ' . ($idx + 1) . ' needs at least one accepted answer.');
                }
                $question['answer_key'] = array_values(array_unique($answers));
            } elseif ($type === 'matching') {
                $pairs = [];
                foreach ((array) ($row['answer_key'] ?? []) as $left => $right) {
                    $left = trim((string) $left);
                    $right = trim((string) $right);
                    if ($left !== '' && $right !== '') {
                        $pairs[$left] = $right;
                    }
                }
                if (count($pairs) < 2) {
                    throw new RuntimeException('Question ' . ($idx + 1) . ' needs at least two matching pairs.');
                }
                $question['answer_key'] = $pairs;
                $question['choices'] = array_values(array_unique(array_values($pairs)));
                $question['metadata'] = ['pairs' => count($pairs)];
            } elseif ($type === 'essay') {
                $question['metadata'] = ['manual_grading' => true];
            }

            $out[] = $question;
        }

        if (empty($out)) {
            throw new RuntimeException('Add at least one question before saving this exam.');
        }

        if (strtolower(trim((string) $this->input->post('delivery_mode'))) === 'omr') {
            foreach ($out as $idx => $question) {
                if (!in_array($question['question_type'], ['single_choice', 'multiple_choice', 'true_false'], true)) {
                    throw new RuntimeException('OMR question ' . ($idx + 1)
                        . ' must be Single Choice, Multiple Choice, or True / False.');
                }
                if (count((array) $question['choices']) > 6) {
                    throw new RuntimeException('OMR question ' . ($idx + 1) . ' has more than six choices.');
                }
            }
        }

        return $out;
    }

    private function posted_points_total(): float
    {
        $decoded = json_decode((string) $this->input->post('questions_json'), true);
        $total = 0.0;

        foreach ((array) $decoded as $row) {
            if (is_array($row)) {
                $total += (float) ($row['points'] ?? 0);
            }
        }

        return $total;
    }

    private function normalize_attempt_limit($value): int
    {
        $value = trim((string) $value);

        if ($value === '' || strtolower($value) === 'unlimited') {
            return 0;
        }

        return max(0, (int) $value);
    }

    /**
     * @throws RuntimeException
     */
    private function normalize_passing_score($value): ?float
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        if (!is_numeric($value) || (float) $value < 0) {
            throw new RuntimeException('Passing score must be a number of 0 or more.');
        }

        $passing = round((float) $value, 2);
        $available = round($this->posted_points_total(), 2);

        if ($available > 0 && $passing > $available) {
            throw new RuntimeException('Passing score cannot exceed the exam total of '
                . number_format($available, 2) . ' points.');
        }

        return $passing;
    }

    private function normalize_time_limit($dropdownValue, $customValue): ?int
    {
        $dropdownValue = trim((string) $dropdownValue);

        if ($dropdownValue === 'custom') {
            $customValue = trim((string) $customValue);
            return ($customValue !== '' && (int) $customValue > 0) ? (int) $customValue : null;
        }

        return ((int) $dropdownValue > 0) ? (int) $dropdownValue : null;
    }

    /**
     * @throws RuntimeException
     */
    private function normalize_datetime($value, string $label): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        // The form posts datetime-local, which uses a T separator.
        $stamp = strtotime(str_replace('T', ' ', $value));

        if ($stamp === false) {
            throw new RuntimeException($label . ' is not a valid date and time.');
        }

        return date('Y-m-d H:i:s', $stamp);
    }

    /**
     * Bounce back to the form with the message and everything that was typed, so
     * a rejected question bank is never lost to a redirect.
     */
    private function fail(string $message, string $redirectTo): void
    {
        $this->session->set_flashdata('danger', $message);
        $this->session->set_flashdata('exam_form_old', [
            'job_id' => (int) $this->input->post('job_id'),
            'title' => (string) $this->input->post('title', true),
            'delivery_mode' => (string) $this->input->post('delivery_mode'),
            'status' => (string) $this->input->post('status', true),
            'instructions' => (string) $this->input->post('instructions'),
            'exam_password' => (string) $this->input->post('exam_password'),
            'attempt_limit' => (string) $this->input->post('attempt_limit'),
            'passing_score' => (string) $this->input->post('passing_score'),
            'time_limit_minutes' => (string) $this->input->post('time_limit_minutes'),
            'time_limit_minutes_custom' => (string) $this->input->post('time_limit_minutes_custom'),
            'open_at' => (string) $this->input->post('open_at'),
            'close_at' => (string) $this->input->post('close_at'),
            'questions_json' => (string) $this->input->post('questions_json'),
        ]);

        redirect($redirectTo);
    }

    private function find_application(array $rows, int $appId): ?object
    {
        foreach ($rows as $row) {
            if ((int) $row->appID === $appId) {
                return $row;
            }
        }
        return null;
    }

    private function audit(int $jobId, int $examId, string $description, string $action): void
    {
        $this->Audit->log($action, [
            'entity_type' => 'exam',
            'entity_id' => $examId,
            'job_id' => $jobId,
            'field' => 'exam_builder',
            'description' => $description,
        ]);
    }

    private function render(string $page, array $data): void
    {
        $this->load->view('templates/head');
        $this->load->view('templates/header');
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer');
    }
}
