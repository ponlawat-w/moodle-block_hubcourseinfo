<?php
require_once(__DIR__ . '/lib.php');

class block_hubcourseinfo extends block_base {
    public function init() {
        $this->title = get_string('pluginname', 'block_hubcourseinfo');
        $this->version = 2018051600;
    }

    public function has_config() {
        return true;
    }

    public function instance_can_be_hidden() {
        return false;
    }

    public function applicable_formats() {
        return array(
            'all' => false,
            'course' => true
        );
    }

    public function instance_create() {
        global $DB, $USER;

        $coursecontext = $this->context->get_course_context();
        $courseid = $coursecontext ? $coursecontext->instanceid : false;
        if (!$courseid) {
            return false;
        }

        $hubcourse = $DB->get_record('block_hubcourses', ['courseid' => $courseid]);
        if (!$hubcourse) {
            $hubcourse = new stdClass();
            $hubcourse->id = 0;
            $hubcourse->courseid = $courseid;
            $hubcourse->userid = $USER->id;
            $hubcourse->stableversion = 0;
            $hubcourse->demourl = '';
            $hubcourse->description = '';
            $hubcourse->timecreated = time();
            $hubcourse->timemodified = time();

            $newid = $DB->insert_record('block_hubcourses', $hubcourse);

            if (!$newid) {
                return false;
            }
        }

        return true;
    }

    public function get_content() {
        global $OUTPUT, $DB;

        $coursecontext = $this->context->get_course_context();
        $courseid = $coursecontext ? $coursecontext->instanceid : 0;

        $hubcourse = $DB->get_record('block_hubcourses', ['courseid' => $courseid]);
        if (!$hubcourse) {
            return null;
        }

        $html = '';

        $html .= block_hubcourseinfo_renderinfo($hubcourse);
        $html .= html_writer::empty_tag('hr');
        $html .= block_hubcourseinfo_renderlike($hubcourse, $this->context);
        $html .= block_hubcourseinfo_renderreviews($hubcourse, $this->context);
        $html .= html_writer::empty_tag('hr');

        if (has_capability('block/hubcourseinfo:managecourse', $this->context) && $this->page->user_is_editing()) {
            $html .= html_writer::link(new moodle_url('/block/hubcourseinfo/manage.php', array('course' => $courseid)),
                $OUTPUT->pix_icon('i/edit', get_string('edit')) .
                get_string('managecourse', 'block_hubcourseinfo'),
                array('class' => 'btn btn-default btn-block'));
        } elseif (has_capability('block/hubcourseinfo:downloadcourse', $this->context)) {
            $html .= html_writer::link(new moodle_url('/'),
                $OUTPUT->pix_icon('t/download', get_string('download')) .
                get_string('downloadcourse', 'block_hubcourseinfo'),
                array('class' => 'btn btn-default btn-block'));

            $html .= html_writer::start_div();
            $html .= html_writer::div(get_string('moodleversion', 'block_hubcourseinfo'), 'bold');
            $html .= html_writer::div(get_string('notknow', 'block_hubcourseinfo'), '', ['style' => 'margin-left: 1em;']);
            $html .= html_writer::end_div();

            $html .= html_writer::start_div();
            $html .= html_writer::div(get_string('dependencies', 'block_hubcourseinfo'), 'bold');
            $html .= html_writer::div(get_string('notknow', 'block_hubcourseinfo'), '', ['style' => 'margin-left: 1em;']);
            $html .= html_writer::end_div();
        }

        $this->content = new stdClass();

        $this->content->text = $html;
        $this->content->footer = '';

        return $this->content;
    }

    public function get_aria_role() {
        return 'application';
    }
}