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

    $settings->add(
        new admin_setting_configempty(
            'block_hubcourseinfo/subjects',
            get_string('settings:subjects', 'block_hubcourseinfo'),
            html_writer::link(
                new moodle_url('/blocks/hubcourseinfo/admin/subjects.php'),
                html_writer::tag('i', '', ['class' => 'fa fa-pencil']) . ' ' . get_string('managesubjectslink', 'block_hubcourseinfo'),
                ['class' => 'text-primary', 'target' => '_blank', 'style' => 'margin-left: 15px;'])
        )
    );
}

