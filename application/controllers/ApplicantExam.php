<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * The applicant's side of the Exam Builder.
 *
 * Reached from the Manage column of the application list (Pages/ja). The flow is
 * gate -> take -> result:
 *
 *   gate   the exams published for the vacancy this application is for, each with
 *          a password box; the password is the entry point, so nothing starts
 *          without it
 *   take   the question paper, with the countdown when the exam is timed
 *   result the score, with no answer key - this is a hiring exam, and showing the
 *          key would hand it to the next applicant
 *
 * Every action is scoped to an application the signed-in applicant owns. The
 * application id in the URL is checked against the session on each request, so a
 * guessed id belonging to somebody else reads as not found.
 */
class ApplicantExam extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'form']);
        $this->load->model('ExamBuilder_model', 'exams');
        $this->load->model('ExamTaking_model', 'taking');
    }

    private function guard(): void
    {
        if (!in_array((string) $this->session->userdata('position'), ['reg', 'user'], true)) {
            show_error('Only applicants can take an exam.', 403, 'Forbidden');
            exit;
        }
    }

    private function require_post(): void
    {
        if (strtoupper((string) $this->input->method()) !== 'POST') {
            show_error('Method Not Allowed', 405);
            exit;
        }
    }

    private function applicant_id(): int
    {
        return (int) $this->session->userdata('c_id');
    }

    /**
     * The application row plus its vacancy, but only when it belongs to the
     * signed-in applicant. Mirrors the ownership check Pages::ma() makes.
     */
    private function owned_application(int $appId): ?object
    {
        if ($appId <= 0) {
            return null;
        }

        $app = $this->db
            ->select('a.appID, a.jobID, a.empEmail, a.applicant_id, a.app_year, a.appStatus, a.dq,
                j.jobTitle, j.position, j.sy, j.itemNo, j.jvStatus', false)
            ->from('hris_applications a')
            ->join('hris_jobvacancy j', 'j.jobID = a.jobID', 'left')
            ->where('a.appID', $appId)
            ->get()
            ->row();

        if (!$app || (int) $app->applicant_id !== $this->applicant_id()) {
            return null;
        }

        return $app;
    }

    private function gate_url(int $appId): string
    {
        return base_url('applicant/exam/' . $appId);
    }

    /**
     * A disqualified application is out of the process, so its exams are closed to
     * it. Everything else - submitted, validated, endorsed, rated - can still sit
     * an exam, because the exam can be administered at any of those stages.
     */
    private function application_blocked(object $app): string
    {
        if ((int) $app->dq === 2) {
            return 'This application has been disqualified, so its exams are closed.';
        }

        return '';
    }

    public function index(int $appId = 0): void
    {
        $this->guard();

        $app = $this->owned_application($appId);

        if (!$app) {
            show_error('That application is not available under your account.', 404, 'Not found');
            return;
        }

        $exams = $this->exams->published_exams_for_job((int) $app->jobID);
        $examIds = [];
        foreach ($exams as $exam) {
            $examIds[] = (int) $exam->exam_id;
        }

        $availability = [];
        foreach ($exams as $exam) {
            $availability[(int) $exam->exam_id] = $this->exams->availability($exam);
        }

        $this->render('applicant_exam_gate', [
            'title' => 'Examination',
            'app' => $app,
            'exams' => $exams,
            'availability' => $availability,
            'attempts' => $this->taking->attempt_summary($examIds, (int) $app->appID),
            'blocked' => $this->application_blocked($app),
        ]);
    }

    /**
     * Check the password and open (or resume) an attempt.
     *
     * Resuming deliberately asks for the password again: the applicant may have
     * closed the tab on a shared machine, and the attempt's clock keeps running
     * either way, so re-entry costs them nothing they still had.
     */
    public function enter(int $appId = 0): void
    {
        $this->guard();
        $this->require_post();

        $app = $this->owned_application($appId);

        if (!$app) {
            show_error('That application is not available under your account.', 404, 'Not found');
            return;
        }

        $blocked = $this->application_blocked($app);
        if ($blocked !== '') {
            $this->fail($blocked, $appId);
            return;
        }

        $examId = (int) $this->input->post('exam_id');
        $exam = $this->exams->get_exam($examId);

        if (!$exam || (int) $exam->job_id !== (int) $app->jobID) {
            $this->fail('That exam is not part of this application.', $appId);
            return;
        }

        $availability = $this->exams->availability($exam);
        if (!$availability['open']) {
            $this->fail($availability['message'], $appId);
            return;
        }

        $password = trim((string) $this->input->post('exam_password'));
        if ($password === '') {
            $this->fail('Enter the exam password to begin.', $appId);
            return;
        }

        if (sha1($password) !== (string) $exam->password_hash) {
            $this->fail('That password is not correct. Ask the Secretariat if you were not given one.', $appId);
            return;
        }

        // An attempt already open is resumed rather than duplicated, so a reload
        // cannot spend a second attempt or restart the clock.
        $attempt = $this->taking->open_attempt($examId, (int) $app->appID);

        if ($attempt && $this->taking->has_expired($attempt)) {
            $this->taking->abandon_attempt($attempt, $this->exams->get_questions($examId));
            $attempt = null;
        }

        if (!$attempt) {
            $used = count(array_filter(
                $this->taking->attempts_for($examId, (int) $app->appID),
                static function ($row) {
                    return (string) $row->status === 'submitted';
                }
            ));
            $limit = (int) $exam->attempt_limit;

            if ($limit > 0 && $used >= $limit) {
                $this->fail('You have used all ' . $limit . ' attempt' . ($limit === 1 ? '' : 's') . ' for this exam.', $appId);
                return;
            }

            $attempt = $this->taking->start_attempt([
                'exam_id' => $examId,
                'job_id' => (int) $app->jobID,
                'app_id' => (int) $app->appID,
                'applicant_id' => (int) $app->applicant_id,
                'applicant_email' => (string) $app->empEmail,
                'ip_address' => (string) $this->input->ip_address(),
            ], $exam->time_limit_minutes === null ? null : (int) $exam->time_limit_minutes);

            if (!$attempt) {
                $this->fail('The exam could not be started. Please try again.', $appId);
                return;
            }
        }

        redirect(base_url('applicant/exam/attempt/' . (int) $attempt->attempt_id));
    }

    public function take(int $attemptId = 0): void
    {
        $this->guard();

        $attempt = $this->owned_attempt($attemptId);

        if (!$attempt) {
            show_error('That exam attempt is not available under your account.', 404, 'Not found');
            return;
        }

        if ((string) $attempt->status === 'submitted') {
            redirect(base_url('applicant/exam/result/' . $attemptId));
            return;
        }

        $questions = $this->exams->get_questions((int) $attempt->exam_id);

        // Refusing to re-serve the paper once the clock has run out is what makes
        // the time limit real; the countdown in the page is only a courtesy.
        if ($this->taking->has_expired($attempt)) {
            $this->taking->abandon_attempt($attempt, $questions);
            $this->session->set_flashdata('danger', 'Your time ran out, so the exam was closed and submitted as it stood.');
            redirect(base_url('applicant/exam/result/' . $attemptId));
            return;
        }

        $this->render('applicant_exam_take', [
            'title' => (string) $attempt->exam_title,
            'attempt' => $attempt,
            'questions' => $this->prepare_questions($questions, (int) $attempt->attempt_id),
            'secondsRemaining' => $this->taking->seconds_remaining($attempt),
        ], false);
    }

    public function submit(int $attemptId = 0): void
    {
        $this->guard();
        $this->require_post();

        $attempt = $this->owned_attempt($attemptId);

        if (!$attempt) {
            show_error('That exam attempt is not available under your account.', 404, 'Not found');
            return;
        }

        if ((string) $attempt->status === 'submitted') {
            redirect(base_url('applicant/exam/result/' . $attemptId));
            return;
        }

        $questions = $this->exams->get_questions((int) $attempt->exam_id);
        $responses = [];

        foreach ((array) $this->input->post('answers') as $questionId => $value) {
            $responses[(int) $questionId] = $value;
        }

        $totals = $this->taking->submit_attempt($attempt, $questions, $responses);

        if (empty($totals)) {
            $this->session->set_flashdata('danger', 'Your answers could not be recorded. Please submit again.');
            redirect(base_url('applicant/exam/attempt/' . $attemptId));
            return;
        }

        $this->session->set_flashdata('success', 'Your exam has been submitted.');
        redirect(base_url('applicant/exam/result/' . $attemptId));
    }

    public function result(int $attemptId = 0): void
    {
        $this->guard();

        $attempt = $this->owned_attempt($attemptId);

        if (!$attempt) {
            show_error('That exam attempt is not available under your account.', 404, 'Not found');
            return;
        }

        if ((string) $attempt->status !== 'submitted') {
            redirect(base_url('applicant/exam/attempt/' . $attemptId));
            return;
        }

        $app = $this->owned_application((int) $attempt->app_id);

        $this->render('applicant_exam_result', [
            'title' => 'Exam Result',
            'attempt' => $attempt,
            'app' => $app,
        ]);
    }

    /** An attempt, but only one belonging to an application this applicant owns. */
    private function owned_attempt(int $attemptId): ?object
    {
        $attempt = $this->taking->get_attempt($attemptId);

        if (!$attempt || (int) $attempt->applicant_id !== $this->applicant_id()) {
            return null;
        }

        return $this->owned_application((int) $attempt->app_id) ? $attempt : null;
    }

    /**
     * Shape the bank for display: choices shuffled per attempt, matching options
     * pooled, and every answer key stripped before it reaches the view.
     */
    private function prepare_questions(array $questions, int $attemptId): array
    {
        $prepared = [];

        foreach ($questions as $index => $question) {
            $type = (string) $question->question_type;
            $seed = $attemptId * 1000 + (int) $question->question_id;

            $item = (object) [
                'question_id' => (int) $question->question_id,
                'number' => $index + 1,
                'question_type' => $type,
                'prompt' => (string) $question->prompt,
                'points' => (float) $question->points,
                'choices' => [],
                'pairs' => [],
                'options' => [],
            ];

            if ($type === 'single_choice' || $type === 'multiple_choice') {
                $item->choices = $this->taking->shuffle_for_attempt((array) $question->choices, $seed);
            } elseif ($type === 'true_false') {
                // Never shuffled: True then False is how the paper reads.
                $item->choices = [['id' => 'true', 'text' => 'True'], ['id' => 'false', 'text' => 'False']];
            } elseif ($type === 'matching') {
                foreach ((array) $question->answer_key as $left => $right) {
                    $item->pairs[] = (string) $left;
                }
                $options = array_values(array_unique(array_map('strval', (array) $question->answer_key)));
                $item->options = $this->taking->shuffle_for_attempt($options, $seed);
            }

            $prepared[] = $item;
        }

        return $prepared;
    }

    private function fail(string $message, int $appId): void
    {
        $this->session->set_flashdata('danger', $message);
        redirect($this->gate_url($appId));
    }

    /**
     * The exam paper itself is rendered without the app chrome: the sidebar and
     * topbar are all navigation, and navigating away mid-exam is the one thing the
     * page should not invite.
     */
    private function render(string $page, array $data, bool $withChrome = true): void
    {
        $this->load->view('templates/head');

        if ($withChrome) {
            $this->load->view('templates/header');
        }

        $this->load->view('pages/' . $page, $data);

        if ($withChrome) {
            $this->load->view('templates/footer');
        }
    }
}
