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
 * Add a new version of hubcourse
 *
 * @package block_hubcourseinfo
 * @copyright 2018 Moodle Association of Japan
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/../lib.php');
require_once(__DIR__ . '/../classes/version_form.php');
require_once(__DIR__ . '/../../../backup/util/includes/restore_includes.php');

$hubcourseid = required_param('id', PARAM_INT);
$hubcourse = $DB->get_record('block_hubcourses', ['id' => $hubcourseid]);
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

if (!block_hubcourseinfo_cancreateversion($hubcourse)) {
    throw new Exception(get_string('error_maxversionamountexceed', 'block_hubcourseinfo'));
}

$form = new version_form($hubcourse->id, 0, false);

if ($form->is_cancelled()) {
    redirect(new moodle_url('/blocks/hubcourseinfo/manage.php', ['id' => $hubcourse->id]));
    exit;
} else if ($form->is_submitted() && $form->is_validated()) {

    $tempname = restore_controller::get_tempdir_name($hubcourse->courseid, $USER->id);
    $path = block_hubcourseinfo_getbackuppath($tempname);

    if (!$form->save_file('coursefile', $path)) {
        fulldelete($path);
        throw new Exception(get_string('error_cannotreadfile', 'block_hubcourseinfo'));
        return $errors;
    }

    $info = backup_general_helper::get_backup_information_from_mbz($path);
    if (!$info) {
        fulldelete($path);
        throw new Exception(get_string('error_cannotreadfile', 'block_hubcourseinfo'));
    }

    if ($info->type != 'course') {
        fulldelete($path);
        throw new Exception(get_string('error_notcoursebackupfile', 'block_hubcourseinfo'));
    }

    $data = $form->get_data();

    $version = new stdClass();
    $version->id = 0;
    $version->hubcourseid = $hubcourse->id;
    $version->moodleversion = $info->moodle_version;
    $version->moodlerelease = $info->moodle_release;
    $version->description = $data->description;
    $version->userid = $USER->id;
    $version->timeuploaded = time();
    $version->fileid = 0;

    $versionid = $DB->insert_record('block_hubcourse_versions', $version);
    if (!$versionid) {
        throw new Exception(get_string('error'));
    }

    if (block_hubcourseinfo_uploadblockenabled()) {
        block_hubcourseinfo_savembzdependencies($hubcourse->courseid, $versionid, $path);
    }

    if (!$form->save_stored_file('coursefile', $hubcoursecontext->id, 'block_hubcourse', 'course', $versionid, '/')) {
        throw new Exception(get_string('error'));
    }

    redirect(new moodle_url('/blocks/hubcourseinfo/manage.php', ['id' => $hubcourse->id]));
    exit;
}

$PAGE->set_context($hubcoursecontext);
$PAGE->set_pagelayout('standard');
$PAGE->set_url('/blocks/hubcourseinfo/version/add.php', ['id' => $hubcourse->id]);
$PAGE->set_title($course->fullname . ' - ' . get_string('addversion', 'block_hubcourseinfo'));
$PAGE->set_heading($PAGE->title);
$PAGE->navbar->add(get_string('managehubcourse', 'block_hubcourseinfo'), new moodle_url('/blocks/hubcourseinfo/manage.php', ['id' => $hubcourse->id]))
    ->add(get_string('addversion', 'block_hubcourseinfo'));

echo $OUTPUT->header();

$form->display();

echo $OUTPUT->footer();