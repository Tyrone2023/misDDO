<!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->
             <?php
                $setting = $this->Common->one_cond_row('settings','id',11);

                // Adding/removing attachments closes with the vacancy and with the
                // administrator switch. Existing details remain correctable by
                // staff; only the applicant is frozen when editing is closed.
                $recordLock   = !empty($record_lock['locked']);
                $lockReason   = $recordLock ? (string) $record_lock['reason'] : '';
                $sessionRole  = trim((string) ($this->session->position ?? ''));
                $isApplicantAccount = strtolower($sessionRole) === 'reg';
                // Verifier is intentionally review-only across the application.
                $isStaffAccount = $sessionRole !== ''
                    && !$isApplicantAccount
                    && $sessionRole !== 'Verifier';

                // Two different locks, two different meanings.
                //   $recordsOpen     - may add or delete a row, i.e. may bring an
                //                      attachment in or take one out. Closed by
                //                      either the settings switch or the vacancy.
                //   $detailsEditable - staff may correct existing details at any
                //                      time; applicants may do so only while both
                //                      locks are open.
                $settingsLock    = ((int) ($setting->status ?? 0) !== 0);
                $recordsOpen     = !$settingsLock && !$recordLock;
                $detailsEditable = $isStaffAccount || ($isApplicantAccount && $recordsOpen);

                $relevanceVacancies = $relevance['vacancies'] ?? array();
                $relevanceMap       = $relevance['map'] ?? array('training' => array(), 'experience' => array());
                $relevanceRemarks   = $relevance['remarks'] ?? array('training' => array(), 'experience' => array());
                $relevanceCredited  = $relevance['credited'] ?? array('training' => array(), 'experience' => array());
                $selectedVacancy    = null;
                foreach ($relevanceVacancies as $vacancy) {
                    if (!empty($vacancy->is_context)) {
                        $selectedVacancy = $vacancy;
                        break;
                    }
                }
                // The vacancy comes from ?jobID= in the URL. "My Profile" opens
                // without one, so fall back to the latest application; the
                // switcher below changes the URL, never shows two at once.
                if ($selectedVacancy === null && !empty($relevanceVacancies)) {
                    foreach ($relevanceVacancies as $vacancy) {
                        if ($selectedVacancy === null || (int) $vacancy->appID > (int) $selectedVacancy->appID) {
                            $selectedVacancy = $vacancy;
                        }
                    }
                }
                $selectedJobId = (int) ($selectedVacancy->jobID ?? 0);
                $selectedVacancyLabel = trim((string) ($selectedVacancy->label ?? ''));
                $canRateSelectedVacancy = $selectedJobId > 0
                    && $this->Reg->can_set_relevance((int) $a_user->id, $selectedJobId);

                $relevance_label = function ($stat) {
                    if ((int) $stat === 1) return array('Relevant', 'success');
                    if ((int) $stat === 2) return array('Not Relevant', 'danger');
                    return array('No Action', 'secondary');
                };

                // Relevant first, then Not Relevant, then not yet reviewed;
                // the original order is kept inside each group.
                $sort_by_relevance = function ($rows, $type, $pk) use ($relevanceMap, $selectedJobId) {
                    if ($selectedJobId < 1 || !is_array($rows)) return $rows;
                    $rank = array(1 => 0, 2 => 1, 0 => 2);
                    $keyed = array();
                    foreach (array_values($rows) as $i => $row) {
                        $stat = (int) ($relevanceMap[$type][(int) $row->{$pk}][$selectedJobId] ?? 0);
                        $keyed[] = array($rank[$stat] ?? 2, $i, $row);
                    }
                    usort($keyed, function ($a, $b) {
                        return $a[0] === $b[0] ? $a[1] - $b[1] : $a[0] - $b[0];
                    });
                    return array_map(function ($k) { return $k[2]; }, $keyed);
                };

                $relevance_counts = function ($rows, $type, $pk) use ($relevanceMap, $selectedJobId) {
                    $counts = array(1 => 0, 2 => 0, 0 => 0);
                    foreach ((array) $rows as $row) {
                        $stat = (int) ($relevanceMap[$type][(int) $row->{$pk}][$selectedJobId] ?? 0);
                        $counts[isset($counts[$stat]) ? $stat : 0]++;
                    }
                    return $counts;
                };

                if (isset($training)) $training = $sort_by_relevance($training, 'training', 'trainingID');
                if (isset($experience)) $experience = $sort_by_relevance($experience, 'experience', 'id');

                // Evaluator Review cell: status for the URL vacancy, plus the
                // Review button for whoever may rate it.
                $review_cell = function ($type, $id, $stat, $remarks, $title, $computed = null, $credited = null) use ($relevance_label, $selectedJobId, $selectedVacancyLabel, $canRateSelectedVacancy) {
                    if ($selectedJobId < 1) return '<span class="badge badge-secondary">No vacancy selected</span>';
                    $status = $relevance_label($stat);
                    $out = '<div class="review-summary" data-review-row="' . $type . ':' . (int) $id . ':' . $selectedJobId . '">'
                        . '<div class="review-status">'
                        . '<span class="badge badge-' . $status[1] . ' review-status-badge">' . $status[0] . '</span>'
                        . '<small class="review-for" title="' . html_escape($selectedVacancyLabel) . '">for ' . html_escape($selectedVacancyLabel) . '</small>'
                        . '</div>';
                    if ($canRateSelectedVacancy) {
                        $out .= '<button type="button" class="btn btn-sm open-record-review"'
                            . ' data-toggle="modal" data-target=".record-review-modal"'
                            . ' data-record-type="' . $type . '" data-record-id="' . (int) $id . '" data-job-id="' . $selectedJobId . '"'
                            . ' data-stat="' . (int) $stat . '" data-remarks="' . html_escape(rawurlencode($remarks)) . '"'
                            . ' data-title="' . html_escape($title) . '"'
                            . ' data-computed="' . html_escape((string) $computed) . '" data-credited="' . html_escape((string) $credited) . '">'
                            . '<i class="mdi mdi-tune-variant"></i>Review</button>';
                    }
                    return $out . '</div>';
                };

                // Remark row under the record, tinted by its relevance status.
                $review_note = function ($type, $id, $stat, $remarks) use ($relevance_label, $selectedJobId, $selectedVacancyLabel) {
                    if ($selectedJobId < 1) return '';
                    $status = $relevance_label($stat);
                    return '<tr class="record-note-row' . ($remarks === '' ? ' d-none' : '') . '" data-review-note="' . $type . ':' . (int) $id . ':' . $selectedJobId . '">'
                        . '<td colspan="4"><div class="record-note tone-' . (int) $stat . '" role="note" aria-label="Evaluator remark">'
                        . '<span class="record-note-icon"><i class="mdi mdi-comment-quote-outline"></i></span>'
                        . '<div class="record-note-body"><div class="record-note-header">'
                        . '<span class="record-note-label">Evaluator Remark</span>'
                        . '<span class="record-note-status">' . $status[0] . '</span>'
                        . '<span class="record-note-vacancy">' . html_escape($selectedVacancyLabel) . '</span>'
                        . '</div><div class="record-note-text">' . html_escape($remarks) . '</div></div>'
                        . '</div></td></tr>';
                };

                $hours_text = function ($hours) {
                    return rtrim(rtrim(number_format((float) $hours, 2, '.', ''), '0'), '.');
                };

                // Evaluator-credited value under the record's own figure; kept in
                // the markup (hidden) so the review modal can fill it after a save.
                $credited_line = function ($type, $id, $text) use ($selectedJobId) {
                    if ($selectedJobId < 1) return '';
                    return '<div class="record-credited' . ($text === null ? ' d-none' : '') . '" data-credit-display="' . $type . ':' . (int) $id . ':' . $selectedJobId . '"'
                        . ' title="Credited by the evaluator for this vacancy">'
                        . '<i class="mdi mdi-account-edit-outline"></i>Credited <span class="record-credited-value">' . html_escape((string) $text) . '</span></div>';
                };

                // Switching vacancy only changes ?jobID=; appID belongs to one job.
                $vacancy_url = function ($jobId) use ($a_user) {
                    return base_url() . 'registered_profile/' . (int) $a_user->id . '?jobID=' . (int) $jobId;
                };

                // "Saved on" stamp for a record. Rows encoded before the column
                // existed carry none, so those read as a dash.
                $saved_stamp = function ($created, $updated = null) {
                    $out = '<span class="text-muted">&mdash;</span>';

                    $c = !empty($created) ? strtotime($created) : false;
                    if ($c) {
                        $out = date('M d, Y', $c) . '<br><small class="text-muted">' . date('g:i A', $c) . '</small>';
                    }

                    $u = !empty($updated) ? strtotime($updated) : false;
                    if ($u && (!$c || date('Y-m-d H:i', $u) !== date('Y-m-d H:i', $c))) {
                        $out .= '<br><small class="text-info" title="Last change to this record">edited '
                            . date('M d, Y g:i A', $u) . '</small>';
                    }

                    return $out;
                };

                // One compact line replaces the repeated vacancy and lock notices.
                $record_context_strip = function ($type = '', $counts = null, $anchor = '') use (
                    $selectedJobId,
                    $selectedVacancyLabel,
                    $relevanceVacancies,
                    $vacancy_url,
                    $recordLock,
                    $settingsLock,
                    $detailsEditable,
                    $lockReason
                ) {
                    $lockText = $recordLock ? 'Records closed' : ($settingsLock ? 'Attachments locked' : 'Attachments open');
                    $lockIcon = $recordLock ? 'mdi-lock-outline' : ($settingsLock ? 'mdi-paperclip-off' : 'mdi-paperclip');
                    $lockTitle = $recordLock && $lockReason !== '' ? ' title="' . html_escape($lockReason) . '"' : '';
                    $editText = $detailsEditable ? 'Details editable' : 'Read-only';
                    $editClass = $detailsEditable ? 'is-editable' : 'is-readonly';

                    if ($selectedJobId < 1) {
                        $vacancyHtml = '<div class="context-vacancy-name is-empty">No active vacancy</div>';
                    } elseif (count($relevanceVacancies) > 1) {
                        $options = '';
                        foreach ($relevanceVacancies as $v) {
                            $options .= '<option value="' . html_escape($vacancy_url($v->jobID) . $anchor) . '"'
                                . ((int) $v->jobID === $selectedJobId ? ' selected' : '') . '>'
                                . html_escape($v->label) . '</option>';
                        }
                        $vacancyHtml = '<select class="custom-select custom-select-sm context-vacancy-switch js-vacancy-switch" aria-label="Show relevance for vacancy">'
                            . $options . '</select>';
                    } else {
                        $vacancyHtml = '<div class="context-vacancy-name">' . html_escape($selectedVacancyLabel) . '</div>';
                    }

                    $countsHtml = '';
                    if ($selectedJobId > 0 && is_array($counts)) {
                        $countsHtml = '<div class="context-counts">'
                            . '<span class="count-pill tone-1"><i class="mdi mdi-check-circle"></i><b data-relevance-count="' . $type . ':1">' . (int) $counts[1] . '</b> Relevant</span>'
                            . '<span class="count-pill tone-2"><i class="mdi mdi-close-circle"></i><b data-relevance-count="' . $type . ':2">' . (int) $counts[2] . '</b> Not Relevant</span>'
                            . '<span class="count-pill tone-0"><i class="mdi mdi-clock-outline"></i><b data-relevance-count="' . $type . ':0">' . (int) $counts[0] . '</b> Pending</span>'
                            . '</div>';
                    }

                    return '<div class="record-context-strip" role="status">'
                        . '<div class="context-vacancy"><i class="mdi mdi-briefcase-check-outline"></i>'
                        . '<div class="context-vacancy-body"><small>Relevance for vacancy</small>' . $vacancyHtml . '</div></div>'
                        . $countsHtml
                        . '<div class="context-flags">'
                        . '<span' . $lockTitle . '><i class="mdi ' . $lockIcon . '"></i>' . $lockText . '</span>'
                        . '<span class="' . $editClass . '"><i class="mdi mdi-pencil-outline"></i>' . $editText . '</span>'
                        . '</div></div>';
                };
             ?>

            <style>
                .record-section {
                    background: #fff;
                    border: 1px solid #e7ebf0;
                    border-radius: 16px;
                    box-shadow: 0 8px 24px rgba(31, 45, 61, .06);
                    overflow: hidden;
                }
                .record-section-header {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 1rem;
                    padding: 1.15rem 1.25rem;
                    border-bottom: 1px solid #edf0f4;
                    background: linear-gradient(135deg, #f8fbff 0%, #fff 70%);
                }
                .record-section-title { margin: 0; font-size: 1.05rem; font-weight: 700; color: #263238; }
                .record-section-body { padding: 1.1rem 1.25rem 1.25rem; }
                .profile-record-table { min-width: 760px; }
                .profile-record-table th,
                .profile-record-table td { vertical-align: middle; }
                .profile-record-table thead th {
                    text-transform: uppercase;
                    font-size: .72rem;
                    letter-spacing: .04em;
                    border-top: 0;
                    white-space: nowrap;
                }
                .profile-record-table .badge { font-size: .78rem; padding: .35em .6em; }
                .profile-record-table .record-title { font-weight: 600; color: #263238; line-height: 1.35; }
                .profile-record-table .record-meta { color: #7b8794; font-size: .77rem; margin-top: .3rem; }
                .profile-record-table a.record-editable {
                    color: inherit;
                    border-bottom: 1px dashed #adb5bd;
                    text-decoration: none;
                }
                .profile-record-table a.record-editable:hover { color: #3f6ad8; border-bottom-color: #3f6ad8; }
                .record-dates { color: #405061; font-size: .84rem; line-height: 1.65; }
                .record-credit { font-size: 1rem; font-weight: 700; color: #2c5cc5; }
                .record-credited { display: inline-flex; align-items: center; gap: .25rem; margin-top: .25rem; padding: .1rem .5rem; border-radius: 999px; background: #fff4e0; color: #9a5b00; font-size: .72rem; font-weight: 700; }
                .record-file { margin-top: .45rem; }
                .record-context-strip {
                    display: flex;
                    align-items: center;
                    flex-wrap: wrap;
                    gap: .4rem .9rem;
                    padding: .55rem .75rem;
                    margin-bottom: .85rem;
                    border: 1px solid #d8e5f6;
                    border-radius: 8px;
                    background: #f7faff;
                    color: #52667d;
                    font-size: .78rem;
                }
                .record-context-strip span { display: inline-flex; align-items: center; gap: .28rem; white-space: nowrap; }
                .record-context-strip .context-vacancy { display: flex; align-items: center; gap: .55rem; color: #244b78; min-width: 0; flex: 1 1 260px; }
                .record-context-strip .context-vacancy > .mdi { font-size: 1.35rem; color: #2f75b5; }
                .context-vacancy-body { min-width: 0; flex: 1; }
                .context-vacancy-body small { display: block; color: #7a8998; font-size: .68rem; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; }
                .context-vacancy-name { color: #1f3f63; font-size: .9rem; font-weight: 700; line-height: 1.3; }
                .context-vacancy-name.is-empty { color: #8995a3; font-weight: 600; }
                .context-vacancy-switch { max-width: 420px; height: 32px; margin-top: .15rem; border-color: #c9dbf0; color: #1f3f63; font-size: .84rem; font-weight: 700; }
                .context-counts { display: flex; flex-wrap: wrap; gap: .35rem; }
                .count-pill { padding: .22rem .6rem; border-radius: 999px; font-size: .74rem; font-weight: 600; }
                .count-pill b { font-weight: 800; }
                .count-pill.tone-1 { background: #e9f7ef; color: #187a55; }
                .count-pill.tone-2 { background: #fdeef0; color: #ad3340; }
                .count-pill.tone-0 { background: #f1f3f6; color: #5f6b76; }
                .context-flags { display: flex; flex-wrap: wrap; gap: .4rem .9rem; margin-left: auto; }
                .record-context-strip .is-editable { color: #18845c; }
                .record-context-strip .is-readonly { color: #9a6700; }
                .vacancy-totals { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: .75rem; margin-top: 1rem; }
                .vacancy-total-card { border: 1px solid #e4e9f0; border-radius: 12px; padding: .9rem 1rem; background: #f8fafc; }
                .vacancy-total-label { color: #667085; font-size: .76rem; line-height: 1.25; }
                .vacancy-total-value { color: #253858; font-size: 1.15rem; font-weight: 700; margin-top: .25rem; }
                .vacancy-total-card.is-context { border-color: #8cb4ff; background: #f2f7ff; }
                .record-actions { display: flex; gap: .4rem; align-items: center; padding-top: .25rem; }
                .record-icon-button {
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    width: 32px;
                    height: 32px;
                    padding: 0;
                    border: 1px solid #dbe3ec;
                    border-radius: 9px;
                    background: #fff;
                    color: #52667d;
                    box-shadow: 0 2px 6px rgba(31, 45, 61, .06);
                    transition: all .16s ease;
                }
                .record-icon-button:hover { border-color: #8cb4e8; background: #f2f7fd; color: #2571b8; transform: translateY(-1px); box-shadow: 0 4px 10px rgba(31, 45, 61, .1); }
                .record-icon-button.is-danger:hover { border-color: #efadb3; background: #fff5f6; color: #c03946; }
                .record-readonly { display: inline-flex; align-items: center; gap: .25rem; color: #8995a3; font-size: .74rem; }
                .relevance-editor { display: flex; align-items: center; gap: .35rem; margin: 0; }
                .relevance-editor .form-control {
                    min-width: 142px;
                    height: 36px;
                    padding: .25rem 1.7rem .25rem .55rem;
                    border-width: 2px;
                    border-radius: 7px;
                    font-size: .78rem;
                    font-weight: 600;
                    cursor: pointer;
                }
                .relevance-editor .relevance-status-0 { border-color: #c8d0da; background-color: #f8f9fa; color: #5f6b76; }
                .relevance-editor .relevance-status-1 { border-color: #48b88a; background-color: #effaf5; color: #187a55; }
                .relevance-editor .relevance-status-2 { border-color: #e28b94; background-color: #fff4f5; color: #ad3340; }
                .relevance-editor.is-saving .form-control { opacity: .62; cursor: wait; }
                .relevance-save-state { display: none; color: #348cd4; font-size: .75rem; white-space: nowrap; }
                .relevance-editor.is-saving .relevance-save-state { display: inline-flex; align-items: center; gap: .2rem; }
                .evaluation-cell { min-width: 180px; }
                .review-summary { display: flex; align-items: center; flex-wrap: wrap; gap: .45rem; }
                .review-status { display: flex; flex-direction: column; align-items: flex-start; gap: .2rem; min-width: 0; }
                .review-for { max-width: 190px; overflow: hidden; color: #7a8998; font-size: .72rem; text-overflow: ellipsis; white-space: nowrap; }
                .review-status-badge { border-radius: 999px; padding: .42em .72em !important; font-weight: 600; }
                .open-record-review {
                    display: inline-flex;
                    align-items: center;
                    gap: .28rem;
                    margin-left: auto;
                    border: 1px solid #c8dbef;
                    border-radius: 8px;
                    background: #f7fbff;
                    color: #256aa5;
                    font-size: .75rem;
                    font-weight: 700;
                }
                .open-record-review:hover { border-color: #79aee0; background: #edf6ff; color: #174f82; }
                .profile-record-table .record-note-row > td { padding: .15rem .75rem .85rem; border-top: 0; background: #fff; }
                .profile-record-table .record-note-row:hover > td { background: #fff; }
                .record-note {
                    display: grid;
                    grid-template-columns: 34px minmax(0, 1fr);
                    gap: .7rem;
                    padding: .72rem .85rem;
                    border: 1px solid #dce8f4;
                    border-left: 3px solid #4c8dc7;
                    border-radius: 10px;
                    background: #f8fbff;
                    color: #34495e;
                }
                .record-note-icon { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 9px; background: #e6f1fc; color: #2f75b5; font-size: 1rem; }
                .record-note-header { display: flex; align-items: center; flex-wrap: wrap; gap: .25rem .5rem; margin-bottom: .35rem; }
                .record-note-label { color: #274b6d; font-size: .72rem; font-weight: 800; letter-spacing: .04em; text-transform: uppercase; }
                .record-note-status { padding: .1rem .5rem; border-radius: 999px; background: #eef1f5; color: #5f6b76; font-size: .7rem; font-weight: 700; }
                .record-note-vacancy { color: #7a8998; font-size: .72rem; }
                .record-note-vacancy::before { content: "\2022"; margin-right: .4rem; color: #c2ccd6; }
                .record-note-text { color: #2b3d50; font-size: .9rem; line-height: 1.6; overflow-wrap: anywhere; white-space: pre-wrap; }
                .record-note.tone-1 { border-color: #cdebdc; border-left-color: #2fa36f; background: #f6fcf9; }
                .record-note.tone-1 .record-note-icon { background: #dff4ea; color: #1d8a5b; }
                .record-note.tone-1 .record-note-status { background: #dff4ea; color: #187a55; }
                .record-note.tone-2 { border-color: #f3d3d7; border-left-color: #d0505d; background: #fff8f9; }
                .record-note.tone-2 .record-note-icon { background: #fde6e9; color: #c03946; }
                .record-note.tone-2 .record-note-status { background: #fde6e9; color: #ad3340; }
                /* Evaluator review modal */
                .record-review-modal .modal-body { display: grid; grid-template-columns: minmax(0, 1fr); gap: .75rem; }
                .record-review-modal .modal-body .review-form-section + .review-form-section { margin-top: 0; }
                @media (min-width: 992px) {
                    /* Large modal: decisions on the left, remarks beside them. */
                    .record-review-modal .modal-body { grid-template-columns: minmax(0, 1.35fr) minmax(0, 1fr); align-items: start; }
                    .record-review-modal .record-credit-grid { grid-template-columns: 124px minmax(0, 1fr); }
                    .record-review-modal .input-group-text { padding: 0 .55rem; }
                    .record-review-modal .record-remarks-editor { grid-column: 2; grid-row: 1 / span 2; display: flex; flex-direction: column; align-self: stretch; }
                    .record-review-modal .record-remarks-editor textarea.form-control { flex: 1; min-height: 220px; }
                }
                .record-review-modal.fade .modal-dialog { transform: translateY(14px) scale(.985); transition: transform .22s ease-out; }
                .record-review-modal.show .modal-dialog { transform: none; }
                .record-review-modal .modal-content { overflow: hidden; border: 0; border-radius: 18px; background: #f6f8fb; box-shadow: 0 24px 60px rgba(23, 43, 77, .22); }
                .record-review-modal .modal-header { position: relative; align-items: flex-start; padding: 1.25rem 1.35rem 1.1rem; border-bottom: 1px solid #e7edf4; background: #fff; }
                .record-review-modal .modal-header::before { content: ""; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #348cd4, #45bbe0 55%, #48b88a); }
                .record-review-modal .modal-header .close { margin: -.35rem -.5rem 0 auto; padding: .35rem .6rem; border-radius: 9px; color: #8a96a3; opacity: 1; text-shadow: none; transition: background .15s, color .15s; }
                .record-review-modal .modal-header .close:hover { background: #f1f4f8; color: #34465a; }
                .review-modal-heading { display: flex; align-items: flex-start; gap: .85rem; min-width: 0; }
                .review-modal-icon { display: inline-flex; align-items: center; justify-content: center; width: 44px; height: 44px; flex: 0 0 44px; border-radius: 13px; background: linear-gradient(135deg, #e6f1fd, #d9ecfb); color: #2470b3; font-size: 1.35rem; box-shadow: inset 0 0 0 1px rgba(52, 140, 212, .12); }
                .review-modal-heading-text { min-width: 0; }
                .review-modal-kicker { color: #348cd4; font-size: .68rem; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }
                .review-modal-title { margin: .15rem 0 .45rem; color: #1f3147; font-size: 1.05rem; font-weight: 700; line-height: 1.35; overflow-wrap: anywhere; }
                .review-vacancy-chip { display: inline-flex; align-items: center; gap: .35rem; max-width: 100%; padding: .22rem .65rem .22rem .5rem; border: 1px solid #dce9f7; border-radius: 999px; background: #f5f9fe; color: #3f5f80; font-size: .74rem; font-weight: 600; }
                .review-vacancy-chip span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
                .record-review-modal .modal-body { padding: 1rem 1.15rem; }
                .review-form-section { margin: 0; padding: 1rem 1.05rem; border: 1px solid #e6ebf1; border-radius: 14px; background: #fff; box-shadow: 0 1px 2px rgba(23, 43, 77, .04); transition: border-color .15s, box-shadow .15s; }
                .review-form-section:focus-within { border-color: #c7dcf1; box-shadow: 0 4px 14px rgba(52, 140, 212, .08); }
                .review-form-section + .review-form-section { margin-top: .75rem; }
                .review-section-head { display: flex; align-items: center; gap: .65rem; margin-bottom: .8rem; }
                .review-section-icon { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; flex: 0 0 30px; border-radius: 9px; background: #eef4fb; color: #3a7cc0; font-size: 1rem; }
                .review-section-icon.is-amber { background: #fff4e0; color: #b46d00; }
                .review-section-icon.is-violet { background: #f1edfc; color: #6a4fc4; }
                .review-section-text { flex: 1; min-width: 0; }
                .review-field-label { display: block; margin: 0; color: #24364b; font-size: .84rem; font-weight: 700; }
                .review-section-hint { display: block; color: #8592a0; font-size: .72rem; line-height: 1.35; }
                .review-character-count { color: #8a96a3; font-size: .7rem; white-space: nowrap; }
                /* Relevance: segmented control drives the (visually hidden) select */
                .record-review-modal form { margin: 0; }
                .record-review-modal .relevance-editor { display: block; }
                .review-segmented { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: .3rem; padding: .3rem; border-radius: 12px; background: #f1f4f8; }
                .review-segment { display: inline-flex; align-items: center; justify-content: center; gap: .3rem; min-height: 38px; padding: .35rem .5rem; border: 1px solid transparent; border-radius: 9px; background: transparent; color: #5f6b76; font-size: .78rem; font-weight: 600; cursor: pointer; transition: background .15s, color .15s, box-shadow .15s, border-color .15s; }
                .review-segment i { font-size: 1rem; }
                .review-segment:hover { background: rgba(255, 255, 255, .7); color: #34465a; }
                .review-segment:focus { outline: 0; box-shadow: 0 0 0 .15rem rgba(52, 140, 212, .25); }
                .review-segment.is-active { background: #fff; box-shadow: 0 1px 3px rgba(23, 43, 77, .12); }
                .review-segment[data-stat="0"].is-active { border-color: #d3dae2; color: #3f4b57; }
                .review-segment[data-stat="1"].is-active { border-color: #9fdcc0; background: #effaf5; color: #187a55; }
                .review-segment[data-stat="2"].is-active { border-color: #f0b8be; background: #fff4f5; color: #ad3340; }
                .relevance-editor.is-saving .review-segment { opacity: .6; cursor: wait; pointer-events: none; }
                .record-review-modal .relevance-save-state { display: inline-flex; align-items: center; min-height: 1rem; margin-top: .45rem; color: #348cd4; font-size: .72rem; }
                .record-review-modal .relevance-save-state:empty { display: none; }
                .record-review-modal .relevance-editor.is-saved .relevance-save-state { color: #18845c; }
                .record-review-modal .relevance-editor.has-error .relevance-save-state { color: #c03946; }
                /* Credited hours / experience */
                .record-credit-editor { margin: 0; }
                .record-credit-grid { display: grid; grid-template-columns: 150px minmax(0, 1fr); gap: .65rem; align-items: stretch; }
                .record-credit-computed { display: flex; flex-direction: column; justify-content: center; padding: .55rem .75rem; border: 1px dashed #d6dee7; border-radius: 11px; background: #f8fafc; }
                .record-credit-computed small { color: #8592a0; font-size: .66rem; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; }
                .record-credit-computed strong { color: #2c5cc5; font-size: 1.02rem; }
                .record-credit-inputs { display: flex; gap: .5rem; align-items: center; }
                .record-credit-inputs .input-group { flex: 1; min-width: 0; }
                .record-review-modal .input-group .form-control { height: 42px; border-color: #d9e2ec; border-radius: 10px 0 0 10px; font-size: .95rem; font-weight: 600; color: #24364b; }
                .record-review-modal .input-group-text { border-color: #d9e2ec; border-radius: 0 10px 10px 0; background: #f4f7fa; color: #6b7886; font-size: .75rem; font-weight: 600; }
                .record-review-modal .input-group .form-control:focus { border-color: #72aee6; box-shadow: 0 0 0 .15rem rgba(52, 140, 212, .12); }
                .record-credit-note { display: flex; gap: .35rem; margin-top: .6rem; color: #7a8795; font-size: .72rem; line-height: 1.45; }
                .record-credit-note i { color: #b46d00; }
                .record-credit-editor.is-saving .btn,
                .record-credit-editor.is-saving input { opacity: .65; cursor: wait; }
                .record-credit-editor.is-saved .credit-save-state { color: #18845c; }
                .record-credit-editor.has-error .credit-save-state { color: #c03946; }
                /* Remarks */
                .record-review-modal textarea.form-control { min-height: 116px; resize: vertical; border-color: #d9e2ec; border-radius: 11px; background: #fcfdfe; font-size: .85rem; line-height: 1.55; }
                .record-review-modal textarea.form-control:focus { border-color: #72aee6; background: #fff; box-shadow: 0 0 0 .15rem rgba(52, 140, 212, .12); }
                .record-remarks-editor { margin: 0; }
                .record-credit-actions,
                .record-remarks-actions { display: flex; align-items: center; justify-content: flex-end; gap: .5rem; padding-top: .75rem; }
                .record-review-modal .record-credit-actions .btn,
                .record-review-modal .record-remarks-actions .btn { border-radius: 9px; font-size: .8rem; font-weight: 600; }
                .remarks-save-state { color: #6c757d; font-size: .72rem; line-height: 1.25; }
                .record-remarks-editor.is-saving textarea,
                .record-remarks-editor.is-saving .btn { opacity: .65; cursor: wait; }
                .record-remarks-editor.is-saved .remarks-save-state { color: #18845c; }
                .record-review-modal .modal-footer { padding: .75rem 1.15rem; border-top: 1px solid #e7edf4; background: #fff; }
                .record-review-modal .modal-footer .btn { border-radius: 9px; font-weight: 600; }
                @media (max-width: 575.98px) {
                    .review-segment { flex-direction: column; gap: .1rem; font-size: .72rem; }
                    .record-credit-grid { grid-template-columns: 1fr; }
                    .record-credit-actions .credit-save-state { flex-basis: 100%; }
                    .record-credit-actions { flex-wrap: wrap; }
                }
                .record-remarks-editor.has-error .remarks-save-state { color: #c03946; }
                .profile-record-table .btn-lg { padding: 0; line-height: 1; }
                @media (max-width: 767.98px) {
                    .tab-content { padding-left: .75rem !important; padding-right: .75rem !important; }
                    .record-section-header { align-items: flex-start; padding: 1rem; }
                    .record-section-header .btn { width: auto; }
                    .record-section-body { padding: .8rem; }
                    .profile-record-table { min-width: 0; border-collapse: separate; border-spacing: 0 .75rem; }
                    .profile-record-table thead { display: none; }
                    .profile-record-table tbody, .profile-record-table tr, .profile-record-table td { display: block; width: 100%; }
                    .profile-record-table tbody tr { border: 1px solid #e3e8ef; border-radius: 12px; padding: .35rem .8rem; background: #fff; box-shadow: 0 4px 12px rgba(31,45,61,.05); }
                    .profile-record-table tbody td { display: grid; grid-template-columns: minmax(105px, 34%) 1fr; gap: .65rem; padding: .65rem 0; border-top: 1px solid #eef1f5; text-align: left !important; }
                    .profile-record-table tbody td:first-child { border-top: 0; }
                    .profile-record-table tbody td::before { content: attr(data-label); color: #7b8794; font-size: .7rem; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; }
                    .profile-record-table tbody td.record-primary { display: block; padding-top: .75rem; }
                    .profile-record-table tbody td.record-primary::before { display: none; }
                    .profile-record-table tbody td[colspan] { display: block; }
                    .profile-record-table tbody td[colspan]::before { display: none; }
                    .profile-record-table tbody tr.has-record-note { border-bottom: 0; border-radius: 12px 12px 0 0; box-shadow: none; }
                    .profile-record-table tbody tr.record-note-row { margin-top: -.75rem; padding: 0 .8rem .75rem; border-top: 0; border-radius: 0 0 12px 12px; box-shadow: 0 4px 12px rgba(31,45,61,.05); }
                    .profile-record-table tbody tr.record-note-row td { padding: 0; border: 0; }
                    .record-note { grid-template-columns: 30px minmax(0, 1fr); padding: .65rem .7rem; }
                    .record-note-icon { width: 29px; height: 29px; }
                    .relevance-editor { align-items: stretch; flex-direction: column; }
                    .relevance-editor .form-control { width: 100%; }
                    .vacancy-totals { grid-template-columns: 1fr; }
                    .modal-dialog { margin: .5rem; }
                }
                @media (max-width: 479.98px) {
                    .record-section-header { flex-direction: column; }
                    .record-section-header .btn { width: 100%; }
                }
            </style>

            <div class="content-page">
                <div class="content">



                    <!-- Start Content-->
                    <div class="container-fluid">
                          <!-- start page title -->
                          <div class="row">
                            <div class="col-sm-12">
                            <div class="profile-bg-picture" style="background-image:url('<?= base_url(); ?>assets/images/mis.jpg')">
                                    <span class="picture-bg-overlay"></span>
                                    <!-- overlay -->
                                </div>
                                <!-- meta -->
                                <div class="profile-user-box">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="profile-user-img"><img src="<?= base_url(); ?>/uploads/profile/<?php if($user->image != ""){echo $user->image;}else{
                                                            if(isset($user->sex)){if($user->sex == 0){echo "icon/m.jpg";}else{echo "icon/f.jpg";}}
                                                        } ?>" alt="" class="avatar-lg rounded-circle"></div>
                                            <div class="">
                                                <h4 class="mt-5 font-18 ellipsis"><?= $a_user->FirstName.' '.$a_user->MiddleName.' '.$a_user->LastName.' '.strtoupper($a_user->NameExtn); ?></h4>
                                                <p class="font-13"> <?= $a_user->empPosition; ?> </p>
                                                <p class="text-muted mb-0">Applicant No.: <strong><?= strtoupper($a_user->record_no); ?></strong></p>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="text-right">
                                                <a href="<?= base_url(); ?>Pages/profile_reg_edit/<?= $a_user->id; ?>" class="btn btn-success waves-effect waves-light"><i class="mdi mdi-account-settings-variant mr-1"></i> Edit Profile</a>
                                                
                                                <?php if($this->session->userdata('position')==='Admin'):?>
                                                
                                                    <a data-toggle="modal_awards" data-id="<?= $a_user->id; ?>" class="open-addAwards btn btn-info waves-effect width-md waves-light" href="#addEmployment">Deactivate</a>
                                                    <?php elseif($this->session->userdata('position')==='User'):?>
                                                <?php endif ?>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--/ meta -->
                            </div>
                        </div>
                        <!-- end row -->

                        <div class="row mt-4">
                            <div class="col-sm-12">
                                <div class="card p-0">
                                    <div class="card-body p-0">

                                    <?php if($this->session->flashdata('success')) : ?>

                                        <?= '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>'
                                                .$this->session->flashdata('success'). 
                                            '</div>'; 
                                        ?>
                                        <?php endif; ?>

                                        <?php if($this->session->flashdata('danger')) : ?>
                                        <?= '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>'
                                                .$this->session->flashdata('danger'). 
                                            '</div>'; 
                                        ?>
                                        <?php endif;  ?>

                                        <!-- <ul class=" nav nav-tabs tabs-bordered nav-justified"> -->
                                        <ul class="nav nav-pills navtab-bg nav-justified">
                                            <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#aboutme">About</a> </li>
                                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#family">Family</a></li>
                                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#trainings">Trainings</a></li>
                                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#work">Work Experience</a></li>
                                            <!-- <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#education">Education</a></li>
                                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#awards">Awards</a></li>
                                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#employment">Employment</a></li> -->
                                        </ul>

                                        <div class="tab-content m-0 p-4">

                                            <div id="aboutme" class="tab-pane active">
                                                <div class="profile-desk">
                                                    <h5 class="text-uppercase font-weight-bold">Official Information</h5>
                                                    <div class="row">
													<div class="col-sm-4">
                                                    <table class="table table-condensed mb-0">
                                                        
                                                        <tbody>
                                                            <tr>
                                                                <th scope="row">Job Title</th>
                                                                <td><?php if(empty($a_user->jobTitle)){echo "";}else{echo $a_user->jobTitle;} ?></td>
                                                            </tr>

                                                             <tr>
                                                                <th scope="row">Position</th>
                                                                <td><?= $a_user->empPosition; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row">Emp. Status</th>
                                                                <td><?= $a_user->empStatus; ?></td>
                                                            </tr>

                                                            <tr>
                                                                <th scope="row">Eligibility</th>
                                                                <td><?= $a_user->csEligibility; ?></td>
                                                            </tr>
                                                            
                                                        </tbody>
                                                    </table>
                                                    </div>

                                                    <div class="col-sm-4">
													<!--<h5 class="mt-4">Contact Person</h5>-->
													<table class="table table-condensed mb-0">
                                                        
                                                            <tbody>
                                                                <tr>
                                                                    <th scope="row">Department</th>
                                                                    <td><?= $a_user->Department; ?></td>
                                                                </tr>

                                                                <tr>
                                                                    <th scope="row">Expected Ret. Year</th>
                                                                    <td><?= $a_user->retYear; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row">TIN</th>
                                                                    <td><?= $a_user->tinNo; ?> </td>
                                                                </tr>
                                                            </tbody>
															
                                                        </table>
														</div>

                                                        <div class="col-sm-4">
													<!--<h5 class="mt-4">Contact Person</h5>-->
													<table class="table table-condensed mb-0">
                                                        
                                                            <tbody>
                                                                <tr>
                                                                    <th scope="row">GSIS BP No.</th>
                                                                    <td><?= $a_user->gsis; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row">PAG-IBIG No.</th>
                                                                    <td><?= $a_user->pagibig; ?></td>
                                                                </tr>

                                                                <tr>
                                                                    <th scope="row">SSS</th>
                                                                    <td><?= $a_user->sssNo; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row">PhilHealth No.</th>
                                                                    <td><?= $a_user->philHealth; ?></td>
                                                                </tr>
                                                            </tbody>
															
                                                        </table>
														</div>
                                                </div>

                                                <!-- End of the row -->
                                                <h5 class="text-uppercase font-weight-bold">Personal Information</h5>
                                                <div class="row">
													    <div class="col-sm-4">
                                                        <table class="table table-condensed mb-0">
                                                        
                                                        <tbody>
                                                            <tr>
                                                                <th scope="row">Gender</th>
                                                                <td><?= $a_user->Sex; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row">Birth Date</th>
                                                                <td><?= $a_user->BirthDate; ?></td>
                                                            </tr>

                                                             <tr>
                                                                <th scope="row">Birth Place</th>
                                                                <td><?= $a_user->BirthPlace; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row">Age</th>
                                                                <td><?= $a_user->age; ?></td>
                                                            </tr>     
                                                        </tbody>															
                                                        </table>
                                                        </div>

                                                        <div class="col-sm-4">
													<!--<h5 class="mt-4">Contact Person</h5>-->
													<table class="table table-condensed mb-0">
                                                        
                                                            <tbody>
                                                                <tr>
                                                                    <th scope="row">Blood Type</th>
                                                                    <td><?= $a_user->bloodType; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row">Marital Status</th>
                                                                    <td><?= $a_user->MaritalStatus; ?></td>
                                                                </tr>

                                                                <tr>
                                                                    <th scope="row">Citizenship</th>
                                                                    <td><?= $a_user->citizenship; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row">Citizenship Type</th>
                                                                    <td><?= $a_user->citizenshipType; ?></td>
                                                                </tr>
                                                            </tbody>
															
                                                        </table>
														</div>

                                                        <div class="col-sm-4">
													<!--<h5 class="mt-4">Contact Person</h5>-->
													<table class="table table-condensed mb-0">
                                                        
                                                            <tbody>
                                                                <tr>
                                                                    <th scope="row">Dual Citizen?</th>
                                                                    <td><?= $a_user->dualCitizenship; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row">Country</th>
                                                                    <td><?= $a_user->citizenshipCountry; ?></td>
                                                                </tr>

                                                                <tr>
                                                                    <th scope="row">Height</th>
                                                                    <td><?= $a_user->height; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row">Weight</th>
                                                                    <td><?= $a_user->weight; ?></td>
                                                                </tr>
                                                            </tbody>
															
                                                        </table>
														</div>

                                                    </div>
                                                    <!-- End of the row -->

                                                   
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                        <h5 class="text-uppercase font-weight-bold">Contact Information</h5>
                                                            <table class="table table-condensed mb-0">
                                                        
                                                            <tbody>
                                                                <tr>
                                                                    <th scope="row">Contact No.</th>
                                                                    <td><?= $a_user->contactNo; ?> <?= $a_user->empTelNo; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row">Official Email</th>
                                                                    <td><?= $a_user->empEmail; ?></td>
                                                                </tr>

                                                                <tr>
                                                                    <th scope="row">Address</th>
                                                                    <td class="ng-binding">  <?= $a_user->resVillage; ?>, <?= $a_user->resBarangay; ?>, <?= $a_user->resCity; ?>, <?= $a_user->resProvince; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row">Facebook</th>
                                                                    <td><?= $a_user->fb; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th scope="row">Skype</th>
                                                                    <td><?= $a_user->skype; ?></td>
                                                                </tr>
                                                                
                                                            </tbody>
                                                            </table>
                                                        </div>

                                                        <div class="col-sm-6">
                                                        <h5 class="text-uppercase font-weight-bold">In Case of Emergency</h5>
                                                            <table class="table table-condensed mb-0">
                                                            
                                                                <tbody>
                                                                    <tr>
                                                                        <th scope="row">Contact Person</th>
                                                                        <td><?= $a_user->contactName; ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th scope="row">Relationship</th>
                                                                        <td><?= $a_user->contactRel; ?></td>
                                                                    </tr>

                                                                    <tr>
                                                                        <th scope="row">Address</th>
                                                                        <td class="ng-binding"><?= $a_user->contactAddress; ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th scope="row">Email</th>
                                                                        <td><?= $a_user->contactEmail; ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th scope="row">Contact No.</th>
                                                                        <td><?= $a_user->empMobile; ?></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
														</div> 


                                                    </div>




                                                    </div> <!-- end profile-desk -->
                                                </div> 
                                                <!-- end of about-me -->

                                                <!-- Family -->
                                                <div id="family" class="tab-pane">
                                                    <!-- start page title -->
                                                    <div class="row">
                                                            <div class="col-12">
                                                                <div class="page-title-box">
                                                                    <h4 class="page-title">FAMILY MEMBERS </h4>
                                                                    <div class="page-title-right">
                                                                        <ol class="breadcrumb p-0 m-0">
                                                                        <!-- <li><a data-toggle="modal" data-id="" class="open-AddBookDialog btn btn-info waves-effect width-md waves-light" href="#familymodal">Add New</a></li>  -->
                                                                        </ol>
                                                                    </div>
                                                                    <div class="clearfix"></div>
                                                                    <br />
                                                                    <!-- <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;"> -->
                                                                    <table class="table mb-0">
                                                                        <thead>
                                                                                <tr>
                                                                                    <th>Full Name</th>
                                                                                    <th>Relationship</th>
                                                                                    <th>Birth Date</th>
                                                                                    <th style="text-align:center">Manage</th>
                                                                                </tr>
                                                                            </thead>

                                                                            <tbody>
                                                                                <?php foreach($family as $row){  ?>
                                                                                <tr>
                                                                                    <td><?= $row['fullName']; ?></td>
                                                                                    <td><?= $row['relationship']; ?></td>
                                                                                    <td><?= $row['bDate']; ?></td>
                                                                                    <td style="text-align:center">
                                                                                        <a href="<?=base_url(); ?>Pages/delete_family?famID=<?= $row['famID']; ?>&id=<?= $row['IDNumber']; ?>" class="text-danger"><i class="mdi mdi-file-document-box-check-outline"></i>Delete</a>
                                                                                    </td>
                                                                                </tr>
                                                                                <?php } ?>
                                                                            </tbody>
                                                                        </table>

                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- end page title -->


                                                </div>
                                                <!-- End of Family -->

                                                <!-- Education -->
                                                <div id="education" class="tab-pane">
                                                    <!-- start page title -->
                                                    <div class="row">
                                                            <div class="col-12">
                                                                <div class="page-title-box">
                                                                    <h4 class="page-title">EDUCATION</h4>
                                                                    <div class="page-title-right">
                                                                        <ol class="breadcrumb p-0 m-0">
                                                                        <li><a data-toggle="modal" data-id="<?= $user->id; ?>" class="open-AddBookDialog btn btn-info waves-effect width-md waves-light" href="#educationmodal">Add New</a></li> 
                                                                        </ol>
                                                                    </div>
                                                                    <div class="clearfix"></div>
                                                                    <br />
                                                                    <!-- <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;"> -->
                                                                    <table class="table mb-0">
                                                                        <thead>
                                                                                <tr>
                                                                                    <th>Level</th>
                                                                                    <th>School Name</th>
                                                                                    <th>Course</th>
                                                                                    <th>Year Started</th>
                                                                                    <th>Year Finished</th>
                                                                                    <th>Scholarship</th>
                                                                                    <th style="text-align:center">Manage</th>
                                                                                </tr>
                                                                            </thead>

                                                                            <tbody>
                                                                                <?php foreach($educ as $row){  ?>
                                                                                <tr>
                                                                                    <td><?= $row['level']; ?></td>
                                                                                    <td><?= $row['schoolName']; ?></td>
                                                                                    <td><?= $row['course']; ?></td>
                                                                                    <td><?= $row['yearStarted']; ?></td>
                                                                                    <td><?= $row['yearEnded']; ?></td>
                                                                                    <td><?= $row['scholarship']; ?></td>
                                                                                    <td style="text-align:center">
                                                                                         <a href="<?=base_url(); ?>Pages/delete_education?educID=<?= $row['educID']; ?>&id=<?= $row['IDNumber']; ?>" class="text-danger"><i class="mdi mdi-file-document-box-check-outline"></i>Delete</a>
                                                                                    </td>
                                                                                </tr>
                                                                                <?php } ?>
                                                                            </tbody>
                                                                        </table>

                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- end page title -->


                                                </div>
                                                <!-- End of Education -->

                                                <!-- Trainings -->
                                                <div id="trainings" class="tab-pane">
                                                    <?php
                                                        $canDeleteTrainings = $recordsOpen && !in_array($sessionRole, array('Evaluator', 'rater', 'raters'), true);
                                                        $trainingTotal = 0;

                                                        // Legacy rows may have dates without a time component.
                                                        $training_stamp = function ($value) {
                                                            if (empty($value) || substr($value, 0, 10) === '0000-00-00') return '<span class="text-muted">&mdash;</span>';
                                                            $stamp = strtotime($value);
                                                            if (!$stamp) return '<span class="text-muted">&mdash;</span>';
                                                            $out = date('M d, Y', $stamp);
                                                            if (date('H:i', $stamp) !== '00:00') $out .= ' <small class="text-muted">' . date('g:i A', $stamp) . '</small>';
                                                            return $out;
                                                        };
                                                    ?>
                                                    <section class="record-section">
                                                        <div class="record-section-header">
                                                            <div>
                                                                <h4 class="record-section-title"><i class="mdi mdi-school-outline text-info mr-1"></i> Trainings &amp; Seminars</h4>
                                                            </div>
                                                            <?php if($recordsOpen): ?>
                                                                <a data-toggle="modal" data-id="<?= (int) $a_user->id; ?>" class="open-AddBookDialog btn btn-info waves-effect waves-light" href=".bs-example-modal-lg"><i class="mdi mdi-plus mr-1"></i>Add Training</a>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="record-section-body">
                                                            <?= $record_context_strip('training', $relevance_counts($training, 'training', 'trainingID'), '#trainings'); ?>

                                                            <div class="table-responsive">
                                                                <table class="table table-hover profile-record-table mb-0">
                                                                    <thead class="thead-light">
                                                                        <tr>
                                                                            <th style="width:35%">Training</th>
                                                                            <th style="width:23%">Inclusive Schedule</th>
                                                                            <th style="width:18%" class="text-center">Hours &amp; File</th>
                                                                            <th style="width:24%">Evaluator Review</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php foreach($training as $row){
                                                                            $trainingId = (int) $row->trainingID;
                                                                            $startValue = !empty($row->dateStarted) && substr($row->dateStarted, 0, 10) !== '0000-00-00' ? date('Y-m-d\TH:i', strtotime($row->dateStarted)) : '';
                                                                            $endValue = !empty($row->dateFinished) && substr($row->dateFinished, 0, 10) !== '0000-00-00' ? date('Y-m-d\TH:i', strtotime($row->dateFinished)) : '';
                                                                            $trainingAttrs = 'data-id="' . $trainingId . '"'
                                                                                . ' data-title="' . html_escape((string) $row->trainingTitle) . '"'
                                                                                . ' data-from="' . html_escape($startValue) . '"'
                                                                                . ' data-to="' . html_escape($endValue) . '"'
                                                                                . ' data-hours="' . html_escape((string) $row->noHours) . '"';

                                                                            $trainingStat = (int) ($relevanceMap['training'][$trainingId][$selectedJobId] ?? 0);
                                                                            $trainingRemarks = (string) ($relevanceRemarks['training'][$trainingId][$selectedJobId] ?? '');
                                                                            $trainingCredited = $relevanceCredited['training'][$trainingId][$selectedJobId] ?? null;
                                                                            if ($selectedJobId > 0 && $trainingStat === 1) {
                                                                                $trainingTotal += $trainingCredited !== null ? (float) $trainingCredited : (float) $row->noHours;
                                                                            }
                                                                        ?>
                                                                        <tr class="<?= $trainingRemarks !== '' ? 'has-record-note' : ''; ?>">
                                                                            <td class="record-primary" data-label="Training">
                                                                                <div class="record-title"><?= html_escape($row->trainingTitle); ?></div>
                                                                                <div class="record-meta">Saved <?= $saved_stamp($row->created_at ?? null, $row->updated_at ?? null); ?></div>
                                                                                <div class="record-actions mt-2">
                                                                                    <?php if($detailsEditable): ?>
                                                                                        <a data-toggle="modal" <?= $trainingAttrs; ?> class="open-training-edit record-icon-button" href=".training-edit" title="Edit training details" aria-label="Edit training details"><i class="mdi mdi-pencil-outline"></i></a>
                                                                                    <?php endif; ?>
                                                                                    <?php if($canDeleteTrainings): ?>
                                                                                        <a onclick="return confirm('Delete this training and its attachment?')" href="<?= base_url(); ?>Page/training_delete_staff/<?= $trainingId; ?>" class="record-icon-button is-danger" title="Delete training" aria-label="Delete training"><i class="mdi mdi-trash-can-outline"></i></a>
                                                                                    <?php elseif(!$detailsEditable): ?>
                                                                                        <span class="record-readonly"><i class="mdi mdi-lock-outline"></i>Read-only</span>
                                                                                    <?php endif; ?>
                                                                                </div>
                                                                            </td>
                                                                            <td data-label="Inclusive Schedule">
                                                                                <div class="record-dates"><strong>From</strong> <?= $training_stamp($row->dateStarted); ?><br><strong>To</strong> <?= $training_stamp($row->dateFinished); ?></div>
                                                                            </td>
                                                                            <td class="text-center" data-label="Credit &amp; File">
                                                                                <div class="record-credit"><?= html_escape((string) $row->noHours); ?> hr</div>
                                                                                <?= $credited_line('training', $trainingId, $trainingCredited !== null ? $hours_text($trainingCredited) . ' hr' : null); ?>
                                                                                <div class="record-file">
                                                                                    <?php if(!empty($row->file)): ?>
                                                                                        <a href="<?= base_url().'uploads/trainings_staff/'.rawurlencode((string) $row->file); ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary"><i class="fas fa-file-pdf mr-1"></i>Certificate</a>
                                                                                    <?php else: ?>
                                                                                        <span class="text-muted small">No attachment</span>
                                                                                    <?php endif; ?>
                                                                                </div>
                                                                            </td>
                                                                            <td class="evaluation-cell" data-label="Evaluator Review">
                                                                                <?= $review_cell('training', $trainingId, $trainingStat, $trainingRemarks, (string) $row->trainingTitle, $hours_text($row->noHours), $trainingCredited !== null ? $hours_text($trainingCredited) : null); ?>
                                                                            </td>
                                                                        </tr>
                                                                        <?= $review_note('training', $trainingId, $trainingStat, $trainingRemarks); ?>
                                                                        <?php } ?>
                                                                        <?php if(empty($training)): ?>
                                                                            <tr><td colspan="4" class="text-center text-muted py-4">No trainings or seminars recorded yet.</td></tr>
                                                                        <?php endif; ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>

                                                            <div class="vacancy-totals" aria-label="Total relevant training hours for selected vacancy">
                                                                <?php $hoursText = $hours_text($trainingTotal); ?>
                                                                <div class="vacancy-total-card is-context">
                                                                    <div class="vacancy-total-label"><?= $selectedJobId > 0 ? 'Total relevant hours for ' . html_escape($selectedVacancyLabel) : 'No active vacancy selected'; ?></div>
                                                                    <div class="vacancy-total-value" data-relevance-total="training"><?= $hoursText; ?> hour<?= $trainingTotal === 1.0 ? '' : 's'; ?></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </section>
                                                </div>
                                                <!-- End of Trainings -->

                                                <!-- Work Experience -->
                                                <div id="work" class="tab-pane">
                                                    <?php
                                                        $experienceTotal = 0;
                                                    ?>
                                                    <section class="record-section">
                                                        <div class="record-section-header">
                                                            <div>
                                                                <h4 class="record-section-title"><i class="mdi mdi-briefcase-outline text-info mr-1"></i> Work Experience</h4>
                                                            </div>
                                                            <?php if($recordsOpen): ?>
                                                                <a data-toggle="modal" data-id="<?= (int) $user->id; ?>" class="open-AddBookDialog btn btn-info waves-effect waves-light" href=".renrenguapo"><i class="mdi mdi-plus mr-1"></i>Add Experience</a>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="record-section-body">
                                                            <?= $record_context_strip('experience', $relevance_counts($experience, 'experience', 'id'), '#work'); ?>

                                                            <div class="table-responsive">
                                                                <table class="table table-hover profile-record-table mb-0">
                                                                    <thead class="thead-light">
                                                                        <tr>
                                                                            <th style="width:35%">Experience</th>
                                                                            <th style="width:23%">Inclusive Dates</th>
                                                                            <th style="width:18%" class="text-center">Duration &amp; File</th>
                                                                            <th style="width:24%">Evaluator Review</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php foreach($experience as $row){
                                                                            $experienceId = (int) $row->id;
                                                                            $xpFrom = !empty($row->date_from ?? null) && $row->date_from !== '0000-00-00' ? $row->date_from : null;
                                                                            $xpTo   = !empty($row->date_to ?? null) && $row->date_to !== '0000-00-00' ? $row->date_to : null;
                                                                            $xpFromRange = ($xpFrom && $xpTo);
                                                                            $xpSpan = $xpFromRange
                                                                                ? $this->Reg->experience_months($xpFrom, $xpTo)
                                                                                : ((int) $row->ny * 12) + (int) $row->nm;
                                                                            $xpJobTitle = trim((string) ($row->position_title ?? ''));
                                                                            $experienceAttrs = 'data-id="' . $experienceId . '"'
                                                                                . ' data-company="' . html_escape((string) $row->title) . '"'
                                                                                . ' data-job="' . html_escape($xpJobTitle) . '"'
                                                                                . ' data-from="' . html_escape((string) $xpFrom) . '"'
                                                                                . ' data-to="' . html_escape((string) $xpTo) . '"';

                                                                            $experienceStat = (int) ($relevanceMap['experience'][$experienceId][$selectedJobId] ?? 0);
                                                                            $experienceRemarks = (string) ($relevanceRemarks['experience'][$experienceId][$selectedJobId] ?? '');
                                                                            $experienceCredited = $relevanceCredited['experience'][$experienceId][$selectedJobId] ?? null;
                                                                            if ($selectedJobId > 0 && $experienceStat === 1) {
                                                                                $experienceTotal += $experienceCredited !== null ? (int) $experienceCredited : $xpSpan;
                                                                            }
                                                                        ?>
                                                                        <tr class="<?= $experienceRemarks !== '' ? 'has-record-note' : ''; ?>">
                                                                            <td class="record-primary" data-label="Experience">
                                                                                <div class="record-title"><?= $xpJobTitle !== '' ? html_escape($xpJobTitle) : '<span class="text-warning">Job title not set</span>'; ?></div>
                                                                                <div class="text-muted mt-1"><?= html_escape($row->title); ?></div>
                                                                                <div class="record-meta">Saved <?= $saved_stamp($row->created_at ?? null, $row->updated_at ?? null); ?></div>
                                                                                <div class="record-actions mt-2">
                                                                                    <?php if($detailsEditable): ?>
                                                                                        <a data-toggle="modal" <?= $experienceAttrs; ?> class="open-experience-edit record-icon-button" href=".experience-edit" title="Edit work experience details" aria-label="Edit work experience details"><i class="mdi mdi-pencil-outline"></i></a>
                                                                                    <?php endif; ?>
                                                                                    <?php if($recordsOpen): ?>
                                                                                        <a onclick="return confirm('Delete this experience and its attachment?')" href="<?= base_url(); ?>Page/experience_delete/<?= $experienceId; ?>" class="record-icon-button is-danger" title="Delete work experience" aria-label="Delete work experience"><i class="mdi mdi-trash-can-outline"></i></a>
                                                                                    <?php elseif(!$detailsEditable): ?>
                                                                                        <span class="record-readonly"><i class="mdi mdi-lock-outline"></i>Read-only</span>
                                                                                    <?php endif; ?>
                                                                                </div>
                                                                            </td>
                                                                            <td data-label="Inclusive Dates">
                                                                                <div class="record-dates"><strong>From</strong> <?= $xpFrom ? date('M d, Y', strtotime($xpFrom)) : '<span class="text-muted">&mdash;</span>'; ?><br><strong>To</strong> <?= $xpTo ? date('M d, Y', strtotime($xpTo)) : '<span class="text-muted">&mdash;</span>'; ?></div>
                                                                            </td>
                                                                            <td class="text-center" data-label="Duration &amp; File">
                                                                                <div class="record-credit" title="<?= $xpFromRange ? 'Calculated from the inclusive dates.' : 'Stored duration; inclusive dates are not yet set.'; ?>"><?= intdiv($xpSpan, 12); ?> yr <?= $xpSpan % 12; ?> mo</div>
                                                                                <?= $credited_line('experience', $experienceId, $experienceCredited !== null ? intdiv($experienceCredited, 12) . ' yr ' . ($experienceCredited % 12) . ' mo' : null); ?>
                                                                                <div class="record-file">
                                                                                    <?php if(!empty($row->file)): ?>
                                                                                        <a href="<?= base_url().'uploads/experience/'.rawurlencode((string) $row->file); ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary"><i class="fas fa-file-pdf mr-1"></i>Evidence</a>
                                                                                    <?php else: ?>
                                                                                        <span class="text-muted small">No attachment</span>
                                                                                    <?php endif; ?>
                                                                                </div>
                                                                            </td>
                                                                            <td class="evaluation-cell" data-label="Evaluator Review">
                                                                                <?= $review_cell('experience', $experienceId, $experienceStat, $experienceRemarks, $xpJobTitle !== '' ? $xpJobTitle : (string) $row->title, $xpSpan, $experienceCredited); ?>
                                                                            </td>
                                                                        </tr>
                                                                        <?= $review_note('experience', $experienceId, $experienceStat, $experienceRemarks); ?>
                                                                        <?php } ?>
                                                                        <?php if(empty($experience)): ?>
                                                                            <tr><td colspan="4" class="text-center text-muted py-4">No work experience recorded yet.</td></tr>
                                                                        <?php endif; ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>

                                                            <div class="vacancy-totals" aria-label="Total relevant experience for selected vacancy">
                                                                <div class="vacancy-total-card is-context">
                                                                    <div class="vacancy-total-label"><?= $selectedJobId > 0 ? 'Total relevant experience for ' . html_escape($selectedVacancyLabel) : 'No active vacancy selected'; ?></div>
                                                                    <div class="vacancy-total-value" data-relevance-total="experience"><?= intdiv($experienceTotal, 12); ?> yr <?= $experienceTotal % 12; ?> mo</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </section>
                                                </div>
                                                <!-- End of Work Experience -->


                                                <!-- 201 Files -->
                                                <div id="files" class="tab-pane">
                                                    <!-- <h5 class="text-uppercase font-weight-bold">201 Files</h5> -->
                                                             <!-- start page title -->
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="page-title-box">
                                                                    <h4 class="page-title">201 FILES</h4>
                                                                    <div class="page-title-right">
                                                                        <ol class="breadcrumb p-0 m-0">
                                                                                <!-- <li><a data-toggle="modal_awards" data-id="" class="open-AddBookDialog btn btn-info waves-effect width-md waves-light" href="#addAwards">Add New</a></li> -->
                                                                                <li><a data-toggle="modal" data-id="<?= $user->id; ?>" class="open-AddBookDialog btn btn-info waves-effect width-md waves-light" href="#addBookDialog">Add New</a></li>
                                                                        </ol>
                                                                    </div>
                                                                    <div class="clearfix"></div>
                                                                    <br />
                                                                    <table class="table mb-0">
                                                                        <thead>
                                                                                <tr>
                                                                                    <th>Document Name</th>
                                                                                    <th>Date Uploaded</th>
                                                                                    <th style="text-align:center">Manage</th>
                                                                                </tr>
                                                                            </thead>

                                                                            <tbody>
                                                                                <?php foreach($files as $row){  ?>
                                                                                <tr>
                                                                                    <td><?= $row['docName']; ?></td>
                                                                                    <td><?= $row['dateUploaded']; ?></td>
                                                                                    <td style="text-align:center">
                                                                                         <!-- <a href="#/<?= $row['IDNumber']; ?>" class="text-success"><i class="mdi mdi-file-document-box-check-outline"></i>View</a>&nbsp;&nbsp;&nbsp;&nbsp; -->
                                                                                         <a href="<?= base_url(); ?>uploads/201files/<?= $row['fileName']; ?>" target="_blank" class="text-success"><i class="mdi mdi-file-document-box-check-outline"></i>View</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                                                                         <a href="<?=base_url(); ?>Pages/del_201/<?= $row['id']; ?>" class="text-danger"><i class="mdi mdi-file-document-box-check-outline"></i>Delete</a> 
                                                                                    </td>
                                                                                </tr>
                                                                                <?php } ?>
                                                                            </tbody>
                                                                        </table>

                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- end page title -->

                                                </div>
                                                <!-- End of 201 Files -->


                                                <!-- Employment -->
                                                <div id="employment" class="tab-pane">
                                                    <!-- <h5 class="text-uppercase font-weight-bold">201 Files</h5> -->
                                                             <!-- start page title -->
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="page-title-box">
                                                                    <h4 class="page-title">EMPLOYMENT HISTORY</h4>
                                                                    <div class="page-title-right">
                                                                        <ol class="breadcrumb p-0 m-0">
                                                                                
                                                                                <li><a data-toggle="modal" data-id="<?= $user->id; ?>" class="open-AddBookDialog btn btn-info waves-effect width-md waves-light" href="#employmentmodal">Add New</a></li>
                                                                        </ol>
                                                                    </div>
                                                                    <div class="clearfix"></div>
                                                                    <br />
                                                                    <table class="table mb-0">
                                                                        <thead>
                                                                                <tr>
                                                                                    <th>Position</th>
                                                                                    <th>SG</th>
                                                                                    <th>Step</th>
                                                                                    <th>Item No.</th>
                                                                                    <th>Salary</th>
                                                                                    <th>Station</th>
                                                                                    <th>Status</th>
                                                                                    <th>From</th>
                                                                                    <th>To</th>
                                                                                    <th style="text-align:center">Manage</th>
                                                                                </tr>
                                                                            </thead>

                                                                            <tbody>
                                                                                <?php foreach($employment as $row){  ?>
                                                                                <tr>
                                                                                    <td><?= $row['empPosition']; ?></td>
                                                                                    <td><?= $row['sgNo']; ?></td>
                                                                                    <td><?= $row['stepInc']; ?></td>
                                                                                    <td><?= $row['itemNo']; ?></td>
                                                                                    <td><?= $row['salary']; ?></td>
                                                                                    <td><?= $row['empStation']; ?></td>
                                                                                    <td><?= $row['empStatus']; ?></td>
                                                                                    <td><?= $row['appointDate']; ?></td>
                                                                                    <td><?= $row['endDate']; ?></td>
                                                                                    <td style="text-align:center">
                                                                                         <!-- <a href="#/<?= $row['IDNumber']; ?>" class="text-success"><i class="mdi mdi-file-document-box-check-outline"></i>View</a>&nbsp;&nbsp;&nbsp;&nbsp; -->
                                                                                         <!-- <a href="#/<?= $row['IDNumber']; ?>" target="_blank" class="text-success"><i class="mdi mdi-file-document-box-check-outline"></i>View</a>&nbsp;&nbsp;&nbsp;&nbsp; -->
                                                                                         <a href="<?=base_url(); ?>Pages/delete_employment?empID=<?= $row['empID']; ?>&id=<?= $row['IDNumber']; ?>" class="text-danger"><i class="mdi mdi-file-document-box-check-outline"></i>Delete</a> 
                                                                                    </td>
                                                                                </tr>
                                                                                <?php } ?>
                                                                            </tbody>
                                                                        </table>

                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- end page title -->

                                                </div>
                                                <!-- End of Employment -->


                                                


                                            </div>

                                        </div> 
                                    </div>
                                </div>
                            <!-- end page title -->

                        </div>
                        <!-- end row -->

                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->

                                        <?php if($canRateSelectedVacancy): ?>
                                        <div class="modal fade record-review-modal" tabindex="-1" role="dialog" aria-labelledby="recordReviewLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <div class="review-modal-heading">
                                                            <span class="review-modal-icon"><i class="mdi mdi-clipboard-check-outline"></i></span>
                                                            <div class="review-modal-heading-text">
                                                                <div class="review-modal-kicker" id="record_review_type">Evaluator Review</div>
                                                                <h5 class="review-modal-title" id="recordReviewLabel">Review record</h5>
                                                                <span class="review-vacancy-chip" title="Reviewing for <?= html_escape($selectedVacancyLabel); ?>"><i class="mdi mdi-briefcase-search-outline"></i><span><?= html_escape($selectedVacancyLabel); ?></span></span>
                                                            </div>
                                                        </div>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="review-form-section">
                                                            <div class="review-section-head">
                                                                <span class="review-section-icon"><i class="mdi mdi-checkbox-marked-circle-outline"></i></span>
                                                                <div class="review-section-text">
                                                                    <span class="review-field-label" id="record_review_relevance_label">Relevance</span>
                                                                    <span class="review-section-hint">Saves automatically when changed.</span>
                                                                </div>
                                                            </div>
                                                            <?= form_open('Page/update_record_relevance', array('class' => 'relevance-editor js-relevance-form')); ?>
                                                                <div class="review-segmented" role="group" aria-labelledby="record_review_relevance_label">
                                                                    <button type="button" class="review-segment" data-stat="0" aria-pressed="false"><i class="mdi mdi-minus-circle-outline"></i>No Action</button>
                                                                    <button type="button" class="review-segment" data-stat="1" aria-pressed="false"><i class="mdi mdi-check-circle-outline"></i>Relevant</button>
                                                                    <button type="button" class="review-segment" data-stat="2" aria-pressed="false"><i class="mdi mdi-close-circle-outline"></i>Not Relevant</button>
                                                                </div>
                                                                <select name="relevance" id="record_review_relevance" class="sr-only js-relevance-select relevance-status-0" tabindex="-1" aria-hidden="true">
                                                                    <option value="">No Action</option>
                                                                    <option value="">Relevant</option>
                                                                    <option value="">Not Relevant</option>
                                                                </select>
                                                                <span class="relevance-save-state" role="status" aria-live="polite"></span>
                                                            </form>
                                                        </div>

                                                        <?= form_open('Page/update_record_credit', array('class' => 'record-credit-editor js-record-credit-form review-form-section')); ?>
                                                            <input type="hidden" name="record_type" value="">
                                                            <input type="hidden" name="record_id" value="">
                                                            <input type="hidden" name="job_id" value="">
                                                            <div class="review-section-head">
                                                                <span class="review-section-icon is-amber"><i class="mdi mdi-timer-sand"></i></span>
                                                                <div class="review-section-text">
                                                                    <span class="review-field-label" id="record_credit_label">Relevant Hours</span>
                                                                    <span class="review-section-hint">Credited toward this vacancy's total.</span>
                                                                </div>
                                                            </div>
                                                            <div class="record-credit-grid">
                                                                <div class="record-credit-computed">
                                                                    <small>Computed</small>
                                                                    <strong id="record_credit_computed">&mdash;</strong>
                                                                </div>
                                                                <div>
                                                                    <div class="record-credit-inputs" data-credit-for="training">
                                                                        <div class="input-group">
                                                                            <input type="number" name="credit_hours" id="record_credit_hours" min="0" max="99999.99" step="0.01" class="form-control" aria-labelledby="record_credit_label">
                                                                            <div class="input-group-append"><span class="input-group-text">hours</span></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="record-credit-inputs" data-credit-for="experience">
                                                                        <div class="input-group">
                                                                            <input type="number" name="credit_years" id="record_credit_years" min="0" max="100" step="1" class="form-control" aria-label="Credited years">
                                                                            <div class="input-group-append"><span class="input-group-text">yr</span></div>
                                                                        </div>
                                                                        <div class="input-group">
                                                                            <input type="number" name="credit_months" id="record_credit_months" min="0" max="1200" step="1" class="form-control" aria-label="Credited months">
                                                                            <div class="input-group-append"><span class="input-group-text">mo</span></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="record-credit-note"><i class="mdi mdi-information-outline"></i><span>Adjust when the actual relevant service differs from the record (e.g. a layoff within the contract). Counts only when marked Relevant.</span></div>
                                                            <div class="record-credit-actions">
                                                                <span class="credit-save-state remarks-save-state mr-auto" role="status" aria-live="polite"></span>
                                                                <button type="button" class="btn btn-light btn-sm js-credit-reset"><i class="mdi mdi-restore mr-1"></i>Use Computed</button>
                                                                <button type="submit" class="btn btn-info btn-sm px-3"><i class="mdi mdi-content-save-outline mr-1"></i>Save</button>
                                                            </div>
                                                        </form>

                                                        <?= form_open('Page/update_record_remarks', array('class' => 'record-remarks-editor js-record-remarks-form review-form-section')); ?>
                                                            <input type="hidden" name="record_type" id="record_review_record_type" value="">
                                                            <input type="hidden" name="record_id" id="record_review_record_id" value="">
                                                            <input type="hidden" name="job_id" id="record_review_job_id" value="">
                                                            <div class="review-section-head">
                                                                <span class="review-section-icon is-violet"><i class="mdi mdi-comment-text-outline"></i></span>
                                                                <div class="review-section-text">
                                                                    <label class="review-field-label" for="record_review_remarks">Evaluator Remarks</label>
                                                                    <span class="review-section-hint">Visible on the record for this vacancy.</span>
                                                                </div>
                                                                <span class="review-character-count"><span id="record_review_count">0</span> / 2,000</span>
                                                            </div>
                                                            <textarea id="record_review_remarks" name="remarks" maxlength="2000" rows="4" class="form-control" placeholder="Add a concise note about this record..."></textarea>
                                                            <div class="record-remarks-actions">
                                                                <span class="remarks-save-state mr-auto" role="status" aria-live="polite"></span>
                                                                <button type="submit" class="btn btn-info btn-sm px-3"><i class="mdi mdi-content-save-outline mr-1"></i>Save Remarks</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light btn-sm px-3" data-dismiss="modal">Done</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>

                                        <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="addTrainingLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                                <div class="modal-content">
                                                    <?php echo form_open_multipart('Pages/trainings', array('class' => 'parsley-examples')); ?>
                                                        <div class="modal-header bg-info text-white">
                                                            <h5 class="modal-title" id="addTrainingLabel"><i class="mdi mdi-school-outline mr-1"></i> Add Training / Seminar</h5>
                                                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                        </div>

                                                        <div class="modal-body">
                                                            <input type="hidden" name="id" id="id" value="<?= (int) $a_user->id; ?>">
                                                            <input type="hidden" name="profile_app_id" value="<?= (int) ($profile_application->appID ?? 0); ?>">
                                                            <input type="hidden" name="profile_job_id" value="<?= (int) ($profile_application->jobID ?? 0); ?>">

                                                            <div class="form-group">
                                                                <label class="font-weight-semibold">Training Title <span class="text-danger">*</span></label>
                                                                <textarea name="trainingTitle" class="form-control" rows="3" placeholder="e.g. Regional Training on Learning Recovery" required></textarea>
                                                            </div>

                                                            <div class="form-row">
                                                                <div class="form-group col-md-6">
                                                                    <label class="font-weight-semibold">Inclusive Date &amp; Time &mdash; From <span class="text-danger">*</span></label>
                                                                    <input type="datetime-local" value="<?= set_value('dateStarted'); ?>" name="dateStarted" class="form-control js-range-from" data-range="training" required>
                                                                </div>
                                                                <div class="form-group col-md-6">
                                                                    <label class="font-weight-semibold">Inclusive Date &amp; Time &mdash; To <span class="text-danger">*</span></label>
                                                                    <input type="datetime-local" value="<?= set_value('dateFinished'); ?>" name="dateFinished" class="form-control js-range-to" data-range="training" required>
                                                                </div>
                                                            </div>

                                                            <div class="text-danger small mb-2 js-range-warning" data-for="training" style="display:none;"></div>

                                                            <div class="form-row">
                                                                <div class="form-group col-md-8">
                                                                    <label class="font-weight-semibold">Conducted By</label>
                                                                    <input type="text" value="<?= set_value('sponsor'); ?>" name="sponsor" class="form-control" placeholder="Sponsoring office or organization">
                                                                </div>
                                                                <div class="form-group col-md-4">
                                                                    <label class="font-weight-semibold">No. of Hours <span class="text-danger">*</span></label>
                                                                    <input name="noHours" type="text" class="form-control js-hours-total" data-for="training" value="" readonly required>
                                                                    <small class="form-text text-muted">Daily time window &times; number of days; 8 hours a day when no time window is set.</small>
                                                                </div>
                                                            </div>

                                                            <div class="form-group mb-0">
                                                                <label class="font-weight-semibold">Attachment <span class="text-danger">*</span></label>
                                                                <input type="file" class="form-control" name="file" accept="application/pdf" required>
                                                                <small class="form-text text-muted">PDF file only.</small>
                                                            </div>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                                                            <button type="submit" name="awards" class="btn btn-primary waves-effect waves-light">Save Training</button>
                                                        </div>
                                                    </form>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->


                                        <div class="modal fade renrenguapo" tabindex="-1" role="dialog" aria-labelledby="addExperienceLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                                <div class="modal-content">
                                                    <?php echo form_open_multipart('Page/insert_experience', array('class' => 'parsley-examples')); ?>
                                                        <div class="modal-header bg-info text-white">
                                                            <h5 class="modal-title" id="addExperienceLabel"><i class="mdi mdi-briefcase-outline mr-1"></i> Add Work Experience</h5>
                                                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                        </div>

                                                        <div class="modal-body">
                                                            <input type="hidden" value="<?= $this->uri->segment(2); ?>" name="id_number">
                                                            <input type="hidden" name="profile_app_id" value="<?= (int) ($profile_application->appID ?? 0); ?>">
                                                            <input type="hidden" name="profile_job_id" value="<?= (int) ($profile_application->jobID ?? 0); ?>">

                                                            <div class="form-group">
                                                                <label class="font-weight-semibold">Company / Office Name <span class="text-danger">*</span></label>
                                                                <textarea name="title" class="form-control" rows="3" placeholder="e.g. Department of Education - Davao Oriental" required></textarea>
                                                            </div>

                                                            <div class="form-group">
                                                                <label class="font-weight-semibold">Job Title / Position Held <span class="text-danger">*</span></label>
                                                                <input name="position_title" type="text" class="form-control" maxlength="255" placeholder="e.g. Administrative Assistant II" value="<?= set_value('position_title'); ?>" required>
                                                                <small class="form-text text-muted">The post actually held at this office, as written on the service record.</small>
                                                            </div>

                                                            <div class="form-row">
                                                                <div class="form-group col-md-6">
                                                                    <label class="font-weight-semibold">Inclusive Date &mdash; From <span class="text-danger">*</span></label>
                                                                    <input name="date_from" type="date" class="form-control js-range-from js-xp-from" data-range="newxp" value="<?= set_value('date_from'); ?>" required>
                                                                </div>
                                                                <div class="form-group col-md-6">
                                                                    <label class="font-weight-semibold">Inclusive Date &mdash; To <span class="text-danger">*</span></label>
                                                                    <input name="date_to" type="date" class="form-control js-range-to js-xp-to" data-range="newxp" value="<?= set_value('date_to'); ?>" required>
                                                                </div>
                                                            </div>

                                                            <div class="text-danger small mb-2 js-range-warning" data-for="newxp" style="display:none;"></div>

                                                            <div class="alert alert-secondary py-2 mb-3">
                                                                <i class="mdi mdi-calendar-clock mr-1"></i>
                                                                Length of service: <strong class="js-xp-preview" data-for="newxp">&mdash;</strong>
                                                            </div>

                                                            <div class="form-group mb-0">
                                                                <label class="font-weight-semibold">Attachment <span class="text-danger">*</span></label>
                                                                <input type="file" class="form-control" name="file" accept="application/pdf" required>
                                                                <small class="form-text text-muted">Service record or certificate of employment, PDF file only.</small>
                                                            </div>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                                                            <button type="submit" name="submit" class="btn btn-primary waves-effect waves-light">Save Experience</button>
                                                        </div>
                                                    </form>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->

                                        <div class="modal fade experience-edit" tabindex="-1" role="dialog" aria-labelledby="experienceEditLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                                <div class="modal-content">
                                                    <?= form_open('Page/update_experience_details', array('class' => 'parsley-examples')); ?>
                                                        <div class="modal-header bg-info text-white">
                                                            <h5 class="modal-title" id="experienceEditLabel"><i class="mdi mdi-briefcase-edit-outline mr-1"></i>Edit Work Experience</h5>
                                                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <input type="hidden" value="<?= (int) $this->uri->segment(2); ?>" name="id_number">
                                                            <input type="hidden" name="id" value="" id="experience_edit_id">
                                                            <input type="hidden" name="profile_app_id" value="<?= (int) ($profile_application->appID ?? 0); ?>">
                                                            <input type="hidden" name="profile_job_id" value="<?= (int) ($profile_application->jobID ?? 0); ?>">

                                                            <div class="form-row">
                                                                <div class="form-group col-md-6">
                                                                    <label class="font-weight-semibold">Company / Office <span class="text-danger">*</span></label>
                                                                    <textarea name="title" id="experience_edit_company" class="form-control" rows="3" required></textarea>
                                                                </div>
                                                                <div class="form-group col-md-6">
                                                                    <label class="font-weight-semibold">Job Title / Position Held <span class="text-danger">*</span></label>
                                                                    <input name="position_title" id="experience_edit_job" type="text" maxlength="255" class="form-control" required>
                                                                    <small class="form-text text-muted">Use the title shown on the service record.</small>
                                                                </div>
                                                            </div>
                                                            <div class="form-row">
                                                                <div class="form-group col-md-6">
                                                                    <label class="font-weight-semibold">From <span class="text-danger">*</span></label>
                                                                    <input name="date_from" type="date" id="experience_edit_from" class="form-control js-range-from" data-range="editexperience" required>
                                                                </div>
                                                                <div class="form-group col-md-6">
                                                                    <label class="font-weight-semibold">To <span class="text-danger">*</span></label>
                                                                    <input name="date_to" type="date" id="experience_edit_to" class="form-control js-range-to" data-range="editexperience" required>
                                                                </div>
                                                            </div>
                                                            <div class="text-danger small mb-2 js-range-warning" data-for="editexperience" style="display:none;"></div>
                                                            <div class="alert alert-secondary py-2 mb-0">
                                                                <i class="mdi mdi-calendar-clock mr-1"></i>Total experience: <strong class="js-xp-preview" data-for="editexperience">&mdash;</strong>
                                                                <br><small class="text-muted">Automatically recalculated from the inclusive dates.</small>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary waves-effect waves-light">Save Changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal fade training-edit" tabindex="-1" role="dialog" aria-labelledby="trainingEditLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                                <div class="modal-content">
                                                    <?= form_open('Page/update_trainings_staff', array('class' => 'parsley-examples')); ?>
                                                        <div class="modal-header bg-info text-white">
                                                            <h5 class="modal-title" id="trainingEditLabel"><i class="mdi mdi-school-outline mr-1"></i>Edit Training / Seminar</h5>
                                                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <input type="hidden" value="<?= (int) $this->uri->segment(2); ?>" name="id_number">
                                                            <input type="hidden" name="id" value="" id="training_edit_id">
                                                            <input type="hidden" name="profile_app_id" value="<?= (int) ($profile_application->appID ?? 0); ?>">
                                                            <input type="hidden" name="profile_job_id" value="<?= (int) ($profile_application->jobID ?? 0); ?>">

                                                            <div class="form-group">
                                                                <label class="font-weight-semibold">Training Title <span class="text-danger">*</span></label>
                                                                <textarea name="trainingTitle" id="training_edit_title" class="form-control" rows="3" required></textarea>
                                                            </div>
                                                            <div class="form-row">
                                                                <div class="form-group col-md-6">
                                                                    <label class="font-weight-semibold">From <span class="text-danger">*</span></label>
                                                                    <input name="dateStarted" type="datetime-local" id="training_edit_from" class="form-control js-range-from" data-range="edittraining" required>
                                                                </div>
                                                                <div class="form-group col-md-6">
                                                                    <label class="font-weight-semibold">To <span class="text-danger">*</span></label>
                                                                    <input name="dateFinished" type="datetime-local" id="training_edit_to" class="form-control js-range-to" data-range="edittraining" required>
                                                                </div>
                                                            </div>
                                                            <div class="text-danger small mb-2 js-range-warning" data-for="edittraining" style="display:none;"></div>
                                                            <div class="form-group mb-0">
                                                                <label class="font-weight-semibold">Total Relevant Hours <span class="text-danger">*</span></label>
                                                                <input name="nh" type="number" id="training_edit_hours" min="0" step="0.01" class="form-control js-hours-total" data-for="edittraining" required>
                                                                <small class="form-text text-muted">Recalculated when the date range changes; you may correct the credited total before saving.</small>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary waves-effect waves-light">Save Changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>


                                        <script type="text/javascript">
                                            // Plain JS throughout: jQuery is not loaded until the footer runs.
                                            (function () {

                                                var activeReviewTrigger = null;

                                                // Handles both the date-only experience fields and the training
                                                // fields, which carry a time (2025-01-15T08:00).
                                                function parseDate(value) {
                                                    var parts = /^(\d{4})-(\d{2})-(\d{2})(?:T(\d{2}):(\d{2}))?/.exec(value || '');
                                                    if (!parts) return null;
                                                    return new Date(+parts[1], parts[2] - 1, +parts[3], +(parts[4] || 0), +(parts[5] || 0));
                                                }

                                                // Mirrors Reg::experience_duration() so the preview matches what the
                                                // server will store. The server value stays authoritative on save.
                                                function monthsBetween(from, to) {
                                                    var months = (to.getFullYear() - from.getFullYear()) * 12 + (to.getMonth() - from.getMonth());
                                                    var days = to.getDate() - from.getDate();

                                                    if (days < 0) {
                                                        months--;
                                                        var anniversary = new Date(to.getFullYear(), to.getMonth() - 1, from.getDate());
                                                        days = Math.round((to - anniversary) / 86400000);
                                                    }

                                                    if (days >= 15) months++;
                                                    return months;
                                                }

                                                // Records are historical, so these fields must accept any date. A min/max
                                                // bound on them (Parsley reads those attributes) is what blocked saving an
                                                // older range, so the bounds are stripped and the order is checked here.
                                                function unbindDateLimits(field) {
                                                    if (!field) return;
                                                    field.removeAttribute('min');
                                                    field.removeAttribute('max');
                                                }

                                                function clearAllDateLimits() {
                                                    var fields = document.querySelectorAll('.js-range-from, .js-range-to');
                                                    for (var i = 0; i < fields.length; i++) {
                                                        unbindDateLimits(fields[i]);
                                                    }
                                                }

                                                function setRangeWarning(group, message) {
                                                    var box = document.querySelector('.js-range-warning[data-for="' + group + '"]');
                                                    if (!box) return;
                                                    box.textContent = message || '';
                                                    box.style.display = message ? '' : 'none';
                                                }

                                                // Mirrors Page_model::training_hours(): the daily time window times
                                                // the number of days, so overnight gaps are not credited. Without a
                                                // usable window the day counts as a standard 8 hours.
                                                function trainingHours(start, end) {
                                                    var startDay = new Date(start.getFullYear(), start.getMonth(), start.getDate());
                                                    var endDay = new Date(end.getFullYear(), end.getMonth(), end.getDate());
                                                    var days = Math.round((endDay - startDay) / 86400000) + 1;
                                                    if (days <= 0) return 0;

                                                    var window = (end.getHours() * 60 + end.getMinutes()) - (start.getHours() * 60 + start.getMinutes());
                                                    if (window <= 0) return days * 8;

                                                    return Math.round(days * window / 60 * 100) / 100;
                                                }

                                                function refreshRange(group) {
                                                    var from = document.querySelector('.js-range-from[data-range="' + group + '"]');
                                                    var to = document.querySelector('.js-range-to[data-range="' + group + '"]');
                                                    var preview = document.querySelector('.js-xp-preview[data-for="' + group + '"]');
                                                    var hours = document.querySelector('.js-hours-total[data-for="' + group + '"]');
                                                    if (!from || !to) return;

                                                    unbindDateLimits(from);
                                                    unbindDateLimits(to);

                                                    var start = parseDate(from.value);
                                                    var end = parseDate(to.value);
                                                    var reversed = !!(start && end && end < start);

                                                    setRangeWarning(group, reversed ? 'The "to" date cannot be earlier than the "from" date.' : '');

                                                    if (hours) {
                                                        hours.value = (start && end && !reversed) ? trainingHours(start, end) : '';
                                                    }

                                                    if (!preview) return;

                                                    if (!start || !end || reversed) {
                                                        preview.textContent = '—';
                                                        return;
                                                    }

                                                    var months = monthsBetween(start, end);
                                                    preview.textContent = Math.floor(months / 12) + ' year(s) and ' + (months % 12) + ' month(s)';
                                                }

                                                document.addEventListener('change', function (e) {
                                                    var field = e.target;
                                                    if (!field || !field.classList) return;
                                                    if (field.classList.contains('js-range-from') || field.classList.contains('js-range-to')) {
                                                        refreshRange(field.getAttribute('data-range'));
                                                    }
                                                });

                                                // Captured on the way down so the range is settled before Parsley's own
                                                // submit handler on the form runs.
                                                document.addEventListener('submit', function (e) {
                                                    var form = e.target;
                                                    if (!form || !form.querySelector) return;

                                                    var from = form.querySelector('.js-range-from');
                                                    var to = form.querySelector('.js-range-to');
                                                    if (!from || !to) return;

                                                    unbindDateLimits(from);
                                                    unbindDateLimits(to);

                                                    var start = parseDate(from.value);
                                                    var end = parseDate(to.value);
                                                    if (start && end && end < start) {
                                                        e.preventDefault();
                                                        e.stopImmediatePropagation();
                                                        setRangeWarning(from.getAttribute('data-range'), 'The "to" date cannot be earlier than the "from" date.');
                                                        to.focus();
                                                    }
                                                }, true);

                                                clearAllDateLimits();
                                                document.addEventListener('DOMContentLoaded', clearAllDateLimits);

                                                // Fill the consolidated experience editor from its record button.
                                                document.addEventListener('click', function (e) {
                                                    var trigger = e.target && e.target.closest ? e.target.closest('.open-experience-edit') : null;
                                                    if (!trigger) return;

                                                    document.getElementById('experience_edit_id').value = trigger.getAttribute('data-id') || '';
                                                    document.getElementById('experience_edit_company').value = trigger.getAttribute('data-company') || '';
                                                    document.getElementById('experience_edit_job').value = trigger.getAttribute('data-job') || '';
                                                    document.getElementById('experience_edit_from').value = trigger.getAttribute('data-from') || '';
                                                    document.getElementById('experience_edit_to').value = trigger.getAttribute('data-to') || '';

                                                    refreshRange('editexperience');
                                                });

                                                // Fill title, inclusive date/time and credited hours together.
                                                document.addEventListener('click', function (e) {
                                                    var trigger = e.target && e.target.closest ? e.target.closest('.open-training-edit') : null;
                                                    if (!trigger) return;

                                                    document.getElementById('training_edit_id').value = trigger.getAttribute('data-id') || '';
                                                    document.getElementById('training_edit_title').value = trigger.getAttribute('data-title') || '';
                                                    document.getElementById('training_edit_from').value = trigger.getAttribute('data-from') || '';
                                                    document.getElementById('training_edit_to').value = trigger.getAttribute('data-to') || '';
                                                    refreshRange('edittraining');
                                                    // Keep the stored credited value authoritative until a date is changed.
                                                    document.getElementById('training_edit_hours').value = trigger.getAttribute('data-hours') || '';
                                                });

                                                // One reusable modal keeps the table compact while retaining the
                                                // record and vacancy context for every save.
                                                document.addEventListener('click', function (e) {
                                                    var trigger = e.target && e.target.closest ? e.target.closest('.open-record-review') : null;
                                                    if (!trigger) return;

                                                    activeReviewTrigger = trigger;

                                                    var type = trigger.getAttribute('data-record-type') || '';
                                                    var recordId = trigger.getAttribute('data-record-id') || '';
                                                    var jobId = trigger.getAttribute('data-job-id') || '';
                                                    var stat = trigger.getAttribute('data-stat') || '0';
                                                    var encodedRemarks = trigger.getAttribute('data-remarks') || '';
                                                    var remarks = '';
                                                    try { remarks = decodeURIComponent(encodedRemarks); } catch (ignore) { remarks = encodedRemarks; }
                                                    var prefix = type + ':' + recordId + ':' + jobId + ':';
                                                    var select = document.getElementById('record_review_relevance');
                                                    var textarea = document.getElementById('record_review_remarks');
                                                    var remarkForm = textarea ? textarea.closest('.js-record-remarks-form') : null;
                                                    var relevanceForm = select ? select.closest('.js-relevance-form') : null;

                                                    document.getElementById('recordReviewLabel').textContent = trigger.getAttribute('data-title') || 'Review record';
                                                    document.getElementById('record_review_type').textContent = type === 'training' ? 'Training Review' : 'Work Experience Review';
                                                    document.getElementById('record_review_record_type').value = type;
                                                    document.getElementById('record_review_record_id').value = recordId;
                                                    document.getElementById('record_review_job_id').value = jobId;

                                                    if (select) {
                                                        select.options[0].value = prefix + '0';
                                                        select.options[1].value = prefix + '1';
                                                        select.options[2].value = prefix + '2';
                                                        select.value = prefix + stat;
                                                        select.setAttribute('data-previous-value', select.value);
                                                        paintRelevance(select, stat);
                                                    }

                                                    if (textarea) textarea.value = remarks;
                                                    updateRemarkCount(textarea);

                                                    fillCreditForm(type, recordId, jobId, trigger.getAttribute('data-computed') || '', trigger.getAttribute('data-credited') || '');

                                                    if (relevanceForm) {
                                                        relevanceForm.classList.remove('is-saving', 'is-saved', 'has-error');
                                                        var relevanceState = relevanceForm.querySelector('.relevance-save-state');
                                                        if (relevanceState) relevanceState.textContent = '';
                                                    }
                                                    if (remarkForm) {
                                                        remarkForm.classList.remove('is-saving', 'is-saved', 'has-error');
                                                        var remarksState = remarkForm.querySelector('.remarks-save-state');
                                                        if (remarksState) remarksState.textContent = '';
                                                    }
                                                });

                                                function monthsText(months) {
                                                    months = parseInt(months, 10) || 0;
                                                    return Math.floor(months / 12) + ' yr ' + (months % 12) + ' mo';
                                                }

                                                function hoursText(hours) {
                                                    return String(Math.round((parseFloat(hours) || 0) * 100) / 100);
                                                }

                                                function fillCreditForm(type, recordId, jobId, computed, credited) {
                                                    var form = document.querySelector('.js-record-credit-form');
                                                    if (!form) return;
                                                    var isTraining = type === 'training';

                                                    form.elements.record_type.value = type;
                                                    form.elements.record_id.value = recordId;
                                                    form.elements.job_id.value = jobId;
                                                    form.querySelector('[data-credit-for="training"]').style.display = isTraining ? '' : 'none';
                                                    form.querySelector('[data-credit-for="experience"]').style.display = isTraining ? 'none' : '';
                                                    document.getElementById('record_credit_label').textContent = isTraining ? 'Relevant Hours' : 'Relevant Experience';
                                                    document.getElementById('record_credit_computed').textContent = computed === ''
                                                        ? '\u2014' : (isTraining ? hoursText(computed) + ' hr' : monthsText(computed));

                                                    // Start from the evaluator's figure, else the computed one.
                                                    var value = credited !== '' ? credited : computed;
                                                    document.getElementById('record_credit_hours').value = isTraining && value !== '' ? hoursText(value) : '';
                                                    document.getElementById('record_credit_years').value = !isTraining && value !== '' ? Math.floor((parseInt(value, 10) || 0) / 12) : '';
                                                    document.getElementById('record_credit_months').value = !isTraining && value !== '' ? (parseInt(value, 10) || 0) % 12 : '';

                                                    form.classList.remove('is-saving', 'is-saved', 'has-error');
                                                    var state = form.querySelector('.credit-save-state');
                                                    if (state) state.textContent = credited !== '' ? 'Evaluator credit applied' : '';
                                                }

                                                function updateCreditSummary(trigger, type, credited) {
                                                    if (!trigger) return;

                                                    var hasCredit = credited !== null && credited !== '';
                                                    trigger.setAttribute('data-credited', hasCredit ? String(credited) : '');
                                                    var summary = trigger.closest('.review-summary');
                                                    var key = summary ? summary.getAttribute('data-review-row') : '';
                                                    var line = key ? document.querySelector('[data-credit-display="' + key + '"]') : null;
                                                    if (!line) return;

                                                    line.classList.toggle('d-none', !hasCredit);
                                                    var value = line.querySelector('.record-credited-value');
                                                    if (value) value.textContent = !hasCredit ? '' : (type === 'training' ? hoursText(credited) + ' hr' : monthsText(credited));
                                                }

                                                function saveCredit(form, reset) {
                                                    if (form.classList.contains('is-saving')) return;

                                                    var formData = new FormData(form);
                                                    if (reset) {
                                                        formData.set('credit_hours', '');
                                                        formData.set('credit_years', '');
                                                        formData.set('credit_months', '');
                                                    }
                                                    var state = form.querySelector('.credit-save-state');
                                                    var buttons = form.querySelectorAll('button');
                                                    var reviewTrigger = activeReviewTrigger;
                                                    var type = form.elements.record_type.value;

                                                    form.classList.remove('is-saved', 'has-error');
                                                    form.classList.add('is-saving');
                                                    Array.prototype.forEach.call(buttons, function (b) { b.disabled = true; });
                                                    if (state) state.innerHTML = '<i class="mdi mdi-loading mdi-spin mr-1"></i>Saving...';

                                                    window.fetch(form.action, {
                                                        method: 'POST',
                                                        body: formData,
                                                        credentials: 'same-origin',
                                                        headers: {
                                                            'X-Requested-With': 'XMLHttpRequest',
                                                            'Accept': 'application/json'
                                                        }
                                                    }).then(function (response) {
                                                        return response.json();
                                                    }).then(function (data) {
                                                        if (!data || !data.success) {
                                                            throw new Error(data && data.message ? data.message : 'The credited value could not be saved.');
                                                        }

                                                        updateCreditSummary(reviewTrigger, type, data.credited);
                                                        if (reviewTrigger) {
                                                            fillCreditForm(type, form.elements.record_id.value, form.elements.job_id.value,
                                                                reviewTrigger.getAttribute('data-computed') || '', reviewTrigger.getAttribute('data-credited') || '');
                                                        }
                                                        var total = document.querySelector('[data-relevance-total="' + data.record_type + '"]');
                                                        if (total && data.total_value) total.textContent = data.total_value;
                                                        form.classList.add('is-saved');
                                                        if (state) state.innerHTML = '<i class="mdi mdi-check-circle-outline mr-1"></i>' + safeMarkup(data.message || 'Saved.');
                                                    }).catch(function (error) {
                                                        form.classList.add('has-error');
                                                        if (state) state.textContent = error.message || 'Unable to save. Please try again.';
                                                    }).then(function () {
                                                        Array.prototype.forEach.call(buttons, function (b) { b.disabled = false; });
                                                        form.classList.remove('is-saving');
                                                    });
                                                }

                                                document.addEventListener('submit', function (e) {
                                                    var form = e.target;
                                                    if (!form || !form.classList || !form.classList.contains('js-record-credit-form')) return;
                                                    if (!window.fetch) return;
                                                    e.preventDefault();
                                                    saveCredit(form, false);
                                                });

                                                document.addEventListener('click', function (e) {
                                                    var button = e.target && e.target.closest ? e.target.closest('.js-credit-reset') : null;
                                                    if (!button || !window.fetch) return;
                                                    saveCredit(button.closest('.js-record-credit-form'), true);
                                                });

                                                function relevanceStatus(value) {
                                                    var parts = String(value || '').split(':');
                                                    return parts.length === 4 ? parts[3] : '0';
                                                }

                                                function paintRelevance(select, stat) {
                                                    select.classList.remove('relevance-status-0', 'relevance-status-1', 'relevance-status-2');
                                                    select.classList.add('relevance-status-' + stat);
                                                    var form = select.closest ? select.closest('.js-relevance-form') : null;
                                                    var segments = form ? form.querySelectorAll('.review-segment') : [];
                                                    Array.prototype.forEach.call(segments, function (segment) {
                                                        var active = segment.getAttribute('data-stat') === String(stat);
                                                        segment.classList.toggle('is-active', active);
                                                        segment.setAttribute('aria-pressed', active ? 'true' : 'false');
                                                    });
                                                }

                                                // The segmented buttons only pick the option; the select's change
                                                // handler below still does the saving.
                                                document.addEventListener('click', function (e) {
                                                    var segment = e.target && e.target.closest ? e.target.closest('.review-segment') : null;
                                                    if (!segment) return;
                                                    var form = segment.closest('.js-relevance-form');
                                                    var select = form ? form.querySelector('.js-relevance-select') : null;
                                                    var stat = parseInt(segment.getAttribute('data-stat'), 10);
                                                    if (!select || select.disabled || select.selectedIndex === stat) return;
                                                    select.selectedIndex = stat;
                                                    var changed = document.createEvent('HTMLEvents');
                                                    changed.initEvent('change', true, false);
                                                    select.dispatchEvent(changed);
                                                });

                                                function relevancePresentation(stat) {
                                                    if (String(stat) === '1') return { label: 'Relevant', tone: 'success' };
                                                    if (String(stat) === '2') return { label: 'Not Relevant', tone: 'danger' };
                                                    return { label: 'No Action', tone: 'secondary' };
                                                }

                                                function updateReviewSummary(trigger, stat) {
                                                    if (!trigger) return;

                                                    var oldStat = trigger.getAttribute('data-stat') || '0';
                                                    var type = trigger.getAttribute('data-record-type') || '';
                                                    trigger.setAttribute('data-stat', String(stat));
                                                    var summary = trigger.closest('.review-summary');
                                                    var badge = summary ? summary.querySelector('.review-status-badge') : null;
                                                    if (!badge) return;

                                                    var presentation = relevancePresentation(stat);
                                                    badge.classList.remove('badge-secondary', 'badge-success', 'badge-danger');
                                                    badge.classList.add('badge-' + presentation.tone);
                                                    badge.textContent = presentation.label;

                                                    // Keep the remark tint and the section counters in step.
                                                    var key = summary.getAttribute('data-review-row');
                                                    var note = key ? document.querySelector('[data-review-note="' + key + '"] .record-note') : null;
                                                    if (note) {
                                                        note.classList.remove('tone-0', 'tone-1', 'tone-2');
                                                        note.classList.add('tone-' + stat);
                                                        var noteStatus = note.querySelector('.record-note-status');
                                                        if (noteStatus) noteStatus.textContent = presentation.label;
                                                    }
                                                    if (String(oldStat) !== String(stat)) {
                                                        var from = document.querySelector('[data-relevance-count="' + type + ':' + oldStat + '"]');
                                                        var to = document.querySelector('[data-relevance-count="' + type + ':' + stat + '"]');
                                                        if (from) from.textContent = Math.max(0, (parseInt(from.textContent, 10) || 0) - 1);
                                                        if (to) to.textContent = (parseInt(to.textContent, 10) || 0) + 1;
                                                    }
                                                }

                                                document.addEventListener('change', function (e) {
                                                    var sw = e.target;
                                                    if (sw && sw.classList && sw.classList.contains('js-vacancy-switch') && sw.value) {
                                                        window.location.href = sw.value;
                                                    }
                                                });

                                                function updateRemarksSummary(trigger, remarks) {
                                                    if (!trigger) return;

                                                    trigger.setAttribute('data-remarks', encodeURIComponent(remarks || ''));
                                                    var summary = trigger.closest('.review-summary');
                                                    var key = summary ? summary.getAttribute('data-review-row') : '';
                                                    var noteRow = key ? document.querySelector('[data-review-note="' + key + '"]') : null;
                                                    var recordRow = summary ? summary.closest('tr') : null;
                                                    var noteText = noteRow ? noteRow.querySelector('.record-note-text') : null;
                                                    var hasRemarks = String(remarks || '').trim() !== '';

                                                    if (noteText) noteText.textContent = remarks || '';
                                                    if (noteRow) noteRow.classList.toggle('d-none', !hasRemarks);
                                                    if (recordRow) recordRow.classList.toggle('has-record-note', hasRemarks);
                                                }

                                                function updateRemarkCount(textarea) {
                                                    var counter = document.getElementById('record_review_count');
                                                    if (counter) counter.textContent = textarea ? String(textarea.value.length) : '0';
                                                }

                                                function safeMarkup(value) {
                                                    var element = document.createElement('span');
                                                    element.textContent = String(value || '');
                                                    return element.innerHTML;
                                                }

                                                // Relevance saves as soon as an evaluator changes the selection.
                                                document.addEventListener('change', function (e) {
                                                    var select = e.target;
                                                    if (!select || !select.classList || !select.classList.contains('js-relevance-select')) return;

                                                    var form = select.closest('.js-relevance-form');
                                                    if (!form || form.classList.contains('is-saving')) return;

                                                    var previous = select.getAttribute('data-previous-value') || select.value;
                                                    var formData = new FormData(form);
                                                    var state = form.querySelector('.relevance-save-state');
                                                    var reviewTrigger = activeReviewTrigger;
                                                    form.classList.remove('is-saved', 'has-error');
                                                    form.classList.add('is-saving');
                                                    select.disabled = true;
                                                    if (state) state.innerHTML = '<i class="mdi mdi-loading mdi-spin mr-1"></i>Saving';

                                                    if (!window.fetch) {
                                                        select.disabled = false;
                                                        form.submit();
                                                        return;
                                                    }

                                                    window.fetch(form.action, {
                                                        method: 'POST',
                                                        body: formData,
                                                        credentials: 'same-origin',
                                                        headers: {
                                                            'X-Requested-With': 'XMLHttpRequest',
                                                            'Accept': 'application/json'
                                                        }
                                                    }).then(function (response) {
                                                        return response.json();
                                                    }).then(function (data) {
                                                        if (!data || !data.success) {
                                                            var rejected = new Error(data && data.message ? data.message : 'Save failed.');
                                                            rejected.responseData = data;
                                                            throw rejected;
                                                        }

                                                        select.setAttribute('data-previous-value', select.value);
                                                        paintRelevance(select, String(data.stat));
                                                        updateReviewSummary(reviewTrigger, data.stat);
                                                        form.classList.add('is-saved');
                                                        if (state) state.innerHTML = '<i class="mdi mdi-check-circle-outline mr-1"></i>Saved';

                                                        var total = document.querySelector('[data-relevance-total="' + data.record_type + '"]');
                                                        if (total && data.total_value) total.textContent = data.total_value;
                                                    }).catch(function (error) {
                                                        select.value = previous;
                                                        paintRelevance(select, relevanceStatus(previous));
                                                        form.classList.add('has-error');
                                                        var responseData = error.responseData || {};
                                                        if (state) state.textContent = responseData.message || 'Save failed';
                                                    }).then(function () {
                                                        select.disabled = false;
                                                        form.classList.remove('is-saving');
                                                    });
                                                });

                                                // Remarks use an explicit save button so evaluators can finish a
                                                // thought before the note is written. Keep the typed text on errors
                                                // so a temporary connection problem never discards their work.
                                                document.addEventListener('submit', function (e) {
                                                    var form = e.target;
                                                    if (!form || !form.classList || !form.classList.contains('js-record-remarks-form')) return;
                                                    if (!window.fetch) return;

                                                    e.preventDefault();
                                                    if (form.classList.contains('is-saving')) return;

                                                    var textarea = form.querySelector('textarea[name="remarks"]');
                                                    var button = form.querySelector('button[type="submit"]');
                                                    var state = form.querySelector('.remarks-save-state');
                                                    var formData = new FormData(form);
                                                    var reviewTrigger = activeReviewTrigger;

                                                    form.classList.remove('is-saved', 'has-error');
                                                    form.classList.add('is-saving');
                                                    if (textarea) textarea.disabled = true;
                                                    if (button) button.disabled = true;
                                                    if (state) state.innerHTML = '<i class="mdi mdi-loading mdi-spin mr-1"></i>Saving...';

                                                    window.fetch(form.action, {
                                                        method: 'POST',
                                                        body: formData,
                                                        credentials: 'same-origin',
                                                        headers: {
                                                            'X-Requested-With': 'XMLHttpRequest',
                                                            'Accept': 'application/json'
                                                        }
                                                    }).then(function (response) {
                                                        return response.json();
                                                    }).then(function (data) {
                                                        if (!data || !data.success) {
                                                            throw new Error(data && data.message ? data.message : 'The remarks could not be saved.');
                                                        }

                                                        if (textarea && typeof data.remarks === 'string') textarea.value = data.remarks;
                                                        updateRemarksSummary(reviewTrigger, data.remarks || '');
                                                        updateRemarkCount(textarea);
                                                        form.classList.add('is-saved');
                                                        if (state) state.innerHTML = '<i class="mdi mdi-check-circle-outline mr-1"></i>' + safeMarkup(data.message || 'Remarks saved.');
                                                    }).catch(function (error) {
                                                        form.classList.add('has-error');
                                                        if (state) state.textContent = error.message || 'Unable to save. Please try again.';
                                                    }).then(function () {
                                                        if (textarea) textarea.disabled = false;
                                                        if (button) button.disabled = false;
                                                        form.classList.remove('is-saving');
                                                    });
                                                });

                                                document.addEventListener('input', function (e) {
                                                    var textarea = e.target;
                                                    if (!textarea || textarea.name !== 'remarks') return;
                                                    var form = textarea.closest ? textarea.closest('.js-record-remarks-form') : null;
                                                    if (!form || form.classList.contains('is-saving')) return;

                                                    form.classList.remove('is-saved', 'has-error');
                                                    var state = form.querySelector('.remarks-save-state');
                                                    if (state) state.textContent = '';
                                                    updateRemarkCount(textarea);
                                                });
                                            })();
                                        </script>

                                        <script>
                                        document.addEventListener('DOMContentLoaded', function () {
                                        if (window.location.hash) {
                                            const link = document.querySelector('.nav a[href="' + window.location.hash + '"]');
                                            if (link) $(link).tab('show'); // bootstrap tab
                                        }
                                        });
                                        </script>
