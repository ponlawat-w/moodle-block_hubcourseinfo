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
 * Admin edit subject page
 *
 *  Page to show form and manipulate the action when admin edit a subject
 *
 * @package block_hubcourseinfo
 * @copyright 2018 Moodle Association of Japan
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/classes/subject_form.php');

require_login();
require_capability('block/hubcourseinfo:managesubjects', context_system::instance());

$editsubjectform = new subject_form(-1);
if ($editsubjectform->is_submitted()) {
    if ($editsubjectform->is_cancelled()) {
        redirect(new moodle_url('/blocks/hubcourseinfo/admin/subjects.php'));
    } else if ($editsubjectform->is_validated()) {
        $data = $editsubjectform->get_data();
        $subject = new stdClass();
        $subject->id = $data->id;
        $subject->name = $data->name;

        if ($DB->update_record('block_hubcourse_subjects', $subject)) {
            redirect(new moodle_url('/blocks/hubcourseinfo/admin/subjects.php'));
        } else {
            throw new moodle_exception('Cannot update subject data', 'block_hubcourseinfo');
        }
    }
} else {
    $subjectid = required_param('id', PARAM_INT);
    $subject = $DB->get_record('block_hubcourse_subjects', ['id' => $subjectid]);
    if (!$subject) {
        throw new moodle_exception('Subject not found', 'block_hubcourseinfo');
    }

    $editsubjectform = new subject_form($subject->id, $subject->name);
}

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
    ->add(get_string('managesubjects', 'block_hubcourseinfo'), new moodle_url('/blocks/hubcourseinfo/admin/subjects.php'))
    ->add(get_string('editsubject', 'block_hubcourseinfo', $subject->name));

echo $OUTPUT->header();

echo html_writer::tag('h4', get_string('editsubject', 'block_hubcourseinfo', $subject->name));
$editsubjectform->display();

echo $OUTPUT->footer();