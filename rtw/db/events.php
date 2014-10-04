<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// List of observers.
$observers = array(
    array(
        'eventname'   => '\core\event\user_enrolment_created',
        'callback'    => 'mod_rtw_observer::user_enrolment_created',
    ),
    array(
        'eventname'   => '\core\event\user_enrolment_deleted',
        'callback'    => 'mod_rtw_observer::user_enrolment_deleted',
    )
);
