<?php
function block_hubcourseinfo_gethubcoursefromcourseid($courseid)
{
    global $DB;
    $hubcourse = $DB->get_record('block_hubcourses', ['courseid' => $courseid]);

    return $hubcourse;
}

function block_hubcourseinfo_getcontextfrominstanceid($instanceid)
{
    return context_block::instance($instanceid);
}

function block_hubcourseinfo_getcontextfromcourseid($courseid)
{
    global $DB;

    $coursecontext = context_course::instance($courseid);
    $instance = $DB->get_record('block_instances', ['blockname' => 'hubcourseinfo', 'parentcontextid' => $coursecontext->id]);
    if (!$instance) {
        return false;
    }

    return context_block::instance($instance->id);
}

function block_hubcourseinfo_getcontextfromhubcourse($hubcourse)
{
    return block_hubcourseinfo_getcontextfromcourseid($hubcourse->courseid);
}

function block_hubcourseinfo_getcontextfromhubcourseid($hubcourseid)
{
    global $DB;

    $hubcourse = $DB->get_record('block_hubcourses', ['id' => $hubcourseid]);
    if (!$hubcourse) {
        return false;
    }

    return block_hubcourseinfo_getcontextfromhubcourse($hubcourse);
}

function block_hubcourseinfo_renderinfo($hubcourse)
{
    global $DB;

    $course = get_course($hubcourse->courseid);
    $stableversion = $DB->get_record('block_hubcourse_versions', ['id' => $hubcourse->stableversion]);

    $data = array(
        'fullnamecourse' => array(
            'title' => get_string('fullnamecourse'),
            'value' => $course->fullname
        ),
        'fullnameuser' => array(
            'title' => get_string('courseowner', 'block_hubcourseinfo'),
            'value' => fullname($DB->get_record('user', ['id' => $hubcourse->userid]))
        ),
        'stableversion' => array(
            'title' => get_string('stableversion', 'block_hubcourseinfo'),
            'value' => $stableversion ? userdate($stableversion->timeuploaded) : false
        ),
        'demourl' => array(
            'title' => get_string('demourl', 'block_hubcourseinfo'),
            'value' => $hubcourse->demourl ? html_writer::link($hubcourse->demourl, mb_substr($hubcourse->demourl, 0, 20) . '…', ['target' => '_blank']) : false
        ),
        'description' => array(
            'title' => get_string('description'),
            'value' => $hubcourse->description,
        ),
        'timecreated' => array(
            'title' => get_string('timecreated', 'block_hubcourseinfo'),
            'value' => userdate($hubcourse->timecreated)
        )
    );

    $html = '';

    foreach ($data as $item) {
        $title = $item['title'];
        $value = $item['value'] ? $item['value'] : get_string('notknow', 'block_hubcourseinfo');

        $html .= html_writer::start_div('');
        $html .= html_writer::div($title, 'bold');
        $html .= html_writer::div($value, '', ['style' => 'margin-left: 1em;']);
        $html .= html_writer::end_div();
    }

    return $html;
}

function block_hubcourseinfo_renderlike($hubcourse, $context)
{
    global $DB, $USER;

    if (!has_capability('block/hubcourseinfo:submitlike', $context)) {
        return '';
    }

    $likecount = $DB->count_records('block_hubcourse_likes', ['hubcourseid' => $hubcourse->id]);
    $alreadyliked = $DB->count_records('block_hubcourse_likes', ['hubcourseid' => $hubcourse->id, 'userid' => $USER->id]) ? true : false;

    $html = '';

    if ($likecount == 1) {
        $html .= get_string('likeamount_singular', 'block_hubcourseinfo', $likecount);
    } else if ($likecount > 1) {
        $html .= get_string('likeamount_plural', 'block_hubcourseinfo', $likecount);
    } else {
        $html .= get_string('nolike', 'block_hubcourseinfo');
    }

    $html .= html_writer::start_div('', ['style' => 'margin-bottom: 10px;']);
    $html .= html_writer::link('javascript:void(0);',
        html_writer::tag('i', '', ['class' => $alreadyliked ? 'fa fa-undo' : 'fa fa-thumbs-up']) .
        ' ' . get_string($alreadyliked ? 'unlike' : 'like', 'block_hubcourseinfo'),
        ['id' => 'block-hubcourseinfo-like-a']
    );
    $html .= html_writer::end_div();

    return $html;
}

function block_hubcourseinfo_previewcomment($comment, $length)
{
    $comment = strip_tags($comment);
    return mb_strlen($comment) > $length ? mb_substr($comment, 0, $length) . '…' : $comment;
}

