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
 * Form of editing metadata
 *
 * @package block_hubcourseinfo
 * @copyright 2018 Moodle Association of Japan
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../lib/formslib.php');

/**
 * Class editmetadata_form
 * @package block_hubcourseinfo
 */
class editmetadata_form extends moodleform {

    /**
     * @var stdClass $hubcourse     Hub course object from database
     */
    private $hubcourse;

    /**
     * @var stdClass $course        Course object from database
     */
    private $course;

    /**
     * @var int $new
     *  Indicating if this form is rendered from newly created hubcourse (1 or 0)
     *      if so, after submission it will redirect to hubcourse page
     *      otherwise, it will redirect back to hubcourse manage page
     */
    private $new;

    /**
     * editmetadata_form constructor.
     * @param stdClass $hubcourse
     * @param int $new
     * @throws coding_exception
     * @throws dml_exception
     */
    public function __construct($hubcourse, $new = 0) {
        global $DB;

        $this->hubcourse = $hubcourse;
        $this->course = $DB->get_record('course', ['id' => $hubcourse->courseid]);
        if (!$this->course) {
            throw new Exception(get_string('hubcoursenotfound', 'block_hubcourseinfo'));
        }

        $this->new = $new;

        parent::__construct();
    }

    /**
     * Form definition
     * @throws coding_exception
     * @throws dml_exception
     */
    public function definition() {
        global $DB;
        $categories = $DB->get_records('course_categories', ['visible' => 1]);
        $categoriesoptions = [];
        foreach ($categories as $category) {
            $categoriesoptions[$category->id] = $category->name;
        }

        $subjects = $DB->get_records('block_hubcourse_subjects', [], 'name ASC');
        $subjectsoptions = [];
        if (!$this->hubcourse->subject) {
            $subjectsoptions[0] = '-';
        }
        foreach ($subjects as $subject) {
            $subjectsoptions[$subject->id] = $subject->name;
        }

        $form = &$this->_form;

        if ($this->new) {
            $form->addElement('html', html_writer::div(get_string('editmetadatanewcourse', 'block_hubcourseinfo'), 'alert alert-info'));
        }

        $form->addElement('text', 'fullname', get_string('fullnamecourse'), ['style' => 'width: 100%;']);
        $form->setDefault('fullname', $this->course->fullname);
        $form->addRule('fullname', get_string('required'), 'required');

        $form->addElement('text', 'shortname', get_string('shortnamecourse'), ['style' => 'width: 100%;']);
        $form->setDefault('shortname', $this->course->shortname);
        $form->addRule('shortname', get_string('required'), 'required');

        $form->addElement('select', 'subject', get_string('subject', 'block_hubcourseinfo'), $subjectsoptions);
        $form->setDefault('subject', $this->hubcourse->subject);
        $form->addRule('subject', get_string('required'), 'required');

        $form->addElement('text', 'tags', get_string('tags', 'block_hubcourseinfo'), ['style' => 'width: 100%']);
        $form->setDefault('tags', $this->hubcourse->tags);
        $form->addHelpButton('tags', 'tags', 'block_hubcourseinfo');

        $form->addElement('select', 'category', get_string('category'), $categoriesoptions);
        $form->setDefault('category', $this->course->category);

        $form->addElement('text', 'demourl', get_string('demourl', 'block_hubcourseinfo'), ['style' => 'width: 100%;']);
        $form->setDefault('demourl', $this->hubcourse->demourl);
        $form->setType('demourl', PARAM_URL);

        $form->addElement('textarea', 'description', get_string('description'), ['style' => 'width: 100%;', 'rows' => '5']);
        $form->setDefault('description', $this->hubcourse->description);

        $form->addElement('hidden', 'id', $this->hubcourse->id);
        $form->addElement('hidden', 'new', $this->new);

        $this->add_action_buttons(!$this->new, ($this->new) ? get_string('continue') : get_string('save'));
    }

    /**
     * Submitted data validation
     * @param array $data
     * @param array $files
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     */
    public function validation($data, $files) {
        global $DB;

        $errors = parent::validation($data, $files);

        if (!$data['subject'] && $DB->count_records('block_hubcourse_subjects')) {
            $errors['subject'] = get_string('required');
        }

        return $errors;
    }
}