<?php
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
            'value' => $stableversion ? $stableversion->versionnumber : false
        ),
        'demourl' => array(
            'title' => get_string('demourl', 'block_hubcourseinfo'),
            'value' => $hubcourse->demourl ? html_writer::link($hubcourse->demourl, $hubcourse->demourl) : false
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

    $html = html_writer::div(get_string('likes', 'block_hubcourseinfo'), 'bold');

    if ($likecount > 0) {
        $html .= get_string('likeamount', 'block_hubcourseinfo', $likecount);
    } else {
        $html .= get_string('nolike', 'block_hubcourseinfo');
    }

    $html .= html_writer::start_div('', ['style' => 'margin-bottom: 10px;']);
    $html .= html_writer::link(new moodle_url('/'),
        html_writer::tag('i', '', ['class' => 'fa fa-thumbs-up']) .
        get_string($alreadyliked ? 'unlike' : 'like', 'block_hubcourseinfo')
    );
    $html .= html_writer::end_div();

    return $html;
}

function block_hubcourseinfo_renderreviews($hubcourse, $context)
{
    global $DB;

    if (!has_capability('block/hubcourseinfo:submitreview', $context)) {
        return '';
    }

    $reviews = $DB->get_records('block_hubcourse_reviews', ['hubcourseid' => $hubcourse->id], 'timecreated DESC');

    $html = html_writer::start_div();
    $html .= html_writer::div(get_string('averagerating', 'block_hubcourseinfo'), 'bold');
    $html .= html_writer::div('0.0 <i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>', '', ['style' => 'margin-left: 1em;']);
    $html .= html_writer::end_div();

    $html .= html_writer::div(get_string('reviews', 'block_hubcourseinfo'), 'bold');

    if (count($reviews) == 0) {
        $html .= html_writer::div(get_string('noreview', 'block_hubcourseinfo'), '', ['style' => 'text-align: center;']);
    }

    $html .= html_writer::start_div('');
    $html .= html_writer::link(new moodle_url('/'),
        html_writer::tag('i', '', ['class' => 'fa fa-pencil']) .
        get_string('writereview', 'block_hubcourseinfo'));
    $html .= html_writer::end_div();

    return $html;
}