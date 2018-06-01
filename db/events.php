<?php

$observers = array(
    array(
        'eventname' => '\core\event\course_restored',
        'callback' => 'block_hubcourseinfo_observer::course_restored'
    ),
    array(
        'eventname' => '\core\event\course_deleted',
        'callback' => 'block_hubcourseinfo_observer::course_deleted'
    )
);