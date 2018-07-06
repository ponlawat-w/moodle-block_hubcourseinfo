<?php
require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/../lib.php');
require_once(__DIR__ . '/../classes/editmetadata_form.php');

$hubcourseid = required_param('id', PARAM_INT);
$new = optional_param('new', 0, PARAM_INT);

$hubcourse = $DB->get_record('block_hubcourses', ['id' => $hubcourseid]);
if (!$hubcourse) {
    throw new Exception(get_string('hubcoursenotfound', 'block_hubocurseinfo'));
}

$hubcoursecontext = block_hubcourseinfo_getcontextfromhubcourse($hubcourse, $new);

$coursecontext = $hubcoursecontext->get_course_context();
$course = $DB->get_record('course', ['id' => $coursecontext->instanceid]);
if (!$course) {
    throw new Exception(get_string('hubcoursenotfound', 'block_hubcourseinfo'));
}

require_login($course);
require_capability('block/hubcourseinfo:managecourse', $hubcoursecontext);

$form = new editmetadata_form($hubcourse, $new);

if ($form->is_cancelled()) {
    redirect(new moodle_url('/blocks/hubcourseinfo/manage.php', ['id' => $hubcourse->id]));
    exit;
}

if ($form->is_submitted()) {
    if ($form->is_validated()) {
        $data = $form->get_data();

        $course->fullname = $data->fullname;
        $course->shortname = $data->shortname;
        $course->category = $data->category;
        $DB->update_record('course', $course);

        $hubcourse->subject = $data->subject;
        $hubcourse->tags = $data->tags;
        $hubcourse->demourl = $data->demourl;
        $hubcourse->description = $data->description;
        $hubcourse->timemodified = time();
        $DB->update_record('block_hubcourses', $hubcourse);

        if ($new) {
            redirect(new moodle_url('/course/view.php', ['id' => $course->id]));
        } else {
            redirect(new moodle_url('/blocks/hubcourseinfo/manage.php', ['id' => $hubcourse->id]));
        }
    }
}

$PAGE->set_context($hubcoursecontext);
$PAGE->set_pagelayout('standard');
$PAGE->set_url('/blocks/hubcourseinfo/metadata/edit.php', ['id' => $hubcourse->id]);
$PAGE->set_title($course->fullname . ' - ' . get_string('editmetadata', 'block_hubcourseinfo'));
$PAGE->set_heading($PAGE->title);
$PAGE->navbar
    ->add(get_string('managehubcourse', 'block_hubcourseinfo'), new moodle_url('/blocks/hubcourseinfo/manage.php', ['id' => $hubcourse->id]))
    ->add(get_string('editmetadata', 'block_hubcourseinfo'));

echo $OUTPUT->header();

$form->display();

echo $OUTPUT->footer();