function block_hubcourseinfo_renderstars($star, $max = 5)
{
    $html = '';
    for ($i = 0; $i < $star; $i++) {
        $html .= '★';
    }
    for ($i = $star; $i < $max; $i++) {
        $html .= '☆';
    }

    return $html;
}

function block_hubcourseinfo_renderreviews($hubcourse, $context)
{
    global $DB, $USER;

    if (!has_capability('block/hubcourseinfo:submitreview', $context)) {
        return '';
    }

    $myreview = $DB->get_record('block_hubcourse_reviews', ['hubcourseid' => $hubcourse->id, 'userid' => $USER->id]);
    $edit = $myreview ? true : false;

    $reviews = $DB->get_records('block_hubcourse_reviews', ['hubcourseid' => $hubcourse->id], 'timecreated DESC', '*', 0, 5);

    $averagedata = $DB->get_record_sql('SELECT AVG(rate) AS averagerating FROM {block_hubcourse_reviews} WHERE hubcourseid = ?', [$hubcourse->id]);
    $averagerating = round($averagedata->averagerating, 2);
    $staramount = round($averagerating);

    $html = html_writer::start_div();
    $html .= html_writer::div(get_string('averagerating', 'block_hubcourseinfo'), 'bold');
    $html .= html_writer::div(block_hubcourseinfo_renderstars($staramount) . ' (' . number_format($averagerating, 2) . ')'
        , '', ['style' => 'margin-left: 1em;']);
    $html .= html_writer::end_div();

    $html .= html_writer::div(get_string('reviews', 'block_hubcourseinfo'), 'bold');

    if (count($reviews) == 0) {
        $html .= html_writer::div(get_string('noreview', 'block_hubcourseinfo'), '', ['style' => 'text-align: center;']);
    } else {
        foreach ($reviews as $review) {
            $user = $DB->get_record('user', ['id' => $review->userid]);

            $html .= html_writer::start_div('', ['style' => 'margin: 0  0 10px 0.5em;']);
            $html .= html_writer::div(block_hubcourseinfo_previewcomment($review->comment, 100));
            $html .= html_writer::start_div('small', ['style' => 'margin-left: 1em;']);
            $html .= html_writer::span(fullname($user), 'bold');
            $html .= ' - ';
            $html .= html_writer::span(userdate($review->timecreated, get_string('strftimedatefullshort', 'langconfig')));
            $html .= html_writer::start_tag('br');
            $html .= html_writer::span(block_hubcourseinfo_renderstars($review->rate));
            $html .= html_writer::end_div();
            $html .= html_writer::end_div();
        }
    }

    $html .= html_writer::start_div('');
    if (count($reviews) > 0) {
        $html .= html_writer::link(new moodle_url('/blocks/hubcourseinfo/review/view.php', ['id' => $hubcourse->id]),
            html_writer::tag('i', '', ['class' => 'fa fa-caret-down']) .
            ' ' . get_string('readmorereview', 'block_hubcourseinfo'));
        $html .= html_writer::start_tag('br');
    }
    $html .= html_writer::link(new moodle_url('/blocks/hubcourseinfo/review/write.php', ['id' => $hubcourse->id]),
        html_writer::tag('i', '', ['class' => 'fa fa-pencil']) .
        ' ' . get_string($edit ? 'editmyreview' : 'writereview', 'block_hubcourseinfo'),
        ['class' => 'btn btn-default btn-block']);
    $html .= html_writer::end_div();

    return $html;
}

function block_hubcourseinfo_updatereview($hubcourseid, $rate, $comment, $commentformat, $versionid = null)
{
    global $USER, $DB;

    $userid = $USER->id;
    $hubcourse = $DB->get_record('block_hubcourses', ['id' => $hubcourseid]);
    if (!$hubcourse) {
        return false;
    }

    if (is_null($versionid)) {
        $versionid = $hubcourse->stableversion;
    }

    $review = $DB->get_record('block_hubcourse_reviews', ['hubcourseid' => $hubcourse->id, 'userid' => $USER->id]);

    if ($review) {
        $review->versionid = $versionid;
        $review->rate = $rate;
        $review->comment = $comment;
        $review->commentformat = $commentformat;
        $review->timecreated = time();
        $result = $DB->update_record('block_hubcourse_reviews', $review) ? true : false;
    } else {
        $review = new stdClass();
        $review->id = 0;
        $review->hubcourseid = $hubcourse->id;
        $review->versionid = $versionid;
        $review->userid = $userid;
        $review->rate = $rate;
        $review->comment = $comment;
        $review->commentformat = $commentformat;
        $review->timecreated = time();
        $result = $DB->insert_record('block_hubcourse_reviews', $review) ? true : false;
    }

    return $result;
}