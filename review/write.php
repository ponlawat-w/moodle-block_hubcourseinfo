<?php
require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/../lib.php');
require_once(__DIR__ . '/../classes/review_form.php');

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

require_login();
require_capability('block/hubcourseinfo:submitreview', $blockcontext);

$review = $DB->get_record('block_hubcourse_reviews', ['hubcourseid' => $hubcourse->id, 'userid' => $USER->id]);
$editing = $review ? true : false;

$form = new review_form($hubcourse, $editing);

if ($form->is_submitted()) {
    if ($form->is_cancelled()) {
        redirect(new moodle_url('/blocks/hubcourseinfo/review/view.php', ['id' => $hubcourseid]));
        exit;
    } else if ($form->is_validated()) {
        $data = $form->get_data();
        $rate = $data->rate;
        $comment = $data->comment['text'];
        $commentformat = $data->comment['format'];

        if (block_hubcourseinfo_updatereview($hubcourse->id, $rate, $comment, $commentformat)) {
            redirect(new moodle_url('/blocks/hubcourseinfo/review/view.php', ['id' => $hubcourseid]));
        } else {
            throw new Exception(get_string('err_cannotsubmit', 'block_hubcourseinfo'));
        }
    }
} else if ($review) {
    $form->set_data([
        'rate' => $review->rate,
        'comment' => [
            'text' => $review->comment,
            'format' => $review->commentformat
        ]
    ]);
}

$PAGE->set_context($blockcontext);
$PAGE->set_pagelayout('standard');
$PAGE->set_url('/blocks/hubcourseinfo/review/write.php');
$PAGE->set_title($course->fullname . ' - ' . get_string($editing ? 'editreview' : 'writereview', 'block_hubcourseinfo'));
$PAGE->set_heading($PAGE->title);
$PAGE->navbar->add($course->fullname, new moodle_url('/course/view.php', ['id' => $course->id]))
    ->add(get_string('reviews', 'block_hubcourseinfo'), new moodle_url('/blocks/hubcourseinfo/review/view.php', ['id' => $hubcourseid]))
    ->add(get_string($editing ? 'editreview' : 'writereview', 'block_hubcourseinfo'));

echo $OUTPUT->header();

$form->display();

echo $OUTPUT->footer();