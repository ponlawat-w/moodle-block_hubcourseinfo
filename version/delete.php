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
 * Deleting a version of hubcourse
 *
 * @package block_hubcourseinfo
 * @copyright 2018 Moodle Association of Japan
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/../lib.php');
require_once(__DIR__ . '/../classes/deleteversionconfirm_form.php');
require_once(__DIR__ . '/../../../backup/util/includes/restore_includes.php');

$versionid = required_param('id', PARAM_INT);

$version  = $DB->get_record('block_hubcourse_versions', ['id' => $versionid]);
if (!$version) {
    throw new Exception(get_string('hubcoursenotfound', 'block_hubocurseinfo'));
}

$hubcourse = $DB->get_record('block_hubcourses', ['id' => $version->hubcourseid]);
if (!$hubcourse) {
    throw new Exception(get_string('hubcoursenotfound', 'block_hubocurseinfo'));
}

$hubcoursecontext = block_hubcourseinfo_getcontextfromhubcourse($hubcourse);

$coursecontext = $hubcoursecontext->get_course_context();
$course = $DB->get_record('course', ['id' => $coursecontext->instanceid]);
if (!$course) {
    throw new Exception(get_string('hubcoursenotfound', 'block_hubcourseinfo'));
}

require_login($course);
require_capability('block/hubcourseinfo:managecourse', $hubcoursecontext);

if ($hubcourse->stableversion == $version->id) {
    throw new Exception(get_string('error_cannotdeletestableversion', 'block_hubcourseinfo'));
}

$PAGE->set_context($hubcoursecontext);
$PAGE->set_pagelayout('standard');
$PAGE->set_url('/blocks/hubcourseinfo/version/delete.php', ['id' => $version->id]);
$PAGE->set_title($course->fullname . ' - ' . get_string('deleteversion', 'block_hubcourseinfo'));
$PAGE->set_heading($PAGE->title);
$PAGE->navbar->add(get_string('managehubcourse', 'block_hubcourseinfo'), new moodle_url('/blocks/hubcourseinfo/manage.php', ['id' => $hubcourse->id]))
    ->add(get_string('deleteversion', 'block_hubcourseinfo'));

echo $OUTPUT->header();

$form = new deleteversionconfirm_form($version);

if ($form->is_submitted()) {
    if ($form->is_cancelled()) {
        redirect(new moodle_url('/blocks/hubcourseinfo/manage.php', ['id' => $hubcourse->id]));
        exit;
    } else if ($form->is_validated()) {
        block_hubcourseinfo_deleteversion($version, $hubcoursecontext->id);

        echo html_writer::div(html_writer::tag('i', '', ['class' => 'fa fa-check']) . ' ' . get_string('versiondeleted', 'block_hubcourseinfo'), 'alert alert-success');
        echo html_writer::tag('p',
            html_writer::link(new moodle_url('/blocks/hubcourseinfo/manage.php', ['id' => $hubcourse->id]), get_string('continue'), ['class' => 'btn btn-primary']));
    }
} else {
    $form->display();
}

echo $OUTPUT->footer();