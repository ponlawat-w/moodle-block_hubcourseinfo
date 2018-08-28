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
 * Block restore structure step
 *
 * @package block_hubcourseinfo
 * @copyright 2018 Moodle Association of Japan
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Class restore_hubcourseinfo_block_structure_step
 * @package block_hubcourseinfo
 */
class restore_hubcourseinfo_block_structure_step extends restore_block_instance_structure_step {

    /**
     * Structure definition
     * @return restore_path_element[]
     */
    protected function define_structure() {
        $paths = [];

        $paths[] = new restore_path_element('block_hubcourses', '/block/block_hubcourses');

        $paths[] = new restore_path_element('block_hubcourse_versions', '/block/block_hubcourses/block_hubcourse_versions');
        $paths[] = new restore_path_element('block_hubcourse_dependencies', '/block/block_hubcourses/block_hubcourse_versions/block_hubcourse_dependencies');
        $paths[] = new restore_path_element('block_hubcourse_downloads', '/block/block_hubcourses/block_hubcourse_versions/block_hubcourse_downloads');

        $paths[] = new restore_path_element('block_hubcourse_likes', '/block/block_hubcourses/block_hubcourse_likes');
        $paths[] = new restore_path_element('block_hubcourse_reviews', '/block/block_hubcourses/block_hubcourse_reviews');

        return $paths;
    }

    /**
     * Process restoration
     * @param mixed $data
     * @throws base_step_exception
     * @throws dml_exception
     * @throws moodle_exception
     * @throws restore_step_exception
     */
    protected function process_block_hubcourses($data) {
        global $DB;

        $coursecontext = context_course::instance($this->get_courseid());
        $blockinstance = $DB->get_record('block_instances', ['blockname' => 'hubcourseinfo', 'parentcontextid' => $coursecontext->id]);
        if (!$blockinstance) {
            throw new moodle_exception('instance_not_found');
        }

        $data = (object)$data;

        $subjectname = $data->subjectname;
        $subject = $DB->get_record('block_hubcourse_subjects', ['name' => $subjectname]);

        $data->subject = $subject ? $subject->id : 0;
        unset($data->subjectname);

        $oldid = $data->id;
        $data->id = 0;
        $data->instanceid = $blockinstance->id;
        $data->contextid = context_block::instance($blockinstance->id)->id;
        $data->courseid = $this->get_courseid();
        $data->userid = $this->get_mappingid('user', $data->userid);

        $newid = $DB->insert_record('block_hubcourses', $data);
        $this->set_mapping('block_hubcourses', $oldid, $newid);
    }

    /**
     * Process courses' versions
     * @param mixed $data
     * @throws dml_exception
     * @throws restore_step_exception
     */
    protected function process_block_hubcourse_versions($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->id = 0;
        $data->hubcourseid = $this->get_mappingid('block_hubcourses', $data->hubcourseid);
        $data->userid = $this->get_mappingid('user', $data->userid);

        $newid = $DB->insert_record('block_hubcourse_versions', $data);
        $this->set_mapping('block_hubcourse_versions', $oldid, $newid, true);
    }

    /**
     * Process courses' plugin dependencies information
     * @param mixed $data
     * @throws dml_exception
     * @throws restore_step_exception
     */
    protected function process_block_hubcourse_dependencies($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->id = 0;
        $data->versionid = $this->get_mappingid('block_hubcourse_versions', $data->versionid);

        $newid = $DB->insert_record('block_hubcourse_dependencies', $data);
        $this->set_mapping('block_hubcourse_dependencies', $oldid, $newid);
    }

    /**
     * Process courses' download history information
     * @param mixed $data
     * @throws dml_exception
     * @throws restore_step_exception
     */
    protected function process_block_hubcourse_downloads($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->id = 0;
        $data->versionid = $this->get_mappingid('block_hubcourse_versions', $data->versionid);
        $data->userid = $this->get_mappingid('user', $data->userid);

        $newid = $DB->insert_record('block_hubcourse_downloads', $data);
        $this->set_mapping('block_hubcourse_downloads', $oldid, $newid);
    }

    /**
     * Process courses likes data
     * @param mixed $data
     * @throws dml_exception
     * @throws restore_step_exception
     */
    protected function process_block_hubcourse_likes($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->hubcourseid = $this->get_mappingid('block_hubcourses', $data->hubcourseid);
        $data->userid = $this->get_mappingid('user', $data->userid);

        $newid = $DB->insert_record('block_hubcourse_likes', $data);
        $this->set_mapping('block_hubcourse_likes', $oldid, $newid);
    }

    /**
     * Process courses reviews
     * @param mixed $data
     * @throws dml_exception
     * @throws restore_step_exception
     */
    protected function process_block_hubcourse_reviews($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->hubcourseid = $this->get_mappingid('block_hubcourses', $data->hubcourseid);
        $data->versionid = $this->get_mappingid('block_hubcourse_versions', $data->versionid);
        $data->userid = $this->get_mappingid('user', $data->userid);

        $newid = $DB->insert_record('block_hubcourse_reviews', $data);
        $this->set_mapping('block_hubcourse_reviews', $oldid, $newid);
    }

    /**
     * Action after execution
     * @throws base_step_exception
     * @throws dml_exception
     */
    protected function after_execute() {
        global $DB;

        $hubcourse = block_hubcourseinfo_gethubcoursefromcourseid($this->get_courseid());
        $hubcourse->stableversion = $this->get_mappingid('block_hubcourse_versions', $hubcourse->stableversion);

        $DB->update_record('block_hubcourses', $hubcourse);

        $this->add_related_files('block_hubcourse', 'course', 'block_hubcourse_versions');
    }
}