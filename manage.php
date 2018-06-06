<?php
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

$category = $DB->get_record('course_categories', ['id' => $course->category]);

require_login($course);
require_capability('block/hubcourseinfo:managecourse', $hubcoursecontext);

$metadatatable = new html_table();
$metadatatable->data = [
    [get_string('fullnamecourse'), $course->fullname],
    [get_string('shortnamecourse'), $course->shortname],
    [get_string('category'), $category ? $category->name : get_string('notknow', 'block_hubcourseinfo')],
    [get_string('demourl', 'block_hubcourseinfo'), $hubcourse->demourl ? html_writer::link($hubcourse->demourl, $hubcourse->demourl, ['target' => '_blank']) : get_string('notknow', 'block_hubcourseinfo')],
    [get_string('description'), $hubcourse->description ? nl2br(htmlspecialchars($hubcourse->description)) : get_string('notknow', 'block_hubcourseinfo')],
];

$versiontable = new html_table();
$versiontable->head = [
    get_string('timeuploaded', 'block_hubcourseinfo'),
    get_string('description'),
    get_string('downloads', 'block_hubcourseinfo'),
    get_string('action')
];
$versiontable->data = [];

$versions = $DB->get_records('block_hubcourse_versions', ['hubcourseid' => $hubcourse->id], 'timeuploaded ASC');
foreach ($versions as $version) {
    $stable = ($hubcourse->stableversion == $version->id);
    $stabletext = '';
    if ($stable) {
        $stabletext = ' ' . html_writer::tag('span', get_string('current', 'block_hubcourseinfo'), ['class' => 'label label-info']);

        $applybutton = html_writer::link(new moodle_url('/blocks/hubcourseupload/restore.php', ['version' => $version->id]),
            html_writer::tag('i','', ['class' => 'fa fa-refresh']) . ' ' . get_string('reset', 'block_hubcourseinfo'),
            ['class' => 'btn btn-sm btn-default']) . ' ';
    } else {
        $applybutton = html_writer::link(new moodle_url('/blocks/hubcourseupload/restore.php', ['version' => $version->id]),
            html_writer::tag('i','', ['class' => 'fa fa-circle']) . ' ' . get_string('apply', 'block_hubcourseinfo'),
            ['class' => 'btn btn-sm btn-default']) . ' ';
    }

    if (!block_hubcourseinfo_uploadblockenabled()) {
        $applybutton = '';
    }

    $editbutton = html_writer::link(new moodle_url('/blocks/hubcourseinfo/version/edit.php', ['id' => $version->id]),
        html_writer::tag('i', '', ['class' => 'fa fa-edit']) . ' ' . get_string('edit'),
        ['class' => 'btn btn-sm btn-default']);
    $downloadbutton = html_writer::link(new moodle_url('/blocks/hubcourseinfo/download.php', ['version' => $version->id]),
        html_writer::tag('i', '', ['class' => 'fa fa-download']) . ' ' . get_string('download'),
        ['class' => 'btn btn-sm btn-default']);

    $versiontable->data[] = [
        userdate($version->timeuploaded) . $stabletext,
        $version->description,
        number_format($DB->count_records('block_hubcourse_downloads', ['versionid' => $version->id]), 0),
        $editbutton . ' ' . $applybutton . $downloadbutton
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

echo html_writer::tag('hr');

echo html_writer::tag('h3', get_string('manageversion', 'block_hubcourseinfo'));
echo html_writer::table($versiontable);
$maxversion = get_config('block_hubcourseinfo', 'maxversionamount');
if (count($versions) < $maxversion) {
    echo html_writer::link(new moodle_url('/blocks/hubcourseinfo/version/add.php', ['id' => $hubcourse->id]),
        html_writer::tag('i', '', ['class' => 'fa fa-plus']) . ' ' . get_string('addversion', 'block_hubcourseinfo'),
        ['class' => 'btn btn-success']);
} else {
    echo html_writer::tag('i', get_string('maxversionamountexceed', 'block_hubcourseinfo', $maxversion));
}
echo html_writer::tag('hr');

echo html_writer::link(new moodle_url('/blocks/hubcourseinfo/delete.php', ['id' => $hubcourse->id]),
    html_writer::tag('i', '', ['class' => 'fa fa-trash']) . ' ' . get_string('deletehubcourse', 'block_hubcourseinfo'),
    ['class' => 'btn btn-danger']);

echo $OUTPUT->footer();