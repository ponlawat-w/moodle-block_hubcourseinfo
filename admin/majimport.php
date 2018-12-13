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
 * Admin import from MAJ Hub
 *
 *  Import course data from moodle-local_majhub
 *
 * @package block_hubcourseinfo
 * @copyright 2018 Moodle Association of Japan
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/../lib.php');
require_once(__DIR__ . '/classes/majimportconfirm_form.php');

require_login();
require_capability('block/hubcourseinfo:importfrommajhub', context_system::instance());

if (!block_hubcourseinfo_uploadblockenabled()) {
    throw new moodle_exception('block_hubcourseupload is required in this site to perform this action.');
}

$hubcourseid = required_param('id', PARAM_INT);

$hubcourse = $DB->get_record('block_hubcourses', ['id' => $hubcourseid]);
if (!$hubcourse) {
    throw new moodle_exception('hubcourse not found', 'block_hubcourseinfo');
}

$confirmform = new majimportconfirm_form($hubcourse);
if ($confirmform->is_submitted()) {

    if ($confirmform->is_cancelled()) {
        redirect(new moodle_url('/blocks/hubcourseinfo/manage.php', ['id' => $hubcourse->id]));
        exit;
    }

    if (block_hubcourseinfo_majimport($hubcourse)) {
        redirect(new moodle_url('/blocks/hubcourseinfo/manage.php', ['id' => $hubcourse->id]));
    } else {
        throw new moodle_exception('Cannot import data', 'block_hubcourseinfo');
    }
}

$PAGE->set_context(block_hubcourseinfo_getcontextfromhubcourse($hubcourse));
$PAGE->set_pagelayout('standard');
$PAGE->set_url('/blocks/hubcourseinfo/admin/truncate.php', ['id' => $hubcourse->id]);
$PAGE->set_title(get_string('truncateconfirm', 'block_hubcourseinfo'));
$PAGE->set_heading($PAGE->title);
$PAGE->navbar->add(get_string('managehubcourse', 'block_hubcourseinfo'),
        new moodle_url('/blocks/hubcourseinfo/manage.php', ['id' => $hubcourse->id]))
    ->add(get_string('majimportconfirm', 'block_hubcourseinfo'));

echo $OUTPUT->header();

$confirmform->display();

echo $OUTPUT->footer();
