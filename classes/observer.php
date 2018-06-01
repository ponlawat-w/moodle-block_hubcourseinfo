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
            $coursecontext = context_course::instance($course->id);

            $weight = 0;
            $minweight = $DB->get_record_sql('SELECT MIN(defaultweight) AS value FROM {block_instances} WHERE parentcontextid = ?', [$coursecontext->id]);
            if ($minweight) {
                $weight = $minweight->value - 1;
            }

            $page = new moodle_page();
            $page->set_context(context_course::instance($course->id));
            $page->blocks->add_region(BLOCK_POS_LEFT, false);
            $page->blocks->add_block('hubcourseinfo', BLOCK_POS_LEFT, $weight, false, 'course-view-*');

            $course->visible = 1;
            $DB->update_record('course', $course);
        }
    }

    public static function course_deleted(\core\event\course_deleted $event)
    {
        require_once(__DIR__ . '/../lib.php');
        $hubcourse = block_hubcourseinfo_gethubcoursefromcourseid($event->courseid);
        block_hubcourseinfo_fulldelete($hubcourse);
    }
}