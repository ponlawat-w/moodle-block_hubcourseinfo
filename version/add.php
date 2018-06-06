<?php
require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/../lib.php');
require_once(__DIR__ . '/../classes/version_form.php');
require_once(__DIR__ . '/../../../backup/util/includes/restore_includes.php');

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

if (!block_hubcourseinfo_cancreateversion($hubcourse)) {
    throw new Exception(get_string('error_maxversionamountexceed', 'block_hubcourseinfo'));
}

$form = new version_form($hubcourse->id, 0, false);

if ($form->is_cancelled()) {
    redirect(new moodle_url('/blocks/hubcourseinfo/manage.php', ['id' => $hubcourse->id]));
    exit;
} else if ($form->is_submitted() && $form->is_validated()) {

    $tempname = restore_controller::get_tempdir_name($hubcourse->courseid, $USER->id);
    $path = block_hubcourseinfo_getbackuppath($tempname);

    if (!$form->save_file('coursefile', $path)) {
        fulldelete($path);
        throw new Exception(get_string('error_cannotreadfile', 'block_hubcourseinfo'));
        return $errors;
    }

    $info = backup_general_helper::get_backup_information_from_mbz($path);
    if (!$info) {
        fulldelete($path);
        throw new Exception(get_string('error_cannotreadfile', 'block_hubcourseinfo'));
    }

    if ($info->type != 'course') {
        fulldelete($path);
        throw new Exception(get_string('error_notcoursebackupfile', 'block_hubcourseinfo'));
    }

    $data = $form->get_data();

    $version = new stdClass();
    $version->id = 0;
    $version->hubcourseid = $hubcourse->id;
    $version->moodleversion = $info->moodle_version;
    $version->description = $data->description;
    $version->userid = $USER->id;
    $version->timeuploaded = time();
    $version->fileid = 0;

    $versionid = $DB->insert_record('block_hubcourse_versions', $version);
    if (!$versionid) {
        throw new Exception(get_string('error'));
    }

    if (block_hubcourseinfo_uploadblockenabled()) {
        $extractedname = restore_controller::get_tempdir_name($hubcourse->courseid, $USER->id);
        $extractedpath = block_hubcourseinfo_getbackuppath($extractedname);
        $fb = get_file_packer('application/vnd.moodle.backup');
        if ($fb->extract_to_pathname($path, $extractedpath)) {
            $plugins = block_hubcourseupload_getplugins($extractedpath);
            block_hubcourseinfo_pluginstodependency($plugins, $versionid);
        }
    }

    if (!$form->save_stored_file('coursefile', $hubcoursecontext->id, 'block_hubcourse', 'course', $versionid, '/')) {
        throw new Exception(get_string('error'));
    }

    redirect(new moodle_url('/blocks/hubcourseinfo/manage.php', ['id' => $hubcourse->id]));
    exit;
}

$PAGE->set_context($hubcoursecontext);
$PAGE->set_pagelayout('standard');
$PAGE->set_url('/blocks/hubcourseinfo/version/add.php', ['id' => $hubcourse->id]);
$PAGE->set_title($course->fullname . ' - ' . get_string('addversion', 'block_hubcourseinfo'));
$PAGE->set_heading($PAGE->title);
$PAGE->navbar->add(get_string('managehubcourse', 'block_hubcourseinfo'), new moodle_url('/blocks/hubcourseinfo/manage.php', ['id' => $hubcourse->id]))
    ->add(get_string('addversion', 'block_hubcourseinfo'));

echo $OUTPUT->header();

$form->display();

echo $OUTPUT->footer();