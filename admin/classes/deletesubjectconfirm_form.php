<?php
require_once(__DIR__ . '/../../../../lib/formslib.php');

class deletesubjectconfirm_form extends moodleform {
    private $subject;

    public function __construct($subject) {
        $this->subject = $subject;
        parent::__construct();
    }

    public function definition() {
        $form = &$this->_form;

        $form->addElement('html', html_writer::tag('h3', get_string('deletesubjectconfirm_title', 'block_hubcourseinfo', $this->subject->name), ['class' => 'text-danger']));
        $form->addElement('html', html_writer::tag('p', get_string('deletesubjectconfirm_description', 'block_hubcourseinfo')));

        $form->addElement('hidden', 'id', $this->subject->id);

        $this->add_action_buttons(true, get_string('delete'));
    }
}