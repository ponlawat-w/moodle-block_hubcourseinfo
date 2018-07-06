<?php
require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/classes/subject_form.php');

require_login();
require_capability('block/hubcourseinfo:managesubjects', context_system::instance());

$editsubjectform = new subject_form(-1);
if ($editsubjectform->is_submitted()) {
    if ($editsubjectform->is_cancelled()) {
        redirect(new moodle_url('/blocks/hubcourseinfo/admin/subjects.php'));
    } else if ($editsubjectform->is_validated()) {
        $data = $editsubjectform->get_data();
        $subject = new stdClass();
        $subject->id = $data->id;
        $subject->name = $data->name;

        if ($DB->update_record('block_hubcourse_subjects', $subject)) {
            redirect(new moodle_url('/blocks/hubcourseinfo/admin/subjects.php'));
        } else {
            throw new moodle_exception('Cannot update subject data', 'block_hubcourseinfo');
        }
    }
} else {
    $subjectid = required_param('id', PARAM_INT);
    $subject = $DB->get_record('block_hubcourse_subjects', ['id' => $subjectid]);
    if (!$subject) {
        throw new moodle_exception('Subject not found', 'block_hubcourseinfo');
    }

    $editsubjectform = new subject_form($subject->id, $subject->name);
}

$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_url('/blocks/hubcourseinfo/admin/subjects.php');
$PAGE->set_title(get_string('managesubjects', 'block_hubcourseinfo'));
$PAGE->set_heading($PAGE->title);

$PAGE->navbar
    ->add(get_string('administrationsite'), new moodle_url('/admin/search.php'))
    ->add(get_string('plugins', 'admin'), new moodle_url('/admin/category.php', ['category' => 'modules']))
    ->add(get_string('blocks'), new moodle_url('/admin/category.php', ['category' => 'blocksettings']))
    ->add(get_string('pluginname', 'block_hubcourseinfo'), new moodle_url('/admin/settings.php', ['section' => 'blocksettinghubcourseinfo']))
    ->add(get_string('managesubjects', 'block_hubcourseinfo'), new moodle_url('/blocks/hubcourseinfo/admin/subjects.php'))
    ->add(get_string('editsubject', 'block_hubcourseinfo', $subject->name));

echo $OUTPUT->header();

echo html_writer::tag('h4', get_string('editsubject', 'block_hubcourseinfo', $subject->name));
$editsubjectform->display();

echo $OUTPUT->footer();