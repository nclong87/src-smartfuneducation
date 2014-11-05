<?php

defined('MOODLE_INTERNAL') || die();
$messageproviders = array (
    // Notify player has request evaluation
    'evaluation' => array (
        'capability'  => 'mod/rtw:emailnotifyevaluation'
    )
);

$messageproviders = array(
    // Notify teacher that a student has submitted a quiz attempt.
    'submission' => array(
        'capability' => 'mod/quiz:emailnotifysubmission'
    ),

    // Confirm a student's quiz attempt.
    'confirmation' => array(
        'capability' => 'mod/quiz:emailconfirmsubmission'
    ),

    // Warning to the student that their quiz attempt is now overdue, if the quiz
    // has a grace period.
    'attempt_overdue' => array(
        'capability' => 'mod/quiz:emailwarnoverdue'
    ),
);