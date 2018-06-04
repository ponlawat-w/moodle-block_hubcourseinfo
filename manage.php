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
    [get_string('description'), $hubcourse->description ? $hubcourse->description : get_string('notknow', 'block_hubcourseinfo')],
];

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
echo html_writer::link(new moodle_url('/course/view.php', ['id' => $course->id]),
    get_string('backto', 'moodle', get_string('course')),
    ['class' => 'btn btn-default']);
echo html_writer::tag('hr');
echo html_writer::tag('h3', get_string('manageversion', 'block_hubcourseinfo'));
echo html_writer::tag('hr');
echo html_writer::link(new moodle_url('/blocks/hubcourseinfo/delete.php', ['id' => $hubcourse->id]),
    html_writer::tag('i', '', ['class' => 'fa fa-trash']) . ' ' . get_string('deletehubcourse', 'block_hubcourseinfo'),
    ['class' => 'btn btn-danger']);

echo $OUTPUT->footer();