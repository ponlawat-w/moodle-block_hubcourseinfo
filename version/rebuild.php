<?php

require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/../lib.php');
require_once(__DIR__ . '/../classes/rebuildconfirm_form.php');

$versionid = optional_param('vid', 0, PARAM_INT);
$hubcourseid = optional_param('hid', 0, PARAM_INT);

$version = $versionid ? $DB->get_record('block_hubcourse_versions', ['id' => $versionid]) : null;
$hubcourse = $hubcourseid ? $DB->get_record('block_hubcourses', ['id' => $hubcourseid]) :
  ($version ? $DB->get_record('block_hubcourses', ['id' => $version->hubcourseid]) : null);

if (!$hubcourse) {
  throw new moodle_exception(get_string('hubcoursenotfound', 'block_hubcourseinfo'));
}

$hubcoursecontext = block_hubcourseinfo_getcontextfromhubcourse($hubcourse);
$coursecontext = $hubcoursecontext->get_course_context();
$course = $DB->get_record('course', ['id' => $coursecontext->instanceid]);
if (!$course) {
  throw new moodle_exception(get_string('hubcoursenotfound', 'block_hubcourseinfo'));
}

require_login($course);
require_capability('block/hubcourseinfo:managecourse', $hubcoursecontext);

if (!$versionid && !block_hubcourseinfo_cancreateversion($hubcourse)) {
  throw new moodle_exception(get_string('error_maxversionamountexceed', 'block_hubcourseinfo'));
}

$PAGE->set_context($hubcoursecontext);
$PAGE->set_pagelayout('standard');
$PAGE->set_url('/blocks/hubcourseinfo/version/rebuild', ['vid' => $versionid, 'hid' => $hubcourseid]);
$PAGE->set_title($course->fullname . ' - ' . get_string('rebuild', 'block_hubcourseinfo'));
$PAGE->set_heading($PAGE->title);
$PAGE->navbar->add(get_string('managehubcourse', 'block_hubcourseinfo'), new moodle_url('/blocks/hubcourseinfo/manage.php', ['id' => $hubcourse->id]))
  ->add(get_string($versionid ? 'rebuild' : 'rebuildasnewversion', 'block_hubcourseinfo'));


$form = new rebuildconfirm_form($versionid, $hubcourseid);
if ($form->is_submitted()) {
  if ($form->is_cancelled()) {
    redirect(new moodle_url('/blocks/hubcourseinfo/manage.php', ['id' => $hubcourse->id]));
    exit;
  } else if ($form->is_validated()) {
    require_once(__DIR__ . '/../classes/coursebackup.php');
    $data = $form->get_data();
    $description = isset($data->description) ? $data->description : '';
    $coursebackup = new block_hubcourseinfo_coursebackup($hubcourse, $versionid, $description);
    $coursebackup->execute();
    redirect(new moodle_url('/blocks/hubcourseinfo/manage.php', ['id' => $hubcourse->id]));
    exit;
  }
}

echo $OUTPUT->header();
$form->display();
echo $OUTPUT->footer();
