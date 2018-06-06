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
require_capability('block/hubcourseinfo:downloadcourse', $hubcoursecontext);

$versiontable = new html_table();
$versiontable->head = [
    get_string('timeuploaded', 'block_hubcourseinfo'),
    get_string('description'),
    get_string('downloads', 'block_hubcourseinfo'),
    get_string('moodleversion', 'block_hubcourseinfo'),
    get_string('dependencies', 'block_hubcourseinfo'),
    get_string('download')
];
$versiontable->data = [];

$versions = $DB->get_records('block_hubcourse_versions', ['hubcourseid' => $hubcourse->id], 'timeuploaded ASC');
foreach ($versions as $version) {
    $stable = ($hubcourse->stableversion == $version->id);
    $stabletext = '';
    if ($stable) {
        $stabletext = ' ' . html_writer::tag('span', get_string('current', 'block_hubcourseinfo'), ['class' => 'label label-info']);
    }

    $dependencies = $DB->get_records('block_hubcourse_dependencies', ['versionid' => $version->id], 'requiredpluginname');

    $versiontable->data[] = [
        userdate($version->timeuploaded, get_string('strftimedate', 'langconfig')) . $stabletext,
        $version->description,
        number_format($DB->count_records('block_hubcourse_downloads', ['versionid' => $version->id]), 0),
        $version->moodleversion,
        block_hubcourseinfo_renderdependencies($dependencies),
        html_writer::link(new moodle_url('/blocks/hubcourseinfo/download.php', ['version' => $version->id]),
            html_writer::tag('i', '', ['class' => 'fa fa-download']) . ' ' . get_string('download'),
            ['class' => 'btn btn-sm btn-default'])
    ];
}

$PAGE->set_context($hubcoursecontext);
$PAGE->set_pagelayout('standard');
$PAGE->set_url('/blocks/hubcourseinfo/versionlist.php', ['id' => $hubcourse->id]);
$PAGE->set_title($course->fullname . ' - ' . get_string('versions', 'block_hubcourseinfo'));
$PAGE->set_heading($PAGE->title);
$PAGE->navbar->add(get_string('versions', 'block_hubcourseinfo'));

echo $OUTPUT->header();

echo html_writer::start_tag('p');
echo html_writer::link(new moodle_url('/course/view.php', ['id' => $course->id]),
    get_string('backto', 'moodle', get_string('course')),
    ['class' => 'btn btn-default']);
echo html_writer::end_tag('p');
echo html_writer::table($versiontable);

echo $OUTPUT->footer();