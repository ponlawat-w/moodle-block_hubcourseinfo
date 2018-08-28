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
 * Block backup task
 *
 * @package block_hubcourseinfo
 * @copyright 2018 Moodle Association of Japan
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/backup_hubcourseinfo_block_structure_step.php');

/**
 * Class backup_hubcourseinfo_block_task
 *
 * @package block_hubcourseinfo
 */
class backup_hubcourseinfo_block_task extends backup_block_task {

    /**
     * Define my settings
     */
    protected function define_my_settings() {
    }

    /**
     * Define customized steps
     * @throws backup_step_exception
     * @throws base_task_exception
     */
    protected function define_my_steps() {
        $this->add_step(new backup_hubcourseinfo_block_structure_step('hubcourseinfo_structure', 'hubcourseinfo.xml'));
    }

    /**
     * File areas used by block
     * @return array
     */
    public function get_fileareas() {
        return array('course');
    }

    /**
     * Config data
     */
    public function get_configdata_encoded_attributes() {
    }

    /**
     * Encoding content links
     * @param string $content
     * @return string
     */
    public static function encode_content_links($content) {
        return $content;
    }
}