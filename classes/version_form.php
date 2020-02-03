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
 * Form when create / edit hubcourse version
 *
 * @package block_hubcourseinfo
 * @copyright 2018 Moodle Association of Japan
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../lib/formslib.php');
require_once(__DIR__ . '/../../../backup/util/includes/restore_includes.php');

/**
 * Class version_form
 * @package block_hubcourseinfo
 */
class version_form extends moodleform {

    /**
     * @var int $hubcourseid
     *  ID of hubcourse owning the version
     */
    private $hubcourseid;

    /**
     * @var int $versionid
     *  ID of version, zero if creating a new version
     */
    private $versionid;

    /**
     * @var bool $edit
     *  Indicate action whether creating or editing
     */
    private $edit;

    /**
     * @var stdClass
     *  Default data of inputs to display
     */
    private $defaultdata;

    /**
     * version_form constructor.
     * @param int $hubcourseid
     * @param int $versionid
     * @param bool $edit
     * @param stdClass $defaultdata
     */
    public function __construct($hubcourseid, $versionid = 0, $edit = false, $defaultdata = null) {
        $this->hubcourseid = $hubcourseid;
        $this->versionid = $versionid;
        $this->edit = $edit;
        $this->info = null;
        $this->defaultdata = $defaultdata;
        parent::__construct();
    }

    /**
     * Form definition
     * @throws coding_exception
     */
    public function definition() {
        $form = &$this->_form;

        $form->addElement('text', 'description', get_string('description'));
        $form->setType('description', PARAM_TEXT);
        $form->addRule('description', get_string('required'), 'required');

        if ($this->edit) {
            $form->addElement('hidden', 'id', $this->versionid);
            $form->setDefault('description', $this->defaultdata->description);

            $this->add_action_buttons(true, get_string('save', 'block_hubcourseinfo'));
        } else {
            $form->addElement('hidden', 'id', $this->hubcourseid);

            $maxbytes = block_hubcourseinfo_getmaxfilesize();
            $form->addElement('filepicker', 'coursefile', get_string('coursefile', 'block_hubcourseinfo'), null,
                ['maxbytes' => $maxbytes, 'accepted_types' => '.mbz']);
            $form->addElement('html', get_string('maxfilesize', 'block_hubcourseinfo', $maxbytes / 1024 / 1024));
            $form->addRule('coursefile', get_string('required'), 'required');

            $this->add_action_buttons(true, get_string('add'));
        }
        $form->setType('id', PARAM_INT);
    }
}