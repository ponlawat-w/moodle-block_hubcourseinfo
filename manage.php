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
 * Hubcourse management page for its owner/uploader
 *
 * @package block_hubcourseinfo
 * @copyright 2018 Moodle Association of Japan
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

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

$category = $DB->get_record('course_categories', ['id' => $course->category]);

$subject = $DB->get_record('block_hubcourse_subjects', ['id' => $hubcourse->subject]);

$metadatatable = new html_table();
$metadatatable->data = [
    [get_string('fullnamecourse'), $course->fullname],
    [get_string('shortnamecourse'), $course->shortname],
    [get_string('subject', 'block_hubcourseinfo'), $subject ? $subject->name : get_string('notknow', 'block_hubcourseinfo')],
    [get_string('tags', 'block_hubcourseinfo'), trim($hubcourse->tags) ? $hubcourse->tags : get_string('notknow', 'block_hubcourseinfo')],
    [get_string('category'), $category ? $category->name : get_string('notknow', 'block_hubcourseinfo')],
    [get_string('demourl', 'block_hubcourseinfo'), $hubcourse->demourl ? html_writer::link($hubcourse->demourl, $hubcourse->demourl, ['target' => '_blank']) : get_string('notknow', 'block_hubcourseinfo')],
    [get_string('description'), $hubcourse->description ? nl2br(htmlspecialchars($hubcourse->description)) : get_string('notknow', 'block_hubcourseinfo')],
];
$metadatafields = block_hubcourseinfo_getmetadatafields();
foreach ($metadatafields as $metadatafield) {
  $metadatavalue = $DB->get_record('block_hubcourse_metavalues', ['hubcourseid' => $hubcourse->id, 'fieldid' => $metadatafield->id]);
  if ($metadatavalue) {
    $metadatatable->data[] = [$metadatafield->name, $metadatavalue->value];
  }
}

$versiontable = new html_table();
$versiontable->head = [
    get_string('timeuploaded', 'block_hubcourseinfo'),
    get_string('description'),
    get_string('downloads', 'block_hubcourseinfo'),
    get_string('action')
];
$versiontable->data = [];

$versions = $DB->get_records('block_hubcourse_versions', ['hubcourseid' => $hubcourse->id], 'id ASC');
foreach ($versions as $version) {
    $stable = ($hubcourse->stableversion == $version->id);
    $stabletext = '';
    if ($stable) {
        $stabletext = ' ' . html_writer::tag('span', '(' . get_string('current', 'block_hubcourseinfo') . ')', ['class' => 'label label-info']);

        $applybutton = html_writer::link(new moodle_url('/blocks/hubcourseupload/restore.php', ['version' => $version->id]),
            html_writer::tag('i','', ['class' => 'fa fa-refresh']) . ' ' . get_string('reset', 'block_hubcourseinfo'),
            ['class' => 'btn btn-sm btn-default', 'title' => get_string('reset_description', 'block_hubcourseinfo')]) . ' ';
    } else {
        $applybutton = html_writer::link(new moodle_url('/blocks/hubcourseupload/restore.php', ['version' => $version->id]),
            html_writer::tag('i','', ['class' => 'fa fa-circle']) . ' ' . get_string('apply', 'block_hubcourseinfo'),
            ['class' => 'btn btn-sm btn-default', 'title' => get_string('apply_description', 'block_hubcourseinfo')]) . ' ';
    }

    if (!block_hubcourseinfo_uploadblockenabled()) {
        $applybutton = '';
    }

    $editbutton = html_writer::link(new moodle_url('/blocks/hubcourseinfo/version/edit.php', ['id' => $version->id]),
        html_writer::tag('i', '', ['class' => 'fa fa-edit']) . ' ' . get_string('editdelete', 'block_hubcourseinfo'),
        ['class' => 'btn btn-sm btn-default']);
    $rebuildbutton = html_writer::link(new moodle_url('/blocks/hubcourseinfo/version/rebuild.php', ['vid' => $version->id]),
      html_writer::tag('i', '', ['class' => 'fa fa-archive']) . ' ' . get_string('rebuild', 'block_hubcourseinfo'),
      ['class' => 'btn btn-sm btn-default', 'title' => get_string('rebuild_description', 'block_hubcourseinfo')]);
    $downloadbutton = html_writer::link(new moodle_url('/blocks/hubcourseinfo/download.php', ['version' => $version->id]),
        html_writer::tag('i', '', ['class' => 'fa fa-download']) . ' ' . get_string('download'),
        ['class' => 'btn btn-sm btn-default']);

    $versiontable->data[] = [
        userdate($version->timeuploaded, get_string('strftimedatetimeshort', 'langconfig')) . $stabletext,
        $version->description,
        number_format($DB->count_records('block_hubcourse_downloads', ['versionid' => $version->id]), 0),
        $editbutton . $applybutton . $rebuildbutton . $downloadbutton
    ];
}

