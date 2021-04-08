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
 * Observer
 *
 * @package block_hubcourseinfo
 * @copyright 2018 Moodle Association of Japan
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

/**
 * Class block_hubcourseinfo_observer
 * @package block_hubcourseinfo
 */
class block_hubcourseinfo_observer {

    /**
     * Event when course restored
     *  To create a new hubcourse instance if enabled
     *
     * @param \core\event\course_restored $event
     * @throws dml_exception
     */
    public static function course_restored(\core\event\course_restored $event) {
        global $DB;

        $autocreate = get_config('block_hubcourseinfo', 'autocreateinfoblock');
        if ($autocreate) {
            $course = get_course($event->courseid);
            $coursecontext = context_course::instance($course->id);

            $hubcourse = block_hubcourseinfo_gethubcoursefromcourseid($course->id);
            if (!$hubcourse) {
                block_hubcourseinfo_deleteotherblockinstances($coursecontext->id, false);
                $weight = 0;
                $minweight = $DB->get_record_sql('SELECT MIN(defaultweight) AS value FROM {block_instances} WHERE parentcontextid = ?', [$coursecontext->id]);
                if ($minweight) {
                    $weight = $minweight->value - 1;
                }

                $page = new moodle_page();
                $page->set_context(context_course::instance($course->id));
                $page->blocks->add_region(BLOCK_POS_LEFT, false);
                $page->blocks->add_block('hubcourseinfo', BLOCK_POS_LEFT, $weight, false, 'course-view-*');
            }

            $course->visible = 1;
            $DB->update_record('course', $course);
        }
    }

    /**
     * Event when a course is deleted
     *  To delete hubcourse data after a course is deleted
     *
     * @param \core\event\course_deleted $event
     */
    public static function course_deleted(\core\event\course_deleted $event) {
        require_once(__DIR__ . '/../lib.php');
        $hubcourse = block_hubcourseinfo_gethubcoursefromcourseid($event->courseid);
        block_hubcourseinfo_fulldelete($hubcourse);
    }
}