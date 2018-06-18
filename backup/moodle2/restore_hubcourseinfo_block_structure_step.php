<?php

class restore_hubcourseinfo_block_structure_step extends restore_block_instance_structure_step {

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

    protected function process_block_hubcourses($data) {
        global $DB;

        $coursecontext = context_course::instance($this->get_courseid());
        $blockinstance = $DB->get_record('block_instances', ['blockname' => 'hubcourseinfo', 'parentcontextid' => $coursecontext->id]);
        if (!$blockinstance) {
            throw new moodle_exception('instance_not_found');
        }

        $data = (object)$data;
        $oldid = $data->id;
        $data->id = 0;
        $data->instanceid = $blockinstance->id;
        $data->contextid = context_block::instance($blockinstance->id)->id;
        $data->courseid = $this->get_courseid();
        $data->userid = $this->get_mappingid('user', $data->userid);

        $newid = $DB->insert_record('block_hubcourses', $data);
        $this->set_mapping('block_hubcourses', $oldid, $newid);
    }

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

    protected function process_block_hubcourse_dependencies($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->id = 0;
        $data->versionid = $this->get_mappingid('block_hubcourse_versions', $data->versionid);

        $newid = $DB->insert_record('block_hubcourse_dependencies', $data);
        $this->set_mapping('block_hubcourse_dependencies', $oldid, $newid);
    }

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

    protected function process_block_hubcourse_likes($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->hubcourseid = $this->get_mappingid('block_hubcourses', $data->hubcourseid);
        $data->userid = $this->get_mappingid('user', $data->userid);

        $newid = $DB->insert_record('block_hubcourse_likes', $data);
        $this->set_mapping('block_hubcourse_likes', $oldid, $newid);
    }

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

    protected function after_execute() {
        global $DB;

        $hubcourse = block_hubcourseinfo_gethubcoursefromcourseid($this->get_courseid());
        $hubcourse->stableversion = $this->get_mappingid('block_hubcourse_versions', $hubcourse->stableversion);

        $DB->update_record('block_hubcourses', $hubcourse);

        $this->add_related_files('block_hubcourse', 'course', 'block_hubcourse_versions');
    }
}