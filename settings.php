<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $settings->add(
        new admin_setting_configcheckbox(
            'block_hubcourseinfo/autocreateinfoblock',
            get_string('settings:autocreateinfoblock', 'block_hubcourseinfo'),
            get_string('settings:autocreateinfoblock_decription', 'block_hubcourseinfo'),
            true, true, false
        )
    );

    $settings->add(
        new admin_setting_configtext(
            'block_hubcourseinfo/maxversionamount',
            get_string('settings:maxversionamount', 'block_hubcourseinfo'),
            get_string('settings:maxversionamount_description', 'block_hubcourseinfo'),
            '3', PARAM_INT)
    );
}

