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

    $hubcourse = $DB->get_record('block_hubcourses', ['courseid' => $courseid]);
    if (!$hubcourse) {
        return false;
    }

    return context_block::instance($hubcourse->instanceid);
}

function block_hubcourseinfo_getcontextfromhubcourse($hubcourse)
{
    return block_hubcourseinfo_getcontextfrominstanceid($hubcourse->instanceid);
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

function block_hubcourseinfo_getcontextfromversion($version)
{
    return block_hubcourseinfo_getcontextfromhubcourseid($version->hubcourseid);
}

function block_hubcourseinfo_uploadblockenabled() {
    return in_array('hubcourseupload', core_plugin_manager::instance()->get_enabled_plugins('block'));
}

function block_hubcourseinfo_getmaxfilesize() {
    if (block_hubcourseinfo_uploadblockenabled()) {
        require_once(__DIR__ . '/../hubcourseupload/lib.php');
        return block_hubcourseupload_getmaxfilesize();
    }

    return get_max_upload_file_size();
}

function block_hubcourseinfo_getbackuppath($filename) {
    global $CFG;
    return $CFG->tempdir . '/backup/' . $filename;
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
            'value' => $stableversion ? $stableversion->description : false
        ),
        'demourl' => array(
            'title' => get_string('demourl', 'block_hubcourseinfo'),
            'value' => $hubcourse->demourl ? html_writer::link($hubcourse->demourl, mb_substr($hubcourse->demourl, 0, 20) . '…', ['target' => '_blank']) : false
        ),
        'description' => array(
            'title' => get_string('description'),
            'value' => nl2br(htmlspecialchars($hubcourse->description)),
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

    $cap_viewlike = has_capability('block/hubcourseinfo:viewlikes', $context);
    $cap_submitlike = has_capability('block/hubcourseinfo:submitlike', $context);

    if (!$cap_viewlike && !$cap_submitlike) {
        return '';
    }

    $likecount = $DB->count_records('block_hubcourse_likes', ['hubcourseid' => $hubcourse->id]);

    $alreadyliked = false;
    if ($cap_submitlike) {
        $alreadyliked = $DB->count_records('block_hubcourse_likes', ['hubcourseid' => $hubcourse->id, 'userid' => $USER->id]) ? true : false;
    }

    $html = '';

    if ($cap_viewlike) {
        if ($likecount == 1) {
            $html .= get_string('likeamount_singular', 'block_hubcourseinfo', $likecount);
        } else if ($likecount > 1) {
            $html .= get_string('likeamount_plural', 'block_hubcourseinfo', $likecount);
        } else {
            $html .= get_string(($cap_submitlike) ? 'nolike' : 'nolike_guest', 'block_hubcourseinfo');
        }
    }

    if ($cap_submitlike) {
        $html .= html_writer::start_div('', ['style' => 'margin-bottom: 10px;']);
        $html .= html_writer::link('javascript:void(0);',
            html_writer::tag('i', '', ['class' => $alreadyliked ? 'fa fa-undo' : 'fa fa-thumbs-up']) .
            ' ' . get_string($alreadyliked ? 'unlike' : 'like', 'block_hubcourseinfo'),
            ['id' => 'block-hubcourseinfo-like-a']
        );
        $html .= html_writer::end_div();
    }

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

    $cap_viewreviews = has_capability('block/hubcourseinfo:viewreviews', $context);
    $cap_submitreview = has_capability('block/hubcourseinfo:submitreview', $context);

    if (!$cap_viewreviews && !$cap_submitreview) {
        return '';
    }

    $myid = isset($USER) && $USER->id ? $USER->id : 0;
    $myreview = $DB->get_record('block_hubcourse_reviews', ['hubcourseid' => $hubcourse->id, 'userid' => $myid]);
    $edit = $myreview ? true : false;

    $reviews = $DB->get_records('block_hubcourse_reviews', ['hubcourseid' => $hubcourse->id], 'timecreated DESC', '*', 0, 5);

    $averagedata = $DB->get_record_sql('SELECT AVG(rate) AS averagerating FROM {block_hubcourse_reviews} WHERE hubcourseid = ?', [$hubcourse->id]);
    $averagerating = round($averagedata->averagerating, 2);
    $staramount = round($averagerating);

    if ($cap_viewreviews) {
        $html = html_writer::start_div();
        $html .= html_writer::div(get_string('averagerating', 'block_hubcourseinfo'), 'bold');
        $html .= html_writer::div(block_hubcourseinfo_renderstars($staramount) . ' (' . number_format($averagerating, 2) . ')'
            , '', ['style' => 'margin-left: 1em;']);
        $html .= html_writer::end_div();

        $html .= html_writer::div(get_string('reviews', 'block_hubcourseinfo'), 'bold');
    }

    if (count($reviews) == 0) {
        $html .= get_string($cap_submitreview ? 'noreview' : 'noreview_guest', 'block_hubcourseinfo');
    } else if ($cap_viewreviews) {
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
    if ($cap_viewreviews && count($reviews) > 0) {
        $html .= html_writer::link(new moodle_url('/blocks/hubcourseinfo/review/view.php', ['id' => $hubcourse->id]),
            html_writer::tag('i', '', ['class' => 'fa fa-caret-down']) .
            ' ' . get_string('readmorereview', 'block_hubcourseinfo'));
        $html .= html_writer::start_tag('br');
    }
    if ($cap_submitreview) {
        $html .= html_writer::link(new moodle_url('/blocks/hubcourseinfo/review/write.php', ['id' => $hubcourse->id]),
            html_writer::tag('i', '', ['class' => 'fa fa-pencil']) .
            ' ' . get_string($edit ? 'editmyreview' : 'writereview', 'block_hubcourseinfo'),
            ['class' => 'btn btn-default btn-block']);
    }
    $html .= html_writer::end_div();

    return $html;
}

function block_hubcourseinfo_renderdependencies($dependencies) {
    $html = '';
    if (count($dependencies) > 0) {
        $html .= html_writer::start_tag('ul');
        foreach ($dependencies as $dependency) {
            $html .= html_writer::tag('li', $dependency->requiredpluginname, ['title' => $dependency->requiredpluginname . ' - ' . $dependency->requiredpluginversion]);
        }
        $html .= html_writer::end_tag('ul');
    } else {
        $html .= html_writer::div(get_string('notknow', 'block_hubcourseinfo'), '', ['style' => 'margin-left: 1em;']);
    }

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

function block_hubcourseinfo_deleteversion($versionorid, $contextid) {
    global $DB;

    $version = null;
    if (!is_object($versionorid)) {
        if (!is_numeric($versionorid)) {
            return false;
        }

        $version = $DB->get_record('block_hubcourse_versions', ['id' => $versionorid]);
        if (!$version) {
            return false;
        }
    } else {
        $version = $versionorid;
    }

    $fs = get_file_storage();
    $files = $fs->get_area_files($contextid, 'block_hubcourse', 'course', $version->id);
    foreach ($files as $file) {
        $file->delete();
    }

    $reviews = $DB->get_records('block_hubcourse_reviews', ['versionid' => $version->id]);
    foreach ($reviews as $review) {
        $review->versionid = 0;
        $DB->update_record('block_hubcourse_reviews', $review);
    }

    $DB->delete_records('block_hubcourse_dependencies', ['versionid' => $version->id]);
    $DB->delete_records('block_hubcourse_downloads', ['versionid' => $version->id]);
    $DB->delete_records('block_hubcourse_versions', ['id' => $version->id]);

    return true;
}

function block_hubcourseinfo_fulldelete($hubcourseorid) {
    global $DB;

    $hubcourse = null;
    if (!is_object($hubcourseorid)) {
        if (!is_numeric($hubcourseorid)) {
            return false;
        }

        $hubcourse = $DB->get_record('block_hubcourses', ['id' => $hubcourseorid]);
        if (!$hubcourse) {
            return false;
        }
    } else {
        $hubcourse = $hubcourseorid;
    }

    $fs = get_file_storage();
    $files = $fs->get_area_files($hubcourse->contextid, 'block_hubcourse', 'course');
    foreach ($files as $file) {
        $file->delete();
    }

    $versions = $DB->get_records('block_hubcourse_versions', ['hubcourseid' => $hubcourse->id]);
    foreach ($versions as $version) {
        block_hubcourseinfo_deleteversion($version, $hubcourse->contextid);
    }

    $DB->delete_records('block_hubcourse_likes', ['hubcourseid' => $hubcourse->id]);
    $DB->delete_records('block_hubcourse_reviews', ['hubcourseid' => $hubcourse->id]);
    $DB->delete_records('block_hubcourses', ['id' => $hubcourse->id]);

    return true;
}

function block_hubcourseinfo_pluginstodependency($plugins, $versionid) {

    global $DB;

    $standardmods = core_plugin_manager::standard_plugins_list('mod');
    $standardblocks = core_plugin_manager::standard_plugins_list('block');

    foreach ($plugins['mod'] as $modname => $version) {
        if (in_array($modname, $standardmods)) {
            continue;
        }

        $dependency = new stdClass();
        $dependency->id = 0;
        $dependency->versionid = $versionid;
        $dependency->requiredpluginname = 'mod_' . $modname;
        $dependency->requiredpluginversion = $version;

        $DB->insert_record('block_hubcourse_dependencies', $dependency);
    }

    foreach ($plugins['blocks'] as $blockname => $version) {
        if (in_array($blockname, $standardblocks)) {
            continue;
        }

        $dependency = new stdClass();
        $dependency->id = 0;
        $dependency->versionid = $versionid;
        $dependency->requiredpluginname = 'block_' . $blockname;
        $dependency->requiredpluginversion = $version;

        $DB->insert_record('block_hubcourse_dependencies', $dependency);
    }
}

function block_hubcourseinfo_enableguestenrol($courseid) {
    global $DB;

    if (!get_config('block_hubcourseupload', 'autoenableguestenrol')) {
        return true;
    }

    $guestenrol = $DB->get_record('enrol', ['courseid' => $courseid, 'enrol' => 'guest']);
    if ($guestenrol) {
        $guestenrol->status = 0;
        return $DB->update_record('enrol', $guestenrol);
    } else {
        $maxenrolsort = $DB->get_record_sql('SELECT MAX(sortorder) AS sortorder FROM {enrol} WHERE courseid = ?', [$courseid]);
        $sortorder = $maxenrolsort && $maxenrolsort->sortorder ? $maxenrolsort->sortorder : 0;

        $guestenrol = new stdClass();
        $guestenrol->id = 0;
        $guestenrol->enrol = 'guest';
        $guestenrol->status = 0;
        $guestenrol->courseid = $courseid;
        $guestenrol->sortorder = $sortorder;
        $guestenrol->password = '';

        return $DB->insert_record('enrol', $guestenrol);
    }

    return false;
}

function block_hubcourseinfo_afterrestore($courseid, $info, $mbzfilename, $archivepath, $plugins) {
    global $DB, $USER;

    block_hubcourseinfo_enableguestenrol($courseid);

    $hubcourse = block_hubcourseinfo_gethubcoursefromcourseid($courseid);

    if ($hubcourse) {

        $hubcourse->demourl = $info->original_wwwroot . '/course/view.php?id=' . $info->original_course_id;

        $version = new stdClass();
        $version->id = 0;
        $version->hubcourseid = $hubcourse->id;
        $version->moodleversion = $info->moodle_version;
        $version->description = get_string('initialversion', 'block_hubcourseupload');
        $version->userid = $USER->id;
        $version->timeuploaded = time();
        $version->fileid = 0;
        $versionid = $DB->insert_record('block_hubcourse_versions', $version);

        $hubcoursecontext = block_hubcourseinfo_getcontextfromhubcourse($hubcourse);

        $fs = get_file_storage();
        $fs->create_file_from_pathname([
            'contextid' => $hubcoursecontext->id,
            'component' => 'block_hubcourse',
            'filearea' => 'course',
            'itemid' => $versionid,
            'filepath' => '/',
            'filename' => $mbzfilename
        ], $archivepath);

        $hubcourse->stableversion = $versionid;
        $DB->update_record('block_hubcourses', $hubcourse);

        block_hubcourseinfo_pluginstodependency($plugins, $versionid);
    }

    return $hubcourse->id;
}

function block_hubcourseinfo_cancreateversion($hubcourse) {
    global $DB;
    $currentversionamount = $DB->count_records('block_hubcourse_versions', ['hubcourseid' => $hubcourse->id]);

    return $currentversionamount < get_config('block_hubcourseinfo', 'maxversionamount');
}

function block_hubcourseinfo_clearcontents($courseorid) {
    global $DB;

    require_once(__DIR__ . '/../../lib/moodlelib.php');
    require_once(__DIR__ . '/../../notes/lib.php');

    $course = null;
    if (is_object($courseorid)) {
        $course = $courseorid;
    } else {
        if (!is_numeric($courseorid)) {
            return false;
        }

        $course = $DB->get_record('course', ['id' => $courseorid]);
        if (!$course) {
            return false;
        }
    }

    $coursecontext = context_course::instance($course->id);

    $modules = $DB->get_records('course_modules', ['course' => $course->id]);
    foreach ($modules as $module) {
        course_delete_module($module->id);
    }

    $blocks = $DB->get_records('block_instances', ['parentcontextid' => $coursecontext->id]);
    foreach ($blocks as $block) {
        if ($block->blockname != 'hubcourseinfo') {
            blocks_delete_instance($block, true);
        }
    }

    $DB->delete_records('course_sections', array('course' => $course->id));

    return true;
}