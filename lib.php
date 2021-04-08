<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Function libraries file
 *
 * @package block_hubcourseinfo
 * @copyright 2018 Moodle Association of Japan
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (block_hubcourseinfo_uploadblockenabled()) {
  require_once(__DIR__ . '/../hubcourseupload/lib.php');
}

/**
 * Get hubcourse object from course ID
 * @param int $courseid
 * @return stdClass
 * @throws dml_exception
 */
function block_hubcourseinfo_gethubcoursefromcourseid($courseid) {
    global $DB;
    $hubcourse = $DB->get_record('block_hubcourses', ['courseid' => $courseid]);

    return $hubcourse;
}

/**
 * Get block context instance from block instance id
 * @param int $instanceid
 * @return context_block
 */
function block_hubcourseinfo_getcontextfrominstanceid($instanceid) {
    return context_block::instance($instanceid);
}

/**
 * Get block context instance from course id
 *  Return false if course is not found or course is not registered as a hubcourse
 * @param int $courseid
 * @return bool|context_block
 * @throws dml_exception
 */
function block_hubcourseinfo_getcontextfromcourseid($courseid) {
    global $DB;

    $hubcourse = $DB->get_record('block_hubcourses', ['courseid' => $courseid]);
    if (!$hubcourse) {
        return false;
    }

    return context_block::instance($hubcourse->instanceid);
}

/**
 * Get block context instance from hubcourse object
 * @param stdClass $hubcourse
 * @return context_block
 */
function block_hubcourseinfo_getcontextfromhubcourse($hubcourse) {
    return block_hubcourseinfo_getcontextfrominstanceid($hubcourse->instanceid);
}

/**
 * Get block context instance from hubcourse ID
 * @param int $hubcourseid
 * @return bool|context_block
 * @throws dml_exception
 */
function block_hubcourseinfo_getcontextfromhubcourseid($hubcourseid) {
    global $DB;

    $hubcourse = $DB->get_record('block_hubcourses', ['id' => $hubcourseid]);
    if (!$hubcourse) {
        return false;
    }

    return block_hubcourseinfo_getcontextfromhubcourse($hubcourse);
}

/**
 * Get block context instance from hubcourse version object
 * @param stdClass $version
 * @return bool|context_block
 * @throws dml_exception
 */
function block_hubcourseinfo_getcontextfromversion($version) {
    return block_hubcourseinfo_getcontextfromhubcourseid($version->hubcourseid);
}

/**
 * Check if block_hubcourseupload is enabled in this site
 * @return bool
 */
function block_hubcourseinfo_uploadblockenabled() {
    return in_array('hubcourseupload', core_plugin_manager::instance()->get_enabled_plugins('block'));
}

/**
 * Check if majhub_coursewares exists
 * @return bool
 * @throws ddl_exception
 */
function block_hubcourseinfo_existsmajhubcoursewares() {
    global $DB;
    $dbman = $DB->get_manager();
    return $dbman->table_exists('majhub_coursewares');
}

/**
 * Get maximum file size
 * @return float|int
 */
function block_hubcourseinfo_getmaxfilesize() {
    if (block_hubcourseinfo_uploadblockenabled()) {
        require_once(__DIR__ . '/../hubcourseupload/lib.php');
        return block_hubcourseupload_getmaxfilesize();
    }

    return get_max_upload_file_size();
}

/**
 * Get backup path
 * @param string $filename
 * @return string
 */
function block_hubcourseinfo_getbackuppath($filename) {
    global $CFG;
    return $CFG->tempdir . '/backup/' . $filename;
}

/**
 * Converting tags (string array) to CSS badges
 * @param string[] $tags
 * @return string
 */
function block_hubcourseinfo_tagstobadges($tags) {
    $tags = array_map(function ($tag) {
        return html_writer::span($tag, 'badge badge-default small');
    }, explode(',', $tags));

    return implode('', $tags);
}

/**
 * Fetch rendered hubcourse information for block display
 * @param stdClass $hubcourse
 * @return string
 * @throws coding_exception
 * @throws dml_exception
 */
