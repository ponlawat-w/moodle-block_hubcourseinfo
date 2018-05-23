<?php
require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/../lib.php');

$hubcourseid = required_param('id', PARAM_INT);
$hubcourse = $DB->get_record('block_hubcourses', ['id' => $hubcourseid]);
if (!$hubcourse) {
    throw new Exception(get_string('hubcoursenotfound', 'block_hubcourseinfo'));
}

$blockcontext = block_hubcourseinfo_getcontextfromhubcourse($hubcourse);
$coursecontext = $blockcontext->get_course_context();
$course = $DB->get_record('course', ['id' => $coursecontext->instanceid]);
if (!$course) {
    throw new Exception(get_string('hubcoursenotfound', 'block_hubcourseinfo'));
}

require_capability('block/hubcourseinfo:submitreview', $blockcontext);

$reviews = $DB->get_records('block_hubcourse_reviews', ['hubcourseid' => $hubcourseid], 'timecreated ASC');
$edit = false;
foreach ($reviews as $review) {
    if ($review->userid == $USER->id) {
        $edit = true;
        break;
    }
}

$PAGE->set_context($blockcontext);
$PAGE->set_pagelayout('standard');
$PAGE->set_url('/blocks/hubcourseinfo/review/view.php', ['id' => $hubcourseid]);
$PAGE->set_title($course->fullname . ' - ' . get_string('reviews', 'block_hubcourseinfo'));
$PAGE->set_heading($PAGE->title);
$PAGE->navbar->add($course->fullname, new moodle_url('/course/view.php', ['id' => $course->id]))
    ->add(get_string('reviews', 'block_hubcourseinfo'));

echo $OUTPUT->header();

if (!$edit) {
    echo html_writer::tag('p', html_writer::link(new moodle_url('/blocks/hubcourseinfo/review/write.php', ['id' => $hubcourse->id]), get_string($edit ? 'editreview' : 'writereview', 'block_hubcourseinfo')));
}

if (count($reviews) == 0) {
    echo html_writer::div(get_string('noreview', 'block_hubcourseinfo'), 'alert alert-warning');
} else {
    foreach ($reviews as $review) {
        $user = $DB->get_record('user', ['id' => $review->userid]);

        echo html_writer::start_div('', ['style' => 'margin: 20px 0;']);
        echo html_writer::div(block_hubcourseinfo_renderstars($review->rate));
        echo html_writer::div($review->comment);
        if ($review->userid == $USER->id) {
            echo html_writer::link(new moodle_url('/blocks/hubcourseinfo/review/write.php', ['id' => $hubcourse->id]),
                html_writer::tag('i', '', ['class' => 'fa fa-pencil']) . ' ' . get_string('editmyreview', 'block_hubcourseinfo'),
                ['class' => 'small']);
        }
        echo html_writer::end_div();

        echo html_writer::start_div('small', ['style' => 'text-align: right;']);
        echo html_writer::link(new moodle_url('/user/profile.php', ['id' => $user->id]), fullname($user));
        echo ' - ';
        echo userdate($review->timecreated);
        echo html_writer::end_div();
        echo html_writer::start_tag('hr');
    }
}

if (!$edit && count($reviews) > 0) {
    echo html_writer::tag('p', html_writer::link(new moodle_url('/blocks/hubcourseinfo/review/write.php', ['id' => $hubcourse->id]), get_string($edit ? 'editreview' : 'writereview', 'block_hubcourseinfo')));
}

echo $OUTPUT->footer();