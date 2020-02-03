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
 * Form of writing a review to hubcourse
 *
 * @package block_hubcourseinfo
 * @copyright 2018 Moodle Association of Japan
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../lib/formslib.php');

/**
 * Class review_form
 * @package block_hubcourseinfo
 */
class review_form extends moodleform {

    /**
     * @var stdClass $hubcourse
     *  Hubcourse object
     */
    private $hubcourse;

    /**
     * @var bool $editing
     *  Is editing a review or writing a new review
     */
    private $editing;

    /**
     * review_form constructor.
     * @param stdClass $hubcourse
     * @param bool $editing
     */
    public function __construct($hubcourse, $editing) {
        $this->hubcourse = $hubcourse;
        $this->editing = $editing;

        parent::__construct();
    }

    /**
     * Private function to generate stars option for review rating
     * @param int $max
     * @return array
     * @throws coding_exception
     */
    private function generatestars($max = 5) {
        $stars = array(0 => get_string('pleaserate', 'block_hubcourseinfo'));
        for ($i = 5; $i > 0; $i--) {
            $text = '';
            for ($s = 0; $s < $i; $s++) {
                $text .= '★';
            }
            for ($s = $i; $s < $max; $s++) {
                $text .= '☆';
            }

            $stars[$i] = $text;
        }

        return $stars;
    }

    /**
     * Form definition
     * @throws coding_exception
     */
    public function definition() {
        $form = &$this->_form;

        $stars = $this->generatestars();

        $form->addElement('select', 'rate', get_string('ratethiscourse', 'block_hubcourseinfo'), $stars);
        $form->addElement('editor', 'comment', get_string('comment', 'block_hubcourseinfo'));

        $this->add_action_buttons(true, get_string($this->editing ? 'editreview' : 'submitreview', 'block_hubcourseinfo'));

        $form->addElement('hidden', 'id', $this->hubcourse->id);
        $form->setType('id', PARAM_INT);
    }

    /**
     * Submitted data validation
     * @param array $data
     * @param array $files
     * @return array
     * @throws coding_exception
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if ($data['rate'] < 1 || $data['rate'] > 5) {
            $errors['rate'] = get_string('reviewerr_pleaserate', 'block_hubcourseinfo');
        }

        if (trim($data['comment']['text']) == '') {
            $errors['comment'] = get_string('reviewerr_pleasecomment', 'block_hubcourseinfo');
        }

        return $errors;
    }
}