function block_hubcourseinfo_renderinfo($hubcourse) {
    global $DB;

    $course = get_course($hubcourse->courseid);
    $subject = $DB->get_record('block_hubcourse_subjects', ['id' => $hubcourse->subject]);
    $stableversion = $DB->get_record('block_hubcourse_versions', ['id' => $hubcourse->stableversion]);

    $data = array(
        'fullnamecourse' => array(
            'title' => get_string('fullnamecourse'),
            'value' => $course->fullname
        ),
        'subject' => array(
            'title' => get_string('subject', 'block_hubcourseinfo'),
            'value' => $subject ? $subject->name : get_string('notknow', 'block_hubcourseinfo')
        ),
        'tags' => array(
            'title' => get_string('tags', 'block_hubcourseinfo'),
            'value' => trim($hubcourse->tags) ? block_hubcourseinfo_tagstobadges($hubcourse->tags) : get_string('notknow', 'block_hubcourseinfo')
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
            'value' => $hubcourse->demourl ? html_writer::link($hubcourse->demourl, mb_substr($hubcourse->demourl, 0, 30) . '…', ['target' => '_blank']) : false
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

/**
 * Fetch rendered hubcourse like data for block display
 * @param stdClass $hubcourse
 * @param context_block $context
 * @return string
 * @throws coding_exception
 * @throws dml_exception
 */
function block_hubcourseinfo_renderlike($hubcourse, $context) {
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

    $html = html_writer::start_div('block-hubcourseinfo-indent');

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

    $html .= html_writer::end_div();

    return $html;
}

/**
 * Trim comment string to defined maximum length
 *  in purpose of preview in block display
 * @param string $comment
 * @param int $length
 * @return string
 */
function block_hubcourseinfo_previewcomment($comment, $length) {
    $comment = strip_tags($comment);
    return mb_strlen($comment) > $length ? mb_substr($comment, 0, $length) . '…' : $comment;
}

/**
 * Convert rating score to star unicodes
 * @param int $star
 * @param int $max
 * @return string
 */
function block_hubcourseinfo_renderstars($star, $max = 5) {
    $html = '';
    for ($i = 0; $i < $star; $i++) {
        $html .= '★';
    }
    for ($i = $star; $i < $max; $i++) {
        $html .= '☆';
    }

    return $html;
}

/**
 * Fetch rendered hubcourse reviews data for block display
 * @param stdClass $hubcourse
 * @param context_block $context
 * @return string
 * @throws coding_exception
 * @throws dml_exception
 * @throws moodle_exception
 */
function block_hubcourseinfo_renderreviews($hubcourse, $context) {
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
    $totalreviews_count = $DB->count_records('block_hubcourse_reviews', ['hubcourseid' => $hubcourse->id]);

    $averagedata = $DB->get_record_sql('SELECT AVG(rate) AS averagerating FROM {block_hubcourse_reviews} WHERE hubcourseid = ?', [$hubcourse->id]);
    $averagerating = round($averagedata->averagerating, 2);
    $staramount = round($averagerating);

    if ($cap_viewreviews) {
        $html = html_writer::start_div();
        $html .= html_writer::div(get_string('averagerating', 'block_hubcourseinfo'), 'bold');
        $html .= html_writer::div(block_hubcourseinfo_renderstars($staramount) . ' (' . number_format($averagerating, 2) . ')'
            , 'block-hubcourseinfo-indent');
        $html .= html_writer::end_div();

        $html .= html_writer::start_div('bold');
        $html .= get_string('reviews', 'block_hubcourseinfo');
        if ($totalreviews_count) {
            $html .= ' (' . $totalreviews_count. ')';
        }
        $html .= html_writer::end_div();

        $html .= html_writer::start_div('block-hubcourseinfo-indent');
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
        $html .= html_writer::end_div();

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
    } else {
        $html = '';
    }

    return $html;
}

/**
 * Fetch rendered hubcourse dependencies data for block display
 * @param stdClass[] $dependencies
 * @return string
 * @throws coding_exception
 */
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

/**
 * Insert or update review data of user's review of a hubcourse
 * @param int $hubcourseid
 * @param int $rate
 * @param string $comment
 * @param string $commentformat
 * @param int $versionid
 * @return bool
 * @throws dml_exception
 */
function block_hubcourseinfo_updatereview($hubcourseid, $rate, $comment, $commentformat, $versionid = null) {
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

/**
 * Delete and cascade hubcourse version
 * @param int $versionorid
 * @param int $contextid
 * @return bool
 * @throws coding_exception
 * @throws dml_exception
 */
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
    $fs->delete_area_files($contextid, 'block_hubcourse', 'course', $version->id);

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

/**
 * Fully unregister hubcourse from site
 * @param int $hubcourseorid
 * @return bool
 * @throws coding_exception
 * @throws dml_exception
 */
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

/**
 * Convert plugins information from backup file to dependencies records
 * @param array $plugins
 * @param int $versionid
 * @throws dml_exception
 */
function block_hubcourseinfo_pluginstodependency($plugins, $versionid) {

    global $DB;

    $DB->delete_records('block_hubcourse_dependencies', ['versionid' => $versionid]);

    $standardmods = core_plugin_manager::standard_plugins_list('mod');
    $standardblocks = core_plugin_manager::standard_plugins_list('block');
    $standardblocks[] = 'hubcourseinfo';

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

/**
 * Save unextracted mbz to dependencies table
 * @param int $courseid
 * @param int $versionid
 * @param string $filepath
 * @throws dml_exception
 * @return bool
 */
function block_hubcourseinfo_savembzdependencies($courseid, $versionid, $filepath = null) {
    global $USER;
    if (!$filepath) {
      $fs = get_file_storage();
      $files = $fs->get_area_files(block_hubcourseinfo_getcontextfromcourseid($courseid)->id, 'block_hubcourse', 'course', $versionid);
      foreach ($files as $file) {
        $exts = explode('.', $file->get_filename());
        $ext = $exts[count($exts) - 1];
        if ($ext == 'mbz') {
          global $CFG;
          $hash = $file->get_contenthash();
          $filepath = $CFG->dataroot . '/filedir/' . substr($hash, 0, 2) . '/' . substr($hash, 2, 2) . '/' . $hash;
          break;
        }
      }
    }
    if (!$filepath || !file_exists($filepath)) {
      throw new moodle_exception('invalid file path');
    }
    $extractedname = restore_controller::get_tempdir_name($courseid, $USER->id);
    $extractedpath = block_hubcourseinfo_getbackuppath($extractedname);
    $fb = get_file_packer('application/vnd.moodle.backup');
    if ($fb->extract_to_pathname($filepath, $extractedpath)) {
        $plugins = block_hubcourseupload_getplugins($extractedpath);
        block_hubcourseinfo_pluginstodependency($plugins, $versionid);
    }

    return true;
}

/**
 * Enable guest enrollment of given course ID, if configured
 * @param int $courseid
 * @return bool|int
 * @throws dml_exception
 */
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
}

/**
 * Truncate hubcourse data
 * @param stdClass $hubcourse
 * @throws coding_exception
 * @throws dml_exception
 */
function block_hubcourseinfo_clearcontent($hubcourse) {
    global $DB;

    $versions = $DB->get_records('block_hubcourse_versions', ['hubcourseid' => $hubcourse->id]);
    foreach ($versions as $version) {
        block_hubcourseinfo_deleteversion($version->id, $hubcourse->contextid);
    }

    $DB->delete_records('block_hubcourse_likes', ['hubcourseid' => $hubcourse->id]);
    $DB->delete_records('block_hubcourse_reviews', ['hubcourseid' => $hubcourse->id]);
}

/**
 * Action after restoration from block_hubcourseupload
 * @param int $courseid
 * @param stdClass $info
 * @param string $mbzfilename
 * @param string $archivepath
 * @param array $plugins
 * @return bool
 * @throws coding_exception
 * @throws dml_exception
 * @throws file_exception
 * @throws stored_file_creation_exception
 */
function block_hubcourseinfo_afterrestore($courseid, $info, $mbzfilename, $archivepath, $plugins) {
    global $DB, $USER;

    block_hubcourseinfo_enableguestenrol($courseid);

    $hubcourse = block_hubcourseinfo_gethubcoursefromcourseid($courseid);

    if ($hubcourse) {

        block_hubcourseinfo_clearcontent($hubcourse);

        $hubcourse->demourl = $info->original_wwwroot . '/course/view.php?id=' . $info->original_course_id;

        $version = new stdClass();
        $version->id = 0;
        $version->hubcourseid = $hubcourse->id;
        $version->moodleversion = $info->moodle_version;
        $version->moodlerelease = $info->moodle_release;
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

        return $hubcourse->id;
    } else {
        return false;
    }
}

/**
 * Check if a version can be created on defined hubcourse (due to configuration in admin settings)
 * @param stdClass $hubcourse
 * @return bool
 * @throws dml_exception
 */
function block_hubcourseinfo_cancreateversion($hubcourse) {
    global $DB;
    $currentversionamount = $DB->count_records('block_hubcourse_versions', ['hubcourseid' => $hubcourse->id]);

    return $currentversionamount < get_config('block_hubcourseinfo', 'maxversionamount');
}

/**
 * Truncate course contents
 *  This function is used when applying different hubcourse version
 * @param int $courseorid
 * @return bool
 * @throws dml_exception
 * @throws moodle_exception
 */
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

function block_hubcourseinfo_deleteotherblockinstances($coursecontextid, $exceptfirst) {
  global $DB;
  $blocks = $DB->get_records('block_instances', ['parentcontextid' => $coursecontextid], 'id ASC');
  $i = 0;
  foreach ($blocks as $block) {
    if ($exceptfirst && $i++ == 0) {
      continue;
    }
    if ($block->blockname == 'hubcourseinfo') {
      blocks_delete_instance($block, true);
      $hubcourses = $DB->get_records('block_hubcourses', ['instanceid' => $block->id]);
      foreach ($hubcourses as $hubcourse) {
        block_hubcourseinfo_fulldelete($hubcourse);
      }
    }
  }
}

/**
 * @param stdClass $hubcourse
 * @return bool
 * @throws backup_helper_exception
 * @throws coding_exception
 * @throws ddl_exception
 * @throws dml_exception
 * @throws file_exception
 * @throws moodle_exception
 * @throws stored_file_creation_exception
 */
function block_hubcourseinfo_majimport($hubcourse) {
    global $DB;

    if (!block_hubcourseinfo_existsmajhubcoursewares()) {
        throw new moodle_exception('Table majhub_coursewares does not exist');
    }

    $courseware = $DB->get_record('majhub_coursewares', ['courseid' => $hubcourse->courseid]);
    if (!$courseware) {
        throw new moodle_exception(get_string('error_nomajhub', 'block_hubcourseinfo'));
    }

    $lastversionid = block_hubcourseinfo_majimport_versions($hubcourse, $courseware);
    if (!$lastversionid) {
        throw new moodle_exception('Cannot import hubcourse versions');
    }

    if (!block_hubcourseinfo_majimport_reviews($hubcourse, $courseware, $lastversionid)) {
        throw new moodle_exception('Cannot import reviews');
    }

    if (!block_hubcourseinfo_majimport_downloads($hubcourse, $courseware, $lastversionid)) {
        throw new moodle_exception('Cannot import downloads');
    }

    $hubcourse->userid = $courseware->userid;
    $hubcourse->stableversion = $lastversionid;
    $hubcourse->demourl = $courseware->demourl ? $courseware->demourl : '';
    $hubcourse->timecreated = $courseware->timecreated;
    $hubcourse->timemodified = $courseware->timemodified;
    return $DB->update_record('block_hubcourses', $hubcourse) ? true : false;
}

/**
 * Import courseware version to hubcourse version
 * @param stdClass $hubcourse
 * @param stdClass $courseware
 * @param bool $skipifexist Skip importing if hubcourse contains versions
 * @return bool|int
 * @throws backup_helper_exception
 * @throws dml_exception
 * @throws file_exception
 * @throws moodle_exception
 * @throws stored_file_creation_exception
 */
function block_hubcourseinfo_majimport_versions($hubcourse, $courseware, $skipifexist = true) {
    global $DB, $CFG;
    require_once(__DIR__ . '/../../backup/util/includes/restore_includes.php');
    require_once(__DIR__ . '/../hubcourseupload/lib.php');

    $existingversions = $DB->get_records('block_hubcourse_versions', ['hubcourseid' => $hubcourse->id]);
    if ($skipifexist && count($existingversions)) {
        if ($hubcourse->stableversion) {
            return $hubcourse->stableversion;
        }
        foreach ($existingversions as $version) {
            return $version->id;
        }
    }

    $fs = new file_storage();
    $coursewareversions = $DB->get_records('majhub_courseware_versions', ['coursewareid' => $courseware->id],
        'timecreated DESC', '*', 0,
        get_config('block_hubcourseinfo', 'maxversionamount') - count($existingversions) - 1);
    $lastid = 0;

    $stable_cversion = new stdClass();
    $stable_cversion->id = 0;
    $stable_cversion->fileid = $courseware->fileid;
    $stable_cversion->description = '-';
    $stable_cversion->timecreated = $courseware->timecreated;
    $coursewareversions[] = $stable_cversion;

    foreach ($coursewareversions as $cversion) {
        $cfile = $fs->get_file_by_id($cversion->fileid);
        if (!$cfile) {
            throw new moodle_exception("File in MAJ courseware #{$cversion->id} not found");
        }
        $f = $cfile->get_content_file_handle();
        $filemeta = stream_get_meta_data($f);
        $filepath = $filemeta['uri'];
        fclose($f);

        if (!file_exists($filepath)) {
            throw new moodle_exception('File not found');
        }

        $info = backup_general_helper::get_backup_information_from_mbz($filepath);
        if (!$info) {
            throw new moodle_exception('Cannot read mbz file');
        }

        $version = new stdClass();
        $version->id = 0;
        $version->hubcourseid = $hubcourse->id;
        $version->moodleversion = $info->moodle_version;
        $version->moodlerelease = $info->moodle_release;
        $version->description = $cversion->description;
        $version->userid = $courseware->userid;
        $version->timeuploaded = $cversion->timecreated;
        $version->fileid = 0;
        $id = $DB->insert_record('block_hubcourse_versions', $version);
        if (!$id) {
            return false;
        }

        $hfile = $fs->create_file_from_storedfile([
            'contextid' => $hubcourse->contextid,
            'component' => 'block_hubcourse',
            'filearea' => 'course',
            'itemid' => $id,
            'filepath' => '/',
            'filename' => $cfile->get_filename()
        ], $cfile);
        if (!$hfile) {
            throw new moodle_exception('Cannot save file');
        }

        $version = new stdClass();
        $version->id = $id;
        $version->fileid = $hfile->get_id();
        if (!$DB->update_record('block_hubcourse_versions', $version)) {
            throw new moodle_exception('Cannot save file id to new version');
        }

        block_hubcourseinfo_savembzdependencies($hubcourse->courseid, $id, $filepath);

        $lastid = $id;
    }

    return $lastid;
}

function block_hubcourseinfo_majimport_reviews($hubcourse, $courseware, $hubcourseversionid) {
    global $DB;
    $coursewarereviews = $DB->get_records('majhub_courseware_reviews', ['siteid' => $courseware->siteid, 'sitecourseid' => $courseware->sitecourseid]);
    foreach ($coursewarereviews as $creview) {
        $review = new stdClass();
        $review->id = 0;
        $review->hubcourseid = $hubcourse->id;
        $review->versionid = $hubcourseversionid;
        $review->userid = $creview->userid;
        $review->rate = round($creview->rating / 2);
        $review->comment = $creview->comment;
        $review->commentformat = FORMAT_HTML;
        $review->timecreated = $creview->timecreated;

        if (!$DB->insert_record('block_hubcourse_reviews', $review)) {
            return false;
        }
    }

    return true;
}

function block_hubcourseinfo_majimport_downloads($hubcourse, $courseware, $hubcourseversionid) {
    global $DB;
    $coursewaredownloads = $DB->get_records('majhub_courseware_downloads', ['siteid' => $courseware->siteid, 'sitecourseid' => $courseware->sitecourseid]);
    foreach ($coursewaredownloads as $cdownload) {
        $download = new stdClass();
        $download->id = 0;
        $download->versionid = $hubcourseversionid;
        $download->userid = $cdownload->userid;
        $download->timedownloaded = $cdownload->timecreated;

        if (!$DB->insert_record('block_hubcourse_downloads', $download)) {
            return false;
        }
    }

    return true;
}
