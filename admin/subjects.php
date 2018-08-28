<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Admin subjects list page
 *
 *  Page to list all subjects
 *
 * @package block_hubcourseinfo
 * @copyright 2018 Moodle Association of Japan
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/classes/subject_form.php');

require_login();
require_capability('block/hubcourseinfo:managesubjects', context_system::instance());

$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_url('/blocks/hubcourseinfo/admin/subjects.php');
$PAGE->set_title(get_string('managesubjects', 'block_hubcourseinfo'));
$PAGE->set_heading($PAGE->title);

$PAGE->navbar
    ->add(get_string('administrationsite'), new moodle_url('/admin/search.php'))
    ->add(get_string('plugins', 'admin'), new moodle_url('/admin/category.php', ['category' => 'modules']))
    ->add(get_string('blocks'), new moodle_url('/admin/category.php', ['category' => 'blocksettings']))
    ->add(get_string('pluginname', 'block_hubcourseinfo'), new moodle_url('/admin/settings.php', ['section' => 'blocksettinghubcourseinfo']))
    ->add(get_string('managesubjects', 'block_hubcourseinfo'));

$subjects = $DB->get_records('block_hubcourse_subjects', [], 'name ASC');
$subjectstable = new html_table();
$subjectstable->head = [
    get_string('subjectname', 'block_hubcourseinfo'),
    '', ''
];
$subjectstable->data = [];
foreach ($subjects as $subject) {
    $subjectstable->data[] = [
        $subject->name,
        html_writer::link(new moodle_url('/blocks/hubcourseinfo/admin/editsubject.php', ['id' => $subject->id]), html_writer::tag('i', '', ['class' => 'fa fa-pencil'])),
        html_writer::link(new moodle_url('/blocks/hubcourseinfo/admin/deletesubject.php', ['id' => $subject->id]), html_writer::tag('i', '', ['class' => 'fa fa-trash']))
    ];
}
$subjectstable->attributes['class'] = 'table';
$subjectstable->attributes['style'] = 'width: auto !important;';

$newsubjectform = new subject_form();
if ($newsubjectform->is_submitted() && $newsubjectform->is_validated()) {
    $data = $newsubjectform->get_data();

    $subject = new stdClass();
    $subject->id = 0;
    $subject->name = $data->name;
    if ($DB->insert_record('block_hubcourse_subjects', $subject)) {
        redirect($PAGE->url);
    } else {
        throw new moodle_exception('Cannot add new subject', 'block_hubcourseinfo');
    }
}

echo $OUTPUT->header();

if ($subjects && count($subjects)) {
    echo html_writer::tag('h4', get_string('coursesubjects', 'block_hubcourseinfo'));
    echo html_writer::table($subjectstable);
    echo html_writer::start_tag('hr');
}

echo html_writer::tag('h4', get_string('newsubject', 'block_hubcourseinfo'));
$newsubjectform->display();

echo $OUTPUT->footer();