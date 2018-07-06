<?php
$capabilities = array(
    'block/hubcourseinfo:addinstance' => array(
        'riskbitmask' => 0,
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
            'user' => CAP_ALLOW
        )
    ),
    'block/hubcourseinfo:managesubjects' => array(
        'riskbitmask' => 0,
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
            'admin' => CAP_ALLOW
        )
    ),
    'block/hubcourseinfo:managecourse' => array(
        'riskbitmask' => RISK_XSS | RISK_SPAM | RISK_PERSONAL,
        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'manager' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW
        )
    ),
    'block/hubcourseinfo:viewlikes' => array(
        'riskbitmask' => 0,
        'captype' => 'read',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'guest' => CAP_ALLOW,
            'user' => CAP_ALLOW
        )
    ),
    'block/hubcourseinfo:viewreviews' => array(
        'riskbitmask' => 0,
        'captype' => 'read',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'guest' => CAP_ALLOW,
            'user' => CAP_ALLOW
        )
    ),
    'block/hubcourseinfo:submitlike' => array(
        'riskbitmask' => RISK_SPAM,
        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'user' => CAP_ALLOW
        )
    ),
    'block/hubcourseinfo:submitreview' => array(
        'riskbitmask' => RISK_XSS | RISK_SPAM,
        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'user' => CAP_ALLOW
        )
    ),
    'block/hubcourseinfo:downloadcourse' => array(
        'riskbitmask' => RISK_SPAM,
        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'user' => CAP_ALLOW
        )
    )
);