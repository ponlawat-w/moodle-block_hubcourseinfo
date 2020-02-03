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
 * Admin truncate hubcourse data
 *
 *  Delete all hubcourse data but preserve course existence
 *
 * @package block_hubcourseinfo
 * @copyright 2018 Moodle Association of Japan
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/../lib.php');
require_once(__DIR__ . '/classes/truncateconfirm_form.php');

require_login();
require_capability('block/hubcourseinfo:truncate', context_system::instance());

$hubcourseid = required_param('id', PARAM_INT);

$hubcourse = $DB->get_record('block_hubcourses', ['id' => $hubcourseid]);
if (!$hubcourse) {
    throw new moodle_exception('hubcourse not found', 'block_hubcourseinfo');
}

$PAGE->set_context(block_hubcourseinfo_getcontextfromhubcourse($hubcourse));
$PAGE->set_url('/blocks/hubcourseinfo/admin/truncate.php', ['id' => $hubcourse->id]);

$confirmform = new truncateconfirm_form($hubcourse);
if ($confirmform->is_submitted()) {
    if ($confirmform->is_cancelled()) {
        redirect(new moodle_url('/blocks/hubcourseinfo/manage.php', ['id' => $hubcourse->id]));
        exit;
    }

    if (block_hubcourseinfo_fulldelete($hubcourse)) {
        redirect(new moodle_url('/course/view.php', ['id' => $hubcourse->courseid]));
    } else {
        throw new moodle_exception('Cannot truncate hubcourse', 'block_hubcourseinfo');
    }
}

$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('truncateconfirm', 'block_hubcourseinfo'));
$PAGE->set_heading($PAGE->title);
$PAGE->navbar->add(get_string('managehubcourse', 'block_hubcourseinfo'),
        new moodle_url('/blocks/hubcourseinfo/manage.php', ['id' => $hubcourse->id]))
    ->add(get_string('truncateconfirm', 'block_hubcourseinfo'));

echo $OUTPUT->header();

$confirmform->display();

echo $OUTPUT->footer();