$PAGE->set_context($hubcoursecontext);
$PAGE->set_pagelayout('standard');
$PAGE->set_url('/blocks/hubcourseinfo/manage.php', ['id' => $hubcourse->id]);
$PAGE->set_title($course->fullname . ' - ' . get_string('managehubcourse', 'block_hubcourseinfo'));
$PAGE->set_heading($PAGE->title);
$PAGE->navbar->add(get_string('managehubcourse', 'block_hubcourseinfo'));

echo $OUTPUT->header();

echo html_writer::tag('h3', get_string('metadata', 'block_hubcourseinfo'));
echo html_writer::table($metadatatable);
echo html_writer::link(new moodle_url('/blocks/hubcourseinfo/metadata/edit.php', ['id' => $hubcourse->id]),
    html_writer::tag('i', '', ['class' => 'fa fa-edit']) . ' ' . get_string('editmetadata', 'block_hubcourseinfo'),
    ['class' => 'btn btn-primary']);
echo ' ';
echo html_writer::link(new moodle_url('/blocks/hubcourseinfo/metadata/export.php', ['id' => $hubcourse->id]),
  html_writer::tag('i', '', ['class' => 'fa fa-upload']) . ' ' . get_string('exportmetadata', 'block_hubcourseinfo'),
    ['class' => 'btn btn-success']);

echo html_writer::tag('hr', '');

echo html_writer::tag('h3', get_string('manageversion', 'block_hubcourseinfo'));
echo html_writer::table($versiontable);
$maxversion = get_config('block_hubcourseinfo', 'maxversionamount');
if (count($versions) < $maxversion) {
    echo html_writer::link(new moodle_url('/blocks/hubcourseinfo/version/rebuild.php', ['hid' => $hubcourse->id]),
      html_writer::tag('i', '', ['class' => 'fa fa-archive']) . ' ' . get_string('rebuildasnewversion', 'block_hubcourseinfo'),
      ['class' => 'btn btn-success', 'title' => get_string('rebuildasnewversion_description', 'block_hubcourseinfo')]);
    echo ' ';
    echo html_writer::link(new moodle_url('/blocks/hubcourseinfo/version/add.php', ['id' => $hubcourse->id]),
        html_writer::tag('i', '', ['class' => 'fa fa-plus']) . ' ' . get_string('addversion', 'block_hubcourseinfo'),
        ['class' => 'btn btn-success']);
} else {
    echo html_writer::tag('i', get_string('maxversionamountexceed', 'block_hubcourseinfo', $maxversion));
}
echo html_writer::tag('hr', '');

if (has_capability('block/hubcourseinfo:deletehubcourse', context_system::instance())) {
  echo html_writer::link(new moodle_url('/blocks/hubcourseinfo/delete.php', ['id' => $hubcourse->id]),
    html_writer::tag('i', '', ['class' => 'fa fa-trash']) . ' ' . get_string('deletehubcourse', 'block_hubcourseinfo'),
    ['class' => 'btn btn-danger']);
}

$cap_importfrommajhub = has_capability('block/hubcourseinfo:importfrommajhub', context_system::instance()) && block_hubcourseinfo_uploadblockenabled();
$cap_truncate = has_capability('block/hubcourseinfo:truncate', context_system::instance());
if ($cap_importfrommajhub || $cap_truncate) {
    echo html_writer::tag('hr', '');

    echo html_writer::tag('h3', get_string('siteadmin', 'block_hubcourseinfo'));

    if ($cap_importfrommajhub) {
        echo html_writer::link(new moodle_url('/blocks/hubcourseinfo/admin/majimport.php', ['id' => $hubcourseid]),
            html_writer::tag('i', '', ['class' => 'fa fa-download']) . ' ' . get_string('importfrommajhub', 'block_hubcourseinfo'),
            ['class' => 'btn btn-primary']);
        echo ' ';
    }
    if ($cap_truncate) {
        echo html_writer::link(new moodle_url('/blocks/hubcourseinfo/admin/truncate.php', ['id' => $hubcourseid]),
            html_writer::tag('i', '', ['class' => 'fa fa-trash']) . ' ' . get_string('clearhubcoursedata', 'block_hubcourseinfo'),
            ['class' => 'btn btn-danger']);
    }
}

echo $OUTPUT->footer();