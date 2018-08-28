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
 * Downloading a hubcourse version
 *
 * @package block_hubcourseinfo
 * @copyright 2018 Moodle Association of Japan
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

$versionid = required_param('version', PARAM_INT);
$version = $DB->get_record('block_hubcourse_versions', ['id' => $versionid]);
if (!$version) {
    throw new Exception(get_string('notknow', 'block_hubcourseinfo'));
}
$hubcourse = $DB->get_record('block_hubcourses', ['id' => $version->hubcourseid]);
if (!$hubcourse) {
    throw new Exception(get_string('notknow', 'block_hubcourseinfo'));
}

$hubcoursecontext = block_hubcourseinfo_getcontextfromhubcourse($hubcourse);

require_capability('block/hubcourseinfo:downloadcourse', $hubcoursecontext);

$fs = get_file_storage();

$files = $fs->get_area_files($hubcoursecontext->id, 'block_hubcourse', 'course', $versionid);
foreach ($files as $file) {
    $exts = explode('.', $file->get_filename());
    $ext = $exts[count($exts) - 1];

    if ($ext == 'mbz') {

        if ($version->userid != $USER->id) {
            $download = new stdClass();
            $download->id = 0;
            $download->versionid = $version->id;
            $download->userid = $USER->id;
            $download->timedownloaded = time();
            $DB->insert_record('block_hubcourse_downloads', $download);
        }

        session_write_close();

        header('Content-Type: ' . $file->get_mimetype());
        header('Content-Length: ' . $file->get_filesize());
        header('Content-Disposition: attachment; filename="' . $file->get_filename() . '"');

        $fstream = $file->get_content_file_handle();
        while (!feof($fstream)) {
            echo fgets($fstream);
        }
        fclose($fstream); exit;
    }
}

throw new Exception('unexpected');