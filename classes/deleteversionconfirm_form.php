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
 * Form to ask user's confirmation of deleting a version in hubcourse
 *
 * @package block_hubcourseinfo
 * @copyright 2018 Moodle Association of Japan
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../lib/formslib.php');

/**
 * Class deleteversionconfirm_form
 * @package block_hubcourseinfo
 */
class deleteversionconfirm_form extends moodleform {

    /**
     * @var stdClass $version   Hubcourse version object
     */
    private $version;

    /**
     * deleteversionconfirm_form constructor.
     * @param stdClass $version
     */
    public function __construct($version) {
        $this->version = $version;
        parent::__construct();
    }

    /**
     * Form definition
     * @throws coding_exception
     */
    public function definition() {
        $form = &$this->_form;

        $form->addElement('html', html_writer::tag('h3', get_string('deleteversionconfirm_title', 'block_hubcourseinfo')));
        $form->addElement('html', html_writer::tag('p', get_string('deleteversionconfirm_description', 'block_hubcourseinfo')));

        $form->addElement('hidden', 'id', $this->version->id);

        $this->add_action_buttons(true, get_string('delete'));
    }
}