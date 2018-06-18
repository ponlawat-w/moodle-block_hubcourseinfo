<?php

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/restore_hubcourseinfo_block_structure_step.php');

class restore_hubcourseinfo_block_task extends restore_block_task {

    protected function define_my_settings() {
    }

    protected function define_my_steps() {
        $this->add_step(new restore_hubcourseinfo_block_structure_step('hubcourseinfo_structure', 'hubcourseinfo.xml'));
    }

    public function get_fileareas() {
        return ['course'];
    }

    public function get_configdata_encoded_attributes() {
    }

    public function after_restore() {

    }

    public static function define_decode_contents() {
        return [];
    }

    public static function define_decode_rules() {
        return [];
    }
}