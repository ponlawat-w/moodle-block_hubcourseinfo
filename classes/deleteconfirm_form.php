<?php
require_once(__DIR__ . '/../../../lib/formslib.php');

class deleteconfirm_form extends moodleform {

    private $hubcourse;

    public function __construct($hubcourse) {
        $this->hubcourse = $hubcourse;

        parent::__construct();
    }

    public function definition() {
        $form = &$this->_form;
        $form->addElement('html', html_writer::tag('h3', get_string('deleteconfirm_title', 'block_hubcourseinfo')));
        $form->addElement('html', html_writer::tag('p', get_string('deleteconfirm_description', 'block_hubcourseinfo')));

        $form->addElement('hidden', 'id', $this->hubcourse->id);

        $this->add_action_buttons(true, get_string('delete'));
    }
}