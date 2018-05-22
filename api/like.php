<?php
define('AJAX_SCRIPT', true);
require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/../lib.php');

$hubcourseid = required_param('hubcourseid', PARAM_INT);
$result = ['success' => false, 'html' => ''];

$hubcourse = $DB->get_record('block_hubcourses', ['id' => $hubcourseid]);

if ($hubcourse) {
    $blockcontext = block_hubcourseinfo_getcontextfromhubcourse($hubcourse);

    if ($blockcontext && has_capability('block/hubcourseinfo:submitlike', $blockcontext)) {
        $like = $DB->get_record('block_hubcourse_likes', ['hubcourseid' => $hubcourseid, 'userid' => $USER->id]);

        $queryresult = false;
        if ($like) {
            $queryresult = $DB->delete_records('block_hubcourse_likes', ['id' => $like->id]);
        } else {
            $newlike = new stdClass();
            $newlike->id = 0;
            $newlike->hubcourseid = $hubcourseid;
            $newlike->userid = $USER->id;
            $newlike->timecreated = time();
            $queryresult = $DB->insert_record('block_hubcourse_likes', $newlike);
        }

        if ($queryresult) {
            $result['success'] = true;
            $result['html'] = block_hubcourseinfo_renderlike($hubcourse, $blockcontext);
        }
    }
}

echo json_encode($result);