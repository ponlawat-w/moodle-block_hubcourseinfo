<?php

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/backup_hubcourseinfo_block_structure_step.php');

class backup_hubcourseinfo_block_task extends backup_block_task {

    protected function define_my_settings() {
    }

    protected function define_my_steps() {
        $this->add_step(new backup_hubcourseinfo_block_structure_step('hubcourseinfo_structure', 'hubcourseinfo.xml'));
    }

    public function get_fileareas() {
        return array('course');
    }

    public function get_configdata_encoded_attributes() {
    }

    public static function encode_content_links($content) {
        return $content;
    }
}