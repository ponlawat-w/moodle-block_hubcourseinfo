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
 * Admin delete subject page
 *
 *  Page to show confirmation form and manipulate the action when admin delete course subject
 *
 * @package block_hubcourseinfo
 * @copyright 2018 Moodle Association of Japan
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/classes/deletesubjectconfirm_form.php');

require_login();
require_capability('block/hubcourseinfo:managesubjects', context_system::instance());

$subjectid = required_param('id', PARAM_INT);

$subject = $DB->get_record('block_hubcourse_subjects', ['id' => $subjectid]);
if (!$subject) {
    throw new moodle_exception('Subject not found', 'block_hubcourseinfo');
}

$confirmform = new deletesubjectconfirm_form($subject);
if ($confirmform->is_submitted()) {
    if ($confirmform->is_cancelled()) {
        redirect(new moodle_url('/blocks/hubcourseinfo/admin/subjects.php'));
        exit;
    }

    if (!$DB->execute('UPDATE {block_hubcourses} SET subject = 0 WHERE subject = ?', [$subject->id])) {
        throw new moodle_exception('Cannot move courses in subject to non-subject');
    }
    if ($DB->delete_records('block_hubcourse_subjects', ['id' => $subject->id])) {
        redirect(new moodle_url('/blocks/hubcourseinfo/admin/subjects.php'));
        exit;
    } else {
        throw new moodle_exception('Cannt delete subject', 'block_hubcourseinfo');
    }
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
    ->add(get_string('deletesubjectconfirm', 'block_hubcourseinfo'));

echo $OUTPUT->header();

$confirmform->display();

echo $OUTPUT->footer();