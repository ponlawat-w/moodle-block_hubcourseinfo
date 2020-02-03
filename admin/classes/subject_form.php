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
 * Form when create or edit subject
 *
 * @package block_hubcourseinfo
 * @copyright 2018 Moodle Association of Japan
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../lib/formslib.php');

/**
 * Class subject_form
 * @package block_hubcourseinfo
 */
class subject_form extends moodleform {

    /**
     * @var int $id Subject ID
     */
    private $id = 0;

    /**
     * @var string $defaultname Default name of subject to show in input box
     */
    private $defaultname = '';

    /**
     * subject_form constructor.
     * @param int $id Subject ID
     * @param string $name Subject name
     */
    public function __construct($id = 0, $name = '') {
        $this->id = $id;
        $this->defaultname = $name;
        parent::__construct();
    }

    /**
     * Form definition
     * @throws coding_exception
     */
    public function definition() {
        $form = &$this->_form;

        $form->addElement('text', 'name', get_string('subjectname', 'block_hubcourseinfo'));
        $form->setDefault('name', $this->defaultname);
        $form->setType('name', PARAM_TEXT);
        $form->addRule('name', get_string('required'), 'required');

        $form->addElement('hidden', 'id');
        $form->setDefault('id', $this->id);
        $form->setType('id', PARAM_INT);

        $this->add_action_buttons($this->id ? true : false, get_string($this->id ? 'edit' : 'add'));
    }
}