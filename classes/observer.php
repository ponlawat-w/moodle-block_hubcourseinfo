<?php

defined('MOODLE_INTERNAL') || die();

class block_hubcourseinfo_observer
{
    public static function course_restored(\core\event\course_restored $event)
    {
        global $DB;

        $autocreate = get_config('block_hubcourseinfo', 'autocreateinfoblock');
        if ($autocreate) {
            $course = get_course($event->courseid);
            $page = new moodle_page();
            $page->set_context(context_course::instance($course->id));
            $page->blocks->add_region(BLOCK_POS_RIGHT, false);
            $page->blocks->add_block('hubcourseinfo', BLOCK_POS_RIGHT, 10, false, 'course-view-*');

            $course->visible = 1;
            $DB->update_record('course', $course);
        }
    }
}