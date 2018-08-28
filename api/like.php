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
 * Like API
 *
 *  API to handle user's like action
 *
 * @package block_hubcourseinfo
 * @copyright 2018 Moodle Association of Japan
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);
require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/../lib.php');

$hubcourseid = required_param('hubcourseid', PARAM_INT);
$result = ['success' => false, 'html' => ''];

$hubcourse = $DB->get_record('block_hubcourses', ['id' => $hubcourseid]);

if ($hubcourse) {
    $blockcontext = block_hubcourseinfo_getcontextfromhubcourse($hubcourse);

    if ($blockcontext && has_capability('block/hubcourseinfo:submitlike', $blockcontext)) {
        $like = $DB->get_record('block_hubcourse_likes', ['hubcourseid' => $hubcourseid, 'userid' => $USER->id]);

        $queryresult = false;
        if ($like) {
            $queryresult = $DB->delete_records('block_hubcourse_likes', ['id' => $like->id]);
        } else {
            $newlike = new stdClass();
            $newlike->id = 0;
            $newlike->hubcourseid = $hubcourseid;
            $newlike->userid = $USER->id;
            $newlike->timecreated = time();
            $queryresult = $DB->insert_record('block_hubcourse_likes', $newlike);
        }

        if ($queryresult) {
            $result['success'] = true;
            $result['html'] = block_hubcourseinfo_renderlike($hubcourse, $blockcontext);
        }
    }
}

echo json_encode($result);