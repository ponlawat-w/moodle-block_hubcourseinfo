<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $settings->add(
        new admin_setting_configtext(
            'maxfilesize',
            get_string('settings:maxfilesize', 'block_hubcourseinfo'),
            get_string('settings:maxfilesize_description', 'block_hubcourseinfo'),
            '50', PARAM_INT)
    );

    $settings->add(
        new admin_setting_configtext(
            'maxversionamount',
            get_string('settings:maxversionamount', 'block_hubcourseinfo'),
            get_string('settings:maxversionamount_description', 'block_hubcourseinfo'),
            '10', PARAM_INT)
    );
}

